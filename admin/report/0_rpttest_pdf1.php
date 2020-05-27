<?	
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

 	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php");

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	$NUMBER_DISPLAY = 1;

	include ("0_rpttest_pdf1_format.php");	// ใช้เฉพาะในส่วนการแปลง COLUMN_FORMAT เท่านั้น
	
	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "บ.แสงดำ จำกัด";
	$report_title = "รายงานการทดสอบ class PDF เพื่อเตรียมการปรับปรุงการ เขียนรายงานใหม่";
	$report_code = "RPTTEST1";
	$orientation='L';
	$heading_border="TRL";
	$report_footer="ทดสอบพิมพ์หางรายงาน เพื่อการปรับปรุง class PDF";

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();

	$pdf = new PDF($orientation,$unit,$paper_size,$lang_code,$company_name,$report_title,$report_code,$heading,$heading_width,$heading_align, $heading_border,$report_footer,$NUMBER_DISPLAY);
	
 	$pdf->Open();
	$pdf->SetMargins(5,5,5);
	$pdf->AliasNbPages();
	$pdf->AddPage();
	$pdf->SetTextColor(0, 0, 0);
	$pdf->SetFont($font,'',14);
	
	$page_start_x = $pdf->x;	  $page_start_y = $pdf->y;
	
	$pdf->AutoPageBreak = false;

	$head_text1 = implode(",", $heading_text);
	$head_width1 = implode(",", $heading_width);
	$head_align1 = implode(",", $heading_align);
	$col_function = implode(",", $column_function);
//	echo "head_text:$head_text1<br>";
	$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", "0066CC", "EEEEFF", $COLUMN_FORMAT, $col_function);
	if (!$result) echo "****** error ****** on open table for $table<br>";

	$count_data = 4;
	$arr_data1[] = array("บรรทัดที่ 1",230,310,240,210,130,255);
	$arr_data1[] = array("บรรทัดที่ 2",110,150,320,280,400,165);
	$arr_data1[] = array("บรรทัดที่ 3",540,250,420,280,200,265);
	$arr_data1[] = array("บรรทัดที่ 4",260,350,120,380,100,465);	

	if($count_data){
		$arr_t = array("รวม",0,0,0,0,0,0);
		$gs = 0;
		for($ii = 0; $ii < $count_data; $ii++) {
			$s = 0;
			$arr_data = (array) null;
			for($jj = 0; $jj < count($arr_data1[$ii]); $jj++) {
				$arr_data[] = $arr_data1[$ii][$jj];
				if ($jj > 0) {
					$s += $arr_data1[$ii][$jj];
					$arr_t[$jj] += $arr_data1[$ii][$jj];
				}
			}
			$arr_data[] = $s;
//			echo "$ii--sum=$s<br>";

			$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
			$gs += $s;
		}
		$arr_t[] = $gs;

		$arr_data = $arr_t;

		$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";

//		$arr_data = array("สรุป","<**1**>243","<**1**>243","<**2**>567","<**2**>567","<**3**>192","<**3**>192","777");
		$arr_data = array("สรุป","<**1**>243","<**1**>243","<**1**>243","<**2**>567","<**2**>567","<**2**>567","777");

		$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
		
		$arr_data = array("สรุป 2","<**1**>1235","<**1**>1235","<**2**>7231","<**2**>7231","<**3**>7822","<**3**>7822","563823");
		$arr_align = array("L","R","R","R","R","R","R","R");

		$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $arr_align, "", "14", "b", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";

		$arr_data = array("สรุป 3","<**1**>3333","<**1**>3333","<**2**>4444","<**2**>4444","<**3**>5555","<**3**>5555","123446");
		$arr_align = array("R","L","L","R","R","C","C","R");

		$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $arr_align, "", "14", "b", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
	}else{
		$result = $pdf->add_text_line("********** ไม่มีข้อมูล **********", 7, "", "L", "", "14", "b", 0, 0);
		if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";
	} // end if

	$pdf->close_tab(""); 
	
	$pdf->close();
	$pdf->Output();
?>