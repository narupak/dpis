<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	$UPDATE_DATE = date("Y-m-d H:i:s");
		
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
		$cmd = " update PER_KP7_BORROW set AUDIT_FLAG = 'N' where KB_ID in (".stripslashes($current_list).") ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		$cmd = " update PER_KP7_BORROW set AUDIT_FLAG = 'Y' where KB_ID in (".stripslashes($setflagaudit).") ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการตรวจสอบข้อมูล");
	}

	if($command=="ADD" && $PER_ID){
		$KB_BORROWDATE = save_date($KB_BORROWDATE); 
		$KB_RETURNDATE = save_date($KB_RETURNDATE); 
		
		if($DPISDB=="oci8")
			$cmd2 = " select KB_BORROWDATE,KB_RETURNDATE from PER_KP7_BORROW  
								where PER_ID='$PER_ID' and  substr(KB_BORROWDATE,0,10)='$KB_BORROWDATE' "; 
		if($DPISDB=="odbc")
			$cmd2 = " select KB_BORROWDATE,KB_RETURNDATE from PER_KP7_BORROW  
								where PER_ID=$PER_ID and  LEFT(KB_BORROWDATE,10)='$KB_BORROWDATE' "; 
		if($DPISDB=="mysql")
			$cmd2 = " select KB_BORROWDATE,KB_RETURNDATE from PER_KP7_BORROW  
								where PER_ID='$PER_ID' and  substr(KB_BORROWDATE,0,10)='$KB_BORROWDATE' "; 
		$count=$db_dpis->send_cmd($cmd2);
		if($count) { ?>  <script> <!--  
			alert("ไม่สามารถเพิ่มข้อมูลได้ เนื่องจาก วันที่ยืม ซ้ำ !!!");
				-->   </script>	<? 
		} else {	
			$cmd = " select max(KB_ID) as max_id from PER_KP7_BORROW ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$KB_ID = $data[max_id] + 1;
		
			$cmd = " insert into PER_KP7_BORROW (KB_ID, PER_ID, KB_BORROWDATE, KB_RETURNDATE, 
							PER_CARDNO, KB_NAME, KB_OBJECTIVE, KB_REMARK, UPDATE_USER, UPDATE_DATE)
							values ($KB_ID, $PER_ID, '$KB_BORROWDATE', '$KB_RETURNDATE', '$PER_CARDNO', 
							'$KB_NAME', '$KB_OBJECTIVE', '$KB_REMARK', $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มประวัติการยืม ก.พ.7 [$PER_ID : $KB_ID : $KB_NAME]");
			$ADD_NEXT = 1;
		} // end if
	} // end if check ข้อมูลซ้ำ

	if($command=="UPDATE" && $PER_ID && $KB_ID){
		$KB_BORROWDATE = save_date($KB_BORROWDATE); 
		$KB_RETURNDATE = save_date($KB_RETURNDATE); 
		
		if($DPISDB=="oci8")    
			$cmd2 = " select KB_BORROWDATE,KB_RETURNDATE from PER_KP7_BORROW  
								where PER_ID='$PER_ID' and  substr(KB_BORROWDATE,0,10)='$KB_BORROWDATE' and KB_ID<>$KB_ID "; 
		if($DPISDB=="odbc")
			$cmd2 = "select KB_BORROWDATE,KB_RETURNDATE from PER_KP7_BORROW  
								where PER_ID=$PER_ID and  LEFT(KB_BORROWDATE,10)='$KB_BORROWDATE' and KB_ID<>$KB_ID "; 
		if($DPISDB=="mysql")
			$cmd2 = " select KB_BORROWDATE,KB_RETURNDATE from PER_KP7_BORROW  
								where PER_ID='$PER_ID' and  substr(KB_BORROWDATE,0,10)='$KB_BORROWDATE' and KB_ID<>$KB_ID "; 
		$count=$db_dpis->send_cmd($cmd2);
		if($count) { ?>  <script> <!--  
			alert("ไม่สามารถแก้ไขข้อมูลได้ เนื่องจาก วันที่ยืม ซ้ำ !!!");
				-->   </script>	<? 
		} else {	
			$cmd = " UPDATE PER_KP7_BORROW SET
							KB_BORROWDATE='$KB_BORROWDATE', 
							KB_RETURNDATE='$KB_RETURNDATE', 
							PER_CARDNO='$PER_CARDNO', 
							KB_NAME='$KB_NAME', 
							KB_OBJECTIVE='$KB_OBJECTIVE', 
							KB_REMARK='$KB_REMARK', 
							UPDATE_USER=$SESS_USERID, 
							UPDATE_DATE='$UPDATE_DATE'
				WHERE KB_ID=$KB_ID";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขประวัติการยืม ก.พ.7 [$PER_ID : $KB_ID : $KB_NAME]");
		} // end if
	}// end if check ข้อมูลซ้ำ
	
	if($command=="DELETE" && $PER_ID && $KB_ID){
		$cmd = " select KB_NAME from PER_KP7_BORROW where KB_ID=$KB_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$KB_NAME = $data[KB_NAME];
		
		$cmd = " delete from PER_KP7_BORROW where KB_ID=$KB_ID ";
		$db_dpis->send_cmd($cmd);
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบประวัติการยืม ก.พ.7 [$PER_ID : $KB_ID : $KB_NAME]");
	} // end if

	if(($UPD && $PER_ID && $KB_ID) || ($VIEW && $PER_ID && $KB_ID)){
		$cmd = " select	KB_BORROWDATE, KB_RETURNDATE, KB_NAME, KB_OBJECTIVE, KB_REMARK, UPDATE_USER, UPDATE_DATE
						from		PER_KP7_BORROW
						where	KB_ID=$KB_ID  ";	
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$KB_BORROWDATE = show_date_format(trim($data[KB_BORROWDATE]), 1);
		$KB_RETURNDATE = show_date_format(trim($data[KB_RETURNDATE]), 1);
		$KB_NAME = $data[KB_NAME];
		$KB_OBJECTIVE = $data[KB_OBJECTIVE];
		$KB_REMARK = $data[KB_REMARK];
		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);
	} // end if
	
	if( !$UPD && !$VIEW ){
		unset($KB_ID);
		unset($KB_BORROWDATE);
		unset($KB_RETURNDATE);
		unset($KB_NAME);
		unset($KB_OBJECTIVE);
		unset($KB_REMARK);
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
	} // end if
?>