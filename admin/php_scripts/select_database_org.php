<?
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$UPDATE_DATE = date("Y-m-d H:i:s");

	switch($CTRL_TYPE){
		case 2 :
			$search_pv_code = $PROVINCE_CODE;
			$search_pv_name = $PROVINCE_NAME;
			break;
		case 3 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			break;
		case 4 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			break;
	} // end switch case

	switch($SESS_USERGROUP_LEVEL){
		case 2 :
			$search_pv_code = $PROVINCE_CODE;
			$search_pv_name = $PROVINCE_NAME;
			break;
		case 3 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			break;
		case 4 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			break;
		case 5 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			$search_org_id = $ORG_ID;
			$search_org_name = $ORG_NAME;
			break;
	} // end switch case

	$where_per_id = " (POS_ID IN (SELECT POS_ID FROM PER_POSITION WHERE DEPARTMENT_ID = $search_department_id AND ORG_ID = $search_org_id) OR
										  POEM_ID IN (SELECT POEM_ID FROM PER_POS_EMP WHERE DEPARTMENT_ID = $search_department_id AND ORG_ID = $search_org_id) OR
										  POEMS_ID IN (SELECT POEMS_ID FROM PER_POS_EMPSER WHERE DEPARTMENT_ID = $search_department_id AND ORG_ID = $search_org_id)) ";

	if( $command=='POSITION' ) { // ตำแหน่ง
		$table = array("per_positionhis", "per_salaryhis", "per_extrahis", "per_educate", "per_training", "per_ability", "per_special_skill", "per_heir", 
									"per_absenthis", "per_punishment", "per_servicehis", "per_rewardhis", "per_marrhis", "per_namehis", "per_decoratehis", 
									"per_timehis", "per_extra_incomehis", "per_personalpic", "per_comdtl", "per_salpromote", "per_coursedtl", "per_decordtl", 
									"per_promote_p", "per_move_req", "per_absent", "per_personal", "per_pos_emp", "per_pos_empser", "per_pos_move", 
									"per_salquotadtl2", "per_salquotadtl1", "per_salquota", "per_bonusquotadtl2", "per_bonusquotadtl1", "per_bonusquota", 
									"per_command" );
		for ( $i=0; $i<count($table); $i++ ) { 
			$cmd = " delete from $table[$i] ";
			$db_dpis1->send_cmd($cmd);
			//$db_dpis1->show_error();
		} // end for

		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_POSITION' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_POSITION WHERE DEPARTMENT_ID = $search_department_id ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

		$cmd = " select max(POS_ID) as MAX_ID from PER_POSITION ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_ID = $data[MAX_ID] + 1;

		$cmd = " SELECT POS_ID, ORG_ID, POS_NO, OT_CODE, ORG_ID_1, ORG_ID_2, PM_CODE, PL_CODE, CL_NAME,
						POS_SALARY, POS_MGTSALARY, SKILL_CODE, PT_CODE, PC_CODE, POS_CONDITION, POS_DOC_NO, 	POS_REMARK, 
						POS_DATE, POS_GET_DATE, POS_CHANGE_DATE, POS_STATUS, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID 
						FROM PER_POSITION WHERE POS_ID IN (SELECT POS_ID FROM PER_PERSONAL 
						WHERE DEPARTMENT_ID = $search_department_id) AND ORG_ID = $search_org_id ORDER BY POS_ID ";
		$db_dpis35->send_cmd($cmd);
		$db_dpis35->show_error();
		echo "$cmd<br>";
		while($data = $db_dpis35->get_array()){
			$PER_POSITION++;
			$POS_ID = $data[POS_ID];
			$ORG_ID = $data[ORG_ID];
			$POS_NO = trim($data[POS_NO]);
			$OT_CODE = trim($data[OT_CODE]);
			$ORG_ID_1 = $data[ORG_ID_1];
			$ORG_ID_2 = $data[ORG_ID_2];
			$PM_CODE = trim($data[PM_CODE]);
			$PL_CODE = trim($data[PL_CODE]);
			$CL_NAME = trim($data[CL_NAME]);
			$POS_SALARY = $data[POS_SALARY];
			$POS_MGTSALARY = $data[POS_MGTSALARY];
			$SKILL_CODE = trim($data[SKILL_CODE]);
			$PT_CODE = trim($data[PT_CODE]);
			$PC_CODE = trim($data[PC_CODE]);
			$POS_CONDITION = trim($data[POS_CONDITION]);
			$POS_DOC_NO = trim($data[POS_DOC_NO]);
			$POS_REMARK = trim($data[POS_REMARK]);
			$POS_DATE = trim($data[POS_DATE]);
			$POS_GET_DATE = trim($data[POS_GET_DATE]);
			$POS_CHANGE_DATE = trim($data[POS_CHANGE_DATE]);
			$POS_STATUS = $data[POS_STATUS];
			$UPDATE_USER = $data[UPDATE_USER];
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			if (!$ORG_ID_1) $ORG_ID_1 = "NULL";
			if (!$ORG_ID_2) $ORG_ID_2 = "NULL";

			$cmd = " INSERT INTO PER_POSITION (POS_ID, ORG_ID, POS_NO, OT_CODE, ORG_ID_1, ORG_ID_2, 
							PM_CODE, PL_CODE, CL_NAME, POS_SALARY, POS_MGTSALARY, SKILL_CODE, PT_CODE, PC_CODE, 
							POS_CONDITION, POS_DOC_NO, POS_REMARK, POS_DATE, POS_GET_DATE, POS_CHANGE_DATE, POS_STATUS, 
							UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID)
							VALUES ($MAX_ID, $ORG_ID, '$POS_NO', '$OT_CODE', $ORG_ID_1, $ORG_ID_2, '$PM_CODE', 
							'$PL_CODE', '$CL_NAME', $POS_SALARY, $POS_MGTSALARY, '$SKILL_CODE', '$PT_CODE', '$PC_CODE', 
							'$POS_CONDITION', '$POS_DOC_NO', '$POS_REMARK', '$POS_DATE', '$POS_GET_DATE', 
							'$POS_CHANGE_DATE', $POS_STATUS, $UPDATE_USER, '$UPDATE_DATE', $search_department_id) ";
			$db_dpis->send_cmd($cmd);
			if ($MAX_ID < 4) {
			$db_dpis->show_error();
			echo "<br>";
			}
			$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
							VALUES ('PER_POSITION', '$POS_ID', '$MAX_ID', $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$MAX_ID++;
		} // end while						

		$cmd = " select count(POS_ID) as COUNT_NEW from PER_POSITION where DEPARTMENT_ID = $search_department_id AND ORG_ID = $search_org_id ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_POSITION - $PER_POSITION - $COUNT_NEW<br>";

// ลูกจ้างประจำ 
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_POS_EMP' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " SELECT POEM_ID, ORG_ID, POEM_NO, ORG_ID_1, ORG_ID_2, PN_CODE, POEM_MIN_SALARY, 
						POEM_MAX_SALARY, POEM_STATUS, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID 
						FROM PER_POS_EMP WHERE DEPARTMENT_ID = $search_department_id AND ORG_ID = $search_org_id ORDER BY POEM_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_POS_EMP++;
			$POEM_ID = $data[POEM_ID] + 0;
			$ORG_ID = $data[ORG_ID] + 0;
			$POEM_NO = trim($data[POEM_NO]);
			$ORG_ID_1 = $data[ORG_ID_1] + 0;
			$ORG_ID_2 = $data[ORG_ID_2] + 0;
			$PN_CODE = trim($data[PN_CODE]);
			$POEM_MIN_SALARY = $data[POEM_MIN_SALARY] + 0;
			$POEM_MAX_SALARY = $data[POEM_MAX_SALARY] + 0;
			$POEM_STATUS = $data[POEM_STATUS] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_POS_EMP (POEM_ID, ORG_ID, POEM_NO, ORG_ID_1, ORG_ID_2, PN_CODE, POEM_MIN_SALARY, 
							POEM_MAX_SALARY, POEM_STATUS, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID)
							VALUES ($MAX_ID, $ORG_ID, '$POEM_NO', $ORG_ID_1, $ORG_ID_2, '$PN_CODE', 
							$POEM_MIN_SALARY, $POEM_MAX_SALARY, $POEM_STATUS, $UPDATE_USER, '$UPDATE_DATE', $search_department_id) ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
//			echo "<br>";

			$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
							VALUES ('PER_POS_EMP', '$POEM_ID', '$MAX_ID', $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//db_dpis->show_error();

			$MAX_ID++;
		} // end while						

		$cmd = " select count(POEM_ID) as COUNT_NEW from PER_POS_EMP where DEPARTMENT_ID = $search_department_id ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_POS_EMP - $PER_POS_EMP - $COUNT_NEW<br>";

// พนักงานราชการ
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_POS_EMPSER' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " SELECT POEMS_ID, ORG_ID, POEMS_NO, ORG_ID_1, ORG_ID_2, EP_CODE, POEM_MIN_SALARY, 
						POEM_MAX_SALARY, POEM_STATUS, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID 
						FROM PER_POS_EMPSER WHERE DEPARTMENT_ID = $search_department_id AND ORG_ID = $search_org_id ORDER BY POEM_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_POS_EMPSER++;
			$POEMS_ID = $data[POEMS_ID] + 0;
			$ORG_ID = $data[ORG_ID] + 0;
			$POEMS_NO = trim($data[POEMS_NO]);
			$ORG_ID_1 = $data[ORG_ID_1] + 0;
			$ORG_ID_2 = $data[ORG_ID_2] + 0;
			$EP_CODE = trim($data[EP_CODE]);
			$POEM_MIN_SALARY = $data[POEM_MIN_SALARY] + 0;
			$POEM_MAX_SALARY = $data[POEM_MAX_SALARY] + 0;
			$POEM_STATUS = $data[POEM_STATUS] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_POS_EMPSER (POEMS_ID, ORG_ID, POEMS_NO, ORG_ID_1, ORG_ID_2, EP_CODE, POEM_MIN_SALARY, 
							POEM_MAX_SALARY, POEM_STATUS, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID)
							VALUES ($MAX_ID, $ORG_ID, '$POEMS_NO', $ORG_ID_1, $ORG_ID_2, '$EP_CODE', 
							$POEM_MIN_SALARY, $POEM_MAX_SALARY, $POEM_STATUS, $UPDATE_USER, '$UPDATE_DATE', $search_department_id) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
							VALUES ('PER_POS_EMPSER', '$POEMS_ID', '$MAX_ID', $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$MAX_ID++;
		} // end while						

		$cmd = " select count(POEMS_ID) as COUNT_NEW from PER_POS_EMPSER where DEPARTMENT_ID = $search_department_id AND ORG_ID = $search_org_id ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_POS_EMPSER - $PER_POS_EMPSER - $COUNT_NEW<br>";

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

	} // end if

	if( $command=='PERSONAL' ) { // ข้อมูลข้าราชการ
		$table = array("per_positionhis", "per_salaryhis", "per_extrahis", "per_educate", "per_training", "per_ability", "per_special_skill", "per_heir", 
									"per_absenthis", "per_punishment", "per_servicehis", "per_rewardhis", "per_marrhis", "per_namehis", "per_decoratehis", 
									"per_timehis", "per_extra_incomehis", "per_personalpic", "per_comdtl", "per_salpromote", "per_coursedtl", "per_decordtl", 
									"per_promote_p", "per_move_req", "per_absent", "per_personal" );
		for ( $i=0; $i<count($table); $i++ ) { 
			$cmd = " delete from $table[$i] ";
			$db_dpis1->send_cmd($cmd);
			$db_dpis1->show_error();
		} // end for

		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_PERSONAL' ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_PERSONAL WHERE DEPARTMENT_ID = $search_department_id ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

		$cmd = " select max(PER_ID) as MAX_ID from PER_PERSONAL ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_ID = $data[MAX_ID] + 1;

			$cmd = " SELECT PER_ID, PER_TYPE, OT_CODE, PN_CODE, PER_NAME, PER_SURNAME, PER_ENG_NAME,
							PER_ENG_SURNAME, ORG_ID, POS_ID, POEM_ID, LEVEL_NO, PER_ORGMGT, PER_SALARY, PER_MGTSALARY,
							PER_SPSALARY, PER_GENDER, MR_CODE, PER_CARDNO, PER_OFFNO, PER_TAXNO, PER_BLOOD, RE_CODE,
							PER_BIRTHDATE, PER_RETIREDATE, PER_STARTDATE, PER_OCCUPYDATE, PER_POSDATE, PER_SALDATE,
							PN_CODE_F, PER_FATHERNAME, PER_FATHERSURNAME, PN_CODE_M, PER_MOTHERNAME, PER_MOTHERSURNAME,
							PER_ADD1, PER_ADD2, PV_CODE, MOV_CODE, PER_ORDAIN, PER_SOLDIER, PER_MEMBER, PER_STATUS,
							UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID, APPROVE_PER_ID, REPLACE_PER_ID, ABSENT_FLAG, POEMS_ID,
							PER_HIP_FLAG, PER_CERT_OCC, LEVEL_NO_SALARY 
							FROM PER_PERSONAL 
							WHERE  $where_per_id 
							ORDER BY PER_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		//echo "$cmd<br>";
		while($data = $db_dpis35->get_array()){
			$PER_PERSONAL++;
			$PER_ID = $data[PER_ID] + 0;
			$PER_TYPE = $data[PER_TYPE] + 0;
			$OT_CODE = trim($data[OT_CODE]);
			$PN_CODE = trim($data[PN_CODE]);
			$PER_NAME = trim($data[PER_NAME]);
			$PER_SURNAME = trim($data[PER_SURNAME]);
			$PER_ENG_NAME = trim($data[PER_ENG_NAME]);
			$PER_ENG_SURNAME = trim($data[PER_ENG_SURNAME]);
			$ORG_ID = $data[ORG_ID];
			$POS_ID = $data[POS_ID];
			$POEM_ID = $data[POEM_ID];
			$LEVEL_NO = trim($data[LEVEL_NO]);
			$PER_ORGMGT = $data[PER_ORGMGT] + 0;
			$PER_SALARY = $data[PER_SALARY] + 0;
			$PER_MGTSALARY = $data[PER_MGTSALARY] + 0;
			$PER_SPSALARY = $data[PER_SPSALARY] + 0;
			$PER_GENDER = $data[PER_GENDER] + 0;
			$MR_CODE = trim($data[MR_CODE]);
			$PER_CARDNO = trim($data[PER_CARDNO]);
			$PER_OFFNO = trim($data[PER_OFFNO]);
			$PER_TAXNO = trim($data[PER_TAXNO]);
			$PER_BLOOD = trim($data[PER_BLOOD]);
			$RE_CODE = trim($data[RE_CODE]);
			$PER_BIRTHDATE = trim($data[PER_BIRTHDATE]);
			$PER_RETIREDATE = trim($data[PER_RETIREDATE]);
			$PER_STARTDATE = trim($data[PER_STARTDATE]);
			$PER_OCCUPYDATE = trim($data[PER_OCCUPYDATE]);
			$PER_POSDATE = trim($data[PER_POSDATE]);
			$PER_SALDATE = trim($data[PER_SALDATE]);
			$PN_CODE_F = trim($data[PN_CODE_F]);
			$PER_FATHERNAME = trim($data[PER_FATHERNAME]);
			$PER_FATHERSURNAME = trim($data[PER_FATHERSURNAME]);
			$PN_CODE_M = trim($data[PN_CODE_M]);
			$PER_MOTHERNAME = trim($data[PER_MOTHERNAME]);
			$PER_MOTHERSURNAME = trim($data[PER_MOTHERSURNAME]);
			$PER_ADD1 = trim($data[PER_ADD1]);
			$PER_ADD2 = trim($data[PER_ADD2]);
			$PV_CODE = trim($data[PV_CODE]);
			$MOV_CODE = trim($data[MOV_CODE]);
			$PER_ORDAIN = $data[PER_ORDAIN] + 0;
			$PER_SOLDIER = $data[PER_SOLDIER] + 0;
			$PER_MEMBER = $data[PER_MEMBER] + 0;
			$PER_STATUS = $data[PER_STATUS] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			$APPROVE_PER_ID = $data[APPROVE_PER_ID] + 0;
			$REPLACE_PER_ID = $data[REPLACE_PER_ID] + 0;
			$ABSENT_FLAG = $data[ABSENT_FLAG];
			$POEMS_ID = $data[POEMS_ID];
			$PER_HIP_FLAG = trim($data[PER_HIP_FLAG]);
			$PER_CERT_OCC = trim($data[PER_CERT_OCC]);
			$LEVEL_NO_SALARY = trim($data[LEVEL_NO_SALARY]);

			if ($PER_TYPE==1) {
				$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_POSITION' AND OLD_CODE = $POS_ID ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$POS_ID = $data2[NEW_CODE] + 0;
			} elseif ($PER_TYPE==2) {
				$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_POS_EMP' AND OLD_CODE = $POEM_ID ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$POEM_ID = $data2[NEW_CODE] + 0;
			} elseif ($PER_TYPE==3) {
				$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_POS_EMPSER' AND OLD_CODE = $POEMS_ID ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$POEMS_ID = $data2[NEW_CODE] + 0;
			}

			if (!$POS_ID) $POS_ID = "NULL";
			if (!$POEM_ID) $POEM_ID = "NULL";
			if (!$POEMS_ID) $POEMS_ID = "NULL";
			if (!$ORG_ID) $ORG_ID = "NULL";
			if (!$ABSENT_FLAG) $ABSENT_FLAG = "NULL";

			$cmd = " INSERT INTO PER_PERSONAL (PER_ID, PER_TYPE, OT_CODE, PN_CODE, PER_NAME, 
							PER_SURNAME, PER_ENG_NAME, PER_ENG_SURNAME, ORG_ID, POS_ID, POEM_ID, LEVEL_NO, PER_ORGMGT, 
							PER_SALARY, PER_MGTSALARY, PER_SPSALARY, PER_GENDER, MR_CODE, PER_CARDNO, PER_OFFNO, 
							PER_TAXNO, PER_BLOOD, RE_CODE, PER_BIRTHDATE, PER_RETIREDATE, PER_STARTDATE, 
							PER_OCCUPYDATE, PER_POSDATE, PER_SALDATE, PN_CODE_F, PER_FATHERNAME, PER_FATHERSURNAME, 
							PN_CODE_M, PER_MOTHERNAME, PER_MOTHERSURNAME, PER_ADD1, PER_ADD2, PV_CODE, MOV_CODE, 
							PER_ORDAIN, PER_SOLDIER, PER_MEMBER, PER_STATUS, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID, 
							APPROVE_PER_ID, REPLACE_PER_ID, ABSENT_FLAG, POEMS_ID, PER_HIP_FLAG, PER_CERT_OCC, 
							LEVEL_NO_SALARY)
							VALUES ($MAX_ID, $PER_TYPE, '$OT_CODE', '$PN_CODE', '$PER_NAME', '$PER_SURNAME', 
							'$PER_ENG_NAME', '$PER_ENG_SURNAME', $ORG_ID, $POS_ID, $POEM_ID, '$LEVEL_NO', $PER_ORGMGT, 
							$PER_SALARY, $PER_MGTSALARY, $PER_SPSALARY, $PER_GENDER, '$MR_CODE', '$PER_CARDNO', 
							'$PER_OFFNO', '$PER_TAXNO', '$PER_BLOOD', '$RE_CODE', '$PER_BIRTHDATE', '$PER_RETIREDATE', 
							'$PER_STARTDATE', '$PER_OCCUPYDATE', '$PER_POSDATE', '$PER_SALDATE', '$PN_CODE_F', 
							'$PER_FATHERNAME', '$PER_FATHERSURNAME', '$PN_CODE_M', '$PER_MOTHERNAME', 
							'$PER_MOTHERSURNAME', '$PER_ADD1', '$PER_ADD2', '$PV_CODE', '$MOV_CODE', $PER_ORDAIN, 
							$PER_SOLDIER, $PER_MEMBER, $PER_STATUS, $UPDATE_USER, '$UPDATE_DATE', $search_department_id, 
							$APPROVE_PER_ID, $REPLACE_PER_ID, $ABSENT_FLAG, $POEMS_ID, '$PER_HIP_FLAG', 
							'$PER_CERT_OCC', '$LEVEL_NO_SALARY') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
							VALUES ('PER_PERSONAL', '$PER_ID', '$MAX_ID', $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$MAX_ID++;
		} // end while				

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_PERSONAL where $where_per_id ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_PERSONAL - $PER_PERSONAL - $COUNT_NEW<br>";

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

	} // end if

	if( $command=='POSITIONHIS' ) { // ประวัติการดำรงตำแหน่ง
		$cmd = " delete from per_positionhis ";
		$db_dpis1->send_cmd($cmd);
//		$db_dpis1->show_error();

		$cmd = " select max(POH_ID) as MAX_ID from PER_POSITIONHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_ID = $data[MAX_ID] + 1;

		$cmd = " SELECT POH_ID, PER_ID, POH_EFFECTIVEDATE, MOV_CODE, POH_ENDDATE, POH_DOCNO, POH_DOCDATE, 
						POH_POS_NO, PM_CODE, LEVEL_NO, PL_CODE, PN_CODE, PT_CODE, CT_CODE, PV_CODE, AP_CODE, POH_ORGMGT, 
						ORG_ID_1, ORG_ID_2, ORG_ID_3, POH_UNDER_ORG1, POH_UNDER_ORG2, POH_ASS_ORG, POH_ASS_ORG1, 
						POH_ASS_ORG2, POH_SALARY, POH_SALARY_POS, POH_REMARK, UPDATE_USER, UPDATE_DATE,
						PER_CARDNO, EP_CODE, POH_ORG1, POH_ORG2, POH_ORG3 
						FROM PER_POSITIONHIS 
						WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL WHERE $where_per_id) ORDER BY POH_ID ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_POSITIONHIS++;
			$POH_ID = $data[POH_ID];
			$PER_ID = $data[PER_ID];
			$POH_EFFECTIVEDATE = trim($data[POH_EFFECTIVEDATE]);
			$MOV_CODE = trim($data[MOV_CODE]);
			$POH_ENDDATE = trim($data[POH_ENDDATE]);
			$POH_DOCNO = trim($data[POH_DOCNO]);
			$POH_DOCDATE = trim($data[POH_DOCDATE]);
			$POH_POS_NO = trim($data[POH_POS_NO]);
			$PM_CODE = trim($data[PM_CODE]);
			$LEVEL_NO = trim($data[LEVEL_NO]);
			$PL_CODE = trim($data[PL_CODE]);
			$PN_CODE = trim($data[PN_CODE]);
			$PT_CODE = trim($data[PT_CODE]);
			$CT_CODE = trim($data[CT_CODE]);
			$PV_CODE = trim($data[PV_CODE]);
			$AP_CODE = trim($data[AP_CODE]);
			$POH_ORGMGT = $data[POH_ORGMGT];
			$ORG_ID_1 = $data[ORG_ID_1];
			$ORG_ID_2 = $data[ORG_ID_2];
			$ORG_ID_3 = $data[ORG_ID_3];
			$POH_UNDER_ORG1 = trim($data[POH_UNDER_ORG1]);
			$POH_UNDER_ORG2 = trim($data[POH_UNDER_ORG2]);
			$POH_ASS_ORG = trim($data[POH_ASS_ORG]);
			$POH_ASS_ORG1 = trim($data[POH_ASS_ORG1]);
			$POH_ASS_ORG2 = trim($data[POH_ASS_ORG2]);
			$POH_SALARY = $data[POH_SALARY];
			$POH_SALARY_POS = $data[POH_SALARY_POS];
			$POH_REMARK = trim($data[POH_REMARK]);
			$UPDATE_USER = $data[UPDATE_USER];
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			$PER_CARDNO = trim($data[PER_CARDNO]);
			$EP_CODE = trim($data[EP_CODE]);
			$POH_ORG1 = trim($data[POH_ORG1]);
			$POH_ORG2 = trim($data[POH_ORG2]);
			$POH_ORG3 = trim($data[POH_ORG3]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = $PER_ID ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PER_ID = $data2[NEW_CODE] + 0;

			if (!$ORG_ID_1) $ORG_ID_1 = "NULL";
			if (!$ORG_ID_2) $ORG_ID_2 = "NULL";
			if (!$ORG_ID_3) $ORG_ID_3 = "NULL";
			if (!$ORG_ID) $ORG_ID = "NULL";

			$cmd = " INSERT INTO PER_POSITIONHIS (POH_ID, PER_ID, POH_EFFECTIVEDATE, MOV_CODE, POH_ENDDATE, POH_DOCNO, 
							POH_DOCDATE, POH_POS_NO, PM_CODE, LEVEL_NO, PL_CODE, PN_CODE, PT_CODE, CT_CODE, PV_CODE, AP_CODE, 
							POH_ORGMGT, ORG_ID_1, ORG_ID_2, ORG_ID_3, POH_UNDER_ORG1, POH_UNDER_ORG2, POH_ASS_ORG, 
							POH_ASS_ORG1, POH_ASS_ORG2, POH_SALARY, POH_SALARY_POS, POH_REMARK, 
							UPDATE_USER, UPDATE_DATE, PER_CARDNO, EP_CODE, POH_ORG1, POH_ORG2, POH_ORG3)
							VALUES ($MAX_ID, $PER_ID, '$POH_EFFECTIVEDATE', '$MOV_CODE', '$POH_ENDDATE', '$POH_DOCNO', 
							'$POH_DOCDATE', '$POH_POS_NO', '$PM_CODE', '$LEVEL_NO', '$PL_CODE', '$PN_CODE', '$PT_CODE', '$CT_CODE', 
							'$PV_CODE', '$AP_CODE', $POH_ORGMGT, $ORG_ID_1, $ORG_ID_2, $ORG_ID_3, '$POH_UNDER_ORG1', 
							'$POH_UNDER_ORG2', '$POH_ASS_ORG', '$POH_ASS_ORG1', '$POH_ASS_ORG2', $POH_SALARY, 
							$POH_SALARY_POS, '$POH_REMARK', $UPDATE_USER, '$UPDATE_DATE', '$PER_CARDNO', '$EP_CODE', '$POH_ORG1', 
							'$POH_ORG2', '$POH_ORG3') ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();

			$MAX_ID++;
		} // end while						
		
		$cmd = " select count(PER_ID) as COUNT_NEW from PER_POSITIONHIS where PER_ID in (select PER_ID from PER_PERSONAL where $where_per_id) ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_POSITIONHIS - $PER_POSITIONHIS - $COUNT_NEW<br>";

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

	} // end if

	if( $command=='SALARYHIS' ) { // ประวัติการรับเงินเดือน
		$cmd = " delete from per_salaryhis ";
		$db_dpis1->send_cmd($cmd);
		$db_dpis1->show_error();

		$cmd = " select max(SAH_ID) as MAX_ID from PER_SALARYHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_ID = $data[MAX_ID] + 1;

		$cmd = " SELECT SAH_ID, PER_ID, SAH_EFFECTIVEDATE, MOV_CODE, SAH_SALARY, SAH_DOCNO, SAH_DOCDATE, 
						SAH_ENDDATE, UPDATE_USER,	UPDATE_DATE, PER_CARDNO FROM PER_SALARYHIS 
						WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL WHERE $where_per_id) 
						ORDER BY SAH_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_SALARYHIS++;
			$SAH_ID = $data[SAH_ID] + 0;
			$PER_ID = $data[PER_ID] + 0;
			$SAH_EFFECTIVEDATE = trim($data[SAH_EFFECTIVEDATE]);
			$MOV_CODE = trim($data[MOV_CODE]);
			$SAH_SALARY = $data[SAH_SALARY] + 0;
			$SAH_DOCNO = trim($data[SAH_DOCNO]);
			$SAH_DOCDATE = trim($data[SAH_DOCDATE]);
			$SAH_ENDDATE = trim($data[SAH_ENDDATE]);
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			$PER_CARDNO = trim($data[PER_CARDNO]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = $PER_ID ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PER_ID = $data2[NEW_CODE] + 0;

			$cmd = " INSERT INTO PER_SALARYHIS (SAH_ID, PER_ID, SAH_EFFECTIVEDATE, MOV_CODE, SAH_SALARY, SAH_DOCNO, 
							SAH_DOCDATE, SAH_ENDDATE, UPDATE_USER, UPDATE_DATE, PER_CARDNO)
							VALUES ($MAX_ID, $PER_ID, '$SAH_EFFECTIVEDATE', '$MOV_CODE', $SAH_SALARY, '$SAH_DOCNO', '$SAH_DOCDATE', 
							'$SAH_ENDDATE', 	$UPDATE_USER, '$UPDATE_DATE', '$PER_CARDNO') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$MAX_ID++;
		} // end while						
		
		$cmd = " select count(PER_ID) as COUNT_NEW from PER_SALARYHIS where PER_ID in (select PER_ID from PER_PERSONAL where $where_per_id) ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_SALARYHIS - $PER_SALARYHIS - $COUNT_NEW<br>";

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

	} // end if

	if( $command=='HISTORY' ) { // ประวัติอื่น ๆ
		$table = array("per_extrahis", "per_educate", "per_training", "per_ability", "per_special_skill", "per_heir", 
									"per_absenthis", "per_punishment", "per_servicehis", "per_rewardhis", "per_marrhis", "per_namehis", "per_decoratehis", 
									"per_timehis", "per_extra_incomehis" );
		for ( $i=0; $i<count($table); $i++ ) { 
			$cmd = " delete from $table[$i] ";
			$db_dpis1->send_cmd($cmd);
			$db_dpis1->show_error();
		} // end for

// ประวัติการรับเงินเพิ่มพิเศษ  
		$cmd = " select max(EXH_ID) as MAX_ID from PER_EXTRAHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_ID = $data[MAX_ID] + 1;

		$cmd = " SELECT EXH_ID, PER_ID, EXH_EFFECTIVEDATE, EX_CODE, EXH_AMT, EXH_ENDDATE, UPDATE_USER, UPDATE_DATE,	
						PER_CARDNO FROM PER_EXTRAHIS 
						WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL WHERE $where_per_id) 
						ORDER BY EXH_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_EXTRAHIS++;
			$EXH_ID = $data[EXH_ID] + 0;
			$PER_ID = $data[PER_ID] + 0;
			$EXH_EFFECTIVEDATE = trim($data[EXH_EFFECTIVEDATE]);
			$EX_CODE = trim($data[EX_CODE]);
			$EXH_AMT = $data[EXH_AMT] + 0;
			$EXH_ENDDATE = trim($data[EXH_ENDDATE]);
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			$PER_CARDNO = trim($data[PER_CARDNO]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = $PER_ID ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PER_ID = $data2[NEW_CODE] + 0;

			$cmd = " INSERT INTO PER_EXTRAHIS (EXH_ID, PER_ID, EXH_EFFECTIVEDATE, EX_CODE, EXH_AMT, EXH_ENDDATE, 
							UPDATE_USER, UPDATE_DATE, PER_CARDNO)
							VALUES ($MAX_ID, $PER_ID, '$EXH_EFFECTIVEDATE', '$EX_CODE', $EXH_AMT, '$EXH_ENDDATE', $UPDATE_USER, 
							'$UPDATE_DATE', '$PER_CARDNO') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$MAX_ID++;
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_EXTRAHIS where PER_ID in (select PER_ID from PER_PERSONAL where $where_per_id) ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_EXTRAHIS - $PER_EXTRAHIS - $COUNT_NEW<br>";

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

// ประวัติการศึกษา  		
		$cmd = " select max(EDU_ID) as MAX_ID from PER_EDUCATE ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_ID = $data[MAX_ID] + 1;

		$cmd = " SELECT EDU_ID, PER_ID, EDU_SEQ, EDU_STARTYEAR, EDU_ENDYEAR, ST_CODE, CT_CODE, EDU_FUND, EN_CODE, 
						EM_CODE, INS_CODE,	EDU_TYPE, UPDATE_USER, UPDATE_DATE, PER_CARDNO FROM PER_EDUCATE 
						WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL WHERE $where_per_id) 
						ORDER BY EDU_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_EDUCATE++;
			$EDU_ID = $data[EDU_ID] + 0;
			$PER_ID = $data[PER_ID] + 0;
			$EDU_SEQ = trim($data[EDU_SEQ]);
			$EDU_STARTYEAR = trim($data[EDU_STARTYEAR]);
			$EDU_ENDYEAR = trim($data[EDU_ENDYEAR]);
			$ST_CODE = trim($data[ST_CODE]);
			$CT_CODE = trim($data[CT_CODE]);
			$EDU_FUND = trim($data[EDU_FUND]);
			$EN_CODE = trim($data[EN_CODE]);
			$EM_CODE = trim($data[EM_CODE]);
			$INS_CODE = trim($data[INS_CODE]);
			$EDU_TYPE = trim($data[EDU_TYPE]);
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			$PER_CARDNO = trim($data[PER_CARDNO]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = $PER_ID ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PER_ID = $data2[NEW_CODE] + 0;

			$cmd = " INSERT INTO PER_EDUCATE (EDU_ID, PER_ID, EDU_SEQ, EDU_STARTYEAR, EDU_ENDYEAR, ST_CODE, CT_CODE, 
							EDU_FUND, EN_CODE, EM_CODE, INS_CODE, EDU_TYPE, UPDATE_USER, UPDATE_DATE, PER_CARDNO)
							VALUES ($MAX_ID, $PER_ID, '$EDU_SEQ', '$EDU_STARTYEAR', '$EDU_ENDYEAR', '$ST_CODE', '$CT_CODE', 
							'$EDU_FUND', '$EN_CODE', '$EM_CODE', '$INS_CODE', '$EDU_TYPE', $UPDATE_USER, '$UPDATE_DATE', 
							'$PER_CARDNO') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$MAX_ID++;
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_EDUCATE where PER_ID in (select PER_ID from PER_PERSONAL where $where_per_id) ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_EDUCATE - $PER_EDUCATE - $COUNT_NEW<br>";

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

// ประวัติการอบรม/ดูงาน/สัมมนา  
		$cmd = " select max(TRN_ID) as MAX_ID from PER_TRAINING ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_ID = $data[MAX_ID] + 1;

		$cmd = " SELECT TRN_ID, PER_ID, TRN_TYPE, TR_CODE, TRN_NO, TRN_STARTDATE, TRN_ENDDATE, TRN_ORG, TRN_PLACE, 
						CT_CODE, TRN_FUND,	CT_CODE_FUND, UPDATE_USER, UPDATE_DATE, PER_CARDNO FROM PER_TRAINING 
						WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL WHERE $where_per_id) 
						ORDER BY TRN_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_TRAINING++;
			$TRN_ID = $data[TRN_ID] + 0;
			$PER_ID = $data[PER_ID] + 0;
			$TRN_TYPE = $data[TRN_TYPE] + 0;
			$TR_CODE = trim($data[TR_CODE]);
			$TRN_NO = trim($data[TRN_NO]);
			$TRN_STARTDATE = trim($data[TRN_STARTDATE]);
			$TRN_ENDDATE = trim($data[TRN_ENDDATE]);
			$TRN_ORG = trim($data[TRN_ORG]);
			$TRN_PLACE = trim($data[TRN_PLACE]);
			$CT_CODE = trim($data[CT_CODE]);
			$TRN_FUND = trim($data[TRN_FUND]);
			$CT_CODE_FUND = trim($data[CT_CODE_FUND]);
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			$PER_CARDNO = trim($data[PER_CARDNO]);
			if (!$TRN_ENDDATE) $TRN_ENDDATE = $TRN_STARTDATE;

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = $PER_ID ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PER_ID = $data2[NEW_CODE] + 0;

			$cmd = " INSERT INTO PER_TRAINING (TRN_ID, PER_ID, TRN_TYPE, TR_CODE, TRN_NO, TRN_STARTDATE, TRN_ENDDATE, 
							TRN_ORG, TRN_PLACE, CT_CODE, TRN_FUND,	CT_CODE_FUND, UPDATE_USER, UPDATE_DATE, PER_CARDNO)
							VALUES ($MAX_ID, $PER_ID, $TRN_TYPE, '$TR_CODE', '$TRN_NO', '$TRN_STARTDATE', '$TRN_ENDDATE', 
							'$TRN_ORG', '$TRN_PLACE', '$CT_CODE', '$TRN_FUND', '$CT_CODE_FUND', $UPDATE_USER, '$UPDATE_DATE', 
							'$PER_CARDNO') ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();

			$MAX_ID++;
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_TRAINING where PER_ID in (select PER_ID from PER_PERSONAL where $where_per_id) ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_TRAINING - $PER_TRAINING - $COUNT_NEW<br>";

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

// ความสามารถพิเศษ  
		$cmd = " select max(ABI_ID) as MAX_ID from PER_ABILITY ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_ID = $data[MAX_ID] + 1;

		$cmd = " SELECT ABI_ID, PER_ID, AL_CODE, ABI_DESC, UPDATE_USER, UPDATE_DATE, PER_CARDNO FROM PER_ABILITY 
						WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL WHERE $where_per_id) 
						ORDER BY ABI_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_ABILITY++;
			$ABI_ID = $data[ABI_ID] + 0;
			$PER_ID = $data[PER_ID] + 0;
			$AL_CODE = trim($data[AL_CODE]);
			$ABI_DESC = trim($data[ABI_DESC]);
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			$PER_CARDNO = trim($data[PER_CARDNO]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = $PER_ID ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PER_ID = $data2[NEW_CODE] + 0;

			$cmd = " INSERT INTO PER_ABILITY (ABI_ID, PER_ID, AL_CODE, ABI_DESC, UPDATE_USER, UPDATE_DATE, PER_CARDNO)
							VALUES ($MAX_ID, $PER_ID, '$AL_CODE', '$ABI_DESC', $UPDATE_USER, '$UPDATE_DATE', '$PER_CARDNO') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$MAX_ID++;
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_ABILITY where PER_ID in (select PER_ID from PER_PERSONAL where $where_per_id) ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_ABILITY - $PER_ABILITY - $COUNT_NEW<br>";

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

// ความเชี่ยวชาญพิเศษ  
		$cmd = " select max(SPS_ID) as MAX_ID from PER_SPECIAL_SKILL ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_ID = $data[MAX_ID] + 1;

		$cmd = " SELECT SPS_ID, PER_ID, SS_CODE, SPS_EMPHASIZE, UPDATE_USER, UPDATE_DATE, PER_CARDNO 
						FROM PER_SPECIAL_SKILL 
						WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL WHERE $where_per_id) 
						ORDER BY SPS_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_SPECIAL_SKILL++;
			$SPS_ID = $data[SPS_ID] + 0;
			$PER_ID = $data[PER_ID] + 0;
			$SS_CODE = trim($data[SS_CODE]);
			$SPS_EMPHASIZE = trim($data[SPS_EMPHASIZE]);
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			$PER_CARDNO = trim($data[PER_CARDNO]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = $PER_ID ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PER_ID = $data2[NEW_CODE] + 0;

			$cmd = " INSERT INTO PER_SPECIAL_SKILL (SPS_ID, PER_ID, SS_CODE, SPS_EMPHASIZE, UPDATE_USER, UPDATE_DATE, 
							PER_CARDNO)
							VALUES ($MAX_ID, $PER_ID, '$SS_CODE', '$SPS_EMPHASIZE', $UPDATE_USER, '$UPDATE_DATE', '$PER_CARDNO') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$MAX_ID++;
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_SPECIAL_SKILL where PER_ID in (select PER_ID from PER_PERSONAL where $where_per_id) ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_SPECIAL_SKILL - $PER_SPECIAL_SKILL - $COUNT_NEW<br>";

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

// ทายาท  
		$cmd = " select max(HEIR_ID) as MAX_ID from PER_HEIR ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_ID = $data[MAX_ID] + 1;

		$cmd = " SELECT HEIR_ID, PER_ID, HR_CODE, HEIR_NAME, HEIR_STATUS, HEIR_BIRTHDAY, HEIR_TAX, UPDATE_USER, 
						UPDATE_DATE, PER_CARDNO FROM PER_HEIR 
						WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL WHERE $where_per_id) 
						ORDER BY HEIR_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_HEIR++;
			$HEIR_ID = $data[HEIR_ID] + 0;
			$PER_ID = $data[PER_ID] + 0;
			$HR_CODE = trim($data[HR_CODE]);
			$HEIR_NAME = trim($data[HEIR_NAME]);
			$HEIR_STATUS = $data[HEIR_STATUS] + 0;
			$HEIR_BIRTHDAY = trim($data[HEIR_BIRTHDAY]);
			$HEIR_TAX = trim($data[HEIR_TAX]);
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			$PER_CARDNO = trim($data[PER_CARDNO]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = $PER_ID ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PER_ID = $data2[NEW_CODE] + 0;

			$cmd = " INSERT INTO PER_HEIR (HEIR_ID, PER_ID, HR_CODE, HEIR_NAME, HEIR_STATUS, HEIR_BIRTHDAY, HEIR_TAX, 
							UPDATE_USER, UPDATE_DATE, 	PER_CARDNO)
							VALUES ($MAX_ID, $PER_ID, '$HR_CODE', '$HEIR_NAME', $HEIR_STATUS, '$HEIR_BIRTHDAY', '$HEIR_TAX', 
							$UPDATE_USER, '$UPDATE_DATE', '$PER_CARDNO') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$MAX_ID++;
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_HEIR where PER_ID in (select PER_ID from PER_PERSONAL where $where_per_id) ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_HEIR - $PER_HEIR - $COUNT_NEW<br>";

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

// ประวัติการลา  		
		$cmd = " select max(ABS_ID) as MAX_ID from PER_ABSENTHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_ID = $data[MAX_ID] + 1;

		$cmd = " SELECT ABS_ID, PER_ID, AB_CODE, ABS_STARTDATE, ABS_STARTPERIOD, ABS_ENDDATE, ABS_ENDPERIOD, ABS_DAY, 
						UPDATE_USER,	UPDATE_DATE, PER_CARDNO FROM PER_ABSENTHIS 
						WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL WHERE $where_per_id) 
						ORDER BY ABS_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_ABSENTHIS++;
			$ABS_ID = $data[ABS_ID] + 0;
			$PER_ID = $data[PER_ID] + 0;
			$AB_CODE = trim($data[AB_CODE]);
			$ABS_STARTDATE = trim($data[ABS_STARTDATE]);
			$ABS_STARTPERIOD = $data[ABS_STARTPERIOD] + 0;
			$ABS_ENDDATE = trim($data[ABS_ENDDATE]);
			$ABS_ENDPERIOD = $data[ABS_ENDPERIOD] + 0;
			$ABS_DAY = $data[ABS_DAY] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			$PER_CARDNO = trim($data[PER_CARDNO]);
			if (!$ABS_STARTPERIOD) $ABS_STARTPERIOD = 3;
			if (!$ABS_ENDPERIOD) $ABS_ENDPERIOD = 3;

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = $PER_ID ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PER_ID = $data2[NEW_CODE] + 0;

			$cmd = " INSERT INTO PER_ABSENTHIS (ABS_ID, PER_ID, AB_CODE, ABS_STARTDATE, ABS_STARTPERIOD, ABS_ENDDATE, 
							ABS_ENDPERIOD, ABS_DAY, UPDATE_USER, UPDATE_DATE, PER_CARDNO)
							VALUES ($MAX_ID, $PER_ID, '$AB_CODE', '$ABS_STARTDATE', $ABS_STARTPERIOD, '$ABS_ENDDATE', 
							$ABS_ENDPERIOD, $ABS_DAY, $UPDATE_USER, '$UPDATE_DATE', '$PER_CARDNO') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$MAX_ID++;
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_ABSENTHIS where PER_ID in (select PER_ID from PER_PERSONAL where $where_per_id) ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_ABSENTHIS - $PER_ABSENTHIS - $COUNT_NEW<br>";

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

// ประวัติทางวินัย  
		$cmd = " select max(PUN_ID) as MAX_ID from PER_PUNISHMENT ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_ID = $data[MAX_ID] + 1;

		$cmd = " SELECT PUN_ID, PER_ID, INV_NO, PUN_NO, PUN_REF_NO, PUN_TYPE, PUN_STARTDATE, PUN_ENDDATE, CRD_CODE, 
						PEN_CODE, PUN_PAY, PUN_SALARY, UPDATE_USER, UPDATE_DATE, PER_CARDNO FROM PER_PUNISHMENT 
						WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL WHERE $where_per_id) 
						ORDER BY PUN_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_PUNISHMENT++;
			$PUN_ID = $data[PUN_ID] + 0;
			$PER_ID = $data[PER_ID] + 0;
			$INV_NO = trim($data[INV_NO]);
			$PUN_NO = trim($data[PUN_NO]);
			$PUN_REF_NO = trim($data[PUN_REF_NO]);
			$PUN_TYPE = $data[PUN_TYPE] + 0;
			$PUN_STARTDATE = trim($data[PUN_STARTDATE]);
			$PUN_ENDDATE = trim($data[PUN_ENDDATE]);
			$CRD_CODE = trim($data[CRD_CODE]);
			$PEN_CODE = trim($data[PEN_CODE]);
			$PUN_PAY = $data[PUN_PAY] + 0;
			$PUN_SALARY = $data[PUN_SALARY] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			$PER_CARDNO = trim($data[PER_CARDNO]);
			if (!$PUN_STARTDATE) $PUN_STARTDATE = "NULL";
			if (!$PUN_ENDDATE) $PUN_ENDDATE = "NULL";

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = $PER_ID ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PER_ID = $data2[NEW_CODE] + 0;

			$cmd = " INSERT INTO PER_PUNISHMENT (PUN_ID, PER_ID, INV_NO, PUN_NO, PUN_REF_NO, PUN_TYPE, PUN_STARTDATE, 
							PUN_ENDDATE, CRD_CODE, PEN_CODE, PUN_PAY, PUN_SALARY, UPDATE_USER, UPDATE_DATE, PER_CARDNO)
							VALUES ($MAX_ID, $PER_ID, '$INV_NO', '$PUN_NO', '$PUN_REF_NO', $PUN_TYPE, '$PUN_STARTDATE', '$PUN_ENDDATE', 
							'$CRD_CODE', '$PEN_CODE', 	$PUN_PAY, $PUN_SALARY, $UPDATE_USER, '$UPDATE_DATE', '$PER_CARDNO') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$MAX_ID++;
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_PUNISHMENT where PER_ID in (select PER_ID from PER_PERSONAL where $where_per_id) ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_PUNISHMENT - $PER_PUNISHMENT - $COUNT_NEW<br>";

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

// ประวัติราชการพิเศษ  
		$cmd = " select max(SRH_ID) as MAX_ID from PER_SERVICEHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_ID = $data[MAX_ID] + 1;

		$cmd = " SELECT SRH_ID, PER_ID, SV_CODE, SRT_CODE, ORG_ID, SRH_STARTDATE, SRH_ENDDATE, SRH_NOTE, SRH_DOCNO, 
						PER_ID_ASSIGN, ORG_ID_ASSIGN, UPDATE_USER, UPDATE_DATE, PER_CARDNO FROM PER_SERVICEHIS 
						WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL WHERE $where_per_id) 
						ORDER BY SRH_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_SERVICEHIS++;
			$SRH_ID = $data[SRH_ID] + 0;
			$PER_ID = $data[PER_ID] + 0;
			$SV_CODE = trim($data[SV_CODE]);
			$SRT_CODE = trim($data[SRT_CODE]);
			$ORG_ID = $data[ORG_ID] + 0;
			$SRH_STARTDATE = trim($data[SRH_STARTDATE]);
			$SRH_ENDDATE = trim($data[SRH_ENDDATE]);
			$SRH_NOTE = trim($data[SRH_NOTE]);
			$SRH_DOCNO = trim($data[SRH_DOCNO]);
			$PER_ID_ASSIGN = $data[PER_ID_ASSIGN] + 0;
			$ORG_ID_ASSIGN = $data[ORG_ID_ASSIGN] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			$PER_CARDNO = trim($data[PER_CARDNO]);
			if (!$SRH_STARTDATE) $SRH_STARTDATE = "-";
			if (!$SRH_ENDDATE) $SRH_ENDDATE = $SRH_STARTDATE;

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = $PER_ID ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PER_ID = $data2[NEW_CODE] + 0;

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = $PER_ID_ASSIGN ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PER_ID_ASSIGN = $data2[NEW_CODE] + 0;
			if ($PER_ID_ASSIGN==0) $PER_ID_ASSIGN = "NULL";

			$cmd = " INSERT INTO PER_SERVICEHIS (SRH_ID, PER_ID, SV_CODE, SRT_CODE, ORG_ID, SRH_STARTDATE, SRH_ENDDATE, 
							SRH_NOTE, SRH_DOCNO, PER_ID_ASSIGN, ORG_ID_ASSIGN, UPDATE_USER, UPDATE_DATE, PER_CARDNO)
							VALUES ($MAX_ID, $PER_ID, '$SV_CODE', '$SRT_CODE', $ORG_ID, '$SRH_STARTDATE', '$SRH_ENDDATE', 
							'$SRH_NOTE', '$SRH_DOCNO', 	$PER_ID_ASSIGN, $ORG_ID_ASSIGN, $UPDATE_USER, '$UPDATE_DATE', 
							'$PER_CARDNO') ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();

			$MAX_ID++;
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_SERVICEHIS where PER_ID in (select PER_ID from PER_PERSONAL where $where_per_id) ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_SERVICEHIS - $PER_SERVICEHIS - $COUNT_NEW<br>";

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

// ประวัติความดีความชอบ  
		$cmd = " select max(REH_ID) as MAX_ID from PER_REWARDHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_ID = $data[MAX_ID] + 1;

		$cmd = " SELECT REH_ID, PER_ID, REW_CODE, REH_ORG, REH_DOCNO, REH_DATE, UPDATE_USER, UPDATE_DATE, 
						PER_CARDNO FROM PER_REWARDHIS 
						WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL WHERE $where_per_id) 
						ORDER BY REH_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_REWARDHIS++;
			$REH_ID = $data[REH_ID] + 0;
			$PER_ID = $data[PER_ID] + 0;
			$REW_CODE = trim($data[REW_CODE]);
			$REH_ORG = trim($data[REH_ORG]);
			$REH_DOCNO = trim($data[REH_DOCNO]);
			$REH_DATE = trim($data[REH_DATE]);
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			$PER_CARDNO = trim($data[PER_CARDNO]);
//			if (!$SRH_STARTDATE) $SRH_STARTDATE = "NULL";
//			if (!$SRH_ENDDATE) $SRH_ENDDATE = "NULL";

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = $PER_ID ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PER_ID = $data2[NEW_CODE] + 0;

			$cmd = " INSERT INTO PER_REWARDHIS (REH_ID, PER_ID, REW_CODE, REH_ORG, REH_DOCNO, REH_DATE, UPDATE_USER, 
							UPDATE_DATE, PER_CARDNO)
							VALUES ($MAX_ID, $PER_ID, '$REW_CODE', '$REH_ORG', '$REH_DOCNO', '$REH_DATE', $UPDATE_USER, 
							'$UPDATE_DATE', '$PER_CARDNO') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$MAX_ID++;
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_REWARDHIS 	where PER_ID in (select PER_ID from PER_PERSONAL where $where_per_id) ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_REWARDHIS - $PER_REWARDHIS - $COUNT_NEW<br>";

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

// ประวัติการสมรส   
		$cmd = " select max(MAH_ID) as MAX_ID from PER_MARRHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_ID = $data[MAX_ID] + 1;

		$cmd = " SELECT MAH_ID, PER_ID, MAH_SEQ, MAH_NAME, MAH_MARRY_DATE, MAH_DIVORCE_DATE, DV_CODE, UPDATE_USER, 
						UPDATE_DATE, PER_CARDNO FROM PER_MARRHIS 
						WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL WHERE $where_per_id) 
						ORDER BY MAH_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_MARRHIS++;
			$MAH_ID = $data[MAH_ID] + 0;
			$PER_ID = $data[PER_ID] + 0;
			$MAH_SEQ = $data[MAH_SEQ] + 0;
			$MAH_NAME = trim($data[MAH_NAME]);
			$MAH_MARRY_DATE = trim($data[MAH_MARRY_DATE]);
			$MAH_DIVORCE_DATE = trim($data[MAH_DIVORCE_DATE]);
			$DV_CODE = trim($data[DV_CODE]);
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			$PER_CARDNO = trim($data[PER_CARDNO]);
//			if (!$MAH_NAME) $MAH_NAME = "NULL";
//			if (!$MAH_MARRY_DATE) $MAH_MARRY_DATE = "NULL";

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = $PER_ID ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PER_ID = $data2[NEW_CODE] + 0;

			$cmd = " INSERT INTO PER_MARRHIS (MAH_ID, PER_ID, MAH_SEQ, MAH_NAME, MAH_MARRY_DATE, MAH_DIVORCE_DATE, 
							DV_CODE, UPDATE_USER, UPDATE_DATE, PER_CARDNO)
							VALUES ($MAX_ID, $PER_ID, $MAH_SEQ, '$MAH_NAME', '$MAH_MARRY_DATE', '$MAH_DIVORCE_DATE', '$DV_CODE', 
							$UPDATE_USER, '$UPDATE_DATE', '$PER_CARDNO') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$MAX_ID++;
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_MARRHIS where PER_ID in (select PER_ID from PER_PERSONAL where $where_per_id) ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_MARRHIS - $PER_MARRHIS - $COUNT_NEW<br>";

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

// ประวัติการเปลี่ยนชื่อ-สกุล  
		$cmd = " select max(NH_ID) as MAX_ID from PER_NAMEHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_ID = $data[MAX_ID] + 1;

		$cmd = " SELECT NH_ID, PER_ID, NH_DATE, PN_CODE, NH_NAME, NH_SURNAME, NH_DOCNO, UPDATE_USER, UPDATE_DATE, 
						PER_CARDNO FROM PER_NAMEHIS 
						WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL WHERE $where_per_id) 
						ORDER BY NH_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_NAMEHIS++;
			$NH_ID = $data[NH_ID] + 0;
			$PER_ID = $data[PER_ID] + 0;
			$NH_DATE = trim($data[NH_DATE]);
			$PN_CODE = trim($data[PN_CODE]);
			$NH_NAME = trim($data[NH_NAME]);
			$NH_SURNAME = trim($data[NH_SURNAME]);
			$NH_DOCNO = trim($data[NH_DOCNO]);
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			$PER_CARDNO = trim($data[PER_CARDNO]);
//			if (!$SRH_STARTDATE) $SRH_STARTDATE = "NULL";
//			if (!$SRH_ENDDATE) $SRH_ENDDATE = "NULL";

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = $PER_ID ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PER_ID = $data2[NEW_CODE] + 0;

			$cmd = " INSERT INTO PER_NAMEHIS (NH_ID, PER_ID, NH_DATE, PN_CODE, NH_NAME, NH_SURNAME, NH_DOCNO, 
							UPDATE_USER, UPDATE_DATE, PER_CARDNO)
							VALUES ($MAX_ID, $PER_ID, '$NH_DATE', '$PN_CODE', '$NH_NAME', '$NH_SURNAME', '$NH_DOCNO', $UPDATE_USER, 
							'$UPDATE_DATE', '$PER_CARDNO') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$MAX_ID++;
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_NAMEHIS where PER_ID in (select PER_ID from PER_PERSONAL where $where_per_id) ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_NAMEHIS - $PER_NAMEHIS - $COUNT_NEW<br>";

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

// ประวัติรับพระราชทานเครื่องราชฯ  
		$cmd = " select max(DEH_ID) as MAX_ID from PER_DECORATEHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_ID = $data[MAX_ID] + 1;

		$cmd = " SELECT DEH_ID, PER_ID, DC_CODE, DEH_DATE, DEH_GAZETTE, UPDATE_USER, UPDATE_DATE, PER_CARDNO 
						FROM PER_DECORATEHIS 
						WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL WHERE $where_per_id) 
						ORDER BY DEH_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_DECORATEHIS++;
			$DEH_ID = $data[DEH_ID] + 0;
			$PER_ID = $data[PER_ID] + 0;
			$DC_CODE = trim($data[DC_CODE]);
			$DEH_DATE = trim($data[DEH_DATE]);
			$DEH_GAZETTE = trim($data[DEH_GAZETTE]);
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			$PER_CARDNO = trim($data[PER_CARDNO]);
//			if (!$SRH_STARTDATE) $SRH_STARTDATE = "NULL";
//			if (!$SRH_ENDDATE) $SRH_ENDDATE = "NULL";

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = $PER_ID ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PER_ID = $data2[NEW_CODE] + 0;

			$cmd = " INSERT INTO PER_DECORATEHIS (DEH_ID, PER_ID, DC_CODE, DEH_DATE, DEH_GAZETTE, UPDATE_USER, 
							UPDATE_DATE, PER_CARDNO)
							VALUES ($MAX_ID, $PER_ID, '$DC_CODE', '$DEH_DATE', '$DEH_GAZETTE', $UPDATE_USER, '$UPDATE_DATE', 
							'$PER_CARDNO') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$MAX_ID++;
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_DECORATEHIS where PER_ID in (select PER_ID from PER_PERSONAL where $where_per_id) ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_DECORATEHIS - $PER_DECORATEHIS - $COUNT_NEW<br>";

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

// เวลาทวีคูณ  
		$cmd = " select max(TIMEH_ID) as MAX_ID from PER_TIMEHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_ID = $data[MAX_ID] + 1;

		$cmd = " SELECT TIMEH_ID, PER_ID, TIME_CODE, TIMEH_MINUS, TIMEH_REMARK, UPDATE_USER, UPDATE_DATE, PER_CARDNO 
						FROM PER_TIMEHIS 
						WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL WHERE $where_per_id) 
						ORDER BY TIMEH_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_TIMEHIS++;
			$TIMEH_ID = $data[TIMEH_ID] + 0;
			$PER_ID = $data[PER_ID] + 0;
			$TIME_CODE = trim($data[TIME_CODE]);
			$TIMEH_MINUS = $data[TIMEH_MINUS] + 0;
			$TIMEH_REMARK = trim($data[TIMEH_REMARK]);
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			$PER_CARDNO = trim($data[PER_CARDNO]);
//			if (!$SRH_STARTDATE) $SRH_STARTDATE = "NULL";
//			if (!$SRH_ENDDATE) $SRH_ENDDATE = "NULL";

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = $PER_ID ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PER_ID = $data2[NEW_CODE] + 0;

			$cmd = " INSERT INTO PER_TIMEHIS (TIMEH_ID, PER_ID, TIME_CODE, TIMEH_MINUS, TIMEH_REMARK, UPDATE_USER, 
							UPDATE_DATE, PER_CARDNO)
							VALUES ($MAX_ID, $PER_ID, '$TIME_CODE', $TIMEH_MINUS, '$TIMEH_REMARK', $UPDATE_USER, '$UPDATE_DATE', 
							'$PER_CARDNO') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$MAX_ID++;
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_TIMEHIS where PER_ID in (select PER_ID from PER_PERSONAL where $where_per_id) ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_TIMEHIS - $PER_TIMEHIS - $COUNT_NEW<br>";

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

// ประวัติการรับเงินพิเศษ  		
		$cmd = " select max(EXINH_ID) as MAX_ID from PER_EXTRA_INCOMEHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_ID = $data[MAX_ID] + 1;

		$cmd = " SELECT EXINH_ID, PER_ID, EXINH_EFFECTIVEDATE, EXIN_CODE, EXINH_AMT, EXINH_ENDDATE, UPDATE_USER, 
						UPDATE_DATE, PER_CARDNO FROM PER_EXTRA_INCOMEHIS 
						WHERE PER_ID IN (SELECT PER_ID FROM PER_PERSONAL WHERE $where_per_id) 
						ORDER BY EXINH_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_EXTRA_INCOMEHIS++;
			$EXINH_ID = $data[EXINH_ID] + 0;
			$PER_ID = $data[PER_ID] + 0;
			$EXINH_EFFECTIVEDATE = trim($data[EXINH_EFFECTIVEDATE]);
			$EXIN_CODE = trim($data[EXIN_CODE]);
			$EXINH_AMT = $data[EXINH_AMT] + 0;
			$EXINH_ENDDATE = trim($data[EXINH_ENDDATE]);
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			$PER_CARDNO = trim($data[PER_CARDNO]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = $PER_ID ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PER_ID = $data2[NEW_CODE] + 0;

			$cmd = " INSERT INTO PER_EXTRA_INCOMEHIS (EXINH_ID, PER_ID, EXINH_EFFECTIVEDATE, EXIN_CODE, EXINH_AMT, 
							EXINH_ENDDATE, UPDATE_USER, UPDATE_DATE, PER_CARDNO)
							VALUES ($MAX_ID, $PER_ID, '$EXINH_EFFECTIVEDATE', '$EXIN_CODE', $EXINH_AMT, '$EXINH_ENDDATE', 
							$UPDATE_USER, '$UPDATE_DATE',	'$PER_CARDNO') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$MAX_ID++;
		} // end while						 

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_EXTRA_INCOMEHIS where PER_ID in (select PER_ID from PER_PERSONAL where $where_per_id) ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_EXTRA_INCOMEHIS - $PER_EXTRA_INCOMEHIS - $COUNT_NEW<br>";

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();
	} // end if

?>