<?
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");	
	include("php_scripts/load_per_control.php");	

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
	$INV_STATUS = (trim($INV_STATUS))? $INV_STATUS : 1;
	
	if($command == "DELETE" && trim($INV_ID) ){

		//---chk invest2 ----------------------------------------------
		$cmd = " select * from PER_INVEST2 where INV_ID_REF=$INV_ID ";
		$count_result_invest2 = $db_dpis->send_cmd($cmd);
		if($count_result_invest2 > 0){
			$error_delete_invest1=1;
		}else{
			$cmd = " delete from PER_INVEST1DTL where INV_ID=$INV_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
				
			$cmd = " delete from PER_INVEST1 where INV_ID=$INV_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		}
		//-------------------------------------------------------------
	
		/****$cmd = " delete from PER_INVEST1DTL where INV_ID=$INV_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
			
		$cmd = " delete from PER_INVEST1 where INV_ID=$INV_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();****/

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูลการสอบข้อเท็จจริง [".trim($INV_ID)." : ".$INV_NO."]");
	}

	if( !$UPD && !$DEL && !$VIEW ){
		$INV_ID = "";
		$INV_NO = "";
		$INV_DATE = "";
		$INV_DESC = "";
		$INV_STATUS = 1;
		$CRD_CODE = "";
		$CRD_NAME = "";
		$CR_NAME = "";
		$INV_DETAIL = "";
	} // end if		
?>