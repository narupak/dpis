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
	if (!$OO_DAY) $OO_DAY = "NULL";

	if ($command == "SETFLAG") {
		$setflagaudit =  implode(",",$list_audit_id);
//		echo "$setflagaudit";
		$cmd = " update PER_OTHER_OCCUPATION set AUDIT_FLAG = 'N' where OO_ID in (".stripslashes($current_list).") ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		$cmd = " update PER_OTHER_OCCUPATION set AUDIT_FLAG = 'Y' where OO_ID in (".stripslashes($setflagaudit).") ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการตรวจสอบข้อมูล");
	}

	if($command=="ADD" && $PER_ID){
		$OO_STARTDATE = save_date($OO_STARTDATE); 
		$OO_ENDDATE = save_date($OO_ENDDATE); 
		$cmd2="select OC_CODE from PER_OTHER_OCCUPATION  where PER_ID=$PER_ID and OC_CODE='$OC_CODE' and OO_YEAR='$OO_YEAR' ";
		$count=$db_dpis->send_cmd($cmd2);
		//$db_dpis->show_error(); echo "<hr>$cmd2<br>";
		if($count) { ?>  <script> <!--  
			alert("ไม่สามารถเพิ่มข้อมูลได้ เนื่องจากคุณระบุประวัติการไปประกอบอาชีพอื่นซ้ำ !!!");
				-->   </script>	<? 
		} else {	  
			$cmd = " select max(OO_ID) as max_id from PER_OTHER_OCCUPATION ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$OO_ID = $data[max_id] + 1;
		
			$OO_DESC = trim($OO_DESC);
			$cmd = " insert into PER_OTHER_OCCUPATION (OO_ID, PER_ID, PER_CARDNO, OC_CODE, OO_DESC, OO_YEAR, 
							OO_REMARK, UPDATE_USER, UPDATE_DATE, OO_STARTDATE, OO_ENDDATE, OO_DAY)
							values ($OO_ID, $PER_ID, '$PER_CARDNO', '$OC_CODE', '$OO_DESC', '$OO_YEAR', '$OO_REMARK', 
							$SESS_USERID, '$UPDATE_DATE', '$OO_STARTDATE', '$OO_ENDDATE', $OO_DAY)   ";
			$db_dpis->send_cmd($cmd);
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มประวัติการไปประกอบอาชีพอื่น [$PER_ID : $OO_ID : $OC_CODE]");
			$ADD_NEXT = 1;
		} // end if
	}

	if($command=="UPDATE" && $PER_ID && $OO_ID){
		$OO_STARTDATE = save_date($OO_STARTDATE); 
		$OO_ENDDATE = save_date($OO_ENDDATE); 
		$cmd2="select OC_CODE from PER_OTHER_OCCUPATION  where PER_ID=$PER_ID and OO_ID!=$OO_ID and OC_CODE='$OC_CODE' and OO_YEAR='$OO_YEAR' ";
		$count=$db_dpis->send_cmd($cmd2);
		//$db_dpis->show_error(); echo "<hr>$cmd2<br>";
		if($count) { ?>  <script> <!--  
			alert("ไม่สามารถแก้ไขข้อมูลได้ เนื่องจากคุณระบุประวัติการไปประกอบอาชีพอื่นซ้ำ !!!");
				-->   </script>	<? 
		} else {	  
			$cmd = " UPDATE PER_OTHER_OCCUPATION SET
							PER_CARDNO='$PER_CARDNO', 
							OC_CODE='$OC_CODE', 
							OO_DESC='$OO_DESC', 
							OO_YEAR='$OO_YEAR', 
							OO_REMARK='$OO_REMARK', 
							OO_STARTDATE='$OO_STARTDATE', 
							OO_ENDDATE='$OO_ENDDATE', 
							OO_DAY=$OO_DAY, 
							UPDATE_USER=$SESS_USERID, 
							UPDATE_DATE='$UPDATE_DATE'
						WHERE OO_ID=$OO_ID ";
			$db_dpis->send_cmd($cmd);
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขประวัติการไปประกอบอาชีพอื่น [$PER_ID : $OO_ID : $OC_CODE]");
		} // end if
	}
	
	if($command=="DELETE" && $PER_ID && $OO_ID){
		$cmd = " select OC_CODE from PER_OTHER_OCCUPATION where OO_ID=$OO_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$OC_CODE = $data[OC_CODE];
		
		$cmd = " delete from PER_OTHER_OCCUPATION where OO_ID=$OO_ID ";
		$db_dpis->send_cmd($cmd);
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบประวัติการไปประกอบอาชีพอื่น [$PER_ID : $OO_ID : $OC_CODE]");
	} // end if

	if(($UPD && $PER_ID && $OO_ID) || ($VIEW && $PER_ID && $OO_ID)){
		$cmd = " SELECT 	OO_ID, pa.OC_CODE, pag.OC_NAME, OO_DESC, OO_YEAR, OO_REMARK, 
												OO_STARTDATE, OO_ENDDATE, OO_DAY, pa.UPDATE_USER, pa.UPDATE_DATE  
						FROM			PER_OTHER_OCCUPATION pa, PER_OCCUPATION pag 
						WHERE		pa.OO_ID=$OO_ID and pa.OC_CODE=pag.OC_CODE ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$OO_ID = $data[OO_ID];
		$OC_CODE = $data[OC_CODE];
		$OC_NAME = $data[OC_NAME];
		$OO_DESC = $data[OO_DESC];
		$OO_YEAR = $data[OO_YEAR];
		$OO_REMARK = $data[OO_REMARK];
		$OO_STARTDATE = show_date_format(trim($data[OO_STARTDATE]), 1);
		$OO_ENDDATE = show_date_format(trim($data[OO_ENDDATE]), 1);
		$OO_DAY = $data[OO_DAY];
		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);
	} // end if
	
	if( !$UPD && !$VIEW ){
		unset($OO_ID);
		unset($OO_DESC);
		unset($OO_YEAR);
		unset($OO_REMARK);
		unset($OO_STARTDATE);
		unset($OO_ENDDATE);
		unset($OO_DAY);
	
		unset($OC_CODE);
		unset($OC_NAME);
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
	} // end if
?>