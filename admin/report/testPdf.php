<?php
    include("../../php_scripts/connect_database.php");
    include("../php_scripts/pdf_wordarray_thaicut.php");
    include ("../php_scripts/function_share.php");
    define('FPDF_FONTPATH','../../PDF/font/');
    include ("../../PDF/fpdf.php");
    include ("../../PDF/pdf_extends_DPIS.php");
    
    ini_set("max_execution_time", $max_execution_time);
    $report_title = trim($report_title);
    $report_code = "";
    
    session_cache_limiter("private");
    session_start();
    
    $unit="mm";
    $paper_size="A4";
    $lang_code="TH";
    $company_name = "";
    $orientation='P';
    $pdf = new PDF($orientation,$unit,$paper_size,$lang_code,$company_name,$report_title,$report_code,$heading,$heading_width,$heading_align);
    $pdf->Open();
    $pdf->SetFont($font,'',14);
    $pdf->SetMargins(5,5,5);
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->SetTextColor(0, 0, 0);
    
    $pdf->SetAutoPageBreak(true,10);
    
    $heading_width[0] = "20";
    $heading_width[1] = "40";
    $heading_width[2] = "50";
    $heading_width[3] = "50";
    $heading_width[4] = "35";
    
    $heading_text[0] = "$CODE_TITLE|";
    $heading_text[1] = "$SHORTNAME_TITLE|";
    $heading_text[2] = "ชื่อ (Th)|";
    $heading_text[3] = "ชื่อ (En)|";
    $heading_text[4] = "$ACTIVE_TITLE";

       
    $heading_align = array('C','C','C','C','C');
    //$table="PER_PRENAME";
$cmd = " select * from $table ";
     
	$db_dpis->send_cmd($cmd);
	$field_list = $db_dpis->list_fields($table);
	//echo "<pre>";		print_r($field_list);		echo "</pre>";
	
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
        
   
  	if(trim($search_code)) $arr_search_condition[] = "($arr_fields[0] like '$search_code%')";
  	if(trim($search_name)) $arr_search_condition[] = "(($arr_fields[1] like '%$search_name%') or ($arr_fields[2] like '%$search_name%') or ($arr_fields[3] like '%$search_name%'))";
	$search_condition = "";
	if(count($arr_search_condition)) $search_condition = " where " . implode(" and ", $arr_search_condition);

	if($DPISDB=="odbc"){
		$cmd = "	select		$arr_fields[0], $arr_fields[1], $arr_fields[2], $arr_fields[3], $arr_fields[4] 
							from		$table
							$search_condition
							order by IIF(ISNULL(PN_SEQ_NO), 9999, PN_SEQ_NO), $arr_fields[0] ";
	}elseif($DPISDB=="oci8"){
		$cmd = "	select		$arr_fields[0], $arr_fields[1], $arr_fields[2], $arr_fields[3], $arr_fields[4] 
							from		$table
							$search_condition
							order by PN_SEQ_NO, $arr_fields[0] ";
	} elseif($DPISDB=="mysql"){
		$cmd = "	select		$arr_fields[0], $arr_fields[1], $arr_fields[2], $arr_fields[3], $arr_fields[4] 
							from		$table
							$search_condition
							order by PN_SEQ_NO, $arr_fields[0] ";
	} // end if
       
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
			$arr_data[] = $data[$arr_fields[0]];
			$arr_data[] = $data[$arr_fields[1]];
			$arr_data[] = $data[$arr_fields[2]];
			$arr_data[] = $data[$arr_fields[3]];
			$arr_data[] = "<*img*".(($data[$arr_fields[4]]==1)?"../images/checkbox_check.jpg":"../images/checkbox_blank.jpg")."*img*>";

			$data_align = array("C", "C", "L", "L", "C");
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

		$data_align = array("C", "C", "C", "C", "C");
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
