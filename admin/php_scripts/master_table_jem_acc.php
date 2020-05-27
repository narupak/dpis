<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$UPDATE_DATE = date("Y-m-d H:i:s");

	if($command == "ADD" && $RESULT){
		$cmd = " select ID, RESULT, SCORE from JEM_ACC where ID=$ID ";
		$count_duplicate = $db_dpis->send_cmd($cmd);
		if($count_duplicate <= 0){
		
		$cmd = " select max(ID) as max_id from JEM_ACC ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$ID = $data[max_id] + 1;
		
			$cmd = " insert into JEM_ACC (ID,RESULT,SCORE) values ($ID,'$RESULT', '$SCORE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [$ID : $RESULT]");
		}else{
			$data = $db_dpis->get_array();			
			$err_text = "รหัสข้อมูลซ้ำ [".$data[ID]." ".$data[RESULT]." ".$data[SCORE]."]";
		} // endif
	}

	if($command == "UPDATE" && $ID){
		$cmd = " update JEM_ACC set RESULT='$RESULT', SCORE='$SCORE' where ID=$ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [$ID : $RESULT : $SCORE]");
	}
	
	if($command == "DELETE" && $ID){
		$cmd = " select RESULT from JEM_ACC where ID=$ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$RESULT = $data[RESULT];
		
		$cmd = " delete from JEM_ACC where ID=$ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [$ID : $RESULT : $SCORE]");
	}
	
	if($UPD){
		$cmd = " select RESULT, SCORE from JEM_ACC where ID=$ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$RESULT = $data[RESULT];
		$SCORE = $data[SCORE];
	} // end if
	
	if( (!$UPD && !$DEL && !$err_text) ){
		$ID = "";
		$RESULT = "";
		$SCORE = "";
	} // end if
?>