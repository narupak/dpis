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
			if($AP_SEQ_NO=="") { $cmd = " update REPGEN_LINK set LINK_NAME='$LINK_NAME', LINK_TEXT='$LINK_TEXT' where LINK_ID=$LINK_ID "; }
		else {	$cmd = " update REPGEN_LINK set LINK_NAME='$LINK_NAME', LINK_TEXT='$LINK_TEXT' where LINK_ID=$LINK_ID "; }
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			//echo "<hr>".$cmd;
		} // end foreach

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > จัดลำดับ REPGEN_LINK");
	} // end if

	if($command == "ADD"){
		$cmd = " select max(LINK_ID) as max_no from REPGEN_LINK ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$NEXT_COND_ID = $data[max_no] + 1;

		$cmd = " insert into REPGEN_LINK (LINK_ID, LINK_NAME, LINK_TEXT, UPDATE_USER, UPDATE_DATE) values ($NEXT_COND_ID, '$LINK_NAME', '$LINK_TEXT', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
//		echo "insert : $cmd<br>";

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [".$LINK_ID." : ".trim($LINK_NAME)."]");
	}

	if($command == "UPDATE" && trim($LINK_ID)){
		$cmd = " update REPGEN_LINK set LINK_NAME='".$LINK_NAME."', LINK_TEXT='".$LINK_TEXT."', UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' where LINK_ID=$LINK_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
//		echo "update : $cmd<br>";

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [".$LINK_ID." : ".trim($LINK_NAME)."]");
	}
	
	if($command == "DELETE" && trim($LINK_ID)){
		$cmd = " select * from REPGEN_LINK where LINK_ID=".$LINK_ID." ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$LINK_NAME = $data[LINK_NAME];
		$LINK_TEXT = $data[LINK_TEXT];
		
		$cmd = " delete from REPGEN_LINK where LINK_ID=".$LINK_ID." ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [".$LINK_ID." : ".trim($LINK_NAME)."]");
	}
	
	if($UPD){
		$cmd = " select LINK_NAME, LINK_TEXT
				 		from REPGEN_LINK
					  where LINK_ID=".$LINK_ID." ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$LINK_NAME = $data[LINK_NAME];
		$LINK_TEXT = $data[LINK_TEXT];
	} // end if
	
	if( (!$UPD && !$DEL && !$err_text) ){
		$LINK_ID = "";
		$LINK_NAME = "";
		$LINK_TEXT = "";
	} // end if
?>