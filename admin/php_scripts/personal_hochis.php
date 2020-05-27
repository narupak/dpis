<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");
	include("php_scripts/load_per_control.php");
	
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
		//$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$PER_NAME = $data[PN_NAME] . $data[PER_NAME] ." ". $data[PER_SURNAME];
		$PER_TYPE = trim($data[PER_TYPE]);
		$PER_CARDNO = trim($data[PER_CARDNO]);
		$PER_ID_DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);
//echo "fdfdf".$PER_ID_DEPARTMENT_ID.":::::::::".$PER_ID."<br>";
	} // end if

	$UPDATE_DATE = date("Y-m-d H:i:s");

	if ($command=="ADD" || $command=="UPDATE") {
		$HOC_EFFECTIVEDATE = save_date($HOC_EFFECTIVEDATE); 
		$HOC_ENDDATE = save_date($HOC_ENDDATE); 
		$HOC_DOCDATE = save_date($HOC_DOCDATE); 
		$HOC_REMARK = str_replace("'", "&rsquo;", $HOC_REMARK);	
		$PER_CARDNO = (trim($PER_CARDNO))? "'$PER_CARDNO'" : "NULL";
		$HOC_SEQ_NO = (trim($HOC_SEQ_NO))? $HOC_SEQ_NO : 1;		
	}

	if ($command == "SETFLAG") {
		$setflagaudit =  implode(",",$list_audit_id);
//		echo "$setflagaudit";
		$cmd = " update PER_HOCHIS set AUDIT_FLAG = 'N' where HOC_ID in (".stripslashes($current_list).") ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		$cmd = " update PER_HOCHIS set AUDIT_FLAG = 'Y' where HOC_ID in (".stripslashes($setflagaudit).") ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการตรวจสอบข้อมูล");
	}

	if($command=="ADD" && $PER_ID){
		$cmd = " select max(HOC_ID) as max_id from PER_HOCHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$HOC_ID = $data[max_id] + 1;	
		
		$cmd = " insert into PER_HOCHIS (HOC_ID, PER_ID, PER_CARDNO, HOC_EFFECTIVEDATE, HOC_ENDDATE, HOC_DOCNO, 
				HOC_DOCDATE, ORG_ID, HOC_ORG_NAME, HOC_REMARK, HOC_SEQ_NO, UPDATE_USER, UPDATE_DATE )
				values ($HOC_ID, $PER_ID, $PER_CARDNO, '$HOC_EFFECTIVEDATE', '$HOC_ENDDATE', '$HOC_DOCNO', '$HOC_DOCDATE', 
				$HOC_ORG_ID, '$HOC_ORG_NAME', '$HOC_REMARK', $HOC_SEQ_NO, $SESS_USERID, '$UPDATE_DATE' )  ";
		$db_dpis->send_cmd($cmd);
		//echo "$cmd<br>";
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มประวัติ Head of Chancery [$PER_ID : $HOC_ID : $HOC_ORG_NAME]");
		$ADD_NEXT = 1;
	} // end if
	

	if($command=="UPDATE" && $PER_ID && $HOC_ID){		
	
		$cmd = " UPDATE PER_HOCHIS SET
					HOC_EFFECTIVEDATE='$HOC_EFFECTIVEDATE', 
					HOC_ENDDATE='$HOC_ENDDATE', 
					HOC_DOCNO='$HOC_DOCNO', 
					HOC_DOCDATE='$HOC_DOCDATE', 
					ORG_ID=$HOC_ORG_ID, 
					HOC_ORG_NAME='$HOC_ORG_NAME', 
					HOC_REMARK='$HOC_REMARK', 
					PER_CARDNO=$PER_CARDNO, 
					HOC_SEQ_NO=$HOC_SEQ_NO, 
					UPDATE_USER=$SESS_USERID, 
					UPDATE_DATE='$UPDATE_DATE'
				WHERE HOC_ID=$HOC_ID";
		$db_dpis->send_cmd($cmd);
		//echo "$cmd<br>";		
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขประวัติ Head of Chancery [$PER_ID : $HOC_ID : $HOC_ORG_NAME]");
	} // end if
	
	if($command=="DELETE" && $PER_ID && $HOC_ID){
		$cmd = " select HOC_ORG_NAME from PER_HOCHIS where HOC_ID=$HOC_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PL_CODE = $data[PL_CODE];
		
		$cmd = " delete from PER_HOCHIS where HOC_ID=$HOC_ID ";
		$db_dpis->send_cmd($cmd);

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบประวัติ Head of Chancery [$PER_ID : $HOC_ID : $HOC_ORG_NAME]");
	} // end if

	if(($UPD && $PER_ID && $HOC_ID) || ($VIEW && $PER_ID && $HOC_ID)){
		$cmd = "	select HOC_EFFECTIVEDATE, HOC_ENDDATE, HOC_DOCNO, HOC_DOCDATE, ORG_ID, HOC_ORG_NAME, HOC_REMARK, 
							HOC_SEQ_NO, UPDATE_USER, UPDATE_DATE  
							from		PER_HOCHIS 
							where	HOC_ID=$HOC_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
//		$db_dpis->show_error();
	
		$HOC_EFFECTIVEDATE = show_date_format(trim($data[HOC_EFFECTIVEDATE]), 1);
		$HOC_ENDDATE = show_date_format(trim($data[HOC_ENDDATE]), 1);
		$HOC_DOCDATE = show_date_format(trim($data[HOC_DOCDATE]), 1);
		$HOC_DOCNO = $data[HOC_DOCNO];
		$HOC_ORG_ID = trim($data[ORG_ID]);
		$HOC_ORG_NAME = trim($data[HOC_ORG_NAME]);
		$HOC_REMARK = trim($data[HOC_REMARK]);
		$HOC_SEQ_NO = $data[HOC_SEQ_NO];
		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);

	} // end if
	
	if( !$UPD && !$VIEW ){
		unset($HOC_ID);
		unset($HOC_DOCNO);
		unset($HOC_DOCDATE);
		unset($HOC_EFFECTIVEDATE);
		unset($HOC_ENDDATE);
		unset($HOC_ORG_ID);
		unset($HOC_ORG_NAME);
		unset($HOC_REMARK);
		unset($HOC_SEQ_NO);
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
	} // end if
//echo $PER_ID."--".$MINISTRY_NAME."--".$DEPARTMENT_NAME;
?>