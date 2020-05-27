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
			$cmd = " select 	PRE.PN_NAME, PER.PER_NAME, PER.PER_SURNAME, PER.PER_TYPE, PER.PER_CARDNO, PER.DEPARTMENT_ID
					from		PER_PERSONAL as PER
							left join PER_PRENAME as PRE on (PER.PN_CODE=PRE.PN_CODE)
					where	PER.PER_ID=$PER_ID ";
		}elseif($DPISDB=="oci8"){
			$cmd = " select 	PER_PRENAME.PN_NAME, PER_PERSONAL.PER_NAME, PER_PERSONAL.PER_SURNAME, 
							PER_PERSONAL.PER_TYPE, PER_PERSONAL.PER_CARDNO, PER_PERSONAL.DEPARTMENT_ID
 					from		PER_PERSONAL, PER_PRENAME
					where	PER_PERSONAL.PN_CODE=PER_PRENAME.PN_CODE(+) and PER_PERSONAL.PER_ID=$PER_ID ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select 	PRE.PN_NAME, PER.PER_NAME, PER.PER_SURNAME, PER.PER_TYPE, PER.PER_CARDNO, PER.DEPARTMENT_ID
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
		$cmd = " update PER_TIMEHIS set AUDIT_FLAG = 'N' where TIMEH_ID in (".stripslashes($current_list).") ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		$cmd = " update PER_TIMEHIS set AUDIT_FLAG = 'Y' where TIMEH_ID in (".stripslashes($setflagaudit).") ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการตรวจสอบข้อมูล");
	}

	if($command=="ADD" && $PER_ID){	
		$TIMEH_BOOK_DATE = save_date($TIMEH_BOOK_DATE); 
		
		$cmd2 = " select TIME_CODE, TIMEH_MINUS from PER_TIMEHIS 
							where PER_ID=$PER_ID and TIME_CODE='$TIME_CODE' and TIMEH_MINUS=$TIMEH_MINUS ";
		$count=$db_dpis->send_cmd($cmd2);
		//$db_dpis->show_error(); echo "<hr>$cmd2<br>";
		if($count) { ?>  <script> <!--  
			alert("ไม่สามารถเพิ่มข้อมูลได้ เนื่องจากคุณระบุ เวลาทวีคูณ และ จำนวนวันที่ไม่นับเวลาทวีคูณ ซ้ำ !!!");
				-->   </script>	<? }  
		else {			
			$cmd = " select max(TIMEH_ID) as max_id from PER_TIMEHIS ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$TIMEH_ID = $data[max_id] + 1;
		
			$cmd = " 	insert into PER_TIMEHIS (TIMEH_ID, PER_ID, TIME_CODE, TIMEH_MINUS, TIMEH_REMARK, PER_CARDNO, 
								UPDATE_USER, UPDATE_DATE, TIMEH_BOOK_NO, TIMEH_BOOK_DATE)
								values ($TIMEH_ID, $PER_ID, '$TIME_CODE', '$TIMEH_MINUS', '$TIMEH_REMARK', '$PER_CARDNO', 
								$SESS_USERID, '$UPDATE_DATE', '$TIMEH_BOOK_NO', '$TIMEH_BOOK_DATE')   ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มประวัติเวลาทวีคูณ [$PER_ID : $TIMEH_ID : $TIME_CODE]");
			$ADD_NEXT = 1;
		} // end if
	} // end if เช็คข้อมูลซ้ำ

	if($command=="UPDATE" && $PER_ID && $TIMEH_ID){
		$TIMEH_BOOK_DATE = save_date($TIMEH_BOOK_DATE); 
		
		$cmd2 = " select   TIME_CODE, TIMEH_MINUS from PER_TIMEHIS 
							where PER_ID=$PER_ID and TIME_CODE='$TIME_CODE' and TIMEH_MINUS=$TIMEH_MINUS and TIMEH_ID<>$TIMEH_ID";
		$count=$db_dpis->send_cmd($cmd2);
		//$db_dpis->show_error(); echo "<hr>$cmd2<br>";
		if($count) { ?>  <script> <!--  
		alert("ไม่สามารถแก้ไขข้อมูลได้ เนื่องจากคุณระบุ เวลาทวีคูณ และ จำนวนวันที่ไม่นับเวลาทวีคูณ ซ้ำ !!!");
				-->   </script>	<? }  
		else {			
	
		$cmd = " UPDATE PER_TIMEHIS SET
					TIME_CODE='$TIME_CODE', 
					TIMEH_MINUS='$TIMEH_MINUS', 
					PER_CARDNO='$PER_CARDNO', 
					TIMEH_REMARK='$TIMEH_REMARK', 
					TIMEH_BOOK_NO='$TIMEH_BOOK_NO', 
					TIMEH_BOOK_DATE='$TIMEH_BOOK_DATE', 
					UPDATE_USER=$SESS_USERID, 
					UPDATE_DATE='$UPDATE_DATE'
				WHERE TIMEH_ID=$TIMEH_ID ";
		$db_dpis->send_cmd($cmd);
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขประวัติเวลาทวีคูณ [$PER_ID : $TIMEH_ID : $TIME_CODE]");
		} // end if
	} // end if เช็คข้อมูลซ้ำ
	
	if($command=="DELETE" && $PER_ID && $TIMEH_ID){
		$cmd = " select TIME_CODE from PER_TIMEHIS where TIMEH_ID=$TIMEH_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$TIME_CODE = $data[TIME_CODE];
		
		$cmd = " delete from PER_TIMEHIS where TIMEH_ID=$TIMEH_ID ";
		$db_dpis->send_cmd($cmd);
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบประวัติเวลาทวีคูณ [$PER_ID : $TIMEH_ID : $TIME_CODE]");
	} // end if

	if(($UPD && $PER_ID && $TIMEH_ID) || ($VIEW && $PER_ID && $TIMEH_ID)){
		$cmd = "	SELECT 	TIMEH_ID, pth.TIME_CODE, pt.TIME_NAME, TIMEH_MINUS, TIMEH_REMARK, TIMEH_BOOK_NO, TIMEH_BOOK_DATE,pth.UPDATE_USER, pth.UPDATE_DATE
							FROM		PER_TIMEHIS pth, PER_TIME pt
							WHERE	pth.TIMEH_ID=$TIMEH_ID and pth.TIME_CODE=pt.TIME_CODE ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$TIMEH_ID = $data[TIMEH_ID];
		$TIME_CODE = $data[TIME_CODE];
		$TIME_NAME = $data[TIME_NAME];
		$TIMEH_MINUS = $data[TIMEH_MINUS];
		$TIMEH_REMARK = $data[TIMEH_REMARK];
		$TIMEH_BOOK_NO = $data[TIMEH_BOOK_NO];
		$TIMEH_BOOK_DATE = show_date_format(trim($data[TIMEH_BOOK_DATE]), 1);
		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);
		
	} // end if
	
	if( !$UPD && !$VIEW ){
		unset($TIMEH_ID);
		unset($TIMEH_MINUS);
		unset($TIMEH_REMARK);
		unset($TIMEH_BOOK_NO);
		unset($TIMEH_BOOK_DATE);
	
		unset($TIME_CODE);
		unset($TIME_NAME);
	} // end if
?>