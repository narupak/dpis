<?
	include("../../php_scripts/connect_database.php");
	include("../php_scripts/pdf_wordarray_thaicut.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
    
	if ($FLAG_RTF) {
		include ("rtf_setvar.php");	// กำหนดตัวแปรค่าสี set_of_colors
		require("../../RTF/rtf_class.php");
	} else{
		define('FPDF_FONTPATH','../../PDF/font/');
		include ("../../PDF/fpdf.php");
		include ("../../PDF/pdf_extends_DPIS.php");
	}

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	ini_set("max_execution_time", $max_execution_time);
	$report_title = trim($report_title);
	$report_code = "";
	$company_name = "";
	
//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();
	if ($FLAG_RTF) {
		$sum_w = array_sum($heading_width);
		for($h = 0; $h < count($heading_width); $h++) {
			$heading_width[$h] = $heading_width[$h] / $sum_w * 100;
		}
		$fname= "rpt_master_table_position.rtf";
	//	RTF $papersize = "a4", $margl = 720, $margr = 720, $margt = 720, $margb = 720, $orient = "p", $u_color_tab="";
		$paper_size="a4";
		$orientation='P';
		$RTF = new RTF($paper_size, ".7in", ".3in", ".5in", ".1in", $orientation, $set_of_colors);
		$RTF->set_default_font($font, 14);
	//	echo "default font_id::".$RTF->dfl_FontID."<br>";	

		$RTF->set_report_code($report_code);
		$RTF->set_report_title($report_title);
	} else {
		$unit="mm";
		$paper_size="A4";
		$lang_code="TH";
		$orientation='L';
		$pdf = new PDF($orientation,$unit,$paper_size,$lang_code,$company_name,$report_title,$report_code,$heading,$heading_width,$heading_align);
		$pdf->Open();
		$pdf->SetMargins(5,5,5);
		$pdf->AliasNbPages();
		$pdf->AddPage();
		$pdf->SetTextColor(0, 0, 0);
		$pdf->SetFont($font,'',14);
		$pdf->SetAutoPageBreak(true,10);
    } 
   	if ($FLAG_RTF) {
		if ($MUNICIPALITY_FLAG==1)
			$heading_width[0] = "8";
		else
			$heading_width[0] = "5";
		$heading_width[1] = "12";
		$heading_width[2] = "12";
		$heading_width[3] = "6";
		if ($MUNICIPALITY_FLAG==1) {
			$heading_width[4] = "8";
			$heading_width[5] = "8";
			$heading_width[6] = "9";
			$heading_width[7] = "9";
			$heading_width[8] = "9";
			$heading_width[9] = "8";
			$heading_width[10] = "9";
			$heading_width[11] = "5";
		} else {
			$heading_width[4] = "8";
			$heading_width[5] = "9";
			$heading_width[6] = "9";
			$heading_width[7] = "9";
			$heading_width[8] = "8";
			$heading_width[9] = "9";
			$heading_width[10] = "5";
		}
	} else {
		if ($MUNICIPALITY_FLAG==1)
			$heading_width[0] = "18";
		else
			$heading_width[0] = "12";
		$heading_width[1] = "32";
		$heading_width[2] = "32";
		if ($MUNICIPALITY_FLAG==1) {
			$heading_width[3] = "14";
			$heading_width[4] = "23";
			$heading_width[5] = "23";
			$heading_width[6] = "25";
			$heading_width[7] = "25";
			$heading_width[8] = "25";
			$heading_width[9] = "23";
			$heading_width[10] = "30";
			$heading_width[11] = "18";
		} else {
			$heading_width[3] = "23";
			$heading_width[4] = "25";
			$heading_width[5] = "30";
			$heading_width[6] = "30";
			$heading_width[7] = "30";
			$heading_width[8] = "25";
			$heading_width[9] = "30";
			$heading_width[10] = "18";
		}
	}
		
	$heading_text[0] = "เลขที่|ตำแหน่ง";
	$heading_text[1] = "ชื่อตำแหน่ง|ทางบริหาร";
	$heading_text[2] = "ชื่อตำแหน่ง|ในสายงาน";
	$heading_text[3] = "ช่วงระดับ|ตำแหน่ง";
	if ($MUNICIPALITY_FLAG==1) {
		$heading_text[4] = (($NUMBER_DISPLAY==2)?convert2thaidigit("$PT_TITLE$PT_TITLE1"):"$PT_TITLE$PT_TITLE1");
		$heading_text[5] = (($NUMBER_DISPLAY==2)?convert2thaidigit("$PT_TITLE$PT_TITLE2"):"$PT_TITLE$PT_TITLE2");
		$heading_text[6] = "$DEPARTMENT_TITLE";
		$heading_text[7] = "$ORG_TITLE";
		$heading_text[8] = "$ORG_TITLE1|$ORG_TITLE2";
		$heading_text[9] = "ผู้ครองตำแหน่ง";
		$heading_text[10] = "$REMARK_TITLE";
		$heading_text[11] = "$ACTIVE_TITLE";
		
		$heading_align = array('C','C','C','C','C','C','C','C','C','C','C','C');
	} else {
		$heading_text[4] = (($NUMBER_DISPLAY==2)?convert2thaidigit("$PT_TITLE$PT_TITLE2"):"$PT_TITLE$PT_TITLE2");
		$heading_text[5] = "$DEPARTMENT_TITLE";
		$heading_text[6] = "$ORG_TITLE";
		$heading_text[7] = "$ORG_TITLE1|$ORG_TITLE2";
		$heading_text[8] = "ผู้ครองตำแหน่ง";
		$heading_text[9] = "$REMARK_TITLE";
		$heading_text[10] = "$ACTIVE_TITLE";
		
		$heading_align = array('C','C','C','C','C','C','C','C','C','C','C');
	}
		
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
		$order_str = "a.DEPARTMENT_ID $SortType[$order_by], b.ORG_SEQ_NO $SortType[$order_by], b.ORG_CODE $SortType[$order_by], POS_NO_NAME $SortType[$order_by], $POS_NO_NUM $SortType[$order_by]";
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
	} 
        if(trim($search_org_id_3)){ /*Release 5.1.0.4*/
		$arr_search_condition[] = "(ORG_ID_3 = $search_org_id_3)";
		$print_search_condition[] = "$ORG_TITLE3 : $search_org_name_3";
	} 
        if(trim($search_org_id_4)){ /*Release 5.1.0.4*/
		$arr_search_condition[] = "(ORG_ID_4 = $search_org_id_4)";
		$print_search_condition[] = "$ORG_TITLE4 : $search_org_name_4";
	} 
        if(trim($search_org_id_5)){ /*Release 5.1.0.4*/
		$arr_search_condition[] = "(ORG_ID_5 = $search_org_id_5)";
		$print_search_condition[] = "$ORG_TITLE5 : $search_org_name_5";
	} 
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
										a.ORG_ID, b.ORG_NAME, b.ORG_SHORT, b.ORG_ID_REF, a.ORG_ID_1, a.ORG_ID_2, a.POS_STATUS, 
										c.PER_STATUS as PER_STATUS1, d.PER_STATUS as PER_STATUS2, e.PER_STATUS as PER_STATUS3, 
										f.PER_STATUS as PER_STATUS4, a.POS_REMARK, a.LEVEL_NO
						from (
									(
										(
											(
												PER_POSITION a
												inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
											) left join PER_PERSONAL c on (a.POS_ID=c.POS_ID and c.PER_TYPE=1 and c.PER_STATUS=1)
										) left join PER_PERSONAL d on (a.POS_ID=d.POS_ID and d.PER_TYPE=1 and (d.PER_STATUS=0))
									) left join PER_PERSONAL e on (a.POS_ID=e.PAY_ID and e.PER_TYPE=1 and e.PER_STATUS=1)
								) left join PER_PERSONAL f on (a.POS_ID=f.PAY_ID and f.PER_TYPE=1 and (f.PER_STATUS=0))
						$search_condition $check_condition
						group by a.POS_ID, a.DEPARTMENT_ID, a.POS_NO_NAME, IIf(IsNull(a.POS_NO), 0, CLng(a.POS_NO)), PM_CODE, PL_CODE, CL_NAME, PT_CODE, a.POS_REMARK, a.LEVEL_NO,
										a.ORG_ID, b.ORG_SEQ_NO, b.ORG_CODE, b.ORG_NAME, b.ORG_SHORT, b.ORG_ID_REF, a.ORG_ID_1, a.ORG_ID_2, a.POS_STATUS, c.PER_STATUS, d.PER_STATUS, e.PER_STATUS, f.PER_STATUS
						order by $order_str ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = "	select 		a.POS_ID, a.DEPARTMENT_ID, a.POS_NO_NAME, to_number(replace(POS_NO,'-','')) as POS_NO, PM_CODE, PL_CODE, CL_NAME, PT_CODE, 
											a.ORG_ID, b.ORG_NAME, b.ORG_SHORT, b.ORG_ID_REF, a.ORG_ID_1, a.ORG_ID_2, a.POS_STATUS, 
											c.PER_STATUS as PER_STATUS1, d.PER_STATUS as PER_STATUS2, e.PER_STATUS as PER_STATUS3, 
										f.PER_STATUS as PER_STATUS4, a.POS_REMARK, a.LEVEL_NO
							from 		PER_POSITION a, PER_ORG b, 
											(select POS_ID, PER_ID, PER_NAME, PER_STATUS from PER_PERSONAL where PER_TYPE=1 and PER_STATUS=1) c, 
											(select POS_ID, PER_ID, PER_NAME, PER_STATUS from PER_PERSONAL where PER_TYPE=1 and (PER_STATUS=0)) d,
											(select PAY_ID, PER_ID, PER_NAME, PER_STATUS from PER_PERSONAL where PER_TYPE=1 and PER_STATUS=1) e, 
											(select PAY_ID, PER_ID, PER_NAME, PER_STATUS from PER_PERSONAL where PER_TYPE=1 and (PER_STATUS=0)) f
							where 	a.ORG_ID=b.ORG_ID and a.POS_ID=c.POS_ID(+) and a.POS_ID=d.POS_ID(+) and a.POS_ID=e.PAY_ID(+) and a.POS_ID=f.PAY_ID(+)
											$search_condition $check_condition
							group by a.POS_ID, a.DEPARTMENT_ID, a.POS_NO_NAME, to_number(replace(POS_NO,'-','')), PM_CODE, PL_CODE, CL_NAME, PT_CODE, a.POS_REMARK, a.LEVEL_NO,
											 a.ORG_ID, b.ORG_SEQ_NO, b.ORG_CODE, b.ORG_NAME, b.ORG_SHORT, b.ORG_ID_REF, a.ORG_ID_1, a.ORG_ID_2, a.POS_STATUS, c.PER_STATUS, d.PER_STATUS, e.PER_STATUS, f.PER_STATUS
							order by $order_str ";
	}elseif($DPISDB=="mysql"){
		$cmd =" select 		a.POS_ID, a.DEPARTMENT_ID, a.POS_NO_NAME, a.POS_NO as POS_NO, PM_CODE, PL_CODE, CL_NAME, PT_CODE, 
										a.ORG_ID, b.ORG_NAME, b.ORG_SHORT, b.ORG_ID_REF, a.ORG_ID_1, a.ORG_ID_2, a.POS_STATUS, 
										c.PER_STATUS as PER_STATUS1, d.PER_STATUS as PER_STATUS2, e.PER_STATUS as PER_STATUS3, 
										f.PER_STATUS as PER_STATUS4, a.POS_REMARK, a.LEVEL_NO
						from (
									(
										(
											(
												PER_POSITION a
												inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
											) left join PER_PERSONAL c on (a.POS_ID=c.POS_ID and c.PER_TYPE=1 and c.PER_STATUS=1)
										) left join PER_PERSONAL d on (a.POS_ID=d.POS_ID and d.PER_TYPE=1 and (d.PER_STATUS=0))
									) left join PER_PERSONAL e on (a.POS_ID=e.PAY_ID and e.PER_TYPE=1 and e.PER_STATUS=1)
								) left join PER_PERSONAL f on (a.POS_ID=f.PAY_ID and f.PER_TYPE=1 and (f.PER_STATUS=0))
						$search_condition $check_condition
						group by a.POS_ID, a.DEPARTMENT_ID, a.POS_NO_NAME, a.POS_NO, PM_CODE, PL_CODE, CL_NAME, PT_CODE, a.POS_REMARK, a.LEVEL_NO,
										a.ORG_ID, b.ORG_SEQ_NO, b.ORG_CODE, b.ORG_NAME, b.ORG_SHORT, b.ORG_ID_REF, a.ORG_ID_1, a.ORG_ID_2, a.POS_STATUS, c.PER_STATUS, d.PER_STATUS, e.PER_STATUS, f.PER_STATUS
						order by $order_str ";
	} // end if

	$count_data = $db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();
	//echo $cmd;

	if($count_data){
		foreach($print_search_condition as $show_condition){
		if ($FLAG_RTF){ 
			$company_name .= "$show_condition"."  ";
			$RTF->set_company_name($company_name);
		}else{
			$pdf->Cell(array_sum($heading_width), 7, $show_condition, "", 1, 'L', 0);
		           }
		} // end foreach
		$head_text1 = implode(",", $heading_text);
		$head_width1 = implode(",", $heading_width);
		$head_align1 = implode(",", $heading_align);
		if ($FLAG_RTF) {
			$RTF->add_header("", 0, false);	// header default
			$RTF->add_footer("", 0, false);		// footer default
			
	//		echo "$head_text1<br>";
			$tab_align = "center";
			$result = $RTF->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", $fmtTableHeader_col_idx, $fmtTableHeader_bgcol_idx, $COLUMN_FORMAT, "", false, $tab_align);
		} else {
		    $pdf->SetFont($font,'',14);
		    $pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
			$pdf->AutoPageBreak = false; 
		    $result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", "0066CC", "EEEEFF", 0);
		}
		if (!$result) echo "****** error ****** on open table for $table<br>";

		$data_count = $data_row = 0;
		while($data = $db_dpis->get_array()){
			$data_count++;
			$data_row++;			

			$POS_ID = trim($data[POS_ID]);
			$POS_NO_NAME = trim($data[POS_NO_NAME]);
			if (substr($POS_NO_NAME,0,4)=="กปด.")
				$POS_NO = $POS_NO_NAME." ".trim($data[POS_NO]);
			else
				$POS_NO = $POS_NO_NAME.trim($data[POS_NO]);
			$ORG_ID = trim($data[ORG_ID]);
			$ORG_NAME = $ORG_NAME_1 = $ORG_NAME_2 = "";
			if ($BKK_FLAG==1) $ORG_NAME = trim($data[ORG_SHORT]);
			if (!$ORG_NAME) $ORG_NAME = trim($data[ORG_NAME]);
			$ORG_ID_REF = trim($data[ORG_ID_REF]);
			$PM_CODE = trim($data[PM_CODE]);
			$PL_CODE = trim($data[PL_CODE]);
			$PT_CODE = trim($data[PT_CODE]);
			$CL_NAME = trim($data[CL_NAME]);
			$ORG_ID_1 = trim($data[ORG_ID_1]);
			$ORG_ID_2 = trim($data[ORG_ID_2]);
			$POS_REMARK=trim($data[POS_REMARK]);
			$LEVEL_NO=trim($data[LEVEL_NO]);
	
			$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='".$PM_CODE."' ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$PM_NAME = $data_dpis2[PM_NAME];
	
			$cmd = " select PL_NAME from PER_LINE where trim(PL_CODE)='".$PL_CODE."' ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$PL_NAME = $data_dpis2[PL_NAME];
	
			$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE)='".$PT_CODE."' ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$PT_NAME = $data_dpis2[PT_NAME];
			$ORG_REF_NAME = "";
			if($ORG_ID_REF){
				$cmd = " select ORG_NAME, ORG_SHORT from PER_ORG where OL_CODE='02' and ORG_ID=$ORG_ID_REF ";
				$db_dpis2->send_cmd($cmd);
				$data_dpis2 = $db_dpis2->get_array();
				if ($BKK_FLAG==1) $ORG_REF_NAME = trim($data_dpis2[ORG_SHORT]);
				if (!$ORG_REF_NAME) $ORG_REF_NAME = trim($data_dpis2[ORG_NAME]);
			}
			$ORG_NAME_1 = "";
			if($ORG_ID_1){
				$cmd = " select ORG_NAME, ORG_SHORT from PER_ORG where OL_CODE='04' and ORG_ID=$ORG_ID_1 ";
				$db_dpis2->send_cmd($cmd);
				$data_dpis2 = $db_dpis2->get_array();
				if ($BKK_FLAG==1) $ORG_NAME_1 = trim($data_dpis2[ORG_SHORT]);
				if (!$ORG_NAME_1) $ORG_NAME_1 = trim($data_dpis2[ORG_NAME]);
				if ($ORG_NAME_1=="-") $ORG_NAME_1 = "";
			}
			$ORG_NAME_2 = "";
			if($ORG_ID_2){
				$cmd = " select ORG_NAME, ORG_SHORT from PER_ORG where OL_CODE='05' and ORG_ID=$ORG_ID_2 ";
				$db_dpis2->send_cmd($cmd);
				$data_dpis2 = $db_dpis2->get_array();
				if ($BKK_FLAG==1) $ORG_NAME_2 = trim($data_dpis2[ORG_SHORT]);
				if (!$ORG_NAME_2) $ORG_NAME_2 = trim($data_dpis2[ORG_NAME]);
				if ($ORG_NAME_2=="-") $ORG_NAME_2 = "";
			}
			if (!$LEVEL_NO) {
				$cmd = " select LEVEL_NO_MIN from PER_CO_LEVEL where trim(CL_NAME)='$CL_NAME' ";
				$db_dpis2->send_cmd($cmd);
				$data_dpis2 = $db_dpis2->get_array();
				$LEVEL_NO = $data_dpis2[LEVEL_NO_MIN];
			}
		
			$cmd = "select LEVEL_NAME, POSITION_TYPE from PER_LEVEL where LEVEL_NO='$LEVEL_NO'";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$LEVEL_NAME = $data_dpis2[LEVEL_NAME];
			$NEW_POSITION_TYPE = $data_dpis2[POSITION_TYPE];
		
			if($DPISDB=="odbc"){
				$cmd = " select 	b.PN_NAME, a.PER_NAME, a.PER_SURNAME 
								 from 		PER_PERSONAL a
												left join PER_PRENAME b on (a.PN_CODE=b.PN_CODE)
								 where	a.POS_ID=$POS_ID and a.PER_STATUS=1 and a.PER_TYPE=1 ";
			}elseif($DPISDB=="oci8"){
				$cmd = " select 	b.PN_NAME, a.PER_NAME, a.PER_SURNAME 
								 from 		PER_PERSONAL a, PER_PRENAME b
								 where	a.PN_CODE=b.PN_CODE(+) and a.POS_ID=$POS_ID and a.PER_STATUS=1 and a.PER_TYPE=1 ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select 	b.PN_NAME, a.PER_NAME, a.PER_SURNAME 
								 from 		PER_PERSONAL a
												left join PER_PRENAME b on (a.PN_CODE=b.PN_CODE)
								 where	a.POS_ID=$POS_ID and a.PER_STATUS=1 and a.PER_TYPE=1 ";
			}
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$POS_PERSON = "$data_dpis2[PN_NAME]$data_dpis2[PER_NAME] $data_dpis2[PER_SURNAME]";
			if($data_dpis2[PER_ID]) $POS_PERSON .= (($data_dpis2[PER_STATUS]==0)?" (รอบรรจุ)":"");
	
			if ($MUNICIPALITY_FLAG==1) $POS_NO = pos_no_format($POS_NO,2);
			$arr_data = (array) null;
			$arr_data[] = (($NUMBER_DISPLAY==2)?convert2thaidigit("$POS_NO"):"$POS_NO");
			$arr_data[] = "$PM_NAME";
			$arr_data[] = "$PL_NAME";
			$arr_data[] = "$CL_NAME";
			if ($MUNICIPALITY_FLAG==1)	$arr_data[] = "$PT_NAME";
			$arr_data[] = "$NEW_POSITION_TYPE";
			$arr_data[] = "$ORG_REF_NAME";
			$arr_data[] = "$ORG_NAME";
			$arr_data[] = "$ORG_NAME_1|$ORG_NAME_2";
			$arr_data[] = (($NUMBER_DISPLAY==2)?convert2thaidigit($POS_PERSON):$POS_PERSON);
			$arr_data[] = "$POS_REMARK";
			$arr_data[] = "<*img*".(($data[POS_STATUS]==1)?"../images/checkbox_check.jpg":"../images/checkbox_blank.jpg")."*img*>";

			if ($MUNICIPALITY_FLAG==1)
				$data_align = array("C", "L", "L", "C", "C", "C", "L", "L", "L", "L", "L", "C");
			else
				$data_align = array("C", "L", "L", "C", "C", "L", "L", "L", "L", "L", "C");
			if ($FLAG_RTF)
				$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
			else
				$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";

		} // end while
	}else{
		$arr_data = (array) null;
		$arr_data[] = "<**1**>********** ไม่มีข้อมูล **********";
		$arr_data[] = "<**1**>********** ไม่มีข้อมูล **********";
		$arr_data[] = "<**1**>********** ไม่มีข้อมูล **********";
		$arr_data[] = "<**1**>********** ไม่มีข้อมูล **********";
		$arr_data[] = "<**1**>********** ไม่มีข้อมูล **********";
		$arr_data[] = "<**1**>********** ไม่มีข้อมูล **********";
		$arr_data[] = "<**1**>********** ไม่มีข้อมูล **********";
		$arr_data[] = "<**1**>********** ไม่มีข้อมูล **********";
		$arr_data[] = "<**1**>********** ไม่มีข้อมูล **********";
		$arr_data[] = "<**1**>********** ไม่มีข้อมูล **********";
		$arr_data[] = "<**1**>********** ไม่มีข้อมูล **********";
		if ($MUNICIPALITY_FLAG==1) {
			$arr_data[] = "<**1**>********** ไม่มีข้อมูล **********";
			$data_align = array("C", "C", "C", "C", "C", "C", "C", "C", "C", "C", "C", "C");
		} else {
			$data_align = array("C", "C", "C", "C", "C", "C", "C", "C", "C", "C", "C");
		}
		if ($FLAG_RTF)
			$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "b", "16", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
		else	
			$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "b", "16", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
	} // end if
	 if ($FLAG_RTF) {
		$RTF->close_tab(); 
//			$RTF->close_section(); 
		$RTF->display($fname);
	} else {
		$pdf->close_tab(""); 
		$pdf->close();
		$pdf->Output();	
	}

	ini_set("max_execution_time", 30);
?>