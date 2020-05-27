<?
	include("../../php_scripts/connect_database.php");
	include("../php_scripts/pdf_wordarray_thaicut.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	include("../php_scripts/load_per_control.php");

	if ($FLAG_RTF) {
		include ("rtf_setvar.php");	// ��˹�����ä���� set_of_colors
		require("../../RTF/rtf_class.php");
	} else	 {
		define('FPDF_FONTPATH','../../PDF/font/');
		include ("../../PDF/fpdf.php");
		include ("../../PDF/pdf_extends_DPIS.php");
	}
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$cmd = " select		COM_NO, a.COM_NAME, COM_DATE, COM_NOTE, COM_PER_TYPE, 
									a.COM_TYPE, COM_CONFIRM, a.DEPARTMENT_ID, b.COM_DESC, b.COM_NAME as COM_TYPE_NAME, COM_GROUP 
					 from		PER_COMMAND a, PER_COMTYPE b
					 where	COM_ID=$COM_ID and a.COM_TYPE=b.COM_TYPE ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
		
	$COM_NO = trim($data[COM_NO]);
	$COM_NAME = trim($data[COM_NAME]);
	$COM_DATE = $data[COM_DATE];
	$COM_DATE = show_date_format($COM_DATE,5);
	$COM_NOTE = trim($data[COM_NOTE]);
	$COM_PER_TYPE = trim($data[COM_PER_TYPE]);
	$COM_CONFIRM = trim($data[COM_CONFIRM]);
	$TMP_DEPARTMENT_ID = $data[DEPARTMENT_ID];
		
	$COM_TYPE = trim($data[COM_TYPE]);
	$COM_DESC = str_replace("�����", "", $data[COM_DESC]);
	$COM_TYPE_NAME = trim($data[COM_TYPE_NAME]);
	$COM_GROUP = trim($data[COM_GROUP]);
	
	if ($print_order_by==1) $order_str = "a.CMD_SEQ";
	else $order_str = "a.CMD_ORG3, a.CMD_ORG4, a.CMD_POS_NO_NAME, a.CMD_POS_NO";
	if ($BKK_FLAG==1) {
		$arr_temp = explode("���", $COM_NO);
		if ($arr_temp[0]=="���. ") $DEPARTMENT_NAME = "��ا෾��ҹ��";
		$report_title = "�ѭ����������´���$COM_NAME Ṻ���¤����$DEPARTMENT_NAME ��� $arr_temp[1] ŧ�ѹ��� $COM_DATE";
	} else {
		if ($MFA_FLAG==1 && $TMP_DEPARTMENT_ID == "") {
			$report_title = "$COM_TYPE_NAME||�ѭ����������´���$COM_NAME Ṻ���¤����$MINISTRY_NAME ��� $COM_NO ŧ�ѹ��� $COM_DATE";
		} else {
			if ($RID_FLAG==1)
				$report_title = "$COM_TYPE_NAME||�ѭ����������´���$COM_DESC Ṻ���¤����$DEPARTMENT_NAME ��� $COM_NO ŧ�ѹ��� $COM_DATE";
			else
				$report_title = "$COM_TYPE_NAME||�ѭ����������´���$COM_NAME Ṻ���¤����$DEPARTMENT_NAME ��� $COM_NO ŧ�ѹ��� $COM_DATE";
		}
	}
	$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
	$report_code = "P1503";
	

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();
	if ($FLAG_RTF) {
//		$sum_w = array_sum($heading_width);
		$sum_w = 0;
		for($h = 0; $h < count($heading_width); $h++) {
			if ($arr_column_sel[$h]==1)
				$sum_w += $heading_width[$h];
		}
		for($h = 0; $h < count($heading_width); $h++) {
			if ($arr_column_sel[$h]==1)
			$heading_width[$h] = $heading_width[$h] / $sum_w * 100;
		}
		$fname= "rpt_data_help_comdtl.rtf";
	//	RTF $papersize = "a4", $margl = 720, $margr = 720, $margt = 720, $margb = 720, $orient = "p", $u_color_tab="";
		$paper_size="a4";
		$orientation='P';
		$RTF = new RTF($paper_size, ".7in", ".3in", ".5in", ".1in", $orientation, $set_of_colors);
		$RTF->set_default_font($font, 14);
	//	echo "default font_id::".$RTF->dfl_FontID."<br>";	

		$RTF->set_report_code($report_code);
		$RTF->set_report_title($report_title);
		$RTF->set_company_name($company_name);
	} else {
		$unit="mm";
		$paper_size="A4";
		$lang_code="TH";
		$company_name = "";
		$orientation='L';
		$pdf = new PDF($orientation,$unit,$paper_size,$lang_code,$company_name,$report_title,$report_code,$heading,$heading_width,$heading_align);
		$pdf->Open();
		$pdf->SetMargins(5,5,5);
		$pdf->AliasNbPages();
		$pdf->AddPage();
		$pdf->SetTextColor(0, 0, 0);
		$pdf->SetFont($font,'',14);
		$pdf->SetAutoPageBreak(true,10);
    }
	include ("rpt_data_help_comdtl_format.php");	// ��˹������е��������Ѻ�������¹�ٻẺ��§ҹ

	if($DPISDB=="odbc"){
		$cmd = " select			a.PER_ID, b.PN_CODE as PRE_PN_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE, b.PER_TYPE, b.LEVEL_NO,
											a.EN_CODE, a.CMD_POSITION, a.CMD_ORG3, a.CMD_ORG4, a.CMD_OLD_SALARY, a.CMD_LEVEL, 
											a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID, a.LEVEL_NO as NEW_LEVEL_NO, a.CMD_SALARY,
											a.PL_CODE_ASSIGN, a.PN_CODE_ASSIGN, a.EP_CODE_ASSIGN, a.TP_CODE_ASSIGN,
											a.CMD_DATE, a.CMD_NOTE1, a.CMD_NOTE2, a.CMD_POS_NO_NAME, a.CMD_POS_NO, a.CMD_NOW
						 from			PER_COMDTL a, PER_PERSONAL b
						 where		a.COM_ID=$COM_ID and a.PER_ID=b.PER_ID 
						 order by 	$order_str ";	
	}elseif($DPISDB=="oci8"){
		$cmd = " select			a.PER_ID, b.PN_CODE as PRE_PN_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE, b.PER_TYPE, b.LEVEL_NO,
											a.EN_CODE, a.CMD_POSITION, a.CMD_ORG3, a.CMD_ORG4, a.CMD_OLD_SALARY, a.CMD_LEVEL,
											a.POS_ID, a.POEM_ID, a.POEMS_ID,a.POT_ID, a.LEVEL_NO as NEW_LEVEL_NO,a.CMD_SALARY,
											a.PL_CODE_ASSIGN, a.PN_CODE_ASSIGN, a.EP_CODE_ASSIGN, a.TP_CODE_ASSIGN,
											a.CMD_DATE, a.CMD_NOTE1, a.CMD_NOTE2, a.CMD_POS_NO_NAME, a.CMD_POS_NO, a.CMD_NOW
						 from			PER_COMDTL a, PER_PERSONAL b
						 where		a.COM_ID=$COM_ID and a.PER_ID=b.PER_ID 
						 order by 	$order_str ";	
	}elseif($DPISDB=="mysql"){
		$cmd = " select			a.PER_ID, b.PN_CODE as PRE_PN_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE, b.PER_TYPE, b.LEVEL_NO,
											a.EN_CODE, a.CMD_POSITION, a.CMD_ORG3, a.CMD_ORG4, a.CMD_OLD_SALARY, a.CMD_LEVEL, 
											a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID, a.LEVEL_NO as NEW_LEVEL_NO, a.CMD_SALARY,
											a.PL_CODE_ASSIGN, a.PN_CODE_ASSIGN, a.EP_CODE_ASSIGN, a.TP_CODE_ASSIGN, 
											a.CMD_DATE, a.CMD_NOTE1, a.CMD_NOTE2, a.CMD_POS_NO_NAME, a.CMD_POS_NO, a.CMD_NOW
						 from			PER_COMDTL a, PER_PERSONAL b
						 where		a.COM_ID=$COM_ID and a.PER_ID=b.PER_ID 
						 order by 	$order_str ";	
	} // end if
	$count_data = $db_dpis->send_cmd($cmd);
//	echo "$cmd<br>";
//	$db_dpis->show_error();
	$data_count = $data_row = 0;
//	$ORG_ID = $ORG_ID_1 = $ORG_ID_2 = -1;
	$CMD_ORG3 = $CMD_ORG4 = -1;
	while($data = $db_dpis->get_array()){
		$data_row++;
		
		$PER_ID = $data[PER_ID];
		$PRE_PN_CODE = trim($data[PRE_PN_CODE]);
		$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='$PRE_PN_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$PN_NAME = $data2[PN_NAME];		
		$PER_NAME = $data[PER_NAME];
		$PER_SURNAME = $data[PER_SURNAME];
		if ($CARDNO_FLAG==1){
		$PER_CARDNO = $data[PER_CARDNO];
		$cardID=card_no_format($PER_CARDNO,$CARD_NO_DISPLAY);
		}
		$PER_BIRTHDATE = show_date_format($data[PER_BIRTHDATE],$CMD_DATE_DISPLAY);
		$PER_TYPE = $data[PER_TYPE];
		$LEVEL_NO = trim($data[LEVEL_NO]);
        

		$EDU_TYPE = "%2%";
		if($DPISDB=="odbc"){
			$cmd = "	select		b.EN_NAME, b.EN_SHORTNAME, c.EM_NAME, d.INS_NAME, EDU_INSTITUTE, EDU_HONOR, CT_CODE_EDU
								from		( 
													(
														PER_EDUCATE a
														left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
													) left join PER_EDUCMAJOR c on (a.EM_CODE=c.EM_CODE)
												) left join PER_INSTITUTE d on (a.INS_CODE=d.INS_CODE)
								where		a.PER_ID = $PER_ID and a.EDU_TYPE like '$EDU_TYPE' ";
		}elseif($DPISDB=="oci8"){
			$cmd = "	select		b.EN_NAME, b.EN_SHORTNAME, c.EM_NAME, d.INS_NAME, EDU_INSTITUTE, EDU_HONOR, CT_CODE_EDU
								from		PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCMAJOR c, PER_INSTITUTE d
								where		a.PER_ID = $PER_ID and a.EDU_TYPE like '$EDU_TYPE' and a.EN_CODE=b.EN_CODE(+) and a.EM_CODE=c.EM_CODE(+) and a.INS_CODE=d.INS_CODE(+) ";
		}elseif($DPISDB=="mysql"){
			$cmd = "	select		b.EN_NAME, b.EN_SHORTNAME, c.EM_NAME, d.INS_NAME, EDU_INSTITUTE, EDU_HONOR, CT_CODE_EDU
								from		( 
													(
														PER_EDUCATE a
														left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
													) left join PER_EDUCMAJOR c on (a.EM_CODE=c.EM_CODE)
												) left join PER_INSTITUTE d on (a.INS_CODE=d.INS_CODE)
								where		a.PER_ID = $PER_ID and a.EDU_TYPE like '$EDU_TYPE' ";
		} // end if
		$db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();
		$data2 = $db_dpis2->get_array();
		$EN_NAME = trim($data2[EN_NAME]);
		if (!trim($data2[EN_SHORTNAME])) {
			$EN_SHORTNAME = trim($data2[EN_NAME]);
		} else {
			$EN_SHORTNAME = trim($data2[EN_SHORTNAME]);
		}
		if (trim($data2[EM_NAME])) {
			$EM_NAME = "(".trim($data2[EM_NAME]).")";
		}
		$INS_NAME = trim($data2[INS_NAME]);
		if (!$INS_NAME) $INS_NAME = trim($data2[EDU_INSTITUTE]);
		$EDU_HONOR = trim($data2[EDU_HONOR]);
		if ($EDU_HONOR && strpos($EDUCATION_NAME,"���õԹ���") !== true) $EDU_HONOR = "���õԹ���" . $EDU_HONOR;
		$CT_CODE_EDU = trim($data2[CT_CODE_EDU]);
		$cmd = " select CT_NAME from PER_COUNTRY where CT_CODE='$CT_CODE_EDU' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$CT_NAME = $data2[CT_NAME];
		if ($CT_NAME=="��") $CT_NAME = "";
		
		$CMD_LEVEL = trim($data[CMD_LEVEL]);
		if($DPISDB=="mysql"){
			$arr_temp = explode("|", $data[CMD_POSITION]);
		}else{
			$arr_temp = explode("\|", $data[CMD_POSITION]);
		}
		$CMD_POSITION = $arr_temp[0];
		$CMD_PM_NAME = $arr_temp[1];
		$CMD_POS_NO_NAME = trim($data[CMD_POS_NO_NAME]); 
		$CMD_POS_NO = trim($data[CMD_POS_NO]); 
		if ($print_order_by==1) {
			if($DPISDB=="mysql"){
				$tmp_org2 = explode("|", trim($data[CMD_ORG2]));
			}else{
				$tmp_org2 = explode("\|", trim($data[CMD_ORG2]));
			}
			$CMD_ORG2 = trim($tmp_org2[0]);
			$NEW_DEPARTMENT_ID = trim($tmp_org2[1]);
			if($DPISDB=="mysql"){
				$tmp_org3 = explode("|", trim($data[CMD_ORG3]));
			}else{
				$tmp_org3 = explode("\|", trim($data[CMD_ORG3]));
			}
			$CMD_ORG3 = trim($tmp_org3[0]);
			$NEW_ORG_ID = trim($tmp_org3[1]);
			if($DPISDB=="mysql"){
				$tmp_org4 = explode("|", trim($data[CMD_ORG4]));
			}else{
				$tmp_org4 = explode("\|", trim($data[CMD_ORG4]));
			}
			$CMD_ORG4 = trim($tmp_org4[0]);
			$NEW_ORG_ID_1 = trim($tmp_org4[1]);
			if($DPISDB=="mysql"){
				$tmp_org5 = explode("|", trim($data[CMD_ORG5]));
			}else{
				$tmp_org5 = explode("\|", trim($data[CMD_ORG5]));
			}
			$CMD_ORG5 = trim($tmp_org5[0]);
			$NEW_ORG_ID_2 = trim($tmp_org5[1]);
		}
		$CMD_OLD_SALARY = $data[CMD_OLD_SALARY];
		$NEW_LEVEL_NO = trim($data[NEW_LEVEL_NO]);
		$CMD_SALARY = $data[CMD_SALARY];
		$CMD_DATE = show_date_format($data[CMD_DATE],$CMD_DATE_DISPLAY);
		$CMD_NOTE1 = trim($data[CMD_NOTE1]);
		$CMD_NOTE2 = trim($data[CMD_NOTE2]);
		$CMD_NOW = trim($data[CMD_NOW]);
		if ($CMD_NOW=="Y") $CMD_DATE = "�Ѵ����繵��";

		$cmd = "select LEVEL_NAME, POSITION_TYPE, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$CMD_LEVEL' ";
		$db_dpis2->send_cmd($cmd);
		$data2= $db_dpis2->get_array();
		$LEVEL_NAME = trim($data2[LEVEL_NAME]);
		$CMD_POSITION_TYPE = $data2[POSITION_TYPE];
		$POSITION_LEVEL = $data2[POSITION_LEVEL];
		if (!$POSITION_LEVEL) $POSITION_LEVEL = $LEVEL_NAME;
		$CMD_LEVEL_NAME =  $POSITION_LEVEL;
		
		$cmd = "select LEVEL_NAME, POSITION_TYPE, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$NEW_LEVEL_NO' ";
		$db_dpis2->send_cmd($cmd);
		$data2= $db_dpis2->get_array();
		$LEVEL_NAME = trim($data2[LEVEL_NAME]);
		$NEW_POSITION_TYPE = $data2[POSITION_TYPE];
		$POSITION_LEVEL = $data2[POSITION_LEVEL];
		if (!$POSITION_LEVEL) $POSITION_LEVEL = $LEVEL_NAME;
		$NEW_LEVEL_NAME =  $POSITION_LEVEL;

		if($PER_TYPE==1){
			$PL_PN_CODE_ASSIGN = trim($data[PL_CODE_ASSIGN]); 
			$cmd = " select PL_CODE, PL_NAME from PER_LINE where PL_CODE= '$PL_PN_CODE_ASSIGN' ";			
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$NEW_PL_NAME = trim($data2[PL_NAME]);
			
			$cmd = " select PM_CODE, PT_CODE from PER_POSITION  
							where trim(POS_NO)='$CMD_POS_NO' and DEPARTMENT_ID = $DEPARTMENT_ID ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$CMD_PT_CODE = trim($data2[PT_CODE]);
			$CMD_PM_CODE = trim($data2[PM_CODE]);
			
			$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE)='$CMD_PT_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$CMD_PT_NAME = trim($data2[PT_NAME]);

			$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$CMD_PM_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			if (!$CMD_PM_NAME) $CMD_PM_NAME = trim($data2[PM_NAME]);

			$CMD_POSITION = pl_name_format($CMD_POSITION, $CMD_PM_NAME, $CMD_PT_NAME, $CMD_LEVEL);

			$POS_ID = $data[POS_ID];
			$cmd = "	select		a.POS_NO_NAME, a.POS_NO, b.PL_NAME, c.ORG_NAME, a.PT_CODE, a.ORG_ID_1, a.ORG_ID_2, a.PM_CODE
						from		PER_POSITION a, PER_LINE b, PER_ORG c
						where		a.POS_ID=$POS_ID and a.PL_CODE=b.PL_CODE and a.ORG_ID=c.ORG_ID ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$NEW_POS_NO_NAME = trim($data2[POS_NO_NAME]);
			$NEW_POS_NO = trim($data2[POS_NO]);
			if (!$NEW_PL_NAME) $NEW_PL_NAME = trim($data2[PL_NAME]);
			$NEW_ORG_NAME = trim($data2[ORG_NAME]);
			$NEW_PT_CODE = trim($data2[PT_CODE]);
			$NEW_PM_CODE = trim($data2[PM_CODE]);
			
			$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE)='$NEW_PT_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$NEW_PT_NAME = trim($data2[PT_NAME]);
			
			$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$NEW_PM_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$NEW_PM_NAME = trim($data2[PM_NAME]);

			$NEW_PL_NAME = pl_name_format($NEW_PL_NAME, $NEW_PM_NAME, $NEW_PT_NAME, $NEW_LEVEL_NO);
		}elseif($PER_TYPE==2){
			$CMD_POSITION = trim($CMD_POSITION)?($CMD_POSITION ." ". level_no_format($CMD_LEVEL)):"";

			$POEM_ID = $data[POEM_ID];
			$cmd = "	select		a.POEM_NO_NAME, a.POEM_NO, b.PN_NAME, c.ORG_NAME, a.ORG_ID_1, a.ORG_ID_2
								from		PER_POS_EMP a, PER_POS_NAME b, PER_ORG c
								where		a.POEM_ID=$POEM_ID and a.PN_CODE=b.PN_CODE and a.ORG_ID=c.ORG_ID ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$NEW_POS_NO_NAME = trim($data2[POEM_NO_NAME]);
			$NEW_POS_NO = trim($data2[POEM_NO]);
			$NEW_ORG_NAME = trim($data2[ORG_NAME]);
			
			$PL_PN_CODE_ASSIGN = trim($data[PN_CODE_ASSIGN]);
			$cmd = " select PN_CODE, PN_NAME from PER_POS_NAME where trim(PN_CODE) = '$PL_PN_CODE_ASSIGN' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$NEW_PL_NAME = trim($data2[PN_NAME]);		
			
			$NEW_PL_NAME = trim($NEW_PL_NAME)?($NEW_PL_NAME ." ". level_no_format($NEW_LEVEL_NO)):"";
		}elseif($PER_TYPE==3){
			$CMD_POSITION = trim($CMD_POSITION)?($CMD_POSITION ." ". level_no_format($CMD_LEVEL)):"";

			$POEMS_ID = $data[POEMS_ID];
			$cmd = "	select		a.POEMS_NO_NAME, a.POEMS_NO, b.EP_NAME, c.ORG_NAME, a.ORG_ID_1, a.ORG_ID_2
								from		PER_POS_EMPSER a, PER_EMPSER_POS_NAME b, PER_ORG c
								where		a.POEMS_ID=$POEMS_ID and a.EP_CODE=b.EP_CODE and a.ORG_ID=c.ORG_ID ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$NEW_POS_NO_NAME = trim($data2[POEMS_NO_NAME]);
			$NEW_POS_NO = trim($data2[POEMS_NO]);
			$NEW_ORG_NAME = trim($data2[ORG_NAME]);

			$PL_PN_CODE_ASSIGN = trim($data[EP_CODE_ASSIGN]);
			$cmd = " select EP_CODE, EP_NAME from PER_EMPSER_POS_NAME 
					where trim(EP_CODE) IN ('$PL_PN_CODE', '$PL_PN_CODE_ASSIGN')";
			$db_dpis2->send_cmd($cmd);
			while ( $data2 = $db_dpis2->get_array() ) {
				$temp_id = trim($data2[EP_CODE]);
				$PL_PN_NAME = ($temp_id == $PL_PN_CODE)?  trim($data2[EP_NAME]) : $PL_PN_NAME;
				$NEW_PL_NAME = ($temp_id == $PL_PN_CODE_ASSIGN)?  trim($data2[EP_NAME]) : $PL_PN_NAME_ASSIGN;
			}
				$NEW_PL_NAME = trim($NEW_PL_NAME)?($NEW_PL_NAME ." ". level_no_format($NEW_LEVEL_NO)):"";
		}elseif($PER_TYPE==4){
			$CMD_POSITION = trim($CMD_POSITION)?($CMD_POSITION ." ". level_no_format($CMD_LEVEL)):"";

			$POT_ID = $data[POT_ID];
			$cmd = "	select		a.POT_NO_NAME, a.POT_NO, b.TP_NAME, c.ORG_NAME, a.ORG_ID_1, a.ORG_ID_2
								from		PER_POS_TEMP a, PER_TEMP_POS_NAME b, PER_ORG c
								where		a.POT_ID=$POT_ID and a.TP_CODE=b.TP_CODE and a.ORG_ID=c.ORG_ID ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$NEW_POS_NO_NAME = trim($data2[POEMS_NO_NAME]);
			$NEW_POS_NO = trim($data2[POEMS_NO]);
			//-o--$NEW_PL_NAME = trim($data2[EP_NAME]);
				
			//$NEW_PL_NAME = trim($NEW_PL_NAME)?($NEW_PL_NAME ." ". level_no_format($NEW_LEVEL_NO)):"";
			$NEW_ORG_NAME = trim($data2[ORG_NAME]);
			//$NEW_PL_NAME = trim($NEW_PL_NAME)?($NEW_PL_NAME ." ". level_no_format($NEW_LEVEL_NO)):"";

			$PL_PN_CODE_ASSIGN = trim($data[TP_CODE_ASSIGN]);
			$cmd = " select TP_CODE, TP_NAME from PER_TEMP_POS_NAME 
					where trim(TP_CODE) IN ('$PL_PN_CODE', '$PL_PN_CODE_ASSIGN')";
			$db_dpis2->send_cmd($cmd);
			while ( $data2 = $db_dpis2->get_array() ) {
				$temp_id = trim($data2[EP_CODE]);
				$PL_PN_NAME = ($temp_id == $PL_PN_CODE)?  trim($data2[TP_NAME]) : $PL_PN_NAME;
				$NEW_PL_NAME = ($temp_id == $PL_PN_CODE_ASSIGN)?  trim($data2[TP_NAME]) : $PL_PN_NAME_ASSIGN;
			}
				$NEW_PL_NAME = trim($NEW_PL_NAME)?($NEW_PL_NAME ." ". level_no_format($NEW_LEVEL_NO)):"";
		} // end if
		
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$NEW_DEPARTMENT_ID ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$NEW_DEPARTMENT_NAME = $data2[ORG_NAME];
		
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$NEW_ORG_ID ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$NEW_ORG_NAME = $data2[ORG_NAME];
		
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$NEW_ORG_ID_1 ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$NEW_ORG_NAME_1 = $data2[ORG_NAME];
		
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$NEW_ORG_ID_2 ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$NEW_ORG_NAME_2 = $data2[ORG_NAME];

		if ($CMD_ORG3=="-") $CMD_ORG3 = "";
		if ($CMD_ORG4=="-") $CMD_ORG4 = "";
		if ($CMD_ORG5=="-") $CMD_ORG5 = "";
		if ($NEW_ORG_NAME=="-") $NEW_ORG_NAME = "";
		if ($NEW_ORG_NAME_1=="-") $NEW_ORG_NAME_1 = "";
		if ($NEW_ORG_NAME_2=="-") $NEW_ORG_NAME_2 = "";
		if ($print_order_by==2) {
			if($CMD_ORG3 != trim($data[CMD_ORG3]) && trim($data[CMD_ORG3]) && trim($data[CMD_ORG3]) != "-"){
				if($DPISDB=="mysql"){
					$tmp_org3 = explode("|", trim($data[CMD_ORG3]));
				}else{
					$tmp_org3 = explode("\|", trim($data[CMD_ORG3]));
				}
				$CMD_ORG3 = trim($tmp_org3[0]);
				$ORG_ID = $tmp_org3[1]; //--ORG_ID

				$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID ";
				if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$NEW_ORG_NAME = $data2[ORG_NAME];
				
				$arr_content[$data_count][type] = "ORG";
				$arr_content[$data_count][org_name] = $CMD_ORG3;
				$arr_content[$data_count][new_org_name] = $NEW_ORG_NAME;

				$data_count++;
			} // end if
		
			if($CMD_ORG4 != trim($data[CMD_ORG4]) && trim($data[CMD_ORG4]) && trim($data[CMD_ORG4]) != "-"){
				if($DPISDB=="mysql"){
					$tmp_org4 = explode("|", trim($data[CMD_ORG4]));
				}else{
					$tmp_org4 = explode("\|", trim($data[CMD_ORG4]));
				}
				$CMD_ORG4 = trim($tmp_org4[0]);
				$ORG_ID_1 = $tmp_org4[1]; //--ORG_ID_1

				$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_1 ";
				if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$NEW_ORG_NAME_1 = $data2[ORG_NAME];
				
				$arr_content[$data_count][type] = "ORG_1";
				$arr_content[$data_count][org_name] = $CMD_ORG4;
				$arr_content[$data_count][new_org_name] = $NEW_ORG_NAME_1;

				$data_count++;
			} // end if
		}

		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][order] = $data_row;
		$arr_content[$data_count][name] = $PN_NAME . $PER_NAME ." ". $PER_SURNAME;
		if ($CARDNO_FLAG==1) $arr_content[$data_count][name] .= "*Enter* (".(($NUMBER_DISPLAY==2)?convert2thaidigit($cardID):$cardID).")"; //������͡���Ţ�ѵ���ѧ����
		$arr_content[$data_count][educate] = $EN_NAME . ($EM_NAME?"$EM_NAME":"") ."*Enter*". ($INS_NAME?"$INS_NAME":"");
		if ($MFA_FLAG==1 && $EDU_HONOR) $arr_content[$data_count][educate] .= "*Enter*".trim($EDU_HONOR);
		if ($MFA_FLAG==1 && $CT_NAME) $arr_content[$data_count][educate] .= "*Enter*".trim($CT_NAME);
		
		$arr_content[$data_count][cmd_pos_no_name] = $CMD_POS_NO_NAME;
		$arr_content[$data_count][cmd_pos_no] = $CMD_POS_NO;
		$arr_content[$data_count][cmd_position] = $CMD_POSITION;
		if ($print_order_by==1 || $COMMAND_PRT==1) {
			if ($CMD_ORG5 && $SESS_DEPARTMENT_NAME!="����Ż�зҹ") $arr_content[$data_count][cmd_position] .= "*Enter*".trim($CMD_ORG5);
			if ($CMD_ORG4) $arr_content[$data_count][cmd_position] .= "*Enter*".trim($CMD_ORG4);
			if ($CMD_ORG3) $arr_content[$data_count][cmd_position] .= "*Enter*".trim($CMD_ORG3);
			if ($MFA_FLAG==1) $arr_content[$data_count][cmd_position] .= "*Enter*".trim($CMD_ORG2);
		} 
		$arr_content[$data_count][cmd_position_type] = $CMD_POSITION_TYPE;
		$arr_content[$data_count][cmd_level_name] = $CMD_LEVEL_NAME;
		$arr_content[$data_count][cmd_old_salary] = $CMD_OLD_SALARY?number_format($CMD_OLD_SALARY):"-";
		
		$arr_content[$data_count][new_position] = $NEW_PL_NAME;
		if ($print_order_by==1 || $COMMAND_PRT==1) {
			if ($NEW_ORG_NAME_2 && $SESS_DEPARTMENT_NAME!="����Ż�зҹ") $arr_content[$data_count][new_position] .= "*Enter*".trim($NEW_ORG_NAME_2);
			if ($NEW_ORG_NAME_1) $arr_content[$data_count][new_position] .= "*Enter*".trim($NEW_ORG_NAME_1);
			if ($NEW_ORG_NAME) $arr_content[$data_count][new_position] .= "*Enter*".trim($NEW_ORG_NAME);
			if ($MFA_FLAG==1) $arr_content[$data_count][new_position] .= "*Enter*".trim($NEW_DEPARTMENT_NAME);
		} 
		$arr_content[$data_count][new_position_type] = $NEW_POSITION_TYPE;
		$arr_content[$data_count][new_level_name] = $NEW_LEVEL_NAME;
		$arr_content[$data_count][new_pos_no_name] = $NEW_POS_NO_NAME;
		$arr_content[$data_count][new_pos_no] = $NEW_POS_NO;
		$arr_content[$data_count][cmd_salary] = $CMD_SALARY?number_format($CMD_SALARY):"-";
		
		$arr_content[$data_count][cmd_date] = $CMD_DATE;
		$arr_content[$data_count][cmd_note1] = $CMD_NOTE1;
		
		$data_count++;

		if ($print_order_by==1 && $COMMAND_PRT!=1) {
			$arr_content[$data_count][type] = "CONTENT";
//		$arr_content[$data_count][name] = card_no_format($PER_CARDNO,$CARD_NO_DISPLAY);
//		$arr_content[$data_count][educate] = $INS_NAME;
			if($EN_SHORTNAME[1])
				$arr_content[$data_count][educate] = $EN_SHORTNAME[1]."*Enter*".$EM_NAME[1];
			if ($DEPARTMENT_NAME=="�����û���ͧ") {
				if ($CMD_OT_CODE=="03") {
					if (!$CMD_ORG5 && !$CMD_ORG4) $arr_content[$data_count][cmd_position] = "���ӡ�û���ͧ".$CMD_ORG3." ".$CMD_ORG3;
					else $arr_content[$data_count][cmd_position] = trim($CMD_ORG5." ".$CMD_ORG4." ".$CMD_ORG3);
				} else {
					$arr_content[$data_count][cmd_position] = trim($CMD_ORG4." ".$CMD_ORG3." ".$DEPARTMENT_NAME);
				}
				if ($NEW_OT_CODE=="03") { 
					if (!$NEW_ORG_NAME_2 && !$NEW_ORG_NAME_1) $arr_content[$data_count][new_position] = "���ӡ�û���ͧ".$NEW_ORG_NAME." ".$NEW_ORG_NAME;
					else $arr_content[$data_count][new_position] = trim($NEW_ORG_NAME_2." ".$NEW_ORG_NAME_1." ".$NEW_ORG_NAME);
				} else { 
					$arr_content[$data_count][new_position] = trim($NEW_ORG_NAME_1." ".$NEW_ORG_NAME." ".$DEPARTMENT_NAME);
				}
			} else {
				$arr_content[$data_count][cmd_position] = trim($CMD_ORG5." ".$CMD_ORG4." ".$CMD_ORG3);
				$arr_content[$data_count][new_position] = trim($NEW_ORG_NAME_2." ".$NEW_ORG_NAME_1." ".$NEW_ORG_NAME);
			}
			$data_count++;

		}
//		$arr_content[$data_count][cmd_note] = $CMD_NOTE2;

		if ($COMMAND_PRT!=1) {
			if($EN_SHORTNAME[2]){
				$arr_content[$data_count][type] = "CONTENT";
				$arr_content[$data_count][educate] = $EN_SHORTNAME[2]."*Enter*".$EM_NAME[2];

				$data_count++;

				if($EN_SHORTNAME[3]){
					$arr_content[$data_count][type] = "CONTENT";
					$arr_content[$data_count][educate] = $EN_SHORTNAME[3]."*Enter*".$EM_NAME[3];

					$data_count++;

					if($EN_SHORTNAME[4]){
						$arr_content[$data_count][type] = "CONTENT";
						$arr_content[$data_count][educate] = $EN_SHORTNAME[4]."*Enter*".$EM_NAME[4];

						$data_count++;
					} // end if 4
				} // end if 3
			} // end if 2
		}

		if($CMD_NOTE2){
			$arr_content[$data_count][type] = "CONTENT";
			$arr_content[$data_count][cmd_note2] = $CMD_NOTE2;
			
			$data_count++;
		} // end if
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
	//new format****************************************
	if($count_data){
	   $head_text1 = implode(",", $heading_text);
		$head_width1 = implode(",", $heading_width);
		$head_align1 = implode(",", $heading_align);
		$col_function = implode(",", $column_function);
		if ($FLAG_RTF) {
			$RTF->add_header("", 0, false);	// header default
			$RTF->add_footer("", 0, false);		// footer default
			
	//		echo "$head_text1<br>";
			$tab_align = "center";
			$result = $RTF->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", $fmtTableHeader_col_idx, $fmtTableHeader_bgcol_idx, $COLUMN_FORMAT, $col_function, false, $tab_align);
		} else {
			$pdf->AutoPageBreak = false; 
		$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", "0066CC", "EEEEFF", $COLUMN_FORMAT, $col_function);
		           }
		if (!$result) echo "****** error ****** on open table for $table<br>";
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$CONTENT_TYPE = $arr_content[$data_count][type];
			$ORDER = $arr_content[$data_count][order];
			$NAME = $arr_content[$data_count][name];
			$EDUCATE = $arr_content[$data_count][educate];
			$CMD_POS_NO_NAME = $arr_content[$data_count][cmd_pos_no_name];
			$CMD_POS_NO = $arr_content[$data_count][cmd_pos_no];
			$CMD_POSITION = $arr_content[$data_count][cmd_position];
			$CMD_ORG3 = $arr_content[$data_count][cmd_org3];
			$CMD_OLD_SALARY = $arr_content[$data_count][cmd_old_salary];
			$NEW_POSITION = $arr_content[$data_count][new_position];
			$NEW_POS_NO_NAME = $arr_content[$data_count][new_pos_no_name];
			$NEW_POS_NO = $arr_content[$data_count][new_pos_no];
			$CMD_SALARY = $arr_content[$data_count][cmd_salary];
			$CMD_DATE = $arr_content[$data_count][cmd_date];
	//			$CMD_NOTE = $arr_content[$data_count][cmd_note];
			$CMD_NOTE1 = $arr_content[$data_count][cmd_note1];
			$CMD_NOTE2 = $arr_content[$data_count][cmd_note2];
		
			if ($print_order_by==2) {
				if($CONTENT_TYPE=="ORG" || $CONTENT_TYPE=="ORG_1" || $CONTENT_TYPE=="ORG_2"){
					$ORG_NAME = $arr_content[$data_count][org_name];
					$NEW_ORG_NAME = $arr_content[$data_count][new_org_name];
					
					$arr_data = (array) null;
					$arr_data[] = "<**1**>";
					$arr_data[] = "<**1**>";
					$arr_data[] = "<**1**>";
					$arr_data[] = "<**2**>$ORG_NAME";
					$arr_data[] = "<**2**>$ORG_NAME";
					$arr_data[] = "<**2**>$ORG_NAME";
					$arr_data[] = "<**3**>$NEW_ORG_NAME";
					$arr_data[] = "<**3**>$NEW_ORG_NAME";
					$arr_data[] = "<**3**>$NEW_ORG_NAME";
					$arr_data[] = "";
					$arr_data[] = "";

					$data_align = array("C", "C", "C", "L", "L", "L", "L", "L", "L", "L", "L");
					
					if($CONTENT_TYPE == "ORG"){
						   if ($FLAG_RTF)
						$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
							else
						$result = $pdf->add_data_tab($arr_data, 7, "RL", $data_align, "", "14", "b", "000000", "");
					}else{ 
						 if ($FLAG_RTF)
							$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
						else
							$result = $pdf->add_data_tab($arr_data, 7, "RL", $data_align, "", "14", "", "000000", "");
					}
					if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
				} // end if
			}
			
			if($CONTENT_TYPE=="CONTENT"){
				$arr_data = (array) null;
				$arr_data[] = $ORDER;
				$arr_data[] = $NAME;
				$arr_data[] = $EDUCATE;
				$arr_data[] = $CMD_POSITION;
				$arr_data[] = $CMD_POS_NO_NAME.$CMD_POS_NO;
				$arr_data[] = $CMD_OLD_SALARY;
				$arr_data[] = $NEW_POSITION;
				$arr_data[] = $NEW_POS_NO_NAME.$NEW_POS_NO;
				$arr_data[] = $CMD_SALARY;
				$arr_data[] = $CMD_DATE;
				$arr_data[] = $CMD_NOTE1;

				$data_align = array("C", "L", "L", "L", "C", "R", "L", "C", "R", "C", "L");
				if ($FLAG_RTF)
					$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
				else
					$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", "000000", "");
				if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
		
				if($CMD_NOTE2){
					$arr_data = (array) null;
					$arr_data[] = "<**1**>�����˵� : $CMD_NOTE2";
					$arr_data[] = "<**1**>�����˵� : $CMD_NOTE2";
					$arr_data[] = "<**1**>�����˵� : $CMD_NOTE2";
					$arr_data[] = "<**1**>�����˵� : $CMD_NOTE2";
					$arr_data[] = "<**1**>�����˵� : $CMD_NOTE2";
					$arr_data[] = "<**1**>�����˵� : $CMD_NOTE2";
					$arr_data[] = "<**1**>�����˵� : $CMD_NOTE2";
					$arr_data[] = "<**1**>�����˵� : $CMD_NOTE2";
					$arr_data[] = "<**1**>�����˵� : $CMD_NOTE2";
					$arr_data[] = "<**1**>�����˵� : $CMD_NOTE2";
					$arr_data[] = "<**1**>�����˵� : $CMD_NOTE2";

					$data_align = array("L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L");
				     if ($FLAG_RTF)
					$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
				    else
					$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", "000000", "");
					if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
				} // end if
			} // end if
		} // end for
		
		if ($FLAG_RTF) {
			$RTF->close_tab(); 
		}else {
			$pdf->close_tab("");
		} 
		
		if($COM_NOTE){
			$head_text1 = "";
			$head_width1 = "20,270";
			$head_align1 = "L,L";
			if ($FLAG_RTF) {
				$RTF->add_header("", 0, false);	// header default
				$RTF->add_footer("", 0, false);		// footer default
				$tab_align = "center";
				$result = $RTF->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", $fmtTableHeader_col_idx, $fmtTableHeader_bgcol_idx, $COLUMN_FORMAT, $col_function, false, $tab_align);
		     } else {
				$result = $pdf->open_tab($head_text1, $head_width1, 7, "", $head_align1, "", "14", "b", "0066CC", "EEEEFF", 0);
			}
			if (!$result) echo "****** error ****** on open table for $table<br>";
			
			$arr_data = (array) null;
			$arr_data[] = "�����˵� : ";
			$arr_data[] = "$COM_NOTE";

			$data_align = array("L", "L");
			if ($FLAG_RTF)
			$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
			else	
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";

			if ($FLAG_RTF) {
				$RTF->close_tab(); 
			}else {
				$pdf->close_tab("");
			} 
		} // end if
	}else{
	     if ($FLAG_RTF){
			$result = $RTF->add_text_line("********** ����բ����� **********", 7, "", "C", "", "14", "b", 0, 0);
			 if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
		  }else{
			$pdf->SetFont($font,'b','',16);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
			$pdf->Cell(287,10,"********** ����բ����� **********",0,1,'C');
		}
	} // end if
	if ($FLAG_RTF) {
		$RTF->close_tab(); 
//			$RTF->close_section(); 
		$RTF->display($fname);
	} else {
		$pdf->close_tab(""); 
		$pdf->close();
		$pdf->Output();	
	}
	ini_set("max_execution_time", 30);
?>
