<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php");

	ini_set("max_execution_time", 1800);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$cmd = " select		COM_NO, a.COM_NAME, COM_DATE, COM_NOTE, COM_PER_TYPE, 
									a.COM_TYPE, COM_CONFIRM, b.COM_DESC
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
		
	$COM_TYPE = trim($data[COM_TYPE]);

	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "";
	if($COM_TYPE=="0101"){ 
		$report_title = "�ѭ����������´��ú�èؼ���ͺ�觢ѹ��Ṻ���¤���� $DEPARTMENT_NAME ��� $COM_NO ŧ�ѹ��� $COM_DATE";
	}elseif(in_array($COM_TYPE, array("0102", "0103", "0105", "0106", "0107", "0108", "0504", "0604", "0704", "0803"))){
		$report_title = "�ѭ����������´��ú�èؼ�����Ѻ�Ѵ���͡Ṻ���¤���� $DEPARTMENT_NAME ��� $COM_NO ŧ�ѹ��� $COM_DATE";
	}else{
		$report_title = "�ѭ����������´����Ѻ�͹$PERSON_TYPE[$COM_PER_TYPE]Ṻ���¤���� $DEPARTMENT_NAME ��� $COM_NO ŧ�ѹ��� $COM_DATE";
	} // end if
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
	
	if($COM_TYPE=="0101"){
		$heading_width[0] = "10";
		$heading_width[1] = "47";
		$heading_width[2] = "30";
		$heading_width[3] = "30";
		$heading_width[4] = "12";
		$heading_width[5] = "33";
		$heading_width[6] = "30";
		$heading_width[7] = "13";
		$heading_width[8] = "17";
		$heading_width[9] = "20";
		$heading_width[10] = "45";
	}elseif(in_array($COM_TYPE, array("0102", "0103", "0105", "0106", "0107", "0108", "0504", "0604", "0704", "0803"))){
		$heading_width[0] = "10";
		$heading_width[1] = "57";
		$heading_width[2] = "55";
		$heading_width[3] = "55";
		$heading_width[4] = "13";
		$heading_width[5] = "17";
		$heading_width[6] = "20";
		$heading_width[7] = "60";
	}else{
		$heading_width[0] = "10";
		$heading_width[1] = "47";
		$heading_width[2] = "33";
		$heading_width[3] = "30";
		$heading_width[4] = "17";
		$heading_width[5] = "40";
		$heading_width[6] = "13";
		$heading_width[7] = "17";
		$heading_width[8] = "20";
		$heading_width[9] = "60";
	} // end if
	
	function print_header(){
		global $pdf, $heading_width;
		global $COM_TYPE;
		
		$pdf->SetFont($font,'',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
		$pdf->Cell(100,2,"",0,1,'C');

		if($COM_TYPE=="0101"){
			$pdf->Cell($heading_width[0] ,7,"�ӴѺ",'LTR',0,'C',1);
			$pdf->Cell($heading_width[1] ,7,"���� (�ѹ��͹�� �Դ)",'LTR',0,'C',1);
			$pdf->Cell($heading_width[2] ,7,"�ز�/�Ң�/ʶҹ�֡��",'LTR',0,'C',1);
			$pdf->Cell(($heading_width[3] + $heading_width[4] + $heading_width[5]) ,7,"�ͺ�觢ѹ��",'LTBR',0,'C',1);
			$pdf->Cell(($heading_width[6] + $heading_width[7] + $heading_width[8]) ,7,"���˹觷���è�",'LTBR',0,'C',1);
			$pdf->Cell($heading_width[9] ,7,"������ѹ���",'LTR',0,'C',1);
			$pdf->Cell($heading_width[10] ,7,"�����˵�",'LTR',1,'C',1);

			$pdf->Cell($heading_width[0] ,7,"���",'LBR',0,'C',1);
			$pdf->Cell($heading_width[1] ,7,"(�Ţ��Шӵ�ǻ�ЪҪ�)",'LBR',0,'C',1);
			$pdf->Cell($heading_width[2] ,7,"",'LBR',0,'C',1);
			$pdf->Cell($heading_width[3] ,7,"���˹�",'LTBR',0,'C',1);
			$pdf->Cell($heading_width[4] ,7,"�ӴѺ���",'LTBR',0,'C',1);
			$pdf->Cell($heading_width[5] ,7,"��С�ȼš���ͺ�ͧ",'LTBR',0,'C',1);
			$pdf->Cell($heading_width[6] ,7,"���˹�/�ѧ�Ѵ",'LTBR',0,'C',1);
			$pdf->Cell($heading_width[7] ,7,"�Ţ���",'LTBR',0,'C',1);
			$pdf->Cell($heading_width[8] ,7,"�Թ��͹",'LTBR',0,'C',1);
			$pdf->Cell($heading_width[9] ,7,"",'LBR',0,'C',1);
			$pdf->Cell($heading_width[10] ,7,"",'LBR',1,'C',1);
		}elseif(in_array($COM_TYPE, array("0102", "0103", "0105", "0106", "0107", "0108", "0504", "0604", "0704", "0803"))){
			$pdf->Cell($heading_width[0] ,7,"�ӴѺ",'LTBR',0,'C',1);
			$pdf->Cell($heading_width[1] ,7,"���� (�ѹ��͹�� �Դ)",'LTR',0,'C',1);
			$pdf->Cell($heading_width[2] ,7,"�ز�/�Ң�/ʶҹ�֡��",'LTR',0,'C',1);
			$pdf->Cell(($heading_width[3] + $heading_width[4] + $heading_width[5]) ,7,"���˹觷���è�",'LTBR',0,'C',1);
			$pdf->Cell($heading_width[6] ,7,"������ѹ���",'LTBR',0,'C',1);
			$pdf->Cell($heading_width[7] ,7,"�����˵�",'LTBR',1,'C',1);

			$pdf->Cell($heading_width[0] ,7,"���",'LBR',0,'C',1);
			$pdf->Cell($heading_width[1] ,7,"(�Ţ��Шӵ�ǻ�ЪҪ�)",'LBR',0,'C',1);
			$pdf->Cell($heading_width[2] ,7,"",'LBR',0,'C',1);
			$pdf->Cell($heading_width[3] ,7,"���˹�/�ѧ�Ѵ",'LTBR',0,'C',1);
			$pdf->Cell($heading_width[4] ,7,"�Ţ���",'LTBR',0,'C',1);
			$pdf->Cell($heading_width[5] ,7,"�Թ��͹",'LTBR',0,'C',1);
			$pdf->Cell($heading_width[6] ,7,"",'LBR',0,'C',1);
			$pdf->Cell($heading_width[7] ,7,"",'LBR',1,'C',1);
		}else{
			$pdf->Cell($heading_width[0] ,7,"�ӴѺ",'LTBR',0,'C',1);
			$pdf->Cell($heading_width[1] ,7,"����",'LTR',0,'C',1);
			$pdf->Cell($heading_width[2] ,7,"�ز�/�Ң�/ʶҹ�֡��",'LTR',0,'C',1);
			$pdf->Cell(($heading_width[3] + $heading_width[4]) ,7,"���˹������ǹ�Ҫ������",'LTBR',0,'C',1);
			$pdf->Cell(($heading_width[5] + $heading_width[6] + $heading_width[7]) ,7,"���˹觷���Ѻ�͹",'LTBR',0,'C',1);
			$pdf->Cell($heading_width[8] ,7,"������ѹ���",'LTBR',0,'C',1);
			$pdf->Cell($heading_width[9] ,7,"�����˵�",'LTBR',1,'C',1);

			$pdf->Cell($heading_width[0] ,7,"���",'LBR',0,'C',1);
			$pdf->Cell($heading_width[1] ,7,"(�Ţ��Шӵ�ǻ�ЪҪ�)",'LBR',0,'C',1);
			$pdf->Cell($heading_width[2] ,7,"",'LBR',0,'C',1);
			$pdf->Cell($heading_width[3] ,7,"���˹�",'LTBR',0,'C',1);
			$pdf->Cell($heading_width[4] ,7,"�Թ��͹",'LTBR',0,'C',1);
			$pdf->Cell($heading_width[5] ,7,"���˹�/�ѧ�Ѵ",'LTBR',0,'C',1);
			$pdf->Cell($heading_width[6] ,7,"�Ţ���",'LTBR',0,'C',1);
			$pdf->Cell($heading_width[7] ,7,"�Թ��͹",'LTBR',0,'C',1);
			$pdf->Cell($heading_width[8] ,7,"",'LBR',0,'C',1);
			$pdf->Cell($heading_width[9] ,7,"",'LBR',1,'C',1);
		} // end if
	} // function		
		
	if($DPISDB=="odbc"){
		$cmd = " select			a.PER_ID, b.PN_CODE as PRENAME_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE,b.PER_RETIREDATE, b.PER_TYPE, b.LEVEL_NO,
											a.EN_CODE, a.PL_CODE, a.PN_CODE, a.EP_CODE, a.TP_CODE, a.CMD_AC_NO, a.CMD_ACCOUNT,
											a.CMD_POSITION, a.CMD_ORG3, a.CMD_OLD_SALARY, a.CMD_LEVEL, 
											a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID, a.LEVEL_NO as NEW_LEVEL_NO, a.CMD_SALARY,
											a.CMD_DATE, a.CMD_NOTE1, a.CMD_NOTE2
						 from			PER_COMDTL a, PER_PERSONAL b
						 where		a.COM_ID=$COM_ID and a.PER_ID=b.PER_ID 
						 order by 	a.CMD_SEQ
					  ";	
	}elseif($DPISDB=="oci8"){
		$cmd = " select			a.PER_ID, b.PN_CODE as PRENAME_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE,b.PER_RETIREDATE, b.PER_TYPE,
											a.EN_CODE, a.PL_CODE, a.PN_CODE, a.EP_CODE, a.TP_CODE, a.CMD_AC_NO, a.CMD_ACCOUNT,
											a.CMD_POSITION, a.CMD_ORG3, a.CMD_OLD_SALARY, a.CMD_LEVEL, 
											a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID, a.LEVEL_NO as NEW_LEVEL_NO, a.CMD_SALARY,
											a.CMD_DATE, a.CMD_NOTE1, a.CMD_NOTE2
						 from			PER_COMDTL a, PER_PERSONAL b
						 where		a.COM_ID=$COM_ID and a.PER_ID=b.PER_ID 
						 order by 	a.CMD_SEQ
					  ";	
	}elseif($DPISDB=="mysql"){
		$cmd = " select			a.PER_ID, b.PN_CODE as PRENAME_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE,b.PER_RETIREDATE, b.PER_TYPE, b.LEVEL_NO,
											a.EN_CODE, a.PL_CODE, a.PN_CODE, a.EP_CODE, a.TP_CODE, a.CMD_AC_NO, a.CMD_ACCOUNT,
											a.CMD_POSITION, a.CMD_ORG3, a.CMD_OLD_SALARY, a.CMD_LEVEL, 
											a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID, a.LEVEL_NO as NEW__NO, a.CMD_SALARY,
											a.CMD_DATE, a.CMD_NOTE1, a.CMD_NOTE2
						 from			PER_COMDTL a, PER_PERSONAL b
						 where		a.COM_ID=$COM_ID and a.PER_ID=b.PER_ID 
						 order by 	a.CMD_SEQ
					  ";	
	} // end if
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data_count = $data_row = 0;
	while($data = $db_dpis->get_array()){
		$data_row++;

		$PER_ID = $data[PER_ID];
		$PRENAME_CODE = trim($data[PRENAME_CODE]);
		$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='$PRENAME_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$PN_NAME = $data2[PN_NAME];		
		$PER_NAME = $data[PER_NAME];
		$PER_SURNAME = $data[PER_SURNAME];
		$PER_CARDNO = $data[PER_CARDNO];
		$PER_BIRTHDATE = show_date_format($data[PER_BIRTHDATE],$DATE_DISPLAY);
		$PER_RETIREDATE = show_date_format($data[PER_RETIREDATE],$DATE_DISPLAY);
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
		$CMD_ACC_NO = trim($data[CMD_AC_NO]);
		$CMD_ACCOUNT = trim($data[CMD_ACCOUNT]);
		$CMD_POSITION = $data[CMD_POSITION];
		$CMD_ORG3 = $data[CMD_ORG3];
		$CMD_OLD_SALARY = $data[CMD_OLD_SALARY];
		$NEW_LEVEL_NO = trim($data[NEW_LEVEL_NO]);
		$CMD_SALARY = $data[CMD_SALARY];
		$CMD_DATE = show_date_format($data[CMD_DATE],$DATE_DISPLAY);
		$CMD_NOTE1 = trim($data[CMD_NOTE1]);
		$CMD_NOTE2 = trim($data[CMD_NOTE2]);

		if($PER_TYPE==1){
			$PL_CODE = trim($data[PL_CODE]);
			$cmd = " select PL_NAME from PER_LINE where trim(PL_CODE)='$PL_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PL_NAME = $data2[PL_NAME];
			
			$cmd = " select a.PM_CODE, a.PT_CODE, b.PT_NAME from PER_POSITION a, PER_TYPE b where trim(a.POS_NO)='$CMD_POS_NO' and a.PT_CODE=b.PT_CODE and a.DEPARTMENT_ID = $DEPARTMENT_ID ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$CMD_PT_CODE = trim($data2[PT_CODE]);
			$CMD_PT_NAME = trim($data2[PT_NAME]);
			$CMD_PM_CODE = trim($data2[PM_CODE]);
			
			$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$CMD_PM_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$CMD_PM_NAME = trim($data2[PM_NAME]);

			$CMD_POSITION = (trim($CMD_PM_CODE)?"$CMD_PM_NAME (":"") . (trim($CMD_POSITION)?($CMD_POSITION ." ". level_no_format($CMD_LEVEL) . (($CMD_PT_NAME != "�����" && $CMD_LEVEL >= 6)?"$CMD_PT_NAME":"")):"") . (trim($CMD_PM_CODE)?")":"");

			$POS_ID = $data[POS_ID];
			$cmd = "	select		a.POS_NO, b.PL_NAME, c.ORG_NAME, a.PT_CODE, d.PT_NAME, a.ORG_ID_1, a.ORG_ID_2, a.PM_CODE
						from		PER_POSITION a, PER_LINE b, PER_ORG c, PER_TYPE d
						where		a.POS_ID=$POS_ID and a.PL_CODE=b.PL_CODE and a.ORG_ID=c.ORG_ID and a.PT_CODE=d.PT_CODE
					";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$NEW_POS_NO = trim($data2[POS_NO]);
			$NEW_PL_NAME = trim($data2[PL_NAME]);
			$NEW_ORG_NAME = trim($data2[ORG_NAME]);
			$NEW_PT_CODE = trim($data2[PT_CODE]);
			$NEW_PT_NAME = trim($data2[PT_NAME]);
			$NEW_PM_CODE = trim($data2[PM_CODE]);
			
			$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$NEW_PM_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$NEW_PM_NAME = trim($data2[PM_NAME]);
			
			$NEW_PL_NAME = (trim($NEW_PM_CODE)?"$NEW_PM_NAME (":"") . (trim($NEW_PL_NAME)?($NEW_PL_NAME ." ". level_no_format($NEW_LEVEL_NO) . (($NEW_PT_NAME != "�����" && $NEW_LEVEL_NO >= 6)?"$NEW_PT_NAME":"")):"") . (trim($NEW_PM_CODE)?")":"");
		}elseif($PER_TYPE==2){
			$PN_CODE = trim($data[PN_CODE]);
			$cmd = " select PN_NAME from PER_POS_NAME where trim(PN_CODE)='$PN_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PL_NAME = $data2[PN_NAME];

			$CMD_POSITION = trim($CMD_POSITION)?($CMD_POSITION ." ". level_no_format($CMD_LEVEL)):"";

			$POEM_ID = $data[POEM_ID];
			$cmd = "	select		a.POEM_NO, b.PN_NAME, c.ORG_NAME
								from		PER_POS_EMP a, PER_POS_NAME b, PER_ORG c
								where		a.POEM_ID=$POEM_ID and a.PN_CODE=b.PN_CODE and a.ORG_ID=c.ORG_ID
						  ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$NEW_POS_NO = trim($data2[POEM_NO]);
			$NEW_PL_NAME = trim($data2[PN_NAME]);
			$NEW_ORG_NAME = trim($data2[ORG_NAME]);
			$NEW_PL_NAME = trim($NEW_PL_NAME)?($NEW_PL_NAME ." ". level_no_format($NEW_LEVEL_NO)):"";
		}elseif($PER_TYPE==3){
			$EP_CODE = trim($data[EP_CODE]);
			$cmd = " select EP_NAME from PER_EMPSER_POS_NAME where trim(EP_CODE)='$EP_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PL_NAME = $data2[EP_NAME];

			$CMD_POSITION = trim($CMD_POSITION)?($CMD_POSITION ." ". level_no_format($CMD_LEVEL)):"";

			$POEMS_ID = $data[POEMS_ID];
			$cmd = "	select		a.POEMS_NO, b.EP_NAME, c.ORG_NAME
								from		PER_POS_EMP a, PER_EMPSER_POS_NAME b, PER_ORG c
								where		a.POEMS_ID=$POEMS_ID and a.EP_CODE=b.EP_CODE and a.ORG_ID=c.ORG_ID
						  ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$NEW_POS_NO = trim($data2[POEMS_NO]);
			$NEW_PL_NAME = trim($data2[EP_NAME]);
			$NEW_ORG_NAME = trim($data2[ORG_NAME]);
			$NEW_PL_NAME = trim($NEW_PL_NAME)?($NEW_PL_NAME ." ". level_no_format($NEW_LEVEL_NO)):"";
		}elseif($PER_TYPE==4){
			$TP_CODE = trim($data[TP_CODE]);
			$cmd = " select TP_NAME from PER_TEMP_POS_NAME where trim(TP_CODE)='$TP_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PL_NAME = $data2[TP_NAME];

			$CMD_POSITION = trim($CMD_POSITION)?($CMD_POSITION ." ". level_no_format($CMD_LEVEL)):"";

			$POT_ID = $data[POT_ID];
			$cmd = "	select		a.POT_NO, b.TP_NAME, c.ORG_NAME
								from		PER_POS_TEMP a, PER_TEMP_POS_NAME b, PER_ORG c
								where		a.POT_ID=$POT_ID and a.TP_CODE=b.TP_CODE and a.ORG_ID=c.ORG_ID
						  ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$NEW_POS_NO = trim($data2[POT_NO]);
			$NEW_PL_NAME = trim($data2[TP_NAME]);
			$NEW_ORG_NAME = trim($data2[ORG_NAME]);
			$NEW_PL_NAME = trim($NEW_PL_NAME)?($NEW_PL_NAME ." ". level_no_format($NEW_LEVEL_NO)):"";
		} // end if
		
		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][order] = $data_row;
		if(in_array($COM_TYPE, array("0101", "0102", "0103", "0105", "0106", "0107", "0108", "0504", "0604", "0704", "0803")))
			$arr_content[$data_count][name] = $PN_NAME . $PER_NAME ." ". $PER_SURNAME ."\n". $PER_BIRTHDATE ."\n".card_no_format($PER_CARDNO,$CARD_NO_DISPLAY);
		else
			$arr_content[$data_count][name] = $PN_NAME . $PER_NAME ." ". $PER_SURNAME ."\n".card_no_format($PER_CARDNO,$CARD_NO_DISPLAY);
		$arr_content[$data_count][educate] = $EN_NAME . "\n".($EM_NAME?"$EM_NAME":"") ."\n".($INS_NAME?"$INS_NAME":"");
		$arr_content[$data_count][position] = $PL_NAME;
		$arr_content[$data_count][cmd_acc_no] = $CMD_ACC_NO;
		$arr_content[$data_count][cmd_account] = $CMD_ACCOUNT;
		
		$arr_content[$data_count][cmd_position] = $CMD_POSITION;
		$arr_content[$data_count][cmd_org3] = $CMD_ORG3;
		$arr_content[$data_count][cmd_old_salary] = $CMD_OLD_SALARY?number_format($CMD_OLD_SALARY):"-";
		
		$arr_content[$data_count][new_position] = $NEW_PL_NAME . "\n".($NEW_ORG_NAME?"$NEW_ORG_NAME":"");
		$arr_content[$data_count][new_pos_no] = $NEW_POS_NO;
		$arr_content[$data_count][cmd_salary] = $CMD_SALARY?number_format($CMD_SALARY):"-";
		
		$arr_content[$data_count][cmd_date] = $CMD_DATE;
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
			$EDUCATE = $arr_content[$data_count][educate];
			$POSITION = $arr_content[$data_count][position];
			$CMD_ACC_NO = $arr_content[$data_count][cmd_acc_no];
			$CMD_ACCOUNT = $arr_content[$data_count][cmd_account];
			$CMD_POSITION = $arr_content[$data_count][cmd_position];
			$CMD_ORG3 = $arr_content[$data_count][cmd_org3];
			$CMD_OLD_SALARY = $arr_content[$data_count][cmd_old_salary];
			$NEW_POSITION = $arr_content[$data_count][new_position];
			$NEW_POS_NO = $arr_content[$data_count][new_pos_no];
			$CMD_SALARY = $arr_content[$data_count][cmd_salary];
			$CMD_DATE = $arr_content[$data_count][cmd_date];
//			$CMD_NOTE = $arr_content[$data_count][cmd_note];
			$CMD_NOTE1 = $arr_content[$data_count][cmd_note1];
			$CMD_NOTE2 = $arr_content[$data_count][cmd_note2];
			
			$border = "";
			$pdf->SetFont($font,'',14);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

			$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
			
			if($COM_TYPE=="0101"){
				$pdf->Cell($heading_width[0], 7, "$ORDER", $border, 0, 'C', 0);
				$pdf->MultiCell($heading_width[1], 7, "$NAME", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1];
				$pdf->y = $start_y;
				$pdf->MultiCell($heading_width[2], 7, "$EDUCATE", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2];
				$pdf->y = $start_y;
				$pdf->MultiCell($heading_width[3], 7, "$POSITION", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3];
				$pdf->y = $start_y;
				$pdf->Cell($heading_width[4], 7, "$CMD_ACC_NO", $border, 0, 'C', 0);
				$pdf->MultiCell($heading_width[5], 7, "$CMD_ACCOUNT", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5];
				$pdf->y = $start_y;
				$pdf->MultiCell($heading_width[6], 7, "$NEW_POSITION", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6];
				$pdf->y = $start_y;
				$pdf->Cell($heading_width[7], 7, "$NEW_POS_NO", $border, 0, 'C', 0);
				$pdf->Cell($heading_width[8], 7, "$CMD_SALARY", $border, 0, 'R', 0);
				$pdf->Cell($heading_width[9], 7, "$CMD_DATE", $border, 0, 'C', 0);
//				$pdf->MultiCell($heading_width[10], 7, "$CMD_NOTE", $border, "L");
//				if($pdf->y > $max_y) $max_y = $pdf->y;
//				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6] + $heading_width[7] + $heading_width[8] + $heading_width[9] + $heading_width[10];
//				$pdf->y = $start_y;
				$pdf->MultiCell($heading_width[10], 7, "$CMD_NOTE1", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6] + $heading_width[7] + $heading_width[8] + $heading_width[9] + $heading_width[10];
				$pdf->y = $start_y;

				//================= Draw Border Line ====================
				$line_start_y = $start_y;		$line_start_x = $start_x;
				$line_end_y = $max_y;		$line_end_x = $start_x;
				$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				
				for($i=0; $i<=10; $i++){
					$line_start_y = $start_y;		$line_start_x += $heading_width[$i];
					$line_end_y = $max_y;		$line_end_x += $heading_width[$i];
					$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				} // end for
				//====================================================
				
				if($CMD_NOTE2){
					$pdf->x = $start_x;			$pdf->y = $max_y;
					$pdf->MultiCell(array_sum($heading_width), 7, "�����˵� : $CMD_NOTE2", "LR", "L");
					if($pdf->y > $max_y) $max_y = $pdf->y;
					$pdf->x = $start_x + array_sum($heading_width);
					$pdf->y = $start_y;
				} // end if
			}elseif(in_array($COM_TYPE, array("0102", "0103", "0105", "0106", "0107", "0108", "0504", "0604", "0704", "0803"))){
				$pdf->Cell($heading_width[0], 7, "$ORDER", $border, 0, 'C', 0);
				$pdf->MultiCell($heading_width[1], 7, "$NAME", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1];
				$pdf->y = $start_y;
				$pdf->MultiCell($heading_width[2], 7, "$EDUCATE", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2];
				$pdf->y = $start_y;
				$pdf->MultiCell($heading_width[3], 7, "$NEW_POSITION", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3];
				$pdf->y = $start_y;
				$pdf->Cell($heading_width[4], 7, "$NEW_POS_NO", $border, 0, 'C', 0);
				$pdf->Cell($heading_width[5], 7, "$CMD_SALARY", $border, 0, 'R', 0);
				$pdf->Cell($heading_width[6], 7, "$CMD_DATE", $border, 0, 'C', 0);
//				$pdf->MultiCell($heading_width[7], 7, "$CMD_NOTE", $border, "L");
//				if($pdf->y > $max_y) $max_y = $pdf->y;
//				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6] + $heading_width[7];
//				$pdf->y = $start_y;
				$pdf->MultiCell($heading_width[7], 7, "$CMD_NOTE1", $border, "L");
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

				if($CMD_NOTE2){
					$pdf->x = $start_x;			$pdf->y = $max_y;
					$pdf->MultiCell(array_sum($heading_width), 7, "�����˵� : $CMD_NOTE2", "LR", "L");
					if($pdf->y > $max_y) $max_y = $pdf->y;
					$pdf->x = $start_x + array_sum($heading_width);
					$pdf->y = $start_y;
				} // end if
			}else{
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
				$pdf->Cell($heading_width[4], 7, "$CMD_OLD_SALARY", $border, 0, 'R', 0);
				$pdf->MultiCell($heading_width[5], 7, "$NEW_POSITION", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5];
				$pdf->y = $start_y;
				$pdf->Cell($heading_width[6], 7, "$NEW_POS_NO", $border, 0, 'C', 0);
				$pdf->Cell($heading_width[7], 7, "$CMD_SALARY", $border, 0, 'R', 0);
				$pdf->Cell($heading_width[8], 7, "$CMD_DATE", $border, 0, 'C', 0);
//				$pdf->MultiCell($heading_width[9], 7, "$CMD_NOTE", $border, "L");
//				if($pdf->y > $max_y) $max_y = $pdf->y;
//				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6] + $heading_width[7] + $heading_width[8] + $heading_width[9];
//				$pdf->y = $start_y;
				$pdf->MultiCell($heading_width[9], 7, "$CMD_NOTE1", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6] + $heading_width[7] + $heading_width[8] + $heading_width[9];
				$pdf->y = $start_y;

				//================= Draw Border Line ====================
				$line_start_y = $start_y;		$line_start_x = $start_x;
				$line_end_y = $max_y;		$line_end_x = $start_x;
				$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				
				for($i=0; $i<=9; $i++){
					$line_start_y = $start_y;		$line_start_x += $heading_width[$i];
					$line_end_y = $max_y;		$line_end_x += $heading_width[$i];
					$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				} // end for
				//====================================================

				if($CMD_NOTE2){
					$pdf->x = $start_x;			$pdf->y = $max_y;
					$pdf->MultiCell(array_sum($heading_width), 7, "�����˵� : $CMD_NOTE2", "LR", "L");
					if($pdf->y > $max_y) $max_y = $pdf->y;
					$pdf->x = $start_x + array_sum($heading_width);
					$pdf->y = $start_y;
				} // end if
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
		} // end for
		
		if($COM_NOTE){
			$border = "";
			$pdf->SetFont($fontb,'',14);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
			$pdf->Cell(15, 7, "�����˵� : ", $border, 0, 'L', 0);
	
			$border = "";
			$pdf->SetFont($font,'',14);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
			$pdf->MultiCell(272, 7, "$COM_NOTE", $border, "L");
		} // end if
	}else{
		$pdf->SetFont($fontb,'',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(287,10,"********** ����բ����� **********",0,1,'C');
	} // end if

	$pdf->close();
	$pdf->Output();

	ini_set("max_execution_time", 30);
?>