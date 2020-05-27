<?	
	$UPDATE_DATE = date("Y-m-d H:i:s");

	if ($command == "ADD" || $command == "UPDATE") {
		$SE_BIRTHDATE =  save_date($SE_BIRTHDATE);
		$SE_STARTDATE =  save_date($SE_STARTDATE);
		$SE_ENDDATE =  save_date($SE_ENDDATE);
		if ($SE_TYPE == 2)	$PER_ID = "NULL";
		if (!$SE_CODE) $SE_CODE = "NULL";
		if (!$SE_NO) $SE_NO = "NULL";
		if (!$SC_TEST_RESULT) $SC_TEST_RESULT = "NULL";
	}

	if($command == "ADD" && trim(!$SE_ID)){
		$cmd = " select max(SE_ID) as max_id from PER_SENIOR_EXCUSIVE ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$SE_ID = $data[max_id] + 1;	
		
		$cmd = " insert into PER_SENIOR_EXCUSIVE (SE_ID, SE_TYPE, PER_ID, SE_CODE, SE_NO, PN_CODE, SE_NAME, 
						SE_SURNAME, SE_CARDNO, SE_MINISTRY_NAME, SE_DEPARTMENT_NAME, SE_ORG_NAME, 
						SE_ORG_NAME1, SE_ORG_NAME2, SE_LINE, LEVEL_NO, SE_MGT, SE_TRAIN_POSITION, 
						SE_TRAIN_MINISTRY, SE_TRAIN_DEPARTMENT, SE_PASS, SE_YEAR, SE_BIRTHDATE, 
						SE_STARTDATE, SE_ENDDATE, SE_TEL, SE_FAX, SE_MOBILE, SE_EMAIL, UPDATE_USER, UPDATE_DATE)
						values ($SE_ID, $SE_TYPE, $PER_ID, '$SE_CODE', '$SE_NO', '$PN_CODE', '$SE_NAME', '$SE_SURNAME', 
						'$SE_CARDNO', '$SE_MINISTRY_NAME', '$SE_DEPARTMENT_NAME', '$SE_ORG_NAME', 
						'$SE_ORG_NAME1', '$SE_ORG_NAME2', '$SE_LINE', '$LEVEL_NO', '$SE_MGT', '$SE_TRAIN_POSITION', 
						'$SE_TRAIN_MINISTRY', '$SE_TRAIN_DEPARTMENT', '$SE_PASS', '$SE_YEAR', '$SE_BIRTHDATE', 
						'$SE_STARTDATE', '$SE_ENDDATE', '$SE_TEL', '$SE_FAX', '$SE_MOBILE', '$SE_EMAIL', $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูลผู้ฝึกอบรมหลักสูตรนักบริหารระดับสูง [".trim($SE_ID)." : ".$PER_ID." : ".$SE_CARDNO." : ".$PER_NAME."]");		
	}

	if($command == "UPDATE" && trim($SE_ID)){
		$cmd = " update PER_SENIOR_EXCUSIVE set 
							SE_TYPE=$SE_TYPE, 
							PER_ID=$PER_ID, 
							SE_CODE='$SE_CODE', 
							SE_NO='$SE_NO', 
							PN_CODE='$PN_CODE', 
							SE_NAME='$SE_NAME', 
							SE_SURNAME='$SE_SURNAME', 
							SE_CARDNO='$SE_CARDNO', 
							SE_MINISTRY_NAME='$SE_MINISTRY_NAME', 
							SE_DEPARTMENT_NAME='$SE_DEPARTMENT_NAME', 
							SE_ORG_NAME='$SE_ORG_NAME', 
							SE_ORG_NAME1='$SE_ORG_NAME1', 
							SE_ORG_NAME2='$SE_ORG_NAME2', 
							SE_LINE='$SE_LINE', 
							LEVEL_NO='$LEVEL_NO', 
							SE_MGT='$SE_MGT',
							SE_TRAIN_POSITION='$SE_TRAIN_POSITION', 
							SE_TRAIN_MINISTRY='$SE_TRAIN_MINISTRY', 
							SE_TRAIN_DEPARTMENT='$SE_TRAIN_DEPARTMENT', 
							SE_PASS='$SE_PASS' ,
							SE_YEAR='$SE_YEAR' ,
							SE_BIRTHDATE='$SE_BIRTHDATE', 
							SE_STARTDATE='$SE_STARTDATE', 
							SE_ENDDATE='$SE_ENDDATE', 
							SE_TEL='$SE_TEL' ,
							SE_FAX='$SE_FAX', 
							SE_MOBILE='$SE_MOBILE', 
							SE_EMAIL='$SE_EMAIL', 
							UPDATE_USER=$SESS_USERID, 
							UPDATE_DATE='$UPDATE_DATE'
				where SE_ID=$SE_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูลผู้ฝึกอบรมหลักสูตรนักบริหารระดับสูง [".trim($SE_ID)." : ".$PER_ID." : ".$SE_CARDNO." : ".$PER_NAME."]");		
	}

	if($command == "DELETE" && trim($SE_ID)){
		$cmd = " select PER_ID, SE_CARDNO, SE_NAME, SE_SURNAME from PER_SENIOR_EXCUSIVE where SE_ID=$SE_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PER_ID = $data[PER_ID];		
		$SE_CARDNO = $data[SE_CARDNO];
		$PER_NAME = $data[SE_NAME] ." " . $data[SE_SURNAME];

		$cmd = " delete from PER_SENIOR_EXCUSIVE where SE_ID=$SE_ID ";
		$db_dpis->send_cmd($cmd);
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูลผู้ฝึกอบรมหลักสูตรนักบริหารระดับสูง [".trim($SE_ID)." : ".$PER_ID." : ".$SE_CARDNO." : ".$PER_NAME."]");
	}

	if($UPD || $VIEW){
		$cmd = " select SE_TYPE, PER_ID, SE_CODE, SE_NO, PN_CODE, SE_NAME, 
						SE_SURNAME, SE_CARDNO, SE_MINISTRY_NAME, SE_DEPARTMENT_NAME, SE_ORG_NAME, 
						SE_ORG_NAME1, SE_ORG_NAME2, SE_LINE, LEVEL_NO, SE_MGT, SE_TRAIN_POSITION, 
						SE_TRAIN_MINISTRY, SE_TRAIN_DEPARTMENT, SE_PASS, SE_YEAR, SE_BIRTHDATE, 
						SE_STARTDATE, SE_ENDDATE, SE_TEL, SE_FAX, SE_MOBILE, SE_EMAIL, UPDATE_USER, UPDATE_DATE 
						from PER_SENIOR_EXCUSIVE 
						where SE_ID=$SE_ID ";
		$db_dpis->send_cmd($cmd);
		//	$db_dpis->show_error();	//echo "$cmd";
		$data = $db_dpis->get_array();
		$SE_TYPE = $data[SE_TYPE];
		$PER_ID = $data[PER_ID];
		$SE_CODE = trim($data[SE_CODE]);	
		$SE_NO = trim($data[SE_NO]);
		if ($SE_CODE=="NULL") $SE_CODE = "";
		if ($SE_NO=="NULL") 		$SE_NO = "";

		$SE_MINISTRY_NAME = trim($data[SE_MINISTRY_NAME]);
		$cmd = " select ORG_ID from PER_ORG where ORG_NAME='$SE_MINISTRY_NAME' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$SE_MINISTRY_ID = $data2[ORG_ID];
		
		$SE_DEPARTMENT_NAME = trim($data[SE_DEPARTMENT_NAME]);
		$cmd = " select ORG_ID from PER_ORG where ORG_NAME='$SE_DEPARTMENT_NAME' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$SE_DEPARTMENT_ID = $data2[ORG_ID];
		
		$SE_ORG_NAME = trim($data[SE_ORG_NAME]);
		$SE_ORG_NAME1 = trim($data[SE_ORG_NAME1]);
		$SE_ORG_NAME2 = trim($data[SE_ORG_NAME2]);
		$SE_LINE = trim($data[SE_LINE]);
		$LEVEL_NO = trim($data[LEVEL_NO]);
		$SE_MGT = trim($data[SE_MGT]);
		$SE_TRAIN_POSITION = trim($data[SE_TRAIN_POSITION]);
		$SE_TRAIN_MINISTRY = trim($data[SE_TRAIN_MINISTRY]);
		$SE_TRAIN_DEPARTMENT = trim($data[SE_TRAIN_DEPARTMENT]);
		$SE_PASS = trim($data[SE_PASS]);
		$SE_YEAR = trim($data[SE_YEAR]);
		$SE_BIRTHDATE = show_date_format($data[SE_BIRTHDATE], 1);
		$SE_STARTDATE = show_date_format($data[SE_STARTDATE], 1);
		$SE_ENDDATE = show_date_format($data[SE_ENDDATE], 1);
		$SE_TEL = trim($data[SE_TEL]);
		$SE_FAX = trim($data[SE_FAX]);
		$SE_MOBILE = trim($data[SE_MOBILE]);
		$SE_EMAIL = trim($data[SE_EMAIL]);
		$UPDATE_USER = $data[UPDATE_USER];
		$SE_CARDNO = trim($data[SE_CARDNO]);
		$PN_CODE = trim($data[PN_CODE]);
		$SE_NAME = trim($data[SE_NAME]);
		$SE_SURNAME = trim($data[SE_SURNAME]);

		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);

		if ($PER_ID) {
			$cmd = " select 	PER_CARDNO, PN_CODE, PER_NAME, PER_SURNAME, PER_BIRTHDATE, a.LEVEL_NO, PL_CODE, PM_CODE, b.ORG_ID , PER_TYPE 
					from 	PER_PERSONAL a, PER_POSITION b 
					where 	PER_ID=$PER_ID and a.POS_ID=b.POS_ID(+) ";
			$db_dpis2->send_cmd($cmd);
			//echo $cmd;	//	$db_dpis2->show_error();	
			$data2 = $db_dpis2->get_array();
			$SE_CARDNO = trim($data2[PER_CARDNO]);
			$PN_CODE = trim($data2[PN_CODE]);
			$SE_NAME = trim($data2[PER_NAME]);
			$SE_SURNAME = trim($data2[PER_SURNAME]);
			$PER_TYPE = trim($data2[PER_TYPE]); 
		} 
		$PER_TYPE = (trim($PER_TYPE))? $PER_TYPE : 1;	

		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);
	} // end if
	
	if( (!$UPD && !$VIEW && !$DEL && !$err_text) ){
		$SE_ID = "";
		$SE_TYPE = 1;		$PER_TYPE = 1;
		$PER_ID = "";
		$SE_CODE = "";
		$SE_NO = "";
		$PN_CODE = "";		
		$SE_NAME = "";
		$SE_SURNAME = "";
		$SE_CARDNO = "";
		$SE_MINISTRY_ID = "";
		$SE_DEPARTMENT_ID = "";
		$SE_MINISTRY_NAME = "";
		$SE_DEPARTMENT_NAME = "";
		$SE_ORG_NAME = "";
		$SE_ORG_NAME1 = "";
		$SE_ORG_NAME2 = "";
		$SE_LINE = "";
		$LEVEL_NO = "";
		$SE_MGT = "";
		$SE_TRAIN_POSITION = "";
		$SE_TRAIN_MINISTRY = "";
		$SE_TRAIN_DEPARTMENT = "";
		$SE_PASS = "";
		$SE_YEAR = "";
		$SE_BIRTHDATE = "";
		$SE_STARTDATE = "";
		$SE_ENDDATE = "";
		$SE_TEL = "";
		$SE_FAX = "";
		$SE_MOBILE = "";
		$SE_EMAIL = "";
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
	} // end if
?>