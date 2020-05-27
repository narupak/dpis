<?
	include("../../php_scripts/connect_database.php");
	if (!$FLAG_RTF) include("../php_scripts/pdf_wordarray_thaicut.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	
	include ("rpt_structure_format.php"); // เก็บ format ของหัวรายงาน

	if ($FLAG_RTF) {
		include ("rtf_setvar.php");	// กำหนดตัวแปรค่าสี set_of_colors
		require("../../RTF/rtf_class.php");
	} else	 {
		define('FPDF_FONTPATH','../../PDF/font/');
		include ("../../PDF/fpdf.php");
		include ("../../PDF/pdf_extends_DPIS.php");
	}

	ini_set("max_execution_time", $max_execution_time);

	/*
	......
	*/

	$company_name = "ชื่อบริษัท";
	$report_title = "หัวรายงาน";
	$report_code = "R_structure";
	include ("rpt_structure_format.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน
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

		$fname= "rpt_structure_rtf.rtf";

	//	RTF $papersize = "a4", $margl = 720, $margr = 720, $margt = 720, $margb = 720, $orient = "p", $u_color_tab="";
		$paper_size="a4";
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
		$pdf->SetFont($font,'',14);
		$pdf->SetMargins(5,5,5);
		$pdf->AliasNbPages();
		$pdf->AddPage();
		$pdf->SetTextColor(0, 0, 0);
		
		$page_start_x = $pdf->x;			$page_start_y = $pdf->y;
	}

	if($DPISDB=="odbc"){
		$cmd = " select	 * from table order by index ";
	}elseif($DPISDB=="oci8"){
		$cmd = " select	 * from table order by index ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select	 * from table order by index ";
	}
	$count_data = $db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();
	$data_count = 0;
	$MINISTRY_ID = -1;		$DEPARTMENT_ID_INI = -1;
	$rpt_order_start_index = 0;
	while($data = $db_dpis->get_array()){
		/*
		 
		data loop	 การจัดการกับข้อมูล เติมข้อมูลใน
		ตัวอย่าง	$arr_content[$data_count][type] = "ORG";
		
		*/
	} // end while

	// ส่วนเริ่มตาราง
	$head_text1 = implode(",", $heading_text);		// ชื่อหัวของตาราง
	$head_width1 = implode(",", $heading_width);	// ความกว้างของ column
	$head_align1 = implode(",", $heading_align);	// align ของหัวตาราง
	$col_function = implode(",", $column_function);	// กำหนดคื่ออื่น ๆ ของ แต่ละ column
	if ($FLAG_RTF) { // ถ้า == 1 เป็น RTF ไม่งั้น เป็น pdf
		$RTF->add_header("", 0, false);	// header default
		$RTF->add_footer("", 0, false);		// footer default
		
//		echo "$head_text1<br>";
		$tab_align = "center";
		$result = $RTF->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", $fmtTableHeader_col_idx, $fmtTableHeader_bgcol_idx, $COLUMN_FORMAT, $col_function, false, $tab_align);
	} else {
		$pdf->AutoPageBreak = false;
//		echo "$head_text1<br>";
		$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", "0066CC", "EEEEFF", $COLUMN_FORMAT, $col_function);
	}
	if (!$result) echo "****** error ****** on open table for $table<br>";
	// จบส่วนเริ่มตาราง
			
	if($count_data){
		for($data_count=0; $data_count<count($arr_content); $data_count++){

			$arr_data = (array) null;
			$arr_data[] = $arr_content[$data_count][data1];	// ข้อมูล column ที่ 1
			$arr_data[] = $arr_content[$data_count][data2];	// ข้อมูล column ที่ 2
			$arr_data[] = $arr_content[$data_count][data3];	// ข้อมูล column ที่ 3
			$arr_data[] = $arr_content[$data_count][data4];	// ข้อมูล column ที่ 4

			$data_align = array("L", "R", "R", "R");
			
			if ($FLAG_RTF)
				$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
			else
				$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
		} // end for

		// บรรทัดรวม (ถ้ามี)
		$arr_data = (array) null;
		$arr_data[] = "รวมทั้งสิ้น";
		$arr_data[] = $total_data1;
		$arr_data[] = $total_data2;
		$arr_data[] = $total_data3;
		$data_align = array("L", "R", "R", "R");
			
		if ($FLAG_RTF)
			$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", $fmtTableHeader_col_idx, $fmtTableHeader_bgcol_idx);
		else
			$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", "FF0000", "");
		if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
		// จบยรรทัดรวม
	}else{
		if ($FLAG_RTF)
			$result = $RTF->add_text_line("********** ไม่มีข้อมูล **********", 7, "", "C", "", "14", "b", 0, 0);
		else
			$result = $pdf->add_text_line("********** ไม่มีข้อมูล **********", 7, "", "C", "", "14", "b", 0, 0);
		if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";
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
?>