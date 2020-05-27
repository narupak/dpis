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
			
	if ($command == "SETFLAG") {
		$setflagshow =  implode(",",$list_show_id);
//		echo "$setflagshow";
		$cmd = " update EAF_MASTER set EAF_ACTIVE = 0 where EAF_ID in (".stripslashes($current_list).") ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
		//$db_dpis->show_error(); echo "<hr>";
		$cmd = " update EAF_MASTER set EAF_ACTIVE = 1 where EAF_ID in (".stripslashes($setflagshow).") ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
		//$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ��駤�ҡ����ҹ������");
	}

	if($command == "DELETE" && $EAF_ID){
		$cmd = " select EAF_NAME from EAF_MASTER where EAF_ID=$EAF_ID";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$EAF_NAME = $data[EAF_NAME];
		
		$cmd = " delete from EAF_MASTER where EAF_ID=$EAF_ID";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ź������ [$EAF_ID : $EAF_NAME]");
	} // end if	
?>