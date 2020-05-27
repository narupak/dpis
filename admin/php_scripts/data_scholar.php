<?	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$UPDATE_DATE = date("Y-m-d H:i:s");

	if ($command == "ADD" || $command == "UPDATE") {
		if ($SC_TYPE != 1)	$PER_ID = "NULL";
		$SC_BIRTHDATE =  save_date($SC_BIRTHDATE);
		$SC_RECEIVEDATE =  save_date($SC_RECEIVEDATE);
		$SC_EXPECTDATE =  save_date($SC_EXPECTDATE);
		$SC_STARTDATE =  save_date($SC_STARTDATE);
		$SC_ENDDATE =  save_date($SC_ENDDATE);
		$SC_STARTDATE1 =  save_date($SC_STARTDATE1);
		$SC_ENDDATE1 =  save_date($SC_ENDDATE1);
		$SC_STARTDATE2 =  save_date($SC_STARTDATE2);
		$SC_ENDDATE2 =  save_date($SC_ENDDATE2);
		$SC_FINISHDATE =  save_date($SC_FINISHDATE);
		$SC_BACKDATE =  save_date($SC_BACKDATE);
		$SC_TEST_DATE =  save_date($SC_TEST_DATE);
		$SC_EL_TYPE = (trim($SC_EL_TYPE))? "'$SC_EL_TYPE'" : "NULL";
		$EL_CODE = (trim($EL_CODE))? "'$EL_CODE'" : "NULL";
		$EN_CODE = (trim($EN_CODE))? "'$EN_CODE'" : "NULL";
		$EM_CODE = (trim($EM_CODE))? "'$EM_CODE'" : "NULL";
		$INS_CODE = (trim($INS_CODE))? "'$INS_CODE'" : "NULL";
		$CT_CODE = (trim($CT_CODE))? "'$CT_CODE'" : "NULL";
		$EL_CODE1 = (trim($EL_CODE1))? "'$EL_CODE1'" : "NULL";
		$EN_CODE1 = (trim($EN_CODE1))? "'$EN_CODE1'" : "NULL";
		$EM_CODE1 = (trim($EM_CODE1))? "'$EM_CODE1'" : "NULL";
		$INS_CODE1 = (trim($INS_CODE1))? "'$INS_CODE1'" : "NULL";
		$CT_CODE1 = (trim($CT_CODE1))? "'$CT_CODE1'" : "NULL";
		$EL_CODE2 = (trim($EL_CODE2))? "'$EL_CODE2'" : "NULL";
		$EN_CODE2 = (trim($EN_CODE2))? "'$EN_CODE2'" : "NULL";
		$EM_CODE2 = (trim($EM_CODE2))? "'$EM_CODE2'" : "NULL";
		$INS_CODE2 = (trim($INS_CODE2))? "'$INS_CODE2'" : "NULL";
		$CT_CODE2 = (trim($CT_CODE2))? "'$CT_CODE2'" : "NULL";
		if (!$SC_TEST_RESULT) $SC_TEST_RESULT = "NULL";
		if (!$DEPARTMENT_ID) $DEPARTMENT_ID = "NULL";
	}

	if($command == "ADD" && trim(!$SC_ID)){
		$cmd = " select max(SC_ID) as max_id from PER_SCHOLAR ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$SC_ID = $data[max_id] + 1;	
		
		$cmd = " insert into PER_SCHOLAR (SC_ID, SC_TYPE, PER_ID, SC_CARDNO, PN_CODE, SC_NAME, SC_SURNAME, SC_BIRTHDATE, SC_GENDER, 
				SC_ADD1, EN_CODE, EM_CODE, SCH_CODE, INS_CODE, EL_CODE, SC_STARTDATE, SC_ENDDATE, SC_FINISHDATE, SC_BACKDATE, 
				PER_CARDNO, DEPARTMENT_ID, UPDATE_USER, UPDATE_DATE, SC_TEST_DATE, SC_TEST_RESULT, SC_REMARK, SC_INSTITUTE, 
				CT_CODE, SC_MAJOR_DESC, SC_RECEIVEDATE, SC_EXPECTDATE, SC_EL_TYPE, SC_LANGUAGE, EL_CODE1, EN_CODE1, EM_CODE1,
				INS_CODE1, SC_INSTITUTE1, CT_CODE1, SC_LANGUAGE1, SC_STARTDATE1, SC_ENDDATE1, EL_CODE2, EN_CODE2, EM_CODE2,
				INS_CODE2, SC_INSTITUTE2, CT_CODE2, SC_LANGUAGE2, SC_STARTDATE2, SC_ENDDATE2)
				values ($SC_ID, $SC_TYPE, $PER_ID, '$SC_CARDNO', '$PN_CODE', '$SC_NAME', '$SC_SURNAME', '$SC_BIRTHDATE', $SC_GENDER, 
				'$SC_ADD1', $EN_CODE, $EM_CODE, '$SCH_CODE', $INS_CODE, $EL_CODE, '$SC_STARTDATE', '$SC_ENDDATE', '$SC_FINISHDATE', 
				'$SC_BACKDATE', '$SC_CARDNO', $DEPARTMENT_ID, $SESS_USERID, '$UPDATE_DATE', '$SC_TEST_DATE', $SC_TEST_RESULT, 
				'$SC_REMARK', '$SC_INSTITUTE', $CT_CODE, '$SC_MAJOR_DESC', '$SC_RECEIVEDATE', '$SC_EXPECTDATE', $SC_EL_TYPE, '$SC_LANGUAGE', 
				$EL_CODE1, $EN_CODE1, $EM_CODE1, $INS_CODE1, '$SC_INSTITUTE1', $CT_CODE1, '$SC_LANGUAGE1', '$SC_STARTDATE1', '$SC_ENDDATE1', 
				$EL_CODE2, $EN_CODE2, $EM_CODE2, $INS_CODE2, '$SC_INSTITUTE2', $CT_CODE2, '$SC_LANGUAGE2', '$SC_STARTDATE2', '$SC_ENDDATE2') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			//echo "<hr>".$cmd;
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูลผู้ลาศึกษา/ฝึกอบรม [".trim($SC_ID)." : ".$PER_ID." : ".$SC_CARDNO." : ".$PER_NAME."]");		
	}

	if($command == "UPDATE" && trim($SC_ID)){
		$cmd = " update PER_SCHOLAR set 
					SC_TYPE=$SC_TYPE, 
					PER_ID=$PER_ID, 
					SC_CARDNO='$SC_CARDNO', 
					PN_CODE='$PN_CODE', 
					SC_NAME='$SC_NAME', 
					SC_SURNAME='$SC_SURNAME', 
					SC_BIRTHDATE='$SC_BIRTHDATE', 
					SC_GENDER=$SC_GENDER, 
					SC_ADD1='$SC_ADD1', 
					EN_CODE=$EN_CODE, 
					EM_CODE=$EM_CODE, 
					SCH_CODE='$SCH_CODE', 
					INS_CODE=$INS_CODE, 
					EL_CODE=$EL_CODE, 
					SC_STARTDATE='$SC_STARTDATE', 
					SC_ENDDATE='$SC_ENDDATE', 
					SC_FINISHDATE='$SC_FINISHDATE', 
					SC_BACKDATE='$SC_BACKDATE',
					PER_CARDNO='$SC_CARDNO', 
					SC_TEST_DATE='$SC_TEST_DATE', 
					SC_TEST_RESULT=$SC_TEST_RESULT, 
					SC_REMARK='$SC_REMARK',
					SC_INSTITUTE='$SC_INSTITUTE',
					CT_CODE=$CT_CODE, 
					SC_MAJOR_DESC='$SC_MAJOR_DESC',
					SC_RECEIVEDATE='$SC_RECEIVEDATE',
					SC_EXPECTDATE='$SC_EXPECTDATE',
					SC_EL_TYPE=$SC_EL_TYPE,
					SC_LANGUAGE='$SC_LANGUAGE',
					EL_CODE1=$EL_CODE1, 
					EN_CODE1=$EN_CODE1, 
					EM_CODE1=$EM_CODE1, 
					INS_CODE1=$INS_CODE1, 
					SC_INSTITUTE1='$SC_INSTITUTE1',
					CT_CODE1=$CT_CODE1, 
					SC_LANGUAGE1='$SC_LANGUAGE1',
					SC_STARTDATE1='$SC_STARTDATE1', 
					SC_ENDDATE1='$SC_ENDDATE1', 
					EL_CODE2=$EL_CODE2, 
					EN_CODE2=$EN_CODE2, 
					EM_CODE2=$EM_CODE2, 
					INS_CODE2=$INS_CODE2, 
					SC_INSTITUTE2='$SC_INSTITUTE2',
					CT_CODE2=$CT_CODE2, 
					SC_LANGUAGE2='$SC_LANGUAGE2',
					SC_STARTDATE2='$SC_STARTDATE2', 
					SC_ENDDATE2='$SC_ENDDATE2', 
					UPDATE_USER=$SESS_USERID, 
					UPDATE_DATE='$UPDATE_DATE' 
				where SC_ID=$SC_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูลผู้ลาศึกษา/ฝึกอบรม [".trim($SC_ID)." : ".$PER_ID." : ".$SC_CARDNO." : ".$PER_NAME."]");		
	}


	if($command == "DELETE" && trim($SC_ID)){
		$cmd = " select PER_ID, SC_CARDNO, SC_NAME, SC_SURNAME from PER_SCHOLAR where SC_ID=$SC_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PER_ID = $data[PER_ID];		
		$PER_CARDNO = $data[SC_CARDNO];
		$PER_NAME = $data[SC_NAME] ." " . $data[SC_SURNAME];

		$cmd = " delete from PER_SCHOLARINC where SC_ID=$SC_ID ";
		$db_dpis->send_cmd($cmd);
		
		$cmd = " delete from PER_SCHOLAR where SC_ID=$SC_ID ";
		$db_dpis->send_cmd($cmd);
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูลผู้ลาศึกษา/ฝึกอบรม [".trim($SC_ID)." : ".$PER_ID." : ".$PER_CARDNO." : ".$PER_NAME."]");
	}

	if($UPD || $VIEW){
		$cmd = " select SC_ID, SC_TYPE, PER_ID, SC_CARDNO, PN_CODE, SC_NAME, SC_SURNAME, SC_BIRTHDATE, SC_GENDER, 
						SC_ADD1, EN_CODE, EM_CODE, SCH_CODE, INS_CODE, EL_CODE, SC_STARTDATE, SC_ENDDATE, SC_FINISHDATE, SC_BACKDATE, 
						PER_CARDNO, DEPARTMENT_ID, UPDATE_USER, UPDATE_DATE, SC_TEST_DATE, SC_TEST_RESULT, SC_REMARK, SC_INSTITUTE, 
						CT_CODE, SC_MAJOR_DESC, SC_RECEIVEDATE, SC_EXPECTDATE, SC_EL_TYPE, SC_LANGUAGE, EL_CODE1, EN_CODE1, EM_CODE1,
						INS_CODE1, SC_INSTITUTE1, CT_CODE1, SC_LANGUAGE1, SC_STARTDATE1, SC_ENDDATE1, EL_CODE2, EN_CODE2, EM_CODE2,
						INS_CODE2, SC_INSTITUTE2, CT_CODE2, SC_LANGUAGE2, SC_STARTDATE2, SC_ENDDATE2 
						from PER_SCHOLAR where SC_ID=$SC_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$SC_TYPE = $data[SC_TYPE];
		$PER_ID = $data[PER_ID];

		if ($PER_ID) {
			$cmd = " select 	PER_CARDNO, PN_CODE, PER_NAME, PER_SURNAME, PER_BIRTHDATE, PER_ADD1, PER_GENDER  
					from 	PER_PERSONAL 
					where 	PER_ID=$PER_ID  ";
			$db_dpis2->send_cmd($cmd);
			//echo $cmd;
		//	$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$SC_CARDNO = $data2[PER_CARDNO];
			$PN_CODE = trim($data2[PN_CODE]);
			$SC_NAME = $data2[PER_NAME];
			$SC_SURNAME = $data2[PER_SURNAME];
			$SC_GENDER = $data2[PER_GENDER];
			//$SC_ADD1 = $data2[PER_ADD1];		
			
		} else {
			$SC_CARDNO = $data[SC_CARDNO];
			$PN_CODE = trim($data[PN_CODE]);
			$SC_NAME = $data[SC_NAME];
			$SC_SURNAME = $data[SC_SURNAME];
			$SC_GENDER = $data[SC_GENDER];
								
		}
		$SC_ADD1 = $data[SC_ADD1];	
		$SC_TEST_RESULT = $data[SC_TEST_RESULT];
		$SC_REMARK = $data[SC_REMARK];
		$SC_MAJOR_DESC = $data[SC_MAJOR_DESC];	
		$SC_EL_TYPE = $data[SC_EL_TYPE];	
		$SC_INSTITUTE = $data[SC_INSTITUTE];
		$SC_INSTITUTE1 = $data[SC_INSTITUTE1];
		$SC_INSTITUTE2 = $data[SC_INSTITUTE2];
		$SC_LANGUAGE = $data[SC_LANGUAGE];
		$SC_LANGUAGE1 = $data[SC_LANGUAGE1];
		$SC_LANGUAGE2 = $data[SC_LANGUAGE2];
		
		$SC_BIRTHDATE = show_date_format($data[SC_BIRTHDATE], 1);
		$SC_RECEIVEDATE = show_date_format($data[SC_RECEIVEDATE], 1);
		$SC_EXPECTDATE = show_date_format($data[SC_EXPECTDATE], 1);
		$SC_STARTDATE = show_date_format($data[SC_STARTDATE], 1);
		$SC_ENDDATE = show_date_format($data[SC_ENDDATE], 1);
		$SC_STARTDATE1 = show_date_format($data[SC_STARTDATE1], 1);
		$SC_ENDDATE1 = show_date_format($data[SC_ENDDATE1], 1);
		$SC_STARTDATE2 = show_date_format($data[SC_STARTDATE2], 1);
		$SC_ENDDATE2 = show_date_format($data[SC_ENDDATE2], 1);
		$SC_FINISHDATE = show_date_format($data[SC_FINISHDATE], 1);
		$SC_BACKDATE = show_date_format($data[SC_BACKDATE], 1);
		$SC_TEST_DATE = show_date_format($data[SC_TEST_DATE], 1);
		
		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);

		$EL_CODE = trim($data[EL_CODE]);
		$cmd = " select EL_NAME from PER_EDUCLEVEL where trim(EL_CODE)='$EL_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$EL_NAME = trim($data2[EL_NAME]);		

		$EN_CODE = trim($data[EN_CODE]);
		$cmd = " select EN_NAME from PER_EDUCNAME where trim(EN_CODE)='$EN_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$EN_NAME = trim($data2[EN_NAME]);
				
		$EM_CODE = trim($data[EM_CODE]);
		$cmd = " select EM_NAME from PER_EDUCMAJOR where trim(EM_CODE)='$EM_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$EM_NAME = trim($data2[EM_NAME]);
				
		$EL_CODE1 = trim($data[EL_CODE1]);
		$cmd = " select EL_NAME from PER_EDUCLEVEL where trim(EL_CODE)='$EL_CODE1' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$EL_NAME1 = trim($data2[EL_NAME]);		

		$EN_CODE1 = trim($data[EN_CODE1]);
		$cmd = " select EN_NAME from PER_EDUCNAME where trim(EN_CODE)='$EN_CODE1' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$EN_NAME1 = trim($data2[EN_NAME]);
				
		$EM_CODE1 = trim($data[EM_CODE1]);
		$cmd = " select EM_NAME from PER_EDUCMAJOR where trim(EM_CODE)='$EM_CODE1' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$EM_NAME1 = trim($data2[EM_NAME]);
				
		$EL_CODE2 = trim($data[EL_CODE2]);
		$cmd = " select EL_NAME from PER_EDUCLEVEL where trim(EL_CODE)='$EL_CODE2' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$EL_NAME2 = trim($data2[EL_NAME]);		

		$EN_CODE2 = trim($data[EN_CODE2]);
		$cmd = " select EN_NAME from PER_EDUCNAME where trim(EN_CODE)='$EN_CODE2' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$EN_NAME2 = trim($data2[EN_NAME]);
				
		$EM_CODE2 = trim($data[EM_CODE2]);
		$cmd = " select EM_NAME from PER_EDUCMAJOR where trim(EM_CODE)='$EM_CODE2' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$EM_NAME2 = trim($data2[EM_NAME]);
				
		$SCH_CODE = trim($data[SCH_CODE]);
		$cmd = " select SCH_NAME, SCH_OWNER, ST_NAME  
				from PER_SCHOLARSHIP a, PER_SCHOLARTYPE b 
				where trim(SCH_CODE)='$SCH_CODE' and a.ST_CODE=b.ST_CODE";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$SCH_NAME = trim($data2[SCH_NAME]);
		$SCH_OWNER = trim($data2[SCH_OWNER]);
		$ST_NAME = trim($data2[ST_NAME]);
				
		$INS_CODE = trim($data[INS_CODE]);
		$cmd = " select INS_NAME, CT_NAME  
				from PER_INSTITUTE a, PER_COUNTRY b
				where trim(INS_CODE)='$INS_CODE' and a.CT_CODE=b.CT_CODE";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$INS_NAME = trim($data2[INS_NAME]);
		$CT_NAME = trim($data2[CT_NAME]);
		if (!$CT_NAME) {
			$CT_CODE = trim($data[CT_CODE]);
			$cmd = " select CT_NAME from PER_COUNTRY where trim(CT_CODE)='$CT_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$CT_NAME = trim($data2[CT_NAME]);		
		}

		$INS_CODE1 = trim($data[INS_CODE1]);
		$cmd = " select INS_NAME, CT_NAME  
				from PER_INSTITUTE a, PER_COUNTRY b
				where trim(INS_CODE)='$INS_CODE1' and a.CT_CODE=b.CT_CODE";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$INS_NAME1 = trim($data2[INS_NAME]);
		$CT_NAME1 = trim($data2[CT_NAME]);
		if (!$CT_NAME1) {
			$CT_CODE1 = trim($data[CT_CODE1]);
			$cmd = " select CT_NAME from PER_COUNTRY where trim(CT_CODE)='$CT_CODE1' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$CT_NAME1 = trim($data2[CT_NAME]);		
		}

		$INS_CODE2 = trim($data[INS_CODE2]);
		$cmd = " select INS_NAME, CT_NAME  
				from PER_INSTITUTE a, PER_COUNTRY b
				where trim(INS_CODE)='$INS_CODE2' and a.CT_CODE=b.CT_CODE";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$INS_NAME2 = trim($data2[INS_NAME]);
		$CT_NAME2 = trim($data2[CT_NAME]);
		if (!$CT_NAME2) {
			$CT_CODE2 = trim($data[CT_CODE2]);
			$cmd = " select CT_NAME from PER_COUNTRY where trim(CT_CODE)='$CT_CODE2' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$CT_NAME2 = trim($data2[CT_NAME]);		
		}

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
		} elseif ($MFA_FLAG==1) {
			$MINISTRY_ID = 2895;
			$MINISTRY_NAME = "กระทรวงการต่างประเทศ";
			$search_ministry_id = 2895;
			$search_ministry_name = "กระทรวงการต่างประเทศ";
		}
	} // end if
	
	if( (!$UPD && !$VIEW && !$DEL && !$err_text) ){
		$SC_ID = "";
		if ($MFA_FLAG==1) {
			$SC_TYPE = 3;
			$EL_CODE = "40";
			$EL_NAME = "ปริญญาตรีหรือเทียบเท่า";		
		} else {
			$SC_TYPE = 1;
			$EL_CODE = "";
			$EL_NAME = "";		
		}
		$SC_EL_TYPE = 1;
		$PER_ID = "";
		$PN_CODE = "";		
		$SC_CARDNO = "";
		$SC_NAME = "";
		$SC_SURNAME = "";
		$SC_BIRTHDATE = "";
		$SC_GENDER = 1;
		$SC_ADD1 = "";
		$SC_MAJOR_DESC = "";
		$SC_RECEIVEDATE = "";
		$SC_EXPECTDATE = "";

		$EN_CODE = "";
		$EN_NAME = "";
		$EM_CODE = "";
		$EM_NAME = "";
		$INS_CODE = "";
		$INS_NAME = "";
		$CT_CODE = "";
		$CT_NAME = "";
		$SC_STARTDATE = "";
		$SC_ENDDATE = "";
		$SC_LANGUAGE = "";
		$SC_INSTITUTE = "";
		$EL_CODE1 = "";
		$EL_NAME1 = "";		
		$EN_CODE1 = "";
		$EN_NAME1 = "";
		$EM_CODE1 = "";
		$EM_NAME1 = "";
		$INS_CODE1 = "";
		$INS_NAME1 = "";
		$CT_CODE1 = "";
		$CT_NAME1 = "";
		$SC_STARTDATE1 = "";
		$SC_ENDDATE1 = "";
		$SC_LANGUAGE1 = "";
		$SC_INSTITUTE1 = "";
		$EL_CODE2 = "";
		$EL_NAME2 = "";		
		$EN_CODE2 = "";
		$EN_NAME2 = "";
		$EM_CODE2 = "";
		$EM_NAME2 = "";
		$INS_CODE2 = "";
		$INS_NAME2 = "";
		$CT_CODE2 = "";
		$CT_NAME2 = "";
		$SC_STARTDATE2 = "";
		$SC_ENDDATE2 = "";
		$SC_LANGUAGE2 = "";
		$SC_INSTITUTE2 = "";

		$SC_FINISHDATE = "";
		$SC_BACKDATE = "";
		$ST_NAME = "";
		$SCH_CODE = "";
		$SCH_NAME = "";
		$SCH_OWNER = "";
		$SC_TEST_DATE = "";
		$SC_TEST_RESULT = "";
		$SC_REMARK = "";
	} // end if
?>