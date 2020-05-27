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

	$search_condition = "";
	if ($arr_search_condition)		$search_condition = " and " . implode(" and ", $arr_search_condition);

	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "";
	$report_title = "$DEPARTMENT_NAME||รายงานการจัดทำเงินรางวัลประจำปี";
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
	$pdf->SetFont($font,'',14);
	$pdf->SetAutoPageBreak(true,10);
	
	$heading_width[0] = "15";
	$heading_width[1] = "80";
	$heading_width[2] = "95";
	$heading_width[3] = "30";
	$heading_width[4] = "37";
	$heading_width[5] = "30";
	
	function print_header(){
		global $pdf, $heading_width;
		
		$pdf->SetFont($font,'',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
		$pdf->Cell(100,2,"",0,1,'C');

		$pdf->Cell($heading_width[0] ,7,"ลำดับที่",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"ชื่อ - สกุล",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7, "ตำแหน่ง/ระดับ",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[3] ,7, "เงินเดือน",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[4] ,7, "เงินรางวัลประจำปี (%)",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7, "จำนวนเงิน (บาท)",'LTBR',1,'C',1);
	} // function		

	$TMP_ORG_ID = (trim($ORG_ID))? $ORG_ID : $ORG_ID_ASS; 
	if ($BONUS_TYPE == 1) {					
		$table = ", PER_POSITION d ";
		$where = " and d.ORG_ID=$TMP_ORG_ID and b.POS_ID=d.POS_ID ";
	} elseif ($BONUS_TYPE == 2) {			
		$table = ", PER_POS_EMP d ";
		$where = " and d.ORG_ID=$TMP_ORG_ID and b.POEM_ID=d.POEM_ID ";
	} elseif ($BONUS_TYPE == 3) {			
		$table = ", PER_POS_EMPSER d ";	
		$where = " and d.ORG_ID=$TMP_ORG_ID and b.POEMS_ID=d.POEM_ID ";
	} elseif ($BONUS_TYPE == 4) {			
		$table = ", PER_POS_TEMP d ";	
		$where = " and d.ORG_ID=$TMP_ORG_ID and b.POT_ID=d.POT_ID ";
	}

	if($DPISDB=="odbc"){
		$cmd = " select			a.PER_ID, BONUS_PERCENT, BONUS_QTY, PER_NAME, PER_SURNAME, PN_NAME, PER_SALARY, b.LEVEL_NO, e.POSITION_LEVEL, b.POS_ID, b.POEM_ID, b.POEMS_ID, b.POT_ID
						 from			PER_BONUSPROMOTE a, PER_PERSONAL b, PER_PRENAME c, PER_LEVEL e  $table 
						 where		BONUS_YEAR='$BONUS_YEAR' and BONUS_TYPE=$BONUS_TYPE and 
											a.DEPARTMENT_ID=$DEPARTMENT_ID and 								
											a.PER_ID=b.PER_ID and b.PN_CODE=c.PN_CODE  and b.LEVEL_NO=e.LEVEL_NO
											$where 
						 order by 	PER_NAME, PER_SURNAME ";	
	}elseif($DPISDB=="oci8"){
		$cmd = " select 		a.PER_ID, BONUS_PERCENT, BONUS_QTY, PER_NAME, PER_SURNAME, PN_NAME, PER_SALARY, b.LEVEL_NO, e.POSITION_LEVEL,
											b.POS_ID, b.POEM_ID, b.POEMS_ID, b.POT_ID
						 from 			PER_BONUSPROMOTE a, PER_PERSONAL b, PER_PRENAME c, PER_LEVEL e  $table 
						 where		BONUS_YEAR='$BONUS_YEAR' and BONUS_TYPE=$BONUS_TYPE and 
											a.DEPARTMENT_ID=$DEPARTMENT_ID and 
										  	a.PER_ID=b.PER_ID and b.PN_CODE=c.PN_CODE  and b.LEVEL_NO=e.LEVEL_NO
										  	$where 
						 order by 	PER_NAME, PER_SURNAME ";	
	}elseif($DPISDB=="mysql"){
		$cmd = " select			a.PER_ID, BONUS_PERCENT, BONUS_QTY, PER_NAME, PER_SURNAME, PN_NAME, PER_SALARY, b.LEVEL_NO, e.POSITION_LEVEL, b.POS_ID, b.POEM_ID, b.POEMS_ID, POT_ID
						 from			PER_BONUSPROMOTE a, PER_PERSONAL b, PER_PRENAME c, PER_LEVEL e  $table 
						 where		BONUS_YEAR='$BONUS_YEAR' and BONUS_TYPE=$BONUS_TYPE and 
											a.DEPARTMENT_ID=$DEPARTMENT_ID and 								
											a.PER_ID=b.PER_ID and b.PN_CODE=c.PN_CODE  and b.LEVEL_NO=e.LEVEL_NO
											$where 
						 order by 	PER_NAME, PER_SURNAME ";
	} // end if
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
//	echo $cmd;
	$data_count = $data_row = 0;
	while($data = $db_dpis->get_array()){
		$data_row++;

		$TMP_PER_ID = $data[PER_ID];
		$TMP_BONUS_PERCENT = $data[BONUS_PERCENT];
		$TMP_BONUS_QTY = number_format($data[BONUS_QTY], 2, '.', ',');
		$TMP_PER_SALARY = number_format($data[PER_SALARY], 2, '.', ',') ;
		$tmp_salary = $data[PER_SALARY];
		$TMP_PER_NAME = $data[PN_NAME] . $data[PER_NAME] . " " . $data[PER_SURNAME];
		$TMP_LEVEL_NO = trim($data[LEVEL_NO]);
		$TMP_LEVEL_NAME = trim($data[POSITION_LEVEL]);
		
		$TMP_POS_ID = $data[POS_ID];
		$TMP_POEM_ID = $data[POEMS_ID];
		$TMP_POEMS_ID = $data[POEM_ID];	
		$TMP_POT_ID = $data[POT_ID];	
		if($TMP_POS_ID){
			$cmd = " select PL_NAME, a.PT_CODE from PER_POSITION a, PER_LINE b where POS_ID=$TMP_POS_ID and a.PL_CODE=b.PL_CODE ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$TMP_PL_NAME = trim($data2[PL_NAME]);
			$TMP_PT_CODE = trim($data2[PT_CODE]);
			$TMP_PT_NAME = trim($data2[PT_NAME]);
			$POSITION = trim($TMP_PL_NAME)?($TMP_PL_NAME . $TMP_LEVEL_NAME . (($TMP_PT_NAME != "ทั่วไป" && $TMP_LEVEL_NO >= 6)?"$TMP_PT_NAME":"")):"$TMP_LEVEL_NAME";
		} // end if
		if($TMP_POEM_ID){
			$cmd = " select PN_NAME from PER_POS_EMP a, PER_POS_NAME b where a.POEM_ID=$TMP_POEM_ID and a.PN_CODE=b.PN_CODE ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$POSITION = $data2[PN_NAME];
		} // end if
		if($TMP_POEMS_ID){
			$cmd = " select EP_NAME from PER_POS_EMPSER a, PER_EMPSER_POS_NAME where a.POEMS_ID=$TMP_POEMS_ID and a.POEMS_ID=b.POEMS_ID ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$POSITION = $data2[EP_NAME];
		} // end if
		if($TMP_POT_ID){
			$cmd = " select TP_NAME from PER_POS_TEMP a, PER_TEMP_POS_NAME where a.POT_ID=$TMP_POT_ID and a.POT_ID=b.POT_ID ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$POSITION = $data2[TP_NAME];
		} // end if
		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][order] = $data_row;
		$arr_content[$data_count][per_name] = $TMP_PER_NAME;
		$arr_content[$data_count][per_position] = $POSITION;
		$arr_content[$data_count][per_salary] = $TMP_PER_SALARY;
		$arr_content[$data_count][bonus_percent] = $TMP_BONUS_PERCENT;
		$arr_content[$data_count][bonus_qty] = $TMP_BONUS_QTY;
		
		$data_count++;
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";

	$border = "";
	$pdf->SetFont($font,'',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

	$pdf->Cell(27, 7, "", $border, 0, 'L', 0);
	$pdf->Cell(25, 7, "ปีงบประมาณ ", $border, 0, 'L', 0);
	$pdf->Cell(95, 7, "$BONUS_YEAR", $border, 0, 'L', 0);
	$pdf->Cell(25, 7, "กระทรวง ", $border, 0, 'L', 0);
	$pdf->Cell(95, 7, "$MINISTRY_NAME", $border, 0, 'L', 0);
	$pdf->Cell(20, 7, "", $border, 1, 'L', 0);
		
	$pdf->Cell(27, 7, "", $border, 0, 'L', 0);
	$pdf->Cell(25, 7, "กรม ", $border, 0, 'L', 0);
	$pdf->Cell(95, 7, "$DEPARTMENT_NAME", $border, 0, 'L', 0);
	$pdf->Cell(25, 7, "สำนัก/กอง ", $border, 0, 'L', 0);
	$pdf->Cell(95, 7, "$ORG_NAME", $border, 0, 'L', 0);
	$pdf->Cell(20, 7, "", $border, 1, 'L', 0);

	$pdf->Cell(27, 7, "", $border, 0, 'L', 0);
	$pdf->Cell(25, 7, "เงินที่จัดสรร ", $border, 0, 'L', 0);
	$pdf->Cell(95, 7, "$BONUSQ_QTY", $border, 0, 'L', 0);
	$pdf->Cell(25, 7, "รวมยอด ", $border, 0, 'L', 0);
	$pdf->Cell(95, 7, "$SUM_BONUSQ_QTY", $border, 0, 'L', 0);
	$pdf->Cell(20, 7, "", $border, 1, 'L', 0);

	$pdf->Cell(27, 7, "", $border, 0, 'L', 0);
	$pdf->Cell(25, 7, "คงเหลือ ", $border, 0, 'L', 0);
	$pdf->Cell(95, 7, "$REST_BONUSQ_QTY", $border, 0, 'L', 0);
	$pdf->Cell(40, 7, "% จัดทำเงินรางวัลประจำปี ", $border, 0, 'L', 0);
	$pdf->Cell(80, 7, "$BONUS_PERCENT_ALL", $border, 0, 'L', 0);
	$pdf->Cell(20, 7, "", $border, 1, 'L', 0);

	if($count_data){
		$pdf->AutoPageBreak = false;
		
		print_header();

		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$ORDER = $arr_content[$data_count][order];
			$PER_NAME = $arr_content[$data_count][per_name];
			$PER_POSITION = $arr_content[$data_count][per_position];
			$PER_SALARY = $arr_content[$data_count][per_salary];
			$BONUS_PERCENT = $arr_content[$data_count][bonus_percent];
			$BONUS_QTY = $arr_content[$data_count][bonus_qty];

			$border = "";
			$pdf->SetFont($font,'',14);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

			$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
			
			$pdf->Cell($heading_width[0], 7, "$ORDER", $border, 0, 'C', 0);
			$pdf->MultiCell($heading_width[1], 7, "$PER_NAME", $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1];
			$pdf->y = $start_y;
			$pdf->MultiCell($heading_width[2], 7, "$PER_POSITION", $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2];
			$pdf->y = $start_y;
			$pdf->Cell($heading_width[3], 7, "$PER_SALARY", $border, 0, 'R', 0);
			$pdf->Cell($heading_width[4], 7, "$BONUS_PERCENT", $border, 0, 'R', 0);
			$pdf->Cell($heading_width[5], 7, "$BONUS_QTY", $border, 0, 'R', 0);

			//================= Draw Border Line ====================
			$line_start_y = $start_y;		$line_start_x = $start_x;
			$line_end_y = $max_y;		$line_end_x = $start_x;
			$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				
			for($i=0; $i<=5; $i++){
				$line_start_y = $start_y;		$line_start_x += $heading_width[$i];
				$line_end_y = $max_y;		$line_end_x += $heading_width[$i];
				$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
			} // end for
			//====================================================

			if(($pdf->h - $max_y - 10) < 15){ 
				$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
				if($data_count < (count($arr_content) - 1)){
					$pdf->AddPage();
					print_header();
					$max_y = $pdf->y;
				} // end if
			}else{
				if($data_count == (count($arr_content) - 1)) $pdf->Line($start_x, $max_y, $pdf->x, $max_y);
			} // end if
			
			$pdf->x = $start_x;			$pdf->y = $max_y;
		} // end for	
	}else{
		$pdf->SetFont($font,'b','',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(287,10,"********** ไม่มีข้อมูล **********",0,1,'C');
	} // end if

	$pdf->Close();
	$pdf->Output();

	ini_set("max_execution_time", 30);
?>