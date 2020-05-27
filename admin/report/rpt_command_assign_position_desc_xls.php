<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	$cmd = " select 	a.ORG_ID, b.ORG_NAME, b.ORG_ID_REF
					 from 		PER_CONTROL a, PER_ORG b
					 where	a.ORG_ID=b.ORG_ID
				   ";
	if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$DEPARTMENT_ID = $data[ORG_ID];
	$DEPARTMENT_NAME = $data[ORG_NAME];
	$MINISTRY_ID = $data[ORG_ID_REF];
	
	$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
	if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	$db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data = $db_dpis->get_array();
	$MINISTRY_NAME = $data[ORG_NAME];

	$company_name = "";
	$report_title = "จัดทำบัญชีคำขอกำหนดตำแหน่งเพิ่ม||(ส่งพร้อมหนังสือสำนักนายกรัฐมนตรี||ที่". str_repeat(".", 40) ." ลงวันที่". str_repeat(".", 40) .")";
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
		global $worksheet, $xlsRow,$select_org_structure;

		$worksheet->set_column(0, 0, 6);
		$worksheet->set_column(1, 1, 6);
		$worksheet->set_column(2, 2, 25);
		$worksheet->set_column(3, 3, 25);
		$worksheet->set_column(4, 4, 8);
		$worksheet->set_column(5, 5, 8);
		$worksheet->set_column(6, 6, 6);
		$worksheet->set_column(7, 7, 25);
		$worksheet->set_column(8, 8, 25);
		$worksheet->set_column(9, 9, 8);
		$worksheet->set_column(10, 10, 8);
		$worksheet->set_column(11, 11, 10);

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "ลำดับ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "ส่วนราชการและตำแหน่งที่ขอ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 6, "ส่วนราชการและตำแหน่งที่นำมาเกลี่ย", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 11, "หมายเหตุ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "ที่", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 1, "ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 2, "ชื่อตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 3, "ชื่อตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 4, "ระดับ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 5, "ขั้น", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 6, "ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 7, "ชื่อตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 8, "ชื่อตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 9, "ระดับ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 10, "ขั้น", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 1, "เลขที่", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 2, "ในการบริหาร", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 3, "ในสายงาน", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 6, "เลขที่", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 7, "ในการบริหาร", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 8, "ในสายงาน", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
	} // function		
		
	if($DPISDB=="odbc"){
		$cmd = " select			a.REQ_SEQ, a.REQ_POS_NO, a.ORG_ID as REQ_ORG_ID, a.ORG_ID_1 as REQ_ORG_ID_1, a.ORG_ID_2 as REQ_ORG_ID_2,
											a.PL_CODE as REQ_PL_CODE, a.PM_CODE as REQ_PM_CODE, a.CL_NAME as REQ_CL_NAME, a.REQ_SALARY,
											c.POS_NO, c.ORG_ID, c.ORG_ID_1, c.ORG_ID_2, c.PL_CODE, c.PM_CODE, c.CL_NAME, c.POS_SALARY
						 from			PER_REQ1_DTL1 a, PER_REQ1_DTL2 b, PER_POSITION c
						 where		a.REQ_ID=b.REQ_ID and a.REQ_SEQ=b.REQ_SEQ and b.POS_ID=c.POS_ID and a.REQ_ID=$REQ_ID
						 order by 	a.ORG_ID, a.ORG_ID_1, a.ORG_ID_2, IIf(IsNull(a.REQ_POS_NO), 0, CLng(a.REQ_POS_NO))
					   ";
	}elseif($DPISDB=="oci8"){
		$cmd = " select			a.REQ_SEQ, a.REQ_POS_NO, a.ORG_ID as REQ_ORG_ID, a.ORG_ID_1 as REQ_ORG_ID_1, a.ORG_ID_2 as REQ_ORG_ID_2,
											a.PL_CODE as REQ_PL_CODE, a.PM_CODE as REQ_PM_CODE, a.CL_NAME as REQ_CL_NAME, a.REQ_SALARY,
											c.POS_NO, c.ORG_ID, c.ORG_ID_1, c.ORG_ID_2, c.PL_CODE, c.PM_CODE, c.CL_NAME, c.POS_SALARY
						 from			PER_REQ1_DTL1 a, PER_REQ1_DTL2 b, PER_POSITION c
						 where		a.REQ_ID=b.REQ_ID and a.REQ_SEQ=b.REQ_SEQ and b.POS_ID=c.POS_ID and a.REQ_ID=$REQ_ID
						 order by 	a.ORG_ID, a.ORG_ID_1, a.ORG_ID_2, to_number(replace(a.REQ_POS_NO,'-',''))  
					   ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			a.REQ_SEQ, a.REQ_POS_NO, a.ORG_ID as REQ_ORG_ID, a.ORG_ID_1 as REQ_ORG_ID_1, a.ORG_ID_2 as REQ_ORG_ID_2,
											a.PL_CODE as REQ_PL_CODE, a.PM_CODE as REQ_PM_CODE, a.CL_NAME as REQ_CL_NAME, a.REQ_SALARY,
											c.POS_NO, c.ORG_ID, c.ORG_ID_1, c.ORG_ID_2, c.PL_CODE, c.PM_CODE, c.CL_NAME, c.POS_SALARY
						 from			PER_REQ1_DTL1 a, PER_REQ1_DTL2 b, PER_POSITION c
						 where		a.REQ_ID=b.REQ_ID and a.REQ_SEQ=b.REQ_SEQ and b.POS_ID=c.POS_ID and a.REQ_ID=$REQ_ID
						 order by 	a.ORG_ID, a.ORG_ID_1, a.ORG_ID_2, a.REQ_POS_NO
						 ";
	} // end if
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data_count = $data_row = 0;
	$ORG_ID = $ORG_ID_1 = $ORG_ID_2 = 1;
	while($data = $db_dpis->get_array()){
		if($ORG_ID != $data[ORG_ID]){		
			$ORG_ID = trim($data[ORG_ID]);
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID='$ORG_ID' ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_NAME = trim($data2[ORG_NAME]);

			$REQ_ORG_ID = trim($data[REQ_ORG_ID]);
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID='$REQ_ORG_ID' ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$REQ_ORG_NAME = trim($data2[ORG_NAME]);

			if($ORG_NAME || $REQ_ORG_NAME){
				$arr_content[$data_count][type] = "ORG";
				$arr_content[$data_count][org_name] = $ORG_NAME;
				$arr_content[$data_count][req_org_name] = $REQ_ORG_NAME;
				
				$data_count++;
			} // end if
		}elseif($data[ORG_ID] != $data[REQ_ORG_ID]){
			$REQ_ORG_ID = trim($data[REQ_ORG_ID]);
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID='$REQ_ORG_ID' ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$REQ_ORG_NAME = trim($data2[ORG_NAME]);

			if($ORG_NAME || $REQ_ORG_NAME){
				$arr_content[$data_count][type] = "ORG";
				$arr_content[$data_count][org_name] = $ORG_NAME;
				$arr_content[$data_count][req_org_name] = $REQ_ORG_NAME;
				
				$data_count++;
			} // end if
		} // end if

		if($ORG_ID_1 != $data[ORG_ID_1]){
			$ORG_ID_1 = trim($data[ORG_ID_1]);
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID='$ORG_ID_1' ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_NAME_1 = trim($data2[ORG_NAME]);

			$REQ_ORG_ID_1 = trim($data[REQ_ORG_ID_1]);
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID='$REQ_ORG_ID_1' ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$REQ_ORG_NAME_1 = trim($data2[ORG_NAME]);

			if($ORG_NAME_1 || $REQ_ORG_NAME_1){
				$arr_content[$data_count][type] = "ORG";
				$arr_content[$data_count][org_name] = str_repeat(" ", 5) . $ORG_NAME_1;
				$arr_content[$data_count][req_org_name] = str_repeat(" ", 5) . $REQ_ORG_NAME_1;
				
				$data_count++;
			} // end if
		}elseif($data[ORG_ID_1] != $data[REQ_ORG_ID_1]){
			$REQ_ORG_ID_1 = trim($data[REQ_ORG_ID_1]);
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID='$REQ_ORG_ID_1' ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$REQ_ORG_NAME_1 = trim($data2[ORG_NAME]);

			if($ORG_NAME_1 || $REQ_ORG_NAME_1){
				$arr_content[$data_count][type] = "ORG";
				$arr_content[$data_count][org_name] = str_repeat(" ", 5) . $ORG_NAME_1;
				$arr_content[$data_count][req_org_name] = str_repeat(" ", 5) . $REQ_ORG_NAME_1;
				
				$data_count++;
			} // end if
		} // end if

		if($ORG_ID_2 != $data[ORG_ID_2]){
			$ORG_ID_2 = trim($data[ORG_ID_2]);
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID='$ORG_ID_2' ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_NAME_2 = trim($data2[ORG_NAME]);

			$REQ_ORG_ID_2 = trim($data[REQ_ORG_ID_2]);
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID='$REQ_ORG_ID_2' ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$REQ_ORG_NAME_2 = trim($data2[ORG_NAME]);

			if($ORG_NAME_2 || $REQ_ORG_NAME_2){
				$arr_content[$data_count][type] = "ORG";
				$arr_content[$data_count][org_name] = str_repeat(" ", 10) . $ORG_NAME_2;
				$arr_content[$data_count][req_org_name] = str_repeat(" ", 10) . $REQ_ORG_NAME_2;
				
				$data_count++;
			} // end if
		}elseif($data[ORG_ID_2] != $data[REQ_ORG_ID_2]){
			$REQ_ORG_ID_2 = trim($data[REQ_ORG_ID_2]);
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID='$REQ_ORG_ID_2' ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$REQ_ORG_NAME_2 = trim($data2[ORG_NAME]);

			if($ORG_NAME_2 || $REQ_ORG_NAME_2){
				$arr_content[$data_count][type] = "ORG";
				$arr_content[$data_count][org_name] = str_repeat(" ", 10) . $ORG_NAME_2;
				$arr_content[$data_count][req_org_name] = str_repeat(" ", 10) . $REQ_ORG_NAME_2;
				
				$data_count++;
			} // end if
		} // end if

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
		
		$CL_NAME = trim($data[CL_NAME]);
		$POS_SALARY = trim($data[POS_SALARY]);

		$REQ_POS_NO = trim($data[REQ_POS_NO]);
		$REQ_PM_CODE = trim($data[REQ_PM_CODE]);
		$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$REQ_PM_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$REQ_PM_NAME = trim($data2[PM_NAME]);
		
		$REQ_PL_CODE = trim($data[REQ_PL_CODE]);
		$cmd = " select PL_NAME from PER_LINE where trim(PL_CODE)='$REQ_PL_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$REQ_PL_NAME = trim($data2[PL_NAME]);
		
		$REQ_CL_NAME = trim($data[REQ_CL_NAME]);
		$REQ_SALARY = trim($data[REQ_SALARY]);
		
		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][order] = $data_row;
		$arr_content[$data_count][pos_no] = $POS_NO;
		$arr_content[$data_count][pm_name] = $PM_NAME;
		$arr_content[$data_count][pl_name] = ($PL_NAME)?"$PL_NAME $CL_NAME":"";
		$arr_content[$data_count][cl_name] = $CL_NAME;
		$arr_content[$data_count][pos_salary] = $POS_SALARY;
		
		$arr_content[$data_count][req_pos_no] = $REQ_POS_NO;
		$arr_content[$data_count][req_pm_name] = $REQ_PM_NAME;
		$arr_content[$data_count][req_pl_name] = ($REQ_PL_NAME)?"$REQ_PL_NAME $REQ_CL_NAME":"";
		$arr_content[$data_count][req_cl_name] = $REQ_CL_NAME;
		$arr_content[$data_count][req_salary] = $REQ_SALARY;
		
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
			$ORG_NAME = $arr_content[$data_count][org_name];
			$REQ_POS_NO = $arr_content[$data_count][req_pos_no];
			$REQ_PM_NAME = $arr_content[$data_count][req_pm_name];
			$REQ_PL_NAME = $arr_content[$data_count][req_pl_name];
			$REQ_CL_NAME = $arr_content[$data_count][req_cl_name];
			$REQ_SALARY = $arr_content[$data_count][req_salary];
			$REQ_ORG_NAME = $arr_content[$data_count][req_org_name];			
			
			if($CONTENT_TYPE=="ORG"){
				$xlsRow++;
				$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write_string($xlsRow, 2, "$REQ_ORG_NAME", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 4, "", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write_string($xlsRow, 6, "", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write_string($xlsRow, 7, "$ORG_NAME", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 8, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 9, "", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 10, "", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write_string($xlsRow, 11, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			}elseif($CONTENT_TYPE=="CONTENT"){
				$xlsRow++;
				$worksheet->write_string($xlsRow, 0, "$ORDER.", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write_string($xlsRow, 1, "$REQ_POS_NO", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write_string($xlsRow, 2, "$REQ_PM_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 3, "$REQ_PL_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 4, "$REQ_CL_NAME", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write($xlsRow, 5, ($REQ_SALARY?number_format($REQ_SALARY):""), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write_string($xlsRow, 6, "$POS_NO", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write_string($xlsRow, 7, "$PM_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 8, "$PL_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 9, "$CL_NAME", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 10, ($POS_SALARY?number_format($POS_SALARY):""), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write_string($xlsRow, 11, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
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
	header("Content-Type: application/x-msexcel; name=\"บัญชีคำขอกำหนดตำแหน่งเพิ่ม.xls\"");
	header("Content-Disposition: inline; filename=\"บัญชีคำขอกำหนดตำแหน่งเพิ่ม.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>