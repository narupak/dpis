<?
	include("../../php_scripts/connect_database.php");
	include("../php_scripts/pdf_wordarray_thaicut.php");
	include ("../php_scripts/function_share.php");

	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php");

	ini_set("max_execution_time", $max_execution_time);
	
	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "";
	$report_code = "";
	$orientation='P';

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();

	$cmd = " select PL_CODE, LEVEL_NO from POS_DES_INFO where POS_DES_ID=$POS_DES_ID ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$PL_CODE = trim($data[PL_CODE]);
	$LEVEL_NO = trim($data[LEVEL_NO]);
	
	$cmd = " select PL_NAME from PER_LINE where PL_CODE='$PL_CODE' ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$PL_NAME = trim($data[PL_NAME]);
	
	$cmd = " select LEVEL_NAME from PER_LEVEL where LEVEL_NO='$LEVEL_NO' ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$LEVEL_NAME = trim($data[LEVEL_NAME]);
	$report_title = "ข้อมูลมาตรฐานกำหนดตำแหน่ง - $PL_NAME - $LEVEL_NAME";

	$pdf = new PDF($orientation,$unit,$paper_size,$lang_code,$company_name,$report_title,$report_code,$heading,$heading_width,$heading_align);
	
 	$pdf->Open();
	$pdf->SetMargins(5,5,5);
	$pdf->AliasNbPages();
	$pdf->AddPage();
	$pdf->SetTextColor(0, 0, 0);
	$pdf->SetFont('angsa','',14);
	$pdf->SetAutoPageBreak(true,10);
	
	$heading_width[0] = "60";
	$heading_width[1] = "140";
		
	function print_header($heading_text1, $heading_text2){
		global $pdf, $heading_width;
		
		$pdf->SetFont('angsa','',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
		$pdf->Cell(100,2,"",0,1,'C');

		$pdf->Cell($heading_width[0] ,7,"$heading_text1",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"$heading_text2",'LTBR',1,'C',1);
	} // function		
		
	$cmd = " select 	a.ACC_TYPE_ID, b.ACC_TYPE_NAME
					 from		ACCOUNTABILITY_LEVEL_TYPE a, ACCOUNTABILITY_TYPE b
					 where	a.ACC_TYPE_ID=b.ACC_TYPE_ID and a.PL_CODE='$PL_CODE' and a.LEVEL_NO='$LEVEL_NO'
					 order by a.ACC_TYPE_ID ";
	$db_dpis->send_cmd($cmd);
	while($data = $db_dpis->get_array()) { 
		$ARR_ACCOUNT_TYPE[] = $data[ACC_TYPE_ID];
		$ARR_ACCOUNT_TYPE_NAME[$data[ACC_TYPE_ID]] = trim($data[ACC_TYPE_NAME]);
	} // loop while

	$pdf->AutoPageBreak = false;

	$pdf->SetFont('angsa','',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
	$pdf->Cell(200 ,7,"หน้าที่ความรับผิดชอบของตำแหน่ง",'',1,'C',0);

	$cmd = "	select		POS_JOB_DES_INFO
						from		POS_JOB_DES_INFO
						where		POS_DES_ID=$POS_DES_ID and POS_JOB_DES_TYPE='a'  ";
	$count_info = $db_dpis->send_cmd($cmd);
	if($count_info){
		$pdf->Cell(200 ,7,"ข้อมูลทั่วไป",'',1,'L',0);

		$data = $db_dpis->get_array();
		$POS_JOB_DES_INFO = trim($data[POS_JOB_DES_INFO]);

		$pdf->MultiCell(200 ,7,$POS_JOB_DES_INFO,'', 'L');
	} // end if

	foreach($ARR_ACCOUNT_TYPE as $ACC_TYPE_ID){
		$pdf->SetFont('angsa','',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(200 ,7,$ARR_ACCOUNT_TYPE_NAME[$ACC_TYPE_ID],'',1,'L',0);

		$pdf->SetFont('angsa','',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$cmd = "	select		ACC_DESCRIPTION
							from		ACCOUNTABILITY_INFO_PRIMARY
							where		POS_DES_ID=$POS_DES_ID and ACC_TYPE_ID=$ACC_TYPE_ID
							order by ACC_ID ";
		$db_dpis->send_cmd($cmd);
		$ACC_COUNT = 0;
		while($data = $db_dpis->get_array()){
			$ACC_COUNT++;
			$ACC_DESCRIPTION = $ACC_COUNT." .". trim($data[ACC_DESCRIPTION]);
			$pdf->MultiCell(200 ,7,$ACC_DESCRIPTION,'','L');
		} // loop while
	} // loop foreach

	$ARR_JOB_TYPE = array("k", "s", "e");
	foreach($ARR_JOB_TYPE as $JOB_TYPE){
		$pdf->SetFont('angsa','',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

		if($JOB_TYPE == "k"){
			// ความรู้ที่จำเป็นในงาน
			$pdf->Cell(200 ,12,"ความรู้ที่จำเป็นในงาน",'',1,'C',0);
			$cmd = "	select		POS_JOB_DES_INFO
								from		POS_JOB_DES_INFO
								where		POS_DES_ID=$POS_DES_ID and POS_JOB_DES_TYPE='k' ";
			$count_info = $db_dpis->send_cmd($cmd);
			if($count_info){
				$pdf->Cell(200 ,7,"ข้อมูลทั่วไป",'',1,'L',0);
				$data = $db_dpis->get_array();
				$POS_JOB_DES_INFO = trim($data[POS_JOB_DES_INFO]);
				$pdf->MultiCell(200 ,7,$POS_JOB_DES_INFO,'', 'L');
			} // end if

			$heading_text1 = "ชื่อความรู้ที่จำเป็นในงาน";
			$heading_text2 = "ระดับที่จำเป็นในงาน";

			$cmd = "	select		a.POS_JOB_DES_PRI_ID, b.JOB_DES_NAME, c.JOB_DES_LEVEL_DESCRIPTION
								from		POS_JOB_DES_PRIMARY a, KNOWLEDGE_INFO b, KNOWLEDGE_LEVEL c
								where		a.POS_DES_ID=$POS_DES_ID and a.JOB_TYPE='$JOB_TYPE' and a.JOB_DES_ID=b.JOB_DES_ID 
												and a.JOB_DES_ID=c.JOB_DES_ID and a.JOB_DES_LEVEL=c.JOB_DES_LEVEL
								order by a.POS_JOB_DES_PRI_ID ";
		}elseif($JOB_TYPE == "s"){
			// ทักษะที่จำเป็นในงาน
			$pdf->Cell(200 ,12,"ทักษะที่จำเป็นในงาน",'',1,'C',0);
			$heading_text1 = "ทักษะที่จำเป็นในงาน";
			$heading_text2 = "ระดับที่จำเป็นในงาน";

			$cmd = "	select		a.POS_JOB_DES_PRI_ID, b.JOB_DES_NAME, c.JOB_DES_LEVEL_DESCRIPTION
								from		POS_JOB_DES_PRIMARY a, SKILL_INFO b, SKILL_LEVEL c
								where		a.POS_DES_ID=$POS_DES_ID and a.JOB_TYPE='$JOB_TYPE' and a.JOB_DES_ID=b.JOB_DES_ID 
												and a.JOB_DES_ID=c.JOB_DES_ID and a.JOB_DES_LEVEL=c.JOB_DES_LEVEL
								order by a.POS_JOB_DES_PRI_ID ";
		}elseif($JOB_TYPE == "e"){
			// ประสบการณ์ที่จำเป็นในงาน
			$pdf->Cell(200 ,12,"ประสบการณ์ที่จำเป็นในงาน",'',1,'C',0);
			$cmd = "	select		POS_JOB_DES_INFO
								from		POS_JOB_DES_INFO
								where		POS_DES_ID=$POS_DES_ID and POS_JOB_DES_TYPE='e' ";
			$count_info = $db_dpis->send_cmd($cmd);
			if($count_info){
				$pdf->Cell(200 ,7,"ข้อมูลทั่วไป",'',1,'L',0);
				$data = $db_dpis->get_array();
				$POS_JOB_DES_INFO = trim($data[POS_JOB_DES_INFO]);
				$pdf->MultiCell(200 ,7,$POS_JOB_DES_INFO,'', 'L');
			} // end if

			$heading_text1 = "ชื่อประสบการณ์ที่จำเป็นในงาน";
			$heading_text2 = "จำนวน (ปี)";

			$cmd = "	select		a.POS_JOB_DES_PRI_ID, b.JOB_DES_NAME, a.JOB_DES_LEVEL
								from		POS_JOB_DES_PRIMARY a, EXP_INFO b
								where		a.POS_DES_ID=$POS_DES_ID and a.JOB_TYPE='$JOB_TYPE' and a.JOB_DES_ID=b.JOB_DES_ID 
								order by a.POS_JOB_DES_PRI_ID ";
		} // end if

		print_header($heading_text1, $heading_text2);

		$count_info = $db_dpis->send_cmd($cmd);
		if($count_info){
			$info_count = 0;
			while($data = $db_dpis->get_array()){
				$info_count++;
				$JOB_DES_NAME = trim($data[JOB_DES_NAME]);
				if($JOB_TYPE == "e")	$JOB_DES_LEVEL_DESCRIPTION = trim($data[JOB_DES_LEVEL]);
				else $JOB_DES_LEVEL_DESCRIPTION = trim($data[JOB_DES_LEVEL_DESCRIPTION]);

				$border = 0;
				$pdf->SetFont('angsa','',14);
				$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

				$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
	
				$pdf->MultiCell($heading_width[0], 7, $JOB_DES_NAME, $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0];
				$pdf->y = $start_y;
				$pdf->MultiCell($heading_width[1], 7, $JOB_DES_LEVEL_DESCRIPTION, $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1];
				$pdf->y = $start_y;
	
				//================= Draw Border Line ====================
				$line_start_y = $start_y;		$line_start_x = $start_x;
				$line_end_y = $max_y;		$line_end_x = $start_x;
				$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
					
				for($i=0; $i<=1; $i++){
					$line_start_y = $start_y;		$line_start_x += $heading_width[$i];
					$line_end_y = $max_y;		$line_end_x += $heading_width[$i];
					$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				} // end for
				//====================================================
	
				if(($pdf->h - $max_y - 10) < 20){ 
					$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
					if($info_count < $count_info){
						$pdf->AddPage();
						print_header($heading_text1, $heading_text2);
						$max_y = $pdf->y;
					} // end if
				}else{
					$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
				} // end if
				$pdf->x = $start_x;			$pdf->y = $max_y;
			} // loop while
		}else{
		} // end if
	} // loop foreach
	
	// สมรรถนะที่จำเป็นในงาน
	$JOB_TYPE = "c";
	$pdf->Cell(200 ,12,"สมรรถนะที่จำเป็นในงาน",'',1,'C',0);
	$heading_text1 = "ชื่อสมรรถนะที่จำเป็นในงาน";
	$heading_text2 = "ระดับที่จำเป็นในงาน";

	$cmd = "	select		a.POS_JOB_DES_PRI_ID, b.JOB_DES_NAME, c.JOB_DES_LEVEL_DESCRIPTION,
											b.COMPETENCY_TYPE
						from		POS_JOB_DES_PRIMARY a, COMPETENCY_INFO b, COMPETENCY_LEVEL c
						where		a.POS_DES_ID=$POS_DES_ID and a.JOB_TYPE='$JOB_TYPE' and a.JOB_DES_ID=b.JOB_DES_ID 
										and a.JOB_DES_ID=c.JOB_DES_ID and a.JOB_DES_LEVEL=c.JOB_DES_LEVEL
						order by b.COMPETENCY_TYPE desc, a.POS_JOB_DES_PRI_ID ";

	print_header($heading_text1, $heading_text2);

	$count_info = $db_dpis->send_cmd($cmd);
	if($count_info){
		$info_count = 0;
		while($data = $db_dpis->get_array()){
			$info_count++;
			$JOB_DES_NAME = trim($data[JOB_DES_NAME]);
			$JOB_DES_LEVEL_DESCRIPTION = trim($data[JOB_DES_LEVEL_DESCRIPTION]);

			$border = 0;
			$pdf->SetFont('angsa','',14);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

			$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;

			$pdf->MultiCell($heading_width[0], 7, $JOB_DES_NAME, $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0];
			$pdf->y = $start_y;
			$pdf->MultiCell($heading_width[1], 7, $JOB_DES_LEVEL_DESCRIPTION, $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1];
			$pdf->y = $start_y;

			//================= Draw Border Line ====================
			$line_start_y = $start_y;		$line_start_x = $start_x;
			$line_end_y = $max_y;		$line_end_x = $start_x;
			$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				
			for($i=0; $i<=1; $i++){
				$line_start_y = $start_y;		$line_start_x += $heading_width[$i];
				$line_end_y = $max_y;		$line_end_x += $heading_width[$i];
				$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
			} // end for
			//====================================================

			if(($pdf->h - $max_y - 10) < 20){ 
				$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
				if($info_count < $count_info){
					$pdf->AddPage();
					print_header($heading_text1, $heading_text2);
					$max_y = $pdf->y;
				} // end if
			}else{
				$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
			} // end if
			$pdf->x = $start_x;			$pdf->y = $max_y;
		} // loop while
	}else{
	} // end if

	$pdf->close();
	$pdf->Output();

	ini_set("max_execution_time", 30);
?>