<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$CREATE_DATE = "NOW()";
	$UPDATE_DATE = date("Y-m-d H:i:s");

	if( $command=='CONVERT' ) {
		$cmd = " DELETE FROM PER_ORG ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " SELECT ORG_ID, ORG_CODE, ORG_NAME, ORG_SHORT, OL_CODE, OT_CODE, OP_CODE, OS_CODE, 
						ORG_ADDR1, ORG_ADDR2, ORG_ADDR3, AP_CODE, PV_CODE, CT_CODE, ORG_DATE, ORG_JOB, ORG_ID_REF, 
						ORG_ACTIVE, UPDATE_USER, UPDATE_DATE, ORG_WEBSITE, ORG_SEQ_NO FROM PER_ORG ORDER BY ORG_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$ORG_ID = $data[ORG_ID] + 0;
			$ORG_CODE = trim($data[ORG_CODE]);
			$ORG_NAME = trim($data[ORG_NAME]);
			$ORG_SHORT = trim($data[ORG_SHORT]);
			$OL_CODE = trim($data[OL_CODE]);
			$OT_CODE = trim($data[OT_CODE]);
			$OP_CODE = trim($data[OP_CODE]);
			$OS_CODE = trim($data[OS_CODE]);
			$ORG_ADDR1 = trim($data[ORG_ADDR1]);
			$ORG_ADDR2 = trim($data[ORG_ADDR2]);
			$ORG_ADDR3 = trim($data[ORG_ADDR3]);
			$AP_CODE = trim($data[AP_CODE]);
			$PV_CODE = trim($data[PV_CODE]);
			$CT_CODE = trim($data[CT_CODE]);
			$ORG_DATE = trim($data[ORG_DATE]);
			$ORG_JOB = trim($data[ORG_JOB]);
			$ORG_ID_REF = $data[ORG_ID_REF] + 0;
			$ORG_ACTIVE = $data[ORG_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			$ORG_WEBSITE = trim($data[ORG_WEBSITE]);
			$ORG_SEQ_NO = $data[ORG_SEQ_NO] + 0;
			if (!$ORG_SEQ_NO) $ORG_SEQ_NO = "NULL";

			$cmd = " INSERT INTO PER_ORG (ORG_ID, ORG_CODE, ORG_NAME, ORG_SHORT, OL_CODE, OT_CODE, 
							OP_CODE, OS_CODE, ORG_ADDR1, ORG_ADDR2, ORG_ADDR3, AP_CODE, PV_CODE, CT_CODE, ORG_DATE, 
							ORG_JOB, ORG_ID_REF, ORG_ACTIVE, UPDATE_USER, UPDATE_DATE, ORG_WEBSITE, ORG_SEQ_NO)
							VALUES ($ORG_ID, '$ORG_CODE', '$ORG_NAME', '$ORG_SHORT', '$OL_CODE', '$OT_CODE', 
							'$OP_CODE', '$OS_CODE', '$ORG_ADDR1', '$ORG_ADDR2', '$ORG_ADDR3', '$AP_CODE', '$PV_CODE', 
							'$CT_CODE', '$ORG_DATE', '$ORG_JOB', '$ORG_ID_REF', $ORG_ACTIVE, $UPDATE_USER, 
							'$UPDATE_DATE', '$ORG_WEBSITE', $ORG_SEQ_NO) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " DELETE FROM PER_ORG_ASS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " SELECT ORG_ID, ORG_CODE, ORG_NAME, ORG_SHORT, OL_CODE, OT_CODE, OP_CODE, OS_CODE, 
						ORG_ADDR1, ORG_ADDR2, ORG_ADDR3, AP_CODE, PV_CODE, CT_CODE, ORG_DATE, ORG_JOB, ORG_ID_REF, 
						ORG_ACTIVE, UPDATE_USER, UPDATE_DATE, ORG_WEBSITE, ORG_SEQ_NO FROM PER_ORG_ASS ORDER BY ORG_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$ORG_ID = $data[ORG_ID] + 0;
			$ORG_CODE = trim($data[ORG_CODE]);
			$ORG_NAME = trim($data[ORG_NAME]);
			$ORG_SHORT = trim($data[ORG_SHORT]);
			$OL_CODE = trim($data[OL_CODE]);
			$OT_CODE = trim($data[OT_CODE]);
			$OP_CODE = trim($data[OP_CODE]);
			$OS_CODE = trim($data[OS_CODE]);
			$ORG_ADDR1 = trim($data[ORG_ADDR1]);
			$ORG_ADDR2 = trim($data[ORG_ADDR2]);
			$ORG_ADDR3 = trim($data[ORG_ADDR3]);
			$AP_CODE = trim($data[AP_CODE]);
			$PV_CODE = trim($data[PV_CODE]);
			$CT_CODE = trim($data[CT_CODE]);
			$ORG_DATE = trim($data[ORG_DATE]);
			$ORG_JOB = trim($data[ORG_JOB]);
			$ORG_ID_REF = $data[ORG_ID_REF] + 0;
			$ORG_ACTIVE = $data[ORG_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			$ORG_WEBSITE = trim($data[ORG_WEBSITE]);
			$ORG_SEQ_NO = $data[ORG_SEQ_NO] + 0;
			if (!$ORG_SEQ_NO) $ORG_SEQ_NO = "NULL";

			$cmd = " INSERT INTO PER_ORG_ASS (ORG_ID, ORG_CODE, ORG_NAME, ORG_SHORT, OL_CODE, OT_CODE, 
							OP_CODE, OS_CODE, ORG_ADDR1, ORG_ADDR2, ORG_ADDR3, AP_CODE, PV_CODE, CT_CODE, ORG_DATE, 
							ORG_JOB, ORG_ID_REF, ORG_ACTIVE, UPDATE_USER, UPDATE_DATE, ORG_WEBSITE, ORG_SEQ_NO)
							VALUES ($ORG_ID, '$ORG_CODE', '$ORG_NAME', '$ORG_SHORT', '$OL_CODE', '$OT_CODE', 
							'$OP_CODE', '$OS_CODE', '$ORG_ADDR1', '$ORG_ADDR2', '$ORG_ADDR3', '$AP_CODE', '$PV_CODE', 
							'$CT_CODE', '$ORG_DATE', '$ORG_JOB', '$ORG_ID_REF', $ORG_ACTIVE, $UPDATE_USER, 
							'$UPDATE_DATE', '$ORG_WEBSITE', $ORG_SEQ_NO) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " DELETE FROM PER_POSITION ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " SELECT POS_ID, ORG_ID, POS_NO, OT_CODE, ORG_ID_1, ORG_ID_2, PM_CODE, PL_CODE, CL_NAME,
						POS_SALARY, POS_MGTSALARY, SKILL_CODE, PT_CODE, PC_CODE, POS_CONDITION, POS_DOC_NO, POS_REMARK, 
						POS_DATE, POS_GET_DATE, POS_CHANGE_DATE, POS_STATUS, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID 
						FROM PER_POSITION ORDER BY POS_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$POS_ID = $data[POS_ID] + 0;
			$ORG_ID = $data[ORG_ID] + 0;
			$POS_NO = trim($data[POS_NO]);
			$OT_CODE = trim($data[OT_CODE]);
			$ORG_ID_1 = $data[ORG_ID_1] + 0;
			$ORG_ID_2 = $data[ORG_ID_2] + 0;
			$PM_CODE = trim($data[PM_CODE]);
			$PL_CODE = trim($data[PL_CODE]);
			$CL_NAME = trim($data[CL_NAME]);
			$POS_SALARY = $data[POS_SALARY] + 0;
			$POS_MGTSALARY = $data[POS_MGTSALARY] + 0;
			$SKILL_CODE = trim($data[SKILL_CODE]);
			$PT_CODE = trim($data[PT_CODE]);
			$PC_CODE = trim($data[PC_CODE]);
			$POS_CONDITION = trim($data[POS_CONDITION]);
			$POS_DOC_NO = trim($data[POS_DOC_NO]);
			$POS_REMARK = trim($data[POS_REMARK]);
			$POS_DATE = trim($data[POS_DATE]);
			$POS_GET_DATE = trim($data[POS_GET_DATE]);
			$POS_CHANGE_DATE = trim($data[POS_CHANGE_DATE]);
			$POS_STATUS = $data[POS_STATUS] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			$DEPARTMENT_ID = $data[DEPARTMENT_ID] + 0;
			if (!$ORG_ID_1) $ORG_ID_1 = "NULL";
			if (!$ORG_ID_2) $ORG_ID_2 = "NULL";
			if (!$POS_SALARY) $POS_SALARY = "NULL";
			if (!$POS_MGTSALARY) $POS_MGTSALARY = "NULL";
			if (!$DEPARTMENT_ID) $DEPARTMENT_ID = "NULL";

			$cmd = " INSERT INTO PER_POSITION (POS_ID, ORG_ID, POS_NO, OT_CODE, ORG_ID_1, ORG_ID_2, 
							PM_CODE, PL_CODE, CL_NAME, POS_SALARY, POS_MGTSALARY, SKILL_CODE, PT_CODE, PC_CODE, 
							POS_CONDITION, POS_DOC_NO, POS_REMARK, POS_DATE, POS_GET_DATE, POS_CHANGE_DATE, POS_STATUS, 
							UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID)
							VALUES ($POS_ID, $ORG_ID, '$POS_NO', '$OT_CODE', $ORG_ID_1, $ORG_ID_2, '$PM_CODE', 
							'$PL_CODE', '$CL_NAME', $POS_SALARY, $POS_MGTSALARY, '$SKILL_CODE', '$PT_CODE', '$PC_CODE', 
							'$POS_CONDITION', '$POS_DOC_NO', '$POS_REMARK', '$POS_DATE', '$POS_GET_DATE', 
							'$POS_CHANGE_DATE', $POS_STATUS, $UPDATE_USER, '$UPDATE_DATE', $DEPARTMENT_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " DELETE FROM PER_POS_NAME ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;
        
		$cmd = " SELECT PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_POS_NAME ORDER BY PN_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PN_CODE = trim($data[PN_CODE]);
			$PN_NAME = trim($data[PN_NAME]);
			$PG_CODE = trim($data[PG_CODE]);
			$PN_DECOR = $data[PN_DECOR] + 0;
			$PN_ACTIVE = $data[PN_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE)
							VALUES ('$PN_CODE', '$PN_NAME', '$PG_CODE', $PN_DECOR, $PN_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " DELETE FROM PER_POS_EMP ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;
        
		$cmd = " SELECT POEM_ID, ORG_ID, POEM_NO, ORG_ID_1, ORG_ID_2, PN_CODE, POEM_MIN_SALARY, POEM_MAX_SALARY, 
						POEM_STATUS, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID 
						FROM PER_POS_EMP ORDER BY POEM_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
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
			$DEPARTMENT_ID = $data[DEPARTMENT_ID] + 0;
			if (!$ORG_ID_1) $ORG_ID_1 = "NULL";
			if (!$ORG_ID_2) $ORG_ID_2 = "NULL";
			if (!$POEM_MIN_SALARY) $POEM_MIN_SALARY = "NULL";
			if (!$POEM_MAX_SALARY) $POEM_MAX_SALARY = "NULL";
			if (!$DEPARTMENT_ID) $DEPARTMENT_ID = "NULL";

			$cmd = " INSERT INTO PER_POS_EMP (POEM_ID, ORG_ID, POEM_NO, ORG_ID_1, ORG_ID_2, PN_CODE, POEM_MIN_SALARY, 
							POEM_MAX_SALARY, POEM_STATUS, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID)
							VALUES ($POEM_ID, $ORG_ID, '$POEM_NO', $ORG_ID_1, $ORG_ID_2, '$PN_CODE', $POEM_MIN_SALARY, 
							$POEM_MAX_SALARY, $POEM_STATUS, $UPDATE_USER, '$UPDATE_DATE', $DEPARTMENT_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " DELETE FROM PER_EMPSER_POS_NAME ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;
        
		$cmd = " SELECT EP_CODE, EP_NAME, LEVEL_NO, EP_DECOR, EP_ACTIVE, UPDATE_USER, UPDATE_DATE 
						FROM PER_EMPSER_POS_NAME ORDER BY EP_CODE ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$EP_CODE = trim($data[EP_CODE]);
			$EP_NAME = trim($data[EP_NAME]);
			$LEVEL_NO = trim($data[LEVEL_NO]);
			$EP_DECOR = $data[EP_DECOR] + 0;
			$EP_ACTIVE = $data[EP_ACTIVE] + 0;
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " INSERT INTO PER_EMPSER_POS_NAME (EP_CODE, EP_NAME, LEVEL_NO, EP_DECOR, EP_ACTIVE, UPDATE_USER, 
							UPDATE_DATE)
							VALUES ('$EP_CODE', '$EP_NAME', '$LEVEL_NO', $EP_DECOR, $EP_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " DELETE FROM PER_POS_EMPSER ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;
        
		$cmd = " SELECT POEMS_ID, ORG_ID, POEMS_NO, ORG_ID_1, ORG_ID_2, EP_CODE, POEM_MIN_SALARY, POEM_MAX_SALARY, 
						POEM_STATUS, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID 
						FROM PER_POS_EMPSER ORDER BY POEMS_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
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
			$DEPARTMENT_ID = $data[DEPARTMENT_ID] + 0;
			if (!$ORG_ID_1) $ORG_ID_1 = "NULL";
			if (!$ORG_ID_2) $ORG_ID_2 = "NULL";
			if (!$POEM_MIN_SALARY) $POEM_MIN_SALARY = "NULL";
			if (!$POEM_MAX_SALARY) $POEM_MAX_SALARY = "NULL";
			if (!$DEPARTMENT_ID) $DEPARTMENT_ID = "NULL";

			$cmd = " INSERT INTO PER_POS_EMPSER (POEMS_ID, ORG_ID, POEMS_NO, ORG_ID_1, ORG_ID_2, EP_CODE, 
							POEM_MIN_SALARY, POEM_MAX_SALARY, POEM_STATUS, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID)
							VALUES ($POEMS_ID, $ORG_ID, '$POEMS_NO', $ORG_ID_1, $ORG_ID_2, '$EP_CODE', $POEM_MIN_SALARY, 
							$POEM_MAX_SALARY, $POEM_STATUS, $UPDATE_USER, '$UPDATE_DATE', $DEPARTMENT_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						

		$cmd = " DELETE FROM PER_PERSONAL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " SELECT PER_ID, PER_TYPE, OT_CODE, PN_CODE, PER_NAME, PER_SURNAME, PER_ENG_NAME,
						PER_ENG_SURNAME, ORG_ID, POS_ID, POEM_ID, LEVEL_NO, PER_ORGMGT, PER_SALARY, PER_MGTSALARY,
						PER_SPSALARY, PER_GENDER, MR_CODE, PER_CARDNO, PER_OFFNO, PER_TAXNO, PER_BLOOD, RE_CODE,
						PER_BIRTHDATE, PER_RETIREDATE, PER_STARTDATE, PER_OCCUPYDATE, PER_POSDATE, PER_SALDATE,
						PN_CODE_F, PER_FATHERNAME, PER_FATHERSURNAME, PN_CODE_M, PER_MOTHERNAME, PER_MOTHERSURNAME,
						PER_ADD1, PER_ADD2, PV_CODE, MOV_CODE, PER_ORDAIN, PER_SOLDIER, PER_MEMBER, PER_STATUS,
						UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID, APPROVE_PER_ID, REPLACE_PER_ID, ABSENT_FLAG, POEMS_ID,
						PER_HIP_FLAG, PER_CERT_OCC, LEVEL_NO_SALARY FROM PER_PERSONAL ORDER BY PER_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_ID = $data[PER_ID] + 0;
			$PER_TYPE = $data[PER_TYPE] + 0;
			$OT_CODE = trim($data[OT_CODE]);
			$PN_CODE = trim($data[PN_CODE]);
			$PER_NAME = trim($data[PER_NAME]);
			$PER_SURNAME = trim($data[PER_SURNAME]);
			$PER_ENG_NAME = trim($data[PER_ENG_NAME]);
			$PER_ENG_SURNAME = trim($data[PER_ENG_SURNAME]);
			$ORG_ID = $data[ORG_ID] + 0;
			$POS_ID = $data[POS_ID] + 0;
			$POEM_ID = $data[POEM_ID] + 0;
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
			$DEPARTMENT_ID = $data[DEPARTMENT_ID] + 0;
			$APPROVE_PER_ID = $data[APPROVE_PER_ID] + 0;
			$REPLACE_PER_ID = $data[REPLACE_PER_ID] + 0;
			$ABSENT_FLAG = $data[ABSENT_FLAG] + 0;
			$POEMS_ID = $data[POEMS_ID] + 0;
			$PER_HIP_FLAG = trim($data[PER_HIP_FLAG]);
			$PER_CERT_OCC = trim($data[PER_CERT_OCC]);
			$LEVEL_NO_SALARY = trim($data[LEVEL_NO_SALARY]);
			if (!$ORG_ID) $ORG_ID = "NULL";
			if (!$POS_ID) $POS_ID = "NULL";
			if (!$POEM_ID) $POEM_ID = "NULL";
			if (!$PER_ORGMGT) $PER_ORGMGT = "NULL";
			if (!$PER_SALARY) $PER_SALARY = "NULL";
			if (!$PER_MGTSALARY) $PER_MGTSALARY = "NULL";
			if (!$PER_SPSALARY) $PER_SPSALARY = "NULL";
			if (!$PER_GENDER) $PER_GENDER = "NULL";
			if (!$PER_ORDAIN) $PER_ORDAIN = "NULL";
			if (!$PER_SOLDIER) $PER_SOLDIER = "NULL";
			if (!$PER_MEMBER) $PER_MEMBER = "NULL";
			if (!$DEPARTMENT_ID) $DEPARTMENT_ID = "NULL";
			if (!$APPROVE_PER_ID) $APPROVE_PER_ID = "NULL";
			if (!$REPLACE_PER_ID) $REPLACE_PER_ID = "NULL";
			if (!$ABSENT_FLAG) $ABSENT_FLAG = "NULL";
			if (!$POEMS_ID) $POEMS_ID = "NULL";

			$cmd = " INSERT INTO PER_PERSONAL (PER_ID, PER_TYPE, OT_CODE, PN_CODE, PER_NAME, PER_SURNAME, PER_ENG_NAME, 
							PER_ENG_SURNAME, ORG_ID, POS_ID, POEM_ID, LEVEL_NO, PER_ORGMGT, 
							PER_SALARY, PER_MGTSALARY, PER_SPSALARY, PER_GENDER, MR_CODE, PER_CARDNO, PER_OFFNO, 
							PER_TAXNO, PER_BLOOD, RE_CODE, PER_BIRTHDATE, PER_RETIREDATE, PER_STARTDATE, 
							PER_OCCUPYDATE, PER_POSDATE, PER_SALDATE, PN_CODE_F, PER_FATHERNAME, PER_FATHERSURNAME, 
							PN_CODE_M, PER_MOTHERNAME, PER_MOTHERSURNAME, PER_ADD1, PER_ADD2, PV_CODE, MOV_CODE, 
							PER_ORDAIN, PER_SOLDIER, PER_MEMBER, PER_STATUS, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID, 
							APPROVE_PER_ID, REPLACE_PER_ID, ABSENT_FLAG, POEMS_ID, PER_HIP_FLAG, PER_CERT_OCC, 
							LEVEL_NO_SALARY)
							VALUES ($PER_ID, $PER_TYPE, '$OT_CODE', '$PN_CODE', '$PER_NAME', '$PER_SURNAME', 
							'$PER_ENG_NAME', '$PER_ENG_SURNAME', $ORG_ID, $POS_ID, $POEM_ID, '$LEVEL_NO', $PER_ORGMGT, 
							$PER_SALARY, $PER_MGTSALARY, $PER_SPSALARY, $PER_GENDER, '$MR_CODE', '$PER_CARDNO', 
							'$PER_OFFNO', '$PER_TAXNO', '$PER_BLOOD', '$RE_CODE', '$PER_BIRTHDATE', '$PER_RETIREDATE', 
							'$PER_STARTDATE', '$PER_OCCUPYDATE', '$PER_POSDATE', '$PER_SALDATE', '$PN_CODE_F', 
							'$PER_FATHERNAME', '$PER_FATHERSURNAME', '$PN_CODE_M', '$PER_MOTHERNAME', 
							'$PER_MOTHERSURNAME', '$PER_ADD1', '$PER_ADD2', '$PV_CODE', '$MOV_CODE', $PER_ORDAIN, 
							$PER_SOLDIER, $PER_MEMBER, $PER_STATUS, $UPDATE_USER, '$UPDATE_DATE', $DEPARTMENT_ID, 
							$APPROVE_PER_ID, $REPLACE_PER_ID, $ABSENT_FLAG, $POEMS_ID, '$PER_HIP_FLAG', 
							'$PER_CERT_OCC', '$LEVEL_NO_SALARY') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while				
		
		$cmd = " DELETE FROM PER_POSITIONHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " SELECT POH_ID, PER_ID, POH_EFFECTIVEDATE, MOV_CODE, POH_ENDDATE, POH_DOCNO, POH_DOCDATE, POH_POS_NO, 
						PM_CODE, LEVEL_NO, PL_CODE, PN_CODE, PT_CODE, CT_CODE, PV_CODE, AP_CODE, POH_ORGMGT, ORG_ID_1, 
						ORG_ID_2, ORG_ID_3, POH_UNDER_ORG1, POH_UNDER_ORG2, POH_ASS_ORG, POH_ASS_ORG1, POH_ASS_ORG2, 
						POH_SALARY, POH_SALARY_POS, POH_REMARK, UPDATE_USER, UPDATE_DATE,
						PER_CARDNO, EP_CODE, POH_ORG1, POH_ORG2, POH_ORG3 FROM PER_POSITIONHIS ORDER BY POH_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$POH_ID = $data[POH_ID] + 0;
			$PER_ID = $data[PER_ID] + 0;
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
			$POH_ORGMGT = $data[POH_ORGMGT] + 0;
			$ORG_ID_1 = $data[ORG_ID_1] + 0;
			$ORG_ID_2 = $data[ORG_ID_2] + 0;
			$ORG_ID_3 = $data[ORG_ID_3] + 0;
			$POH_UNDER_ORG1 = trim($data[POH_UNDER_ORG1]);
			$POH_UNDER_ORG2 = trim($data[POH_UNDER_ORG2]);
			$POH_ASS_ORG = trim($data[POH_ASS_ORG]);
			$POH_ASS_ORG1 = trim($data[POH_ASS_ORG1]);
			$POH_ASS_ORG2 = trim($data[POH_ASS_ORG2]);
			$POH_SALARY = $data[POH_SALARY] + 0;
			$POH_SALARY_POS = $data[POH_SALARY_POS] + 0;
			$POH_REMARK = trim($data[POH_REMARK]);
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			$PER_CARDNO = trim($data[PER_CARDNO]);
			$EP_CODE = trim($data[EP_CODE]);
			$POH_ORG1 = trim($data[POH_ORG1]);
			$POH_ORG2 = trim($data[POH_ORG2]);
			$POH_ORG3 = trim($data[POH_ORG3]);
			if (!$POH_ORGMGT) $POH_ORGMGT = "NULL";
			if (!$ORG_ID_1) $ORG_ID_1 = "NULL";
			if (!$ORG_ID_2) $ORG_ID_2 = "NULL";
			if (!$ORG_ID_3) $ORG_ID_3 = "NULL";
			if (!$POH_SALARY) $POH_SALARY = "NULL";
			if (!$POH_SALARY_POS) $POH_SALARY_POS = "NULL";
			if ($POH_REMARK=="'") $POH_REMARK = "NULL";

			$cmd = " INSERT INTO PER_POSITIONHIS (POH_ID, PER_ID, POH_EFFECTIVEDATE, MOV_CODE, POH_ENDDATE, POH_DOCNO, 
							POH_DOCDATE, POH_POS_NO, PM_CODE, LEVEL_NO, PL_CODE, PN_CODE, PT_CODE, CT_CODE, PV_CODE, AP_CODE, 
							POH_ORGMGT, ORG_ID_1, ORG_ID_2, ORG_ID_3, POH_UNDER_ORG1, POH_UNDER_ORG2, POH_ASS_ORG, 
							POH_ASS_ORG1, POH_ASS_ORG2, POH_SALARY, POH_SALARY_POS, POH_REMARK, 
							UPDATE_USER, UPDATE_DATE, PER_CARDNO, EP_CODE, POH_ORG1, POH_ORG2, POH_ORG3)
							VALUES ($POH_ID, $PER_ID, '$POH_EFFECTIVEDATE', '$MOV_CODE', '$POH_ENDDATE', '$POH_DOCNO', '$POH_DOCDATE', 
							'$POH_POS_NO', '$PM_CODE', '$LEVEL_NO', '$PL_CODE', '$PN_CODE', '$PT_CODE', '$CT_CODE', '$PV_CODE', '$AP_CODE', 
							$POH_ORGMGT, $ORG_ID_1, $ORG_ID_2, $ORG_ID_3, '$POH_UNDER_ORG1', '$POH_UNDER_ORG2', '$POH_ASS_ORG', 
							'$POH_ASS_ORG1', '$POH_ASS_ORG2', $POH_SALARY, $POH_SALARY_POS, '$POH_REMARK', $UPDATE_USER, 
							'$UPDATE_DATE', '$PER_CARDNO', '$EP_CODE', '$POH_ORG1', '$POH_ORG2', '$POH_ORG3') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						
		
		$cmd = " DELETE FROM PER_SALARYHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " SELECT SAH_ID, PER_ID, SAH_EFFECTIVEDATE, MOV_CODE, SAH_SALARY, SAH_DOCNO, SAH_DOCDATE, SAH_ENDDATE, 
						UPDATE_USER,	UPDATE_DATE, PER_CARDNO FROM PER_SALARYHIS ORDER BY SAH_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
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
			if (!$SAH_SALARY) $SAH_SALARY = "NULL";

			$cmd = " INSERT INTO PER_SALARYHIS (SAH_ID, PER_ID, SAH_EFFECTIVEDATE, MOV_CODE, SAH_SALARY, SAH_DOCNO, 
							SAH_DOCDATE, SAH_ENDDATE, UPDATE_USER, UPDATE_DATE, PER_CARDNO)
							VALUES ($SAH_ID, $PER_ID, '$SAH_EFFECTIVEDATE', '$MOV_CODE', $SAH_SALARY, '$SAH_DOCNO', '$SAH_DOCDATE', 
							'$SAH_ENDDATE', $UPDATE_USER, '$UPDATE_DATE', '$PER_CARDNO') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						
		
		$cmd = " DELETE FROM PER_ABSENTHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " SELECT ABS_ID, PER_ID, AB_CODE, ABS_STARTDATE, ABS_STARTPERIOD, ABS_ENDDATE, ABS_ENDPERIOD, ABS_DAY, 
						UPDATE_USER,	UPDATE_DATE, PER_CARDNO FROM PER_ABSENTHIS ORDER BY ABS_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
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
			if (!$ABS_STARTPERIOD) $ABS_STARTPERIOD = "NULL";
			if (!$ABS_ENDPERIOD) $ABS_ENDPERIOD = "NULL";
			if (!$ABS_DAY) $ABS_DAY = "NULL";

			$cmd = " INSERT INTO PER_ABSENTHIS (ABS_ID, PER_ID, AB_CODE, ABS_STARTDATE, ABS_STARTPERIOD, ABS_ENDDATE, 
							ABS_ENDPERIOD, ABS_DAY, UPDATE_USER, UPDATE_DATE, PER_CARDNO)
							VALUES ($ABS_ID, $PER_ID, '$AB_CODE', '$ABS_STARTDATE', $ABS_STARTPERIOD, '$ABS_ENDDATE', 
							$ABS_ENDPERIOD, $ABS_DAY, $UPDATE_USER, '$UPDATE_DATE', '$PER_CARDNO') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						
		
		$cmd = " DELETE FROM PER_EDUCATE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " SELECT EDU_ID, PER_ID, EDU_SEQ, EDU_STARTYEAR, EDU_ENDYEAR, ST_CODE, CT_CODE, EDU_FUND, EN_CODE, 
						EM_CODE, INS_CODE,	EDU_TYPE, UPDATE_USER, UPDATE_DATE, PER_CARDNO 
						FROM PER_EDUCATE ORDER BY EDU_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
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

			$cmd = " INSERT INTO PER_EDUCATE (EDU_ID, PER_ID, EDU_SEQ, EDU_STARTYEAR, EDU_ENDYEAR, ST_CODE, CT_CODE, 
							EDU_FUND, EN_CODE, EM_CODE, INS_CODE, EDU_TYPE, UPDATE_USER, UPDATE_DATE, PER_CARDNO)
							VALUES ($EDU_ID, $PER_ID, '$EDU_SEQ', '$EDU_STARTYEAR', '$EDU_ENDYEAR', '$ST_CODE', '$CT_CODE', '$EDU_FUND', 
							'$EN_CODE', '$EM_CODE', '$INS_CODE', '$EDU_TYPE', $UPDATE_USER, '$UPDATE_DATE', '$PER_CARDNO') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						
		
		$cmd = " DELETE FROM PER_EXTRAHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " SELECT EXH_ID, PER_ID, EXH_EFFECTIVEDATE, EX_CODE, EXH_AMT, EXH_ENDDATE, UPDATE_USER, UPDATE_DATE, 
						PER_CARDNO FROM PER_EXTRAHIS ORDER BY EXH_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$EXH_ID = $data[EXH_ID] + 0;
			$PER_ID = $data[PER_ID] + 0;
			$EXH_EFFECTIVEDATE = trim($data[EXH_EFFECTIVEDATE]);
			$EX_CODE = trim($data[EX_CODE]);
			$EXH_AMT = $data[EXH_AMT] + 0;
			$EXH_ENDDATE = trim($data[EXH_ENDDATE]);
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			$PER_CARDNO = trim($data[PER_CARDNO]);
			if (!$EXH_AMT) $EXH_AMT = "NULL";

			$cmd = " INSERT INTO PER_EXTRAHIS (EXH_ID, PER_ID, EXH_EFFECTIVEDATE, EX_CODE, EXH_AMT, EXH_ENDDATE, 
							UPDATE_USER, UPDATE_DATE, PER_CARDNO)
							VALUES ($EXH_ID, $PER_ID, '$EXH_EFFECTIVEDATE', '$EX_CODE', $EXH_AMT, '$EXH_ENDDATE', $UPDATE_USER, 
							'$UPDATE_DATE', '$PER_CARDNO') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						
		
		$cmd = " DELETE FROM PER_EXTRA_INCOMEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " SELECT EXINH_ID, PER_ID, EXINH_EFFECTIVEDATE, EXIN_CODE, EXINH_AMT, EXINH_ENDDATE, UPDATE_USER, 
						UPDATE_DATE, PER_CARDNO FROM PER_EXTRA_INCOMEHIS ORDER BY EXINH_ID ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$EXINH_ID = $data[EXINH_ID] + 0;
			$PER_ID = $data[PER_ID] + 0;
			$EXINH_EFFECTIVEDATE = trim($data[EXINH_EFFECTIVEDATE]);
			$EXIN_CODE = trim($data[EXIN_CODE]);
			$EXINH_AMT = $data[EXINH_AMT] + 0;
			$EXINH_ENDDATE = trim($data[EXINH_ENDDATE]);
			$UPDATE_USER = $data[UPDATE_USER] + 0;
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			$PER_CARDNO = trim($data[PER_CARDNO]);
			if (!$EXINH_AMT) $EXINH_AMT = "NULL";

			$cmd = " INSERT INTO PER_EXTRA_INCOMEHIS (EXINH_ID, PER_ID, EXINH_EFFECTIVEDATE, EXIN_CODE, EXINH_AMT, 
							EXINH_ENDDATE, UPDATE_USER, UPDATE_DATE, PER_CARDNO)
							VALUES ($EXINH_ID, $PER_ID, '$EXINH_EFFECTIVEDATE', '$EXIN_CODE', $EXINH_AMT, '$EXINH_ENDDATE', 
							$UPDATE_USER, '$UPDATE_DATE',	'$PER_CARDNO') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end while						
	} // end if

?>