<?	
	include("../php_scripts/connect_database.php");
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");
	include("php_scripts/load_per_control.php");

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$UPDATE_DATE = date("Y-m-d H:i:s");

	if($command == "CONFIRM"){
		$cmd = " DELETE FROM USERINFO WHERE SSN IS NOT NULL ";
//		$db_att->send_cmd($cmd);
//		$db_att->show_error();

		$cmd = " select PER_ID, PER_OFFNO, PER_CARDNO, PER_NAME, PER_SURNAME, PER_GENDER, POS_ID, PER_BIRTHDATE, PER_STARTDATE from PER_PERSONAL where PER_STATUS = 1 ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		while ( $data_dpis = $db_dpis->get_array() ) {
			$PER_ID = $data_dpis[PER_ID];
			$PER_OFFNO = trim($data_dpis[PER_OFFNO]);
			$PER_CARDNO = trim($data_dpis[PER_CARDNO]);
			$PER_NAME = trim($data_dpis[PER_NAME]);
			$PER_SURNAME = trim($data_dpis[PER_SURNAME]);
			$PER_GENDER = trim($data_dpis[PER_GENDER]);
			$POS_ID = $data_dpis[POS_ID];
			$PER_BIRTHDATE = show_date_format($data_dpis[PER_BIRTHDATE], 1);
			$PER_STARTDATE = show_date_format($data_dpis[PER_STARTDATE], 1);

			$cmd = " select PL_NAME from PER_LINE A, PER_POSITION B where B.POS_ID = $POS_ID AND A.PL_CODE = B.PL_CODE ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data_dpis2 = $db_dpis2->get_array();
			$PL_NAME = $data_dpis2[PL_NAME];
/*	
			$cmd = " INSERT INTO USERINFO (USERID, BADGENUMBER, SSN, NAME, GENDER, TITLE, BIRTHDAY, HIREDDAY) 
					 VALUES ($PER_ID, $PER_ID, '$PER_CARDNO', '$PER_NAME $PER_SURNAME', '$PER_GENDER', '$PL_NAME', '$PER_BIRTHDATE', '$PER_STARTDATE') "; */
			$cmd = " UPDATE USERINFO SET SSN = '$PER_CARDNO', 
					 NAME = '$PER_NAME $PER_SURNAME', 
					 GENDER = '$PER_GENDER', 
					 TITLE = '$PL_NAME', 
					 BIRTHDAY = '$PER_BIRTHDATE', 
					 HIREDDAY = '$PER_STARTDATE' 
					 WHERE BADGENUMBER = '$PER_OFFNO' "; 
			$db_att->send_cmd($cmd);
			$db_att->show_error();
		} // end while
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ถ่ายโอนข้อมูลบุคลากรไปสู่โปรแกรม HIP Time Attendance System [$DEPARTMENT_ID : $PER_ID : $TR_CARDNO : $TR_NAME]");
	} // end if
?>