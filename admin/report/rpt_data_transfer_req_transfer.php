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
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	$search_per_type = (trim($search_per_type))? $search_per_type : 1;
	if($DEPARTMENT_ID) $arr_search_condition[] = "(DEPARTMENT_ID = $DEPARTMENT_ID)";	
	if($ORG_ID) $arr_search_condition[] = "(ORG_ID_1=$ORG_ID or ORG_ID_2=$ORG_ID or ORG_ID_3=$ORG_ID)";
  	
	if(trim($search_name)) 		$arr_search_condition[] = "(TR_NAME like '%$search_name%')";
  	if(trim($search_cardno)) 	$arr_search_condition[] = "(TR_CARDNO like '$search_cardno%')";
	if(trim($PER_TYPE_SEARCH)) 	$arr_search_condition[] = "(TR_PER_TYPE = $PER_TYPE_SEARCH)";
  	if(trim($EN_CODE)) 			$arr_search_condition[] = "(EN_CODE='$EN_CODE')";
  	if(trim($EM_CODE)) 			$arr_search_condition[] = "(EM_CODE='$EM_CODE')";	
  	if(trim($INS_CODE)) 			$arr_search_condition[] = "(INS_CODE='$INS_CODE')";		
  	if(trim($TR_POSITION))		$arr_search_condition[] = "(TR_POSITION like '%$TR_POSITION%')";
  	if(trim($LEVEL_START_N) || trim($LEVEL_END_N)) 			
		$arr_search_condition[] = "(TR_LEVEL >= '$LEVEL_START_N' and TR_LEVEL <= '$LEVEL_END_N')";	
	if(trim($PL_PN_CODE) && trim($search_per_type) == 1)
		$arr_search_condition[] = "(PL_CODE_1='$PL_PN_CODE' or PL_CODE_2='$PL_PN_CODE' or PL_CODE_3='$PL_PN_CODE')";
	elseif(trim($PL_PN_CODE) && trim($search_per_type) == 2)
		$arr_search_condition[] = "(PN_CODE_1='$PL_PN_CODE' or PN_CODE_2='$PL_PN_CODE' or PN_CODE_3='$PL_PN_CODE')";	
  	if(trim($LEVEL_START_F) || trim($LEVEL_END_F)) 
		$arr_search_condition[] = "((LEVEL_NO_1 >= '$LEVEL_START_F' and LEVEL_NO_1 <= '$LEVEL_END_F') or (LEVEL_NO_2 >= '$LEVEL_START_F' and LEVEL_NO_2 <= '$LEVEL_END_F') or (LEVEL_NO_3 >= '$LEVEL_START_F' and LEVEL_NO_3 <= '$LEVEL_END_F'))";
	if(trim($TR_DATE_START) || trim($TR_DATE_END)) {
		$temp_start =  save_date($TR_DATE_START);
		$temp_end =  save_date($TR_DATE_END);
		$arr_search_condition[] = "(TR_DATE >= '$temp_start' or TR_DATE <= '$temp_end')";
	}
	$search_condition = "";
	if(count($arr_search_condition)) $search_condition = " and " . implode(" and ", $arr_search_condition);

	$report_code = "P0201";


//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();
        
	   if ($FLAG_RTF) {
		$sum_w = array_sum($heading_width);
		for($h = 0; $h < count($heading_width); $h++) {
			$heading_width[$h] = $heading_width[$h] / $sum_w * 100;
		}

		$fname= "rpt_data_tranfer_req_tranfer.rtf";

	//	RTF $papersize = "a4", $margl = 720, $margr = 720, $margt = 720, $margb = 720, $orient = "p", $u_color_tab="";
		$paper_size="A4";
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
        $report_title = trim(iconv("utf-8","tis620",urldecode($report_title)));
	$pdf = new PDF($orientation,$unit,$paper_size,$lang_code,$company_name,$report_title,$report_code,$heading,$heading_width,$heading_align);
 	$pdf->Open();
	$pdf->SetMargins(5,5,5);
	$pdf->AliasNbPages();
	$pdf->AddPage();
	$pdf->SetTextColor(0, 0, 0);
	$pdf->SetFont($font,'',14);
	$pdf->SetAutoPageBreak(true,10);
	}
	if ($FLAG_RTF){
	$heading_width[0] = "5";
	$heading_width[1] = "26";
	$heading_width[2] = "17";
	$heading_width[3] = "17";
	$heading_width[4] = "25";
	$heading_width[5] = "10";
	}else {
	$heading_width[0] = "15";
	$heading_width[1] = "75";
	$heading_width[2] = "50";
	$heading_width[3] = "50";
	$heading_width[4] = "70";
	$heading_width[5] = "27";
	}
//new format*******************************************************
   $heading_text[0] = "ลำดับที่";
	$heading_text[1] = "ชื่อ-สกุล";
	$heading_text[2] = "ตำแหน่ง";
	$heading_text[3] = "ระดับ";
	$heading_text[4] = "สำนัก/กอง";
	$heading_text[5] = "อัตราเงินเดือน";
		
	$heading_align = array('C','C','C','C','C','C');
	
		
	$cmd = " select 		TR_ID, TR_NAME, TR_POSITION, TR_LEVEL, TR_ORG3, TR_ORG2, TR_SALARY
					 from 			PER_TRANSFER_REQ
					 where 		TR_TYPE = 1
										$search_condition	
					 order by 	TR_DATE ";
	$count_data = $db_dpis->send_cmd($cmd);
	//echo "$cmd<br>";
	//$db_dpis->show_error();
	$data_count = $data_row = 0;
	while($data = $db_dpis->get_array()){
		$data_row++;

		$temp_TR_ID = trim($data[TR_ID]);
		$TR_NAME = $data[TR_NAME];
		$TR_POSITION = $data[TR_POSITION];
		$LEVEL_NO = $data[TR_LEVEL];
		
		$cmd = "select LEVEL_NAME from PER_LEVEL where LEVEL_NO='$LEVEL_NO' ";
		$db_dpis2->send_cmd($cmd);
		
		$data2 = $db_dpis2->get_array();
		$TR_LEVEL = $data2[LEVEL_NAME];
		
		
		$TR_ORG3 = $data[TR_ORG3];
		$TR_SALARY = number_format($data[TR_SALARY], 2, '.', ',');
		
		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][order] = $data_row;
		$arr_content[$data_count][tr_name] = $TR_NAME;
		$arr_content[$data_count][tr_position] = $TR_POSITION;
		$arr_content[$data_count][tr_level] = level_no_format($TR_LEVEL);
		$arr_content[$data_count][tr_org3] = $TR_ORG3;
		$arr_content[$data_count][tr_salary] = $TR_SALARY;
				
		$data_count++;
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
//new format************************************************************	
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
			$ORDER = $arr_content[$data_count][order];
			$TR_NAME = $arr_content[$data_count][tr_name];
			$TR_POSITION = $arr_content[$data_count][tr_position];
			$TR_LEVEL = $arr_content[$data_count][tr_level];
			$TR_ORG3 = $arr_content[$data_count][tr_org3];
			$TR_SALARY = $arr_content[$data_count][tr_salary];
		//new format************************************************************			
			$arr_data = (array) null;
			$arr_data[] =(($NUMBER_DISPLAY==2)?convert2thaidigit($ORDER):$ORDER);
			$arr_data[] =$TR_NAME;
			$arr_data[] =$TR_POSITION;
			$arr_data[] =$TR_LEVEL;
			$arr_data[] =$TR_ORG3;         //*************ปัจจุบัน 
			$arr_data[] =(($NUMBER_DISPLAY==2)?convert2thaidigit($TR_SALARY):$TR_SALARY);
		
			$data_align = array("C", "L", "L", "C", "L", "R");
			if ($FLAG_RTF)
			$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
			else
			$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "14", "", "000000", "");		//TRHBL
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
		} // end for
		if (!$FLAG_RTF){
		$pdf->add_data_tab("", 7, "RHBL", $data_align, "", "12", "", "000000", "");		// เส้นปิดบรรทัด
		}
			
	}else{
	     if ($FLAG_RTF) {
		$result = $RTF->add_text_line("********** ไม่มีข้อมูล **********", 7, "", "C", "", "14", "b", 0, 0);
		      }else {
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