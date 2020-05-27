<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$UPDATE_DATE = date("Y-m-d H:i:s");

	if($command == "ADD" && $NAME){
		$cmd = " select GRADE_ID, NAME, K_H from JEM_GRADE where GRADE_ID=$GRADE_ID ";
		$count_duplicate = $db_dpis->send_cmd($cmd);
		if($count_duplicate <= 0){
		
		$cmd = " select max(GRADE_ID) as max_id from JEM_GRADE ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$ID = $data[max_id] + 1;
		
			$cmd = " insert into JEM_GRADE (GRADE_ID, NAME, K_H, GRADE, MINIMUM, MAXIMUM, RANGE, RANGE_IN_BAND) 
							values ($ID, '$NAME', '$K_H', $GRADE, $MINIMUM, $MAXIMUM, '$RANGE', '$RANGE_IN_BAND') ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();

			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [$GRADE_ID : $NAME]");
		}else{
			$data = $db_dpis->get_array();			
			$err_text = "รหัสข้อมูลซ้ำ [".$data[GRADE_ID]." ".$data[NAME]." ".$data[K_H]."]";
		} // endif
	}

	if($command == "UPDATE" && $GRADE_ID){
		$cmd = " update JEM_GRADE set NAME='$NAME', K_H='$K_H', GRADE=$GRADE, 
						MINIMUM=$MINIMUM, MAXIMUM=$MAXIMUM, 
						RANGE='$RANGE', RANGE_IN_BAND='$RANGE_IN_BAND' 
						where GRADE_ID=$GRADE_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [$GRADE_ID : $NAME : $K_H]");
	}
	
	if($command == "DELETE" && $GRADE_ID){
		$cmd = " select NAME, K_H from JEM_GRADE where GRADE_ID=$GRADE_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$NAME = $data[NAME];
		$K_H = $data[K_H];
		
		$cmd = " delete from JEM_GRADE where GRADE_ID=$GRADE_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [$GRADE_ID : $NAME : $K_H]");
	}
	
	if($UPD){
		$cmd = " select NAME, K_H, GRADE, MINIMUM, MAXIMUM, RANGE, RANGE_IN_BAND from JEM_GRADE where GRADE_ID=$GRADE_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$NAME = $data[NAME];
		$K_H = $data[K_H];
		$GRADE = $data[GRADE];
		$MINIMUM = $data[MINIMUM];
		$MAXIMUM = $data[MAXIMUM];
		$RANGE = $data[RANGE];
		$RANGE_IN_BAND = $data[RANGE_IN_BAND];
	} // end if
	
	if( (!$UPD && !$DEL && !$err_text) ){
		$GRADE_ID = "";
		$NAME = "";
		$K_H = "";
		$GRADE = "";
		$MINIMUM = "";
		$MAXIMUM = "";
		$RANGE = "";
		$RANGE_IN_BAND = "";
	} // end if
?>