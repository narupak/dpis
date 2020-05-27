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
		$cmd = " update PER_HOLIDAYHIS set AUDIT_FLAG = 'N' where HH_ID in (".stripslashes($current_list).") ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		$cmd = " update PER_HOLIDAYHIS set AUDIT_FLAG = 'Y' where HH_ID in (".stripslashes($setflagaudit).") ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการตรวจสอบข้อมูล");
	}

	if($command=="ADD" && $PER_ID){
		$START_DATE = save_date($START_DATE); 
		$END_DATE = save_date($END_DATE); 
	
		$cmd = " select max(HH_ID) as max_id from PER_HOLIDAYHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$HH_ID = $data[max_id] + 1;
		
		$cmd = " insert into PER_HOLIDAYHIS	(HH_ID, PER_ID, HG_CODE, START_DATE, END_DATE, REMARK, UPDATE_USER, UPDATE_DATE)
						  values ($HH_ID, $PER_ID, '$HG_CODE', '$START_DATE', '$END_DATE', '$REMARK', $SESS_USERID, '$UPDATE_DATE')   ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูลประวัติวันหยุดพิเศษ [$PER_ID : $HH_ID : $HG_CODE]");
		$ADD_NEXT = 1;
	} // end if

	if($command=="UPDATE" && $PER_ID && $HH_ID){
		$START_DATE = save_date($START_DATE); 
		$END_DATE = save_date($END_DATE); 
	
		$cmd = " UPDATE PER_HOLIDAYHIS SET HG_CODE='$HG_CODE', 
					START_DATE='$START_DATE', 
					END_DATE='$END_DATE',  
					REMARK='$REMARK',  
					UPDATE_USER=$SESS_USERID, 
					UPDATE_DATE='$UPDATE_DATE'
				WHERE HH_ID=$HH_ID";
		$db_dpis->send_cmd($cmd);
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูลประวัติวันหยุดพิเศษ [$PER_ID : $HH_ID : $HG_CODE]");
	} // end if
	
	if($command=="DELETE" && $PER_ID && $HH_ID){
		$cmd = " select HG_CODE from PER_HOLIDAYHIS where HH_ID=$HH_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$HG_CODE = $data[HG_CODE];
		
		$cmd = " delete from PER_HOLIDAYHIS where HH_ID=$HH_ID ";
		$db_dpis->send_cmd($cmd);
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูลประวัติวันหยุดพิเศษ [$PER_ID : $HH_ID : $HG_CODE]");
	} // end if

	if(($UPD && $PER_ID && $HH_ID) || ($VIEW && $PER_ID && $HH_ID)){
		$cmd = "	SELECT 	HH_ID, a.HG_CODE, b.HG_NAME, START_DATE, END_DATE, REMARK, a.UPDATE_USER, a.UPDATE_DATE   
							FROM		PER_HOLIDAYHIS a, PER_HOLIDAY_GROUP b  
						WHERE		HH_ID=$HH_ID and a.HG_CODE=b.HG_CODE  ";	
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

		$HG_CODE = $data[HG_CODE];
		$HG_NAME = $data[HG_NAME];

	} // end if
	
	if( !$UPD && !$VIEW ){
		unset($HH_ID);
		unset($START_DATE);
		unset($END_DATE);
		unset($HG_CODE);
		unset($HG_NAME);
		unset($REMARK);
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
	} // end if
?>