<?
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");	
	include("php_scripts/load_per_control.php");	

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$UPDATE_DATE = date("Y-m-d H:i:s");

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	if (!$ORG_ID_DTL) $ORG_ID_DTL = "NULL";
	if (!$search_org_id) $search_org_id = "NULL";
	if (($BKK_FLAG==1 || $MFA_FLAG==1) && (trim($DEPARTMENT_ID)=="" || $DEPARTMENT_ID=="NULL" || is_null($DEPARTMENT_ID))) $DEPARTMENT_ID="''";
	
	//$COM_LEVEL_SALP = (trim($COM_LEVEL_SALP))? $COM_LEVEL_SALP : 1; 
	
	if ($COM_CONDITION==3 && $PER_TYPE==1) {
		$cmd = " select LAYER_SALARY_MAX from PER_LAYER 
						where LAYER_TYPE=0 and LEVEL_NO = 'O1' and LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		//echo "$cmd<br>";			
		$data = $db_dpis->get_array();
		$LAYER_SALARY_MAX = $data[LAYER_SALARY_MAX];
		if ($LAYER_SALARY_MAX != 19100) {
			$error_layer = "1";
		}
	}

	if( ($command == "ADD" || $command == "ADD_ALL") && trim(!$COM_ID) && trim($COM_NO) ){
		$cmd = " select max(COM_ID) as max_id from PER_COMMAND ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$COM_ID = $data[max_id] + 1;

		$COM_DATE =  save_date($COM_DATE);
		$COM_EFFECTIVEDATE =  save_date($COM_EFFECTIVEDATE);
		
		$cmd = " insert into PER_COMMAND (COM_ID, COM_NO, COM_NAME, COM_DATE, COM_NOTE, COM_PER_TYPE, 
						COM_TYPE, COM_CONFIRM, COM_STATUS, DEPARTMENT_ID, COM_LEVEL_SALP, UPDATE_USER, UPDATE_DATE, ORG_ID) 
						VALUES 	($COM_ID, '$COM_NO', '$COM_NAME', '$COM_DATE', '$COM_NOTE', $COM_PER_TYPE, 
						'$COM_TYPE', 0, '', $DEPARTMENT_ID, 9, $SESS_USERID, '$UPDATE_DATE', $ORG_ID_DTL) ";
		$db_dpis->send_cmd($cmd);
		//echo "-> $cmd";
// $db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $COM_ADD_TITLE$COM_TYPE_NM [".trim($COM_ID)." : ".$COM_NO." : ".$COM_TYPE."]");
	
		if ($command == "ADD") {
			// ===== เริ่มต้น insert ข้อมูลจากผู้สมควรได้เลื่อนขั้นเงินเดือน เข้าสู่ข้อมูลบัญชีแนบท้ายคำสั่ง (table PER_COMDTL) =====
			if ($COM_PER_TYPE==1) {
				include("data_salpromote_new_comdtl_layer.php");
			} elseif ($COM_PER_TYPE==2) {
				include("data_salpromote_new_comdtl_layeremp.php");
			} elseif ($COM_PER_TYPE==3) {
				include("data_salpromote_new_comdtl_layerempser.php");
			} // if
			// ===== สิ้นสุด insert ข้อมูลจากผู้สมควรได้เลื่อนขั้นเงินเดือน เข้าสู่ข้อมูลบัญชีแนบท้ายคำสั่ง (table PER_COMDTL) ===== 
		}
		$COM_EFFECTIVEDATE = show_date_format($COM_EFFECTIVEDATE, 1);
	}			// 	if( $command == "ADD" && trim(!$COM_ID) && trim($COM_NO) )
	
	if( $command == "UPDATE" && trim($COM_ID) ) {
		$COM_DATE =  save_date($COM_DATE);
		
		$cmd = " update PER_COMMAND set  
						COM_NO='$COM_NO', COM_NAME='$COM_NAME', COM_DATE='$COM_DATE', COM_NOTE='$COM_NOTE', 
						COM_PER_TYPE=$COM_PER_TYPE, COM_TYPE='$COM_TYPE', 
						UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' 
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
			include("data_salpromote_new_comdtl_confirm_layer.php");
		} elseif ($COM_PER_TYPE==2) {
			include("data_salpromote_new_comdtl_confirm_layeremp.php");
		} elseif ($COM_PER_TYPE==3) {
			include("data_salpromote_new_comdtl_confirm_layerempser.php");
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

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $COM_DEL_TITLE$COM_TYPE_NM [".trim($COM_ID)." : ".$PER_ID."]");
		$COM_ID = "";
		$COM_CONDITION = 1;
	}
	
	if (trim($COM_ID)) {
		$cmd = "	select	COM_NO, a.COM_NAME, COM_DATE, COM_NOTE, COM_PER_TYPE, 
										a.COM_TYPE, COM_CONFIRM, COM_STATUS, b.COM_DESC, a.DEPARTMENT_ID , a.ORG_ID, UPDATE_USER, UPDATE_DATE
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

		if($DEPARTMENT_ID){	//กรณีที่เพิ่มของ กทม แบบไม่มี DEPARTMENT_ID ก็จะเอาค่า MINISTRY_NAME จาก session load_per_control
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
		
		if($ORG_ID_DTL){
			$cmd = " select ORG_ID_REF, ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_DTL ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$ORG_NAME_DTL = $data[ORG_NAME];
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
		$COM_EFFECTIVEDATE = "";
		$COM_SAL_PERCENT = "";
		$COM_LEVEL_NO = "";
		
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