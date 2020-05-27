<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	if($PER_ID){
		if($DPISDB=="odbc"){
			$cmd = " select 	PRE.PN_NAME, PER.PER_NAME, PER.PER_SURNAME, PER.PER_TYPE, PER.PER_CARDNO,PER.DEPARTMENT_ID
					from		PER_PERSONAL as PER
							left join PER_PRENAME as PRE on (PER.PN_CODE=PRE.PN_CODE)
					where	PER.PER_ID=$PER_ID ";
		}elseif($DPISDB=="oci8"){
			$cmd = " select 	PER_PRENAME.PN_NAME, PER_PERSONAL.PER_NAME, PER_PERSONAL.PER_SURNAME, 
							PER_PERSONAL.PER_TYPE, PER_PERSONAL.PER_CARDNO,PER_PERSONAL.DEPARTMENT_ID
 					from		PER_PERSONAL, PER_PRENAME
					where	PER_PERSONAL.PN_CODE=PER_PRENAME.PN_CODE(+) and 
							PER_PERSONAL.PER_ID=$PER_ID ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select 	PRE.PN_NAME, PER.PER_NAME, PER.PER_SURNAME, PER.PER_TYPE, PER.PER_CARDNO,PER.DEPARTMENT_ID
					from		PER_PERSONAL as PER
							left join PER_PRENAME as PRE on (PER.PN_CODE=PRE.PN_CODE)
					where	PER.PER_ID=$PER_ID ";
		} // end if
		
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PER_NAME = $data[PN_NAME] . $data[PER_NAME] ." ". $data[PER_SURNAME];
		$PER_CARDNO = trim($data[PER_CARDNO]);
		$PER_ID_DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);
	} // end if

	$UPDATE_DATE = date("Y-m-d H:i:s");

	if ($command == "SETFLAG") {
		$setflagaudit =  implode(",",$list_audit_id);
//		echo "$setflagaudit";
		$cmd = " update PER_APPROVE_RESOLUTION set AUDIT_FLAG = 'N' where AR_ID in (".stripslashes($current_list).") ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		$cmd = " update PER_APPROVE_RESOLUTION set AUDIT_FLAG = 'Y' where AR_ID in (".stripslashes($setflagaudit).") ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการตรวจสอบข้อมูล");
	}

	if($command=="ADD" && $PER_ID){
		$cmd = " select max(AR_ID) as max_id from PER_APPROVE_RESOLUTION ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$AR_ID = $data[max_id] + 1;
	
		$AR_DESC = trim($AR_DESC);
		$cmd = " insert into PER_APPROVE_RESOLUTION (AR_ID, PER_ID, AR_DESC, AR_REMARK, PER_CARDNO, UPDATE_USER, UPDATE_DATE)
						values ($AR_ID, $PER_ID, '$AR_DESC', '$AR_REMARK', '$PER_CARDNO', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มมติอนุมัติ อนุญาตต่าง ๆ [$PER_ID : $AR_ID : $AR_DESC]");
		$ADD_NEXT = 1;
	}

	if($command=="UPDATE" && $PER_ID && $AR_ID){
		$cmd = " UPDATE PER_APPROVE_RESOLUTION SET
						AR_DESC='$AR_DESC', 
						AR_REMARK='$AR_REMARK', 
						PER_CARDNO='$PER_CARDNO', 
						UPDATE_USER=$SESS_USERID, 
						UPDATE_DATE='$UPDATE_DATE'
					WHERE AR_ID=$AR_ID ";
		$db_dpis->send_cmd($cmd);
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขมติอนุมัติ อนุญาตต่าง ๆ [$PER_ID : $AR_ID : $AR_DESC]");
	}
	
	if($command=="DELETE" && $PER_ID && $AR_ID){
		$cmd = " select AR_DESC from PER_APPROVE_RESOLUTION where AR_ID=$AR_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$SS_CODE = $data[SS_CODE];
		
		$cmd = " delete from PER_APPROVE_RESOLUTION where AR_ID=$AR_ID ";
		$db_dpis->send_cmd($cmd);
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบมติอนุมัติ อนุญาตต่าง ๆ [$PER_ID : $AR_ID : $AR_DESC]");
	} // end if

	if(($UPD && $PER_ID && $AR_ID) || ($VIEW && $PER_ID && $AR_ID)){
		$cmd = " SELECT 	AR_ID, AR_DESC, AR_REMARK, UPDATE_USER, UPDATE_DATE  
						FROM			PER_APPROVE_RESOLUTION 
						WHERE		AR_ID=$AR_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$AR_ID = $data[AR_ID];
		$AR_DESC = $data[AR_DESC];
		$AR_REMARK = $data[AR_REMARK];
		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);
	} // end if
	
	if( !$UPD && !$VIEW ){
		unset($AR_ID);
		unset($AR_DESC);
		unset($AR_REMARK);
	
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
	} // end if
?>