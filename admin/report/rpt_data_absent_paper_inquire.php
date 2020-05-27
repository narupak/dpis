<?
	include("../../php_scripts/connect_database.php");
	include("../php_scripts/pdf_wordarray_thaicut.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	include("../php_scripts/load_per_control.php");

		if ($FLAG_RTF) {
		include ("rtf_setvar.php");	// กำหนดตัวแปรค่าสี set_of_colors
		require("../../RTF/rtf_class.php");
	} else	 {
		define('FPDF_FONTPATH','../../PDF/font/');
		include ("../../PDF/fpdf.php");
		include ("../../PDF/pdf_extends_DPIS.php");
	}

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if ($PER_TYPE==1) {
		$search_from = ", PER_POSITION c";
		$arr_search_condition[] = "b.POS_ID=c.POS_ID";
	} elseif ($PER_TYPE==2) { 
		$search_from = ", PER_POS_EMP c";
		$arr_search_condition[] = "b.POEM_ID=c.POEM_ID";
	} elseif ($PER_TYPE==3) { 
		$search_from = ", PER_POS_EMPSER c";
		$arr_search_condition[] = "b.POEMS_ID=c.POEMS_ID"; 
	} elseif ($PER_TYPE==4) {
		$search_from = ", PER_POS_TEMP c";
		$arr_search_condition[] = "b.POT_ID=c.POT_ID"; 
	}
	
	if ($ABS_STARTDATE) {
		$temp_start =  save_date($ABS_STARTDATE);
		$arr_search_condition[] = "(ABS_STARTDATE >= '$temp_start')";
	} // end if
	
	if ($ABS_ENDDATE) {
		$temp_end =  save_date($ABS_ENDDATE);
		$arr_search_condition[] = "(ABS_ENDDATE <= '$temp_end')";
	} // end if

	if ($ORG_ID){ 
		$arr_search_condition[] = "(c.ORG_ID = $ORG_ID)";	
	}elseif($DEPARTMENT_ID){
		$cmd = " select ORG_ID from PER_ORG where OL_CODE='03' and ORG_ID_REF=$DEPARTMENT_ID ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_org[] = $data[ORG_ID];
		$arr_search_condition[] = "(c.ORG_ID in (". implode(",", $arr_org) ."))";
	}elseif($MINISTRY_ID){
		$cmd = " select 	b.ORG_ID
						 from   	PER_ORG a, PER_ORG b
						 where  	a.OL_CODE='02' and b.OL_CODE='03' and a.ORG_ID_REF=$MINISTRY_ID and b.ORG_ID_REF=a.ORG_ID
						 order by a.ORG_ID, b.ORG_ID ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_org[] = $data[ORG_ID];
		$arr_search_condition[] = "(c.ORG_ID in (". implode(",", $arr_org) ."))";
	}elseif($PV_CODE){
		$cmd = " select 	ORG_ID
						 from   	PER_ORG
						 where  	OL_CODE='03' and PV_CODE='$PV_CODE'
						 order by ORG_ID ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_org[] = $data[ORG_ID];
		$arr_search_condition[] = "(c.ORG_ID in (". implode(",", $arr_org) ."))";
	}
		
	$search_condition = "";
	if ($arr_search_condition)		$search_condition = " and " . implode(" and ", $arr_search_condition);

	
	$report_title = "$DEPARTMENT_NAME||รายงานแสดงข้อมูล$PERSON_TYPE[$search_per_type]ที่ส่งใบลาไม่ถูกต้อง/ยังไม่ได้ส่งใบลา";
	$report_code = "P0602";
	

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();
	if ($FLAG_RTF) {
		$sum_w = array_sum($heading_width);
		for($h = 0; $h < count($heading_width); $h++) {
			$heading_width[$h] = $heading_width[$h] / $sum_w * 100;
		}
		$fname= "rpt_data_absent_paper_inquire.rtf";
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
	$heading_width[0] = "26";
	$heading_width[1] = "27";
	$heading_width[2] = "11";
	$heading_width[3] = "11";
	$heading_width[4] = "9";
	$heading_width[5] = "18";
	}else {
	$heading_width[0] = "75";
	$heading_width[1] = "77";
	$heading_width[2] = "25";
	$heading_width[3] = "25";
	$heading_width[4] = "25";
	$heading_width[5] = "60";
	}
		//new format*****************************************
	$heading_text[0] = "ชื่อ - สกุล";
	$heading_text[1] =  "ประเภท";
	$heading_text[2] = "ตั้งแต่วันที่";
	$heading_text[3] = "ถึงวันที่";
	$heading_text[4] = "จำนวนวัน";
	$heading_text[5] = "การส่งใบลา";
	$heading_align = array('C','C','C','C','C','C');

/*	
	function print_header(){
		global $pdf, $heading_width;
		
		$pdf->SetFont($font,'',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
		$pdf->Cell(100,2,"",0,1,'C');

		$pdf->Cell($heading_width[0] ,7,"ชื่อ - สกุล",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"ประเภท",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7, "ตั้งแต่วันที่",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[3] ,7, "ถึงวันที่",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[4] ,7, "จำนวนวัน",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7, "การส่งใบลา",'LTBR',1,'C',1);
	} // function		
*/
	if($DPISDB=="odbc"){
		$cmd = " select 		a.PER_ID, b.PN_CODE, PER_NAME, PER_SURNAME, AB_CODE, ABS_STARTDATE, ABS_ENDDATE, ABS_DAY, ABS_LETTER, 
						  					b.POS_ID, b.POEM_ID, b.POEMS_ID, c.ORG_ID 
						 from 			PER_ABSENT a, PER_PERSONAL b
						  					$search_from
						 where 		b.PER_TYPE=$PER_TYPE and PER_STATUS=1 and a.PER_ID=b.PER_ID and ABS_LETTER in (1, 3) 
						  					$search_condition 
						 order by 	PER_NAME, PER_SURNAME ";	
	}elseif($DPISDB=="oci8"){
		$cmd = " select 		a.PER_ID, b.PN_CODE, PER_NAME, PER_SURNAME, AB_CODE, ABS_STARTDATE, ABS_ENDDATE, ABS_DAY, ABS_LETTER, 
						  					b.POS_ID, b.POEM_ID, b.POEMS_ID, c.ORG_ID 
						 from 			PER_ABSENT a, PER_PERSONAL b  
						  					$search_from
						 where 		b.PER_TYPE=$PER_TYPE and PER_STATUS=1 and a.PER_ID=b.PER_ID and ABS_LETTER in (1, 3) 
						  					$search_condition 
						 order by 	PER_NAME, PER_SURNAME ";	
	}elseif($DPISDB=="mysql"){
		$cmd = " select 		a.PER_ID, b.PN_CODE, PER_NAME, PER_SURNAME, AB_CODE, ABS_STARTDATE, ABS_ENDDATE, ABS_DAY, ABS_LETTER, 
						  					b.POS_ID, b.POEM_ID, b.POEMS_ID, c.ORG_ID 
						 from 			PER_ABSENT a, PER_PERSONAL b
						  					$search_from
						 where 		b.PER_TYPE=$PER_TYPE and PER_STATUS=1 and a.PER_ID=b.PER_ID and ABS_LETTER in (1, 3) 
						  					$search_condition 
						 order by 	PER_NAME, PER_SURNAME ";
	} // end if
	if($SESS_ORG_STRUCTURE==1) { 
			$cmd = str_replace("c.ORG_ID", "b.ORG_ID", $cmd);
			//$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		}
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	//echo $cmd;
	$data_count = $data_row = 0;
	$total_data = 0;
	while($data = $db_dpis->get_array()){
		$data_row++;

		$TMP_PER_ID = $data[PER_ID];
		$TMP_PN_CODE = $data[PN_CODE];
		$TMP_PER_NAME = $data[PER_NAME];
		$TMP_PER_SURNAME = $data[PER_SURNAME];
		
		$TMP_ABS_STARTDATE = show_date_format($data[ABS_STARTDATE],$DATE_DISPLAY);
		$TMP_ABS_ENDDATE = show_date_format($data[ABS_ENDDATE],$DATE_DISPLAY);
		$TMP_ABS_DAY = trim($data[ABS_DAY]);
		$TMP_ABS_LETTER = trim($data[ABS_LETTER]);
		if ($TMP_ABS_LETTER == 1) 				$ABS_LETTER_STR = "ยังไม่ได้ส่ง";
		elseif ($TMP_ABS_LETTER == 3) 		$ABS_LETTER_STR = "ไม่ถูกต้อง";		

		$TMP_PN_CODE = trim($data[PN_CODE]);
		if($TMP_PN_CODE){
			$cmd = " select PN_NAME from PER_PRENAME where PN_CODE='$TMP_PN_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$TMP_PN_NAME = $data2[PN_NAME];
		} // end if
		
		$TMP_AB_CODE = trim($data[AB_CODE]);
		if($TMP_AB_CODE){
			$cmd = " select AB_NAME from PER_ABSENTTYPE where AB_CODE='$TMP_AB_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$TMP_AB_NAME = $data2[AB_NAME];
		} // end if
		
		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][per_name] = (($NUMBER_DISPLAY==2)?convert2thaidigit($data_row):$data_row).". $TMP_PN_NAME$TMP_PER_NAME $TMP_PER_SURNAME";
		$arr_content[$data_count][ab_name] = $TMP_AB_NAME;
		$arr_content[$data_count][abs_startdate] = $TMP_ABS_STARTDATE;
		$arr_content[$data_count][abs_enddate] = $TMP_ABS_ENDDATE;
		$arr_content[$data_count][abs_day] = $TMP_ABS_DAY;
		$arr_content[$data_count][abs_letter] = $ABS_LETTER_STR;
		
		$data_count++;
		$total_data++;
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";

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

		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$PER_NAME = $arr_content[$data_count][per_name];
			$AB_NAME = $arr_content[$data_count][ab_name];
			$ABS_STARTDATE = $arr_content[$data_count][abs_startdate];
			$ABS_ENDDATE = $arr_content[$data_count][abs_enddate];
			$ABS_DAY = number_format($arr_content[$data_count][abs_day]);
			$ABS_LETTER = $arr_content[$data_count][abs_letter];
		//new format************************************************************			
			$arr_data = (array) null;
			$arr_data[] ="$PER_NAME";
			$arr_data[] = "$AB_NAME";
			$arr_data[] = (($NUMBER_DISPLAY==2)?convert2thaidigit($ABS_STARTDATE):$ABS_STARTDATE);
			$arr_data[] = (($NUMBER_DISPLAY==2)?convert2thaidigit($ABS_ENDDATE):$ABS_ENDDATE);
			$arr_data[] = (($NUMBER_DISPLAY==2)?convert2thaidigit($ABS_DAY):$ABS_DAY);
			$arr_data[] = "$ABS_LETTER";
	
			$data_align = array("L", "L", "R", "R", "R", "L");
			if ($FLAG_RTF)
			 $result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
		     else
			$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "14", "", "000000", "");		//TRHBL
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
		} // end for
		if (!$FLAG_RTF) {
		$pdf->add_data_tab("", 0, "RHBL", $data_align, "", "14", "", "000000", "");		// เส้นปิดบรรทัด			
                                     }
		$arr_data = (array) null;
		$arr_data[] ="<**1**>จำนวนรายการทั้งหมด ". (($NUMBER_DISPLAY==2)?convert2thaidigit($total_data):$total_data)." รายการ";
		$arr_data[]  ="<**1**>จำนวนรายการทั้งหมด ". (($NUMBER_DISPLAY==2)?convert2thaidigit($total_data):$total_data)." รายการ";
		$arr_data[]  ="<**1**>จำนวนรายการทั้งหมด ". (($NUMBER_DISPLAY==2)?convert2thaidigit($total_data):$total_data)." รายการ";
		$arr_data[]  ="<**1**>จำนวนรายการทั้งหมด ". (($NUMBER_DISPLAY==2)?convert2thaidigit($total_data):$total_data)." รายการ";
		$arr_data[]  ="<**1**>จำนวนรายการทั้งหมด ". (($NUMBER_DISPLAY==2)?convert2thaidigit($total_data):$total_data)." รายการ";
		$arr_data[] ="<**1**>จำนวนรายการทั้งหมด ". (($NUMBER_DISPLAY==2)?convert2thaidigit($total_data):$total_data)." รายการ";
		
		$data_align = array("R", "R", "R", "R", "R", "R");
		if ($FLAG_RTF)
		$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
		else
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");		//TRHBL
		if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";

//		$pdf->add_data_tab("", 0, "RHBL", $data_align, "", "14", "", "000000", "");		// เส้นปิดบรรทัด				
		
	}else{
	    if ($FLAG_RTF){
		$result = $RTF->add_text_line("********** ไม่มีข้อมูล **********", 7, "", "C", "", "14", "b", 0, 0);
		if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
		  }else{
		$pdf->SetFont($font,'b','',16);
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