<?
	header('Content-Type: text/html; charset=windows-874');
	
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php");

	ini_set("max_execution_time", $max_execution_time); 
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	function getPIC_SIGN($PER_CARDNO){
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
		if (trim($data[PER_CARDNO]) && trim($data[PER_CARDNO]) != "NULL") {
			$TMP_PIC_NAME = $data[PER_PICPATH].$data[PER_CARDNO]."-".$SIGN_NAME.$T_PIC_SEQ.".jpg".($tmp_SERVER_NAME ? " [".$tmp_SERVER_NAME."]" : "");
			//$TMP_SHOW_PIC = ($tmp_http_server ? $tmp_http_server."/" : "").$data[PER_PICPATH].$data[PER_CARDNO]."-".$SIGN_NAME.$T_PIC_SEQ.".jpg";
		} else {
			$TMP_PIC_NAME = $data[PER_PICPATH].$data[PER_GENNAME]."-".$SIGN_NAME.$T_PIC_SEQ.".jpg".($tmp_SERVER_NAME ? " [".$tmp_SERVER_NAME."]" : "");
			//$TMP_SHOW_PIC = ($tmp_http_server ? $tmp_http_server."/" : "").$data[PER_PICPATH].$data[PER_CARDNO]."-".$SIGN_NAME.$T_PIC_SEQ.".jpg";
		}
		//--------------------------------------------------------------------------------
		$PIC_SIGN = "../../attachments/".$PER_CARDNO."/PER_SIGN/".$TMP_PIC_NAME;
		} //end count	
	return $PIC_SIGN;
	}	

	//หาชื่อหน่วยงาน
	if($PV_CODE){
		$select_org_name = "PV_ENG_NAME";
		$find_org_name = "PV_CODE='$PV_CODE'";
	}else{
		$select_org_name = "ORG_ENG_NAME";
		$find_org_name = "ORG_ID='$DEPARTMENT_ID'";
	}
	$cmd = " select $select_org_name from PER_ORG where $find_org_name";
	if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	$db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();
	$data = $db_dpis->get_array();
	$ORG_ENG_NAME = trim($data[$select_org_name]);
	//echo $cmd;

	$cmd = " select 	KF_CYCLE, KF_START_DATE, KF_END_DATE, PER_ID, PER_CARDNO, PER_ID_REVIEW, PER_ID_REVIEW1, PER_ID_REVIEW2,
						SCORE_KPI, SUM_KPI, SCORE_COMPETENCE, SUM_COMPETENCE, SCORE_OTHER, SUM_OTHER, RESULT_COMMENT, 
						COMPETENCE_COMMENT, SALARY_RESULT, SALARY_REMARK1, SALARY_REMARK2, AGREE_REVIEW1, DIFF_REVIEW1, 
						AGREE_REVIEW2, DIFF_REVIEW2, PERFORMANCE_WEIGHT, COMPETENCE_WEIGHT, OTHER_WEIGHT
			   from 		PER_KPI_FORM
			   where 	KF_ID=$KF_ID ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$KF_CYCLE = trim($data[KF_CYCLE]);
	$KF_START_DATE = show_date_format($data[KF_START_DATE], 1);
	$KF_END_DATE = show_date_format($data[KF_END_DATE], 1);
	$KF_YEAR = substr($KF_END_DATE, 6, 4);

	$PER_ID = $data[PER_ID];
	$PER_CARDNO = trim($data[PER_CARDNO]);		
	$PER_ID_REVIEW = $data[PER_ID_REVIEW];
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

	$cmd = " select 	PN_CODE, PER_NAME, PER_SURNAME, LEVEL_NO, PER_SALARY, PER_TYPE, POS_ID, POEM_ID, POEMS_ID
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
	$POEM_ID = trim($data[POEM_ID]);
	$POEMS_ID = trim($data[POEMS_ID]);

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

	if($PER_TYPE==1){
		$cmd = " select 	b.PL_NAME, c.ORG_NAME, a.PT_CODE, d.PT_NAME 
						 from 		PER_POSITION a, PER_LINE b, PER_ORG c, PER_TYPE d
						 where	a.POS_ID=$POS_ID and a.PL_CODE=b.PL_CODE and a.ORG_ID=c.ORG_ID and a.PT_CODE=d.PT_CODE ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PL_NAME = trim($data[PL_NAME]);
		$ORG_NAME = trim($data[ORG_NAME]);
		$PT_CODE = trim($data[PT_CODE]);
		$PT_NAME = trim($data[PT_NAME]);
	}elseif($PER_TYPE==2){
		$cmd = " select 	b.PN_NAME, c.ORG_NAME 
						 from 		PER_POS_EMP a, PER_POS_NAME b, PER_ORG c
						 where	a.POEM_ID=$POEM_ID and a.PN_CODE=b.PN_CODE and a.ORG_ID=c.ORG_ID ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PL_NAME = trim($data[PN_NAME]);
		$ORG_NAME = trim($data[ORG_NAME]);
	}elseif($PER_TYPE==3){
		$cmd = " select 	b.EP_NAME, c.ORG_NAME 
						 from 		PER_POS_EMPSER a, PER_EMPSER_POS_NAME b, PER_ORG c
						 where	a.POEMS_ID=$POEMS_ID and a.EP_CODE=b.EP_CODE and a.ORG_ID=c.ORG_ID ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PL_NAME = trim($data[EP_NAME]);
		$ORG_NAME = trim($data[ORG_NAME]);
	} // end if

	$cmd = " select 	PN_CODE, PER_NAME, PER_SURNAME, PER_TYPE, POS_ID, POEM_ID, POEMS_ID, LEVEL_NO
					 from		PER_PERSONAL
					 where	PER_ID=$PER_ID_REVIEW ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$REVIEW_PN_CODE = trim($data[PN_CODE]);
	$REVIEW_PER_NAME = trim($data[PER_NAME]);
	$REVIEW_PER_SURNAME = trim($data[PER_SURNAME]);
	$REVIEW_PER_TYPE = trim($data[PER_TYPE]);
	$REVIEW_POS_ID = trim($data[POS_ID]);
	$REVIEW_POEM_ID = trim($data[POEM_ID]);
	$REVIEW_POEMS_ID = trim($data[POEMS_ID]);
	$REVIEW_LEVEL_NO = trim($data[LEVEL_NO]);

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
	
	if($REVIEW_PER_TYPE==1){
		$cmd = " select 	b.PL_NAME, a.PT_CODE, c.PT_NAME, a.PM_CODE 
						 from 		PER_POSITION a, PER_LINE b, PER_TYPE c
						 where	a.POS_ID=$REVIEW_POS_ID and a.PL_CODE=b.PL_CODE and a.PT_CODE=c.PT_CODE ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$REVIEW_PL_NAME = trim($data[PL_NAME]);
		$REVIEW_PT_CODE = trim($data[PT_CODE]);
		$REVIEW_PT_NAME = trim($data[PT_NAME]);
		$REVIEW_PL_NAME = trim($REVIEW_PL_NAME)?($REVIEW_PL_NAME . $REVIEW_POSITION_LEVEL . (($REVIEW_PT_NAME != "ทั่วไป" && $REVIEW_LEVEL_NO >= 6)?"$REVIEW_PT_NAME":"")):$REVIEW_LEVEL_NAME;
		$REVIEW_PM_CODE = trim($data[PM_CODE]);
		if($REVIEW_PM_CODE){
			$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$REVIEW_PM_CODE' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$REVIEW_PL_NAME = $data[PM_NAME]." ($REVIEW_PL_NAME)"; 
		} // end if
	}elseif($REVIEW_PER_TYPE==2){
		$cmd = " select 	b.PN_NAME
						 from 		PER_POS_EMP a, PER_POS_NAME b
						 where	a.POEM_ID=$REVIEW_POEM_ID and a.PN_CODE=b.PN_CODE ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$REVIEW_PL_NAME = trim($data[PN_NAME]);
	}elseif($REVIEW_PER_TYPE==3){
		$cmd = " select 	b.EP_NAME
						 from 		PER_POS_EMPSER a, PER_EMPSER_POS_NAME b
						 where	a.POEMS_ID=$REVIEW_POEMS_ID and a.EP_CODE=b.EP_CODE ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$REVIEW_PL_NAME = trim($data[EP_NAME]);
	} // end if
	
	$cmd = " select 	PN_CODE, PER_NAME, PER_SURNAME, PER_TYPE, POS_ID, POEM_ID, POEMS_ID, LEVEL_NO
					 from		PER_PERSONAL
					 where	PER_ID=$PER_ID_REVIEW1 ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$REVIEW_PN_CODE1 = trim($data[PN_CODE]);
	$REVIEW_PER_NAME1 = trim($data[PER_NAME]);
	$REVIEW_PER_SURNAME1 = trim($data[PER_SURNAME]);
	$REVIEW_PER_TYPE1 = trim($data[PER_TYPE]);
	$REVIEW_POS_ID1 = trim($data[POS_ID]);
	$REVIEW_POEM_ID1 = trim($data[POEM_ID]);
	$REVIEW_POEMS_ID1 = trim($data[POEMS_ID]);
	$REVIEW_LEVEL_NO1 = trim($data[LEVEL_NO]);

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
	
	if($REVIEW_PER_TYPE1==1){
		$cmd = " select 	b.PL_NAME, a.PT_CODE, c.PT_NAME, a.PM_CODE 
						 from 		PER_POSITION a, PER_LINE b, PER_TYPE c
						 where	a.POS_ID=$REVIEW_POS_ID1 and a.PL_CODE=b.PL_CODE and a.PT_CODE=c.PT_CODE ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$REVIEW_PL_NAME1 = trim($data[PL_NAME]);
		$REVIEW_PT_CODE1 = trim($data[PT_CODE]);
		$REVIEW_PT_NAME1 = trim($data[PT_NAME]);
		$REVIEW_PL_NAME1 = trim($REVIEW_PL_NAME1)?($REVIEW_PL_NAME1 . $REVIEW_POSITION_LEVEL1 . (($REVIEW_PT_NAME1 != "ทั่วไป" && $REVIEW_LEVEL_NO1 >= 6)?"$REVIEW_PT_NAME1":"")):$REVIEW_LEVEL_NAME1;
		$REVIEW_PM_CODE1 = trim($data[PM_CODE]);
		if($REVIEW_PM_CODE1){
			$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$REVIEW_PM_CODE1' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$REVIEW_PL_NAME1 = $data[PM_NAME]." ($REVIEW_PL_NAME1)";
		} // end if
	}elseif($REVIEW_PER_TYPE1==2){
		$cmd = " select 	b.PN_NAME
						 from 		PER_POS_EMP a, PER_POS_NAME b
						 where	a.POEM_ID=$REVIEW_POEM_ID1 and a.PN_CODE=b.PN_CODE ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$REVIEW_PL_NAME1 = trim($data[PN_NAME]);
	}elseif($REVIEW_PER_TYPE1==3){
		$cmd = " select 	b.EP_NAME
						 from 		PER_POS_EMPSER a, PER_EMPSER_POS_NAME b
						 where	a.POEMS_ID=$REVIEW_POEMS_ID1 and a.EP_CODE=b.EP_CODE ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$REVIEW_PL_NAME1 = trim($data[EP_NAME]);
	} // end if
  
	$cmd = " select 	PN_CODE, PER_NAME, PER_SURNAME, PER_TYPE, POS_ID, POEM_ID, POEMS_ID, LEVEL_NO
					 from		PER_PERSONAL
					 where	PER_ID=$PER_ID_REVIEW2 ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$REVIEW_PN_CODE2 = trim($data[PN_CODE]);
	$REVIEW_PER_NAME2 = trim($data[PER_NAME]);
	$REVIEW_PER_SURNAME2 = trim($data[PER_SURNAME]);
	$REVIEW_PER_TYPE2 = trim($data[PER_TYPE]);
	$REVIEW_POS_ID2 = trim($data[POS_ID]);
	$REVIEW_POEM_ID2 = trim($data[POEM_ID]);
	$REVIEW_POEMS_ID2 = trim($data[POEMS_ID]);
	$REVIEW_LEVEL_NO2 = trim($data[LEVEL_NO]);

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
	
	if($REVIEW_PER_TYPE2==1){
		$cmd = " select 	b.PL_NAME, a.PT_CODE, c.PT_NAME, a.PM_CODE 
						 from 		PER_POSITION a, PER_LINE b, PER_TYPE c
						 where	a.POS_ID=$REVIEW_POS_ID2 and a.PL_CODE=b.PL_CODE and a.PT_CODE=c.PT_CODE ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$REVIEW_PL_NAME2 = trim($data[PL_NAME]);
		$REVIEW_PT_CODE2 = trim($data[PT_CODE]);
		$REVIEW_PT_NAME2 = trim($data[PT_NAME]);
		$REVIEW_PL_NAME2 = trim($REVIEW_PL_NAME2)?($REVIEW_PL_NAME2 . $REVIEW_POSITION_LEVEL2 . (($REVIEW_PT_NAME2 != "ทั่วไป" && $REVIEW_LEVEL_NO2 >= 6)?"$REVIEW_PT_NAME2":"")):$REVIEW_LEVEL_NAME2;
		$REVIEW_PM_CODE2 = trim($data[PM_CODE]);
		if($REVIEW_PM_CODE2){
			$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$REVIEW_PM_CODE2' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$REVIEW_PL_NAME2 = $data[PM_NAME]." ($REVIEW_PL_NAME2)";
		} // end if
	}elseif($REVIEW_PER_TYPE2==2){
		$cmd = " select 	b.PN_NAME
						 from 		PER_POS_EMP a, PER_POS_NAME b
						 where	a.POEM_ID=$REVIEW_POEM_ID2 and a.PN_CODE=b.PN_CODE ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$REVIEW_PL_NAME2 = trim($data[PN_NAME]);
	}elseif($REVIEW_PER_TYPE2==3){
		$cmd = " select 	b.EP_NAME
						 from 		PER_POS_EMPSER a, PER_EMPSER_POS_NAME b
						 where	a.POEMS_ID=$REVIEW_POEMS_ID2 and a.EP_CODE=b.EP_CODE ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$REVIEW_PL_NAME2 = trim($data[EP_NAME]);
	} // end if

	$cmd = " select 	a.PG_ID, a.PG_SEQ, a.KPI_NAME, c.PFR_NAME, a.KPI_WEIGHT, a.PG_RESULT, a.PG_EVALUATE,
						a.KPI_TARGET_LEVEL1, a.KPI_TARGET_LEVEL2, a.KPI_TARGET_LEVEL3, a.KPI_TARGET_LEVEL4, a.KPI_TARGET_LEVEL5,
						a.KPI_TARGET_LEVEL1_DESC, a.KPI_TARGET_LEVEL2_DESC, a.KPI_TARGET_LEVEL3_DESC, a.KPI_TARGET_LEVEL4_DESC, a.KPI_TARGET_LEVEL5_DESC
			   from 		PER_PERFORMANCE_GOALS a, PER_KPI b, PER_PERFORMANCE_REVIEW c
			   where 	a.KF_ID=$KF_ID and a.KPI_ID=b.KPI_ID and b.PFR_ID=c.PFR_ID
			   order by	a.PG_SEQ ";
	$db_dpis->send_cmd($cmd);
	//echo "<br>$cmd<br>";
	while($data = $db_dpis->get_array()){
		$PG_ID = $data[PG_ID];
		$PG_SEQ = $data[PG_SEQ];
		$PFR_NAME = $data[PFR_NAME];
		$KPI_NAME = $data[KPI_NAME];
		$KPI_WEIGHT = $data[KPI_WEIGHT];
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
		
//		$TOTAL_KPI_EVALUATE += $PG_EVALUATE;
		$TOTAL_KPI_EVALUATE += ($PG_EVALUATE * $KPI_WEIGHT);
		$TOTAL_KPI_WEIGHT += $KPI_WEIGHT;
	} // end while
	
	$COUNT_KPI = count($ARR_KPI);
	
	//สมรรถนะ
	$cmd = " select 	a.KC_ID, a.CP_CODE, b.CP_NAME, b.CP_MODEL, a.KC_EVALUATE, a.KC_WEIGHT, a.PC_TARGET_LEVEL
			   from 		PER_KPI_COMPETENCE a, PER_COMPETENCE b
			   where 	a.KF_ID=$KF_ID and a.CP_CODE=b.CP_CODE
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
		
		$ARR_COMPETENCE[$KC_ID] = $CP_NAME.(($CP_MODEL==2)?" *":"");
		$ARR_COMPETENCE_TARGET[$KC_ID] = $PC_TARGET_LEVEL;
		$ARR_COMPETENCE_EVALUATE[$KC_ID] = $KC_EVALUATE;
		$ARR_COMPETENCE_WEIGHT[$KC_ID]=$KC_WEIGHT;
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
	
	$cmd = " select 	IPIP_ID, DEVELOP_SEQ, DEVELOP_COMPETENCE, DEVELOP_METHOD, DEVELOP_INTERVAL
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
	} // end while

	$COUNT_IPIP = count($ARR_IPIP);

	// =============================== START GEN PDF ========================//

	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "";
	$report_title = "";
	$report_code = "";
	$orientation='P';

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
	
	$heading_width1[0] = "75";
	$heading_width1[1] = "75";
	$heading_width1[2] = "10";
	
	$heading_width2[0] = "80";
	$heading_width2[1] = "40";
	$heading_width2[2] = "40";
	$heading_width2[3] = "40";
	
	$heading_width3[0] = "93";
	$heading_width3[1] = "55";
	$heading_width3[2] = "52";

	$heading_width4[0] = "85";
	$heading_width4[1] = "18";
	$heading_width4[2] = "25";
	$heading_width4[3] = "18";
	$heading_width4[4] = "18";
	$heading_width4[5] = "40";

	$heading_width5[0] = "94";
	$heading_width5[1] = "10";
	$heading_width5[2] = "18";
	$heading_width5[3] = "18";
	$heading_width5[4] = "24";
	
	$heading_width6[0] = "135";
	$heading_width6[1] = "15";
	$heading_width6[2] = "10";
	
	function print_header($header_select){
		global $pdf, $heading_width1, $heading_width2, $heading_width3, $heading_width4, $heading_width5, $heading_width6;
		
		if($header_select == 2){
			$pdf->SetFont($font,'',14);
			$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
			$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
			$pdf->Cell($heading_width2[0] ,7,"องค์ประกอบการประเมิน",'LTBR',0,'C',1);
			$pdf->Cell($heading_width2[1] ,7,"คะแนน (ก)",'LTBR',0,'C',1);
			$pdf->Cell($heading_width2[2] ,7,"น้ำหนัก (ข)",'LTBR',0,'C',1);
			$pdf->Cell($heading_width2[3] ,7,"รวมคะแนน (ก)x(ข)",'LTBR',1,'C',1);
		}elseif($header_select == 3){
			$pdf->SetFont($font,'',14);
			$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
			$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
			$pdf->Cell($heading_width3[0] ,7,"ความรู้/ ทักษะ/ สมรรถนะ",'LTR',0,'C',1);
			$pdf->Cell($heading_width3[1] ,12,"",'LTR',0,'C',1);
			$pdf->Cell($heading_width3[2] ,12,"",'LTR',1,'C',1);

			$pdf->y -= 7;
			$pdf->Cell($heading_width3[0] ,12,"ที่ต้องได้รับการพัฒนา",'LBR',0,'C',1);
			$pdf->Cell($heading_width3[1] ,12,"วิธีการพัฒนา",'LBR',0,'C',1);
			$pdf->Cell($heading_width3[2] ,12,"ช่วงเวลาที่ต้องการการพัฒนา",'LBR',1,'C',1);
		}elseif($header_select == 4){
			$pdf->SetFont($font,'',14);
			$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
			$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
			$pdf->Cell($heading_width4[0] ,7,"สมรรถนะ",'LTBR',0,'C',1);
			$pdf->Cell($heading_width4[1] ,7,"ระดับที่",'LTBR',0,'C',1);
			$pdf->Cell($heading_width4[2] ,7,"ผลการประเมิน (ก)",'LTBR',0,'C',1);
			$pdf->Cell($heading_width4[3] ,7,"น้ำหนัก (ข)",'LTBR',0,'C',1);
			$pdf->Cell($heading_width4[4] ,7,"รวมคะแนน",'LTBR',0,'C',1);
			$pdf->Cell($heading_width4[5] ,7,"บันทึกโดยผู้ประเมิน (ถ้ามี)",'LTBR',1,'C',1);
			
			/*$pdf->y -= 7;
			$pdf->Cell($heading_width4[1] ,7,"คาดหวัง",'LBR',0,'C',1);
			$pdf->Cell($heading_width4[4] ,7,"(กxข)",'LBR',0,'C',1);
			$pdf->Cell($heading_width4[5] ,7,"พื้นที่ไม่พอ ให้บันทึกลงในเอกสารหน้าหลัง",'LBR',1,'C',1);  */

			//กรอบขวา
/***
			$pdf->Cell(17, 7, "แนวทางการประเมินสมรรถนะ", 0, 0, 'L', 0);

			$pdf->Cell(17, 7, "ได้นำคะแนนมาจากแบบประเมินสมรรถนะอื่นๆ มาสรุปไว้ในแบบสรุปนี้", 0, 0, 'L', 0);
			$pdf->Cell(17, 7, "ระบุ ..........................................................................................................", 0, 0, 'L', 0);
			
			$pdf->Cell(17, 7, "ใช้แบบสรุปฯนี้ในการประเมินสมรรถนะ โดยตั้งมาตรวัดสมรรถนะซึ่งส่วนราชการเห็นว่ามีความเหมาะสมไว้ดังนี้", 0, 0, 'L', 0);
			
			$pdf->Cell(17, 7, "คะแนน		นิยาม", 0, 0, 'L', 0);
			$pdf->Cell(17, 7, "๑					ไม่สังเกตุเห็น (Not Abserve)", 0, 0, 'L', 0);
			$pdf->Cell(17, 7, "๑					กำลังพัฒนา โดยต้องใช้เวลาอีกมากจึงจะพัฒนาได้", 0, 0, 'L', 0);
			$pdf->Cell(17, 7, "๓					กำลังพัฒนา	 (Developing)", 0, 0, 'L', 0);
			$pdf->Cell(17, 7, "๔					อยู่ในระดับที่ใช้งานได้ (Sufficient)", 0, 0, 'L', 0);
			$pdf->Cell(17, 7, "๕					เป็นแบบอย่างที่ดีให้กับผู้อื่น (Role Model)", 0, 0, 'L', 0);
***/
		}elseif($header_select == 5){	//แบบสรุปการประเมินผลสัมฤทธิ์ของงาน (PAGE 6)
			$pdf->SetFont($font,'',14);
			$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
			$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	
			$pdf->Cell($heading_width5[0] ,14,"ตัวชี้วัดผลงาน",'LTBR',0,'C',1);
			$pdf->Cell(($heading_width5[1] * 5) ,7, "คะแนนตามระดับค่าเป้าหมาย",'LTBR',0,'C',1);
			$pdf->Cell($heading_width5[2] ,14,"คะแนน (ก)",'LTBR',0,'C',1);
			$pdf->Cell($heading_width5[3] ,14,"น้ำหนัก (ข)",'LTBR',0,'C',1);
			$pdf->Cell($heading_width5[4] ,14,"รวมคะแนน (กxข)",'LTBR',0,'C',1);

			$pdf->x = ($heading_width5[0]+5);
			$pdf->y = $pdf->y + 7;
			for($i=1; $i<=5; $i++){ 
				if($i < 5) $pdf->Cell($heading_width5[1] ,7, convert2thaidigit($i),'LTBR',0,'C',1);
				else $pdf->Cell($heading_width5[1] ,7, convert2thaidigit($i),'LTBR',1,'C',1);
			} // end for
		}elseif($header_select == 6){
			$pdf->SetFont($font,'',14);
			$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
			$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	
			$pdf->Cell($heading_width6[0] ,14,"ผลงานจริง",'LTBR',0,'C',1);
			$pdf->Cell($heading_width6[1] ,14,"น้ำหนัก",'LTBR',0,'C',1);
			$pdf->Cell(($heading_width6[2] * 5) ,7, "ผลการประเมิน",'LTBR',1,'C',1);
			
			$pdf->x += ($heading_width6[0] + $heading_width6[1]);
			for($i=1; $i<=5; $i++){ 
				if($i < 5) $pdf->Cell($heading_width6[2] ,7, convert2thaidigit($i),'LTBR',0,'C',1);
				else $pdf->Cell($heading_width6[2] ,7, convert2thaidigit($i),'LTBR',1,'C',1);
			} // end for
		}	// end if
	} // function		

	$pdf->AutoPageBreak = false;
	
	$pdf->Image("../images/logo_ocsc.jpg", 95, 15, 20, 26,"jpg");		// Original size = 50x65 => 20x26

	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(200, 38, "", 0, 1, 'C', 0);
	$pdf->SetFont($font,'',25);
	$pdf->Cell(200, 7, "$DEPARTMENT_NAME", 0, 1, 'C', 0);
	$pdf->SetFont($font,'',20);
	//$pdf->Cell(200, 10, "Office of the Civil Service Commission (OCSC)", 0, 1, 'C', 0);
	$pdf->Cell(200, 7, "$ORG_ENG_NAME", 0, 1, 'C', 0);

	$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->SetFont($fontb,'',20);
	$pdf->Cell(200, 10, "แบบสรุปการประเมินผลการปฏิบัติราชการ", 0, 1, 'C', 1);
	$pdf->Cell(200, 3, "", 0, 1, 'C', 0);
	$pdf->SetFont($font,'',14);
	$pdf->SetDrawColor(hexdec("00"),hexdec("66"),hexdec("CC"));
	$pdf->SetFillColor(hexdec("99"),hexdec("CC"),hexdec("FF"));
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(200, 10, "ส่วนที่ ๑:  ข้อมูลของผู้รับการประเมิน", 1, 1, 'L', 1);
	$pdf->Cell(200, 3, "", 0, 1, 'C', 0);
	
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->SetFont($fontb,'',18);
	$pdf->Cell(15, 10, "", 0, 0, 'C', 0);
	$pdf->Cell(30, 10, "รอบการประเมิน", 0, 0, 'L', 0);
	$pdf->Cell(15, 10, "", 0, 0, 'L', 0);
	$pdf->Cell(25, 10, convert2thaidigit("รอบที่ ๑"), 0, 0, 'L', 0);
	$pdf->Cell(22, 10, convert2thaidigit("๑ ตุลาคม "), 0, 0, 'L', 0);
	$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
	$pdf->Cell(18, 10, convert2thaidigit(($KF_CYCLE==1)?($KF_YEAR - 1):""), 0, 0, 'L', 0);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(32, 10, convert2thaidigit("ถึง ๓๑ มีนาคม "), 0, 0, 'L', 0);
	$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
	$pdf->Cell(20, 10, convert2thaidigit(($KF_CYCLE==1)?$KF_YEAR:""), 0, 1, 'L', 0);
	if($KF_CYCLE==1) $pdf->Image("../images/checkbox_check.jpg", 58, ($pdf->y - 7), 4, 4,"jpg");
	else $pdf->Image("../images/checkbox_blank.jpg", 58, ($pdf->y - 7), 4, 4,"jpg");

	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->SetFont($fontb,'',18);
	$pdf->Cell(15, 10, "", 0, 0, 'C', 0);
	$pdf->Cell(30, 10, "", 0, 0, 'L', 0);
	$pdf->Cell(15, 10, "", 0, 0, 'L', 0);
	$pdf->Cell(25, 10, convert2thaidigit("รอบที่ ๒"), 0, 0, 'L', 0);
	$pdf->Cell(22, 10, convert2thaidigit("๑ เมษายน "), 0, 0, 'L', 0);
	$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
	$pdf->Cell(18, 10, convert2thaidigit(($KF_CYCLE==2)?$KF_YEAR:""), 0, 0, 'L', 0);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(32, 10, convert2thaidigit("ถึง ๓๐ กันยายน "), 0, 0, 'L', 0);
	$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
	$pdf->Cell(20, 10, convert2thaidigit(($KF_CYCLE==2)?$KF_YEAR:""), 0, 1, 'L', 0);	
	if($KF_CYCLE==2) $pdf->Image("../images/checkbox_check.jpg", 58, ($pdf->y - 7), 4, 4,"jpg");
	else $pdf->Image("../images/checkbox_blank.jpg", 58, ($pdf->y - 7), 4, 4,"jpg");

	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->SetFont($fontb,'',18);
	$pdf->Cell(200, 3, "", 0, 1, 'C', 0);
	$pdf->Cell(15, 10, "", 0, 0, 'C', 0);
	$pdf->Cell(40, 10, "ชื่อผู้รับการประเมิน  ", 0, 0, 'L', 0);
	$pdf->SetFont($fontb,'',18);
	$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
	$pdf->Cell(130, 10, "$PER_NAME  ", 0, 1, 'L', 0);
	
	$pdf->SetFont($fontb,'',18);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(200, 3, "", 0, 1, 'C', 0);
	$pdf->Cell(15, 10, "", 0, 0, 'C', 0);
	$pdf->Cell(15, 10, "ตำแหน่ง  ", 0, 0, 'L', 0);
	$pdf->SetFont($fontb,'',16);
	$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
	$pdf->Cell(15, 10, "", 0, 0, 'C', 0);
	$pdf->Cell(15, 10, convert2thaidigit("$PL_NAME  "), 0, 1, 'L', 0);
	
	if ($PER_TYPE==1) {
		$pdf->SetFont($fontb,'',18);
		$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
		$pdf->Cell(200, 3, "", 0, 1, 'C', 0);
		$pdf->Cell(15, 10, "", 0, 0, 'C', 0);
		$pdf->Cell(15, 10, "ประเภทตำแหน่ง  ", 0, 0, 'L', 0);
		$pdf->SetFont($fontb,'',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
		$pdf->Cell(15, 10, "", 0, 0, 'C', 0);
		$pdf->Cell(15, 10, convert2thaidigit("$POSITION_TYPE  "), 0, 0, 'L', 0);
		$pdf->SetFont($fontb,'',18);
		$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
		$pdf->Cell(40, 10, "", 0, 0, 'C', 0);
		$pdf->Cell(40, 10, "ระดับตำแหน่ง  ", 0, 0, 'L', 0);
		$pdf->SetFont($fontb,'',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
		$pdf->Cell(130, 10, convert2thaidigit("$POSITION_LEVEL  "), 0, 1, 'L', 0);
	} elseif ($PER_TYPE==3) {
		$pdf->SetFont($fontb,'',18);
		$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
		$pdf->Cell(40, 10, "", 0, 0, 'C', 0);
		$pdf->Cell(40, 10, "กลุ่มงาน  ", 0, 0, 'L', 0);
		$pdf->SetFont($fontb,'',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
		$pdf->Cell(130, 10, convert2thaidigit("$POSITION_LEVEL  "), 0, 1, 'L', 0);
	}
	
	$pdf->SetFont($fontb,'',18);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(200, 3, "", 0, 1, 'C', 0);
	$pdf->Cell(15, 10, "", 0, 0, 'C', 0);
	$pdf->Cell(15, 10, "สังกัด  ", 0, 0, 'L', 0);
	$pdf->SetFont($fontb,'',16);
	$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
	$pdf->Cell(15, 10, "", 0, 0, 'C', 0);
	$pdf->Cell(15, 10, convert2thaidigit("$ORG_NAME  "), 0, 1, 'L', 0);
	
	$pdf->SetFont($fontb,'',18);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(200, 3, "", 0, 1, 'C', 0);
	$pdf->Cell(15, 10, "", 0, 0, 'C', 0);
	$pdf->Cell(15, 10, "เงินเดือน  ", 0, 0, 'L', 0);
	$pdf->SetFont($fontb,'',16);
	$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
	$pdf->Cell(15, 10, "", 0, 0, 'C', 0);
	$pdf->Cell(15, 10, convert2thaidigit(number_format($PER_SALARY)), 0, 0, 'L', 0);
	$pdf->SetFont($fontb,'',18);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(15, 10, "บาท", 0, 1, 'L', 0);

	$pdf->SetFont($fontb,'',18);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(200, 4, "", 0, 1, 'C', 0);
	$pdf->Cell(15, 10, "", 0, 0, 'C', 0);
	$pdf->Cell(50, 10, "ชื่อผู้ประเมิน  ", 0, 0, 'L', 0);
	$pdf->SetFont($fontb,'',18);
	$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
	$pdf->Cell(130, 10, $REVIEW_PER_NAME, 0, 1, 'L', 0);

	$pdf->SetFont($fontb,'',18);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(15, 10, "", 0, 0, 'C', 0);
	$pdf->Cell(20, 10, "ตำแหน่ง  ", 0, 0, 'L', 0);
	$pdf->SetFont($fontb,'',16);
	$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
	$pdf->Cell(150, 10, convert2thaidigit("$REVIEW_PL_NAME"), 0, 1, 'L', 0);
	
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->SetFont($fontb,'',16);
	$pdf->Cell(200, 3, "", 0, 1, 'C', 0);	
	$pdf->Cell(10, 10, "", 0, 0, 'C', 0);
	$pdf->SetDrawColor(hexdec("00"),hexdec("66"),hexdec("CC"));
	$pdf->SetFillColor(hexdec("99"),hexdec("CC"),hexdec("FF"));
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(180, 10, "คำชี้แจง", 0, 1, 'L', 1);

	$pdf->SetFont($font,'',14);
	$pdf->Cell(200, 3, "", 0, 1, 'C', 0);	
	$pdf->Cell(12, 7, "", 0, 0, 'C', 0);
	$pdf->Cell(170, 7, " แบบสรุปการประเมินผลการปฏิบัติราชการนี้ มีด้วยกัน ๓ หน้า ประกอบด้วย", 0, 1, 'L', 0);

	$pdf->SetFont($fontb,'',14);
	$pdf->Cell(17, 7, "", 0, 0, 'C', 0);
	$pdf->Cell(17, 7, "ส่วนที่ ๑: ", 0, 0, 'L', 0);
	$pdf->Cell(40, 7, "ข้อมูลของผู้รับการประเมิน", 0, 0, 'L', 0);
	$pdf->SetFont($font,'',14);
	$pdf->Cell(40, 7, "เพื่อระบุรายละเอียดต่างๆ ที่เกี่ยวข้องกับตัวผู้รับการประเมิน", 0, 1, 'L', 0);

	$pdf->SetFont($fontb,'',14);
	$pdf->Cell(17, 7, "", 0, 0, 'C', 0);
	$pdf->Cell(17, 7, "ส่วนที่ ๒: ", 0, 0, 'L', 0);
	$pdf->Cell(40, 7, "สรุปผลการประเมิน", 0, 0, 'L', 0);
	$pdf->SetFont($font,'',14);
	$pdf->Cell(40, 7, "ใช้เพื่อกรอกค่าคะแนนการประเมินในองค์ประกอบด้านผลสัมฤทธิ์ของงาน องค์ประกอบ", 0, 1, 'L', 0);
	$pdf->SetFont($font,'',14);
	$pdf->Cell(17, 7, "", 0, 0, 'C', 0);
	$pdf->Cell(17, 7, "ด้านพฤติกรรมการปฏิบัติราชการ และน้ำหนักของทั้งสององค์ประกอบ ในแบบส่วนสรุปส่วนที่ ๒ นี้", 0, 1, 'L', 0);
	$pdf->SetFont($font,'',14);
	$pdf->Cell(17, 7, "", 0, 0, 'C', 0);
	$pdf->Cell(17, 7, "ยังใช้สำหรับคำนวณคะแนนผลการปฏิบัติราชการรวมด้วย", 0, 1, 'L', 0);
	$pdf->SetFont($font,'',14);
	$pdf->Cell(17, 7, "", 0, 0, 'C', 0);
	$pdf->Cell(17, 7, "- สำหรับคะแนนองค์ประกอบด้านผลสัมฤทธิ์ของงาน ให้นำมาจากแบบประเมินผลสัมฤทธิ์ของงาน โดยให้แนบท้ายแบบสรุปฉบับนี้", 0, 1, 'L', 0);
	$pdf->SetFont($font,'',14);
	$pdf->Cell(17, 7, "", 0, 0, 'C', 0);
	$pdf->Cell(17, 7, "- สำหรับคะแนนองค์ประกอบด้านพฤติกรรมการปฏิบัติราชการ ให้นำมาจากแบบประเมินสมรรถนะ โดยให้แนบท้ายแบบสรุปฉบับนี้", 0, 1, 'L', 0);

	$pdf->SetFont($fontb,'',14);
	$pdf->Cell(17, 7, "", 0, 0, 'C', 0);
	$pdf->Cell(17, 7, "ส่วนที่ ๓: ", 0, 0, 'L', 0);
	$pdf->Cell(40, 7, "แผนพัฒนาการปฏิบัติราชการรายบุคคล", 0, 0, 'L', 0);
	$pdf->SetFont($font,'',14);
	$pdf->Cell(17, 7, "", 0, 0, 'C', 0);
	$pdf->Cell(40, 7, "ผู้ประเมิน และผู้รับการประเมินร่วมกันจัดทำแผนพัฒนาผลการปฏิบัติราชการ", 0, 1, 'L', 0);

	$pdf->SetFont($fontb,'',14);
	$pdf->Cell(17, 7, "", 0, 0, 'C', 0);
	$pdf->Cell(17, 7, "ส่วนที่ ๔: ", 0, 0, 'L', 0);
	$pdf->Cell(40, 7, "การรับทราบผลการประเมิน", 0, 0, 'L', 0);
	$pdf->SetFont($font,'',14);
	$pdf->Cell(17, 7, "", 0, 0, 'C', 0);
	$pdf->Cell(40, 7, "ผู้รับการประเมินลงนามรับทราบผลการประเมิน", 0, 1, 'L', 0);
		
	$pdf->SetFont($fontb,'',14);
	$pdf->Cell(17, 7, "", 0, 0, 'C', 0);
	$pdf->Cell(17, 7, "ส่วนที่ ๕: ", 0, 0, 'L', 0);
	$pdf->Cell(40, 7, "ความเห็นของผู้บังคับบัญชาเหนือขึ้นไป", 0, 0, 'L', 0);
	$pdf->SetFont($font,'',14);
	$pdf->Cell(17, 7, "", 0, 0, 'C', 0);
	$pdf->Cell(40, 7, "ผู้บังคับบัญชาเหนือขึ้นไปกลั่นกรองผลการประเมิน แผนพัฒนาผลการปฏิบัติ", 0, 1, 'L', 0);
	$pdf->SetFont($font,'',14);
	$pdf->Cell(17, 7, "", 0, 0, 'C', 0);
	$pdf->Cell(17, 7, " ราชการและให้ความเห็น", 0, 1, 'L', 0);
	
	$pdf->SetFont($font,'',14);
//	$pdf->Cell(220, 7, "", 0, 1, 'C', 0);
	$pdf->Cell(17, 7, "", 0, 0, 'C', 0);
	$pdf->Cell(170, 7, "คำว่า 'ผู้บังคับบัญชาเหนือขึ้นไป' สำหรับผู้ประเมินตามข้อ ๒ (๙) หมายถึงหัวหน้าส่วนราชการประจำจังหวัดผู้บังคับบัญชาของผู้รับการประเมิน", 0, 1, 'C', 0);

	// ======================= PAGE 2 =====================//	
	$pdf->AddPage();
	$pdf->SetFont($font,'',14);

	$pdf->SetDrawColor(hexdec("00"),hexdec("66"),hexdec("CC"));
	$pdf->SetFillColor(hexdec("99"),hexdec("CC"),hexdec("FF"));
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(200, 7, "ส่วนที่ ๒: การสรุปผลการประเมิน", 1, 1, 'L', 1);
	$pdf->Cell(200, 3, "", 0, 1, 'C', 0);

	print_header(2);

	$border = "";
	$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;

	$pdf->SetFont($font,'',12);
	$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

	$pdf->Cell($heading_width2[0], 7, convert2thaidigit("องค์ประกอบที่ ๑: ผลสัมฤทธิ์ของงาน"), 'LTBR', 0, 'L', 0);
	$pdf->Cell($heading_width2[1], 7, convert2thaidigit(number_format($SHOW_SCORE_KPI, 2)), 'LTBR', 0, 'C', 0);
	if ($PERFORMANCE_WEIGHT)
		$pdf->Cell($heading_width2[2], 7, convert2thaidigit($PERFORMANCE_WEIGHT . "%"), 'LTBR', 0, 'C', 0);
	else
		$pdf->Cell($heading_width2[2], 7, "", 'LTBR', 0, 'C', 0);
	$pdf->Cell($heading_width2[3], 7, convert2thaidigit(number_format($SUM_KPI, 2)), 'LTBR', 1, 'C', 0);
	
	//แถวที่ 2
	$pdf->Cell($heading_width2[0], 7, convert2thaidigit("องค์ประกอบที่ ๒: พฤติกรรมการปฏิบัติราชการ (สมรรถนะ)"), 'LTBR', 0, 'L', 0);
	$pdf->Cell($heading_width2[1], 7, convert2thaidigit(number_format($SHOW_SCORE_COMPETENCE, 2)), 'LTBR', 0, 'C', 0);
	if ($COMPETENCE_WEIGHT)
		$pdf->Cell($heading_width2[2], 7, convert2thaidigit($COMPETENCE_WEIGHT . "%"), 'LTBR', 0, 'C', 0);
	else
		$pdf->Cell($heading_width2[2], 7, "", 'LTBR', 0, 'C', 0);
	$pdf->Cell($heading_width2[3], 7, convert2thaidigit(number_format($SUM_COMPETENCE, 2)), 'LTBR', 1, 'C', 0);
	
	//แถวที่ 2
	$pdf->Cell($heading_width2[0], 7, convert2thaidigit("องค์ประกอบอื่นๆ (ถ้ามี)"), 'LTBR', 0, 'L', 0);
	$pdf->Cell($heading_width2[1], 7, convert2thaidigit(number_format($SHOW_SCORE_OTHER, 2)), 'LTBR', 0, 'C', 0);
	if ($OTHER_WEIGHT)
		$pdf->Cell($heading_width2[2], 7, convert2thaidigit($OTHER_WEIGHT . "%"), 'LTBR', 0, 'C', 0);
	else
		$pdf->Cell($heading_width2[2], 7, "", 'LTBR', 0, 'C', 0);
	$pdf->Cell($heading_width2[3], 7, convert2thaidigit(number_format($SUM_OTHER, 2)), 'LTBR', 1, 'C', 0);
	
	//แถวที่ 3
	$pdf->Cell($heading_width2[0] + $heading_width2[1] , 7, "รวม", 'LTBR', 0, 'R', 0);
	$pdf->Cell($heading_width2[2], 7,convert2thaidigit("๑๐๐%"), 'LTBR', 0, 'C', 0);
	$pdf->Cell($heading_width2[3], 7, convert2thaidigit(number_format($SUM_TOTAL, 2)), 'LTBR', 1, 'C', 0);

	//================= Draw Border Line ====================
	$line_start_y = $start_y;		$line_start_x = $start_x;
	$line_end_y = $max_y;		$line_end_x = $start_x;
	$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
		
	for($i=0; $i<=4; $i++){
		$line_start_y = $start_y;		$line_start_x += $heading_width2[$i];
		$line_end_y = $max_y;		$line_end_x += $heading_width2[$i];
		$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
	} // end for
	//====================================================

	if(($pdf->h - $max_y - 10) < 16){ 
		$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
		if($data_count < $COUNT_KPI){
			$pdf->AddPage();
			print_header(2);
			$max_y = $pdf->y;
		} // end if
	}else{
		$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
	} // end if
	$pdf->x = $start_x;			$pdf->y = $max_y;

	$pdf->SetFont($font,'',14);
	$pdf->SetDrawColor(hexdec("00"),hexdec("66"),hexdec("CC"));
	$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(200, 35, "", 0, 1, 'C', 0);
	$pdf->Cell(40, 7, "ระดับผลการประเมิน", 0, 1, 'C', 1);
	$pdf->Cell(400, 3, "", 0, 1, 'C', 0);	
	
	//หาระดับผลการประเมินหลัก 
	$cmd = "	select		AM_CODE, AM_NAME, AM_POINT_MIN, AM_POINT_MAX, AM_ACTIVE
								from		PER_ASSESS_MAIN where PER_TYPE = $PER_TYPE
					order by AM_POINT_MIN desc ";
	$db_dpis->send_cmd($cmd);
	while($data = $db_dpis->get_array()){
		$AM_NAME = $data[AM_NAME];
		$AM_POINT_MIN = $data[AM_POINT_MIN];
		$AM_POINT_MAX = $data[AM_POINT_MAX];

//	if($SUM_TOTAL >= $AM_POINT_MIN && $SUM_TOTAL <= $AM_POINT_MAX){
//			$pdf->SetFont($fontb,'',16);
//			$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
//			$show_checkbox = "../images/checkbox_check.jpg";
//	}else{
			$pdf->SetFont($font,'',14);
			$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
			$show_checkbox = "../images/checkbox_blank.jpg";
//	}
		$pdf->Cell(25, 8, "", 0, 0, 'C', 0);
		$pdf->Cell(10, 8, "", 0, 0, 'C', 0);
		$pdf->Image($show_checkbox, ($pdf->x - 7), ($pdf->y + 2), 4, 4,"jpg");
		$pdf->Cell(50, 8, "$AM_NAME", 0, 1, 'L', 0);
//		$pdf->Cell(30, 8, convert2thaidigit("$AM_POINT_MIN %")." - ".convert2thaidigit("$AM_POINT_MAX %"), 0, 1, 'L', 0);
	} //end while
	
	//--------------------------------------------------
	$pdf->Cell(200, 7, "", 0, 1, 'C', 0);
	$pdf->SetFont($font,'',14);
	$pdf->SetDrawColor(hexdec("00"),hexdec("66"),hexdec("CC"));
	$pdf->SetFillColor(hexdec("99"),hexdec("CC"),hexdec("FF"));
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(200, 7, "ส่วนที่ ๓: แผนพัฒนาการปฏิบัติราชการรายบุคคล", 1, 1, 'L', 1);
	$pdf->Cell(200, 3, "", 0, 1, 'C', 0);
	
	print_header(3);

	$data_count = 0;
	foreach($ARR_IPIP as $IPIP_ID => $DEVELOP_COMPETENCE){
		$data_count++;
		$border = "";
		$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;

		$pdf->SetFont($font,'',12);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

		$pdf->MultiCell($heading_width3[0], 7, convert2thaidigit($DEVELOP_COMPETENCE), $border, "L");
		if($pdf->y > $max_y) $max_y = $pdf->y;
		$pdf->x = $start_x + $heading_width3[0];
		$pdf->y = $start_y;
		$pdf->MultiCell($heading_width3[1], 7, convert2thaidigit($ARR_IPIP_METHOD[$IPIP_ID]), $border, "L");
		if($pdf->y > $max_y) $max_y = $pdf->y;
		$pdf->x = $start_x + $heading_width3[0] + $heading_width3[1];
		$pdf->y = $start_y;
		$pdf->MultiCell($heading_width3[2], 7, convert2thaidigit($ARR_IPIP_INTERVAL[$IPIP_ID]), $border, "L");
		if($pdf->y > $max_y) $max_y = $pdf->y;
		$pdf->x = $start_x + $heading_width3[0] + $heading_width3[1] + $heading_width3[2];
		$pdf->y = $start_y;

		//================= Draw Border Line ====================
		$line_start_y = $start_y;		$line_start_x = $start_x;
		$line_end_y = $max_y;		$line_end_x = $start_x;
		$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
			
		for($i=0; $i<=2; $i++){
			$line_start_y = $start_y;		$line_start_x += $heading_width3[$i];
			$line_end_y = $max_y;		$line_end_x += $heading_width3[$i];
			
			$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
		} // end for
		//====================================================

		if(($pdf->h - $max_y - 10) < 15){ 
			$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
			if($data_count < $COUNT_IPIP){
				$pdf->AddPage();
				print_header(3);
				$max_y = $pdf->y;
			} // end if
		}else{
			$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
		} // end if

		$pdf->x = $start_x;			$pdf->y = $max_y;
	} // end foreach

	// ======================= PAGE 3 =====================//	
	$pdf->AddPage();
	$pdf->SetFont($font,'',14);

	$pdf->SetDrawColor(hexdec("00"),hexdec("66"),hexdec("CC"));
	$pdf->SetFillColor(hexdec("99"),hexdec("CC"),hexdec("FF"));
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(200, 7, "ส่วนที่ ๔: การรับทราบผลการประเมิน", 1, 1, 'L', 1);
	$pdf->Cell(200, 3, "", 0, 1, 'C', 0);

	$pdf->SetFont($font,'',14);
	$pdf->SetDrawColor(hexdec("00"),hexdec("66"),hexdec("CC"));
	$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(200, 3, "", 0, 1, 'C', 0);
	$pdf->Cell(200, 7, "ผู้รับการประเมิน:", 1, 1, 'L', 1);
	if($PER_NAME) $pdf->Image("../images/checkbox_check.jpg",($pdf->x+1), ($pdf->y+1), 4, 4,"jpg");
	else $pdf->Image("../images/checkbox_blank.jpg", ($pdf->x +1), ($pdf->y+1), 4, 4,"jpg");
	$pdf->Cell(200, 15, "", "LR", 1, 'L', 0);
	$save_x = $pdf->x;		$save_y = $pdf->y;
	$pdf->x += 5;			$pdf->y -= 15;
	$pdf->Cell(190, 7, "ได้รับทราบผลการประเมินและแผนพัฒนาผลการปฏิบัติราชการรายบุคคลแล้ว", 0, 1, 'L', 0);
	
	$PIC_SIGN = getPIC_SIGN($PER_CARDNO);
	//echo $PIC_SIGN;      //function
	if(trim($PIC_SIGN)){
		if(file_exists($PIC_SIGN)){
			$pdf->Image($PIC_SIGN,($pdf->x+1), ($pdf->y+1), 60, 15,"jpg");	// Original size = wxh (60x15)
			$pdf->Cell(100, 5, "", "L", 0, 'L', 0); 
			$save_x = $pdf->x;		$save_y = $pdf->y;
			$pdf->x += 5;			$pdf->y -= 15;
		}
	}else{ 
		$pdf->SetFont($font,'',12);
		$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
		$pdf->Cell(100, 5, "", "L", 0, 'C', 0);
		$pdf->Cell(100, 5, ("ลงชื่อ ". str_repeat(".", 100)), "R", 1, 'C', 0);
	}
	$pdf->Cell(100, 5, "", "L", 0, 'C', 0);
	$pdf->Cell(100, 5, ("( $PER_NAME )"), "R", 1, 'C', 0); 

	$pdf->Cell(100, 5, "", "L", 0, 'C', 0);
	$pdf->Cell(100, 5, ("ตำแหน่ง ".convert2thaidigit("$PL_NAME$POSITION_LEVEL")), "R", 1, 'C', 0);

	$pdf->Cell(100, 5, "", "LB", 0, 'C', 0);
	$pdf->Cell(100, 5, ("วันที่ ". str_repeat(".", 13) ." เดือน ". str_repeat(".", 30) ." พ.ศ. ". str_repeat(".", 20)), "BR", 1, 'C', 0);

	$pdf->SetFont($font,'',14);
	$pdf->SetDrawColor(hexdec("00"),hexdec("66"),hexdec("CC"));
	$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(200, 3, "", 0, 1, 'C', 0);
	$pdf->Cell(200, 7, "ผู้ประเมิน:", 1, 1, 'L', 1);
	///if() $pdf->Image("../images/checkbox_check.jpg",($pdf->x +1), ($pdf->y + 1), 4, 4,"jpg");
	///else $pdf->Image("../images/checkbox_blank.jpg", ($pdf->x +1), ($pdf->y + 1), 4, 4,"jpg");
	$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x  + 1), ($pdf->y + 1), 4, 4,"jpg");
	$pdf->Cell(200, 15, "", "LR", 1, 'L', 0);
	$save_x = $pdf->x;		$save_y = $pdf->y;
	$pdf->x += 5;			$pdf->y -= 15; 
	$pdf->Cell(190, 7, "ได้แจ้งผลการประเมินและผู้รับการประเมินได้ลงนามรับทราบ", 0, 1, 'L', 0);
	///if() $pdf->Image("../images/checkbox_check.jpg",($pdf->x +1), ($pdf->y + 1), 4, 4,"jpg");
	///else $pdf->Image("../images/checkbox_blank.jpg", ($pdf->x +1), ($pdf->y + 1), 4, 4,"jpg");
	$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x + 1), ($pdf->y + 1), 4, 4,"jpg");
	$pdf->Cell(200, 15, "", "LR", 1, 'L', 0);
	$save_x = $pdf->x;		$save_y = $pdf->y;
	$pdf->x += 5;			$pdf->y -= 15;
	$pdf->Cell(190, 7, "ได้แจ้งผลการประเมิน เมื่อวันที่ ". str_repeat(".", 50), 0, 1, 'L', 0);
	$pdf->Cell(200, 15, "", "LR", 1, 'L', 0);
	$save_x = $pdf->x;		$save_y = $pdf->y;
	$pdf->x += 5;			$pdf->y -= 15;
	$pdf->Cell(190, 7, "แต่ผู้รับการประเมินไม่ลงนามรับทราบ", 0, 1, 'L', 0);
	$pdf->Cell(200, 15, "", "LR", 1, 'L', 0);
	$save_x = $pdf->x;		$save_y = $pdf->y;
	$pdf->x += 5;			$pdf->y -= 15;
	$pdf->Cell(190, 7, "โดยมี ". str_repeat(".", 75)."เป็นพยาน", 0, 1, 'L', 0);
	$pdf->Cell(200, 15, "", "LR", 1, 'L', 0);
	$save_x = $pdf->x;		$save_y = $pdf->y;
	$pdf->x += 5;			$pdf->y -= 10;
	//ลงชื่อพยาน
	$pdf->SetFont($font,'',12);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(190, 7, ("ลงชื่อ ". str_repeat(".", 80)."พยาน"), 0, 1, 'L', 0);
	$pdf->Cell(200, 15, "", "LR", 1, 'L', 0);
	$save_x = $pdf->x;		$save_y = $pdf->y;
	$pdf->x += 5;			$pdf->y -= 15;
	$pdf->Cell(190, 7, ("ตำแหน่ง". str_repeat(".", 77)), 0, 1, 'L', 0);
	$pdf->Cell(200, 15, "", "LR", 1, 'L', 0);
	$save_x = $pdf->x;		$save_y = $pdf->y;
	$pdf->x += 5;			$pdf->y -= 15;
	$pdf->Cell(170, 7, ("วันที่ ". str_repeat(".", 13) ." เดือน ". str_repeat(".", 30) ." พ.ศ. ". str_repeat(".", 20)), 0, 1, 'L', 0);
	
	//ลงชื่อผู้ประเมิน
	$pdf->SetFont($font,'',12);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(100, 5, "", "L", 0, 'C', 0);
	$pdf->Cell(100, 5, ("ลงชื่อ ". str_repeat(".", 100)), "R", 1, 'C', 0);

	$pdf->Cell(100, 5, "", "L", 0, 'C', 0);
	$pdf->Cell(100, 5, ("( $REVIEW_PER_NAME )"), "R", 1, 'C', 0);

	$pdf->Cell(100, 5, "", "L", 0, 'C', 0);
	$pdf->Cell(100, 5, ("ตำแหน่ง $REVIEW_PL_NAME "), "R", 1, 'C', 0);

	$pdf->Cell(100, 5, "", "LB", 0, 'C', 0);
	$pdf->Cell(100, 5, ("วันที่ ". str_repeat(".", 13) ." เดือน ". str_repeat(".", 30) ." พ.ศ. ". str_repeat(".", 20)), "BR", 1, 'C', 0);
		
	// ======================= PAGE 4 =====================//	
	$pdf->AddPage();
	$pdf->SetFont($font,'',14);

	$pdf->SetDrawColor(hexdec("00"),hexdec("66"),hexdec("CC"));
	$pdf->SetFillColor(hexdec("99"),hexdec("CC"),hexdec("FF"));
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(200, 7, "ส่วนที่ ๕: ความเห็นของผู้บังคับบัญชาเหนือขึ้นไป", 1, 1, 'L', 1);

	$pdf->SetFont($font,'',14);
	$pdf->SetDrawColor(hexdec("00"),hexdec("66"),hexdec("CC"));
	$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(200, 3, "", 0, 1, 'C', 0);
	$pdf->Cell(200, 7, "ผู้บังคับบัญชาเหนือขึ้นไป:", 1, 1, 'L', 1);	
	
	if(trim($AGREE_REVIEW1)) $pdf->Image("../images/checkbox_check.jpg",($pdf->x +1), ($pdf->y + 1), 4, 4,"jpg");
	else $pdf->Image("../images/checkbox_blank.jpg", ($pdf->x + 1), ($pdf->y + 1), 4, 4,"jpg");
	$pdf->Cell(200, 15, "", "LR", 1, 'L', 0);
	$save_x = $pdf->x;		$save_y = $pdf->y;
	$pdf->x += 5;			$pdf->y -= 15;
	$pdf->Cell(190, 7, "เห็นด้วยกับผลการประเมิน", 0, 1, 'L', 0);

	$pdf->SetFont($font,'',12);
	$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
	$pdf->Cell(200, 15, "", "LR", 1, 'L', 0);
	$save_x = $pdf->x;		$save_y = $pdf->y;
	$pdf->x += 5;			$pdf->y -= 15;
	$pdf->MultiCell(190, 5, convert2thaidigit($AGREE_REVIEW1), "", "L");
	$pdf->x = $save_x;		$pdf->y = $save_y;
	$pdf->SetFont($font,'',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	if(trim($DIFF_REVIEW1)) $pdf->Image("../images/checkbox_check.jpg",($pdf->x +1), ($pdf->y + 1), 4, 4,"jpg");
	else $pdf->Image("../images/checkbox_blank.jpg", ($pdf->x +1), ($pdf->y + 1), 4, 4,"jpg");
	$pdf->Cell(200, 15, "", "LR", 1, 'L', 0);
	$save_x = $pdf->x;		$save_y = $pdf->y;
	$pdf->x += 5;			$pdf->y -= 15;
	$pdf->Cell(190, 7, "มีความเห็นแตกต่าง ดังนี้", 0, 1, 'L', 0);
	$pdf->SetFont($font,'',12);
	$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
	$pdf->Cell(200, 15, "", "LR", 1, 'L', 0);
	$save_x = $pdf->x;		$save_y = $pdf->y;
	$pdf->x += 5;			$pdf->y -= 15;
	$pdf->MultiCell(190, 5, convert2thaidigit($DIFF_REVIEW1), "", "L");
	$pdf->x = $save_x;		$pdf->y = $save_y;

	$pdf->SetFont($font,'',12);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(100, 5, "", "L", 0, 'C', 0);
	$pdf->Cell(100, 5, ("ลงชื่อ ". str_repeat(".", 100)), "R", 1, 'C', 0);

	$pdf->Cell(100, 5, "", "L", 0, 'C', 0);
//	$pdf->Cell(100, 5, ("( ". str_repeat(".", 90) ." )"), "R", 1, 'C', 0);
	$pdf->Cell(100, 5, ("( $REVIEW_PER_NAME1 )"), "R", 1, 'C', 0);

	$pdf->Cell(100, 5, "", "L", 0, 'C', 0);
//	$pdf->Cell(100, 5, ("ตำแหน่ง ". str_repeat(".", 80)), "R", 1, 'C', 0);
	$pdf->Cell(100, 5, ("ตำแหน่ง $REVIEW_PL_NAME1"), "R", 1, 'C', 0);

	$pdf->Cell(100, 5, "", "LB", 0, 'C', 0);
	$pdf->Cell(100, 5, ("วันที่ ". str_repeat(".", 13) ." เดือน ". str_repeat(".", 30) ." พ.ศ. ". str_repeat(".", 20)), "BR", 1, 'C', 0);

	$pdf->SetFont($font,'',14);
	$pdf->SetDrawColor(hexdec("00"),hexdec("66"),hexdec("CC"));
	$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(200, 3, "", 0, 1, 'C', 0);
	$pdf->Cell(200, 7, "ผู้บังคับบัญชาเหนือขึ้นไปอีกชั้นหนึ่ง (ถ้ามี):", 1, 1, 'L', 1);
	if(trim($AGREE_REVIEW2)) $pdf->Image("../images/checkbox_check.jpg",($pdf->x + 1), ($pdf->y + 1), 4, 4,"jpg");
	else $pdf->Image("../images/checkbox_blank.jpg", ($pdf->x + 1), ($pdf->y + 1), 4, 4,"jpg");
	$pdf->Cell(200, 15, "", "LR", 1, 'L', 0);
	$save_x = $pdf->x;		$save_y = $pdf->y;
	$pdf->x += 5;			$pdf->y -= 15;
	$pdf->Cell(190, 7, "เห็นด้วยกับผลการประเมิน", 0, 1, 'L', 0);
	$pdf->SetFont($font,'',12);
	$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
	$pdf->Cell(200, 15, "", "LR", 1, 'L', 0);
	$save_x = $pdf->x;		$save_y = $pdf->y;
	$pdf->x += 5;			$pdf->y -= 15;
	$pdf->MultiCell(190, 5, convert2thaidigit($AGREE_REVIEW2), "", "L");
	$pdf->x = $save_x;		$pdf->y = $save_y;
	$pdf->SetFont($font,'',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	if(trim($DIFF_REVIEW2)) $pdf->Image("../images/checkbox_check.jpg",($pdf->x + 1), ($pdf->y + 1), 4, 4,"jpg");
	else $pdf->Image("../images/checkbox_blank.jpg", ($pdf->x + 1), ($pdf->y + 1), 4, 4,"jpg");
	$pdf->Cell(200, 15, "", "LR", 1, 'L', 0);
	$save_x = $pdf->x;		$save_y = $pdf->y;
	$pdf->x += 5;			$pdf->y -= 15;
	$pdf->Cell(190, 7, "มีความเห็นแตกต่าง ดังนี้", 0, 1, 'L', 0);
	$pdf->SetFont($font,'',12);
	$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
	$pdf->Cell(200, 15, "", "LR", 1, 'L', 0);
	$save_x = $pdf->x;		$save_y = $pdf->y;
	$pdf->x += 5;			$pdf->y -= 15;
	$pdf->MultiCell(190, 5, convert2thaidigit($DIFF_REVIEW2), "", "L");
	$pdf->x = $save_x;		$pdf->y = $save_y;

	$pdf->SetFont($font,'',12);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(100, 5, "", "L", 0, 'C', 0);
	$pdf->Cell(100, 5, ("ลงชื่อ ". str_repeat(".", 100)), "R", 1, 'C', 0);

	$pdf->Cell(100, 5, "", "L", 0, 'C', 0);
//	$pdf->Cell(100, 5, ("( ". str_repeat(".", 90) ." )"), "R", 1, 'C', 0);
	$pdf->Cell(100, 5, ("( $REVIEW_PER_NAME2 )"), "R", 1, 'C', 0);

	$pdf->Cell(100, 5, "", "L", 0, 'C', 0);
//	$pdf->Cell(100, 5, ("ตำแหน่ง ". str_repeat(".", 80)), "R", 1, 'C', 0);
	$pdf->Cell(100, 5, ("ตำแหน่ง $REVIEW_PL_NAME2"), "R", 1, 'C', 0);

	$pdf->Cell(100, 5, "", "LB", 0, 'C', 0);
	$pdf->Cell(100, 5, ("วันที่ ". str_repeat(".", 13) ." เดือน ". str_repeat(".", 30) ." พ.ศ. ". str_repeat(".", 20)), "BR", 1, 'C', 0);

	// ======================= PAGE 5 =====================//	
	$pdf->AddPage();
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->SetFont($fontb,'',16);

	$pdf->SetDrawColor(hexdec("00"),hexdec("66"),hexdec("CC"));
	$pdf->SetFillColor(hexdec("99"),hexdec("CC"),hexdec("FF"));
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(60, 7, "แบบสรุปการประเมินสมรรถนะ", 0, 0, 'L', 0);

	$pdf->SetFont($font,'',14);
	$pdf->SetDrawColor(hexdec("00"),hexdec("66"),hexdec("CC"));
	$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(60, 7, "", 0, 0, 'L', 0);		//space
	$pdf->Cell(40, 7, "รอบการประเมิน", 0, 0, 'L', 0);
	$pdf->Cell(22, 7, convert2thaidigit("รอบที่ ๑"), 0, 0, 'L', 0);
	if($KF_CYCLE==1) $pdf->Image("../images/checkbox_check.jpg", ($pdf->x-27), ($pdf->y+1), 4, 4,"jpg");
	else $pdf->Image("../images/checkbox_blank.jpg", ($pdf->x-27), ($pdf->y+1), 4, 4,"jpg");

	$pdf->SetFont($font,'',14);
	$pdf->SetDrawColor(hexdec("00"),hexdec("66"),hexdec("CC"));
	$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(15, 7, convert2thaidigit("รอบที่ ๒"), 0, 0, 'L', 0);
	if($KF_CYCLE==2) $pdf->Image("../images/checkbox_check.jpg", ($pdf->x-20), ($pdf->y+1), 4, 4,"jpg");
	else $pdf->Image("../images/checkbox_blank.jpg", ($pdf->x-20), ($pdf->y+1), 4, 4,"jpg");

	$pdf->SetFont($font,'',14);
	$pdf->Cell(200, 10, "", 0, 1, 'C', 0);
	$pdf->Cell(100, 7, "ชื่อผู้รับการประเมิน: $PER_NAME",0, 0, 'L', 0);
	$pdf->Cell(100, 7, ("ลงนาม ". str_repeat(".", 100)), 0, 0, 'L', 0);
	
	$pdf->SetFont($font,'',14);
	$pdf->Cell(200, 10, "", 0, 1, 'C', 0);
	$pdf->Cell(100, 7, "ชื่อผู้บังคับบัญชา/ผู้ประเมิน: ".trim($REVIEW_PER_NAME), 0, 0, 'L',0);
	$pdf->Cell(100, 7, "ลงนาม ". str_repeat(".", 100), 0, 0, 'L',0);
	
	$pdf->Cell(10, 10, "", 0, 1, 'C', 0);
	print_header(4);
	
	$data_count = 0;
	foreach($ARR_COMPETENCE as $KC_ID => $CP_NAME){
		$data_count++;
		$border = "";
		$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;

		$pdf->SetFont($font,'',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

		$pdf->MultiCell($heading_width4[0], 7, convert2thaidigit($CP_NAME), $border, "L");
		if($pdf->y > $max_y) $max_y = $pdf->y;
		$pdf->x = $start_x + $heading_width4[0];
		$pdf->y = $start_y;
		$pdf->Cell($heading_width4[1], 7, convert2thaidigit($ARR_COMPETENCE_TARGET[$KC_ID]), $border, 0, 'C', 0);
		$pdf->SetFont($fontb,'',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
		$pdf->Cell($heading_width4[2], 7, convert2thaidigit($ARR_COMPETENCE_EVALUATE[$KC_ID]), $border, 0, 'C', 0);
		if ($ARR_COMPETENCE_WEIGHT[$KC_ID])
			$pdf->Cell($heading_width4[3], 7, convert2thaidigit($ARR_COMPETENCE_WEIGHT[$KC_ID] . "%"), $border, 0, 'C', 0);
		else
			$pdf->Cell($heading_width4[3], 7, "", $border, 0, 'C', 0);
		$pdf->Cell($heading_width4[4], 7,convert2thaidigit($PC_SCORE[$KC_ID]), $border, 0, 'C', 0);
		$pdf->Cell($heading_width4[5], 7, "", $border, 0, 'C', 0);

		//================= Draw Border Line ====================
		$line_start_y = $start_y;		$line_start_x = $start_x;
		$line_end_y = $max_y;		$line_end_x = $start_x;
		$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
			
		for($i=0; $i<=5; $i++){
			$line_start_y = $start_y;		$line_start_x += $heading_width4[$i];
			$line_end_y = $max_y;		$line_end_x += $heading_width4[$i];
			
			$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
		} // end for
		//====================================================

		if(($pdf->h - $max_y - 10) < 15){ 
			$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
			if($data_count < $COUNT_COMPETENCE){
				$pdf->AddPage();
				print_header(4);
				$max_y = $pdf->y;
			} // end if
		}else{
			$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
		} // end if

		$pdf->x = $start_x;			$pdf->y = $max_y;
	} // end foreach
	
	$pdf->SetFont($font,'',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell($heading_width4[0]+$heading_width4[1], 7, "คะแนนรวม ", $border, 0, 'R', 0);
	$pdf->SetFont($fontb,'',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
///	$pdf->Cell(($heading_width4[2] +$heading_width4[3]+$heading_width4[4]+$heading_width4[5]), 7, convert2thaidigit(), 1, 1, 'C', 0);
	$pdf->Cell(($heading_width4[2]), 7,convert2thaidigit(array_sum($ARR_COMPETENCE_EVALUATE)), 1, 0, 'C', 0);
	if (array_sum($ARR_COMPETENCE_WEIGHT))
		$pdf->Cell(($heading_width4[3]), 7,convert2thaidigit(array_sum($ARR_COMPETENCE_WEIGHT) . "%"), 1, 0, 'C', 0);
	else
		$pdf->Cell(($heading_width4[3]), 7,"", 1, 0, 'C', 0);
	$pdf->Cell(($heading_width4[4]), 7,convert2thaidigit($TOTAL_PC_SCORE),1, 0, 'C', 0);
	$pdf->Cell(($heading_width4[5]), 7,"",1, 1, 'C', 0);

	$pdf->SetFont($font,'',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell($heading_width4[0]+$heading_width4[1], 7, "คะแนนประเมิน ", $border, 0, 'R', 0);
	$pdf->SetFont($fontb,'',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
	$pdf->Cell(($heading_width4[2] +$heading_width4[3]+$heading_width4[4]+$heading_width4[5]), 7,convert2thaidigit($TOTAL_PC_SCORE), 1, 1, 'C', 0); //convert2thaidigit(number_format(($TOTAL_KPI_EVALUATE / $TOTAL_KPI_WEIGHT), 2))
		
 /**********************
	//ผลสำเร็จของงานจริง
	$data_count = 0;
	foreach($ARR_KPI as $PG_ID => $KPI_NAME){
		$data_count++;
		$border = "";
		$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
		$max_y = $ARR_KPI_MAXY[$PG_ID];

		$pdf->SetFont($font,'',12);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

		$pdf->MultiCell($heading_width4[0], 7, convert2thaidigit($ARR_KPI_RESULT[$PG_ID]), $border, "L");
		if($pdf->y > $max_y) $max_y = $pdf->y;
		$pdf->x = $start_x + $heading_width4[0];
		$pdf->y = $start_y;
		$pdf->Cell($heading_width4[1], 7, convert2thaidigit($ARR_KPI_WEIGHT[$PG_ID]), $border, 0, 'C', 0);
		for($i=1; $i<5; $i++){ 
			if($i == $ARR_KPI_EVALUATE[$PG_ID]){
				$pdf->SetFont($fontb,'',12);
				$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
				$pdf->Cell($heading_width4[$i+1], 7, $i."+".convert2thaidigit(${"ARR_KPI_TARGET".$i}[$PG_ID]), $border, 0, 'C', 0);
			}else{
				$pdf->SetFont($font,'',12);
				$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
//				$pdf->Cell($heading_width2[$i+1], 7, convert2thaidigit(${"ARR_KPI_TARGET".$i}[$PG_ID]), $border, 0, 'C', 0);
				$pdf->Cell($heading_width4[$i+1], 7, "$i", $border, 0, 'C', 0);
			} // end if			
		} // end for

		//================= Draw Border Line ====================
		$line_start_y = $start_y;		$line_start_x = $start_x;
		$line_end_y = $max_y;		$line_end_x = $start_x;
		$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
			
		for($i=0; $i<=5; $i++){
			$line_start_y = $start_y;		$line_start_x += $heading_width4[$i];
			$line_end_y = $max_y;		$line_end_x += $heading_width4[$i];
			
			$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
		} // end for
		//====================================================

		if(($pdf->h - $max_y - 10) < 15){ 
			$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
			if($data_count < $COUNT_KPI){
				$pdf->AddPage();
				print_header(4);
				$max_y = $pdf->y;
			} // end if
		}else{
			$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
		} // end if

		$pdf->x = $start_x;			$pdf->y = $max_y;
	} // end foreach

	$pdf->SetFont($font,'',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell($heading_width4[0], 7, "คะแนนรวม ", $border, 0, 'R', 0);
	$pdf->SetFont($font,'',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
	$pdf->Cell($heading_width4[1], 7, convert2thaidigit($TOTAL_KPI_WEIGHT), 1, 0, 'R', 0);
	$pdf->SetFont($fontb,'',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
	$pdf->Cell(($heading_width4[2] +$heading_width4[3]+$heading_width4[4]+$heading_width4[5]), 7, convert2thaidigit($TOTAL_KPI_EVALUATE), 1, 1, 'C', 0);

	$pdf->SetFont($font,'',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell($heading_width4[0], 7, "คะแนนประเมิน ", $border, 0, 'R', 0);
	$pdf->Cell($heading_width4[1], 7, "", $border, 0, 'R', 0);
	$pdf->SetFont($fontb,'',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
	$pdf->Cell(($heading_width4[2] +$heading_width4[3]+$heading_width4[4]+$heading_width4[5]), 7, convert2thaidigit(number_format(($TOTAL_KPI_EVALUATE / $TOTAL_KPI_WEIGHT), 2)), 1, 1, 'C', 0);
**********************/

	// ======================= PAGE 6 =====================//	
	$pdf->AddPage();

	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->SetFont($fontb,'',16);
	$pdf->Cell(60, 7, "แบบสรุปการประเมินผลสัมฤทธิ์ของงาน", 0, 0, 'L', 0);
	
	$pdf->SetFont($font,'',14);
	$pdf->SetDrawColor(hexdec("00"),hexdec("66"),hexdec("CC"));
	$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(60, 7, "", 0, 0, 'L', 0);		//space
	$pdf->Cell(40, 7, "รอบการประเมิน", 0, 0, 'L', 0);
//	$pdf->Cell(10, 7, "", 0, 0, 'L', 0);		//space
	$pdf->Cell(22, 7, convert2thaidigit("รอบที่ ๑"), 0, 0, 'L', 0);
	if($KF_CYCLE==1) $pdf->Image("../images/checkbox_check.jpg", ($pdf->x - 27), ($pdf->y +1 ), 4, 4,"jpg");
	else $pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 27), ($pdf->y + 1), 4, 4,"jpg");
	
	$pdf->SetFont($font,'',14);
	$pdf->SetDrawColor(hexdec("00"),hexdec("66"),hexdec("CC"));
	$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(15, 7, convert2thaidigit("รอบที่ ๒"), 0, 0, 'L', 0);
	if($KF_CYCLE==2) $pdf->Image("../images/checkbox_check.jpg", ($pdf->x - 20), ($pdf->y+1), 4, 4,"jpg");
	else $pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 20), ($pdf->y+1), 4, 4,"jpg");
	
	$pdf->SetFont($font,'',14);
	$pdf->Cell(200, 10, "", 0, 1, 'C', 0);
	$pdf->Cell(100, 7, "ชื่อผู้รับการประเมิน: ".trim($PER_NAME),0, 0, 'L', 0);
	$pdf->Cell(100, 7, ("ลงนาม ". str_repeat(".", 100)), 0, 0, 'L', 0);
	
	$pdf->SetFont($font,'',14);
	$pdf->Cell(200, 7, "", 0, 1, 'C', 0);
	$pdf->Cell(100, 7, "ชื่อผู้บังคับบัญชา/ผู้ประเมิน: ".trim($REVIEW_PER_NAME), 0, 0, 'L',0);
	$pdf->Cell(100, 7, "ลงนาม ". str_repeat(".", 100), 0, 0, 'L',0);

	$pdf->Cell(200, 10, "", 0, 1, 'C', 0);
	print_header(5);
	
	$data_count = 0;
	foreach($ARR_KPI as $PG_ID => $KPI_NAME){
		$data_count++;
		$border = "";
		$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;

		$pdf->SetFont($font,'',12);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

		//$pdf->MultiCell($heading_width5[0], 7, convert2thaidigit($ARR_KPI_PFR[$PG_ID]), $border, "L");
		$pdf->MultiCell($heading_width5[0], 7, convert2thaidigit($data_count).". ".convert2thaidigit($KPI_NAME), $border, "L");
		if($pdf->y > $max_y) $max_y = $pdf->y;
		$pdf->x = $start_x + $heading_width5[0];
		$pdf->y = $start_y;
//		echo "$data_count).KPI_NAME=$KPI_NAME<br>";
		for($i=1; $i<=5; $i++) { 
			$pdf->Cell($heading_width5[1], 7, convert2thaidigit(${"ARR_KPI_TARGET".$i}[$PG_ID]), $border, 0, 'C', 0); 
//			$a=${"ARR_KPI_TARGET".$i}[$PG_ID]; echo "     $i.. $a<br>"; 
		}

		for($i=1; $i<=5; $i++){ 
			if($i == $ARR_KPI_EVALUATE[$PG_ID]){
					$SCORE[$PG_ID]=convert2thaidigit(${"ARR_KPI_TARGET".$i}[$PG_ID]);
					$SUMSCORE[$PG_ID]=convert2thaidigit(${"ARR_KPI_TARGET".$i}[$PG_ID]*$ARR_KPI_WEIGHT[$PG_ID]);
					$TOTAL_KPI_RESULT+=${"ARR_KPI_TARGET".$i}[$PG_ID];
//					echo "SCORE[$PG_ID]:".$SCORE[$PG_ID]."<br>";
			}	
		} // end for
		$pdf->SetFont($fontb,'',12);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
		$pdf->Cell($heading_width5[2], 7, $SCORE[$PG_ID], $border, 0, 'C', 0);
		$pdf->Cell($heading_width5[3], 7, convert2thaidigit($ARR_KPI_WEIGHT[$PG_ID]), $border, 0, 'C', 0);
		$pdf->Cell($heading_width5[4], 7, $SUMSCORE[$PG_ID], $border, 0, 'C', 0);

		$ARR_KPI_MAXY[$PG_ID] = $max_y;
//		echo "ARR_KPI_MAXY[$PG_ID]:".$ARR_KPI_MAXY[$PG_ID]."<br>";

		//================= Draw Border Line ====================
		$line_start_y = $start_y;		$line_start_x = $start_x;
		$line_end_y = $max_y;		$line_end_x = $start_x;
		$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
			
		for($i=0; $i<=8; $i++){
			if($i>=1 && $i <=5){
				$line_start_y = $start_y;		$line_start_x += $heading_width5[1];
				$line_end_y = $max_y;		$line_end_x += $heading_width5[1];
			}else if($i>5){
				$line_start_y = $start_y;		$line_start_x += $heading_width5[$i-4];
				$line_end_y = $max_y;		$line_end_x += $heading_width5[$i-4];
			}else{
				$line_start_y = $start_y;		$line_start_x += $heading_width5[$i];
				$line_end_y = $max_y;		$line_end_x += $heading_width5[$i];
			}
			$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
		} // end for
		//====================================================

//		echo "1..pdf->h(".$pdf->h.") - $max_y(".$pdf->y."), data_count($data_count) < COUNT_KPI($COUNT_KPI)<br>";
		if(($pdf->h - $pdf->y - 10) < 15){ 
				$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
				if($data_count < $COUNT_KPI){
//					echo "addpage<br>";
					$pdf->AddPage();
					print_header(5);
					$max_y = $pdf->y;
				} // end if
		}else{
				$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
		} // end if

		$pdf->x = $start_x;			$pdf->y = $max_y;
		
		//===ข้อ 1 - 5===//
		$pdf->SetFont($fontb,'',12);
		$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
		//$pdf->Cell(204, 7, convert2thaidigit("1 = ".$ARR_KPI_TARGET1_DESC[$PG_ID]), 1, 1, 'L', 0);
		$pdf->MultiCell(204, 7, convert2thaidigit("1 = ".$ARR_KPI_TARGET1_DESC[$PG_ID]), 1, "L");

//		echo "2..pdf->h(".$pdf->h.") - $max_y(".$pdf->y."), data_count($data_count) < COUNT_KPI($COUNT_KPI)<br>";
		if(($pdf->h - $pdf->y - 10) < 15){ 
				$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
				if($data_count < $COUNT_KPI){
//					echo "addpage<br>";
					$pdf->AddPage();
					print_header(5);
					$max_y = $pdf->y;
				} // end if
		}else{
				$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
		} // end if

		$pdf->SetFont($fontb,'',12);
		$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
		//$pdf->Cell(204, 7, convert2thaidigit("2 = ".$ARR_KPI_TARGET2_DESC[$PG_ID]), 1, 1, 'L', 0);
		$pdf->MultiCell(204, 7, convert2thaidigit("2 = ".$ARR_KPI_TARGET2_DESC[$PG_ID]), 1, "L");

//		echo "3..pdf->h(".$pdf->h.") - $max_y(".$pdf->y."), data_count($data_count) < COUNT_KPI($COUNT_KPI)<br>";
		if(($pdf->h - $pdf->y - 10) < 15){ 
				$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
				if($data_count < $COUNT_KPI){
//					echo "addpage<br>";
					$pdf->AddPage();
					print_header(5);
					$max_y = $pdf->y;
				} // end if
		}else{
				$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
		} // end if

		$pdf->SetFont($fontb,'',12);
		$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
		//$pdf->Cell(204, 7, convert2thaidigit("3 = ".$ARR_KPI_TARGET3_DESC[$PG_ID]), 1, 1, 'L', 0);
		$pdf->MultiCell(204, 7, convert2thaidigit("3 = ".$ARR_KPI_TARGET3_DESC[$PG_ID]), 1, "L");

//		echo "4..pdf->h(".$pdf->h.") - $max_y(".$pdf->y."), data_count($data_count) < COUNT_KPI($COUNT_KPI)<br>";
		if(($pdf->h - $pdf->y - 10) < 15){ 
				$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
				if($data_count < $COUNT_KPI){
//					echo "addpage<br>";
					$pdf->AddPage();
					print_header(5);
					$max_y = $pdf->y;
				} // end if
		}else{
				$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
		} // end if

		$pdf->SetFont($fontb,'',12);
		$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
		//$pdf->Cell(204, 7, convert2thaidigit("4 = ".$ARR_KPI_TARGET4_DESC[$PG_ID]), 1, 1, 'L', 0);
		$pdf->MultiCell(204, 7, convert2thaidigit("4 = ".$ARR_KPI_TARGET4_DESC[$PG_ID]), 1, "L");

	
//		echo "5..pdf->h(".$pdf->h.") - $max_y(".$pdf->y."), data_count($data_count) < COUNT_KPI($COUNT_KPI)<br>";
		if(($pdf->h - $pdf->y - 10) < 15){ 
				$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
				if($data_count < $COUNT_KPI){
//					echo "addpage<br>";
					$pdf->AddPage();
					print_header(5);
					$max_y = $pdf->y;
				} // end if
		}else{
				$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
		} // end if

		$pdf->SetFont($fontb,'',12);
		$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
		//$pdf->Cell(204, 7, convert2thaidigit("5 = ".$ARR_KPI_TARGET5_DESC[$PG_ID]), 1, 1, 'L', 0);
		$pdf->MultiCell(204, 7, convert2thaidigit("5 = ".$ARR_KPI_TARGET5_DESC[$PG_ID]), 1, "L");

		if(($pdf->h - $pdf->y - 10) < 15){ 
				$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
				if($data_count < $COUNT_KPI){
					$pdf->AddPage();
					print_header(5);
					$max_y = $pdf->y;
				} // end if
		}else{
				$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
		} // end if
		//==============================================================//
	} // end foreach
//	echo "$data_count - ".count($ARR_KPI);

	$pdf->SetFont($font,'',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(($heading_width5[0]+($heading_width5[1]*5)), 7, "คะแนนรวม ", $border, 0, 'R', 0);
	$pdf->SetFont($fontb,'',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
	$pdf->Cell(($heading_width5[2]), 7,convert2thaidigit($TOTAL_KPI_RESULT), 1, 0, 'C', 0);
	$pdf->Cell(($heading_width5[3]), 7,convert2thaidigit($TOTAL_KPI_WEIGHT), 1, 0, 'C', 0);
	$pdf->Cell(($heading_width5[4]), 7,convert2thaidigit($TOTAL_KPI_EVALUATE),1, 1, 'C', 0);

	$pdf->SetFont($font,'',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell($heading_width5[0]+($heading_width5[1]*5), 7, "คะแนนประเมิน ", $border, 0, 'R', 0);
	$pdf->SetFont($fontb,'',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
	/**$pdf->Cell(($heading_width5[2]), 7, "", 1, 0, 'C', 0);
	$pdf->Cell(($heading_width5[3]), 7, "",  1, 0, 'C', 0);
	$pdf->Cell(($heading_width5[4]), 7, "", 1, 1, 'C', 0);	**/
	$pdf->Cell(($heading_width5[2]+$heading_width5[3]+$heading_width5[4]), 7, convert2thaidigit(number_format(($TOTAL_KPI_EVALUATE / $TOTAL_KPI_WEIGHT), 2)), 1, 1, 'C', 0);

/**********************
	// ======================= PAGE 7 =====================//	
	$pdf->AddPage();
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->SetFont($fontb,'',16);
	$pdf->Cell(200, 10, "บันทึกเพิ่มเติมประกอบแบบสรุปการประเมินสมรรถนะ", 0, 1, 'C', 1);
	$pdf->Cell(200, 7, "", 0, 1, 'C', 0);

	$pdf->SetFont($font,'',14);
	$pdf->SetDrawColor(hexdec("00"),hexdec("66"),hexdec("CC"));
	$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(200, 15, "", 0, 1, 'C', 0);
	$save_x = $pdf->x;		$save_y = $pdf->y;
	$pdf->x += 5;			$pdf->y -= 10;
	
	//__1
	$pdf->Cell(250, 7, "สมรรถนะ".str_repeat("_", 30), 0, 1, 'L', 0);
	$pdf->Cell(200, 15, "", 0, 1, 'C', 0);
	$save_x = $pdf->x;		$save_y = $pdf->y;
	$pdf->x += 75;			$pdf->y -= 15;			//space
	$pdf->Cell(190, 7, str_repeat(".", 150), 0, 1, 'L', 0);
	$pdf->Cell(200, 15, "", 0, 1, 'C', 0);
	$save_x = $pdf->x;		$save_y = $pdf->y;
	$pdf->x += 75;			$pdf->y -= 15;			//space
	$pdf->Cell(190, 7, str_repeat(".", 150), 0, 1, 'L', 0);
	$pdf->Cell(200, 15, "", 0, 1, 'C', 0);
	$save_x = $pdf->x;		$save_y = $pdf->y;
	$pdf->x += 75;			$pdf->y -= 15;			//space
	$pdf->Cell(190, 7, str_repeat(".", 150), 0, 1, 'L', 0);
	$pdf->Cell(200, 15, "", 0, 1, 'C', 0);
	$save_x = $pdf->x;		$save_y = $pdf->y;
	$pdf->x += 5;			$pdf->y -=10;
	
	//__2
	$pdf->Cell(250, 7, "สมรรถนะ".str_repeat("_", 30), 0, 1, 'L', 0);
	$pdf->Cell(200, 15, "", 0, 1, 'C', 0);
	$save_x = $pdf->x;		$save_y = $pdf->y;
	$pdf->x += 75;			$pdf->y -= 15;			//space
	$pdf->Cell(190, 7, str_repeat(".", 150), 0, 1, 'L', 0);
	$pdf->Cell(200, 15, "", 0, 1, 'C', 0);
	$save_x = $pdf->x;		$save_y = $pdf->y;
	$pdf->x += 75;			$pdf->y -= 15;			//space
	$pdf->Cell(190, 7, str_repeat(".", 150), 0, 1, 'L', 0);
	$pdf->Cell(200, 15, "", 0, 1, 'C', 0);
	$save_x = $pdf->x;		$save_y = $pdf->y;
	$pdf->x += 75;			$pdf->y -= 15;			//space
	$pdf->Cell(190, 7, str_repeat(".", 150), 0, 1, 'L', 0);
	$pdf->Cell(200, 15, "", 0, 1, 'C', 0);
	$save_x = $pdf->x;		$save_y = $pdf->y;
	$pdf->x += 5;			$pdf->y -=10;
	
	//__3	
	$pdf->Cell(250, 7, "สมรรถนะ".str_repeat("_", 30), 0, 1, 'L', 0);
	$pdf->Cell(200, 15, "", 0, 1, 'C', 0);
	$save_x = $pdf->x;		$save_y = $pdf->y;
	$pdf->x += 75;			$pdf->y -= 15;			//space
	$pdf->Cell(190, 7, str_repeat(".", 150), 0, 1, 'L', 0);
	$pdf->Cell(200, 15, "", 0, 1, 'C', 0);
	$save_x = $pdf->x;		$save_y = $pdf->y;
	$pdf->x += 75;			$pdf->y -= 15;			//space
	$pdf->Cell(190, 7, str_repeat(".", 150), 0, 1, 'L', 0);
	$pdf->Cell(200, 15, "", 0, 1, 'C', 0);
	$save_x = $pdf->x;		$save_y = $pdf->y;
	$pdf->x += 75;			$pdf->y -= 15;			//space
	$pdf->Cell(190, 7, str_repeat(".", 150), 0, 1, 'L', 0);
	$pdf->Cell(200, 15, "", 0, 1, 'C', 0);
	$save_x = $pdf->x;		$save_y = $pdf->y;
	$pdf->x += 5;			$pdf->y -=10;
	
	//__4	
	$pdf->Cell(250, 7, "สมรรถนะ".str_repeat("_", 30), 0, 1, 'L', 0);
	$pdf->Cell(200, 15, "", 0, 1, 'C', 0);
	$save_x = $pdf->x;		$save_y = $pdf->y;
	$pdf->x += 75;			$pdf->y -= 15;			//space
	$pdf->Cell(190, 7, str_repeat(".", 150), 0, 1, 'L', 0);
	$pdf->Cell(200, 15, "", 0, 1, 'C', 0);
	$save_x = $pdf->x;		$save_y = $pdf->y;
	$pdf->x += 75;			$pdf->y -= 15;			//space
	$pdf->Cell(190, 7, str_repeat(".", 150), 0, 1, 'L', 0);
	$pdf->Cell(200, 15, "", 0, 1, 'C', 0);
	$save_x = $pdf->x;		$save_y = $pdf->y;
	$pdf->x += 75;			$pdf->y -= 15;			//space
	$pdf->Cell(190, 7, str_repeat(".", 150), 0, 1, 'L', 0);
	$pdf->Cell(200, 15, "", 0, 1, 'C', 0);
	$save_x = $pdf->x;		$save_y = $pdf->y;
	$pdf->x += 5;			$pdf->y -=10;
*****************************/

/*	if(($pdf->h - $pdf_y) < 60) $pdf->AddPage();
	else $pdf->Cell(200, 5, "", 0, 1, 'C', 0);  */
	
	// ======================= PAGE 7 =====================//	
	$pdf->AddPage();
	
	$pdf->SetFont($font,'',14);
	$pdf->SetDrawColor(hexdec("00"),hexdec("66"),hexdec("CC"));
	$pdf->SetFillColor(hexdec("BB"),hexdec("EE"),hexdec("FF"));
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(200, 7, "ผลสำเร็จของงานจริง", 1, 1, 'L', 1);

	print_header(6);

	$line_end_x = $start_x+$heading_width6[0]+$heading_width6[1]+$heading_width6[2]+$heading_width6[2]+$heading_width6[2]+$heading_width6[2]+$heading_width6[2];
	
	$data_count = 0;
	foreach($ARR_KPI as $PG_ID => $KPI_NAME){
		$data_count++;
		$border = "";
		$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;

//		$max_y = $ARR_KPI_MAXY[$PG_ID];

		$pdf->SetFont($fontb,'',12);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
		$ii = $ARR_KPI_EVALUATE[$PG_ID];
//		echo "ARR_KPI_EVALUATE[$PG_ID]=".$ARR_KPI_EVALUATE[$PG_ID]."<br>";
//		echo "0..$data_count >> pdf->h(".$pdf->h.") - y_multi(".$y_multi.") - max_y(".$max_y.") >> COUNT_KPI=$COUNT_KPI, ii=$ii<br>";
		$y_multi =$max_y + $pdf->y_MultiCellThaiCut($heading_width6[0], 7, convert2thaidigit($ARR_KPI_RESULT[$PG_ID]), $border, "L");
//		echo "1..$data_count >> pdf->h(".$pdf->h.") - y_multi(".$y_multi.") - max_y(".$max_y.") >> COUNT_KPI=$COUNT_KPI, ii=$ii<br>";
		if(($pdf->h - $y_multi - 10) < 15){ 
			$pdf->Line($start_x, $max_y, $line_end_x, $max_y);
			if($data_count <= $COUNT_KPI){
//				echo "AddPage<br>";
				$pdf->AddPage();
				print_header(6);
				$max_y = $pdf->y;
			} // end if
			$pdf->x = $start_x;			$pdf->y = $max_y;
			$start_x = $pdf->x;			$start_y = $pdf->y;
		} else {
//			echo "start_x=$start_x, max_y=$max_y, line_end_x=$line_end_x, max_y=$max_y<br>";
			$pdf->Line($start_x, $max_y, $line_end_x, $max_y);
		}

		$pdf->SetFont($font,'',12);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

//		echo "1..pdf->y(".$pdf->y.") > max_y(".$max_y.")<br>";
		$pdf->MultiCellThaiCut($heading_width6[0], 7, convert2thaidigit($data_count).". ".convert2thaidigit($ARR_KPI_RESULT[$PG_ID]), $border, "L");
		if($pdf->y > $max_y) $max_y = $pdf->y;
		$pdf->x = $start_x + $heading_width6[0];
		$pdf->y = $start_y;
//		echo "2..pdf->y(".$pdf->y.") > max_y(".$max_y.")<br>";
		$pdf->Cell($heading_width6[1], 7, convert2thaidigit($ARR_KPI_WEIGHT[$PG_ID]), $border, 0, 'R', 0);
		for($i=1; $i<=5; $i++){ 
			if($i == $ARR_KPI_EVALUATE[$PG_ID]){
				$pdf->SetFont($fontb,'',12);
				$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
				$pdf->MultiCellThaiCut($heading_width6[2], 7, convert2thaidigit(${"ARR_KPI_TARGET".$i}[$PG_ID]), $border, 0, 'C', 0);
			}else{
				$pdf->SetFont($font,'',12);
				$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
				$pdf->Cell($heading_width6[2], 7, "", $border, 0, 'C', 0);
			} // end if			
		} // end for

		//================= Draw Border Line ====================
		$line_start_y = $start_y;		$line_start_x = $start_x;
		$line_end_y = $max_y;		$line_end_x = $start_x;
		$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
			
		for($i=0; $i<=6; $i++){
			if($i < 2){
				$line_start_y = $start_y;		$line_start_x += $heading_width6[$i];
				$line_end_y = $max_y;		$line_end_x += $heading_width6[$i];
			}else{
				$line_start_y = $start_y;		$line_start_x += $heading_width6[2];
				$line_end_y = $max_y;		$line_end_x += $heading_width6[2];
			} // end if
			
//			echo "$line_start_x, $line_start_y, $line_end_x, $line_end_y<br>";
			$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
		} // end for
		//====================================================

//		echo "2..$data_count >> pdf->h(".$pdf->h.") - y_multi(".$y_multi.") - max_y(".$max_y.") >> COUNT_KPI=$COUNT_KPI, ii=$ii<br>";

		$pdf->x = $start_x;			$pdf->y = $max_y;
	} // end foreach
	
	$line_start_y = $max_y;		$line_start_x = $start_x;
	$line_end_y = $max_y;		$line_end_x = $start_x+$heading_width6[0]+$heading_width6[1]+$heading_width6[2]+$heading_width6[2]+$heading_width6[2]+$heading_width6[2]+$heading_width6[2];
	$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
		
//	echo "1..$pdf->h - $pdf->y<br>";
	if(($pdf->h - $pdf->y) < 60) $pdf->AddPage();
	else $pdf->Cell(200, 5, "", 0, 1, 'C', 0);
	
	$pdf->SetFont($font,'',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell($heading_width6[0], 7, "คะแนนรวม ", $border, 0, 'R', 0);
	$pdf->SetFont($font,'',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
	$pdf->Cell($heading_width6[1], 7, convert2thaidigit($TOTAL_KPI_WEIGHT), 1, 0, 'R', 0);
	$pdf->SetFont($fontb,'',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
	$pdf->Cell(($heading_width6[2] * 5), 7, convert2thaidigit($TOTAL_KPI_EVALUATE), 1, 1, 'C', 0);

	$pdf->SetFont($font,'',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell($heading_width6[0], 7, "คะแนนประเมิน ", $border, 0, 'R', 0);
	$pdf->Cell($heading_width6[1], 7, "", $border, 0, 'R', 0);
	$pdf->SetFont($fontb,'',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
	$pdf->Cell(($heading_width6[2] * 5), 7, convert2thaidigit(number_format(($TOTAL_KPI_EVALUATE / $TOTAL_KPI_WEIGHT), 2)), 1, 1, 'C', 0);

//	echo "2..$pdf->h - $pdf->y<br>";
	/*if(($pdf->h - $pdf->y) < 50) $pdf->AddPage();																					
	else $pdf->Cell(200, 5, "", 0, 1, 'C', 0);*/

//	$pdf->AddPage();				
	
	$pdf->SetFont($font,'',14);
	$pdf->SetDrawColor(hexdec("00"),hexdec("66"),hexdec("CC"));
	$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(200, 7, "ผลรวมของคะแนนประเมินของผลสำเร็จของงาน", 0, 1, 'L', 1);
	$pdf->Cell(200, 5, "", 0, 1, 'C', 0);

	$pdf->Cell(70, 7, "ผลรวมของคะแนนประเมินผลสำเร็จของงานทั้งหมด", 0, 0, 'R', 0);
	$pdf->Cell(5, 7, "", 0, 0, 'C', 0);
	$pdf->SetFont($fontb,'',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
//	$pdf->Cell(20, 7, convert2thaidigit(number_format(($TOTAL_KPI_EVALUATE / $COUNT_KPI), 2)), 1, 0, 'C', 0);
	$pdf->Cell(20, 7, convert2thaidigit(number_format(($TOTAL_KPI_EVALUATE / $TOTAL_KPI_WEIGHT), 2)), 1, 0, 'C', 0);
	$pdf->Cell(5, 7, "", 0, 1, 'C', 0);

	$pdf->Close();
	$pdf->Output();

	ini_set("max_execution_time", 30);
?>