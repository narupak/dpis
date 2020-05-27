<? 
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	include("../../php_scripts/connect_database.php");
	//if (!$FLAG_RTF) include("../php_scripts/pdf_wordarray_thaicut.php");
	//include("../../php_scripts/calendar_data.php");
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
	//$report_title = trim($report_title);
	
	//$db_dpis = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);	
	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);	
	
	if(!$year_10){
		if(date("Y-m-d") <= date("Y")."-10-01") 
		$year_10 = date("Y") + 543;
		else 
		$year_10 = (date("Y") + 543) + 1;
	} // end if
	
	
	$company_name = "";
	$report_title = "สรุปจำนวนครั้งการประชุมคณะกรรมการการกำหนดตำแหน่งระดับสูงของกระทรวง||ย้อนหลังนับจาก ปีงบประมาณ $year_10";
	$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
	$report_code = "  ";
	
	include ("rpt_data_jethro_summary_format.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน
//	session_cache_limiter("nocache");
	
	session_cache_limiter("private");
	session_start();
	if ($FLAG_RTF) {
		$sum_w = array_sum($heading_width);
		for($h = 0; $h < count($heading_width); $h++) {
			$heading_width[$h] = $heading_width[$h] / $sum_w * 100;
		}

		$fname= "rpt_data_jethro_summary.rtf";

	//	RTF $papersize = "a4", $margl = 720, $margr = 720, $margt = 720, $margb = 720, $orient = "p", $u_color_tab="";
		$paper_size="A4";
		$orientation='L';
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
		$orientation='L';

		$pdf = new PDF($orientation,$unit,$paper_size,$lang_code,$company_name,$report_title,$report_code,$heading,$heading_width,$heading_align);
		
		$pdf->Open();
		$pdf->SetMargins(5,5,5);
		$pdf->AliasNbPages();
		$pdf->AddPage();
		$pdf->SetTextColor(0, 0, 0);
		$pdf->SetFont($font,'',14);
		
		$page_start_x = $pdf->x;			$page_start_y = $pdf->y;
	}
	if($DPISDB=="odbc"){	
		$cmd = " select distinct(a.DEPARTMENT_ID),b.ORG_NAME as DEPARTMENT_NAME   
						 from PER_JETHRO  a, PER_ORG b 
						where a.DEPARTMENT_ID=b.ORG_ID(+)		
						";
	}elseif($DPISDB=="oci8"){
		$cmd = " select distinct(a.DEPARTMENT_ID),b.ORG_NAME as DEPARTMENT_NAME   
						 from PER_JETHRO  a, PER_ORG b 
						where a.DEPARTMENT_ID=b.ORG_ID(+)		
						";						 
	}elseif($DPISDB=="mysql"){
		$cmd = " select distinct(a.DEPARTMENT_ID),b.ORG_NAME as DEPARTMENT_NAME   
						 from PER_JETHRO  a, PER_ORG b 
						where a.DEPARTMENT_ID=b.ORG_ID(+)		
						";
	} // end if

	$count_page_data = $db_dpis->send_cmd($cmd);
//echo "cmd=$cmd ($count_page_data)<br>";
//$db_dpis->show_error();
	$head_text1 = implode(",", $heading_text);
	$head_width1 = implode(",", $heading_width);
	$head_align1 = implode(",", $heading_align);
	$col_function = implode(",", $column_function);
	if ($FLAG_RTF) {
		$RTF->add_header("", 0, false);	// header default
		$RTF->add_footer("", 0, false);		// footer default
			
	//	echo "$head_text1<br>";
		$tab_align = "center";
		$result = $RTF->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", $fmtTableHeader_col_idx, $fmtTableHeader_bgcol_idx, $COLUMN_FORMAT, $col_function, false, $tab_align);
	} else {
		$pdf->AutoPageBreak = false; 
	//	echo "$head_text1<br>";
		$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", "0066CC", "EEEEFF", $COLUMN_FORMAT, $col_function);
	}
	if (!$result) echo "****** error ****** on open table for $table<br>";
	
	if($count_page_data){
		$data_count = 0;
        while ($data = $db_dpis->get_array()) {
				$data_count++;
				// $temp_JETHRO_ID = $data[JETHRO_ID];
				//$current_list .= ((trim($current_list))?", ":"") . "'" . $temp_JETHRO_ID ."'";
    
				$DEPARTMENT_ID = $data[DEPARTMENT_ID];
				$TMP_DEPARTMENT_NAME = $data[DEPARTMENT_NAME];
         
				$arr_data = (array) null;
				$year_10 = $year_10-10;
				$total = 0;
				$arr_data[] = $data_count."  ".$TMP_DEPARTMENT_NAME;
				for ($i = 0; $i < 10; $i++) {
				$year_10++;
				$cmd = "select count(MEETING_YEAR) as MEETING_YEAR from PER_JETHRO where MEETING_YEAR='$year_10' and DEPARTMENT_ID=$DEPARTMENT_ID";
				$db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$DIS_DEPARTMENT_YEAR =  $data1[MEETING_YEAR];
				$arr_data[] = $DIS_DEPARTMENT_YEAR;
				$total += $DIS_DEPARTMENT_YEAR;
				}
				$arr_data[] = $total;
				$arr_data[] = "";
              if ($FLAG_RTF)
				$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
			  else 
				$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "14", "", "000000", "");		//TRHBL
//				echo "out...pdf-x::".$pdf->x." , pdf-y::".$pdf->y."<br>";
			  
		    if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
        } // while
			if (!$FLAG_RTF)
			$pdf->add_data_tab("", 0, "RHBL", $data_align, "", "14", "", "000000", "");		// เส้นปิดบรรทัด			
	}else{
		if ($FLAG_RTF)
			$result = $RTF->add_text_line("********** ไม่มีข้อมูล **********", 7, "", "C", "", "14", "b", 0, 0);
		else
			$result = $pdf->add_text_line("********** ไม่มีข้อมูล **********", 7, "", "C", "", "14", "b", 0, 0);
			
		if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";
	} // end if
	if ($FLAG_RTF) {
		$RTF->close_tab(); 
//		$RTF->close_section(); 
		$RTF->display($fname);
	} else {
		$pdf->close_tab(""); 
		$pdf->close();
		$pdf->Output();
	}
?>	