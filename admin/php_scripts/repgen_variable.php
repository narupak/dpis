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
			if($AP_SEQ_NO=="") { $cmd = " update REPGEN_VARIABLE set VAR_NAME='$VAR_NAME', VAR_FORMULA='$VAR_FORMULA', VAR_LINK='$VAR_LINK' where VAR_ID=$VAR_ID "; }
		else {	$cmd = " update REPGEN_VARIABLE set VAR_NAME='$VAR_NAME', VAR_FORMULA='$VAR_FORMULA', VAR_LINK='$VAR_LINK' where VAR_ID=$VAR_ID "; }
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			//echo "<hr>".$cmd;
		} // end foreach

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > จัดลำดับ REPGEN_VARIABLE");
	} // end if

	if($command == "ADD"){
		$cmd = " select max(VAR_ID) as max_no from REPGEN_VARIABLE ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$NEXT_VAR_ID = $data[max_no] + 1;

		$cmd = " insert into REPGEN_VARIABLE (VAR_ID, VAR_NAME, VAR_FORMULA, VAR_LINK, UPDATE_USER, UPDATE_DATE) values ($NEXT_VAR_ID, '$VAR_NAME', '$VAR_FORMULA', '$VAR_LINK', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
//		echo "insert : $cmd<br>";

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [".$VAR_ID." : ".trim($VAR_NAME)."]");
	}

	if($command == "UPDATE" && trim($VAR_ID)){
		$cmd = " update REPGEN_VARIABLE set VAR_NAME='$VAR_NAME', VAR_FORMULA='$VAR_FORMULA', VAR_LINK='$VAR_LINK', UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' where VAR_ID=$VAR_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
//		echo "update : $cmd<br>";

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [".$VAR_ID." : ".trim($VAR_NAME)."]");
	}
	
	if($command == "DELETE" && trim($VAR_ID)){
		$cmd = " select * from REPGEN_VARIABLE where VAR_ID=".$VAR_ID." ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$VAR_NAME = $data[VAR_NAME];
		$VAR_FORMULA = $data[VAR_FORMULA];
		
		$cmd = " delete from REPGEN_VARIABLE where VAR_ID=".$VAR_ID." ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [".$VAR_ID." : ".trim($VAR_NAME)."]");
	}
	
//	if($UPD){
	if($VAR_ID){
		$cmd = " select VAR_NAME, VAR_FORMULA, VAR_LINK from REPGEN_VARIABLE where VAR_ID=".$VAR_ID." ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
//		echo "$cmd<br>";
		$data = $db_dpis->get_array();
		$VAR_NAME = $data[VAR_NAME];
		$VAR_FORMULA = stripslashes($data[VAR_FORMULA]);
		$VAR_LINK = stripslashes($data[VAR_LINK]);
		$UPD=1;
	} // end if
	
	if( (!$UPD && !$DEL && !$err_text) ){
		$VAR_ID = "";
		$VAR_NAME = "";
		$VAR_FORMULA = "";
		$VAR_LINK = "";
	} // end if
?>