<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

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
	$SESS_GROUPCODE = $data[code];
	
	$cmd = " select 	PER_ID_REVIEW, AGREE_REVIEW, DIFF_REVIEW, AGREE_REVIEW1, DIFF_REVIEW1, AGREE_REVIEW2, DIFF_REVIEW2 
			   from 		EAF_PERSONAL 
			   where 	EP_ID=$EP_ID ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$PER_ID_REVIEW = $data[PER_ID_REVIEW];
	$AGREE_REVIEW = trim($data[AGREE_REVIEW]);
	$DIFF_REVIEW = trim($data[DIFF_REVIEW]);
	$AGREE_REVIEW1 = trim($data[AGREE_REVIEW1]);
	$DIFF_REVIEW1 = trim($data[DIFF_REVIEW1]);
	$AGREE_REVIEW2 = trim($data[AGREE_REVIEW2]);
	$DIFF_REVIEW2 = trim($data[DIFF_REVIEW2]);
	
	if($SESS_USERGROUP == 1){
		$USER_AUTH = TRUE;
	}elseif((($SESS_GROUPCODE == "BUREAU" || substr($SESS_GROUPCODE, 0, 7) == "BUREAU_" ) || $SESS_USERGROUP==3) && $PER_ID==$SESS_PER_ID && !$AGREE_REVIEW && !$DIFF_REVIEW && !$AGREE_REVIEW1 && !$DIFF_REVIEW1 && !$AGREE_REVIEW2 && !$DIFF_REVIEW2){
		$USER_AUTH = TRUE;
	}else{
		$USER_AUTH = FALSE;
	} // end if
	
	if($SESS_USERGROUP == 1){
		$USER_REVIEW_AUTH = TRUE;
	}elseif((($SESS_GROUPCODE == "BUREAU" || substr($SESS_GROUPCODE, 0, 7) == "BUREAU_" ) || $SESS_USERGROUP==3) && $PER_ID_REVIEW==$SESS_PER_ID && !$AGREE_REVIEW && !$DIFF_REVIEW && !$AGREE_REVIEW1 && !$DIFF_REVIEW1 && !$AGREE_REVIEW2 && !$DIFF_REVIEW2){
		$USER_REVIEW_AUTH = TRUE;
	}else{
		$USER_REVIEW_AUTH = FALSE;
	} // end if
	
	if($SUBPAGE==1){

	}elseif($SUBPAGE==2){

		if($command == "SAVE"){
			$cmd = " select EPD_ID from EAF_PERSONAL_DETAIL where EP_ID=$EP_ID and EPK_ID=$EPK_ID ";
			$count_data = $db_dpis->send_cmd($cmd);
//			echo "$cmd<br>";
//			$db_dpis->show_error();
			if(!$count_data){
				$cmd = " select max(EPD_ID) as MAX_EPD_ID from EAF_PERSONAL_DETAIL ";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				$EPD_ID = $data[MAX_EPD_ID] + 1;
				
				$cmd = " insert into EAF_PERSONAL_DETAIL 
									( EPD_ID, EP_ID, EPK_ID, EPD_NAME, EPD_ORG, EPD_BEHAVIOR, EPD_COACH, EPD_JOB,
									  EPD_EVALUATE, EPD_EVALUATE_REASON, UPDATE_USER, UPDATE_DATE )
								values
									( $EPD_ID, $EP_ID, $EPK_ID, '$EPD_NAME', '$EPD_ORG', '$EPD_BEHAVIOR', '$EPD_COACH', '$EPD_JOB',
									  '$EPD_EVALUATE', '$EPD_EVALUATE_REASON', $SESS_USERID, '$UPDATE_DATE')
							 ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			}else{
				$data = $db_dpis->get_array();
				$EPD_ID = $data[EPD_ID];
				
				$cmd = " update EAF_PERSONAL_DETAIL set
									EPD_NAME='$EPD_NAME',
									EPD_ORG='$EPD_ORG',
									EPD_BEHAVIOR='$EPD_BEHAVIOR',
									EPD_COACH='$EPD_COACH',
									EPD_JOB='$EPD_JOB',
									EPD_EVALUATE='$EPD_EVALUATE',
									EPD_EVALUATE_REASON='$EPD_EVALUATE_REASON',
									UPDATE_USER = $SESS_USERID, UPDATE_DATE = '$UPDATE_DATE'
								where EPD_ID=$EPD_ID and EP_ID=$EP_ID and ELK_ID=$EPK_ID
							 ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			} // end if
		} // end if
		
		if($EPK_ID){
			$cmd = " select 	EPK_NAME, EPK_COACH, EPK_BEHAVIOR, EPK_TRAIN, EPK_JOB
							from		EAF_PERSONAL_KNOWLEDGE
							where	EPK_ID=$EPK_ID and EPS_ID=$EPS_ID and EP_ID=$EP_ID
						 ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$EPK_NAME = trim($data[EPK_NAME]);
			$EPK_COACH = trim($data[EPK_COACH]);
			$EPK_BEHAVIOR = trim($data[EPK_BEHAVIOR]);
			$EPK_TRAIN = trim($data[EPK_TRAIN]);
			$EPK_JOB = trim($data[EPK_JOB]);

			$cmd = " select 	EPD_NAME, EPD_ORG, EPD_BEHAVIOR, EPD_COACH, EPD_JOB, 
										EPD_EVALUATE, EPD_EVALUATE_REASON
							from		EAF_PERSONAL_DETAIL
							where	EPK_ID=$EPK_ID and EP_ID=$EP_ID
						 ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$EPD_NAME = trim($data[EPD_NAME]);
			$EPD_ORG = trim($data[EPD_ORG]);
			$EPD_BEHAVIOR = trim($data[EPD_BEHAVIOR]);
			$EPD_COACH = trim($data[EPD_COACH]);
			$EPD_JOB = trim($data[EPD_JOB]);
			$EPD_EVALUATE = trim($data[EPD_EVALUATE]);
			$EPD_EVALUATE_REASON = trim($data[EPD_EVALUATE_REASON]);
		} // end if
		
	} // end if
?>