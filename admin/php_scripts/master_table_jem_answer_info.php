<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$UPDATE_DATE = date("Y-m-d H:i:s");

	if($command == "ADD" && $QUESTION_NO){
		$cmd = " select ID, QUESTION_NO, ANSWER_INFO from JEM_ANSWER_INFO where ID=$ID ";
		$count_duplicate = $db_dpis->send_cmd($cmd);
		if($count_duplicate <= 0){
		
		$cmd = " select max(ID) as max_id from JEM_ANSWER_INFO ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$ID = $data[max_id] + 1;
		
			$cmd = " insert into JEM_ANSWER_INFO (ID, QUESTION_NO, ANSWER_INFO, SCORE, TYPE_OF_CHOICE, TYPE_OF_CHOICE1, 
							TYPE_OF_CHOICE2, TYPE_OF_CHOICE3, TYPE_OF_CHOICE4) 
							values ($ID, $QUESTION_NO, '$ANSWER_INFO', '$SCORE', '$TYPE_OF_CHOICE', '$TYPE_OF_CHOICE1', 
							'$TYPE_OF_CHOICE2', '$TYPE_OF_CHOICE3', '$TYPE_OF_CHOICE4') ";
			$db_dpis->send_cmd($cmd);
			$db_dpis->show_error();

			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [$ID : $QUESTION_NO]");
		}else{
			$data = $db_dpis->get_array();			
			$err_text = "รหัสข้อมูลซ้ำ [".$data[ID]." ".$data[QUESTION_NO]." ".$data[ANSWER_INFO]."]";
		} // endif
	}

	if($command == "UPDATE" && $ID){
		$cmd = " update JEM_ANSWER_INFO set QUESTION_NO=$QUESTION_NO, ANSWER_INFO='$ANSWER_INFO', SCORE='$SCORE', 
						TYPE_OF_CHOICE='$TYPE_OF_CHOICE', TYPE_OF_CHOICE1='$TYPE_OF_CHOICE1', 
						TYPE_OF_CHOICE2='$TYPE_OF_CHOICE2', TYPE_OF_CHOICE3='$TYPE_OF_CHOICE3', 
						TYPE_OF_CHOICE4='$TYPE_OF_CHOICE4' 
						where ID=$ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [$ID : $QUESTION_NO : $ANSWER_INFO]");
	}
	
	if($command == "DELETE" && $ID){
		$cmd = " select QUESTION_NO, ANSWER_INFO from JEM_ANSWER_INFO where ID=$ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$QUESTION_NO = $data[QUESTION_NO];
		$ANSWER_INFO = $data[ANSWER_INFO];
		
		$cmd = " delete from JEM_ANSWER_INFO where ID=$ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [$ID : $QUESTION_NO : $ANSWER_INFO]");
	}
	
	if($UPD){
		$cmd = " select QUESTION_NO, ANSWER_INFO, SCORE, TYPE_OF_CHOICE, TYPE_OF_CHOICE1, TYPE_OF_CHOICE2, 
						TYPE_OF_CHOICE3, TYPE_OF_CHOICE4 from JEM_ANSWER_INFO where ID=$ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$QUESTION_NO = $data[QUESTION_NO];
		$ANSWER_INFO = $data[ANSWER_INFO];
		$SCORE = $data[SCORE];
		$TYPE_OF_CHOICE = $data[TYPE_OF_CHOICE];
		$TYPE_OF_CHOICE1 = $data[TYPE_OF_CHOICE1];
		$TYPE_OF_CHOICE2 = $data[TYPE_OF_CHOICE2];
		$TYPE_OF_CHOICE3 = $data[TYPE_OF_CHOICE3];
		$TYPE_OF_CHOICE4 = $data[TYPE_OF_CHOICE4];
	} // end if
	
	if( (!$UPD && !$DEL && !$err_text) ){
		$ID = "";
		$QUESTION_NO = "";
		$ANSWER_INFO = "";
		$SCORE = "";
		$TYPE_OF_CHOICE = "";
		$TYPE_OF_CHOICE1 = "";
		$TYPE_OF_CHOICE2 = "";
		$TYPE_OF_CHOICE3 = "";
		$TYPE_OF_CHOICE4 = "";
	} // end if
?>