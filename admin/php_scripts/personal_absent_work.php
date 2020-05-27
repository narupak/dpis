<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	if($PER_ID){
		if($DPISDB=="odbc"){
			$cmd = " select 	PRE.PN_NAME, PER.PER_NAME, PER.PER_SURNAME, PER.PER_CARDNO
					from		PER_PERSONAL as PER
							left join PER_PRENAME as PRE on (PER.PN_CODE=PRE.PN_CODE)
					where	PER.PER_ID=$PER_ID
						  ";
		}elseif($DPISDB=="oci8"){
			$cmd = " select 	PER_PRENAME.PN_NAME, PER_PERSONAL.PER_NAME, PER_PERSONAL.PER_SURNAME, 
							PER_PERSONAL.PER_CARDNO 
					from		PER_PERSONAL, PER_PRENAME
					where	PER_PERSONAL.PN_CODE=PER_PRENAME.PN_CODE(+) and PER_PERSONAL.PER_ID=$PER_ID
						  ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select 	PRE.PN_NAME, PER.PER_NAME, PER.PER_SURNAME, PER.PER_CARDNO
					from		PER_PERSONAL as PER
							left join PER_PRENAME as PRE on (PER.PN_CODE=PRE.PN_CODE)
					where	PER.PER_ID=$PER_ID
						  ";
		} // end if

		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PER_NAME = $data[PN_NAME] . $data[PER_NAME] ." ". $data[PER_SURNAME];
		$PER_CARDNO = (trim($data[PER_CARDNO]))? "'".trim($data[PER_CARDNO])."'" : "NULL";		
	} // end if

	$UPDATE_DATE = date("Y-m-d H:i:s");

	if($command=="ADD" && $PER_ID){
		$temp_date = explode("/", trim($ABS_STARTDATE));
		$ABS_STARTDATE = ($temp_date)? ($temp_date[2] - 543) ."-". $temp_date[1] ."-". $temp_date[0] : ""; 
		$temp_date = explode("/", trim($ABS_ENDDATE));
		$ABS_ENDDATE = ($temp_date)? ($temp_date[2] - 543) ."-". $temp_date[1] ."-". $temp_date[0] : ""; 
	
		$cmd = " select max(AS_ID) as max_id from PER_ABSENTSUM ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$AS_ID = $data[max_id] + 1;
		
		$cmd = " insert into PER_ABSENTSUM
					(AS_ID, PER_ID, AB_CODE, ABS_STARTDATE, ABS_STARTPERIOD, ABS_ENDDATE, ABS_ENDPERIOD, ABS_DAY, PER_CARDNO, UPDATE_USER, UPDATE_DATE)
					values
					($AS_ID, $PER_ID, '$AB_CODE', '$ABS_STARTDATE', '$ABS_STARTPERIOD', '$ABS_ENDDATE', '$ABS_ENDPERIOD', '$ABS_DAY', $PER_CARDNO, $SESS_USERID, '$UPDATE_DATE')   ";
		$db_dpis->send_cmd($cmd);

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูลประวัติการลา [$PER_ID : $AS_ID : $AB_CODE]");
	} // end if

	if($command=="UPDATE" && $PER_ID && $AS_ID){
		$temp_date = explode("/", trim($ABS_STARTDATE));
		$ABS_STARTDATE = ($temp_date)? ($temp_date[2] - 543) ."-". $temp_date[1] ."-". $temp_date[0] : ""; 
		$temp_date = explode("/", trim($ABS_ENDDATE));
		$ABS_ENDDATE = ($temp_date)? ($temp_date[2] - 543) ."-". $temp_date[1] ."-". $temp_date[0] : ""; 
	
		$cmd = " UPDATE PER_ABSENTSUM SET
					AB_CODE='$AB_CODE', ABS_STARTDATE='$ABS_STARTDATE', ABS_STARTPERIOD='$ABS_STARTPERIOD', 
					ABS_ENDDATE='$ABS_ENDDATE', ABS_ENDPERIOD='$ABS_ENDPERIOD', ABS_DAY='$ABS_DAY',  
					PER_CARDNO=$PER_CARDNO, UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE'
				WHERE AS_ID=$AS_ID";
		$db_dpis->send_cmd($cmd);
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูลประวัติการลา [$PER_ID : $AS_ID : $AB_CODE]");
	} // end if
	
	if($command=="DELETE" && $PER_ID && $AS_ID){
		$cmd = " select AB_CODE from PER_ABSENTSUM where AS_ID=$AS_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$AB_CODE = $data[AB_CODE];
		
		$cmd = " delete from PER_ABSENTSUM where AS_ID=$AS_ID ";
		$db_dpis->send_cmd($cmd);

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูลประวัติการลา [$PER_ID : $AS_ID : $AB_CODE]");
	} // end if

	if(($UPD && $PER_ID && $AS_ID) || ($VIEW && $PER_ID && $AS_ID)){
		$cmd = "	SELECT 		AS_ID, pah.AB_CODE, pat.AB_NAME, ABS_STARTDATE, ABS_STARTPERIOD, ABS_ENDDATE, ABS_ENDPERIOD, ABS_DAY   
				FROM		PER_ABSENTSUM pah, PER_ABSENTTYPE pat  
				WHERE		AS_ID=$AS_ID and pah.AB_CODE=pat.AB_CODE  ";	
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$temp_date = explode("-", trim($data[ABS_STARTDATE]));
		$ABS_STARTDATE = ($temp_date)? substr($temp_date[2], 0, 2) ."/". $temp_date[1] ."/". ($temp_date[0] + 543) : ""; 
		$temp_date = explode("-", trim($data[ABS_ENDDATE]));
		$ABS_ENDDATE = ($temp_date)? substr($temp_date[2], 0, 2) ."/". $temp_date[1] ."/". ($temp_date[0] + 543) : ""; 
		$ABS_STARTPERIOD = $data[ABS_STARTPERIOD];		
		$ABS_ENDPERIOD = $data[ABS_ENDPERIOD];		
		$ABS_DAY = $data[ABS_DAY];

		$AB_CODE = $data[AB_CODE];
		$AB_NAME = $data[AB_NAME];

	} // end if
	
	if( !$UPD && !$VIEW ){
		unset($AS_ID);
		unset($ABS_STARTDATE);
		unset($ABS_STARTPERIOD);		
		unset($ABS_ENDDATE);
		unset($ABS_ENDPERIOD);
		unset($ABS_DAY);		

		unset($AB_CODE);
		unset($AB_NAME);
		
		$ABS_STARTPERIOD = 3;
		$ABS_ENDPERIOD = 3;
		$ABS_LETTER = 3;
	} // end if
?>