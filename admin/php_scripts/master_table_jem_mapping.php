<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$UPDATE_DATE = date("Y-m-d H:i:s");

	if($command == "ADD" && $MAP_NAME){
		$cmd = " select MAP_ID, MAP_NAME, MAP_LEVEL from JEM_MAPPING where MAP_ID=$MAP_ID ";
		$count_duplicate = $db_dpis->send_cmd($cmd);
		if($count_duplicate <= 0){
		
		$cmd = " select max(MAP_ID) as max_id from JEM_MAPPING ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$MAP_ID = $data[max_id] + 1;
		
			$cmd = " insert into JEM_MAPPING (MAP_ID, MAP_NAME, MAP_LEVEL, GRADE) values ($MAP_ID, '$MAP_NAME', '$MAP_LEVEL', '$GRADE') ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();

			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [$MAP_ID : $MAP_NAME]");
		}else{
			$data = $db_dpis->get_array();			
			$err_text = "รหัสข้อมูลซ้ำ [".$data[MAP_ID]." ".$data[MAP_NAME]." ".$data[MAP_LEVEL]."]";
		} // endif
	}

	if($command == "UPDATE" && $MAP_ID){
		$cmd = " update JEM_MAPPING set MAP_NAME='$MAP_NAME', MAP_LEVEL='$MAP_LEVEL', GRADE='$GRADE' 
						where MAP_ID=$MAP_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [$MAP_ID : $MAP_NAME : $MAP_LEVEL]");
	}
	
	if($command == "DELETE" && $MAP_ID){
		$cmd = " select MAP_NAME from JEM_MAPPING where MAP_ID=$MAP_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAP_NAME = $data[MAP_NAME];
		
		$cmd = " delete from JEM_MAPPING where MAP_ID=$MAP_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [$MAP_ID : $MAP_NAME : $MAP_LEVEL]");
	}
	
	if($UPD){
		$cmd = " select MAP_NAME, MAP_LEVEL, GRADE from JEM_MAPPING where MAP_ID=$MAP_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAP_NAME = $data[MAP_NAME];
		$MAP_LEVEL = $data[MAP_LEVEL];
		$GRADE = $data[GRADE];
	} // end if
	
	if( (!$UPD && !$DEL && !$err_text) ){
		$MAP_ID = "";
		$MAP_NAME = "";
		$MAP_LEVEL = "";
		$GRADE = "";
	} // end if
?>