<?
	include("../../php_scripts/connect_database.php");
	include ("../php_scripts/function_share.php");

	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends.php");

//	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	// ==== use for testing phase =====
	$db_dpis = $db;
	$db_dpis2 = new connect_db($db_host, $db_name, $db_user, $db_pwd);
	// ==========================
	
	$db2 = new connect_db($db_host, $db_name, $db_user, $db_pwd);
	
	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "";
	$report_title = "สรุปการเทียบเคียงข้อมูลตำแหน่งว่าง";
	$report_code = "";
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
	$pdf->SetFont('angsa','',14);
	
	$heading_width[0] = "40";
	$heading_width[1] = "13";
	$heading_width[2] = "13";
		
	$cmd = " select PT_GROUP_N, PT_GROUP_NAME from PER_GROUP_N order by PT_GROUP_N ";
	$count_data = $db_dpis->send_cmd($cmd);
	while($data_dpis = $db_dpis->get_array()){
		$PT_GROUP_N = trim($data_dpis[PT_GROUP_N]);
		$PT_GROUP_NAME = trim($data_dpis[PT_GROUP_NAME]);

		$arr_group[$PT_GROUP_N] = $PT_GROUP_NAME;
		
		$cmd = " select PT_CODE_N, PT_NAME_N from PER_TYPE_N where trim(PT_GROUP_N)='$PT_GROUP_N' ";
		$db_dpis2->send_cmd($cmd);
		while($data_dpis2 = $db_dpis2->get_array()){
			$PT_CODE_N = trim($data_dpis2[PT_CODE_N]);
			$PT_NAME_N = trim($data_dpis2[PT_NAME_N]);

			$arr_type[$PT_GROUP_N][$PT_CODE_N] = $PT_NAME_N;
			$arr_type_count[$PT_GROUP_N][$PT_CODE_N] = 0;
		} // end while

		$arr_type[$PT_GROUP_N][TOTAL] = "รวม";
		$arr_type_count[$PT_GROUP_N][TOTAL] = 0;		
	} // end while	

	function print_header(){
		global $pdf, $heading_width;
		global $arr_group, $arr_type;
		
		$pdf->SetFont('angsa','',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
		$pdf->Cell(100,2,"",0,1,'C');
		
		$line_start_x = $pdf->x;			$line_start_y = $pdf->y;

		$pdf->Cell($heading_width[0] ,7,"ประเภทตำแหน่ง",'LTR',0,'R',1);
		foreach($arr_group as $PT_GROUP_N => $PT_GROUP_NAME){
			$pdf->Cell(($heading_width[1] * count($arr_type[$PT_GROUP_N])) ,7,"$PT_GROUP_NAME",'LTR',0,'C',1);
		} // end foreach
		$pdf->Cell($heading_width[2] ,7,"รวม",'LTR',1,'C',1);
		
		$line_end_x = $pdf->x + $heading_width[0];			$line_end_y = $pdf->y + 7;

		$pdf->Cell($heading_width[0] ,7,"ระดับควบ",'LBR',0,'L',1);
		foreach($arr_group as $PT_GROUP_N => $PT_GROUP_NAME){
			foreach($arr_type[$PT_GROUP_N] as $PT_CODE_N => $PT_NAME_N){
				if($PT_CODE_N == "TOTAL") $PT_CODE_N = "รวม";
				$pdf->Cell($heading_width[1] ,7,"$PT_CODE_N",'LTBR',0,'C',1);
			} // end foreach
		} // end foreach
		$pdf->Cell($heading_width[2] ,7,"",'LBR',1,'C',1);		
		
		$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
	} // function		
	
	$cmd = " select distinct CL_NAME from PER_MAP_POSITION where PER_ID=0 order by CONVERT(LEVEL_NO, UNSIGNED) ";
	$count_data = $db->send_cmd($cmd);
	if($count_data){
		$GRAND_TOTAL = 0;
		$data_count = 0;
		while($data = $db->get_array()){
			$data_count++;
			if(($data_count % 24) == 1){
				if($data_count > 1) $pdf->AddPage();
				print_header();
			} // end if

			$CL_NAME = trim($data[CL_NAME]);

			$pdf->SetFont('angsa','',14);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
			$pdf->Cell($heading_width[0], 7, "$CL_NAME", 'LBR', 0, 'C', 0);
			
			$count_co_level_total = 0;
			foreach($arr_group as $PT_GROUP_N => $PT_GROUP_NAME){

				$count_type_total = 0;
				foreach($arr_type[$PT_GROUP_N] as $PT_CODE_N => $PT_NAME_N){
					if($PT_CODE_N != "TOTAL"){
						$cmd = " select POS_ID from PER_MAP_POSITION where PER_ID=0 and trim(CL_NAME)='$CL_NAME' and trim(PT_CODE_N)='$PT_CODE_N' ";
						$count_position = $db2->send_cmd($cmd);
						$count_type_total += $count_position;
						$count_co_level_total += $count_position;	
	
						$arr_type_count[$PT_GROUP_N][$PT_CODE_N] += $count_position;
						$arr_type_count[$PT_GROUP_N][TOTAL] += $count_position;

						$pdf->SetFont('angsa','',14);
						$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
						$pdf->Cell($heading_width[1], 7, (($count_position)?$count_position:""), 'LBR', 0, 'R', 0);
					}else{
						$pdf->SetFont('angsab','',14);
						$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
						$pdf->Cell($heading_width[1], 7, "$count_type_total", 'LBR', 0, 'R', 0);
					} // end if
				} // end foreach
			} // end foreach
			
			$GRAND_TOTAL += $count_co_level_total;
			
			$pdf->SetFont('angsab','',14);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
			$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
			$pdf->Cell($heading_width[2], 7, "$count_co_level_total", 'LTBR', 1, 'R', 1);
		} // end while
		
		$pdf->SetFont('angsa','',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));

		$pdf->Cell($heading_width[0], 7, "รวม", 'LTBR', 0, 'C', 1);
		foreach($arr_group as $PT_GROUP_N => $PT_GROUP_NAME){
			foreach($arr_type[$PT_GROUP_N] as $PT_CODE_N => $PT_NAME_N){
				if($PT_CODE_N != "TOTAL"){
					$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
					$pdf->Cell($heading_width[1], 7, number_format($arr_type_count[$PT_GROUP_N][$PT_CODE_N], 0, "", ""), 'LTBR', 0, 'R', 1);
				}else{
					$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
					$pdf->Cell($heading_width[1], 7, number_format($arr_type_count[$PT_GROUP_N][TOTAL], 0, "", ""), 'LTBR', 0, 'R', 1);
				} // end if
			} // end foreach
		} // end foreach
		$pdf->Cell($heading_width[2], 7, "$GRAND_TOTAL", 'LTBR', 1, 'R', 1);
	}else{
		$pdf->SetFont('angsab','',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(270,10,"********** ไม่มีข้อมูล **********",0,1,'C');		
	} // end if
	
	$pdf->close();
	$pdf->Output();
?>