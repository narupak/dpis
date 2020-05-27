<?
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");	
	include("php_scripts/load_per_control.php");	

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if (is_null($ORG_ID) || $ORG_ID=="NULL") $ORG_ID=0;	
	if (($COM_SITE=="personal_workflow" && $ORG_ID) || ($SESS_USERGROUP_LEVEL < 5 && $ORG_ID)) {
			$search_org_id = $ORG_ID;
			$search_org_name = $ORG_NAME;
	} else if ($SESS_USERGROUP_LEVEL < 5){
			$search_org_id = "0";
			$search_org_name = "";
	}

	if(!$current_page) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	if (!$ORG_ID_DTL && $_POST[ORG_ID_DTL]) $search_org_id = $_POST[ORG_ID_DTL];
	if (!$ORG_ID_DTL) $ORG_ID_DTL = "NULL";
	if ($BKK_FLAG==1 && (trim($DEPARTMENT_ID)=="" || $DEPARTMENT_ID=="NULL" || is_null($DEPARTMENT_ID))) $DEPARTMENT_ID="''"; 
	
	$UPDATE_DATE = date("Y-m-d H:i:s");
	$tmp_COM_DATE =  save_date($COM_DATE);

	if( $command == "ADD" && trim(!$COM_ID) && trim($COM_NO) ){
		$cmd = " select max(COM_ID) as max_id from PER_COMMAND ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$COM_ID = $data[max_id] + 1;

		if (trim($ORG_ID_DTL)=="" || $ORG_ID_DTL=="NULL" || is_null($ORG_ID_DTL)) {
			$ORG_ID_DTL="0";
		}
	
		$cmd = " insert into PER_COMMAND (COM_ID, COM_NO, COM_NAME, COM_DATE, COM_NOTE, COM_PER_TYPE, 
						COM_TYPE, COM_CONFIRM, COM_STATUS, DEPARTMENT_ID, COM_LEVEL_SALP, UPDATE_USER, UPDATE_DATE, ORG_ID, COM_YEAR) 
						VALUES 	($COM_ID, '$COM_NO', '$COM_NAME', '$tmp_COM_DATE', '$COM_NOTE', $COM_PER_TYPE, 
						'$COM_TYPE', 0, '', $DEPARTMENT_ID, 9, $SESS_USERID, '$UPDATE_DATE', $ORG_ID_DTL, '$COM_YEAR') ";
		$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $COM_ADD_TITLE$COM_TYPE_NM [".trim($COM_ID)." : ".$COM_NO." : ".$COM_TYPE."]");
	
		// ===== เริ่มต้น insert ข้อมูลจากผู้สมควรได้เลื่อนขั้นเงินเดือน เข้าสู่ข้อมูลบัญชีแนบท้ายคำสั่ง (table PER_COMDTL) =====
		if ($COM_PER_TYPE==1) {
			include("data_bonus_comdtl_layer.php");
		} elseif ($COM_PER_TYPE==2) {
			include("data_bonus_comdtl_layeremp.php");
		} // if
		// ===== สิ้นสุด insert ข้อมูลจากผู้สมควรได้เลื่อนขั้นเงินเดือน เข้าสู่ข้อมูลบัญชีแนบท้ายคำสั่ง (table PER_COMDTL) ===== 
	}			// 	if( $command == "ADD" && trim(!$COM_ID) && trim($COM_NO) )
	
	if( $command == "UPDATE" && trim($COM_ID) ) {
		
		$cmd = " update PER_COMMAND set  
						COM_NO='$COM_NO', 
						COM_NAME='$COM_NAME', 
						COM_DATE='$tmp_COM_DATE', 
						COM_NOTE='$COM_NOTE', 
						COM_PER_TYPE=$COM_PER_TYPE, 
						COM_TYPE='$COM_TYPE', 
						COM_YEAR='$COM_YEAR', 
						UPDATE_USER=$SESS_USERID, 
						UPDATE_DATE='$UPDATE_DATE' 
				where COM_ID=$COM_ID  ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		//echo $cmd;

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $COM_EDIT_TITLE$COM_TYPE_NM [".trim($COM_ID)." : ".$COM_NO." : ".$COM_TYPE."]");
	}			// 	if( $command == "UPDATE" && trim($COM_ID) )
	

// ============================================================
	// ===== เมื่อมีการยืนยันข้อมูลของปัญชีแนบท้ายคำสั่ง ===== 
	if( $command == "COMMAND" && trim($COM_ID) ) {
		$COM_DATE =  save_date($COM_DATE);

		if ($COM_PER_TYPE==1) {
			include("data_bonus_comdtl_confirm_layer.php");
		} elseif ($COM_PER_TYPE==2) {
			include("data_bonus_comdtl_confirm_layeremp.php");
		} // if
		$cmd = " update PER_COMMAND set  
						COM_CONFIRM=1, UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' 
				where COM_ID=$COM_ID  ";
		$db_dpis->send_cmd($cmd);		

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $COM_CONFIRM_TITLE$COM_TYPE_NM [".trim($COM_ID)." : ".$COM_NO." : ".$COM_TYPE."]");
	}		// 	if ($COM_CONFIRM == 1 && ($command=="ADD" || $command=="UPDATE")) 	
// ============================================================

	if($command == "DELETE_COMDTL" && trim($COM_ID) && trim($PER_ID) ){
		$cmd = " delete from PER_COMDTL where COM_ID=$COM_ID and PER_ID=$PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $DEL_PERSON_TITLE$COM_TYPE_NM [".trim($COM_ID)." : ".$PER_ID."]");
		$PER_ID = "";
	}
	
	if($command == "DELETE_COMMAND" && trim($COM_ID) ){
		$cmd = " delete from PER_COMDTL where COM_ID=$COM_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
			
		$cmd = " delete from PER_COMMAND where COM_ID=$COM_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $COM_DEL_TITLE$COM_TYPE_NM [".trim($COM_ID)." : ".$COM_NO."]");
		$COM_ID = "";
	}
	
	if (trim($COM_ID)) {
		$cmd = "	select	COM_NO, a.COM_NAME, COM_DATE, COM_NOTE, COM_PER_TYPE, 
										a.COM_TYPE, COM_CONFIRM, COM_STATUS, b.COM_DESC, a.DEPARTMENT_ID, a.ORG_ID, COM_LEVEL_SALP, COM_YEAR, UPDATE_USER, UPDATE_DATE
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
		$COM_STATUS = trim($data[COM_STATUS]);
		$COM_LEVEL_SALP = trim($data[COM_LEVEL_SALP]);
		$COM_YEAR = trim($data[COM_YEAR]);
		
		$COM_TYPE = trim($data[COM_TYPE]);
		$COM_TYPE_NAME = trim($data[COM_DESC]);

		$ORG_ID_DTL = $data[ORG_ID];
		$DEPARTMENT_ID = $data[DEPARTMENT_ID];

		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);

		$cmd = " select ORG_ID_REF, ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_DTL ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$ORG_NAME_DTL = $data[ORG_NAME];
		
		if ($DEPARTMENT_ID) {
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
	}

	if( !$COM_ID ){
		$COM_ID = "";
		$COM_NO = "";
		$COM_NAME = "";
		$COM_DATE = "";
		$COM_NOTE = "";
		$COM_PER_TYPE = "1";
		$COM_CONFIRM = 0;
		$COM_STATUS = "";
		$COM_LEVEL_SALP = "";
		$COM_YEAR = "";
		
		$COM_TYPE = "";
		$COM_TYPE_NAME = "";
		$search_per_name = "";
		$search_per_surname = "";	
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);

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