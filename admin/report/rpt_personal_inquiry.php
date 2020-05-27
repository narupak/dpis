<?php
	include("../../php_scripts/connect_database.php");
	include("../php_scripts/pdf_wordarray_thaicut.php");
	include ("../php_scripts/function_share.php");
	
	include ("rpt_personal_inquiry_format.php");

	if ($FLAG_RTF) {
		include ("rtf_setvar.php");	// กำหนดตัวแปรค่าสี set_of_colors
		require("../../RTF/rtf_class.php");
	} else	 {
		define('FPDF_FONTPATH','../../PDF/font/');
		include ("../../PDF/fpdf.php");
		include ("../../PDF/pdf_extends_DPIS.php");
	}

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	$report_title = trim($report_title);
	$report_code = "";

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();
	if ($FLAG_RTF) {
//		$sum_w = array_sum($heading_width);
		$sum_w = 0;
		for($h = 0; $h < count($heading_width); $h++) {
			if ($arr_column_sel[$h]==1)
				$sum_w += $heading_width[$h];
		}
		for($h = 0; $h < count($heading_width); $h++) {
			if ($arr_column_sel[$h]==1)
			$heading_width[$h] = $heading_width[$h] / $sum_w * 100;
		}
		$fname= "rpt_personal_inquiry.rtf";
	//	RTF $papersize = "a4", $margl = 720, $margr = 720, $margt = 720, $margb = 720, $orient = "p", $u_color_tab="";
		$paper_size="a4";
		$orientation='P';
		$RTF = new RTF($paper_size, ".7in", ".3in", ".5in", ".1in", $orientation, $set_of_colors);
		$RTF->set_default_font($font, 14);
	//	echo "default font_id::".$RTF->dfl_FontID."<br>";	

		$RTF->set_report_code($report_code);
		$RTF->set_report_title($report_title);
		$RTF->set_company_name($company_name);
	} else {
		$unit="mm";
		$paper_size="A4";
		$lang_code="TH";
		$company_name = "";
		$orientation='P';
		$pdf = new PDF($orientation,$unit,$paper_size,$lang_code,$company_name,$report_title,$report_code,$heading,$heading_width,$heading_align);
		$pdf->Open();
		$pdf->SetMargins(5,5,5);
		$pdf->AliasNbPages();
		$pdf->AddPage();
		$pdf->SetTextColor(0, 0, 0);
		$pdf->SetFont($font,'',14);
		$pdf->SetAutoPageBreak(true,10);
	}
/*	$heading_width[0] = "20";
	$heading_width[1] = "50";
	$heading_width[2] = "60";
	$heading_width[3] = "70";
	if (!$font) $font ="AngsanaUPC";
	if ($FLAG_RTF) {
		function print_header(){
			global $RTF, $heading_width, $ORG_TITLE;
			$RTF->set_font($font, 14);
			$RTF->Color(0); // ระบบสีของ RTF จำกัดมาก กำหนดไม่ได้ ลองใช้เลข 0 - 32 ทดสอบสีดู
	
			$RTF->open_line();			
			$RTF->cell("เลขที่ตำแหน่ง", "25", "left", "0"); // $fill_color ลองใช้สีเลข 0-32
			$RTF->cell("ชื่อ - นามสกุล", "25", "left", "0");
			$RTF->cell("ตำแหน่ง", "25", "left", "0");
			$RTF->cell("$ORG_TITLE", "25", "left", "0");
			//$RTF->cell((($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($INCOME_01,2)):number_format($INCOME_01, 2)), "15", "right", "0");
			//$RTF->cell("บาท.", "5", "right", "0");
			$RTF->close_line();

	     } // function
	}else{	
		function print_header(){
			global $pdf, $heading_width, $ORG_TITLE;
			
			$pdf->SetFont($font,'',14);
			$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
			$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
			$pdf->Cell(100,2,"",0,1,'C');
	
			$pdf->Cell($heading_width[0] ,7,"เลขที่ตำแหน่ง",'LTBR',0,'C',1);
			$pdf->Cell($heading_width[1] ,7,"ชื่อ - นามสกุล",'LTBR',0,'C',1);
			$pdf->Cell($heading_width[2] ,7,"ตำแหน่ง",'LTBR',0,'C',1);
			$pdf->Cell($heading_width[3] ,7,"$ORG_TITLE",'LTBR',1,'C',1);
		} // function		
     }	//end else
*/
	if(!isset($search_per_type)) $search_per_type = 1;
	if(!isset($search_per_status) && $command != "SEARCH") $search_per_status = array(1);
	if(!isset($search_per_gender) && $command != "SEARCH") $search_per_gender = array(1, 2);
	if(!isset($search_per_ordain) && $command != "SEARCH") $search_per_ordain = array(0, 1);
	if(!isset($search_per_soldier) && $command != "SEARCH") $search_per_soldier = array(0, 1);
	if ($BKK_FLAG!=1) if(!isset($search_per_member) && $command != "SEARCH") $search_per_member = array(0, 1);
	if(!isset($search_per_punishment) && $command != "SEARCH") $search_per_punishment = array(0, 1);
	if(!isset($search_per_invest) && $command != "SEARCH") $search_per_invest = array(0, 1);
	if ($BKK_FLAG!=1) if(!isset($search_hip_flag) && $command != "SEARCH") $search_hip_flag = array(0, 1, 2, 3, 4, 5, 6);
	if(!isset($EDU_TYPE) && $command != "SEARCH") $EDU_TYPE = array(1, 2, 3, 4, 5);
	
  	if($search_department_id){
		$arr_search_condition[] = "(a.DEPARTMENT_ID = $search_department_id)";
	}elseif($search_ministry_id){
		unset($arr_department);
		$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$search_ministry_id and OL_CODE='02' ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_department[] = $data[ORG_ID];
		$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode(",", $arr_department) ."))";
	}elseif($PROVINCE_CODE){
		$cmd = " select ORG_ID_REF from PER_ORG where PV_CODE='$PROVINCE_CODE' and OL_CODE='03' ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_department[] = $data[ORG_ID_REF];
		$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode(",", $arr_department) ."))";
	} // end if
	
	/* ================= 	ข้อมูลทั่วไป    ===================== */
        if($search_per_disability){$arr_search_condition[] = "(a.per_disability in (". implode(",", $search_per_disability) ."))";}
  	if(trim($search_per_name)) $arr_search_condition[] = "(a.PER_NAME like '$search_per_name%' or UPPER(a.PER_ENG_NAME) like UPPER('$search_per_name%'))";
  	if(trim($search_per_surname)) $arr_search_condition[] = "(a.PER_SURNAME like '$search_per_surname%' or UPPER(a.PER_ENG_SURNAME) like UPPER('$search_per_surname%'))";
	if(trim($search_pn_code)) $arr_search_condition[] = "(a.PN_CODE='$search_pn_code')";
	if(trim($search_mr_code)) $arr_search_condition[] = "(a.MR_CODE='$search_mr_code')";
  	if(trim($search_per_cardno)) $arr_search_condition[] = "(a.PER_CARDNO like '$search_per_cardno%')";
  	if(trim($search_per_offno)) $arr_search_condition[] = "(a.PER_OFFNO like '$search_per_offno%')";
	if(trim($search_per_type)) $arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	if(trim($search_per_blood)) $arr_search_condition[] = "(trim(a.PER_BLOOD) = '$search_per_blood')";
	if(trim($search_re_code)) $arr_search_condition[] = "(a.RE_CODE='$search_re_code')";
	if(trim($search_pv_code)) $arr_search_condition[] = "(a.PV_CODE='$search_pv_code')";
	if(trim($search_es_code)) $arr_search_condition[] = "(a.ES_CODE='$search_es_code')";
  	if(trim($search_per_file_no)) $arr_search_condition[] = "(a.PER_FILE_NO='$search_per_file_no')";
  	if(trim($search_per_cooperative_no)) $arr_search_condition[] = "(a.PER_COOPERATIVE_NO like '$search_per_cooperative_no%')";
	if(trim($search_per_birthdate_min)){
		$search_per_birthdate_min =  save_date($search_per_birthdate_min);
		if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(a.PER_BIRTHDATE, 10) >= '$search_per_birthdate_min')";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(a.PER_BIRTHDATE, 1, 10) >= '$search_per_birthdate_min')";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(a.PER_BIRTHDATE, 10) >= '$search_per_birthdate_min')";
		$search_per_birthdate_min = show_date_format($search_per_birthdate_min, 1);
	} // end if
	if(trim($search_per_birthdate_max)){
		$search_per_birthdate_max =  save_date($search_per_birthdate_max);
		if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(a.PER_BIRTHDATE, 10) <= '$search_per_birthdate_max')";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(a.PER_BIRTHDATE, 1, 10) <= '$search_per_birthdate_max')";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(a.PER_BIRTHDATE, 10) <= '$search_per_birthdate_max')";
		$search_per_birthdate_max = show_date_format($search_per_birthdate_max, 1);
	} // end if
	if(trim($search_per_age_min)){
//			คิดตามอายุจริง (ถ้ายังไม่ถึงวันเกิดจะไม่นับเป็นปี)
		if ($search_per_age_date) $per_age_date = save_date($search_per_age_date,1);
		else $per_age_date = date("Y-m-d");
		$birthdate_min = date_adjust($per_age_date, "y", ($search_per_age_min * -1));
		if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(trim(a.PER_BIRTHDATE), 10) <= '$birthdate_min')";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) <= '$birthdate_min')";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(trim(a.PER_BIRTHDATE), 10) <= '$birthdate_min')";

//			คิดเฉพาะปีเกิด กับปีปัจจุบัน
//			$birthyear_min = date("Y") - $search_per_age_min;
//			if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(trim(a.PER_BIRTHDATE), 4) >= '$birthyear_min')";
//			elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(trim(a.PER_BIRTHDATE), 1, 4) >= '$birthyear_min')";
//			elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(trim(a.PER_BIRTHDATE), 4) >= '$birthyear_min')";
	} // end if
	if(trim($search_per_age_max)){
//			คิดตามอายุจริง (ถ้ายังไม่ถึงวันเกิดจะไม่นับเป็นปี)
		if ($search_per_age_date) $per_age_date = save_date($search_per_age_date,1);
		else $per_age_date = date("Y-m-d");
		$birthdate_max = date_adjust($per_age_date, "y", ($search_per_age_max * -1));
		if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(trim(a.PER_BIRTHDATE), 10) >= '$birthdate_max')";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) >= '$birthdate_max')";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(trim(a.PER_BIRTHDATE), 10) >= '$birthdate_max')";

//			คิดเฉพาะปีเกิด กับปีปัจจุบัน
//			$birthyear_max = date("Y") - $search_per_age_max;
//			if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(trim(a.PER_BIRTHDATE), 4) <= '$birthyear_max')";
//			elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(trim(a.PER_BIRTHDATE), 1, 4) <= '$birthyear_max')";
//			elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(trim(a.PER_BIRTHDATE), 4) <= '$birthyear_max')";
	} // end if

		if ($search_service_age_min || $search_service_age_max){
			if($search_service_age_min == $search_service_age_max) $search_service_age_max += 1;
			if($search_service_age_min){
				if ($search_service_age_date) $service_age_date = save_date($search_service_age_date,1);
				else $service_age_date = date("Y-m-d");
				$startdate_min = date_adjust($service_age_date, "y", ($search_service_age_min * -1));
				if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(trim(a.PER_STARTDATE), 10) <= '$startdate_min')";
				elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(trim(a.PER_STARTDATE), 1, 10) <= '$startdate_min')";
				elseif($DPISDB=="mysql") $arr_search_condition[] = "(SUBSTRING(trim(a.PER_STARTDATE), 1, 10) <= '$startdate_min')";
			} // end if
			if($search_service_age_max){
				if ($search_service_age_date) $service_age_date = save_date($search_service_age_date,1);
				else $service_age_date = date("Y-m-d");
				$startdate_max = date_adjust($service_age_date, "y", ($search_service_age_max * -1));
				if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(trim(a.PER_STARTDATE), 10) >= '$startdate_max')";
				elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(trim(a.PER_STARTDATE), 1, 10) >= '$startdate_max')";
				elseif($DPISDB=="mysql") $arr_search_condition[] = "(SUBSTRING(trim(a.PER_STARTDATE), 1, 10) >= '$startdate_max')";
			} // end if
		} // end if
	
  	if(trim($search_per_taxno)) $arr_search_condition[] = "(a.PER_TAXNO like '$search_per_taxno%')";
  	if(trim($search_per_pos_orgmgt)) $arr_search_condition[] = "(a.PER_POS_ORGMGT like '%$search_per_pos_orgmgt%')";
	if(trim($search_ot_code)) $arr_search_condition[] = "(a.OT_CODE='$search_ot_code')";
	if(trim($search_per_startdate_min)){
		$search_per_startdate_min =  save_date($search_per_startdate_min);
		if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(a.PER_STARTDATE, 10) >= '$search_per_startdate_min')";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(a.PER_STARTDATE, 1, 10) >= '$search_per_startdate_min')";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(a.PER_STARTDATE, 10) >= '$search_per_startdate_min')";
		$search_per_startdate_min = show_date_format($search_per_startdate_min, 1);
	} // end if
	if(trim($search_per_startdate_max)){
		$search_per_startdate_max =  save_date($search_per_startdate_max);
		if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(a.PER_STARTDATE, 10) <= '$search_per_startdate_max')";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(a.PER_STARTDATE, 1, 10) <= '$search_per_startdate_max')";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(a.PER_STARTDATE, 10) <= '$search_per_startdate_max')";
		$search_per_startdate_max = show_date_format($search_per_startdate_max, 1);
	} // end if
	if(trim($search_per_occupydate_min)){
		$search_per_occupydate_min =  save_date($search_per_occupydate_min);
		if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(a.PER_OCCUPYDATE, 10) >= '$search_per_occupydate_min')";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(a.PER_OCCUPYDATE, 1, 10) >= '$search_per_occupydate_min')";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(a.PER_OCCUPYDATE, 10) >= '$search_per_occupydate_min')";
		$search_per_occupydate_min = show_date_format($search_per_occupydate_min, 1);
	} // end if
	if(trim($search_per_occupydate_max)){
		$search_per_occupydate_max =  save_date($search_per_occupydate_max);
		if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(a.PER_OCCUPYDATE, 10) <= '$search_per_occupydate_max')";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(a.PER_OCCUPYDATE, 1, 10) <= '$search_per_occupydate_max')";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(a.PER_OCCUPYDATE, 10) <= '$search_per_occupydate_max')";
		$search_per_occupydate_max = show_date_format($search_per_occupydate_max, 1);
	} // end if
	if(count($search_per_punishment)){
		$cmd = " select distinct PER_ID from PER_PUNISHMENT order by PER_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		while($data = $db_dpis->get_array()) $arr_punishment[] = $data[PER_ID];
		
		if(in_array(1, $search_per_punishment) && !in_array(0, $search_per_punishment)){
			// เคยรับโทษ
			$arr_search_condition[] = "(a.PER_ID in (". implode(",", $arr_punishment) ."))";
		}elseif(!in_array(1, $search_per_punishment) && in_array(0, $search_per_punishment)){
			// ไม่เคยรับโทษ
			$arr_search_condition[] = "(a.PER_ID not in (". implode(",", $arr_punishment) ."))";
		} // end if
//	}else{
//		$arr_search_condition[] = "(a.PER_ID in ())";
	} // end if
	
	if(count($search_per_invest)){
		$cmd = " select distinct PER_ID from PER_INVEST2DTL where PEN_CODE IS NULL order by PER_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		while($data = $db_dpis->get_array()) $arr_investigate[] = $data[PER_ID];

		if(in_array(1, $search_per_invest) && !in_array(0, $search_per_invest)){
			// ปัจจุบันกำลังถูกสอบสวน
			$arr_search_condition[] = "(a.PER_ID in (". implode(",", $arr_investigate) ."))";
		}elseif(!in_array(1, $search_per_invest) && in_array(0, $search_per_invest)){
			// ปัจจุบันไม่อยู่ระหว่างการสอบสวน
			$arr_search_condition[] = "(a.PER_ID not in (". implode(",", $arr_investigate) ."))";
		} // end if
//	}else{
//		$arr_search_condition[] = "(a.PER_ID in ())";
	} // end if
	if(trim($search_al_code)){
		$cmd = " select distinct PER_ID from PER_ABILITY where AL_CODE='$search_al_code' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		
		$count = 0;
		while($data = $db_dpis->get_array()) { 
			$count++;
			if ($count < 1000) $arr_ability[] = $data[PER_ID];
			else $arr_ability2[] = $data[PER_ID];
		}
		
		if (count($arr_ability2)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_ability) .")) or (a.PER_ID in (". implode(",", $arr_ability2) .")))";
		else $arr_search_condition[] = "(a.PER_ID in (". implode(",", $arr_ability) ."))";
	} // end if

	if(trim($search_ss_code)){
		$cmd = " select distinct PER_ID from PER_SPECIAL_SKILL where SS_CODE='$search_ss_code' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		
		$count = 0;
		while($data = $db_dpis->get_array()) { 
			$count++;
			if ($count < 1000) $arr_special_skill[] = $data[PER_ID];
			else $arr_special_skill2[] = $data[PER_ID];
		}
		
		if (count($arr_special_skill2)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_special_skill) .")) or (a.PER_ID in (". implode(",", $arr_special_skill2) .")))";
		else $arr_search_condition[] = "(a.PER_ID in (". implode(",", $arr_special_skill) ."))";
	} // end if

	if(trim($search_sps_emphasize)){
		$cmd = " select distinct PER_ID from PER_SPECIAL_SKILL where SPS_EMPHASIZE like '%$search_sps_emphasize%' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		
		$count = 0;
		while($data = $db_dpis->get_array()) { 
			$count++;
			if ($count < 1000) $arr_sps_emphasize[] = $data[PER_ID];
			else $arr_sps_emphasize2[] = $data[PER_ID];
		}
		
		if (count($arr_sps_emphasize2)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_sps_emphasize) .")) or (a.PER_ID in (". implode(",", $arr_sps_emphasize2) .")))";
		else $arr_search_condition[] = "(a.PER_ID in (". implode(",", $arr_sps_emphasize) ."))";
	} // end if

	if ($BKK_FLAG!=1) { 
		foreach($search_hip_flag as $hip_flag) if ($hip_flag == '0') $hip_all = "Y";
		if ($hip_all != 'Y') {
			foreach($search_hip_flag as $hip_flag) $arr_search_hip_flag[] = "(a.PER_HIP_FLAG like '%$hip_flag%')";
			$arr_search_condition[] = "(".implode(" or ", $arr_search_hip_flag).")";
		}
	}

	if($search_per_audit_absent_flag)	$arr_search_condition[] = "(a.PER_AUDIT_FLAG =1)";
	if($search_per_probation_flag)	$arr_search_condition[] = "(a.PER_PROBATION_FLAG =1)";
        if($search_per_renew)	$arr_search_condition[] = "(a.PER_RENEW =1)";
	$arr_search_condition[] = "(a.PER_GENDER in (". implode(",", $search_per_gender) ."))";
	$arr_search_condition[] = "(a.PER_STATUS in (". implode(",", $search_per_status) ."))";
	$arr_search_condition[] = "(a.PER_ORDAIN in (". implode(",", $search_per_ordain) ."))";
	$arr_search_condition[] = "(a.PER_SOLDIER in (". implode(",", $search_per_soldier) ."))";
	if ($BKK_FLAG!=1) $arr_search_condition[] = "(a.PER_MEMBER in (". implode(",", $search_per_member) ."))";

	/* ================= 	ตำแหน่งปัจจุบัน    ===================== */
	if(trim($search_pos_change_date_min)){
		$search_pos_change_date_min =  save_date($search_pos_change_date_min);
		if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(b.POS_CHANGE_DATE, 10) >= '$search_pos_change_date_min')";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(b.POS_CHANGE_DATE, 1, 10) >= '$search_pos_change_date_min')";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(b.POS_CHANGE_DATE, 10) >= '$search_pos_change_date_min')";
		$search_pos_change_date_min = show_date_format($search_pos_change_date_min, 1);
	} // end if
	if(trim($search_pos_change_date_max)){
		$search_pos_change_date_max =  save_date($search_pos_change_date_max);
		if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(b.POS_CHANGE_DATE, 10) <= '$search_pos_change_date_max')";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(b.POS_CHANGE_DATE, 1, 10) <= '$search_pos_change_date_max')";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(b.POS_CHANGE_DATE, 10) <= '$search_pos_change_date_max')";
		$search_pos_change_date_max = show_date_format($search_pos_change_date_max, 1);
	} // end if
  	if(trim($search_pos_no_name))  {	
		if ($search_per_type == 1 || $search_per_type == 5)
			$arr_search_condition[] = "(b.POS_NO_NAME like '$search_pos_no_name%')";
		elseif ($search_per_type == 2) 
			$arr_search_condition[] = "(c.POEM_NO_NAME like '$search_pos_no_name%')";		
		elseif ($search_per_type == 3) 	
			$arr_search_condition[] = "(d.POEMS_NO_NAME like '$search_pos_no_name%')";
		elseif ($search_per_type == 4) 	
			$arr_search_condition[] = "(e.POT_NO_NAME like '$search_pos_no_name%')";			
	}
  	if(trim($search_pos_no))  {	
		if ($search_per_type == 1)
			$arr_search_condition[] = "(b.POS_NO like '$search_pos_no%')";
		elseif ($search_per_type == 2) 
			$arr_search_condition[] = "(c.POEM_NO like '$search_pos_no%')";		
		elseif ($search_per_type == 3) 	
			$arr_search_condition[] = "(d.POEMS_NO like '$search_pos_no%')";
		elseif ($search_per_type == 4) 	
			$arr_search_condition[] = "(e.POT_NO like '$search_pos_no%')";			
	}
	if(trim($search_pl_code)){
		if($search_per_type == 1 || $search_per_type == 5)
			$arr_search_condition[] = "(b.PL_CODE = '$search_pl_code')";
		elseif($search_per_type == 2)
			$arr_search_condition[] = "(c.PN_CODE = '$search_pl_code')";
		elseif($search_per_type == 3)
			$arr_search_condition[] = "(d.EP_CODE = '$search_pl_code')";
		elseif($search_per_type == 4)
			$arr_search_condition[] = "(e.TP_CODE = '$search_pl_code')";
	} // end if
	if(trim($search_pm_code)) $arr_search_condition[] = "(b.PM_CODE = '$search_pm_code')";
//	if(trim($search_pt_code)) $arr_search_condition[] = "(b.PT_CODE = '$search_pt_code')";
//	if(trim($search_level_no_min)) $arr_search_condition[] = "(trim(a.LEVEL_NO) >= trim('$search_level_no_min'))";
//	if(trim($search_level_no_max)) $arr_search_condition[] = "(trim(a.LEVEL_NO) <= trim('$search_level_no_max'))";
//  อ่านจาก search_level_no_min[] ที่เป็น multiselection
	$arr_level_no_condi = (array) null;
	foreach ($search_level_no_min as $search_level_no)
	{
        if ($search_level_no) $arr_level_no_condi[] = "'$search_level_no'";
//		echo "search_level_no=$search_level_no<br>";
	}
	if (count($arr_level_no_condi) > 0) $arr_search_condition[] = "(trim(a.LEVEL_NO) in (".implode(",",$arr_level_no_condi)."))";
//	echo "arr_level_no_condi = (".implode(",",$arr_level_no_condi).")<br>";
	if(trim($search_per_salary_min)) $arr_search_condition[] = "(a.PER_SALARY >= $search_per_salary_min)";
	if(trim($search_per_salary_max)) $arr_search_condition[] = "(a.PER_SALARY <= $search_per_salary_max)";
	if(trim($search_mov_code)){
		$cmd = " select 	distinct a.PER_ID 
						 from 		PER_PERSONAL a, PER_POSITIONHIS b
						 where	a.PER_ID=b.PER_ID and (trim(a.MOV_CODE) = trim('$search_mov_code') or trim(b.MOV_CODE) = trim('$search_mov_code')) ";
		$db_dpis->send_cmd($cmd);

		$count = 0;
		while($data = $db_dpis->get_array()) { 
			$count++;
			if ($count < 1000) $arr_movement[] = $data[PER_ID];
			elseif ($count < 2000) $arr_movement2[] = $data[PER_ID];
			elseif ($count < 3000) $arr_movement3[] = $data[PER_ID];
			elseif ($count < 4000) $arr_movement4[] = $data[PER_ID];
			elseif ($count < 5000) $arr_movement5[] = $data[PER_ID];
			else $arr_movement6[] = $data[PER_ID];
		}
		
		if (count($arr_movement6)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_movement) .")) or (a.PER_ID in (". implode(",", $arr_movement2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_movement3) .")) or (a.PER_ID in (". implode(",", $arr_movement4) .")) or 
																															(a.PER_ID in (". implode(",", $arr_movement5) .")) or (a.PER_ID in (". implode(",", $arr_movement6) .")))";
		elseif (count($arr_movement5)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_movement) .")) or (a.PER_ID in (". implode(",", $arr_movement2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_movement3) .")) or (a.PER_ID in (". implode(",", $arr_movement4) .")) or 
																															(a.PER_ID in (". implode(",", $arr_movement5) .")))";
		elseif (count($arr_movement4)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_movement) .")) or (a.PER_ID in (". implode(",", $arr_movement2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_movement3) .")) or (a.PER_ID in (". implode(",", $arr_movement4) .")))";
		elseif (count($arr_movement3)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_movement) .")) or (a.PER_ID in (". implode(",", $arr_movement2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_movement3) .")))";
		elseif (count($arr_movement2)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_movement) .")) or (a.PER_ID in (". implode(",", $arr_movement2) .")))";
		else $arr_search_condition[] = "(a.PER_ID in (". implode(",", $arr_movement) ."))";
	} // end if

	if(trim($search_cur_poh_docno)){
		$cmd = " select distinct PER_ID from PER_POSITIONHIS where trim(POH_DOCNO)='$search_cur_poh_docno' ";
		$db_dpis->send_cmd($cmd);

		$count = 0;
		while($data = $db_dpis->get_array()) { 
			$count++;
			if ($count < 1000) $arr_cur_docno[] = $data[PER_ID];
			elseif ($count < 2000) $arr_cur_docno2[] = $data[PER_ID];
			elseif ($count < 3000) $arr_cur_docno3[] = $data[PER_ID];
			elseif ($count < 4000) $arr_cur_docno4[] = $data[PER_ID];
			elseif ($count < 5000) $arr_cur_docno5[] = $data[PER_ID];
			else $arr_cur_docno6[] = $data[PER_ID];
		}
		
		if (count($arr_cur_docno6)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_cur_docno) .")) or (a.PER_ID in (". implode(",", $arr_cur_docno2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_cur_docno3) .")) or (a.PER_ID in (". implode(",", $arr_cur_docno4) .")) or 
																															(a.PER_ID in (". implode(",", $arr_cur_docno5) .")) or (a.PER_ID in (". implode(",", $arr_cur_docno6) .")))";
		elseif (count($arr_cur_docno5)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_cur_docno) .")) or (a.PER_ID in (". implode(",", $arr_cur_docno2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_cur_docno3) .")) or (a.PER_ID in (". implode(",", $arr_cur_docno4) .")) or 
																															(a.PER_ID in (". implode(",", $arr_cur_docno5) .")))";
		elseif (count($arr_cur_docno4)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_cur_docno) .")) or (a.PER_ID in (". implode(",", $arr_cur_docno2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_cur_docno3) .")) or (a.PER_ID in (". implode(",", $arr_cur_docno4) .")))";
		elseif (count($arr_cur_docno3)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_cur_docno) .")) or (a.PER_ID in (". implode(",", $arr_cur_docno2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_cur_docno3) .")))";
		elseif (count($arr_cur_docno2)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_cur_docno) .")) or (a.PER_ID in (". implode(",", $arr_cur_docno2) .")))";
		else $arr_search_condition[] = "(a.PER_ID in (". implode(",", $arr_cur_docno) ."))";
	} 

	if(trim($search_pos_ot_code)) {
		$cmd = " select 	distinct a.PER_ID 
						 from 		PER_PERSONAL a, PER_POSITION b, PER_ORG c
						 where	a.POS_ID=b.POS_ID and b.ORG_ID = c.ORG_ID and c.OT_CODE='$search_pos_ot_code' ";
		$db_dpis->send_cmd($cmd);

		$count = 0;
		while($data = $db_dpis->get_array()) { 
			$count++;
			if ($count < 1000) $arr_ot_code[] = $data[PER_ID];
			elseif ($count < 2000) $arr_ot_code2[] = $data[PER_ID];
			elseif ($count < 3000) $arr_ot_code3[] = $data[PER_ID];
			elseif ($count < 4000) $arr_ot_code4[] = $data[PER_ID];
			elseif ($count < 5000) $arr_ot_code5[] = $data[PER_ID];
			else $arr_ot_code6[] = $data[PER_ID];
		}
		
		if (count($arr_ot_code6)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_ot_code) .")) or (a.PER_ID in (". implode(",", $arr_ot_code2) .")) or 
																														(a.PER_ID in (". implode(",", $arr_ot_code3) .")) or (a.PER_ID in (". implode(",", $arr_ot_code4) .")) or 
																														(a.PER_ID in (". implode(",", $arr_ot_code5) .")) or (a.PER_ID in (". implode(",", $arr_ot_code6) .")))";
		elseif (count($arr_ot_code5)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_ot_code) .")) or (a.PER_ID in (". implode(",", $arr_ot_code2) .")) or 
																														(a.PER_ID in (". implode(",", $arr_ot_code3) .")) or (a.PER_ID in (". implode(",", $arr_ot_code4) .")) or 
																														(a.PER_ID in (". implode(",", $arr_ot_code5) .")))";
		elseif (count($arr_ot_code4)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_ot_code) .")) or (a.PER_ID in (". implode(",", $arr_ot_code2) .")) or 
																														(a.PER_ID in (". implode(",", $arr_ot_code3) .")) or (a.PER_ID in (". implode(",", $arr_ot_code4) .")))";
		elseif (count($arr_ot_code3)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_ot_code) .")) or (a.PER_ID in (". implode(",", $arr_ot_code2) .")) or 
																														(a.PER_ID in (". implode(",", $arr_ot_code3) .")))";
		elseif (count($arr_ot_code2)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_ot_code) .")) or (a.PER_ID in (". implode(",", $arr_ot_code2) .")))";
		else $arr_search_condition[] = "(a.PER_ID in (". implode(",", $arr_ot_code) ."))";
	}

	if(trim($search_org_id)){ 
		if($SESS_ORG_STRUCTURE==1 || $select_org_structure==1){	
			$arr_search_condition[] = "(a.ORG_ID = $search_org_id)";
		}else{	
			if($search_per_type == 1 || $search_per_type == 5)
				$arr_search_condition[] = "(b.ORG_ID = $search_org_id)";
			elseif($search_per_type == 2)
				$arr_search_condition[] = "(c.ORG_ID = $search_org_id)";
			elseif($search_per_type == 3)
				$arr_search_condition[] = "(d.ORG_ID = $search_org_id)";
			elseif($search_per_type == 4)
				$arr_search_condition[] = "(e.ORG_ID = $search_org_id)";
		}
	} // end if
	if(trim($search_org_id_1)){ 
		if($SESS_ORG_STRUCTURE==1 || $select_org_structure==1){	
			$arr_search_condition[] = "(a.ORG_ID_1 = $search_org_id_1)";
		}else{	
			if($search_per_type == 1 || $search_per_type == 5)
				$arr_search_condition[] = "(b.ORG_ID_1 = $search_org_id_1)";
			elseif($search_per_type == 2)
				$arr_search_condition[] = "(c.ORG_ID_1 = $search_org_id_1)";
			elseif($search_per_type == 3)
				$arr_search_condition[] = "(d.ORG_ID_1 = $search_org_id_1)";
			elseif($search_per_type == 4)
				$arr_search_condition[] = "(e.ORG_ID_1 = $search_org_id_1)";
		}
	} // end if
	if(trim($search_org_id_2)){ 
		if($SESS_ORG_STRUCTURE==1 || $select_org_structure==1){	
			$arr_search_condition[] = "(a.ORG_ID_2 = $search_org_id_2)";
		}else{	
			if($search_per_type == 1 || $search_per_type == 5)
				$arr_search_condition[] = "(b.ORG_ID_2 = $search_org_id_2)";
			elseif($search_per_type == 2)
				$arr_search_condition[] = "(c.ORG_ID_2 = $search_org_id_2)";
			elseif($search_per_type == 3)
				$arr_search_condition[] = "(d.ORG_ID_2 = $search_org_id_2)";
			elseif($search_per_type == 4)
				$arr_search_condition[] = "(e.ORG_ID_2 = $search_org_id_2)";
		}
	} // end if
	if(trim($search_pc_code)) $arr_search_condition[] = "(b.PC_CODE = '$search_pc_code')";
	if(trim($search_sg_code)){
		$cmd = " select SKILL_CODE from PER_SKILL where SG_CODE='$search_sg_code' ";
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_skill[] = $data[SKILL_CODE];
		
		$arr_search_condition[] = "(b.SKILL_CODE in (". implode(",", $arr_skill) ."))";
	} // end if
	if(trim($search_skill_code)) $arr_search_condition[] = "(b.SKILL_CODE = '$search_skill_code')";

	/* ================= 	ประวัติการดำรงตำแหน่ง    ===================== */
	if(trim($search_poh_effectivedate_min)){
		$search_poh_effectivedate_min =  save_date($search_poh_effectivedate_min);
		if($DPISDB=="odbc") $arr_search_positionhis_condition[] = "(LEFT(POH_EFFECTIVEDATE, 10) >= '$search_poh_effectivedate_min')";
		elseif($DPISDB=="oci8") $arr_search_positionhis_condition[] = "(SUBSTR(POH_EFFECTIVEDATE, 1, 10) >= '$search_poh_effectivedate_min')";
		elseif($DPISDB=="mysql") $arr_search_positionhis_condition[] = "(LEFT(POH_EFFECTIVEDATE, 10) >= '$search_poh_effectivedate_min')";
		$search_poh_effectivedate_min = show_date_format($search_poh_effectivedate_min, 1);
	} // end if
	if(trim($search_poh_effectivedate_max)){
		$search_poh_effectivedate_max =  save_date($search_poh_effectivedate_max);
		if($DPISDB=="odbc") $arr_search_positionhis_condition[] = "(LEFT(POH_EFFECTIVEDATE, 10) <= '$search_poh_effectivedate_max')";
		elseif($DPISDB=="oci8") $arr_search_positionhis_condition[] = "(SUBSTR(POH_EFFECTIVEDATE, 1, 10) <= '$search_poh_effectivedate_max')";
		elseif($DPISDB=="mysql") $arr_search_positionhis_condition[] = "(LEFT(POH_EFFECTIVEDATE, 10) <= '$search_poh_effectivedate_max')";
		$search_poh_effectivedate_max = show_date_format($search_poh_effectivedate_max, 1);
	} // end if
	if(trim($search_poh_pos_no_name)) $arr_search_positionhis_condition[] = "(trim(POH_POS_NO_NAME) = '$search_poh_pos_no_name')";
	if(trim($search_poh_pos_no)) $arr_search_positionhis_condition[] = "(trim(POH_POS_NO) = '$search_poh_pos_no')";
	if(trim($search_poh_pl_code)){
		if($search_per_type == 1 || $search_per_type == 5) $arr_search_positionhis_condition[] = "(PL_CODE = '$search_poh_pl_code')";
		elseif($search_per_type == 2) $arr_search_positionhis_condition[] = "(PN_CODE = '$search_poh_pl_code')";
		elseif($search_per_type == 3) $arr_search_positionhis_condition[] = "(EP_CODE = '$search_poh_pl_code')";
		elseif($search_per_type == 4) $arr_search_positionhis_condition[] = "(TP_CODE = '$search_poh_pl_code')";
	} // end if
	if(trim($search_poh_pm_code)) $arr_search_positionhis_condition[] = "(PM_CODE = '$search_poh_pm_code')";
	if(trim($search_poh_pt_code)) $arr_search_positionhis_condition[] = "(PT_CODE = '$search_poh_pt_code')";
	if(trim($search_poh_mov_code)) $arr_search_positionhis_condition[] = "(MOV_CODE = '$search_poh_mov_code')";
	if(trim($search_poh_docno)) $arr_search_positionhis_condition[] = "(trim(POH_DOCNO) = '$search_poh_docno')";
	//if(trim($search_poh_ot_code)) $arr_search_positionhis_condition[] = "(OT_CODE = '$search_poh_ot_code')";
         $valsearch_poh_ot_nameV2= implode(",", $search_poh_ot_nameV2);
       //  echo $valsearch_poh_ot_nameV2;
        $valsearch_poh_ot_nameV2= str_replace("]", "'",str_replace("[", "'", $valsearch_poh_ot_nameV2)) ;
        //echo $valsearch_poh_ot_nameV2.'<<';
        if(trim($valsearch_poh_ot_nameV2)) $arr_search_positionhis_condition[] = "(OT_NAME1 in ($valsearch_poh_ot_nameV2) 
                                                                                OR OT_NAME2 in ($valsearch_poh_ot_nameV2)
                                                                                OR OT_NAME3 in ($valsearch_poh_ot_nameV2) )"; /*ปรับให้เลือกได้มากกว่า 1*/
        
	if(trim($search_poh_org_name)) $arr_search_positionhis_condition[] = "(POH_ORG3 = '$search_poh_org_name')";
	if(trim($search_poh_org_name_1)) $arr_search_positionhis_condition[] = "(trim(POH_UNDER_ORG1) = trim('$search_poh_org_name_1'))";
	if(trim($search_poh_org_name_2)) $arr_search_positionhis_condition[] = "(trim(POH_UNDER_ORG2) = trim('$search_poh_org_name_2'))";
	if(trim($search_poh_ass_department_name)) $arr_search_positionhis_condition[] = "(POH_ASS_DEPARTMENT = '$search_poh_ass_department_name')";
	if(trim($search_poh_ass_org_name)) $arr_search_positionhis_condition[] = "(POH_ASS_ORG = '$search_poh_ass_org_name')";
	if(trim($search_poh_ass_org_name_1)) $arr_search_positionhis_condition[] = "(trim(POH_ASS_ORG1) = trim('$search_poh_ass_org_name_1'))";
	if(trim($search_poh_ass_org_name_2)) $arr_search_positionhis_condition[] = "(trim(POH_ASS_ORG2) = trim('$search_poh_ass_org_name_2'))";
	if(trim($search_poh_remark)) $arr_search_positionhis_condition[] = "(trim(POH_REMARK) like '%$search_poh_remark%')";
	
	if(count($arr_search_positionhis_condition)){
		if(trim($search_poh_ministry_name)) $arr_search_positionhis_condition[] = "(POH_ORG1 = '$search_poh_ministry_name')";
		if(trim($search_poh_department_name)) $arr_search_positionhis_condition[] = "(POH_ORG2 = '$search_poh_department_name')";
		$cmd = " select distinct PER_ID from PER_POSITIONHIS where ". implode(" and ", $arr_search_positionhis_condition) ." order by PER_ID";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$count = 0;
		while($data = $db_dpis->get_array()) { 
			$count++;
			if ($count < 1000) $arr_positionhis[] = $data[PER_ID];
			elseif ($count < 2000) $arr_positionhis2[] = $data[PER_ID];
			elseif ($count < 3000) $arr_positionhis3[] = $data[PER_ID];
			elseif ($count < 4000) $arr_positionhis4[] = $data[PER_ID];
			elseif ($count < 5000) $arr_positionhis5[] = $data[PER_ID];
			elseif ($count < 6000) $arr_positionhis6[] = $data[PER_ID];
			elseif ($count < 7000) $arr_positionhis7[] = $data[PER_ID];
			elseif ($count < 8000) $arr_positionhis8[] = $data[PER_ID];
			elseif ($count < 9000) $arr_positionhis9[] = $data[PER_ID];
			elseif ($count < 10000) $arr_positionhis10[] = $data[PER_ID];
			elseif ($count < 11000) $arr_positionhis11[] = $data[PER_ID];
			elseif ($count < 12000) $arr_positionhis12[] = $data[PER_ID];
			elseif ($count < 13000) $arr_positionhis13[] = $data[PER_ID];
			elseif ($count < 14000) $arr_positionhis14[] = $data[PER_ID];
			elseif ($count < 15000) $arr_positionhis15[] = $data[PER_ID];
			else $arr_positionhis16[] = $data[PER_ID];
		}
		
		if (count($arr_positionhis16)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_positionhis) .")) or (a.PER_ID in (". implode(",", $arr_positionhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis3) .")) or (a.PER_ID in (". implode(",", $arr_positionhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis5) .")) or (a.PER_ID in (". implode(",", $arr_positionhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis7) .")) or (a.PER_ID in (". implode(",", $arr_positionhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis9) .")) or (a.PER_ID in (". implode(",", $arr_positionhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis11) .")) or (a.PER_ID in (". implode(",", $arr_positionhis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis13) .")) or (a.PER_ID in (". implode(",", $arr_positionhis14) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis15) .")) or (a.PER_ID in (". implode(",", $arr_positionhis16) .")))";
		elseif (count($arr_positionhis15)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_positionhis) .")) or (a.PER_ID in (". implode(",", $arr_positionhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis3) .")) or (a.PER_ID in (". implode(",", $arr_positionhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis5) .")) or (a.PER_ID in (". implode(",", $arr_positionhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis7) .")) or (a.PER_ID in (". implode(",", $arr_positionhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis9) .")) or (a.PER_ID in (". implode(",", $arr_positionhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis11) .")) or (a.PER_ID in (". implode(",", $arr_positionhis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis13) .")) or (a.PER_ID in (". implode(",", $arr_positionhis14) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis15) .")))";
		elseif (count($arr_positionhis14)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_positionhis) .")) or (a.PER_ID in (". implode(",", $arr_positionhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis3) .")) or (a.PER_ID in (". implode(",", $arr_positionhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis5) .")) or (a.PER_ID in (". implode(",", $arr_positionhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis7) .")) or (a.PER_ID in (". implode(",", $arr_positionhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis9) .")) or (a.PER_ID in (". implode(",", $arr_positionhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis11) .")) or (a.PER_ID in (". implode(",", $arr_positionhis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis13) .")) or (a.PER_ID in (". implode(",", $arr_positionhis14) .")))";
		elseif (count($arr_positionhis13)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_positionhis) .")) or (a.PER_ID in (". implode(",", $arr_positionhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis3) .")) or (a.PER_ID in (". implode(",", $arr_positionhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis5) .")) or (a.PER_ID in (". implode(",", $arr_positionhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis7) .")) or (a.PER_ID in (". implode(",", $arr_positionhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis9) .")) or (a.PER_ID in (". implode(",", $arr_positionhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis11) .")) or (a.PER_ID in (". implode(",", $arr_positionhis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis13) .")))";
		elseif (count($arr_positionhis12)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_positionhis) .")) or (a.PER_ID in (". implode(",", $arr_positionhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis3) .")) or (a.PER_ID in (". implode(",", $arr_positionhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis5) .")) or (a.PER_ID in (". implode(",", $arr_positionhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis7) .")) or (a.PER_ID in (". implode(",", $arr_positionhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis9) .")) or (a.PER_ID in (". implode(",", $arr_positionhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis11) .")) or (a.PER_ID in (". implode(",", $arr_positionhis12) .")))";
		elseif (count($arr_positionhis11)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_positionhis) .")) or (a.PER_ID in (". implode(",", $arr_positionhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis3) .")) or (a.PER_ID in (". implode(",", $arr_positionhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis5) .")) or (a.PER_ID in (". implode(",", $arr_positionhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis7) .")) or (a.PER_ID in (". implode(",", $arr_positionhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis9) .")) or (a.PER_ID in (". implode(",", $arr_positionhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis11) .")))";
		elseif (count($arr_positionhis10)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_positionhis) .")) or (a.PER_ID in (". implode(",", $arr_positionhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis3) .")) or (a.PER_ID in (". implode(",", $arr_positionhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis5) .")) or (a.PER_ID in (". implode(",", $arr_positionhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis7) .")) or (a.PER_ID in (". implode(",", $arr_positionhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis9) .")) or (a.PER_ID in (". implode(",", $arr_positionhis10) .")))";
		elseif (count($arr_positionhis9)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_positionhis) .")) or (a.PER_ID in (". implode(",", $arr_positionhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis3) .")) or (a.PER_ID in (". implode(",", $arr_positionhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis5) .")) or (a.PER_ID in (". implode(",", $arr_positionhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis7) .")) or (a.PER_ID in (". implode(",", $arr_positionhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis9) .")))";
		elseif (count($arr_positionhis8)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_positionhis) .")) or (a.PER_ID in (". implode(",", $arr_positionhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis3) .")) or (a.PER_ID in (". implode(",", $arr_positionhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis5) .")) or (a.PER_ID in (". implode(",", $arr_positionhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis7) .")) or (a.PER_ID in (". implode(",", $arr_positionhis8) .")))";
		elseif (count($arr_positionhis7)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_positionhis) .")) or (a.PER_ID in (". implode(",", $arr_positionhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis3) .")) or (a.PER_ID in (". implode(",", $arr_positionhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis5) .")) or (a.PER_ID in (". implode(",", $arr_positionhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis7) .")))";
		elseif (count($arr_positionhis6)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_positionhis) .")) or (a.PER_ID in (". implode(",", $arr_positionhis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_positionhis3) .")) or (a.PER_ID in (". implode(",", $arr_positionhis4) .")) or 
																															(a.PER_ID in (". implode(",", $arr_positionhis5) .")) or (a.PER_ID in (". implode(",", $arr_positionhis6) .")))";
		elseif (count($arr_positionhis5)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_positionhis) .")) or (a.PER_ID in (". implode(",", $arr_positionhis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_positionhis3) .")) or (a.PER_ID in (". implode(",", $arr_positionhis4) .")) or 
																															(a.PER_ID in (". implode(",", $arr_positionhis5) .")))";
		elseif (count($arr_positionhis4)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_positionhis) .")) or (a.PER_ID in (". implode(",", $arr_positionhis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_positionhis3) .")) or (a.PER_ID in (". implode(",", $arr_positionhis4) .")))";
		elseif (count($arr_positionhis3)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_positionhis) .")) or (a.PER_ID in (". implode(",", $arr_positionhis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_positionhis3) .")))";
		elseif (count($arr_positionhis2)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_positionhis) .")) or (a.PER_ID in (". implode(",", $arr_positionhis2) .")))";
		else $arr_search_condition[] = "(a.PER_ID in (". implode(",", $arr_positionhis) ."))";
	} // end if
	
	/* ================= 	ประวัติการรับเงินเดือน    ===================== */
	if(trim($search_sah_effectivedate_min)){
		$search_sah_effectivedate_min =  save_date($search_sah_effectivedate_min);
		if($DPISDB=="odbc") $arr_search_salaryhis_condition[] = "(LEFT(SAH_EFFECTIVEDATE, 10) >= '$search_sah_effectivedate_min')";
		elseif($DPISDB=="oci8") $arr_search_salaryhis_condition[] = "(SUBSTR(SAH_EFFECTIVEDATE, 1, 10) >= '$search_sah_effectivedate_min')";
		elseif($DPISDB=="mysql") $arr_search_salaryhis_condition[] = "(LEFT(SAH_EFFECTIVEDATE, 10) >= '$search_sah_effectivedate_min')";
		$search_sah_effectivedate_min = show_date_format($search_sah_effectivedate_min, 1);
	} // end if
	if(trim($search_sah_effectivedate_max)){
		$search_sah_effectivedate_max =  save_date($search_sah_effectivedate_max);
		if($DPISDB=="odbc") $arr_search_salaryhis_condition[] = "(LEFT(SAH_EFFECTIVEDATE, 10) <= '$search_sah_effectivedate_max')";
		elseif($DPISDB=="oci8") $arr_search_salaryhis_condition[] = "(SUBSTR(SAH_EFFECTIVEDATE, 1, 10) <= '$search_sah_effectivedate_max')";
		elseif($DPISDB=="mysql") $arr_search_salaryhis_condition[] = "(LEFT(SAH_EFFECTIVEDATE, 10) <= '$search_sah_effectivedate_max')";
		$search_sah_effectivedate_max = show_date_format($search_sah_effectivedate_max, 1);
	} // end if
	if(trim($search_sah_docno)) $arr_search_salaryhis_condition[] = "(trim(SAH_DOCNO) = '$search_sah_docno')";
	if(trim($search_sah_pos_no)) $arr_search_salaryhis_condition[] = "(trim(SAH_POS_NO) = '$search_sah_pos_no')";
	if(trim($search_sah_pl_code)){
		if($search_per_type == 1 || $search_per_type == 5) $arr_search_salaryhis_condition[] = "(PL_CODE = '$search_sah_pl_code')";
		elseif($search_per_type == 2) $arr_search_salaryhis_condition[] = "(PN_CODE = '$search_sah_pl_code')";
		elseif($search_per_type == 3) $arr_search_salaryhis_condition[] = "(EP_CODE = '$search_sah_pl_code')";
		elseif($search_per_type == 4) $arr_search_salaryhis_condition[] = "(TP_CODE = '$search_sah_pl_code')";
	} // end if
	if(trim($search_sah_pm_code)) $arr_search_salaryhis_condition[] = "(PM_CODE = '$search_sah_pm_code')";
	if(trim($search_sah_org_id)) $arr_search_salaryhis_condition[] = "(ORG_ID_3 = $search_sah_org_id)";
	if(trim($search_sah_org_name_1)) $arr_search_salaryhis_condition[] = "(trim(SAH_UNDER_ORG1) = trim('$search_sah_org_name_1'))";
	if(trim($search_sah_org_name_2)) $arr_search_salaryhis_condition[] = "(trim(SAH_UNDER_ORG2) = trim('$search_sah_org_name_2'))";
//	if(trim($search_sah_level_no_min)) $arr_search_salaryhis_condition[] = "(trim(SAH_LEVEL_NO) >= trim('$search_sah_level_no_min'))";
//	if(trim($search_sah_level_no_max)) $arr_search_salaryhis_condition[] = "(trim(SAH_LEVEL_NO) <= trim('$search_sah_level_no_max'))";
	//  อ่านจาก search_level_no_min[] ที่เป็น multiselection
	if(trim($search_sah_level_no_min)) {
		$arr_level_no_condi = (array) null;
		foreach ($search_sah_level_no_min as $search_level_no)
		{
			if ($search_level_no) $arr_level_no_condi[] = "'$search_level_no'";
	//		echo "search_level_no=$search_level_no<br>";
		}
		if (count($arr_level_no_condi) > 0) $arr_search_salaryhis_condition[] = "(trim(SAH_LEVEL_NO) in (".implode(",",$arr_level_no_condi)."))";
//		echo "sah  arr_level_no_condi = (".implode(",",$arr_level_no_condi).")<br>";
	}
	if(trim($search_sah_salary_min)) $arr_search_salaryhis_condition[] = "(SAH_SALARY >= $search_sah_salary_min)";
	if(trim($search_sah_salary_max)) $arr_search_salaryhis_condition[] = "(SAH_SALARY <= $search_sah_salary_max)";
	if(trim($search_sah_mov_code)) $arr_search_salaryhis_condition[] = "(MOV_CODE = '$search_sah_mov_code')";
	
	if(count($arr_search_salaryhis_condition)){
		$cmd = " select distinct PER_ID from PER_SALARYHIS where ". implode(" and ", $arr_search_salaryhis_condition) ." order by PER_ID";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$count = 0;
		while($data = $db_dpis->get_array()) { 
			$count++;
			if ($count < 1000) $arr_salaryhis[] = $data[PER_ID];
			elseif ($count < 2000) $arr_salaryhis2[] = $data[PER_ID];
			elseif ($count < 3000) $arr_salaryhis3[] = $data[PER_ID];
			elseif ($count < 4000) $arr_salaryhis4[] = $data[PER_ID];
			elseif ($count < 5000) $arr_salaryhis5[] = $data[PER_ID];
			elseif ($count < 6000) $arr_salaryhis6[] = $data[PER_ID];
			elseif ($count < 7000) $arr_salaryhis7[] = $data[PER_ID];
			elseif ($count < 8000) $arr_salaryhis8[] = $data[PER_ID];
			elseif ($count < 9000) $arr_salaryhis9[] = $data[PER_ID];
			elseif ($count < 10000) $arr_salaryhis10[] = $data[PER_ID];
			elseif ($count < 11000) $arr_salaryhis11[] = $data[PER_ID];
			elseif ($count < 12000) $arr_salaryhis12[] = $data[PER_ID];
			elseif ($count < 13000) $arr_salaryhis13[] = $data[PER_ID];
			elseif ($count < 14000) $arr_salaryhis14[] = $data[PER_ID];
			elseif ($count < 15000) $arr_salaryhis15[] = $data[PER_ID];
			elseif ($count < 16000) $arr_salaryhis16[] = $data[PER_ID];
			elseif ($count < 17000) $arr_salaryhis17[] = $data[PER_ID];
			elseif ($count < 18000) $arr_salaryhis18[] = $data[PER_ID];
			elseif ($count < 19000) $arr_salaryhis19[] = $data[PER_ID];
			else $arr_salaryhis20[] = $data[PER_ID];
		}
		
		if (count($arr_salaryhis20)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis5) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis7) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis9) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis11) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis13) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis14) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis15) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis16) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis17) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis18) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis19) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis20) .")))";
		elseif (count($arr_salaryhis19)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis5) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis7) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis9) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis11) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis13) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis14) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis15) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis16) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis17) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis18) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis19) .")))";
		elseif (count($arr_salaryhis18)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis5) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis7) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis9) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis11) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis13) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis14) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis15) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis16) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis17) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis18) .")))";
		elseif (count($arr_salaryhis17)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis5) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis7) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis9) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis11) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis13) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis14) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis15) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis16) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis17) .")))";
		elseif (count($arr_salaryhis16)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis5) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis7) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis9) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis11) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis13) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis14) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis15) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis16) .")))";
		elseif (count($arr_salaryhis15)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis5) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis7) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis9) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis11) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis13) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis14) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis15) .")))";
		elseif (count($arr_salaryhis14)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis5) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis7) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis9) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis11) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis13) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis14) .")))";
		elseif (count($arr_salaryhis13)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis5) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis7) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis9) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis11) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis13) .")))";
		elseif (count($arr_salaryhis12)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis5) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis7) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis9) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis11) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis12) .")))";
		elseif (count($arr_salaryhis11)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis5) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis7) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis9) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis11) .")))";
		elseif (count($arr_salaryhis10)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis5) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis7) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis9) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis10) .")))";
		elseif (count($arr_salaryhis9)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis5) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis7) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis9) .")))";
		elseif (count($arr_salaryhis8)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis5) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis7) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis8) .")))";
		elseif (count($arr_salaryhis7)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis5) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis7) .")))";
		elseif (count($arr_salaryhis6)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")) or 
																															(a.PER_ID in (". implode(",", $arr_salaryhis5) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis6) .")))";
		elseif (count($arr_salaryhis5)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")) or 
																															(a.PER_ID in (". implode(",", $arr_salaryhis5) .")))";
		elseif (count($arr_salaryhis4)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")))";
		elseif (count($arr_salaryhis3)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_salaryhis3) .")))";
		elseif (count($arr_salaryhis2)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")))";
		else $arr_search_condition[] = "(a.PER_ID in (". implode(",", $arr_salaryhis) ."))";
	} // end if
	
	/* ================= 	ประวัติการศึกษา    ===================== */
	$search_edu = "";
	if(trim($search_el_code)) $arr_search_educatehis_condition[] = "(trim(a.EL_CODE) = trim('$search_el_code'))";
	if(trim($search_en_code)) $arr_search_educatehis_condition[] = "(a.EN_CODE = '$search_en_code')";
	if(trim($search_em_code)) $arr_search_educatehis_condition[] = "(a.EM_CODE = '$search_em_code')";
	if(trim($search_ins_code)) $arr_search_educatehis_condition[] = "(trim(a.INS_CODE) = trim('$search_ins_code') or trim(a.EDU_INSTITUTE) = trim('$search_ins_name'))";
	if(trim($search_edu_institute)) $arr_search_educatehis_condition[] = "(trim(b.INS_NAME) = trim('$search_edu_institute') or trim(a.EDU_INSTITUTE) = trim('$search_edu_institute'))";
	if(trim($search_ins_ct_code)) $arr_search_educatehis_condition[] = "(trim(a.CT_CODE_EDU) = trim('$search_ins_ct_code'))";
	if(trim($search_st_code)) $arr_search_educatehis_condition[] = "(a.ST_CODE = '$search_st_code')";
	if(trim($search_edu_ct_code)) $arr_search_educatehis_condition[] = "(a.CT_CODE = '$search_edu_ct_code')";
	if(trim($search_edu_endyear_min)) $arr_search_educatehis_condition[] = "(a.EDU_ENDYEAR >= '$search_edu_endyear_min')";
	if(trim($search_edu_endyear_max)) $arr_search_educatehis_condition[] = "(a.EDU_ENDYEAR <= '$search_edu_endyear_max')";

	if(count($arr_search_educatehis_condition)){
		for ($i=0;$i<count($EDU_TYPE);$i++) {
			if($search_edu) { $search_edu.= ' or '; }
			$search_edu.= "a.EDU_TYPE like '%$EDU_TYPE[$i]%' "; 
		} 
		if ($search_edu) $arr_search_educatehis_condition[] = "(".$search_edu.")";
		$cmd = " select 	distinct a.PER_ID 
						 from 		PER_EDUCATE a, PER_INSTITUTE b
						 where 	a.INS_CODE=b.INS_CODE(+)
						 				and ". implode(" and ", $arr_search_educatehis_condition) ." 
						 order by a.PER_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$count = 0;
		while($data = $db_dpis->get_array()) { 
			$count++;
			if ($count < 1000) $arr_educatehis[] = $data[PER_ID];
			elseif ($count < 2000) $arr_educatehis2[] = $data[PER_ID];
			elseif ($count < 3000) $arr_educatehis3[] = $data[PER_ID];
			elseif ($count < 4000) $arr_educatehis4[] = $data[PER_ID];
			elseif ($count < 5000) $arr_educatehis5[] = $data[PER_ID];
			elseif ($count < 6000) $arr_educatehis6[] = $data[PER_ID];
			elseif ($count < 7000) $arr_educatehis7[] = $data[PER_ID];
			elseif ($count < 8000) $arr_educatehis8[] = $data[PER_ID];
			elseif ($count < 9000) $arr_educatehis9[] = $data[PER_ID];
			elseif ($count < 10000) $arr_educatehis10[] = $data[PER_ID];
			elseif ($count < 11000) $arr_educatehis11[] = $data[PER_ID];
			elseif ($count < 12000) $arr_educatehis12[] = $data[PER_ID];
			elseif ($count < 13000) $arr_educatehis13[] = $data[PER_ID];
			elseif ($count < 14000) $arr_educatehis14[] = $data[PER_ID];
			elseif ($count < 15000) $arr_educatehis15[] = $data[PER_ID];
			else $arr_educatehis16[] = $data[PER_ID];
		}
		
		if (count($arr_educatehis16)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_educatehis) .")) or (a.PER_ID in (". implode(",", $arr_educatehis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis3) .")) or (a.PER_ID in (". implode(",", $arr_educatehis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis5) .")) or (a.PER_ID in (". implode(",", $arr_educatehis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis7) .")) or (a.PER_ID in (". implode(",", $arr_educatehis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis9) .")) or (a.PER_ID in (". implode(",", $arr_educatehis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis11) .")) or (a.PER_ID in (". implode(",", $arr_educatehis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis13) .")) or (a.PER_ID in (". implode(",", $arr_educatehis14) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis15) .")) or (a.PER_ID in (". implode(",", $arr_educatehis16) .")))";
		elseif (count($arr_educatehis15)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_educatehis) .")) or (a.PER_ID in (". implode(",", $arr_educatehis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis3) .")) or (a.PER_ID in (". implode(",", $arr_educatehis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis5) .")) or (a.PER_ID in (". implode(",", $arr_educatehis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis7) .")) or (a.PER_ID in (". implode(",", $arr_educatehis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis9) .")) or (a.PER_ID in (". implode(",", $arr_educatehis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis11) .")) or (a.PER_ID in (". implode(",", $arr_educatehis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis13) .")) or (a.PER_ID in (". implode(",", $arr_educatehis14) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis15) .")))";
		elseif (count($arr_educatehis14)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_educatehis) .")) or (a.PER_ID in (". implode(",", $arr_educatehis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis3) .")) or (a.PER_ID in (". implode(",", $arr_educatehis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis5) .")) or (a.PER_ID in (". implode(",", $arr_educatehis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis7) .")) or (a.PER_ID in (". implode(",", $arr_educatehis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis9) .")) or (a.PER_ID in (". implode(",", $arr_educatehis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis11) .")) or (a.PER_ID in (". implode(",", $arr_educatehis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis13) .")) or (a.PER_ID in (". implode(",", $arr_educatehis14) .")))";
		elseif (count($arr_educatehis13)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_educatehis) .")) or (a.PER_ID in (". implode(",", $arr_educatehis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis3) .")) or (a.PER_ID in (". implode(",", $arr_educatehis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis5) .")) or (a.PER_ID in (". implode(",", $arr_educatehis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis7) .")) or (a.PER_ID in (". implode(",", $arr_educatehis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis9) .")) or (a.PER_ID in (". implode(",", $arr_educatehis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis11) .")) or (a.PER_ID in (". implode(",", $arr_educatehis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis13) .")))";
		elseif (count($arr_educatehis12)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_educatehis) .")) or (a.PER_ID in (". implode(",", $arr_educatehis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis3) .")) or (a.PER_ID in (". implode(",", $arr_educatehis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis5) .")) or (a.PER_ID in (". implode(",", $arr_educatehis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis7) .")) or (a.PER_ID in (". implode(",", $arr_educatehis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis9) .")) or (a.PER_ID in (". implode(",", $arr_educatehis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis11) .")) or (a.PER_ID in (". implode(",", $arr_educatehis12) .")))";
		elseif (count($arr_educatehis11)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_educatehis) .")) or (a.PER_ID in (". implode(",", $arr_educatehis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis3) .")) or (a.PER_ID in (". implode(",", $arr_educatehis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis5) .")) or (a.PER_ID in (". implode(",", $arr_educatehis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis7) .")) or (a.PER_ID in (". implode(",", $arr_educatehis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis9) .")) or (a.PER_ID in (". implode(",", $arr_educatehis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis11) .")))";
		elseif (count($arr_educatehis10)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_educatehis) .")) or (a.PER_ID in (". implode(",", $arr_educatehis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis3) .")) or (a.PER_ID in (". implode(",", $arr_educatehis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis5) .")) or (a.PER_ID in (". implode(",", $arr_educatehis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis7) .")) or (a.PER_ID in (". implode(",", $arr_educatehis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis9) .")) or (a.PER_ID in (". implode(",", $arr_educatehis10) .")))";
		elseif (count($arr_educatehis9)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_educatehis) .")) or (a.PER_ID in (". implode(",", $arr_educatehis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis3) .")) or (a.PER_ID in (". implode(",", $arr_educatehis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis5) .")) or (a.PER_ID in (". implode(",", $arr_educatehis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis7) .")) or (a.PER_ID in (". implode(",", $arr_educatehis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis9) .")))";
		elseif (count($arr_educatehis8)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_educatehis) .")) or (a.PER_ID in (". implode(",", $arr_educatehis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis3) .")) or (a.PER_ID in (". implode(",", $arr_educatehis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis5) .")) or (a.PER_ID in (". implode(",", $arr_educatehis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis7) .")) or (a.PER_ID in (". implode(",", $arr_educatehis8) .")))";
		elseif (count($arr_educatehis7)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_educatehis) .")) or (a.PER_ID in (". implode(",", $arr_educatehis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis3) .")) or (a.PER_ID in (". implode(",", $arr_educatehis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis5) .")) or (a.PER_ID in (". implode(",", $arr_educatehis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis7) .")))";
		elseif (count($arr_educatehis6)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_educatehis) .")) or (a.PER_ID in (". implode(",", $arr_educatehis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_educatehis3) .")) or (a.PER_ID in (". implode(",", $arr_educatehis4) .")) or 
																															(a.PER_ID in (". implode(",", $arr_educatehis5) .")) or (a.PER_ID in (". implode(",", $arr_educatehis6) .")))";
		elseif (count($arr_educatehis5)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_educatehis) .")) or (a.PER_ID in (". implode(",", $arr_educatehis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_educatehis3) .")) or (a.PER_ID in (". implode(",", $arr_educatehis4) .")) or 
																															(a.PER_ID in (". implode(",", $arr_educatehis5) .")))";
		elseif (count($arr_educatehis4)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_educatehis) .")) or (a.PER_ID in (". implode(",", $arr_educatehis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_educatehis3) .")) or (a.PER_ID in (". implode(",", $arr_educatehis4) .")))";
		elseif (count($arr_educatehis3)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_educatehis) .")) or (a.PER_ID in (". implode(",", $arr_educatehis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_educatehis3) .")))";
		elseif (count($arr_educatehis2)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_educatehis) .")) or (a.PER_ID in (". implode(",", $arr_educatehis2) .")))";
		else $arr_search_condition[] = "(a.PER_ID in (". implode(",", $arr_educatehis) ."))";
	} // end if

	/* ================= 	ประวัติการอบรม/ดูงาน    ===================== */
	$search_trn = "";
	if(trim($search_trn_startyear)){ 
		$trn_startyear = $search_trn_startyear - 543;
		if($DPISDB=="odbc") $arr_search_trainhis_condition[] = "(LEFT(TRN_STARTDATE, 4) >= '$trn_startyear')";
		elseif($DPISDB=="oci8") $arr_search_trainhis_condition[] = "(SUBSTR(TRN_STARTDATE, 1, 4) >= '$trn_startyear')";
		elseif($DPISDB=="mysql") $arr_search_trainhis_condition[] = "(LEFT(TRN_STARTDATE, 4) >= '$trn_startyear')";
	} // endif
	if(trim($search_trn_endyear)){
		$trn_endyear = $search_trn_endyear - 543;
		if($DPISDB=="odbc") $arr_search_trainhis_condition[] = "(LEFT(TRN_ENDDATE, 4) <= '$trn_endyear')";
		elseif($DPISDB=="oci8") $arr_search_trainhis_condition[] = "(SUBSTR(TRN_ENDDATE, 1, 4) <= '$trn_endyear')";
		elseif($DPISDB=="mysql") $arr_search_trainhis_condition[] = "(LEFT(TRN_ENDDATE, 4) <= '$trn_endyear')";
	} // end if
	if(trim($search_tr_code)) $arr_search_trainhis_condition[] = "(TR_CODE = '$search_tr_code')";
	if(trim($search_trn_course_name)) $arr_search_trainhis_condition[] = "(TRN_COURSE_NAME like '$search_trn_course_name%')";
	if(trim($search_trn_no)) $arr_search_trainhis_condition[] = "(TRN_NO = '$search_trn_no')";
	if(trim($search_tr_ct_code)) $arr_search_trainhis_condition[] = "(CT_CODE = '$search_tr_ct_code')";
	if(trim($search_trn_fund)) $arr_search_trainhis_condition[] = "(TRN_FUND like '$search_trn_fund%')";
	if(trim($search_fund_ct_code)) $arr_search_trainhis_condition[] = "(CT_CODE_FUND = '$search_fund_ct_code')";

	if(count($arr_search_trainhis_condition)){
		for ($i=0;$i<count($TRN_TYPE);$i++) {
			if($search_trn) { $search_trn.= ' or '; }
			$search_trn.= "TRN_TYPE like '%$TRN_TYPE[$i]%' "; 
		} 
		if ($search_trn) $arr_search_trainhis_condition[] = "(".$search_trn.")";
		$cmd = " select distinct PER_ID from PER_TRAINING where ". implode(" and ", $arr_search_trainhis_condition) ." order by PER_ID";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$count = 0;
		while($data = $db_dpis->get_array()) { 
			$count++;
			if ($count < 1000) $arr_trainhis[] = $data[PER_ID];
			elseif ($count < 2000) $arr_trainhis2[] = $data[PER_ID];
			elseif ($count < 3000) $arr_trainhis3[] = $data[PER_ID];
			elseif ($count < 4000) $arr_trainhis4[] = $data[PER_ID];
			elseif ($count < 5000) $arr_trainhis5[] = $data[PER_ID];
			elseif ($count < 6000) $arr_trainhis6[] = $data[PER_ID];
			elseif ($count < 7000) $arr_trainhis7[] = $data[PER_ID];
			elseif ($count < 8000) $arr_trainhis8[] = $data[PER_ID];
			elseif ($count < 9000) $arr_trainhis9[] = $data[PER_ID];
			elseif ($count < 10000) $arr_trainhis10[] = $data[PER_ID];
			elseif ($count < 11000) $arr_trainhis11[] = $data[PER_ID];
			elseif ($count < 12000) $arr_trainhis12[] = $data[PER_ID];
			elseif ($count < 13000) $arr_trainhis13[] = $data[PER_ID];
			elseif ($count < 14000) $arr_trainhis14[] = $data[PER_ID];
			elseif ($count < 15000) $arr_trainhis15[] = $data[PER_ID];
			else $arr_trainhis16[] = $data[PER_ID];
		}
		
		if (count($arr_trainhis16)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_trainhis) .")) or (a.PER_ID in (". implode(",", $arr_trainhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis3) .")) or (a.PER_ID in (". implode(",", $arr_trainhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis5) .")) or (a.PER_ID in (". implode(",", $arr_trainhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis7) .")) or (a.PER_ID in (". implode(",", $arr_trainhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis9) .")) or (a.PER_ID in (". implode(",", $arr_trainhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis11) .")) or (a.PER_ID in (". implode(",", $arr_trainhis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis13) .")) or (a.PER_ID in (". implode(",", $arr_trainhis14) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis15) .")) or (a.PER_ID in (". implode(",", $arr_trainhis16) .")))";
		elseif (count($arr_trainhis15)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_trainhis) .")) or (a.PER_ID in (". implode(",", $arr_trainhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis3) .")) or (a.PER_ID in (". implode(",", $arr_trainhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis5) .")) or (a.PER_ID in (". implode(",", $arr_trainhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis7) .")) or (a.PER_ID in (". implode(",", $arr_trainhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis9) .")) or (a.PER_ID in (". implode(",", $arr_trainhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis11) .")) or (a.PER_ID in (". implode(",", $arr_trainhis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis13) .")) or (a.PER_ID in (". implode(",", $arr_trainhis14) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis15) .")))";
		elseif (count($arr_trainhis14)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_trainhis) .")) or (a.PER_ID in (". implode(",", $arr_trainhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis3) .")) or (a.PER_ID in (". implode(",", $arr_trainhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis5) .")) or (a.PER_ID in (". implode(",", $arr_trainhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis7) .")) or (a.PER_ID in (". implode(",", $arr_trainhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis9) .")) or (a.PER_ID in (". implode(",", $arr_trainhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis11) .")) or (a.PER_ID in (". implode(",", $arr_trainhis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis13) .")) or (a.PER_ID in (". implode(",", $arr_trainhis14) .")))";
		elseif (count($arr_trainhis13)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_trainhis) .")) or (a.PER_ID in (". implode(",", $arr_trainhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis3) .")) or (a.PER_ID in (". implode(",", $arr_trainhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis5) .")) or (a.PER_ID in (". implode(",", $arr_trainhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis7) .")) or (a.PER_ID in (". implode(",", $arr_trainhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis9) .")) or (a.PER_ID in (". implode(",", $arr_trainhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis11) .")) or (a.PER_ID in (". implode(",", $arr_trainhis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis13) .")))";
		elseif (count($arr_trainhis12)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_trainhis) .")) or (a.PER_ID in (". implode(",", $arr_trainhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis3) .")) or (a.PER_ID in (". implode(",", $arr_trainhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis5) .")) or (a.PER_ID in (". implode(",", $arr_trainhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis7) .")) or (a.PER_ID in (". implode(",", $arr_trainhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis9) .")) or (a.PER_ID in (". implode(",", $arr_trainhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis11) .")) or (a.PER_ID in (". implode(",", $arr_trainhis12) .")))";
		elseif (count($arr_trainhis11)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_trainhis) .")) or (a.PER_ID in (". implode(",", $arr_trainhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis3) .")) or (a.PER_ID in (". implode(",", $arr_trainhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis5) .")) or (a.PER_ID in (". implode(",", $arr_trainhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis7) .")) or (a.PER_ID in (". implode(",", $arr_trainhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis9) .")) or (a.PER_ID in (". implode(",", $arr_trainhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis11) .")))";
		elseif (count($arr_trainhis10)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_trainhis) .")) or (a.PER_ID in (". implode(",", $arr_trainhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis3) .")) or (a.PER_ID in (". implode(",", $arr_trainhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis5) .")) or (a.PER_ID in (". implode(",", $arr_trainhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis7) .")) or (a.PER_ID in (". implode(",", $arr_trainhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis9) .")) or (a.PER_ID in (". implode(",", $arr_trainhis10) .")))";
		elseif (count($arr_trainhis9)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_trainhis) .")) or (a.PER_ID in (". implode(",", $arr_trainhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis3) .")) or (a.PER_ID in (". implode(",", $arr_trainhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis5) .")) or (a.PER_ID in (". implode(",", $arr_trainhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis7) .")) or (a.PER_ID in (". implode(",", $arr_trainhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis9) .")))";
		elseif (count($arr_trainhis8)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_trainhis) .")) or (a.PER_ID in (". implode(",", $arr_trainhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis3) .")) or (a.PER_ID in (". implode(",", $arr_trainhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis5) .")) or (a.PER_ID in (". implode(",", $arr_trainhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis7) .")) or (a.PER_ID in (". implode(",", $arr_trainhis8) .")))";
		elseif (count($arr_trainhis7)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_trainhis) .")) or (a.PER_ID in (". implode(",", $arr_trainhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis3) .")) or (a.PER_ID in (". implode(",", $arr_trainhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis5) .")) or (a.PER_ID in (". implode(",", $arr_trainhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis7) .")))";
		elseif (count($arr_trainhis6)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_trainhis) .")) or (a.PER_ID in (". implode(",", $arr_trainhis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_trainhis3) .")) or (a.PER_ID in (". implode(",", $arr_trainhis4) .")) or 
																															(a.PER_ID in (". implode(",", $arr_trainhis5) .")) or (a.PER_ID in (". implode(",", $arr_trainhis6) .")))";
		elseif (count($arr_trainhis5)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_trainhis) .")) or (a.PER_ID in (". implode(",", $arr_trainhis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_trainhis3) .")) or (a.PER_ID in (". implode(",", $arr_trainhis4) .")) or 
																															(a.PER_ID in (". implode(",", $arr_trainhis5) .")))";
		elseif (count($arr_trainhis4)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_trainhis) .")) or (a.PER_ID in (". implode(",", $arr_trainhis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_trainhis3) .")) or (a.PER_ID in (". implode(",", $arr_trainhis4) .")))";
		elseif (count($arr_trainhis3)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_trainhis) .")) or (a.PER_ID in (". implode(",", $arr_trainhis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_trainhis3) .")))";
		elseif (count($arr_trainhis2)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_trainhis) .")) or (a.PER_ID in (". implode(",", $arr_trainhis2) .")))";
		else $arr_search_condition[] = "(a.PER_ID in (". implode(",", $arr_trainhis) ."))";
	} // end if

	/* ================= 	ความดีความชอบ   ===================== */
	if(trim($search_rew_code)) $arr_search_rewardhis_condition[] = "(REW_CODE = '$search_rew_code')";
	if(trim($search_reh_year_min)){ 
                $search_reh_year_minx = $search_reh_year_min-543;
		if($DPISDB=="odbc") $arr_search_rewardhis_condition[] = "(LEFT(REH_DATE, 4) >= '$search_reh_year_minx')";
		elseif($DPISDB=="oci8") $arr_search_rewardhis_condition[] = "(SUBSTR(REH_DATE, 1, 4) >= '$search_reh_year_minx')";
		elseif($DPISDB=="mysql") $arr_search_rewardhis_condition[] = "(LEFT(REH_DATE, 4) >= '$search_reh_year_minx')";
	} // endif
	if(trim($search_reh_year_max)){
                $search_reh_year_maxx = $search_reh_year_max-543;
		if($DPISDB=="odbc") $arr_search_rewardhis_condition[] = "(LEFT(REH_DATE, 4) <= '$search_reh_year_maxx')";
		elseif($DPISDB=="oci8") $arr_search_rewardhis_condition[] = "(SUBSTR(REH_DATE, 1, 4) <= '$search_reh_year_maxx')";
		elseif($DPISDB=="mysql") $arr_search_rewardhis_condition[] = "(LEFT(REH_DATE, 4) <= '$search_reh_year_maxx')";
	} // end if

	if(count($arr_search_rewardhis_condition)){
		$cmd = " select distinct PER_ID from PER_REWARDHIS where ". implode(" and ", $arr_search_rewardhis_condition) ." order by PER_ID";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$count = 0;
		while($data = $db_dpis->get_array()) { 
			$count++;
			if ($count < 1000) $arr_rewardhis[] = $data[PER_ID];
			else $arr_rewardhis2[] = $data[PER_ID];
		}
		
		if (count($arr_rewardhis2)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_rewardhis) .")) or (a.PER_ID in (". implode(",", $arr_rewardhis2) .")))";
		else $arr_search_condition[] = "(a.PER_ID in (". implode(",", $arr_rewardhis) ."))";
	} // end if

	/* ================= 	ราชการพิเศษ    ===================== */
	if(trim($search_srh_startdate)){
		$search_srh_startdate =  save_date($search_srh_startdate);
		if($DPISDB=="odbc") $arr_search_servicehis_condition[] = "(LEFT(SRH_STARTDATE, 10) >= '$search_srh_startdate')";
		elseif($DPISDB=="oci8") $arr_search_servicehis_condition[] = "(SUBSTR(SRH_STARTDATE, 1, 10) >= '$search_srh_startdate')";
		elseif($DPISDB=="mysql") $arr_search_servicehis_condition[] = "(LEFT(SRH_STARTDATE, 10) >= '$search_srh_startdate')";
		$search_srh_startdate = show_date_format($search_srh_startdate, 1);
	} // end if
	if(trim($search_srh_enddate)){
		$search_srh_enddate =  save_date($search_srh_enddate);
		if($DPISDB=="odbc") $arr_search_servicehis_condition[] = "(LEFT(SRH_ENDDATE, 10) <= '$search_srh_enddate')";
		elseif($DPISDB=="oci8") $arr_search_servicehis_condition[] = "(SUBSTR(SRH_ENDDATE, 1, 10) <= '$search_srh_enddate')";
		elseif($DPISDB=="mysql") $arr_search_servicehis_condition[] = "(LEFT(SRH_ENDDATE, 10) <= '$search_srh_enddate')";
		$search_srh_enddate = show_date_format($search_srh_enddate, 1);
	} // end if
	if(trim($search_sv_code)) $arr_search_servicehis_condition[] = "(SV_CODE = '$search_sv_code')";
	if(trim($search_srt_code)) $arr_search_servicehis_condition[] = "(SRT_CODE = '$search_srt_code')";
	if(trim($search_srh_org_id)) $arr_search_servicehis_condition[] = "(ORG_ID = $search_srh_org_id)";

	if(count($arr_search_servicehis_condition)){
		$cmd = " select distinct PER_ID from PER_SERVICEHIS where ". implode(" and ", $arr_search_servicehis_condition) ." order by PER_ID";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$count = 0;
		while($data = $db_dpis->get_array()) { 
			$count++;
			if ($count < 1000) $arr_servicehis[] = $data[PER_ID];
			else $arr_servicehis2[] = $data[PER_ID];
		}
		
		if (count($arr_servicehis2)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_servicehis) .")) or (a.PER_ID in (". implode(",", $arr_servicehis2) .")))";
		else $arr_search_condition[] = "(a.PER_ID in (". implode(",", $arr_servicehis) ."))";
	} // end if

	/* ================= 	การลา    ===================== */
	if(trim($search_abs_startdate)){
		$search_abs_startdate =  save_date($search_abs_startdate);
		if($DPISDB=="odbc") $arr_search_absenthis_condition[] = "(LEFT(ABS_STARTDATE, 10) >= '$search_abs_startdate')";
		elseif($DPISDB=="oci8") $arr_search_absenthis_condition[] = "(SUBSTR(ABS_STARTDATE, 1, 10) >= '$search_abs_startdate')";
		elseif($DPISDB=="mysql") $arr_search_absenthis_condition[] = "(LEFT(ABS_STARTDATE, 10) >= '$search_abs_startdate')";
		$search_abs_startdate = show_date_format($search_abs_startdate, 1);
	} // end if
	if(trim($search_abs_enddate)){
		$search_abs_enddate =  save_date($search_abs_enddate);
		if($DPISDB=="odbc") $arr_search_absenthis_condition[] = "(LEFT(ABS_ENDDATE, 10) <= '$search_abs_enddate')";
		elseif($DPISDB=="oci8") $arr_search_absenthis_condition[] = "(SUBSTR(ABS_ENDDATE, 1, 10) <= '$search_abs_enddate')";
		elseif($DPISDB=="mysql") $arr_search_absenthis_condition[] = "(LEFT(ABS_ENDDATE, 10) <= '$search_abs_enddate')";
		$search_abs_enddate = show_date_format($search_abs_enddate, 1);
	} // end if
	if(trim($search_ab_code)) $arr_search_absenthis_condition[] = "(AB_CODE = '$search_ab_code')";

	if(count($arr_search_absenthis_condition)){
		$cmd = " select distinct PER_ID from PER_ABSENTHIS where ". implode(" and ", $arr_search_absenthis_condition) ." order by PER_ID";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$count = 0;
		while($data = $db_dpis->get_array()) { 
			$count++;
			if ($count < 1000) $arr_absenthis[] = $data[PER_ID];
			elseif ($count < 2000) $arr_absenthis2[] = $data[PER_ID];
			elseif ($count < 3000) $arr_absenthis3[] = $data[PER_ID];
			elseif ($count < 4000) $arr_absenthis4[] = $data[PER_ID];
			elseif ($count < 5000) $arr_absenthis5[] = $data[PER_ID];
			elseif ($count < 6000) $arr_absenthis6[] = $data[PER_ID];
			elseif ($count < 7000) $arr_absenthis7[] = $data[PER_ID];
			elseif ($count < 8000) $arr_absenthis8[] = $data[PER_ID];
			elseif ($count < 9000) $arr_absenthis9[] = $data[PER_ID];
			elseif ($count < 10000) $arr_absenthis10[] = $data[PER_ID];
			elseif ($count < 11000) $arr_absenthis11[] = $data[PER_ID];
			elseif ($count < 12000) $arr_absenthis12[] = $data[PER_ID];
			elseif ($count < 13000) $arr_absenthis13[] = $data[PER_ID];
			elseif ($count < 14000) $arr_absenthis14[] = $data[PER_ID];
			elseif ($count < 15000) $arr_absenthis15[] = $data[PER_ID];
			else $arr_absenthis16[] = $data[PER_ID];
		}
		
		if (count($arr_absenthis16)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_absenthis) .")) or (a.PER_ID in (". implode(",", $arr_absenthis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis3) .")) or (a.PER_ID in (". implode(",", $arr_absenthis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis5) .")) or (a.PER_ID in (". implode(",", $arr_absenthis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis7) .")) or (a.PER_ID in (". implode(",", $arr_absenthis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis9) .")) or (a.PER_ID in (". implode(",", $arr_absenthis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis11) .")) or (a.PER_ID in (". implode(",", $arr_absenthis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis13) .")) or (a.PER_ID in (". implode(",", $arr_absenthis14) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis15) .")) or (a.PER_ID in (". implode(",", $arr_absenthis16) .")))";
		elseif (count($arr_absenthis15)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_absenthis) .")) or (a.PER_ID in (". implode(",", $arr_absenthis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis3) .")) or (a.PER_ID in (". implode(",", $arr_absenthis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis5) .")) or (a.PER_ID in (". implode(",", $arr_absenthis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis7) .")) or (a.PER_ID in (". implode(",", $arr_absenthis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis9) .")) or (a.PER_ID in (". implode(",", $arr_absenthis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis11) .")) or (a.PER_ID in (". implode(",", $arr_absenthis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis13) .")) or (a.PER_ID in (". implode(",", $arr_absenthis14) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis15) .")))";
		elseif (count($arr_absenthis14)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_absenthis) .")) or (a.PER_ID in (". implode(",", $arr_absenthis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis3) .")) or (a.PER_ID in (". implode(",", $arr_absenthis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis5) .")) or (a.PER_ID in (". implode(",", $arr_absenthis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis7) .")) or (a.PER_ID in (". implode(",", $arr_absenthis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis9) .")) or (a.PER_ID in (". implode(",", $arr_absenthis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis11) .")) or (a.PER_ID in (". implode(",", $arr_absenthis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis13) .")) or (a.PER_ID in (". implode(",", $arr_absenthis14) .")))";
		elseif (count($arr_absenthis13)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_absenthis) .")) or (a.PER_ID in (". implode(",", $arr_absenthis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis3) .")) or (a.PER_ID in (". implode(",", $arr_absenthis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis5) .")) or (a.PER_ID in (". implode(",", $arr_absenthis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis7) .")) or (a.PER_ID in (". implode(",", $arr_absenthis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis9) .")) or (a.PER_ID in (". implode(",", $arr_absenthis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis11) .")) or (a.PER_ID in (". implode(",", $arr_absenthis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis13) .")))";
		elseif (count($arr_absenthis12)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_absenthis) .")) or (a.PER_ID in (". implode(",", $arr_absenthis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis3) .")) or (a.PER_ID in (". implode(",", $arr_absenthis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis5) .")) or (a.PER_ID in (". implode(",", $arr_absenthis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis7) .")) or (a.PER_ID in (". implode(",", $arr_absenthis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis9) .")) or (a.PER_ID in (". implode(",", $arr_absenthis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis11) .")) or (a.PER_ID in (". implode(",", $arr_absenthis12) .")))";
		elseif (count($arr_absenthis11)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_absenthis) .")) or (a.PER_ID in (". implode(",", $arr_absenthis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis3) .")) or (a.PER_ID in (". implode(",", $arr_absenthis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis5) .")) or (a.PER_ID in (". implode(",", $arr_absenthis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis7) .")) or (a.PER_ID in (". implode(",", $arr_absenthis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis9) .")) or (a.PER_ID in (". implode(",", $arr_absenthis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis11) .")))";
		elseif (count($arr_absenthis10)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_absenthis) .")) or (a.PER_ID in (". implode(",", $arr_absenthis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis3) .")) or (a.PER_ID in (". implode(",", $arr_absenthis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis5) .")) or (a.PER_ID in (". implode(",", $arr_absenthis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis7) .")) or (a.PER_ID in (". implode(",", $arr_absenthis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis9) .")) or (a.PER_ID in (". implode(",", $arr_absenthis10) .")))";
		elseif (count($arr_absenthis9)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_absenthis) .")) or (a.PER_ID in (". implode(",", $arr_absenthis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis3) .")) or (a.PER_ID in (". implode(",", $arr_absenthis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis5) .")) or (a.PER_ID in (". implode(",", $arr_absenthis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis7) .")) or (a.PER_ID in (". implode(",", $arr_absenthis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis9) .")))";
		elseif (count($arr_absenthis8)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_absenthis) .")) or (a.PER_ID in (". implode(",", $arr_absenthis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis3) .")) or (a.PER_ID in (". implode(",", $arr_absenthis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis5) .")) or (a.PER_ID in (". implode(",", $arr_absenthis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis7) .")) or (a.PER_ID in (". implode(",", $arr_absenthis8) .")))";
		elseif (count($arr_absenthis7)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_absenthis) .")) or (a.PER_ID in (". implode(",", $arr_absenthis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis3) .")) or (a.PER_ID in (". implode(",", $arr_absenthis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis5) .")) or (a.PER_ID in (". implode(",", $arr_absenthis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis7) .")))";
		elseif (count($arr_absenthis6)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_absenthis) .")) or (a.PER_ID in (". implode(",", $arr_absenthis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_absenthis3) .")) or (a.PER_ID in (". implode(",", $arr_absenthis4) .")) or 
																															(a.PER_ID in (". implode(",", $arr_absenthis5) .")) or (a.PER_ID in (". implode(",", $arr_absenthis6) .")))";
		elseif (count($arr_absenthis5)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_absenthis) .")) or (a.PER_ID in (". implode(",", $arr_absenthis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_absenthis3) .")) or (a.PER_ID in (". implode(",", $arr_absenthis4) .")) or 
																															(a.PER_ID in (". implode(",", $arr_absenthis5) .")))";
		elseif (count($arr_absenthis4)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_absenthis) .")) or (a.PER_ID in (". implode(",", $arr_absenthis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_absenthis3) .")) or (a.PER_ID in (". implode(",", $arr_absenthis4) .")))";
		elseif (count($arr_absenthis3)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_absenthis) .")) or (a.PER_ID in (". implode(",", $arr_absenthis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_absenthis3) .")))";
		elseif (count($arr_absenthis2)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_absenthis) .")) or (a.PER_ID in (". implode(",", $arr_absenthis2) .")))";
		else $arr_search_condition[] = "(a.PER_ID in (". implode(",", $arr_absenthis) ."))";
	} // end if

	/* ================= 	การลาศึกษาต่อ    ===================== */
	if(trim($search_abs_startdate)){
		$search_abs_startdate =  save_date($search_abs_startdate);
		if($DPISDB=="odbc") $arr_search_scholar_condition[] = "(LEFT(SC_STARTDATE, 10) >= '$search_abs_startdate')";
		elseif($DPISDB=="oci8") $arr_search_scholar_condition[] = "(SUBSTR(SC_STARTDATE, 1, 10) >= '$search_abs_startdate')";
		elseif($DPISDB=="mysql") $arr_search_scholar_condition[] = "(LEFT(SC_STARTDATE, 10) >= '$search_abs_startdate')";
		$search_abs_startdate = show_date_format($search_abs_startdate, 1);
	} // end if
	if(trim($search_abs_enddate)){
		$search_abs_enddate =  save_date($search_abs_enddate);
		if($DPISDB=="odbc") $arr_search_scholar_condition[] = "(LEFT(SC_ENDDATE, 10) <= '$search_abs_enddate')";
		elseif($DPISDB=="oci8") $arr_search_scholar_condition[] = "(SUBSTR(SC_ENDDATE, 1, 10) <= '$search_abs_enddate')";
		elseif($DPISDB=="mysql") $arr_search_scholar_condition[] = "(LEFT(SC_ENDDATE, 10) <= '$search_abs_enddate')";
		$search_abs_enddate = show_date_format($search_abs_enddate, 1);
	} // end if
	if(trim($search_sc_el_code)) $arr_search_scholar_condition[] = "(trim(a.EL_CODE) = trim('$search_sc_el_code'))";
	if(trim($search_sc_en_code)) $arr_search_scholar_condition[] = "(a.EN_CODE = '$search_sc_en_code')";
	if(trim($search_sc_em_code)) $arr_search_scholar_condition[] = "(a.EM_CODE = '$search_sc_em_code')";
	if(trim($search_sc_ins_code)) $arr_search_scholar_condition[] = "(trim(a.INS_CODE) = trim('$search_sc_ins_code') or trim(a.SC_INSTITUTE) = trim('$search_sc_ins_name'))";
	if(trim($search_sc_institute)) $arr_search_scholar_condition[] = "(trim(b.INS_NAME) = trim('$search_sc_institute') or trim(a.SC_INSTITUTE) = trim('$search_sc_institute'))";
	if(trim($search_sc_ins_ct_code)) $arr_search_scholar_condition[] = "(trim(b.CT_CODE) = trim('$search_sc_ins_ct_code'))";
	if(trim($search_sc_st_code)) $arr_search_scholar_condition[] = "(a.ST_CODE = '$search_sc_st_code')";
	if(trim($search_sc_ct_code)) $arr_search_scholar_condition[] = "(a.CT_CODE = '$search_sc_ct_code')";
	if(trim($search_sc_endyear_min)) $arr_search_scholar_condition[] = "(substr(a.SC_FINISHDATE,1,4) >= '$search_sc_endyear_min')";
	if(trim($search_sc_endyear_max)) $arr_search_scholar_condition[] = "(substr(a.SC_FINISHDATE,1,4) <= '$search_sc_endyear_max')";

	if(count($arr_search_scholar_condition)){
		$cmd = " select distinct PER_ID from PER_SCHOLAR where ". implode(" and ", $arr_search_scholar_condition) ." order by PER_ID";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$count = 0;
		while($data = $db_dpis->get_array()) { 
			$count++;
			if ($count < 1000) $arr_scholar[] = $data[PER_ID];
			else $arr_scholar2[] = $data[PER_ID];
		}
		
		if (count($arr_scholar2)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_scholar) .")) or (a.PER_ID in (". implode(",", $arr_scholar2) .")))";
		else $arr_search_condition[] = "(a.PER_ID in (". implode(",", $arr_scholar) ."))";
	} // end if

	/* ================= 	เครื่องราชฯ    ===================== */
	if(trim($search_deh_startdate)){
		$search_deh_startdate =  save_date($search_deh_startdate);
		if($DPISDB=="odbc") $arr_search_decoratehis_condition[] = "(LEFT(DEH_DATE, 10) >= '$search_deh_startdate')";
		elseif($DPISDB=="oci8") $arr_search_decoratehis_condition[] = "(SUBSTR(DEH_DATE, 1, 10) >= '$search_deh_startdate')";
		elseif($DPISDB=="mysql") $arr_search_decoratehis_condition[] = "(LEFT(DEH_DATE, 10) >= '$search_deh_startdate')";
		$search_deh_startdate = show_date_format($search_deh_startdate, 1);
	} // end if
	if(trim($search_deh_enddate)){
		$search_deh_enddate =  save_date($search_deh_enddate);
		if($DPISDB=="odbc") $arr_search_decoratehis_condition[] = "(LEFT(DEH_DATE, 10) <= '$search_deh_enddate')";
		elseif($DPISDB=="oci8") $arr_search_decoratehis_condition[] = "(SUBSTR(DEH_DATE, 1, 10) <= '$search_deh_enddate')";
		elseif($DPISDB=="mysql") $arr_search_decoratehis_condition[] = "(LEFT(DEH_DATE, 10) <= '$search_deh_enddate')";
		$search_deh_enddate = show_date_format($search_deh_enddate, 1);
	} // end if
	if(trim($search_dc_code)) $arr_search_decoratehis_condition[] = "(DC_CODE = '$search_dc_code')";

	if(count($arr_search_decoratehis_condition)){
		$cmd = " select distinct PER_ID from PER_DECORATEHIS where ". implode(" and ", $arr_search_decoratehis_condition) ." order by PER_ID";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$count = 0;
		while($data = $db_dpis->get_array()) { 
			$count++;
			if ($count < 1000) $arr_decoratehis[] = $data[PER_ID];
			elseif ($count < 2000) $arr_decoratehis2[] = $data[PER_ID];
			elseif ($count < 3000) $arr_decoratehis3[] = $data[PER_ID];
			elseif ($count < 4000) $arr_decoratehis4[] = $data[PER_ID];
			elseif ($count < 5000) $arr_decoratehis5[] = $data[PER_ID];
			elseif ($count < 6000) $arr_decoratehis6[] = $data[PER_ID];
			elseif ($count < 7000) $arr_decoratehis7[] = $data[PER_ID];
			elseif ($count < 8000) $arr_decoratehis8[] = $data[PER_ID];
			elseif ($count < 9000) $arr_decoratehis9[] = $data[PER_ID];
			elseif ($count < 10000) $arr_decoratehis10[] = $data[PER_ID];
			elseif ($count < 11000) $arr_decoratehis11[] = $data[PER_ID];
			elseif ($count < 12000) $arr_decoratehis12[] = $data[PER_ID];
			elseif ($count < 13000) $arr_decoratehis13[] = $data[PER_ID];
			elseif ($count < 14000) $arr_decoratehis14[] = $data[PER_ID];
			elseif ($count < 15000) $arr_decoratehis15[] = $data[PER_ID];
			else $arr_decoratehis16[] = $data[PER_ID];
		}
		
		if (count($arr_decoratehis16)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_decoratehis) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis3) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis5) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis7) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis9) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis11) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis13) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis14) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis15) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis16) .")))";
		elseif (count($arr_decoratehis15)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_decoratehis) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis3) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis5) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis7) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis9) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis11) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis13) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis14) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis15) .")))";
		elseif (count($arr_decoratehis14)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_decoratehis) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis3) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis5) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis7) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis9) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis11) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis13) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis14) .")))";
		elseif (count($arr_decoratehis13)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_decoratehis) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis3) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis5) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis7) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis9) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis11) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis13) .")))";
		elseif (count($arr_decoratehis12)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_decoratehis) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis3) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis5) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis7) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis9) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis11) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis12) .")))";
		elseif (count($arr_decoratehis11)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_decoratehis) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis3) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis5) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis7) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis9) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis11) .")))";
		elseif (count($arr_decoratehis10)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_decoratehis) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis3) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis5) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis7) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis9) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis10) .")))";
		elseif (count($arr_decoratehis9)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_decoratehis) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis3) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis5) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis7) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis9) .")))";
		elseif (count($arr_decoratehis8)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_decoratehis) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis3) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis5) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis7) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis8) .")))";
		elseif (count($arr_decoratehis7)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_decoratehis) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis3) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis5) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis7) .")))";
		elseif (count($arr_decoratehis6)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_decoratehis) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_decoratehis3) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis4) .")) or 
																															(a.PER_ID in (". implode(",", $arr_decoratehis5) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis6) .")))";
		elseif (count($arr_decoratehis5)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_decoratehis) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_decoratehis3) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis4) .")) or 
																															(a.PER_ID in (". implode(",", $arr_decoratehis5) .")))";
		elseif (count($arr_decoratehis4)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_decoratehis) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_decoratehis3) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis4) .")))";
		elseif (count($arr_decoratehis3)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_decoratehis) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_decoratehis3) .")))";
		elseif (count($arr_decoratehis2)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_decoratehis) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis2) .")))";
		else $arr_search_condition[] = "(a.PER_ID in (". implode(",", $arr_decoratehis) ."))";
	} // end if

	/* ======================================================== */

	$search_condition = "";
	if(count($arr_search_condition)) $search_condition = " where " . implode(" and ", $arr_search_condition);
	if($DPISDB=="oci8") $search_condition = str_replace(" where ", " and ", $search_condition);

	$org_cond = "";
	if ($POSITION_NO_CHAR=="Y") $org_cond = ", f.ORG_SEQ_NO $SortType[$order_by], f.ORG_CODE $SortType[$order_by]";
	if($order_by==1){	// เลขที่ตำแหน่ง
		if($search_per_type==1){
			$order_str = "g.ORG_SEQ_NO, g.ORG_CODE, a.DEPARTMENT_ID $SortType[$order_by]".$org_cond.", b.POS_NO_NAME $SortType[$order_by] ,to_number(replace(b.POS_NO,'-','')) $SortType[$order_by] ";
		}elseif($search_per_type==2){
			$order_str = "g.ORG_SEQ_NO, g.ORG_CODE, a.DEPARTMENT_ID $SortType[$order_by]".$org_cond.", c.POEM_NO_NAME $SortType[$order_by] ,to_number(replace(c.POEM_NO,'-','')) $SortType[$order_by] ";
		}elseif($search_per_type==3){
			$order_str = "g.ORG_SEQ_NO, g.ORG_CODE, a.DEPARTMENT_ID $SortType[$order_by]".$org_cond.", d.POEMS_NO_NAME $SortType[$order_by] ,to_number(replace(d.POEMS_NO,'-','')) $SortType[$order_by] ";
		} elseif($search_per_type==4){
			$order_str = "g.ORG_SEQ_NO, g.ORG_CODE, a.DEPARTMENT_ID $SortType[$order_by]".$org_cond.", e.POT_NO_NAME $SortType[$order_by] ,to_number(replace(e.POT_NO,'-','')) $SortType[$order_by] ";
		}
  	}elseif($order_by==2) {	//ชื่อ-สกุล
		if($DPISDB=="oci8"){
			$order_str = "NLSSORT(a.PER_NAME,'NLS_SORT=THAI_DICTIONARY') $SortType[$order_by], NLSSORT(a.PER_SURNAME,'NLS_SORT=THAI_DICTIONARY') $SortType[$order_by] ";
		}else{
			$order_str = "a.PER_NAME $SortType[$order_by] ,a.PER_SURNAME $SortType[$order_by] ";
		}
  	} elseif($order_by==3) {	//ตำแหน่งในการบริหารงาน
		$order_str = "g.ORG_SEQ_NO, g.ORG_CODE, a.DEPARTMENT_ID, h.PM_SEQ_NO, i.LEVEL_SEQ_NO $SortType[$order_by], a.LEVEL_NO $SortType[$order_by], 
									NLSSORT(a.PER_NAME,'NLS_SORT=THAI_DICTIONARY'), NLSSORT(a.PER_SURNAME,'NLS_SORT=THAI_DICTIONARY'), a.PER_SALARY $SortType[$order_by], 
				   				SUBSTR(trim(a.PER_STARTDATE), 1, 10), SUBSTR(trim(a.PER_BIRTHDATE), 1, 10)";
  	}elseif($order_by==4) {	//ตำแหน่งในสายงาน
		if($search_per_type==1){
			$order_str = "b.PL_CODE $SortType[$order_by] ";
		}elseif($search_per_type==2){
			$order_str = "c.PN_CODE $SortType[$order_by]";
		}elseif($search_per_type==3){
			$order_str = "d.EP_CODE $SortType[$order_by]";
		} elseif($search_per_type==4){
			$order_str = "e.TP_CODE $SortType[$order_by]";
		}
  	} elseif($order_by==5) {	//ระดับตำแหน่ง
		$order_str = "a.LEVEL_NO ".$SortType[$order_by];
	} elseif($order_by==6){	//สำนัก / กอง
		$order_str = "g.ORG_SEQ_NO, g.ORG_CODE, a.DEPARTMENT_ID $SortType[$order_by]".$org_cond.", b.ORG_ID $SortType[$order_by]";
  	}

	if($DPISDB=="odbc"){	
		if($search_per_type==1){
			$cmd = " select 	a.PER_ID, a.PN_CODE, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, a.PER_TYPE, a.PER_FILE_NO, 
											a.POS_ID, b.POS_NO_NAME, b.POS_NO, b.PL_CODE, b.ORG_ID, b.PT_CODE
							 from 		(	
												(
													PER_PERSONAL a
													left join PER_POSITION b on (a.POS_ID=b.POS_ID)	
												)	left join PER_ORG f on (b.ORG_ID=f.ORG_ID)
											)	left join PER_ORG g on (a.DEPARTMENT_ID=g.ORG_ID)
							$search_condition
							 order by $order_str  ";
					if($SESS_ORG_STRUCTURE==1) { $cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd); }
		}elseif($search_per_type==2){
			$cmd = " select 	a.PER_ID, a.PN_CODE, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, a.PER_TYPE, a.PER_FILE_NO, 
											a.POEM_ID as POS_ID, c.POEM_NO_NAME as POS_NO_NAME, c.POEM_NO as POS_NO, c.PN_CODE as PL_CODE, c.ORG_ID
							 from 		(	
												(
													PER_PERSONAL a
													left join PER_POS_EMP c on (a.POEM_ID=c.POEM_ID)	
												)	left join PER_ORG f on (c.ORG_ID=f.ORG_ID)
											)	left join PER_ORG g on (a.DEPARTMENT_ID=g.ORG_ID)
							$search_condition
							 order by $order_str  ";
					if($SESS_ORG_STRUCTURE==1) { $cmd = str_replace("c.ORG_ID", "a.ORG_ID", $cmd); }
		}elseif($search_per_type==3){
			$cmd = " select 	a.PER_ID, a.PN_CODE, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, a.PER_TYPE, a.PER_FILE_NO, 
											a.POEMS_ID as POS_ID, d.POEMS_NO_NAME as POS_NO_NAME, d.POEMS_NO as POS_NO, d.EP_CODE as PL_CODE, d.ORG_ID
							 from 		(	
												(
													PER_PERSONAL a
													left join PER_POS_EMPSER d on (a.POEMS_ID=d.POEMS_ID)	
												)	left join PER_ORG f on (d.ORG_ID=f.ORG_ID)
											)	left join PER_ORG g on (a.DEPARTMENT_ID=g.ORG_ID)
							$search_condition
							 order by $order_str  ";
					if($SESS_ORG_STRUCTURE==1) { $cmd = str_replace("d.ORG_ID", "a.ORG_ID", $cmd); }
		} elseif($search_per_type==4){
			$cmd = " select 	a.PER_ID, a.PN_CODE, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, a.PER_TYPE, a.PER_FILE_NO, 
											a.POT_ID as POS_ID, e.POT_NO_NAME as POS_NO_NAME, e.POT_NO as POS_NO, e.TP_CODE as PL_CODE, e.ORG_ID
							 from 		(	
												(
													PER_PERSONAL a
													left join PER_POS_TEMP e on (a.POT_ID=e.POT_ID)	
												)	left join PER_ORG f on (e.ORG_ID=f.ORG_ID)
											)	left join PER_ORG g on (a.DEPARTMENT_ID=g.ORG_ID)
							$search_condition
									 order by $order_str  ";
						if($SESS_ORG_STRUCTURE==1) { $cmd = str_replace("e.ORG_ID", "a.ORG_ID", $cmd); }
		} // end if
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		if($search_per_type==1){
			$cmd = " select 		a.PER_ID, a.PN_CODE, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, a.PER_TYPE, a.PER_FILE_NO, 
							  					a.POS_ID, b.POS_NO_NAME, b.POS_NO, b.PL_CODE, b.ORG_ID, b.PT_CODE
							 from 			PER_PERSONAL a, PER_POSITION b, PER_ORG f, PER_ORG g, PER_MGT h, PER_LEVEL i
							 where 		a.POS_ID=b.POS_ID(+) and a.DEPARTMENT_ID=g.ORG_ID and b.ORG_ID=f.ORG_ID(+) and b.PM_CODE=h.PM_CODE(+) and a.LEVEL_NO=i.LEVEL_NO(+)
												$search_condition
							 order by $order_str ";
				if($SESS_ORG_STRUCTURE==1) { $cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd); }
		}elseif($search_per_type==2){
			$cmd = " select 		a.PER_ID, a.PN_CODE, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, a.PER_TYPE, a.PER_FILE_NO, 
												a.POEM_ID as POS_ID, c.POEM_NO_NAME as POS_NO_NAME, c.POEM_NO as POS_NO, c.PN_CODE as PL_CODE, c.ORG_ID
							 from 			PER_PERSONAL a, PER_POS_EMP c, PER_ORG f, PER_ORG g 
							 where 		a.POEM_ID=c.POEM_ID(+) and a.DEPARTMENT_ID=g.ORG_ID and c.ORG_ID=f.ORG_ID(+)
												$search_condition
							 order by $order_str ";
			if($SESS_ORG_STRUCTURE==1) { $cmd = str_replace("c.ORG_ID", "a.ORG_ID", $cmd); }
		}elseif($search_per_type==3){
			$cmd = " select 		a.PER_ID, a.PN_CODE, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, a.PER_TYPE, a.PER_FILE_NO, 
												a.POEMS_ID as POS_ID, d.POEMS_NO_NAME as POS_NO_NAME, d.POEMS_NO as POS_NO, d.EP_CODE as PL_CODE, d.ORG_ID
							 from 			PER_PERSONAL a, PER_POS_EMPSER d, PER_ORG f, PER_ORG g 
							 where 		a.POEMS_ID=d.POEMS_ID(+) and a.DEPARTMENT_ID=g.ORG_ID and d.ORG_ID=f.ORG_ID(+) 
												$search_condition
							 order by $order_str ";
			if($SESS_ORG_STRUCTURE==1) { $cmd = str_replace("d.ORG_ID", "a.ORG_ID", $cmd); }
		} elseif($search_per_type==4){
			$cmd = " select 		a.PER_ID, a.PN_CODE, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, a.PER_TYPE, a.PER_FILE_NO, 
														a.POT_ID as POS_ID, e.POT_NO_NAME as POS_NO_NAME, e.POT_NO as POS_NO, e.TP_CODE as PL_CODE, e.ORG_ID
									  from 			PER_PERSONAL a, PER_POS_TEMP e, PER_ORG f, PER_ORG g
									  where 		a.POT_ID=e.POT_ID(+) and a.DEPARTMENT_ID=g.ORG_ID and e.ORG_ID=f.ORG_ID(+) 
														$search_condition
									  order by $order_str ";
			if($SESS_ORG_STRUCTURE==1) { $cmd = str_replace("e.ORG_ID", "a.ORG_ID", $cmd); }
		} // end if
	}elseif($DPISDB=="mysql"){	
		if($search_per_type==1){
			$cmd = " select 	a.PER_ID, a.PN_CODE, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, a.PER_TYPE, a.PER_FILE_NO, 
											a.POS_ID, b.POS_NO_NAME, b.POS_NO, b.PL_CODE, b.ORG_ID, b.PT_CODE
							 from 		(	
												(
													PER_PERSONAL a
													left join PER_POSITION b on (a.POS_ID=b.POS_ID)	
												)	left join PER_ORG f on (b.ORG_ID=f.ORG_ID)
											)	left join PER_ORG g on (a.DEPARTMENT_ID=g.ORG_ID)
							$search_condition
							 order by $order_str  ";
				if($SESS_ORG_STRUCTURE==1) { $cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd); }
		}elseif($search_per_type==2){
			$cmd = " select 	a.PER_ID, a.PN_CODE, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, a.PER_TYPE, a.PER_FILE_NO, 
											a.POEM_ID as POS_ID, c.POEM_NO_NAME as POS_NO_NAME, c.POEM_NO as POS_NO, c.PN_CODE as PL_CODE, c.ORG_ID
							 from 		(	
												(
													PER_PERSONAL a
													left join PER_POS_EMP c on (a.POEM_ID=c.POEM_ID)	
												)	left join PER_ORG f on (c.ORG_ID=f.ORG_ID)
											)	left join PER_ORG g on (a.DEPARTMENT_ID=g.ORG_ID)
							$search_condition
							 order by $order_str  ";
				if($SESS_ORG_STRUCTURE==1) { $cmd = str_replace("c.ORG_ID", "a.ORG_ID", $cmd); }
		}elseif($search_per_type==3){
			$cmd = " select 	a.PER_ID, a.PN_CODE, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, a.PER_TYPE, a.PER_FILE_NO, 
											a.POEMS_ID as POS_ID, d.POEMS_NO_NAME as POS_NO_NAME, d.POEMS_NO as POS_NO, d.EP_CODE as PL_CODE, d.ORG_ID
							 from 		(	
												(
													PER_PERSONAL a
													left join PER_POS_EMPSER d on (a.POEMS_ID=d.POEMS_ID)	
												)	left join PER_ORG f on (d.ORG_ID=f.ORG_ID)
											)	left join PER_ORG g on (a.DEPARTMENT_ID=g.ORG_ID)
							$search_condition
							 order by $order_str  ";
				if($SESS_ORG_STRUCTURE==1) { $cmd = str_replace("d.ORG_ID", "a.ORG_ID", $cmd); }
		} elseif($search_per_type==4){
			$cmd = " select 	a.PER_ID, a.PN_CODE, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, a.PER_TYPE, a.PER_FILE_NO, 
											a.POT_ID as POS_ID, e.POT_NO_NAME as POS_NO_NAME, e.POT_NO as POS_NO, e.TP_CODE as PL_CODE, e.ORG_ID
							 from 		(	
												(
													PER_PERSONAL a
													left join PER_POS_TEMP e on (a.POT_ID=e.POT_ID)	
												)	left join PER_ORG f on (e.ORG_ID=f.ORG_ID)
											)	left join PER_ORG g on (a.DEPARTMENT_ID=g.ORG_ID)
											$search_condition
							 order by $order_str  ";
					if($SESS_ORG_STRUCTURE==1) { $cmd = str_replace("e.ORG_ID", "a.ORG_ID", $cmd); }
		} // end if
	} // end if

	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();

	if($count_data){
		$head_text1 = implode(",", $heading_text);
		$head_width1 = implode(",", $heading_width);
		$head_align1 = implode(",", $heading_align);
		$col_function = implode(",", $column_function);
	    if ($FLAG_RTF) {
			$RTF->set_default_font($font, 14);
			$RTF->add_header("", 0, false);	// header default
			$RTF->add_footer("", 0, false);		// footer default
			
	//		echo "$head_text1<br>";
			$tab_align = "center";
			$result = $RTF->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", $fmtTableHeader_col_idx, $fmtTableHeader_bgcol_idx, $COLUMN_FORMAT, "", false, $tab_align);
//			print_header();
		}else{
			$pdf->AutoPageBreak = false;
	//		echo "$head_text1<br>";
			$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", "0066CC", "EEEEFF", $COLUMN_FORMAT, $col_function);
		}
		if (!$result) echo "****** error ****** on open table for $table<br>";

		$data_count = $data_row = 0;
		while($data = $db_dpis->get_array()){
			$data_count++;
			$data_row++;			

			$PER_ID = trim($data[PER_ID]);
			$LEVEL_NO = trim($data[LEVEL_NO]);
			$PER_NAME = $data[PER_NAME];
			$PER_SURNAME = $data[PER_SURNAME];
			$FULLNAME = "$PER_NAME $PER_SURNAME";
			
			$cmd = "select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$LEVEL_NO'";
			$db_dpis2->send_cmd($cmd);
			$data2=$db_dpis2->get_array();
			$LEVEL_NAME=trim($data2[LEVEL_NAME]);
			$POSITION_LEVEL = $data2[POSITION_LEVEL];
			if (!$POSITION_LEVEL) $POSITION_LEVEL = $LEVEL_NAME;
			
			$PN_CODE = trim($data[PN_CODE]);
			if ($PN_CODE) {
				$cmd = "	select PN_NAME, PN_SHORTNAME from PER_PRENAME where PN_CODE='$PN_CODE'";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$PN_NAME = $data2[PN_NAME];
				$PN_SHORTNAME = $data2[PN_SHORTNAME];
			}
					
			$PER_TYPE = $data[PER_TYPE];
			$PER_FILE_NO = trim($data[PER_FILE_NO]);
			$POS_ID = $data[POS_ID];
			$POS_NO_NAME = trim($data[POS_NO_NAME]);
			if (substr($POS_NO_NAME,0,4)=="กปด.")
				$POS_NO = $POS_NO_NAME." ".trim($data[POS_NO]);
			else
				$POS_NO = $POS_NO_NAME.trim($data[POS_NO]);
			$PL_CODE = $data[PL_CODE];
			if ($PER_TYPE == 1) {
				$cmd = " select PL_NAME from PER_LINE where PL_CODE='$PL_CODE' ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$PL_NAME = $data2[PL_NAME];
				
				$PT_CODE = trim($data[PT_CODE]);
				$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE)='$PT_CODE' ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$PT_NAME = trim($data2[PT_NAME]);

				$PL_NAME = (trim($PL_NAME))? ("$PL_NAME". $POSITION_LEVEL . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?" $PT_NAME":"")) : "".$LEVEL_NAME;
			} elseif ($PER_TYPE == 2) {
				$cmd = " select PN_NAME as PL_NAME from PER_POS_NAME where PN_CODE='$PL_CODE' ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$PL_NAME = $data2[PL_NAME];
				$PL_NAME = (trim($PL_NAME))? "$PL_NAME".$POSITION_LEVEL : "".$LEVEL_NAME;
			} elseif ($PER_TYPE == 3) {
				$cmd = " select EP_NAME as PL_NAME from PER_EMPSER_POS_NAME where EP_CODE='$PL_CODE' ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$PL_NAME = $data2[PL_NAME];
				$PL_NAME = (trim($PL_NAME))? "$PL_NAME".$POSITION_LEVEL : "".$LEVEL_NAME;
			}	elseif ($PER_TYPE == 4) {
				$cmd = " select TP_NAME as PL_NAME from PER_TEMP_POS_NAME where TP_CODE='$PL_CODE' ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$PL_NAME = $data2[PL_NAME];
				$PL_NAME = (trim($PL_NAME))? "$PL_NAME".$POSITION_LEVEL : "".$LEVEL_NAME;
			}		

			$ORG_ID = $data[ORG_ID];
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_NAME = $data2[ORG_NAME];

			$arr_data = (array) null;
			$arr_data[] = $POS_NO;
			$arr_data[] = (($PN_SHORTNAME)?$PN_SHORTNAME:$PN_NAME).$PER_NAME." ".$PER_SURNAME;
			$arr_data[] = $PL_NAME;
			$arr_data[] = $ORG_NAME;

		    if ($FLAG_RTF)
				$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
			else
				$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", "000000", "");
		} // end while
	}else{
	    if ($FLAG_RTF){
			$result = $RTF->add_text_line("********** ไม่มีข้อมูล **********", 7, "", "C", "", "14", "b", 0, 0);
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
		}else{
			$pdf->SetFont($font,'b','',16);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
			$pdf->Cell(200,10,"********** ไม่มีข้อมูล **********",0,1,'C');
		}
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