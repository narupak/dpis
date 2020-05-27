<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	include("../php_scripts/load_per_control.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$company_name = "";
	$report_title = "";
	$report_code = "ประวัติการเลื่อนขั้นเงินเดือน";

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$workbook = new writeexcel_workbook($fname);

	$worksheet = &$workbook->addworksheet("$report_code");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	function print_header(){
		global $worksheet, $xlsRow;
		global $heading_name;
		
		$worksheet->set_column(0, 0, 40);
		$worksheet->set_column(1, 1, 15);
		$worksheet->set_column(2, 2, 15);
		$worksheet->set_column(3, 3, 20);
		$worksheet->set_column(4, 4, 40);
		$worksheet->set_column(5, 5, 15);
		$worksheet->set_column(6, 6, 35);

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "ชื่อ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 1, "เลขที่ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 2, "วันที่", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 3, "เลขที่ตำแหน่งในประวัติ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 4, "ชื่อตำแหน่งในประวัติ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 5, "อัตราเงินเดือน", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 6, "ประเภทการเคลื่อนไหว", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
	} // function		

	// ===== select data =====
	if($DPISDB=="odbc"){
		$cmd = " select		distinct 
							b.ORG_ID_REF, $select_list, a.POS_ID, b.ORG_NAME, c.PL_NAME, d.PM_NAME, a.PT_CODE, 
							a.CL_NAME, f.PV_NAME, g.OT_NAME, b.ORG_CODE, a.PM_CODE, a.PL_CODE, b.PV_CODE, 
							b.OT_CODE, 
							a.ORG_ID, a.ORG_ID_1, a.ORG_ID_2 
				 from		(
									(
										(
											(
												PER_POSITION a
												inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
											) inner join PER_LINE c on (a.PL_CODE=c.PL_CODE)
										) left join PER_MGT d on (a.PM_CODE=d.PM_CODE)
									) left join PER_PROVINCE f on (b.PV_CODE=f.PV_CODE)
								) left join PER_ORG_TYPE g on (b.OT_CODE=g.OT_CODE)
							$search_condition
				 order by		b.ORG_ID_REF, $order_by ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);	
/*		$cmd = " select		a.PER_ID, a.SAH_EFFECTIVEDATE, a.MOV_CODE, a.SAH_SALARY, b.PER_TYPE, b.PER_NAME, b.PER_SURNAME, c.POS_NO
				from			PER_SALARYHIS a, PER_PERSONAL b, PER_POSITION c
				where		a.PER_ID=b.PER_ID and b.POS_ID=c.POS_ID and b.PER_TYPE = 1	
				order by		b.PER_TYPE, to_number(replace(c.POS_NO,'-','')), a.SAH_EFFECTIVEDATE "; 
		$cmd = " select		a.PER_ID, a.SAH_EFFECTIVEDATE, a.MOV_CODE, a.SAH_SALARY, b.PER_TYPE, b.PER_NAME, b.PER_SURNAME, c.POEM_NO
				from			PER_SALARYHIS a, PER_PERSONAL b, PER_POS_EMP c
				where		a.PER_ID=b.PER_ID and b.POEM_ID=c.POEM_ID and b.PER_TYPE = 2	
				order by		b.PER_TYPE, to_number(c.POEM_NO), a.SAH_EFFECTIVEDATE ";  */
		$cmd = " select		a.PER_ID, a.SAH_EFFECTIVEDATE, a.MOV_CODE, a.SAH_SALARY, b.PER_TYPE, b.PER_NAME, b.PER_SURNAME, c.POEMS_NO, c.EP_CODE
				from			PER_SALARYHIS a, PER_PERSONAL b, PER_POS_EMPSER c
				where		a.PER_ID=b.PER_ID and b.POEMS_ID=c.POEMS_ID and b.PER_TYPE = 3
				order by		b.PER_TYPE, to_number(c.POEMS_NO), a.SAH_EFFECTIVEDATE "; 
	} // end if
	$count_data = $db_dpis->send_cmd($cmd);
//	echo "$cmd<br>";
//	$db_dpis->show_error();
	$data_count = $data_row = 0;
//	initialize_parameter(0);
	while($data = $db_dpis->get_array()){

			$data_row++;
			$PER_ID = $data[PER_ID];
			$SAH_EFFECTIVEDATE = trim($data[SAH_EFFECTIVEDATE]);
			$TMP_SAH_EFFECTIVEDATE = show_date_format($SAH_EFFECTIVEDATE,$DATE_DISPLAY);
			$MOV_CODE = trim($data[MOV_CODE]);
			$SAH_SALARY = $data[SAH_SALARY];
			$PER_TYPE = $data[PER_TYPE];
			$PER_NAME = trim($data[PER_NAME]).' '.trim($data[PER_SURNAME]);	
			if ($PER_TYPE==1)
				$POS_NO = trim($data[POS_NO]);
			elseif ($PER_TYPE==2)
				$POS_NO = trim($data[POEM_NO]);
			elseif ($PER_TYPE==3)
				$POS_NO = trim($data[POEMS_NO]);
			$PER_EP_CODE = trim($data[EP_CODE]);

			$cmd = " select MOV_NAME from PER_MOVMENT where MOV_CODE= '$MOV_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$MOV_NAME = $data2[MOV_NAME];

			$cmd = " select POH_POS_NO, PM_CODE, LEVEL_NO, PL_CODE, PT_CODE, PN_CODE, EP_CODE 
								from   PER_POSITIONHIS
								where PER_ID=$PER_ID and POH_EFFECTIVEDATE <= '$SAH_EFFECTIVEDATE' 
								order by POH_EFFECTIVEDATE desc ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$POH_POS_NO = trim($data2[POH_POS_NO]);
			$PM_CODE = trim($data2[PM_CODE]);
			$LEVEL_NO = trim($data2[LEVEL_NO]);
			$PL_CODE = trim($data2[PL_CODE]);
			$PN_CODE = trim($data2[PN_CODE]);
			$EP_CODE = trim($data2[EP_CODE]);
			
			if ($PER_TYPE==1) {
				$cmd = " select PL_NAME from PER_LINE where PL_CODE='$PL_CODE' ";			
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$PL_NAME = $data2[PL_NAME];

				$cmd = " 	select PM_NAME from PER_MGT	where PM_CODE='$PM_CODE'  ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$PM_NAME = trim($data2[PM_NAME]);

				$cmd = " 	select PT_NAME from PER_TYPE	where PT_CODE='$PT_CODE'  ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$PT_NAME = trim($data2[PT_NAME]);

				$TMP_PL_NAME = (trim($PM_NAME) && $PM_NAME!='ไม่ใช่ตำแหน่งทางบริหาร/วิชาการ'?"$PM_NAME (":"") . (trim($PL_NAME)?($PL_NAME ." ". level_no_format($LEVEL_NO) . 
				(($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"")):"") . (trim($PM_NAME) && $PM_NAME!='ไม่ใช่ตำแหน่งทางบริหาร/วิชาการ'?")":"");
			} elseif($PER_TYPE==2){
				$cmd = " select PN_NAME from PER_POS_NAME where PN_CODE='$PN_CODE' ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$TMP_PL_NAME = $data2[PN_NAME];	
			} elseif($PER_TYPE==3){
				if (!$POH_POS_NO) $POH_POS_NO = $POS_NO;
				if (!$EP_CODE) $EP_CODE = $PER_EP_CODE;
				$cmd = " select EP_NAME from PER_EMPSER_POS_NAME where EP_CODE='$EP_CODE' ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$TMP_PL_NAME = $data2[EP_NAME];		
			} // end if

			$arr_content[$data_count][per_name] = $PER_NAME;
			$arr_content[$data_count][pos_no] = $POS_NO;
			$arr_content[$data_count][sah_effectivedate] = $TMP_SAH_EFFECTIVEDATE;
			$arr_content[$data_count][poh_pos_no] = $POH_POS_NO;
			$arr_content[$data_count][pl_name] = $TMP_PL_NAME;
			$arr_content[$data_count][sah_salary] = (($SAH_SALARY)?number_format($SAH_SALARY,0,'',','):"");
			$arr_content[$data_count][mov_name] = $MOV_NAME;

			$data_count++;														
	} // end while
	
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
	// ===== condition $count_data  from "select data" =====
	if($count_data){
		$xlsRow = 0;
		$arr_title = explode("||",$report_title);

		for($i=0; $i<count($arr_title); $i++){
			$xlsRow = $i;
			$worksheet->write($xlsRow, 0, $arr_title[$i], set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		} // end if
		
		if($company_name){
			$xlsRow++;
			$worksheet->write($xlsRow, 0, $company_name, set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 2, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 4, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 5, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 6, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
		} // end if
		
		print_header();

		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$PER_NAME = $arr_content[$data_count][per_name];
			$POS_NO = $arr_content[$data_count][pos_no];
			$SAH_EFFECTIVEDATE = $arr_content[$data_count][sah_effectivedate];
			$POH_POS_NO = $arr_content[$data_count][poh_pos_no];
			$PL_NAME = $arr_content[$data_count][pl_name];
			$SAH_SALARY = $arr_content[$data_count][sah_salary];
			$MOV_NAME = $arr_content[$data_count][mov_name];

			$xlsRow++;
			$worksheet->write_string($xlsRow, 0, "$PER_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 1, "$POS_NO", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 2, "$SAH_EFFECTIVEDATE", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 3, "$POH_POS_NO", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 4, "$PL_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 5, "$SAH_SALARY", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 6, "$MOV_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
		} 	// end for				
	}else{	// if($count_data)
		$worksheet = &$workbook->addworksheet("$report_code");
		$worksheet->set_margin_right(0.50);
		$worksheet->set_margin_bottom(1.10);
		
		//====================== SET FORMAT ======================//
		require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
		//====================== SET FORMAT ======================//

		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));
	} // end if

	$workbook->close();

	ini_set("max_execution_time", 30);
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"gfmis.xls\"");
	header("Content-Disposition: inline; filename=\"gfmis.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);

?>