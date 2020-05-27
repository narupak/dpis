<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	$cmd = " select		ORD_NO, ORD_TITLE, ORD_DATE
					 from		PER_ORDER 
					 where	ORD_ID=$ORD_ID ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
		
	$ORD_NO = trim($data[ORD_NO]);
	$ORD_TITLE = trim($data[ORD_TITLE]);
	$ORD_DATE = $data[ORD_DATE];
	$ORD_DATE = 	show_date_format($ORD_DATE,$DATE_DISPLAY);

	if ($order_by==1) $order_str = "a.ORD_SEQ";
	else 
		if($DPISDB=="odbc") $order_str = "b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2, IIf(IsNull(b.POS_NO), 0, CLng(b.POS_NO))";
		elseif($DPISDB=="oci8")  $order_str = "b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2, to_number(replace(b.POS_NO,'-',''))";
		elseif($DPISDB=="mysql")  $order_str = "b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2, b.POS_NO";
	$order_str = "a.ORD_SEQ";

	$company_name = "";
	$report_title = "บัญชีรายละเอียดการปรับปรุงการกำหนดตำแหน่ง||แนบท้ายคำสั่ง $DEPARTMENT_NAME ที่ $ORD_NO ลงวันที่ $ORD_DATE";
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
		$worksheet->set_column(1, 1, 6);
		$worksheet->set_column(2, 2, 30);
		$worksheet->set_column(3, 3, 25);
		$worksheet->set_column(4, 4, 8);
		$worksheet->set_column(5, 5, 12);
		$worksheet->set_column(6, 6, 30);
		$worksheet->set_column(7, 7, 25);
		$worksheet->set_column(8, 8, 8);
		$worksheet->set_column(9, 9, 12);
		$worksheet->set_column(10, 10, 8);
		$worksheet->set_column(11, 11, 20);

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "ลำดับ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 2, "ส่วนราชการและตำแหน่งที่กำหนดไว้เดิม", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 6, "ส่วนราชการและตำแหน่งที่ขอกำหนดใหม่", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 10, "อัตรา", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 11, "หมายเหตุ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "ที่", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 1, "เลขที่", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 2, "ชื่อตำแหน่งในการบริหารงาน", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 3, "ชื่อตำแหน่งในสายงาน", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 4, "ประเภท", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 5, "ระดับ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 6, "ชื่อตำแหน่งในการบริหารงาน", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 7, "ชื่อตำแหน่งในสายงาน", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 8, "ประเภท", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 9, "ระดับ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 10, "เงินเดือน", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 4, "ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 8, "ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
	} // function		
		
	if($DPISDB=="odbc"){
		$cmd = " select		a.ORD_SEQ, a.ORD_POS_NO, a.ORG_ID as ORD_ORG_ID, a.ORG_ID_1 as ORD_ORG_ID_1, 
											a.ORG_ID_2 as ORD_ORG_ID_2, a.ORG_ID_3 as ORD_ORG_ID_3, a.ORG_ID_4 as ORD_ORG_ID_4, 
											a.ORG_ID_5 as ORD_ORG_ID_5, a.PL_CODE as ORD_PL_CODE, a.PM_CODE as ORD_PM_CODE, 
											a.CL_NAME as ORD_CL_NAME, a.ORD_SALARY, a.LEVEL_NO as ORD_LEVEL_NO, a.ORD_REMARK,
											b.POS_NO, b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2, b.ORG_ID_3, b.ORG_ID_4, b.ORG_ID_5, b.PL_CODE, 
											b.PM_CODE, b.CL_NAME, b.POS_SALARY, b.LEVEL_NO, b.POS_REMARK
						 from			PER_ORDER_DTL a, PER_POSITION b
						 where		a.POS_ID_OLD=b.POS_ID and a.ORD_ID=$ORD_ID
						 order by 	$order_str ";
	}elseif($DPISDB=="oci8"){
		$cmd = " select		a.ORD_SEQ, a.ORD_POS_NO, a.ORG_ID as ORD_ORG_ID, a.ORG_ID_1 as ORD_ORG_ID_1, 
											a.ORG_ID_2 as ORD_ORG_ID_2, a.ORG_ID_3 as ORD_ORG_ID_3, a.ORG_ID_4 as ORD_ORG_ID_4, 
											a.ORG_ID_5 as ORD_ORG_ID_5, a.PL_CODE as ORD_PL_CODE, a.PM_CODE as ORD_PM_CODE, 
											a.CL_NAME as ORD_CL_NAME, a.ORD_SALARY, a.LEVEL_NO as ORD_LEVEL_NO, a.ORD_REMARK,
											b.POS_NO, b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2, b.ORG_ID_3, b.ORG_ID_4, b.ORG_ID_5, b.PL_CODE, 
											b.PM_CODE, b.CL_NAME, b.POS_SALARY, b.LEVEL_NO, b.POS_REMARK
						 from			PER_ORDER_DTL a, PER_POSITION b
						 where		a.POS_ID_OLD=b.POS_ID and a.ORD_ID=$ORD_ID
						 order by 	$order_str ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select		a.ORD_SEQ, a.ORD_POS_NO, a.ORG_ID as ORD_ORG_ID, a.ORG_ID_1 as ORD_ORG_ID_1, 
											a.ORG_ID_2 as ORD_ORG_ID_2, a.ORG_ID_3 as ORD_ORG_ID_3, a.ORG_ID_4 as ORD_ORG_ID_4, 
											a.ORG_ID_5 as ORD_ORG_ID_5, a.PL_CODE as ORD_PL_CODE, a.PM_CODE as ORD_PM_CODE, 
											a.CL_NAME as ORD_CL_NAME, a.ORD_SALARY, a.LEVEL_NO as ORD_LEVEL_NO, a.ORD_REMARK,
											b.POS_NO, b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2, b.ORG_ID_3, b.ORG_ID_4, b.ORG_ID_5, b.PL_CODE, 
											b.PM_CODE, b.CL_NAME, b.POS_SALARY, b.LEVEL_NO, b.POS_REMARK
						 from			PER_ORDER_DTL a, PER_POSITION b
						 where		a.POS_ID_OLD=b.POS_ID and a.ORD_ID=$ORD_ID
						 order by 	$order_str ";
	} // end if
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data_count = $data_row = 0;
	$ORG_ID = $ORG_ID_1 = $ORG_ID_2 = -1;
	$ORD_ORG_ID = $ORD_ORG_ID_1 = $ORD_ORG_ID_2 = -1;
	while($data = $db_dpis->get_array()){
		$ORG_ID = trim($data[ORG_ID]);
		$cmd = " select OT_CODE, ORG_NAME from PER_ORG where ORG_ID='$ORG_ID' ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$OT_CODE = trim($data2[OT_CODE]);
		$ORG_NAME = trim($data2[ORG_NAME]);

		$ORD_ORG_ID = trim($data[ORD_ORG_ID]);
		$cmd = " select OT_CODE, ORG_NAME from PER_ORG where ORG_ID='$ORD_ORG_ID' ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$ORD_OT_CODE = trim($data2[OT_CODE]);
		$ORD_ORG_NAME = trim($data2[ORG_NAME]);

		$ORG_ID_1 = trim($data[ORG_ID_1]);
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID='$ORG_ID_1' ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$ORG_NAME_1 = trim($data2[ORG_NAME]);

		$ORD_ORG_ID_1 = trim($data[ORD_ORG_ID_1]);
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID='$ORD_ORG_ID_1' ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$ORD_ORG_NAME_1 = trim($data2[ORG_NAME]);

		$ORG_ID_2 = trim($data[ORG_ID_2]);
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID='$ORG_ID_2' ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$ORG_NAME_2 = trim($data2[ORG_NAME]);

		$ORD_ORG_ID_2 = trim($data[ORD_ORG_ID_2]);
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID='$ORD_ORG_ID_2' ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$ORD_ORG_NAME_2 = trim($data2[ORG_NAME]);

		$ORG_ID_3 = trim($data[ORG_ID_3]);
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID='$ORG_ID_3' ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$ORG_NAME_3 = trim($data2[ORG_NAME]);

		$ORD_ORG_ID_3 = trim($data[ORD_ORG_ID_3]);
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID='$ORD_ORG_ID_3' ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$ORD_ORG_NAME_3 = trim($data2[ORG_NAME]);

		$ORG_ID_4 = trim($data[ORG_ID_4]);
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID='$ORG_ID_4' ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$ORG_NAME_4 = trim($data2[ORG_NAME]);

		$ORD_ORG_ID_4 = trim($data[ORD_ORG_ID_4]);
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID='$ORD_ORG_ID_4' ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$ORD_ORG_NAME_4 = trim($data2[ORG_NAME]);

		$ORG_ID_5 = trim($data[ORG_ID_5]);
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID='$ORG_ID_5' ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$ORG_NAME_5 = trim($data2[ORG_NAME]);

		$ORD_ORG_ID_5 = trim($data[ORD_ORG_ID_5]);
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID='$ORD_ORG_ID_5' ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$ORD_ORG_NAME_5 = trim($data2[ORG_NAME]);

		$data_row++;
		$POS_NO = trim($data[POS_NO]);
		$PM_CODE = trim($data[PM_CODE]);
		$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$PM_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$PM_NAME = trim($data2[PM_NAME]);
		
		$PL_CODE = trim($data[PL_CODE]);
		$cmd = " select PL_NAME from PER_LINE where trim(PL_CODE)='$PL_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$PL_NAME = trim($data2[PL_NAME]);
		if (!$PM_NAME) $PM_NAME = $PL_NAME;
		
		$CL_NAME = trim($data[CL_NAME]);
		$POS_SALARY = trim($data[POS_SALARY]);
		$POS_REMARK = trim($data[POS_REMARK]);

		$LEVEL_NO = trim($data[LEVEL_NO]);
		$cmd = " select POSITION_TYPE, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$LEVEL_NO' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$POSITION_TYPE = trim($data2[POSITION_TYPE]);
		$POSITION_LEVEL = trim($data2[POSITION_LEVEL]);
		
		$ORD_POS_NO = trim($data[ORD_POS_NO]);
		$ORD_PM_CODE = trim($data[ORD_PM_CODE]);
		$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$ORD_PM_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$ORD_PM_NAME = trim($data2[PM_NAME]);
		
		$ORD_PL_CODE = trim($data[ORD_PL_CODE]);
		$cmd = " select PL_NAME from PER_LINE where trim(PL_CODE)='$ORD_PL_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$ORD_PL_NAME = trim($data2[PL_NAME]);
		if (!$ORD_PM_NAME) $ORD_PM_NAME = $ORD_PL_NAME;
		
		$ORD_CL_NAME = trim($data[ORD_CL_NAME]);
		$ORD_SALARY = trim($data[ORD_SALARY]);
		$ORD_REMARK = trim($data[ORD_REMARK]);
		
		$ORD_LEVEL_NO = trim($data[ORD_LEVEL_NO]);
		$cmd = " select POSITION_TYPE, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$ORD_LEVEL_NO' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$ORD_POSITION_TYPE = trim($data2[POSITION_TYPE]);
		$ORD_POSITION_LEVEL = trim($data2[POSITION_LEVEL]);
		
		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][order] = $data_row;
		$arr_content[$data_count][pos_no] = $POS_NO;
		$arr_content[$data_count][pm_name] = $PM_NAME;
//		$arr_content[$data_count][pl_name] = ($PL_NAME)?"$PL_NAME $CL_NAME":"";
		$arr_content[$data_count][pl_name] = $PL_NAME;
		$arr_content[$data_count][cl_name] = $CL_NAME;
		$arr_content[$data_count][pos_salary] = $POS_SALARY;
		$arr_content[$data_count][position_type] = $POSITION_TYPE;
		$arr_content[$data_count][position_level] = $POSITION_LEVEL;
		
		$arr_content[$data_count][ord_pos_no] = $ORD_POS_NO;
		$arr_content[$data_count][ord_pm_name] = $ORD_PM_NAME;
//		$arr_content[$data_count][ord_pl_name] = ($ORD_PL_NAME)?"$ORD_PL_NAME $ORD_CL_NAME":"";
		$arr_content[$data_count][ord_pl_name] = $ORD_PL_NAME;
		$arr_content[$data_count][ord_cl_name] = $ORD_CL_NAME;
		$arr_content[$data_count][ord_salary] = $ORD_SALARY;
		$arr_content[$data_count][ord_position_type] = $ORD_POSITION_TYPE;
		$arr_content[$data_count][ord_position_level] = $ORD_POSITION_LEVEL;
		$arr_content[$data_count][ord_remark] = $ORD_REMARK;
//		if ($order_by==1) {
			if ($ORG_NAME_5 || $ORD_ORG_NAME_5) {
				$data_count++;
				$arr_content[$data_count][type] = "CONTENT";
				$arr_content[$data_count][pm_name] = $ORG_NAME_5;
				$arr_content[$data_count][ord_pm_name] = $ORD_ORG_NAME_5;
			}
			if ($ORG_NAME_4 || $ORD_ORG_NAME_4) {
				$data_count++;
				$arr_content[$data_count][type] = "CONTENT";
				$arr_content[$data_count][pm_name] = $ORG_NAME_4;
				$arr_content[$data_count][ord_pm_name] = $ORD_ORG_NAME_4;
			}
			if ($ORG_NAME_3 || $ORD_ORG_NAME_3) {
				$data_count++;
				$arr_content[$data_count][type] = "CONTENT";
				$arr_content[$data_count][pm_name] = $ORG_NAME_3;
				$arr_content[$data_count][ord_pm_name] = $ORD_ORG_NAME_3;
			}
			if ($ORG_NAME_2 || $ORD_ORG_NAME_2) {
				$data_count++;
				$arr_content[$data_count][type] = "CONTENT";
				$arr_content[$data_count][pm_name] = $ORG_NAME_2;
				$arr_content[$data_count][ord_pm_name] = $ORD_ORG_NAME_2;
			}
			if ($ORG_NAME_1 || $ORD_ORG_NAME_1) {
				$data_count++;
				$arr_content[$data_count][type] = "CONTENT";
				$arr_content[$data_count][pm_name] = $ORG_NAME_1;
				$arr_content[$data_count][ord_pm_name] = $ORD_ORG_NAME_1;
			}
			if ($ORG_NAME || $ORD_ORG_NAME) {
				$data_count++;
				$arr_content[$data_count][type] = "CONTENT";
				$arr_content[$data_count][pm_name] = $ORG_NAME;
				$arr_content[$data_count][ord_pm_name] = $ORD_ORG_NAME;
			}
			if ($OT_CODE=="01" || $ORD_OT_CODE=="01") {
				$data_count++;
				$arr_content[$data_count][type] = "CONTENT";
				if ($OT_CODE=="01") $arr_content[$data_count][pm_name] = $DEPARTMENT_NAME;
				if ($ORD_OT_CODE=="01") $arr_content[$data_count][ord_pm_name] = $DEPARTMENT_NAME;
			}
//		}
		
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
			$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTitle", "B", "C", "", 1));
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
			$worksheet->write($xlsRow, 10, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 11, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
		} // end if

		print_header();

		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$CONTENT_TYPE = $arr_content[$data_count][type];
			$ORDER = $arr_content[$data_count][order];
			$POS_NO = $arr_content[$data_count][pos_no];
			$PM_NAME = $arr_content[$data_count][pm_name];
			$PL_NAME = $arr_content[$data_count][pl_name];
			$CL_NAME = $arr_content[$data_count][cl_name];
			$POS_SALARY = $arr_content[$data_count][pos_salary];
			$POSITION_TYPE = $arr_content[$data_count][position_type];
			$POSITION_LEVEL = $arr_content[$data_count][position_level];
			$ORG_NAME = $arr_content[$data_count][org_name];
			$ORD_POS_NO = $arr_content[$data_count][ord_pos_no];
			$ORD_PM_NAME = $arr_content[$data_count][ord_pm_name];
			$ORD_PL_NAME = $arr_content[$data_count][ord_pl_name];
			$ORD_CL_NAME = $arr_content[$data_count][ord_cl_name];
			$ORD_SALARY = $arr_content[$data_count][ord_salary];
			$ORD_ORG_NAME = $arr_content[$data_count][ord_org_name];
			$ORD_POSITION_TYPE = $arr_content[$data_count][ord_position_type];
			$ORD_POSITION_LEVEL = $arr_content[$data_count][ord_position_level];
			$ORD_REMARK = $arr_content[$data_count][ord_remark];
			
			if($CONTENT_TYPE=="ORG"){
				$xlsRow++;
				$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write_string($xlsRow, 2, "$ORG_NAME", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 4, "", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write_string($xlsRow, 6, "", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write_string($xlsRow, 7, "$ORD_ORG_NAME", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 8, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 9, "", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 10, "", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write_string($xlsRow, 11, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			}elseif($CONTENT_TYPE=="CONTENT"){
				$xlsRow++;
				$worksheet->write_string($xlsRow, 0, "$ORDER", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write_string($xlsRow, 1, "$POS_NO", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write_string($xlsRow, 2, "$PM_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 3, "$PL_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 4, "$POSITION_TYPE", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 5, "$POSITION_LEVEL", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 6, "$ORD_PM_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 7, "$ORD_PL_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 8, "$ORD_POSITION_TYPE", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 9, "$ORD_POSITION_LEVEL", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 10, ($ORD_SALARY?number_format($ORD_SALARY):""), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write_string($xlsRow, 11, "$ORD_REMARK", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			} // end if
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
		$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTitle", "B", "C", "", 1));
	} // end if

	$workbook->close();

	ini_set("max_execution_time", 30);
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"การปรับปรุงการกำหนดตำแหน่ง.xls\"");
	header("Content-Disposition: inline; filename=\"การปรับปรุงการกำหนดตำแหน่ง.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>