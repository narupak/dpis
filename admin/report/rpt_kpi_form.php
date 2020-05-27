<?
	include("../../php_scripts/connect_database.php");
	include("../php_scripts/pdf_wordarray_thaicut.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php");

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	//�Ҫ���˹��§ҹ
	if($PV_CODE){
		$select_org_name = "PV_ENG_NAME";
		$find_org_name = "PV_CODE='$PV_CODE'";
	}else{
		$select_org_name = "ORG_ENG_NAME";
		$find_org_name = "ORG_ID=$DEPARTMENT_ID";
	}
	$cmd = " select $select_org_name from PER_ORG where $find_org_name";
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
	$KF_YEAR = substr($KF_END_DATE, 0, 6);
	
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
					 where	PER_ID=$PER_ID
				   ";
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
		$REVIEW_PL_NAME = trim($REVIEW_PL_NAME)?($REVIEW_PL_NAME . $REVIEW_POSITION_LEVEL . (($REVIEW_PT_NAME != "�����" && $REVIEW_LEVEL_NO >= 6)?"$REVIEW_PT_NAME":"")):$REVIEW_LEVEL_NAME;
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
		$REVIEW_PL_NAME1 = trim($REVIEW_PL_NAME1)?($REVIEW_PL_NAME1 . $REVIEW_POSITION_LEVEL1 . (($REVIEW_PT_NAME1 != "�����" && $REVIEW_LEVEL_NO1 >= 6)?"$REVIEW_PT_NAME1":"")):$REVIEW_LEVEL_NAME1;
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
		$REVIEW_PL_NAME2 = trim($REVIEW_PL_NAME2)?($REVIEW_PL_NAME2 . $REVIEW_POSITION_LEVEL2 . (($REVIEW_PT_NAME2 != "�����" && $REVIEW_LEVEL_NO2 >= 6)?"$REVIEW_PT_NAME2":"")):$REVIEW_LEVEL_NAME2;
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
		$ARR_KPI_RESULT[$PG_ID] = $PG_RESULT;
		$ARR_KPI_EVALUATE[$PG_ID] = $PG_EVALUATE;
		
//		$TOTAL_KPI_EVALUATE += $PG_EVALUATE;
		$TOTAL_KPI_EVALUATE += ($PG_EVALUATE * $KPI_WEIGHT);
		$TOTAL_KPI_WEIGHT += $KPI_WEIGHT;
	} // end while
	
	$COUNT_KPI = count($ARR_KPI);
	
	//���ö��
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
		//�����ʴ���ṹ���ǹ Ẻ�����Թ�����ػ���ö��
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
	
	$heading_width2[0] = "135";
	$heading_width2[1] = "15";
	$heading_width2[2] = "10";
	
	$heading_width3[0] = "95";
	$heading_width3[1] = "43";
	$heading_width3[2] = "2";
	$heading_width3[3] = "60";

	$heading_width4[0] = "10";
	$heading_width4[1] = "130";
	$heading_width4[2] = "15";
	$heading_width4[3] = "15";
	$heading_width4[4] = "20";

	$heading_width5[0] = "50";
	$heading_width5[1] = "100";
	$heading_width5[2] = "50";

	function print_header($header_select){
		global $pdf, $heading_width1, $heading_width2, $heading_width3, $heading_width5;
		
		if($header_select == 1){
			$pdf->SetFont($font,'',14);
			$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
			$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	
			$pdf->Cell($heading_width1[0] ,14,"������稢ͧ�ҹ",'LTBR',0,'C',1);
			$pdf->Cell($heading_width1[1] ,14,"��Ǫ���Ѵ (KPI)",'LTBR',0,'C',1);
			$pdf->Cell(($heading_width1[2] * 5) ,7, "�������",'LTBR',1,'C',1);
			
			$pdf->x += ($heading_width1[0] + $heading_width1[1]);
			for($i=1; $i<=5; $i++){ 
				if($i < 5) $pdf->Cell($heading_width1[2] ,7, convert2thaidigit($i),'LTBR',0,'C',1);
				else $pdf->Cell($heading_width1[2] ,7, convert2thaidigit($i),'LTBR',1,'C',1);
			} // end for
		}elseif($header_select == 2){
			$pdf->SetFont($font,'',14);
			$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
			$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	
			$pdf->Cell($heading_width2[0] ,14,"�ŧҹ��ԧ",'LTBR',0,'C',1);
			$pdf->Cell($heading_width2[1] ,14,"���˹ѡ",'LTBR',0,'C',1);
			$pdf->Cell(($heading_width2[2] * 5) ,7, "�š�û����Թ",'LTBR',1,'C',1);
			
			$pdf->x += ($heading_width2[0] + $heading_width2[1]);
			for($i=1; $i<=5; $i++){ 
				if($i < 5) $pdf->Cell($heading_width2[2] ,7, convert2thaidigit($i),'LTBR',0,'C',1);
				else $pdf->Cell($heading_width2[2] ,7, convert2thaidigit($i),'LTBR',1,'C',1);
			} // end for
		}elseif($header_select == 3){
			$pdf->SetFont($font,'',14);
			$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
			$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	
			$pdf->Cell($heading_width3[0] ,14,"���ͤس�ѡɳ�/���ö��",'LTBR',0,'C',1);
			$pdf->Cell($heading_width3[1] ,14,"�дѺ����˹�",'LTBR',0,'C',1);
			$pdf->Cell($heading_width3[2] ,14,"",'LTBR',0,'C',1);
			$pdf->Cell($heading_width3[3] ,14,"�š�û����Թ",'LTBR',1,'C',1);
		}elseif($header_select == 5){
			$pdf->SetFont($font,'',14);
			$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
			$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	
			$pdf->Cell($heading_width5[0] ,7,"������� �ѡ�� ���ö��",'LTR',0,'C',1);
			$pdf->Cell($heading_width5[1] ,14,"�Ըա�þѲ��",'LTBR',0,'C',1);
			$pdf->Cell($heading_width5[2] ,14,"��ǧ���ҷ���ͧ��þѲ��",'LTBR',1,'C',1);

			$pdf->y -= 7;
			$pdf->Cell($heading_width5[0] ,7,"����ͧ���Ѻ��þѲ��",'LBR',1,'C',1);
		} // end if
	} // function		

	$pdf->AutoPageBreak = false;
	
	$pdf->Image("../images/logo_ocsc.jpg", 95, 15, 20, 26,"jpg");		// Original size = 50x65 => 20x26

	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(200, 40, "", 0, 1, 'C', 0);
	$pdf->SetFont($font,'',25);
	$pdf->Cell(200, 10, "$DEPARTMENT_NAME", 0, 1, 'C', 0);

	$pdf->SetFont($font,'',20);
	//$pdf->Cell(200, 10, "Office of the Civil Service Commission (OCSC)", 0, 1, 'C', 0);
	$pdf->Cell(200, 10, "$ORG_ENG_NAME", 0, 1, 'C', 0);

	$pdf->Cell(200, 7, "", 0, 1, 'C', 0);
	$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->SetFont($font,'b','',20);
	$pdf->Cell(10, 10, "", 0, 0, 'C', 0);
	$pdf->Cell(180, 10, "Ẻ�����Թ�š�û�Ժѵԧҹ", 0, 1, 'C', 1);

	$pdf->Cell(200, 7, "", 0, 1, 'C', 0);	
	$pdf->Cell(15, 10, "", 0, 0, 'C', 0);
	$pdf->Cell(30, 10, "�ͺ��û����Թ", 0, 0, 'L', 0);
	$pdf->Cell(15, 10, "", 0, 0, 'L', 0);
	$pdf->Cell(25, 10, convert2thaidigit("���駷�� 1"), 0, 0, 'L', 0);
	$pdf->Cell(20, 10, convert2thaidigit("1 ���Ҥ� "), 0, 0, 'L', 0);
	$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
	$pdf->Cell(20, 10, convert2thaidigit(($KF_CYCLE==1)?($KF_YEAR - 1):""), 0, 0, 'L', 0);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(30, 10, convert2thaidigit("�֧ 31 �չҤ� "), 0, 0, 'L', 0);
	$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
	$pdf->Cell(20, 10, convert2thaidigit(($KF_CYCLE==1)?$KF_YEAR:""), 0, 1, 'L', 0);
	if($KF_CYCLE==1) $pdf->Image("../images/checkbox_check.jpg", 58, ($pdf->y - 7), 4, 4,"jpg");
	else $pdf->Image("../images/checkbox_blank.jpg", 58, ($pdf->y - 7), 4, 4,"jpg");

	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(15, 10, "", 0, 0, 'C', 0);
	$pdf->Cell(30, 10, "", 0, 0, 'L', 0);
	$pdf->Cell(15, 10, "", 0, 0, 'L', 0);
	$pdf->Cell(25, 10, convert2thaidigit("���駷�� 2"), 0, 0, 'L', 0);
	$pdf->Cell(22, 10, convert2thaidigit("1 ����¹ "), 0, 0, 'L', 0);
	$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
	$pdf->Cell(18, 10, convert2thaidigit(($KF_CYCLE==2)?$KF_YEAR:""), 0, 0, 'L', 0);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(32, 10, convert2thaidigit("�֧ 30 �ѹ��¹ "), 0, 0, 'L', 0);
	$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
	$pdf->Cell(20, 10, convert2thaidigit(($KF_CYCLE==2)?$KF_YEAR:""), 0, 1, 'L', 0);	
	if($KF_CYCLE==2) $pdf->Image("../images/checkbox_check.jpg", 58, ($pdf->y - 7), 4, 4,"jpg");
	else $pdf->Image("../images/checkbox_blank.jpg", 58, ($pdf->y - 7), 4, 4,"jpg");

	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->SetFont($font,'b','',18);
	$pdf->Cell(200, 3, "", 0, 1, 'C', 0);
	$pdf->Cell(15, 10, "", 0, 0, 'C', 0);
	$pdf->Cell(40, 10, "���ͼ���Ѻ��û����Թ  ", 0, 0, 'L', 0);
	$pdf->SetFont($font,'b','',18);
	$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
	$pdf->Cell(130, 10, "$PER_NAME  ", 0, 1, 'L', 0);
	
	$pdf->SetFont($font,'b','',18);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(200, 3, "", 0, 1, 'C', 0);
	$pdf->Cell(15, 10, "", 0, 0, 'C', 0);
	$pdf->Cell(15, 10, "���˹�  ", 0, 0, 'L', 0);
	$pdf->SetFont($font,'b','',16);
	$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
	$pdf->Cell(100, 10, convert2thaidigit("$PL_NAME  "), 0, 0, 'L', 0);
	$pdf->SetFont($font,'b','',18);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(10, 10, "�дѺ  ", 0, 0, 'L', 0);
	$pdf->SetFont($font,'b','',16);
	$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
	$pdf->Cell(10, 10, convert2thaidigit($LEVEL_NAME), 0, 1, 'L', 0);

	$pdf->SetFont($font,'b','',18);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(15, 10, "", 0, 0, 'C', 0);
	$pdf->Cell(20, 10, "�ѧ�Ѵ  ", 0, 0, 'L', 0);
	$pdf->SetFont($font,'b','',16);
	$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
	$pdf->Cell(110, 10, convert2thaidigit("$ORG_NAME  "), 0, 0, 'L', 0);
	$pdf->SetFont($font,'b','',18);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(18, 10, "�Թ��͹  ", 0, 0, 'L', 0);
	$pdf->SetFont($font,'b','',16);
	$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
	$pdf->Cell(20, 10, convert2thaidigit(number_format($PER_SALARY)), 0, 0, 'C', 0);
	$pdf->SetFont($font,'b','',18);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(10, 10, "�ҷ", 0, 1, 'L', 0);

	$pdf->SetFont($font,'b','',18);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(200, 10, "", 0, 1, 'C', 0);
	$pdf->Cell(15, 10, "", 0, 0, 'C', 0);
	$pdf->Cell(50, 10, "���ͼ��ѧ�Ѻ�ѭ��/�������Թ  ", 0, 0, 'L', 0);
	$pdf->SetFont($font,'b','',18);
	$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
	$pdf->Cell(120, 10, "$REVIEW_PER_NAME  ", 0, 1, 'L', 0);

	$pdf->SetFont($font,'b','',18);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(15, 10, "", 0, 0, 'C', 0);
	$pdf->Cell(20, 10, "���˹�  ", 0, 0, 'L', 0);
	$pdf->SetFont($font,'b','',16);
	$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
	$pdf->Cell(150, 10, convert2thaidigit("$REVIEW_PL_NAME  "), 0, 1, 'L', 0);

	$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->SetFont($font,'b','',16);
	$pdf->Cell(200, 3, "", 0, 1, 'C', 0);	
	$pdf->Cell(10, 10, "", 0, 0, 'C', 0);
	$pdf->Cell(180, 10, "�Ӫ��ᨧ", 0, 1, 'C', 1);

	$pdf->SetFont($font,'',14);
	$pdf->Cell(200, 3, "", 0, 1, 'C', 0);	
	$pdf->Cell(12, 7, "", 0, 0, 'C', 0);
	$pdf->Cell(170, 7, "- Ẻ�����Թ�š�û�Ժѵԧҹ  ��Сͺ���� � ��ǹ  �ѧ���", 0, 1, 'L', 0);

	$pdf->SetFont($font,'b','',14);
	$pdf->Cell(17, 7, "", 0, 0, 'C', 0);
	$pdf->Cell(17, 7, "��ǹ��� � ", 0, 0, 'L', 0);
	$pdf->Cell(40, 7, "������¡�û�Ժѵԧҹ", 0, 0, 'L', 0);
	$pdf->SetFont($font,'',14);
	$pdf->Cell(25, 7, "(������ͺ��û����Թ)", 0, 1, 'C', 0);
	$pdf->SetFont($font,'',14);
	$pdf->Cell(17, 7, "", 0, 0, 'C', 0);
	$pdf->Cell(17, 7, "", 0, 0, 'L', 0);
	$pdf->Cell(150, 7, "���ѧ�Ѻ�ѭ��/�������Թ ��м���Ѻ��û����Թ�����ѹ�Ԩ�óҡ�˹�������¡�û�Ժѵԧҹ (������稢ͧ�ҹ������ö��)", 0, 1, 'L', 0);

	$pdf->SetFont($font,'b','',14);
	$pdf->Cell(17, 7, "", 0, 0, 'C', 0);
	$pdf->Cell(17, 7, "��ǹ��� � ", 0, 0, 'L', 0);
	$pdf->Cell(40, 7, "�š�û�Ժѵԧҹ��ԧ", 0, 0, 'L', 0);
	$pdf->SetFont($font,'',14);
	$pdf->Cell(25, 7, "(�ú�ͺ��û����Թ)", 0, 1, 'C', 0);
	$pdf->SetFont($font,'',14);
	$pdf->Cell(17, 7, "", 0, 0, 'C', 0);
	$pdf->Cell(17, 7, "", 0, 0, 'L', 0);
	$pdf->Cell(150, 7, "���ѧ�Ѻ�ѭ��/�������Թ �����Թ�š�÷ӧҹ��ԧ�ͧ����Ѻ��û����Թ��º�Ѻ������¡�û�Ժѵԧҹ", 0, 1, 'L', 0);

	$pdf->SetFont($font,'b','',14);
	$pdf->Cell(17, 7, "", 0, 0, 'C', 0);
	$pdf->Cell(17, 7, "��ǹ��� � ", 0, 0, 'L', 0);
	$pdf->Cell(40, 7, "�š�û����Թ", 0, 1, 'L', 0);
	$pdf->SetFont($font,'',14);
	$pdf->Cell(17, 7, "", 0, 0, 'C', 0);
	$pdf->Cell(17, 7, "", 0, 0, 'L', 0);
	$pdf->Cell(150, 7, "���ѧ�Ѻ�ѭ��/�������Թ �Ӽš�û�Ժѵԧҹ��ԧ���ǹ��� �  �Ҥӹǳ��ػ��ШѴ�дѺ�š�û����Թ", 0, 1, 'L', 0);

	$pdf->SetFont($font,'b','',14);
	$pdf->Cell(17, 7, "", 0, 0, 'C', 0);
	$pdf->Cell(17, 7, "��ǹ��� � ", 0, 0, 'L', 0);
	$pdf->Cell(40, 7, "Ἱ�Ѳ�Ҽš�û�Ժѵԧҹ��ºؤ�� (Individual Performance Improvement Plan : IPIP)", 0, 1, 'L', 0);
	$pdf->SetFont($font,'',14);
	$pdf->Cell(17, 7, "", 0, 0, 'C', 0);
	$pdf->Cell(17, 7, "", 0, 0, 'L', 0);
	$pdf->Cell(150, 7, "���ѧ�Ѻ�ѭ�������Ѻ����Ѻ��û����Թ㹡�èѴ��Ἱ�Ѳ�һ�Ѻ��ا�š�û�Ժѵԧҹ", 0, 1, 'L', 0);

	$pdf->SetFont($font,'b','',14);
	$pdf->Cell(17, 7, "", 0, 0, 'C', 0);
	$pdf->Cell(17, 7, "��ǹ��� � ", 0, 0, 'L', 0);
	$pdf->Cell(40, 7, "������繢ͧ���ѧ�Ѻ�ѭ���˹�ͪ�鹢���", 0, 1, 'L', 0);
	$pdf->SetFont($font,'',14);
	$pdf->Cell(17, 7, "", 0, 0, 'C', 0);
	$pdf->Cell(17, 7, "", 0, 0, 'L', 0);
	$pdf->Cell(150, 7, "���ѧ�Ѻ�ѭ���˹�ͪ�鹢��仡��蹡�ͧ�š�û�Ժѵԧҹ �š�û����Թ���Ἱ�Ѳ�ҷ��Ѵ�Ӣ��", 0, 1, 'L', 0);

	$pdf->SetFont($font,'',14);
	$pdf->Cell(200, 5, "", 0, 1, 'C', 0);
	$pdf->Cell(12, 7, "", 0, 0, 'C', 0);
	$pdf->Cell(170, 7, "- �ҡ���ͷ���Ẻ��û����Թ�š�û�Ժѵԧҹ��������§��  �Ҩ�����������������������", 0, 1, 'L', 0);
	
	// ======================= PAGE 2 =====================//	
	$pdf->AddPage();
	$pdf->SetFont($font,'',14);

	$pdf->SetDrawColor(hexdec("00"),hexdec("66"),hexdec("CC"));
	$pdf->SetFillColor(hexdec("99"),hexdec("CC"),hexdec("FF"));
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(200, 7, "��ǹ��� �  ������¡�û�Ժѵԧҹ", 1, 1, 'L', 1);

	$pdf->Cell(200, 3, "", 0, 1, 'C', 0);
	$pdf->SetDrawColor(hexdec("00"),hexdec("66"),hexdec("CC"));
	$pdf->SetFillColor(hexdec("BB"),hexdec("EE"),hexdec("FF"));
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(200, 7, "�.�  ������稢ͧ�ҹ���Ҵ��ѧ", 1, 1, 'L', 1);

	print_header(1);
	
	$data_count = 0;
	foreach($ARR_KPI as $PG_ID => $KPI_NAME){
		$data_count++;
		$border = "";
		$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;

		$pdf->SetFont($font,'',12);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

		$pdf->MultiCell($heading_width1[0], 7, convert2thaidigit($ARR_KPI_PFR[$PG_ID]), $border, "L");
		if($pdf->y > $max_y) $max_y = $pdf->y;
		$pdf->x = $start_x + $heading_width1[0];
		$pdf->y = $start_y;
		$pdf->MultiCell($heading_width1[1], 7, convert2thaidigit($KPI_NAME), $border, "L");
		if($pdf->y > $max_y) $max_y = $pdf->y;
		$pdf->x = $start_x + $heading_width1[0] + $heading_width1[1];
		$pdf->y = $start_y;
		for($i=1; $i<=5; $i++) $pdf->Cell($heading_width1[2], 7, convert2thaidigit(${"ARR_KPI_TARGET".$i}[$PG_ID]), $border, 0, 'C', 0);

		$ARR_KPI_MAXY[$PG_ID] = $max_y;

		//================= Draw Border Line ====================
		$line_start_y = $start_y;		$line_start_x = $start_x;
		$line_end_y = $max_y;		$line_end_x = $start_x;
		$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
			
		for($i=0; $i<=6; $i++){
			if($i < 2){
				$line_start_y = $start_y;		$line_start_x += $heading_width1[$i];
				$line_end_y = $max_y;		$line_end_x += $heading_width1[$i];
			}else{
				$line_start_y = $start_y;		$line_start_x += $heading_width1[2];
				$line_end_y = $max_y;		$line_end_x += $heading_width1[2];
			} // end if
			
			$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
		} // end for
		//====================================================

		if(($pdf->h - $max_y - 10) < 21){ 
			$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
			if($data_count < $COUNT_KPI){
				$pdf->AddPage();
				print_header(1);
				$max_y = $pdf->y;
			} // end if
		}else{
			$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
		} // end if

		$pdf->x = $start_x;			$pdf->y = $max_y;
	} // end foreach
	
	if(($pdf->h - $pdf_y) < 60) $pdf->AddPage();
	else $pdf->Cell(200, 5, "", 0, 1, 'C', 0);
	
	$pdf->SetDrawColor(hexdec("00"),hexdec("66"),hexdec("CC"));
	$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(200, 7, "ŧ�����Ѻ��Һ������¡�û�Ժѵԧҹ", 0, 1, 'L', 1);

	$pdf->Cell(200, 3, "", 0, 1, 'C', 0);
	$pdf->Cell(100, 7, ("ŧ���� ". str_repeat(".", 70) ." ����Ѻ��û����Թ"), 0, 0, 'C', 0);
	$pdf->Cell(100, 7, ("ŧ���� ". str_repeat(".", 70) ." ���ѧ�Ѻ�ѭ�Ҫ�鹵�"), 0, 1, 'C', 0);

//	$pdf->Cell(100, 7, ("( ". str_repeat(".", 90) ." )"), 0, 0, 'C', 0);
//	$pdf->Cell(100, 7, ("( ". str_repeat(".", 90) ." )"), 0, 1, 'C', 0);
	$pdf->Cell(100, 7, ("( $PER_NAME )"), 0, 0, 'C', 0);
	$pdf->Cell(100, 7, ("( $REVIEW_PER_NAME )"), 0, 1, 'C', 0);

	$pdf->Cell(100, 7, ("�ѹ��� ". str_repeat(".", 13) ." ��͹ ". str_repeat(".", 30) ." �.�. ". str_repeat(".", 20)), 0, 0, 'C', 0);
	$pdf->Cell(100, 7, ("�ѹ��� ". str_repeat(".", 13) ." ��͹ ". str_repeat(".", 30) ." �.�. ". str_repeat(".", 20)), 0, 1, 'C', 0);

	$pdf->Cell(200, 7, "", 0, 1, 'C', 0);
	$pdf->Cell(100, 7, "", 0, 0, 'C', 0);
	$pdf->Cell(100, 7, ("ŧ���� ". str_repeat(".", 61) ." ���ѧ�Ѻ�ѭ���˹�͢���"), 0, 1, 'C', 0);

	$pdf->Cell(100, 7, "", 0, 0, 'C', 0);
//	$pdf->Cell(100, 7, ("( ". str_repeat(".", 90) ." )"), 0, 1, 'C', 0);
	$pdf->Cell(100, 7, ("( $REVIEW_PER_NAME1 )"), 0, 1, 'C', 0);

	$pdf->Cell(100, 7, "", 0, 0, 'C', 0);
	$pdf->Cell(100, 7, ("�ѹ��� ". str_repeat(".", 13) ." ��͹ ". str_repeat(".", 30) ." �.�. ". str_repeat(".", 20)), 0, 1, 'C', 0);

	// ======================= PAGE 3 =====================//	
	$pdf->AddPage();
	$pdf->SetFont($font,'',14);

	$pdf->SetDrawColor(hexdec("00"),hexdec("66"),hexdec("CC"));
	$pdf->SetFillColor(hexdec("99"),hexdec("CC"),hexdec("FF"));
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(200, 7, "��ǹ��� �  �š�û�Ժѵԧҹ", 1, 1, 'L', 1);

	$pdf->Cell(200, 3, "", 0, 1, 'C', 0);
	$pdf->SetDrawColor(hexdec("00"),hexdec("66"),hexdec("CC"));
	$pdf->SetFillColor(hexdec("BB"),hexdec("EE"),hexdec("FF"));
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(200, 7, "�.�  ������稢ͧ�ҹ��ԧ", 1, 1, 'L', 1);

	print_header(2);
	
	$data_count = 0;
	foreach($ARR_KPI as $PG_ID => $KPI_NAME){
		$data_count++;
		$border = "";
		$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
		$max_y = $ARR_KPI_MAXY[$PG_ID];

		$pdf->SetFont($font,'',12);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

		$pdf->MultiCell($heading_width2[0], 7, convert2thaidigit($ARR_KPI_RESULT[$PG_ID]), $border, "L");
		if($pdf->y > $max_y) $max_y = $pdf->y;
		$pdf->x = $start_x + $heading_width2[0];
		$pdf->y = $start_y;
		$pdf->Cell($heading_width2[1], 7, convert2thaidigit($ARR_KPI_WEIGHT[$PG_ID]), $border, 0, 'R', 0);
		for($i=1; $i<=5; $i++){ 
			if($i == $ARR_KPI_EVALUATE[$PG_ID]){
				$pdf->SetFont($font,'b','',12);
				$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
				$pdf->Cell($heading_width2[2], 7, convert2thaidigit(${"ARR_KPI_TARGET".$i}[$PG_ID]), $border, 0, 'C', 0);
			}else{
				$pdf->SetFont($font,'',12);
				$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
//				$pdf->Cell($heading_width2[2], 7, convert2thaidigit(${"ARR_KPI_TARGET".$i}[$PG_ID]), $border, 0, 'C', 0);
				$pdf->Cell($heading_width2[2], 7, "", $border, 0, 'C', 0);
			} // end if			
		} // end for

		//================= Draw Border Line ====================
		$line_start_y = $start_y;		$line_start_x = $start_x;
		$line_end_y = $max_y;		$line_end_x = $start_x;
		$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
			
		for($i=0; $i<=6; $i++){
			if($i < 2){
				$line_start_y = $start_y;		$line_start_x += $heading_width2[$i];
				$line_end_y = $max_y;		$line_end_x += $heading_width2[$i];
			}else{
				$line_start_y = $start_y;		$line_start_x += $heading_width2[2];
				$line_end_y = $max_y;		$line_end_x += $heading_width2[2];
			} // end if
			
			$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
		} // end for
		//====================================================

		if(($pdf->h - $max_y - 10) < 21){ 
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
	} // end foreach
	
	$pdf->SetFont($font,'',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell($heading_width2[0], 7, "��ṹ��� ", $border, 0, 'R', 0);
	$pdf->SetFont($font,'',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
	$pdf->Cell($heading_width2[1], 7, convert2thaidigit($TOTAL_KPI_WEIGHT), 1, 0, 'R', 0);
	$pdf->SetFont($font,'b','',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
	$pdf->Cell(($heading_width2[2] * 5), 7, convert2thaidigit($TOTAL_KPI_EVALUATE), 1, 1, 'C', 0);

	$pdf->SetFont($font,'',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell($heading_width2[0], 7, "��ṹ�����Թ ", $border, 0, 'R', 0);
	$pdf->Cell($heading_width2[1], 7, "", $border, 0, 'R', 0);
	$pdf->SetFont($font,'b','',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
//	$pdf->Cell(($heading_width2[2] * 5), 7, convert2thaidigit(number_format(($TOTAL_KPI_EVALUATE / $COUNT_KPI), 2)), 1, 1, 'C', 0);
	$pdf->Cell(($heading_width2[2] * 5), 7, convert2thaidigit(number_format(($TOTAL_KPI_EVALUATE / $TOTAL_KPI_WEIGHT), 2)), 1, 1, 'C', 0);

	if(($pdf->h - $pdf_y) < 60) $pdf->AddPage();
	else $pdf->Cell(200, 5, "", 0, 1, 'C', 0);
	
	$pdf->SetFont($font,'',14);
	$pdf->SetDrawColor(hexdec("00"),hexdec("66"),hexdec("CC"));
	$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(200, 7, "������ͧ��ṹ�����Թ�ͧ������稢ͧ�ҹ", 0, 1, 'L', 1);
	$pdf->Cell(200, 5, "", 0, 1, 'C', 0);

	$pdf->Cell(70, 7, "������ͧ��ṹ�����Թ������稢ͧ�ҹ������", 0, 0, 'R', 0);
	$pdf->Cell(5, 7, "", 0, 0, 'C', 0);
	$pdf->SetFont($font,'b','',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
//	$pdf->Cell(20, 7, convert2thaidigit(number_format(($TOTAL_KPI_EVALUATE / $COUNT_KPI), 2)), 1, 0, 'C', 0);
	$pdf->Cell(20, 7, convert2thaidigit(number_format(($TOTAL_KPI_EVALUATE / $TOTAL_KPI_WEIGHT), 2)), 1, 0, 'C', 0);
	$pdf->Cell(5, 7, "", 0, 1, 'C', 0);

	// ======================= PAGE 4 =====================//	
	$pdf->AddPage();
	$pdf->SetFont($font,'',14);

	$pdf->SetDrawColor(hexdec("00"),hexdec("66"),hexdec("CC"));
	$pdf->SetFillColor(hexdec("BB"),hexdec("EE"),hexdec("FF"));
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(($heading_width3[0] + $heading_width3[1]), 7, "�.�  �س�ѡɳ�/���ö�з��Ҵ��ѧ", 1, 0, 'L', 1);
	$pdf->Cell($heading_width3[2], 7, "", 1, 0, 'L', 1);
	$pdf->Cell($heading_width3[3], 7, " �.�  �س�ѡɳ�/���ö�з���ʴ���ԧ", 1, 1, 'L', 1);

	print_header(3);

	$data_count = 0;
	foreach($ARR_COMPETENCE as $KC_ID => $CP_NAME){
		$data_count++;
		$border = "";
		$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;

		$pdf->SetFont($font,'',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

		$pdf->MultiCell($heading_width3[0], 7, convert2thaidigit($CP_NAME), $border, "L");
		if($pdf->y > $max_y) $max_y = $pdf->y;
		$pdf->x = $start_x + $heading_width3[0];
		$pdf->y = $start_y;
		$pdf->Cell($heading_width3[1], 7, convert2thaidigit($ARR_COMPETENCE_TARGET[$KC_ID]), $border, 0, 'C', 0);
		$pdf->Cell($heading_width3[2], 7, "", $border, 0, 'C', 0);
		$pdf->SetFont($font,'b','',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
		$pdf->Cell($heading_width3[3], 7, convert2thaidigit($ARR_COMPETENCE_EVALUATE[$KC_ID]), $border, 0, 'C', 0);

		//================= Draw Border Line ====================
		$line_start_y = $start_y;		$line_start_x = $start_x;
		$line_end_y = $max_y;		$line_end_x = $start_x;
		$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
			
		for($i=0; $i<=3; $i++){
			$line_start_y = $start_y;		$line_start_x += $heading_width3[$i];
			$line_end_y = $max_y;		$line_end_x += $heading_width3[$i];
			
			$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
		} // end for
		//====================================================

		if(($pdf->h - $max_y - 10) < 15){ 
			$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
			if($data_count < $COUNT_COMPETENCE){
				$pdf->AddPage();
				print_header(3);
				$max_y = $pdf->y;
			} // end if
		}else{
			$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
		} // end if

		$pdf->x = $start_x;			$pdf->y = $max_y;
	} // end foreach

	$pdf->SetFont($font,'',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(3, 7, "*", 0, 0, 'L', 0);
	$pdf->SetTextColor(hexdec("99"),hexdec("00"),hexdec("00"));
	$pdf->Cell(100, 7, "�����Թ੾�Т���Ҫ����дѺ���ѧ�Ѻ�ѭ��", 0, 1, 'L', 0);

	$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(200, 3, "", 0, 1, 'C', 0);
	$pdf->Cell(200, 7, "������ͧ��ṹ�س�ѡɳ�/���ö��", 0, 1, 'L', 1);

	$pdf->Cell(200, 2, "", 0, 1, 'C', 0);
	$pdf->Cell($heading_width4[0] ,10,"",'',0,'C',0);
	$pdf->Cell($heading_width4[1] ,10,"��ѡࡳ��",'LTBR',0,'C',0);
	$pdf->Cell($heading_width4[2] ,10,"�ӹǹ",'LTBR',0,'C',0);
	$pdf->Cell($heading_width4[3] ,10,"��Ǥٳ",'LTBR',0,'C',0);
	$pdf->Cell($heading_width4[4] ,10,"��ṹ",'LTBR',1,'C',0);

	$pdf->SetFont($font,'',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell($heading_width4[0] ,10,"",'',0,'C',0);
	$pdf->Cell($heading_width4[1] ,10,"�ӹǹ���ö�з�����дѺ�ͧ���ö�� �٧����������ҡѺ �дѺ�ͧ���ö�з��Ҵ��ѧ",'LTBR',0,'L',0);
	$pdf->SetFont($font,'b','',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
	$pdf->Cell($heading_width4[2] ,10, convert2thaidigit($ARR_COMPETENCE_COUNT[GE]),'LTBR',0,'C',0);
	$pdf->SetFont($font,'',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell($heading_width4[3] ,10,"�",'LTBR',0,'C',0);
	$pdf->SetFont($font,'b','',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
	$pdf->Cell($heading_width4[4] ,10, (convert2thaidigit($ARR_COMPETENCE_COUNT[GE])?convert2thaidigit($ARR_COMPETENCE_SCORE[GE]):""),'LTBR',1,'C',0);

	$pdf->SetFont($font,'',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell($heading_width4[0] ,10,"",'',0,'C',0);
	$pdf->Cell($heading_width4[1] ,10,"�ӹǹ���ö�з�����дѺ�ͧ���ö�� ��ӡ��� �дѺ�ͧ���ö�з��Ҵ��ѧ � �дѺ",'LTBR',0,'L',0);
	$pdf->SetFont($font,'b','',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
	$pdf->Cell($heading_width4[2] ,10, convert2thaidigit($ARR_COMPETENCE_COUNT[L1]),'LTBR',0,'C',0);
	$pdf->SetFont($font,'',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell($heading_width4[3] ,10,"�",'LTBR',0,'C',0);
	$pdf->SetFont($font,'b','',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
	$pdf->Cell($heading_width4[4] ,10, (convert2thaidigit($ARR_COMPETENCE_COUNT[L1])?convert2thaidigit($ARR_COMPETENCE_SCORE[L1]):""),'LTBR',1,'C',0);

	$pdf->SetFont($font,'',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell($heading_width4[0] ,10,"",'',0,'C',0);
	$pdf->Cell($heading_width4[1] ,10,"�ӹǹ���ö�з�����дѺ�ͧ���ö�� ��ӡ��� �дѺ�ͧ���ö�з��Ҵ��ѧ � �дѺ",'LTBR',0,'L',0);
	$pdf->SetFont($font,'b','',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
	$pdf->Cell($heading_width4[2] ,10, convert2thaidigit($ARR_COMPETENCE_COUNT[L2]),'LTBR',0,'C',0);
	$pdf->SetFont($font,'',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell($heading_width4[3] ,10,"�",'LTBR',0,'C',0);
	$pdf->SetFont($font,'b','',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
	$pdf->Cell($heading_width4[4] ,10, (convert2thaidigit($ARR_COMPETENCE_COUNT[L2])?convert2thaidigit($ARR_COMPETENCE_SCORE[L2]):""),'LTBR',1,'C',0);

	$pdf->SetFont($font,'',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell($heading_width4[0] ,10,"",'',0,'C',0);
	$pdf->Cell($heading_width4[1] ,10,"�ӹǹ���ö�з�����дѺ�ͧ���ö�� ��ӡ��� �дѺ�ͧ���ö�з��Ҵ��ѧ � �дѺ",'LTBR',0,'L',0);
	$pdf->SetFont($font,'b','',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
	$pdf->Cell($heading_width4[2] ,10, convert2thaidigit($ARR_COMPETENCE_COUNT[L3]),'LTBR',0,'C',0);
	$pdf->SetFont($font,'',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell($heading_width4[3] ,10,"�",'LTBR',0,'C',0);
	$pdf->SetFont($font,'b','',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
	$pdf->Cell($heading_width4[4] ,10, (convert2thaidigit($ARR_COMPETENCE_COUNT[L3])?convert2thaidigit($ARR_COMPETENCE_SCORE[L3]):""),'LTBR',1,'C',0);
	
	$pdf->SetFont($font,'',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell($heading_width4[0] ,10,"",'',0,'C',0);
	$pdf->Cell(($heading_width4[1] + $heading_width4[2] + $heading_width4[3]) ,10,"������ͧ��ṹ���ö�з�����",'',0,'C',0);
	$pdf->SetFont($font,'b','',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
	$pdf->Cell($heading_width4[4] ,10, convert2thaidigit($TOTAL_COMPETENCE_SCORE),'LTBR',1,'C',0);

	$pdf->SetFont($font,'',14);
	$pdf->SetDrawColor(hexdec("00"),hexdec("66"),hexdec("CC"));
	$pdf->SetFillColor(hexdec("99"),hexdec("CC"),hexdec("FF"));
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(200, 3, "", 0, 1, 'C', 0);
	$pdf->Cell(200, 7, "��ǹ��� �  �š�û����Թ", 1, 1, 'L', 1);

	$pdf->SetFont($font,'',14);
	$pdf->SetDrawColor(hexdec("00"),hexdec("66"),hexdec("CC"));
	$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(200, 3, "", 0, 1, 'C', 0);
	$pdf->Cell(10, 7, "", 0, 0, 'C', 0);
	$pdf->Cell(40, 7, "��ṹ������稢ͧ�ҹ", 0, 1, 'C', 1);
	$pdf->Cell(200, 3, "", 0, 1, 'C', 0);

	$pdf->SetFont($font,'',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(40, 7, "", 0, 0, 'C', 0);
	$pdf->Cell(70, 7, "������ͧ��ṹ������稢ͧ�ҹ", "B", 0, 'C', 0);
	$pdf->Cell(10, 7, "", 0, 0, 'C', 0);
	$pdf->SetFont($font,'b','',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
	$pdf->Cell(15, 7, convert2thaidigit($SCORE_KPI), 1, 0, 'C', 0);
	$pdf->SetFont($font,'',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(15, 14, "=", 0, 0, 'C', 0);
	$pdf->SetFont($font,'b','',18);
	$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
	$pdf->Cell(20, 14, convert2thaidigit(number_format($SHOW_SCORE_KPI, 2)), 1, 0, 'C', 0);
	$pdf->Cell(10, 7, "", 0, 1, 'C', 0);

	$pdf->SetFont($font,'',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(40, 7, "", 0, 0, 'C', 0);
//	$pdf->Cell(70, 7, "�ӹǹ \"������稢ͧ�ҹ\" x � (��ṹ���)", 0, 0, 'C', 0);
	$pdf->Cell(70, 7, "���˹ѡ��� \"������稢ͧ�ҹ\" x � (��ṹ���)", 0, 0, 'C', 0);
	$pdf->Cell(10, 7, "", 0, 0, 'C', 0);
	$pdf->SetFont($font,'b','',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
//	$pdf->Cell(15, 7, convert2thaidigit($COUNT_KPI * 5), 1, 1, 'C', 0);
	$pdf->Cell(15, 7, convert2thaidigit($TOTAL_KPI_WEIGHT * 5), 1, 1, 'C', 0);

	$pdf->SetFont($font,'',14);
	$pdf->SetDrawColor(hexdec("00"),hexdec("66"),hexdec("CC"));
	$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(200, 5, "", 0, 1, 'C', 0);
	$pdf->Cell(10, 7, "", 0, 0, 'C', 0);
	$pdf->Cell(40, 7, "��ṹ���ö��", 0, 1, 'C', 1);
	$pdf->Cell(200, 3, "", 0, 1, 'C', 0);

	$pdf->SetFont($font,'',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(40, 7, "", 0, 0, 'C', 0);
	$pdf->Cell(70, 7, "������ͧ��ṹ���ö�з�����", "B", 0, 'C', 0);
	$pdf->Cell(10, 7, "", 0, 0, 'C', 0);
	$pdf->SetFont($font,'b','',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
	$pdf->Cell(15, 7, convert2thaidigit($SCORE_COMPETENCE), 1, 0, 'C', 0);
	$pdf->SetFont($font,'',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(15, 14, "=", 0, 0, 'C', 0);
	$pdf->SetFont($font,'b','',18);
	$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
	$pdf->Cell(20, 14, convert2thaidigit(number_format($SHOW_SCORE_COMPETENCE, 2)), 1, 0, 'C', 0);
	$pdf->Cell(10, 7, "", 0, 1, 'C', 0);

	$pdf->SetFont($font,'',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(40, 7, "", 0, 0, 'C', 0);
//	$pdf->Cell(70, 7, "�ӹǹ \"���ö��\" x � (��ṹ���)", 0, 0, 'C', 0);
	$pdf->Cell(70, 7, "� (��ṹ���)", 0, 0, 'C', 0);
	$pdf->Cell(10, 7, "", 0, 0, 'C', 0);
	$pdf->SetFont($font,'b','',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
//	$pdf->Cell(15, 7, convert2thaidigit($COUNT_COMPETENCE * 3), 1, 1, 'C', 0);
	$pdf->Cell(15, 7, convert2thaidigit(5), 1, 1, 'C', 0);

	// ======================= PAGE 5 =====================//	
	$pdf->AddPage();
	$pdf->SetFont($font,'',14);

	$pdf->SetDrawColor(hexdec("00"),hexdec("66"),hexdec("CC"));
	$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(200, 7, "", 0, 1, 'C', 0);
	$pdf->Cell(10, 7, "", 0, 0, 'C', 0);
	$pdf->Cell(40, 7, "�š�û����Թ�����", 0, 1, 'C', 1);
	$pdf->Cell(200, 7, "", 0, 1, 'C', 0);
	
	$pdf->SetFont($font,'',14);
	$pdf->SetDrawColor(hexdec("00"),hexdec("66"),hexdec("CC"));
	$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(25, 7, "", 0, 0, 'C', 0);
	$pdf->Cell(40, 7, "ͧ���Сͺ", 1, 0, 'C', 1);
	$pdf->Cell(20, 7, "���˹ѡ", 1, 0, 'C', 1);
	$pdf->Cell(75, 7, "��äӹǳ", 1, 0, 'C', 1);
	$pdf->Cell(25, 7, "�š�û����Թ", 1, 1, 'C', 1);

	$pdf->SetFont($font,'b','',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(25, 7, "", 0, 0, 'C', 0);
	$pdf->Cell(40, 14, "�. ������稢ͧ�ҹ", 1, 0, 'L', 0);
	$pdf->Cell(20, 14, convert2thaidigit($PERFORMANCE_WEIGHT."%"), 1, 0, 'C', 0);
	$pdf->Cell(75, 14, "", 1, 0, 'C', 0);
	$pdf->x -= 75;		$pdf->y += 2;
	$pdf->SetFont($font,'b','',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(25, 10, "��ǹ��� �", 0, 0, 'C', 0);
	$pdf->Cell(10, 10, "", 0, 0, 'C', 0);
	$pdf->SetFont($font,'b','',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
	$pdf->Cell(15, 10, convert2thaidigit(number_format($SHOW_SCORE_KPI, 2)), 1, 0, 'C', 0);
	$pdf->SetFont($font,'b','',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(25, 10, convert2thaidigit("    x    $PERFORMANCE_WEIGHT     ="), 0, 0, 'L', 0);
	$pdf->y -= 2;
	$pdf->SetFont($font,'b','',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
	$pdf->Cell(25, 14, convert2thaidigit(number_format($SUM_KPI, 2)), 1, 1, 'C', 0);
	

	$pdf->SetFont($font,'b','',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(25, 7, "", 0, 0, 'C', 0);
	$pdf->Cell(40, 14, "�. ���ö��", 1, 0, 'L', 0);
	$pdf->Cell(20, 14, convert2thaidigit($COMPETENCE_WEIGHT."%"), 1, 0, 'C', 0);
	$pdf->Cell(75, 14, "", 1, 0, 'C', 0);
	$pdf->x -= 75;		$pdf->y += 2;
	$pdf->SetFont($font,'b','',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(25, 10, "��ǹ��� �", 0, 0, 'C', 0);
	$pdf->Cell(10, 10, "", 0, 0, 'C', 0);
	$pdf->SetFont($font,'b','',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
	$pdf->Cell(15, 10, convert2thaidigit(number_format($SHOW_SCORE_COMPETENCE, 2)), 1, 0, 'C', 0);
	$pdf->SetFont($font,'b','',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(25, 10, convert2thaidigit("    x    $COMPETENCE_WEIGHT     ="), 0, 0, 'L', 0);
	$pdf->y -= 2;
	$pdf->SetFont($font,'b','',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
	$pdf->Cell(25, 14, convert2thaidigit(number_format($SUM_COMPETENCE, 2)), 1, 1, 'C', 0);

	$pdf->SetFont($font,'b','',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(25, 7, "", 0, 0, 'C', 0);
	$pdf->Cell(135, 14, "�š�û����Թ����� (������稢ͧ�ҹ) + (���ö��)", 1, 0, 'C', 1);
	$pdf->SetFont($font,'b','',18);
	$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
	$pdf->Cell(25, 14, convert2thaidigit(number_format($SUM_TOTAL, 2)), 1, 1, 'C', 1);

	$pdf->SetFont($font,'',14);
	$pdf->SetDrawColor(hexdec("00"),hexdec("66"),hexdec("CC"));
	$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(200, 7, "", 0, 1, 'C', 0);
	$pdf->Cell(10, 7, "", 0, 0, 'C', 0);
	$pdf->Cell(40, 7, "�дѺ�š�û����Թ�����", 0, 1, 'C', 1);
	$pdf->Cell(200, 3, "", 0, 1, 'C', 0);

//	if($SUM_TOTAL >= 90){
//		$pdf->SetFont($font,'b','',16);
//		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
//		$show_checkbox = "../images/checkbox_check.jpg";
//	}else{
		$pdf->SetFont($font,'',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
		$show_checkbox = "../images/checkbox_blank.jpg";
//	} // end if
	$pdf->Cell(25, 8, "", 0, 0, 'C', 0);
	$pdf->Cell(10, 8, "", 0, 0, 'C', 0);
	$pdf->Image($show_checkbox, ($pdf->x - 7), ($pdf->y + 2), 4, 4,"jpg");
	$pdf->Cell(50, 8, "����", 0, 1, 'L', 0);
//	$pdf->Cell(30, 8, "�� - ���%", 0, 1, 'L', 0);

//	if($SUM_TOTAL >= 80 && $SUM_TOTAL < 90){
//		$pdf->SetFont($font,'b','',16);
//		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
//		$show_checkbox = "../images/checkbox_check.jpg";
//	}else{
		$pdf->SetFont($font,'',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
		$show_checkbox = "../images/checkbox_blank.jpg";
//	} // end if
	$pdf->Cell(25, 8, "", 0, 0, 'C', 0);
	$pdf->Cell(10, 8, "", 0, 0, 'C', 0);
	$pdf->Image($show_checkbox, ($pdf->x - 7), ($pdf->y + 2), 4, 4,"jpg");
	$pdf->Cell(50, 8, "���ҡ", 0, 1, 'L', 0);
//	$pdf->Cell(30, 8, "�� - ��%", 0, 1, 'L', 0);

//	if($SUM_TOTAL >= 70 && $SUM_TOTAL < 80){
//		$pdf->SetFont($font,'b','',16);
//		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
//		$show_checkbox = "../images/checkbox_check.jpg";
//	}else{
		$pdf->SetFont($font,'',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
		$show_checkbox = "../images/checkbox_blank.jpg";
//	} // end if
	$pdf->Cell(25, 8, "", 0, 0, 'C', 0);
	$pdf->Cell(10, 8, "", 0, 0, 'C', 0);
	$pdf->Image($show_checkbox, ($pdf->x - 7), ($pdf->y + 2), 4, 4,"jpg");
	$pdf->Cell(50, 8, "��", 0, 1, 'L', 0);
//	$pdf->Cell(30, 8, "�� - ��%", 0, 1, 'L', 0);

//	if($SUM_TOTAL >= 60 && $SUM_TOTAL < 70){
//		$pdf->SetFont($font,'b','',16);
//		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
//		$show_checkbox = "../images/checkbox_check.jpg";
//	}else{
		$pdf->SetFont($font,'',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
		$show_checkbox = "../images/checkbox_blank.jpg";
//	} // end if
	$pdf->Cell(25, 8, "", 0, 0, 'C', 0);
	$pdf->Cell(10, 8, "", 0, 0, 'C', 0);
	$pdf->Image($show_checkbox, ($pdf->x - 7), ($pdf->y + 2), 4, 4,"jpg");
	$pdf->Cell(50, 8, "����", 0, 1, 'L', 0);
//	$pdf->Cell(30, 8, "�� - ��%", 0, 1, 'L', 0);

//	if($SUM_TOTAL < 60){
//		$pdf->SetFont($font,'b','',16);
//		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
//		$show_checkbox = "../images/checkbox_check.jpg";
//	}else{
		$pdf->SetFont($font,'',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
		$show_checkbox = "../images/checkbox_blank.jpg";
//	} // end if
	$pdf->Cell(25, 8, "", 0, 0, 'C', 0);
	$pdf->Cell(10, 8, "", 0, 0, 'C', 0);
	$pdf->Image($show_checkbox, ($pdf->x - 7), ($pdf->y + 2), 4, 4,"jpg");
	$pdf->Cell(50, 8, "�ѧ��ͧ��Ѻ��ا", 0, 1, 'L', 0);
//	$pdf->Cell(30, 8, "� - ��%", 0, 1, 'L', 0);

	$pdf->SetFont($font,'',14);
	$pdf->SetDrawColor(hexdec("00"),hexdec("66"),hexdec("CC"));
	$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(200, 3, "", 0, 1, 'C', 0);
	$pdf->Cell(200, 7, "������繢ͧ���ѧ�Ѻ�ѭ�Ҫ�鹵�", 1, 1, 'L', 1);
	$pdf->Cell(200, 7, "������繴�ҹ�ŧҹ (�кبش�����/������觷�������)", "LTR", 1, 'L', 0);
	$pdf->SetFont($font,'',12);
	$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
	$pdf->Cell(200, 25, "", "LR", 1, 'L', 0);
	$save_x = $pdf->x;		$save_y = $pdf->y;
	$pdf->x += 5;			$pdf->y -= 25;
	$pdf->MultiCell(190, 5, convert2thaidigit($RESULT_COMMENT), "", "L");
	$pdf->x = $save_x;		$pdf->y = $save_y;
	$pdf->SetFont($font,'',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(200, 7, "������繴�ҹ���ö�� (�к����ö�з��ⴴ�����/�������ö�з�������Ѻ��þѲ��)", "LR", 1, 'L', 0);
	$pdf->SetFont($font,'',12);
	$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
	$pdf->Cell(200, 25, "", "LBR", 1, 'L', 0);
	$save_x = $pdf->x;		$save_y = $pdf->y;
	$pdf->x += 5;			$pdf->y -= 25;
	$pdf->MultiCell(190, 5, convert2thaidigit($COMPETENCE_COMMENT), "", "L");
	$pdf->x = $save_x;		$pdf->y = $save_y;
/*
	$pdf->SetFont($font,'',14);
	$pdf->SetDrawColor(hexdec("00"),hexdec("66"),hexdec("CC"));
	$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(200, 5, "", 0, 1, 'C', 0);
	$pdf->Cell(200, 7, "�����������ǡѺ�������͹����Թ��͹", 0, 1, 'L', 1);
	$pdf->Cell(200, 3, "", 0, 1, 'C', 0);

	$pdf->Cell(20, 8, "", 0, 0, 'C', 0);
	$pdf->Cell(80, 8, "�������͹����Թ��͹ � ���", 0, 0, 'L', 0);
	if($SALARY_RESULT==1) $pdf->Image("../images/checkbox_check.jpg", ($pdf->x - 87), ($pdf->y + 2), 4, 4,"jpg");
	else $pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 87), ($pdf->y + 2), 4, 4,"jpg");
	$pdf->Cell(10, 8, "", 0, 0, 'C', 0);
	$pdf->Cell(70, 8, "�������͹����Թ��͹ �.� �������Ѻ�ͺ��� � ����� �.�. ��Ҵ���", 0, 0, 'L', 0);
	if($SALARY_RESULT==4) $pdf->Image("../images/checkbox_check.jpg", ($pdf->x - 77), ($pdf->y + 2), 4, 4,"jpg");
	else $pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 77), ($pdf->y + 2), 4, 4,"jpg");
	$pdf->Cell(10, 8, "", 0, 1, 'C', 0);

	$pdf->Cell(20, 8, "", 0, 0, 'C', 0);
	$pdf->Cell(80, 8, "�������͹����Թ��͹ �.� ���", 0, 0, 'L', 0);
	if($SALARY_RESULT==2) $pdf->Image("../images/checkbox_check.jpg", ($pdf->x - 87), ($pdf->y + 2), 4, 4,"jpg");
	else $pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 87), ($pdf->y + 2), 4, 4,"jpg");
	$pdf->Cell(10, 8, "", 0, 0, 'C', 0);
	$pdf->Cell(70, 8, "�������͹����Թ��͹ �.�.���� ��� �� ��ä���", 0, 1, 'L', 0);

	$pdf->Cell(20, 8, "", 0, 0, 'C', 0);
	$pdf->Cell(80, 8, "���������͹����Թ��͹", 0, 0, 'L', 0);
	if($SALARY_RESULT==3) $pdf->Image("../images/checkbox_check.jpg", ($pdf->x - 87), ($pdf->y + 2), 4, 4,"jpg");
	else $pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 87), ($pdf->y + 2), 4, 4,"jpg");
	$pdf->Cell(10, 8, "", 0, 1, 'C', 0);

	$pdf->Cell(100, 3, "", 0, 1, 'C', 0);
	$pdf->Cell(95, 7, "�к��˵ؼ�㹡���ʹ�����͹����Թ��͹", 1, 1, 'L', 1);
	$pdf->SetFont($font,'',12);
	$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
	$pdf->Cell(95, 35, "", 1, 1, 'L', 0);
	$save_x = $pdf->x;		$save_y = $pdf->y;
	$pdf->x += 2;			$pdf->y -= 35;
	$pdf->MultiCell(90, 5, convert2thaidigit($SALARY_REMARK1), "", "L");
	$pdf->x = $save_x;		$pdf->y = $save_y;
	
	$save_x = $pdf->x;		$save_y = $pdf->y;
	$pdf->y -= 52;			

	$pdf->SetFont($font,'',10);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->x += 100;
	$pdf->MultiCell(100, 6, "�к��˵���Ҽš�û����Թ�������ࡳ������͹����Թ��͹ � ���㹤����á���բ�ͨӡѴ����ͧ�ǵ� ��мš�û����Թ�����ա㹤��駷�� � ������ǵ��������͹����Թ��͹�����ҧ�ú�ҧ", 1, "L", 1);
	$pdf->SetFont($font,'',12);
	$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
	$pdf->x += 100;
	$pdf->Cell(100, 40, "", 1, 1, 'L', 0);
	$save_x = $pdf->x;		$save_y = $pdf->y;
	$pdf->x += 102;		$pdf->y -= 40;
	$pdf->MultiCell(96, 5, convert2thaidigit($SALARY_REMARK2), "", "L");
	$pdf->x = $save_x;		$pdf->y = $save_y;
*/
	// ======================= PAGE 6 =====================//	
	$pdf->AddPage();
	$pdf->SetFont($font,'',14);

	$pdf->SetDrawColor(hexdec("00"),hexdec("66"),hexdec("CC"));
	$pdf->SetFillColor(hexdec("99"),hexdec("CC"),hexdec("FF"));
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(200, 7, "��ǹ��� �  Ἱ�Ѳ�ҡ�û�Ժѵԧҹ��ºؤ�� (Individual Performance Improvement Plan : IPIP)", 1, 1, 'L', 1);
	$pdf->Cell(200, 3, "", 0, 1, 'C', 0);
	
	print_header(5);

	$data_count = 0;
	foreach($ARR_IPIP as $IPIP_ID => $DEVELOP_COMPETENCE){
		$data_count++;
		$border = "";
		$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;

		$pdf->SetFont($font,'',12);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

		$pdf->MultiCell($heading_width5[0], 7, convert2thaidigit($DEVELOP_COMPETENCE), $border, "L");
		if($pdf->y > $max_y) $max_y = $pdf->y;
		$pdf->x = $start_x + $heading_width5[0];
		$pdf->y = $start_y;
		$pdf->MultiCell($heading_width5[1], 7, convert2thaidigit($ARR_IPIP_METHOD[$IPIP_ID]), $border, "L");
		if($pdf->y > $max_y) $max_y = $pdf->y;
		$pdf->x = $start_x + $heading_width5[0] + $heading_width5[1];
		$pdf->y = $start_y;
		$pdf->MultiCell($heading_width5[2], 7, convert2thaidigit($ARR_IPIP_INTERVAL[$IPIP_ID]), $border, "L");
		if($pdf->y > $max_y) $max_y = $pdf->y;
		$pdf->x = $start_x + $heading_width5[0] + $heading_width5[1] + $heading_width5[2];
		$pdf->y = $start_y;

		//================= Draw Border Line ====================
		$line_start_y = $start_y;		$line_start_x = $start_x;
		$line_end_y = $max_y;		$line_end_x = $start_x;
		$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
			
		for($i=0; $i<=2; $i++){
			$line_start_y = $start_y;		$line_start_x += $heading_width5[$i];
			$line_end_y = $max_y;		$line_end_x += $heading_width5[$i];
			
			$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
		} // end for
		//====================================================

		if(($pdf->h - $max_y - 10) < 15){ 
			$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
			if($data_count < $COUNT_IPIP){
				$pdf->AddPage();
				print_header(5);
				$max_y = $pdf->y;
			} // end if
		}else{
			$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
		} // end if

		$pdf->x = $start_x;			$pdf->y = $max_y;
	} // end foreach

	$pdf->SetFont($font,'',14);
	$pdf->SetDrawColor(hexdec("00"),hexdec("66"),hexdec("CC"));
	$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(200, 5, "", 0, 1, 'C', 0);
	$pdf->Cell(200, 7, "ŧ�����Ѻ��Һ�š�û����Թ���Ἱ�Ѳ�Ҽš�û�Ժѵԧҹ��ºؤ��", 0, 1, 'L', 1);

	$pdf->Cell(200, 3, "", 0, 1, 'C', 0);
	$pdf->Cell(100, 7, ("ŧ���� ". str_repeat(".", 70) ." ����Ѻ��û����Թ"), 0, 0, 'C', 0);
	$pdf->Cell(100, 7, ("ŧ���� ". str_repeat(".", 70) ." ���ѧ�Ѻ�ѭ�Ҫ�鹵�"), 0, 1, 'C', 0);

//	$pdf->Cell(100, 7, ("( ". str_repeat(".", 90) ." )"), 0, 0, 'C', 0);
//	$pdf->Cell(100, 7, ("( ". str_repeat(".", 90) ." )"), 0, 1, 'C', 0);
	$pdf->Cell(100, 7, ("( $PER_NAME )"), 0, 0, 'C', 0);
	$pdf->Cell(100, 7, ("( $REVIEW_PER_NAME )"), 0, 1, 'C', 0);

	$pdf->Cell(100, 7, ("�ѹ��� ". str_repeat(".", 13) ." ��͹ ". str_repeat(".", 30) ." �.�. ". str_repeat(".", 20)), 0, 0, 'C', 0);
	$pdf->Cell(100, 7, ("�ѹ��� ". str_repeat(".", 13) ." ��͹ ". str_repeat(".", 30) ." �.�. ". str_repeat(".", 20)), 0, 1, 'C', 0);

	$pdf->Cell(200, 3, "", 0, 1, 'C', 0);
	$pdf->SetFont($font,'',14);
	$pdf->SetDrawColor(hexdec("00"),hexdec("66"),hexdec("CC"));
	$pdf->SetFillColor(hexdec("99"),hexdec("CC"),hexdec("FF"));
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(200, 7, "��ǹ��� �  ������繢ͧ���ѧ�Ѻ�ѭ���˹�͢���", 1, 1, 'L', 1);

	$pdf->SetFont($font,'',14);
	$pdf->SetDrawColor(hexdec("00"),hexdec("66"),hexdec("CC"));
	$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(200, 3, "", 0, 1, 'C', 0);
	$pdf->Cell(200, 7, "������繢ͧ���ѧ�Ѻ�ѭ���˹�͢���", 1, 1, 'L', 1);
	$pdf->Cell(200, 7, "��繴��¡Ѻ�š�û����Թ���Ἱ�Ѳ��� �ѧ���", "LTR", 1, 'L', 0);
	$pdf->SetFont($font,'',12);
	$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
	$pdf->Cell(200, 15, "", "LR", 1, 'L', 0);
	$save_x = $pdf->x;		$save_y = $pdf->y;
	$pdf->x += 5;			$pdf->y -= 15;
	$pdf->MultiCell(190, 5, convert2thaidigit($AGREE_REVIEW1), "", "L");
	$pdf->x = $save_x;		$pdf->y = $save_y;
	$pdf->SetFont($font,'',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(200, 7, "�դ������ᵡ��ҧ �ѧ���", "LR", 1, 'L', 0);
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
	$pdf->Cell(100, 5, ("ŧ���� ". str_repeat(".", 100)), "R", 1, 'C', 0);

	$pdf->Cell(100, 5, "", "L", 0, 'C', 0);
//	$pdf->Cell(100, 5, ("( ". str_repeat(".", 90) ." )"), "R", 1, 'C', 0);
	$pdf->Cell(100, 5, ("( $REVIEW_PER_NAME1 )"), "R", 1, 'C', 0);

	$pdf->Cell(100, 5, "", "L", 0, 'C', 0);
//	$pdf->Cell(100, 5, ("���˹� ". str_repeat(".", 80)), "R", 1, 'C', 0);
	$pdf->Cell(100, 5, ("���˹� $REVIEW_PL_NAME1"), "R", 1, 'C', 0);

	$pdf->Cell(100, 5, "", "LB", 0, 'C', 0);
	$pdf->Cell(100, 5, ("�ѹ��� ". str_repeat(".", 13) ." ��͹ ". str_repeat(".", 30) ." �.�. ". str_repeat(".", 20)), "BR", 1, 'C', 0);

	$pdf->SetFont($font,'',14);
	$pdf->SetDrawColor(hexdec("00"),hexdec("66"),hexdec("CC"));
	$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(200, 3, "", 0, 1, 'C', 0);
	$pdf->Cell(200, 7, "������繢ͧ���ѧ�Ѻ�ѭ���˹�͢����ա���˹��", 1, 1, 'L', 1);
	$pdf->Cell(200, 7, "��繴��¡Ѻ�š�û����Թ���Ἱ�Ѳ��� �ѧ���", "LTR", 1, 'L', 0);
	$pdf->SetFont($font,'',12);
	$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
	$pdf->Cell(200, 15, "", "LR", 1, 'L', 0);
	$save_x = $pdf->x;		$save_y = $pdf->y;
	$pdf->x += 5;			$pdf->y -= 15;
	$pdf->MultiCell(190, 5, convert2thaidigit($AGREE_REVIEW2), "", "L");
	$pdf->x = $save_x;		$pdf->y = $save_y;
	$pdf->SetFont($font,'',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(200, 7, "�դ������ᵡ��ҧ �ѧ���", "LR", 1, 'L', 0);
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
	$pdf->Cell(100, 5, ("ŧ���� ". str_repeat(".", 100)), "R", 1, 'C', 0);

	$pdf->Cell(100, 5, "", "L", 0, 'C', 0);
//	$pdf->Cell(100, 5, ("( ". str_repeat(".", 90) ." )"), "R", 1, 'C', 0);
	$pdf->Cell(100, 5, ("( $REVIEW_PER_NAME2 )"), "R", 1, 'C', 0);

	$pdf->Cell(100, 5, "", "L", 0, 'C', 0);
//	$pdf->Cell(100, 5, ("���˹� ". str_repeat(".", 80)), "R", 1, 'C', 0);
	$pdf->Cell(100, 5, ("���˹� $REVIEW_PL_NAME2"), "R", 1, 'C', 0);

	$pdf->Cell(100, 5, "", "LB", 0, 'C', 0);
	$pdf->Cell(100, 5, ("�ѹ��� ". str_repeat(".", 13) ." ��͹ ". str_repeat(".", 30) ." �.�. ". str_repeat(".", 20)), "BR", 1, 'C', 0);

	$pdf->Close();
	$pdf->Output();

	ini_set("max_execution_time", 30);
?>