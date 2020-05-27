<?
	ini_set('memory_limit', '128M');
	
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	include("../php_scripts/load_per_control.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
		
	if(!trim($RPTORD_LIST)){ 
		if($list_type=="PER_ORG" && trim($search_org_id_1)=="" && trim($search_org_id_2)=="") $RPTORD_LIST = "ORG_1";
		elseif($list_type=="PER_ORG" && trim($search_org_id_1)!="" && trim($search_org_id_2)=="") $RPTORD_LIST = "ORG_2";
		elseif($list_type=="PER_ORG" && trim($search_org_id_1)!="" && trim($search_org_id_2)!="") $RPTORD_LIST = "LINE";
		elseif($list_type=="PER_COUNTRY" && trim($search_pv_code)!="") $RPTORD_LIST = "PROVINCE";
		else $RPTORD_LIST = "ORG";
	} // end if
	$arr_rpt_order = explode("|", $RPTORD_LIST);
//	$arr_rpt_order = array("POSNO"); 

	$select_list = "";
	$order_by = "";
	$heading_name = "";
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
		$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
		switch($REPORT_ORDER){
			case "POSNO" :
				if($select_list) $select_list .= ", ";

				if($order_by) $order_by .= ", ";
				if($DPISDB=="odbc") {
					$select_list .= "IIf(IsNull(a.POS_NO), 0 , CLng(a.POS_NO)) as POS_NO";				
					$order_by .= "IIf(IsNull(a.POS_NO), 0 , CLng(a.POS_NO))";
				}
				if($DPISDB=="oci8") {
					$select_list .= "a.POS_NO";
					$order_by .= "TO_NUMBER(a.POS_NO)";
				}elseif($DPISDB=="mysql"){
					$select_list .= "a.POS_NO";				
					$order_by .= "a.POS_NO";
				}

				$heading_name .= " เลขที่ตำแหน่ง";
				break;
		} // end switch case
	} // end for
	if(!trim($order_by)) $order_by = "a.POS_NO";
	if(!trim($select_list)) $select_list = "a.POS_NO";

	$search_condition = "";
	$arr_search_condition[] = "(a.POS_STATUS=1)";

	$list_type_text = "ทั้งส่วนราชการ";

	// ทั้งส่วนราชการ
	if($DEPARTMENT_ID){
		$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
	}elseif($MINISTRY_ID){
		$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

		$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		$list_type_text .= " - $MINISTRY_NAME";
	}elseif($PROVINCE_CODE){
		$PROVINCE_CODE = trim($PROVINCE_CODE);
		$arr_search_condition[] = "(trim(b.PV_CODE) = '$PROVINCE_CODE')";
		$list_type_text .= " - $PROVINCE_NAME";
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);
	
	$todate = date("d/m/Y");
	$arr_temp = explode("/", $todate);
	$show_date = ($arr_temp[0] + 0) ." ". $month_full[($arr_temp[1] + 0)][TH] ." ". $arr_temp[2];
	
	$company_name = "แบบ คปร.7 - พลเรือน ส่วนราชการ : $list_type_text";
	$report_title = "ข้อมูลสถานภาพของตำแหน่ง และผู้ถือครองตำแหน่งข้าราชการพลเรือน ณ วันที่ $show_date";
	$report_code = "คปร.7";

	$token = md5(uniqid(rand(), true)); 
//	$fname= "../../Excel/tmp/dpis_$token.xls";

//	$workbook = new writeexcel_workbook($fname);
/*
	$worksheet = &$workbook->addworksheet("$report_code");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//
*/
	function print_header(){
		global $worksheet, $xlsRow;
		global $heading_name;
		
		$worksheet->set_column(0, 0, 5);
		$worksheet->set_column(1, 1, 6);
		$worksheet->set_column(2, 2, 37);
		$worksheet->set_column(3, 3, 18);
		$worksheet->set_column(4, 4, 25);
		$worksheet->set_column(5, 5, 42);
		$worksheet->set_column(6, 6, 25);
		$worksheet->set_column(7, 7, 10);
		$worksheet->set_column(8, 8, 10);
		$worksheet->set_column(9, 9, 16);
		$worksheet->set_column(10, 10, 20);
		$worksheet->set_column(11, 11, 17);
		$worksheet->set_column(12, 12, 5);
		$worksheet->set_column(13, 13, 4);
		$worksheet->set_column(14, 14, 4);
		$worksheet->set_column(15, 15, 4);
		$worksheet->set_column(16, 16, 4);
		$worksheet->set_column(17, 17, 4);
		$worksheet->set_column(18, 18, 4);
		$worksheet->set_column(19, 19, 7);
		$worksheet->set_column(20, 20, 30);
		$worksheet->set_column(21, 21, 11);
		$worksheet->set_column(22, 22, 11);
		$worksheet->set_column(23, 23, 20);
		$worksheet->set_column(24, 24, 28);
		$worksheet->set_column(25, 25, 25);

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "ข้อมูลตำแหน่งข้าราชการ / หน่วยงานที่สังกัด", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 10, "ข้อมูลข้าราชการผู้ครองตำแหน่ง / หน่วยงาน", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 14, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 15, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 16, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 17, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 18, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 19, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 20, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 21, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 22, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 23, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 24, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 25, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "(1)", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 1, "(2)", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 2, "(3)", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));
		$worksheet->write($xlsRow, 4, "(4)", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 5, "(5)", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 6, "(6)", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 7, "(7)", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 8, "(8)", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 9, "(9)", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 10, "(10)", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 11, "(11)", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 12, "(12)", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 13, "(13)", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));
		$worksheet->write($xlsRow, 14, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));
		$worksheet->write($xlsRow, 15, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));
		$worksheet->write($xlsRow, 16, "(14)", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));
		$worksheet->write($xlsRow, 17, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));
		$worksheet->write($xlsRow, 18, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));
		$worksheet->write($xlsRow, 19, "(15)", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 20, "(16)", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 21, "(17)", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 22, "(18)", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 23, "(19)", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 24, "(20)", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 25, "(21)", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableHeader", "B", "C", "L", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableHeader", "B", "C", "", 1));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 11, "เลข", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTableHeader", "B", "C", "L", 1));
		$worksheet->write($xlsRow, 14, "", set_format("xlsFmtTableHeader", "B", "C", "", 1));
		$worksheet->write($xlsRow, 15, "", set_format("xlsFmtTableHeader", "B", "C", "R", 1));
		$worksheet->write($xlsRow, 16, "", set_format("xlsFmtTableHeader", "B", "C", "L", 1));
		$worksheet->write($xlsRow, 17, "", set_format("xlsFmtTableHeader", "B", "C", "", 1));
		$worksheet->write($xlsRow, 18, "", set_format("xlsFmtTableHeader", "B", "C", "R", 1));
		$worksheet->write($xlsRow, 19, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 20, "ระดับ", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));		
		$worksheet->write($xlsRow, 21, "อัตรา", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));		
		$worksheet->write($xlsRow, 22, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 23, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 24, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 25, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "ลำดับ", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 1, "ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 2, "ชื่อส่วนราชการ", set_format("xlsFmtTableHeader", "B", "C", "LRB", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 1));
		$worksheet->write($xlsRow, 4, "ชื่อตำแหน่งในการบริหาร", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 5, "ชื่อตำแหน่งในสายงาน", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 6, "ระดับ", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 7, "ประเภท", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 8, "ส่วนกลาง", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 9, "พื้นที่", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 10, "ชื่อ - สกุล", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 11, "ประจำตัว", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 12, "เพศ", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 13, "เกิด", set_format("xlsFmtTableHeader", "B", "C", "LRB", 1));
		$worksheet->write($xlsRow, 14, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 1));
		$worksheet->write($xlsRow, 15, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 1));
		$worksheet->write($xlsRow, 16, "บรรจุ", set_format("xlsFmtTableHeader", "B", "C", "LRB", 1));
		$worksheet->write($xlsRow, 17, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 1));
		$worksheet->write($xlsRow, 18, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 1));
		$worksheet->write($xlsRow, 19, "ปีที่", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 20, "ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 21, "เงินเดือน", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 22, "เงินประจำ", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 23, "ระดับ", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 24, "สาขา", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 25, "หมายเหตุ", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "ที่", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 1, "เลขที่", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 2, "สำนัก/กอง", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 3, "เขต/แขวง/ศูนย์", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 6, "ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 7, "ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 8, "/ภูมิภาค", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 9, "ปฏิบัติงาน", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 11, "ประชาชน", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 13, "ว", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 14, "ด", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 15, "ป", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 16, "ว", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 17, "ด", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 18, "ป", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 19, "เกษียณ", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 20, "ปัจจุบัน", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 21, "ปัจจุบัน", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 22, "ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 23, "การศึกษา", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 24, "วิชา", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 25, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
	} // function		

	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order;
		global $POS_NO;
				
		for($rpt_order_index=0; $rpt_order_index <= $current_index; $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "POSNO" :	
					if($POS_NO) $arr_addition_condition[] = "(trim(a.POS_NO) = '$POS_NO')";
					else $arr_addition_condition[] = "(trim(a.POS_NO) = '$POS_NO' or a.POS_NO is null)";
				break;
			} // end switch case
		} // end for
		
		$addition_condition = "";
		if(count($arr_addition_condition)) $addition_condition = implode(" and ", $arr_addition_condition);

		return $addition_condition;
	} // function
	
	function initialize_parameter($current_index){
		global $arr_rpt_order;
		global $POS_NO;
		for($rpt_order_index=$current_index; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "POSNO" :	
					$POS_NO = -1;
				break;
			} // end switch case
		} // end for
	} // function

	if($DPISDB=="odbc"){
		$cmd = " select			distinct 
											a.DEPARTMENT_ID, $select_list, a.POS_ID, b.ORG_NAME, c.PL_NAME, d.PM_NAME, e.PT_NAME, a.CL_NAME, f.PV_NAME, g.OT_NAME
						 from			(
												(
													(
														(
															(
																PER_POSITION a
																inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
															) inner join PER_LINE c on (a.PL_CODE=c.PL_CODE)
														) left join PER_MGT d on (a.PM_CODE=d.PM_CODE)
													) inner join PER_TYPE e on (a.PT_CODE=e.PT_CODE)
												) left join PER_PROVINCE f on (b.PV_CODE=f.PV_CODE)
											) left join PER_ORG_TYPE g on (b.OT_CODE=g.OT_CODE)
						 $search_condition
						 order by		a.DEPARTMENT_ID, $order_by ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);	
		$cmd = " select			distinct 
											a.DEPARTMENT_ID, $select_list, a.POS_ID, b.ORG_NAME, c.PL_NAME, d.PM_NAME, e.PT_NAME, a.CL_NAME, f.PV_NAME, g.OT_NAME
						 from			PER_POSITION a, PER_ORG b, PER_LINE c, PER_MGT d, PER_TYPE e, PER_PROVINCE f, PER_ORG_TYPE g
						 where		a.ORG_ID=b.ORG_ID and a.PL_CODE=c.PL_CODE and a.PM_CODE=d.PM_CODE(+) and a.PT_CODE=e.PT_CODE
						 					and b.PV_CODE=f.PV_CODE(+) and b.OT_CODE=g.OT_CODE(+)
											$search_condition
						 order by		a.DEPARTMENT_ID, $order_by ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			distinct 
											a.DEPARTMENT_ID, $select_list, a.POS_ID, b.ORG_NAME, c.PL_NAME, d.PM_NAME, e.PT_NAME, a.CL_NAME, f.PV_NAME, g.OT_NAME
						 from			(
												(
													(
														(
															(
																PER_POSITION a
																inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
															) inner join PER_LINE c on (a.PL_CODE=c.PL_CODE)
														) left join PER_MGT d on (a.PM_CODE=d.PM_CODE)
													) inner join PER_TYPE e on (a.PT_CODE=e.PT_CODE)
												) left join PER_PROVINCE f on (b.PV_CODE=f.PV_CODE)
											) left join PER_ORG_TYPE g on (b.OT_CODE=g.OT_CODE)
						 $search_condition
						 order by		a.DEPARTMENT_ID, $order_by ";
	} // end if
	if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	$count_data = $db_dpis->send_cmd($cmd);
//	echo "$cmd<br>";
//	$db_dpis->show_error();
	$data_count = $data_row = 0;
	initialize_parameter(0);
	$ORG_ID_REF = -1;
	while($data = $db_dpis->get_array()){
		if($ORG_ID_REF != $data[ORG_ID_REF]){
			$ORG_ID_REF = $data[ORG_ID_REF];
			
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_REF ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_NAME_REF = $data2[ORG_NAME];

			$arr_content[$data_count][type] = "ORG_REF";
			$arr_content[$data_count][org_id_ref] = $ORG_ID_REF;
			$arr_content[$data_count][org_name_ref] = $ORG_NAME_REF;
			
			$data_count++;
		} // end if
		
		for($rpt_order_index=0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "POSNO" :
					if($POS_NO != trim($data[POS_NO])){
						$POS_NO = trim($data[POS_NO]);

						$addition_condition = generate_condition($rpt_order_index);

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);

						if($rpt_order_index == (count($arr_rpt_order) - 1)){	
							$data_row++;
							$POS_ID = $data[POS_ID];
							$ORG_NAME = trim($data[ORG_NAME]);
							$ORG_NAME2 = "";
							$PM_NAME = trim($data[PM_NAME]);
							$PL_NAME = trim($data[PL_NAME]);
							$CL_NAME = trim($data[CL_NAME]);
							$PT_NAME = trim($data[PT_NAME]);
							$OT_NAME = trim($data[OT_NAME]);
							$PV_NAME = trim($data[PV_NAME]);
							
							if ($DEPARTMENT_NAME=="กรมการปกครอง") 
								$cmd = " select 	PER_ID, PER_GENDER, PER_BIRTHDATE, PER_STARTDATE, LEVEL_NO, 
												PER_SALARY,PER_MGTSALARY, PER_NAME , PER_SURNAME,PER_CARDNO
												 from		PER_PERSONAL
												 where	PAY_ID=$POS_ID and PER_STATUS=1 ";
							else
								$cmd = " select 	PER_ID, PER_GENDER, PER_BIRTHDATE, PER_STARTDATE, LEVEL_NO, 
												PER_SALARY,PER_MGTSALARY, PER_NAME , PER_SURNAME,PER_CARDNO
												 from		PER_PERSONAL
												 where	POS_ID=$POS_ID and PER_STATUS=1 ";
							$db_dpis2->send_cmd($cmd);
							$data2 = $db_dpis2->get_array();
							$PER_ID = $data2[PER_ID];
							$PER_GENDER = $data2[PER_GENDER];
							$PER_NAME = $data2[PER_NAME];
							$PER_SURNAME = $data2[PER_SURNAME];
							$PER_CARDNO = $data2[PER_CARDNO];
							$PER_BIRTHDATE = substr(trim($data2[PER_BIRTHDATE]), 0, 10);
							$BIRTHDATE_D = $BIRTHDATE_M = $BIRTHDATE_Y = $RETIREDATE_Y = "";
							if($PER_BIRTHDATE){
								$arr_temp = explode("-", $PER_BIRTHDATE);
								$BIRTHDATE_D = $arr_temp[2];
								$BIRTHDATE_M = $arr_temp[1];
//								$BIRTHDATE_Y = substr(($arr_temp[0] + 543), -2);
								$BIRTHDATE_Y = $arr_temp[0] + 543;
								$RETIREDATE_Y = ("$BIRTHDATE_M-$BIRTHDATE_D" >= "10-01")?($arr_temp[0] + 543 + 61):($arr_temp[0] + 543 + 60);
							} // end if
							$PER_STARTDATE = substr(trim($data2[PER_STARTDATE]), 0, 10);
							$STARTDATE_D = $STARTDATE_M = $STARTDATE_Y = "";
							if($PER_STARTDATE){
								$arr_temp = explode("-", $PER_STARTDATE);
								$STARTDATE_D = $arr_temp[2];
								$STARTDATE_M = $arr_temp[1];
//								$STARTDATE_Y = substr(($arr_temp[0] + 543), -2);
								$STARTDATE_Y = $arr_temp[0] + 543;
							} // end if
							$LEVEL_NO = trim($data2[LEVEL_NO]);
							$PER_SALARY = $data2[PER_SALARY];
							$PER_MGTSALARY = $data2[PER_MGTSALARY];
							
							$cmd="select LEVEL_NAME from PER_LEVEL where LEVEL_NO='$LEVEL_NO'";
							$db_dpis2->send_cmd($cmd);
							$data_level=$db_dpis2->get_array();
							
							$LEVEL_NAME=$data_level[LEVEL_NAME];
							
							
							//============ ระดับตำแหน่ง ===========	
			
						$arr_temp = explode(" ", $LEVEL_NAME);
					//หาชื่อระดับตำแหน่ง  
					$LEVEL_NAME =  $arr_temp[1];
						//หาชื่อตำแหน่งประเภท
						$NEW_POSITION_TYPE = str_replace("ประเภท", "", $arr_temp[0]);
						//unset($LEVEL_NAME);
							
							$EDU_TYPE="";		$EL_NAME = "";			$EM_NAME = "";		$EN_SHORTNAME="";		$EN_NAME="";
							//หาข้อมูลการศึกษาเลือก วุฒิสูงสุด ถ้าไม่มีเอาวุฒิในตน.ปัจจุบันมา
							if($PER_ID){
								if($DPISDB=="odbc"){
									$cmd = " select 	a.EDU_TYPE,c.EL_NAME,d.EM_NAME,b.EN_SHORTNAME,b.EN_NAME
													from ( 	
																(
																	PER_EDUCATE a
																	left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
																) left join PER_EDUCLEVEL c on (b.EL_CODE=c.EL_CODE)
															) left join PER_EDUCMAJOR d on (a.EM_CODE=d.EM_CODE)
												  where a.PER_ID=$PER_ID and a.EDU_TYPE like '%||4||%' ";									
								}elseif($DPISDB=="oci8"){
									$cmd = " select	a.EDU_TYPE,c.EL_NAME ,d.EM_NAME,b.EN_SHORTNAME,b.EN_NAME
													 from PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCLEVEL c,PER_EDUCMAJOR d
												   where a.PER_ID=$PER_ID and  a.EDU_TYPE like '%||4||%'  
															and a.EN_CODE=b.EN_CODE(+) and b.EL_CODE=c.EL_CODE(+) 
															and a.EM_CODE=d.EM_CODE(+) ";				
								}elseif($DPISDB=="mysql"){
									$cmd = " select 	a.EDU_TYPE,c.EL_NAME ,d.EM_NAME,b.EN_SHORTNAME,b.EN_NAME
													from ( 	
																(
																	PER_EDUCATE a
																	left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
																) left join PER_EDUCLEVEL c on (b.EL_CODE=c.EL_CODE)
															) left join PER_EDUCMAJOR d on (a.EM_CODE=d.EM_CODE)
													where a.PER_ID=$PER_ID and  a.EDU_TYPE like '%||4||%' ";	
								} // end if
								$ed = $db_dpis2->send_cmd($cmd);

								if ($ed) { 
									$data2 = $db_dpis2->get_array();
								
									$EDU_TYPE = trim($data2[EDU_TYPE]);
									$EL_NAME = trim($data2[EL_NAME]);
									$EM_NAME = trim($data2[EM_NAME]);		//สาขา
									$EN_SHORTNAME =  trim($data2[EN_SHORTNAME]);
									$EN_NAME =  trim($data2[EN_NAME]);		//ชื่อวุฒิ
								} 	else {
									if($DPISDB=="odbc"){
										$cmd = " select 	a.EDU_TYPE,c.EL_NAME,d.EM_NAME,b.EN_SHORTNAME,b.EN_NAME
														from ( 	
																	(
																		PER_EDUCATE a
																		left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
																	) left join PER_EDUCLEVEL c on (b.EL_CODE=c.EL_CODE)
																) left join PER_EDUCMAJOR d on (a.EM_CODE=d.EM_CODE)
													  where a.PER_ID=$PER_ID and a.EDU_TYPE like '%||2||%' ";									
									}elseif($DPISDB=="oci8"){
										$cmd = " select	a.EDU_TYPE,c.EL_NAME ,d.EM_NAME,b.EN_SHORTNAME,b.EN_NAME
														 from PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCLEVEL c,PER_EDUCMAJOR d
													   where a.PER_ID=$PER_ID and  a.EDU_TYPE like '%||2||%'  
																and a.EN_CODE=b.EN_CODE(+) and b.EL_CODE=c.EL_CODE(+) 
																and a.EM_CODE=d.EM_CODE(+) ";				
									}elseif($DPISDB=="mysql"){
										$cmd = " select 	a.EDU_TYPE,c.EL_NAME ,d.EM_NAME,b.EN_SHORTNAME,b.EN_NAME
														from ( 	
																	(
																		PER_EDUCATE a
																		left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
																	) left join PER_EDUCLEVEL c on (b.EL_CODE=c.EL_CODE)
																) left join PER_EDUCMAJOR d on (a.EM_CODE=d.EM_CODE)
														where a.PER_ID=$PER_ID and  a.EDU_TYPE like '%||2||%' ";	
									} // end if
									$db_dpis2->send_cmd($cmd);
									$data2 = $db_dpis2->get_array();
								
									$EDU_TYPE = trim($data2[EDU_TYPE]);
									$EL_NAME = trim($data2[EL_NAME]);
									$EM_NAME = trim($data2[EM_NAME]);		//สาขา
									$EN_SHORTNAME =  trim($data2[EN_SHORTNAME]);
									$EN_NAME =  trim($data2[EN_NAME]);		//ชื่อวุฒิ
								} // end if
							} // end if
							
							$arr_content[$data_count][type] = "CONTENT";
							$arr_content[$data_count][order] = $data_row;
							$arr_content[$data_count][pos_no] = $POS_NO;
							$arr_content[$data_count][org_name] = $ORG_NAME;
							$arr_content[$data_count][org_name2] = $ORG_NAME2;
							$arr_content[$data_count][pm_name] = ($PM_NAME?$PM_NAME:$PL_NAME);
							$arr_content[$data_count][pl_name] = "$PL_NAME $CL_NAME";
							$arr_content[$data_count][cl_name] = $CL_NAME;
							$arr_content[$data_count][new_position_type] = $NEW_POSITION_TYPE;
							//$arr_content[$data_count][pt_name] = $PT_NAME;
							$arr_content[$data_count][ot_name] = $OT_NAME;
							$arr_content[$data_count][pv_name] = $PV_NAME;
							$arr_content[$data_count][per_name] = $PER_NAME;
							$arr_content[$data_count][per_surname] = $PER_SURNAME;
							$arr_content[$data_count][per_cardno] = $PER_CARDNO;
							$arr_content[$data_count][per_gender] = ($PER_GENDER==1?"ชาย":($PER_GENDER==2?"หญิง":"ว่าง"));
							$arr_content[$data_count][birthdate_d] = $BIRTHDATE_D;
							$arr_content[$data_count][birthdate_m] = $BIRTHDATE_M;
							$arr_content[$data_count][birthdate_y] = $BIRTHDATE_Y;
							$arr_content[$data_count][startdate_d] = $STARTDATE_D;
							$arr_content[$data_count][startdate_m] = $STARTDATE_M;
							$arr_content[$data_count][startdate_y] = $STARTDATE_Y;
							$arr_content[$data_count][retiredate_y] = $RETIREDATE_Y;
							$arr_content[$data_count][level_no] = level_no_format($LEVEL_NAME);
//							$arr_content[$data_count][per_salary] = ($PER_SALARY?number_format($PER_SALARY):"");
//							$arr_content[$data_count][per_mgtsalary] = ($PER_MGTSALARY?number_format($PER_MGTSALARY):"");
							$arr_content[$data_count][per_salary] = $PER_SALARY;
							$arr_content[$data_count][per_mgtsalary] = $PER_MGTSALARY;
							$arr_content[$data_count][el_name] = 	$EL_NAME;	//"$EDU_TYPE - $EL_NAME - $EM_NAME - $EN_SHORTNAME";
							$arr_content[$data_count][em_name] = $EN_NAME;	//$EN_SHORTNAME;
	
							$data_count++;														
						} // end if
					} // end if
				break;
			} // end switch case
		} // end for
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
//	echo "count data=".count($arr_content)."<br>";
	if($count_data){
		$xlsRow = 0;
		$count_org_ref = 0;

		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$ORDER = $arr_content[$data_count][order];
			$POS_NO = $arr_content[$data_count][pos_no];
			$ORG_NAME = $arr_content[$data_count][org_name];
			$ORG_NAME2 = $arr_content[$data_count][org_name2];
			$PM_NAME = $arr_content[$data_count][pm_name];
			$PL_NAME = $arr_content[$data_count][pl_name];
			$CL_NAME = $arr_content[$data_count][cl_name];
			$NEW_POSITION_TYPE = $arr_content[$data_count][new_position_type];
			//$PT_NAME = $arr_content[$data_count][pt_name];
			$OT_NAME = $arr_content[$data_count][ot_name];
			$PV_NAME = $arr_content[$data_count][pv_name];
			$PER_NAME = $arr_content[$data_count][per_name];
			$PER_SURNAME = $arr_content[$data_count][per_surname];
			$PER_CARDNO = $arr_content[$data_count][per_cardno];
			$PER_GENDER = $arr_content[$data_count][per_gender];
			$BIRTHDATE_D = $arr_content[$data_count][birthdate_d];
			$BIRTHDATE_M = $arr_content[$data_count][birthdate_m];
			$BIRTHDATE_Y = $arr_content[$data_count][birthdate_y];
			$STARTDATE_D = $arr_content[$data_count][startdate_d];
			$STARTDATE_M = $arr_content[$data_count][startdate_m];
			$STARTDATE_Y = $arr_content[$data_count][startdate_y];
			$RETIREDATE_Y = $arr_content[$data_count][retiredate_y];
			$LEVEL_NO = $arr_content[$data_count][level_no];
			$PER_SALARY = $arr_content[$data_count][per_salary];
			$PER_MGTSALARY = $arr_content[$data_count][per_mgtsalary];
			$EL_NAME = $arr_content[$data_count][el_name];
			$EM_NAME = $arr_content[$data_count][em_name];
			
//			if($REPORT_ORDER == "ORG_REF"){
				if($data_count > 0) $count_org_ref++;

				$ORG_ID_REF = $arr_content[$data_count][org_id_ref];
				$ORG_NAME_REF = $arr_content[$data_count][org_name_ref];

//				$worksheet = &$workbook->addworksheet("$report_code".($count_org_ref?"_$count_org_ref":""));
//				$worksheet->set_margin_right(0.50);
//				$worksheet->set_margin_bottom(1.10);
/*				
				//====================== SET FORMAT ======================//
				require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
				//====================== SET FORMAT ======================//
				
				//$arr_title = explode("||", "$ORG_NAME_REF||$report_title");
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
					$worksheet->write($xlsRow, 24, "", set_format("xlsFmtTitle", "B", "C", "", 1));
					$worksheet->write($xlsRow, 25, "", set_format("xlsFmtTitle", "B", "C", "", 1));
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
					$worksheet->write($xlsRow, 12, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 13, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 14, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 15, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 16, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 17, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 18, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 19, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 20, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 21, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 22, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 23, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 24, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 25, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
				} // end if

				print_header();
			}else{
				$xlsRow++;
				$worksheet->write_string($xlsRow, 0, "$ORDER", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 1, "$POS_NO", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 2, "$ORG_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 3, "$ORG_NAME2", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 4, "$PM_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 5, "$PL_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 6, "$CL_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 7, "$NEW_POSITION_TYPE", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 8, "$OT_NAME", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 9, "$PV_NAME", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 10, "$PER_NAME   $PER_SURNAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 11, "$PER_CARDNO", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 12, "$PER_GENDER", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 13, "$BIRTHDATE_D", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 14, "$BIRTHDATE_M", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 15, "$BIRTHDATE_Y", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 16, "$STARTDATE_D", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 17, "$STARTDATE_M", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 18, "$STARTDATE_Y", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 19, "$RETIREDATE_Y", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 20, "$LEVEL_NO", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 21, "$PER_SALARY", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write_string($xlsRow, 22, "$PER_MGTSALARY", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));	
				$worksheet->write_string($xlsRow, 23, "$EL_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 24, "$EM_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 25, "", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));		*/
				echo "$ORDER, $POS_NO, $ORG_NAME, $ORG_NAME2, $PM_NAME, $PL_NAME, $CL_NAME, $NEW_POSITION_TYPE, $OT_NAME, $PV_NAME, $PER_NAME   $PER_SURNAME, $PER_CARDNO, $PER_GENDER, $BIRTHDATE_D, $BIRTHDATE_M, $BIRTHDATE_Y, $PER_SALARY<br>";
//			} // end if
		} // end for
/*				$xlsRow++;
				$worksheet->write_string($xlsRow, 2, "ผู้ให้ข้อมูล", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
				$xlsRow++;
				$worksheet->write_string($xlsRow, 2, "โทร", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
	}else{
		$worksheet = &$workbook->addworksheet("$report_code");
		$worksheet->set_margin_right(0.50);
		$worksheet->set_margin_bottom(1.10);
*/		
		//====================== SET FORMAT ======================//
//		require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
		//====================== SET FORMAT ======================//

/*		$xlsRow = 0;
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
		$worksheet->write($xlsRow, 24, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 25, "", set_format("xlsFmtTitle", "B", "C", "", 1));
	} // end if

	$workbook->close();
*/
	}
	ini_set("max_execution_time", 30);
/*	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"แบบ คปร.7.xls\"");
	header("Content-Disposition: inline; filename=\"แบบ คปร.7.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
*/
?>