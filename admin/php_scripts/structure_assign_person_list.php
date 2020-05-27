<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$UPDATE_DATE = date("Y-m-d H:i:s");

	if($command == "DELETE" && $PER_ID){
		$cmd = " select PER_NAME, PER_SURNAME, PER_CARDNO from PER_PERSONAL where PER_ID=$PER_ID";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PER_NAME = $data[PER_NAME];
		$PER_SURNAME = $data[PER_SURNAME];
		$PER_CARDNO = $data[PER_CARDNO];
		
		$cmd = " delete from PER_ORG_JOB where PER_ID=$PER_ID";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูลโครงสร้างการแบ่งงานตามคำสั่งมอบหมายงาน [ $PER_ID : $PER_NAME $PER_SURNAME : $PER_CARDNO ]");
	} // end if
	
	if($DPISDB=="odbc"){
		$cmd = " select 	distinct PER_ID
						 from		PER_ORG_JOB
					  ";
	}elseif($DPISDB=="oci8"){
		$cmd = " select 	distinct PER_ID
						 from		PER_ORG_JOB
					  ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select 	distinct PER_ID
						 from		PER_ORG_JOB
					  ";
	} // end if

	$ASSIGNED_PER_ID = "";
	$db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	while($data = $db_dpis->get_array()){
		if($ASSIGNED_PER_ID) $ASSIGNED_PER_ID .= ",";
		$ASSIGNED_PER_ID .= $data[PER_ID];
	} // end while		
//	echo "ASSIGNED : $ASSIGNED_PER_ID";
?>