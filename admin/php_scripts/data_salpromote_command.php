<?
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");	
	include("php_scripts/load_per_control.php");	

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	switch($CTRL_TYPE){
		case 2 :
			$PV_CODE = $PROVINCE_CODE;
			$PV_NAME = $PROVINCE_NAME;
			break;
		case 3 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			break;
		case 4 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			break;
	} // end switch case

	switch($SESS_USERGROUP_LEVEL){
		case 2 :
			$PV_CODE = $PROVINCE_CODE;
			$PV_NAME = $PROVINCE_NAME;
			break;
		case 3 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			break;
		case 4 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			break;
		case 5 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			$search_org_id = $ORG_ID;
			$search_org_name = $ORG_NAME;
			break;
	} // end switch case

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$UPDATE_DATE = date("Y-m-d H:i:s");

	if($command == "DELETE" && trim($COM_ID) ){
		$cmd = " delete from PER_COMDTL where COM_ID=$COM_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
			
		$cmd = " delete from PER_COMMAND where COM_ID=$COM_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูลบัญชีแนบท้ายคำสั่งเลื่อนขั้นเงินเดือน [".trim($COM_ID)." : ".$COM_NO." : ".$COM_TYPE."]");
	}


	if($UPD || $VIEW){
		$cmd = " select 	COM_NO, COM_NAME, COM_DATE, COM_NOTE, COM_PER_TYPE, COM_TYPE, COM_CONFIRM
				from 	PER_COMMAND 
				where 	COM_ID=$COM_ID  "; 
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COM_NO = trim($data[COM_NO]);
		$COM_NAME = trim($data[COM_NAME]);
		$COM_NOTE = trim($data[COM_NOTE]);
		$COM_PER_TYPE = trim($data[COM_PER_TYPE]);
		$COM_CONFIRM = trim($data[COM_CONFIRM]);

		$COM_DATE =  substr(trim($data[COM_DATE]), 8, 2) ."/". substr(trim($data[COM_DATE]), 5, 2) ."/". (substr(trim($data[COM_DATE]), 0, 4) + 543);

		$COM_TYPE = trim($data[COM_TYPE]);
		$COM_TYPE_NAME = "";
		$cmd = "select COM_NAME from PER_COMTYPE where COM_TYPE='$COM_TYPE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COM_TYPE_NAME = trim($data2[COM_NAME]);
	} // end if
	
	if( !$UPD && !$DEL && !$VIEW ){
		$COM_ID = "";
		$COM_NO = "";
		$COM_NAME = "";
		$COM_DATE = "";
		$COM_NOTE = "";
		$COM_PER_TYPE = "";
		$COM_CONFIRM = "";
				
		$COM_TYPE = "";
		$COM_TYPE_NAME = "";
		
		if($CTRL_TYPE < 3 && $SESS_USERGROUP_LEVEL < 3){ 
			$MINISTRY_ID = "";
			$MINISTRY_NAME = "";
		} // end if
		if($CTRL_TYPE < 4 && $SESS_USERGROUP_LEVEL < 4){ 
			$DEPARTMENT_ID = "";
			$DEPARTMENT_NAME = "";
		} // end if			
	} // end if		
?>