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
			$cmd = " select 	PRE.PN_NAME, PER.PER_NAME, PER.PER_SURNAME, PER.PER_CARDNO,PER.DEPARTMENT_ID
					from		PER_PERSONAL as PER
							left join PER_PRENAME as PRE on (PER.PN_CODE=PRE.PN_CODE)
					where	PER.PER_ID=$PER_ID ";
		}elseif($DPISDB=="oci8"){
			$cmd = " select 	PER_PRENAME.PN_NAME, PER_PERSONAL.PER_NAME, PER_PERSONAL.PER_SURNAME, 
							PER_PERSONAL.PER_CARDNO ,PER_PERSONAL.DEPARTMENT_ID
					from		PER_PERSONAL, PER_PRENAME
					where	PER_PERSONAL.PN_CODE=PER_PRENAME.PN_CODE(+) and PER_PERSONAL.PER_ID=$PER_ID ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select 	PRE.PN_NAME, PER.PER_NAME, PER.PER_SURNAME, PER.PER_CARDNO,PER.DEPARTMENT_ID
					from		PER_PERSONAL as PER
							left join PER_PRENAME as PRE on (PER.PN_CODE=PRE.PN_CODE)
					where	PER.PER_ID=$PER_ID ";
		} // end if
		
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PER_NAME = $data[PN_NAME] . $data[PER_NAME] ." ". $data[PER_SURNAME];
		$PER_CARDNO = (trim($data[PER_CARDNO]))? "'".trim($data[PER_CARDNO])."'" : "NULL";		
		$PER_ID_DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);
	} // end if

	$UPDATE_DATE = date("Y-m-d H:i:s");
	$CHECK_DATE = date("Y-m-d");
	if (!isset($PMH_ACTIVE)) $PMH_ACTIVE = 1;
	
	if ($command == "SETFLAG") {
		$setflagaudit =  implode(",",$list_audit_id);
//		echo "$setflagaudit";
		$cmd = " update PER_POS_MGTSALARYHIS set AUDIT_FLAG = 'N' where PMH_ID in (".stripslashes($current_list).") ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		$cmd = " update PER_POS_MGTSALARYHIS set AUDIT_FLAG = 'Y' where PMH_ID in (".stripslashes($setflagaudit).") ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการตรวจสอบข้อมูล");
	}

	if($command=="ADD" && $PER_ID){
		$PMH_EFFECTIVEDATE = save_date($PMH_EFFECTIVEDATE); 
		$PMH_ENDDATE = save_date($PMH_ENDDATE); 
		
		$cmd = " select max(PMH_ID) as max_id from PER_POS_MGTSALARYHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$PMH_ID = $data[max_id] + 1;
		
		$cmd = " insert into PER_POS_MGTSALARYHIS (PMH_ID, PER_ID, PMH_EFFECTIVEDATE, EX_CODE, PMH_AMT, 
						PMH_ENDDATE, PER_CARDNO, UPDATE_USER, UPDATE_DATE, PMH_REMARK, PMH_ACTIVE)
						values ($PMH_ID, $PER_ID, '$PMH_EFFECTIVEDATE', '$EX_CODE', $PMH_AMT, '$PMH_ENDDATE', 
						$PER_CARDNO, $SESS_USERID, '$UPDATE_DATE', '$PMH_REMARK', $PMH_ACTIVE) ";
		$db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();

		$cmd = " select sum(PMH_AMT) as PER_MGTSALARY 
						  from PER_POS_MGTSALARYHIS a, PER_EXTRATYPE b 
						  where PER_ID = $PER_ID and trim(a.EX_CODE)=trim(b.EX_CODE) and PMH_ACTIVE = 1 and MGT_FLAG = 1 and 
						  (PMH_ENDDATE is NULL or PMH_ENDDATE >= '$CHECK_DATE')
						  group by PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$PER_MGTSALARY = $data[PER_MGTSALARY]+0;

		$cmd = " UPDATE PER_PERSONAL SET PER_MGTSALARY = $PER_MGTSALARY WHERE PER_ID = $PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		$cmd = " select POS_ID from PER_PERSONAL where PER_ID = $PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$POS_ID = $data[POS_ID];

		$cmd = " UPDATE PER_POSITION SET POS_MGTSALARY = $PER_MGTSALARY WHERE POS_ID = $POS_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มประวัติการรับเงินประจำตำแหน่ง [$PER_ID : $PMH_ID : $EX_CODE]");
		echo "<script language=\"JavaScript\">parent.refresh_opener('A<::>0')</script>";
		
	} // end if
	
	if($command=="UPDATE" && $PER_ID && $PMH_ID){
		$PMH_EFFECTIVEDATE = save_date($PMH_EFFECTIVEDATE); 
		$PMH_ENDDATE = save_date($PMH_ENDDATE); 
		
		$cmd = " UPDATE PER_POS_MGTSALARYHIS SET
						PMH_EFFECTIVEDATE='$PMH_EFFECTIVEDATE', 
						EX_CODE='$EX_CODE', 
						PMH_AMT=$PMH_AMT, 
						PMH_ENDDATE='$PMH_ENDDATE', 
						PER_CARDNO=$PER_CARDNO,  
						PMH_REMARK='$PMH_REMARK',  
						PMH_ACTIVE=$PMH_ACTIVE, 
						UPDATE_USER=$SESS_USERID, 
						UPDATE_DATE='$UPDATE_DATE'
				WHERE PMH_ID=$PMH_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " select sum(PMH_AMT) as PER_MGTSALARY 
						  from PER_POS_MGTSALARYHIS a, PER_EXTRATYPE b 
						  where PER_ID = $PER_ID and trim(a.EX_CODE)=trim(b.EX_CODE) and PMH_ACTIVE = 1 and MGT_FLAG = 1 and 
						  (PMH_ENDDATE is NULL or PMH_ENDDATE >= '$CHECK_DATE')
						  group by PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$PER_MGTSALARY = $data[PER_MGTSALARY]+0;

		$cmd = " UPDATE PER_PERSONAL SET PER_MGTSALARY = $PER_MGTSALARY WHERE PER_ID = $PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " select POS_ID from PER_PERSONAL where PER_ID = $PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$POS_ID = $data[POS_ID];

		$cmd = " UPDATE PER_POSITION SET POS_MGTSALARY = $PER_MGTSALARY WHERE POS_ID = $POS_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขประวัติการรับเงินประจำตำแหน่ง [$PER_ID : $PMH_ID : $EX_CODE]");
		echo "<script language=\"JavaScript\">parent.refresh_opener('U<::>0')</script>";
		
	} // end if
	
	if($command=="DELETE" && $PER_ID && $PMH_ID){
		$cmd = " select EX_CODE from PER_POS_MGTSALARYHIS where PMH_ID=$PMH_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$EX_CODE = $data[EX_CODE];
		
		$cmd = " delete from PER_POS_MGTSALARYHIS where PMH_ID=$PMH_ID ";
		$db_dpis->send_cmd($cmd);

		$cmd = " select sum(PMH_AMT) as PER_MGTSALARY 
						  from PER_POS_MGTSALARYHIS a, PER_EXTRATYPE b 
						  where PER_ID = $PER_ID and trim(a.EX_CODE)=trim(b.EX_CODE) and PMH_ACTIVE = 1 and MGT_FLAG = 1 and 
						  (PMH_ENDDATE is NULL or PMH_ENDDATE >= '$CHECK_DATE')
						  group by PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$PER_MGTSALARY = $data[PER_MGTSALARY];

		$cmd = " UPDATE PER_PERSONAL SET PER_MGTSALARY = $PER_MGTSALARY WHERE PER_ID = $PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " select POS_ID from PER_PERSONAL where PER_ID = $PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$POS_ID = $data[POS_ID];

		$cmd = " UPDATE PER_POSITION SET POS_MGTSALARY = $PER_MGTSALARY WHERE POS_ID = $POS_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบประวัติการรับเงินประจำตำแหน่ง [$PER_ID : $PMH_ID : $EX_CODE]");
	} // end if

	if(($UPD && $PER_ID && $PMH_ID) || ($VIEW && $PER_ID && $PMH_ID)){
		$cmd = "	SELECT 	PMH_ID, PMH_EFFECTIVEDATE, peh.EX_CODE, pet.EX_NAME, PMH_AMT, PMH_ENDDATE, 
												PMH_REMARK, PMH_ACTIVE, peh.UPDATE_USER, peh.UPDATE_DATE
							FROM		PER_POS_MGTSALARYHIS peh, PER_EXTRATYPE pet
							WHERE	peh.PMH_ID=$PMH_ID and trim(peh.EX_CODE)=trim(pet.EX_CODE) ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PMH_ID = $data[PMH_ID];
		$PMH_EFFECTIVEDATE = show_date_format(trim($data[PMH_EFFECTIVEDATE]), 1);
		$PMH_ENDDATE = show_date_format(trim($data[PMH_ENDDATE]), 1);
		$EX_CODE = $data[EX_CODE];
		$EX_NAME = $data[EX_NAME];		
		$PMH_AMT = $data[PMH_AMT];
		$PMH_REMARK = trim($data[PMH_REMARK]);
		$PMH_ACTIVE = $data[PMH_ACTIVE];
		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);

	} // end if
	
	if( !$UPD && !$VIEW ){
		unset($PMH_ID);
		unset($PMH_EFFECTIVEDATE);
		unset($PMH_AMT);		
		unset($PMH_ENDDATE);
		unset($PMH_REMARK);
		unset($PMH_ACTIVE);
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
	
		unset($EX_CODE);
		unset($EX_NAME);
	} // end if
?>