<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$UPDATE_DATE = date("Y-m-d H:i:s");

	if($command == "ADD" && $NAME){
		$cmd = " select ID, NAME, TOPIC from JEM_QUESTION_INFO where ID=$ID ";
		$count_duplicate = $db_dpis->send_cmd($cmd);
		if($count_duplicate <= 0){
		$cmd = " select max(ID) as max_id from JEM_QUESTION_INFO";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$ID = $data[max_id] + 1;
			$cmd = " insert into JEM_QUESTION_INFO (ID, NAME, TOPIC, DESCRIPTION, SEQUENCE) 
							values ($ID, '$NAME', '$TOPIC', '$DESCRIPTION', $SEQUENCE) ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();

			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [$ID : $NAME]");
		}else{
			$data = $db_dpis->get_array();			
			$err_text = "รหัสข้อมูลซ้ำ [".$data[ID]." ".$data[NAME]." ".$data[TOPIC]."]";
		} // endif
	}

	if($command == "UPDATE" && $ID){
		$cmd = " update JEM_QUESTION_INFO set NAME='$NAME', TOPIC='$TOPIC', DESCRIPTION='$DESCRIPTION', 
						SEQUENCE=$SEQUENCE 
						where ID=$ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [$ID : $NAME : $TOPIC]");
	}
	
	if($command == "DELETE" && $ID){
		$cmd = " select NAME, TOPIC from JEM_QUESTION_INFO where ID=$ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$NAME = $data[NAME];
		$TOPIC = $data[TOPIC];
		
		$cmd = " delete from JEM_QUESTION_INFO where ID=$ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [$ID : $NAME : $TOPIC]");
	}
	
	if($UPD){
		$cmd = " select NAME, TOPIC, DESCRIPTION, SEQUENCE from JEM_QUESTION_INFO where ID=$ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$NAME = $data[NAME];
		$TOPIC = $data[TOPIC];
		$DESCRIPTION = $data[DESCRIPTION];
		$SEQUENCE = $data[SEQUENCE];
	} // end if
	
	if( (!$UPD && !$DEL && !$err_text) ){
		$ID = "";
		$NAME = "";
		$TOPIC = "";
		$DESCRIPTION = "";
		$SEQUENCE = "";
	} // end if
?>