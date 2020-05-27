<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";
	
	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	ini_set("max_execution_time", $max_execution_time);

	$report_title = "รายงานการสงวนตำแหน่ง";
	$report_code = "S0201";

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$workbook = new writeexcel_workbook($fname);
	$worksheet = &$workbook->addworksheet("Sheet1");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	function print_header($xlsRow){
		global $worksheet;
		global $SEQ_NO_TITLE, $POS_NO_TITLE, $OT_TITLE, $PV_TITLE, $DOCNO_TITLE, $POS_CHANGE_DATE_TITLE, $CARDNO_TITLE, $PM_TITLE, $PL_TITLE, 
					$PT_TITLE, $LEVEL_TITLE, $CL_TITLE, $ORG_TITLE, $ORG_TITLE1, $ORG_TITLE2, $REMARK_TITLE, $NUMBER_DISPLAY;

		$worksheet->set_column(0, 0, 6);
		$worksheet->set_column(1, 1, 10);
		$worksheet->set_column(2, 2, 15);
		$worksheet->set_column(3, 3, 12);
		$worksheet->set_column(4, 4, 12);
		$worksheet->set_column(5, 5, 25);
		$worksheet->set_column(6, 6, 25);
		$worksheet->set_column(7, 7, 20);
		$worksheet->set_column(8, 8, 20);
		$worksheet->set_column(9, 9, 20);
		$worksheet->set_column(10, 10, 25);
		$worksheet->set_column(11, 11, 25);
		$worksheet->set_column(12, 12, 20);
		$worksheet->set_column(13, 13, 12);
		$worksheet->set_column(14, 14, 15);
		$worksheet->set_column(15, 15, 20);
		$worksheet->set_column(16, 16, 30);
		$worksheet->set_column(17, 17, 30);
		$worksheet->set_column(18, 18, 30);
		$worksheet->set_column(19, 19, 30);
		$worksheet->set_column(20, 20, 30);
		$worksheet->set_column(21, 21, 30);
		$worksheet->set_column(22, 22, 30);
		$worksheet->set_column(23, 23, 30);
		
		$worksheet->write($xlsRow, 0, "$SEQ_NO_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "$POS_NO_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 2, "$OT_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 3, "$PV_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 4, "ภารกิจ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 5, "ยุบเกษียณ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 6, "หมายเหตุ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 7, "$DOCNO_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 8, "$POS_CHANGE_DATE_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 9, "$CARDNO_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 10, "ผู้ครองตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 11, "$PM_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 12, "$PL_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 13, "$PT_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 14, "$LEVEL_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 15, "$CL_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 16, "$ORG_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 17, "$ORG_TITLE1", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 18, "$ORG_TITLE2", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 19, "สงวนตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 20, "$REMARK_TITLE 1", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 21, "$REMARK_TITLE 2", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 22, "รายละเอียด", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 23, "$DOCNO_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));

	} // end if
	
	if($DPISDB=="odbc") $POS_NO_NUM = "CLng(POS_NO)";
	elseif($DPISDB=="oci8") $POS_NO_NUM = "to_number(replace(POS_NO,'-',''))";
	elseif($DPISDB=="mysql") $POS_NO_NUM = "POS_NO+0";
	if(!$sort_by) $sort_by=1;
	$sort_type = (isset($sort_type))?  $sort_type : "1:asc";
	$arrSort=explode(":",$sort_type);
	$SortType[$arrSort[0]]	=$arrSort[1];
	if(!$order_by) $order_by=1;
	if($order_by==1){
		$org_cond = "";
		if ($POSITION_NO_CHAR=="Y") $org_cond = ", b.ORG_SEQ_NO $SortType[$order_by], b.ORG_CODE $SortType[$order_by]";
		if($DPISDB=="odbc")	$order_str = "a.DEPARTMENT_ID $SortType[$order_by]".$org_cond.", POS_NO_NAME $SortType[$order_by], iif(isnull(POS_NO),0,$POS_NO_NUM) $SortType[$order_by]";
		if($DPISDB=="oci8")	$order_str = "a.DEPARTMENT_ID $SortType[$order_by]".$org_cond.", POS_NO_NAME $SortType[$order_by], $POS_NO_NUM $SortType[$order_by]";
		if($DPISDB=="mysql")	$order_str = "a.DEPARTMENT_ID $SortType[$order_by]".$org_cond.", POS_NO_NAME $SortType[$order_by], $POS_NO_NUM $SortType[$order_by]";
  	}elseif($order_by==2) {
		$order_str = "PM_NAME $SortType[$order_by]";
  	} elseif($order_by==3){
		$order_str = "PL_NAME $SortType[$order_by]";
  	} elseif($order_by==4) {
		$order_str = "a.CL_NAME $SortType[$order_by]";
	} else 	if($order_by==5){
		$order_str = "a.LEVEL_NO $SortType[$order_by]";
  	}elseif($order_by==6) {
		$order_str = "a.DEPARTMENT_ID $SortType[$order_by]";
  	} elseif($order_by==7){
		$order_str = "b.ORG_NAME $SortType[$order_by]";
  	} elseif($order_by==8) {
		$order_str = "a.ORG_ID_1 $SortType[$order_by]";
	} elseif($order_by==9) {	
		$order_str = "a.POS_SEQ_NO $SortType[$order_by]";
	}		
  	if(trim($search_pos_no_min)){ 
		if($DPISDB=="odbc") $arr_search_condition[] = "(CLng(POS_NO) >= $search_pos_no_min)";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(to_number(replace(POS_NO,'-','')) >= $search_pos_no_min)";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(POS_NO >= $search_pos_no_min)";
	} // end if
  	if(trim($search_pos_no_max)){ 
		if($DPISDB=="odbc") $arr_search_condition[] = "(CLng(POS_NO) <= $search_pos_no_max)";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(to_number(replace(POS_NO,'-','')) <= $search_pos_no_max)";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(POS_NO <= $search_pos_no_max)";
	} // end if
	if(trim($search_pos_no_min) && trim($search_pos_no_max)){
		$print_search_condition[] = "เลขที่ตำแหน่ง : $search_pos_no_min ถึง $search_pos_no_max";
	}elseif(trim($search_pos_no_min)){
		$print_search_condition[] = "เลขที่ตำแหน่ง : มากกว่า $search_pos_no_min";
	}elseif(trim($search_pos_no_max)){
		$print_search_condition[] = "เลขที่ตำแหน่ง : น้อยกว่า $search_pos_no_max";
	} // end if
  	if(trim($search_pay_no_min)){ 
		if($DPISDB=="odbc") $arr_search_condition[] = "(CLng(PAY_NO) >= $search_pay_no_min)";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(to_number(replace(PAY_NO,'-','')) >= $search_pay_no_min)";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(PAY_NO+0 >= $search_pay_no_min)";
	} // end if
  	if(trim($search_pay_no_max)){ 
		if($DPISDB=="odbc") $arr_search_condition[] = "(CLng(PAY_NO) <= $search_pay_no_max)";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(to_number(replace(PAY_NO,'-','')) <= $search_pay_no_max)";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(PAY_NO+0 <= $search_pay_no_max)";
	} // end if
	if(trim($search_pl_code)){ 
		$arr_search_condition[] = "(trim(PL_CODE) = '". trim($search_pl_code) ."')";
		$print_search_condition[] = "$PL_TITLE : $search_pl_name";
	} // end if
	if(trim($search_pm_code)){ 
		$arr_search_condition[] = "(trim(PM_CODE) = '". trim($search_pm_code) ."')";
		$print_search_condition[] = "$PM_TITLE : $search_pm_name";
	} // end if
	if(trim($search_cl_name)){ 
		$arr_search_condition[] = "(trim(CL_NAME) = '". trim($search_cl_name) ."')";
		$print_search_condition[] = "$CL_TITLE : $search_cl_name";
	} // end if
	if(trim($search_level_no)){ 
		$arr_search_condition[] = "(trim(LEVEL_NO) = '". trim($search_level_no) ."')";
		$print_search_condition[] = "$LEVEL_TITLE : $search_level_no";
	} // end if
	if(trim($search_pt_code)){ 
		$arr_search_condition[] = "(trim(PT_CODE) = '". trim($search_pt_code) ."')";
		$print_search_condition[] = "$PT_TITLE : $search_pt_name";
	} // end if
	if(trim($search_skill_code)){ 
		$arr_search_condition[] = "(trim(SKILL_CODE) = '". trim($search_skill_code) ."')";
		$print_search_condition[] = "$SKILL_TITLE : $search_skill_name";
	} // end if
	if(trim($search_pc_code)){ 
		$arr_search_condition[] = "(trim(PC_CODE) = '". trim($search_pc_code) ."')";
		$print_search_condition[] = "$PC_TITLE : $search_pc_name";
	} // end if

	if(trim($search_org_id)){ 
		$arr_search_condition[] = "(a.ORG_ID = $search_org_id)";
		$print_search_condition[] = "$MINISTRY_TITLE : $search_ministry_name";
		$print_search_condition[] = "$DEPARTMENT_TITLE : $search_department_name";
		$print_search_condition[] = "$ORG_TITLE : $search_org_name";
	}elseif(trim($search_department_id)){
		$arr_search_condition[] = "(a.DEPARTMENT_ID = $search_department_id)";
		$print_search_condition[] = "$MINISTRY_TITLE : $search_ministry_name";
		$print_search_condition[] = "$DEPARTMENT_TITLE : $search_department_name";
	}elseif(trim($search_ministry_id)){
		$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$search_ministry_id ";
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_org[] = $data[ORG_ID];
		$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode(",", $arr_org) ."))";
		$print_search_condition[] = "$MINISTRY_TITLE : $search_ministry_name";
	} // end if
	
	if(trim($search_org_id_1)){ 
		$arr_search_condition[] = "(ORG_ID_1 = $search_org_id_1)";
		$print_search_condition[] = "$ORG_TITLE1 : $search_org_name_1";
	} // end if
	if(trim($search_org_id_2)){ 
		$arr_search_condition[] = "(ORG_ID_2 = $search_org_id_2)";
		$print_search_condition[] = "$ORG_TITLE2 : $search_org_name_2";
	} // end if
	if(trim($search_ct_code)){ 
		$arr_search_condition[] = "(trim(b.CT_CODE) = '". trim($search_ct_code) ."')";
		$print_search_condition[] = "ประเทศ : $search_ct_name";
	} // end if
	if(trim($search_pv_code)){ 
		$arr_search_condition[] = "(trim(b.PV_CODE) = '". trim($search_pv_code) ."')";
		$print_search_condition[] = "จังหวัด : $search_pv_name";
	} // end if
	if(trim($search_ap_code)){
		$arr_search_condition[] = "(trim(b.AP_CODE) = '". trim($search_ap_code) ."')";
		$print_search_condition[] = "อำเภอ : $search_ap_name";
	} // end if
	if(trim($search_ot_code)){ 
		$arr_search_condition[] = "(trim(b.OT_CODE) = '". trim($search_ot_code) ."')";
		$print_search_condition[] = "สังกัด : $search_ot_name";
	} // end if

	if(trim($search_pr_code)){ 
		$arr_search_condition[] = "(trim(a.PR_CODE) = '". trim($search_pr_code) ."')";
		$print_search_condition[] = "สงวนตำแหน่ง : $search_pr_name";
	} // end if

	if(trim($search_pos_date_min)){
		$search_pos_date_min =  save_date($search_pos_date_min);
		if($DPISDB=="odbc") $arr_search_condition[] = "(Mid(POS_DATE, 1, 10) >= '$search_pos_date_min')";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(POS_DATE, 1, 10) >= '$search_pos_date_min')";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(Mid(POS_DATE, 1, 10) >= '$search_pos_date_min')";
		$search_pos_date_min = show_date_format($search_pos_date_min, 1);
		$show_search_pos_date_min = show_date_format($search_pos_date_min, 2);
	} // end if
	if(trim($search_pos_date_max)){
		$search_pos_date_max =  save_date($search_pos_date_max);
		if($DPISDB=="odbc") $arr_search_condition[] = "(Mid(POS_DATE, 1, 10) <= '$search_pos_date_max')";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(POS_DATE, 1, 10) <= '$search_pos_date_max')";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(Mid(POS_DATE, 1, 10) <= '$search_pos_date_max')";
		$search_pos_date_max = show_date_format($search_pos_date_max, 1);
		$show_search_pos_date_max = show_date_format($search_pos_date_max, 2);
	} // end if
	if(trim($search_pos_date_min) && trim($search_pos_date_max)){
		$print_search_condition[] = "วันที่ ก.พ. กำหนดตำแหน่ง : $show_search_pos_date_min ถึง $show_search_pos_date_max";
	}elseif(trim($search_pos_date_min)){
		$print_search_condition[] = "วันที่ ก.พ. กำหนดตำแหน่ง : ตั้งแต่ $show_search_pos_date_min";
	}elseif(trim($search_pos_date_max)){
		$print_search_condition[] = "วันที่ ก.พ. กำหนดตำแหน่ง : ก่อน $show_search_pos_date_max";
	} // end if
	if(trim($search_pos_change_date_min)){
		$search_pos_change_date_min =  save_date($search_pos_change_date_min);
		if($DPISDB=="odbc") $arr_search_condition[] = "(Mid(POS_CHANGE_DATE, 1, 10) >= '$search_pos_change_date_min')";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(POS_CHANGE_DATE, 1, 10) >= '$search_pos_change_date_min')";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(Mid(POS_CHANGE_DATE, 1, 10) >= '$search_pos_change_date_min')";
		$search_pos_change_date_min = show_date_format($search_pos_change_date_min, 1);
		$show_search_pos_change_date_min = show_date_format($search_pos_change_date_min, 2);
	} // end if
	if(trim($search_pos_change_date_max)){
		$search_pos_change_date_max =  save_date($search_pos_change_date_max);
		if($DPISDB=="odbc") $arr_search_condition[] = "(Mid(POS_CHANGE_DATE, 1, 10) <= '$search_pos_change_date_max')";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(POS_CHANGE_DATE, 1, 10) <= '$search_pos_change_date_max')";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(Mid(POS_CHANGE_DATE, 1, 10) <= '$search_pos_change_date_max')";
		$search_pos_change_date_max = show_date_format($search_pos_change_date_max, 1);
		$show_search_pos_change_date_max = show_date_format($search_pos_change_date_max, 2);
	} // end if
	if(trim($search_pos_change_date_min) && trim($search_pos_change_date_max)){
		$print_search_condition[] = "วันที่ตำแหน่งเปลี่ยนสถานภาพ : $show_search_pos_change_date_min ถึง $show_search_pos_change_date_max";
	}elseif(trim($search_pos_change_date_min)){
		$print_search_condition[] = "วันที่ตำแหน่งเปลี่ยนสถานภาพ : ตั้งแต่ $show_search_pos_change_date_min";
	}elseif(trim($search_pos_change_date_max)){
		$print_search_condition[] = "วันที่ตำแหน่งเปลี่ยนสถานภาพ : ก่อน $show_search_pos_change_date_max";
	} // end if
	if(trim($search_pos_vacant_date_min)){
		$search_pos_vacant_date_min =  save_date($search_pos_vacant_date_min);
		if($DPISDB=="odbc") $arr_search_condition[] = "(Mid(POS_VACANT_DATE, 1, 10) >= '$search_pos_vacant_date_min')";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(POS_VACANT_DATE, 1, 10) >= '$search_pos_vacant_date_min')";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(Mid(POS_VACANT_DATE, 1, 10) >= '$search_pos_vacant_date_min')";
		$search_pos_vacant_date_min = show_date_format($search_pos_vacant_date_min, 1);
		$show_search_pos_vacant_date_min = show_date_format($search_pos_vacant_date_min, 2);
	} // end if
	if(trim($search_pos_vacant_date_max)){
		$search_pos_vacant_date_max =  save_date($search_pos_vacant_date_max);
		if($DPISDB=="odbc") $arr_search_condition[] = "(Mid(POS_VACANT_DATE, 1, 10) <= '$search_pos_vacant_date_max')";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(POS_VACANT_DATE, 1, 10) <= '$search_pos_vacant_date_max')";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(Mid(POS_VACANT_DATE, 1, 10) <= '$search_pos_vacant_date_max')";
		$search_pos_vacant_date_max = show_date_format($search_pos_vacant_date_max, 1);
		$show_search_pos_vacant_date_max = show_date_format($search_pos_vacant_date_max, 2);
	} // end if
	if(trim($search_pos_vacant_date_min) && trim($search_pos_vacant_date_max)){
		$print_search_condition[] = "วันที่ตำแหน่งว่าง : $show_search_pos_vacant_date_min ถึง $show_search_pos_vacant_date_max";
	}elseif(trim($search_pos_vacant_date_min)){
		$print_search_condition[] = "วันที่ตำแหน่งว่าง : ตั้งแต่ $show_search_pos_vacant_date_min";
	}elseif(trim($search_pos_vacant_date_max)){
		$print_search_condition[] = "วันที่ตำแหน่งว่าง : ก่อน $show_search_pos_vacant_date_max";
	} // end if
  	if(trim($search_pos_salary_min)) $arr_search_condition[] = "(POS_SALARY >= $search_pos_salary_min)";
  	if(trim($search_pos_salary_max)) $arr_search_condition[] = "(POS_SALARY <= $search_pos_salary_max)";
	if(trim($search_pos_salary_min) && trim($search_pos_salary_max)){
		$print_search_condition[] = "อัตราเงินเดือนถือจ่าย : $search_pos_salary_min ถึง $search_pos_salary_max";
	}elseif(trim($search_pos_salary_min)){
		$print_search_condition[] = "อัตราเงินเดือนถือจ่าย : มากกว่า $search_pos_salary_min";
	}elseif(trim($search_pos_salary_max)){
		$print_search_condition[] = "อัตราเงินเดือนถือจ่าย : น้อยกว่า $search_pos_salary_max";
	} // end if
	if(trim($search_pos_situation) == 1){ 
		$arr_search_condition[] = "(c.PER_STATUS IS NULL)";
		$print_search_condition[] = "สถานภาพของตำแหน่ง : ว่าง";
	} // end if
	if(trim($search_pos_situation) == 2){ 
		$arr_search_condition[] = "(c.PER_STATUS=1)";
		$print_search_condition[] = "สถานภาพของตำแหน่ง : มีคนครอง";
	} // end if
	if(trim($search_pay_situation) == 1){ 
		$arr_search_condition[] = "(e.PER_STATUS IS NULL)";
		$print_search_condition[] = "สถานภาพของถือจ่าย : ว่าง";
	} // end if
	if(trim($search_pay_situation) == 2){ 
		$arr_search_condition[] = "(e.PER_STATUS=1)";
		$print_search_condition[] = "สถานภาพของถือจ่าย : มีคนครอง";
	} // end if

	if(trim($search_pos_status) == 1) $arr_search_condition[] = "(a.POS_STATUS = 1)";
	if(trim($search_pos_status) == 2) $arr_search_condition[] = "(a.POS_STATUS = 2)";
	if($search_pos_status == 0){
		$print_search_condition[] = "สถานะ : ทั้งหมด";
	}elseif($search_pos_status == 1){
		$print_search_condition[] = "สถานะ : ใช้งาน";
	}elseif($search_pos_status == 2){
		$print_search_condition[] = "สถานะ : ยกเลิก";
	} // end if
  	if(trim($search_pos_reserve)) $arr_search_condition[] = "(POS_RESERVE IS NOT NULL or POS_RESERVE2 IS NOT NULL)";
  	if(trim($search_pos_reserve1)) $arr_search_condition[] = "(POS_RESERVE like '$search_pos_reserve1%')";
  	if(trim($search_pos_reserve2)) $arr_search_condition[] = "(POS_RESERVE2 like '$search_pos_reserve2%')";

	$check_condition = "";
	if ($search_data==1) 
		$check_condition = " and (a.LEVEL_NO is NULL) ";
	elseif ($search_data==2)
		$check_condition = " and (a.PL_CODE in (select d.PL_CODE from PER_LINE d where a.PL_CODE = d.PL_CODE and a.LEVEL_NO not between LEVEL_NO_MIN and LEVEL_NO_MAX)) ";

	$search_condition = "";
	if(count($arr_search_condition)) $search_condition = " where " . implode(" and ", $arr_search_condition);

	if($DPISDB=="odbc"){
		$cmd =" select 		a.POS_ID, a.DEPARTMENT_ID, a.POS_NO_NAME, IIf(IsNull(a.POS_NO), 0, CLng(a.POS_NO)) as POS_NO, PM_CODE, PL_CODE, CL_NAME, PT_CODE, 
											a.ORG_ID, b.ORG_NAME, b.ORG_ID_REF, b.PV_CODE, a.ORG_ID_1, a.ORG_ID_2, a.POS_STATUS, a.OT_CODE,
											c.PER_STATUS as PER_STATUS1, d.PER_STATUS as PER_STATUS2, e.PER_STATUS as PER_STATUS3, 
											f.PER_STATUS as PER_STATUS4, a.POS_CONDITION, a.POS_REMARK, a.LEVEL_NO, a.PAY_NO, a.POS_CHANGE_DATE, 
											a.PPT_CODE, a.POS_RETIRE, a.PR_CODE, a.POS_RESERVE, a.POS_RESERVE2, a.POS_RESERVE_DESC, 
											a.POS_RESERVE_DOCNO, a.POS_DOC_NO, a.POS_RETIRE_REMARK
						from (
									(
										(
											(
												PER_POSITION a
												inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
											) left join PER_PERSONAL c on (a.POS_ID=c.POS_ID and c.PER_TYPE=1 and c.PER_STATUS=1)
										) left join PER_PERSONAL d on (a.POS_ID=d.POS_ID and d.PER_TYPE=1 and (d.PER_STATUS=0 or d.PER_STATUS=2))
									) left join PER_PERSONAL e on (a.POS_ID=e.PAY_ID and e.PER_TYPE=1 and e.PER_STATUS=1)
								) left join PER_PERSONAL f on (a.POS_ID=f.PAY_ID and f.PER_TYPE=1 and (f.PER_STATUS=0 or f.PER_STATUS=2))
						$search_condition $check_condition
						group by a.POS_ID, a.DEPARTMENT_ID, a.POS_NO_NAME, IIf(IsNull(a.POS_NO), 0, CLng(a.POS_NO)), PM_CODE, PL_CODE, CL_NAME, PT_CODE, a.POS_CONDITION,
											a.ORG_ID, b.ORG_NAME, b.ORG_ID_REF, b.PV_CODE, a.ORG_ID_1, a.ORG_ID_2, a.POS_STATUS, a.OT_CODE, c.PER_STATUS, d.PER_STATUS, 
											e.PER_STATUS, f.PER_STATUS, a.POS_REMARK, a.LEVEL_NO, a.PAY_NO, a.POS_CHANGE_DATE, 
											a.PPT_CODE, a.POS_RETIRE, a.PR_CODE, a.POS_RESERVE, a.POS_RESERVE2, a.POS_RESERVE_DESC, a.POS_RESERVE_DOCNO, 
											a.POS_DOC_NO, a.POS_RETIRE_REMARK
						order by $order_str ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = "	select 	a.POS_ID, a.DEPARTMENT_ID, a.POS_NO_NAME, to_number(replace(POS_NO,'-','')) as POS_NO, PM_CODE, PL_CODE, CL_NAME, PT_CODE, 
											a.ORG_ID, b.ORG_NAME, b.ORG_ID_REF, b.PV_CODE, a.ORG_ID_1, a.ORG_ID_2, a.POS_STATUS, a.OT_CODE, 
											c.PER_STATUS as PER_STATUS1, d.PER_STATUS as PER_STATUS2, e.PER_STATUS as PER_STATUS3, 
											f.PER_STATUS as PER_STATUS4, a.POS_CONDITION, a.POS_REMARK, a.LEVEL_NO, a.PAY_NO, a.POS_CHANGE_DATE, 
											a.PPT_CODE, a.POS_RETIRE, a.PR_CODE, a.POS_RESERVE, a.POS_RESERVE2, a.POS_RESERVE_DESC, 
											a.POS_RESERVE_DOCNO, a.POS_DOC_NO, a.POS_RETIRE_REMARK
							from 		PER_POSITION a, PER_ORG b, 
											(select POS_ID, PER_ID, PER_NAME, PER_STATUS from PER_PERSONAL where PER_TYPE=1 and PER_STATUS=1) c, 
											(select POS_ID, PER_ID, PER_NAME, PER_STATUS from PER_PERSONAL where PER_TYPE=1 and (PER_STATUS=0 or PER_STATUS=2)) d,
											(select PAY_ID, PER_ID, PER_NAME, PER_STATUS from PER_PERSONAL where PER_TYPE=1 and PER_STATUS=1) e, 
											(select PAY_ID, PER_ID, PER_NAME, PER_STATUS from PER_PERSONAL where PER_TYPE=1 and (PER_STATUS=0 or PER_STATUS=2)) f
							where 	a.ORG_ID=b.ORG_ID and a.POS_ID=c.POS_ID(+) and a.POS_ID=d.POS_ID(+) and a.POS_ID=e.PAY_ID(+) and a.POS_ID=f.PAY_ID(+)
											$search_condition $check_condition
							group by a.POS_ID, a.DEPARTMENT_ID, a.POS_NO_NAME, to_number(replace(POS_NO,'-','')), PM_CODE, PL_CODE, CL_NAME, PT_CODE, a.POS_CONDITION,
											 a.ORG_ID, b.ORG_NAME, b.ORG_ID_REF, b.PV_CODE, a.ORG_ID_1, a.ORG_ID_2, a.POS_STATUS, a.OT_CODE, c.PER_STATUS, d.PER_STATUS, 
											e.PER_STATUS, f.PER_STATUS, a.POS_REMARK, a.LEVEL_NO, a.PAY_NO, a.POS_CHANGE_DATE, 
											a.PPT_CODE, a.POS_RETIRE, a.PR_CODE, a.POS_RESERVE, a.POS_RESERVE2, a.POS_RESERVE_DESC, a.POS_RESERVE_DOCNO, 
											a.POS_DOC_NO, a.POS_RETIRE_REMARK
							order by $order_str ";
	}elseif($DPISDB=="mysql"){
		$cmd =" select 		a.POS_ID, a.DEPARTMENT_ID, a.POS_NO_NAME, a.POS_NO as POS_NO, PM_CODE, PL_CODE, CL_NAME, PT_CODE, 
											a.ORG_ID, b.ORG_NAME, b.ORG_ID_REF, b.PV_CODE, a.ORG_ID_1, a.ORG_ID_2, a.POS_STATUS, a.OT_CODE, 
											c.PER_STATUS as PER_STATUS1, d.PER_STATUS as PER_STATUS2, e.PER_STATUS as PER_STATUS3, 
											f.PER_STATUS as PER_STATUS4, a.POS_CONDITION, a.POS_REMARK, a.LEVEL_NO, a.PAY_NO, a.POS_CHANGE_DATE, 
											a.PPT_CODE, a.POS_RETIRE, a.PR_CODE, a.POS_RESERVE, a.POS_RESERVE2, a.POS_RESERVE_DESC, 
											a.POS_RESERVE_DOCNO, a.POS_DOC_NO, a.POS_RETIRE_REMARK
						from (
									(
										(
											(
												PER_POSITION a
												inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
											) left join PER_PERSONAL c on (a.POS_ID=c.POS_ID and c.PER_TYPE=1 and c.PER_STATUS=1)
										) left join PER_PERSONAL d on (a.POS_ID=d.POS_ID and d.PER_TYPE=1 and (d.PER_STATUS=0 or d.PER_STATUS=2))
									) left join PER_PERSONAL e on (a.POS_ID=e.PAY_ID and e.PER_TYPE=1 and e.PER_STATUS=1)
								) left join PER_PERSONAL f on (a.POS_ID=f.PAY_ID  and f.PER_TYPE=1and (f.PER_STATUS=0 or f.PER_STATUS=2))
						$search_condition $check_condition
						group by a.POS_ID, a.DEPARTMENT_ID, a.POS_NO_NAME, a.POS_NO, PM_CODE, PL_CODE, CL_NAME, PT_CODE, a.POS_CONDITION,
											a.ORG_ID, b.ORG_NAME, b.ORG_ID_REF, b.PV_CODE, a.ORG_ID_1, a.ORG_ID_2, a.POS_STATUS, a.OT_CODE, c.PER_STATUS, d.PER_STATUS, 
											e.PER_STATUS, f.PER_STATUS, a.POS_REMARK, a.LEVEL_NO, a.PAY_NO, a.POS_CHANGE_DATE, 
											a.PPT_CODE, a.POS_RETIRE, a.PR_CODE, a.POS_RESERVE, a.POS_RESERVE2, a.POS_RESERVE_DESC, 
											a.POS_RESERVE_DOCNO, a.POS_DOC_NO, a.POS_RETIRE_REMARK
						order by $order_str ";
	} // end if

	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
//	echo "$cmd<br>";

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
		$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 14, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 15, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 16, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 17, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 18, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 19, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 20, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 21, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 22, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 23, "", set_format("xlsFmtTitle", "B", "C", "", 1));

		foreach($print_search_condition as $show_condition){
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "$show_condition", set_format("xlsFmtTitle", "B", "L", "", 0));
		} // end foreach

		$xlsRow++;
		print_header($xlsRow);
		$data_count = $xlsRow;
		
		while($data = $db_dpis->get_array()){
			$data_count++;

			$POS_ID = trim($data[POS_ID]);
			$POS_NO_NAME = trim($data[POS_NO_NAME]);
			if (substr($POS_NO_NAME,0,4)=="กปด.")
				$POS_NO = $POS_NO_NAME." ".trim($data[POS_NO]);
			else
				$POS_NO = $POS_NO_NAME.trim($data[POS_NO]);
			$PAY_NO = trim($data[PAY_NO]);
			$ORG_ID = trim($data[ORG_ID]);
			$ORG_NAME = trim($data[ORG_NAME]);
			$ORG_ID_REF = trim($data[ORG_ID_REF]);
			$PM_CODE = trim($data[PM_CODE]);
			$PL_CODE = trim($data[PL_CODE]);
			$PT_CODE = trim($data[PT_CODE]);
			$CL_NAME = trim($data[CL_NAME]);
			$ORG_ID_1 = trim($data[ORG_ID_1]);
			$ORG_ID_2 = trim($data[ORG_ID_2]);
			$POS_CONDITION = trim($data[POS_CONDITION]);
			$POS_CHANGE_DATE = show_date_format($data[POS_CHANGE_DATE], 1);
			$POS_REMARK = trim($data[POS_REMARK]);
			$LEVEL_NO = trim($data[LEVEL_NO]);
			$OT_CODE = trim($data[OT_CODE]);
			$PV_CODE = trim($data[PV_CODE]);
			$PPT_CODE = trim($data[PPT_CODE]);
			$POS_RETIRE = trim($data[POS_RETIRE]);
			$POS_RETIRE_REMARK = trim($data[POS_RETIRE_REMARK]);
			$PR_CODE = trim($data[PR_CODE]);
			$POS_RESERVE = trim($data[POS_RESERVE]);
			$POS_RESERVE2 = trim($data[POS_RESERVE2]);
			$POS_RESERVE_DESC = trim($data[POS_RESERVE_DESC]);
			$POS_RESERVE_DOCNO = trim($data[POS_RESERVE_DOCNO]);
			$POS_DOC_NO = trim($data[POS_DOC_NO]);
	
			$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$PM_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$PM_NAME = $data_dpis2[PM_NAME];
	
			$cmd = " select PL_NAME from PER_LINE where trim(PL_CODE)='$PL_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$PL_NAME = $data_dpis2[PL_NAME];
	
			$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE)='$PT_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$PT_NAME = $data_dpis2[PT_NAME];
	
			$cmd = " select OT_NAME from PER_ORG_TYPE where trim(OT_CODE)='$OT_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$OT_NAME = $data_dpis2[OT_NAME];
	
			$cmd = " select PV_NAME from PER_PROVINCE where trim(PV_CODE)='$PV_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$PV_NAME = $data_dpis2[PV_NAME];
	
			$cmd = " select PPT_NAME from PER_PRACTICE where trim(PPT_CODE)='$PPT_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$PPT_NAME = $data_dpis2[PPT_NAME];
	
			$cmd = " select ORG_NAME from PER_ORG where OL_CODE='02' and ORG_ID=$ORG_ID_REF ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$ORG_REF_NAME = $data_dpis2[ORG_NAME];
	
			$cmd = " select ORG_NAME from PER_ORG where OL_CODE='04' and ORG_ID=$ORG_ID_1 ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$ORG_NAME_1 = $data_dpis2[ORG_NAME];
	
			$cmd = " select ORG_NAME from PER_ORG where OL_CODE='05' and ORG_ID=$ORG_ID_2 ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$ORG_NAME_2 = $data_dpis2[ORG_NAME];
			
			if (!$LEVEL_NO) {
				$cmd = " select LEVEL_NO_MIN from PER_CO_LEVEL where trim(CL_NAME)='$CL_NAME' ";
				$db_dpis2->send_cmd($cmd);
				$data_dpis2 = $db_dpis2->get_array();
				$LEVEL_NO = $data_dpis2[LEVEL_NO_MIN];
			}
		
			$cmd = "select LEVEL_NAME, POSITION_TYPE, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$LEVEL_NO'";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$LEVEL_NAME = $data_dpis2[LEVEL_NAME];
			$NEW_POSITION_TYPE = $data_dpis2[POSITION_TYPE];
			$POSITION_LEVEL = $data_dpis2[POSITION_LEVEL];

			$arr_temp = explode(" ", $LEVEL_NAME);
			$LEVEL_NAME =  $arr_temp[1];
		
			$cmd = " select PR_NAME from PER_POS_RESERVE where trim(PR_CODE)='$PR_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$PR_NAME = $data_dpis2[PR_NAME];
	
			if($PAY_NO && ($SESS_DEPARTMENT_NAME=="กรมการปกครอง" || $SESS_DEPARTMENT_NAME=="กรมชลประทาน")){
				if($DPISDB=="odbc"){
					$cmd = "	select		b.PER_ID, b.PER_NAME, b.PER_SURNAME, c.PN_NAME
										from		
													(
														PER_POSITION a
														left join PER_PERSONAL b on a.POS_ID=b.PAY_ID
													) left join PER_PRENAME c on b.PN_CODE=c.PN_CODE
										where		a.PAY_NO=$PAY_NO and PER_TYPE = 1 and PER_STATUS = 1 ";
				}elseif($DPISDB=="oci8"){
					$cmd = "	select		b.PER_ID, b.PER_NAME, b.PER_SURNAME, c.PN_NAME
										from		PER_POSITION a, PER_PERSONAL b, PER_PRENAME c
										where		a.POS_ID=b.PAY_ID(+) and a.PAY_NO=$PAY_NO and b.PN_CODE=c.PN_CODE(+) and PER_TYPE = 1 and PER_STATUS = 1 ";
				}elseif($DPISDB=="mysql"){
					$cmd = "	select		b.PER_ID, b.PER_NAME, b.PER_SURNAME, c.PN_NAME
										from		
													(
														PER_POSITION a
														left join PER_PERSONAL b on a.POS_ID=b.PAY_ID
													) left join PER_PRENAME c on b.PN_CODE=c.PN_CODE
										where		a.PAY_NO=$PAY_NO and PER_TYPE = 1 and PER_STATUS = 1 ";
				} // end if
				$db_dpis2->send_cmd($cmd);
//				echo "$cmd<br>";
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PER_ID_PAY = trim($data2[PER_ID]);
				$PAYNAME = (trim($data2[PN_NAME])?($data2[PN_NAME]):"") . $data2[PER_NAME] ." ". $data2[PER_SURNAME];
			} // end if

			$cnt = 0;
			$PER_ID = $FULLNAME = $PER_CARDNO = "";
			$cmd = "	select PER_ID, PER_STATUS from	PER_PERSONAL where POS_ID=$POS_ID and PER_TYPE = 1 ";
			$db_dpis1->send_cmd($cmd);
//			$db_dpis1->show_error();
			while($data1 = $db_dpis1->get_array()){
				$cnt++;
				$PER_ID[$cnt] = trim($data1[PER_ID]);
				$PER_STATUS = $data1[PER_STATUS];

				if($PER_ID[$cnt] && $PER_STATUS != 2){
					$cmd = " select 	b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, a.PER_STATUS, a.PER_CARDNO
									 from 		PER_PERSONAL a, PER_PRENAME b 
									 where 	a.PN_CODE=b.PN_CODE and PER_ID=$PER_ID[$cnt] ";
					$db_dpis2->send_cmd($cmd);
					$data_dpis2 = $db_dpis2->get_array();
					$FULLNAME[$cnt] = (trim($data_dpis2[PN_NAME])?($data_dpis2[PN_NAME]):"") . $data_dpis2[PER_NAME] ." ". $data_dpis2[PER_SURNAME];
					$FULLNAME[$cnt] .= (($data_dpis2[PER_STATUS]==0)?" (รอบรรจุ)":"");
					$LEVEL_NO = trim($data_dpis2[LEVEL_NO]);
					$PER_CARDNO[$cnt] = trim($data_dpis2[PER_CARDNO]);
				} // end if
			} // end while

			$SEQ_NO++;
			$xlsRow = $data_count;
			$worksheet->write($xlsRow, 0, (($NUMBER_DISPLAY==2)?convert2thaidigit("$SEQ_NO"):"$SEQ_NO"), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 1, (($NUMBER_DISPLAY==2)?convert2thaidigit("$POS_NO"):"$POS_NO"), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 2, "$OT_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 3, "$PV_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 4, "$PPT_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 5, "$POS_RETIRE", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 6, "$POS_RETIRE_REMARK", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 7, "$POS_DOC_NO", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 8, (($NUMBER_DISPLAY==2)?convert2thaidigit("$POS_CHANGE_DATE"):"$POS_CHANGE_DATE"), set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 9, (($NUMBER_DISPLAY==2)?convert2thaidigit("$PER_CARDNO[1]"):"$PER_CARDNO[1]"), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 10, "$FULLNAME[1]", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 11, "$PM_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 12, "$PL_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 13, "$NEW_POSITION_TYPE", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 14, "$LEVEL_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 15, "$CL_NAME", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 16, "$ORG_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 17, "$ORG_NAME_1", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 18, "$ORG_NAME_2", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 19, "$PR_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 20, "$POS_RESERVE", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 21, "$POS_RESERVE2", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 22, "$POS_RESERVE_DESC", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 23, "$POS_RESERVE_DOCNO", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));

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
		$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 14, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 15, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 16, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 17, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 18, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 19, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 20, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 21, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 22, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 23, "", set_format("xlsFmtTitle", "B", "C", "", 1));
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