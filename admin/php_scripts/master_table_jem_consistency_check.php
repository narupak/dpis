<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$UPDATE_DATE = date("Y-m-d H:i:s");

	if($command == "ADD" && $CONSISTENCY_CHECK){
		$cmd = " select ID, CONSISTENCY_CHECK, ANSWER from JEM_CONSISTENCY_CHECK where ID=$ID ";
		$count_duplicate = $db_dpis->send_cmd($cmd);
		if($count_duplicate <= 0){
		
		$cmd = " select max(ID) as max_id from JEM_CONSISTENCY_CHECK ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$ID = $data[max_id] + 1;
		
			$cmd = " insert into JEM_CONSISTENCY_CHECK (ID, CONSISTENCY_CHECK, ANSWER) values ($ID, '$CONSISTENCY_CHECK', '$ANSWER') ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();

			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [$ID : $CONSISTENCY_CHECK]");
		}else{
			$data = $db_dpis->get_array();			
			$err_text = "รหัสข้อมูลซ้ำ [".$data[ID]." ".$data[CONSISTENCY_CHECK]." ".$data[ANSWER]."]";
		} // endif
	}

	if($command == "UPDATE" && $ID){
		$cmd = " update JEM_CONSISTENCY_CHECK set CONSISTENCY_CHECK='$CONSISTENCY_CHECK', ANSWER='$ANSWER' where ID=$ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [$ID : $CONSISTENCY_CHECK : $ANSWER]");
	}
	
	if($command == "DELETE" && $ID){
		$cmd = " select CONSISTENCY_CHECK from JEM_CONSISTENCY_CHECK where ID=$ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$CONSISTENCY_CHECK = $data[CONSISTENCY_CHECK];
		
		$cmd = " delete from JEM_CONSISTENCY_CHECK where ID=$ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [$ID : $CONSISTENCY_CHECK : $ANSWER]");
	}
	
	if($UPD){
		$cmd = " select CONSISTENCY_CHECK, ANSWER from JEM_CONSISTENCY_CHECK where ID=$ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$CONSISTENCY_CHECK = $data[CONSISTENCY_CHECK];
		$ANSWER = $data[ANSWER];
	} // end if
	
	if( (!$UPD && !$DEL && !$err_text) ){
		$ID = "";
		$CONSISTENCY_CHECK = "";
		$ANSWER = "";
	} // end if
?>