<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	$search_condition = "";
//	$arr_search_condition[] = "(a.POS_STATUS=1)";

	$list_type_text = $ALL_REPORT_TITLE;

	if(trim($search_pos_no)){ 
		$arr_search_condition[] = "(a.POS_NO = $search_pos_no)";
		$list_type_text .= "$search_pos_no";
	} // end if
	if(trim($search_pl_code)){ 
		$search_pl_code = trim($search_pl_code);
		$arr_search_condition[] = "(a.PL_CODE = '$search_pl_code')";
		$list_type_text .= "$search_pl_name";
	} // end if
	if(trim($search_cl_code)){ 
		$search_cl_code = trim($search_cl_code);
		$arr_search_condition[] = "(a.CL_NAME = '$search_cl_code')";
		$list_type_text .= "$search_cl_name";
	} // end if
	if(trim($search_lv_code)){ 
		$search_lv_code = trim($search_lv_code);
		$arr_search_condition[] = "(a.LEVEL_NO = '$search_lv_code')";
		$list_type_text .= "$search_lv_name";
	} // end if

	if(trim($search_org_id_2)){ 
		$arr_search_condition[] = "(a.ORG_ID_2 = $search_org_id_2)";
		$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME - $search_org_name - $search_org_name_1 - $search_org_name_2";
	}elseif(trim($search_org_id_1)){ 
		$arr_search_condition[] = "(a.ORG_ID_1 = $search_org_id_1)";
		$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME - $search_org_name - $search_org_name_1";
	}elseif(trim($search_org_id)){ 
		$arr_search_condition[] = "(a.ORG_ID = $search_org_id)";
		$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME - $search_org_name";
	}elseif($DEPARTMENT_ID){
		$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
	}elseif($MINISTRY_ID){
//		$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
//		$db_dpis->send_cmd($cmd);
//		while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

//		$arr_search_condition[] = "(b.ORG_ID_REF in (". implode($arr_org_ref, ",") ."))";
		$arr_search_condition[] = "(e.ORG_ID = $MINISTRY_ID)";
		$list_type_text .= " - $MINISTRY_NAME";
	}elseif($PROVINCE_CODE){
		$PROVINCE_CODE = trim($PROVINCE_CODE);
		$arr_search_condition[] = "(b.PV_CODE = '$PROVINCE_CODE')";
		$list_type_text .= " - $PROVINCE_NAME";
	} // end if

	if(trim($search_ol_code)){ 
		$search_ol_code = trim($search_ol_code);
		$arr_search_condition[] = "(b.OL_CODE = '$search_ol_code')";
		$list_type_text .= "$search_ol_name";
	} // end if

	if(trim($search_pv_code)){ 
		$search_pv_code = trim($search_pv_code);
		$arr_search_condition[] = "(.PV_CODE = '$search_pv_code')";
		$list_type_text .= " - $search_ct_name - $search_pv_name";
	}elseif(trim($search_ct_code)){ 
		$search_ct_code = trim($search_ct_code);
		$arr_search_condition[] = "(b.CT_CODE = '$search_ct_code')";
		$list_type_text .= " - $search_ct_name";
	} // end if

	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);
	
//	$company_name = "รูปแบบการออกรายงาน : $list_type_text";
	$report_title = "รายชื่อตำแหน่ง";
	$report_code = "POSITION LIST";

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/jethro_$token.xls";

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
		
		$worksheet->set_column(0, 0, 10);
		$worksheet->set_column(1, 1, 25);
		$worksheet->set_column(2, 2, 40);
		$worksheet->set_column(3, 3, 10);
		$worksheet->set_column(4, 4, 30);
		$worksheet->set_column(5, 5, 10);
		$worksheet->set_column(6, 6, 10);
		$worksheet->set_column(7, 7, 15);
		$worksheet->set_column(8, 8, 15);
		$worksheet->set_column(9, 9, 10);
		$worksheet->set_column(10, 10, 12);
		$worksheet->set_column(11, 11, 12);
		$worksheet->set_column(12, 12, 30);
		$worksheet->set_column(13, 13, 30);
		$worksheet->set_column(14, 14, 30);
		$worksheet->set_column(15, 15, 30);
		$worksheet->set_column(16, 16, 30);
		$worksheet->set_column(17, 17, 15);
		$worksheet->set_column(18, 18, 15);
		$worksheet->set_column(19, 19, 15);
		$worksheet->set_column(20, 20, 12);

		$worksheet->write($xlsRow, 0, "เลขที่", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 1, "ตำแหน่งใน", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 2, "ตำแหน่งในสายงาน", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 3, "ระดับควบ", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 4, "ระดับใหม่", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 5, "อัตรา", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 6, "เงินประจำ", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 7, "ตำแหน่ง", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 8, "ตำแหน่ง", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 9, "สถานภาพ", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 10, "วันที่กำหนด", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 11, "วันที่ตำแหน่ง", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 12, "ชื่อหน่วยงาน", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLBR&isMerge=1&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 13, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLBR&isMerge=1&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 14, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLBR&isMerge=1&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 15, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLBR&isMerge=1&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 16, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLBR&isMerge=1&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 17, "ที่ตั้ง", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLBR&isMerge=1&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 18, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLBR&isMerge=1&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 19, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLBR&isMerge=1&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 20, "วันที่ประกาศ", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "ตำแหน่ง", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 1, "การบริหาร", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 2, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 3, "(เดิม)", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 4, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 5, "เงินเดือน", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 6, "ตำแหน่ง", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 7, "ประเภท", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 8, "ประเภท", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 9, "ตำแหน่ง", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 10, "ตำแหน่ง", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 11, "มีเงิน", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 12, "ส่วนราชการ", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 13, "ส่วนราชการ", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 14, "$ORG_TITLE", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 15, "ต่ำกว่ากอง", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 16, "ต่ำกว่ากอง", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 17, "อำเภอ", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 18, "จังหวัด", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 19, "ประเทศ", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 20, "กำหนด/", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 1, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 2, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 3, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 4, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 5, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 6, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 7, "(เดิม)", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 8, "(ใหม่)", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 9, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 10, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 11, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 12, "ระดับ$MINISTRY_TITLE", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 13, "ระดับ$DEPARTMENT_TITLE", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 14, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 15, "1 ระดับ", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 16, "2 ระดับ", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 17, "/เขต", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 18, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 19, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 20, "ปรับปรุงโครงสร้าง", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LBR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 1, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LBR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 2, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LBR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 3, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LBR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 4, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LBR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 5, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LBR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 6, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LBR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 7, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LBR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 8, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LBR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 9, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LBR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 10, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LBR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 11, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LBR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 12, "/ทบวง", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LBR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 13, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LBR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 14, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LBR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 15, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LBR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 16, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LBR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 17, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LBR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 18, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LBR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 19, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LBR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 20, "ส่วนราชการ", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LBR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
	} // function		
	
	if($DPISDB=="odbc"){
		$cmd = " 	select		a.POS_NO, h.PM_NAME, f.PL_NAME, a.CL_NAME, a.LEVEL_NO, a.POS_SALARY,
											a.POS_MGTSALARY, g.PT_NAME, a.POS_STATUS, a.POS_DATE, a.POS_GET_DATE, 
											e.ORG_NAME as MINISTRY_NAME, d.ORG_NAME as DEPARTMENT_NAME, b.ORG_NAME,
											a.ORG_ID_1, a.ORG_ID_2, i.AP_NAME, j.PV_NAME, k.CT_NAME
							from		(
												(
													(
														(
															(
																(
																	(
																		(
																			(
																				PER_POSITION a
																				inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
																			) inner join PER_CO_LEVEL c on (a.CL_NAME=c.CL_NAME)
																		) inner join PER_ORG d on (b.ORG_ID_REF=d.ORG_ID)
																	) inner join PER_ORG e on (d.ORG_ID_REF=e.ORG_ID)
																) inner join PER_LINE f on (a.PL_CODE=f.PL_CODE)
															) left join PER_TYPE g on (a.PT_CODE=g.PT_CODE)
														) left join PER_MGT h on (a.PM_CODE=h.PM_CODE)
													) left join PER_AMPHUR i on (b.AP_CODE=i.AP_CODE)
												) left join PER_PROVINCE j on (b.PV_CODE=j.PV_CODE)
											) left join PER_COUNTRY k on (b.CT_CODE=k.CT_CODE)
							$search_condition
							order by	e.ORG_SEQ_NO, e.ORG_ID, d.ORG_SEQ_NO, d.ORG_ID, IIf(IsNull(a.POS_NO), 0, CLng(a.POS_NO)) ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);	
		$cmd = " 	select		a.POS_NO, h.PM_NAME, f.PL_NAME, a.CL_NAME, a.LEVEL_NO, a.POS_SALARY,
											a.POS_MGTSALARY, g.PT_NAME, a.POS_STATUS, a.POS_DATE, a.POS_GET_DATE, 
											e.ORG_NAME as MINISTRY_NAME, d.ORG_NAME as DEPARTMENT_NAME, b.ORG_NAME,
											a.ORG_ID_1, a.ORG_ID_2, i.AP_NAME, j.PV_NAME, k.CT_NAME
							from		PER_POSITION a, PER_ORG b, PER_CO_LEVEL c, PER_ORG d, PER_ORG e,
											PER_LINE f, PER_TYPE g, PER_MGT h, PER_AMPHUR i, PER_PROVINCE j, PER_COUNTRY k
							where		a.ORG_ID=b.ORG_ID and a.CL_NAME=c.CL_NAME and b.ORG_ID_REF=d.ORG_ID 
											and d.ORG_ID_REF=e.ORG_ID and a.PL_CODE=f.PL_CODE and a.PT_CODE=g.PT_CODE(+)
											and a.PM_CODE=h.PM_CODE and b.AP_CODE=i.AP_CODE(+) and b.PV_CODE=j.PV_CODE(+) 
											and b.CT_CODE=k.CT_CODE(+)
											$search_condition
							order by	e.ORG_SEQ_NO, e.ORG_ID, d.ORG_SEQ_NO, d.ORG_ID, to_number(replace(a.POS_NO,'-','')) ";
	}elseif($DPISDB=="mssql"){
		$cmd = " 	select		a.POS_NO, h.PM_NAME, f.PL_NAME, a.CL_NAME, a.LEVEL_NO, a.POS_SALARY,
											a.POS_MGTSALARY, g.PT_NAME, a.POS_STATUS, a.POS_DATE, a.POS_GET_DATE, 
											e.ORG_NAME as MINISTRY_NAME, d.ORG_NAME as DEPARTMENT_NAME, b.ORG_NAME,
											a.ORG_ID_1, a.ORG_ID_2, i.AP_NAME, j.PV_NAME, k.CT_NAME
							from		(
												(
													(
														(
															(
																(
																	(
																		(
																			(
																				PER_POSITION a
																				inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
																			) inner join PER_CO_LEVEL c on (a.CL_NAME=c.CL_NAME)
																		) inner join PER_ORG d on (b.ORG_ID_REF=d.ORG_ID)
																	) inner join PER_ORG e on (d.ORG_ID_REF=e.ORG_ID)
																) inner join PER_LINE f on (a.PL_CODE=f.PL_CODE)
															) left join PER_TYPE g on (a.PT_CODE=g.PT_CODE)
														) left join PER_MGT h on (a.PM_CODE=h.PM_CODE)
													) left join PER_AMPHUR i on (b.AP_CODE=i.AP_CODE)
												) left join PER_PROVINCE j on (b.PV_CODE=j.PV_CODE)
											) left join PER_COUNTRY k on (b.CT_CODE=k.CT_CODE)
							$search_condition
							order by	e.ORG_SEQ_NO, e.ORG_ID, d.ORG_SEQ_NO, d.ORG_ID, a.POS_NO ";
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data_count = 0;

	while($data = $db_dpis->get_array()){
		$POS_NO = $data[POS_NO];
		$PM_NAME = $data[PM_NAME];
		$PL_NAME = $data[PL_NAME];
		$CL_NAME = $data[CL_NAME];
		$LEVEL_NO = $data[LEVEL_NO];
		$cmd = " select LEVEL_NAME from PER_LEVEL where LEVEL_NO='$LEVEL_NO' ";
		$db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();
		$data2 = $db_dpis2->get_array();
		$LEVEL_NAME = $data2[LEVEL_NAME];

		$POS_SALARY = $data[POS_SALARY];
		$POS_MGTSALARY = $data[POS_MGTSALARY];
		$PT_NAME = $data[PT_NAME];
		$POS_STATUS = $data[POS_STATUS];
		if ($POS_STATUS==1) $POS_STATUS = "ปกติ"; else $POS_STATUS = "ยุบเลิก";

		$POS_DATE = show_date_format($data[POS_DATE], 2);
		$POS_GET_DATE = show_date_format($data[POS_GET_DATE], 2);

		$MINISTRY_NAME = $data[MINISTRY_NAME];
		$DEPARTMENT_NAME = $data[DEPARTMENT_NAME];
		$ORG_NAME = $data[ORG_NAME];

		$ORG_ID_1 = $data[ORG_ID_1];
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_1 ";
		$db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();
		$data2 = $db_dpis2->get_array();
		$ORG_NAME_1 = $data2[ORG_NAME];

		$ORG_ID_2 = $data[ORG_ID_2];
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_2 ";
		$db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();
		$data2 = $db_dpis2->get_array();
		$ORG_NAME_2 = $data2[ORG_NAME];

		$AP_NAME = $data[AP_NAME];
		$PV_NAME = $data[PV_NAME];
		$CT_NAME = $data[CT_NAME];
		
		$arr_content[$data_count][pos_no] = $POS_NO;
		$arr_content[$data_count][pm_name] = $PM_NAME;
		$arr_content[$data_count][pl_name] = $PL_NAME;
		$arr_content[$data_count][cl_name] = $CL_NAME;
		$arr_content[$data_count][level_name] = $LEVEL_NAME;
		$arr_content[$data_count][pos_salary] = $POS_SALARY;
		$arr_content[$data_count][pos_mgtsalary] = $POS_MGTSALARY;
		$arr_content[$data_count][pt_name] = $PT_NAME;
		$arr_content[$data_count][pos_status] = $POS_STATUS;
		$arr_content[$data_count][pos_date] = $POS_DATE;
		$arr_content[$data_count][pos_get_date] = $POS_GET_DATE;
		$arr_content[$data_count][ministry_name] = $MINISTRY_NAME;
		$arr_content[$data_count][department_name] = $DEPARTMENT_NAME;
		$arr_content[$data_count][org_name] = $ORG_NAME;
		$arr_content[$data_count][org_name_1] = $ORG_NAME_1;
		$arr_content[$data_count][org_name_2] = $ORG_NAME_2;
		$arr_content[$data_count][ap_name] = $AP_NAME;
		$arr_content[$data_count][pv_name] = $PV_NAME;
		$arr_content[$data_count][ct_name] = $CT_NAME;

		$data_count++;
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
	if($count_data){
		$xlsRow = 0;
		$arr_title = explode("||", $report_title);
		for($i=0; $i<count($arr_title); $i++){
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
			$xlsRow++;
		} // end if

		if($company_name){
			$worksheet->write($xlsRow, 0, $company_name, set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 2, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 4, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 5, "", set_format("xlsFmtSubTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 6, "", set_format("xlsFmtSubTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 7, "", set_format("xlsFmtSubTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 8, "", set_format("xlsFmtSubTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 9, "", set_format("xlsFmtSubTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 10, "", set_format("xlsFmtSubTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 11, "", set_format("xlsFmtSubTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 12, "", set_format("xlsFmtSubTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 13, "", set_format("xlsFmtSubTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 14, "", set_format("xlsFmtSubTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 15, "", set_format("xlsFmtSubTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 16, "", set_format("xlsFmtSubTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 17, "", set_format("xlsFmtSubTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 18, "", set_format("xlsFmtSubTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 19, "", set_format("xlsFmtSubTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 20, "", set_format("xlsFmtSubTitle", "B", "C", "", 1));
			$xlsRow++;
		} // end if

		print_header();
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$POS_NO = $arr_content[$data_count][pos_no];
			$PM_NAME = $arr_content[$data_count][pm_name];
			$PL_NAME = $arr_content[$data_count][pl_name];
			$CL_NAME = $arr_content[$data_count][cl_name];
			$LEVEL_NAME = $arr_content[$data_count][level_name];
			$POS_SALARY = $arr_content[$data_count][pos_salary];
			$POS_MGTSALARY = $arr_content[$data_count][pos_mgtsalary];
			$PT_NAME = $arr_content[$data_count][pt_name];
			$POS_STATUS = $arr_content[$data_count][pos_status];
			$POS_DATE = $arr_content[$data_count][pos_date];
			$POS_GET_DATE = $arr_content[$data_count][pos_get_date];
			$MINISTRY_NAME = $arr_content[$data_count][ministry_name];
			$DEPARTMENT_NAME = $arr_content[$data_count][department_name];
			$ORG_NAME = $arr_content[$data_count][org_name];
			$ORG_NAME_1 = $arr_content[$data_count][org_name_1];
			$ORG_NAME_2 = $arr_content[$data_count][org_name_2];
			$AP_NAME = $arr_content[$data_count][ap_name];
			$PV_NAME = $arr_content[$data_count][pv_name];
			$CT_NAME = $arr_content[$data_count][ct_name];
			
			$xlsRow++;
			$worksheet->write_string($xlsRow, 0, "$POS_NO", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 1, "$PM_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 2, "$PL_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 3, "$CL_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 4, "$LEVEL_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 5, "$POS_SALARY", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
			$worksheet->write_string($xlsRow, 6, "$POS_MGTSALARY", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
			$worksheet->write_string($xlsRow, 7, "$PT_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 8, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 9, "$POS_STATUS", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 10, "$POS_DATE", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 11, "$POS_GET_DATE", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 12, "$MINISTRY_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 13, "$DEPARTMENT_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 14, "$ORG_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 15, "$ORG_NAME_1", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 16, "$ORG_NAME_2", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 17, "$AP_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 18, "$PV_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 19, "$CT_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 20, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			
			if($data_count > 0 && ($data_count % 30000)==0){
				$page_count++;
				$worksheet = &$workbook->addworksheet("$report_code($page_count)");
				$worksheet->set_margin_right(0.50);
				$worksheet->set_margin_bottom(1.10);

				$xlsRow = 0;				
				print_header();
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
		$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 14, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 15, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 16, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 17, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 18, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 19, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 20, "", set_format("xlsFmtTitle", "B", "C", "", 1));
	} // end if

	$workbook->close();

	ini_set("max_execution_time", 30);
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"$report_code.xls\"");
	header("Content-Disposition: inline; filename=\"$report_code.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);

?>