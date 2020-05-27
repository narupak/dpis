<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");

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
		$cmd = " update PER_EXTRA_INCOMEHIS set AUDIT_FLAG = 'N' where EXINH_ID in (".stripslashes($current_list).") ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		$cmd = " update PER_EXTRA_INCOMEHIS set AUDIT_FLAG = 'Y' where EXINH_ID in (".stripslashes($setflagaudit).") ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการตรวจสอบข้อมูล");
	}

	if($command=="ADD" && $PER_ID){
		$EXINH_EFFECTIVEDATE =  save_date($EXINH_EFFECTIVEDATE);
		$EXINH_ENDDATE =  save_date($EXINH_ENDDATE);
	
		$cmd = " select max(EXINH_ID) as max_id from PER_EXTRA_INCOMEHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$EXINH_ID = $data[max_id] + 1;
		
		$cmd = " 		insert into PER_EXTRA_INCOMEHIS
						 	(EXINH_ID, PER_ID, EXINH_EFFECTIVEDATE, EXIN_CODE, EXINH_AMT, EXINH_ENDDATE, UPDATE_USER, UPDATE_DATE)
					values
						 	($EXINH_ID, $PER_ID, '$EXINH_EFFECTIVEDATE', '$EXIN_CODE', '$EXINH_AMT', '$EXINH_ENDDATE', $SESS_USERID, '$UPDATE_DATE')   ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มประวัติการรับเงินเพิ่มพิเศษ [$PER_ID : $EXINH_ID : $EXIN_CODE]");
	} // end if
	

	if($command=="UPDATE" && $PER_ID && $EXINH_ID){
		$EXINH_EFFECTIVEDATE =  save_date($EXINH_EFFECTIVEDATE);
		$EXINH_ENDDATE =  save_date($EXINH_ENDDATE);
	
		$cmd = " UPDATE PER_EXTRA_INCOMEHIS SET
					EXINH_EFFECTIVEDATE='$EXINH_EFFECTIVEDATE', EXIN_CODE='$EXIN_CODE', EXINH_AMT='$EXINH_AMT', 
					EXINH_ENDDATE='$EXINH_ENDDATE', UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' 
					WHERE EXINH_ID=$EXINH_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขประวัติการรับเงินเพิ่มพิเศษ [$PER_ID : $EXINH_ID : $EXIN_CODE]");
	} // end if

	
	if($command=="DELETE" && $PER_ID && $EXINH_ID){
		$cmd = " select EXIN_CODE from PER_EXTRA_INCOMEHIS where EXINH_ID=$EXINH_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$EXIN_CODE = $data[EXIN_CODE];
		
		$cmd = " delete from PER_EXTRA_INCOMEHIS where EXINH_ID=$EXINH_ID ";
		$db_dpis->send_cmd($cmd);

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบประวัติการรับเงินเพิ่มพิเศษ [$PER_ID : $EXINH_ID : $EXIN_CODE]");
	} // end if

	if(($UPD && $PER_ID && $EXINH_ID) || ($VIEW && $PER_ID && $EXINH_ID)){
		$cmd = "	SELECT 		EXINH_ID, EXINH_EFFECTIVEDATE, peh.EXIN_CODE, pet.EXIN_NAME, EXINH_AMT, EXINH_ENDDATE
				FROM		PER_EXTRA_INCOMEHIS peh, PER_EXTRA_INCOME_TYPE pet
				WHERE		peh.EXINH_ID=$EXINH_ID and peh.EXIN_CODE=pet.EXIN_CODE   ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$EXINH_ID = $data[EXINH_ID];
		$EXIN_CODE = $data[EXIN_CODE];
		$EXIN_NAME = $data[EXIN_NAME];		
		$EXINH_AMT = $data[EXINH_AMT];
		
		$EXINH_EFFECTIVEDATE = show_date_format($data[EXINH_EFFECTIVEDATE], 1);
		$EXINH_ENDDATE = show_date_format($data[EXINH_ENDDATE], 1);
	} // end if
	
	if( !$UPD && !$VIEW ){
		unset($EXINH_ID);
		unset($EXINH_EFFECTIVEDATE);
		unset($EXINH_AMT);		
		unset($EXINH_ENDDATE);
	
		unset($EXIN_CODE);
		unset($EXIN_NAME);
	} // end if
?>