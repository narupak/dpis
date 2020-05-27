<?
	include("../../php_scripts/connect_database.php");
	include ("../php_scripts/session_start.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	// read template content
	$filename = "rpt_personal_salaryhis_template.rtf";
	$handle = fopen ($filename, "r");
	$rtf_contents = fread($handle, filesize($filename));
	fclose($handle);
//	echo $rtf_contents;

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if($DPISDB=="odbc"){
		$cmd = " select			b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, a.PER_TYPE,
											a.POS_ID, a.POEM_ID, a.POEMS_ID
						 from			PER_PERSONAL a, PER_PRENAME b
						 where		a.PER_ID=$PER_ID and a.PN_CODE=b.PN_CODE ";	
	}elseif($DPISDB=="oci8"){
		$cmd = " select			b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, a.PER_TYPE,
											a.POS_ID, a.POEM_ID, a.POEMS_ID
						 from			PER_PERSONAL a, PER_PRENAME b
						 where		a.PER_ID=$PER_ID and a.PN_CODE=b.PN_CODE ";	
	}elseif($DPISDB=="mysql"){
		$cmd = " select			b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, a.PER_TYPE,
											a.POS_ID, a.POEM_ID, a.POEMS_ID
						 from			PER_PERSONAL a, PER_PRENAME b
						 where		a.PER_ID=$PER_ID and a.PN_CODE=b.PN_CODE ";	
	} // end if
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data = $db_dpis->get_array();

	$PER_NAME = "$data[PN_NAME]$data[PER_NAME] $data[PER_SURNAME]";
	$PER_STARTDATE = show_date_format($data[PER_STARTDATE],$DATE_DISPLAY);

	$LEVEL_NO = trim($data[LEVEL_NO]);
	$cmd = "select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where LEVEL_NO = '$LEVEL_NO'";
	$db_dpis2->send_cmd($cmd);
	$data2  = $db_dpis2->get_array();
	$LEVEL_NAME2 = $data2[LEVEL_NAME];
	$POSITION_LEVEL = $data2[POSITION_LEVEL];
	if (!$POSITION_LEVEL) $POSITION_LEVEL = $LEVEL_NAME;
	
	$PER_TYPE = $data[PER_TYPE];
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
		$PT_CODE = trim($data2[PT_CODE]);
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
	} // end if
	
	$cmd = " select ORG_ID_REF, ORG_NAME  from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
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
	
	$cmd = " select SAH_EFFECTIVEDATE, SAH_SALARY_MIDPOINT, SAH_PERCENT_UP, SAH_SALARY_UP, SAH_SALARY, SAH_SALARY_EXTRA 
					  from PER_SALARYHIS where SAH_ID=$SAH_ID ";
	$db_dpis2->send_cmd($cmd);
	$data2 = $db_dpis2->get_array();
	$SAH_EFFECTIVEDATE = $data2[SAH_EFFECTIVEDATE];
	$SAH_SALARY_MIDPOINT = $data2[SAH_SALARY_MIDPOINT]?number_format($data2[SAH_SALARY_MIDPOINT],2):" ";
	$SAH_PERCENT_UP = $data2[SAH_PERCENT_UP]?number_format($data2[SAH_PERCENT_UP],4):" ";
	$SAH_SALARY_UP = $data2[SAH_SALARY_UP]?number_format($data2[SAH_SALARY_UP],2):" ";
	$SAH_SALARY = $data2[SAH_SALARY]?number_format($data2[SAH_SALARY],2):" ";
	$SAH_SALARY_EXTRA = $data2[SAH_SALARY_EXTRA]?number_format($data2[SAH_SALARY_EXTRA],2):" ";
	$PER_SALARY = $data2[SAH_SALARY] - $data2[SAH_SALARY_UP];
	$PER_SALARY = number_format($PER_SALARY,2);
	$arr_temp = explode("-", $SAH_EFFECTIVEDATE);
	if ($arr_temp[1]=="04") $KF_CYCLE = 1;
	elseif ($arr_temp[1]=="10") $KF_CYCLE = 2;
	if($KF_CYCLE == 1){
		$KF_START_DATE = show_date_format(($arr_temp[0] - 1)."-10-01",$DATE_DISPLAY);
		$KF_END_DATE = show_date_format($arr_temp[0]."-03-31",$DATE_DISPLAY);
	}elseif($KF_CYCLE == 2){
		$KF_START_DATE = show_date_format($arr_temp[0]."-04-01",$DATE_DISPLAY);
		$KF_END_DATE = show_date_format($arr_temp[0]."-09-30",$DATE_DISPLAY);
	} // end if
	
	$rtf_contents = replaceRTF("@DEPARTMENT_NAME@", convert2rtfascii($DEPARTMENT_NAME), $rtf_contents);
	$rtf_contents = replaceRTF("@KF_CYCLE@", convert2rtfascii($KF_CYCLE), $rtf_contents);
	$rtf_contents = replaceRTF("@KF_START_DATE@", convert2rtfascii($KF_START_DATE), $rtf_contents);
	$rtf_contents = replaceRTF("@KF_END_DATE@", convert2rtfascii($KF_END_DATE), $rtf_contents);
	$rtf_contents = replaceRTF("@PER_NAME@", convert2rtfascii($PER_NAME), $rtf_contents);
	$rtf_contents = replaceRTF("@PL_NAME@", convert2rtfascii($PL_NAME), $rtf_contents);
	$rtf_contents = replaceRTF("@POS_NO@", convert2rtfascii($POS_NO), $rtf_contents);
	$rtf_contents = replaceRTF("@ORG_NAME@", convert2rtfascii($ORG_NAME), $rtf_contents);
	$rtf_contents = replaceRTF("@MINISTRY_NAME@", convert2rtfascii($MINISTRY_NAME), $rtf_contents);
	$rtf_contents = replaceRTF("@PER_SALARY@", convert2rtfascii($PER_SALARY), $rtf_contents);
	$rtf_contents = replaceRTF("@PER_MGTSALARY@", convert2rtfascii($PER_MGTSALARY), $rtf_contents);
	$rtf_contents = replaceRTF("@SAH_SALARY_MIDPOINT@", convert2rtfascii($SAH_SALARY_MIDPOINT), $rtf_contents);
	$rtf_contents = replaceRTF("@SAH_PERCENT_UP@", convert2rtfascii($SAH_PERCENT_UP), $rtf_contents);
	$rtf_contents = replaceRTF("@SAH_SALARY_UP@", convert2rtfascii($SAH_SALARY_UP), $rtf_contents);
	$rtf_contents = replaceRTF("@SAH_SALARY@", convert2rtfascii($SAH_SALARY), $rtf_contents);
	$rtf_contents = replaceRTF("@SAH_SALARY_EXTRA@", convert2rtfascii($SAH_SALARY_EXTRA), $rtf_contents);
	$rtf_contents = replaceRTF("@SAH_SALARY_SIGN@", "........................................................", $rtf_contents);
	
	// write rtf content
	$filename = "../tmp/rpt_personal_salaryhis_$SESS_USERNAME.rtf";
	$handle = fopen ($filename, "w");
	fwrite($handle, $rtf_contents);
	fclose($handle);
?>
	<meta http-equiv='refresh' content='0;URL=<?=$filename?>'>