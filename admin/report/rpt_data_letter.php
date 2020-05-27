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
	
	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "";
	$report_title = "";
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
	
	if(trim($LET_DATE)){
		$arr_temp = explode("/", $LET_DATE);
		$LET_DATE = ($arr_temp[0] + 0) ."   ". $month_full[($arr_temp[1] + 0)][TH] ."   พ.ศ.  ". $arr_temp[2];
		//$LET_DATE = ($arr_temp[0])."-".($arr_temp[1] + 0)."-".$arr_temp[2];
		//$LET_DATE = show_date_format($data2[TRN_STARTDATE],$DATE_DISPLAY);
	} // end if
	
	if($DPISDB=="odbc"){
		$cmd = " select			a.PER_ID, b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_STARTDATE, a.LEVEL_NO, a.PER_TYPE, c.OT_NAME,
											a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID, a.PER_SALARY, a.PER_MGTSALARY, a.PER_SPSALARY
						 from			PER_PERSONAL a, PER_PRENAME b, PER_OFF_TYPE c
						 where		a.PER_ID=$PER_ID and a.PN_CODE=b.PN_CODE and a.OT_CODE=c.OT_CODE
					  ";	
	}elseif($DPISDB=="oci8"){
		$cmd = " select			a.PER_ID, b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_STARTDATE, a.LEVEL_NO, a.PER_TYPE, c.OT_NAME,
											a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID, a.PER_SALARY, a.PER_MGTSALARY, a.PER_SPSALARY
						 from			PER_PERSONAL a, PER_PRENAME b, PER_OFF_TYPE c
						 where		a.PER_ID=$PER_ID and a.PN_CODE=b.PN_CODE and a.OT_CODE=c.OT_CODE
					  ";	
	}elseif($DPISDB=="mysql"){
		$cmd = " select			a.PER_ID, b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_STARTDATE, a.LEVEL_NO, a.PER_TYPE, c.OT_NAME,
											a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID, a.PER_SALARY, a.PER_MGTSALARY, a.PER_SPSALARY
						 from			PER_PERSONAL a, PER_PRENAME b, PER_OFF_TYPE c
						 where		a.PER_ID=$PER_ID and a.PN_CODE=b.PN_CODE and a.OT_CODE=c.OT_CODE
					  ";	
	} // end if
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data = $db_dpis->get_array();

	$PER_ID = $data[PER_ID];
	$PER_NAME = "$data[PN_NAME]$data[PER_NAME] $data[PER_SURNAME]";
	$PER_STARTDATE = show_date_format($data[PER_STARTDATE],$DATE_DISPLAY);
	$LEVEL_NO = trim($data[LEVEL_NO]);
	
	$cmd = "select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where LEVEL_NO = '$LEVEL_NO'";
	$db_dpis2->send_cmd($cmd);
	$data2  = $db_dpis2->get_array();
	$LEVEL_NAME2 = $data2[LEVEL_NAME];
	$POSITION_LEVEL = $data2[POSITION_LEVEL];
	if (!$POSITION_LEVEL) $POSITION_LEVEL = $LEVEL_NAME2;
	
	$PER_TYPE = $data[PER_TYPE];
	$OFFICER_TYPE = $data[OT_NAME];
	$PER_SALARY = $data[PER_SALARY]?number_format($data[PER_SALARY]):"-";
	$PER_MGTSALARY = $data[PER_MGTSALARY]?number_format($data[PER_MGTSALARY]):"-";
	$PER_SPSALARY = $data[PER_SPSALARY]?number_format($data[PER_SPSALARY]):"-";

	if($PER_TYPE==1){
		$POS_ID = $data[POS_ID];
		$cmd = "	select		a.POS_NO, b.PL_NAME, a.ORG_ID, c.ORG_NAME, c.ORG_ID_REF, a.PT_CODE
							from		PER_POSITION a, PER_LINE b, PER_ORG c
							where		a.POS_ID=$POS_ID and a.PL_CODE=b.PL_CODE and a.ORG_ID=c.ORG_ID ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$POS_NO = trim($data2[POS_NO]);
		$PL_NAME = trim($data2[PL_NAME]);
		$ORG_ID = $data2[ORG_ID];
		$ORG_NAME = trim($data2[ORG_NAME]);
		$DEPARTMENT_ID = $data2[ORG_ID_REF];
		$PT_CODE = trim($data[PT_CODE]);
		$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE)='$PT_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$PT_NAME = trim($data2[PT_NAME]);
		$PL_NAME = trim($PL_NAME)?($PL_NAME . $POSITION_LEVEL . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"")):"";
		$LEVEL_NAME = (($PT_CODE==11)?"ท.":$PT_NAME) . $LEVEL_NAME2;
	}elseif($PER_TYPE==2){
		$POEM_ID = $data[POEM_ID];
		$cmd = "	select		a.POEM_NO, b.PN_NAME, a.ORG_ID, c.ORG_NAME, c.ORG_ID_REF
							from		PER_POS_EMP a, PER_POS_NAME b, PER_ORG c
							where		a.POEM_ID=$POEM_ID and a.PN_CODE=b.PN_CODE and a.ORG_ID=c.ORG_ID ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$POS_NO = trim($data2[POEM_NO]);
		$PL_NAME = trim($data2[PN_NAME]);
		$ORG_ID = $data2[ORG_ID];
		$ORG_NAME = trim($data2[ORG_NAME]);
		$DEPARTMENT_ID = $data2[ORG_ID_REF];
		$PL_NAME = trim($PL_NAME)?($PL_NAME . $POSITION_LEVEL):"";
		$LEVEL_NAME = $LEVEL_NAME2;
	}elseif($PER_TYPE==3){
		$POEMS_ID = $data[POEMS_ID];
		$cmd = "	select		a.POEMS_NO, b.EP_NAME, a.ORG_ID, c.ORG_NAME, c.ORG_ID_REF
							from		PER_POS_EMPSER a, PER_EMPSER_POS_NAME b, PER_ORG c
							where		a.POEMS_ID=$POEMS_ID and a.EP_CODE=b.EP_CODE and a.ORG_ID=c.ORG_ID ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$POS_NO = trim($data2[POEMS_NO]);
		$PL_NAME = trim($data2[EP_NAME]);
		$ORG_ID = $data2[ORG_ID];
		$ORG_NAME = trim($data2[ORG_NAME]);
		$DEPARTMENT_ID = $data2[ORG_ID_REF];
		$PL_NAME = trim($PL_NAME)?($PL_NAME . $POSITION_LEVEL):"";
		$LEVEL_NAME = $LEVEL_NAME2;
	}elseif($PER_TYPE==4){
		$POT_ID = $data[POT_ID];
		$cmd = "	select		a.POT_NO, b.TP_NAME, a.ORG_ID, c.ORG_NAME, c.ORG_ID_REF
							from		PER_POS_TEMP a, PER_TEMP_POS_NAME b, PER_ORG c
							where		a.POT_ID=$POT_ID and a.TP_CODE=b.TP_CODE and a.ORG_ID=c.ORG_ID ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$POS_NO = trim($data2[POT_NO]);
		$PL_NAME = trim($data2[TP_NAME]);
		$ORG_ID = $data2[ORG_ID];
		$ORG_NAME = trim($data2[ORG_NAME]);
		$DEPARTMENT_ID = $data2[ORG_ID_REF];
		$PL_NAME = trim($PL_NAME)?($PL_NAME . $POSITION_LEVEL):"";
		$LEVEL_NAME = $LEVEL_NAME2;
	} // end if
	
	$cmd = " select ORG_ID_REF, ORG_NAME from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
	if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	$db_dpis2->send_cmd($cmd);
	$data2 = $db_dpis2->get_array();
	$DEPARTMENT_NAME = $data2[ORG_NAME];
	$MINISTRY_ID = $data2[ORG_ID_REF];
	
	$cmd = " select ORG_NAME,ORG_ADDR1, ORG_ADDR2, ORG_ADDR3 from PER_ORG where ORG_ID=$MINISTRY_ID ";
	if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	$db_dpis2->send_cmd($cmd);
	$data2 = $db_dpis2->get_array();
	$MINISTRY_NAME = $data2[ORG_NAME];
	$ORG_ADDR1 = $data2[ORG_ADDR1];
	$ORG_ADDR2 = $data2[ORG_ADDR2];
	$ORG_ADDR3 = $data2[ORG_ADDR3];
	
	$pdf->SetFont($font,'',18);

	$pdf->Cell(200, 20, "", "", 1, 'L', 0);
		
	$pdf->Image("../images/krut.jpg", ($pdf->x + ((200 / 2) - 10)), ($pdf->y - 10), 20, 20,"jpg");

	$pdf->Cell(200, 15, "", "", 1, 'L', 0);

	$pdf->Cell(20, 7, "", "", 0, 'L', 0);
	$pdf->Cell(180, 7, convert2thaidigit("เลขที่   $LET_NO"), "", 1, 'L', 0);

	$pdf->Cell(130, 7, "", "", 0, 'L', 0);
	$pdf->Cell(70, 7, "$DEPARTMENT_NAME", "", 1, 'L', 0);

	$pdf->Cell(130, 7, "", "", 0, 'L', 0);
	$pdf->Cell(70, 7, convert2thaidigit("$ORG_ADDR1"), "", 1, 'L', 0);
	
	$pdf->Cell(200, 10, "", "", 1, 'L', 0);

	$text = "หนังสือฉบับนี้ให้ไว้เพื่อรับรองว่า  $PER_NAME  เป็น  $OFFICER_TYPE  ตำแหน่ง$PL_NAME  $ORG_NAME  $DEPARTMENT_NAME  $MINISTRY_NAME  รับเงินเดือนในอันดับ  $LEVEL_NAME  ขั้น  $PER_SALARY  บาท  เงินค่าตอบแทนนอกเหนือจากเงินเดือนเดือนละ  $PER_MGTSALARY  บาท และเงินตอบแทนพิเศษของข้าราชการผู้ได้รับเงินเดือนขั้นสูงของอันดับหรือตำแหน่งเดือนละ  $PER_SPSALARY  บาท";
	$pdf->Cell(20, 7, "", "", 0, 'L', 0);
	$pdf->MultiCell(160, 7, str_repeat(" ", 20) . convert2thaidigit($text), "", "L");

	$pdf->Cell(200, 20, "", "", 1, 'L', 0);

	$pdf->Cell(80, 7, "", "", 0, 'L', 0);
	$pdf->Cell(100, 7, convert2thaidigit("ให้ไว้  ณ  วันที่  $LET_DATE"), "", 1, 'C', 0);

	$pdf->Cell(200, 20, "", "", 1, 'L', 0);

	$pdf->Cell(80, 7, "", "", 0, 'L', 0);
	$pdf->Cell(100, 7, "($PER_NAME_SIGN1)", "", 1, 'C', 0);

	$pdf->Cell(80, 7, "", "", 0, 'L', 0);
	$pdf->Cell(100, 7, convert2thaidigit("$LET_POSITION"), "", 1, 'C', 0);

	$pdf->Cell(80, 7, "", "", 0, 'L', 0);
	$pdf->Cell(100, 7, ($LET_ASSIGN==1?"แทน":($LET_ASSIGN==2?"รักษาการแทน":""))."$LET_SIGN", "", 1, 'C', 0);

	$pdf->close();
	$pdf->Output();

	ini_set("max_execution_time", 30);
?>