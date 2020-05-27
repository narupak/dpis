<?
	include("../../php_scripts/connect_database.php");
	include("../php_scripts/pdf_wordarray_thaicut.php");
	include ("../php_scripts/function_share.php");
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php");

	ini_set("max_execution_time", $max_execution_time);
	
	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "";
	$report_title = trim($report_title);
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
	
	$heading_width[0] = "5";
	$heading_width[1] = "190";
		
	function print_header(){
		global $pdf, $heading_width, $DEPARTMENT_NAME;
		
		$pdf->SetFont($font,'',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
		$pdf->Cell($heading_width[0],7, "",0,0,'C');
		$pdf->Cell($heading_width[1],7,"กรอบโครงสร้างอัตรากำลัง ข้าราชการ $DEPARTMENT_NAME",0,1,'C');
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell($heading_width[0],4, "",0,0,'C');
		$pdf->Cell($heading_width[1],4,"",0,1,'C');
	} // function		
	
	function print_sub_org($org_id, $layer) {
		global $dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd, $arr_content, $pdf, $heading_width;
	
		$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
			
		$cmd = "	select * from PER_ORG where ORG_ID = $org_id ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis1->send_cmd($cmd);
		if ($data1 = $db_dpis1->get_array()) {
			$org_name = $data1[ORG_NAME];
		}
		if ($layer-1 > 0) {
			$tab=str_repeat("-",$layer-1);
		} else {
			$tab="";
		}
//		echo "$tab>$org_name<br>";
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->Cell($heading_width[0],7, "",0,0,'C');
		if ($layer==1) {
			$pdf->Cell($heading_width[1], 7, $org_name, "LR", 1, 'C', 0);
		} else {
			$pdf->Cell($heading_width[1], 7, "$tab$org_name", "LR", 1, 'L', 0);
		}

		$cmd = "	select * from PER_ORG where ORG_ID_REF = $org_id 
						order by ORG_ID ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$count_data1 = $db_dpis1->send_cmd($cmd);
		if ($count_data1) {

			$data_count = $data_row = 0;
			$layer_sub=$layer+1;
			while($data1 = $db_dpis1->get_array()){
		
				$org_ref1 = $data1[ORG_ID];
				print_sub_org($org_ref1, $layer_sub);
				$data_count++;
				$data_row++;
			} // end loop while
		} // end if ($count_data1)
//		$pdf->Cell($heading_width[0],4,"|","T",1,'C');
	} // end function print_sub_org
	
	$cmd = " select * from PER_ORG where ORG_ID = $SESS_DEPARTMENT_ID";
	if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	if($count_data){
//		$pdf->AutoPageBreak = false;
		$data_count = $data_row = 0;
		while($data = $db_dpis->get_array()){
			$DEPARTMENT_NAME = $data[ORG_NAME];
			print_header();
			$org_ref = $data[ORG_ID];

			$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
			$pdf->Cell($heading_width[0], 7, "",0,0,'C');
			$pdf->Cell($heading_width[1], 7, $DEPARTMENT_NAME, 1, 1, 'C', 0);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
			$pdf->Cell($heading_width[0],3, "",0,0,'C');
			$pdf->Cell($heading_width[1],3,"|","T",1,'C');

			$cmd = "	select * from PER_ORG where ORG_ID_REF = $org_ref 
							order by ORG_ID ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$count_data2 = $db_dpis2->send_cmd($cmd);
			if ($count_data2) {
				$layer=1;
				while($data2 = $db_dpis2->get_array()){
					$org_ref2 = $data2[ORG_ID];
					$pdf->Cell($heading_width[0],4, "",0,0,'C');
					$pdf->Cell($heading_width[1],4,"|","B",1,'C');
					print_sub_org($org_ref2, $layer);
					$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
					$pdf->Cell($heading_width[0],3, "",0,0,'C');
					$pdf->Cell($heading_width[1],3,"|","T",1,'C');
				} // end loop while
			} // end if ($count_data1)
		} // end while
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell($heading_width[0],4, "",0,0,'C');
		$pdf->Cell($heading_width[1],4,"|",0,1,'C');
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->Cell($heading_width[0],7, "",0,0,'C');
		$pdf->Cell($heading_width[1],7,"E N D",1,1,'C');
	}else{
		$pdf->SetFont($font,'b','',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(200,10,"********** ไม่มีข้อมูล **********",0,1,'C');
	} // end if

	$pdf->close();
	$pdf->Output();

	ini_set("max_execution_time", 30);
?>