<?
	include("../../php_scripts/connect_database.php");
	include("../php_scripts/pdf_wordarray_thaicut.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	if ($FLAG_RTF) {
		include ("rtf_setvar.php");	// กำหนดตัวแปรค่าสี set_of_colors
		require("../../RTF/rtf_class.php");
	} else	 {
		define('FPDF_FONTPATH','../../PDF/font/');
		include ("../../PDF/fpdf.php");
		include ("../../PDF/pdf_extends_DPIS.php");
	}

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$cmd = " select		COM_NO, a.COM_NAME, COM_DATE, COM_NOTE, COM_PER_TYPE, 
									a.COM_TYPE, COM_CONFIRM, a.DEPARTMENT_ID, b.COM_DESC, b.COM_NAME as COM_TYPE_NAME
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
	$COM_DESC = str_replace("คำสั่ง", "", $data[COM_DESC]);
	$COM_TYPE_NAME = trim($data[COM_TYPE_NAME]);
	
	if ($print_order_by==1) $order_str = "a.CMD_SEQ";
	else $order_str = "a.CMD_ORG3, a.CMD_ORG4, CMD_POS_NO_NAME, CMD_POS_NO";

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];

	

	if ($BKK_FLAG==1) {
		$arr_temp = explode("ที่", $COM_NO);
		if ($arr_temp[0]=="กทม. ") $DEPARTMENT_NAME = "กรุงเทพมหานคร";
		$report_title = "บัญชีรายละเอียดการ$COM_NAME แนบท้ายคำสั่ง$DEPARTMENT_NAME ที่ $arr_temp[1] ลงวันที่ $COM_DATE";
	} else {
		if ($MFA_FLAG==1 && $TMP_DEPARTMENT_ID == "") {
			$report_title = "$COM_TYPE_NAME||บัญชีรายละเอียดการ$COM_NAME แนบท้ายคำสั่ง$MINISTRY_NAME ที่ $COM_NO ลงวันที่ $COM_DATE";
		} else {
			if ($RID_FLAG==1)
				$report_title = "$COM_TYPE_NAME||บัญชีรายละเอียดการ$COM_DESC แนบท้ายคำสั่ง$DEPARTMENT_NAME ที่ $COM_NO ลงวันที่ $COM_DATE";
			else
				$report_title = "$COM_TYPE_NAME||บัญชีรายละเอียดการ$COM_NAME แนบท้ายคำสั่ง$DEPARTMENT_NAME ที่ $COM_NO ลงวันที่ $COM_DATE";
		}
	}
	$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
	$report_code = "P0407";
	

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
		$fname= "rpt_data_salreceive_comdtl.rtf";
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
	include ("rpt_data_salreceive_comdtl_format.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน
	
	if($DPISDB=="odbc"){
		$cmd = " select			a.PER_ID, b.PN_CODE as PRENAME_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE,b.PER_RETIREDATE, b.PER_TYPE, b.LEVEL_NO,
											a.EN_CODE, a.PL_CODE, a.PN_CODE, a.EP_CODE, a.TP_CODE, a.CMD_AC_NO, a.CMD_ACCOUNT,
											a.CMD_POSITION, a.CMD_ORG3, a.CMD_OLD_SALARY, a.CMD_LEVEL, 
											a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID, a.LEVEL_NO as NEW_LEVEL_NO, a.CMD_SALARY,
											a.CMD_DATE, a.CMD_NOTE1, a.CMD_NOTE2, a.CMD_DATE2, a.CMD_POS_NO_NAME, a.CMD_POS_NO, a.PM_CODE
						 from			PER_COMDTL a, PER_PERSONAL b
						 where		a.COM_ID=$COM_ID and a.PER_ID=b.PER_ID 
						 order by 	$order_str ";	
	}elseif($DPISDB=="oci8"){
		$cmd = " select			a.PER_ID, b.PN_CODE as PRENAME_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE,b.PER_RETIREDATE, b.PER_TYPE,
											a.EN_CODE, a.PL_CODE, a.PN_CODE, a.EP_CODE, a.TP_CODE, a.CMD_AC_NO, a.CMD_ACCOUNT,
											a.CMD_POSITION, a.CMD_ORG3, a.CMD_OLD_SALARY, a.CMD_LEVEL, 
											a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID, a.LEVEL_NO as NEW_LEVEL_NO, a.CMD_SALARY,
											a.CMD_DATE, a.CMD_NOTE1, a.CMD_NOTE2, a.CMD_DATE2, a.CMD_POS_NO_NAME, a.CMD_POS_NO, a.PM_CODE
						 from			PER_COMDTL a, PER_PERSONAL b
						 where		a.COM_ID=$COM_ID and a.PER_ID=b.PER_ID 
						 order by 	$order_str ";	
	}elseif($DPISDB=="mysql"){
		$cmd = " select			a.PER_ID, b.PN_CODE as PRENAME_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE,b.PER_RETIREDATE, b.PER_TYPE, b.LEVEL_NO,
											a.EN_CODE, a.PL_CODE, a.PN_CODE, a.EP_CODE, a.TP_CODE, a.CMD_AC_NO, a.CMD_ACCOUNT,
											a.CMD_POSITION, a.CMD_ORG3, a.CMD_OLD_SALARY, a.CMD_LEVEL, 
											a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID, a.LEVEL_NO as NEW__NO, a.CMD_SALARY,
											a.CMD_DATE, a.CMD_NOTE1, a.CMD_NOTE2, a.CMD_DATE2, a.CMD_POS_NO_NAME, a.CMD_POS_NO, a.PM_CODE
						 from			PER_COMDTL a, PER_PERSONAL b
						 where		a.COM_ID=$COM_ID and a.PER_ID=b.PER_ID 
						 order by 	$order_str ";	
	} // end if
	$count_data = $db_dpis->send_cmd($cmd);
//	echo $cmd;
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
		if ($CARDNO_FLAG==1){ // ถ้าเลือกโชวเลขบัตรหลังชื่อ
		$PER_CARDNO = $data[PER_CARDNO];
		$cardID = card_no_format($PER_CARDNO,$CARD_NO_DISPLAY);
		}
		$PER_BIRTHDATE = show_date_format($data[PER_BIRTHDATE],$CMD_DATE_DISPLAY);
		$PER_RETIREDATE = show_date_format($data[PER_RETIREDATE],$CMD_DATE_DISPLAY);
		$PER_TYPE = $data[PER_TYPE];
		$LEVEL_NO = trim($data[LEVEL_NO]);
		
		$EN_CODE = trim($data[EN_CODE]);
	
		//ข้อมุลการศึกษา
		$cmd = "	select		b.EN_NAME, b.EN_SHORTNAME, c.EM_NAME, d.INS_NAME, a.EDU_ENDDATE, EDU_INSTITUTE, EDU_HONOR, CT_CODE_EDU
								from		PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCMAJOR c, PER_INSTITUTE d
								where		a.PER_ID = $PER_ID and  trim(a.EN_CODE)='$EN_CODE' 
								and a.EN_CODE=b.EN_CODE(+) and a.EM_CODE=c.EM_CODE(+) and a.INS_CODE=d.INS_CODE(+) ";
		$db_dpis2->send_cmd($cmd);
//		echo "->".$cmd;
//		$db_dpis->show_error();
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
		$EDU_ENDDATE = show_date_format($data2[EDU_ENDDATE],$CMD_DATE_DISPLAY);
		$EDU_HONOR = trim($data2[EDU_HONOR]);
		if ($EDU_HONOR && strpos($EDUCATION_NAME,"เกียรตินิยม") !== true) $EDU_HONOR = "เกียรตินิยม" . $EDU_HONOR;
		$CT_CODE_EDU = trim($data2[CT_CODE_EDU]);
		$cmd = " select CT_NAME from PER_COUNTRY where CT_CODE='$CT_CODE_EDU' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$CT_NAME = $data2[CT_NAME];
		if ($CT_NAME=="ไทย") $CT_NAME = "";
		
		$CMD_LEVEL = trim($data[CMD_LEVEL]);
		$CMD_ACC_NO = trim($data[CMD_AC_NO]);
		$CMD_ACCOUNT = trim($data[CMD_ACCOUNT]);
		if($DPISDB=="mysql")	{
			$tmp_data = explode("|", trim($data[CMD_POSITION]));
		}else{
			$tmp_data = explode("\|", trim($data[CMD_POSITION]));
		}
		//ในกรณีที่มี CMD_PM_NAME
		if(is_array($tmp_data)){
			$CMD_POSITION = $tmp_data[0];
			$CMD_PM_NAME = $tmp_data[1];
		}else{
			$CMD_POSITION = $data[CMD_POSITION];
		}
		$CMD_POS_NO_NAME = trim($data[CMD_POS_NO_NAME]); 
		$CMD_POS_NO = trim($data[CMD_POS_NO]); 

		$CMD_ORG3 = $data[CMD_ORG3];
		$CMD_OLD_SALARY = $data[CMD_OLD_SALARY];
		$NEW_LEVEL_NO = trim($data[NEW_LEVEL_NO]);
		$CMD_SALARY = $data[CMD_SALARY];
		$CMD_DATE = show_date_format($data[CMD_DATE],$CMD_DATE_DISPLAY);
		$CMD_DATE2 = show_date_format($data[CMD_DATE2],$CMD_DATE_DISPLAY);
		$CMD_NOTE1 = trim($data[CMD_NOTE1]);
		$CMD_NOTE2 = trim($data[CMD_NOTE2]);

		$cmd = "select POSITION_TYPE, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$CMD_LEVEL' ";
		$db_dpis2->send_cmd($cmd);
		$data2= $db_dpis2->get_array();
		$CMD_POSITION_TYPE = trim($data2[POSITION_TYPE]);
		$CMD_LEVEL_NAME = trim($data2[POSITION_LEVEL]);
		
		$cmd = "select POSITION_TYPE, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$NEW_LEVEL_NO' ";
		$db_dpis2->send_cmd($cmd);
		$data2= $db_dpis2->get_array();
		$NEW_POSITION_TYPE = trim($data2[POSITION_TYPE]);
		$NEW_LEVEL_NAME = trim($data2[POSITION_LEVEL]);

		if($PER_TYPE==1){
			$PL_CODE = trim($data[PL_CODE]);
			$cmd = " select PL_NAME from PER_LINE where trim(PL_CODE)='$PL_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PL_NAME = $data2[PL_NAME]."*Enter*".$LEVEL_NAME;
			
			$cmd = " select PM_CODE, PT_CODE from PER_POSITION  
							where trim(POS_NO)='$CMD_POS_NO' and DEPARTMENT_ID = $DEPARTMENT_ID ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$CMD_PT_CODE = trim($data2[PT_CODE]);
			$CMD_PM_CODE = trim($data[PM_CODE]);
			
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
			$cmd = "	select		a.POS_NO, b.PL_NAME, c.ORG_NAME, a.PT_CODE, a.ORG_ID_1, a.ORG_ID_2, a.PM_CODE
						from		PER_POSITION a, PER_LINE b, PER_ORG c
						where		a.POS_ID=$POS_ID and a.PL_CODE=b.PL_CODE and a.ORG_ID=c.ORG_ID ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$NEW_POS_NO = trim($data2[POS_NO]);
			$NEW_PL_NAME = trim($data2[PL_NAME]);
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
			$PN_CODE = trim($data[PN_CODE]);
			$cmd = " select PN_NAME from PER_POS_NAME where trim(PN_CODE)='$PN_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PL_NAME = $data2[PN_NAME]."*Enter*".$LEVEL_NAME;

			$CMD_POSITION = trim($CMD_POSITION)?($CMD_POSITION ." ". $CMD_LEVEL_NAME):"";

			$POEM_ID = $data[POEM_ID];
			$cmd = "	select		a.POEM_NO, b.PN_NAME, c.ORG_NAME
								from		PER_POS_EMP a, PER_POS_NAME b, PER_ORG c
								where		a.POEM_ID=$POEM_ID and a.PN_CODE=b.PN_CODE and a.ORG_ID=c.ORG_ID ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$NEW_POS_NO = trim($data2[POEM_NO]);
			$NEW_PL_NAME = trim($data2[PN_NAME]);
			$NEW_ORG_NAME = trim($data2[ORG_NAME]);

			$NEW_PL_NAME = trim($NEW_PL_NAME)?($NEW_PL_NAME):"";
		}elseif($PER_TYPE==3){
			$EP_CODE = trim($data[EP_CODE]);
			$cmd = " select EP_NAME from PER_EMPSER_POS_NAME where trim(EP_CODE)='$EP_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PL_NAME = $data2[EP_NAME]."*Enter*".$LEVEL_NAME;

			$CMD_POSITION = trim($CMD_POSITION)?($CMD_POSITION ." ". $CMD_LEVEL_NAME):"";

			$POEMS_ID = $data[POEMS_ID];
			$cmd = "	select		a.POEMS_NO, b.EP_NAME, c.ORG_NAME
								from		PER_POS_EMPSER a, PER_EMPSER_POS_NAME b, PER_ORG c
								where		a.POEMS_ID=$POEMS_ID and a.EP_CODE=b.EP_CODE and a.ORG_ID=c.ORG_ID ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$NEW_POS_NO = trim($data2[POEMS_NO]);
			$NEW_PL_NAME = trim($data2[EP_NAME]);
			$NEW_ORG_NAME = trim($data2[ORG_NAME]);

			$NEW_PL_NAME = trim($NEW_PL_NAME)?($NEW_PL_NAME):"";
		} elseif($PER_TYPE==4){
			$TP_CODE = trim($data[TP_CODE]);
			$cmd = " select TP_NAME from PER_TEMP_POS_NAME where trim(TP_CODE)='$TP_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PL_NAME = $data2[TP_NAME]."*Enter*".$LEVEL_NAME;

			$CMD_POSITION = trim($CMD_POSITION)?($CMD_POSITION ." ". $CMD_LEVEL_NAME):"";

			$POT_ID = $data[POT_ID];
			$cmd = "	select		a.POT_NO, b.TP_NAME, c.ORG_NAME
								from		PER_POS_TEMP a, PER_TEMP_POS_NAME b, PER_ORG c
								where		a.POT_ID=$POT_ID and a.TP_CODE=b.TP_CODE and a.ORG_ID=c.ORG_ID ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$NEW_POS_NO = trim($data2[POT_NO]);
			$NEW_PL_NAME = trim($data2[TP_NAME]);
			$NEW_ORG_NAME = trim($data2[ORG_NAME]);

			$NEW_PL_NAME = trim($NEW_PL_NAME)?($NEW_PL_NAME):"";
		} // end if
		
		//หาวันดำรงตำแหน่งปัจจุบัน
/*		if($DPISDB=="odbc"){  
			$cmd = " select MAX(LEFT(POH_EFFECTIVEDATE,10)) as POH_EFFECTIVEDATE  from PER_POSITIONHIS where PER_ID='$PER_ID' order by LEFT(POH_EFFECTIVEDATE,10) desc";
		}elseif($DPISDB=="oci8"){
				$cmd = " select MAX(SUBSTR(POH_EFFECTIVEDATE,1,10)) as POH_EFFECTIVEDATE  from PER_POSITIONHIS where PER_ID='$PER_ID' order by SUBSTR(POH_EFFECTIVEDATE,1,10) desc";
		}elseif($DPISDB=="mysql"){
				$cmd = " select MAX(LEFT(POH_EFFECTIVEDATE,10)) as POH_EFFECTIVEDATE  from PER_POSITIONHIS where PER_ID='$PER_ID' order by LEFT(POH_EFFECTIVEDATE,10) desc";
		} // end if */
		if ($MFA_FLAG==1 && $CMD_PM_CODE) 
			$cmd = " select POH_EFFECTIVEDATE as POH_EFFECTIVEDATE
							from   PER_POSITIONHIS a, PER_MOVMENT b
							where PER_ID=$PER_ID and a.MOV_CODE=b.MOV_CODE and LEVEL_NO='$CMD_LEVEL' and PM_CODE='$CMD_PM_CODE' and  
										(MOV_SUB_TYPE=1 or MOV_SUB_TYPE=2 or MOV_SUB_TYPE=3 or MOV_SUB_TYPE=6 or MOV_SUB_TYPE=10 or MOV_SUB_TYPE=11)
							order by POH_EFFECTIVEDATE ";
		else
			$cmd = " select POH_EFFECTIVEDATE as POH_EFFECTIVEDATE
							from   PER_POSITIONHIS a, PER_MOVMENT b
							where PER_ID=$PER_ID and a.MOV_CODE=b.MOV_CODE and LEVEL_NO='$CMD_LEVEL' and 
										(MOV_SUB_TYPE=1 or MOV_SUB_TYPE=2 or MOV_SUB_TYPE=3 or MOV_SUB_TYPE=6 or MOV_SUB_TYPE=10 or MOV_SUB_TYPE=11)
							order by POH_EFFECTIVEDATE ";
		$db_dpis2->send_cmd($cmd);
//		echo $cmd;
		$data2 = $db_dpis2->get_array();
		$POH_EFFECTIVEDATE = show_date_format($data2[POH_EFFECTIVEDATE],$CMD_DATE_DISPLAY);

		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][order] = $data_row;
		//$arr_content[$data_count][name] = $PN_NAME . $PER_NAME ." ". $PER_SURNAME ."*Enter*(". $PER_BIRTHDATE .")*Enter*(".card_no_format($PER_CARDNO,$CARD_NO_DISPLAY).")";
		$arr_content[$data_count][name] = $PN_NAME . $PER_NAME ." ". $PER_SURNAME;
		if ($CARDNO_FLAG==1) $arr_content[$data_count][name] .= "*Enter* (".(($NUMBER_DISPLAY==2)?convert2thaidigit($cardID):$cardID).")"; //ถ้าเลือกโชวเลขบัตรหลังชื่อ
		$arr_content[$data_count][educate] = $EN_NAME . "*Enter*".($EM_NAME?"$EM_NAME":"") ."*Enter*".($INS_NAME?"$INS_NAME":"")."*Enter*".$EDU_ENDDATE;
		$arr_content[$data_count][position] = $PL_NAME;	//ตน.
		$arr_content[$data_count][cmd_acc_no] = $CMD_ACC_NO;
		$arr_content[$data_count][cmd_account] = $CMD_ACCOUNT;
		
		$arr_content[$data_count][cmd_position] = $CMD_POSITION;
		$arr_content[$data_count][cmd_position_type] = $CMD_POSITION_TYPE;
		$arr_content[$data_count][cmd_position_level] = $CMD_LEVEL_NAME;
		$arr_content[$data_count][cmd_position_no_name] = $CMD_POS_NO_NAME;
		$arr_content[$data_count][cmd_position_no] = $CMD_POS_NO;
		$arr_content[$data_count][cmd_org3] = $CMD_ORG3;
		$arr_content[$data_count][cmd_old_salary] = $CMD_OLD_SALARY?number_format($CMD_OLD_SALARY):"";
		$arr_content[$data_count][retire_date] = $PER_RETIREDATE;
		$arr_content[$data_count][effective_date] = $POH_EFFECTIVEDATE;
	
		$arr_content[$data_count][new_position] = $NEW_PL_NAME." ".($NEW_ORG_NAME?"$NEW_ORG_NAME":"");
		$arr_content[$data_count][new_position_type] = $NEW_POSITION_TYPE;
		$arr_content[$data_count][new_position_level] = $NEW_LEVEL_NAME;
		$arr_content[$data_count][new_pos_no] = $NEW_POS_NO;
		$arr_content[$data_count][cmd_salary] = $CMD_SALARY?number_format($CMD_SALARY):"-";
		
		$arr_content[$data_count][cmd_date] = $CMD_DATE;
		$arr_content[$data_count][cmd_date2] = $CMD_DATE2;
//		$arr_content[$data_count][cmd_note] = $CMD_NOTE1 . ($CMD_NOTE2?("*Enter*".$CMD_NOTE2):"");
		$arr_content[$data_count][cmd_note1] = $CMD_NOTE1;
		$arr_content[$data_count][cmd_note2] = $CMD_NOTE2;
		
		$data_count++;
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
	//new format**********************************************************
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
			$POSITION = $arr_content[$data_count][position];
			$CMD_ACC_NO = $arr_content[$data_count][cmd_acc_no];
			$CMD_ACCOUNT = $arr_content[$data_count][cmd_account];
			$CMD_POSITION = $arr_content[$data_count][cmd_position];
			$CMD_POSITION_TYPE = $arr_content[$data_count][cmd_position_type];
			$CMD_LEVEL_NAME=$arr_content[$data_count][cmd_position_level];
			$CMD_POSITION_NO_NAME = $arr_content[$data_count][cmd_position_no_name];
			$CMD_POSITION_NO = $arr_content[$data_count][cmd_position_no];
			$CMD_ORG3 = $arr_content[$data_count][cmd_org3];
			$CMD_OLD_SALARY = $arr_content[$data_count][cmd_old_salary];
			$PER_RETIREDATE = $arr_content[$data_count][retire_date];
			$POH_EFFECTIVEDATE = $arr_content[$data_count][effective_date];
			$NEW_POSITION = $arr_content[$data_count][new_position];
			$NEW_POSITION_TYPE = $arr_content[$data_count][new_position_type];
			$NEW_POSITION_LEVEL = $arr_content[$data_count][new_position_level];
			$NEW_LEVEL_NAME = $arr_content[$data_count][new_position_level];
			$NEW_POS_NO = $arr_content[$data_count][new_pos_no];
			$CMD_SALARY = $arr_content[$data_count][cmd_salary];
			$CMD_DATE = $arr_content[$data_count][cmd_date];
			$CMD_DATE2 = $arr_content[$data_count][cmd_date2];
//			$CMD_NOTE = $arr_content[$data_count][cmd_note];
			$CMD_NOTE1 = $arr_content[$data_count][cmd_note1];
			$CMD_NOTE2 = $arr_content[$data_count][cmd_note2];

			$arr_data = (array) null;
			$arr_data[] = $ORDER;
			$arr_data[] = $NAME;
			$arr_data[] = $NEW_POSITION;
			$arr_data[] = $CMD_POSITION_TYPE;
			$arr_data[] = $CMD_LEVEL_NAME;
			$arr_data[] =  $CMD_POSITION_NO_NAME.$CMD_POSITION_NO;
			$arr_data[] =  $CMD_OLD_SALARY;
			$arr_data[] = $POH_EFFECTIVEDATE;
			$arr_data[] = $EDUCATE;
			$arr_data[] = $CMD_DATE2;
			$arr_data[] = $CMD_SALARY;
			$arr_data[] = $CMD_DATE;
			$arr_data[] = $CMD_NOTE1;

			$data_align = array("C", "L", "L", "C", "C", "R", "C", "R", "L", "R", "R", "C", "L");
			 if ($FLAG_RTF)
			$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
		    else
			$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";

			if($CMD_NOTE2){
				$arr_data = (array) null;
				$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
				$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
				$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
				$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
				$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
				$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
				$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
				$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
				$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
				$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
				$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
				$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
				$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
				

				$data_align = array("L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L");
			     if ($FLAG_RTF)
			     $result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
				 else
				$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", "000000", "");
				if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
			} // end if
		} // end for
		if ($FLAG_RTF) {
			$RTF->close_tab(); 
			}else {
			$pdf->close_tab("");
			        } 
	}else{
	     if ($FLAG_RTF){
		$result = $RTF->add_text_line("********** ไม่มีข้อมูล **********", 7, "", "C", "", "14", "b", 0, 0);
		  }else{
		$pdf->SetFont($font,'b',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(287,10,"********** ไม่มีข้อมูล **********",0,1,'C');
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