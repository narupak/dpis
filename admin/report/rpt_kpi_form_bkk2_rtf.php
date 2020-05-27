<?
//	header('Content-Type: text/html; charset=windows-874');
	
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require("../../RTF/rtf_class.php");

	ini_set("max_execution_time", $max_execution_time); 
	
	$fname= "rpt_kpi_form_bkk_rtf.rtf";

	if (!$font) $font = "AngsanaUPC";

	$RTF = new RTF("a4", 500, 500, 500, 750);

//	$RTF->set_default_font($font, 14);

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	function getPIC_SIGN($PER_ID,$PER_CARDNO){
		global $db_dpis , $db_dpis2;
	
		$PIC_SIGN="";
		//หารูปที่เป็นลายเซ็น
		$cmd = "	select 		*
									  from 		PER_PERSONALPIC
									  where 		PER_ID=$PER_ID and PER_CARDNO = '$PER_CARDNO' and  PIC_SIGN=1
									  order by 	PER_PICSEQ ";
		$count_pic_sign=$db_dpis->send_cmd($cmd);
		if($count_pic_sign>0){	
		$data = $db_dpis->get_array();
		$TMP_PIC_SEQ = $data[PER_PICSEQ];
		$current_list .= ((trim($current_list))?",":"") . $TMP_PIC_SEQ;
		$T_PIC_SEQ = substr("000",0,3-strlen("$TMP_PIC_SEQ"))."$TMP_PIC_SEQ";
		$TMP_SERVER = $data[PIC_SERVER_ID];
		$TMP_PIC_SIGN= $data[PIC_SIGN];
		
		if ($TMP_SERVER) {
			$cmd1 = " SELECT * FROM OTH_SERVER WHERE SERVER_ID=$TMP_SERVER ";
			$db_dpis2->send_cmd($cmd1);
			$data2 = $db_dpis2->get_array();
			$tmp_SERVER_NAME = trim($data2[SERVER_NAME]);
			$tmp_ftp_server = trim($data2[FTP_SERVER]);
			$tmp_ftp_username = trim($data2[FTP_USERNAME]);
			$tmp_ftp_password = trim($data2[FTP_PASSWORD]);
			$tmp_main_path = trim($data2[MAIN_PATH]);
			$tmp_http_server = trim($data2[HTTP_SERVER]);
		} else {
			$TMP_SERVER = 0;
			$tmp_SERVER_NAME = "";
			$tmp_ftp_server = "";
			$tmp_ftp_username = "";
			$tmp_ftp_password = "";
			$tmp_main_path = "";
			$tmp_http_server = "";
		}
		$SIGN_NAME = "";
		if($TMP_PIC_SIGN==1){ $SIGN_NAME = "SIGN"; }
		if (trim($PER_CARDNO) && trim($PER_CARDNO) != "NULL") {
			$TMP_PIC_NAME = $data[PER_PICPATH].$PER_CARDNO."-".$SIGN_NAME.$T_PIC_SEQ.".jpg".($tmp_SERVER_NAME ? " [".$tmp_SERVER_NAME."]" : "");
			//$TMP_SHOW_PIC = ($tmp_http_server ? $tmp_http_server."/" : "").$data[PER_PICPATH].$PER_CARDNO."-".$SIGN_NAME.$T_PIC_SEQ.".jpg";
		} else {
			$TMP_PIC_NAME = $data[PER_PICPATH].$data[PER_GENNAME]."-".$SIGN_NAME.$T_PIC_SEQ.".jpg".($tmp_SERVER_NAME ? " [".$tmp_SERVER_NAME."]" : "");
			//$TMP_SHOW_PIC = ($tmp_http_server ? $tmp_http_server."/" : "").$data[PER_PICPATH].$PER_CARDNO."-".$SIGN_NAME.$T_PIC_SEQ.".jpg";
		}
		if(file_exists("../".$TMP_PIC_NAME)){
			$PIC_SIGN = "../".$TMP_PIC_NAME;		//	if($PER_CARDNO && $TMP_PIC_NAME)		$PIC_SIGN = "../../attachments/".$PER_CARDNO."/PER_SIGN/".$TMP_PIC_NAME;
		}
		} //end count	
	return $PIC_SIGN;
	}

	//หาชื่อหน่วยงาน
	if($PV_CODE){
		$select_org_name = "PV_ENG_NAME";
		$find_org_name = "PV_CODE='$PV_CODE'";
	}else{
		$select_org_name = "ORG_ENG_NAME";
		$find_org_name = "ORG_ID=$DEPARTMENT_ID";
	}
	$cmd = " select $select_org_name from PER_ORG where $find_org_name";
	if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	$db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();
	$data = $db_dpis->get_array();
	$ORG_ENG_NAME = trim($data[$select_org_name]);
	//echo $cmd;

	$cmd = " select 	KF_CYCLE, KF_START_DATE, KF_END_DATE, PER_ID, PER_CARDNO, PER_ID_REVIEW, PER_ID_REVIEW0, PER_ID_REVIEW1, PER_ID_REVIEW2,
						SCORE_KPI, SUM_KPI, SCORE_COMPETENCE, SUM_COMPETENCE, SCORE_OTHER, SUM_OTHER, RESULT_COMMENT, 
						COMPETENCE_COMMENT, SALARY_RESULT, SALARY_REMARK1, SALARY_REMARK2, AGREE_REVIEW1, DIFF_REVIEW1, 
						AGREE_REVIEW2, DIFF_REVIEW2, PERFORMANCE_WEIGHT, COMPETENCE_WEIGHT, OTHER_WEIGHT, DEPARTMENT_ID
			   from 		PER_KPI_FORM
			   where 	KF_ID=$KF_ID ";
	$db_dpis->send_cmd($cmd);
//echo $cmd;
	$data = $db_dpis->get_array();
	$KF_CYCLE = trim($data[KF_CYCLE]);
	$KF_START_DATE = show_date_format($data[KF_START_DATE], 1);
	$KF_END_DATE = show_date_format($data[KF_END_DATE], 1);
	$KF_YEAR = substr($KF_END_DATE, 6, 4);

	$PER_ID = $data[PER_ID];
	$PER_CARDNO = trim($data[PER_CARDNO]);		
	$PER_ID_REVIEW = $data[PER_ID_REVIEW];
	$PER_ID_REVIEW0 = $data[PER_ID_REVIEW0];
	$PER_ID_REVIEW1 = $data[PER_ID_REVIEW1];
	$PER_ID_REVIEW2 = $data[PER_ID_REVIEW2];
	
	$SCORE_KPI = $data[SCORE_KPI];
	$SUM_KPI = $data[SUM_KPI];
	$SCORE_COMPETENCE = $data[SCORE_COMPETENCE]; 
	$SUM_COMPETENCE = $data[SUM_COMPETENCE];
	$SCORE_OTHER = $data[SCORE_OTHER];
	$SUM_OTHER = $data[SUM_OTHER];
	$PERFORMANCE_WEIGHT = $data[PERFORMANCE_WEIGHT];
	$COMPETENCE_WEIGHT = $data[COMPETENCE_WEIGHT];
	$OTHER_WEIGHT = $data[OTHER_WEIGHT];
	
	$RESULT_COMMENT = $data[RESULT_COMMENT];
	$COMPETENCE_COMMENT = $data[COMPETENCE_COMMENT];
	$SALARY_RESULT = $data[SALARY_RESULT];
	$SALARY_REMARK1 = $data[SALARY_REMARK1];
	$SALARY_REMARK2 = $data[SALARY_REMARK2];
	$AGREE_REVIEW1 = $data[AGREE_REVIEW1];
	$DIFF_REVIEW1 = $data[DIFF_REVIEW1];
	$AGREE_REVIEW2 = $data[AGREE_REVIEW2];
	$DIFF_REVIEW2 = $data[DIFF_REVIEW2];
	$TMP_DEPARTMENT_ID = $data[DEPARTMENT_ID];

	$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$TMP_DEPARTMENT_ID ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$TMP_DEPARTMENT_NAME = trim($data[ORG_NAME]);
	
	$cmd = " select 	PN_CODE, PER_NAME, PER_SURNAME, LEVEL_NO, PER_SALARY, PER_TYPE, POS_ID
					 from		PER_PERSONAL
					 where	PER_ID=$PER_ID ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$PN_CODE = trim($data[PN_CODE]);
	$PER_NAME = trim($data[PER_NAME]);
	$PER_SURNAME = trim($data[PER_SURNAME]);
	$LEVEL_NO = trim($data[LEVEL_NO]);
	$PER_SALARY = trim($data[PER_SALARY]);
	$PER_TYPE = trim($data[PER_TYPE]);
	$POS_ID = trim($data[POS_ID]);

	$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='$PN_CODE' ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$PN_NAME = trim($data[PN_NAME]);
	
	$PER_NAME = $PN_NAME . $PER_NAME . " " . $PER_SURNAME;
	
	$cmd = " select LEVEL_NAME, POSITION_TYPE, POSITION_LEVEL from PER_LEVEL where trim(LEVEL_NO)='$LEVEL_NO' ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$LEVEL_NAME = trim($data[LEVEL_NAME]);
	$POSITION_TYPE = trim($data[POSITION_TYPE]);
	$POSITION_LEVEL = trim($data[POSITION_LEVEL]);

	$cmd = " select 	b.PL_NAME, c.ORG_NAME, a.PT_CODE, a.POS_NO_NAME,  a.POS_NO
					 from 		PER_POSITION a, PER_LINE b, PER_ORG c
					 where	a.POS_ID=$POS_ID and a.PL_CODE=b.PL_CODE and a.ORG_ID=c.ORG_ID ";
	if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$PL_NAME = trim($data[PL_NAME]);
	$ORG_NAME = trim($data[ORG_NAME]);
	$PT_CODE = trim($data[PT_CODE]);
	$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE)='$PT_CODE' ";
	$db_dpis2->send_cmd($cmd);
	$data2 = $db_dpis2->get_array();
	$PT_NAME = trim($data2[PT_NAME]);
	$POS_NO = trim($data[POS_NO_NAME]).trim($data[POS_NO]);
	if($PER_ID_REVIEW){
		$cmd = " select 	PN_CODE, PER_NAME, PER_SURNAME, PER_TYPE, POS_ID, LEVEL_NO , PER_CARDNO
						 from		PER_PERSONAL
						 where	PER_ID=$PER_ID_REVIEW ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$REVIEW_PN_CODE = trim($data[PN_CODE]);
		$REVIEW_PER_NAME = trim($data[PER_NAME]);
		$REVIEW_PER_SURNAME = trim($data[PER_SURNAME]);
		$REVIEW_PER_TYPE = trim($data[PER_TYPE]);
		$REVIEW_POS_ID = trim($data[POS_ID]);
		$REVIEW_LEVEL_NO = trim($data[LEVEL_NO]);
		$PER_CARDNO_REVIEW = trim($data[PER_CARDNO]);

		$cmd = " select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where trim(LEVEL_NO)='$REVIEW_LEVEL_NO' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$REVIEW_LEVEL_NAME = trim($data[LEVEL_NAME]);
		$REVIEW_POSITION_LEVEL = $data[POSITION_LEVEL];
		if (!$REVIEW_POSITION_LEVEL) $REVIEW_POSITION_LEVEL = $REVIEW_LEVEL_NAME;
		
		$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='$REVIEW_PN_CODE' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$REVIEW_PN_NAME = trim($data[PN_NAME]);
		
		$REVIEW_PER_NAME = $REVIEW_PN_NAME . $REVIEW_PER_NAME . " " . $REVIEW_PER_SURNAME;
		
		$cmd = " select 	b.PL_NAME, a.PT_CODE, a.PM_CODE 
						 from 		PER_POSITION a, PER_LINE b
						 where	a.POS_ID=$REVIEW_POS_ID and a.PL_CODE=b.PL_CODE ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$REVIEW_PL_NAME = trim($data[PL_NAME]);
		$REVIEW_PT_CODE = trim($data[PT_CODE]);
		$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE)='$REVIEW_PT_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$REVIEW_PT_NAME = trim($data2[PT_NAME]);
		$REVIEW_PL_NAME = trim($REVIEW_PL_NAME)?($REVIEW_PL_NAME . $REVIEW_POSITION_LEVEL . (($REVIEW_PT_NAME != "ทั่วไป" && $REVIEW_LEVEL_NO >= 6)?"$REVIEW_PT_NAME":"")):$REVIEW_LEVEL_NAME;
		$REVIEW_PM_CODE = trim($data[PM_CODE]);
		if($REVIEW_PM_CODE){
			$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$REVIEW_PM_CODE' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$REVIEW_PL_NAME = $data[PM_NAME]; 
		} // end if
	}
	if($PER_ID_REVIEW0){
		$cmd = " select 	PN_CODE, PER_NAME, PER_SURNAME, PER_TYPE, POS_ID, LEVEL_NO , PER_CARDNO
						 from		PER_PERSONAL
						 where	PER_ID=$PER_ID_REVIEW0 ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$REVIEW_PN_CODE0 = trim($data[PN_CODE]);
		$REVIEW_PER_NAME0 = trim($data[PER_NAME]);
		$REVIEW_PER_SURNAME0 = trim($data[PER_SURNAME]);
		$REVIEW_PER_TYPE0 = trim($data[PER_TYPE]);
		$REVIEW_POS_ID0 = trim($data[POS_ID]);
		$REVIEW_LEVEL_NO0 = trim($data[LEVEL_NO]);
		$PER_CARDNO_REVIEW0 = trim($data[PER_CARDNO]);

		$cmd = " select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where trim(LEVEL_NO)='$REVIEW_LEVEL_NO0' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$REVIEW_LEVEL_NAME0 = trim($data[LEVEL_NAME]);
		$REVIEW_POSITION_LEVEL0 = $data[POSITION_LEVEL];
		if (!$REVIEW_POSITION_LEVEL0) $REVIEW_POSITION_LEVEL0 = $REVIEW_LEVEL_NAME0;

		$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='$REVIEW_PN_CODE0' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$REVIEW_PN_NAME0 = trim($data[PN_NAME]);
		
		$REVIEW_PER_NAME0 = $REVIEW_PN_NAME0 . $REVIEW_PER_NAME0 . " " . $REVIEW_PER_SURNAME0;
		
		$cmd = " select 	b.PL_NAME, a.PT_CODE, a.PM_CODE 
						 from 		PER_POSITION a, PER_LINE b
						 where	a.POS_ID=$REVIEW_POS_ID0 and a.PL_CODE=b.PL_CODE ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$REVIEW_PL_NAME0 = trim($data[PL_NAME]);
		$REVIEW_PT_CODE0 = trim($data[PT_CODE]);
		$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE)='$REVIEW_PT_CODE0' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$REVIEW_PT_NAME0 = trim($data2[PT_NAME]);
		$REVIEW_PL_NAME0 = trim($REVIEW_PL_NAME0)?($REVIEW_PL_NAME0 . $REVIEW_POSITION_LEVEL0 . (($REVIEW_PT_NAME0 != "ทั่วไป" && $REVIEW_LEVEL_NO0 >= 6)?"$REVIEW_PT_NAME0":"")):$REVIEW_LEVEL_NAME0;
		$REVIEW_PM_CODE0 = trim($data[PM_CODE]);
		if($REVIEW_PM_CODE0){
			$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$REVIEW_PM_CODE0' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$REVIEW_PL_NAME0 = $data[PM_NAME];
		} // end if
    }
	if($PER_ID_REVIEW1){
		$cmd = " select 	PN_CODE, PER_NAME, PER_SURNAME, PER_TYPE, POS_ID, LEVEL_NO , PER_CARDNO
						 from		PER_PERSONAL
						 where	PER_ID=$PER_ID_REVIEW1 ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$REVIEW_PN_CODE1 = trim($data[PN_CODE]);
		$REVIEW_PER_NAME1 = trim($data[PER_NAME]);
		$REVIEW_PER_SURNAME1 = trim($data[PER_SURNAME]);
		$REVIEW_PER_TYPE1 = trim($data[PER_TYPE]);
		$REVIEW_POS_ID1 = trim($data[POS_ID]);
		$REVIEW_LEVEL_NO1 = trim($data[LEVEL_NO]);
		$PER_CARDNO_REVIEW1 = trim($data[PER_CARDNO]);

		$cmd = " select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where trim(LEVEL_NO)='$REVIEW_LEVEL_NO1' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$REVIEW_LEVEL_NAME1 = trim($data[LEVEL_NAME]);
		$REVIEW_POSITION_LEVEL1 = $data[POSITION_LEVEL];
		if (!$REVIEW_POSITION_LEVEL1) $REVIEW_POSITION_LEVEL1 = $REVIEW_LEVEL_NAME1;

		$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='$REVIEW_PN_CODE1' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$REVIEW_PN_NAME1 = trim($data[PN_NAME]);
		
		$REVIEW_PER_NAME1 = $REVIEW_PN_NAME1 . $REVIEW_PER_NAME1 . " " . $REVIEW_PER_SURNAME1;
		
		$cmd = " select 	b.PL_NAME, a.PT_CODE, a.PM_CODE 
						 from 		PER_POSITION a, PER_LINE b
						 where	a.POS_ID=$REVIEW_POS_ID1 and a.PL_CODE=b.PL_CODE ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$REVIEW_PL_NAME1 = trim($data[PL_NAME]);
		$REVIEW_PT_CODE1 = trim($data[PT_CODE]);
		$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE)='$REVIEW_PT_CODE1' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$REVIEW_PT_NAME1 = trim($data2[PT_NAME]);
		$REVIEW_PL_NAME1 = trim($REVIEW_PL_NAME1)?($REVIEW_PL_NAME1 . $REVIEW_POSITION_LEVEL1 . (($REVIEW_PT_NAME1 != "ทั่วไป" && $REVIEW_LEVEL_NO1 >= 6)?"$REVIEW_PT_NAME1":"")):$REVIEW_LEVEL_NAME1;
		$REVIEW_PM_CODE1 = trim($data[PM_CODE]);
		if($REVIEW_PM_CODE1){
			$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$REVIEW_PM_CODE1' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$REVIEW_PL_NAME1 = $data[PM_NAME];
		} // end if
	}
	if($PER_ID_REVIEW2){
		$cmd = " select 	PN_CODE, PER_NAME, PER_SURNAME, PER_TYPE, POS_ID, LEVEL_NO , PER_CARDNO
						 from		PER_PERSONAL
						 where	PER_ID=$PER_ID_REVIEW2 ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$REVIEW_PN_CODE2 = trim($data[PN_CODE]);
		$REVIEW_PER_NAME2 = trim($data[PER_NAME]);
		$REVIEW_PER_SURNAME2 = trim($data[PER_SURNAME]);
		$REVIEW_PER_TYPE2 = trim($data[PER_TYPE]);
		$REVIEW_POS_ID2 = trim($data[POS_ID]);
		$REVIEW_LEVEL_NO2 = trim($data[LEVEL_NO]);
		$PER_CARDNO_REVIEW2 = trim($data[PER_CARDNO]);

		$cmd = " select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where trim(LEVEL_NO)='$REVIEW_LEVEL_NO2' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$REVIEW_LEVEL_NAME2 = trim($data[LEVEL_NAME]);
		$REVIEW_POSITION_LEVEL2 = $data[POSITION_LEVEL];
		if (!$REVIEW_POSITION_LEVEL2) $REVIEW_POSITION_LEVEL2 = $REVIEW_LEVEL_NAME2;

		$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='$REVIEW_PN_CODE2' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$REVIEW_PN_NAME2 = trim($data[PN_NAME]);
		
		$REVIEW_PER_NAME2 = $REVIEW_PN_NAME2 . $REVIEW_PER_NAME2 . " " . $REVIEW_PER_SURNAME2;
		
		$cmd = " select 	b.PL_NAME, a.PT_CODE, a.PM_CODE 
						 from 		PER_POSITION a, PER_LINE b
						 where	a.POS_ID=$REVIEW_POS_ID2 and a.PL_CODE=b.PL_CODE ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$REVIEW_PL_NAME2 = trim($data[PL_NAME]);
		$REVIEW_PT_CODE2 = trim($data[PT_CODE]);
		$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE)='$REVIEW_PT_CODE2' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$REVIEW_PT_NAME2 = trim($data2[PT_NAME]);
		$REVIEW_PL_NAME2 = trim($REVIEW_PL_NAME2)?($REVIEW_PL_NAME2 . $REVIEW_POSITION_LEVEL2 . (($REVIEW_PT_NAME2 != "ทั่วไป" && $REVIEW_LEVEL_NO2 >= 6)?"$REVIEW_PT_NAME2":"")):$REVIEW_LEVEL_NAME2;
		$REVIEW_PM_CODE2 = trim($data[PM_CODE]);
		if($REVIEW_PM_CODE2){
			$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$REVIEW_PM_CODE2' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$REVIEW_PL_NAME2 = $data[PM_NAME];
		} // end if
	}
	$cmd = " select 	a.PG_ID, a.PG_SEQ, a.KPI_NAME, c.PFR_NAME, a.KPI_WEIGHT, a.PG_RESULT, a.PG_EVALUATE, a.KF_TYPE, a.PG_REMARK,
						a.KPI_TARGET_LEVEL1, a.KPI_TARGET_LEVEL2, a.KPI_TARGET_LEVEL3, a.KPI_TARGET_LEVEL4, a.KPI_TARGET_LEVEL5,
						a.KPI_TARGET_LEVEL1_DESC, a.KPI_TARGET_LEVEL2_DESC, a.KPI_TARGET_LEVEL3_DESC, a.KPI_TARGET_LEVEL4_DESC, a.KPI_TARGET_LEVEL5_DESC
			   from 		PER_PERFORMANCE_GOALS a, PER_KPI b, PER_PERFORMANCE_REVIEW c
			   where 	a.KF_ID=$KF_ID and a.KPI_ID=b.KPI_ID(+) and b.PFR_ID=c.PFR_ID(+)
			   order by	a.KF_TYPE, a.PG_SEQ ";
	$db_dpis->send_cmd($cmd);
	//echo "<br>$cmd<br>";
	while($data = $db_dpis->get_array()){
		$PG_ID++;
		$PG_SEQ = $data[PG_SEQ];
		$PFR_NAME = $data[PFR_NAME];
		$KPI_NAME = $data[KPI_NAME];
		$KPI_WEIGHT = $data[KPI_WEIGHT]+0;
		$KPI_TARGET_LEVEL1 = $data[KPI_TARGET_LEVEL1];
		$KPI_TARGET_LEVEL2 = $data[KPI_TARGET_LEVEL2];
		$KPI_TARGET_LEVEL3 = $data[KPI_TARGET_LEVEL3];
		$KPI_TARGET_LEVEL4 = $data[KPI_TARGET_LEVEL4];
		$KPI_TARGET_LEVEL5 = $data[KPI_TARGET_LEVEL5];
		$KPI_TARGET_LEVEL1_DESC = $data[KPI_TARGET_LEVEL1_DESC];
		$KPI_TARGET_LEVEL2_DESC = $data[KPI_TARGET_LEVEL2_DESC];
		$KPI_TARGET_LEVEL3_DESC = $data[KPI_TARGET_LEVEL3_DESC];
		$KPI_TARGET_LEVEL4_DESC = $data[KPI_TARGET_LEVEL4_DESC];
		$KPI_TARGET_LEVEL5_DESC = $data[KPI_TARGET_LEVEL5_DESC];
		$PG_RESULT = $data[PG_RESULT];
		$PG_EVALUATE = $data[PG_EVALUATE];
		$PG_REMARK = $data[PG_REMARK];
		$KF_TYPE = $data[KF_TYPE];
		
		if ($KF_TYPE==1 && !$FLAG_1) {
			$ARR_KPI[$PG_ID] = "ตัวชี้วัดตามแผนปฏิบัติราชการ";
			$ARR_KPI_WEIGHT[$PG_ID] = "";
			$FLAG_1 = 1;
			$PG_ID++;
		} elseif ($KF_TYPE==2 && !$FLAG_2) {
			$ARR_KPI[$PG_ID] = "ตัวชี้วัดตามหน้าที่ความรับผิดชอบหลัก";
			$ARR_KPI_WEIGHT[$PG_ID] = "";
			$FLAG_2 = 1;
			$PG_ID++;
		} elseif ($KF_TYPE==3 && !$FLAG_3) {
			$ARR_KPI[$PG_ID] = "ตัวชี้วัดตามงานที่ได้รับมอบหมายพิเศษ";
			$ARR_KPI_WEIGHT[$PG_ID] = "";
			$FLAG_3 = 1;
			$PG_ID++;
		}
		$ARR_KPI[$PG_ID] = $KPI_NAME;
		$ARR_KPI_SEQ[$PG_ID] = $PG_SEQ;
		$ARR_KPI_PFR[$PG_ID] = $PFR_NAME;
		$ARR_KPI_WEIGHT[$PG_ID] = $KPI_WEIGHT;
		$ARR_KPI_TARGET1[$PG_ID] = $KPI_TARGET_LEVEL1;
		$ARR_KPI_TARGET2[$PG_ID] = $KPI_TARGET_LEVEL2;
		$ARR_KPI_TARGET3[$PG_ID] = $KPI_TARGET_LEVEL3;
		$ARR_KPI_TARGET4[$PG_ID] = $KPI_TARGET_LEVEL4;
		$ARR_KPI_TARGET5[$PG_ID] = $KPI_TARGET_LEVEL5;
		$ARR_KPI_TARGET1_DESC[$PG_ID] = $KPI_TARGET_LEVEL1_DESC;
		$ARR_KPI_TARGET2_DESC[$PG_ID] = $KPI_TARGET_LEVEL2_DESC;
		$ARR_KPI_TARGET3_DESC[$PG_ID] = $KPI_TARGET_LEVEL3_DESC;
		$ARR_KPI_TARGET4_DESC[$PG_ID] = $KPI_TARGET_LEVEL4_DESC;
		$ARR_KPI_TARGET5_DESC[$PG_ID] = $KPI_TARGET_LEVEL5_DESC;
		$ARR_KPI_RESULT[$PG_ID] = $PG_RESULT;
		$ARR_KPI_EVALUATE[$PG_ID] = $PG_EVALUATE;
		$ARR_KPI_REMARK[$PG_ID] = $PG_REMARK;
		
//		$TOTAL_KPI_EVALUATE += $PG_EVALUATE;
		$TOTAL_KPI_EVALUATE += ($PG_EVALUATE * $KPI_WEIGHT);
		$TOTAL_KPI_WEIGHT += $KPI_WEIGHT;
	} // end while
	
	$COUNT_KPI = count($ARR_KPI);
	
	//สมรรถนะ
	$cmd = " select 	a.KC_ID, a.CP_CODE, b.CP_NAME, b.CP_MODEL, a.KC_EVALUATE, a.KC_WEIGHT, a.PC_TARGET_LEVEL, a.KC_REMARK
			   from 		PER_KPI_COMPETENCE a, PER_COMPETENCE b
			   where 	a.KF_ID=$KF_ID and a.CP_CODE=b.CP_CODE(+) and b.DEPARTMENT_ID=$TMP_DEPARTMENT_ID
			   order by 	a.CP_CODE ";
	$db_dpis->send_cmd($cmd);
	while($data = $db_dpis->get_array()){
		$KC_ID = $data[KC_ID];
		$CP_CODE = $data[CP_CODE];
		$CP_NAME = $data[CP_NAME];
		$CP_MODEL = $data[CP_MODEL];
		$KC_EVALUATE = $data[KC_EVALUATE];
		$KC_WEIGHT = $data[KC_WEIGHT];
		$PC_TARGET_LEVEL = $data[PC_TARGET_LEVEL];
		$KC_REMARK = $data[KC_REMARK];
		
		$ARR_COMPETENCE[$KC_ID] = $CP_NAME.(($CP_MODEL==2)?" *":"");
		$ARR_COMPETENCE_TARGET[$KC_ID] = $PC_TARGET_LEVEL;
		$ARR_COMPETENCE_EVALUATE[$KC_ID] = $KC_EVALUATE;
		$ARR_COMPETENCE_WEIGHT[$KC_ID]=$KC_WEIGHT;
		$ARR_COMPETENCE_REMARK[$KC_ID]=$KC_REMARK;
		//เพื่อแสดงคะแนนในส่วน แบบประเมินการสรุปสมรรถนะ
		$PC_SCORE[$KC_ID] = "";
 		if ($COMPETENCY_SCALE==1) {			//COMPETENCY_SCALE ??????????
			if($KC_EVALUATE > 0){	
				if($KC_EVALUATE >= $PC_TARGET_LEVEL) $PC_SCORE[$KC_ID] = 3;
				elseif(($PC_TARGET_LEVEL - $KC_EVALUATE) <= 1) $PC_SCORE[$KC_ID] = 2;
				elseif(($PC_TARGET_LEVEL - $KC_EVALUATE) <= 2) $PC_SCORE[$KC_ID] = 1;
				elseif(($PC_TARGET_LEVEL - $KC_EVALUATE) <= 3) $PC_SCORE[$KC_ID] = 0;
				else $PC_SCORE[$KC_ID] = 0;
			} else $KC_EVALUATE = "";
 		} elseif ($COMPETENCY_SCALE==2) {		
			$PC_SCORE[$KC_ID] = $KC_EVALUATE * $KC_WEIGHT / 100;
 		} elseif ($COMPETENCY_SCALE==3) {		
			$PC_SCORE[$KC_ID] = $KC_EVALUATE;
		}
		$TOTAL_PC_SCORE += $PC_SCORE[$KC_ID];
		//----------------------------------------------------
		
		if($KC_EVALUATE != ""){
			if($KC_EVALUATE >= $PC_TARGET_LEVEL) $ARR_COMPETENCE_COUNT[GE] += 1;
			elseif(($PC_TARGET_LEVEL - $KC_EVALUATE) == 1) $ARR_COMPETENCE_COUNT[L1] += 1;
			elseif(($PC_TARGET_LEVEL - $KC_EVALUATE) == 2) $ARR_COMPETENCE_COUNT[L2] += 1;
			elseif(($PC_TARGET_LEVEL - $KC_EVALUATE) == 3) $ARR_COMPETENCE_COUNT[L3] += 1;
			else $ARR_COMPETENCE_COUNT[L3] += 1;
		} // end if
	} // end while
	
	$COUNT_COMPETENCE = count($ARR_COMPETENCE);

	$ARR_COMPETENCE_SCORE[GE] = $ARR_COMPETENCE_COUNT[GE] * 3;
	$ARR_COMPETENCE_SCORE[L1] = $ARR_COMPETENCE_COUNT[L1] * 2;
	$ARR_COMPETENCE_SCORE[L2] = $ARR_COMPETENCE_COUNT[L2] * 1;
	$ARR_COMPETENCE_SCORE[L3] = $ARR_COMPETENCE_COUNT[L3] * 0;
	
	$TOTAL_COMPETENCE_SCORE = "";
	if($ARR_COMPETENCE_COUNT[GE] || $ARR_COMPETENCE_COUNT[L1] || $ARR_COMPETENCE_COUNT[L2] || $ARR_COMPETENCE_COUNT[L3]) 
		$TOTAL_COMPETENCE_SCORE = array_sum($ARR_COMPETENCE_SCORE);
		
//	$SHOW_SCORE_KPI = round(round(($SCORE_KPI / ($COUNT_KPI * 5)), 3), 2);
	$SHOW_SCORE_KPI = round(round(($SCORE_KPI / ($TOTAL_KPI_WEIGHT * 5)), 3), 2);
//	$SHOW_SCORE_COMPETENCE = round(round(($SCORE_COMPETENCE / ($COUNT_COMPETENCE * 3)), 3), 2);
	$SHOW_SCORE_COMPETENCE = round(round(($SCORE_COMPETENCE / 5), 3), 2);
	$SHOW_SCORE_OTHER = $SCORE_OTHER;

	$SUM_TOTAL = $SUM_KPI + $SUM_COMPETENCE + $SUM_OTHER;
	
	$cmd = " select 	IPIP_ID, DEVELOP_SEQ, DEVELOP_COMPETENCE, DEVELOP_METHOD, DEVELOP_INTERVAL, DEVELOP_EVALUATE
			   from 		PER_IPIP
			   where 	KF_ID=$KF_ID
			   order by	DEVELOP_SEQ ";
	$db_dpis->send_cmd($cmd);
	while($data = $db_dpis->get_array()){
		$IPIP_ID = $data[IPIP_ID];
		$DEVELOP_SEQ = $data[DEVELOP_SEQ];
		$DEVELOP_COMPETENCE = $data[DEVELOP_COMPETENCE];
		$DEVELOP_METHOD = $data[DEVELOP_METHOD];
		$DEVELOP_INTERVAL = $data[DEVELOP_INTERVAL];
		
		$ARR_IPIP[$IPIP_ID] = $DEVELOP_COMPETENCE;
		$ARR_IPIP_METHOD[$IPIP_ID] = $DEVELOP_METHOD;
		$ARR_IPIP_INTERVAL[$IPIP_ID] = $DEVELOP_INTERVAL;
		$ARR_IPIP_EVALUATE[$IPIP_ID] = $DEVELOP_EVALUATE;
	} // end while

	$COUNT_IPIP = count($ARR_IPIP);

	// =============================== START GEN PDF ========================//

	$heading_width1[0] = "28";
	$heading_width1[1] = "7";
	$heading_width1[2] = "8";
	$heading_width1[3] = "7";
	$heading_width1[4] = "3";
	$heading_width1[5] = "20";
	$heading_width1[6] = "15";
	
	$heading_width2[0] = "45";
	$heading_width2[1] = "15";
	$heading_width2[2] = "15";
	$heading_width2[3] = "25";
	
	$heading_width3[0] = "25";
	$heading_width3[1] = "25";
	$heading_width3[2] = "25";
	$heading_width3[3] = "25";

	$heading_width4[0] = "30";
	$heading_width4[1] = "7";
	$heading_width4[2] = "7";
	$heading_width4[3] = "9";
	$heading_width4[4] = "7";
	$heading_width4[5] = "20";
	$heading_width4[6] = "20";

	function print_header($header_select){
		global $RTF, $heading_width2, $heading_width3, $heading_width4, $heading_width1, $heading_width6;
		global $font;
		
		if($header_select == 1){	
			$RTF->open_line();			
			$RTF->set_font($font, 14);
			$RTF->cell("ตัวชี้วัด", $heading_width1[0], "center", "16", "TRL");
			$RTF->cell("น้ำหนัก", $heading_width1[1], "center", "16", "TRL");
			$RTF->cell("เป้าหมาย", $heading_width1[2], "center", "16", "TRL");
			$RTF->cell("ผลงาน", $heading_width1[3], "center", "16", "TRL");
			$RTF->cell("คะแนนที่ได้", ($heading_width1[4]*5), "center", "16", "TRL");
			$RTF->cell("ผลการประเมิน", $heading_width1[5], "center", "16", "TRL");
			$RTF->cell("เหตุผลที่ทำให้งาน", $heading_width1[6], "center", "16", "TRL");
			$RTF->close_line();
			$RTF->open_line();
			$RTF->cell("ผลสัมฤทธิ์ของงาน", $heading_width1[0], "center", "16", "RL");
			$RTF->cell("", $heading_width1[1], "center", "16", "RL");
			$RTF->cell("", $heading_width1[2], "center", "16", "RL");
			$RTF->cell("ที่ทำได้", $heading_width1[3], "center", "16", "RL");
			for($i=1; $i<=5; $i++){ 
				$RTF->cell($RTF->bold(1).convert2thaidigit($i).$RTF->bold(0), $heading_width1[4], "center", "16", "TRL");
			} // end for
			$RTF->cell(convert2thaidigit("[(น้ำหนักxคะแนนที่ได้) / 5]"), $heading_width1[5], "center", "16", "RL");
//			$RTF->cell(convert2thaidigit("[(น้ำหนักxคะแนนที่ได้) #symbol_f7# 5]"), $heading_width1[5], "center", "16", "RBL");	// #symbol_f7# คือให้พิมพ์ ascii F7=เครื่องหมาย "หาร"
			$RTF->cell("บรรลุ/", $heading_width1[6], "center", "16", "RL");
			$RTF->close_line();
			$RTF->open_line();
			$RTF->cell("", $heading_width1[0], "center", "16", "RBL");
			$RTF->cell("", $heading_width1[1], "center", "16", "RBL");
			$RTF->cell("", $heading_width1[2], "center", "16", "RBL");
			$RTF->cell("", $heading_width1[3], "center", "16", "RBL");
			for($i=1; $i<=5; $i++){ 
				$RTF->cell("", $heading_width1[4], "center", "16", "RBL");
			} // end for
			$RTF->cell("", $heading_width1[5], "center", "16", "RBL");
			$RTF->cell("ไม่บรรลุเป้าหมาย", $heading_width1[6], "center", "16", "RBL");
			$RTF->close_line();
		}elseif($header_select == 2){
			$RTF->open_line();
			$RTF->set_font($font, 14);
			$RTF->cell("องค์ประกอบการประเมิน", $heading_width2[0], "center", "16", "TRL");	
			$RTF->cell("คะแนนเต็ม", $heading_width2[1], "center", "16", "TRL");
			$RTF->cell("ผลการประเมิน", $heading_width2[2], "center", "16", "TRL");
			$RTF->cell("หมายเหตุ", $heading_width2[3], "center", "16", "TRL");
			$RTF->close_line();
			$RTF->open_line();
			$RTF->cell("", $heading_width2[0], "center", "16", "RBL");	
			$RTF->cell("", $heading_width2[1], "center", "16", "RBL");
			$RTF->cell("(คะแนนที่ได้)", $heading_width2[2], "center", "16", "RBL");
			$RTF->cell("", $heading_width2[3], "center", "16", "RBL");
			$RTF->close_line();
		}elseif($header_select == 3){
			$RTF->open_line();			
			$RTF->set_font($font, 14);
			$RTF->cell("สมรรถนะที่เลือกพัฒนา", $heading_width3[0], "center", "16", "TRL");	// 9='BLUE'
			$RTF->cell("วิธีการพัฒนา", $heading_width3[1], "center", "16", "TRL");
			$RTF->cell("ช่วงเวลาที่ต้องการพัฒนา", $heading_width3[2], "center", "16", "TRL");
			$RTF->cell("วิธีการวัดผลในการพัฒนา", $heading_width3[3], "center", "16", "TRL");	// 9='BLUE'
			$RTF->close_line();
		}elseif($header_select == 4){
			$RTF->open_line();			
			$RTF->set_font($font, 14);
			$RTF->cell("สมรรถนะหลัก", $heading_width4[0], "center", "16", "TRL");
			$RTF->cell("น้ำหนัก", $heading_width4[1], "center", "16", "TRL");
			$RTF->cell("ระดับที่", $heading_width4[2], "center", "16", "TRL");
			$RTF->cell("ระดับที่", $heading_width4[3], "center", "16", "TRL");
			$RTF->cell("คะแนน", $heading_width4[4], "center", "16", "TRL");
			$RTF->cell("ผลการประเมิน", $heading_width4[5], "center", "16", "TRL");
			$RTF->cell("ระบุเหตุการณ์/พฤติกรรมที่", $heading_width4[6], "center", "16", "TRL");
			$RTF->close_line();
			$RTF->open_line();			
			$RTF->set_font($font, 14);
			$RTF->cell("", $heading_width4[0], "center", "16", "RBL");
			$RTF->cell("", $heading_width4[1], "center", "16", "RBL");
			$RTF->cell("ต้องการ", $heading_width4[2], "center", "16", "RBL");
			$RTF->cell("ประเมินได้", $heading_width4[3], "center", "16", "RBL");
			$RTF->cell("ที่ได้", $heading_width4[4], "center", "16", "RBL");
			$RTF->cell(convert2thaidigit("[(น้ำหนักxคะแนนที่ได้) / 5]"), $heading_width4[5], "center", "16", "RBL");
//			$RTF->cell(convert2thaidigit("[(น้ำหนักxคะแนนที่ได้) #symbol_f7# 5]"), $heading_width4[5], "center", "16", "RBL");	// #symbol_f7# คือให้พิมพ์ ascii F7=เครื่องหมาย "หาร"
			$RTF->cell("ผู้รับการประเมินแสดงออก", $heading_width4[6], "center", "16", "RBL");
			$RTF->close_line();
		}	// end if
	} // function		

	$RTF->set_table_font($font, 14);
	$RTF->color("0");	// 0=BLACK
	
	$RTF->open_line();
	$RTF->set_font($font,16);
	$RTF->cell($RTF->color("0").$RTF->bold(1)."แบบประเมินผลการปฏิบัติราชการของข้าราชการกรุงเทพมหานครสามัญ".$RTF->bold(0), "100", "center", "0");
	$RTF->close_line();

	$RTF->open_line();			
	$RTF->set_font($font,16);
	$RTF->cell($RTF->color("0")."ระยะการประเมิน", "20", "left", "0", "TL");
	if($KF_CYCLE==1) $RTF->cellImage("../images/checkbox_check.jpg", 100, 3, "left", 0, "T");
	else $RTF->cellImage("../images/checkbox_blank.jpg", 100, 3, "left", 0, "T");
	$RTF->cell(convert2thaidigit("ระยะที่ ๑ ตั้งแต่ ๑ ตุลาคม ".($KF_YEAR - 1)." ถึง ๓๑ มีนาคม ".$KF_YEAR), "77", "left", "0", "TR");
	$RTF->close_line();

	$RTF->open_line();			
	$RTF->set_font($font,16);
	$RTF->cell("", "20", "left", "0", "BL");
	if($KF_CYCLE==2) $RTF->cellImage("../images/checkbox_check.jpg", 100, 3, "left", 0, "B");
	else $RTF->cellImage("../images/checkbox_blank.jpg", 100, 3, "left", 0, "B");
	$RTF->cell($RTF->color("0").convert2thaidigit("ระยะที่ ๒ ตั้งแต่ ๑ เมษายน ".$KF_YEAR." ถึง ๓๐ กันยายน ".$KF_YEAR), "77", "left", "0", "RB");
	$RTF->close_line();

	$RTF->open_line();			
	$RTF->set_font($font,16);
	$RTF->cell($RTF->color("0").$RTF->bold(1)." ผู้รับการประเมิน".$RTF->bold(0), "100", "left", "16", "TRBL");
	$RTF->close_line();

	$RTF->open_line();			
	$RTF->set_font($font,16);
	$RTF->cell($RTF->color("0")."ชื่อ - นามสกุล  $PER_NAME", "50", "left", "0", "TRBL");
	$RTF->cell($RTF->color("0")."ตำแหน่ง  ".convert2thaidigit("$PL_NAME"), "50", "left", "0", "TRBL");
	$RTF->close_line();

	$RTF->open_line();			
	$RTF->set_font($font,16);
	$RTF->cell($RTF->color("0")."ประเภทตำแหน่ง  $POSITION_TYPE  ", "50", "left", "0", "TRBL");
	$RTF->cell($RTF->color("0").convert2thaidigit("ตำแหน่งเลขที่  $POS_NO"), "50", "left", "0", "TRBL");
	$RTF->close_line();

	$RTF->open_line();			
	$RTF->set_font($font,16);
	$RTF->cell($RTF->color("0")."ระดับตำแหน่ง  $POSITION_LEVEL  ", "50", "left", "0", "TRBL");
	if ($ORG_NAME=="-") {
		$RTF->cell($RTF->color("0")."สังกัด  ".convert2thaidigit("$TMP_DEPARTMENT_NAME  "), "50", "left", "0", "TRBL");
		$RTF->close_line();
	} else {
		$RTF->cell($RTF->color("0")."สังกัด  ".convert2thaidigit("$ORG_NAME  "), "50", "left", "0", "TRBL");
		$RTF->close_line();
		$RTF->open_line();			
		$RTF->set_font($font,16);
		$RTF->cell($RTF->color("0")."", "50", "left", "0", "TRBL");
		$RTF->cell($RTF->color("0")."      ".convert2thaidigit("$TMP_DEPARTMENT_NAME  "), "50", "left", "0", "TRBL");
		$RTF->close_line();
	}

	$RTF->open_line();
	$RTF->set_font($font,16);
	$RTF->cell($RTF->color("0").$RTF->bold(1)." ผู้ประเมิน (ผู้บังคับบัญชาชั้นต้น)".$RTF->bold(0), "100", "left", "16", "TRBL");
	$RTF->close_line();

	$RTF->open_line();			
	$RTF->set_font($font,16);
	if ($REVIEW_PER_NAME0 > " ") {
		$RTF->cell($RTF->color("0")."ชื่อ - นามสกุล  $REVIEW_PER_NAME0", "50", "left", "0", "TRBL");
		$RTF->cell($RTF->color("0")."ตำแหน่ง  $REVIEW_PL_NAME0", "50", "left", "0", "TRBL");
	} else {
		$RTF->cell($RTF->color("0")."ชื่อ - นามสกุล  $REVIEW_PER_NAME", "50", "left", "0", "TRBL");
		$RTF->cell($RTF->color("0")."ตำแหน่ง  $REVIEW_PL_NAME", "50", "left", "0", "TRBL");
	}
	$RTF->close_line();

	$RTF->open_line();
	$RTF->set_font($font,16);
	$RTF->cell($RTF->color("0").$RTF->bold(1)." ผู้ประเมิน (ผู้บังคับบัญชาเหนือขึ้นไป)".$RTF->bold(0), "100", "left", "16", "TRBL");
	$RTF->close_line();

	$RTF->open_line();			
	$RTF->set_font($font,16);
	if ($REVIEW_PER_NAME0 > " ") {
		$RTF->cell($RTF->color("0")."ชื่อ - นามสกุล  $REVIEW_PER_NAME", "50", "left", "0", "TRBL");
		$RTF->cell($RTF->color("0")."ตำแหน่ง  $REVIEW_PL_NAME", "50", "left", "0", "TRBL");
	} else {
		$RTF->cell($RTF->color("0")."ชื่อ - นามสกุล  $REVIEW_PER_NAME1", "50", "left", "0", "TRBL");
		$RTF->cell($RTF->color("0")."ตำแหน่ง  $REVIEW_PL_NAME1", "50", "left", "0", "TRBL");
	}
	$RTF->close_line();

	$RTF->ln();

	$RTF->open_line();
	$RTF->set_font($font,16);
	$RTF->cell($RTF->color("0").$RTF->bold(1)."ส่วนที่ ๑ ผลสัมฤทธิ์ของงาน (ร้อยละ ๗๐)".$RTF->bold(0), "100", "left", 0);
	$RTF->close_line();

	print_header(1);
	
	foreach($ARR_KPI as $PG_ID => $KPI_NAME){

		$RTF->open_line();
		$RTF->set_font($font,14);
		if (($KPI_NAME=="ตัวชี้วัดตามแผนปฏิบัติราชการ" || $KPI_NAME=="ตัวชี้วัดตามหน้าที่ความรับผิดชอบหลัก" || $KPI_NAME=="ตัวชี้วัดตามงานที่ได้รับมอบหมายพิเศษ") && !trim($ARR_KPI_WEIGHT[$PG_ID])) {
			$data_count = 0;
			$RTF->cell($RTF->bold(1).$KPI_NAME.$RTF->bold(0), ($heading_width1[0]+$heading_width1[1]+$heading_width1[2]+$heading_width1[3]+($heading_width1[4]*5)+$heading_width1[5]+$heading_width1[6]), "left", "0", "TRBL");
		} else {
			$data_count++;
			$RTF->cell(convert2thaidigit($data_count.". ".$KPI_NAME), $heading_width1[0], "left", "0", "TRBL");
			$RTF->cell(convert2thaidigit($ARR_KPI_WEIGHT[$PG_ID]), $heading_width1[1], "center", "0", "TRBL");
			$RTF->cell(convert2thaidigit($ARR_KPI_TARGET5_DESC[$PG_ID]), $heading_width1[2], "center", "0", "TRBL");
			$RTF->cell(convert2thaidigit($ARR_KPI_RESULT[$PG_ID]), $heading_width1[3], "center", "0", "TRBL");
			for($i=1; $i<=5; $i++){ 
				if($i == $ARR_KPI_EVALUATE[$PG_ID]){
					$RTF->cell(convert2thaidigit($ARR_KPI_EVALUATE[$PG_ID]), $heading_width1[4], "center", "0", "TRBL");
				} else {
					$RTF->cell("", $heading_width1[4], "center", "0", "TRBL");
				}	
			} // end for
			$RTF->cell($RTF->bold(1).convert2thaidigit(($ARR_KPI_WEIGHT[$PG_ID]*$ARR_KPI_EVALUATE[$PG_ID])/5).$RTF->bold(0), $heading_width1[5], "center", "0", "TRBL");
			$RTF->cell(convert2thaidigit($ARR_KPI_REMARK[$PG_ID]), $heading_width1[6], "center", "0", "TRBL");
			$TOTAL_KPI_RESULT+= ($ARR_KPI_WEIGHT[$PG_ID]*$ARR_KPI_EVALUATE[$PG_ID])/5;
		}
		$RTF->close_line();
	} // end foreach
//	echo "$data_count - ".count($ARR_KPI);

	$RTF->open_line();
	$RTF->set_font($font,14);
	$RTF->cell("น้ำหนักรวม ", $heading_width1[0], "center", "0", "TRBL");
	$RTF->cell(convert2thaidigit($TOTAL_KPI_WEIGHT), $heading_width1[1], "center", "0", "TRBL");
	$RTF->cell("รวม ", ($heading_width1[2]+$heading_width1[3]+($heading_width1[4]*5)), "right", "0", "TRBL");
	$RTF->cell($RTF->bold(1).convert2thaidigit($TOTAL_KPI_RESULT).$RTF->bold(0), $heading_width1[5], "center", "0", "TRBL");
	$RTF->cell("", $heading_width1[6], "center", "0", "TRBL");
	$RTF->close_line();
	$RTF->ln();

	$RTF->open_line();
	$RTF->set_font($font,16);
	$RTF->cell($RTF->color("0").$RTF->bold(1)."ส่วนที่ ๒ พฤติกรรมการปฏิบัติราชการ (สมรรถนะหลัก) (ร้อยละ ๓๐)".$RTF->bold(0), "100", "left", 0);
	$RTF->close_line();

	print_header(4);
	
	$data_count = 0;
	foreach($ARR_COMPETENCE as $KC_ID => $CP_NAME){
		$data_count++;
		$KC_EVALUATE = $ARR_COMPETENCE_EVALUATE[$KC_ID];
		if ($LEVEL_NO=='O1' || $LEVEL_NO=='O2' || $LEVEL_NO=='K1') {
			if ($KC_EVALUATE==0) $PC_SCORE = 0;
			elseif ($KC_EVALUATE==1) $PC_SCORE = 4;
			elseif ($KC_EVALUATE==2) $PC_SCORE = 5;
			elseif ($KC_EVALUATE==3) $PC_SCORE = 5;
			elseif ($KC_EVALUATE==4) $PC_SCORE = 5;
			elseif ($KC_EVALUATE==5) $PC_SCORE = 5;
		} elseif ($LEVEL_NO=='O3' || $LEVEL_NO=='K2') {
			if ($KC_EVALUATE==0) $PC_SCORE = 0;
			elseif ($KC_EVALUATE==1) $PC_SCORE = 3;
			elseif ($KC_EVALUATE==2) $PC_SCORE = 4;
			elseif ($KC_EVALUATE==3) $PC_SCORE = 5;
			elseif ($KC_EVALUATE==4) $PC_SCORE = 5;
			elseif ($KC_EVALUATE==5) $PC_SCORE = 5;
		} elseif ($LEVEL_NO=='O4' || $LEVEL_NO=='K3' || $LEVEL_NO=='K4' || $LEVEL_NO=='K5' || 
			$LEVEL_NO=='D1' || $LEVEL_NO=='D2' || $LEVEL_NO=='M1' || $LEVEL_NO=='M2') {
			if ($KC_EVALUATE==0) $PC_SCORE = 0;
			elseif ($KC_EVALUATE==1) $PC_SCORE = 2;
			elseif ($KC_EVALUATE==2) $PC_SCORE = 3;
			elseif ($KC_EVALUATE==3) $PC_SCORE = 4;
			elseif ($KC_EVALUATE==4) $PC_SCORE = 5;
			elseif ($KC_EVALUATE==5) $PC_SCORE = 5;
		} 
		$KC_RESULT = ($ARR_COMPETENCE_WEIGHT[$KC_ID] * $PC_SCORE) / 5;
		$KC_SCORE = $PC_SCORE /  5;
		$TOTAL_KC_RESULT += $KC_RESULT;
		
		$RTF->open_line();
		$RTF->set_font($font,14);
		$RTF->cell(convert2thaidigit("$data_count. $CP_NAME"), $heading_width4[0], "left", "0", "TRBL");
		if ($ARR_COMPETENCE_WEIGHT[$KC_ID])
			$RTF->cell(convert2thaidigit($ARR_COMPETENCE_WEIGHT[$KC_ID]), $heading_width4[1], "center", "0", "TRBL");
		else
			$RTF->cell("", $heading_width4[1], "center", "0", "TRBL");
		$RTF->cell(convert2thaidigit($ARR_COMPETENCE_TARGET[$KC_ID]), $heading_width4[2], "center", "0", "TRBL");
		$RTF->cell(convert2thaidigit($KC_EVALUATE), $heading_width4[3], "center", "0", "TRBL");
		$RTF->cell(convert2thaidigit($KC_SCORE), $heading_width4[4], "center", "0", "TRBL");
		$RTF->cell(convert2thaidigit($KC_RESULT), $heading_width4[5], "center", "0", "TRBL");
		$RTF->cell(convert2thaidigit($ARR_COMPETENCE_REMARK[$KC_ID]), $heading_width4[6], "left", "0", "TRBL");
		$RTF->close_line();
	} // end foreach
	
	$RTF->open_line();
	$RTF->set_font($font,14);
	$RTF->cell("น้ำหนักรวม", $heading_width4[0], "right", "0", "TRBL");
	if (array_sum($ARR_COMPETENCE_WEIGHT))
		$RTF->cell(convert2thaidigit(array_sum($ARR_COMPETENCE_WEIGHT)), $heading_width4[1], "center", "0", "TRBL");
	else
		$RTF->cell("", $heading_width4[1], "center", "0", "TRBL");
	$RTF->cell("", $heading_width4[2]+$heading_width4[3]+$heading_width4[4], "right", "0", "TRBL");
	$RTF->cell("รวม", 5, "right", "0", "TRBL");
	$RTF->cell(convert2thaidigit($TOTAL_KC_RESULT), $heading_width4[5]-5, "center", "0", "TRBL");
	$RTF->cell("", $heading_width4[6], "center", "0", "TRBL");
	$RTF->close_line();
	$RTF->ln();

	// ======================= PAGE 2 =====================//	
	$RTF->new_page();

	$RTF->open_line();
	$RTF->set_font($font,16);
	$RTF->cell($RTF->color("0").$RTF->bold(1)."ส่วนที่ ๓ แผนพัฒนาการปฏิบัติราชการรายบุคคล".$RTF->bold(0), "100", "left", "0");
	$RTF->close_line();

	print_header(3);

	$data_count = 0;
	foreach($ARR_IPIP as $IPIP_ID => $DEVELOP_COMPETENCE){
		$data_count++;

		$RTF->open_line();
		$RTF->cell(convert2thaidigit($DEVELOP_COMPETENCE), $heading_width3[0], "center", "0", "TRBL");
		$RTF->cell(convert2thaidigit($ARR_IPIP_METHOD[$IPIP_ID]), $heading_width3[1], "center", "0", "TRBL");
		$RTF->cell(convert2thaidigit($ARR_IPIP_INTERVAL[$IPIP_ID]), $heading_width3[2], "center", "0", "TRBL");
		$RTF->cell(convert2thaidigit($ARR_IPIP_EVALUATE[$IPIP_ID]), $heading_width3[3], "center", "0", "TRBL");
		$RTF->close_line();
	} // end foreach
	if ($data_count == 0) {
		$RTF->open_line();
		$RTF->cell("", $heading_width3[0], "center", "0", "TRBL");
		$RTF->cell("", $heading_width3[1], "center", "0", "TRBL");
		$RTF->cell("", $heading_width3[2], "center", "0", "TRBL");
		$RTF->cell("", $heading_width3[3], "center", "0", "TRBL");
		$RTF->close_line();
		$RTF->open_line();
		$RTF->cell("", $heading_width3[0], "center", "0", "TRBL");
		$RTF->cell("", $heading_width3[1], "center", "0", "TRBL");
		$RTF->cell("", $heading_width3[2], "center", "0", "TRBL");
		$RTF->cell("", $heading_width3[3], "center", "0", "TRBL");
		$RTF->close_line();
	};
	$RTF->ln();

	$RTF->open_line();
	$RTF->set_font($font,16);
	$RTF->cell($RTF->color("0").$RTF->bold(1)."สรุปผลการประเมิน".$RTF->bold(0), "100", "left", "0");
	$RTF->close_line();

	print_header(2);

	$RTF->open_line();			
	$RTF->set_font($font,16);
	$RTF->cell(convert2thaidigit("๑. ผลสัมฤทธิ์ของงาน"), $heading_width2[0], "left", "0", "TRBL");
	if ($PERFORMANCE_WEIGHT)
		$RTF->cell(convert2thaidigit($PERFORMANCE_WEIGHT), $heading_width2[1], "center", "0", "TRBL");
	else
		$RTF->cell("", $heading_width2[1], "center", "0", "TRBL");
	$RTF->cell(convert2thaidigit(number_format($SUM_KPI, 2)), $heading_width2[2], "center", "0", "TRBL");
	$RTF->cell("", $heading_width2[3], "center", "0", "TRBL");
	$RTF->close_line();

	//แถวที่ 1
	$RTF->open_line();			
	$RTF->set_font($font,16);
	$RTF->cell(convert2thaidigit("๒. พฤติกรรมการปฏิบัติราชการ (สมรรถนะหลัก)"), $heading_width2[0], "left", "0", "TRBL");
	if ($COMPETENCE_WEIGHT)
		$RTF->cell(convert2thaidigit($COMPETENCE_WEIGHT), $heading_width2[1], "center", "0", "TRBL");
	else
		$RTF->cell("", $heading_width2[1], "center", "0", "TRBL");
	$RTF->cell(convert2thaidigit(number_format($SUM_COMPETENCE, 2)), $heading_width2[2], "center", "0", "TRBL");
	$RTF->cell("", $heading_width2[3], "center", "0", "TRBL");
	$RTF->close_line();
	
	//แถวที่ 2
	$RTF->open_line();			
	$RTF->set_font($font,16);
	$RTF->cell("รวม", $heading_width2[0], "center", "0", "TRBL");
	$RTF->cell(convert2thaidigit("๑๐๐"), $heading_width2[1], "center", "0", "TRBL");
	$RTF->cell(convert2thaidigit(number_format($SUM_TOTAL, 2)), $heading_width2[2], "center", "0", "TRBL");
	$RTF->cell("", $heading_width2[3], "center", "0", "TRBL");
	$RTF->close_line();

	$RTF->ln();

	$RTF->open_line();			
	$RTF->set_font($font,16);
	$RTF->cell($RTF->color("0").$RTF->bold(1)."ระดับผลการประเมิน".$RTF->bold(0), "30", "left", "0");
	$RTF->close_line();
/*
	//หาระดับผลการประเมินหลัก 
	$cmd = "	select		AM_CODE, AM_NAME, AM_POINT_MIN, AM_POINT_MAX, AM_ACTIVE
								from		PER_ASSESS_MAIN where AM_YEAR = '$KF_YEAR' and AM_CYCLE = $KF_CYCLE and PER_TYPE = $PER_TYPE
					order by AM_POINT_MAX desc, AM_CODE desc ";
	$db_dpis->send_cmd($cmd);
	while($data = $db_dpis->get_array()){
		$AM_NAME = $data[AM_NAME];
		$AM_POINT_MIN = $data[AM_POINT_MIN];
		$AM_POINT_MAX = $data[AM_POINT_MAX];

			$RTF->open_line();
			$RTF->set_font($font,16);
			$RTF->cell("", "1", "center", "0");
			$RTF->cellImage("../images/checkbox_blank.jpg", 80, 2, "center", 0);
			$RTF->cell("$AM_NAME", "40", "left", "0");
			$RTF->close_line();

	} //end while
*/	

	$RTF->open_line();
	$RTF->set_font($font,16);
	$RTF->cellImage("../images/checkbox_blank.jpg", 100, 3, "left", 0);
	$RTF->cell("ดีเด่น (ร้อยละ ๙๐ - ๑๐๐)", "40", "left", "0");
	$RTF->close_line();
	$RTF->open_line();
	$RTF->set_font($font,16);
	$RTF->cellImage("../images/checkbox_blank.jpg", 100, 3, "left", 0);
	$RTF->cell("ดีมาก (ร้อยละ ๘๐ - ๘๙)", "40", "left", "0");
	$RTF->close_line();
	$RTF->open_line();
	$RTF->set_font($font,16);
	$RTF->cellImage("../images/checkbox_blank.jpg", 100, 3, "left", 0);
	$RTF->cell("ดี (ร้อยละ ๗๐ - ๗๙)", "40", "left", "0");
	$RTF->close_line();
	$RTF->open_line();
	$RTF->set_font($font,16);
	$RTF->cellImage("../images/checkbox_blank.jpg", 100, 3, "left", 0);
	$RTF->cell("พอใช้ (ร้อยละ ๖๐ - ๖๙)", "40", "left", "0");
	$RTF->close_line();
	$RTF->open_line();
	$RTF->set_font($font,16);
	$RTF->cellImage("../images/checkbox_blank.jpg", 100, 3, "left", 0);
	$RTF->cell("ต้องปรับปรุง (ต่ำกว่าร้อยละ ๖๐)", "40", "left", "0");
	$RTF->close_line();

	$RTF->ln();
	$RTF->open_line();
	$RTF->set_font($font,16);
	$RTF->cell($RTF->color("0").$RTF->bold(1)."ส่วนที่ ๔ ข้อตกลงการปฏิบัติราชการ".$RTF->bold(0), "100", "left", "0");
	$RTF->close_line();
	$RTF->ln();
	
	$RTF->open_line();
	$RTF->cell($RTF->color("0")."     ชื่อ-นามสกุล (ผู้ทำข้อตกลง)  $PER_NAME", "50", "left", "0");
	$RTF->cell($RTF->color("0")."ตำแหน่ง  $PL_NAME", "50", "left", "0");
	$RTF->close_line();
	$RTF->open_line();
	$RTF->cell("ได้เลือกตัวชี้วัดที่ได้รับการกระจายตัวชี้วัด (ตามแผนปฏิบัติราชการ) ตัวชี้วัดตามหน้าที่ความรับผิดชอบหลัก และหรือตัวชี้วัด", "100", "left", "0");
	$RTF->close_line();
	$RTF->open_line();
	$RTF->cell("ที่ได้รับมอบหมายพิเศษดังกล่าวข้างต้น เพื่อขอรับการประเมิน โดยร่วมกับผู้ประเมิน (ผู้รับข้อตกลง) ในการกำหนดน้ำหนักและ", "100", "left", "0");
	$RTF->close_line();
	$RTF->open_line();
	$RTF->cell("เป้าหมายตัวชี้วัด รวมทั้งกำหนดน้ำหนักสมรรถนะหลักในแต่ละสมรรถนะหลัก พร้อมลงชื่อรับทราบข้อตกลงการปฏิบัติราชการ", "100", "left", "0");
	$RTF->close_line();
	$RTF->open_line();
	$RTF->cell("ร่วมกันตั้งแต่เริ่มระยะการประเมิน", "100", "left", "0");
	$RTF->close_line();
	$RTF->ln();

	$RTF->open_line();
	$RTF->set_font($font,16);
	$RTF->color("1");	// 1=DARKGRAY
	$RTF->cell($RTF->color("0")."ลงชื่อ................................................................(ผู้ทำข้อตกลง)", "50", "left", "0");
	$RTF->cell($RTF->color("0")."ลงชื่อ................................................................(ผู้รับข้อตกลง)", "50", "left", "0");
	$RTF->close_line();
	$RTF->open_line();
	$RTF->cell($RTF->color("0")."     ($PER_NAME)", "50", "left", "0");
	$RTF->cell($RTF->color("0")."     ($REVIEW_PER_NAME)", "50", "left", "0");
	$RTF->close_line();
	$RTF->open_line();
	$RTF->cell($RTF->color("0")."ตำแหน่ง  $PL_NAME", "50", "left", "0");
	$RTF->cell($RTF->color("0")."ตำแหน่ง  $REVIEW_PL_NAME", "50", "left", "0");
	$RTF->close_line();
	$RTF->open_line();
	$RTF->cell($RTF->color("0")."     วันที่......................................................", "50", "left", "0");
	$RTF->cell($RTF->color("0")."     วันที่......................................................", "50", "left", "0");
	$RTF->close_line();
	
	//--------------------------------------------------
	$RTF->open_line();
	$RTF->cell("", "100", "left", "0");
	$RTF->close_line();
	
	// ======================= PAGE 3 =====================//	
	$RTF->new_page();
	
	$RTF->open_line();
	$RTF->set_font($font,16);
	$RTF->cell($RTF->color("0").$RTF->bold(1)."ส่วนที่ ๕ การรับทราบผลการประเมิน".$RTF->bold(0), "100", "left", "0");
	$RTF->close_line();

	$RTF->open_line();
	$RTF->set_font($font,16);
	$RTF->cell(" ผู้รับการประเมิน :", "100", "left", "16");
	$RTF->close_line();

	$RTF->open_line();
	$RTF->set_font($font,16);
	if ($PER_NAME)
		$RTF->cellImage("../images/checkbox_check.jpg", 100, 3, "center", 0);
	else
		$RTF->cellImage("../images/checkbox_blank.jpg", 100, 3, "center", 0);
	$RTF->cell("ได้รับทราบผลการประเมินและแผนพัฒนาการปฏิบัติราชการรายบุคคลแล้ว", "100", "left", "0");
	$RTF->close_line();
	
	if($SESS_E_SIGN[1]==1){	// ใช้รูปแบบของลายเซ็นอิเล็คทรอนิกส์	
		$PIC_SIGN = getPIC_SIGN($PER_ID,$PER_CARDNO);
		if($PIC_SIGN){
			$RTF->open_line();
			$RTF->cell("", "50", "left", "0");
			$RTF->cellImage($PIC_SIGN, 20, 100, "right", 0);
			$RTF->close_line();
		}else{
			$RTF->open_line();
			$RTF->set_font($font,16);
			$RTF->cell("", "50", "left", "0");
			$RTF->cell(("ลงชื่อ ". str_repeat(".", 100)), "50", "right", "0");
			$RTF->close_line();
		}
	}else{
		$RTF->open_line();
		$RTF->set_font($font,16);
		$RTF->cell("", "50", "left", "0");
		$RTF->cell(("ลงชื่อ ". str_repeat(".", 100)), "50", "right", "0");
		$RTF->close_line();
	}	
	
	$RTF->open_line();
	$RTF->set_font($font,16);
	$RTF->cell("", "50", "left", "0");
	$RTF->cell(("( $PER_NAME )"), "50", "center", "0");
	$RTF->close_line();

	$RTF->open_line();
	$RTF->set_font($font,16);
	$RTF->cell("", "50", "left", "0");
	$RTF->cell(("ตำแหน่ง ".convert2thaidigit("$PL_NAME$POSITION_LEVEL")), "50", "center", "0");
	$RTF->close_line();

	$RTF->open_line();
	$RTF->set_font($font,16);
	$RTF->cell("", "50", "left", "0");
	$RTF->cell(("วันที่ ". str_repeat(".", 13) ." เดือน ". str_repeat(".", 30) ." พ.ศ. ". str_repeat(".", 20)), "50", "center", "0");
	$RTF->close_line();

	$RTF->ln();

	$RTF->open_line();
	$RTF->set_font($font,16);
	$RTF->cell(" ผู้ประเมิน :", "100", "left", "16");
	$RTF->close_line();

	$RTF->open_line();
	$RTF->set_font($font,16);
	$RTF->cellImage("../images/checkbox_blank.jpg", 100, 3, "center", 0);
	$RTF->cell("ได้แจ้งผลการประเมินและผู้รับการประเมินได้ลงนามรับทราบแล้ว", "100", "left", "0");
	$RTF->close_line();

	$RTF->open_line();
	$RTF->set_font($font,16);
	$RTF->cellImage("../images/checkbox_blank.jpg", 100, 3, "center", 0);
	$RTF->cell("ได้แจ้งผลการประเมิน เมื่อวันที่ ". str_repeat(".", 50), "100", "left", "0");
	$RTF->close_line();

	$RTF->open_line();
	$RTF->set_font($font,16);
	$RTF->cell("", "1", "left", "0");
	$RTF->cell("แต่ผู้รับการประเมินไม่ลงนามรับทราบ", "99", "left", "0");
	$RTF->close_line();

	$RTF->open_line();
	$RTF->set_font($font,16);
	$RTF->cell("", "1", "left", "0");
	$RTF->cell("โดยมี ". str_repeat(".", 75)."เป็นพยาน", "99", "left", "0");
	$RTF->close_line();

	//ลงชื่อพยาน
	$RTF->open_line();
	$RTF->set_font($font,16);
	$RTF->cell("", "1", "left", "0");
	$RTF->cell(("ลงชื่อ ". str_repeat(".", 80)."พยาน"), "99", "left", "0");
	$RTF->close_line();

	$RTF->open_line();
	$RTF->set_font($font,16);
	$RTF->cell("", "1", "left", "0");
	$RTF->cell(("ตำแหน่ง". str_repeat(".", 77)), "99", "left", "0");
	$RTF->close_line();

	$RTF->open_line();
	$RTF->set_font($font,16);
	$RTF->cell("", "1", "left", "0");
	$RTF->cell(("วันที่ ". str_repeat(".", 13) ." เดือน ". str_repeat(".", 30) ." พ.ศ. ". str_repeat(".", 20)), "99", "left", "0");
	$RTF->close_line();
	
	//ลงชื่อผู้ประเมิน
	if($SESS_E_SIGN[1]==1){	// ใช้รูปแบบของลายเซ็นอิเล็คทรอนิกส์	
		$PIC_SIGN = getPIC_SIGN($PER_ID_REVIEW,$PER_CARDNO_REVIEW);
		if($PIC_SIGN){
			$RTF->open_line();
			$RTF->cell("", "50", "left", "0");
			$RTF->cellImage($PIC_SIGN, 20, 50, "right", 0);
			$RTF->close_line();
		}else{
		$RTF->open_line();
		$RTF->set_font($font,16);
		$RTF->cell("", "50", "left", "0");
		$RTF->cell(("ลงชื่อ ". str_repeat(".", 100)), "50", "left", "0");
		$RTF->close_line();
		}
	}else{
		$RTF->open_line();
		$RTF->set_font($font,16);
		$RTF->cell("", "50", "left", "0");
		$RTF->cell(("ลงชื่อ ". str_repeat(".", 100)), "50", "right", "0");
		$RTF->close_line();
	}

	$RTF->open_line();
	$RTF->set_font($font,16);
	$RTF->cell("", "50", "left", "0");
	$RTF->cell(("( $REVIEW_PER_NAME )"), "50", "center", "0");
	$RTF->close_line();

	$RTF->open_line();
	$RTF->set_font($font,16);
	$RTF->cell("", "50", "left", "0");
	$RTF->cell(("ตำแหน่ง $REVIEW_PL_NAME "), "50", "center", "0");
	$RTF->close_line();

	$RTF->open_line();
	$RTF->set_font($font,16);
	$RTF->cell("", "50", "left", "0");
	$RTF->cell(("วันที่ ". str_repeat(".", 13) ." เดือน ". str_repeat(".", 30) ." พ.ศ. ". str_repeat(".", 20)), "50", "center", "0");
	$RTF->close_line();
	$RTF->ln();
			
	$RTF->open_line();
	$RTF->set_font($font,16);
	$RTF->cell($RTF->color("0").$RTF->bold(1)."ส่วนที่ ๖ ความเห็นของผู้บังคับบัญชาเหนือขึ้นไป (ถ้ามี)".$RTF->bold(0), "100", "left", "0");
	$RTF->close_line();

	$RTF->ln();

	$RTF->open_line();
	$RTF->set_font($font,16);
	$RTF->cell(" ผู้บังคับบัญชาเหนือขึ้นไป :", "100", "left", "16");
	$RTF->close_line();
	
	$RTF->open_line();
	if(trim($AGREE_REVIEW1)) 
		$RTF->cellImage("../images/checkbox_check.jpg", 100, 3, "left", 0);
	else 
		$RTF->cellImage("../images/checkbox_blank.jpg", 100, 3, "left", 0);
	$RTF->set_font($font,16);
	$RTF->cell("เห็นด้วยกับผลการประเมิน", "90", "left", "0");
	$RTF->close_line();

	$RTF->open_line();
	$RTF->set_font($font,16);
	$RTF->cell(convert2thaidigit($AGREE_REVIEW1), "100", "left", "0");
	$RTF->close_line();

	$RTF->open_line();
	if(trim($DIFF_REVIEW1)) 
		$RTF->cellImage("../images/checkbox_check.jpg", 100, 3, "left", 0);
	else 
		$RTF->cellImage("../images/checkbox_blank.jpg", 100, 3, "left", 0);
	$RTF->set_font($font,16);
	$RTF->cell("มีความเห็นต่าง ดังนี้", "90", "left", "0");
	$RTF->close_line();

	$RTF->open_line();
	$RTF->set_font($font,16);
	$RTF->cell(convert2thaidigit($DIFF_REVIEW1), "95", "left", "0");
	$RTF->close_line();

	if($SESS_E_SIGN[1]==1){	// ใช้รูปแบบของลายเซ็นอิเล็คทรอนิกส์	
		$PIC_SIGN = getPIC_SIGN($PER_ID_REVIEW1,$PER_CARDNO_REVIEW1);
		if($PIC_SIGN){
			$RTF->open_line();
			$RTF->cell("", "50", "left", "0");
			$RTF->cellImage($PIC_SIGN, 20, 50, "right", 0);
			$RTF->close_line();
		}else{
			$RTF->open_line();
			$RTF->set_font($font,16);
			$RTF->cell("", "50", "left", "0");
			$RTF->cell(("ลงชื่อ ". str_repeat(".", 100)), "50", "center", "0");
			$RTF->close_line();
		}
	}else{
		$RTF->open_line();
		$RTF->set_font($font,16);
		$RTF->cell("", "50", "left", "0");
		$RTF->cell(("ลงชื่อ ". str_repeat(".", 100)), "50", "center", "0");
		$RTF->close_line();
	}

	$RTF->open_line();
	$RTF->set_font($font,16);
	$RTF->cell("", "50", "left", "0");
	$RTF->cell(("( $REVIEW_PER_NAME1 )"), "50", "center", "0");
	$RTF->close_line();

	$RTF->open_line();
	$RTF->set_font($font,16);
	$RTF->cell("", "50", "left", "0");
	$RTF->cell(("ตำแหน่ง $REVIEW_PL_NAME1"), "50", "center", "0");
	$RTF->close_line();

	$RTF->open_line();
	$RTF->set_font($font,16);
	$RTF->cell("", "50", "left", "0");
	$RTF->cell(("วันที่ ". str_repeat(".", 13) ." เดือน ". str_repeat(".", 30) ." พ.ศ. ". str_repeat(".", 20)), "50", "center", "0");
	$RTF->close_line();

	$RTF->open_line();
	$RTF->set_font($font,16);
	$RTF->cell(" ผู้บังคับบัญชาเหนือขึ้นไปอีกชั้นหนึ่ง (ถ้ามี) :", "100", "left", "16");
	$RTF->close_line();

	$RTF->open_line();
	$RTF->set_font($font,16);
	if(trim($AGREE_REVIEW2)) 
		$RTF->cellImage("../images/checkbox_check.jpg", 100, 3, "center", 0);
	else 
		$RTF->cellImage("../images/checkbox_blank.jpg", 100, 3, "center", 0);
	$RTF->cell("เห็นด้วยกับผลการประเมิน", "90", "left", "0");
	$RTF->close_line();

	$RTF->open_line();
	$RTF->set_font($font,16);
	$RTF->cell(convert2thaidigit($AGREE_REVIEW2), "100", "center", "0");
	$RTF->close_line();

	$RTF->open_line();
	$RTF->set_font($font,16);
	if(trim($DIFF_REVIEW2)) 
		$RTF->cellImage("../images/checkbox_check.jpg", 100, 3, "center", 0);
	else 
		$RTF->cellImage("../images/checkbox_blank.jpg", 100, 3, "center", 0);
	$RTF->cell("มีความเห็นต่าง ดังนี้", "90", "left", "0");
	$RTF->close_line();

	$RTF->open_line();
	$RTF->set_font($font,16);
	$RTF->cell(convert2thaidigit($DIFF_REVIEW2), "99", "left", "0");
	$RTF->close_line();

	if($SESS_E_SIGN[1]==1){	// ใช้รูปแบบของลายเซ็นอิเล็คทรอนิกส์	
		$PIC_SIGN = getPIC_SIGN($PER_ID_REVIEW2,$PER_CARDNO_REVIEW2);
		if($PIC_SIGN){
			$RTF->open_line();
			$RTF->cell("", "50", "left", "0");
			$RTF->cellImage($PIC_SIGN, 20, 50, "right", 0);
			$RTF->close_line();
		}else{
			$RTF->open_line();
			$RTF->set_font($font,16);
			$RTF->cell("", "50", "center", "0");
			$RTF->cell(("ลงชื่อ ". str_repeat(".", 100)), "50", "center", "0");
			$RTF->close_line();
		}
	}else{
		$RTF->open_line();
		$RTF->set_font($font,16);
		$RTF->cell("", "50", "center", "0");
		$RTF->cell(("ลงชื่อ ". str_repeat(".", 100)), "50", "center", "0");
		$RTF->close_line();
	}

	$RTF->open_line();
	$RTF->set_font($font,16);
	$RTF->cell("", "50", "center", "0");
	$RTF->cell(("( $REVIEW_PER_NAME2 )"), "50", "center", "0");
	$RTF->close_line();

	$RTF->open_line();
	$RTF->set_font($font,16);
	$RTF->cell("", "50", "center", "0");
	$RTF->cell(("ตำแหน่ง $REVIEW_PL_NAME2"), "50", "center", "0");
	$RTF->close_line();

	$RTF->open_line();
	$RTF->set_font($font,16);
	$RTF->cell("", "50", "center", "0");
	$RTF->cell(("วันที่ ". str_repeat(".", 13) ." เดือน ". str_repeat(".", 30) ." พ.ศ. ". str_repeat(".", 20)), "50", "center", "0");
	$RTF->close_line();
	$RTF->ln();

	$RTF->display($fname);

	ini_set("max_execution_time", 30);
?>