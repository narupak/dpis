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
	
	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "";
	$report_title = "สรุปการเทียบเคียงข้อมูลประเภทตำแหน่ง";
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
	$pdf->SetFont('angsa','',14);
	
	$heading_width[0] = "44";
	$heading_width[1] = "13";
	$heading_width[2] = "13";
		
	function print_header(){
		global $pdf, $heading_width;
		
		$pdf->SetFont('angsa','',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
		$pdf->Cell(100,2,"",0,1,'C');

		$pdf->Cell($heading_width[0] ,7,"ประเภทตำแหน่ง",'LTR',0,'C',1);
		$pdf->Cell(($heading_width[1] * 11) ,7,"ระดับตำแหน่ง",'LTR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"รวม",'LTR',1,'C',1);
		
		$pdf->Cell($heading_width[0] ,7,"",'LBR',0,'C',1);
		for($i=1; $i<=11; $i++) $pdf->Cell($heading_width[1] ,7,"$i",'LTRB',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"",'LBR',1,'C',1);		
	} // function		
	
	$cmd = " select PT_GROUP_N, PT_GROUP_NAME from PER_GROUP_N order by PT_GROUP_N ";
	$count_data = $db_dpis->send_cmd($cmd);
	$group_count = 0;
	while($data_dpis = $db_dpis->get_array()){
		$group_count++;
		$PT_GROUP_N = trim($data_dpis[PT_GROUP_N]);
		$PT_GROUP_NAME = trim($data_dpis[PT_GROUP_NAME]);

		$arr_group[$group_count][code] = $PT_GROUP_N;
		$arr_group[$group_count][name] = $PT_GROUP_NAME;
		for($i=1; $i<=11; $i++) $arr_group[$group_count]["level$i"] = 0;
		
		$cmd = " select PT_CODE_N, PT_NAME_N from PER_TYPE_N where trim(PT_GROUP_N)='$PT_GROUP_N' ";
		$db_dpis2->send_cmd($cmd);
		$type_count = 0;
		while($data_dpis2 = $db_dpis2->get_array()){
			$type_count++;
			$PT_CODE_N = trim($data_dpis2[PT_CODE_N]);
			$PT_NAME_N = trim($data_dpis2[PT_NAME_N]);

			$arr_type[$PT_GROUP_N][$type_count][code] = $PT_CODE_N;
			$arr_type[$PT_GROUP_N][$type_count][name] = $PT_NAME_N;
			for($i=1; $i<=11; $i++){ 
				$cmd = " select count(PER_ID) from PER_FORMULA where LEVEL_NO=$i and PT_CODE_N='$PT_CODE_N' ";
				$db->send_cmd($cmd);
				$data = $db->get_data();
				$count_per_id = $data[0];
				
				${"total_level".$i} += $count_per_id;
				$arr_group[$group_count]["level$i"] += $count_per_id;
				$arr_type[$PT_GROUP_N][$type_count]["level$i"] = $count_per_id;				
			} // end for
		} // end while
	} // end while

	if($count_data){
		print_header();	
		for($i=1; $i<=count($arr_group); $i++){
			$pdf->SetFont('angsab','',14);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
			
			$PT_GROUP_N = $arr_group[$i][code];
			$PT_GROUP_NAME = $arr_group[$i][name];
			
			$pdf->Cell($heading_width[0], 7, "$i. $PT_GROUP_NAME", 'LBR', 0, 'L', 0);
			$row_total = 0;
			for($level=1; $level<=11; $level++){ 
				$show_score = ($arr_group[$i]["level$level"])?$arr_group[$i]["level$level"]:"0";
				$row_total += $arr_group[$i]["level$level"];
				$pdf->Cell($heading_width[1], 7, $show_score, 'LBR', 0, 'R', 0);
			} // end for

			$pdf->SetFont('angsab','',14);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
			$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
			$pdf->Cell($heading_width[2], 7, "$row_total", 'LTBR', 1, 'R', 1);
			
			for($j=1; $j<=count($arr_type[$PT_GROUP_N]); $j++){
				$pdf->SetFont('angsa','',14);
				$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

				$PT_CODE_N = $arr_type[$PT_GROUP_N][$j][code];
				$PT_NAME_N = $arr_type[$PT_GROUP_N][$j][name];

				$pdf->Cell($heading_width[0], 7, "    $i.$j $PT_NAME_N", 'LBR', 0, 'L', 0);
				$row_total = 0;
				for($level=1; $level<=11; $level++){ 
					$show_score = ($arr_type[$PT_GROUP_N][$j]["level$level"])?$arr_type[$PT_GROUP_N][$j]["level$level"]:"";
					$row_total += $arr_type[$PT_GROUP_N][$j]["level$level"];
					$pdf->Cell($heading_width[1], 7, $show_score, 'LBR', 0, 'R', 0);
				} // end for

				$pdf->SetFont('angsa','',14);
				$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
				$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
				$pdf->Cell($heading_width[2], 7, "$row_total", 'LTBR', 1, 'R', 1);
			} // end for
		} // end for
		
		$pdf->SetFont('angsa','',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
		
		$pdf->Cell($heading_width[0], 7, "รวม", 'LTBR', 0, 'C', 1);
		$grand_total = 0;
		for($i=1; $i<=11; $i++){ 
			$grand_total += ${"total_level".$i};
			$pdf->Cell($heading_width[1], 7, ((${"total_level".$i})?${"total_level".$i}:"0"), 'LTBR', 0, 'R', 1);
		} // end for

		$pdf->SetFont('angsab','',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
		$pdf->Cell($heading_width[2], 7, "$grand_total", 'LTBR', 1, 'R', 1);
	}else{
		$pdf->SetFont('angsab','',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(200,10,"********** ไม่มีข้อมูล **********",0,1,'C');		
	} // end if

	$pdf->close();
	$pdf->Output();
?>