<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	
	// =================================== PER_CONTROL ==================================//
	$cmd = " select CTRL_TYPE, PV_CODE, ORG_ID from PER_CONTROL ";
	$db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data = $db_dpis->get_array();
	$CTRL_TYPE = $data[CTRL_TYPE];
	if(!$CTRL_TYPE) $CTRL_TYPE = 4;
	
	switch($CTRL_TYPE){
		case 2 :
			$CTRL_PROVINCE_CODE = $data[PV_CODE];
			break;
		case 3 :
			$CTRL_MINISTRY_ID = $data[ORG_ID];
			break;
		case 4 :
			$CTRL_DEPARTMENT_ID = $data[ORG_ID];
			break;
	} // end switch case

	if($CTRL_DEPARTMENT_ID){
		$cmd = " select ORG_NAME, ORG_ID_REF from PER_ORG where ORG_ID=$CTRL_DEPARTMENT_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$CTRL_DEPARTMENT_NAME = $data[ORG_NAME];
		$CTRL_MINISTRY_ID = $data[ORG_ID_REF];	
	} // end if

	if($CTRL_MINISTRY_ID){
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$CTRL_MINISTRY_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$CTRL_MINISTRY_NAME = $data[ORG_NAME];
	} // end if

	if($CTRL_PROVINCE_CODE){
		$cmd = " select PV_NAME from PER_PROVINCE where PV_CODE='$CTRL_PROVINCE_CODE' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$CTRL_PROVINCE_NAME = $data[PV_NAME];
	} // end if
//	echo "$CTRL_TYPE :: $PROVINCE_CODE :: $MINISTRY_ID :: $DEPARTMENT_ID";
	// =================================== PER_CONTROL ==================================//	

//	echo "$CTRL_TYPE :: $ctrl_type<br>";
	if(!$command){
		switch($CTRL_TYPE){
			case 2 :
				$CTRL_PROVINCE_CODE = $CTRL_PROVINCE_CODE;
				$CTRL_PROVINCE_NAME = $CTRL_PROVINCE_NAME;
				break;
			case 3 :
				$CTRL_MINISTRY_ID = $CTRL_MINISTRY_ID;
				$CTRL_MINISTRY_NAME = $CTRL_MINISTRY_NAME;
				break;
			case 4 :
				$CTRL_MINISTRY_ID = $CTRL_MINISTRY_ID;
				$CTRL_MINISTRY_NAME = $CTRL_MINISTRY_NAME;
				$CTRL_DEPARTMENT_ID = $CTRL_DEPARTMENT_ID;
				$CTRL_DEPARTMENT_NAME = $CTRL_DEPARTMENT_NAME;
				break;
		} // end switch case
	} // end if

	if($command == "UPDATE"){
		$UPDATE_DATE = date("Y-m-d H:i:s");
		switch($group_level){
			case 1 :
				$CH_PROVINCE_CODE = "";
				$CH_ORG_ID = 0;
				break;
			case 2 :
				$CH_ORG_ID = 0;
				break;
			case 3 :
				$CH_PROVINCE_CODE = "";
				$CH_ORG_ID = $CH_MINISTRY_ID;
				break;
			case 4 :
				$CH_PROVINCE_CODE = "";
				$CH_ORG_ID = $CH_DEPARTMENT_ID;
				break;
			case 5 :
				$CH_PROVINCE_CODE = "";
				$CH_ORG_ID = $CH_ORG_ID;
				break;
		} // end switch case
 
		$cmd = " update user_group set 
							dpisdb = '$ch_dpisdb',
							dpisdb_host = '$ch_dpisdb_host',
							dpisdb_name = '$ch_dpisdb_name',
							dpisdb_user = '$ch_dpisdb_user',
							dpisdb_pwd = '$ch_dpisdb_pwd',
							update_by = '$SESS_USERNAME',
							update_date = '$UPDATE_DATE'
						 where id=$group_id ";
		$db->send_cmd($cmd);
//		$db->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ��䢢����� [$group_id : $PROVINCE_CODE => $CH_PROVINCE_CODE ; $MINISTRY_ID => $CH_MINISTRY_ID ; $DEPARTMENT_ID => $CH_DEPARTMENT_ID ; $ORG_ID => $CH_ORG_ID]");
		
		if($SESS_USERGROUP_LEVEL != $group_level){
			session_unregister("SESS_USERGROUP_LEVEL");
			$SESS_USERGROUP_LEVEL = $group_level;
			session_register("SESS_USERGROUP_LEVEL");
		} // end if
		if($SESS_PROVINCE_CODE != $CH_PROVINCE_CODE){
			session_unregister("SESS_PROVINCE_CODE");
			session_unregister("SESS_PROVINCE_NAME");
			if($group_level==2){ 
				$SESS_PROVINCE_CODE = $CH_PROVINCE_CODE;	
				$SESS_PROVINCE_NAME = $PROVINCE_NAME;	
			}else{ 
				$SESS_PROVINCE_CODE = "";
				$SESS_PROVINCE_NAME = "";
			} // end if
			session_register("SESS_PROVINCE_CODE");
			session_register("SESS_PROVINCE_NAME");
		} // end if
		if($SESS_MINISTRY_ID != $CH_MINISTRY_ID){
			session_unregister("SESS_MINISTRY_ID");
			session_unregister("SESS_MINISTRY_NAME");
			if($group_level==3 || $group_level==4 || $group_level==5){ 
				$SESS_MINISTRY_ID = $CH_MINISTRY_ID;
				$SESS_MINISTRY_NAME = $MINISTRY_NAME;
			}else{ 
				$SESS_MINISTRY_ID = "";
				$SESS_MINISTRY_NAME = "";
			} // end if
			session_register("SESS_MINISTRY_ID");
			session_register("SESS_MINISTRY_NAME");
		} // end if
		if($SESS_DEPARTMENT_ID != $CH_DEPARTMENT_ID){
			session_unregister("SESS_DEPARTMENT_ID");
			session_unregister("SESS_DEPARTMENT_NAME");
			if($group_level==4 || $group_level==5){
				$SESS_DEPARTMENT_ID = $CH_DEPARTMENT_ID;
				$SESS_DEPARTMENT_NAME = $DEPARTMENT_NAME;
			}else{
				$SESS_DEPARTMENT_ID = "";
				$SESS_DEPARTMENT_NAME = "";
			} // end if
			session_register("SESS_DEPARTMENT_ID");
			session_register("SESS_DEPARTMENT_NAME");
		} // end if
		if($SESS_ORG_ID != $CH_ORG_ID){
			session_unregister("SESS_ORG_ID");
			session_unregister("SESS_ORG_NAME");
			if($group_level==5){
				$SESS_ORG_ID = $CH_ORG_ID;
				$SESS_ORG_NAME = $ORG_NAME;
			}else{
				$SESS_ORG_ID = "";
				$SESS_ORG_NAME = "";
			} // end if
			session_register("SESS_ORG_ID");
			session_register("SESS_ORG_NAME");
		} // end if
	} // end if

	$PROVINCE_CODE = "";
	$CH_PROVINCE_CODE = "";
	$PROVINCE_NAME = "";
	$MINISTRY_ID = "";
	$CH_MINISTRY_ID = "";
	$MINISTRY_NAME = "";
	$DEPARTMENT_ID = "";
	$CH_DEPARTMENT_ID = "";
	$DEPARTMENT_NAME = "";
	$CH_ORG_ID = "";
	$ORG_NAME = "";
	
	$cmd = " select code, name_th, group_level, pv_code, org_id from user_group where id=$group_id ";
	$db->send_cmd($cmd);
//	$db->show_error();
	$data = $db->get_array();
	$data = array_change_key_case($data, CASE_LOWER);
	$group_code = $data[code];
	$group_name = $data[name_th];
	$group_level = $data[group_level];
	if(!$group_level) $group_level = 4;
	
	switch($group_level){
		case 2 :
			$PROVINCE_CODE = $data[pv_code];
			break;
		case 3 :
			$MINISTRY_ID = $data[org_id];
			break;
		case 4 :
			$DEPARTMENT_ID = $data[org_id];
			break;
		case 5 :
			$ORG_ID = $data[org_id];
			break;
	} // end switch case

//	echo "$group_level :: $PROVINCE_CODE :: $MINISTRY_ID :: $DEPARTMENT_ID :: $ORG_ID";
		
	if($ORG_ID){
		$cmd = " select ORG_NAME, ORG_ID_REF from PER_ORG where ORG_ID=$ORG_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$ORG_NAME = $data[ORG_NAME];
		$DEPARTMENT_ID = $data[ORG_ID_REF];	
	} // end if

	if($DEPARTMENT_ID){
		$cmd = " select ORG_NAME, ORG_ID_REF from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$DEPARTMENT_NAME = $data[ORG_NAME];
		$MINISTRY_ID = $data[ORG_ID_REF];	
	} // end if

	if($MINISTRY_ID){
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$MINISTRY_NAME = $data[ORG_NAME];
	} // end if

	if($PROVINCE_CODE){
		$cmd = " select PV_NAME from PER_PROVINCE where PV_CODE='$PROVINCE_CODE' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$PROVINCE_NAME = $data[PV_NAME];
	} // end if
?>