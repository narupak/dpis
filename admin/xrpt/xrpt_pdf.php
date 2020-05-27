<?

	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php");

	ini_set("max_execution_time", "1800");
	
	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "";
	$report_code = "";
	if(array_sum($width_pdf) < 80) { 
		$orientation='P';
	} else {
		$orientation='L';
	}
	session_cache_limiter("private");
	session_start();

	$pdf = new PDF($orientation,$unit,$paper_size,$lang_code,$company_name,$report_title,$report_code,$heading,$heading_width,$heading_align);
	
 	$pdf->Open();
	$pdf->SetMargins(5,5,5);
	$pdf->AliasNbPages();
	$pdf->AddPage();
	$pdf->SetTextColor(0, 0, 0);
	$pdf->SetFont('angsa','',14);
	
	$page_start_x = $pdf->x;			
	$page_start_y = $pdf->y;

	$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
	$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
	
	foreach($col_array as $header_key => $header_value) {
		if($header_key == (count($col_array) - 1))	$pdf->Cell(($width_pdf[$header_key]*2)+6 ,7,$header_value,'LTBR',1,'C',1);
		else $pdf->Cell(($width_pdf[$header_key]*2)+6,7,$header_value,'LTBR',0,'C',1);
	}
	
	$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
	$pdf->SetFillColor(hexdec("FF"),hexdec("FF"),hexdec("FF"));
	
	foreach($array_content as $content_key => $content_row){
		foreach($content_row as $content_col => $content_value) {
			if($content_col == (count($content_row) - 1))	$pdf->Cell(($width_pdf[$content_col]*2)+6 ,7,$content_value,'LTBR',1,'C',1);
			else $pdf->Cell(($width_pdf[$content_col]*2)+6 ,7,$content_value,'LTBR',0,'C',1);
		}
	}
	
	$pdf->close();
	$fname_pdf = "../../PDF/TMP/dpis_$token.pdf";
	$pdf->Output("$fname_pdf");	

?>