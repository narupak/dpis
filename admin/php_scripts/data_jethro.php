<?	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$UPDATE_DATE = date("Y-m-d H:i:s");

	if ($command == "ADD" || $command == "UPDATE") {
		$MEETING_DATE =  save_date($MEETING_DATE);
		$OKP_MEETING_DATE =  save_date($OKP_MEETING_DATE);
		$PL_CODE = (trim($PL_CODE))? "'$PL_CODE'" : "NULL";
		$PM_CODE = (trim($PM_CODE))? "'$PM_CODE'" : "NULL";
		$CL_NAME = (trim($CL_NAME))? "'$CL_NAME'" : "NULL";
		$NEW_PL_CODE = (trim($NEW_PL_CODE))? "'$NEW_PL_CODE'" : "NULL";
		$NEW_PM_CODE = (trim($NEW_PM_CODE))? "'$NEW_PM_CODE'" : "NULL";
		$NEW_CL_NAME = (trim($NEW_CL_NAME))? "'$NEW_CL_NAME'" : "NULL";
		if (!$ORG_ID) $ORG_ID = "NULL";
		if (!$NEW_ORG_ID) $NEW_ORG_ID = "NULL";
	}

	if($command == "ADD" && trim($MEETING_TIME) && trim($MEETING_YEAR) && trim($POS_NO)){
		$cmd = " select max(JETHRO_ID) as max_id from PER_JETHRO ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$JETHRO_ID = $data[max_id] + 1;	
		
		$cmd = " insert into PER_JETHRO (JETHRO_ID, DEPARTMENT_ID, MEETING_TIME, MEETING_YEAR, POS_NO, MEETING_DATE, ORG_ID, ORG_NAME,	
						PL_CODE, PL_NAME, PM_CODE, PM_NAME, CL_NAME, NEW_ORG_ID, NEW_ORG_NAME, NEW_PL_CODE, NEW_PL_NAME,
						NEW_PM_CODE, NEW_PM_NAME, NEW_CL_NAME, COMMITTEE_RESULT, COMMITTEE_REMARK, OKP_MEETING_TIME,
						OKP_MEETING_YEAR, OKP_MEETING_DATE, OKP_COMMITTEE_RESULT, OKP_COMMITTEE_REMARK, UPDATE_USER, UPDATE_DATE)
						values ($JETHRO_ID, $DEPARTMENT_ID, '$MEETING_TIME', '$MEETING_YEAR', '$POS_NO', '$MEETING_DATE', $ORG_ID, '$ORG_NAME', 
						$PL_CODE, '$PL_NAME', $PM_CODE, '$PM_NAME', $CL_NAME, $NEW_ORG_ID, '$NEW_ORG_NAME', $NEW_PL_CODE, '$NEW_PL_NAME', 
						$NEW_PM_CODE, '$NEW_PM_NAME', $NEW_CL_NAME, '$COMMITTEE_RESULT', '$COMMITTEE_REMARK', '$OKP_MEETING_TIME', 
						'$OKP_MEETING_YEAR', '$OKP_MEETING_DATE', '$OKP_COMMITTEE_RESULT', '$OKP_COMMITTEE_REMARK', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		//echo "<hr>".$cmd;
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูลการพิจารณากำหนดตำแหน่งระดับสูง [$JETHRO_ID : $DEPARTMENT_ID : $MEETING_TIME/$MEETING_YEAR : $POS_NO]");		
	}

	if($command == "UPDATE" && trim($JETHRO_ID)){
		$cmd = " update PER_JETHRO set 
					DEPARTMENT_ID=$DEPARTMENT_ID, 
					MEETING_TIME='$MEETING_TIME', 
					MEETING_YEAR='$MEETING_YEAR', 
					POS_NO='$POS_NO', 
					MEETING_DATE='$MEETING_DATE', 
					ORG_ID=$ORG_ID, 
					ORG_NAME='$ORG_NAME', 
					PL_CODE=$PL_CODE, 
					PL_NAME='$PL_NAME', 
					PM_CODE=$PM_CODE, 
					PM_NAME='$PM_NAME', 
					CL_NAME=$CL_NAME, 
					NEW_ORG_ID=$NEW_ORG_ID, 
					NEW_ORG_NAME='$NEW_ORG_NAME',
					NEW_PL_CODE=$NEW_PL_CODE, 
					NEW_PL_NAME='$NEW_PL_NAME', 
					NEW_PM_CODE=$NEW_PM_CODE,
					NEW_PM_NAME='$NEW_PM_NAME',
					NEW_CL_NAME=$NEW_CL_NAME,
					COMMITTEE_RESULT='$COMMITTEE_RESULT',
					COMMITTEE_REMARK='$COMMITTEE_REMARK',
					OKP_MEETING_TIME='$OKP_MEETING_TIME',
					OKP_MEETING_YEAR='$OKP_MEETING_YEAR',
					OKP_MEETING_DATE='$OKP_MEETING_DATE',
					OKP_COMMITTEE_RESULT='$OKP_COMMITTEE_RESULT', 
					OKP_COMMITTEE_REMARK='$OKP_COMMITTEE_REMARK', 
					UPDATE_USER=$SESS_USERID, 
					UPDATE_DATE='$UPDATE_DATE' 
				where JETHRO_ID=$JETHRO_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูลการพิจารณากำหนดตำแหน่งระดับสูง [$JETHRO_ID : $DEPARTMENT_ID : $MEETING_TIME/$MEETING_YEAR : $POS_NO]");		
	}

	if($command == "DELETE" && trim($JETHRO_ID)){
		$cmd = " select DEPARTMENT_ID, MEETING_TIME, MEETING_YEAR, POS_NO from PER_JETHRO where JETHRO_ID=$JETHRO_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$DEPARTMENT_ID = $data[DEPARTMENT_ID];		
		$MEETING_TIME = $data[MEETING_TIME];
		$MEETING_YEAR = $data[MEETING_YEAR];
		$POS_NO = $data[POS_NO];

		$cmd = " delete from PER_JETHRO where JETHRO_ID=$JETHRO_ID ";
		$db_dpis->send_cmd($cmd);
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูลการพิจารณากำหนดตำแหน่งระดับสูง [$JETHRO_ID : $DEPARTMENT_ID : $MEETING_TIME/$MEETING_YEAR : $POS_NO]");
	}

	if($UPD || $VIEW){
		$cmd = " select DEPARTMENT_ID, MEETING_TIME, MEETING_YEAR, POS_NO, MEETING_DATE, ORG_ID, ORG_NAME,	
										PL_CODE, PL_NAME, PM_CODE, PM_NAME, CL_NAME, NEW_ORG_ID, NEW_ORG_NAME, NEW_PL_CODE, NEW_PL_NAME,
										NEW_PM_CODE, NEW_PM_NAME, NEW_CL_NAME, COMMITTEE_RESULT, COMMITTEE_REMARK, OKP_MEETING_TIME,
										OKP_MEETING_YEAR, OKP_MEETING_DATE, OKP_COMMITTEE_RESULT, OKP_COMMITTEE_REMARK, UPDATE_USER, UPDATE_DATE 
						from PER_JETHRO 
						where JETHRO_ID=$JETHRO_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MEETING_TIME = $data[MEETING_TIME];
		$MEETING_YEAR = $data[MEETING_YEAR];
		$POS_NO = $data[POS_NO];
		$ORG_ID = trim($data[ORG_ID]);
		$ORG_NAME = $data[ORG_NAME];
		$PL_CODE = $data[PL_CODE];
		$PL_NAME = $data[PL_NAME];
		$PM_CODE = $data[PM_CODE];	
		$PM_NAME = $data[PM_NAME];
		$CL_NAME = $data[CL_NAME];
		$NEW_ORG_ID = $data[NEW_ORG_ID];	
		$NEW_ORG_NAME = $data[NEW_ORG_NAME];	
		$NEW_PL_CODE = $data[NEW_PL_CODE];
		$NEW_PL_NAME = $data[NEW_PL_NAME];
		$NEW_PM_CODE = $data[NEW_PM_CODE];
		$NEW_PM_NAME = $data[NEW_PM_NAME];
		$NEW_CL_NAME = $data[NEW_CL_NAME];
		$COMMITTEE_RESULT = $data[COMMITTEE_RESULT];
		$COMMITTEE_REMARK = trim($data[COMMITTEE_REMARK]);
		$OKP_MEETING_TIME = trim($data[OKP_MEETING_TIME]);
		$OKP_MEETING_YEAR = trim($data[OKP_MEETING_YEAR]);
		$OKP_COMMITTEE_RESULT = trim($data[OKP_COMMITTEE_RESULT]);
		$OKP_COMMITTEE_REMARK = trim($data[OKP_COMMITTEE_REMARK]);
		
		$MEETING_DATE = show_date_format($data[MEETING_DATE], 1);
		$OKP_MEETING_DATE = show_date_format($data[OKP_MEETING_DATE], 1);
		
		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);

		$DEPARTMENT_ID = $data[DEPARTMENT_ID];
		if ($DEPARTMENT_ID) {
			$cmd = " select ORG_ID_REF, ORG_NAME from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$DEPARTMENT_NAME = $data[ORG_NAME];
			$MINISTRY_ID = $data[ORG_ID_REF];
			
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$MINISTRY_NAME = $data[ORG_NAME];
		}
	} // end if
	
	if( (!$UPD && !$VIEW && !$DEL && !$err_text) ){
		$JETHRO_ID = "";
		$DEPARTMENT_NAME = "";
		$MINISTRY_NAME = "";
		$MEETING_TIME = "";
		$MEETING_YEAR = "";		
		$POS_NO = "";
		$MEETING_DATE = "";
		$ORG_ID = "";
		$ORG_NAME = "";
		$PL_CODE = "";
		$PL_NAME = "";
		$PM_CODE = "";
		$PM_NAME = "";
		$CL_NAME = "";

		$NEW_ORG_ID = "";
		$NEW_ORG_NAME = "";
		$NEW_PL_CODE = "";
		$NEW_PL_NAME = "";
		$NEW_PM_CODE = "";
		$NEW_PM_NAME = "";
		$NEW_CL_NAME = "";
		$COMMITTEE_RESULT = 1;
		$COMMITTEE_REMARK = "";
		$OKP_MEETING_TIME = "";
		$OKP_MEETING_YEAR = "";
		$OKP_MEETING_DATE = "";
		$OKP_COMMITTEE_RESULT = "";
		$OKP_COMMITTEE_REMARK = "";		
		$SHOW_UPDATE_USER = "";
		$SHOW_UPDATE_DATE =  "";
	} // end if
?>