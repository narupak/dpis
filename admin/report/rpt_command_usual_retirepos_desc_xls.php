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
	$report_title = "จัดทำบัญชีคำขอยุบเลิก/ขอจัดสรร ตำแหน่งเกษียณ (ปกติ)||สำนักนายกรัฐมนตรี||(ส่งพร้อมหนังสือสำนักนายกรัฐมนตรี||ที่". str_repeat(".", 40) ." ลงวันที่". str_repeat(".", 40) .")";
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
		$worksheet->set_column(2, 2, 40);
		$worksheet->set_column(3, 3, 8);
		$worksheet->set_column(4, 4, 6);
		$worksheet->set_column(5, 5, 40);
		$worksheet->set_column(6, 6, 8);
		$worksheet->set_column(7, 7, 10);
		$worksheet->set_column(8, 8, 10);
		$worksheet->set_column(9, 9, 8);

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "ลำดับ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "หน่วยงาน/ตำแหน่งที่ว่างจากการเกษียณอายุ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 4, "หน่วยงาน/ตำแหน่งว่างที่ยุบเลิกแทนตำแหน่งเกษียณอายุ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "ที่", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 1, "ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 2, "ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 3, "ระดับ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 4, "ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 5, "ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 6, "ระดับ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 7, "อัตรา", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 8, "วัน เดือน ปี", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 9, "หมายเหตุ", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 1, "เลขที่", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 4, "เลขที่", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 7, "เงินเดือน", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 8, "ที่ยุบเลิก", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
	} // function		
		
	if($DPISDB=="odbc"){
		$cmd = " select			b.POS_NO as RETIRE_POS_NO, b.ORG_ID as RETIRE_ORG_ID, b.ORG_ID_1 as RETIRE_ORG_ID_1, 
											b.ORG_ID_2 as RETIRE_ORG_ID_2, 
											b.PL_CODE as RETIRE_PL_CODE, b.PM_CODE as RETIRE_PM_CODE, b.CL_NAME as RETIRE_CL_NAME,
											c.POS_NO as DROP_POS_NO, c.ORG_ID as DROP_ORG_ID, c.ORG_ID_1 as DROP_ORG_ID_1, 
											c.ORG_ID_2 as DROP_ORG_ID_2, c.PL_CODE as DROP_PL_CODE, c.PM_CODE as DROP_PM_CODE, 
											c.CL_NAME as DROP_CL_NAME, c.POS_SALARY as DROP_SALARY, 
											a.REQ_RESULT, LEFT(trim(a.REQ_EFF_DATE), 10) as REQ_EFF_DATE
						 from			(
							 					PER_REQ2_DTL a
							 					inner join PER_POSITION b on (a.POS_ID_RETIRE=b.POS_ID)
											) left join PER_POSITION c on (a.POS_ID_DROP=c.POS_ID)
						 where		a.REQ_ID=$REQ_ID
						 order by 	b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2, IIf(IsNull(b.POS_NO), 0, CLng(b.POS_NO))
					   ";
	}elseif($DPISDB=="oci8"){
		$cmd = " select			b.POS_NO as RETIRE_POS_NO, b.ORG_ID as RETIRE_ORG_ID, b.ORG_ID_1 as RETIRE_ORG_ID_1, 
											b.ORG_ID_2 as RETIRE_ORG_ID_2, 
											b.PL_CODE as RETIRE_PL_CODE, b.PM_CODE as RETIRE_PM_CODE, b.CL_NAME as RETIRE_CL_NAME,
											c.POS_NO as DROP_POS_NO, c.ORG_ID as DROP_ORG_ID, c.ORG_ID_1 as DROP_ORG_ID_1, 
											c.ORG_ID_2 as DROP_ORG_ID_2, c.PL_CODE as DROP_PL_CODE, c.PM_CODE as DROP_PM_CODE, 
											c.CL_NAME as DROP_CL_NAME, c.POS_SALARY as DROP_SALARY, 
											a.REQ_RESULT, SUBSTR(trim(a.REQ_EFF_DATE), 1, 10) as REQ_EFF_DATE
						 from			PER_REQ2_DTL a, PER_POSITION b, PER_POSITION c
						 where		a.POS_ID_RETIRE=b.POS_ID and a.POS_ID_DROP=c.POS_ID(+) and a.REQ_ID=$REQ_ID
						 order by 	b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2, to_number(replace(b.POS_NO,'-',''))  
					   ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			b.POS_NO as RETIRE_POS_NO, b.ORG_ID as RETIRE_ORG_ID, b.ORG_ID_1 as RETIRE_ORG_ID_1, 
											b.ORG_ID_2 as RETIRE_ORG_ID_2, 
											b.PL_CODE as RETIRE_PL_CODE, b.PM_CODE as RETIRE_PM_CODE, b.CL_NAME as RETIRE_CL_NAME,
											c.POS_NO as DROP_POS_NO, c.ORG_ID as DROP_ORG_ID, c.ORG_ID_1 as DROP_ORG_ID_1, 
											c.ORG_ID_2 as DROP_ORG_ID_2, c.PL_CODE as DROP_PL_CODE, c.PM_CODE as DROP_PM_CODE, 
											c.CL_NAME as DROP_CL_NAME, c.POS_SALARY as DROP_SALARY, 
											a.REQ_RESULT, LEFT(trim(a.REQ_EFF_DATE), 10) as REQ_EFF_DATE
						 from			(
							 					PER_REQ2_DTL a
							 					inner join PER_POSITION b on (a.POS_ID_RETIRE=b.POS_ID)
											) left join PER_POSITION c on (a.POS_ID_DROP=c.POS_ID)
						 where		a.REQ_ID=$REQ_ID
						order by 	b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2, b.POS_NO  
					   ";
	} // end if
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data_count = $data_row = 0;
	$RETIRE_ORG_ID = $RETIRE_ORG_ID_1 = $RETIRE_ORG_ID_2 = 1;
	while($data = $db_dpis->get_array()){
		if($RETIRE_ORG_ID != $data[RETIRE_ORG_ID]){		
			$RETIRE_ORG_ID = trim($data[RETIRE_ORG_ID]);
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID='$RETIRE_ORG_ID' ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$RETIRE_ORG_NAME = trim($data2[ORG_NAME]);

			$DROP_ORG_ID = trim($data[DROP_ORG_ID]);
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID='$DROP_ORG_ID' ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$DROP_ORG_NAME = trim($data2[ORG_NAME]);

			if($RETIRE_ORG_NAME || $DROP_ORG_NAME){
				$arr_content[$data_count][type] = "ORG";
				$arr_content[$data_count][retire_org_name] = $RETIRE_ORG_NAME;
				$arr_content[$data_count][drop_org_name] = $DROP_ORG_NAME;
				
				$data_count++;
			} // end if
		}elseif($data[RETIRE_ORG_ID] != $data[DROP_ORG_ID]){
			$DROP_ORG_ID = trim($data[DROP_ORG_ID]);
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID='$DROP_ORG_ID' ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$DROP_ORG_NAME = trim($data2[ORG_NAME]);

			if($RETIRE_ORG_NAME || $DROP_ORG_NAME){
				$arr_content[$data_count][type] = "ORG";
				$arr_content[$data_count][retire_org_name] = $RETIRE_ORG_NAME;
				$arr_content[$data_count][drop_org_name] = $DROP_ORG_NAME;
				
				$data_count++;
			} // end if
		} // end if

		if($RETIRE_ORG_ID_1 != $data[RETIRE_ORG_ID_1]){
			$RETIRE_ORG_ID_1 = trim($data[RETIRE_ORG_ID_1]);
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID='$RETIRE_ORG_ID_1' ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$RETIRE_ORG_NAME_1 = trim($data2[ORG_NAME]);

			$DROP_ORG_ID_1 = trim($data[DROP_ORG_ID_1]);
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID='$DROP_ORG_ID_1' ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$DROP_ORG_NAME_1 = trim($data2[ORG_NAME]);

			if($RETIRE_ORG_NAME_1 || $DROP_ORG_NAME_1){
				$arr_content[$data_count][type] = "ORG";
				$arr_content[$data_count][retire_org_name] = str_repeat(" ", 5) . $RETIRE_ORG_NAME_1;
				$arr_content[$data_count][drop_org_name] = str_repeat(" ", 5) . $DROP_ORG_NAME_1;
				
				$data_count++;
			} // end if
		}elseif($data[RETIRE_ORG_ID_1] != $data[DROP_ORG_ID_1]){
			$DROP_ORG_ID_1 = trim($data[DROP_ORG_ID_1]);
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID='$DROP_ORG_ID_1' ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$DROP_ORG_NAME_1 = trim($data2[ORG_NAME]);

			if($RETIRE_ORG_NAME_1 || $DROP_ORG_NAME_1){
				$arr_content[$data_count][type] = "ORG";
				$arr_content[$data_count][retire_org_name] = str_repeat(" ", 5) . $RETIRE_ORG_NAME_1;
				$arr_content[$data_count][drop_org_name] = str_repeat(" ", 5) . $DROP_ORG_NAME_1;
				
				$data_count++;
			} // end if
		} // end if

		if($RETIRE_ORG_ID_2 != $data[RETIRE_ORG_ID_2]){
			$RETIRE_ORG_ID_2 = trim($data[RETIRE_ORG_ID_2]);
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID='$RETIRE_ORG_ID_2' ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$RETIRE_ORG_NAME_2 = trim($data2[ORG_NAME]);

			$DROP_ORG_ID_2 = trim($data[DROP_ORG_ID_2]);
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID='$DROP_ORG_ID_2' ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$DROP_ORG_NAME_2 = trim($data2[ORG_NAME]);

			if($RETIRE_ORG_NAME_2 || $DROP_ORG_NAME_2){
				$arr_content[$data_count][type] = "ORG";
				$arr_content[$data_count][retire_org_name] = str_repeat(" ", 10) . $RETIRE_ORG_NAME_2;
				$arr_content[$data_count][drop_org_name] = str_repeat(" ", 10) . $DROP_ORG_NAME_2;
				
				$data_count++;
			} // end if
		}elseif($data[RETIRE_ORG_ID_2] != $data[DROP_ORG_ID_2]){
			$DROP_ORG_ID_2 = trim($data[DROP_ORG_ID_2]);
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID='$DROP_ORG_ID_2' ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$DROP_ORG_NAME_2 = trim($data2[ORG_NAME]);

			if($RETIRE_ORG_NAME_2 || $DROP_ORG_NAME_2){
				$arr_content[$data_count][type] = "ORG";
				$arr_content[$data_count][retire_org_name] = str_repeat(" ", 10) . $RETIRE_ORG_NAME_2;
				$arr_content[$data_count][drop_org_name] = str_repeat(" ", 10) . $DROP_ORG_NAME_2;
				
				$data_count++;
			} // end if
		} // end if

		$data_row++;
		$RETIRE_POS_NO = trim($data[RETIRE_POS_NO]);
		$RETIRE_PM_CODE = trim($data[RETIRE_PM_CODE]);
		$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$RETIRE_PM_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$RETIRE_PM_NAME = trim($data2[PM_NAME]);
		
		$RETIRE_PL_CODE = trim($data[RETIRE_PL_CODE]);
		$cmd = " select PL_NAME from PER_LINE where trim(PL_CODE)='$RETIRE_PL_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$RETIRE_PL_NAME = trim($data2[PL_NAME]);
		
		$RETIRE_CL_NAME = trim($data[RETIRE_CL_NAME]);

		$DROP_POS_NO = trim($data[DROP_POS_NO]);
		$DROP_PM_CODE = trim($data[DROP_PM_CODE]);
		$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$DROP_PM_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$DROP_PM_NAME = trim($data2[PM_NAME]);
		
		$DROP_PL_CODE = trim($data[DROP_PL_CODE]);
		$cmd = " select PL_NAME from PER_LINE where trim(PL_CODE)='$DROP_PL_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$DROP_PL_NAME = trim($data2[PL_NAME]);
		
		$DROP_CL_NAME = trim($data[DROP_CL_NAME]);
		$DROP_SALARY = trim($data[DROP_SALARY]);

		$REQ_RESULT = trim($data[REQ_RESULT]);
		$REQ_EFF_DATE = trim($data[REQ_EFF_DATE]);
		if($REQ_EFF_DATE && $REQ_RESULT!=""){
			$REQ_EFF_DATE = show_date_format($REQ_EFF_DATE,$DATE_DISPLAY);
		}else{
			$REQ_EFF_DATE = "";
		} // end if
		
		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][order] = $data_row;
		$arr_content[$data_count][retire_pos_no] = $RETIRE_POS_NO;
		$arr_content[$data_count][retire_position] = ($RETIRE_PM_NAME)?($RETIRE_PM_NAME.(($RETIRE_PL_NAME)?" ($RETIRE_PL_NAME $RETIRE_CL_NAME)":"")):(($RETIRE_PL_NAME)?"$RETIRE_PL_NAME $RETIRE_CL_NAME":"");
		$arr_content[$data_count][retire_cl_name] = $RETIRE_CL_NAME;
		
		$arr_content[$data_count][drop_pos_no] = $DROP_POS_NO;
		$arr_content[$data_count][drop_position] = ($DROP_PM_NAME)?($DROP_PM_NAME.(($DROP_PL_NAME)?" ($DROP_PL_NAME $DROP_CL_NAME)":"")):(($DROP_PL_NAME)?"$DROP_PL_NAME $DROP_CL_NAME":"");
		$arr_content[$data_count][drop_cl_name] = $DROP_CL_NAME;
		$arr_content[$data_count][drop_salary] = $DROP_SALARY;
		$arr_content[$data_count][req_eff_date] = $REQ_EFF_DATE;
		
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
			$RETIRE_POS_NO = $arr_content[$data_count][retire_pos_no];
			$RETIRE_POSITION = $arr_content[$data_count][retire_position];
			$RETIRE_CL_NAME = $arr_content[$data_count][retire_cl_name];
			$RETIRE_ORG_NAME = $arr_content[$data_count][retire_org_name];
			$DROP_POS_NO = $arr_content[$data_count][drop_pos_no];
			$DROP_POSITION = $arr_content[$data_count][drop_position];
			$DROP_CL_NAME = $arr_content[$data_count][drop_cl_name];
			$DROP_SALARY = $arr_content[$data_count][drop_salary];
			$DROP_ORG_NAME = $arr_content[$data_count][drop_org_name];
			$REQ_EFF_DATE = $arr_content[$data_count][req_eff_date];
			
			if($CONTENT_TYPE=="ORG"){
				$xlsRow++;
				$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write_string($xlsRow, 2, "$RETIRE_ORG_NAME", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 4, "", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write_string($xlsRow, 5, "$DROP_ORG_NAME", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 6, "", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 7, "", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write_string($xlsRow, 8, "", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 9, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			}elseif($CONTENT_TYPE=="CONTENT"){
				$xlsRow++;
				$worksheet->write_string($xlsRow, 0, "$ORDER.", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write_string($xlsRow, 1, "$RETIRE_POS_NO", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write_string($xlsRow, 2, "$RETIRE_POSITION", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 3, "$RETIRE_CL_NAME", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 4, "$DROP_POS_NO", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write_string($xlsRow, 5, "$DROP_POSITION", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 6, "$DROP_CL_NAME", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 7, ($DROP_SALARY?number_format($DROP_SALARY):""), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write_string($xlsRow, 8, "$REQ_EFF_DATE", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 9, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
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
	} // end if

	$workbook->close();

	ini_set("max_execution_time", 30);
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"บัญชีคำขอยุบเลิกตำแหน่งเกษียณ(ปกติ).xls\"");
	header("Content-Disposition: inline; filename=\"บัญชีคำขอยุบเลิกตำแหน่งเกษียณ(ปกติ).xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>