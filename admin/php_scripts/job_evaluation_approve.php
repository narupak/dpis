<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	switch($CTRL_TYPE){
		case 2 :
			$search_pv_code = $PROVINCE_CODE;
			$search_pv_name = $PROVINCE_NAME;
			break;
		case 3 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			break;
		case 4 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			break;
	} // end switch case

	switch($SESS_USERGROUP_LEVEL){
		case 2 :
			$search_pv_code = $PROVINCE_CODE;
			$search_pv_name = $PROVINCE_NAME;
			break;
		case 3 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			break;
		case 4 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			break;
		case 5 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			$search_org_id = $ORG_ID;
			$search_org_name = $ORG_NAME;
			break;
	} // end switch case

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$UPDATE_DATE = date("Y-m-d H:i:s");
	
	$cmd = " select START_TEST, END_TEST, END_APPROVE from CONFIG_JOB_EVALUATION where ID=0 ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();

	$START_TEST = trim($data[START_TEST]);
//	$arr_temp = explode("-", $START_TEST);
//	$START_TEST = $arr_temp[2] . $arr_temp[1] . $arr_temp[0];

	$END_TEST = trim($data[END_TEST]);
//	$arr_temp = explode("-", $END_TEST);
//	$END_TEST = $arr_temp[2] . $arr_temp[1] . $arr_temp[0];

	$END_APPROVE = trim($data[END_APPROVE]);
//	$arr_temp = explode("-", $END_APPROVE);
//	$END_APPROVE = $arr_temp[2] . $arr_temp[1] . $arr_temp[0];
	
	if(date("Ymd") <= $END_APPROVE){
//		$cmd = " select JOB_EVA_ID, TEST_TIME from JOB_EVALUATION where ISPASSED='Y' ";
		$cmd = " select JOB_EVA_ID, TEST_TIME from JOB_EVALUATION ";
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()){
			$TEST_TIME = substr($data[TEST_TIME], 0 , 10);
//			$arr_temp = explode("-", $TEST_TIME);
//			$TEST_TIME = $arr_temp[2] . $arr_temp[1] . $arr_temp[0];
			
			if($TEST_TIME >= $START_TEST && $TEST_TIME <= $END_TEST){
				$ARR_JOB_EVALUATION[] = $data[JOB_EVA_ID];
			} // end if
		} // loop while
	} // end if
	
	$LIST_JOB_EVALUATION = implode(",", $ARR_JOB_EVALUATION);
?>" select JOB_EVA_ID, TEST_TIME from JOB_EVALUATION ";
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()){
			$TEST_TIME = substr($data[TEST_TIME], 0 , 10);
//			$arr_temp = explode("-", $TEST_TIME);
//			$TEST_TIME = 