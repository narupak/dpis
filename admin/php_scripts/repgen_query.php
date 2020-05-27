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
			if($AP_SEQ_NO=="") { $cmd = " update REPGEN_QUERY set QUERY_NAME='$QUERY_NAME', SELECTED_COLUMNS='$SELECTED_COLUMNS', Q_CONDITION='$Q_CONDITION', Q_TABLES='$Q_TABLES', Q_GROUP='$Q_GROUP', Q_ORDER='$Q_ORDER', Q_LINK='$Q_LINK' where QUERY_ID=$QUERY_ID "; }
		else {	$cmd = " update REPGEN_QUERY set QUERY_NAME='$QUERY_NAME', SELECTED_COLUMNS='$SELECTED_COLUMNS', Q_CONDITION='$Q_CONDITION', Q_TABLES='$Q_TABLES', Q_GROUP='$Q_GROUP', Q_ORDER='$Q_ORDER', Q_LINK='$Q_LINK' where QUERY_ID=$QUERY_ID "; }
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			//echo "<hr>".$cmd;
		} // end foreach

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > จัดลำดับ REPGEN_QUERY");
		$QUERY_ID = "";
		$running_tab="";
	} // end if

	if($command == "ADD"){
		$cmd = " select QUERY_ID from REPGEN_QUERY where QUERY_NAME='$QUERY_NAME' ";
		$cnt = $db_dpis->send_cmd($cmd);
		if ($cnt > 0) {
			echo "<script>alert('ชื่อ QUERY ซ้ำ โปรดตั้งชื่อใหม่');</script>";
		} else {
			$cmd = " select max(QUERY_ID) as max_no from REPGEN_QUERY ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$NEXT_QUERY_ID = $data[max_no] + 1;

			$cmd = " insert into REPGEN_QUERY (QUERY_ID, QUERY_NAME, SELECTED_COLUMNS, Q_CONDITION, Q_TABLES, Q_GROUP, Q_ORDER, UPDATE_USER, UPDATE_DATE, Q_LINK) values ($NEXT_QUERY_ID, '$QUERY_NAME', '$SELECTED_COLUMNS', '$Q_CONDITION', '$Q_TABLES', '$Q_GROUP', '$Q_ORDER', $SESS_USERID, '$UPDATE_DATE', '$Q_LINK') ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
//			echo "insert : $cmd<br>";

			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [".$QUERY_ID." : ".trim($QUERY_NAME)."]");
		}
	}

	if($command == "UPDATE" && trim($QUERY_ID)){
		$cmd = " update REPGEN_QUERY set QUERY_NAME='$QUERY_NAME', SELECTED_COLUMNS='$SELECTED_COLUMNS', Q_CONDITION='$Q_CONDITION', Q_TABLES='$Q_TABLES', Q_GROUP='$Q_GROUP', Q_ORDER='$Q_ORDER', Q_LINK='$Q_LINK', UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' where QUERY_ID=$QUERY_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
//		echo "update : $cmd<br>";

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [".$QUERY_ID." : ".trim($QUERY_NAME)."]");
	}
	
	if($command == "DELETE" && trim($QUERY_ID)){
		$cmd = " select * from REPGEN_QUERY where QUERY_ID=".$QUERY_ID." ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$QUERY_NAME = $data[QUERY_NAME];
		$SELECTED_COLUMNS = $data[SELECTED_COLUMNS];
		
		$cmd = " delete from REPGEN_QUERY where QUERY_ID=".$QUERY_ID." ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [".$QUERY_ID." : ".trim($QUERY_NAME)."]");
		$QUERY_ID = "";
		$running_tab="";
	}
	
//	if($UPD){
	if($QUERY_ID){
		$cmd = " select QUERY_NAME, SELECTED_COLUMNS, Q_CONDITION, Q_TABLES, Q_GROUP, Q_ORDER, Q_LINK from REPGEN_QUERY where QUERY_ID=".$QUERY_ID." ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
//		echo "$cmd<br>";
		$data = $db_dpis->get_array();
		$QUERY_NAME = $data[QUERY_NAME];
		$SELECTED_COLUMNS = $data[SELECTED_COLUMNS];
		$Q_CONDITION = $data[Q_CONDITION];
		$Q_VAR = sql_condition_var($Q_CONDITION);
//		echo "Q_VAR=$Q_VAR<br>";
		$Q_TABLES = $data[Q_TABLES];
		$Q_GROUP = $data[Q_GROUP];
		$Q_ORDER = $data[Q_ORDER];
		$Q_LINK = $data[Q_LINK];
		$UPD=1;
	} // end if
	
	if( (!$UPD && !$DEL && !$err_text) ){
		$QUERY_NAME = "";
		$SELECTED_COLUMNS = "";
		$Q_CONDITION = "";
		$Q_VAR = "";
		$Q_TABLES = "";
		$Q_GROUP = "";
		$Q_ORDER = "";
		$Q_LINK = "";
	} // end if
?>