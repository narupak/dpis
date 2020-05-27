<?
	include("../../php_scripts/connect_database.php");
	include("../php_scripts/pdf_wordarray_thaicut.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

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
			$fname= "T0102.pdf";
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
		$heading_width[0] = "30";
		$heading_width[1] = "43";
		$heading_width[2] = "22";
		$heading_width[3] = "22";
		$heading_width[4] = "42";
		$heading_width[5] = "41";

	}else {
		$heading_width[0] = "30";
		$heading_width[1] = "43";
		$heading_width[2] = "22";
		$heading_width[3] = "22";
		$heading_width[4] = "42";
		$heading_width[5] = "41";

	}	
	$heading_text[0] = "วันที่ปฏิบัติราชการ";
	$heading_text[1] = "รอบการมาปฏิบัติราชการ";
	$heading_text[2] = "เวลาที่ไม่สาย";
	$heading_text[3] = "เวลาเพิ่มพิเศษ";
	$heading_text[4] = "สถานที่ปฏิบัติราชการ";
	$heading_text[5] = "หมายเหตุ";
		
	$heading_align = array('C','C','C','C','C','C');
		
	if(trim($search_wl_code)) $arr_search_condition[] = "(wla.WL_CODE = '$search_wl_code')";
  	if(trim($search_wc_code)) $arr_search_condition[] = "(wla.WC_CODE = '$search_wc_code')";
    
    if($search_date_min && $search_date_max){ 
		 $tmpsearch_date_min =  save_date($search_date_min);
         $tmpsearch_date_max =  save_date($search_date_max);
         $arr_search_condition[] = "  wla.WORK_DATE BETWEEN '$tmpsearch_date_min' and '$tmpsearch_date_max' ";
	}else if($search_date_min && empty($search_date_max)){ 
		 $tmpsearch_date_min =  save_date($search_date_min);
         $arr_search_condition[] = " wla.WORK_DATE = '$tmpsearch_date_min'  ";
    }else if(empty($search_date_min) && $search_date_max){ 
		 $tmpsearch_date_max =  save_date($search_date_max);
         $arr_search_condition[] = " wla.WORK_DATE = '$tmpsearch_date_max' ";
    }else{
      	$arr_search_condition[] = " wla.WORK_DATE = (select max(WORK_DATE) from PER_WORK_LATE) ";
    }

	$search_condition = "";
	if(count($arr_search_condition)) $search_condition = " where " . implode(" and ", $arr_search_condition);
  	if($order_by==1){	//สถานที่
    	$order_str = "wla.WORK_DATE ".$SortType[$order_by].",wlo.WL_NAME ".$SortType[$order_by].",wcy.WC_SEQ_NO ".$SortType[$order_by];
  	}elseif($order_by==2) {	//รอบ
		$order_str = "wcy.WC_SEQ_NO   ".$SortType[$order_by];
  	} elseif($order_by==3) {	//สถานที่
		$order_str = "wlo.WL_NAME ".$SortType[$order_by];
  	}

	$search_condition = "";
	if(count($arr_search_condition)) $search_condition = " where " . implode(" and ", $arr_search_condition);

	if($DPISDB=="oci8"){
		$cmd = "	select 		wla.WL_CODE, wla.WC_CODE, wla.WORK_DATE, wla.LATE_TIME, wla.LATE_REMARK,
                                  				wlo.WL_NAME,wcy.WC_NAME  ,wcy.WC_START
								  from 		PER_WORK_LATE  wla
                                  left join PER_WORK_LOCATION  wlo on(wlo.WL_CODE=wla.WL_CODE)
                                  left join PER_WORK_CYCLE wcy on(wcy.WC_CODE=wla.WC_CODE)
								  $search_condition
									order by  WL_CODE asc, WC_CODE asc, WORK_DATE asc ";
	}

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
		
			$arr_data[] = show_date_format($data[WORK_DATE], $DATE_DISPLAY);
			$arr_data[] = $data[WC_NAME];
			
			$newtimestampBgn = strtotime(date('Y-m-d').' '.substr($data[WC_START],0,2).':'.substr($data[WC_START],2,2).' + '.$TMP_P_EXTRATIME.' minute');
        	$DATA_P_EXTRATIME_SHOW =  date('H:i', $newtimestampBgn) ." น.";
			$arr_data[] = $DATA_P_EXTRATIME_SHOW;
			
			$arr_data[] =substr($data[LATE_TIME],0,2).":".substr($data[LATE_TIME],2,2). " น.";
			$arr_data[] = $data[WL_NAME];
			$arr_data[] = $data[LATE_REMARK];

			$data_align = array("C", "L", "C", "C", "L", "L");
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
		$data_align = array("C", "L", "C", "C", "L", "L");
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
			$pdf->Output($fname,'D');
		}
	ini_set("max_execution_time", 30);
?>