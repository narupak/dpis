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
	
	$cmd = " select 	PG_CYCLE, PG_START_DATE, PG_END_DATE, PER_ID, PER_CARDNO, PER_ID_REVIEW, 
									PER_ID_REVIEW1, IPIP_DESC, CONCLUSION_DESC, CONCLUSION_REVIEW,
									OUTSTANDING_PERFORMANCE, OUTSTANDING_GOODNESS, IPIP_REVIEW						
			   		 from 		PER_PERFORMANCE_GOODNESS
			   		 where 	PG_ID=$PG_ID ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$PG_CYCLE = trim($data[PG_CYCLE]);
	$PG_START_DATE = substr($data[PG_START_DATE], 0, 10);
	if($PG_START_DATE){
		$PG_START_DATE = show_date_format($PG_START_DATE,$DATE_DISPLAY);
	} // end if
	$PG_END_DATE = substr($data[PG_END_DATE], 0, 10);
	$PG_YEAR = substr($PG_END_DATE, 0, 4) + 543;
	if($PG_END_DATE){
		$PG_END_DATE = show_date_format($PG_END_DATE,$DATE_DISPLAY);
	} // end if
	$BUDGET_YEAR = $PG_YEAR - 543;
	
	$PER_ID = $data[PER_ID];
	$PER_CARDNO = trim($data[PER_CARDNO]);		
	$PER_ID_REVIEW = $data[PER_ID_REVIEW];
	$PER_ID_REVIEW1 = $data[PER_ID_REVIEW1];
	
	$IPIP_DESC = $data[IPIP_DESC];
	$CONCLUSION_DESC = $data[CONCLUSION_DESC];
	$CONCLUSION_REVIEW = $data[CONCLUSION_REVIEW];
	$OUTSTANDING_PERFORMANCE = $data[OUTSTANDING_PERFORMANCE];
	$OUTSTANDING_GOODNESS = $data[OUTSTANDING_GOODNESS];
	$IPIP_REVIEW = $data[IPIP_REVIEW];

	$cmd = " select 	PN_CODE, PER_NAME, PER_SURNAME, LEVEL_NO, PER_SALARY, PER_TYPE, POS_ID, POEM_ID, POEMS_ID, POT_ID
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
	$POT_ID = trim($data[POT_ID]);
	
	$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='$PN_CODE' ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$PN_NAME = trim($data[PN_NAME]);
	
	$PER_NAME = $PN_NAME . $PER_NAME . " " . $PER_SURNAME;
	
	$cmd = " select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where trim(LEVEL_NO)='$LEVEL_NO' ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$LEVEL_NAME = trim($data[LEVEL_NAME]);
	$POSITION_LEVEL = $data[POSITION_LEVEL];
	if (!$POSITION_LEVEL) $POSITION_LEVEL = $LEVEL_NAME;

	if($PER_TYPE==1){
		$cmd = " select 	b.PL_NAME, c.ORG_NAME, a.PT_CODE 
						 from 		PER_POSITION a, PER_LINE b, PER_ORG c
						 where	a.POS_ID=$POS_ID and a.PL_CODE=b.PL_CODE and a.ORG_ID=c.ORG_ID ";
// ไม่ถูก		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PL_NAME = trim($data[PL_NAME]);
		$ORG_NAME = trim($data[ORG_NAME]);
		$PT_CODE = trim($data[PT_CODE]);
		$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE)='$PT_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$PT_NAME = trim($data2[PT_NAME]);
		$PL_NAME = trim($PL_NAME)?($PL_NAME . $POSITION_LEVEL . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"")):$LEVEL_NAME;
	}elseif($PER_TYPE==2){
		$cmd = " select 	b.PN_NAME, c.ORG_NAME 
						 from 		PER_POS_EMP a, PER_POS_NAME b, PER_ORG c
						 where	a.POEM_ID=$POEM_ID and a.PN_CODE=b.PN_CODE and a.ORG_ID=c.ORG_ID ";
// ไม่ถูก		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PL_NAME = trim($data[PN_NAME]);
		$ORG_NAME = trim($data[ORG_NAME]);
	}elseif($PER_TYPE==3){
		$cmd = " select 	b.EP_NAME, c.ORG_NAME 
						 from 		PER_POS_EMPSER a, PER_EMPSER_POS_NAME b, PER_ORG c
						 where	a.POEMS_ID=$POEMS_ID and a.EP_CODE=b.EP_CODE and a.ORG_ID=c.ORG_ID ";
// ไม่ถูก		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PL_NAME = trim($data[EP_NAME]);
		$ORG_NAME = trim($data[ORG_NAME]);
	} elseif($PER_TYPE==4){
		$cmd = " select 	b.TP_NAME, c.ORG_NAME 
						 from 		PER_POS_TEMP a, PER_TEMP_POS_NAME b, PER_ORG c
						 where	a.POT_ID=$POT_ID and a.TP_CODE=b.TP_CODE and a.ORG_ID=c.ORG_ID ";
// ไม่ถูก		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PL_NAME = trim($data[TP_NAME]);
		$ORG_NAME = trim($data[ORG_NAME]);
	} // end if

	$cmd = " select 	PN_CODE, PER_NAME, PER_SURNAME, PER_TYPE, POS_ID, POEM_ID, POEMS_ID,POT_ID, LEVEL_NO
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
	$REVIEW_POT_ID = trim($data[POT_ID]);
	$REVIEW_LEVEL_NO = trim($data[LEVEL_NO]);

	$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='$REVIEW_PN_CODE' ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$REVIEW_PN_NAME = trim($data[PN_NAME]);
	
	$REVIEW_PER_NAME = $REVIEW_PN_NAME . $REVIEW_PER_NAME . " " . $REVIEW_PER_SURNAME;
	
	$cmd = " select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where trim(LEVEL_NO)='$REVIEW_LEVEL_NO' ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$REVIEW_LEVEL_NAME = trim($data[LEVEL_NAME]);
	$REVIEW_POSITION_LEVEL = $data[POSITION_LEVEL];
	if (!$REVIEW_POSITION_LEVEL) $REVIEW_POSITION_LEVEL = $REVIEW_LEVEL_NAME;

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
	} elseif($REVIEW_PER_TYPE==4){
		$cmd = " select 	b.TP_NAME
						 from 		PER_POS_TEMP a, PER_TEMP_POS_NAME b
						 where	a.POT_ID=$REVIEW_POT_ID and a.TP_CODE=b.TP_CODE ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$REVIEW_PL_NAME = trim($data[TP_NAME]);
	} // end if
	
	$cmd = " select 	PN_CODE, PER_NAME, PER_SURNAME, PER_TYPE, POS_ID, POEM_ID, POEMS_ID,POT_ID, LEVEL_NO
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
	$REVIEW_POT_ID1 = trim($data[POT_ID]);
	$REVIEW_LEVEL_NO1 = trim($data[LEVEL_NO]);

	$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='$REVIEW_PN_CODE1' ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$REVIEW_PN_NAME1 = trim($data[PN_NAME]);
	
	$REVIEW_PER_NAME1 = $REVIEW_PN_NAME1 . $REVIEW_PER_NAME1 . " " . $REVIEW_PER_SURNAME1;
	
	$cmd = " select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where trim(LEVEL_NO)='$REVIEW_LEVEL_NO1' ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$REVIEW_LEVEL_NAME1 = trim($data[LEVEL_NAME]);
	$REVIEW_POSITION_LEVEL1 = $data[POSITION_LEVEL];
	if (!$REVIEW_POSITION_LEVEL1) $REVIEW_POSITION_LEVEL1 = $REVIEW_LEVEL_NAME1;

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
	} elseif($REVIEW_PER_TYPE1==4){
		$cmd = " select 	b.TP_NAME
						 from 		PER_POS_TEMP a, PER_TEMP_POS_NAME b
						 where	a.POT_ID=$REVIEW_POT_ID1 and a.TP_CODE=b.TP_CODE ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$REVIEW_PL_NAME1 = trim($data[TP_NAME]);
	} // end if

	$search_condition = "";
	unset($arr_search_condition);
	if($DPISDB=="odbc")	$arr_search_condition[] = "(LEFT(trim(POH_EFFECTIVEDATE, 10)) >= '".($BUDGET_YEAR - 6)."-10-01')";
	elseif($DPISDB=="oci8")	$arr_search_condition[] = "(SUBSTR(trim(POH_EFFECTIVEDATE), 1, 10) >= '".($BUDGET_YEAR - 6)."-10-01')";
	elseif($DPISDB=="mysql")	$arr_search_condition[] = "(LEFT(trim(POH_EFFECTIVEDATE, 10)) >= '".($BUDGET_YEAR - 6)."-10-01')";
	if(count($arr_search_condition)) $search_condition = " and ". implode(" and ", $arr_search_condition);

	$cmd = " SELECT 		POH_ID, POH_EFFECTIVEDATE, POH_ENDDATE, POH_ORG2, POH_ORG3, 
											PL_CODE, PN_CODE, EP_CODE, LEVEL_NO, POH_POS_NO    
					 FROM			PER_POSITIONHIS 
					 WHERE		PER_ID=$PER_ID 
											$search_condition
					 ORDER BY	POH_EFFECTIVEDATE desc ";
	$db_dpis->send_cmd($cmd);
	while($data = $db_dpis->get_array()){
		$POH_ID = $data[POH_ID];
		$POH_EFFECTIVEDATE = show_date_format($data[POH_EFFECTIVEDATE],$DATE_DISPLAY);
		$POH_ENDDATE = show_date_format($data[POH_ENDDATE],$DATE_DISPLAY);
		$POH_LEVEL_NO = $data[LEVEL_NO];
		$POH_POS_NO = (trim($data[POH_POS_NO]))?   $data[POH_POS_NO] : "-";
		
		$POH_ORG_NAME_2 = $data[POH_ORG2];
		$POH_ORG_NAME_3 = $data[POH_ORG3];		
		
		$POH_PL_CODE = $data[PL_CODE];
		$POH_PN_CODE = $data[PN_CODE];
		$POH_EP_CODE = $data[EP_CODE];
		if($PER_TYPE==1){
			$cmd = " select PL_NAME from PER_LINE where PL_CODE='$POH_PL_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$POH_PL_NAME = $data2[PL_NAME];
		} elseif($PER_TYPE==2){
			$cmd = " select PN_NAME from PER_POS_NAME where PN_CODE='$POH_PN_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$POH_PL_NAME = $data2[PN_NAME];	
		} elseif($PER_TYPE==3){
			$cmd = " select EP_NAME from PER_POS_EMPSER where EP_CODE='$POH_EP_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$POH_PL_NAME = $data2[EP_NAME];		
		} // end if
		
		$ARR_POSITIONHIS[$POH_ID] = $POH_POS_NO;
		$ARR_POSITIONHIS_NAME[$POH_ID] = $POH_PL_NAME;
		$ARR_POSITIONHIS_EFFECTIVEDATE[$POH_ID] = $POH_EFFECTIVEDATE;
		$ARR_POSITIONHIS_ENDDATE[$POH_ID] = $POH_ENDDATE;
		$ARR_POSITIONHIS_ORG2[$POH_ID] = $POH_ORG_NAME_2;
		$ARR_POSITIONHIS_ORG3[$POH_ID] = $POH_ORG_NAME_3;
	} // end while
	
	$COUNT_POSITIONHIS = count($ARR_POSITIONHIS);

	$search_condition = "";
	unset($arr_search_condition);
	if($DPISDB=="odbc")	$arr_search_condition[] = "(LEFT(trim(SAH_EFFECTIVEDATE, 10)) >= '".($BUDGET_YEAR - 6)."-10-01')";
	elseif($DPISDB=="oci8")	$arr_search_condition[] = "(SUBSTR(trim(SAH_EFFECTIVEDATE), 1, 10) >= '".($BUDGET_YEAR - 6)."-10-01')";
	elseif($DPISDB=="mysql")	$arr_search_condition[] = "(LEFT(trim(SAH_EFFECTIVEDATE, 10)) >= '".($BUDGET_YEAR - 6)."-10-01')";
	if(count($arr_search_condition)) $search_condition = " and ". implode(" and ", $arr_search_condition);

	$cmd = " SELECT 		SAH_ID, SAH_EFFECTIVEDATE, pm.MOV_NAME, SAH_SALARY, SAH_DOCNO, SAH_ENDDATE 
					 FROM			PER_SALARYHIS psh, PER_MOVMENT pm 
					 WHERE		psh.PER_ID=$PER_ID and psh.MOV_CODE=pm.MOV_CODE 
											$search_condition
					ORDER BY	SAH_EFFECTIVEDATE desc, SAH_SALARY desc ";	
	$db_dpis->send_cmd($cmd);
	while($data = $db_dpis->get_array()){
		$SAH_ID = $data[SAH_ID];
		$SAH_EFFECTIVEDATE = show_date_format($data[SAH_EFFECTIVEDATE],$DATE_DISPLAY);
		$SAH_ENDDATE = show_date_format($data[SAH_ENDDATE],$DATE_DISPLAY);
		$SAH_SALARY = number_format($data[SAH_SALARY], 2, '.', ',');
		$SAH_MOV_NAME = $data[MOV_NAME];
		$SAH_DOCNO = (trim($data[SAH_DOCNO]))? $data[SAH_DOCNO] : "-" ;	
		
		$ARR_SALARYHIS[$SAH_ID] = $SAH_MOV_NAME;
		$ARR_SALARYHIS_SALARY[$SAH_ID] = $SAH_SALARY;
		$ARR_SALARYHIS_DOCNO[$SAH_ID] = $SAH_DOCNO;
		$ARR_SALARYHIS_EFFECTIVEDATE[$SAH_ID] = $SAH_EFFECTIVEDATE;
		$ARR_SALARYHIS_ENDDATE[$SAH_ID] = $SAH_ENDDATE;
	} // end while

	$COUNT_SALARYHIS = count($ARR_SALARYHIS);

	$search_condition = "";
	unset($arr_search_condition);
//	if($DPISDB=="odbc")	$arr_search_condition[] = "(LEFT(trim(REH_DATE, 10)) >= '".($BUDGET_YEAR - 6)."-10-01')";
//	elseif($DPISDB=="oci8")	$arr_search_condition[] = "(SUBSTR(trim(REH_DATE), 1, 10) >= '".($BUDGET_YEAR - 6)."-10-01')";
//  elseif($DPISDB=="mysql")	$arr_search_condition[] = "(LEFT(trim(REH_DATE, 10)) >= '".($BUDGET_YEAR - 6)."-10-01')";
	if(count($arr_search_condition)) $search_condition = " and ". implode(" and ", $arr_search_condition);

	$cmd = " SELECT		top $data_per_page
											REH_ID, prh.REW_CODE, pr.REW_NAME, REH_ORG, REH_DOCNO, REH_DATE 
					 FROM			PER_REWARDHIS prh, PER_REWARD pr
					 WHERE		prh.PER_ID=$PER_ID and prh.REW_CODE=pr.REW_CODE 
					ORDER BY	REH_DATE desc ";	
	$db_dpis->send_cmd($cmd);
	while($data = $db_dpis->get_array()){
		$REH_ID = $data[REH_ID];
		$REH_DOCNO = (trim($data[REH_DOCNO]))? $data[REH_DOCNO] : "-" ;
		$REH_ORG = (trim($data[REH_ORG]))? $data[REH_ORG] : "-" ;
		$REH_DATE = show_date_format($data[REH_DATE],$DATE_DISPLAY);
		$REW_NAME = $data[REW_NAME];
		
		$ARR_REWARDHIS[$REH_ID] = $REW_NAME;
		$ARR_REWARDHIS_DATE[$REH_ID] = $REH_DATE;
		$ARR_REWARDHIS_ORG[$REH_ID] = $REH_ORG;
		$ARR_REWARDHIS_DOCNO[$REH_ID] = $REH_DOCNO;
	} // end while

	$COUNT_REWARDHIS = count($ARR_REWARDHIS);

	$cmd = " select 	a.PD_ID, b.PF_NAME, a.PERFORMANCE_DESC
				   	 from 		PER_PERFORMANCE_DTL a, PER_PERFORMANCE b
				   	 where 	a.PG_ID=$PG_ID and a.PF_CODE=b.PF_CODE
				   	 order by	a.PD_ID";
	$db_dpis->send_cmd($cmd);
	while($data = $db_dpis->get_array()){
		$PD_ID = $data[PD_ID];
		$PF_NAME = $data[PF_NAME];
		$PERFORMANCE_DESC = $data[PERFORMANCE_DESC];
		
		$ARR_PERFORMANCE[$PD_ID] = $PF_NAME;
		$ARR_PERFORMANCE_DESC[$PD_ID] = $PERFORMANCE_DESC;
	} // end while
	
	$COUNT_PERFORMANCE = count($ARR_PERFORMANCE);

	$cmd = " select 	a.GD_ID, b.GN_NAME, a.GOODNESS_DESC
				   	 from 		PER_GOODNESS_DTL a, PER_GOODNESS b
				   	 where 	a.PG_ID=$PG_ID and a.GN_CODE=b.GN_CODE
				   	 order by	a.GD_ID";
	$db_dpis->send_cmd($cmd);
	while($data = $db_dpis->get_array()){
		$GD_ID = $data[GD_ID];
		$GN_NAME = $data[GN_NAME];
		$GOODNESS_DESC = $data[GOODNESS_DESC];
		
		$ARR_GOODNESS[$GD_ID] = $GN_NAME;
		$ARR_GOODNESS_DESC[$GD_ID] = $GOODNESS_DESC;
	} // end while
	
	$COUNT_GOODNESS = count($ARR_GOODNESS);

	$cmd = " select 	a.GD_ID, b.GN_NAME, a.GOODNESS_DESC
				   	 from 		PER_GOODNESS_DTL a, PER_GOODNESS b
				   	 where 	a.PG_ID=$PG_ID and a.GN_CODE=b.GN_CODE
				   	 order by	a.GD_ID";
	$db_dpis->send_cmd($cmd);
	while($data = $db_dpis->get_array()){
		$GD_ID = $data[GD_ID];
		$GN_NAME = $data[GN_NAME];
		$GOODNESS_DESC = $data[GOODNESS_DESC];
		
		$ARR_GOODNESS[$GD_ID] = $GN_NAME;
		$ARR_GOODNESS_DESC[$GD_ID] = $GOODNESS_DESC;
	} // end while
	
	$COUNT_GOODNESS = count($ARR_GOODNESS);

	$cmd = " select 	TD_ID, TD_SEQ, TRAINING_DESC
				   	 from 		PER_TRAINING_DTL
				   	 where 	PG_ID=$PG_ID
				   	 order by	TD_SEQ";
	$db_dpis->send_cmd($cmd);
	while($data = $db_dpis->get_array()){
		$TD_ID = $data[TD_ID];
		$TD_SEQ = $data[TD_SEQ];
		$TRAINING_DESC = $data[TRAINING_DESC];
		
		$ARR_TRAINING[$TD_ID] = $TD_SEQ;
		$ARR_TRAINING_DESC[$TD_ID] = $TRAINING_DESC;
	} // end while
	
	$COUNT_TRAINING = count($ARR_TRAINING);

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
	
	$heading_width1[0] = "10";
	$heading_width1[1] = "140";
	$heading_width1[2] = "25";
	$heading_width1[3] = "25";
	
	$heading_width2[0] = "10";
	$heading_width2[1] = "25";
	$heading_width2[2] = "90";
	$heading_width2[3] = "25";
	$heading_width2[4] = "25";
	$heading_width2[5] = "25";
	
	$heading_width3[0] = "10";
	$heading_width3[1] = "80";
	$heading_width3[2] = "80";
	$heading_width3[3] = "30";

	$heading_width4[0] = "10";
	$heading_width4[1] = "90";
	$heading_width4[2] = "100";

	$heading_width5[0] = "10";
	$heading_width5[1] = "90";
	$heading_width5[2] = "100";

	$heading_width6[0] = "10";
	$heading_width6[1] = "190";

	function print_header($header_select){
		global $pdf, $heading_width1, $heading_width2, $heading_width3, $heading_width5;
		
		if($header_select == 1){
			$pdf->SetFont($font,'',14);
			$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
			$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	
			$pdf->Cell($heading_width1[0] ,14,"ลำดับที่",'LTBR',0,'C',1);
			$pdf->Cell($heading_width1[1] ,14,"ตำแหน่ง",'LTBR',0,'C',1);
			$pdf->Cell($heading_width1[2] ,7, "วันที่เข้า",'LTR',0,'C',1);
			$pdf->Cell($heading_width1[3] ,7, "วันที่สิ้นสุด",'LTR',1,'C',1);

			$pdf->x += ($heading_width1[0] + $heading_width1[1]);
			$pdf->Cell($heading_width1[2] ,7, "ดำรงตำแหน่ง",'LBR',0,'C',1);
			$pdf->Cell($heading_width1[3] ,7, "การดำรงตำแหน่ง",'LBR',1,'C',1);			
		}elseif($header_select == 2){
			$pdf->SetFont($font,'',14);
			$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
			$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	
			$pdf->Cell($heading_width2[0] ,14,"ลำดับที่",'LTBR',0,'C',1);
			$pdf->Cell($heading_width2[1] ,14,"คำสั่งเลขที่",'LTBR',0,'C',1);
			$pdf->Cell($heading_width2[2] ,14,"ประเภทการเคลื่อนไหว",'LTBR',0,'C',1);
			$pdf->Cell($heading_width2[3] ,14,"อัตราเงินเดือน",'LTBR',0,'C',1);
			$pdf->Cell($heading_width2[4] ,14,"วันที่มีผลบังคับใช้",'LTBR',0,'C',1);
			$pdf->Cell($heading_width2[5] ,14,"ถึงวันที่",'LTBR',1,'C',1);
		}elseif($header_select == 3){
			$pdf->SetFont($font,'',14);
			$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
			$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	
			$pdf->Cell($heading_width3[0] ,14,"ลำดับที่",'LTBR',0,'C',1);
			$pdf->Cell($heading_width3[1] ,14,"รายการความดีความชอบ",'LTBR',0,'C',1);
			$pdf->Cell($heading_width3[2] ,14,"หน่วยงานที่ให้ความดีความชอบ",'LTBR',0,'C',1);
			$pdf->Cell($heading_width3[3] ,14,"วันเดือนปีที่ได้รับ",'LTBR',1,'C',1);
		}elseif($header_select == 4){
			$pdf->SetFont($font,'',14);
			$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
			$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	
			$pdf->Cell($heading_width4[0] ,7,"ลำดับที่",'LTR',0,'C',1);
			$pdf->Cell($heading_width4[1] ,14,"ผลงานที่ได้ดำเนินการ",'LTBR',0,'C',1);
			$pdf->Cell($heading_width4[2] ,14,"รายละเอียดผลงาน",'LTBR',1,'C',1);
		}elseif($header_select == 5){
			$pdf->SetFont($font,'',14);
			$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
			$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	
			$pdf->Cell($heading_width5[0] ,7,"ลำดับที่",'LTR',0,'C',1);
			$pdf->Cell($heading_width5[1] ,14,"พฤติกรรมที่เป็นคุณงามความดี",'LTBR',0,'C',1);
			$pdf->Cell($heading_width5[2] ,14,"รายละเอียดคุณงามความดี",'LTBR',1,'C',1);
		}elseif($header_select == 6){
			$pdf->SetFont($font,'',14);
			$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
			$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	
			$pdf->Cell($heading_width6[0] ,7,"ลำดับที่",'LTR',0,'C',1);
			$pdf->Cell($heading_width6[1] ,14,"หลักสูตรอบรม สัมมนา",'LTBR',1,'C',1);
		} // end if
	} // function		

	$pdf->AutoPageBreak = false;
	
	// หน้าแรก สำหรับผู้บันทึก
	$pdf->Image("../images/krut.jpg", 87.5, 70, 34, 35,"jpg");		// Original size = 101x104 => 34x35

	$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

	$pdf->y = 160;
	$pdf->SetFont($font,'b','',25);
	$pdf->Cell(200, 10, "สมุดบันทึก", 0, 1, 'C', 0);
	$pdf->Cell(200, 10, "ผลงานและคุณงามความดีของข้าราชการ", 0, 1, 'C', 0);

	$pdf->y = 240;
	$pdf->Cell(50, 7, "", 0, 0, 'L', 0);
	$pdf->SetFont($font,'',18);
	$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
	$pdf->Cell(30, 7, "ชื่อข้าราชการ", 0, 0, 'L', 0);
	$pdf->SetFont($font,'',18);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(70, 7, "$PER_NAME", 0, 0, 'L', 0);
	$pdf->Cell(50, 7, "", 0, 1, 'L', 0);

	$pdf->Cell(50, 7, "", 0, 0, 'L', 0);
	$pdf->SetFont($font,'',18);
	$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
	$pdf->Cell(45, 7, "เลขประจำตัวประชาชน", 0, 0, 'L', 0);
	$pdf->SetFont($font,'',18);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(35, 7,card_no_format($PER_CARDNO,$CARD_NO_DISPLAY), 0, 0, 'L', 0);
	$pdf->Cell(50, 7, "", 0, 1, 'L', 0);

	$pdf->Cell(50, 7, "", 0, 0, 'L', 0);
	$pdf->SetFont($font,'',18);
	$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
	$pdf->Cell(30, 7, "$ORG_TITLE", 0, 0, 'L', 0);
	$pdf->SetFont($font,'',18);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(70, 7, "$ORG_NAME", 0, 0, 'L', 0);
	$pdf->Cell(50, 7, "", 0, 1, 'L', 0);

	$pdf->Cell(50, 7, "", 0, 0, 'L', 0);
	$pdf->SetFont($font,'',18);
	$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
	$pdf->Cell(30, 7, "$DEPARTMENT_TITLE", 0, 0, 'L', 0);
	$pdf->SetFont($font,'',18);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(70, 7, "$DEPARTMENT_NAME", 0, 0, 'L', 0);
	$pdf->Cell(50, 7, "", 0, 1, 'L', 0);

	$pdf->Cell(50, 7, "", 0, 0, 'L', 0);
	$pdf->SetFont($font,'',18);
	$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
	$pdf->Cell(30, 7, "$MINISTRY_TITLE", 0, 0, 'L', 0);
	$pdf->SetFont($font,'',18);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(70, 7, "$MINISTRY_NAME", 0, 0, 'L', 0);
	$pdf->Cell(50, 7, "", 0, 1, 'L', 0);

	// หน้าแรก สำหรับผู้ประเมิน
	$pdf->AddPage();
	$pdf->Image("../images/krut.jpg", 87.5, 70, 34, 35,"jpg");		// Original size = 101x104 => 34x35

	$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

	$pdf->y = 160;
	$pdf->SetFont($font,'b','',25);
	$pdf->Cell(200, 10, "บันทึกสำหรับผู้ประเมินเพื่อสรุปผลงาน คุณงามความดี", 0, 1, 'C', 0);
	$pdf->Cell(200, 10, "และพฤติกรรมการทำงานของข้าราชการ", 0, 1, 'C', 0);

	$pdf->y = 240;
	$pdf->Cell(50, 7, "", 0, 0, 'L', 0);
	$pdf->SetFont($font,'',18);
	$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
	$pdf->Cell(30, 7, "ชื่อข้าราชการ", 0, 0, 'L', 0);
	$pdf->SetFont($font,'',18);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(70, 7, "$PER_NAME", 0, 0, 'L', 0);
	$pdf->Cell(50, 7, "", 0, 1, 'L', 0);

	$pdf->Cell(50, 7, "", 0, 0, 'L', 0);
	$pdf->SetFont($font,'',18);
	$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
	$pdf->Cell(45, 7, "เลขประจำตัวประชาชน", 0, 0, 'L', 0);
	$pdf->SetFont($font,'',18);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(35, 7,card_no_format($PER_CARDNO,$CARD_NO_DISPLAY), 0, 0, 'L', 0);
	$pdf->Cell(50, 7, "", 0, 1, 'L', 0);

	$pdf->Cell(50, 7, "", 0, 0, 'L', 0);
	$pdf->SetFont($font,'',18);
	$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
	$pdf->Cell(30, 7, "$ORG_TITLE", 0, 0, 'L', 0);
	$pdf->SetFont($font,'',18);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(70, 7, "$ORG_NAME", 0, 0, 'L', 0);
	$pdf->Cell(50, 7, "", 0, 1, 'L', 0);

	$pdf->Cell(50, 7, "", 0, 0, 'L', 0);
	$pdf->SetFont($font,'',18);
	$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
	$pdf->Cell(30, 7, "$DEPARTMENT_TITLE", 0, 0, 'L', 0);
	$pdf->SetFont($font,'',18);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(70, 7, "$DEPARTMENT_NAME", 0, 0, 'L', 0);
	$pdf->Cell(50, 7, "", 0, 1, 'L', 0);

	$pdf->Cell(50, 7, "", 0, 0, 'L', 0);
	$pdf->SetFont($font,'',18);
	$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
	$pdf->Cell(30, 7, "$MINISTRY_TITLE", 0, 0, 'L', 0);
	$pdf->SetFont($font,'',18);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(70, 7, "$MINISTRY_NAME", 0, 0, 'L', 0);
	$pdf->Cell(50, 7, "", 0, 1, 'L', 0);
	$pdf->Close();
	$pdf->Output();

	ini_set("max_execution_time", 30);
?>