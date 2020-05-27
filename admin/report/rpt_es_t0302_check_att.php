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
			$fname= "T0302.pdf";
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
		$heading_width[0] = "24";
		$heading_width[1] = "32";
		$heading_width[2] = "39";
		$heading_width[3] = "30";
		$heading_width[4] = "18";
		$heading_width[5] = "23";
		$heading_width[6] = "34";
	}else{
		$heading_width[0] = "24";
		$heading_width[1] = "32";
		$heading_width[2] = "39";
		$heading_width[3] = "30";
		$heading_width[4] = "18";
		$heading_width[5] = "23";
		$heading_width[6] = "34";
	}	
	$heading_text[0] = "หมายเลขเครื่อง";
	$heading_text[1] = "ชื่อเครื่องบันทึกเวลา";
	$heading_text[2] = "สถานที่ปฏิบัติราชการ";
	$heading_text[3] = "IP Address";
	$heading_text[4] = "สถานะ";
	$heading_text[5] = "Lock/Unlock";
	$heading_text[6] = "วัน-เวลาของสถานะล่าสุด";
		
	$heading_align = array('C','C','C','C','C','C','C');
		

  	if(trim($search_code)) $arr_search_condition[] = "(a.TA_CODE like '".trim($search_code)."%')";
  	if(trim($search_name)) $arr_search_condition[] = "(a.TA_NAME like '%".trim($search_name)."%')";
  	if(trim($search_wl_code)) $arr_search_condition[] = "(a.WL_CODE = '".trim($search_wl_code)."')";
	$search_condition = "";
	if(count($arr_search_condition)) $search_condition = " and " . implode(" and ", $arr_search_condition);



		$cmd = " select		a.TA_CODE, a.TA_NAME, a.WL_CODE, a.TA_ACTIVE, b.WL_NAME,
                                 			a.ALIVE_STATUS,a.ALIVE_IP_ADDR,a.LOCK_STATUS,
                                            TO_CHAR(a.SYNC_DATE,'yyyy-mm-dd') AS SYNC_DATE,
                                            TO_CHAR(a.SYNC_DATE,'HH24:MI:SS') AS SYNC_TIME
								from  		PER_TIME_ATT a, PER_WORK_LOCATION b
								where		a.WL_CODE = b.WL_CODE AND a.TA_ACTIVE=1
												$search_condition order by a.TA_CODE ";
///echo  "<pre>".$cmd; die();

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
			
			$DATA_TA_CODE = $data[TA_CODE];
			$arr_data[] = $DATA_TA_CODE; 
			
			$DATA_TA_NAME = $data[TA_NAME]; 
			$arr_data[] = $DATA_TA_NAME; 
			
			$DATA_WL_NAME = $data[WL_NAME];
			$arr_data[] = $DATA_WL_NAME; 
			
			$ALIVE_IP_ADDR = $data[ALIVE_IP_ADDR];
			$arr_data[] = $ALIVE_IP_ADDR; 
			
			if($data[ALIVE_STATUS]==1){ 
      				$ALIVE_STATUS = "ON";
           }else if($data[ALIVE_STATUS]==0){ 
           			$ALIVE_STATUS = "OFF";
           			
      	   }else{ 
           			$ALIVE_STATUS = "";
           }
		   
		   $arr_data[] = $ALIVE_STATUS; 
		   
		   
			
			if($data[LOCK_STATUS]=="N"){  
      				$LOCK_STATUS="Unlock";
			}else if($data[LOCK_STATUS]=="Y"){ 
					$LOCK_STATUS="Lock";	
			}
			$arr_data[] = $LOCK_STATUS; 	
			
			$DATA_SYNC_DATE="";
			if($data[SYNC_DATE]){
				$DATA_SYNC_DATE = show_date_format($data[SYNC_DATE], $DATE_DISPLAY)." ".$data[SYNC_TIME];
			}
			$arr_data[] = $DATA_SYNC_DATE; 

			$data_align = array("C", "L", "L", "C", "C", "C", "C");
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
                  }
		$data_align = array("C", "C", "C","C", "C","C","C");
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