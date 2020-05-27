<?
	include("../../php_scripts/connect_database.php");
	include("../php_scripts/pdf_wordarray_thaicut.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	 if ($FLAG_RTF) {
		include ("rtf_setvar.php");	// กำหนดตัวแปรค่าสี set_of_colors
		require("../../RTF/rtf_class.php");
	} else	 {
		define('FPDF_FONTPATH','../../PDF/font/');
		include ("../../PDF/fpdf.php");
		include ("../../PDF/pdf_extends_DPIS.php");
	}

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	ini_set("max_execution_time", $max_execution_time);
	
	$report_title = $report_title = trim(iconv("utf-8","tis620",urldecode(trim($report_title))));//trim($report_title);
	$report_code = "";
	

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();
	$company_name = "";
	if ($FLAG_RTF) {
		$sum_w = array_sum($heading_width);
		for($h = 0; $h < count($heading_width); $h++) {
			$heading_width[$h] = $heading_width[$h] / $sum_w * 100;
		}
		$fname= "rpt_master_table_pos_emper.rtf";
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
	$heading_width[0] = "8";
	$heading_width[1] = "10";
	$heading_width[2] = "14";
	$heading_width[3] = "10";
	$heading_width[4] = "14";
	$heading_width[5] = "12";
	$heading_width[6] = "12";
	$heading_width[7] = "12";
	$heading_width[8] = "8";
	}else{
		$heading_width[0] = "20";
	$heading_width[1] = "30";
	$heading_width[2] = "40";
	$heading_width[3] = "30";
	$heading_width[4] = "40";
	$heading_width[5] = "35";
	$heading_width[6] = "35";
	$heading_width[7] = "35";
	$heading_width[8] = "20";
	}
	$heading_text[0] = "$POS_NO_TITLE";
	$heading_text[1] = "กลุ่มงาน";
	$heading_text[2] = "ชื่อตำแหน่ง";
	$heading_text[3] = "อัตราค่าจ้าง";
	$heading_text[4] = "$ORG_TITLE";
	$heading_text[5] = "$ORG_TITLE1";
	$heading_text[6] = "$ORG_TITLE2";
	$heading_text[7] = "ผู้ครองตำแหน่ง";
	$heading_text[8] = "$ACTIVE_TITLE";
		
	$heading_align = array('C','C','C','C','C','C','C','C','C');
		
  	if(trim($search_poem_no_min)){ 
		if($DPISDB=="odbc") $arr_search_condition[] = "(CLng(POEMS_NO) >= $search_poem_no_min)";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(to_number(replace(POEMS_NO,'-','')) >= $search_poem_no_min)";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(POEMS_NO >= $search_poem_no_min)";
	} // end if
  	if(trim($search_poem_no_max)){ 
		if($DPISDB=="odbc") $arr_search_condition[] = "(CLng(POEMS_NO) <= $search_poem_no_max)";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(to_number(replace(POEMS_NO,'-','')) <= $search_poem_no_max)";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(POEMS_NO <= $search_poem_no_max)";
	} // end if
	if(trim($search_poem_no_min) && trim($search_poem_no_max)){
		$print_search_condition[] = "เลขที่ตำแหน่ง : $search_poem_no_min ถึง $search_poem_no_max";
	}elseif(trim($search_poem_no_min)){
		$print_search_condition[] = "เลขที่ตำแหน่ง : มากกว่า $search_poem_no_min";
	}elseif(trim($search_poem_no_max)){
		$print_search_condition[] = "เลขที่ตำแหน่ง : น้อยกว่า $search_poem_no_max";
	} // end if
	if(trim($search_level_no)){ 
		$arr_search_condition[] = "(trim(LEVEL_NO) = '". trim($search_level_no) ."')";
		$cmd = " select LEVEL_NAME from PER_LEVEL where trim(LEVEL_NO)='".trim($search_level_no)."' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$search_level_name = $data[LEVEL_NAME];
		$print_search_condition[] = "กลุ่มงาน : $search_level_name";
	} // end if
	if(trim($search_ep_code)){ 
		$arr_search_condition[] = "(trim(a.EP_CODE) = '". trim($search_ep_code) ."')";
		$print_search_condition[] = "ชื่อตำแหน่ง : $search_ep_name";
	} // end if
  	if(trim($search_poem_salary_min)) $arr_search_condition[] = "(POEM_MIN_SALARY >= $search_poem_salary_min)";
  	if(trim($search_poem_salary_max)) $arr_search_condition[] = "(POEM_MAX_SALARY <= $search_poem_salary_max)";
	if(trim($search_poem_salary_min) && trim($search_poem_salary_max)){
		$print_search_condition[] = "อัตราค่าจ้าง : $search_poem_salary_min ถึง $search_poem_salary_max";
	}elseif(trim($search_poem_salary_min)){
		$print_search_condition[] = "อัตราค่าจ้าง : มากกว่า $search_poem_salary_min";
	}elseif(trim($search_poem_salary_max)){
		$print_search_condition[] = "อัตราค่าจ้าง : น้อยกว่า $search_poem_salary_max";
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
		unset($arr_org);
		while($data = $db_dpis->get_array()) $arr_org[] = $data[ORG_ID];
		$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode(",", $arr_org) ."))";
		$print_search_condition[] = "$MINISTRY_TITLE : $search_ministry_name";
	}elseif($PV_CODE){
		$cmd = " select 	a.ORG_ID as MINISTRY_ID, b.ORG_ID as DEPARTMENT_ID, c.ORG_ID
						 from   	PER_ORG a, PER_ORG b, PER_ORG c
						 where  	a.OL_CODE='01' and b.OL_CODE='02' and c.OL_CODE='03' and c.PV_CODE='$PV_CODE' 
						 				and a.ORG_ID=b.ORG_ID_REF and b.ORG_ID=c.ORG_ID_REF
						 order by a.ORG_ID, b.ORG_ID, c.ORG_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		unset($arr_org);
		while($data = $db_dpis->get_array()) $arr_org[] = $data[ORG_ID];
		$arr_search_condition[] = "(ORG_ID in (". implode(",", $arr_org) ."))";
		$print_search_condition[] = "จังหวัด : $PV_NAME";
	}

	if(trim($search_org_id_1)){ 
		$arr_search_condition[] = "(ORG_ID_1 = $search_org_id_1)";
		$print_search_condition[] = "$ORG_TITLE1 : $search_org_name_1";
	} // end if
	if(trim($search_org_id_2)){ 
		$arr_search_condition[] = "(ORG_ID_2 = $search_org_id_2)";
		$print_search_condition[] = "$ORG_TITLE2 : $search_org_name_2";
	} // end if
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

	if(trim($search_pos_status) == 1) $arr_search_condition[] = "(a.POEM_STATUS = 1)";
	if(trim($search_pos_status) == 2) $arr_search_condition[] = "(a.POEM_STATUS = 2)";
	$search_pps = implode(",", $search_pps_code);	
	if($search_pps){$arr_search_condition[] = "(a.PPS_CODE in($search_pps))";}else{$arr_search_condition[] = "(a.PPS_CODE in(1,2,3,4))";}	
	if($search_pos_status == 0){
		$print_search_condition[] = "สถานะ : ทั้งหมด";
	}elseif($search_pos_status == 1){
		$print_search_condition[] = "สถานะ : ใช้งาน";
	}elseif($search_pos_status == 2){
		$print_search_condition[] = "สถานะ : ยกเลิก";
	} // end if
		
	$search_condition = "";
	if(count($arr_search_condition)) $search_condition = " and " . implode(" and ", $arr_search_condition);

	if($DPISDB=="odbc"){
		$cmd = "	select		POEMS_ID, POEMS_NO, a.EP_CODE, b.EP_NAME, b.LEVEL_NO, POEM_MIN_SALARY, POEM_MAX_SALARY, 
											ORG_ID, ORG_ID_1, ORG_ID_2, POEM_STATUS, POEMS_NO_NAME
							from		PER_POS_EMPSER a, PER_EMPSER_POS_NAME b
							where		a.EP_CODE=b.EP_CODE
											$search_condition
							order by IIf(IsNull(POEMS_NO), 0, CLng(POEMS_NO)) ";
	}elseif($DPISDB=="oci8"){
		$cmd = "	select		POEMS_ID, POEMS_NO, a.EP_CODE, b.EP_NAME, b.LEVEL_NO, POEM_MIN_SALARY, POEM_MAX_SALARY, 
											ORG_ID, ORG_ID_1, ORG_ID_2, POEM_STATUS, POEMS_NO_NAME
							from		PER_POS_EMPSER a, PER_EMPSER_POS_NAME b
							where		a.EP_CODE=b.EP_CODE
											$search_condition
							order by to_number(replace(POEMS_NO,'-','')) ";
	}elseif($DPISDB=="mysql"){
		$cmd = "	select		POEMS_ID, POEMS_NO, a.EP_CODE, b.EP_NAME, b.LEVEL_NO, POEM_MIN_SALARY, POEM_MAX_SALARY, 
											ORG_ID, ORG_ID_1, ORG_ID_2, POEM_STATUS, POEMS_NO_NAME
							from		PER_POS_EMPSER a, PER_EMPSER_POS_NAME b
							where		a.EP_CODE=b.EP_CODE
											$search_condition
							order by POEMS_NO ";
	} // end if

	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	//echo $cmd;

	if($count_data){
		foreach($print_search_condition as $show_condition){
		    if ($FLAG_RTF) {
			$company_name .= "$show_condition";
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
		$RTF->set_company_name($company_name);
		$result = $RTF->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", $fmtTableHeader_col_idx, $fmtTableHeader_bgcol_idx, $COLUMN_FORMAT, "", false, $tab_align);
		} else {
		$pdf->AutoPageBreak = false;
		$pdf->SetFont($font,'',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", "0066CC", "EEEEFF", 0);
		           }
		if (!$result) echo "****** error ****** on open table for $table<br>";

		$data_count = $data_row = 0;
		while($data = $db_dpis->get_array()){
			$data_count++;
			$data_row++;			

			$POEMS_ID = trim($data[POEMS_ID]);
			$POEMS_NO = trim($data[POEMS_NO_NAME]).trim($data[POEMS_NO]);
			$EP_NAME = trim($data[EP_NAME]);
			$LEVEL_NO = trim($data[LEVEL_NO]);
			$POEM_MIN_SALARY = trim($data[POEM_MIN_SALARY]);
			$POEM_MAX_SALARY = trim($data[POEM_MAX_SALARY]);
			$POEM_SALARY = number_format($POEM_MIN_SALARY) . (trim($POEM_MAX_SALARY)?(" - ".number_format($POEM_MAX_SALARY)):"");
			$ORG_ID = trim($data[ORG_ID]);
			$ORG_ID_1 = trim($data[ORG_ID_1]);
			$ORG_ID_2 = trim($data[ORG_ID_2]);

			$cmd = " select LEVEL_NAME from PER_LEVEL where trim(LEVEL_NO)='".$LEVEL_NO."' ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$LEVEL_NAME = $data_dpis2[LEVEL_NAME];
			
			$ORG_NAME = "";
			if($ORG_ID){
				$cmd = " select ORG_NAME, ORG_SHORT from PER_ORG where OL_CODE='03' and ORG_ID=$ORG_ID ";
				$db_dpis2->send_cmd($cmd);
				$data_dpis2 = $db_dpis2->get_array();
				$ORG_NAME = trim($data_dpis2[ORG_SHORT]);
				if (!$ORG_NAME) $ORG_NAME = trim($data_dpis2[ORG_NAME]);
			}
			$ORG_NAME_1 = "";
			if($ORG_ID_1){
				$cmd = " select ORG_NAME, ORG_SHORT from PER_ORG where OL_CODE='04' and ORG_ID=$ORG_ID_1 ";
				$db_dpis2->send_cmd($cmd);
				$data_dpis2 = $db_dpis2->get_array();
				$ORG_NAME_1 = trim($data_dpis2[ORG_SHORT]);
				if (!$ORG_NAME_1) $ORG_NAME_1 = trim($data_dpis2[ORG_NAME]);
			}
			$ORG_NAME_2 = "";
			if($ORG_ID_2){
				$cmd = " select ORG_NAME, ORG_SHORT from PER_ORG where OL_CODE='05' and ORG_ID=$ORG_ID_2 ";
				$db_dpis2->send_cmd($cmd);
				$data_dpis2 = $db_dpis2->get_array();
				$ORG_NAME_2 = trim($data_dpis2[ORG_SHORT]);
				if (!$ORG_NAME_2) $ORG_NAME_2 = trim($data_dpis2[ORG_NAME]);
			}
			if($DPISDB=="odbc"){
				$cmd = " select 	a.PER_ID, b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_STATUS 
								 from 		PER_PERSONAL a
								 				left join PER_PRENAME b on (a.PN_CODE=b.PN_CODE)
								 where	a.POEMS_ID=$POEMS_ID and (a.PER_STATUS=1 or a.PER_STATUS=0) and a.PER_TYPE=3 ";
			}elseif($DPISDB=="oci8"){
				$cmd = " select 	a.PER_ID, b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_STATUS 
								 from 		PER_PERSONAL a, PER_PRENAME b
								 where	a.PN_CODE=b.PN_CODE(+) and a.POEMS_ID=$POEMS_ID and (a.PER_STATUS=1 or a.PER_STATUS=0) and a.PER_TYPE=3 ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select 	a.PER_ID, b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_STATUS 
								 from 		PER_PERSONAL a
								 				left join PER_PRENAME b on (a.PN_CODE=b.PN_CODE)
								 where	a.POEMS_ID=$POEMS_ID and (a.PER_STATUS=1 or a.PER_STATUS=0) and a.PER_TYPE=3 ";
			}
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
		//	$db_dpis2->show_error();
			$POS_PERSON = "$data_dpis2[PN_NAME]$data_dpis2[PER_NAME] $data_dpis2[PER_SURNAME]";
			if($data_dpis2[PER_ID]) $POS_PERSON .= (($data_dpis2[PER_STATUS]==0)?" (รอบรรจุ)":"");
	
			$arr_data = (array) null;
			$arr_data[] = "$POEMS_NO";
			$arr_data[] = "$LEVEL_NAME";
			$arr_data[] = "$EP_NAME";
			$arr_data[] = "$POEM_SALARY";
			$arr_data[] = "$ORG_NAME";
			$arr_data[] = "$ORG_NAME_1";
			$arr_data[] = "$ORG_NAME_2";
			$arr_data[] = "$POS_PERSON";
			$arr_data[] = "<*img*".(($data[POEM_STATUS]==1)?"../images/checkbox_check.jpg":"../images/checkbox_blank.jpg")."*img*>";

			$data_align = array("C", "L", "L", "C", "L", "L", "L", "L", "C");
			 if ($FLAG_RTF)
			 $result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
		    else	
			$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";

		} // end while
	}else{
	     if ($FLAG_RTF){
		$result = $RTF->add_text_line("********** ไม่มีข้อมูล **********", 7, "", "C", "", "14", "b", 0, 0);
		if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
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
        }
		$data_align = array("C", "C", "C", "C", "C", "C", "C", "C", "C");
		if ($FLAG_RTF)
	    $result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "16", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
		else		
		$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "16", "", "000000", "");
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