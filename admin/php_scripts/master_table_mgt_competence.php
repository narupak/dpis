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
		case 6 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			$search_org_id = $ORG_ID;
			$search_org_name = $ORG_NAME;
			$search_org_id_1 = $ORG_ID_1;
			$search_org_name_1 = $ORG_NAME_1;
			break;
	} // end switch case

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
//		$$arr_fields[$i]=str_replace("'","''",$$arr_fields[$i]);
//		if($UPD){
//			$$arr_fields[$i]=str_replace("\"","&quot;",$$arr_fields[$i]);
//			}

	$UPDATE_DATE = date("Y-m-d H:i:s");
//echo $command;
	
	if ($command == "SETFLAG") {
		$setflagshow =  implode(",",$list_show_id);
		$cmd = " update PER_MGT_COMPETENCE set CP_ACTIVE = 0 where CP_CODE in (".stripslashes($current_list).") ";
		$db_dpis->send_cmd($cmd);
		$cmd = " update PER_MGT_COMPETENCE set CP_ACTIVE = 1 where CP_CODE in (".stripslashes($setflagshow).") ";
		$db_dpis->send_cmd($cmd);
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการใช้งานข้อมูล");
	}
	
	if (!$CP_ENG_NAME) $CP_ENG_NAME = $CP_NAME;
	if($command == "ADD" && trim($CP_CODE)){
		$cmd = " select CP_CODE, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ACTIVE  
				  from PER_MGT_COMPETENCE where CP_CODE='$CP_CODE' ";
		$count_duplicate = $db_dpis->send_cmd($cmd);
		if($count_duplicate <= 0){
			$cmd = " insert into PER_MGT_COMPETENCE (CP_CODE, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE) 
							  values ('$CP_CODE', '$CP_NAME', '$CP_ENG_NAME', '$CP_MEANING', $CP_MODEL, $CP_ACTIVE, $SESS_USERID, 
							  '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [$CP_CODE : $CP_NAME : $CP_MEANING : $CP_MODEL]");
		}else{
			$data = $db_dpis->get_array();			
			$err_text = "รหัสข้อมูลซ้ำ [$data[CP_CODE] $data[CP_NAME] $data[CP_MEANING]";
		} // endif
	}

	if($command == "UPDATE" && trim($CP_CODE)){
		$cmd = " update PER_MGT_COMPETENCE set 
								CP_CODE='$CP_CODE', 
								CP_NAME='$CP_NAME', 
								CP_ENG_NAME='$CP_ENG_NAME', 
								CP_MEANING='$CP_MEANING', 
								CP_MODEL=$CP_MODEL, 
								CP_ACTIVE=$CP_ACTIVE, 
								UPDATE_USER=$SESS_USERID, 
								UPDATE_DATE='$UPDATE_DATE' 
							where CP_CODE='$CP_CODE' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [$CP_CODE : $CP_NAME : $CP_MEANING : $CP_MODEL]");
	}
	
	if($command == "DELETE" && trim($CP_CODE)){
		$cmd = " select CP_NAME from PER_MGT_COMPETENCE where CP_CODE='$CP_CODE' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$CP_NAME = $data[CP_NAME];
		
		$cmd = " delete from PER_MGT_COMPETENCE where CP_CODE='$CP_CODE' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [$CP_CODE : $CP_NAME : $CP_MEANING : $CP_MODEL]");
	}
	
	if($UPD){
		$cmd = " select CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ACTIVE, UPDATE_USER, UPDATE_DATE 
				  from PER_MGT_COMPETENCE where CP_CODE='$CP_CODE' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$CP_NAME = $data[CP_NAME];
		$CP_ENG_NAME = $data[CP_ENG_NAME];
		$CP_MEANING = $data[CP_MEANING];
		$CP_MODEL = $data[CP_MODEL];
		$CP_ACTIVE = $data[CP_ACTIVE];
		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);
	} // end if
	
	if( (!$UPD && !$DEL && !$err_text) ){
		$CP_CODE = "";
		$CP_NAME = "";
		$CP_ENG_NAME = "";
		$CP_MEANING = "";
		$CP_MODEL = 1;
		$CP_ACTIVE = 1;
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
	} // end if
?>