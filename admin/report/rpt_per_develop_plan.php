<?
	include("../../php_scripts/connect_database.php");
	include("../php_scripts/pdf_wordarray_thaicut.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php");

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	$cmd = " select 	KF_CYCLE, KF_START_DATE, KF_END_DATE, PER_ID from PER_KPI_FORM where KF_ID=$KF_ID ";
	$db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data = $db_dpis->get_array();
	$KF_CYCLE = trim($data[KF_CYCLE]);
	$chkKFStartDate = $data[KF_START_DATE];
	$chkKFEndDate = $data[KF_END_DATE];
	if($KF_CYCLE==1){	//ตรวจสอบรอบการประเมิน
		$KF_START_DATE_1 = show_date_format($data[KF_START_DATE], 1);
		$KF_END_DATE_1 = show_date_format($data[KF_END_DATE], 1);
		$KF_YEAR = substr($KF_END_DATE_1, 0, 6);
	}else if($KF_CYCLE==2){
		$KF_START_DATE_2 = show_date_format($data[KF_START_DATE], 1);
		$KF_END_DATE_2 = show_date_format($data[KF_END_DATE], 1);
		$KF_YEAR = substr($KF_END_DATE_2, 0, 6);
	}

	$PER_ID = $data[PER_ID];
	$cmd = " select 	PN_CODE, PER_NAME, PER_SURNAME, LEVEL_NO, PER_SALARY, PER_TYPE, POS_ID, POEM_ID, POEMS_ID
						 from PER_PERSONAL where	PER_ID=$PER_ID ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$PN_CODE = trim($data[PN_CODE]);
	$PER_NAME = trim($data[PER_NAME]);
	$PER_SURNAME = trim($data[PER_SURNAME]);
	$LEVEL_NO = trim($data[LEVEL_NO]);
	$PER_SALARY = trim($data[PER_SALARY]);
	$PER_TYPE = trim($data[PER_TYPE]);
	$POS_ID = trim($data[POS_ID]);
	$POEM_ID = trim($data[POEM_ID]);
	$POEMS_ID = trim($data[POEMS_ID]);
		
	$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='$PN_CODE' ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$PN_NAME = trim($data[PN_NAME]);
		
	$PER_NAME = $PN_NAME . $PER_NAME . " " . $PER_SURNAME;
		
	$cmd = " select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where trim(LEVEL_NO)='$LEVEL_NO' ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$LEVEL_NAME = trim($data[LEVEL_NAME]);
	$POSITION_LEVEL = $data[POSITION_LEVEL];
	if (!$POSITION_LEVEL) $POSITION_LEVEL = $LEVEL_NAME;

	if($PER_TYPE==1){
		$cmd = " select 	a.POS_NO, b.PL_NAME, c.ORG_NAME, a.PT_CODE 
						 from 		PER_POSITION a, PER_LINE b, PER_ORG c
						 where	a.POS_ID=$POS_ID and a.PL_CODE=b.PL_CODE and a.ORG_ID=c.ORG_ID ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$POS_NO = $data[POS_NO];
		$PL_NAME = trim($data[PL_NAME]);
		$ORG_NAME = trim($data[ORG_NAME]);
		$PT_CODE = trim($data[PT_CODE]);
		$cmd = "	select PT_NAME from PER_TYPE where PT_CODE='$PT_CODE'";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$PT_NAME = trim($data2[PT_NAME]);
		$PL_NAME = trim($PL_NAME)?($PL_NAME . $POSITION_LEVEL . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"")):" ".$LEVEL_NAME;
	}elseif($PER_TYPE==2){
		$cmd = " select 	b.PN_NAME, c.ORG_NAME 
						 from 		PER_POS_EMP a, PER_POS_NAME b, PER_ORG c
						 where	a.POEM_ID=$POEM_ID and a.PN_CODE=b.PN_CODE and a.ORG_ID=c.ORG_ID ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PL_NAME = trim($data[PN_NAME]);
		$ORG_NAME = trim($data[ORG_NAME]);
	}elseif($PER_TYPE==3){
		$cmd = " select 	b.EP_NAME, c.ORG_NAME 
						 from 		PER_POS_EMPSER a, PER_EMPSER_POS_NAME b, PER_ORG c
						 where	a.POEMS_ID=$POEMS_ID and a.EP_CODE=b.EP_CODE and a.ORG_ID=c.ORG_ID ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PL_NAME = trim($data[EP_NAME]);
		$ORG_NAME = trim($data[ORG_NAME]);
	} // end if

	$report_title = "ตารางแสดงแผนพัฒนาสมรรถนะของข้าราชการ $DEPARTMENT_NAME";
	$company_name = "ประจำรอบการประเมินครั้งที่ $KF_CYCLE พ.ศ. $KF_YEAR  $ORG_TITLE $ORG_NAME";
//	$company_name = $company_name + "\nชื่อวางแผนการประเมิน $PER_NAME  ชื่อตำแหน่งงาน $PL_NAME";
	$report_code = "R_Develope_Plan";

	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$orientation='P';

	$pdf = new PDF($orientation,$unit,$paper_size,$lang_code,$company_name,$report_title,$report_code,$heading,$heading_width,$heading_align);
	
 	$pdf->Open();
	$pdf->SetMargins(5,5,5);
	$pdf->AliasNbPages();
	$pdf->AddPage();
	$pdf->SetTextColor(0, 0, 0);
	$pdf->SetFont($font,'',14);
	$pdf->SetAutoPageBreak(true,10);

	$heading_width[0] = "10";
	$heading_width[1] = "40";
	$heading_width[2] = "10";
	$heading_width[3] = "70";
	$heading_width[4] = "70";
		
	function print_header(){
		global $pdf, $heading_width;
		
		$pdf->SetFont($font,'',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
		$pdf->Cell(100,2,"",0,1,'C');

		$pdf->Cell($heading_width[0] ,7,"ลำดับ",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"สมรรถนะ",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"ระดับ",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[3] ,7,"การพัฒนา",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[4] ,7,"อบรม",'LTBR',1,'C',1);
	} // function		

// Read Plan Guide List สำหรับ CP_CODE และ GUIDE_LEVEL ที่ต้องการ
	$arr_content = (array) null;

	$cmd = " SELECT * FROM PER_DEVELOPE_PLAN a, PER_DEVELOPE_GUIDE b 
					WHERE a.PD_GUIDE_ID = b.PD_GUIDE_ID AND PD_PLAN_KF_ID = $KF_ID 
					ORDER BY PD_GUIDE_COMPETENCE, PD_GUIDE_LEVEL";
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data_count=0;
	while($data = $db_dpis->get_array()){
		$CP_CODE = $data[PD_GUIDE_COMPETENCE];
		$GAP_LEVEL = $data[PD_GUIDE_LEVEL];
		$DESCRIPTION1 = $data[PD_GUIDE_DESCRIPTION1];
		$DESCRIPTION2 = $data[PD_GUIDE_DESCRIPTION2];
		$cmd = " select CP_NAME, CP_MODEL from PER_COMPETENCE where CP_CODE='$CP_CODE' ";
		$db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error(); 
		$data2 = $db_dpis2->get_array();
		$CP_NAME = $data2[CP_NAME];
		$CP_MODEL = $data2[CP_MODEL];
		$ST_CP_MODEL="";
		if($CP_MODEL==1) $ST_CP_MODEL = "สมรรถนะหลัก";
		elseif($CP_MODEL==2) $ST_CP_MODEL = "สมรรถนะผู้บริหาร";
		elseif($CP_MODEL==3) $ST_CP_MODEL = "สมรรถนะประจำสายงาน";
		$arr_content[$data_count][cp_name] = $CP_NAME;
		$arr_content[$data_count][level] = $GAP_LEVEL;
		$arr_content[$data_count][description1] = $DESCRIPTION1;
		$arr_content[$data_count][description2] = $DESCRIPTION2;
		$arr_content[$data_count][fanswer] = $fANSWER;
	//	echo "$GUIDE_ID,$data[PD_GUIDE_DESCRIPTION1],$data[PD_GUIDE_DESCRIPTION2],$fANSWER,$planStartDate,$planEndDate<br>";
		$data_count++;
	} // end while
// end Read Plan Guide List สำหรับ CP_CODE และ GUIDE_LEVEL ที่ต้องการ

	if($count_data){
		$pdf->AutoPageBreak = false;

		print_header();
	
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$CP_NAME = $arr_content[$data_count][cp_name];
			$GAP_LEVEL = $arr_content[$data_count][level];
			$DESCRIPTION1 = $arr_content[$data_count][description1];
			$DESCRIPTION2 = $arr_content[$data_count][description2];
			
			$seq_no++;
			$border = "";
			$pdf->SetFont($font,'',14);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

			$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
			$max_line_h = $pdf->y + 7;

			$pdf->Cell($heading_width[0], 7, $seq_no, $border, 0, 'C', 0);
			$pdf->MultiCell($heading_width[1], 7, $CP_NAME, $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1];
			if ($pdf->y > $max_line_h) $max_line_h = $pdf->y;
			$pdf->y = $start_y;
			$pdf->MultiCell($heading_width[2], 7, $GAP_LEVEL, $border, "C");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2];
			if ($pdf->y > $max_line_h) $max_line_h = $pdf->y;
			$pdf->y = $start_y;
			$pdf->MultiCell($heading_width[3], 7, $DESCRIPTION1, $border, "L");
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3];
			if ($pdf->y > $max_line_h) $max_line_h = $pdf->y;
			$pdf->y = $start_y;
			$pdf->MultiCell($heading_width[4], 7, $DESCRIPTION2, $border, "L");
			if ($pdf->y > $max_line_h) $max_line_h = $pdf->y;
			
			//================= Draw Border Line ====================
			$line_start_y = $start_y;		$line_start_x = $start_x;
			$line_end_y = $max_line_h;		$line_end_x = $start_x;
			$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				
			for($i=0; $i<=3; $i++){
				$line_start_y = $start_y;		$line_start_x += $heading_width[$i];
				$line_end_y = $max_line_h;		$line_end_x += $heading_width[$i];
				$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
			} // end for
			//====================================================

			if(($pdf->h - $max_line_h - 10) < 15){ 
				$pdf->Line($start_x, $max_line_h, $pdf->x, $max_line_h);
				if($data_count < $count_data){
					$pdf->AddPage();
					print_header();
					$max_line_h = $pdf->y;
				} // end if
			}else{
				$pdf->Line($start_x, $max_line_h, $pdf->x, $max_line_h);
			} // end if
			$pdf->x = $start_x;			$pdf->y = $max_line_h;
		} // end for
	}else{
		$pdf->SetFont($font,'b','',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(200,10,"********** ไม่มีข้อมูล **********",0,1,'C');
	} // end if

	$pdf->close();
	$pdf->Output();

	ini_set("max_execution_time", 30);
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"$report_code.xls\"");
	header("Content-Disposition: inline; filename=\"$report_code.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>