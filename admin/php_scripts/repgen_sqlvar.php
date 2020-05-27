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
			if($AP_SEQ_NO=="") { $cmd = " update REPGEN_SQLVAR set SQL_VAR_NAME='$SQL_VAR_NAME', SQL_SELECT='$SQL_SELECT', SQL_WHERE='$SQL_WHERE', SQL_GROUP='$SQL_GROUP', SQL_ORDER='$SQL_ORDER' where SQL_VAR_ID=$SQL_VAR_ID "; }
		else { $cmd = " update REPGEN_SQLVAR set SQL_VAR_NAME='$SQL_VAR_NAME', SQL_SELECT='$SQL_SELECT', SQL_WHERE='$SQL_WHERE', SQL_GROUP='$SQL_GROUP', SQL_ORDER='$SQL_ORDER' where SQL_VAR_ID=$SQL_VAR_ID "; }
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			//echo "<hr>".$cmd;
		} // end foreach

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > จัดลำดับ REPGEN_SQLVAR");
	} // end if

	if($command == "ADD"){
		$cmd = " select max(SQL_VAR_ID) as max_no from REPGEN_SQLVAR ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$NEXT_VAR_ID = $data[max_no] + 1;

		$cmd = " insert into REPGEN_SQLVAR (SQL_VAR_ID, SQL_VAR_NAME, SQL_SELECT, SQL_WHERE, SQL_GROUP, SQL_ORDER, UPDATE_USER, UPDATE_DATE) values ($NEXT_COND_ID, '$CONDITION_NAME', '$CONDITION_TEXT', $SESS_USERID, '$UPDATE_DATE') ";
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
		$cmd = " select * from REPGEN_SQLVAR where SQL_VAR_ID=".$SQL_VAR_ID." ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$SQL_VAR_NAME = $data[SQL_VAR_NAME];
		$SQL_SELECT = $data[SQL_SELECT];
		
		$cmd = " delete from REPGEN_SQLVAR where SQL_VAR_ID=".$SQL_VAR_ID." ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [".$SQL_VAR_ID." : ".trim($SQL_VAR_NAME)."]");
	}
	
	if($UPD){
		$cmd = " select SQL_VAR_NAME, SQL_SELECT
				 		from REPGEN_SQLVAR
					  where SQL_VAR_ID=".$SQL_VAR_ID." ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$SQL_VAR_NAME = $data[SQL_VAR_NAME];
		$SQL_SELECT = $data[SQL_SELECT];
	} // end if
	
	if( (!$UPD && !$DEL && !$err_text) ){
		$SQL_VAR_ID = "";
		$SQL_VAR_NAME = "";
		$SQL_SELECT = "";
		$SQL_WHERE = "";
		$SQL_GROUP = "";
		$SQL_ORDER = "";
	} // end if
?>