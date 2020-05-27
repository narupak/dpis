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
		$cmd = " update PER_REWARDHIS set AUDIT_FLAG = 'N' where REH_ID in (".stripslashes($current_list).") ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		$cmd = " update PER_REWARDHIS set AUDIT_FLAG = 'Y' where REH_ID in (".stripslashes($setflagaudit).") ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการตรวจสอบข้อมูล");
	}

	if($command=="ADD" && $PER_ID){
		$REH_DATE = save_date($REH_DATE); 

		$cmd = " select max(REH_ID) as max_id from PER_REWARDHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$REH_ID = $data[max_id] + 1;

		$cmd = " insert into PER_REWARDHIS (REH_ID, PER_ID, REH_DATE, REW_CODE, REH_ORG, REH_DOCNO, UPDATE_USER, 
						UPDATE_DATE, PER_CARDNO, REH_YEAR, REH_PERFORMANCE, REH_OTHER_PERFORMANCE, REH_REMARK)
						values ($REH_ID, $PER_ID, '$REH_DATE', '$REW_CODE', '$REH_ORG', '$REH_DOCNO', $SESS_USERID, '$UPDATE_DATE', 
						'$PER_CARDNO', '$REH_YEAR', '$REH_PERFORMANCE', '$REH_OTHER_PERFORMANCE', '$REH_REMARK') ";
		$db_dpis->send_cmd($cmd);
		//echo "$cmd<hr>";
		//$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มประวัติความดีความชอบ [$PER_ID : $REH_ID : $REW_CODE]");
		$ADD_NEXT = 1;
	} // end if

	if($command=="UPDATE" && $PER_ID && $REH_ID){
		$REH_DATE = save_date($REH_DATE); 
	
		$cmd = " UPDATE PER_REWARDHIS SET
						REH_DATE='$REH_DATE', 
						REW_CODE='$REW_CODE', 
						REH_ORG='$REH_ORG', 
						REH_DOCNO='$REH_DOCNO', 
						REH_YEAR='$REH_YEAR', 
						REH_PERFORMANCE='$REH_PERFORMANCE', 
						REH_OTHER_PERFORMANCE='$REH_OTHER_PERFORMANCE', 
						REH_REMARK='$REH_REMARK', 
						UPDATE_USER=$SESS_USERID, 
						UPDATE_DATE='$UPDATE_DATE', 
						PER_CARDNO='$PER_CARDNO' 
					WHERE REH_ID=$REH_ID ";
		$db_dpis->send_cmd($cmd);
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขประวัติความดีความชอบ [$PER_ID : $REH_ID : $REW_CODE]");
	} // end if
	
	if($command=="DELETE" && $PER_ID && $REH_ID){
		$cmd = " select REW_CODE from PER_REWARDHIS where REH_ID=$REH_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$REW_CODE = $data[REW_CODE];
		
		$cmd = " delete from PER_REWARDHIS where REH_ID=$REH_ID ";
		$db_dpis->send_cmd($cmd);
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบประวัติความดีความชอบ [$PER_ID : $REH_ID : $REW_CODE]");
	} // end if

	if(($UPD && $PER_ID && $REH_ID) || ($VIEW && $PER_ID && $REH_ID)){
		$cmd = " SELECT		prh.REW_CODE, pr.REW_NAME, REH_ORG, REH_DOCNO, REH_DATE, prh.UPDATE_USER, prh.UPDATE_DATE, 
												REH_YEAR, REH_PERFORMANCE, REH_OTHER_PERFORMANCE, REH_REMARK 
							FROM		PER_REWARDHIS prh, PER_REWARD pr
							WHERE	REH_ID=$REH_ID and prh.REW_CODE=pr.REW_CODE(+) ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$REH_DATE = show_date_format(trim($data[REH_DATE]), 1);

		$REH_ORG = trim($data[REH_ORG]);
		$REH_DOCNO = trim($data[REH_DOCNO]);
		$REH_YEAR = trim($data[REH_YEAR]);
		$REH_PERFORMANCE = trim($data[REH_PERFORMANCE]);
		$REH_OTHER_PERFORMANCE = trim($data[REH_OTHER_PERFORMANCE]);
		$REH_REMARK = trim($data[REH_REMARK]);
		$REW_CODE = trim($data[REW_CODE]);
		$REW_NAME = trim($data[REW_NAME]);
		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);
	} // end if
	
	if( !$UPD && !$VIEW ){
		unset($REH_ID);
		unset($REH_DATE);
		unset($REH_ORG);		
		unset($REH_DOCNO);
		unset($REH_YEAR);
		unset($REH_PERFORMANCE);
		unset($REH_OTHER_PERFORMANCE);
		unset($REH_REMARK);
	
		unset($REW_CODE);
		unset($REW_NAME);
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
	} // end if
?>