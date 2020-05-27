<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";
	
	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$workbook = new writeexcel_workbook($fname);
	$worksheet = &$workbook->addworksheet("ชื่อตำแหน่งในการบริหารงาน");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	function print_header($xlsRow){
		global $worksheet, $MINISTRY_TITLE, $DEPARTMENT_TITLE, $PL_TITLE, $PM_TITLE;

		$worksheet->set_column(0, 0, 10);
		$worksheet->set_column(1, 1, 30);
		$worksheet->set_column(2, 2, 30);
		$worksheet->set_column(3, 3, 30);
		$worksheet->set_column(4, 4, 30);
		$worksheet->set_column(5, 5, 60);
		$worksheet->set_column(6, 6, 10);
		$worksheet->set_column(7, 7, 10);
		
		$worksheet->write($xlsRow, 0, "เลขที่ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, $MINISTRY_TITLE, set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 2, $DEPARTMENT_TITLE, set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 3, $PL_TITLE, set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 4, $PM_TITLE, set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 5, "ประเภทเงินเพิ่มพิเศษ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 6, "จำนวนเงิน", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 7, "ใช้งาน/ยกเลิก", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
	} // end if

	$cmd = " select * from $table ";
	$db_dpis->send_cmd($cmd);
	$field_list = $db_dpis->list_fields($table);
//	echo "<pre>";		print_r($field_list);		echo "</pre>";
	unset($arr_fields);
	if($DPISDB=="odbc" || $DPISDB=="oci8"){
		for($i=1; $i<=count($field_list); $i++) :
			$arr_fields[] = $field_list[$i]["name"];
		endfor;
	}elseif($DPISDB=="mysql"){
		for($i=0; $i<count($field_list); $i++) :
			$arr_fields[] = $field_list[$i]["name"];
		endfor;
	} // end if

  	if(trim($search_department_id)) $arr_search_condition[] = "(b.DEPARTMENT_ID = $search_department_id)";
  	if(trim($search_pos_id)) $arr_search_condition[] = "(a.POS_ID like '$search_pos_id%')";
  	if(trim($search_ex_code)) $arr_search_condition[] = "(a.EX_CODE like '%$search_ex_code%')";
	$search_condition = "";
	$search_condition_count = "";
	if(count($arr_search_condition)){
		$search_condition = " and " . implode(" and ", $arr_search_condition);
		$search_condition_count = " where " . implode(" and ", $arr_search_condition);
	}

	if($DPISDB=="odbc"){
		$cmd = "	select	a.POS_ID, a.EX_CODE, POS_STARTDATE, a.POS_STATUS, POS_NO_NAME, POS_NO, PL_CODE, PM_CODE, EX_NAME, EX_AMT, b.DEPARTMENT_ID
									from	PER_POS_MGTSALARY a, PER_POSITION b, PER_EXTRATYPE c,PER_PERSONAL d  
									where 	a.POS_ID=b.POS_ID and a.EX_CODE=c.EX_CODE and  a.POS_ID=d.POS_ID
											$search_condition 
									order by b.POS_NO_NAME, iif(isnull(b.POS_NO),0,CLng(b.POS_NO)), a.EX_CODE ";
	}elseif($DPISDB=="oci8"){
				$cmd = "	  select 	a.POS_ID, a.EX_CODE, POS_STARTDATE, a.POS_STATUS, POS_NO_NAME, POS_NO, PL_CODE, PM_CODE, EX_NAME, EX_AMT, b.DEPARTMENT_ID 
								  from 		PER_POS_MGTSALARY a, PER_POSITION b, PER_EXTRATYPE c 
								  where 	a.POS_ID=b.POS_ID and trim(a.EX_CODE)=trim(c.EX_CODE) 
											$search_condition 
								  order by 	b.POS_NO_NAME, to_number(replace(b.POS_NO,'-','')), a.EX_CODE ";						   
	}elseif($DPISDB=="mysql"){
		$cmd = "	select 		a.POS_ID, a.EX_CODE, POS_STARTDATE, a.POS_STATUS, POS_NO_NAME, POS_NO, PL_CODE, PM_CODE, EX_NAME, EX_AMT, b.DEPARTMENT_ID 
					from 		PER_POS_MGTSALARY a, PER_POSITION b, PER_EXTRATYPE c,PER_PERSONAL d  
					where 		a.POS_ID=b.POS_ID and a.EX_CODE=c.EX_CODE and  a.POS_ID=d.POS_ID
								$search_condition 
					order by 	b.POS_NO_NAME, b.POS_NO+0, a.EX_CODE ";
	} // end if

	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();

	if($count_data){
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "$report_title", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTitle", "B", "C", "", 1));

		print_header(1);
		$data_count = 1;
		
		while($data = $db_dpis->get_array()){
			$data_count++;

			$POS_ID = $data[POS_ID];
			$POS_NO = $data[POS_NO_NAME].$data[POS_NO];
			$EX_CODE = $data[EX_CODE];		
			$PL_CODE = trim($data[PL_CODE]);
			$PM_CODE = trim($data[PM_CODE]);
			$EX_NAME = trim($data[EX_NAME]);		
			$EX_AMT = $data[EX_AMT];
			$POS_STATUS = $data[POS_STATUS];
			$TMP_DEPARTMENT_ID = $data[DEPARTMENT_ID];
			
			$cmd = " select PL_NAME from PER_LINE where PL_CODE='$PL_CODE' ";			
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PL_NAME = $data2[PL_NAME];

			$cmd = " 	select PM_NAME from PER_MGT	where PM_CODE='$PM_CODE'  ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PM_NAME = trim($data2[PM_NAME]);
			if (!$PM_NAME) $PM_NAME = $PL_NAME;
			
			$cmd = " select ORG_NAME, ORG_ID_REF from PER_ORG where ORG_ID=$TMP_DEPARTMENT_ID ";			
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$TMP_DEPARTMENT_NAME = $data2[ORG_NAME];
			$TMP_MINISTRY_ID = $data2[ORG_ID_REF];

			$cmd = " 	select ORG_NAME from PER_ORG	where ORG_ID=$TMP_MINISTRY_ID  ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$TMP_MINISTRY_NAME = trim($data2[ORG_NAME]);

			$POS_STATUS = ($POS_STATUS==1)?"../images/checkbox_check.bmp":"../images/checkbox_blank.bmp";
			$ps_name = $data[PS_NAME];

			$xlsRow = $data_count;
			$worksheet->write_string($xlsRow, 0, $POS_NO, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 1, $TMP_MINISTRY_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 2, $TMP_DEPARTMENT_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 3, $PL_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 4, $PM_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 5, $EX_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 6, ($EX_AMT?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($EX_AMT)):number_format($EX_AMT)):"-"), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->insert_bitmap($xlsRow, 7,$POS_STATUS, 35, 4, 1, 0.8);
		} // end while
	}else{
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTitle", "B", "C", "", 1));
	} // end if

	$workbook->close();

	ini_set("max_execution_time", 30);
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"$report_title.xls\"");
	header("Content-Disposition: inline; filename=\"$report_title.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>