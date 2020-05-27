<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 50;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$UPDATE_DATE = date("Y-m-d H:i:s");

	if(!$PER_ID_REVIEW) $PER_ID_REVIEW = "NULL";
	if(!$PER_ID_REVIEW1) $PER_ID_REVIEW1 = "NULL";
	if(!$PER_ID_REVIEW2) $PER_ID_REVIEW2 = "NULL";

	if($command=="UPDATE" && $EP_ID){
		$cmd = " UPDATE EAF_PERSONAL SET
							PER_ID_REVIEW=$PER_ID_REVIEW, AGREE_REVIEW='$AGREE_REVIEW', DIFF_REVIEW='$DIFF_REVIEW',
							PER_ID_REVIEW1=$PER_ID_REVIEW1, AGREE_REVIEW1='$AGREE_REVIEW1', DIFF_REVIEW1='$DIFF_REVIEW1',
							PER_ID_REVIEW2=$PER_ID_REVIEW2, AGREE_REVIEW2='$AGREE_REVIEW2', DIFF_REVIEW2='$DIFF_REVIEW2',
							UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE'
						WHERE EP_ID=$EP_ID ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > บันทึกความเห็นของผู้บังคับบัญชา [$EP_ID : $PER_ID_REVIEW : $PER_ID_REVIEW1 : $PER_ID_REVIEW2]");
	} // end if
		
	$cmd = " select code, name_th, group_level, pv_code, org_id from user_group where id=$SESS_USERGROUP ";
	$db->send_cmd($cmd);
//	$db->show_error();
	$data = $db->get_array();
	$SESS_GROUPCODE = $data[code];

	$cmd = "	SELECT 	PER_ID_REVIEW, AGREE_REVIEW, DIFF_REVIEW,
									PER_ID_REVIEW1, AGREE_REVIEW1, DIFF_REVIEW1,
									PER_ID_REVIEW2, AGREE_REVIEW2, DIFF_REVIEW2
					FROM		EAF_PERSONAL 
					WHERE	EP_ID=$EP_ID ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$PER_ID_REVIEW = $data[PER_ID_REVIEW];
	$AGREE_REVIEW = $data[AGREE_REVIEW];
	$DIFF_REVIEW = $data[DIFF_REVIEW];

	$PER_ID_REVIEW1 = $data[PER_ID_REVIEW1];
	$AGREE_REVIEW1 = $data[AGREE_REVIEW1];
	$DIFF_REVIEW1 = $data[DIFF_REVIEW1];

	$PER_ID_REVIEW2 = $data[PER_ID_REVIEW2];
	$AGREE_REVIEW2 = $data[AGREE_REVIEW2];
	$DIFF_REVIEW2 = $data[DIFF_REVIEW2];

	if($SESS_USERGROUP == 1){
		$USER_REVIEW_AUTH = TRUE;
	}elseif((($SESS_GROUPCODE == "BUREAU" || substr($SESS_GROUPCODE, 0, 7) == "BUREAU_" ) || $SESS_USERGROUP==3) && $PER_ID_REVIEW==$SESS_PER_ID && !$AGREE_REVIEW1 && !$DIFF_REVIEW1){
		$USER_REVIEW_AUTH = TRUE;
	}else{
		$USER_REVIEW_AUTH = FALSE;
	} // end if

	if($SESS_USERGROUP == 1){
		$USER_REVIEW_AUTH1 = TRUE;
	}elseif((($SESS_GROUPCODE == "BUREAU" || substr($SESS_GROUPCODE, 0, 7) == "BUREAU_" ) || $SESS_USERGROUP==3) && $PER_ID_REVIEW1==$SESS_PER_ID && !$AGREE_REVIEW2 && !$DIFF_REVIEW2){
		$USER_REVIEW_AUTH1 = TRUE;
	}else{
		$USER_REVIEW_AUTH1 = FALSE;
	} // end if

	if($SESS_USERGROUP == 1){
		$USER_REVIEW_AUTH2 = TRUE;
	}elseif((($SESS_GROUPCODE == "BUREAU" || substr($SESS_GROUPCODE, 0, 7) == "BUREAU_" ) || $SESS_USERGROUP==3) && $PER_ID_REVIEW2==$SESS_PER_ID){
		$USER_REVIEW_AUTH2 = TRUE;
	}else{
		$USER_REVIEW_AUTH2 = FALSE;
	} // end if

	$cmd = " select 	PN_CODE, PER_NAME, PER_SURNAME, PER_TYPE, POS_ID, POEM_ID, POEMS_ID, LEVEL_NO, DEPARTMENT_ID
					 from		PER_PERSONAL
					 where	PER_ID=$PER_ID_REVIEW
				   ";
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
	$REVIEW_DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);

	$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='$REVIEW_PN_CODE' ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$REVIEW_PN_NAME = trim($data[PN_NAME]);
	
	$REVIEW_PER_NAME = $REVIEW_PN_NAME . $REVIEW_PER_NAME . " " . $REVIEW_PER_SURNAME;
		
	$cmd = " select LEVEL_NAME from PER_LEVEL where trim(LEVEL_NO)='$REVIEW_LEVEL_NO' ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$REVIEW_LEVEL_NAME = trim($data[LEVEL_NAME]);
		
	$cmd = " select 	b.PL_NAME, a.PT_CODE
					 from 		PER_POSITION a, PER_LINE b
					 where	a.POS_ID=$REVIEW_POS_ID and a.PL_CODE=b.PL_CODE ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$REVIEW_PL_NAME = trim($data[PL_NAME]);
	$REVIEW_PT_CODE = trim($data[PT_CODE]);
	$REVIEW_PT_NAME = trim($data[PT_NAME]);
	$REVIEW_PL_NAME = (trim($REVIEW_PM_NAME) ?"$REVIEW_PM_NAME (":"") . (trim($REVIEW_PL_NAME)? "$REVIEW_PL_NAME $REVIEW_LEVEL_NAME" : "") . (trim($REVIEW_PM_NAME) ?")":"");

	$cmd = " select ORG_ID_REF, ORG_NAME from PER_ORG where ORG_ID=$REVIEW_DEPARTMENT_ID ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$REVIEW_DEPARTMENT_NAME = $data[ORG_NAME];
	$REVIEW_MINISTRY_ID = $data[ORG_ID_REF];

	$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$REVIEW_MINISTRY_ID ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$REVIEW_MINISTRY_NAME = $data[ORG_NAME];

	$cmd = " select 	PN_CODE, PER_NAME, PER_SURNAME, PER_TYPE, POS_ID, POEM_ID, POEMS_ID, LEVEL_NO, DEPARTMENT_ID
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
	$REVIEW_DEPARTMENT_ID1 = trim($data[DEPARTMENT_ID]);

	$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='$REVIEW_PN_CODE1' ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$REVIEW_PN_NAME1 = trim($data[PN_NAME]);
	
	$REVIEW_PER_NAME1 = $REVIEW_PN_NAME1 . $REVIEW_PER_NAME1 . " " . $REVIEW_PER_SURNAME1;
		
	$cmd = " select LEVEL_NAME from PER_LEVEL where trim(LEVEL_NO)='$REVIEW_LEVEL_NO1' ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$REVIEW_LEVEL_NAME1 = trim($data[LEVEL_NAME]);
		
	$cmd = " select 	b.PL_NAME, a.PT_CODE
					 from 		PER_POSITION a, PER_LINE b
					 where	a.POS_ID=$REVIEW_POS_ID1 and a.PL_CODE=b.PL_CODE ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$REVIEW_PL_NAME1 = trim($data[PL_NAME]);
	$REVIEW_PT_CODE1 = trim($data[PT_CODE]);
	$REVIEW_PT_NAME1 = trim($data[PT_NAME]);
	$REVIEW_PL_NAME1 = (trim($REVIEW_PM_NAME1) ?"$REVIEW_PM_NAME1 (":"") . (trim($REVIEW_PL_NAME1)? "$REVIEW_PL_NAME1 $REVIEW_LEVEL_NAME1" : "") . (trim($REVIEW_PM_NAME1) ?")":"");

	$cmd = " select ORG_ID_REF, ORG_NAME from PER_ORG where ORG_ID=$REVIEW_DEPARTMENT_ID1 ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$REVIEW_DEPARTMENT_NAME1 = $data[ORG_NAME];
	$REVIEW_MINISTRY_ID1 = $data[ORG_ID_REF];

	$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$REVIEW_MINISTRY_ID1 ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$REVIEW_MINISTRY_NAME1 = $data[ORG_NAME];

	$cmd = " select 	PN_CODE, PER_NAME, PER_SURNAME, PER_TYPE, POS_ID, POEM_ID, POEMS_ID, LEVEL_NO, DEPARTMENT_ID
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
	$REVIEW_DEPARTMENT_ID2 = trim($data[DEPARTMENT_ID]);

	$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='$REVIEW_PN_CODE2' ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$REVIEW_PN_NAME2 = trim($data[PN_NAME]);
	
	$REVIEW_PER_NAME2 = $REVIEW_PN_NAME2 . $REVIEW_PER_NAME2 . " " . $REVIEW_PER_SURNAME2;
	
	$cmd = " select LEVEL_NAME from PER_LEVEL where trim(LEVEL_NO)='$REVIEW_LEVEL_NO2' ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$REVIEW_LEVEL_NAME2 = trim($data[LEVEL_NAME]);
		
	$cmd = " select 	b.PL_NAME, a.PT_CODE
					 from 		PER_POSITION a, PER_LINE b
					 where	a.POS_ID=$REVIEW_POS_ID2 and a.PL_CODE=b.PL_CODE ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$REVIEW_PL_NAME2 = trim($data[PL_NAME]);
	$REVIEW_PT_CODE2 = trim($data[PT_CODE]);
	$REVIEW_PT_NAME2 = trim($data[PT_NAME]);
	$REVIEW_PL_NAME2 = (trim($REVIEW_PM_NAME2) ?"$REVIEW_PM_NAME2 (":"") . (trim($REVIEW_PL_NAME2)? "$REVIEW_PL_NAME2 $REVIEW_LEVEL_NAME2" : "") . (trim($REVIEW_PM_NAME2) ?")":"");

	$cmd = " select ORG_ID_REF, ORG_NAME from PER_ORG where ORG_ID=$REVIEW_DEPARTMENT_ID2 ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$REVIEW_DEPARTMENT_NAME2 = $data[ORG_NAME];
	$REVIEW_MINISTRY_ID2 = $data[ORG_ID_REF];

	$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$REVIEW_MINISTRY_ID2 ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$REVIEW_MINISTRY_NAME2 = $data[ORG_NAME];
?>