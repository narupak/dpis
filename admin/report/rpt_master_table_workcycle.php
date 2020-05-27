<?
	include("../../php_scripts/connect_database.php");
	include("../php_scripts/pdf_wordarray_thaicut.php");
	include ("../php_scripts/function_share.php");
	
	   if ($FLAG_RTF) {
		include ("rtf_setvar.php");	// กำหนดตัวแปรค่าสี set_of_colors
		require("../../RTF/rtf_class.php");
	} else	 {
		define('FPDF_FONTPATH','../../PDF/font/');
		include ("../../PDF/fpdf.php");
		include ("../../PDF/pdf_extends_DPIS.php");
	}
	
	ini_set("max_execution_time", $max_execution_time);
	
	$report_title = trim(iconv("utf-8","tis620",urldecode($report_title)));
	$report_code = "";
	

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();
	if ($FLAG_RTF) {
		$sum_w = array_sum($heading_width);
		for($h = 0; $h < count($heading_width); $h++) {
			$heading_width[$h] = $heading_width[$h] / $sum_w * 100;
		}
		$fname= $report_title.".rtf";
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
			$fname= "M1203.pdf";
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
	if ($FLAG_RTF) {
		$heading_width[0] = "10";
		$heading_width[1] = "30";
		$heading_width[2] = "20";
		$heading_width[3] = "20";
		$heading_width[4] = "20";
		$heading_width[5] = "20";
		$heading_width[6] = "20";
		$heading_width[7] = "20";
		$heading_width[8] = "20";
	}else{
		$heading_width[0] = "13";
		$heading_width[1] = "41";
		$heading_width[2] = "21";
		$heading_width[3] = "21";
		$heading_width[4] = "21";
		$heading_width[5] = "21";
		$heading_width[6] = "21";
		$heading_width[7] = "21";
		$heading_width[8] = "20";
	}	
	$heading_text[0] = "รหัส";
	$heading_text[1] = "ชื่อรอบ";
	$heading_text[2] = "เวลาเข้า";
	$heading_text[3] = "เวลาออก";
	$heading_text[4] = "เวลาที่ไม่สาย";
	$heading_text[5] = "สิ้นสุดเวลาสาย";
	$heading_text[6] = "เวลาเข้า\t(กรณีทำงานครึ่งวันหลัง)";
	$heading_text[7] = "เวลาออก\t(กรณีทำงานครึ่งวันแรก)";
	$heading_text[8] = "$ACTIVE_TITLE";
		
	$heading_align = array('C','C','C','C','C','C','C','C','C');
		
	

  	if(trim($search_code)) $arr_search_condition[] = "(a.WC_CODE like '$search_code%')";
  	if(trim($search_name)) $arr_search_condition[] = "(a.WC_NAME like '%$search_name%')";
	$search_condition = "";
	if(count($arr_search_condition)) $search_condition = " and " . implode(" and ", $arr_search_condition);

	$cmd = "	select		a.WC_CODE, a.WC_NAME, a.WC_START, a.WC_END, a.WC_ACTIVE,
                                a.WC_SEQ_NO,a.TIME_LEAVEEARLY,a.TIME_LEAVEAFTER,a.ON_TIME,
                                a.END_LATETIME
								from 		PER_WORK_CYCLE a
								WHERE a.WORKCYCLE_TYPE = 1 
								$search_condition			
								order by 	a.WC_SEQ_NO,a.WC_CODE, a.WC_NAME ";
	
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();

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
		$pdf->AutoPageBreak = false; 
	    $result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", "0066CC", "EEEEFF", 0);
		          }
	if (!$result) echo "****** error ****** on open table for $table<br>";

	if($count_data){

		$data_count = $data_row = 0;
		while($data = $db_dpis->get_array()){
			$data_count++;
			$data_row++;			

			$arr_data = (array) null;
			$arr_data[] = $data[WC_CODE];
			$arr_data[] = $data[WC_NAME];
			$arr_data[] = substr($data[WC_START],0,2).":".substr($data[WC_START],2,2)." น.";
			$arr_data[] = substr($data[WC_END],0,2).":".substr($data[WC_END],2,2)." น.";
			
			$DATA_P_EXTRATIME_SHOW ="";
			if($data[ON_TIME]){
				$DATA_P_EXTRATIME_SHOW = substr($data[ON_TIME],0,2).':'.substr($data[ON_TIME],2,2)." น.";
			}
			
			$arr_data[] = $DATA_P_EXTRATIME_SHOW;
		
			$DATA_P_TIMEOVERLATE_SHOW = "";
			if($data[END_LATETIME]){
				$DATA_P_TIMEOVERLATE_SHOW = substr($data[END_LATETIME],0,2).':'.substr($data[END_LATETIME],2,2)." น.";
			}
			
			$arr_data[] = $DATA_P_TIMEOVERLATE_SHOW;
			
			$DATA_TIME_LEAVEEARLY ="";
			if($data[TIME_LEAVEEARLY] != "0000"){
				$DATA_TIME_LEAVEEARLY = substr($data[TIME_LEAVEEARLY],0,2).":".substr($data[TIME_LEAVEEARLY],2,2)." น.";
			}
			
			$arr_data[] = $DATA_TIME_LEAVEEARLY;
        
			$DATA_TIME_LEAVEAFTER ="";
			if($data[TIME_LEAVEEARLY] != "0000"){
				$DATA_TIME_LEAVEAFTER = substr($data[TIME_LEAVEAFTER],0,2).":".substr($data[TIME_LEAVEAFTER],2,2)." น.";
			}
			
			$arr_data[] = $DATA_TIME_LEAVEAFTER;
        
			
			
			$arr_data[] = "<*img*".(($data[WC_ACTIVE]==1)?"../images/checkbox_check.jpg":"../images/checkbox_blank.jpg")."*img*>";

			$data_align = array("C", "L", "C", "C", "C", "C", "C", "C", "C");
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
			$pdf->Output($fname,'D');	
		}
	ini_set("max_execution_time", 30);
?>