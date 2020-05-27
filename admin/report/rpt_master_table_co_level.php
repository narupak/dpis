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
		$fname= "rpt_master_table_co_level.rtf";
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
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	if ($FLAG_RTF) {
	$heading_width[0] = "30";
	$heading_width[1] = "30";
	$heading_width[2] = "30";
	$heading_width[3] = "10";
	}else {
	$heading_width[0] = "60";
	$heading_width[1] = "60";
	$heading_width[2] = "60";
	$heading_width[3] = "20";
	}	
	$heading_text[0] = "��ǧ�дѺ���˹�";
	$heading_text[1] = "�дѺ�������";
	$heading_text[2] = "�дѺ�٧�ش";
	$heading_text[3] = "$ACTIVE_TITLE";
		
	$heading_align = array('C','C','C','C');
		
	$cmd = " select * from $table ";
	$db_dpis->send_cmd($cmd);
	$field_list = $db_dpis->list_fields($table);
//	echo "<pre>";		print_r($field_list);		echo "</pre>";
	unset($arr_fields);
	if($DPISDB=="odbc" || $DPISDB=="oci8"){
		for($i=1; $i<=count($field_list); $i++) :
			$arr_fields[] = $field_list[$i]["name"];
		endfor;
	}elseif($DPISDB=="mysql"){
		for($i=0; $i<count($field_list); $i++) :
			$arr_fields[] = $field_list[$i]["name"];
		endfor;
	} // end if  

//  	if(!$search_level_no_min) $search_level_no_min = 1;
 // 	if(!$search_level_no_max) $search_level_no_max = 11;

 	if(trim($search_name)) $arr_search_condition[] = "($arr_fields[0] like '%$search_name%')";
  	if(trim($search_level_no_min)){ 
		if($DPISDB=="oci8") $arr_search_condition[] = "($arr_fields[1] >= '$search_level_no_min')";
		elseif($DPISDB=="odbc") $arr_search_condition[] = "($arr_fields[1] >= '$search_level_no_min')";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "($arr_fields[1] >= '$search_level_no_min')";
	}
  	if(trim($search_level_no_max)){ 
		if($DPISDB=="oci8") $arr_search_condition[] = "($arr_fields[2] <= '$search_level_no_max')";
		elseif($DPISDB=="odbc") $arr_search_condition[] = "($arr_fields[2] <= '$search_level_no_max')";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "($arr_fields[2] <= '$search_level_no_max')";
	}
	$search_condition = "";
	if(count($arr_search_condition)) $search_condition = " where " . implode(" and ", $arr_search_condition);

	if($DPISDB=="odbc"){
		$cmd = "	select	$arr_fields[0], $arr_fields[1], $arr_fields[2], $arr_fields[3]
							from		$table
											$search_condition
							order by IIF(ISNULL(CL_SEQ_NO), 9999, CL_SEQ_NO), $arr_fields[1], $arr_fields[2], $arr_fields[0] ";
	}elseif($DPISDB=="oci8"){
		$cmd = "	select	$arr_fields[0], $arr_fields[1], $arr_fields[2], $arr_fields[3] 
							from		$table
											$search_condition
							order by  CL_SEQ_NO, $arr_fields[1], $arr_fields[2], $arr_fields[0] ";
	}elseif($DPISDB=="mysql"){
		$cmd = "	select	$arr_fields[0], $arr_fields[1], $arr_fields[2], $arr_fields[3]
							from		$table
											$search_condition
							order by CL_SEQ_NO, $arr_fields[1], $arr_fields[2], $arr_fields[0] ";
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
			$arr_data[] = $data[$arr_fields[0]];

			$LEVEL_NO_MIN = $data[$arr_fields[1]];
			$cmd = "select LEVEL_NAME from PER_LEVEL where LEVEL_NO='$LEVEL_NO_MIN' ";
			$db_dpis2->send_cmd($cmd);
		// $db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$arr_data[] = $data2[LEVEL_NAME];

			$LEVEL_NO_MAX = $data[$arr_fields[2]];
			$cmd = "select LEVEL_NAME from PER_LEVEL where LEVEL_NO='$LEVEL_NO_MAX' ";
			$db_dpis2->send_cmd($cmd);
		// $db_dpis2->show_error();
			$data3 = $db_dpis2->get_array();
			$arr_data[] = $data3[LEVEL_NAME];
			$arr_data[] = "<*img*".(($data[$arr_fields[3]]==1)?"../images/checkbox_check.jpg":"../images/checkbox_blank.jpg")."*img*>";

			$data_align = array("C", "C", "C", "C");
			if ($FLAG_RTF)
			 $result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
		     else
			$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
		} // end while
	}else{
	     if ($FLAG_RTF){
		$result = $RTF->add_text_line("********** ����բ����� **********", 7, "", "C", "", "14", "b", 0, 0);
		if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
		 }else{
		$arr_data = (array) null;
		$arr_data[] = "<**1**>********** ����բ����� **********";
		$arr_data[] = "<**1**>********** ����բ����� **********";
		$arr_data[] = "<**1**>********** ����բ����� **********";
		$arr_data[] = "<**1**>********** ����բ����� **********";
         }
		$data_align = array("C", "C", "C", "C");
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