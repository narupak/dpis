<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$UPDATE_DATE = date("Y-m-d H:i:s");

	if($command == "ADD" && $PS_KH){
		$cmd = " select ID, PS_KH, SCORE from JEM_PS_KH where ID=$ID ";
		$count_duplicate = $db_dpis->send_cmd($cmd);
		if($count_duplicate <= 0){
		$cmd = " select max(ID) as max_id from JEM_PS_KH ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$ID = $data[max_id] + 1;
			$cmd = " insert into JEM_PS_KH (ID, PS_KH, SCORE) values ($ID, '$PS_KH', '$SCORE') ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();

			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [$ID : $PS_KH]");
		}else{
			$data = $db_dpis->get_array();			
			$err_text = "รหัสข้อมูลซ้ำ [".$data[ID]." ".$data[PS_KH]." ".$data[SCORE]."]";
		} // endif
	}

	if($command == "UPDATE" && $ID){
		$cmd = " update JEM_PS_KH set PS_KH='$PS_KH', SCORE='$SCORE' where ID=$ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [$ID : $PS_KH : $SCORE]");
	}
	
	if($command == "DELETE" && $ID){
		$cmd = " select PS_KH from JEM_PS_KH where ID=$ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PS_KH = $data[PS_KH];
		
		$cmd = " delete from JEM_PS_KH where ID=$ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [$ID : $PS_KH : $SCORE]");
	}
	
	if($UPD){
		$cmd = " select PS_KH, SCORE from JEM_PS_KH where ID=$ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PS_KH = $data[PS_KH];
		$SCORE = $data[SCORE];
	} // end if
	
	if( (!$UPD && !$DEL && !$err_text) ){
		$ID = "";
		$PS_KH = "";
		$SCORE = "";
	} // end if
?>