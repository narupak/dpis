<?
	header('Content-Type: text/html; charset=windows-874');
	
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

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
		$find_org_name = "ORG_ID=$DEPARTMENT_ID";
	}
	$cmd = " select $select_org_name from PER_ORG where $find_org_name";
	if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	$db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();
	$data = $db_dpis->get_array();
	$ORG_ENG_NAME = trim($data[$select_org_name]);

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
		$cmd = " select 	b.PL_NAME, c.ORG_NAME, a.PT_CODE 
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
//			$REVIEW_PL_NAME = $data[PM_NAME]." ($REVIEW_PL_NAME)"; 
			$REVIEW_PL_NAME = $data[PM_NAME]; 
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
//			$REVIEW_PL_NAME1 = $data[PM_NAME]." ($REVIEW_PL_NAME1)";
			$REVIEW_PL_NAME1 = $data[PM_NAME];
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
//			$REVIEW_PL_NAME2 = $data[PM_NAME]." ($REVIEW_PL_NAME2)";
			$REVIEW_PL_NAME2 = $data[PM_NAME];
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

	if($DPISDB=="odbc") 
		$cmd = " select 	a.PG_ID, a.PG_SEQ, a.KPI_NAME, c.PFR_NAME, a.KPI_WEIGHT, a.PG_RESULT, a.PG_EVALUATE,
							a.KPI_TARGET_LEVEL1, a.KPI_TARGET_LEVEL2, a.KPI_TARGET_LEVEL3, a.KPI_TARGET_LEVEL4, a.KPI_TARGET_LEVEL5,
							a.KPI_TARGET_LEVEL1_DESC, a.KPI_TARGET_LEVEL2_DESC, a.KPI_TARGET_LEVEL3_DESC, a.KPI_TARGET_LEVEL4_DESC, a.KPI_TARGET_LEVEL5_DESC
				   from		(
									PER_PERFORMANCE_GOALS a
									left join PER_KPI b on (a.KPI_ID=b.KPI_ID)
								) left join PER_PERFORMANCE_REVIEW c on (b.PFR_ID=c.PFR_ID)
				   where 	a.KF_ID=$KF_ID 
				   order by	a.PG_SEQ ";
	elseif($DPISDB=="oci8") 
		$cmd = " select 	a.PG_ID, a.PG_SEQ, a.KPI_NAME, c.PFR_NAME, a.KPI_WEIGHT, a.PG_RESULT, a.PG_EVALUATE,
							a.KPI_TARGET_LEVEL1, a.KPI_TARGET_LEVEL2, a.KPI_TARGET_LEVEL3, a.KPI_TARGET_LEVEL4, a.KPI_TARGET_LEVEL5,
							a.KPI_TARGET_LEVEL1_DESC, a.KPI_TARGET_LEVEL2_DESC, a.KPI_TARGET_LEVEL3_DESC, a.KPI_TARGET_LEVEL4_DESC, a.KPI_TARGET_LEVEL5_DESC
				   from 		PER_PERFORMANCE_GOALS a, PER_KPI b, PER_PERFORMANCE_REVIEW c
				   where 	a.KF_ID=$KF_ID and a.KPI_ID=b.KPI_ID(+) and b.PFR_ID=c.PFR_ID(+)
				   order by	a.PG_SEQ ";
	elseif($DPISDB=="mysql")
		$cmd = " select 	a.PG_ID, a.PG_SEQ, a.KPI_NAME, c.PFR_NAME, a.KPI_WEIGHT, a.PG_RESULT, a.PG_EVALUATE,
							a.KPI_TARGET_LEVEL1, a.KPI_TARGET_LEVEL2, a.KPI_TARGET_LEVEL3, a.KPI_TARGET_LEVEL4, a.KPI_TARGET_LEVEL5,
							a.KPI_TARGET_LEVEL1_DESC, a.KPI_TARGET_LEVEL2_DESC, a.KPI_TARGET_LEVEL3_DESC, a.KPI_TARGET_LEVEL4_DESC, a.KPI_TARGET_LEVEL5_DESC
				   from		(
									PER_PERFORMANCE_GOALS a
									left join PER_KPI b on (a.KPI_ID=b.KPI_ID)
								) left join PER_PERFORMANCE_REVIEW c on (b.PFR_ID=c.PFR_ID)
				   where 	a.KF_ID=$KF_ID 
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
		
	$cmd = " select 	CP_CODE from PER_KPI_COMPETENCE where KF_ID=$KF_ID ";
	$COUNT_COMPETENCE = $db_dpis->send_cmd($cmd);
	$FULL_SCORE = 5;

	$cmd = " select 	sum(PC_TARGET_LEVEL) as SUM_TARGET_LEVEL from PER_KPI_COMPETENCE where KF_ID=$KF_ID ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$SUM_TARGET_LEVEL =$data[SUM_TARGET_LEVEL];

//	$SHOW_SCORE_KPI = round(round(($SCORE_KPI / ($COUNT_KPI * 5)), 3), 2);
	$SHOW_SCORE_KPI = round(round(($SCORE_KPI / ($TOTAL_KPI_WEIGHT * 5)), 3), 2);
//	$SHOW_SCORE_COMPETENCE = round(round(($SCORE_COMPETENCE / ($COUNT_COMPETENCE * 3)), 3), 2);
//	$SHOW_SCORE_COMPETENCE = round(round(($SCORE_COMPETENCE / 5), 3), 2);
	if ($COMPETENCY_SCALE==1)
		$SHOW_SCORE_COMPETENCE = round(round(($SCORE_COMPETENCE / ($COUNT_COMPETENCE * 3)), 3), 2);
	elseif ($COMPETENCY_SCALE==2 || $COMPETENCY_SCALE==5 || $COMPETENCY_SCALE==6)
		$SHOW_SCORE_COMPETENCE = round(round(($SCORE_COMPETENCE / $FULL_SCORE), 3), 2);
	elseif ($COMPETENCY_SCALE==3)
		$SHOW_SCORE_COMPETENCE = round(round(($SCORE_COMPETENCE / $SUM_TARGET_LEVEL), 3), 2);
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

	// =============================== START GEN EXEC ========================//

	$company_name = "";
	$report_title = "";
	$report_code = "";

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();

	$fname= "../../Excel/tmp/rpt_kpi_formN_xls.xls";

	$workbook = &new writeexcel_workbook($fname);

	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.set_format.php";	
	// use set_format(set of parameter) funtion , help is in file
	require_once "../../Excel/my.defined_format.inc.format_param.php";	
	// define format parameter
	//====================== SET FORMAT ======================//
	
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
		global $worksheet, $xlsRow;
		global $heading_name;
		
		if($header_select == 2){
			$worksheet->set_column(0, 0, $heading_width2[0]);
			$worksheet->set_column(1, 1, $heading_width2[1]);
			$worksheet->set_column(2, 2, $heading_width2[2]);
			$worksheet->set_column(3, 3, $heading_width2[3]);
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "องค์ประกอบการประเมิน", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 1, "คะแนน (ก)", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 2, "น้ำหนัก (ข)", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 3, "รวมคะแนน (ก)x(ข)", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		}elseif($header_select == 3){
			$worksheet->set_column(0, 0, $heading_width3[0]);
			$worksheet->set_column(1, 1, $heading_width3[1]);
			$worksheet->set_column(2, 2, $heading_width3[2]);
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "ความรู้/ ทักษะ/ สมรรถนะ ที่ต้องได้รับการพัฒนา", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 1, "วิธีการพัฒนา", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 2, "ช่วงเวลาที่ต้องการการพัฒนา", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		}elseif($header_select == 4){
			$worksheet->set_column(0, 0, $heading_width4[0]);
			$worksheet->set_column(1, 1, $heading_width4[1]);
			$worksheet->set_column(2, 2, $heading_width4[2]);
			$worksheet->set_column(3, 2, $heading_width4[3]);
			$worksheet->set_column(4, 2, $heading_width4[4]);
			$worksheet->set_column(5, 2, $heading_width4[5]);
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "สมรรถนะ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 1, "ระดับที่", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 2, "ผลการประเมิน (ก)", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 3, "น้ำหนัก (ข)", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 4, "รวมคะแนน", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 5, "บันทึกโดยผู้ประเมิน (ถ้ามี)", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		}elseif($header_select == 5){	//แบบสรุปการประเมินผลสัมฤทธิ์ของงาน (PAGE 6)
			$worksheet->set_column(0, 0, $heading_width5[0]);
			$worksheet->set_column(1, 1, $heading_width5[1]);
			$worksheet->set_column(2, 2, $heading_width5[2]);
			$worksheet->set_column(3, 2, $heading_width5[3]);
			$worksheet->set_column(4, 2, $heading_width5[4]);
			for($i=1; $i<=5; $i++){ 
				$worksheet->set_column(2+$i, 2+$i, $heading_width5[1]);
			} // end for
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "ตัวชี้วัดผลงาน", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 1, "คะแนนตามระดับค่าเป้าหมาย", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 2, "คะแนน (ก)", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 3, "น้ำหนัก (ข)", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 4, "รวมคะแนน (กxข)", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
			for($i=1; $i<=5; $i++){ 
				if($i < 5)
					$worksheet->write($xlsRow, 4+$i, convert2thaidigit($i), set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
				else
					$worksheet->write($xlsRow, 4+$i, convert2thaidigit($i), set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
			} // end for
		}elseif($header_select == 6){
			$worksheet->set_column(0, 0, $heading_width5[0]);
			$worksheet->set_column(1, 1, $heading_width5[1]);
			$worksheet->set_column(2, 2, $heading_width5[2]);
			for($i=1; $i<=5; $i++){ 
				$worksheet->set_column(2+$i, 2+$i, $heading_width5[1]);
			} // end for
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "ผลงานจริง", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 1, "น้ำหนัก", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 2, "ผลการประเมิน", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
			for($i=1; $i<=5; $i++){ 
				if($i < 5)
					$worksheet->write($xlsRow, 2+$i, convert2thaidigit($i), set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
				else
					$worksheet->write($xlsRow, 2+$i, convert2thaidigit($i), set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
			} // end for
		}	// end if
	} // function print_header

	$sheet_no_text = "kpi_formN_sheet_1";

	$worksheet = &$workbook->addworksheet("$sheet_no_text");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);

	$xlsRow=1;
	$worksheet->insert_bitmap($xlsRow, 0, "../images/logo_ocsc.bmp", 0, 0, 1, 1);
	$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTitle", "B", "C", "", 1));

	$xlsRow++;
	$worksheet->write($xlsRow, 0, "$DEPARTMENT_NAME", set_format("xlsFmtTitle", "B", "C", "", 1));

	$xlsRow++;
	$worksheet->write($xlsRow, 0, "$ORG_ENG_NAME", set_format("xlsFmtTitle", "B", "C", "", 1));

	$xlsRow++;
	$worksheet->write($xlsRow, 0, "แบบสรุปการประเมินผลการปฏิบัติราชการ", set_format("xlsFmtTitle", "B", "C", "", 1));

	$xlsRow++;
	$worksheet->write($xlsRow, 0, "ส่วนที่ ๑:  ข้อมูลของผู้รับการประเมิน", set_format("xlsFmtTitle", "B", "C", "", 1));

	$xlsRow++;
	$worksheet->set_column(0, 0, 15);
	$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTitle", "B", "C", "", 0));
	$worksheet->set_column(1, 1, 30);
	$worksheet->write($xlsRow, 1, "รอบการประเมิน", set_format("xlsFmtTitle", "B", "L", "", 0));
	$worksheet->set_column(2, 2, 15);
	$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 0));
	$worksheet->set_column(3, 3, 25);
	$worksheet->write($xlsRow, 3, convert2thaidigit("รอบที่ ๑"), set_format("xlsFmtTitle", "B", "L", "", 0));
	$worksheet->set_column(4, 4, 25);
	$worksheet->write($xlsRow, 4, convert2thaidigit("๑ ตุลาคม "), set_format("xlsFmtTitle", "B", "L", "", 0));
	$worksheet->set_column(5, 5, 18);
	$worksheet->write($xlsRow, 5, convert2thaidigit(($KF_CYCLE==1)?($KF_YEAR - 1):""), set_format("xlsFmtTitle", "B", "L", "", 0));
	$worksheet->set_column(6, 6, 32);
	$worksheet->write($xlsRow, 6, convert2thaidigit("ถึง ๓๑ มีนาคม "), set_format("xlsFmtTitle", "B", "L", "", 0));
	$worksheet->set_column(7, 7, 32);
	$worksheet->write($xlsRow, 7, convert2thaidigit(($KF_CYCLE==1)?$KF_YEAR:""), set_format("xlsFmtTitle", "B", "L", "", 0));
	$worksheet->set_column(8, 8, 20);
	if($KF_CYCLE==1)
		$worksheet->insert_bitmap($xlsRow, 8, "../images/checkbox_check.bmp", 0, 0, 4, 4);
	else
		$worksheet->insert_bitmap($xlsRow, 8, "../images/checkbox_blank.bmp", 0, 0, 4, 4);
	$worksheet->set_column(9, 9, 10);
	$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTitle", "B", "L", "", 1));

	$xlsRow++;
	$worksheet->set_column(0, 0, 15);
	$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTitle", "B", "C", "", 0));
	$worksheet->set_column(1, 1, 30);
	$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "L", "", 0));
	$worksheet->set_column(2, 2, 15);
	$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 0));
	$worksheet->set_column(3, 3, 25);
	$worksheet->write($xlsRow, 3, convert2thaidigit("รอบที่ ๒"), set_format("xlsFmtTitle", "B", "L", "", 0));
	$worksheet->set_column(4, 4, 25);
	$worksheet->write($xlsRow, 4, convert2thaidigit("๑ เมษายน "), set_format("xlsFmtTitle", "B", "L", "", 0));
	$worksheet->set_column(5, 5, 18);
	$worksheet->write($xlsRow, 5, convert2thaidigit(($KF_CYCLE==2)?$KF_YEAR:""), set_format("xlsFmtTitle", "B", "L", "", 0));
	$worksheet->set_column(6, 6, 32);
	$worksheet->write($xlsRow, 6, convert2thaidigit("ถึง ๓๐ กันยายน "), set_format("xlsFmtTitle", "B", "L", "", 0));
	$worksheet->set_column(7, 7, 32);
	$worksheet->write($xlsRow, 7, convert2thaidigit(($KF_CYCLE==2)?$KF_YEAR:""), set_format("xlsFmtTitle", "B", "L", "", 0));
	$worksheet->set_column(8, 8, 20);
	if($KF_CYCLE==2)
		$worksheet->insert_bitmap($xlsRow, 8, "../images/checkbox_check.bmp", 0, 0, 4, 4);
	else
		$worksheet->insert_bitmap($xlsRow, 8, "../images/checkbox_blank.bmp", 0, 0, 4, 4);
	$worksheet->set_column(9, 9, 10);
	$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTitle", "B", "L", "", 1));

	$xlsRow++;
	$worksheet->set_column(0, 0, 15);
	$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTitle", "", "C", "", 0));
	$worksheet->set_column(1, 1, 40);
	$worksheet->write($xlsRow, 1, "ชื่อผู้รับการประเมิน  ", set_format("xlsFmtTitle", "", "L", "", 0));
	$worksheet->set_column(2, 2, 130);
	$worksheet->write($xlsRow, 2, "$PER_NAME  ", set_format("xlsFmtTitle", "", "L", "", 1));

	$xlsRow++;
	$worksheet->set_column(0, 0, 15);
	$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTitle", "", "C", "", 0));
	$worksheet->set_column(1, 1, 15);
	$worksheet->write($xlsRow, 1, "ตำแหน่ง  ", set_format("xlsFmtTitle", "", "L", "", 0));
	$worksheet->set_column(2, 2, 30);
	$worksheet->write($xlsRow, 2, convert2thaidigit("$PL_NAME  "), set_format("xlsFmtTitle", "", "L", "", 1));
	
	if ($PER_TYPE==1) {
		$xlsRow++;
		$worksheet->set_column(0, 0, 15);
		$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTitle", "", "C", "", 0));
		$worksheet->set_column(1, 1, 15);
		$worksheet->write($xlsRow, 1, "ประเภทตำแหน่ง  ", set_format("xlsFmtTitle", "", "L", "", 0));
		$worksheet->set_column(2, 2, 15);
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "", "C", "", 0));
		$worksheet->set_column(3, 3, 15);
		$worksheet->write($xlsRow, 3, convert2thaidigit("$POSITION_TYPE  "), set_format("xlsFmtTitle", "", "L", "", 0));
		$worksheet->set_column(4, 4, 40);
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "", "C", "", 0));
		$worksheet->set_column(5, 5, 15);
		$worksheet->write($xlsRow, 5, "ระดับตำแหน่ง  ", set_format("xlsFmtTitle", "", "L", "", 0));
		$worksheet->set_column(6, 6, 15);
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "", "C", "", 0));
		$worksheet->set_column(7, 7, 130);
		$worksheet->write($xlsRow, 7, convert2thaidigit("$POSITION_LEVEL  "), set_format("xlsFmtTitle", "", "L", "", 1));
	} elseif ($PER_TYPE==3) {
		$xlsRow++;
		$worksheet->set_column(0, 0, 40);
		$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTitle", "", "C", "", 0));
		$worksheet->set_column(1, 1, 40);
		$worksheet->write($xlsRow, 1, "กลุ่มงาน  ", set_format("xlsFmtTitle", "", "L", "", 0));
		$worksheet->set_column(2, 2, 130);
		$worksheet->write($xlsRow, 2, convert2thaidigit("$POSITION_LEVEL  "), set_format("xlsFmtTitle", "", "L", "", 1));
	}
	
	$xlsRow++;
	$worksheet->set_column(0, 0, 15);
	$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTitle", "", "C", "", 0));
	$worksheet->set_column(1, 1, 15);
	$worksheet->write($xlsRow, 1, "สังกัด  ", set_format("xlsFmtTitle", "", "L", "", 0));
	$worksheet->set_column(2, 2, 30);
	$worksheet->write($xlsRow, 2, convert2thaidigit("$ORG_NAME  "), set_format("xlsFmtTitle", "", "L", "", 1));

	$xlsRow++;
	$worksheet->set_column(0, 0, 15);
	$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTitle", "", "C", "", 0));
	$worksheet->set_column(1, 1, 15);
	$worksheet->write($xlsRow, 1, "เงินเดือน  ", set_format("xlsFmtTitle", "", "L", "", 0));
	$worksheet->set_column(2, 2, 30);
	$worksheet->write($xlsRow, 2, convert2thaidigit(number_format($PER_SALARY)), set_format("xlsFmtTitle", "", "L", "", 0));
	$worksheet->set_column(3, 3, 15);
	$worksheet->write($xlsRow, 3, "บาท", set_format("xlsFmtTitle", "", "L", "", 1));

	$xlsRow++;
	$worksheet->set_column(0, 0, 15);
	$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTitle", "", "C", "", 0));
	$worksheet->set_column(1, 1, 15);
	$worksheet->write($xlsRow, 1, "ชื่อผู้ประเมิน  ", set_format("xlsFmtTitle", "", "L", "", 0));
	$worksheet->set_column(2, 2, 130);
	$worksheet->write($xlsRow, 2, $REVIEW_PER_NAME, set_format("xlsFmtTitle", "", "L", "", 1));

	$xlsRow++;
	$worksheet->set_column(0, 0, 15);
	$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTitle", "", "C", "", 0));
	$worksheet->set_column(1, 1, 15);
	$worksheet->write($xlsRow, 1, "ตำแหน่ง  ", set_format("xlsFmtTitle", "", "L", "", 0));
	$worksheet->set_column(2, 2, 130);
	$worksheet->write($xlsRow, 2, convert2thaidigit("$REVIEW_PL_NAME"), set_format("xlsFmtTitle", "", "L", "", 1));
	
	$xlsRow++;
	$worksheet->set_column(0, 0, 15);
	$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTitle", "", "C", "", 0));
	$worksheet->set_column(1, 1, 15);
	$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "", "L", "", 0));
	$worksheet->set_column(2, 2, 180);
	$worksheet->write($xlsRow, 2, "คำชี้แจง", set_format("xlsFmtTitle", "", "L", "", 1));

	$xlsRow++;
	$worksheet->set_column(0, 0, 17);
	$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTitle", "", "C", "", 0));
	$worksheet->set_column(1, 1, 170);
	$worksheet->write($xlsRow, 1, " แบบสรุปการประเมินผลการปฏิบัติราชการนี้ มีด้วยกัน ๓ หน้า ประกอบด้วย", set_format("xlsFmtTitle", "", "L", "", 1));

	$xlsRow++;
	$worksheet->set_column(0, 0, 17);
	$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTitle", "", "C", "", 0));
	$worksheet->set_column(1, 1, 17);
	$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "", "L", "", 0));
	$worksheet->set_column(2, 2, 180);
	$worksheet->write($xlsRow, 2, "คำชี้แจง", set_format("xlsFmtTitle", "", "L", "", 1));

	$xlsRow++;
	$worksheet->set_column(0, 0, 17);
	$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTitle", "", "C", "", 0));
	$worksheet->set_column(1, 1, 17);
	$worksheet->write($xlsRow, 1, "ส่วนที่ ๑: ", set_format("xlsFmtTitle", "", "L", "", 0));
	$worksheet->set_column(2, 2, 40);
	$worksheet->write($xlsRow, 2, "ข้อมูลของผู้รับการประเมิน", set_format("xlsFmtTitle", "", "L", "", 0));
	$worksheet->set_column(3, 3, 40);
	$worksheet->write($xlsRow, 3, "เพื่อระบุรายละเอียดต่างๆ ที่เกี่ยวข้องกับตัวผู้รับการประเมิน", set_format("xlsFmtTitle", "", "L", "", 1));

	$xlsRow++;
	$worksheet->set_column(0, 0, 17);
	$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTitle", "", "C", "", 0));
	$worksheet->set_column(1, 1, 17);
	$worksheet->write($xlsRow, 1, "ส่วนที่ ๒: ", set_format("xlsFmtTitle", "", "L", "", 0));
	$worksheet->set_column(2, 2, 40);
	$worksheet->write($xlsRow, 2, "สรุปผลการประเมิน", set_format("xlsFmtTitle", "", "L", "", 0));
	$worksheet->set_column(3, 3, 40);
	$worksheet->write($xlsRow, 3, "ใช้เพื่อกรอกค่าคะแนนการประเมินในองค์ประกอบด้านผลสัมฤทธิ์ของงาน องค์ประกอบ", set_format("xlsFmtTitle", "", "L", "", 1));

	$xlsRow++;
	$worksheet->set_column(0, 0, 17);
	$worksheet->write($xlsRow, 0, "ด้านพฤติกรรมการปฏิบัติราชการ และน้ำหนักของทั้งสององค์ประกอบ ในแบบส่วนสรุปส่วนที่ ๒ นี้", set_format("xlsFmtTitle", "", "L", "", 1));

	$xlsRow++;
	$worksheet->set_column(0, 0, 17);
	$worksheet->write($xlsRow, 0, "ยังใช้สำหรับคำนวณคะแนนผลการปฏิบัติราชการรวมด้วย", set_format("xlsFmtTitle", "", "L", "", 0));

	$xlsRow++;
	$worksheet->set_column(0, 0, 17);
	$worksheet->write($xlsRow, 0, "- สำหรับคะแนนองค์ประกอบด้านผลสัมฤทธิ์ของงาน ให้นำมาจากแบบประเมินผลสัมฤทธิ์ของงาน โดยให้แนบท้ายแบบสรุปฉบับนี้", set_format("xlsFmtTitle", "", "L", "", 1));

	$xlsRow++;
	$worksheet->set_column(0, 0, 17);
	$worksheet->write($xlsRow, 0, "- สำหรับคะแนนองค์ประกอบด้านพฤติกรรมการปฏิบัติราชการ ให้นำมาจากแบบประเมินสมรรถนะ โดยให้แนบท้ายแบบสรุปฉบับนี้", set_format("xlsFmtTitle", "", "L", "", 1));

	$xlsRow++;
	$worksheet->set_column(0, 0, 17);
	$worksheet->write($xlsRow, 0, "- สำหรับคะแนนองค์ประกอบด้านพฤติกรรมการปฏิบัติราชการ ให้นำมาจากแบบประเมินสมรรถนะ โดยให้แนบท้ายแบบสรุปฉบับนี้", set_format("xlsFmtTitle", "", "L", "", 1));

	$xlsRow++;
	$worksheet->set_column(0, 0, 17);
	$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTitle", "", "C", "", 0));
	$worksheet->set_column(1, 1, 17);
	$worksheet->write($xlsRow, 1, "ส่วนที่ ๓: ", set_format("xlsFmtTitle", "", "L", "", 0));
	$worksheet->set_column(2, 2, 40);
	$worksheet->write($xlsRow, 2, "แผนพัฒนาการปฏิบัติราชการรายบุคคล", set_format("xlsFmtTitle", "", "L", "", 0));
	$worksheet->set_column(3, 3, 40);
	$worksheet->write($xlsRow, 3, "ผู้ประเมิน และผู้รับการประเมินร่วมกันจัดทำแผนพัฒนาผลการปฏิบัติราชการ", set_format("xlsFmtTitle", "", "L", "", 1));

	$xlsRow++;
	$worksheet->set_column(0, 0, 17);
	$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTitle", "", "C", "", 0));
	$worksheet->set_column(1, 1, 17);
	$worksheet->write($xlsRow, 1, "ส่วนที่ ๔: ", set_format("xlsFmtTitle", "", "L", "", 0));
	$worksheet->set_column(2, 2, 40);
	$worksheet->write($xlsRow, 2, "การรับทราบผลการประเมิน", set_format("xlsFmtTitle", "", "L", "", 0));
	$worksheet->set_column(3, 3, 40);
	$worksheet->write($xlsRow, 3, "ผู้รับการประเมินลงนามรับทราบผลการประเมิน", set_format("xlsFmtTitle", "", "L", "", 1));

	$xlsRow++;
	$worksheet->set_column(0, 0, 17);
	$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTitle", "", "C", "", 0));
	$worksheet->set_column(1, 1, 17);
	$worksheet->write($xlsRow, 1, "ส่วนที่ ๕: ", set_format("xlsFmtTitle", "", "L", "", 0));
	$worksheet->set_column(2, 2, 40);
	$worksheet->write($xlsRow, 2, "ความเห็นของผู้บังคับบัญชาเหนือขึ้นไป", set_format("xlsFmtTitle", "", "L", "", 0));
	$worksheet->set_column(3, 3, 40);
	$worksheet->write($xlsRow, 3, "ผู้บังคับบัญชาเหนือขึ้นไปกลั่นกรองผลการประเมิน แผนพัฒนาผลการปฏิบัติ", set_format("xlsFmtTitle", "", "L", "", 0));
	$worksheet->set_column(4, 4, 17);
	$worksheet->write($xlsRow, 4, " ราชการและให้ความเห็น", set_format("xlsFmtTitle", "", "L", "", 1));
	
	$xlsRow++;
	$worksheet->set_column(0, 0, 17);
	$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTitle", "", "C", "", 0));
	$worksheet->set_column(1, 1, 170);
	$worksheet->write($xlsRow, 1, "คำว่า 'ผู้บังคับบัญชาเหนือขึ้นไป' สำหรับผู้ประเมินตามข้อ ๒ (๙) หมายถึงหัวหน้าส่วนราชการประจำจังหวัดผู้บังคับบัญชาของผู้รับการประเมิน", set_format("xlsFmtTitle", "", "L", "", 1));

	// ======================= PAGE 2 =====================//	
	$sheet_no_text = "kpi_formN_sheet_2";
			
	$worksheet = &$workbook->addworksheet("$sheet_no_text");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);

	$xlsRow=1;
	$worksheet->set_column(0, 0, 200);
	$worksheet->write($xlsRow, 0, "ส่วนที่ ๒: การสรุปผลการประเมิน", set_format("xlsFmtTitle", "B", "C", "", 1));

	print_header(2);

	$xlsRow++;
	$worksheet->write($xlsRow, 0, convert2thaidigit("องค์ประกอบที่ ๑: ผลสัมฤทธิ์ของงาน"), set_format("xlsFmtTitle", "LTBR", "L", "", 0));
	$worksheet->write($xlsRow, 1, convert2thaidigit(number_format($SHOW_SCORE_KPI, 2)), set_format("xlsFmtTitle", "LTBR", "C", "", 0));
	if ($PERFORMANCE_WEIGHT)
		$worksheet->write($xlsRow, 2, convert2thaidigit($PERFORMANCE_WEIGHT . "%"), set_format("xlsFmtTitle", "LTBR", "C", "", 0));
	else
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "LTBR", "C", "", 0));
	$worksheet->write($xlsRow, 3, convert2thaidigit(number_format($SUM_KPI, 2)), set_format("xlsFmtTitle", "LTBR", "C", "", 1));

	//แถวที่ 2
	$xlsRow++;
	$worksheet->write($xlsRow, 0, convert2thaidigit("องค์ประกอบที่ ๒: พฤติกรรมการปฏิบัติราชการ (สมรรถนะ)"), set_format("xlsFmtTitle", "LTBR", "L", "", 0));
	$worksheet->write($xlsRow, 1, convert2thaidigit(number_format($SHOW_SCORE_COMPETENCE, 2)), set_format("xlsFmtTitle", "LTBR", "C", "", 0));
	if ($COMPETENCE_WEIGHT)
		$worksheet->write($xlsRow, 2, convert2thaidigit($COMPETENCE_WEIGHT . "%"), set_format("xlsFmtTitle", "LTBR", "C", "", 0));
	else
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "LTBR", "C", "", 0));
	$worksheet->write($xlsRow, 3, convert2thaidigit(number_format($SUM_COMPETENCE, 2)), set_format("xlsFmtTitle", "LTBR", "C", "", 1));
	
	//แถวที่ 2
	$xlsRow++;
	$worksheet->write($xlsRow, 0, convert2thaidigit("องค์ประกอบอื่นๆ (ถ้ามี)"), set_format("xlsFmtTitle", "LTBR", "L", "", 0));
	$worksheet->write($xlsRow, 1, convert2thaidigit(number_format($SHOW_SCORE_OTHER, 2)), set_format("xlsFmtTitle", "LTBR", "C", "", 0));
	if ($OTHER_WEIGHT)
		$worksheet->write($xlsRow, 2, convert2thaidigit($OTHER_WEIGHT . "%"), set_format("xlsFmtTitle", "LTBR", "C", "", 0));
	else
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "LTBR", "C", "", 0));
	$worksheet->write($xlsRow, 3, convert2thaidigit(number_format($SUM_OTHER, 2)), set_format("xlsFmtTitle", "LTBR", "C", "", 1));

	//แถวที่ 3
	$xlsRow++;
	$worksheet->write($xlsRow, 0, "รวม", set_format("xlsFmtTitle", "LTBR", "L", "", 0));
//	$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "LTBR", "C", "", 0));
	$worksheet->write($xlsRow, 2, convert2thaidigit("๑๐๐%"), set_format("xlsFmtTitle", "LTBR", "C", "", 0));
	$worksheet->write($xlsRow, 3, convert2thaidigit(number_format($SUM_TOTAL, 2)), set_format("xlsFmtTitle", "LTBR", "C", "", 1));

	$xlsRow++;
	$worksheet->write($xlsRow, 0, "ระดับผลการประเมิน", set_format("xlsFmtTitle", "", "C", "", 0));
//	$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "", "L", "", 1));

	//หาระดับผลการประเมินหลัก 
	$cmd = "	select		AM_CODE, AM_NAME, AM_POINT_MIN, AM_POINT_MAX, AM_ACTIVE
								from		PER_ASSESS_MAIN where AM_YEAR = '$KF_YEAR' and AM_CYCLE = $KF_CYCLE and PER_TYPE = $PER_TYPE
					order by AM_POINT_MAX desc, AM_CODE desc ";
	$db_dpis->send_cmd($cmd);
	while($data = $db_dpis->get_array()){
		$AM_NAME = $data[AM_NAME];
		$AM_POINT_MIN = $data[AM_POINT_MIN];
		$AM_POINT_MAX = $data[AM_POINT_MAX];

		$show_checkbox = "../images/checkbox_blank.bmp";

		$xlsRow++;
		$worksheet->insert_bitmap($xlsRow, 0, $show_checkbox, 0, 0, 4, 4);
		$worksheet->write($xlsRow, 0, "$AM_NAME", set_format("xlsFmtTitle", "", "L", "", 1));
	} //end while
	
	//--------------------------------------------------
	$xlsRow++;
	$worksheet->write($xlsRow, 0, "ส่วนที่ ๓: แผนพัฒนาการปฏิบัติราชการรายบุคคล", set_format("xlsFmtTitle", "LTBR", "L", "", 1));

	print_header(3);

	$data_count = 0;
	foreach($ARR_IPIP as $IPIP_ID => $DEVELOP_COMPETENCE){
		$data_count++;

		$xlsRow++;
		$worksheet->write($xlsRow, 0, convert2thaidigit($DEVELOP_COMPETENCE), set_format("xlsFmtTitle", "", "L", "", 0));
		$worksheet->write($xlsRow, 1, convert2thaidigit($ARR_IPIP_METHOD[$IPIP_ID]), set_format("xlsFmtTitle", "", "L", "", 0));
		$worksheet->write($xlsRow, 2, convert2thaidigit($ARR_IPIP_INTERVAL[$IPIP_ID]), set_format("xlsFmtTitle", "", "L", "", 0));
		$worksheet->write($xlsRow, 3, convert2thaidigit($ARR_IPIP_INTERVAL[$IPIP_ID]), set_format("xlsFmtTitle", "", "L", "", 1));
	} // end foreach

	// ======================= PAGE 3 =====================//	
	$sheet_no_text = "kpi_formN_sheet_3";
			
	$worksheet = &$workbook->addworksheet($sheet_no_text);
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);

	$xlsRow=1;
	$worksheet->set_column(0, 0, 200);
	$worksheet->write($xlsRow, 0, "ส่วนที่ ๔: การรับทราบผลการประเมิน", set_format("xlsFmtTitle", "B", "C", "", 1));

	$xlsRow++;
	$worksheet->write($xlsRow, 0, "ผู้รับการประเมิน:", set_format("xlsFmtTitle", "", "L", "", 0));
	if($PER_NAME)
		$worksheet->insert_bitmap($xlsRow, 1, "../images/checkbox_check.bmp", 0, 0, 4, 4);
	else 
		$worksheet->insert_bitmap($xlsRow, 1, "../images/checkbox_blank.bmp", 0, 0, 4, 4);
	$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "", "L", "", 0));

	$worksheet->write($xlsRow, 0, "ได้รับทราบผลการประเมินและแผนพัฒนาผลการปฏิบัติราชการรายบุคคลแล้ว", set_format("xlsFmtTitle", "", "L", "", 1));
	
	$PIC_SIGN = getPIC_SIGN($PER_CARDNO);
	//echo $PIC_SIGN;      //function
	if(trim($PIC_SIGN)){
		if(file_exists($PIC_SIGN)){
			$worksheet->insert_bitmap($xlsRow, 0, $PIC_SIGN, 0, 0, 10, 10);
		}
	}else{ 
		$worksheet->write($xlsRow, 0, ("ลงชื่อ ". str_repeat(".", 100)), set_format("xlsFmtTitle", "R", "L", "", 1));
	}
	$worksheet->write($xlsRow, 0, ("( $PER_NAME )"), set_format("xlsFmtTitle", "R", "C", "", 1));
	$worksheet->write($xlsRow, 0, ("ตำแหน่ง ".convert2thaidigit("$PL_NAME$POSITION_LEVEL")), set_format("xlsFmtTitle", "R", "C", "", 1));
	$worksheet->write($xlsRow, 0, ("วันที่ ". str_repeat(".", 13) ." เดือน ". str_repeat(".", 30) ." พ.ศ. ". str_repeat(".", 20)), set_format("xlsFmtTitle", "R", "C", "", 1));
	$worksheet->write($xlsRow, 0, "ผู้ประเมิน:", set_format("xlsFmtTitle", "R", "C", "", 1));
	$worksheet->insert_bitmap($xlsRow, 0, "../images/checkbox_blank.bmp", 0, 0, 4, 4);
	$worksheet->write($xlsRow, 0, "ได้แจ้งผลการประเมินและผู้รับการประเมินได้ลงนามรับทราบ", set_format("xlsFmtTitle", "R", "L", "", 1));
	$worksheet->insert_bitmap($xlsRow, 0, "../images/checkbox_blank.bmp", 0, 0, 4, 4);
	$worksheet->write($xlsRow, 0, "ได้แจ้งผลการประเมิน เมื่อวันที่ ". str_repeat(".", 50), set_format("xlsFmtTitle", "R", "L", "", 1));
	$worksheet->write($xlsRow, 0, "แต่ผู้รับการประเมินไม่ลงนามรับทราบ", set_format("xlsFmtTitle", "R", "L", "", 1));
	$worksheet->write($xlsRow, 0, "โดยมี ". str_repeat(".", 75)."เป็นพยาน", set_format("xlsFmtTitle", "R", "L", "", 1));
	//ลงชื่อพยาน
	$worksheet->write($xlsRow, 0, ("ลงชื่อ ". str_repeat(".", 80)."พยาน"), set_format("xlsFmtTitle", "R", "L", "", 1));
	$worksheet->write($xlsRow, 0, ("ตำแหน่ง". str_repeat(".", 77)), set_format("xlsFmtTitle", "R", "L", "", 1));
	$worksheet->write($xlsRow, 0, ("วันที่ ". str_repeat(".", 13) ." เดือน ". str_repeat(".", 30) ." พ.ศ. ". str_repeat(".", 20)), set_format("xlsFmtTitle", "R", "L", "", 1));
	//ลงชื่อผู้ประเมิน
	$worksheet->write($xlsRow, 0, ("ลงชื่อ ". str_repeat(".", 100)), set_format("xlsFmtTitle", "R", "C", "", 1));
	$worksheet->write($xlsRow, 0, ("( $REVIEW_PER_NAME )"), set_format("xlsFmtTitle", "R", "C", "", 1));
	$worksheet->write($xlsRow, 0, ("ตำแหน่ง $REVIEW_PL_NAME "), set_format("xlsFmtTitle", "R", "C", "", 1));
	$worksheet->write($xlsRow, 0, ("วันที่ ". str_repeat(".", 13) ." เดือน ". str_repeat(".", 30) ." พ.ศ. ". str_repeat(".", 20)), set_format("xlsFmtTitle", "BR", "C", "", 1));
		
	// ======================= PAGE 4 =====================//	
	$sheet_no_text = "kpi_formN_sheet_4";
			
	$worksheet = &$workbook->addworksheet($sheet_no_text);
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);

	$xlsRow=1;
	$worksheet->set_column(0, 0, 200);
	$worksheet->write($xlsRow, 0, "ส่วนที่ ๕: ความเห็นของผู้บังคับบัญชาเหนือขึ้นไป", set_format("xlsFmtTitle", "B", "C", "", 1));

	$xlsRow++;
	$worksheet->write($xlsRow, 0, "ผู้บังคับบัญชาเหนือขึ้นไป:", set_format("xlsFmtTitle", "", "L", "", 1));
	
	$xlsRow++;
	if(trim($AGREE_REVIEW1))
		$worksheet->insert_bitmap($xlsRow, 0, "../images/checkbox_check.bmp", 0, 0, 4, 4);
	else 
		$worksheet->insert_bitmap($xlsRow, 0, "../images/checkbox_blank.bmp", 0, 0, 4, 4);
	$worksheet->write($xlsRow, 0, "เห็นด้วยกับผลการประเมิน", set_format("xlsFmtTitle", "", "L", "", 0));
	$worksheet->write($xlsRow, 1, convert2thaidigit($AGREE_REVIEW1), set_format("xlsFmtTitle", "", "L", "", 1));

	$xlsRow++;
	if(trim($DIFF_REVIEW1))
		$worksheet->insert_bitmap($xlsRow, 0, "../images/checkbox_check.bmp", 0, 0, 4, 4);
	else 
		$worksheet->insert_bitmap($xlsRow, 0, "../images/checkbox_blank.bmp", 0, 0, 4, 4);
	$worksheet->write($xlsRow, 0, "มีความเห็นแตกต่าง ดังนี้", set_format("xlsFmtTitle", "", "L", "", 0));
	$worksheet->write($xlsRow, 1, convert2thaidigit($DIFF_REVIEW1), set_format("xlsFmtTitle", "", "L", "", 1));

	$xlsRow++;
	$worksheet->write($xlsRow, 0, "ลงชื่อ ", set_format("xlsFmtTitle", "R", "C", "", 1));

	$xlsRow++;
	$worksheet->write($xlsRow, 0, ("( $REVIEW_PER_NAME1 )"), set_format("xlsFmtTitle", "R", "C", "", 1));

	$xlsRow++;
	$worksheet->write($xlsRow, 0, ("ตำแหน่ง $REVIEW_PL_NAME1"), set_format("xlsFmtTitle", "R", "C", "", 1));

	$xlsRow++;
	$worksheet->write($xlsRow, 0, ("วันที่ ". str_repeat(".", 13) ." เดือน ". str_repeat(".", 30) ." พ.ศ. ". str_repeat(".", 20)), set_format("xlsFmtTitle", "BR", "C", "", 1));

	$xlsRow++;
	$worksheet->write($xlsRow, 0, "ผู้บังคับบัญชาเหนือขึ้นไปอีกชั้นหนึ่ง (ถ้ามี):", set_format("xlsFmtTitle", "TLBR", "L", "", 1));

	$xlsRow++;
	if(trim($AGREE_REVIEW2))
		$worksheet->insert_bitmap($xlsRow, 0, "../images/checkbox_check.bmp", 0, 0, 4, 4);
	else 
		$worksheet->insert_bitmap($xlsRow, 0, "../images/checkbox_blank.bmp", 0, 0, 4, 4);
	$worksheet->write($xlsRow, 0, "เห็นด้วยกับผลการประเมิน", set_format("xlsFmtTitle", "", "L", "", 0));
	$worksheet->write($xlsRow, 1, convert2thaidigit($AGREE_REVIEW2), set_format("xlsFmtTitle", "", "L", "", 1));

	$xlsRow++;
	if(trim($DIFF_REVIEW2))
		$worksheet->insert_bitmap($xlsRow, 0, "../images/checkbox_check.bmp", 0, 0, 4, 4);
	else 
		$worksheet->insert_bitmap($xlsRow, 0, "../images/checkbox_blank.bmp", 0, 0, 4, 4);
	$worksheet->write($xlsRow, 0, "มีความเห็นแตกต่าง ดังนี้", set_format("xlsFmtTitle", "", "L", "", 0));
	$worksheet->write($xlsRow, 1, convert2thaidigit($DIFF_REVIEW2), set_format("xlsFmtTitle", "", "L", "", 1));

	$xlsRow++;
	$worksheet->write($xlsRow, 0, ("ลงชื่อ ". str_repeat(".", 100)), set_format("xlsFmtTitle", "", "R", "", 1));

	$xlsRow++;
	$worksheet->write($xlsRow, 0, ("( $REVIEW_PER_NAME2 )"), set_format("xlsFmtTitle", "", "R", "", 1));

	$xlsRow++;
	$worksheet->write($xlsRow, 0, ("ตำแหน่ง $REVIEW_PL_NAME2"), set_format("xlsFmtTitle", "", "R", "", 1));
	$worksheet->write($xlsRow, 0, ("วันที่ ". str_repeat(".", 13) ." เดือน ". str_repeat(".", 30) ." พ.ศ. ". str_repeat(".", 20)), set_format("xlsFmtTitle", "BR", "R", "", 1));

	// ======================= PAGE 5 =====================//	
	$sheet_no_text = "kpi_formN_sheet_5";
			
	$worksheet = &$workbook->addworksheet($sheet_no_text);
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);

	$xlsRow=1;
	$worksheet->set_column(0, 0, 200);
	$worksheet->write($xlsRow, 0, "แบบสรุปการประเมินสมรรถนะ", set_format("xlsFmtTitle", "B", "L", "", 1));

	$xlsRow++;
	$worksheet->write($xlsRow, 0, "รอบการประเมิน", set_format("xlsFmtTitle", "", "L", "", 0));
	$worksheet->write($xlsRow, 0, convert2thaidigit("รอบที่ ๑"), set_format("xlsFmtTitle", "BR", "R", "", 1));
	if($KF_CYCLE==1)
		$worksheet->insert_bitmap($xlsRow, 0, "../images/checkbox_check.bmp", 0, 0, 4, 4);
	else 
		$worksheet->insert_bitmap($xlsRow, 0, "../images/checkbox_blank.bmp", 0, 0, 4, 4);

	$xlsRow++;
	$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTitle", "", "L", "", 0));
	$worksheet->write($xlsRow, 0, convert2thaidigit("รอบที่ ๒"), set_format("xlsFmtTitle", "BR", "R", "", 1));
	if($KF_CYCLE==2)
		$worksheet->insert_bitmap($xlsRow, 0, "../images/checkbox_check.bmp", 0, 0, 4, 4);
	else 
		$worksheet->insert_bitmap($xlsRow, 0, "../images/checkbox_blank.bmp", 0, 0, 4, 4);

	$xlsRow++;
	$worksheet->write($xlsRow, 0, "ชื่อผู้รับการประเมิน: $PER_NAME", set_format("xlsFmtTitle", "", "L", "", 0));
	$worksheet->write($xlsRow, 0, ("ลงนาม ". str_repeat(".", 100)), set_format("xlsFmtTitle", "", "", "", 1));

	$xlsRow++;
	$worksheet->write($xlsRow, 0, "ชื่อผู้บังคับบัญชา/ผู้ประเมิน:".trim($REVIEW_PER_NAME), set_format("xlsFmtTitle", "", "L", "", 0));
	$worksheet->write($xlsRow, 0, ("ลงนาม ". str_repeat(".", 100)), set_format("xlsFmtTitle", "", "L", "", 1));

	print_header(4);
	
	$data_count = 0;
	foreach($ARR_COMPETENCE as $KC_ID => $CP_NAME){
		$data_count++;
		$border = "";

		$xlsRow++;
		$worksheet->write($xlsRow, 0, convert2thaidigit($CP_NAME), set_format("xlsFmtTitle", "", "L", "", 0));
		$worksheet->write($xlsRow, 1, convert2thaidigit($ARR_COMPETENCE_TARGET[$KC_ID]), set_format("xlsFmtTitle", "", "L", "", 0));
		$worksheet->write($xlsRow, 2, convert2thaidigit($ARR_COMPETENCE_EVALUATE[$KC_ID]), set_format("xlsFmtTitle", "", "L", "", 0));
		if ($ARR_COMPETENCE_WEIGHT[$KC_ID])
			$worksheet->write($xlsRow, 3, convert2thaidigit($ARR_COMPETENCE_WEIGHT[$KC_ID] . "%"), set_format("xlsFmtTitle", $border, "L", "", 0));
		else
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", $border, "L", "", 0));
		$worksheet->write($xlsRow, 4, convert2thaidigit($PC_SCORE[$KC_ID]), set_format("xlsFmtTitle", "", "L", "", 1));
	} // end foreach
	
	$xlsRow++;
	$worksheet->write($xlsRow, 0, "คะแนนรวม ", set_format("xlsFmtTitle", "", "L", "", 0));
	$worksheet->write($xlsRow, 2, convert2thaidigit(array_sum($ARR_COMPETENCE_EVALUATE)), set_format("xlsFmtTitle", "", "", "", 1));
	if (array_sum($ARR_COMPETENCE_WEIGHT))
		$worksheet->write($xlsRow, 3, convert2thaidigit(array_sum($ARR_COMPETENCE_WEIGHT) . "%"), set_format("xlsFmtTitle", "", "C", "", 1));
	else
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "", "C", "", 1));
	$worksheet->write($xlsRow, 4, convert2thaidigit($TOTAL_PC_SCORE), set_format("xlsFmtTitle", "", "L", "", 1));

	$xlsRow++;
	$worksheet->write($xlsRow, 0, "คะแนนประเมิน ", set_format("xlsFmtTitle", "", "R", "", 0));
	$worksheet->write($xlsRow, 2, convert2thaidigit($TOTAL_PC_SCORE), set_format("xlsFmtTitle", "", "C", "", 1));

	// ======================= PAGE 6 =====================//	
	$sheet_no_text = "kpi_formN_sheet_6";
			
	$worksheet = &$workbook->addworksheet($sheet_no_text);
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);

	$xlsRow=1;
	$worksheet->set_column(0, 0, 200);
	$worksheet->write($xlsRow, 0, "แบบสรุปการประเมินผลสัมฤทธิ์ของงาน", set_format("xlsFmtTitle", "B", "L", "", 1));

	$xlsRow++;
	$worksheet->write($xlsRow, 0, "รอบการประเมิน", set_format("xlsFmtTitle", "", "L", "", 0));
	$worksheet->write($xlsRow, 0, convert2thaidigit("รอบที่ ๑"), set_format("xlsFmtTitle", "BR", "R", "", 1));
	if($KF_CYCLE==1)
		$worksheet->insert_bitmap($xlsRow, 0, "../images/checkbox_check.bmp", 0, 0, 4, 4);
	else 
		$worksheet->insert_bitmap($xlsRow, 0, "../images/checkbox_blank.bmp", 0, 0, 4, 4);

	$xlsRow++;
	$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTitle", "", "L", "", 0));
	$worksheet->write($xlsRow, 0, convert2thaidigit("รอบที่ ๒"), set_format("xlsFmtTitle", "BR", "R", "", 1));
	if($KF_CYCLE==2)
		$worksheet->insert_bitmap($xlsRow, 0, "../images/checkbox_check.bmp", 0, 0, 4, 4);
	else 
		$worksheet->insert_bitmap($xlsRow, 0, "../images/checkbox_blank.bmp", 0, 0, 4, 4);

	$xlsRow++;
	$worksheet->write($xlsRow, 0, "ชื่อผู้รับการประเมิน: $PER_NAME", set_format("xlsFmtTitle", "", "L", "", 0));
	$worksheet->write($xlsRow, 0, ("ลงนาม ". str_repeat(".", 100)), set_format("xlsFmtTitle", "", "", "", 1));

	$xlsRow++;
	$worksheet->write($xlsRow, 0, "ชื่อผู้บังคับบัญชา/ผู้ประเมิน:".trim($REVIEW_PER_NAME), set_format("xlsFmtTitle", "", "L", "", 0));
	$worksheet->write($xlsRow, 0, ("ลงนาม ". str_repeat(".", 100)), set_format("xlsFmtTitle", "", "L", "", 1));

	print_header(5);
	
	$data_count = 0;
	foreach($ARR_KPI as $PG_ID => $KPI_NAME){
		$data_count++;
		$border = "";

		$xlsRow++;
		$worksheet->write($xlsRow, 0, convert2thaidigit($KPI_NAME), set_format("xlsFmtTitle", "", "L", "", 0));
		for($i=1; $i<=5; $i++) { 
			$worksheet->write($xlsRow, $i, convert2thaidigit(${"ARR_KPI_TARGET".$i}[$PG_ID]), set_format("xlsFmtTitle", "", "L", "", 0));
		}

		for($i=1; $i<=5; $i++){ 
			if($i == $ARR_KPI_EVALUATE[$PG_ID]){
					$SCORE[$PG_ID]=convert2thaidigit(${"ARR_KPI_TARGET".$i}[$PG_ID]);
					$SUMSCORE[$PG_ID]=convert2thaidigit(${"ARR_KPI_TARGET".$i}[$PG_ID]*$ARR_KPI_WEIGHT[$PG_ID]);
					$TOTAL_KPI_RESULT+=${"ARR_KPI_TARGET".$i}[$PG_ID];
//					echo "SCORE[$PG_ID]:".$SCORE[$PG_ID]."<br>";
			}	
		} // end for

		$xlsRow++;
		$worksheet->write($xlsRow, 0, $SCORE[$PG_ID], set_format("xlsFmtTitle", "", "L", "", 0));
		$worksheet->write($xlsRow, 1, convert2thaidigit($ARR_KPI_WEIGHT[$PG_ID]), set_format("xlsFmtTitle", "", "L", "", 0));
		$worksheet->write($xlsRow, 2, $SUMSCORE[$PG_ID], set_format("xlsFmtTitle", "", "L", "", 1));

		$ARR_KPI_MAXY[$PG_ID] = $max_y;

		//===ข้อ 1 - 5===//
		$xlsRow++;
		$worksheet->write($xlsRow, 0, convert2thaidigit("1 = ".$ARR_KPI_TARGET1_DESC[$PG_ID]), set_format("xlsFmtTitle", "", "L", "", 1));

		$xlsRow++;
		$worksheet->write($xlsRow, 0, convert2thaidigit("2 = ".$ARR_KPI_TARGET2_DESC[$PG_ID]), set_format("xlsFmtTitle", "", "L", "", 1));

		$xlsRow++;
		$worksheet->write($xlsRow, 0, convert2thaidigit("3 = ".$ARR_KPI_TARGET3_DESC[$PG_ID]), set_format("xlsFmtTitle", "", "L", "", 1));

		$xlsRow++;
		$worksheet->write($xlsRow, 0, convert2thaidigit("4 = ".$ARR_KPI_TARGET4_DESC[$PG_ID]), set_format("xlsFmtTitle", "", "L", "", 1));

		$xlsRow++;
		$worksheet->write($xlsRow, 0, convert2thaidigit("5 = ".$ARR_KPI_TARGET5_DESC[$PG_ID]), set_format("xlsFmtTitle", "", "L", "", 1));
	} // end foreach

	$xlsRow++;
	$worksheet->write($xlsRow, 0, "คะแนนรวม ", set_format("xlsFmtTitle", "", "L", "", 0));
	$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "", "", "", 0));
	$worksheet->write($xlsRow, 2, convert2thaidigit($TOTAL_KPI_RESULT), set_format("xlsFmtTitle", "", "C", "", 0));
	$worksheet->write($xlsRow, 3, convert2thaidigit($TOTAL_KPI_WEIGHT), set_format("xlsFmtTitle", "", "C", "", 0));
	$worksheet->write($xlsRow, 4, convert2thaidigit($TOTAL_KPI_EVALUATE), set_format("xlsFmtTitle", "", "C", "", 1));

	$xlsRow++;
	$worksheet->write($xlsRow, 0, "คะแนนประเมิน ", set_format("xlsFmtTitle", "", "L", "", 0));
	$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "", "", "", 0));
	$worksheet->write($xlsRow, 2, convert2thaidigit(number_format(($TOTAL_KPI_EVALUATE / $TOTAL_KPI_WEIGHT), 2)), set_format("xlsFmtTitle", "", "C", "", 1));
	
	// ======================= PAGE 7 =====================//	
	$sheet_no_text = "kpi_formN_sheet_7";
			
	$worksheet = &$workbook->addworksheet($sheet_no_text);
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);

	$xlsRow=1;
	$worksheet->set_column(0, 0, 200);
	$worksheet->write($xlsRow, 0, "ผลสำเร็จของงานจริง", set_format("xlsFmtTitle", "B", "L", "", 1));

	print_header(6);

	$data_count = 0;
	foreach($ARR_KPI as $PG_ID => $KPI_NAME){
		$data_count++;

		$ii = $ARR_KPI_EVALUATE[$PG_ID];
		$xlsRow++;
		$worksheet->write($xlsRow, 0, convert2thaidigit($ARR_KPI_RESULT[$PG_ID]), set_format("xlsFmtTitle", "", "C", "", 0));
		$worksheet->write($xlsRow, 1, convert2thaidigit($ARR_KPI_WEIGHT[$PG_ID]), set_format("xlsFmtTitle", "", "C", "", 0));

		for($i=1; $i<=5; $i++){ 
			if($i == $ARR_KPI_EVALUATE[$PG_ID]){
				$worksheet->write($xlsRow, 1+$i, convert2thaidigit(${"ARR_KPI_TARGET".$i}[$PG_ID]), set_format("xlsFmtTitle", "", "C", "", 0));
			}else{
				$worksheet->write($xlsRow, 1+$i, "", set_format("xlsFmtTitle", "", "C", "", 0));
			} // end if			
		} // end for
	} // end foreach
		
	$xlsRow++;
	$worksheet->write($xlsRow, 0, "คะแนนรวม ", set_format("xlsFmtTitle", "", "C", "", 0));
	$worksheet->write($xlsRow, 1, convert2thaidigit($TOTAL_KPI_WEIGHT), set_format("xlsFmtTitle", "", "C", "", 0));
	$worksheet->write($xlsRow, 2, convert2thaidigit($TOTAL_KPI_EVALUATE), set_format("xlsFmtTitle", "", "C", "", 0));

	$xlsRow++;
	$worksheet->write($xlsRow, 0, "คะแนนประเมิน ", set_format("xlsFmtTitle", "", "C", "", 0));
	$worksheet->write($xlsRow, 1, convert2thaidigit(number_format(($TOTAL_KPI_EVALUATE / $TOTAL_KPI_WEIGHT), 2)), set_format("xlsFmtTitle", "", "C", "", 0));

	$xlsRow++;
	$worksheet->write($xlsRow, 0, "ผลรวมของคะแนนประเมินของผลสำเร็จของงาน", set_format("xlsFmtTitle", "", "C", "", 1));

	$xlsRow++;
	$worksheet->write($xlsRow, 0, "ผลรวมของคะแนนประเมินผลสำเร็จของงานทั้งหมด", set_format("xlsFmtTitle", "", "C", "", 1));
	$worksheet->write($xlsRow, 1, convert2thaidigit(number_format(($TOTAL_KPI_EVALUATE / $TOTAL_KPI_WEIGHT), 2)), set_format("xlsFmtTitle", "", "C", "", 0));

	$workbook->close();
	
	ini_set("max_execution_time", 30);

	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"$fname\"");
	header("Content-Disposition: inline; filename=\"$fname\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>