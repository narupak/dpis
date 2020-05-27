<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php");

	ini_set("max_execution_time", $max_execution_time);

	$NUMBER_DISPLAY = $NUMBER_DISPLAY1;
	
	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
//	$company_name = "รูปแบบการออกรายงาน : $list_type_text";
	$report_title = "ทดสอบรายงาน";
	$report_code = "R00test";
	$orientation='L';

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();

	$pdf = new PDF($orientation,$unit,$paper_size,$lang_code,$company_name,$report_title,$report_code,$heading,$heading_width,$heading_align);
	
 	$pdf->Open();
	$pdf->SetMargins(5,5,5);
	$pdf->AliasNbPages();
	$pdf->AddPage();
	$pdf->SetTextColor(0, 0, 0);
	$pdf->SetFont($font,'',14);
	
	$page_start_x = $pdf->x;			$page_start_y = $pdf->y;

	include ("rpt_R00test_format.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน

	$arr_content[0][seq] = 1;
	$arr_content[0][name] = "นาม 1";
	$arr_content[0][num] = 12;
	$arr_content[0][val] = 200.34;
	$arr_content[1][seq] = 2;
	$arr_content[1][name] = "นาม 2";
	$arr_content[1][num] = 15.032;
	$arr_content[1][val] = 130.25;
	$arr_content[2][seq] = 3;
	$arr_content[2][name] = "นาม 3";
	$arr_content[2][num] = 7.2;
	$arr_content[2][val] = 325.00;
	$arr_content[3][seq] = 4;
	$arr_content[3][name] = "นาม 4";
	$arr_content[3][num] = 3012.2;
	$arr_content[3][val] = 7522.10;
	$arr_content[4][seq] = 5;
	$arr_content[4][name] = "นาม 5";
	$arr_content[4][num] = 8;
	$arr_content[4][val] = 412.50;
	$arr_content[5][seq] = 6;
	$arr_content[5][name] = "นาม 6";
	$arr_content[5][num] = 14.2000;
	$arr_content[5][val] = 125.67;
	$count_data = 6;

//	echo "NUMBER_DISPLAY=$NUMBER_DISPLAY, NUMBER_DISPLAY1=$NUMBER_DISPLAY1<br>";
//	echo "column_function=".implode("|",$column_function)."<br>";

	if($count_data){
		$pdf->AutoPageBreak = false; 
		$head_text1 = implode(",", $heading_text);
		$head_width1 = implode(",", $heading_width);
		$head_align1 = implode(",", $heading_align);
		$col_function = implode(",", $column_function);
//		echo "$head_text1<br>";
		$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", "0066CC", "EEEEFF", $COLUMN_FORMAT, $col_function);
		if (!$result) echo "****** error ****** on open table for $table<br>";
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$r_seq = $arr_content[$data_count][seq];
			$r_name = $arr_content[$data_count][name];
			$r_num = $arr_content[$data_count][num];
			$r_val = $arr_content[$data_count][val];

			$arr_data = (array) null;
			$arr_data[] = $r_seq;
			$arr_data[] = $r_name;
			$arr_data[] = $r_num;
			$arr_data[] = $r_val;

			$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "cordia", "14", "", "000000", "");		//TRHBL
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
		} // end for
	}else{
		$result = $pdf->add_text_line("********** ไม่มีข้อมูล **********", 7, "", "L", "", "14", "b", 0, 0);
		if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";
	} // end if
	$pdf->close_tab(""); 

	$pdf->close();
	$pdf->Output();			
?>