<?
	include("../../php_scripts/connect_database.php");
//	include ("../php_scripts/session_start.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if(trim($LET_DATE)){
		$arr_temp = explode("/", $LET_DATE);
		$LET_DATE = ($arr_temp[0] + 0) ."   ". $month_full[($arr_temp[1] + 0)][TH] ."   ..  ". $arr_temp[2];
	} // end if
	
	if($DPISDB=="odbc"){
		$cmd = " select			a.PER_ID, b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_STARTDATE, a.LEVEL_NO, a.PER_TYPE, c.OT_NAME,
											a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID, a.PER_SALARY, a.PER_MGTSALARY, a.PER_SPSALARY
						 from			PER_PERSONAL a, PER_PRENAME b, PER_OFF_TYPE c
						 where			a.PN_CODE=b.PN_CODE and a.OT_CODE=c.OT_CODE ";	
	}elseif($DPISDB=="oci8"){
		$cmd = " select			a.PER_ID, b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_STARTDATE, a.LEVEL_NO, a.PER_TYPE, c.OT_NAME,
											a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID, a.PER_SALARY, a.PER_MGTSALARY, a.PER_SPSALARY
						 from			PER_PERSONAL a, PER_PRENAME b, PER_OFF_TYPE c
						 where			a.PN_CODE=b.PN_CODE and a.OT_CODE=c.OT_CODE ";	
	}elseif($DPISDB=="mysql"){
		$cmd = " select			a.PER_ID, b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_STARTDATE, a.LEVEL_NO, a.PER_TYPE, c.OT_NAME,
											a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID, a.PER_SALARY, a.PER_MGTSALARY, a.PER_SPSALARY
						 from			PER_PERSONAL a, PER_PRENAME b, PER_OFF_TYPE c
						 where			a.PN_CODE=b.PN_CODE and a.OT_CODE=c.OT_CODE ";	
	} // end if
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	while ($data = $db_dpis->get_array()) {

	// read template content
	$filename = "rpt_data_letter_template.rtf";
	$handle = fopen ($filename, "r");
	$rtf_contents = fread($handle, filesize($filename));
	fclose($handle);
//	echo $rtf_contents;

		$PER_ID = $data[PER_ID];
		$PER_NAME = "$data[PN_NAME]$data[PER_NAME] $data[PER_SURNAME]";
		$PER_STARTDATE = show_date_format($data[PER_STARTDATE], $DATE_DISPLAY);
		$LEVEL_NO = trim($data[LEVEL_NO]);
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
			$PL_NAME = trim($PL_NAME)?($PL_NAME ." ". level_no_format($LEVEL_NO) . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"")):"";
			$LEVEL_NAME = (($PT_CODE==11)?".":$PT_NAME) . level_no_format($LEVEL_NO);
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
			$PL_NAME = trim($PL_NAME)?($PL_NAME ." ". level_no_format($LEVEL_NO)):"";
			$LEVEL_NAME = level_no_format($LEVEL_NO);
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
			$PL_NAME = trim($PL_NAME)?($PL_NAME ." ". level_no_format($LEVEL_NO)):"";
			$LEVEL_NAME = level_no_format($LEVEL_NO);
		} elseif($PER_TYPE==4){
			$POEMS_ID = $data[POEMS_ID];
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
			$PL_NAME = trim($PL_NAME)?($PL_NAME ." ". level_no_format($LEVEL_NO)):"";
			$LEVEL_NAME = level_no_format($LEVEL_NO);
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
	
		$LET_SIGN = ($LET_ASSIGN==1?"แทน":($LET_ASSIGN==2?"ักากแทน":""))."$LET_SIGN";
	
		$rtf_contents = replaceRTF("@LET_NO@", convert2rtfascii(convert2thaidigit($LET_NO)), $rtf_contents);
		$rtf_contents = replaceRTF("@DEPARTMENT_NAME@", convert2rtfascii(convert2thaidigit($DEPARTMENT_NAME)), $rtf_contents);
		$rtf_contents = replaceRTF("@PER_NAME@", convert2rtfascii(convert2thaidigit($PER_NAME)), $rtf_contents);
		$rtf_contents = replaceRTF("@OFFICER_TYPE@", convert2rtfascii(convert2thaidigit($OFFICER_TYPE)), $rtf_contents);
		$rtf_contents = replaceRTF("@PL_NAME@", convert2rtfascii(convert2thaidigit($PL_NAME)), $rtf_contents);
		$rtf_contents = replaceRTF("@ORG_NAME@", convert2rtfascii(convert2thaidigit($ORG_NAME)), $rtf_contents);
		$rtf_contents = replaceRTF("@MINISTRY_NAME@", convert2rtfascii(convert2thaidigit($MINISTRY_NAME)), $rtf_contents);
		$rtf_contents = replaceRTF("@LEVEL_NAME@", convert2rtfascii(convert2thaidigit($LEVEL_NAME)), $rtf_contents);
		$rtf_contents = replaceRTF("@PER_SALARY@", convert2rtfascii(convert2thaidigit($PER_SALARY)), $rtf_contents);
		$rtf_contents = replaceRTF("@PER_MGTSALARY@", convert2rtfascii(convert2thaidigit($PER_MGTSALARY)), $rtf_contents);
		$rtf_contents = replaceRTF("@PER_SPSALARY@", convert2rtfascii(convert2thaidigit($PER_SPSALARY)), $rtf_contents);
		$rtf_contents = replaceRTF("@LET_DATE@", convert2rtfascii(convert2thaidigit($LET_DATE)), $rtf_contents);
		$rtf_contents = replaceRTF("@PER_NAME_SIGN1@", convert2rtfascii(convert2thaidigit($PER_NAME_SIGN1)), $rtf_contents);
		$rtf_contents = replaceRTF("@LET_POSITION@", convert2rtfascii(convert2thaidigit($LET_POSITION)), $rtf_contents);
		$rtf_contents = replaceRTF("@LET_SIGN@", convert2rtfascii(convert2thaidigit($LET_SIGN)), $rtf_contents);

		// write rtf content
		$filename = "../tmp/rpt_data_letter_".$PER_ID."_".$LET_DATE.".rtf";
		echo "$filename<br>";
		$handle = fopen ($filename, "w");
		fwrite($handle, $rtf_contents);
		fclose($handle);
	} // loop while
	ini_set("max_execution_time", 30);
?>
<!-- <meta http-equiv='refresh' content='0;URL=<?//=$filename?>'>-->