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
		$PER_TYPE = trim($data[PER_TYPE]);
		$PER_CARDNO = trim($data[PER_CARDNO]);
		$PER_ID_DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);
	} // end if

	$UPDATE_DATE = date("Y-m-d H:i:s");

	if ($command == "SETFLAG") {
		$setflagaudit =  implode(",",$list_audit_id);
//		echo "$setflagaudit";
		$cmd = " update PER_WORK_CYCLEHIS set AUDIT_FLAG = 'N' where WH_ID in (".stripslashes($current_list).") ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		$cmd = " update PER_WORK_CYCLEHIS set AUDIT_FLAG = 'Y' where WH_ID in (".stripslashes($setflagaudit).") ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการตรวจสอบข้อมูล");
	}

	if($command=="ADD" && $PER_ID){
		$START_DATE = save_date($START_DATE); 
		$END_DATE = save_date($END_DATE); 
	
		$cmd = " select max(WH_ID) as max_id from PER_WORK_CYCLEHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$WH_ID = $data[max_id] + 1;
		
		$cmd = " insert into PER_WORK_CYCLEHIS	(WH_ID, PER_ID, WC_CODE, START_DATE, END_DATE, REMARK, UPDATE_USER, UPDATE_DATE)
						  values ($WH_ID, $PER_ID, '$WC_CODE', '$START_DATE', '$END_DATE', '$REMARK', $SESS_USERID, '$UPDATE_DATE')   ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูลประวัติรอบการมาปฏิบัติราชการ [$PER_ID : $WH_ID : $WC_CODE]");
		$ADD_NEXT = 1;
	} // end if

	if($command=="UPDATE" && $PER_ID && $WH_ID){
		$START_DATE = save_date($START_DATE); 
		$END_DATE = save_date($END_DATE); 
	
		$cmd = " UPDATE PER_WORK_CYCLEHIS SET WC_CODE='$WC_CODE', 
					START_DATE='$START_DATE', 
					END_DATE='$END_DATE',  
					REMARK='$REMARK',  
					UPDATE_USER=$SESS_USERID, 
					UPDATE_DATE='$UPDATE_DATE'
				WHERE WH_ID=$WH_ID";
		$db_dpis->send_cmd($cmd);
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูลประวัติรอบการมาปฏิบัติราชการ [$PER_ID : $WH_ID : $WC_CODE]");
	} // end if
	
	if($command=="DELETE" && $PER_ID && $WH_ID){
		$cmd = " select WC_CODE from PER_WORK_CYCLEHIS where WH_ID=$WH_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$WC_CODE = $data[WC_CODE];
		
		$cmd = " delete from PER_WORK_CYCLEHIS where WH_ID=$WH_ID ";
		$db_dpis->send_cmd($cmd);
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูลประวัติรอบการมาปฏิบัติราชการ [$PER_ID : $WH_ID : $WC_CODE]");
	} // end if

	if(($UPD && $PER_ID && $WH_ID) || ($VIEW && $PER_ID && $WH_ID)){
		$cmd = "	SELECT 	WH_ID, a.WC_CODE, b.WC_NAME, START_DATE, END_DATE, REMARK, a.UPDATE_USER, a.UPDATE_DATE   
							FROM		PER_WORK_CYCLEHIS a, PER_WORK_CYCLE b  
						WHERE		WH_ID=$WH_ID and a.WC_CODE=b.WC_CODE  ";	
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$START_DATE = show_date_format(trim($data[START_DATE]), 1);
		$END_DATE = show_date_format(trim($data[END_DATE]), 1);
		$REMARK = $data[REMARK];
		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);

		$WC_CODE = $data[WC_CODE];
		$WC_NAME = $data[WC_NAME];

	} // end if
	
	if( !$UPD && !$VIEW ){
		unset($WH_ID);
		unset($START_DATE);
		unset($END_DATE);
		unset($WC_CODE);
		unset($WC_NAME);
		unset($REMARK);
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
	} // end if
?>