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

	if($DPISDB=="odbc"){	
		$cmd = " select 	a.PER_ID, a.POS_ID, c.POS_NO, d.POEM_NO as EMP_POS_NO, e.POEMS_NO as EMPS_POS_NO, f.POT_NO as EMPTEMP_POS_NO,
										b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_TYPE, 
										a.PER_CARDNO, a.PER_BIRTHDATE, c.ORG_ID, d.ORG_ID as EMP_ORG_ID, e.ORG_ID as EMPS_ORG_ID, f.ORG_ID as EMPTEMP_ORG_ID,
										c.PL_CODE, d.PN_CODE, e.EP_CODE,f.TP_CODE, a.PER_SALARY, a.LEVEL_NO
						 from 		PER_PRENAME b
										inner join (
										(	
											(
												(
												PER_PERSONAL a
												left join PER_POSITION c on a.POS_ID = c.POS_ID
											) 	left join PER_POS_EMP d on a.POEM_ID = d.POEM_ID
											) 	left join PER_POS_EMPSER e on a.POEMS_ID = e.POEMS_ID 
										 ) 	left join PER_POS_TEMP f on a.POT_ID = f.POT_ID
										) on a.PN_CODE = b.PN_CODE
						where		a.PER_ID = $PER_ID ";
	}elseif($DPISDB=="oci8"){
		$cmd = "	select 		a.PER_ID, a.POS_ID, c.POS_NO, d.POEM_NO as EMP_POS_NO, e.POEMS_NO as EMPS_POS_NO, f.POT_NO as EMPTEMP_POS_NO,
											b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_TYPE, 
											a.PER_CARDNO, a.PER_BIRTHDATE, c.ORG_ID, d.ORG_ID as EMP_ORG_ID, e.ORG_ID as EMPS_ORG_ID, f.ORG_ID as EMPTEMP_ORG_ID,
											c.PL_CODE, d.PN_CODE, e.EP_CODE,f.TP_CODE, a.PER_SALARY, a.LEVEL_NO 
							  from 		PER_PERSONAL a, PER_PRENAME b, PER_POSITION c, PER_POS_EMP d, PER_POS_EMPSER e,PER_POS_TEMP f
							  where 	a.PN_CODE=b.PN_CODE and a.POS_ID=c.POS_ID(+) and a.POEM_ID=d.POEM_ID(+) and a.POEMS_ID=e.POEMS_ID(+) and a.POT_ID = f.POT_ID(+)
							  				and a.PER_ID=$PER_ID ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select 	a.PER_ID, a.POS_ID, c.POS_NO, d.POEM_NO as EMP_POS_NO, e.POEMS_NO as EMPS_POS_NO, f.POT_NO as EMPTEMP_POS_NO,
										b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_TYPE, 
										a.PER_CARDNO, a.PER_BIRTHDATE, c.ORG_ID, d.ORG_ID as EMP_ORG_ID, e.ORG_ID as EMPS_ORG_ID, f.ORG_ID as EMPTEMP_ORG_ID,
										c.PL_CODE, d.PN_CODE, e.EP_CODE,f.TP_CODE, a.PER_SALARY, a.LEVEL_NO
						 from 		PER_PERSONAL a
						 				inner join PER_PRENAME b on a.PN_CODE = b.PN_CODE
										left join PER_POSITION c on a.POS_ID = c.POS_ID
										left join PER_POS_EMP d on a.POEM_ID = d.POEM_ID
										left join PER_POS_EMPSER e on a.POEMS_ID = e.POEMS_ID 
										left join PER_POS_TEMP f on a.POT_ID = f.POT_ID
						where		a.PER_ID = $PER_ID ";
	} // end if
	if($SESS_ORG_STRUCTURE==1) { 
			$cmd = str_replace("c.ORG_ID", "a.ORG_ID", $cmd);
			$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		}
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
//$db_dpis->show_error();

	$PER_NAME = $data[PN_NAME] . $data[PER_NAME] . " " . $data[PER_SURNAME];				
	$PER_TYPE = $data[PER_TYPE];
	if($PER_TYPE==1) $ORG_ID = $data[ORG_ID];
	elseif($PER_TYPE==2) $ORG_ID = $data[EMP_ORG_ID];
	elseif ($PER_TYPE == 3) $ORG_ID = $data[EMPS_ORG_ID];
	elseif ($PER_TYPE == 4) $ORG_ID = $data[EMPTEMP_ORG_ID];

	$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID ";
	if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	$db_dpis2->send_cmd($cmd);
	$data2 = $db_dpis2->get_array();
	$ORG_NAME = $data2[ORG_NAME];

	$cmd = " select ORG_ID_REF from PER_ORG where OL_CODE='03' and ORG_ID=$ORG_ID ";
	if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$DEPARTMENT_ID = $data[ORG_ID_REF];

	$cmd = " select ORG_NAME, ORG_ID_REF from PER_ORG where OL_CODE='02' and ORG_ID=$DEPARTMENT_ID ";
	if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$DEPARTMENT_NAME = $data[ORG_NAME];
	$MINISTRY_ID = $data[ORG_ID_REF];

	$cmd = " select ORG_NAME from PER_ORG where OL_CODE='01' and ORG_ID=$MINISTRY_ID ";
	if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$MINISTRY_NAME = $data[ORG_NAME];

	$search_ab_code = trim($search_ab_code);
	$search_abs_startdate = trim($search_abs_startdate);
	$search_abs_enddate = trim($search_abs_enddate);		
	if ($search_ab_code)
		$arr_search_condition[] = "(AB_CODE = '$search_ab_code')";
	
	if ($search_abs_startdate || $search_abs_enddate) {
		$temp_start =  save_date($search_abs_startdate);
		$temp_end =  save_date($search_abs_enddate);
		$arr_search_condition[] = "(ABS_STARTDATE >= '$temp_start' and ABS_ENDDATE <= '$temp_end')";
	}

	if(trim($search_abs_month)){
		if($DPISDB=="odbc") $arr_search_condition[] = "(MID(ABS_STARTDATE, 6, 2)='". str_pad($search_abs_month, 2, "0", STR_PAD_LEFT) ."')";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(ABS_STARTDATE, 6, 2)='". str_pad($search_abs_month, 2, "0", STR_PAD_LEFT) ."')";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(SUBSTRING(ABS_STARTDATE, 6, 2)='". str_pad($search_abs_month, 2, "0", STR_PAD_LEFT) ."')";
	} // end if

	if(trim($search_abs_year)){
		if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(ABS_STARTDATE, 4)='". ($search_abs_year - 543) ."')";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(ABS_STARTDATE, 1, 4)='". ($search_abs_year - 543) ."')";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(ABS_STARTDATE, 4)='". ($search_abs_year - 543) ."')";
	} // end if

	$search_condition = "";
	if ($arr_search_condition)		$search_condition = " and " . implode(" and ", $arr_search_condition);

	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "";
	$report_title = "$DEPARTMENT_NAME||รายงานการลา/สาย/ขาด";
	$report_code = "P0601";
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
	$heading_width[1] = "100";
	$heading_width[2] = "20";
	$heading_width[3] = "25";
	$heading_width[4] = "25";
	$heading_width[5] = "60";
	
		//new format*****************************************
	$heading_text[0] = "ลำดับ";
	$heading_text[1] = "ประเภท";
	$heading_text[2] = "ครั้งที่";
	$heading_text[3] = "ตั้งแต่วันที่";
	$heading_text[4] = "ถึงวันที่";
	$heading_text[5] = "การส่งใบลา";
	
	$heading_align = array('C','C','C','C','C','C');

	
	$heading_width[0] = "15";
	$heading_width[1] = "100";
	
	$heading_text[0] = "ลำดับ";
	$heading_text[1] = "ประเภท";
	$heading_text[2] = "ครั้งที่";
	$heading_text[3] = "ตั้งแต่วันที่";
	$heading_text[4] = "ถึงวันที่";
	$heading_text[5] = "การส่งใบลา";
	
	$heading_align = array('C','C','C','C','C','C');
		
	/*
	function print_header(){
		global $pdf, $heading_width, $select_org_structure;
		
		$pdf->SetFont($font,'',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
		$pdf->Cell(100,2,"",0,1,'C');

		$pdf->Cell($heading_width[0] ,7,"ลำดับ",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"ประเภท",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7, "ครั้งที่",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[3] ,7, "ตั้งแต่วันที่",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[4] ,7, "ถึงวันที่",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7, "การส่งใบลา",'LTBR',1,'C',1);
	} // function		
*/
	if($DPISDB=="odbc"){
		$cmd = " select 		ABS_ID, a.PER_ID, a.AB_CODE, AB_NAME, ABS_STARTDATE, ABS_ENDDATE, ABS_DAY, ABS_LETTER
						 from 			PER_ABSENT a, PER_PERSONAL b, PER_ABSENTTYPE c 
						 where 		b.PER_TYPE=$PER_TYPE and a.PER_ID=$PER_ID and a.PER_ID=b.PER_ID and a.AB_CODE=c.AB_CODE
											$search_condition 
						 order by 	PER_NAME, PER_SURNAME ";	
	}elseif($DPISDB=="oci8"){
		$cmd = " select 		ABS_ID, a.PER_ID, a.AB_CODE, AB_NAME, ABS_STARTDATE, ABS_ENDDATE, ABS_DAY, ABS_LETTER
						 from 			PER_ABSENT a, PER_PERSONAL b, PER_ABSENTTYPE c 
						 where 		b.PER_TYPE=$PER_TYPE and a.PER_ID=$PER_ID and a.PER_ID=b.PER_ID and a.AB_CODE=c.AB_CODE
											$search_condition 
						 order by 	PER_NAME, PER_SURNAME ";	
					//  echo $cmd;
	}elseif($DPISDB=="mysql"){
		$cmd = " select 		ABS_ID, a.PER_ID, a.AB_CODE, AB_NAME, ABS_STARTDATE, ABS_ENDDATE, ABS_DAY, ABS_LETTER
						 from 			PER_ABSENT a, PER_PERSONAL b, PER_ABSENTTYPE c 
						 where 		b.PER_TYPE=$PER_TYPE and a.PER_ID=$PER_ID and a.PER_ID=b.PER_ID and a.AB_CODE=c.AB_CODE
											$search_condition 
						 order by 	PER_NAME, PER_SURNAME ";	
	} // end if
	$count_data = $db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();
	$data_count = $data_row = 0;
	while($data = $db_dpis->get_array()){
		$data_row++;

		$TMP_ABS_ID = $data[ABS_ID];
		$TMP_ABS_STARTDATE = show_date_format($data[ABS_STARTDATE],$DATE_DISPLAY);
		$TMP_ABS_ENDDATE = show_date_format($data[ABS_ENDDATE],$DATE_DISPLAY);
				
		$TMP_ABS_DAY = trim($data[ABS_DAY]);
		$TMP_AB_CODE = trim($data[AB_CODE]);
		if(trim($TMP_AB_CODE) == "10") $TMP_ABS_ENDDATE = "";
		$TMP_AB_NAME = trim($data[AB_NAME]);	
		$TMP_ABS_LETTER = trim($data[ABS_LETTER]);
		if ($TMP_ABS_LETTER == 1) 				$ABS_LETTER_STR = "ยังไม่ได้ส่ง";
		elseif ($TMP_ABS_LETTER == 2) 		$ABS_LETTER_STR = "ถูกต้อง";
		elseif ($TMP_ABS_LETTER == 3) 		$ABS_LETTER_STR = "ไม่ถูกต้อง";
		if(trim($TMP_AB_CODE) == "10") $ABS_LETTER_STR = "-";
		
		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][order] = $data_row;
		$arr_content[$data_count][ab_name] = $TMP_AB_NAME;
		$arr_content[$data_count][abs_no] = $data_row;
		$arr_content[$data_count][abs_startdate] = $TMP_ABS_STARTDATE;
		$arr_content[$data_count][abs_enddate] = $TMP_ABS_ENDDATE;
		$arr_content[$data_count][abs_letter] = $ABS_LETTER_STR;
		
		$data_count++;
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";

	$border = "";
	$pdf->SetFont($font,'',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

	$pdf->Cell(27, 7, "", $border, 0, 'L', 0);
	$pdf->Cell(120, 7, $ORG_NAME, $border, 0, 'L', 0);
	$pdf->Cell(170, 7, (trim($search_abs_month)?("เดือน  ".$month_full[$search_abs_month][TH]):""), $border, 0, 'L', 0);
	$pdf->Cell(70, 7, (trim($search_abs_year)?"ปี  $search_abs_year":""), $border, 1, 'L', 0);
		
	$pdf->Cell(27, 7, "", $border, 0, 'L', 0);
	$pdf->Cell(120, 7, "ชื่อ $PER_NAME", $border, 0, 'L', 0);
	$pdf->Cell(70, 7, "", $border, 0, 'L', 0);
	$pdf->Cell(70, 7, "", $border, 1, 'L', 0);

	if($count_data){
		$pdf->AutoPageBreak = false;
		$head_text1 = implode(",", $heading_text);
		$head_width1 = implode(",", $heading_width);
		$head_align1 = implode(",", $heading_align);
		$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", "0066CC", "EEEEFF", 0);
		if (!$result) echo "****** error ****** on open table for $table<br>";
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$ORDER = $arr_content[$data_count][order];
			$AB_NAME = $arr_content[$data_count][ab_name];
			$ABS_NO = $arr_content[$data_count][abs_no];
			$ABS_STARTDATE = $arr_content[$data_count][abs_startdate];
			$ABS_ENDDATE = $arr_content[$data_count][abs_enddate];
			$ABS_LETTER = $arr_content[$data_count][abs_letter];
	//new format************************************************************			
			$arr_data = (array) null;
			$arr_data[] = (($NUMBER_DISPLAY==2)?convert2thaidigit($ORDER):$ORDER);
			$arr_data[] =  "$AB_NAME";
			$arr_data[] = (($NUMBER_DISPLAY==2)?convert2thaidigit($ABS_NO):$ABS_NO);
			$arr_data[] =  (($NUMBER_DISPLAY==2)?convert2thaidigit($ABS_STARTDATE):$ABS_STARTDATE);
			$arr_data[] = (($NUMBER_DISPLAY==2)?convert2thaidigit($ABS_ENDDATE):$ABS_ENDDATE);
			$arr_data[] = "$ABS_LETTER";
			$data_align = array("C", "L", "C", "C", "L");
			
			$result = $pdf->add_data_tab($arr_data, 7, "TRHL", $data_align, "", "14", "", "000000", "");		//TRHBL
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
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