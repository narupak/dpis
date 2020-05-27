<?
	include("../../php_scripts/connect_database.php");
	include("../php_scripts/pdf_wordarray_thaicut.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	include("../php_scripts/load_per_control.php");

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];

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

	$report_title = "สอบถามข้อมูลบัญชีแนบท้ายคำสั่งยกเลิกคำสั่งเดิม";
	$report_code = "P1701";
	

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();
	if ($FLAG_RTF) {
//		$sum_w = array_sum($heading_width);
		$sum_w = 0;
		for($h = 0; $h < count($heading_width); $h++) {
			if ($arr_column_sel[$h]==1)
				$sum_w += $heading_width[$h];
		}
		for($h = 0; $h < count($heading_width); $h++) {
			if ($arr_column_sel[$h]==1)
			$heading_width[$h] = $heading_width[$h] / $sum_w * 100;
		}
		$fname= "rpt_data_cancel_comdtl.rtf";
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
	include ("rpt_data_cancel_comdtl_format.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน
/*
	$heading_width[0] = "20";
	$heading_width[1] = "30";
	$heading_width[2] = "120";
	$heading_width[3] = "25";
	$heading_width[4] = "50";
	$heading_width[5] = "30";
	
		//new format**************************************************
    $heading_text[0] = "$SEQ_NO_TITLE";
	$heading_text[1] = "$COM_NO_TITLE";
	$heading_text[2] = "$COM_NAME_TITLE";
	$heading_text[3] = "$COM_DATE_TITLE";
	$heading_text[4] = "$COM_TYPE_TITLE";
	$heading_text[5] = "$CONFIRM_TITLE";

	$heading_align = array('C','C','C','C','C','C');
*/
	if($DPISDB=="odbc"){	
		$cmd = "	select	distinct COM_ID, COM_NO, a.COM_NAME, COM_DATE, a.COM_TYPE, COM_CONFIRM 
						from		PER_COMMAND a, PER_COMTYPE b 
						where COM_GROUP in ('513') and a.COM_TYPE=b.COM_TYPE
									$search_condition 
						order by 	COM_DATE desc, COM_NO 	";	
	}elseif($DPISDB=="oci8"){
		$cmd = " select 	distinct COM_ID, COM_NO, a.COM_NAME, COM_DATE, a.COM_TYPE, COM_CONFIRM
								  from 		PER_COMMAND a, PER_COMTYPE b 
								  where 		COM_GROUP in ('513') and a.COM_TYPE=b.COM_TYPE
												$search_condition
								  order by 	COM_DATE desc, COM_NO ";
	}elseif($DPISDB=="mysql"){
		$cmd = "	select	distinct	COM_ID, COM_NO, a.COM_NAME, COM_DATE, a.COM_TYPE, COM_CONFIRM 
						from		PER_COMMAND a, PER_COMTYPE b 
						where COM_GROUP in ('513') and a.COM_TYPE=b.COM_TYPE
									$search_condition 
						order by 	COM_DATE desc, COM_NO ";
	} // end if
	$count_data = $db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();
//	echo "$cmd ($count_data)<br>";
	$data_count = $data_row = 0;
	while($data = $db_dpis->get_array()){
		$data_row++;

		$TMP_COM_ID = $data[COM_ID];
		$TMP_COM_NO = trim($data[COM_NO]);
		$TMP_COM_NAME = trim($data[COM_NAME]);
		$TMP_COM_DATE = show_date_format($data[COM_DATE], 5);
		$TMP_COM_CONFIRM = trim($data[COM_CONFIRM]);
		
		$TMP_COM_TYPE = trim($data[COM_TYPE]);
		$TMP_COM_TYPE_NAME = "";
		$cmd = "select COM_NAME from PER_COMTYPE where COM_TYPE='$TMP_COM_TYPE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$TMP_COM_TYPE_NAME = trim($data2[COM_NAME]);

		if ($TMP_COM_CONFIRM==1) { 
			$confirm = "ยืนยัน";
		} else {
			$confirm = "---";
		}

		$arr_content[$data_count][num] = $data_row;
		$arr_content[$data_count][com_no] = $TMP_COM_NO;
		$arr_content[$data_count][com_name] = $TMP_COM_NAME;
		$arr_content[$data_count][com_date] = $TMP_COM_DATE;
		$arr_content[$data_count][com_type] = $TMP_COM_TYPE_NAME;
		$arr_content[$data_count][com_confirm] = $confirm;
				
		$data_count++;
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
//new format****************************************************************
	if($count_data){
		 $head_text1 = implode(",", $heading_text);
		$head_width1 = implode(",", $heading_width);
		$head_align1 = implode(",", $heading_align);
		$col_function = implode(",", $column_function);
		if ($FLAG_RTF) {
			$RTF->add_header("", 0, false);	// header default
			$RTF->add_footer("", 0, false);		// footer default
			
	//		echo "$head_text1<br>";
			$tab_align = "center";
			$result = $RTF->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", $fmtTableHeader_col_idx, $fmtTableHeader_bgcol_idx, $COLUMN_FORMAT, $col_function, false, $tab_align);
		} else {
			$pdf->AutoPageBreak = false; 
		$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", "0066CC", "EEEEFF", $COLUMN_FORMAT, $col_function);
		          }
		if (!$result) echo "****** error ****** on open table for $table<br>";

		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$data_num = $arr_content[$data_count][num];
			$TMP_COM_NO = $arr_content[$data_count][com_no];
			$TMP_COM_NAME = $arr_content[$data_count][com_name];
			$TMP_COM_DATE = $arr_content[$data_count][com_date];
			$TMP_COM_TYPE_NAME = $arr_content[$data_count][com_type];
			$confirm = $arr_content[$data_count][com_confirm];


	//new format************************************************************			
            	$arr_data = (array) null;
				$arr_data[] = $data_num;
				$arr_data[] = $TMP_COM_NO;
				$arr_data[] = $TMP_COM_NAME;
				$arr_data[] = $TMP_COM_DATE;
				$arr_data[] = $TMP_COM_TYPE_NAME;
            	$arr_data[] = $confirm;
		
				$data_align = array("C", "C", "L", "C", "L", "C");
				  if ($FLAG_RTF)
				  $result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
				 else
				 $result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "14", "", "000000", "");		//TRHBL
				if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
			} // end for
			  if (!$FLAG_RTF) 
			$pdf->add_data_tab("", 0, "RHBL", $data_align, "", "14", "", "000000", "");		// เส้นปิดบรรทัด		
		    if ($FLAG_RTF) {
			$RTF->close_tab(); 
			}else {
			$pdf->close_tab("");
			        } 
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