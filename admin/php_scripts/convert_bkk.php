<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

// เลขที่ตำแหน่งครอง 2 คน select pos_id, count(*) from per_personal where per_type=1 and per_status=1 group by pos_id having count(*) > 1

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_att1 = new connect_att($attdb_host, $attdb_name, $attdb_user, $attdb_pwd);
	$UPDATE_DATE = date("Y-m-d H:i:s");
	$UPDATE_USER = 99999;
	$PFR_YEAR = "2556";

	if( $command=='PERFORMANCE_REVIEW' ){
// ยุทธศาสตร์ 
		$cmd = " DELETE FROM PER_PERFORMANCE_REVIEW WHERE PFR_YEAR = '$PFR_YEAR' AND UPDATE_USER = 99999 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " SELECT POLICY_ID, POLICY, POLICY_YEAR 
						  FROM VIEW_STRATEGY
						  ORDER BY POLICY_ID ";
		$db_att->send_cmd($cmd);
//						  FROM TBL_POLICY_MAIN
//		$db_att->show_error();
//		echo "<br>";
		while($data = $db_att->get_array()){
			$PER_PERFORMANCE_REVIEW1++;
			$POLICY_ID = trim($data[POLICY_ID]);
			$POLICY = trim($data[POLICY]);
			$POLICY_YEAR = trim($data[POLICY_YEAR]);

			$cmd = " INSERT INTO PER_PERFORMANCE_REVIEW(PFR_ID, PFR_NAME, PFR_YEAR, DEPARTMENT_ID, PFR_TYPE, UPDATE_USER, UPDATE_DATE)
							 VALUES ($POLICY_ID, '$POLICY', '$POLICY_YEAR', 0, 1, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
//			echo "$cmd<br>==================<br>";
//			$db_dpis->show_error();
//			echo "<br>end ". ++$i  ."=======================<br>";
		} // end while						

		$cmd = " select count(PFR_ID) as COUNT_NEW from PER_PERFORMANCE_REVIEW WHERE PFR_YEAR = '$PFR_YEAR' AND PFR_TYPE = 1 ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_PERFORMANCE_REVIEW - $PER_PERFORMANCE_REVIEW1 - $COUNT_NEW<br>";
		
		$cmd = " SELECT BRANCH_ID, BRANCH, BRANCH_DETAIL, POLICY_ID, POLICY_YEAR 
						  FROM VIEW_STRATEGY2
						  ORDER BY BRANCH_ID ";
		$db_att->send_cmd($cmd);
//						  FROM TBL_POLICY_MAIN1
//		$db_att->show_error();
//		echo "<br>";
		while($data = $db_att->get_array()){
			$PER_PERFORMANCE_REVIEW2++;
			$BRANCH_ID = trim($data[BRANCH_ID]);
			$BRANCH = trim($data[BRANCH]);
			$BRANCH_DETAIL = trim($data[BRANCH_DETAIL]);
			$POLICY_ID = trim($data[POLICY_ID]);
			$POLICY_YEAR = trim($data[POLICY_YEAR]);

			$cmd = " INSERT INTO PER_PERFORMANCE_REVIEW(PFR_ID, PFR_NAME, PFR_YEAR, PFR_ID_REF, 
							 DEPARTMENT_ID, PFR_TYPE, UPDATE_USER, UPDATE_DATE)
							 VALUES ($BRANCH_ID, '$BRANCH', '$POLICY_YEAR', $POLICY_ID, 0, 2, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
		$db_dpis->send_cmd($cmd);
//			echo "$cmd<br>==================<br>";
//			$db_dpis->show_error();
//			echo "<br>end ". ++$i  ."=======================<br>";
		} // end while						

		$cmd = " select count(PFR_ID) as COUNT_NEW from PER_PERFORMANCE_REVIEW WHERE PFR_YEAR = '$PFR_YEAR' AND PFR_TYPE = 2 ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_PERFORMANCE_REVIEW - $PER_PERFORMANCE_REVIEW2 - $COUNT_NEW<br>";
		
		$cmd = " SELECT STRATEGY_ID, STRATEGY, STRATEGY_DETAIL, BRANCH_ID
						  FROM TBL_POLICY_MAIN2
						  ORDER BY STRATEGY_ID ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		while($data = $db_att->get_array()){
			$PER_PERFORMANCE_REVIEW3++;
			$STRATEGY_ID = trim($data[STRATEGY_ID]);
			$STRATEGY = trim($data[STRATEGY]);
			$STRATEGY_DETAIL = trim($data[STRATEGY_DETAIL]);
			$BRANCH_ID = trim($data[BRANCH_ID]);

			$cmd = " INSERT INTO PER_PERFORMANCE_REVIEW(PFR_ID, PFR_NAME, PFR_YEAR, PFR_ID_REF, 
							 DEPARTMENT_ID, PFR_TYPE, UPDATE_USER, UPDATE_DATE)
							 VALUES ($STRATEGY_ID, '$STRATEGY', '$PFR_YEAR', $BRANCH_ID, 0, 3, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
//			echo "$cmd<br>==================<br>";
//			$db_dpis->show_error();
//			echo "<br>end ". ++$i  ."=======================<br>";
		} // end while						

		$cmd = " select count(PFR_ID) as COUNT_NEW from PER_PERFORMANCE_REVIEW WHERE PFR_YEAR = '$PFR_YEAR' AND PFR_TYPE = 3 ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_PERFORMANCE_REVIEW - $PER_PERFORMANCE_REVIEW3 - $COUNT_NEW<br>";
		
		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_att->free_result();
		$db_att1->free_result();
	} // end if

	if( $command=='KPI' ){
// ตัวชี้วัด 
		$cmd = " DELETE FROM PER_KPI WHERE KPI_YEAR = '$PFR_YEAR' AND UPDATE_USER = 99999 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

//		$cmd = " SELECT KPI_ID, a.DEPART_ID, SUBDEPART_ID, KPI_YEAR, KPI_NAME, KPI_TYPE, KPI_LEVEL, KEY_SUCCESS, POLICY_ID,
//						  BRANCH_ID, STRATEGY_ID, UNIT_COUNT, TARGET_NUM, REPORT_NUM, KPI_DEFINE, CACULATION, CONFIRM_SCORE, 
//						  REPORT_DATE, b.DEPART_ID2, b.DEPARTMENT
//						  FROM TBL_KPI_MAIN a, TBL_DEPARTMENT_CODE b
//						  WHERE a.DEPART_ID=b.DEPART_ID(+)
//						  ORDER BY KPI_ID ";
//		$cmd = " SELECT KPI_ID, DEPART_ID, SUBDEPART_ID, KPI_YEAR, KPI_NAME, MITI_NUM, KPI_TYPE, KPI_LEVEL, KEY_SUCCESS, POLICY_ID,
//						  BRANCH_ID, STRATEGY_ID, UNIT_COUNT, TARGET_NUM, REPORT_NUM, KPI_DEFINE, CACULATION, KPI_MEMO, PROJECT_MEMO, 
//						  TARGET_MEMO, BYDEPARTMENT, USE_CALCULATE
//						  FROM TBL_KPI_MAIN
//						  ORDER BY KPI_ID ";
		$cmd = " SELECT KPI_ID, KPI_NAME, KPI_YEAR, DEPART_ID, SUBDEPART_ID, MITI_NUM, KPI_TYPE, KPI_LEVEL, KEY_SUCCESS, POLICY_ID,
						  BRANCH_ID, STRATEGY_ID, UNIT_COUNT, TARGET_NUM, REPORT_NUM, KPI_DEFINE, CACULATION, KPI_MEMO, PROJECT_MEMO, 
						  TARGET_MEMO, BYDEPARTMENT, USE_CALCULATE
						  FROM VIEW_KPI
						  ORDER BY KPI_ID ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		while($data = $db_att->get_array()){
			$PER_KPI++;
			$KPI_ID = trim($data[KPI_ID]);
			$KPI_ID = str_replace("-", "", $KPI_ID);
			$DEPART_ID = trim($data[DEPART_ID]);
			$SUBDEPART_ID = trim($data[SUBDEPART_ID]);
			$KPI_YEAR = trim($data[KPI_YEAR]);
			$KPI_NAME = trim($data[KPI_NAME]);
			$MITI_NUM = trim($data[MITI_NUM]);
			$KPI_TYPE = trim($data[KPI_TYPE]);
			$KPI_LEVEL = trim($data[KPI_LEVEL]);
			$KEY_SUCCESS = trim($data[KEY_SUCCESS]);
			$POLICY_ID = trim($data[POLICY_ID]);
			$BRANCH_ID = trim($data[BRANCH_ID]);
			$STRATEGY_ID = trim($data[STRATEGY_ID]);
			$UNIT_COUNT = trim($data[UNIT_COUNT]);
			$TARGET_NUM = $data[TARGET_NUM]+0;
			$REPORT_NUM = trim($data[REPORT_NUM]);
			$KPI_DEFINE = trim($data[KPI_DEFINE]);
			if (!($KPI_DEFINE) || $KPI_DEFINE=="-") $KPI_DEFINE = trim($data[CACULATION]);
			$KPI_MEMO = trim($data[KPI_MEMO]);
			$PROJECT_MEMO = trim($data[PROJECT_MEMO]);
			$TARGET_MEMO = trim($data[TARGET_MEMO]);
			$BYDEPARTMENT = trim($data[BYDEPARTMENT]);
			$USE_CALCULATE = $data[USE_CALCULATE]+0;
			$CONFIRM_SCORE = trim($data[CONFIRM_SCORE]);
			$REPORT_DATE = trim($data[REPORT_DATE]);
//			$DEPART_ID2 = substr($data[DEPART_ID2],0,2);
//			$DEPARTMENT = trim($data[DEPARTMENT]);
			$KPI_NAME = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($KPI_NAME)));
			$KPI_DEFINE = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($KPI_DEFINE)));
			if (strlen($KPI_DEFINE) > 4000) $KPI_DEFINE = substr($KPI_DEFINE,0,4000);

			if ($STRATEGY_ID) {
				$cmd = " select PFR_ID from PER_PERFORMANCE_REVIEW where PFR_ID = $STRATEGY_ID ";
				$db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$PFR_ID = $data1[PFR_ID];
			} 
			if (!$PFR_ID && $BRANCH_ID) {
				$cmd = " select PFR_ID from PER_PERFORMANCE_REVIEW where PFR_ID = $BRANCH_ID ";
				$db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$PFR_ID = $data1[PFR_ID];
			} 
			if (!$PFR_ID && $POLICY_ID) {
				$cmd = " select PFR_ID from PER_PERFORMANCE_REVIEW where PFR_ID = $POLICY_ID ";
				$db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$PFR_ID = $data1[PFR_ID];
			}

			$ORG_CODE1 = substr($DEPART_ID,0,2);
			$ORG_CODE2 = substr($DEPART_ID,0,4);
			if (substr($DEPART_ID,2,2)=="00" || $ORG_CODE1=="04") 
				$cmd = " select ORG_ID from PER_ORG where ORG_CODE = '$ORG_CODE1' ";
			else
				$cmd = " select ORG_ID from PER_ORG where ORG_CODE = '$ORG_CODE2' ";
			$db_dpis1->send_cmd($cmd);
			$data1 = $db_dpis1->get_array();
			$DEPT_ID = $data1[ORG_ID];
			if (!$DEPT_ID) echo "หน่วยงาน - $cmd<br>";

			if ($SUBDEPART_ID=="00000000") {
				$ORG_ID = "NULL";
			} else {
				$ORG_CODE = substr($SUBDEPART_ID,0,4);
				$cmd = " select ORG_ID, ORG_NAME, ORG_ID_REF from PER_ORG where ORG_CODE = '$ORG_CODE' and OL_CODE = '03' ";
				$db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$ORG_ID = $data1[ORG_ID];
				if (!$ORG_ID) $ORG_ID = "NULL";
				$ORG_NAME = $data1[ORG_NAME];
				$ORG_ID_REF = $data1[ORG_ID_REF];
				if ($ORG_NAME=="-") $ORG_ID = 0;
				if (!$ORG_NAME && substr($ORG_CODE,0,2)!="50") echo "ส่วนราชการ - $cmd<br>";

				if (!$DEPT_ID) {
					$cmd = " select ORG_ID from PER_ORG where ORG_ID = $ORG_ID_REF ";
					$db_dpis1->send_cmd($cmd);
					$data1 = $db_dpis1->get_array();
					$DEPT_ID = $data1[ORG_ID];
					if (!$DEPT_ID) echo "หน่วยงาน - $cmd<br>";
				}
			}

			$cmd = " INSERT INTO PER_KPI(KPI_ID, KPI_NAME, KPI_YEAR, KPI_WEIGHT, KPI_MEASURE, KPI_PER_ID, PFR_ID, 
							  DEPARTMENT_ID, ORG_ID, ORG_NAME, UNDER_ORG_NAME1, KPI_DEFINE, KPI_TYPE, UPDATE_USER, UPDATE_DATE)
							 VALUES ($KPI_ID, '$KPI_NAME', '$KPI_YEAR', $TARGET_NUM, '$UNIT_COUNT', 1, $PFR_ID, $DEPT_ID, $ORG_ID, 
							  '$ORG_NAME', '$UNDER_ORG_NAME1', '$KPI_DEFINE', '$KPI_TYPE', $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);

			$cmd = " select KPI_ID from PER_KPI where KPI_ID = $KPI_ID ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$CHECK_KPI_ID = $data2[KPI_ID]+0;
			if ($CHECK_KPI_ID!=$KPI_ID) {
				echo "$cmd<br>==================<br>";
				$db_dpis->show_error();
				echo "<br>end ". ++$i  ."=======================<br>";
			}
		} // end while						

		$cmd = " select count(KPI_ID) as COUNT_NEW from PER_KPI WHERE KPI_YEAR = '$PFR_YEAR' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_KPI - $PER_KPI - $COUNT_NEW<br>";
		
		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_att->free_result();
		$db_att1->free_result();
	} // end if

	if( $command=='PROJECT' ){
// โครงการ 
		$cmd = " DELETE FROM PER_PROJECT WHERE PJ_YEAR = '$PFR_YEAR' AND UPDATE_USER = 99999 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " select max(PJ_ID) as MAX_ID from PER_PROJECT ";
		$db_dpis1->send_cmd($cmd);
		$data1 = $db_dpis1->get_array();
		$MAX_ID = $data1[MAX_ID] + 1;

//		$cmd = " SELECT PROJECT_ID, PROJECT_YEAR, PROJECT_NAME, POLICY_ID, BRANCH_ID, STRATEGY_ID, KPI_ID, 
//						  BUDGET_AMT, BUDGET_PAY, EVALUATION, TARGET_POINT, a.DEPART_ID, a.SUBDEPART_ID, START_DATE, 
//						  FINISH_DATE, OBJECTIVE_NOTE, TARGET_NOTE,  CONTACT_PERSON, b.DEPART_ID2, b.DEPARTMENT, c.SUBDEPARTMENT
//						  FROM TBL_PROJECT_MAIN a, TBL_DEPARTMENT_CODE b, TBL_DEPARTMENT_DETAIL c
//						  WHERE a.DEPART_ID=b.DEPART_ID AND a.SUBDEPART_ID=c.SUBDEPART_ID
//						  ORDER BY PROJECT_ID ";
		$cmd = " SELECT PROJECT_ID, PROJECT_YEAR, PROJECT_NAME, POLICY_ID, BRANCH_ID, STRATEGY_ID, KPI_ID, 
						  BUDGET_AMT, BUDGET_PAY, EVALUATION, TARGET_POINT, a.DEPART_ID, a.SUBDEPART_ID, START_DATE, 
						  FINISH_DATE, OBJECTIVE_NOTE, TARGET_NOTE,  CONTACT_PERSON, b.DEPART_ID2, b.DEPARTMENT, c.SUBDEPARTMENT
						  FROM VIEW_PROJECT a, VIEW_DEPARTMENT b, VIEW_DEPARTMENT2 c
						  WHERE a.DEPART_ID=b.DEPART_ID AND a.SUBDEPART_ID=c.SUBDEPART_ID
						  ORDER BY PROJECT_ID ";
		$db_att->send_cmd($cmd);
//						  PROJECT_TYPE, PROJECT_CLASS, PROJECT_STATUS, REPORT_STATUS, TARGET_STATUS, TELEPHONE_NO, 
//		$db_att->show_error();
//		echo "<br>";
		while($data = $db_att->get_array()){
			$PER_PROJECT++;
			$PROJECT_ID = trim($data[PROJECT_ID]);
			$PROJECT_ID = str_replace("0000-", "", $PROJECT_ID);
			$PROJECT_YEAR = trim($data[PROJECT_YEAR]);
			$PROJECT_NAME = trim($data[PROJECT_NAME]);
			$KPI_ID = trim($data[KPI_ID]);
			$KPI_ID = str_replace("-", "", $KPI_ID);
			$POLICY_ID = trim($data[POLICY_ID]);
			$BRANCH_ID = trim($data[BRANCH_ID]);
			$STRATEGY_ID = trim($data[STRATEGY_ID]);
			$BUDGET_AMT = trim($data[BUDGET_AMT]);
			$BUDGET_PAY = trim($data[BUDGET_PAY]);
			$PROJECT_TYPE = trim($data[PROJECT_TYPE]);
			$PROJECT_CLASS = trim($data[PROJECT_CLASS]);
			$PROJECT_STATUS = trim($data[PROJECT_STATUS]);
			$EVALUATION = trim($data[EVALUATION]);
			$REPORT_STATUS = trim($data[REPORT_STATUS]);
			$TARGET_STATUS = trim($data[TARGET_STATUS]);
			$DEPART_ID = trim($data[DEPART_ID]);
			$SUBDEPART_ID = trim($data[SUBDEPART_ID]);
			$START_DATE = trim($data[START_DATE]);
			$FINISH_DATE = trim($data[FINISH_DATE]);
			$OBJECTIVE_NOTE = trim($data[OBJECTIVE_NOTE]);
			$TARGET_NOTE = trim($data[TARGET_NOTE]);
			$CONTACT_PERSON = trim($data[CONTACT_PERSON]);
			$TELEPHONE_NO = trim($data[TELEPHONE_NO]);
			$DEPART_ID2 = substr($data[DEPART_ID2],0,2);
			$DEPARTMENT = trim($data[DEPARTMENT]);
			$SUBDEPARTMENT = trim($data[SUBDEPARTMENT]);
			$OBJECTIVE_NOTE = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($OBJECTIVE_NOTE)));
			$TARGET_NOTE = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($TARGET_NOTE)));
			if (strlen($TARGET_NOTE) > 4000) $TARGET_NOTE = substr($TARGET_NOTE,0,4000);

			$cmd = " select ORG_ID from PER_ORG where ORG_CODE = '$DEPART_ID2' ";
			$db_dpis1->send_cmd($cmd);
			$data1 = $db_dpis1->get_array();
			$DEPT_ID = $data1[ORG_ID];
			if (!$DEPT_ID) {
				$cmd = " select ORG_ID from PER_ORG where ORG_NAME = '$DEPARTMENT' ";
				$db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$DEPT_ID = $data1[ORG_ID];
				if (!$DEPT_ID) echo "หน่วยงาน - $cmd<br>";
			}

			$cmd = " select ORG_ID, ORG_NAME from PER_ORG where ORG_CODE = '$SUBDEPART_ID' ";
			$db_dpis1->send_cmd($cmd);
			$data1 = $db_dpis1->get_array();
			$ORG_ID = $data1[ORG_ID];
			$ORG_NAME = $data1[ORG_NAME];
			if (!$ORG_ID) {
				$cmd = " select ORG_ID, ORG_NAME from PER_ORG where ORG_NAME = '$SUBDEPARTMENT' ";
				$db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$ORG_ID = $data1[ORG_ID];
				$ORG_NAME = $data1[ORG_NAME];
				if (!$ORG_ID) echo "ส่วนราชการ - $cmd<br>";
			}
			if (!$ORG_ID) $ORG_ID = "NULL";

			if ($STRATEGY_ID) {
				$cmd = " select PFR_ID from PER_PERFORMANCE_REVIEW where PFR_ID = $STRATEGY_ID ";
				$db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$PFR_ID = $data1[PFR_ID];
			} 
			if (!$PFR_ID && $BRANCH_ID) {
				$cmd = " select PFR_ID from PER_PERFORMANCE_REVIEW where PFR_ID = $BRANCH_ID ";
				$db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$PFR_ID = $data1[PFR_ID];
			} 
			if (!$PFR_ID && $POLICY_ID) {
				$cmd = " select PFR_ID from PER_PERFORMANCE_REVIEW where PFR_ID = $POLICY_ID ";
				$db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$PFR_ID = $data1[PFR_ID];
			}

			$cmd = " INSERT INTO PER_PROJECT(PJ_ID, PJ_NAME, PJ_YEAR, KPI_ID, PFR_ID, PJ_TYPE, PJ_CLASS, 
							  PJ_STATUS, PJ_EVALUATION, PJ_REPORT_STATUS, PJ_TARGET_STATUS, DEPARTMENT_ID, 
							  ORG_ID, START_DATE, END_DATE, PJ_OBJECTIVE, PJ_TARGET, PJ_ID_REF, PJ_BUDGET_RECEIVE, 
							  PJ_BUDGET_USED, UPDATE_USER, UPDATE_DATE)
							  VALUES ($MAX_ID, '$PROJECT_NAME', '$PROJECT_YEAR', $KPI_ID, $PFR_ID, '$PROJECT_TYPE', 
							  '$PROJECT_CLASS', '$PROJECT_STATUS', '$EVALUATION', '$REPORT_STATUS', '$TARGET_STATUS', $DEPT_ID, 
							  $ORG_ID, '$START_DATE', '$FINISH_DATE', '$OBJECTIVE_NOTE', '$TARGET_NOTE', NULL, $BUDGET_AMT, 
							  $BUDGET_PAY, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);

			$cmd = " select PJ_ID from PER_PROJECT where PJ_ID = $MAX_ID ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$CHECK_PJ_ID = $data2[PJ_ID]+0;
			if ($CHECK_PJ_ID!=$MAX_ID) {
				echo "$cmd<br>==================<br>";
				$db_dpis->show_error();
				echo "<br>end ". ++$i  ."=======================<br>";
			}
			$MAX_ID++;
		} // end while						

		$cmd = " select count(KPI_ID) as COUNT_NEW from PER_PROJECT WHERE PJ_YEAR = '$PFR_YEAR' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_PROJECT - $PER_PROJECT - $COUNT_NEW<br>";
		
		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_att->free_result();
		$db_att1->free_result();
	} // end if

	if( $command=='COUNTRY' ){
		$cmd = " DELETE FROM PER_ORG ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_INSTITUTE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

// ตำบล 7494
		$cmd = " DELETE FROM PER_DISTRICT ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

// อำเภอ 963 ขาด 1
		$cmd = " DELETE FROM PER_AMPHUR ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

// จังหวัด 77
		$cmd = " DELETE FROM PER_PROVINCE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

// ประเทศ 188
		$cmd = " DELETE FROM PER_COUNTRY ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " SELECT COUNTRY_CODE, COUNTRY_NAME, COUNTRY_NAME_E, COUNTRY_ABB_NAME, 
						  USER_CREATE, to_char(CREATE_DATE,'yyyy-mm-dd') as CREATE_DATE, 
						  USER_UPDATE, to_char(UPDATE_DATE,'yyyy-mm-dd') as UPDATE_DATE 
						  FROM HR_COUNTRY_CODE
						  ORDER BY COUNTRY_CODE ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		$SEQ_NO = 1;
		while($data = $db_att->get_array()){
			$PER_COUNTRY++;
			$COUNTRY_CODE = trim($data[COUNTRY_CODE]);
			$COUNTRY_NAME = trim($data[COUNTRY_NAME]);
			$COUNTRY_NAME_E = trim($data[COUNTRY_NAME_E]);
			$COUNTRY_ABB_NAME = trim($data[COUNTRY_ABB_NAME]);

			$UPDATE_USER = 99999;
			if ($data[USER_UPDATE]) $USER_UPDATE = trim($data[USER_UPDATE]);
			else $USER_UPDATE = trim($data[USER_CREATE]);
			if ($data[UPDATE_DATE]) $UPDATE_DATE = trim($data[UPDATE_DATE]);
			else  $UPDATE_DATE = trim($data[CREATE_DATE]);
			if ($USER_UPDATE) {
				$cmd = " select ID from USER_DETAIL where USERNAME = '$USER_UPDATE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				if ($data2[ID]) $UPDATE_USER = $data2[ID];
			}
			if (!$UPDATE_DATE) $UPDATE_DATE = date("Y-m-d H:i:s");

			$cmd = " INSERT INTO PER_COUNTRY(CT_CODE, CT_NAME, CT_ACTIVE, CT_SEQ_NO, UPDATE_USER, UPDATE_DATE)
							 VALUES ('$COUNTRY_CODE', '$COUNTRY_NAME', 1, $SEQ_NO, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
//			echo "$cmd<br>==================<br>";
//			$db_dpis->show_error();
//			echo "<br>end ". ++$i  ."=======================<br>";
			$SEQ_NO++;
		} // end while						

		$cmd = " select count(CT_CODE) as COUNT_NEW from PER_COUNTRY ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_COUNTRY - $PER_COUNTRY - $COUNT_NEW<br>";
		
		$cmd = " SELECT PROVINCE_CODE, PROVINCE_NAME, PROVINCE_NAME_E, PROVINCE_ABB_NAME, 
						  USER_CREATE, to_char(CREATE_DATE,'yyyy-mm-dd') as CREATE_DATE, 
						  USER_UPDATE, to_char(UPDATE_DATE,'yyyy-mm-dd') as UPDATE_DATE 
						  FROM HR_PROVINCE
						  ORDER BY PROVINCE_CODE ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		$SEQ_NO = 1;
		while($data = $db_att->get_array()){
			$PER_PROVINCE++;
			$PROVINCE_CODE = trim($data[PROVINCE_CODE])."00";
			$PROVINCE_NAME = trim($data[PROVINCE_NAME]);
			$PROVINCE_NAME_E = trim($data[PROVINCE_NAME_E]);
			$PROVINCE_ABB_NAME = trim($data[PROVINCE_ABB_NAME]);

			$UPDATE_USER = 99999;
			if ($data[USER_UPDATE]) $USER_UPDATE = trim($data[USER_UPDATE]);
			else $USER_UPDATE = trim($data[USER_CREATE]);
			if ($data[UPDATE_DATE]) $UPDATE_DATE = trim($data[UPDATE_DATE]);
			else  $UPDATE_DATE = trim($data[CREATE_DATE]);
			if ($USER_UPDATE) {
				$cmd = " select ID from USER_DETAIL where USERNAME = '$USER_UPDATE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				if ($data2[ID]) $UPDATE_USER = $data2[ID];
			}
			if (!$UPDATE_DATE) $UPDATE_DATE = date("Y-m-d H:i:s");

			$cmd = " INSERT INTO PER_PROVINCE(PV_CODE, PV_NAME, CT_CODE, PV_ACTIVE, PV_SEQ_NO, UPDATE_USER, UPDATE_DATE)
							 VALUES ('$PROVINCE_CODE', '$PROVINCE_NAME', '140', 1, $SEQ_NO, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
//			echo "$cmd<br>==================<br>";
//			$db_dpis->show_error();
//			echo "<br>end ". ++$i  ."=======================<br>";
			$SEQ_NO++;
		} // end while						

		$cmd = " select count(PV_CODE) as COUNT_NEW from PER_PROVINCE ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_PROVINCE - $PER_PROVINCE - $COUNT_NEW<br>";
		
		$cmd = " SELECT PROVINCE_CODE, AMPHUR_CODE, AMPHUR_NAME, AMPHUR_NAME_E, 
						  USER_CREATE, to_char(CREATE_DATE,'yyyy-mm-dd') as CREATE_DATE, 
						  USER_UPDATE, to_char(UPDATE_DATE,'yyyy-mm-dd') as UPDATE_DATE 
						  FROM HR_AMPHUR
						  ORDER BY PROVINCE_CODE, AMPHUR_CODE ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		$SEQ_NO = 1;
		while($data = $db_att->get_array()){
			$PER_AMPHUR++;
			$PROVINCE_CODE = trim($data[PROVINCE_CODE])."00";
			$AMPHUR_CODE = trim($data[PROVINCE_CODE]).trim($data[AMPHUR_CODE]);
			$AMPHUR_NAME = trim($data[AMPHUR_NAME]);
			$AMPHUR_NAME_E = trim($data[AMPHUR_NAME_E]);

			$UPDATE_USER = 99999;
			if ($data[USER_UPDATE]) $USER_UPDATE = trim($data[USER_UPDATE]);
			else $USER_UPDATE = trim($data[USER_CREATE]);
			if ($data[UPDATE_DATE]) $UPDATE_DATE = trim($data[UPDATE_DATE]);
			else  $UPDATE_DATE = trim($data[CREATE_DATE]);
			if ($USER_UPDATE) {
				$cmd = " select ID from USER_DETAIL where USERNAME = '$USER_UPDATE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				if ($data2[ID]) $UPDATE_USER = $data2[ID];
			}
			if (!$UPDATE_DATE) $UPDATE_DATE = date("Y-m-d H:i:s");

			$cmd = " INSERT INTO PER_AMPHUR(AP_CODE, AP_NAME, PV_CODE, AP_ACTIVE, AP_SEQ_NO, UPDATE_USER, UPDATE_DATE)
							 VALUES ('$AMPHUR_CODE', '$AMPHUR_NAME', '$PROVINCE_CODE', 1, $SEQ_NO, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
//			echo "$cmd<br>==================<br>";
//			$db_dpis->show_error();
//			echo "<br>end ". ++$i  ."=======================<br>";
			$SEQ_NO++;
		} // end while						

		$cmd = " select count(AP_CODE) as COUNT_NEW from PER_AMPHUR ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_AMPHUR - $PER_AMPHUR - $COUNT_NEW<br>";
		
		$cmd = " SELECT PROVINCE_CODE, AMPHUR_CODE, DISTRICT_CODE, DISTRICT_NAME, DISTRICT_NAME_E, ZIPCODE, 
						  USER_CREATE, to_char(CREATE_DATE,'yyyy-mm-dd') as CREATE_DATE, 
						  USER_UPDATE, to_char(UPDATE_DATE,'yyyy-mm-dd') as UPDATE_DATE 
						  FROM HR_DISTRICT
						  ORDER BY PROVINCE_CODE, AMPHUR_CODE, DISTRICT_CODE ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		$SEQ_NO = 1;
		while($data = $db_att->get_array()){
			$PER_DISTRICT++;
			$PROVINCE_CODE = trim($data[PROVINCE_CODE])."00";
			$AMPHUR_CODE = trim($data[PROVINCE_CODE]).trim($data[AMPHUR_CODE]);
			$DISTRICT_CODE = trim($data[PROVINCE_CODE]).trim($data[AMPHUR_CODE]).trim($data[DISTRICT_CODE]);
			$DISTRICT_NAME = trim($data[DISTRICT_NAME]);
			$DISTRICT_NAME_E = trim($data[DISTRICT_NAME_E]);
			$ZIPCODE = trim($data[ZIPCODE]);

			$UPDATE_USER = 99999;
			if ($data[USER_UPDATE]) $USER_UPDATE = trim($data[USER_UPDATE]);
			else $USER_UPDATE = trim($data[USER_CREATE]);
			if ($data[UPDATE_DATE]) $UPDATE_DATE = trim($data[UPDATE_DATE]);
			else  $UPDATE_DATE = trim($data[CREATE_DATE]);
			if ($USER_UPDATE) {
				$cmd = " select ID from USER_DETAIL where USERNAME = '$USER_UPDATE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				if ($data2[ID]) $UPDATE_USER = $data2[ID];
			}
			if (!$UPDATE_DATE) $UPDATE_DATE = date("Y-m-d H:i:s");

			$cmd = " INSERT INTO PER_DISTRICT(DT_CODE, DT_NAME, PV_CODE, AP_CODE, ZIP_CODE, DT_ACTIVE, DT_SEQ_NO, UPDATE_USER, UPDATE_DATE)
							 VALUES ('$DISTRICT_CODE', '$DISTRICT_NAME', '$PROVINCE_CODE', '$AMPHUR_CODE', '$ZIPCODE', 1, $SEQ_NO, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
//			echo "$cmd<br>==================<br>";
//			$db_dpis->show_error();
//			echo "<br>end ". ++$i  ."=======================<br>";
			$SEQ_NO++;
		} // end while						

		$cmd = " select count(DT_CODE) as COUNT_NEW from PER_DISTRICT ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_DISTRICT - $PER_DISTRICT - $COUNT_NEW<br>";
		
		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_att->free_result();
		$db_att1->free_result();
	} // end if

	if( $command=='MGT' ){
/* // ตำแหน่งในการบริหารงาน 77
		$cmd = " DELETE FROM PER_MGT ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " SELECT ADMIN_CODE, ADMIN_NAME, ADMIN_ABB_NAME, 
						  USER_CREATE, to_char(CREATE_DATE,'yyyy-mm-dd') as CREATE_DATE, 
						  USER_UPDATE, to_char(UPDATE_DATE,'yyyy-mm-dd') as UPDATE_DATE 
						  FROM HR_ADMIN_CODE
						  ORDER BY ADMIN_CODE ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		$SEQ_NO = 1;
		while($data = $db_att->get_array()){
			$PER_MGT++;
			$ADMIN_CODE = trim($data[ADMIN_CODE]);
			$ADMIN_NAME = trim($data[ADMIN_NAME]);
			$ADMIN_ABB_NAME = trim($data[ADMIN_ABB_NAME]);

			$UPDATE_USER = 99999;
			if ($data[USER_UPDATE]) $USER_UPDATE = trim($data[USER_UPDATE]);
			else $USER_UPDATE = trim($data[USER_CREATE]);
			if ($data[UPDATE_DATE]) $UPDATE_DATE = trim($data[UPDATE_DATE]);
			else  $UPDATE_DATE = trim($data[CREATE_DATE]);
			if ($USER_UPDATE) {
				$cmd = " select ID from USER_DETAIL where USERNAME = '$USER_UPDATE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				if ($data2[ID]) $UPDATE_USER = $data2[ID];
			}
			if (!$UPDATE_DATE) $UPDATE_DATE = date("Y-m-d H:i:s");

			$cmd = " INSERT INTO PER_MGT(PM_CODE, PM_NAME, PM_SHORTNAME, PS_CODE, PM_ACTIVE, PM_SEQ_NO, UPDATE_USER, UPDATE_DATE)
							 VALUES ('$ADMIN_CODE', '$ADMIN_NAME', '$ADMIN_ABB_NAME', NULL, 1, $SEQ_NO, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
//			echo "$cmd<br>==================<br>";
//			$db_dpis->show_error();
//			echo "<br>end ". ++$i  ."=======================<br>";
			$SEQ_NO++;
		} // end while						

		$cmd = " select count(PM_CODE) as COUNT_NEW from PER_MGT ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_MGT - $PER_MGT - $COUNT_NEW<br>";
		
// หมวดตำแหน่งลูกจ้างประจำ/ลูกจ้างชั่วคราว 8
		$cmd = " DELETE FROM PER_POS_GROUP ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_TEMP_POS_GROUP ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " SELECT CATEGORY_SAL_CODE, CATEGORY_SAL_NAME, FLAG_USE, CLUSTER_CODE, 
						  USER_CREATE, to_char(CREATE_DATE,'yyyy-mm-dd') as CREATE_DATE, 
						  USER_UPDATE, to_char(UPDATE_DATE,'yyyy-mm-dd') as UPDATE_DATE 
						  FROM HR_CATEGORY_SAL_EMP
						  ORDER BY CATEGORY_SAL_CODE ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		$SEQ_NO = 1;
		while($data = $db_att->get_array()){
			$PER_POS_GROUP++;
			$CATEGORY_SAL_CODE = trim($data[CATEGORY_SAL_CODE]);
			$CATEGORY_SAL_NAME = trim($data[CATEGORY_SAL_NAME]);
			$FLAG_USE = trim($data[FLAG_USE]);
			$CLUSTER_CODE = trim($data[CLUSTER_CODE]);

			$UPDATE_USER = 99999;
			if ($data[USER_UPDATE]) $USER_UPDATE = trim($data[USER_UPDATE]);
			else $USER_UPDATE = trim($data[USER_CREATE]);
			if ($data[UPDATE_DATE]) $UPDATE_DATE = trim($data[UPDATE_DATE]);
			else  $UPDATE_DATE = trim($data[CREATE_DATE]);
			if ($USER_UPDATE) {
				$cmd = " select ID from USER_DETAIL where USERNAME = '$USER_UPDATE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				if ($data2[ID]) $UPDATE_USER = $data2[ID];
			}
			if (!$UPDATE_DATE) $UPDATE_DATE = date("Y-m-d H:i:s");

			$cmd = " INSERT INTO PER_POS_GROUP(PG_CODE, PG_NAME, PG_ACTIVE, PG_SEQ_NO, UPDATE_USER, UPDATE_DATE, PG_NAME_SALARY)
							 VALUES ('$CATEGORY_SAL_CODE', '$CATEGORY_SAL_NAME', $FLAG_USE, $SEQ_NO, $UPDATE_USER, '$UPDATE_DATE', '$CATEGORY_SAL_NAME') ";
			$db_dpis->send_cmd($cmd);
//			echo "$cmd<br>==================<br>";
//			$db_dpis->show_error();
//			echo "<br>end ". ++$i  ."=======================<br>";

			$cmd = " INSERT INTO PER_TEMP_POS_GROUP(TG_CODE, TG_NAME, TG_ACTIVE, TG_SEQ_NO, UPDATE_USER, UPDATE_DATE)
							 VALUES ('$CATEGORY_SAL_CODE', '$CATEGORY_SAL_NAME', $FLAG_USE, $SEQ_NO, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
//			echo "$cmd<br>==================<br>";
//			$db_dpis->show_error();
//			echo "<br>end ". ++$i  ."=======================<br>";
			$SEQ_NO++;
		} // end while						

		$cmd = " select count(PG_CODE) as COUNT_NEW from PER_POS_GROUP ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_POS_GROUP - $PER_POS_GROUP - $COUNT_NEW<br>";
		
// วุฒิการศึกษา 544
		$cmd = " DELETE FROM PER_EDUCNAME ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " SELECT EDUCATION_CODE, EDUCATION_NAME, EDUCATION_NAME_E, EDUCATION_ABB_NAME, 
						  USER_CREATE, to_char(CREATE_DATE,'yyyy-mm-dd') as CREATE_DATE, 
						  USER_UPDATE, to_char(UPDATE_DATE,'yyyy-mm-dd') as UPDATE_DATE 
						  FROM HR_EDUCATION_CODE
						  ORDER BY EDUCATION_CODE ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		$SEQ_NO = 1;
		while($data = $db_att->get_array()){
			$PER_EDUCNAME++;
			$EDUCATION_CODE = trim($data[EDUCATION_CODE]);
			$EDUCATION_NAME = trim($data[EDUCATION_NAME]);
			$EDUCATION_NAME_E = trim($data[EDUCATION_NAME_E]);
			$EDUCATION_ABB_NAME = trim($data[EDUCATION_ABB_NAME]);

			$UPDATE_USER = 99999;
			if ($data[USER_UPDATE]) $USER_UPDATE = trim($data[USER_UPDATE]);
			else $USER_UPDATE = trim($data[USER_CREATE]);
			if ($data[UPDATE_DATE]) $UPDATE_DATE = trim($data[UPDATE_DATE]);
			else  $UPDATE_DATE = trim($data[CREATE_DATE]);
			if ($USER_UPDATE) {
				$cmd = " select ID from USER_DETAIL where USERNAME = '$USER_UPDATE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				if ($data2[ID]) $UPDATE_USER = $data2[ID];
			}
			if (!$UPDATE_DATE) $UPDATE_DATE = date("Y-m-d H:i:s");

			$cmd = " SELECT FUND_COURSE_CODE FROM HR_EDUCATION WHERE EDUCATION_CODE = '$EDUCATION_CODE' AND FUND_COURSE_CODE IS NOT NULL ";
			$db_att1->send_cmd($cmd);
			$data1 = $db_att1->get_array();
			$FUND_COURSE_CODE = $data1[FUND_COURSE_CODE];
			if ($FUND_COURSE_CODE=="001") $EL_CODE = "40";
			elseif ($FUND_COURSE_CODE=="002") $EL_CODE = "60";
			elseif ($FUND_COURSE_CODE=="003") $EL_CODE = "80";
			elseif ($FUND_COURSE_CODE=="004") $EL_CODE = "004";
			elseif ($FUND_COURSE_CODE=="005") $EL_CODE = "005";
			else $EL_CODE = "99";
			if (strpos($EDUCATION_NAME,"ดุษฎีบัณฑิต") !== false || strpos($EDUCATION_NAME,"DOCTOR OF") !== false) $EL_CODE = "80";
			elseif (strpos($EDUCATION_NAME,"มหาบัณฑิต") !== false || strpos($EDUCATION_NAME,"MASTER OF") !== false) $EL_CODE = "60";
			elseif (strpos($EDUCATION_NAME,"BACHELOR OF") !== false) $EL_CODE = "40";

			$cmd = " INSERT INTO PER_EDUCNAME(EN_CODE, EL_CODE, EN_SHORTNAME, EN_NAME, EN_ACTIVE, EN_SEQ_NO, UPDATE_USER, UPDATE_DATE)
							 VALUES ('$EDUCATION_CODE', '$EL_CODE', '$EDUCATION_ABB_NAME', '$EDUCATION_NAME', 1, $SEQ_NO, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
//			echo "$cmd<br>==================<br>";
//			$db_dpis->show_error();
//			echo "<br>end ". ++$i  ."=======================<br>";
			$SEQ_NO++;
		} // end while						

		$cmd = " select count(EN_CODE) as COUNT_NEW from PER_EDUCNAME ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_EDUCNAME - $PER_EDUCNAME - $COUNT_NEW<br>";
		
// ประเภทข้าราชการ 12
		$cmd = " DELETE FROM PER_OFF_TYPE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " SELECT FLAG_PERSON_TYPE, FLAG_PERSON_NAME, 
						  USER_CREATE, to_char(CREATE_DATE,'yyyy-mm-dd') as CREATE_DATE, 
						  USER_UPDATE, to_char(UPDATE_DATE,'yyyy-mm-dd') as UPDATE_DATE 
						  FROM HR_FLAG_PERSON_TYPE
						  ORDER BY FLAG_PERSON_TYPE ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		$SEQ_NO = 1;
		while($data = $db_att->get_array()){
			$PER_OFF_TYPE++;
			$FLAG_PERSON_TYPE = trim($data[FLAG_PERSON_TYPE]);
			$FLAG_PERSON_NAME = trim($data[FLAG_PERSON_NAME]);

			$UPDATE_USER = 99999;
			if ($data[USER_UPDATE]) $USER_UPDATE = trim($data[USER_UPDATE]);
			else $USER_UPDATE = trim($data[USER_CREATE]);
			if ($data[UPDATE_DATE]) $UPDATE_DATE = trim($data[UPDATE_DATE]);
			else  $UPDATE_DATE = trim($data[CREATE_DATE]);
			if ($USER_UPDATE) {
				$cmd = " select ID from USER_DETAIL where USERNAME = '$USER_UPDATE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				if ($data2[ID]) $UPDATE_USER = $data2[ID];
			}
			if (!$UPDATE_DATE) $UPDATE_DATE = date("Y-m-d H:i:s");

			$cmd = " INSERT INTO PER_OFF_TYPE(OT_CODE, OT_NAME, OT_ACTIVE, OT_SEQ_NO, UPDATE_USER, UPDATE_DATE)
							 VALUES ('$FLAG_PERSON_TYPE', '$FLAG_PERSON_NAME', 1, $SEQ_NO, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
//			echo "$cmd<br>==================<br>";
//			$db_dpis->show_error();
//			echo "<br>end ". ++$i  ."=======================<br>";
			$SEQ_NO++;
		} // end while						

		$cmd = " select count(OT_CODE) as COUNT_NEW from PER_OFF_TYPE ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_OFF_TYPE - $PER_OFF_TYPE - $COUNT_NEW<br>";
	
// สาขาวิชาเอก 1013
		$cmd = " DELETE FROM PER_EDUCMAJOR ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " SELECT MAJOR_CODE, MAJOR_NAME, MAJOR_ABB_NAME, 
						  USER_CREATE, to_char(CREATE_DATE,'yyyy-mm-dd') as CREATE_DATE, 
						  USER_UPDATE, to_char(UPDATE_DATE,'yyyy-mm-dd') as UPDATE_DATE 
						  FROM HR_MAJOR_CODE
						  ORDER BY MAJOR_CODE ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		$SEQ_NO = 1;
		while($data = $db_att->get_array()){
			$PER_EDUCMAJOR++;
			$MAJOR_CODE = trim($data[MAJOR_CODE]);
			$MAJOR_NAME = trim($data[MAJOR_NAME]);
			$MAJOR_ABB_NAME = trim($data[MAJOR_ABB_NAME]);

			$UPDATE_USER = 99999;
			if ($data[USER_UPDATE]) $USER_UPDATE = trim($data[USER_UPDATE]);
			else $USER_UPDATE = trim($data[USER_CREATE]);
			if ($data[UPDATE_DATE]) $UPDATE_DATE = trim($data[UPDATE_DATE]);
			else  $UPDATE_DATE = trim($data[CREATE_DATE]);
			if ($USER_UPDATE) {
				$cmd = " select ID from USER_DETAIL where USERNAME = '$USER_UPDATE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				if ($data2[ID]) $UPDATE_USER = $data2[ID];
			}
			if (!$UPDATE_DATE) $UPDATE_DATE = date("Y-m-d H:i:s");

			$cmd = " INSERT INTO PER_EDUCMAJOR(EM_CODE, EM_NAME, EM_ACTIVE, EM_SEQ_NO, UPDATE_USER, UPDATE_DATE)
							 VALUES ('$MAJOR_CODE', '$MAJOR_NAME', 1, $SEQ_NO, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
//			echo "$cmd<br>==================<br>";
//			$db_dpis->show_error();
//			echo "<br>end ". ++$i  ."=======================<br>";
			$SEQ_NO++;
		} // end while						

		$cmd = " select count(EM_CODE) as COUNT_NEW from PER_EDUCMAJOR ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_EDUCMAJOR - $PER_EDUCMAJOR - $COUNT_NEW<br>";

// ตำแหน่งในสายงาน 249
		$cmd = " DELETE FROM PER_LINE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " SELECT WORK_LINE_CODE, WORK_LINE_NAME, WORK_LINE_ABB_NAME, WORK_LINE_OFFICE, LINE_CLASS_BEGIN, 
						  LINE_CLASS_END, USER_CREATE, to_char(CREATE_DATE,'yyyy-mm-dd') as CREATE_DATE, 
						  USER_UPDATE, to_char(UPDATE_DATE,'yyyy-mm-dd') as UPDATE_DATE 
						  FROM HR_WORK_LINE_CODE
						  ORDER BY WORK_LINE_CODE ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		$SEQ_NO = 1;
		while($data = $db_att->get_array()){
			$PER_LINE++;
			$WORK_LINE_CODE = trim($data[WORK_LINE_CODE]);
			$WORK_LINE_NAME = trim($data[WORK_LINE_NAME]);
			$WORK_LINE_ABB_NAME = trim($data[WORK_LINE_ABB_NAME]);
			$WORK_LINE_OFFICE = trim($data[WORK_LINE_OFFICE]);
			$LINE_CLASS_BEGIN = trim($data[LINE_CLASS_BEGIN]);
			$LINE_CLASS_END = trim($data[LINE_CLASS_END]);
			if (!$WORK_LINE_NAME) $WORK_LINE_NAME = "-";

			$UPDATE_USER = 99999;
			if ($data[USER_UPDATE]) $USER_UPDATE = trim($data[USER_UPDATE]);
			else $USER_UPDATE = trim($data[USER_CREATE]);
			if ($data[UPDATE_DATE]) $UPDATE_DATE = trim($data[UPDATE_DATE]);
			else  $UPDATE_DATE = trim($data[CREATE_DATE]);
			if ($USER_UPDATE) {
				$cmd = " select ID from USER_DETAIL where USERNAME = '$USER_UPDATE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				if ($data2[ID]) $UPDATE_USER = $data2[ID];
			}
			if (!$UPDATE_DATE) $UPDATE_DATE = date("Y-m-d H:i:s");

			$cmd = " INSERT INTO PER_LINE(PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, PL_SEQ_NO, UPDATE_USER, UPDATE_DATE)
							 VALUES ('$WORK_LINE_CODE', '$WORK_LINE_NAME', '$WORK_LINE_OFFICE', 1, $SEQ_NO, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
//			echo "$cmd<br>==================<br>";
//			$db_dpis->show_error();
//			echo "<br>end ". ++$i  ."=======================<br>";
			$SEQ_NO++;
		} // end while						

		$cmd = " select count(PL_CODE) as COUNT_NEW from PER_LINE ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_LINE - $PER_LINE - $COUNT_NEW<br>";
		
// ตำแหน่งลูกจ้างประจำ/ลูกจ้างชั่วคราว 587
		$cmd = " DELETE FROM PER_POS_NAME ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_TEMP_POS_NAME ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " SELECT CATEGORY_SAL_CODE, WORK_LINE_CODE, WORK_LINE_NAME, CATEGORY_SAL_NAME, 
						  SALARY_START, SALARY_DAY_START, SALARY_END, SALARY_DAY_END, FLAG_USE, CLUSTER_CODE, 
						  USER_CREATE, to_char(CREATE_DATE,'yyyy-mm-dd') as CREATE_DATE, 
						  USER_UPDATE, to_char(UPDATE_DATE,'yyyy-mm-dd') as UPDATE_DATE 
						  FROM HR_WORKLINE_EMP
						  ORDER BY CATEGORY_SAL_CODE, WORK_LINE_CODE ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		$SEQ_NO = 1;
		while($data = $db_att->get_array()){
			$PER_POS_NAME++;
			$CATEGORY_SAL_CODE = trim($data[CATEGORY_SAL_CODE]);
			$WORK_LINE_CODE = trim($data[WORK_LINE_CODE]);
			$WORK_LINE_NAME = trim($data[WORK_LINE_NAME]);
			$CATEGORY_SAL_NAME = trim($data[CATEGORY_SAL_NAME]);
			$SALARY_START = trim($data[SALARY_START]);
			$SALARY_DAY_START = trim($data[SALARY_DAY_START]);
			$SALARY_END = trim($data[SALARY_END]);
			$SALARY_DAY_END = trim($data[SALARY_DAY_END]);
			$FLAG_USE = trim($data[FLAG_USE]);
			$CLUSTER_CODE = trim($data[CLUSTER_CODE]);

			$UPDATE_USER = 99999;
			if ($data[USER_UPDATE]) $USER_UPDATE = trim($data[USER_UPDATE]);
			else $USER_UPDATE = trim($data[USER_CREATE]);
			if ($data[UPDATE_DATE]) $UPDATE_DATE = trim($data[UPDATE_DATE]);
			else  $UPDATE_DATE = trim($data[CREATE_DATE]);
			if ($USER_UPDATE) {
				$cmd = " select ID from USER_DETAIL where USERNAME = '$USER_UPDATE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				if ($data2[ID]) $UPDATE_USER = $data2[ID];
			}
			if (!$UPDATE_DATE) $UPDATE_DATE = date("Y-m-d H:i:s");

			$cmd = " INSERT INTO PER_POS_NAME(PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, PN_SEQ_NO, UPDATE_USER, UPDATE_DATE)
							 VALUES ('$CATEGORY_SAL_CODE$WORK_LINE_CODE', '$WORK_LINE_NAME', '$CATEGORY_SAL_CODE', 0, 1, $SEQ_NO, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
//			echo "$cmd<br>==================<br>";
//			$db_dpis->show_error();
//			echo "<br>end ". ++$i  ."=======================<br>";

			$cmd = " INSERT INTO PER_TEMP_POS_NAME(TP_CODE, TP_NAME, TG_CODE, TP_ACTIVE, TP_SEQ_NO, UPDATE_USER, UPDATE_DATE)
							 VALUES ('$CATEGORY_SAL_CODE$WORK_LINE_CODE', '$WORK_LINE_NAME', '$CATEGORY_SAL_CODE', 1, $SEQ_NO, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
//			echo "$cmd<br>==================<br>";
//			$db_dpis->show_error();
//			echo "<br>end ". ++$i  ."=======================<br>";
			$SEQ_NO++;
		} // end while						

		$cmd = " select count(PN_CODE) as COUNT_NEW from PER_POS_NAME ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_POS_NAME - $PER_POS_NAME - $COUNT_NEW<br>";
*/
/*		
// บัญชีอัตราเงินเดือน 1863
		$cmd = " DELETE FROM PER_LAYER ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " SELECT MP_CEE, SALARY_LEVEL_CODE, GP_SAL_GOV_YEAR, FLAG_STATUS, SALARY, 
						  to_char(EFFECT_DATE,'yyyy-mm-dd') as EFFECT_DATE, MP_CEE_CODE, 
						  USER_CREATE, to_char(CREATE_DATE,'yyyy-mm-dd') as CREATE_DATE, 
						  USER_UPDATE, to_char(UPDATE_DATE,'yyyy-mm-dd') as UPDATE_DATE 
						  FROM HR_SALARY_CODE
						  WHERE FLAG_STATUS = 1
						  ORDER BY MP_CEE_CODE, MP_CEE, SALARY_LEVEL_CODE ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		$SEQ_NO = 1;
		while($data = $db_att->get_array()){
			$PER_LAYER++;
			$LEVEL_NO = str_pad(trim($data[MP_CEE]), 2, "0", STR_PAD_LEFT);
			$SALARY_LEVEL_CODE = trim($data[SALARY_LEVEL_CODE]);
			$GP_SAL_GOV_YEAR = trim($data[GP_SAL_GOV_YEAR]);
			$FLAG_STATUS = trim($data[FLAG_STATUS]);
			$SALARY = trim($data[SALARY]);
			$EFFECT_DATE = trim($data[EFFECT_DATE]);
			$MP_CEE_CODE = trim($data[MP_CEE_CODE]);
			if ($MP_CEE_CODE=="บ.") $LAYER_TYPE = 2;
			elseif ($MP_CEE_CODE=="ท.") $LAYER_TYPE = 1;

			$UPDATE_USER = 99999;
			if ($data[USER_UPDATE]) $USER_UPDATE = trim($data[USER_UPDATE]);
			else $USER_UPDATE = trim($data[USER_CREATE]);
			if ($data[UPDATE_DATE]) $UPDATE_DATE = trim($data[UPDATE_DATE]);
			else  $UPDATE_DATE = trim($data[CREATE_DATE]);
			if ($USER_UPDATE) {
				$cmd = " select ID from USER_DETAIL where USERNAME = '$USER_UPDATE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				if ($data2[ID]) $UPDATE_USER = $data2[ID];
			}
			if (!$UPDATE_DATE) $UPDATE_DATE = date("Y-m-d H:i:s");

			$cmd = " INSERT INTO PER_LAYER(LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, LAYER_SEQ_NO, UPDATE_USER, UPDATE_DATE)
							 VALUES ($LAYER_TYPE, '$LEVEL_NO', $SALARY_LEVEL_CODE, $SALARY, 1, $SEQ_NO, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
//			echo "$cmd<br>==================<br>";
//			$db_dpis->show_error();
//			echo "<br>end ". ++$i  ."=======================<br>";
			$SEQ_NO++;
		} // end while						

		$cmd = " select count(LAYER_TYPE) as COUNT_NEW from PER_LAYER ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_LAYER - $PER_LAYER - $COUNT_NEW<br>";
*/		
/*
// คำนำหน้าชื่อ 521
		$cmd = " DELETE FROM PER_PRENAME ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " SELECT RANK_CODE, RANK_NAME, RANK_NAME_E, RANK_ABB_NAME, 
						  USER_CREATE, to_char(CREATE_DATE,'yyyy-mm-dd') as CREATE_DATE, 
						  USER_UPDATE, to_char(UPDATE_DATE,'yyyy-mm-dd') as UPDATE_DATE 
						  FROM RANK_CODE
						  ORDER BY RANK_CODE ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		$SEQ_NO = 1;
		while($data = $db_att->get_array()){
			$PER_PRENAME++;
			$RANK_CODE = trim($data[RANK_CODE]);
			$RANK_NAME = trim($data[RANK_NAME]);
			$RANK_NAME_E = trim($data[RANK_NAME_E]);
			$RANK_ABB_NAME = trim($data[RANK_ABB_NAME]);

			$UPDATE_USER = 99999;
			if ($data[USER_UPDATE]) $USER_UPDATE = trim($data[USER_UPDATE]);
			else $USER_UPDATE = trim($data[USER_CREATE]);
			if ($data[UPDATE_DATE]) $UPDATE_DATE = trim($data[UPDATE_DATE]);
			else  $UPDATE_DATE = trim($data[CREATE_DATE]);
			if ($USER_UPDATE) {
				$cmd = " select ID from USER_DETAIL where USERNAME = '$USER_UPDATE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				if ($data2[ID]) $UPDATE_USER = $data2[ID];
			}
			if (!$UPDATE_DATE) $UPDATE_DATE = date("Y-m-d H:i:s");

			$cmd = " INSERT INTO PER_PRENAME(PN_CODE, PN_SHORTNAME, PN_NAME, PN_ENG_NAME, PN_ACTIVE, PN_SEQ_NO, UPDATE_USER, UPDATE_DATE)
							 VALUES ('$RANK_CODE', '$RANK_ABB_NAME', '$RANK_NAME', '$RANK_NAME_E', 1, $SEQ_NO, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
//			echo "$cmd<br>==================<br>";
//			$db_dpis->show_error();
//			echo "<br>end ". ++$i  ."=======================<br>";
			$SEQ_NO++;
		} // end while						

		$cmd = " select count(PN_CODE) as COUNT_NEW from PER_PRENAME ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_PRENAME - $PER_PRENAME - $COUNT_NEW<br>";
		
// ประเภทการเคลื่อนไหว 69
		$cmd = " DELETE FROM PER_MOVMENT ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " SELECT FLAG_TO_NAME_CODE, FLAG_TO_NAME, FLAG_CUR_ST, 
						  USER_CREATE, to_char(CREATE_DATE,'yyyy-mm-dd') as CREATE_DATE, 
						  USER_UPDATE, to_char(UPDATE_DATE,'yyyy-mm-dd') as UPDATE_DATE 
						  FROM HR_FLAG_TO_NAME
						  ORDER BY FLAG_TO_NAME_CODE ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		$SEQ_NO = 1;
		while($data = $db_att->get_array()){
			$PER_MOVMENT++;
			$FLAG_TO_NAME_CODE = trim($data[FLAG_TO_NAME_CODE]);
			$FLAG_TO_NAME = trim($data[FLAG_TO_NAME]);
			$FLAG_CUR_ST = trim($data[FLAG_CUR_ST]);

			$UPDATE_USER = 99999;
			if ($data[USER_UPDATE]) $USER_UPDATE = trim($data[USER_UPDATE]);
			else $USER_UPDATE = trim($data[USER_CREATE]);
			if ($data[UPDATE_DATE]) $UPDATE_DATE = trim($data[UPDATE_DATE]);
			else  $UPDATE_DATE = trim($data[CREATE_DATE]);
			if ($USER_UPDATE) {
				$cmd = " select ID from USER_DETAIL where USERNAME = '$USER_UPDATE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				if ($data2[ID]) $UPDATE_USER = $data2[ID];
			}
			if (!$UPDATE_DATE) $UPDATE_DATE = date("Y-m-d H:i:s");

			$cmd = " INSERT INTO PER_MOVMENT(MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, MOV_SEQ_NO, UPDATE_USER, UPDATE_DATE)
							 VALUES ('$FLAG_TO_NAME_CODE', '$FLAG_TO_NAME', 1, 1, $SEQ_NO, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
//			echo "$cmd<br>==================<br>";
//			$db_dpis->show_error();
//			echo "<br>end ". ++$i  ."=======================<br>";
			$SEQ_NO++;
		} // end while						

		$cmd = " SELECT FLAG_TO_NAME_CODE, FLAG_TO_NAME, REMARK, FLAG_CUR_ST, 
						  USER_CREATE, to_char(CREATE_DATE,'yyyy-mm-dd') as CREATE_DATE, 
						  USER_UPDATE, to_char(UPDATE_DATE,'yyyy-mm-dd') as UPDATE_DATE 
						  FROM HR_FLAG_TO_NAME_EMP
						  ORDER BY FLAG_TO_NAME_CODE ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		while($data = $db_att->get_array()){
			$PER_MOVMENT++;
			$FLAG_TO_NAME_CODE = "20" . trim($data[FLAG_TO_NAME_CODE]);
			$FLAG_TO_NAME = trim($data[FLAG_TO_NAME]);
			$FLAG_CUR_ST = trim($data[FLAG_CUR_ST]);

			$UPDATE_USER = 99999;
			if ($data[USER_UPDATE]) $USER_UPDATE = trim($data[USER_UPDATE]);
			else $USER_UPDATE = trim($data[USER_CREATE]);
			if ($data[UPDATE_DATE]) $UPDATE_DATE = trim($data[UPDATE_DATE]);
			else  $UPDATE_DATE = trim($data[CREATE_DATE]);
			if ($USER_UPDATE) {
				$cmd = " select ID from USER_DETAIL where USERNAME = '$USER_UPDATE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				if ($data2[ID]) $UPDATE_USER = $data2[ID];
			}
			if (!$UPDATE_DATE) $UPDATE_DATE = date("Y-m-d H:i:s");

			$cmd = " INSERT INTO PER_MOVMENT(MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, MOV_SEQ_NO, UPDATE_USER, UPDATE_DATE)
							 VALUES ('$FLAG_TO_NAME_CODE', '$FLAG_TO_NAME', 1, 1, $SEQ_NO, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
//			echo "$cmd<br>==================<br>";
//			$db_dpis->show_error();
//			echo "<br>end ". ++$i  ."=======================<br>";
			$SEQ_NO++;
		} // end while						

		$cmd = " select count(MOV_CODE) as COUNT_NEW from PER_MOVMENT ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_MOVMENT - $PER_MOVMENT - $COUNT_NEW<br>";
		
// ชั้นเครื่องราชย์ 18 ขาด 19 เหรียญกาชาด
		$cmd = " DELETE FROM PER_DECORATION ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " SELECT DECORATIONS_CODE, DECORATIONS_NAME, DECORATIONS_ABB, CLASS_BEGIN, CLASS_END, 
						  ORDER_DECOR, USER_CREATE, to_char(CREATE_DATE,'yyyy-mm-dd') as CREATE_DATE, 
						  USER_UPDATE, to_char(UPDATE_DATE,'yyyy-mm-dd') as UPDATE_DATE 
						  FROM HR_DECORA_CODE
						  ORDER BY DECORATIONS_CODE ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		$SEQ_NO = 1;
		while($data = $db_att->get_array()){
			$PER_DECORATION++;
			$DECORATIONS_CODE = trim($data[DECORATIONS_CODE]);
			$DECORATIONS_NAME = trim($data[DECORATIONS_NAME]);
			$DECORATIONS_ABB = trim($data[DECORATIONS_ABB]);
			$CLASS_BEGIN = trim($data[CLASS_BEGIN]);
			$CLASS_END = trim($data[CLASS_END]);
			$ORDER_DECOR = trim($data[ORDER_DECOR]);
			if ($DECORATIONS_CODE>="01" && $DECORATIONS_CODE<="04") $DC_TYPE = 1;
			elseif ($DECORATIONS_CODE>="05" && $DECORATIONS_CODE<="12") $DC_TYPE = 2;
			elseif ($DECORATIONS_CODE>="13" && $DECORATIONS_CODE<="17") $DC_TYPE = 3;
			else $DC_TYPE = 1;

			$UPDATE_USER = 99999;
			if ($data[USER_UPDATE]) $USER_UPDATE = trim($data[USER_UPDATE]);
			else $USER_UPDATE = trim($data[USER_CREATE]);
			if ($data[UPDATE_DATE]) $UPDATE_DATE = trim($data[UPDATE_DATE]);
			else  $UPDATE_DATE = trim($data[CREATE_DATE]);
			if ($USER_UPDATE) {
				$cmd = " select ID from USER_DETAIL where USERNAME = '$USER_UPDATE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				if ($data2[ID]) $UPDATE_USER = $data2[ID];
			}
			if (!$UPDATE_DATE) $UPDATE_DATE = date("Y-m-d H:i:s");

			$cmd = " INSERT INTO PER_DECORATION(DC_CODE, DC_SHORTNAME, DC_NAME, 
							 DC_ORDER, DC_TYPE, DC_ACTIVE, DC_SEQ_NO, UPDATE_USER, UPDATE_DATE)
							 VALUES ('$DECORATIONS_CODE', '$DECORATIONS_ABB', '$DECORATIONS_NAME', 
							 $ORDER_DECOR, $DC_TYPE, 1, $SEQ_NO, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
//			echo "$cmd<br>==================<br>";
//			$db_dpis->show_error();
//			echo "<br>end ". ++$i  ."=======================<br>";
			$SEQ_NO++;
		} // end while						

		$cmd = " select count(DC_CODE) as COUNT_NEW from PER_DECORATION ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_DECORATION - $PER_DECORATION - $COUNT_NEW<br>";

		$cmd = " INSERT INTO PER_DECORATION(DC_CODE, DC_SHORTNAME, DC_NAME, 
						 DC_ORDER, DC_TYPE, DC_ACTIVE, DC_SEQ_NO, UPDATE_USER, UPDATE_DATE)
						 VALUES ('19', 'เหรียญกาชาดสมนาคุณชั้นที่ 3', 'เหรียญกาชาดสมนาคุณชั้นที่ 3', 
						 19, 3, 1, 19, $UPDATE_USER, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
*/		
// ประเภทตำแหน่ง 8
		$cmd = " DELETE FROM PER_TYPE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " INSERT INTO PER_TYPE(PT_CODE, PT_NAME, PT_GROUP, PT_ACTIVE, PT_SEQ_NO, UPDATE_USER, UPDATE_DATE)
						 VALUES ('1', 'ทั่วไป', 1, 1, 1, $UPDATE_USER, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);

		$cmd = " INSERT INTO PER_TYPE(PT_CODE, PT_NAME, PT_GROUP, PT_ACTIVE, PT_SEQ_NO, UPDATE_USER, UPDATE_DATE)
						 VALUES ('2', 'ผู้บริหารระดับสูง', 3, 1, 2, $UPDATE_USER, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);

		$cmd = " INSERT INTO PER_TYPE(PT_CODE, PT_NAME, PT_GROUP, PT_ACTIVE, PT_SEQ_NO, UPDATE_USER, UPDATE_DATE)
						 VALUES ('3', 'ผู้บริหารระดับกลาง', 3, 1, 3, $UPDATE_USER, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);

		$cmd = " INSERT INTO PER_TYPE(PT_CODE, PT_NAME, PT_GROUP, PT_ACTIVE, PT_SEQ_NO, UPDATE_USER, UPDATE_DATE)
						 VALUES ('4', 'วช.', 2, 1, 4, $UPDATE_USER, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);

		$cmd = " INSERT INTO PER_TYPE(PT_CODE, PT_NAME, PT_GROUP, PT_ACTIVE, PT_SEQ_NO, UPDATE_USER, UPDATE_DATE)
						 VALUES ('5', 'ชช.', 2, 1, 5, $UPDATE_USER, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);

		$cmd = " INSERT INTO PER_TYPE(PT_CODE, PT_NAME, PT_GROUP, PT_ACTIVE, PT_SEQ_NO, UPDATE_USER, UPDATE_DATE)
						 VALUES ('6', 'ผู้บริหารระดับกลาง ชช.', 3, 1, 6, $UPDATE_USER, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);

		$cmd = " INSERT INTO PER_TYPE(PT_CODE, PT_NAME, PT_GROUP, PT_ACTIVE, PT_SEQ_NO, UPDATE_USER, UPDATE_DATE)
						 VALUES ('7', 'ทั่วไป/วช.', 1, 1, 7, $UPDATE_USER, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);

		$cmd = " INSERT INTO PER_TYPE(PT_CODE, PT_NAME, PT_GROUP, PT_ACTIVE, PT_SEQ_NO, UPDATE_USER, UPDATE_DATE)
						 VALUES ('8', 'ทั่วไป/ชช.', 1, 1, 8, $UPDATE_USER, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);

//		$cmd = " SELECT FLAG_POSITION, FLAG_POSITION_NAME, FLAG_POSITION_ABB, 
//						  USER_CREATE, to_char(CREATE_DATE,'yyyy-mm-dd') as CREATE_DATE, 
//						  USER_UPDATE, to_char(UPDATE_DATE,'yyyy-mm-dd') as UPDATE_DATE 
//						  FROM HR_FLAG_POSITION
//						  ORDER BY FLAG_POSITION ";
//		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
//		$SEQ_NO = 1;
//		while($data = $db_att->get_array()){
//			$PER_TYPE++;
//			$FLAG_POSITION = trim($data[FLAG_POSITION]);
//			$FLAG_POSITION_NAME = trim($data[FLAG_POSITION_NAME]);
//			$FLAG_POSITION_ABB = trim($data[FLAG_POSITION_ABB]);

//			$UPDATE_USER = 99999;
//			if ($data[USER_UPDATE]) $USER_UPDATE = trim($data[USER_UPDATE]);
//			else $USER_UPDATE = trim($data[USER_CREATE]);
//			if ($data[UPDATE_DATE]) $UPDATE_DATE = trim($data[UPDATE_DATE]);
//			else  $UPDATE_DATE = trim($data[CREATE_DATE]);
//			if ($USER_UPDATE) {
//				$cmd = " select ID from USER_DETAIL where USERNAME = '$USER_UPDATE' ";
//				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
//				$data2 = $db_dpis2->get_array();
//				if ($data2[ID]) $UPDATE_USER = $data2[ID];
//			}
//			if (!$UPDATE_DATE) $UPDATE_DATE = date("Y-m-d H:i:s");

//			$cmd = " INSERT INTO PER_TYPE(PT_CODE, PT_NAME, PT_GROUP, PT_ACTIVE, PT_SEQ_NO, UPDATE_USER, UPDATE_DATE)
//							 VALUES ('$FLAG_POSITION', '$FLAG_POSITION_NAME', 1, 1, $SEQ_NO, $UPDATE_USER, '$UPDATE_DATE') ";
//			$db_dpis->send_cmd($cmd);
//			echo "$cmd<br>==================<br>";
//			$db_dpis->show_error();
//			echo "<br>end ". ++$i  ."=======================<br>";
//			$SEQ_NO++;
//		} // end while						

		$cmd = " select count(PT_CODE) as COUNT_NEW from PER_TYPE ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_TYPE - $PER_TYPE - $COUNT_NEW<br>";
/*		
// ประเภทการลา 14
		$cmd = " DELETE FROM PER_ABSENTTYPE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " SELECT LEAVE_CODE, LEAVE_NAME, REC_STS, LEAVE_FLAG, 
						  USER_CREATE, to_char(CREATE_DATE,'yyyy-mm-dd') as CREATE_DATE, 
						  USER_UPDATE, to_char(UPDATE_DATE,'yyyy-mm-dd') as UPDATE_DATE 
						  FROM HR_LEAVE_CODE
						  ORDER BY LEAVE_CODE ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		$SEQ_NO = 1;
		while($data = $db_att->get_array()){
			$PER_ABSENTTYPE++;
			$LEAVE_CODE = trim($data[LEAVE_CODE]);
			$LEAVE_NAME = trim($data[LEAVE_NAME]);
			$REC_STS = trim($data[REC_STS]);
			$LEAVE_FLAG = trim($data[LEAVE_FLAG]);

			$UPDATE_USER = 99999;
			if ($data[USER_UPDATE]) $USER_UPDATE = trim($data[USER_UPDATE]);
			else $USER_UPDATE = trim($data[USER_CREATE]);
			if ($data[UPDATE_DATE]) $UPDATE_DATE = trim($data[UPDATE_DATE]);
			else  $UPDATE_DATE = trim($data[CREATE_DATE]);
			if ($USER_UPDATE) {
				$cmd = " select ID from USER_DETAIL where USERNAME = '$USER_UPDATE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				if ($data2[ID]) $UPDATE_USER = $data2[ID];
			}
			if (!$UPDATE_DATE) $UPDATE_DATE = date("Y-m-d H:i:s");

			$cmd = " INSERT INTO PER_ABSENTTYPE(AB_CODE, AB_NAME, AB_QUOTA, AB_COUNT, AB_ACTIVE, AB_SEQ_NO, UPDATE_USER, UPDATE_DATE)
							 VALUES ('$LEAVE_CODE', '$LEAVE_NAME', 0, 1, 1, $SEQ_NO, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
//			echo "$cmd<br>==================<br>";
//			$db_dpis->show_error();
//			echo "<br>end ". ++$i  ."=======================<br>";
			$SEQ_NO++;
		} // end while						

		$cmd = " select count(AB_CODE) as COUNT_NEW from PER_ABSENTTYPE ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_ABSENTTYPE - $PER_ABSENTTYPE - $COUNT_NEW<br>";

// ประเภทโทษทางวินัย 27
		$cmd = " DELETE FROM PER_PENALTY ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " SELECT PUN_LGOV_CODE, PUN_LGOV_NAME, PUN_PERCENT, MONTH_AMOUNT, LEVEL_AMOUNT, 
						  USER_CREATE, to_char(CREATE_DATE,'yyyy-mm-dd') as CREATE_DATE, 
						  USER_UPDATE, to_char(UPDATE_DATE,'yyyy-mm-dd') as UPDATE_DATE 
						  FROM HR_PUNISH_LEVEL_GOV
						  ORDER BY PUN_LGOV_CODE ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		$SEQ_NO = 1;
		while($data = $db_att->get_array()){
			$PER_PENALTY++;
			$PUN_LGOV_CODE = trim($data[PUN_LGOV_CODE]);
			$PUN_LGOV_NAME = trim($data[PUN_LGOV_NAME]);
			$PUN_PERCENT = trim($data[PUN_PERCENT]);
			$MONTH_AMOUNT = trim($data[MONTH_AMOUNT]);
			$LEVEL_AMOUNT = trim($data[LEVEL_AMOUNT]);

			$UPDATE_USER = 99999;
			if ($data[USER_UPDATE]) $USER_UPDATE = trim($data[USER_UPDATE]);
			else $USER_UPDATE = trim($data[USER_CREATE]);
			if ($data[UPDATE_DATE]) $UPDATE_DATE = trim($data[UPDATE_DATE]);
			else  $UPDATE_DATE = trim($data[CREATE_DATE]);
			if ($USER_UPDATE) {
				$cmd = " select ID from USER_DETAIL where USERNAME = '$USER_UPDATE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				if ($data2[ID]) $UPDATE_USER = $data2[ID];
			}
			if (!$UPDATE_DATE) $UPDATE_DATE = date("Y-m-d H:i:s");

			$cmd = " INSERT INTO PER_PENALTY(PEN_CODE, PEN_NAME, PEN_ACTIVE, PEN_SEQ_NO, UPDATE_USER, UPDATE_DATE)
							 VALUES ('$PUN_LGOV_CODE', '$PUN_LGOV_NAME', 1, $SEQ_NO, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
//			echo "$cmd<br>==================<br>";
//			$db_dpis->show_error();
//			echo "<br>end ". ++$i  ."=======================<br>";
			$SEQ_NO++;
		} // end while						

		$cmd = " select count(PEN_CODE) as COUNT_NEW from PER_PENALTY ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_PENALTY - $PER_PENALTY - $COUNT_NEW<br>";

// ความดีความชอบ 4
		$cmd = " DELETE FROM PER_REWARD ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " SELECT GOOD_WORK_CODE, GOOD_WORK_NAME, 
						  USER_CREATE, to_char(CREATE_DATE,'yyyy-mm-dd') as CREATE_DATE, 
						  USER_UPDATE, to_char(UPDATE_DATE,'yyyy-mm-dd') as UPDATE_DATE 
						  FROM HR_GOOD_WORK_CODE
						  ORDER BY GOOD_WORK_CODE ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		$SEQ_NO = 1;
		while($data = $db_att->get_array()){
			$PER_REWARD++;
			$GOOD_WORK_CODE = trim($data[GOOD_WORK_CODE]);
			$GOOD_WORK_NAME = trim($data[GOOD_WORK_NAME]);

			$UPDATE_USER = 99999;
			if ($data[USER_UPDATE]) $USER_UPDATE = trim($data[USER_UPDATE]);
			else $USER_UPDATE = trim($data[USER_CREATE]);
			if ($data[UPDATE_DATE]) $UPDATE_DATE = trim($data[UPDATE_DATE]);
			else  $UPDATE_DATE = trim($data[CREATE_DATE]);
			if ($USER_UPDATE) {
				$cmd = " select ID from USER_DETAIL where USERNAME = '$USER_UPDATE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				if ($data2[ID]) $UPDATE_USER = $data2[ID];
			}
			if (!$UPDATE_DATE) $UPDATE_DATE = date("Y-m-d H:i:s");

			$cmd = " INSERT INTO PER_REWARD(REW_CODE, REW_NAME, REW_ACTIVE, REW_SEQ_NO, UPDATE_USER, UPDATE_DATE)
							 VALUES ('$GOOD_WORK_CODE', '$GOOD_WORK_NAME', 1, $SEQ_NO, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
//			echo "$cmd<br>==================<br>";
//			$db_dpis->show_error();
//			echo "<br>end ". ++$i  ."=======================<br>";
			$SEQ_NO++;
		} // end while						

		$cmd = " select count(REW_CODE) as COUNT_NEW from PER_REWARD ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_REWARD - $PER_REWARD - $COUNT_NEW<br>";

// ระดับการศึกษา 5
		$cmd = " DELETE FROM PER_EDUCLEVEL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " SELECT FUND_COURSE_CODE, FUND_COURSE_NAME, LEVEL_SEQ, 
						  USER_CREATE, to_char(CREATE_DATE,'yyyy-mm-dd') as CREATE_DATE, 
						  USER_UPDATE, to_char(UPDATE_DATE,'yyyy-mm-dd') as UPDATE_DATE 
						  FROM HR_FUND_COURSE_CODE
						  ORDER BY FUND_COURSE_CODE ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		while($data = $db_att->get_array()){
			$PER_EDUCLEVEL++;
			$FUND_COURSE_CODE = trim($data[FUND_COURSE_CODE]);
			$FUND_COURSE_NAME = trim($data[FUND_COURSE_NAME]);
			$LEVEL_SEQ = trim($data[LEVEL_SEQ]);

			$UPDATE_USER = 99999;
			if ($data[USER_UPDATE]) $USER_UPDATE = trim($data[USER_UPDATE]);
			else $USER_UPDATE = trim($data[USER_CREATE]);
			if ($data[UPDATE_DATE]) $UPDATE_DATE = trim($data[UPDATE_DATE]);
			else  $UPDATE_DATE = trim($data[CREATE_DATE]);
			if ($USER_UPDATE) {
				$cmd = " select ID from USER_DETAIL where USERNAME = '$USER_UPDATE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				if ($data2[ID]) $UPDATE_USER = $data2[ID];
			}
			if (!$UPDATE_DATE) $UPDATE_DATE = date("Y-m-d H:i:s");

			$cmd = " INSERT INTO PER_EDUCLEVEL(EL_CODE, EL_NAME, EL_ACTIVE, EL_SEQ_NO, UPDATE_USER, UPDATE_DATE)
							 VALUES ('$FUND_COURSE_CODE', '$FUND_COURSE_NAME', 1, $LEVEL_SEQ, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
//			echo "$cmd<br>==================<br>";
//			$db_dpis->show_error();
//			echo "<br>end ". ++$i  ."=======================<br>";
		} // end while						

		$cmd = " select count(EL_CODE) as COUNT_NEW from PER_EDUCLEVEL ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_EDUCLEVEL - $PER_EDUCLEVEL - $COUNT_NEW<br>";
		
// สถาบันการศึกษา 73
		$cmd = " DELETE FROM PER_INSTITUTE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " SELECT UNIVER_CODE, UNIVER_NAME, UNIVER_NAME_E, UNIVER_ABB_NAME, 
						  USER_CREATE, to_char(CREATE_DATE,'yyyy-mm-dd') as CREATE_DATE, 
						  USER_UPDATE, to_char(UPDATE_DATE,'yyyy-mm-dd') as UPDATE_DATE 
						  FROM HR_UNIVER_CODE
						  ORDER BY UNIVER_CODE ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		$SEQ_NO = 1;
		while($data = $db_att->get_array()){
			$PER_INSTITUTE++;
			$UNIVER_CODE = trim($data[UNIVER_CODE]);
			$UNIVER_NAME = trim($data[UNIVER_NAME]);
			$UNIVER_NAME_E = trim($data[UNIVER_NAME_E]);
			$UNIVER_ABB_NAME = trim($data[UNIVER_ABB_NAME]);

			$UPDATE_USER = 99999;
			if ($data[USER_UPDATE]) $USER_UPDATE = trim($data[USER_UPDATE]);
			else $USER_UPDATE = trim($data[USER_CREATE]);
			if ($data[UPDATE_DATE]) $UPDATE_DATE = trim($data[UPDATE_DATE]);
			else  $UPDATE_DATE = trim($data[CREATE_DATE]);
			if ($USER_UPDATE) {
				$cmd = " select ID from USER_DETAIL where USERNAME = '$USER_UPDATE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				if ($data2[ID]) $UPDATE_USER = $data2[ID];
			}
			if (!$UPDATE_DATE) $UPDATE_DATE = date("Y-m-d H:i:s");

			$cmd = " INSERT INTO PER_INSTITUTE(INS_CODE, INS_NAME, CT_CODE, INS_ACTIVE, INS_SEQ_NO, UPDATE_USER, UPDATE_DATE)
							 VALUES ('$UNIVER_CODE', '$UNIVER_NAME', '140', 1, $SEQ_NO, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
//			echo "$cmd<br>==================<br>";
//			$db_dpis->show_error();
//			echo "<br>end ". ++$i  ."=======================<br>";
			$SEQ_NO++;
		} // end while						

		$cmd = " select count(INS_CODE) as COUNT_NEW from PER_INSTITUTE ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_INSTITUTE - $PER_INSTITUTE - $COUNT_NEW<br>";
*/
// บัญชีอัตราเงินเดือนลูกจ้าง 593
/*		$cmd = " DELETE FROM PER_LAYEREMP ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " SELECT CLUSTER_CODE, SALARY_LEVEL_CODE, GP_SAL_GOV_YEAR, FLAG_STATUS, 
						  SALARY, SALARY_D, SALARY_H, to_char(EFFECT_DATE,'yyyy-mm-dd') as EFFECT_DATE, 
						  USER_CREATE, to_char(CREATE_DATE,'yyyy-mm-dd') as CREATE_DATE, 
						  USER_UPDATE, to_char(UPDATE_DATE,'yyyy-mm-dd') as UPDATE_DATE 
						  FROM HR_SALARY_EMP
						  WHERE FLAG_STATUS = 1 AND GP_SAL_GOV_YEAR = '2550'
						  ORDER BY CLUSTER_CODE, SALARY_LEVEL_CODE, SALARY ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		$SEQ_NO = 1;
		while($data = $db_att->get_array()){
			$PER_LAYEREMP++;
			$CLUSTER_CODE = trim($data[CLUSTER_CODE]);
			$SALARY_LEVEL_CODE = trim($data[SALARY_LEVEL_CODE]);
			$GP_SAL_GOV_YEAR = trim($data[GP_SAL_GOV_YEAR]);
			$FLAG_STATUS = trim($data[FLAG_STATUS]);
			$SALARY = trim($data[SALARY]);
			$SALARY_D = trim($data[SALARY_D]);
			$SALARY_H = trim($data[SALARY_H]);
			$EFFECT_DATE = trim($data[EFFECT_DATE]);

			$UPDATE_USER = 99999;
			if ($data[USER_UPDATE]) $USER_UPDATE = trim($data[USER_UPDATE]);
			else $USER_UPDATE = trim($data[USER_CREATE]);
			if ($data[UPDATE_DATE]) $UPDATE_DATE = trim($data[UPDATE_DATE]);
			else  $UPDATE_DATE = trim($data[CREATE_DATE]);
			if ($USER_UPDATE) {
				$cmd = " select ID from USER_DETAIL where USERNAME = '$USER_UPDATE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				if ($data2[ID]) $UPDATE_USER = $data2[ID];
			}
			if (!$UPDATE_DATE) $UPDATE_DATE = date("Y-m-d H:i:s");

			$cmd = " INSERT INTO PER_LAYEREMP(PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, 
							LAYERE_HOUR, LAYERE_ACTIVE, LAYERE_SEQ_NO, UPDATE_USER, UPDATE_DATE)
							VALUES ($CLUSTER_CODE, $SALARY_LEVEL_CODE, $SALARY, $SALARY_D, $SALARY_H, 1, $SEQ_NO, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
//			echo "$cmd<br>==================<br>";
//			$db_dpis->show_error();
//			echo "<br>end ". ++$i  ."=======================<br>";
			$SEQ_NO++;
		} // end while						

		$cmd = " select count(PG_CODE) as COUNT_NEW from PER_LAYEREMP ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_LAYEREMP - $PER_LAYEREMP - $COUNT_NEW<br>";
*/		
/*
// ความเชี่ยวชาญ 316 ขาด 9 (ชื่อซ้ำ)
		$cmd = " DELETE FROM PER_SKILL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " SELECT SPECIALIST_CODE, SPECIALIST_NAME, 
						  USER_CREATE, to_char(CREATE_DATE,'yyyy-mm-dd') as CREATE_DATE, 
						  USER_UPDATE, to_char(UPDATE_DATE,'yyyy-mm-dd') as UPDATE_DATE 
						  FROM HR_SPECIALIST_CODE
						  ORDER BY SPECIALIST_CODE ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		$SEQ_NO = 1;
		while($data = $db_att->get_array()){
			$PER_SKILL++;
			$SPECIALIST_CODE = trim($data[SPECIALIST_CODE]);
			$SPECIALIST_NAME = trim($data[SPECIALIST_NAME]);
			$SG_CODE = "990";

			$UPDATE_USER = 99999;
			if ($data[USER_UPDATE]) $USER_UPDATE = trim($data[USER_UPDATE]);
			else $USER_UPDATE = trim($data[USER_CREATE]);
			if ($data[UPDATE_DATE]) $UPDATE_DATE = trim($data[UPDATE_DATE]);
			else  $UPDATE_DATE = trim($data[CREATE_DATE]);
			if ($USER_UPDATE) {
				$cmd = " select ID from USER_DETAIL where USERNAME = '$USER_UPDATE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				if ($data2[ID]) $UPDATE_USER = $data2[ID];
			}
			if (!$UPDATE_DATE) $UPDATE_DATE = date("Y-m-d H:i:s");

			$cmd = " INSERT INTO PER_SKILL(SKILL_CODE, SKILL_NAME, SG_CODE, SKILL_ACTIVE, SKILL_SEQ_NO, UPDATE_USER, UPDATE_DATE)
							 VALUES ('$SPECIALIST_CODE', '$SPECIALIST_NAME', '$SG_CODE', 1, $SEQ_NO, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
//			echo "$cmd<br>==================<br>";
//			$db_dpis->show_error();
//			echo "<br>end ". ++$i  ."=======================<br>";
			$SEQ_NO++;
		} // end while						

		$cmd = " select count(SKILL_CODE) as COUNT_NEW from PER_SKILL ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_SKILL - $PER_SKILL - $COUNT_NEW<br>";
	
// จำนวนขั้นเงินเดือน 14
		$cmd = " DELETE FROM PER_SALARY_MOVMENT ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " SELECT MP_FLAG, MP_FLAG_NAME, MP_FLAG_VALUE, 
						  USER_CREATE, to_char(CREATE_DATE,'yyyy-mm-dd') as CREATE_DATE, 
						  USER_UPDATE, to_char(UPDATE_DATE,'yyyy-mm-dd') as UPDATE_DATE 
						  FROM HR_MP_FLAG
						  ORDER BY MP_FLAG ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		$SEQ_NO = 1;
		while($data = $db_att->get_array()){
			$PER_SALARY_MOVMENT++;
			$MP_FLAG = trim($data[MP_FLAG]);
			$MP_FLAG_NAME = trim($data[MP_FLAG_NAME]);
			$MP_FLAG_VALUE = $data[MP_FLAG_VALUE];
			if (!$MP_FLAG_VALUE) $MP_FLAG_VALUE = "NULL";

			$UPDATE_USER = 99999;
			if ($data[USER_UPDATE]) $USER_UPDATE = trim($data[USER_UPDATE]);
			else $USER_UPDATE = trim($data[USER_CREATE]);
			if ($data[UPDATE_DATE]) $UPDATE_DATE = trim($data[UPDATE_DATE]);
			else  $UPDATE_DATE = trim($data[CREATE_DATE]);
			if ($USER_UPDATE) {
				$cmd = " select ID from USER_DETAIL where USERNAME = '$USER_UPDATE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				if ($data2[ID]) $UPDATE_USER = $data2[ID];
			}
			if (!$UPDATE_DATE) $UPDATE_DATE = date("Y-m-d H:i:s");

			$cmd = " INSERT INTO PER_SALARY_MOVMENT(SM_CODE, SM_NAME, SM_FACTOR, SM_ACTIVE, SM_SEQ_NO, UPDATE_USER, UPDATE_DATE)
							 VALUES ('$MP_FLAG', '$MP_FLAG_NAME', $MP_FLAG_VALUE, 1, $SEQ_NO, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
//			echo "$cmd<br>==================<br>";
//			$db_dpis->show_error();
//			echo "<br>end ". ++$i  ."=======================<br>";
			$SEQ_NO++;
		} // end while						

		$cmd = " select count(SM_CODE) as COUNT_NEW from PER_SALARY_MOVMENT ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_SALARY_MOVMENT - $PER_SALARY_MOVMENT - $COUNT_NEW<br>";
*/
		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_att->free_result();
		$db_att1->free_result();
	} // end if

	if($command=='ORG'){
		$cmd = " ALTER TABLE PER_TRAINING DISABLE CONSTRAINTS FK1_PER_TRAINING ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PUNISHMENT DISABLE CONSTRAINTS FK1_PER_PUNISHMENT ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_DECORDTL DISABLE CONSTRAINTS FK3_PER_DECORDTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_DECORATEHIS DISABLE CONSTRAINTS FK1_PER_DECORATEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_COURSE DISABLE CONSTRAINTS FK4_PER_COURSE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_COURSEDTL DISABLE CONSTRAINTS FK2_PER_COURSEDTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SERVICEHIS DISABLE CONSTRAINTS FK1_PER_SERVICEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SERVICEHIS DISABLE CONSTRAINTS FK5_PER_SERVICEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SALPROMOTE DISABLE CONSTRAINTS FK2_PER_SALPROMOTE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SALARYHIS DISABLE CONSTRAINTS FK1_PER_SALARYHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PROMOTE_P DISABLE CONSTRAINTS FK2_PER_PROMOTE_P ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PROMOTE_E DISABLE CONSTRAINTS FK2_PER_PROMOTE_E ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PROMOTE_C DISABLE CONSTRAINTS FK1_PER_PROMOTE_C ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ORG_JOB DISABLE CONSTRAINTS FK1_PER_ORG_JOB ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_MOVE_REQ DISABLE CONSTRAINTS FK1_PER_MOVE_REQ ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_LETTER DISABLE CONSTRAINTS FK1_PER_LETTER ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_LETTER DISABLE CONSTRAINTS FK2_PER_LETTER ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_BONUSPROMOTE DISABLE CONSTRAINTS FK2_PER_BONUSPROMOTE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_TIMEHIS DISABLE CONSTRAINTS FK1_PER_TIMEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SCHOLAR DISABLE CONSTRAINTS FK1_PER_SCHOLAR ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_REWARDHIS DISABLE CONSTRAINTS FK1_PER_REWARDHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_POSITIONHIS DISABLE CONSTRAINTS FK1_PER_POSITIONHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PERSONALPIC DISABLE CONSTRAINTS FK1_PER_PERSONALPIC ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_NAMEHIS DISABLE CONSTRAINTS FK1_PER_NAMEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_MARRHIS DISABLE CONSTRAINTS FK1_PER_MARRHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_EDUCATE DISABLE CONSTRAINTS FK1_PER_EDUCATE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ABSENTHIS DISABLE CONSTRAINTS FK1_PER_ABSENTHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ABSENT DISABLE CONSTRAINTS FK1_PER_ABSENT ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ABILITY DISABLE CONSTRAINTS FK1_PER_ABILITY ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_INVEST2DTL DISABLE CONSTRAINTS FK2_PER_INVEST2DTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_INVEST1DTL DISABLE CONSTRAINTS FK2_PER_INVEST1DTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_HEIR DISABLE CONSTRAINTS FK1_PER_HEIR ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_EXTRA_INCOMEHIS DISABLE CONSTRAINTS FK1_PER_EXTRA_INCOMEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_EXTRAHIS DISABLE CONSTRAINTS FK1_PER_EXTRAHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_KPI DISABLE CONSTRAINTS FK1_PER_KPI ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_FAMILY DISABLE CONSTRAINTS FK1_PER_FAMILY ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$table = array( "per_absenthis", "per_marrhis", "per_timehis", "per_ability", "per_rewardhis", "per_extrahis", "per_extra_incomehis", "per_namehis", 
			"per_punishment", "per_decoratehis", "per_training", "per_educate", "per_salaryhis", "per_positionhis", "per_personalpic", "per_heir", "per_servicehis", 
			"per_comdtl", "per_bonuspromote", "per_bonusquotadtl2", "per_bonusquotadtl1", "per_bonusquota", "per_special_skill", "per_course", "per_coursedtl", "per_decordtl", "per_org_job", 
			"per_promote_p", "per_letter", "per_move_req", "per_scholar", "per_absent", "per_invest1dtl", "per_promote_c", "per_address", "per_kpi", "per_kpi_form", 
			"per_salpromote", "per_family", "per_personal", "per_pos_move", "per_salquotadtl1", "per_salquotadtl2", "per_salquota", "per_command", "per_decor", 
			"per_order_dtl", "per_req3_dtl" );
		for ( $i=0; $i<count($table); $i++ ) { 
			if ($table[$i]=="per_bonusquota" || $table[$i]=="per_scholar" || $table[$i]=="per_salquota" || $table[$i]=="per_command" || $table[$i]=="per_decor")
				$cmd = " delete from $table[$i] ";
			else
				$cmd = " truncate table $table[$i] ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end for

// OK โครงสร้าง 
		$cmd = " DELETE FROM PER_POSITION ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_POS_EMP ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_POS_EMPSER ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_POS_TEMP ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_SUM ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_ORG' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_ORG WHERE ORG_ID != 1 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_att->free_result();
		$db_att1->free_result();

		$cmd = " select max(ORG_ID) as MAX_ID from PER_ORG ";
		$db_dpis1->send_cmd($cmd);
		$data1 = $db_dpis1->get_array();
		$MAX_ID = $data1[MAX_ID] + 1;

		$ORG_SEQ_NO = 1;
		$cmd = " SELECT DEPARTMENT_CODE, DEPARTMENT_NAME, DEPARTMENT_NAME_S, DEPARTMENT_NAME_E, 
						DEPARTMENT_ABB_NAME, DEPARTMENT_STATUS, DEPARTMENT_CODE_REF, DIVISION_CODE_REF, 
						SECTION_CODE_REF, JOB_CODE_REF, USER_CREATE, to_char(CREATE_DATE,'yyyy-mm-dd') as CREATE_DATE, 
						USER_UPDATE, to_char(UPDATE_DATE,'yyyy-mm-dd') as UPDATE_DATE
						FROM DEPARTMENT_CODE
						ORDER BY DEPARTMENT_CODE ";
		$db_att->send_cmd($cmd);
		//$db_att->show_error();
		//echo "<br>";
		while($data = $db_att->get_array()){
			$DEPARTMENT_CODE = trim($data[DEPARTMENT_CODE]);
			$DEPARTMENT_NAME = trim($data[DEPARTMENT_NAME]);
			$DEPARTMENT_NAME_S = trim($data[DEPARTMENT_NAME_S]);
			$DEPARTMENT_NAME_E = trim($data[DEPARTMENT_NAME_E]);
			$DEPARTMENT_ABB_NAME = trim($data[DEPARTMENT_ABB_NAME]);
			$DEPARTMENT_STATUS = trim($data[DEPARTMENT_STATUS]);
			$DEPARTMENT_CODE_REF = trim($data[DEPARTMENT_CODE_REF]);
			$DIVISION_CODE_REF = trim($data[DIVISION_CODE_REF]);
			$SECTION_CODE_REF = trim($data[SECTION_CODE_REF]);
			$JOB_CODE_REF = trim($data[JOB_CODE_REF]);

			$UPDATE_USER = 99999;
			if ($data[USER_UPDATE]) $USER_UPDATE = trim($data[USER_UPDATE]);
			else $USER_UPDATE = trim($data[USER_CREATE]);
			if ($data[UPDATE_DATE]) $UPDATE_DATE = trim($data[UPDATE_DATE]);
			else  $UPDATE_DATE = trim($data[CREATE_DATE]);
			if ($USER_UPDATE) {
				$cmd = " select ID from USER_DETAIL where USERNAME = '$USER_UPDATE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				if ($data2[ID]) $UPDATE_USER = $data2[ID];
			}
			if (!$UPDATE_DATE) $UPDATE_DATE = date("Y-m-d H:i:s");

//			if ($DEPARTMENT_CODE=="00") $OL_CODE = "01"; 
//			else $OL_CODE = "02"; 
			$OL_CODE = "02"; 
			$OT_CODE = "01"; 
			$AP_CODE = "NULL";
			$PV_CODE = "1000";
			$CT_CODE = "140";
			$ORG_ID_REF = 1;
			if ($DEPARTMENT_STATUS==1) $ORG_ACTIVE = 1;
			else $ORG_ACTIVE = 0;

			$cmd = " INSERT INTO PER_ORG (ORG_ID, ORG_CODE, ORG_NAME, ORG_SHORT, OL_CODE, OT_CODE,  
							AP_CODE, PV_CODE, CT_CODE, ORG_ID_REF, ORG_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							DEPARTMENT_ID, ORG_ENG_NAME, ORG_SEQ_NO)
							VALUES ($MAX_ID, '$DEPARTMENT_CODE', '$DEPARTMENT_NAME', '$DEPARTMENT_ABB_NAME', '$OL_CODE', '$OT_CODE', 
							$AP_CODE, '$PV_CODE', '$CT_CODE', $ORG_ID_REF, $ORG_ACTIVE, $UPDATE_USER, '$UPDATE_DATE', 
							NULL, '$DEPARTMENT_NAME_E', $ORG_SEQ_NO) ";
			$db_dpis->send_cmd($cmd);

			$cmd1 = " SELECT ORG_ID FROM PER_ORG WHERE ORG_ID = $MAX_ID "; 
			$count_data = $db_dpis1->send_cmd($cmd1);
			if (!$count_data) {
				echo "$cmd<br>==================<br>";
				$db_dpis->show_error();
				echo "<br>end ". ++$i  ."=======================<br>";
			}

			$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
							VALUES ('PER_ORG', '$DEPARTMENT_CODE', '$MAX_ID', $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$MAX_ID++;
			$PER_ORG1++;
			$ORG_SEQ_NO++;

		} // end while						

		$cmd = " select count(ORG_ID) as COUNT_NEW from PER_ORG where OL_CODE = '02' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_ORG - $PER_ORG1 - $COUNT_NEW<br>";

		$ORG_SEQ_NO = 1;
		$cmd = " SELECT DEPARTMENT_CODE, DIVISION_CODE, DIVISION_NAME, DIVISION_NAME_S, DIVISION_NAME_E, 
						DIVISION_ABB_NAME, DIVISION_STATUS, DEPARTMENT_CODE_REF, DIVISION_CODE_REF, SECTION_CODE_REF, 
						JOB_CODE_REF, USER_CREATE, to_char(CREATE_DATE,'yyyy-mm-dd') as CREATE_DATE, 
						USER_UPDATE, to_char(UPDATE_DATE,'yyyy-mm-dd') as UPDATE_DATE
						FROM DIVISION_CODE
						ORDER BY DEPARTMENT_CODE, DIVISION_CODE ";
		$db_att->send_cmd($cmd);
		//$db_att->show_error();
		//echo "<br>";
		while($data = $db_att->get_array()){
			$DEPARTMENT_CODE = trim($data[DEPARTMENT_CODE]);
			$DIVISION_CODE = $DEPARTMENT_CODE.trim($data[DIVISION_CODE]);
			$DIVISION_NAME = trim($data[DIVISION_NAME]);
			$DIVISION_NAME_S = trim($data[DIVISION_NAME_S]);
			$DIVISION_NAME_E = trim($data[DIVISION_NAME_E]);
			$DIVISION_ABB_NAME = trim($data[DIVISION_ABB_NAME]);
			$DIVISION_STATUS = trim($data[DIVISION_STATUS]);
			$DEPARTMENT_CODE_REF = trim($data[DEPARTMENT_CODE_REF]);
			$DIVISION_CODE_REF = trim($data[DIVISION_CODE_REF]);
			$SECTION_CODE_REF = trim($data[SECTION_CODE_REF]);
			$JOB_CODE_REF = trim($data[JOB_CODE_REF]);

			$UPDATE_USER = 99999;
			if ($data[USER_UPDATE]) $USER_UPDATE = trim($data[USER_UPDATE]);
			else $USER_UPDATE = trim($data[USER_CREATE]);
			if ($data[UPDATE_DATE]) $UPDATE_DATE = trim($data[UPDATE_DATE]);
			else  $UPDATE_DATE = trim($data[CREATE_DATE]);
			if ($USER_UPDATE) {
				$cmd = " select ID from USER_DETAIL where USERNAME = '$USER_UPDATE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				if ($data2[ID]) $UPDATE_USER = $data2[ID];
			}
			if (!$UPDATE_DATE) $UPDATE_DATE = date("Y-m-d H:i:s");

			$OL_CODE = "03"; 
			$OT_CODE = "01"; 
			$AP_CODE = "NULL";
			$PV_CODE = "1000";
			$CT_CODE = "140";
			if ($DIVISION_STATUS==1) $ORG_ACTIVE = 1;
			else $ORG_ACTIVE = 0;

			$cmd = " select NEW_CODE from PER_MAP_CODE 
							  where MAP_CODE = 'PER_ORG' AND OLD_CODE = '$DEPARTMENT_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_ID_REF = $data2[NEW_CODE] + 0;
			$DEPARTMENT_ID = $ORG_ID_REF;

			$cmd = " INSERT INTO PER_ORG (ORG_ID, ORG_CODE, ORG_NAME, ORG_SHORT, OL_CODE, OT_CODE,  
							AP_CODE, PV_CODE, CT_CODE, ORG_ID_REF, ORG_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							DEPARTMENT_ID, ORG_ENG_NAME, ORG_SEQ_NO)
							VALUES ($MAX_ID, '$DIVISION_CODE', '$DIVISION_NAME', '$DIVISION_ABB_NAME', '$OL_CODE', '$OT_CODE',  
							$AP_CODE, '$PV_CODE', '$CT_CODE', $ORG_ID_REF, $ORG_ACTIVE, $UPDATE_USER, '$UPDATE_DATE', 
							$DEPARTMENT_ID, '$DIVISION_NAME_E', $ORG_SEQ_NO) ";
			$db_dpis->send_cmd($cmd);

			$cmd1 = " SELECT ORG_ID FROM PER_ORG WHERE ORG_ID = $MAX_ID "; 
			$count_data = $db_dpis1->send_cmd($cmd1);
			if (!$count_data) {
				echo "$cmd<br>==================<br>";
				$db_dpis->show_error();
				echo "<br>end ". ++$i  ."=======================<br>";
			}

			$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
							VALUES ('PER_ORG', '$DIVISION_CODE', '$MAX_ID', $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$MAX_ID++;
			$PER_ORG2++;
			$ORG_SEQ_NO++;

		} // end while						

		$cmd = " select count(ORG_ID) as COUNT_NEW from PER_ORG where OL_CODE = '03' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_ORG - $PER_ORG2 - $COUNT_NEW<br>";

		$ORG_SEQ_NO = 1;
		$cmd = " SELECT DEPARTMENT_CODE, DIVISION_CODE, SECTION_CODE, SECTION_NAME, SECTION_NAME_S, SECTION_NAME_E, 
						SECTION_ABB_NAME, SECTION_STATUS, DEPARTMENT_CODE_REF, DIVISION_CODE_REF, SECTION_CODE_REF, 
						JOB_CODE_REF, USER_CREATE, to_char(CREATE_DATE,'yyyy-mm-dd') as CREATE_DATE, 
						USER_UPDATE, to_char(UPDATE_DATE,'yyyy-mm-dd') as UPDATE_DATE
						FROM SECTION_CODE
						ORDER BY DEPARTMENT_CODE, DIVISION_CODE, SECTION_CODE ";
		$db_att->send_cmd($cmd);
		//$db_att->show_error();
		//echo "<br>";
		while($data = $db_att->get_array()){
			$DEPARTMENT_CODE = trim($data[DEPARTMENT_CODE]);
			$DIVISION_CODE = $DEPARTMENT_CODE.trim($data[DIVISION_CODE]);
			$SECTION_CODE = $DIVISION_CODE.trim($data[SECTION_CODE]);
			$SECTION_NAME = trim($data[SECTION_NAME]);
			$SECTION_NAME_S = trim($data[SECTION_NAME_S]);
			$SECTION_NAME_E = trim($data[SECTION_NAME_E]);
			$SECTION_ABB_NAME = trim($data[SECTION_ABB_NAME]);
			$SECTION_STATUS = trim($data[SECTION_STATUS]);
			$DEPARTMENT_CODE_REF = trim($data[DEPARTMENT_CODE_REF]);
			$DIVISION_CODE_REF = trim($data[DIVISION_CODE_REF]);
			$SECTION_CODE_REF = trim($data[SECTION_CODE_REF]);
			$JOB_CODE_REF = trim($data[JOB_CODE_REF]);
			if (!$SECTION_NAME) $SECTION_NAME = "-";

			$UPDATE_USER = 99999;
			if ($data[USER_UPDATE]) $USER_UPDATE = trim($data[USER_UPDATE]);
			else $USER_UPDATE = trim($data[USER_CREATE]);
			if ($data[UPDATE_DATE]) $UPDATE_DATE = trim($data[UPDATE_DATE]);
			else  $UPDATE_DATE = trim($data[CREATE_DATE]);
			if ($USER_UPDATE) {
				$cmd = " select ID from USER_DETAIL where USERNAME = '$USER_UPDATE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				if ($data2[ID]) $UPDATE_USER = $data2[ID];
			}
			if (!$UPDATE_DATE) $UPDATE_DATE = date("Y-m-d H:i:s");

			$OL_CODE = "04"; 
			$OT_CODE = "01"; 
			$AP_CODE = "NULL";
			$PV_CODE = "1000";
			$CT_CODE = "140";
			if ($SECTION_STATUS==1) $ORG_ACTIVE = 1;
			else $ORG_ACTIVE = 0;

			$cmd = " select NEW_CODE from PER_MAP_CODE 
							  where MAP_CODE = 'PER_ORG' AND OLD_CODE = '$DEPARTMENT_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$DEPARTMENT_ID = $data2[NEW_CODE] + 0;

			$cmd = " select NEW_CODE from PER_MAP_CODE 
							  where MAP_CODE = 'PER_ORG' AND OLD_CODE = '$DIVISION_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_ID_REF = $data2[NEW_CODE] + 0;

			$cmd = " INSERT INTO PER_ORG (ORG_ID, ORG_CODE, ORG_NAME, ORG_SHORT, OL_CODE, OT_CODE,  
							AP_CODE, PV_CODE, CT_CODE, ORG_ID_REF, ORG_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							DEPARTMENT_ID, ORG_ENG_NAME, ORG_SEQ_NO)
							VALUES ($MAX_ID, '$SECTION_CODE', '$SECTION_NAME', '$SECTION_ABB_NAME', '$OL_CODE', '$OT_CODE',  
							$AP_CODE, '$PV_CODE', '$CT_CODE', $ORG_ID_REF, $ORG_ACTIVE, $UPDATE_USER, '$UPDATE_DATE', 
							$DEPARTMENT_ID, '$SECTION_NAME_E', $ORG_SEQ_NO) ";
			$db_dpis->send_cmd($cmd);

			$cmd1 = " SELECT ORG_ID FROM PER_ORG WHERE ORG_ID = $MAX_ID "; 
			$count_data = $db_dpis1->send_cmd($cmd1);
			if (!$count_data) {
				echo "$cmd<br>==================<br>";
				$db_dpis->show_error();
				echo "<br>end ". ++$i  ."=======================<br>";
			}

			$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
							VALUES ('PER_ORG', '$SECTION_CODE', '$MAX_ID', $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$MAX_ID++;
			$PER_ORG3++;
			$ORG_SEQ_NO++;

		} // end while						

		$cmd = " select count(ORG_ID) as COUNT_NEW from PER_ORG where OL_CODE = '04' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_ORG - $PER_ORG3 - $COUNT_NEW<br>";

		$ORG_SEQ_NO = 1;
		$cmd = " SELECT DEPARTMENT_CODE, DIVISION_CODE, SECTION_CODE, JOB_CODE, JOB_NAME, JOB_NAME_S, 
						JOB_ABB_NAME, JOB_STATUS, DEPARTMENT_CODE_REF, DIVISION_CODE_REF, SECTION_CODE_REF, 
						JOB_CODE_REF, USER_CREATE, to_char(CREATE_DATE,'yyyy-mm-dd') as CREATE_DATE, 
						USER_UPDATE, to_char(UPDATE_DATE,'yyyy-mm-dd') as UPDATE_DATE
						FROM JOB_CODE
						ORDER BY DEPARTMENT_CODE, DIVISION_CODE, SECTION_CODE, JOB_CODE ";
		$db_att->send_cmd($cmd);
		//$db_att->show_error();
		//echo "<br>";
		while($data = $db_att->get_array()){
			$DEPARTMENT_CODE = trim($data[DEPARTMENT_CODE]);
			$DIVISION_CODE = $DEPARTMENT_CODE.trim($data[DIVISION_CODE]);
			$SECTION_CODE = $DIVISION_CODE.trim($data[SECTION_CODE]);
			$JOB_CODE = $SECTION_CODE.trim($data[JOB_CODE]);
			$JOB_NAME = trim($data[JOB_NAME]);
			$JOB_NAME_S = trim($data[JOB_NAME_S]);
			$JOB_ABB_NAME = trim($data[JOB_ABB_NAME]);
			$JOB_STATUS = trim($data[JOB_STATUS]);
			$DEPARTMENT_CODE_REF = trim($data[DEPARTMENT_CODE_REF]);
			$DIVISION_CODE_REF = trim($data[DIVISION_CODE_REF]);
			$SECTION_CODE_REF = trim($data[SECTION_CODE_REF]);
			$JOB_CODE_REF = trim($data[JOB_CODE_REF]);
			if (!$JOB_NAME) $JOB_NAME = "-";

			$UPDATE_USER = 99999;
			if ($data[USER_UPDATE]) $USER_UPDATE = trim($data[USER_UPDATE]);
			else $USER_UPDATE = trim($data[USER_CREATE]);
			if ($data[UPDATE_DATE]) $UPDATE_DATE = trim($data[UPDATE_DATE]);
			else  $UPDATE_DATE = trim($data[CREATE_DATE]);
			if ($USER_UPDATE) {
				$cmd = " select ID from USER_DETAIL where USERNAME = '$USER_UPDATE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				if ($data2[ID]) $UPDATE_USER = $data2[ID];
			}
			if (!$UPDATE_DATE) $UPDATE_DATE = date("Y-m-d H:i:s");

			$OL_CODE = "05"; 
			$OT_CODE = "01"; 
			$AP_CODE = "NULL";
			$PV_CODE = "1000";
			$CT_CODE = "140";
			if ($JOB_STATUS==1) $ORG_ACTIVE = 1;
			else $ORG_ACTIVE = 0;

			$cmd = " select NEW_CODE from PER_MAP_CODE 
							  where MAP_CODE = 'PER_ORG' AND OLD_CODE = '$DEPARTMENT_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$DEPARTMENT_ID = $data2[NEW_CODE] + 0;

			$cmd = " select NEW_CODE from PER_MAP_CODE 
							  where MAP_CODE = 'PER_ORG' AND OLD_CODE = '$SECTION_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_ID_REF = $data2[NEW_CODE] + 0;

			$cmd = " INSERT INTO PER_ORG (ORG_ID, ORG_CODE, ORG_NAME, ORG_SHORT, OL_CODE, OT_CODE, 
							AP_CODE, PV_CODE, CT_CODE, ORG_ID_REF, ORG_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							DEPARTMENT_ID, ORG_ENG_NAME, ORG_SEQ_NO)
							VALUES ($MAX_ID, '$JOB_CODE', '$JOB_NAME', '$JOB_ABB_NAME', '$OL_CODE', '$OT_CODE',
							$AP_CODE, '$PV_CODE', '$CT_CODE', $ORG_ID_REF, $ORG_ACTIVE, $UPDATE_USER, '$UPDATE_DATE', 
							$DEPARTMENT_ID, '$JOB_NAME', $ORG_SEQ_NO) ";
			$db_dpis->send_cmd($cmd);

			$cmd1 = " SELECT ORG_ID FROM PER_ORG WHERE ORG_ID = $MAX_ID "; 
			$count_data = $db_dpis1->send_cmd($cmd1);
			if (!$count_data) {
				echo "$cmd<br>==================<br>";
				$db_dpis->show_error();
				echo "<br>end ". ++$i  ."=======================<br>";
			}

			$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
							VALUES ('PER_ORG', '$JOB_CODE', '$MAX_ID', $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$MAX_ID++;
			$PER_ORG4++;
			$ORG_SEQ_NO++;

		} // end while						

		$cmd = " select count(ORG_ID) as COUNT_NEW from PER_ORG where OL_CODE = '05' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_ORG - $PER_ORG4 - $COUNT_NEW<br>";

		$cmd = " ALTER TABLE PER_TRAINING ENABLE CONSTRAINTS FK1_PER_TRAINING ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PUNISHMENT ENABLE CONSTRAINTS FK1_PER_PUNISHMENT ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_DECORDTL ENABLE CONSTRAINTS FK3_PER_DECORDTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_DECORATEHIS ENABLE CONSTRAINTS FK1_PER_DECORATEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_COURSE ENABLE CONSTRAINTS FK4_PER_COURSE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_COURSEDTL ENABLE CONSTRAINTS FK2_PER_COURSEDTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SERVICEHIS ENABLE CONSTRAINTS FK1_PER_SERVICEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SERVICEHIS ENABLE CONSTRAINTS FK5_PER_SERVICEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SALPROMOTE ENABLE CONSTRAINTS FK2_PER_SALPROMOTE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SALARYHIS ENABLE CONSTRAINTS FK1_PER_SALARYHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PROMOTE_P ENABLE CONSTRAINTS FK2_PER_PROMOTE_P ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PROMOTE_E ENABLE CONSTRAINTS FK2_PER_PROMOTE_E ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PROMOTE_C ENABLE CONSTRAINTS FK1_PER_PROMOTE_C ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ORG_JOB ENABLE CONSTRAINTS FK1_PER_ORG_JOB ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_MOVE_REQ ENABLE CONSTRAINTS FK1_PER_MOVE_REQ ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_LETTER ENABLE CONSTRAINTS FK1_PER_LETTER ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_LETTER ENABLE CONSTRAINTS FK2_PER_LETTER ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_BONUSPROMOTE ENABLE CONSTRAINTS FK2_PER_BONUSPROMOTE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_TIMEHIS ENABLE CONSTRAINTS FK1_PER_TIMEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SCHOLAR ENABLE CONSTRAINTS FK1_PER_SCHOLAR ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_REWARDHIS ENABLE CONSTRAINTS FK1_PER_REWARDHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_POSITIONHIS ENABLE CONSTRAINTS FK1_PER_POSITIONHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PERSONALPIC ENABLE CONSTRAINTS FK1_PER_PERSONALPIC ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_NAMEHIS ENABLE CONSTRAINTS FK1_PER_NAMEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_MARRHIS ENABLE CONSTRAINTS FK1_PER_MARRHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_EDUCATE ENABLE CONSTRAINTS FK1_PER_EDUCATE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ABSENTHIS ENABLE CONSTRAINTS FK1_PER_ABSENTHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ABSENT ENABLE CONSTRAINTS FK1_PER_ABSENT ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ABILITY ENABLE CONSTRAINTS FK1_PER_ABILITY ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_INVEST2DTL ENABLE CONSTRAINTS FK2_PER_INVEST2DTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_INVEST1DTL ENABLE CONSTRAINTS FK2_PER_INVEST1DTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_HEIR ENABLE CONSTRAINTS FK1_PER_HEIR ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_EXTRA_INCOMEHIS ENABLE CONSTRAINTS FK1_PER_EXTRA_INCOMEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_EXTRAHIS ENABLE CONSTRAINTS FK1_PER_EXTRAHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_KPI ENABLE CONSTRAINTS FK1_PER_KPI ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_FAMILY ENABLE CONSTRAINTS FK1_PER_FAMILY ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_att->free_result();
		$db_att1->free_result();
	} // end if
	
	if($command=='POSITION'){
		$cmd = " ALTER TABLE PER_TRAINING DISABLE CONSTRAINTS FK1_PER_TRAINING ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PUNISHMENT DISABLE CONSTRAINTS FK1_PER_PUNISHMENT ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_DECORDTL DISABLE CONSTRAINTS FK3_PER_DECORDTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_DECORATEHIS DISABLE CONSTRAINTS FK1_PER_DECORATEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_COURSE DISABLE CONSTRAINTS FK4_PER_COURSE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_COURSEDTL DISABLE CONSTRAINTS FK2_PER_COURSEDTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SERVICEHIS DISABLE CONSTRAINTS FK1_PER_SERVICEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SERVICEHIS DISABLE CONSTRAINTS FK5_PER_SERVICEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SALPROMOTE DISABLE CONSTRAINTS FK2_PER_SALPROMOTE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SALARYHIS DISABLE CONSTRAINTS FK1_PER_SALARYHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PROMOTE_P DISABLE CONSTRAINTS FK2_PER_PROMOTE_P ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PROMOTE_E DISABLE CONSTRAINTS FK2_PER_PROMOTE_E ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PROMOTE_C DISABLE CONSTRAINTS FK1_PER_PROMOTE_C ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ORG_JOB DISABLE CONSTRAINTS FK1_PER_ORG_JOB ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_MOVE_REQ DISABLE CONSTRAINTS FK1_PER_MOVE_REQ ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_LETTER DISABLE CONSTRAINTS FK1_PER_LETTER ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_LETTER DISABLE CONSTRAINTS FK2_PER_LETTER ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_BONUSPROMOTE DISABLE CONSTRAINTS FK2_PER_BONUSPROMOTE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_TIMEHIS DISABLE CONSTRAINTS FK1_PER_TIMEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SCHOLAR DISABLE CONSTRAINTS FK1_PER_SCHOLAR ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_REWARDHIS DISABLE CONSTRAINTS FK1_PER_REWARDHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_POSITIONHIS DISABLE CONSTRAINTS FK1_PER_POSITIONHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PERSONALPIC DISABLE CONSTRAINTS FK1_PER_PERSONALPIC ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_NAMEHIS DISABLE CONSTRAINTS FK1_PER_NAMEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_MARRHIS DISABLE CONSTRAINTS FK1_PER_MARRHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_EDUCATE DISABLE CONSTRAINTS FK1_PER_EDUCATE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ABSENTHIS DISABLE CONSTRAINTS FK1_PER_ABSENTHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ABSENT DISABLE CONSTRAINTS FK1_PER_ABSENT ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ABILITY DISABLE CONSTRAINTS FK1_PER_ABILITY ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_INVEST2DTL DISABLE CONSTRAINTS FK2_PER_INVEST2DTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_INVEST1DTL DISABLE CONSTRAINTS FK2_PER_INVEST1DTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_HEIR DISABLE CONSTRAINTS FK1_PER_HEIR ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_EXTRA_INCOMEHIS DISABLE CONSTRAINTS FK1_PER_EXTRA_INCOMEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_EXTRAHIS DISABLE CONSTRAINTS FK1_PER_EXTRAHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_KPI DISABLE CONSTRAINTS FK1_PER_KPI ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_FAMILY DISABLE CONSTRAINTS FK1_PER_FAMILY ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$table = array( "per_absenthis", "per_marrhis", "per_timehis", "per_ability", "per_rewardhis", "per_extrahis", "per_extra_incomehis", "per_namehis", 
			"per_punishment", "per_decoratehis", "per_training", "per_educate", "per_salaryhis", "per_positionhis", "per_personalpic", "per_heir", "per_servicehis", 
			"per_comdtl", "per_bonuspromote", "per_bonusquotadtl2", "per_bonusquotadtl1", "per_bonusquota", "per_special_skill", "per_coursedtl", "per_decordtl", "per_org_job", 
			"per_promote_p", "per_letter", "per_move_req", "per_scholar", "per_absent", "per_invest1dtl", "per_promote_c", "per_address", "per_kpi_form", 
			"per_salpromote", "per_family", "per_personal", "per_pos_move", "per_salquotadtl1", "per_salquotadtl2", "per_salquota", "per_command", "per_decor", 
			"per_order_dtl", "per_req3_dtl" );
		for ( $i=0; $i<count($table); $i++ ) { 
			if ($table[$i]=="per_bonusquota" || $table[$i]=="per_scholar" || $table[$i]=="per_salquota" || $table[$i]=="per_command" || $table[$i]=="per_decor")
				$cmd = " delete from $table[$i] ";
			else
				$cmd = " truncate table $table[$i] ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end for

// ตำแหน่งข้าราชการ + ครู 50172
// ตำแหน่งข้าราชการ 27434

		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_POSITION' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_POSITION ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_att->free_result();
		$db_att1->free_result();

		$POS_ID = 1;

		$cmd = " SELECT POS_NUM_NAME, POS_NUM_CODE, WORK_LINE_CODE, REC_STATUS, 
						DEPARTMENT_CODE, DIVISION_CODE, SECTION_CODE, JOB_CODE, JOB_NAME, 
						SECTION_NAME, DIVISION_NAME, DEPARTMENT_NAME, WORK_LINE_NAME, 
						ADMIN_CODE, ADMIN_NAME, MP_CEE, MP_CEE_COMBINE, TECH_CLASS1, 
						TECH_CLASS2, TECH_CLASS3, CUTOFF_LIST, CONDITION_LIST, EXCEED_LIST, 
						WAIT_LIST, FLAG_PERSON_TYPE, POSITION_TYPE, HQ_REF_CODE, HQ_REF_ID, 
						HQ_REF_YEAR, to_char(HQ_REF_YMD,'yyyy-mm-dd') as HQ_REF_YMD, SKILLFUL, 
						REMARK, FLAG_HOLDER_CODE, POSITION_CATG, WAIT_LIST_DEPT, REMARK1, 
						USER_CREATE, to_char(CREATE_DATE,'yyyy-mm-dd') as CREATE_DATE, 
						USER_UPDATE, to_char(UPDATE_DATE,'yyyy-mm-dd') as UPDATE_DATE
						FROM HR_RATE_OFFICER 
						WHERE FLAG_PERSON_TYPE!=3
						ORDER BY POS_NUM_NAME, POS_NUM_CODE ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "$cmd<br>";
		while($data = $db_att->get_array()){
			$FLAG_PERSON_TYPE = trim($data[FLAG_PERSON_TYPE]);
			if ($FLAG_PERSON_TYPE==1) $PER_POSITION1++;
			elseif ($FLAG_PERSON_TYPE==2) $PER_POSITION2++;
			elseif ($FLAG_PERSON_TYPE==3) $PER_POSITION3++;
			$POS_NO_NAME = trim($data[POS_NUM_NAME]);
			$POS_NO = trim($data[POS_NUM_CODE]);
			$PL_CODE = trim($data[WORK_LINE_CODE]);
			$POS_STATUS = trim($data[REC_STATUS]);
			$DEPARTMENT_CODE = trim($data[DEPARTMENT_CODE]);
			$DIVISION_CODE = $DEPARTMENT_CODE.trim($data[DIVISION_CODE]);
			$SECTION_CODE = $DIVISION_CODE.trim($data[SECTION_CODE]);
			$JOB_CODE = $SECTION_CODE.trim($data[JOB_CODE]);
			$PM_CODE = trim($data[ADMIN_CODE]);
			$CL_NAME = trim($data[MP_CEE]);
			$MP_CEE_COMBINE = trim($data[MP_CEE_COMBINE]);
			$TECH_CLASS1 = trim($data[TECH_CLASS1]); // ระดับควบ /
			$TECH_CLASS2 = trim($data[TECH_CLASS2]);
			$TECH_CLASS3 = trim($data[TECH_CLASS3]);
			$PT_CODE = trim($data[POSITION_TYPE]);
			$POS_REMARK = trim($data[REMARK]);
			if ($PM_CODE=='ั' || $PM_CODE=='้') $PM_CODE = "";

			$OT_CODE = $FLAG_PERSON_TYPE;
			$POS_SALARY = 0;
			$POS_MGTSALARY = 0;
			$PM_CODE = trim($PM_CODE)? "'".$PM_CODE."'" : "NULL";

			$UPDATE_USER = 99999;
			if ($data[USER_UPDATE]) $USER_UPDATE = trim($data[USER_UPDATE]);
			else $USER_UPDATE = trim($data[USER_CREATE]);
			if ($data[UPDATE_DATE]) $UPDATE_DATE = trim($data[UPDATE_DATE]);
			else  $UPDATE_DATE = trim($data[CREATE_DATE]);
			if ($USER_UPDATE) {
				$cmd = " select ID from USER_DETAIL where USERNAME = '$USER_UPDATE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				if ($data2[ID]) $UPDATE_USER = $data2[ID];
			}
			if (!$UPDATE_DATE) $UPDATE_DATE = date("Y-m-d H:i:s");

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = '$DEPARTMENT_CODE' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$DEPARTMENT_ID = $data2[NEW_CODE];

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = '$DIVISION_CODE' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$ORG_ID = $data2[NEW_CODE];

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = '$SECTION_CODE' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$ORG_ID_1 = $data2[NEW_CODE];

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = '$JOB_CODE' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$ORG_ID_2 = $data2[NEW_CODE];

			if (!$ORG_ID) $ORG_ID = 1;
			if (!$ORG_ID_1) $ORG_ID_1 = "NULL";
			if (!$ORG_ID_2) $ORG_ID_2 = "NULL";

			$cmd = " INSERT INTO PER_POSITION (POS_ID, ORG_ID, POS_NO, OT_CODE, ORG_ID_1, ORG_ID_2, PM_CODE, PL_CODE, 
							CL_NAME, POS_SALARY, POS_MGTSALARY, PT_CODE, POS_CONDITION, POS_REMARK, POS_DATE, 
							POS_GET_DATE, POS_CHANGE_DATE, POS_STATUS, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID, 
							LEVEL_NO, POS_NO_NAME)
							VALUES ($POS_ID, $ORG_ID, '$POS_NO', '$OT_CODE', $ORG_ID_1, $ORG_ID_2, $PM_CODE, '$PL_CODE', 
							'$CL_NAME', $POS_SALARY, $POS_MGTSALARY, '$PT_CODE', '$POS_CONDITION', '$POS_REMARK', 
							'$START_DATE', '$POS_GET_DATE', '$START_DATE', $POS_STATUS, $UPDATE_USER, '$UPDATE_DATE', 
							$DEPARTMENT_ID, '$LEVEL_NO', '$POS_NO_NAME') ";
			$db_dpis->send_cmd($cmd);

			$cmd1 = " SELECT POS_ID FROM PER_POSITION WHERE POS_ID = $POS_ID "; 
			$count_data = $db_dpis1->send_cmd($cmd1);
			if (!$count_data) {
				echo "$cmd<br>==================<br>";
				$db_dpis->show_error();
				echo "<br>end ". ++$i  ."=======================<br>";
			}

			$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
							VALUES ('PER_POSITION', '$POS_NO_NAME$POS_NO', '$POS_ID', $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$POS_ID++;
		} // end while						

		$cmd = " select count(POS_ID) as COUNT_NEW from PER_POSITION ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_POSITION - $PER_POSITION1 + $PER_POSITION2 + $PER_POSITION3 = $COUNT_NEW<br>";

		$cmd = " ALTER TABLE PER_TRAINING ENABLE CONSTRAINTS FK1_PER_TRAINING ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PUNISHMENT ENABLE CONSTRAINTS FK1_PER_PUNISHMENT ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_DECORDTL ENABLE CONSTRAINTS FK3_PER_DECORDTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_DECORATEHIS ENABLE CONSTRAINTS FK1_PER_DECORATEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_COURSE ENABLE CONSTRAINTS FK4_PER_COURSE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_COURSEDTL ENABLE CONSTRAINTS FK2_PER_COURSEDTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SERVICEHIS ENABLE CONSTRAINTS FK1_PER_SERVICEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SERVICEHIS ENABLE CONSTRAINTS FK5_PER_SERVICEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SALPROMOTE ENABLE CONSTRAINTS FK2_PER_SALPROMOTE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SALARYHIS ENABLE CONSTRAINTS FK1_PER_SALARYHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PROMOTE_P ENABLE CONSTRAINTS FK2_PER_PROMOTE_P ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PROMOTE_E ENABLE CONSTRAINTS FK2_PER_PROMOTE_E ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PROMOTE_C ENABLE CONSTRAINTS FK1_PER_PROMOTE_C ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ORG_JOB ENABLE CONSTRAINTS FK1_PER_ORG_JOB ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_MOVE_REQ ENABLE CONSTRAINTS FK1_PER_MOVE_REQ ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_LETTER ENABLE CONSTRAINTS FK1_PER_LETTER ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_LETTER ENABLE CONSTRAINTS FK2_PER_LETTER ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_BONUSPROMOTE ENABLE CONSTRAINTS FK2_PER_BONUSPROMOTE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_TIMEHIS ENABLE CONSTRAINTS FK1_PER_TIMEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SCHOLAR ENABLE CONSTRAINTS FK1_PER_SCHOLAR ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_REWARDHIS ENABLE CONSTRAINTS FK1_PER_REWARDHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_POSITIONHIS ENABLE CONSTRAINTS FK1_PER_POSITIONHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PERSONALPIC ENABLE CONSTRAINTS FK1_PER_PERSONALPIC ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_NAMEHIS ENABLE CONSTRAINTS FK1_PER_NAMEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_MARRHIS ENABLE CONSTRAINTS FK1_PER_MARRHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_EDUCATE ENABLE CONSTRAINTS FK1_PER_EDUCATE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ABSENTHIS ENABLE CONSTRAINTS FK1_PER_ABSENTHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ABSENT ENABLE CONSTRAINTS FK1_PER_ABSENT ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ABILITY ENABLE CONSTRAINTS FK1_PER_ABILITY ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_INVEST2DTL ENABLE CONSTRAINTS FK2_PER_INVEST2DTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_INVEST1DTL ENABLE CONSTRAINTS FK2_PER_INVEST1DTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_HEIR ENABLE CONSTRAINTS FK1_PER_HEIR ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_EXTRA_INCOMEHIS ENABLE CONSTRAINTS FK1_PER_EXTRA_INCOMEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_EXTRAHIS ENABLE CONSTRAINTS FK1_PER_EXTRAHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_KPI ENABLE CONSTRAINTS FK1_PER_KPI ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_FAMILY ENABLE CONSTRAINTS FK1_PER_FAMILY ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_att->free_result();
		$db_att1->free_result();
	} // end if

	if($command=='POS_EMP'){
		$cmd = " ALTER TABLE PER_TRAINING DISABLE CONSTRAINTS FK1_PER_TRAINING ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PUNISHMENT DISABLE CONSTRAINTS FK1_PER_PUNISHMENT ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_DECORDTL DISABLE CONSTRAINTS FK3_PER_DECORDTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_DECORATEHIS DISABLE CONSTRAINTS FK1_PER_DECORATEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_COURSE DISABLE CONSTRAINTS FK4_PER_COURSE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_COURSEDTL DISABLE CONSTRAINTS FK2_PER_COURSEDTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SERVICEHIS DISABLE CONSTRAINTS FK1_PER_SERVICEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SERVICEHIS DISABLE CONSTRAINTS FK5_PER_SERVICEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SALPROMOTE DISABLE CONSTRAINTS FK2_PER_SALPROMOTE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SALARYHIS DISABLE CONSTRAINTS FK1_PER_SALARYHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PROMOTE_P DISABLE CONSTRAINTS FK2_PER_PROMOTE_P ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PROMOTE_E DISABLE CONSTRAINTS FK2_PER_PROMOTE_E ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PROMOTE_C DISABLE CONSTRAINTS FK1_PER_PROMOTE_C ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ORG_JOB DISABLE CONSTRAINTS FK1_PER_ORG_JOB ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_MOVE_REQ DISABLE CONSTRAINTS FK1_PER_MOVE_REQ ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_LETTER DISABLE CONSTRAINTS FK1_PER_LETTER ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_LETTER DISABLE CONSTRAINTS FK2_PER_LETTER ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_BONUSPROMOTE DISABLE CONSTRAINTS FK2_PER_BONUSPROMOTE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_TIMEHIS DISABLE CONSTRAINTS FK1_PER_TIMEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SCHOLAR DISABLE CONSTRAINTS FK1_PER_SCHOLAR ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_REWARDHIS DISABLE CONSTRAINTS FK1_PER_REWARDHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_POSITIONHIS DISABLE CONSTRAINTS FK1_PER_POSITIONHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PERSONALPIC DISABLE CONSTRAINTS FK1_PER_PERSONALPIC ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_NAMEHIS DISABLE CONSTRAINTS FK1_PER_NAMEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_MARRHIS DISABLE CONSTRAINTS FK1_PER_MARRHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_EDUCATE DISABLE CONSTRAINTS FK1_PER_EDUCATE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ABSENTHIS DISABLE CONSTRAINTS FK1_PER_ABSENTHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ABSENT DISABLE CONSTRAINTS FK1_PER_ABSENT ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ABILITY DISABLE CONSTRAINTS FK1_PER_ABILITY ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_INVEST2DTL DISABLE CONSTRAINTS FK2_PER_INVEST2DTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_INVEST1DTL DISABLE CONSTRAINTS FK2_PER_INVEST1DTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_HEIR DISABLE CONSTRAINTS FK1_PER_HEIR ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_EXTRA_INCOMEHIS DISABLE CONSTRAINTS FK1_PER_EXTRA_INCOMEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_EXTRAHIS DISABLE CONSTRAINTS FK1_PER_EXTRAHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_KPI DISABLE CONSTRAINTS FK1_PER_KPI ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_FAMILY DISABLE CONSTRAINTS FK1_PER_FAMILY ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$table = array( "per_absenthis", "per_marrhis", "per_timehis", "per_ability", "per_rewardhis", "per_extrahis", "per_extra_incomehis", "per_namehis", 
			"per_punishment", "per_decoratehis", "per_training", "per_educate", "per_salaryhis", "per_positionhis", "per_personalpic", "per_heir", "per_servicehis", 
			"per_comdtl", "per_bonuspromote", "per_bonusquotadtl2", "per_bonusquotadtl1", "per_bonusquota", "per_special_skill", "per_coursedtl", "per_decordtl", "per_org_job", 
			"per_promote_p", "per_letter", "per_move_req", "per_scholar", "per_absent", "per_invest1dtl", "per_promote_c", "per_address", "per_kpi_form", 
			"per_salpromote", "per_family", "per_personal", "per_pos_move", "per_salquotadtl1", "per_salquotadtl2", "per_salquota", "per_command", "per_decor", 
			"per_order_dtl", "per_req3_dtl" );
		for ( $i=0; $i<count($table); $i++ ) { 
			if ($table[$i]=="per_bonusquota" || $table[$i]=="per_scholar" || $table[$i]=="per_salquota" || $table[$i]=="per_command" || $table[$i]=="per_decor")
				$cmd = " delete from $table[$i] ";
			else
				$cmd = " truncate table $table[$i] ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end for

// ตำแหน่งลูกจ้างประจำ 47358 - 47358

		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_POS_EMP' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_POS_EMP ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_att->free_result();
		$db_att1->free_result();

		$cmd = " select max(POEM_ID) as MAX_ID from PER_POS_EMP ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$POEM_ID = $data[MAX_ID] + 1;

		$cmd = " SELECT POS_NUM_NAME, POS_NUM_CODE, REC_STATUS, DEPARTMENT_CODE, DIVISION_CODE, 
						SECTION_CODE, JOB_CODE, CLUSTER_CODE, CATEGORY_SAL_CODE, WORK_LINE_CODE, 
						BOARD_TYPE, FLAG_HOLDER_CODE, SALARY_START_M, SALARY_START_D, SALARY_END_M, 
						SALARY_END_D, SALARY_LEVEL_CODE, CATEGORY_SAL_NAME, WORK_LINE_NAME, JOB_NAME, 
						SECTION_NAME, DIVISION_NAME, DEPARTMENT_NAME, HQ_REF_DEPT_ID, HQ_REF_CODE, 
						HQ_REF_ID, HQ_REF_YEAR, to_char(HQ_REF_YMD,'yyyy-mm-dd') as HQ_REF_YMD, REMARK, 
						LAST_POSITION_HIRE, LINE_RANK_REPLACE, POSITION_ID_REPLACE, REDUCED_AMOUNT, 
						POSITION_NO_REPLACE, POS_NUM_NAME_OFF, POS_NUM_CODE_OFF, FLAG_OFF, 
						USER_CREATE, to_char(CREATE_DATE,'yyyy-mm-dd') as CREATE_DATE, 
						USER_UPDATE, to_char(UPDATE_DATE,'yyyy-mm-dd') as UPDATE_DATE
						FROM HR_RATE_EMP 
						ORDER BY POS_NUM_NAME, POS_NUM_CODE ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "$cmd<br>";
		while($data = $db_att->get_array()){
			$PER_POS_EMP++;
			$POEM_NO_NAME = trim($data[POS_NUM_NAME]);
			$POEM_NO = trim($data[POS_NUM_CODE]);
			$POEM_STATUS = trim($data[REC_STATUS]);
			$DEPARTMENT_CODE = trim($data[DEPARTMENT_CODE]);
			$DIVISION_CODE = $DEPARTMENT_CODE.trim($data[DIVISION_CODE]);
			$SECTION_CODE = $DIVISION_CODE.trim($data[SECTION_CODE]);
			$JOB_CODE = $SECTION_CODE.trim($data[JOB_CODE]);
			$PN_CODE = trim($data[CATEGORY_SAL_CODE]).trim($data[WORK_LINE_CODE]);
			$POEM_MIN_SALARY = $data[SALARY_START_M];
			$POEM_MAX_SALARY = $data[SALARY_END_M];
			$POEM_REMARK = trim($data[REMARK]);
			if (!$POEM_MIN_SALARY) $POEM_MIN_SALARY = 0;
			if (!$POEM_MAX_SALARY) $POEM_MAX_SALARY = 0;

			$UPDATE_USER = 99999;
			if ($data[USER_UPDATE]) $USER_UPDATE = trim($data[USER_UPDATE]);
			else $USER_UPDATE = trim($data[USER_CREATE]);
			if ($data[UPDATE_DATE]) $UPDATE_DATE = trim($data[UPDATE_DATE]);
			else  $UPDATE_DATE = trim($data[CREATE_DATE]);
			if ($USER_UPDATE) {
				$cmd = " select ID from USER_DETAIL where USERNAME = '$USER_UPDATE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				if ($data2[ID]) $UPDATE_USER = $data2[ID];
			}
			if (!$UPDATE_DATE) $UPDATE_DATE = date("Y-m-d H:i:s");

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = '$DEPARTMENT_CODE' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$DEPARTMENT_ID = $data2[NEW_CODE];

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = '$DIVISION_CODE' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$ORG_ID = $data2[NEW_CODE];

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = '$SECTION_CODE' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$ORG_ID_1 = $data2[NEW_CODE];

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = '$JOB_CODE' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$ORG_ID_2 = $data2[NEW_CODE];

			if (!$ORG_ID) $ORG_ID = 1;
			if (!$ORG_ID_1) $ORG_ID_1 = "NULL";
			if (!$ORG_ID_2) $ORG_ID_2 = "NULL";

			$cmd = " INSERT INTO PER_POS_EMP (POEM_ID, ORG_ID, POEM_NO, ORG_ID_1, ORG_ID_2, PN_CODE, POEM_MIN_SALARY, 
							  POEM_MAX_SALARY, POEM_STATUS, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID, POEM_REMARK, POEM_NO_NAME)
							  VALUES ($POEM_ID, $ORG_ID, '$POEM_NO', $ORG_ID_1, $ORG_ID_2, '$PN_CODE', $POEM_MIN_SALARY, $POEM_MAX_SALARY, 
							  $POEM_STATUS, $UPDATE_USER, '$UPDATE_DATE', $DEPARTMENT_ID, '$POEM_REMARK', '$POEM_NO_NAME') ";
			$db_dpis->send_cmd($cmd);

			$cmd1 = " SELECT POEM_ID FROM PER_POS_EMP WHERE POEM_ID = $POEM_ID "; 
			$count_data = $db_dpis1->send_cmd($cmd1);
			if (!$count_data) {
				echo "$cmd<br>==================<br>";
				$db_dpis->show_error();
				echo "<br>end ". ++$i  ."=======================<br>";
			}

			if ($POEM_STATUS==1) {
				$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
								VALUES ('PER_POS_EMP', '$POEM_NO_NAME$POEM_NO', '$POEM_ID', $UPDATE_USER, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			}

			$POEM_ID++;
		} // end while						

		$cmd = " select count(POEM_ID) as COUNT_NEW from PER_POS_EMP ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_POS_EMP - $PER_POS_EMP - $COUNT_NEW<br>";

		$cmd = " ALTER TABLE PER_TRAINING ENABLE CONSTRAINTS FK1_PER_TRAINING ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PUNISHMENT ENABLE CONSTRAINTS FK1_PER_PUNISHMENT ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_DECORDTL ENABLE CONSTRAINTS FK3_PER_DECORDTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_DECORATEHIS ENABLE CONSTRAINTS FK1_PER_DECORATEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_COURSE ENABLE CONSTRAINTS FK4_PER_COURSE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_COURSEDTL ENABLE CONSTRAINTS FK2_PER_COURSEDTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SERVICEHIS ENABLE CONSTRAINTS FK1_PER_SERVICEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SERVICEHIS ENABLE CONSTRAINTS FK5_PER_SERVICEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SALPROMOTE ENABLE CONSTRAINTS FK2_PER_SALPROMOTE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SALARYHIS ENABLE CONSTRAINTS FK1_PER_SALARYHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PROMOTE_P ENABLE CONSTRAINTS FK2_PER_PROMOTE_P ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PROMOTE_E ENABLE CONSTRAINTS FK2_PER_PROMOTE_E ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PROMOTE_C ENABLE CONSTRAINTS FK1_PER_PROMOTE_C ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ORG_JOB ENABLE CONSTRAINTS FK1_PER_ORG_JOB ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_MOVE_REQ ENABLE CONSTRAINTS FK1_PER_MOVE_REQ ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_LETTER ENABLE CONSTRAINTS FK1_PER_LETTER ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_LETTER ENABLE CONSTRAINTS FK2_PER_LETTER ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_BONUSPROMOTE ENABLE CONSTRAINTS FK2_PER_BONUSPROMOTE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_TIMEHIS ENABLE CONSTRAINTS FK1_PER_TIMEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SCHOLAR ENABLE CONSTRAINTS FK1_PER_SCHOLAR ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_REWARDHIS ENABLE CONSTRAINTS FK1_PER_REWARDHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_POSITIONHIS ENABLE CONSTRAINTS FK1_PER_POSITIONHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PERSONALPIC ENABLE CONSTRAINTS FK1_PER_PERSONALPIC ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_NAMEHIS ENABLE CONSTRAINTS FK1_PER_NAMEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_MARRHIS ENABLE CONSTRAINTS FK1_PER_MARRHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_EDUCATE ENABLE CONSTRAINTS FK1_PER_EDUCATE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ABSENTHIS ENABLE CONSTRAINTS FK1_PER_ABSENTHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ABSENT ENABLE CONSTRAINTS FK1_PER_ABSENT ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ABILITY ENABLE CONSTRAINTS FK1_PER_ABILITY ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_INVEST2DTL ENABLE CONSTRAINTS FK2_PER_INVEST2DTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_INVEST1DTL ENABLE CONSTRAINTS FK2_PER_INVEST1DTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_HEIR ENABLE CONSTRAINTS FK1_PER_HEIR ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_EXTRA_INCOMEHIS ENABLE CONSTRAINTS FK1_PER_EXTRA_INCOMEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_EXTRAHIS ENABLE CONSTRAINTS FK1_PER_EXTRAHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_KPI ENABLE CONSTRAINTS FK1_PER_KPI ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_FAMILY ENABLE CONSTRAINTS FK1_PER_FAMILY ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_att->free_result();
		$db_att1->free_result();
	} // end if

	if($command=='POS_TEMP'){
		$cmd = " ALTER TABLE PER_TRAINING DISABLE CONSTRAINTS FK1_PER_TRAINING ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PUNISHMENT DISABLE CONSTRAINTS FK1_PER_PUNISHMENT ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_DECORDTL DISABLE CONSTRAINTS FK3_PER_DECORDTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_DECORATEHIS DISABLE CONSTRAINTS FK1_PER_DECORATEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_COURSE DISABLE CONSTRAINTS FK4_PER_COURSE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_COURSEDTL DISABLE CONSTRAINTS FK2_PER_COURSEDTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SERVICEHIS DISABLE CONSTRAINTS FK1_PER_SERVICEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SERVICEHIS DISABLE CONSTRAINTS FK5_PER_SERVICEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SALPROMOTE DISABLE CONSTRAINTS FK2_PER_SALPROMOTE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SALARYHIS DISABLE CONSTRAINTS FK1_PER_SALARYHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PROMOTE_P DISABLE CONSTRAINTS FK2_PER_PROMOTE_P ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PROMOTE_E DISABLE CONSTRAINTS FK2_PER_PROMOTE_E ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PROMOTE_C DISABLE CONSTRAINTS FK1_PER_PROMOTE_C ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ORG_JOB DISABLE CONSTRAINTS FK1_PER_ORG_JOB ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_MOVE_REQ DISABLE CONSTRAINTS FK1_PER_MOVE_REQ ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_LETTER DISABLE CONSTRAINTS FK1_PER_LETTER ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_LETTER DISABLE CONSTRAINTS FK2_PER_LETTER ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_BONUSPROMOTE DISABLE CONSTRAINTS FK2_PER_BONUSPROMOTE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_TIMEHIS DISABLE CONSTRAINTS FK1_PER_TIMEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SCHOLAR DISABLE CONSTRAINTS FK1_PER_SCHOLAR ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_REWARDHIS DISABLE CONSTRAINTS FK1_PER_REWARDHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_POSITIONHIS DISABLE CONSTRAINTS FK1_PER_POSITIONHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PERSONALPIC DISABLE CONSTRAINTS FK1_PER_PERSONALPIC ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_NAMEHIS DISABLE CONSTRAINTS FK1_PER_NAMEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_MARRHIS DISABLE CONSTRAINTS FK1_PER_MARRHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_EDUCATE DISABLE CONSTRAINTS FK1_PER_EDUCATE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ABSENTHIS DISABLE CONSTRAINTS FK1_PER_ABSENTHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ABSENT DISABLE CONSTRAINTS FK1_PER_ABSENT ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ABILITY DISABLE CONSTRAINTS FK1_PER_ABILITY ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_INVEST2DTL DISABLE CONSTRAINTS FK2_PER_INVEST2DTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_INVEST1DTL DISABLE CONSTRAINTS FK2_PER_INVEST1DTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_HEIR DISABLE CONSTRAINTS FK1_PER_HEIR ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_EXTRA_INCOMEHIS DISABLE CONSTRAINTS FK1_PER_EXTRA_INCOMEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_EXTRAHIS DISABLE CONSTRAINTS FK1_PER_EXTRAHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_KPI DISABLE CONSTRAINTS FK1_PER_KPI ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_FAMILY DISABLE CONSTRAINTS FK1_PER_FAMILY ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$table = array( "per_absenthis", "per_marrhis", "per_timehis", "per_ability", "per_rewardhis", "per_extrahis", "per_extra_incomehis", "per_namehis", 
			"per_punishment", "per_decoratehis", "per_training", "per_educate", "per_salaryhis", "per_positionhis", "per_personalpic", "per_heir", "per_servicehis", 
			"per_comdtl", "per_bonuspromote", "per_bonusquotadtl2", "per_bonusquotadtl1", "per_bonusquota", "per_special_skill", "per_coursedtl", "per_decordtl", "per_org_job", 
			"per_promote_p", "per_letter", "per_move_req", "per_scholar", "per_absent", "per_invest1dtl", "per_promote_c", "per_address", "per_kpi_form", 
			"per_salpromote", "per_family", "per_personal", "per_pos_move", "per_salquotadtl1", "per_salquotadtl2", "per_salquota", "per_command", "per_decor", 
			"per_order_dtl", "per_req3_dtl" );
		for ( $i=0; $i<count($table); $i++ ) { 
			if ($table[$i]=="per_bonusquota" || $table[$i]=="per_scholar" || $table[$i]=="per_salquota" || $table[$i]=="per_command" || $table[$i]=="per_decor")
				$cmd = " delete from $table[$i] ";
			else
				$cmd = " truncate table $table[$i] ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end for

// ตำแหน่งลูกจ้างชั่วคราว 30292 - 30292

		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_POS_TEMP' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_POS_TEMP ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_att->free_result();
		$db_att1->free_result();

		$cmd = " select max(POT_ID) as MAX_ID from PER_POS_TEMP ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$POT_ID = $data[MAX_ID] + 1;

		$cmd = " SELECT POS_NUM_CODE, REC_STATUS, CLUSTER_CODE, CATEGORY_SAL_CODE, WORK_LINE_CODE, 
						DEPARTMENT_CODE, DIVISION_CODE, SECTION_CODE, JOB_CODE, DEPARTMENT_NAME, 
						DIVISION_NAME, SECTION_NAME, JOB_NAME, CATEGORY_SAL_NAME, WORK_LINE_NAME, 
						SALARY_START_M, SALARY_START_D, SALARY_END_M, SALARY_END_D, HQ_REF_CODE, 
						HQ_REF_ID, HQ_REF_YEAR, to_char(HQ_REF_YMD,'yyyy-mm-dd') as HQ_REF_YMD, 
						MP_COMMAND_NUM, to_char(MP_COMMAND_DATE,'yyyy-mm-dd') as MP_COMMAND_DATE, 
						FLAG_HOLDER_CODE, BOARD_TYPE, POS_NUM_NAME, POS_NUM_CODE_R, REMARK, REMARKS, 
						HQ_REF_BYEAR, USER_CREATE, to_char(CREATE_DATE,'yyyy-mm-dd') as CREATE_DATE, 
						USER_UPDATE, to_char(UPDATE_DATE,'yyyy-mm-dd') as UPDATE_DATE
						FROM HR_RATE_EMPTEMP 
						ORDER BY POS_NUM_CODE, DEPARTMENT_CODE, DIVISION_CODE, SECTION_CODE, JOB_CODE ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "$cmd<br>";
		while($data = $db_att->get_array()){
			$PER_POS_TEMP++;
			$POT_NO = trim($data[POS_NUM_CODE]);
			$POT_STATUS = trim($data[REC_STATUS]);
			$DEPARTMENT_CODE = trim($data[DEPARTMENT_CODE]);
			$DIVISION_CODE = $DEPARTMENT_CODE.trim($data[DIVISION_CODE]);
			$SECTION_CODE = $DIVISION_CODE.trim($data[SECTION_CODE]);
			$JOB_CODE = $SECTION_CODE.trim($data[JOB_CODE]);
			$TP_CODE = trim($data[CATEGORY_SAL_CODE]).trim($data[WORK_LINE_CODE]);
			$POT_MIN_SALARY = $data[SALARY_START_M];
			$POT_MAX_SALARY = $data[SALARY_END_M];
			$POT_REMARK = trim($data[REMARK]);
			if (!$POT_MIN_SALARY) $POT_MIN_SALARY = 0;
			if (!$POT_MAX_SALARY) $POT_MAX_SALARY = 0;

			$UPDATE_USER = 99999;
			if ($data[USER_UPDATE]) $USER_UPDATE = trim($data[USER_UPDATE]);
			else $USER_UPDATE = trim($data[USER_CREATE]);
			if ($data[UPDATE_DATE]) $UPDATE_DATE = trim($data[UPDATE_DATE]);
			else  $UPDATE_DATE = trim($data[CREATE_DATE]);
			if ($USER_UPDATE) {
				$cmd = " select ID from USER_DETAIL where USERNAME = '$USER_UPDATE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				if ($data2[ID]) $UPDATE_USER = $data2[ID];
			}
			if (!$UPDATE_DATE) $UPDATE_DATE = date("Y-m-d H:i:s");

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = '$DEPARTMENT_CODE' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$DEPARTMENT_ID = $data2[NEW_CODE];

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = '$DIVISION_CODE' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$ORG_ID = $data2[NEW_CODE];

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = '$SECTION_CODE' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$ORG_ID_1 = $data2[NEW_CODE];

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = '$JOB_CODE' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$ORG_ID_2 = $data2[NEW_CODE];

			if (!$ORG_ID) $ORG_ID = 1;
			if (!$ORG_ID_1) $ORG_ID_1 = "NULL";
			if (!$ORG_ID_2) $ORG_ID_2 = "NULL";

			$cmd = " INSERT INTO PER_POS_TEMP (POT_ID, ORG_ID, POT_NO, ORG_ID_1, ORG_ID_2, TP_CODE, POT_MIN_SALARY, 
							  POT_MAX_SALARY, POT_STATUS, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID, POT_REMARK)
							  VALUES ($POT_ID, $ORG_ID, '$POT_NO', $ORG_ID_1, $ORG_ID_2, '$TP_CODE', $POT_MIN_SALARY, $POT_MAX_SALARY, 
							  $POT_STATUS, $UPDATE_USER, '$UPDATE_DATE', $DEPARTMENT_ID, '$POT_REMARK') ";
			$db_dpis->send_cmd($cmd);

			$cmd1 = " SELECT POT_ID FROM PER_POS_TEMP WHERE POT_ID = $POT_ID "; 
			$count_data = $db_dpis1->send_cmd($cmd1);
			if (!$count_data) {
				echo "$cmd<br>==================<br>";
				$db_dpis->show_error();
				echo "<br>end ". ++$i  ."=======================<br>";
			}

			if ($POT_STATUS==1) {
				$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
								VALUES ('PER_POS_TEMP', '$POT_NO', '$POT_ID', $UPDATE_USER, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			}

			$POT_ID++;
		} // end while						

		$cmd = " select count(POT_ID) as COUNT_NEW from PER_POS_TEMP ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_POS_TEMP - $PER_POS_TEMP - $COUNT_NEW<br>";

		$cmd = " ALTER TABLE PER_TRAINING ENABLE CONSTRAINTS FK1_PER_TRAINING ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PUNISHMENT ENABLE CONSTRAINTS FK1_PER_PUNISHMENT ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_DECORDTL ENABLE CONSTRAINTS FK3_PER_DECORDTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_DECORATEHIS ENABLE CONSTRAINTS FK1_PER_DECORATEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_COURSE ENABLE CONSTRAINTS FK4_PER_COURSE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_COURSEDTL ENABLE CONSTRAINTS FK2_PER_COURSEDTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SERVICEHIS ENABLE CONSTRAINTS FK1_PER_SERVICEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SERVICEHIS ENABLE CONSTRAINTS FK5_PER_SERVICEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SALPROMOTE ENABLE CONSTRAINTS FK2_PER_SALPROMOTE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SALARYHIS ENABLE CONSTRAINTS FK1_PER_SALARYHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PROMOTE_P ENABLE CONSTRAINTS FK2_PER_PROMOTE_P ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PROMOTE_E ENABLE CONSTRAINTS FK2_PER_PROMOTE_E ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PROMOTE_C ENABLE CONSTRAINTS FK1_PER_PROMOTE_C ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ORG_JOB ENABLE CONSTRAINTS FK1_PER_ORG_JOB ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_MOVE_REQ ENABLE CONSTRAINTS FK1_PER_MOVE_REQ ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_LETTER ENABLE CONSTRAINTS FK1_PER_LETTER ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_LETTER ENABLE CONSTRAINTS FK2_PER_LETTER ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_BONUSPROMOTE ENABLE CONSTRAINTS FK2_PER_BONUSPROMOTE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_TIMEHIS ENABLE CONSTRAINTS FK1_PER_TIMEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SCHOLAR ENABLE CONSTRAINTS FK1_PER_SCHOLAR ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_REWARDHIS ENABLE CONSTRAINTS FK1_PER_REWARDHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_POSITIONHIS ENABLE CONSTRAINTS FK1_PER_POSITIONHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PERSONALPIC ENABLE CONSTRAINTS FK1_PER_PERSONALPIC ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_NAMEHIS ENABLE CONSTRAINTS FK1_PER_NAMEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_MARRHIS ENABLE CONSTRAINTS FK1_PER_MARRHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_EDUCATE ENABLE CONSTRAINTS FK1_PER_EDUCATE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ABSENTHIS ENABLE CONSTRAINTS FK1_PER_ABSENTHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ABSENT ENABLE CONSTRAINTS FK1_PER_ABSENT ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ABILITY ENABLE CONSTRAINTS FK1_PER_ABILITY ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_INVEST2DTL ENABLE CONSTRAINTS FK2_PER_INVEST2DTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_INVEST1DTL ENABLE CONSTRAINTS FK2_PER_INVEST1DTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_HEIR ENABLE CONSTRAINTS FK1_PER_HEIR ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_EXTRA_INCOMEHIS ENABLE CONSTRAINTS FK1_PER_EXTRA_INCOMEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_EXTRAHIS ENABLE CONSTRAINTS FK1_PER_EXTRAHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_KPI ENABLE CONSTRAINTS FK1_PER_KPI ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_FAMILY ENABLE CONSTRAINTS FK1_PER_FAMILY ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_att->free_result();
		$db_att1->free_result();
	} // end if

	if($command=='PERSONAL'){
		$cmd = " ALTER TABLE PER_TRAINING DISABLE CONSTRAINTS FK1_PER_TRAINING ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PUNISHMENT DISABLE CONSTRAINTS FK1_PER_PUNISHMENT ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_DECORDTL DISABLE CONSTRAINTS FK3_PER_DECORDTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_DECORATEHIS DISABLE CONSTRAINTS FK1_PER_DECORATEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_COURSE DISABLE CONSTRAINTS FK4_PER_COURSE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_COURSEDTL DISABLE CONSTRAINTS FK2_PER_COURSEDTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SERVICEHIS DISABLE CONSTRAINTS FK1_PER_SERVICEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SERVICEHIS DISABLE CONSTRAINTS FK5_PER_SERVICEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SALPROMOTE DISABLE CONSTRAINTS FK2_PER_SALPROMOTE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SALARYHIS DISABLE CONSTRAINTS FK1_PER_SALARYHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PROMOTE_P DISABLE CONSTRAINTS FK2_PER_PROMOTE_P ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PROMOTE_E DISABLE CONSTRAINTS FK2_PER_PROMOTE_E ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PROMOTE_C DISABLE CONSTRAINTS FK1_PER_PROMOTE_C ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ORG_JOB DISABLE CONSTRAINTS FK1_PER_ORG_JOB ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_MOVE_REQ DISABLE CONSTRAINTS FK1_PER_MOVE_REQ ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_LETTER DISABLE CONSTRAINTS FK1_PER_LETTER ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_LETTER DISABLE CONSTRAINTS FK2_PER_LETTER ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_BONUSPROMOTE DISABLE CONSTRAINTS FK2_PER_BONUSPROMOTE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_TIMEHIS DISABLE CONSTRAINTS FK1_PER_TIMEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SCHOLAR DISABLE CONSTRAINTS FK1_PER_SCHOLAR ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_REWARDHIS DISABLE CONSTRAINTS FK1_PER_REWARDHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_POSITIONHIS DISABLE CONSTRAINTS FK1_PER_POSITIONHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PERSONALPIC DISABLE CONSTRAINTS FK1_PER_PERSONALPIC ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_NAMEHIS DISABLE CONSTRAINTS FK1_PER_NAMEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_MARRHIS DISABLE CONSTRAINTS FK1_PER_MARRHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_EDUCATE DISABLE CONSTRAINTS FK1_PER_EDUCATE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ABSENTHIS DISABLE CONSTRAINTS FK1_PER_ABSENTHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ABSENT DISABLE CONSTRAINTS FK1_PER_ABSENT ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ABILITY DISABLE CONSTRAINTS FK1_PER_ABILITY ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_INVEST2DTL DISABLE CONSTRAINTS FK2_PER_INVEST2DTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_INVEST1DTL DISABLE CONSTRAINTS FK2_PER_INVEST1DTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_HEIR DISABLE CONSTRAINTS FK1_PER_HEIR ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_EXTRA_INCOMEHIS DISABLE CONSTRAINTS FK1_PER_EXTRA_INCOMEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_EXTRAHIS DISABLE CONSTRAINTS FK1_PER_EXTRAHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_KPI DISABLE CONSTRAINTS FK1_PER_KPI ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_FAMILY DISABLE CONSTRAINTS FK1_PER_FAMILY ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$table = array( "per_absenthis", "per_marrhis", "per_timehis", "per_ability", "per_rewardhis", "per_extrahis", "per_extra_incomehis", "per_namehis", 
			"per_punishment", "per_decoratehis", "per_training", "per_educate", "per_salaryhis", "per_positionhis", "per_personalpic", "per_heir", "per_servicehis", 
			"per_comdtl", "per_bonuspromote", "per_bonusquotadtl2", "per_bonusquotadtl1", "per_bonusquota", "per_special_skill", "per_coursedtl", "per_decordtl", "per_org_job", 
			"per_promote_p", "per_letter", "per_move_req", "per_scholar", "per_absent", "per_invest1dtl", "per_promote_c", "per_address", "per_kpi_form", 
			"per_salpromote", "per_family" );
		for ( $i=0; $i<count($table); $i++ ) { 
			if ($table[$i]=="per_bonusquota" || $table[$i]=="per_scholar" || $table[$i]=="per_salquota" || $table[$i]=="per_command" || $table[$i]=="per_decor")
				$cmd = " delete from $table[$i] ";
			else
				$cmd = " truncate table $table[$i] ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end for

// ข้าราชการ + ครู 60415
// ข้าราชการ 33772
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_PERSONAL' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_PERSONAL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_att->free_result();
		$db_att1->free_result();

		$MAX_ID = 1;

		$cmd = " SELECT a.ID, FLAG_PERSON_TYPE, RANK_CODE, FNAME, LNAME, SEX, FLAG_TYPE, to_char(BORN,'yyyy-mm-dd') as BORN, 
						to_char(RET_BORN_DATE,'yyyy-mm-dd') as RET_BORN_DATE, to_char(RET_BORN_YEAR,'yyyy-mm-dd') as RET_BORN_YEAR, 
						to_char(BEGIN_ENTRY_DATE,'yyyy-mm-dd') as BEGIN_ENTRY_DATE, 
						to_char(RETURN_OCCUPY_DATE,'yyyy-mm-dd') as RETURN_OCCUPY_DATE, 
						to_char(MP_FORCE_DATE,'yyyy-mm-dd') as MP_FORCE_DATE, to_char(UPCLASS_DATE,'yyyy-mm-dd') as UPCLASS_DATE, 
						to_char(WORK_LINE_DATE,'yyyy-mm-dd') as WORK_LINE_DATE, FLAG_CUR_ST, POS_NUM_CODE_SIT, MP_COMMAND_NUM, 
						to_char(MP_COMMAND_DATE,'yyyy-mm-dd') as MP_COMMAND_DATE, FLAG_TO_NAME_CODE, 
						to_char(MP_POS_DATE,'yyyy-mm-dd') as MP_POS_DATE, MP_YEAR, POS_NUM_NAME, POS_NUM_CODE, POSITION_CATG, 
						WORK_LINE_CODE, WORK_LINE_NAME, MP_CEE, SALARY_LEVEL_CODE, SALARY, SALARY_POS_ABB_NAME, 
						SAL_POS_AMOUNT_2, SPECIALIST_CODE, to_char(SPECIALIST_DATE,'yyyy-mm-dd') as SPECIALIST_DATE, ADMIN_CODE, 
						ADMIN_NAME, SALARY_POS_ABB_NAME_1, SAL_POS_AMOUNT_1, to_char(ADMIN_DATE,'yyyy-mm-dd') as ADMIN_DATE, 
						MP_FLAG, SALARY_ADD, SPECIAL_PERCENT, SPECIAL_AMT, PAYMENT_AMT, PAYMENT_PERCENT, MP_FLAG_1, 
						COST_LIVING_AMOUNT, JOB_CODE, JOB_NAME, SECTION_CODE, SECTION_NAME, DIVISION_CODE, DIVISION_NAME, 
						DEPARTMENT_CODE, DEPARTMENT_NAME, RET_BORN_MP_YEAR, SALARY_POS_CODE, SALARY_POS_CODE_1, 
						CUR_YEAR, FLAG_TO_NAME, POS_NUM_CODE_SIT_ABB, POS_NUM_CODE_SIT_CODE, CONTENT_NO, 
						POS_NUM_CODE_SIT_O, POS_NUM_CODE_SIT_ABB_O, POS_NUM_CODE_SIT_CODE_O, MP_COMMAND_NUM_O, 
						CUR_YEAR_O, MP_YEAR_O, FLAG_TO_NAME_CODE_O, FLAG_TO_NAME_O, 
						to_char(MP_COMMAND_DATE_O,'yyyy-mm-dd') as MP_COMMAND_DATE_O, 
						to_char(MP_POS_DATE_O,'yyyy-mm-dd') as MP_POS_DATE_O, MP_FLAG_O, MP_FLAG_1_O, MP_CEE_O, 
						SALARY_LEVEL_CODE_O, SALARY_O, SPECIAL_AMT_O, SALARY_ADD_O, MP_CEE_CODE, SALARY_LEVEL, 
						MP_CEE_CODE_O, SALARY_LEVEL_O, GROUPWORK_CODE, WORK_LINE_CODE_O, WORK_LINE_NAME_O, 
						FLAG_RETIRE_STATUS, MARRIAGE_STATE, SUN_NO, RETIRE_TYPE_CODE, RETIRE_POS_NO, 
						to_char(DEXPIRE_DATE,'yyyy-mm-dd') as DEXPIRE_DATE, HELP_LIVING_AMOUNT, OLD_RETIRE_DEPARTMENT_CODE, 
						OLD_RETIRE_DIVISION_CODE, OLD_RETIRE_SECTION_CODE, OLD_RETIRE_JOB_CODE, 
						a.USER_CREATE, to_char(a.CREATE_DATE,'yyyy-mm-dd') as CREATE_DATE, 
						a.USER_UPDATE, to_char(a.UPDATE_DATE,'yyyy-mm-dd') as UPDATE_DATE,
						TAX_ID, ID_COMPTROLLER, to_char(ISSUED_DATE,'yyyy-mm-dd') as ISSUED_DATE, 
						to_char(EXPIRED_DATE,'yyyy-mm-dd') as EXPIRED_DATE, PASSPORT_CODE, HEIR_SUBSCR_NAME,
						SPECI_HELP_SUBSCR_NAME, SPECI_ABILITY_DATA, EXTRACTION, CITIZENSHIP, RELIGION, BORN_REGIS,
						BLOOD, BLOOD_RH, FLAG_KBK, FLAG_TYPE_KBK
						FROM HR_PERSONAL_OFFICER a, HR_PERSONAL_OFFICER_DETAIL b
						WHERE a.ID = b.ID(+) and a.FLAG_PERSON_TYPE != 3 
						ORDER BY FLAG_PERSON_TYPE, a.ID ";
		$db_att->send_cmd($cmd);
//							WHERE a.ID > '1' AND a.ID < '2'
//		$db_att->show_error();
//		echo "<br>";
		while($data = $db_att->get_array()){
			$PER_PERSONAL++;
			$PN_CODE = trim($data[RANK_CODE]);
			$PER_NAME = trim($data[FNAME]);
			$PER_SURNAME = trim($data[LNAME]);
			$OT_CODE = trim($data[FLAG_PERSON_TYPE]);
			$PER_TYPE = 1;
			if ($OT_CODE=="3") $PER_TYPE = 5;
			$PER_CARDNO = trim($data[ID]);
			$PER_GENDER = trim($data[SEX]);
			$PER_BIRTHDATE = trim($data[BORN]);
			$PER_RETIREDATE = trim($data[RET_BORN_YEAR]); 
			$PER_STARTDATE = trim($data[BEGIN_ENTRY_DATE]);
			$PER_OCCUPYDATE = trim($data[RETURN_OCCUPY_DATE]);
			$LEVEL_NO = str_pad(trim($data[MP_CEE]), 2, "0", STR_PAD_LEFT);
			if ($LEVEL_NO=="21" || $LEVEL_NO=="ปฏิบัติงาน") $LEVEL_NO = "O1";
			if ($LEVEL_NO=="22" || $LEVEL_NO=="ชำนาญงาน") $LEVEL_NO = "O2";
			if ($LEVEL_NO=="23") $LEVEL_NO = "O3";
			if ($LEVEL_NO=="26" || $LEVEL_NO=="ปฏิบัติการ") $LEVEL_NO = "K1";
			if ($LEVEL_NO=="27" || $LEVEL_NO=="ชำนาญการ") $LEVEL_NO = "K2";
			if ($LEVEL_NO=="28") $LEVEL_NO = "K3";
			if ($LEVEL_NO=="29") $LEVEL_NO = "K4";
			if ($LEVEL_NO=="30") $LEVEL_NO = "K5";
			if ($LEVEL_NO=="32") $LEVEL_NO = "D1";
			if ($LEVEL_NO=="33") $LEVEL_NO = "D2";
			if ($LEVEL_NO=="34") $LEVEL_NO = "M1";
			if ($LEVEL_NO=="35") $LEVEL_NO = "M2";
			$POS_NO_NAME = trim($data[POS_NUM_NAME]);
			$POS_NO = trim($data[POS_NUM_CODE]);
			$PER_SALARY = $data[SALARY]+0;
			$MOV_CODE = trim($data[FLAG_TO_NAME_CODE]);
			$FLAG_CUR_ST = trim($data[FLAG_CUR_ST]);
			if ($FLAG_CUR_ST == 1) $PER_STATUS = 1; else $PER_STATUS = 2;
			$MR_CODE = trim($data[MARRIAGE_STATE]);
			$RELIGION = trim($data[RELIGION]);
			if (!$RELIGION) $RE_CODE = '0';
			elseif ($RELIGION=="พุทธ") $RE_CODE = '1';
			else $RE_CODE = $RELIGION;
			$BLOOD = trim($data[BLOOD]);
			if ($BLOOD=="1") $PER_BLOOD = "A";
			elseif ($BLOOD=="2") $PER_BLOOD = "B";
			elseif ($BLOOD=="4") $PER_BLOOD = "O";
			elseif ($BLOOD=="3" || $BLOODGRP=="เอ บี") $PER_BLOOD = "AB";
			else $PER_BLOOD = "-";
			$PER_TAXNO = trim($data[TAX_ID]);
			$FLAG_KBK = trim($data[FLAG_KBK]); 
			if ($FLAG_KBK=="1") $PER_MEMBER = 1;
			else  $PER_MEMBER = 0;

			$UPDATE_USER = 99999;
			if ($data[USER_UPDATE]) $USER_UPDATE = trim($data[USER_UPDATE]);
			else $USER_UPDATE = trim($data[USER_CREATE]);
			if ($data[UPDATE_DATE]) $UPDATE_DATE = trim($data[UPDATE_DATE]);
			else  $UPDATE_DATE = trim($data[CREATE_DATE]);
			if ($USER_UPDATE) {
				$cmd = " select ID from USER_DETAIL where USERNAME = '$USER_UPDATE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				if ($data2[ID]) $UPDATE_USER = $data2[ID];
			}
			if (!$UPDATE_DATE) $UPDATE_DATE = date("Y-m-d H:i:s");
/*
			$MILITARYSTATUS = strtoupper(trim($data[MILITARYSTATUS])); 
			$ORDAINSTATUS = strtoupper(trim($data[ORDAINSTATUS])); 
			$ORDAINDETAIL = trim($data[ORDAINDETAIL]); 
			$GBCDETAIL = trim($data[GBCDETAIL]); 
			$COOPSTATUS = strtoupper(trim($data[COOPSTATUS])); 
			$COOPDETAIL = trim($data[COOPDETAIL]); 
			$ENGAGENAME = trim($data[ENGAGENAME]);
			$PERSONSTATUSID = $data[PERSONSTATUSID];
			$SUBPERSONSTATUSID = $data[SUBPERSONSTATUSID];
			$PERSONPOSITIONID = $data[PERSONPOSITIONID];
			$PER_POSDATE = trim($data[PSTATUSEFFECTIVEDATE]);
			$ES_CODE = str_pad(trim($data[EMP_STATUS]), 2, "0", STR_PAD_LEFT);
			$OLDPOSCODE = trim($data[OLDPOSCODE]);
			$OLDPOSNAME = trim($data[OLDPOSNAME]);
			$OLDSECCODE = trim($data[OLDSECCODE]);
			$OLDSECNAME = trim($data[OLDSECNAME]);
			$PAYMENTNO = $data[PAYMENTNO];
			$PER_START_ORG = trim($data[FIRSTENTRANCEPLACE]);
			$PV_CODE = trim($data[PV_CODE]);
			if ($PV_CODE) $PV_CODE .= "00";
			$PER_DOCNO = trim($data[PSTATUSORDERNO]);
			$PER_DOCDATE = trim($data[PSTATUSORDERDATE]);
			if ($PER_DOCDATE=="0000-00-00") $PER_DOCDATE = "";
			$PER_REMARK = trim($data[REMARK]);
			$SPECIALCAPA = trim($data[SPECIALCAPA]); // PER_ABILITY
			$PER_EFFECTIVEDATE = trim($data[PSTATUSEFFECTIVEDATE]);
			$PER_POS_REASON= trim($data[PSTATUSREASON]);
			$PER_POS_YEAR = trim($data[PSTATUSYEAR]);
			$PER_POS_DOCTYPE = trim($data[PSTATUSDOCREFER]);
			$PER_POS_DOCNO = trim($data[PSTATUSDOCREFERNO]);
			$PER_POS_ORG = trim($data[PSTATUSSECREFER]);
*/
			$PER_MGTSALARY = 0; // รอแก้

			$POS_ID = "NULL";
			$POEM_ID = "NULL";
			$POEMS_ID = "NULL";
			$POT_ID = "NULL";
			$PER_ORGMGT = 0;
			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_POSITION' AND OLD_CODE = '$POS_NO_NAME$POS_NO' ";
			$db_dpis2->send_cmd($cmd);
			//$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$POS_ID = $data2[NEW_CODE] + 0;

			$cmd = " select DEPARTMENT_ID from PER_POSITION where POS_ID = $POS_ID ";
			$db_dpis2->send_cmd($cmd);
			//$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$DEPARTMENT_ID = $data2[DEPARTMENT_ID] + 0;

			if ($POS_ORGMGT) $PER_ORGMGT = 1;
				
			$ASS_ORG_ID = "NULL";
			$LEVEL_NO = trim($LEVEL_NO)? "'".$LEVEL_NO."'" : "NULL";
			if ($LEVEL_NO=="'00'" || $LEVEL_NO=="'0-'") $LEVEL_NO = "NULL";
			if ($POS_ID==0) $POS_ID = "NULL";
			if ($POEM_ID==0) $POEM_ID = "NULL";
			if ($POEMS_ID==0) $POEMS_ID = "NULL";
			if ($POT_ID==0) $POT_ID = "NULL";
			if (!$PAY_ID) $PAY_ID = "NULL";
			if (!$OT_CODE) $OT_CODE = "01";
			if (!$MR_CODE) $MR_CODE = "9";
			if (!$PER_STARTDATE) $PER_STARTDATE = "-";
//			if (!$PER_OCCUPYDATE) $PER_OCCUPYDATE = "-";

			if ($MILITARYSTATUS=="Y") $PER_SOLDIER = 1;
			else  $PER_SOLDIER = 0;

			if ($ORDAINSTATUS=="Y") $PER_ORDAIN = 1;
			else  $PER_ORDAIN = 0;

			$PER_MEMBERDATE = "";
			if ($GBCDETAIL) { 
				$temp_date = explode("/", $GBCDETAIL);
				$PER_MEMBERDATE = ($temp_date)? ($temp_date[2] - 543) ."-". str_pad(trim($temp_date[1]), 2, "0", STR_PAD_LEFT) ."-". str_pad(trim($temp_date[0]), 2, "0", STR_PAD_LEFT) : "";
			}

			if ($COOPSTATUS=="Y") $PER_COOPERATIVE = 1;
			else  $PER_COOPERATIVE = 0;

			if (!$PER_GENDER)
				if ($PN_CODE == "003") $PER_GENDER = 1;
				elseif ($PN_CODE == "004" || $PN_CODE == "005") $PER_GENDER = 2;
			$MOV_CODE = "0"; // รอแก้
			$ES_CODE = "02"; 

			$cmd = " INSERT INTO PER_PERSONAL (PER_ID, PER_TYPE, OT_CODE, PN_CODE, PER_NAME, PER_SURNAME, 
							PER_ENG_NAME, PER_ENG_SURNAME, ORG_ID, POS_ID, POEM_ID, POEMS_ID, POT_ID, LEVEL_NO, PER_ORGMGT, 
							PER_SALARY, PER_MGTSALARY, PER_SPSALARY, PER_GENDER, MR_CODE, PER_CARDNO, PER_OFFNO, 
							PER_TAXNO, PER_BLOOD, RE_CODE, PER_BIRTHDATE, PER_RETIREDATE, PER_STARTDATE, 
							PER_OCCUPYDATE, PER_POSDATE, PN_CODE_F, PER_FATHERNAME, PER_FATHERSURNAME, PN_CODE_M, 
							PER_MOTHERNAME, PER_MOTHERSURNAME, PV_CODE, MOV_CODE, PER_ORDAIN, PER_ORDAIN_DETAIL, 
							PER_SOLDIER, PER_MEMBER, PER_MEMBERDATE, PER_STATUS, PER_HIP_FLAG, PER_CERT_OCC, 
							DEPARTMENT_ID, LEVEL_NO_SALARY, UPDATE_USER, UPDATE_DATE, PER_HOME_TEL, PER_FILE_NO, 
							PER_BANK_ACCOUNT, PER_REMARK, PER_START_ORG, PAY_ID, ES_CODE, ORG_NAME_WORK, 
							PL_NAME_WORK, PER_DOCNO, PER_DOCDATE, PER_COOPERATIVE, PER_COOPERATIVE_NO,
							PER_EFFECTIVEDATE, PER_POS_REASON, PER_POS_YEAR, PER_POS_DOCTYPE, PER_POS_DOCNO, PER_POS_ORG)
							VALUES ($MAX_ID, $PER_TYPE, '$OT_CODE', '$PN_CODE', trim('$PER_NAME'), trim('$PER_SURNAME'), 
							trim('$PER_ENG_NAME'), trim('$PER_ENG_SURNAME'), $ASS_ORG_ID, $POS_ID, $POEM_ID, $POEMS_ID, $POT_ID, 
							$LEVEL_NO, $PER_ORGMGT, $PER_SALARY, $PER_MGTSALARY, 0, $PER_GENDER, '$MR_CODE', '$PER_CARDNO', 
							'$PER_OFFNO', '$PER_TAXNO', '$PER_BLOOD', '$RE_CODE', '$PER_BIRTHDATE', 	'$PER_RETIREDATE', '$PER_STARTDATE', 
							'$PER_OCCUPYDATE', '$PER_POSDATE', '$PN_CODE_F', trim('$PER_FATHERNAME'), 
							trim('$PER_FATHERSURNAME'), '$PN_CODE_M', trim('$PER_MOTHERNAME'), 
							trim('$PER_MOTHERSURNAME'), '$PV_CODE', '$MOV_CODE', $PER_ORDAIN, '$ORDAINDETAIL', $PER_SOLDIER, 
							$PER_MEMBER, '$PER_MEMBERDATE', $PER_STATUS, NULL, NULL, $DEPARTMENT_ID, $LEVEL_NO, $UPDATE_USER, 
							'$UPDATE_DATE', NULL, NULL, NULL, '$PER_REMARK', '$PER_START_ORG', $PAY_ID, '$ES_CODE', 
							'$OLDSECNAME', '$OLDPOSNAME', '$PER_DOCNO', '$PER_DOCDATE', $PER_COOPERATIVE, '$COOPDETAIL',
							'$PER_EFFECTIVEDATE', '$PER_POS_REASON', '$PER_POS_YEAR', '$PER_POS_DOCTYPE', '$PER_POS_DOCNO', 
							'$PER_POS_ORG') ";
			$db_dpis->send_cmd($cmd);

			$cmd1 = " SELECT PER_ID FROM PER_PERSONAL WHERE PER_ID = $MAX_ID "; 
			$count_data = $db_dpis1->send_cmd($cmd1);
			if (!$count_data) {
				echo "$cmd<br>==================<br>";
				$db_dpis->show_error();
				echo "<br>end ". ++$i  ."=======================<br>";
			}

			$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
							VALUES ('PER_PERSONAL', '$PER_CARDNO', '$MAX_ID', $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$MAX_ID++;
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_PERSONAL where PER_TYPE in (1,5) ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_PERSONAL - $PER_PERSONAL - $COUNT_NEW<br>";
		
		$cmd = " ALTER TABLE PER_TRAINING ENABLE CONSTRAINTS FK1_PER_TRAINING ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PUNISHMENT ENABLE CONSTRAINTS FK1_PER_PUNISHMENT ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_DECORDTL ENABLE CONSTRAINTS FK3_PER_DECORDTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_DECORATEHIS ENABLE CONSTRAINTS FK1_PER_DECORATEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_COURSE ENABLE CONSTRAINTS FK4_PER_COURSE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_COURSEDTL ENABLE CONSTRAINTS FK2_PER_COURSEDTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SERVICEHIS ENABLE CONSTRAINTS FK1_PER_SERVICEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SERVICEHIS ENABLE CONSTRAINTS FK5_PER_SERVICEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SALPROMOTE ENABLE CONSTRAINTS FK2_PER_SALPROMOTE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SALARYHIS ENABLE CONSTRAINTS FK1_PER_SALARYHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PROMOTE_P ENABLE CONSTRAINTS FK2_PER_PROMOTE_P ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PROMOTE_E ENABLE CONSTRAINTS FK2_PER_PROMOTE_E ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PROMOTE_C ENABLE CONSTRAINTS FK1_PER_PROMOTE_C ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ORG_JOB ENABLE CONSTRAINTS FK1_PER_ORG_JOB ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_MOVE_REQ ENABLE CONSTRAINTS FK1_PER_MOVE_REQ ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_LETTER ENABLE CONSTRAINTS FK1_PER_LETTER ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_LETTER ENABLE CONSTRAINTS FK2_PER_LETTER ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_BONUSPROMOTE ENABLE CONSTRAINTS FK2_PER_BONUSPROMOTE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_TIMEHIS ENABLE CONSTRAINTS FK1_PER_TIMEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SCHOLAR ENABLE CONSTRAINTS FK1_PER_SCHOLAR ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_REWARDHIS ENABLE CONSTRAINTS FK1_PER_REWARDHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_POSITIONHIS ENABLE CONSTRAINTS FK1_PER_POSITIONHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PERSONALPIC ENABLE CONSTRAINTS FK1_PER_PERSONALPIC ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_NAMEHIS ENABLE CONSTRAINTS FK1_PER_NAMEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_MARRHIS ENABLE CONSTRAINTS FK1_PER_MARRHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_EDUCATE ENABLE CONSTRAINTS FK1_PER_EDUCATE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ABSENTHIS ENABLE CONSTRAINTS FK1_PER_ABSENTHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ABSENT ENABLE CONSTRAINTS FK1_PER_ABSENT ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ABILITY ENABLE CONSTRAINTS FK1_PER_ABILITY ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_INVEST2DTL ENABLE CONSTRAINTS FK2_PER_INVEST2DTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_INVEST1DTL ENABLE CONSTRAINTS FK2_PER_INVEST1DTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_HEIR ENABLE CONSTRAINTS FK1_PER_HEIR ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_EXTRA_INCOMEHIS ENABLE CONSTRAINTS FK1_PER_EXTRA_INCOMEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_EXTRAHIS ENABLE CONSTRAINTS FK1_PER_EXTRAHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_KPI ENABLE CONSTRAINTS FK1_PER_KPI ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_FAMILY ENABLE CONSTRAINTS FK1_PER_FAMILY ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_att->free_result();
		$db_att1->free_result();
	} // end if
	
	if($command=='PER_EMP'){
		$cmd = " ALTER TABLE PER_TRAINING DISABLE CONSTRAINTS FK1_PER_TRAINING ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PUNISHMENT DISABLE CONSTRAINTS FK1_PER_PUNISHMENT ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_DECORDTL DISABLE CONSTRAINTS FK3_PER_DECORDTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_DECORATEHIS DISABLE CONSTRAINTS FK1_PER_DECORATEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_COURSE DISABLE CONSTRAINTS FK4_PER_COURSE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_COURSEDTL DISABLE CONSTRAINTS FK2_PER_COURSEDTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SERVICEHIS DISABLE CONSTRAINTS FK1_PER_SERVICEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SERVICEHIS DISABLE CONSTRAINTS FK5_PER_SERVICEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SALPROMOTE DISABLE CONSTRAINTS FK2_PER_SALPROMOTE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SALARYHIS DISABLE CONSTRAINTS FK1_PER_SALARYHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PROMOTE_P DISABLE CONSTRAINTS FK2_PER_PROMOTE_P ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PROMOTE_E DISABLE CONSTRAINTS FK2_PER_PROMOTE_E ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PROMOTE_C DISABLE CONSTRAINTS FK1_PER_PROMOTE_C ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ORG_JOB DISABLE CONSTRAINTS FK1_PER_ORG_JOB ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_MOVE_REQ DISABLE CONSTRAINTS FK1_PER_MOVE_REQ ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_LETTER DISABLE CONSTRAINTS FK1_PER_LETTER ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_LETTER DISABLE CONSTRAINTS FK2_PER_LETTER ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_BONUSPROMOTE DISABLE CONSTRAINTS FK2_PER_BONUSPROMOTE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_TIMEHIS DISABLE CONSTRAINTS FK1_PER_TIMEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SCHOLAR DISABLE CONSTRAINTS FK1_PER_SCHOLAR ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_REWARDHIS DISABLE CONSTRAINTS FK1_PER_REWARDHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_POSITIONHIS DISABLE CONSTRAINTS FK1_PER_POSITIONHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PERSONALPIC DISABLE CONSTRAINTS FK1_PER_PERSONALPIC ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_NAMEHIS DISABLE CONSTRAINTS FK1_PER_NAMEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_MARRHIS DISABLE CONSTRAINTS FK1_PER_MARRHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_EDUCATE DISABLE CONSTRAINTS FK1_PER_EDUCATE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ABSENTHIS DISABLE CONSTRAINTS FK1_PER_ABSENTHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ABSENT DISABLE CONSTRAINTS FK1_PER_ABSENT ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ABILITY DISABLE CONSTRAINTS FK1_PER_ABILITY ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_INVEST2DTL DISABLE CONSTRAINTS FK2_PER_INVEST2DTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_INVEST1DTL DISABLE CONSTRAINTS FK2_PER_INVEST1DTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_HEIR DISABLE CONSTRAINTS FK1_PER_HEIR ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_EXTRA_INCOMEHIS DISABLE CONSTRAINTS FK1_PER_EXTRA_INCOMEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_EXTRAHIS DISABLE CONSTRAINTS FK1_PER_EXTRAHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_KPI DISABLE CONSTRAINTS FK1_PER_KPI ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$table = array( "per_absenthis", "per_marrhis", "per_timehis", "per_ability", "per_rewardhis", "per_extrahis", "per_extra_incomehis", "per_namehis", 
			"per_punishment", "per_decoratehis", "per_training", "per_educate", "per_salaryhis", "per_positionhis", "per_personalpic", "per_heir", "per_servicehis", 
			"per_comdtl", "per_bonuspromote", "per_bonusquotadtl2", "per_bonusquotadtl1", "per_bonusquota", "per_special_skill", "per_coursedtl", "per_decordtl", "per_org_job", 
			"per_promote_p", "per_letter", "per_move_req", "per_scholar", "per_absent", "per_invest1dtl", "per_promote_c", "per_address", "per_kpi_form", 
			"per_salpromote" );
		for ( $i=0; $i<count($table); $i++ ) { 
			if ($table[$i]=="per_bonusquota" || $table[$i]=="per_scholar" || $table[$i]=="per_salquota" || $table[$i]=="per_command" || $table[$i]=="per_decor")
				$cmd = " delete from $table[$i] ";
			else
				$cmd = " truncate table $table[$i] ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end for

// ลูกจ้างประจำ 49245 - 49245
		$cmd = " DELETE FROM PER_PERSONAL WHERE PER_TYPE = 2 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_att->free_result();
		$db_att1->free_result();

		$cmd = " select max(PER_ID) as MAX_ID from PER_PERSONAL ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_ID = $data[MAX_ID] + 1;

		$cmd = " SELECT a.ID, FLAG_PERSON_TYPE, RANK_CODE, FNAME, LNAME, SEX, FLAG_TYPE, to_char(BORN,'yyyy-mm-dd') as BORN, 
						to_char(RET_BORN_DATE,'yyyy-mm-dd') as RET_BORN_DATE, to_char(RET_BORN_YEAR,'yyyy-mm-dd') as RET_BORN_YEAR, 
						to_char(BEGIN_ENTRY_DATE,'yyyy-mm-dd') as BEGIN_ENTRY_DATE, 
						to_char(RETURN_OCCUPY_DATE,'yyyy-mm-dd') as RETURN_OCCUPY_DATE, 
						to_char(MP_FORCE_DATE,'yyyy-mm-dd') as MP_FORCE_DATE, to_char(UPCLASS_DATE,'yyyy-mm-dd') as UPCLASS_DATE, 
						to_char(WORK_LINE_DATE,'yyyy-mm-dd') as WORK_LINE_DATE, POS_NUM_CODE_SIT, MP_COMMAND_NUM, FLAG_TO_NAME_CODE, 
						to_char(MP_POS_DATE,'yyyy-mm-dd') as MP_POS_DATE, MP_YEAR, POS_NUM_NAME, POS_NUM_CODE, CLUSTER_CODE, 
						CATEGORY_SAL_CODE, CATEGORY_SAL_NAME, WORK_LINE_CODE, WORK_LINE_NAME, SALARY_LEVEL_CODE, SALARY, 
						MP_FLAG, SALARY_ADD, SPECIAL_PERCENT, SPECIAL_AMT, PAYMENT_AMT, PAYMENT_PERCENT, MP_FLAG_1, 
						COST_LIVING_AMOUNT, JOB_CODE, JOB_NAME, SECTION_CODE, SECTION_NAME, DIVISION_CODE, DIVISION_NAME, 
						DEPARTMENT_CODE, DEPARTMENT_NAME, FLAG_CUR_ST, BOARD_TYPE, EMP_FIXED_TYPE, 
						to_char(MP_COMMAND_DATE,'yyyy-mm-dd') as MP_COMMAND_DATE, RET_BORN_MP_YEAR, CUR_YEAR, FLAG_TO_NAME, 
						POS_NUM_CODE_SIT_ABB, POS_NUM_CODE_SIT_CODE, POS_NUM_CODE_SIT_O, POS_NUM_CODE_SIT_ABB_O, 
						POS_NUM_CODE_SIT_CODE_O, MP_COMMAND_NUM_O, CUR_YEAR_O, MP_YEAR_O, FLAG_TO_NAME_CODE_O, 
						FLAG_TO_NAME_O, to_char(MP_COMMAND_DATE_O,'yyyy-mm-dd') as MP_COMMAND_DATE_O, 
						to_char(MP_POS_DATE_O,'yyyy-mm-dd') as MP_POS_DATE_O, MP_FLAG_O, MP_FLAG_1_O, SALARY_LEVEL_CODE_O, 
						SALARY_O, SPECIAL_AMT_O, SALARY_ADD_O, CLUSTER_CODE_O, SALARY_D, SALARY_D_O, SPECIAL_AMT_D, 
						SPECIAL_AMT_D_O, MARRIAGE_STATE, SUN_NO, HELP_LIVING_AMOUNT, a.USER_CREATE, 
						to_char(a.CREATE_DATE,'yyyy-mm-dd') as CREATE_DATE, a.USER_UPDATE, to_char(a.UPDATE_DATE,'yyyy-mm-dd') as UPDATE_DATE,
						TAX_ID, PASSPORT_CODE, HEIR_SUBSCR_NAME, SPECI_HELP_SUBSCR_NAME, SPECI_ABILITY_DATA, EXTRACTION, 
						CITIZENSHIP, RELIGION, BORN_REGIS, BLOOD, BLOOD_RH, FLAG_KBK, FLAG_TYPE_KBK
						FROM HR_PERSONAL_EMP a, HR_PERSONAL_EMP_DETAIL b
						WHERE a.ID = b.ID(+) 
						ORDER BY FLAG_PERSON_TYPE, a.ID ";
		$db_att->send_cmd($cmd);
//							WHERE ID > '1' AND ID < '2'
//		$db_att->show_error();
//		echo "<br>";
		while($data = $db_att->get_array()){
			$PER_PERSONAL++;
			$PN_CODE = trim($data[RANK_CODE]);
			if ($PN_CODE=="-") $PN_CODE = "000";
			$PER_NAME = trim($data[FNAME]);
			$PER_SURNAME = trim($data[LNAME]);
			$OT_CODE = trim($data[FLAG_PERSON_TYPE]);
			$PER_TYPE = 2;
			$PER_CARDNO = trim($data[ID]);
			$PER_GENDER = trim($data[SEX]);
			if (!$PER_GENDER) $PER_GENDER = 1;
			$PER_BIRTHDATE = trim($data[BORN]);
			$PER_RETIREDATE = trim($data[RET_BORN_YEAR]); 
			$PER_STARTDATE = trim($data[BEGIN_ENTRY_DATE]);
			$PER_OCCUPYDATE = trim($data[RETURN_OCCUPY_DATE]);
			$LEVEL_NO = str_pad(trim($data[MP_CEE]), 2, "0", STR_PAD_LEFT);
			$POS_NO_NAME = trim($data[POS_NUM_NAME]);
			$POS_NO = trim($data[POS_NUM_CODE]);
			$PER_SALARY = $data[SALARY]+0;
			$MOV_CODE = trim($data[FLAG_TO_NAME_CODE]);
			$FLAG_CUR_ST = trim($data[FLAG_CUR_ST]);
			if ($FLAG_CUR_ST == 1) $PER_STATUS = 1; else $PER_STATUS = 2;
			$MR_CODE = trim($data[MARRIAGE_STATE]);
			$RELIGION = trim($data[RELIGION]);
			if (!$RELIGION) $RE_CODE = '0';
			elseif ($RELIGION=="พุทธ") $RE_CODE = '1';
			else $RE_CODE = $RELIGION;
			$BLOOD = trim($data[BLOOD]);
			if ($BLOOD=="1") $PER_BLOOD = "A";
			elseif ($BLOOD=="2") $PER_BLOOD = "B";
			elseif ($BLOOD=="4") $PER_BLOOD = "O";
			elseif ($BLOOD=="3" || $BLOODGRP=="เอ บี") $PER_BLOOD = "AB";
			else $PER_BLOOD = "-";
			$PER_TAXNO = trim($data[TAX_ID]);
			$FLAG_KBK = trim($data[FLAG_KBK]); 
			if ($FLAG_KBK=="1") $PER_MEMBER = 1;
			else  $PER_MEMBER = 0;

			$UPDATE_USER = 99999;
			if ($data[USER_UPDATE]) $USER_UPDATE = trim($data[USER_UPDATE]);
			else $USER_UPDATE = trim($data[USER_CREATE]);
			if ($data[UPDATE_DATE]) $UPDATE_DATE = trim($data[UPDATE_DATE]);
			else  $UPDATE_DATE = trim($data[CREATE_DATE]);
			if ($USER_UPDATE) {
				$cmd = " select ID from USER_DETAIL where USERNAME = '$USER_UPDATE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				if ($data2[ID]) $UPDATE_USER = $data2[ID];
			}
			if (!$UPDATE_DATE) $UPDATE_DATE = date("Y-m-d H:i:s");
/*
			$MILITARYSTATUS = strtoupper(trim($data[MILITARYSTATUS])); 
			$ORDAINSTATUS = strtoupper(trim($data[ORDAINSTATUS])); 
			$ORDAINDETAIL = trim($data[ORDAINDETAIL]); 
			$GBCDETAIL = trim($data[GBCDETAIL]); 
			$COOPSTATUS = strtoupper(trim($data[COOPSTATUS])); 
			$COOPDETAIL = trim($data[COOPDETAIL]); 
			$ENGAGENAME = trim($data[ENGAGENAME]);
			$PERSONSTATUSID = $data[PERSONSTATUSID];
			$SUBPERSONSTATUSID = $data[SUBPERSONSTATUSID];
			$PERSONPOSITIONID = $data[PERSONPOSITIONID];
			$PER_POSDATE = trim($data[PSTATUSEFFECTIVEDATE]);
			$ES_CODE = str_pad(trim($data[EMP_STATUS]), 2, "0", STR_PAD_LEFT);
			$OLDPOSCODE = trim($data[OLDPOSCODE]);
			$OLDPOSNAME = trim($data[OLDPOSNAME]);
			$OLDSECCODE = trim($data[OLDSECCODE]);
			$OLDSECNAME = trim($data[OLDSECNAME]);
			$PAYMENTNO = $data[PAYMENTNO];
			$PER_START_ORG = trim($data[FIRSTENTRANCEPLACE]);
			$PV_CODE = trim($data[PV_CODE]);
			if ($PV_CODE) $PV_CODE .= "00";
			$PER_DOCNO = trim($data[PSTATUSORDERNO]);
			$PER_DOCDATE = trim($data[PSTATUSORDERDATE]);
			if ($PER_DOCDATE=="0000-00-00") $PER_DOCDATE = "";
			$PER_REMARK = trim($data[REMARK]);
			$SPECIALCAPA = trim($data[SPECIALCAPA]); // PER_ABILITY
			$PER_EFFECTIVEDATE = trim($data[PSTATUSEFFECTIVEDATE]);
			$PER_POS_REASON= trim($data[PSTATUSREASON]);
			$PER_POS_YEAR = trim($data[PSTATUSYEAR]);
			$PER_POS_DOCTYPE = trim($data[PSTATUSDOCREFER]);
			$PER_POS_DOCNO = trim($data[PSTATUSDOCREFERNO]);
			$PER_POS_ORG = trim($data[PSTATUSSECREFER]);
*/
			$PER_MGTSALARY = 0; // รอแก้

			$POS_ID = "NULL";
			$POEM_ID = "NULL";
			$POEMS_ID = "NULL";
			$POT_ID = "NULL";
			$PER_ORGMGT = 0;

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_POS_EMP' AND OLD_CODE = '$POS_NO_NAME$POS_NO' ";
			$db_dpis2->send_cmd($cmd);
			//$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$POEM_ID = $data2[NEW_CODE] + 0;

			$cmd = " select DEPARTMENT_ID from PER_POS_EMP where POEM_ID = $POEM_ID ";
			$db_dpis2->send_cmd($cmd);
			//$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$DEPARTMENT_ID = $data2[DEPARTMENT_ID] + 0;

			if ($POS_ORGMGT) $PER_ORGMGT = 1;
				
			$ASS_ORG_ID = "NULL";
			$LEVEL_NO = trim($LEVEL_NO)? "'".$LEVEL_NO."'" : "NULL";
			if ($LEVEL_NO=="'00'" || $LEVEL_NO=="'0-'") $LEVEL_NO = "NULL";
			if ($POS_ID==0) $POS_ID = "NULL";
			if ($POEM_ID==0) $POEM_ID = "NULL";
			if ($POEMS_ID==0) $POEMS_ID = "NULL";
			if ($POT_ID==0) $POT_ID = "NULL";
			if (!$PAY_ID) $PAY_ID = "NULL";
			if (!$OT_CODE) $OT_CODE = "01";
			if (!$MR_CODE) $MR_CODE = "9";
			if (!$PER_STARTDATE) $PER_STARTDATE = "-";
//			if (!$PER_OCCUPYDATE) $PER_OCCUPYDATE = "-";

			if ($MILITARYSTATUS=="Y") $PER_SOLDIER = 1;
			else  $PER_SOLDIER = 0;

			if ($ORDAINSTATUS=="Y") $PER_ORDAIN = 1;
			else  $PER_ORDAIN = 0;

			$PER_MEMBERDATE = "";
			if ($GBCDETAIL) { 
				$temp_date = explode("/", $GBCDETAIL);
				$PER_MEMBERDATE = ($temp_date)? ($temp_date[2] - 543) ."-". str_pad(trim($temp_date[1]), 2, "0", STR_PAD_LEFT) ."-". str_pad(trim($temp_date[0]), 2, "0", STR_PAD_LEFT) : "";
			}

			if ($COOPSTATUS=="Y") $PER_COOPERATIVE = 1;
			else  $PER_COOPERATIVE = 0;

			if (!$PER_GENDER)
				if ($PN_CODE == "003") $PER_GENDER = 1;
				elseif ($PN_CODE == "004" || $PN_CODE == "005") $PER_GENDER = 2;
			$MOV_CODE = "0"; // รอแก้
			$ES_CODE = "02"; 

			$cmd = " INSERT INTO PER_PERSONAL (PER_ID, PER_TYPE, OT_CODE, PN_CODE, PER_NAME, PER_SURNAME, 
							PER_ENG_NAME, PER_ENG_SURNAME, ORG_ID, POS_ID, POEM_ID, POEMS_ID, POT_ID, LEVEL_NO, PER_ORGMGT, 
							PER_SALARY, PER_MGTSALARY, PER_SPSALARY, PER_GENDER, MR_CODE, PER_CARDNO, PER_OFFNO, 
							PER_TAXNO, PER_BLOOD, RE_CODE, PER_BIRTHDATE, PER_RETIREDATE, PER_STARTDATE, 
							PER_OCCUPYDATE, PER_POSDATE, PN_CODE_F, PER_FATHERNAME, PER_FATHERSURNAME, PN_CODE_M, 
							PER_MOTHERNAME, PER_MOTHERSURNAME, PV_CODE, MOV_CODE, PER_ORDAIN, PER_ORDAIN_DETAIL, 
							PER_SOLDIER, PER_MEMBER, PER_MEMBERDATE, PER_STATUS, PER_HIP_FLAG, PER_CERT_OCC, 
							DEPARTMENT_ID, LEVEL_NO_SALARY, UPDATE_USER, UPDATE_DATE, PER_HOME_TEL, PER_FILE_NO, 
							PER_BANK_ACCOUNT, PER_REMARK, PER_START_ORG, PAY_ID, ES_CODE, ORG_NAME_WORK, 
							PL_NAME_WORK, PER_DOCNO, PER_DOCDATE, PER_COOPERATIVE, PER_COOPERATIVE_NO,
							PER_EFFECTIVEDATE, PER_POS_REASON, PER_POS_YEAR, PER_POS_DOCTYPE, PER_POS_DOCNO, PER_POS_ORG)
							VALUES ($MAX_ID, $PER_TYPE, '$OT_CODE', '$PN_CODE', trim('$PER_NAME'), trim('$PER_SURNAME'), 
							trim('$PER_ENG_NAME'), trim('$PER_ENG_SURNAME'), $ASS_ORG_ID, $POS_ID, $POEM_ID, $POEMS_ID, $POT_ID, 
							$LEVEL_NO, $PER_ORGMGT, $PER_SALARY, $PER_MGTSALARY, 0, $PER_GENDER, '$MR_CODE', '$PER_CARDNO', 
							'$PER_OFFNO', '$PER_TAXNO', '$PER_BLOOD', '$RE_CODE', '$PER_BIRTHDATE', 	'$PER_RETIREDATE', '$PER_STARTDATE', 
							'$PER_OCCUPYDATE', '$PER_POSDATE', '$PN_CODE_F', trim('$PER_FATHERNAME'), 
							trim('$PER_FATHERSURNAME'), '$PN_CODE_M', trim('$PER_MOTHERNAME'), 
							trim('$PER_MOTHERSURNAME'), '$PV_CODE', '$MOV_CODE', $PER_ORDAIN, '$ORDAINDETAIL', $PER_SOLDIER, 
							$PER_MEMBER, '$PER_MEMBERDATE', $PER_STATUS, NULL, NULL, $DEPARTMENT_ID, $LEVEL_NO, $UPDATE_USER, 
							'$UPDATE_DATE', NULL, NULL, NULL, '$PER_REMARK', '$PER_START_ORG', $PAY_ID, '$ES_CODE', 
							'$OLDSECNAME', '$OLDPOSNAME', '$PER_DOCNO', '$PER_DOCDATE', $PER_COOPERATIVE, '$COOPDETAIL',
							'$PER_EFFECTIVEDATE', '$PER_POS_REASON', '$PER_POS_YEAR', '$PER_POS_DOCTYPE', '$PER_POS_DOCNO', 
							'$PER_POS_ORG') ";
			$db_dpis->send_cmd($cmd);

			$cmd1 = " SELECT PER_ID FROM PER_PERSONAL WHERE PER_ID = $MAX_ID "; 
			$count_data = $db_dpis1->send_cmd($cmd1);
			if (!$count_data) {
				echo "$cmd<br>==================<br>";
				$db_dpis->show_error();
				echo "<br>end ". ++$i  ."=======================<br>";
			}

			$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
							VALUES ('PER_PERSONAL', '$PER_CARDNO', '$MAX_ID', $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$MAX_ID++;
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_PERSONAL where PER_TYPE = 2 ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_PERSONAL - $PER_PERSONAL - $COUNT_NEW<br>";
		
		$cmd = " ALTER TABLE PER_TRAINING ENABLE CONSTRAINTS FK1_PER_TRAINING ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PUNISHMENT ENABLE CONSTRAINTS FK1_PER_PUNISHMENT ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_DECORDTL ENABLE CONSTRAINTS FK3_PER_DECORDTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_DECORATEHIS ENABLE CONSTRAINTS FK1_PER_DECORATEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_COURSE ENABLE CONSTRAINTS FK4_PER_COURSE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_COURSEDTL ENABLE CONSTRAINTS FK2_PER_COURSEDTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SERVICEHIS ENABLE CONSTRAINTS FK1_PER_SERVICEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SERVICEHIS ENABLE CONSTRAINTS FK5_PER_SERVICEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SALPROMOTE ENABLE CONSTRAINTS FK2_PER_SALPROMOTE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SALARYHIS ENABLE CONSTRAINTS FK1_PER_SALARYHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PROMOTE_P ENABLE CONSTRAINTS FK2_PER_PROMOTE_P ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PROMOTE_E ENABLE CONSTRAINTS FK2_PER_PROMOTE_E ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PROMOTE_C ENABLE CONSTRAINTS FK1_PER_PROMOTE_C ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ORG_JOB ENABLE CONSTRAINTS FK1_PER_ORG_JOB ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_MOVE_REQ ENABLE CONSTRAINTS FK1_PER_MOVE_REQ ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_LETTER ENABLE CONSTRAINTS FK1_PER_LETTER ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_LETTER ENABLE CONSTRAINTS FK2_PER_LETTER ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_BONUSPROMOTE ENABLE CONSTRAINTS FK2_PER_BONUSPROMOTE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_TIMEHIS ENABLE CONSTRAINTS FK1_PER_TIMEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SCHOLAR ENABLE CONSTRAINTS FK1_PER_SCHOLAR ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_REWARDHIS ENABLE CONSTRAINTS FK1_PER_REWARDHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_POSITIONHIS ENABLE CONSTRAINTS FK1_PER_POSITIONHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PERSONALPIC ENABLE CONSTRAINTS FK1_PER_PERSONALPIC ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_NAMEHIS ENABLE CONSTRAINTS FK1_PER_NAMEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_MARRHIS ENABLE CONSTRAINTS FK1_PER_MARRHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_EDUCATE ENABLE CONSTRAINTS FK1_PER_EDUCATE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ABSENTHIS ENABLE CONSTRAINTS FK1_PER_ABSENTHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ABSENT ENABLE CONSTRAINTS FK1_PER_ABSENT ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ABILITY ENABLE CONSTRAINTS FK1_PER_ABILITY ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_INVEST2DTL ENABLE CONSTRAINTS FK2_PER_INVEST2DTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_INVEST1DTL ENABLE CONSTRAINTS FK2_PER_INVEST1DTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_HEIR ENABLE CONSTRAINTS FK1_PER_HEIR ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_EXTRA_INCOMEHIS ENABLE CONSTRAINTS FK1_PER_EXTRA_INCOMEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_EXTRAHIS ENABLE CONSTRAINTS FK1_PER_EXTRAHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_KPI ENABLE CONSTRAINTS FK1_PER_KPI ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_att->free_result();
		$db_att1->free_result();
	} // end if
	
	if($command=='PER_TEMP'){
		$cmd = " ALTER TABLE PER_TRAINING DISABLE CONSTRAINTS FK1_PER_TRAINING ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PUNISHMENT DISABLE CONSTRAINTS FK1_PER_PUNISHMENT ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_DECORDTL DISABLE CONSTRAINTS FK3_PER_DECORDTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_DECORATEHIS DISABLE CONSTRAINTS FK1_PER_DECORATEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_COURSE DISABLE CONSTRAINTS FK4_PER_COURSE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_COURSEDTL DISABLE CONSTRAINTS FK2_PER_COURSEDTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SERVICEHIS DISABLE CONSTRAINTS FK1_PER_SERVICEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SERVICEHIS DISABLE CONSTRAINTS FK5_PER_SERVICEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SALPROMOTE DISABLE CONSTRAINTS FK2_PER_SALPROMOTE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SALARYHIS DISABLE CONSTRAINTS FK1_PER_SALARYHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PROMOTE_P DISABLE CONSTRAINTS FK2_PER_PROMOTE_P ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PROMOTE_E DISABLE CONSTRAINTS FK2_PER_PROMOTE_E ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PROMOTE_C DISABLE CONSTRAINTS FK1_PER_PROMOTE_C ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ORG_JOB DISABLE CONSTRAINTS FK1_PER_ORG_JOB ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_MOVE_REQ DISABLE CONSTRAINTS FK1_PER_MOVE_REQ ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_LETTER DISABLE CONSTRAINTS FK1_PER_LETTER ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_LETTER DISABLE CONSTRAINTS FK2_PER_LETTER ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_BONUSPROMOTE DISABLE CONSTRAINTS FK2_PER_BONUSPROMOTE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_TIMEHIS DISABLE CONSTRAINTS FK1_PER_TIMEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SCHOLAR DISABLE CONSTRAINTS FK1_PER_SCHOLAR ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_REWARDHIS DISABLE CONSTRAINTS FK1_PER_REWARDHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_POSITIONHIS DISABLE CONSTRAINTS FK1_PER_POSITIONHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PERSONALPIC DISABLE CONSTRAINTS FK1_PER_PERSONALPIC ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_NAMEHIS DISABLE CONSTRAINTS FK1_PER_NAMEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_MARRHIS DISABLE CONSTRAINTS FK1_PER_MARRHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_EDUCATE DISABLE CONSTRAINTS FK1_PER_EDUCATE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ABSENTHIS DISABLE CONSTRAINTS FK1_PER_ABSENTHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ABSENT DISABLE CONSTRAINTS FK1_PER_ABSENT ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ABILITY DISABLE CONSTRAINTS FK1_PER_ABILITY ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_INVEST2DTL DISABLE CONSTRAINTS FK2_PER_INVEST2DTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_INVEST1DTL DISABLE CONSTRAINTS FK2_PER_INVEST1DTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_HEIR DISABLE CONSTRAINTS FK1_PER_HEIR ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_EXTRA_INCOMEHIS DISABLE CONSTRAINTS FK1_PER_EXTRA_INCOMEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_EXTRAHIS DISABLE CONSTRAINTS FK1_PER_EXTRAHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_KPI DISABLE CONSTRAINTS FK1_PER_KPI ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$table = array( "per_absenthis", "per_marrhis", "per_timehis", "per_ability", "per_rewardhis", "per_extrahis", "per_extra_incomehis", "per_namehis", 
			"per_punishment", "per_decoratehis", "per_training", "per_educate", "per_salaryhis", "per_positionhis", "per_personalpic", "per_heir", "per_servicehis", 
			"per_comdtl", "per_bonuspromote", "per_bonusquotadtl2", "per_bonusquotadtl1", "per_bonusquota", "per_special_skill", "per_coursedtl", "per_decordtl", "per_org_job", 
			"per_promote_p", "per_letter", "per_move_req", "per_scholar", "per_absent", "per_invest1dtl", "per_promote_c", "per_address", "per_kpi_form", 
			"per_salpromote" );
		for ( $i=0; $i<count($table); $i++ ) { 
			if ($table[$i]=="per_bonusquota" || $table[$i]=="per_scholar" || $table[$i]=="per_salquota" || $table[$i]=="per_command" || $table[$i]=="per_decor")
				$cmd = " delete from $table[$i] ";
			else
				$cmd = " truncate table $table[$i] ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end for

// ลูกจ้างชั่วคราว 27062 - 27062
		$cmd = " DELETE FROM PER_PERSONAL WHERE PER_TYPE = 4 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_att->free_result();
		$db_att1->free_result();

		$cmd = " select max(PER_ID) as MAX_ID from PER_PERSONAL ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_ID = $data[MAX_ID] + 1;

		$cmd = " SELECT a.ID, FLAG_PERSON_TYPE, BOARD_TYPE, RANK_CODE, FNAME, LNAME, SEX, FLAG_TYPE, 
						to_char(BORN,'yyyy-mm-dd') as BORN, MP_YEAR, POS_NUM_CODE_SIT, POS_NUM_CODE_SIT_ABB, 
						MP_COMMAND_NUM, CUR_YEAR, to_char(MP_COMMAND_DATE,'yyyy-mm-dd') as MP_COMMAND_DATE, 
						to_char(MP_POS_DATE,'yyyy-mm-dd') as MP_POS_DATE, FLAG_TO_NAME_CODE, FLAG_TO_NAME, 
						JOB_CODE, JOB_NAME, SECTION_CODE, SECTION_NAME, DIVISION_CODE, DIVISION_NAME, 
						DEPARTMENT_CODE, DEPARTMENT_NAME, POS_NUM_CODE, POS_NUM_NAME, POS_NUM_CODE_R, 
						CLUSTER_CODE, CATEGORY_SAL_CODE, CATEGORY_SAL_NAME, WORK_LINE_CODE, WORK_LINE_NAME, 
						SALARY_LEVEL_CODE, SALARY, COST_LIVING_AMOUNT, to_char(BEGIN_ENTRY_DATE,'yyyy-mm-dd') as BEGIN_ENTRY_DATE, to_char(START_DATE,'yyyy-mm-dd') as START_DATE, to_char(END_DATE,'yyyy-mm-dd') as END_DATE, 
						FLAG_POS_STATUS, FLAG_CUR_ST, FUNC_ID, BUDGET_TYPE, ITEM_ID, DETAIL, BUDGET_CODE, BUDGET_YEAR, 
						SOURCE_ID, BOOK_ID, FISCAL_YEAR, EXPENDITURE_ID, FUNC_YEAR, FUNC_SEQ, SECTOR_ID, SECTOR_NAME, 
						PROGRAM_ID, PROGRAM_NAME, EXP_OBJECT_ID, EXP_SUBOBJECT_ID, CENTRAL_FUND_ID, S_PROJECT_ID, 
						S_ACTIVITY_ID, S_EXP_OBJECT_ID, POS_NUM_CODE_SIT_CODE, HELP_LIVING_AMOUNT, a.USER_CREATE, 
						to_char(a.CREATE_DATE,'yyyy-mm-dd') as CREATE_DATE, a.USER_UPDATE, to_char(a.UPDATE_DATE,'yyyy-mm-dd') as UPDATE_DATE,
						PASSPORT_CODE, HEIR_SUBSCR_NAME, SPECI_HELP_SUBSCR_NAME, SPECI_ABILITY_DATA, EXTRACTION, 
						CITIZENSHIP, RELIGION, BORN_REGIS, BLOOD, BLOOD_RH, FLAG_KBK, FLAG_TYPE_KBK, TAX_ID, INSURE_GROVERMENT,
						to_char(INSURE_BEGIN_DATE,'yyyy-mm-dd') as INSURE_BEGIN_DATE, to_char(INSURE_END_DATE,'yyyy-mm-dd') as INSURE_END_DATE
						FROM HR_PERSONAL_EMPTEMP a, HR_PERSONAL_EMPTEMP_DETAIL b
						WHERE a.ID = b.ID(+)
						ORDER BY FLAG_PERSON_TYPE, a.ID ";
		$db_att->send_cmd($cmd);
//							WHERE ID > '1' AND ID < '2'
//		$db_att->show_error();
//		echo "<br>";
		while($data = $db_att->get_array()){
			$PER_PERSONAL++;
			$PN_CODE = trim($data[RANK_CODE]);
			$PER_NAME = trim($data[FNAME]);
			$PER_SURNAME = trim($data[LNAME]);
			$OT_CODE = trim($data[FLAG_PERSON_TYPE]);
			$PER_TYPE = 4;
			$PER_CARDNO = trim($data[ID]);
			$PER_GENDER = trim($data[SEX]);
			$PER_BIRTHDATE = trim($data[BORN]);
			$PER_RETIREDATE = trim($data[RET_BORN_YEAR]); 
			$PER_STARTDATE = trim($data[BEGIN_ENTRY_DATE]);
			$PER_OCCUPYDATE = trim($data[RETURN_OCCUPY_DATE]);
			$LEVEL_NO = str_pad(trim($data[MP_CEE]), 2, "0", STR_PAD_LEFT);
			$POS_NUM_NAME = trim($data[POS_NUM_NAME]);
			$POS_NUM_CODE = trim($data[POS_NUM_CODE]);
			$POS_NO = $POS_NUM_NAME.$POS_NUM_CODE;
			$PER_SALARY = $data[SALARY]+0;
			$MOV_CODE = trim($data[FLAG_TO_NAME_CODE]);
			$FLAG_CUR_ST = trim($data[FLAG_CUR_ST]);
			if ($FLAG_CUR_ST == 1) $PER_STATUS = 1; else $PER_STATUS = 2;
			$MR_CODE = trim($data[MARRIAGE_STATE]);
			$RELIGION = trim($data[RELIGION]);
			if (!$RELIGION) $RE_CODE = '0';
			elseif ($RELIGION=="พุทธ") $RE_CODE = '1';
			else $RE_CODE = $RELIGION;
			$BLOOD = trim($data[BLOOD]);
			if ($BLOOD=="1") $PER_BLOOD = "A";
			elseif ($BLOOD=="2") $PER_BLOOD = "B";
			elseif ($BLOOD=="4") $PER_BLOOD = "O";
			elseif ($BLOOD=="3" || $BLOODGRP=="เอ บี") $PER_BLOOD = "AB";
			else $PER_BLOOD = "-";
			$PER_TAXNO = trim($data[TAX_ID]);
			$FLAG_KBK = trim($data[FLAG_KBK]); 
			if ($FLAG_KBK=="1") $PER_MEMBER = 1;
			else  $PER_MEMBER = 0;

			$UPDATE_USER = 99999;
			if ($data[USER_UPDATE]) $USER_UPDATE = trim($data[USER_UPDATE]);
			else $USER_UPDATE = trim($data[USER_CREATE]);
			if ($data[UPDATE_DATE]) $UPDATE_DATE = trim($data[UPDATE_DATE]);
			else  $UPDATE_DATE = trim($data[CREATE_DATE]);
			if ($USER_UPDATE) {
				$cmd = " select ID from USER_DETAIL where USERNAME = '$USER_UPDATE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				if ($data2[ID]) $UPDATE_USER = $data2[ID];
			}
			if (!$UPDATE_DATE) $UPDATE_DATE = date("Y-m-d H:i:s");
/*
			$MILITARYSTATUS = strtoupper(trim($data[MILITARYSTATUS])); 
			$ORDAINSTATUS = strtoupper(trim($data[ORDAINSTATUS])); 
			$ORDAINDETAIL = trim($data[ORDAINDETAIL]); 
			$GBCDETAIL = trim($data[GBCDETAIL]); 
			$COOPSTATUS = strtoupper(trim($data[COOPSTATUS])); 
			$COOPDETAIL = trim($data[COOPDETAIL]); 
			$ENGAGENAME = trim($data[ENGAGENAME]);
			$PERSONSTATUSID = $data[PERSONSTATUSID];
			$SUBPERSONSTATUSID = $data[SUBPERSONSTATUSID];
			$PERSONPOSITIONID = $data[PERSONPOSITIONID];
			$PER_POSDATE = trim($data[PSTATUSEFFECTIVEDATE]);
			$ES_CODE = str_pad(trim($data[EMP_STATUS]), 2, "0", STR_PAD_LEFT);
			$OLDPOSCODE = trim($data[OLDPOSCODE]);
			$OLDPOSNAME = trim($data[OLDPOSNAME]);
			$OLDSECCODE = trim($data[OLDSECCODE]);
			$OLDSECNAME = trim($data[OLDSECNAME]);
			$PAYMENTNO = $data[PAYMENTNO];
			$PER_START_ORG = trim($data[FIRSTENTRANCEPLACE]);
			$PV_CODE = trim($data[PV_CODE]);
			if ($PV_CODE) $PV_CODE .= "00";
			$PER_DOCNO = trim($data[PSTATUSORDERNO]);
			$PER_DOCDATE = trim($data[PSTATUSORDERDATE]);
			if ($PER_DOCDATE=="0000-00-00") $PER_DOCDATE = "";
			$PER_REMARK = trim($data[REMARK]);
			$SPECIALCAPA = trim($data[SPECIALCAPA]); // PER_ABILITY
			$PER_EFFECTIVEDATE = trim($data[PSTATUSEFFECTIVEDATE]);
			$PER_POS_REASON= trim($data[PSTATUSREASON]);
			$PER_POS_YEAR = trim($data[PSTATUSYEAR]);
			$PER_POS_DOCTYPE = trim($data[PSTATUSDOCREFER]);
			$PER_POS_DOCNO = trim($data[PSTATUSDOCREFERNO]);
			$PER_POS_ORG = trim($data[PSTATUSSECREFER]);
*/
			$PER_MGTSALARY = 0; // รอแก้

			$POS_ID = "NULL";
			$POEM_ID = "NULL";
			$POEMS_ID = "NULL";
			$POT_ID = "NULL";
			$PER_ORGMGT = 0;

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_POS_TEMP' AND OLD_CODE = '$POS_NO' ";
			$db_dpis2->send_cmd($cmd);
			//$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$POT_ID = $data2[NEW_CODE] + 0;

			$cmd = " select DEPARTMENT_ID from PER_POS_TEMP where POT_ID = $POT_ID ";
			$db_dpis2->send_cmd($cmd);
			//$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$DEPARTMENT_ID = $data2[DEPARTMENT_ID] + 0;

			if ($POS_ORGMGT) $PER_ORGMGT = 1;
				
			$ASS_ORG_ID = "NULL";
			$LEVEL_NO = trim($LEVEL_NO)? "'".$LEVEL_NO."'" : "NULL";
			if ($LEVEL_NO=="'00'" || $LEVEL_NO=="'0-'") $LEVEL_NO = "NULL";
			if ($POS_ID==0) $POS_ID = "NULL";
			if ($POEM_ID==0) $POEM_ID = "NULL";
			if ($POEMS_ID==0) $POEMS_ID = "NULL";
			if ($POT_ID==0) $POT_ID = "NULL";
			if (!$PAY_ID) $PAY_ID = "NULL";
			if (!$OT_CODE) $OT_CODE = "01";
			if (!$MR_CODE) $MR_CODE = "9";
			if (!$PER_STARTDATE) $PER_STARTDATE = "-";
//			if (!$PER_OCCUPYDATE) $PER_OCCUPYDATE = "-";

			if ($MILITARYSTATUS=="Y") $PER_SOLDIER = 1;
			else  $PER_SOLDIER = 0;

			if ($ORDAINSTATUS=="Y") $PER_ORDAIN = 1;
			else  $PER_ORDAIN = 0;

			$PER_MEMBERDATE = "";
			if ($GBCDETAIL) { 
				$temp_date = explode("/", $GBCDETAIL);
				$PER_MEMBERDATE = ($temp_date)? ($temp_date[2] - 543) ."-". str_pad(trim($temp_date[1]), 2, "0", STR_PAD_LEFT) ."-". str_pad(trim($temp_date[0]), 2, "0", STR_PAD_LEFT) : "";
			}

			if ($COOPSTATUS=="Y") $PER_COOPERATIVE = 1;
			else  $PER_COOPERATIVE = 0;

			if (!$PER_GENDER)
				if ($PN_CODE == "003") $PER_GENDER = 1;
				elseif ($PN_CODE == "004" || $PN_CODE == "005") $PER_GENDER = 2;
			$MOV_CODE = "0"; // รอแก้
			$ES_CODE = "02"; 

			$cmd = " INSERT INTO PER_PERSONAL (PER_ID, PER_TYPE, OT_CODE, PN_CODE, PER_NAME, PER_SURNAME, 
							PER_ENG_NAME, PER_ENG_SURNAME, ORG_ID, POS_ID, POEM_ID, POEMS_ID, POT_ID, LEVEL_NO, PER_ORGMGT, 
							PER_SALARY, PER_MGTSALARY, PER_SPSALARY, PER_GENDER, MR_CODE, PER_CARDNO, PER_OFFNO, 
							PER_TAXNO, PER_BLOOD, RE_CODE, PER_BIRTHDATE, PER_RETIREDATE, PER_STARTDATE, 
							PER_OCCUPYDATE, PER_POSDATE, PN_CODE_F, PER_FATHERNAME, PER_FATHERSURNAME, PN_CODE_M, 
							PER_MOTHERNAME, PER_MOTHERSURNAME, PV_CODE, MOV_CODE, PER_ORDAIN, PER_ORDAIN_DETAIL, 
							PER_SOLDIER, PER_MEMBER, PER_MEMBERDATE, PER_STATUS, PER_HIP_FLAG, PER_CERT_OCC, 
							DEPARTMENT_ID, LEVEL_NO_SALARY, UPDATE_USER, UPDATE_DATE, PER_HOME_TEL, PER_FILE_NO, 
							PER_BANK_ACCOUNT, PER_REMARK, PER_START_ORG, PAY_ID, ES_CODE, ORG_NAME_WORK, 
							PL_NAME_WORK, PER_DOCNO, PER_DOCDATE, PER_COOPERATIVE, PER_COOPERATIVE_NO,
							PER_EFFECTIVEDATE, PER_POS_REASON, PER_POS_YEAR, PER_POS_DOCTYPE, PER_POS_DOCNO, PER_POS_ORG)
							VALUES ($MAX_ID, $PER_TYPE, '$OT_CODE', '$PN_CODE', trim('$PER_NAME'), trim('$PER_SURNAME'), 
							trim('$PER_ENG_NAME'), trim('$PER_ENG_SURNAME'), $ASS_ORG_ID, $POS_ID, $POEM_ID, $POEMS_ID, $POT_ID, 
							$LEVEL_NO, $PER_ORGMGT, $PER_SALARY, $PER_MGTSALARY, 0, $PER_GENDER, '$MR_CODE', '$PER_CARDNO', 
							'$PER_OFFNO', '$PER_TAXNO', '$PER_BLOOD', '$RE_CODE', '$PER_BIRTHDATE', 	'$PER_RETIREDATE', '$PER_STARTDATE', 
							'$PER_OCCUPYDATE', '$PER_POSDATE', '$PN_CODE_F', trim('$PER_FATHERNAME'), 
							trim('$PER_FATHERSURNAME'), '$PN_CODE_M', trim('$PER_MOTHERNAME'), 
							trim('$PER_MOTHERSURNAME'), '$PV_CODE', '$MOV_CODE', $PER_ORDAIN, '$ORDAINDETAIL', $PER_SOLDIER, 
							$PER_MEMBER, '$PER_MEMBERDATE', $PER_STATUS, NULL, NULL, $DEPARTMENT_ID, $LEVEL_NO, $UPDATE_USER, 
							'$UPDATE_DATE', NULL, NULL, NULL, '$PER_REMARK', '$PER_START_ORG', $PAY_ID, '$ES_CODE', 
							'$OLDSECNAME', '$OLDPOSNAME', '$PER_DOCNO', '$PER_DOCDATE', $PER_COOPERATIVE, '$COOPDETAIL',
							'$PER_EFFECTIVEDATE', '$PER_POS_REASON', '$PER_POS_YEAR', '$PER_POS_DOCTYPE', '$PER_POS_DOCNO', 
							'$PER_POS_ORG') ";
			$db_dpis->send_cmd($cmd);

			$cmd1 = " SELECT PER_ID FROM PER_PERSONAL WHERE PER_ID = $MAX_ID "; 
			$count_data = $db_dpis1->send_cmd($cmd1);
			if (!$count_data) {
				echo "$cmd<br>==================<br>";
				$db_dpis->show_error();
				echo "<br>end ". ++$i  ."=======================<br>";
			}

			$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
							VALUES ('PER_PERSONAL', '$PER_CARDNO', '$MAX_ID', $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$MAX_ID++;
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_PERSONAL where PER_TYPE = 4 ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_PERSONAL - $PER_PERSONAL - $COUNT_NEW<br>";
		
		$cmd = " ALTER TABLE PER_TRAINING ENABLE CONSTRAINTS FK1_PER_TRAINING ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PUNISHMENT ENABLE CONSTRAINTS FK1_PER_PUNISHMENT ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_DECORDTL ENABLE CONSTRAINTS FK3_PER_DECORDTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_DECORATEHIS ENABLE CONSTRAINTS FK1_PER_DECORATEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_COURSE ENABLE CONSTRAINTS FK4_PER_COURSE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_COURSEDTL ENABLE CONSTRAINTS FK2_PER_COURSEDTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SERVICEHIS ENABLE CONSTRAINTS FK1_PER_SERVICEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SERVICEHIS ENABLE CONSTRAINTS FK5_PER_SERVICEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SALPROMOTE ENABLE CONSTRAINTS FK2_PER_SALPROMOTE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SALARYHIS ENABLE CONSTRAINTS FK1_PER_SALARYHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PROMOTE_P ENABLE CONSTRAINTS FK2_PER_PROMOTE_P ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PROMOTE_E ENABLE CONSTRAINTS FK2_PER_PROMOTE_E ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PROMOTE_C ENABLE CONSTRAINTS FK1_PER_PROMOTE_C ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ORG_JOB ENABLE CONSTRAINTS FK1_PER_ORG_JOB ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_MOVE_REQ ENABLE CONSTRAINTS FK1_PER_MOVE_REQ ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_LETTER ENABLE CONSTRAINTS FK1_PER_LETTER ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_LETTER ENABLE CONSTRAINTS FK2_PER_LETTER ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_BONUSPROMOTE ENABLE CONSTRAINTS FK2_PER_BONUSPROMOTE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_TIMEHIS ENABLE CONSTRAINTS FK1_PER_TIMEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SCHOLAR ENABLE CONSTRAINTS FK1_PER_SCHOLAR ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_REWARDHIS ENABLE CONSTRAINTS FK1_PER_REWARDHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_POSITIONHIS ENABLE CONSTRAINTS FK1_PER_POSITIONHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PERSONALPIC ENABLE CONSTRAINTS FK1_PER_PERSONALPIC ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_NAMEHIS ENABLE CONSTRAINTS FK1_PER_NAMEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_MARRHIS ENABLE CONSTRAINTS FK1_PER_MARRHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_EDUCATE ENABLE CONSTRAINTS FK1_PER_EDUCATE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ABSENTHIS ENABLE CONSTRAINTS FK1_PER_ABSENTHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ABSENT ENABLE CONSTRAINTS FK1_PER_ABSENT ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ABILITY ENABLE CONSTRAINTS FK1_PER_ABILITY ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_INVEST2DTL ENABLE CONSTRAINTS FK2_PER_INVEST2DTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_INVEST1DTL ENABLE CONSTRAINTS FK2_PER_INVEST1DTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_HEIR ENABLE CONSTRAINTS FK1_PER_HEIR ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_EXTRA_INCOMEHIS ENABLE CONSTRAINTS FK1_PER_EXTRA_INCOMEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_EXTRAHIS ENABLE CONSTRAINTS FK1_PER_EXTRAHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_KPI ENABLE CONSTRAINTS FK1_PER_KPI ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_att->free_result();
		$db_att1->free_result();
	} // end if
	
	if( $command=='NAME' ){
		$cmd = " truncate table per_namehis ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

// เปลี่ยนชื่อข้าราชการ + ครู 13973 - 13774 ok
// เปลี่ยนชื่อข้าราชการ 9166 ok
		$cmd = " SELECT ID, to_char(EFFECT_DATE,'yyyy-mm-dd') as EFFECT_DATE, LAST_RANK_CODE, LAST_FNAME, 
						LAST_LNAME, NEW_RANK_CODE, NEW_FNAME, NEW_LNAME, REGISTER_TYPE, REGISTER_NO, REGISTER_C_NO, 
						to_char(REGISTER_DATE,'yyyy-mm-dd') as REGISTER_DATE, REGISTER_DEPT, PROVINCE_CODE, NAME_HUSBAND, 
						ORD_CHANGED, USER_CREATE, to_char(CREATE_DATE,'yyyy-mm-dd') as CREATE_DATE, USER_UPDATE,
						to_char(UPDATE_DATE,'yyyy-mm-dd') as UPDATE_DATE
						FROM HR_CHANGED_NAME
						ORDER BY ID, EFFECT_DATE ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		$MAX_ID = 1;
		while($data = $db_att->get_array()){
			$PER_CARDNO = trim($data[ID]);
			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = '$PER_CARDNO' ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			$data_dpis = $db_dpis->get_array();
			$PER_ID = $data_dpis[NEW_CODE] + 0;

			if ($PER_ID > 0) {
				$NH_DATE = trim($data[EFFECT_DATE]);
				$PN_CODE = $data[LAST_RANK_CODE];
				if (!$PN_CODE) $PN_CODE = "000";
				$NH_NAME = trim($data[LAST_FNAME]);
				if (!$NH_NAME) $NH_NAME = "-";
				$NH_SURNAME = trim($data[LAST_LNAME]);
				if (!$NH_SURNAME) $NH_SURNAME = "-";
				$NH_DOCNO = trim($data[REGISTER_NO]);
				$PN_CODE_NEW = $data[NEW_RANK_CODE];
				$NH_NAME_NEW = trim($data[NEW_FNAME]);
				$NH_SURNAME_NEW = trim($data[NEW_LNAME]);
				$NH_ORG = trim($data[REGISTER_DEPT]);
				$NH_REMARK = trim($data[REGISTER_TYPE])."  ".trim($data[NAME_HUSBAND]);
				$NH_BOOK_NO = trim($data[REGISTER_C_NO]);
				$NH_BOOK_DATE = trim($data[REGISTER_DATE]);

				$UPDATE_USER = 99999;
				if ($data[USER_UPDATE]) $USER_UPDATE = trim($data[USER_UPDATE]);
				else $USER_UPDATE = trim($data[USER_CREATE]);
				if ($data[UPDATE_DATE]) $UPDATE_DATE = trim($data[UPDATE_DATE]);
				else  $UPDATE_DATE = trim($data[CREATE_DATE]);
				if ($USER_UPDATE) {
					$cmd = " select ID from USER_DETAIL where USERNAME = '$USER_UPDATE' ";
					$db_dpis2->send_cmd($cmd);
	//				$db_dpis2->show_error();
					$data2 = $db_dpis2->get_array();
					if ($data2[ID]) $UPDATE_USER = $data2[ID];
				}
				if (!$UPDATE_DATE) $UPDATE_DATE = date("Y-m-d H:i:s");

				if (!$NH_DOCNO) $NH_DOCNO = "-";

				$cmd = " INSERT INTO PER_NAMEHIS(NH_ID, PER_ID, NH_DATE, PN_CODE, NH_NAME, NH_SURNAME, NH_DOCNO, 
								UPDATE_USER, UPDATE_DATE, PER_CARDNO, NH_ORG, PN_CODE_NEW, NH_NAME_NEW, NH_SURNAME_NEW, 
								NH_BOOK_NO, NH_BOOK_DATE, NH_REMARK)
								VALUES ($MAX_ID, $PER_ID, '$NH_DATE', '$PN_CODE', '$NH_NAME', '$NH_SURNAME', '$NH_DOCNO', 
								$UPDATE_USER, '$UPDATE_DATE', '$PER_CARDNO', '$NH_ORG', '$PN_CODE_NEW', '$NH_NAME_NEW', '$NH_SURNAME_NEW', 
								'$NH_BOOK_NO', '$NH_BOOK_DATE', '$NH_REMARK') ";
				$db_dpis->send_cmd($cmd);

				$cmd1 = " SELECT NH_ID FROM PER_NAMEHIS WHERE NH_ID = $MAX_ID "; 
				$count_data = $db_dpis1->send_cmd($cmd1);
				if (!$count_data) {
					echo "$cmd<br>==================<br>";
					$db_dpis->show_error();
					echo "<br>end ". ++$i  ."=======================<br>";
				}
				$MAX_ID++;
				$PER_NAMEHIS = $MAX_ID; 
			}
		} // end while						
		
		$PER_NAMEHIS--;
		$cmd = " select count(PER_ID) as COUNT_NEW from PER_NAMEHIS ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_NAMEHIS - $PER_NAMEHIS - $COUNT_NEW<br>";

// เปลี่ยนชื่อลูกจ้างประจำ 5263 ok
		$cmd = " SELECT ID, to_char(EFFECT_DATE,'yyyy-mm-dd') as EFFECT_DATE, LAST_RANK_CODE, LAST_FNAME, 
						LAST_LNAME, NEW_RANK_CODE, NEW_FNAME, NEW_LNAME, REGISTER_TYPE, REGISTER_NO, REGISTER_C_NO, 
						to_char(REGISTER_DATE,'yyyy-mm-dd') as REGISTER_DATE, REGISTER_DEPT, PROVINCE_CODE, NAME_HUSBAND, 
						ORD_CHANGED, USER_CREATE, to_char(CREATE_DATE,'yyyy-mm-dd') as CREATE_DATE, USER_UPDATE,
						to_char(UPDATE_DATE,'yyyy-mm-dd') as UPDATE_DATE
						FROM HR_CHANGED_NAME_EMP
						ORDER BY ID, EFFECT_DATE ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		while($data = $db_att->get_array()){
			$PER_CARDNO = trim($data[ID]);
			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = '$PER_CARDNO' ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			$data_dpis = $db_dpis->get_array();
			$PER_ID = $data_dpis[NEW_CODE] + 0;

			if ($PER_ID > 0) {
				$NH_DATE = trim($data[EFFECT_DATE]);
				$PN_CODE = $data[LAST_RANK_CODE];
				if (!$PN_CODE) $PN_CODE = "000";
				$NH_NAME = trim($data[LAST_FNAME]);
				if (!$NH_NAME) $NH_NAME = "-";
				$NH_SURNAME = trim($data[LAST_LNAME]);
				if (!$NH_SURNAME) $NH_SURNAME = "-";
				$NH_DOCNO = trim($data[REGISTER_NO]);
				$PN_CODE_NEW = $data[NEW_RANK_CODE];
				$NH_NAME_NEW = trim($data[NEW_FNAME]);
				$NH_SURNAME_NEW = trim($data[NEW_LNAME]);
				$NH_ORG = trim($data[REGISTER_DEPT]);
				$NH_REMARK = trim($data[REGISTER_TYPE])."  ".trim($data[NAME_HUSBAND]);
				$NH_BOOK_NO = trim($data[REGISTER_C_NO]);
				$NH_BOOK_DATE = trim($data[REGISTER_DATE]);

				$UPDATE_USER = 99999;
				if ($data[USER_UPDATE]) $USER_UPDATE = trim($data[USER_UPDATE]);
				else $USER_UPDATE = trim($data[USER_CREATE]);
				if ($data[UPDATE_DATE]) $UPDATE_DATE = trim($data[UPDATE_DATE]);
				else  $UPDATE_DATE = trim($data[CREATE_DATE]);
				if ($USER_UPDATE) {
					$cmd = " select ID from USER_DETAIL where USERNAME = '$USER_UPDATE' ";
					$db_dpis2->send_cmd($cmd);
	//				$db_dpis2->show_error();
					$data2 = $db_dpis2->get_array();
					if ($data2[ID]) $UPDATE_USER = $data2[ID];
				}
				if (!$UPDATE_DATE) $UPDATE_DATE = date("Y-m-d H:i:s");

				if (!$NH_DOCNO) $NH_DOCNO = "-";

				$cmd = " INSERT INTO PER_NAMEHIS(NH_ID, PER_ID, NH_DATE, PN_CODE, NH_NAME, NH_SURNAME, NH_DOCNO, 
								UPDATE_USER, UPDATE_DATE, PER_CARDNO, NH_ORG, PN_CODE_NEW, NH_NAME_NEW, NH_SURNAME_NEW, 
								NH_BOOK_NO, NH_BOOK_DATE, NH_REMARK)
								VALUES ($MAX_ID, $PER_ID, '$NH_DATE', '$PN_CODE', '$NH_NAME', '$NH_SURNAME', '$NH_DOCNO', 
								$UPDATE_USER, '$UPDATE_DATE', '$PER_CARDNO', '$NH_ORG', '$PN_CODE_NEW', '$NH_NAME_NEW', '$NH_SURNAME_NEW', 
								'$NH_BOOK_NO', '$NH_BOOK_DATE', '$NH_REMARK') ";
				$db_dpis->send_cmd($cmd);

				$cmd1 = " SELECT NH_ID FROM PER_NAMEHIS WHERE NH_ID = $MAX_ID "; 
				$count_data = $db_dpis1->send_cmd($cmd1);
				if (!$count_data) {
					echo "$cmd<br>==================<br>";
					$db_dpis->show_error();
					echo "<br>end ". ++$i  ."=======================<br>";
				}
				$MAX_ID++;
				$PER_NAMEHIS = $MAX_ID; 
			}
		} // end while						

		$PER_NAMEHIS--;
		$cmd = " select count(PER_ID) as COUNT_NEW from PER_NAMEHIS ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_NAMEHIS - $PER_NAMEHIS - $COUNT_NEW<br>";

// เปลี่ยนชื่อลูกจ้างชั่วคราว 1050 ok
		$cmd = " SELECT ID, to_char(EFFECT_DATE,'yyyy-mm-dd') as EFFECT_DATE, LAST_RANK_CODE, LAST_FNAME, 
						LAST_LNAME, NEW_RANK_CODE, NEW_FNAME, NEW_LNAME, REGISTER_TYPE, REGISTER_NO, REGISTER_C_NO, 
						to_char(REGISTER_DATE,'yyyy-mm-dd') as REGISTER_DATE, REGISTER_DEPT, PROVINCE_CODE, NAME_HUSBAND, 
						ORD_CHANGED, USER_CREATE, to_char(CREATE_DATE,'yyyy-mm-dd') as CREATE_DATE, USER_UPDATE,
						to_char(UPDATE_DATE,'yyyy-mm-dd') as UPDATE_DATE
						FROM HR_CHANGED_NAME_EMPTEMP
						ORDER BY ID, EFFECT_DATE ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		while($data = $db_att->get_array()){
			$PER_CARDNO = trim($data[ID]);
			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = '$PER_CARDNO' ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			$data_dpis = $db_dpis->get_array();
			$PER_ID = $data_dpis[NEW_CODE] + 0;

			if ($PER_ID > 0) {
				$NH_DATE = trim($data[EFFECT_DATE]);
				$PN_CODE = $data[LAST_RANK_CODE];
				if (!$PN_CODE) $PN_CODE = "000";
				$NH_NAME = trim($data[LAST_FNAME]);
				if (!$NH_NAME) $NH_NAME = "-";
				$NH_SURNAME = trim($data[LAST_LNAME]);
				if (!$NH_SURNAME) $NH_SURNAME = "-";
				$NH_DOCNO = trim($data[REGISTER_NO]);
				$PN_CODE_NEW = $data[NEW_RANK_CODE];
				$NH_NAME_NEW = trim($data[NEW_FNAME]);
				$NH_SURNAME_NEW = trim($data[NEW_LNAME]);
				$NH_ORG = trim($data[REGISTER_DEPT]);
				$NH_REMARK = trim($data[REGISTER_TYPE])."  ".trim($data[NAME_HUSBAND]);
				$NH_BOOK_NO = trim($data[REGISTER_C_NO]);
				$NH_BOOK_DATE = trim($data[REGISTER_DATE]);

				$UPDATE_USER = 99999;
				if ($data[USER_UPDATE]) $USER_UPDATE = trim($data[USER_UPDATE]);
				else $USER_UPDATE = trim($data[USER_CREATE]);
				if ($data[UPDATE_DATE]) $UPDATE_DATE = trim($data[UPDATE_DATE]);
				else  $UPDATE_DATE = trim($data[CREATE_DATE]);
				if ($USER_UPDATE) {
					$cmd = " select ID from USER_DETAIL where USERNAME = '$USER_UPDATE' ";
					$db_dpis2->send_cmd($cmd);
	//				$db_dpis2->show_error();
					$data2 = $db_dpis2->get_array();
					if ($data2[ID]) $UPDATE_USER = $data2[ID];
				}
				if (!$UPDATE_DATE) $UPDATE_DATE = date("Y-m-d H:i:s");

				if (!$NH_DOCNO) $NH_DOCNO = "-";

				$cmd = " INSERT INTO PER_NAMEHIS(NH_ID, PER_ID, NH_DATE, PN_CODE, NH_NAME, NH_SURNAME, NH_DOCNO, 
								UPDATE_USER, UPDATE_DATE, PER_CARDNO, NH_ORG, PN_CODE_NEW, NH_NAME_NEW, NH_SURNAME_NEW, 
								NH_BOOK_NO, NH_BOOK_DATE, NH_REMARK)
								VALUES ($MAX_ID, $PER_ID, '$NH_DATE', '$PN_CODE', '$NH_NAME', '$NH_SURNAME', '$NH_DOCNO', 
								$UPDATE_USER, '$UPDATE_DATE', '$PER_CARDNO', '$NH_ORG', '$PN_CODE_NEW', '$NH_NAME_NEW', '$NH_SURNAME_NEW', 
								'$NH_BOOK_NO', '$NH_BOOK_DATE', '$NH_REMARK') ";
				$db_dpis->send_cmd($cmd);

				$cmd1 = " SELECT NH_ID FROM PER_NAMEHIS WHERE NH_ID = $MAX_ID "; 
				$count_data = $db_dpis1->send_cmd($cmd1);
				if (!$count_data) {
					echo "$cmd<br>==================<br>";
					$db_dpis->show_error();
					echo "<br>end ". ++$i  ."=======================<br>";
				}
				$MAX_ID++;
				$PER_NAMEHIS = $MAX_ID;    
			}
		} // end while						

		$PER_NAMEHIS--;
		$cmd = " select count(PER_ID) as COUNT_NEW from PER_NAMEHIS ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_NAMEHIS - $PER_NAMEHIS - $COUNT_NEW<br>";

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_att->free_result();
		$db_att1->free_result();
	} // end if

	if( $command=='DECORATE' ){
// เครื่องราชย์ 64942 ok
		$cmd = " truncate table per_decoratehis ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

// เครื่องราชย์ข้าราชการ 55364
		$cmd = " SELECT ID, DECORATIONS_CODE, DECORATIONS_ABB, DECORATIONS_NAME, JOB_CODE, JOB_NAME, 
						  SECTION_CODE, SECTION_NAME, DIVISION_CODE, DIVISION_NAME, DEPARTMENT_CODE, DEPARTMENT_NAME, 
						  POS_NUM_NAME, POS_NUM_CODE, ADMIN_CODE, ADMIN_NAME, WORK_LINE_CODE, WORK_LINE_NAME, MP_CEE, 
						  SALARY_POS_CODE, SALARY_POS_ABB_NAME, to_char(PERMISSION_DATE,'yyyy-mm-dd') as PERMISSION_DATE, 
						  to_char(RECEIVED_DATE,'yyyy-mm-dd') as RECEIVED_DATE, to_char(RETURN_DATE,'yyyy-mm-dd') as RETURN_DATE, 
						  FLAG_WAYBILL, ISSUE, BOOK, PART, PAGE, ORDER_DECOR, to_char(DECORATION_DATE,'yyyy-mm-dd') as DECORATION_DATE, 
						  RECEIVE_AS, HONOR_STATUS, REC_STATUS, USER_CREATE, to_char(CREATE_DATE,'yyyy-mm-dd') as CREATE_DATE, 
						  USER_UPDATE, to_char(UPDATE_DATE,'yyyy-mm-dd') as UPDATE_DATE 
						  FROM HR_DECORATIONS_OFFICER
						  ORDER BY ID, DECORATION_DATE, DECORATIONS_CODE ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		$MAX_ID = 1;
		while($data = $db_att->get_array()){
			$PER_CARDNO = trim($data[ID]);
			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = '$PER_CARDNO' ";
			$db_dpis->send_cmd($cmd);
			$db_dpis->show_error();
			$data_dpis = $db_dpis->get_array();
			$PER_ID = $data_dpis[NEW_CODE] + 0;

			if ($PER_ID > 0) {
				$DC_CODE = $data[DECORATIONS_CODE];
				$DEH_DATE = trim($data[PERMISSION_DATE]);
				if (!$DEH_DATE) $DEH_DATE = "-";
				$DEH_GAZETTE = trim($data[ISSUE])." เล่ม ".trim($data[BOOK])." ตอนที่ ".trim($data[PART])." หน้า ".trim($data[PAGE])." ลำดับ ".trim($data[ORDER_DECOR]);
				$DEH_RECEIVE_DATE = trim($data[RECEIVED_DATE]);
				$DEH_RETURN_DATE = trim($data[RETURN_DATE]);
				$DEH_POSITION = trim($data[WORK_LINE_NAME]);
				$DEH_ORG = trim($data[JOB_NAME])." ".trim($data[SECTION_NAME])." ".trim($data[DIVISION_NAME])." ".trim($data[DEPARTMENT_NAME]);

				$UPDATE_USER = 99999;
				if ($data[USER_UPDATE]) $USER_UPDATE = trim($data[USER_UPDATE]);
				else $USER_UPDATE = trim($data[USER_CREATE]);
				if ($data[UPDATE_DATE]) $UPDATE_DATE = trim($data[UPDATE_DATE]);
				else  $UPDATE_DATE = trim($data[CREATE_DATE]);
				if ($USER_UPDATE) {
					$cmd = " select ID from USER_DETAIL where USERNAME = '$USER_UPDATE' ";
					$db_dpis2->send_cmd($cmd);
	//				$db_dpis2->show_error();
					$data2 = $db_dpis2->get_array();
					if ($data2[ID]) $UPDATE_USER = $data2[ID];
				}
				if (!$UPDATE_DATE) $UPDATE_DATE = date("Y-m-d H:i:s");

				$cmd = " INSERT INTO PER_DECORATEHIS (DEH_ID, PER_ID, DC_CODE, DEH_DATE, UPDATE_USER, UPDATE_DATE, 
								PER_CARDNO, DEH_GAZETTE, DEH_RECEIVE_FLAG, DEH_RETURN_FLAG, DEH_RETURN_DATE, DEH_RETURN_TYPE, 
								DEH_RECEIVE_DATE, DEH_BOOK_NO, DEH_BOOK_DATE, DEH_REMARK, DEH_POSITION, DEH_ORG)
								VALUES ($MAX_ID, $PER_ID, '$DC_CODE', '$DEH_DATE', $UPDATE_USER, '$UPDATE_DATE', '$PER_CARDNO', 
								'$DEH_GAZETTE', NULL, NULL, '$DEH_RETURN_DATE', NULL, '$DEH_RECEIVE_DATE', NULL, NULL ,NULL, '$DEH_POSITION', '$DEH_ORG') ";
				$db_dpis->send_cmd($cmd);

				$cmd1 = " SELECT DEH_ID FROM PER_DECORATEHIS WHERE DEH_ID = $MAX_ID "; 
				$count_data = $db_dpis1->send_cmd($cmd1);
				if (!$count_data) {
					echo "$cmd<br>==================<br>";
					$db_dpis->show_error();
					echo "<br>end ". ++$i  ."=======================<br>";
				}
				$MAX_ID++;
				$PER_DECORATEHIS = $MAX_ID;    
			}
		} // end while						

		$PER_DECORATEHIS--;
		$cmd = " select count(PER_ID) as COUNT_NEW from PER_DECORATEHIS ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_DECORATEHIS - $PER_DECORATEHIS - $COUNT_NEW<br>";

// เครื่องราชย์ลูกจ้างประจำ 10259
		$cmd = " SELECT ID, DECORATIONS_CODE, DECORATIONS_ABB, DECORATIONS_NAME, JOB_CODE, JOB_NAME, 
						  SECTION_CODE, SECTION_NAME, DIVISION_CODE, DIVISION_NAME, DEPARTMENT_CODE, DEPARTMENT_NAME, 
						  POS_NUM_NAME, POS_NUM_CODE, CLUSTER_CODE, CATEGORY_SAL_CODE, CATEGORY_SAL_NAME, 
						  WORK_LINE_CODE, WORK_LINE_NAME, to_char(PERMISSION_DATE,'yyyy-mm-dd') as PERMISSION_DATE, 
						  to_char(RECEIVED_DATE,'yyyy-mm-dd') as RECEIVED_DATE, to_char(RETURN_DATE,'yyyy-mm-dd') as RETURN_DATE, 
						  FLAG_WAYBILL, ISSUE, BOOK, PART, PAGE, ORDER_DECOR, to_char(DECORATION_DATE,'yyyy-mm-dd') as DECORATION_DATE, 
						  FISCAL_YEAR, RECEIVE_AS, HONOR_STATUS, REC_STATUS, USER_CREATE, to_char(CREATE_DATE,'yyyy-mm-dd') as CREATE_DATE, 
						  USER_UPDATE, to_char(UPDATE_DATE,'yyyy-mm-dd') as UPDATE_DATE 
						  FROM HR_DECORATIONS_EMP
						  ORDER BY ID, DECORATION_DATE, DECORATIONS_CODE ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		while($data = $db_att->get_array()){
			$PER_CARDNO = trim($data[ID]);
			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = '$PER_CARDNO' ";
			$db_dpis->send_cmd($cmd);
			$db_dpis->show_error();
			$data_dpis = $db_dpis->get_array();
			$PER_ID = $data_dpis[NEW_CODE] + 0;

			if ($PER_ID > 0) {
				$DC_CODE = $data[DECORATIONS_CODE];
				$DEH_DATE = trim($data[PERMISSION_DATE]);
				$DEH_ISSUE = trim($data[ISSUE]);
				$DEH_BOOK = trim($data[BOOK]);
				$DEH_PART = trim($data[PART]);
				$DEH_PAGE = trim($data[PAGE]);
				$DEH_ORDER_DECOR = trim($data[ORDER_DECOR]);
				$DEH_GAZETTE = trim($data[ISSUE])." เล่ม ".trim($data[BOOK])." ตอนที่ ".trim($data[PART])." หน้า ".trim($data[PAGE])." ลำดับ ".trim($data[ORDER_DECOR]);
				if ($DEH_ISSUE=="พิเศษ") $DEH_ISSUE = "2";
				else $DEH_ISSUE = "1";
				$DEH_RECEIVE_DATE = trim($data[RECEIVED_DATE]);
				$DEH_RETURN_DATE = trim($data[RETURN_DATE]);
				$DEH_POSITION = trim($data[WORK_LINE_NAME]);
				$DEH_ORG = trim($data[JOB_NAME])." ".trim($data[SECTION_NAME])." ".trim($data[DIVISION_NAME])." ".trim($data[DEPARTMENT_NAME]);
				if (!$DEH_DATE) 
					if ($DEH_RECEIVE_DATE) $DEH_DATE = $DEH_RECEIVE_DATE;
					else $DEH_DATE = "-";

				$UPDATE_USER = 99999;
				if ($data[USER_UPDATE]) $USER_UPDATE = trim($data[USER_UPDATE]);
				else $USER_UPDATE = trim($data[USER_CREATE]);
				if ($data[UPDATE_DATE]) $UPDATE_DATE = trim($data[UPDATE_DATE]);
				else  $UPDATE_DATE = trim($data[CREATE_DATE]);
				if ($USER_UPDATE) {
					$cmd = " select ID from USER_DETAIL where USERNAME = '$USER_UPDATE' ";
					$db_dpis2->send_cmd($cmd);
	//				$db_dpis2->show_error();
					$data2 = $db_dpis2->get_array();
					if ($data2[ID]) $UPDATE_USER = $data2[ID];
				}
				if (!$UPDATE_DATE) $UPDATE_DATE = date("Y-m-d H:i:s");

				$cmd = " INSERT INTO PER_DECORATEHIS (DEH_ID, PER_ID, DC_CODE, DEH_DATE, UPDATE_USER, UPDATE_DATE, 
								PER_CARDNO, DEH_GAZETTE, DEH_RECEIVE_FLAG, DEH_RETURN_FLAG, DEH_RETURN_DATE, DEH_RETURN_TYPE, 
								DEH_RECEIVE_DATE, DEH_BOOK_NO, DEH_BOOK_DATE, DEH_REMARK, DEH_POSITION, DEH_ORG, DEH_ISSUE, 
								DEH_BOOK, DEH_PART, DEH_PAGE, DEH_ORDER_DECOR)
								VALUES ($MAX_ID, $PER_ID, '$DC_CODE', '$DEH_DATE', $UPDATE_USER, '$UPDATE_DATE', '$PER_CARDNO', 
								'$DEH_GAZETTE', NULL, NULL, '$DEH_RETURN_DATE', NULL, '$DEH_RECEIVE_DATE', NULL, NULL ,NULL, '$DEH_POSITION', 
								'$DEH_ORG', '$DEH_ISSUE', '$DEH_BOOK', '$DEH_PART', '$DEH_PAGE', '$DEH_ORDER_DECOR') ";
				$db_dpis->send_cmd($cmd);

				$cmd1 = " SELECT DEH_ID FROM PER_DECORATEHIS WHERE DEH_ID = $MAX_ID "; 
				$count_data = $db_dpis1->send_cmd($cmd1);
				if (!$count_data) {
					echo "$cmd<br>==================<br>";
					$db_dpis->show_error();
					echo "<br>end ". ++$i  ."=======================<br>";
				}
				$MAX_ID++;
				$PER_DECORATEHIS = $MAX_ID;    
			}
		} // end while						

		$PER_DECORATEHIS--;
		$cmd = " select count(PER_ID) as COUNT_NEW from PER_DECORATEHIS ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_DECORATEHIS - $PER_DECORATEHIS - $COUNT_NEW<br>";

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_att->free_result();
		$db_att1->free_result();
	} // end if

	if( $command=='EDUCATE' ){
// ประวัติการศึกษา 158520
		$cmd = " truncate table per_educate ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

// ประวัติการศึกษาข้าราชการ 118850
		$cmd = " SELECT ID, EDUCATION_SEQ, EDUCATION_CODE, MAJOR_CODE, MINOR_CODE, COUNTRY_CODE, UNIVER_CODE, 
						EDUCATION_YEAR, GRADE_AVERAGE, START_EDUCATION_YEAR, COUNTRY_NAME, INSTITUE, START_YM, GRADUATE_YM, 
						FLAG_EDUCATION, FUND_COURSE_CODE, USER_CREATE, to_char(CREATE_DATE,'yyyy-mm-dd') as CREATE_DATE, 
						USER_UPDATE, to_char(UPDATE_DATE,'yyyy-mm-dd') as UPDATE_DATE 
						FROM HR_EDUCATION
						ORDER BY ID, EDUCATION_SEQ ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		$MAX_ID = 1;
		while($data = $db_att->get_array()){
			$PER_CARDNO = trim($data[ID]);
			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = '$PER_CARDNO' ";
			$db_dpis->send_cmd($cmd);
			$db_dpis->show_error();
			$data_dpis = $db_dpis->get_array();
			$PER_ID = $data_dpis[NEW_CODE] + 0;

			if ($PER_ID > 0) {
				$EDU_SEQ = $data[EDUCATION_SEQ];
				$EN_CODE = trim($data[EDUCATION_CODE]);
				$EM_CODE = trim($data[MAJOR_CODE]);
				$CT_CODE_EDU = trim($data[COUNTRY_CODE]);
				$INS_CODE = trim($data[UNIVER_CODE]);
				if ($INS_CODE=="1015" || $INS_CODE=="1016") $INS_CODE = "";
				$EDU_ENDYEAR = trim($data[EDUCATION_YEAR]);
				$EDU_GRADE = trim($data[GRADE_AVERAGE]);
				$EDU_STARTYEAR = trim($data[START_EDUCATION_YEAR]);
				$EDU_INSTITUTE = trim($data[INSTITUE]);
				$FLAG_EDUCATION = trim($data[FLAG_EDUCATION]);
				$EL_CODE = trim($data[FUND_COURSE_CODE]);

				$UPDATE_USER = 99999;
				if ($data[USER_UPDATE]) $USER_UPDATE = trim($data[USER_UPDATE]);
				else $USER_UPDATE = trim($data[USER_CREATE]);
				if ($data[UPDATE_DATE]) $UPDATE_DATE = trim($data[UPDATE_DATE]);
				else  $UPDATE_DATE = trim($data[CREATE_DATE]);
				if ($USER_UPDATE) {
					$cmd = " select ID from USER_DETAIL where USERNAME = '$USER_UPDATE' ";
					$db_dpis2->send_cmd($cmd);
	//				$db_dpis2->show_error();
					$data2 = $db_dpis2->get_array();
					if ($data2[ID]) $UPDATE_USER = $data2[ID];
				}
				if (!$UPDATE_DATE) $UPDATE_DATE = date("Y-m-d H:i:s");

				if (($INS_CODE || $EDU_INSTITUTE) && !$CT_CODE_EDU) $CT_CODE_EDU = "140";
				$EDU_TYPE = "";
				if ($FLAG_EDUCATION == "3") $EDU_TYPE .= "||4||";
				if ($FLAG_EDUCATION == "2") $EDU_TYPE .= "||3||";
				if ($FLAG_EDUCATION == "1") $EDU_TYPE .= "||1||";
				if (!$EDU_TYPE) $EDU_TYPE = "||3||";

				if (!$EDU_ENDYEAR) $EDU_ENDYEAR = '-';
				if (!$EDU_STARTYEAR) $EDU_STARTYEAR = '-';
				if (!$EDU_GRADE) $EDU_GRADE = "NULL";
				$EN_CODE = (trim($EN_CODE) && $EN_CODE != "0")? "'" . $EN_CODE . "'"  : "NULL";
				$EM_CODE = (trim($EM_CODE) && $EM_CODE != "0")? "'" . $EM_CODE . "'"  : "NULL";
				$INS_CODE = (trim($INS_CODE) && $INS_CODE != "0")? "'" . $INS_CODE . "'"  : "NULL";	
				$EDU_INSTITUTE = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($EDU_INSTITUTE)));

				$cmd = " INSERT INTO PER_EDUCATE(EDU_ID, PER_ID, EDU_SEQ, EDU_STARTYEAR, EDU_ENDYEAR, ST_CODE, CT_CODE, 
								EDU_FUND, EN_CODE, EM_CODE, INS_CODE, EDU_TYPE, UPDATE_USER, UPDATE_DATE, PER_CARDNO, EL_CODE, EDU_ENDDATE,
								EDU_GRADE, EDU_HONOR, EDU_BOOK_NO, EDU_BOOK_DATE, EDU_REMARK, EDU_INSTITUTE, CT_CODE_EDU)
								VALUES ($MAX_ID, $PER_ID, $EDU_SEQ, '$EDU_STARTYEAR', '$EDU_ENDYEAR', NULL, '$CT_CODE_EDU', NULL, $EN_CODE, $EM_CODE, 
								$INS_CODE, '$EDU_TYPE', $UPDATE_USER, '$UPDATE_DATE', '$PER_CARDNO', '$EL_CODE', '$EDU_ENDDATE',
								$EDU_GRADE, NULL, NULL, NULL, NULL, '$EDU_INSTITUTE', '$CT_CODE_EDU') ";
				$db_dpis->send_cmd($cmd);

				$cmd1 = " SELECT EDU_ID FROM PER_EDUCATE WHERE EDU_ID = $MAX_ID "; 
				$count_data = $db_dpis1->send_cmd($cmd1);
				if (!$count_data) {
					echo "$cmd<br>==================<br>";
					$db_dpis->show_error();
					echo "<br>end ". ++$i  ."=======================<br>";
				}
				$MAX_ID++;
				$PER_EDUCATE = $MAX_ID;    
			}
		} // end while						

		$PER_EDUCATE--;
		$cmd = " select count(PER_ID) as COUNT_NEW from PER_EDUCATE ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_EDUCATE - $PER_EDUCATE - $COUNT_NEW<br>";

// ประวัติการศึกษาลูกจ้างประจำ 37598
		$cmd = " SELECT ID, EDUCATION_SEQ, EDUCATION_CODE, MAJOR_CODE, MINOR_CODE, COUNTRY_CODE, UNIVER_CODE, 
						EDUCATION_YEAR, START_EDUCATION_YEAR, COUNTRY_NAME, INSTITUE, START_YM, GRADUATE_YM, 
						FLAG_EDUCATION, FUND_COURSE_CODE, USER_CREATE, to_char(CREATE_DATE,'yyyy-mm-dd') as CREATE_DATE, 
						USER_UPDATE, to_char(UPDATE_DATE,'yyyy-mm-dd') as UPDATE_DATE 
						FROM HR_EDUCATION_EMP
						ORDER BY ID, EDUCATION_SEQ ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		while($data = $db_att->get_array()){
			$PER_CARDNO = trim($data[ID]);
			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = '$PER_CARDNO' ";
			$db_dpis->send_cmd($cmd);
			$db_dpis->show_error();
			$data_dpis = $db_dpis->get_array();
			$PER_ID = $data_dpis[NEW_CODE] + 0;

			if ($PER_ID > 0) {
				$EDU_SEQ = $data[EDUCATION_SEQ];
				$EN_CODE = trim($data[EDUCATION_CODE]);
				$EM_CODE = trim($data[MAJOR_CODE]);
				$CT_CODE_EDU = trim($data[COUNTRY_CODE]);
				$INS_CODE = trim($data[UNIVER_CODE]);
				if ($INS_CODE=="1015" || $INS_CODE=="1016") $INS_CODE = "";
				$EDU_ENDYEAR = trim($data[EDUCATION_YEAR]);
				$EDU_STARTYEAR = trim($data[START_EDUCATION_YEAR]);
				$EDU_INSTITUTE = trim($data[INSTITUE]);
				$FLAG_EDUCATION = trim($data[FLAG_EDUCATION]);
				$EL_CODE = trim($data[FUND_COURSE_CODE]);

				$UPDATE_USER = 99999;
				if ($data[USER_UPDATE]) $USER_UPDATE = trim($data[USER_UPDATE]);
				else $USER_UPDATE = trim($data[USER_CREATE]);
				if ($data[UPDATE_DATE]) $UPDATE_DATE = trim($data[UPDATE_DATE]);
				else  $UPDATE_DATE = trim($data[CREATE_DATE]);
				if ($USER_UPDATE) {
					$cmd = " select ID from USER_DETAIL where USERNAME = '$USER_UPDATE' ";
					$db_dpis2->send_cmd($cmd);
	//				$db_dpis2->show_error();
					$data2 = $db_dpis2->get_array();
					if ($data2[ID]) $UPDATE_USER = $data2[ID];
				}
				if (!$UPDATE_DATE) $UPDATE_DATE = date("Y-m-d H:i:s");

				if (($INS_CODE || $EDU_INSTITUTE) && !$CT_CODE_EDU) $CT_CODE_EDU = "140";
				$EDU_TYPE = "";
				if ($FLAG_EDUCATION == "3") $EDU_TYPE .= "||4||";
				if ($FLAG_EDUCATION == "2") $EDU_TYPE .= "||3||";
				if ($FLAG_EDUCATION == "1") $EDU_TYPE .= "||1||";
				if (!$EDU_TYPE) $EDU_TYPE = "||3||";

				if (!$EDU_ENDYEAR) $EDU_ENDYEAR = '-';
				if (!$EDU_STARTYEAR) $EDU_STARTYEAR = '-';
				if (!$EDU_GRADE) $EDU_GRADE = "NULL";
				$EN_CODE = (trim($EN_CODE) && $EN_CODE != "0")? "'" . $EN_CODE . "'"  : "NULL";
				$EM_CODE = (trim($EM_CODE) && $EM_CODE != "0")? "'" . $EM_CODE . "'"  : "NULL";
				$INS_CODE = (trim($INS_CODE) && $INS_CODE != "0")? "'" . $INS_CODE . "'"  : "NULL";	
				$EDU_INSTITUTE = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($EDU_INSTITUTE)));

				$cmd = " INSERT INTO PER_EDUCATE(EDU_ID, PER_ID, EDU_SEQ, EDU_STARTYEAR, EDU_ENDYEAR, ST_CODE, CT_CODE, 
								EDU_FUND, EN_CODE, EM_CODE, INS_CODE, EDU_TYPE, UPDATE_USER, UPDATE_DATE, PER_CARDNO, EL_CODE, EDU_ENDDATE,
								EDU_GRADE, EDU_HONOR, EDU_BOOK_NO, EDU_BOOK_DATE, EDU_REMARK, EDU_INSTITUTE, CT_CODE_EDU)
								VALUES ($MAX_ID, $PER_ID, $EDU_SEQ, '$EDU_STARTYEAR', '$EDU_ENDYEAR', NULL, '$CT_CODE_EDU', NULL, $EN_CODE, $EM_CODE, 
								$INS_CODE, '$EDU_TYPE', $UPDATE_USER, '$UPDATE_DATE', '$PER_CARDNO', '$EL_CODE', '$EDU_ENDDATE',
								NULL, NULL, NULL, NULL, NULL, '$EDU_INSTITUTE', '$CT_CODE_EDU') ";
				$db_dpis->send_cmd($cmd);

				$cmd1 = " SELECT EDU_ID FROM PER_EDUCATE WHERE EDU_ID = $MAX_ID "; 
				$count_data = $db_dpis1->send_cmd($cmd1);
				if (!$count_data) {
					echo "$cmd<br>==================<br>";
					$db_dpis->show_error();
					echo "<br>end ". ++$i  ."=======================<br>";
				}
				$MAX_ID++;
				$PER_EDUCATE = $MAX_ID;    
			}
		} // end while						

		$PER_EDUCATE--;
		$cmd = " select count(PER_ID) as COUNT_NEW from PER_EDUCATE ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_EDUCATE - $PER_EDUCATE - $COUNT_NEW<br>";

// ประวัติการศึกษาลูกจ้างชั่วคราว 2202
		$cmd = " SELECT ID, EDUCATION_SEQ, EDUCATION_CODE, MAJOR_CODE, MINOR_CODE, COUNTRY_CODE, UNIVER_CODE, 
						EDUCATION_YEAR, START_EDUCATION_YEAR, COUNTRY_NAME, INSTITUE, START_YM, GRADUATE_YM, 
						FLAG_EDUCATION, FUND_COURSE_CODE, USER_CREATE, to_char(CREATE_DATE,'yyyy-mm-dd') as CREATE_DATE, 
						USER_UPDATE, to_char(UPDATE_DATE,'yyyy-mm-dd') as UPDATE_DATE 
						FROM HR_EDUCATION_EMPTEMP
						ORDER BY ID, EDUCATION_SEQ ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		while($data = $db_att->get_array()){
			$PER_CARDNO = trim($data[ID]);
			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = '$PER_CARDNO' ";
			$db_dpis->send_cmd($cmd);
			$db_dpis->show_error();
			$data_dpis = $db_dpis->get_array();
			$PER_ID = $data_dpis[NEW_CODE] + 0;

			if ($PER_ID > 0) {
				$EDU_SEQ = $data[EDUCATION_SEQ];
				$EN_CODE = trim($data[EDUCATION_CODE]);
				$EM_CODE = trim($data[MAJOR_CODE]);
				$CT_CODE_EDU = trim($data[COUNTRY_CODE]);
				$INS_CODE = trim($data[UNIVER_CODE]);
				if ($INS_CODE=="1015" || $INS_CODE=="1016") $INS_CODE = "";
				$EDU_ENDYEAR = trim($data[EDUCATION_YEAR]);
				$EDU_STARTYEAR = trim($data[START_EDUCATION_YEAR]);
				$EDU_INSTITUTE = trim($data[INSTITUE]);
				$FLAG_EDUCATION = trim($data[FLAG_EDUCATION]);
				$EL_CODE = trim($data[FUND_COURSE_CODE]);

				$UPDATE_USER = 99999;
				if ($data[USER_UPDATE]) $USER_UPDATE = trim($data[USER_UPDATE]);
				else $USER_UPDATE = trim($data[USER_CREATE]);
				if ($data[UPDATE_DATE]) $UPDATE_DATE = trim($data[UPDATE_DATE]);
				else  $UPDATE_DATE = trim($data[CREATE_DATE]);
				if ($USER_UPDATE) {
					$cmd = " select ID from USER_DETAIL where USERNAME = '$USER_UPDATE' ";
					$db_dpis2->send_cmd($cmd);
	//				$db_dpis2->show_error();
					$data2 = $db_dpis2->get_array();
					if ($data2[ID]) $UPDATE_USER = $data2[ID];
				}
				if (!$UPDATE_DATE) $UPDATE_DATE = date("Y-m-d H:i:s");

				if (($INS_CODE || $EDU_INSTITUTE) && !$CT_CODE_EDU) $CT_CODE_EDU = "140";
				$EDU_TYPE = "";
				if ($FLAG_EDUCATION == "3") $EDU_TYPE .= "||4||";
				if ($FLAG_EDUCATION == "2") $EDU_TYPE .= "||3||";
				if ($FLAG_EDUCATION == "1") $EDU_TYPE .= "||1||";
				if (!$EDU_TYPE) $EDU_TYPE = "||3||";

				if (!$EDU_ENDYEAR) $EDU_ENDYEAR = '-';
				if (!$EDU_STARTYEAR) $EDU_STARTYEAR = '-';
				if (!$EDU_GRADE) $EDU_GRADE = "NULL";
				$EN_CODE = (trim($EN_CODE) && $EN_CODE != "0")? "'" . $EN_CODE . "'"  : "NULL";
				$EM_CODE = (trim($EM_CODE) && $EM_CODE != "0")? "'" . $EM_CODE . "'"  : "NULL";
				$INS_CODE = (trim($INS_CODE) && $INS_CODE != "0")? "'" . $INS_CODE . "'"  : "NULL";	
				$EDU_INSTITUTE = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($EDU_INSTITUTE)));

				$cmd = " INSERT INTO PER_EDUCATE(EDU_ID, PER_ID, EDU_SEQ, EDU_STARTYEAR, EDU_ENDYEAR, ST_CODE, CT_CODE, 
								EDU_FUND, EN_CODE, EM_CODE, INS_CODE, EDU_TYPE, UPDATE_USER, UPDATE_DATE, PER_CARDNO, EL_CODE, EDU_ENDDATE,
								EDU_GRADE, EDU_HONOR, EDU_BOOK_NO, EDU_BOOK_DATE, EDU_REMARK, EDU_INSTITUTE, CT_CODE_EDU)
								VALUES ($MAX_ID, $PER_ID, $EDU_SEQ, '$EDU_STARTYEAR', '$EDU_ENDYEAR', NULL, '$CT_CODE_EDU', NULL, $EN_CODE, $EM_CODE, 
								$INS_CODE, '$EDU_TYPE', $UPDATE_USER, '$UPDATE_DATE', '$PER_CARDNO', '$EL_CODE', '$EDU_ENDDATE',
								NULL, NULL, NULL, NULL, NULL, '$EDU_INSTITUTE', '$CT_CODE_EDU') ";
				$db_dpis->send_cmd($cmd);

				$cmd1 = " SELECT EDU_ID FROM PER_EDUCATE WHERE EDU_ID = $MAX_ID "; 
				$count_data = $db_dpis1->send_cmd($cmd1);
				if (!$count_data) {
					echo "$cmd<br>==================<br>";
					$db_dpis->show_error();
					echo "<br>end ". ++$i  ."=======================<br>";
				}
				$MAX_ID++;
				$PER_EDUCATE = $MAX_ID;    
			}
		} // end while						

		$PER_EDUCATE--;
		$cmd = " select count(PER_ID) as COUNT_NEW from PER_EDUCATE ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_EDUCATE - $PER_EDUCATE - $COUNT_NEW<br>";

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_att->free_result();
		$db_att1->free_result();
	} // end if

	if( $command=='REWARD' ){
// ความดีความชอบ 1576 ok
		$cmd = " truncate table per_rewardhis ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

// ความดีความชอบข้าราชการ 223 
		$cmd = " SELECT ID, MP_YEAR, GOOD_WORK_CODE, GOOD_WORK_NAME, JOB_CODE, JOB_NAME, SECTION_CODE, 
						SECTION_NAME, DIVISION_CODE, DIVISION_NAME, DEPARTMENT_CODE, DEPARTMENT_NAME, 
						POS_NUM_NAME, POS_NUM_CODE, WORK_LINE_NAME, MP_CEE, SALARY_POS_ABB_NAME, SPECIALIST_CODE, 
						to_char(RECEIVED_DATE,'yyyy-mm-dd') as RECEIVED_DATE, APPROVE_NUM, to_char(APPROVE_DATE,'yyyy-mm-dd') as APPROVE_DATE, 
						to_char(BORN,'yyyy-mm-dd') as BORN, to_char(RET_BORN_YEAR,'yyyy-mm-dd') as RET_BORN_YEAR, 
						to_char(BEGIN_ENTRY_DATE,'yyyy-mm-dd') as BEGIN_ENTRY_DATE, SALARY_POS_CODE, ADMIN_NAME, 
						WORK_LINE_CODE, ADMIN_CODE, USER_CREATE, to_char(CREATE_DATE,'yyyy-mm-dd') as CREATE_DATE, 
						USER_UPDATE, to_char(UPDATE_DATE,'yyyy-mm-dd') as UPDATE_DATE
						FROM HR_GOOD_WORK_OFFICER
						ORDER BY ID, MP_YEAR ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		$MAX_ID = 1;
		while($data = $db_att->get_array()){
			$PER_CARDNO = trim($data[ID]);
			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = '$PER_CARDNO' ";
			$db_dpis->send_cmd($cmd);
			$db_dpis->show_error();
			$data_dpis = $db_dpis->get_array();
			$PER_ID = $data_dpis[NEW_CODE] + 0;

			if ($PER_ID > 0) {
				$REH_DATE = trim($data[RECEIVED_DATE]);
				$REW_CODE = trim($data[GOOD_WORK_CODE]);
				$REH_REMARK = trim($data[APPROVE_NUM]);
				$REH_YEAR = trim($data[MP_YEAR]);

				$UPDATE_USER = 99999;
				if ($data[USER_UPDATE]) $USER_UPDATE = trim($data[USER_UPDATE]);
				else $USER_UPDATE = trim($data[USER_CREATE]);
				if ($data[UPDATE_DATE]) $UPDATE_DATE = trim($data[UPDATE_DATE]);
				else  $UPDATE_DATE = trim($data[CREATE_DATE]);
				if ($USER_UPDATE) {
					$cmd = " select ID from USER_DETAIL where USERNAME = '$USER_UPDATE' ";
					$db_dpis2->send_cmd($cmd);
	//				$db_dpis2->show_error();
					$data2 = $db_dpis2->get_array();
					if ($data2[ID]) $UPDATE_USER = $data2[ID];
				}
				if (!$UPDATE_DATE) $UPDATE_DATE = date("Y-m-d H:i:s");

				$cmd = " INSERT INTO PER_REWARDHIS(REH_ID, PER_ID, REW_CODE, REH_ORG, REH_DOCNO, REH_DATE, UPDATE_USER, 
								UPDATE_DATE, PER_CARDNO, REH_YEAR, REH_PERFORMANCE, REH_OTHER_PERFORMANCE, REH_REMARK)
								VALUES ($MAX_ID, $PER_ID, '$REW_CODE', '$REH_ORG', NULL, '$REH_DATE', $UPDATE_USER, '$UPDATE_DATE', 
								'$PER_CARDNO', '$REH_YEAR', NULL, NULL, '$REH_REMARK') ";
				$db_dpis->send_cmd($cmd);

				$cmd1 = " SELECT REH_ID FROM PER_REWARDHIS WHERE REH_ID = $MAX_ID "; 
				$count_data = $db_dpis1->send_cmd($cmd1);
				if (!$count_data) {
					echo "$cmd<br>==================<br>";
					$db_dpis->show_error();
					echo "<br>end ". ++$i  ."=======================<br>";
				}
				$MAX_ID++;
				$PER_REWARDHIS = $MAX_ID;    
			}
		} // end while						

		$PER_REWARDHIS--;
		$cmd = " select count(PER_ID) as COUNT_NEW from PER_REWARDHIS ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_REWARDHIS - $PER_REWARDHIS - $COUNT_NEW<br>";

// ความดีความชอบลูกจ้างประจำ 1365 
		$cmd = " SELECT ID, GOOD_WORK_CODE, GOOD_WORK_NAME, JOB_CODE, JOB_NAME, SECTION_CODE, SECTION_NAME, 
						DIVISION_CODE, DIVISION_NAME, DEPARTMENT_CODE, DEPARTMENT_NAME, POS_NUM_NAME, POS_NUM_CODE, 
						CLUSTER_CODE, CATEGORY_SAL_CODE, CATEGORY_SAL_NAME, WORK_LINE_CODE, WORK_LINE_NAME, 
						to_char(RECEIVED_DATE,'yyyy-mm-dd') as RECEIVED_DATE, APPROVE_NUM, to_char(APPROVE_DATE,'yyyy-mm-dd') as APPROVE_DATE, 
						to_char(BORN,'yyyy-mm-dd') as BORN, to_char(RET_BORN_YEAR,'yyyy-mm-dd') as RET_BORN_YEAR, 
						to_char(BEGIN_ENTRY_DATE,'yyyy-mm-dd') as BEGIN_ENTRY_DATE, RECEIVED_FLAG, MP_YEAR, 
						USER_CREATE, to_char(CREATE_DATE,'yyyy-mm-dd') as CREATE_DATE, 
						USER_UPDATE, to_char(UPDATE_DATE,'yyyy-mm-dd') as UPDATE_DATE
						FROM HR_GOOD_WORK_EMP
						ORDER BY ID, MP_YEAR ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		while($data = $db_att->get_array()){
			$PER_CARDNO = trim($data[ID]);
			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = '$PER_CARDNO' ";
			$db_dpis->send_cmd($cmd);
			$db_dpis->show_error();
			$data_dpis = $db_dpis->get_array();
			$PER_ID = $data_dpis[NEW_CODE] + 0;

			if ($PER_ID > 0) {
				$REH_DATE = trim($data[RECEIVED_DATE]);
				$REW_CODE = trim($data[GOOD_WORK_CODE]);
				$REH_ORG = trim($data[APPROVE_NUM]);
				$REH_YEAR = trim($data[YEAR]);

				$UPDATE_USER = 99999;
				if ($data[USER_UPDATE]) $USER_UPDATE = trim($data[USER_UPDATE]);
				else $USER_UPDATE = trim($data[USER_CREATE]);
				if ($data[UPDATE_DATE]) $UPDATE_DATE = trim($data[UPDATE_DATE]);
				else  $UPDATE_DATE = trim($data[CREATE_DATE]);
				if ($USER_UPDATE) {
					$cmd = " select ID from USER_DETAIL where USERNAME = '$USER_UPDATE' ";
					$db_dpis2->send_cmd($cmd);
	//				$db_dpis2->show_error();
					$data2 = $db_dpis2->get_array();
					if ($data2[ID]) $UPDATE_USER = $data2[ID];
				}
				if (!$UPDATE_DATE) $UPDATE_DATE = date("Y-m-d H:i:s");

				$cmd = " INSERT INTO PER_REWARDHIS(REH_ID, PER_ID, REW_CODE, REH_ORG, REH_DOCNO, REH_DATE, UPDATE_USER, 
								UPDATE_DATE, PER_CARDNO, REH_YEAR, REH_PERFORMANCE, REH_OTHER_PERFORMANCE, REH_REMARK)
								VALUES ($MAX_ID, $PER_ID, '$REW_CODE', '$REH_ORG', NULL, '$REH_DATE', $UPDATE_USER, '$UPDATE_DATE', 
								'$PER_CARDNO', '$REH_YEAR', NULL, NULL, NULL) ";
				$db_dpis->send_cmd($cmd);

				$cmd1 = " SELECT REH_ID FROM PER_REWARDHIS WHERE REH_ID = $MAX_ID "; 
				$count_data = $db_dpis1->send_cmd($cmd1);
				if (!$count_data) {
					echo "$cmd<br>==================<br>";
					$db_dpis->show_error();
					echo "<br>end ". ++$i  ."=======================<br>";
				}
				$MAX_ID++;
				$PER_REWARDHIS = $MAX_ID;    
			}
		} // end while						

		$PER_REWARDHIS--;
		$cmd = " select count(PER_ID) as COUNT_NEW from PER_REWARDHIS ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_REWARDHIS - $PER_REWARDHIS - $COUNT_NEW<br>";

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_att->free_result();
		$db_att1->free_result();
	} // end if

	if( $command=='ABSENT' ){
// การลา 53515 ok
		$cmd = " truncate table per_absenthis ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

// การลาข้าราชการ 23713
		$cmd = " SELECT ID, BUD_YEAR, MONTH_CODE, ORDER_LEAVE, LEAVE_CODE, to_char(LEAVE_FROM,'yyyy-mm-dd') as LEAVE_FROM, 
						  to_char(LEAVE_TO,'yyyy-mm-dd') as LEAVE_TO, DAY_TOTAL, REPRESENTATIVE, LEAVE_MEMO, CUR_YEAR, USER_CREATE, 
						  to_char(CREATE_DATE,'yyyy-mm-dd') as CREATE_DATE, USER_UPDATE, to_char(UPDATE_DATE,'yyyy-mm-dd') as UPDATE_DATE 
						  FROM HR_LEAVE_OFFICER
						  ORDER BY ID, LEAVE_FROM ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		$MAX_ID = 1;
		while($data = $db_att->get_array()){
			$PER_CARDNO = trim($data[ID]);
			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = '$PER_CARDNO' ";
			$db_dpis->send_cmd($cmd);
			$db_dpis->show_error();
			$data_dpis = $db_dpis->get_array();
			$PER_ID = $data_dpis[NEW_CODE] + 0;

			if ($PER_ID > 0) {
				$ABSENT_TYPE = $data[LEAVE_CODE];
				$ABS_STARTDATE = trim($data[LEAVE_FROM]);
				$ABS_ENDDATE = trim($data[LEAVE_TO]);
				$ABS_DAY = trim($data[DAY_TOTAL]);
				$ABS_REMARK = trim($data[LEAVE_MEMO])." ".trim($data[REPRESENTATIVE]);
				if (!$ABS_DAY) $ABS_DAY = 0;
				if (!$ABS_STARTDATE) $ABS_STARTDATE = '-';
				if (!$ABS_ENDDATE) $ABS_ENDDATE = '-';

				$UPDATE_USER = 99999;
				if ($data[USER_UPDATE]) $USER_UPDATE = trim($data[USER_UPDATE]);
				else $USER_UPDATE = trim($data[USER_CREATE]);
				if ($data[UPDATE_DATE]) $UPDATE_DATE = trim($data[UPDATE_DATE]);
				else  $UPDATE_DATE = trim($data[CREATE_DATE]);
				if ($USER_UPDATE) {
					$cmd = " select ID from USER_DETAIL where USERNAME = '$USER_UPDATE' ";
					$db_dpis2->send_cmd($cmd);
	//				$db_dpis2->show_error();
					$data2 = $db_dpis2->get_array();
					if ($data2[ID]) $UPDATE_USER = $data2[ID];
				}
				if (!$UPDATE_DATE) $UPDATE_DATE = date("Y-m-d H:i:s");

				$cmd = " INSERT INTO PER_ABSENTHIS(ABS_ID, PER_ID, AB_CODE, ABS_STARTDATE, ABS_STARTPERIOD, 
								ABS_ENDDATE, ABS_ENDPERIOD, ABS_DAY, UPDATE_USER, UPDATE_DATE, PER_CARDNO, ABS_REMARK)
								VALUES ($MAX_ID, $PER_ID, '$ABSENT_TYPE', '$ABS_STARTDATE', 3, '$ABS_ENDDATE', 3, $ABS_DAY, 
								$UPDATE_USER, '$UPDATE_DATE', '$PER_CARDNO', '$ABS_REMARK') ";
				$db_dpis->send_cmd($cmd);

				$cmd1 = " SELECT ABS_ID FROM PER_ABSENTHIS WHERE ABS_ID = $MAX_ID "; 
				$count_data = $db_dpis1->send_cmd($cmd1);
				if (!$count_data) {
					echo "$cmd<br>==================<br>";
					$db_dpis->show_error();
					echo "<br>end ". ++$i  ."=======================<br>";
				}
				$MAX_ID++;
				$PER_ABSENTHIS = $MAX_ID;    
			}
		} // end while						

		$PER_ABSENTHIS--;
		$cmd = " select count(PER_ID) as COUNT_NEW from PER_ABSENTHIS ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_ABSENTHIS - $PER_ABSENTHIS - $COUNT_NEW<br>";
		
// การลาลูกจ้างประจำ 31079
		$cmd = " SELECT ID, BUD_YEAR, MONTH_CODE, ORDER_LEAVE, LEAVE_CODE, to_char(LEAVE_FROM,'yyyy-mm-dd') as LEAVE_FROM, 
						  to_char(LEAVE_TO,'yyyy-mm-dd') as LEAVE_TO, DAY_TOTAL, REPRESENTATIVE, LEAVE_MEMO, CUR_YEAR, USER_CREATE, 
						  to_char(CREATE_DATE,'yyyy-mm-dd') as CREATE_DATE, USER_UPDATE, to_char(UPDATE_DATE,'yyyy-mm-dd') as UPDATE_DATE 
						  FROM HR_LEAVE_EMP
						  ORDER BY ID, LEAVE_FROM ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		while($data = $db_att->get_array()){
			$PER_CARDNO = trim($data[ID]);
			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = '$PER_CARDNO' ";
			$db_dpis->send_cmd($cmd);
			$db_dpis->show_error();
			$data_dpis = $db_dpis->get_array();
			$PER_ID = $data_dpis[NEW_CODE] + 0;

			if ($PER_ID > 0) {
				$ABSENT_TYPE = $data[LEAVE_CODE];
				$ABS_STARTDATE = trim($data[LEAVE_FROM]);
				$ABS_ENDDATE = trim($data[LEAVE_TO]);
				$ABS_DAY = trim($data[DAY_TOTAL]);
				$ABS_REMARK = trim($data[LEAVE_MEMO])." ".trim($data[REPRESENTATIVE]);
				if (!$ABS_DAY) $ABS_DAY = 0;
				if (!$ABS_STARTDATE) $ABS_STARTDATE = '-';
				if (!$ABS_ENDDATE) $ABS_ENDDATE = '-';

				$UPDATE_USER = 99999;
				if ($data[USER_UPDATE]) $USER_UPDATE = trim($data[USER_UPDATE]);
				else $USER_UPDATE = trim($data[USER_CREATE]);
				if ($data[UPDATE_DATE]) $UPDATE_DATE = trim($data[UPDATE_DATE]);
				else  $UPDATE_DATE = trim($data[CREATE_DATE]);
				if ($USER_UPDATE) {
					$cmd = " select ID from USER_DETAIL where USERNAME = '$USER_UPDATE' ";
					$db_dpis2->send_cmd($cmd);
	//				$db_dpis2->show_error();
					$data2 = $db_dpis2->get_array();
					if ($data2[ID]) $UPDATE_USER = $data2[ID];
				}
				if (!$UPDATE_DATE) $UPDATE_DATE = date("Y-m-d H:i:s");

				$cmd = " INSERT INTO PER_ABSENTHIS(ABS_ID, PER_ID, AB_CODE, ABS_STARTDATE, ABS_STARTPERIOD, 
								ABS_ENDDATE, ABS_ENDPERIOD, ABS_DAY, UPDATE_USER, UPDATE_DATE, PER_CARDNO, ABS_REMARK)
								VALUES ($MAX_ID, $PER_ID, '$ABSENT_TYPE', '$ABS_STARTDATE', 3, '$ABS_ENDDATE', 3, $ABS_DAY, 
								$UPDATE_USER, '$UPDATE_DATE', '$PER_CARDNO', '$ABS_REMARK') ";
				$db_dpis->send_cmd($cmd);

				$cmd1 = " SELECT ABS_ID FROM PER_ABSENTHIS WHERE ABS_ID = $MAX_ID "; 
				$count_data = $db_dpis1->send_cmd($cmd1);
				if (!$count_data) {
					echo "$cmd<br>==================<br>";
					$db_dpis->show_error();
					echo "<br>end ". ++$i  ."=======================<br>";
				}
				$MAX_ID++;
				$PER_ABSENTHIS = $MAX_ID;    
			}
		} // end while						

		$PER_ABSENTHIS--;
		$cmd = " select count(PER_ID) as COUNT_NEW from PER_ABSENTHIS ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_ABSENTHIS - $PER_ABSENTHIS - $COUNT_NEW<br>";
		
// การลาลูกจ้างชั่วคราว 2545
		$cmd = " SELECT ID, BUD_YEAR, MONTH_CODE, ORDER_LEAVE, LEAVE_CODE, to_char(LEAVE_FROM,'yyyy-mm-dd') as LEAVE_FROM, 
						  to_char(LEAVE_TO,'yyyy-mm-dd') as LEAVE_TO, DAY_TOTAL, REPRESENTATIVE, LEAVE_MEMO, CUR_YEAR, USER_CREATE, 
						  to_char(CREATE_DATE,'yyyy-mm-dd') as CREATE_DATE, USER_UPDATE, to_char(UPDATE_DATE,'yyyy-mm-dd') as UPDATE_DATE 
						  FROM HR_LEAVE_EMPTEMP
						  ORDER BY ID, LEAVE_FROM ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		while($data = $db_att->get_array()){
			$PER_CARDNO = trim($data[ID]);
			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = '$PER_CARDNO' ";
			$db_dpis->send_cmd($cmd);
			$db_dpis->show_error();
			$data_dpis = $db_dpis->get_array();
			$PER_ID = $data_dpis[NEW_CODE] + 0;

			if ($PER_ID > 0) {
				$ABSENT_TYPE = $data[LEAVE_CODE];
				$ABS_STARTDATE = trim($data[LEAVE_FROM]);
				$ABS_ENDDATE = trim($data[LEAVE_TO]);
				$ABS_DAY = trim($data[DAY_TOTAL]);
				$ABS_REMARK = trim($data[LEAVE_MEMO])." ".trim($data[REPRESENTATIVE]);
				if (!$ABS_DAY) $ABS_DAY = 0;
				if (!$ABS_STARTDATE) $ABS_STARTDATE = '-';
				if (!$ABS_ENDDATE) $ABS_ENDDATE = '-';

				$UPDATE_USER = 99999;
				if ($data[USER_UPDATE]) $USER_UPDATE = trim($data[USER_UPDATE]);
				else $USER_UPDATE = trim($data[USER_CREATE]);
				if ($data[UPDATE_DATE]) $UPDATE_DATE = trim($data[UPDATE_DATE]);
				else  $UPDATE_DATE = trim($data[CREATE_DATE]);
				if ($USER_UPDATE) {
					$cmd = " select ID from USER_DETAIL where USERNAME = '$USER_UPDATE' ";
					$db_dpis2->send_cmd($cmd);
	//				$db_dpis2->show_error();
					$data2 = $db_dpis2->get_array();
					if ($data2[ID]) $UPDATE_USER = $data2[ID];
				}
				if (!$UPDATE_DATE) $UPDATE_DATE = date("Y-m-d H:i:s");

				$cmd = " INSERT INTO PER_ABSENTHIS(ABS_ID, PER_ID, AB_CODE, ABS_STARTDATE, ABS_STARTPERIOD, 
								ABS_ENDDATE, ABS_ENDPERIOD, ABS_DAY, UPDATE_USER, UPDATE_DATE, PER_CARDNO, ABS_REMARK)
								VALUES ($MAX_ID, $PER_ID, '$ABSENT_TYPE', '$ABS_STARTDATE', 3, '$ABS_ENDDATE', 3, $ABS_DAY, 
								$UPDATE_USER, '$UPDATE_DATE', '$PER_CARDNO', '$ABS_REMARK') ";
				$db_dpis->send_cmd($cmd);

				$cmd1 = " SELECT ABS_ID FROM PER_ABSENTHIS WHERE ABS_ID = $MAX_ID "; 
				$count_data = $db_dpis1->send_cmd($cmd1);
				if (!$count_data) {
					echo "$cmd<br>==================<br>";
					$db_dpis->show_error();
					echo "<br>end ". ++$i  ."=======================<br>";
				}
				$MAX_ID++;
				$PER_ABSENTHIS = $MAX_ID;    
			}
		} // end while						

		$PER_ABSENTHIS--;
		$cmd = " select count(PER_ID) as COUNT_NEW from PER_ABSENTHIS ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_ABSENTHIS - $PER_ABSENTHIS - $COUNT_NEW<br>";
		
		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_att->free_result();
		$db_att1->free_result();
	} // end if

	if( $command=='ABSENTSUM' ){
// สรุปวันลา 
		$cmd = " truncate table per_absentsum ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

// สรุปวันลาข้าราชการ 3289
		$cmd = " SELECT ID, BUD_YEAR, INCREASE_LEAVE_CODE6, TOTAL_LEAVE_CODE1, TOTAL_LEAVE_CODE2, TOTAL_LEAVE_CODE3, 
						  TOTAL_LEAVE_CODE4, TOTAL_LEAVE_CODE5, TOTAL_LEAVE_CODE6, TOTAL_LEAVE_CODE7, TOTAL_LEAVE_CODE8, 
						  TOTAL_LEAVE_CODE9, TOTAL_LEAVE_CODE10, TOTAL_LEAVE_CODE11, TOTAL_LEAVE_CODE12, TOTAL_LEAVE_CODE13, 
						  TOTAL_LEAVE_CODE14, TOTAL_LEAVE_CODE15, TOTAL_LEAVE_CODE16, TOTAL_LEAVE_CODE17, TOTAL_LEAVE_CODE99, 
						  GRANDTOTAL_LEAVE, GRANDTOTAL_ORDER, REMAIN_LEAVE_CODE6, GRANDTOTAL_LEAVE_N6, GRANDTOTAL_ORDER_N6, 
						  CUR_YEAR, USER_CREATE, to_char(CREATE_DATE,'yyyy-mm-dd') as CREATE_DATE, USER_UPDATE, 
						  to_char(UPDATE_DATE,'yyyy-mm-dd') as UPDATE_DATE 
						  FROM HR_LEAVEYEAR_OFFICER
						  ORDER BY ID, BUD_YEAR ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		$MAX_ID = 1;
		while($data = $db_att->get_array()){
			$PER_CARDNO = trim($data[ID]);
			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = '$PER_CARDNO' ";
			$db_dpis->send_cmd($cmd);
			$db_dpis->show_error();
			$data_dpis = $db_dpis->get_array();
			$PER_ID = $data_dpis[NEW_CODE] + 0;

			if ($PER_ID > 0) {
				$BUD_YEAR = trim($data[BUD_YEAR]);
				$ABSENT_TYPE = $data[LEAVE_CODE];
				$ABS_ENDDATE = trim($data[LEAVE_TO]);
				$ABS_DAY = trim($data[DAY_TOTAL]);
				$ABS_REMARK = trim($data[LEAVE_MEMO])." ".trim($data[REPRESENTATIVE]);
				if (!$ABS_DAY) $ABS_DAY = "NULL";

				$UPDATE_USER = 99999;
				if ($data[USER_UPDATE]) $USER_UPDATE = trim($data[USER_UPDATE]);
				else $USER_UPDATE = trim($data[USER_CREATE]);
				if ($data[UPDATE_DATE]) $UPDATE_DATE = trim($data[UPDATE_DATE]);
				else  $UPDATE_DATE = trim($data[CREATE_DATE]);
				if ($USER_UPDATE) {
					$cmd = " select ID from USER_DETAIL where USERNAME = '$USER_UPDATE' ";
					$db_dpis2->send_cmd($cmd);
	//				$db_dpis2->show_error();
					$data2 = $db_dpis2->get_array();
					if ($data2[ID]) $UPDATE_USER = $data2[ID];
				}
				if (!$UPDATE_DATE) $UPDATE_DATE = date("Y-m-d H:i:s");

				$cmd = " INSERT INTO PER_ABSENTSUM(AS_ID, PER_ID, AS_YEAR, AS_CYCLE, START_DATE,  END_DATE, AB_CODE_01, AB_COUNT_01, AB_CODE_02, 
								AB_CODE_03, AB_COUNT_03, AB_CODE_04, AB_CODE_05, AB_CODE_06, AB_CODE_07, AB_CODE_08, AB_CODE_09, AB_CODE_10, 
								AB_CODE_11, AB_CODE_12, AB_CODE_13, INCREASE_AB_CODE_04, REMAIN_AB_CODE_04, TOTAL_LEAVE, 
								TOTAL_COUNT, PER_CARDNO, UPDATE_USER, UPDATE_DATE)
								VALUES ($MAX_ID, $PER_ID, '$BUD_YEAR', $AS_CYCLE, '$START_DATE', '$END_DATE', $AB_CODE_01, $AB_COUNT_01, $AB_CODE_02, $AB_CODE_03, 
								$AB_COUNT_03, $AB_CODE_04, $AB_CODE_05, $AB_CODE_06, $AB_CODE_07, $AB_CODE_08, $AB_CODE_09, $AB_CODE_10, $AB_CODE_11, 
								$AB_CODE_12, $AB_CODE_13, $INCREASE_AB_CODE_04, $REMAIN_AB_CODE_04, $TOTAL_LEAVE_2, $TOTAL_COUNT_2, 
								$PER_CARDNO, $UPDATE_USER, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);

				$cmd1 = " SELECT AS_ID FROM PER_ABSENTSUM WHERE AS_ID = $MAX_ID "; 
				$count_data = $db_dpis1->send_cmd($cmd1);
				if (!$count_data) {
					echo "$cmd<br>==================<br>";
					$db_dpis->show_error();
					echo "<br>end ". ++$i  ."=======================<br>";
				}
				$MAX_ID++;
				$PER_ABSENTSUM = $MAX_ID;    
			}
		} // end while						

		$PER_ABSENTSUM--;
		$cmd = " select count(PER_ID) as COUNT_NEW from PER_ABSENTSUM ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_ABSENTSUM - $PER_ABSENTSUM - $COUNT_NEW<br>";
		
// สรุปวันลาลูกจ้างประจำ 3873
		$cmd = " SELECT ID, BUD_YEAR, INCREASE_LEAVE_CODE6, TOTAL_LEAVE_CODE1, TOTAL_LEAVE_CODE2, TOTAL_LEAVE_CODE3, 
						  TOTAL_LEAVE_CODE4, TOTAL_LEAVE_CODE5, TOTAL_LEAVE_CODE6, TOTAL_LEAVE_CODE7, TOTAL_LEAVE_CODE8, 
						  TOTAL_LEAVE_CODE9, TOTAL_LEAVE_CODE10, TOTAL_LEAVE_CODE11, TOTAL_LEAVE_CODE12, TOTAL_LEAVE_CODE13, 
						  TOTAL_LEAVE_CODE14, TOTAL_LEAVE_CODE15, TOTAL_LEAVE_CODE16, TOTAL_LEAVE_CODE17, TOTAL_LEAVE_CODE99, 
						  GRANDTOTAL_LEAVE, GRANDTOTAL_ORDER, REMAIN_LEAVE_CODE6, GRANDTOTAL_LEAVE_N6, GRANDTOTAL_ORDER_N6, 
						  CUR_YEAR, USER_CREATE, to_char(CREATE_DATE,'yyyy-mm-dd') as CREATE_DATE, USER_UPDATE, 
						  to_char(UPDATE_DATE,'yyyy-mm-dd') as UPDATE_DATE 
						  FROM HR_LEAVEYEAR_EMP
						  ORDER BY ID, BUD_YEAR ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		while($data = $db_att->get_array()){
			$PER_ABSENTSUM++;
			$PER_CARDNO = trim($data[ID]);
			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = '$PER_CARDNO' ";
			$db_dpis->send_cmd($cmd);
			$db_dpis->show_error();
			$data_dpis = $db_dpis->get_array();
			$PER_ID = $data_dpis[NEW_CODE] + 0;

			if ($PER_ID > 0) {
				$ABSENT_TYPE = $data[LEAVE_CODE];
				$ABS_STARTDATE = trim($data[LEAVE_FROM]);
				$ABS_ENDDATE = trim($data[LEAVE_TO]);
				$ABS_DAY = trim($data[DAY_TOTAL]);
				$ABS_REMARK = trim($data[LEAVE_MEMO])." ".trim($data[REPRESENTATIVE]);
				if (!$ABS_DAY) $ABS_DAY = "NULL";

				$UPDATE_USER = 99999;
				if ($data[USER_UPDATE]) $USER_UPDATE = trim($data[USER_UPDATE]);
				else $USER_UPDATE = trim($data[USER_CREATE]);
				if ($data[UPDATE_DATE]) $UPDATE_DATE = trim($data[UPDATE_DATE]);
				else  $UPDATE_DATE = trim($data[CREATE_DATE]);
				if ($USER_UPDATE) {
					$cmd = " select ID from USER_DETAIL where USERNAME = '$USER_UPDATE' ";
					$db_dpis2->send_cmd($cmd);
	//				$db_dpis2->show_error();
					$data2 = $db_dpis2->get_array();
					if ($data2[ID]) $UPDATE_USER = $data2[ID];
				}
				if (!$UPDATE_DATE) $UPDATE_DATE = date("Y-m-d H:i:s");

				$cmd = " INSERT INTO PER_ABSENTSUM(ABS_ID, PER_ID, AB_CODE, ABS_STARTDATE, ABS_STARTPERIOD, 
								ABS_ENDDATE, ABS_ENDPERIOD, ABS_DAY, UPDATE_USER, UPDATE_DATE, PER_CARDNO, ABS_REMARK)
								VALUES ($MAX_ID, $PER_ID, '$ABSENT_TYPE', '$ABS_STARTDATE', 3, '$ABS_ENDDATE', 3, $ABS_DAY, 
								$UPDATE_USER, '$UPDATE_DATE', '$PER_CARDNO', '$ABS_REMARK') ";
				$db_dpis->send_cmd($cmd);

				$cmd1 = " SELECT AS_ID FROM PER_ABSENTSUM WHERE AS_ID = $MAX_ID "; 
				$count_data = $db_dpis1->send_cmd($cmd1);
				if (!$count_data) {
					echo "$cmd<br>==================<br>";
					$db_dpis->show_error();
					echo "<br>end ". ++$i  ."=======================<br>";
				}
				$MAX_ID++;
				$PER_ABSENTSUM = $MAX_ID;    
			}
		} // end while						

		$PER_ABSENTSUM--;
		$cmd = " select count(PER_ID) as COUNT_NEW from PER_ABSENTSUM ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_ABSENTSUM - $PER_ABSENTSUM - $COUNT_NEW<br>";
		
// สรุปวันลาลูกจ้างชั่วคราว 481
		$cmd = " SELECT ID, BUD_YEAR, INCREASE_LEAVE_CODE6, TOTAL_LEAVE_CODE1, TOTAL_LEAVE_CODE2, TOTAL_LEAVE_CODE3, 
						  TOTAL_LEAVE_CODE4, TOTAL_LEAVE_CODE5, TOTAL_LEAVE_CODE6, TOTAL_LEAVE_CODE7, TOTAL_LEAVE_CODE8, 
						  TOTAL_LEAVE_CODE9, TOTAL_LEAVE_CODE10, TOTAL_LEAVE_CODE11, TOTAL_LEAVE_CODE12, TOTAL_LEAVE_CODE13, 
						  TOTAL_LEAVE_CODE14, TOTAL_LEAVE_CODE15, TOTAL_LEAVE_CODE16, TOTAL_LEAVE_CODE17, TOTAL_LEAVE_CODE99, 
						  GRANDTOTAL_LEAVE, GRANDTOTAL_ORDER, REMAIN_LEAVE_CODE6, GRANDTOTAL_LEAVE_N6, GRANDTOTAL_ORDER_N6, 
						  CUR_YEAR, USER_CREATE, to_char(CREATE_DATE,'yyyy-mm-dd') as CREATE_DATE, USER_UPDATE, 
						  to_char(UPDATE_DATE,'yyyy-mm-dd') as UPDATE_DATE 
						  FROM HR_LEAVEYEAR_EMPTEMP
						  ORDER BY ID, BUD_YEAR ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		while($data = $db_att->get_array()){
			$PER_ABSENTSUM++;
			$PER_CARDNO = trim($data[ID]);
			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = '$PER_CARDNO' ";
			$db_dpis->send_cmd($cmd);
			$db_dpis->show_error();
			$data_dpis = $db_dpis->get_array();
			$PER_ID = $data_dpis[NEW_CODE] + 0;

			if ($PER_ID > 0) {
				$ABSENT_TYPE = $data[LEAVE_CODE];
				$ABS_STARTDATE = trim($data[LEAVE_FROM]);
				$ABS_ENDDATE = trim($data[LEAVE_TO]);
				$ABS_DAY = trim($data[DAY_TOTAL]);
				$ABS_REMARK = trim($data[LEAVE_MEMO])." ".trim($data[REPRESENTATIVE]);
				if (!$ABS_DAY) $ABS_DAY = "NULL";

				$UPDATE_USER = 99999;
				if ($data[USER_UPDATE]) $USER_UPDATE = trim($data[USER_UPDATE]);
				else $USER_UPDATE = trim($data[USER_CREATE]);
				if ($data[UPDATE_DATE]) $UPDATE_DATE = trim($data[UPDATE_DATE]);
				else  $UPDATE_DATE = trim($data[CREATE_DATE]);
				if ($USER_UPDATE) {
					$cmd = " select ID from USER_DETAIL where USERNAME = '$USER_UPDATE' ";
					$db_dpis2->send_cmd($cmd);
	//				$db_dpis2->show_error();
					$data2 = $db_dpis2->get_array();
					if ($data2[ID]) $UPDATE_USER = $data2[ID];
				}
				if (!$UPDATE_DATE) $UPDATE_DATE = date("Y-m-d H:i:s");

				$cmd = " INSERT INTO PER_ABSENTSUM(ABS_ID, PER_ID, AB_CODE, ABS_STARTDATE, ABS_STARTPERIOD, 
								ABS_ENDDATE, ABS_ENDPERIOD, ABS_DAY, UPDATE_USER, UPDATE_DATE, PER_CARDNO, ABS_REMARK)
								VALUES ($MAX_ID, $PER_ID, '$ABSENT_TYPE', '$ABS_STARTDATE', 3, '$ABS_ENDDATE', 3, $ABS_DAY, 
								$UPDATE_USER, '$UPDATE_DATE', '$PER_CARDNO', '$ABS_REMARK') ";
				$db_dpis->send_cmd($cmd);

				$cmd1 = " SELECT AS_ID FROM PER_ABSENTSUM WHERE AS_ID = $MAX_ID "; 
				$count_data = $db_dpis1->send_cmd($cmd1);
				if (!$count_data) {
					echo "$cmd<br>==================<br>";
					$db_dpis->show_error();
					echo "<br>end ". ++$i  ."=======================<br>";
				}
				$MAX_ID++;
				$PER_ABSENTSUM = $MAX_ID;    
			}
		} // end while						

		$PER_ABSENTSUM--;
		$cmd = " select count(PER_ID) as COUNT_NEW from PER_ABSENTSUM ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_ABSENTSUM - $PER_ABSENTSUM - $COUNT_NEW<br>";
		
		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_att->free_result();
		$db_att1->free_result();
	} // end if

	if( $command=='ADDRESS' ){
// ที่อยู่ 76395 ok
		$cmd = " truncate table per_address ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

// ที่อยู่ข้าราชการ 29876
		$cmd = " SELECT ID, H_NUMBER, STREET, PROVINCE_CODE, AMPHUR_CODE, DISTRICT_CODE, TEL, ZIPCODE, 
						CONTACT_H_NUMBER, CONTACT_STREET, CONTACT_PROVINCE_CODE, CONTACT_AMPHUR_CODE, 
						CONTACT_DISTRICT_CODE, CONTACT_TEL, CONTACT_ZIPCODE, USER_CREATE, to_char(CREATE_DATE,'yyyy-mm-dd') as CREATE_DATE, 
						USER_UPDATE, to_char(UPDATE_DATE,'yyyy-mm-dd') as UPDATE_DATE
						FROM HR_PERSONAL_OFFICER_ADDRESS
						ORDER BY ID ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		$MAX_ID = 1;
		while($data = $db_att->get_array()){
			$PER_CARDNO = trim($data[ID]);
			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = '$PER_CARDNO' ";
			$db_dpis->send_cmd($cmd);
			$db_dpis->show_error();
			$data_dpis = $db_dpis->get_array();
			$PER_ID = $data_dpis[NEW_CODE] + 0;

			if ($PER_ID > 0) {
				$ADR_NO = trim($data[H_NUMBER]);
				$ADR_ROAD = trim($data[STREET]);
				if (trim($data[PROVINCE_CODE])) $PV_CODE = trim($data[PROVINCE_CODE])."00";
				$AP_CODE = trim($data[PROVINCE_CODE]).trim($data[AMPHUR_CODE]);
				$DT_CODE = trim($data[PROVINCE_CODE]).trim($data[AMPHUR_CODE]).trim($data[DISTRICT_CODE]);
				$ADR_HOME_TEL = trim($data[TEL]);
				$ADR_POSTCODE = trim($data[ZIPCODE]);
				$ADR_ROAD = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($ADR_ROAD)));

				$UPDATE_USER = 99999;
				if ($data[USER_UPDATE]) $USER_UPDATE = trim($data[USER_UPDATE]);
				else $USER_UPDATE = trim($data[USER_CREATE]);
				if ($data[UPDATE_DATE]) $UPDATE_DATE = trim($data[UPDATE_DATE]);
				else  $UPDATE_DATE = trim($data[CREATE_DATE]);
				if ($USER_UPDATE) {
					$cmd = " select ID from USER_DETAIL where USERNAME = '$USER_UPDATE' ";
					$db_dpis2->send_cmd($cmd);
	//				$db_dpis2->show_error();
					$data2 = $db_dpis2->get_array();
					if ($data2[ID]) $UPDATE_USER = $data2[ID];
				}
				if (!$UPDATE_DATE) $UPDATE_DATE = date("Y-m-d H:i:s");

				$cmd = " INSERT INTO PER_ADDRESS(ADR_ID, PER_ID, ADR_TYPE, ADR_NO, ADR_ROAD, ADR_SOI, ADR_MOO, ADR_VILLAGE, 
								ADR_BUILDING, ADR_DISTRICT, AP_CODE, PV_CODE, ADR_HOME_TEL, ADR_OFFICE_TEL, ADR_FAX, ADR_MOBILE, 
								ADR_EMAIL, ADR_POSTCODE, ADR_REMARK, UPDATE_USER, UPDATE_DATE, DT_CODE)
								VALUES ($MAX_ID, $PER_ID, 2, '$ADR_NO', '$ADR_ROAD', '$ADR_SOI', '$ADR_MOO', '$ADR_VILLAGE', 
								'$ADR_BUILDING', '$ADR_DISTRICT', '$AP_CODE', '$PV_CODE', '$ADR_HOME_TEL', '$ADR_OFFICE_TEL', '$ADR_FAX', '$ADR_MOBILE', 
								'$ADR_EMAIL', '$ADR_POSTCODE', '$ADR_REMARK', $UPDATE_USER, '$UPDATE_DATE', '$DT_CODE') ";
				$db_dpis->send_cmd($cmd);

				$cmd1 = " SELECT ADR_ID FROM PER_ADDRESS WHERE ADR_ID = $MAX_ID "; 
				$count_data = $db_dpis1->send_cmd($cmd1);
				if (!$count_data) {
					echo "$cmd<br>==================<br>";
					$db_dpis->show_error();
					echo "<br>end ". ++$i  ."=======================<br>";
				}
				$MAX_ID++;
				$PER_ADDRESS = $MAX_ID;    

				$ADR_NO = trim($data[CONTACT_H_NUMBER]);
				$ADR_ROAD = trim($data[CONTACT_STREET]);
				if (trim($data[CONTACT_PROVINCE_CODE])) $PV_CODE = trim($data[CONTACT_PROVINCE_CODE])."00";
				$AP_CODE = trim($data[CONTACT_PROVINCE_CODE]).trim($data[CONTACT_AMPHUR_CODE]);
				$DT_CODE = trim($data[CONTACT_PROVINCE_CODE]).trim($data[CONTACT_AMPHUR_CODE]).trim($data[CONTACT_DISTRICT_CODE]);
				$ADR_HOME_TEL = trim($data[TEL]);
				$ADR_POSTCODE = trim($data[ZIPCODE]);
				$ADR_ROAD = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($ADR_ROAD)));

				$cmd = " INSERT INTO PER_ADDRESS(ADR_ID, PER_ID, ADR_TYPE, ADR_NO, ADR_ROAD, ADR_SOI, ADR_MOO, ADR_VILLAGE, 
								ADR_BUILDING, ADR_DISTRICT, AP_CODE, PV_CODE, ADR_HOME_TEL, ADR_OFFICE_TEL, ADR_FAX, ADR_MOBILE, 
								ADR_EMAIL, ADR_POSTCODE, ADR_REMARK, UPDATE_USER, UPDATE_DATE, DT_CODE)
								VALUES ($MAX_ID, $PER_ID, 1, '$ADR_NO', '$ADR_ROAD', '$ADR_SOI', '$ADR_MOO', '$ADR_VILLAGE', 
								'$ADR_BUILDING', '$ADR_DISTRICT', '$AP_CODE', '$PV_CODE', '$ADR_HOME_TEL', '$ADR_OFFICE_TEL', '$ADR_FAX', '$ADR_MOBILE', 
								'$ADR_EMAIL', '$ADR_POSTCODE', '$ADR_REMARK', $UPDATE_USER, '$UPDATE_DATE', '$DT_CODE') ";
				$db_dpis->send_cmd($cmd);

				$cmd1 = " SELECT ADR_ID FROM PER_ADDRESS WHERE ADR_ID = $MAX_ID "; 
				$count_data = $db_dpis1->send_cmd($cmd1);
				if (!$count_data) {
					echo "$cmd<br>==================<br>";
					$db_dpis->show_error();
					echo "<br>end ". ++$i  ."=======================<br>";
				}
				$MAX_ID++;
				$PER_ADDRESS = $MAX_ID;    
			}
		} // end while						

		$PER_ADDRESS--;
		$cmd = " select count(PER_ID) as COUNT_NEW from PER_ADDRESS ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_ADDRESS - $PER_ADDRESS - $COUNT_NEW<br>";

// ที่อยู่ลูกจ้างประจำ 42834
		$cmd = " SELECT ID, H_NUMBER, STREET, PROVINCE_CODE, AMPHUR_CODE, DISTRICT_CODE, TEL, ZIPCODE, 
						CONTACT_H_NUMBER, CONTACT_STREET, CONTACT_PROVINCE_CODE, CONTACT_AMPHUR_CODE, 
						CONTACT_DISTRICT_CODE, CONTACT_TEL, CONTACT_ZIPCODE, USER_CREATE, to_char(CREATE_DATE,'yyyy-mm-dd') as CREATE_DATE, 
						USER_UPDATE, to_char(UPDATE_DATE,'yyyy-mm-dd') as UPDATE_DATE
						FROM HR_PERSONAL_EMP_ADDRESS
						ORDER BY ID ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		while($data = $db_att->get_array()){
			$PER_CARDNO = trim($data[ID]);
			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = '$PER_CARDNO' ";
			$db_dpis->send_cmd($cmd);
			$db_dpis->show_error();
			$data_dpis = $db_dpis->get_array();
			$PER_ID = $data_dpis[NEW_CODE] + 0;

			if ($PER_ID > 0) {
				$ADR_NO = trim($data[H_NUMBER]);
				$ADR_ROAD = trim($data[STREET]);
				if (trim($data[PROVINCE_CODE])) $PV_CODE = trim($data[PROVINCE_CODE])."00";
				$AP_CODE = trim($data[PROVINCE_CODE]).trim($data[AMPHUR_CODE]);
				$DT_CODE = trim($data[PROVINCE_CODE]).trim($data[AMPHUR_CODE]).trim($data[DISTRICT_CODE]);
				$ADR_HOME_TEL = trim($data[TEL]);
				$ADR_POSTCODE = trim($data[ZIPCODE]);
				$ADR_ROAD = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($ADR_ROAD)));

				$UPDATE_USER = 99999;
				if ($data[USER_UPDATE]) $USER_UPDATE = trim($data[USER_UPDATE]);
				else $USER_UPDATE = trim($data[USER_CREATE]);
				if ($data[UPDATE_DATE]) $UPDATE_DATE = trim($data[UPDATE_DATE]);
				else  $UPDATE_DATE = trim($data[CREATE_DATE]);
				if ($USER_UPDATE) {
					$cmd = " select ID from USER_DETAIL where USERNAME = '$USER_UPDATE' ";
					$db_dpis2->send_cmd($cmd);
	//				$db_dpis2->show_error();
					$data2 = $db_dpis2->get_array();
					if ($data2[ID]) $UPDATE_USER = $data2[ID];
				}
				if (!$UPDATE_DATE) $UPDATE_DATE = date("Y-m-d H:i:s");

				$cmd = " INSERT INTO PER_ADDRESS(ADR_ID, PER_ID, ADR_TYPE, ADR_NO, ADR_ROAD, ADR_SOI, ADR_MOO, ADR_VILLAGE, 
								ADR_BUILDING, ADR_DISTRICT, AP_CODE, PV_CODE, ADR_HOME_TEL, ADR_OFFICE_TEL, ADR_FAX, ADR_MOBILE, 
								ADR_EMAIL, ADR_POSTCODE, ADR_REMARK, UPDATE_USER, UPDATE_DATE, DT_CODE)
								VALUES ($MAX_ID, $PER_ID, 2, '$ADR_NO', '$ADR_ROAD', '$ADR_SOI', '$ADR_MOO', '$ADR_VILLAGE', 
								'$ADR_BUILDING', '$ADR_DISTRICT', '$AP_CODE', '$PV_CODE', '$ADR_HOME_TEL', '$ADR_OFFICE_TEL', '$ADR_FAX', '$ADR_MOBILE', 
								'$ADR_EMAIL', '$ADR_POSTCODE', '$ADR_REMARK', $UPDATE_USER, '$UPDATE_DATE', '$DT_CODE') ";
				$db_dpis->send_cmd($cmd);

				$cmd1 = " SELECT ADR_ID FROM PER_ADDRESS WHERE ADR_ID = $MAX_ID "; 
				$count_data = $db_dpis1->send_cmd($cmd1);
				if (!$count_data) {
					echo "$cmd<br>==================<br>";
					$db_dpis->show_error();
					echo "<br>end ". ++$i  ."=======================<br>";
				}
				$MAX_ID++;
				$PER_ADDRESS = $MAX_ID;    

				$ADR_NO = trim($data[CONTACT_H_NUMBER]);
				$ADR_ROAD = trim($data[CONTACT_STREET]);
				if (trim($data[CONTACT_PROVINCE_CODE])) $PV_CODE = trim($data[CONTACT_PROVINCE_CODE])."00";
				$AP_CODE = trim($data[CONTACT_PROVINCE_CODE]).trim($data[CONTACT_AMPHUR_CODE]);
				$DT_CODE = trim($data[CONTACT_PROVINCE_CODE]).trim($data[CONTACT_AMPHUR_CODE]).trim($data[CONTACT_DISTRICT_CODE]);
				$ADR_HOME_TEL = trim($data[TEL]);
				$ADR_POSTCODE = trim($data[ZIPCODE]);
				$ADR_ROAD = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($ADR_ROAD)));

				$cmd = " INSERT INTO PER_ADDRESS(ADR_ID, PER_ID, ADR_TYPE, ADR_NO, ADR_ROAD, ADR_SOI, ADR_MOO, ADR_VILLAGE, 
								ADR_BUILDING, ADR_DISTRICT, AP_CODE, PV_CODE, ADR_HOME_TEL, ADR_OFFICE_TEL, ADR_FAX, ADR_MOBILE, 
								ADR_EMAIL, ADR_POSTCODE, ADR_REMARK, UPDATE_USER, UPDATE_DATE, DT_CODE)
								VALUES ($MAX_ID, $PER_ID, 1, '$ADR_NO', '$ADR_ROAD', '$ADR_SOI', '$ADR_MOO', '$ADR_VILLAGE', 
								'$ADR_BUILDING', '$ADR_DISTRICT', '$AP_CODE', '$PV_CODE', '$ADR_HOME_TEL', '$ADR_OFFICE_TEL', '$ADR_FAX', '$ADR_MOBILE', 
								'$ADR_EMAIL', '$ADR_POSTCODE', '$ADR_REMARK', $UPDATE_USER, '$UPDATE_DATE', '$DT_CODE') ";
				$db_dpis->send_cmd($cmd);

				$cmd1 = " SELECT ADR_ID FROM PER_ADDRESS WHERE ADR_ID = $MAX_ID "; 
				$count_data = $db_dpis1->send_cmd($cmd1);
				if (!$count_data) {
					echo "$cmd<br>==================<br>";
					$db_dpis->show_error();
					echo "<br>end ". ++$i  ."=======================<br>";
				}
				$MAX_ID++;
				$PER_ADDRESS = $MAX_ID;    
			}
		} // end while						

		$PER_ADDRESS--;
		$cmd = " select count(PER_ID) as COUNT_NEW from PER_ADDRESS ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_ADDRESS - $PER_ADDRESS - $COUNT_NEW<br>";

// ที่อยู่ลูกจ้างชั่วคราว 3883
		$cmd = " SELECT ID, H_NUMBER, STREET, PROVINCE_CODE, AMPHUR_CODE, DISTRICT_CODE, TEL, ZIPCODE, 
						CONTACT_H_NUMBER, CONTACT_STREET, CONTACT_PROVINCE_CODE, CONTACT_AMPHUR_CODE, 
						CONTACT_DISTRICT_CODE, CONTACT_TEL, CONTACT_ZIPCODE, USER_CREATE, to_char(CREATE_DATE,'yyyy-mm-dd') as CREATE_DATE, 
						USER_UPDATE, to_char(UPDATE_DATE,'yyyy-mm-dd') as UPDATE_DATE
						FROM HR_PERSONAL_EMPTEMP_ADDRESS
						ORDER BY ID ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		while($data = $db_att->get_array()){
			$PER_CARDNO = trim($data[ID]);
			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = '$PER_CARDNO' ";
			$db_dpis->send_cmd($cmd);
			$db_dpis->show_error();
			$data_dpis = $db_dpis->get_array();
			$PER_ID = $data_dpis[NEW_CODE] + 0;

			if ($PER_ID > 0) {
				$ADR_NO = trim($data[H_NUMBER]);
				$ADR_ROAD = trim($data[STREET]);
				if (trim($data[PROVINCE_CODE])) $PV_CODE = trim($data[PROVINCE_CODE])."00";
				$AP_CODE = trim($data[PROVINCE_CODE]).trim($data[AMPHUR_CODE]);
				$DT_CODE = trim($data[PROVINCE_CODE]).trim($data[AMPHUR_CODE]).trim($data[DISTRICT_CODE]);
				$ADR_HOME_TEL = trim($data[TEL]);
				$ADR_POSTCODE = trim($data[ZIPCODE]);
				$ADR_ROAD = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($ADR_ROAD)));

				$UPDATE_USER = 99999;
				if ($data[USER_UPDATE]) $USER_UPDATE = trim($data[USER_UPDATE]);
				else $USER_UPDATE = trim($data[USER_CREATE]);
				if ($data[UPDATE_DATE]) $UPDATE_DATE = trim($data[UPDATE_DATE]);
				else  $UPDATE_DATE = trim($data[CREATE_DATE]);
				if ($USER_UPDATE) {
					$cmd = " select ID from USER_DETAIL where USERNAME = '$USER_UPDATE' ";
					$db_dpis2->send_cmd($cmd);
	//				$db_dpis2->show_error();
					$data2 = $db_dpis2->get_array();
					if ($data2[ID]) $UPDATE_USER = $data2[ID];
				}
				if (!$UPDATE_DATE) $UPDATE_DATE = date("Y-m-d H:i:s");

				$cmd = " INSERT INTO PER_ADDRESS(ADR_ID, PER_ID, ADR_TYPE, ADR_NO, ADR_ROAD, ADR_SOI, ADR_MOO, ADR_VILLAGE, 
								ADR_BUILDING, ADR_DISTRICT, AP_CODE, PV_CODE, ADR_HOME_TEL, ADR_OFFICE_TEL, ADR_FAX, ADR_MOBILE, 
								ADR_EMAIL, ADR_POSTCODE, ADR_REMARK, UPDATE_USER, UPDATE_DATE, DT_CODE)
								VALUES ($MAX_ID, $PER_ID, 2, '$ADR_NO', '$ADR_ROAD', '$ADR_SOI', '$ADR_MOO', '$ADR_VILLAGE', 
								'$ADR_BUILDING', '$ADR_DISTRICT', '$AP_CODE', '$PV_CODE', '$ADR_HOME_TEL', '$ADR_OFFICE_TEL', '$ADR_FAX', '$ADR_MOBILE', 
								'$ADR_EMAIL', '$ADR_POSTCODE', '$ADR_REMARK', $UPDATE_USER, '$UPDATE_DATE', '$DT_CODE') ";
				$db_dpis->send_cmd($cmd);

				$cmd1 = " SELECT ADR_ID FROM PER_ADDRESS WHERE ADR_ID = $MAX_ID "; 
				$count_data = $db_dpis1->send_cmd($cmd1);
				if (!$count_data) {
					echo "$cmd<br>==================<br>";
					$db_dpis->show_error();
					echo "<br>end ". ++$i  ."=======================<br>";
				}
				$MAX_ID++;
				$PER_ADDRESS = $MAX_ID;    

				$ADR_NO = trim($data[CONTACT_H_NUMBER]);
				$ADR_ROAD = trim($data[CONTACT_STREET]);
				if (trim($data[CONTACT_PROVINCE_CODE])) $PV_CODE = trim($data[CONTACT_PROVINCE_CODE])."00";
				$AP_CODE = trim($data[CONTACT_PROVINCE_CODE]).trim($data[CONTACT_AMPHUR_CODE]);
				$DT_CODE = trim($data[CONTACT_PROVINCE_CODE]).trim($data[CONTACT_AMPHUR_CODE]).trim($data[CONTACT_DISTRICT_CODE]);
				$ADR_HOME_TEL = trim($data[TEL]);
				$ADR_POSTCODE = trim($data[ZIPCODE]);
				$ADR_ROAD = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($ADR_ROAD)));

				$cmd = " INSERT INTO PER_ADDRESS(ADR_ID, PER_ID, ADR_TYPE, ADR_NO, ADR_ROAD, ADR_SOI, ADR_MOO, ADR_VILLAGE, 
								ADR_BUILDING, ADR_DISTRICT, AP_CODE, PV_CODE, ADR_HOME_TEL, ADR_OFFICE_TEL, ADR_FAX, ADR_MOBILE, 
								ADR_EMAIL, ADR_POSTCODE, ADR_REMARK, UPDATE_USER, UPDATE_DATE, DT_CODE)
								VALUES ($MAX_ID, $PER_ID, 1, '$ADR_NO', '$ADR_ROAD', '$ADR_SOI', '$ADR_MOO', '$ADR_VILLAGE', 
								'$ADR_BUILDING', '$ADR_DISTRICT', '$AP_CODE', '$PV_CODE', '$ADR_HOME_TEL', '$ADR_OFFICE_TEL', '$ADR_FAX', '$ADR_MOBILE', 
								'$ADR_EMAIL', '$ADR_POSTCODE', '$ADR_REMARK', $UPDATE_USER, '$UPDATE_DATE', '$DT_CODE') ";
				$db_dpis->send_cmd($cmd);

				$cmd1 = " SELECT ADR_ID FROM PER_ADDRESS WHERE ADR_ID = $MAX_ID "; 
				$count_data = $db_dpis1->send_cmd($cmd1);
				if (!$count_data) {
					echo "$cmd<br>==================<br>";
					$db_dpis->show_error();
					echo "<br>end ". ++$i  ."=======================<br>";
				}
				$MAX_ID++;
				$PER_ADDRESS = $MAX_ID;    
			}
		} // end while						

		$PER_ADDRESS--;
		$cmd = " select count(PER_ID) as COUNT_NEW from PER_ADDRESS ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_ADDRESS - $PER_ADDRESS - $COUNT_NEW<br>";

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_att->free_result();
		$db_att1->free_result();
	} // end if

	if( $command=='FAMILY1' ){
// ครอบครัว 118543
		$cmd = " truncate table per_family ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

// ครอบครัวข้าราชการ 70253 
		$cmd = " SELECT ID, FATHER_RANK_CODE, FATHER_FNAME, FATHER_LNAME, MOTHER_RANK_CODE, 
						  MOTHER_FNAME, MOTHER_LNAME, SPOUSE_ID, SPOUSE_FNAME, SPOUSE_LNAME, SPOUSE_FLNAME, 
						  MARRIAGE_STATE, LIFE_SPOUSE, SUN_NO, SPOUSE_RANK_CODE, USER_CREATE, 
						  to_char(CREATE_DATE,'yyyy-mm-dd') as CREATE_DATE, USER_UPDATE, 
						  to_char(UPDATE_DATE,'yyyy-mm-dd') as UPDATE_DATE
						  FROM HR_PERSONAL_OFFICER_FAMILY 
						  ORDER BY ID ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		$MAX_ID = 1;
		while($data = $db_att->get_array()){
			$PER_CARDNO = trim($data[ID]);
			$cmd = " select PER_ID, PER_GENDER from PER_PERSONAL where PER_CARDNO = '$PER_CARDNO' ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			$data_dpis = $db_dpis->get_array();
			$PER_ID = $data_dpis[PER_ID] + 0;
			if ($PER_ID > 0) {

				$PER_GENDER = $data_dpis[PER_GENDER] + 0;

				$PN_CODE = trim($data[FATHER_RANK_CODE]); 
				$FATHER_FNAME = trim($data[FATHER_FNAME]);
				$FATHER_LNAME = trim($data[FATHER_LNAME]);
				if ($FATHER_FNAME || $FATHER_LNAME) {
					$FML_GENDER = 1;
					$FML_TYPE = 1;
					$FML_ALIVE = 1;

					$UPDATE_USER = 99999;
					if ($data[USER_UPDATE]) $USER_UPDATE = trim($data[USER_UPDATE]);
					else $USER_UPDATE = trim($data[USER_CREATE]);
					if ($data[UPDATE_DATE]) $UPDATE_DATE = trim($data[UPDATE_DATE]);
					else  $UPDATE_DATE = trim($data[CREATE_DATE]);
					if ($USER_UPDATE) {
						$cmd = " select ID from USER_DETAIL where USERNAME = '$USER_UPDATE' ";
						$db_dpis2->send_cmd($cmd);
		//				$db_dpis2->show_error();
						$data2 = $db_dpis2->get_array();
						if ($data2[ID]) $UPDATE_USER = $data2[ID];
					}
					if (!$UPDATE_DATE) $UPDATE_DATE = date("Y-m-d H:i:s");

					$cmd = " insert into PER_FAMILY (FML_ID, PER_ID, FML_TYPE, PN_CODE, FML_NAME, FML_SURNAME, FML_CARDNO, 
									FML_GENDER, FML_BIRTHDATE,	FML_ALIVE, RE_CODE, MR_CODE, UPDATE_USER, UPDATE_DATE)
									values ($MAX_ID, $PER_ID, $FML_TYPE, '$PN_CODE', '$FATHER_FNAME', '$FATHER_LNAME',  '$FML_CARDNO', 
									$FML_GENDER, '$FML_BIRTHDATE', $FML_ALIVE, '$RE_CODE', '$MR_CODE', $SESS_USERID, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);

					$cmd1 = " SELECT FML_ID FROM PER_FAMILY WHERE FML_ID = $MAX_ID "; 
					$count_data = $db_dpis1->send_cmd($cmd1);
					if (!$count_data) {
						echo "$cmd<br>==================<br>";
						$db_dpis->show_error();
						echo "<br>end ". ++$i  ."=======================<br>";
					}
					$MAX_ID++;
					$PER_FAMILY = $MAX_ID;    
				}

				$PN_CODE = trim($data[MOTHER_RANK_CODE]); 
				$MOTHER_FNAME = trim($data[MOTHER_FNAME]);
				$MOTHER_LNAME = trim($data[MOTHER_LNAME]);
				$FML_GENDER = 2;
				$FML_TYPE = 2;
				$FML_ALIVE = 1;

				if ($MOTHER_FNAME || $MOTHER_LNAME) {
					$cmd = " insert into PER_FAMILY (FML_ID, PER_ID, FML_TYPE, PN_CODE, FML_NAME, FML_SURNAME, FML_CARDNO, 
									FML_GENDER, FML_BIRTHDATE,	FML_ALIVE, RE_CODE, MR_CODE, UPDATE_USER, UPDATE_DATE)
									values ($MAX_ID, $PER_ID, $FML_TYPE, '$PN_CODE', '$MOTHER_FNAME', '$MOTHER_LNAME',  '$FML_CARDNO', 
									$FML_GENDER, '$FML_BIRTHDATE', $FML_ALIVE, '$RE_CODE', '$MR_CODE', $SESS_USERID, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);

					$cmd1 = " SELECT FML_ID FROM PER_FAMILY WHERE FML_ID = $MAX_ID "; 
					$count_data = $db_dpis1->send_cmd($cmd1);
					if (!$count_data) {
						echo "$cmd<br>==================<br>";
						$db_dpis->show_error();
						echo "<br>end ". ++$i  ."=======================<br>";
					}
					$MAX_ID++;
					$PER_FAMILY = $MAX_ID;    
				}

				$PN_CODE = trim($data[SPOUSE_RANK_CODE]); 
				$SPOUSE_FNAME = trim($data[SPOUSE_FNAME]);
				$SPOUSE_LNAME = trim($data[SPOUSE_LNAME]);
				if ($PER_GENDER == 2) $FML_GENDER = 1;
				elseif ($PER_GENDER == 1) $FML_GENDER = 2;
				$FML_TYPE = 3;
				$FML_ALIVE = 1;

				if ($SPOUSE_FNAME || $SPOUSE_LNAME) {
					$cmd = " insert into PER_FAMILY (FML_ID, PER_ID, FML_TYPE, PN_CODE, FML_NAME, FML_SURNAME, FML_CARDNO, 
									FML_GENDER, FML_BIRTHDATE,	FML_ALIVE, RE_CODE, MR_CODE, UPDATE_USER, UPDATE_DATE)
									values ($MAX_ID, $PER_ID, $FML_TYPE, '$PN_CODE', '$SPOUSE_FNAME', '$SPOUSE_LNAME',  '$FML_CARDNO', 
									$FML_GENDER, '$FML_BIRTHDATE', $FML_ALIVE, '$RE_CODE', '$MR_CODE', $SESS_USERID, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);

					$cmd1 = " SELECT FML_ID FROM PER_FAMILY WHERE FML_ID = $MAX_ID "; 
					$count_data = $db_dpis1->send_cmd($cmd1);
					if (!$count_data) {
						echo "$cmd<br>==================<br>";
						$db_dpis->show_error();
						echo "<br>end ". ++$i  ."=======================<br>";
					}
					$MAX_ID++;
					$PER_FAMILY = $MAX_ID;    
				}
			}
		} // end while						

		$PER_FAMILY--;
		$cmd = " select count(FML_ID) as COUNT_NEW from PER_FAMILY ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_FAMILY - $PER_FAMILY - $COUNT_NEW<br>";

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_att->free_result();
		$db_att1->free_result();
	} // end if

	if( $command=='FAMILY2' ){
		$cmd = " select max(FML_ID) as MAX_ID from PER_FAMILY ";
		$db_dpis1->send_cmd($cmd);
		$data1 = $db_dpis1->get_array();
		$MAX_ID = $data1[MAX_ID] + 1;

// ครอบครัวลูกจ้างประจำ 32365 
		$cmd = " SELECT ID, FATHER_RANK_CODE, FATHER_FNAME, FATHER_LNAME, MOTHER_RANK_CODE, 
						  MOTHER_FNAME, MOTHER_LNAME, SPOUSE_ID, SPOUSE_FNAME, SPOUSE_LNAME, 
						  MARRIAGE_STATE, LIFE_SPOUSE, SUN_NO, SPOUSE_RANK_CODE, USER_CREATE, 
						  to_char(CREATE_DATE,'yyyy-mm-dd') as CREATE_DATE, USER_UPDATE, 
						  to_char(UPDATE_DATE,'yyyy-mm-dd') as UPDATE_DATE
						  FROM HR_PERSONAL_EMP_FAMILY 
						  ORDER BY ID ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		$PER_FAMILY = 0;
		while($data = $db_att->get_array()){
			$PER_CARDNO = trim($data[ID]);
			$cmd = " select PER_ID, PER_GENDER from PER_PERSONAL where PER_CARDNO = '$PER_CARDNO' ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			$data_dpis = $db_dpis->get_array();
			$PER_ID = $data_dpis[PER_ID] + 0;

			if ($PER_ID > 0) {
				$PER_GENDER = $data_dpis[PER_GENDER] + 0;

				$PN_CODE = trim($data[FATHER_RANK_CODE]); 
				$FATHER_FNAME = trim($data[FATHER_FNAME]);
				$FATHER_LNAME = trim($data[FATHER_LNAME]);
				if ($FATHER_FNAME || $FATHER_LNAME) {
					$FML_GENDER = 1;
					$FML_TYPE = 1;
					$FML_ALIVE = 1;

					$UPDATE_USER = 99999;
					if ($data[USER_UPDATE]) $USER_UPDATE = trim($data[USER_UPDATE]);
					else $USER_UPDATE = trim($data[USER_CREATE]);
					if ($data[UPDATE_DATE]) $UPDATE_DATE = trim($data[UPDATE_DATE]);
					else  $UPDATE_DATE = trim($data[CREATE_DATE]);
					if ($USER_UPDATE) {
						$cmd = " select ID from USER_DETAIL where USERNAME = '$USER_UPDATE' ";
						$db_dpis2->send_cmd($cmd);
		//				$db_dpis2->show_error();
						$data2 = $db_dpis2->get_array();
						if ($data2[ID]) $UPDATE_USER = $data2[ID];
					}
					if (!$UPDATE_DATE) $UPDATE_DATE = date("Y-m-d H:i:s");

					$cmd = " insert into PER_FAMILY (FML_ID, PER_ID, FML_TYPE, PN_CODE, FML_NAME, FML_SURNAME, FML_CARDNO, 
									FML_GENDER, FML_BIRTHDATE,	FML_ALIVE, RE_CODE, MR_CODE, UPDATE_USER, UPDATE_DATE)
									values ($MAX_ID, $PER_ID, $FML_TYPE, '$PN_CODE', '$FATHER_FNAME', '$FATHER_LNAME',  '$FML_CARDNO', 
									$FML_GENDER, '$FML_BIRTHDATE', $FML_ALIVE, '$RE_CODE', '$MR_CODE', $SESS_USERID, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);

					$cmd1 = " SELECT FML_ID FROM PER_FAMILY WHERE FML_ID = $MAX_ID "; 
					$count_data = $db_dpis1->send_cmd($cmd1);
					if (!$count_data) {
						echo "$cmd<br>==================<br>";
						$db_dpis->show_error();
						echo "<br>end ". ++$i  ."=======================<br>";
					}
					$MAX_ID++;
					$PER_FAMILY++;    
				}

				$PN_CODE = trim($data[MOTHER_RANK_CODE]); 
				$MOTHER_FNAME = trim($data[MOTHER_FNAME]);
				$MOTHER_LNAME = trim($data[MOTHER_LNAME]);
				$FML_GENDER = 2;
				$FML_TYPE = 2;
				$FML_ALIVE = 1;

				if ($MOTHER_FNAME || $MOTHER_LNAME) {
					$cmd = " insert into PER_FAMILY (FML_ID, PER_ID, FML_TYPE, PN_CODE, FML_NAME, FML_SURNAME, FML_CARDNO, 
									FML_GENDER, FML_BIRTHDATE,	FML_ALIVE, RE_CODE, MR_CODE, UPDATE_USER, UPDATE_DATE)
									values ($MAX_ID, $PER_ID, $FML_TYPE, '$PN_CODE', '$MOTHER_FNAME', '$MOTHER_LNAME',  '$FML_CARDNO', 
									$FML_GENDER, '$FML_BIRTHDATE', $FML_ALIVE, '$RE_CODE', '$MR_CODE', $SESS_USERID, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);

					$cmd1 = " SELECT FML_ID FROM PER_FAMILY WHERE FML_ID = $MAX_ID "; 
					$count_data = $db_dpis1->send_cmd($cmd1);
					if (!$count_data) {
						echo "$cmd<br>==================<br>";
						$db_dpis->show_error();
						echo "<br>end ". ++$i  ."=======================<br>";
					}
					$MAX_ID++;
					$PER_FAMILY++;    
				}

				$PN_CODE = trim($data[SPOUSE_RANK_CODE]); 
				$SPOUSE_FNAME = trim($data[SPOUSE_FNAME]);
				$SPOUSE_LNAME = trim($data[SPOUSE_LNAME]);
				if ($PER_GENDER == 2) $FML_GENDER = 1;
				elseif ($PER_GENDER == 1) $FML_GENDER = 2;
				$FML_TYPE = 3;
				$FML_ALIVE = 1;

				if ($SPOUSE_FNAME || $SPOUSE_LNAME) {
					$cmd = " insert into PER_FAMILY (FML_ID, PER_ID, FML_TYPE, PN_CODE, FML_NAME, FML_SURNAME, FML_CARDNO, 
									FML_GENDER, FML_BIRTHDATE,	FML_ALIVE, RE_CODE, MR_CODE, UPDATE_USER, UPDATE_DATE)
									values ($MAX_ID, $PER_ID, $FML_TYPE, '$PN_CODE', '$SPOUSE_FNAME', '$SPOUSE_LNAME',  '$FML_CARDNO', 
									$FML_GENDER, '$FML_BIRTHDATE', $FML_ALIVE, '$RE_CODE', '$MR_CODE', $SESS_USERID, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);

					$cmd1 = " SELECT FML_ID FROM PER_FAMILY WHERE FML_ID = $MAX_ID "; 
					$count_data = $db_dpis1->send_cmd($cmd1);
					if (!$count_data) {
						echo "$cmd<br>==================<br>";
						$db_dpis->show_error();
						echo "<br>end ". ++$i  ."=======================<br>";
					}
					$MAX_ID++;
					$PER_FAMILY++;    
				}
			}
		} // end while						

		$PER_FAMILY--;
		$cmd = " select count(FML_ID) as COUNT_NEW from PER_FAMILY where PER_ID in (select PER_ID from PER_PERSONAL where PER_TYPE = 2) ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_FAMILY - $PER_FAMILY - $COUNT_NEW<br>";

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_att->free_result();
		$db_att1->free_result();
	} // end if

	if( $command=='FAMILY3' ){
		$cmd = " select max(FML_ID) as MAX_ID from PER_FAMILY ";
		$db_dpis1->send_cmd($cmd);
		$data1 = $db_dpis1->get_array();
		$MAX_ID = $data1[MAX_ID] + 1;

// ครอบครัวลูกจ้างชั่วคราว 3258 
		$cmd = " SELECT ID, FATHER_RANK_CODE, FATHER_FNAME, FATHER_LNAME, MOTHER_RANK_CODE, 
						  MOTHER_FNAME, MOTHER_LNAME, SPOUSE_ID, SPOUSE_FNAME, SPOUSE_LNAME, 
						  MARRIAGE_STATE, LIFE_SPOUSE, SUN_NO, SPOUSE_RANK_CODE, USER_CREATE, 
						  to_char(CREATE_DATE,'yyyy-mm-dd') as CREATE_DATE, USER_UPDATE, 
						  to_char(UPDATE_DATE,'yyyy-mm-dd') as UPDATE_DATE
						  FROM HR_PERSONAL_EMPTEMP_FAMILY 
						  ORDER BY ID ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		$PER_FAMILY = 0;
		while($data = $db_att->get_array()){
			$PER_CARDNO = trim($data[ID]);
			$cmd = " select PER_ID, PER_GENDER from PER_PERSONAL where PER_CARDNO = '$PER_CARDNO' ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			$data_dpis = $db_dpis->get_array();
			$PER_ID = $data_dpis[PER_ID] + 0;

			if ($PER_ID > 0) {
				$PER_GENDER = $data_dpis[PER_GENDER] + 0;

				$PN_CODE = trim($data[FATHER_RANK_CODE]); 
				$FATHER_FNAME = trim($data[FATHER_FNAME]);
				$FATHER_LNAME = trim($data[FATHER_LNAME]);
				if ($FATHER_FNAME || $FATHER_LNAME) {
					$FML_GENDER = 1;
					$FML_TYPE = 1;
					$FML_ALIVE = 1;

					$UPDATE_USER = 99999;
					if ($data[USER_UPDATE]) $USER_UPDATE = trim($data[USER_UPDATE]);
					else $USER_UPDATE = trim($data[USER_CREATE]);
					if ($data[UPDATE_DATE]) $UPDATE_DATE = trim($data[UPDATE_DATE]);
					else  $UPDATE_DATE = trim($data[CREATE_DATE]);
					if ($USER_UPDATE) {
						$cmd = " select ID from USER_DETAIL where USERNAME = '$USER_UPDATE' ";
						$db_dpis2->send_cmd($cmd);
		//				$db_dpis2->show_error();
						$data2 = $db_dpis2->get_array();
						if ($data2[ID]) $UPDATE_USER = $data2[ID];
					}
					if (!$UPDATE_DATE) $UPDATE_DATE = date("Y-m-d H:i:s");

					$cmd = " insert into PER_FAMILY (FML_ID, PER_ID, FML_TYPE, PN_CODE, FML_NAME, FML_SURNAME, FML_CARDNO, 
									FML_GENDER, FML_BIRTHDATE,	FML_ALIVE, RE_CODE, MR_CODE, UPDATE_USER, UPDATE_DATE)
									values ($MAX_ID, $PER_ID, $FML_TYPE, '$PN_CODE', '$FATHER_FNAME', '$FATHER_LNAME',  '$FML_CARDNO', 
									$FML_GENDER, '$FML_BIRTHDATE', $FML_ALIVE, '$RE_CODE', '$MR_CODE', $SESS_USERID, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);

					$cmd1 = " SELECT FML_ID FROM PER_FAMILY WHERE FML_ID = $MAX_ID "; 
					$count_data = $db_dpis1->send_cmd($cmd1);
					if (!$count_data) {
						echo "$cmd<br>==================<br>";
						$db_dpis->show_error();
						echo "<br>end ". ++$i  ."=======================<br>";
					}
					$MAX_ID++;
					$PER_FAMILY++;    
				}

				$PN_CODE = trim($data[MOTHER_RANK_CODE]); 
				$MOTHER_FNAME = trim($data[MOTHER_FNAME]);
				$MOTHER_LNAME = trim($data[MOTHER_LNAME]);
				$FML_GENDER = 2;
				$FML_TYPE = 2;
				$FML_ALIVE = 1;

				if ($MOTHER_FNAME || $MOTHER_LNAME) {
					$cmd = " insert into PER_FAMILY (FML_ID, PER_ID, FML_TYPE, PN_CODE, FML_NAME, FML_SURNAME, FML_CARDNO, 
									FML_GENDER, FML_BIRTHDATE,	FML_ALIVE, RE_CODE, MR_CODE, UPDATE_USER, UPDATE_DATE)
									values ($MAX_ID, $PER_ID, $FML_TYPE, '$PN_CODE', '$MOTHER_FNAME', '$MOTHER_LNAME',  '$FML_CARDNO', 
									$FML_GENDER, '$FML_BIRTHDATE', $FML_ALIVE, '$RE_CODE', '$MR_CODE', $SESS_USERID, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);

					$cmd1 = " SELECT FML_ID FROM PER_FAMILY WHERE FML_ID = $MAX_ID "; 
					$count_data = $db_dpis1->send_cmd($cmd1);
					if (!$count_data) {
						echo "$cmd<br>==================<br>";
						$db_dpis->show_error();
						echo "<br>end ". ++$i  ."=======================<br>";
					}
					$MAX_ID++;
					$PER_FAMILY++;    
				}

				$PN_CODE = trim($data[SPOUSE_RANK_CODE]); 
				$SPOUSE_FNAME = trim($data[SPOUSE_FNAME]);
				$SPOUSE_LNAME = trim($data[SPOUSE_LNAME]);
				if ($PER_GENDER == 2) $FML_GENDER = 1;
				elseif ($PER_GENDER == 1) $FML_GENDER = 2;
				$FML_TYPE = 3;
				$FML_ALIVE = 1;

				if ($SPOUSE_FNAME || $SPOUSE_LNAME) {
					$cmd = " insert into PER_FAMILY (FML_ID, PER_ID, FML_TYPE, PN_CODE, FML_NAME, FML_SURNAME, FML_CARDNO, 
									FML_GENDER, FML_BIRTHDATE,	FML_ALIVE, RE_CODE, MR_CODE, UPDATE_USER, UPDATE_DATE)
									values ($MAX_ID, $PER_ID, $FML_TYPE, '$PN_CODE', '$SPOUSE_FNAME', '$SPOUSE_LNAME',  '$FML_CARDNO', 
									$FML_GENDER, '$FML_BIRTHDATE', $FML_ALIVE, '$RE_CODE', '$MR_CODE', $SESS_USERID, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);

					$cmd1 = " SELECT FML_ID FROM PER_FAMILY WHERE FML_ID = $MAX_ID "; 
					$count_data = $db_dpis1->send_cmd($cmd1);
					if (!$count_data) {
						echo "$cmd<br>==================<br>";
						$db_dpis->show_error();
						echo "<br>end ". ++$i  ."=======================<br>";
					}
					$MAX_ID++;
					$PER_FAMILY++;    
				}
			}
		} // end while						

		$PER_FAMILY--;
		$cmd = " select count(FML_ID) as COUNT_NEW from PER_FAMILY where PER_ID in (select PER_ID from PER_PERSONAL where PER_TYPE = 4) ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_FAMILY - $PER_FAMILY - $COUNT_NEW<br>";

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_att->free_result();
		$db_att1->free_result(); 
	} // end if

	if( $command=='CHILD' ){
// บุตร 
// บุตรข้าราชการ 8714 
		$cmd = " delete from PER_FAMILY where FML_TYPE = 4 ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();

		$cmd = " select max(FML_ID) as MAX_ID from PER_FAMILY ";
		$db_dpis1->send_cmd($cmd);
		$data1 = $db_dpis1->get_array();
		$MAX_ID = $data1[MAX_ID] + 1;

		$cmd = " SELECT ID, SEQ, RANK_CODE, CHILD_FNAME, CHILD_LNAME, to_char(BIRTH_DATE,'yyyy-mm-dd') as BIRTH_DATE, CHILD_SEX, 
						  CHILD_CATEGORY, ADOPTED_DATE, STUDY_STATUS, LIFE_STATUS, USER_CREATE, 
						  to_char(CREATE_DATE,'yyyy-mm-dd') as CREATE_DATE, USER_UPDATE, 
						  to_char(UPDATE_DATE,'yyyy-mm-dd') as UPDATE_DATE
						  FROM HR_CHILD_DEPENDENT 
						  ORDER BY ID ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		$PER_FAMILY = 0;
		while($data = $db_att->get_array()){
			$PER_CARDNO = trim($data[ID]);
			$cmd = " select PER_ID from PER_PERSONAL where PER_CARDNO = '$PER_CARDNO' ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			$data_dpis = $db_dpis->get_array();
			$PER_ID = $data_dpis[PER_ID] + 0;

			if ($PER_ID > 0) {
				$FML_TYPE = 4;
				$PN_CODE = trim($data[RANK_CODE]); 
				$FML_NAME = trim($data[CHILD_FNAME]);
				$FML_SURNAME = trim($data[CHILD_LNAME]);
				if ($FML_NAME || $FML_SURNAME) {
					$FML_BIRTHDATE = trim($data[BIRTH_DATE]);
					$FML_GENDER = trim($data[CHILD_SEX]);
					$FML_ALIVE = trim($data[LIFE_STATUS]);
					if (!$FML_GENDER) $FML_GENDER = 0;
					if (!$FML_ALIVE) $FML_ALIVE = 1;

					$UPDATE_USER = 99999;
					if ($data[USER_UPDATE]) $USER_UPDATE = trim($data[USER_UPDATE]);
					else $USER_UPDATE = trim($data[USER_CREATE]);
					if ($data[UPDATE_DATE]) $UPDATE_DATE = trim($data[UPDATE_DATE]);
					else  $UPDATE_DATE = trim($data[CREATE_DATE]);
					if ($USER_UPDATE) {
						$cmd = " select ID from USER_DETAIL where USERNAME = '$USER_UPDATE' ";
						$db_dpis2->send_cmd($cmd);
		//				$db_dpis2->show_error();
						$data2 = $db_dpis2->get_array();
						if ($data2[ID]) $UPDATE_USER = $data2[ID];
					}
					if (!$UPDATE_DATE) $UPDATE_DATE = date("Y-m-d H:i:s");

					$cmd = " insert into PER_FAMILY (FML_ID, PER_ID, FML_TYPE, PN_CODE, FML_NAME, FML_SURNAME, FML_CARDNO, 
									FML_GENDER, FML_BIRTHDATE,	FML_ALIVE, RE_CODE, MR_CODE, UPDATE_USER, UPDATE_DATE)
									values ($MAX_ID, $PER_ID, $FML_TYPE, '$PN_CODE', '$FML_NAME', '$FML_SURNAME',  '$FML_CARDNO', 
									$FML_GENDER, '$FML_BIRTHDATE', $FML_ALIVE, '$RE_CODE', '$MR_CODE', $SESS_USERID, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);

					$cmd1 = " SELECT FML_ID FROM PER_FAMILY WHERE FML_ID = $MAX_ID "; 
					$count_data = $db_dpis1->send_cmd($cmd1);
					if (!$count_data) {
						echo "$cmd<br>==================<br>";
						$db_dpis->show_error();
						echo "<br>end ". ++$i  ."=======================<br>";
					}
					$MAX_ID++; 
					$PER_FAMILY++; 
				}
			}
		} // end while						

		$cmd = " select count(FML_ID) as COUNT_NEW from PER_FAMILY where FML_TYPE = 4 ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_FAMILY - $PER_FAMILY - $COUNT_NEW<br>";

// บุตรลูกจ้างประจำ 11010 
		$cmd = " SELECT ID, SEQ, RANK_CODE, CHILD_FNAME, CHILD_LNAME, to_char(BIRTH_DATE,'yyyy-mm-dd') as BIRTH_DATE, CHILD_SEX, 
						  CHILD_CATEGORY, ADOPTED_DATE, STUDY_STATUS, LIFE_STATUS, USER_CREATE, 
						  to_char(CREATE_DATE,'yyyy-mm-dd') as CREATE_DATE, USER_UPDATE, 
						  to_char(UPDATE_DATE,'yyyy-mm-dd') as UPDATE_DATE
						  FROM HR_CHILD_EMP 
						  ORDER BY ID ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		while($data = $db_att->get_array()){
			$PER_CARDNO = trim($data[ID]);
			$cmd = " select PER_ID from PER_PERSONAL where PER_CARDNO = '$PER_CARDNO' ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			$data_dpis = $db_dpis->get_array();
			$PER_ID = $data_dpis[PER_ID] + 0;

			if ($PER_ID > 0) {
				$FML_TYPE = 4;
				$PN_CODE = trim($data[RANK_CODE]); 
				$FML_NAME = trim($data[CHILD_FNAME]);
				$FML_SURNAME = trim($data[CHILD_LNAME]);
				if ($FML_NAME || $FML_SURNAME) {
					$FML_BIRTHDATE = trim($data[BIRTH_DATE]);
					$FML_GENDER = trim($data[CHILD_SEX]);
					$FML_ALIVE = trim($data[LIFE_STATUS]);
					if (!$FML_GENDER) $FML_GENDER = 0;
					if (!$FML_ALIVE) $FML_ALIVE = 1;

					$UPDATE_USER = 99999;
					if ($data[USER_UPDATE]) $USER_UPDATE = trim($data[USER_UPDATE]);
					else $USER_UPDATE = trim($data[USER_CREATE]);
					if ($data[UPDATE_DATE]) $UPDATE_DATE = trim($data[UPDATE_DATE]);
					else  $UPDATE_DATE = trim($data[CREATE_DATE]);
					if ($USER_UPDATE) {
						$cmd = " select ID from USER_DETAIL where USERNAME = '$USER_UPDATE' ";
						$db_dpis2->send_cmd($cmd);
		//				$db_dpis2->show_error();
						$data2 = $db_dpis2->get_array();
						if ($data2[ID]) $UPDATE_USER = $data2[ID];
					}
					if (!$UPDATE_DATE) $UPDATE_DATE = date("Y-m-d H:i:s");

					$cmd = " insert into PER_FAMILY (FML_ID, PER_ID, FML_TYPE, PN_CODE, FML_NAME, FML_SURNAME, FML_CARDNO, 
									FML_GENDER, FML_BIRTHDATE,	FML_ALIVE, RE_CODE, MR_CODE, UPDATE_USER, UPDATE_DATE)
									values ($MAX_ID, $PER_ID, $FML_TYPE, '$PN_CODE', '$FML_NAME', '$FML_SURNAME',  '$FML_CARDNO', 
									$FML_GENDER, '$FML_BIRTHDATE', $FML_ALIVE, '$RE_CODE', '$MR_CODE', $SESS_USERID, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);

					$cmd1 = " SELECT FML_ID FROM PER_FAMILY WHERE FML_ID = $MAX_ID "; 
					$count_data = $db_dpis1->send_cmd($cmd1);
					if (!$count_data) {
						echo "$cmd<br>==================<br>";
						$db_dpis->show_error();
						echo "<br>end ". ++$i  ."=======================<br>";
					}
					$MAX_ID++; 
					$PER_FAMILY++; 
				}
			}
		} // end while						

		$cmd = " select count(FML_ID) as COUNT_NEW from PER_FAMILY where FML_TYPE = 4 ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_FAMILY - $PER_FAMILY - $COUNT_NEW<br>";

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_att->free_result();
		$db_att1->free_result(); 
	} // end if

	if( $command=='MOVMENT' ){
// ประเภทการเคลื่อนไหว 72 ok *************************************************************************
		$UPDATE_USER = 98765;
		$cmd = " delete from per_movment where update_user = $UPDATE_USER ";
		//$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " delete from per_map_code where map_code = 'PER_MOVMENT' ";
		//$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " SELECT DISTINCT MVMENT_CODE, MVMENT_OF FROM HR_POSITION_OFFICER 
						  WHERE MVMENT_OF IS NOT NULL ORDER BY MVMENT_CODE, MVMENT_OF ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		while($data = $db_att->get_array()){
			$MOV_CODE = trim($data[MVMENT_CODE]);
			$MOV_NAME = trim($data[MVMENT_OF]);
			$MOV_TYPE = 1;
			$MOV_SUB_TYPE = "NULL";
			if ($MOV_CODE=="007" || $MOV_CODE=="012" || $MOV_CODE=="013" || $MOV_CODE=="014" || $MOV_CODE=="015" || $MOV_CODE=="016" || 
				$MOV_CODE=="026" || $MOV_CODE=="028" || $MOV_CODE=="030" || $MOV_CODE=="031" || $MOV_CODE=="032" || $MOV_CODE=="033" || 
				$MOV_CODE=="034" || $MOV_CODE=="035" || $MOV_CODE=="036" || $MOV_CODE=="044" || $MOV_CODE=="048" || $MOV_CODE=="050" || 
				$MOV_CODE=="051" || $MOV_CODE=="052" || $MOV_CODE=="053" || $MOV_CODE=="059" || $MOV_CODE=="060" || $MOV_CODE=="061") 
				{ $MOV_TYPE = 2;  $MOV_SUB_TYPE = 4; }

			$cmd = " SELECT MOV_CODE FROM PER_MOVMENT WHERE MOV_NAME = '$MOV_NAME' ";
			$count_data = $db_dpis->send_cmd($cmd);
			if ($count_data) {
				$data_dpis = $db_dpis->get_array();
				echo "$MOV_CODE - $MOV_NAME - $data_dpis[MOV_CODE]<br>";
				$MAP_MOV_CODE = $data_dpis[MOV_CODE];

				$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
								VALUES ('PER_MOVMENT', '$MAP_MOV_CODE', '$MOV_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
	//			$db_dpis->show_error();
	//			echo "$cmd<br>";
			} else {
				$cmd = " INSERT INTO PER_MOVMENT(MOV_CODE, MOV_NAME, MOV_TYPE, MOV_SUB_TYPE, MOV_ACTIVE, UPDATE_USER, UPDATE_DATE)
								  VALUES ('$MOV_CODE', '$MOV_NAME', $MOV_TYPE, $MOV_SUB_TYPE, 1, $UPDATE_USER, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
//				echo "<br>";
			}
		} // end while			

		$cmd = " SELECT DISTINCT MOVEMENT_CODE, MOVEMENT_NAME FROM HR_POSITION_EMP 
						  WHERE MOVEMENT_NAME IS NOT NULL ORDER BY MOVEMENT_CODE, MOVEMENT_NAME ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		while($data = $db_att->get_array()){
			$MOV_CODE = trim($data[MOVEMENT_CODE]);
			$MOV_NAME = trim($data[MOVEMENT_NAME]);
			$MOV_TYPE = 1;
			$MOV_SUB_TYPE = "NULL";
			if ($MOV_CODE=="007" || $MOV_CODE=="008" || $MOV_CODE=="009" || $MOV_CODE=="010" || $MOV_CODE=="020" || $MOV_CODE=="021" || 
				$MOV_CODE=="022" || $MOV_CODE=="025" || $MOV_CODE=="026" || $MOV_CODE=="027" || $MOV_CODE=="028") 
				{ $MOV_TYPE = 2;  $MOV_SUB_TYPE = 4; }

			$cmd = " SELECT MOV_CODE FROM PER_MOVMENT WHERE MOV_NAME = '$MOV_NAME' ";
			$count_data = $db_dpis->send_cmd($cmd);
			if ($count_data) {
				$data_dpis = $db_dpis->get_array();
				echo "$MOV_CODE - $MOV_NAME - $data_dpis[MOV_CODE]<br>";
				$MAP_MOV_CODE = $data_dpis[MOV_CODE];

				$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
								VALUES ('PER_MOVMENT_EMP', '$MAP_MOV_CODE', '$MOV_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
	//			$db_dpis->show_error();
	//			echo "$cmd<br>";
			} else {
				$cmd = " INSERT INTO PER_MOVMENT(MOV_CODE, MOV_NAME, MOV_TYPE, MOV_SUB_TYPE, MOV_ACTIVE, UPDATE_USER, UPDATE_DATE)
								  VALUES ('20$MOV_CODE', '$MOV_NAME', $MOV_TYPE, $MOV_SUB_TYPE, 1, $UPDATE_USER, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
				echo "$cmd <br>";
			}
		} // end while			

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_att->free_result();
		$db_att1->free_result();
	} // end if
		
	if( $command=='POSITIONHIS' ){
// การดำรงตำแหน่ง 1865423
// หลังจากลบ + ครู 1175046 
//84833 - 84833
//79873 - 164840
//76464 - 241385
//70698 - 312485

// delete from HR_POSITION_OFFICER where ID not in (select ID from HR_PERSONAL_OFFICER)
// update HR_POSITION_OFFICER set WORK_LINE_CODE = NULL where WORK_LINE_CODE not in (select WORK_LINE_CODE from HR_WORK_LINE_CODE)
// update HR_POSITION_OFFICER set ADMIN_CODE = NULL where ADMIN_CODE not in (select ADMIN_CODE from HR_ADMIN_CODE)
// update HR_POSITION_OFFICER set FLAG_TO_NAME_CODE = NULL where FLAG_TO_NAME_CODE not in (select FLAG_TO_NAME_CODE from HR_FLAG_TO_NAME)
// select  distinct WORK_LINE_CODE, WORK_LINE_NAME from HR_POSITION_OFFICER minus
// select WORK_LINE_CODE, WORK_LINE_NAME from HR_WORK_LINE_CODE

// select FLAG_TO_NAME_CODE, count(*) from hr_position_officer group by FLAG_TO_NAME_CODE order by to_number(FLAG_TO_NAME_CODE)
// 0	843
// 1	45378
// 2	357
// 3	4171
// 4	4053
// 5	3723
// 6	95085
// 7	74
// 8	7390
// 9	1147
// 10	136705
// 11	178
// 12	6840
// 13	8
// 14	959540
// 15	5672
// 16	4128
// 17	2089
// 18	10
// 19	1783
// 20	44125
// 21	1
// 22	3
// 23	8706
// 24	43
// 25	1280
// 26	70
// 27	5946
// 28	14
// 29	28
// 30	568
// 31	1
// 32	2069
// 33	7
// 34	598
// 35	1017
// 36	41
// 37	293
// 38	1859
// 39	192
// 40	20043
// 43	203
// 44	55608
// 45	159
// 46	276
// 47	4
// 49	20
// 50	614
// 51	7330
// 52	3570
// 53	63462
// 54	709
// 55	78
// 56	105
// 57	13
// 58	103
// 59	9
// 61	2
// 62	1
// 63	313
// 64	499
// 65	76
// 66	488
// 67	35
// 68	40
// 	288456

		$cmd = " truncate table per_positionhis ";
		//$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " truncate table per_salaryhis ";
		//$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " select count(PER_ID) as CNT_POH_ID from PER_POSITIONHIS ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$CNT_POH_ID = $data2[CNT_POH_ID] + 0;

		$cmd = " select count(PER_ID) as CNT_SAH_ID from PER_SALARYHIS ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$CNT_SAH_ID = $data2[CNT_SAH_ID] + 0;

		$cmd = " select max(POH_ID) as MAX_ID from PER_POSITIONHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_POH_ID = $data[MAX_ID] + 1;

		$cmd = " select max(SAH_ID) as MAX_ID from PER_SALARYHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_SAH_ID = $data[MAX_ID] + 1;

		$cmd = " SELECT a.ID, a.FLAG_PERSON_TYPE, ORDER_MOVE_POSITION, a.POS_NUM_CODE_SIT, a.POS_NUM_CODE_SIT_ABB, 
                        a.MP_COMMAND_NUM, a.CUR_YEAR, a.MP_YEAR, to_char(a.MP_COMMAND_DATE,'yyyy-mm-dd') as MP_COMMAND_DATE, 
                        a.FLAG_TO_NAME_CODE, a.FLAG_TO_NAME, to_char(a.MP_FORCE_DATE,'yyyy-mm-dd') as MP_FORCE_DATE, 
                        to_char(a.MP_POS_DATE,'yyyy-mm-dd') as MP_POS_DATE, a.JOB_CODE, a.JOB_NAME, a.SECTION_CODE, a.SECTION_NAME, 
                        a.DIVISION_CODE, a.DIVISION_NAME, a.DEPARTMENT_CODE, a.DEPARTMENT_NAME, a.POS_NUM_NAME, a.POS_NUM_CODE, 
                        a.WORK_LINE_CODE, a.WORK_LINE_NAME, a.MP_CEE, a.SALARY_LEVEL_CODE, a.SALARY, a.ADMIN_CODE, a.ADMIN_NAME, 
                        a.SALARY_POS_CODE_1, a.SALARY_POS_ABB_NAME_1, a.SAL_POS_AMOUNT_1, a.SALARY_POS_CODE, a.SALARY_POS_ABB_NAME, 
                        a.SAL_POS_AMOUNT_2, a.SPECIALIST_CODE, FILL_APP_FLAG, FLAG_POS_STATUS, a.MP_FLAG, UP_C_FLAG, 
                        MP_FLAG_CURRENT, REMARK, AUDIT_FLAG, USER_AUDIT, to_char(AUDIT_DATE,'yyyy-mm-dd') as AUDIT_DATE, 
                        a.MP_FLAG_1, COURSE_CODE, COURSE_NAME, PLACE_NAME, PROVINCE_CODE, COUNTRY_CODE, 
                        a.POS_NUM_CODE_SIT_CODE, a.POSITION_CATG, REC_STATUS, SPECIALIST_NAME, MVMENT_CODE, MVMENT_OF, 
                        a.SPECIAL_AMT, POS_NUM_CODE_SIT_EDIT, POS_NUM_CODE_SIT_ABB_EDIT, POS_NUM_CODE_SIT_CODE_EDIT, 
                        MP_COMMAND_NUM_EDIT, CUR_YEAR_EDIT, FLAG_TO_NAME_CODE_E, FLAG_TO_NAME_E, 
                        to_char(MP_COMMAND_DATE_EDIT,'yyyy-mm-dd') as MP_COMMAND_DATE_EDIT, a.MP_CEE_CODE, a.SALARY_LEVEL, 
                        a.GROUPWORK_CODE, GROUPWORK_NAME, ACTIVE_STATUS, a.USER_CREATE, 
                        to_char(a.CREATE_DATE,'yyyy-mm-dd') as CREATE_DATE, a.USER_UPDATE,     to_char(a.UPDATE_DATE,'yyyy-mm-dd') as UPDATE_DATE 
                        FROM HR_POSITION_OFFICER a
						WHERE a.ID >= '3750000000000' AND FLAG_PERSON_TYPE != '3'  
                        ORDER BY ID, FLAG_PERSON_TYPE, ORDER_MOVE_POSITION ";

// 			WHERE a.ID < '3100300000000' AND FLAG_PERSON_TYPE != '3'  
// 			WHERE a.ID >= '3100300000000' AND a.ID < '3100602000000' AND FLAG_PERSON_TYPE != '3'  
// 			WHERE a.ID >= '3100602000000' AND a.ID < '3101100000000' AND FLAG_PERSON_TYPE != '3'  
// 			WHERE a.ID >= '3101100000000' AND a.ID < '3101600000000' AND FLAG_PERSON_TYPE != '3'  
// 			WHERE a.ID >= '3101600000000' AND a.ID < '3102000000000' AND FLAG_PERSON_TYPE != '3'  
// 			WHERE a.ID >= '3102000000000' AND a.ID < '3102200000000' AND FLAG_PERSON_TYPE != '3'  
// 			WHERE a.ID >= '3102200000000' AND a.ID < '3120100500000' AND FLAG_PERSON_TYPE != '3'  
// 			WHERE a.ID >= '3120100500000' AND a.ID < '3205000000000' AND FLAG_PERSON_TYPE != '3'  
// 			WHERE a.ID >= '3205000000000' AND a.ID < '3470000000000' AND FLAG_PERSON_TYPE != '3'  
// 			WHERE a.ID >= '3470000000000' AND a.ID < '3750000000000' AND FLAG_PERSON_TYPE != '3'  
// 			WHERE a.ID >= '3750000000000' AND FLAG_PERSON_TYPE != '3'  



//	00 01 02 03 04 06 *07* 08 09 10 11 12 13 *14* 15 16 17 18 19 20 2122 23 24 50 xx					  
//						  WHERE FLAG_TO_NAME_CODE in ('1', '58', '55', '3', '50', '13', '17', '28', '6', '12', '18', '11', '59', '21', '33', '29', '22', '31', '10', '32', '54', '36', '56', '38', '15', '5', '7', '37', '40', '27', '64', '57' ,'65', '23', '4', '2', '34', '30', '35', '62') 
// 39 อบรม 45 ดูงาน 47 ขยายเวลาฝึกอบรม 46 ศึกษาต่อ 49 ขยายเวลาศึกษาต่อ 26 รักษาราชการแทน 16 รักษาการ 19 ยกเลิกคำสั่ง 63 มอบหมาย 0 อื่นๆ 9 ช่วยราชการ 68 มอบหมาย
// 61 ลดขั้นเงินเดือน 66 ปรับเงินเดือน 44 ปรับ 20 ตอบแทนพิเศษ 67 ให้เงินเดือนตามวุฒิ 8 ปรับเงินเดือนตามวุฒิ 43 ปรับเงินเดือน 51 ไม่ได้เลื่อนขั้น 56 เลื่อนเงินเดือนและระดับ (2 รายการ) 25 กรณีพิเศษ 14 เลื่อนขั้นเงินเดือน 52, 53 เลื่อนขั้น 24 ค่าครองชีพ
		$db_att->send_cmd($cmd);
//						  WHERE FLAG_TO_NAME_CODE in ('1', '58', '55', '3', '50', '13', '17', '28', '6', '12', '18', '11', '59', '21', '33', '29', '22', '31', '10', '32', '54', '36', '56', '38', '15', '5', '7', '37', '40', '27', '64', '57' ,'65', '23', '4', '2', '34', '30', '35', '62') 
//						  WHERE FLAG_TO_NAME_CODE in ('61', '66', '44', '20', '67', '8', '43', '51', '56', '25', '14', '52', '53', '24')  เงินเดือน
//		$db_att->show_error();
//		echo "<br>";
		while($data = $db_att->get_array()){
			$PER_CARDNO = trim($data[ID]);
			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = '$PER_CARDNO' ";
			$db_dpis->send_cmd($cmd);
			$db_dpis->show_error();
			$data_dpis = $db_dpis->get_array();
			$PER_ID = $data_dpis[NEW_CODE] + 0;

			if ($PER_ID) {
				$EFFECTIVEDATE = trim($data[MP_POS_DATE]);
				if (!$EFFECTIVEDATE) $EFFECTIVEDATE = "-";
				$DOCNO = trim($data[POS_NUM_CODE_SIT_ABB]).trim($data[MP_COMMAND_NUM]);
				$CUR_YEAR = trim($data[CUR_YEAR]);
				if (trim($CUR_YEAR)) $DOCNO .= '/'.trim($CUR_YEAR);
				$DOCDATE = trim($data[MP_COMMAND_DATE]);

				$DOCNO_EDIT = trim($data[POS_NUM_CODE_SIT_ABB_EDIT]).trim($data[MP_COMMAND_NUM_EDIT]);
				$CUR_YEAR_EDIT = trim($data[CUR_YEAR_EDIT]);
				if (trim($CUR_YEAR_EDIT)) $DOCNO_EDIT .= '/'.trim($CUR_YEAR_EDIT);
				$DOCDATE_EDIT = trim($data[MP_COMMAND_DATE_EDIT]);

				if (substr($DOCDATE,0,4)=="0425") $DOCDATE = "1984".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0009") $DOCDATE = "1991".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0008") $DOCDATE = "1992".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0383") $DOCDATE = "1993".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="1822") $DOCDATE = "1993".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0006" && $CUR_YEAR=="2537") $DOCDATE = "1994".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0286" && $CUR_YEAR=="2537") $DOCDATE = "1994".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0357") $DOCDATE = "1994".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0364") $DOCDATE = "1994".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0484") $DOCDATE = "1994".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="1614" && $CUR_YEAR=="2537") $DOCDATE = "1994".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="1814") $DOCDATE = "1994".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="1834") $DOCDATE = "1994".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0285") $DOCDATE = "1995".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0284" && $CUR_YEAR=="2539") $DOCDATE = "1996".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0433" && $CUR_YEAR=="2539") $DOCDATE = "1996".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0486") $DOCDATE = "1996".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0636") $DOCDATE = "1996".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0716") $DOCDATE = "1996".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0996") $DOCDATE = "1996".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="1516") $DOCDATE = "1996".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="1716") $DOCDATE = "1996".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="1816") $DOCDATE = "1996".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0293") $DOCDATE = "1997".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0523") $DOCDATE = "1997".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0607") $DOCDATE = "1997".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0708") $DOCDATE = "1998".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="1608") $DOCDATE = "1998".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="1708" && $CUR_YEAR=="2541") $DOCDATE = "1998".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="1709" && $CUR_YEAR=="2542") $DOCDATE = "1999".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0438") $DOCDATE = "2001".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0499") $DOCDATE = "2001".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0711") $DOCDATE = "2001".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="1711") $DOCDATE = "2001".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0002") $DOCDATE = "2002".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0428") $DOCDATE = "2002".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0437") $DOCDATE = "2002".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0003") $DOCDATE = "2003".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0429") $DOCDATE = "2003".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0433" && $CUR_YEAR=="2546") $DOCDATE = "2003".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0583") $DOCDATE = "2003".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0713") $DOCDATE = "2003".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="1713") $DOCDATE = "2003".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0004") $DOCDATE = "2004".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0286" && $CUR_YEAR=="2547") $DOCDATE = "2004".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="1514") $DOCDATE = "2004".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="1614" && $CUR_YEAR=="2547") $DOCDATE = "2004".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="1714") $DOCDATE = "2004".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0005") $DOCDATE = "2005".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0289" && $CUR_YEAR=="2548") $DOCDATE = "2005".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0295") $DOCDATE = "2005".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0495") $DOCDATE = "2005".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0006" && $CUR_YEAR=="2549") $DOCDATE = "2006".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0284" && $CUR_YEAR=="2549") $DOCDATE = "2006".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0289" && $CUR_YEAR=="2549") $DOCDATE = "2006".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0294") $DOCDATE = "2006".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0494") $DOCDATE = "2006".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0288") $DOCDATE = "2007".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="1707") $DOCDATE = "2007".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0292") $DOCDATE = "2008".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0492") $DOCDATE = "2008".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="1708" && $CUR_YEAR=="2551") $DOCDATE = "2008".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0491") $DOCDATE = "2009".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="1709" && $CUR_YEAR=="2552") $DOCDATE = "2009".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0290") $DOCDATE = "2010".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0490") $DOCDATE = "2010".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="1710") $DOCDATE = "2010".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="2286") $DOCDATE = "1986".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="2546") $DOCDATE = "2003".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="4904") $DOCDATE = "2004".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="5004") $DOCDATE = "2004".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="2305") $DOCDATE = "2005".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="2548") $DOCDATE = "2005".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="2549") $DOCDATE = "2006".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="2550") $DOCDATE = "2007".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="5007") $DOCDATE = "2007".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="3010") $DOCDATE = "2010".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="5010") $DOCDATE = "2010".substr($DOCDATE,4);

				if (substr($EFFECTIVEDATE,0,4)=="0494") $EFFECTIVEDATE = "2006".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0291") $EFFECTIVEDATE = "1977".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1857" && $CUR_YEAR=="2533") $EFFECTIVEDATE = "1990".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1857" && $CUR_YEAR=="2535") $EFFECTIVEDATE = "1992".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1857" && $CUR_YEAR=="2536") $EFFECTIVEDATE = "1993".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1857" && $CUR_YEAR=="2537") $EFFECTIVEDATE = "1994".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1857" && $CUR_YEAR=="2538") $EFFECTIVEDATE = "1995".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1857" && $CUR_YEAR=="2539") $EFFECTIVEDATE = "1996".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1857" && $CUR_YEAR=="2540") $EFFECTIVEDATE = "1997".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1857" && $CUR_YEAR=="2541") $EFFECTIVEDATE = "1998".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1857" && $CUR_YEAR=="2542") $EFFECTIVEDATE = "1999".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0289" && $CUR_YEAR=="2543") $EFFECTIVEDATE = "2000".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0001") $EFFECTIVEDATE = "2001".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1712") $EFFECTIVEDATE = "2002".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0287") $EFFECTIVEDATE = "2003".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0703") $EFFECTIVEDATE = "2003".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1003") $EFFECTIVEDATE = "2003".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1713") $EFFECTIVEDATE = "2003".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0004") $EFFECTIVEDATE = "2004".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0286") $EFFECTIVEDATE = "2004".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0289" && $CUR_YEAR=="2547") $EFFECTIVEDATE = "2004".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0296") $EFFECTIVEDATE = "2004".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1714") $EFFECTIVEDATE = "2004".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0005") $EFFECTIVEDATE = "2005".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1857" && $CUR_YEAR=="2548") $EFFECTIVEDATE = "2005".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0006") $EFFECTIVEDATE = "2006".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0294") $EFFECTIVEDATE = "2006".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1711" && $CUR_YEAR=="2549") $EFFECTIVEDATE = "2006".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0007") $EFFECTIVEDATE = "2007".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0293") $EFFECTIVEDATE = "2007".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1707") $EFFECTIVEDATE = "2007".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0008") $EFFECTIVEDATE = "2008".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0492") $EFFECTIVEDATE = "2008".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1708" && $CUR_YEAR=="2551") $EFFECTIVEDATE = "2008".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0009") $EFFECTIVEDATE = "2009".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0491") $EFFECTIVEDATE = "2009".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1708" && $CUR_YEAR=="2552") $EFFECTIVEDATE = "2009".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1709") $EFFECTIVEDATE = "2009".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0490") $EFFECTIVEDATE = "2010".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1711" && $CUR_YEAR=="2554") $EFFECTIVEDATE = "2011".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="4679") $EFFECTIVEDATE = "1979".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="2546") $EFFECTIVEDATE = "2003".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="5004") $EFFECTIVEDATE = "2004".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="2305") $EFFECTIVEDATE = "2005".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="2548") $EFFECTIVEDATE = "2005".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="2016") $EFFECTIVEDATE = "2006".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="2549") $EFFECTIVEDATE = "2006".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="2550") $EFFECTIVEDATE = "2007".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="4680") $EFFECTIVEDATE = "2010".substr($EFFECTIVEDATE,4); 

				$POS_NO_NAME = trim($data[POS_NUM_NAME]);
				$POS_NO_NAME = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($POS_NO_NAME)));
				$POS_NO_NAME = str_replace("'", "", trim($POS_NO_NAME));
				$POS_NO = trim($data[POS_NUM_CODE]);
				$PL_CODE = trim($data[WORK_LINE_CODE]);
				$PL_NAME = trim($data[WORK_LINE_NAME]);
				if (!$PL_CODE && $PL_NAME) { 
					$cmd = " select PL_CODE from PER_LINE where PL_NAME = '$PL_NAME' ";
					$db_dpis->send_cmd($cmd);
					$data_dpis = $db_dpis->get_array();
					$PL_CODE = trim($data_dpis[PL_CODE]);
				} 
				if ($PL_CODE=="-") $PL_CODE = "";
				if ($PL_CODE=="00000") $PL_CODE = "";
				if ($PL_CODE=="11091") $PL_CODE = "";
				if ($PL_CODE=="204") $PL_CODE = "";
				if ($PL_CODE=="60004") $PL_CODE = "";
				if ($PL_CODE=="60140") $PL_CODE = "";
				if ($PL_CODE=="60604") $PL_CODE = "";
				if ($PL_CODE=="6101") $PL_CODE = "";
				if ($PL_CODE=="6153") $PL_CODE = "";
				if ($PL_CODE=="62204") $PL_CODE = "";
				if ($PL_CODE=="62307") $PL_CODE = "";
				if ($PL_CODE=="6254") $PL_CODE = "";
				if ($PL_CODE=="6301") $PL_CODE = "";
				if ($PL_CODE=="70213") $PL_CODE = "70203";
				if ($PL_CODE=="82403") $PL_CODE = "";
				if ($PL_CODE=="82623") $PL_CODE = "";
				if ($PL_CODE=="82365") $PL_CODE = "";
				if ($PL_CODE=="82695") $PL_CODE = "84624";
				if ($PL_CODE=="82696" || $PL_CODE=="84696") $PL_CODE = "84625";
				if ($PL_CODE=="82697" || $PL_CODE=="84697") $PL_CODE = "84626";
				if ($PL_CODE=="82698" || $PL_CODE=="84698") $PL_CODE = "84627";
				if ($PL_CODE=="กคส01") $PL_CODE = "";
				if ($PL_CODE=="0" && $PL_NAME=="ครู") $PL_CODE = ""; 
				elseif ($PL_CODE=="0" && $PL_NAME=="อาจารย์ 2 รับเงินเดือนในระดับ 7") $PL_CODE = "10035"; 
				elseif ($PL_CODE=="0" && $PL_NAME=="พยาบาลจัตวา") $PL_CODE = ""; 
				elseif ($PL_CODE=="0" && $PL_NAME=="เจ้าหน้าที่บริหารงานการเงินและบัญชี") $PL_CODE = "20435"; 
				elseif ($PL_CODE=="0" && $PL_NAME=="นักบริหารการศึกษา") $PL_CODE = "84504"; 
				elseif ($PL_CODE=="00" && $PL_NAME=="ช่างรังวัด") $PL_CODE = ""; 
				elseif ($PL_CODE=="00" && $PL_NAME=="นักประชาสงเคราะห์") $PL_CODE = "82903"; 
				elseif ($PL_CODE=="00" && $PL_NAME=="พนักงานการเงินและบัญชี จัตวา") $PL_CODE = "42001"; 
				elseif ($PL_CODE=="00" && $PL_NAME=="เจ้าหน้าที่ทะเบียน") $PL_CODE = ""; 
				elseif ($PL_CODE=="00" && $PL_NAME=="เศรษฐกร") $PL_CODE = ""; 
				elseif ($PL_CODE=="00" && $PL_NAME=="ช่างก่อสร้าง") $PL_CODE = ""; 
				elseif ($PL_CODE=="1043" && $PL_NAME=="เจ้าพนักงานปกครอง") $PL_CODE = "10403"; 
				elseif ($PL_CODE=="10910" && $PL_NAME=="เจ้าหน้าที่ปกครอง") $PL_CODE = "41006"; 
				elseif ($PL_CODE=="11890" && $PL_NAME=="เจ้าหน้าที่บันทึกข้อมูล 1-3/4/5") $PL_CODE = "11801"; 
				elseif ($PL_CODE=="11890" && $PL_NAME=="เจ้าหน้าที่บันทึกข้อมูล") $PL_CODE = "11801"; 
				elseif ($PL_CODE=="12320" && $PL_NAME=="เจ้าหน้าที่ปกครอง") $PL_CODE = "41006"; 
				elseif ($PL_CODE=="13730" && $PL_NAME=="เจ้าหน้าที่ปกครอง") $PL_CODE = "41006"; 
				elseif ($PL_CODE=="15450" && $PL_NAME=="เจ้าหน้าที่บันทึกข้อมูล") $PL_CODE = "11801"; 
				elseif ($PL_CODE=="15450" && $PL_NAME=="เจ้าหน้าที่ปกครอง") $PL_CODE = "41006"; 
				elseif ($PL_CODE=="18640" && $PL_NAME=="พนักงานปกครอง") $PL_CODE = "10512"; 
				elseif ($PL_CODE=="18670" && $PL_NAME=="เจ้าพนักงานปกครอง") $PL_CODE = "10403"; 
				elseif ($PL_CODE=="21390" && $PL_NAME=="เจ้าพนักงานปกครอง") $PL_CODE = "10403"; 
				elseif ($PL_CODE=="23490" && $PL_NAME=="เจ้าพนักงานปกครอง") $PL_CODE = "10403"; 
				elseif ($PL_CODE=="23910" && $PL_NAME=="เจ้าพนักงานธุรการ") $PL_CODE = "11612"; 
				elseif ($PL_CODE=="24330" && $PL_NAME=="เจ้าพนักงานปกครอง") $PL_CODE = "10403"; 
				elseif ($PL_CODE=="31750" && $PL_NAME=="เจ้าพนักงานปกครอง") $PL_CODE = "31006"; 
				elseif ($PL_CODE=="32250" && $PL_NAME=="เจ้าหน้าที่บริหารงานโยธา") $PL_CODE = "75505"; 
				elseif ($PL_CODE=="8852" && $PL_NAME=="อาจารย์ 1") $PL_CODE = "10023"; 
				elseif ($PL_CODE=="90000" && $PL_NAME=="เจ้าหน้าที่บริหารงานคลัง") $PL_CODE = "20224"; 
				elseif ($PL_CODE=="9100" && $PL_NAME=="เจ้าหน้าที่ปกครอง") $PL_CODE = "41006"; 
				elseif ($PL_CODE=="999" && $PL_NAME=="นักเทคนิคการแพทย์") $PL_CODE = "60503"; 
				elseif ($PL_CODE=="999" && $PL_NAME=="ดุริยางคศิลปิน") $PL_CODE = "82201"; 
				elseif ($PL_CODE=="999" && $PL_NAME=="xx") $PL_CODE = ""; 
				elseif ($PL_CODE=="999" && $PL_NAME=="เจ้าพนักงานพัสดุ") $PL_CODE = "11712"; 
				elseif ($PL_CODE=="999" && $PL_NAME=="ผู้ช่วยผู้อำนวยการโรงเรียน") $PL_CODE = "20046"; 
				elseif ($PL_CODE=="999" && $PL_NAME=="อาจารย์ 1") $PL_CODE = "10023"; 
				elseif ($PL_CODE=="999" && $PL_NAME=="ผู้อำนวยการโรงเรียน") $PL_CODE = "20067"; 
				elseif ($PL_CODE=="999" && $PL_NAME=="อาจารย์ใหญ่") $PL_CODE = "20036"; 
				elseif ($PL_CODE=="999" && $PL_NAME=="ผู้ช่วยอาจารย์ใหญ่") $PL_CODE = "20025"; 
				elseif ($PL_CODE=="999" && $PL_NAME=="อาจารย์ 2") $PL_CODE = "10035"; 

				$PM_CODE = trim($data[ADMIN_CODE]);
				$PM_NAME = trim($data[ADMIN_NAME]);
				if ($PM_CODE=="00000") $PM_CODE = "";
				if (!$PM_CODE && $PM_NAME) { 
					$cmd = " select PM_CODE from PER_MGT where PM_NAME = '$PM_NAME' ";
					$db_dpis->send_cmd($cmd);
					$data_dpis = $db_dpis->get_array();
					$PM_CODE = trim($data_dpis[PM_CODE]);
				} 
				if ($PM_CODE=="0") $PM_CODE = "";
				if ($PM_CODE=="0" && $PM_NAME=="ผู้ช่วยศึกษาธิการ") $PM_CODE = "50031";
				if ($PM_CODE=="20214") $PM_CODE = "";
				if ($PM_CODE=="ั") $PM_CODE = "";
				if ($PM_CODE=="60011") $PM_CODE = "";
				if ($PM_CODE=="้") $PM_CODE = "";

				$POH_ORG1 = "กรุงเทพมหานคร";
				$POH_ORG2 = trim($data[DEPARTMENT_NAME]);
				$POH_ORG2 = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($POH_ORG2)));
				$POH_ORG2 = str_replace("'", "", trim($POH_ORG2));
				$POH_ORG3 = trim($data[DIVISION_NAME]);
				$POH_ORG3 = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($POH_ORG3)));
				$POH_ORG3 = str_replace("'", "", trim($POH_ORG3));
				if (strpos($POH_ORG3,"สำนักงานเขต") !== false && !$POH_ORG2) $POH_ORG2 = "สำนักงานเขต";
				$POH_UNDER_ORG1 = trim($data[SECTION_NAME]);
				$POH_UNDER_ORG1 = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($POH_UNDER_ORG1)));
				$POH_UNDER_ORG1 = str_replace("'", "", trim($POH_UNDER_ORG1));
				$POH_UNDER_ORG2 = trim($data[JOB_NAME]);
				$POH_UNDER_ORG2 = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($POH_UNDER_ORG2)));
				$POH_UNDER_ORG2 = str_replace("'", "", trim($POH_UNDER_ORG2));
				if ($POH_ORG2=="สำนักงานเขต" && $POH_UNDER_ORG1=="โรงเรียน")
					$POH_ORG = trim((($POH_UNDER_ORG2=="-") ? "":$POH_UNDER_ORG2)." ".(($POH_ORG3=="-") ? "":$POH_ORG3));
				elseif ($POH_ORG2=="สำนักงานเขต") 
					$POH_ORG = trim((($POH_UNDER_ORG2=="-") ? "":$POH_UNDER_ORG2)." ".(($POH_UNDER_ORG1=="-") ? "":$POH_UNDER_ORG1)." ".
												(($POH_ORG3=="-") ? "":$POH_ORG3));
				else
					$POH_ORG = trim((($POH_UNDER_ORG2=="-") ? "":$POH_UNDER_ORG2)." ".(($POH_UNDER_ORG1=="-") ? "":$POH_UNDER_ORG1)." ".
												(($POH_ORG3=="-") ? "":$POH_ORG3)." ".$POH_ORG2);
				$SALARY = $data[SALARY];
				$SAH_SALARY_EXTRA = $data[SPECIAL_AMT];
				$POH_SALARY_POS = $data[SAL_POS_AMOUNT_2] + 0;
				if ($POH_SALARY_POS==0) $POH_SALARY_POS = $data[SAL_POS_AMOUNT_1] + 0;
				$FLAG_TO_NAME = trim($data[FLAG_TO_NAME]);
				$SPECIALIST_NAME = trim($data[SPECIALIST_NAME]);
				$REMARK = trim($data[REMARK]);
				$FLAG_TO_NAME_CODE = trim($data[FLAG_TO_NAME_CODE]);
				$MOV_CODE = trim($data[MVMENT_CODE]);

				$cmd = " select MOV_CODE, MOV_TYPE from PER_MOVMENT where MOV_NAME = '$FLAG_TO_NAME' ";
				$db_dpis->send_cmd($cmd);
	//				$db_dpis->show_error();
				$data_dpis = $db_dpis->get_array();
				$MOV_CODE = trim($data_dpis[MOV_CODE]);
				$MOV_TYPE = trim($data_dpis[MOV_TYPE]);
				if (!$MOV_CODE) { 
					if ($FLAG_TO_NAME_CODE) {
						$MOV_CODE = $FLAG_TO_NAME_CODE;
						if ($MOV_CODE=="00") $MOV_CODE = "43";
					} else { 
						if ($FLAG_TO_NAME=="แต่งตั้ง (ย้าย)"|| $FLAG_TO_NAME=="แต่งตั้ง ( ย้ายสับเปลี่ยน )") $MOV_CODE = "6"; 
						elseif ($FLAG_TO_NAME=="อื่นๆ") $MOV_CODE = "0"; 
						elseif ($FLAG_TO_NAME=="ปรับอัตราเงินเดือนตามบัญชีอัตราเงินเดือนใหม่ ท้าย พ.ร.บ. เงินเดือนและเงินประจำตำ") $MOV_CODE = "43"; 
						elseif ($FLAG_TO_NAME=="รับโอนข้าราชการตามกฎหมายอื่น") $MOV_CODE = "4"; 
						elseif ($FLAG_TO_NAME=="รับโอนข้าราชการตามกฎหมายอื่น ผู้สอบแข่งขันได้") $MOV_CODE = "32"; 
						else { 
							$MOV_CODE = "0"; 
							if ($FLAG_TO_NAME) echo "ประเภทการเคลื่อนไหว->$FLAG_TO_NAME_CODE - $FLAG_TO_NAME<br>"; 
						}
					}
				}

				if (!$MOV_TYPE) { 
					$cmd = " select MOV_TYPE from PER_MOVMENT where MOV_CODE = '$MOV_CODE' ";
					$db_dpis->send_cmd($cmd);
		//				$db_dpis->show_error();
					$data_dpis = $db_dpis->get_array();
					$MOV_TYPE = trim($data_dpis[MOV_TYPE]);
				}

				$SALARYHIS = $POSITIONHIS = "";		
				$EX_CODE = "024";
				if ($MOV_TYPE==2)
					$SALARYHIS = 1;
				else
					$POSITIONHIS = 1;		

				$SM_CODE = "";
				if ($REMARK=="เลื่อนขั้น 0.5 ขั้น" || $REMARK=="ปรับครึ่งขั้น" || $REMARK=="ปรับ0.5ขั้น" || $REMARK=="ปรับ0.5 ขั้น" || $REMARK=="ปรับ 0.5ขั้น" || 
					$REMARK=="ปรับ 0.5 ขั้น" || $REMARK=="ปรับ .5ขั้น" || $REMARK=="ปรับ .5 ขั้น" || $REMARK=="ปรับ  0.5 ขั้น" || $REMARK=="ปรับ  0.5  ขั้น") 
					$SM_CODE = "2";
				elseif ($REMARK=="หนึ่งขั้น" || $REMARK=="เลื่อนหนึ่งขั้น" || $REMARK=="เลื่อนขั้น 1 ขั้น" || $REMARK=="ปรับหนึ่งขั้น" || $REMARK=="ปรับ1ขั้น" || 
					$REMARK=="ปรับ1 ขั้น" || $REMARK=="ปรับ 1ขั้น" || $REMARK=="ปรับ 1 ขั้น" || $REMARK=="ปรับ 1.0ขั้น" || $REMARK=="ปรับ 1.0 ขั้น" || 
					$REMARK=="ปรับ  1 ขั้น" || $REMARK=="ปรับ  1  ขั้น") 
					$SM_CODE = "3";
				elseif ($REMARK=="หนึ่งขั้นครึ่ง" || $REMARK=="เลื่อนขั้น 1.5 ขั้น" || $REMARK=="ปรับหนึ่งขั้นครึ่ง" || $REMARK=="ปรับ1.5ขั้น" || $REMARK=="ปรับ 1.5ขั้น" || 
					$REMARK=="ปรับ 1.5 ขั้น") 
					$SM_CODE = "4";
				elseif ($REMARK=="สองขั้น" || $REMARK=="เลื่อนขั้น 2 ขั้น" || $REMARK=="ปรับสองขั้น" || $REMARK=="ปรับ 2 ขั้น" || $REMARK=="ปรับ 2.0 ขั้น" || 
					$REMARK=="ปรับ  2  ขั้น" ) 
					$SM_CODE = "5";
				elseif ($REMARK=="ไม่ได้เลื่อนขั้น") 
					$SM_CODE = "1";
				elseif ($FLAG_TO_NAME=="เงินตอบแทนพิเศษ 2 %" || $REMARK=="เงินตอบแทนพิเศษ 2 %") 
					{ $SM_CODE = "8"; $EX_CODE = "015"; } 
				elseif ($FLAG_TO_NAME=="เงินตอบแทนพิเศษ 4 %" || $REMARK=="เงินตอบแทนพิเศษ 4 %" || $REMARK=="ปรับ 4 เปอร์เซ็นต์") 
					{ $SM_CODE = "9";  $EX_CODE = "016"; }

				$LEVEL_NO = str_pad(trim($data[MP_CEE]), 2, "0", STR_PAD_LEFT);
				$LEVEL_NO = trim($LEVEL_NO)? "'".$LEVEL_NO."'" : "NULL";
				if ($LEVEL_NO=='"21"' || $LEVEL_NO=='"ปฏิบัติงาน"') $LEVEL_NO = '"O1"';
				if ($LEVEL_NO=='"22"' || $LEVEL_NO=='"ชำนาญงาน"') $LEVEL_NO = '"O2"';
				if ($LEVEL_NO=='"23"') $LEVEL_NO = '"O3"';
				if ($LEVEL_NO=='"26"' || $LEVEL_NO=='"ปฏิบัติการ"') $LEVEL_NO = '"K1"';
				if ($LEVEL_NO=='"27"' || $LEVEL_NO=='"ชำนาญการ"') $LEVEL_NO = '"K2"';
				if ($LEVEL_NO=='"28"') $LEVEL_NO = '"K3"';
				if ($LEVEL_NO=='"29"') $LEVEL_NO = '"K4"';
				if ($LEVEL_NO=='"30"') $LEVEL_NO = '"K5"';
				if ($LEVEL_NO=='"32"') $LEVEL_NO = '"D1"';
				if ($LEVEL_NO=='"33"') $LEVEL_NO = '"D2"';
				if ($LEVEL_NO=='"34"') $LEVEL_NO = '"M1"';
				if ($LEVEL_NO=='"35"') $LEVEL_NO = '"M2"';
				if ($LEVEL_NO=="'6 ว'" || $LEVEL_NO=="'6ว'") $LEVEL_NO = "'06'";
				if ($LEVEL_NO=="'7ว'" || $LEVEL_NO=="'7 ว.'" || $LEVEL_NO=="'7วช.'") $LEVEL_NO = "'07'";
				if ($LEVEL_NO=="'8 วช'" || $LEVEL_NO=="'8 ว'" || $LEVEL_NO=="'8ว'") $LEVEL_NO = "'08'";
				if ($LEVEL_NO!="'01'" && $LEVEL_NO!=="'02'" && $LEVEL_NO!="'03'" && $LEVEL_NO!="'04'" && $LEVEL_NO!="'05'" && $LEVEL_NO!="'06'" && 
					$LEVEL_NO!="'07'" && $LEVEL_NO!="'08'" && $LEVEL_NO!="'09'" && $LEVEL_NO!="'10'" && $LEVEL_NO!="'11'" && $LEVEL_NO!="'O1'" && 
					$LEVEL_NO!="'O2'" && $LEVEL_NO!="'O3'" && $LEVEL_NO!="'K1'" && $LEVEL_NO!="'K2'" && $LEVEL_NO!="'K3'" && $LEVEL_NO!="'K4'" && 
					$LEVEL_NO!="'K5'" && $LEVEL_NO!="'D1'" && $LEVEL_NO!="'D2'" && $LEVEL_NO!="'M1'" && $LEVEL_NO!="'M2'") $LEVEL_NO = "NULL";
				$MP_FLAG_CURRENT = trim($data[MP_FLAG_CURRENT]);
				if ($MP_FLAG_CURRENT=="1") $LAST_TRANSACTION = "Y"; 
				else $LAST_TRANSACTION = "N";

				$POH_ORG_DOPA_CODE = trim($data[DEPARTMENT_CODE]).trim($data[DIVISION_CODE]).trim($data[SECTION_CODE]).trim($data[JOB_CODE]);
				$POH_ISREAL = "Y";
				$ORDERID = $data[ORDERID];
				$ES_CODE = "02";
				$POH_SEQ_NO = $data[ORDERPIORITY];
				$POH_CMD_SEQ = $data[ORDERTH]; 

				$UPDATE_USER = 99999;
				if ($data[USER_UPDATE]) $USER_UPDATE = trim($data[USER_UPDATE]);
				else $USER_UPDATE = trim($data[USER_CREATE]);
				if ($data[UPDATE_DATE]) $UPDATE_DATE = trim($data[UPDATE_DATE]);
				else  $UPDATE_DATE = trim($data[CREATE_DATE]);
				if ($USER_UPDATE) {
					$cmd = " select ID from USER_DETAIL where USERNAME = '$USER_UPDATE' ";
					$db_dpis2->send_cmd($cmd);
	//				$db_dpis2->show_error();
					$data2 = $db_dpis2->get_array();
					if ($data2[ID]) $UPDATE_USER = $data2[ID];
				}
				if (!$UPDATE_DATE) $UPDATE_DATE = date("Y-m-d H:i:s");

				$STATUS = trim($data[STATUS]);

				if (!$ORG_NAME) $ORG_NAME = "-";
				if (!$POS_NO) $POS_NO = "-";
				if (!$DOCNO) $DOCNO = "-";
				if (!$DOCDATE) $DOCDATE = "-";
				if (!$SALARY) $SALARY = 0;
				if (!$SEQ_NO) $SEQ_NO = 1;
				if (!$CMD_SEQ || $CMD_SEQ > 20000) $CMD_SEQ = "NULL";
				if (!$SAH_PERCENT_UP) $SAH_PERCENT_UP = "NULL";
				if (!$SAH_SALARY_UP) $SAH_SALARY_UP = "NULL";
				if (!$SAH_SALARY_EXTRA) $SAH_SALARY_EXTRA = "NULL";

				$ORG_ID_1 = "NULL";
				$ORG_ID_2 = "NULL";
				$ORG_ID_3 = "NULL";
				if ($POH_ORG_DOPA_CODE) { 
					$cmd = " SELECT OL_CODE, ORG_ID, ORG_ID_REF FROM PER_ORG WHERE ORG_DOPA_CODE = '$POH_ORG_DOPA_CODE' ";
					$db_dpis2->send_cmd($cmd);
	//				$db_dpis2->show_error();
					$data2 = $db_dpis2->get_array();
					$OL_CODE = trim($data2[OL_CODE]);
					$ORG_ID = $data2[ORG_ID];
					$ORG_ID_REF = $data2[ORG_ID_REF];
					if ($OL_CODE == "03") {
						$ORG_ID_1 = $ORG_ID;
					} elseif ($OL_CODE == "04") {
						$ORG_ID_1 = $ORG_ID_REF;
						$ORG_ID_2 = $ORG_ID;
					} elseif ($OL_CODE == "05") {
						$ORG_ID_2 = $ORG_ID_REF;
						$ORG_ID_3 = $ORG_ID;
						$cmd = " SELECT ORG_ID_REF FROM PER_ORG WHERE ORG_ID = $ORG_ID_REF ";
						$db_dpis2->send_cmd($cmd);
	//					$db_dpis2->show_error();
						$data2 = $db_dpis2->get_array();
						$ORG_ID_1 = $data2[ORG_ID_REF];
					}
				}

				if ($POSITIONHIS) {
					$PER_POSITIONHIS++;
					$cmd = " INSERT INTO PER_POSITIONHIS(POH_ID, PER_ID, POH_EFFECTIVEDATE, MOV_CODE, POH_ENDDATE, POH_DOCNO, 
									POH_DOCDATE, POH_POS_NO_NAME, POH_POS_NO, PM_CODE, LEVEL_NO, PL_CODE, PN_CODE, PT_CODE, CT_CODE, PV_CODE, AP_CODE, 
									POH_ORGMGT, ORG_ID_1, ORG_ID_2, ORG_ID_3, POH_UNDER_ORG1, POH_UNDER_ORG2, POH_SALARY, 
									POH_SALARY_POS, POH_REMARK, UPDATE_USER, UPDATE_DATE, PER_CARDNO, POH_ORG1, POH_ORG2, POH_ORG3, POH_ORG, 
									POH_PM_NAME, POH_PL_NAME, POH_SEQ_NO, POH_LAST_POSITION, POH_CMD_SEQ, POH_ISREAL, 
									POH_ORG_DOPA_CODE, ES_CODE, POH_LEVEL_NO, POH_REMARK1, POH_REMARK2, POH_DOCNO_EDIT, POH_DOCDATE_EDIT)
									VALUES ($MAX_POH_ID, $PER_ID, '$EFFECTIVEDATE', '$MOV_CODE', NULL, '$DOCNO', 
									'$DOCDATE', '$POS_NO_NAME', '$POS_NO', '$PM_CODE', $LEVEL_NO, '$PL_CODE', NULL, NULL, '140', NULL, NULL, 2, NULL, NULL, NULL, 
									'$POH_UNDER_ORG1', '$POH_UNDER_ORG2', $SALARY, $POH_SALARY_POS, '$REMARK', $UPDATE_USER, '$UPDATE_DATE', 
									'$PER_CARDNO', '$POH_ORG1', '$POH_ORG2', '$POH_ORG3', '$POH_ORG', '$PM_NAME', '$PL_NAME', $SEQ_NO, 
									'$LAST_TRANSACTION', $CMD_SEQ, '$POH_ISREAL', '$POH_ORG_DOPA_CODE', '$ES_CODE', $LEVEL_NO, '$FLAG_TO_NAME', 
									'$SPECIALIST_NAME', '$DOCNO_EDIT', '$DOCDATE_EDIT') ";
					$db_dpis->send_cmd($cmd);

					$cmd1 = " SELECT POH_ID FROM PER_POSITIONHIS WHERE POH_ID = $MAX_POH_ID "; 
					$count_data = $db_dpis1->send_cmd($cmd1);
					if (!$count_data) {
						echo "$cmd<br>==================<br>";
						$db_dpis->show_error();
						echo "<br>end ". ++$i  ."=======================<br>";
					}
					$MAX_POH_ID++;
				} // end if						
				if ($SALARYHIS) {
					$PER_SALARYHIS++;
					$cmd = " INSERT INTO PER_SALARYHIS(SAH_ID, PER_ID, SAH_EFFECTIVEDATE, MOV_CODE, SAH_SALARY, 
									SAH_DOCNO, SAH_DOCDATE, SAH_ENDDATE, UPDATE_USER, UPDATE_DATE, PER_CARDNO, SAH_PERCENT_UP, 
									SAH_SALARY_UP, SAH_SALARY_EXTRA, SAH_SEQ_NO, SAH_REMARK, LEVEL_NO, SAH_POS_NO_NAME, SAH_POS_NO, 
									SAH_POSITION, SAH_ORG, EX_CODE, SAH_PAY_NO, SAH_LAST_SALARY, SM_CODE, SAH_CMD_SEQ, SAH_ORG_DOPA_CODE)
									VALUES ($MAX_SAH_ID, $PER_ID, '$EFFECTIVEDATE', '$MOV_CODE', $SALARY, '$DOCNO', 
									'$DOCDATE', NULL, $UPDATE_USER, '$UPDATE_DATE', '$PER_CARDNO', $SAH_PERCENT_UP, $SAH_SALARY_UP, 
									$SAH_SALARY_EXTRA, $SEQ_NO, '$REMARK', $LEVEL_NO, '$POS_NO_NAME', '$POS_NO', 
									'$PL_NAME', '$POH_ORG', '$EX_CODE', '$SAH_PAY_NO', '$LAST_TRANSACTION', '$SM_CODE', $CMD_SEQ, '$SAH_ORG_DOPA_CODE') ";
					$db_dpis->send_cmd($cmd);

					$cmd1 = " SELECT SAH_ID FROM PER_SALARYHIS WHERE SAH_ID = $MAX_SAH_ID "; 
					$count_data = $db_dpis1->send_cmd($cmd1);
					if (!$count_data) {
						echo "$cmd<br>==================<br>";
						$db_dpis->show_error();
						echo "<br>end ". ++$j  ."=======================<br>";
					}
					$MAX_SAH_ID++;
				} // end if		
			} else {
//				echo "$PER_CARDNO<br>";
			} // end if						
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_POSITIONHIS ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] - $CNT_POH_ID;
		echo "PER_POSITIONHIS - $PER_POSITIONHIS - $COUNT_NEW<br>";

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_SALARYHIS ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] - $CNT_SAH_ID;
		echo "PER_SALARYHIS - $PER_SALARYHIS - $COUNT_NEW<br>";

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_att->free_result();
		$db_att1->free_result();
	} // end if

	if( $command=='SALARY_POS_ABB_NAME' ){
// แก้ ว.
		$cmd = " SELECT a.ID, a.FLAG_PERSON_TYPE, ORDER_MOVE_POSITION, a.POS_NUM_CODE_SIT, a.POS_NUM_CODE_SIT_ABB, 
                        a.MP_COMMAND_NUM, a.CUR_YEAR, a.MP_YEAR, to_char(a.MP_COMMAND_DATE,'yyyy-mm-dd') as MP_COMMAND_DATE, 
                        a.FLAG_TO_NAME_CODE, a.FLAG_TO_NAME, to_char(a.MP_FORCE_DATE,'yyyy-mm-dd') as MP_FORCE_DATE, 
                        to_char(a.MP_POS_DATE,'yyyy-mm-dd') as MP_POS_DATE, a.JOB_CODE, a.JOB_NAME, a.SECTION_CODE, a.SECTION_NAME, 
                        a.DIVISION_CODE, a.DIVISION_NAME, a.DEPARTMENT_CODE, a.DEPARTMENT_NAME, a.POS_NUM_NAME, a.POS_NUM_CODE, 
                        a.WORK_LINE_CODE, a.WORK_LINE_NAME, a.MP_CEE, a.SALARY_LEVEL_CODE, a.SALARY, a.ADMIN_CODE, a.ADMIN_NAME, 
                        a.SALARY_POS_CODE_1, a.SALARY_POS_ABB_NAME_1, a.SAL_POS_AMOUNT_1, a.SALARY_POS_CODE, a.SALARY_POS_ABB_NAME, 
                        a.SAL_POS_AMOUNT_2, a.SPECIALIST_CODE, FILL_APP_FLAG, FLAG_POS_STATUS, a.MP_FLAG, UP_C_FLAG, 
                        MP_FLAG_CURRENT, REMARK, AUDIT_FLAG, USER_AUDIT, to_char(AUDIT_DATE,'yyyy-mm-dd') as AUDIT_DATE, 
                        a.MP_FLAG_1, COURSE_CODE, COURSE_NAME, PLACE_NAME, PROVINCE_CODE, COUNTRY_CODE, 
                        a.POS_NUM_CODE_SIT_CODE, a.POSITION_CATG, REC_STATUS, SPECIALIST_NAME, MVMENT_CODE, MVMENT_OF, 
                        a.SPECIAL_AMT, POS_NUM_CODE_SIT_EDIT, POS_NUM_CODE_SIT_ABB_EDIT, POS_NUM_CODE_SIT_CODE_EDIT, 
                        MP_COMMAND_NUM_EDIT, CUR_YEAR_EDIT, FLAG_TO_NAME_CODE_E, FLAG_TO_NAME_E, 
                        to_char(MP_COMMAND_DATE_EDIT,'yyyy-mm-dd') as MP_COMMAND_DATE_EDIT, a.MP_CEE_CODE, a.SALARY_LEVEL, 
                        a.GROUPWORK_CODE, GROUPWORK_NAME, ACTIVE_STATUS, a.USER_CREATE, 
                        to_char(a.CREATE_DATE,'yyyy-mm-dd') as CREATE_DATE, a.USER_UPDATE,     to_char(a.UPDATE_DATE,'yyyy-mm-dd') as UPDATE_DATE 
                        FROM HR_POSITION_OFFICER a
						WHERE FLAG_PERSON_TYPE != '3' AND TRIM(SALARY_POS_ABB_NAME) IN ('ว', '7 ว', '7ว', '7วช.', 'ชช', 'ชช.', 'ว.', 'วช', 'วช.', 'วชฃ.')
                        ORDER BY ID, FLAG_PERSON_TYPE, ORDER_MOVE_POSITION ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		while($data = $db_att->get_array()){
			$PER_CARDNO = trim($data[ID]);
			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = '$PER_CARDNO' ";
			$db_dpis->send_cmd($cmd);
			$db_dpis->show_error();
			$data_dpis = $db_dpis->get_array();
			$PER_ID = $data_dpis[NEW_CODE] + 0;

			if ($PER_ID) {
				$EFFECTIVEDATE = trim($data[MP_POS_DATE]);
				if (!$EFFECTIVEDATE) $EFFECTIVEDATE = "-";

				if (substr($EFFECTIVEDATE,0,4)=="0494") $EFFECTIVEDATE = "2006".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0291") $EFFECTIVEDATE = "1977".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1857" && $CUR_YEAR=="2533") $EFFECTIVEDATE = "1990".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1857" && $CUR_YEAR=="2535") $EFFECTIVEDATE = "1992".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1857" && $CUR_YEAR=="2536") $EFFECTIVEDATE = "1993".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1857" && $CUR_YEAR=="2537") $EFFECTIVEDATE = "1994".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1857" && $CUR_YEAR=="2538") $EFFECTIVEDATE = "1995".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1857" && $CUR_YEAR=="2539") $EFFECTIVEDATE = "1996".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1857" && $CUR_YEAR=="2540") $EFFECTIVEDATE = "1997".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1857" && $CUR_YEAR=="2541") $EFFECTIVEDATE = "1998".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1857" && $CUR_YEAR=="2542") $EFFECTIVEDATE = "1999".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0289" && $CUR_YEAR=="2543") $EFFECTIVEDATE = "2000".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0001") $EFFECTIVEDATE = "2001".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1712") $EFFECTIVEDATE = "2002".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0287") $EFFECTIVEDATE = "2003".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0703") $EFFECTIVEDATE = "2003".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1003") $EFFECTIVEDATE = "2003".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1713") $EFFECTIVEDATE = "2003".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0004") $EFFECTIVEDATE = "2004".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0286") $EFFECTIVEDATE = "2004".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0289" && $CUR_YEAR=="2547") $EFFECTIVEDATE = "2004".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0296") $EFFECTIVEDATE = "2004".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1714") $EFFECTIVEDATE = "2004".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0005") $EFFECTIVEDATE = "2005".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1857" && $CUR_YEAR=="2548") $EFFECTIVEDATE = "2005".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0006") $EFFECTIVEDATE = "2006".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0294") $EFFECTIVEDATE = "2006".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1711" && $CUR_YEAR=="2549") $EFFECTIVEDATE = "2006".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0007") $EFFECTIVEDATE = "2007".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0293") $EFFECTIVEDATE = "2007".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1707") $EFFECTIVEDATE = "2007".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0008") $EFFECTIVEDATE = "2008".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0492") $EFFECTIVEDATE = "2008".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1708" && $CUR_YEAR=="2551") $EFFECTIVEDATE = "2008".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0009") $EFFECTIVEDATE = "2009".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0491") $EFFECTIVEDATE = "2009".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1708" && $CUR_YEAR=="2552") $EFFECTIVEDATE = "2009".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1709") $EFFECTIVEDATE = "2009".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0490") $EFFECTIVEDATE = "2010".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1711" && $CUR_YEAR=="2554") $EFFECTIVEDATE = "2011".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="4679") $EFFECTIVEDATE = "1979".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="2546") $EFFECTIVEDATE = "2003".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="5004") $EFFECTIVEDATE = "2004".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="2305") $EFFECTIVEDATE = "2005".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="2548") $EFFECTIVEDATE = "2005".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="2016") $EFFECTIVEDATE = "2006".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="2549") $EFFECTIVEDATE = "2006".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="2550") $EFFECTIVEDATE = "2007".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="4680") $EFFECTIVEDATE = "2010".substr($EFFECTIVEDATE,4); 

				$PL_CODE = trim($data[WORK_LINE_CODE]);
				$PL_NAME = trim($data[WORK_LINE_NAME]);
				if (!$PL_CODE && $PL_NAME) { 
					$cmd = " select PL_CODE from PER_LINE where PL_NAME = '$PL_NAME' ";
					$db_dpis->send_cmd($cmd);
					$data_dpis = $db_dpis->get_array();
					$PL_CODE = trim($data_dpis[PL_CODE]);
				} 
				if ($PL_CODE=="-") $PL_CODE = "";
				if ($PL_CODE=="00000") $PL_CODE = "";
				if ($PL_CODE=="11091") $PL_CODE = "";
				if ($PL_CODE=="204") $PL_CODE = "";
				if ($PL_CODE=="60004") $PL_CODE = "";
				if ($PL_CODE=="60140") $PL_CODE = "";
				if ($PL_CODE=="60604") $PL_CODE = "";
				if ($PL_CODE=="6101") $PL_CODE = "";
				if ($PL_CODE=="6153") $PL_CODE = "";
				if ($PL_CODE=="62204") $PL_CODE = "";
				if ($PL_CODE=="62307") $PL_CODE = "";
				if ($PL_CODE=="6254") $PL_CODE = "";
				if ($PL_CODE=="6301") $PL_CODE = "";
				if ($PL_CODE=="70213") $PL_CODE = "70203";
				if ($PL_CODE=="82403") $PL_CODE = "";
				if ($PL_CODE=="82623") $PL_CODE = "";
				if ($PL_CODE=="82365") $PL_CODE = "";
				if ($PL_CODE=="82695") $PL_CODE = "84624";
				if ($PL_CODE=="82696" || $PL_CODE=="84696") $PL_CODE = "84625";
				if ($PL_CODE=="82697" || $PL_CODE=="84697") $PL_CODE = "84626";
				if ($PL_CODE=="82698" || $PL_CODE=="84698") $PL_CODE = "84627";
				if ($PL_CODE=="กคส01") $PL_CODE = "";
				if ($PL_CODE=="0" && $PL_NAME=="ครู") $PL_CODE = ""; 
				elseif ($PL_CODE=="0" && $PL_NAME=="อาจารย์ 2 รับเงินเดือนในระดับ 7") $PL_CODE = "10035"; 
				elseif ($PL_CODE=="0" && $PL_NAME=="พยาบาลจัตวา") $PL_CODE = ""; 
				elseif ($PL_CODE=="0" && $PL_NAME=="เจ้าหน้าที่บริหารงานการเงินและบัญชี") $PL_CODE = "20435"; 
				elseif ($PL_CODE=="0" && $PL_NAME=="นักบริหารการศึกษา") $PL_CODE = "84504"; 
				elseif ($PL_CODE=="00" && $PL_NAME=="ช่างรังวัด") $PL_CODE = ""; 
				elseif ($PL_CODE=="00" && $PL_NAME=="นักประชาสงเคราะห์") $PL_CODE = "82903"; 
				elseif ($PL_CODE=="00" && $PL_NAME=="พนักงานการเงินและบัญชี จัตวา") $PL_CODE = "42001"; 
				elseif ($PL_CODE=="00" && $PL_NAME=="เจ้าหน้าที่ทะเบียน") $PL_CODE = ""; 
				elseif ($PL_CODE=="00" && $PL_NAME=="เศรษฐกร") $PL_CODE = ""; 
				elseif ($PL_CODE=="00" && $PL_NAME=="ช่างก่อสร้าง") $PL_CODE = ""; 
				elseif ($PL_CODE=="1043" && $PL_NAME=="เจ้าพนักงานปกครอง") $PL_CODE = "10403"; 
				elseif ($PL_CODE=="10910" && $PL_NAME=="เจ้าหน้าที่ปกครอง") $PL_CODE = "41006"; 
				elseif ($PL_CODE=="11890" && $PL_NAME=="เจ้าหน้าที่บันทึกข้อมูล 1-3/4/5") $PL_CODE = "11801"; 
				elseif ($PL_CODE=="11890" && $PL_NAME=="เจ้าหน้าที่บันทึกข้อมูล") $PL_CODE = "11801"; 
				elseif ($PL_CODE=="12320" && $PL_NAME=="เจ้าหน้าที่ปกครอง") $PL_CODE = "41006"; 
				elseif ($PL_CODE=="13730" && $PL_NAME=="เจ้าหน้าที่ปกครอง") $PL_CODE = "41006"; 
				elseif ($PL_CODE=="15450" && $PL_NAME=="เจ้าหน้าที่บันทึกข้อมูล") $PL_CODE = "11801"; 
				elseif ($PL_CODE=="15450" && $PL_NAME=="เจ้าหน้าที่ปกครอง") $PL_CODE = "41006"; 
				elseif ($PL_CODE=="18640" && $PL_NAME=="พนักงานปกครอง") $PL_CODE = "10512"; 
				elseif ($PL_CODE=="18670" && $PL_NAME=="เจ้าพนักงานปกครอง") $PL_CODE = "10403"; 
				elseif ($PL_CODE=="21390" && $PL_NAME=="เจ้าพนักงานปกครอง") $PL_CODE = "10403"; 
				elseif ($PL_CODE=="23490" && $PL_NAME=="เจ้าพนักงานปกครอง") $PL_CODE = "10403"; 
				elseif ($PL_CODE=="23910" && $PL_NAME=="เจ้าพนักงานธุรการ") $PL_CODE = "11612"; 
				elseif ($PL_CODE=="24330" && $PL_NAME=="เจ้าพนักงานปกครอง") $PL_CODE = "10403"; 
				elseif ($PL_CODE=="31750" && $PL_NAME=="เจ้าพนักงานปกครอง") $PL_CODE = "31006"; 
				elseif ($PL_CODE=="32250" && $PL_NAME=="เจ้าหน้าที่บริหารงานโยธา") $PL_CODE = "75505"; 
				elseif ($PL_CODE=="8852" && $PL_NAME=="อาจารย์ 1") $PL_CODE = "10023"; 
				elseif ($PL_CODE=="90000" && $PL_NAME=="เจ้าหน้าที่บริหารงานคลัง") $PL_CODE = "20224"; 
				elseif ($PL_CODE=="9100" && $PL_NAME=="เจ้าหน้าที่ปกครอง") $PL_CODE = "41006"; 
				elseif ($PL_CODE=="999" && $PL_NAME=="นักเทคนิคการแพทย์") $PL_CODE = "60503"; 
				elseif ($PL_CODE=="999" && $PL_NAME=="ดุริยางคศิลปิน") $PL_CODE = "82201"; 
				elseif ($PL_CODE=="999" && $PL_NAME=="xx") $PL_CODE = ""; 
				elseif ($PL_CODE=="999" && $PL_NAME=="เจ้าพนักงานพัสดุ") $PL_CODE = "11712"; 
				elseif ($PL_CODE=="999" && $PL_NAME=="ผู้ช่วยผู้อำนวยการโรงเรียน") $PL_CODE = "20046"; 
				elseif ($PL_CODE=="999" && $PL_NAME=="อาจารย์ 1") $PL_CODE = "10023"; 
				elseif ($PL_CODE=="999" && $PL_NAME=="ผู้อำนวยการโรงเรียน") $PL_CODE = "20067"; 
				elseif ($PL_CODE=="999" && $PL_NAME=="อาจารย์ใหญ่") $PL_CODE = "20036"; 
				elseif ($PL_CODE=="999" && $PL_NAME=="ผู้ช่วยอาจารย์ใหญ่") $PL_CODE = "20025"; 
				elseif ($PL_CODE=="999" && $PL_NAME=="อาจารย์ 2") $PL_CODE = "10035"; 

				$PM_CODE = trim($data[ADMIN_CODE]);
				$PM_NAME = trim($data[ADMIN_NAME]);
				if ($PM_CODE=="00000") $PM_CODE = "";
				if (!$PM_CODE && $PM_NAME) { 
					$cmd = " select PM_CODE from PER_MGT where PM_NAME = '$PM_NAME' ";
					$db_dpis->send_cmd($cmd);
					$data_dpis = $db_dpis->get_array();
					$PM_CODE = trim($data_dpis[PM_CODE]);
				} 
				if ($PM_CODE=="0") $PM_CODE = "";
				if ($PM_CODE=="0" && $PM_NAME=="ผู้ช่วยศึกษาธิการ") $PM_CODE = "50031";
				if ($PM_CODE=="20214") $PM_CODE = "";
				if ($PM_CODE=="ั") $PM_CODE = "";
				if ($PM_CODE=="60011") $PM_CODE = "";
				if ($PM_CODE=="้") $PM_CODE = "";

				$FLAG_TO_NAME = trim($data[FLAG_TO_NAME]);
				$FLAG_TO_NAME_CODE = trim($data[FLAG_TO_NAME_CODE]);
				$MOV_CODE = trim($data[MVMENT_CODE]);

				$cmd = " select MOV_CODE, MOV_TYPE from PER_MOVMENT where MOV_NAME = '$FLAG_TO_NAME' ";
				$db_dpis->send_cmd($cmd);
	//				$db_dpis->show_error();
				$data_dpis = $db_dpis->get_array();
				$MOV_CODE = trim($data_dpis[MOV_CODE]);
				$MOV_TYPE = trim($data_dpis[MOV_TYPE]);
				if (!$MOV_CODE) { 
					if ($FLAG_TO_NAME_CODE) {
						$MOV_CODE = $FLAG_TO_NAME_CODE;
						if ($MOV_CODE=="00") $MOV_CODE = "43";
					} else { 
						if ($FLAG_TO_NAME=="แต่งตั้ง (ย้าย)"|| $FLAG_TO_NAME=="แต่งตั้ง ( ย้ายสับเปลี่ยน )") $MOV_CODE = "6"; 
						elseif ($FLAG_TO_NAME=="อื่นๆ") $MOV_CODE = "0"; 
						elseif ($FLAG_TO_NAME=="ปรับอัตราเงินเดือนตามบัญชีอัตราเงินเดือนใหม่ ท้าย พ.ร.บ. เงินเดือนและเงินประจำตำ") $MOV_CODE = "43"; 
						elseif ($FLAG_TO_NAME=="รับโอนข้าราชการตามกฎหมายอื่น") $MOV_CODE = "4"; 
						elseif ($FLAG_TO_NAME=="รับโอนข้าราชการตามกฎหมายอื่น ผู้สอบแข่งขันได้") $MOV_CODE = "32"; 
						else { 
							$MOV_CODE = "0"; 
							if ($FLAG_TO_NAME) echo "ประเภทการเคลื่อนไหว->$FLAG_TO_NAME_CODE - $FLAG_TO_NAME<br>"; 
						}
					}
				}

				if (!$MOV_TYPE) { 
					$cmd = " select MOV_TYPE from PER_MOVMENT where MOV_CODE = '$MOV_CODE' ";
					$db_dpis->send_cmd($cmd);
		//				$db_dpis->show_error();
					$data_dpis = $db_dpis->get_array();
					$MOV_TYPE = trim($data_dpis[MOV_TYPE]);
				}

				$SALARYHIS = $POSITIONHIS = "";		
				if ($MOV_TYPE==2)
					$SALARYHIS = 1;
				else
					$POSITIONHIS = 1;		

				$LEVEL_NO = str_pad(trim($data[MP_CEE]), 2, "0", STR_PAD_LEFT);
				$PT_NAME = "";
				$SALARY_POS_ABB_NAME = trim($data[SALARY_POS_ABB_NAME]);
				if ($SALARY_POS_ABB_NAME=='ว' || $SALARY_POS_ABB_NAME=='7 ว' || $SALARY_POS_ABB_NAME=='7ว' || $SALARY_POS_ABB_NAME=='ว.') {
					$PT_CODE = "9";
					$PT_NAME = "ว";
				} elseif ($SALARY_POS_ABB_NAME=='7วช.' || $SALARY_POS_ABB_NAME=='วช' || $SALARY_POS_ABB_NAME=='วช.' || $SALARY_POS_ABB_NAME=='วชฃ.') {
					$PT_CODE = "4";
					$PT_NAME = "วช";
				} elseif ($SALARY_POS_ABB_NAME=='ชช' || $SALARY_POS_ABB_NAME=='ชช.') {
					$PT_CODE = "5";
					$PT_NAME = "ชช";
				}
				$POH_PL_NAME = $PL_NAME . " " . level_no_format($LEVEL_NO) . $PT_NAME;

//echo  "$POH_ORG $PM_NAME $POH_PL_NAME<br>";
				if ($POSITIONHIS) {
					$cmd = " select POH_ID from PER_POSITIONHIS where PER_ID = $PER_ID AND POH_EFFECTIVEDATE = '$EFFECTIVEDATE' and MOV_CODE = '$MOV_CODE' ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
					$data_dpis = $db_dpis->get_array();
					$POH_ID = $data_dpis[POH_ID];

					if ($POH_ID) {
						$cmd = " UPDATE PER_POSITIONHIS SET PT_CODE = '$PT_CODE', POH_PL_NAME = '$POH_PL_NAME' WHERE POH_ID = $POH_ID ";
						$db_dpis->send_cmd($cmd);
//$i++;
//echo "$i $cmd<br>";

					} // end if						
				} // end if						
				if ($SALARYHIS) {
					$cmd = " select SAH_ID from PER_SALARYHIS where PER_ID = $PER_ID AND SAH_EFFECTIVEDATE = '$EFFECTIVEDATE' and MOV_CODE = '$MOV_CODE' ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
					$data_dpis = $db_dpis->get_array();
					$SAH_ID = $data_dpis[SAH_ID];

					if ($SAH_ID) {
						$cmd = " UPDATE PER_SALARYHIS SET SAH_POSITION = '$POH_PL_NAME' WHERE SAH_ID = $SAH_ID ";
						$db_dpis->send_cmd($cmd);

//$i++;
//echo "$i $cmd<br>";
					} // end if						
				} // end if		
			} else {
//				echo "$PER_CARDNO<br>";
			} // end if						
		} // end while						

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_att->free_result();
		$db_att1->free_result();
	} // end if

	if( $command=='SPECIALIST' ){
// แก้ ด้าน
		$cmd = " SELECT a.ID, a.FLAG_PERSON_TYPE, ORDER_MOVE_POSITION, a.POS_NUM_CODE_SIT, a.POS_NUM_CODE_SIT_ABB, 
                        a.MP_COMMAND_NUM, a.CUR_YEAR, a.MP_YEAR, to_char(a.MP_COMMAND_DATE,'yyyy-mm-dd') as MP_COMMAND_DATE, 
                        a.FLAG_TO_NAME_CODE, a.FLAG_TO_NAME, to_char(a.MP_FORCE_DATE,'yyyy-mm-dd') as MP_FORCE_DATE, 
                        to_char(a.MP_POS_DATE,'yyyy-mm-dd') as MP_POS_DATE, a.JOB_CODE, a.JOB_NAME, a.SECTION_CODE, a.SECTION_NAME, 
                        a.DIVISION_CODE, a.DIVISION_NAME, a.DEPARTMENT_CODE, a.DEPARTMENT_NAME, a.POS_NUM_NAME, a.POS_NUM_CODE, 
                        a.WORK_LINE_CODE, a.WORK_LINE_NAME, a.MP_CEE, a.SALARY_LEVEL_CODE, a.SALARY, a.ADMIN_CODE, a.ADMIN_NAME, 
                        a.SALARY_POS_CODE_1, a.SALARY_POS_ABB_NAME_1, a.SAL_POS_AMOUNT_1, a.SALARY_POS_CODE, a.SALARY_POS_ABB_NAME, 
                        a.SAL_POS_AMOUNT_2, a.SPECIALIST_CODE, FILL_APP_FLAG, FLAG_POS_STATUS, a.MP_FLAG, UP_C_FLAG, 
                        MP_FLAG_CURRENT, REMARK, AUDIT_FLAG, USER_AUDIT, to_char(AUDIT_DATE,'yyyy-mm-dd') as AUDIT_DATE, 
                        a.MP_FLAG_1, COURSE_CODE, COURSE_NAME, PLACE_NAME, PROVINCE_CODE, COUNTRY_CODE, 
                        a.POS_NUM_CODE_SIT_CODE, a.POSITION_CATG, REC_STATUS, SPECIALIST_NAME, MVMENT_CODE, MVMENT_OF, 
                        a.SPECIAL_AMT, POS_NUM_CODE_SIT_EDIT, POS_NUM_CODE_SIT_ABB_EDIT, POS_NUM_CODE_SIT_CODE_EDIT, 
                        MP_COMMAND_NUM_EDIT, CUR_YEAR_EDIT, FLAG_TO_NAME_CODE_E, FLAG_TO_NAME_E, 
                        to_char(MP_COMMAND_DATE_EDIT,'yyyy-mm-dd') as MP_COMMAND_DATE_EDIT, a.MP_CEE_CODE, a.SALARY_LEVEL, 
                        a.GROUPWORK_CODE, GROUPWORK_NAME, ACTIVE_STATUS, a.USER_CREATE, 
                        to_char(a.CREATE_DATE,'yyyy-mm-dd') as CREATE_DATE, a.USER_UPDATE,     to_char(a.UPDATE_DATE,'yyyy-mm-dd') as UPDATE_DATE 
                        FROM HR_POSITION_OFFICER a
						WHERE FLAG_PERSON_TYPE != '3' AND SPECIALIST_NAME IS NOT NULL
                        ORDER BY ID, FLAG_PERSON_TYPE, ORDER_MOVE_POSITION ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		while($data = $db_att->get_array()){
			$PER_CARDNO = trim($data[ID]);
			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = '$PER_CARDNO' ";
			$db_dpis->send_cmd($cmd);
			$db_dpis->show_error();
			$data_dpis = $db_dpis->get_array();
			$PER_ID = $data_dpis[NEW_CODE] + 0;

			if ($PER_ID) {
				$EFFECTIVEDATE = trim($data[MP_POS_DATE]);
				if (!$EFFECTIVEDATE) $EFFECTIVEDATE = "-";

				if (substr($EFFECTIVEDATE,0,4)=="0494") $EFFECTIVEDATE = "2006".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0291") $EFFECTIVEDATE = "1977".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1857" && $CUR_YEAR=="2533") $EFFECTIVEDATE = "1990".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1857" && $CUR_YEAR=="2535") $EFFECTIVEDATE = "1992".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1857" && $CUR_YEAR=="2536") $EFFECTIVEDATE = "1993".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1857" && $CUR_YEAR=="2537") $EFFECTIVEDATE = "1994".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1857" && $CUR_YEAR=="2538") $EFFECTIVEDATE = "1995".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1857" && $CUR_YEAR=="2539") $EFFECTIVEDATE = "1996".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1857" && $CUR_YEAR=="2540") $EFFECTIVEDATE = "1997".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1857" && $CUR_YEAR=="2541") $EFFECTIVEDATE = "1998".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1857" && $CUR_YEAR=="2542") $EFFECTIVEDATE = "1999".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0289" && $CUR_YEAR=="2543") $EFFECTIVEDATE = "2000".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0001") $EFFECTIVEDATE = "2001".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1712") $EFFECTIVEDATE = "2002".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0287") $EFFECTIVEDATE = "2003".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0703") $EFFECTIVEDATE = "2003".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1003") $EFFECTIVEDATE = "2003".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1713") $EFFECTIVEDATE = "2003".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0004") $EFFECTIVEDATE = "2004".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0286") $EFFECTIVEDATE = "2004".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0289" && $CUR_YEAR=="2547") $EFFECTIVEDATE = "2004".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0296") $EFFECTIVEDATE = "2004".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1714") $EFFECTIVEDATE = "2004".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0005") $EFFECTIVEDATE = "2005".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1857" && $CUR_YEAR=="2548") $EFFECTIVEDATE = "2005".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0006") $EFFECTIVEDATE = "2006".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0294") $EFFECTIVEDATE = "2006".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1711" && $CUR_YEAR=="2549") $EFFECTIVEDATE = "2006".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0007") $EFFECTIVEDATE = "2007".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0293") $EFFECTIVEDATE = "2007".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1707") $EFFECTIVEDATE = "2007".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0008") $EFFECTIVEDATE = "2008".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0492") $EFFECTIVEDATE = "2008".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1708" && $CUR_YEAR=="2551") $EFFECTIVEDATE = "2008".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0009") $EFFECTIVEDATE = "2009".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0491") $EFFECTIVEDATE = "2009".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1708" && $CUR_YEAR=="2552") $EFFECTIVEDATE = "2009".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1709") $EFFECTIVEDATE = "2009".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0490") $EFFECTIVEDATE = "2010".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1711" && $CUR_YEAR=="2554") $EFFECTIVEDATE = "2011".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="4679") $EFFECTIVEDATE = "1979".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="2546") $EFFECTIVEDATE = "2003".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="5004") $EFFECTIVEDATE = "2004".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="2305") $EFFECTIVEDATE = "2005".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="2548") $EFFECTIVEDATE = "2005".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="2016") $EFFECTIVEDATE = "2006".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="2549") $EFFECTIVEDATE = "2006".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="2550") $EFFECTIVEDATE = "2007".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="4680") $EFFECTIVEDATE = "2010".substr($EFFECTIVEDATE,4); 

				$FLAG_TO_NAME = trim($data[FLAG_TO_NAME]);
				$SPECIALIST_NAME = trim($data[SPECIALIST_NAME]);
				$FLAG_TO_NAME_CODE = trim($data[FLAG_TO_NAME_CODE]);
				$MOV_CODE = trim($data[MVMENT_CODE]);

				$cmd = " select MOV_CODE, MOV_TYPE from PER_MOVMENT where MOV_NAME = '$FLAG_TO_NAME' ";
				$db_dpis->send_cmd($cmd);
	//				$db_dpis->show_error();
				$data_dpis = $db_dpis->get_array();
				$MOV_CODE = trim($data_dpis[MOV_CODE]);
				$MOV_TYPE = trim($data_dpis[MOV_TYPE]);
				if (!$MOV_CODE) { 
					if ($FLAG_TO_NAME_CODE) {
						$MOV_CODE = $FLAG_TO_NAME_CODE;
						if ($MOV_CODE=="00") $MOV_CODE = "43";
					} else { 
						if ($FLAG_TO_NAME=="แต่งตั้ง (ย้าย)"|| $FLAG_TO_NAME=="แต่งตั้ง ( ย้ายสับเปลี่ยน )") $MOV_CODE = "6"; 
						elseif ($FLAG_TO_NAME=="อื่นๆ") $MOV_CODE = "0"; 
						elseif ($FLAG_TO_NAME=="ปรับอัตราเงินเดือนตามบัญชีอัตราเงินเดือนใหม่ ท้าย พ.ร.บ. เงินเดือนและเงินประจำตำ") $MOV_CODE = "43"; 
						elseif ($FLAG_TO_NAME=="รับโอนข้าราชการตามกฎหมายอื่น") $MOV_CODE = "4"; 
						elseif ($FLAG_TO_NAME=="รับโอนข้าราชการตามกฎหมายอื่น ผู้สอบแข่งขันได้") $MOV_CODE = "32"; 
						else { 
							$MOV_CODE = "0"; 
							if ($FLAG_TO_NAME) echo "ประเภทการเคลื่อนไหว->$FLAG_TO_NAME_CODE - $FLAG_TO_NAME<br>"; 
						}
					}
				}

				if (!$MOV_TYPE) { 
					$cmd = " select MOV_TYPE from PER_MOVMENT where MOV_CODE = '$MOV_CODE' ";
					$db_dpis->send_cmd($cmd);
		//				$db_dpis->show_error();
					$data_dpis = $db_dpis->get_array();
					$MOV_TYPE = trim($data_dpis[MOV_TYPE]);
				}

				$SALARYHIS = $POSITIONHIS = "";		
				if ($MOV_TYPE==2)
					$SALARYHIS = 1;
				else
					$POSITIONHIS = 1;		

//echo  "$POH_ORG $PM_NAME $POH_PL_NAME<br>";
				if ($POSITIONHIS) {
					$cmd = " select POH_ID from PER_POSITIONHIS where PER_ID = $PER_ID AND POH_EFFECTIVEDATE = '$EFFECTIVEDATE' and MOV_CODE = '$MOV_CODE' ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
					$data_dpis = $db_dpis->get_array();
					$POH_ID = $data_dpis[POH_ID];

					if ($POH_ID) {
						$cmd = " UPDATE PER_POSITIONHIS SET POH_SPECIALIST = '$SPECIALIST_NAME' WHERE POH_ID = $POH_ID ";
						$db_dpis->send_cmd($cmd);
//$i++;
//echo "$i $cmd<br>";

					} // end if						
				} // end if						
				if ($SALARYHIS) {
					$cmd = " select SAH_ID from PER_SALARYHIS where PER_ID = $PER_ID AND SAH_EFFECTIVEDATE = '$EFFECTIVEDATE' and MOV_CODE = '$MOV_CODE' ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
					$data_dpis = $db_dpis->get_array();
					$SAH_ID = $data_dpis[SAH_ID];

					if ($SAH_ID) {
						$cmd = " UPDATE PER_SALARYHIS SET SAH_SPECIALIST = '$SPECIALIST_NAME' WHERE SAH_ID = $SAH_ID ";
						$db_dpis->send_cmd($cmd);

//$i++;
//echo "$i $cmd<br>";
					} // end if						
				} // end if		
			} else {
//				echo "$PER_CARDNO<br>";
			} // end if						
		} // end while						

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_att->free_result();
		$db_att1->free_result();
	} // end if

	if( $command=='SALARY_MVMENT' ){
// แก้ ขั้น
		$cmd = " SELECT a.ID, a.FLAG_PERSON_TYPE, ORDER_MOVE_POSITION, a.POS_NUM_CODE_SIT, a.POS_NUM_CODE_SIT_ABB, 
                        a.MP_COMMAND_NUM, a.CUR_YEAR, a.MP_YEAR, to_char(a.MP_COMMAND_DATE,'yyyy-mm-dd') as MP_COMMAND_DATE, 
                        a.FLAG_TO_NAME_CODE, a.FLAG_TO_NAME, to_char(a.MP_FORCE_DATE,'yyyy-mm-dd') as MP_FORCE_DATE, 
                        to_char(a.MP_POS_DATE,'yyyy-mm-dd') as MP_POS_DATE, a.JOB_CODE, a.JOB_NAME, a.SECTION_CODE, a.SECTION_NAME, 
                        a.DIVISION_CODE, a.DIVISION_NAME, a.DEPARTMENT_CODE, a.DEPARTMENT_NAME, a.POS_NUM_NAME, a.POS_NUM_CODE, 
                        a.WORK_LINE_CODE, a.WORK_LINE_NAME, a.MP_CEE, a.SALARY_LEVEL_CODE, a.SALARY, a.ADMIN_CODE, a.ADMIN_NAME, 
                        a.SALARY_POS_CODE_1, a.SALARY_POS_ABB_NAME_1, a.SAL_POS_AMOUNT_1, a.SALARY_POS_CODE, a.SALARY_POS_ABB_NAME, 
                        a.SAL_POS_AMOUNT_2, a.SPECIALIST_CODE, FILL_APP_FLAG, FLAG_POS_STATUS, a.MP_FLAG, UP_C_FLAG, 
                        MP_FLAG_CURRENT, REMARK, AUDIT_FLAG, USER_AUDIT, to_char(AUDIT_DATE,'yyyy-mm-dd') as AUDIT_DATE, 
                        a.MP_FLAG_1, COURSE_CODE, COURSE_NAME, PLACE_NAME, PROVINCE_CODE, COUNTRY_CODE, 
                        a.POS_NUM_CODE_SIT_CODE, a.POSITION_CATG, REC_STATUS, SPECIALIST_NAME, MVMENT_CODE, MVMENT_OF, 
                        a.SPECIAL_AMT, POS_NUM_CODE_SIT_EDIT, POS_NUM_CODE_SIT_ABB_EDIT, POS_NUM_CODE_SIT_CODE_EDIT, 
                        MP_COMMAND_NUM_EDIT, CUR_YEAR_EDIT, FLAG_TO_NAME_CODE_E, FLAG_TO_NAME_E, 
                        to_char(MP_COMMAND_DATE_EDIT,'yyyy-mm-dd') as MP_COMMAND_DATE_EDIT, a.MP_CEE_CODE, a.SALARY_LEVEL, 
                        a.GROUPWORK_CODE, GROUPWORK_NAME, ACTIVE_STATUS, a.USER_CREATE, 
                        to_char(a.CREATE_DATE,'yyyy-mm-dd') as CREATE_DATE, a.USER_UPDATE,     to_char(a.UPDATE_DATE,'yyyy-mm-dd') as UPDATE_DATE 
                        FROM HR_POSITION_OFFICER a
						WHERE FLAG_PERSON_TYPE != '3' AND TRIM(MVMENT_OF) IN ('เลื่อน 1.5 ขั้นและเลื่อนระดับ', 'เลื่อนขั้น 1.5 ขั้น', 'เลื่อน 2 ขั้นและเลื่อนระดับ', 'เลื่อนขั้น 2 ขั้น', 'ให้ได้รับเงินเดือนในระดับถัดไป', 'ปรับอัตราเงินเดือน', 'ปรับอัตราเงินเดือนตาม พรฎ.2547', 'ปรับตามบัญชีเงินเดือนใหม่', 'ปรับเงินเดือนตาม กพ.') 
                        ORDER BY ID, FLAG_PERSON_TYPE, ORDER_MOVE_POSITION ";
//						WHERE FLAG_PERSON_TYPE != '3' AND TRIM(MVMENT_OF) IN ('เงินตอบแทนพิเศษ 2%', 'เงินตอบแทนพิเศษ 4%', 'ไม่ได้เลื่อนขั้น', 'เลื่อน 0.5 ขั้น และเลื่อนระดับ', 'เลื่อนขั้น 0.5 ขั้น') 
//						WHERE FLAG_PERSON_TYPE != '3' AND TRIM(MVMENT_OF) IN ('เลื่อน 1 ขั้นและเลื่อนระดับ', 'เลื่อนขั้น 1 ขั้น') AND ID < '3102000000000'
//						WHERE FLAG_PERSON_TYPE != '3' AND TRIM(MVMENT_OF) IN ('เลื่อน 1 ขั้นและเลื่อนระดับ', 'เลื่อนขั้น 1 ขั้น') AND ID >= '3102000000000'
//						WHERE FLAG_PERSON_TYPE != '3' AND TRIM(MVMENT_OF) IN ('เลื่อน 1.5 ขั้นและเลื่อนระดับ', 'เลื่อนขั้น 1.5 ขั้น', 'เลื่อน 2 ขั้นและเลื่อนระดับ', 'เลื่อนขั้น 2 ขั้น', 'ให้ได้รับเงินเดือนในระดับถัดไป', 'ปรับอัตราเงินเดือน', 'ปรับอัตราเงินเดือนตาม พรฎ.2547', 'ปรับตามบัญชีเงินเดือนใหม่', 'ปรับเงินเดือนตาม กพ.') 
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		while($data = $db_att->get_array()){
			$PER_CARDNO = trim($data[ID]);
			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = '$PER_CARDNO' ";
			$db_dpis->send_cmd($cmd);
			$db_dpis->show_error();
			$data_dpis = $db_dpis->get_array();
			$PER_ID = $data_dpis[NEW_CODE] + 0;

			if ($PER_ID) {
				$EFFECTIVEDATE = trim($data[MP_POS_DATE]);
				if (!$EFFECTIVEDATE) $EFFECTIVEDATE = "-";

				if (substr($EFFECTIVEDATE,0,4)=="0494") $EFFECTIVEDATE = "2006".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0291") $EFFECTIVEDATE = "1977".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1857" && $CUR_YEAR=="2533") $EFFECTIVEDATE = "1990".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1857" && $CUR_YEAR=="2535") $EFFECTIVEDATE = "1992".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1857" && $CUR_YEAR=="2536") $EFFECTIVEDATE = "1993".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1857" && $CUR_YEAR=="2537") $EFFECTIVEDATE = "1994".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1857" && $CUR_YEAR=="2538") $EFFECTIVEDATE = "1995".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1857" && $CUR_YEAR=="2539") $EFFECTIVEDATE = "1996".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1857" && $CUR_YEAR=="2540") $EFFECTIVEDATE = "1997".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1857" && $CUR_YEAR=="2541") $EFFECTIVEDATE = "1998".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1857" && $CUR_YEAR=="2542") $EFFECTIVEDATE = "1999".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0289" && $CUR_YEAR=="2543") $EFFECTIVEDATE = "2000".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0001") $EFFECTIVEDATE = "2001".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1712") $EFFECTIVEDATE = "2002".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0287") $EFFECTIVEDATE = "2003".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0703") $EFFECTIVEDATE = "2003".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1003") $EFFECTIVEDATE = "2003".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1713") $EFFECTIVEDATE = "2003".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0004") $EFFECTIVEDATE = "2004".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0286") $EFFECTIVEDATE = "2004".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0289" && $CUR_YEAR=="2547") $EFFECTIVEDATE = "2004".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0296") $EFFECTIVEDATE = "2004".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1714") $EFFECTIVEDATE = "2004".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0005") $EFFECTIVEDATE = "2005".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1857" && $CUR_YEAR=="2548") $EFFECTIVEDATE = "2005".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0006") $EFFECTIVEDATE = "2006".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0294") $EFFECTIVEDATE = "2006".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1711" && $CUR_YEAR=="2549") $EFFECTIVEDATE = "2006".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0007") $EFFECTIVEDATE = "2007".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0293") $EFFECTIVEDATE = "2007".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1707") $EFFECTIVEDATE = "2007".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0008") $EFFECTIVEDATE = "2008".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0492") $EFFECTIVEDATE = "2008".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1708" && $CUR_YEAR=="2551") $EFFECTIVEDATE = "2008".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0009") $EFFECTIVEDATE = "2009".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0491") $EFFECTIVEDATE = "2009".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1708" && $CUR_YEAR=="2552") $EFFECTIVEDATE = "2009".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1709") $EFFECTIVEDATE = "2009".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0490") $EFFECTIVEDATE = "2010".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1711" && $CUR_YEAR=="2554") $EFFECTIVEDATE = "2011".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="4679") $EFFECTIVEDATE = "1979".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="2546") $EFFECTIVEDATE = "2003".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="5004") $EFFECTIVEDATE = "2004".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="2305") $EFFECTIVEDATE = "2005".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="2548") $EFFECTIVEDATE = "2005".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="2016") $EFFECTIVEDATE = "2006".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="2549") $EFFECTIVEDATE = "2006".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="2550") $EFFECTIVEDATE = "2007".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="4680") $EFFECTIVEDATE = "2010".substr($EFFECTIVEDATE,4); 

				$MVMENT_OF = trim($data[MVMENT_OF]);
				if ($MVMENT_OF=="เงินตอบแทนพิเศษ 2%") $SM_CODE = "8";
				elseif ($MVMENT_OF=="เงินตอบแทนพิเศษ 4%") $SM_CODE = "9";
				elseif ($MVMENT_OF=="ไม่ได้เลื่อนขั้น") $SM_CODE = "1";
				elseif ($MVMENT_OF=="เลื่อน 0.5 ขั้น และเลื่อนระดับ" || $MVMENT_OF=="เลื่อนขั้น 0.5 ขั้น") $SM_CODE = "2";
				elseif ($MVMENT_OF=="เลื่อน 1 ขั้นและเลื่อนระดับ" || $MVMENT_OF=="เลื่อนขั้น 1 ขั้น") $SM_CODE = "3";
				elseif ($MVMENT_OF=="เลื่อน 1.5 ขั้นและเลื่อนระดับ" || $MVMENT_OF=="เลื่อนขั้น 1.5 ขั้น") $SM_CODE = "4";
				elseif ($MVMENT_OF=="เลื่อน 2 ขั้นและเลื่อนระดับ" || $MVMENT_OF=="เลื่อนขั้น 2 ขั้น") $SM_CODE = "5";
				elseif ($MVMENT_OF=="ให้ได้รับเงินเดือนในระดับถัดไป") $SM_CODE = "11";
				elseif ($MVMENT_OF=="ปรับอัตราเงินเดือน" || $MVMENT_OF=="ปรับอัตราเงินเดือนตาม พรฎ.2547" || $MVMENT_OF=="ปรับตามบัญชีเงินเดือนใหม่" || $MVMENT_OF=="ปรับเงินเดือนตาม กพ.") 
					$SM_CODE = "6";
				
				$FLAG_TO_NAME = trim($data[FLAG_TO_NAME]);
				$FLAG_TO_NAME_CODE = trim($data[FLAG_TO_NAME_CODE]);
				$MOV_CODE = trim($data[MVMENT_CODE]);

				$cmd = " select MOV_CODE, MOV_TYPE from PER_MOVMENT where MOV_NAME = '$FLAG_TO_NAME' ";
				$db_dpis->send_cmd($cmd);
	//				$db_dpis->show_error();
				$data_dpis = $db_dpis->get_array();
				$MOV_CODE = trim($data_dpis[MOV_CODE]);
				$MOV_TYPE = trim($data_dpis[MOV_TYPE]);
				if (!$MOV_CODE) { 
					if ($FLAG_TO_NAME_CODE) {
						$MOV_CODE = $FLAG_TO_NAME_CODE;
						if ($MOV_CODE=="00") $MOV_CODE = "43";
					} else { 
						if ($FLAG_TO_NAME=="แต่งตั้ง (ย้าย)"|| $FLAG_TO_NAME=="แต่งตั้ง ( ย้ายสับเปลี่ยน )") $MOV_CODE = "6"; 
						elseif ($FLAG_TO_NAME=="อื่นๆ") $MOV_CODE = "0"; 
						elseif ($FLAG_TO_NAME=="ปรับอัตราเงินเดือนตามบัญชีอัตราเงินเดือนใหม่ ท้าย พ.ร.บ. เงินเดือนและเงินประจำตำ") $MOV_CODE = "43"; 
						elseif ($FLAG_TO_NAME=="รับโอนข้าราชการตามกฎหมายอื่น") $MOV_CODE = "4"; 
						elseif ($FLAG_TO_NAME=="รับโอนข้าราชการตามกฎหมายอื่น ผู้สอบแข่งขันได้") $MOV_CODE = "32"; 
						else { 
							$MOV_CODE = "0"; 
							if ($FLAG_TO_NAME) echo "ประเภทการเคลื่อนไหว->$FLAG_TO_NAME_CODE - $FLAG_TO_NAME<br>"; 
						}
					}
				}

				if (!$MOV_TYPE) { 
					$cmd = " select MOV_TYPE from PER_MOVMENT where MOV_CODE = '$MOV_CODE' ";
					$db_dpis->send_cmd($cmd);
		//				$db_dpis->show_error();
					$data_dpis = $db_dpis->get_array();
					$MOV_TYPE = trim($data_dpis[MOV_TYPE]);
				}

				$SALARYHIS = $POSITIONHIS = "";		
				if ($MOV_TYPE==2)
					$SALARYHIS = 1;
				else
					$POSITIONHIS = 1;		

				if ($SALARYHIS) {
					$cmd = " select SAH_ID from PER_SALARYHIS where PER_ID = $PER_ID AND SAH_EFFECTIVEDATE = '$EFFECTIVEDATE' and MOV_CODE = '$MOV_CODE' ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
					$data_dpis = $db_dpis->get_array();
					$SAH_ID = $data_dpis[SAH_ID];

					if ($SAH_ID) {
						$cmd = " UPDATE PER_SALARYHIS SET SM_CODE = '$SM_CODE' WHERE SAH_ID = $SAH_ID ";
						$db_dpis->send_cmd($cmd);

//$i++;
//echo "$i $cmd<br>";
					} // end if						
				} // end if		
			} else {
//				echo "$PER_CARDNO<br>";
			} // end if						
		} // end while						

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_att->free_result();
		$db_att1->free_result();
	} // end if

	if( $command=='POSEMPHIS' ){
// การดำรงตำแหน่ง 976426
// delete from HR_POSITION_EMP where ID not in (select ID from HR_PERSONAL_EMP)
// update HR_POSITION_EMP set WORK_LINE_CODE = NULL where CATEGORY_SAL_CODE||WORK_LINE_CODE not in (select CATEGORY_SAL_CODE||WORK_LINE_CODE from HR_WORKLINE_EMP)
// update HR_POSITION_EMP set FLAG_TO_NAME_CODE = NULL where FLAG_TO_NAME_CODE not in (select FLAG_TO_NAME_CODE from HR_FLAG_TO_NAME_EMP)
// select  distinct WORK_LINE_CODE, WORK_LINE_NAME from HR_POSITION_EMP minus
// select WORK_LINE_CODE, WORK_LINE_NAME from HR_WORK_LINE_CODE

// select FLAG_TO_NAME_CODE, count(*) from hr_position_emp group by FLAG_TO_NAME_CODE order by to_number(FLAG_TO_NAME_CODE)
// 0	825
// 1	10764
// 01 63
// 2	5964
// 3	1155
// 4	3627
// 5	5849
// 6	122
// 7	28
// 8	270509
// 9 343
// 10	47382
// 11	12
// 12	200
// 13	86
// 14	2845
// 15	1242
// 16	27
// 17	678
// 18	24
// 19	16
// 20	834
// 21	15040
// 22	39877
// 23	10
// 24	3
// 25	74
// 26	21
// 27	95
// 29	1029
// 	535574

		$cmd = " delete from PER_POSITIONHIS where PER_ID in (select PER_ID from PER_PERSONAL where PER_TYPE = 2) ";
		//$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " delete from PER_SALARYHIS where PER_ID in (select PER_ID from PER_PERSONAL where PER_TYPE = 2) ";
		//$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " select count(PER_ID) as CNT_POH_ID from PER_POSITIONHIS ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$CNT_POH_ID = $data2[CNT_POH_ID] + 0;

		$cmd = " select count(PER_ID) as CNT_SAH_ID from PER_SALARYHIS ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$CNT_SAH_ID = $data2[CNT_SAH_ID] + 0;

		$cmd = " select max(POH_ID) as MAX_ID from PER_POSITIONHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_POH_ID = $data[MAX_ID] + 1;

		$cmd = " select max(SAH_ID) as MAX_ID from PER_SALARYHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_SAH_ID = $data[MAX_ID] + 1;

		$cmd = " SELECT a.ID, a.FLAG_PERSON_TYPE, ORDER_MOVE_POSITION, a.BOARD_TYPE, a.MP_YEAR, a.POS_NUM_CODE_SIT, 
                        a.MP_COMMAND_NUM, a.CUR_YEAR, to_char(a.MP_COMMAND_DATE,'yyyy-mm-dd') as MP_COMMAND_DATE, 
                        a.FLAG_TO_NAME_CODE, to_char(a.MP_FORCE_DATE,'yyyy-mm-dd') as MP_FORCE_DATE, 
                        to_char(a.MP_POS_DATE,'yyyy-mm-dd') as MP_POS_DATE, to_char(a.UPCLASS_DATE,'yyyy-mm-dd') as UPCLASS_DATE, 
						to_char(a.WORK_LINE_DATE,'yyyy-mm-dd') as WORK_LINE_DATE, a.JOB_CODE, a.JOB_NAME, a.SECTION_CODE, a.SECTION_NAME, 
                        a.DIVISION_CODE, a.DIVISION_NAME, a.DEPARTMENT_CODE, a.DEPARTMENT_NAME, a.POS_NUM_NAME, a.POS_NUM_CODE, 
                        a.CLUSTER_CODE, a.CATEGORY_SAL_CODE, a.CATEGORY_SAL_NAME, a.WORK_LINE_CODE, a.WORK_LINE_NAME, 
						a.SALARY_LEVEL_CODE, a.SALARY, a.MP_FLAG, MP_FLAG_CURRENT, REMARK, AUDIT_FLAG, USER_AUDIT, 
						to_char(AUDIT_DATE,'yyyy-mm-dd') as AUDIT_DATE, a.USER_CREATE, 
                        to_char(a.CREATE_DATE,'yyyy-mm-dd') as CREATE_DATE, a.USER_UPDATE,     to_char(a.UPDATE_DATE,'yyyy-mm-dd') as UPDATE_DATE, 
                        a.POS_NUM_CODE_SIT_ABB, a.FLAG_TO_NAME, a.POS_NUM_CODE_SIT_CODE, REC_STATUS, MOVEMENT_CODE, MOVEMENT_NAME, 
						a.SPECIAL_AMT, a.MP_FLAG_1, POS_NUM_CODE_SIT_EDIT, POS_NUM_CODE_SIT_ABB_EDIT, POS_NUM_CODE_SIT_CODE_EDIT,
						MP_COMMAND_NUM_EDIT, CUR_YEAR_EDIT, FLAG_TO_NAME_CODE_E, FLAG_TO_NAME_E, 
						to_char(MP_COMMAND_DATE_EDIT,'yyyy-mm-dd') as MP_COMMAND_DATE_EDIT, a.SALARY_D, a.SALARY_ADD_D, a.SPECIAL_AMT_D, 
                        a.SALARY_ADD, ACTIVE_STATUS 
                        FROM HR_POSITION_EMP a
						WHERE a.ID >= '3740000000000' 
                        ORDER BY ID, FLAG_PERSON_TYPE, ORDER_MOVE_POSITION ";

// 110585			WHERE a.ID < '3100600000000'  
// 109641			WHERE a.ID >= '3100600000000' AND a.ID < '3101100000000' 
// 110798			WHERE a.ID >= '3101100000000' AND a.ID < '3101701500000' 
// 108410			WHERE a.ID >= '3101701500000' AND a.ID < '3102100100000' 
// 119878			WHERE a.ID >= '3102100100000' AND a.ID < '3102500000000'
// 113097			WHERE a.ID >= '3102500000000' AND a.ID < '3141000000000'
// 110179			WHERE a.ID >= '3141000000000' AND a.ID < '3290000000000' 
// 112997			WHERE a.ID >= '3290000000000' AND a.ID < '3540000000000'
// 104200			WHERE a.ID >= '3540000000000' AND a.ID < '3740000000000'
// 97641			WHERE a.ID >= '3740000000000' 

		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		while($data = $db_att->get_array()){
			$PER_CARDNO = trim($data[ID]);
			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = '$PER_CARDNO' ";
			$db_dpis->send_cmd($cmd);
			$db_dpis->show_error();
			$data_dpis = $db_dpis->get_array();
			$PER_ID = $data_dpis[NEW_CODE] + 0;

			if ($PER_ID) {
				$EFFECTIVEDATE = trim($data[MP_POS_DATE]);
				if (!$EFFECTIVEDATE) $EFFECTIVEDATE = "-";
				$DOCNO = trim($data[POS_NUM_CODE_SIT_ABB]).trim($data[MP_COMMAND_NUM]);
				$CUR_YEAR = trim($data[CUR_YEAR]);
				if (trim($CUR_YEAR)) $DOCNO .= '/'.trim($CUR_YEAR);
				$DOCDATE = trim($data[MP_COMMAND_DATE]);

				$DOCNO_EDIT = trim($data[POS_NUM_CODE_SIT_ABB_EDIT]).trim($data[MP_COMMAND_NUM_EDIT]);
				$CUR_YEAR_EDIT = trim($data[CUR_YEAR_EDIT]);
				if (trim($CUR_YEAR_EDIT)) $DOCNO_EDIT .= '/'.trim($CUR_YEAR_EDIT);
				$DOCDATE_EDIT = trim($data[MP_COMMAND_DATE_EDIT]);

				if (substr($DOCDATE,0,4)=="0425") $DOCDATE = "1984".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0009") $DOCDATE = "1991".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0008") $DOCDATE = "1992".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0383") $DOCDATE = "1993".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="1822") $DOCDATE = "1993".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0006" && $CUR_YEAR=="2537") $DOCDATE = "1994".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0286" && $CUR_YEAR=="2537") $DOCDATE = "1994".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0357") $DOCDATE = "1994".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0364") $DOCDATE = "1994".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0484") $DOCDATE = "1994".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="1614" && $CUR_YEAR=="2537") $DOCDATE = "1994".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="1814") $DOCDATE = "1994".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="1834") $DOCDATE = "1994".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0285") $DOCDATE = "1995".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0284" && $CUR_YEAR=="2539") $DOCDATE = "1996".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0433" && $CUR_YEAR=="2539") $DOCDATE = "1996".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0486") $DOCDATE = "1996".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0636") $DOCDATE = "1996".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0716") $DOCDATE = "1996".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0996") $DOCDATE = "1996".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="1516") $DOCDATE = "1996".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="1716") $DOCDATE = "1996".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="1816") $DOCDATE = "1996".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0293") $DOCDATE = "1997".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0523") $DOCDATE = "1997".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0607") $DOCDATE = "1997".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0708") $DOCDATE = "1998".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="1608") $DOCDATE = "1998".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="1708" && $CUR_YEAR=="2541") $DOCDATE = "1998".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="1709" && $CUR_YEAR=="2542") $DOCDATE = "1999".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0438") $DOCDATE = "2001".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0499") $DOCDATE = "2001".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0711") $DOCDATE = "2001".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="1711") $DOCDATE = "2001".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0002") $DOCDATE = "2002".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0428") $DOCDATE = "2002".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0437") $DOCDATE = "2002".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0003") $DOCDATE = "2003".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0429") $DOCDATE = "2003".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0433" && $CUR_YEAR=="2546") $DOCDATE = "2003".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0583") $DOCDATE = "2003".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0713") $DOCDATE = "2003".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="1713") $DOCDATE = "2003".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0004") $DOCDATE = "2004".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0286" && $CUR_YEAR=="2547") $DOCDATE = "2004".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="1514") $DOCDATE = "2004".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="1614" && $CUR_YEAR=="2547") $DOCDATE = "2004".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="1714") $DOCDATE = "2004".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0005") $DOCDATE = "2005".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0289" && $CUR_YEAR=="2548") $DOCDATE = "2005".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0295") $DOCDATE = "2005".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0495") $DOCDATE = "2005".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0006" && $CUR_YEAR=="2549") $DOCDATE = "2006".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0284" && $CUR_YEAR=="2549") $DOCDATE = "2006".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0289" && $CUR_YEAR=="2549") $DOCDATE = "2006".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0294") $DOCDATE = "2006".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0494") $DOCDATE = "2006".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0288") $DOCDATE = "2007".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="1707") $DOCDATE = "2007".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0292") $DOCDATE = "2008".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0492") $DOCDATE = "2008".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="1708" && $CUR_YEAR=="2551") $DOCDATE = "2008".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0491") $DOCDATE = "2009".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="1709" && $CUR_YEAR=="2552") $DOCDATE = "2009".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0290") $DOCDATE = "2010".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0490") $DOCDATE = "2010".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="1710") $DOCDATE = "2010".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="2286") $DOCDATE = "1986".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="2546") $DOCDATE = "2003".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="4904") $DOCDATE = "2004".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="5004") $DOCDATE = "2004".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="2305") $DOCDATE = "2005".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="2548") $DOCDATE = "2005".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="2549") $DOCDATE = "2006".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="2550") $DOCDATE = "2007".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="5007") $DOCDATE = "2007".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="3010") $DOCDATE = "2010".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="5010") $DOCDATE = "2010".substr($DOCDATE,4);

				if (substr($EFFECTIVEDATE,0,4)=="0494") $EFFECTIVEDATE = "2006".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0291") $EFFECTIVEDATE = "1977".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1857" && $CUR_YEAR=="2533") $EFFECTIVEDATE = "1990".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1857" && $CUR_YEAR=="2535") $EFFECTIVEDATE = "1992".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1857" && $CUR_YEAR=="2536") $EFFECTIVEDATE = "1993".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1857" && $CUR_YEAR=="2537") $EFFECTIVEDATE = "1994".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1857" && $CUR_YEAR=="2538") $EFFECTIVEDATE = "1995".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1857" && $CUR_YEAR=="2539") $EFFECTIVEDATE = "1996".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1857" && $CUR_YEAR=="2540") $EFFECTIVEDATE = "1997".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1857" && $CUR_YEAR=="2541") $EFFECTIVEDATE = "1998".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1857" && $CUR_YEAR=="2542") $EFFECTIVEDATE = "1999".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0289" && $CUR_YEAR=="2543") $EFFECTIVEDATE = "2000".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0001") $EFFECTIVEDATE = "2001".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1712") $EFFECTIVEDATE = "2002".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0287") $EFFECTIVEDATE = "2003".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0703") $EFFECTIVEDATE = "2003".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1003") $EFFECTIVEDATE = "2003".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1713") $EFFECTIVEDATE = "2003".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0004") $EFFECTIVEDATE = "2004".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0286") $EFFECTIVEDATE = "2004".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0289" && $CUR_YEAR=="2547") $EFFECTIVEDATE = "2004".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0296") $EFFECTIVEDATE = "2004".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1714") $EFFECTIVEDATE = "2004".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0005") $EFFECTIVEDATE = "2005".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1857" && $CUR_YEAR=="2548") $EFFECTIVEDATE = "2005".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0006") $EFFECTIVEDATE = "2006".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0294") $EFFECTIVEDATE = "2006".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1711" && $CUR_YEAR=="2549") $EFFECTIVEDATE = "2006".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0007") $EFFECTIVEDATE = "2007".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0293") $EFFECTIVEDATE = "2007".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1707") $EFFECTIVEDATE = "2007".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0008") $EFFECTIVEDATE = "2008".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0492") $EFFECTIVEDATE = "2008".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1708" && $CUR_YEAR=="2551") $EFFECTIVEDATE = "2008".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0009") $EFFECTIVEDATE = "2009".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0491") $EFFECTIVEDATE = "2009".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1708" && $CUR_YEAR=="2552") $EFFECTIVEDATE = "2009".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1709") $EFFECTIVEDATE = "2009".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0490") $EFFECTIVEDATE = "2010".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1711" && $CUR_YEAR=="2554") $EFFECTIVEDATE = "2011".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="4679") $EFFECTIVEDATE = "1979".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="2546") $EFFECTIVEDATE = "2003".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="5004") $EFFECTIVEDATE = "2004".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="2305") $EFFECTIVEDATE = "2005".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="2548") $EFFECTIVEDATE = "2005".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="2016") $EFFECTIVEDATE = "2006".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="2549") $EFFECTIVEDATE = "2006".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="2550") $EFFECTIVEDATE = "2007".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="4680") $EFFECTIVEDATE = "2010".substr($EFFECTIVEDATE,4);

				$POS_NO_NAME = trim($data[POS_NUM_NAME]);
				$POS_NO_NAME = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($POS_NO_NAME)));
				$POS_NO_NAME = str_replace("'", "", trim($POS_NO_NAME));
				$POS_NO = trim($data[POS_NUM_CODE]);
				$PN_CODE = trim($data[CATEGORY_SAL_CODE]).trim($data[WORK_LINE_CODE]);
				$PN_NAME = trim($data[WORK_LINE_NAME]);
				if ($PN_CODE=="00000" || $PN_CODE=="0203" || $PN_CODE=="99903") $PN_CODE = "";
				if ((!$PN_CODE || strlen($PN_CODE != 6)) && $PN_NAME) { 
					$cmd = " select PN_CODE from PER_POS_NAME where PN_NAME = '$PN_NAME' ";
					$db_dpis->send_cmd($cmd);
					$data_dpis = $db_dpis->get_array();
					$PN_CODE = trim($data_dpis[PN_CODE]);
				} 
				if ($PN_CODE=="01") $PN_CODE = "";
				if ($PN_CODE=="06") $PN_CODE = "";
				if ($PN_CODE=="0100") $PN_CODE = "";
				if ($PN_CODE=="030203") $PN_CODE = "";
				if ($PN_CODE=="063420") $PN_CODE = "";
				if ($PN_CODE=="070100") $PN_CODE = "";
				if ($PN_NAME=="ครูชั้น 1") $PN_CODE = "063401";
				elseif ($PN_NAME=="ครูชั้น 2") $PN_CODE = "063402";
				elseif ($PN_NAME=="ครูชั้น 3") $PN_CODE = "063403";
				elseif ($PN_NAME=="ครูชั้น 4") $PN_CODE = "063404";

				$POH_ORG1 = "กรุงเทพมหานคร";
				$POH_ORG2 = trim($data[DEPARTMENT_NAME]);
				$POH_ORG2 = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($POH_ORG2)));
				$POH_ORG2 = str_replace("'", "", trim($POH_ORG2));
				$POH_ORG3 = trim($data[DIVISION_NAME]);
				$POH_ORG3 = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($POH_ORG3)));
				$POH_ORG3 = str_replace("'", "", trim($POH_ORG3));
				if (strpos($POH_ORG3,"สำนักงานเขต") !== false && !$POH_ORG2) $POH_ORG2 = "สำนักงานเขต";
				$POH_UNDER_ORG1 = trim($data[SECTION_NAME]);
				$POH_UNDER_ORG1 = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($POH_UNDER_ORG1)));
				$POH_UNDER_ORG1 = str_replace("'", "", trim($POH_UNDER_ORG1));
				$POH_UNDER_ORG2 = trim($data[JOB_NAME]);
				$POH_UNDER_ORG2 = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($POH_UNDER_ORG2)));
				$POH_UNDER_ORG2 = str_replace("'", "", trim($POH_UNDER_ORG2));
				$POH_ORG = trim((($POH_UNDER_ORG2=="-") ? "":$POH_UNDER_ORG2)." ".(($POH_UNDER_ORG1=="-") ? "":$POH_UNDER_ORG1)." ".
											(($POH_ORG3=="-") ? "":$POH_ORG3)." ".$POH_ORG2);
				$SALARY = $data[SALARY];
				$SAH_SALARY_EXTRA = $data[SPECIAL_AMT];
				$POH_SALARY_POS = $data[SAL_POS_AMOUNT_2] + 0;
				if ($POH_SALARY_POS==0) $POH_SALARY_POS = $data[SAL_POS_AMOUNT_1] + 0;
				$FLAG_TO_NAME = trim($data[FLAG_TO_NAME]);
				$REMARK = trim($data[REMARK]);
				$FLAG_TO_NAME_CODE = trim($data[FLAG_TO_NAME_CODE]);
				$MOV_CODE = trim($data[MOVEMENT_CODE]);

	//			if (!$MOV_CODE) { 
					$cmd = " select MOV_CODE, MOV_TYPE from PER_MOVMENT where MOV_NAME = '$FLAG_TO_NAME' ";
					$db_dpis->send_cmd($cmd);
	//				$db_dpis->show_error();
					$data_dpis = $db_dpis->get_array();
					$MOV_CODE = trim($data_dpis[MOV_CODE]);
					$MOV_TYPE = trim($data_dpis[MOV_TYPE]);
					if (!$MOV_CODE) { 
						if ($FLAG_TO_NAME=="ปรับอัตราค่าจ้างตามมติ ครม. 2548") $MOV_CODE = "20028"; 
						elseif ($FLAG_TO_NAME=="เลื่อนขั้นค่าจ้างประจำปี") $MOV_CODE = "20020"; 
						elseif ($FLAG_TO_NAME=="ปรับอัตราค่าจ้างตามมติ ครม. 2550") $MOV_CODE = "20028"; 
						elseif ($FLAG_TO_NAME=="อื่นๆ" || $FLAG_TO_NAME=="อื่น ๆ") $MOV_CODE = "0"; 
						elseif ($FLAG_TO_NAME=="จ้างและแต่งตั้งลูกจ้างประจำ") $MOV_CODE = "41"; 
						elseif ($FLAG_TO_NAME=="ไม่ได้เลื่อนขั้นเงินเดือน") $MOV_CODE = "20022"; 
						elseif ($FLAG_TO_NAME=="ปรับอัตราค่าจ้างตามมติ ครม.") $MOV_CODE = "20028"; 
						elseif ($FLAG_TO_NAME=="ไม่ได้เลื่อนขั้นค่าจ้างประจำปี") $MOV_CODE = "20022"; 
						elseif ($FLAG_TO_NAME=="จ้างและแต่งตั้ง") $MOV_CODE = "20001"; 
						elseif ($FLAG_TO_NAME=="แต่งตั้ง (ย้าย)") $MOV_CODE = "3"; 
						elseif ($FLAG_TO_NAME=="การเลื่อนขั้นค่าจ้าง (เฉพาะราย)") $MOV_CODE = "52"; 
						elseif ($FLAG_TO_NAME=="จ้างและแต่งตั้งลูกจ้างชั่วคราว") $MOV_CODE = "41"; 
						elseif ($FLAG_TO_NAME=="แก้ไขคำสั่งเลื่อนขั้นค่าจ้างลูกจ้างประจำ") $MOV_CODE = "15"; 
						elseif ($FLAG_TO_NAME=="ปฏิบัติหน้าที่ต่อไป") $MOV_CODE = "42"; 
						elseif ($FLAG_TO_NAME=="ตัดโอนและเปลี่ยนตำแหน่ง") $MOV_CODE = "20002"; 
						elseif ($FLAG_TO_NAME=="ไม่ได้รับเงินตอบแทนพิเศษ") $MOV_CODE = "0"; 
						else {  
							$MOV_CODE = "0"; 
							if ($FLAG_TO_NAME) echo "ประเภทการเคลื่อนไหว->$FLAG_TO_NAME<br>"; }
					}
	//			}

				if (!$MOV_TYPE) { 
					$cmd = " select MOV_TYPE from PER_MOVMENT where MOV_CODE = '$MOV_CODE' ";
					$db_dpis->send_cmd($cmd);
		//				$db_dpis->show_error();
					$data_dpis = $db_dpis->get_array();
					$MOV_TYPE = trim($data_dpis[MOV_TYPE]);
				}

				$SALARYHIS = $POSITIONHIS = "";		
				$EX_CODE = "024";
				if ($MOV_TYPE==2)
					$SALARYHIS = 1;
				else
					$POSITIONHIS = 1;		

				$SM_CODE = "";
				if ($REMARK=="เลื่อนขั้น 0.5 ขั้น" || $REMARK=="ปรับครึ่งขั้น" || $REMARK=="ปรับ0.5ขั้น" || $REMARK=="ปรับ0.5 ขั้น" || $REMARK=="ปรับ 0.5ขั้น" || 
					$REMARK=="ปรับ 0.5 ขั้น" || $REMARK=="ปรับ .5ขั้น" || $REMARK=="ปรับ .5 ขั้น" || $REMARK=="ปรับ  0.5 ขั้น" || $REMARK=="ปรับ  0.5  ขั้น") 
					$SM_CODE = "1";
				elseif ($REMARK=="หนึ่งขั้น" || $REMARK=="เลื่อนหนึ่งขั้น" || $REMARK=="เลื่อนขั้น 1 ขั้น" || $REMARK=="ปรับหนึ่งขั้น" || $REMARK=="ปรับ1ขั้น" || 
					$REMARK=="ปรับ1 ขั้น" || $REMARK=="ปรับ 1ขั้น" || $REMARK=="ปรับ 1 ขั้น" || $REMARK=="ปรับ 1.0ขั้น" || $REMARK=="ปรับ 1.0 ขั้น" || 
					$REMARK=="ปรับ  1 ขั้น" || $REMARK=="ปรับ  1  ขั้น") 
					$SM_CODE = "2";
				elseif ($REMARK=="หนึ่งขั้นครึ่ง" || $REMARK=="เลื่อนขั้น 1.5 ขั้น" || $REMARK=="ปรับหนึ่งขั้นครึ่ง" || $REMARK=="ปรับ1.5ขั้น" || $REMARK=="ปรับ 1.5ขั้น" || 
					$REMARK=="ปรับ 1.5 ขั้น") 
					$SM_CODE = "3";
				elseif ($REMARK=="สองขั้น" || $REMARK=="เลื่อนขั้น 2 ขั้น" || $REMARK=="ปรับสองขั้น" || $REMARK=="ปรับ 2 ขั้น" || $REMARK=="ปรับ 2.0 ขั้น" || 
					$REMARK=="ปรับ  2  ขั้น" ) 
					$SM_CODE = "4";
				elseif ($REMARK=="ไม่ได้เลื่อนขั้น") 
					$SM_CODE = "10";
				elseif ($FLAG_TO_NAME=="เงินตอบแทนพิเศษ 2 %" || $REMARK=="เงินตอบแทนพิเศษ 2 %") 
					{ $SM_CODE = "5"; $EX_CODE = "015"; } 
				elseif ($FLAG_TO_NAME=="เงินตอบแทนพิเศษ 4 %" || $REMARK=="เงินตอบแทนพิเศษ 4 %" || $REMARK=="ปรับ 4 เปอร์เซ็นต์") 
					{ $SM_CODE = "17";  $EX_CODE = "016"; }

				$LEVEL_NO = str_pad(trim($data[MP_CEE]), 2, "0", STR_PAD_LEFT);
				$LEVEL_NO = trim($LEVEL_NO)? "'".$LEVEL_NO."'" : "NULL";
				if ($LEVEL_NO=="'6 ว'" || $LEVEL_NO=="'6ว'") $LEVEL_NO = "'06'";
				if ($LEVEL_NO=="'7ว'" || $LEVEL_NO=="'7 ว.'" || $LEVEL_NO=="'7วช.'") $LEVEL_NO = "'07'";
				if ($LEVEL_NO=="'8 วช'" || $LEVEL_NO=="'8 ว'" || $LEVEL_NO=="'8ว'") $LEVEL_NO = "'08'";
				if ($LEVEL_NO!="'01'" && $LEVEL_NO!=="'02'" && $LEVEL_NO!="'03'" && $LEVEL_NO!="'04'" && $LEVEL_NO!="'05'" && $LEVEL_NO!="'06'" && 
					$LEVEL_NO!="'07'" && $LEVEL_NO!="'08'" && $LEVEL_NO!="'09'" && $LEVEL_NO!="'10'" && $LEVEL_NO!="'11'") $LEVEL_NO = "NULL";
				$MP_FLAG_CURRENT = trim($data[MP_FLAG_CURRENT]);
				if ($MP_FLAG_CURRENT=="1") $LAST_TRANSACTION = "Y"; 
				else $LAST_TRANSACTION = "N";

				$POH_ORG_DOPA_CODE = trim($data[DEPARTMENT_CODE]).trim($data[DIVISION_CODE]).trim($data[SECTION_CODE]).trim($data[JOB_CODE]);
				$POH_ISREAL = "Y";
				$ORDERID = $data[ORDERID];
				$ES_CODE = "02";
				$POH_SEQ_NO = $data[ORDERPIORITY];
				$POH_CMD_SEQ = $data[ORDERTH]; 

				$UPDATE_USER = 99999;
				if ($data[USER_UPDATE]) $USER_UPDATE = trim($data[USER_UPDATE]);
				else $USER_UPDATE = trim($data[USER_CREATE]);
				if ($data[UPDATE_DATE]) $UPDATE_DATE = trim($data[UPDATE_DATE]);
				else  $UPDATE_DATE = trim($data[CREATE_DATE]);
				if ($USER_UPDATE) {
					$cmd = " select ID from USER_DETAIL where USERNAME = '$USER_UPDATE' ";
					$db_dpis2->send_cmd($cmd);
	//				$db_dpis2->show_error();
					$data2 = $db_dpis2->get_array();
					if ($data2[ID]) $UPDATE_USER = $data2[ID];
				}
				if (!$UPDATE_DATE) $UPDATE_DATE = date("Y-m-d H:i:s");

				$STATUS = trim($data[STATUS]);

				if (!$ORG_NAME) $ORG_NAME = "-";
				if (!$POS_NO) $POS_NO = "-";
				if (!$DOCNO) $DOCNO = "-";
				if (!$DOCDATE) $DOCDATE = "-";
				if (!$SALARY) $SALARY = 0;
				if (!$SEQ_NO) $SEQ_NO = 1;
				if (!$CMD_SEQ || $CMD_SEQ > 20000) $CMD_SEQ = "NULL";
				if (!$SAH_PERCENT_UP) $SAH_PERCENT_UP = "NULL";
				if (!$SAH_SALARY_UP) $SAH_SALARY_UP = "NULL";
				if (!$SAH_SALARY_EXTRA) $SAH_SALARY_EXTRA = "NULL";

				$ORG_ID_1 = "NULL";
				$ORG_ID_2 = "NULL";
				$ORG_ID_3 = "NULL";
				if ($POH_ORG_DOPA_CODE) { 
					$cmd = " SELECT OL_CODE, ORG_ID, ORG_ID_REF FROM PER_ORG WHERE ORG_DOPA_CODE = '$POH_ORG_DOPA_CODE' ";
					$db_dpis2->send_cmd($cmd);
	//				$db_dpis2->show_error();
					$data2 = $db_dpis2->get_array();
					$OL_CODE = trim($data2[OL_CODE]);
					$ORG_ID = $data2[ORG_ID];
					$ORG_ID_REF = $data2[ORG_ID_REF];
					if ($OL_CODE == "03") {
						$ORG_ID_1 = $ORG_ID;
					} elseif ($OL_CODE == "04") {
						$ORG_ID_1 = $ORG_ID_REF;
						$ORG_ID_2 = $ORG_ID;
					} elseif ($OL_CODE == "05") {
						$ORG_ID_2 = $ORG_ID_REF;
						$ORG_ID_3 = $ORG_ID;
						$cmd = " SELECT ORG_ID_REF FROM PER_ORG WHERE ORG_ID = $ORG_ID_REF ";
						$db_dpis2->send_cmd($cmd);
	//					$db_dpis2->show_error();
						$data2 = $db_dpis2->get_array();
						$ORG_ID_1 = $data2[ORG_ID_REF];
					}
				}

				if ($POSITIONHIS) {
					$PER_POSITIONHIS++;
					$cmd = " INSERT INTO PER_POSITIONHIS(POH_ID, PER_ID, POH_EFFECTIVEDATE, MOV_CODE, POH_ENDDATE, POH_DOCNO, 
									POH_DOCDATE, POH_POS_NO_NAME, POH_POS_NO, PM_CODE, LEVEL_NO, PL_CODE, PN_CODE, PT_CODE, CT_CODE, PV_CODE, AP_CODE, 
									POH_ORGMGT, ORG_ID_1, ORG_ID_2, ORG_ID_3, POH_UNDER_ORG1, POH_UNDER_ORG2, POH_SALARY, 
									POH_SALARY_POS, POH_REMARK, UPDATE_USER, UPDATE_DATE, PER_CARDNO, POH_ORG1, POH_ORG2, POH_ORG3, POH_ORG, 
									POH_PM_NAME, POH_PL_NAME, POH_SEQ_NO, POH_LAST_POSITION, POH_CMD_SEQ, POH_ISREAL, 
									POH_ORG_DOPA_CODE, ES_CODE, POH_LEVEL_NO, POH_REMARK1, POH_DOCNO_EDIT, POH_DOCDATE_EDIT)
									VALUES ($MAX_POH_ID, $PER_ID, '$EFFECTIVEDATE', '$MOV_CODE', NULL, '$DOCNO', 
									'$DOCDATE', '$POS_NO_NAME', '$POS_NO', '$PM_CODE', $LEVEL_NO, NULL, '$PN_CODE', NULL, '140', NULL, NULL, 2, NULL, NULL, NULL, 
									'$POH_UNDER_ORG1', '$POH_UNDER_ORG2', $SALARY, $POH_SALARY_POS, '$REMARK', $UPDATE_USER, '$UPDATE_DATE', 
									'$PER_CARDNO', '$POH_ORG1', '$POH_ORG2', '$POH_ORG3', '$POH_ORG', '$PM_NAME', '$PN_NAME', $SEQ_NO, 
									'$LAST_TRANSACTION', $CMD_SEQ, '$POH_ISREAL', '$POH_ORG_DOPA_CODE', '$ES_CODE', $LEVEL_NO, '$FLAG_TO_NAME', 
									'$DOCNO_EDIT', '$DOCDATE_EDIT') ";
					$db_dpis->send_cmd($cmd);

					$cmd1 = " SELECT POH_ID FROM PER_POSITIONHIS WHERE POH_ID = $MAX_POH_ID "; 
					$count_data = $db_dpis1->send_cmd($cmd1);
					if (!$count_data) {
						echo "$cmd<br>==================<br>";
						$db_dpis->show_error();
						echo "<br>end ". ++$i  ."=======================<br>";
					}
					$MAX_POH_ID++;
				} // end if						
				if ($SALARYHIS) {
					$PER_SALARYHIS++;
					$cmd = " INSERT INTO PER_SALARYHIS(SAH_ID, PER_ID, SAH_EFFECTIVEDATE, MOV_CODE, SAH_SALARY, 
									SAH_DOCNO, SAH_DOCDATE, SAH_ENDDATE, UPDATE_USER, UPDATE_DATE, PER_CARDNO, SAH_PERCENT_UP, 
									SAH_SALARY_UP, SAH_SALARY_EXTRA, SAH_SEQ_NO, SAH_REMARK, LEVEL_NO, SAH_POS_NO_NAME, SAH_POS_NO, 
									SAH_POSITION, SAH_ORG, EX_CODE, SAH_PAY_NO, SAH_LAST_SALARY, SM_CODE, SAH_CMD_SEQ, SAH_ORG_DOPA_CODE)
									VALUES ($MAX_SAH_ID, $PER_ID, '$EFFECTIVEDATE', '$MOV_CODE', $SALARY, '$DOCNO', 
									'$DOCDATE', NULL, $UPDATE_USER, '$UPDATE_DATE', '$PER_CARDNO', $SAH_PERCENT_UP, $SAH_SALARY_UP, 
									$SAH_SALARY_EXTRA, $SEQ_NO, '$REMARK', $LEVEL_NO, '$POS_NO_NAME', '$POS_NO', 
									'$PN_NAME', '$POH_ORG', '$EX_CODE', '$SAH_PAY_NO', '$LAST_TRANSACTION', '$SM_CODE', $CMD_SEQ, '$SAH_ORG_DOPA_CODE') ";
					$db_dpis->send_cmd($cmd);

					$cmd1 = " SELECT SAH_ID FROM PER_SALARYHIS WHERE SAH_ID = $MAX_SAH_ID "; 
					$count_data = $db_dpis1->send_cmd($cmd1);
					if (!$count_data) {
						echo "$cmd<br>==================<br>";
						$db_dpis->show_error();
						echo "<br>end ". ++$j  ."=======================<br>";
					}
					$MAX_SAH_ID++;
				} // end if		
			} else {
//				echo "$PER_CARDNO<br>";
			} // end if						
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_POSITIONHIS ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] - $CNT_POH_ID;
		echo "PER_POSITIONHIS - $PER_POSITIONHIS - $COUNT_NEW<br>";

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_SALARYHIS ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] - $CNT_SAH_ID;
		echo "PER_SALARYHIS - $PER_SALARYHIS - $COUNT_NEW<br>";

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_att->free_result();
		$db_att1->free_result();
	} // end if

	if( $command=='POSTEMPHIS' ){
		$cmd = " delete from PER_POSITIONHIS where PER_ID in (select PER_ID from PER_PERSONAL where PER_TYPE = 4) ";
		//$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " delete from PER_SALARYHIS where PER_ID in (select PER_ID from PER_PERSONAL where PER_TYPE = 4) ";
		//$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " select count(PER_ID) as CNT_POH_ID from PER_POSITIONHIS ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$CNT_POH_ID = $data2[CNT_POH_ID] + 0;

		$cmd = " select count(PER_ID) as CNT_SAH_ID from PER_SALARYHIS ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$CNT_SAH_ID = $data2[CNT_SAH_ID] + 0;

		$cmd = " select max(POH_ID) as MAX_ID from PER_POSITIONHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_POH_ID = $data[MAX_ID] + 1;

		$cmd = " select max(SAH_ID) as MAX_ID from PER_SALARYHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_SAH_ID = $data[MAX_ID] + 1;

		$cmd = " SELECT a.ID, a.FLAG_PERSON_TYPE, ORDER_MOVE_POSITION, a.BOARD_TYPE, a.MP_YEAR, a.POS_NUM_CODE_SIT, 
                        a.POS_NUM_CODE_SIT_ABB, a.MP_COMMAND_NUM, a.CUR_YEAR, to_char(a.MP_COMMAND_DATE,'yyyy-mm-dd') as MP_COMMAND_DATE, 
                        to_char(a.MP_POS_DATE,'yyyy-mm-dd') as MP_POS_DATE, a.FLAG_TO_NAME_CODE, a.FLAG_TO_NAME,
						to_char(a.START_DATE,'yyyy-mm-dd') as START_DATE, to_char(a.END_DATE,'yyyy-mm-dd') as END_DATE, 
						a.JOB_CODE, a.JOB_NAME, a.SECTION_CODE, a.SECTION_NAME, a.DIVISION_CODE, a.DIVISION_NAME, 
						a.DEPARTMENT_CODE, a.DEPARTMENT_NAME, a.POS_NUM_CODE, a.POS_NUM_NAME, a.POS_NUM_CODE_R, 
						a.CLUSTER_CODE, a.CATEGORY_SAL_CODE, a.CATEGORY_SAL_NAME, a.WORK_LINE_CODE, a.WORK_LINE_NAME, 
						a.SALARY_LEVEL_CODE, a.SALARY, a.COST_LIVING_AMOUNT, a.FLAG_POS_STATUS, a.FUNC_ID, a.FUNC_NAME, 
						a.BUDGET_TYPE, a.ITEM_ID, a.DETAIL, a.BUDGET_CODE, a.BUDGET_YEAR, a.SOURCE_ID, a.BOOK_ID, 
						a.FISCAL_YEAR, a.EXPENDITURE_ID, a.FUNC_YEAR, a.FUNC_SEQ, a.SECTOR_ID, a.SECTOR_NAME, 
						a.PROGRAM_ID, a.PROGRAM_NAME, a.EXP_OBJECT_ID, a.EXP_OBJECT_NAME, a.EXP_SUBOBJECT_ID, 
						a.EXP_SUBOBJECT_NAME, a.CENTRAL_FUND_ID, a.S_PROJECT_ID, a.S_ACTIVITY_ID, a.S_EXP_OBJECT_ID, 
						a.USER_CREATE, to_char(a.CREATE_DATE,'yyyy-mm-dd') as CREATE_DATE, a.USER_UPDATE,     
						to_char(a.UPDATE_DATE,'yyyy-mm-dd') as UPDATE_DATE, REMARK, MP_FLAG_CURRENT, POS_NUM_CODE_SIT_CODE
                        FROM HR_POSITION_EMPTEMP a
                        ORDER BY ID, FLAG_PERSON_TYPE, ORDER_MOVE_POSITION ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		while($data = $db_att->get_array()){
			$PER_CARDNO = trim($data[ID]);
			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = '$PER_CARDNO' ";
			$db_dpis->send_cmd($cmd);
			$db_dpis->show_error();
			$data_dpis = $db_dpis->get_array();
			$PER_ID = $data_dpis[NEW_CODE] + 0;

			if ($PER_ID) {
				$EFFECTIVEDATE = trim($data[MP_POS_DATE]);
				if (!$EFFECTIVEDATE) $EFFECTIVEDATE = "-";
				$DOCNO = trim($data[POS_NUM_CODE_SIT_ABB]).trim($data[MP_COMMAND_NUM]);
				$CUR_YEAR = trim($data[CUR_YEAR]);
				if (trim($CUR_YEAR)) $DOCNO .= '/'.trim($CUR_YEAR);
				$DOCDATE = trim($data[MP_COMMAND_DATE]);

				$DOCNO_EDIT = trim($data[POS_NUM_CODE_SIT_ABB_EDIT]).trim($data[MP_COMMAND_NUM_EDIT]);
				$CUR_YEAR_EDIT = trim($data[CUR_YEAR_EDIT]);
				if (trim($CUR_YEAR_EDIT)) $DOCNO_EDIT .= '/'.trim($CUR_YEAR_EDIT);
				$DOCDATE_EDIT = trim($data[MP_COMMAND_DATE_EDIT]);

				if (substr($DOCDATE,0,4)=="0425") $DOCDATE = "1984".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0009") $DOCDATE = "1991".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0008") $DOCDATE = "1992".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0383") $DOCDATE = "1993".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="1822") $DOCDATE = "1993".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0006" && $CUR_YEAR=="2537") $DOCDATE = "1994".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0286" && $CUR_YEAR=="2537") $DOCDATE = "1994".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0357") $DOCDATE = "1994".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0364") $DOCDATE = "1994".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0484") $DOCDATE = "1994".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="1614" && $CUR_YEAR=="2537") $DOCDATE = "1994".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="1814") $DOCDATE = "1994".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="1834") $DOCDATE = "1994".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0285") $DOCDATE = "1995".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0284" && $CUR_YEAR=="2539") $DOCDATE = "1996".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0433" && $CUR_YEAR=="2539") $DOCDATE = "1996".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0486") $DOCDATE = "1996".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0636") $DOCDATE = "1996".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0716") $DOCDATE = "1996".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0996") $DOCDATE = "1996".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="1516") $DOCDATE = "1996".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="1716") $DOCDATE = "1996".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="1816") $DOCDATE = "1996".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0293") $DOCDATE = "1997".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0523") $DOCDATE = "1997".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0607") $DOCDATE = "1997".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0708") $DOCDATE = "1998".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="1608") $DOCDATE = "1998".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="1708" && $CUR_YEAR=="2541") $DOCDATE = "1998".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="1709" && $CUR_YEAR=="2542") $DOCDATE = "1999".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0438") $DOCDATE = "2001".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0499") $DOCDATE = "2001".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0711") $DOCDATE = "2001".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="1711") $DOCDATE = "2001".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0002") $DOCDATE = "2002".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0428") $DOCDATE = "2002".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0437") $DOCDATE = "2002".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0003") $DOCDATE = "2003".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0429") $DOCDATE = "2003".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0433" && $CUR_YEAR=="2546") $DOCDATE = "2003".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0583") $DOCDATE = "2003".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0713") $DOCDATE = "2003".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="1713") $DOCDATE = "2003".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0004") $DOCDATE = "2004".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0286" && $CUR_YEAR=="2547") $DOCDATE = "2004".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="1514") $DOCDATE = "2004".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="1614" && $CUR_YEAR=="2547") $DOCDATE = "2004".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="1714") $DOCDATE = "2004".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0005") $DOCDATE = "2005".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0289" && $CUR_YEAR=="2548") $DOCDATE = "2005".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0295") $DOCDATE = "2005".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0495") $DOCDATE = "2005".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0006" && $CUR_YEAR=="2549") $DOCDATE = "2006".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0284" && $CUR_YEAR=="2549") $DOCDATE = "2006".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0289" && $CUR_YEAR=="2549") $DOCDATE = "2006".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0294") $DOCDATE = "2006".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0494") $DOCDATE = "2006".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0288") $DOCDATE = "2007".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="1707") $DOCDATE = "2007".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0292") $DOCDATE = "2008".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0492") $DOCDATE = "2008".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="1708" && $CUR_YEAR=="2551") $DOCDATE = "2008".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0491") $DOCDATE = "2009".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="1709" && $CUR_YEAR=="2552") $DOCDATE = "2009".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0290") $DOCDATE = "2010".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="0490") $DOCDATE = "2010".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="1710") $DOCDATE = "2010".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="2286") $DOCDATE = "1986".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="2546") $DOCDATE = "2003".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="4904") $DOCDATE = "2004".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="5004") $DOCDATE = "2004".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="2305") $DOCDATE = "2005".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="2548") $DOCDATE = "2005".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="2549") $DOCDATE = "2006".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="2550") $DOCDATE = "2007".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="5007") $DOCDATE = "2007".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="3010") $DOCDATE = "2010".substr($DOCDATE,4);
				elseif (substr($DOCDATE,0,4)=="5010") $DOCDATE = "2010".substr($DOCDATE,4);

				if (substr($EFFECTIVEDATE,0,4)=="0494") $EFFECTIVEDATE = "2006".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0291") $EFFECTIVEDATE = "1977".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1857" && $CUR_YEAR=="2533") $EFFECTIVEDATE = "1990".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1857" && $CUR_YEAR=="2535") $EFFECTIVEDATE = "1992".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1857" && $CUR_YEAR=="2536") $EFFECTIVEDATE = "1993".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1857" && $CUR_YEAR=="2537") $EFFECTIVEDATE = "1994".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1857" && $CUR_YEAR=="2538") $EFFECTIVEDATE = "1995".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1857" && $CUR_YEAR=="2539") $EFFECTIVEDATE = "1996".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1857" && $CUR_YEAR=="2540") $EFFECTIVEDATE = "1997".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1857" && $CUR_YEAR=="2541") $EFFECTIVEDATE = "1998".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1857" && $CUR_YEAR=="2542") $EFFECTIVEDATE = "1999".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0289" && $CUR_YEAR=="2543") $EFFECTIVEDATE = "2000".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0001") $EFFECTIVEDATE = "2001".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1712") $EFFECTIVEDATE = "2002".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0287") $EFFECTIVEDATE = "2003".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0703") $EFFECTIVEDATE = "2003".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1003") $EFFECTIVEDATE = "2003".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1713") $EFFECTIVEDATE = "2003".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0004") $EFFECTIVEDATE = "2004".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0286") $EFFECTIVEDATE = "2004".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0289" && $CUR_YEAR=="2547") $EFFECTIVEDATE = "2004".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0296") $EFFECTIVEDATE = "2004".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1714") $EFFECTIVEDATE = "2004".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0005") $EFFECTIVEDATE = "2005".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1857" && $CUR_YEAR=="2548") $EFFECTIVEDATE = "2005".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0006") $EFFECTIVEDATE = "2006".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0294") $EFFECTIVEDATE = "2006".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1711" && $CUR_YEAR=="2549") $EFFECTIVEDATE = "2006".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0007") $EFFECTIVEDATE = "2007".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0293") $EFFECTIVEDATE = "2007".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1707") $EFFECTIVEDATE = "2007".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0008") $EFFECTIVEDATE = "2008".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0492") $EFFECTIVEDATE = "2008".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1708" && $CUR_YEAR=="2551") $EFFECTIVEDATE = "2008".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0009") $EFFECTIVEDATE = "2009".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0491") $EFFECTIVEDATE = "2009".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1708" && $CUR_YEAR=="2552") $EFFECTIVEDATE = "2009".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1709") $EFFECTIVEDATE = "2009".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="0490") $EFFECTIVEDATE = "2010".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="1711" && $CUR_YEAR=="2554") $EFFECTIVEDATE = "2011".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="4679") $EFFECTIVEDATE = "1979".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="2546") $EFFECTIVEDATE = "2003".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="5004") $EFFECTIVEDATE = "2004".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="2305") $EFFECTIVEDATE = "2005".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="2548") $EFFECTIVEDATE = "2005".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="2016") $EFFECTIVEDATE = "2006".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="2549") $EFFECTIVEDATE = "2006".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="2550") $EFFECTIVEDATE = "2007".substr($EFFECTIVEDATE,4);
				elseif (substr($EFFECTIVEDATE,0,4)=="4680") $EFFECTIVEDATE = "2010".substr($EFFECTIVEDATE,4);

				$POS_NO_NAME = trim($data[POS_NUM_NAME]);
				$POS_NO_NAME = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($POS_NO_NAME)));
				$POS_NO_NAME = str_replace("'", "", trim($POS_NO_NAME));
				$POS_NO = trim($data[POS_NUM_CODE]);
				$TP_CODE = trim($data[CATEGORY_SAL_CODE]).trim($data[WORK_LINE_CODE]);
				$TP_NAME = trim($data[WORK_LINE_NAME]);
				if ($TP_CODE=="00000" || $TP_CODE=="0203" || $TP_CODE=="99903") $TP_CODE = "";
				if ((!$TP_CODE || strlen($TP_CODE != 6)) && $TP_NAME) { 
					$cmd = " select TP_CODE from PER_TEMP_POS_NAME where TP_NAME = '$TP_NAME' ";
					$db_dpis->send_cmd($cmd);
					$data_dpis = $db_dpis->get_array();
					$TP_CODE = trim($data_dpis[TP_CODE]);
				}  
				$POH_ORG1 = "กรุงเทพมหานคร";
				$POH_ORG2 = trim($data[DEPARTMENT_NAME]);
				$POH_ORG2 = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($POH_ORG2)));
				$POH_ORG2 = str_replace("'", "", trim($POH_ORG2));
				$POH_ORG3 = trim($data[DIVISION_NAME]);
				$POH_ORG3 = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($POH_ORG3)));
				$POH_ORG3 = str_replace("'", "", trim($POH_ORG3));
				if (strpos($POH_ORG3,"สำนักงานเขต") !== false && !$POH_ORG2) $POH_ORG2 = "สำนักงานเขต";
				$POH_UNDER_ORG1 = trim($data[SECTION_NAME]);
				$POH_UNDER_ORG1 = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($POH_UNDER_ORG1)));
				$POH_UNDER_ORG1 = str_replace("'", "", trim($POH_UNDER_ORG1));
				$POH_UNDER_ORG2 = trim($data[JOB_NAME]);
				$POH_UNDER_ORG2 = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($POH_UNDER_ORG2)));
				$POH_UNDER_ORG2 = str_replace("'", "", trim($POH_UNDER_ORG2));
				$POH_ORG = trim((($POH_UNDER_ORG2=="-") ? "":$POH_UNDER_ORG2)." ".(($POH_UNDER_ORG1=="-") ? "":$POH_UNDER_ORG1)." ".
											(($POH_ORG3=="-") ? "":$POH_ORG3)." ".$POH_ORG2);
				$SALARY = $data[SALARY];
				$SAH_SALARY_EXTRA = $data[SPECIAL_AMT];
				$POH_SALARY_POS = $data[SAL_POS_AMOUNT_2] + 0;
				if ($POH_SALARY_POS==0) $POH_SALARY_POS = $data[SAL_POS_AMOUNT_1] + 0;
				$FLAG_TO_NAME = trim($data[FLAG_TO_NAME]);
				$FUNC_NAME = trim($data[FUNC_NAME]);
				$REMARK = trim($data[REMARK]);
				$FLAG_TO_NAME_CODE = trim($data[FLAG_TO_NAME_CODE]);
				$MOV_CODE = trim($data[MOVEMENT_CODE]);

				// แก้ตาม HR_FLAG_TO_NAME_EMP

	//			if (!$MOV_CODE) { 
					$cmd = " select MOV_CODE, MOV_TYPE from PER_MOVMENT where MOV_NAME = '$FLAG_TO_NAME' ";
					$db_dpis->send_cmd($cmd);
	//				$db_dpis->show_error();
					$data_dpis = $db_dpis->get_array();
					$MOV_CODE = trim($data_dpis[MOV_CODE]);
					$MOV_TYPE = trim($data_dpis[MOV_TYPE]);
					if (!$MOV_CODE) { 
						if ($FLAG_TO_NAME=="ปรับอัตราค่าจ้างตามมติ ครม. 2548") $MOV_CODE = "20028"; 
						elseif ($FLAG_TO_NAME=="เลื่อนขั้นค่าจ้างประจำปี") $MOV_CODE = "20020"; 
						elseif ($FLAG_TO_NAME=="ปรับอัตราค่าจ้างตามมติ ครม. 2550") $MOV_CODE = "20028"; 
						elseif ($FLAG_TO_NAME=="อื่นๆ") $MOV_CODE = "0"; 
						elseif ($FLAG_TO_NAME=="จ้างและแต่งตั้งลูกจ้างประจำ") $MOV_CODE = "41"; 
						elseif ($FLAG_TO_NAME=="ไม่ได้เลื่อนขั้นเงินเดือน") $MOV_CODE = "20022"; 
						elseif ($FLAG_TO_NAME=="ปรับอัตราค่าจ้างตามมติ ครม.") $MOV_CODE = "20028"; 
						elseif ($FLAG_TO_NAME=="ไม่ได้เลื่อนขั้นค่าจ้างประจำปี") $MOV_CODE = "20022"; 
						elseif ($FLAG_TO_NAME=="จ้างและแต่งตั้ง") $MOV_CODE = "20001"; 
						elseif ($FLAG_TO_NAME=="แต่งตั้ง (ย้าย)") $MOV_CODE = "3"; 
						elseif ($FLAG_TO_NAME=="การเลื่อนขั้นค่าจ้าง (เฉพาะราย)") $MOV_CODE = "52"; 
						elseif ($FLAG_TO_NAME=="จ้างและแต่งตั้งลูกจ้างชั่วคราว") $MOV_CODE = "41"; 
						else {  
							$MOV_CODE = "0"; 
							if ($FLAG_TO_NAME) echo "ประเภทการเคลื่อนไหว->$FLAG_TO_NAME<br>"; }
					}
	//			}

				if (!$MOV_TYPE) { 
					$cmd = " select MOV_TYPE from PER_MOVMENT where MOV_CODE = '$MOV_CODE' ";
					$db_dpis->send_cmd($cmd);
		//				$db_dpis->show_error();
					$data_dpis = $db_dpis->get_array();
					$MOV_TYPE = trim($data_dpis[MOV_TYPE]);
				}

				$SALARYHIS = $POSITIONHIS = "";		
				$EX_CODE = "024";
				if ($MOV_TYPE==2)
					$SALARYHIS = 1;
				else
					$POSITIONHIS = 1;		

				$SM_CODE = "";
				if ($REMARK=="เลื่อนขั้น 0.5 ขั้น" || $REMARK=="ปรับครึ่งขั้น" || $REMARK=="ปรับ0.5ขั้น" || $REMARK=="ปรับ0.5 ขั้น" || $REMARK=="ปรับ 0.5ขั้น" || 
					$REMARK=="ปรับ 0.5 ขั้น" || $REMARK=="ปรับ .5ขั้น" || $REMARK=="ปรับ .5 ขั้น" || $REMARK=="ปรับ  0.5 ขั้น" || $REMARK=="ปรับ  0.5  ขั้น") 
					$SM_CODE = "1";
				elseif ($REMARK=="หนึ่งขั้น" || $REMARK=="เลื่อนหนึ่งขั้น" || $REMARK=="เลื่อนขั้น 1 ขั้น" || $REMARK=="ปรับหนึ่งขั้น" || $REMARK=="ปรับ1ขั้น" || 
					$REMARK=="ปรับ1 ขั้น" || $REMARK=="ปรับ 1ขั้น" || $REMARK=="ปรับ 1 ขั้น" || $REMARK=="ปรับ 1.0ขั้น" || $REMARK=="ปรับ 1.0 ขั้น" || 
					$REMARK=="ปรับ  1 ขั้น" || $REMARK=="ปรับ  1  ขั้น") 
					$SM_CODE = "2";
				elseif ($REMARK=="หนึ่งขั้นครึ่ง" || $REMARK=="เลื่อนขั้น 1.5 ขั้น" || $REMARK=="ปรับหนึ่งขั้นครึ่ง" || $REMARK=="ปรับ1.5ขั้น" || $REMARK=="ปรับ 1.5ขั้น" || 
					$REMARK=="ปรับ 1.5 ขั้น") 
					$SM_CODE = "3";
				elseif ($REMARK=="สองขั้น" || $REMARK=="เลื่อนขั้น 2 ขั้น" || $REMARK=="ปรับสองขั้น" || $REMARK=="ปรับ 2 ขั้น" || $REMARK=="ปรับ 2.0 ขั้น" || 
					$REMARK=="ปรับ  2  ขั้น" ) 
					$SM_CODE = "4";
				elseif ($REMARK=="ไม่ได้เลื่อนขั้น") 
					$SM_CODE = "10";
				elseif ($FLAG_TO_NAME=="เงินตอบแทนพิเศษ 2 %" || $REMARK=="เงินตอบแทนพิเศษ 2 %") 
					{ $SM_CODE = "5"; $EX_CODE = "015"; } 
				elseif ($FLAG_TO_NAME=="เงินตอบแทนพิเศษ 4 %" || $REMARK=="เงินตอบแทนพิเศษ 4 %" || $REMARK=="ปรับ 4 เปอร์เซ็นต์") 
					{ $SM_CODE = "17";  $EX_CODE = "016"; }

				$LEVEL_NO = str_pad(trim($data[MP_CEE]), 2, "0", STR_PAD_LEFT);
				$LEVEL_NO = trim($LEVEL_NO)? "'".$LEVEL_NO."'" : "NULL";
				if ($LEVEL_NO=="'6 ว'" || $LEVEL_NO=="'6ว'") $LEVEL_NO = "'06'";
				if ($LEVEL_NO=="'7ว'" || $LEVEL_NO=="'7 ว.'" || $LEVEL_NO=="'7วช.'") $LEVEL_NO = "'07'";
				if ($LEVEL_NO=="'8 วช'" || $LEVEL_NO=="'8 ว'" || $LEVEL_NO=="'8ว'") $LEVEL_NO = "'08'";
				if ($LEVEL_NO!="'01'" && $LEVEL_NO!=="'02'" && $LEVEL_NO!="'03'" && $LEVEL_NO!="'04'" && $LEVEL_NO!="'05'" && $LEVEL_NO!="'06'" && 
					$LEVEL_NO!="'07'" && $LEVEL_NO!="'08'" && $LEVEL_NO!="'09'" && $LEVEL_NO!="'10'" && $LEVEL_NO!="'11'") $LEVEL_NO = "NULL";
				$MP_FLAG_CURRENT = trim($data[MP_FLAG_CURRENT]);
				if ($MP_FLAG_CURRENT=="1") $LAST_TRANSACTION = "Y"; 
				else $LAST_TRANSACTION = "N";

				$POH_ORG_DOPA_CODE = trim($data[DEPARTMENT_CODE]).trim($data[DIVISION_CODE]).trim($data[SECTION_CODE]).trim($data[JOB_CODE]);
				$POH_ISREAL = "Y";
				$ORDERID = $data[ORDERID];
				$ES_CODE = "02";
				$POH_SEQ_NO = $data[ORDERPIORITY];
				$POH_CMD_SEQ = $data[ORDERTH]; 

				$UPDATE_USER = 99999;
				if ($data[USER_UPDATE]) $USER_UPDATE = trim($data[USER_UPDATE]);
				else $USER_UPDATE = trim($data[USER_CREATE]);
				if ($data[UPDATE_DATE]) $UPDATE_DATE = trim($data[UPDATE_DATE]);
				else  $UPDATE_DATE = trim($data[CREATE_DATE]);
				if ($USER_UPDATE) {
					$cmd = " select ID from USER_DETAIL where USERNAME = '$USER_UPDATE' ";
					$db_dpis2->send_cmd($cmd);
	//				$db_dpis2->show_error();
					$data2 = $db_dpis2->get_array();
					if ($data2[ID]) $UPDATE_USER = $data2[ID];
				}
				if (!$UPDATE_DATE) $UPDATE_DATE = date("Y-m-d H:i:s");

				$STATUS = trim($data[STATUS]);

				if (!$ORG_NAME) $ORG_NAME = "-";
				if (!$POS_NO) $POS_NO = "-";
				if (!$DOCNO) $DOCNO = "-";
				if (!$DOCDATE) $DOCDATE = "-";
				if (!$SALARY) $SALARY = 0;
				if (!$SEQ_NO) $SEQ_NO = 1;
				if (!$CMD_SEQ || $CMD_SEQ > 20000) $CMD_SEQ = "NULL";
				if (!$SAH_PERCENT_UP) $SAH_PERCENT_UP = "NULL";
				if (!$SAH_SALARY_UP) $SAH_SALARY_UP = "NULL";
				if (!$SAH_SALARY_EXTRA) $SAH_SALARY_EXTRA = "NULL";

				$ORG_ID_1 = "NULL";
				$ORG_ID_2 = "NULL";
				$ORG_ID_3 = "NULL";
				if ($POH_ORG_DOPA_CODE) { 
					$cmd = " SELECT OL_CODE, ORG_ID, ORG_ID_REF FROM PER_ORG WHERE ORG_DOPA_CODE = '$POH_ORG_DOPA_CODE' ";
					$db_dpis2->send_cmd($cmd);
	//				$db_dpis2->show_error();
					$data2 = $db_dpis2->get_array();
					$OL_CODE = trim($data2[OL_CODE]);
					$ORG_ID = $data2[ORG_ID];
					$ORG_ID_REF = $data2[ORG_ID_REF];
					if ($OL_CODE == "03") {
						$ORG_ID_1 = $ORG_ID;
					} elseif ($OL_CODE == "04") {
						$ORG_ID_1 = $ORG_ID_REF;
						$ORG_ID_2 = $ORG_ID;
					} elseif ($OL_CODE == "05") {
						$ORG_ID_2 = $ORG_ID_REF;
						$ORG_ID_3 = $ORG_ID;
						$cmd = " SELECT ORG_ID_REF FROM PER_ORG WHERE ORG_ID = $ORG_ID_REF ";
						$db_dpis2->send_cmd($cmd);
	//					$db_dpis2->show_error();
						$data2 = $db_dpis2->get_array();
						$ORG_ID_1 = $data2[ORG_ID_REF];
					}
				}

				if ($POSITIONHIS) {
					$PER_POSITIONHIS++;
					$cmd = " INSERT INTO PER_POSITIONHIS(POH_ID, PER_ID, POH_EFFECTIVEDATE, MOV_CODE, POH_ENDDATE, POH_DOCNO, 
									POH_DOCDATE, POH_POS_NO_NAME, POH_POS_NO, PM_CODE, LEVEL_NO, PL_CODE, TP_CODE, PT_CODE, CT_CODE, PV_CODE, AP_CODE, 
									POH_ORGMGT, ORG_ID_1, ORG_ID_2, ORG_ID_3, POH_UNDER_ORG1, POH_UNDER_ORG2, POH_SALARY, 
									POH_SALARY_POS, POH_REMARK, UPDATE_USER, UPDATE_DATE, PER_CARDNO, POH_ORG1, POH_ORG2, POH_ORG3, POH_ORG, 
									POH_PM_NAME, POH_PL_NAME, POH_SEQ_NO, POH_LAST_POSITION, POH_CMD_SEQ, POH_ISREAL, 
									POH_ORG_DOPA_CODE, ES_CODE, POH_LEVEL_NO, POH_REMARK1, POH_REMARK2, POH_DOCNO_EDIT, POH_DOCDATE_EDIT)
									VALUES ($MAX_POH_ID, $PER_ID, '$EFFECTIVEDATE', '$MOV_CODE', NULL, '$DOCNO', 
									'$DOCDATE', '$POS_NO_NAME', '$POS_NO', '$PM_CODE', $LEVEL_NO, NULL, '$PN_CODE', NULL, '140', NULL, NULL, 2, NULL, NULL, NULL, 
									'$POH_UNDER_ORG1', '$POH_UNDER_ORG2', $SALARY, $POH_SALARY_POS, '$REMARK', $UPDATE_USER, '$UPDATE_DATE', 
									'$PER_CARDNO', '$POH_ORG1', '$POH_ORG2', '$POH_ORG3', '$POH_ORG', '$PM_NAME', '$TP_NAME', $SEQ_NO, 
									'$LAST_TRANSACTION', $CMD_SEQ, '$POH_ISREAL', '$POH_ORG_DOPA_CODE', '$ES_CODE', $LEVEL_NO, '$FLAG_TO_NAME', 
									'$FUNC_NAME', '$DOCNO_EDIT', '$DOCDATE_EDIT') ";
					$db_dpis->send_cmd($cmd);

					$cmd1 = " SELECT POH_ID FROM PER_POSITIONHIS WHERE POH_ID = $MAX_POH_ID "; 
					$count_data = $db_dpis1->send_cmd($cmd1);
					if (!$count_data) {
						echo "$cmd<br>==================<br>";
						$db_dpis->show_error();
						echo "<br>end ". ++$i  ."=======================<br>";
					}
					$MAX_POH_ID++;
				} // end if						
				if ($SALARYHIS) {
					$PER_SALARYHIS++;
					$cmd = " INSERT INTO PER_SALARYHIS(SAH_ID, PER_ID, SAH_EFFECTIVEDATE, MOV_CODE, SAH_SALARY, 
									SAH_DOCNO, SAH_DOCDATE, SAH_ENDDATE, UPDATE_USER, UPDATE_DATE, PER_CARDNO, SAH_PERCENT_UP, 
									SAH_SALARY_UP, SAH_SALARY_EXTRA, SAH_SEQ_NO, SAH_REMARK, LEVEL_NO, SAH_POS_NO_NAME, SAH_POS_NO, 
									SAH_POSITION, SAH_ORG, EX_CODE, SAH_PAY_NO, SAH_LAST_SALARY, SM_CODE, SAH_CMD_SEQ, SAH_ORG_DOPA_CODE)
									VALUES ($MAX_SAH_ID, $PER_ID, '$EFFECTIVEDATE', '$MOV_CODE', $SALARY, '$DOCNO', 
									'$DOCDATE', NULL, $UPDATE_USER, '$UPDATE_DATE', '$PER_CARDNO', $SAH_PERCENT_UP, $SAH_SALARY_UP, 
									$SAH_SALARY_EXTRA, $SEQ_NO, '$REMARK', $LEVEL_NO, '$POS_NO_NAME', '$POS_NO', 
									'$PN_NAME', '$POH_ORG', '$EX_CODE', '$SAH_PAY_NO', '$LAST_TRANSACTION', '$SM_CODE', $CMD_SEQ, '$SAH_ORG_DOPA_CODE') ";
					$db_dpis->send_cmd($cmd);

					$cmd1 = " SELECT SAH_ID FROM PER_SALARYHIS WHERE SAH_ID = $MAX_SAH_ID "; 
					$count_data = $db_dpis1->send_cmd($cmd1);
					if (!$count_data) {
						echo "$cmd<br>==================<br>";
						$db_dpis->show_error();
						echo "<br>end ". ++$j  ."=======================<br>";
					}
					$MAX_SAH_ID++;
				} // end if		
			} else {
//				echo "$PER_CARDNO<br>";
			} // end if						
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_POSITIONHIS ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] - $CNT_POH_ID;
		echo "PER_POSITIONHIS - $PER_POSITIONHIS - $COUNT_NEW<br>";

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_SALARYHIS ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] - $CNT_SAH_ID;
		echo "PER_SALARYHIS - $PER_SALARYHIS - $COUNT_NEW<br>";

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_att->free_result();
		$db_att1->free_result();
	} // end if

	if( $command=='EXTRAHIS' ){
// เงินเพิ่มค่าครองชีพ 13605 ok ******************************************************************
		$cmd = " truncate table per_extrahis ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

// เงินเพิ่มค่าครองชีพข้าราชการ 55364
		$cmd = " SELECT ID, FLAG_PERSON_TYPE, ORDER_MOVE_POSITION, POS_NUM_CODE_SIT, POS_NUM_CODE_SIT_ABB, MP_COMMAND_NUM,
						  CUR_YEAR, MP_YEAR, to_char(MP_COMMAND_DATE,'yyyy-mm-dd') as MP_COMMAND_DATE, to_char(MP_POS_DATE,'yyyy-mm-dd') as MP_POS_DATE, 
						  FLAG_POS_STATUS, FLAG_TO_NAME_CODE, FLAG_TO_NAME, POS_NUM_NAME, POS_NUM_CODE, WORK_LINE_CODE, WORK_LINE_NAME, 
						  SALARY_POS_CODE, SALARY_POS_ABB_NAME, SAL_POS_AMOUNT_2, MP_CEE, SALARY_LEVEL_CODE, SALARY, ADMIN_CODE, ADMIN_NAME, 
						  SALARY_POS_CODE_1, SALARY_POS_ABB_NAME_1, SAL_POS_AMOUNT_1, JOB_CODE, JOB_NAME, SECTION_CODE, SECTION_NAME, DIVISION_CODE,
						  DIVISION_NAME, DEPARTMENT_CODE, DEPARTMENT_NAME, COST_LIVING_AMOUNT, REMARK, REMARKS, AUDIT_FLAG, USER_AUDIT, 
						  to_char(AUDIT_DATE,'yyyy-mm-dd') as AUDIT_DATE, USER_CREATE, to_char(CREATE_DATE,'yyyy-mm-dd') as CREATE_DATE, USER_UPDATE, 
						  to_char(UPDATE_DATE,'yyyy-mm-dd') as UPDATE_DATE, POS_NUM_CODE_SIT_CODE
						  FROM HR_COSTLIVING_OFFICER
						  ORDER BY ID, FLAG_PERSON_TYPE, ORDER_MOVE_POSITION ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		$MAX_ID = 1;
		while($data = $db_att->get_array()){
			$PER_CARDNO = trim($data[ID]);
			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = '$PER_CARDNO' ";
			$db_dpis->send_cmd($cmd);
			$db_dpis->show_error();
			$data_dpis = $db_dpis->get_array();
			$PER_ID = $data_dpis[NEW_CODE] + 0;

			if ($PER_ID > 0) {
				$EXH_EFFECTIVEDATE = trim($data[MP_POS_DATE]);
				if (!$EXH_EFFECTIVEDATE) $EXH_EFFECTIVEDATE = "-";
				$EX_CODE = "012";
				$EXH_AMT = $data[COST_LIVING_AMOUNT] + 0;
				$EXH_ENDDATE = "NULL";
				$EXH_ORG_NAME = trim($data[POS_NUM_CODE_SIT]);
				$EXH_DOCNO = trim($data[POS_NUM_CODE_SIT_ABB]).trim($data[MP_COMMAND_NUM]);
				$CUR_YEAR = trim($data[CUR_YEAR]);
				if (trim($CUR_YEAR)) $EXH_DOCNO .= '/'.trim($CUR_YEAR);
				$EXH_DOCDATE = trim($data[MP_COMMAND_DATE]);
				$EXH_SALARY = $data[SALARY] + 0;
				$EXH_REMARK = trim($data[REMARK]);
				$EXH_ACTIVE = 1;

				$cmd = " INSERT INTO PER_EXTRAHIS (EXH_ID, PER_ID, EXH_EFFECTIVEDATE, EX_CODE, EXH_AMT, EXH_ENDDATE, 
								UPDATE_USER, 	UPDATE_DATE, PER_CARDNO, EXH_ORG_NAME, EXH_DOCNO, EXH_DOCDATE, EXH_SALARY, 
								EXH_REMARK, EXH_ACTIVE)
								VALUES ($MAX_ID, $PER_ID, '$EXH_EFFECTIVEDATE', '$EX_CODE', $EXH_AMT, '$EXH_ENDDATE', 
								$UPDATE_USER, '$UPDATE_DATE', '$PER_CARDNO', '$EXH_ORG_NAME', '$EXH_DOCNO', '$EXH_DOCDATE', 
								$EXH_SALARY, '$EXH_REMARK', $EXH_ACTIVE) ";
				$db_dpis->send_cmd($cmd);

				$cmd1 = " SELECT EXH_ID FROM PER_EXTRAHIS WHERE EXH_ID = $MAX_ID "; 
				$count_data = $db_dpis1->send_cmd($cmd1);
				if (!$count_data) {
					echo "$cmd<br>==================<br>";
					$db_dpis->show_error();
					echo "<br>end ". ++$i  ."=======================<br>";
				}
				$MAX_ID++;
				$PER_EXTRAHIS = $MAX_ID;    
			}
		} // end while						

		$PER_EXTRAHIS--;
		$cmd = " select count(PER_ID) as COUNT_NEW from PER_EXTRAHIS ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_EXTRAHIS - $PER_EXTRAHIS - $COUNT_NEW<br>";

// เงินเพิ่มค่าครองชีพ์ลูกจ้างประจำ
		$cmd = " SELECT ID, FLAG_PERSON_TYPE, ORDER_MOVE_POSITION, BOARD_TYPE, MP_YEAR, POS_NUM_CODE_SIT, POS_NUM_CODE_SIT_ABB, 
						  MP_COMMAND_NUM, CUR_YEAR, to_char(MP_COMMAND_DATE,'yyyy-mm-dd') as MP_COMMAND_DATE, to_char(MP_POS_DATE,'yyyy-mm-dd') as MP_POS_DATE, 
						  FLAG_POS_STATUS, FLAG_TO_NAME_CODE, FLAG_TO_NAME, POS_NUM_NAME, POS_NUM_CODE, CLUSTER_CODE, CATEGORY_SAL_CODE,
						  CATEGORY_SAL_NAME, WORK_LINE_CODE, WORK_LINE_NAME, SALARY_LEVEL_CODE, SALARY, JOB_CODE, JOB_NAME, SECTION_CODE, 
						  SECTION_NAME, DIVISION_CODE, DIVISION_NAME, DEPARTMENT_CODE, DEPARTMENT_NAME, COST_LIVING_AMOUNT, REMARK, USER_CREATE, to_char(CREATE_DATE,'yyyy-mm-dd') as CREATE_DATE, USER_UPDATE, to_char(UPDATE_DATE,'yyyy-mm-dd') as UPDATE_DATE, POS_NUM_CODE_SIT_CODE
						  FROM HR_COSTLIVING_EMP
						  ORDER BY ID, FLAG_PERSON_TYPE, ORDER_MOVE_POSITION ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		while($data = $db_att->get_array()){
			$PER_CARDNO = trim($data[ID]);
			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = '$PER_CARDNO' ";
			$db_dpis->send_cmd($cmd);
			$db_dpis->show_error();
			$data_dpis = $db_dpis->get_array();
			$PER_ID = $data_dpis[NEW_CODE] + 0;

			if ($PER_ID > 0) {
				$EXH_EFFECTIVEDATE = trim($data[MP_POS_DATE]);
				if (!$EXH_EFFECTIVEDATE) $EXH_EFFECTIVEDATE = "-";
				$EX_CODE = "013";
				$EXH_AMT = $data[COST_LIVING_AMOUNT] + 0;
				$EXH_ENDDATE = "NULL";
				$EXH_ORG_NAME = trim($data[POS_NUM_CODE_SIT]);
				$EXH_DOCNO = trim($data[POS_NUM_CODE_SIT_ABB]).trim($data[MP_COMMAND_NUM]);
				$CUR_YEAR = trim($data[CUR_YEAR]);
				if (trim($CUR_YEAR)) $EXH_DOCNO .= '/'.trim($CUR_YEAR);
				$EXH_DOCDATE = trim($data[MP_COMMAND_DATE]);
				$EXH_SALARY = $data[SALARY] + 0;
				$EXH_REMARK = trim($data[REMARK]);
				$EXH_ACTIVE = 1;

				$cmd = " INSERT INTO PER_EXTRAHIS (EXH_ID, PER_ID, EXH_EFFECTIVEDATE, EX_CODE, EXH_AMT, EXH_ENDDATE, 
								UPDATE_USER, 	UPDATE_DATE, PER_CARDNO, EXH_ORG_NAME, EXH_DOCNO, EXH_DOCDATE, EXH_SALARY, 
								EXH_REMARK, EXH_ACTIVE)
								VALUES ($MAX_ID, $PER_ID, '$EXH_EFFECTIVEDATE', '$EX_CODE', $EXH_AMT, '$EXH_ENDDATE', 
								$UPDATE_USER, '$UPDATE_DATE', '$PER_CARDNO', '$EXH_ORG_NAME', '$EXH_DOCNO', '$EXH_DOCDATE', 
								$EXH_SALARY, '$EXH_REMARK', $EXH_ACTIVE) ";
				$db_dpis->send_cmd($cmd);

				$cmd1 = " SELECT EXH_ID FROM PER_EXTRAHIS WHERE EXH_ID = $MAX_ID "; 
				$count_data = $db_dpis1->send_cmd($cmd1);
				if (!$count_data) {
					echo "$cmd<br>==================<br>";
					$db_dpis->show_error();
					echo "<br>end ". ++$i  ."=======================<br>";
				}
				$MAX_ID++;
				$PER_EXTRAHIS = $MAX_ID;    
			}
		} // end while						

		$PER_EXTRAHIS--;
		$cmd = " select count(PER_ID) as COUNT_NEW from PER_EXTRAHIS ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_EXTRAHIS - $PER_EXTRAHIS - $COUNT_NEW<br>";

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_att->free_result();
		$db_att1->free_result();
	} // end if

	if( $command=='KPIFORM' ){
// KPI Form 1576 ok
		$cmd = " truncate table per_kpi_form ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " SELECT BUD_YEAR, CLASS_ORDER, FLAG_PERSON_TYPE, to_char(EVALUATE_DATE,'yyyy-mm-dd') as EVALUATE_DATE, 
						ID, POS_NUM_NAME, POS_NUM_CODE, POSITION_CATG, ADMIN_CODE, ADMIN_NAME, WORK_LINE_CODE, WORK_LINE_NAME, 
						SALARY_POS_CODE, SALARY_POS_ABB_NAME, MP_CEE, SALARY_LEVEL_CODE, SALARY, SCORE, EVALUATE_CODE, 
						EVALUATE_NAME, EVALUATE_LEVEL, DESCRIPTION_LEVEL, DEPARTMENT_CODE, DEPARTMENT_NAME, DIVISION_CODE, 
						DIVISION_NAME, SECTION_CODE, SECTION_NAME, JOB_CODE, JOB_NAME, SUM_MP_FLAG,	USER_CREATE, 
						to_char(CREATE_DATE,'yyyy-mm-dd') as CREATE_DATE, USER_UPDATE, to_char(UPDATE_DATE,'yyyy-mm-dd') as UPDATE_DATE
						FROM HR_EVALUATE_OFFICER
						WHERE FLAG_PERSON_TYPE = '1' AND SCORE IS NOT NULL
						ORDER BY ID, BUD_YEAR ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		$MAX_ID = 1;
		while($data = $db_att->get_array()){
			$PER_CARDNO = trim($data[ID]);
			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = '$PER_CARDNO' ";
			$db_dpis->send_cmd($cmd);
			$db_dpis->show_error();
			$data_dpis = $db_dpis->get_array();
			$PER_ID = $data_dpis[NEW_CODE] + 0;

			if ($PER_ID > 0) {
				$BUD_YEAR = trim($data[BUD_YEAR]);
				$CLASS_ORDER = $data[CLASS_ORDER];
				if($CLASS_ORDER == 1){
					$KF_START_DATE = ($BUD_YEAR-544) . "-10-01";
					$KF_END_DATE = ($BUD_YEAR-543) . "-03-31";
				}elseif($CLASS_ORDER == 2){
					$KF_START_DATE = ($BUD_YEAR-543) . "-04-01";
					$KF_END_DATE = ($BUD_YEAR-543) . "-09-30";
				} // end if
				$SCORE = $data[SCORE];
				$LEVEL_NO = str_pad(trim($data[MP_CEE]), 2, "0", STR_PAD_LEFT);
				if ($LEVEL_NO=="7ว") $LEVEL_NO = "07";
				$EVALUATE_NAME = trim($data[EVALUATE_NAME]);
				$DESCRIPTION_LEVEL = trim($data[DESCRIPTION_LEVEL]);
				$DEPARTMENT_CODE = trim($data[DEPARTMENT_CODE]);
				$DIVISION_CODE = trim($data[DIVISION_CODE]);
				$SECTION_CODE = trim($data[SECTION_CODE]);

				$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = '$DEPARTMENT_CODE' ";
				$db_dpis2->send_cmd($cmd);
	//			$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$DEPARTMENT_ID = $data2[NEW_CODE];

				$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = '$DIVISION_CODE' ";
				$db_dpis2->send_cmd($cmd);
	//			$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$ORG_ID = $data2[NEW_CODE];

				$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = '$SECTION_CODE' ";
				$db_dpis2->send_cmd($cmd);
	//			$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$ORG_ID_1 = $data2[NEW_CODE];

				if (!$DEPARTMENT_ID) $DEPARTMENT_ID = "NULL";
				if (!$ORG_ID) $ORG_ID = "NULL";
				if (!$ORG_ID_1) $ORG_ID_1 = "NULL";

				$UPDATE_USER = 99999;
				if ($data[USER_UPDATE]) $USER_UPDATE = trim($data[USER_UPDATE]);
				else $USER_UPDATE = trim($data[USER_CREATE]);
				if ($data[UPDATE_DATE]) $UPDATE_DATE = trim($data[UPDATE_DATE]);
				else  $UPDATE_DATE = trim($data[CREATE_DATE]);
				if ($USER_UPDATE) {
					$cmd = " select ID from USER_DETAIL where USERNAME = '$USER_UPDATE' ";
					$db_dpis2->send_cmd($cmd);
	//				$db_dpis2->show_error();
					$data2 = $db_dpis2->get_array();
					if ($data2[ID]) $UPDATE_USER = $data2[ID];
				}
				if (!$UPDATE_DATE) $UPDATE_DATE = date("Y-m-d H:i:s");

				$cmd = " INSERT INTO PER_KPI_FORM (KF_ID, PER_ID, PER_CARDNO, KF_CYCLE, KF_START_DATE, KF_END_DATE, 
								SALARY_REMARK1, SALARY_REMARK2, DEPARTMENT_ID, UPDATE_USER, UPDATE_DATE, ORG_ID, 
								TOTAL_SCORE, SALARY_FLAG, ORG_ID_SALARY, KPI_FLAG, ORG_ID_KPI, 
								ORG_ID_1_SALARY, PERFORMANCE_WEIGHT, COMPETENCE_WEIGHT, LEVEL_NO)
								VALUES ($MAX_ID, $PER_ID, '$PER_CARDNO', $CLASS_ORDER, '$KF_START_DATE', '$KF_END_DATE', 	
								'$EVALUATE_NAME', '$DESCRIPTION_LEVEL', $DEPARTMENT_ID, $UPDATE_USER, '$UPDATE_DATE', 
								$ORG_ID, $SCORE, 'Y', $ORG_ID, 'Y', $ORG_ID, $ORG_ID_1, 70, 30, '$LEVEL_NO') ";
				$db_dpis->send_cmd($cmd);

				$cmd1 = " SELECT KF_ID FROM PER_KPI_FORM WHERE KF_ID = $MAX_ID "; 
				$count_data = $db_dpis1->send_cmd($cmd1);
				if (!$count_data) {
					echo "$cmd<br>==================<br>";
					$db_dpis->show_error();
					echo "<br>end ". ++$i  ."=======================<br>";
				}
				$MAX_ID++;
				$PER_KPI_FORM = $MAX_ID;    
			}
		} // end while						

		$PER_KPI_FORM--;
		$cmd = " select count(PER_ID) as COUNT_NEW from PER_KPI_FORM ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_KPI_FORM - $PER_KPI_FORM - $COUNT_NEW<br>";

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_att->free_result();
		$db_att1->free_result();
	} // end if

	if( $command=='PUNISHMENT' ){
// วินัย 298 16572 ok *********************************************************************************
		$cmd = " truncate table per_punishment ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " SELECT ID, FLAG_PERSON_TYPE, MP_YEAR, BLAME_ORDER, REASON_FLAW, POS_NUM_NAME, POS_NUM_CODE, 
						WORK_LINE_CODE, WORK_LINE_NAME, MP_CEE, SALARY, SALARY_POS_CODE, SALARY_POS_NAME, SALARY_POS_ABB_NAME, 
						SPECIALIST_CODE, ADMIN_CODE, ADMIN_NAME, SALARY_POS_CODE_1, SALARY_POS_ABB_NAME_1, JOB_CODE, JOB_NAME,
						SECTION_CODE, SECTION_NAME, DIVISION_CODE, DIVISION_NAME, DEPARTMENT_CODE, DEPARTMENT_NAME, USER_CREATE, 
						  to_char(CREATE_DATE,'yyyy-mm-dd') as CREATE_DATE, USER_UPDATE, 
						  to_char(UPDATE_DATE,'yyyy-mm-dd') as UPDATE_DATE
						  FROM HR_MOVEMENT_OFFICER 
						  WHERE FLAG_PERSON_TYPE != '3' 
						  ORDER BY ID, MP_YEAR ";
		$db_att->send_cmd($cmd);
		$db_att->show_error();
		echo "<br>";
		$MAX_ID = 1;
		while($data = $db_att->get_array()){
			$PER_PUNISHMENT++;
			$PER_CARDNO = trim($data[ID]);
			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = '$PER_CARDNO' ";
			$db_dpis->send_cmd($cmd);
			$db_dpis->show_error();
			$data_dpis = $db_dpis->get_array();
			$PER_ID = $data_dpis[NEW_CODE] + 0;

			if ($PER_ID) {
				$MOVEMENT_TYPE = trim($data[MOVEMENT_TYPE]);
				$REMARK = trim($data[REMARK]);
				$START_DATE = trim($data[START_DATE]);
				$END_DATE = trim($data[END_DATE]);
				$DS_SALARY = $data[DS_SALARY] + 0;
				$DS_MONTH = $data[DS_MONTH];

				$UPDATE_USER = 99999;
				if ($data[USER_UPDATE]) $USER_UPDATE = trim($data[USER_UPDATE]);
				else $USER_UPDATE = trim($data[USER_CREATE]);
				if ($data[UPDATE_DATE]) $UPDATE_DATE = trim($data[UPDATE_DATE]);
				else  $UPDATE_DATE = trim($data[CREATE_DATE]);
				if ($USER_UPDATE) {
					$cmd = " select ID from USER_DETAIL where USERNAME = '$USER_UPDATE' ";
					$db_dpis2->send_cmd($cmd);
	//				$db_dpis2->show_error();
					$data2 = $db_dpis2->get_array();
					if ($data2[ID]) $UPDATE_USER = $data2[ID];
				}
				if (!$UPDATE_DATE) $UPDATE_DATE = date("Y-m-d H:i:s");

				if ($MOVEMENT_TYPE=="502") $PEN_CODE = "08";
				elseif ($MOVEMENT_TYPE=="503") $PEN_CODE = "01";
				elseif ($MOVEMENT_TYPE=="504") $PEN_CODE = "02";
				elseif ($MOVEMENT_TYPE=="505") $PEN_CODE = "03";
				elseif ($MOVEMENT_TYPE=="506") $PEN_CODE = "05";
				elseif ($MOVEMENT_TYPE=="507") $PEN_CODE = "06";
				elseif ($MOVEMENT_TYPE=="508") $PEN_CODE = "10";
				elseif ($MOVEMENT_TYPE=="509") $PEN_CODE = "09";
				elseif ($MOVEMENT_TYPE=="510") $PEN_CODE = "11";
				elseif ($MOVEMENT_TYPE=="511") $PEN_CODE = "12";
				elseif ($MOVEMENT_TYPE=="515") $PEN_CODE = "04";

				if ($PEN_CODE=="04" || $PEN_CODE=="05" || $PEN_CODE=="06" || $PEN_CODE=="11" || $PEN_CODE=="12") $PUN_TYPE = 1;
				elseif ($PEN_CODE=="01" || $PEN_CODE=="02" || $PEN_CODE=="03" || $PEN_CODE=="07" || $PEN_CODE=="08") $PUN_TYPE = 2;
				elseif ($PEN_CODE=="09" || $PEN_CODE=="10") $PUN_TYPE = 3;

				if ($DS_MONTH) $PUN_PAY = 1; else $PUN_PAY = 0;
				$CRD_CODE = "9900";
				if (!$START_DATE) $START_DATE = "-";
				if (!$END_DATE) $END_DATE = $START_DATE;

				$cmd = " INSERT INTO PER_PUNISHMENT(PUN_ID, PER_ID, INV_NO, PUN_NO, PUN_REF_NO, PUN_TYPE, 
								PUN_STARTDATE, PUN_ENDDATE, CRD_CODE, PEN_CODE, PUN_PAY, PUN_SALARY, UPDATE_USER, 
								UPDATE_DATE, PER_CARDNO, PUN_REMARK)
								 VALUES ($MAX_ID, $PER_ID, '$INV_NO', '$PUN_NO', 'PUN_REF_NO', $PUN_TYPE, '$PUN_STARTDATE', 
								'$PUN_ENDDATE', '$CRD_CODE', '$PEN_CODE', $PUN_PAY, $PUN_SALARY, $UPDATE_USER, 
								'$UPDATE_DATE', '$PER_CARDNO', '$PUN_REMARK') ";
				$db_dpis->send_cmd($cmd);

				$cmd1 = " SELECT PUN_ID FROM PER_PUNISHMENT WHERE PUN_ID = $MAX_ID "; 
				$count_data = $db_dpis1->send_cmd($cmd1);
				if (!$count_data) {
					echo "$cmd<br>==================<br>";
					$db_dpis->show_error();
					echo "<br>end ". ++$i  ."=======================<br>";
				}
				$MAX_ID++;
			}
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_PUNISHMENT ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_PUNISHMENT - $PER_PUNISHMENT - $COUNT_NEW<br>";
		
		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_att->free_result();
		$db_att1->free_result();
	} // end if

	if( $command=='TIME' ){ 
// เวลาทวีคูณ 1794 ok **********************************************************************
		$cmd = " truncate table per_timehis ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " SELECT PERSONID, AREACODE, DAYRECEIVE, a.MODBY, a.MODDATE 
						  FROM MULTITIME a, MULTIPLEAREA b
						  WHERE a.AREACODE = b.MULTIPLEAREAID
						  ORDER BY MULITTIMEID ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		$MAX_ID = 1;
		while($data = $db_att->get_array()){
			$PER_TIMEHIS++;
			$PER_CARDNO = trim($data[ID]);
			$AREACODE = $data[AREACODE];
			$DAYRECEIVE = trim($data[DAYRECEIVE]);
			if ($AREACODE==1) $TIME_CODE = "10";
			elseif ($AREACODE==2) $TIME_CODE = "08";
			elseif ($AREACODE==3) $TIME_CODE = "04";
			elseif ($AREACODE==4) $TIME_CODE = "05";
			elseif ($AREACODE==5) $TIME_CODE = "06";
			elseif ($AREACODE==6) $TIME_CODE = "07";
			elseif ($AREACODE==7) $TIME_CODE = "09";
			$MODBY = $data[MODBY];
			if ($MODBY) {
				$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'USER_DETAIL' AND OLD_CODE = '$MODBY' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
				$data_dpis = $db_dpis->get_array();
				$UPDATE_USER = $data_dpis[NEW_CODE] + 0;
				if ($UPDATE_USER==0) echo "$MODBY<br>";
			}
			$MODDATE = trim($data[MODDATE]);
			if ($MODDATE && $MODDATE != "00000000000000") $UPDATE_DATE = substr($MODDATE, 0, 4) . "-" . substr($MODDATE, 4, 2) . "-" . substr($MODDATE, 6, 2);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = '$PER_CARDNO' ";
			$db_dpis->send_cmd($cmd);
			$db_dpis->show_error();
			$data_dpis = $db_dpis->get_array();
			$PER_ID = $data_dpis[NEW_CODE] + 0;

			$cmd = " select TIME_DAY from PER_TIME where TIME_CODE = '$TIME_CODE' ";
			$db_dpis->send_cmd($cmd);
			$db_dpis->show_error();
			$data_dpis = $db_dpis->get_array();
			$TIME_DAY = $data_dpis[TIME_DAY] + 0;
			$TIMEH_MINUS = $TIME_DAY - $DAYRECEIVE;

			$cmd = " INSERT INTO PER_TIMEHIS(TIMEH_ID, PER_ID, TIME_CODE, TIMEH_MINUS, TIMEH_REMARK, UPDATE_USER, 
							 UPDATE_DATE)
							 VALUES ($MAX_ID, $PER_ID, '$TIME_CODE', $TIMEH_MINUS, NULL, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);

			$cmd1 = " SELECT TIMEH_ID FROM PER_TIMEHIS WHERE TIMEH_ID = $MAX_ID "; 
			$count_data = $db_dpis1->send_cmd($cmd1);
			if (!$count_data) {
				echo "$cmd<br>==================<br>";
				$db_dpis->show_error();
				echo "<br>end ". ++$i  ."=======================<br>";
			}
			$MAX_ID++;
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_TIMEHIS ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_TIMEHIS - $PER_TIMEHIS - $COUNT_NEW<br>";
		
		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_att->free_result();
		$db_att1->free_result();
	} // end if

	if( $command=='MARRIED' ){
// สมรส 1029 ok ********************************************************************
		$cmd = " truncate table per_marrhis ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " SELECT PERSONID, MARRIEDNO, MARRIEDAT, MARRIEDDATE, WEDDEDPAIR, MARRYSTATUS, REMARK, MODDATE, MODBY, 
						BOOK_NO, REGIS_DATE, PRO_CODE
						FROM MARRIED
						ORDER BY MARRIEDID ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		$MAX_ID = 1;
		while($data = $db_att->get_array()){
			$PER_MARRHIS++;
			$PER_CARDNO = trim($data[ID]);
			$MAH_MARRY_DATE = trim($data[MARRIEDDATE]);
			if ($MAH_MARRY_DATE=="0000-00-00") $MAH_MARRY_DATE = "";
			$MAH_NAME = trim($data[WEDDEDPAIR]);
			$MR_CODE = trim($data[MARRYSTATUS]);
			$MAH_MARRY_NO = trim($data[MARRIEDNO]);
			$MAH_MARRY_ORG = trim($data[MARRIEDAT]);
			$PV_CODE = trim($data[PRO_CODE]);
			if ($PV_CODE) $PV_CODE .= "00";
			$MAH_BOOK_NO = trim($data[BOOK_NO]);
			$MAH_BOOK_DATE = trim($data[REGIS_DATE]);
			$MAH_REMARK = trim($data[REMARK]);
			$MODBY = $data[MODBY];
			if ($MODBY) {
				$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'USER_DETAIL' AND OLD_CODE = '$MODBY' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
				$data_dpis = $db_dpis->get_array();
				$UPDATE_USER = $data_dpis[NEW_CODE] + 0;
				if ($UPDATE_USER==0) echo "$MODBY<br>";
			}
			$MODDATE = trim($data[MODDATE]);
			if ($MODDATE && $MODDATE != "00000000000000") $UPDATE_DATE = substr($MODDATE, 0, 4) . "-" . substr($MODDATE, 4, 2) . "-" . substr($MODDATE, 6, 2);

			if ($MR_CODE=="3" || $MR_CODE=="4") $MAH_DIVORCE_DATE  = trim($data[MARRIEDDATE]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = '$PER_CARDNO' ";
			$db_dpis->send_cmd($cmd);
			$db_dpis->show_error();
			$data_dpis = $db_dpis->get_array();
			$PER_ID = $data_dpis[NEW_CODE] + 0;

			$cmd = " select max(MAH_SEQ) as MAH_SEQ from PER_MARRHIS where PER_ID = $PER_ID ";
			$db_dpis->send_cmd($cmd);
			$data_dpis = $db_dpis->get_array();
			$MAH_SEQ = $data_dpis[MAH_SEQ] + 1;

			$cmd = " INSERT INTO PER_MARRHIS(MAH_ID, PER_ID, MAH_SEQ, MAH_NAME, MAH_MARRY_DATE, 
							MAH_DIVORCE_DATE, DV_CODE, UPDATE_USER, UPDATE_DATE, MAH_MARRY_NO, 
							MAH_MARRY_ORG, PV_CODE, MR_CODE, MAH_BOOK_NO, MAH_BOOK_DATE, MAH_REMARK)
							VALUES ($MAX_ID, $PER_ID, $MAH_SEQ, '$MAH_NAME', '$MAH_MARRY_DATE', '$MAH_DIVORCE_DATE', 
							'$DV_CODE', $UPDATE_USER, '$UPDATE_DATE', '$MAH_MARRY_NO', '$MAH_MARRY_ORG', 
							'$PV_CODE', '$MR_CODE', '$MAH_BOOK_NO', '$MAH_BOOK_DATE', '$MAH_REMARK') ";
			$db_dpis->send_cmd($cmd);

			$cmd1 = " SELECT MAH_ID FROM PER_MARRHIS WHERE MAH_ID = $MAX_ID "; 
			$count_data = $db_dpis1->send_cmd($cmd1);
			if (!$count_data) {
				echo "$cmd<br>==================<br>";
				$db_dpis->show_error();
				echo "<br>end ". ++$i  ."=======================<br>";
			}	
			$MAX_ID++;
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_MARRHIS ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_MARRHIS - $PER_MARRHIS - $COUNT_NEW<br>";

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_att->free_result();
		$db_att1->free_result();
	} // end if

	if( $command=='CONTACT' ){
// ผู้ที่มีความสัมพันธ์กับพนักงาน 13590 13589 ok **********************************************************
		$cmd = " SELECT PERSONID, CONTACTADDR FROM CONTACTPERSON WHERE CONTACTADDR > ' ' ORDER BY PERSONID ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		while($data = $db_att->get_array()){
			$CONTACT_PERSON++;
			$PER_CARDNO = trim($data[ID]);
			$PER_CONTACT_PERSON = trim($data[CONTACTADDR]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = '$PER_CARDNO' ";
			$db_dpis->send_cmd($cmd);
			$db_dpis->show_error();
			$data_dpis = $db_dpis->get_array();
			$PER_ID = $data_dpis[NEW_CODE] + 0;

			$cmd = " UPDATE PER_PERSONAL SET PER_CONTACT_PERSON = '$PER_CONTACT_PERSON' WHERE PER_ID = $PER_ID ";
			$db_dpis->send_cmd($cmd);
//			echo "$cmd<br>==================<br>";
//			$db_dpis->show_error();
//			echo "<br>end ". ++$i  ."=======================<br>";
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_PERSONAL WHERE PER_CONTACT_PERSON IS NOT NULL ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "CONTACT_PERSON - $CONTACT_PERSON $COUNT_NEW<br>";

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_att->free_result();
		$db_att1->free_result();
	} // end if

	if( $command=='USER' ){
// กลุ่มผู้ใช้งาน 173 ok *************************************************************
		$cmd = " delete from user_group where update_by = '99999' ";
		$db->send_cmd($cmd);
		//$db->show_error();

		$cmd = " delete from per_map_code where map_code = 'USER_GROUP' ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error();

		$cmd = " select MAX(ID) as MAX_ID from USER_GROUP ";
		$db->send_cmd($cmd);
		//$db->show_error();
		$data = $db->get_array();
		$MAX_ID = $data[MAX_ID] + 1;

		$cmd = " SELECT ROLEID, NAME, ABBR, ROLELEVEL, UPPERROLE
						FROM ROLE
						ORDER BY ROLEID ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		while($data = $db_att->get_array()){
			$USER_GROUP++;
			$ROLEID = $data[ROLEID];
			$NAME = trim($data[NAME]);
			$ABBR = trim($data[ABBR]);
			$ROLELEVEL = $data[ROLELEVEL];
			$UPPERROLE = $data[UPPERROLE]; // หัวหน้า

			$GROUP_LEVEL = 4;
			if (strpos($NAME,"สมุทรปราการ") !== false) $PV_CODE = "1100";
			elseif (strpos($NAME,"นนทบุรี") !== false) $PV_CODE = "1200";
			elseif (strpos($NAME,"ปทุมธานี") !== false) $PV_CODE = "1300";
			elseif (strpos($NAME,"พระนครศรีอยุธยา") !== false) $PV_CODE = "1400";
			elseif (strpos($NAME,"อ่างทอง") !== false) $PV_CODE = "1500";
			elseif (strpos($NAME,"ลพบุรี") !== false) $PV_CODE = "1600";
			elseif (strpos($NAME,"สิงห์บุรี") !== false) $PV_CODE = "1700";
			elseif (strpos($NAME,"ชัยนาท") !== false) $PV_CODE = "1800";
			elseif (strpos($NAME,"สระบุรี") !== false) $PV_CODE = "1900";
			elseif (strpos($NAME,"ชลบุรี") !== false) $PV_CODE = "2000";
			elseif (strpos($NAME,"ระยอง") !== false) $PV_CODE = "2100";
			elseif (strpos($NAME,"จันทบุรี") !== false) $PV_CODE = "2200";
			elseif (strpos($NAME,"ตราด") !== false) $PV_CODE = "2300";
			elseif (strpos($NAME,"ฉะเชิงเทรา") !== false) $PV_CODE = "2400";
			elseif (strpos($NAME,"ปราจีนบุรี") !== false) $PV_CODE = "2500";
			elseif (strpos($NAME,"นครนายก") !== false) $PV_CODE = "2600";
			elseif (strpos($NAME,"สระแก้ว") !== false) $PV_CODE = "2700";
			elseif (strpos($NAME,"นครราชสีมา") !== false) $PV_CODE = "3000";
			elseif (strpos($NAME,"บุรีรัมย์") !== false) $PV_CODE = "3100";
			elseif (strpos($NAME,"สุรินทร์") !== false) $PV_CODE = "3200";
			elseif (strpos($NAME,"ศรีสะเกษ") !== false) $PV_CODE = "3300";
			elseif (strpos($NAME,"อุบลราชธานี") !== false) $PV_CODE = "3400";
			elseif (strpos($NAME,"ยโสธร") !== false) $PV_CODE = "3500";
			elseif (strpos($NAME,"ชัยภูมิ") !== false) $PV_CODE = "3600";
			elseif (strpos($NAME,"อำนาจเจริญ") !== false) $PV_CODE = "3700";
			elseif (strpos($NAME,"หนองบัวลำภู") !== false) $PV_CODE = "3900";
			elseif (strpos($NAME,"ขอนแก่น") !== false) $PV_CODE = "4000";
			elseif (strpos($NAME,"อุดรธานี") !== false) $PV_CODE = "4100";
			elseif (strpos($NAME,"เลย") !== false) $PV_CODE = "4200";
			elseif (strpos($NAME,"หนองคาย") !== false) $PV_CODE = "4300";
			elseif (strpos($NAME,"มหาสารคาม") !== false) $PV_CODE = "4400";
			elseif (strpos($NAME,"ร้อยเอ็ด") !== false) $PV_CODE = "4500";
			elseif (strpos($NAME,"กาฬสินธุ์") !== false) $PV_CODE = "4600";
			elseif (strpos($NAME,"สกลนคร") !== false) $PV_CODE = "4700";
			elseif (strpos($NAME,"นครพนม") !== false) $PV_CODE = "4800";
			elseif (strpos($NAME,"มุกดาหาร") !== false) $PV_CODE = "4900";
			elseif (strpos($NAME,"เชียงใหม่") !== false) $PV_CODE = "5000";
			elseif (strpos($NAME,"ลำพูน") !== false) $PV_CODE = "5100";
			elseif (strpos($NAME,"ลำปาง") !== false) $PV_CODE = "5200";
			elseif (strpos($NAME,"อุตรดิตถ์") !== false) $PV_CODE = "5300";
			elseif (strpos($NAME,"แพร่") !== false) $PV_CODE = "5400";
			elseif (strpos($NAME,"น่าน") !== false) $PV_CODE = "5500";
			elseif (strpos($NAME,"พะเยา") !== false) $PV_CODE = "5600";
			elseif (strpos($NAME,"เชียงราย") !== false) $PV_CODE = "5700";
			elseif (strpos($NAME,"แม่ฮ่องสอน") !== false) $PV_CODE = "5800";
			elseif (strpos($NAME,"นครสวรรค์") !== false) $PV_CODE = "6000";
			elseif (strpos($NAME,"อุทัยธานี") !== false) $PV_CODE = "6100";
			elseif (strpos($NAME,"กำแพงเพชร") !== false) $PV_CODE = "6200";
			elseif (strpos($NAME,"ตาก") !== false) $PV_CODE = "6300";
			elseif (strpos($NAME,"สุโขทัย") !== false) $PV_CODE = "6400";
			elseif (strpos($NAME,"พิษณุโลก") !== false) $PV_CODE = "6500";
			elseif (strpos($NAME,"พิจิตร") !== false) $PV_CODE = "6600";
			elseif (strpos($NAME,"เพชรบูรณ์") !== false) $PV_CODE = "6700";
			elseif (strpos($NAME,"ราชบุรี") !== false) $PV_CODE = "7000";
			elseif (strpos($NAME,"กาญจนบุรี") !== false) $PV_CODE = "7100";
			elseif (strpos($NAME,"สุพรรณบุรี") !== false) $PV_CODE = "7200";
			elseif (strpos($NAME,"นครปฐม") !== false) $PV_CODE = "7300";
			elseif (strpos($NAME,"สมุทรสาคร") !== false) $PV_CODE = "7400";
			elseif (strpos($NAME,"สมุทรสงคราม") !== false) $PV_CODE = "7500";
			elseif (strpos($NAME,"เพชรบุรี") !== false) $PV_CODE = "7600";
			elseif (strpos($NAME,"ประจวบคีรีขันธ์") !== false) $PV_CODE = "7700";
			elseif (strpos($NAME,"นครศรีธรรมราช") !== false) $PV_CODE = "8000";
			elseif (strpos($NAME,"กระบี่") !== false) $PV_CODE = "8100";
			elseif (strpos($NAME,"พังงา") !== false) $PV_CODE = "8200";
			elseif (strpos($NAME,"ภูเก็ต") !== false) $PV_CODE = "8300";
			elseif (strpos($NAME,"สุราษฎร์ธานี") !== false) $PV_CODE = "8400";
			elseif (strpos($NAME,"ระนอง") !== false) $PV_CODE = "8500";
			elseif (strpos($NAME,"ชุมพร") !== false) $PV_CODE = "8600";
			elseif (strpos($NAME,"สงขลา") !== false) $PV_CODE = "9000";
			elseif (strpos($NAME,"สตูล") !== false) $PV_CODE = "9100";
			elseif (strpos($NAME,"ตรัง") !== false) $PV_CODE = "9200";
			elseif (strpos($NAME,"พัทลุง") !== false) $PV_CODE = "9300";
			elseif (strpos($NAME,"ปัตตานี") !== false) $PV_CODE = "9400";
			elseif (strpos($NAME,"ยะลา") !== false) $PV_CODE = "9500";
			elseif (strpos($NAME,"นราธิวาส") !== false) $PV_CODE = "9600";
			
			if ($PV_CODE) $GROUP_LEVEL = 5;
			$ORG_ID = $DEPARTMENT_ID;
			if ($PV_CODE) {
				$cmd = " select ORG_ID from PER_ORG a, PER_PROVINCE b
								  where a.PV_CODE= b.PV_CODE and a.PV_CODE = '$PV_CODE' and a.ORG_NAME = 'จังหวัด'||b.PV_NAME and 
								  DEPARTMENT_ID = $DEPARTMENT_ID and OL_CODE = '03' ";
				$count_data = $db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
//				echo "$cmd<br>";
				if ($count_data) {
					$data1 = $db_dpis->get_array();
					$ORG_ID = $data1[ORG_ID];
				}
			}

			$cmd = " INSERT INTO USER_GROUP(ID, CODE, NAME_TH, NAME_EN, ACCESS_LIST, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY, 
							GROUP_LEVEL, ORG_ID, PV_CODE)
							VALUES ($MAX_ID, '$ABBR', '$NAME', '$NAME', ',1,', '$UPDATE_DATE', '$UPDATE_USER', '$UPDATE_DATE', '$UPDATE_USER', 
							$GROUP_LEVEL, $ORG_ID, '$PV_CODE') ";
			$db->send_cmd($cmd);
			//echo "$cmd<br>==================<br>";
			//$db->show_error();
			//echo "<br>end ". ++$i  ."=======================<br>";

			$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
							VALUES ('USER_GROUP', '$ROLEID', '$MAX_ID', $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			$MAX_ID++;
		} // end while						

		$cmd = " select count(ID) as COUNT_NEW from USER_GROUP where update_by = '99999' ";
		$db->send_cmd($cmd);
		$data = $db->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "USER_GROUP - $USER_GROUP - $COUNT_NEW<br>";

// ผู้ใช้งาน 379 ok
		$cmd = " delete from user_detail where update_by = '99999' ";
		$db->send_cmd($cmd);
		//$db->show_error();

		$cmd = " delete from per_map_code where map_code = 'USER_DETAIL' ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error();

		$cmd = " select MAX(ID) as MAX_ID from USER_DETAIL ";
		$db->send_cmd($cmd);
		//$db->show_error();
		$data = $db->get_array();
		$MAX_ID = $data[MAX_ID] + 1;

		$cmd = " SELECT USERID, FIRSTNAME, LASTNAME, LOGINNAME, PASSWORD, PHONE, MOBILEPHONE, TITLE, NAME
						FROM USERS a, SECTIONLIST b
						WHERE a.DOPANAME = b.DOPANAME
						ORDER BY USERID ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		while($data = $db_att->get_array()){
			$USER_DETAIL++;
			$USERID = $data[USERID];
			$FIRSTNAME = trim($data[FIRSTNAME]);
			$LASTNAME = trim($data[LASTNAME]);
			$LOGINNAME = trim($data[LOGINNAME]);
			$PASSWORD = trim($data[PASSWORD]);
			$PHONE = trim($data[PHONE]);
			$MOBILEPHONE = trim($data[MOBILEPHONE]);
			$TITLE = trim($data[TITLE]);
			$NAME = trim($data[NAME]);
			$FULLNAME = $FIRSTNAME . " " . $LASTNAME;

			$cmd = " SELECT ROLEID FROM USERROLE WHERE USERID = $USERID ";
			$db_att1->send_cmd($cmd);
			$data1 = $db_att1->get_array();
			$ROLEID = $data1[ROLEID];

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'USER_GROUP' AND OLD_CODE = $ROLEID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			//echo "$cmd<br>";
			$data1 = $db_dpis->get_array();
			$GROUP_ID = $data1[NEW_CODE] + 0;

			$cmd = " INSERT INTO USER_DETAIL(ID, GROUP_ID, USERNAME, PASSWORD, INHERIT_GROUP, USER_LINK_ID, FULLNAME, ADDRESS, 
							DISTRICT_ID, AMPHUR_ID, PROVINCE_ID, EMAIL, TEL, FAX, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY, TITLENAME)
							VALUES ($MAX_ID, $GROUP_ID, '$LOGINNAME', '$PASSWORD', NULL, NULL, '$FULLNAME', '$NAME',
							NULL, NULL, NULL, NULL, '$PHONE', '$MOBILEPHONE', '$UPDATE_DATE', '$UPDATE_USER', '$UPDATE_DATE', '$UPDATE_USER', '$TITLE') ";
			$db->send_cmd($cmd);
			//echo "$cmd<br>==================<br>";
			//$db->show_error();
			//echo "<br>end ". ++$i  ."=======================<br>";

			$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
							VALUES ('USER_DETAIL', '$USERID', '$MAX_ID', $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			$MAX_ID++;
		} // end while						

		$cmd = " select count(ID) as COUNT_NEW from USER_DETAIL where update_by = '99999' ";
		$db->send_cmd($cmd);
		$data = $db->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "USER_DETAIL - $USER_DETAIL - $COUNT_NEW<br>";

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_att->free_result();
		$db_att1->free_result();
	} // end if

	if( $command=='MGTSALARY' ){
// เงินประจำตำแหน่ง 2346 ok ****************************************************************************************
/*		$cmd = " truncate table PER_POS_MGTSALARY ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " SELECT MP_ID, POS_CODE, SPECIAL_TYPEID, STARTDATE, STATUS, UPDATE_DATE, UPDATE_BY
						  FROM MONEY_POSITION
						  ORDER BY MP_ID ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		while($data = $db_att->get_array()){
			$PER_POS_MGTSALARY++;
			$MP_ID = $data[MP_ID];
			$POS_CODE = trim($data[POS_CODE]);
			$EX_CODE = str_pad(trim($data[SPECIAL_TYPEID]), 3, "0", STR_PAD_LEFT);
			$POS_STARTDATE = trim($data[STARTDATE]);
			$STATUS = strtoupper(trim($data[STATUS]));
			if ($STATUS=="Y") $POS_STATUS = 1;
			elseif ($STATUS=="N") $POS_STATUS = 2; 
			$UPDATE_BY = $data[UPDATE_BY];
			if ($UPDATE_BY) {
				$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'USER_DETAIL' AND OLD_CODE = '$UPDATE_BY' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
				$data_dpis = $db_dpis->get_array();
				$UPDATE_USER = $data_dpis[NEW_CODE] + 0;
				if ($UPDATE_USER==0) echo "$UPDATE_BY<br>";
			}
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			if ($UPDATE_DATE) $UPDATE_DATE = substr($UPDATE_DATE, 0, 4) . "-" . substr($UPDATE_DATE, 4, 2) . "-" . substr($UPDATE_DATE, 6, 2);

			$cmd = " select POS_ID from PER_POSITION where POS_NO = '$POS_CODE' ";
			$db_dpis->send_cmd($cmd);
			$db_dpis->show_error();
			$data_dpis = $db_dpis->get_array();
			$POS_ID = $data_dpis[POS_ID];
			if (!$POS_ID) echo "เลขที่ตำแหน่ง $POS_CODE<br>";

			$cmd = " INSERT INTO PER_POS_MGTSALARY (POS_ID, EX_CODE, POS_STARTDATE, POS_STATUS, 
							UPDATE_USER, 	UPDATE_DATE)
							VALUES ($POS_ID, '$EX_CODE', '$POS_STARTDATE', $POS_STATUS, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);

			$cmd1 = " SELECT POS_ID, EX_CODE FROM PER_POS_MGTSALARY WHERE POS_ID = $POS_ID AND EX_CODE = '$EX_CODE' "; 
			$count_data = $db_dpis1->send_cmd($cmd1);
			if (!$count_data) {
				echo "$cmd<br>==================<br>";
				$db_dpis->show_error();
				echo "<br>end ". ++$i  ."=======================<br>";
			}
		} // end while						

		$cmd = " select count(POS_ID) as COUNT_NEW from PER_POS_MGTSALARY ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_POS_MGTSALARY - $PER_POS_MGTSALARY - $COUNT_NEW<br>";

		$cmd = " truncate table PER_POS_MGTSALARYHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " SELECT a.PERSONID, a.STARTDATE, a.ENDDATE, a.STATUS, b.SPECIAL_TYPEID
						  FROM PERSONMONEY a, MONEY_POSITION b
						  WHERE a.MONEY_POSITIONID=b.MP_ID
						  ORDER BY PERSONMONEYID ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		$MAX_ID = 1;
		while($data = $db_att->get_array()){
			$PER_POS_MGTSALARYHIS++;
			$PER_CARDNO = trim($data[ID]);
			$EX_CODE = str_pad(trim($data[SPECIAL_TYPEID]), 3, "0", STR_PAD_LEFT);
			$PMH_EFFECTIVEDATE = trim($data[STARTDATE]);
			$PMH_ENDDATE = trim($data[ENDDATE]);
			$STATUS = strtoupper(trim($data[STATUS]));
			if ($STATUS=="Y") $PMH_ACTIVE = 1;
			elseif ($STATUS=="N") $PMH_ACTIVE = 0; 

			$cmd = " select EX_AMT from PER_EXTRATYPE where EX_CODE = '$EX_CODE' ";
			$db_dpis->send_cmd($cmd);
			$db_dpis->show_error();
			$data_dpis = $db_dpis->get_array();
			$PMH_AMT = $data_dpis[EX_AMT];
			if (!$PMH_AMT) echo "เงินประจำตำแหน่ง $EX_CODE<br>";

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = '$PER_CARDNO' ";
			$db_dpis->send_cmd($cmd);
			$db_dpis->show_error();
			$data_dpis = $db_dpis->get_array();
			$PER_ID = $data_dpis[NEW_CODE] + 0;

			$cmd = " INSERT INTO PER_POS_MGTSALARYHIS (PMH_ID, PER_ID, PMH_EFFECTIVEDATE, EX_CODE, 
							PMH_AMT, PMH_ENDDATE, PMH_ACTIVE, UPDATE_USER, UPDATE_DATE)
							VALUES ($MAX_ID, $PER_ID, '$PMH_EFFECTIVEDATE', '$EX_CODE', $PMH_AMT, '$PMH_ENDDATE', 
							$PMH_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);

			$cmd1 = " SELECT PMH_ID FROM PER_POS_MGTSALARYHIS WHERE PMH_ID = $MAX_ID "; 
			$count_data = $db_dpis1->send_cmd($cmd1);
			if (!$count_data) {
				echo "$cmd<br>==================<br>";
				$db_dpis->show_error();
				echo "<br>end ". ++$i  ."=======================<br>";
			}
			$MAX_ID++;
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_POS_MGTSALARYHIS ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_POS_MGTSALARYHIS - $PER_POS_MGTSALARYHIS - $COUNT_NEW<br>";

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_att->free_result();
		$db_att1->free_result(); */
	} // end if

	if( $command=='EXTRATYPE' ){
// เงินเพิ่มพิเศษ 142 ok *********************************************************************************************
/*		$cmd = " truncate table per_extrahis ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " delete from per_extratype where update_user = $UPDATE_USER ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " SELECT SPECIAL_TYPEID, NAME, REMARK, NET_VALUE, ABBNAME FROM SPECIAL_PAYTYPE ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		while($data = $db_att->get_array()){
			$EX_CODE = str_pad(trim($data[SPECIAL_TYPEID]), 3, "0", STR_PAD_LEFT);
			$EX_NAME = trim($data[NAME]);
			$EX_REMARK = trim($data[REMARK]);
			$EX_AMT = $data[NET_VALUE];
			$EX_SHORTNAME = trim($data[ABBNAME]);

			$cmd = " INSERT INTO PER_EXTRATYPE(EX_CODE, EX_NAME, EX_ACTIVE, UPDATE_USER, UPDATE_DATE, EX_SHORTNAME, EX_AMT, EX_REMARK)
							  VALUES (trim('$EX_CODE'), '$EX_NAME', 1, $UPDATE_USER, '$UPDATE_DATE', '$EX_SHORTNAME', $EX_AMT, '$EX_REMARK') ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
//			echo "<br>";
		} // end while			

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_att->free_result();
		$db_att1->free_result(); */
	} // end if
		
	if( $command=='TRAIN_PRODHRM' ){
// ฝึกอบรม 63410 ok 
		$cmd = " SELECT COURSE_CODE, COURSE_NAME, COURSE_TYPE, USER_CREATE, to_char(CREATE_DATE,'yyyy-mm-dd') as CREATE_DATE, 
						  USER_UPDATE, to_char(UPDATE_DATE,'yyyy-mm-dd') as UPDATE_DATE
						  FROM HR_TRAIN_COURSE_CODE 
						  ORDER BY COURSE_CODE ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		while($data = $db_att->get_array()){
			$TR_CODE = trim($data[COURSE_CODE]);
			$TR_NAME = trim($data[COURSE_NAME]);
			$TR_NAME = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($TR_NAME)));
			$TR_TYPE =  1;

			$UPDATE_USER = 99999;
			if ($data[USER_UPDATE]) $USER_UPDATE = trim($data[USER_UPDATE]);
			else $USER_UPDATE = trim($data[USER_CREATE]);
			if ($data[UPDATE_DATE]) $UPDATE_DATE = trim($data[UPDATE_DATE]);
			else  $UPDATE_DATE = trim($data[CREATE_DATE]);
			if ($USER_UPDATE) {
				$cmd = " select ID from USER_DETAIL where USERNAME = '$USER_UPDATE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				if ($data2[ID]) $UPDATE_USER = $data2[ID];
			}
			if (!$UPDATE_DATE) $UPDATE_DATE = date("Y-m-d H:i:s");

			$cmd = " SELECT TR_CODE FROM PER_TRAIN WHERE TR_NAME = '$TR_NAME' ";
			$count_data = $db_dpis->send_cmd($cmd);
			if ($count_data) {
				$data_dpis = $db_dpis->get_array();
				$MAP_TR_CODE = $data_dpis[TR_CODE];
			} else {
				$cmd = " INSERT INTO PER_TRAIN(TR_CODE, TR_TYPE, TR_NAME, TR_ACTIVE, UPDATE_USER, UPDATE_DATE)
								  VALUES ('$TR_CODE', $TR_TYPE, '$TR_NAME', 1, $UPDATE_USER, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
				$MAP_TR_CODE = $TR_CODE;
			}

			$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
							VALUES ('PER_TRAIN', '$TR_CODE', '$MAP_TR_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
		} // end while			
		
		$cmd = " select max(TRN_ID) as MAX_ID from PER_TRAINING ";
		$db_dpis1->send_cmd($cmd);
		$data1 = $db_dpis1->get_array();
		$MAX_ID = $data1[MAX_ID] + 1;

		$cmd = " SELECT ID, ORDER_MOVE, MP_YEAR, COURSE_CODE, COURSE_TYPE, COURSE_NAME, PLACE_NAME, PROVINCE_CODE, 
						  COUNTRY_CODE, to_char(START_TRAIN_DATE,'yyyy-mm-dd') as START_TRAIN_DATE, 
						  to_char(END_TRAIN_DATE,'yyyy-mm-dd') as END_TRAIN_DATE, OWNER, MEMO, POS_NUM_CODE_SIT,
						  MP_COMMAND_NUM, to_char(MP_COMMAND_DATE,'yyyy-mm-dd') as MP_COMMAND_DATE, FLAG_TO_NAME_CODE, 
						  to_char(MP_POS_DATE,'yyyy-mm-dd') as MP_POS_DATE, USER_CREATE, to_char(CREATE_DATE,'yyyy-mm-dd') as CREATE_DATE, 
						  USER_UPDATE, to_char(UPDATE_DATE,'yyyy-mm-dd') as UPDATE_DATE, CUR_YEAR, POS_NUM_CODE_SIT_CODE, 
						  POS_NUM_CODE_SIT_ABB, NO_DAY, REC_STATUS, TRAIN_SESSION, REF_DEPT, TRAIN_COUNTRY
						  FROM HR_TRAIN_OFFICER
						  ORDER BY ID, ORDER_MOVE ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		while($data = $db_att->get_array()){
			$PER_CARDNO = trim($data[ID]);
			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = '$PER_CARDNO' ";
			$db_dpis->send_cmd($cmd);
			$db_dpis->show_error();
			$data_dpis = $db_dpis->get_array();
			$PER_ID = $data_dpis[NEW_CODE] + 0;

			if ($PER_ID > 0) {
				$TR_CODE = $data[COURSE_CODE];
				$TRN_BOOK_NO = trim($data[POS_NUM_CODE_SIT_ABB]).trim($data[MP_COMMAND_NUM]);
				$CUR_YEAR = trim($data[CUR_YEAR]);
				if (trim($CUR_YEAR)) $TRN_BOOK_NO .= '/'.trim($CUR_YEAR);
				$TRN_BOOK_DATE = trim($data[MP_COMMAND_DATE]);
				$TRN_DAY = $data[NO_DAY];
				if (!$TRN_DAY) $TRN_DAY = "NULL";
				$TRN_PASS = 1;
				$TRN_REMARK = trim($data[MEMO]);
				$TRN_ORG = trim($data[OWNER]);
				$MP_YEAR = trim($data[MP_YEAR]);
				$TRN_PLACE = trim($data[PLACE_NAME]);
				$TRN_STARTDATE = trim($data[START_TRAIN_DATE]);
				if (!$TRN_STARTDATE) $TRN_STARTDATE = ($MP_YEAR - 543) . "-00-00";
				$TRN_ENDDATE = trim($data[END_TRAIN_DATE]);
				if (!$TRN_ENDDATE) $TRN_ENDDATE = ($MP_YEAR - 543) . "-00-00";

				$UPDATE_USER = 99999;
				if ($data[USER_UPDATE]) $USER_UPDATE = trim($data[USER_UPDATE]);
				else $USER_UPDATE = trim($data[USER_CREATE]);
				if ($data[UPDATE_DATE]) $UPDATE_DATE = trim($data[UPDATE_DATE]);
				else  $UPDATE_DATE = trim($data[CREATE_DATE]);
				if ($USER_UPDATE) {
					$cmd = " select ID from USER_DETAIL where USERNAME = '$USER_UPDATE' ";
					$db_dpis2->send_cmd($cmd);
	//				$db_dpis2->show_error();
					$data2 = $db_dpis2->get_array();
					if ($data2[ID]) $UPDATE_USER = $data2[ID];
				}
				if (!$UPDATE_DATE) $UPDATE_DATE = date("Y-m-d H:i:s");

	//			$TRN_NO = trim($data[GENERATIONNO]);
	//			$TRN_DEGREE_RECEIVE = trim($data[DEGREERECEIVE]);
	//			$TRN_POINT = trim($data[POINTRECEIVE]);
	//			$CT_CODE = trim($data[CODE]);
				$TRN_PLACE = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($TRN_PLACE)));
				$TRN_ORG = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($TRN_ORG)));
				$TRN_REMARK = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($TRN_REMARK)));

				if  (!$TR_CODE) {				
					$TRN_COURSE_NAME = $data[COURSE_NAME];
					$TRN_COURSE_NAME = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($TRN_COURSE_NAME)));
				}

				$cmd = " INSERT INTO PER_TRAINING(TRN_ID, PER_ID, TRN_TYPE, TR_CODE, TRN_NO, TRN_STARTDATE, TRN_ENDDATE, TRN_ORG, 
								TRN_PLACE, CT_CODE, UPDATE_USER, UPDATE_DATE, PER_CARDNO, TRN_DAY, TRN_REMARK, TRN_PASS, TRN_BOOK_NO, TRN_BOOK_DATE, 
								TRN_PROJECT_NAME, TRN_COURSE_NAME, TRN_DEGREE_RECEIVE, TRN_POINT, TRN_OBJECTIVE)
								VALUES ($MAX_ID, $PER_ID, 1, '$TR_CODE', '$TRN_NO', '$TRN_STARTDATE', '$TRN_ENDDATE', '$TRN_ORG', '$TRN_PLACE', 
								'$CT_CODE', $UPDATE_USER, '$UPDATE_DATE', '$PER_CARDNO', $TRN_DAY, '$TRN_REMARK', $TRN_PASS, '$TRN_BOOK_NO', '$TRN_BOOK_DATE', 
								'$TRN_PROJECT_NAME', '$TRN_COURSE_NAME', '$TRN_DEGREE_RECEIVE', '$TRN_POINT', '$TRN_OBJECTIVE') ";
				$db_dpis->send_cmd($cmd);

				$cmd1 = " SELECT TRN_ID FROM PER_TRAINING WHERE TRN_ID = $MAX_ID "; 
				$count_data = $db_dpis1->send_cmd($cmd1);
				if (!$count_data) {
					echo "$cmd<br>==================<br>";
					$db_dpis->show_error();
					echo "<br>end ". ++$i  ."=======================<br>";
				}
				$MAX_ID++;
				$PER_TRAINING++;
			}
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_TRAINING ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_TRAINING - $PER_TRAINING - $COUNT_NEW<br>";

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_att->free_result();
		$db_att1->free_result();
	} // end if

	if( $command=='TRAIN_BKKADM' ){
// ฝึกอบรม 63410 ok 
		$cmd = " truncate table per_training ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " delete from per_train where update_user = $UPDATE_USER ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " delete from per_map_code where map_code = 'PER_TRAIN' ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error();

		$cmd = " SELECT ID_C, CODE_C, NAME_C, EDITOR_C, TRAINER_C, TEL_C, FAX_C, TYPE_C, DURMONTH_C, DURDAY_C,
						  DURNIGHT_C, DURATION_C, OTHER_C, PRERIODMONTH_C, PRERIODDAY_C, PRERIODNIGHT_C, REPEAT_C,
						  FISCALYEAR_C, BEGIN_C, END_C, FEE_C, DETAIL_C, CLASSID_C, REMARK_C, STATUS_C, SECTION_C, 
						  WEB_C, DURYEAR_C, DURATIONY_C, DURATIONM_C, PRERIODYEAR_C, STRATEGY_C, AIM_C, PER_C,
						  TOTAL_C, PLACE_C, AUTHORITY_C, AUTHORITY_TEL_C, UPDATE_BY_C, to_char(UPDATE_DATE_C,'yyyy-mm-dd') as UPDATE_DATE_C, BRANCH_C,
						  PROJECT_CODE_C, SEQID_C
						  FROM COURSE_T 
						  ORDER BY ID_C ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		while($data = $db_att->get_array()){
			$ID_C = trim($data[ID_C]);
			$TR_NAME = trim($data[NAME_C]);
			$TR_NAME = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($TR_NAME)));
			$TR_TYPE = trim($data[TYPE_C]);
			if ($TR_TYPE=="ดูงาน") $TR_TYPE = 2;
			elseif (strpos($TR_TYPE,"สัมมนา") !== false) $TR_TYPE = "3";
			else  $TR_TYPE = 1;
			$UPDATE_DATE = trim($data[UPDATE_DATE_C]);

			$cmd = " SELECT TR_CODE FROM PER_TRAIN WHERE TR_NAME = '$TR_NAME' ";
			$count_data = $db_dpis->send_cmd($cmd);
			if ($count_data) {
				$data_dpis = $db_dpis->get_array();
				$TR_CODE = $data_dpis[TR_CODE];
			} else {
				$cmd = " INSERT INTO PER_TRAIN(TR_CODE, TR_TYPE, TR_NAME, TR_ACTIVE, UPDATE_USER, UPDATE_DATE)
								  VALUES ('$ID_C', $TR_TYPE, '$TR_NAME', 1, $UPDATE_USER, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
				$TR_CODE = $ID_C;
			}

			$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
							VALUES ('PER_TRAIN', '$ID_C', '$TR_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
		} // end while			
		
		$cmd = " SELECT a.ID_C, IDCARD_C, COURSE_C, ORDERID_C, ROOMMATE_C, WORK_LINE_C, WORK_C, 
						  LEVEL_C, DEPARTMENT_C, a.STATUS_C, COURSE_TYPE_C, a.UPDATE_BY_C, to_char(a.UPDATE_DATE_C,'yyyy-mm-dd') as UPDATE_DATE_C, 
						  a.REMARK_C, CODE_C, NAME_C, EDITOR_C, TRAINER_C, TEL_C, FAX_C, TYPE_C, DURMONTH_C, DURDAY_C,
						  DURNIGHT_C, DURATION_C, OTHER_C, PRERIODMONTH_C, PRERIODDAY_C, PRERIODNIGHT_C, REPEAT_C,
						  FISCALYEAR_C,  to_char(BEGIN_C,'yyyy-mm-dd') as BEGIN_C, to_char(END_C,'yyyy-mm-dd') as END_C, FEE_C, 
						  DETAIL_C, CLASSID_C, b.REMARK_C, b.STATUS_C as B_STATUS_C, SECTION_C, WEB_C, DURYEAR_C, DURATIONY_C, 
						  DURATIONM_C, PRERIODYEAR_C, STRATEGY_C, AIM_C, PER_C,
						  TOTAL_C, PLACE_C, AUTHORITY_C, AUTHORITY_TEL_C, BRANCH_C,
						  PROJECT_CODE_C, SEQID_C
						  FROM TRAINING_T a, COURSE_T b
						 WHERE a.COURSE_C = b.ID_C(+)
						  ORDER BY IDCARD_C ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		$MAX_ID = 1;
		while($data = $db_att->get_array()){
			$PER_CARDNO = trim($data[IDCARD_C]);
			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = '$PER_CARDNO' ";
			$db_dpis->send_cmd($cmd);
			$db_dpis->show_error();
			$data_dpis = $db_dpis->get_array();
			$PER_ID = $data_dpis[NEW_CODE] + 0;

			if ($PER_ID > 0) {
				$COURSE_C = $data[COURSE_C];
				$TRN_BOOK_NO = trim($data[ORDERID_C]);
				$TRN_REMARK = trim($data[ROOMMATE_C]);
				$TRN_ORG = trim($data[WORK_C])." ".trim($data[DEPARTMENT_C]);
				$TRN_PASS = trim($data[STATUS_C]);
				$TRN_PASS = 1;
	//		a.COURSE_TYPE_C = b.CLASSID_C
				$TRN_REMARK = trim($data[REMARK_C]);
				if ($TRN_REMARK=="null") $TRN_REMARK = "";
				$TRN_ORG = trim($data[TRAINER_C])." ".trim($data[EDITOR_C]);
				$FISCALYEAR_C = trim($data[FISCALYEAR_C]);
				$STRATEGY_C = strtoupper(trim($data[STRATEGY_C]));
				$TRN_PLACE = trim($data[PLACE_C]);
				$TRN_STARTDATE = trim($data[BEGIN_C]);
				if (!$TRN_STARTDATE && $FISCALYEAR_C) $TRN_STARTDATE = ($FISCALYEAR_C - 543) . "-00-00";
				$TRN_ENDDATE = trim($data[END_C]);
				if (!$TRN_ENDDATE && $FISCALYEAR_C) $TRN_ENDDATE = ($FISCALYEAR_C - 543) . "-00-00";
				$TRN_OBJECTIVE = trim($data[AIM_C]);
				$TRN_PROJECT_NAME = trim($data[PROJECT_CODE_C]);

				$UPDATE_BY_C = $data[UPDATE_BY_C];
				if ($UPDATE_BY_C) {
					$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'USER_DETAIL' AND OLD_CODE = '$UPDATE_BY_C' ";
					$db_dpis->send_cmd($cmd);
	//				$db_dpis->show_error();
					$data_dpis = $db_dpis->get_array();
					$UPDATE_USER = $data_dpis[NEW_CODE] + 0;
	//				if ($UPDATE_USER==0) echo "$UPDATE_BY_C<br>";
					if ($UPDATE_USER==0) $UPDATE_USER = 99999;
				}
				$UPDATE_DATE = trim($data[UPDATE_DATE_C]);

	//			$TRN_NO = trim($data[GENERATIONNO]);
	//			$TRN_DEGREE_RECEIVE = trim($data[DEGREERECEIVE]);
	//			$TRN_POINT = trim($data[POINTRECEIVE]);
	//			$TRN_BOOK_DATE = trim($data[REGIS_DATE]);
	//			$CT_CODE = trim($data[CODE]);
				$TRN_PLACE = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($TRN_PLACE)));
				$TRN_ORG = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($TRN_ORG)));
				$TRN_REMARK = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($TRN_REMARK)));
				$TRN_COURSE_NAME = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($TRN_COURSE_NAME)));

				$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_TRAIN' AND OLD_CODE = $COURSE_C ";
				$db_dpis->send_cmd($cmd);
				$db_dpis->show_error();
				$data_dpis = $db_dpis->get_array();
				$TR_CODE = $data_dpis[NEW_CODE];

				$cmd = " INSERT INTO PER_TRAINING(TRN_ID, PER_ID, TRN_TYPE, TR_CODE, TRN_NO, TRN_STARTDATE, TRN_ENDDATE, TRN_ORG, 
								TRN_PLACE, CT_CODE, UPDATE_USER, UPDATE_DATE, PER_CARDNO, TRN_REMARK, TRN_PASS, TRN_BOOK_NO, TRN_BOOK_DATE, 
								TRN_PROJECT_NAME, TRN_COURSE_NAME, TRN_DEGREE_RECEIVE, TRN_POINT, TRN_OBJECTIVE)
								VALUES ($MAX_ID, $PER_ID, 1, '$TR_CODE', '$TRN_NO', '$TRN_STARTDATE', '$TRN_ENDDATE', '$TRN_ORG', '$TRN_PLACE', 
								'$CT_CODE', $UPDATE_USER, '$UPDATE_DATE', '$PER_CARDNO', '$TRN_REMARK', $TRN_PASS, '$TRN_BOOK_NO', '$TRN_BOOK_DATE', 
								'$TRN_PROJECT_NAME', '$TRN_COURSE_NAME', '$TRN_DEGREE_RECEIVE', '$TRN_POINT', '$TRN_OBJECTIVE') ";
				$db_dpis->send_cmd($cmd);

				$cmd1 = " SELECT TRN_ID FROM PER_TRAINING WHERE TRN_ID = $MAX_ID "; 
				$count_data = $db_dpis1->send_cmd($cmd1);
				if (!$count_data) {
					echo "$cmd<br>==================<br>";
					$db_dpis->show_error();
					echo "<br>end ". ++$i  ."=======================<br>";
				}
				$MAX_ID++;
				$PER_TRAINING++;
			}
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_TRAINING ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_TRAINING - $PER_TRAINING - $COUNT_NEW<br>";

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_att->free_result();
		$db_att1->free_result();
	} // end if

	if( $command=='SCHOLARSHIP' ){
// ต่างประเทศ 
		$cmd = " truncate table per_scholar ";
		//$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " delete from per_scholarship where update_user = $UPDATE_USER ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " delete from per_map_code where map_code = 'SCHOLARSHIP' ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error();

		$cmd = " SELECT ACTIVITY_ID, ACTIVITY, FIELD, KIND_OF_ACTIVITY, OWNER, NUMBER_OF_MEMBER, DURATION, DUE_DATE,
						  COUNTRY, FISCAL_YEAR, KIND_OF_BUDGET, BUDGET, TEE_KOR_TOR, DATE, MONTH_YEAR, FILE
						  FROM ACTIVITY 
						  ORDER BY ACTIVITY_ID ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		while($data = $db_att->get_array()){
			$SCH_CODE = trim($data[ACTIVITY_ID]);
			$SCH_NAME = trim($data[ACTIVITY]);
			$SCH_NAME = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($SCH_NAME)));
			$FIELD = trim($data[FIELD]);
			$TR_TYPE = trim($data[KIND_OF_ACTIVITY]);
			if ($TR_TYPE=="ดูงาน") $TR_TYPE = 2;
			elseif (strpos($TR_TYPE,"สัมมนา") !== false) $TR_TYPE = "3";
			elseif (strpos($TR_TYPE,"ประชุม") !== false) $TR_TYPE = "4";
			else  $TR_TYPE = 1;
			$SCH_OWNER = trim($data[OWNER]);
			$NUMBER_OF_MEMBER = trim($data[NUMBER_OF_MEMBER]);
			$DURATION = trim($data[DURATION]);
			if ($DURATION=="21 ม.ค. - 7 ก.พ. 56") {
				$SCH_START_DATE = "2013-01-21";
				$SCH_END_DATE = "2013-02-07";
			} elseif ($DURATION=="18 พ.ย. - 13 ธ.ค. 55") {
				$SCH_START_DATE = "2012-11-18";
				$SCH_END_DATE = "2012-12-13";
			} elseif ($DURATION=="4 - 14 พ.ย. 55") {
				$SCH_START_DATE = "2012-11-04";
				$SCH_END_DATE = "2012-11-14";
			} elseif ($DURATION=="5 - 17 พ.ย. 55") {
				$SCH_START_DATE = "2012-11-05";
				$SCH_END_DATE = "2012-11-17";
			} elseif ($DURATION=="26 พ.ย. - 1 ธ.ค. 55") {
				$SCH_START_DATE = "2012-11-26";
				$SCH_END_DATE = "2012-12-01";
			} elseif ($DURATION=="4 - 19 ธ.ค. 55") {
				$SCH_START_DATE = "2012-12-04";
				$SCH_END_DATE = "2012-12-19";
			} elseif ($DURATION=="18 - 23 พ.ย. 55") {
				$SCH_START_DATE = "2012-11-18";
				$SCH_END_DATE = "2012-11-23";
			} elseif ($DURATION=="18 - 27 พ.ย. 55") {
				$SCH_START_DATE = "2012-11-18";
				$SCH_END_DATE = "2012-11-27";
			} elseif ($DURATION=="11  - 18 พ.ย. 55") {
				$SCH_START_DATE = "2012-11-11";
				$SCH_END_DATE = "2012-11-18";
			} elseif ($DURATION=="24 พ.ย. - 2 ธ.ค. 55") {
				$SCH_START_DATE = "2012-11-24";
				$SCH_END_DATE = "2012-12-02";
			} elseif ($DURATION=="15 - 21 ธ.ค. 55") {
				$SCH_START_DATE = "2012-12-15";
				$SCH_END_DATE = "2012-12-21";

			} elseif ($DURATION=="21 ต.ค. - 2 พ.ย. 55") {
				$SCH_START_DATE = "2013-01-14";
				$SCH_END_DATE = "2013-01-18";
			} elseif ($DURATION=="27 ม.ค. - 1 ก.พ. 56") {
				$SCH_START_DATE = "2013-01-14";
				$SCH_END_DATE = "2013-01-18";
			} elseif ($DURATION=="4 - 8 พ.ค. 55") {
				$SCH_START_DATE = "2013-01-14";
				$SCH_END_DATE = "2013-01-18";
			} elseif ($DURATION=="27 ม.ค. - 4 ก.พ. 56") {
				$SCH_START_DATE = "2013-01-14";
				$SCH_END_DATE = "2013-01-18";
			} elseif ($DURATION=="5 - 12 ก.พ. 55") {
				$SCH_START_DATE = "2013-01-14";
				$SCH_END_DATE = "2013-01-18";
			} elseif ($DURATION=="14 - 18 ม.ค. 56") {
				$SCH_START_DATE = "2013-01-14";
				$SCH_END_DATE = "2013-01-18";
			} elseif ($DURATION=="27 ม.ค. - 16 ก.พ. 56") {
				$SCH_START_DATE = "2013-01-14";
				$SCH_END_DATE = "2013-01-18";
			} elseif ($DURATION=="13 - 20 ก.พ. 56") {
				$SCH_START_DATE = "2013-01-14";
				$SCH_END_DATE = "2013-01-18";
			} elseif ($DURATION=="19 - 25 มี.ค. 56") {
				$SCH_START_DATE = "2013-01-14";
				$SCH_END_DATE = "2013-01-18";
			} elseif ($DURATION=="21 ม.ค. - 7 ก.พ. 56") {
				$SCH_START_DATE = "2013-01-14";
				$SCH_END_DATE = "2013-01-18";
			} elseif ($DURATION=="21 - 27 ต.ค. 55") {
				$SCH_START_DATE = "2013-01-14";
				$SCH_END_DATE = "2013-01-18";
			} elseif ($DURATION=="28 ก.พ. - 16 มี.ค. 56") {
				$SCH_START_DATE = "2013-01-14";
				$SCH_END_DATE = "2013-01-18";
			} elseif ($DURATION=="6 - 10 มี.ค. 56") {
				$SCH_START_DATE = "2013-01-14";
				$SCH_END_DATE = "2013-01-18";
			} elseif ($DURATION=="3 - 9 ก.พ. 56") {
				$SCH_START_DATE = "2013-01-14";
				$SCH_END_DATE = "2013-01-18";
			} elseif ($DURATION=="4 - 8 ก.พ. 56") {
				$SCH_START_DATE = "2013-01-14";
				$SCH_END_DATE = "2013-01-18";
			} elseif ($DURATION=="31 พ.ค. - 6 มิ.ย. 56") {
				$SCH_START_DATE = "2013-01-14";
				$SCH_END_DATE = "2013-01-18";
			} elseif ($DURATION=="16 - 24 พ.ค. 56") {
				$SCH_START_DATE = "2013-01-14";
				$SCH_END_DATE = "2013-01-18";
			} elseif ($DURATION=="10 - 21 ก.พ. 56") {
				$SCH_START_DATE = "2013-01-14";
				$SCH_END_DATE = "2013-01-18";
			} elseif ($DURATION=="17 - 25 มี.ค. 56") {
				$SCH_START_DATE = "2013-01-14";
				$SCH_END_DATE = "2013-01-18";
			} elseif ($DURATION=="6 - 14 มี.ค. 56") {
				$SCH_START_DATE = "2013-01-14";
				$SCH_END_DATE = "2013-01-18";
			} elseif ($DURATION=="17 - 21 พ.ย. 55") {
				$SCH_START_DATE = "2013-01-14";
				$SCH_END_DATE = "2013-01-18";
			} elseif ($DURATION=="4 - 10 มี.ค. 56") {
				$SCH_START_DATE = "2013-01-14";
				$SCH_END_DATE = "2013-01-18";
			} elseif ($DURATION=="9 - 16 พ.ย. 55") {
				$SCH_START_DATE = "2013-01-14";
				$SCH_END_DATE = "2013-01-18";
			} elseif ($DURATION=="8 - 15 พ.ย. 55") {
				$SCH_START_DATE = "2013-01-14";
				$SCH_END_DATE = "2013-01-18";
			} elseif ($DURATION=="1 - 7 ธ.ค. 55") {
				$SCH_START_DATE = "2013-01-14";
				$SCH_END_DATE = "2013-01-18";
			} elseif ($DURATION=="19 พ.ย. - 16 ธ.ค. 55") {
				$SCH_START_DATE = "2013-01-14";
				$SCH_END_DATE = "2013-01-18";
			} elseif ($DURATION=="11 - 17 พ.ย. 55") {
				$SCH_START_DATE = "2013-01-14";
				$SCH_END_DATE = "2013-01-18";
			} elseif ($DURATION=="") {
				$SCH_START_DATE = "2013-01-14";
				$SCH_END_DATE = "2013-01-18";
			} elseif ($DURATION=="") {
				$SCH_START_DATE = "2013-01-14";
				$SCH_END_DATE = "2013-01-18";
			} elseif ($DURATION=="") {
				$SCH_START_DATE = "2013-01-14";
				$SCH_END_DATE = "2013-01-18";
			} elseif ($DURATION=="") {
				$SCH_START_DATE = "2013-01-14";
				$SCH_END_DATE = "2013-01-18";
			} elseif ($DURATION=="") {
				$SCH_START_DATE = "2013-01-14";
				$SCH_END_DATE = "2013-01-18";
			} elseif ($DURATION=="") {
				$SCH_START_DATE = "2013-01-14";
				$SCH_END_DATE = "2013-01-18";
			} elseif ($DURATION=="") {
				$SCH_START_DATE = "2013-01-14";
				$SCH_END_DATE = "2013-01-18";
			} elseif ($DURATION=="") {
				$SCH_START_DATE = "2013-01-14";
				$SCH_END_DATE = "2013-01-18";
			} elseif ($DURATION=="") {
				$SCH_START_DATE = "2013-01-14";
				$SCH_END_DATE = "2013-01-18";
			} elseif ($DURATION=="") {
				$SCH_START_DATE = "2013-01-14";
				$SCH_END_DATE = "2013-01-18";
			} elseif ($DURATION=="") {
				$SCH_START_DATE = "2013-01-14";
				$SCH_END_DATE = "2013-01-18";
			} elseif ($DURATION=="") {
				$SCH_START_DATE = "2013-01-14";
				$SCH_END_DATE = "2013-01-18";
			} elseif ($DURATION=="") {
				$SCH_START_DATE = "2013-01-14";
				$SCH_END_DATE = "2013-01-18";
			} elseif ($DURATION=="") {
				$SCH_START_DATE = "2013-01-14";
				$SCH_END_DATE = "2013-01-18";
			} elseif ($DURATION=="") {
				$SCH_START_DATE = "2013-01-14";
				$SCH_END_DATE = "2013-01-18";
			} elseif ($DURATION=="") {
				$SCH_START_DATE = "2013-01-14";
				$SCH_END_DATE = "2013-01-18";
			} elseif ($DURATION=="") {
				$SCH_START_DATE = "2013-01-14";
				$SCH_END_DATE = "2013-01-18";
			}
			$DUE_DATE = trim($data[DUE_DATE]);
			$COUNTRY = trim($data[COUNTRY]);
			$SCH_YEAR = trim($data[FISCAL_YEAR]);
			$ST_CODE = trim($data[KIND_OF_BUDGET]);
			$ST_CODE = "09";
			if ($ST_CODE=="ทุน 1 (ก)") $ST_CODE = "01";
			elseif (strpos($ST_CODE,"สัมมนา") !== false) $ST_CODE = "3";
			elseif (strpos($ST_CODE,"ประชุม") !== false) $ST_CODE = "4";
			else  echo "$ST_CODE<br>";
			$SCH_BUDGET = trim($data[BUDGET]);
			$SCH_APP_DOC_NO = trim($data[TEE_KOR_TOR]);
			$DATE = str_pad(trim($data[DATE]), 2, "0", STR_PAD_LEFT);
			$MONTH_YEAR = trim($data[MONTH_YEAR]);
			if ($MONTH_YEAR=="ก.ย. 55") 
				$SCH_DOC_DATE = "2012-09-" . $DATE;
			elseif ($MONTH_YEAR=="ต.ค. 55") 
				$SCH_DOC_DATE = "2012-10-" . $DATE;
			elseif ($MONTH_YEAR=="พ.ย. 55") 
				$SCH_DOC_DATE = "2012-11-" . $DATE;
			elseif ($MONTH_YEAR=="ม.ค. 56") 
				$SCH_DOC_DATE = "2013-01-" . $DATE;
			elseif ($MONTH_YEAR=="ม.ค. 56") 
				$SCH_DOC_DATE = "2013-01-" . $DATE;
			elseif ($MONTH_YEAR=="ม.ค. 56") 
				$SCH_DOC_DATE = "2013-01-" . $DATE;
			elseif ($MONTH_YEAR=="ม.ค. 56") 
				$SCH_DOC_DATE = "2013-01-" . $DATE;
			elseif ($MONTH_YEAR=="ม.ค. 56") 
				$SCH_DOC_DATE = "2013-01-" . $DATE;
			$FILE = trim($data[FILE]);
			$SCH_TYPE = 1;
			$SCH_ACTIVE = 1;
			$SCH_CLASS = $SCH_APP_PER_ID = "NULL";

			$CT_CODE_GO = "";
			if ($COUNTRY) {
				$cmd = " SELECT CT_CODE FROM PER_COUNTRY WHERE CT_NAME = '$COUNTRY' ";
				$count_data = $db_dpis->send_cmd($cmd);
				if ($count_data) {
					$data_dpis = $db_dpis->get_array();
					$CT_CODE_GO = $data_dpis[CT_CODE];
				} else echo "$COUNTRY<br>";
			}
/*
			$cmd = " SELECT TR_CODE FROM PER_TRAIN WHERE TR_NAME = '$TR_NAME' ";
			$count_data = $db_dpis->send_cmd($cmd);
			if ($count_data) {
				$data_dpis = $db_dpis->get_array();
				$TR_CODE = $data_dpis[TR_CODE];
			} else { */
			$cmd = " insert into PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
							SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
							CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, 
							SCH_START_DATE2, SCH_END_DATE2, SCH_PLACE2, SCH_DEAD_LINE) 
							values ('$SCH_CODE', '$SCH_NAME', '$ST_CODE', '$SCH_OWNER', $SCH_ACTIVE, $SESS_USERID, '$UPDATE_DATE',
							'$SCH_YEAR', $SCH_TYPE, $SCH_CLASS, '$EN_CODE', '$EM_CODE', '$SCH_START_DATE', '$SCH_END_DATE', '$SCH_PLACE', '$CT_CODE_OWN',
							'$CT_CODE_GO', $SCH_BUDGET, '$SCH_APP_DOC_NO', '$SCH_DOC_DATE', '$SCH_APP_DATE', $SCH_APP_PER_ID, '$SCH_REMARK', 
							'$SCH_START_DATE2', '$SCH_END_DATE2', '$SCH_PLACE2', '$SCH_DEAD_LINE') ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
echo $cmd;
//				$TR_CODE = $ID_C;
//			}

//			$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
//							VALUES ('PER_TRAIN', '$ID_C', '$TR_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
//			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
		} // end while			
		
		$cmd = " select max(SC_ID) as max_id from PER_SCHOLAR ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$MAX_ID = $data[max_id] + 1;
					
		$cmd = " SELECT FIELD1, NAME_ID, TITLE, NAME, SURNAME, POSITION, POSITION_LEVEL, SUB_OFFICE, OFFICE, ACTIVITY_ID
						  FROM NAME
						  ORDER BY NAME_ID ";
		$db_att->send_cmd($cmd);
		$db_att->show_error();
		echo "<br>";
		while($data = $db_att->get_array()){
			$TITLE = trim($data[TITLE]);
			if ($TITLE) {
				$cmd = " select PN_CODE from PER_PRENAME where PN_NAME = '$TITLE' OR PN_SHORTNAME = '$TITLE' ";
				$count_data = $db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
				if ($count_data) {
					$data_dpis = $db_dpis->get_array();
					$PN_CODE = trim($data_dpis[PN_CODE]);
				} else echo "$TITLE<br>";
			}
			$OFFICE = trim($data[OFFICE]);
			if ($OFFICE) {
				$cmd = " select ORG_ID from PER_ORG where ORG_NAME = '$OFFICE' ";
				$count_data = $db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
				if ($count_data) {
					$data_dpis = $db_dpis->get_array();
					$DEPARTMENT_ID = trim($data_dpis[ORG_ID]);
				} else echo "$OFFICE<br>";
			}
			$SC_NAME = trim($data[NAME]);
			$SC_SURNAME = trim($data[SURNAME]);
			$cmd = " select PER_ID, PER_CARDNO from PER_PERSONAL where PER_NAME = '$SC_NAME' AND PER_SURNAME = '$SC_SURNAME' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			$data_dpis = $db_dpis->get_array();
			$PER_ID = $data_dpis[PER_ID] + 0;

			if ($PER_ID > 0) {
				$SC_TYPE = 1;
				$PER_CARDNO = trim($data_dpis[PER_CARDNO]);
			} else {
				$SC_TYPE = 2;
			}
			$SCH_CODE = trim($data[ACTIVITY_ID]);
			$cmd = " select SCH_START_DATE, SCH_END_DATE from PER_SCHOLARSHIP where SCH_CODE = '$SCH_CODE' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			$data_dpis = $db_dpis->get_array();
			$SC_STARTDATE = trim($data_dpis[SCH_START_DATE]);
			$SC_ENDDATE = trim($data_dpis[SCH_END_DATE]);
			$EL_CODE = "99";
			$EN_CODE = $EM_CODE = $INS_CODE = "NULL";
			if (!$SC_STARTDATE) $SC_STARTDATE = "-";
			if (!$SC_ENDDATE) $SC_ENDDATE = "-";

			$cmd = " INSERT INTO PER_SCHOLAR(SC_ID, SC_TYPE, PER_ID, EN_CODE, EM_CODE, SCH_CODE, INS_CODE, EL_CODE, SC_STARTDATE, 
							  SC_ENDDATE, SC_FINISHDATE, SC_BACKDATE, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID, SC_REMARK,
							  SC_DOCNO, SC_DOCDATE, SC_INSTITUTE, CT_CODE, SC_FUND)
							  VALUES ($MAX_ID, 1, $PER_ID, $EN_CODE, $EM_CODE, '$SCH_CODE', $INS_CODE, '$EL_CODE', '$SC_STARTDATE', 
							  '$SC_ENDDATE', '$SC_FINISHDATE', '$SC_BACKDATE', $UPDATE_USER, '$UPDATE_DATE', $DEPARTMENT_ID, '$SC_REMARK',
							  '$SC_DOCNO', '$SC_DOCDATE', '$INS_ANOTHER', '$CT_CODE', '$SC_FUND') ";
			$db_dpis->send_cmd($cmd);
			echo $cmd;

			$cmd1 = " SELECT SC_ID FROM PER_SCHOLAR WHERE SC_ID = $MAX_ID "; 
			$count_data = $db_dpis1->send_cmd($cmd1);
			if (!$count_data) {
				echo "$cmd<br>==================<br>";
				$db_dpis->show_error();
				echo "<br>end ". ++$i  ."=======================<br>";
			}
			$MAX_ID++;
			$PER_SCHOLAR++;
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_SCHOLAR ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_SCHOLAR - $PER_SCHOLAR - $COUNT_NEW<br>";

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_att->free_result();
		$db_att1->free_result();
	} // end if

	if( $command=='SCHOLAR' ){
// ลาศึกษาต่อ 46 **************************************************************************************
		$cmd = " delete from per_scholar ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " SELECT PERSONID, REQUESTTYPE, ORDERNO, ORDERDATE, STARTDATE, FINISHEDDATE, 
						  INSTUTUTENAME, COUNTRYCODE, DEGREE, MAJOR, FACULTY, REMARK, CREATEDATE, CREATEBY, 
						  MODDATE, MODBY, INS_ANOTHER, REPORTDATE, MAJORID, MAJOR_SUBJECTID, EDUCATION_FUNDTYPEID
						  FROM EDUREQ_TIMEREQ
						  ORDER BY EDUREQ_TIMEREQ_ID ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		$MAX_ID = 1;
		while($data = $db_att->get_array()){
			$PER_SCHOLAR++;
			$PER_CARDNO = trim($data[ID]);
			$SC_DOCNO = trim($data[ORDERNO]);
			$ORDERDATE = trim($data[ORDERDATE]);
			$STARTDATE = trim($data[STARTDATE]);
			$FINISHEDDATE = trim($data[FINISHEDDATE]);
			$SC_DOCDATE = $SC_STARTDATE = $SC_ENDDATE = "";
			if ($ORDERDATE && $ORDERDATE != "00000000000000") 
				$SC_DOCDATE = substr($ORDERDATE, 0, 4) . "-" . substr($ORDERDATE, 4, 2) . "-" . substr($ORDERDATE, 6, 2);
			if ($STARTDATE && $STARTDATE != "00000000000000") 
				$SC_STARTDATE = substr($STARTDATE, 0, 4) . "-" . substr($STARTDATE, 4, 2) . "-" . substr($STARTDATE, 6, 2);
			if ($FINISHEDDATE && $FINISHEDDATE != "00000000000000") 
				$SC_ENDDATE = substr($FINISHEDDATE, 0, 4) . "-" . substr($FINISHEDDATE, 4, 2) . "-" . substr($FINISHEDDATE, 6, 2);
			$INSTUTUTENAME = trim($data[INSTUTUTENAME]);
			$INS_ANOTHER = trim($data[INS_ANOTHER]);
			$CT_CODE = trim($data[COUNTRYCODE]);
			$DEGREE = trim($data[DEGREE]);
			$SC_REMARK = trim($data[SC_REMARK]);
			$SC_REMARK = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($SC_REMARK)));
			$SC_BACKDATE = trim($data[REPORTDATE]);
			$MAJORID = trim($data[MAJORID]);
			$SCHOLARSHIP = trim($data[SCHOLARSHIP]);
			$INST_NAME = trim($data[INST_NAME]);
			$GRADUATED_DATE = trim($data[GRADUATED_DATE]);
			$OTHER_INS = trim($data[OTHER_INS]);
			$MODBY = $data[MODBY];
			if ($MODBY) {
				$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'USER_DETAIL' AND OLD_CODE = '$MODBY' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
				$data_dpis = $db_dpis->get_array();
				$UPDATE_USER = $data_dpis[NEW_CODE] + 0;
				if ($UPDATE_USER==0) echo "$MODBY<br>";
			}
			$MODDATE = trim($data[MODDATE]);
			if ($MODDATE && $MODDATE != "00000000000000") $UPDATE_DATE = substr($MODDATE, 0, 4) . "-" . substr($MODDATE, 4, 2) . "-" . substr($MODDATE, 6, 2);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_PERSONAL' AND OLD_CODE = '$PER_CARDNO' ";
			$db_dpis->send_cmd($cmd);
			$db_dpis->show_error();
			$data_dpis = $db_dpis->get_array();
			$PER_ID = $data_dpis[NEW_CODE] + 0;

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_EDUCNAME' AND OLD_CODE = '$DEGREE' ";
			$db_dpis->send_cmd($cmd);
			$db_dpis->show_error();
			$data_dpis = $db_dpis->get_array();
			$EN_CODE = trim($data_dpis[NEW_CODE]);
			if ($EN_CODE) {
				$cmd = " select EL_CODE from PER_EDUCNAME where EN_CODE = '$EN_CODE' ";
				$db_dpis->send_cmd($cmd);
				$db_dpis->show_error();
				$data_dpis = $db_dpis->get_array();
				$EL_CODE = trim($data_dpis[EL_CODE]);
			}

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_EDUCMAJOR' AND OLD_CODE = '$MAJORID' ";
			$db_dpis->send_cmd($cmd);
			$db_dpis->show_error();
			$data_dpis = $db_dpis->get_array();
			$EM_CODE = trim($data_dpis[NEW_CODE]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_INSTITUTE' AND OLD_CODE = '$INSTUTUTENAME' ";
			$db_dpis->send_cmd($cmd);
			$db_dpis->show_error();
			$data_dpis = $db_dpis->get_array();
			$INS_CODE = trim($data_dpis[NEW_CODE]);
			if (!$INS_CODE) {
				$cmd = " select INS_CODE from PER_INSTITUTE where INS_NAME = '$INS_ANOTHER' ";
				$db_dpis->send_cmd($cmd);
				$db_dpis->show_error();
				$data_dpis = $db_dpis->get_array();
				$INS_CODE = trim($data_dpis[INS_CODE]);
			}

			$SCH_CODE = "1";
			if (!$EL_CODE) $EL_CODE = "99";
			$EN_CODE = (trim($EN_CODE) && $EN_CODE != "0")? "'" . $EN_CODE . "'"  : "NULL";
			$EM_CODE = (trim($EM_CODE) && $EM_CODE != "0")? "'" . $EM_CODE . "'"  : "NULL";
			$INS_CODE = (trim($INS_CODE) && $INS_CODE != "0")? "'" . $INS_CODE . "'"  : "NULL";	

			$cmd = " INSERT INTO PER_SCHOLAR(SC_ID, SC_TYPE, PER_ID, EN_CODE, EM_CODE, SCH_CODE, INS_CODE, EL_CODE, SC_STARTDATE, 
							  SC_ENDDATE, SC_FINISHDATE, SC_BACKDATE, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID, SC_REMARK,
							  SC_DOCNO, SC_DOCDATE, SC_INSTITUTE, CT_CODE, SC_FUND)
							  VALUES ($MAX_ID, 1, $PER_ID, $EN_CODE, $EM_CODE, '$SCH_CODE', $INS_CODE, '$EL_CODE', '$SC_STARTDATE', 
							  '$SC_ENDDATE', '$SC_FINISHDATE', '$SC_BACKDATE', $UPDATE_USER, '$UPDATE_DATE', $DEPARTMENT_ID, '$SC_REMARK',
							  '$SC_DOCNO', '$SC_DOCDATE', '$INS_ANOTHER', '$CT_CODE', '$SC_FUND') ";
			$db_dpis->send_cmd($cmd);

			$cmd1 = " SELECT SC_ID FROM PER_SCHOLAR WHERE SC_ID = $MAX_ID "; 
			$count_data = $db_dpis1->send_cmd($cmd1);
			if (!$count_data) {
				echo "$cmd<br>==================<br>";
				$db_dpis->show_error();
				echo "<br>end ". ++$i  ."=======================<br>";
			}
			$MAX_ID++; 
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_SCHOLAR ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_SCHOLAR - $PER_SCHOLAR - $COUNT_NEW<br>";

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_att->free_result();
		$db_att1->free_result();
	} // end if

	if( $command=='UPDATESAL' ){
// ปรับปรุงประวัติการรับเงินเดือน *******************************************************
		$cmd = " UPDATE PER_SALARYHIS SET SAH_ENDDATE = NULL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " SELECT SAH_ID, PER_ID, SAH_EFFECTIVEDATE, SAH_ENDDATE, MOV_CODE, SAH_DOCNO, SAH_DOCDATE FROM PER_SALARYHIS 
						  ORDER BY PER_ID, SAH_EFFECTIVEDATE DESC, SAH_SEQ_NO DESC ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		while($data = $db_dpis->get_array()){
			$SAH_ID = $data[SAH_ID];
			$PER_ID = $data[PER_ID];
			$SAH_EFFECTIVEDATE = trim($data[SAH_EFFECTIVEDATE]);
			$SAH_ENDDATE = trim($data[SAH_ENDDATE]);
			$MOV_CODE = trim($data[MOV_CODE]);
			$SAH_DOCNO = trim($data[SAH_DOCNO]);
			$SAH_DOCDATE = trim($data[SAH_DOCDATE]);
/*
			$cmd = " SELECT LEVEL_NO FROM PER_POSITIONHIS WHERE PER_ID = $PER_ID AND POH_EFFECTIVEDATE <= '$SAH_EFFECTIVEDATE'
							  ORDER BY POH_EFFECTIVEDATE DESC ";
			$db_dpis1->send_cmd($cmd);
			$data_dpis1 = $db_dpis1->get_array();
			$LEVEL_NO = trim($data_dpis1[LEVEL_NO]);
*/
			if ($PER_ID != $TEMP_ID) {
				$TEMP_ID = $PER_ID;
/*
				$cmd = " UPDATE PER_SALARYHIS SET LEVEL_NO = '$LEVEL_NO' WHERE SAH_ID = $SAH_ID ";
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
*/
				$cmd = " SELECT PER_DOCDATE FROM PER_PERSONAL WHERE PER_ID = $PER_ID AND PER_STATUS = 1 ";
				$db_dpis1->send_cmd($cmd);
				$data_dpis1 = $db_dpis1->get_array();
				$PER_DOCDATE = trim($data_dpis1[PER_DOCDATE]);

				if ($SAH_DOCDATE > $PER_DOCDATE) {
					$cmd = " UPDATE PER_PERSONAL SET MOV_CODE = '$MOV_CODE', PER_DOCNO = '$SAH_DOCNO', PER_DOCDATE = '$SAH_DOCDATE' 
									WHERE PER_ID = $PER_ID AND PER_STATUS = 1 ";
					$db_dpis1->send_cmd($cmd);
				}
			} elseif (!$SAH_ENDDATE) {
				if ($TEMP_ENDDATE < $SAH_EFFECTIVEDATE)	$TEMP_ENDDATE = $SAH_EFFECTIVEDATE;
				$cmd = " UPDATE PER_SALARYHIS SET SAH_ENDDATE = '$TEMP_ENDDATE' WHERE SAH_ID = $SAH_ID ";
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
			} elseif ($SAH_ENDDATE) {
				if ($TEMP_ENDDATE < $SAH_EFFECTIVEDATE){
					//echo "$TEMP_ENDDATE-$SAH_EFFECTIVEDATE<br>";
					$TEMP_ENDDATE = $SAH_EFFECTIVEDATE;
					$cmd = " UPDATE PER_SALARYHIS SET SAH_ENDDATE = '$TEMP_ENDDATE' WHERE SAH_ID = $SAH_ID ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
				}
			} // end if
			$tmp_date = explode("-", $SAH_EFFECTIVEDATE);
			$TEMP_ENDDATE = (mktime(0, 0, 0, $tmp_date[1], substr($tmp_date[2],0,2),		$tmp_date[0]) - 86400);
			$TEMP_ENDDATE = date("Y-m-d", $TEMP_ENDDATE);
		} // end while						
	} // end if

	if( $command=='UPDATEPOS' ){
// ปรับปรุงประวัติการดำรงตำแหน่ง ***************************************************
		$cmd = " UPDATE PER_POSITIONHIS SET POH_ENDDATE = NULL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " SELECT POH_ID, PER_ID, POH_EFFECTIVEDATE, POH_ENDDATE, MOV_CODE, POH_DOCNO, POH_DOCDATE FROM PER_POSITIONHIS 
						  ORDER BY PER_ID, POH_EFFECTIVEDATE DESC, POH_SEQ_NO desc ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		while($data = $db_dpis->get_array()){
			$POH_ID = $data[POH_ID];
			$PER_ID = $data[PER_ID];
			$POH_EFFECTIVEDATE = trim($data[POH_EFFECTIVEDATE]);
			$POH_ENDDATE = trim($data[POH_ENDDATE]);
			$MOV_CODE = trim($data[MOV_CODE]);
			$POH_DOCNO = trim($data[POH_DOCNO]);
			$POH_DOCDATE = trim($data[POH_DOCDATE]);

			$cmd = " SELECT SAH_SALARY FROM PER_SALARYHIS 
							WHERE PER_ID = $PER_ID AND SAH_EFFECTIVEDATE <= '$POH_EFFECTIVEDATE'
							ORDER BY SAH_EFFECTIVEDATE DESC ";
			$db_dpis1->send_cmd($cmd);
			$data_dpis1 = $db_dpis1->get_array();
			$SAH_SALARY = $data_dpis1[SAH_SALARY];

			if ($SAH_SALARY) {
				$cmd = " UPDATE PER_POSITIONHIS SET POH_SALARY = $SAH_SALARY 	WHERE POH_ID = $POH_ID ";
				$db_dpis1->send_cmd($cmd);
			}

			//echo "$POH_ID - $PER_ID - $POH_EFFECTIVEDATE - $POH_ENDDATE<br>"; 
			if ($PER_ID != $TEMP_ID) {
				$TEMP_ID = $PER_ID;

/*				$cmd = " SELECT PER_DOCDATE FROM PER_PERSONAL WHERE PER_ID = $PER_ID AND PER_STATUS = 1 ";
				$db_dpis1->send_cmd($cmd);
				$data_dpis1 = $db_dpis1->get_array();
				$PER_DOCDATE = trim($data_dpis1[PER_DOCDATE]);

				if ($POH_DOCDATE > $PER_DOCDATE) {
					$cmd = " UPDATE PER_PERSONAL SET MOV_CODE = '$MOV_CODE', PER_DOCNO = '$POH_DOCNO', PER_DOCDATE = '$POH_DOCDATE' 
									WHERE PER_ID = $PER_ID AND PER_STATUS = 1 ";
					$db_dpis1->send_cmd($cmd);
				}  */
			} elseif (!$POH_ENDDATE) {
				if ($TEMP_ENDDATE < $POH_EFFECTIVEDATE)	$TEMP_ENDDATE = $POH_EFFECTIVEDATE;
				$cmd = " UPDATE PER_POSITIONHIS SET POH_ENDDATE = '$TEMP_ENDDATE' WHERE POH_ID = $POH_ID ";
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
			} elseif ($POH_ENDDATE) {
				if ($TEMP_ENDDATE < $POH_EFFECTIVEDATE){
					//echo "$TEMP_ENDDATE-$POH_EFFECTIVEDATE<br>";
					$TEMP_ENDDATE = $POH_EFFECTIVEDATE;
					$cmd = " UPDATE PER_POSITIONHIS SET POH_ENDDATE = '$TEMP_ENDDATE' WHERE POH_ID = $POH_ID ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
				}
			} // end if
			$tmp_date = explode("-", $POH_EFFECTIVEDATE);
			$TEMP_ENDDATE = (mktime(0, 0, 0, $tmp_date[1], substr($tmp_date[2],0,2),		$tmp_date[0]) - 86400);
			$TEMP_ENDDATE = date("Y-m-d", $TEMP_ENDDATE);
		} // end while			
/*		$cmd = " SELECT RECORD_ID, ORG_NAME
						  FROM HR_EMPLOY_RECORD
						  WHERE TRIM(ORG_NAME) > ' '
						  ORDER BY TRIM(ORG_NAME) ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
		while($data = $db_att->get_array()){
			$RECORD_ID = $data[RECORD_ID];
			$ORG_NAME = trim($data[ORG_NAME]);
			$ORG_NAME = str_replace("\n", "  ", $ORG_NAME);
			$ORG_NAME = str_replace("\r", "  ", $ORG_NAME);
			$ORG_NAME = str_replace("\n\r", "  ", $ORG_NAME);
			$ORG_NAME = str_replace("\r\n", "  ", $ORG_NAME);
			$cmd = " UPDATE HR_EMPLOY_RECORD SET  ORG_NAME = '$ORG_NAME' WHERE RECORD_ID = $RECORD_ID ";
			$db_att1->send_cmd($cmd);
//			$db_att1->show_error();
		} // end while			
*/
	} // end if

	if( $command=='GENABSENTSUM' ){
// สร้างสรุปวันลาสะสม *******************************************************
		$cmd = " select max(AS_ID) as max_id from PER_ABSENTSUM ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$AS_ID = $data[max_id] + 1;
					
		$cmd = " SELECT PER_ID, PER_OCCUPYDATE, PER_CARDNO FROM PER_PERSONAL 
						  WHERE DEPARTMENT_ID = 27 AND PER_TYPE = 1 AND PER_STATUS = 1
						  ORDER BY PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		while($data = $db_dpis->get_array()){
			$PER_ID = $data[PER_ID];
			$PER_OCCUPYDATE = trim($data[PER_OCCUPYDATE]);
			$PER_CARDNO = trim($data[PER_CARDNO]);
			if (substr($PER_OCCUPYDATE,5,2) > "09" || substr($PER_OCCUPYDATE,5,2) < "04") $AS_CYCLE = 1;
			elseif (substr($PER_OCCUPYDATE,5,2) > "03" && substr($PER_OCCUPYDATE,5,2) < "10")	$AS_CYCLE = 2;
			$TEMP_YEAR = substr($PER_OCCUPYDATE, 0, 4);
			$UPDATE_YEAR = substr($UPDATE_DATE, 0, 4);
			for ( $AS_YEAR=$TEMP_YEAR; $AS_YEAR<=$UPDATE_YEAR; $AS_YEAR++ ) { 
				$TMP_AS_YEAR = $AS_YEAR;
				if($AS_CYCLE==1){	//ตรวจสอบรอบการลา
					if (substr($PER_OCCUPYDATE,5,2) > "09") $TMP_AS_YEAR += 1;
					$START_DATE = ($TMP_AS_YEAR - 1) . "-10-01";
					$END_DATE = $TMP_AS_YEAR . "-03-31";
				}else if($AS_CYCLE==2){
					$START_DATE = $TMP_AS_YEAR . "-04-01";
					$END_DATE = $TMP_AS_YEAR . "-09-30"; 
				}
				$AS_YEAR_BDH = $TMP_AS_YEAR + 543;

				$cmd="select   AS_YEAR from PER_ABSENTSUM where PER_ID=$PER_ID and AS_YEAR = '$AS_YEAR_BDH' and AS_CYCLE in (1,2) ";
				$count=$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error(); echo "<hr>$cmd<br>";
				if(!$count) { 
					$cmd = " insert into PER_ABSENTSUM (AS_ID, PER_ID, AS_YEAR, AS_CYCLE, START_DATE,  END_DATE, PER_CARDNO, UPDATE_USER, UPDATE_DATE)
									values ($AS_ID, $PER_ID, '$AS_YEAR_BDH', 1, '$START_DATE', '$END_DATE', '$PER_CARDNO', $SESS_USERID, '$UPDATE_DATE') ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
					$AS_ID++;
				}
			}
		} // end while						
	} // end if

	if( $command=='GENKPI' ){
// สร้าง KPI Form *******************************************************
		$cmd = " select max(KF_ID) as max_id from PER_KPI_FORM ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$KF_ID = $data[max_id] + 1;
					
		$cmd = " SELECT PER_ID, PER_OCCUPYDATE, PER_CARDNO, DEPARTMENT_ID, LEVEL_NO FROM PER_PERSONAL WHERE PER_TYPE = 1 AND PER_STATUS = 1 
						  ORDER BY PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		while($data = $db_dpis->get_array()){
			$PER_ID = $data[PER_ID];
			$PER_OCCUPYDATE = trim($data[PER_OCCUPYDATE]);
			$PER_CARDNO = trim($data[PER_CARDNO]);
			$PER_ID_DEPARTMENT_ID = $data[DEPARTMENT_ID];
//			$LEVEL_NO = trim($data[LEVEL_NO]); ระดับตำแหน่งต้องหาจากประวัติ (หน่วยงานด้วย)
			if (substr($PER_OCCUPYDATE,5,2) > "09" || substr($PER_OCCUPYDATE,5,2) < "04") $KF_CYCLE = 1;
			elseif (substr($PER_OCCUPYDATE,5,2) > "03" && substr($PER_OCCUPYDATE,5,2) < "10")	$KF_CYCLE = 2;
			$TEMP_YEAR = substr($PER_OCCUPYDATE, 0, 4);
			if ($TEMP_YEAR < 2001) $TEMP_YEAR = 2001;
			$UPDATE_YEAR = substr($UPDATE_DATE, 0, 4);
			for ( $KF_YEAR=$TEMP_YEAR; $KF_YEAR<$UPDATE_YEAR; $KF_YEAR++ ) { 
				if (substr($PER_OCCUPYDATE,5,2) > "09") $KF_YEAR += 1;
				$KF_START_DATE_1 = ($KF_YEAR - 1) . "-10-01";
				$KF_END_DATE_1 = $KF_YEAR . "-03-31";
				$KF_START_DATE_2 = $KF_YEAR . "-04-01";
				$KF_END_DATE_2 = $KF_YEAR . "-09-30"; 
				$KF_YEAR_BDH = $KF_YEAR + 543;
				if(!$WEIGHT_OTHER) $WEIGHT_OTHER = "NULL";

				$cmd = " select 	KF_END_DATE, KF_CYCLE, PER_ID
								 from 		PER_KPI_FORM 
								 where 	KF_END_DATE = '$KF_END_DATE_1' and  KF_CYCLE=1 and PER_ID = $PER_ID ";
				$count=$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error(); echo "<hr>$cmd<br>";
				if(!$count) { 
					$cmd = " 	insert into PER_KPI_FORM (KF_ID, PER_ID, PER_CARDNO, KF_CYCLE, KF_START_DATE, KF_END_DATE, 
										TOTAL_SCORE, DEPARTMENT_ID, LEVEL_NO, UPDATE_USER, UPDATE_DATE, PERFORMANCE_WEIGHT, COMPETENCE_WEIGHT, OTHER_WEIGHT)
										values ($KF_ID, $PER_ID, '$PER_CARDNO', 1, '$KF_START_DATE_1', '$KF_END_DATE_1', 
										NULL, $PER_ID_DEPARTMENT_ID, '$LEVEL_NO', $SESS_USERID, '$UPDATE_DATE', $WEIGHT_KPI, $WEIGHT_COMPETENCE, $WEIGHT_OTHER)   ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
					$KF_ID++;
				}

				$cmd = " select 	KF_END_DATE, KF_CYCLE, PER_ID
								 from 		PER_KPI_FORM 
								 where 	KF_END_DATE = '$KF_END_DATE_2' and  KF_CYCLE=2 and PER_ID = $PER_ID ";
				$count=$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error(); echo "<hr>$cmd<br>";
				if(!$count) { 
					$cmd = " 	insert into PER_KPI_FORM (KF_ID, PER_ID, PER_CARDNO, KF_CYCLE, KF_START_DATE, KF_END_DATE, 
										TOTAL_SCORE, DEPARTMENT_ID, LEVEL_NO, UPDATE_USER, UPDATE_DATE, PERFORMANCE_WEIGHT, COMPETENCE_WEIGHT, OTHER_WEIGHT)
										values ($KF_ID, $PER_ID, '$PER_CARDNO', 2, '$KF_START_DATE_2', '$KF_END_DATE_2', 
										NULL, $PER_ID_DEPARTMENT_ID, '$LEVEL_NO', $SESS_USERID, '$UPDATE_DATE', $WEIGHT_KPI, $WEIGHT_COMPETENCE, $WEIGHT_OTHER)   ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
					$KF_ID++;
				}
			}
		} // end while						
	} // end if

?>