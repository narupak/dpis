<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$UPDATE_DATE = date("Y-m-d H:i:s");
	
	if($command=="REORDER"){
		foreach($ARR_ORDER as $AP_CODE => $AP_SEQ_NO){
			if($AP_SEQ_NO=="") { $cmd = " update REPGEN_CONDITION set CONDITION_NAME='$CONDITION_NAME', CONDITION_TEXT='$CONDITION_TEXT' where CONDITION_ID=$CONDITION_ID "; }
		else {	$cmd = " update REPGEN_CONDITION set CONDITION_NAME='$CONDITION_NAME', CONDITION_TEXT='$CONDITION_TEXT' where CONDITION_ID=$CONDITION_ID "; }
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			//echo "<hr>".$cmd;
		} // end foreach

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > จัดลำดับ REPGEN_CONDITION");
	} // end if

	if($command == "ADD"){
		$cmd = " select max(CONDITION_ID) as max_no from REPGEN_CONDITION ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$NEXT_COND_ID = $data[max_no] + 1;

		$cmd = " insert into REPGEN_CONDITION (CONDITION_ID, CONDITION_NAME, CONDITION_TEXT, UPDATE_USER, UPDATE_DATE) values ($NEXT_COND_ID, '$CONDITION_NAME', '$CONDITION_TEXT', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
//		echo "insert : $cmd<br>";

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [".$CONDITION_ID." : ".trim($CONDITION_NAME)."]");
	}

	if($command == "UPDATE" && trim($CONDITION_ID)){
		$cmd = " update REPGEN_CONDITION set CONDITION_NAME='".$CONDITION_NAME."', CONDITION_TEXT='".$CONDITION_TEXT."', UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' where CONDITION_ID=$CONDITION_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
//		echo "update : $cmd<br>";

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [".$CONDITION_ID." : ".trim($CONDITION_NAME)."]");
	}
	
	if($command == "DELETE" && trim($CONDITION_ID)){
		$cmd = " select * from REPGEN_CONDITION where CONDITION_ID=".$CONDITION_ID." ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$CONDITION_NAME = $data[CONDITION_NAME];
		$CONDITION_TEXT = $data[CONDITION_TEXT];
		
		$cmd = " delete from REPGEN_CONDITION where CONDITION_ID=".$CONDITION_ID." ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [".$CONDITION_ID." : ".trim($CONDITION_NAME)."]");
	}
	
	if($UPD){
		$cmd = " select CONDITION_NAME, CONDITION_TEXT
				 		from REPGEN_CONDITION
					  where CONDITION_ID=".$CONDITION_ID." ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$CONDITION_NAME = $data[CONDITION_NAME];
		$CONDITION_TEXT = $data[CONDITION_TEXT];
	} // end if
	
	if( (!$UPD && !$DEL && !$err_text) ){
		$CONDITION_ID = "";
		$CONDITION_NAME = "";
		$CONDITION_TEXT = "";
	} // end if
?>