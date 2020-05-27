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
	
	
	$report_title = "สอบถามข้อมูลวิทยากร";
	$report_code = "P0806";
	

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();
	if ($FLAG_RTF) {
		$sum_w = array_sum($heading_width);
		for($h = 0; $h < count($heading_width); $h++) {
			$heading_width[$h] = $heading_width[$h] / $sum_w * 100;
		}
		$fname= "rpt_data_trainer.rtf";
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
	$heading_width[0] = "7";
	$heading_width[1] = "25";
	$heading_width[2] = "25";
	$heading_width[3] = "25";
	$heading_width[4] = "18";
	} else {
	$heading_width[0] = "20";
	$heading_width[1] = "70";
	$heading_width[2] = "70";
	$heading_width[3] = "70";
	$heading_width[4] = "50";
	}
//new format*******************************************************
   $heading_text[0] = "ลำดับที่";
	$heading_text[1] = "$FULLNAME_TITLE";
	$heading_text[2] = "ตำแหน่งปัจจุบัน";
	$heading_text[3] = "สถานที่ทำงาน";
	$heading_text[4] = "เบอร์โทรศัพท์ที่ทำงาน";
		
	$heading_align = array('C','C','C','C','C');
	
if(trim($search_name)) $arr_search_condition[] = "(TRAINNER_NAME like '$search_name%')";
  	if(trim($search_train_skill)) $arr_search_condition[] = "(TN_TRAIN_SKILL1 like '$search_train_skill%' || TN_TRAIN_SKILL2 like '$search_train_skill%' || TN_TRAIN_SKILL3 like '$search_train_skill%')";
	if(trim($search_inout_org) < 4) {
		$temp_per_status = $search_inout_org - 1;		
		$arr_search_condition[] = "(TN_INOUT_ORG = $temp_per_status)";	
	} 
	$search_condition = "";
	if(count($arr_search_condition)) $search_condition = " where " . implode(" and ", $arr_search_condition);

	if($DPISDB=="odbc"){	
		$cmd = " select 	distinct 	TRAINNER_ID, TRAINNER_NAME, tn_train_skill1, tn_train_skill2, tn_train_skill3, tn_address_tel, tn_work_place, tn_work_tel, TN_POSITION
						 from 		PER_TRAINNER
						$search_condition
						  $order_str  ";
	}elseif($DPISDB=="oci8"){
		$cmd = " select 	distinct TRAINNER_ID, TRAINNER_NAME, tn_train_skill1, tn_train_skill2, tn_train_skill3, tn_address_tel, tn_work_place, tn_work_tel, TN_POSITION
								  from 		PER_TRAINNER
								  $search_condition
								 $order_str";
	}elseif($DPISDB=="mysql"){
		$cmd = " select 	distinct	TRAINNER_ID, TRAINNER_NAME, tn_train_skill1, tn_train_skill2, tn_train_skill3, tn_address_tel, tn_work_place, tn_work_tel, TN_POSITION
							from 	PER_TRAINNER
								$search_condition
								 $order_str ";
	} // end if
	$count_data = $db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();
//	echo "$cmd ($count_data)<br>";
	$data_count = $data_row = 0;
	while($data = $db_dpis->get_array()){
		$data_row++;

		$TRAINNER_NAME = trim($data[TRAINNER_NAME]);
		$TN_TRAIN_SKILL1 = $data[TN_TRAIN_SKILL1];
		$TN_TRAIN_SKILL2 = $data[TN_TRAIN_SKILL2];
		$TN_TRAIN_SKILL3 = $data[TN_TRAIN_SKILL3];
		$TN_ADDRESS_TEL = $data[TN_ADDRESS_TEL];
		$TN_POSITION = $data[TN_POSITION];
		$TN_WORK_PLACE = $data[TN_WORK_PLACE];
		$TN_WORK_TEL = $data[TN_WORK_TEL];

		$arr_content[$data_count][tn_id] = $data[TRAINNER_ID];
		$arr_content[$data_count][tn_name] = $TRAINNER_NAME;
		$arr_content[$data_count][tn_pos] = $TN_POSITION;
		$arr_content[$data_count][tn_workplace] = $TN_WORK_PLACE;
		$arr_content[$data_count][tn_wroktel] = $TN_WORK_TEL;
				
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
			$TRAINNER_ID = $arr_content[$data_count][tn_id];
			$TRAINNER_NAME = $arr_content[$data_count][tn_name];
			$TN_POSITION = $arr_content[$data_count][tn_pos];
			$TN_WORK_PLACE = $arr_content[$data_count][tn_workplace];
			$TN_WORK_TEL = $arr_content[$data_count][tn_wroktel];

			//new format************************************************************			
            	$arr_data = (array) null;
				$arr_data[] = (($NUMBER_DISPLAY==2)?convert2thaidigit($TRAINNER_ID):$TRAINNER_ID);
				$arr_data[] = "$TRAINNER_NAME";
				$arr_data[] =  "$TN_POSITION";
				$arr_data[] = "$TN_WORK_PLACE";
				$arr_data[] =  (($NUMBER_DISPLAY==2)?convert2thaidigit($TN_WORK_TEL):$TN_WORK_TEL);

				$data_align = array("C", "L", "L", "L", "L");
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