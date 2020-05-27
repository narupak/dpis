<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");	

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 50;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$UPDATE_DATE = date("Y-m-d H:i:s");

	if(!$SUBPAGE) $SUBPAGE = 1;

	$cmd = " select code, name_th, group_level, pv_code, org_id from user_group where id=$SESS_USERGROUP ";
	$db->send_cmd($cmd);
//	$db->show_error();
	$data = $db->get_array();
	$data = array_change_key_case($data, CASE_LOWER);
	$SESS_GROUPCODE = $data[code];
	
	$cmd = " select PER_ID_REVIEW, PER_ID_REVIEW1 from PER_PERFORMANCE_GOODNESS where PG_ID=$PG_ID ";	
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$PER_ID_REVIEW = $data[PER_ID_REVIEW];
	$PER_ID_REVIEW1 = $data[PER_ID_REVIEW1];

	if($SESS_USERGROUP == 1){
		$USER_AUTH = TRUE;
	}elseif((($SESS_GROUPCODE == "BUREAU" || substr($SESS_GROUPCODE, 0, 7) == "BUREAU_" ) || $SESS_USERGROUP==3) && $PER_ID_REVIEW==$SESS_PER_ID){
		$USER_AUTH = TRUE;
	}else{
		$USER_AUTH = FALSE;
	} // end if
	if($PER_ID_REVIEW){
		$cmd = " select 	PN_CODE, PER_NAME, PER_SURNAME, PER_TYPE, POS_ID, POEM_ID, POEMS_ID, LEVEL_NO, DEPARTMENT_ID
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
		$REVIEW_DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);

		$cmd = " select POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$REVIEW_LEVEL_NO' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$REVIEW_POSITION_LEVEL = $data[POSITION_LEVEL];
		
		$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='$REVIEW_PN_CODE' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$REVIEW_PN_NAME = trim($data[PN_NAME]);
		
		$REVIEW_PER_NAME = $REVIEW_PN_NAME . $REVIEW_PER_NAME . " " . $REVIEW_PER_SURNAME;
			
		if($REVIEW_PER_TYPE==1){
			$cmd = " select 	b.PL_NAME, a.PT_CODE
							 from 		PER_POSITION a, PER_LINE b
							 where	a.POS_ID=$REVIEW_POS_ID and a.PL_CODE=b.PL_CODE ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$REVIEW_PL_NAME = trim($data[PL_NAME]);
			$REVIEW_PT_CODE = trim($data[PT_CODE]);
			$REVIEW_PT_NAME = trim($data[PT_NAME]);
			//$REVIEW_PL_NAME = trim($REVIEW_PL_NAME)?($REVIEW_PL_NAME ." ". level_no_format($REVIEW_LEVEL_NO) . (($REVIEW_PT_NAME != "ทั่วไป" && $REVIEW_LEVEL_NO >= 6)?"$REVIEW_PT_NAME":"")):"ระดับ ".level_no_format($REVIEW_LEVEL_NO);
			$REVIEW_PL_NAME = trim($REVIEW_PL_NAME)?($REVIEW_PL_NAME ."". level_no_format($REVIEW_POSITION_LEVEL) . (($REVIEW_PT_NAME != "ทั่วไป" && $REVIEW_LEVEL_NO >= 6)?"$REVIEW_PT_NAME":"")):"ระดับ ".level_no_format($REVIEW_LEVEL_NO);
		}elseif($REVIEW_PER_TYPE==2){
			$cmd = " select 	b.PN_NAME
							 from 		PER_POS_EMP a, PER_POS_NAME b
							 where	a.POEM_ID=$REVIEW_POEM_ID and a.PN_CODE=b.PN_CODE
						  ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$REVIEW_PL_NAME = trim($data[PN_NAME]);
		}elseif($REVIEW_PER_TYPE==3){
			$cmd = " select 	b.EP_NAME
							 from 		PER_POS_EMPSER a, PER_EMPSER_POS_NAME b
							 where	a.POEMS_ID=$REVIEW_POEMS_ID and a.EP_CODE=b.EP_CODE
						  ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$REVIEW_PL_NAME = trim($data[EP_NAME]);
		} // end if
		$REVIEW_DEPARTMENT_NAME = $REVIEW_MINISTRY_ID = "";
		if($REVIEW_DEPARTMENT_ID){
			$cmd = " select ORG_ID_REF, ORG_NAME from PER_ORG where ORG_ID=$REVIEW_DEPARTMENT_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$REVIEW_DEPARTMENT_NAME = $data[ORG_NAME];
			$REVIEW_MINISTRY_ID = $data[ORG_ID_REF];
		}
		$REVIEW_MINISTRY_NAME = "";
		if($REVIEW_MINISTRY_ID){
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$REVIEW_MINISTRY_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$REVIEW_MINISTRY_NAME = $data[ORG_NAME];
		}
	}
	if($PER_ID_REVIEW1){
		$cmd = " select 	PN_CODE, PER_NAME, PER_SURNAME, PER_TYPE, POS_ID, POEM_ID, POEMS_ID, LEVEL_NO, DEPARTMENT_ID
						 from		PER_PERSONAL
						 where	PER_ID=$PER_ID_REVIEW1
					   ";
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
		
		$cmd = " select POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$REVIEW_LEVEL_NO1' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$REVIEW_POSITION_LEVEL1 = $data[POSITION_LEVEL];

		$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='$REVIEW_PN_CODE1' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$REVIEW_PN_NAME1 = trim($data[PN_NAME]);
		
		$REVIEW_PER_NAME1 = $REVIEW_PN_NAME1 . $REVIEW_PER_NAME1 . " " . $REVIEW_PER_SURNAME1;
			
		if($REVIEW_PER_TYPE1==1){
			$cmd = " select 	b.PL_NAME, a.PT_CODE
							 from 		PER_POSITION a, PER_LINE b
							 where	a.POS_ID=$REVIEW_POS_ID1 and a.PL_CODE=b.PL_CODE ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$REVIEW_PL_NAME1 = trim($data[PL_NAME]);
			$REVIEW_PT_CODE1 = trim($data[PT_CODE]);
			$REVIEW_PT_NAME1 = trim($data[PT_NAME]);
			//$REVIEW_PL_NAME1 = trim($REVIEW_PL_NAME1)?($REVIEW_PL_NAME1 ." ". level_no_format($REVIEW_LEVEL_NO1) . (($REVIEW_PT_NAME1 != "ทั่วไป" && $REVIEW_LEVEL_NO1 >= 6)?"$REVIEW_PT_NAME1":"")):"ระดับ ".level_no_format($REVIEW_LEVEL_NO1);
			$REVIEW_PL_NAME1 = trim($REVIEW_PL_NAME1)?($REVIEW_PL_NAME1 ."".$REVIEW_POSITION_LEVEL1. (($REVIEW_PT_NAME1 != "ทั่วไป" && $REVIEW_LEVEL_NO1 >= 6)?"$REVIEW_PT_NAME1":"")):"ระดับ ".level_no_format($REVIEW_LEVEL_NO1);
		}elseif($REVIEW_PER_TYPE1==2){
			$cmd = " select 	b.PN_NAME
							 from 		PER_POS_EMP a, PER_POS_NAME b
							 where	a.POEM_ID=$REVIEW_POEM_ID1 and a.PN_CODE=b.PN_CODE
						  ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$REVIEW_PL_NAME1 = trim($data[PN_NAME]);
		}elseif($REVIEW_PER_TYPE1==3){
			$cmd = " select 	b.EP_NAME
							 from 		PER_POS_EMPSER a, PER_EMPSER_POS_NAME b
							 where	a.POEMS_ID=$REVIEW_POEMS_ID1 and a.EP_CODE=b.EP_CODE
						  ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$REVIEW_PL_NAME1 = trim($data[EP_NAME]);
		} // end if
		$REVIEW_DEPARTMENT_NAME1 = $REVIEW_MINISTRY_ID1 = "";
		if($REVIEW_DEPARTMENT_ID1){
			$cmd = " select ORG_ID_REF, ORG_NAME from PER_ORG where ORG_ID=$REVIEW_DEPARTMENT_ID1 ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$REVIEW_DEPARTMENT_NAME1 = $data[ORG_NAME];
			$REVIEW_MINISTRY_ID1 = $data[ORG_ID_REF];
		}
		$REVIEW_MINISTRY_NAME1 = "";
		if($REVIEW_MINISTRY_ID1){
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$REVIEW_MINISTRY_ID1 ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$REVIEW_MINISTRY_NAME1 = $data[ORG_NAME];
		}
	}
	if($SUBPAGE==1){
		if($command == "SAVE"){
			if (!get_magic_quotes_gpc()) {
				$OUTSTANDING_PERFORMANCE = addslashes(str_replace('"', "&quot;", trim($OUTSTANDING_PERFORMANCE)));
			}else{
				$OUTSTANDING_PERFORMANCE = addslashes(str_replace('"', "&quot;", stripslashes(trim($OUTSTANDING_PERFORMANCE))));
			} // end if

			$cmd = " update PER_PERFORMANCE_GOODNESS set
								OUTSTANDING_PERFORMANCE = '$OUTSTANDING_PERFORMANCE',
								UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE'
							 where PG_ID=$PG_ID ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
		} // end if
		
		$cmd = " select OUTSTANDING_PERFORMANCE from PER_PERFORMANCE_GOODNESS where PG_ID=$PG_ID ";		
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$OUTSTANDING_PERFORMANCE = $data[OUTSTANDING_PERFORMANCE];
	}elseif($SUBPAGE==2){
		if($command == "SAVE"){
			if (!get_magic_quotes_gpc()) {
				$OUTSTANDING_GOODNESS = addslashes(str_replace('"', "&quot;", trim($OUTSTANDING_GOODNESS)));
			}else{
				$OUTSTANDING_GOODNESS = addslashes(str_replace('"', "&quot;", stripslashes(trim($OUTSTANDING_GOODNESS))));
			} // end if

			$cmd = " update PER_PERFORMANCE_GOODNESS set
								OUTSTANDING_GOODNESS = '$OUTSTANDING_GOODNESS',
								UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE'
							 where PG_ID=$PG_ID ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
		} // end if
		
		$cmd = " select OUTSTANDING_GOODNESS from PER_PERFORMANCE_GOODNESS where PG_ID=$PG_ID ";		
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$OUTSTANDING_GOODNESS = $data[OUTSTANDING_GOODNESS];
	}elseif($SUBPAGE==3){
		if($command == "SAVE"){
			if (!get_magic_quotes_gpc()) {
				$IPIP_REVIEW = addslashes(str_replace('"', "&quot;", trim($IPIP_REVIEW)));
			}else{
				$IPIP_REVIEW = addslashes(str_replace('"', "&quot;", stripslashes(trim($IPIP_REVIEW))));
			} // end if

			$cmd = " update PER_PERFORMANCE_GOODNESS set
								IPIP_REVIEW = '$IPIP_REVIEW',
								UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE'
							 where PG_ID=$PG_ID ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
		} // end if
		
		$cmd = " select IPIP_REVIEW from PER_PERFORMANCE_GOODNESS where PG_ID=$PG_ID ";		
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$IPIP_REVIEW = $data[IPIP_REVIEW];
	} // end if
?>