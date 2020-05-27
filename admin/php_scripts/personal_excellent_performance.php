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
		$cmd = " update PER_EXCELLENT_PERFORMANCE set AUDIT_FLAG = 'N' where EP_ID in (".stripslashes($current_list).") ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		$cmd = " update PER_EXCELLENT_PERFORMANCE set AUDIT_FLAG = 'Y' where EP_ID in (".stripslashes($setflagaudit).") ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ��駤�ҡ�õ�Ǩ�ͺ������");
	}

	if($command=="ADD" && $PER_ID){
		$REH_DATE = save_date($REH_DATE); 

		$cmd = " select max(EP_ID) as max_id from PER_EXCELLENT_PERFORMANCE ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$EP_ID = $data[max_id] + 1;
		
		$cmd = " insert into PER_EXCELLENT_PERFORMANCE (EP_ID, PER_ID, PER_CARDNO, EP_DESC, EP_YEAR, EP_REMARK, UPDATE_USER, UPDATE_DATE)
						values ($EP_ID, $PER_ID, '$PER_CARDNO', '$EP_DESC', '$EP_YEAR', '$EP_REMARK', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > �����ŧҹ���� [$PER_ID : $EP_ID : $EP_DESC]");
		$ADD_NEXT = 1;
	} // end if

	if($command=="UPDATE" && $PER_ID && $EP_ID){
		$REH_DATE = save_date($REH_DATE); 
	
		$cmd = " UPDATE PER_EXCELLENT_PERFORMANCE SET
						PER_CARDNO='$PER_CARDNO', 
						EP_DESC='$EP_DESC', 
						EP_YEAR='$EP_YEAR', 
						EP_REMARK='$EP_REMARK', 
						UPDATE_USER=$SESS_USERID, 
						UPDATE_DATE='$UPDATE_DATE' 
					WHERE EP_ID=$EP_ID ";
		$db_dpis->send_cmd($cmd);
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ��䢼ŧҹ���� [$PER_ID : $EP_ID : $EP_DESC]");
	} // end if
	
	if($command=="DELETE" && $PER_ID && $EP_ID){
		$cmd = " select EP_DESC from PER_EXCELLENT_PERFORMANCE where EP_ID=$EP_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$EP_DESC = $data[EP_DESC];
		
		$cmd = " delete from PER_EXCELLENT_PERFORMANCE where EP_ID=$EP_ID ";
		$db_dpis->send_cmd($cmd);
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ź�ŧҹ���� [$PER_ID : $EP_ID : $EP_DESC]");
	} // end if

	if(($UPD && $PER_ID && $EP_ID) || ($VIEW && $PER_ID && $EP_ID)){
		$cmd = " SELECT		EP_DESC, EP_YEAR, EP_REMARK, UPDATE_USER, UPDATE_DATE
							FROM		PER_EXCELLENT_PERFORMANCE 
							WHERE	EP_ID=$EP_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$EP_DESC = trim($data[EP_DESC]);
		$EP_YEAR = trim($data[EP_YEAR]);
		$EP_REMARK = trim($data[EP_REMARK]);
		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);
	} // end if
	
	if( !$UPD && !$VIEW ){
		unset($EP_ID);
		unset($EP_DESC);
		unset($EP_YEAR);
		unset($EP_REMARK);
	
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
	} // end if
?>