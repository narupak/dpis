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
	$report_title = "บัญชีตำแหน่งข้าราชการพลเรือนสามัญที่ถูกยุบเลิก<br>ใน$DEPARTMENT_NAME  $MINISTRY_NAME";
	$report_code = "";
	
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

		$worksheet->set_column(0, 0, 6);
		$worksheet->set_column(1, 1, 10);
		$worksheet->set_column(2, 2, 35);
		$worksheet->set_column(3, 3, 35);
		$worksheet->set_column(4, 4, 15);
		$worksheet->set_column(5, 5, 10);
		$worksheet->set_column(6, 6, 30);
		$worksheet->set_column(7, 7, 30);
		$worksheet->set_column(8, 8, 30);
		$worksheet->set_column(9, 9, 40);

		set_format("xlsFmtTableHeader", "B", "C", "LRB", 0);
		
		$xlsRow++;
		$worksheet->write($xlsRow, 0, "ลำดับที่", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 1, "เลขที่ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 2, "ชื่อตำแหน่งในการบริหารงาน", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 3, "ชื่อตำแหน่งในสายงาน", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 4, "ประเภทตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 5, "ระดับตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 6, "$ORG_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 7, "$ORG_TITLE1", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 8, "$ORG_TITLE2", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 9, "หมายเหตุ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
	} // function		

	if($DPISDB=="odbc"){
		$select_top = ($current_page==$total_page)?($count_data - ($data_per_page * ($current_page - 1))):$data_per_page;			
		$cmd = "	select a.PL_CODE, a.POS_NO, a.ORG_ID, a.ORG_ID_1, a.ORG_ID_2, a.CL_NAME, a.POS_SALARY, a.POS_CONDITION, a.PM_CODE, a.PT_CODE
				from			((((PER_POSITION a 
							) left join PER_ORG d on (a.ORG_ID=d.ORG_ID)
							) left join PER_ORG e on (a.ORG_ID_1=e.ORG_ID)
							) left join PER_ORG f on (a.ORG_ID_2=f.ORG_ID)
				where		a.DEPARTMENT_ID = $DEPARTMENT_ID and a.POS_STATUS = 2
				order by 		IIf(IsNull(d.ORG_SEQ_NO), 0, d.ORG_SEQ_NO), d.ORG_CODE, 
							IIf(IsNull(e.ORG_SEQ_NO), 0, e.ORG_SEQ_NO), IIf(IsNull(e.ORG_CODE), d.ORG_CODE, e.ORG_CODE), 
							IIf(IsNull(f.ORG_SEQ_NO), 0, f.ORG_SEQ_NO), IIf(IsNull(f.ORG_CODE),  IIf(IsNull(e.ORG_CODE), d.ORG_CODE, e.ORG_CODE), f.ORG_CODE), 
							IIf(IsNull(a.POS_NO), 0, CLng(a.POS_NO))  ";
	}elseif($DPISDB=="oci8"){
		$cmd = "  select	 a.PL_CODE, a.POS_NO, a.ORG_ID, a.ORG_ID_1, a.ORG_ID_2, a.CL_NAME, a.POS_SALARY, a.POS_CONDITION, a.PM_CODE, a.PT_CODE
				from			PER_POSITION a, PER_ORG d, PER_ORG e, PER_ORG f
				where		a.DEPARTMENT_ID = $DEPARTMENT_ID and a.POS_STATUS = 2 and 
									a.ORG_ID=d.ORG_ID(+) and a.ORG_ID_1=e.ORG_ID(+) and a.ORG_ID_2=f.ORG_ID(+) 
				order by 		nvl(d.ORG_SEQ_NO,0), d.ORG_CODE, nvl(e.ORG_SEQ_NO,0), nvl(e.ORG_CODE,d.ORG_CODE), nvl(f.ORG_SEQ_NO,0), nvl(f.ORG_CODE,nvl(e.ORG_CODE,d.ORG_CODE)), to_number(replace(a.POS_NO,'-',''))	";	
	}elseif($DPISDB=="mysql"){
		$cmd = "	select a.PL_CODE, a.POS_NO, a.ORG_ID, a.ORG_ID_1, a.ORG_ID_2, a.CL_NAME, a.POS_SALARY, a.POS_CONDITION, a.PM_CODE, a.PT_CODE
				from			((((PER_POSITION a 
							) left join PER_ORG d on (a.ORG_ID=d.ORG_ID)
							) left join PER_ORG e on (a.ORG_ID_1=e.ORG_ID)
							) left join PER_ORG f on (a.ORG_ID_2=f.ORG_ID)
				where		a.DEPARTMENT_ID = $DEPARTMENT_ID and a.POS_STATUS = 2 
				order by 		d.ORG_SEQ_NO, d.ORG_CODE, e.ORG_SEQ_NO, e.ORG_CODE, a.POS_NO ";
	} // end if	
	if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
//echo $cmd;
//exit;
	$data_count = $data_row = 0;
	$ORG_NAME_1 = $ORG_NAME_2 = -1;
	while($data = $db_dpis->get_array()){
		$data_row++;
		
		$PL_CODE = trim($data[PL_CODE]);
		$CL_NAME = trim($data[CL_NAME]);
		$POS_NO = $data[POS_NO];
		$POS_SALARY = $data[POS_SALARY];
		$POS_CONDITION = trim($data[POS_CONDITION]);
		$PT_CODE = trim($data[PT_CODE]);
		$PM_CODE = trim($data[PM_CODE]);
			
		$cmd = " select PL_NAME from PER_LINE where trim(PL_CODE)='$PL_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$PL_NAME = trim($data2[PL_NAME]);

		$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE)='$PT_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$PT_NAME = trim($data2[PT_NAME]);

		$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$PM_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$PM_NAME = trim($data2[PM_NAME]);
		if (!$PM_NAME) $PM_NAME = $PL_NAME;

		$PL_NAME = (trim($PL_NAME)?($PL_NAME ." ". level_no_format($CL_NAME)):"");

		if ($PT_NAME=="ว") $PT_NAME="ทั่วไป";  
			
		$ORG_ID = $data[ORG_ID];
		$ORG_ID_1 = $data[ORG_ID_1];
		$ORG_ID_2 = $data[ORG_ID_2];

		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$ORG_NAME = $data2[ORG_NAME];
		
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_1 ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$ORG_NAME_1 = $data2[ORG_NAME];
		
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_2 ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$ORG_NAME_2 = $data2[ORG_NAME];

		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][order] = $data_row;
		$arr_content[$data_count][pos_no] = $POS_NO;
		$arr_content[$data_count][cl_name] = $CL_NAME;
		$arr_content[$data_count][pl_name] = $PL_NAME;
		$arr_content[$data_count][pm_name] = $PM_NAME;
		$arr_content[$data_count][pt_name] = $PT_NAME;
		$arr_content[$data_count][org_name] = $ORG_NAME;
		$arr_content[$data_count][org_name_1] = $ORG_NAME_1;
		$arr_content[$data_count][org_name_2] = $ORG_NAME_2;
		$arr_content[$data_count][pos_salary] = $POS_SALARY?number_format($POS_SALARY):"-";
		$arr_content[$data_count][pos_condition] = $POS_CONDITION;
		
		$data_count++;

	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
	if($count_data){
		$xlsRow = 0;
		$arr_title = explode("||", $report_title);
		for($i=0; $i<count($arr_title); $i++){
			$xlsRow = $i;
			$worksheet->write($xlsRow, 0, $arr_title[$i], set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTitle", "B", "C", "", 1));
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
			$worksheet->write($xlsRow, 7, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 8, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 9, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
		} // end if

		print_header();

		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$CONTENT_TYPE = $arr_content[$data_count][type];
			$ORDER = $arr_content[$data_count][order];
			$POS_NO = $arr_content[$data_count][pos_no];
			$CL_NAME = $arr_content[$data_count][cl_name];
			$PL_NAME = $arr_content[$data_count][pl_name];
			$PT_NAME = $arr_content[$data_count][pt_name];
			$PM_NAME = $arr_content[$data_count][pm_name];
			$ORG_NAME = $arr_content[$data_count][org_name];
			$ORG_NAME_1 = $arr_content[$data_count][org_name_1];
			$ORG_NAME_2 = $arr_content[$data_count][org_name_2];
			$POS_SALARY = $arr_content[$data_count][pos_salary];
			$POS_CONDITION = $arr_content[$data_count][pos_condition];
			
			$xlsRow++;
			$worksheet->write_string($xlsRow, 0, "$ORDER", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write_string($xlsRow, 1, "$POS_NO", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 2, "$PM_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 3, "$PL_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 4, "$PT_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 5, "$CL_NAME", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write_string($xlsRow, 6, "$ORG_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 7, "$ORG_NAME_1", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 8, "$ORG_NAME_2", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 9, "$POS_CONDITION", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
		} // end for				
		
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
		$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTitle", "B", "C", "", 1));
	} // end if

	$workbook->close();

	ini_set("max_execution_time", 30);
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"บัญชีแนบท้ายคำสั่งจัดตำแหน่ง.xls\"");
	header("Content-Disposition: inline; filename=\"บัญชีแนบท้ายคำสั่งจัดตำแหน่ง.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>