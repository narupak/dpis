<?
	include("../../php_scripts/connect_database.php");
	include("../php_scripts/pdf_wordarray_thaicut.php");
	include ("../php_scripts/function_share.php");

	if ($FLAG_RTF) {
		include ("rtf_setvar.php");	// ��˹�����ä���� set_of_colors
		require("../../RTF/rtf_class.php");
	    } else	 {
		define('FPDF_FONTPATH','../../PDF/font/');
		include ("../../PDF/fpdf.php");
		include ("../../PDF/pdf_extends_DPIS.php");
	    }

	ini_set("max_execution_time", $max_execution_time);
	

	$report_title = trim($report_title);
	$report_code = "";
	

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();
		if ($FLAG_RTF) {
		$sum_w = array_sum($heading_width);
		for($h = 0; $h < count($heading_width); $h++) {
			$heading_width[$h] = $heading_width[$h] / $sum_w * 100;
	   }
		$fname= "rpt_kpi_line_competence.rtf";
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
	if ($FLAG_RTF) {
	$heading_width[0] = "10";
	$heading_width[1] = "40";
	$heading_width[2] = "40";
	$heading_width[3] = "10";
	}else{
	$heading_width[0] = "20";
	$heading_width[1] = "80";
	$heading_width[2] = "80";
	$heading_width[3] = "20";
	}
	//new format**************************************************
    $heading_text[0] = "�ӴѺ";
	$heading_text[1] = "$PL_TITLE";
	$heading_text[2] ="$COMPETENCE_TITLE";
	$heading_text[3] = "$ACTIVE_TITLE";
	
	$heading_align = array('C','C','C','C');

  	if(trim($search_pl_code)) $arr_search_condition[] = "(a.PL_CODE like '$search_pl_code%')";
  	if(trim($search_cp_code)) $arr_search_condition[] = "(a.CP_CODE like '%$search_cp_code%')";
	if(trim($ORG_ID)) { 
    	$arr_search_condition[] = "(ORG_ID = $ORG_ID)";
   	}elseif($search_department_id){
		$arr_search_condition[] = "(a.DEPARTMENT_ID = $search_department_id)";
	}elseif($search_ministry_id){
		$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$search_ministry_id ";
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_org[] = $data[ORG_ID];
		$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode(",", $arr_org) ."))";
	} // end if
	$search_condition = "";
	if(count($arr_search_condition)) $search_condition = " and " . implode(" and ", $arr_search_condition);

	if($DPISDB=="odbc"){
		$cmd = "	select		a.PL_CODE, a.CP_CODE, LC_ACTIVE, PL_NAME, CP_NAME
							from		PER_LINE_COMPETENCE a, PER_LINE b, PER_COMPETENCE c 
							where 		a.PL_CODE=trim(b.PL_CODE) and a.CP_CODE=trim(c.CP_CODE)
								$search_condition 
							order by 	a.PL_CODE, a.CP_CODE	";
	}elseif($DPISDB=="oci8"){
		$cmd = "	select		a.PL_CODE, a.CP_CODE, LC_ACTIVE, PL_NAME, CP_NAME
							from		PER_LINE_COMPETENCE a, PER_LINE b, PER_COMPETENCE c 
							where 		a.PL_CODE=trim(b.PL_CODE) and a.CP_CODE=trim(c.CP_CODE)
								$search_condition 
							order by 	a.PL_CODE, a.CP_CODE	";
	}elseif($DPISDB=="mysql"){
		$cmd = "	select		a.PL_CODE, a.CP_CODE, LC_ACTIVE, PL_NAME, CP_NAME
							from		PER_LINE_COMPETENCE a, PER_LINE b, PER_COMPETENCE c 
							where 		a.PL_CODE=trim(b.PL_CODE) and a.CP_CODE=trim(c.CP_CODE)
								$search_condition 
							order by 	a.PL_CODE, a.CP_CODE	";
	} // end if

	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
//	echo $cmd;

	if($count_data){
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
		$data_count = $data_row = 0;
		while($data = $db_dpis->get_array()){
			$data_count++;
			$data_row++;			

			$PL_CODE = $data[PL_CODE];
			$CP_CODE = $data[CP_CODE];
			$LC_ACTIVE = ($data[LC_ACTIVE]==1)?"../images/checkbox_check.jpg":"../images/checkbox_blank.jpg";
			$PL_NAME = $data[PL_NAME];
			$CP_NAME = $data[CP_NAME];		

	//new format************************************************************			
			$arr_data = (array) null;
			$arr_data[] = $data_row;
			$arr_data[] ="$PL_NAME";
			$arr_data[] ="$CP_NAME";
			$data_align = array("C", "L", "L");
			 if ($FLAG_RTF)
			 $result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
		    else
			$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", "000000", "");		//TRHBL
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
		} // end while
	}else{
	      if ($FLAG_RTF){
		$result = $RTF->add_text_line("********** ����բ����� **********", 7, "", "C", "", "14", "b", 0, 0);
		 if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
		  }else{
		$pdf->SetFont($font,'b','',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(200,10,"********** ����բ����� **********",0,1,'C');
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