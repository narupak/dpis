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
	
	if($command=="REORDER"){
	
		foreach($ARR_MAH_ORDER as $MAH_ID => $MAH_SEQ){
			$cmd = " update PER_MARRHIS set MAH_SEQ=$MAH_SEQ where MAH_ID=$MAH_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end foreach

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > จัดลำดับประวัติการสมรส [$PER_ID : $PER_NAME]");
	} // end if

	if ($command == "SETFLAG") {
		$setflagaudit =  implode(",",$list_audit_id);
//		echo "$setflagaudit";
		$cmd = " update PER_MARRHIS set AUDIT_FLAG = 'N' where MAH_ID in (".stripslashes($current_list).") ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		$cmd = " update PER_MARRHIS set AUDIT_FLAG = 'Y' where MAH_ID in (".stripslashes($setflagaudit).") ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการตรวจสอบข้อมูล");
	}

	if($command=="ADD" && $PER_ID){
		$MAH_MARRY_DATE = save_date($MAH_MARRY_DATE); 
		$MAH_DIVORCE_DATE = save_date($MAH_DIVORCE_DATE); 
		$MAH_BOOK_DATE = save_date($MAH_BOOK_DATE); 
	
		$cmd = " select max(MAH_ID) as max_id from PER_MARRHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$MAH_ID = $data[max_id] + 1;
		
		$cmd = " insert into PER_MARRHIS (MAH_ID, PER_ID, MAH_SEQ, MAH_NAME, MAH_MARRY_DATE, MAH_DIVORCE_DATE, DV_CODE, 
						UPDATE_USER, UPDATE_DATE, PER_CARDNO, PN_CODE, MAH_MARRY_NO, MAH_MARRY_ORG, PV_CODE, MR_CODE, 
						MAH_BOOK_NO, MAH_BOOK_DATE, MAH_REMARK)
						 values ($MAH_ID, $PER_ID, $MAH_SEQ, '$MAH_NAME', '$MAH_MARRY_DATE', '$MAH_DIVORCE_DATE', '$DV_CODE', 
						$SESS_USERID, '$UPDATE_DATE', '$PER_CARDNO', '$PN_CODE', '$MAH_MARRY_NO', '$MAH_MARRY_ORG', '$PV_CODE', 
						'$MR_CODE', '$MAH_BOOK_NO', '$MAH_BOOK_DATE', '$MAH_REMARK') ";
		$db_dpis->send_cmd($cmd);

		$cmd ="update per_personal set mr_code = '$MR_CODE' where per_id = $PER_ID";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มประวัติการสมรส [$PER_ID : $MAH_ID : $MAH_SEQ : $MAH_NAME]");
		$ADD_NEXT = 1;
	} // end if
	
	if($command=="UPDATE" && $PER_ID && $MAH_ID){
		$MAH_MARRY_DATE = save_date($MAH_MARRY_DATE); 
		$MAH_DIVORCE_DATE = save_date($MAH_DIVORCE_DATE); 
		$MAH_BOOK_DATE = save_date($MAH_BOOK_DATE); 
	
		$cmd = " update PER_MARRHIS set
							MAH_SEQ=$MAH_SEQ, 
							MAH_NAME='$MAH_NAME', 
							MAH_MARRY_DATE='$MAH_MARRY_DATE', 
							DV_CODE='$DV_CODE', 
							MAH_DIVORCE_DATE='$MAH_DIVORCE_DATE', 
							PN_CODE='$PN_CODE', 
							MAH_MARRY_NO='$MAH_MARRY_NO', 
							MAH_MARRY_ORG='$MAH_MARRY_ORG', 
							PV_CODE='$PV_CODE', 
							MR_CODE='$MR_CODE', 
							MAH_BOOK_NO='$MAH_BOOK_NO', 
							MAH_BOOK_DATE='$MAH_BOOK_DATE', 
							MAH_REMARK='$MAH_REMARK', 
							UPDATE_USER=$SESS_USERID, 
							UPDATE_DATE='$UPDATE_DATE', 
							PER_CARDNO='$PER_CARDNO' 
						where MAH_ID=$MAH_ID ";
		$db_dpis->send_cmd($cmd);
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขประวัติการสมรส [$PER_ID : $PER_NAME : $MAH_SEQ : $MAH_NAME]");
		$cmd ="update per_personal set mr_code = '$MR_CODE' where per_id = $PER_ID";
		$db_dpis->send_cmd($cmd);
	} // end if
	
	if($command=="DELETE" && $PER_ID && $MAH_ID){
		$cmd = " select MAH_NAME from PER_MARRHIS where MAH_ID=$MAH_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAH_NAME = trim($data[MAH_NAME]);
		
		$cmd = " delete from PER_MARRHIS where MAH_ID=$MAH_ID ";
		$db_dpis->send_cmd($cmd);
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบประวัติการสมรส [$PER_ID : $MAH_ID : $MAH_SEQ : $MAH_NAME]");
	} // end if

	if(($UPD && $PER_ID && $MAH_ID) || ($VIEW && $PER_ID && $MAH_ID)){
		$cmd = " select		MAH_ID, MAH_SEQ, MAH_NAME, MAH_MARRY_DATE, MAH_DIVORCE_DATE, DV_CODE, 
											UPDATE_USER, UPDATE_DATE, PN_CODE, MAH_MARRY_NO, MAH_MARRY_ORG, PV_CODE, 
											MR_CODE, MAH_BOOK_NO, MAH_BOOK_DATE, MAH_REMARK
						from			PER_MARRHIS
						where		MAH_ID=$MAH_ID ";	
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();		
		$MAH_SEQ = $data[MAH_SEQ];
		$MAH_NAME = $data[MAH_NAME];
		$MAH_MARRY_DATE = show_date_format(trim($data[MAH_MARRY_DATE]), 1);
		$MAH_DIVORCE_DATE = show_date_format(trim($data[MAH_DIVORCE_DATE]), 1);
		$MAH_BOOK_DATE = show_date_format(trim($data[MAH_BOOK_DATE]), 1);
		$MAH_MARRY_NO = trim($data[MAH_MARRY_NO]);
		$MAH_MARRY_ORG = trim($data[MAH_MARRY_ORG]);
		$MR_CODE = trim($data[MR_CODE]);
		$MAH_BOOK_NO = trim($data[MAH_BOOK_NO]);
		$MAH_REMARK = trim($data[MAH_REMARK]);

		$DV_CODE = trim($data[DV_CODE]);
		if($DV_CODE){
			$cmd = " select DV_NAME from PER_DIVORCE where DV_CODE='$DV_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$DV_NAME = $data2[DV_NAME];
		} // end if

		$PN_CODE = trim($data[PN_CODE]);
		if($PN_CODE){
			$cmd = " select PN_NAME from PER_PRENAME where PN_CODE='$PN_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PN_NAME = $data2[PN_NAME];
		} // end if

		$PV_CODE = trim($data[PV_CODE]);
		$PV_CODE2 = trim($data[PV_CODE]);
		if($PV_CODE){
			$cmd = " select PV_NAME from PER_PROVINCE where PV_CODE='$PV_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PV_NAME = $data2[PV_NAME];
			$PV_NAME2 = $data2[PV_NAME];
		} // end if

		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);
	} // end if
	
	if( !$UPD && !$VIEW ){
		unset($MAH_ID);
		unset($MAH_SEQ);
		unset($MAH_NAME);
		unset($MAH_MARRY_DATE);
		unset($MAH_DIVORCE_DATE);
		unset($MAH_MARRY_NO);
		unset($MAH_MARRY_ORG);
		unset($MR_CODE);
		unset($MAH_BOOK_NO);
		unset($MAH_BOOK_DATE);
		unset($MAH_REMARK);

		unset($DV_CODE);
		unset($DV_NAME);
		unset($PN_CODE);
		unset($PN_NAME);
		unset($PV_CODE);
		unset($PV_NAME);
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
	} // end if
?>