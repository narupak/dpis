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
		$cmd = " update PER_WORK_TIME set AUDIT_FLAG = 'N' where WT_ID in (".stripslashes($current_list).") ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		$cmd = " update PER_WORK_TIME set AUDIT_FLAG = 'Y' where WT_ID in (".stripslashes($setflagaudit).") ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการตรวจสอบข้อมูล");
	}

	if($command=="ADD" && $PER_ID){
		$START_DATE = save_date($START_DATE); 
		$END_DATE = save_date($END_DATE); 
	
		$cmd = " select max(WT_ID) as max_id from PER_WORK_TIME ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$WT_ID = $data[max_id] + 1;
		$HOLIDAY_FLAG = (trim($HOLIDAY_FLAG))? "$HOLIDAY_FLAG" : "NULL";
		$ABSENT_FLAG = (trim($ABSENT_FLAG))? "$ABSENT_FLAG" : "NULL";
		$cmd = " insert into PER_WORK_TIME (WT_ID, PER_ID, WL_CODE, WC_CODE, START_DATE, END_DATE, 
						HOLIDAY_FLAG, ABSENT_FLAG, REMARK, UPDATE_USER, UPDATE_DATE) 
						values ($WT_ID, $PER_ID, '$WL_CODE', '$WC_CODE', '$START_DATE', '$END_DATE', 
						$HOLIDAY_FLAG, $ABSENT_FLAG, '$REMARK', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูลประวัติการมาปฏิบัติราชการ [$PER_ID : $WT_ID : $WC_CODE]");
		$ADD_NEXT = 1;
	} // end if

	if($command=="UPDATE" && $PER_ID && $WT_ID){
		$START_DATE = save_date($START_DATE); 
		$END_DATE = save_date($END_DATE); 
		$HOLIDAY_FLAG = (trim($HOLIDAY_FLAG))? "$HOLIDAY_FLAG" : "NULL";
		$ABSENT_FLAG = (trim($ABSENT_FLAG))? "$ABSENT_FLAG" : "NULL";
		$cmd = " UPDATE PER_WORK_TIME SET
							WL_CODE='$WL_CODE', 
							WC_CODE='$WC_CODE', 
							HOLIDAY_FLAG=$HOLIDAY_FLAG,  
							ABSENT_FLAG=$ABSENT_FLAG, 
							REMARK='$REMARK', 
							UPDATE_USER=$SESS_USERID, 
							UPDATE_DATE='$UPDATE_DATE'
							WHERE WT_ID=$WT_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูลประวัติการมาปฏิบัติราชการ [$PER_ID : $WT_ID : $WC_CODE]");
	} // end if
	
	if($command=="DELETE" && $PER_ID && $WT_ID){
		$cmd = " select WL_CODE, WC_CODE from PER_WORK_TIME where WT_ID=$WT_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$WL_CODE = $data[WL_CODE];
		$WC_CODE = $data[WC_CODE];
		
		$cmd = " delete from PER_WORK_TIME where WT_ID=$WT_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูลประวัติการมาปฏิบัติราชการ [$PER_ID : $WT_ID : $WL_CODE : $WC_CODE]");
	} // end if

	if(($UPD && $PER_ID && $WT_ID) || ($VIEW && $PER_ID && $WT_ID)){
		$cmd = "	SELECT 	WT_ID, WL_CODE, a.WC_CODE, b.WC_NAME, START_DATE, END_DATE, 
												HOLIDAY_FLAG, ABSENT_FLAG, REMARK, a.UPDATE_USER, a.UPDATE_DATE   
							FROM		PER_WORK_TIME a, PER_WORK_CYCLE b  
							WHERE	WT_ID=$WT_ID and a.WC_CODE=b.WC_CODE  ";	
		$db_dpis->send_cmd($cmd);
//		echo $cmd;
		$data = $db_dpis->get_array();
		$START_DATE = show_date_format(trim($data[START_DATE]), 1);
		$END_DATE = show_date_format(trim($data[END_DATE]), 1);
		$HOLIDAY_FLAG = $data[HOLIDAY_FLAG];
		$ABSENT_FLAG = $data[ABSENT_FLAG];
		$REMARK = $data[REMARK];
		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);

		$WL_CODE = $data[WL_CODE];
		$cmd = "	select WL_NAME from PER_WORK_LOCATION where WL_CODE='$WL_CODE'";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$WL_NAME = $data2[WL_NAME];
		$WC_CODE = $data[WC_CODE];
		$WC_NAME = $data[WC_NAME];

	} // end if
	
	if( !$UPD && !$VIEW ){
		unset($WT_ID);
		unset($START_DATE);
		unset($END_DATE);
		unset($START_TIME);
		unset($END_TIME);
		unset($HOLIDAY_FLAG);		
		unset($ABSENT_FLAG);		
		unset($REMARK);		

		unset($WL_CODE);
		unset($WL_NAME);
		unset($WC_CODE);
		unset($WC_NAME);
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
		
	} // end if
?>