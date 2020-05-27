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

	$COM_SEND_STATUS = "";
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
			$COM_SEND_STATUS = "S";
			break;
	} // end switch case
//----------------------------------------------------------------------------------

	if ($ORG_ID) {
		$search_org_id = $ORG_ID;
		$search_org_name = $ORG_NAME;
		
		$cmd = " select ORG_ID_REF from PER_ORG where ORG_ID=$search_org_id ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$search_department_id = $data[ORG_ID_REF];
		$search_department_name = "";
		if($search_department_id){
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$search_department_id ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$search_department_name = $data[ORG_NAME];
		}
	}

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$UPDATE_DATE = date("Y-m-d H:i:s");
	
// ============================================================	
	if($command == "DELETE" && trim($COM_ID) ){
		$cmd = " delete from PER_COMDTL where COM_ID=$COM_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
			
		$cmd = " delete from PER_COMMAND where COM_ID=$COM_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $COM_DEL_TITLE$COM_TYPE_NM [".trim($COM_ID)." : ".$COM_NO." : ".$COM_TYPE."]");
	}

	// ============================================================
	// เมื่อมีการส่งจากภูมิภาค
	if( $command == "SEND" && trim($COM_ID) && $SESS_USERGROUP_LEVEL > 4 ) {
		$cmd = " update PER_COMMAND set  
						COM_STATUS='S', UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' 
						where COM_ID=$COM_ID  ";
		$db_dpis->send_cmd($cmd);	
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $COM_SEND_TITLE$COM_TYPE_NM [".trim($COM_ID)." : ".$COM_NO." : ".$COM_TYPE."]");
	}		// 	if( $command == "SEND" && trim($COM_ID) ) 	

	if($UPD || $VIEW){		//ไปใช้ tab ที่ 2 แทนแล้ว ตรงนี้ไม่มีผล
		$cmd = " select 	COM_NO, COM_NAME, COM_DATE, COM_NOTE, COM_PER_TYPE, COM_TYPE, COM_CONFIRM, COM_STATUS, DEPARTMENT_ID
				from 	PER_COMMAND 
				where 	COM_ID=$COM_ID  "; 
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COM_NO = trim($data[COM_NO]);
		$COM_NAME = trim($data[COM_NAME]);
		$COM_NOTE = trim($data[COM_NOTE]);
		$COM_PER_TYPE = trim($data[COM_PER_TYPE]);
		$COM_CONFIRM = trim($data[COM_CONFIRM]);
		$COM_STATUS = trim($data[COM_STATUS]);

		$COM_DATE = show_date_format($data[COM_DATE], 1);

		$COM_TYPE = trim($data[COM_TYPE]);
		$COM_TYPE_NAME1 = "";
		$cmd = "select COM_NAME from PER_COMTYPE where COM_TYPE='$COM_TYPE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COM_TYPE_NAME1 = trim($data2[COM_NAME]);
		$DEPARTMENT_ID = $data[DEPARTMENT_ID];
		$MINISTRY_ID = $MINISTRY_NAME = "";
		if($DEPARTMENT_ID){
			$cmd = " select ORG_ID_REF, ORG_NAME from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$DEPARTMENT_NAME = $data[ORG_NAME];
			$MINISTRY_ID = $data[ORG_ID_REF];
		
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$MINISTRY_NAME = $data[ORG_NAME];
		}
	} // end if

	if( !$UPD && !$DEL && !$VIEW ){
		$COM_ID = "";
		$COM_NO = "";
		$COM_NAME = "";
		$COM_DATE = "";
		$COM_NOTE = "";
		$COM_PER_TYPE = "";
		$COM_CONFIRM = "";
		$COM_STATUS = "";
		
				
		$COM_TYPE = "";
		$COM_TYPE_NAME1 = "";
/*
		if($CTRL_TYPE < 3 && $SESS_USERGROUP_LEVEL < 3){ 
			$MINISTRY_ID = "";
			$MINISTRY_NAME = "";
			$search_ministry_id = "";
			$search_ministry_name = "";
		} // end if
		if($CTRL_TYPE < 4 && $SESS_USERGROUP_LEVEL < 4){ 
			$DEPARTMENT_ID = "";
			$DEPARTMENT_NAME = "";
			$search_department_id = "";
			$search_department_name = "";
		} // end if		
*/
	} // end if	
	
	//	เอาขึ้นเฉพาะที่ set มาจากกลุ่ม
	if(count($PERSON_TYPE)==1){
		foreach($PERSON_TYPE as $key=>$value){
			//$PER_TYPE = $key;
			$COM_PER_TYPE = $key;
		}
	}
	//print_r($PERSON_TYPE); 	echo $COM_PER_TYPE."+".$PER_TYPE;
?>