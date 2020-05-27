<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends.php");

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	ini_set("max_execution_time", $max_execution_time);
	
	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "";
	$report_title = trim($report_title);
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
	$pdf->SetAutoPageBreak(true,10);
	
	$heading_width[0] = "15";
	$heading_width[1] = "60";
	$heading_width[2] = "62";
	$heading_width[3] = "30";
	$heading_width[4] = "30";
	$heading_width[5] = "20";
	$heading_width[6] = "35";
		
	function print_header(){
		global $pdf, $heading_width;
		global $PER_TYPE, $SHOW_PRO_DATE, $POS_POEM_NO, $POS_POEM_NAME;
		
		$pdf->SetFont('angsab','',14);
		$pdf->Cell(($heading_width[0] + $heading_width[1]) ,7,"ประเภทบุคลากร : ".($PER_TYPE==1?"ข้าราชการ":($PER_TYPE==2?"ลูกจ้างประจำ":($PER_TYPE==3?"พนักงานราชการ":""))),'',0,'L',0);
		$pdf->Cell(($heading_width[2] + $heading_width[3]) ,7,"วันที่ประมวลผล : ".($SHOW_PRO_DATE?$SHOW_PRO_DATE:""),'',1,'L',0);
//		$pdf->Cell(($heading_width[0] + $heading_width[1]) ,7,"เลขที่ตำแหน่ง : ".($POS_POEM_NO?$POS_POEM_NO:""),'',0,'L',0);
//		$pdf->Cell(($heading_width[2] + $heading_width[3]) ,7,"ตำแหน่ง : ".($POS_POEM_NAME?$POS_POEM_NAME:""),'',1,'L',0);
		$pdf->Cell(100,2,"",0,1,'C');

		$pdf->SetFont('angsa','',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
//		$pdf->Cell(100,2,"",0,1,'C');

		$pdf->Cell($heading_width[0] ,7,"ลำดับที่",'LTR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"ชื่อ - สกุล",'LTR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"ประเภท",'LTR',0,'C',1);
		$pdf->Cell($heading_width[3] ,7,"ตั้งแต่วันที่",'LTR',0,'C',1);
		$pdf->Cell($heading_width[4] ,7,"ถึงวันที่",'LTR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,"จำนวนวัน",'LTR',0,'C',1);
		$pdf->Cell($heading_width[6] ,7,"การส่งใบลา",'LTR',0,'C',1);

		$pdf->Cell($heading_width[0] ,7,"",'LR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"",'LR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"",'LR',0,'C',1);
		$pdf->Cell($heading_width[3] ,7,"",'LR',0,'C',1);
		$pdf->Cell($heading_width[4] ,7,"ตำแหน่ง",'LR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,"",'LR',0,'C',1);
		$pdf->Cell($heading_width[6] ,7,"",'LR',0,'C',1);

		$pdf->Cell($heading_width[0] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[3] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[4] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[6] ,7,"",'LBR',0,'C',1);
	} // function		
		
//	$PER_TYPE = (trim($PER_TYPE))? $PER_TYPE : 1;	
	if ($PER_TYPE==1) {
		$search_from = ", PER_POSITION c";
		$arr_search_condition[] = "b.POS_ID=c.POS_ID";
	} elseif ($PER_TYPE==2) { 
		$search_from = ", PER_POS_EMP c";
		$arr_search_condition[] = "b.POEM_ID=c.POEM_ID";
	} elseif ($PER_TYPE==3) { 
		$search_from = ", PER_POS_EMPSER c";
		$arr_search_condition[] = "b.POEMS_ID=c.POEMS_ID"; 
	}
	
	if ($ABS_STARTDATE || $ABS_ENDDATE) {
		$temp_date = explode("/", $ABS_STARTDATE);
		$temp_start = ($temp_date[2] - 543) ."-". $temp_date[1] ."-". $temp_date[0];
		$temp_date = explode("/", $ABS_ENDDATE);
		$temp_end = ($temp_date[2] - 543) ."-". $temp_date[1] ."-". $temp_date[0];		
		$arr_search_condition[] = "(ABS_STARTDATE >= '$temp_start' and ABS_ENDDATE <= '$temp_end')";
	}

	if(trim($PV_CODE) && !trim($MINISTRY_ID) && !trim($DEPARTMENT_ID) && !trim($ORG_ID)){
		$cmd = " select 	ORG_ID
						 from   	PER_ORG
						 where  	OL_CODE='03' and PV_CODE='$PV_CODE'
						 order by ORG_ID
					  ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_org[] = $data[ORG_ID];
		$arr_search_condition[] = "(c.ORG_ID in (". implode(",", $arr_org) ."))";
	} // end if
	if(trim($MINISTRY_ID) && !trim($DEPARTMENT_ID) && !trim($ORG_ID)){
		$cmd = " select 	b.ORG_ID
						 from   	PER_ORG a, PER_ORG b
						 where  	a.OL_CODE='02' and b.OL_CODE='03' and a.ORG_ID_REF=$MINISTRY_ID and b.ORG_ID_REF=a.ORG_ID
						 order by a.ORG_ID, b.ORG_ID
					  ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_org[] = $data[ORG_ID];
		$arr_search_condition[] = "(c.ORG_ID in (". implode(",", $arr_org) ."))";
	} // end if	
	if(trim($DEPARTMENT_ID) && !trim($ORG_ID)){
		$cmd = " select ORG_ID from PER_ORG where OL_CODE='03' and ORG_ID_REF=$DEPARTMENT_ID ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_org[] = $data[ORG_ID];
		$arr_search_condition[] = "(c.ORG_ID in (". implode(",", $arr_org) ."))";
	} // end if
	if ($ORG_ID) $arr_search_condition[] = "(c.ORG_ID = $ORG_ID)";	
	
	$search_condition = "";
	if ($arr_search_condition)		$search_condition = " and " . implode(" and ", $arr_search_condition);

	if($DPISDB=="odbc"){
		$cmd = "	select			top $data_per_page 
											a.PER_ID, b.PN_CODE, PER_NAME, PER_SURNAME, AB_CODE, ABS_STARTDATE, ABS_ENDDATE, ABS_DAY, ABS_LETTER,
											b.POS_ID, b.POEM_ID, b.POEMS_ID, c.ORG_ID
						from				PER_ABSENT a, PER_PERSONAL b
											$search_from 
						where			b.PER_TYPE=$PER_TYPE and PER_STATUS=1 and a.PER_ID=b.PER_ID and ABS_LETTER in (1, 3) 
											$search_condition
						order by 		PER_NAME, PER_SURNAME 	";	
	}elseif($DPISDB=="oci8"){
		$cmd = "  select 		a.PER_ID, b.PN_CODE, PER_NAME, PER_SURNAME, AB_CODE, ABS_STARTDATE, ABS_ENDDATE, ABS_DAY, ABS_LETTER, 
										b.POS_ID, b.POEM_ID, b.POEMS_ID, c.ORG_ID 
					  from 			PER_ABSENT a, PER_PERSONAL b  
										$search_from
					  where 			b.PER_TYPE=$PER_TYPE and PER_STATUS=1 and a.PER_ID=b.PER_ID and ABS_LETTER in (1, 3) 
										$search_condition 
					  order by 		PER_NAME, PER_SURNAME ";
	}elseif($DPISDB=="mysql"){
		$cmd = "	select	a.PER_ID, b.PN_CODE, PER_NAME, PER_SURNAME, AB_CODE, ABS_STARTDATE, ABS_ENDDATE, ABS_DAY, ABS_LETTER,
											b.POS_ID, b.POEM_ID, b.POEMS_ID, c.ORG_ID
						from				PER_ABSENT a, PER_PERSONAL b
											$search_from 
						where			b.PER_TYPE=$PER_TYPE and PER_STATUS=1 and a.PER_ID=b.PER_ID and ABS_LETTER in (1, 3) 
											$search_condition
						order by 		PER_NAME, PER_SURNAME 	";	
	} // end if

		if($SESS_ORG_STRUCTURE==1) { 
			$cmd = str_replace("c.ORG_ID", "b.ORG_ID", $cmd);
			//$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		}
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();

	if($count_data){
		$pdf->AutoPageBreak = false;
		print_header();
		$data_count = $data_row = 0;
		while($data = $db_dpis->get_array()){
			$data_count++;
			$data_row++;			

		$TMP_PER_ID = $data[PER_ID];
		$current_list .= ((trim($current_list))?",":"") . $TMP_EDU_ID;
		$TMP_PN_CODE = $data[PN_CODE];
		$TMP_PER_NAME = $data[PER_NAME];
		$TMP_PER_SURNAME = $data[PER_SURNAME];
		//$temp_date = explode("-", trim($data[ABS_STARTDATE]));
		//$TMP_ABS_STARTDATE = substr($temp_date[2],0,2) ."/". $temp_date[1] ."/". ($temp_date[0] + 543);
		$TMP_ABS_STARTDATE = show_date_format(substr(trim($data[ABS_STARTDATE]), 0, 10),$DATE_DISPLAY);
		//$temp_date = explode("-", trim($data[ABS_ENDDATE]));
		//$TMP_ABS_ENDDATE = substr($temp_date[2],0,2) ."/". $temp_date[1] ."/". ($temp_date[0] + 543);		
		$TMP_ABS_ENDDATE = show_date_format(substr(trim($data[ABS_ENDDATE]), 0, 10),$DATE_DISPLAY);
		$TMP_ABS_DAY = trim($data[ABS_DAY]);
		$TMP_ABS_LETTER = trim($data[ABS_LETTER]);
		if ($TMP_ABS_LETTER == 1) 				$ABS_LETTER_STR = "ยังไม่ได้ส่ง";
		elseif ($TMP_ABS_LETTER == 3) 		$ABS_LETTER_STR = "ไม่ถูกต้อง";		

		$TMP_PN_CODE = trim($data[PN_CODE]);
		if($TMP_PN_CODE){
			$cmd = " select PN_NAME from PER_PRENAME where PN_CODE='$TMP_PN_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$TMP_PN_NAME = $data2[PN_NAME];
		} // end if
		
		$TMP_AB_CODE = trim($data[AB_CODE]);
		if($TMP_AB_CODE){
			$cmd = " select AB_NAME from PER_ABSENTTYPE where AB_CODE='$TMP_AB_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$TMP_AB_NAME = $data2[AB_NAME];
		} // end if
		

		$ORG_ID = $data[ORG_ID];
		$cmd = " 	select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID"; 
//		echo "$cmd<br>";
//		$db_dpis2->send_cmd($cmd);
//		$data2 = $db_dpis2->get_array();
//		echo "$ORG_ID - $data2[ORG_NAME]<br>";

			$border = "";
			$pdf->SetFont('angsa','',14);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

			$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;

			$pdf->Cell($heading_width[0], 7, $data_row, $border, 0, 'C', 0);
			$pdf->MultiCell($heading_width[1], 7, $PER_NAME, $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1];
			$pdf->y = $start_y;
			$pdf->MultiCell($heading_width[2], 7, $POSITION, $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2];
			$pdf->y = $start_y;
			$pdf->MultiCell($heading_width[3], 7, $ORG_NAME, $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3];
			$pdf->y = $start_y;
			$pdf->Cell($heading_width[4], 7, $POS_NO, $border, 0, 'C', 0);
			$pdf->Cell($heading_width[5], 7, $PER_SALARY, $border, 0, 'R', 0);
			$pdf->Cell($heading_width[6], 7, $POS_CHANGE_DATE, $border, 0, 'C', 0);
			$pdf->Cell($heading_width[7], 7, $POS_STARTDATE, $border, 0, 'L', 0);
			$pdf->Cell($heading_width[8], 7, $PER_STARTDATE, $border, 0, 'L', 0);

			//================= Draw Border Line ====================
			$line_start_y = $start_y;		$line_start_x = $start_x;
			$line_end_y = $max_y;		$line_end_x = $start_x;
			$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				
			for($i=0; $i<=8; $i++){
				$line_start_y = $start_y;		$line_start_x += $heading_width[$i];
				$line_end_y = $max_y;		$line_end_x += $heading_width[$i];
				$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
			} // end for
			//====================================================

			if(($pdf->h - $max_y - 10) < 15){ 
				$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
				if($data_count < $count_data){
					$pdf->AddPage();
					print_header();
					$max_y = $pdf->y;
				} // end if
			}else{
				$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
			} // end if
			$pdf->x = $start_x;			$pdf->y = $max_y;
		} // end while
	}else{
		$pdf->SetFont('angsab','',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(287,10,"********** ไม่มีข้อมูล **********",0,1,'C');
	} // end if

	$pdf->close();
	$pdf->Output();

	ini_set("max_execution_time", 30);
?>