<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis352 = new connect_dpis35($dpis35db_host, $dpis35db_name, $dpis35db_user, $dpis35db_pwd);
	$CREATE_DATE = "NOW()";
	$UPDATE_USER = 99999;
	$UPDATE_DATE = date("Y-m-d H:i:s");

	if( $command=='CONVERT' ) {  
// วิทยากร  
		$cmd = " DELETE FROM PER_TRAINNER WHERE UPDATE_USER = $UPDATE_USER ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " SELECT TRAINNER_ID, TRAINNER_T_NAME, GENDER, INOUT_ORG, TO_CHAR(BIRTHDAY,'YYYY-MM-DD') AS BIRTHDAY, 
						EDUC_HIS1, EDUC_HIS2, EDUC_HIS3, CURR_POSI, WORK_PLACE, WORK_TEL, WORK_EXPERIENCE, TRAIN_EXPERIENCE, 
						ADDRESS, ADDRESS_TEL, TECHNOLOGY_HIS, SUBJECT_SKILL1, SUBJECT_SKILL2, SUBJECT_SKILL3, DEPT_TRAIN_HIS, 
						SPEC_ABILITY, HOBBY, SEQ, STATUS, TO_CHAR(LAST_UPDATE,'YYYY-MM-DD') AS LAST_UPDATE, WHO_UPDATE              
						FROM T_TRAINNER ORDER BY TRAINNER_ID ";
		$db_dpis35->send_cmd($cmd);
		$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$TRAINNER_ID = $data[TRAINNER_ID] + 0;
			$TRAINNER_T_NAME = trim($data[TRAINNER_T_NAME]);
			$GENDER = trim($data[GENDER]);
			if ($GENDER=="M") $GENDER = 1;
			elseif ($GENDER=="F") $GENDER = 2;
			$INOUT_ORG = $data[INOUT_ORG] + 0;
			$BIRTHDAY = trim($data[BIRTHDAY]);
			$EDUC_HIS1 = trim($data[EDUC_HIS1]);
			$EDUC_HIS2 = trim($data[EDUC_HIS2]);
			$EDUC_HIS3 = trim($data[EDUC_HIS3]);
			$CURR_POSI = trim($data[CURR_POSI]);
			$WORK_PLACE = trim($data[WORK_PLACE]);
			$WORK_TEL = trim($data[WORK_TEL]);
			$WORK_EXPERIENCE = trim($data[WORK_EXPERIENCE]);
			$TRAIN_EXPERIENCE = trim($data[TRAIN_EXPERIENCE]);
			$ADDRESS = trim($data[ADDRESS]);
			$ADDRESS_TEL = trim($data[ADDRESS_TEL]);
			$TECHNOLOGY_HIS = trim($data[TECHNOLOGY_HIS]);
			$SUBJECT_SKILL1 = trim($data[SUBJECT_SKILL1]);
			$SUBJECT_SKILL2 = trim($data[SUBJECT_SKILL2]);
			$SUBJECT_SKILL3 = trim($data[SUBJECT_SKILL3]);
			$DEPT_TRAIN_HIS = trim($data[DEPT_TRAIN_HIS]);
			$SPEC_ABILITY = trim($data[SPEC_ABILITY]);
			$HOBBY = trim($data[HOBBY]);
			$SEQ = $data[SEQ] + 0;
			$STATUS = $data[STATUS] + 0;
			$LAST_UPDATE = trim($data[LAST_UPDATE]);
			$WHO_UPDATE = trim($data[WHO_UPDATE]);

			$cmd = " INSERT INTO PER_TRAINNER (TRAINNER_ID, TRAINNER_NAME, TN_GENDER, TN_INOUT_ORG, TN_BIRTHDATE, 
							TN_EDU_HIS1, TN_EDU_HIS2, TN_EDU_HIS3, TN_POSITION, TN_WORK_PLACE, TN_WORK_TEL, TN_WORK_EXPERIENCE, 
							TN_TRAIN_EXPERIENCE, TN_ADDRESS, TN_ADDRESS_TEL, TN_TECHNOLOGY_HIS, TN_TRAIN_SKILL1, 
							TN_TRAIN_SKILL2, TN_TRAIN_SKILL3, 
							TN_DEPT_TRAIN, TN_SPEC_ABILITY, TN_HOBBY, TN_SEQ, TN_ACTIVE, UPDATE_USER, UPDATE_DATE)
							VALUES ($TRAINNER_ID, '$TRAINNER_T_NAME', $GENDER, $INOUT_ORG, '$BIRTHDAY', '$EDUC_HIS1', '$EDUC_HIS2', 
							'$EDUC_HIS3', '$CURR_POSI', '$WORK_PLACE', '$WORK_TEL', '$WORK_EXPERIENCE', '$TRAIN_EXPERIENCE', 
							'$ADDRESS', '$ADDRESS_TEL', '$TECHNOLOGY_HIS', '$SUBJECT_SKILL1', '$SUBJECT_SKILL2', '$SUBJECT_SKILL3', 
							'$DEPT_TRAIN_HIS', '$SPEC_ABILITY', '$HOBBY', $SEQ, $STATUS, $UPDATE_USER, '$LAST_UPDATE') ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
		} // end while						

// แผนฝึกอบรมประจำปี
		$cmd = " DELETE FROM PER_TRAIN_PLAN WHERE UPDATE_USER = $UPDATE_USER ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " SELECT PLAN_ID, PLAN_NAME, BYEAR, INOUT_PLAN, ZONE1, STATUS, TO_CHAR(LAST_UPDATE,'YYYY-MM-DD') AS 
						LAST_UPDATE, WHO_UPDATE      
						FROM T_PLAN ORDER BY PLAN_ID ";
		$db_dpis35->send_cmd($cmd);
		$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PLAN_ID = $data[PLAN_ID] + 0;
			$PLAN_NAME = trim($data[PLAN_NAME]);
			$BYEAR = trim($data[BYEAR]);
			$INOUT_PLAN = $data[INOUT_PLAN] + 0;
			$ZONE1 = trim($data[ZONE1]);
			$STATUS = $data[STATUS] + 0;
			$LAST_UPDATE = trim($data[LAST_UPDATE]);
			$WHO_UPDATE = trim($data[WHO_UPDATE]);

			$cmd = " INSERT INTO PER_TRAIN_PLAN (PLAN_ID, PLAN_NAME, TP_BUDGET_YEAR, TP_INOUT_PLAN, TP_ZONE, PLAN_ID_REF, 
							DEPARTMENT_ID, TP_ACTIVE, UPDATE_USER, UPDATE_DATE)
							VALUES ($PLAN_ID, '$PLAN_NAME', '$BYEAR', $INOUT_PLAN, '$ZONE1', NULL, $DEPARTMENT_ID, $STATUS, 
							$UPDATE_USER, '$LAST_UPDATE') ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
		} // end while						

// หมวดโครงการ
		$cmd = " DELETE FROM PER_PROJECT_GROUP WHERE UPDATE_USER = $UPDATE_USER ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " SELECT GROUP_ID, GROUP_T_NAME, STATUS, TO_CHAR(LAST_UPDATE,'YYYY-MM-DD') AS LAST_UPDATE, WHO_UPDATE 
						FROM T_PROJECT_GROUP ORDER BY GROUP_ID ";
		$db_dpis35->send_cmd($cmd);
		$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$GROUP_ID = $data[GROUP_ID] + 0;
			$GROUP_T_NAME = trim($data[GROUP_T_NAME]);
			$STATUS = $data[STATUS] + 0;
			if ($STATUS==0) $STATUS = 1;
			elseif ($STATUS==1) $STATUS = 0;
			$LAST_UPDATE = trim($data[LAST_UPDATE]);
			$WHO_UPDATE = trim($data[WHO_UPDATE]);

			$cmd = " INSERT INTO PER_PROJECT_GROUP (PG_ID, PG_NAME, PG_ACTIVE, UPDATE_USER, UPDATE_DATE)
							VALUES ($GROUP_ID, '$GROUP_T_NAME', $STATUS, $UPDATE_USER, '$LAST_UPDATE') ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
		} // end while						

// ค่าใช้จ่ายโครงการ
		$cmd = " DELETE FROM PER_PROJECT_PAYMENT WHERE UPDATE_USER = $UPDATE_USER ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " SELECT DISTINCT PAYM_ID, NAME
						FROM T_PROJECT_COURSE_PAYMENT ORDER BY PAYM_ID ";
		$db_dpis35->send_cmd($cmd);
		$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PAYM_ID = $data[PAYM_ID] + 0;
			$NAME = trim($data[NAME]);
			if ($PAYM_ID==16) $NAME = "ค่าใช้จ่ายอื่นๆ";

			$cmd = " INSERT INTO PER_PROJECT_PAYMENT (PP_ID, PP_NAME, PP_ACTIVE, UPDATE_USER, UPDATE_DATE)
							VALUES ($PAYM_ID, '$NAME', 1, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
		} // end while						

// โครงการตามแผนฝึกอบรมประจำปี  
		$cmd = " DELETE FROM PER_TRAIN_PROJECT WHERE UPDATE_USER = $UPDATE_USER ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " SELECT PROJ_ID, a.PLAN_ID, PROJ_T_NAME, a.DEPT_ID, DEPT_NAME, a.DIVISION_ID, DIVISION_NAME, a.APP_ID, 
						d.PERS_CODE, GROUP_ID, TO_CHAR(APP_ISS_DATE,'YYYY-MM-DD') AS APP_ISS_DATE, APP_BOOK_ID, INOUT_TRAIN, 
						a.ZONE1, a.WHO_UPDATE, a.STATUS, TO_CHAR(a.LAST_UPDATE,'YYYY-MM-DD') AS LAST_UPDATE, e.BYEAR     
						FROM T_PROJECT a, T_DEPARTMENT b, T_DIVISION c, T_APPROVAL d, T_PLAN e 
						WHERE a.DEPT_ID = b.DEPT_ID(+) AND a.DIVISION_ID = c.DIVISION_ID(+) AND a.APP_ID = d.APP_ID(+) AND 
						a.PLAN_ID = e.PLAN_ID
						ORDER BY PROJ_ID ";
		$db_dpis35->send_cmd($cmd);
		$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PROJ_ID = $data[PROJ_ID] + 0;
			$PLAN_ID = $data[PLAN_ID] + 0;
			$PROJ_T_NAME = trim($data[PROJ_T_NAME]);
			$DEPT_ID = $data[DEPT_ID] + 0;
			$DEPT_NAME = trim($data[DEPT_NAME]);
			$DIVISION_ID = trim($data[DIVISION_ID]);
			$DIVISION_NAME = trim($data[DIVISION_NAME]);
			$APP_ID = trim($data[APP_ID]);
			$PERS_CODE = trim($data[PERS_CODE]);
			$GROUP_ID = $data[GROUP_ID] + 0;
			$APP_ISS_DATE = trim($data[APP_ISS_DATE]);
			$APP_BOOK_ID = trim($data[APP_BOOK_ID]);
			$INOUT_TRAIN = trim($data[INOUT_TRAIN]);
			if ($INOUT_TRAIN=="O") $INOUT_TRAIN = 0;
			elseif ($INOUT_TRAIN=="I") $INOUT_TRAIN = 1;
			else $INOUT_TRAIN = "NULL";
			$ZONE1 = trim($data[ZONE1]);
			$WHO_UPDATE = trim($data[WHO_UPDATE]);
			$STATUS = $data[STATUS] + 0;
			$LAST_UPDATE = trim($data[LAST_UPDATE]);
			$BYEAR = trim($data[BYEAR]);
			if (!$PERS_CODE) $PERS_CODE = "NULL";

			$cmd = " INSERT INTO PER_TRAIN_PROJECT (PROJ_ID, PLAN_ID, PROJ_NAME, TPJ_BUDGET_YEAR, TPJ_MANAGE_ORG, 
							TPJ_RESPONSE_ORG, TPJ_APP_PER_ID, PG_ID, TPJ_APP_DATE, TPJ_APP_DOC_NO, TPJ_INOUT_TRAIN, TPJ_ZONE, 
							PROJ_ID_REF, DEPARTMENT_ID, TPJ_ACTIVE, UPDATE_USER, UPDATE_DATE)
							VALUES ($PROJ_ID, $PLAN_ID, '$PROJ_T_NAME', '$BYEAR', '$DEPT_NAME', '$DIVISION_NAME', $PERS_CODE, $GROUP_ID, 
							'$APP_ISS_DATE', '$APP_BOOK_ID', $INOUT_TRAIN, $ZONE1, NULL, $DEPARTMENT_ID, $STATUS, $UPDATE_USER, 
							'$LAST_UPDATE') ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
		} // end while						

// หลักสูตรในโครงการ 
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_TRAIN' ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_TRAIN WHERE UPDATE_USER = $UPDATE_USER ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " SELECT PROJ_COURSE_ID, COURSE_T_NAME, CLASS AS CLASS1, ZONE1, STATUS, 
						TO_CHAR(LAST_UPDATE,'YYYY-MM-DD') AS LAST_UPDATE, WHO_UPDATE      
						FROM T_PROJECT_COURSE ORDER BY PROJ_COURSE_ID ";
		$db_dpis35->send_cmd($cmd);
		$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PROJ_COURSE_ID = $data[PROJ_COURSE_ID] + 0;
			$COURSE_T_NAME = trim($data[COURSE_T_NAME]);
			$CLASS = $data[CLASS1] + 0;
			$ZONE1 = trim($data[ZONE1]);
			$STATUS = $data[STATUS] + 0;
			if ($STATUS==0) $STATUS = 1;
			elseif ($STATUS==1) $STATUS = 0;
			$LAST_UPDATE = trim($data[LAST_UPDATE]);
			$WHO_UPDATE = trim($data[WHO_UPDATE]);

			$cmd = " select TR_CODE from PER_TRAIN where TR_NAME = '$COURSE_T_NAME' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if ($count_data) {			
				$data2 = $db_dpis2->get_array();
				$NEW_CODE = $data2[TR_CODE] + 0;
				$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
								VALUES ('PER_TRAIN', '$PROJ_COURSE_ID', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			} else {
				$cmd = " INSERT INTO PER_TRAIN (TR_CODE, TR_TYPE, TR_NAME, TR_ACTIVE, UPDATE_USER, UPDATE_DATE)
								VALUES ($PROJ_COURSE_ID, 1, '$COURSE_T_NAME', $STATUS, $UPDATE_USER, '$LAST_UPDATE') ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			} 
		} // end while						

// หลักสูตรในโครงการตามแผนฝึกอบรมประจำปี
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_TRAIN_PROJECT_DTL' ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_TRAIN_PROJECT_DTL WHERE UPDATE_USER = $UPDATE_USER ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " SELECT PROJ_ID, PROJ_COURSE_ID, CLASS AS CLASS1, MAXDATE, PLACE, TRGT_POSI_CODE, TRGT_POSI_NAME, 
						TRGT_LVL_FM, TRGT_LVL_TO, TO_CHAR(STTDATE,'YYYY-MM-DD') AS STTDATE, TO_CHAR(FINDATE,'YYYY-MM-DD') AS 
						FINDATE, BUDGET, BUDGET_USED, LOCAL_TAX, LOCAL_TAX_USED, PERS_KLUNG, PERS_KLUNG_USED, OTH_BUDGET, 
						OTH_BUDGET_NAME, OTH_BUDGET_USED, PUBLISH_STATUS, TO_CHAR(PUBLISH_EXPIRE_DATE,'YYYY-MM-DD') AS 
						PUBLISH_EXPIRE_DATE, DOM_INT, PROV_CODE, TYPE_COURSE, GROUP_ID, APP_ID, 
						TO_CHAR(APP_ISS_DATE,'YYYY-MM-DD') AS APP_ISS_DATE, APP_BOOK_ID, INOUT_TRAIN, DEPT_ID, DIVISION_ID, 
						COUNTRY_CODE            
						FROM T_PROJECT_COURSE_REF ORDER BY PROJ_ID, PROJ_COURSE_ID, CLASS ";
		$db_dpis35->send_cmd($cmd);
		$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PROJ_ID = $data[PROJ_ID] + 0;
			$PROJ_COURSE_ID = $data[PROJ_COURSE_ID] + 0;
			$CLASS = $data[CLASS1] + 0;
			$MAXDATE = $data[MAXDATE] + 0;
			$PLACE = trim($data[PLACE]);
			$TRGT_POSI_CODE = trim($data[TRGT_POSI_CODE]);
			$TRGT_POSI_NAME = trim($data[TRGT_POSI_NAME]);
			$TRGT_LVL_FM = $data[TRGT_LVL_FM] + 0;
			$TRGT_LVL_FM = str_pad(trim($TRGT_LVL_FM), 2, "0", STR_PAD_LEFT);
			$TRGT_LVL_TO = $data[TRGT_LVL_TO] + 0;
			$TRGT_LVL_TO = str_pad(trim($TRGT_LVL_TO), 2, "0", STR_PAD_LEFT);
			$STTDATE = trim($data[STTDATE]);
			$FINDATE = trim($data[FINDATE]);
			$BUDGET = $data[BUDGET] + 0;
			$BUDGET_USED = $data[BUDGET_USED] + 0;
			$LOCAL_TAX = $data[LOCAL_TAX] + 0;
			$LOCAL_TAX_USED = $data[LOCAL_TAX_USED] + 0;
			$PERS_KLUNG = $data[PERS_KLUNG] + 0;
			$PERS_KLUNG_USED = $data[PERS_KLUNG_USED] + 0;
			$OTH_BUDGET = $data[OTH_BUDGET] + 0;
			$OTH_BUDGET_NAME = trim($data[OTH_BUDGET_NAME]);
			$OTH_BUDGET_USED = $data[OTH_BUDGET_USED] + 0;
			$PUBLISH_STATUS = $data[PUBLISH_STATUS] + 0;
			$PUBLISH_EXPIRE_DATE = trim($data[PUBLISH_EXPIRE_DATE]);
			$DOM_INT = trim($data[DOM_INT]);
			$PROV_CODE = trim($data[PROV_CODE]);
			$TYPE_COURSE = trim($data[TYPE_COURSE]);
			$GROUP_ID = $data[GROUP_ID] + 0;
			$APP_ID = $data[APP_ID] + 0;
			$APP_ISS_DATE = trim($data[APP_ISS_DATE]);
			$APP_BOOK_ID = trim($data[APP_BOOK_ID]);
			$INOUT_TRAIN = trim($data[INOUT_TRAIN]);
			$DEPT_ID = $data[DEPT_ID] + 0;
			$DIVISION_ID = $data[DIVISION_ID] + 0;
			$COUNTRY_CODE = $data[COUNTRY_CODE] + 0;

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_TRAIN' AND OLD_CODE = $PROJ_COURSE_ID ";
			$count_data = $db_dpis2->send_cmd($cmd);
			if ($count_data) {
				$data2 = $db_dpis2->get_array();
				$PROJ_COURSE_ID = $data2[NEW_CODE] + 0;
			}

			$cmd = " INSERT INTO PER_TRAIN_PROJECT_DTL (PROJ_ID, TR_CODE, TR_CLASS, MAX_DAY, TRAIN_PLACE, TARGET_POSITION, 
							LEVEL_NO_START, LEVEL_NO_END, START_DATE, END_DATE, BUDGET, BUDGET_USED, LOCAL_TAX, 
							LOCAL_TAX_USED, PER_DEVELOP_FUND, PER_DEVELOP_FUND_USED, OTHER_BUDGET, OTHER_BUDGET_USED, 
							UPDATE_USER, UPDATE_DATE)
							VALUES ($PROJ_ID, $PROJ_COURSE_ID, $CLASS, $MAXDATE, '$PLACE', '$TRGT_POSI_NAME', '$TRGT_LVL_FM', 
							'$TRGT_LVL_TO', '$STTDATE', '$FINDATE', $BUDGET, $BUDGET_USED, $LOCAL_TAX, $LOCAL_TAX_USED, 
							$PERS_KLUNG, $PERS_KLUNG_USED, $OTH_BUDGET, $OTH_BUDGET_USED, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
		} // end while						

// รายละเอียดค่าใช้จ่ายของโครงการ
		$cmd = " DELETE FROM PER_TRAIN_PROJECT_PAYMENT WHERE UPDATE_USER = $UPDATE_USER ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " SELECT PROJ_ID, PROJ_COURSE_ID, PAYM_ID, CLASS AS CLASS1, BUDGET_SOURCE, NAME, AMOUNT            
						FROM T_PROJECT_COURSE_PAYMENT ORDER BY PROJ_ID, PROJ_COURSE_ID, PAYM_ID, CLASS ";
		$db_dpis35->send_cmd($cmd);
		$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PROJ_ID = $data[PROJ_ID] + 0;
			$PROJ_COURSE_ID = $data[PROJ_COURSE_ID] + 0;
			$PAYM_ID = $data[PAYM_ID] + 0;
			$CLASS = $data[CLASS1] + 0;
			$BUDGET_SOURCE = trim($data[BUDGET_SOURCE]);
			$NAME = trim($data[NAME]);
			if ($PAYM_ID != 16) $NAME = "";
			$AMOUNT = $data[AMOUNT] + 0;

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_TRAIN' AND OLD_CODE = $PROJ_COURSE_ID ";
			$count_data = $db_dpis2->send_cmd($cmd);
			if ($count_data) {
				$data2 = $db_dpis2->get_array();
				$PROJ_COURSE_ID = $data2[NEW_CODE] + 0;
			}

			$cmd = " INSERT INTO PER_TRAIN_PROJECT_PAYMENT (PROJ_ID, TR_CODE, PP_ID, TR_CLASS, BUDGET_SOURCE, 
							OTHER_PAYMENT, PAY_AMOUNT, UPDATE_USER, UPDATE_DATE)
							VALUES ($PROJ_ID, $PROJ_COURSE_ID, $PAYM_ID, $CLASS, '$BUDGET_SOURCE', '$NAME', $AMOUNT, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
		} // end while						

// บุคลากรที่อบรมในโครงการ
		$cmd = " DELETE FROM PER_TRAIN_PROJECT_PERSONAL WHERE UPDATE_USER = $UPDATE_USER ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " SELECT PROJ_ID, PROJ_COURSE_ID, CLASS AS CLASS1, PERS_CODE, PRF_CODE, PERS_T_F_NAME, PERS_T_L_NAME, 
						ORG_ID, LEVEL_NO, POSI_CODE, TEST_RESULT, CAN_PERS, SEQ            
						FROM T_PROJECT_COURSE_PERSONAL_REF
						ORDER BY PROJ_ID, PROJ_COURSE_ID, CLASS ";
		$db_dpis35->send_cmd($cmd);
		$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PROJ_ID = $data[PROJ_ID] + 0;
			$PROJ_COURSE_ID = $data[PROJ_COURSE_ID] + 0;
			$CLASS = $data[CLASS1] + 0;
			$PERS_CODE = $data[PERS_CODE] + 0;
			$PRF_CODE = trim($data[PRF_CODE]);
			$PERS_T_F_NAME = trim($data[PERS_T_F_NAME]);
			$PERS_T_L_NAME = trim($data[PERS_T_L_NAME]);
			$ORG_ID = trim($data[ORG_ID]);
			$LEVEL_NO = trim($data[LEVEL_NO]);
			$LEVEL_NO = str_pad(trim($LEVEL_NO), 2, "0", STR_PAD_LEFT);
			$POSI_CODE = trim($data[POSI_CODE]);
			$TEST_RESULT = trim($data[TEST_RESULT]);
			$CAN_PERS = trim($data[CAN_PERS]);
			$SEQ = $data[SEQ] + 0;

			$cmd = " SELECT ORG_NAME FROM T_ORG WHERE ORG_ID = '$ORG_ID' ";
			$db_dpis352->send_cmd($cmd);
			$db_dpis352->show_error();
			$data2 = $db_dpis352->get_array();
			$ORG_NAME = trim($data2[ORG_NAME]);

			$cmd = " SELECT POSI_T_NAME FROM T_POSITION WHERE POSI_CODE = '$POSI_CODE' ";
			$db_dpis352->send_cmd($cmd);
			$db_dpis352->show_error();
			$data2 = $db_dpis352->get_array();
			$POSI_T_NAME = trim($data2[POSI_T_NAME]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_TRAIN' AND OLD_CODE = $PROJ_COURSE_ID ";
			$count_data = $db_dpis2->send_cmd($cmd);
			if ($count_data) {
				$data2 = $db_dpis2->get_array();
				$PROJ_COURSE_ID = $data2[NEW_CODE] + 0;
			}

			$cmd = " INSERT INTO PER_TRAIN_PROJECT_PERSONAL (PROJ_ID, TR_CODE, TR_CLASS, PER_ID, ORG_NAME, PL_NAME, 
							LEVEL_NO, UPDATE_USER, UPDATE_DATE)
							VALUES ($PROJ_ID, $PROJ_COURSE_ID, $CLASS, $PERS_CODE, '$ORG_NAME', '$POSI_T_NAME', '$LEVEL_NO', 
							$UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
		} // end while						

// สาขาวิชาเอก  
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_EDUCMAJOR' ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_EDUCMAJOR WHERE UPDATE_USER = $UPDATE_USER ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " SELECT SCHOLAR_BRANCH_ID, SCHOLAR_BRANCH_NAME 
						FROM T_SCHOLAR_BRANCH ORDER BY SCHOLAR_BRANCH_NAME ";
		$db_dpis35->send_cmd($cmd);
		$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$SCHOLAR_BRANCH_ID = $data[SCHOLAR_BRANCH_ID] + 0;
			$SCHOLAR_BRANCH_NAME = trim($data[SCHOLAR_BRANCH_NAME]);

			$cmd = " select EM_CODE from PER_EDUCMAJOR where EM_NAME = '$SCHOLAR_BRANCH_NAME' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if ($count_data) {			
				$data2 = $db_dpis2->get_array();
				$NEW_CODE = trim($data2[EM_CODE]);
				$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
								VALUES ('PER_EDUCMAJOR', '$SCHOLAR_BRANCH_ID', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			} else {
				$cmd = " INSERT INTO PER_EDUCMAJOR (EM_CODE, EM_NAME, EM_ACTIVE, UPDATE_USER, UPDATE_DATE)
								VALUES ('$SCHOLAR_BRANCH_ID', '$SCHOLAR_BRANCH_NAME', 1, $UPDATE_USER, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			}
		} // end while						

// วุฒิการศึกษา  
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_EDUCNAME' ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_EDUCNAME WHERE UPDATE_USER = $UPDATE_USER ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " SELECT SCHOLAR_COURSE_ID, SCHOLAR_COURSE_NAME 
						FROM T_SCHOLAR_COURSE ORDER BY SCHOLAR_COURSE_NAME ";
		$db_dpis35->send_cmd($cmd);
		$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$NEW_CODE = "";
			$SCHOLAR_COURSE_ID = $data[SCHOLAR_COURSE_ID] + 0;
			$SCHOLAR_COURSE_NAME = trim($data[SCHOLAR_COURSE_NAME]);
			if ($SCHOLAR_COURSE_NAME=="นิติศาสตร์มหาบัณฑิต") $NEW_CODE = "6010018";
			elseif ($SCHOLAR_COURSE_NAME=="รัฐประศาสนศาสตร์มหาบัณฑิต") $NEW_CODE = "6010049";
			elseif ($SCHOLAR_COURSE_NAME=="รัฐศาสตร์มหาบัณฑิต") $NEW_CODE = "6010050";
			elseif ($SCHOLAR_COURSE_NAME=="วิทยาศาสตร์มหาบัณฑิต") $NEW_CODE = "6010054";
			elseif ($SCHOLAR_COURSE_NAME=="ศิลปศาสตร์มหาบัณฑิต") $NEW_CODE = "6010089";
			elseif ($SCHOLAR_COURSE_NAME=="เศรษฐศาสตร์มหาบัณฑิต") $NEW_CODE = "6010104";

			$cmd = " select EN_CODE from PER_EDUCNAME where EN_NAME = '$SCHOLAR_COURSE_NAME' OR 
							EN_NAME = 'ป.$SCHOLAR_COURSE_NAME' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if ($count_data || $NEW_CODE) {			
				$data2 = $db_dpis2->get_array();
				if (!$NEW_CODE) $NEW_CODE = trim($data2[EN_CODE]);
				$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
								VALUES ('PER_EDUCNAME', '$SCHOLAR_COURSE_ID', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			} else {
				
				$cmd = " INSERT INTO PER_EDUCNAME (EN_CODE, EL_CODE, EN_NAME, EN_ACTIVE, UPDATE_USER, UPDATE_DATE)
								VALUES ('$SCHOLAR_COURSE_ID', '99', '$SCHOLAR_COURSE_NAME', 1, $UPDATE_USER, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			}
		} // end while						

// วุฒิการศึกษา  
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_EDUCNAME' ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_EDUCNAME WHERE UPDATE_USER = $UPDATE_USER ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " SELECT ELVL_ID, ELVL_NAME 
						FROM T_SCHOLAR_ELEVEL ORDER BY ELVL_NAME ";
		$db_dpis35->send_cmd($cmd);
		$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$NEW_CODE = "";
			$ELVL_ID = $data[ELVL_ID] + 0;
			$ELVL_NAME = trim($data[ELVL_NAME]);
			$arr_temp = explode(",", $ELVL_NAME);
			$ELVL_NAME1 = trim($arr_temp[0]);
			$ELVL_NAME2 = trim($arr_temp[1]);
			if ($ELVL_NAME=="สถิติการศึกษา,(ปกส.)") $NEW_CODE = "5010017";
			elseif ($ELVL_NAME=="อุสาหกรรมศาสตรบัณฑิต") $NEW_CODE = "4010166";
			elseif ($ELVL_NAME=="การบัญชี,(ปกส.)") $NEW_CODE = "5010005";
			elseif ($ELVL_NAME=="ประกาศนียบัตรวิชาชีพชั้นสูง (ปวส.)") $NEW_CODE = "3010000";
			elseif ($ELVL_NAME=="ประกาศนียบัตรชั้นสูง") $NEW_CODE = "5010000";
			elseif ($ELVL_NAME=="ประกาศนียบัตรวิชาการศึกษาชั้นสูง (ป.กศ.สูง)") $NEW_CODE = "2010043";
			elseif ($ELVL_NAME=="ประกาศนียบัตรประโยชคอาชีวศึกษาชั้นสูง") $NEW_CODE = "3010031";
			elseif ($ELVL_NAME=="ประกาศนียบัตรวิชาชีพ (ปวช.)") $NEW_CODE = "1010000";
			elseif ($ELVL_NAME=="ประกาศนียบัตรวิชาชีพเทคนิค (ปวท.)") $NEW_CODE = "2010000";
			elseif ($ELVL_NAME=="ปริญญาตรี" || $ELVL_NAME=="ปริญญาตรี (ไม่ทราบชื่อวุฒิ)") $NEW_CODE = "4010000";
			elseif ($ELVL_NAME=="ปริญญาโท" || $ELVL_NAME=="ปริญญาโท (ไม่ทราบชื่อวุฒิ)") $NEW_CODE = "6010000";
			elseif ($ELVL_NAME=="ปริญญาเอก" || $ELVL_NAME=="ปริญญาเอก (ไม่ทราบชื่อวุฒิ)") $NEW_CODE = "8010000";
			elseif ($ELVL_NAME=="วุฒิต่ำกว่าประกาศนียบัตรวิชาชีพ") $NEW_CODE = "0510000";
			elseif ($ELVL_NAME=="ศิลปศาสตร์,อนุปริญญา(อ.ศศ.)") $NEW_CODE = "2010080";
			elseif ($ELVL_NAME=="เศรษฐศาสตรมหาบัณฑิต (หลักสูตรภาษาไทย),ปริญญา") $NEW_CODE = "6010104";
			elseif ($ELVL_NAME=="ประกาศนียบัตรวิชาการศึกษา (ป.กศ.)") $NEW_CODE = "0510050";
			elseif ($ELVL_NAME=="เศรษฐศาสตร์สหกรณ์บัณฑิต (ป.)") $NEW_CODE = "4010150";

			$cmd = " select EN_CODE from PER_EDUCNAME where EN_NAME = '$ELVL_NAME' OR 
							EN_NAME = 'ป.$ELVL_NAME' OR (EN_NAME = 'ป.$ELVL_NAME1' AND '$ELVL_NAME2' = 'ปริญญา') OR (EN_NAME = 'อนุป.$ELVL_NAME1' AND '$ELVL_NAME2' = 'อนุปริญญา') OR (EN_NAME = 'ปบ.$ELVL_NAME1' AND '$ELVL_NAME2' = '(ป.)')  ";
			$count_data = $db_dpis2->send_cmd($cmd);
			echo "$cmd<br>";

			if ($count_data || $NEW_CODE) {			
				$data2 = $db_dpis2->get_array();
				if (!$NEW_CODE) $NEW_CODE = trim($data2[EN_CODE]);
				$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
								VALUES ('PER_EDUCNAME', '$ELVL_ID', '$NEW_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			} else {
				
				$cmd = " INSERT INTO PER_EDUCNAME (EN_CODE, EL_CODE, EN_NAME, EN_ACTIVE, UPDATE_USER, UPDATE_DATE)
								VALUES ('$ELVL_ID', '99', '$ELVL_NAME', 1, $UPDATE_USER, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			}
		} // end while						

// ทุน
		$cmd = " DELETE FROM PER_TRAIN_PROJECT_PERSONAL WHERE UPDATE_USER = $UPDATE_USER ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " SELECT SCHOLAR_ID, SCHOLAR_YEAR, SCHOLAR_NAME, SCHOLAR_TYPE, SCHOLAR_SOURCE_ID, 
						SCHOLAR_COURSE_ID, SCHOLAR_NO, SCHOLAR_BRANCH_ID, TO_CHAR(SCHOLAR_STT,'YYYY-MM-DD') AS SCHOLAR_STT, 
						TO_CHAR(SCHOLAR_END,'YYYY-MM-DD') AS SCHOLAR_END, SCHOLAR_PLACE, SCHOLAR_CNT_GO, 
						SCHOLAR_CNT_PROD, ELVL_ID, BUDGET, APPROVALID, TO_CHAR(APP_ISS_DATE,'YYYY-MM-DD') AS APP_ISS_DATE, 
						APPROVAL_NAME, REMARKS, TO_CHAR(APPROVE_DATE,'YYYY-MM-DD') AS APPROVE_DATE, DOM_INT            
						FROM T_SCHOLAR
						ORDER BY SCHOLAR_ID ";
		$db_dpis35->send_cmd($cmd);
		$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$SCHOLAR_ID = $data[SCHOLAR_ID] + 0;
			$SCHOLAR_YEAR = trim($data[SCHOLAR_YEAR]);
			$SCHOLAR_NAME = trim($data[SCHOLAR_NAME]);
			$SCHOLAR_TYPE = $data[SCHOLAR_TYPE] + 0;
			if ($SCHOLAR_TYPE==7) $SCHOLAR_TYPE = 1;
			else $SCHOLAR_TYPE = 0;
			$SCHOLAR_SOURCE_ID = $data[SCHOLAR_SOURCE_ID] + 0;
			$SCHOLAR_COURSE_ID = $data[SCHOLAR_COURSE_ID] + 0;
			$SCHOLAR_NO = trim($data[SCHOLAR_NO]);
			$SCHOLAR_BRANCH_ID = $data[SCHOLAR_BRANCH_ID] + 0;
			$SCHOLAR_STT = trim($data[SCHOLAR_STT]);
			$SCHOLAR_END = trim($data[SCHOLAR_END]);
			$SCHOLAR_PLACE = trim($data[SCHOLAR_PLACE]);
			$SCHOLAR_CNT_GO = $data[SCHOLAR_CNT_GO] + 0;
			$SCHOLAR_CNT_PROD = $data[SCHOLAR_CNT_PROD] + 0;
			$ELVL_ID = $data[ELVL_ID] + 0;
			$BUDGET = $data[BUDGET] + 0;
			$APPROVALID = trim($data[APPROVALID]);
			$APP_ISS_DATE = trim($data[APP_ISS_DATE]);
			$APPROVAL_NAME = trim($data[APPROVAL_NAME]);
			$REMARKS = trim($data[REMARKS]);
			$APPROVE_DATE = trim($data[APPROVE_DATE]);
			$DOM_INT = trim($data[DOM_INT]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_EDUCNAME' AND OLD_CODE = $ELVL_ID ";
			$count_data = $db_dpis2->send_cmd($cmd);
			if ($count_data) {
				$data2 = $db_dpis2->get_array();
				$ELVL_ID = $data2[NEW_CODE] + 0;
			}

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_EDUCMAJOR' AND OLD_CODE = 
							$SCHOLAR_BRANCH_ID ";
			$count_data = $db_dpis2->send_cmd($cmd);
			if ($count_data) {
				$data2 = $db_dpis2->get_array();
				$SCHOLAR_BRANCH_ID = $data2[NEW_CODE] + 0;
			}

			$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_YEAR, SCH_TYPE, 
							SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN, CT_CODE_GO, 
							SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_ACTIVE, 
							UPDATE_USER, UPDATE_DATE)
							VALUES ('$SCHOLAR_ID', '$SCHOLAR_NAME', '$SCHOLAR_SOURCE_ID', NULL, '$SCHOLAR_YEAR', '$SCHOLAR_TYPE', 
							'$SCHOLAR_NO', '$ELVL_ID', '$SCHOLAR_BRANCH_ID', '$SCHOLAR_STT', '$SCHOLAR_END', '$SCHOLAR_PLACE', 
							'$SCHOLAR_CNT_PROD', '$SCHOLAR_CNT_GO', $BUDGET, '$APPROVALID', '$APP_ISS_DATE', '$APPROVE_DATE', 
							NULL, '$REMARKS', 1, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			$db_dpis->show_error();
		} // end while						

// ขยายระยะเวลาการศึกษา
		$cmd = " DELETE FROM PER_SCHOLARINC WHERE UPDATE_USER = $UPDATE_USER ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " SELECT EXT_ID, SCHOLAR_ID, PERS_CODE, TO_CHAR(EXT_START,'YYYY-MM-DD') AS EXT_START, 
						TO_CHAR(EXT_END,'YYYY-MM-DD') AS EXT_END FROM T_SCHOLAR_EXT ORDER BY EXT_ID ";
		$db_dpis35->send_cmd($cmd);
		$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$EXT_ID = $data[EXT_ID] + 0;
			$SCHOLAR_ID = $data[SCHOLAR_ID] + 0;
			$PERS_CODE = $data[PERS_CODE] + 0;
			$EXT_START = trim($data[EXT_START]);
			$EXT_END = trim($data[EXT_END]);

			$cmd = " INSERT INTO PER_SCHOLARINC (SC_ID, SCI_BEGINDATE, SC_ENDDATE, UPDATE_USER, UPDATE_DATE)
							VALUES ($EXT_ID, '$EXT_START', '$EXT_END', $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
		} // end while						

// ผู้ได้รับทุน
		$cmd = " DELETE FROM PER_SCHOLAR WHERE UPDATE_USER = $UPDATE_USER ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " SELECT REF_ID, a.SCHOLAR_ID, PERS_CODE, TO_CHAR(START_DUE,'YYYY-MM-DD') AS START_DUE, 
						TO_CHAR(END_DUE,'YYYY-MM-DD') AS END_DUE, NUM_OF_YEAR,	CONTRACT_NO, 
						TO_CHAR(START_SCHOLAR,'YYYY-MM-DD') AS START_SCHOLAR, TO_CHAR(END_SCHOLAR,'YYYY-MM-DD') AS 
						END_SCHOLAR, TO_CHAR(PLACE_DATE,'YYYY-MM-DD') AS PLACE_DATE, TO_CHAR(LEAVE_DATE,'YYYY-MM-DD') AS 
						LEAVE_DATE, TO_CHAR(COMEBACK_DATE,'YYYY-MM-DD') AS COMEBACK_DATE, PERS_GPA, a.REMARKS, LEAVEFLAG, 
						ACTIVE, TO_CHAR(a.SCHOLAR_STT,'YYYY-MM-DD') AS SCHOLAR_STT, TO_CHAR(a.SCHOLAR_END,'YYYY-MM-DD') AS 
						SCHOLAR_END, a.SCHOLAR_PLACE, a.SCHOLAR_CNT_GO, a.SCHOLAR_CNT_PROD, a.BUDGET, a.APPROVALID, 
						TO_CHAR(a.APP_ISS_DATE,'YYYY-MM-DD') AS APP_ISS_DATE, a.APPROVAL_NAME, 
						TO_CHAR(a.APPROVE_DATE,'YYYY-MM-DD') AS APPROVE_DATE, TO_CHAR(REPORT_DATE,'YYYY-MM-DD') AS 
						REPORT_DATE, TO_CHAR(GRAD_DATE,'YYYY-MM-DD') AS GRAD_DATE, b.ELVL_ID, b.SCHOLAR_BRANCH_ID            
						FROM T_SCHOLAR_REF a, T_SCHOLAR b
						WHERE a.SCHOLAR_ID = b.SCHOLAR_ID(+)
						ORDER BY REF_ID, a.SCHOLAR_ID, PERS_CODE ";
		$db_dpis35->send_cmd($cmd);
		$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$REF_ID = $data[REF_ID] + 100;
			$SCHOLAR_ID = $data[SCHOLAR_ID] + 0;
			$PERS_CODE = $data[PERS_CODE] + 0;
			$START_DUE = trim($data[START_DUE]);
			$END_DUE = trim($data[END_DUE]);
			$NUM_OF_YEAR = $data[NUM_OF_YEAR] + 0;
			$CONTRACT_NO = trim($data[CONTRACT_NO]);
			$START_SCHOLAR = trim($data[START_SCHOLAR]);
			$END_SCHOLAR = trim($data[END_SCHOLAR]);
			$PLACE_DATE = trim($data[PLACE_DATE]);
			$LEAVE_DATE = trim($data[LEAVE_DATE]);
			$COMEBACK_DATE = trim($data[COMEBACK_DATE]);
			$PERS_GPA = trim($data[PERS_GPA]);
			$REMARKS = trim($data[REMARKS]);
			$LEAVEFLAG = trim($data[LEAVEFLAG]);
			$ACTIVE = trim($data[ACTIVE]);
			$SCHOLAR_STT = trim($data[SCHOLAR_STT]);
			$SCHOLAR_END = trim($data[SCHOLAR_END]);
			$SCHOLAR_PLACE = trim($data[SCHOLAR_PLACE]);
			$SCHOLAR_CNT_GO = trim($data[SCHOLAR_CNT_GO]);
			$SCHOLAR_CNT_PROD = trim($data[SCHOLAR_CNT_PROD]);
			$BUDGET = $data[BUDGET] + 0;
			$APPROVALID = trim($data[APPROVALID]);
			$APP_ISS_DATE = trim($data[APP_ISS_DATE]);
			$APPROVAL_NAME = trim($data[APPROVAL_NAME]);
			$APPROVE_DATE = trim($data[APPROVE_DATE]);
			$REPORT_DATE = trim($data[REPORT_DATE]);
			$GRAD_DATE = trim($data[GRAD_DATE]);
			$EL_CODE = "99";
			$INS_CODE = "1409999";
			if (!$START_DUE) $START_DUE = "-";
			if (!$END_DUE) $END_DUE = "-";

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_EDUCNAME' AND OLD_CODE = $ELVL_ID ";
			$count_data = $db_dpis2->send_cmd($cmd);
			if ($count_data) {
				$data2 = $db_dpis2->get_array();
				$ELVL_ID = $data2[NEW_CODE] + 0;
			}

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_EDUCMAJOR' AND OLD_CODE = 
							$SCHOLAR_BRANCH_ID ";
			$count_data = $db_dpis2->send_cmd($cmd);
			if ($count_data) {
				$data2 = $db_dpis2->get_array();
				$SCHOLAR_BRANCH_ID = $data2[NEW_CODE] + 0;
			}

			$cmd = " INSERT INTO PER_SCHOLAR (SC_ID, SC_TYPE, PER_ID, SC_CARDNO, PN_CODE, SC_NAME, SC_SURNAME, 
							SC_BIRTHDATE, SC_GENDER, SC_ADD1, EN_CODE, EM_CODE, SCH_CODE, INS_CODE, EL_CODE, SC_STARTDATE, 
							SC_ENDDATE, SC_FINISHDATE, SC_BACKDATE, UPDATE_USER, UPDATE_DATE, PER_CARDNO, DEPARTMENT_ID)
							VALUES ($REF_ID, 1, $PERS_CODE, '$SC_CARDNO', '$PN_CODE', '$SC_NAME', '$SC_SURNAME', '$SC_BIRTHDATE', 
							'$SC_GENDER', '$SC_ADD1', '$ELVL_ID', '$SCHOLAR_BRANCH_ID', '$SCHOLAR_ID', '$INS_CODE', '$EL_CODE', 
							'$START_DUE', '$END_DUE', '$SC_FINISHDATE', '$COMEBACK_DATE', $UPDATE_USER, '$UPDATE_DATE', '$PER_CARDNO', 
							$DEPARTMENT_ID) ";
			$db_dpis->send_cmd($cmd);
			$db_dpis->show_error();
		} // end while						

	} // end if						
?>