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
	if (!$HEIR_SEQ) $HEIR_SEQ = "NULL";
	if (!$HEIR_RATIO) $HEIR_RATIO = "NULL";

	if($command=="REORDER"){
		foreach($ARR_HEIR_ORDER as $HEIR_ID => $HEIR_SEQ){
			$cmd = " update PER_HEIR set HEIR_SEQ='$HEIR_SEQ' where HEIR_ID=$HEIR_ID ";
			$db_dpis->send_cmd($cmd);
		} // end foreach

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > จัดลำดับทายาท [$PER_ID : $PER_NAME]");
	} // end if

	if ($command == "SETFLAG") {
		$setflagaudit =  implode(",",$list_audit_id);
//		echo "$setflagaudit";
		$cmd = " update PER_HEIR set AUDIT_FLAG = 'N' where HEIR_ID in (".stripslashes($current_list).") ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		$cmd = " update PER_HEIR set AUDIT_FLAG = 'Y' where HEIR_ID in (".stripslashes($setflagaudit).") ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการตรวจสอบข้อมูล");
	}

	if($command=="ADD" && $PER_ID){
		$HEIR_BIRTHDAY = save_date($HEIR_BIRTHDAY); 
		
		$cmd2 = " select  HEIR_NAME, HR_CODE, HEIR_BIRTHDAY  from PER_HEIR  
							where PER_ID=$PER_ID and HEIR_NAME='$HEIR_NAME' and HR_CODE='$HR_CODE' and HEIR_BIRTHDAY='$HEIR_BIRTHDAY' ";
		$count=$db_dpis->send_cmd($cmd2);
		//$db_dpis->show_error(); echo "<hr>$cmd2<br>";
		if($count) { ?>  <script> <!--  
		alert("ไม่สามารถเพิ่มข้อมูลได้ เนื่องจากคุณระบุ ชื่อ-สกุล , วัน-เดือน-ปี เกิด และความสัมพันธ์ ซ้ำ !!!");
				-->   </script>	<? }  
		else {	  	
	
		$cmd = " select max(HEIR_ID) as max_id from PER_HEIR ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$HEIR_ID = $data[max_id] + 1;
		
		$cmd = " insert into PER_HEIR (HEIR_ID, PER_ID, HEIR_NAME, HR_CODE, HEIR_STATUS, HEIR_BIRTHDAY, 
						  HEIR_TAX, HEIR_REMARK, PER_CARDNO, UPDATE_USER, UPDATE_DATE, 
						  HEIR_TYPE, HEIR_ADDRESS, HEIR_PHONE, HEIR_ACTIVE, HEIR_SEQ, HEIR_RATIO)
						  values	($HEIR_ID, $PER_ID, '$HEIR_NAME', '$HR_CODE', '$HEIR_STATUS', '$HEIR_BIRTHDAY', 
						  '$HEIR_TAX', '$HEIR_REMARK', '$PER_CARDNO', $SESS_USERID, '$UPDATE_DATE', 
						  '$HEIR_TYPE', '$HEIR_ADDRESS', '$HEIR_PHONE', '$HEIR_ACTIVE', $HEIR_SEQ, $HEIR_RATIO) ";
		$db_dpis->send_cmd($cmd);
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูลทายาท [$PER_ID : $HEIR_ID : $HR_CODE]");
		$ADD_NEXT = 1;
		} // end if
	} // end if เช็คข้อมูลซ้ำ

	if($command=="UPDATE" && $PER_ID && $HEIR_ID){
		$HEIR_BIRTHDAY = save_date($HEIR_BIRTHDAY); 
		
		$cmd2="select  HEIR_NAME, HR_CODE, HEIR_BIRTHDAY  from PER_HEIR  where PER_ID=$PER_ID 
						and HEIR_NAME='$HEIR_NAME' and HR_CODE='$HR_CODE' and HEIR_BIRTHDAY='$HEIR_BIRTHDAY' and HEIR_ID<>$HEIR_ID ";
		$count=$db_dpis->send_cmd($cmd2);
		//$db_dpis->show_error(); echo "<hr>$cmd2<br>";
		if($count) { ?>  <script> <!--  
		alert("ไม่สามารถแก้ไขข้อมูลได้ เนื่องจากคุณระบุ ชื่อ-สกุล , วัน-เดือน-ปี เกิด และความสัมพันธ์ ซ้ำ !!!");
				-->   </script>	<? }  
		else {	  		
		$cmd = " UPDATE PER_HEIR SET
						HEIR_NAME='$HEIR_NAME', 
						HR_CODE='$HR_CODE', 
						HEIR_STATUS='$HEIR_STATUS', 
						HEIR_TAX='$HEIR_TAX', 
						HEIR_REMARK='$HEIR_REMARK', 
						HEIR_BIRTHDAY='$HEIR_BIRTHDAY', 
						PER_CARDNO='$PER_CARDNO', 
						HEIR_TYPE='$HEIR_TYPE', 
						HEIR_ADDRESS='$HEIR_ADDRESS', 
						HEIR_PHONE='$HEIR_PHONE', 
						HEIR_ACTIVE='$HEIR_ACTIVE', 
						HEIR_SEQ=$HEIR_SEQ, 
						HEIR_RATIO=$HEIR_RATIO, 
						UPDATE_USER=$SESS_USERID, 
						UPDATE_DATE='$UPDATE_DATE'
				WHERE HEIR_ID=$HEIR_ID ";
		$db_dpis->send_cmd($cmd);
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูลทายาท [$PER_ID : $HEIR_ID : $HR_CODE]");
		} // end if
	} //end if เช็คข้อมูลซ้ำ
	
	if($command=="DELETE" && $PER_ID && $HEIR_ID){
		$cmd = " select HR_CODE from PER_HEIR where HEIR_ID=$HEIR_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$HR_CODE = $data[HR_CODE];
		
		$cmd = " delete from PER_HEIR where HEIR_ID=$HEIR_ID ";
		$db_dpis->send_cmd($cmd);
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูลทายาท [$PER_ID : $HEIR_ID : $HR_CODE]");
	} // end if

	if(($UPD && $PER_ID && $HEIR_ID) || ($VIEW && $PER_ID && $HEIR_ID)){
		$cmd = "	SELECT 	HEIR_ID, HEIR_NAME, ph.HR_CODE, pht.HR_NAME, HEIR_BIRTHDAY, HEIR_STATUS, HEIR_TAX, HEIR_REMARK,
												ph.UPDATE_USER, ph.UPDATE_DATE, HEIR_TYPE, HEIR_ADDRESS, HEIR_PHONE, HEIR_ACTIVE, HEIR_SEQ, HEIR_RATIO
							FROM		PER_HEIR ph, PER_HEIRTYPE pht  
						WHERE		HEIR_ID=$HEIR_ID and ph.HR_CODE=pht.HR_CODE  ";	
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$HEIR_NAME = $data[HEIR_NAME];
		$HEIR_STATUS = $data[HEIR_STATUS];
		$HEIR_TAX = $data[HEIR_TAX];
		$HEIR_REMARK = $data[HEIR_REMARK];
		$HEIR_BIRTHDAY = show_date_format(trim($data[HEIR_BIRTHDAY]), 1);
		$HR_CODE = $data[HR_CODE];
		$HR_NAME = $data[HR_NAME];
		$HEIR_TYPE = $data[HEIR_TYPE];
		$HEIR_ADDRESS = $data[HEIR_ADDRESS];
		$HEIR_PHONE = $data[HEIR_PHONE];
		$HEIR_ACTIVE = $data[HEIR_ACTIVE];
		$HEIR_SEQ = $data[HEIR_SEQ];
		$HEIR_RATIO = $data[HEIR_RATIO];
		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);

	} // end if
	
	if( !$UPD && !$VIEW ){
		unset($HEIR_ID);
		unset($HEIR_NAME);
		unset($HEIR_STATUS);		
		unset($HEIR_BIRTHDAY);
		unset($HEIR_TAX);
		unset($HEIR_REMARK);
		unset($HEIR_TYPE);
		unset($HEIR_ADDRESS);
		unset($HEIR_PHONE);
		unset($HEIR_ACTIVE);
		unset($HEIR_SEQ);
		unset($HEIR_RATIO);

		unset($HR_CODE);
		unset($HR_NAME);
		
		$HEIR_TAX = 3;
		$HEIR_STATUS = 1;
	} // end if
?>