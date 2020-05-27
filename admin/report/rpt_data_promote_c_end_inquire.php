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
	
	if (trim($PRO_YEAR))	 {
		$tmp_pro_year = $PRO_YEAR - 543;
		$tmp_pro_year_start = ($PRO_YEAR - 3) - 543;	
		$tmp_pro_year_end = ($PRO_YEAR - 2) - 543;
	}

	if ($DPISDB=="odbc") {
		$where_effectivedate = " LEFT(POH_EFFECTIVEDATE,10) >= '$tmp_pro_year_start-10-01' and LEFT(POH_EFFECTIVEDATE,10) <= '$tmp_pro_year_end-09-30' and 	";
	} elseif ($DPISDB=="oci8") {
		$where_effectivedate = " SUBSTR(POH_EFFECTIVEDATE,1,10) >= '$tmp_pro_year_start-10-01' and SUBSTR(POH_EFFECTIVEDATE,1,10) <= '$tmp_pro_year_end-09-30' and ";
	}elseif($DPISDB=="mysql"){
		$where_effectivedate = " LEFT(POH_EFFECTIVEDATE,10) >= '$tmp_pro_year_start-10-01' and LEFT(POH_EFFECTIVEDATE,10) <= '$tmp_pro_year_end-09-30' and 	";
	}
	if(count($arr_search_condition)) 	$search_condition 	= implode(" and ", $arr_search_condition);	

	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "";
	$report_title = "รายชื่อข้าราชการได้เลื่อนระดับควบปลาย ประจำปีงบประมาณ $PRO_YEAR";
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
	
	$heading_width[0] = "12";
	$heading_width[1] = "50";
	$heading_width[2] = "45";
	$heading_width[3] = "45";
	$heading_width[4] = "45";
	$heading_width[5] = "25";
	$heading_width[6] = "20";
	$heading_width[7] = "45";
	
	function print_header(){
		global $pdf, $heading_width;
		
		$pdf->SetFont($font,'',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
		$pdf->Cell(100,2,"",0,1,'C');

		$pdf->Cell($heading_width[0] ,7,"ลำดับที่",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"ชื่อ-สกุล",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"$ORG_TITLE",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[3] ,7,"$ORG_TITLE1",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[4] ,7,"ตำแหน่งเดิม",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,"วันที่เข้าสู่ระดับ",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[6] ,7,"ขั้นเงินเดือน",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[7] ,7,"ตำแหน่งใหม่",'LTBR',1,'C',1);
	} // function		
		
	if($DPISDB=="odbc"){
		$cmd = " select			b.PER_ID, b.PN_CODE, PER_NAME, PER_SURNAME, a.LEVEL_NO as LEVEL_FILL, b.LEVEL_NO as LEVEL_NOW, b.POS_ID, 
											c.PL_CODE, b.PER_SALARY, c.ORG_ID, c.ORG_ID_1, 
											min(POH_EFFECTIVEDATE) as EFFECTIVEDATE, b.PER_CARDNO, c.PT_CODE 
						 from			PER_POSITIONHIS a, PER_PERSONAL b, PER_POSITION c  
						 where		a.MOV_CODE in ('101', '10110', '10120', '10130', '10140', '105', '10510', '10520')  and 
											((a.LEVEL_NO='01' and b.LEVEL_NO='02') or (a.LEVEL_NO='02' and b.LEVEL_NO='03') or (a.LEVEL_NO='03' and b.LEVEL_NO='04') or (a.LEVEL_NO='04' and b.LEVEL_NO='04')) 
											and LEFT(POH_EFFECTIVEDATE,10) >= '$tmp_pro_year_start-10-01' 
											and LEFT(POH_EFFECTIVEDATE,10) <= '$tmp_pro_year_end-09-30' 
											and PER_TYPE=1 and PER_STATUS=1 and a.PER_ID=b.PER_ID and b.POS_ID=c.POS_ID and b.DEPARTMENT_ID=$DEPARTMENT_ID 
						 group by 	b.PER_ID, b.PN_CODE, PER_NAME, PER_SURNAME, a.LEVEL_NO, b.LEVEL_NO, b.POS_ID, 
											c.PL_CODE, b.PER_SALARY, c.ORG_ID, c.ORG_ID_1, b.PER_CARDNO, c.PT_CODE  
						 order by  	a.LEVEL_NO, b.PER_SALARY, PER_NAME, PER_SURNAME  ";
	}elseif($DPISDB=="oci8"){
		$cmd = " select 		b.PER_ID, b.PN_CODE, PER_NAME, PER_SURNAME, a.LEVEL_NO as LEVEL_FILL, b.LEVEL_NO as LEVEL_NOW, b.POS_ID, 
											c.PL_CODE, b.PER_SALARY, c.ORG_ID, c.ORG_ID_1,
											min(POH_EFFECTIVEDATE) as EFFECTIVEDATE, b.PER_CARDNO, c.PT_CODE
						 from			PER_POSITIONHIS a, PER_PERSONAL b, PER_POSITION c  
						 where		a.MOV_CODE in ('101', '10110', '10120', '10130', '10140', '105', '10510', '10520')  
											and ((a.LEVEL_NO='01' and b.LEVEL_NO='02') or (a.LEVEL_NO='02' and b.LEVEL_NO='03') or (a.LEVEL_NO='03' and b.LEVEL_NO='04') or (a.LEVEL_NO='04' and b.LEVEL_NO='04')) 
											and SUBSTR(POH_EFFECTIVEDATE,1,10) >= '$tmp_pro_year_start-10-01' 
											and SUBSTR(POH_EFFECTIVEDATE,1,10) <= '$tmp_pro_year_end-09-30' 
											and PER_TYPE=1 and PER_STATUS=1 and a.PER_ID=b.PER_ID and b.POS_ID=c.POS_ID and b.DEPARTMENT_ID=$DEPARTMENT_ID 
						 group by 	b.PER_ID, b.PN_CODE, PER_NAME, PER_SURNAME, a.LEVEL_NO, b.LEVEL_NO, b.POS_ID, 
											c.PL_CODE, b.PER_SALARY, c.ORG_ID, c.ORG_ID_1, b.PER_CARDNO, c.PT_CODE
						 order by  	a.LEVEL_NO, b.PER_SALARY, PER_NAME, PER_SURNAME
					  ";						
	}elseif($DPISDB=="mysql"){
		$cmd = " select			b.PER_ID, b.PN_CODE, PER_NAME, PER_SURNAME, a.LEVEL_NO as LEVEL_FILL, b.LEVEL_NO as LEVEL_NOW, b.POS_ID, 
											c.PL_CODE, b.PER_SALARY, c.ORG_ID, c.ORG_ID_1, 
											min(POH_EFFECTIVEDATE) as EFFECTIVEDATE, b.PER_CARDNO, c.PT_CODE 
						 from			PER_POSITIONHIS a, PER_PERSONAL b, PER_POSITION c  
						 where		a.MOV_CODE in ('101', '10110', '10120', '10130', '10140', '105', '10510', '10520')  and 
											((a.LEVEL_NO='01' and b.LEVEL_NO='02') or (a.LEVEL_NO='02' and b.LEVEL_NO='03') or (a.LEVEL_NO='03' and b.LEVEL_NO='04') or (a.LEVEL_NO='04' and b.LEVEL_NO='04')) 
											and LEFT(POH_EFFECTIVEDATE,10) >= '$tmp_pro_year_start-10-01' 
											and LEFT(POH_EFFECTIVEDATE,10) <= '$tmp_pro_year_end-09-30' 
											and PER_TYPE=1 and PER_STATUS=1 and a.PER_ID=b.PER_ID and b.POS_ID=c.POS_ID and b.DEPARTMENT_ID=$DEPARTMENT_ID 
						 group by 	b.PER_ID, b.PN_CODE, PER_NAME, PER_SURNAME, a.LEVEL_NO, b.LEVEL_NO, b.POS_ID, 
											c.PL_CODE, b.PER_SALARY, c.ORG_ID, c.ORG_ID_1, b.PER_CARDNO, c.PT_CODE  
						 order by  	a.LEVEL_NO, b.PER_SALARY, PER_NAME, PER_SURNAME  ";
	} // end if
	if($SESS_ORG_STRUCTURE==1) { 
			$cmd = str_replace("c.ORG_ID", "b.ORG_ID", $cmd);
					}
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data_count = $data_row = 0;
	while($data = $db_dpis->get_array()){
		$data_row++;

		$TMP_PER_ID = $data[PER_ID];
		$TMP_PER_NAME = trim($data[PER_NAME]) . " " . trim($data[PER_SURNAME]);
		$POS_DATE = trim($data[POS_DATE]);
		$PER_SALARY = $data[PER_SALARY];
		$PER_TYPE = trim($data[PER_TYPE]);
		$LEVEL_FILL = trim($data[LEVEL_FILL]);
		$LEVEL_NOW = trim($data[LEVEL_NOW]);
		$POH_EFFECTIVEDATE = show_date_format($data[EFFECTIVEDATE],$DATE_DISPLAY);
		$PER_CARDNO = trim($data[PER_CARDNO]);

		$PN_CODE = trim($data[PN_CODE]);
		$cmd = " select PN_NAME from PER_PRENAME where PN_CODE='$PN_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$PN_NAME = trim($data2[PN_NAME]);

		$PL_CODE = trim($data[PL_CODE]);
		$cmd = " select PL_NAME from PER_LINE where PL_CODE='$PL_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$PL_NAME = trim($data2[PL_NAME]);
		
		$PT_CODE = trim($data[PT_CODE]);
		$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE)='$PT_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$PT_NAME = trim($data2[PT_NAME]);

		$OLD_POSITION = trim($PL_NAME)?($PL_NAME ." ". level_no_format($LEVEL_NOW) . (($PT_NAME != "ทั่วไป" && $LEVEL_NOW >= 6)?"$PT_NAME":"")):"ระดับ $LEVEL_NOW";
		$NEW_POSITION = trim($PL_NAME)?($PL_NAME ." ". level_no_format($LEVEL_NOW + 1) . (($PT_NAME != "ทั่วไป" && ($LEVEL_NOW + 1) >= 6)?"$PT_NAME":"")):"ระดับ ".($LEVEL_NOW + 1);
		
		$POS_ID = trim($data[POS_ID]);
		$cmd = " 	select ORG_ID, ORG_ID_1 from PER_POSITION where POS_ID=$POS_ID ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$ORG_ID = trim($data2[ORG_ID]);
		$ORG_ID_1 = trim($data2[ORG_ID_1]);
		if ($ORG_ID || $ORG_ID_1) {
			$ORG_NAME = $ORG_NAME_1; 
			$cmd = " select ORG_ID, ORG_NAME from PER_ORG where ORG_ID in ($ORG_ID, $ORG_ID_1) ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			while ($data2 = $db_dpis2->get_array() ) {
				$ORG_NAME 	= ($ORG_ID == trim($data2[ORG_ID]))?		trim($data2[ORG_NAME]) : $ORG_NAME;
				$ORG_NAME_1 	= ($ORG_ID_1 == trim($data2[ORG_ID]))?		trim($data2[ORG_NAME]) : $ORG_NAME_1;
			}
		}		
		
		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][order] = $data_row;
		$arr_content[$data_count][per_name] = "$PN_NAME$TMP_PER_NAME";
		$arr_content[$data_count][org_name] = $ORG_NAME;
		$arr_content[$data_count][org_name_1] = $ORG_NAME_1;
		$arr_content[$data_count][old_position] = ($OLD_POSITION)? "$OLD_POSITION\n(ระดับที่บรรจุ ".level_no_format($LEVEL_FILL).")" : "";
		$arr_content[$data_count][poh_effectivedate] = $POH_EFFECTIVEDATE;
		$arr_content[$data_count][per_salary] = $PER_SALARY;
		$arr_content[$data_count][new_position] = $NEW_POSITION;
						
		$data_count++;
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
	if($count_data){
		$pdf->AutoPageBreak = false;
		print_header();

		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$ORDER = $arr_content[$data_count][order];
			$PER_NAME = $arr_content[$data_count][per_name];
			$ORG_NAME = $arr_content[$data_count][org_name];
			$ORG_NAME_1 = $arr_content[$data_count][org_name_1];
			$OLD_POSITION = $arr_content[$data_count][old_position];
			$POH_EFFECTIVEDATE = $arr_content[$data_count][poh_effectivedate];
			$PER_SALARY = number_format($arr_content[$data_count][per_salary]);
			$NEW_POSITION = $arr_content[$data_count][new_position];
		
			$border = "";
			$pdf->SetFont($font,'',14);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

			$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
			
			$pdf->Cell($heading_width[0], 7, "$ORDER", $border, 0, 'C', 0);
			$pdf->MultiCell($heading_width[1], 7, "$PER_NAME", $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1];
			$pdf->y = $start_y;
			$pdf->MultiCell($heading_width[2], 7, "$ORG_NAME", $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2];
			$pdf->y = $start_y;
			$pdf->MultiCell($heading_width[3], 7, "$ORG_NAME_1", $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3];
			$pdf->y = $start_y;
			$pdf->MultiCell($heading_width[4], 7, "$OLD_POSITION", $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4];
			$pdf->y = $start_y;
			$pdf->Cell($heading_width[5], 7, "$POH_EFFECTIVEDATE", $border, 0, 'C', 0);
			$pdf->Cell($heading_width[6], 7, "$PER_SALARY", $border, 0, 'R', 0);
			$pdf->MultiCell($heading_width[7], 7, "$NEW_POSITION", $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6] + $heading_width[7];
			$pdf->y = $start_y;

			//================= Draw Border Line ====================
			$line_start_y = $start_y;		$line_start_x = $start_x;
			$line_end_y = $max_y;		$line_end_x = $start_x;
			$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				
			for($i=0; $i<=7; $i++){
				$line_start_y = $start_y;		$line_start_x += $heading_width[$i];
				$line_end_y = $max_y;		$line_end_x += $heading_width[$i];
				$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
			} // end for
			//====================================================

			if(($pdf->h - $max_y - 10) < 22){ 
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

	$pdf->close();
	$pdf->Output();

	ini_set("max_execution_time", 30);
?>