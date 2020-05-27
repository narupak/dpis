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
	
	$report_title = trim($report_title);
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
		$fname= "rpt_master_table_pos_type.rtf";
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
	$heading_width[0] = "7";
	$heading_width[1] = "53";
	$heading_width[2] = "10";
	$heading_width[3] = "10";
	$heading_width[4] = "10";
	$heading_width[5] = "10";
	}else{
	$heading_width[0] = "20";
	$heading_width[1] = "160";
	$heading_width[2] = "25";
	$heading_width[3] = "25";
	$heading_width[4] = "25";
	$heading_width[5] = "25";
	}
	$heading_text[0] = "$CODE_TITLE";
	$heading_text[1] = "$PT_TITLE";
	$heading_text[2] = "ตนเอง";
	$heading_text[3] = "ผู้บังคับบัญชา";
	$heading_text[4] = "เพื่อนร่วมงาน";
	$heading_text[5] = "ผู้ใต้บังคับบัญชา";
		
	$heading_align = array('C','C','C','C','C','C');
		
  	//if(trim($search_POS_TYPE)) $arr_search_condition[] = "(POS_TYPE LIKE '$search_POS_TYPE%')";
  	if(trim($search_POS_NAME)) $arr_search_condition[] = "(POS_NAME LIKE '%$search_POS_NAME%')";
	$search_condition = "";
	$search_condition_count = "";
	if(count($arr_search_condition)) {
		$search_condition = " and " . implode(" and ", $arr_search_condition);
		$search_condition_count = " where " . implode(" and ", $arr_search_condition);
	}
	$cmd ="select POS_TYPE, POS_NAME, SEFT_RATIO, CHIEF_RATIO, FRIEND_RATIO, SUB_RATIO from PER_POS_TYPE ";
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();

	
	foreach($print_search_condition as $show_condition){
		if ($FLAG_RTF) {
		$company_name = "$show_condition"." ";
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
			$arr_data[] = trim($data[POS_TYPE]);		
			$arr_data[] = trim($data[POS_NAME]);		
			$arr_data[] = $data[SEFT_RATIO];
			$arr_data[] = $data[CHIEF_RATIO];
			$arr_data[] = $data[FRIEND_RATIO];
			$arr_data[] = $data[SUB_RATIO];
			$arr_data[] = "<*img*".(($data[$arr_fields[2]]==1)?"../images/checkbox_check.jpg":"../images/checkbox_blank.jpg")."*img*>";

			$data_align = array("C", "L", "L", "C", "L", "L");
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
                 }
		$data_align = array("C", "C", "C", "C", "C", "C");
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