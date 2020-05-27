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
	$report_code = "K12";
	

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();
	if ($FLAG_RTF) {
		$sum_w = array_sum($heading_width);
		for($h = 0; $h < count($heading_width); $h++) {
			$heading_width[$h] = $heading_width[$h] / $sum_w * 100;
		}
		$fname= "rpt_kpi_standard_compentence.rtf";
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
	}
	if ($FLAG_RTF) {
	$heading_width[0] = "50";
	$heading_width[1] = "50";
	}else{
	$heading_width[0] = "145";
	$heading_width[1] = "145";
	}
	//new format**************************************************
    $heading_text[0] = "$DEPARTMENT_TITLE";
	$heading_text[1] = "$LEVEL_TITLE";
	
	$heading_align = array('C','C');

	if ($ISCS_FLAG==1) $arr_search_condition[] = "(a.LEVEL_NO in $LIST_LEVEL_NO)"; 
  	if($search_department_id){
		$arr_search_condition[] = "(a.DEPARTMENT_ID = $search_department_id)";
	}/*else if($search_ministry_id){
		$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$search_ministry_id ";
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_org[] = $data[ORG_ID];
		$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode(",", $arr_org) ."))";
	} // end if */
	
	if(!$sort_by) $sort_by=1;
	$sort_type = (isset($sort_type))?  $sort_type : "1:asc";
	$arrSort=explode(":",$sort_type);
	$SortType[$arrSort[0]]	=$arrSort[1];
	if(!$order_by) $order_by=1;
	if($order_by==1){	//(ค่าเริ่มต้น) 
		$order_str = "c.ORG_ID_REF, c.ORG_SEQ_NO, c.ORG_CODE, a.DEPARTMENT_ID $SortType[$order_by], b.LEVEL_SEQ_NO $SortType[$order_by], b.LEVEL_NAME $SortType[$order_by]";
	}else if($order_by==2){	
		$order_str = "b.LEVEL_SEQ_NO $SortType[$order_by], b.LEVEL_NAME $SortType[$order_by]";
	}
  	if(trim($search_level_no)) $arr_search_condition[] = "(a.LEVEL_NO = '$search_level_no')";
	$search_condition = "";
	if(count($arr_search_condition)) $search_condition = " where " . implode(" and ", $arr_search_condition);
	
	$cmd = " select		distinct LEVEL_NO, DEPARTMENT_ID
						 from		PER_STANDARD_COMPETENCE a
						 	$search_condition
											 ";
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
//	echo "$count_data<br>";

if(count($arr_search_condition)) $search_condition = " and " . implode(" and ", $arr_search_condition);
	
	if($DPISDB=="odbc"){
		$cmd = " select		distinct a.LEVEL_NO, b.LEVEL_NAME, b.LEVEL_SEQ_NO, c.ORG_ID_REF, c.ORG_SEQ_NO, c.ORG_CODE, a.DEPARTMENT_ID
								 from			PER_STANDARD_COMPETENCE a, PER_LEVEL b, PER_ORG c
						 where a.LEVEL_NO = b.LEVEL_NO and a.DEPARTMENT_ID = c.ORG_ID
									 	$search_condition
										order by $order_str ";
	}elseif($DPISDB=="oci8"){			   
		$rec_start = (($current_page-1) * $data_per_page) + 1;
		$rec_end = ($current_page > 1)? ($current_page * $data_per_page) : $data_per_page;

		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = "select * from (
						   select rownum rnum, q1.* from ( 
								  select		distinct a.LEVEL_NO, b.LEVEL_NAME, b.LEVEL_SEQ_NO, c.ORG_ID_REF, c.ORG_SEQ_NO, c.ORG_CODE, a.DEPARTMENT_ID
								 from			PER_STANDARD_COMPETENCE a, PER_LEVEL b, PER_ORG c
						 where a.LEVEL_NO = b.LEVEL_NO and a.DEPARTMENT_ID = c.ORG_ID
													$search_condition 
						 order by $order_str
						   )  q1
					) ";					   
	}elseif($DPISDB=="mysql"){
		$cmd = " select		distinct a.LEVEL_NO, b.LEVEL_NAME, b.LEVEL_SEQ_NO , c.ORG_ID_REF, c.ORG_SEQ_NO, c.ORG_CODE, a.DEPARTMENT_ID
								 from			PER_STANDARD_COMPETENCE a, PER_LEVEL b, PER_ORG c
						 where a.LEVEL_NO = b.LEVEL_NO and a.DEPARTMENT_ID = c.ORG_ID
									 	$search_condition
						  order by $order_str";
	} // end if
    $count_page_data = $db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();
	    if($count_page_data){
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
		$temp_LEVEL_NO = trim($data[LEVEL_NO]);
		$LEVEL_NAME = trim($data[LEVEL_NAME]);
		$temp_DEPARTMENT_ID = $data[DEPARTMENT_ID];
		// หา DEPARTMENT_NAME
		$cmd2 = " select ORG_NAME from PER_ORG where ORG_ID=$temp_DEPARTMENT_ID ";
		$db_dpis2->send_cmd($cmd2); 
		$data2 = $db_dpis2->get_array();
		$DEPARTMENT_NAME = $data2[ORG_NAME];


	//new format************************************************************			
            	$arr_data = (array) null;
				$arr_data[] ="$DEPARTMENT_NAME";
				$arr_data[] ="$LEVEL_NAME";
				$data_align = array("L", "L");
				  if ($FLAG_RTF)
		    	 $result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
		         else	
				$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", "000000", "");		//TRHBL
				if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
			} // end for
		}else{
		 if ($FLAG_RTF){
		$result = $RTF->add_text_line("********** ไม่มีข้อมูล **********", 7, "", "C", "", "14", "b", 0, 0);
		if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
		  }else{
		$pdf->SetFont($font,'b',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(287,10,"********** ไม่มีข้อมูล **********",0,1,'C');
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