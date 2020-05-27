<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	if($PER_ID){
		if($DPISDB=="odbc"){
			$cmd = " select 	PRE.PN_NAME, PER.PER_NAME, PER.PER_SURNAME
							 from		PER_PERSONAL as PER
											left join PER_PRENAME as PRE on (PER.PN_CODE=PRE.PN_CODE)
							 where	PER.PER_ID=$PER_ID
						  ";
		}elseif($DPISDB=="oci8"){
			$cmd = " select 	PER_PRENAME.PN_NAME, PER_PERSONAL.PER_NAME, PER_PERSONAL.PER_SURNAME
 							 from		PER_PERSONAL, PER_PRENAME
							 where	PER_PERSONAL.PN_CODE=PER_PRENAME.PN_CODE(+) and PER_PERSONAL.PER_ID=$PER_ID
						  ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select 	PRE.PN_NAME, PER.PER_NAME, PER.PER_SURNAME
							 from		PER_PERSONAL as PER
											left join PER_PRENAME as PRE on (PER.PN_CODE=PRE.PN_CODE)
							 where	PER.PER_ID=$PER_ID
						  ";
		} // end if
		
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$PER_NAME = $data[PN_NAME] . $data[PER_NAME] ." ". $data[PER_SURNAME];
	} // end if

	$UPDATE_DATE = date("Y-m-d H:i:s");

	if($command=="ADD" && $PER_ID){
		$cmd = " select max(HEIR_ID) as MAX_ID from PER_HEIR ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_data();
		$HEIR_ID = $data[MAX_ID] + 1;
		
		$cmd = " insert into PER_HEIR
					(HEIR_ID, PER_ID, HEIR_NAME, HR_CODE, HEIR_STATUS, HEIR_BIRTHDAY, HEIR_TAX, UPDATE_USER, UPDATE_DATE)
					values
					($HEIR_ID, $PER_ID, '$HEIR_NAME', '$HR_CODE', '$HEIR_STATUS', '$HEIR_BIRTHDAY', '$HEIR_TAX', $SESS_USERID, '$UPDATE_DATE')   ";
		$db_dpis->send_cmd($cmd);

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูลทายาท [$PER_ID : $HEIR_ID : $HR_CODE]");
	} // end if

	if($command=="UPDATE" && $PER_ID && $HEIR_ID){
		$cmd = " UPDATE PER_HEIR SET
					HEIR_NAME='$HEIR_NAME', HR_CODE='$HR_CODE', HEIR_STATUS='$HEIR_STATUS', HEIR_TAX='$HEIR_TAX', 
					HEIR_BIRTHDAY='$HEIR_BIRTHDAY', UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE'
				WHERE HEIR_ID=$HEIR_ID";
		$db_dpis->send_cmd($cmd);
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูลทายาท [$PER_ID : $HEIR_ID : $HR_CODE]");
	} // end if
	
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
		$cmd = "	SELECT 		HEIR_ID, HEIR_NAME, ph.HR_CODE, pht.HR_NAME, HEIR_BIRTHDAY, HEIR_STATUS, HEIR_TAX  
				FROM		PER_HEIR ph, PER_HEIRTYPE pht  
				WHERE		HEIR_ID=$HEIR_ID and ph.HR_CODE=pht.HR_CODE  ";	
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$HEIR_NAME = $data[HEIR_NAME];
		$HEIR_STATUS = $data[HEIR_STATUS];
		$HEIR_BIRTHDAY = $data[HEIR_BIRTHDAY];
		$HEIR_TAX = $data[HEIR_TAX];

		$HR_CODE = $data[HR_CODE];
		$HR_NAME = $data[HR_NAME];

	} // end if
	
	if( !$UPD && !$VIEW ){
		unset($HEIR_ID);
		unset($HEIR_NAME);
		unset($HEIR_STATUS);		
		unset($HEIR_BIRTHDAY);
		unset($HEIR_TAX);

		unset($HR_CODE);
		unset($HR_NAME);
		
		$HEIR_TAX = 1;
		$HEIR_STATUS = 1;
	} // end if
?>