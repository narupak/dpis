<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php");

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	$cmd = " select		COM_NO, a.COM_NAME, COM_DATE, COM_NOTE, COM_PER_TYPE, 
									a.COM_TYPE, COM_CONFIRM, COM_LEVEL_SALP, b.COM_DESC
					 from		PER_COMMAND a, PER_COMTYPE b
					 where	COM_ID=$COM_ID and a.COM_TYPE=b.COM_TYPE ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
		
	$COM_NO = trim($data[COM_NO]);
	$COM_NAME = trim($data[COM_NAME]);
	$COM_DATE = $data[COM_DATE];
	$COM_DATE = show_date_format($COM_DATE,3);
	$COM_NOTE = trim($data[COM_NOTE]);
	$COM_PER_TYPE = trim($data[COM_PER_TYPE]);
	$COM_CONFIRM = trim($data[COM_CONFIRM]);
	$COM_LEVEL_SALP = $data[COM_LEVEL_SALP];
		
	$COM_TYPE = trim($data[COM_TYPE]);
	$COM_DESC = trim($data[COM_DESC]);

	if($COM_PER_TYPE == 1){
		$position_table = "PER_POSITION";
		$position_join = "b.POS_ID=c.POS_ID";
		$type_name="ข้าราชการ";
	}elseif($COM_PER_TYPE == 2){
		$position_table = "PER_POS_EMP";
		$position_join = "b.POEM_ID=c.POEM_ID";
		$type_name="ลูกจ้างประจำ";
	}elseif($COM_PER_TYPE == 3){
		$position_table = "PER_POS_EMPSER";
		$position_join = "b.POEMS_ID=c.POEMS_ID";
		$type_name="พนักงานราชการ";
	}elseif($COM_PER_TYPE == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "b.POT_ID=c.POT_ID";
		$type_name="ลูกจ้างชั่วคราว";
	} // end if

	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "";
	$report_title = "บัญชีรายละเอียดการเลื่อนขั้นเงินเดือน$type_name||แนบท้ายคำสั่ง $DEPARTMENT_NAME ที่ $COM_NO ลงวันที่ $COM_DATE";
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
	
	$heading_width[0] = "10";
	$heading_width[1] = "60";
	$heading_width[2] = "62";
	$heading_width[3] = "15";
	$heading_width[4] = "15";
	$heading_width[5] = "25";
	$heading_width[6] = "15";
	$heading_width[7] = "25";
	$heading_width[8] = "60";
	
	function print_header(){
		global $pdf, $heading_width;
		
		$pdf->SetFont($font,'',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
		$pdf->Cell(100,2,"",0,1,'C');

		$pdf->Cell($heading_width[0] ,7,"ลำดับ",'LTR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"ชื่อ",'LTR',0,'C',1);
		$pdf->Cell(($heading_width[2] + $heading_width[3]) ,7,"ตำแหน่งและส่วนราชการ",'LTBR',0,'C',1);
		$pdf->Cell(($heading_width[4] + $heading_width[5]) ,7,"เงินเดือนก่อนเลื่อน",'LTBR',0,'C',1);
		$pdf->Cell(($heading_width[6] + $heading_width[7]) ,7,"ให้ได้รับเงินเดือน",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[8] ,7,"หมายเหตุ",'LTR',1,'C',1);

		$pdf->Cell($heading_width[0] ,7,"ที่",'LBR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"สังกัด/ตำแหน่ง",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[3] ,7,"เลขที่",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[4] ,7,"อันดับ",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,"ขั้น",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[6] ,7,"อันดับ",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[7] ,7,"ขั้น",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[8] ,7,"",'LBR',1,'C',1);
	} // function		

	if($DPISDB=="odbc"){
		$cmd = " select			a.PER_ID, b.PN_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE, b.PER_TYPE, b.LEVEL_NO,
								a.EN_CODE, a.CMD_POSITION, a.CMD_ORG3, a.CMD_ORG4, a.CMD_OLD_SALARY, a.CMD_LEVEL, 
								a.POS_ID, a.POEM_ID, a.POEMS_ID, a.LEVEL_NO as NEW_LEVEL_NO, a.CMD_SALARY,
								a.CMD_DATE, a.CMD_NOTE1, a.CMD_NOTE2, d.ORG_SEQ_NO, e.ORG_SEQ_NO
				   from		(
									(
										(
											PER_COMDTL a 
											inner join PER_PERSONAL b on (a.PER_ID=b.PER_ID)
										) left join $position_table c on ($position_join)
									) left join PER_ORG d on (c.ORG_ID=d.ORG_ID)
								) left join PER_ORG e on (c.ORG_ID_1=e.ORG_ID)
				   where		a.COM_ID=$COM_ID
				   order by 	d.ORG_SEQ_NO, e.ORG_SEQ_NO, IIf(IsNull(a.CMD_POSITION), 0, CLng(LEFT(a.CMD_POSITION, (InStr(a.CMD_POSITION, '\|') - 1)))) ";
	}elseif($DPISDB=="oci8"){
		$cmd = " select		a.PER_ID, b.PN_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE, b.PER_TYPE, b.LEVEL_NO,
							a.EN_CODE, a.CMD_POSITION, a.CMD_ORG3, a.CMD_ORG4, a.CMD_OLD_SALARY, a.CMD_LEVEL,
							a.POS_ID, a.POEM_ID, a.POEMS_ID, a.LEVEL_NO as NEW_LEVEL_NO, a.CMD_SALARY,
							a.CMD_DATE, a.CMD_NOTE1, a.CMD_NOTE2
			   from			PER_COMDTL a, PER_PERSONAL b, $position_table c, PER_ORG d, PER_ORG e
			   where			a.COM_ID=$COM_ID and a.PER_ID=b.PER_ID and $position_join(+) and c.ORG_ID=d.ORG_ID(+) and c.ORG_ID_1=e.ORG_ID(+)
			   order by 		d.ORG_SEQ_NO, e.ORG_SEQ_NO, TO_NUMBER(SUBSTR(a.CMD_POSITION, 1, (INSTR(a.CMD_POSITION, '\|') - 1))) ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			a.PER_ID, b.PN_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE, b.PER_TYPE, b.LEVEL_NO,
								a.EN_CODE, a.CMD_POSITION, a.CMD_ORG3, a.CMD_ORG4, a.CMD_OLD_SALARY, a.CMD_LEVEL, 
								a.POS_ID, a.POEM_ID, a.POEMS_ID, a.LEVEL_NO as NEW_LEVEL_NO, a.CMD_SALARY,
								a.CMD_DATE, a.CMD_NOTE1, a.CMD_NOTE2, d.ORG_SEQ_NO, e.ORG_SEQ_NO
				   from		(
									(
										(
											PER_COMDTL a 
											inner join PER_PERSONAL b on (a.PER_ID=b.PER_ID)
										) left join $position_table c on ($position_join)
									) left join PER_ORG d on (c.ORG_ID=d.ORG_ID)
								) left join PER_ORG e on (c.ORG_ID_1=e.ORG_ID)
				   where		a.COM_ID=$COM_ID
				   order by 	d.ORG_SEQ_NO, e.ORG_SEQ_NO, a.CMD_POSITION ";
	} // end if
	if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
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
/*
		$EDU_TYPE = "%||2||%";
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
*/		
		$CMD_LEVEL = trim($data[CMD_LEVEL]);
		$CMD_POSITION = $data[CMD_POSITION];
		$arr_temp = explode("\|", $CMD_POSITION);
		$CMD_POS_NO = $arr_temp[0];
		$CMD_POSITION = $arr_temp[1];
		$CMD_POSITION_M = $arr_temp[2];
//		$CMD_ORG3 = $data[CMD_ORG3];
		$CMD_OLD_SALARY = $data[CMD_OLD_SALARY];
		$NEW_LEVEL_NO = trim($data[NEW_LEVEL_NO]);
		$CMD_SALARY = $data[CMD_SALARY];
		$CMD_DATE = show_date_format($data[CMD_DATE],$DATE_DISPLAY);
		$CMD_NOTE1 = trim($data[CMD_NOTE1]);
		$CMD_NOTE2 = trim($data[CMD_NOTE2]);

		if($PER_TYPE==1){
			$cmd = " select a.PM_CODE, a.PT_CODE, b.PT_NAME from PER_POSITION a, PER_TYPE b where trim(a.POS_NO)='$CMD_POS_NO' and a.PT_CODE=b.PT_CODE ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$CMD_PT_CODE = trim($data2[PT_CODE]);
			$CMD_PT_NAME = trim($data2[PT_NAME]);
			$CMD_PM_CODE = trim($data2[PM_CODE]);
			
			$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$CMD_PM_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$CMD_PM_NAME = trim($data2[PM_NAME]);

			$CMD_POSITION = (trim($CMD_PM_CODE)?"$CMD_PM_NAME (":"") . (trim($CMD_POSITION)?($CMD_POSITION ." ". level_no_format($CMD_LEVEL) . (($CMD_PT_CODE != "11" && $CMD_LEVEL >= 6)?"$CMD_PT_NAME":"")):"") . (trim($CMD_PM_CODE)?")":"");
/*
			$POS_ID = $data[POS_ID];
			$cmd = "	select		a.POS_NO, b.PL_NAME, c.ORG_NAME, a.PT_CODE, d.PT_NAME, a.ORG_ID_1, a.ORG_ID_2
								from		PER_POSITION a, PER_LINE b, PER_ORG c, PER_TYPE d
								where		a.POS_ID=$POS_ID and a.PL_CODE=b.PL_CODE and a.ORG_ID=c.ORG_ID and a.PT_CODE=d.PT_CODE
						  ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$NEW_POS_NO = trim($data2[POS_NO]);
			$NEW_PL_NAME = trim($data2[PL_NAME]);
			$NEW_ORG_NAME = trim($data2[ORG_NAME]);
			$NEW_PT_CODE = trim($data2[PT_CODE]);
			$NEW_PT_NAME = trim($data2[PT_NAME]);
			$NEW_PL_NAME = trim($NEW_PL_NAME)?($NEW_PL_NAME ." ". level_no_format($NEW_LEVEL_NO) . (($NEW_PT_CODE != "11" && $NEW_LEVEL_NO >= 6)?"$NEW_PT_NAME":"")):"";
*/
		}elseif($PER_TYPE==2){
			$CMD_POSITION = trim($CMD_POSITION)?($CMD_POSITION ." ". level_no_format($CMD_LEVEL)):"";
/*
			$POEM_ID = $data[POEM_ID];
			$cmd = "	select		a.POEM_NO, b.PN_NAME, c.ORG_NAME, a.ORG_ID_1, a.ORG_ID_2
								from		PER_POS_EMP a, PER_POS_NAME b, PER_ORG c
								where		a.POEM_ID=$POEM_ID and a.PN_CODE=b.PN_CODE and a.ORG_ID=c.ORG_ID
						  ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$NEW_POS_NO = trim($data2[POEM_NO]);
			$NEW_PL_NAME = trim($data2[PN_NAME]);
			$NEW_PL_NAME = trim($NEW_PL_NAME)?($NEW_PL_NAME ." ". level_no_format($NEW_LEVEL_NO)):"";
			$NEW_ORG_NAME = trim($data2[ORG_NAME]);
			$NEW_PL_NAME = trim($NEW_PL_NAME)?($NEW_PL_NAME ." ". level_no_format($NEW_LEVEL_NO)):"";
*/
		}elseif($PER_TYPE==3){
			$CMD_POSITION = trim($CMD_POSITION)?($CMD_POSITION ." ". level_no_format($CMD_LEVEL)):"";
/*
			$POEMS_ID = $data[POEMS_ID];
			$cmd = "	select		a.POEMS_NO, b.EP_NAME, c.ORG_NAME, a.ORG_ID_1, a.ORG_ID_2
								from		PER_POS_EMP a, PER_EMPSER_POS_NAME b, PER_ORG c
								where		a.POEMS_ID=$POEMS_ID and a.EP_CODE=b.EP_CODE and a.ORG_ID=c.ORG_ID
						  ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$NEW_POS_NO = trim($data2[POEMS_NO]);
			$NEW_PL_NAME = trim($data2[EP_NAME]);
			$NEW_PL_NAME = trim($NEW_PL_NAME)?($NEW_PL_NAME ." ". level_no_format($NEW_LEVEL_NO)):"";
			$NEW_ORG_NAME = trim($data2[ORG_NAME]);
			$NEW_PL_NAME = trim($NEW_PL_NAME)?($NEW_PL_NAME ." ". level_no_format($NEW_LEVEL_NO)):"";
*/
		} // end if
/*				
		$NEW_ORG_ID_1 = $data2[ORG_ID_1];
		$NEW_ORG_ID_2 = $data2[ORG_ID_2];

		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$NEW_ORG_ID_1 ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$NEW_ORG_NAME_1 = $data2[ORG_NAME];
		
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$NEW_ORG_ID_2 ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$NEW_ORG_NAME_2 = $data2[ORG_NAME];
*/
		if($CMD_ORG3 != trim($data[CMD_ORG3])){
			$CMD_ORG3 = trim($data[CMD_ORG3]);

			$arr_content[$data_count][type] = "ORG";
			$arr_content[$data_count][org_name] = $CMD_ORG3;
//			$arr_content[$data_count][new_org_name] = $NEW_ORG_NAME;

			$data_count++;
		} // end if
		
		if($CMD_ORG4 != trim($data[CMD_ORG4])){
			$CMD_ORG4 = trim($data[CMD_ORG4]);

			$arr_content[$data_count][type] = "ORG_1";
			$arr_content[$data_count][org_name] = $CMD_ORG4;
//			$arr_content[$data_count][new_org_name] = $NEW_ORG_NAME_1;

			$data_count++;
		} // end if
/*
		$cmd = " select 	min(POH_EFFECTIVEDATE) as LEVEL_EFFECTIVEDATE 
						 from 		PER_POSITIONHIS 
						 where 	PER_ID=$PER_ID and trim(LEVEL_NO)='$LEVEL_NO' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$LEVEL_EFFECTIVEDATE = show_date_format($data2[LEVEL_EFFECTIVEDATE], $DATE_DISPLAY);
		
		$cmd = " select 	SAH_SALARY 
						 from 		PER_SALARYHIS 
						 where 	PER_ID=$PER_ID 
						 				and SAH_EFFECTIVEDATE < '". ($CMD_BUDGET_YEAR - 543) ."-10-01'
						 				and SAH_EFFECTIVEDATE >= '". ($CMD_BUDGET_YEAR - 543 - 1) ."-10-01'
						 order by SAH_EFFECTIVEDATE desc ";
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
*/
		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][order] = $data_row;
		$arr_content[$data_count][name] = $PN_NAME . $PER_NAME ." ". $PER_SURNAME ."\n".card_no_format($PER_CARDNO,$CARD_NO_DISPLAY);
//		$arr_content[$data_count][educate] = $EN_NAME ."\n". ($EM_NAME?"$EM_NAME":"") ."\n". ($INS_NAME?"$INS_NAME":"");
		
		$arr_content[$data_count][cmd_pos_no] = $CMD_POS_NO;
//		$arr_content[$data_count][cmd_position] = $CMD_POSITION ."\n". ($CMD_ORG3?"$CMD_ORG3":"");
		$arr_content[$data_count][cmd_position] = (trim($CMD_POSITION_M)?"$CMD_POSITION_M\n(":"") . $CMD_POSITION . (trim($CMD_POSITION_M)?")":"");
		$arr_content[$data_count][cmd_level] = "ท." . level_no_format($CMD_LEVEL);
		$arr_content[$data_count][cmd_old_salary] = $CMD_OLD_SALARY?number_format($CMD_OLD_SALARY):"-";
		
//		$arr_content[$data_count][level_effectivedate] = $LEVEL_EFFECTIVEDATE;
//		$arr_content[$data_count][budget_year_salary] = $BUDGET_YEAR_SALARY?number_format($BUDGET_YEAR_SALARY):"-";
//		$arr_content[$data_count][prev_budget_year_salary] = $PREV_BUDGET_YEAR_SALARY?number_format($PREV_BUDGET_YEAR_SALARY):"-";

//		$arr_content[$data_count][new_position] = $NEW_PL_NAME ."\n". ($NEW_ORG_NAME?"$NEW_ORG_NAME":"");
//		$arr_content[$data_count][new_position] = $NEW_PL_NAME;
//		$arr_content[$data_count][new_pos_no] = $NEW_POS_NO;
		$arr_content[$data_count][cmd_salary] = $CMD_SALARY?number_format($CMD_SALARY):"-";
		
//		$arr_content[$data_count][cmd_date] = $CMD_DATE;
//		$arr_content[$data_count][cmd_note] = $CMD_NOTE1 . ($CMD_NOTE2?("\n".$CMD_NOTE2):"");
		$arr_content[$data_count][cmd_note1] = $CMD_NOTE1;
		$arr_content[$data_count][cmd_note2] = $CMD_NOTE2;
		
		$data_count++;
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
	if($count_data){
		$pdf->AutoPageBreak = false;
		print_header();

		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$CONTENT_TYPE = $arr_content[$data_count][type];
			$ORDER = $arr_content[$data_count][order];
			$NAME = $arr_content[$data_count][name];
//			$EDUCATE = $arr_content[$data_count][educate];
			$CMD_POS_NO = $arr_content[$data_count][cmd_pos_no];
			$CMD_LEVEL = $arr_content[$data_count][cmd_level];
			$CMD_POSITION = $arr_content[$data_count][cmd_position];
//			$CMD_ORG3 = $arr_content[$data_count][cmd_org3];

//			$LEVEL_EFFECTIVEDATE = $arr_content[$data_count][level_effectivedate];
//			$BUDGET_YEAR_SALARY = $arr_content[$data_count][budget_year_salary];
//			$PREV_BUDGET_YEAR_SALARY = $arr_content[$data_count][prev_budget_year_salary];

			$CMD_OLD_SALARY = $arr_content[$data_count][cmd_old_salary];
//			$NEW_POSITION = $arr_content[$data_count][new_position];
//			$NEW_POS_NO = $arr_content[$data_count][new_pos_no];
			$CMD_SALARY = $arr_content[$data_count][cmd_salary];
//			$CMD_DATE = $arr_content[$data_count][cmd_date];
//			$CMD_NOTE = $arr_content[$data_count][cmd_note];
			$CMD_NOTE1 = $arr_content[$data_count][cmd_note1];
			$CMD_NOTE2 = $arr_content[$data_count][cmd_note2];
			
			if($CONTENT_TYPE=="ORG" || $CONTENT_TYPE=="ORG_1" || $CONTENT_TYPE=="ORG_2"){
				$ORG_NAME = $arr_content[$data_count][org_name];
//				$NEW_ORG_NAME = $arr_content[$data_count][new_org_name];
				
				$border = "";
				if($CONTENT_TYPE == "ORG") $pdf->SetFont($fontb,'',14);
				else $pdf->SetFont($font,'',14);
				$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
				
				$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
				
				$pdf->Cell(($heading_width[0] + $heading_width[1]), 7, "", $border, 0, 'L', 0);
				$pdf->MultiCell(($heading_width[2] + $heading_width[3]), 7, "$ORG_NAME", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3];
				$pdf->y = $start_y;
				$pdf->Cell($heading_width[4], 7, "", $border, 0, 'L', 0);
				$pdf->Cell($heading_width[5], 7, "", $border, 0, 'L', 0);
				$pdf->Cell($heading_width[6], 7, "", $border, 0, 'L', 0);
				$pdf->Cell($heading_width[7], 7, "", $border, 0, 'L', 0);
				$pdf->Cell($heading_width[8], 7, "", $border, 0, 'L', 0);

				//================= Draw Border Line ====================
				$line_start_y = $start_y;		$line_start_x = $start_x;
				$line_end_y = $max_y;		$line_end_x = $start_x;
				$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
					
				for($i=0; $i<=8; $i++){
					if($i < 2){
						$line_start_y = $start_y;		$line_start_x += $heading_width[$i];
						$line_end_y = $max_y;		$line_end_x += $heading_width[$i];
					}elseif($i==2){
						$line_start_y = $start_y;		$line_start_x += ($heading_width[2] + $heading_width[3]);
						$line_end_y = $max_y;		$line_end_x += ($heading_width[2] + $heading_width[3]);
					}elseif($i > 3){
						$line_start_y = $start_y;		$line_start_x += $heading_width[$i];
						$line_end_y = $max_y;		$line_end_x += $heading_width[$i];
					} // end if

					$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				} // end for
				//====================================================

				if(($pdf->h - $max_y) < 21){ 
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
				$pdf->MultiCell($heading_width[2], 7, "$CMD_POSITION", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2];
				$pdf->y = $start_y;
				$pdf->Cell($heading_width[3], 7, "$CMD_POS_NO", $border, 0, 'C', 0);
				$pdf->Cell($heading_width[4], 7, "$CMD_LEVEL", $border, 0, 'C', 0);
				$pdf->Cell($heading_width[5], 7, "$CMD_OLD_SALARY", $border, 0, 'R', 0);
				$pdf->Cell($heading_width[6], 7, "$CMD_LEVEL", $border, 0, 'C', 0);
				$pdf->Cell($heading_width[7], 7, "$CMD_SALARY", $border, 0, 'R', 0);
				$pdf->MultiCell($heading_width[8], 7, "$CMD_NOTE1", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6] + $heading_width[7] + $heading_width[8];
				$pdf->y = $start_y;
	
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
	
				if($CMD_NOTE2){
					$pdf->x = $start_x;			$pdf->y = $max_y;
					$pdf->MultiCell(array_sum($heading_width), 7, "หมายเหตุ : $CMD_NOTE2", "LR", "L");
					if($pdf->y > $max_y) $max_y = $pdf->y;
					$pdf->x = $start_x + array_sum($heading_width);
					$pdf->y = $start_y;
				} // end if
	
	//			if(($pdf->h - $max_y - 10) < 22){ 
				if(($pdf->h - $max_y) < 21){ 
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
			$pdf->SetFont($fontb,'',14);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
			$pdf->Cell(15, 7, "หมายเหตุ : ", $border, 0, 'L', 0);
	
			$border = "";
			$pdf->SetFont($font,'',14);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
			$pdf->MultiCell(272, 7, "$COM_NOTE", $border, "L");
		} // end if
	}else{
		$pdf->SetFont($fontb,'',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(287,10,"********** ไม่มีข้อมูล **********",0,1,'C');
	} // end if

	$pdf->close();
	$pdf->Output();

	ini_set("max_execution_time", 30);
?>