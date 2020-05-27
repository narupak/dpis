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

	$CHECK_DATE = date("Y-m-d");
	$UPDATE_DATE = date("Y-m-d H:i:s");
	if (!$EXH_SALARY) $EXH_SALARY = "NULL";
	if (!isset($EXH_ACTIVE)) $EXH_ACTIVE = 1;
	
	if ($command == "SETFLAG") {
		$setflagaudit =  implode(",",$list_audit_id);
//		echo "$setflagaudit";
		$cmd = " update PER_EXTRAHIS set AUDIT_FLAG = 'N' where EXH_ID in (".stripslashes($current_list).") ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		$cmd = " update PER_EXTRAHIS set AUDIT_FLAG = 'Y' where EXH_ID in (".stripslashes($setflagaudit).") ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการตรวจสอบข้อมูล");
	}

	if($command=="ADD" && $PER_ID){
		$EXH_EFFECTIVEDATE = save_date($EXH_EFFECTIVEDATE); 
		$EXH_ENDDATE = save_date($EXH_ENDDATE); 
		$EXH_DOCDATE = save_date($EXH_DOCDATE); 
		
		$cmd = " select max(EXH_ID) as max_id from PER_EXTRAHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$EXH_ID = $data[max_id] + 1;
		
		$cmd = " insert into PER_EXTRAHIS (EXH_ID, PER_ID, EXH_EFFECTIVEDATE, EX_CODE, EXH_AMT, EXH_ENDDATE, PER_CARDNO, 
						UPDATE_USER, UPDATE_DATE, EXH_ORG_NAME, EXH_DOCNO, EXH_DOCDATE, EXH_SALARY, EXH_REMARK, EXH_ACTIVE)
						values ($EXH_ID, $PER_ID, '$EXH_EFFECTIVEDATE', '$EX_CODE', $EXH_AMT, '$EXH_ENDDATE', $PER_CARDNO, 
						$SESS_USERID, '$UPDATE_DATE', '$EXH_ORG_NAME', '$EXH_DOCNO', '$EXH_DOCDATE', $EXH_SALARY, '$EXH_REMARK', $EXH_ACTIVE) ";
		$db_dpis->send_cmd($cmd);
	//	$db_dpis->show_error();
		
		$cmd = " select sum(EXH_AMT) as PER_SPSALARY 
						  from PER_EXTRAHIS a, PER_EXTRATYPE b 
						  where PER_ID = $PER_ID and trim(a.EX_CODE)=trim(b.EX_CODE) and EXH_ACTIVE = 1 and 
						  (EXH_ENDDATE is NULL or EXH_ENDDATE >= '$CHECK_DATE')
						  group by PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$PER_SPSALARY = $data[PER_SPSALARY];

		$cmd = " UPDATE PER_PERSONAL SET PER_SPSALARY = $PER_SPSALARY WHERE PER_ID = $PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มประวัติการรับเงินเพิ่มพิเศษ [$PER_ID : $EXH_ID : $EX_CODE]");
		echo "<script language=\"JavaScript\">parent.refresh_opener('A<::>0')</script>";
		
	} // end if
	
	if($command=="UPDATE" && $PER_ID && $EXH_ID){
		$EXH_EFFECTIVEDATE = save_date($EXH_EFFECTIVEDATE); 
		$EXH_ENDDATE = save_date($EXH_ENDDATE); 
		$EXH_DOCDATE = save_date($EXH_DOCDATE); 
		
		$cmd = " UPDATE PER_EXTRAHIS SET
						EXH_EFFECTIVEDATE='$EXH_EFFECTIVEDATE', 
						EX_CODE='$EX_CODE', 
						EXH_AMT=$EXH_AMT, 
						EXH_ENDDATE='$EXH_ENDDATE', 
						PER_CARDNO=$PER_CARDNO,  
						EXH_ORG_NAME='$EXH_ORG_NAME', 
						EXH_DOCNO='$EXH_DOCNO', 
						EXH_DOCDATE='$EXH_DOCDATE', 
						EXH_SALARY=$EXH_SALARY, 
						EXH_REMARK='$EXH_REMARK',  
						EXH_ACTIVE=$EXH_ACTIVE, 
						UPDATE_USER=$SESS_USERID, 
						UPDATE_DATE='$UPDATE_DATE'
				WHERE EXH_ID=$EXH_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		
		$cmd = " select sum(EXH_AMT) as PER_SPSALARY 
						  from PER_EXTRAHIS a, PER_EXTRATYPE b 
						  where PER_ID = $PER_ID and trim(a.EX_CODE)=trim(b.EX_CODE) and EXH_ACTIVE = 1 and 
						  (EXH_ENDDATE is NULL or EXH_ENDDATE >= '$CHECK_DATE')
						  group by PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$PER_SPSALARY = $data[PER_SPSALARY];

		$cmd = " UPDATE PER_PERSONAL SET PER_SPSALARY = $PER_SPSALARY WHERE PER_ID = $PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขประวัติการรับเงินเพิ่มพิเศษ [$PER_ID : $EXH_ID : $EX_CODE]");
		echo "<script language=\"JavaScript\">parent.refresh_opener('U<::>0')</script>";
	
	} // end if
	
	if($command=="DELETE" && $PER_ID && $EXH_ID){
		$cmd = " select EX_CODE from PER_EXTRAHIS where EXH_ID=$EXH_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$EX_CODE = $data[EX_CODE];
		
		$cmd = " delete from PER_EXTRAHIS where EXH_ID=$EXH_ID ";
		$db_dpis->send_cmd($cmd);
	
		$cmd = " select sum(EXH_AMT) as PER_SPSALARY 
						  from PER_EXTRAHIS a, PER_EXTRATYPE b 
						  where PER_ID = $PER_ID and trim(a.EX_CODE)=trim(b.EX_CODE) and EXH_ACTIVE = 1 and 
						  (EXH_ENDDATE is NULL or EXH_ENDDATE >= '$CHECK_DATE')
						  group by PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$PER_SPSALARY = $data[PER_SPSALARY];

		$cmd = " UPDATE PER_PERSONAL SET PER_SPSALARY = $PER_SPSALARY WHERE PER_ID = $PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบประวัติการรับเงินเพิ่มพิเศษ [$PER_ID : $EXH_ID : $EX_CODE]");
	} // end if

	
	if(($UPD && $PER_ID && $EXH_ID) || ($VIEW && $PER_ID && $EXH_ID)){
		$cmd = "	SELECT 	EXH_ID, EXH_EFFECTIVEDATE, peh.EX_CODE, pet.EX_NAME, EXH_AMT, EXH_ENDDATE, 
												EXH_ORG_NAME, EXH_DOCNO, EXH_DOCDATE, EXH_SALARY, EXH_REMARK, EXH_ACTIVE, 
												peh.UPDATE_USER, peh.UPDATE_DATE
							FROM		PER_EXTRAHIS peh, PER_EXTRATYPE pet
							WHERE	peh.EXH_ID=$EXH_ID and peh.EX_CODE=pet.EX_CODE   ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$EXH_ID = $data[EXH_ID];
		$EXH_EFFECTIVEDATE = show_date_format(trim($data[EXH_EFFECTIVEDATE]), 1);
		$EX_CODE = $data[EX_CODE];
		$EX_NAME = $data[EX_NAME];		
		$EXH_AMT = $data[EXH_AMT];
		$EXH_ENDDATE = show_date_format(trim($data[EXH_ENDDATE]), 1);
		$EXH_ORG_NAME = trim($data[EXH_ORG_NAME]);
		$EXH_DOCNO = trim($data[EXH_DOCNO]);
		$EXH_DOCDATE = show_date_format(trim($data[EXH_DOCDATE]), 1);
		$EXH_SALARY = $data[EXH_SALARY];
		$EXH_REMARK = trim($data[EXH_REMARK]);
		$EXH_ACTIVE = $data[EXH_ACTIVE];
		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);

	} // end if
	
	if( !$UPD && !$VIEW ){
		unset($EXH_ID);
		unset($EXH_EFFECTIVEDATE);
		unset($EXH_AMT);		
		unset($EXH_ENDDATE);
		unset($EXH_ORG_NAME);
		unset($EXH_DOCNO);
		unset($EXH_DOCDATE);
		unset($EXH_SALARY);
		unset($EXH_REMARK);
		unset($EXH_ACTIVE);
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
	
		unset($EX_CODE);
		unset($EX_NAME);
	} // end if
?>