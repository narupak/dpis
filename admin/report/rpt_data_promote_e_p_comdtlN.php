<?
	include("../../php_scripts/connect_database.php");
	include("../php_scripts/pdf_wordarray_thaicut.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
      //  die($chk_salary);//เเสดงเงิน
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	include ("rpt_data_promote_e_p_comdtlN_format.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน

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
	else $order_str = "a.CMD_ORG3, a.CMD_ORG4, a.CMD_POS_NO_NAME, a.CMD_POS_NO";

	
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
   
	$report_code = "P0310";

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
		$fname= "rpt_data_promote_e_p_comdtlN.rtf";
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
	if($DPISDB=="odbc"){
		$cmd = " select			a.PER_ID, b.PN_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE, b.PER_TYPE, b.LEVEL_NO,
											a.CMD_EDUCATE, a.CMD_POSITION, a.CMD_ORG3, a.CMD_ORG4, a.CMD_ORG5, a.CMD_OLD_SALARY, a.CMD_LEVEL, 
											a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID, a.LEVEL_NO as NEW_LEVEL_NO, a.CMD_SALARY,
											a.CMD_DATE, a.CMD_NOTE1, a.CMD_NOTE2, CMD_POS_NO_NAME, CMD_POS_NO, a.CMD_NOW, a.PM_CODE
						 from			PER_COMDTL a, PER_PERSONAL b
						 where		a.COM_ID=$COM_ID and a.PER_ID=b.PER_ID 
						 order by 	$order_str ";	
	}elseif($DPISDB=="oci8"){
		$cmd = " select			a.PER_ID, b.PN_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE, b.PER_TYPE, b.LEVEL_NO,
											a.CMD_EDUCATE, a.CMD_POSITION, a.CMD_ORG3, a.CMD_ORG4, a.CMD_ORG5, a.CMD_OLD_SALARY, a.CMD_LEVEL,
											a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID, a.LEVEL_NO as NEW_LEVEL_NO, a.CMD_SALARY,
											a.CMD_DATE, a.CMD_NOTE1, a.CMD_NOTE2, CMD_POS_NO_NAME, CMD_POS_NO, a.CMD_NOW, a.PM_CODE
						 from			PER_COMDTL a, PER_PERSONAL b
						 where		a.COM_ID=$COM_ID and a.PER_ID=b.PER_ID 
						 order by 	$order_str ";	
	}elseif($DPISDB=="mysql"){
		$cmd = " select			a.PER_ID, b.PN_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE, b.PER_TYPE, b.LEVEL_NO,
											a.CMD_EDUCATE, a.CMD_POSITION, a.CMD_ORG3, a.CMD_ORG4, a.CMD_ORG5, a.CMD_OLD_SALARY, a.CMD_LEVEL, 
											a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID, a.LEVEL_NO as NEW_LEVEL_NO, a.CMD_SALARY,
											a.CMD_DATE, a.CMD_NOTE1, a.CMD_NOTE2, CMD_POS_NO_NAME, CMD_POS_NO, a.CMD_NOW, a.PM_CODE
						 from			PER_COMDTL a, PER_PERSONAL b
						 where		a.COM_ID=$COM_ID and a.PER_ID=b.PER_ID 
						 order by 	$order_str ";
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
		if ($CARDNO_FLAG==1){ // ถ้าเลือกโชวเลขบัตรหลังชื่อ
		$PER_CARDNO = $data[PER_CARDNO];
		$cardID = card_no_format($PER_CARDNO,$CARD_NO_DISPLAY);
		}
		$PER_BIRTHDATE = show_date_format($data[PER_BIRTHDATE],$CMD_DATE_DISPLAY);
		$PER_TYPE = $data[PER_TYPE];
		$LEVEL_NO = trim($data[LEVEL_NO]);
		$CMD_OLD_SALARY = $data[CMD_OLD_SALARY];
        
		
		$EN_NAME = (array) null;
		$EN_SHORTNAME = (array) null;
		$EM_NAME = (array) null;
		$INS_NAME = (array) null;
		$ARR_EN_CODE = explode(",",$data[CMD_EDUCATE]);
		for($i = 0; $i < count($ARR_EN_CODE); $i++) {
			if($DPISDB=="odbc"){
				$cmd = "	select		b.EN_NAME, b.EN_SHORTNAME, c.EM_NAME, d.INS_NAME, EDU_INSTITUTE, EDU_HONOR, CT_CODE_EDU
									from		( 
													(
														PER_EDUCATE a
														left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
													) left join PER_EDUCMAJOR c on (a.EM_CODE=c.EM_CODE)
												) left join PER_INSTITUTE d on (a.INS_CODE=d.INS_CODE)
									where		a.PER_ID = $PER_ID and a.EN_CODE = ".$ARR_EN_CODE[$i]." order by EDU_SEQ ";
			}elseif($DPISDB=="oci8"){
				$cmd = "	select		b.EN_NAME, b.EN_SHORTNAME, c.EM_NAME, d.INS_NAME, EDU_INSTITUTE, EDU_HONOR, CT_CODE_EDU
									from		PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCMAJOR c, PER_INSTITUTE d
									where		a.EN_CODE=b.EN_CODE(+) and a.EM_CODE=c.EM_CODE(+) and a.INS_CODE=d.INS_CODE(+)
													and a.PER_ID = $PER_ID and a.EN_CODE = ".$ARR_EN_CODE[$i]." order by EDU_SEQ ";
			}elseif($DPISDB=="mysql"){
				$cmd = "	select		b.EN_NAME, b.EN_SHORTNAME, c.EM_NAME, d.INS_NAME, EDU_INSTITUTE, EDU_HONOR, CT_CODE_EDU
									from		( 
													(
														PER_EDUCATE a
														left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
													) left join PER_EDUCMAJOR c on (a.EM_CODE=c.EM_CODE)
												) left join PER_INSTITUTE d on (a.INS_CODE=d.INS_CODE)
								where a.PER_ID = $PER_ID and a.EN_CODE = ".$ARR_EN_CODE[$i]." order by EDU_SEQ ";
			} // end if
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			while ($data2 = $db_dpis2->get_array()) {
				$EN_NAME[] = trim($data2[EN_NAME]);
				if (!trim($data2[EN_SHORTNAME])) {
					$EN_SHORTNAME[] = trim($data2[EN_NAME]);
				} else {
					$EN_SHORTNAME[] = trim($data2[EN_SHORTNAME]);
				}
				if (trim($data2[EM_NAME])) {
					$EM_NAME[] = "(".trim($data2[EM_NAME]).")";
				}
				if (trim($data2[INS_NAME])) {
					//$INS_NAME[] = trim($data2[INS_NAME]);
				} else {
					$INS_NAME[] = trim($data2[EDU_INSTITUTE]);
				}
				$EDU_HONOR = trim($data2[EDU_HONOR]);
				if ($EDU_HONOR && strpos($EDUCATION_NAME,"เกียรตินิยม") !== true) $EDU_HONOR = "เกียรตินิยม" . $EDU_HONOR;
				$CT_CODE_EDU = trim($data2[CT_CODE_EDU]);
				$cmd = " select CT_NAME from PER_COUNTRY where CT_CODE='$CT_CODE_EDU' ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$CT_NAME = $data2[CT_NAME];
				if ($CT_NAME=="ไทย") $CT_NAME = "";
			}
		} // end for loop
		
		$CMD_LEVEL = trim($data[CMD_LEVEL]);
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
		if ($CMD_PM_NAME) {
			$cmd = " select PM_CODE from PER_MGT where trim(PM_NAME)='$CMD_PM_NAME' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$CMD_PM_CODE = trim($data2[PM_CODE]);
		}

		$CMD_POS_NO_NAME = trim($data[CMD_POS_NO_NAME]); 
		$CMD_POS_NO = trim($data[CMD_POS_NO]); 
		if ($print_order_by==1) {
			$CMD_ORG3 = $data[CMD_ORG3];
			$CMD_ORG4 = $data[CMD_ORG4];
			$CMD_ORG5 = $data[CMD_ORG5];
		}
		$CMD_OLD_SALARY = $data[CMD_OLD_SALARY];
		$NEW_LEVEL_NO = trim($data[NEW_LEVEL_NO]);
		$CMD_SALARY = $data[CMD_SALARY];
		$CMD_DATE = show_date_format($data[CMD_DATE],$CMD_DATE_DISPLAY);
		$CMD_NOTE1 = trim($data[CMD_NOTE1]);
		$CMD_NOTE2 = trim($data[CMD_NOTE2]);
		$CMD_NOW = trim($data[CMD_NOW]);
		if ($CMD_NOW=="Y") $CMD_DATE = "บัดนี้เป็นต้นไป";

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
			$cmd = " select PM_CODE, PT_CODE from PER_POSITION  
							where trim(POS_NO)='$CMD_POS_NO' and DEPARTMENT_ID = $DEPARTMENT_ID ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$CMD_PT_CODE = trim($data2[PT_CODE]);
			if (!$CMD_PM_CODE) $CMD_PM_CODE = trim($data2[PM_CODE]);
			
			$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE)='$CMD_PT_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$CMD_PT_NAME = trim($data2[PT_NAME]);

			$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$CMD_PM_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			if (!$CMD_PM_NAME) $CMD_PM_NAME = trim($data2[PM_NAME]);

			//$CMD_POSITION = pl_name_format($CMD_POSITION, $CMD_PM_NAME, $CMD_PT_NAME, $CMD_LEVEL);
                            $CMD_POSITION = pl_name_format($CMD_POSITION, $CMD_PM_NAME, $CMD_PT_NAME);

			$POS_ID = $data[POS_ID];
			$cmd = "	select		a.POS_NO, a.POS_NO_NAME, b.PL_NAME, c.ORG_NAME, a.PT_CODE, a.ORG_ID_1, a.ORG_ID_2, a.PM_CODE
						from		PER_POSITION a, PER_LINE b, PER_ORG c
						where		a.POS_ID=$POS_ID and a.PL_CODE=b.PL_CODE and a.ORG_ID=c.ORG_ID ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$NEW_POS_NO = trim($data2[POS_NO_NAME]).trim($data2[POS_NO]);
			$NEW_PL_NAME = trim($data2[PL_NAME]);
			$NEW_ORG_NAME = trim($data2[ORG_NAME]);
			$NEW_PT_CODE = trim($data2[PT_CODE]);
			$NEW_PM_CODE = trim($data[PM_CODE]);

			$NEW_ORG_ID_1 = $data2[ORG_ID_1];
			$NEW_ORG_ID_2 = $data2[ORG_ID_2];
			
			$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE)='$NEW_PT_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$NEW_PT_NAME = trim($data2[PT_NAME]);
			
			$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$NEW_PM_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$NEW_PM_NAME = trim($data2[PM_NAME]);
			
			//$NEW_PL_NAME = pl_name_format($NEW_PL_NAME, $NEW_PM_NAME, $NEW_PT_NAME, $NEW_LEVEL_NO);
                        $NEW_PL_NAME = pl_name_format($NEW_PL_NAME, $NEW_PM_NAME, $NEW_PT_NAME);
		}elseif($PER_TYPE==2){
			//$CMD_POSITION = trim($CMD_POSITION)?($CMD_POSITION ." ". $CMD_LEVEL_NAME):"";
                            $CMD_POSITION = trim($CMD_POSITION)?($CMD_POSITION ):"";

			$POEM_ID = $data[POEM_ID];
			$cmd = "	select		a.POEM_NO, a.POEM_NO_NAME, b.PN_NAME, c.ORG_NAME, a.ORG_ID_1, a.ORG_ID_2
								from		PER_POS_EMP a, PER_POS_NAME b, PER_ORG c
								where		a.POEM_ID=$POEM_ID and a.PN_CODE=b.PN_CODE and a.ORG_ID=c.ORG_ID ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$NEW_POS_NO = trim($data2[POEM_NO_NAME]).trim($data2[POEM_NO]);
			$NEW_PL_NAME = trim($data2[PN_NAME]);
			$NEW_ORG_NAME = trim($data2[ORG_NAME]);

			$NEW_ORG_ID_1 = $data2[ORG_ID_1];
			$NEW_ORG_ID_2 = $data2[ORG_ID_2];
		}elseif($PER_TYPE==3){
			//$CMD_POSITION = trim($CMD_POSITION)?($CMD_POSITION ." ". $CMD_LEVEL_NAME):"";
                        $CMD_POSITION = trim($CMD_POSITION)?($CMD_POSITION ):"";

			$POEMS_ID = $data[POEMS_ID];
			$cmd = "	select		a.POEMS_NO, a.POEMS_NO_NAME, b.EP_NAME, c.ORG_NAME, a.ORG_ID_1, a.ORG_ID_2
								from		PER_POS_EMPSER a, PER_EMPSER_POS_NAME b, PER_ORG c
								where		a.POEMS_ID=$POEMS_ID and a.EP_CODE=b.EP_CODE and a.ORG_ID=c.ORG_ID ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$NEW_POS_NO = trim($data2[POEMS_NO_NAME]).trim($data2[POEMS_NO]);
			$NEW_PL_NAME = trim($data2[EP_NAME]);
			$NEW_ORG_NAME = trim($data2[ORG_NAME]);

			$NEW_ORG_ID_1 = $data2[ORG_ID_1];
			$NEW_ORG_ID_2 = $data2[ORG_ID_2];
		}elseif($PER_TYPE==4){
			//$CMD_POSITION = trim($CMD_POSITION)?($CMD_POSITION ." ". $CMD_LEVEL_NAME):"";
                        $CMD_POSITION = trim($CMD_POSITION)?($CMD_POSITION ):"";

			$POT_ID = $data[POT_ID];
			$cmd = "	select		a.POT_NO, a.POT_NO_NAME, b.TP_NAME, c.ORG_NAME, a.ORG_ID_1, a.ORG_ID_2
								from		PER_POS_TEMP a, PER_TEMP_POS_NAME b, PER_ORG c
								where		a.POT_ID=$POT_ID and a.TP_CODE=b.TP_CODE and a.ORG_ID=c.ORG_ID ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$NEW_POS_NO = trim($data2[POT_NO_NAME]).trim($data2[POT_NO]);
			$NEW_PL_NAME = trim($data2[TP_NAME]);
			$NEW_ORG_NAME = trim($data2[ORG_NAME]);

			$NEW_ORG_ID_1 = $data2[ORG_ID_1];
			$NEW_ORG_ID_2 = $data2[ORG_ID_2];
		} // end if

		$NEW_ORG_NAME_1 = "";
		if($NEW_ORG_ID_1){
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$NEW_ORG_ID_1 ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$NEW_ORG_NAME_1 = $data2[ORG_NAME];
		}
		$NEW_ORG_NAME_2 = "";
		if($NEW_ORG_ID_2){
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$NEW_ORG_ID_2 ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$NEW_ORG_NAME_2 = $data2[ORG_NAME];
		}
		if ($CMD_ORG3=="-") $CMD_ORG3 = "";
		if ($CMD_ORG4=="-") $CMD_ORG4 = "";
		if ($CMD_ORG5=="-") $CMD_ORG5 = "";
		if ($NEW_ORG_NAME=="-") $NEW_ORG_NAME = "";
		if ($NEW_ORG_NAME_1=="-") $NEW_ORG_NAME_1 = "";
		if ($NEW_ORG_NAME_2=="-") $NEW_ORG_NAME_2 = "";
		if ($print_order_by==2) {
			if($CMD_ORG3 != trim($data[CMD_ORG3]) && trim($data[CMD_ORG3]) && trim($data[CMD_ORG3]) != "-"){
				$CMD_ORG3 = trim($data[CMD_ORG3]);

				$arr_content[$data_count][type] = "ORG";
				$arr_content[$data_count][org_name] = $CMD_ORG3;
				$arr_content[$data_count][new_org_name] = $NEW_ORG_NAME;

				$data_count++;
			} // end if
		
			if($CMD_ORG4 != trim($data[CMD_ORG4]) && trim($data[CMD_ORG4]) && trim($data[CMD_ORG4]) != "-"){
				$CMD_ORG4 = trim($data[CMD_ORG4]);

				$arr_content[$data_count][type] = "ORG_1";
				$arr_content[$data_count][org_name] = $CMD_ORG4;
				$arr_content[$data_count][new_org_name] = $NEW_ORG_NAME_1;

				$data_count++;
			} // end if
		}

		if ($MFA_FLAG==1 && $CMD_PM_CODE) 
			$cmd = " select POH_EFFECTIVEDATE as LEVEL_EFFECTIVEDATE
							from   PER_POSITIONHIS a, PER_MOVMENT b
							where PER_ID=$PER_ID and a.MOV_CODE=b.MOV_CODE and LEVEL_NO='$CMD_LEVEL' and PM_CODE='$CMD_PM_CODE' and  
										(MOV_SUB_TYPE=1 or MOV_SUB_TYPE=2 or MOV_SUB_TYPE=3 or MOV_SUB_TYPE=6 or MOV_SUB_TYPE=10 or MOV_SUB_TYPE=11)
							order by POH_EFFECTIVEDATE ";
		else
			$cmd = " select POH_EFFECTIVEDATE as LEVEL_EFFECTIVEDATE
							from   PER_POSITIONHIS a, PER_MOVMENT b
							where PER_ID=$PER_ID and a.MOV_CODE=b.MOV_CODE and LEVEL_NO='$CMD_LEVEL' and 
										(MOV_SUB_TYPE=1 or MOV_SUB_TYPE=2 or MOV_SUB_TYPE=3 or MOV_SUB_TYPE=6 or MOV_SUB_TYPE=10 or MOV_SUB_TYPE=11)
							order by POH_EFFECTIVEDATE ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$LEVEL_EFFECTIVEDATE = show_date_format($data2[LEVEL_EFFECTIVEDATE],$CMD_DATE_DISPLAY);
		
		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][order] = $data_row;
		if ($COMMAND_PRT==1)
			$arr_content[$data_count][name] = $PN_NAME . $PER_NAME ." ". $PER_SURNAME;
		else
			$arr_content[$data_count][name] = $PN_NAME . $PER_NAME ." ". $PER_SURNAME;
		
		if ($CARDNO_FLAG==1) $arr_content[$data_count][name] .= "*Enter* (".(($NUMBER_DISPLAY==2)?convert2thaidigit($cardID):$cardID).")"; //ถ้าเลือกโชวเลขบัตรหลังชื่อ
//		$arr_content[$data_count][educate] = $EN_NAME ."\n". ($EM_NAME?"$EM_NAME":"") ."\n". ($INS_NAME?"$INS_NAME":"");
		$arr_content[$data_count][educate] = $EN_SHORTNAME[0]."*Enter*".$EM_NAME[0]."*Enter*".$INS_NAME[0];
		if ($print_order_by==1 || $COMMAND_PRT==1) {
			if ($EN_SHORTNAME[1]) $arr_content[$data_count][educate] .= "*Enter*".$EN_SHORTNAME[1]."*Enter*".$EM_NAME[1]."*Enter*".$INS_NAME[1];
			if ($EN_SHORTNAME[2]) $arr_content[$data_count][educate] .= "*Enter*".$EN_SHORTNAME[2]."*Enter*".$EM_NAME[2]."*Enter*".$INS_NAME[2];
			if ($EN_SHORTNAME[3]) $arr_content[$data_count][educate] .= "*Enter*".$EN_SHORTNAME[3]."*Enter*".$EM_NAME[3]."*Enter*".$INS_NAME[3];
			if ($EN_SHORTNAME[4]) $arr_content[$data_count][educate] .= "*Enter*".$EN_SHORTNAME[4]."*Enter*".$EM_NAME[4]."*Enter*".$INS_NAME[4];
			if ($MFA_FLAG==1 && $EDU_HONOR) $arr_content[$data_count][educate] .= "*Enter*".trim($EDU_HONOR);
			if ($MFA_FLAG==1 && $CT_NAME) $arr_content[$data_count][educate] .= "*Enter*".trim($CT_NAME);
		}
		$arr_content[$data_count][cmd_pos_no_name] = $CMD_POS_NO_NAME;
		$arr_content[$data_count][cmd_pos_no] = $CMD_POS_NO;
		$arr_content[$data_count][cmd_position] = $CMD_POSITION;
		if ($print_order_by==1 || $COMMAND_PRT==1) {
			if ($DEPARTMENT_NAME=="กรมการปกครอง") {
				if ($CMD_OT_CODE=="03") {
					if (!$CMD_ORG5 && !$CMD_ORG4) {
						$arr_content[$data_count][cmd_position] .= "*Enter*ที่ทำการปกครอง".$CMD_ORG3." ".$CMD_ORG3;
					} else {
						if ($CMD_ORG5) $arr_content[$data_count][cmd_position] .= "*Enter*".trim($CMD_ORG5);
						if ($CMD_ORG4) $arr_content[$data_count][cmd_position] .= "*Enter*".trim($CMD_ORG4);
						if ($CMD_ORG3) $arr_content[$data_count][cmd_position] .= "*Enter*".trim($CMD_ORG3);
					}
				} else {
					if ($CMD_ORG4) $arr_content[$data_count][cmd_position] .= "*Enter*".trim($CMD_ORG4);
					if ($CMD_ORG3) $arr_content[$data_count][cmd_position] .= "*Enter*".trim($CMD_ORG3);
					if ($DEPARTMENT_NAME) $arr_content[$data_count][cmd_position] .= "*Enter*".trim($DEPARTMENT_NAME);
				}
			} else {
				if ($CMD_ORG5 && $SESS_DEPARTMENT_NAME!="กรมชลประทาน") $arr_content[$data_count][cmd_position] .= "*Enter*".trim($CMD_ORG5);
				if ($CMD_ORG4) $arr_content[$data_count][cmd_position] .= "*Enter*".trim($CMD_ORG4);
				if ($CMD_ORG3) $arr_content[$data_count][cmd_position] .= "*Enter*".trim($CMD_ORG3);
			}
		} 
		$arr_content[$data_count][cmd_position_type] = $CMD_POSITION_TYPE;
		$arr_content[$data_count][cmd_level_name] = $CMD_LEVEL_NAME;
                if($chk_salary){
                    $arr_content[$data_count][cmd_old_salary] = $CMD_OLD_SALARY?number_format($CMD_OLD_SALARY):"-";
                }else{
                    $arr_content[$data_count][cmd_old_salary] = $CMD_OLD_SALARY = "-";
                }
		
		
		$arr_content[$data_count][level_effectivedate] = $LEVEL_EFFECTIVEDATE;

//		$arr_content[$data_count][new_position] = $ ."\n". ($NEW_ORG_NAME?"$NEW_ORG_NAME":"");
		$arr_content[$data_count][new_position] = $NEW_PL_NAME;
		if ($print_order_by==1 || $COMMAND_PRT==1) {
			if ($DEPARTMENT_NAME=="กรมการปกครอง") {
				if ($NEW_OT_CODE=="03") { 
					if (!$NEW_ORG_NAME_2 && !$NEW_ORG_NAME_1) {
						$arr_content[$data_count][new_position] .= "*Enter*ที่ทำการปกครอง".$NEW_ORG_NAME." ".$NEW_ORG_NAME;
					} else {
						if ($NEW_ORG_NAME_2) $arr_content[$data_count][new_position] .= "*Enter*".trim($NEW_ORG_NAME_2);
						if ($NEW_ORG_NAME_1) $arr_content[$data_count][new_position] .= "*Enter*".trim($NEW_ORG_NAME_1);
						if ($NEW_ORG_NAME) $arr_content[$data_count][new_position] .= "*Enter*".trim($NEW_ORG_NAME);
					}
				} else { 
					if ($NEW_ORG_NAME_1) $arr_content[$data_count][new_position] .= "*Enter*".trim($NEW_ORG_NAME_1);
					if ($NEW_ORG_NAME) $arr_content[$data_count][new_position] .= "*Enter*".trim($NEW_ORG_NAME);
					if ($DEPARTMENT_NAME) $arr_content[$data_count][new_position] .= "*Enter*".trim($DEPARTMENT_NAME);
				}
			} else {
				if ($NEW_ORG_NAME_2 && $SESS_DEPARTMENT_NAME!="กรมชลประทาน") $arr_content[$data_count][new_position] .= "*Enter*".trim($NEW_ORG_NAME_2);
				if ($NEW_ORG_NAME_1) $arr_content[$data_count][new_position] .= "*Enter*".trim($NEW_ORG_NAME_1);
				if ($NEW_ORG_NAME) $arr_content[$data_count][new_position] .= "*Enter*".trim($NEW_ORG_NAME);
			}
		} 
		$arr_content[$data_count][new_position_type] = $NEW_POSITION_TYPE;
		$arr_content[$data_count][new_level_name] = $NEW_LEVEL_NAME;
		$arr_content[$data_count][new_pos_no] = $NEW_POS_NO;
                if($chk_salary){
                    $arr_content[$data_count][cmd_salary] = $CMD_SALARY?number_format($CMD_SALARY):"-";
                }else{
                    $arr_content[$data_count][cmd_salary] = $CMD_SALARY = "-";
                }

		$arr_content[$data_count][cmd_date] = $CMD_DATE;
//		$arr_content[$data_count][cmd_note] = $CMD_NOTE1 . ($CMD_NOTE2?("\n".$CMD_NOTE2):"");
		$arr_content[$data_count][cmd_note1] = $CMD_NOTE1;
		
		$data_count++;
/*
		if ($print_order_by==1 && $COMMAND_PRT!=1) {
			$arr_content[$data_count][type] = "CONTENT";
//		$arr_content[$data_count][name] = card_no_format($PER_CARDNO,$CARD_NO_DISPLAY);
//		$arr_content[$data_count][educate] = $INS_NAME;
			if($EN_SHORTNAME[1])
				$arr_content[$data_count][educate] = $EN_SHORTNAME[1]."*Enter*".$EM_NAME[1];
			if ($DEPARTMENT_NAME=="กรมการปกครอง") {
				if ($CMD_OT_CODE=="03") {
					if (!$CMD_ORG5 && !$CMD_ORG4) $arr_content[$data_count][cmd_position] = "ที่ทำการปกครอง".$CMD_ORG3." ".$CMD_ORG3;
					else $arr_content[$data_count][cmd_position] = trim($CMD_ORG5." ".$CMD_ORG4." ".$CMD_ORG3);
				} else {
					$arr_content[$data_count][cmd_position] = trim($CMD_ORG4." ".$CMD_ORG3." ".$DEPARTMENT_NAME);
				}
				if ($NEW_OT_CODE=="03") { 
					if (!$NEW_ORG_NAME_2 && !$NEW_ORG_NAME_1) $arr_content[$data_count][new_position] = "ที่ทำการปกครอง".$NEW_ORG_NAME." ".$NEW_ORG_NAME;
					else $arr_content[$data_count][new_position] = trim($NEW_ORG_NAME_2." ".$NEW_ORG_NAME_1." ".$NEW_ORG_NAME);
				} else { 
					$arr_content[$data_count][new_position] = trim($NEW_ORG_NAME_1." ".$NEW_ORG_NAME." ".$DEPARTMENT_NAME);
				}
			} else {
				$arr_content[$data_count][cmd_position] = trim($CMD_ORG5." ".$CMD_ORG4." ".$CMD_ORG3);
				$arr_content[$data_count][new_position] = trim($NEW_ORG_NAME_2." ".$NEW_ORG_NAME_1." ".$NEW_ORG_NAME);
			}
			$data_count++;
		
		} */
//		$arr_content[$data_count][cmd_note] = $CMD_NOTE2;

		if ($COMMAND_PRT!=1) {
			if($EN_SHORTNAME[1]){
				$arr_content[$data_count][type] = "CONTENT";
				$arr_content[$data_count][educate] = $EN_SHORTNAME[1]."*Enter*".$EM_NAME[1]."*Enter*".$INS_NAME[1];

				$data_count++;

				if($EN_SHORTNAME[2]){
					$arr_content[$data_count][type] = "CONTENT";
					$arr_content[$data_count][educate] = $EN_SHORTNAME[2]."*Enter*".$EM_NAME[2]."*Enter*".$INS_NAME[2];

					$data_count++;

					if($EN_SHORTNAME[3]){
						$arr_content[$data_count][type] = "CONTENT";
						$arr_content[$data_count][educate] = $EN_SHORTNAME[3]."*Enter*".$EM_NAME[3]."*Enter*".$INS_NAME[3];

						$data_count++;

						if($EN_SHORTNAME[4]){
							$arr_content[$data_count][type] = "CONTENT";
							$arr_content[$data_count][educate] = $EN_SHORTNAME[4]."*Enter*".$EM_NAME[4]."*Enter*".$INS_NAME[4];

							$data_count++;
						} // end if 4
					} // end if 3
				} // end if 2
			} // end if 1
			if ($MFA_FLAG==1 && $EDU_HONOR) $arr_content[$data_count][educate] .= "*Enter*".trim($EDU_HONOR);
			if ($MFA_FLAG==1 && $CT_NAME) $arr_content[$data_count][educate] .= "*Enter*".trim($CT_NAME);
		}
		if($CMD_NOTE2){
			$arr_content[$data_count][type] = "CONTENT";
			$arr_content[$data_count][cmd_note2] = $CMD_NOTE2;

			$data_count++;
		}
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
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
			$CMD_POSITION_TYPE = $arr_content[$data_count][cmd_position_type];
			$CMD_LEVEL_NAME=$arr_content[$data_count][cmd_level_name];
			$CMD_ORG3 = $arr_content[$data_count][cmd_org3];

			$LEVEL_EFFECTIVEDATE = $arr_content[$data_count][level_effectivedate];

			$CMD_OLD_SALARY = $arr_content[$data_count][cmd_old_salary];
			$NEW_POSITION = $arr_content[$data_count][new_position];
			$NEW_POSITION_TYPE = $arr_content[$data_count][new_position_type];
			$NEW_LEVEL_NAME=$arr_content[$data_count][new_level_name];
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
					$arr_data[] = "<**2**>$ORG_NAME";
					$arr_data[] = "<**2**>$ORG_NAME";
					$arr_data[] = "<**2**>$ORG_NAME";
					$arr_data[] = "<**3**>$NEW_ORG_NAME";
					$arr_data[] = "<**3**>$NEW_ORG_NAME";
					$arr_data[] = "<**3**>$NEW_ORG_NAME";
					$arr_data[] = "<**3**>$NEW_ORG_NAME";
					$arr_data[] = "<**3**>$NEW_ORG_NAME";
					$arr_data[] = "<**3**>$NEW_ORG_NAME";
					$arr_data[] = "<**3**>$NEW_ORG_NAME";

					$data_align = array("L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L");
					  if ($FLAG_RTF)
					$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
				         else
					$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", "000000", "");
					if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
				} // end if
			}

			if($CONTENT_TYPE=="CONTENT"){
				$arr_data = (array) null;
				$arr_data[] = $ORDER;
				$arr_data[] = $NAME;
				$arr_data[] = $EDUCATE;
				$arr_data[] = $CMD_POSITION;
				if($COM_TYPE=="5042"){
					$arr_data[] = $CMD_POS_NO_NAME.$CMD_POS_NO;
					$arr_data[] = $LEVEL_EFFECTIVEDATE;
					$arr_data[] = $CMD_OLD_SALARY;
					$arr_data[] = $NEW_POSITION;

					$data_align = array("C", "L", "L", "L", "C", "C", "R", "L", "C", "R", "C", "L");
				}else if($COM_TYPE=="5043" && $MFA_FLAG!=1){
					$arr_data[] = $CMD_POSITION_TYPE;
					$arr_data[] = $CMD_LEVEL_NAME;
					$arr_data[] = $CMD_POS_NO_NAME.$CMD_POS_NO;
					$arr_data[] = $CMD_OLD_SALARY;
					$arr_data[] = $NEW_POSITION;
					$arr_data[] = $NEW_POSITION_TYPE;
					$arr_data[] = $NEW_LEVEL_NAME;

					$data_align = array("C", "L", "L", "L", "C", "C", "C", "R", "L", "C", "C", "C", "R", "C", "L");
				}else{
					$arr_data[] = $CMD_POSITION_TYPE;
					$arr_data[] = $CMD_LEVEL_NAME;
					$arr_data[] = $CMD_POS_NO_NAME.$CMD_POS_NO;
					$arr_data[] = $CMD_OLD_SALARY;
					$arr_data[] = $LEVEL_EFFECTIVEDATE;
					$arr_data[] = $NEW_POSITION;
					$arr_data[] = $NEW_POSITION_TYPE;
					$arr_data[] = $NEW_LEVEL_NAME;

					$data_align = array("C", "L", "L", "L", "C", "C", "C", "R", "C", "L", "C", "C", "C", "R", "C", "L");
				}
				$arr_data[] = $NEW_POS_NO;
				$arr_data[] = $CMD_SALARY;
				$arr_data[] = $CMD_DATE;
				$arr_data[] = $CMD_NOTE1;
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
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";

					$data_align = array("L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L");
				     if ($FLAG_RTF)
					$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
				     else
					$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
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
			$head_text1 = ",";
			$head_width1 = "20,269";
			$head_align1 = "L,L";
			$c_function[0] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
			$c_function[1] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
			$col_function = implode(",", $c_function);
			if ($FLAG_RTF) {
			$RTF->add_header("", 0, false);	// header default
			$RTF->add_footer("", 0, false);		// footer default
			$tab_align = "center";
			$result = $RTF->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", $fmtTableHeader_col_idx, $fmtTableHeader_bgcol_idx, $COLUMN_FORMAT, $col_function, false, $tab_align);
		     } else {
			$result = $pdf->open_tab($head_text1, $head_width1, 7, "", $head_align1, "", "14", "b", "0066CC", "EEEEFF", "", $col_function, true);
			           }
			if (!$result) echo "****** error ****** on open table for $table<br>";
			
			$arr_data = (array) null;
			$arr_data[] = "หมายเหตุ : ";
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
		$result = $RTF->add_text_line("********** ไม่มีข้อมูล **********", 7, "", "C", "", "14", "b", 0, 0);
		           	}else{
		$result = $pdf->add_text_line("********** ไม่มีข้อมูล **********", 7, "", "L", "", "14", "b", 0, 0);
		                    }
		if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";
	
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