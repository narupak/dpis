<?
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

	if($command == "UPDATE"){
		$UPDATE_DATE = date("Y-m-d H:i:s");
		switch($CH_CTRL_TYPE){
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
		} // end switch case
		
		if(trim($CH_PROVINCE_CODE)) $CH_PROVINCE_CODE = "'$CH_PROVINCE_CODE'";
		else $CH_PROVINCE_CODE = "NULL";
		if(!$CH_ORG_ID) $CH_ORG_ID = "NULL";

/*
		$cmd = " select CTRL_TYPE from PER_CONTROL ";
		$count_ctrl = $db_dpis->send_cmd($cmd);
		if($count_ctrl){
			$cmd = " update PER_CONTROL set 
								CTRL_TYPE = $CH_CTRL_TYPE,
								PV_CODE = $CH_PROVINCE_CODE,
								ORG_ID = $CH_ORG_ID,
								UPDATE_USER = $SESS_USERID,
								UPDATE_DATE = '$UPDATE_DATE'
						  ";
		}else{
			$cmd = " insert into PER_CONTROL
							 (CTRL_TYPE, PV_CODE, ORG_ID, UPDATE_USER, UPDATE_DATE)
							 values
							 ($CH_CTRL_TYPE, $CH_PROVINCE_CODE, $CH_ORG_ID, $SESS_USERID, '$UPDATE_DATE')
						  ";
		} // end if
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
*/
		$cmd = " delete from PER_CONTROL ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " insert into PER_CONTROL
						 (CTRL_TYPE, PV_CODE, ORG_ID, UPDATE_USER, UPDATE_DATE)
						 values
						 ($CH_CTRL_TYPE, $CH_PROVINCE_CODE, $CH_ORG_ID, $SESS_USERID, '$UPDATE_DATE')
					  ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [$PROVINCE_CODE => $CH_PROVINCE_CODE ; $MINISTRY_ID => $CH_MINISTRY_ID ; $DEPARTMENT_ID => $CH_DEPARTMENT_ID]");
		
		$db2 = new connect_db($db_host, $db_name, $db_user, $db_pwd);
		
		$cmd = " select id, group_level, pv_code, org_id from user_group order by group_level ";
		$db->send_cmd($cmd);
//		$db->show_error();
		while($data = $db->get_array()){
			$group_id = $data[id];
			$group_level = $data[group_level];
			$group_pv_code = $data[pv_code];
			$group_org_id = $data[org_id];
			
			if($CH_CTRL_TYPE > $group_level){
				$group_level = $CH_CTRL_TYPE;
				$group_pv_code = $CH_PROVINCE_CODE;
				$group_org_id = $CH_ORG_ID;
			}elseif($CH_CTRL_TYPE == $group_level){
				$group_pv_code = $CH_PROVINCE_CODE;
				$group_org_id = $CH_ORG_ID;
			}elseif($CH_CTRL_TYPE < $group_level){
			} // end if

			$cmd = " update user_group set 
								group_level = $group_level,
								pv_code = '$group_pv_code',
								org_id = '$group_org_id'
							 where id=$group_id
						  ";
			$db2->send_cmd($cmd);
//			$db2->show_error();
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
	
	$cmd = " select CTRL_TYPE, PV_CODE, ORG_ID from PER_CONTROL ";
	$db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data = $db_dpis->get_array();
	$CTRL_TYPE = $data[CTRL_TYPE];
	if(!$CTRL_TYPE) $CTRL_TYPE = 4;
	
	switch($CTRL_TYPE){
		case 2 :
			$PROVINCE_CODE = $data[PV_CODE];
			break;
		case 3 :
			$MINISTRY_ID = $data[ORG_ID];
			break;
		case 4 :
			$DEPARTMENT_ID = $data[ORG_ID];
			break;
	} // end switch case

//	echo "$CTRL_TYPE :: $PROVINCE_CODE :: $MINISTRY_ID :: $DEPARTMENT_ID";
		
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