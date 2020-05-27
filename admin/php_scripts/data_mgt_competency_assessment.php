<?	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$UPDATE_DATE = date("Y-m-d H:i:s");

	if ($command == "ADD" || $command == "UPDATE") {
		$CA_TEST_DATE =  save_date($CA_TEST_DATE);
		$CA_APPROVE_DATE =  save_date($CA_APPROVE_DATE);
		$CA_DOC_DATE =  save_date($CA_DOC_DATE);
		if ($CA_TYPE == 2)	$PER_ID = "NULL";
		if(!$ORG_CODE)	$ORG_CODE= "NULL";	//คืออะไร????
		if (!$CA_COURSE) $CA_COURSE = 0;		//เก็บเป็นตัวเลข
		if(!$CA_CODE)	$CA_CODE= "NULL";	//คืออะไร????
		if (!$CA_CONSISTENCY) $CA_CONSISTENCY = "NULL";
		if (!$CA_SCORE_1) $CA_SCORE_1 = "NULL";
		if (!$CA_SCORE_2) $CA_SCORE_2 = "NULL";
		if (!$CA_SCORE_3) $CA_SCORE_3 = "NULL";
		if (!$CA_SCORE_4) $CA_SCORE_4 = "NULL";
		if (!$CA_SCORE_5) $CA_SCORE_5 = "NULL";
		if (!$CA_SCORE_6) $CA_SCORE_6 = "NULL";
		if (!$CA_SCORE_7) $CA_SCORE_7 = "NULL";
		if (!$CA_SCORE_8) $CA_SCORE_8 = "NULL";
		if (!$CA_SCORE_9) $CA_SCORE_9 = "NULL";
		if (!$CA_SCORE_10) $CA_SCORE_10 = "NULL";
		if (!$CA_SCORE_11) $CA_SCORE_11 = "NULL";
		if (!$CA_SCORE_12) $CA_SCORE_12 = "NULL";
		if (!$CA_MEAN) $CA_MEAN = "NULL";
		if (!$CA_NEW_SCORE_1) $CA_NEW_SCORE_1 = "NULL";
		if (!$CA_NEW_SCORE_2) $CA_NEW_SCORE_2 = "NULL";
		if (!$CA_NEW_SCORE_3) $CA_NEW_SCORE_3 = "NULL";
		if (!$CA_NEW_SCORE_4) $CA_NEW_SCORE_4 = "NULL";
		if (!$CA_NEW_SCORE_5) $CA_NEW_SCORE_5 = "NULL";
		if (!$CA_NEW_SCORE_6) $CA_NEW_SCORE_6 = "NULL";
		if (!$CA_NEW_SCORE_7) $CA_NEW_SCORE_7 = "NULL";
		if (!$CA_NEW_SCORE_8) $CA_NEW_SCORE_8 = "NULL";
		if (!$CA_NEW_SCORE_9) $CA_NEW_SCORE_9 = "NULL";
		if (!$CA_NEW_SCORE_10) $CA_NEW_SCORE_10 = "NULL";
		if (!$CA_NEW_SCORE_11) $CA_NEW_SCORE_11 = "NULL";
		if (!$CA_NEW_MEAN) $CA_NEW_MEAN = "NULL";
		
		$cmd = " select max(CA_SEQ) as max_seq from PER_MGT_COMPETENCY_ASSESSMENT ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$CA_SEQ = $data[max_seq] + 1;
	}

	if($command == "ADD" && trim(!$CA_ID)){
		$cmd = " select max(CA_ID) as max_id from PER_MGT_COMPETENCY_ASSESSMENT ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$CA_ID = $data[max_id] + 1;
		
		$cmd = " insert into PER_MGT_COMPETENCY_ASSESSMENT (CA_ID, CA_COURSE, ORG_CODE, CA_SEQ, CA_CODE, CA_TYPE, PER_ID, 
							CA_TEST_DATE, CA_APPROVE_DATE, PN_CODE, CA_NAME, CA_SURNAME, CA_CARDNO, CA_CONSISTENCY, CA_SCORE_1, 
							CA_SCORE_2, CA_SCORE_3, CA_SCORE_4, CA_SCORE_5, CA_SCORE_6, CA_SCORE_7, CA_SCORE_8, CA_SCORE_9, CA_SCORE_10,
							CA_SCORE_11, CA_SCORE_12, CA_MEAN, CA_MINISTRY_NAME, CA_DEPARTMENT_NAME, CA_ORG_NAME, CA_ORG_NAME1,
							CA_ORG_NAME2, CA_LINE, LEVEL_NO, CA_MGT, CA_POSITION, CA_POS_NO, CA_DOC_DATE, CA_DOC_NO, CA_NEW_SCORE_1, 
							CA_NEW_SCORE_2, CA_NEW_SCORE_3, CA_NEW_SCORE_4, CA_NEW_SCORE_5, CA_NEW_SCORE_6, CA_NEW_SCORE_7, 
							CA_NEW_SCORE_8, CA_NEW_SCORE_9, CA_NEW_SCORE_10, CA_NEW_SCORE_11, CA_NEW_MEAN, CA_REMARK, UPDATE_USER, UPDATE_DATE)
				values ($CA_ID, $CA_COURSE, '$ORG_CODE', $CA_SEQ, '$CA_CODE', $CA_TYPE, $PER_ID, '$CA_TEST_DATE', '$CA_APPROVE_DATE', 
							'$PN_CODE', '$CA_NAME', '$CA_SURNAME', '$CA_CARDNO', $CA_CONSISTENCY, $CA_SCORE_1, $CA_SCORE_2, $CA_SCORE_3, 
							$CA_SCORE_4, $CA_SCORE_5, $CA_SCORE_6, $CA_SCORE_7, $CA_SCORE_8, $CA_SCORE_9, $CA_SCORE_10,
							$CA_SCORE_11, $CA_SCORE_12, $CA_MEAN, '$CA_MINISTRY_NAME', '$CA_DEPARTMENT_NAME', '$CA_ORG_NAME', 
							'$CA_ORG_NAME1', '$CA_ORG_NAME2', '$CA_LINE', '$LEVEL_NO', '$CA_MGT', '$CA_POSITION', '$CA_POS_NO', '$CA_DOC_DATE', 
							'$CA_DOC_NO', $CA_NEW_SCORE_1, $CA_NEW_SCORE_2, $CA_NEW_SCORE_3, $CA_NEW_SCORE_4, $CA_NEW_SCORE_5, 
							$CA_NEW_SCORE_6, $CA_NEW_SCORE_7, $CA_NEW_SCORE_8, $CA_NEW_SCORE_9, $CA_NEW_SCORE_10,	$CA_NEW_SCORE_11, 
							$CA_NEW_MEAN, '$CA_REMARK', $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
//$db_dpis->show_error();
//echo "<hr>".$cmd;
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูลผู้ลาศึกษา/ฝึกอบรม [".trim($CA_ID)." : ".$PER_ID." : ".$CA_CARDNO." : ".$PER_NAME."]");		
	}

	if($command == "UPDATE" && trim($CA_ID)){
		$cmd = " update PER_MGT_COMPETENCY_ASSESSMENT set 
					CA_COURSE = $CA_COURSE, 
					ORG_CODE = '$ORG_CODE', 
					CA_SEQ = $CA_SEQ, 
					CA_CODE = '$CA_CODE', 
					CA_TYPE = $CA_TYPE, 
					PER_ID = $PER_ID, 
					CA_TEST_DATE = '$CA_TEST_DATE', 
					CA_APPROVE_DATE = '$CA_APPROVE_DATE', 
					PN_CODE = '$PN_CODE', 
					CA_NAME = '$CA_NAME', 
					CA_SURNAME = '$CA_SURNAME', 
					CA_CARDNO = '$CA_CARDNO', 
					CA_CONSISTENCY = $CA_CONSISTENCY, 
					CA_SCORE_1 = $CA_SCORE_1, 
					CA_SCORE_2 = $CA_SCORE_2, 
					CA_SCORE_3 = $CA_SCORE_3, 
					CA_SCORE_4 = $CA_SCORE_4, 
					CA_SCORE_5 = $CA_SCORE_5, 
					CA_SCORE_6 = $CA_SCORE_6, 
					CA_SCORE_7 = $CA_SCORE_7, 
					CA_SCORE_8 = $CA_SCORE_8, 
					CA_SCORE_9 = $CA_SCORE_9, 
					CA_SCORE_10 = $CA_SCORE_10, 
					CA_SCORE_11 = $CA_SCORE_11, 
					CA_SCORE_12 = $CA_SCORE_12, 
					CA_MEAN = $CA_MEAN, 
					CA_MINISTRY_NAME = '$CA_MINISTRY_NAME', 
					CA_DEPARTMENT_NAME = '$CA_DEPARTMENT_NAME', 
					CA_ORG_NAME = '$CA_ORG_NAME', 
					CA_ORG_NAME1 = '$CA_ORG_NAME1', 
					CA_ORG_NAME2 = '$CA_ORG_NAME2',
					CA_LINE = '$CA_LINE',
					LEVEL_NO = '$LEVEL_NO', 
					CA_MGT = '$CA_MGT', 
					CA_POSITION = '$CA_POSITION',
					CA_POS_NO = '$CA_POS_NO',
					CA_DOC_DATE = '$CA_DOC_DATE', 
					CA_DOC_NO = '$CA_DOC_NO', 
					CA_NEW_SCORE_1 = $CA_NEW_SCORE_1, 
					CA_NEW_SCORE_2 = $CA_NEW_SCORE_2, 
					CA_NEW_SCORE_3 = $CA_NEW_SCORE_3, 
					CA_NEW_SCORE_4 = $CA_NEW_SCORE_4, 
					CA_NEW_SCORE_5 = $CA_NEW_SCORE_5, 
					CA_NEW_SCORE_6 = $CA_NEW_SCORE_6, 
					CA_NEW_SCORE_7 = $CA_NEW_SCORE_7, 
					CA_NEW_SCORE_8 = $CA_NEW_SCORE_8, 
					CA_NEW_SCORE_9 = $CA_NEW_SCORE_9, 
					CA_NEW_SCORE_10 = $CA_NEW_SCORE_10, 
					CA_NEW_SCORE_11 = $CA_NEW_SCORE_11, 
					CA_NEW_MEAN = $CA_NEW_MEAN, 
					CA_REMARK = '$CA_REMARK', 
					UPDATE_USER = $SESS_USERID, 
					UPDATE_DATE = '$UPDATE_DATE' 
				where CA_ID = $CA_ID ";
		$db_dpis->send_cmd($cmd);
//$db_dpis->show_error();
//echo "<hr>".$cmd;
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูลผู้ลาศึกษา/ฝึกอบรม [".trim($CA_ID)." : ".$PER_ID." : ".$CA_CARDNO." : ".$PER_NAME."]");		
	}


	if($command == "DELETE" && trim($CA_ID)){
		$cmd = " select PER_ID, CA_CARDNO, CA_NAME, CA_SURNAME from PER_MGT_COMPETENCY_ASSESSMENT where CA_ID=$CA_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PER_ID = $data[PER_ID];		
		$PER_CARDNO = $data[CA_CARDNO];
		$PER_NAME = $data[CA_NAME] ." " . $data[CA_SURNAME];

		$cmd = " delete from PER_MGT_COMPETENCY_ASSESSMENT where CA_ID=$CA_ID ";
		$db_dpis->send_cmd($cmd);
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูลผู้ลาศึกษา/ฝึกอบรม [".trim($CA_ID)." : ".$PER_ID." : ".$PER_CARDNO." : ".$PER_NAME."]");
	}

//	echo "UPD=$UPD , VIEW=$VIEW<br>";
	if($UPD || $VIEW){
		$cmd = " select CA_COURSE, ORG_CODE, CA_SEQ, CA_CODE, CA_TYPE, PER_ID, CA_TEST_DATE, CA_APPROVE_DATE, PN_CODE, 
							CA_NAME, CA_SURNAME, CA_CARDNO, CA_CONSISTENCY, CA_SCORE_1, CA_SCORE_2, CA_SCORE_3, CA_SCORE_4, 
							CA_SCORE_5, CA_SCORE_6, CA_SCORE_7, CA_SCORE_8, CA_SCORE_9, CA_SCORE_10, CA_SCORE_11, CA_SCORE_12, 
							CA_MEAN, CA_MINISTRY_NAME, CA_DEPARTMENT_NAME, CA_ORG_NAME, CA_ORG_NAME1, CA_ORG_NAME2, CA_LINE, 
							LEVEL_NO, CA_MGT, CA_POSITION, CA_POS_NO, CA_DOC_DATE, CA_DOC_NO, CA_NEW_SCORE_1, CA_NEW_SCORE_2, 
							CA_NEW_SCORE_3, CA_NEW_SCORE_4, CA_NEW_SCORE_5, CA_NEW_SCORE_6, CA_NEW_SCORE_7, CA_NEW_SCORE_8, 
							CA_NEW_SCORE_9, CA_NEW_SCORE_10, CA_NEW_SCORE_11, CA_NEW_MEAN, CA_REMARK, UPDATE_USER, UPDATE_DATE
							from PER_MGT_COMPETENCY_ASSESSMENT 
							where CA_ID=$CA_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
//		echo "$cmd<br>";
		$data = $db_dpis->get_array();
		$CA_COURSE = $data[CA_COURSE];
		$ORG_CODE = trim($data[ORG_CODE]);
		$CA_SEQ = $data[CA_SEQ];
		$CA_CODE = trim($data[CA_CODE]);
		if ($CA_CODE=="NULL") 	$CA_CODE = "";
		if ($CA_NO=="NULL") 		$CA_NO = "";
		
		$CA_TYPE = $data[CA_TYPE];
		$PER_ID = $data[PER_ID];
		$CA_CARDNO = $data[CA_CARDNO];
		$PN_CODE = trim($data[PN_CODE]);
		$CA_NAME = $data[CA_NAME];
		$CA_SURNAME = $data[CA_SURNAME];

		$CA_TEST_DATE = show_date_format($data[CA_TEST_DATE], 1);
		$CA_APPROVE_DATE = show_date_format($data[CA_APPROVE_DATE], 1);
		$CA_CONSISTENCY = $data[CA_CONSISTENCY];	
		$CA_SCORE_1 = $data[CA_SCORE_1];
		$CA_SCORE_2 = $data[CA_SCORE_2];
		$CA_SCORE_3 = $data[CA_SCORE_3];
		$CA_SCORE_4 = $data[CA_SCORE_4];
		$CA_SCORE_5 = $data[CA_SCORE_5];
		$CA_SCORE_6 = $data[CA_SCORE_6];
		$CA_SCORE_7 = $data[CA_SCORE_7];
		$CA_SCORE_8 = $data[CA_SCORE_8];
		$CA_SCORE_9 = $data[CA_SCORE_9];
		$CA_SCORE_10 = $data[CA_SCORE_10];
		$CA_SCORE_11 = $data[CA_SCORE_11];
		$CA_SCORE_12 = $data[CA_SCORE_12];
		$CA_MEAN = $data[CA_MEAN];

		$CA_MINISTRY_NAME = trim($data[CA_MINISTRY_NAME]);
		$cmd = " select ORG_ID from PER_ORG where ORG_NAME='$CA_MINISTRY_NAME' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$CA_MINISTRY_ID = $data2[ORG_ID];
		
		$CA_DEPARTMENT_NAME = trim($data[CA_DEPARTMENT_NAME]);
		$cmd = " select ORG_ID from PER_ORG where ORG_NAME='$CA_DEPARTMENT_NAME' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$CA_DEPARTMENT_ID = $data2[ORG_ID];
		
		$CA_ORG_NAME = trim($data[CA_ORG_NAME]);
		$CA_ORG_NAME1 = trim($data[CA_ORG_NAME1]);
		$CA_ORG_NAME2 = trim($data[CA_ORG_NAME2]);
		$CA_LINE = trim($data[CA_LINE]);
		$LEVEL_NO = trim($data[LEVEL_NO]);
		$CA_MGT = trim($data[CA_MGT]);
		$CA_POSITION = trim($data[CA_POSITION]);
		$CA_POS_NO = trim($data[CA_POS_NO]);
		$CA_DOC_DATE = show_date_format($data[CA_DOC_DATE], 1);
		$CA_DOC_NO = trim($data[CA_DOC_NO]);
		$CA_NEW_SCORE_1 = $data[CA_NEW_SCORE_1];
		$CA_NEW_SCORE_2 = $data[CA_NEW_SCORE_2];
		$CA_NEW_SCORE_3 = $data[CA_NEW_SCORE_3];
		$CA_NEW_SCORE_4 = $data[CA_NEW_SCORE_4];
		$CA_NEW_SCORE_5 = $data[CA_NEW_SCORE_5];
		$CA_NEW_SCORE_6 = $data[CA_NEW_SCORE_6];
		$CA_NEW_SCORE_7 = $data[CA_NEW_SCORE_7];
		$CA_NEW_SCORE_8 = $data[CA_NEW_SCORE_8];
		$CA_NEW_SCORE_9 = $data[CA_NEW_SCORE_9];
		$CA_NEW_SCORE_10 = $data[CA_NEW_SCORE_10];
		$CA_NEW_SCORE_11 = $data[CA_NEW_SCORE_11];
		$CA_NEW_MEAN = $data[CA_NEW_MEAN];
		$CA_REMARK = trim($data[CA_REMARK]);

		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);

		if (!$CA_MINISTRY_NAME) {
			$cmd = " select ORG_ID_REF from PER_ORG where ORG_ID=$CA_DEPARTMENT_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$CA_MINISTRY_ID = $data[ORG_ID_REF];
			
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$CA_MINISTRY_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$CA_MINISTRY_NAME = $data[ORG_NAME];
		} 

		if ($PER_ID) {
			$cmd = " select 	PER_CARDNO, PN_CODE, PER_NAME, PER_SURNAME  
					from 	PER_PERSONAL 
					where 	PER_ID=$PER_ID  ";
			$db_dpis2->send_cmd($cmd);
			//echo $cmd;
		//	$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$CA_CARDNO = $data2[PER_CARDNO];
			$PN_CODE = trim($data2[PN_CODE]);
			$CA_NAME = $data2[PER_NAME];
			$CA_SURNAME = $data2[PER_SURNAME];
		}
	} // end if
	
	if( (!$UPD && !$VIEW && !$DEL && !$err_text) ){
		$CA_COURSE = "";
		$CA_CODE = "";
		$CA_TYPE = 1;
		$PER_ID = "";
		$CA_TEST_DATE = "";
		$CA_APPROVE_DATE = "";
		$PN_CODE = "";		
		$CA_NAME = "";
		$CA_SURNAME = "";
		$CA_CARDNO = "";
		$CA_CONSISTENCY = "";
		$CA_SCORE_1 = "";
		$CA_SCORE_2 = "";
		$CA_SCORE_3 = "";
		$CA_SCORE_4 = "";
		$CA_SCORE_5 = "";
		$CA_SCORE_6 = "";
		$CA_SCORE_7 = "";
		$CA_SCORE_8 = "";
		$CA_SCORE_9 = "";
		$CA_SCORE_10 = "";		
		$CA_SCORE_11 = "";
		$CA_SCORE_12 = "";
		$CA_MEAN = "";
//		if (!$CA_MINISTRY_ID) $CA_MINISTRY_NAME = "";
		$CA_MINISTRY_NAME = "";
		$CA_DEPARTMENT_NAME = "";
		$CA_ORG_NAME = "";
		$CA_ORG_NAME1 = "";
		$CA_ORG_NAME2 = "";
		$CA_LINE = "";
		$LEVEL_NO = "";
		$CA_MGT = "";
		$CA_POSITION = "";
		$CA_POS_NO = "";
		$CA_DOC_DATE = "";
		$CA_DOC_NO = "";
		$CA_NEW_SCORE_1 = "";
		$CA_NEW_SCORE_2 = "";
		$CA_NEW_SCORE_3 = "";
		$CA_NEW_SCORE_4 = "";
		$CA_NEW_SCORE_5 = "";
		$CA_NEW_SCORE_6 = "";
		$CA_NEW_SCORE_7 = "";
		$CA_NEW_SCORE_8 = "";
		$CA_NEW_SCORE_9 = "";
		$CA_NEW_SCORE_10 = "";
		$CA_NEW_SCORE_11 = "";
		$CA_NEW_MEAN = "";
		$CA_REMARK = "";
	} // end if
?>