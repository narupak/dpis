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
	
	if($command=="REPLACEPOS"){
		if(trim($REPLACE_POS_ID)){
			$cmd = " update PER_POSITION set POS_STATUS='2' where POS_ID=$REPLACE_POS_ID ";
			$db_dpis->send_cmd($cmd);

			$cmd = " select POS_STATUS from PER_POSITION where POS_ID=$POS_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$POS_STATUS = trim($data[POS_STATUS]);
		
			$cmd = " update PER_POSITION set POS_STATUS = ". ($POS_STATUS - 50) ." where POS_ID=$POS_ID ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
		}else{
			$cmd = " delete from PER_POSITION where POS_ID=$POS_ID ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
		}
	} // end if

?>md($cmd);

			$cmd = " select POS_STATUS from PER_POSITION where POS_ID=$POS_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$POS_STATUS = trim($data[POS_STATUS]);
		
			$cmd = " update PER_POSITION set POS_STATUS = ". ($POS_STATUS - 50) ." where POS_ID=$POS