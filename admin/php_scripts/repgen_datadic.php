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
			if($AP_SEQ_NO=="") { $cmd = " update DATA_DICTIONARY set DATA_THAI_NAME='$DATA_THAI_NAME', DATA_ENG_NAME='$DATA_ENG_NAME', MAP_TABLE_NAME='$MAP_TABLE_NAME', MAP_COLUMN_NAME='$MAP_COLUMN_NAME' where DATA_NO=$DATA_NO "; }
		else {	$cmd = " update DATA_DICTIONARY set DATA_THAI_NAME='$DATA_THAI_NAME', DATA_ENG_NAME='$DATA_ENG_NAME', MAP_TABLE_NAME='$MAP_TABLE_NAME', MAP_COLUMN_NAME='$MAP_COLUMN_NAME' where DATA_NO=$DATA_NO "; }
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			//echo "<hr>".$cmd;
		} // end foreach

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > จัดลำดับ DATA_DICTIONARY");
	} // end if

	if($command == "ADD"){
		$cmd = " select max(DATA_NO) as max_no from DATA_DICTIONARY";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$NEXT_DATA_NO = $data[max_no] + 1;

		$cmd = " insert into DATA_DICTIONARY (DATA_NO, DATA_THAI_NAME, DATA_ENG_NAME, MAP_TABLE_NAME, MAP_COLUMN_NAME, UPDATE_USER, UPDATE_DATE) values ($NEXT_DATA_NO, '$DATA_THAI_NAME', '$DATA_ENG_NAME', '$MAP_TABLE_NAME', '$MAP_COLUMN_NAME', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
//		echo "insert : $cmd<br>";

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [".$DATA_NO." : ".trim($DATA_THAI_NAME)." : ".trim($DATA_ENG_NAME)."]");
	}

	if($command == "UPDATE" && trim($DATA_NO)){
		$cmd = " update DATA_DICTIONARY set DATA_THAI_NAME='".$DATA_THAI_NAME."', DATA_ENG_NAME='".$DATA_ENG_NAME."', MAP_TABLE_NAME='".$MAP_TABLE_NAME."', MAP_COLUMN_NAME='".$MAP_COLUMN_NAME."', UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' where DATA_NO=$DATA_NO ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
//		echo "update : $cmd<br>";

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [".$DATA_NO." : ".trim($DATA_THAI_NAME)." : ".trim($DATA_ENG_NAME)."]");
	}
	
	if($command == "DELETE" && trim($DATA_NO)){
		$cmd = " select * from DATA_DICTIONARY where DATA_NO=".$DATA_NO." ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$DATA_THAI_NAME = $data[DATA_THAI_NAME];
		$DATA_ENG_NAME = $data[DATA_ENG_NAME];
		
		$cmd = " delete from DATA_DICTIONARY where DATA_NO=".$DATA_NO." ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [".$DATA_NO." : ".trim($DATA_THAI_NAME)." : ".trim($DATA_ENG_NAME)."]");
	}
	
	if($UPD){
		$cmd = " select DATA_THAI_NAME, DATA_ENG_NAME, MAP_TABLE_NAME, MAP_COLUMN_NAME
				 		from DATA_DICTIONARY
					  where DATA_NO=".$DATA_NO." ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$DATA_THAI_NAME = $data[DATA_THAI_NAME];
		$DATA_ENG_NAME = $data[DATA_ENG_NAME];
		$MAP_TABLE_NAME = $data[MAP_TABLE_NAME];
		$MAP_COLUMN_NAME = $data[MAP_COLUMN_NAME];
	} // end if
	
	if( (!$UPD && !$DEL && !$err_text && $command != "TABLE_GETCOLUMN") ){
		$DATA_NO = "";
		$DATA_THAI_NAME = "";
		$DATA_ENG_NAME = "";
		$MAP_TABLE_NAME = "";
		$MAP_COLUMN_NAME = "";
	} // end if
?>