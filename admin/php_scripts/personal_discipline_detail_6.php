<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");	

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 50;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$UPDATE_DATE = date("Y-m-d H:i:s");

	if ($command=="ADD" || $command=="UPDATE") {
		if (!$PEN_CODE) $PEN_CODE = "NULL";
	}

	if($command=="ADD" && trim($DCL_PER_ID)){			//$DCL_ID = link PER_DISCIPLINE
		$cmd = " 	insert into PER_DISCIPLINE_PER(DCL_ID,PER_ID,	PER_CARDNO,DCL_PL_NAME,DCL_PM_NAME,LEVEL_NO,DCL_POS_NO,DCL_ORG1,DCL_ORG2,DCL_ORG3,DCL_ORG4,DCL_ORG5,PEN_CODE,UPDATE_USER,UPDATE_DATE)
							values ($DCL_ID, $DCL_PER_ID ,'$DCL_PER_CARDNO', '$DCL_PL_NAME', '$DCL_PM_NAME', '$LEVEL_NO','$POS_NO','$MINISTRY_NAME', '$DEPARTMENT_NAME', '$ORG_NAME', '$ORG_NAME_1', '$ORG_NAME_2', '$PEN_CODE', $SESS_USERID, '$UPDATE_DATE')   ";
		$db_dpis->send_cmd($cmd);
//echo "ADD - > ".$cmd;				
//$db_dpis->show_error();
	
		$UPD = "";	$VIEW = "";
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มผู้ถูกกล่าวหาทางวินัย [$DCL_ID : $DCL_PER_ID: $DCL_PER_CARDNO]");
	} // end if

	if($command=="UPDATE" && (trim($DCL_ID) && trim($DCL_PER_ID) && trim($DCL_TMP_PER_ID))){
		$cmd = " UPDATE PER_DISCIPLINE_PER SET
				PER_ID = $DCL_PER_ID,
				PER_CARDNO ='$DCL_PER_CARDNO',
				DCL_PL_NAME = '$DCL_PL_NAME',
				DCL_PM_NAME = '$DCL_PM_NAME',
				LEVEL_NO = '$LEVEL_NO',
				DCL_POS_NO = '$POS_NO',
				DCL_ORG1 = '$MINISTRY_NAME',
				DCL_ORG2 = '$DEPARTMENT_NAME',
				DCL_ORG3 = '$ORG_NAME',
				DCL_ORG4 ='$ORG_NAME_1',
				DCL_ORG5 = '$ORG_NAME_2',
				PEN_CODE ='$PEN_CODE', 
				UPDATE_USER =$SESS_USERID, 
				UPDATE_DATE ='$UPDATE_DATE'
			WHERE (DCL_ID=$DCL_ID AND PER_ID = $DCL_TMP_PER_ID) ";
		$db_dpis->send_cmd($cmd);
//$db_dpis->show_error();
//echo "UPD - > ".$cmd;				

		$UPD = "";	$VIEW = "";
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขผู้ถูกกล่าวหาทางวินัย [$DCL_ID : ($DCL_TMP_PER_ID -> $DCL_PER_ID): $DCL_PER_CARDNO]");
	} // end if
	
	if($command=="DELETE" && (trim($DCL_ID) && trim($DCL_TMP_PER_ID))){
		$cmd = " delete from PER_DISCIPLINE_PER where (DCL_ID=$DCL_ID and PER_ID=$DCL_TMP_PER_ID) ";
		$db_dpis->send_cmd($cmd);

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบผู้ถูกกล่าวหาทางวินัย [$DCL_ID : $DCL_TMP_PER_ID]");
	} // end if

	if(($UPD || $VIEW) && (trim($DCL_ID) && trim($DCL_TMP_PER_ID))){
		$cmd = "	SELECT 		DCL_ID,PER_ID,PER_CARDNO,DCL_PL_NAME,DCL_PM_NAME,LEVEL_NO,DCL_POS_NO,DCL_ORG1,DCL_ORG2,DCL_ORG3,DCL_ORG4,DCL_ORG5,PEN_CODE
							FROM		PER_DISCIPLINE_PER
							WHERE	(DCL_ID=$DCL_ID AND PER_ID=$DCL_TMP_PER_ID) ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		//echo "-> $cmd";	
		
//$DCL_ID = $data[DCL_ID];
		$DCL_PER_ID = $data[PER_ID];
		$DCL_TMP_PER_ID = $DCL_PER_ID;
		$DCL_PER_CARDNO = $data[PER_CARDNO];
		$DCL_PL_NAME= $data[DCL_PL_NAME];
		$DCL_PM_NAME= $data[DCL_PM_NAME];
		$LEVEL_NO = $data[LEVEL_NO];
		
		$POS_NO = $data[DCL_POS_NO];			//$POS_NO_NAME = $data[DCL_POS_NO_NAME];		//BKK
		$MINISTRY_NAME = $data[DCL_ORG1];
		$DEPARTMENT_NAME = $data[DCL_ORG2];
		$ORG_NAME = $data[DCL_ORG3];
		$ORG_NAME_1 = $data[DCL_ORG4];
		$ORG_NAME_2 = $data[DCL_ORG5];
		
		$PEN_CODE	 = $data[PEN_CODE];

		$cmd = " select PN_CODE, PER_NAME, PER_SURNAME, PER_SALARY from PER_PERSONAL where PER_ID=$DCL_PER_ID ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$DCL_PER_NAME = trim($data2[PER_NAME]) ." ". trim($data2[PER_SURNAME]);
		$DCL_PER_SALARY  = $data2[PER_SALARY];

		$cmd = " select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$LEVEL_NO' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$LEVEL_NAME = $data2[LEVEL_NAME];
		
		$cmd = " select PEN_NAME from PER_PENALTY where PEN_CODE='$PEN_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$PEN_NAME = $data2[PEN_NAME];
	} // end if
	
	if( !$UPD && !$VIEW){
		unset($DCL_TMP_PER_ID);  
		unset($DCL_PER_ID);  
		unset($DCL_PER_NAME);
		unset($DCL_PER_CARDNO);
		unset($DCL_PL_CODE);  unset($DCL_PL_NAME);
		unset($DCL_PM_CODE);  unset($DCL_PM_NAME);
		unset($LEVEL_NO);  unset($LEVEL_NAME);  
		unset($DCL_PER_SALARY);  
		unset($POS_NO);  		
		unset($POS_NO_NAME);  
		unset($ORG_ID);  		
		unset($ORG_NAME);  
		unset($ORG_ID_1);  		
		unset($ORG_NAME_1);  
		unset($ORG_ID_2);  		
		unset($ORG_NAME_2);  
		unset($PEN_CODE);  		
		unset($PEN_NAME);  
	} // end if 
?>