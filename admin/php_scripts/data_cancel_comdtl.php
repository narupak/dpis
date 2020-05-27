<?
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");	
	include("php_scripts/load_per_control.php");		

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	//-----------------------------------------------------------------
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
		case 6 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			$search_org_id = $ORG_ID;
			$search_org_name = $ORG_NAME;
			$search_org_id_1 = $ORG_ID_1;
			$search_org_name_1 = $ORG_NAME_1;
			break;
	} // end switch case
	//-----------------------------------------------------------------

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	if (!$search_org_id) $search_org_id = "NULL";
	
	$UPDATE_DATE = date("Y-m-d H:i:s");

// ============================================================
	if( $command == "ADD" && trim(!$COM_ID) && trim($COM_NO) ){
		$cmd = " select max(COM_ID) as max_id from PER_COMMAND ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$COM_ID = $data[max_id] + 1;

		$COM_DATE =  save_date($COM_DATE);

		$cmd = " insert into PER_COMMAND 
						(COM_ID, COM_NO, COM_NAME, COM_DATE, COM_NOTE, COM_PER_TYPE, 
						COM_TYPE, COM_CONFIRM, DEPARTMENT_ID, UPDATE_USER, UPDATE_DATE, ORG_ID) 
					 VALUES 
						($COM_ID, '$COM_NO', '$COM_NAME', '$COM_DATE', '$COM_NOTE', $COM_PER_TYPE, 
						'$COM_TYPE', 0, $search_department_id, $SESS_USERID, '$UPDATE_DATE', $search_org_id) ";
		$db_dpis->send_cmd($cmd);
//		echo "=> $cmd";
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูลบัญชีแนบท้ายคำสั่งยกเลิกคำสั่งเดิม [$search_department_id : ".trim($COM_ID)." : ".$COM_NO." : ".$COM_TYPE."]");
	}			// 	if( $command == "ADD" && trim(!$COM_ID) && trim($COM_NO) )

	if( $command == "UPDATE" && trim($COM_ID) ) {
		$COM_DATE =  save_date($COM_DATE);

		// , ORG_ID=$search_org_id,   			//ไม่ควรให้แก้ไข DEPARTMENT_ID / ORG_ID ได้ ควรสร้างมาตั้งแต่ตอนเพิ่มบัญชี **** 
		$cmd = " update PER_COMMAND set  
						COM_NO='$COM_NO', COM_NAME='$COM_NAME', COM_DATE='$COM_DATE', COM_NOTE='$COM_NOTE', 
						COM_PER_TYPE=$COM_PER_TYPE, COM_TYPE='$COM_TYPE' ,
						UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' 
				where COM_ID=$COM_ID and DEPARTMENT_ID=$search_department_id  ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูลบัญชีแนบท้ายคำสั่งยกเลิกคำสั่งเดิม [$search_department_id : ".trim($COM_ID)." : ".$COM_NO." : ".$COM_TYPE."]");
	}			// 	if( $command == "UPDATE" && trim($COM_ID) )
	
// ============================================================
	// เมื่อมีการยืนยันข้อมูลของปัญชีแนบท้ายคำสั่ง
	if( $command == "COMMAND" && trim($COM_ID) ) {
		$COM_DATE =  save_date($COM_DATE);
		$cmd = " update PER_COMMAND set  
						COM_CONFIRM=1, UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' 
				where COM_ID=$COM_ID  ";
		$db_dpis->send_cmd($cmd);		

		$cmd = " update PER_COMMAND set  
						COM_STATUS=0, UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' 
				where COM_ID=$COM_ID_CANCEL  ";
		$db_dpis->send_cmd($cmd);		

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ยกเลิกคำสั่งเดิมเมื่อยืนยันข้อมูลบัญชีแนบท้ายคำสั่งยกเลิกคำสั่งเดิม [$COM_ID : $COM_ID_CANCEL]");
	}		// 	if( $command == "COMMAND" && trim($COM_ID) ) 	
// ============================================================

	if($command == "DELETE_COMMAND" && trim($COM_ID) ){
		$cmd = " delete from PER_COMDTL where COM_ID=$COM_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
			
		$cmd = " delete from PER_COMMAND where COM_ID=$COM_ID and DEPARTMENT_ID=$search_department_id ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูลบัญชีแนบท้ายคำสั่งยกเลิกคำสั่งเดิม [$search_department_id : ".trim($COM_ID)." : ".$PER_ID."]");
		$COM_ID = "";
	}
	
	if (trim($COM_ID)) {
		$cmd = "	select	COM_NO, a.COM_NAME, COM_DATE, COM_NOTE, COM_PER_TYPE, 
										a.COM_TYPE, COM_CONFIRM, b.COM_DESC, a.DEPARTMENT_ID, UPDATE_USER, UPDATE_DATE 
							from	PER_COMMAND a, PER_COMTYPE b
							where	COM_ID=$COM_ID  and a.COM_TYPE=b.COM_TYPE	";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		
		$COM_NO = trim($data[COM_NO]);
		$COM_NAME = trim($data[COM_NAME]);
		$COM_DATE = show_date_format($data[COM_DATE], 1);
		$COM_NOTE = trim($data[COM_NOTE]);
		$COM_PER_TYPE = trim($data[COM_PER_TYPE]);
		$COM_CONFIRM = trim($data[COM_CONFIRM]);
		
		$COM_TYPE = trim($data[COM_TYPE]);
		$COM_TYPE_NAME = trim($data[COM_DESC]);
		
		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);

		$search_department_id = $data[DEPARTMENT_ID];
		$cmd = " select ORG_ID_REF, ORG_NAME from PER_ORG where ORG_ID=$search_department_id ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$search_department_name = $data[ORG_NAME];
		$search_ministry_id = $data[ORG_ID_REF];

		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$search_ministry_id ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$search_ministry_name = $data[ORG_NAME];
	}

	if( !$COM_ID ){
		$COM_ID = "";
		$COM_NO = "";
		$COM_NAME = "";
		$COM_DATE = "";
		$COM_NOTE = "";
		$COM_PER_TYPE = "";
		$COM_CONFIRM = "";
		
		$COM_TYPE = "";
		$COM_TYPE_NAME = "";		
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);

		if($CTRL_TYPE < 3 && $SESS_USERGROUP_LEVEL < 3){ 
			$search_ministry_id = "";
			$search_ministry_name = "";
		} // end if
		if($CTRL_TYPE < 4 && $SESS_USERGROUP_LEVEL < 4){ 
			$search_department_id = "";
			$search_department_name = "";
		} // end if	
	} // end if		
?>