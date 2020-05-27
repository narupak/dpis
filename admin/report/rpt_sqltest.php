<?
	include("../../php_scripts/connect_database.php");
	include("../php_scripts/pdf_wordarray_thaicut.php");
	include ("../php_scripts/function_share.php");

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];

	$report_title = stripslashes($report_title);
	$sel_cmd= stripslashes($sel_cmd);
//	echo "report_title=$report_title , sel_cmd=$sel_cmd<br>";

	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php");

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "";
	$report_title = trim((($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title));
	$report_code = "";
	$orientation='P';

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
	$pdf->SetAutoPageBreak(true,10);
	
	$count_data = $db_dpis->send_cmd($sel_cmd);
	//$db_dpis->show_error();
	$data = $db_dpis->get_array();
	$data = array_change_key_case($data, CASE_LOWER);
	
//	begin set format
	include ("rpt_sqltest_format.php");
//	end set format

	$count_data = $db_dpis->send_cmd($sel_cmd);
	if($count_data){
		$head_text1 = implode(",", $heading_text);
		$head_width1 = implode(",", $heading_width);
		$head_align1 = implode(",", $heading_align);
		$col_function = implode(",", $column_function);
	//	echo "head_text:$head_text1<br>";
		$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", "0066CC", "EEEEFF", $COLUMN_FORMAT, $col_function);
		if (!$result) echo "****** error ****** on open table for $table<br>";
		
		$data_count = $data_row = 0;
		while($data = $db_dpis->get_array()){
			$data = array_change_key_case($data, CASE_LOWER);
			$data_count++;
			$data_row++;			

			$data_align = (array) null;
			$arr_data = (array) null;			
			foreach($data as $key => $val) {
				$arr_data[] = $val;
				$data_align[] = "C";
			}

			$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
		} // end while
		$pdf->close_tab();
	}else{
		$pdf->SetFont($font,'b','',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(200,10,"********** ไม่มีข้อมูล **********",0,1,'C');
	} // end if

	$pdf->close();
	$pdf->Output();

	ini_set("max_execution_time", 30);
?>