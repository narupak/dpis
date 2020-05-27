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
	
	$cmd = " select		COM_NO, a.COM_NAME, COM_DATE, COM_NOTE, COM_PER_TYPE, 
									a.COM_TYPE, COM_CONFIRM, b.COM_DESC
					 from		PER_COMMAND a, PER_COMTYPE b
					 where	COM_ID=$COM_ID and a.COM_TYPE=b.COM_TYPE ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	//$db_dpis->show_error();
		
	$COM_NO = trim($data[COM_NO]);
	$COM_NAME = trim($data[COM_NAME]);
	$COM_DATE = $data[COM_DATE];
	$COM_DATE = show_date_format($COM_DATE,3);
	$COM_NOTE = trim($data[COM_NOTE]);
	$COM_PER_TYPE = trim($data[COM_PER_TYPE]);
	$COM_CONFIRM = trim($data[COM_CONFIRM]);
		
	$COM_TYPE = trim($data[COM_TYPE]);

	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "";
	$report_title = "บัญชีรายละเอียดการเลื่อนระดับ$PERSON_TYPE[$COM_PER_TYPE] แนบท้ายคำสั่ง $DEPARTMENT_NAME ที่ $COM_NO ลงวันที่ $COM_DATE";
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
	
	$heading_width[0] = "8";
	$heading_width[1] = "29";
	$heading_width[2] = "31";
	$heading_width[3] = "31";
	$heading_width[4] = "15";
	$heading_width[5] = "12";
	$heading_width[6] = "12";
	$heading_width[7] = "15";
	$heading_width[8] = "18";
	$heading_width[9] = "31";
	$heading_width[10] = "15";
	$heading_width[11] = "12";
	$heading_width[12] = "12";
	$heading_width[13] = "15";
	$heading_width[14] = "16";
	$heading_width[15] = "25";
	
	function print_header(){
		global $pdf, $heading_width, $FULLNAME_HEAD;
		
		$pdf->SetFont($font,'',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
		$pdf->Cell(100,2,"",0,1,'C');

		$pdf->Cell($heading_width[0] ,7,"ลำดับ",'LTR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"",'LTR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"",'LTR',0,'C',1);
		$pdf->Cell(($heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6]+$heading_width[7]) ,7,"ตำแหน่งและส่วนราชการเดิม",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[8] ,7,"ดำรงตำแหน่ง",'LTR',0,'C',1);
		$pdf->Cell(($heading_width[9] + $heading_width[10] + $heading_width[11] + $heading_width[12]+$heading_width[13]) ,7,"ตำแหน่งและส่วนราชการที่เลื่อน",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[14] ,7,"",'LTR',0,'C',1);
		$pdf->Cell($heading_width[15] ,7,"",'LTR',1,'C',1);

		$pdf->Cell($heading_width[0] ,7,"ที่",'LR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,$FULLNAME_HEAD,'LR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"วุฒิ/สาขา",'LR',0,'C',1);
		$pdf->Cell($heading_width[3] ,7,"ตำแหน่ง/สังกัด",'LTR',0,'C',1);
		$pdf->Cell($heading_width[4] ,7,"ตำแหน่ง",'LTR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,"ระดับ",'LTR',0,'C',1);
		$pdf->Cell($heading_width[6] ,7,"เลขที่",'LTR',0,'C',1);
		$pdf->Cell($heading_width[7] ,7,"เงินเดือน",'LTR',0,'C',1);
		$pdf->Cell($heading_width[8] ,7,"ในระดับ",'LR',0,'C',1);
		$pdf->Cell($heading_width[9] ,7,"ตำแหน่ง/สังกัด",'LTR',0,'C',1);
		$pdf->Cell($heading_width[10] ,7,"ตำแหน่ง",'LTR',0,'C',1);
		$pdf->Cell($heading_width[11] ,7,"ระดับ",'LTR',0,'C',1);
		$pdf->Cell($heading_width[12] ,7,"เลขที่",'LTR',0,'C',1);
		$pdf->Cell($heading_width[13] ,7,"เงินเดือน",'LTR',0,'C',1);
		$pdf->Cell($heading_width[14] ,7,"ตั้งแต่วันที่",'LR',0,'C',1);
		$pdf->Cell($heading_width[15] ,7,"หมายเหตุ",'LR',1,'C',1);

		$pdf->Cell($heading_width[0] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"สถานศึกษา",'LBR',0,'C',1);
		$pdf->Cell($heading_width[3] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[4] ,7,"ประเภท",'LBR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[6] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[7] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[8] ,7,"ปัจจุบันเมื่อ",'LBR',0,'C',1);
		$pdf->Cell($heading_width[9] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[10] ,7,"ประเภท",'LBR',0,'C',1);
		$pdf->Cell($heading_width[11] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[12] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[13] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[14] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[15] ,7,"",'LBR',1,'C',1);
	} // function		
		
	if($DPISDB=="odbc"){
		$cmd = " select			a.PER_ID, b.PN_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE, b.PER_TYPE, b.LEVEL_NO,
											a.EN_CODE, a.CMD_POSITION, a.CMD_ORG3, a.CMD_ORG4, a.CMD_OLD_SALARY, a.CMD_LEVEL, 
											a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID, a.LEVEL_NO as NEW_LEVEL_NO, a.CMD_SALARY,
											a.CMD_DATE, a.CMD_NOTE1, a.CMD_NOTE2
						 from			PER_COMDTL a, PER_PERSONAL b
						 where		a.COM_ID=$COM_ID and a.PER_ID=b.PER_ID 
						 order by 	a.CMD_ORG3, a.CMD_ORG4, a.CMD_POS_NO_NAME, a.CMD_POS_NO ";	
	}elseif($DPISDB=="oci8"){
		$cmd = " select			a.PER_ID, b.PN_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE, b.PER_TYPE, b.LEVEL_NO,
											a.EN_CODE, a.CMD_POSITION, a.CMD_ORG3, a.CMD_ORG4, a.CMD_OLD_SALARY, a.CMD_LEVEL,
											a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID, a.LEVEL_NO as NEW_LEVEL_NO, a.CMD_SALARY,
											a.CMD_DATE, a.CMD_NOTE1, a.CMD_NOTE2
						 from			PER_COMDTL a, PER_PERSONAL b
						 where		a.COM_ID=$COM_ID and a.PER_ID=b.PER_ID 
						 order by 	a.CMD_ORG3, a.CMD_ORG4, a.CMD_POS_NO_NAME, a.CMD_POS_NO ";	
	}elseif($DPISDB=="mysql"){
		$cmd = " select			a.PER_ID, b.PN_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE, b.PER_TYPE, b.LEVEL_NO,
											a.EN_CODE, a.CMD_POSITION, a.CMD_ORG3, a.CMD_ORG4, a.CMD_OLD_SALARY, a.CMD_LEVEL, 
											a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID, a.LEVEL_NO as NEW_LEVEL_NO, a.CMD_SALARY,
											a.CMD_DATE, a.CMD_NOTE1, a.CMD_NOTE2
						 from			PER_COMDTL a, PER_PERSONAL b
						 where		a.COM_ID=$COM_ID and a.PER_ID=b.PER_ID 
						 order by 	a.CMD_ORG3, a.CMD_ORG4, a.CMD_POS_NO_NAME, a.CMD_POS_NO ";	
	} // end if
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data_count = $data_row = 0;
	$CMD_ORG3 = $CMD_ORG4 = -1;
	while($data = $db_dpis->get_array()){
		$data_row++;

		$PER_ID = $data[PER_ID];
		$PN_CODE = trim($data[PN_CODE]);
		$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='$PN_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$PN_NAME = $data2[PN_NAME];		
		$PER_NAME = $data[PER_NAME];
		$PER_SURNAME = $data[PER_SURNAME];
		$PER_CARDNO = $data[PER_CARDNO];
		$PER_BIRTHDATE = show_date_format($data[PER_BIRTHDATE],$DATE_DISPLAY);
		$PER_TYPE = $data[PER_TYPE];
		$LEVEL_NO = trim($data[LEVEL_NO]);

		$EDU_TYPE = "%2%";
		if($DPISDB=="odbc"){
			$cmd = "	select		b.EN_NAME, c.EM_NAME, d.INS_NAME
								from		( 
													(
														PER_EDUCATE a
														left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
													) left join PER_EDUCMAJOR c on (a.EM_CODE=c.EM_CODE)
												) left join PER_INSTITUTE d on (a.INS_CODE=d.INS_CODE)
								where		a.PER_ID = $PER_ID and a.EDU_TYPE like '$EDU_TYPE'
						   ";
		}elseif($DPISDB=="oci8"){
			$cmd = "	select		b.EN_NAME, c.EM_NAME, d.INS_NAME
								from		PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCMAJOR c, PER_INSTITUTE d
								where		a.PER_ID = $PER_ID and a.EDU_TYPE like '$EDU_TYPE' and a.EN_CODE=b.EN_CODE(+) and a.EM_CODE=c.EM_CODE(+) and a.INS_CODE=d.INS_CODE(+)
						   ";
		}elseif($DPISDB=="mysql"){
			$cmd = "	select		b.EN_NAME, c.EM_NAME, d.INS_NAME
								from		( 
													(
														PER_EDUCATE a
														left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
													) left join PER_EDUCMAJOR c on (a.EM_CODE=c.EM_CODE)
												) left join PER_INSTITUTE d on (a.INS_CODE=d.INS_CODE)
								where		a.PER_ID = $PER_ID and a.EDU_TYPE like '$EDU_TYPE'
						   ";
		} // end if
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$EN_NAME = trim($data2[EN_NAME]);
		$EM_NAME = trim($data2[EM_NAME]);
		$INS_NAME = trim($data2[INS_NAME]);
		
		$CMD_LEVEL = trim($data[CMD_LEVEL]);
		if($DPISDB=="mysql"){
			$arr_temp = explode("|", $data[CMD_POSITION]);
		}else{
			$arr_temp = explode("\|", $data[CMD_POSITION]);
		}
		$CMD_POS_NO = $arr_temp[0];
		$CMD_POSITION = $arr_temp[1];
//		$CMD_ORG3 = $data[CMD_ORG3];
		$CMD_OLD_SALARY = $data[CMD_OLD_SALARY];
		$NEW_LEVEL_NO = trim($data[NEW_LEVEL_NO]);
		$CMD_SALARY = $data[CMD_SALARY];
		$CMD_DATE = show_date_format(substr($data[CMD_DATE], 0, 10),$DATE_DISPLAY);
		$CMD_BUDGET_YEAR = ($arr_temp[0] + 543);
		if(($arr_temp[1]."-".$arr_temp[2]) >= "10-01") $CMD_BUDGET_YEAR += 1;
		$CMD_NOTE1 = trim($data[CMD_NOTE1]);
		$CMD_NOTE2 = trim($data[CMD_NOTE2]);				
		
		//--ตำแหน่งและส่วนราชการเดิม
		$cmd = "select POSITION_TYPE, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$CMD_LEVEL' ";
		$db_dpis2->send_cmd($cmd);
		$data2= $db_dpis2->get_array();
		$CMD_POSITION_TYPE = trim($data2[POSITION_TYPE]);
		$CMD_LEVEL_NAME = trim($data2[POSITION_LEVEL]);
		
		//--ตำแหน่งและส่วนราชการที่ย้าย
		$cmd = "select POSITION_TYPE, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$NEW_LEVEL_NO' ";
		$db_dpis2->send_cmd($cmd);
		$data2= $db_dpis2->get_array();
		$NEW_POSITION_TYPE = trim($data2[POSITION_TYPE]);
		$NEW_LEVEL_NAME = trim($data2[POSITION_LEVEL]);
		
		if($PER_TYPE==1){
			$cmd = " select a.PM_CODE, a.PT_CODE, b.PT_NAME from PER_POSITION a, PER_TYPE b where trim(a.POS_NO)='$CMD_POS_NO' and a.PT_CODE=b.PT_CODE and a.DEPARTMENT_ID = $DEPARTMENT_ID";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$CMD_PT_CODE = trim($data2[PT_CODE]);
			$CMD_PT_NAME = trim($data2[PT_NAME]);
			$CMD_PM_CODE = trim($data2[PM_CODE]);
			
			$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$CMD_PM_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$CMD_PM_NAME = trim($data2[PM_NAME]);

			$CMD_POSITION = (trim($CMD_PM_CODE)?"$CMD_PM_NAME (":"") . (trim($CMD_POSITION)?($CMD_POSITION ." ". (($CMD_PT_NAME != "ทั่วไป" && $CMD_LEVEL >= 6)?"$CMD_PT_NAME":"")):"") . (trim($CMD_PM_CODE)?")":"");

			$POS_ID = $data[POS_ID];
			$cmd = "	select		a.POS_NO, b.PL_NAME, c.ORG_NAME, a.PT_CODE, d.PT_NAME, a.ORG_ID_1, a.ORG_ID_2, a.PM_CODE
						from		PER_POSITION a, PER_LINE b, PER_ORG c, PER_TYPE d
						where		a.POS_ID=$POS_ID and a.PL_CODE=b.PL_CODE and a.ORG_ID=c.ORG_ID and a.PT_CODE=d.PT_CODE ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$NEW_POS_NO = trim($data2[POS_NO]);
			$NEW_PL_NAME = trim($data2[PL_NAME]);
			$NEW_ORG_NAME = trim($data2[ORG_NAME]);
			$NEW_PT_CODE = trim($data2[PT_CODE]);
			$NEW_PT_NAME = trim($data2[PT_NAME]);
			$NEW_PM_CODE = trim($data2[PM_CODE]);
		
			$NEW_ORG_ID_1 = $data2[ORG_ID_1];
			$NEW_ORG_ID_2 = $data2[ORG_ID_2];
			
			$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$NEW_PM_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$NEW_PM_NAME = trim($data2[PM_NAME]);
			
			$NEW_PL_NAME = (trim($NEW_PM_CODE)?"$NEW_PM_NAME (":"") . (trim($NEW_PL_NAME)?($NEW_PL_NAME ." ". (($NEW_PT_NAME != "ทั่วไป" && $NEW_LEVEL_NO >= 6)?"$NEW_PT_NAME":"")):"") . (trim($NEW_PM_CODE)?")":"");
		}elseif($PER_TYPE==2){
			$CMD_POSITION = trim($CMD_POSITION)?($CMD_POSITION):"";

			$POEM_ID = $data[POEM_ID];
			$cmd = "	select		a.POEM_NO, b.PN_NAME, c.ORG_NAME, a.ORG_ID_1, a.ORG_ID_2
								from		PER_POS_EMP a, PER_POS_NAME b, PER_ORG c
								where		a.POEM_ID=$POEM_ID and a.PN_CODE=b.PN_CODE and a.ORG_ID=c.ORG_ID ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$NEW_POS_NO = trim($data2[POEM_NO]);
			$NEW_PL_NAME = trim($data2[PN_NAME]);
			$NEW_ORG_NAME = trim($data2[ORG_NAME]);
			$NEW_PL_NAME = trim($NEW_PL_NAME)?($NEW_PL_NAME):"";
		
			$NEW_ORG_ID_1 = $data2[ORG_ID_1];
			$NEW_ORG_ID_2 = $data2[ORG_ID_2];
		}elseif($PER_TYPE==3){
			$CMD_POSITION = trim($CMD_POSITION)?($CMD_POSITION):"";

			$POEMS_ID = $data[POEMS_ID];
			$cmd = "	select		a.POEMS_NO, b.EP_NAME, c.ORG_NAME, a.ORG_ID_1, a.ORG_ID_2
								from		PER_POS_EMPSER a, PER_EMPSER_POS_NAME b, PER_ORG c
								where		a.POEMS_ID=$POEMS_ID and a.EP_CODE=b.EP_CODE and a.ORG_ID=c.ORG_ID ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$NEW_POS_NO = trim($data2[POEMS_NO]);
			$NEW_PL_NAME = trim($data2[EP_NAME]);
			$NEW_ORG_NAME = trim($data2[ORG_NAME]);
			$NEW_PL_NAME = trim($NEW_PL_NAME)?($NEW_PL_NAME):"";
		
			$NEW_ORG_ID_1 = $data2[ORG_ID_1];
			$NEW_ORG_ID_2 = $data2[ORG_ID_2];
		} elseif($PER_TYPE==4){
			$CMD_POSITION = trim($CMD_POSITION)?($CMD_POSITION):"";

			$POT_ID = $data[POT_ID];
			$cmd = "	select		a.POT_NO, b.TP_NAME, c.ORG_NAME, a.ORG_ID_1, a.ORG_ID_2
								from		PER_POS_TEMP a, PER_TEMP_POS_NAME b, PER_ORG c
								where		a.POT_ID=$POT_ID and a.TP_CODE=b.TP_CODE and a.ORG_ID=c.ORG_ID ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$NEW_POS_NO = trim($data2[POT_NO]);
			$NEW_PL_NAME = trim($data2[TP_NAME]);
			$NEW_ORG_NAME = trim($data2[ORG_NAME]);
			$NEW_PL_NAME = trim($NEW_PL_NAME)?($NEW_PL_NAME):"";
		
			$NEW_ORG_ID_1 = $data2[ORG_ID_1];
			$NEW_ORG_ID_2 = $data2[ORG_ID_2];
		} // end if

		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$NEW_ORG_ID_1 ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$NEW_ORG_NAME_1 = $data2[ORG_NAME];
		
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$NEW_ORG_ID_2 ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$NEW_ORG_NAME_2 = $data2[ORG_NAME];

		if($CMD_ORG3 != trim($data[CMD_ORG3])){
			$CMD_ORG3 = trim($data[CMD_ORG3]);

			$arr_content[$data_count][type] = "ORG";
			$arr_content[$data_count][org_name] = $CMD_ORG3;
			$arr_content[$data_count][new_org_name] = $NEW_ORG_NAME;

			$data_count++;
		} // end if
		
		if($CMD_ORG4 != trim($data[CMD_ORG4])){
			$CMD_ORG4 = trim($data[CMD_ORG4]);

			$arr_content[$data_count][type] = "ORG_1";
			$arr_content[$data_count][org_name] = $CMD_ORG4;
			$arr_content[$data_count][new_org_name] = $NEW_ORG_NAME_1;

			$data_count++;
		} // end if

		$cmd = " select 	min(POH_EFFECTIVEDATE) as LEVEL_EFFECTIVEDATE 
						 from 		PER_POSITIONHIS 
						 where 	PER_ID=$PER_ID and trim(LEVEL_NO)='$LEVEL_NO'
					  ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$LEVEL_EFFECTIVEDATE = show_date_format($data2[LEVEL_EFFECTIVEDATE],$DATE_DISPLAY);
		
		$cmd = " select 	SAH_SALARY 
						 from 		PER_SALARYHIS 
						 where 	PER_ID=$PER_ID 
						 				and SAH_EFFECTIVEDATE < '". ($CMD_BUDGET_YEAR - 543) ."-10-01'
						 				and SAH_EFFECTIVEDATE >= '". ($CMD_BUDGET_YEAR - 543 - 1) ."-10-01'
						 order by SAH_EFFECTIVEDATE desc
					  ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$BUDGET_YEAR_SALARY = $data2[SAH_SALARY];

		$cmd = " select 	SAH_SALARY 
						 from 		PER_SALARYHIS 
						 where 	PER_ID=$PER_ID 
						 				and SAH_EFFECTIVEDATE < '". ($CMD_BUDGET_YEAR - 543 - 1) ."-10-01'
						 				and SAH_EFFECTIVEDATE >= '". ($CMD_BUDGET_YEAR - 543 - 2) ."-10-01'
						 order by SAH_EFFECTIVEDATE desc
					  ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$PREV_BUDGET_YEAR_SALARY = $data2[SAH_SALARY];
$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][order] = $data_row;
		$arr_content[$data_count][name] = $PN_NAME . $PER_NAME ." ". $PER_SURNAME;
		$arr_content[$data_count][educate] = $EN_NAME . ($EM_NAME?"/$EM_NAME":"");
		
		$arr_content[$data_count][cmd_pos_no] = $CMD_POS_NO;
		$arr_content[$data_count][cmd_position] = $CMD_POSITION;
		$arr_content[$data_count][cmd_position_type] = $CMD_POSITION_TYPE;
		$arr_content[$data_count][cmd_level_name] = $CMD_LEVEL_NAME;
		$arr_content[$data_count][cmd_old_salary] = $CMD_OLD_SALARY?number_format($CMD_OLD_SALARY):"-";

		$arr_content[$data_count][level_effectivedate] = $LEVEL_EFFECTIVEDATE;
		$arr_content[$data_count][budget_year_salary] = $BUDGET_YEAR_SALARY?number_format($BUDGET_YEAR_SALARY):"-";
		$arr_content[$data_count][prev_budget_year_salary] = $PREV_BUDGET_YEAR_SALARY?number_format($PREV_BUDGET_YEAR_SALARY):"-";
		
		$arr_content[$data_count][new_position] = $NEW_PL_NAME;
		$arr_content[$data_count][new_position_type] = $NEW_POSITION_TYPE;
		$arr_content[$data_count][new_level_name] = $NEW_LEVEL_NAME;
		$arr_content[$data_count][new_pos_no] = $NEW_POS_NO;
		$arr_content[$data_count][cmd_salary] = $CMD_SALARY?number_format($CMD_SALARY):"-";
		
		$arr_content[$data_count][cmd_date] = $CMD_DATE;
//		$arr_content[$data_count][cmd_note] = $CMD_NOTE1;
		$arr_content[$data_count][cmd_note1] = $CMD_NOTE1;
		
		$data_count++;

		$arr_content[$data_count][type] = "CONTENT";
		///$arr_content[$data_count][name] = card_no_format($PER_CARDNO,$CARD_NO_DISPLAY);
		$arr_content[$data_count][educate] = $INS_NAME;
//		$arr_content[$data_count][cmd_position] = $CMD_ORG3;
//		$arr_content[$data_count][new_position] = $NEW_ORG_NAME;
//		$arr_content[$data_count][cmd_note] = $CMD_NOTE2;

		$data_count++;
		
		if($CMD_NOTE2){
			$arr_content[$data_count][type] = "CONTENT";
			$arr_content[$data_count][cmd_note2] = $CMD_NOTE2;

			$data_count++;
		} // end if
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
	if($count_data){
		$pdf->AutoPageBreak = false;
		print_header();

		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$CONTENT_TYPE = $arr_content[$data_count][type];
			$ORDER = $arr_content[$data_count][order];
			$NAME = $arr_content[$data_count][name];
			$EDUCATE = $arr_content[$data_count][educate];
			$CMD_POS_NO = $arr_content[$data_count][cmd_pos_no];
			$CMD_POSITION = $arr_content[$data_count][cmd_position];
			$CMD_POSITION_TYPE = $arr_content[$data_count][cmd_position_type];
			$CMD_LEVEL_NAME = $arr_content[$data_count][cmd_level_name];
			$CMD_ORG3 = $arr_content[$data_count][cmd_org3];

			$LEVEL_EFFECTIVEDATE = $arr_content[$data_count][level_effectivedate];
			$BUDGET_YEAR_SALARY = $arr_content[$data_count][budget_year_salary];
			$PREV_BUDGET_YEAR_SALARY = $arr_content[$data_count][prev_budget_year_salary];

			$CMD_OLD_SALARY = $arr_content[$data_count][cmd_old_salary];
			$NEW_POSITION = $arr_content[$data_count][new_position];
			$NEW_POSITION_TYPE = $arr_content[$data_count][new_position_type];
			$NEW_LEVEL_NAME = $arr_content[$data_count][new_level_name];
			$NEW_POS_NO = $arr_content[$data_count][new_pos_no];
			$CMD_SALARY = $arr_content[$data_count][cmd_salary];
			$CMD_DATE = $arr_content[$data_count][cmd_date];
//			$CMD_NOTE = $arr_content[$data_count][cmd_note];
			$CMD_NOTE1 = $arr_content[$data_count][cmd_note1];
			$CMD_NOTE2 = $arr_content[$data_count][cmd_note2];
			
			if($CONTENT_TYPE=="ORG" || $CONTENT_TYPE=="ORG_1" || $CONTENT_TYPE=="ORG_2"){
				$ORG_NAME = $arr_content[$data_count][org_name];
				$NEW_ORG_NAME = $arr_content[$data_count][new_org_name];
				
				$border = "";
				if($CONTENT_TYPE == "ORG") $pdf->SetFont($font,'b','',14);
				else $pdf->SetFont($font,'',14);
				$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
				
				$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
				
				$pdf->Cell(($heading_width[0] + $heading_width[1] + $heading_width[2]), 7, "", $border, 0, 'L', 0);
				$pdf->MultiCell(($heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6] + $heading_width[7]), 7, "$ORG_NAME", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6] + $heading_width[7];
				$pdf->y = $start_y;
				$pdf->Cell($heading_width[8] , 7, "", $border, 0, 'L', 0);
				$pdf->MultiCell(($heading_width[9] + $heading_width[10] + $heading_width[11] + $heading_width[12] + $heading_width[13]), 7, "$NEW_ORG_NAME", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6] + $heading_width[7] + $heading_width[8] + $heading_width[9] + $heading_width[10] + $heading_width[11] + $heading_width[12] + $heading_width[13];
				$pdf->y = $start_y;
				$pdf->Cell($heading_width[14] , 7, "", $border, 0, 'L', 0);
				$pdf->Cell($heading_width[15] , 7, "", $border, 0, 'L', 0);			
				
				//================= Draw Border Line ====================
				$line_start_y = $start_y;		$line_start_x = $start_x;
				$line_end_y = $max_y;		$line_end_x = $start_x;
				$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
					
				for($i=0; $i<=15; $i++){
					if($i < 3 || $i==8 || $i > 13){
						$line_start_y = $start_y;		$line_start_x += $heading_width[$i];
						$line_end_y = $max_y;		$line_end_x += $heading_width[$i];
					}elseif($i==3){
						$line_start_y = $start_y;		$line_start_x += ($heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6] + $heading_width[7]);
						$line_end_y = $max_y;		$line_end_x += ($heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6] + $heading_width[7]);
					}elseif($i==9){
						$line_start_y = $start_y;		$line_start_x += ($heading_width[9] + $heading_width[10] + $heading_width[11] + $heading_width[12] + $heading_width[13]);
						$line_end_y = $max_y;		$line_end_x += ($heading_width[9] + $heading_width[10] + $heading_width[11] + $heading_width[12] + $heading_width[13]);
					} // end if

					$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				} // end for
				//====================================================

				if(($pdf->h - $max_y) < 55){ 
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
			}elseif($CONTENT_TYPE=="CONTENT"){
				$border = "";
				$pdf->SetFont($font,'',14);
				$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
	
				$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
				
				$pdf->Cell($heading_width[0], 7, "$ORDER", $border, 0, 'C', 0);
				$pdf->MultiCell($heading_width[1], 7, "$NAME", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1];
				$pdf->y = $start_y;
				$pdf->MultiCell($heading_width[2], 7, "$EDUCATE", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2];
				$pdf->y = $start_y;
				$pdf->MultiCell($heading_width[3], 7, "$CMD_POSITION", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3];
				$pdf->y = $start_y;
				$pdf->Cell($heading_width[4], 7, "$CMD_POSITION_TYPE", $border, 0, 'C', 0);
				$pdf->Cell($heading_width[5], 7, "$CMD_LEVEL_NAME", $border, 0, 'C', 0);
				$pdf->Cell($heading_width[6], 7, "$CMD_POS_NO", $border, 0, 'C', 0);
				$pdf->Cell($heading_width[7], 7, "$PREV_BUDGET_YEAR_SALARY", $border, 0, 'R', 0);
				$pdf->Cell($heading_width[8], 7, "$LEVEL_EFFECTIVEDATE", $border, 0, 'C', 0);
//				$pdf->Cell($heading_width[6], 7, "$BUDGET_YEAR_SALARY", $border, 0, 'R', 0);
				$pdf->MultiCell($heading_width[9], 7, "$NEW_POSITION", $border, "C");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6] + $heading_width[7] + $heading_width[8] + $heading_width[9];
				$pdf->y = $start_y;	
				$pdf->Cell($heading_width[10], 7, "$NEW_POSITION_TYPE", $border, 0, 'R', 0);
				$pdf->Cell($heading_width[11], 7, "$NEW_LEVEL_NAME", $border, 0, 'C', 0);
				$pdf->Cell($heading_width[12], 7, "$NEW_POS_NO", $border, 0, 'C', 0);
				$pdf->Cell($heading_width[13], 7, "$CMD_SALARY", $border, 0, 'R', 0);
				$pdf->Cell($heading_width[14], 7, "$CMD_DATE", $border, 0, 'C', 0);
				$pdf->MultiCell($heading_width[15], 7, "$CMD_NOTE1", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6] + $heading_width[7] + $heading_width[8] + $heading_width[9] + $heading_width[10] + $heading_width[11] + $heading_width[12] + $heading_width[13] + $heading_width[14] + $heading_width[15];
				$pdf->y = $start_y;

				//================= Draw Border Line ====================
				$line_start_y = $start_y;		$line_start_x = $start_x;
				$line_end_y = $max_y;		$line_end_x = $start_x;
				$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
					
				for($i=0; $i<=15; $i++){
					$line_start_y = $start_y;		$line_start_x += $heading_width[$i];
					$line_end_y = $max_y;		$line_end_x += $heading_width[$i];
					$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				} // end for
				//====================================================
	
				if($CMD_NOTE2){
					$pdf->x = $start_x;			$pdf->y = $max_y;
					$pdf->MultiCell(array_sum($heading_width), 7, "หมายเหตุ : $CMD_NOTE2", "LR", "L");
					if($pdf->y > $max_y) $max_y = $pdf->y;
					$pdf->x = $start_x + array_sum($heading_width);
					$pdf->y = $start_y;
				} // end if
	
	//			if(($pdf->h - $max_y - 10) < 22){ 
				if(($pdf->h - $max_y) < 55){ 
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
			} // end if
		} // end for				
		
		if($COM_NOTE){
			$border = "";
			$pdf->SetFont($font,'b','',14);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
			$pdf->Cell(15, 7, "หมายเหตุ : ", $border, 0, 'L', 0);
	
			$border = "";
			$pdf->SetFont($font,'',14);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
			$pdf->MultiCell(272, 7, "$COM_NOTE", $border, "L");
		} // end if
	}else{
		$pdf->SetFont($font,'b','',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(287,10,"********** ไม่มีข้อมูล **********",0,1,'C');
	} // end if

	$pdf->close();
	$pdf->Output();

	ini_set("max_execution_time", 30);
?>