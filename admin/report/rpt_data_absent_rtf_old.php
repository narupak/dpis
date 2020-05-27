<?
	include("../../php_scripts/connect_database.php");
	include ("../php_scripts/session_start.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	// read template content
	if($_GET[ABS_ID])		$ABS_ID = $_GET[ABS_ID];
	if($_GET[AB_CODE])	$AB_CODE = $_GET[AB_CODE];
	if(trim($AB_CODE)=="01" || trim($AB_CODE)=="02" || trim($AB_CODE)=="03"){
		$filename = "rpt_data_absent_template_1.rtf";
		$ABS_NOTE = "";
	}elseif(trim($AB_CODE)=="04"){
		$filename = "rpt_data_absent_template_2.rtf";
	}
	$handle = fopen ($filename, "r");
	$rtf_contents = fread($handle, filesize($filename));
	fclose($handle);
//	echo $rtf_contents;

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if(trim($LET_DATE)){
		$arr_temp = explode("/", $LET_DATE);
		//$LET_DATE = ($arr_temp[0] + 0) ."   ". $month_full[($arr_temp[1] + 0)][TH] ."   พ.ศ.  ". $arr_temp[2];
		$LET_DATE = ($arr_temp[2])."-".($arr_temp[1] + 0)."-".($arr_temp[0] + 0);
		$LET_DATE = show_date_format($LET_DATE,$DATE_DISPLAY);
	} // end if
	
	if($DPISDB=="odbc"){
		$cmd = " select 	a.AB_CODE, b.AB_NAME, a.ABS_STARTDATE, a.ABS_ENDDATE, a.ABS_DAY, a.ABS_REASON, a.ABS_ADDRESS, 
										a.PER_ID, ABS_LETTER, a.APPROVE_FLAG, a.APPROVE_PER_ID, a.AUDIT_FLAG, a.AUDIT_PER_ID, a.CANCEL_FLAG, 
										a.REVIEW1_FLAG, a.REVIEW1_PER_ID, a.REVIEW2_FLAG, a.REVIEW2_PER_ID, d.PN_NAME, c.PER_NAME, c.PER_SURNAME, 
										c.LEVEL_NO, c.PER_TYPE, c.POS_ID, c.POEM_ID, c.POEMS_ID, c.POT_ID 
						from		PER_ABSENT a, PER_ABSENTTYPE b,	PER_PERSONAL c, PER_PRENAME d 
						where	a.AB_CODE=b.AB_CODE and a.PER_ID=c.PER_ID and c.PN_CODE=d.PN_CODE and ABS_ID = $ABS_ID ";
	}elseif($DPISDB=="oci8"){	
		$cmd = " select 	a.AB_CODE, b.AB_NAME, a.ABS_STARTDATE, a.ABS_ENDDATE, a.ABS_DAY, a.ABS_REASON, a.ABS_ADDRESS, 
										a.PER_ID, ABS_LETTER, a.APPROVE_FLAG, a.APPROVE_PER_ID, a.AUDIT_FLAG, a.AUDIT_PER_ID, a.CANCEL_FLAG, 
										a.REVIEW1_FLAG, a.REVIEW1_PER_ID, a.REVIEW2_FLAG, a.REVIEW2_PER_ID, d.PN_NAME, c.PER_NAME, c.PER_SURNAME, 
										c.LEVEL_NO, c.PER_TYPE, c.POS_ID, c.POEM_ID, c.POEMS_ID, c.POT_ID 
						from		PER_ABSENT a, PER_ABSENTTYPE b, PER_PERSONAL c, PER_PRENAME d 
						where	a.AB_CODE=b.AB_CODE and a.PER_ID=c.PER_ID and c.PN_CODE=d.PN_CODE and ABS_ID = $ABS_ID ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select 	a.AB_CODE, b.AB_NAME, a.ABS_STARTDATE, a.ABS_ENDDATE, a.ABS_DAY, a.ABS_REASON, a.ABS_ADDRESS, 
										a.PER_ID, ABS_LETTER, a.APPROVE_FLAG, a.APPROVE_PER_ID, a.AUDIT_FLAG, a.AUDIT_PER_ID, a.CANCEL_FLAG, 
										a.REVIEW1_FLAG, a.REVIEW1_PER_ID, a.REVIEW2_FLAG, a.REVIEW2_PER_ID, d.PN_NAME, c.PER_NAME, c.PER_SURNAME, 
										c.LEVEL_NO, c.PER_TYPE, c.POS_ID, c.POEM_ID, c.POEMS_ID, c.POT_ID 
						from		PER_ABSENT a, PER_ABSENTTYPE b, 	PER_PERSONAL c, PER_PRENAME d 
						where	a.AB_CODE=b.AB_CODE and a.PER_ID=c.PER_ID and c.PN_CODE=d.PN_CODE and ABS_ID = $ABS_ID ";
	} // end if
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data = $db_dpis->get_array();

	$AB_CODE = trim($data[AB_CODE]);
	$AB_NAME = trim($data[AB_NAME]);
	$TITLE = "ขอ$AB_NAME";
	
	$ABS_STARTDATE = substr($data[ABS_STARTDATE], 0, 10);
	$VC_YEAR = substr($ABS_STARTDATE,0,4) + 543;
	$ABS_STARTDATE = show_date_format($ABS_STARTDATE,$DATE_DISPLAY);

	$ABS_ENDDATE = substr($data[ABS_ENDDATE], 0, 10);
	$ABS_ENDDATE =  show_date_format($ABS_ENDDATE,$DATE_DISPLAY);

	$ABS_DAY = trim($data[ABS_DAY]);
	$ABS_REASON = trim($data[ABS_REASON]);
	$ABS_ADDRESS = trim($data[ABS_ADDRESS]);

	$TMP_REVIEW1_FLAG = trim($data[REVIEW1_FLAG]);
	$TMP_REVIEW1_PER_ID = trim($data[REVIEW1_PER_ID]);
	
	$TMP_REVIEW2_FLAG = trim($data[REVIEW2_FLAG]);
	$TMP_REVIEW2_PER_ID = trim($data[REVIEW2_PER_ID]);

	$TMP_MAIN_PER_NAME=$TMP_APPROVE_PER_NAME=$TMP_REVIEW_PER_NAME=$TMP_AUDIT_PER_NAME="____________________";
	$TMP_REVIEW_POSITION_NAME=$TMP_APPROVE_POSITION_NAME=$TMP_AUDIT_POSITION_NAME="____________________";
	
	$TMP_REVIEW_FLAG_RESULT = "รอเห็นควร/ไม่เห็นควรอนุญาต";
	if($TMP_REVIEW2_FLAG){
		if($TMP_REVIEW2_FLAG==1)				$TMP_REVIEW_FLAG_RESULT = "เห็นควรอนุญาต";
		else if($TMP_REVIEW2_FLAG==2)		$TMP_REVIEW_FLAG_RESULT = "ไม่เห็นควรอนุญาต";
	}else{
		if($TMP_REVIEW1_FLAG){
			if($TMP_REVIEW1_FLAG==1)			$TMP_REVIEW_FLAG_RESULT = "เห็นควรอนุญาต";
			else if($TMP_REVIEW1_FLAG==2)	$TMP_REVIEW_FLAG_RESULT = "ไม่เห็นควรอนุญาต";
		}
	}

	$TMP_AUDIT_FLAG = trim($data[AUDIT_FLAG]);
	$TMP_AUDIT_FLAG_RESULT = "รอตรวจสอบ";
	if($TMP_AUDIT_FLAG==1)					$TMP_AUDIT_FLAG_RESULT = "ตรวจสอบ";
	else if($TMP_AUDIT_FLAG==2)			$TMP_AUDIT_FLAG_RESULT = "ไม่ตรวจสอบ";
	$TMP_AUDIT_PER_ID = trim($data[AUDIT_PER_ID]);

	$TMP_APPROVE_FLAG = trim($data[APPROVE_FLAG]);
	$TMP_APPROVE_FLAG_RESULT = "รออนุญาต/ไม่อนุญาต";
	if($TMP_APPROVE_FLAG==1)				$TMP_APPROVE_FLAG_RESULT = "อนุญาต";
	else if($TMP_APPROVE_FLAG==2)		$TMP_APPROVE_FLAG_RESULT = "ไม่อนุญาต";
	$TMP_APPROVE_PER_ID = trim($data[APPROVE_PER_ID]);

	$TMP_CANCEL_FLAG = $data[CANCEL_FLAG];

	$PER_ID = $data[PER_ID];
	$PER_NAME = $data[PN_NAME].$data[PER_NAME]." ".$data[PER_SURNAME];
	$LEVEL_NO = trim($data[LEVEL_NO]);
	$PER_TYPE = $data[PER_TYPE];
	
	$cmd = " select VC_DAY from PER_VACATION where  VC_YEAR = '$VC_YEAR' and PER_ID = $PER_ID ";
	$db_dpis2->send_cmd($cmd);
	$data2 = $db_dpis2->get_array();
	if ($data2[VC_DAY]) {
		$TOT_DAY = $data2[VC_DAY];
		$VC_DAY = $TOT_DAY - 10;
	}

	/***
	$cmd = " select b.PN_CODE, b.PER_NAME, b.PER_SURNAME from PER_PERSONAL b 
				where  b.PER_ID = $PER_ID and b.PER_TYPE=$PER_TYPE ";
	$db_dpis2->send_cmd($cmd);
	$data2 = $db_dpis2->get_array();
	$TMP_PN_CODE = $data2[PN_CODE];
	$TMP_PER_NAME = $data2[PER_NAME];
	$TMP_PER_SURNAME = $data2[PER_SURNAME];
	
	$cmd = " select PN_NAME from PER_PRENAME where PN_CODE='$TMP_PN_CODE' ";
	$db_dpis2->send_cmd($cmd);
	$data2 = $db_dpis2->get_array();
	$TMP_PN_NAME = $data2[PN_NAME];
	
	$PER_NAME = $TMP_PN_NAME.$TMP_PER_NAME." ".$TMP_PER_SURNAME;
	***/
	
	$cmd="select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$LEVEL_NO'";
	$db_dpis2->send_cmd($cmd);
	$data3=$db_dpis2->get_array();
	$LEVEL_NAME = trim($data3[LEVEL_NAME]);
	$POSITION_LEVEL = $data3[POSITION_LEVEL];
	if (!$POSITION_LEVEL) $POSITION_LEVEL = $LEVEL_NAME;

	if($PER_TYPE==1){
		$POS_ID = $data[POS_ID];
		$cmd = "	select		a.POS_NO_NAME, a.POS_NO, b.PL_NAME, a.ORG_ID, c.ORG_NAME, c.ORG_ID_REF, a.PT_CODE, d.PT_NAME
					from		PER_POSITION a, PER_LINE b, PER_ORG c, PER_TYPE d
					where		a.POS_ID=$POS_ID and a.PL_CODE=b.PL_CODE and a.ORG_ID=c.ORG_ID and a.PT_CODE=d.PT_CODE ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$POS_NO = trim($data2[POS_NO_NAME]).trim($data2[POS_NO]);
		$PL_NAME = trim($data2[PL_NAME]);
		$ORG_ID = $data2[ORG_ID];
		$ORG_NAME = trim($data2[ORG_NAME]);
		$DEPARTMENT_ID = $data2[ORG_ID_REF];
		$PT_CODE = trim($data2[PT_CODE]);
		$PT_NAME = trim($data2[PT_NAME]);
		$POSITION_NAME = trim($PL_NAME)?($PL_NAME . $POSITION_LEVEL . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"")):"";
	}elseif($PER_TYPE==2){
		$POEM_ID = $data[POEM_ID];
		$cmd = "	select		a.POEM_NO_NAME, a.POEM_NO, b.PN_NAME, a.ORG_ID, c.ORG_NAME, c.ORG_ID_REF
							from		PER_POS_EMP a, PER_POS_NAME b, PER_ORG c
							where		a.POEM_ID=$POEM_ID and a.PN_CODE=b.PN_CODE and a.ORG_ID=c.ORG_ID ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$POS_NO = trim($data2[POEM_NO_NAME]).trim($data2[POEM_NO]);
		$PL_NAME = trim($data2[PN_NAME]);
		$ORG_ID = $data2[ORG_ID];
		$ORG_NAME = trim($data2[ORG_NAME]);
		$DEPARTMENT_ID = $data2[ORG_ID_REF];
		$POSITION_NAME = trim($PL_NAME)?($PL_NAME . $POSITION_LEVEL):"";
	}elseif($PER_TYPE==3){
		$POEMS_ID = $data[POEMS_ID];
		$cmd = "	select		a.POEMS_NO_NAME, a.POEMS_NO, b.EP_NAME, a.ORG_ID, c.ORG_NAME, c.ORG_ID_REF
							from		PER_POS_EMPSER a, PER_EMPSER_POS_NAME b, PER_ORG c
							where		a.POEMS_ID=$POEMS_ID and a.EP_CODE=b.EP_CODE and a.ORG_ID=c.ORG_ID ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$POS_NO = trim($data2[POEMS_NO_NAME]).trim($data2[POEMS_NO]);
		$PL_NAME = trim($data2[EP_NAME]);
		$ORG_ID = $data2[ORG_ID];
		$ORG_NAME = trim($data2[ORG_NAME]);
		$DEPARTMENT_ID = $data2[ORG_ID_REF];
		$POSITION_NAME = trim($PL_NAME)?($PL_NAME . $POSITION_LEVEL):"";
	} elseif($PER_TYPE==4){
		$POT_ID = $data[POT_ID];
		$cmd = "	select		a.POT_NO_NAME, a.POT_NO, b.TP_NAME, a.ORG_ID, c.ORG_NAME, c.ORG_ID_REF
							from		PER_POS_TEMP a, PER_TEMP_POS_NAME  b, PER_ORG c
							where		a.POT_ID=$POT_ID and a.TP_CODE=b.TP_CODE and a.ORG_ID=c.ORG_ID ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$POS_NO = trim($data2[POT_NO_NAME]).trim($data2[POT_NO]);
		$PL_NAME = trim($data2[TP_NAME]);
		$ORG_ID = $data2[ORG_ID];
		$ORG_NAME = trim($data2[ORG_NAME]);
		$DEPARTMENT_ID = $data2[ORG_ID_REF];
		$POSITION_NAME = trim($PL_NAME)?($PL_NAME . $POSITION_LEVEL):"";
	} // end if
	
	$cmd = " select ORG_ID_REF, ORG_NAME from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
	if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	$db_dpis2->send_cmd($cmd);
	$data2 = $db_dpis2->get_array();
	$DEPARTMENT_NAME = $data2[ORG_NAME];
	$MINISTRY_ID = $data2[ORG_ID_REF];
	
	$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
	if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	$db_dpis2->send_cmd($cmd);
	$data2 = $db_dpis2->get_array();
	$MINISTRY_NAME = $data2[ORG_NAME];

if($TMP_REVIEW2_PER_ID){	// ถ้ามีคนที่เหนือ ให้เอาคนนี้ ถ้าไม่มีเอาคนขั้นต้น
	$cmd_review = " select b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.POS_ID, a.LEVEL_NO from PER_PERSONAL a, PER_PRENAME b where a.PER_ID=$TMP_REVIEW2_PER_ID and a.PN_CODE=b.PN_CODE ";
}else{ // end if($TMP_REVIEW2_PER_ID)
	if($TMP_REVIEW1_PER_ID){
		$cmd_review = " select b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.POS_ID, a.LEVEL_NO from PER_PERSONAL a, PER_PRENAME b where a.PER_ID=$TMP_REVIEW1_PER_ID and a.PN_CODE=b.PN_CODE ";
	} 
}
if($cmd_review){
	$db_dpis2->send_cmd($cmd_review);
	$data2 = $db_dpis2->get_array();
	$TMP_REVIEW_PER_NAME = $data2[PN_NAME].$data2[PER_NAME]." ".$data2[PER_SURNAME];
	$TMP_REVIEW_POS_ID = $data2[POS_ID];
	$TMP_REVIEW_LEVEL_NO = trim($data2[LEVEL_NO]);

	$cmd="select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$TMP_REVIEW_LEVEL_NO'";
	$db_dpis2->send_cmd($cmd);
	$data3=$db_dpis2->get_array();
	$TMP_REVIEW_LEVEL_NAME = trim($data3[LEVEL_NAME]);
	$TMP_REVIEW_POSITION_LEVEL = $data3[POSITION_LEVEL];
	if (!$TMP_REVIEW_POSITION_LEVEL) $TMP_REVIEW_POSITION_LEVEL = $TMP_REVIEW_LEVEL_NAME;
	$cmd = "	select		a.POS_NO_NAME, a.POS_NO, b.PL_NAME, a.ORG_ID, c.ORG_NAME, c.ORG_ID_REF, a.PT_CODE, d.PT_NAME
				from		PER_POSITION a, PER_LINE b, PER_ORG c, PER_TYPE d
				where		a.POS_ID=$TMP_REVIEW_POS_ID and a.PL_CODE=b.PL_CODE and a.ORG_ID=c.ORG_ID and a.PT_CODE=d.PT_CODE ";
	if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	$db_dpis2->send_cmd($cmd);
	$data2 = $db_dpis2->get_array();
	$TMP_REVIEW_PL_NAME = trim($data2[PL_NAME]);
	$TMP_REVIEW_PT_CODE = trim($data2[PT_CODE]);
	$TMP_REVIEW_PT_NAME = trim($data2[PT_NAME]);
	$TMP_REVIEW_POSITION_NAME = trim($TMP_REVIEW_PL_NAME)?($TMP_REVIEW_PL_NAME . $TMP_REVIEW_POSITION_LEVEL . (($TMP_REVIEW_PT_NAME != "ทั่วไป" && $TMP_REVIEW_LEVEL_NO >= 6)?"$TMP_REVIEW_PT_NAME":"")):"";
}
//--------------------

	if($TMP_AUDIT_PER_ID){	
		$cmd = " select b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.POS_ID, a.LEVEL_NO from PER_PERSONAL a, PER_PRENAME b where a.PER_ID=$TMP_AUDIT_PER_ID and a.PN_CODE=b.PN_CODE ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$TMP_AUDIT_PER_NAME = $data2[PN_NAME].$data2[PER_NAME]." ".$data2[PER_SURNAME];
		$TMP_AUDIT_POS_ID = $data2[POS_ID];
		$TMP_AUDIT_LEVEL_NO = trim($data2[LEVEL_NO]);
		
		$cmd="select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$TMP_AUDIT_LEVEL_NO'";
		$db_dpis2->send_cmd($cmd);
		$data3=$db_dpis2->get_array();
		$TMP_AUDIT_NAME = trim($data3[LEVEL_NAME]);
		$TMP_AUDIT_POSITION_LEVEL = $data3[POSITION_LEVEL];
		if (!$TMP_AUDIT_POSITION_LEVEL) $TMP_AUDIT_POSITION_LEVEL = $TMP_AUDIT_LEVEL_NAME;
		$cmd = "	select		a.POS_NO_NAME, a.POS_NO, b.PL_NAME, a.ORG_ID, c.ORG_NAME, c.ORG_ID_REF, a.PT_CODE, d.PT_NAME
					from		PER_POSITION a, PER_LINE b, PER_ORG c, PER_TYPE d
					where		a.POS_ID=$TMP_AUDIT_POS_ID and a.PL_CODE=b.PL_CODE and a.ORG_ID=c.ORG_ID and a.PT_CODE=d.PT_CODE ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$TMP_AUDIT_PL_NAME = trim($data2[PL_NAME]);
		$TMP_AUDIT_PT_CODE = trim($data2[PT_CODE]);
		$TMP_AUDIT_PT_NAME = trim($data2[PT_NAME]);
		$TMP_AUDIT_POSITION_NAME = trim($TMP_AUDIT_PL_NAME)?($TMP_AUDIT_PL_NAME . $TMP_AUDIT_POSITION_LEVEL . (($TMP_AUDIT_PT_NAME != "ทั่วไป" && $TMP_AUDIT_LEVEL_NO >= 6)?"$TMP_AUDIT_PT_NAME":"")):"";	
	}
//--------------------

	$cmd = " select b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.POS_ID, a.LEVEL_NO from PER_PERSONAL a, PER_PRENAME b where a.PER_ID=$TMP_APPROVE_PER_ID and a.PN_CODE=b.PN_CODE ";
	$db_dpis2->send_cmd($cmd);
	$data2 = $db_dpis2->get_array();
	$TMP_APPROVE_PER_NAME = $data2[PN_NAME].$data2[PER_NAME]." ".$data2[PER_SURNAME];
	$TMP_APPROVE_POS_ID = $data2[POS_ID];
	$TMP_APPROVE_LEVEL_NO = trim($data2[LEVEL_NO]);
	
	$cmd="select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$TMP_APPROVE_LEVEL_NO'";
	$db_dpis2->send_cmd($cmd);
	$data3=$db_dpis2->get_array();
	$TMP_APPROVE_LEVEL_NAME = trim($data3[LEVEL_NAME]);
	$TMP_APPROVE_POSITION_LEVEL = $data3[POSITION_LEVEL];
	if (!$TMP_APPROVE_POSITION_LEVEL) $TMP_APPROVE_POSITION_LEVEL = $TMP_APPROVE_LEVEL_NAME;
	$cmd = "select		a.POS_NO_NAME, a.POS_NO, b.PL_NAME, a.ORG_ID_2, a.ORG_ID_1, a.ORG_ID, c.ORG_NAME, c.ORG_ID_REF, a.PT_CODE, d.PT_NAME, a.PM_CODE from PER_POSITION a, PER_LINE b, PER_ORG c, PER_TYPE d where		a.POS_ID=$TMP_APPROVE_POS_ID and a.PL_CODE=b.PL_CODE and a.ORG_ID=c.ORG_ID and a.PT_CODE=d.PT_CODE ";
	if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	$db_dpis2->send_cmd($cmd);
	$data2 = $db_dpis2->get_array();
	$TMP_APPROVE_PL_NAME = trim($data2[PL_NAME]);
	$TMP_APPROVE_PT_CODE = trim($data2[PT_CODE]);
	$TMP_APPROVE_PT_NAME = trim($data2[PT_NAME]);
	$TMP_APPROVE_PM_CODE = trim($data2[PM_CODE]);
	$TMP_APPROVE_ORG_NAME= trim($data2[ORG_NAME]);
	$TMP_APPROVE_ORG_ID_1 = trim($data2[ORG_ID_1]);
	$TMP_APPROVE_ORG_ID_2 = trim($data2[ORG_ID_2]);
	
	if ($TMP_APPROVE_ORG_ID_1) {
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$TMP_APPROVE_ORG_ID_1 ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$TMP_APPROVE_ORG_NAME_1 = trim($data2[ORG_NAME]);
		if ($data2[ORG_NAME]=="โรงเรียน") {
			if ($TMP_APPROVE_ORG_ID_2) {
				$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$TMP_APPROVE_ORG_ID_2 ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
			}
		} 
		if ($data2[ORG_NAME]!="-") $TMP_APPROVE_ORG_NAME = $TMP_APPROVE_ORG_NAME . "<hr>" . "&nbsp; " . $data2[ORG_NAME];
	}
	
	$cmd = " 	select PM_NAME from PER_MGT	where PM_CODE='$TMP_APPROVE_PM_CODE'  ";
	$db_dpis2->send_cmd($cmd);
	$data2 = $db_dpis2->get_array();
	$TMP_APPROVE_PM_NAME = trim($data2[PM_NAME]);
	if ($SESS_DEPARTMENT_NAME=="กรมการปกครอง" && !$TMP_APPROVE_PM_NAME) $TMP_APPROVE_PM_NAME = $TMP_APPROVE_PL_NAME;
	if ($TMP_APPROVE_PM_NAME=="ผู้ว่าราชการจังหวัด" || $TMP_APPROVE_PM_NAME=="รองผู้ว่าราชการจังหวัด" || $TMP_APPROVE_PM_NAME=="ปลัดจังหวัด") {
		$TMP_APPROVE_PM_NAME .= $TMP_APPROVE_ORG_NAME;
		$TMP_APPROVE_PM_NAME = str_replace("จังหวัดจังหวัด", "จังหวัด", $TMP_APPROVE_PM_NAME); 
	} elseif ($TMP_APPROVE_PM_NAME=="นายอำเภอ") {
		$TMP_APPROVE_PM_NAME .= $TMP_APPROVE_ORG_NAME_1;
		$TMP_APPROVE_PM_NAME = str_replace("อำเภอที่ทำการปกครองอำเภอ", "อำเภอ", $TMP_APPROVE_PM_NAME); 
	}
	
	if($TMP_APPROVE_PM_NAME){
		$TMP_APPROVE_POSITION_NAME = trim($TMP_APPROVE_PM_NAME);
		$TMP_MAIN_PER_NAME = trim($TMP_APPROVE_PM_NAME);
	}else{
		$TMP_APPROVE_POSITION_NAME = trim($TMP_APPROVE_PL_NAME)?($TMP_APPROVE_PL_NAME . $TMP_APPROVE_POSITION_LEVEL . (($TMP_APPROVE_PT_NAME != "ทั่วไป" && $TMP_APPROVE_LEVEL_NO >= 6)?"$TMP_APPROVE_PT_NAME":"")):"";
		$TMP_MAIN_PER_NAME = trim($TMP_APPROVE_PER_NAME);
	}
//--------------------

	if($AB_CODE=="01" || $AB_CODE=="02" || $AB_CODE=="03"){
		if(trim($DEPARTMENT_NAME)=="สำนักงาน ก.พ."){
			$ABS_NOTE = "หมายเหตุ  ในการพิจารณาเลื่อนเงินเดือนประจำปีในแต่ละครั้งจะพิจารณาจากเกณฑ์วันลาในรอบ 6 เดือน  ตั้งแต่วันที่ 1 ต.ค. ของปีหนึ่งถึงวันที่ 31 มี.ค. ของปีถัดไป  หรือวันที่ 1 เม.ย. ถึงวันที่ 30 ก.ย. ของปีเดียวกันแล้วแต่กรณี  โดยจะต้องไม่ลาป่วยและลากิจส่วนตัวรวมกันเกินกว่า 10 ครั้ง  ซึ่งในจำนวน 10 ครั้ง จะต้องไม่เกิน 23 วันและต้องไม่มาทำงานสายเกิน 18 ครั้ง (วัน) ";	
		}
		$cmd = " select 	ABS_STARTDATE, ABS_ENDDATE, ABS_DAY 
				   from		PER_ABSENT
				   where	PER_ID=$PER_ID and trim(AB_CODE)='$AB_CODE' and ABS_ID not in ($ABS_ID)
				   order by	ABS_STARTDATE desc ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();

		$LAST_ABS_STARTDATE = substr($data2[ABS_STARTDATE], 0, 10);
		if(trim($LAST_ABS_STARTDATE)){
			$LAST_ABS_STARTDATE = show_date_format($LAST_ABS_STARTDATE,$DATE_DISPLAY);
		} // end if
	
		$LAST_ABS_ENDDATE = substr($data2[ABS_ENDDATE], 0, 10);
		if(trim($LAST_ABS_ENDDATE)){
			$LAST_ABS_ENDDATE = show_date_format($LAST_ABS_ENDDATE,$DATE_DISPLAY);
		} // end if
	
		$LAST_ABS_DAY = trim($data2[ABS_DAY]);
		
		$arr_temp = explode("-", substr($data[ABS_STARTDATE], 0, 10));
		$SEARCH_PERIOD = $arr_temp[1] . $arr_temp[2];
		if($SEARCH_PERIOD >= "1001" || $SEARCH_PERIOD <= "0331"){
			if($arr_temp[1] >= 10){
				if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(ABS_STARTDATE, 10) >= '".$arr_temp[0]."-10-01') and (LEFT(ABS_STARTDATE, 10) <= '".($arr_temp[0] + 1)."-03-31')";
				elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(ABS_STARTDATE, 1, 10) >= '".$arr_temp[0]."-10-01') and (SUBSTR(ABS_STARTDATE, 1, 10) <= '".($arr_temp[0] + 1)."-03-31')";
				elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(ABS_STARTDATE, 10) >= '".$arr_temp[0]."-10-01') and (LEFT(ABS_STARTDATE, 10) <= '".($arr_temp[0] + 1)."-03-31')";
			}else{
				if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(ABS_STARTDATE, 10) >= '".($arr_temp[0] - 1)."-10-01') and (LEFT(ABS_STARTDATE, 10) <= '".$arr_temp[0]."-03-31')";
				elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(ABS_STARTDATE, 1, 10) >= '".($arr_temp[0] - 1)."-10-01') and (SUBSTR(ABS_STARTDATE, 1, 10) <= '".$arr_temp[0]."-03-31')";
				elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(ABS_STARTDATE, 10) >= '".($arr_temp[0] - 1)."-10-01') and (LEFT(ABS_STARTDATE, 10) <= '".$arr_temp[0]."-03-31')";
			} // end if 
		}elseif($SEARCH_PERIOD >= "0401" && $SEARCH_PERIOD <= "0930"){
			if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(ABS_STARTDATE, 10) >= '".$arr_temp[0]."-04-01') and (LEFT(ABS_STARTDATE, 10) <= '".$arr_temp[0]."-09-30')";
			elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(ABS_STARTDATE, 1, 10) >= '".$arr_temp[0]."-04-01') and (SUBSTR(ABS_STARTDATE, 1, 10) <= '".$arr_temp[0]."-09-30')";
			elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(ABS_STARTDATE, 10) >= '".$arr_temp[0]."-04-01') and (LEFT(ABS_STARTDATE, 10) <= '".$arr_temp[0]."-09-30')";
		} // end if

		$search_condition = "";
		if(count($arr_search_condition)) $search_condition = " and ". implode(" and ", $arr_search_condition);
		$cmd = " select 	sum(ABS_DAY) as SUM_ABS_DAY
				   from		PER_ABSENT
				   where	PER_ID=$PER_ID and trim(AB_CODE)='$AB_CODE' and ABS_ID not in ($ABS_ID)
				   			$search_condition ";
				//$db_dpis->show_error();
				//echo "<hr>$cmd<br>";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		if($AB_CODE=="01"){
			$DAY1_1 = $data2[SUM_ABS_DAY];
			$DAY1_2 = $ABS_DAY;
			$DAY1_3 = $DAY1_1 + $DAY1_2;
		}elseif($AB_CODE=="03"){
			$DAY2_1 = $data2[SUM_ABS_DAY];
			$DAY2_2 = $ABS_DAY;
			$DAY2_3 = $DAY2_1 + $DAY2_2;
		}elseif($AB_CODE=="02"){
			$DAY3_1 = $data2[SUM_ABS_DAY];
			$DAY3_2 = $ABS_DAY;
			$DAY3_3 = $DAY3_1 + $DAY3_2;
		} // end if
		
		$cmd = " select 	sum(ABS_DAY) as SUM_ABS_DAY
				   from		PER_ABSENTHIS
				   where	PER_ID=$PER_ID and trim(AB_CODE)='$AB_CODE'
				   			$search_condition ";
				//$db_dpis->show_error();
				//echo "<hr>$cmd<br>";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		if($AB_CODE=="01"){
			$DAY1_1 = $data2[SUM_ABS_DAY];
			$DAY1_2 = $ABS_DAY;
			$DAY1_3 = $DAY1_1 + $DAY1_2;
		}elseif($AB_CODE=="03"){
			$DAY2_1 = $data2[SUM_ABS_DAY];
			$DAY2_2 = $ABS_DAY;
			$DAY2_3 = $DAY2_1 + $DAY2_2;
		}elseif($AB_CODE=="02"){
			$DAY3_1 = $data2[SUM_ABS_DAY];
			$DAY3_2 = $ABS_DAY;
			$DAY3_3 = $DAY3_1 + $DAY3_2;
		} // end if
		
		$rtf_contents = replaceRTF("@DEPARTMENT_NAME@", convert2rtfascii($DEPARTMENT_NAME), $rtf_contents);
		$rtf_contents = replaceRTF("@DATE@", convert2rtfascii((date("d") + 0)), $rtf_contents);
		$rtf_contents = replaceRTF("@MONTH@", convert2rtfascii($month_full[(date("m") + 0)][TH]), $rtf_contents);
		$rtf_contents = replaceRTF("@YEAR@", convert2rtfascii((date("Y") + 543)), $rtf_contents);
		$rtf_contents = replaceRTF("@TITLE@", convert2rtfascii($TITLE), $rtf_contents);
		$rtf_contents = replaceRTF("@MAIN_PER_NAME@", convert2rtfascii($TMP_MAIN_PER_NAME), $rtf_contents);
		$rtf_contents = replaceRTF("@APPROVE_PER_NAME@", convert2rtfascii($TMP_APPROVE_PER_NAME), $rtf_contents);
		$rtf_contents = replaceRTF("@PER_NAME@", convert2rtfascii($PER_NAME), $rtf_contents);
		$rtf_contents = replaceRTF("@PL_NAME@", convert2rtfascii($PL_NAME), $rtf_contents);
		$rtf_contents = replaceRTF("@LEVEL_NAME@", convert2rtfascii($LEVEL_NAME), $rtf_contents);
		$rtf_contents = replaceRTF("@POSITION_NAME@", convert2rtfascii($POSITION_NAME), $rtf_contents);
		$rtf_contents = replaceRTF("@ORG_NAME@", convert2rtfascii($ORG_NAME), $rtf_contents);
		$rtf_contents = replaceRTF("@AB_NAME@", convert2rtfascii($AB_NAME), $rtf_contents);
		$rtf_contents = replaceRTF("@ABS_REASON@", convert2rtfascii($ABS_REASON), $rtf_contents);
		$rtf_contents = replaceRTF("@CUR_START@", convert2rtfascii($ABS_STARTDATE), $rtf_contents);
		$rtf_contents = replaceRTF("@CUR_END@", convert2rtfascii($ABS_ENDDATE), $rtf_contents);
		$rtf_contents = replaceRTF("@DAY@", convert2rtfascii($ABS_DAY), $rtf_contents);
		$rtf_contents = replaceRTF("@LAST_START@", convert2rtfascii($LAST_ABS_STARTDATE), $rtf_contents);
		$rtf_contents = replaceRTF("@LAST_END@", convert2rtfascii($LAST_ABS_ENDDATE), $rtf_contents);
		$rtf_contents = replaceRTF("@LAST_DAY@", convert2rtfascii($LAST_ABS_DAY), $rtf_contents);
		$rtf_contents = replaceRTF("@ABS_ADDRESS@", convert2rtfascii($ABS_ADDRESS), $rtf_contents);
		$rtf_contents = replaceRTF("@DAY1_1@", convert2rtfascii($DAY1_1), $rtf_contents);
		$rtf_contents = replaceRTF("@DAY1_2@", convert2rtfascii($DAY1_2), $rtf_contents);
		$rtf_contents = replaceRTF("@DAY1_3@", convert2rtfascii($DAY1_3), $rtf_contents);
		$rtf_contents = replaceRTF("@DAY2_1@", convert2rtfascii($DAY2_1), $rtf_contents);
		$rtf_contents = replaceRTF("@DAY2_2@", convert2rtfascii($DAY2_2), $rtf_contents);
		$rtf_contents = replaceRTF("@DAY2_3@", convert2rtfascii($DAY2_3), $rtf_contents);
		$rtf_contents = replaceRTF("@DAY3_1@", convert2rtfascii($DAY3_1), $rtf_contents);
		$rtf_contents = replaceRTF("@DAY3_2@", convert2rtfascii($DAY3_2), $rtf_contents);
		$rtf_contents = replaceRTF("@DAY3_3@", convert2rtfascii($DAY3_3), $rtf_contents);
		$rtf_contents = replaceRTF("@REVIEW_FLG@", convert2rtfascii($TMP_REVIEW_FLAG_RESULT), $rtf_contents);
		$rtf_contents = replaceRTF("@REVIEW_PER_NAME@", convert2rtfascii($TMP_REVIEW_PER_NAME), $rtf_contents);
		$rtf_contents = replaceRTF("@REVIEW_PL@", convert2rtfascii($TMP_REVIEW_POSITION_NAME), $rtf_contents);
		$rtf_contents = replaceRTF("@APPROVE_FLG@", convert2rtfascii($TMP_APPROVE_FLAG_RESULT), $rtf_contents);
		$rtf_contents = replaceRTF("@APPROVE_PL@", convert2rtfascii($TMP_APPROVE_POSITION_NAME), $rtf_contents);
		$rtf_contents = replaceRTF("@AUDIT_PER_NAME@", convert2rtfascii($TMP_AUDIT_PER_NAME), $rtf_contents);
		$rtf_contents = replaceRTF("@AUDIT_PL@", convert2rtfascii($TMP_AUDIT_POSITION_NAME), $rtf_contents);
		$rtf_contents = replaceRTF("@ABS_NOTE@", convert2rtfascii($ABS_NOTE), $rtf_contents);
	
	}elseif($AB_CODE=="04"){
		$arr_temp = explode("-", substr($data[ABS_STARTDATE], 0, 10));
		$SEARCH_PERIOD = $arr_temp[1] . $arr_temp[2];
		if($SEARCH_PERIOD >= "1001"){
			if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(ABS_STARTDATE, 10) >= '".$arr_temp[0]."-10-01') and (LEFT(ABS_STARTDATE, 10) <= '".($arr_temp[0] + 1)."-09-30')";
			elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(ABS_STARTDATE, 1, 10) >= '".$arr_temp[0]."-10-01') and (SUBSTR(ABS_STARTDATE, 1, 10) <= '".($arr_temp[0] + 1)."-09-30')";
			elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(ABS_STARTDATE, 10) >= '".$arr_temp[0]."-10-01') and (LEFT(ABS_STARTDATE, 10) <= '".($arr_temp[0] + 1)."-09-30')";
		}else{
			if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(ABS_STARTDATE, 10) >= '".($arr_temp[0] - 1)."-10-01') and (LEFT(ABS_STARTDATE, 10) <= '".$arr_temp[0]."-09-30')";
			elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(ABS_STARTDATE, 1, 10) >= '".($arr_temp[0] - 1)."-10-01') and (SUBSTR(ABS_STARTDATE, 1, 10) <= '".$arr_temp[0]."-09-30')";
			elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(ABS_STARTDATE, 10) >= '".($arr_temp[0] - 1)."-10-01') and (LEFT(ABS_STARTDATE, 10) <= '".$arr_temp[0]."-09-30')";
		} // end if

		$search_condition = "";
		if(count($arr_search_condition)) $search_condition = " and ". implode(" and ", $arr_search_condition);
		$cmd = " select 	sum(ABS_DAY) as SUM_ABS_DAY
				   from		PER_ABSENT
				   where	PER_ID=$PER_ID and trim(AB_CODE)='$AB_CODE' and ABS_ID not in ($ABS_ID)
				   			$search_condition ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$DAY1_1 = $data2[SUM_ABS_DAY];
		$DAY1_2 = $ABS_DAY;
		$DAY1_3 = $DAY1_1 + $DAY1_2;

		$cmd = " select 	sum(ABS_DAY) as SUM_ABS_DAY
				   from		PER_ABSENTHIS
				   where	PER_ID=$PER_ID and trim(AB_CODE)='$AB_CODE'
				   			$search_condition ";
				//$db_dpis->show_error();
				//echo "<hr>$cmd<br>";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$DAY1_1 = $data2[SUM_ABS_DAY];
		$DAY1_2 = $ABS_DAY;
		$DAY1_3 = $DAY1_1 + $DAY1_2;
		
		$rtf_contents = replaceRTF("@DEPARTMENT_NAME@", convert2rtfascii($DEPARTMENT_NAME), $rtf_contents);
		$rtf_contents = replaceRTF("@DATE@", convert2rtfascii((date("d") + 0)), $rtf_contents);
		$rtf_contents = replaceRTF("@MONTH@", convert2rtfascii($month_full[(date("m") + 0)][TH]), $rtf_contents);
		$rtf_contents = replaceRTF("@YEAR@", convert2rtfascii((date("Y") + 543)), $rtf_contents);
		$rtf_contents = replaceRTF("@MAIN_PER_NAME@", convert2rtfascii($TMP_MAIN_PER_NAME), $rtf_contents);
		$rtf_contents = replaceRTF("@APPROVE_PER_NAME@", convert2rtfascii($TMP_APPROVE_PER_NAME), $rtf_contents);
		$rtf_contents = replaceRTF("@PER_NAME@", convert2rtfascii($PER_NAME), $rtf_contents);
		$rtf_contents = replaceRTF("@POSITION_NAME@", convert2rtfascii($POSITION_NAME), $rtf_contents);
		$rtf_contents = replaceRTF("@ORG_NAME@", convert2rtfascii($ORG_NAME), $rtf_contents);
		$rtf_contents = replaceRTF("@CUR_START@", convert2rtfascii($ABS_STARTDATE), $rtf_contents);
		$rtf_contents = replaceRTF("@CUR_END@", convert2rtfascii($ABS_ENDDATE), $rtf_contents);
		$rtf_contents = replaceRTF("@DAY@", convert2rtfascii($ABS_DAY), $rtf_contents);
		$rtf_contents = replaceRTF("@ABS_ADDRESS@", convert2rtfascii($ABS_ADDRESS), $rtf_contents);
		$rtf_contents = replaceRTF("@DAY1_1@", convert2rtfascii($DAY1_1), $rtf_contents);
		$rtf_contents = replaceRTF("@DAY1_2@", convert2rtfascii($DAY1_2), $rtf_contents);
		$rtf_contents = replaceRTF("@DAY1_3@", convert2rtfascii($DAY1_3), $rtf_contents);
		$rtf_contents = replaceRTF("@REVIEW_FLG@", convert2rtfascii($TMP_REVIEW_FLAG_RESULT), $rtf_contents);
		$rtf_contents = replaceRTF("@REVIEW_PER_NAME@", convert2rtfascii($TMP_REVIEW_PER_NAME), $rtf_contents);
		$rtf_contents = replaceRTF("@REVIEW_PL@", convert2rtfascii($TMP_REVIEW_POSITION_NAME), $rtf_contents);
		$rtf_contents = replaceRTF("@APPROVE_FLG@", convert2rtfascii($TMP_APPROVE_FLAG_RESULT), $rtf_contents);
		$rtf_contents = replaceRTF("@APPROVE_PL@", convert2rtfascii($TMP_APPROVE_POSITION_NAME), $rtf_contents);
		$rtf_contents = replaceRTF("@AUDIT_PER_NAME@", convert2rtfascii($TMP_AUDIT_PER_NAME), $rtf_contents);
		$rtf_contents = replaceRTF("@AUDIT_PL@", convert2rtfascii($TMP_AUDIT_POSITION_NAME), $rtf_contents);
		$rtf_contents = replaceRTF("@VC_DAY@", convert2rtfascii($VC_DAY), $rtf_contents);
		$rtf_contents = replaceRTF("@TOT_DAY@", convert2rtfascii($TOT_DAY), $rtf_contents);
	} // end if

	// write rtf content
	$filename = "../tmp/rpt_data_absent_$SESS_USERNAME.rtf";
	$handle = fopen ($filename, "w");
	fwrite($handle, $rtf_contents);
	fclose($handle);

	ini_set("max_execution_time", 30);
?>
	<meta http-equiv='refresh' content='0;URL=<?=$filename?>'>