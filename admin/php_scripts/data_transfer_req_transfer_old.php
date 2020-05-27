<?	
	include("../php_scripts/connect_database.php");
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");	
	include("php_scripts/load_per_control.php");

//	$db_dpis_n = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	// ==== use for testing phase =====
	$db_dpis_n = new connect_db($db_host, $db_name, $db_user, $db_pwd);
	// ==========================

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$UPDATE_DATE = date("Y-m-d H:i:s");

	if($command == "DELETE" && $TR_ID){
		$cmd = " select TR_CARDNO from PER_TRANSFER_REQ where TR_ID=$TR_ID and DEPARTMENT_ID=$DEPARTMENT_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$TR_CARDNO = $data[TR_CARDNO];
		
		$cmd = " delete from PER_TRANSFER_REQ where TR_ID=$TR_ID and DEPARTMENT_ID=$DEPARTMENT_ID ";
		$db_dpis->send_cmd($cmd);

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูลข้าราชการ/ลูกจ้างขอโอน [$DEPARTMENT_ID : $TR_ID : $TR_CARDNO : $TR_NAME]");
	} // end if
?>