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
		$fname= "rpt_master_table_layer.rtf";
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
	$heading_width[0] = "14";
	$heading_width[1] = "6";
	$heading_width[2] = "14";
	$heading_width[3] = "14";
	$heading_width[4] = "11";
	$heading_width[5] = "11";
	$heading_width[6] = "11";
	$heading_width[7] = "11";
	$heading_width[8] = "8";
	}else {
	$heading_width[0] = "50";
	$heading_width[1] = "20";
	$heading_width[2] = "40";
	$heading_width[3] = "40";
	$heading_width[4] = "30";
	$heading_width[5] = "30";
	$heading_width[6] = "30";
	$heading_width[7] = "30";
	$heading_width[8] = "20";
	}	
	$heading_text[0] = "$LEVEL_TITLE";
	$heading_text[1] = "ขั้น";
	$heading_text[2] = "เงินเดือน";
	$heading_text[3] = "ประเภทข้าราชการ";
	$heading_text[4] = "ค่ากลาง/0.5 ขั้น";
	$heading_text[5] = "ค่ากลาง/1 ขั้น";
	$heading_text[6] = "ค่ากลาง/1.5 ขั้น";
	$heading_text[7] = "เงินเดือนพิเศษ/เต็มขั้น";
	$heading_text[8] = "$ACTIVE_TITLE";
		
	$heading_align = array('C','C','C','C','C','C','C','C','C');
		
//  if(!$search_level_no_min) $search_level_no_min = 1;
// 	if(!$search_level_no_max) $search_level_no_max = 11;

	if ($ISCS_FLAG==1) $arr_search_condition[] = "(a.LEVEL_NO in $LIST_LEVEL_NO)"; 
	if(trim($search_type)) $arr_search_condition[] = "(LAYER_TYPE = $search_type)";
	if(trim($search_per_type)) $arr_search_condition[] = "(PER_TYPE = $search_per_type)";
  	if(trim($search_level_no_min)){ 
		if($DPISDB=="oci8") $arr_search_condition[] = "(a.LEVEL_NO >= '$search_level_no_min')";
		elseif($DPISDB=="odbc") $arr_search_condition[] = "(a.LEVEL_NO >= '$search_level_no_min')";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(a.LEVEL_NO >= '$search_level_no_min')";
	}
  	if(trim($search_level_no_max)){ 
		if($DPISDB=="oci8") $arr_search_condition[] = "(a.LEVEL_NO <= '$search_level_no_max')";
		elseif($DPISDB=="odbc") $arr_search_condition[] = "(a.LEVEL_NO <= '$search_level_no_max')";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(a.LEVEL_NO <= '$search_level_no_max')";
	}
  	if(trim($search_layer_no_min)) $arr_search_condition[] = "(LAYER_NO >= $search_layer_no_min)";
  	if(trim($search_layer_no_max)) $arr_search_condition[] = "(LAYER_NO <= $search_layer_no_max)";
  	if(trim($search_salary_min)) $arr_search_condition[] = "(LAYER_SALARY >= $search_salary_min)";
  	if(trim($search_salary_max)) $arr_search_condition[] = "(LAYER_SALARY <= $search_salary_max)";
	$search_condition = "";
	if(count($arr_search_condition)) $search_condition = " and " . implode(" and ", $arr_search_condition);

	if($DPISDB=="odbc"){
		$cmd = "	select		LAYER_TYPE, a.LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, LAYER_SALARY_MIN, 
							LAYER_SALARY_MAX, LAYER_SALARY_MIDPOINT,	LAYER_SALARY_MIDPOINT1, LAYER_SALARY_MIDPOINT2, 
							LAYER_SALARY_FULL, b.LEVEL_NAME, b.PER_TYPE 
							from		$table a, PER_LEVEL b
							where		a.LEVEL_NO=b.LEVEL_NO
											$search_condition
							order by a.LEVEL_NO, a.LAYER_TYPE, a.LAYER_NO ";
	}elseif($DPISDB=="oci8"){
		$cmd = "	select		LAYER_TYPE, a.LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, LAYER_SALARY_MIN, 
							LAYER_SALARY_MAX, LAYER_SALARY_MIDPOINT,	LAYER_SALARY_MIDPOINT1, LAYER_SALARY_MIDPOINT2, 
							LAYER_SALARY_FULL, b.LEVEL_NAME, b.PER_TYPE
							from		$table a, PER_LEVEL b
							where		a.LEVEL_NO=b.LEVEL_NO
											$search_condition
							order by 	a.LEVEL_NO, a.LAYER_TYPE, a.LAYER_NO ";
	}elseif($DPISDB=="mysql"){
		$cmd = "	select		LAYER_TYPE, a.LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, LAYER_SALARY_MIN, 
							LAYER_SALARY_MAX, LAYER_SALARY_MIDPOINT,	LAYER_SALARY_MIDPOINT1, LAYER_SALARY_MIDPOINT2, 
							LAYER_SALARY_FULL, b.LEVEL_NAME, b.PER_TYPE 
							from		$table a, PER_LEVEL b
							where		a.LEVEL_NO=b.LEVEL_NO
											$search_condition
							order by a.LEVEL_NO, a.LAYER_TYPE, a.LAYER_NO ";
	} // end if

	$count_data = $db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();

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
			$arr_data[] = $data[LEVEL_NAME];
			$LAYER_NO = $data[LAYER_NO];
			$LAYER_SALARY = number_format($data[LAYER_SALARY], 2, ".", ",");
			$LAYER_SALARY_MIN = number_format($data[LAYER_SALARY_MIN], 2, ".", ",");
			$LAYER_SALARY_MAX = number_format($data[LAYER_SALARY_MAX], 2, ".", ",");
			if($LAYER_NO==0) $LAYER_SALARY = $LAYER_SALARY_MIN ." - ". $LAYER_SALARY_MAX;
			$arr_data[] = $LAYER_NO;
			$arr_data[] = $LAYER_SALARY;
			if($data[LAYER_TYPE] == 1) $LAYER_TYPE = "ทั่วไป (ท)";
			elseif($data[LAYER_TYPE] == 2) $LAYER_TYPE = "ผู้บริหารระดับสูง (บ)";
			elseif ($data[LAYER_TYPE] == 0) $LAYER_TYPE = $LAYER_TYPE_TITLE; 
			$arr_data[] = $LAYER_TYPE;
			$arr_data[] = number_format($data[LAYER_SALARY_MIDPOINT], 2, ".", ",");
			$arr_data[] = number_format($data[LAYER_SALARY_MIDPOINT1], 2, ".", ",");
			$arr_data[] = number_format($data[LAYER_SALARY_MIDPOINT2], 2, ".", ",");
			$arr_data[] = number_format($data[LAYER_SALARY_FULL], 2, ".", ",");
			$arr_data[] = "<*img*".(($data[LAYER_ACTIVE]==1)?"../images/checkbox_check.jpg":"../images/checkbox_blank.jpg")."*img*>";

			$data_align = array("C", "C", "R", "C", "R", "R", "R", "R", "C");
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