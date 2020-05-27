<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	$UPDATE_DATE = date("Y-m-d H:i:s");
	if (!$TCH_SCORE) $TCH_SCORE = "NULL";
		
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

	if ($command == "SETFLAG") {
		$setflagaudit =  implode(",",$list_audit_id);
//		echo "$setflagaudit";
		$cmd = " update PER_TEST_COURSEHIS set AUDIT_FLAG = 'N' where TCH_ID in (".stripslashes($current_list).") ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		$cmd = " update PER_TEST_COURSEHIS set AUDIT_FLAG = 'Y' where TCH_ID in (".stripslashes($setflagaudit).") ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการตรวจสอบข้อมูล");
	}

	if($command=="ADD" && $PER_ID){
		$TCH_TESTDATE = save_date($TCH_TESTDATE); 
		$TCH_ENDDATE = save_date($TCH_ENDDATE); 
		$TCH_POSTDATE = save_date($TCH_POSTDATE); 
		
		if($DPISDB=="oci8")
			$cmd2 = " select TCH_TESTDATE,TCH_ENDDATE from PER_TEST_COURSEHIS  
								where PER_ID='$PER_ID' and  substr(TCH_TESTDATE,0,10)='$TCH_TESTDATE' and 
								substr(TCH_ENDDATE,0,10)='$TCH_ENDDATE'  and TC_CODE='$TC_CODE' "; 
		if($DPISDB=="odbc")
			$cmd2 = " select TCH_TESTDATE,TCH_ENDDATE from PER_TEST_COURSEHIS  
								where PER_ID=$PER_ID and  LEFT(TCH_TESTDATE,10)='$TCH_TESTDATE' and 
								LEFT(TCH_ENDDATE,10)='$TCH_ENDDATE'  and TC_CODE='$TC_CODE' "; 
		if($DPISDB=="mysql")
			$cmd2 = " select TCH_TESTDATE,TCH_ENDDATE from PER_TEST_COURSEHIS  
								where PER_ID='$PER_ID' and  substr(TCH_TESTDATE,0,10)='$TCH_TESTDATE' and 
								substr(TCH_ENDDATE,0,10)='$TCH_ENDDATE'  and TC_CODE='$TC_CODE' "; 
		$count=$db_dpis->send_cmd($cmd2);
		if($count) { ?>  <script> <!--  
			alert("ไม่สามารถเพิ่มข้อมูลได้ เนื่องจาก วันที่เริ่ม -วันที่สิ้นสุด และหลักสูตร ซ้ำ !!!");
				-->   </script>	<? 
		} else {	
			$cmd = " select max(TCH_ID) as max_id from PER_TEST_COURSEHIS ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$TCH_ID = $data[max_id] + 1;
		
			$cmd = " insert into PER_TEST_COURSEHIS	(TCH_ID, PER_ID, TC_CODE, TCH_SEQ_NO, TCH_TESTDATE, TCH_ENDDATE, 
							TCH_ORG, TCH_PLACE, TCH_LEVEL, PER_CARDNO, UPDATE_USER, UPDATE_DATE, 
							TCH_SCORE, TCH_REMARK, TCH_POSTDATE, TCH_COURSE_NAME)
							values ($TCH_ID, $PER_ID, '$TC_CODE', '$TCH_SEQ_NO', '$TCH_TESTDATE', '$TCH_ENDDATE', 
							'$TCH_ORG', '$TCH_PLACE', '$TCH_LEVEL', '$PER_CARDNO', $SESS_USERID, 
							'$UPDATE_DATE', $TCH_SCORE, '$TCH_REMARK', '$TCH_POSTDATE', '$TCH_COURSE_NAME') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มประวัติการสอบ [$PER_ID : $TCH_ID : $TC_CODE]");
			$ADD_NEXT = 1;
		} // end if
	} // end if check ข้อมูลซ้ำ

	if($command=="UPDATE" && $PER_ID && $TCH_ID){
		$TCH_TESTDATE = save_date($TCH_TESTDATE); 
		$TCH_ENDDATE = save_date($TCH_ENDDATE); 
		$TCH_POSTDATE = save_date($TCH_POSTDATE); 
		
		if($DPISDB=="oci8")    
			$cmd2 = " select TCH_TESTDATE,TCH_ENDDATE from PER_TEST_COURSEHIS  
								where PER_ID='$PER_ID' and  substr(TCH_TESTDATE,0,10)='$TCH_TESTDATE' and 
								substr(TCH_ENDDATE,0,10)='$TCH_ENDDATE'  and TC_CODE='$TC_CODE' and TCH_ID<>$TCH_ID "; 
		if($DPISDB=="odbc")
			$cmd2 = "select TCH_TESTDATE,TCH_ENDDATE from PER_TEST_COURSEHIS  
								where PER_ID=$PER_ID and  LEFT(TCH_TESTDATE,10)='$TCH_TESTDATE' and 
								LEFT(TCH_ENDDATE,10)='$TCH_ENDDATE'  and TC_CODE='$TC_CODE' and TCH_ID<>$TCH_ID "; 
		if($DPISDB=="mysql")
			$cmd2 = " select TCH_TESTDATE,TCH_ENDDATE from PER_TEST_COURSEHIS  
								where PER_ID='$PER_ID' and  substr(TCH_TESTDATE,0,10)='$TCH_TESTDATE' and 
								substr(TCH_ENDDATE,0,10)='$TCH_ENDDATE'  and TC_CODE='$TC_CODE' and TCH_ID<>$TCH_ID "; 
		$count=$db_dpis->send_cmd($cmd2);
		if($count) { ?>  <script> <!--  
			alert("ไม่สามารถแก้ไขข้อมูลได้ เนื่องจาก วันที่เริ่ม -วันที่สิ้นสุด และหลักสูตร ซ้ำ !!!");
				-->   </script>	<? 
		} else {	
			$cmd = " UPDATE PER_TEST_COURSEHIS SET
							TC_CODE='$TC_CODE', 
							TCH_SEQ_NO='$TCH_SEQ_NO', 
							TCH_TESTDATE='$TCH_TESTDATE', 
							TCH_ENDDATE='$TCH_ENDDATE', 
							TCH_ORG='$TCH_ORG', 
							TCH_PLACE='$TCH_PLACE', 
							TCH_LEVEL='$TCH_LEVEL', 
							PER_CARDNO='$PER_CARDNO', 
							TCH_SCORE=$TCH_SCORE, 
							TCH_REMARK='$TCH_REMARK', 
							TCH_POSTDATE='$TCH_POSTDATE', 
							TCH_COURSE_NAME='$TCH_COURSE_NAME', 
							UPDATE_USER=$SESS_USERID, 
							UPDATE_DATE='$UPDATE_DATE'
				WHERE TCH_ID=$TCH_ID";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขประวัติการสอบ [$PER_ID : $TCH_ID : $TC_CODE]");
		} // end if
	}// end if check ข้อมูลซ้ำ
	
	if($command=="DELETE" && $PER_ID && $TCH_ID){
		$cmd = " select TC_CODE from PER_TEST_COURSEHIS where TCH_ID=$TCH_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$TC_CODE = $data[TC_CODE];
		
		$cmd = " delete from PER_TEST_COURSEHIS where TCH_ID=$TCH_ID ";
		$db_dpis->send_cmd($cmd);
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบประวัติการสอบ [$PER_ID : $TCH_ID : $TC_CODE]");
	} // end if

	if(($UPD && $PER_ID && $TCH_ID) || ($VIEW && $PER_ID && $TCH_ID)){
		$cmd = " select	TC_CODE, TCH_SEQ_NO, TCH_TESTDATE, TCH_ENDDATE, TCH_ORG, TCH_PLACE, 
										TCH_LEVEL, UPDATE_USER, UPDATE_DATE, TCH_SCORE, TCH_REMARK, 
										TCH_POSTDATE, TCH_COURSE_NAME 
						from		PER_TEST_COURSEHIS
						where	TCH_ID=$TCH_ID  ";	
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$TCH_SEQ_NO = $data[TCH_SEQ_NO];
		$TCH_TESTDATE = show_date_format(trim($data[TCH_TESTDATE]), 1);
		$TCH_ENDDATE = show_date_format(trim($data[TCH_ENDDATE]), 1);
		$TCH_ORG = $data[TCH_ORG];
		$TCH_PLACE = $data[TCH_PLACE];
		$TCH_LEVEL = $data[TCH_LEVEL];
		$TCH_SCORE = $data[TCH_SCORE];
		$TCH_REMARK = $data[TCH_REMARK];
		$TCH_POSTDATE = show_date_format(trim($data[TCH_POSTDATE]), 1);
		$TCH_COURSE_NAME = $data[TCH_COURSE_NAME];
		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);
		
		$TC_CODE = $data[TC_CODE];
		if($TC_CODE){
			$cmd = " select TC_NAME from PER_TEST_COURSE where TC_CODE='$TC_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$TC_NAME = $data2[TC_NAME];
		} // end if

	} // end if
	
	if( !$UPD && !$VIEW ){
		unset($TCH_ID);
		unset($TCH_SEQ_NO);		
		unset($TCH_TESTDATE);
		unset($TCH_ENDDATE);
		unset($TCH_ORG);
		unset($TCH_PLACE);
		unset($TCH_LEVEL);
		unset($TCH_SCORE);
		unset($TCH_REMARK);
		unset($TCH_POSTDATE);
		unset($TCH_COURSE_NAME);

		unset($TC_CODE);
		unset($TC_NAME);
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
	} // end if
?>