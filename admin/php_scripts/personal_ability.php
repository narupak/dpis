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

	if ($command == "SETFLAG") {
		$setflagaudit =  implode(",",$list_audit_id);
//		echo "$setflagaudit";
		$cmd = " update PER_ABILITY set AUDIT_FLAG = 'N' where ABI_ID in (".stripslashes($current_list).") ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		$cmd = " update PER_ABILITY set AUDIT_FLAG = 'Y' where ABI_ID in (".stripslashes($setflagaudit).") ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการตรวจสอบข้อมูล");
	}

	if($command=="ADD" && $PER_ID){
		$cmd2="select AL_CODE from PER_ABILITY  where PER_ID=$PER_ID and AL_CODE='$AL_CODE' and ABI_DESC='$ABI_DESC' ";
		$count=$db_dpis->send_cmd($cmd2);
		//$db_dpis->show_error(); echo "<hr>$cmd2<br>";
		if($count) { ?>  <script> <!--  
			alert("ไม่สามารถเพิ่มข้อมูลได้ เนื่องจากคุณระบุด้านความสามารถพิเศษและความสามารถพิเศษซ้ำ !!!");
				-->   </script>	<? 
		} else {	  
			$cmd = " select max(ABI_ID) as max_id from PER_ABILITY ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$ABI_ID = $data[max_id] + 1;
		
			$ABI_DESC = trim($ABI_DESC);
			$cmd = " insert into PER_ABILITY (ABI_ID, PER_ID, AL_CODE, ABI_DESC, ABI_REMARK, PER_CARDNO, UPDATE_USER, UPDATE_DATE)
							values ($ABI_ID, $PER_ID, '$AL_CODE', '$ABI_DESC', '$ABI_REMARK', '$PER_CARDNO', $SESS_USERID, '$UPDATE_DATE')   ";
			$db_dpis->send_cmd($cmd);
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มประวัติความสามารถพิเศษ [$PER_ID : $ABI_ID : $AL_CODE]");
			$ADD_NEXT = 1;
		} // end if
	}

	if($command=="UPDATE" && $PER_ID && $ABI_ID){
		$cmd2="select AL_CODE from PER_ABILITY  where PER_ID=$PER_ID and ABI_ID!=$ABI_ID and AL_CODE='$AL_CODE' and ABI_DESC='$ABI_DESC' ";
		$count=$db_dpis->send_cmd($cmd2);
		//$db_dpis->show_error(); echo "<hr>$cmd2<br>";
		if($count) { ?>  <script> <!--  
			alert("ไม่สามารถแก้ไขข้อมูลได้ เนื่องจากคุณระบุด้านความสามารถพิเศษและความสามารถพิเศษซ้ำ !!!");
				-->   </script>	<? 
		} else {	  
			$cmd = " UPDATE PER_ABILITY SET
							AL_CODE='$AL_CODE', 
							ABI_DESC='$ABI_DESC', 
							ABI_REMARK='$ABI_REMARK', 
							PER_CARDNO='$PER_CARDNO', 
							UPDATE_USER=$SESS_USERID, 
							UPDATE_DATE='$UPDATE_DATE'
						WHERE ABI_ID=$ABI_ID ";
			$db_dpis->send_cmd($cmd);
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขประวัติความสามารถพิเศษ [$PER_ID : $ABI_ID : $AL_CODE]");
		} // end if
	}
	
	if($command=="DELETE" && $PER_ID && $ABI_ID){
		$cmd = " select AL_CODE from PER_ABILITY where ABI_ID=$ABI_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$AL_CODE = $data[AL_CODE];
		
		$cmd = " delete from PER_ABILITY where ABI_ID=$ABI_ID ";
		$db_dpis->send_cmd($cmd);
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบประวัติความสามารถพิเศษ [$PER_ID : $ABI_ID : $AL_CODE]");
	} // end if

	if(($UPD && $PER_ID && $ABI_ID) || ($VIEW && $PER_ID && $ABI_ID)){
		$cmd = " SELECT 	ABI_ID, pa.AL_CODE, pag.AL_NAME, ABI_DESC, ABI_REMARK, pa.UPDATE_USER, pa.UPDATE_DATE  
						FROM			PER_ABILITY pa, PER_ABILITYGRP pag 
						WHERE		pa.ABI_ID=$ABI_ID and pa.AL_CODE=pag.AL_CODE ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$ABI_ID = $data[ABI_ID];
		$AL_CODE = $data[AL_CODE];
		$AL_NAME = $data[AL_NAME];
		$ABI_DESC = $data[ABI_DESC];
		$ABI_REMARK = $data[ABI_REMARK];
		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);
	} // end if
	
	if( !$UPD && !$VIEW ){
		unset($ABI_ID);
		unset($ABI_DESC);
		unset($ABI_REMARK);
	
		unset($AL_CODE);
		unset($AL_NAME);
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
	} // end if
?>