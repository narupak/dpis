<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$UPDATE_DATE = date("Y-m-d H:i:s");
	
	$APPROVE_TXT = "";
	if($APPROVE_NO == 1) $APPROVE_TXT = "FIRST";
	elseif($APPROVE_NO == 2) $APPROVE_TXT = "SECOND";
		
	if($command == "SAVE"){
		if( $HISTORY_ID ){
			$cmd = " update JOB_EVALUATION_APPROVED set
								ISPASSED_".$APPROVE_TXT."='$ISPASSED',
								APPROVE_".$APPROVE_TXT."_BY=$SESS_USERID,
								APPROVE_".$APPROVE_TXT."_TIME='$UPDATE_DATE',
								REASON_".$APPROVE_TXT."='$REASON'
							 where HISTORY_ID=$HISTORY_ID and JOB_EVA_ID=$JOB_EVA_ID ";
		}else{
			$cmd = " insert into JOB_EVALUATION_APPROVED
								(JOB_EVA_ID, ISPASSED_".$APPROVE_TXT.", APPROVE_".$APPROVE_TXT."_BY, 
								 APPROVE_".$APPROVE_TXT."_TIME, REASON_".$APPROVE_TXT.")
							 values
							 	($JOB_EVA_ID, '$ISPASSED', $SESS_USERID, '$UPDATE_DATE', '$REASON') ";
		} //if( $HISTORY_ID ){
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " select POS_ID, SCORE from JOB_EVALUATION where JOB_EVA_ID=$JOB_EVA_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$POS_ID = $data[POS_ID];

		$JOB_SCORE = trim($data[SCORE]);
		$arr_temp = explode(",", $JOB_SCORE);
		$REQUEST_LV = $arr_temp[14];
		$CURRENT_LV = $arr_temp[15];
		
		$cmd = " select WORKFLOW_TIMES from CONFIG_WORKFLOW where LV_NAME='$LEVEL_NO' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$APPROVE_TIMES = $data[WORKFLOW_TIMES];
		
		if($APPROVE_TIMES == $APPROVE_NO && $ISPASSED == "Y"){
			// If pass final approve, update PER_POSITION from current level ($CURRENT_LV) to new level ($REQUEST_LV)
			// and insert old record to POSITION_HISTORY
			
			$UPDATE_MONTH = date("m", strtotime($UPDATE_DATE)) + 0;
			$UPDATE_YEAR = date("Y", strtotime($UPDATE_DATE)) + 543;
			$IS_FINAL_YEAR_UPDATED = 0;		// ??? still don't know what is the purpose of this field
			
			$cmd = "	insert	into POSITION_HISTORY 
										( 	POS_ID, ORG_ID, POS_NO, OT_CODE, ORG_ID_1, ORG_ID_2, PM_CODE, PL_CODE, CL_NAME, LEVEL_NO,
											POS_SALARY, POS_MGTSALARY, SKILL_CODE, PT_CODE, PC_CODE, POS_CONDITION, POS_DOC_NO, POS_REMARK,
											POS_DATE, POS_GET_DATE, POS_CHANGE_DATE, POS_STATUS,
											UPDATE_USER, UPDATE_DATE, UPDATE_MONTH, UPDATE_YEAR, IS_FINAL_YEAR_UPDATED)
							select	POS_ID, ORG_ID, POS_NO, OT_CODE, ORG_ID_1, ORG_ID_2, PM_CODE, PL_CODE, CL_NAME, LEVEL_NO,
										POS_SALARY, POS_MGTSALARY, SKILL_CODE, PT_CODE, PC_CODE, POS_CONDITION, POS_DOC_NO, POS_REMARK,
										POS_DATE, POS_GET_DATE, POS_CHANGE_DATE, POS_STATUS,
										$SESS_USERID, '$UPDATE_DATE', $UPDATE_MONTH, $UPDATE_YEAR, $IS_FINAL_YEAR_UPDATED
							from		PER_POSITION
							where	POS_ID=$POS_ID ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();

			$cmd = " update PER_POSITION set
								LEVEL_NO = '$REQUEST_LV'
						     where POS_ID=$POS_ID ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
		} //if($APPROVE_TIMES == $APPROVE_NO && $ISPASSED == "Y"){
	} //if($command == "SAVE"){

	$cmd = " select POS_ID from JOB_EVALUATION where JOB_EVA_ID=$JOB_EVA_ID ";
	$db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data = $db_dpis->get_array();
	$POS_ID = trim($data[POS_ID]);

	$cmd = " select 	HISTORY_ID, ISPASSED_".$APPROVE_TXT.", REASON_".$APPROVE_TXT."
					 from 		JOB_EVALUATION_APPROVED 
					 where 	JOB_EVA_ID=$JOB_EVA_ID ";
	$db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data = $db_dpis->get_array();
	$HISTORY_ID = trim($data[HISTORY_ID]);
	$ISPASSED = trim($data["ISPASSED_".$APPROVE_TXT]);
	$REASON = trim($data["REASON_".$APPROVE_TXT]);

	$cmd = " select a.PL_CODE, b.ORG_NAME from PER_POSITION a, PER_ORG b where a.POS_ID=$POS_ID and a.ORG_ID=b.ORG_ID ";
	$db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();
	$data = $db_dpis->get_array();
	$ORG_NAME = trim($data[ORG_NAME]);
	$PL_CODE = trim($data[PL_CODE]);

	$cmd = " select PL_NAME from PER_LINE where PL_CODE='".$PL_CODE."' ";
	$db_dpis2->send_cmd($cmd);
	$data_dpis2 = $db_dpis2->get_array();
	$PL_NAME = $data_dpis2[PL_NAME];
	
	$cmd = " select POS_ID,SCORE, CONSISTENCY, ISPASSED, TESTER_ID, TEST_TIME from JOB_EVALUATION where JOB_EVA_ID = '$JOB_EVA_ID' ";
	$db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();
	$data = $db_dpis->get_array();
	$SCORE = trim($data[SCORE]);
	$CONSISTENCY = trim($data[CONSISTENCY]);
	$EVALUATE_RESULT = trim($data[ISPASSED]);
	$TESTER_ID = trim($data[TESTER_ID]);
	$TEST_DATE = substr(trim($data[TEST_TIME]), 0, 10);
	$TEST_TIME = substr(trim($data[TEST_TIME]), 10, 9);
	if(trim($TEST_DATE)){
		$arr_temp = explode("-", $TEST_DATE);
		$TEST_TIME = $arr_temp[2] ." ". $month_abbr[($arr_temp[1] + 0)][TH] ." ". ($arr_temp[0] + 543) . " " . $TEST_TIME;
	} // end if
	
	$cmd = " select FULLNAME from USER_DETAIL where ID=$TESTER_ID ";
	$db_dpis->send_cmd($cmd);
	$data_dpis = $db_dpis->get_array();
	$TESTER_NAME = $data_dpis[FULLNAME];
		
	$ARR_SCORE = explode(",", $SCORE);
	$KH1 = $ARR_SCORE[0];
	$KH2 = $ARR_SCORE[1];
	$KH3 = $ARR_SCORE[2];
	$KH_SCORE = $ARR_SCORE[3];
	
	$PS1 = $ARR_SCORE[4];
	$PS2 = $ARR_SCORE[5];
	$PS_SCORE = $ARR_SCORE[6];
	$PS_KH_SCORE = $ARR_SCORE[7];
	
	$ACC1 = $ARR_SCORE[8];
	$ACC2 = $ARR_SCORE[9];
	$ACC3 = $ARR_SCORE[10];
	$ACC_SCORE = $ARR_SCORE[11];
	
	$TOTAL_POINTS = $ARR_SCORE[12];
	$PROFILE_CHECK = $ARR_SCORE[13];
	$EVALUATE_LEVEL_NO = $ARR_SCORE[14];
	
	$ARR_CONSISTENCY = explode(",", $CONSISTENCY);
	$KH1_CONSISTENCY = $ARR_CONSISTENCY[0];
	$KH2_CONSISTENCY = $ARR_CONSISTENCY[1];
	$KH3_CONSISTENCY = $ARR_CONSISTENCY[2];
	$KH_CONSISTENCY = $ARR_CONSISTENCY[3];	

	$PS1_CONSISTENCY = $ARR_CONSISTENCY[4];
	$PS2_CONSISTENCY = $ARR_CONSISTENCY[5];
	$PS_CONSISTENCY = $ARR_CONSISTENCY[6];	

	$ACC1_CONSISTENCY = $ARR_CONSISTENCY[7];	
	$ACC_CONSISTENCY = $ARR_CONSISTENCY[8];	
	
	$PC_CONSISTENCY = $ARR_CONSISTENCY[9];
	
	$cmd = " select LEVEL_NAME from PER_LEVEL where LEVEL_NO='$EVALUATE_LEVEL_NO' ";
	$db_dpis2->send_cmd($cmd);
	$data2 = $db_dpis2->get_array();
	$EVALUATE_LEVEL_NAME = $data2[LEVEL_NAME];
	
	$cmd = " select MINIMUM,MAXIMUM from JEM_GRADE where NAME ='$EVALUATE_LEVEL_NO' ";
	$db_dpis2->send_cmd($cmd);
	$data2 = $db_dpis2->get_array();
	$MINIMUM = $data2[MINIMUM];
	$MAXIMUM = $data2[MAXIMUM];
?>