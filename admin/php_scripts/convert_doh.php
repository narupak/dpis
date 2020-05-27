<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

// เลขที่ตำแหน่งครอง 2 คน select pos_id, count(*) from per_personal where per_type=1 and per_status=1 group by pos_id having count(*) > 1

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis351 = new connect_dpis35($dpis35db_host, $dpis35db_name, $dpis35db_user, $dpis35db_pwd);
	$UPDATE_DATE = date("Y-m-d H:i:s");
	$UPDATE_USER = 99999;

	if ($command=='COUNTRY'){
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

		$cmd = " SELECT C_CODE, C_NAME, CT_CODE
						  FROM COUNTRY
						  ORDER BY C_CODE ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "<br>";
		$SEQ_NO = 1;
		while($data = $db_dpis35->get_array()){
			$PER_COUNTRY++;
			$C_CODE = trim($data[C_CODE]);
			$C_NAME = trim($data[C_NAME]);
			$CT_CODE = trim($data[CT_CODE]);

			$cmd = " INSERT INTO PER_COUNTRY(CT_CODE, CT_NAME, CT_ACTIVE, CT_SEQ_NO, UPDATE_USER, UPDATE_DATE)
							 VALUES ('$C_CODE', '$C_NAME', 1, $SEQ_NO, $UPDATE_USER, '$UPDATE_DATE') ";
//			$db_dpis->send_cmd($cmd);
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
		
		$cmd = " SELECT PROV_CODE, PROV_NAME, TF_PROV
						  FROM PROVINCE
						  ORDER BY PROV_CODE ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "<br>";
		$SEQ_NO = 1;
		while($data = $db_dpis35->get_array()){
			$PER_PROVINCE++;
			$PROV_CODE = trim($data[PROV_CODE]);
			$PROV_NAME = trim($data[PROV_NAME]);
			$TF_PROV = trim($data[TF_PROV]);

			$cmd = " INSERT INTO PER_PROVINCE(PV_CODE, PV_NAME, CT_CODE, PV_ACTIVE, PV_SEQ_NO, UPDATE_USER, UPDATE_DATE)
							 VALUES ('$PROV_CODE', '$PROV_NAME', '140', 1, $SEQ_NO, $UPDATE_USER, '$UPDATE_DATE') ";
//			$db_dpis->send_cmd($cmd);
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
		
		$cmd = " SELECT DIST_CODE, PROV_CODE, DIST_NAME, ZIP_CODE, SUBPROV, AP_CODE
						  FROM DISTRICT
						  ORDER BY DIST_CODE ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "<br>";
		$SEQ_NO = 1;
		while($data = $db_dpis35->get_array()){
			$PER_AMPHUR++;
			$DIST_CODE = trim($data[DIST_CODE]);
			$PROV_CODE = trim($data[PROV_CODE]);
			$DIST_NAME = trim($data[DIST_NAME]);
			$ZIP_CODE = trim($data[ZIP_CODE]);
			$SUBPROV = trim($data[SUBPROV]);
			$AP_CODE = trim($data[AP_CODE]);

			$cmd = " INSERT INTO PER_AMPHUR(AP_CODE, AP_NAME, PV_CODE, AP_ACTIVE, AP_SEQ_NO, UPDATE_USER, UPDATE_DATE)
							 VALUES ('$DIST_CODE', '$DIST_NAME', '$PROV_CODE', 1, $SEQ_NO, $UPDATE_USER, '$UPDATE_DATE') ";
//			$db_dpis->send_cmd($cmd);
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
		
		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();
		$db_dpis351->free_result();
	} // end if

	if ($command=='MGT'){
/* // ตำแหน่งในการบริหารงาน 77
		$cmd = " DELETE FROM PER_MGT ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " SELECT ADMIN_CODE, ADMIN_NAME, ADMIN_ABB_NAME, 
						  USER_CREATE, to_char(CREATE_DATE,'yyyy-mm-dd') as CREATE_DATE, 
						  USER_UPDATE, to_char(UPDATE_DATE,'yyyy-mm-dd') as UPDATE_DATE 
						  FROM HR_ADMIN_CODE
						  ORDER BY ADMIN_CODE ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "<br>";
		$SEQ_NO = 1;
		while($data = $db_dpis35->get_array()){
			$PER_MGT++;
			$ADMIN_CODE = trim($data[ADMIN_CODE]);
			$ADMIN_NAME = trim($data[ADMIN_NAME]);
			$ADMIN_ABB_NAME = trim($data[ADMIN_ABB_NAME]);

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
		
// หมวดตำแหน่งลูกจ้างประจำ/พนักงานราชการ 8
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
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "<br>";
		$SEQ_NO = 1;
		while($data = $db_dpis35->get_array()){
			$PER_POS_GROUP++;
			$CATEGORY_SAL_CODE = trim($data[CATEGORY_SAL_CODE]);
			$CATEGORY_SAL_NAME = trim($data[CATEGORY_SAL_NAME]);
			$FLAG_USE = trim($data[FLAG_USE]);
			$CLUSTER_CODE = trim($data[CLUSTER_CODE]);

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
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "<br>";
		$SEQ_NO = 1;
		while($data = $db_dpis35->get_array()){
			$PER_EDUCNAME++;
			$EDUCATION_CODE = trim($data[EDUCATION_CODE]);
			$EDUCATION_NAME = trim($data[EDUCATION_NAME]);
			$EDUCATION_NAME_E = trim($data[EDUCATION_NAME_E]);
			$EDUCATION_ABB_NAME = trim($data[EDUCATION_ABB_NAME]);

			$cmd = " SELECT FUND_COURSE_CODE FROM HR_EDUCATION WHERE EDUCATION_CODE = '$EDUCATION_CODE' AND FUND_COURSE_CODE IS NOT NULL ";
			$db_dpis351->send_cmd($cmd);
			$data1 = $db_dpis351->get_array();
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
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "<br>";
		$SEQ_NO = 1;
		while($data = $db_dpis35->get_array()){
			$PER_OFF_TYPE++;
			$FLAG_PERSON_TYPE = trim($data[FLAG_PERSON_TYPE]);
			$FLAG_PERSON_NAME = trim($data[FLAG_PERSON_NAME]);

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
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "<br>";
		$SEQ_NO = 1;
		while($data = $db_dpis35->get_array()){
			$PER_EDUCMAJOR++;
			$MAJOR_CODE = trim($data[MAJOR_CODE]);
			$MAJOR_NAME = trim($data[MAJOR_NAME]);
			$MAJOR_ABB_NAME = trim($data[MAJOR_ABB_NAME]);

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
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "<br>";
		$SEQ_NO = 1;
		while($data = $db_dpis35->get_array()){
			$PER_LINE++;
			$WORK_LINE_CODE = trim($data[WORK_LINE_CODE]);
			$WORK_LINE_NAME = trim($data[WORK_LINE_NAME]);
			$WORK_LINE_ABB_NAME = trim($data[WORK_LINE_ABB_NAME]);
			$WORK_LINE_OFFICE = trim($data[WORK_LINE_OFFICE]);
			$LINE_CLASS_BEGIN = trim($data[LINE_CLASS_BEGIN]);
			$LINE_CLASS_END = trim($data[LINE_CLASS_END]);
			if (!$WORK_LINE_NAME) $WORK_LINE_NAME = "-";

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
		
// ตำแหน่งลูกจ้างประจำ/พนักงานราชการ 587
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
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "<br>";
		$SEQ_NO = 1;
		while($data = $db_dpis35->get_array()){
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
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "<br>";
		$SEQ_NO = 1;
		while($data = $db_dpis35->get_array()){
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
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "<br>";
		$SEQ_NO = 1;
		while($data = $db_dpis35->get_array()){
			$PER_PRENAME++;
			$RANK_CODE = trim($data[RANK_CODE]);
			$RANK_NAME = trim($data[RANK_NAME]);
			$RANK_NAME_E = trim($data[RANK_NAME_E]);
			$RANK_ABB_NAME = trim($data[RANK_ABB_NAME]);

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
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "<br>";
		$SEQ_NO = 1;
		while($data = $db_dpis35->get_array()){
			$PER_MOVMENT++;
			$FLAG_TO_NAME_CODE = trim($data[FLAG_TO_NAME_CODE]);
			$FLAG_TO_NAME = trim($data[FLAG_TO_NAME]);
			$FLAG_CUR_ST = trim($data[FLAG_CUR_ST]);

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
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "<br>";
		while($data = $db_dpis35->get_array()){
			$PER_MOVMENT++;
			$FLAG_TO_NAME_CODE = "20" . trim($data[FLAG_TO_NAME_CODE]);
			$FLAG_TO_NAME = trim($data[FLAG_TO_NAME]);
			$FLAG_CUR_ST = trim($data[FLAG_CUR_ST]);

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
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "<br>";
		$SEQ_NO = 1;
		while($data = $db_dpis35->get_array()){
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
//		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "<br>";
//		$SEQ_NO = 1;
//		while($data = $db_dpis35->get_array()){
//			$PER_TYPE++;
//			$FLAG_POSITION = trim($data[FLAG_POSITION]);
//			$FLAG_POSITION_NAME = trim($data[FLAG_POSITION_NAME]);
//			$FLAG_POSITION_ABB = trim($data[FLAG_POSITION_ABB]);

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
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "<br>";
		$SEQ_NO = 1;
		while($data = $db_dpis35->get_array()){
			$PER_ABSENTTYPE++;
			$LEAVE_CODE = trim($data[LEAVE_CODE]);
			$LEAVE_NAME = trim($data[LEAVE_NAME]);
			$REC_STS = trim($data[REC_STS]);
			$LEAVE_FLAG = trim($data[LEAVE_FLAG]);

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
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "<br>";
		$SEQ_NO = 1;
		while($data = $db_dpis35->get_array()){
			$PER_PENALTY++;
			$PUN_LGOV_CODE = trim($data[PUN_LGOV_CODE]);
			$PUN_LGOV_NAME = trim($data[PUN_LGOV_NAME]);
			$PUN_PERCENT = trim($data[PUN_PERCENT]);
			$MONTH_AMOUNT = trim($data[MONTH_AMOUNT]);
			$LEVEL_AMOUNT = trim($data[LEVEL_AMOUNT]);

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
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "<br>";
		$SEQ_NO = 1;
		while($data = $db_dpis35->get_array()){
			$PER_REWARD++;
			$GOOD_WORK_CODE = trim($data[GOOD_WORK_CODE]);
			$GOOD_WORK_NAME = trim($data[GOOD_WORK_NAME]);

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
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "<br>";
		while($data = $db_dpis35->get_array()){
			$PER_EDUCLEVEL++;
			$FUND_COURSE_CODE = trim($data[FUND_COURSE_CODE]);
			$FUND_COURSE_NAME = trim($data[FUND_COURSE_NAME]);
			$LEVEL_SEQ = trim($data[LEVEL_SEQ]);

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
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "<br>";
		$SEQ_NO = 1;
		while($data = $db_dpis35->get_array()){
			$PER_INSTITUTE++;
			$UNIVER_CODE = trim($data[UNIVER_CODE]);
			$UNIVER_NAME = trim($data[UNIVER_NAME]);
			$UNIVER_NAME_E = trim($data[UNIVER_NAME_E]);
			$UNIVER_ABB_NAME = trim($data[UNIVER_ABB_NAME]);

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
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "<br>";
		$SEQ_NO = 1;
		while($data = $db_dpis35->get_array()){
			$PER_LAYEREMP++;
			$CLUSTER_CODE = trim($data[CLUSTER_CODE]);
			$SALARY_LEVEL_CODE = trim($data[SALARY_LEVEL_CODE]);
			$GP_SAL_GOV_YEAR = trim($data[GP_SAL_GOV_YEAR]);
			$FLAG_STATUS = trim($data[FLAG_STATUS]);
			$SALARY = trim($data[SALARY]);
			$SALARY_D = trim($data[SALARY_D]);
			$SALARY_H = trim($data[SALARY_H]);
			$EFFECT_DATE = trim($data[EFFECT_DATE]);

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
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "<br>";
		$SEQ_NO = 1;
		while($data = $db_dpis35->get_array()){
			$PER_SKILL++;
			$SPECIALIST_CODE = trim($data[SPECIALIST_CODE]);
			$SPECIALIST_NAME = trim($data[SPECIALIST_NAME]);
			$SG_CODE = "990";

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
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "<br>";
		$SEQ_NO = 1;
		while($data = $db_dpis35->get_array()){
			$PER_SALARY_MOVMENT++;
			$MP_FLAG = trim($data[MP_FLAG]);
			$MP_FLAG_NAME = trim($data[MP_FLAG_NAME]);
			$MP_FLAG_VALUE = $data[MP_FLAG_VALUE];
			if (!$MP_FLAG_VALUE) $MP_FLAG_VALUE = "NULL";

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
		$db_dpis35->free_result();
		$db_dpis351->free_result();
	} // end if

	if($command=='ORG' || $command=='ORG_EMP'){
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
			"per_comdtl", "per_bonuspromote", "per_bonusquotadtl2", "per_bonusquotadtl1", "per_bonusquota", "per_special_skill", "per_coursedtl", "per_course", "per_decordtl", "per_org_job", 
			"per_promote_p", "per_letter", "per_move_req", "per_scholar", "per_absent", "per_invest1dtl", "per_promote_c", "per_address", "per_kpi", "per_kpi_form", 
			"per_salpromote", "per_family", "per_licensehis", "per_personal", "per_pos_move", "per_salquotadtl1", "per_salquotadtl2", "per_salquota", "per_command", "per_decor", 
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

		$cmd = " DELETE FROM PER_ORG WHERE DEPARTMENT_ID = $SESS_DEPARTMENT_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();
		$db_dpis351->free_result();

		$cmd = " select max(ORG_ID) as MAX_ID from PER_ORG ";
		$db_dpis1->send_cmd($cmd);
		$data1 = $db_dpis1->get_array();
		$MAX_ID = $data1[MAX_ID] + 1;

		if ($command=='ORG') $where = "G";
		elseif ($command=='ORG_EMP') $where = "E";
		$cmd = " SELECT a.DEPT_CODE, DEPT_NAME, DEPT_SUBOF, DEPT_ORDER, DEPT_WHEREBUDGET, DEPT_OLDNEWFLAG, 
						DEPT_AREA, DEPT_INOUTFLAG, CHANGE_FROM, DEPT_SHORTNAME, DATE_UPDATE_DEPT, DATE_DELETE_DEPT, 
						DEPT_CHECK, TYPE_FLAG, DEPT_LINE, SORT_DEPT_LINE, FLAG_CONTROL, c.TF_PROV, a.PROV_CODE, DEPT_ENAME
						FROM DOH_DIVISION a, DOH_EDIVISION b, PROVINCE c
						WHERE a.DEPT_CODE=b.DEPT_CODE(+) AND a.PROV_CODE=c.PROV_CODE(+) AND a.DEPT_CODE LIKE '$where%' AND a.DEPT_INOUTFLAG in (0,1,2)
						ORDER BY DEPT_ORDER ";
//						, DEPT_SUBOF_KP
//						WHERE a.DEPT_CODE=b.DEPT_CODE(+) AND a.DEPT_CODE IN (SELECT DISTINCT DEPT_CODE FROM GOVPOSITION)
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		//echo "<br>";
		while($data = $db_dpis35->get_array()){
			$DEPT_CODE = trim($data[DEPT_CODE]);
			$DEPT_NAME = trim($data[DEPT_NAME]);
			$DEPT_SUBOF = trim($data[DEPT_SUBOF]);
			$DEPT_ORDER = trim($data[DEPT_ORDER]);
			$DEPT_WHEREBUDGET = trim($data[DEPT_WHEREBUDGET]);
			$DEPT_OLDNEWFLAG = trim($data[DEPT_OLDNEWFLAG]);
			$DEPT_AREA = trim($data[DEPT_AREA]);
			$DEPT_INOUTFLAG = trim($data[DEPT_INOUTFLAG]);
			$CHANGE_FROM = trim($data[CHANGE_FROM]);
			$DEPT_SHORTNAME = trim($data[DEPT_SHORTNAME]);
			$DATE_UPDATE_DEPT = trim($data[DATE_UPDATE_DEPT]);
			$DATE_DELETE_DEPT = trim($data[DATE_DELETE_DEPT]);
			if ($DATE_DELETE_DEPT) $DATE_DELETE_DEPT = save_date($DATE_DELETE_DEPT);
			$DEPT_CHECK = trim($data[DEPT_CHECK]);
			$TYPE_FLAG = trim($data[TYPE_FLAG]);
			$DEPT_LINE = trim($data[DEPT_LINE]);
			$SORT_DEPT_LINE = trim($data[SORT_DEPT_LINE]);
			$FLAG_CONTROL = trim($data[FLAG_CONTROL]);
			$TF_PROV = trim($data[TF_PROV]);
			$PROV_CODE = trim($data[PROV_CODE]);
			$DEPT_SUBOF_KP = trim($data[DEPT_SUBOF_KP]);
			$DEPT_ENAME = trim($data[DEPT_ENAME]);

			$OL_CODE = "03"; 
/*			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = '$DEPT_SUBOF' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$ORG_ID_REF = $data2[NEW_CODE];
			if ($ORG_ID_REF) $OL_CODE = "04";
			else $ORG_ID_REF = $SESS_DEPARTMENT_ID; */
			$ORG_ID_REF = $SESS_DEPARTMENT_ID;

			$OT_CODE = "01"; 
			$AP_CODE = "NULL";
			if (!$TF_PROV || $TF_PROV=="0") $TF_PROV = "1000";
			$CT_CODE = "140";
			if ($DEPT_OLDNEWFLAG==1) $ORG_ACTIVE = 1;
			else $ORG_ACTIVE = 0;
			if (!$DEPT_ORDER) $DEPT_ORDER = "NULL";
			if (!$DEPT_NAME) $DEPT_NAME = "กรมทางหลวง";

			$cmd = " INSERT INTO PER_ORG (ORG_ID, ORG_CODE, ORG_NAME, ORG_SHORT, OL_CODE, OT_CODE,  
							AP_CODE, PV_CODE, CT_CODE, ORG_ID_REF, ORG_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							DEPARTMENT_ID, ORG_ENG_NAME, ORG_SEQ_NO, ORG_ADDR2, ORG_ADDR3, ORG_DATE, ORG_DOPA_CODE)
							VALUES ($MAX_ID, '$DEPT_CODE', '$DEPT_NAME', '$DEPT_SHORTNAME', '$OL_CODE', '$OT_CODE',  
							$AP_CODE, '$TF_PROV', '$CT_CODE', $ORG_ID_REF, $ORG_ACTIVE, $UPDATE_USER, '$UPDATE_DATE', 
							$SESS_DEPARTMENT_ID, '$DEPT_ENAME', $DEPT_ORDER, '$DEPT_AREA', '$DEPT_WHEREBUDGET', '$DATE_DELETE_DEPT', '$CHANGE_FROM') ";
			$db_dpis->send_cmd($cmd);

			$cmd1 = " SELECT ORG_ID FROM PER_ORG WHERE ORG_ID = $MAX_ID "; 
			$count_data = $db_dpis1->send_cmd($cmd1);
			if (!$count_data) {
				echo "$cmd<br>==================<br>";
				$db_dpis->show_error();
				echo "<br>end ". ++$i  ."=======================<br>";
			}

			$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
							VALUES ('PER_ORG', '$DEPT_CODE', '$MAX_ID', $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$MAX_ID++;
			$PER_ORG2++;

		} // end while						

		if ($command=='ORG') $where = "GOVPOSITION";
		elseif ($command=='ORG_EMP') $where = "EMPPOSITION_EACHPOSITION";
		if ($command=='ORG') $where = "G";
		elseif ($command=='ORG_EMP') $where = "E";
		$cmd = " SELECT DEPT_CODE, a.SECTION_CODE, SECTION_NAME, SECTION_SUBOF, SECTION_ORDER, 
						SECTION_OLDNEWFLAG, CHANGE_FROM, SECTION_INOUTFLAG, DATE_UPDATE_SECTION, DATE_DELETE_SECTION 
						FROM SECTION_EACHDIVISION a, SECTION b 
						WHERE a.SECTION_CODE=b.SECTION_CODE and a.SECTION_CODE <> '99999999' AND DEPT_CODE LIKE '$where%'  
						ORDER BY DEPT_CODE, ORDER_UNDER_DEPT, SECTION_ORDER ";
//						WHERE a.SECTION_CODE=b.SECTION_CODE and a.SECTION_CODE <> '99999999' and a.SECTION_CODE IN (SELECT DISTINCT SECTION_CODE FROM $where) 
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		//echo "$cmd<br>";
		while($data = $db_dpis35->get_array()){
			$DEPT_CODE = trim($data[DEPT_CODE]);
			$SECTION_CODE = trim($data[SECTION_CODE]);
			$SECTION_NAME = trim($data[SECTION_NAME]);
			$SECTION_SUBOF = trim($data[SECTION_SUBOF]);
			$SECTION_ORDER = trim($data[SECTION_ORDER]);
			$SECTION_OLDNEWFLAG = trim($data[SECTION_OLDNEWFLAG]);
			$CHANGE_FROM = trim($data[CHANGE_FROM]);
			$SECTION_INOUTFLAG = trim($data[SECTION_INOUTFLAG]);
			$DATE_UPDATE_SECTION = trim($data[DATE_UPDATE_SECTION]);
			$DATE_DELETE_SECTION = trim($data[DATE_DELETE_SECTION]);
			if (!$SECTION_NAME) $SECTION_NAME = "กรมทางหลวง";

			$OL_CODE = "04"; 
			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = '$DEPT_CODE$SECTION_SUBOF' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$ORG_ID_REF = $data2[NEW_CODE];
			if ($ORG_ID_REF) $OL_CODE = "05";
			else {
				$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = '$DEPT_CODE' ";
				$db_dpis2->send_cmd($cmd);
	//			$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$ORG_ID_REF = $data2[NEW_CODE];
				if (!$ORG_ID_REF) echo "PER_ORG2 - $SECTION_CODE - $DEPT_CODE - $SECTION_SUBOF<br>";
			}

			$cmd = " select PV_CODE from PER_ORG where ORG_ID = $ORG_ID_REF ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PV_CODE = trim($data2[PV_CODE]);

			$OT_CODE = "01"; 
			$AP_CODE = "NULL";
			$CT_CODE = "140";
			if ($SECTION_OLDNEWFLAG==1) $ORG_ACTIVE = 1;
			else $ORG_ACTIVE = 0;
			if (!$SECTION_ORDER) $SECTION_ORDER = "NULL";

			$cmd = " INSERT INTO PER_ORG (ORG_ID, ORG_CODE, ORG_NAME, ORG_SHORT, OL_CODE, OT_CODE,  
							AP_CODE, PV_CODE, CT_CODE, ORG_ID_REF, ORG_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							DEPARTMENT_ID, ORG_ENG_NAME, ORG_SEQ_NO)
							VALUES ($MAX_ID, '$SECTION_CODE', '$SECTION_NAME', NULL, '$OL_CODE', '$OT_CODE',  
							$AP_CODE, '$PV_CODE', '$CT_CODE', $ORG_ID_REF, $ORG_ACTIVE, $UPDATE_USER, '$UPDATE_DATE', 
							$SESS_DEPARTMENT_ID, '$SECTION_NAME_E', $SECTION_ORDER) ";
			$db_dpis->send_cmd($cmd);

			$cmd1 = " SELECT ORG_ID FROM PER_ORG WHERE ORG_ID = $MAX_ID "; 
			$count_data = $db_dpis1->send_cmd($cmd1);
			if (!$count_data) {
				echo "$cmd<br>==================<br>";
				$db_dpis->show_error();
				echo "<br>end ". ++$i  ."=======================<br>";
			}

			$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
							VALUES ('PER_ORG', '$DEPT_CODE$SECTION_CODE', '$MAX_ID', $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$MAX_ID++;
			$PER_ORG2++;

		} // end while						

		$cmd = " select count(ORG_ID) as COUNT_NEW from PER_ORG where DEPARTMENT_ID = $SESS_DEPARTMENT_ID and OL_CODE in ('03', '04', '05') ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_ORG - $PER_ORG2 - $COUNT_NEW<br>";

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
		$db_dpis35->free_result();
		$db_dpis351->free_result();
	} // end if
	
	if($command=='ORG_ASS' || $command=='ORG_ASS_EMP'){
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_ORG_ASS' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_ORG_ASS WHERE DEPARTMENT_ID = $SESS_DEPARTMENT_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " select max(ORG_ID) as MAX_ID from PER_ORG_ASS ";
		$db_dpis1->send_cmd($cmd);
		$data1 = $db_dpis1->get_array();
		$MAX_ID = $data1[MAX_ID] + 1;

		if ($command=='ORG_ASS') $where = "G";
		elseif ($command=='ORG_ASS_EMP') $where = "E";
		$cmd = " SELECT a.DEPT_CODE, DEPT_NAME, DEPT_SUBOF, DEPT_ORDER, DEPT_WHEREBUDGET, DEPT_OLDNEWFLAG, 
						DEPT_AREA, DEPT_INOUTFLAG, CHANGE_FROM, DEPT_SHORTNAME, DATE_UPDATE_DEPT, DATE_DELETE_DEPT, 
						DEPT_CHECK, TYPE_FLAG, DEPT_LINE, SORT_DEPT_LINE, FLAG_CONTROL, c.TF_PROV, a.PROV_CODE, DEPT_ENAME
						FROM DOH_DIVISION a, DOH_EDIVISION b, PROVINCE c
						WHERE a.DEPT_CODE=b.DEPT_CODE(+) AND a.PROV_CODE=c.PROV_CODE(+) AND a.DEPT_CODE LIKE '$where%' AND a.DEPT_INOUTFLAG in (0,1,2)
						ORDER BY DEPT_ORDER ";
//						, DEPT_SUBOF_KP
//						WHERE a.DEPT_CODE=b.DEPT_CODE(+) AND a.DEPT_CODE IN (SELECT DISTINCT DEPT_CODE FROM GOVPOSITION)
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		//echo "$cmd<br>";
		while($data = $db_dpis35->get_array()){
			$DEPT_CODE = trim($data[DEPT_CODE]);
			$DEPT_NAME = trim($data[DEPT_NAME]);
			$DEPT_SUBOF = trim($data[DEPT_SUBOF]);
			$DEPT_ORDER = trim($data[DEPT_ORDER]);
			$DEPT_WHEREBUDGET = trim($data[DEPT_WHEREBUDGET]);
			$DEPT_OLDNEWFLAG = trim($data[DEPT_OLDNEWFLAG]);
			$DEPT_AREA = trim($data[DEPT_AREA]);
			$DEPT_INOUTFLAG = trim($data[DEPT_INOUTFLAG]);
			$CHANGE_FROM = trim($data[CHANGE_FROM]);
			$DEPT_SHORTNAME = trim($data[DEPT_SHORTNAME]);
			$DATE_UPDATE_DEPT = trim($data[DATE_UPDATE_DEPT]);
			$DATE_DELETE_DEPT = trim($data[DATE_DELETE_DEPT]);
			if ($DATE_DELETE_DEPT) $DATE_DELETE_DEPT = save_date($DATE_DELETE_DEPT);
			$DEPT_CHECK = trim($data[DEPT_CHECK]);
			$TYPE_FLAG = trim($data[TYPE_FLAG]);
			$DEPT_LINE = trim($data[DEPT_LINE]);
			$SORT_DEPT_LINE = trim($data[SORT_DEPT_LINE]);
			$FLAG_CONTROL = trim($data[FLAG_CONTROL]);
			$TF_PROV = trim($data[TF_PROV]);
			$PROV_CODE = trim($data[PROV_CODE]);
			$DEPT_SUBOF_KP = trim($data[DEPT_SUBOF_KP]);
			$DEPT_ENAME = trim($data[DEPT_ENAME]);

			$OL_CODE = "03"; 
			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG_ASS' AND OLD_CODE = '$DEPT_SUBOF' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$ORG_ID_REF = $data2[NEW_CODE];
			if ($ORG_ID_REF) $OL_CODE = "04";
			else $ORG_ID_REF = $SESS_DEPARTMENT_ID; 

			$OT_CODE = "01"; 
			$AP_CODE = "NULL";
			if (!$TF_PROV || $TF_PROV=="0") $TF_PROV = "1000";
			$CT_CODE = "140";
			if ($DEPT_OLDNEWFLAG==1) $ORG_ACTIVE = 1;
			else $ORG_ACTIVE = 0;
			if (!$DEPT_ORDER) $DEPT_ORDER = "NULL";
			if (!$DEPT_NAME) $DEPT_NAME = "กรมทางหลวง";

			$cmd = " INSERT INTO PER_ORG_ASS (ORG_ID, ORG_CODE, ORG_NAME, ORG_SHORT, OL_CODE, OT_CODE,  
							AP_CODE, PV_CODE, CT_CODE, ORG_ID_REF, ORG_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							DEPARTMENT_ID, ORG_ENG_NAME, ORG_SEQ_NO, ORG_ADDR2, ORG_ADDR3, ORG_DATE, ORG_DOPA_CODE)
							VALUES ($MAX_ID, '$DEPT_CODE', '$DEPT_NAME', '$DEPT_SHORTNAME', '$OL_CODE', '$OT_CODE',  
							$AP_CODE, '$TF_PROV', '$CT_CODE', $ORG_ID_REF, $ORG_ACTIVE, $UPDATE_USER, '$UPDATE_DATE', 
							$SESS_DEPARTMENT_ID, '$DEPT_ENAME', $DEPT_ORDER, '$DEPT_AREA', '$DEPT_WHEREBUDGET', '$DATE_DELETE_DEPT', '$CHANGE_FROM') ";
			$db_dpis->send_cmd($cmd);

			$cmd1 = " SELECT ORG_ID FROM PER_ORG_ASS WHERE ORG_ID = $MAX_ID "; 
			$count_data = $db_dpis1->send_cmd($cmd1);
			if (!$count_data) {
				echo "$cmd<br>==================<br>";
				$db_dpis->show_error();
				echo "<br>end ". ++$i  ."=======================<br>";
			}

			$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
							VALUES ('PER_ORG_ASS', '$DEPT_CODE', '$MAX_ID', $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$MAX_ID++;
			$PER_ORG2++;

		} // end while						

		if ($command=='ORG_ASS') $where = "GOVPOSITION";
		elseif ($command=='ORG_ASS_EMP') $where = "EMPPOSITION_EACHPOSITION";
		if ($command=='ORG_ASS') $where = "G";
		elseif ($command=='ORG_ASS_EMP') $where = "E";
		$cmd = " SELECT DEPT_CODE, a.SECTION_CODE, SECTION_NAME, SECTION_SUBOF, SECTION_ORDER, 
						SECTION_OLDNEWFLAG, CHANGE_FROM, SECTION_INOUTFLAG, DATE_UPDATE_SECTION, DATE_DELETE_SECTION 
						FROM SECTION_EACHDIVISION a, SECTION b 
						WHERE a.SECTION_CODE=b.SECTION_CODE and a.SECTION_CODE <> '99999999' AND DEPT_CODE LIKE '$where%'  
						ORDER BY DEPT_CODE, ORDER_UNDER_DEPT, SECTION_ORDER ";
//						WHERE a.SECTION_CODE=b.SECTION_CODE and a.SECTION_CODE <> '99999999' and a.SECTION_CODE IN (SELECT DISTINCT SECTION_CODE FROM $where) 
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		//echo "$cmd<br>";
		while($data = $db_dpis35->get_array()){
			$DEPT_CODE = trim($data[DEPT_CODE]);
			$SECTION_CODE = trim($data[SECTION_CODE]);
			$SECTION_NAME = trim($data[SECTION_NAME]);
			$SECTION_SUBOF = trim($data[SECTION_SUBOF]);
			$SECTION_ORDER = trim($data[SECTION_ORDER]);
			$SECTION_OLDNEWFLAG = trim($data[SECTION_OLDNEWFLAG]);
			$CHANGE_FROM = trim($data[CHANGE_FROM]);
			$SECTION_INOUTFLAG = trim($data[SECTION_INOUTFLAG]);
			$DATE_UPDATE_SECTION = trim($data[DATE_UPDATE_SECTION]);
			$DATE_DELETE_SECTION = trim($data[DATE_DELETE_SECTION]);
			if (!$SECTION_NAME) $SECTION_NAME = "กรมทางหลวง";

			$OL_CODE = "04"; 
			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG_ASS' AND OLD_CODE = '$DEPT_CODE$SECTION_SUBOF' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$ORG_ID_REF = $data2[NEW_CODE];

			if (!$ORG_ID_REF) {
				$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG_ASS' AND OLD_CODE = '$DEPT_CODE' ";
				$db_dpis2->send_cmd($cmd);
	//			$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$ORG_ID_REF = $data2[NEW_CODE];
				if (!$ORG_ID_REF) echo "PER_ORG2 - $SECTION_CODE - $DEPT_CODE - $SECTION_SUBOF<br>";
			}

			$cmd = " select OL_CODE, PV_CODE from PER_ORG_ASS where ORG_ID = $ORG_ID_REF ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$OL_CODE_REF = trim($data2[OL_CODE]);
			if ($OL_CODE_REF=="04") $OL_CODE = "05";
			elseif ($OL_CODE_REF=="05") $OL_CODE = "06";
			$PV_CODE = trim($data2[PV_CODE]);

			$OT_CODE = "01"; 
			$AP_CODE = "NULL";
			$CT_CODE = "140";
			if ($SECTION_OLDNEWFLAG==1) $ORG_ACTIVE = 1;
			else $ORG_ACTIVE = 0;
			if (!$SECTION_ORDER) $SECTION_ORDER = "NULL";

			$cmd = " INSERT INTO PER_ORG_ASS (ORG_ID, ORG_CODE, ORG_NAME, ORG_SHORT, OL_CODE, OT_CODE,  
							AP_CODE, PV_CODE, CT_CODE, ORG_ID_REF, ORG_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							DEPARTMENT_ID, ORG_ENG_NAME, ORG_SEQ_NO)
							VALUES ($MAX_ID, '$SECTION_CODE', '$SECTION_NAME', NULL, '$OL_CODE', '$OT_CODE',  
							$AP_CODE, '$PV_CODE', '$CT_CODE', $ORG_ID_REF, $ORG_ACTIVE, $UPDATE_USER, '$UPDATE_DATE', 
							$SESS_DEPARTMENT_ID, '$SECTION_NAME_E', $SECTION_ORDER) ";
			$db_dpis->send_cmd($cmd);

			$cmd1 = " SELECT ORG_ID FROM PER_ORG_ASS WHERE ORG_ID = $MAX_ID "; 
			$count_data = $db_dpis1->send_cmd($cmd1);
			if (!$count_data) {
				echo "$cmd<br>==================<br>";
				$db_dpis->show_error();
				echo "<br>end ". ++$i  ."=======================<br>";
			}

			$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
							VALUES ('PER_ORG_ASS', '$DEPT_CODE$SECTION_CODE', '$MAX_ID', $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$MAX_ID++;
			$PER_ORG2++;

		} // end while						

		$cmd = " select count(ORG_ID) as COUNT_NEW from PER_ORG_ASS where DEPARTMENT_ID = $SESS_DEPARTMENT_ID and OL_CODE in ('03', '04', '05', '06') ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_ORG_ASS - $PER_ORG2 - $COUNT_NEW<br>";

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();
		$db_dpis351->free_result();
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
			"per_salpromote", "per_family", "per_licensehis", "per_personal", "per_pos_move", "per_salquotadtl1", "per_salquotadtl2", "per_salquota", "per_command", "per_decor", 
			"per_order_dtl", "per_req3_dtl" );
		for ( $i=0; $i<count($table); $i++ ) { 
			if ($table[$i]=="per_bonusquota" || $table[$i]=="per_scholar" || $table[$i]=="per_salquota" || $table[$i]=="per_command" || $table[$i]=="per_decor")
				$cmd = " delete from $table[$i] ";
			else
				$cmd = " truncate table $table[$i] ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end for

// ตำแหน่งข้าราชการ 7161

		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_POSITION' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_POSITION ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();
		$db_dpis351->free_result();

		$POS_ID = 1;

		$cmd = " SELECT GOV_POSNO, DEPT_CODE, SECTION_CODE, ID, GOVPOS_SALARY, DEPT_PRIORITY, GOVPOS_LAGMONEY, 
						WPOS_CODE, GOVPOS_POSMONEY, MPOS_CODE, GOVPOS_LC, GOVPOS_LASTMONEY, GOVPOS_UPMONEY, 
						GOVPOS_INCMONEY, GOVPOS_DECMONEY, GOVPOS_STRAVMONEY, GOVPOS_NEWSALARY, GOVPOS_PRIORITY, 
						GOVPOS_STATUS, GOVPOS_REPEATFLAG, GOVPOS_EMPID, GOVPOS_RESVFLAG, GOVPOS_WKSTATUS, GOVPOS_ID, 
						GOVPOS_INCFLAG, GOVPOS_INOUTFLAG, GOVPOS_RANKFLAG, GOVPOS_LASTPOSC, GOVPOS_STRAVFLAG, GOVPOS_ASB, 
						GOVPOS_TYPE, TYPE_POS, SECTION_ORDER, GOVPOS_ASBMONEY, DEPT_CODE_KP, WPOS_CODE_PRE, GOVPOS_REMARK, 
						REASON_CODE, MPOS_KROB, WPOS_KROB, OLD_POSNO, GOVPOS_PREADJLEV, GOVPOS_LC_ALL
						FROM GOVPOSITION 
						ORDER BY GOV_POSNO ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "$cmd<br>";
		while($data = $db_dpis35->get_array()){
			$PER_POSITION++;
			$POS_NO = trim($data[GOV_POSNO]);
			$DEPT_CODE = trim($data[DEPT_CODE]);
			$SECTION_CODE = trim($data[SECTION_CODE]);
			$POS_SALARY = trim($data[GOVPOS_SALARY]);
			$WPOS_CODE = trim($data[WPOS_CODE]);
			$POS_MGTSALARY = trim($data[GOVPOS_POSMONEY]);
			$MPOS_CODE = trim($data[MPOS_CODE]);
			$GOVPOS_RANKFLAG = trim($data[GOVPOS_RANKFLAG]);
			$GOVPOS_LC = trim($data[GOVPOS_LC]);
//			$POS_REMARK = trim($data[GOVPOS_REMARK]);
			$POS_CONDITION = trim($data[GOVPOS_REMARK]);
			$PC_CODE = "";
			if (trim($data[REASON_CODE]) > "0") $PC_CODE = trim($data[REASON_CODE]);
			$LEVEL_NO = "";
			if ($GOVPOS_LC=="21") $LEVEL_NO = "O1";
			elseif ($GOVPOS_LC=="22") $LEVEL_NO = "O2";
			elseif ($GOVPOS_LC=="23") $LEVEL_NO = "O3";
			elseif ($GOVPOS_LC=="24") $LEVEL_NO = "O4";
			elseif ($GOVPOS_LC=="31") $LEVEL_NO = "K1";
			elseif ($GOVPOS_LC=="32") $LEVEL_NO = "K2";
			elseif ($GOVPOS_LC=="33") $LEVEL_NO = "K3";
			elseif ($GOVPOS_LC=="34") $LEVEL_NO = "K4";
			elseif ($GOVPOS_LC=="35") $LEVEL_NO = "K5";
			elseif ($GOVPOS_LC=="41") $LEVEL_NO = "D1";
			elseif ($GOVPOS_LC=="42") $LEVEL_NO = "D2";
			elseif ($GOVPOS_LC=="51") $LEVEL_NO = "M1";
			elseif ($GOVPOS_LC=="52") $LEVEL_NO = "M2";
			else echo "ระดับตำแหน่ง $GOVPOS_LC<br>";

			$TECH_CLASS2 = trim($data[TECH_CLASS2]);
			$PT_CODE = trim($data[POSITION_TYPE]);

			$OT_CODE = '01';
			$POS_STATUS = 1;

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = '$DEPT_CODE' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$ORG_ID = $data2[NEW_CODE];

			$cmd = " select OL_CODE, ORG_ID_REF from PER_ORG where ORG_ID = $ORG_ID ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$OL_CODE = trim($data2[OL_CODE]);
			if ($OL_CODE=="04") {
				$ORG_ID_1 = $ORG_ID;
				$ORG_ID = $data2[ORG_ID_REF];
			}

			$ORG_ID_2 = "";
			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = '$DEPT_CODE$SECTION_CODE' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$ORG_ID_1 = $data2[NEW_CODE];
			if ($ORG_ID_1) {
				$cmd = " select OL_CODE, ORG_ID_REF from PER_ORG where ORG_ID = $ORG_ID_1 ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$OL_CODE = trim($data2[OL_CODE]);
				if ($OL_CODE=="05") {
					$ORG_ID_2 = $ORG_ID_1;
					$ORG_ID_1 = $data2[ORG_ID_REF];
				}
			}

			$GOVPOS_LC_ALL = trim($data[GOVPOS_LC_ALL]);
			$CL_NAME = "";
			if ($GOVPOS_LC_ALL=="21") $CL_NAME = "ปฏิบัติงาน";
			elseif ($GOVPOS_LC_ALL=="22") $CL_NAME = "ชำนาญงาน";
			elseif ($GOVPOS_LC_ALL=="23") $CL_NAME = "อาวุโส";
			elseif ($GOVPOS_LC_ALL=="24") $CL_NAME = "ทักษะพิเศษ";
			elseif ($GOVPOS_LC_ALL=="25") $CL_NAME = "ปฏิบัติงาน หรือ ชำนาญงาน";
			elseif ($GOVPOS_LC_ALL=="31") $CL_NAME = "ปฏิบัติการ";
			elseif ($GOVPOS_LC_ALL=="32") $CL_NAME = "ชำนาญการ";
			elseif ($GOVPOS_LC_ALL=="33") $CL_NAME = "ชำนาญการพิเศษ";
			elseif ($GOVPOS_LC_ALL=="34") $CL_NAME = "เชี่ยวชาญ";
			elseif ($GOVPOS_LC_ALL=="35") $CL_NAME = "ทรงคุณวุฒิ";
			elseif ($GOVPOS_LC_ALL=="36") $CL_NAME = "ปฏิบัติการ หรือ ชำนาญการ";
			elseif ($GOVPOS_LC_ALL=="37") $CL_NAME = "ปฏิบัติการ หรือ ชำนาญการ หรือ ชำนาญการพิเศษ";
			elseif ($GOVPOS_LC_ALL=="41") $CL_NAME = "อำนวยการต้น";
			elseif ($GOVPOS_LC_ALL=="42") $CL_NAME = "อำนวยการสูง";
			elseif ($GOVPOS_LC_ALL=="51") $CL_NAME = "บริหารต้น";
			elseif ($GOVPOS_LC_ALL=="52") $CL_NAME = "บริหารสูง";
			else echo "ช่วงระดับตำแหน่ง $GOVPOS_LC_ALL<br>";
/*			$cmd = " SELECT CASE AS PNAME FROM MPS_POSCASE WHERE PCODE = '$GOVPOS_RANKFLAG' ";
			$db_dpis351->send_cmd($cmd);
			$data1 = $db_dpis351->get_array();
			$PNAME = trim($data1[PNAME]);

			$cmd = " select CL_NAME from PER_CO_LEVEL where CL_NAME = '$PNAME' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$CL_NAME = $data2[CL_NAME];
			if ($GOVPOS_RANKFLAG=="300") $CL_NAME = "บริหารสูง";
			elseif ($GOVPOS_RANKFLAG=="290") $CL_NAME = "บริหารต้น";
			elseif ($GOVPOS_RANKFLAG=="280") $CL_NAME = "อำนวยการสูง";
			elseif ($GOVPOS_RANKFLAG=="270") $CL_NAME = "อำนวยการต้น";
			elseif ($GOVPOS_RANKFLAG=="210") $CL_NAME = "ปฏิบัติการ หรือ ชำนาญการ หรือ ชำนาญการพิเศษ";
			elseif ($GOVPOS_RANKFLAG=="200") $CL_NAME = "ปฏิบัติการ หรือ ชำนาญการ";
			elseif ($GOVPOS_RANKFLAG=="100") $CL_NAME = "ปฏิบัติงาน หรือ ชำนาญงาน";
			if (!$CL_NAME) echo "ช่วงระดับตำแหน่ง $GOVPOS_RANKFLAG - $PNAME<br>";
*/	
			$cmd = " SELECT WPOS_NAME FROM WORKING_POSITION WHERE WPOS_CODE = '$WPOS_CODE' ";
			$db_dpis351->send_cmd($cmd);
			$data1 = $db_dpis351->get_array();
			$WPOS_NAME = trim($data1[WPOS_NAME]);
			$WPOS_NAME = str_replace("ผู้อำนวยการเฉพาะด้าน(", "ผู้อำนวยการเฉพาะด้าน (", $WPOS_NAME);

			$cmd = " select PL_CODE from PER_LINE where PL_NAME = '$WPOS_NAME' order by PL_CODE desc ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PL_CODE = $data2[PL_CODE];
			if (!$PL_CODE) echo "ตำแหน่งในสายงาน $WPOS_CODE - $WPOS_NAME<br>";
	
			$PM_CODE = "";
			if ($MPOS_CODE > "0") {
				$cmd = " SELECT MPOS_NAME FROM MANAGING_POSITION WHERE MPOS_CODE = '$MPOS_CODE' ";
				$db_dpis351->send_cmd($cmd);
				$data1 = $db_dpis351->get_array();
				$MPOS_NAME = trim($data1[MPOS_NAME]);

				$cmd = " select PM_CODE from PER_MGT where PM_NAME = '$MPOS_NAME' ";
				$db_dpis2->send_cmd($cmd);
	//			$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PM_CODE = $data2[PM_CODE];
				if (!$PM_CODE) echo "ตำแหน่งในการบริหารงาน $MPOS_CODE - $MPOS_NAME<br>";
			}

			$PM_CODE = trim($PM_CODE)? "'".$PM_CODE."'" : "NULL";
			$PC_CODE = trim($PC_CODE)? "'".$PC_CODE."'" : "NULL";
//			if (!$ORG_ID) $ORG_ID = 1;
			if (!$ORG_ID_1) $ORG_ID_1 = "NULL";
			if (!$ORG_ID_2) $ORG_ID_2 = "NULL";

			$cmd = " INSERT INTO PER_POSITION (POS_ID, ORG_ID, POS_NO, OT_CODE, ORG_ID_1, ORG_ID_2, PM_CODE, PL_CODE, 
							CL_NAME, POS_SALARY, POS_MGTSALARY, PT_CODE, PC_CODE, POS_CONDITION, POS_REMARK, POS_DATE, 
							POS_GET_DATE, POS_CHANGE_DATE, POS_STATUS, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID, 
							LEVEL_NO)
							VALUES ($POS_ID, $ORG_ID, '$POS_NO', '$OT_CODE', $ORG_ID_1, $ORG_ID_2, $PM_CODE, '$PL_CODE', 
							'$CL_NAME', $POS_SALARY, $POS_MGTSALARY, '$PT_CODE', $PC_CODE, '$POS_CONDITION', '$POS_REMARK', 
							'$START_DATE', '$POS_GET_DATE', '$START_DATE', $POS_STATUS, $UPDATE_USER, '$UPDATE_DATE', 
							$SESS_DEPARTMENT_ID, '$LEVEL_NO') ";
			$db_dpis->send_cmd($cmd);

			$cmd1 = " SELECT POS_ID FROM PER_POSITION WHERE POS_ID = $POS_ID "; 
			$count_data = $db_dpis1->send_cmd($cmd1);
			if (!$count_data) {
				echo "$cmd<br>==================<br>";
				$db_dpis->show_error();
				echo "<br>end ". ++$i  ."=======================<br>";
			}

			$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
							VALUES ('PER_POSITION', '$POS_NO', '$POS_ID', $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$POS_ID++;
		} // end while						

		$cmd = " select count(POS_ID) as COUNT_NEW from PER_POSITION ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_POSITION - $PER_POSITION = $COUNT_NEW<br>";

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
		$db_dpis35->free_result();
		$db_dpis351->free_result();
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
			"per_salpromote", "per_family", "per_licensehis", "per_personal", "per_pos_move", "per_salquotadtl1", "per_salquotadtl2", "per_salquota", "per_command", "per_decor", 
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
		$db_dpis35->free_result();
		$db_dpis351->free_result();

		$cmd = " select max(POEM_ID) as MAX_ID from PER_POS_EMP ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$POEM_ID = $data[MAX_ID] + 1;

		$cmd = " SELECT POS_NO, POS_CODE, SEC_CODE, SECTION_CODE, DEPT_CODE, ID, POS_SALARY, POS_LAGMONEY, 
						POS_LASTMONEY, POS_INCMONEY, POS_DECMONEY, POS_UPMONEY, POS_MONEYSTRAV, POS_NEWPAY, 
						POS_PRIORITY, POS_STATUS, POS_REPEATFLAG, GOV_POSNO, POS_INOUTFLAG, DEPT_ORDER, POS_INCFLAG, 
						POS_STRAVFLAG, CHG_POSCODE, CHG_SECCODE, POS_LIFEMONEY, POS_ACC2FLAG, GROUP_ASB, FLAG_ASB, 
						ASB_SALARY, DEPT_PROPERTY, POS_PROPERTY, ASB_LASTSALARY, FLAG_MONEYSTRAV, POS_SALGROUP, 
						POS_LAGGROUP, POS_LASTGROUP, ASB_LAGSALARY, 	ASB_LAGGROUP, ASB_LASTGROUP, STP_LAGFLAG, 
						STP_LASTFLAG, STP_SALFLAG
						FROM EMPPOSITION_EACHPOSITION 
						ORDER BY POS_NO ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "$cmd<br>";
		while($data = $db_dpis35->get_array()){
			$PER_POS_EMP++;
			$POEM_NO = trim($data[POS_NO]);
//			$POEM_STATUS = trim($data[POS_STATUS]);
			$SEC_CODE = trim($data[SEC_CODE]);
			$SECTION_CODE = trim($data[SECTION_CODE]);
			$DEPT_CODE = trim($data[DEPT_CODE]);
			$POS_CODE = trim($data[POS_CODE]);
			$PN_CODE = "";
			$cmd = " SELECT NAME FROM EMPPOSITION_POSDETAIL WHERE POS_CODE = '$POS_CODE' ";
			$db_dpis351->send_cmd($cmd);
			$data1 = $db_dpis351->get_array();
			$NAME = trim($data1[NAME]);
			$LEVEL_NO = "";
			if (strpos($NAME,"บ 1") !== false) $LEVEL_NO = "บ1";
			elseif (strpos($NAME,"บ 1") !== false) $LEVEL_NO = "บ1";
			elseif (strpos($NAME,"บ 2 / หัวหน้า") !== false) $LEVEL_NO = "บ3";
			elseif (strpos($NAME,"บ 2") !== false) $LEVEL_NO = "บ2";
			elseif (strpos($NAME,"ส 1") !== false) $LEVEL_NO = "ส1";
			elseif (strpos($NAME,"ส 2 / หัวหน้า") !== false) $LEVEL_NO = "ส3";
			elseif (strpos($NAME,"ส 2") !== false) $LEVEL_NO = "ส2";
			elseif (strpos($NAME,"ส 3 / หัวหน้า") !== false) $LEVEL_NO = "ส5";
			elseif (strpos($NAME,"ส 3") !== false) $LEVEL_NO = "ส4";
			elseif (strpos($NAME,"ส 4 / หัวหน้า") !== false) $LEVEL_NO = "ส7";
			elseif (strpos($NAME,"ส 4") !== false) $LEVEL_NO = "ส6";
			elseif (strpos($NAME,"ช 1") !== false) $LEVEL_NO = "ช1";
			elseif (strpos($NAME,"ช 2 / หัวหน้า") !== false) $LEVEL_NO = "ช3";
			elseif (strpos($NAME,"ช 2") !== false) $LEVEL_NO = "ช2";
			elseif (strpos($NAME,"ช 3 / หัวหน้า") !== false) $LEVEL_NO = "ช5";
			elseif (strpos($NAME,"ช 3") !== false) $LEVEL_NO = "ช4";
			elseif (strpos($NAME,"ช 4 / หัวหน้า") !== false) $LEVEL_NO = "ช7";
			elseif (strpos($NAME,"ช 4") !== false) $LEVEL_NO = "ช6";

			$NAME = str_replace("พ.", "พนักงาน", $NAME);
			$NAME = str_replace("พข.", "พนักงานขับ", $NAME);
			$NAME = str_replace("พค.", "พนักงานควบคุม", $NAME);
			$NAME = str_replace("หน.", "หัวหน้า", $NAME);
			$NAME = str_replace("ผช.", "ผู้ช่วย", $NAME);
			$NAME = str_replace("ดีดชั้น", "ดีด ชั้น", $NAME);
			$NAME = str_replace("ชั้น 1", "", $NAME);
			$NAME = str_replace("ชั้น 2", "", $NAME);
			$NAME = str_replace("ชั้น 3", "", $NAME);
			$NAME = str_replace("ชั้น 4", "", $NAME);
			$NAME = str_replace("ช่างยนต์", "ช่างเครื่องยนต์", $NAME);
			$NAME = str_replace("พนักงานพิมพ์ดีด", "พนักงานพิมพ์", $NAME);
			$NAME = str_replace("พนักงานบัญชี", "พนักงานการเงินและบัญชี", $NAME);
			$NAME = str_replace("พนักงานอัดสำเนา", "พนักงานบริการอัดสำเนา", $NAME);
			$NAME = str_replace("หัวหน้าหล่อลื่น", "พนักงานบริการน้ำมันและหล่อลื่น", $NAME);
			$NAME = str_replace("พนักงานประจำท่าแพขนานยนต์", "พนักงานประจำแพขนานยนต์", $NAME);
			$NAME = str_replace("พนักงานควบคุมเครื่องจักรขนาดเบา", "พนักงานควบคุมเครื่องจักรกลขนาดเบา", $NAME);
			$NAME = str_replace("  บ 1", "", $NAME);
			$NAME = str_replace("  บ 2 / หัวหน้า", "", $NAME);
			$NAME = str_replace("  บ 2", "", $NAME);
			$NAME = str_replace("  ส 1", "", $NAME);
			$NAME = str_replace("  ส 2 / หัวหน้า", "", $NAME);
			$NAME = str_replace("  ส 2", "", $NAME);
			$NAME = str_replace("  ส 3 / หัวหน้า", "", $NAME);
			$NAME = str_replace("  ส 3", "", $NAME);
			$NAME = str_replace("  ส 4 / หัวหน้า", "", $NAME);
			$NAME = str_replace("  ส 4", "", $NAME);
			$NAME = str_replace("  ช 1", "", $NAME);
			$NAME = str_replace("  ช 2 / หัวหน้า", "", $NAME);
			$NAME = str_replace("  ช 2", "", $NAME);
			$NAME = str_replace("  ช 3 / หัวหน้า", "", $NAME);
			$NAME = str_replace("  ช 3", "", $NAME);
			$NAME = str_replace("  ช 4 / หัวหน้า", "", $NAME);
			$NAME = str_replace("  ช 4", "", $NAME);

			$cmd = " select PN_CODE from PER_POS_NAME where PN_NAME = trim('$NAME') order by PN_SEQ_NO ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PN_CODE = $data2[PN_CODE];
			if (!$PN_CODE) echo "ชื่อตำแหน่งลูกจ้างประจำ $POS_CODE - $NAME - $cmd<br>";
	
			$POEM_MIN_SALARY = $data[SALARY_START_M];
			$POEM_MAX_SALARY = $data[POS_LAGMONEY];
			$POEM_REMARK = trim($data[REMARK]);
			$POS_SALGROUP = trim($data[POS_SALGROUP]);
			if ($POS_SALGROUP==1) $PG_CODE_SALARY = "1000";
			elseif ($POS_SALGROUP==2) $PG_CODE_SALARY = "2000";
			elseif ($POS_SALGROUP==3) $PG_CODE_SALARY = "3000";
			elseif ($POS_SALGROUP==4) $PG_CODE_SALARY = "4000";
			elseif ($POS_SALGROUP==5) $PG_CODE_SALARY = "2000"; // 1+2
			elseif ($POS_SALGROUP==6) $PG_CODE_SALARY = "3000"; // 2+3
			if (!$POEM_MIN_SALARY) $POEM_MIN_SALARY = 0;
			if (!$POEM_MAX_SALARY) $POEM_MAX_SALARY = 0;
			if (!$POEM_STATUS) $POEM_STATUS = 1;

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = '$DEPT_CODE' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$ORG_ID = $data2[NEW_CODE];

			$cmd = " select OL_CODE, ORG_ID_REF from PER_ORG where ORG_ID = $ORG_ID ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$OL_CODE = trim($data2[OL_CODE]);
			if ($OL_CODE=="04") {
				$ORG_ID_1 = $ORG_ID;
				$ORG_ID = $data2[ORG_ID_REF];
			}

			$ORG_ID_2 = "";
			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = '$DEPT_CODE$SECTION_CODE' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$ORG_ID_1 = $data2[NEW_CODE];
			if ($ORG_ID_1) {
				$cmd = " select OL_CODE, ORG_ID_REF from PER_ORG where ORG_ID = $ORG_ID_1 ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$OL_CODE = trim($data2[OL_CODE]);
				if ($OL_CODE=="05") {
					$ORG_ID_2 = $ORG_ID_1;
					$ORG_ID_1 = $data2[ORG_ID_REF];
				}
			}

//			if (!$ORG_ID) $ORG_ID = 1;
			if (!$ORG_ID_1) $ORG_ID_1 = "NULL";
			if (!$ORG_ID_2) $ORG_ID_2 = "NULL";

			$cmd = " INSERT INTO PER_POS_EMP (POEM_ID, ORG_ID, POEM_NO, ORG_ID_1, ORG_ID_2, PN_CODE, POEM_MIN_SALARY, 
							  POEM_MAX_SALARY, POEM_STATUS, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID, POEM_REMARK, POEM_NO_NAME, 
							  PG_CODE_SALARY, LEVEL_NO)
							  VALUES ($POEM_ID, $ORG_ID, '$POEM_NO', $ORG_ID_1, $ORG_ID_2, '$PN_CODE', $POEM_MIN_SALARY, $POEM_MAX_SALARY, 
							  $POEM_STATUS, $UPDATE_USER, '$UPDATE_DATE', $SESS_DEPARTMENT_ID, '$POEM_REMARK', '$POEM_NO_NAME', 
							  '$PG_CODE_SALARY', '$LEVEL_NO') ";
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
		$db_dpis35->free_result();
		$db_dpis351->free_result();
	} // end if

	if($command=='POS_EMPSER'){
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
			"per_salpromote", "per_family", "per_licensehis", "per_personal", "per_pos_move", "per_salquotadtl1", "per_salquotadtl2", "per_salquota", "per_command", "per_decor", 
			"per_order_dtl", "per_req3_dtl" );
		for ( $i=0; $i<count($table); $i++ ) { 
			if ($table[$i]=="per_bonusquota" || $table[$i]=="per_scholar" || $table[$i]=="per_salquota" || $table[$i]=="per_command" || $table[$i]=="per_decor")
				$cmd = " delete from $table[$i] ";
			else
				$cmd = " truncate table $table[$i] ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end for

// ตำแหน่งพนักงานราชการ 30292 - 30292

		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_POS_EMPSER' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_POS_EMPSER ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();
		$db_dpis351->free_result();

		$cmd = " select max(POT_ID) as MAX_ID from PER_POS_EMPSER ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$POT_ID = $data[MAX_ID] + 1;

		$cmd = " SELECT POS_NO, POS_CODE, SECTION_CODE, DEPT_CODE, ID, POS_STATUS, POS_LASTMONEY, 
						POS_THISMONEY, STP_THISFLAG, POS_SPCMONEY, POS_SPCRATE, POS_PCHK, POS_UPMONEY, 
						POS_UPRATE, POS_OTHMONEY, STP_LASTFLAG, POS_LASTGROUP, POS_THISGROUP, LAST_SPCMONEY, 
						LAST_SPCRATE, LAST_PCHK, LAST_UPMONEY, LAST_UPRATE
						FROM CTPOSITION_EACHPOSITION 
						ORDER BY POS_NO ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "$cmd<br>";
		while($data = $db_dpis35->get_array()){
			$PER_POS_EMPSER++;
			$POEMS_NO = trim($data[POS_NO]);
			$POEM_STATUS = trim($data[POS_STATUS]);
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

			$cmd = " INSERT INTO PER_POS_EMPSER (POT_ID, ORG_ID, POT_NO, ORG_ID_1, ORG_ID_2, TP_CODE, POT_MIN_SALARY, 
							  POT_MAX_SALARY, POT_STATUS, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID, POT_REMARK)
							  VALUES ($POT_ID, $ORG_ID, '$POT_NO', $ORG_ID_1, $ORG_ID_2, '$TP_CODE', $POT_MIN_SALARY, $POT_MAX_SALARY, 
							  $POT_STATUS, $UPDATE_USER, '$UPDATE_DATE', $SESS_DEPARTMENT_ID, '$POT_REMARK') ";
			$db_dpis->send_cmd($cmd);

			$cmd1 = " SELECT POT_ID FROM PER_POS_EMPSER WHERE POT_ID = $POT_ID "; 
			$count_data = $db_dpis1->send_cmd($cmd1);
			if (!$count_data) {
				echo "$cmd<br>==================<br>";
				$db_dpis->show_error();
				echo "<br>end ". ++$i  ."=======================<br>";
			}

			if ($POT_STATUS==1) {
				$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
								VALUES ('PER_POS_EMPSER', '$POT_NO', '$POT_ID', $UPDATE_USER, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			}

			$POT_ID++;
		} // end while						

		$cmd = " select count(POT_ID) as COUNT_NEW from PER_POS_EMPSER ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_POS_EMPSER - $PER_POS_EMPSER - $COUNT_NEW<br>";

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
		$db_dpis35->free_result();
		$db_dpis351->free_result();
	} // end if

	if($command=='PERSONAL' || $command=='PER_EMP'){
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
			"per_salpromote", "per_family", "per_licensehis" );
		for ( $i=0; $i<count($table); $i++ ) { 
			if ($table[$i]=="per_bonusquota" || $table[$i]=="per_scholar" || $table[$i]=="per_salquota" || $table[$i]=="per_command" || $table[$i]=="per_decor")
				$cmd = " delete from $table[$i] ";
			else
				$cmd = " truncate table $table[$i] ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end for

// ข้าราชการ 33772
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_PERSONAL' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		if ($command=='PERSONAL') {
			$where = "G";
			$PER_TYPE = 1;
			$table = "PER_POSITION";
			$OT_CODE = "01";
		} elseif ($command=='PER_EMP') {
			$where = "E";
			$PER_TYPE = 2;
			$table = "PER_POS_EMP";
			$OT_CODE = "07";
		}

		$cmd = " DELETE FROM PER_PERSONAL WHERE PER_TYPE = $PER_TYPE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();
		$db_dpis351->free_result();

		$cmd = " SELECT a.ID, PRE_CODE, FNAME, LNAME, SEX, BIRTH_DATE, RGTIRE_DATE, BIRTH_PLACE, FILE_NO, NATION, RACE, 
						RELIGION_CODE, STAT_CODE, BLAME, OLD_ID, PICTURE_NAME, REC_STAT, ID_TYPE, GOVKI_25_DATE, ID_CARD, 
						S_C, CARRIER_CERTIFICATE, S_STEP, CARRIER_CERTIFICATE_ENG, E_PRE_CODE, E_FNAME, E_LNAME, 
						WPOS_CODE, MPOS_CODE, RC_DATE, TF_DATE, WK_DATE, N_DIV_DATE, N_PST_DATE, AMT_N_PST, PV_LV_DATE, 
						N_LV_DATE, NAT_N_DIV, WK_STAT, LASTYEAR_C, LASTYEAR_SALARY, MONEY_QUAL, MONEY_PSR, MONEY_LIFE,  
						MONEY_PPM, STEP_STATUS, DEPT_CODE, SECTION_CODE, POS_NO, QUA, TYPE_POS_CURRENT, TYPE_POS_GO, 
						CARRIER_CERTIFICATE_NO, SEND_DATE, EXPRIE_DATE, TYPE_POS, J_ORDER, MPOS_RAK, WPOS_RAK, LEV_RAK, 
						DIV_RAK, PST_TYPE, LEV_GRP, TYPE_POS_RAK, POSNO_RAK, SALARY, OLD_LEV
						FROM COMMON_HISTORY a, GOVERMENT_SERVICE b
						WHERE a.ID=b.ID(+) AND a.ID != '999999999' AND ID_TYPE='$where' 
						ORDER BY a.ID ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "$cmd<br>";
		while($data = $db_dpis35->get_array()){
			$PER_PERSONAL++;
			$PER_ID = trim($data[ID]);
			$PRE_CODE = trim($data[PRE_CODE]);
			$PER_NAME = trim($data[FNAME]);
			$PER_SURNAME = trim($data[LNAME]);
			$PER_ENG_NAME = trim($data[E_FNAME]);
			$PER_ENG_SURNAME = trim($data[E_LNAME]);
			$PER_CARDNO = trim($data[ID_CARD]);
			$SEX = trim($data[SEX]);
			$PER_GENDER = "";
			if ($SEX=="M") $PER_GENDER = 1;
			elseif ($SEX=="F") $PER_GENDER = 2;
			$BIRTH_DATE = trim($data[BIRTH_DATE]);
			$RC_DATE = trim($data[RC_DATE]);
			$WK_DATE = trim($data[WK_DATE]);
			$RGTIRE_DATE = trim($data[RGTIRE_DATE]); 
			$BIRTH_YEAR = substr($BIRTH_DATE, 4, 4) - 543;
			$RC_YEAR = substr($RC_DATE, 4, 4) - 543;
			$WK_YEAR = substr($WK_DATE, 4, 4) - 543;
			$RETIRE_YEAR = $BIRTH_YEAR + 60;
			$PER_BIRTHDATE = ($BIRTH_DATE)? ($BIRTH_YEAR ."-". substr($BIRTH_DATE, 2, 2) ."-". substr($BIRTH_DATE, 0, 2)) : "";
			$PER_STARTDATE = ($RC_DATE)? ($RC_YEAR ."-". substr($RC_DATE, 2, 2) ."-". substr($RC_DATE, 0, 2)) : "";
			$PER_OCCUPYDATE = ($WK_DATE)? ($WK_YEAR ."-". substr($WK_DATE, 2, 2) ."-". substr($WK_DATE, 0, 2)) : "";
			if(substr($BIRTH_DATE, 2, 2) > 10 || (substr($BIRTH_DATE, 2, 2)==10 && substr($BIRTH_DATE, 0, 2) > "01"))    
				$RETIRE_YEAR += 1;
			$PER_RETIREDATE = $RETIRE_YEAR."-10-01"; 
			$PER_BIRTH_PLACE = trim($data[BIRTH_PLACE]);
			$PER_FILE_NO = trim($data[FILE_NO]);
			$NATION = trim($data[NATION]);
			$RACE = trim($data[RACE]);
			$RELIGION_CODE = trim($data[RELIGION_CODE]);
			if ($RELIGION_CODE=="1") $RE_CODE = '01';
			elseif ($RELIGION_CODE=="2") $RE_CODE = '02';
			elseif ($RELIGION_CODE=="3") $RE_CODE = '03';
			else $RE_CODE = "06";
			$STAT_CODE = trim($data[STAT_CODE]); 
			if ($STAT_CODE==1) $MR_CODE = '1';
			elseif ($STAT_CODE==2) $MR_CODE = '2';
			elseif ($STAT_CODE==3) $MR_CODE = '3';
			elseif ($STAT_CODE==4) $MR_CODE = '4';
			elseif ($STAT_CODE==9) $MR_CODE = '9';
			else $MR_CODE = "9";
			$PER_SCAR = trim($data[BLAME]);
			$REC_STAT = trim($data[REC_STAT]);
			if ($REC_STAT == 10 || $REC_STAT == 11 || $REC_STAT == 12) $PER_STATUS = 1; else $PER_STATUS = 2;
			$MOV_CODE = "";
			if ($REC_STAT == 11) $MOV_CODE = "11260";
			elseif ($REC_STAT == 12) $MOV_CODE = "10510";
			elseif ($REC_STAT == 20) $MOV_CODE = "11810";
			elseif ($REC_STAT == 21) $MOV_CODE = "10610";
			elseif ($REC_STAT == 22) $MOV_CODE = "12210";
			elseif ($REC_STAT == 23) $MOV_CODE = "12310";
			elseif ($REC_STAT == 24) $MOV_CODE = "11910";
			elseif ($REC_STAT == 25) $MOV_CODE = "12110";
			elseif ($REC_STAT == 26) $MOV_CODE = "120";
			elseif ($REC_STAT == 31) $MOV_CODE = "11830";
			elseif ($REC_STAT == 50) $MOV_CODE = "125";
			elseif ($REC_STAT == 55) $MOV_CODE = "125";

			$ID_TYPE = trim($data[ID_TYPE]);
			if ($ID_TYPE=="G") $PER_TYPE = 1;
			elseif ($ID_TYPE=="E") $PER_TYPE = 2;
			elseif ($ID_TYPE=="C") $PER_TYPE = 3;
//			$PER_STARTDATE = trim($data[BEGIN_ENTRY_DATE]);
//			$PER_OCCUPYDATE = trim($data[RETURN_OCCUPY_DATE]);
			$S_C = trim($data[S_C]);
			$LEVEL_NO = "";
			if ($S_C=="1") $LEVEL_NO = "01";
			elseif ($S_C=="2") $LEVEL_NO = "02";
			elseif ($S_C=="3") $LEVEL_NO = "03";
			elseif ($S_C=="4") $LEVEL_NO = "04";
			elseif ($S_C=="5") $LEVEL_NO = "05";
			elseif ($S_C=="6") $LEVEL_NO = "06";
			elseif ($S_C=="7") $LEVEL_NO = "07";
			elseif ($S_C=="8") $LEVEL_NO = "08";
			elseif ($S_C=="9") $LEVEL_NO = "09";
			elseif ($S_C=="10") $LEVEL_NO = "10";
			elseif ($S_C=="11") $LEVEL_NO = "11";
			elseif ($S_C=="21") $LEVEL_NO = "O1";
			elseif ($S_C=="22") $LEVEL_NO = "O2";
			elseif ($S_C=="23") $LEVEL_NO = "O3";
			elseif ($S_C=="24") $LEVEL_NO = "O4";
			elseif ($S_C=="31") $LEVEL_NO = "K1";
			elseif ($S_C=="32") $LEVEL_NO = "K2";
			elseif ($S_C=="33") $LEVEL_NO = "K3";
			elseif ($S_C=="34") $LEVEL_NO = "K4";
			elseif ($S_C=="35") $LEVEL_NO = "K5";
			elseif ($S_C=="41") $LEVEL_NO = "D1";
			elseif ($S_C=="42") $LEVEL_NO = "D2";
			elseif ($S_C=="51") $LEVEL_NO = "M1";
			elseif ($S_C=="52") $LEVEL_NO = "M2";
			elseif ($S_C && $S_C!="0") echo "ระดับตำแหน่ง $S_C<br>";
			$SECTION_CODE = trim($data[SECTION_CODE]);
			$DEPT_CODE = trim($data[DEPT_CODE]);
			$LH_LICENSE_NO = trim($data[CARRIER_CERTIFICATE_NO]);
			$SEND_DATE = trim($data[SEND_DATE]);
			$EXPRIE_DATE = trim($data[EXPRIE_DATE]);
			if ($LH_LICENSE_NO) {
				$LH_LICENSE_DATE = $LH_EXPIRE_DATE = "";
				$LT_CODE = "10";
				if ($SEND_DATE) {
					$SEND_YEAR = substr($SEND_DATE, 4, 4) - 543;
					$LH_LICENSE_DATE = ($SEND_DATE)? ($SEND_YEAR ."-". substr($SEND_DATE, 2, 2) ."-". substr($SEND_DATE, 0, 2)) : "";
				}
				if ($EXPRIE_DATE) {
					$EXPRIE_YEAR = substr($EXPRIE_DATE, 4, 4) - 543;
					$LH_EXPIRE_DATE = ($EXPRIE_DATE)? ($EXPRIE_YEAR ."-". substr($EXPRIE_DATE, 2, 2) ."-". substr($EXPRIE_DATE, 0, 2)) : "";
				}
				$cmd = " select max(LH_ID) as max_id from PER_LICENSEHIS ";
				$db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$data1 = array_change_key_case($data1, CASE_LOWER);
				$LH_ID = $data1[max_id] + 1;
			
				$cmd = " insert into PER_LICENSEHIS	(LH_ID, PER_ID, PER_CARDNO, LT_CODE, LH_SUB_TYPE, LH_LICENSE_NO, 
								LH_LICENSE_DATE, LH_EXPIRE_DATE, LH_SEQ_NO, LH_REMARK, LH_MAJOR, UPDATE_USER, UPDATE_DATE)
								values ($LH_ID, $PER_ID, '$PER_CARDNO', '$LT_CODE', '$LH_SUB_TYPE', '$LH_LICENSE_NO', '$LH_LICENSE_DATE', 
								'$LH_EXPIRE_DATE', '$LH_SEQ_NO', '$LH_REMARK', '$LH_MAJOR', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
			}
			$POS_NO = trim($data[POS_NO]);
			$PER_SALARY = $data[SALARY]+0;
//			$MOV_CODE = trim($data[FLAG_TO_NAME_CODE]);
//			$BLOOD = trim($data[BLOOD]);
//			if ($BLOOD=="1") $PER_BLOOD = "A";
//			elseif ($BLOOD=="2") $PER_BLOOD = "B";
//			elseif ($BLOOD=="4") $PER_BLOOD = "O";
//			elseif ($BLOOD=="3" || $BLOODGRP=="เอ บี") $PER_BLOOD = "AB";
//			else $PER_BLOOD = "-";
//			$PER_TAXNO = trim($data[TAX_ID]);
//			$FLAG_KBK = trim($data[FLAG_KBK]); 
			if ($FLAG_KBK=="1") $PER_MEMBER = 1;
			else  $PER_MEMBER = 0;

			$PER_MGTSALARY = 0; // รอแก้

			if ($PER_TYPE==2) {
				$PER_STARTDATE = $PER_OCCUPYDATE = "";
				$cmd = " SELECT POS_CODE, SEC_CODE, GROUP_CODE, SAL_STEP, EMP_RC_DATE, EMP_WK_DATE, EMP_N_LV_DATE, CREDIT, LASTYEAR_PAY, 
								MONEYPSR, STEP_STATUS, WK_STATUS, TF_DATE, SECTION_CODE, DEPT_CODE, POS_NO, QUA, EMP_N_PST_DATE
								FROM EMPLOYEE_HISTORY 
								WHERE ID=$PER_ID ";
				$db_dpis351->send_cmd($cmd);
				//$db_dpis351->show_error();
				$data1 = $db_dpis351->get_array();
				$POS_CODE = trim($data1[POS_CODE]);
				$SEC_CODE = trim($data1[SEC_CODE]);
				$GROUP_CODE = trim($data1[GROUP_CODE]);
				$SAL_STEP = trim($data1[SAL_STEP]);
				$EMP_RC_DATE = trim($data1[EMP_RC_DATE]);
				$EMP_WK_DATE = trim($data1[EMP_WK_DATE]);
				if ($EMP_RC_DATE) {
					$EMP_RC_YEAR = substr($EMP_RC_DATE, 4, 4) - 543;
					$PER_STARTDATE = ($EMP_RC_DATE)? ($EMP_RC_YEAR ."-". substr($EMP_RC_DATE, 2, 2) ."-". substr($EMP_RC_DATE, 0, 2)) : "";
				}
				if ($EMP_WK_DATE) {
					$EMP_WK_YEAR = substr($EMP_WK_DATE, 4, 4) - 543;
					$PER_OCCUPYDATE = ($EMP_WK_DATE)? ($EMP_WK_YEAR ."-". substr($EMP_WK_DATE, 2, 2) ."-". substr($EMP_WK_DATE, 0, 2)) : "";
				}
				$EMP_N_LV_DATE = trim($data1[EMP_N_LV_DATE]);
				$CREDIT = trim($data1[CREDIT]);
				$LASTYEAR_PAY = trim($data1[LASTYEAR_PAY]);
				$MONEYPSR = trim($data1[MONEYPSR]);
				$STEP_STATUS = trim($data1[STEP_STATUS]);
				$WK_STATUS = trim($data1[WK_STATUS]);
				$TF_DATE = trim($data1[TF_DATE]);
				$SECTION_CODE = trim($data1[SECTION_CODE]);
				$DEPT_CODE = trim($data1[DEPT_CODE]);
				$POS_NO = trim($data1[POS_NO]);
				$QUA = trim($data1[QUA]);
				$EMP_N_PST_DATE = trim($data1[EMP_N_PST_DATE]);
				$cmd = " SELECT SAL_SALARY FROM EMPPOSITION_SALARY
								WHERE SAL_STEP=$SAL_STEP AND GROUP_CODE=$GROUP_CODE ";
				$db_dpis351->send_cmd($cmd);
				//$db_dpis351->show_error();
				$data1 = $db_dpis351->get_array();
				$PER_SALARY = $data1[SAL_SALARY]+0;
			}

			$cmd = " SELECT PRE_NAME, TITLENAME FROM PREFIX WHERE PRE_CODE = '$PRE_CODE' ";
			$db_dpis351->send_cmd($cmd);
			$data1 = $db_dpis351->get_array();
			$PRE_NAME = trim($data1[PRE_NAME]);
			$TITLENAME = trim($data1[TITLENAME]);

			$cmd = " select PN_CODE from PER_PRENAME 
							where PN_NAME = '$PRE_NAME' or PN_SHORTNAME = '$PRE_NAME' or PN_NAME = '$TITLENAME' or PN_SHORTNAME = '$TITLENAME' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PN_CODE = $data2[PN_CODE];
			if ($PRE_NAME=="ว่าที่ พันตรี") $PN_CODE = "213";
			elseif ($PRE_CODE=="0" && $PER_SURNAME=="ภิงคะสาร (ม.ล.)") $PN_CODE = "122";
			if (!$PN_CODE) echo "คำนำหน้าชื่อ $PRE_CODE - $PRE_NAME - $TITLENAME<br>";
	
			$POS_ID = "NULL";
			$POEM_ID = "NULL";
			$POEMS_ID = "NULL";
			$POT_ID = "NULL";
			$PER_ORGMGT = 0;

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = '$table' AND OLD_CODE = '$POS_NO' ";
			$db_dpis2->send_cmd($cmd);
			//$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if ($command=='PERSONAL') $POS_ID = $data2[NEW_CODE] + 0;
			elseif ($command=='PER_EMP') $POEM_ID = $data2[NEW_CODE] + 0;

			if ($POEM_ID && $POEM_ID != "NULL") {
				$cmd = " select LEVEL_NO from PER_POS_EMP where POEM_ID = $POEM_ID ";
				$db_dpis2->send_cmd($cmd);
				//$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$LEVEL_NO = trim($data2[LEVEL_NO]);
			}
			if ($POS_ORGMGT) $PER_ORGMGT = 1;
				
			$ORG_ID = $ORG_ID_1 = $ORG_ID_2 = "";
			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG_ASS' AND OLD_CODE = '$DEPT_CODE' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$ORG_ID = $data2[NEW_CODE];

			$cmd = " select OL_CODE, ORG_ID_REF from PER_ORG_ASS where ORG_ID = $ORG_ID ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$OL_CODE = trim($data2[OL_CODE]);
			if ($OL_CODE=="04") {
				$ORG_ID_1 = $ORG_ID;
				$ORG_ID = $data2[ORG_ID_REF];
			}

			if ($SECTION_CODE) {
				$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG_ASS' AND OLD_CODE = '$DEPT_CODE$SECTION_CODE' ";
				$db_dpis2->send_cmd($cmd);
	//			$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				if ($ORG_ID_1) 
					$ORG_ID_2 = $data2[NEW_CODE];
				else
					$ORG_ID_1 = $data2[NEW_CODE];
				if ($ORG_ID_1) {
					$cmd = " select OL_CODE, ORG_ID_REF from PER_ORG_ASS where ORG_ID = $ORG_ID_1 ";
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();
					$OL_CODE = trim($data2[OL_CODE]);
					if ($OL_CODE=="05") {
						$ORG_ID_2 = $ORG_ID_1;
						$ORG_ID_1 = $data2[ORG_ID_REF];
					}
				}

				if ($ORG_ID_2) {
					$cmd = " select OL_CODE, ORG_ID_REF from PER_ORG_ASS where ORG_ID = $ORG_ID_2 ";
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();
					$OL_CODE = trim($data2[OL_CODE]);
					if ($OL_CODE=="06") {
						$ORG_ID_3 = $ORG_ID_2;
						$ORG_ID_2 = $data2[ORG_ID_REF];
					}
				}
			}

			if (!$ORG_ID) $ORG_ID = "NULL";
			if (!$ORG_ID_1) $ORG_ID_1 = "NULL";
			if (!$ORG_ID_2) $ORG_ID_2 = "NULL";

			$LEVEL_NO = trim($LEVEL_NO)? "'".$LEVEL_NO."'" : "NULL";
//			if ($LEVEL_NO=="'00'" || $LEVEL_NO=="'0-'") $LEVEL_NO = "NULL";
			if ($POS_ID==0) $POS_ID = "NULL";
			if ($POEM_ID==0) $POEM_ID = "NULL";
			if ($POEMS_ID==0) $POEMS_ID = "NULL";
			if ($POT_ID==0) $POT_ID = "NULL";
			if (!$PAY_ID) $PAY_ID = "NULL";
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

//			if (!$PER_GENDER)
//				if ($PN_CODE == "003") $PER_GENDER = 1;
//				elseif ($PN_CODE == "004" || $PN_CODE == "005") $PER_GENDER = 2;
			if (!$MOV_CODE) $MOV_CODE = "99999"; // รอแก้
			$ES_CODE = "02"; 
			$PER_DISABILITY = 1; 

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
							PER_EFFECTIVEDATE, PER_POS_REASON, PER_POS_YEAR, PER_POS_DOCTYPE, PER_POS_DOCNO, 
							PER_POS_ORG, PER_DISABILITY, PER_BIRTH_PLACE, PER_SCAR, DEPARTMENT_ID_ASS, ORG_ID_1, ORG_ID_2)
							VALUES ($PER_ID, $PER_TYPE, '$OT_CODE', '$PN_CODE', trim('$PER_NAME'), trim('$PER_SURNAME'), 
							trim('$PER_ENG_NAME'), trim('$PER_ENG_SURNAME'), $ORG_ID, $POS_ID, $POEM_ID, $POEMS_ID, $POT_ID, 
							$LEVEL_NO, $PER_ORGMGT, $PER_SALARY, $PER_MGTSALARY, 0, $PER_GENDER, '$MR_CODE', '$PER_CARDNO', 
							'$PER_OFFNO', '$PER_TAXNO', '$PER_BLOOD', '$RE_CODE', '$PER_BIRTHDATE', 	'$PER_RETIREDATE', '$PER_STARTDATE', 
							'$PER_OCCUPYDATE', '$PER_POSDATE', '$PN_CODE_F', trim('$PER_FATHERNAME'), 
							trim('$PER_FATHERSURNAME'), '$PN_CODE_M', trim('$PER_MOTHERNAME'), 
							trim('$PER_MOTHERSURNAME'), '$PV_CODE', '$MOV_CODE', $PER_ORDAIN, '$ORDAINDETAIL', $PER_SOLDIER, 
							$PER_MEMBER, '$PER_MEMBERDATE', $PER_STATUS, NULL, NULL, $SESS_DEPARTMENT_ID, $LEVEL_NO, $UPDATE_USER, 
							'$UPDATE_DATE', NULL, '$PER_FILE_NO', NULL, '$PER_REMARK', '$PER_START_ORG', $PAY_ID, '$ES_CODE', 
							'$OLDSECNAME', '$OLDPOSNAME', '$PER_DOCNO', '$PER_DOCDATE', $PER_COOPERATIVE, '$COOPDETAIL',
							'$PER_EFFECTIVEDATE', '$PER_POS_REASON', '$PER_POS_YEAR', '$PER_POS_DOCTYPE', '$PER_POS_DOCNO', 
							'$PER_POS_ORG', $PER_DISABILITY, '$PER_BIRTH_PLACE', '$PER_SCAR', $SESS_DEPARTMENT_ID, $ORG_ID_1, $ORG_ID_2) ";
			$db_dpis->send_cmd($cmd);

			$cmd1 = " SELECT PER_ID FROM PER_PERSONAL WHERE PER_ID = $PER_ID "; 
			$count_data = $db_dpis1->send_cmd($cmd1);
			if (!$count_data) {
				echo "$cmd<br>==================<br>";
				$db_dpis->show_error();
				echo "<br>end ". ++$i  ."=======================<br>";
			}
/*
			$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
							VALUES ('PER_PERSONAL', '$PER_CARDNO', '$PER_ID', $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
*/
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_PERSONAL where PER_TYPE = $PER_TYPE ";
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
		$db_dpis35->free_result();
		$db_dpis351->free_result();
	} // end if
	
	if($command=='PER_EMPSER'){
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

// พนักงานราชการ 27062 - 27062
		$cmd = " DELETE FROM PER_PERSONAL WHERE PER_TYPE = 3 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();
		$db_dpis351->free_result();

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
		$db_dpis35->send_cmd($cmd);
//							WHERE ID > '1' AND ID < '2'
//		$db_dpis35->show_error();
//		echo "<br>";
		while($data = $db_dpis35->get_array()){
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

			$PER_MGTSALARY = 0; // รอแก้

			$POS_ID = "NULL";
			$POEM_ID = "NULL";
			$POEMS_ID = "NULL";
			$POT_ID = "NULL";
			$PER_ORGMGT = 0;

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_POS_EMPSER' AND OLD_CODE = '$POS_NO' ";
			$db_dpis2->send_cmd($cmd);
			//$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$POT_ID = $data2[NEW_CODE] + 0;

			$cmd = " select DEPARTMENT_ID from PER_POS_EMPSER where POT_ID = $POT_ID ";
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
							'$UPDATE_DATE', NULL, '$PER_FILE_NO', NULL, '$PER_REMARK', '$PER_START_ORG', $PAY_ID, '$ES_CODE', 
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

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_PERSONAL where PER_TYPE = 3 ";
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
		$db_dpis35->free_result();
		$db_dpis351->free_result();
	} // end if
	
	if ($command=='NAME' || $command=='NAME_EMP'){
		$cmd = " truncate table per_namehis ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

// เปลี่ยนชื่อข้าราชการ 9166 ok
		if ($command=='NAME')
			$cmd = " SELECT a.ID, GOVCHG_ORDER, GOVCHG_REFF, a.STAT_CODE, GOVNEW_PNAME, GOVNEW_FNAME, 
							GOVNEW_LNAME, GOVOLD_PNAME, GOVOLD_FNAME, GOVOLD_LNAME, GOVCHG_DECOLA_MECODE, 
							GOVCHG_MPB_ORDER, GOVCHG_DATE, REMARK, GOVCHG_NAME
							FROM GOVCHGNM a, COMMON_HISTORY b
							WHERE a.ID=b.ID
							ORDER BY a.ID, GOVCHG_ORDER ";
		elseif ($command=='NAME_EMP')
			$cmd = " SELECT a.ID, EMPCHG_ORDER as GOVCHG_ORDER, EMPCHG_REFF as GOVCHG_REFF, EMPNEW_PNAME as GOVNEW_PNAME, 
							EMPNEW_FNAME as GOVNEW_FNAME, EMPNEW_LNAME as GOVNEW_LNAME, EMPOLD_PNAME as GOVOLD_PNAME, 
							EMPOLD_FNAME as GOVOLD_FNAME, EMPOLD_LNAME as GOVOLD_LNAME, EMPCHG_DATE as GOVCHG_DATE, 
							EMPCHG_ME_CODE as , EMPCHG_MPB_ORDER as GOVCHG_MPB_ORDER
							FROM EMPCHGNM a, EMPLOYEE_HISTORY b
							WHERE a.ID=b.ID
							ORDER BY a.ID, EMPCHG_ORDER ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "<br>";
		$MAX_ID = 1;
		while($data = $db_dpis35->get_array()){
			$PER_NAMEHIS++; 
			$PER_ID = trim($data[ID]);
			$GOVCHG_DATE = trim($data[GOVCHG_DATE]);
			$NH_DATE = ($GOVCHG_DATE)? ((substr($GOVCHG_DATE, 4, 4) - 543) ."-". substr($GOVCHG_DATE, 2, 2) ."-". substr($GOVCHG_DATE, 0, 2)) : "";
			$PRE_CODE = $data[GOVOLD_PNAME];
			$PN_CODE = "";
			if ($PRE_CODE > 0) {
				$cmd = " SELECT PRE_NAME, TITLENAME FROM PREFIX WHERE PRE_CODE = '$PRE_CODE' ";
				$db_dpis351->send_cmd($cmd);
				$data1 = $db_dpis351->get_array();
				$PRE_NAME = trim($data1[PRE_NAME]);
				$TITLENAME = trim($data1[TITLENAME]);

				$cmd = " select PN_CODE from PER_PRENAME 
								where PN_NAME = '$PRE_NAME' or PN_SHORTNAME = '$PRE_NAME' or PN_NAME = '$TITLENAME' or PN_SHORTNAME = '$TITLENAME' ";
				$db_dpis2->send_cmd($cmd);
	//			$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PN_CODE = $data2[PN_CODE];
				if ($PRE_NAME=="ว่าที่ พันตรี") $PN_CODE = "213";
				elseif ($PRE_CODE=="0" && $LNAME=="ภิงคะสาร (ม.ล.)") $PN_CODE = "122";
				if (!$PN_CODE) echo "คำนำหน้าชื่อ $PRE_CODE - $PRE_NAME - $TITLENAME<br>";
			}
			if (!$PN_CODE) {
				$cmd = " select PN_CODE from PER_PERSONAL	where PER_ID = $PER_ID ";
				$db_dpis2->send_cmd($cmd);
	//			$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PN_CODE = $data2[PN_CODE];
			}
	
			$NH_NAME = trim($data[GOVOLD_FNAME]);
			if (!$NH_NAME) $NH_NAME = "-";
			$NH_SURNAME = trim($data[GOVOLD_LNAME]);
			if (!$NH_SURNAME) $NH_SURNAME = "-";
			$NH_DOCNO = trim($data[GOVCHG_REFF]);
			$PRE_CODE = $data[GOVNEW_PNAME];
			$PN_CODE_NEW = "";
			if ($PRE_CODE > 0) {
				$cmd = " SELECT PRE_NAME, TITLENAME FROM PREFIX WHERE PRE_CODE = '$PRE_CODE' ";
				$db_dpis351->send_cmd($cmd);
				$data1 = $db_dpis351->get_array();
				$PRE_NAME = trim($data1[PRE_NAME]);
				$TITLENAME = trim($data1[TITLENAME]);

				$cmd = " select PN_CODE from PER_PRENAME 
								where PN_NAME = '$PRE_NAME' or PN_SHORTNAME = '$PRE_NAME' or PN_NAME = '$TITLENAME' or PN_SHORTNAME = '$TITLENAME' ";
				$db_dpis2->send_cmd($cmd);
	//			$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PN_CODE_NEW = $data2[PN_CODE];
				if ($PRE_NAME=="ว่าที่ พันตรี") $PN_CODE_NEW = "213";
				elseif ($PRE_CODE=="0" && $LNAME=="ภิงคะสาร (ม.ล.)") $PN_CODE_NEW = "122";
				if (!$PN_CODE_NEW) echo "คำนำหน้าชื่อ $PRE_CODE - $PRE_NAME - $TITLENAME<br>";
			}
	
			$NH_NAME_NEW = trim($data[GOVNEW_FNAME]);
			$NH_SURNAME_NEW = trim($data[GOVNEW_LNAME]);
			$NH_ORG = trim($data[REGISTER_DEPT]);
			$NH_REMARK = trim($data[REMARK]);
			$NH_BOOK_NO = trim($data[REGISTER_C_NO]);
			$NH_BOOK_DATE = trim($data[REGISTER_DATE]);

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
		} // end while						
		
		$cmd = " select count(PER_ID) as COUNT_NEW from PER_NAMEHIS ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_NAMEHIS - $PER_NAMEHIS - $COUNT_NEW<br>";

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();
		$db_dpis351->free_result();
	} // end if

	if ($command=='DECORATE' || $command=='DECORATE_EMP'){
// เครื่องราชย์ 64942 ok
		$cmd = " truncate table per_decoratehis ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

// เครื่องราชย์ข้าราชการ 45069
		if ($command=='DECORATE') 
			$cmd = " SELECT a.ID, B_YEAR, GOV_C, ME_CODE, GOVDE_REQUEST_DATE, GOVDE_RECIEVE_DATE, GOVDE_RETURN_DATE, 
							  GOVDE_CERT_RECIEVE_DATE, GOVDE_NOTE, GOVDE_DOC_NO, GOVDE_CAUSE, GOVDE_LASTRECIEVE, 
							  GOVDE_REQUEST_STATUS, GOVPOS_ORDER, GOVDE_RETURN_FLAG, GOVDE_RETURN_NO, GOVDE_MONEY, 
							  GOVDE_LEV, WPOS_CODE, MPOS_CODE, TYPE_POS, WPOS_NAME 
							  FROM GOVDECOLA_HISTORY a, COMMON_HISTORY b
							  WHERE a.ID=b.ID AND ME_CODE > 0
							  ORDER BY a.ID, B_YEAR ";
		elseif ($command=='DECORATE_EMP') 
			$cmd = " SELECT a.ID, ME_CODE, EMPPOS_ORDER, EMPDE_REQUEST_DATE, EMPDE_RECIEVE_DATE, EMPDE_RETURN_DATE, 
							  EMPDE_CERT_RECIEVE_DATE, EMPDE_DOC_NO, EMPDE_RETURN_DOC_NO, EMPDE_NOTE, EMPDE_MONEY, 
							  EMPDE_RETURN_FLAG, EMPDE_LASTRECIEVE, EMPDE_REQUEST_STATUS, EMPDE_CASE, EMPDE_POSITION_DATE 
							  FROM EMPDECOLA_HISTORY a, EMPLOYEE_HISTORY b
							  WHERE a.ID=b.ID AND ME_CODE > 0
							  ORDER BY a.ID, EMPPOS_ORDER ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "<br>";
		$MAX_ID = 1;
		while($data = $db_dpis35->get_array()){
			$PER_DECORATEHIS++;    
			$PER_ID = trim($data[ID]);
			$ME_CODE = $data[ME_CODE];
			if ($ME_CODE==1) $DC_CODE = "58";
			elseif ($ME_CODE==2) $DC_CODE = "57";
			elseif ($ME_CODE==3) $DC_CODE = "55";
			elseif ($ME_CODE==4) $DC_CODE = "54";
			elseif ($ME_CODE==5) $DC_CODE = "34";
			elseif ($ME_CODE==6) $DC_CODE = "33";
			elseif ($ME_CODE==7) $DC_CODE = "29";
			elseif ($ME_CODE==8) $DC_CODE = "28";
			elseif ($ME_CODE==9) $DC_CODE = "24";
			elseif ($ME_CODE==10) $DC_CODE = "23";
			elseif ($ME_CODE==11) $DC_CODE = "16";
			elseif ($ME_CODE==12) $DC_CODE = "15";
			elseif ($ME_CODE==13) $DC_CODE = "11";
			elseif ($ME_CODE==14) $DC_CODE = "10";
			elseif ($ME_CODE==15) $DC_CODE = "09";
			elseif ($ME_CODE==16) $DC_CODE = "08";
			elseif ($ME_CODE==90) $DC_CODE = "61";
			elseif ($ME_CODE==91) $DC_CODE = "91";
			elseif ($ME_CODE==92) $DC_CODE = "92";
			elseif ($ME_CODE==95) $DC_CODE = "95";
			elseif ($ME_CODE==97) $DC_CODE = "97";
			elseif ($ME_CODE==98) $DC_CODE = "98";
			$GOVDE_REQUEST_DATE = trim($data[GOVDE_REQUEST_DATE]);
			$DEH_DATE = ($GOVDE_REQUEST_DATE)? ((substr($GOVDE_REQUEST_DATE, 4, 4) - 543) ."-". substr($GOVDE_REQUEST_DATE, 2, 2) ."-". substr($GOVDE_REQUEST_DATE, 0, 2)) : "-";
//			$DEH_GAZETTE = trim($data[ISSUE])." เล่ม ".trim($data[BOOK])." ตอนที่ ".trim($data[PART])." หน้า ".trim($data[PAGE])." ลำดับ ".trim($data[ORDER_DECOR]);
//			$DEH_RECEIVE_DATE = trim($data[RECEIVED_DATE]);
//			$DEH_RETURN_DATE = trim($data[RETURN_DATE]);
			if ($command=='DECORATE') 
				$DEH_POSITION = trim($data[WPOS_NAME]);
			elseif ($command=='DECORATE_EMP') 
				$DEH_POSITION = trim($data[EMPDE_NOTE]);
//			$DEH_ORG = trim($data[JOB_NAME])." ".trim($data[SECTION_NAME])." ".trim($data[DIVISION_NAME])." ".trim($data[DEPARTMENT_NAME]);

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
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_DECORATEHIS ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_DECORATEHIS - $PER_DECORATEHIS - $COUNT_NEW<br>";


		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();
		$db_dpis351->free_result();
	} // end if

	if ($command=='EDUCATE' || $command=='EDUCATE_EMP'){
		$cmd = " truncate table per_educate ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

// ประวัติการศึกษาข้าราชการ 46774
		if ($command=='EDUCATE')
			$cmd = " SELECT a.ID, GOVED_ORDER, INS_CODE, MAJOR_CODE, EDU_LEV_CODE, C_CODE, EDU_F_CODE, 
							GOVED_BEGIN_YEAR, GOVED_END_YEAR, GOVED_TYPE, GOVED_REM, QUA_CODE, GOVED_WORK_QUA, 
							GOVED_HIGH_QUA, GOVED_ENTRY_QUA, GOVED_DET 
							FROM GOVEDU_HISTORY a, COMMON_HISTORY b
							WHERE a.ID=b.ID
							ORDER BY ID, GOVED_ORDER ";
		elseif ($command=='EDUCATE_EMP')
			$cmd = " SELECT a.ID, EMPED_ORDER as GOVED_ORDER, INS_CODE, MAJOR_CODE, EDU_LEV_CODE, C_CODE, EDU_F_CODE, 
							EMPED_BEGIN_YEAR as GOVED_BEGIN_YEAR, EMPED_END_YEAR as GOVED_END_YEAR, EMPED_TYPE as GOVED_TYPE, 
							EMPED_REM as GOVED_REM, QUA_CODE, EMPED_WORKQUA as GOVED_WORK_QUA
							FROM EMPEDU_HISTORY a, EMPLOYEE_HISTORY b
							WHERE a.ID=b.ID
							ORDER BY a.ID, EMPED_ORDER ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "<br>";
		$MAX_ID = 1;
		while($data = $db_dpis35->get_array()){
			$PER_ID = trim($data[ID]);
			$EDU_SEQ = $data[GOVED_ORDER];
			$GOVED_REM = trim($data[GOVED_REM]);
			$QUA_CODE = trim($data[QUA_CODE]);
			$GOVED_WORK_QUA = trim($data[GOVED_WORK_QUA]);
			$GOVED_HIGH_QUA = trim($data[GOVED_HIGH_QUA]);
			$GOVED_ENTRY_QUA = trim($data[GOVED_ENTRY_QUA]);
			$GOVED_DET = trim($data[GOVED_DET]);
			if ($GOVED_DET)
				$EDU_REMARK = $GOVED_DET;
			else
				$EDU_REMARK = $GOVED_REM;
			$MAJOR_CODE = trim($data[MAJOR_CODE]);
			$EM_CODE = $MAJOR_NAME = "";
			if ($MAJOR_CODE && $MAJOR_CODE!='9999') {
				$cmd = " SELECT MAJOR_NAME FROM MAJOR WHERE MAJOR_CODE = '$MAJOR_CODE' ";
				$db_dpis351->send_cmd($cmd);
				$data1 = $db_dpis351->get_array();
				$MAJOR_NAME = trim($data1[MAJOR_NAME]);

				$cmd = " select EM_CODE from PER_EDUCMAJOR where EM_NAME = '$MAJOR_NAME' ";
				$db_dpis2->send_cmd($cmd);
	//			$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$EM_CODE = $data2[EM_CODE];
				if ($MAJOR_NAME=="นโยบายสาธารณะ และการบริหารโครงการ") $EM_CODE = "51051100";
				elseif ($QUA_NAME=="ปวช. ช่างอุตสาหกรรม") $EM_CODE = "80020100";
				elseif ($QUA_NAME=="ปวช (สำรวจ)") $EM_CODE = "80011100";
				elseif ($QUA_NAME=="ปวช. (วิจิตรศิลป์-เขียน)") $EM_CODE = "32000101";
				elseif ($QUA_NAME=="ปวช.(คหกรรมศาสตร์)") $EM_CODE = "57000100";
				elseif ($MAJOR_NAME=="การจัดการภาครัฐและเอกชนมหาบัณฑิต") $EM_CODE = "54008101";
//				elseif ($MAJOR_NAME=="แพทย์ศาสตรบัณฑิต") $EM_CODE = "4010049";
//				elseif ($MAJOR_NAME=="วิทยาศาสตร์มหาบัณฑิต") $EM_CODE = "6010054";
//				elseif ($MAJOR_NAME=="วิทยาศาสตร์บัณฑิต") $EM_CODE = "4010061";
//				elseif ($MAJOR_NAME=="วิทยาศาสตร์บัณฑิต") $EM_CODE = "4010061";
//				elseif ($MAJOR_NAME=="วิทยาศาสตร์บัณฑิต") $EM_CODE = "4010061";
//				elseif ($MAJOR_NAME=="วิทยาศาสตร์บัณฑิต") $EM_CODE = "4010061";
//				elseif ($MAJOR_NAME=="วิทยาศาสตร์บัณฑิต") $EM_CODE = "4010061";
//				if (!$EM_CODE) echo "สาขาวิชาเอก $MAJOR_CODE - $MAJOR_NAME<br>";
			}
	
			$EN_CODE = $QUA_NAME = "";
			if ($QUA_CODE || $GOVED_DET) {
				if ($QUA_CODE != "0" && $QUA_CODE != "9999") {
					$cmd = " SELECT QUA_NAME FROM QUALIFICATION WHERE QUA_CODE = '$QUA_CODE' ";
					$db_dpis351->send_cmd($cmd);
					$data1 = $db_dpis351->get_array();
					$QUA_NAME = trim($data1[QUA_NAME]);
				}
				if (!$QUA_NAME) $QUA_NAME = $GOVED_DET;
				$QUA_NAME = str_replace("  ", " ", $QUA_NAME);
				if ($QUA_NAME=="ครุศาสตร์อุตสาหกรรมบัณฑิต") $EN_CODE = "4010022";
				elseif ($QUA_NAME=="วิทยาศาสตร์บัณฑิต" || $QUA_NAME=="วท.บ.สถิติประยุกต์" || $QUA_NAME=="วท.บ.การวิจัยดำเนินงาน" || $QUA_NAME=="วท.บ.เกษตรกลวิธาน" || 
					$QUA_NAME=="วท.บ.สุขศึกษา" || $QUA_NAME=="วทบ." || $QUA_NAME=="วทบ.(เทคโนโลยีการเกษตร)" || $QUA_NAME=="วทบ.(เศรษฐศาสตร์)" || $QUA_NAME=="วทบ. สังคมวิทยาและมนุษยวิทยา" || 
					$QUA_NAME=="วทบ.(สถิติประยุกต์)" || $QUA_NAME=="วท.บ.(วิทยาการคอมพิวเตอร์)" || $QUA_NAME=="วท.บ.เทคโนโลยี่การเกษตร" || $QUA_NAME=="วท.บ.คอมพิวเตอร์") 
					$EN_CODE = "4010061";
				elseif ($QUA_NAME=="แพทย์ศาสตรบัณฑิต") $EN_CODE = "4010049";
				elseif ($QUA_NAME=="วิทยาศาสตร์มหาบัณฑิต" || $QUA_NAME=="วท.ม.(ธรณีวิทยา)" || $QUA_NAME=="วท.ม.วิทยาศาสตร์") $EN_CODE = "6010054";
				elseif ($QUA_NAME=="พัฒนบริหารศาสตร์") $EN_CODE = "6010027";
				elseif ($QUA_NAME=="ปรัชญาและศาสนา") $EN_CODE = "4010129";
				elseif ($QUA_NAME=="การจัดการภาครัฐและภาคเอกชน") $EN_CODE = "6010002";
				elseif ($QUA_NAME=="ทันตแพทย์ศาสตรบัณฑิต" || $QUA_NAME=="ทบ.(เกียรตินิยมอันดับ 2)") $EN_CODE = "4010030";
				elseif ($QUA_NAME=="สังคมสงเคราะห์ศาสตร์") $EN_CODE = "4010156";
				elseif ($QUA_NAME=="Master of Arts") $EN_CODE = "6029001";
				elseif ($QUA_NAME=="Doctor of Engineering") $EN_CODE = "8010037";
				elseif ($QUA_NAME=="พัฒนาสังคม") $EN_CODE = "6010030";
				elseif ($QUA_NAME=="เทคโนโลยีและสื่อสารการศึกษา") $EN_CODE = "6010016";
				elseif ($QUA_NAME=="เทคโนโลยีชีวภาพ") $EN_CODE = "4010068";
				elseif ($QUA_NAME=="เทคนิคการแพทย์") $EN_CODE = "5010041";
				elseif ($QUA_NAME=="สถิติบัณฑิต") $EN_CODE = "4010153";
				elseif ($QUA_NAME=="วนศาสตร์") $EN_CODE = "4010075";
				elseif ($QUA_NAME=="มนุษยศาสตร์") $EN_CODE = "4010053";
				elseif ($QUA_NAME=="ภูมิสถาปัตยกรรมศาสตรบัณฑิต") $EN_CODE = "4010168";
				elseif (strpos($QUA_NAME,"ปวส") !== false || strpos($QUA_NAME,"ประกาศนียบัตรวิชาชีพชั้นสูง") !== false || $QUA_NAME=="ปว.ส.(ช่างยนต์)" || $QUA_NAME=="ประกาศนียบัตรชั้นสูง ( ก่อสร้าง)" || 
					$QUA_NAME=="ประการศนียบัตรวิชาชีพชั้นสูง (สถาปัตยกรรม)" || $QUA_NAME=="ประกาศนียบัตรชิชาชีพชั้นสูง (ช่างก่อสร้าง)" || $QUA_NAME=="ป.ว.ส.(โยธา)") $EN_CODE = "3010000";
				elseif (strpos($QUA_NAME,"ปวท") !== false || strpos($QUA_NAME,"ประกาศนียบัตรวิชาชีพเทคนิค") !== false) $EN_CODE = "2010000";
				elseif (strpos($QUA_NAME,"ปวช") !== false || strpos($QUA_NAME,"ประกาศนียบัตรวิชาชีพ") !== false || $QUA_NAME=="ปว.ช." || $QUA_NAME=="ปว.ช(ก่อสร้าง)" || 
					$QUA_NAME=="ประการศนียบัตรวิชาชีพ (การก่อสร้าง)" || $QUA_NAME=="ประกาศนียับตรวิชาชีพ" || $QUA_NAME=="ประกาศนียบัตรวิชาขีพ (ข่างก่อสร้าง)" || $QUA_NAME=="ปว.ช.(พณิชยการ)" || 
					$QUA_NAME=="ปวข.(ช่างไม้และก่อสร้าง)" || $QUA_NAME=="ปวข.(ก่อสร้าง)" || $QUA_NAME=="ปววช.(ช่างยนต์)" || $QUA_NAME=="ปวข.สำรวจ") 
					$EN_CODE = "1010000";
				elseif ($QUA_NAME=="ป. 1") $EN_CODE = "0510077";
				elseif ($QUA_NAME=="ป. 2") $EN_CODE = "0510078";
				elseif ($QUA_NAME=="ป. 3") $EN_CODE = "0510079";
				elseif ($QUA_NAME=="ป. 4" || $QUA_NAME=="ปง4" || $QUA_NAME=="ผ.4") $EN_CODE = "0510080";
				elseif ($QUA_NAME=="ป. 5") $EN_CODE = "0510081";
				elseif ($QUA_NAME=="ป. 6" || $QUA_NAME=="แ.6" || $QUA_NAME=="ประถาศึกษาปีที่ 6" || $QUA_NAME=="ประถมศึกษาที่ 6" || $QUA_NAME=="ป..6" || $QUA_NAME=="ประถมศึกษา 6" || 
					$QUA_NAME=="ประถมศึกษาชั้น ป.6") 
					$EN_CODE = "0510082";
				elseif ($QUA_NAME=="ป.7." || $QUA_NAME=="ป7" || $QUA_NAME=="ป 7" || $QUA_NAME=="ป. 7" || $QUA_NAME=="ป .7" || $QUA_NAME=="ปง7") $EN_CODE = "0510083";
				elseif ($QUA_NAME=="ม. 1" || $QUA_NAME=="ม.1(ปัจจุบัน)") $EN_CODE = "0510085";
				elseif ($QUA_NAME=="ม. 2" || $QUA_NAME=="ม.2(ปัจจุบัน)") $EN_CODE = "0510086";
				elseif ($QUA_NAME=="ม. 3" || $QUA_NAME=="ม.3(ปัจจุบัน)" || $QUA_NAME=="แ.3" || $QUA_NAME=="ฒ.3" || $QUA_NAME=="ท.3" || $QUA_NAME=="ม .3" || 
					$QUA_NAME=="ม.3(ปัจจุบัย)" || $QUA_NAME=="ม3") $EN_CODE = "0510087";
				elseif ($QUA_NAME=="ม. 4" || $QUA_NAME=="ม.4(ปัจจุบัน)") $EN_CODE = "0510088";
				elseif ($QUA_NAME=="ม. 5" || $QUA_NAME=="ม.5(ปัจจุบัน)") $EN_CODE = "0510089";
				elseif ($QUA_NAME=="ม. 6" || $QUA_NAME=="มัธยมศึกษาปี่ที่ 6" || $QUA_NAME=="มัธยมศึกษาที่ 6" || $QUA_NAME=="มัธยมศึกษาชั้น ม.6") $EN_CODE = "0510090";
				elseif ($QUA_NAME=="ม.6(ปัจจุบัน)" || $QUA_NAME=="ม.6 (ปัจุบัน)") $EN_CODE = "0510110";
				elseif ($QUA_NAME=="ม. 8") $EN_CODE = "0510092";
				elseif ($QUA_NAME=="ม.ศ. 1" || $QUA_NAME=="มศ.1") $EN_CODE = "0510093";
				elseif ($QUA_NAME=="ม.ศ. 2" || $QUA_NAME=="มศ.2") $EN_CODE = "0510094";
				elseif ($QUA_NAME=="ม.ศ. 3" || $QUA_NAME=="มศ.3" || $QUA_NAME=="มงศ.3" || $QUA_NAME=="ท.ศ.3" || $QUA_NAME=="ม.ศ3" || $QUA_NAME=="มศ. 3") $EN_CODE = "0510095";
				elseif ($QUA_NAME=="ม.ศ. 4" || $QUA_NAME=="มศ.4") $EN_CODE = "0510096";
				elseif ($QUA_NAME=="ม.ศ. 5" || $QUA_NAME=="มศ.5" || $QUA_NAME=="ม.ศ.5." || $QUA_NAME=="ม.ศ 5" || $QUA_NAME=="มศ. 5") $EN_CODE = "0510097";
				elseif ($QUA_NAME=="ม.ศ.5(วิทยาศาสตร์)" || $QUA_NAME=="ม.ศ.5 วิทยาศาสตร์" || $QUA_NAME=="ม.ศ.5 (วิทย์)" || $QUA_NAME=="ม.ศ.5 (แผนกวิทย์)" || $QUA_NAME=="ม.ศ.5(แผนกวิทยาศาสตร์)" || 
					$QUA_NAME=="ม.ศ.5 (แผนกวิทยาศาสตร์)" || $QUA_NAME=="ม.ศ. 5 วิทยาศาสตร์" || $QUA_NAME=="ม.ศ.5(แผนกวิทย์)" || $QUA_NAME=="ม.ศ.5(วิทย์)" || $QUA_NAME=="ม.ศ.5 วิทยฯ" || 
					$QUA_NAME=="ม.ศ.5วิทยาศาสตร์" || $QUA_NAME=="ม.ศ.5 วิทย์" || $QUA_NAME=="ม.ศ.5 (วิทย์๙)" || $QUA_NAME=="ม.ศ. 5 (วิทยาศาสตร์)") $EN_CODE = "0510099";
				elseif ($QUA_NAME=="ประกาศนียบัตรมัธยมศึกษาตอนปลาย(ม.6)" || $QUA_NAME=="ประกาศนียบัตรมัธยศึกษาตอนปลาย (ม.6)" || $QUA_NAME=="มัธยมศึกษาตอนปลาย (ม.6)" || 
					$QUA_NAME=="มัธยมศึกษาตอนปลาย (ม.6ป" || $QUA_NAME=="มัธยมศึกษาตอนปลาย (ม.6)") $EN_CODE = "0510113";
				elseif ($QUA_NAME=="ประกาศนียบัตรประถมศึกษาปีที่6" || $QUA_NAME=="ประกาศนียบัตรประถมศึกษา(ป.6)" || $QUA_NAME=="ประกาศนียบัตรป.6" || 
					$QUA_NAME=="ประกานียบัตร ป.6" || $QUA_NAME=="ประกาศนีบัตร ป.6" || $QUA_NAME=="ประการศนียบัตร ป.6" || $QUA_NAME=="ประกาศนียบัตร ปง6" || $QUA_NAME=="ประกาศนียบัตร ป.๖" || 
					$QUA_NAME=="ประกาศนีบบัตรป.6" || $QUA_NAME=="ประกาศนียบัตรประถมศึกษาปีที่ 6" || $QUA_NAME=="ประกาศนียบัตรประถม 6" || $QUA_NAME=="ประกาศนียบัตร์ ป.6") $EN_CODE = "0510100";
				elseif ($QUA_NAME=="ประกาศนีบัตรประถมศึกษาตอนปลาย(ป.6)" || $QUA_NAME=="ประถมศึกษาตอนปลาย") $EN_CODE = "0510114";
				elseif ($QUA_NAME=="ประกาศนียบัตรมัธยมศึกษาตอนต้น(ม.3)" || $QUA_NAME=="มัธยมศึกษาตอนต้น" || $QUA_NAME=="มัธยมศึกษาตอนต้น (ม.3)" || 
					$QUA_NAME=="ประกาศนียบัติมัธยมศึกษาตอนต้น" || $QUA_NAME=="มัธยมศึกษาที่ 3" || $QUA_NAME=="ประกาศนียบัตรมัธยมศึกษาตอนต้น (ม.3)" || 
					$QUA_NAME=="ประกาศนียบัตรมัะยมศึกษาตอนต้น ม.3" || $QUA_NAME=="ประกาศนียบัตรมัธยมศึกษาปีที่ 3") $EN_CODE = "0510111";
				elseif ($QUA_NAME=="วศ.บ. (โยธา)" || $QUA_NAME=="วศ.บ(โยธา)" || $QUA_NAME=="ว.ศ.บ.(โยธา)" || $QUA_NAME=="วศ.บ (โยธา)" || 
					$QUA_NAME=="วศบ. (โยธา)" || $QUA_NAME=="วศบ.(โยธา)" || $QUA_NAME=="วศ.บ. (โยธา) เกียรตินิยม" || $QUA_NAME=="วิศวกรรมศาสตรบัณฑิต(โยธา)" || 
					$QUA_NAME=="วศ.บ.(โยธา)เกียรตินิยมอันดับ2" || $QUA_NAME=="วศบ.(โยธา) เกียรตินิยมอันดับ2" || $QUA_NAME=="วศ.บ.(โยธา) เกียรตินิยมอันดับ2" || 
					$QUA_NAME=="วศ.บ.โยธา (โครงสร้าง)" || $QUA_NAME=="วศ.บ.โยธา(ขนส่ง)" || $QUA_NAME=="วศ.ษ.(โยธา)" || $QUA_NAME=="วิศวกรรมศาสตรบัณฑิต (วิศวกรรมโยธา)" || 
					$QUA_NAME=="วศ.บ. (โครงสร้าง)" || $QUA_NAME=="วศ.บ.(โครงสร้าง)" || $QUA_NAME=="วศ.บ.วิศวกรรมโครงสร้าง" || $QUA_NAME=="วิศวกรรมโยธา(วศ.บ.)" || 
					$QUA_NAME=="ปริญญาตรี วิศวกรรมศาสตร์") 
					$EN_CODE = "4010109";
				elseif ($QUA_NAME=="ศศ.บ(การจัดการทั่วไป)" || $QUA_NAME=="ศศ.บ.การจัดการทั้วไป" || $QUA_NAME=="ศศ.บ. (การจัดการทั่วไป)" || $QUA_NAME=="ศศบ.(การจัดการทั่วไป)" || 
					$QUA_NAME=="ศิลปศาสตรบัณฑิต(ศศ.บ.),การจัดการทั่วไป" || $QUA_NAME=="ศศ.บ การจัดการทั่วไป" || $QUA_NAME=="ศศ.บ.การจัดการทั่วไป" || $QUA_NAME=="ศิลปศาสตรบัณฑิต (การจัดการทั่วไป)" || 
					$QUA_NAME=="ศศ.บ. (การจัดการงานทั่วไป)") 
					$EN_CODE = "4010145";
				elseif ($QUA_NAME=="วทบ.(เคมี)" || $QUA_NAME=="วท.บ. (เคมี)") $EN_CODE = "4010091";
				elseif ($QUA_NAME=="วทบ.(เทคโนยีอุตสาหกรรม)" || $QUA_NAME=="วท.บ. (เทคโนโลยีอุตสาหกรรม)" || $QUA_NAME=="วท.บ. เทคโนโลยี่อุตสาหกรรม" || $QUA_NAME=="วทบ.(เทคโนโลยีอุตสาหกรรม)" || 
					$QUA_NAME=="วท.บ.เทคโนโลยี่อุตสาหกรรม" || $QUA_NAME=="วท.บ. เทคโนโลยีอุตสาหกรรม (ก่อสร้าง)" || $QUA_NAME=="วท.บ. เทคโนโลยีอุตสาหกรรม" || 
					$QUA_NAME=="วิทยาศาสตรบัณฑิต(วท.บ.)เทคโนโลยีอุตสาหกรรม" || $QUA_NAME=="วท.บ.(เทคโนโลยี่อุตสาหกรรม)" || $QUA_NAME=="วทบ.(เทคโนโลยี่อุตสาหกรรม)" || 
					$QUA_NAME=="วิทยาศาสตร์บัณฑิต(เทคโนโลยีอุตสาหกรรม)" || $QUA_NAME=="วิทยาศาสตรบัณฑิต(วท.บ.),เทคโนโลยีอุตสาหกรรม" || $QUA_NAME=="วท.บ.เทคโนโลยีอุตสาหกรรม" || 
					$QUA_NAME=="วท.บ.เทคโนโลยีอุตสาหกรรม(ก่อสร้าง)" || $QUA_NAME=="วท.บ.เทคโนโลยีอุตสาหกรรมก่อสร้าง" || $QUA_NAME=="วท.บ.(เทคโนโลยี่อุตสาหกรรมและก่อสร้าง)" || 
					$QUA_NAME=="วท.บ.เทคโนโลยี่อุตสาหกรรมเครื่องกล" || $QUA_NAME=="วิทยาศาสตร์และเทคโนโลยี" || $QUA_NAME=="วท.บ.(เทคโนโลยี่อุตสาหกรรม-ก่อสร้าง)" || 
					$QUA_NAME=="เทคโนโลยีอุตสาหกรรม(เทคโนโลยีก่อสร้าง)" || $QUA_NAME=="วทบ.(เทคโนโลยี่อุคสาหกรรม)" || $QUA_NAME=="เทคโนโลยีอุตสาหกรรม (เทคโนโลยีก่อสร้าง)" || $QUA_NAME=="วท.บ.เทคโนโลยีอุตสาหกรรม-ก่อสร้าง") 
					$EN_CODE = "4010063";
				elseif ($QUA_NAME=="วท.บ. (ธรณีวิทยา)" || $QUA_NAME=="วท.บ. ธรณีวิทยา") $EN_CODE = "4010066";
				elseif ($QUA_NAME=="วท.บ (ภูมิศาสตร์)" || $QUA_NAME=="วทบ.(ภูมิศาสตร์)" || $QUA_NAME=="วท.บ. (ภูมิศาสตร์)" || $QUA_NAME=="วท.บ.ภูมิศาสตร์" || $QUA_NAME=="วทบ.ภูมิศาสตร์") 
					$EN_CODE = "4010073";
				elseif ($QUA_NAME=="วท.บ.สถิติ" || $QUA_NAME=="วท.บ. (สถิติ)" || $QUA_NAME=="วท.บ.(สถิติศาสตร์)" || $QUA_NAME=="วทบ.สถิติ") $EN_CODE = "4010096";
				elseif ($QUA_NAME=="วิทยาศาสตรบัณฑิต (คณิตศาสตร์)" || $QUA_NAME=="วท.บ(คณิตศาสตร์)" || $QUA_NAME=="วท.บ. (คณิตศาสตร์)" || $QUA_NAME=="วท.บ.คณิตศาสตร์" || 
					$QUA_NAME=="วทบ.คณิตศาสตร์" || $QUA_NAME=="วทบ. (คณิตศาสตร์)") $EN_CODE = "4010090";
				elseif ($QUA_NAME=="วทบ.(ศึกษาศาสตร์)" || $QUA_NAME=="วท.บ ศึกษาศาสตร์" || $QUA_NAME=="วท.บ.ศึกษาศาสตร์") $EN_CODE = "4010080";
				elseif ($QUA_NAME=="วศ.บ" || $QUA_NAME=="วศบ." || $QUA_NAME=="วิศวกรรมศาสตร์" || $QUA_NAME=="ปริญญาวิศวกรรมศาสตร์บัณฑิต" || $QUA_NAME=="ปริญญาตรี วิศวกรรมศาสตรบัณฑิต" || 
					$QUA_NAME=="วศ.บ.(B.S.C.E)" || $QUA_NAME=="วศ.บ.(เกษตร)") 
					$EN_CODE = "4010103";
				elseif ($QUA_NAME=="บธ.บ. (การจัดการงานก่อสร้าง)" || $QUA_NAME=="บธ.บ.การจัดการงานก่อสร้าง" || $QUA_NAME=="บธ.บ. การจัดการงานก่อสร้าง" || 
					$QUA_NAME=="บธบ.(การจัดการงานก่อสร้าง)" || $QUA_NAME=="บธ.บ (การจัดการงานก่อสร้าง)" || $QUA_NAME=="บธ.บ.(การจัดการก่อสร้าง)" || $QUA_NAME=="บ.ธ.บ.(การจัดการงานก่อสร้าง)" || 
					$QUA_NAME=="บธ.บ(การจัดการงานก่อสร้าง)" || $QUA_NAME=="บ.ธบ.(การจัดการงานก่อสร้าง)" || $QUA_NAME=="บริหารธุรกิจบัณฑิต (การจัดการงานก่อสร้าง)" || 
					$QUA_NAME=="บธบ.การจัดการงานก่อสร้าง" || $QUA_NAME=="บธ.บ การจัดการงานก่อสร้าง" || $QUA_NAME=="ปริญญาตร บริหารธุรกิจบัณฑิต (การจัดการงานก่อสร้าง)") $EN_CODE = "4010026";
				elseif ($QUA_NAME=="ร.บ. (การปกครอง)" || $QUA_NAME=="ร.บ.(ปกครอง)") $EN_CODE = "4010170";
				elseif ($QUA_NAME=="รัฐประศาสนศาสตรมหาบัณฑิต(นโยบายสาธารณะ)" || $QUA_NAME=="สาขาวิชา นโยบายสารธรณะ รป.ม.") $EN_CODE = "6010055";
				elseif ($QUA_NAME=="ศศ.บ.การพัฒนาชุมชน" || $QUA_NAME=="ศิลปศาสตร์บัณฑิต วิชาเอกการพัฒนาชุมชน" || $QUA_NAME=="ศศ.บ.(พัฒนาชุมชน)" || $QUA_NAME=="ศศ.บ. การพัฒนาชุมชน" || 
					$QUA_NAME=="ศศ.บ.(การพัมนาชุมชน)" || $QUA_NAME=="ศศ.บ.พัฒนาชุมชน" || $QUA_NAME=="ศศ.บ. (การพัฒนาชุมชน)") 
					$EN_CODE = "4010173";
				elseif ($QUA_NAME=="นิเทศศาสตร์บัณฑิต" || $QUA_NAME=="นิเทศศาสตร์บัณฑิต สื่อสารมวลชน" || $QUA_NAME=="น.ศ.บ.(ประชาสัมพันธ์)" || $QUA_NAME=="นศ.บ.(ประชาสัมพันธ์)" || 
					$QUA_NAME=="นศ.บ. (โฆษณา)") $EN_CODE = "4010035";
				elseif ($QUA_NAME=="นบ." || $QUA_NAME=="น.บ" || $QUA_NAME=="นิติศาสตร์บัณฑิต" || $QUA_NAME=="นบ.(นิติศาสตร์)") $EN_CODE = "4010034";
				elseif ($QUA_NAME=="พบ.ม.(รัฐประศาสนศาสตร์)" || $QUA_NAME=="พัฒนบริหารศาสตร์มหาบัณฑิต(รัฐประศาสตร์)" || $QUA_NAME=="พบ.ม.รัฐประศาสนศาสตร์" || 
					$QUA_NAME=="พบ.ม.(รัฐประศาสน์ศาสตร์)") $EN_CODE = "6010039";
				elseif ($QUA_NAME=="วศ.บ.วิศวกรรมโยธา" || $QUA_NAME=="วิศวกรรมศาสตร์บัณฑิต (วิศวกรรมโยธา)" || $QUA_NAME=="ป.ตรี (วิศวกรรมโยธา)") $EN_CODE = "4010171";
				elseif ($QUA_NAME=="วิศวกรรมศาสตรบัณฑิต(วศ.บ.วิศวกรรมเครื่องกล)" || $QUA_NAME=="วิศวกรรมศาสตรบัณฑิต(วิศวกรรมเครื่องกล)" || $QUA_NAME=="วศ.บ. (วิศวกรรมเครื่องกล)") $EN_CODE = "4010179";
				elseif ($QUA_NAME=="วศ.บ.(เครื่องกล)" || $QUA_NAME=="วศ.บ (เครื่องกล)" || $QUA_NAME=="วิศวกรรมศาสตรบัณฑิต(วศ.บ.เครื่องกล)" || 
					$QUA_NAME=="วศ.บ.วิศวกรรมเครื่องกล" ||	$QUA_NAME=="วศบ.วิศวกรรมเครื่องกล" || $QUA_NAME=="วศบ.เครื่องกล" || $QUA_NAME=="วศบ.(เครื่องกล)" || $QUA_NAME=="วศ.บ.เครื่องกล") 
					$EN_CODE = "4010169";
				elseif ($QUA_NAME=="รัฐประศาสนศาสตรมหาบัณฑิต(รป.ม.)การจัดการภาครัฐและเอกชน" || $QUA_NAME=="ปริญญาโทรัฐประศาสนศาสตร์ มหาบัณฑิต" || 
					$QUA_NAME=="รป.ม.(รัฐประศาสนศาสตร์)" || $QUA_NAME=="รัฐประศาสนศาสตร์มหาบัณฑิต (เอกการบริหารโครงการและนโยบาย)" || 
					$QUA_NAME=="ปริญญาโท รัฐประศาสนศาสตรมหาบัณฑิต การจัดการสำหรับนักบริหาร" || $QUA_NAME=="ปริญญาโท รับประศาสนศาสตรมหาบัรฑิต" || 
					$QUA_NAME=="รัฐประศาสนศาสตร์มหาบัณฑิต (เอกการบริหารโครงการและนโยบาย)") $EN_CODE = "6010049";
				elseif ($QUA_NAME=="วศ.ม.วิศวกรรมโยธา" || $QUA_NAME=="วิศวกรรมศาสตรมหาบัณฑิต(วิศวกรรมโยธา)") $EN_CODE = "6010084";
				elseif ($QUA_NAME=="นิติศาสตรบัณฑิต(น.บ.)" || $QUA_NAME=="น.บ.(นิติศาสตร์)") $EN_CODE = "4010034";
				elseif ($QUA_NAME=="บธ.บ.บัญชี" || $QUA_NAME=="บธบ.(บัญชี)" || $QUA_NAME=="บธ.บ บัญชี") $EN_CODE = "4010175";
				elseif ($QUA_NAME=="ปริญญาโท การจัดการภาครัฐและภาคเอกชนมหาบัณฑิต") $EN_CODE = "6010002";
				elseif ($QUA_NAME=="ค.บ. (สังคมศึกษา)") $EN_CODE = "4010021";
				elseif ($QUA_NAME=="วศ.ม" || $QUA_NAME=="วิศวกรรมศาสตรมหาบัณฑิต (วศ.ม.)" || $QUA_NAME=="วศ.ม.(ปฐพีกลศาสตร์)" || $QUA_NAME=="วศ.ม.(ขนส่ง)" || 
					$QUA_NAME=="วศ.ม.(วิศวกรรมและการบริหารการก่อสราง)" || $QUA_NAME=="วศ.ม.การบริหารงานก่อสร้าง" || $QUA_NAME=="วิศวกรรมศาสตรมหาบัณฑิต (วิศวกรรมการจัดการอุตสาหกรรม)" || 
					$QUA_NAME=="วศ.ม. (วิศวกรรมโครสร้างพื้นฐานและการบริหาร)" || $QUA_NAME=="วิศวกรรมศาสตรมหาบัณฑิต(การบริหารงานก่อสร้างและสาธารณูปโภค" || $QUA_NAME=="วศ.ม.การบริหารงานก่อสร้าง" || 
					$QUA_NAME=="วศ.ม. (การจัดการงานวิศวกรรม)") 
					$EN_CODE = "6010083";
				elseif ($QUA_NAME=="วศ.บ. (ขนส่ง)" || $QUA_NAME=="วศ.บ.(ขนส่ง)(เกียรตินิยมอันดับ" || $QUA_NAME=="วศ.บ.(ขนส่ง)(เกียรนิยม)" || $QUA_NAME=="วศ.บ. (การขนส่ง)") $EN_CODE = "4010172";
				elseif ($QUA_NAME=="ศิลปศาสตรมหาบัณฑิต ศศ.ม." || $QUA_NAME=="ศศ.ม. รัฐศาสตร์" || $QUA_NAME=="ศิลปศาสตรมหาบัณฑิต(รัฐศาสตร์)" || 
					$QUA_NAME=="ปริญญาโท ศิลปศาสตรมหาบัณฑิต ยุทธศาสตร์การพัฒนา") $EN_CODE = "6010089";
				elseif ($QUA_NAME=="บธ.บ. (การบริหารทั่วไป)") $EN_CODE = "4010176";
				elseif ($QUA_NAME=="วศ.ม(โยธา)") $EN_CODE = "6010117";
				elseif ($QUA_NAME=="ร.บ. (รัฐประศาสนศาสตร์)") $EN_CODE = "4010177";
				elseif ($QUA_NAME=="ร.ม. (การปกครอง)" || $QUA_NAME=="ร.ม.(ปกครอง)") $EN_CODE = "6010118";
				elseif ($QUA_NAME=="พบ.ม" || $QUA_NAME=="พบ.ม.คอมพิวเตอร์") $EN_CODE = "6010027";
				elseif ($QUA_NAME=="คอบ. (โยธา)" || $QUA_NAME=="คอบ.วิศวกรรมโยธา" || $QUA_NAME=="คอบ.(โยธา)" || $QUA_NAME=="คอ.บ.วิศวกรรมโยธา-ก่อสร้าง" || $QUA_NAME=="คอ.บ.โยธา" || 
					$QUA_NAME=="ค.อ.บ.(วิศวกรรมโยธา)" || $QUA_NAME=="คอ.บ.วิศวกรรมโยธา") 
					$EN_CODE = "4010178";
				elseif ($QUA_NAME=="ศิลปศาสตรมหาบัณฑิต(นโยบายและการวางแผนสังคม)") $EN_CODE = "6010095";
				elseif ($QUA_NAME=="ประกาศนียบัตรผดุงครรภ์") $EN_CODE = "2010029";
				elseif ($QUA_NAME=="ประกาศนียบัตรพยาบาลผดุงครรภ์" || $QUA_NAME=="ประกาศนียบัตรพยาบาลและผดุงครรภ์") $EN_CODE = "2010036";
				elseif ($QUA_NAME=="ประกาศนียบัตรผู้ช่วยพยาบาล" || $QUA_NAME=="ผู้ช่วยพยาบาล") $EN_CODE = "0510030";
				elseif ($QUA_NAME=="ประกาศนียบัตรครูเทคนิคชั้นสูง" || $QUA_NAME=="ปทส.(ประกาศนียบัตรครูเทคนิคชั้นสูง)" || $QUA_NAME=="ประกาศนียบัตรครูเทคนิคชั้นสูง (ปทส.)" || 
					$QUA_NAME=="ประกาศนียบัตรครูเทคนิคชั้นสูง (ปทส.) เครื่องมือกล" || $QUA_NAME=="ประกาศนียบัตรครูเทคนิคชั้นสูง (โยธา)" || $QUA_NAME=="ประกาศนียบัตรครูเทคนิคชั้นสูง(ช่างโยธา)" || 
					$QUA_NAME=="ปทส.ครุศาสตร์อุตสาหกรรมโยธา" || $QUA_NAME=="ปทส.ช่างโยธา") 
					$EN_CODE = "4010001";
				elseif ($QUA_NAME=="ประกาศนียบัตรพยาบาลศาสตร์และผดุงครรภ์ชั้นสูง" || $QUA_NAME=="ประกาศนียบัตรพยาบาลศาสตร์และผดุงครรภ์ขั้นสูง") $EN_CODE = "4010009";
				elseif ($QUA_NAME=="ประกาศนียบัตรวิชารังสีเทคนิค" || $QUA_NAME=="ประกาศนียบัตรรังสีเทคนิค") $EN_CODE = "2010060";
				elseif ($QUA_NAME=="ประกาศนียบัตร ม.6(ปัจจุบัน)") $EN_CODE = "0510108";
				elseif ($QUA_NAME=="ประกาศนียบัตรการชลประทาน") $EN_CODE = "3010016";
				elseif ($QUA_NAME=="ป.ตรี" || $QUA_NAME=="ปริญญาตรีในประเทศ") $EN_CODE = "4010000";
				elseif ($QUA_NAME=="บช.บ (บัญชี)" || $QUA_NAME=="บช.บ.(บัญชี)" || $QUA_NAME=="บช.บ" || $QUA_NAME=="บช.บ. (บัญชี)" || $QUA_NAME=="บช.บ.บัญชี" || 
					$QUA_NAME=="บชบ. การบัญชี" || $QUA_NAME=="บช.บ. (อุตสาหกรรมการบริการ)" || $QUA_NAME=="บชบ. (บริหารบุคคล)" || $QUA_NAME=="ป.ตรี (บัญชีบัณฑิต)" || $QUA_NAME=="บช.บ(บัญชี)" || 
					$QUA_NAME=="บ.ช.บ. (บัญชี)" || $QUA_NAME=="บ.บ.") $EN_CODE = "4010044";
				elseif ($QUA_NAME=="อศศ." || $QUA_NAME=="อ.ศศ." || $QUA_NAME=="อนุปริญญา(ศิลปศาสตร์)" || $QUA_NAME=="อศ.ศ.(บริหารธุรกิจ)" || $QUA_NAME=="อ.ศศ.บริหารธุรกิจ" || 
					$QUA_NAME=="อศ.ศ.บริหารธุรกิจ" || $QUA_NAME=="อศศ.การบริหารธุรกิจ" || $QUA_NAME=="อ.ศ.ศ.(การบริหารธุรกิจ)" || $QUA_NAME=="อศ.ศ.(การบริหารธุรกิจ)" || 
					$QUA_NAME=="อ.ศศ.การบริหารธุรกิจ" || $QUA_NAME=="อศ.ศ การบริหารธุรกิจ" || $QUA_NAME=="อศ.ศ (การบริหารธุรกิจ)" || $QUA_NAME=="อศ.ศ.การบริหารธุรกิจ" || 
					$QUA_NAME=="อศ.ศ(การบริหารธุรกิจ)" || $QUA_NAME=="อศ.ศ บริหารธุรกิจและการจัดการ" || $QUA_NAME=="อ.ศ.ศ.(บริหารธุรกิจและการจัดการทั่วไป)" || $QUA_NAME=="อศ.ศ(การจัดการทั่วไป)" || 
					$QUA_NAME=="'อศศ.การจัดการทั่วไป" || $QUA_NAME=="อศ.ศ.(สังคมศาสตร์)" || $QUA_NAME=="อ.ศศ.(การจัดการทั่วไป)" || $QUA_NAME=="อ.ศศ.(บริหารธุรกิจและการจัดการ)" || 
					$QUA_NAME=="อ.ศศ.(การบริหารธุรกิจ)" || $QUA_NAME=="อ.ศศ.(บริหารธุรกิจ)" || $QUA_NAME=="อนุปริญญา ศิลปศาสตร์ (การบริหารธุรกิจ)" || $QUA_NAME=="อนุปริญญาศิลปศาสตร์ (บริหารธุรกิจ)" || 
					$QUA_NAME=="อนุปริญญาศิลปศาสตร์ การบริหารธุรกิจ" || $QUA_NAME=="อ.ศ.ศ. (บริหารธุรกิจและการจัดการ)" || $QUA_NAME=="อนุปริญญาศิลปศาสตร์(การจัดการทั่วไป)" || 
					$QUA_NAME=="อนุปริญญาศิลปศาสตร์(การจัดการ)" || $QUA_NAME=="อนุปริญญา (ศิลปศาสตร์)" || $QUA_NAME=="อนุปริญญาศิลปศาสตร์ (เศรษฐศาสตรสหกรณ์)" || 
					$QUA_NAME=="อนุปริญญาศิลปศาสตร์ (การจัดการทั่วไป)" || $QUA_NAME=="อนุปริญญาศิลปศาสตร์(การบริหาร)" || $QUA_NAME=="อนุปริญญาศิลปศาสตร์ (การจัดการทั่วไป )" || 
					$QUA_NAME=="อ.ศศ.การบริหารธุรกิจและการจัดการ" || $QUA_NAME=="อนุปริญญาศิลปศาสตร์การจัดการ" || $QUA_NAME=="อนุปริญญาศิลปศาตร์ (การจัดการทั่วไป)" || $QUA_NAME=="อศศ. (การบริหารธุรกิจ)" || 
					$QUA_NAME=="อนุปริญญาศิลปศาสตร์ (การบริหารธุรกิจ)" || $QUA_NAME=="อนุปริญญา (อศ.ศ.)การจัดการทั่วไป" || $QUA_NAME=="อศศ.(การบริหารธุรกิจ)" || $QUA_NAME=="อ.ศ.ศ.(การจัดการทั่วไป)" || 
					$QUA_NAME=="อศ.ศ.(การจัดการสำนักงาน)" || $QUA_NAME=="อนุปริญญาศิลปศาสตร์(การบริหารธุรกิจ)" || $QUA_NAME=="อศ.ศ.การบริหารธุรกิจและการจัดการ" || 
					$QUA_NAME=="อนุปริญญาศิลปศาสตร์(อ.ศศ.การบริหารธุรกิจ)" || $QUA_NAME=="อนุปริญญาศิลปศาสตร (การบริหารธุรกิจ)" || $QUA_NAME=="อ.ศศ.ภาษาอังกฤษธุรกิจ" || 
					$QUA_NAME=="อ.ศศ.การบริหารธุรกิจ(การตลาด)" || $QUA_NAME=="อ.ศศ.การจัดการทั่วไป" || $QUA_NAME=="อนุปริญญาศิลปศาสตร์ สาขาวิชาศิลปศาสตร์" || 
					$QUA_NAME=="อนุปริญญาศิลปศาสตร(อศ.ศ.บริหารธุรกิจ)" || $QUA_NAME=="อศ.ศ.(วิทยาการจัดการ)" || $QUA_NAME=="อ.ศศ.การจัดการสำนักงาน" || $QUA_NAME=="อศ.ศ.(การจัดการทั่วไป)" || 
					$QUA_NAME=="อศ.ศ.(ศิลปะศาสตร์)" || $QUA_NAME=="อศศ.บริหารธุรกิจ" || $QUA_NAME=="อศ.ศ.(เศรษฐศาสตร์สหกรณ์)" || $QUA_NAME=="อศ.ศ. การบริหารธุรกิจ" || 
					$QUA_NAME=="อศ.ศ(บริหารธุรกิจ)" || $QUA_NAME=="อศศ.(การจัดการทั่วไป)" || $QUA_NAME=="อศ.ศ.(การพัฒนาชุมชน)" || $QUA_NAME=="อศศ.เศรษฐศาสตร์สหกรณ์" || 
					$QUA_NAME=="อศศ.การจัดการทั่วไป" || $QUA_NAME=="อศ.ศ.(บริหารธุรกิจและการจัดการ)" || $QUA_NAME=="อ.ศศ.การบัญชี" || $QUA_NAME=="อ.ศศ.วารสาร" || 
					$QUA_NAME=="อ.ศศ.บริหารธุรกิจและการจัดการ" || $QUA_NAME=="อ.ศ.ศ.การบริหารธุรกิจ" || $QUA_NAME=="อ.ศศ. (การบริหารธุรกิจ)" || $QUA_NAME=="อ.ศ.ศ. (บริหารธุรกิจ)" || 
					$QUA_NAME=="อ.ศศ.การพัฒนาชุมชน" || $QUA_NAME=="อนุปริญญาศิลปศาสตร์" || $QUA_NAME=="อนุปริญญา (การจัดการทั่วไป)" || $QUA_NAME=="อ.ศศ.(การพัฒนาชุมชน)" || 
					$QUA_NAME=="อ.ศศ.การเงินและการธนาคาร" || $QUA_NAME=="อ.ศศ.การบิรหารธุรกิจ" || $QUA_NAME=="อ.ศศ.การบริหารงานทั่วไป" || $QUA_NAME=="อศ.ศ. (การบริหารธุรกิจ)" || 
					$QUA_NAME=="อนุปริญญา (การพัฒนาชุมชน)" || $QUA_NAME=="อ.ศศ.วารสารศาสตร์และการประชาสัมพันธ์" || $QUA_NAME=="อ.ศศ.บริหารธุรกิจ และการจัดการ" || 
					$QUA_NAME=="อ.ศศ.(การจัดการสำนักงาน)" || $QUA_NAME=="อ.ศศ.(วารสารศาสตร์)" || $QUA_NAME=="อ.ศศ.(เศรษฐศาสตร์สหกรณ์)" || $QUA_NAME=="อนุศิลป์บัณฑิต มัณฑณศิลป์" || 
					$QUA_NAME=="อ.ศศ. การจัดการทั่วไป" || $QUA_NAME=="อนุปริญญา ศศ.บ. บริหารธุรกิจ" || $QUA_NAME=="อ.ศศ.ศิลปะการสื่อสารและโฆษณา" || $QUA_NAME=="อ.ศศ.งานสำนักงาน" || $QUA_NAME=="อนุปริญญาศิลปศาสตร์" || $QUA_NAME=="อนุปริญญาศิลปศาสตร์") 
					$EN_CODE = "2010080";
				elseif ($QUA_NAME=="เนติบัณฑิต" || $QUA_NAME=="เนติบัณฑิต (นบท.)" || $QUA_NAME=="นบท." || $QUA_NAME=="สอบไล่ได้ความรู้ชั้นเนติบัณฑิต (น.บ.ท.) เนติบัณฑิตไทย" || 
					$QUA_NAME=="สอบไล่ได้ความรู้ชั้นเนติบัณฑิต สมัยที่ 66 ปีการศึกษา 2556" || $QUA_NAME=="ประกาศนียบัตรเนติบัณฑิต") $EN_CODE = "0010001";
				elseif ($QUA_NAME=="ค.บ. อุตสาหกรรมศิลป์" || $QUA_NAME=="ค.บ.อุตสาหกรรมศิลป์" || $QUA_NAME=="คบ.อุตสาหกรรมศิลป์" || $QUA_NAME=="คบ.(อุตสาหกรรมศิลป์)" || 
					$QUA_NAME=="คบ. อุตสาหกรรมศิลป์" || $QUA_NAME=="ค.บ. (อุตสาหกรรมศิลป์)" || $QUA_NAME=="คบ.(อุตสาหกรรมศิลป)" || $QUA_NAME=="คบ.(อุตสาหกรรมศิลป์ สาขาก่อ๙)" || 
					$QUA_NAME=="ครุศาสตรบัณฑิต(คบ.),อุตสาหกรรมศิลป์" || $QUA_NAME=="คบ.(อุตสาหกรรมศิลป์ สาขาช่างโลหะ)") $EN_CODE = "4010086";
				elseif ($QUA_NAME=="ศศบ." || $QUA_NAME=="ศศ.บ.เศรษฐศาสตร์สหกรณ์" || $QUA_NAME=="ศศ.บ.บริหารรัฐกิจ" || $QUA_NAME=="ศศ.บ.นิเทศศาสตร์" || $QUA_NAME=="ศศ.บ.บรรณารักษ์" || 
					$QUA_NAME=="ศศ.บ.(บัญชี)" || $QUA_NAME=="ศศ.บ.(บรรณารักษศาสตร์)" || $QUA_NAME=="ศศ.บ. (เศรษฐศาสตร์)" || $QUA_NAME=="ศศ.บ.(บริหารธุรกิจ-การตลาด)" || 
					$QUA_NAME=="ศศ.บ.(เศรษฐศาสตร์สหกรณ์)" || $QUA_NAME=="ศศ.บ. (สถิติ)" || $QUA_NAME=="ศศ.บ.(บริหารธุรกิจและการจัดการ)" || $QUA_NAME=="ศศ.บ.(บริหารการศึกษา)" || 
					$QUA_NAME=="ศศ.บ.มนุษย์ศาสตร์" || $QUA_NAME=="ศศ.บ. (เศรษฐศาสตร์ธุรกิจ)" || $QUA_NAME=="ศศ.บ.นิเทศศาสตร์(การประชาสัมพันธ์)" || $QUA_NAME=="ศศ.บ. (ประวัติศาสตร์)" || 
					$QUA_NAME=="ศศ.บ.(การบริหารธุรกิจและการจัดการ)" || $QUA_NAME=="ศศ.บ.(บริหารธุรกิจ)" || $QUA_NAME=="ศศ.บ. เศรษฐศาสตร์สหกรณ์" || $QUA_NAME=="ศศ.บ.(มนุษย์ศาสตร์)" || 
					$QUA_NAME=="ศศบ.(เศรษฐศาสตร์สหกรณ์)" || $QUA_NAME=="ศศบ.(เศรษฐศาสตร์การเกษตร)") 
					$EN_CODE = "4010123";
				elseif ($QUA_NAME=="ศศ.บ.รัฐศาสตร์" || $QUA_NAME=="ศศบ.รัฐศาสตร์" || $QUA_NAME=="ศศบ.(รัฐศาสตร์)" || $QUA_NAME=="ศศบ. (รัฐศาสตร์)" || $QUA_NAME=="ศศ.บ. (รัฐศาสตร์)") 
					$EN_CODE = "4010140";
				elseif ($QUA_NAME=="ปก.ศ.(สูง)" || $QUA_NAME=="ปก.ศ.สูง" || $QUA_NAME=="ปกศ.สูง" || $QUA_NAME=="ปก.ศ. สูง" || $QUA_NAME=="ปกศ.สูง(สังคม)" || $QUA_NAME=="ปกศ.สูง(คณิตศาสตร์)" || 
					$QUA_NAME=="ปกศ.สูง สังคมศึกษา" || $QUA_NAME=="ปก.ศ.สูง(ภาษาไทย)" || $QUA_NAME=="ปกศ.สูง การศึกษาวิทย์" || $QUA_NAME=="ปกศ สูง (ภาษาไทย)" || $QUA_NAME=="ปกศ. สูง (สังคม)" || 
					$QUA_NAME=="ปกศ.สูง อุตสาหกรรมศิลป์" || $QUA_NAME=="ปกศ.สูง(อังกฤษ)" || $QUA_NAME=="ปกศ.สูง ภาษาไทย" || $QUA_NAME=="ปกศ.สูง (มัธยม)" || $QUA_NAME=="ปกศ.สูง(ภาษาอังกฤษ)" || 
					$QUA_NAME=="ปกศ.สูง ภาษาอังกฤษ" || $QUA_NAME=="ปกศ.สูง (เอกสหกรณ์)" || $QUA_NAME=="ปกศ.สูง(อุตสาหกรรมศิลป์)" || $QUA_NAME=="ปกศ.ชั้นสูง(คณิตศาสตร์)" || 
					$QUA_NAME=="ปกศ.สูง(สังคมศึกษา)" || $QUA_NAME=="ป.กศ.สูง(คณิตศาสตร์)" || $QUA_NAME=="ป.กศ.สูง(สังคมศาสตร์)" || $QUA_NAME=="ปกศ.(สูง)" || $QUA_NAME=="ปวศ.สูง (เอกพัฒนาชุมชน)") $EN_CODE = "2010043";
				elseif ($QUA_NAME=="ปก.ศ." || $QUA_NAME=="ปก.ศ. ต้น" || $QUA_NAME=="ปกศ." || $QUA_NAME=="ปกศ.(ภาษาไทย)" || $QUA_NAME=="ปกศ.ต้น" || $QUA_NAME=="ป.กศ.") 
					$EN_CODE = "0510050";
				elseif ($QUA_NAME=="อนุปริญญาวิทยาศาสตร์ (อ.วท.) ก่อสร้าง" || $QUA_NAME=="อ.วท.ก่อสร้าง" || $QUA_NAME=="อวท." || $QUA_NAME=="อว.ท.ช่างก่อสร้าง" || $QUA_NAME=="อว.ท.ก่อสร้าง" || 
					$QUA_NAME=="อว.ท.(ก่อสร้าง)" || $QUA_NAME=="อ.วท.คอมพิวเตอร์" || $QUA_NAME=="อว.ท.บริหารธุรกิจ" || $QUA_NAME=="อวท.(ก่อสร้าง)" || $QUA_NAME=="อนุปริญญาวิทยาศาสตร์" || 
					$QUA_NAME=="อนุปริญญาวิทยาศาสตร์(ก่อสร้าง)" || $QUA_NAME=="อนุปริญญาวิทยาศาสตร์(เทคโนโลยีอุตสาหกรรม)" || $QUA_NAME=="อวท.(วิทยาศาสตร์)" || 
					$QUA_NAME=="อนุปริญญาวิทยาศาสตร์(เกษตรศาสตร์)" || $QUA_NAME=="อนุปริญญาวิทยาศาสตร์ (โปรแกรมวิชาก่อสร้าง)" || $QUA_NAME=="อนุปริญญาวิทยาศาสตร์(เครื่องกล)" || 
					$QUA_NAME=="อ.วท.เครื่องกล" || $QUA_NAME=="อว.ท.(เครื่องกล)" || $QUA_NAME=="อนุปริญญา(ก่อสร้าง)" || $QUA_NAME=="อนุปริญญา (ก่อสร้าง)" || 
					$QUA_NAME=="อ.วท.วิทยาศาสตร์" || $QUA_NAME=="อนุปริญญาสาขาวิทยาศาสตร์ (ก่อสร้าง)" || $QUA_NAME=="อวท.ก่อสร้าง" || $QUA_NAME=="อนุปริญญา(การจัดการทั่วไป)" || 
					$QUA_NAME=="อนุปริญญาวิทยาศาสตร์ โปรแกรมวิชาก่อสร้าง" || $QUA_NAME=="อนุปริญญ(การจัดการทั่วไป)" || $QUA_NAME=="อนุปริญญาวิทยาศาสตร์ (อ.วท.)" || 
					$QUA_NAME=="อนุปริญญาวิทยาศาสตร์ สาขาก่อสร้าง" || $QUA_NAME=="อนุปริญญาวิทยาศาสตร์ (อ.ว.ท.)ช่างก่อสร้าง" || $QUA_NAME=="อนุปริญญาวิทยาศาสตร์ (ก่อสร้าง)" || 
					$QUA_NAME=="อ.วท.(ช่างก่อสร้าง)" || $QUA_NAME=="อนุปริญญาวิทยาศาสตร์(เทคโนโลยี่อุตสาหกรรม)" || $QUA_NAME=="อ.วท.(ก่อสร้าง)" || $QUA_NAME=="อว.ท.(คอมพิวเตอร์)" || 
					$QUA_NAME=="อ.ว.ท.(ก่อสร้าง)" || $QUA_NAME=="อ.วท. (ก่อสร้าง)" || $QUA_NAME=="อ.วท.(เครื่องกล)" || $QUA_NAME=="อ.วท.การก่อสร้าง" || $QUA_NAME=="อนุปริญญา วิทยาศาสตร์(ก่อสร้าง)" || 
					$QUA_NAME=="อนุปริญญาวิทยาศาสตร์(คอมพิวเตอร์)" || $QUA_NAME=="อ.วท.(ไฟฟ้า)" || $QUA_NAME=="อว.ท." || $QUA_NAME=="อนุปริญญา(เครื่องกล)" || $QUA_NAME=="อว.ท (ก่อสร้าง)" || 
					$QUA_NAME=="อนุปริญญาวิทยาศาสตร์ คอมพิวเตอร์" || $QUA_NAME=="อ.วท.ช่างก่อสร้าง" || $QUA_NAME=="อนุปริญญา (อ.วท.) อิเล็กทรอนิกส์" || $QUA_NAME=="อนุปริญญาวิทยาศาสตร์" || 
					$QUA_NAME=="อว.ท ก่อสร้าง" || $QUA_NAME=="อวท.เทคโนโลยี่อุตสาหกรรม" || $QUA_NAME=="อ.ว.ท.ก่อสร้าง" || $QUA_NAME=="อนุปริญญาวิทยาศาสตร์ โปรแกรมวิชาก่อสร้าง" || 
					$QUA_NAME=="อนุปริญญาวิทยาศาสตร์ (โปรแกรมวิชาก่อสร้าง)" || $QUA_NAME=="อ.วท.อิเล็กทรอนิกส์" || 
					$QUA_NAME=="อนุปริญญาวิทยาศาสตร์" || $QUA_NAME=="อนุปริญญาวิทยาศาสตร์" || $QUA_NAME=="อนุปริญญาวิทยาศาสตร์") 
					$EN_CODE = "2010079";
				elseif ($QUA_NAME=="อนุปริญญา (การโรงแรมและการท่องเที่ยว)" || $QUA_NAME=="อนุปริญญา (คอมพิวเตอร์ธุรกิจ)" || $QUA_NAME=="อนุปริญญา(การจัดการทั่วๆไป)" || 
					$QUA_NAME=="อนุปริญญา (การบริหารธุรกิจ)") $EN_CODE = "3010032";
				elseif ($QUA_NAME=="วิทยาศาสตรบัณฑิต(วท.บ.เทคโนโลยีก่อสร้าง)" || $QUA_NAME=="วท.บ. เทคโนโลยี่ก่อสร้าง" || $QUA_NAME=="วท.บ.(เทคโนโลยี่ก่อสร้าง)" || 
					$QUA_NAME=="วิทยาศาสตรบัณฑิต (เทคโนโลยีการก่อสร้าง)" || $QUA_NAME=="วท.บ.เทคโนโลยี่ก่อสร้าง" || $QUA_NAME=="วท.บ.(เทคโนโลยี่อุตสาหกรรมแขนงก่อสร้าง)" || 
					$QUA_NAME=="วิทยาศาสตรบัณฑิต(เทคโนโลยีก่อสร้าง)" || $QUA_NAME=="ปริญญาตรี วิทยาศาสตรบัณฑิต เทคโนโลยีอุตสาหกรรม(เทคโนโลยีก่อสร้าง)") $EN_CODE = "4010076";
				elseif ($QUA_NAME=="บธ.บ.การบัญชี") $EN_CODE = "4010041";
				elseif ($QUA_NAME=="นิเทศศาสตร์มหาบัณฑิต(นิเทศศาสตรพัฒนาการ)") $EN_CODE = "6010020";
				elseif ($QUA_NAME=="อักษรศาสตรมหาบัณฑิต (อ.ม.)วรรณคดีเปรียบเทียบ") $EN_CODE = "6010115";
				elseif ($QUA_NAME=="ค.ม.สุขศึกษา" || $QUA_NAME=="ครุศาสตร์มหาบัณฑิต(วิจัยการศึกษา)") $EN_CODE = "6010010";
				elseif ($QUA_NAME=="ประกาศนียบัตรนักเรียนจ่าทหารเรือ" || $QUA_NAME=="นักเรียนจ่าทหารเรือ") $EN_CODE = "0010035";
				elseif ($QUA_NAME=="วท.บ.ฟิสิกส์") $EN_CODE = "4010095";
				elseif ($QUA_NAME=="ค.บ.คณิตศาสตร์" || $QUA_NAME=="ค.บ.พลศึกษา" || $QUA_NAME=="คบ.ช่างอุตสาหกรรม" || $QUA_NAME=="คบ.(ก่อสร้าง)" || $QUA_NAME=="คบ.(สังคม)" || 
					$QUA_NAME=="คบ.(การบริหารโรงเรียน)" || $QUA_NAME=="คบ.สังคม" || $QUA_NAME=="คบ.(ก่อสร้าง)" || $QUA_NAME=="คบ.การบริหารโรงเรียน" || $QUA_NAME=="คบ. บริหารการศึกษา" || 
					$QUA_NAME=="คบ.(สังคมศาสตร์)" || $QUA_NAME=="ค.บ.ช่างอุตสาหกรรม" || $QUA_NAME=="ค.บ. การบริหารการศึกษา" || $QUA_NAME=="ครุศาสตร์(คบ.),เกษตรศาสตร์ทั่วไป" || 
					$QUA_NAME=="ค.บ. การพัฒนาชุมชน" || $QUA_NAME=="ค.บ.(ช่างอุตสาหกรรม)" || $QUA_NAME=="ค.บ. ช่างอุตสาหกรรม" || $QUA_NAME=="คบ.(การบริหารการศึกษา)" || 
					$QUA_NAME=="ค.บ.สังคมศึกษา" || $QUA_NAME=="คบ.(อังกฤษ)" || $QUA_NAME=="ค.บ.ภาษาไทย" || $QUA_NAME=="ค.บ.(บรรณารักษ์ศาสตร์)" || $QUA_NAME=="คบ. (สุขศึกษา)" || 
					$QUA_NAME=="ค.บ.(เทคโนโลยี่ทางการศึกษา)" || $QUA_NAME=="คบ.การบริหารการศึกษา" || $QUA_NAME=="คบ.(อุตสาหกรรม)" || $QUA_NAME=="ค.บ.สังคมศาสตร์" || 
					$QUA_NAME=="คบ.(อุตสาหกรรมช่างยนต์)" || $QUA_NAME=="คบ.(ไฟฟ้าอิเล็คทรอนิคส์)" || $QUA_NAME=="ครุศาสตร์บัณฑิต" || $QUA_NAME=="ค.บ.บริหารการศึกษา" || 
					$QUA_NAME=="ค.บ.การจัดการทั่วไป" || $QUA_NAME=="ครุศาสตรบัณฑิต(ค.บ.),สังคมศาสตร์" || $QUA_NAME=="ค.บ.การบริหารการศึกษา" || $QUA_NAME=="ค.บ.(เกษตรศาสตร์)" || 
					$QUA_NAME=="ค.บ.ก่อสร้าง") 
					$EN_CODE = "4010018";
				elseif ($QUA_NAME=="ศศ.บ.ภาษาอังกฤษ" || $QUA_NAME=="ศศ.บ.(อังกฤษ)" || $QUA_NAME=="ศิลปศาสตรบัณฑิต(ภาษาอังกฤษธุรกิจ)" || $QUA_NAME=="ศศ.บ.อังกฤษ") $EN_CODE = "4010133";
				elseif ($QUA_NAME=="วท.บ.(สาธารณสุขศาสตร)(สุขาภิบาล)" || $QUA_NAME=="วท.บ.สาธารณสุขศาสตร์") $EN_CODE = "4010085";
				elseif ($QUA_NAME=="บธ.ม. บริหารธุรกิจ") $EN_CODE = "6010021";
				elseif ($QUA_NAME=="กศ.บ.อังกฤษ" || $QUA_NAME=="กศ.บ.สังคมศึกษา" || $QUA_NAME=="กศ.บ.สุขศึกษา" || $QUA_NAME=="กศ.บ.(อังกฤษ)" || $QUA_NAME=="กศ.บ.(คณิตศาสตร์)" || 
					$QUA_NAME=="กศ.บ.(สังคมศึกษา)" || $QUA_NAME=="กศบ. ภาษาอังกฤษ" || $QUA_NAME=="การศึกษาศาสตรบัณฑิต(กศ.บ.)สังคมศาสตร์" || $QUA_NAME=="กศ.บ.(พลศึกษา)" || 
					$QUA_NAME=="กศบ.(เทคโนโลยี่ทางการศึกษา)" || $QUA_NAME=="กศ.บ.เทคโนโลยี่ทางการศึกษา" || $QUA_NAME=="กศ.บ.(ภาษาไทย)" || $QUA_NAME=="กศ.บ.(ศิลปศึกษา)" || 
					$QUA_NAME=="กศ.บ.(ธุรกิจศึกษา)" || $QUA_NAME=="กศ.บ.ศึกษาศาสตร์" || $QUA_NAME=="กศ.บ.ธุรกิจศึกษา(การตลาด)" || $QUA_NAME=="กศ.บ.การวัดผลการศึกษา") 
					$EN_CODE = "4010012";
				elseif ($QUA_NAME=="อนุปริญญาบริหารธุรกิจ(อ.บธ.การบริหารธุรกิจ)" || $QUA_NAME=="อนุปริญญา(อ.บธ.การจัดการทั่วไป)" || $QUA_NAME=="อ.บธ.คอมพิวเตอร์ธุรกิจ") $EN_CODE = "3010043";
				elseif ($QUA_NAME=="รัฐศาสตรมหาบัณฑิต(ร.ม.)" || $QUA_NAME=="ร.ม.(รัฐศาสตร์)" || $QUA_NAME=="ร.ม. (บริหารรัฐกิจ)") $EN_CODE = "6010050";
				elseif ($QUA_NAME=="ครุศาสตรดุษฎีบัณฑิต(ค.ด.) (การศึกษานอกระบบโรงเรียน)") $EN_CODE = "8010004";
				elseif ($QUA_NAME=="รป.บ.(การบริหารรัฐกิจ)" || $QUA_NAME=="รป.บ. รัฐประศาสนศาสตร์" || $QUA_NAME=="รป.บ.(บริหารรัฐกิจ)" || $QUA_NAME=="รป.บ.(รัฐประศาสนศาสตร์)" || 
					$QUA_NAME=="รป.บ.รัฐประศาสนศาสตร์" || $QUA_NAME=="รปศ." || $QUA_NAME=="รป.บ.รัฐประศาสนศาตร์" || $QUA_NAME=="รป.บ. (รัฐประศาสนศาสตร์)" || 
					$QUA_NAME=="รป.บ.(การบริหารงานบุคคล)" || $QUA_NAME=="รป.บ.รัฐประศาสนศตร์" || $QUA_NAME=="ปริญญาตรี รัฐประศาสนศาสตร์บัณฑิต") 
					$EN_CODE = "4010054";
				elseif ($QUA_NAME=="ศษ.บ.คณิตศาสตร์" || $QUA_NAME=="ศษ.บ.(คณิตศาสตร์)" || $QUA_NAME=="ศษ.บ.การวัดและประเมินผล ๙") $EN_CODE = "4010142";
				elseif ($QUA_NAME=="MS.CE.HIGHWAY AND TRAFFIC ENGINEERING" || $QUA_NAME=="M.S.C.E" || $QUA_NAME=="M.S.C.E.(HIGHWAY ENGINEERING)" || 
					$QUA_NAME=="M.S.C.A.") 
					$EN_CODE = "6029010";
				elseif ($QUA_NAME=="M.ENG. (โครงสร้าง)" || $QUA_NAME=="M.ENG" || $QUA_NAME=="M.ENG.(GEOTECHNICAL ENGINEERIN" || 
					$QUA_NAME=="M. ENG. (TRANSPORTATION ENG.)" || $QUA_NAME=="M.E" || $QUA_NAME=="MASTER ENGINEERING" || 
					$QUA_NAME=="M.ENG (TRANSPORTATION)" || $QUA_NAME=="M.ENG (SOIL)" || $QUA_NAME=="M.ENG(SOIL ENGINEERING)" || 
					$QUA_NAME=="M.ENG.(TRANSPORTATION)" || $QUA_NAME=="M.E.(MECHANICAL ENGINEER)" || $QUA_NAME=="M.ENG.SC.(TRANSPORTATION)" || 
					$QUA_NAME=="M.SC (TRANSPORTATION)" || $QUA_NAME=="M.ENG (STRUCTURE)" || $QUA_NAME=="M.S.T") $EN_CODE = "6029016";
				elseif ($QUA_NAME=="รบ. (การคลัง)" || $QUA_NAME=="ร.บ. (บริหารรัฐกิจ)" || $QUA_NAME=="ร.บ.ทฤษฎีและเทคนิคทางรัฐศาสตร์" || $QUA_NAME=="รบ." || 
					$QUA_NAME=="รัฐศาสตร์บัณฑิต(บริหารรัฐกิจ)" || $QUA_NAME=="ปริญญาตรี รัฐศาสตร์บัณฑิต") $EN_CODE = "4010055";
				elseif ($QUA_NAME=="ศบ." || $QUA_NAME=="ศบ. (เศรษฐศาสตร์)" || $QUA_NAME=="ศ.บ.เศรษฐศาสตร์" || $QUA_NAME=="ศ.บ.(เศรษฐศาสตร์อุตสาหกรรม)" || 
					$QUA_NAME=="เศรษฐศาสตร์บัณฑิต(การคลัง)" || $QUA_NAME=="ศ.บ.(เศรษฐศาสตร์การเกษตร)" || $QUA_NAME=="ศ.บ.การคลัง" || $QUA_NAME=="ศ.บ.เศรษฐศาสตร์การเงิน" || 
					$QUA_NAME=="ศ.บ.เศรษฐศาสตร์ระหว่างประเทศ") $EN_CODE = "4010149";
				elseif ($QUA_NAME=="ประกาศนียบัตรบัณฑิตทางกฎหมายมหาชน" || $QUA_NAME=="ประกาศนียบัตรบัณฑิตทางกฎหมายมหาชน(ป.บัณฑิต (กฎหมายมหาชน))") $EN_CODE = "5010027";
				elseif ($QUA_NAME=="พศ.บ. (สถิติ)" || $QUA_NAME=="พศ.บ(เกียรตินิยมอันดับ2)" || $QUA_NAME=="พาณิชย์ศาสตร์(พณ.บ.),สถิติศาสตร์") $EN_CODE = "4010047";
				elseif ($QUA_NAME=="B.S.C.E" || $QUA_NAME=="B.Eng(Civil Engineering)") $EN_CODE = "4029007";
				elseif ($QUA_NAME=="ประกาศนียบัตรบัณฑิต") $EN_CODE = "5010021";
				elseif ($QUA_NAME=="ปทส.(ช่างโยธา)") $EN_CODE = "4010003";
				elseif ($QUA_NAME=="BB.A.(BACHELOR OF BUSINESS ADA") $EN_CODE = "4029003";
				elseif ($QUA_NAME=="สม.บ (สังคมวิทยาและมานุษยวิทยา)" || $QUA_NAME=="สังคมวิทยาและมานุษยวิทยาบัณฑิต (เอกสังคมวิทยา)" || $QUA_NAME=="มนุษยศาสตร์และสังคมศาสตร์") $EN_CODE = "4010155";
				elseif ($QUA_NAME=="อนุปริญญาพยาบาลและอนามัย") $EN_CODE = "3010047";
				elseif ($QUA_NAME=="สถาบัตยกรรมบัณิ(สถ.บ.),สถาปัตยกรรม" || $QUA_NAME=="สถ.บ.(สถาปัตยกรรม)") $EN_CODE = "4010152";
				elseif ($QUA_NAME=="พัฒนบริหารศาสตร์มหาบัณฑิต") $EN_CODE = "6010034";
				elseif ($QUA_NAME=="บธบ.(บริหารงานบุคคล)" || $QUA_NAME=="บธบ.(การบริหารงานบุคคล)") $EN_CODE = "4010040";
				elseif ($QUA_NAME=="บธบ.(การเงินการธนาคาร)" || $QUA_NAME=="บธบ.การเงินและธนาคาร" || $QUA_NAME=="บธบ." || $QUA_NAME=="บธบ.(บริหารธุรกิจและการจัดการ)" || 
					$QUA_NAME=="บธ.บ การเงิน") $EN_CODE = "4010038";
				elseif ($QUA_NAME=="ค.บ. การสหกรณ์") $EN_CODE = "4010020";
				elseif ($QUA_NAME=="อส.บ.วิศวกรรมก่อสร้าง" || $QUA_NAME=="อส.บ.," || $QUA_NAME=="อุตสาหกรรมศาสตรบัณฑิต(วิศวกรรมก่อสร้าง)" || $QUA_NAME=="อสบ.เทคโนโลยีการผลิต" || 
					$QUA_NAME=="อสบ.เทคโนโลยีขนถ่ายวัสดุ" || $QUA_NAME=="อส.บ.วิศวกรรมโยธา" || $QUA_NAME=="อส.บ.เทคโนโลยี่ขนถ่ายวัสดุ" || $QUA_NAME=="อส.บ.วิศวกรรมโยธา" || 
					$QUA_NAME=="อส.บ.(เทคโนโลยีขนถ่ายวัสดุ)" || $QUA_NAME=="อส.บ.(วิศวกรรมเครื่องกล)") $EN_CODE = "4010166";
				elseif ($QUA_NAME=="อนุปริญญานิติศาสตร์") $EN_CODE = "3010041";
				elseif ($QUA_NAME=="ม.ศ.6(ช่างยนต์)" || $QUA_NAME=="ม.ศ.6(ก่อสร้าง)" || $QUA_NAME=="ม.ศ.6(ช่างก่อสร้าง)" || $QUA_NAME=="ม.ศ.6 (บัญชี)" || $QUA_NAME=="ม.ศ.6(แผนภาษาต่างประเทศ)" || 
					$QUA_NAME=="ม.ศ.6 (พณิชยการ)" || $QUA_NAME=="ม.ศ.6 (ก่อสร้าง)" || $QUA_NAME=="ม.ศ.6 (ช่างยนต์)" || $QUA_NAME=="ม.ศ.6 (ช่างก่อสร้าง)" || $QUA_NAME=="ม.ศ.6 (ช่างเครื่องยนต์)" || $QUA_NAME=="ม.ศ.6 ช่างก่อสร้าง" || $QUA_NAME=="ม.ศ. 6 (พณิชยการ)") $EN_CODE = "0510119";
				elseif ($QUA_NAME=="ประกาศนียบัตรม.3" || $QUA_NAME=="ประกาศนียม.3" || $QUA_NAME=="ประกาศนัยบัตร ม.3" || $QUA_NAME=="ประกาศนียบัตร ม.3ม.3" || $QUA_NAME=="ประกาศนีบัตร ม.3" || $QUA_NAME=="ประกาศนีบบัตรม.3" || $QUA_NAME=="ประกาสนียบัตร ม.3") 
					$EN_CODE = "0510101";
				elseif ($QUA_NAME=="ประกาศนียบัตรม.6" || $QUA_NAME=="ประกาศนียบัตร (ม.6.)") $EN_CODE = "0510102";
				elseif ($QUA_NAME=="เทคโนโลยีบัณฑิต (การจัดการงานช่างและผังเมือง)" || $QUA_NAME=="เทคโนโลยี่บัณฑิต(เทคโนโลยีสารสนเทศธุรกิจ)") $EN_CODE = "4010033";
				elseif ($QUA_NAME=="ม.8(วิทยาศาสตร์)" || $QUA_NAME=="ม.8 วิทย์" || $QUA_NAME=="ม.8 (วิทย์)") $EN_CODE = "0510120";
				elseif ($QUA_NAME=="การวางแผนภาคและเมืองมหาบัณฑิต (ผ.ม.)") $EN_CODE = "6010006";
				elseif ($QUA_NAME=="วารสารศาสตรมหาบัณฑิต(การสื่อสารภาครัฐและเอกชน)") $EN_CODE = "6010052";
				elseif ($QUA_NAME=="ประกาศนียบัตร ม.ศ. 3") $EN_CODE = "0510104";
				elseif ($QUA_NAME=="ประกาศนียบัตร นักเรียนจ่าพรรคนาวิน") $EN_CODE = "0510069";
				elseif ($QUA_NAME=="ประกาศนียบัตรพยาบาลศาสตร์ ระดับต้น" || $QUA_NAME=="ประกาศนียบัตรพยาบาลศาสตร์ระดับต้น") $EN_CODE = "2010038";
				elseif ($QUA_NAME=="อ.บ.(ภาษาต่างประเทศ)") $EN_CODE = "4010163";
				elseif ($QUA_NAME=="ปม.(ครูมัธยม)" || $QUA_NAME=="ประโยคครูมัธยมศึกษา" || $QUA_NAME=="ปม.(ประโยคครูมัยม)" || $QUA_NAME=="ปม." || $QUA_NAME=="ป.ม." || 
					$QUA_NAME=="ประโยคครูมัธยม(ก่อสร้าง)" || $QUA_NAME=="ประโยคครูมัธยมศึกษา" || $QUA_NAME=="ปม." || $QUA_NAME=="ป.ม." || $QUA_NAME=="ปม.(ก่อสร้าง)" || 
					$QUA_NAME=="ปม.ช่างยนต์") $EN_CODE = "3010006";
				elseif ($QUA_NAME=="ประกาศนียบัตรประโยคครูประถม๙") $EN_CODE = "0510021";
				elseif ($QUA_NAME=="ศษ.บ.ศิลปกรรม") $EN_CODE = "4010148";
				elseif ($QUA_NAME=="ประกาศนียบัตรคนใช้เครื่องจักรยนต์") $EN_CODE = "0010020";
				elseif ($QUA_NAME=="ประกาศนียบัตรนายเรือ") $EN_CODE = "0010028";
				elseif ($QUA_NAME=="บธ.บ.(การจัดการ)" || $QUA_NAME=="บริหารธุรกิจบัณฑิต(การจัดการทั่วไป)") $EN_CODE = "4010174";
				elseif ($QUA_NAME=="ประกาศนียบัตรคนใช้เครื่องจักรเดินลำน้ำ") $EN_CODE = "0010025";
				elseif ($QUA_NAME=="บธบ.(การตลาด)") $EN_CODE = "4010039";
				elseif ($QUA_NAME=="ปริญญาโทสถิติประยุกต์(คอมพิวเตอร์)" || $QUA_NAME=="ปริญญาโทสถิติประยุกต์(ประชากร)") $EN_CODE = "6010075";
				elseif ($QUA_NAME=="ประกาศนียบัตรชั้นสูงทางสถิติ") $EN_CODE = "5010046";
				elseif ($QUA_NAME=="อนุปริญญาวิทยาศาสตร์การแพทย์ (รังสีเทคนิค)") $EN_CODE = "2010019";
				elseif ($QUA_NAME=="ปริญญาโทในประเทศ") $EN_CODE = "6010000";
				elseif ($QUA_NAME=="เภสัชศาสตร์บัณฑิต") $EN_CODE = "4010052";
				elseif ($QUA_NAME=="ประกาศนียบัตรวิชาภาษาอังกฤษเฉพาะอาชีพ(กฏหมย)" || $QUA_NAME=="ภาษาอังกฤษเฉพาะอาชีพ(กฏหมาย)") $EN_CODE = "1010056";
				elseif ($QUA_NAME=="ปรัชญาดุษฎีบัณฑิต (ปร.ด.)") $EN_CODE = "8010014";
				elseif ($QUA_NAME=="ประกาศนียบัตรบัณฑิการจัดการ และการประเมินโครงการ สาขาวิชาศิลปศาสตร์") $EN_CODE = "5010024";
				elseif ($QUA_NAME=="ประกาศนียบัตรบัณฑิตทางภาษาอังกฤษสำหรับธุรกิจและการจัดการ") $EN_CODE = "5010034";
				elseif ($QUA_NAME=="ประถมศึกษา") $EN_CODE = "0510112";
				elseif ($QUA_NAME=="ประกาศนียบัตรวิชาการพยาบาลและผดุงครรภ์") $EN_CODE = "2010041";
				elseif ($QUA_NAME=="ประกาศนียบัตรบัณฑิต(วิชาชีพครู)") $EN_CODE = "5010042";
				elseif ($QUA_NAME=="ป.1-ป.4" || $QUA_NAME=="ป.1 - ป.4") $EN_CODE = "0510084";
				elseif ($QUA_NAME=="ม.1-ม.4") $EN_CODE = "4010061";
				elseif ($QUA_NAME=="ป.1-ป.7") $EN_CODE = "4010061";
				elseif ($QUA_NAME=="ม.1-ม.2") $EN_CODE = "4010061";
				elseif ($QUA_NAME=="ม.ศ.1-2") $EN_CODE = "4010061";
				elseif ($QUA_NAME=="ม.ศ.1-ม.ศ.5" || $QUA_NAME=="ม.ศ. 1-5") $EN_CODE = "4010061";
				elseif ($QUA_NAME=="ป.1-ป.2") $EN_CODE = "4010061";
				elseif ($QUA_NAME=="ป.3-ป.4") $EN_CODE = "4010061";
				elseif ($QUA_NAME=="ม.1-ม.7") $EN_CODE = "4010061";
				elseif ($QUA_NAME=="ม.ศ.2-ม.ศ.3") $EN_CODE = "4010061";
				elseif ($QUA_NAME=="ป.5-ป.7") $EN_CODE = "4010061";
				elseif ($QUA_NAME=="วิทยาศาสตร์บัณฑิต") $EN_CODE = "4010061";

				if (!$EN_CODE) {
					$cmd = " select EN_CODE from PER_EDUCNAME where EN_NAME = '$QUA_NAME' or EN_SHORTNAME = '$QUA_NAME' ";
					$db_dpis2->send_cmd($cmd);
		//			$db_dpis2->show_error();
					$data2 = $db_dpis2->get_array();
					$EN_CODE = $data2[EN_CODE];
				}
				if (!$EN_CODE && $QUA_NAME) {
					$i++;
					echo "$i วุฒิการศึกษา $QUA_CODE - $QUA_NAME - $MAJOR_NAME<br>";
				}
			}
	
			$CT_CODE_EDU = trim($data[COUNTRY_CODE]);
			$INSTITUTE_CODE = trim($data[INS_CODE]);
			$cmd = " SELECT INS_NAME FROM INSTITUTION WHERE INS_CODE = '$INSTITUTE_CODE' ";
			$db_dpis351->send_cmd($cmd);
			$data1 = $db_dpis351->get_array();
			$INS_NAME = trim($data1[INS_NAME]);
			$INSTITUTE_NAME = str_replace("ร.ร.", "โรงเรียน", $INS_NAME);

			$EDU_INSTITUTE = "";
			$cmd = " select INS_CODE from PER_INSTITUTE where INS_NAME = '$INS_NAME' or INS_NAME = '$INSTITUTE_NAME' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$INS_CODE = $data2[INS_CODE];
			if (!$INS_CODE) $EDU_INSTITUTE = $INSTITUTE_NAME;
	
			$EDU_ENDYEAR = substr(trim($data[GOVED_END_YEAR]),2,4);
			$EDU_GRADE = trim($data[GRADE_AVERAGE]);
			$EDU_STARTYEAR = substr(trim($data[GOVED_BEGIN_YEAR]),2,4);
			
			$EDU_LEV_CODE = trim($data[EDU_LEV_CODE]);
			if ($EDU_LEV_CODE==1) $EL_CODE = "80"; // ปริญญาเอกต่างประเทศ
			elseif ($EDU_LEV_CODE==2) $EL_CODE = "80"; // ปริญญาเอกในประเทศ
			elseif ($EDU_LEV_CODE==3) $EL_CODE = "60"; // ปริญญาโทต่างประเทศ
			elseif ($EDU_LEV_CODE==4) $EL_CODE = "60"; // ปริญญาโทในประเทศ
			elseif ($EDU_LEV_CODE==5) $EL_CODE = "40"; // ปริญญาตรีต่างประเทศ
			elseif ($EDU_LEV_CODE==6) $EL_CODE = "40"; // ปริญญาตรีในประเทศ
			elseif ($EDU_LEV_CODE==7) $EL_CODE = "35"; // อนุปริญญา
			elseif ($EDU_LEV_CODE==8) $EL_CODE = "30"; // ปวส.
			elseif ($EDU_LEV_CODE==9) $EL_CODE = "10"; // ปวช.	
			elseif ($EDU_LEV_CODE==10) $EL_CODE = "99"; // อนุบาล1
			elseif ($EDU_LEV_CODE==11) $EL_CODE = "99"; // อื่น ๆ
			elseif ($EDU_LEV_CODE==12) $EL_CODE = "20"; // ปวท.
			elseif ($EDU_LEV_CODE==13) $EL_CODE = "40"; // ปทส.
			elseif ($EDU_LEV_CODE==14) $EL_CODE = "02"; // มัธยมศึกษาตอนปลาย
			elseif ($EDU_LEV_CODE==15) $EL_CODE = "01"; // มัธยมศึกษาตอนต้น
			elseif ($EDU_LEV_CODE==16) $EL_CODE = "99"; // 
			elseif ($EDU_LEV_CODE==19) $EL_CODE = "10"; // ประกาศนียบัตร
			elseif ($EDU_LEV_CODE==20) $EL_CODE = "02"; // ปกศ.	
			elseif ($EDU_LEV_CODE==21) $EL_CODE = "30"; // ประกาศนียบัตรชั้นสูง
			elseif ($EDU_LEV_CODE==30) $EL_CODE = "03"; // ป.1
			elseif ($EDU_LEV_CODE==31) $EL_CODE = "03"; // ป.2
			elseif ($EDU_LEV_CODE==32) $EL_CODE = "03"; // ป.3
			elseif ($EDU_LEV_CODE==33) $EL_CODE = "03"; // ป.4
			elseif ($EDU_LEV_CODE==34) $EL_CODE = "03"; // ป.5
			elseif ($EDU_LEV_CODE==35) $EL_CODE = "03"; // ป.6
			elseif ($EDU_LEV_CODE==36) $EL_CODE = "03"; // ป.7
			elseif ($EDU_LEV_CODE==40) $EL_CODE = "01"; // ม.1
			elseif ($EDU_LEV_CODE==41) $EL_CODE = "01"; // ม.2
			elseif ($EDU_LEV_CODE==42) $EL_CODE = "01"; // ม.3
			elseif ($EDU_LEV_CODE==43) $EL_CODE = "02"; // ม.4
			elseif ($EDU_LEV_CODE==44) $EL_CODE = "02"; // ม.5
			elseif ($EDU_LEV_CODE==45) $EL_CODE = "02"; // ม.6
			elseif ($EDU_LEV_CODE==46) $EL_CODE = "02"; // ม.7
			elseif ($EDU_LEV_CODE==47) $EL_CODE = "02"; // ม.8
			elseif ($EDU_LEV_CODE==50) $EL_CODE = "01"; // ม.ศ.1
			elseif ($EDU_LEV_CODE==51) $EL_CODE = "01"; // ม.ศ.2
			elseif ($EDU_LEV_CODE==52) $EL_CODE = "01"; // ม.ศ.3
			elseif ($EDU_LEV_CODE==53) $EL_CODE = "02"; // ม.ศ.4
			elseif ($EDU_LEV_CODE==54) $EL_CODE = "02"; // ม.ศ.5
			elseif ($EDU_LEV_CODE==55) $EL_CODE = "02"; // ม.ศ.6
			elseif ($EDU_LEV_CODE==60) $EL_CODE = "01"; // ม.1(ปัจจุบัน)
			elseif ($EDU_LEV_CODE==61) $EL_CODE = "01"; // ม.2(ปัจจุบัน)
			elseif ($EDU_LEV_CODE==62) $EL_CODE = "01"; // ม.3(ปัจจุบัน)
			elseif ($EDU_LEV_CODE==63) $EL_CODE = "02"; // ม.4(ปัจจุบัน)
			elseif ($EDU_LEV_CODE==64) $EL_CODE = "02"; // ม.5(ปัจจุบัน)
			elseif ($EDU_LEV_CODE==65) $EL_CODE = "02"; // ม.6(ปัจจุบัน)
			elseif ($EDU_LEV_CODE==66) $EL_CODE = "40"; // ประกาศนียบัตรพยาบาลศาสตร์และผดุงครรภ์ชั้นสูง
			elseif ($EDU_LEV_CODE==67) $EL_CODE = "40"; // ประกาศนียบัตรครูเทคนิคชั้นสูง(ปทส.)
			elseif ($EDU_LEV_CODE==68) $EL_CODE = "30"; // ประกาศนียบัตรการชลประทาน

			if ($QUA_NAME=="วศ.บ.(โยธา)เกียรตินิยมอันดับ2" || $QUA_NAME=="วศบ.(โยธา) เกียรตินิยมอันดับ2" || $QUA_NAME=="วศ.บ.(โยธา) เกียรตินิยมอันดับ2") $xxEN_CODE = "เกียรตินิยมอันดับ 2";
			elseif($QUA_NAME=="วศ.บ. (โยธา) เกียรตินิยม") $xxEN_CODE = "เกียรตินิยม";

			if (($INS_CODE || $EDU_INSTITUTE) && !$CT_CODE_EDU) $CT_CODE_EDU = "140";
			$EDU_TYPE = "";
			if ($GOVED_HIGH_QUA == "1") $EDU_TYPE .= "4";
			if ($GOVED_WORK_QUA == "1") $EDU_TYPE .= "2";
			if ($GOVED_ENTRY_QUA == "1") $EDU_TYPE .= "1";
			if (!$EDU_TYPE) $EDU_TYPE = "3";

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
							$EDU_GRADE, NULL, NULL, NULL, '$EDU_REMARK', '$EDU_INSTITUTE', '$CT_CODE_EDU') ";
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
		$db_dpis35->free_result();
		$db_dpis351->free_result();
	} // end if

	if ($command=='RETIRE' || $command=='RETIRE_EMP'){
// พ้นจากส่วนราชการ 9166 ok
		if ($command=='RETIRE')
			$cmd = " SELECT a.ID, GOVRE_ORDER, GOVRE_TO_DATE, GOVRE_RET_DATE, GOVRE_TO_TYPE, 
							GOVRE_RES, GOVRE_FLAG, GOVRE_REFF, GOVRE_PCS_FLAG, GOVRE_PCS_ORDER, 
							GOVRE_CONTINUE, GOVRE_EVER, GOVE_CONTINUE_FLAG
							FROM GOVRESG a, COMMON_HISTORY b
							WHERE a.ID=b.ID
							ORDER BY a.ID, GOVRE_ORDER ";
		elseif ($command=='RETIRE_EMP')
			$cmd = " SELECT a.ID, EMPCHG_ORDER as GOVCHG_ORDER, EMPCHG_REFF as GOVCHG_REFF, EMPNEW_PNAME as GOVNEW_PNAME, 
							EMPNEW_FNAME as GOVNEW_FNAME, EMPNEW_LNAME as GOVNEW_LNAME, EMPOLD_PNAME as GOVOLD_PNAME, 
							EMPOLD_FNAME as GOVOLD_FNAME, EMPOLD_LNAME as GOVOLD_LNAME, EMPCHG_DATE as GOVCHG_DATE, 
							EMPCHG_ME_CODE as , EMPCHG_MPB_ORDER as GOVCHG_MPB_ORDER
							FROM EMPRESG a, EMPLOYEE_HISTORY b
							WHERE a.ID=b.ID
							ORDER BY a.ID, EMPCHG_ORDER ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "<br>";
		while($data = $db_dpis35->get_array()){
			$PER_RETIRE++; 
			$PER_ID = trim($data[ID]);
			$GOVRE_TO_DATE = trim($data[GOVRE_TO_DATE]);
			$PER_EFFECTIVEDATE = ($GOVRE_TO_DATE)? ((substr($GOVRE_TO_DATE, 4, 4) - 543) ."-". substr($GOVRE_TO_DATE, 2, 2) ."-". substr($GOVRE_TO_DATE, 0, 2)) : "";
			$PER_POS_REASON = trim($data[GOVRE_RES]);
			$MOV_CODE = "11910";
			$GOVRE_TO_TYPE = trim($data[GOVRE_TO_TYPE]);
			if ($GOVRE_TO_TYPE == 15) $MOV_CODE = "11820";
			elseif ($GOVRE_TO_TYPE == 20) $MOV_CODE = "11810";
			elseif ($GOVRE_TO_TYPE == 21) $MOV_CODE = "10610";
			elseif ($GOVRE_TO_TYPE == 22) $MOV_CODE = "12210";
			elseif ($GOVRE_TO_TYPE == 23) $MOV_CODE = "12310";
			elseif ($GOVRE_TO_TYPE == 24) $MOV_CODE = "11910";
			elseif ($GOVRE_TO_TYPE == 25) $MOV_CODE = "12110";
			elseif ($GOVRE_TO_TYPE == 26) $MOV_CODE = "120";
			elseif ($GOVRE_TO_TYPE == 30) $MOV_CODE = "11895";
			$PER_DOCNO = trim($data[GOVCHG_REFF]);

			$cmd = " UPDATE PER_PERSONAL SET PER_EFFECTIVEDATE = '$PER_EFFECTIVEDATE', 
							PER_POS_REASON = '$PER_POS_REASON', 
							MOV_CODE = '$MOV_CODE', 
							PER_DOCNO = '$PER_DOCNO' 
							WHERE PER_ID = $PER_ID ";
			$db_dpis->send_cmd($cmd);

		} // end while						
		
		echo "PER_RETIRE - $PER_RETIRE<br>";

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();
		$db_dpis351->free_result();
	} // end if

	if ($command=='REWARD'){
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
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "<br>";
		$MAX_ID = 1;
		while($data = $db_dpis35->get_array()){
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

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();
		$db_dpis351->free_result();
	} // end if

	if ($command=='REWARD_EMP'){
// ความดีความชอบ 1576 ok
		$cmd = " truncate table per_rewardhis ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

// ความดีความชอบลูกจ้างประจำ 1365 
		$cmd = " SELECT ID, GOOD_WORK_CODE, GOOD_WORK_NAME, JOB_CODE, JOB_NAME, SECTION_CODE, SECTION_NAME, 
						DIVISION_CODE, DIVISION_NAME, DEPARTMENT_CODE, DEPARTMENT_NAME, POS_NUM_NAME, POS_NUM_CODE, 
						CLUSTER_CODE, CATEGORY_SAL_CODE, CATEGORY_SAL_NAME, WORK_LINE_CODE, WORK_LINE_NAME, 
						to_char(RECEIVED_DATE,'yyyy-mm-dd') as RECEIVED_DATE, APPROVE_NUM, to_char(APPROVE_DATE,'yyyy-mm-dd') as APPROVE_DATE, 
						to_char(BORN,'yyyy-mm-dd') as BORN, to_char(RET_BORN_YEAR,'yyyy-mm-dd') as RET_BORN_YEAR, 
						to_char(BEGIN_ENTRY_DATE,'yyyy-mm-dd') as BEGIN_ENTRY_DATE, RECEIVED_FLAG, MP_YEAR, 
						USER_CREATE, to_char(CREATE_DATE,'yyyy-mm-dd') as CREATE_DATE, 
						USER_UPDATE, to_char(UPDATE_DATE,'yyyy-mm-dd') as UPDATE_DATE
						FROM HR_GOOD_WORK_EMP a, EMPLOYEE_HISTORY b
						WHERE a.ID=b.ID
						ORDER BY ID, MP_YEAR ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "<br>";
		while($data = $db_dpis35->get_array()){
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
		$db_dpis35->free_result();
		$db_dpis351->free_result();
	} // end if

	if ($command=='ABSENT' || $command=='ABSENT_EMP'){
// การลา 53515 ok
		$cmd = " truncate table per_absenthis ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

// การลาข้าราชการ 23713
		if ($command=='ABSENT')
			$cmd = " SELECT a.ID, a.GOVABSENT_ORDER, a.ABSENT_TYPE_CODE, GOVABSENT_AMOUNT, GOVABSENT_NOTE, GOVABSENT_REFF, 
							  GOVABSENT_HALFDAY_FLAG, GOVABSENT_TYPEPERMIT, GOVABSENT_PERMITDATE, DATE_START, DATE_END, AMOUNT, 
							  HALFDAY_FLAG, GOVABSENT_MEMO 
							  FROM GOVABSENT_DATE a, GOVABSENT_DATE_DETAIL b
							  WHERE a.ID=b.ID and a.GOVABSENT_ORDER=b.GOVABSENT_ORDER and a.ABSENT_TYPE_CODE=b.ABSENT_TYPE_CODE
							  ORDER BY a.ID, a.GOVABSENT_ORDER, a.ABSENT_TYPE_CODE ";
		elseif ($command=='ABSENT')
			$cmd = " SELECT a.ID, a.EMPABSENT_ORDER, a.ABSENT_TYPE_CODE, EMPABSENT_AMOUNT, EMPABSENT_NOTE, EMPABSENT_REFF, 
							  EMPABSENT_HALFDAY_FLAG, EMPABSENT_TYPEPERMIT, EMPABSENT_PERMITDATE, DATE_START, DATE_END, AMOUNT, 
							  HALFDAY_FLAG, EMPABSENT_MEMO as GOVABSENT_MEMO
							  FROM EMPABSENT_DATE a, EMPABSENT_DATE_DETAIL b
							  WHERE a.ID=b.ID and a.EMPABSENT_ORDER=b.EMPABSENT_ORDER and a.ABSENT_TYPE_CODE=b.ABSENT_TYPE_CODE
							  ORDER BY a.ID, a.EMPABSENT_ORDER, a.ABSENT_TYPE_CODE ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "<br>";
		$MAX_ID = 1;
		while($data = $db_dpis35->get_array()){
			$PER_ID = trim($data[ID]);
			$ABSENT_TYPE_CODE = $data[ABSENT_TYPE_CODE];
			if ($ABSENT_TYPE_CODE==0) $ABSENT_TYPE = "10";
			elseif ($ABSENT_TYPE_CODE==1) $ABSENT_TYPE = "01";
			elseif ($ABSENT_TYPE_CODE==2) $ABSENT_TYPE = "03";
			elseif ($ABSENT_TYPE_CODE==3) $ABSENT_TYPE = "02";
			elseif ($ABSENT_TYPE_CODE==4) $ABSENT_TYPE = "06";
			elseif ($ABSENT_TYPE_CODE==5) $ABSENT_TYPE = "05";
			elseif ($ABSENT_TYPE_CODE==6) $ABSENT_TYPE = "07";
			elseif ($ABSENT_TYPE_CODE==7) $ABSENT_TYPE = "08";
			elseif ($ABSENT_TYPE_CODE==8) $ABSENT_TYPE = "13";
			elseif ($ABSENT_TYPE_CODE==9) $ABSENT_TYPE = "09";
			elseif ($ABSENT_TYPE_CODE==10) $ABSENT_TYPE = "04";
			$DATE_START = trim($data[DATE_START]);
			$ABS_STARTDATE = ($DATE_START)? ((substr($DATE_START, 4, 4) - 543) ."-". substr($DATE_START, 2, 2) ."-". substr($DATE_START, 0, 2)) : "";
			$DATE_END = trim($data[DATE_END]);
			$ABS_ENDDATE = ($DATE_END)? ((substr($DATE_END, 4, 4) - 543) ."-". substr($DATE_END, 2, 2) ."-". substr($DATE_END, 0, 2)) : "";
			$ABS_DAY = trim($data[AMOUNT]);
			$HALFDAY_FLAG = trim($data[HALFDAY_FLAG]);
			$ABS_STARTPERIOD = 3;
			$ABS_ENDPERIOD = 3;
			if ($HALFDAY_FLAG==1) {
				$ABS_STARTPERIOD = 1;
				if ($ABS_DAY == 0.5) $ABS_ENDPERIOD = 1;
			} elseif ($HALFDAY_FLAG==2) {
				if ($ABS_DAY == 0.5) $ABS_STARTPERIOD = 2;
				$ABS_ENDPERIOD = 2;
			}

			$ABS_REMARK = trim($data[GOVABSENT_MEMO])." ".trim($data[GOVABSENT_NOTE]);
			if (!$ABS_DAY) $ABS_DAY = 0;
			if (!$ABS_STARTDATE) $ABS_STARTDATE = '-';
			if (!$ABS_ENDDATE) $ABS_ENDDATE = '-';

			$cmd = " INSERT INTO PER_ABSENTHIS(ABS_ID, PER_ID, AB_CODE, ABS_STARTDATE, ABS_STARTPERIOD, 
							ABS_ENDDATE, ABS_ENDPERIOD, ABS_DAY, UPDATE_USER, UPDATE_DATE, PER_CARDNO, ABS_REMARK)
							VALUES ($MAX_ID, $PER_ID, '$ABSENT_TYPE', '$ABS_STARTDATE', $ABS_STARTPERIOD, '$ABS_ENDDATE', $ABS_ENDPERIOD, $ABS_DAY, 
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
		$db_dpis35->free_result();
		$db_dpis351->free_result();
	} // end if

	if ($command=='ADDRESS' || $command=='ADDRESS_EMP'){
		$cmd = " truncate table per_address ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

// ที่อยู่ข้าราชการ 23661
		if ($command=='ADDRESS')
			$cmd = " SELECT a.ID, GOVADDR_TYPE, GOVADDR_LINE1, GOVADDR_LINE2, GOVTELEPHONE, GOVZIP_CODE, DIST_CODE, PROV_CODE
							FROM GOVADDRESS a, COMMON_HISTORY b
							WHERE a.ID=b.ID AND GOVADDR_TYPE=1
							ORDER BY a.ID ";
		elseif ($command=='ADDRESS_EMP')
			$cmd = " SELECT a.ID, EMPADDR_TYPE as GOVADDR_TYPE, EMPADDR_LINE1 as GOVADDR_LINE1, EMPADDR_LINE2 as GOVADDR_LINE2, 
							EMPTELEPHONE as GOVTELEPHONE, EMPZIP_CODE as GOVZIP_CODE, DIST_CODE, PROV_CODE
							FROM EMPADDRESS a, EMPLOYEE_HISTORY b
							WHERE a.ID=b.ID AND EMPADDR_TYPE=1
							ORDER BY a.ID ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "<br>";
		$MAX_ID = 1;
		while($data = $db_dpis35->get_array()){
			$PER_ADDRESS++;
			$PER_ID = trim($data[ID]);
			$ADR_TYPE = trim($data[GOVADDR_TYPE]);
			if ($ADR_TYPE==0) $ADR_TYPE = 4;
			$ADR_ROAD = trim($data[GOVADDR_LINE1]);
			$ADR_DISTRICT = trim($data[GOVADDR_LINE2]);
			$ADR_HOME_TEL = trim($data[GOVTELEPHONE]);
			$ADR_POSTCODE = trim($data[GOVZIP_CODE]);
			$PROV_CODE = trim($data[PROV_CODE]);
			$DIST_CODE = trim($data[DIST_CODE]);
			$PV_CODE = $AP_CODE = "";
			if (!$PROV_CODE || !$DIST_CODE) {
				$cmd = " SELECT PROV_CODE, DIST_CODE FROM GOVADDRESS WHERE ID='$PER_ID' AND GOVADDR_TYPE=0 ";
				$db_dpis351->send_cmd($cmd);
		//		$db_dpis351->show_error();
				$data1 = $db_dpis351->get_array();
				$PROV_CODE = trim($data1[PROV_CODE]);
				$DIST_CODE = trim($data1[DIST_CODE]);
			} 
			$cmd = " SELECT TF_PROV FROM PROVINCE WHERE PROV_CODE='$PROV_CODE' ";
			$db_dpis351->send_cmd($cmd);
	//		$db_dpis351->show_error();
			$data1 = $db_dpis351->get_array();
			$PV_CODE = trim($data1[TF_PROV]);
			if (!$PV_CODE || $PV_CODE=="0") {
				if (strpos($ADR_ROAD,"สมุทรปราการ") !== false) $PV_CODE = "1100";
				elseif (strpos($ADR_ROAD,"นนทบุรี") !== false) $PV_CODE = "1200";
				elseif (strpos($ADR_ROAD,"ปทุมธานี") !== false) $PV_CODE = "1300";
				elseif (strpos($ADR_ROAD,"พระนครศรีอยุธยา") !== false) $PV_CODE = "1400";
				elseif (strpos($ADR_ROAD,"อ่างทอง") !== false) $PV_CODE = "1500";
				elseif (strpos($ADR_ROAD,"ลพบุรี") !== false) $PV_CODE = "1600";
				elseif (strpos($ADR_ROAD,"สิงห์บุรี") !== false) $PV_CODE = "1700";
				elseif (strpos($ADR_ROAD,"ชัยนาท") !== false) $PV_CODE = "1800";
				elseif (strpos($ADR_ROAD,"สระบุรี") !== false) $PV_CODE = "1900";
				elseif (strpos($ADR_ROAD,"ชลบุรี") !== false) $PV_CODE = "2000";
				elseif (strpos($ADR_ROAD,"ระยอง") !== false) $PV_CODE = "2100";
				elseif (strpos($ADR_ROAD,"จันทบุรี") !== false) $PV_CODE = "2200";
				elseif (strpos($ADR_ROAD,"ตราด") !== false) $PV_CODE = "2300";
				elseif (strpos($ADR_ROAD,"ฉะเชิงเทรา") !== false) $PV_CODE = "2400";
				elseif (strpos($ADR_ROAD,"ปราจีนบุรี") !== false) $PV_CODE = "2500";
				elseif (strpos($ADR_ROAD,"นครนายก") !== false) $PV_CODE = "2600";
				elseif (strpos($ADR_ROAD,"สระแก้ว") !== false) $PV_CODE = "2700";
				elseif (strpos($ADR_ROAD,"นครราชสีมา") !== false) $PV_CODE = "3000";
				elseif (strpos($ADR_ROAD,"บุรีรัมย์") !== false) $PV_CODE = "3100";
				elseif (strpos($ADR_ROAD,"สุรินทร์") !== false) $PV_CODE = "3200";
				elseif (strpos($ADR_ROAD,"ศรีสะเกษ") !== false) $PV_CODE = "3300";
				elseif (strpos($ADR_ROAD,"อุบลราชธานี") !== false) $PV_CODE = "3400";
				elseif (strpos($ADR_ROAD,"ยโสธร") !== false) $PV_CODE = "3500";
				elseif (strpos($ADR_ROAD,"ชัยภูมิ") !== false) $PV_CODE = "3600";
				elseif (strpos($ADR_ROAD,"อำนาจเจริญ") !== false) $PV_CODE = "3700";
				elseif (strpos($ADR_ROAD,"หนองบัวลำภู") !== false) $PV_CODE = "3900";
				elseif (strpos($ADR_ROAD,"ขอนแก่น") !== false) $PV_CODE = "4000";
				elseif (strpos($ADR_ROAD,"อุดรธานี") !== false) $PV_CODE = "4100";
				elseif (strpos($ADR_ROAD,"เลย") !== false) $PV_CODE = "4200";
				elseif (strpos($ADR_ROAD,"หนองคาย") !== false) $PV_CODE = "4300";
				elseif (strpos($ADR_ROAD,"มหาสารคาม") !== false) $PV_CODE = "4400";
				elseif (strpos($ADR_ROAD,"ร้อยเอ็ด") !== false) $PV_CODE = "4500";
				elseif (strpos($ADR_ROAD,"กาฬสินธุ์") !== false) $PV_CODE = "4600";
				elseif (strpos($ADR_ROAD,"สกลนคร") !== false) $PV_CODE = "4700";
				elseif (strpos($ADR_ROAD,"นครพนม") !== false) $PV_CODE = "4800";
				elseif (strpos($ADR_ROAD,"มุกดาหาร") !== false) $PV_CODE = "4900";
				elseif (strpos($ADR_ROAD,"เชียงใหม่") !== false) $PV_CODE = "5000";
				elseif (strpos($ADR_ROAD,"ลำพูน") !== false) $PV_CODE = "5100";
				elseif (strpos($ADR_ROAD,"ลำปาง") !== false) $PV_CODE = "5200";
				elseif (strpos($ADR_ROAD,"อุตรดิตถ์") !== false) $PV_CODE = "5300";
				elseif (strpos($ADR_ROAD,"แพร่") !== false) $PV_CODE = "5400";
				elseif (strpos($ADR_ROAD,"น่าน") !== false) $PV_CODE = "5500";
				elseif (strpos($ADR_ROAD,"พะเยา") !== false) $PV_CODE = "5600";
				elseif (strpos($ADR_ROAD,"เชียงราย") !== false) $PV_CODE = "5700";
				elseif (strpos($ADR_ROAD,"แม่ฮ่องสอน") !== false) $PV_CODE = "5800";
				elseif (strpos($ADR_ROAD,"นครสวรรค์") !== false) $PV_CODE = "6000";
				elseif (strpos($ADR_ROAD,"อุทัยธานี") !== false) $PV_CODE = "6100";
				elseif (strpos($ADR_ROAD,"กำแพงเพชร") !== false) $PV_CODE = "6200";
				elseif (strpos($ADR_ROAD,"ตาก") !== false) $PV_CODE = "6300";
				elseif (strpos($ADR_ROAD,"สุโขทัย") !== false) $PV_CODE = "6400";
				elseif (strpos($ADR_ROAD,"พิษณุโลก") !== false) $PV_CODE = "6500";
				elseif (strpos($ADR_ROAD,"พิจิตร") !== false) $PV_CODE = "6600";
				elseif (strpos($ADR_ROAD,"เพชรบูรณ์") !== false) $PV_CODE = "6700";
				elseif (strpos($ADR_ROAD,"ราชบุรี") !== false) $PV_CODE = "7000";
				elseif (strpos($ADR_ROAD,"กาญจนบุรี") !== false) $PV_CODE = "7100";
				elseif (strpos($ADR_ROAD,"สุพรรณบุรี") !== false) $PV_CODE = "7200";
				elseif (strpos($ADR_ROAD,"นครปฐม") !== false) $PV_CODE = "7300";
				elseif (strpos($ADR_ROAD,"สมุทรสาคร") !== false) $PV_CODE = "7400";
				elseif (strpos($ADR_ROAD,"สมุทรสงคราม") !== false) $PV_CODE = "7500";
				elseif (strpos($ADR_ROAD,"เพชรบุรี") !== false) $PV_CODE = "7600";
				elseif (strpos($ADR_ROAD,"ประจวบคีรีขันธ์") !== false) $PV_CODE = "7700";
				elseif (strpos($ADR_ROAD,"นครศรีธรรมราช") !== false) $PV_CODE = "8000";
				elseif (strpos($ADR_ROAD,"กระบี่") !== false) $PV_CODE = "8100";
				elseif (strpos($ADR_ROAD,"พังงา") !== false) $PV_CODE = "8200";
				elseif (strpos($ADR_ROAD,"ภูเก็ต") !== false) $PV_CODE = "8300";
				elseif (strpos($ADR_ROAD,"สุราษฎร์ธานี") !== false) $PV_CODE = "8400";
				elseif (strpos($ADR_ROAD,"ระนอง") !== false) $PV_CODE = "8500";
				elseif (strpos($ADR_ROAD,"ชุมพร") !== false) $PV_CODE = "8600";
				elseif (strpos($ADR_ROAD,"สงขลา") !== false) $PV_CODE = "9000";
				elseif (strpos($ADR_ROAD,"สตูล") !== false) $PV_CODE = "9100";
				elseif (strpos($ADR_ROAD,"ตรัง") !== false) $PV_CODE = "9200";
				elseif (strpos($ADR_ROAD,"พัทลุง") !== false) $PV_CODE = "9300";
				elseif (strpos($ADR_ROAD,"ปัตตานี") !== false) $PV_CODE = "9400";
				elseif (strpos($ADR_ROAD,"ยะลา") !== false) $PV_CODE = "9500";
				elseif (strpos($ADR_ROAD,"นราธิวาส") !== false) $PV_CODE = "9600";
				else $PV_CODE = "1000";
			}
			$cmd = " SELECT AP_CODE, SUBPROV FROM DISTRICT WHERE DIST_CODE='$DIST_CODE' ";
			$db_dpis351->send_cmd($cmd);
	//		$db_dpis351->show_error();
			$data1 = $db_dpis351->get_array();
			$AP_CODE = trim($data1[AP_CODE]);
			$SUBPROV = trim($data1[SUBPROV]);
			if (!$AP_CODE && $SUBPROV) $AP_CODE = substr($PV_CODE,0,2).$SUBPROV;

			if ($ADR_ROAD || $ADR_DISTRICT || $ADR_HOME_TEL || $ADR_POSTCODE || $PV_CODE || $AP_CODE) {
				if ($PV_CODE) {
					$cmd = " select PV_NAME from PER_PROVINCE where PV_CODE = '$PV_CODE' ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
					$data_dpis = $db_dpis->get_array();
					$PV_NAME = trim($data_dpis[PV_NAME]);
					$ADR_ROAD = str_replace("จ.", "", $ADR_ROAD);
					$ADR_ROAD = str_replace("$PV_NAME", "", $ADR_ROAD);
					if ($PV_NAME=="กรุงเทพมหานคร") {
						$ADR_ROAD = str_replace("กทม.", "", $ADR_ROAD);
						$ADR_ROAD = str_replace("กทม", "", $ADR_ROAD);
					}
				}
				if ($AP_CODE) {
					$cmd = " select AP_NAME from PER_AMPHUR where AP_CODE = '$AP_CODE' ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
					$data_dpis = $db_dpis->get_array();
					$AP_NAME = trim($data_dpis[AP_NAME]);
					$ADR_ROAD = str_replace("$AP_NAME", "", $ADR_ROAD);
				}
				if ($ADR_DISTRICT) $ADR_ROAD = str_replace("$ADR_DISTRICT", "", $ADR_ROAD);
				if ($ADR_POSTCODE) $ADR_ROAD = str_replace("$ADR_POSTCODE", "", $ADR_ROAD);

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
			}
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_ADDRESS ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_ADDRESS - $PER_ADDRESS - $COUNT_NEW<br>";

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();
		$db_dpis351->free_result();
	} // end if

	if ($command=='FAMILY' || $command=='FAMILY_EMP'){
		$cmd = " truncate table per_family ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

// ครอบครัวข้าราชการ 70253 
		if ($command=='FAMILY') 
			$cmd = " SELECT a.ID, FATHER_NAME, MATHER_NAME, OMATHER_LNAME, PARENT_ADDR, MATE_NAME, OMATE_LNAME, WEDDING_DATE, CHILD_NO, EMER_ADDRESS
							  FROM COMMON_DETAIL a, COMMON_HISTORY b
							WHERE a.ID=b.ID AND ID_TYPE='G'
							  ORDER BY a.ID ";
		elseif ($command=='FAMILY_EMP')
			$cmd = " SELECT a.ID, FATHER_NAME, MATHER_NAME, OMATHER_LNAME, PARENT_ADDR, MATE_NAME, OMATE_LNAME, WEDDING_DATE, CHILD_NO, EMER_ADDRESS
							  FROM COMMON_DETAIL a, EMPLOYEE_HISTORY b
							WHERE a.ID=b.ID 
							  ORDER BY a.ID ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "<br>";
		$MAX_ID = 1;
		while($data = $db_dpis35->get_array()){
			$PER_ID = trim($data[ID]);
			$FATHER_NAME = trim($data[FATHER_NAME]);
			$PN_CODE = "";
			if (substr($FATHER_NAME, 0, 3)=="นาย" || substr($FATHER_NAME, 0, 3)=="นาง") {
				$PN_CODE = "003";
				$FATHER_NAME = trim(substr($FATHER_NAME, 3));
			} elseif (substr($FATHER_NAME, 0, 4)=="านาย") {
				$PN_CODE = "003";
				$FATHER_NAME = trim(substr($FATHER_NAME, 4));
			} elseif (substr($FATHER_NAME, 0, 6)=="ม.ร.ว.") {
				$PN_CODE = "121";
				$FATHER_NAME = trim(substr($FATHER_NAME, 6));
			} elseif (substr($FATHER_NAME, 0, 4)=="ส.อ.") {
				$PN_CODE = "223";
				$FATHER_NAME = trim(substr($FATHER_NAME, 4));
			} elseif (substr($FATHER_NAME, 0, 3)=="สอ.") {
				$PN_CODE = "223";
				$FATHER_NAME = trim(substr($FATHER_NAME, 3));
			} elseif (substr($FATHER_NAME, 0, 4)=="ส.ท.") {
				$PN_CODE = "224";
				$FATHER_NAME = trim(substr($FATHER_NAME, 4));
			} elseif (substr($FATHER_NAME, 0, 3)=="สท.") {
				$PN_CODE = "224";
				$FATHER_NAME = trim(substr($FATHER_NAME, 3));
			} elseif (substr($FATHER_NAME, 0, 6)=="ร.ต.อ.") {
				$PN_CODE = "667";
				$FATHER_NAME = trim(substr($FATHER_NAME, 6));
			} elseif (substr($FATHER_NAME, 0, 6)=="ร.ต.ท.") {
				$PN_CODE = "669";
				$FATHER_NAME = trim(substr($FATHER_NAME, 6));
			} elseif (substr($FATHER_NAME, 0, 6)=="ร.ต.ต.") {
				$PN_CODE = "671";
				$FATHER_NAME = trim(substr($FATHER_NAME, 6));
			} elseif (substr($FATHER_NAME, 0, 6)=="ส.ต.อ.") {
				$PN_CODE = "675";
				$FATHER_NAME = trim(substr($FATHER_NAME, 6));
			} elseif (substr($FATHER_NAME, 0, 4)=="สตอ.") {
				$PN_CODE = "675";
				$FATHER_NAME = trim(substr($FATHER_NAME, 4));
			} elseif (substr($FATHER_NAME, 0, 6)=="ส.ตท. " || substr($FATHER_NAME, 0, 6)=="ส.ต.ท.") {
				$PN_CODE = "676";
				$FATHER_NAME = trim(substr($FATHER_NAME, 6));
			} elseif (substr($FATHER_NAME, 0, 4)=="สตท.") {
				$PN_CODE = "676";
				$FATHER_NAME = trim(substr($FATHER_NAME, 4));
			} elseif (substr($FATHER_NAME, 0, 6)=="ส.ต.ต.") {
				$PN_CODE = "677";
				$FATHER_NAME = trim(substr($FATHER_NAME, 6));
			} elseif (substr($FATHER_NAME, 0, 4)=="สตต.") {
				$PN_CODE = "677";
				$FATHER_NAME = trim(substr($FATHER_NAME, 4));
			} elseif (substr($FATHER_NAME, 0, 6)=="จ.ส.ต." || substr($FATHER_NAME, 0, 6)=="จ.ต.ส.") {
				$PN_CODE = "222";
				$FATHER_NAME = trim(substr($FATHER_NAME, 6));
			} elseif (substr($FATHER_NAME, 0, 6)=="จ.ส.ท.") {
				$PN_CODE = "221";
				$FATHER_NAME = trim(substr($FATHER_NAME, 6));
			} elseif (substr($FATHER_NAME, 0, 6)=="จ.ส.อ.") {
				$PN_CODE = "220";
				$FATHER_NAME = trim(substr($FATHER_NAME, 6));
			} elseif (substr($FATHER_NAME, 0, 6)=="พ.อ.อ.") {
				$PN_CODE = "520";
				$FATHER_NAME = trim(substr($FATHER_NAME, 6));
			} elseif (substr($FATHER_NAME, 0, 4)=="ส.ต.") {
				$PN_CODE = "225";
				$FATHER_NAME = trim(substr($FATHER_NAME, 4));
			} elseif (substr($FATHER_NAME, 0, 4)=="จ.ต.") {
				$PN_CODE = "376";
				$FATHER_NAME = trim(substr($FATHER_NAME, 4));
			} elseif (substr($FATHER_NAME, 0, 4)=="จ.ท.") {
				$PN_CODE = "375";
				$FATHER_NAME = trim(substr($FATHER_NAME, 4));
			} elseif (substr($FATHER_NAME, 0, 4)=="จ.อ.") {
				$PN_CODE = "374";
				$FATHER_NAME = trim(substr($FATHER_NAME, 4));
			} elseif (substr($FATHER_NAME, 0, 4)=="จสต.") {
				$PN_CODE = "222";
				$FATHER_NAME = trim(substr($FATHER_NAME, 4));
			} elseif (substr($FATHER_NAME, 0, 4)=="จสอ.") {
				$PN_CODE = "220";
				$FATHER_NAME = trim(substr($FATHER_NAME, 4));
			} elseif (substr($FATHER_NAME, 0, 4)=="ด.ต.") {
				$PN_CODE = "673";
				$FATHER_NAME = trim(substr($FATHER_NAME, 4));
			} elseif (substr($FATHER_NAME, 0, 3)=="ดต.") {
				$PN_CODE = "673";
				$FATHER_NAME = trim(substr($FATHER_NAME, 3));
			} elseif (substr($FATHER_NAME, 0, 8)=="ดาบตำรวจ") {
				$PN_CODE = "673";
				$FATHER_NAME = trim(substr($FATHER_NAME, 8));
			} elseif (substr($FATHER_NAME, 0, 4)=="น.ต.") {
				$PN_CODE = "512";
				$FATHER_NAME = trim(substr($FATHER_NAME, 4));
			} elseif (substr($FATHER_NAME, 0, 4)=="น.ท.") {
				$PN_CODE = "510";
				$FATHER_NAME = trim(substr($FATHER_NAME, 4));
			} elseif (substr($FATHER_NAME, 0, 4)=="น.อ.") {
				$PN_CODE = "508";
				$FATHER_NAME = trim(substr($FATHER_NAME, 4));
			} elseif (substr($FATHER_NAME, 0, 4)=="ร.ต.") {
				$PN_CODE = "218";
				$FATHER_NAME = trim(substr($FATHER_NAME, 4));
			} elseif (substr($FATHER_NAME, 0, 4)=="ร.ท.") {
				$PN_CODE = "216";
				$FATHER_NAME = trim(substr($FATHER_NAME, 4));
			} elseif (substr($FATHER_NAME, 0, 4)=="ร.อ.") {
				$PN_CODE = "214";
				$FATHER_NAME = trim(substr($FATHER_NAME, 4));
			} elseif (substr($FATHER_NAME, 0, 4)=="น.พ.") {
				$PN_CODE = "134";
				$FATHER_NAME = trim(substr($FATHER_NAME, 4));
			} elseif (substr($FATHER_NAME, 0, 3)=="นท.") {
				$PN_CODE = "510";
				$FATHER_NAME = trim(substr($FATHER_NAME, 3));
			} elseif (substr($FATHER_NAME, 0, 11)=="พ.อ.(พิเศษ)") {
				$PN_CODE = "206";
				$FATHER_NAME = trim(substr($FATHER_NAME, 11));
			} elseif (substr($FATHER_NAME, 0, 4)=="พ.อ.") {
				$PN_CODE = "208";
				$FATHER_NAME = trim(substr($FATHER_NAME, 4));
			} elseif (substr($FATHER_NAME, 0, 4)=="พ.ท.") {
				$PN_CODE = "210";
				$FATHER_NAME = trim(substr($FATHER_NAME, 4));
			} elseif (substr($FATHER_NAME, 0, 6)=="พ.ต.อ.") {
				$PN_CODE = "661";
				$FATHER_NAME = trim(substr($FATHER_NAME, 6));
			} elseif (substr($FATHER_NAME, 0, 6)=="พ.ต.ท.") {
				$PN_CODE = "663";
				$FATHER_NAME = trim(substr($FATHER_NAME, 6));
			} elseif (substr($FATHER_NAME, 0, 6)=="พ.ต.ต.") {
				$PN_CODE = "665";
				$FATHER_NAME = trim(substr($FATHER_NAME, 6));
			} elseif (substr($FATHER_NAME, 0, 4)=="พ.ต.") {
				$PN_CODE = "212";
				$FATHER_NAME = trim(substr($FATHER_NAME, 4));
			} elseif (substr($FATHER_NAME, 0, 6)=="พ.จ.อ.") {
				$PN_CODE = "371";
				$FATHER_NAME = trim(substr($FATHER_NAME, 6));
			} elseif (substr($FATHER_NAME, 0, 6)=="พ.จ.ท.") {
				$PN_CODE = "372";
				$FATHER_NAME = trim(substr($FATHER_NAME, 6));
			}  //        มีอีกเยอะ---------------------------------------------------------------
			$arr_temp = explode(" ", $FATHER_NAME);
			$FATHER_NAME = $arr_temp[0];
			$FATHER_SURNAME = $arr_temp[1].$arr_temp[2];
			$MATHER_NAME = trim($data[MATHER_NAME]);
			$OMATHER_LNAME = trim($data_dpis[OMATHER_LNAME]);
			$PARENT_ADDR = trim($data[PARENT_ADDR]); 
			$MATE_NAME = trim($data[MATE_NAME]);
			$OMATE_LNAME = trim($data[OMATE_LNAME]); 
			$WEDDING_DATE = trim($data[WEDDING_DATE]);
			$MR_DOCDATE = ($WEDDING_DATE)? ((substr($WEDDING_DATE, 4, 4) - 543) ."-". substr($WEDDING_DATE, 2, 2) ."-". substr($WEDDING_DATE, 0, 2)) : "-";
			$CHILD_NO = trim($data[CHILD_NO]);
			$EMER_ADDRESS = trim($data[EMER_ADDRESS]);
			if ($EMER_ADDRESS){
				$cmd = " UPDATE PER_PERSONAL SET PER_CONTACT_PERSON = '$EMER_ADDRESS' WHERE PER_ID = $PER_ID ";
				$db_dpis->send_cmd($cmd);
	//			echo "$cmd<br>==================<br>";
	//			$db_dpis->show_error();
	//			echo "<br>end ". ++$i  ."=======================<br>";
			} // end if

			if ($FATHER_NAME || $FATHER_SURNAME) {
				$FML_GENDER = 1;
				$FML_TYPE = 1;
				$FML_ALIVE = 1;

				$cmd = " insert into PER_FAMILY (FML_ID, PER_ID, FML_TYPE, PN_CODE, FML_NAME, FML_SURNAME, FML_CARDNO, 
								FML_GENDER, FML_BIRTHDATE,	FML_ALIVE, RE_CODE, MR_CODE, UPDATE_USER, UPDATE_DATE)
								values ($MAX_ID, $PER_ID, $FML_TYPE, '$PN_CODE', '$FATHER_NAME', '$FATHER_SURNAME',  '$FML_CARDNO', 
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

			$PN_CODE = "";
			if (substr($MATHER_NAME, 0, 3)=="นาย" || substr($MATHER_NAME, 0, 3)=="นาง") {
				$PN_CODE = "005";
				$MATHER_NAME = substr($MATHER_NAME, 3);
			} elseif (substr($MATHER_NAME, 0, 3)=="นาง") {
				$PN_CODE = "005";
				$MATHER_NAME = substr($MATHER_NAME, 3);
			}
			$arr_temp = explode(" ", $MATHER_NAME);
			$MATHER_NAME = $arr_temp[0];
			$MATHER_SURNAME = $arr_temp[1].$arr_temp[2];
			$FML_GENDER = 2;
			$FML_TYPE = 2;
			$FML_ALIVE = 1;

			if ($MATHER_NAME || $MATHER_SURNAME) {
				$cmd = " insert into PER_FAMILY (FML_ID, PER_ID, FML_TYPE, PN_CODE, FML_NAME, FML_SURNAME, FML_CARDNO, 
								FML_GENDER, FML_BIRTHDATE,	FML_ALIVE, RE_CODE, MR_CODE, FML_OLD_SURNAME, UPDATE_USER, UPDATE_DATE)
								values ($MAX_ID, $PER_ID, $FML_TYPE, '$PN_CODE', '$MATHER_NAME', '$MATHER_SURNAME',  '$FML_CARDNO', 
								$FML_GENDER, '$FML_BIRTHDATE', $FML_ALIVE, '$RE_CODE', '$MR_CODE', '$OMATHER_LNAME', $SESS_USERID, '$UPDATE_DATE') ";
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

			$PN_CODE = "";
			if (substr($MATE_NAME, 0, 3)=="นาย") {
				$PN_CODE = "003";
				$MATE_NAME = substr($MATE_NAME, 3);
			} elseif (substr($MATE_NAME, 0, 3)=="นาง") {
				$PN_CODE = "005";
				$MATE_NAME = substr($MATE_NAME, 3);
			} elseif (substr($MATE_NAME, 0, 4)=="นาาง") {
				$PN_CODE = "005";
				$MATE_NAME = substr($MATE_NAME, 4);
			} elseif (substr($MATE_NAME, 0, 4)=="น.ส.") {
				$PN_CODE = "004";
				$MATE_NAME = substr($MATE_NAME, 4);
			} elseif (substr($MATE_NAME, 0, 6)=="ม.ร.ว.") {
				$PN_CODE = "121";
				$MATE_NAME = trim(substr($MATE_NAME, 6));
			} elseif (substr($MATE_NAME, 0, 4)=="ส.อ.") {
				$PN_CODE = "223";
				$MATE_NAME = trim(substr($MATE_NAME, 4));
			} elseif (substr($MATE_NAME, 0, 3)=="สอ.") {
				$PN_CODE = "223";
				$MATE_NAME = trim(substr($MATE_NAME, 3));
			} elseif (substr($MATE_NAME, 0, 4)=="ส.ท.") {
				$PN_CODE = "224";
				$MATE_NAME = trim(substr($MATE_NAME, 4));
			} elseif (substr($MATE_NAME, 0, 3)=="สท.") {
				$PN_CODE = "224";
				$MATE_NAME = trim(substr($MATE_NAME, 3));
			} elseif (substr($MATE_NAME, 0, 6)=="ร.ต.อ.") {
				$PN_CODE = "667";
				$MATE_NAME = trim(substr($MATE_NAME, 6));
			} elseif (substr($MATE_NAME, 0, 6)=="ร.ต.ท.") {
				$PN_CODE = "669";
				$MATE_NAME = trim(substr($MATE_NAME, 6));
			} elseif (substr($MATE_NAME, 0, 6)=="ร.ต.ต.") {
				$PN_CODE = "671";
				$MATE_NAME = trim(substr($MATE_NAME, 6));
			} elseif (substr($MATE_NAME, 0, 6)=="ส.ต.อ.") {
				$PN_CODE = "675";
				$MATE_NAME = trim(substr($MATE_NAME, 6));
			} elseif (substr($MATE_NAME, 0, 4)=="สตอ.") {
				$PN_CODE = "675";
				$MATE_NAME = trim(substr($MATE_NAME, 4));
			} elseif (substr($MATE_NAME, 0, 6)=="ส.ตท. " || substr($MATE_NAME, 0, 6)=="ส.ต.ท.") {
				$PN_CODE = "676";
				$MATE_NAME = trim(substr($MATE_NAME, 6));
			} elseif (substr($MATE_NAME, 0, 4)=="สตท.") {
				$PN_CODE = "676";
				$MATE_NAME = trim(substr($MATE_NAME, 4));
			} elseif (substr($MATE_NAME, 0, 6)=="ส.ต.ต.") {
				$PN_CODE = "677";
				$MATE_NAME = trim(substr($MATE_NAME, 6));
			} elseif (substr($MATE_NAME, 0, 4)=="สตต.") {
				$PN_CODE = "677";
				$MATE_NAME = trim(substr($MATE_NAME, 4));
			} elseif (substr($MATE_NAME, 0, 6)=="จ.ส.ต." || substr($MATE_NAME, 0, 6)=="จ.ต.ส.") {
				$PN_CODE = "222";
				$MATE_NAME = trim(substr($MATE_NAME, 6));
			} elseif (substr($MATE_NAME, 0, 6)=="จ.ส.ท.") {
				$PN_CODE = "221";
				$MATE_NAME = trim(substr($MATE_NAME, 6));
			} elseif (substr($MATE_NAME, 0, 6)=="จ.ส.อ.") {
				$PN_CODE = "220";
				$MATE_NAME = trim(substr($MATE_NAME, 6));
			} elseif (substr($MATE_NAME, 0, 6)=="พ.อ.อ.") {
				$PN_CODE = "520";
				$MATE_NAME = trim(substr($MATE_NAME, 6));
			} elseif (substr($MATE_NAME, 0, 4)=="ส.ต.") {
				$PN_CODE = "225";
				$MATE_NAME = trim(substr($MATE_NAME, 4));
			} elseif (substr($MATE_NAME, 0, 4)=="จ.ต.") {
				$PN_CODE = "376";
				$MATE_NAME = trim(substr($MATE_NAME, 4));
			} elseif (substr($MATE_NAME, 0, 4)=="จ.ท.") {
				$PN_CODE = "375";
				$MATE_NAME = trim(substr($MATE_NAME, 4));
			} elseif (substr($MATE_NAME, 0, 4)=="จ.อ.") {
				$PN_CODE = "374";
				$MATE_NAME = trim(substr($MATE_NAME, 4));
			} elseif (substr($MATE_NAME, 0, 4)=="จสต.") {
				$PN_CODE = "222";
				$MATE_NAME = trim(substr($MATE_NAME, 4));
			} elseif (substr($MATE_NAME, 0, 4)=="จสอ.") {
				$PN_CODE = "220";
				$MATE_NAME = trim(substr($MATE_NAME, 4));
			} elseif (substr($MATE_NAME, 0, 4)=="ด.ต.") {
				$PN_CODE = "673";
				$MATE_NAME = trim(substr($MATE_NAME, 4));
			} elseif (substr($MATE_NAME, 0, 3)=="ดต.") {
				$PN_CODE = "673";
				$MATE_NAME = trim(substr($MATE_NAME, 3));
			} elseif (substr($MATE_NAME, 0, 8)=="ดาบตำรวจ") {
				$PN_CODE = "673";
				$MATE_NAME = trim(substr($MATE_NAME, 8));
			} elseif (substr($MATE_NAME, 0, 4)=="น.ต.") {
				$PN_CODE = "512";
				$MATE_NAME = trim(substr($MATE_NAME, 4));
			} elseif (substr($MATE_NAME, 0, 4)=="น.ท.") {
				$PN_CODE = "510";
				$MATE_NAME = trim(substr($MATE_NAME, 4));
			} elseif (substr($MATE_NAME, 0, 4)=="น.อ.") {
				$PN_CODE = "508";
				$MATE_NAME = trim(substr($MATE_NAME, 4));
			} elseif (substr($MATE_NAME, 0, 4)=="ร.ต.") {
				$PN_CODE = "218";
				$MATE_NAME = trim(substr($MATE_NAME, 4));
			} elseif (substr($MATE_NAME, 0, 4)=="ร.ท.") {
				$PN_CODE = "216";
				$MATE_NAME = trim(substr($MATE_NAME, 4));
			} elseif (substr($MATE_NAME, 0, 4)=="ร.อ.") {
				$PN_CODE = "214";
				$MATE_NAME = trim(substr($MATE_NAME, 4));
			} elseif (substr($MATE_NAME, 0, 4)=="น.พ.") {
				$PN_CODE = "134";
				$MATE_NAME = trim(substr($MATE_NAME, 4));
			} elseif (substr($MATE_NAME, 0, 3)=="นท.") {
				$PN_CODE = "510";
				$MATE_NAME = trim(substr($MATE_NAME, 3));
			} elseif (substr($MATE_NAME, 0, 11)=="พ.อ.(พิเศษ)") {
				$PN_CODE = "206";
				$MATE_NAME = trim(substr($MATE_NAME, 11));
			} elseif (substr($MATE_NAME, 0, 4)=="พ.อ.") {
				$PN_CODE = "208";
				$MATE_NAME = trim(substr($MATE_NAME, 4));
			} elseif (substr($MATE_NAME, 0, 4)=="พ.ท.") {
				$PN_CODE = "210";
				$MATE_NAME = trim(substr($MATE_NAME, 4));
			} elseif (substr($MATE_NAME, 0, 6)=="พ.ต.อ.") {
				$PN_CODE = "661";
				$MATE_NAME = trim(substr($MATE_NAME, 6));
			} elseif (substr($MATE_NAME, 0, 6)=="พ.ต.ท.") {
				$PN_CODE = "663";
				$MATE_NAME = trim(substr($MATE_NAME, 6));
			} elseif (substr($MATE_NAME, 0, 6)=="พ.ต.ต.") {
				$PN_CODE = "665";
				$MATE_NAME = trim(substr($MATE_NAME, 6));
			} elseif (substr($MATE_NAME, 0, 4)=="พ.ต.") {
				$PN_CODE = "212";
				$MATE_NAME = trim(substr($MATE_NAME, 4));
			} elseif (substr($MATE_NAME, 0, 6)=="พ.จ.อ.") {
				$PN_CODE = "371";
				$MATE_NAME = trim(substr($MATE_NAME, 6));
			} elseif (substr($MATE_NAME, 0, 6)=="พ.จ.ท.") {
				$PN_CODE = "372";
				$MATE_NAME = trim(substr($MATE_NAME, 6));
			}
			$arr_temp = explode(" ", $MATE_NAME);
			$MATE_NAME = $arr_temp[0];
			$MATE_SURNAME = $arr_temp[1].$arr_temp[2];
			if ($PER_GENDER == 2) $FML_GENDER = 1;
			elseif ($PER_GENDER == 1) $FML_GENDER = 2;
			$FML_TYPE = 3;
			$FML_ALIVE = 1;

			if ($MATE_NAME || $MATE_SURNAME) {
				$cmd = " insert into PER_FAMILY (FML_ID, PER_ID, FML_TYPE, PN_CODE, FML_NAME, FML_SURNAME, FML_CARDNO, 
								FML_GENDER, FML_BIRTHDATE,	FML_ALIVE, RE_CODE, MR_CODE, MR_DOCDATE, FML_OLD_SURNAME, UPDATE_USER, UPDATE_DATE)
								values ($MAX_ID, $PER_ID, $FML_TYPE, '$PN_CODE', '$MATE_NAME', '$MATE_SURNAME',  '$FML_CARDNO', 
								$FML_GENDER, '$FML_BIRTHDATE', $FML_ALIVE, '$RE_CODE', '$MR_CODE', '$MR_DOCDATE', '$OMATE_LNAME', $SESS_USERID, '$UPDATE_DATE') ";
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
		$db_dpis35->free_result();
		$db_dpis351->free_result();
	} // end if

	if ($command=='CHILD' || $command=='CHILD_EMP'){
		$cmd = " delete from PER_FAMILY where FML_TYPE = 4 ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();

// บุตรข้าราชการ 8714 
		$cmd = " select max(FML_ID) as MAX_ID from PER_FAMILY ";
		$db_dpis1->send_cmd($cmd);
		$data1 = $db_dpis1->get_array();
		$MAX_ID = $data1[MAX_ID] + 1;

		if ($command=='CHILD')
			$cmd = " SELECT a.ID, GOVCHILD_ORDER, GOVCHILD_NAME, GOVCHILD_DATE
							  FROM GOV_CHILD a, COMMON_HISTORY b
							  WHERE a.ID=b.ID
							  ORDER BY a.ID ";
		elseif ($command=='CHILD_EMP')
			$cmd = " SELECT a.ID, EMPCHILD_ORDER as GOVCHILD_ORDER, EMPCHILD_NAME as GOVCHILD_NAME, EMPCHILD_DATE as GOVCHILD_DATE
							  FROM EMP_CHILD a, EMPLOYEE_HISTORY b
							  WHERE a.ID=b.ID
							  ORDER BY a.ID ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "<br>";
		$PER_FAMILY = 0;
		while($data = $db_dpis35->get_array()){
			$PER_ID = trim($data[ID]);
			$FML_TYPE = 4;
			$GOVCHILD_NAME = trim($data[GOVCHILD_NAME]); 
			$GOVCHILD_DATE = trim($data[GOVCHILD_DATE]);
			$FML_BIRTHDATE = ($GOVCHILD_DATE)? ((substr($GOVCHILD_DATE, 4, 4) - 543) ."-". substr($GOVCHILD_DATE, 2, 2) ."-". substr($GOVCHILD_DATE, 0, 2)) : "";
			$FML_NAME = trim($data[GOVCHILD_NAME]);
			$FML_SURNAME = trim($data[CHILD_LNAME]);
			if ($FML_NAME || $FML_SURNAME) {
				$FML_GENDER = trim($data[CHILD_SEX]);
				if (!$FML_GENDER) $FML_GENDER = 0;
				if (!$FML_ALIVE) $FML_ALIVE = 1;

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
		} // end while						

		$cmd = " select count(FML_ID) as COUNT_NEW from PER_FAMILY where FML_TYPE = 4 ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_FAMILY - $PER_FAMILY - $COUNT_NEW<br>";

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();
		$db_dpis351->free_result(); 
	} // end if

	if ($command=='POSITIONHIS'){
// การดำรงตำแหน่ง 571904

		$cmd = " truncate table per_positionhis ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " truncate table per_salaryhis ";
		$db_dpis->send_cmd($cmd);
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

		$cmd = " SELECT a.ID, GOVPOS_ORDER, GOVPOS_C, GOVPOS_STEP, GOVPOS_HISSALARY, GOVPOS_PST_NO, WPOS_CODE, 
                        MPOS_CODE, GOVPOS_DATE, GOVPOS_REFF, GOVPOS_DET, GOVPOS_YEAR, GOVPOS_EVER, 
                        GOVPOS_COMCODE, GOVPOS_LASTFLAG, GOVPOS_WKSTATUS, SECTION_CODE, 
                        DEPT_CODE, TYPE_POS, WPOS_NAME, MPOS_NAME, SECTION_NAME, 
                        DEPT_NAME, FLAG_WKSTATUS_OLDNEW, PO_UPD_C, PO_DEP, 
                        PO_MIN, GOVPOS_PRAMEAUN_LAYER, GOVPOS_PRAMEAUN_PERCENT, GOVPOS_MONEY_UP, GOVPOS_MONEY_SPEC, PERSON_ID
                        FROM GOVPOSITION_HISTORY a, COMMON_HISTORY b
			 			WHERE a.ID = b.ID AND a.ID != '0'  
                        ORDER BY a.ID, GOVPOS_ORDER ";
//			 			WHERE a.ID = '000005737'  
//			 			WHERE a.ID < '000003%' AND a.ID != '0'  

		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "<br>";
		while($data = $db_dpis35->get_array()){
			$PER_ID = trim($data[ID]);
			$GOVPOS_DATE = trim($data[GOVPOS_DATE]);
			$EFFECTIVEDATE = ($GOVPOS_DATE)? ((substr($GOVPOS_DATE, 4, 4) - 543) ."-". substr($GOVPOS_DATE, 2, 2) ."-". substr($GOVPOS_DATE, 0, 2)) : "-";
			$DOCNO = trim($data[GOVPOS_REFF]);
			$DOCNO = str_replace("'", "", $DOCNO);
			$DOCNO = str_replace("  ", " ", $DOCNO);
			$DOCNO = str_replace("  ", " ", $DOCNO);
			$DOCNO = str_replace("  ", " ", $DOCNO);
			$DOCNO = str_replace("  ", " ", $DOCNO);
			$DOCNO = str_replace("  ", " ", $DOCNO);
			$DOCNO = str_replace("  ", " ", $DOCNO);
			$REMARK2 = $DOCNO;
			$DOCDATE = $DOCNO_EDIT = $DOCDATE_EDIT = "";
			if ($DOCNO=="จ.1.5/ 21 /2544 - 72/54/4" || $DOCNO=="จ.1.5/ - 0/0/0") {
				$DOCDATE = "";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง- 8 ก.พ.2551" || $DOCNO=="ปะรกาศกรมทางหลวง - 8 ก.พ.2551" || $DOCNO=="ประกาศกรมทางหลวง ลงวันที่ 8 ก.พ.2551") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "8 ก.พ.2551";
			} elseif ($DOCNO=="จ. 3.3/41/2545 - ลงวันที่ 19 พฤศจิกายน 2545") {
				$DOCNO = "จ. 3.3/41/2545";
				$DOCDATE = "19 พฤศจิกายน 2545";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง 30 ม.ค.50") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "30 ม.ค.50";
			} elseif ($DOCNO=="คค 201/2552 - ลง 1 มิ.ย.2552") {
				$DOCNO = "คค 201/2552";
				$DOCDATE = "1 มิ.ย.2552";
			} elseif ($DOCNO=="ที่ 315/2552 ลวง 12 ต.ค.2552") {
				$DOCNO = "ที่ 315/2552";
				$DOCDATE = "12 ต.ค.2552";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง ลงวันท่ 25 กุมภาพันธ์ 2543") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "25 กุมภาพันธ์ 2543";
			} elseif ($DOCNO=="จ.1.6/14/1/2550 02/02/2550") {
				$DOCNO = "จ.1.6/14/1/2550";
				$DOCDATE = "02/02/2550";
			} elseif ($DOCNO=="จ.1.5/20/35 - 20 มึ.ค.35") {
				$DOCNO = "จ.1.5/20/35";
				$DOCDATE = "20 มึ.ค.35";
			} elseif ($DOCNO=="จ.1.5/178/2551 ลว . 27 ต.ค. 2551") {
				$DOCNO = "จ.1.5/178/2551";
				$DOCDATE = "27 ต.ค. 2551";
			} elseif ($DOCNO=="จ.1.6/67/51 -ก 9 ธ.ค.51") {
				$DOCNO = "จ.1.6/67/51";
				$DOCDATE = "9 ธ.ค.51";
			} elseif ($DOCNO=="จ.1.3/30/2546 ลว. 21 มี.ค. 2546จ.1.1/1/2546 ลว. 8 มกราคม 2546") {
				$DOCNO = "จ.1.3/30/2546 ลว. 21 มี.ค. 2546จ.1.1/1/2546";
				$DOCDATE = "8 มกราคม 2546";
			} elseif ($DOCNO=="จ.1.6/5/2555 - 19ม.ค. 55") {
				$DOCNO = "จ.1.6/5/2555";
				$DOCDATE = "19 ม.ค. 55";
			} elseif ($DOCNO=="ที่ จ.1.1/70/2554 ลว เม.ย. 54") {
				$DOCNO = "ที่ จ.1.1/70/2554";
				$DOCDATE = "12 เม.ย. 54";
			} elseif ($DOCNO=="จ.1.5/ 115/2551 ลว.25 สิงหาคม2551") {
				$DOCNO = "จ.1.5/ 115/2551";
				$DOCDATE = "25 สิงหาคม 2551";
			} elseif ($DOCNO=="จ.1.1/76/53 - 2 มิ.ย.53 แก้ไขคำสั่ง จ.1.1/17/53 - 23 ก.พ.53") {
				$DOCNO = "จ.1.1/76/53";
				$DOCDATE = "2 มิ.ย.53";
				$DOCNO_EDIT = "จ.1.1/17/53";
				$DOCDATE_EDIT = "2010-02-23";
			} elseif ($DOCNO=="จ.1.6/สทล.13/1/2553 ลว.1/8 มกราคม 2553") {
				$DOCNO = "จ.1.6/สทล.13/1/2553";
				$DOCDATE = "18 มกราคม 2553";
			} elseif ($DOCNO=="จ.1.6/สทล.10 ขท.กาญจนบุรี-สุพรรณบุรี(ที่ 2).1/1496/2556 - 25 ธ.ค.56") {
				$DOCNO = "จ.1.6/สทล.10 ขท.กาญจนบุรี-สุพรรณบุรี(ที่ 2).1/1496/2556";
				$DOCDATE = "25 ธ.ค.56";
			} elseif ($DOCNO=="จ.1.1/32/2552 ลว . 23 ก.พ.2552") {
				$DOCNO = "จ.1.1/32/2552";
				$DOCDATE = "23 ก.พ.2552";
			} elseif ($DOCNO=="คำสั่งย้ายข้าราชการ ที่ จ.1.1/ 32 / 2555 ลวงวันที่ 15 มีนาคม 2555") {
				$DOCNO = "คำสั่งย้ายข้าราชการ ที่ จ.1.1/ 32 / 2555";
				$DOCDATE = "15 มีนาคม 2555";
			} elseif ($DOCNO=="จ.1.6/8/2556 - 26 ก.พ2556.") {
				$DOCNO = "จ.1.6/8/2556";
				$DOCDATE = "26 ก.พ2556";
			} elseif ($DOCNO=="จ.1.6/สทล.10 ขท.กาญจนบุรี-สุพรรณบุรี(ที่2).1/1496/2556 - 25 ธ.ค.56") {
				$DOCNO = "จ.1.6/สทล.10 ขท.กาญจนบุรี-สุพรรณบุรี(ที่2).1/1496/2556";
				$DOCDATE = "25 ธ.ค.56";
			} elseif ($DOCNO=="จ. 1.1/2/5/2545 ลว. 1พ.ค. 2545จ. 1.1/ 2/9 /2545 ลว. 6 ก.ย. 2545") {
				$DOCNO = "จ. 1.1/2/5/2545 ลว. 1พ.ค. 2545จ. 1.1/ 2/9 /2545";
				$DOCDATE = "6 ก.ย. 2545";
			} elseif ($DOCNO=="จ.1.3/80/255 -ลว 17 เม.ย. 2555") {
				$DOCNO = "จ.1.3/80/255";
				$DOCDATE = "17 เม.ย. 2555";
			} elseif ($DOCNO=="คค 0603/3919 -ลงวันที่ 25 เม.ย.2543") {
				$DOCNO = "คค 0603/3919";
				$DOCDATE = "25 เม.ย.2543";
			} elseif ($DOCNO=="จ.1.3/3/2550 -ลว. 4 ม.ค.2550") {
				$DOCNO = "จ.1.3/3/2550";
				$DOCDATE = "4 ม.ค.2550";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง 3 พ.ย.43") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "3 พ.ย.43";
			} elseif ($DOCNO=="จ.1.6/39/2551  27 ต.ค.2551") {
				$DOCNO = "จ.1.6/39/2551";
				$DOCDATE = "27 ต.ค.2551";
			} elseif ($DOCNO=="จ.1.5/162/2551 ลว . 13 ต.ค. 2551") {
				$DOCNO = "จ.1.5/162/2551";
				$DOCDATE = "13 ต.ค. 2551";
			} elseif ($DOCNO=="จ.1.5/159/2551 ลว . 10 ต.ค. 2551") {
				$DOCNO = "จ.1.5/159/2551";
				$DOCDATE = "10 ต.ค. 2551";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง 3 มี.ค.2549") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "3 มี.ค.2549";
			} elseif ($DOCNO=="จ.1.5/105/2551ลว . 30 ก.ค. 2551") {
				$DOCNO = "จ.1.5/105/2551";
				$DOCDATE = "30 ก.ค. 2551";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง 3 มี.ค.2549") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "3 มี.ค.2549";
			} elseif ($DOCNO=="ประกาศกรมทางหลวงชลบท 8 มิ.ย.52") {
				$DOCNO = "ประกาศกรมทางหลวงชนบท";
				$DOCDATE = "8 มิ.ย.52";
			} elseif ($DOCNO=="ศย.ที่ 540/51 - 14 พค.51 (ตามแผนอัตรากำลัง 3 ปี)") {
				$DOCNO = "ศย.ที่ 540/51 (ตามแผนอัตรากำลัง 3 ปี)";
				$DOCDATE = "14 พค.51";
			} elseif ($DOCNO=="ที่ จ.1.5/ /2557 ลว เม.ย. 57") {
				$DOCNO = "ที่ จ.1.5/ /2557";
				$DOCDATE = "00 เม.ย. 57";
			} elseif ($DOCNO=="ที่ จ.2.1/ /2555 ลว มี.ค. 55") {
				$DOCNO = "ที่ จ.2.1/ /2555";
				$DOCDATE = "00 มี.ค. 55";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง 8 ก.พ.2551") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "8 ก.พ.2551";
			} elseif ($DOCNO=="คำสั่งที่ จ.1.6/1/2543 ลว. 0 0") {
				$DOCNO = "คำสั่งที่ จ.1.6/1/2543";
				$DOCDATE = "";
			} elseif ($DOCNO=="คำสั่งที่ จ.2.1/ 186 /2542 ลว. 0 0") {
				$DOCNO = "คำสั่งที่ จ.2.1/ 186 /2542";
				$DOCDATE = "";
			} elseif ($DOCNO=="คำสั่งที่ จ.2.1/198/2542 ลว. 0 0") {
				$DOCNO = "คำสั่งที่ จ.2.1/198/2542";
				$DOCDATE = "";
			} elseif ($DOCNO=="ที่ จ.1.5/159/2555 ลว 6 ธ .ค. 55") {
				$DOCNO = "ที่ จ.1.5/159/2555";
				$DOCDATE = "6 ธ.ค. 55";
			} elseif ($DOCNO=="จ.1.6/สทล.4/2/2553-8 ก.พ25.53") {
				$DOCNO = "จ.1.6/สทล.4/2/2553";
				$DOCDATE = "8 ก.พ2553";
			} elseif ($DOCNO=="ศก.ที่ 185/54 - 9 มี.ค. 54 เพิกถอน ศก.ที่ 820/53 - 30 ธ.ค. 53") {
				$DOCNO = "ศก.ที่ 185/54 - 9 มี.ค. 54 เพิกถอน ศก.ที่ 820/53";
				$DOCDATE = "30 ธ.ค. 53";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง ณ วันที่ ท 27 มกราคม 2553") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "27 มกราคม 2553";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง ณ วันที่ 15 มีนาคม 2553") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "15 มีนาคม 2553";
			} elseif ($DOCNO=="-217-29/09/15") {
				$DOCNO = "217";
				$DOCDATE = "29/09/15";
			} elseif ($DOCNO=="-จ.1.1/5/2541-26/03/2541") {
				$DOCNO = "จ.1.1/5/2541";
				$DOCDATE = "26/03/2541";
			} elseif ($DOCNO=="-จ.1.5/43/2542-28/09/42 //") {
				$DOCNO = "จ.1.5/43/2542";
				$DOCDATE = "28/09/42";
			} elseif ($DOCNO=="1/139/11-มิย.11") {
				$DOCNO = "1/139/11";
				$DOCDATE = "00 มิ.ย. 11";
			} elseif ($DOCNO=="180-34-07/02/34") {
				$DOCNO = "180-34";
				$DOCDATE = "07/02/34";
			} elseif ($DOCNO=="3962/33-05/102533") {
				$DOCNO = "3962/33";
				$DOCDATE = "05/10/2533";
			} elseif ($DOCNO=="73/35-3009/35") {
				$DOCNO = "73/35";
				$DOCDATE = "30/09/35";
			} elseif ($DOCNO=="747/21-12/096/21") {
				$DOCNO = "747/21";
				$DOCDATE = "12/09/21";
			} elseif ($DOCNO=="774/-782(17)3424/17 - 26/") {
				$DOCNO = "774/-782(17)3424/17";
				$DOCDATE = "26/10/2518";
			} elseif ($DOCNO=="ก.2152/37-23/092537") {
				$DOCNO = "ก.2152/37";
				$DOCDATE = "23/09/2537";
			} elseif ($DOCNO=="กรมฯที่1412/36-18/082536") {
				$DOCNO = "กรมฯที่1412/36";
				$DOCDATE = "18/08/2536";
			} elseif ($DOCNO=="กรมชลประทานที่ข.331/21-20") {
				$DOCNO = "กรมชลประทานที่ข.331/21";
				$DOCDATE = "20/03/2521";
			} elseif ($DOCNO=="กรมส่งเสริมสหกรณ์945/22-1") {
				$DOCNO = "กรมส่งเสริมสหกรณ์945/22-1";
				$DOCDATE = "";
			} elseif ($DOCNO=="ค.618/46-10/1146") {
				$DOCNO = "ค.618/46";
				$DOCDATE = "10/11/46";
			} elseif ($DOCNO=="คค.0602/จ.1/12779-29-07/2") {
				$DOCNO = "คค.0602/จ.1/12779";
				$DOCDATE = "29/07/2524";
			} elseif ($DOCNO=="คค.0602/จ.1/6069-12/102/2") {
				$DOCNO = "คค.0602/จ.1/6069";
				$DOCDATE = "12/02/2524";
			} elseif ($DOCNO=="คค359/2540-06/1140") {
				$DOCNO = "คค359/2540";
				$DOCDATE = "06/11/40";
			} elseif ($DOCNO=="คส.ผม.ที่367/42-27/092542") {
				$DOCNO = "คส.ผม.ที่367/42";
				$DOCDATE = "27/09/2542";
			} elseif ($DOCNO=="คส.สป.ที่8/34-07/012534") {
				$DOCNO = "คส.สป.ที่8/34";
				$DOCDATE = "07/01/2534";
			} elseif ($DOCNO=="คสจ.ปข.2327/2538ลว(2ขั้น)") {
				$DOCNO = "คสจ.ปข.2327/2538";
				$DOCDATE = "";
			} elseif ($DOCNO=="คำสั่ง ที่ 299/47-26") {
				$DOCNO = "คำสั่ง ที่ 299/47";
				$DOCDATE = "26/05/2547";
			} elseif ($DOCNO=="คำสั่งกรมที่ดิน ที่ 2197-2552-05/10/2552") {
				$DOCNO = "คำสั่งกรมที่ดิน ที่ 2197-2552";
				$DOCDATE = "05/10/2552";
			} elseif ($DOCNO=="คำสั่งที่ จ. 1.1/14/2543-13/01/25433/1/2543 ลว. 17 ม.ค. 2543") {
				$DOCNO = "คำสั่งที่ จ. 1.1/14/2543-13/01/25433/1/2543";
				$DOCDATE = "17 ม.ค. 2543";
			} elseif ($DOCNO=="คำสั่งปท.ที่212/2543-26/09/2543180/2542-15/09/2542") {
				$DOCNO = "คำสั่งปท.ที่212/2543-26/09/2543180/2542";
				$DOCDATE = "15/09/2542";
			} elseif ($DOCNO=="คำสั่งศก.ที่226/2548-04/2548") {
				$DOCNO = "คำสั่งศก.ที่226/2548";
				$DOCDATE = "01/04/2548";
			} elseif ($DOCNO=="จ-.1.5/14/2541-30/09/41") {
				$DOCNO = "จ-.1.5/14/2541";
				$DOCDATE = "30/09/41";
			} elseif ($DOCNO=="จ.1.6/ขท.น่าน2/6/2556 ลว. ธ.ค.2556") {
				$DOCNO = "จ.1.6/ขท.น่าน2/6/2556";
				$DOCDATE = "00 ธ.ค. 2556";
			} elseif ($DOCNO=="จ.2.1/ 63 /2552 ลว. 30") {
				$DOCNO = "จ.2.1/ 63 /2552";
				$DOCDATE = "30 ก.ค. 2552";
			} elseif ($DOCNO=="จ.2.1/135-20-04/11/20") {
				$DOCNO = "จ.2.1/135-20";
				$DOCDATE = "04/11/20";
			} elseif ($DOCNO=="จ.2.1/18-39-25/01/39") {
				$DOCNO = "จ.2.1/18-39";
				$DOCDATE = "25/01/39";
			} elseif ($DOCNO=="จ.2.1/193-42-27/12/42") {
				$DOCNO = "จ.2.1/193-42";
				$DOCDATE = "27/12/42";
			} elseif ($DOCNO=="ตัดโอนตำแหน่งและแต่งตั้งข้าราชการตามกฎกระทรวงส่วนราชการกรมทางหลวง") {
				$DOCNO = "ตัดโอนตำแหน่งและแต่งตั้งข้าราชการตามกฎกระทรวงส่วนราชการกรมทางหลวง";
				$DOCDATE = "";
			} elseif ($DOCNO=="ตัดโอนตำแหน่งและแต่งตั้งข้าราชการให้ ดำรงตำแหน่งตามกฎกระทรวงส่วนราชการกรมทางหลวง" ||		
				$DOCNO=="ตัดโอนตำแหน่งและแต่งตั้งข้าราชการให้ดำรงตำแหน่งตามกฎกระทรวงส่วนราชการกรมทางหลวง") {
				$DOCNO = "ตัดโอนตำแหน่งและแต่งตั้งข้าราชการให้ดำรงตำแหน่งตามกฎกระทรวงส่วนราชการกรมทางหลวง";
				$DOCDATE = "";
			} elseif ($DOCNO=="ทล.1/18-16-10/06/16") {
				$DOCNO = "ทล.1/18-16";
				$DOCDATE = "10/06/16";
			} elseif ($DOCNO=="ทล.1/188-13-27/04/13") {
				$DOCNO = "ทล.1/188-13";
				$DOCDATE = "27/04/13";
			} elseif ($DOCNO=="ทล.1/25/14-15") {
				$DOCNO = "ทล.1/25/14";
				$DOCDATE = "15/01/14";
			} elseif ($DOCNO=="ทล.1/682-14-21/09/14") {
				$DOCNO = "ทล.1/682-14";
				$DOCDATE = "21/09/14";
			} elseif ($DOCNO=="ทล.จ.1.1/1/28-28-02/01/28") {
				$DOCNO = "ทล.จ.1.1/1/28-28";
				$DOCDATE = "02/01/28";
			} elseif ($DOCNO=="ทล.จ.1.1/112-24-28/05/24") {
				$DOCNO = "ทล.จ.1.1/112-24";
				$DOCDATE = "28/05/24";
			} elseif ($DOCNO=="ทล.จ.1.1/174-34-06/09/34") {
				$DOCNO = "ทล.จ.1.1/174-34";
				$DOCDATE = "06/09/34";
			} elseif ($DOCNO=="ทล.จ.1.1/178/37-02/05/25376/14/37-02/05/37") {
				$DOCNO = "ทล.จ.1.1/178/37-02/05/25376/14/37";
				$DOCDATE = "02/05/37";
			} elseif ($DOCNO=="ทล.จ.1.1/185-32-11/10/32") {
				$DOCNO = "ทล.จ.1.1/185-32";
				$DOCDATE = "11/10/32";
			} elseif ($DOCNO=="ทล.จ.1.1/85-16-25/06/16") {
				$DOCNO = "ทล.จ.1.1/85-16";
				$DOCDATE = "25/06/16";
			} elseif ($DOCNO=="ทล.จ.1.6/14/37-02/05/25371/178/37-02/05/37") {
				$DOCNO = "ทล.จ.1.6/14/37-02/05/25371/178/37";
				$DOCDATE = "02/05/37";
			} elseif ($DOCNO=="ที่ จ.1.1/1/3/2556 ลว 23") {
				$DOCNO = "ที่ จ.1.1/1/3/2556";
				$DOCDATE = "23 ธ.ค. 56";
			} elseif ($DOCNO=="นส.สนย.ที่มท.0301/3762-29") {
				$DOCNO = "นส.สนย.ที่มท.0301/3762-29";
				$DOCDATE = "";
			} elseif ($DOCNO=="ประกาศกรทางหลวง 21/03/2548") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "21/03/2548";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง 15/06/2548") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "15/06/2548";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง 17/03/2548") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "17/03/2548";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง 20/06/2548") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "20/06/2548";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง 21/03/2548") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "21/03/2548";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง 23/03/2548") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "23/03/2548";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง 31/05/2548") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "31/05/2548";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง 8 ก.พ. 2551") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "8 ก.พ. 2551";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง ลว 31/05/2547") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "31/05/2547";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง ลว. มิ.ย.2546") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "00 มิ.ย. 2546";
			} elseif ($DOCNO=="ปรับตามพรบ.ฉบับที่ 11-251") {
				$DOCNO = "ปรับตามพรบ.ฉบับที่ 11-251";
				$DOCDATE = "";
			} elseif ($DOCNO=="ลาศึกษา 08/2515-08/2517") {
				$DOCNO = "ลาศึกษา 08/2515-08/2517";
				$DOCDATE = "";
			} elseif ($DOCNO=="ศึกษา(2517-2519)") {
				$DOCNO = "ศึกษา(2517-2519)";
				$DOCDATE = "";
			} elseif ($DOCNO=="สนง.ปลัดกระทรวงสาธารณสุข ที่2979/28-0") {
				$DOCNO = "สนง.ปลัดกระทรวงสาธารณสุข ที่2979/28-0";
				$DOCDATE = "";
			} elseif ($DOCNO=="สุขาภิบาลคลองหลวงที่3/32") {
				$DOCNO = "สุขาภิบาลคลองหลวงที่3/32";
				$DOCDATE = "";
			} elseif ($DOCNO=="คค0602/จ.1/4264 - 08/01/2") {
				$DOCNO = "คค0602/จ.1/4264";
				$DOCDATE = "08/01/23";
			} elseif ($DOCNO=="คค0602/จ.1/7248 - 11/03/2") {
				$DOCNO = "คค0602/จ.1/7248";
				$DOCDATE = "11/03/24";
			} elseif ($DOCNO=="คค.0602/จ.1/11392-18/06/2") {
				$DOCNO = "คค.0602/จ.1/11392";
				$DOCDATE = "18/06/23";
			} elseif ($DOCNO=="คค.0602/จ.1/10665 -07/07/") {
				$DOCNO = "คค.0602/จ.1/10665";
				$DOCDATE = "07/07/30";
			} elseif ($DOCNO=="คค 0602/จ.1/13251 - 06/08") {
				$DOCNO = "คค 0602/จ.1/13251";
				$DOCDATE = "06/08/24";
			} elseif ($DOCNO=="คค0602/จ.1/13876 - 17/08/") {
				$DOCNO = "คค0602/จ.1/13876";
				$DOCDATE = "17/08/31";
			} elseif ($DOCNO=="คค.0602/จ.1/14114-18/08/2") {
				$DOCNO = "คค.0602/จ.1/14114";
				$DOCDATE = "18/08/23";
			} elseif ($DOCNO=="คค.0602/จ.1/13593-26/08/3") {
				$DOCNO = "คค.0602/จ.1/13593";
				$DOCDATE = "26/08/30";
			} elseif ($DOCNO=="คค0602/จ.1/15856 - 21/09/") {
				$DOCNO = "คค0602/จ.1/15856";
				$DOCDATE = "21/09/25";
			} elseif ($DOCNO=="คค.0602/จ.1/18014-26/09/3") {
				$DOCNO = "คค.0602/จ.1/18014";
				$DOCDATE = "26/09/32";
			} elseif ($DOCNO=="คค.0602/จ.1/20253-25/10/3") {
				$DOCNO = "คค.0602/จ.1/20253";
				$DOCDATE = "25/10/32";
			} elseif ($DOCNO=="คค.0602/จ.1/1192 - 25/10/") {
				$DOCNO = "คค.0602/จ.1/1192";
				$DOCDATE = "25/10/32";
			} elseif ($DOCNO=="คค0602/จ.1/1585 - 05/11/2") {
				$DOCNO = "คค0602/จ.1/1585";
				$DOCDATE = "05/11/25";
			} elseif ($DOCNO=="ทล.จ.1.5/33/20-23/09/2000") {
				$DOCNO = "ทล.จ.1.5/33/20";
				$DOCDATE = "23/09/20";
			} elseif ($DOCNO=="แบบรายงาน กพ.หมายเลข2-16/" || $DOCNO=="กรมการพัฒนาที่170/32-20/" || $DOCNO=="กรมอาชีวศึดษา1518/32-10/0" || $DOCNO=="ตามแบบรายงานหมายเลข 2-05/" || 
				$DOCNO=="สำนักการโยธาที่12/32-26/0" || $DOCNO=="สพนักการโยธาที่17/31-19/0" || $DOCNO=="บริหารส่วนจว. 326/31-10/0" || $DOCNO=="กรมราชทัณฑ์ที่ 343/30-23" || 
				$DOCNO=="สำนักผังเมือง211/30 - 17/" || $DOCNO=="สนง.พลังงาน๙ที่ 76/30-13/" || $DOCNO=="แบบรายงาน กพ.หมายเลข2-26/" || $DOCNO=="สนง.พลังงาน๙ที่294/29-23" || 
				$DOCNO=="สำนักผังเมือง305/29 - 22/" || $DOCNO=="สำนักผังเมือง249/29 - 22/" || $DOCNO=="แบบรายงาน กพ.หมายเลข2-14/" || $DOCNO=="กรมราชทัณฑ์ที่ 611/29-12/" || 
				$DOCNO=="คำสั่งที่จ.ที่3198/29-07/" || $DOCNO=="ปฏิรูปที่ดินที่780/28-30/" || $DOCNO=="สำนักผังเมือง301/28 - 23/" || $DOCNO=="กรมราชทัณฑ์ที่ 669/28-23/" || 
				$DOCNO=="กรมราชทัณฑ์ที่ 125/29-14/" || $DOCNO=="สปก.ที่กษ.1201/1874 - 07/" || $DOCNO=="กรมราชทัณฑ์ที่ 754/27-30/" || $DOCNO=="กรมส่งเสริม๙374/27 - 27/0" || 
				$DOCNO=="สำนักผังเมือง344/27 - 18/" || $DOCNO=="กรมพลศึกษาที่ 456/27-18/0" || $DOCNO=="สำนักผังเมือง333/27 - 12/" || $DOCNO=="กรมราชทัณฑ์ที่ 881/26-30/" || 
				$DOCNO=="กรมพลศึกษาที่ 644/26-28/0" || $DOCNO=="สาธารณสุขที่ 2898/26-20/0" || $DOCNO=="กรมราชทัณฑ์ที่ 418/26-19/" || $DOCNO=="สำนักผังเมือง264/26 - 13/" || 
				$DOCNO=="แบบรายงาน กพ.หมายเลข2-20/" || $DOCNO=="กรมอาชีวศึกษา1721/25-19/0" || $DOCNO=="สำนักผังเมือง101/25 - 18/" || $DOCNO=="แม่ฮ่องสอนที่ 102/25-17/0" || 
				$DOCNO=="สนง.ปลัดกระทรวง๙21/26-12/" || $DOCNO=="กรมราชทัณฑ์ที่ 937/25-01/" || $DOCNO=="ทล.จ. 1.1/115/25-01/0" || $DOCNO=="กรมราชทัณฑ์ที่ 79/24-29/0" || 
				$DOCNO=="กรมป่าไม้ที่ 226/24 - 23/" || $DOCNO=="แม่ฮ่องสอนที่ 214/24-22/0" || $DOCNO=="กรมสามัญศึกษา 1268/24-15/" || $DOCNO=="ปลัดกระทรวง๙2210/24 - 10/" || 
				$DOCNO=="คำสั่งจังหวัด 591/23-30/0" || $DOCNO=="กรมปศุสัตว์๙ที่398/23-30/" || $DOCNO=="คำสั่งจ.ตรังที่193/23-25/" || $DOCNO=="กรมสามัญศึกษา 5711/23-19/" || 
				$DOCNO=="แบบรายงานกพ.หมายเลข 2-16/" || $DOCNO=="แนบรายงาน กพ.หมายเลข2-16/" || $DOCNO=="ตรวจบ/ชสหกรณ์162/23 - 13/" || $DOCNO=="ปลัดกระทรวง๙74797/23-11/0" || 
				$DOCNO=="คค.0602/จ.1/ 10722 - 04/0" || $DOCNO=="ตรวจบ/ชสหกรณ์102/22 - 27/" || $DOCNO=="จ.สุโขทัยที่1006/22 - 27/" || $DOCNO=="กรมปศุสัตว์ที่217/22-21/0" || 
				$DOCNO=="จ.สุโขทัยที่ 38/22 - 15/0" || $DOCNO=="กรมส่งเสริม๙ที่947/22-13/" || $DOCNO=="มหาวิทยาลัยที่107/23-13/" || $DOCNO=="ตรวจบ/ชสหกรณ์191/22 - 09/" || 
				$DOCNO=="กรมศิลปากรที่395/22 - 04/" || $DOCNO=="กรมปศุสัตว์ที่126/22-01/0" || $DOCNO=="กรมชลประทานที่ข710/21-29/" || $DOCNO=="กรมชลประทานที่341/21-24/0" || 
				$DOCNO=="กรมชลประทานที่342/21-24/0" || $DOCNO=="กรมชลประทานที่ 331/21-20/" || $DOCNO=="กรมศิลปากรที่688/21 - 15/" || $DOCNO=="กรมการค้าภายใน 116/21-10/" || 
				$DOCNO=="คค.0602/จ.1/1621/21 - 06/" || $DOCNO=="สปก.ที่ก.ส.0301/12506-04/" || $DOCNO=="กรมการค้าภายใน 152/20-23/" || $DOCNO=="กรมที่ดินที่ 1139/20-02/0" || 
				$DOCNO=="กรมการค้าภายใน 282/19-29/" || $DOCNO=="กรมที่ดินที่ 96/20 - 27/0" || $DOCNO=="กรมการค้าภายใน 224/19-24/" || $DOCNO=="ยย.ทบ.(เฉพาะ)ที่1/19-14/0" || 
				$DOCNO=="กรมการค้าภายใน 43/19-06/0" || $DOCNO=="ยย.ทบ.(เฉพาะ)ที่13/19-01/" || $DOCNO=="สำนักผังเมืองที่78/18-25/" || $DOCNO=="ยย.ทบ.(เฉพาะ)ที่7/18-16/0" || 
				$DOCNO=="ทบ.(เฉพาะ)ที่ 197/19-04/0" || $DOCNO=="ทบ.ที่ กห.0315/11118-27/0" || $DOCNO=="ทบ.(เฉพาะ)ที่ 1071/17-22/" || $DOCNO=="คำสั่งกรมที่ 1/406/15-9/0" || 
				$DOCNO=="ปลัดกระทรวงอุต๙292/15-22/" || $DOCNO=="คำสั่งกรมที่1/610/14-02/0") {
				$DOCDATE = "";
			} elseif ($DOCNO=="ทล.จ.1.5/28/27-18//9/27") {
				$DOCNO = "ทล.จ.1.5/28/27";
				$DOCDATE = "18/09/27";
			} elseif ($DOCNO=="คค.0602/จ.1/117467 - 12/0") {
				$DOCNO = "คค.0602/จ.1/11767";
				$DOCDATE = "12/06/27";
			} elseif ($DOCNO=="ทล.จ.1.1/112-24/-28/05/24") {
				$DOCNO = "ทล.จ.1.1/112/24";
				$DOCDATE = "28/05/24";
			} elseif ($DOCNO=="ทล.จ. 2.1/106/23-25/0") {
				$DOCNO = "ทล.จ. 2.1/106/23";
				$DOCDATE = "25/03/23";
			} elseif ($DOCNO=="ทล.จ.2.1/255/22-30/") {
				$DOCNO = "ทล.จ.2.1/255/22";
				$DOCDATE = "30/11/22";
			} elseif ($DOCNO=="อทล.คค0602/จ.1/10211-24/0") {
				$DOCNO = "อทล.คค0602/จ.1/10211";
				$DOCDATE = "24/07/21";
			} elseif ($DOCNO=="คค.0602/จ.1/102081 - 24/0") {
				$DOCNO = "คค.0602/จ.1/102081";
				$DOCDATE = "24/07/21";
			} elseif ($DOCNO=="อทล.คค.0602/จ.1/8377-07/0") {
				$DOCNO = "อทล.คค.0602/จ.1/8377";
				$DOCDATE = "07/06/21";
			} elseif ($DOCNO=="คำสั่งกรมที่11/85/16-25/0") {
				$DOCNO = "คำสั่งกรมที่11/85/16";
				$DOCDATE = "25/05/16";
			} elseif ($DOCNO=="คำสั่งกรมที่1/567/15-30/0") {
				$DOCNO = "คำสั่งกรมที่1/567/15";
				$DOCDATE = "30/06/15";
			} elseif ($DOCNO=="คำสั่งกรมที่1/435/14-29/0") {
				$DOCNO = "คำสั่งกรมที่1/435/14";
				$DOCDATE = "29/06/14";
			} elseif ($DOCNO=="คำสั่งกรมที่1/309/12-15/0") {
				$DOCNO = "คำสั่งกรมที่1/309/12";
				$DOCDATE = "15/07/12";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง ลว. 26 เมษยน พ.ศ.2556") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "26 เม.ย. 56";
			} elseif ($DOCNO=="จ.1.3/133/57 - 6 ส.๕. 57") {
				$DOCNO = "จ.1.3/133/57";
				$DOCDATE = "6 ส.ค.57";
			} elseif ($DOCNO=="ที่ จ.2.1/37/2557 ลว 4 เม.ษ. 57") {
				$DOCNO = "จ.2.1/37/2557";
				$DOCDATE = "4 เม.ย. 57";
			} elseif ($DOCNO=="จง1.3/สทล.11/2/2558 - 21 ม..ค.2558") {
				$DOCNO = "จ.1.3/สทล.11/2/2558";
				$DOCDATE = "21 ม.ค. 58";
			} elseif ($DOCNO=="คส.จ.สุรินทร์ที่ 2727/30 - 28 ส.ค.30 , 3255/31 - 26 ก.ย.31") {
				$DOCNO = "คส.จ.สุรินทร์ที่ 2727/30 - 28 ส.ค.30 , 3255/31";
				$DOCDATE = "26 ก.ย. 31";
			} elseif ($DOCNO=="คำสั่งจังหวัดลพบุรี 809/48 - 2 มิงย. 48") {
				$DOCNO = "คำสั่งจังหวัดลพบุรี 809/48";
				$DOCDATE = "2 มิ.ย. 48";
			} elseif ($DOCNO=="จ.นราธิวาส 1183/53 ลว.24 พ.ค.53 แก้โดย จ.นราธิวาส 5331/53 ลว.30 ธ.ค.53") {
				$DOCNO = "จ.นราธิวาส 5331/53";
				$DOCDATE = "30 ธ.ค. 53";
				$DOCNO_EDIT = "จ.นราธิวาส 1183/53";
				$DOCDATE_EDIT = "24 พ.ค. 53";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง 30 ม.ค.50") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "30 ม.ค.50";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง 30 ม.ค.50") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "30 ม.ค.50";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง 30 ม.ค.50") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "30 ม.ค.50";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง 30 ม.ค.50") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "30 ม.ค.50";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง 30 ม.ค.50") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "30 ม.ค.50";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง 30 ม.ค.50") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "30 ม.ค.50";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง 30 ม.ค.50") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "30 ม.ค.50";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง 30 ม.ค.50") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "30 ม.ค.50";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง 30 ม.ค.50") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "30 ม.ค.50";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง 30 ม.ค.50") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "30 ม.ค.50";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง 30 ม.ค.50") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "30 ม.ค.50";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง 30 ม.ค.50") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "30 ม.ค.50";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง 30 ม.ค.50") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "30 ม.ค.50";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง 30 ม.ค.50") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "30 ม.ค.50";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง 30 ม.ค.50") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "30 ม.ค.50";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง 30 ม.ค.50") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "30 ม.ค.50";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง 30 ม.ค.50") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "30 ม.ค.50";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง 30 ม.ค.50") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "30 ม.ค.50";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง 30 ม.ค.50") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "30 ม.ค.50";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง 30 ม.ค.50") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "30 ม.ค.50";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง 30 ม.ค.50") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "30 ม.ค.50";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง 30 ม.ค.50") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "30 ม.ค.50";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง 30 ม.ค.50") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "30 ม.ค.50";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง 30 ม.ค.50") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "30 ม.ค.50";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง 30 ม.ค.50") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "30 ม.ค.50";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง 30 ม.ค.50") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "30 ม.ค.50";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง 30 ม.ค.50") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "30 ม.ค.50";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง 30 ม.ค.50") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "30 ม.ค.50";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง 30 ม.ค.50") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "30 ม.ค.50";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง 30 ม.ค.50") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "30 ม.ค.50";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง 30 ม.ค.50") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "30 ม.ค.50";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง 30 ม.ค.50") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "30 ม.ค.50";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง 30 ม.ค.50") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "30 ม.ค.50";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง 30 ม.ค.50") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "30 ม.ค.50";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง 30 ม.ค.50") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "30 ม.ค.50";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง 30 ม.ค.50") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "30 ม.ค.50";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง 30 ม.ค.50") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "30 ม.ค.50";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง 30 ม.ค.50") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "30 ม.ค.50";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง 30 ม.ค.50") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "30 ม.ค.50";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง 30 ม.ค.50") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "30 ม.ค.50";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง 30 ม.ค.50") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "30 ม.ค.50";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง 30 ม.ค.50") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "30 ม.ค.50";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง 30 ม.ค.50") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "30 ม.ค.50";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง 30 ม.ค.50") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "30 ม.ค.50";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง 30 ม.ค.50") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "30 ม.ค.50";
			} elseif (strpos($DOCNO,"-ลว.") !== false) {
				$arr_temp = explode("-ลว.", $DOCNO);
				$DOCNO = $arr_temp[0];
				$DOCDATE = trim($arr_temp[1]);
			} elseif (strpos($DOCNO,"- ลว.") !== false) {
				$arr_temp = explode("- ลว.", $DOCNO);
				$DOCNO = $arr_temp[0];
				$DOCDATE = trim($arr_temp[1]);
			} elseif (strpos($DOCNO,"- ลว") !== false) {
				$arr_temp = explode("- ลว", $DOCNO);
				$DOCNO = $arr_temp[0];
				$DOCDATE = trim($arr_temp[1]);
			} elseif (strpos($DOCNO,"-ลว") !== false) {
				$arr_temp = explode("-ลว", $DOCNO);
				$DOCNO = $arr_temp[0];
				$DOCDATE = trim($arr_temp[1]);
			} elseif (strpos($DOCNO,"- ลงวันที่") !== false) {
				$arr_temp = explode("- ลงวันที่", $DOCNO);
				$DOCNO = $arr_temp[0];
				$DOCDATE = trim($arr_temp[1]);
			} elseif (strpos($DOCNO,"-") !== false && strpos($DOCNO,"ลว.") === false) {
				$arr_temp = explode("-", $DOCNO);
				$DOCNO = $arr_temp[0];
				$DOCDATE = trim($arr_temp[1]);
			} elseif (strpos($DOCNO,"ลงวันที่") !== false) {
				$arr_temp = explode("ลงวันที่", $DOCNO);
				$DOCNO = $arr_temp[0];
				$DOCDATE = trim($arr_temp[1]);
			} elseif (strpos($DOCNO,"ลว.") !== false) {
				$arr_temp = explode("ลว.", $DOCNO);
				$DOCNO = $arr_temp[0];
				$DOCDATE = trim($arr_temp[1]);
			} elseif (strpos($DOCNO,"ลว .") !== false) {
				$arr_temp = explode("ลว .", $DOCNO);
				$DOCNO = $arr_temp[0];
				$DOCDATE = trim($arr_temp[1]);
			} elseif (strpos($DOCNO,"ลว") !== false && strpos($DOCNO,"กรมทางหลวง") === false) {
				$arr_temp = explode("ลว", $DOCNO);
				$DOCNO = $arr_temp[0];
				$DOCDATE = trim($arr_temp[1]);
			} elseif (strpos($DOCNO,"ณ วันที่ ท") !== false) {
				$arr_temp = explode("ณ วันที่ ท", $DOCNO);
				$DOCNO = $arr_temp[0];
				$DOCDATE = trim($arr_temp[1]);
			} elseif (strpos($DOCNO,"ณ วันที่") !== false) {
				$arr_temp = explode("ณ วันที่", $DOCNO);
				$DOCNO = $arr_temp[0];
				$DOCDATE = trim($arr_temp[1]);
			}
			if ($DOCDATE) {
				$dd = $mm = $yy = "";
				if (strpos($DOCDATE,"/") !== false) {
					$arr_temp = explode("/", $DOCDATE);
					$dd = str_pad(trim($arr_temp[0]), 2, "0", STR_PAD_LEFT);
					$mm = str_pad(trim($arr_temp[1]), 2, "0", STR_PAD_LEFT);
					$yy = trim($arr_temp[2]);
					if (strlen($yy)==2) $yy = "25".$yy;
					if ($yy+0 < 543) {
						if ($mm < substr($GOVPOS_DATE, 2, 2))
							$yy = substr($GOVPOS_DATE, 4, 4) + 1;
						else
							$yy = substr($GOVPOS_DATE, 4, 4);
					}
					$yy = $yy - 543;
					$DOCDATE = $yy."-".$mm."-".$dd;
				} else	if (strpos($DOCDATE," ") !== false) {
					$arr_temp = explode(" ", $DOCDATE);
					$dd = str_pad(trim($arr_temp[0]), 2, "0", STR_PAD_LEFT);
					if (trim($arr_temp[1])=="มกราคม" || trim($arr_temp[1])=="ม.ค." || trim($arr_temp[1])=="ม.ค" || trim($arr_temp[1])=="ม.คง" || trim($arr_temp[1])=="ม,ค,") $mm = "01";
					elseif (trim($arr_temp[1])=="กุมภาพันธ์" || trim($arr_temp[1])=="กุมภาภันธ์" || trim($arr_temp[1])=="กุมหภาพันธ์" || trim($arr_temp[1])=="กุมภาพันธฺ์" || trim($arr_temp[1])=="ก.พ." || 
						trim($arr_temp[1])=="กพ." || trim($arr_temp[1])=="กพ" || trim($arr_temp[1])=="ก.พ" || trim($arr_temp[1])=="กุมภารพันธ์" || trim($arr_temp[1])=="กกุมภาพันธ์") $mm = "02";
					elseif (trim($arr_temp[1])=="มีนาคม" || trim($arr_temp[1])=="มี.ค." || trim($arr_temp[1])=="มี.คง" || trim($arr_temp[1])=="มี.ค" || trim($arr_temp[1])=="มี.ต." || trim($arr_temp[1])=="มึ.ค.") $mm = "03";
					elseif (trim($arr_temp[1])=="เมษายน" || trim($arr_temp[1])=="เม.ย." || trim($arr_temp[1])=="เม.ย" || trim($arr_temp[1])=="เม.ยง") $mm = "04";
					elseif (trim($arr_temp[1])=="พฤษภาคม" || trim($arr_temp[1])=="พ.ค." || trim($arr_temp[1])=="พค." || trim($arr_temp[1])=="พ.ต." || trim($arr_temp[1])=="พ.ค") $mm = "05";
					elseif (trim($arr_temp[1])=="มิถุนายน" || trim($arr_temp[1])=="มิ.ย." || trim($arr_temp[1])=="มิ.ยง" || trim($arr_temp[1])=="มิ.ย") $mm = "06";
					elseif (trim($arr_temp[1])=="กรกฎาคม" || trim($arr_temp[1])=="กรกฏาคม" || trim($arr_temp[1])=="ก.ค." || trim($arr_temp[1])=="ก.คง" || trim($arr_temp[1])=="ก.ต." || trim($arr_temp[1])=="กค.") $mm = "07";
					elseif (trim($arr_temp[1])=="สิงหาคม" || trim($arr_temp[1])=="สิงหาค" || trim($arr_temp[1])=="ส.ค." || trim($arr_temp[1])=="ส.ค" || trim($arr_temp[1])=="ส.คง" || trim($arr_temp[1])=="สค.") $mm = "08";
					elseif (trim($arr_temp[1])=="กันยายน" || trim($arr_temp[1])=="ก.ย." || trim($arr_temp[1])=="ก.ย" || trim($arr_temp[1])=="ก.ยง") $mm = "09";
					elseif (trim($arr_temp[1])=="ตุลาคม" || trim($arr_temp[1])=="ต.ค." || trim($arr_temp[1])=="ต.คย." || trim($arr_temp[1])=="ตค" || trim($arr_temp[1])=="ต.ค" || 
						trim($arr_temp[1])=="ต.คง" || trim($arr_temp[1])=="าต.ค." || trim($arr_temp[1])=="ตงค.") $mm = "10";
					elseif (trim($arr_temp[1])=="พฤศจิกายน" || trim($arr_temp[1])=="พ.ย." || trim($arr_temp[1])=="พย" || trim($arr_temp[1])=="พย." || trim($arr_temp[1])=="พ.ย" || 
						trim($arr_temp[1])=="พ.ยง" || trim($arr_temp[1])=="พ..ย") $mm = "11";
					elseif (trim($arr_temp[1])=="ธันวาคม" || trim($arr_temp[1])=="ธันวาคา" || trim($arr_temp[1])=="ธ.ค." || trim($arr_temp[1])=="ธ.คง" || trim($arr_temp[1])=="ธ.ค" || trim($arr_temp[1])=="ธ .ค.") $mm = "12";
					elseif (substr($arr_temp[1],0,4)=="ม.ค.") {
						$mm = "01";
						$yy = trim(substr($arr_temp[1],4));
					} elseif (substr($arr_temp[1],0,3)=="มค." || substr($arr_temp[1],0,3)=="ม.ค") {
						$mm = "01";
						$yy = trim(substr($arr_temp[1],3));
					} elseif (substr($arr_temp[1],0,4)=="ก.พ." || substr($arr_temp[1],0,4)=="กงพ.") {
						$mm = "02";
						$yy = trim(substr($arr_temp[1],4));
					} elseif (substr($arr_temp[1],0,3)=="กพ." || substr($arr_temp[1],0,3)=="ก.พ") {
						$mm = "02";
						$yy = trim(substr($arr_temp[1],3));
					} elseif (substr($arr_temp[1],0,5)=="มี.ค." || substr($arr_temp[1],0,5)=="ม๊.ค." || substr($arr_temp[1],0,5)=="มึ.ค." || substr($arr_temp[1],0,5)=="มี.คง") {
						$mm = "03";
						$yy = trim(substr($arr_temp[1],5));
					} elseif (substr($arr_temp[1],0,4)=="มีค.") {
						$mm = "03";
						$yy = trim(substr($arr_temp[1],4));
					} elseif (substr($arr_temp[1],0,5)=="เม.ย." || substr($arr_temp[1],0,5)=="เม.ยง") {
						$mm = "04";
						$yy = trim(substr($arr_temp[1],5));
					} elseif (substr($arr_temp[1],0,4)=="เมย." || substr($arr_temp[1],0,4)=="เมษ." || substr($arr_temp[1],0,4)=="เม.ย") {
						$mm = "04";
						$yy = trim(substr($arr_temp[1],4));
					} elseif (substr($arr_temp[1],0,4)=="พ.ค." || substr($arr_temp[1],0,4)=="พ.คง" || substr($arr_temp[1],0,4)=="พงค." || substr($arr_temp[1],0,4)=="พ.ต.") {
						$mm = "05";
						$yy = trim(substr($arr_temp[1],4));
					} elseif (substr($arr_temp[1],0,3)=="พค.") {
						$mm = "05";
						$yy = trim(substr($arr_temp[1],3));
					} elseif (substr($arr_temp[1],0,5)=="มิ.ย." || substr($arr_temp[1],0,5)=="มิ.ยง") {
						$mm = "06";
						$yy = trim(substr($arr_temp[1],5));
					} elseif (substr($arr_temp[1],0,4)=="มิย.") {
						$mm = "06";
						$yy = trim(substr($arr_temp[1],4));
					} elseif (substr($arr_temp[1],0,4)=="ก.ค." || substr($arr_temp[1],0,4)=="ก.คง" || substr($arr_temp[1],0,4)=="ก.ต.") {
						$mm = "07";
						$yy = trim(substr($arr_temp[1],4));
					} elseif (substr($arr_temp[1],0,3)=="กค.") {
						$mm = "07";
						$yy = trim(substr($arr_temp[1],3));
					} elseif (substr($arr_temp[1],0,4)=="ส.ค.") {
						$mm = "08";
						$yy = trim(substr($arr_temp[1],4));
					} elseif (substr($arr_temp[1],0,3)=="สค.") {
						$mm = "08";
						$yy = trim(substr($arr_temp[1],3));
					} elseif (substr($arr_temp[1],0,4)=="ก.ย." || substr($arr_temp[1],0,4)=="ก.ยง" || substr($arr_temp[1],0,4)=="กซนซ" || substr($arr_temp[1],0,4)=="ก.ยซ") {
						$mm = "09";
						$yy = trim(substr($arr_temp[1],4));
					} elseif (substr($arr_temp[1],0,3)=="กย." || substr($arr_temp[1],0,2)=="กย") {
						$mm = "09";
						$yy = trim(substr($arr_temp[1],2));
					} elseif (substr($arr_temp[1],0,4)=="ต.ค." || substr($arr_temp[1],0,4)=="ต.คง" || substr($arr_temp[1],0,4)=="ตงค.") {
						$mm = "10";
						$yy = trim(substr($arr_temp[1],4));
					} elseif (substr($arr_temp[1],0,3)=="ตค." || substr($arr_temp[1],0,3)=="ต.ต") {
						$mm = "10";
						$yy = trim(substr($arr_temp[1],3));
					} elseif (substr($arr_temp[1],0,3)=="ต.ค") {
						$mm = "10";
						$yy = trim(substr($arr_temp[1],3));
					} elseif (substr($arr_temp[1],0,4)=="พ.ย.") {
						$mm = "11";
						$yy = trim(substr($arr_temp[1],4));
					} elseif (substr($arr_temp[1],0,3)=="พย.") {
						$mm = "11";
						$yy = trim(substr($arr_temp[1],3));
					} elseif (substr($arr_temp[1],0,4)=="ธ.ค." || substr($arr_temp[1],0,4)=="ธ.คง") {
						$mm = "12";
						$yy = trim(substr($arr_temp[1],4));
					} elseif (substr($arr_temp[1],0,3)=="ธค.") {
						$mm = "12";
						$yy = trim(substr($arr_temp[1],3));
					} elseif ($arr_temp[0]=="21ธ.ค." && $arr_temp[1]=="2542") {
						$dd = "21";
						$mm = "12";
						$yy = "2542";
					} elseif ($arr_temp[0]=="25กุมภาพันธ์" && $arr_temp[1]=="2543") {
						$dd = "25";
						$mm = "02";
						$yy = "2543";
					} elseif ($arr_temp[0]=="27ต.ค." && $arr_temp[1]=="2543") {
						$dd = "27";
						$mm = "10";
						$yy = "2543";
					} elseif ($arr_temp[0]=="22พ.ค." && $arr_temp[1]=="2545") {
						$dd = "22";
						$mm = "05";
						$yy = "2545";
					} elseif ($arr_temp[0]=="1พ.ย." && $arr_temp[1]=="2545") {
						$dd = "01";
						$mm = "11";
						$yy = "2545";
					} elseif ($arr_temp[0]=="4ธันวาคม" && $arr_temp[1]=="2545") {
						$dd = "04";
						$mm = "12";
						$yy = "2545";
					} elseif ($arr_temp[0]=="28ม.ค." && $arr_temp[1]=="2546") {
						$dd = "28";
						$mm = "01";
						$yy = "2546";
					} elseif ($arr_temp[0]=="11สิงหาคม" && $arr_temp[1]=="2546") {
						$dd = "11";
						$mm = "08";
						$yy = "2546";
					} elseif ($arr_temp[0]=="17มกราคม" && $arr_temp[1]=="2550") {
						$dd = "17";
						$mm = "01";
						$yy = "2550";
					} elseif ($arr_temp[0]=="1ก.พ." && $arr_temp[1]=="2551") {
						$dd = "01";
						$mm = "02";
						$yy = "2551";
					} elseif ($arr_temp[0]=="27ก.พ." && $arr_temp[1]=="2551") {
						$dd = "27";
						$mm = "02";
						$yy = "2551";
					} elseif ($arr_temp[0]=="7ต.ค." && $arr_temp[1]=="2551") {
						$dd = "07";
						$mm = "10";
						$yy = "2551";
					} elseif ($arr_temp[0]=="27ก.พ." && $arr_temp[1]=="52") {
						$dd = "27";
						$mm = "02";
						$yy = "2552";
					} elseif ($arr_temp[0]=="11ก.ย." && $arr_temp[1]=="52") {
						$dd = "11";
						$mm = "09";
						$yy = "2552";
					} elseif ($arr_temp[0]=="30พ.ค." && $arr_temp[1]=="2555") {
						$dd = "30";
						$mm = "05";
						$yy = "2555";
					} elseif ($arr_temp[0]=="28" && $arr_temp[1]=="กรกฎาคม2547") {
						$dd = "28";
						$mm = "07";
						$yy = "2547";
					} elseif ($arr_temp[0]=="6" && $arr_temp[1]=="ธันวาคม2550") {
						$dd = "06";
						$mm = "12";
						$yy = "2550";
					} elseif ($arr_temp[0]=="11" && $arr_temp[1]=="ธันวาคม2552") {
						$dd = "11";
						$mm = "12";
						$yy = "2552";
					} elseif ($arr_temp[0]=="16" && $arr_temp[1]=="ธ.ค2556.") {
						$dd = "16";
						$mm = "12";
						$yy = "2556";
					} elseif ($arr_temp[0]=="7" && $arr_temp[1]=="ก." && $arr_temp[2]=="ย.53") {
						$dd = "07";
						$mm = "09";
						$yy = "2553";
					} else echo "$arr_temp[0]**$arr_temp[1]**$arr_temp[2]**$REMARK2<br>";
					if (!$yy) $yy = trim($arr_temp[2]);
					if (strlen($yy)==2) $yy = "25".$yy;
					if ($yy+0 < 543) {
						if ($mm < substr($GOVPOS_DATE, 2, 2))
							$yy = substr($GOVPOS_DATE, 4, 4) + 1;
						else
							$yy = substr($GOVPOS_DATE, 4, 4);
					}
					$yy = $yy - 543;
					$DOCDATE = $yy."-".$mm."-".$dd; 
				} else	if ($DOCDATE=="31ตค.09") {
					$DOCDATE = "1966-10-31";
				} else	if ($DOCDATE=="15พค.10") {
					$DOCDATE = "1967-05-15";
				} else	if ($DOCDATE=="16มิย.10") {
					$DOCDATE = "1967-06-16";
				} else	if ($DOCDATE=="5กค.10") {
					$DOCDATE = "1967-07-05";
				} else	if ($DOCDATE=="13มิย.11") {
					$DOCDATE = "1968-06-13";
				} else	if ($DOCDATE=="26กย.11") {
					$DOCDATE = "1968-09-26";
				} else	if ($DOCDATE=="12พค.12") {
					$DOCDATE = "1969-05-12";
				} else	if ($DOCDATE=="27เมย.13") {
					$DOCDATE = "1970-04-27";
				} else	if ($DOCDATE=="29มิย.14") {
					$DOCDATE = "1971-06-29";
				} else	if ($DOCDATE=="20ตค.14") {
					$DOCDATE = "1971-10-20";
				} else	if ($DOCDATE=="30มิย.15") {
					$DOCDATE = "1972-06-30";
				} else	if ($DOCDATE=="21พค.16)") {
					$DOCDATE = "1973-05-21";
				} else	if ($DOCDATE=="25052516" || $DOCDATE=="25พค.16") {
					$DOCDATE = "1973-05-16";
				} else	if ($DOCDATE=="5-11-16") {
					$DOCDATE = "1973-11-05";
				} else	if ($DOCDATE=="22-12-16") {
					$DOCDATE = "1973-12-22";
				} else	if ($DOCDATE=="07052517" || $DOCDATE=="7พค.17") {
					$DOCDATE = "1974-05-07";
				} else	if ($DOCDATE=="17พค.17") {
					$DOCDATE = "1974-05-17";
				} else	if ($DOCDATE=="5มี.ค.18") {
					$DOCDATE = "1975-03-05";
				} else	if ($DOCDATE=="21มีค.18") {
					$DOCDATE = "1975-03-21";
				} else	if ($DOCDATE=="14พค.18") {
					$DOCDATE = "1975-05-14";
				} else	if ($DOCDATE=="5กย.18") {
					$DOCDATE = "1975-09-05";
				} else	if ($DOCDATE=="18กย.18") {
					$DOCDATE = "1975-09-18";
				} else	if ($DOCDATE=="18ธค.18") {
					$DOCDATE = "1975-12-18";
				} else	if ($DOCDATE=="27กพ.19") {
					$DOCDATE = "1976-02-27";
				} else	if ($DOCDATE=="01102519") {
					$DOCDATE = "1976-10-01";
				} else	if ($DOCDATE=="13มค.20") {
					$DOCDATE = "1977-01-13";
				} else	if ($DOCDATE=="2กพ.20") {
					$DOCDATE = "1977-02-02";
				} else	if ($DOCDATE=="13มิย.20") {
					$DOCDATE = "1977-06-13";
				} else	if ($DOCDATE=="14มิย.20") {
					$DOCDATE = "1977-06-14";
				} else	if ($DOCDATE=="23กย.20") {
					$DOCDATE = "1977-09-23";
				} else	if ($DOCDATE=="20ก.ย.21" || $DOCDATE=="20กย.21") {
					$DOCDATE = "1978-09-20";
				} else	if ($DOCDATE=="11ตค.21") {
					$DOCDATE = "1978-10-11";
				} else	if ($DOCDATE=="27เมย.22") {
					$DOCDATE = "1979-04-27";
				} else	if ($DOCDATE=="24ธ.ค.22") {
					$DOCDATE = "1979-12-24";
				} else	if ($DOCDATE=="17กย.23") {
					$DOCDATE = "1980-09-17";
				} else	if ($DOCDATE=="2ตค.23") {
					$DOCDATE = "1980-10-02";
				} else	if ($DOCDATE=="3ตค.23") {
					$DOCDATE = "1980-10-03";
				} else	if ($DOCDATE=="18กพ.24") {
					$DOCDATE = "1981-02-18";
				} else	if ($DOCDATE=="27มค.25") {
					$DOCDATE = "1982-01-27";
				} else	if ($DOCDATE=="15กพ.25") {
					$DOCDATE = "1982-02-15";
				} else	if ($DOCDATE=="2ธค.25") {
					$DOCDATE = "1982-12-02";
				} else	if ($DOCDATE=="7มิย.26") {
					$DOCDATE = "1983-06-07";
				} else	if ($DOCDATE=="21กย.26") {
					$DOCDATE = "1983-09-21";
				} else	if ($DOCDATE=="30พย.26") {
					$DOCDATE = "1983-11-26";
				} else	if ($DOCDATE=="11กย.27") {
					$DOCDATE = "1984-09-11";
				} else	if ($DOCDATE=="18กย.27") {
					$DOCDATE = "1984-09-18";
				} else	if ($DOCDATE=="31ตค.27") {
					$DOCDATE = "1984-10-31";
				} else	if ($DOCDATE=="5เมย.28") {
					$DOCDATE = "1985-04-05";
				} else	if ($DOCDATE=="12กย.28") {
					$DOCDATE = "1985-09-12";
				} else	if ($DOCDATE=="31ตค.28") {
					$DOCDATE = "1985-10-31";
				} else	if ($DOCDATE=="23กย.29") {
					$DOCDATE = "1986-09-23";
				} else	if ($DOCDATE=="28กย29") {
					$DOCDATE = "1986-09-28";
				} else	if ($DOCDATE=="8มิย.31") {
					$DOCDATE = "1988-06-08";
				} else	if ($DOCDATE=="28กย.31" || $DOCDATE=="28ก.ย.31") {
					$DOCDATE = "1988-09-28";
				} else	if ($DOCDATE=="26ตค.31") {
					$DOCDATE = "1988-10-26";
				} else	if ($DOCDATE=="4ตค.32" || $DOCDATE=="4ต.ค.32") {
					$DOCDATE = "1989-10-04";
				} else	if ($DOCDATE=="27ก.ย.2533") {
					$DOCDATE = "1990-09-27";
				} else	if ($DOCDATE=="29062536") {
					$DOCDATE = "1993-06-29";
				} else	if ($DOCDATE=="23มี.ค.38") {
					$DOCDATE = "1995-03-23";
				} else	if ($DOCDATE=="26มี.ค.2541" || $DOCDATE=="26มี.ค.41") {
					$DOCDATE = "1998-03-26";
				} else	if ($DOCDATE=="6ต.ค.41") {
					$DOCDATE = "1998-10-06";
				} else	if ($DOCDATE=="21ธ.ค. 2542") {
					$DOCDATE = "1999-12-21";
				} else	if ($DOCDATE=="27ก.ค.2543") {
					$DOCDATE = "2000-07-27";
				} else	if ($DOCDATE=="27ต.ค. 2543" || $DOCDATE=="27ต.ค.2543") {
					$DOCDATE = "2000-10-27";
				} else	if ($DOCDATE=="20ก.ย.2544") {
					$DOCDATE = "2001-09-20";
				} else	if ($DOCDATE=="7พ.ย.2544") {
					$DOCDATE = "2001-11-07";
				} else	if ($DOCDATE=="22พ.ค. 2545" || $DOCDATE=="22พ.ค.2545") {
					$DOCDATE = "2002-05-22";
				} else	if ($DOCDATE=="1พ.ย. 2545") {
					$DOCDATE = "2002-11-01";
				} else	if ($DOCDATE=="4ธันวาคม 2545") {
					$DOCDATE = "2002-12-04";
				} else	if ($DOCDATE=="28ม.ค. 2546") {
					$DOCDATE = "2003-01-28";
				} else	if ($DOCDATE=="7ก.พ.46") {
					$DOCDATE = "2003-02-07";
				} else	if ($DOCDATE=="17ก.ค.46") {
					$DOCDATE = "2003-07-17";
				} else	if ($DOCDATE=="11สิงหาคม 2546") {
					$DOCDATE = "2003-08-11";
				} else	if ($DOCDATE=="6ก.พ.47") {
					$DOCDATE = "2004-02-06";
				} else	if ($DOCDATE=="9มี.ค.47") {
					$DOCDATE = "2004-03-09";
				} else	if ($DOCDATE=="28  กรกฎาคม2547") {
					$DOCDATE = "2004-07-28";
				} else	if ($DOCDATE=="3ก.ย.2547") {
					$DOCDATE = "2004-09-03";
				} else	if ($DOCDATE=="11ม.ค.2548") {
					$DOCDATE = "2005-01-11";
				} else	if ($DOCDATE=="17ม.ค.2548") {
					$DOCDATE = "2005-01-17";
				} else	if ($DOCDATE=="13ต.ค.2548") {
					$DOCDATE = "2005-10-13";
				} else	if ($DOCDATE=="21ธ.ค.2548") {
					$DOCDATE = "2005-12-21";
				} else	if ($DOCDATE=="23ม.ค.49") {
					$DOCDATE = "2006-01-23";
				} else	if ($DOCDATE=="18พ.ค.2549") {
					$DOCDATE = "2006-05-18";
				} else	if ($DOCDATE=="25พ.ค.2549") {
					$DOCDATE = "2006-05-25";
				} else	if ($DOCDATE=="8ส.ค.2549") {
					$DOCDATE = "2006-08-08";
				} else	if ($DOCDATE=="14ก.ย.2549") {
					$DOCDATE = "2006-09-14";
				} else	if ($DOCDATE=="24ต.ค.2549") {
					$DOCDATE = "2006-10-24";
				} else	if ($DOCDATE=="22มี.ค.2550") {
					$DOCDATE = "2007-03-22";
				} else	if ($DOCDATE=="18เม.ย.2550") {
					$DOCDATE = "2007-04-18";
				} else	if ($DOCDATE=="7ส.ค.2550") {
					$DOCDATE = "2007-08-07";
				} else	if ($DOCDATE=="22ส.ค.2550") {
					$DOCDATE = "2007-08-22";
				} else	if ($DOCDATE=="6 ธันวาคม2550" || $DOCDATE=="6ธ.ค.2550") {
					$DOCDATE = "2007-12-06";
				} else	if ($DOCDATE=="1ก.พ. 2551") {
					$DOCDATE = "2008-02-01";
				} else	if ($DOCDATE=="25มี.ค.51") {
					$DOCDATE = "2008-03-25";
				} else	if ($DOCDATE=="12พ.ค2551") {
					$DOCDATE = "2008-05-12";
				} else	if ($DOCDATE=="9มิ.ย.51") {
					$DOCDATE = "2008-06-09";
				} else	if ($DOCDATE=="7ต.ค. 2551") {
					$DOCDATE = "2008-10-07";
				} else	if ($DOCDATE=="1-12-51") {
					$DOCDATE = "2008-12-01";
				} else	if ($DOCDATE=="8ธ.ค.51") {
					$DOCDATE = "2008-12-08";
				} else	if ($DOCDATE=="30ธ.ค.51" || $DOCDATE=="30ธ.ค.2551") {
					$DOCDATE = "2008-12-30";
				} else	if ($DOCDATE=="21ม.ค.2552") {
					$DOCDATE = "2009-01-21";
				} else	if ($DOCDATE=="27ก.พ. 52") {
					$DOCDATE = "2009-02-27";
				} else	if ($DOCDATE=="25พ.ค.2552") {
					$DOCDATE = "2009-05-25";
				} else	if ($DOCDATE=="11ก.ย. 52") {
					$DOCDATE = "2009-09-11";
				} else	if ($DOCDATE=="30ก.ย.2552") {
					$DOCDATE = "2009-09-30";
				} else	if ($DOCDATE=="8ต.ค.2552") {
					$DOCDATE = "2009-10-08";
				} else	if ($DOCDATE=="9ต.ค.2552") {
					$DOCDATE = "2009-10-09";
				} else	if ($DOCDATE=="26ต.ค.2552") {
					$DOCDATE = "2009-10-26";
				} else	if ($DOCDATE=="29ต.ค.2552") {
					$DOCDATE = "2009-10-29";
				} else	if ($DOCDATE=="3พ.ย.2552") {
					$DOCDATE = "2009-11-03";
				} else	if ($DOCDATE=="4พ.ย.2552") {
					$DOCDATE = "2009-11-04";
				} else	if ($DOCDATE=="5พ.ย.2552") {
					$DOCDATE = "2009-11-05";
				} else	if ($DOCDATE=="6พ.ย.2552") {
					$DOCDATE = "2009-11-06";
				} else	if ($DOCDATE=="9พ.ย.2552") {
					$DOCDATE = "2009-11-09";
				} else	if ($DOCDATE=="11พ.ย.2552") {
					$DOCDATE = "2009-11-11";
				} else	if ($DOCDATE=="12พ.ย.2552") {
					$DOCDATE = "2009-11-12";
				} else	if ($DOCDATE=="8 ก.พ25.53") {
					$DOCDATE = "2010-02-08";
				} else	if ($DOCDATE=="03032553") {
					$DOCDATE = "2010-03-03";
				} else	if ($DOCDATE=="7ก.ค.2553") {
					$DOCDATE = "2010-07-07";
				} else	if ($DOCDATE=="29พ.ย.2553") {
					$DOCDATE = "2010-11-29";
				} else	if ($DOCDATE=="01012554") {
					$DOCDATE = "2011-01-01";
				} else	if ($DOCDATE=="28ก.พ.2554") {
					$DOCDATE = "2011-02-28";
				} else	if ($DOCDATE=="21มิ.ย.54") {
					$DOCDATE = "2011-06-21";
				} else	if ($DOCDATE=="6ก.ย.54") {
					$DOCDATE = "2011-09-06";
				} else	if ($DOCDATE=="12ม.ค.2555") {
					$DOCDATE = "2012-01-12";
				} else	if ($DOCDATE=="4พ.ค.55") {
					$DOCDATE = "2012-05-04";
				} else	if ($DOCDATE=="07052517") {
					$DOCDATE = "1974-05-07";
				} else	if ($DOCDATE=="07052517") {
					$DOCDATE = "1974-05-07";
				} else	if ($DOCDATE=="07052517") {
					$DOCDATE = "1974-05-07";
				} else	if ($DOCDATE=="07052517") {
					$DOCDATE = "1974-05-07";
				} else	if ($DOCDATE=="07052517") {
					$DOCDATE = "1974-05-07";
				} else	if ($DOCDATE=="07052517") {
					$DOCDATE = "1974-05-07";
				} else	if ($DOCDATE=="07052517") {
					$DOCDATE = "1974-05-07";
				} else	if ($DOCDATE=="07052517") {
					$DOCDATE = "1974-05-07";
				} else	if ($DOCDATE=="07052517") {
					$DOCDATE = "1974-05-07";
				}
			}
			$GOVPOS_YEAR = trim($data[GOVPOS_YEAR]);

			if (substr($DOCDATE,0,4)=="0425") $DOCDATE = "1984".substr($DOCDATE,4);
			elseif (substr($DOCDATE,0,4)=="0009") $DOCDATE = "1991".substr($DOCDATE,4);
			elseif (substr($DOCDATE,0,4)=="0008") $DOCDATE = "1992".substr($DOCDATE,4);
			elseif (substr($DOCDATE,0,4)=="0383") $DOCDATE = "1993".substr($DOCDATE,4);
			elseif (substr($DOCDATE,0,4)=="1822") $DOCDATE = "1993".substr($DOCDATE,4);
			elseif (substr($DOCDATE,0,4)=="0006" && $CUR_YEAR=="2537") $DOCDATE = "1994".substr($DOCDATE,4);
			elseif (substr($DOCDATE,0,4)=="0286" && $CUR_YEAR=="2537") $DOCDATE = "1994".substr($DOCDATE,4);

			if (substr($EFFECTIVEDATE,0,4)=="0494") $EFFECTIVEDATE = "2006".substr($EFFECTIVEDATE,4);
			elseif (substr($EFFECTIVEDATE,0,4)=="0291") $EFFECTIVEDATE = "1977".substr($EFFECTIVEDATE,4);
			elseif (substr($EFFECTIVEDATE,0,4)=="1857" && $CUR_YEAR=="2533") $EFFECTIVEDATE = "1990".substr($EFFECTIVEDATE,4);

			$POS_NO = trim($data[GOVPOS_PST_NO]);
			$WPOS_CODE = trim($data[WPOS_CODE]);
			$WPOS_NAME = trim($data[WPOS_NAME]);
			if ($WPOS_NAME=="'") $WPOS_NAME = "";
			$cmd = " select PL_CODE from PER_LINE where PL_NAME = '$WPOS_NAME' or PL_SHORTNAME = '$WPOS_NAME' ";
			$db_dpis->send_cmd($cmd);
			$data_dpis = $db_dpis->get_array();
			$PL_CODE = trim($data_dpis[PL_CODE]);

			$MPOS_CODE = trim($data[MPOS_CODE]);
			$MPOS_NAME = trim($data[MPOS_NAME]);
			$PM_CODE = "";
			if ($MPOS_NAME) { 
				$cmd = " select PM_CODE from PER_MGT where PM_NAME = '$MPOS_NAME' or PM_SHORTNAME = '$MPOS_NAME' ";
				$db_dpis->send_cmd($cmd);
				$data_dpis = $db_dpis->get_array();
				$PM_CODE = trim($data_dpis[PM_CODE]);
			} 

			$POH_ORG1 = "กระทรวงคมนาคม";
			$POH_ORG2 = "กรมทางหลวง";
			$POH_ORG3 = trim($data[DEPT_NAME]);
			$POH_ORG3 = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($POH_ORG3)));
			$POH_ORG3 = str_replace("'", "", trim($POH_ORG3));
			if ($POH_ORG3=="กองประมวลผลสถิติ") {
				$POH_ORG1 = "สำนักนายกรัฐมนตรี";
				$POH_ORG2 = "สำนักงานสถิติแห่งชาติ";
			}
			$POH_UNDER_ORG1 = trim($data[SECTION_NAME]);
			$POH_UNDER_ORG1 = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($POH_UNDER_ORG1)));
			$POH_UNDER_ORG1 = str_replace("'", "", trim($POH_UNDER_ORG1));
			$POH_UNDER_ORG2 = trim($data[JOB_NAME]);
			$POH_UNDER_ORG2 = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($POH_UNDER_ORG2)));
			$POH_UNDER_ORG2 = str_replace("'", "", trim($POH_UNDER_ORG2));
			$POH_ORG = trim((($POH_UNDER_ORG2=="-") ? "":$POH_UNDER_ORG2)." ".(($POH_UNDER_ORG1=="-") ? "":$POH_UNDER_ORG1)." ".
											(($POH_ORG3=="-") ? "":$POH_ORG3)." ".$POH_ORG2);
			$SALARY = $data[GOVPOS_HISSALARY];
			$SAH_SALARY_UP = $data[GOVPOS_MONEY_UP];
			$SAH_SALARY_EXTRA = $data[GOVPOS_MONEY_SPEC];
			$SAH_PERCENT_UP = $data[GOVPOS_PRAMEAUN_PERCENT];
			$FLAG_TO_NAME = trim($data[FLAG_TO_NAME]);
			$SPECIALIST_NAME = trim($data[SPECIALIST_NAME]);
			$REMARK = trim($data[GOVPOS_DET]);
			$REMARK = str_replace("'", "", $REMARK);
			$REMARK1 = trim($data[GOVPOS_PRAMEAUN_LAYER]);
			$FLAG_TO_NAME_CODE = trim($data[FLAG_TO_NAME_CODE]);
			$POH_SALARY_POS = 0;
			$GOVPOS_COMCODE = trim($data[GOVPOS_COMCODE]);

			if ($GOVPOS_COMCODE=="0") $MOV_CODE = "6"; // ผิด **********************
			elseif ($GOVPOS_COMCODE=="1") {
				$MOV_CODE = "102"; // ทดลองปฏิบัติราชการ
				if (strpos($REMARK,"บรรจุและแต่งตั้งผู้สอบแข่งขันได้") !== false) $MOV_CODE = "10110";
				elseif (strpos($REMARK,"บรรจุและแต่งตั้งผู้ได้รับการคัดเลือก") !== false) $MOV_CODE = "10120";
				elseif (strpos($REMARK,"ผ่านการทดลองปฏิบัติหน้าที่ราชการ") !== false) $MOV_CODE = "10210";
				elseif (strpos($REMARK,"บรรจุ") !== false) $MOV_CODE = "101";
			} elseif ($GOVPOS_COMCODE=="10") $MOV_CODE = "21310"; // 0.5 ขั้น
			elseif ($GOVPOS_COMCODE=="11") $MOV_CODE = "21330"; // 1.5 ขั้น
			elseif ($GOVPOS_COMCODE=="12") $MOV_CODE = "213"; // เลื่อนขั้นเงินเดือน
			elseif ($GOVPOS_COMCODE=="13") $MOV_CODE = "11010"; // รักษาราชการแทน
			elseif ($GOVPOS_COMCODE=="14") $MOV_CODE = "106"; // โอนไป ******************
			elseif ($GOVPOS_COMCODE=="15") $MOV_CODE = "11810"; // ลาออก
			elseif ($GOVPOS_COMCODE=="16") $MOV_CODE = "11020"; // รักษาการในตำแหน่ง
			elseif ($GOVPOS_COMCODE=="17") $MOV_CODE = "21370"; // งดขั้นเงินเดือน ************************
			elseif ($GOVPOS_COMCODE=="18") $MOV_CODE = "103"; // ย้ายข้าราชการ
			elseif ($GOVPOS_COMCODE=="19") $MOV_CODE = "99999"; // แต่งตั้งเจ้าหน้าที่พัสดุประจำโครงการ *************
			elseif ($GOVPOS_COMCODE=="2") $MOV_CODE = "10210"; // พ้นทดลองปฏิบัติราชการ
			elseif ($GOVPOS_COMCODE=="21") $MOV_CODE = "105"; // โอนมา
			elseif ($GOVPOS_COMCODE=="22") $MOV_CODE = "10140"; // กลับเข้ารับราชการใหม่
			elseif ($GOVPOS_COMCODE=="23") $MOV_CODE = "216"; // ลดขั้นเงินเดือน
			elseif ($GOVPOS_COMCODE=="24") $MOV_CODE = "21510"; // ปรับเงินเดือนตามวุฒิ
			elseif ($GOVPOS_COMCODE=="25") $MOV_CODE = "12410"; // ลดระดับ
			elseif ($GOVPOS_COMCODE=="26") $MOV_CODE = "12310"; // ตาย
			elseif ($GOVPOS_COMCODE=="27") $MOV_CODE = "11910"; // เกษียณ
			elseif ($GOVPOS_COMCODE=="28") $MOV_CODE = "12210"; // ไล่ออก
			elseif ($GOVPOS_COMCODE=="29") $MOV_CODE = "106"; // ให้โอน
			elseif ($GOVPOS_COMCODE=="3") $MOV_CODE = "21520"; // ปรับอัตราเงินเดือน
			elseif ($GOVPOS_COMCODE=="30") $MOV_CODE = "11420"; // แก้ไขคำสั่ง
			elseif ($GOVPOS_COMCODE=="31") $MOV_CODE = "21540"; // ปรับเงินพิเศษสำหรับการสู้รบ 
			elseif ($GOVPOS_COMCODE=="32") $MOV_CODE = "21380"; // เลื่อนขั้นเงินเดือนข้าราชการผู้เกษียณอายุ ***************************
			elseif ($GOVPOS_COMCODE=="33") $MOV_CODE = "11410"; // ยกเลิกการบรรจุ ***********************
			elseif ($GOVPOS_COMCODE=="34") $MOV_CODE = "21370"; // ไม่ได้เลื่อนขั้น
			elseif ($GOVPOS_COMCODE=="35") $MOV_CODE = "21320"; // 1 ขั้น
			elseif ($GOVPOS_COMCODE=="36") $MOV_CODE = "21420"; // 2%
			elseif ($GOVPOS_COMCODE=="37") $MOV_CODE = "21430"; // 4%
			elseif ($GOVPOS_COMCODE=="38") $MOV_CODE = "11410"; // ยกเลิกการรับโอน *******************
			elseif ($GOVPOS_COMCODE=="39") $MOV_CODE = "120"; // ให้ออก
			elseif ($GOVPOS_COMCODE=="4") $MOV_CODE = "99999"; // ตัดโอนตำแหน่ง ********************
			elseif ($GOVPOS_COMCODE=="40") $MOV_CODE = "12110"; // ปลดออก
			elseif ($GOVPOS_COMCODE=="41") $MOV_CODE = "104"; // เลื่อนข้าราชการ
			elseif ($GOVPOS_COMCODE=="42") $MOV_CODE = "11830"; // เกษียณอายุก่อนกำหนด
			elseif ($GOVPOS_COMCODE=="43") $MOV_CODE = "99999"; // แก้ไขตัดโอนตำแหน่ง
			elseif ($GOVPOS_COMCODE=="5") $MOV_CODE = "21410"; // เต็มขั้น
			elseif ($GOVPOS_COMCODE=="6") $MOV_CODE = "21350"; // เลื่อนขั้นเงินเดือนข้าราชการกรณีพิเศษ
			elseif ($GOVPOS_COMCODE=="7") $MOV_CODE = "11310"; // ปฏิบัติราชการ *****************
			elseif ($GOVPOS_COMCODE=="8") $MOV_CODE = "21340"; // 2 ขั้น
			elseif ($GOVPOS_COMCODE=="9") $MOV_CODE = "21305"; // เลื่อนเงินเดือน
			elseif ($GOVPOS_COMCODE=="99") $MOV_CODE = "99999"; // 
			elseif ($GOVPOS_COMCODE=="999") $MOV_CODE = "99999"; // 
			else { 
				$MOV_CODE = "99999"; 
				if ($FLAG_TO_NAME) echo "ประเภทการเคลื่อนไหว->$GOVPOS_COMCODE<br>"; 
			}

			$cmd = " select MOV_TYPE from PER_MOVMENT where MOV_CODE = '$MOV_CODE' ";
			$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			$data_dpis = $db_dpis->get_array();
			$MOV_TYPE = trim($data_dpis[MOV_TYPE]);

			$SM_CODE = "";
			if (strpos($REMARK,"0.5 ขั้น") !== false || $REMARK==".5 ขั้น" || $REMARK=="0.5   จ.1.5/  21   /2546 - 7/7/2546") {
				$MOV_TYPE = 2;
				$MOV_CODE = "21310";
				$SM_CODE = "1";
			} elseif (strpos($REMARK,"1.5 ขั้น") !== false || strpos($REMARK,"1 ขั้นครึ่ง") !== false || strpos($REMARK,"1ขั้นครึ่ง") !== false || strpos($REMARK,"หนึ่งขั้นครึ่ง") !== false) {
				$MOV_TYPE = 2;
				$MOV_CODE = "21330";
				$SM_CODE = "3";
			} elseif (strpos($REMARK,"ลดขั้นเงินเดือน1") !== false || $REMARK=="ลดขั้น 1 ขั้น") {
				$MOV_TYPE = 2;
				$MOV_CODE = "21620";
				$SM_CODE = "7";
			} elseif (strpos($REMARK,"1 ขั้น") !== false || strpos($REMARK,"1ขั้น") !== false) {
				$MOV_TYPE = 2;
				$MOV_CODE = "21320";
				$SM_CODE = "2";
			} elseif (strpos($REMARK,"2 ขั้น") !== false || strpos($REMARK,"2ขั้น") !== false || strpos($REMARK,"2  ขั้น") !== false) {
				$MOV_TYPE = 2;
				$MOV_CODE = "21340";
				$SM_CODE = "4";
			} elseif (strpos($REMARK,"งดขั้น") !== false || strpos($REMARK,"เงินเดือนไม่ขึ้น") !== false || strpos($REMARK,"ไม่ขึ้นขั้น") !== false) {
				$MOV_TYPE = 2;
				$MOV_CODE = "21370";
				$SM_CODE = "10";
			} elseif (strpos($REMARK,"เต็มขั้น") !== false) {
				$MOV_TYPE = 2;
				$MOV_CODE = "21410";
			} elseif (strpos($REMARK,"ลดขั้นเงินเดือน") !== false) {
				$MOV_TYPE = 2;
				$MOV_CODE = "216";
			} elseif (strpos($REMARK,"เงินเพิ่มพิเศษ 2%") !== false || $REMARK=="เงินตอบแทนพิเศษ 2 %") { 
				$MOV_TYPE = 2;
				$MOV_CODE = "21420";
				$SM_CODE = "5"; 
				$EX_CODE = "015"; 
			} elseif (strpos($REMARK,"เงินเพิ่มพิเศษ 4%") !== false || $REMARK=="เงินตอบแทนพิเศษ 4 %") { 
				$MOV_TYPE = 2;
				$MOV_CODE = "21430";
				$SM_CODE = "17";  
				$EX_CODE = "016"; 
			} elseif (strpos($REMARK,"ตัดเงินเดือน 10 %") !== false || strpos($REMARK,"ตัดเงินเดือน 10%") !== false) {
				$MOV_TYPE = 2;
				$MOV_CODE = "21720";
				$SM_CODE = "8";
			} elseif (strpos($REMARK,"ถูกตัดเงิน 5% (ตั้งแต่เดือน มิ.ย.37-ส.ค") !== false) {
				$MOV_TYPE = 2;
				$MOV_CODE = "21710";
				$SM_CODE = "8";
			} elseif (strpos($REMARK,"ตามวุฒิ") !== false || strpos($REMARK,"ตามคุณวุฒิ") !== false) { 
				$MOV_TYPE = 2;
				$MOV_CODE = "21510";
				$SM_CODE = "14";
			} elseif (strpos($REMARK,"ปรับอัตราเงินเดือน") !== false || $REMARK=="xx") { 
				$MOV_TYPE = 2;
				$MOV_CODE = "21520";
				$SM_CODE = "12";
			} elseif (strpos($REMARK,"เลื่อนเงินเดือน") !== false && $REMARK1=="พอใช้") { 
				$MOV_TYPE = 2;
				$MOV_CODE = "21315";
			} elseif (strpos($REMARK,"เลื่อนเงินเดือน") !== false && $REMARK1=="ดี") { 
				$MOV_TYPE = 2;
				$MOV_CODE = "21325";
			} elseif (strpos($REMARK,"เลื่อนเงินเดือน") !== false && $REMARK1=="ดีมาก") { 
				$MOV_TYPE = 2;
				$MOV_CODE = "21335";
			} elseif (strpos($REMARK,"เลื่อนเงินเดือน") !== false && $REMARK1=="ดีเด่น") { 
				$MOV_TYPE = 2;
				$MOV_CODE = "21345";
			} elseif (strpos($REMARK,"แก้ไขคำสั่งเงินเดือน") !== false || strpos($REMARK,"แก้ไขคำสั่งเลื่อนขั้น") !== false || strpos($REMARK,"แก้ไขเงินเดือน") !== false || strpos($REMARK,"แก้ไขอัตราเงินเดือน") !== false || strpos($REMARK,"แก้ไขเลื่อนขั้น") !== false) { 
				$MOV_TYPE = 2;
				$MOV_CODE = "11420";
			} elseif (strpos($REMARK,"ยกเลิกคำสั่งเลื่อนขั้น") !== false) { 
				$MOV_TYPE = 2;
				$MOV_CODE = "11410"; 
			} elseif (strpos($REMARK,"เลื่อนขั้นเงินเดือน") !== false || $REMARK=="xx") { 
				$MOV_TYPE = 2;
				$MOV_CODE = "213";
			} elseif (strpos($REMARK,"พ้นจากการทดลอง") !== false || strpos($REMARK,"พ้นทดลอง") !== false || strpos($REMARK,"ห้นทดลอง") !== false || $REMARK=="xx") { 
				$MOV_TYPE = 1;
				$MOV_CODE = "10210";
			} elseif (strpos($REMARK,"ทดลองปฎิบัติราชการ") !== false || $REMARK=="xx") { 
				$MOV_TYPE = 1;
				$MOV_CODE = "102";
			} elseif (strpos($REMARK,"โอนย้ายอัตราเงินเดือน") !== false || $REMARK=="xx") { 
				$MOV_TYPE = 2;
				$MOV_CODE = "21395"; 
			} elseif (strpos($REMARK,"รับโอน") !== false || strpos($REMARK,"โอนมา") !== false || strpos($REMARK,"โอนย้าย") !== false || $REMARK=="xx") { 
				$MOV_TYPE = 1;
				$MOV_CODE = "10510";
			} elseif (strpos($REMARK,"โอนไป") !== false || strpos($REMARK,"โอรไป") !== false || strpos($REMARK,"ให้โอน") !== false || $REMARK=="xx") { 
				$MOV_TYPE = 1;
				$MOV_CODE = "10610";
			} elseif (strpos($REMARK,"รับราชการทหาร") !== false || $REMARK=="xx") { 
				$MOV_TYPE = 1;
				$MOV_CODE = "11820";
			} elseif (strpos($REMARK,"เกษียณก่อนกำหนด") !== false || $REMARK=="xx") { 
				$MOV_TYPE = 1;
				$MOV_CODE = "10830";
			} elseif (strpos($REMARK,"ลาออกจากราชการ") !== false || strpos($REMARK,"ออกจากราชการ") !== false || $REMARK=="xx") { 
				$MOV_TYPE = 1;
				$MOV_CODE = "10810";
			} elseif (strpos($REMARK,"เลื่อนข้าราชการ") !== false || strpos($REMARK,"เลื่อนระดับ") !== false || strpos($REMARK,"เลื่อนข้าราชาการ") !== false || strpos($REMARK,"ปรับระดับ") !== false || $REMARK=="xx") { 
				$MOV_TYPE = 1;
				$MOV_CODE = "104";
			} elseif ($REMARK=="(แต่งตั้งตาม พ.ร.บ.ระเบียบข้าราชการพลเรือน พ.ศ.2551)") { 
				$MOV_TYPE = 1;
				$MOV_CODE = "103"; // *********************
			} elseif (strpos($REMARK,"กลับเข้ารับราชการ") !== false || $REMARK=="xx") { 
				$MOV_TYPE = 1;
				$MOV_CODE = "10140";
			} elseif (strpos($REMARK,"แก้ไขคำสั่งเลื่อนระดับ") !== false || strpos($REMARK,"แก้ไขตำแหน่ง") !== false) { 
				$MOV_TYPE = 1;
				$MOV_CODE = "11420";
			} elseif (strpos($REMARK,"ตัดโอนตำแหน่งและแต่งตั้งข้าราชการ") !== false || strpos($REMARK2,"ตัดโอนตำแหน่งและแต่งตั้งข้าราชการ") !== false || $REMARK=="xx") { 
				$MOV_TYPE = 1;
				$MOV_CODE = "11510"; 
			} elseif (strpos($REMARK,"ตัดโอนตำแหน่ง") !== false || $REMARK=="xx") { 
				$MOV_TYPE = 1;
				$MOV_CODE = "11520"; 
			} elseif (strpos($REMARK,"ปฏิบัติราชการ") !== false || $REMARK=="xx") { 
				$MOV_TYPE = 1;
				$MOV_CODE = "11310"; 
			} elseif (strpos($REMARK,"รักษาราชการ") !== false || $REMARK=="xx") { 
				$MOV_TYPE = 1;
				$MOV_CODE = "11010"; 
			} elseif (strpos($REMARK,"ลาศึกษา") !== false || strpos($REMARK,"ศึกษาต่อ") !== false || $REMARK=="xx") { 
				$MOV_TYPE = 1;
				$MOV_CODE = "11210"; 
			} elseif (strpos($REMARK,"อัตรากำลัง 3 ปี") !== false || strpos($REMARK,"อัตรากำลังแผน 3 ปี") !== false || $REMARK=="xx") { 
				$MOV_TYPE = 1;
				$MOV_CODE = "11210"; 
			}

			$SALARYHIS = $POSITIONHIS = "";		
			$EX_CODE = "024";
			if ($MOV_TYPE==1 || $MOV_TYPE==3)	$POSITIONHIS = 1;		
			if ($MOV_TYPE==2 || $MOV_TYPE==3)	$SALARYHIS = 1;

			$LEVEL_NO = str_pad(trim($data[GOVPOS_C]), 2, "0", STR_PAD_LEFT);
			if ($LEVEL_NO=="21") $LEVEL_NO = "O1";
			if ($LEVEL_NO=="22") $LEVEL_NO = "O2";
			if ($LEVEL_NO=="23") $LEVEL_NO = "O3";
			if ($LEVEL_NO=="24") $LEVEL_NO = "O4";
			if ($LEVEL_NO=="31") $LEVEL_NO = "K1";
			if ($LEVEL_NO=="32") $LEVEL_NO = "K2";
			if ($LEVEL_NO=="33") $LEVEL_NO = "K3";
			if ($LEVEL_NO=="34") $LEVEL_NO = "K4";
			if ($LEVEL_NO=="35") $LEVEL_NO = "K5";
			if ($LEVEL_NO=="41") $LEVEL_NO = "D1";
			if ($LEVEL_NO=="42") $LEVEL_NO = "D2";
			if ($LEVEL_NO=="51") $LEVEL_NO = "M1";
			if ($LEVEL_NO=="52") $LEVEL_NO = "M2";
			if ($LEVEL_NO!="01" && $LEVEL_NO!="02" && $LEVEL_NO!="03" && $LEVEL_NO!="04" && $LEVEL_NO!="05" && $LEVEL_NO!="06" && 
				$LEVEL_NO!="07" && $LEVEL_NO!="08" && $LEVEL_NO!="09" && $LEVEL_NO!="10" && $LEVEL_NO!="11" && $LEVEL_NO!="O1" && 
				$LEVEL_NO!="O2" && $LEVEL_NO!="O3" && $LEVEL_NO!="K1" && $LEVEL_NO!="K2" && $LEVEL_NO!="K3" && $LEVEL_NO!="K4" && 
				$LEVEL_NO!="K5" && $LEVEL_NO!="D1" && $LEVEL_NO!="D2" && $LEVEL_NO!="M1" && $LEVEL_NO!="M2" && $LEVEL_NO!="90" && 
				$LEVEL_NO!="91" && $LEVEL_NO!="92" && $LEVEL_NO!="93" && $LEVEL_NO!="94") {
				$LEVEL_NO = "NULL";
				if ($LEVEL_NO!="96" && $LEVEL_NO!="97" && $LEVEL_NO!="98" && $LEVEL_NO!="99" && $LEVEL_NO!="NULL") echo "$LEVEL_NO<br>";
			} else $LEVEL_NO = trim($LEVEL_NO)? "'".$LEVEL_NO."'" : "NULL";
			$MP_FLAG_CURRENT = trim($data[MP_FLAG_CURRENT]);
			if ($MP_FLAG_CURRENT=="1") $LAST_TRANSACTION = "Y"; 
			else $LAST_TRANSACTION = "N";

			$POH_ISREAL = "Y";
			$ORDERID = $data[ORDERID];
			$ES_CODE = "02";
			$POH_SEQ_NO = $data[GOVPOS_ORDER];
			$POH_CMD_SEQ = $data[ORDERTH]; 

			$STATUS = trim($data[STATUS]);

			if (!$ORG_NAME) $ORG_NAME = "-";
			if (!$POS_NO) $POS_NO = "-";
			if (!$SALARY) $SALARY = 0;
			if (!$SEQ_NO) $SEQ_NO = 1;
			if (!$CMD_SEQ || $CMD_SEQ > 20000) $CMD_SEQ = "NULL";
			if (!$SAH_PERCENT_UP) $SAH_PERCENT_UP = "NULL";
			if (!$SAH_SALARY_UP) $SAH_SALARY_UP = "NULL";
			if (!$SAH_SALARY_EXTRA) $SAH_SALARY_EXTRA = "NULL";
			if ($EFFECTIVEDATE=="-543-00-00") $EFFECTIVEDATE = "-";
			if ($DOCDATE=="-543-00-00" || $DOCDATE=="-") $DOCDATE = "NULL";

			$ORG_ID_1 = "NULL";
			$ORG_ID_2 = "NULL";
			$ORG_ID_3 = "NULL";

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
								'$PER_CARDNO', '$POH_ORG1', '$POH_ORG2', '$POH_ORG3', '$POH_ORG', '$MPOS_NAME', '$WPOS_NAME', $SEQ_NO, 
								'$LAST_TRANSACTION', $CMD_SEQ, '$POH_ISREAL', '$POH_ORG_DOPA_CODE', '$ES_CODE', $LEVEL_NO, '$REMARK1', 
								'$REMARK2', '$DOCNO_EDIT', '$DOCDATE_EDIT') ";
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
								SAH_POSITION, SAH_ORG, EX_CODE, SAH_PAY_NO, SAH_KF_YEAR, SAH_LAST_SALARY, SM_CODE, SAH_CMD_SEQ, SAH_REMARK1, SAH_REMARK2)
								VALUES ($MAX_SAH_ID, $PER_ID, '$EFFECTIVEDATE', '$MOV_CODE', $SALARY, '$DOCNO', 
								'$DOCDATE', NULL, $UPDATE_USER, '$UPDATE_DATE', '$PER_CARDNO', $SAH_PERCENT_UP, $SAH_SALARY_UP, 
								$SAH_SALARY_EXTRA, $SEQ_NO, '$REMARK', $LEVEL_NO, '$POS_NO_NAME', '$POS_NO', 
								'$WPOS_NAME', '$POH_ORG', '$EX_CODE', '$SAH_PAY_NO', '$GOVPOS_YEAR', '$LAST_TRANSACTION', '$SM_CODE', $CMD_SEQ, '$REMARK1', '$REMARK2') ";
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
		$db_dpis35->free_result();
		$db_dpis351->free_result();
	} // end if

	if ($command=='POSEMPHIS'){
// การดำรงตำแหน่ง 976426

		$cmd = " delete from PER_POSITIONHIS where PER_ID in (select PER_ID from PER_PERSONAL where PER_TYPE = 2) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " delete from PER_SALARYHIS where PER_ID in (select PER_ID from PER_PERSONAL where PER_TYPE = 2) ";
		$db_dpis->send_cmd($cmd);
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

		$cmd = " SELECT a.ID, EMPPOS_ORDER, a.SECTION_CODE, a.DEPT_CODE, EMPPOS_PST_NO, a.POS_CODE, a.SEC_CODE, 
						EMPPOS_HISSTEP, EMPPOS_HISSALARY, EMPPOS_DATE, EMPPOS_REFF, EMPPOS_DET, EMPPOS_YEAR, 
						EMPPOS_EVER, EMPPOS_COMCODE, EMPPOS_LASTFLAG, EMPPOS_DEPTXT, EMPPOS_GRPTXT, EMPPOS_SECTXT, 
						EMPPOS_POSTXT, EMPPOS_SECTIONTXT, EMPPOS_MPOSTEXT, EMPPOS_WPOSTEXT
						FROM EMPPOSITION_HISTORY a, EMPLOYEE_HISTORY b
			 			WHERE a.ID = b.ID 
                        ORDER BY a.ID, EMPPOS_ORDER ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "<br>";
		while($data = $db_dpis35->get_array()){
			$PER_ID = trim($data[ID]);
			$EMPPOS_DATE = trim($data[EMPPOS_DATE]);
			$EFFECTIVEDATE = ($EMPPOS_DATE)? ((substr($EMPPOS_DATE, 4, 4) - 543) ."-". substr($EMPPOS_DATE, 2, 2) ."-". substr($EMPPOS_DATE, 0, 2)) : "-";
			$DOCNO = trim($data[EMPPOS_REFF]);
			$DOCNO = str_replace("'", "", $DOCNO);
			$DOCNO = str_replace("  ", " ", $DOCNO);
			$DOCNO = str_replace("  ", " ", $DOCNO);
			$DOCNO = str_replace("  ", " ", $DOCNO);
			$DOCNO = str_replace("  ", " ", $DOCNO);
			$DOCNO = str_replace("  ", " ", $DOCNO);
			$DOCNO = str_replace("  ", " ", $DOCNO);
			$REMARK2 = $DOCNO;
			$DOCDATE = $DOCNO_EDIT = $DOCDATE_EDIT = "";
			if ($DOCNO=="จ.1.5/ 21 /2544 - 72/54/4" || $DOCNO=="จ.1.5/ - 0/0/0") {
				$DOCDATE = "";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง- 8 ก.พ.2551" || $DOCNO=="ปะรกาศกรมทางหลวง - 8 ก.พ.2551" || $DOCNO=="ประกาศกรมทางหลวง ลงวันที่ 8 ก.พ.2551") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "8 ก.พ.2551";
			} elseif ($DOCNO=="จ. 3.3/41/2545 - ลงวันที่ 19 พฤศจิกายน 2545") {
				$DOCNO = "จ. 3.3/41/2545";
				$DOCDATE = "19 พฤศจิกายน 2545";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง 30 ม.ค.50") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "30 ม.ค.50";
			} elseif ($DOCNO=="คค 201/2552 - ลง 1 มิ.ย.2552") {
				$DOCNO = "คค 201/2552";
				$DOCDATE = "1 มิ.ย.2552";
			} elseif ($DOCNO=="ที่ 315/2552 ลวง 12 ต.ค.2552") {
				$DOCNO = "ที่ 315/2552";
				$DOCDATE = "12 ต.ค.2552";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง ลงวันท่ 25 กุมภาพันธ์ 2543") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "25 กุมภาพันธ์ 2543";
			} elseif ($DOCNO=="จ.1.6/14/1/2550 02/02/2550") {
				$DOCNO = "จ.1.6/14/1/2550";
				$DOCDATE = "02/02/2550";
			} elseif ($DOCNO=="จ.1.5/20/35 - 20 มึ.ค.35") {
				$DOCNO = "จ.1.5/20/35";
				$DOCDATE = "20 มึ.ค.35";
			} elseif ($DOCNO=="จ.1.5/178/2551 ลว . 27 ต.ค. 2551") {
				$DOCNO = "จ.1.5/178/2551";
				$DOCDATE = "27 ต.ค. 2551";
			} elseif ($DOCNO=="จ.1.6/67/51 -ก 9 ธ.ค.51") {
				$DOCNO = "จ.1.6/67/51";
				$DOCDATE = "9 ธ.ค.51";
			} elseif ($DOCNO=="จ.1.3/30/2546 ลว. 21 มี.ค. 2546จ.1.1/1/2546 ลว. 8 มกราคม 2546") {
				$DOCNO = "จ.1.3/30/2546 ลว. 21 มี.ค. 2546จ.1.1/1/2546";
				$DOCDATE = "8 มกราคม 2546";
			} elseif ($DOCNO=="จ.1.6/5/2555 - 19ม.ค. 55") {
				$DOCNO = "จ.1.6/5/2555";
				$DOCDATE = "19 ม.ค. 55";
			} elseif ($DOCNO=="ที่ จ.1.1/70/2554 ลว เม.ย. 54") {
				$DOCNO = "ที่ จ.1.1/70/2554";
				$DOCDATE = "12 เม.ย. 54";
			} elseif ($DOCNO=="จ.1.5/ 115/2551 ลว.25 สิงหาคม2551") {
				$DOCNO = "จ.1.5/ 115/2551";
				$DOCDATE = "25 สิงหาคม 2551";
			} elseif ($DOCNO=="จ.1.1/76/53 - 2 มิ.ย.53 แก้ไขคำสั่ง จ.1.1/17/53 - 23 ก.พ.53") {
				$DOCNO = "จ.1.1/76/53";
				$DOCDATE = "2 มิ.ย.53";
				$DOCNO_EDIT = "จ.1.1/17/53";
				$DOCDATE_EDIT = "2010-02-23";
			} elseif ($DOCNO=="จ.1.6/สทล.13/1/2553 ลว.1/8 มกราคม 2553") {
				$DOCNO = "จ.1.6/สทล.13/1/2553";
				$DOCDATE = "18 มกราคม 2553";
			} elseif ($DOCNO=="จ.1.6/สทล.10 ขท.กาญจนบุรี-สุพรรณบุรี(ที่ 2).1/1496/2556 - 25 ธ.ค.56") {
				$DOCNO = "จ.1.6/สทล.10 ขท.กาญจนบุรี-สุพรรณบุรี(ที่ 2).1/1496/2556";
				$DOCDATE = "25 ธ.ค.56";
			} elseif ($DOCNO=="จ.1.1/32/2552 ลว . 23 ก.พ.2552") {
				$DOCNO = "จ.1.1/32/2552";
				$DOCDATE = "23 ก.พ.2552";
			} elseif ($DOCNO=="คำสั่งย้ายข้าราชการ ที่ จ.1.1/ 32 / 2555 ลวงวันที่ 15 มีนาคม 2555") {
				$DOCNO = "คำสั่งย้ายข้าราชการ ที่ จ.1.1/ 32 / 2555";
				$DOCDATE = "15 มีนาคม 2555";
			} elseif ($DOCNO=="จ.1.6/8/2556 - 26 ก.พ2556.") {
				$DOCNO = "จ.1.6/8/2556";
				$DOCDATE = "26 ก.พ2556";
			} elseif ($DOCNO=="จ.1.6/สทล.10 ขท.กาญจนบุรี-สุพรรณบุรี(ที่2).1/1496/2556 - 25 ธ.ค.56") {
				$DOCNO = "จ.1.6/สทล.10 ขท.กาญจนบุรี-สุพรรณบุรี(ที่2).1/1496/2556";
				$DOCDATE = "25 ธ.ค.56";
			} elseif ($DOCNO=="จ. 1.1/2/5/2545 ลว. 1พ.ค. 2545จ. 1.1/ 2/9 /2545 ลว. 6 ก.ย. 2545") {
				$DOCNO = "จ. 1.1/2/5/2545 ลว. 1พ.ค. 2545จ. 1.1/ 2/9 /2545";
				$DOCDATE = "6 ก.ย. 2545";
			} elseif ($DOCNO=="จ.1.3/80/255 -ลว 17 เม.ย. 2555") {
				$DOCNO = "จ.1.3/80/255";
				$DOCDATE = "17 เม.ย. 2555";
			} elseif ($DOCNO=="คค 0603/3919 -ลงวันที่ 25 เม.ย.2543") {
				$DOCNO = "คค 0603/3919";
				$DOCDATE = "25 เม.ย.2543";
			} elseif ($DOCNO=="จ.1.3/3/2550 -ลว. 4 ม.ค.2550") {
				$DOCNO = "จ.1.3/3/2550";
				$DOCDATE = "4 ม.ค.2550";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง 3 พ.ย.43") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "3 พ.ย.43";
			} elseif ($DOCNO=="จ.1.6/39/2551  27 ต.ค.2551") {
				$DOCNO = "จ.1.6/39/2551";
				$DOCDATE = "27 ต.ค.2551";
			} elseif ($DOCNO=="จ.1.5/162/2551 ลว . 13 ต.ค. 2551") {
				$DOCNO = "จ.1.5/162/2551";
				$DOCDATE = "13 ต.ค. 2551";
			} elseif ($DOCNO=="จ.1.5/159/2551 ลว . 10 ต.ค. 2551") {
				$DOCNO = "จ.1.5/159/2551";
				$DOCDATE = "10 ต.ค. 2551";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง 3 มี.ค.2549") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "3 มี.ค.2549";
			} elseif ($DOCNO=="จ.1.5/105/2551ลว . 30 ก.ค. 2551") {
				$DOCNO = "จ.1.5/105/2551";
				$DOCDATE = "30 ก.ค. 2551";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง 3 มี.ค.2549") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "3 มี.ค.2549";
			} elseif ($DOCNO=="ประกาศกรมทางหลวงชลบท 8 มิ.ย.52") {
				$DOCNO = "ประกาศกรมทางหลวงชนบท";
				$DOCDATE = "8 มิ.ย.52";
			} elseif ($DOCNO=="ศย.ที่ 540/51 - 14 พค.51 (ตามแผนอัตรากำลัง 3 ปี)") {
				$DOCNO = "ศย.ที่ 540/51 (ตามแผนอัตรากำลัง 3 ปี)";
				$DOCDATE = "14 พค.51";
			} elseif ($DOCNO=="ที่ จ.1.5/ /2557 ลว เม.ย. 57") {
				$DOCNO = "ที่ จ.1.5/ /2557";
				$DOCDATE = "00 เม.ย. 57";
			} elseif ($DOCNO=="ที่ จ.2.1/ /2555 ลว มี.ค. 55") {
				$DOCNO = "ที่ จ.2.1/ /2555";
				$DOCDATE = "00 มี.ค. 55";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง 8 ก.พ.2551") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "8 ก.พ.2551";
			} elseif ($DOCNO=="คำสั่งที่ จ.1.6/1/2543 ลว. 0 0") {
				$DOCNO = "คำสั่งที่ จ.1.6/1/2543";
				$DOCDATE = "";
			} elseif ($DOCNO=="คำสั่งที่ จ.2.1/ 186 /2542 ลว. 0 0") {
				$DOCNO = "คำสั่งที่ จ.2.1/ 186 /2542";
				$DOCDATE = "";
			} elseif ($DOCNO=="คำสั่งที่ จ.2.1/198/2542 ลว. 0 0") {
				$DOCNO = "คำสั่งที่ จ.2.1/198/2542";
				$DOCDATE = "";
			} elseif ($DOCNO=="ที่ จ.1.5/159/2555 ลว 6 ธ .ค. 55") {
				$DOCNO = "ที่ จ.1.5/159/2555";
				$DOCDATE = "6 ธ.ค. 55";
			} elseif ($DOCNO=="จ.1.6/สทล.4/2/2553-8 ก.พ25.53") {
				$DOCNO = "จ.1.6/สทล.4/2/2553";
				$DOCDATE = "8 ก.พ2553";
			} elseif ($DOCNO=="ศก.ที่ 185/54 - 9 มี.ค. 54 เพิกถอน ศก.ที่ 820/53 - 30 ธ.ค. 53") {
				$DOCNO = "ศก.ที่ 185/54 - 9 มี.ค. 54 เพิกถอน ศก.ที่ 820/53";
				$DOCDATE = "30 ธ.ค. 53";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง ณ วันที่ ท 27 มกราคม 2553") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "27 มกราคม 2553";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง ณ วันที่ 15 มีนาคม 2553") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "15 มีนาคม 2553";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง เรื่อง ลูกจ้างประจำพ้นจากราชการฯ ปี 2549") {
				$DOCNO = "ประกาศกรมทางหลวง เรื่อง ลูกจ้างประจำพ้นจากราชการฯ ปี 2549";
				$DOCDATE = "";
			} elseif ($DOCNO=="ประกาศสำนักทางหลวงที่ 14") {
				$DOCNO = "ประกาศสำนักทางหลวงที่ 14";
				$DOCDATE = "";
			} elseif ($DOCNO=="ราชกิจจาฯ109 ตอนที่ 76 เล่ม 12 หน้า 176-299ลว. 05 ธ.ค. 2534") {
				$DOCNO = "ราชกิจจาฯ109 ตอนที่ 76 เล่ม 12 หน้า 176-299";
				$DOCDATE = "05 ธ.ค. 2534";
			} elseif ($DOCNO=="1/1106/2515ลว.20/12/15-กค.0606(ธ.1)/0052/29") {
				$DOCNO = "1/1106/2515";
				$DOCDATE = "20/12/15";
			} elseif ($DOCNO=="จ.1.13/64/2549 ลว. พ.ย. 2549") {
				$DOCNO = "จ.1.13/64/2549";
				$DOCDATE = "00 พ.ย. 2549";
			} elseif ($DOCNO=="คำสั่งกรมฯ ที่ จ.1.8/20/44 ลว. ธ.ค. 2544") {
				$DOCNO = "คำสั่งกรมฯ ที่ จ.1.8/20/44";
				$DOCDATE = "00 ธ.ค. 2544";
			} elseif ($DOCNO=="ประกาศแขวงการทางกาญจนบุรี-สุพรรณบุรีที่ 2 (อู่ทอง) - 26 พ.ย. 2556") {
				$DOCNO = "ประกาศแขวงการทางกาญจนบุรี-สุพรรณบุรีที่ 2 (อู่ทอง)";
				$DOCDATE = "26 พ.ย. 2556";
			} elseif ($DOCNO=="ประกาศแขวงการทางกาญจนบุรี-สุพรรณบุรี (ที่ 2)") {
				$DOCNO = "ประกาศแขวงการทางกาญจนบุรี-สุพรรณบุรี (ที่ 2)";
				$DOCDATE = "";
			} elseif ($DOCNO=="ประกาศสำนักทางหลวงที่ 5 ถึงแก่กรรมด้วยโรคเบาหวาน เมื่อ 7 พย.56 ตามใบมรณบัตรเลขที่ 04-40015636") {
				$DOCNO = "ประกาศสำนักทางหลวงที่ 5 ถึงแก่กรรมด้วยโรคเบาหวาน เมื่อ 7 พย.56 ตามใบมรณบัตรเลขที่ 04-40015636";
				$DOCDATE = "";
			} elseif ($DOCNO=="คำสั่งกรมฯ ที่ จ.2.2/220/39 ลว. 27 พ.ย. 2539 ประจำยานพาหนะหมายเลข 23-1017/8") {
				$DOCNO = "คำสั่งกรมฯ ที่ จ.2.2/220/39";
				$DOCDATE = "27 พ.ย. 2539";
			} elseif ($DOCNO=="คำสั่งกรมฯ ที่ จ.1.8/15/43 ลว. - 28 ก.ย. 2543") {
				$DOCNO = "คำสั่งกรมฯ ที่ จ.1.8/15/43";
				$DOCDATE = "28 ก.ย. 2543";
			} elseif ($DOCNO=="ใบมรณบัตรเลขที่ 03-92996900 ลว 28 ส.ค. 55") {
				$DOCNO = "ใบมรณบัตรเลขที่ 03-92996900";
				$DOCDATE = "28 ส.ค. 55";
			} elseif ($DOCNO=="ศลว.(จ.1)/57/2521ลว. 10 ม.ค. 2521") {
				$DOCNO = "ศลว.(จ.1)/57/2521";
				$DOCDATE = "10 ม.ค. 2521";
			} elseif ($DOCNO=="ศลว.1/132/16 - 13 มี.ค. 16") {
				$DOCNO = "ศลว.1/132/16";
				$DOCDATE = "13 มี.ค. 16";
			} elseif ($DOCNO=="ศลว.1/266/2516ลว. 17 เม.ย.2516") {
				$DOCNO = "ศลว.1/266/2516";
				$DOCDATE = "17 เม.ย. 2516";
			} elseif ($DOCNO=="ศลว.1/203/2515ลว. 30 มี.ค. 2515") {
				$DOCNO = "ศลว.1/203/2515";
				$DOCDATE = "30 มี.ค. 2515";
			} elseif ($DOCNO=="จ.1.4/23/40-06/1040") {
				$DOCNO = "จ.1.4/23/40-06/1040";
				$DOCDATE = "";
			} elseif ($DOCNO=="พก.0705 - ธ.01/928 - 9 ต.ค. 10") {
				$DOCNO = "พก.0705 - ธ.01/928";
				$DOCDATE = "9 ต.ค. 10";
			} elseif ($DOCNO=="จ.1.13/15/2550 - 2/8 ก.พ. 2550") {
				$DOCNO = "จ.1.13/15/2550";
				$DOCDATE = "28 ก.พ. 2550";
			} elseif ($DOCNO=="ศลว.290(จ.1)/2519ลว. 25 ต.ค. 2519") {
				$DOCNO = "ศลว.290(จ.1)/2519";
				$DOCDATE = "25 ต.ค. 2519";
			} elseif ($DOCNO=="ศลว.1/228/2516ลว. 14 มี.ค.2516") {
				$DOCNO = "ศลว.1/228/2516";
				$DOCDATE = "14 มี.ค. 2516";
			} elseif ($DOCNO=="ที่ ศลว.16.7(จ.1)/2519ลว. 09 เม.ย.2519") {
				$DOCNO = "ที่ ศลว.16.7(จ.1)/2519";
				$DOCDATE = "09 เม.ย. 2519";
			} elseif ($DOCNO=="ศลว.834(จ.1)/2520ลว. 10 พ.ค. 2520") {
				$DOCNO = "ศลว.834(จ.1)/2520";
				$DOCDATE = "10 พ.ค. 2520";
			} elseif ($DOCNO=="ศ.ลว.153(จ.1)/2519ลว. 18 มี.ค. 2519") {
				$DOCNO = "ศ.ลว.153(จ.1)/2519";
				$DOCDATE = "18 มี.ค. 2519";
			} elseif ($DOCNO=="ศลว.834(จ.1)/2520ลว. 10 พ.ค. 2520") {
				$DOCNO = "ศลว.834(จ.1)/2520";
				$DOCDATE = "10 พ.ค. 2520";
			} elseif ($DOCNO=="คำสั่งศูนย์ฯ ที่ ศลว.1/201/2515ลว. 30 มี.ค. 2515") {
				$DOCNO = "คำสั่งศูนย์ฯ ที่ ศลว.1/201/2515";
				$DOCDATE = "30 มี.ค. 2515";
			} elseif ($DOCNO=="มรณบัตร 53-5099") {
				$DOCNO = "มรณบัตร 53-5099";
				$DOCDATE = "";
			} elseif ($DOCNO=="กค 0510 ลว.8322ลว. 17 เม.ย.2517") {
				$DOCNO = "กค 0510/8322";
				$DOCDATE = "17 เม.ย. 2517";
			} elseif ($DOCNO=="กค.0510/8332 - 2517") {
				$DOCNO = "กค.0510/8332";
				$DOCDATE = "17 เม.ย. 2517";
			} elseif ($DOCNO=="จ.2.2/217/39-26-11/39") {
				$DOCNO = "จ.2.2/217/39-26-11/39";
				$DOCDATE = "";
			} elseif ($DOCNO=="ตามใบมรณบัตรเลขที่ 02-4401704") {
				$DOCNO = "ตามใบมรณบัตรเลขที่ 02-4401704";
				$DOCDATE = "";
			} elseif ($DOCNO=="ใบมรณบัตร 02-93998328 - 21 ก.ย. 55") {
				$DOCNO = "ใบมรณบัตร 02-93998328";
				$DOCDATE = "21 ก.ย. 55";
			} elseif ($DOCNO=="ใบมรณบัตร 291/2555 เลขที่ 02-44067320") {
				$DOCNO = "ใบมรณบัตร 291/2555 เลขที่ 02-44067320";
				$DOCDATE = "";
			} elseif ($DOCNO=="ใบมรณบัตร เลขที่ 01-16839135") {
				$DOCNO = "ใบมรณบัตร เลขที่ 01-16839135";
				$DOCDATE = "";
			} elseif ($DOCNO=="ใบมรณบัตร เลขที่ 05-73993374") {
				$DOCNO = "ใบมรณบัตร เลขที่ 05-73993374";
				$DOCDATE = "";
			} elseif ($DOCNO=="ใบมรณบัตรเลขที่ 01-90754676 - 10 มีค.57") {
				$DOCNO = "ใบมรณบัตรเลขที่ 01-90754676";
				$DOCDATE = "10 มี.ค. 57";
			} elseif ($DOCNO=="ใบมรณบัตรเลขที่ 02-94999958") {
				$DOCNO = "ใบมรณบัตรเลขที่ 02-94999958";
				$DOCDATE = "";
			} elseif ($DOCNO=="ใบมรณบัตรเลขที่ 04-40014813") {
				$DOCNO = "ใบมรณบัตรเลขที่ 04-40014813";
				$DOCDATE = "";
			} elseif ($DOCNO=="ใบมรณบัตรเลขที่ 07-90981478 - 15 พค.56") {
				$DOCNO = "ใบมรณบัตรเลขที่ 07-90981478";
				$DOCDATE = "15 พ.ค. 56";
			} elseif ($DOCNO=="ใบมรณบัตรเลขที่01-38890099 - 20 กพ.57") {
				$DOCNO = "ใบมรณบัตรเลขที่01-38890099";
				$DOCDATE = "20 ก.พ. 57";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง ลว 13 มี.ค. 2550") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "13 มี.ค. 2550";
			} elseif ($DOCNO=="ประกาศศูนย์ฯ ขอนแก่น ตามใบมรณบัตรเลขที่ 01-48114936 - 17 ตค.56") {
				$DOCNO = "ประกาศศูนย์ฯ ขอนแก่น ตามใบมรณบัตรเลขที่ 01-48114936";
				$DOCDATE = "17 ต.ค. 56";
			} elseif ($DOCNO=="ประกาศศูนย์สร้างทางหล่มสัก ตามใบมรณบัตรเลขที่ 03-67039364") {
				$DOCNO = "ประกาศศูนย์สร้างทางหล่มสัก ตามใบมรณบัตรเลขที่ 03-67039364";
				$DOCDATE = "";
			} elseif ($DOCNO=="ปรับตามบัญชี 3 ตามหนังสือกค.ด่วนมากที่กค.0503ลว.51906ลว. 04 ธ.ค. 2524") {
				$DOCNO = "ปรับตามบัญชี 3 ตามหนังสือกค.ด่วนมากที่กค.0503ลว.51906";
				$DOCDATE = "04 ธ.ค. 2524";
			} elseif ($DOCNO=="มรณบัตร 02-32056844") {
				$DOCNO = "มรณบัตร 02-32056844";
				$DOCDATE = "";
			} elseif ($DOCNO=="มรณบัตร 02-50115389") {
				$DOCNO = "มรณบัตร 02-50115389";
				$DOCDATE = "";
			} elseif ($DOCNO=="มรณบัตร 02-67024141") {
				$DOCNO = "มรณบัตร 02-67024141";
				$DOCDATE = "";
			} elseif ($DOCNO=="มรณบัตร เลขที่ 02-10230603") {
				$DOCNO = "มรณบัตร เลขที่ 02-10230603";
				$DOCDATE = "";
			} elseif ($DOCNO=="มรณบัตร เลขที่ 05-57992278") {
				$DOCNO = "มรณบัตร เลขที่ 05-57992278";
				$DOCDATE = "";
			} elseif ($DOCNO=="สทล.15 บท.สงขลาที่ 2.1(จ.1)/1280 ใบมรณบัตรเลขที่02-90971303") {
				$DOCNO = "สทล.15 บท.สงขลาที่ 2.1(จ.1)/1280 ใบมรณบัตรเลขที่02-90971303";
				$DOCDATE = "";
			} elseif ($DOCNO=="สร.0203 - 18019 - 20 ก.ย. 21") {
				$DOCNO = "สร.0203 - 18019";
				$DOCDATE = "20 ก.ย. 21";
			} elseif ($DOCNO=="สำเนาใบมรณบัตร เลขที่ 04-74993628") {
				$DOCNO = "สำเนาใบมรณบัตร เลขที่ 04-74993628";
				$DOCDATE = "";
			} elseif ($DOCNO=="สำเนาใบมรณะบัตร เลขที่ 04-21998512") {
				$DOCNO = "สำเนาใบมรณะบัตร เลขที่ 04-21998512";
				$DOCDATE = "";
			} elseif ($DOCNO=="สำเนามรณบัตร เลขที่ 05-50073012 - 13/12/53") {
				$DOCNO = "สำเนามรณบัตร เลขที่ 05-50073012";
				$DOCDATE = "13/12/53";
			} elseif ($DOCNO=="สำเนามรณบัตรเลขที่ 01-44079862 - 03/12/53") {
				$DOCNO = "สำเนามรณบัตรเลขที่ 01-44079862";
				$DOCDATE = "03/12/53";
			} elseif ($DOCNO=="ประกาศแขวงการทางกาญจนบุรี-สุพรรณบุรี (ที่ 2) - 13 ธ.ค. 2556") {
				$DOCNO = "ประกาศแขวงการทางกาญจนบุรี-สุพรรณบุรี (ที่ 2)";
				$DOCDATE = "13 ธ.ค. 56";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง 30 ม.ค.50") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "30 ม.ค.50";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง 30 ม.ค.50") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "30 ม.ค.50";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง 30 ม.ค.50") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "30 ม.ค.50";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง 30 ม.ค.50") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "30 ม.ค.50";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง 30 ม.ค.50") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "30 ม.ค.50";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง 30 ม.ค.50") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "30 ม.ค.50";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง 30 ม.ค.50") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "30 ม.ค.50";
			} elseif (strpos($DOCNO,"- ลงวันที่") !== false) {
				$arr_temp = explode("- ลงวันที่", $DOCNO);
				$DOCNO = $arr_temp[0];
				$DOCDATE = trim($arr_temp[1]);
			} elseif (strpos($DOCNO,"-") !== false && strpos($DOCNO,"ลว.") === false) {
				$arr_temp = explode("-", $DOCNO);
				$DOCNO = $arr_temp[0];
				$DOCDATE = trim($arr_temp[1]);
			} elseif (strpos($DOCNO,"ลงวันที่") !== false) {
				$arr_temp = explode("ลงวันที่", $DOCNO);
				$DOCNO = $arr_temp[0];
				$DOCDATE = trim($arr_temp[1]);
			} elseif (strpos($DOCNO,"ลว.") !== false) {
				$arr_temp = explode("ลว.", $DOCNO);
				$DOCNO = $arr_temp[0];
				$DOCDATE = trim($arr_temp[1]);
			} elseif (strpos($DOCNO,"ลว") !== false && strpos($DOCNO,"กรมทางหลวง") === false) {
				$arr_temp = explode("ลว", $DOCNO);
				$DOCNO = $arr_temp[0];
				$DOCDATE = trim($arr_temp[1]);
			} elseif (strpos($DOCNO,"ณ วันที่ ท") !== false) {
				$arr_temp = explode("ณ วันที่ ท", $DOCNO);
				$DOCNO = $arr_temp[0];
				$DOCDATE = trim($arr_temp[1]);
			} elseif (strpos($DOCNO,"ณ วันที่") !== false) {
				$arr_temp = explode("ณ วันที่", $DOCNO);
				$DOCNO = $arr_temp[0];
				$DOCDATE = trim($arr_temp[1]);
			}
			if ($DOCDATE) {
				$dd = $mm = $yy = "";
				if (strpos($DOCDATE,"/") !== false) {
					$arr_temp = explode("/", $DOCDATE);
					$dd = str_pad(trim($arr_temp[0]), 2, "0", STR_PAD_LEFT);
					$mm = str_pad(trim($arr_temp[1]), 2, "0", STR_PAD_LEFT);
					$yy = trim($arr_temp[2]);
					if (strlen($yy)==2) $yy = "25".$yy;
					if ($yy+0 < 543) {
						if ($mm < substr($EMPPOS_DATE, 2, 2))
							$yy = substr($EMPPOS_DATE, 4, 4) + 1;
						else
							$yy = substr($EMPPOS_DATE, 4, 4);
					}
					$yy = $yy - 543;
					$DOCDATE = $yy."-".$mm."-".$dd;
				} else	if (strpos($DOCDATE," ") !== false) {
					$arr_temp = explode(" ", $DOCDATE);
					$dd = str_pad(trim($arr_temp[0]), 2, "0", STR_PAD_LEFT);
					if (trim($arr_temp[1])=="มกราคม" || trim($arr_temp[1])=="ม.ค." || trim($arr_temp[1])=="ม.ค" || trim($arr_temp[1])=="ม.คง" || trim($arr_temp[1])=="ม,ค,") $mm = "01";
					elseif (trim($arr_temp[1])=="กุมภาพันธ์" || trim($arr_temp[1])=="กุมภาภันธ์" || trim($arr_temp[1])=="กุมหภาพันธ์" || trim($arr_temp[1])=="กุมภาพันธฺ์" || trim($arr_temp[1])=="ก.พ." || 
						trim($arr_temp[1])=="กพ." || trim($arr_temp[1])=="กพ" || trim($arr_temp[1])=="ก.พ" || trim($arr_temp[1])=="กุมภารพันธ์" || trim($arr_temp[1])=="กกุมภาพันธ์") $mm = "02";
					elseif (trim($arr_temp[1])=="มีนาคม" || trim($arr_temp[1])=="มี.ค." || trim($arr_temp[1])=="มี.คง" || trim($arr_temp[1])=="มี.ค" || trim($arr_temp[1])=="มี.ต." || trim($arr_temp[1])=="มึ.ค.") $mm = "03";
					elseif (trim($arr_temp[1])=="เมษายน" || trim($arr_temp[1])=="เม.ย." || trim($arr_temp[1])=="เม.ย" || trim($arr_temp[1])=="เม.ยง") $mm = "04";
					elseif (trim($arr_temp[1])=="พฤษภาคม" || trim($arr_temp[1])=="พ.ค." || trim($arr_temp[1])=="พค." || trim($arr_temp[1])=="พ.ต." || trim($arr_temp[1])=="พ.ค" || trim($arr_temp[1])=="พง๕ง") $mm = "05";
					elseif (trim($arr_temp[1])=="มิถุนายน" || trim($arr_temp[1])=="มิ.ย." || trim($arr_temp[1])=="มิ.ยง" || trim($arr_temp[1])=="มิ.ย") $mm = "06";
					elseif (trim($arr_temp[1])=="กรกฎาคม" || trim($arr_temp[1])=="กรกฏาคม" || trim($arr_temp[1])=="ก.ค." || trim($arr_temp[1])=="ก.คง" || trim($arr_temp[1])=="ก.ต." || trim($arr_temp[1])=="กค.") $mm = "07";
					elseif (trim($arr_temp[1])=="สิงหาคม" || trim($arr_temp[1])=="สิงหาค" || trim($arr_temp[1])=="ส.ค." || trim($arr_temp[1])=="ส.ค" || trim($arr_temp[1])=="ส.คง" || trim($arr_temp[1])=="สค.") $mm = "08";
					elseif (trim($arr_temp[1])=="กันยายน" || trim($arr_temp[1])=="ก.ย." || trim($arr_temp[1])=="ก.ย" || trim($arr_temp[1])=="ก.ยง") $mm = "09";
					elseif (trim($arr_temp[1])=="ตุลาคม" || trim($arr_temp[1])=="ต.ค." || trim($arr_temp[1])=="ต.คย." || trim($arr_temp[1])=="ตค" || trim($arr_temp[1])=="ต.ค" || 
						trim($arr_temp[1])=="ต.คง" || trim($arr_temp[1])=="าต.ค." || trim($arr_temp[1])=="ตงค.") $mm = "10";
					elseif (trim($arr_temp[1])=="พฤศจิกายน" || trim($arr_temp[1])=="พ.ย." || trim($arr_temp[1])=="พย" || trim($arr_temp[1])=="พย." || trim($arr_temp[1])=="พ.ย" || 
						trim($arr_temp[1])=="พ.ยง" || trim($arr_temp[1])=="พ..ย") $mm = "11";
					elseif (trim($arr_temp[1])=="ธันวาคม" || trim($arr_temp[1])=="ธันวาคา" || trim($arr_temp[1])=="ธ.ค." || trim($arr_temp[1])=="ธ.คง" || trim($arr_temp[1])=="ธ.ค" || trim($arr_temp[1])=="ธ .ค." || 
						trim($arr_temp[1])=="ธค") $mm = "12";
					elseif (substr($arr_temp[1],0,4)=="ม.ค.") {
						$mm = "01";
						$yy = trim(substr($arr_temp[1],4));
					} elseif (substr($arr_temp[1],0,3)=="มค." || substr($arr_temp[1],0,3)=="ม.ค") {
						$mm = "01";
						$yy = trim(substr($arr_temp[1],3));
					} elseif (substr($arr_temp[1],0,4)=="ก.พ." || substr($arr_temp[1],0,4)=="กงพ.") {
						$mm = "02";
						$yy = trim(substr($arr_temp[1],4));
					} elseif (substr($arr_temp[1],0,3)=="กพ." || substr($arr_temp[1],0,3)=="ก.พ") {
						$mm = "02";
						$yy = trim(substr($arr_temp[1],3));
					} elseif (substr($arr_temp[1],0,5)=="มี.ค." || substr($arr_temp[1],0,5)=="ม๊.ค." || substr($arr_temp[1],0,5)=="มึ.ค." || substr($arr_temp[1],0,5)=="มี.คง") {
						$mm = "03";
						$yy = trim(substr($arr_temp[1],5));
					} elseif (substr($arr_temp[1],0,4)=="มีค.") {
						$mm = "03";
						$yy = trim(substr($arr_temp[1],4));
					} elseif (substr($arr_temp[1],0,5)=="เม.ย." || substr($arr_temp[1],0,5)=="เม.ยง") {
						$mm = "04";
						$yy = trim(substr($arr_temp[1],5));
					} elseif (substr($arr_temp[1],0,4)=="เมย." || substr($arr_temp[1],0,4)=="เมษ." || substr($arr_temp[1],0,4)=="เม.ย") {
						$mm = "04";
						$yy = trim(substr($arr_temp[1],4));
					} elseif (substr($arr_temp[1],0,4)=="พ.ค." || substr($arr_temp[1],0,4)=="พ.คง" || substr($arr_temp[1],0,4)=="พงค." || substr($arr_temp[1],0,4)=="พ.ต.") {
						$mm = "05";
						$yy = trim(substr($arr_temp[1],4));
					} elseif (substr($arr_temp[1],0,3)=="พค.") {
						$mm = "05";
						$yy = trim(substr($arr_temp[1],3));
					} elseif (substr($arr_temp[1],0,5)=="มิ.ย." || substr($arr_temp[1],0,5)=="มิ.ยง") {
						$mm = "06";
						$yy = trim(substr($arr_temp[1],5));
					} elseif (substr($arr_temp[1],0,4)=="มิย.") {
						$mm = "06";
						$yy = trim(substr($arr_temp[1],4));
					} elseif (substr($arr_temp[1],0,4)=="ก.ค." || substr($arr_temp[1],0,4)=="ก.คง" || substr($arr_temp[1],0,4)=="ก.ต.") {
						$mm = "07";
						$yy = trim(substr($arr_temp[1],4));
					} elseif (substr($arr_temp[1],0,3)=="กค.") {
						$mm = "07";
						$yy = trim(substr($arr_temp[1],3));
					} elseif (substr($arr_temp[1],0,4)=="ส.ค.") {
						$mm = "08";
						$yy = trim(substr($arr_temp[1],4));
					} elseif (substr($arr_temp[1],0,3)=="สค.") {
						$mm = "08";
						$yy = trim(substr($arr_temp[1],3));
					} elseif (substr($arr_temp[1],0,4)=="ก.ย." || substr($arr_temp[1],0,4)=="ก.ยง" || substr($arr_temp[1],0,4)=="กซนซ" || substr($arr_temp[1],0,4)=="ก.ยซ") {
						$mm = "09";
						$yy = trim(substr($arr_temp[1],4));
					} elseif (substr($arr_temp[1],0,3)=="กย." || substr($arr_temp[1],0,2)=="กย") {
						$mm = "09";
						$yy = trim(substr($arr_temp[1],2));
					} elseif (substr($arr_temp[1],0,4)=="ต.ค." || substr($arr_temp[1],0,4)=="ต.คง" || substr($arr_temp[1],0,4)=="ตงค.") {
						$mm = "10";
						$yy = trim(substr($arr_temp[1],4));
					} elseif (substr($arr_temp[1],0,3)=="ตค." || substr($arr_temp[1],0,3)=="ต.ต") {
						$mm = "10";
						$yy = trim(substr($arr_temp[1],3));
					} elseif (substr($arr_temp[1],0,3)=="ต.ค") {
						$mm = "10";
						$yy = trim(substr($arr_temp[1],3));
					} elseif (substr($arr_temp[1],0,4)=="พ.ย.") {
						$mm = "11";
						$yy = trim(substr($arr_temp[1],4));
					} elseif (substr($arr_temp[1],0,3)=="พย.") {
						$mm = "11";
						$yy = trim(substr($arr_temp[1],3));
					} elseif (substr($arr_temp[1],0,4)=="ธ.ค." || substr($arr_temp[1],0,4)=="ธ.คง") {
						$mm = "12";
						$yy = trim(substr($arr_temp[1],4));
					} elseif (substr($arr_temp[1],0,3)=="ธค.") {
						$mm = "12";
						$yy = trim(substr($arr_temp[1],3));
					} elseif ($arr_temp[0]=="13มี.ค." && $arr_temp[1]=="16") {
						$dd = "13";
						$mm = "03";
						$yy = "2516";
					} elseif ($arr_temp[0]=="20ก.ย." && $arr_temp[1]=="21") {
						$dd = "20";
						$mm = "09";
						$yy = "2521";
					} elseif ($arr_temp[0]=="21ธ.ค." && $arr_temp[1]=="2542") {
						$dd = "21";
						$mm = "12";
						$yy = "2542";
					} elseif ($arr_temp[0]=="25กุมภาพันธ์" && $arr_temp[1]=="2543") {
						$dd = "25";
						$mm = "02";
						$yy = "2543";
					} elseif ($arr_temp[0]=="27ต.ค." && $arr_temp[1]=="2543") {
						$dd = "27";
						$mm = "10";
						$yy = "2543";
					} elseif ($arr_temp[0]=="22พ.ค." && $arr_temp[1]=="2545") {
						$dd = "22";
						$mm = "05";
						$yy = "2545";
					} elseif ($arr_temp[0]=="1พ.ย." && $arr_temp[1]=="2545") {
						$dd = "01";
						$mm = "11";
						$yy = "2545";
					} elseif ($arr_temp[0]=="4ธันวาคม" && $arr_temp[1]=="2545") {
						$dd = "04";
						$mm = "12";
						$yy = "2545";
					} elseif ($arr_temp[0]=="28ม.ค." && $arr_temp[1]=="2546") {
						$dd = "28";
						$mm = "01";
						$yy = "2546";
					} elseif ($arr_temp[0]=="11สิงหาคม" && $arr_temp[1]=="2546") {
						$dd = "11";
						$mm = "08";
						$yy = "2546";
					} elseif ($arr_temp[0]=="17มกราคม" && $arr_temp[1]=="2550") {
						$dd = "17";
						$mm = "01";
						$yy = "2550";
					} elseif ($arr_temp[0]=="1ก.พ." && $arr_temp[1]=="2551") {
						$dd = "01";
						$mm = "02";
						$yy = "2551";
					} elseif ($arr_temp[0]=="27ก.พ." && $arr_temp[1]=="2551") {
						$dd = "27";
						$mm = "02";
						$yy = "2551";
					} elseif ($arr_temp[0]=="7ต.ค." && $arr_temp[1]=="2551") {
						$dd = "07";
						$mm = "10";
						$yy = "2551";
					} elseif ($arr_temp[0]=="27ก.พ." && $arr_temp[1]=="52") {
						$dd = "27";
						$mm = "02";
						$yy = "2552";
					} elseif ($arr_temp[0]=="11ก.ย." && $arr_temp[1]=="52") {
						$dd = "11";
						$mm = "09";
						$yy = "2552";
					} elseif ($arr_temp[0]=="30พ.ค." && $arr_temp[1]=="2555") {
						$dd = "30";
						$mm = "05";
						$yy = "2555";
					} elseif ($arr_temp[0]=="28" && $arr_temp[1]=="กรกฎาคม2547") {
						$dd = "28";
						$mm = "07";
						$yy = "2547";
					} elseif ($arr_temp[0]=="6" && $arr_temp[1]=="ธันวาคม2550") {
						$dd = "06";
						$mm = "12";
						$yy = "2550";
					} elseif ($arr_temp[0]=="11" && $arr_temp[1]=="ธันวาคม2552") {
						$dd = "11";
						$mm = "12";
						$yy = "2552";
					} elseif ($arr_temp[0]=="16" && $arr_temp[1]=="ธ.ค2556.") {
						$dd = "16";
						$mm = "12";
						$yy = "2556";
					} elseif ($arr_temp[0]=="8ส.ค." && $arr_temp[1]=="55") {
						$dd = "08";
						$mm = "08";
						$yy = "2555";
					} elseif ($arr_temp[0]=="20" && $arr_temp[1]=="ส.57") {
						$dd = "20";
						$mm = "08";
						$yy = "2557";
					} elseif ($arr_temp[0]=="22เม.ย." && $arr_temp[1]=="57") {
						$dd = "22";
						$mm = "04";
						$yy = "2557";
					} elseif ($arr_temp[0]=="31" && $arr_temp[1]=="จต.57") {
						$dd = "31";
						$mm = "10";
						$yy = "2557";
					} elseif ($arr_temp[0]=="31มี.ค." && $arr_temp[1]=="35") {
						$dd = "31";
						$mm = "03";
						$yy = "2535";
					} elseif ($arr_temp[0]=="7" && $arr_temp[1]=="ก." && $arr_temp[2]=="ย.53") {
						$dd = "07";
						$mm = "09";
						$yy = "2553";
					} else echo "$arr_temp[0]**$arr_temp[1]**$arr_temp[2]**$REMARK2<br>";
					if (!$yy) $yy = trim($arr_temp[2]);
					if (strlen($yy)==2) $yy = "25".$yy;
					if ($yy+0 < 543) {
						if ($mm < substr($EMPPOS_DATE, 2, 2))
							$yy = substr($EMPPOS_DATE, 4, 4) + 1;
						else
							$yy = substr($EMPPOS_DATE, 4, 4);
					}
					$yy = $yy - 543;
					$DOCDATE = $yy."-".$mm."-".$dd; 
				} else	if ($DOCDATE=="31ตค.09") {
					$DOCDATE = "1966-10-31";
				} else	if ($DOCDATE=="15พค.10") {
					$DOCDATE = "1967-05-15";
				} else	if ($DOCDATE=="16มิย.10") {
					$DOCDATE = "1967-06-16";
				} else	if ($DOCDATE=="5กค.10") {
					$DOCDATE = "1967-07-05";
				} else	if ($DOCDATE=="13มิย.11") {
					$DOCDATE = "1968-06-13";
				} else	if ($DOCDATE=="26กย.11") {
					$DOCDATE = "1968-09-26";
				} else	if ($DOCDATE=="12พค.12") {
					$DOCDATE = "1969-05-12";
				} else	if ($DOCDATE=="27เมย.13") {
					$DOCDATE = "1970-04-27";
				} else	if ($DOCDATE=="29มิย.14") {
					$DOCDATE = "1971-06-29";
				} else	if ($DOCDATE=="20ตค.14") {
					$DOCDATE = "1971-10-20";
				} else	if ($DOCDATE=="30มิย.15") {
					$DOCDATE = "1972-06-30";
				} else	if ($DOCDATE=="21พค.16)") {
					$DOCDATE = "1973-05-21";
				} else	if ($DOCDATE=="25052516" || $DOCDATE=="25พค.16") {
					$DOCDATE = "1973-05-16";
				} else	if ($DOCDATE=="07052517" || $DOCDATE=="7พค.17") {
					$DOCDATE = "1974-05-07";
				} else	if ($DOCDATE=="17พค.17") {
					$DOCDATE = "1974-05-17";
				} else	if ($DOCDATE=="21มีค.18") {
					$DOCDATE = "1975-03-21";
				} else	if ($DOCDATE=="14พค.18") {
					$DOCDATE = "1975-05-14";
				} else	if ($DOCDATE=="5กย.18") {
					$DOCDATE = "1975-09-05";
				} else	if ($DOCDATE=="18กย.18") {
					$DOCDATE = "1975-09-18";
				} else	if ($DOCDATE=="18ธค.18") {
					$DOCDATE = "1975-12-18";
				} else	if ($DOCDATE=="27กพ.19") {
					$DOCDATE = "1976-02-27";
				} else	if ($DOCDATE=="01102519") {
					$DOCDATE = "1976-10-01";
				} else	if ($DOCDATE=="13มค.20") {
					$DOCDATE = "1977-01-13";
				} else	if ($DOCDATE=="2กพ.20") {
					$DOCDATE = "1977-02-02";
				} else	if ($DOCDATE=="13มิย.20") {
					$DOCDATE = "1977-06-13";
				} else	if ($DOCDATE=="14มิย.20") {
					$DOCDATE = "1977-06-14";
				} else	if ($DOCDATE=="23กย.20") {
					$DOCDATE = "1977-09-23";
				} else	if ($DOCDATE=="20ก.ย.21" || $DOCDATE=="20กย.21") {
					$DOCDATE = "1978-09-20";
				} else	if ($DOCDATE=="11ตค.21") {
					$DOCDATE = "1978-10-11";
				} else	if ($DOCDATE=="27เมย.22") {
					$DOCDATE = "1979-04-27";
				} else	if ($DOCDATE=="17กย.23") {
					$DOCDATE = "1980-09-17";
				} else	if ($DOCDATE=="2ตค.23") {
					$DOCDATE = "1980-10-02";
				} else	if ($DOCDATE=="3ตค.23") {
					$DOCDATE = "1980-10-03";
				} else	if ($DOCDATE=="18กพ.24") {
					$DOCDATE = "1981-02-18";
				} else	if ($DOCDATE=="27มค.25") {
					$DOCDATE = "1982-01-27";
				} else	if ($DOCDATE=="15กพ.25") {
					$DOCDATE = "1982-02-15";
				} else	if ($DOCDATE=="2ธค.25") {
					$DOCDATE = "1982-12-02";
				} else	if ($DOCDATE=="21กย.26") {
					$DOCDATE = "1983-09-21";
				} else	if ($DOCDATE=="30พย.26") {
					$DOCDATE = "1983-11-26";
				} else	if ($DOCDATE=="11กย.27") {
					$DOCDATE = "1984-09-11";
				} else	if ($DOCDATE=="18กย.27") {
					$DOCDATE = "1984-09-18";
				} else	if ($DOCDATE=="31ตค.27") {
					$DOCDATE = "1984-10-31";
				} else	if ($DOCDATE=="5เมย.28") {
					$DOCDATE = "1985-04-05";
				} else	if ($DOCDATE=="12กย.28") {
					$DOCDATE = "1985-09-28";
				} else	if ($DOCDATE=="31ตค.28") {
					$DOCDATE = "1985-10-31";
				} else	if ($DOCDATE=="23กย.29") {
					$DOCDATE = "1986-09-23";
				} else	if ($DOCDATE=="28กย29") {
					$DOCDATE = "1986-09-28";
				} else	if ($DOCDATE=="01ม.ค.2532") {
					$DOCDATE = "1989-01-01";
				} else	if ($DOCDATE=="06มี.ค.2533") {
					$DOCDATE = "1990-03-06";
				} else	if ($DOCDATE=="29062536") {
					$DOCDATE = "1993-06-29";
				} else	if ($DOCDATE=="23มี.ค.38") {
					$DOCDATE = "1995-03-23";
				} else	if ($DOCDATE=="26มี.ค.2541") {
					$DOCDATE = "1998-03-26";
				} else	if ($DOCDATE=="21ธ.ค. 2542") {
					$DOCDATE = "1999-12-21";
				} else	if ($DOCDATE=="27ต.ค. 2543") {
					$DOCDATE = "2000-10-27";
				} else	if ($DOCDATE=="20ก.ย.2544") {
					$DOCDATE = "2001-09-20";
				} else	if ($DOCDATE=="7พ.ย.2544") {
					$DOCDATE = "2001-11-07";
				} else	if ($DOCDATE=="22พ.ค. 2545") {
					$DOCDATE = "2002-05-22";
				} else	if ($DOCDATE=="1พ.ย. 2545") {
					$DOCDATE = "2002-11-01";
				} else	if ($DOCDATE=="4ธันวาคม 2545") {
					$DOCDATE = "2002-12-04";
				} else	if ($DOCDATE=="28ม.ค. 2546") {
					$DOCDATE = "2003-01-28";
				} else	if ($DOCDATE=="17ก.ค.46") {
					$DOCDATE = "2003-07-17";
				} else	if ($DOCDATE=="11สิงหาคม 2546") {
					$DOCDATE = "2003-08-11";
				} else	if ($DOCDATE=="28  กรกฎาคม2547") {
					$DOCDATE = "2004-07-28";
				} else	if ($DOCDATE=="11ม.ค.2548") {
					$DOCDATE = "2005-01-11";
				} else	if ($DOCDATE=="17ม.ค.2548") {
					$DOCDATE = "2005-01-17";
				} else	if ($DOCDATE=="13ต.ค.2548") {
					$DOCDATE = "2005-10-13";
				} else	if ($DOCDATE=="23ม.ค.49") {
					$DOCDATE = "2006-01-23";
				} else	if ($DOCDATE=="18พ.ค.2549") {
					$DOCDATE = "2006-05-18";
				} else	if ($DOCDATE=="25พ.ค.2549") {
					$DOCDATE = "2006-05-25";
				} else	if ($DOCDATE=="8ส.ค.2549") {
					$DOCDATE = "2006-08-08";
				} else	if ($DOCDATE=="14ก.ย.2549") {
					$DOCDATE = "2006-09-14";
				} else	if ($DOCDATE=="24ต.ค.2549") {
					$DOCDATE = "2006-10-24";
				} else	if ($DOCDATE=="22มี.ค.2550") {
					$DOCDATE = "2007-03-22";
				} else	if ($DOCDATE=="18เม.ย.2550") {
					$DOCDATE = "2007-04-18";
				} else	if ($DOCDATE=="7ส.ค.2550") {
					$DOCDATE = "2007-08-07";
				} else	if ($DOCDATE=="22ส.ค.2550") {
					$DOCDATE = "2007-08-22";
				} else	if ($DOCDATE=="6 ธันวาคม2550") {
					$DOCDATE = "2007-12-06";
				} else	if ($DOCDATE=="1ก.พ. 2551") {
					$DOCDATE = "2008-02-01";
				} else	if ($DOCDATE=="12พ.ค2551") {
					$DOCDATE = "2008-05-12";
				} else	if ($DOCDATE=="7ต.ค. 2551") {
					$DOCDATE = "2008-10-07";
				} else	if ($DOCDATE=="1-12-51") {
					$DOCDATE = "2008-12-01";
				} else	if ($DOCDATE=="27ก.พ. 52") {
					$DOCDATE = "2009-02-27";
				} else	if ($DOCDATE=="11ก.ย. 52") {
					$DOCDATE = "2009-09-11";
				} else	if ($DOCDATE=="3พ.ย.2552") {
					$DOCDATE = "2009-11-03";
				} else	if ($DOCDATE=="6พ.ย.2552") {
					$DOCDATE = "2009-11-06";
				} else	if ($DOCDATE=="11พ.ย.2552") {
					$DOCDATE = "2009-11-11";
				} else	if ($DOCDATE=="12พ.ย.2552") {
					$DOCDATE = "2009-11-12";
				} else	if ($DOCDATE=="8 ก.พ25.53") {
					$DOCDATE = "2010-02-08";
				} else	if ($DOCDATE=="03032553") {
					$DOCDATE = "2010-03-03";
				} else	if ($DOCDATE=="01012554") {
					$DOCDATE = "2011-01-01";
				} else	if ($DOCDATE=="12ม.ค.2555") {
					$DOCDATE = "2012-01-12";
				} else	if ($DOCDATE=="4พ.ค.55") {
					$DOCDATE = "2012-05-04";
				} else	if ($DOCDATE=="8ส.ค. 55") {
					$DOCDATE = "2012-08-08";
				} else	if ($DOCDATE=="20 ส.57") {
					$DOCDATE = "2014-08-20";
				} else	if ($DOCDATE=="22เม.ย. 57") {
					$DOCDATE = "2014-04-22";
				} else	if ($DOCDATE=="31 จต.57") {
					$DOCDATE = "2014-10-31";
				} else	if ($DOCDATE=="07052517") {
					$DOCDATE = "1974-05-07";
				} else	if ($DOCDATE=="07052517") {
					$DOCDATE = "1974-05-07";
				} else	if ($DOCDATE=="07052517") {
					$DOCDATE = "1974-05-07";
				} else	if ($DOCDATE=="07052517") {
					$DOCDATE = "1974-05-07";
				} else	if ($DOCDATE=="07052517") {
					$DOCDATE = "1974-05-07";
				} else	if ($DOCDATE=="07052517") {
					$DOCDATE = "1974-05-07";
				} else	if ($DOCDATE=="07052517") {
					$DOCDATE = "1974-05-07";
				} else	if ($DOCDATE=="07052517") {
					$DOCDATE = "1974-05-07";
				} else	if ($DOCDATE=="07052517") {
					$DOCDATE = "1974-05-07";
				} else	if ($DOCDATE=="07052517") {
					$DOCDATE = "1974-05-07";
				} else	if ($DOCDATE=="07052517") {
					$DOCDATE = "1974-05-07";
				} else	if ($DOCDATE=="07052517") {
					$DOCDATE = "1974-05-07";
				} else	if ($DOCDATE=="07052517") {
					$DOCDATE = "1974-05-07";
				} else	if ($DOCDATE=="07052517") {
					$DOCDATE = "1974-05-07";
				} else	if ($DOCDATE=="07052517") {
					$DOCDATE = "1974-05-07";
				} else	if ($DOCDATE=="07052517") {
					$DOCDATE = "1974-05-07";
				} else	if ($DOCDATE=="07052517") {
					$DOCDATE = "1974-05-07";
				} else	if ($DOCDATE=="07052517") {
					$DOCDATE = "1974-05-07";
				} else	if ($DOCDATE=="07052517") {
					$DOCDATE = "1974-05-07";
				} else	if ($DOCDATE=="07052517") {
					$DOCDATE = "1974-05-07";
				} else	if ($DOCDATE=="07052517") {
					$DOCDATE = "1974-05-07";
				} else	if ($DOCDATE=="07052517") {
					$DOCDATE = "1974-05-07";
				} else	if ($DOCDATE=="07052517") {
					$DOCDATE = "1974-05-07";
				} else	if ($DOCDATE=="07052517") {
					$DOCDATE = "1974-05-07";
				} else	if ($DOCDATE=="07052517") {
					$DOCDATE = "1974-05-07";
				} else	if ($DOCDATE=="07052517") {
					$DOCDATE = "1974-05-07";
				} else	if ($DOCDATE=="07052517") {
					$DOCDATE = "1974-05-07";
				} else	if ($DOCDATE=="07052517") {
					$DOCDATE = "1974-05-07";
				} else	if ($DOCDATE=="07052517") {
					$DOCDATE = "1974-05-07";
				} else	if ($DOCDATE=="07052517") {
					$DOCDATE = "1974-05-07";
				} else	if ($DOCDATE=="07052517") {
					$DOCDATE = "1974-05-07";
				} else	if ($DOCDATE=="07052517") {
					$DOCDATE = "1974-05-07";
				} else	if ($DOCDATE=="07052517") {
					$DOCDATE = "1974-05-07";
				} else	if ($DOCDATE=="07052517") {
					$DOCDATE = "1974-05-07";
				} else	if ($DOCDATE=="07052517") {
					$DOCDATE = "1974-05-07";
				}
			}
			$EMPPOS_YEAR = trim($data[EMPPOS_YEAR]);

			if (substr($DOCDATE,0,4)=="0425") $DOCDATE = "1984".substr($DOCDATE,4);
			elseif (substr($DOCDATE,0,4)=="0009") $DOCDATE = "1991".substr($DOCDATE,4);
			elseif (substr($DOCDATE,0,4)=="0008") $DOCDATE = "1992".substr($DOCDATE,4);
			elseif (substr($DOCDATE,0,4)=="0383") $DOCDATE = "1993".substr($DOCDATE,4);
			elseif (substr($DOCDATE,0,4)=="1822") $DOCDATE = "1993".substr($DOCDATE,4);
			elseif (substr($DOCDATE,0,4)=="0006" && $CUR_YEAR=="2537") $DOCDATE = "1994".substr($DOCDATE,4);
			elseif (substr($DOCDATE,0,4)=="0286" && $CUR_YEAR=="2537") $DOCDATE = "1994".substr($DOCDATE,4);

			if (substr($EFFECTIVEDATE,0,4)=="0494") $EFFECTIVEDATE = "2006".substr($EFFECTIVEDATE,4);
			elseif (substr($EFFECTIVEDATE,0,4)=="0291") $EFFECTIVEDATE = "1977".substr($EFFECTIVEDATE,4);
			elseif (substr($EFFECTIVEDATE,0,4)=="1857" && $CUR_YEAR=="2533") $EFFECTIVEDATE = "1990".substr($EFFECTIVEDATE,4);

			$POS_NO = trim($data[EMPPOS_PST_NO]);
			$WPOS_CODE = trim($data[WPOS_CODE]);
			$WPOS_NAME = trim($data[WPOS_NAME]);
			if ($WPOS_NAME=="'") $WPOS_NAME = "";
			$cmd = " select PL_CODE from PER_LINE where PL_NAME = '$WPOS_NAME' or PL_SHORTNAME = '$WPOS_NAME' ";
			$db_dpis->send_cmd($cmd);
			$data_dpis = $db_dpis->get_array();
			$PL_CODE = trim($data_dpis[PL_CODE]);

			$MPOS_CODE = trim($data[MPOS_CODE]);
			$MPOS_NAME = trim($data[MPOS_NAME]);
			$PM_CODE = "";
			if ($MPOS_NAME) { 
				$cmd = " select PM_CODE from PER_MGT where PM_NAME = '$MPOS_NAME' or PM_SHORTNAME = '$MPOS_NAME' ";
				$db_dpis->send_cmd($cmd);
				$data_dpis = $db_dpis->get_array();
				$PM_CODE = trim($data_dpis[PM_CODE]);
			} 

			$POH_ORG1 = "กระทรวงคมนาคม";
			$POH_ORG2 = "กรมทางหลวง";
			$POH_ORG3 = trim($data[EMPPOS_DEPTXT]);
			$POH_ORG3 = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($POH_ORG3)));
			$POH_ORG3 = str_replace("'", "", trim($POH_ORG3));
			if ($POH_ORG3=="กองประมวลผลสถิติ") {
				$POH_ORG1 = "สำนักนายกรัฐมนตรี";
				$POH_ORG2 = "สำนักงานสถิติแห่งชาติ";
			}
			$POH_UNDER_ORG1 = trim($data[EMPPOS_SECTIONTXT]);
			$POH_UNDER_ORG1 = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($POH_UNDER_ORG1)));
			$POH_UNDER_ORG1 = str_replace("'", "", trim($POH_UNDER_ORG1));
			$POH_UNDER_ORG2 = trim($data[JOB_NAME]);
			$POH_UNDER_ORG2 = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($POH_UNDER_ORG2)));
			$POH_UNDER_ORG2 = str_replace("'", "", trim($POH_UNDER_ORG2));
			$POH_ORG = trim((($POH_UNDER_ORG2=="-") ? "":$POH_UNDER_ORG2)." ".(($POH_UNDER_ORG1=="-") ? "":$POH_UNDER_ORG1)." ".
											(($POH_ORG3=="-") ? "":$POH_ORG3)." ".$POH_ORG2);
			$SALARY = $data[EMPPOS_HISSALARY];
			$SAH_SALARY_UP = $data[EMPPOS_MONEY_UP];
			$SAH_SALARY_EXTRA = $data[EMPPOS_MONEY_SPEC];
			$SAH_PERCENT_UP = $data[EMPPOS_PRAMEAUN_PERCENT];
			$FLAG_TO_NAME = trim($data[FLAG_TO_NAME]);
			$SPECIALIST_NAME = trim($data[SPECIALIST_NAME]);
			$REMARK = trim($data[EMPPOS_DET]);
			$REMARK = str_replace("'", "", $REMARK);
			$REMARK1 = trim($data[EMPPOS_PRAMEAUN_LAYER]);
			$FLAG_TO_NAME_CODE = trim($data[FLAG_TO_NAME_CODE]);
			$POH_SALARY_POS = 0;
			$EMPPOS_COMCODE = trim($data[EMPPOS_COMCODE]);

			if ($EMPPOS_COMCODE=="0") $MOV_CODE = "99999"; // ผิด **********************
			elseif ($EMPPOS_COMCODE=="1") $MOV_CODE = "101"; // บรรจุใหม่
			elseif ($EMPPOS_COMCODE=="10") $MOV_CODE = "115"; // ปรับระดับชั้นลูกจ้างประจำ
			elseif ($EMPPOS_COMCODE=="11") $MOV_CODE = "24040"; // 1.5 ขั้น
			elseif ($EMPPOS_COMCODE=="12") $MOV_CODE = "24060"; // 2 ขั้น
			elseif ($EMPPOS_COMCODE=="13") $MOV_CODE = "24510"; // การเพิ่มค่าจ้างลูกจ้างประจำ
			elseif ($EMPPOS_COMCODE=="14") $MOV_CODE = "245"; // การปรับค่าจ้างลูกจ้างประจำ
			elseif ($EMPPOS_COMCODE=="15") $MOV_CODE = "21410"; // เต็มขั้น
			elseif ($EMPPOS_COMCODE=="16") $MOV_CODE = "24070"; // งดขั้นค่าจ้าง ************************
			elseif ($EMPPOS_COMCODE=="17") $MOV_CODE = "246"; // ลดขั้นค่าจ้าง
			elseif ($EMPPOS_COMCODE=="18") $MOV_CODE = "11810"; // ลาออก
			elseif ($EMPPOS_COMCODE=="19") $MOV_CODE = "11910"; // เกษียณ
			elseif ($EMPPOS_COMCODE=="2") $MOV_CODE = "10140"; // กลับเข้ารับราชการใหม่
			elseif ($EMPPOS_COMCODE=="20") $MOV_CODE = "12310"; // ตาย
			elseif ($EMPPOS_COMCODE=="21") $MOV_CODE = "106"; // โอนไป
			elseif ($EMPPOS_COMCODE=="22") $MOV_CODE = "11310"; // ปฏิบัติราชการ *****************
			elseif ($EMPPOS_COMCODE=="23") $MOV_CODE = "24520"; // แก้ไขอัตราค่าจ้าง (โอนย้าย/ปรับระดับชั้น/แต่งตั้ง)
			elseif ($EMPPOS_COMCODE=="24") $MOV_CODE = "12210"; // ไล่ออก
			elseif ($EMPPOS_COMCODE=="25") $MOV_CODE = "24080"; // การเพิ่มค่าจ้างลูกจ้างประจำเป็นกรณีพิเศษ 2 ขั้นซึ่งถึงแก่ความตาย
			elseif ($EMPPOS_COMCODE=="26") $MOV_CODE = "24530"; // การเพิ่มค่าจ้างลูกจ้างประจำที่ครบเกษียณอายุ
			elseif ($EMPPOS_COMCODE=="27") $MOV_CODE = "12110"; // ปลดออก
			elseif ($EMPPOS_COMCODE=="28") $MOV_CODE = "11820"; // ลาออกไปรับราชการทหาร
			elseif ($EMPPOS_COMCODE=="29") $MOV_CODE = "120"; // ให้ออก
			elseif ($EMPPOS_COMCODE=="3") $MOV_CODE = "105"; // โอนมา
			elseif ($EMPPOS_COMCODE=="30") $MOV_CODE = "21370"; // ไม่ได้เลื่อนขั้น
			elseif ($EMPPOS_COMCODE=="31") $MOV_CODE = "125"; // ภาคทัณฑ์
			elseif ($EMPPOS_COMCODE=="32") $MOV_CODE = "126"; // พักราชการ
			elseif ($EMPPOS_COMCODE=="33") $MOV_CODE = "247"; // ตัดค่าจ้าง
			elseif ($EMPPOS_COMCODE=="35") $MOV_CODE = "240"; // เลื่อนค่าจ้าง
			elseif ($EMPPOS_COMCODE=="4") $MOV_CODE = "10840"; // แต่งตั้งลูกจ้างประจำ
			elseif ($EMPPOS_COMCODE=="5") $MOV_CODE = "24030"; // 1 ขั้น
			elseif ($EMPPOS_COMCODE=="51") $MOV_CODE = "21430"; // 4%
			elseif ($EMPPOS_COMCODE=="6") $MOV_CODE = "103"; // โอนย้ายลูกจ้างประจำ
			elseif ($EMPPOS_COMCODE=="7") $MOV_CODE = "12730"; // ตัดโอนตำแหน่งลูกจ้างประจำ
			elseif ($EMPPOS_COMCODE=="71") $MOV_CODE = "12740"; // ตัดโอนและเปลี่ยนชื่อตำแหน่งลูกจ้างประจำ
			elseif ($EMPPOS_COMCODE=="8") $MOV_CODE = "12730"; // เปลี่ยนชื่อตำแหน่งลูกจ้างประจำ
			elseif ($EMPPOS_COMCODE=="81") $MOV_CODE = "12740"; // เปลี่ยนชื่อตำแหน่งและแต่งตั้งลูกจ้างประจำ
			elseif ($EMPPOS_COMCODE=="9") $MOV_CODE = "24010"; // 0.5 ขั้น
			elseif ($EMPPOS_COMCODE=="900") $MOV_CODE = "11410"; // ยกเลิกคำสั่ง
			elseif ($EMPPOS_COMCODE=="91") $MOV_CODE = "21420"; // 2%
			else { 
				$MOV_CODE = "99999"; 
				if ($FLAG_TO_NAME) echo "ประเภทการเคลื่อนไหว->$EMPPOS_COMCODE<br>"; 
			}

			$cmd = " select MOV_TYPE from PER_MOVMENT where MOV_CODE = '$MOV_CODE' ";
			$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			$data_dpis = $db_dpis->get_array();
			$MOV_TYPE = trim($data_dpis[MOV_TYPE]);

			$SM_CODE = "";
			if (strpos($REMARK,"0.5 ขั้น") !== false || $REMARK==".5 ขั้น" || $REMARK=="0.5   จ.1.5/  21   /2546 - 7/7/2546") {
				$MOV_TYPE = 2;
				$MOV_CODE = "21310";
				$SM_CODE = "1";
			} elseif (strpos($REMARK,"1.5 ขั้น") !== false || strpos($REMARK,"1 ขั้นครึ่ง") !== false || strpos($REMARK,"1ขั้นครึ่ง") !== false) {
				$MOV_TYPE = 2;
				$MOV_CODE = "21330";
				$SM_CODE = "3";
			} elseif (strpos($REMARK,"ลดขั้นเงินเดือน1") !== false || $REMARK=="ลดขั้น 1 ขั้น") {
				$MOV_TYPE = 2;
				$MOV_CODE = "21620";
				$SM_CODE = "7";
			} elseif (strpos($REMARK,"1 ขั้น") !== false || strpos($REMARK,"1ขั้น") !== false) {
				$MOV_TYPE = 2;
				$MOV_CODE = "21320";
				$SM_CODE = "2";
			} elseif (strpos($REMARK,"2 ขั้น") !== false || strpos($REMARK,"2ขั้น") !== false || strpos($REMARK,"2  ขั้น") !== false) {
				$MOV_TYPE = 2;
				$MOV_CODE = "21340";
				$SM_CODE = "4";
			} elseif (strpos($REMARK,"งดขั้น") !== false || strpos($REMARK,"เงินเดือนไม่ขึ้น") !== false || strpos($REMARK,"ไม่ขึ้นขั้น") !== false) {
				$MOV_TYPE = 2;
				$MOV_CODE = "21370";
				$SM_CODE = "10";
			} elseif (strpos($REMARK,"เต็มขั้น") !== false) {
				$MOV_TYPE = 2;
				$MOV_CODE = "21410";
			} elseif (strpos($REMARK,"ลดขั้นเงินเดือน") !== false) {
				$MOV_TYPE = 2;
				$MOV_CODE = "216";
			} elseif (strpos($REMARK,"เงินเพิ่มพิเศษ 2%") !== false || $REMARK=="เงินตอบแทนพิเศษ 2 %") { 
				$MOV_TYPE = 2;
				$MOV_CODE = "21420";
				$SM_CODE = "5"; 
				$EX_CODE = "015"; 
			} elseif (strpos($REMARK,"เงินเพิ่มพิเศษ 4%") !== false || $REMARK=="เงินตอบแทนพิเศษ 4 %") { 
				$MOV_TYPE = 2;
				$MOV_CODE = "21430";
				$SM_CODE = "17";  
				$EX_CODE = "016"; 
			} elseif (strpos($REMARK,"ตัดเงินเดือน 10 %") !== false || strpos($REMARK,"ตัดเงินเดือน 10%") !== false) {
				$MOV_TYPE = 2;
				$MOV_CODE = "21720";
				$SM_CODE = "8";
			} elseif (strpos($REMARK,"ถูกตัดเงิน 5% (ตั้งแต่เดือน มิ.ย.37-ส.ค") !== false) {
				$MOV_TYPE = 2;
				$MOV_CODE = "21710";
				$SM_CODE = "8";
			} elseif (strpos($REMARK,"ปรับอัตราเงินเดือน") !== false || $REMARK=="xx") { 
				$MOV_TYPE = 2;
				$MOV_CODE = "21520";
				$SM_CODE = "12";
			} elseif (strpos($REMARK,"เลื่อนเงินเดือน") !== false && $REMARK1=="พอใช้") { 
				$MOV_TYPE = 2;
				$MOV_CODE = "21315";
			} elseif (strpos($REMARK,"เลื่อนเงินเดือน") !== false && $REMARK1=="ดี") { 
				$MOV_TYPE = 2;
				$MOV_CODE = "21325";
			} elseif (strpos($REMARK,"เลื่อนเงินเดือน") !== false && $REMARK1=="ดีมาก") { 
				$MOV_TYPE = 2;
				$MOV_CODE = "21335";
			} elseif (strpos($REMARK,"เลื่อนเงินเดือน") !== false && $REMARK1=="ดีเด่น") { 
				$MOV_TYPE = 2;
				$MOV_CODE = "21345";
			} elseif (strpos($REMARK,"แก้ไขคำสั่งเงินเดือน") !== false || strpos($REMARK,"แก้ไขคำสั่งเลื่อนขั้น") !== false || strpos($REMARK,"แก้ไขเงินเดือน") !== false || strpos($REMARK,"แก้ไขอัตราเงินเดือน") !== false || strpos($REMARK,"แก้ไขเลื่อนขั้น") !== false) { 
				$MOV_TYPE = 2;
				$MOV_CODE = "11420";
			} elseif (strpos($REMARK,"เลื่อนขั้นเงินเดือน") !== false || $REMARK=="xx") { 
				$MOV_TYPE = 2;
				$MOV_CODE = "213";
			} elseif (strpos($REMARK,"พ้นจากการทดลอง") !== false || strpos($REMARK,"พ้นทดลอง") !== false || $REMARK=="xx") { 
				$MOV_TYPE = 1;
				$MOV_CODE = "10210";
			} elseif (strpos($REMARK,"ทดลองปฎิบัติราชการ") !== false || $REMARK=="xx") { 
				$MOV_TYPE = 1;
				$MOV_CODE = "102";
			} elseif (strpos($REMARK,"รับโอน") !== false || $REMARK=="xx") { 
				$MOV_TYPE = 1;
				$MOV_CODE = "10510";
			} elseif (strpos($REMARK,"โอนไป") !== false || $REMARK=="xx") { 
				$MOV_TYPE = 1;
				$MOV_CODE = "10610";
			} elseif (strpos($REMARK,"ไปรับราชการทหาร") !== false || $REMARK=="xx") { 
				$MOV_TYPE = 1;
				$MOV_CODE = "11820";
			} elseif (strpos($REMARK,"เกษียณก่อนกำหนด") !== false || $REMARK=="xx") { 
				$MOV_TYPE = 1;
				$MOV_CODE = "10830";
			} elseif (strpos($REMARK,"เลื่อนข้าราชการ") !== false || strpos($REMARK,"เลื่อนระดับ") !== false || strpos($REMARK,"ปรับระดับ") !== false || $REMARK=="xx") { 
				$MOV_TYPE = 1;
				$MOV_CODE = "104";
			} elseif ($REMARK=="(แต่งตั้งตาม พ.ร.บ.ระเบียบข้าราชการพลเรือน พ.ศ.2551)") { 
				$MOV_TYPE = 1;
				$MOV_CODE = "103"; // *********************
			} elseif (strpos($REMARK,"กลับเข้ารับราชการ") !== false || $REMARK=="xx") { 
				$MOV_TYPE = 1;
				$MOV_CODE = "10140";
			} elseif (strpos($REMARK,"แก้ไขคำสั่งเลื่อนระดับ") !== false || strpos($REMARK,"แก้ไขตำแหน่ง") !== false) { 
				$MOV_TYPE = 1;
				$MOV_CODE = "11420";
			} elseif (strpos($REMARK,"ตัดโอนตำแหน่ง") !== false || $REMARK=="xx") { 
				$MOV_TYPE = 1;
				$MOV_CODE = "10140"; // ยังไม่มีรหัส
			} elseif (strpos($REMARK,"ปฏิบัติราชการ") !== false || $REMARK=="xx") { 
				$MOV_TYPE = 1;
				$MOV_CODE = "11310"; 
			} elseif (strpos($REMARK,"รักษาราชการ") !== false || $REMARK=="xx") { 
				$MOV_TYPE = 1;
				$MOV_CODE = "11010"; 
			} elseif (strpos($REMARK,"ลาศึกษา") !== false || $REMARK=="xx") { 
				$MOV_TYPE = 1;
				$MOV_CODE = "11210"; 
			}

			$SALARYHIS = $POSITIONHIS = "";		
			$EX_CODE = "024";
			if ($MOV_TYPE==1 || $MOV_TYPE==3)	$POSITIONHIS = 1;		
			if ($MOV_TYPE==2 || $MOV_TYPE==3)	$SALARYHIS = 1;

			$LEVEL_NO = str_pad(trim($data[EMPPOS_C]), 2, "0", STR_PAD_LEFT);
			if ($LEVEL_NO=="21") $LEVEL_NO = "O1";
			if ($LEVEL_NO=="22") $LEVEL_NO = "O2";
			if ($LEVEL_NO=="23") $LEVEL_NO = "O3";
			if ($LEVEL_NO=="24") $LEVEL_NO = "O4";
			if ($LEVEL_NO=="31") $LEVEL_NO = "K1";
			if ($LEVEL_NO=="32") $LEVEL_NO = "K2";
			if ($LEVEL_NO=="33") $LEVEL_NO = "K3";
			if ($LEVEL_NO=="34") $LEVEL_NO = "K4";
			if ($LEVEL_NO=="35") $LEVEL_NO = "K5";
			if ($LEVEL_NO=="41") $LEVEL_NO = "D1";
			if ($LEVEL_NO=="42") $LEVEL_NO = "D2";
			if ($LEVEL_NO=="51") $LEVEL_NO = "M1";
			if ($LEVEL_NO=="52") $LEVEL_NO = "M2";
			if ($LEVEL_NO!="01" && $LEVEL_NO!="02" && $LEVEL_NO!="03" && $LEVEL_NO!="04" && $LEVEL_NO!="05" && $LEVEL_NO!="06" && 
				$LEVEL_NO!="07" && $LEVEL_NO!="08" && $LEVEL_NO!="09" && $LEVEL_NO!="10" && $LEVEL_NO!="11" && $LEVEL_NO!="O1" && 
				$LEVEL_NO!="O2" && $LEVEL_NO!="O3" && $LEVEL_NO!="K1" && $LEVEL_NO!="K2" && $LEVEL_NO!="K3" && $LEVEL_NO!="K4" && 
				$LEVEL_NO!="K5" && $LEVEL_NO!="D1" && $LEVEL_NO!="D2" && $LEVEL_NO!="M1" && $LEVEL_NO!="M2" && $LEVEL_NO!="90" && 
				$LEVEL_NO!="91" && $LEVEL_NO!="92" && $LEVEL_NO!="93" && $LEVEL_NO!="94") {
				$LEVEL_NO = "NULL";
				if ($LEVEL_NO!="96" && $LEVEL_NO!="97" && $LEVEL_NO!="98" && $LEVEL_NO!="99" && $LEVEL_NO!="NULL") echo "$LEVEL_NO<br>";
			} else $LEVEL_NO = trim($LEVEL_NO)? "'".$LEVEL_NO."'" : "NULL";
			$MP_FLAG_CURRENT = trim($data[MP_FLAG_CURRENT]);
			if ($MP_FLAG_CURRENT=="1") $LAST_TRANSACTION = "Y"; 
			else $LAST_TRANSACTION = "N";

			$POH_ISREAL = "Y";
			$ORDERID = $data[ORDERID];
			$ES_CODE = "02";
			$POH_SEQ_NO = $data[EMPPOS_ORDER];
			$POH_CMD_SEQ = $data[ORDERTH]; 

			$STATUS = trim($data[STATUS]);

			if (!$ORG_NAME) $ORG_NAME = "-";
			if (!$POS_NO) $POS_NO = "-";
			if (!$SALARY) $SALARY = 0;
			if (!$SEQ_NO) $SEQ_NO = 1;
			if (!$CMD_SEQ || $CMD_SEQ > 20000) $CMD_SEQ = "NULL";
			if (!$SAH_PERCENT_UP) $SAH_PERCENT_UP = "NULL";
			if (!$SAH_SALARY_UP) $SAH_SALARY_UP = "NULL";
			if (!$SAH_SALARY_EXTRA) $SAH_SALARY_EXTRA = "NULL";
			if (!$DOCDATE = "0") $DOCDATE = "NULL";

			$ORG_ID_1 = "NULL";
			$ORG_ID_2 = "NULL";
			$ORG_ID_3 = "NULL";

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
								'$PER_CARDNO', '$POH_ORG1', '$POH_ORG2', '$POH_ORG3', '$POH_ORG', '$MPOS_NAME', '$WPOS_NAME', $SEQ_NO, 
								'$LAST_TRANSACTION', $CMD_SEQ, '$POH_ISREAL', '$POH_ORG_DOPA_CODE', '$ES_CODE', $LEVEL_NO, '$REMARK1', 
								'$REMARK2', '$DOCNO_EDIT', '$DOCDATE_EDIT') ";
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
								SAH_POSITION, SAH_ORG, EX_CODE, SAH_PAY_NO, SAH_KF_YEAR, SAH_LAST_SALARY, SM_CODE, SAH_CMD_SEQ, SAH_REMARK1, SAH_REMARK2)
								VALUES ($MAX_SAH_ID, $PER_ID, '$EFFECTIVEDATE', '$MOV_CODE', $SALARY, '$DOCNO', 
								'$DOCDATE', NULL, $UPDATE_USER, '$UPDATE_DATE', '$PER_CARDNO', $SAH_PERCENT_UP, $SAH_SALARY_UP, 
								$SAH_SALARY_EXTRA, $SEQ_NO, '$REMARK', $LEVEL_NO, '$POS_NO_NAME', '$POS_NO', 
								'$WPOS_NAME', '$POH_ORG', '$EX_CODE', '$SAH_PAY_NO', '$EMPPOS_YEAR', '$LAST_TRANSACTION', '$SM_CODE', $CMD_SEQ, '$REMARK1', '$REMARK2') ";
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
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_POSITIONHIS where PER_ID in (select PER_ID from PER_PERSONAL where PER_TYPE = 2) ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] - $CNT_POH_ID;
		echo "PER_POSITIONHIS - $PER_POSITIONHIS - $COUNT_NEW<br>";

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_SALARYHIS where PER_ID in (select PER_ID from PER_PERSONAL where PER_TYPE = 2) ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] - $CNT_SAH_ID;
		echo "PER_SALARYHIS - $PER_SALARYHIS - $COUNT_NEW<br>";

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();
		$db_dpis351->free_result();
	} // end if

	if ($command=='EXTRAHIS'){
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
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "<br>";
		$MAX_ID = 1;
		while($data = $db_dpis35->get_array()){
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
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "<br>";
		while($data = $db_dpis35->get_array()){
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
		$db_dpis35->free_result();
		$db_dpis351->free_result();
	} // end if

	if ($command=='KPIFORM'){
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
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "<br>";
		$MAX_ID = 1;
		while($data = $db_dpis35->get_array()){
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
		$db_dpis35->free_result();
		$db_dpis351->free_result();
	} // end if

	if ($command=='PUNISHMENT'){
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
		$db_dpis35->send_cmd($cmd);
		$db_dpis35->show_error();
		echo "<br>";
		$MAX_ID = 1;
		while($data = $db_dpis35->get_array()){
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
		$db_dpis35->free_result();
		$db_dpis351->free_result();
	} // end if

	if ($command=='TIME' || $command=='TIME_EMP'){ 
// เวลาทวีคูณ 1794 ok 
		$cmd = " truncate table per_timehis ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if ($command=='TIME')
			$cmd = " SELECT a.ID, DDT_ORDER, DDT_DET, DDT_ST_DATE, DDT_END_DATE, DDT_REFF 
							  FROM DOUBLE_DUTY a, COMMON_HISTORY b
							  WHERE a.ID = b.ID
							  ORDER BY a.ID ";
		elseif ($command=='TIME_EMP')
			$cmd = " SELECT a.ID, DDT_ORDER, DDT_DET, DDT_ST_DATE, DDT_END_DATE, DDT_REFF 
							  FROM EMPDOUBLE_DUTY a, EMPLOYEE_HISTORY b
							  WHERE a.ID = b.ID
							  ORDER BY a.ID ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "<br>";
		$MAX_ID = 1;
		while($data = $db_dpis35->get_array()){
			$PER_TIMEHIS++;
			$PER_ID = trim($data[ID]);
			$TIMEH_REMARK = trim($data[DDT_DET]);
			$DDT_ST_DATE = trim($data[DDT_ST_DATE]);
			$TIME_START = ($DDT_ST_DATE)? ((substr($DDT_ST_DATE, 4, 4) - 543) ."-". substr($DDT_ST_DATE, 2, 2) ."-". substr($DDT_ST_DATE, 0, 2)) : "";
			$DDT_END_DATE = trim($data[DDT_END_DATE]);
			$TIME_END = ($DDT_END_DATE)? ((substr($DDT_END_DATE, 4, 4) - 543) ."-". substr($DDT_END_DATE, 2, 2) ."-". substr($DDT_END_DATE, 0, 2)) : "";
			$TIMEH_BOOK_NO = $data[DDT_REFF];

			if ($TIME_START && $TIME_END) {
				$cmd = " select TIME_CODE, TIME_DAY from PER_TIME where TIME_START = '$TIME_START' and TIME_END = '$TIME_END' ";
				$db_dpis->send_cmd($cmd);
				$db_dpis->show_error();
				$data_dpis = $db_dpis->get_array();
				$TIME_CODE = trim($data_dpis[TIME_CODE]);
				$TIME_DAY = $data_dpis[TIME_DAY];
//				if (!$TIME_DAY) $TIME_DAY = $date_difference($TIME_START, $TIME_END, "d");

			}
			if (strpos($TIMEH_REMARK,"2 เดือน 8 วัน") !== false || strpos($TIMEH_REMARK,"2 เดือน  8 วัน") !== false || 
				strpos($TIMEH_REMARK,"2  เดือน  8  วัน") !== false || strpos($TIMEH_REMARK,"2  เดือน  8 วัน") !== false || 
				strpos($TIMEH_REMARK,"2  เดือน   8   วัน") !== false || strpos($TIMEH_REMARK,"2   เดือน  8  วัน") !== false || 
				strpos($TIMEH_REMARK,"2 เดือน  8  วัน") !== false || strpos($TIMEH_REMARK,"2 เดือน 8วัน" !== false) || 
				strpos($TIMEH_REMARK,"2  เดือน  8   วัน") !== false || strpos($TIMEH_REMARK,"2เดือน 8 วัน") !== false || 
				strpos($TIMEH_REMARK,"2  เดือน 8 วัน") !== false || strpos($TIMEH_REMARK,"2  เดือน   8  วัน") !== false || 
				strpos($TIMEH_REMARK,"2534") !== false) $TIME_CODE = "06";
			elseif (strpos($TIMEH_REMARK,"2519") !== false) $TIME_CODE = "05";
			elseif (strpos($TIMEH_REMARK,"2494") !== false) $TIME_CODE = "01";
			else $TIME_CODE = "99";
			if (!$TIMEH_MINUS) $TIMEH_MINUS = 0;

			$cmd = " INSERT INTO PER_TIMEHIS(TIMEH_ID, PER_ID, TIME_CODE, TIMEH_MINUS, TIMEH_REMARK, TIMEH_BOOK_NO, UPDATE_USER, 
							 UPDATE_DATE)
							 VALUES ($MAX_ID, $PER_ID, '$TIME_CODE', $TIMEH_MINUS, '$TIMEH_REMARK', '$TIMEH_BOOK_NO', $UPDATE_USER, '$UPDATE_DATE') ";
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
		$db_dpis35->free_result();
		$db_dpis351->free_result();
	} // end if

	if ($command=='SERVICE' || $command=='SERVICE_EMP'){ 
// เวลาทวีคูณ 1794 ok 
		$cmd = " truncate table per_servicehis ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if ($command=='SERVICE')
			$cmd = " SELECT a.ID, SDT_ORDER, SDT_DET, SDT_ST_DATE, SDT_END_DATE, SDT_REFF, SDUTY_CODE 
							  FROM SPECIAL_DUTY a, COMMON_HISTORY b
							  WHERE a.ID = b.ID
							  ORDER BY a.ID ";
		elseif ($command=='SERVICE_EMP')
			$cmd = " SELECT a.ID, SDT_ORDER, SDT_DET, SDT_ST_DATE, SDT_END_DATE, SDT_REFF, SDUTY_CODE 
							  FROM EMPSPECIAL_DUTY a, EMPLOYEE_HISTORY b
							  WHERE a.ID = b.ID
							  ORDER BY a.ID ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "<br>";
		$MAX_ID = 1;
		while($data = $db_dpis35->get_array()){
			$PER_SERVICEHIS++;
			$PER_ID = trim($data[ID]);
			$SDT_ST_DATE = trim($data[SDT_ST_DATE]);
			$SRH_STARTDATE = ($SDT_ST_DATE)? ((substr($SDT_ST_DATE, 4, 4) - 543) ."-". substr($SDT_ST_DATE, 2, 2) ."-". substr($SDT_ST_DATE, 0, 2)) : "";
			$SDT_END_DATE = trim($data[SDT_END_DATE]);
			$SRH_ENDDATE = ($SDT_END_DATE)? ((substr($SDT_END_DATE, 4, 4) - 543) ."-". substr($SDT_END_DATE, 2, 2) ."-". substr($SDT_END_DATE, 0, 2)) : "";
			$SRH_DOCNO = $data[DDT_REFF];
			$SRH_SRT_NAME = $data[SDT_DET];
			if (strpos($SRH_SRT_NAME,"วิทยากร") !== false) $SV_CODE = "01";
			elseif (strpos($SRH_SRT_NAME,"อนุกรรมการและผู้ช่วยเลขานุการ") !== false) $SV_CODE = "21";
			elseif (strpos($SRH_SRT_NAME,"กรรมการและผู้ช่วยเลขานุการ") !== false) $SV_CODE = "07";
			elseif (strpos($SRH_SRT_NAME,"เลขานุการคณะทำงาน") !== false) $SV_CODE = "06";
			elseif (strpos($SRH_SRT_NAME,"เลขานุการคณะกรรมการ") !== false) $SV_CODE = "05";
			elseif (strpos($SRH_SRT_NAME,"รองประธานอนุกรรมการ") !== false) $SV_CODE = "20";
			elseif (strpos($SRH_SRT_NAME,"ประธานอนุกรรมการ") !== false) $SV_CODE = "19";
			elseif (strpos($SRH_SRT_NAME,"อนุกรรมการ") !== false || strpos($SRH_SRT_NAME,"คณะอนุกรรม") !== false) $SV_CODE = "04";
			elseif (strpos($SRH_SRT_NAME,"คณะทำงาน") !== false) $SV_CODE = "02";
			elseif (strpos($SRH_SRT_NAME,"ผู้แทนสำรอง") !== false) $SV_CODE = "16";
			elseif (strpos($SRH_SRT_NAME,"ผู้แทน") !== false) $SV_CODE = "09";
			elseif (strpos($SRH_SRT_NAME,"ช่วยราชการ") !== false || strpos($SRH_SRT_NAME,"ช่วยปฏิบัติราชการ") !== false) $SV_CODE = "10";
			elseif (strpos($SRH_SRT_NAME,"รักษาราชการแทน") !== false || strpos($SRH_SRT_NAME,"รักษาการแทน") !== false) $SV_CODE = "11";
			elseif (strpos($SRH_SRT_NAME,"รักษาราชการในตำแหน่ง") !== false || strpos($SRH_SRT_NAME,"รักษาการในตำแหน่ง") !== false) $SV_CODE = "12";
			elseif (strpos($SRH_SRT_NAME,"รองประธานกรรมการ") !== false) $SV_CODE = "30";
			elseif (strpos($SRH_SRT_NAME,"ประธานกรรมการ") !== false || strpos($SRH_SRT_NAME,"ประธานกรมการ") !== false || strpos($SRH_SRT_NAME,"ประธานกรรม") !== false) $SV_CODE = "13";
			elseif (strpos($SRH_SRT_NAME,"รองประธาน") !== false) $SV_CODE = "14";
			elseif (strpos($SRH_SRT_NAME,"อนุกรรมการและเลขานุการ") !== false) $SV_CODE = "26";
			elseif (strpos($SRH_SRT_NAME,"กรรมการและเลขานุการ") !== false) $SV_CODE = "15";
			elseif (strpos($SRH_SRT_NAME,"ที่ปรึกษา") !== false) $SV_CODE = "17";
			elseif (strpos($SRH_SRT_NAME,"เข้าร่วมประชุม") !== false) $SV_CODE = "18";
			elseif (strpos($SRH_SRT_NAME,"ผู้เชี่ยวชาญ") !== false) $SV_CODE = "22";
			elseif (strpos($SRH_SRT_NAME,"อนุกรรมาธิการ") !== false) $SV_CODE = "23";
			elseif (strpos($SRH_SRT_NAME,"ผู้ทำงานและเลขานุการร่วม") !== false) $SV_CODE = "24";
			elseif (strpos($SRH_SRT_NAME,"ผู้ทรงคุณวุฒิ") !== false) $SV_CODE = "27";
			elseif (strpos($SRH_SRT_NAME,"นายทะเบียนระดับกรม") !== false) $SV_CODE = "28";
			elseif (strpos($SRH_SRT_NAME,"ผู้ช่วยเลขานุการสำรอง") !== false) $SV_CODE = "29";
			elseif (strpos($SRH_SRT_NAME,"ผู้ช่วยเลชานุการ") !== false) $SV_CODE = "08";
			elseif (strpos($SRH_SRT_NAME,"เจ้าหน้าที่") !== false) $SV_CODE = "31";
			elseif (strpos($SRH_SRT_NAME,"หัวหน้า") !== false) $SV_CODE = "32";
			elseif (strpos($SRH_SRT_NAME,"ผู้ช่วย") !== false) $SV_CODE = "33";
			elseif (strpos($SRH_SRT_NAME,"ผู้ประสานงาน") !== false || strpos($SRH_SRT_NAME,"ประสานงาน") !== false) $SV_CODE = "34";
			elseif (strpos($SRH_SRT_NAME,"เข้าร่วมฟัง") !== false) $SV_CODE = "35";
			elseif (strpos($SRH_SRT_NAME,"เดินทางไป") !== false) $SV_CODE = "36";
			elseif (strpos($SRH_SRT_NAME,"แต่งตั้ง") !== false) $SV_CODE = "37";
			elseif (strpos($SRH_SRT_NAME,"ผู้บรรยาย") !== false || strpos($SRH_SRT_NAME,"ไปบรรยาย") !== false || strpos($SRH_SRT_NAME,"มาร่วมบรรยาย") !== false) $SV_CODE = "38";
			elseif (strpos($SRH_SRT_NAME,"รองผู้อำนวยการโครงการ") !== false) $SV_CODE = "39";
			elseif (strpos($SRH_SRT_NAME,"ได้รับคัดเลือก") !== false) $SV_CODE = "40";
			elseif (strpos($SRH_SRT_NAME,"ปฏิบัติหน้าที่") !== false || strpos($SRH_SRT_NAME,"ปฏิบัติงาน") !== false || strpos($SRH_SRT_NAME,"ปฎิบัติงาน") !== false || strpos($SRH_SRT_NAME,"ทำหน้าที่") !== false) $SV_CODE = "41";
			elseif (strpos($SRH_SRT_NAME,"นายช่างโครงการ") !== false || strpos($SRH_SRT_NAME,"นายช่าโครงการ") !== false) $SV_CODE = "42";
			elseif (strpos($SRH_SRT_NAME,"ผู้จัดการโครงการ") !== false) $SV_CODE = "43";
			elseif (strpos($SRH_SRT_NAME,"สัมมนา") !== false) $SV_CODE = "44";
			elseif (strpos($SRH_SRT_NAME,"ผู้อำนวยการโครงการ") !== false) $SV_CODE = "45";
			elseif (strpos($SRH_SRT_NAME,"วิศวกรกำกับโครงการ") !== false || strpos($SRH_SRT_NAME,"วิศวกรโครงการ") !== false || strpos($SRH_SRT_NAME,"วิศวกรทำกับโครงการ") !== false) $SV_CODE = "46";
			elseif (strpos($SRH_SRT_NAME,"นายทะเบียนเอกสารลับ") !== false) $SV_CODE = "47";
			elseif (strpos($SRH_SRT_NAME,"ประชุม") !== false) $SV_CODE = "48";
			elseif (strpos($SRH_SRT_NAME,"ปฏิบัติราชการ") !== false) $SV_CODE = "49";
			elseif (strpos($SRH_SRT_NAME,"ได้รับการคัดเลือก") !== false) $SV_CODE = "50";
			elseif (strpos($SRH_SRT_NAME,"เลขานุการ") !== false) $SV_CODE = "25";
			elseif (strpos($SRH_SRT_NAME,"กรรมการ") !== false || strpos($SRH_SRT_NAME,"กรรการ") !== false) $SV_CODE = "03";
			elseif (strpos($SRH_SRT_NAME,"อาจารย์") !== false || strpos($SRH_SRT_NAME,"อาาจารย์") !== false || strpos($SRH_SRT_NAME,"สอนพิเศษ") !== false || 
				strpos($SRH_SRT_NAME,"ทำการสอน") !== false || strpos($SRH_SRT_NAME,"ช่วยสอน") !== false) $SV_CODE = "12";
			else $SV_CODE = "99"; 

			$cmd = " insert into PER_SERVICEHIS (SRH_ID, PER_ID, SV_CODE, SRT_CODE, SRH_STARTDATE, SRH_ENDDATE, SRH_NOTE, 
							SRH_DOCNO, PER_CARDNO, UPDATE_USER, UPDATE_DATE, SRH_ORG, SRH_SRT_NAME)
							values ($MAX_ID, $PER_ID, '$SV_CODE', '$SRT_CODE', '$SRH_STARTDATE', '$SRH_ENDDATE', '$SRH_NOTE', 
							'$SRH_DOCNO', '$PER_CARDNO', $SESS_USERID, '$UPDATE_DATE', '$SRH_ORG', '$SRH_SRT_NAME') ";
			$db_dpis->send_cmd($cmd);

			$cmd1 = " SELECT SRH_ID FROM PER_SERVICEHIS WHERE SRH_ID = $MAX_ID "; 
			$count_data = $db_dpis1->send_cmd($cmd1);
			if (!$count_data) {
				echo "$cmd<br>==================<br>";
				$db_dpis->show_error();
				echo "<br>end ". ++$i  ."=======================<br>";
			}
			$MAX_ID++;
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_SERVICEHIS ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_SERVICEHIS - $PER_SERVICEHIS - $COUNT_NEW<br>";
		
		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();
		$db_dpis351->free_result();
	} // end if

	if ($command=='MARRIED'){
// สมรส 1029 ok ********************************************************************
		$cmd = " truncate table per_marrhis ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " SELECT PERSONID, MARRIEDNO, MARRIEDAT, MARRIEDDATE, WEDDEDPAIR, MARRYSTATUS, REMARK, MODDATE, MODBY, 
						BOOK_NO, REGIS_DATE, PRO_CODE
						FROM MARRIED
						ORDER BY MARRIEDID ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "<br>";
		$MAX_ID = 1;
		while($data = $db_dpis35->get_array()){
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
		$db_dpis35->free_result();
		$db_dpis351->free_result();
	} // end if

	if ($command=='USER'){
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
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "<br>";
		while($data = $db_dpis35->get_array()){
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
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "<br>";
		while($data = $db_dpis35->get_array()){
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
			$db_dpis351->send_cmd($cmd);
			$data1 = $db_dpis351->get_array();
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
		$db_dpis35->free_result();
		$db_dpis351->free_result();
	} // end if

	if ($command=='MGTSALARY'){
// เงินประจำตำแหน่ง 2346 ok ****************************************************************************************
		$cmd = " truncate table PER_POS_MGTSALARY ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " SELECT GOV_POSNO, REM, MONEY, MONEY2
						  FROM GOVPOS_POSMONEYREM
						  ORDER BY GOV_POSNO ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "<br>";
		while($data = $db_dpis35->get_array()){
			$PER_POS_MGTSALARY++;
			$MP_ID = $data[MP_ID];
			$GOV_POSNO = trim($data[GOV_POSNO]);
			$EX_CODE = str_pad(trim($data[SPECIAL_TYPEID]), 3, "0", STR_PAD_LEFT);
			$POS_STARTDATE = trim($data[STARTDATE]);
			$STATUS = strtoupper(trim($data[STATUS]));
			if ($STATUS=="Y") $POS_STATUS = 1;
			elseif ($STATUS=="N") $POS_STATUS = 2; 

			$cmd = " select POS_ID from PER_POSITION where POS_NO = '$GOV_POSNO' ";
			$db_dpis->send_cmd($cmd);
			$db_dpis->show_error();
			$data_dpis = $db_dpis->get_array();
			$POS_ID = $data_dpis[POS_ID];
			if (!$POS_ID) echo "เลขที่ตำแหน่ง $GOV_POSNO<br>";

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
/*
		$cmd = " truncate table PER_POS_MGTSALARYHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " SELECT a.PERSONID, a.STARTDATE, a.ENDDATE, a.STATUS, b.SPECIAL_TYPEID
						  FROM PERSONMONEY a, MONEY_POSITION b
						  WHERE a.MONEY_POSITIONID=b.MP_ID
						  ORDER BY PERSONMONEYID ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "<br>";
		$MAX_ID = 1;
		while($data = $db_dpis35->get_array()){
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
		$db_dpis35->free_result();
		$db_dpis351->free_result(); */
	} // end if

	if ($command=='EXTRATYPE'){
// เงินเพิ่มพิเศษ 142 ok *********************************************************************************************
/*		$cmd = " truncate table per_extrahis ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " delete from per_extratype where update_user = $UPDATE_USER ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " SELECT SPECIAL_TYPEID, NAME, REMARK, NET_VALUE, ABBNAME FROM SPECIAL_PAYTYPE ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "<br>";
		while($data = $db_dpis35->get_array()){
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
		$db_dpis35->free_result();
		$db_dpis351->free_result(); */
	} // end if
		
	if ($command=='TRAIN' || $command=='TRAIN_EMP'){
// ฝึกอบรม 63410 ok 
		$cmd = " truncate table per_training ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

/*		$cmd = " SELECT DISTINCT TS_TYPE, TS_CODE, TS_DET
						  FROM TSCV 
						  ORDER BY TS_DET ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "<br>";
		while($data = $db_dpis35->get_array()){
			$TS_TYPE =  trim($data[TS_TYPE]);
			if ($TS_TYPE==0) $TR_TYPE = 1;
			elseif ($TS_TYPE==1) $TR_TYPE = 3;
			elseif ($TS_TYPE==2) $TR_TYPE = 2;
			elseif ($TS_TYPE==3) $TR_TYPE = 4;
			else $TR_TYPE = 1;
			$TS_CODE = trim($data[TS_CODE]);
			$TS_DET = trim($data[TS_DET]);
			if (!$TS_DET && $TS_CODE) $TS_DET = $TS_CODE
			$TR_NAME = $TS_DET;
			$TR_NAME = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($TR_NAME)));

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
*/		
		$cmd = " select max(TRN_ID) as MAX_ID from PER_TRAINING ";
		$db_dpis1->send_cmd($cmd);
		$data1 = $db_dpis1->get_array();
		$MAX_ID = $data1[MAX_ID] + 1;

		if ($command=='TRAIN')
			$cmd = " SELECT a.ID, TS_ORDER, TS_TYPE, TS_REFF, C_CODE, INSTITUTION, QUALIFICATION, TS_ST_DATE, 
												TS_END_DATE, TS_CODE, EDU_F_CODE, TS_DET
							  FROM TSCV a, COMMON_HISTORY b
							  WHERE a.ID=b.ID
							  ORDER BY a.ID, TS_ORDER ";
		elseif ($command=='TRAIN_EMP')
			$cmd = " SELECT a.ID, EMPTS_ORDER as TS_ORDER, EMPTS_TYPE as TS_TYPE, EMPTS_REFF as TS_REFF, C_CODE, 
												EMP_INSTITUTION as INSTITUTION, EMP_QUALIFICATION as QUALIFICATION, EMP_TS_ST_DATE as TS_ST_DATE, 
												EMP_TS_END_DATE as TS_END_DATE, EMP_TS_CODE as TS_CODE, EDU_F_CODE, EMP_TS_DET as TS_DET
							  FROM EMPTSCV a, EMPLOYEE_HISTORY b
							  WHERE a.ID=b.ID
							  ORDER BY a.ID, EMPTS_ORDER ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "<br>";
		while($data = $db_dpis35->get_array()){
			$PER_ID = trim($data[ID]);
			$TS_TYPE =  trim($data[TS_TYPE]);
			if ($TS_TYPE==0) $TR_TYPE = 1;
			elseif ($TS_TYPE==1) $TR_TYPE = 3;
			elseif ($TS_TYPE==2) $TR_TYPE = 2;
			elseif ($TS_TYPE==3) $TR_TYPE = 4;
			else $TR_TYPE = 1;
			$TRN_BOOK_NO = trim($data[TS_REFF]);
			$TRN_PLACE = trim($data[INSTITUTION]);
			$TRN_DEGREE_RECEIVE = trim($data[QUALIFICATION]);
			$TS_ST_DATE = trim($data[TS_ST_DATE]);
			$TRN_STARTDATE = (substr($TS_ST_DATE,4,4) - 543) . "-" . substr($TS_ST_DATE,2,2) . "-" . substr($TS_ST_DATE,0,2);
			$TS_END_DATE = trim($data[TS_END_DATE]);
			$TRN_ENDDATE = (substr($TS_END_DATE,4,4) - 543) . "-" . substr($TS_END_DATE,2,2) . "-" . substr($TS_END_DATE,0,2);
			$TS_CODE = trim($data[TS_CODE]);
			$TS_DET = trim($data[TS_DET]);
			if (!$TS_DET && $TS_CODE) $TS_DET = $TS_CODE;
			$TRN_DAY = "NULL";
			$TRN_PASS = 1;

			$TRN_PLACE = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($TRN_PLACE)));
			$TRN_ORG = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($TRN_ORG)));
			$TRN_REMARK = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($TRN_REMARK)));

			$TRN_COURSE_NAME = $TS_DET;
			$TRN_COURSE_NAME = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($TRN_COURSE_NAME)));

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
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_TRAINING ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_TRAINING - $PER_TRAINING - $COUNT_NEW<br>";

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();
		$db_dpis351->free_result();
	} // end if

	if ($command=='UPDATESAL'){
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

	if ($command=='UPDATEPOS'){
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
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$RECORD_ID = $data[RECORD_ID];
			$ORG_NAME = trim($data[ORG_NAME]);
			$ORG_NAME = str_replace("\n", "  ", $ORG_NAME);
			$ORG_NAME = str_replace("\r", "  ", $ORG_NAME);
			$ORG_NAME = str_replace("\n\r", "  ", $ORG_NAME);
			$ORG_NAME = str_replace("\r\n", "  ", $ORG_NAME);
			$cmd = " UPDATE HR_EMPLOY_RECORD SET  ORG_NAME = '$ORG_NAME' WHERE RECORD_ID = $RECORD_ID ";
			$db_dpis351->send_cmd($cmd);
//			$db_dpis351->show_error();
		} // end while			
*/
	} // end if

	if ($command=='UPDATE_HISTORY'){ 
		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '  ', ' ') WHERE EMPPOS_REFF like '%  %' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '  ', ' ') WHERE EMPPOS_REFF like '%  %' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '  ', ' ') WHERE EMPPOS_REFF like '%  %' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '  ', ' ') WHERE EMPPOS_REFF like '%  %' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '  ', ' ') WHERE EMPPOS_REFF like '%  %' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'มี. ค.', 'มี.ค.') WHERE EMPPOS_REFF like '%มี. ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'ต. ค.', 'ต.ค.') WHERE EMPPOS_REFF like '%ต. ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'ธ ค.', 'ธ.ค.') WHERE EMPPOS_REFF like '%ธ ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'มต.ค.', 'ต.ค.') WHERE EMPPOS_REFF like '%มต.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'มี ค.', 'มี.ค.') WHERE EMPPOS_REFF like '%มี ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '3มี.ค.', '3 มี.ค.') WHERE EMPPOS_REFF like '%3มี.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '0ก.ย.', '0 ก.ย.') WHERE EMPPOS_REFF like '%0ก.ย.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '3ต.ค.', '3 ต.ค.') WHERE EMPPOS_REFF like '%3ต.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '2ม.ค.', '2 ม.ค.') WHERE EMPPOS_REFF like '%2ม.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '1มี.ค.', '1 มี.ค.') WHERE EMPPOS_REFF like '%1มี.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '7ก.พ.', '7 ก.พ.') WHERE EMPPOS_REFF like '%7ก.พ.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '1พ.ย.', '1 พ.ย.') WHERE EMPPOS_REFF like '%1พ.ย.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '7เม.ย.', '7 เม.ย.') WHERE EMPPOS_REFF like '%7เม.ย.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '6มี.ค.', '6 มี.ค.') WHERE EMPPOS_REFF like '%6มี.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '9มี.ค.', '9 มี.ค.') WHERE EMPPOS_REFF like '%9มี.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '8ต.ค.', '8 ต.ค.') WHERE EMPPOS_REFF like '%8ต.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '4ม.ค.', '4 ม.ค.') WHERE EMPPOS_REFF like '%4ม.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'มี .ค.3', 'มี.ค. 3') WHERE EMPPOS_REFF like '%มี .ค.3%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'ก.ค ', 'ก.ค. ') WHERE EMPPOS_REFF like '%ก.ค %' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'มี.ค3', 'มี.ค. 3') WHERE EMPPOS_REFF like '%มี.ค3%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'ก.ย3', 'ก.ย. 3') WHERE EMPPOS_REFF like '%ก.ย3%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'ก. ย.', 'ก.ย.') WHERE EMPPOS_REFF like '%ก. ย.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'ก. พ.', 'ก.พ.') WHERE EMPPOS_REFF like '%ก. พ.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'ส. ค.', 'ส.ค.') WHERE EMPPOS_REFF like '%ส. ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'ก. ย.', 'ก.ย.') WHERE EMPPOS_REFF like '%ก. ย.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'พ. ย.', 'พ.ย.') WHERE EMPPOS_REFF like '%พ. ย.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'ก. ค.', 'ก.ค.') WHERE EMPPOS_REFF like '%ก. ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'เม. ย.', 'เม.ย.') WHERE EMPPOS_REFF like '%เม. ย.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '2ธ.ค.', '2 ธ.ค.') WHERE EMPPOS_REFF like '%2ธ.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'กค กค.', 'ก.ค.') WHERE EMPPOS_REFF like '%กค กค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'มค ', 'ม.ค. ') WHERE EMPPOS_REFF like '%มค %' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'มีค ', 'มี.ค. ') WHERE EMPPOS_REFF like '%มีค %' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '2ต.ค.', '2 ต.ค.') WHERE EMPPOS_REFF like '%2ต.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '7มี.ค.', '7 มี.ค.') WHERE EMPPOS_REFF like '%7มี.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '6ต.ค.', '6 ต.ค.') WHERE EMPPOS_REFF like '%6ต.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '1ต.ค.2', '1 ต.ค. 2') WHERE EMPPOS_REFF like '%1ต.ค.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'กห.ย.', 'ก.ย.') WHERE EMPPOS_REFF like '%กห.ย.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '7ก.ย.2', '7 ก.ย. 2') WHERE EMPPOS_REFF like '%7ก.ย.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '8ก.ค.', '8 ก.ค.') WHERE EMPPOS_REFF like '%8ก.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'มี .ค.', 'มี.ค.') WHERE EMPPOS_REFF like '%มี .ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '3ม.ค.2', '3 ม.ค. 2') WHERE EMPPOS_REFF like '%3ม.ค.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '1ต.ค.', '1 ต.ค.') WHERE EMPPOS_REFF like '%1ต.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '8ส.ค.', '8 ส.ค.') WHERE EMPPOS_REFF like '%8ส.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '0ก.ค.', '0 ก.ค.') WHERE EMPPOS_REFF like '%0ก.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '7ธ.ค.', '7 ธ.ค.') WHERE EMPPOS_REFF like '%7ธ.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '9ต.ค.', '9 ต.ค.') WHERE EMPPOS_REFF like '%9ต.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'มงค.', 'ม.ค.') WHERE EMPPOS_REFF like '%มงค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '6ก.ย.', '6 ก.ย.') WHERE EMPPOS_REFF like '%6ก.ย.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '6ต.ค.2', '6 ต.ค. 2') WHERE EMPPOS_REFF like '%6ต.ค.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '2ก.พ.', '2 ก.พ.') WHERE EMPPOS_REFF like '%2ก.พ.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '3ก.ย.', '3 ก.ย.') WHERE EMPPOS_REFF like '%3ก.ย.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '5ต.ค.2', '5 ต.ค. 2') WHERE EMPPOS_REFF like '%5ต.ค.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '3 1 มี.ค.', '31 มี.ค.') WHERE EMPPOS_REFF like '%3 1 มี.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '5ม.ค.', '5 ม.ค.') WHERE EMPPOS_REFF like '%5ม.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '7ต.ค.2', '7 ต.ค. 2') WHERE EMPPOS_REFF like '%7ต.ค.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '4ต.ค.2', '4 ต.ค. 2') WHERE EMPPOS_REFF like '%4ต.ค.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '8ก.ย.', '8 ก.ย.') WHERE EMPPOS_REFF like '%8ก.ย.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '2ส.ค.', '2 ส.ค.') WHERE EMPPOS_REFF like '%2ส.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '7ก.ย.3', '7 ก.ย. 3') WHERE EMPPOS_REFF like '%7ก.ย.3%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'มิ. ย.', 'มิ.ย.') WHERE EMPPOS_REFF like '%มิ. ย.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'มืย.58', 'มิ.ย. 58') WHERE EMPPOS_REFF like '%มืย.58%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'ก.ย1', 'ก.ย. 1') WHERE EMPPOS_REFF like '%ก.ย1%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '2 1 มี.ค.3', '21 มี.ค. 3') WHERE EMPPOS_REFF like '%2 1 มี.ค.3%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '5ธ.ค.2', '5 ธ.ค. 2') WHERE EMPPOS_REFF like '%5ธ.ค.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'ลว. ลว.', 'ลว.') WHERE EMPPOS_REFF like '%ลว. ลว.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'ลฃว.', 'ลว.') WHERE EMPPOS_REFF like '%ลฃว.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '-ลว.', 'ลว.') WHERE EMPPOS_REFF like '%-ลว.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '- ลว.', 'ลว.') WHERE EMPPOS_REFF like '%- ลว.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '- ลว', 'ลว.') WHERE EMPPOS_REFF like '%- ลว%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '-ลว', 'ลว.') WHERE EMPPOS_REFF like '%-ลว%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'ลว .', 'ลว.') WHERE EMPPOS_REFF like '%ลว .%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'ลวฆ.', 'ลว.') WHERE EMPPOS_REFF like '%ลวฆ.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'ลส.', 'ลว.') WHERE EMPPOS_REFF like '%ลส.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'ลว. .', 'ลว.') WHERE EMPPOS_REFF like '%ลว. .%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '- ฃลว.', 'ลว.') WHERE EMPPOS_REFF like '%- ฃลว.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'พค5', 'พ.ค. 5') WHERE EMPPOS_REFF like '%พค5%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '5พ.ค.', '5 พ.ค.') WHERE EMPPOS_REFF like '%5พ.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '0ต.ค.', '0 ต.ค.') WHERE EMPPOS_REFF like '%0ต.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'ธ.ค4', 'ธ.ค. 4') WHERE EMPPOS_REFF like '%ธ.ค4%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '4ก.ย.', '4 ก.ย.') WHERE EMPPOS_REFF like '%4ก.ย.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'ธ. ค.', 'ธ.ค.') WHERE EMPPOS_REFF like '%ธ. ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '6ธ.ค.', '6 ธ.ค.') WHERE EMPPOS_REFF like '%6ธ.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'ส.คต.', 'ส.ค.') WHERE EMPPOS_REFF like '%ส.คต.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '1 9 มี.ค.3', '19 มี.ค. 3') WHERE EMPPOS_REFF like '%1 9 มี.ค.3%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'พดค.5', 'พ.ค. 5') WHERE EMPPOS_REFF like '%พดค.5%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'พ.ยง4', 'พ.ย. 4') WHERE EMPPOS_REFF like '%พ.ยง4%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'ก.ย4', 'ก.ย. 4') WHERE EMPPOS_REFF like '%ก.ย4%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'ก.ย2', 'ก.ย. 2') WHERE EMPPOS_REFF like '%ก.ย2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'ก.ค2', 'ก.ค. 2') WHERE EMPPOS_REFF like '%ก.ค2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'มี.ค2', 'มี.ค. 2') WHERE EMPPOS_REFF like '%มี.ค2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'ลว.0 24', 'ลว. 24') WHERE EMPPOS_REFF like '%ลว.0 24%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'ธ..ค.', 'ธ.ค.') WHERE EMPPOS_REFF like '%ธ..ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'ืก.ย.', 'ก.ย.') WHERE EMPPOS_REFF like '%ืก.ย.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'ก.ย.2', 'ก.ย. 2') WHERE EMPPOS_REFF like '%ก.ย.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'ลวง 28', 'ลว. 28') WHERE EMPPOS_REFF like '%ลวง 28%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'ส.ค4', 'ส.ค. 4') WHERE EMPPOS_REFF like '%ส.ค4%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '2 7 ก.ย.3', '27 ก.ย. 3') WHERE EMPPOS_REFF like '%2 7 ก.ย.3%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'พพ.ย.', 'พ.ย.') WHERE EMPPOS_REFF like '%พพ.ย.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'ทต.ค.', 'ต.ค.') WHERE EMPPOS_REFF like '%ทต.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'สค.5', 'ส.ค. 5') WHERE EMPPOS_REFF like '%สค.5%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'กก.ย.', 'ก.ย.') WHERE EMPPOS_REFF like '%กก.ย.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '9เม.ย.', '9 เม.ย.') WHERE EMPPOS_REFF like '%9เม.ย.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'ก3ย3', 'ก.ย.') WHERE EMPPOS_REFF like '%ก3ย3%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '60มิ.ย.', '6 มิ.ย.') WHERE EMPPOS_REFF like '%60มิ.ย.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'สค.2', 'ส.ค. 2') WHERE EMPPOS_REFF like '%สค.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'ก.ค.2', 'ก.ค. 2') WHERE EMPPOS_REFF like '%ก.ค.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'มิย ', 'มิ.ย. ') WHERE EMPPOS_REFF like '%มิย %' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '8มิย.', '8 มิ.ย.') WHERE EMPPOS_REFF like '%8มิย.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '170ตค ', '17 ต.ค. ') WHERE EMPPOS_REFF like '%170ตค %' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'ธ.ค2', 'ธ.ค. 2') WHERE EMPPOS_REFF like '%ธ.ค2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'ก .ย.', 'ก.ย.') WHERE EMPPOS_REFF like '%ก .ย.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'เมย.5', 'เม.ย. 5') WHERE EMPPOS_REFF like '%เมย.5%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'พึค.5', 'พ.ค. 5') WHERE EMPPOS_REFF like '%พึค.5%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '];. 19 d.r.', 'ลว. 19 ก.พ.') WHERE EMPPOS_REFF like '%];. 19 d.r.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'กย.5', 'ก.ย. 5') WHERE EMPPOS_REFF like '%กย.5%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '5ก.ค.2', '5 ก.ค. 2') WHERE EMPPOS_REFF like '%5ก.ค.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'ส.ค.2', 'ส.ค. 2') WHERE EMPPOS_REFF like '%ส.ค.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'ต.ค.2', 'ต.ค. 2') WHERE EMPPOS_REFF like '%ต.ค.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'ลว.0 12', 'ลว. 12') WHERE EMPPOS_REFF like '%ลว.0 12%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'กด.ย.', 'ก.ย.') WHERE EMPPOS_REFF like '%กด.ย.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, ' ลวง ', ' ลว. ') WHERE EMPPOS_REFF like '% ลวง %' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '9ก.ย.', '9 ก.ย.') WHERE EMPPOS_REFF like '%9ก.ย.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '7ก.ค.', '7 ก.ค.') WHERE EMPPOS_REFF like '%7ก.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '5ก.ย.', '5 ก.ย.') WHERE EMPPOS_REFF like '%5ก.ย.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '1ก.ค.', '1 ก.ค.') WHERE EMPPOS_REFF like '%1ก.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '9ก.ค.', '9 ก.ค.') WHERE EMPPOS_REFF like '%9ก.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '1ส.ค.', '1 ส.ค.') WHERE EMPPOS_REFF like '%1ส.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '5ก.ค.', '5 ก.ค.') WHERE EMPPOS_REFF like '%5ก.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '3ก.ค.', '3 ก.ค.') WHERE EMPPOS_REFF like '%3ก.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '4ก.ค.', '4 ก.ค.') WHERE EMPPOS_REFF like '%4ก.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '3ส.ค.', '3 ส.ค.') WHERE EMPPOS_REFF like '%3ส.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '1ก.ย.', '1 ก.ย.') WHERE EMPPOS_REFF like '%1ก.ย.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '2ก.ย.', '2 ก.ย.') WHERE EMPPOS_REFF like '%2ก.ย.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '2ก.ค.', '2 ก.ค.') WHERE EMPPOS_REFF like '%2ก.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '6ก.ค.', '6 ก.ค.') WHERE EMPPOS_REFF like '%6ก.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '4ส.ค.', '4 ส.ค.') WHERE EMPPOS_REFF like '%4ส.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '5ส.ค.', '5 ส.ค.') WHERE EMPPOS_REFF like '%5ส.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '6ส.ค.', '6 ส.ค.') WHERE EMPPOS_REFF like '%6ส.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '7ส.ค.', '7 ส.ค.') WHERE EMPPOS_REFF like '%7ส.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '0ส.ค.', '0 ส.ค.') WHERE EMPPOS_REFF like '%0ส.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '9ส.ค.', '9 ส.ค.') WHERE EMPPOS_REFF like '%9ส.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '7/ ก.ย.3', '7 ก.ย. 3') WHERE EMPPOS_REFF like '%7/ ก.ย.3%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'ลว. ก.ย.', 'ลว. 00 ก.ย.') WHERE EMPPOS_REFF like '%ลว. ก.ย.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '9ก.พ.2', '9 ก.พ. 2') WHERE EMPPOS_REFF like '%9ก.พ.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'ลว..', 'ลว.') WHERE EMPPOS_REFF like '%ลว..%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'ต.๕ง2', 'ต.ค. 2') WHERE EMPPOS_REFF like '%ต.๕ง2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '4มี.ค.', '4 มี.ค.') WHERE EMPPOS_REFF like '%4มี.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'พ.บ.', 'พ.ย.') WHERE EMPPOS_REFF like '%พ.บ.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '4ต.ค.3', '4 ต.ค. 3') WHERE EMPPOS_REFF like '%4ต.ค.3%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '่ต.ค.', 'ต.ค.') WHERE EMPPOS_REFF like '%่ต.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'มิิ.ย.', 'มิ.ย.') WHERE EMPPOS_REFF like '%มิิ.ย.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '- พ.ย. 45', '- 00 พ.ย. 45') WHERE EMPPOS_REFF like '%- พ.ย. 45%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '1 .ค. 2', '1 ธ.ค. 2') WHERE EMPPOS_REFF like '%1 .ค. 2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '3.ย.', '3 ก.ย.') WHERE EMPPOS_REFF like '%3.ย.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'ก.พ.2', 'ก.พ. 2') WHERE EMPPOS_REFF like '%ก.พ.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '8.ย.', '8 ก.ย.') WHERE EMPPOS_REFF like '%8.ย.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '1 ํ.ค.', '1 ธ.ค.') WHERE EMPPOS_REFF like '%1 ํ.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'ก.บ.', 'ก.ย.') WHERE EMPPOS_REFF like '%ก.บ.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'กค5', 'ก.ค. 5') WHERE EMPPOS_REFF like '%กค5%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '3พ.ย.2', '3 พ.ย. 2') WHERE EMPPOS_REFF like '%3พ.ย.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'ส.คง2', 'ส.ค. 2') WHERE EMPPOS_REFF like '%ส.คง2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '1 7 ก.ย.3', '17 ก.ย. 3') WHERE EMPPOS_REFF like '%1 7 ก.ย.3%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '21.ค.', '21 ธ.ค.') WHERE EMPPOS_REFF like '%21.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '- ล. ', '- ลว. ') WHERE EMPPOS_REFF like '%- ล. %' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '3พ.ย.', '3 พ.ย.') WHERE EMPPOS_REFF like '%3พ.ย.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '.ต.ค.', 'ต.ค.') WHERE EMPPOS_REFF like '%.ต.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'ป.ย.', 'ก.ย.') WHERE EMPPOS_REFF like '%ป.ย.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '18.ค.', '18 ก.ค.') WHERE EMPPOS_REFF like '%18.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '28 2541', '28 ก.ย. 2541') WHERE EMPPOS_REFF like '%28 2541%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'ตค.2', 'ต.ค. 2') WHERE EMPPOS_REFF like '%ตค.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'ส.ค.5', 'ส.ค. 5') WHERE EMPPOS_REFF like '%ส.ค.5%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'กค.5', 'ก.ค. 5') WHERE EMPPOS_REFF like '%กค.5%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'ธค.5', 'ธ.ค. 5') WHERE EMPPOS_REFF like '%ธค.5%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '3 .ค.', '3 ธ.ค.') WHERE EMPPOS_REFF like '%3 .ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'มิย.5', 'มิ.ย. 5') WHERE EMPPOS_REFF like '%มิย.5%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '4ก.พ.', '4 ก.พ.') WHERE EMPPOS_REFF like '%4ก.พ.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '0ก.พ.', '0 ก.พ.') WHERE EMPPOS_REFF like '%0ก.พ.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '8ก.พ.', '8 ก.พ.') WHERE EMPPOS_REFF like '%8ก.พ.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '6ก.พ.', '6 ก.พ.') WHERE EMPPOS_REFF like '%6ก.พ.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '1ก.พ.', '1 ก.พ.') WHERE EMPPOS_REFF like '%1ก.พ.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '3ก.พ.', '3 ก.พ.') WHERE EMPPOS_REFF like '%3ก.พ.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '5ก.พ.', '5 ก.พ.') WHERE EMPPOS_REFF like '%5ก.พ.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '9 ม.ย.2', '9 เม.ย. 2') WHERE EMPPOS_REFF like '%9 ม.ย.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '7มิ.ย.', '7 มิ.ย.') WHERE EMPPOS_REFF like '%7มิ.ย.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'พก.ค.', 'พ.ค.') WHERE EMPPOS_REFF like '%พก.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '1ธ.ค.2', '1 ธ.ค. 2') WHERE EMPPOS_REFF like '%1ธ.ค.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '1พ.ค.2', '1 พ.ค. 2') WHERE EMPPOS_REFF like '%1พ.ค.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '1ม.ค.2', '1 ม.ค. 2') WHERE EMPPOS_REFF like '%1ม.ค.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '1มิ.ย.2', '1 มิ.ย. 2') WHERE EMPPOS_REFF like '%1มิ.ย.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '1เม.ย.2', '1 เม.ย. 2') WHERE EMPPOS_REFF like '%1เม.ย.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '02092551', '02/09/2551') WHERE EMPPOS_REFF like '%02092551%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '2พ.ค.2', '2 พ.ค. 2') WHERE EMPPOS_REFF like '%2พ.ค.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '2พ.ย.2', '2 พ.ย. 2') WHERE EMPPOS_REFF like '%2พ.ย.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '2มิ.ย.2', '2 มิ.ย. 2') WHERE EMPPOS_REFF like '%2มิ.ย.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '2มี.ค.2', '2 มี.ค. 2') WHERE EMPPOS_REFF like '%2มี.ค.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '2เม.ย.2', '2 เม.ย. 2') WHERE EMPPOS_REFF like '%2เม.ย.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '3ธ.ค.2', '3 ธ.ค. 2') WHERE EMPPOS_REFF like '%3ธ.ค.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '3พ.ค.2', '3 พ.ค. 2') WHERE EMPPOS_REFF like '%3พ.ค.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '3มิ.ย.2', '3 มิ.ย. 2') WHERE EMPPOS_REFF like '%3มิ.ย.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '3เม.ย.2', '3 เม.ย. 2') WHERE EMPPOS_REFF like '%3เม.ย.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '4ธ.ค.2', '4 ธ.ค. 2') WHERE EMPPOS_REFF like '%4ธ.ค.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '4พ.ค.2', '4 พ.ค. 2') WHERE EMPPOS_REFF like '%4พ.ค.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '4พ.ย.2', '4 พ.ย. 2') WHERE EMPPOS_REFF like '%4พ.ย.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '4มิ.ย.2', '4 มิ.ย. 2') WHERE EMPPOS_REFF like '%4มิ.ย.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '4เม.ย.2', '4 เม.ย. 2') WHERE EMPPOS_REFF like '%4เม.ย.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '5พ.ย.2', '5 พ.ย. 2') WHERE EMPPOS_REFF like '%5พ.ย.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '5มิ.ย.2', '5 มิ.ย. 2') WHERE EMPPOS_REFF like '%5มิ.ย.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '5มี.ค.2', '5 มี.ค. 2') WHERE EMPPOS_REFF like '%5มี.ค.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '5เม.ย.2', '5 เม.ย. 2') WHERE EMPPOS_REFF like '%5เม.ย.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '6พ.ค.2', '6 พ.ค. 2') WHERE EMPPOS_REFF like '%6พ.ค.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '6พ.ย.2', '6 พ.ย. 2') WHERE EMPPOS_REFF like '%6พ.ย.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '6ม.ค.2', '6 ม.ค. 2') WHERE EMPPOS_REFF like '%6ม.ค.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '6มิ.ย.2', '6 มิ.ย. 2') WHERE EMPPOS_REFF like '%6มิ.ย.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '6เม.ย.2', '6 เม.ย. 2') WHERE EMPPOS_REFF like '%6เม.ย.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '07032554', '07/03/2554') WHERE EMPPOS_REFF like '%07032554%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '7พ.ค.2', '7 พ.ค. 2') WHERE EMPPOS_REFF like '%7พ.ค.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '7พ.ย.2', '7 พ.ย. 2') WHERE EMPPOS_REFF like '%7พ.ย.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '7ม.ค.2', '7 ม.ค. 2') WHERE EMPPOS_REFF like '%7ม.ค.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '7มิ.ย.2', '7 มิ.ย. 2') WHERE EMPPOS_REFF like '%7มิ.ย.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '8ธ.ค.2', '8 ธ.ค. 2') WHERE EMPPOS_REFF like '%8ธ.ค.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '8พ.ค.2', '8 พ.ค. 2') WHERE EMPPOS_REFF like '%8พ.ค.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '8พ.ย.2', '8 พ.ย. 2') WHERE EMPPOS_REFF like '%8พ.ย.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '8ม.ค.2', '8 ม.ค. 2') WHERE EMPPOS_REFF like '%8ม.ค.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '8มิ.ย.2', '8 มิ.ย. 2') WHERE EMPPOS_REFF like '%8มิ.ย.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '8มี.ค.2', '8 มี.ค. 2') WHERE EMPPOS_REFF like '%8มี.ค.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '8เม.ย.2', '8 เม.ย. 2') WHERE EMPPOS_REFF like '%8เม.ย.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '9ธ.ค.2', '9 ธ.ค. 2') WHERE EMPPOS_REFF like '%9ธ.ค.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '9พ.ค.2', '9 พ.ค. 2') WHERE EMPPOS_REFF like '%9พ.ค.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '9พ.ย.2', '9 พ.ย. 2') WHERE EMPPOS_REFF like '%9พ.ย.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '9ม.ค.2', '9 ม.ค. 2') WHERE EMPPOS_REFF like '%9ม.ค.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '9มิ.ย.2', '9 มิ.ย. 2') WHERE EMPPOS_REFF like '%9มิ.ย.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '10025117', '10/02/2517') WHERE EMPPOS_REFF like '%10025117%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '0พ.ค.2', '0 พ.ค. 2') WHERE EMPPOS_REFF like '%0พ.ค.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '0พ.ย.2', '0 พ.ย. 2') WHERE EMPPOS_REFF like '%0พ.ย.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '0ม.ค.2', '0 ม.ค. 2') WHERE EMPPOS_REFF like '%0ม.ค.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '0มิ.ย.2', '0 มิ.ย. 2') WHERE EMPPOS_REFF like '%0มิ.ย.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '0มี.ค.2', '0 มี.ค. 2') WHERE EMPPOS_REFF like '%0มี.ค.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '0เม.ย.2', '0 เม.ย. 2') WHERE EMPPOS_REFF like '%0เม.ย.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'ลว. ท19 มิ.ย. 2549', 'ลว. 19 มิ.ย. 2549') WHERE EMPPOS_REFF like '%ลว. ท19 มิ.ย. 2549%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'ลวง.25', 'ลว.25') WHERE EMPPOS_REFF like '%ลวง.25%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '0ธ.ค.2', '0 ธ.ค. 2') WHERE EMPPOS_REFF like '%0ธ.ค.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '2มี.ค.3', '2 มี.ค. 3') WHERE EMPPOS_REFF like '%2มี.ค.3%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '3ธ.ค.4', '3 ธ.ค. 4') WHERE EMPPOS_REFF like '%3ธ.ค.4%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '4มีค.5', '4 มี.ค. 5') WHERE EMPPOS_REFF like '%4มีค.5%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '4เม.ย.4', '4 เม.ย. 4') WHERE EMPPOS_REFF like '%4เม.ย.4%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '5มีค.5', '5 มี.ค. 5') WHERE EMPPOS_REFF like '%5มีค.5%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '7ก.ย.4', '7 ก.ย. 4') WHERE EMPPOS_REFF like '%7ก.ย.4%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '7มีค.5', '7 มี.ค. 5') WHERE EMPPOS_REFF like '%7มีค.5%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '4.มค.5', '4 ม.ค. 5') WHERE EMPPOS_REFF like '%4.มค.5%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '15ธ.ค.0245', '15 ธ.ค. 2545') WHERE EMPPOS_REFF like '%15ธ.ค.0245%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '6ตค.5', '6 ต.ค. 5') WHERE EMPPOS_REFF like '%6ตค.5%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '6พย.2', '6 พ.ย. 2') WHERE EMPPOS_REFF like '%6พย.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '8ม.ค.3', '8 ม.ค. 3') WHERE EMPPOS_REFF like '%8ม.ค.3%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '8มีค.5', '8 มี.ค. 5') WHERE EMPPOS_REFF like '%8มีค.5%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, '19032552', '19/03/2552') WHERE EMPPOS_REFF like '%19032552%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'ต.ค. 2626', 'ต.ค. 2526') WHERE EMPPOS_REFF like '%ต.ค. 2626%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'ก.ค.5219', 'ก.ค. 2519') WHERE EMPPOS_REFF like '%ก.ค.5219%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'ส.ค. 5222', 'ส.ค. 2522') WHERE EMPPOS_REFF like '%ส.ค. 5222%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'ก.พ.5437', 'ก.พ. 2537') WHERE EMPPOS_REFF like '%ก.พ.5437%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'xx', 'xx') WHERE EMPPOS_REFF like '%xx%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'xx', 'xx') WHERE EMPPOS_REFF like '%xx%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'xx', 'xx') WHERE EMPPOS_REFF like '%xx%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'xx', 'xx') WHERE EMPPOS_REFF like '%xx%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'xx', 'xx') WHERE EMPPOS_REFF like '%xx%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'xx', 'xx') WHERE EMPPOS_REFF like '%xx%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'xx', 'xx') WHERE EMPPOS_REFF like '%xx%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'xx', 'xx') WHERE EMPPOS_REFF like '%xx%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'xx', 'xx') WHERE EMPPOS_REFF like '%xx%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'xx', 'xx') WHERE EMPPOS_REFF like '%xx%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'xx', 'xx') WHERE EMPPOS_REFF like '%xx%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'xx', 'xx') WHERE EMPPOS_REFF like '%xx%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'xx', 'xx') WHERE EMPPOS_REFF like '%xx%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'xx', 'xx') WHERE EMPPOS_REFF like '%xx%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'xx', 'xx') WHERE EMPPOS_REFF like '%xx%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'xx', 'xx') WHERE EMPPOS_REFF like '%xx%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'xx', 'xx') WHERE EMPPOS_REFF like '%xx%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'xx', 'xx') WHERE EMPPOS_REFF like '%xx%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'xx', 'xx') WHERE EMPPOS_REFF like '%xx%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'xx', 'xx') WHERE EMPPOS_REFF like '%xx%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'xx', 'xx') WHERE EMPPOS_REFF like '%xx%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'xx', 'xx') WHERE EMPPOS_REFF like '%xx%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'xx', 'xx') WHERE EMPPOS_REFF like '%xx%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'xx', 'xx') WHERE EMPPOS_REFF like '%xx%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'xx', 'xx') WHERE EMPPOS_REFF like '%xx%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'xx', 'xx') WHERE EMPPOS_REFF like '%xx%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'xx', 'xx') WHERE EMPPOS_REFF like '%xx%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'xx', 'xx') WHERE EMPPOS_REFF like '%xx%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'xx', 'xx') WHERE EMPPOS_REFF like '%xx%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'xx', 'xx') WHERE EMPPOS_REFF like '%xx%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'xx', 'xx') WHERE EMPPOS_REFF like '%xx%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_REFF  = REPLACE(EMPPOS_REFF, 'xx', 'xx') WHERE EMPPOS_REFF like '%xx%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_DATE  = '01102549' WHERE EMPPOS_DATE = ' 1102549' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_DATE  = '01042552' WHERE EMPPOS_DATE = ' 0104255' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE EMPPOSITION_HISTORY SET EMPPOS_DATE  = '06122539' WHERE EMPPOS_DATE = ' 6122539' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();


	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '  ', ' ') WHERE GOVPOS_REFF like '%  %' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '  ', ' ') WHERE GOVPOS_REFF like '%  %' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '  ', ' ') WHERE GOVPOS_REFF like '%  %' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '  ', ' ') WHERE GOVPOS_REFF like '%  %' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '  ', ' ') WHERE GOVPOS_REFF like '%  %' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'มี. ค.', 'มี.ค.') WHERE GOVPOS_REFF like '%มี. ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'ต. ค.', 'ต.ค.') WHERE GOVPOS_REFF like '%ต. ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'ธ ค.', 'ธ.ค.') WHERE GOVPOS_REFF like '%ธ ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'มต.ค.', 'ต.ค.') WHERE GOVPOS_REFF like '%มต.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'มี ค.', 'มี.ค.') WHERE GOVPOS_REFF like '%มี ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '3มี.ค.', '3 มี.ค.') WHERE GOVPOS_REFF like '%3มี.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '0ก.ย.', '0 ก.ย.') WHERE GOVPOS_REFF like '%0ก.ย.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '3ต.ค.', '3 ต.ค.') WHERE GOVPOS_REFF like '%3ต.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '2ม.ค.', '2 ม.ค.') WHERE GOVPOS_REFF like '%2ม.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '1มี.ค.', '1 มี.ค.') WHERE GOVPOS_REFF like '%1มี.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '7ก.พ.', '7 ก.พ.') WHERE GOVPOS_REFF like '%7ก.พ.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '1พ.ย.', '1 พ.ย.') WHERE GOVPOS_REFF like '%1พ.ย.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '7เม.ย.', '7 เม.ย.') WHERE GOVPOS_REFF like '%7เม.ย.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '6มี.ค.', '6 มี.ค.') WHERE GOVPOS_REFF like '%6มี.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '9มี.ค.', '9 มี.ค.') WHERE GOVPOS_REFF like '%9มี.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '8ต.ค.', '8 ต.ค.') WHERE GOVPOS_REFF like '%8ต.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '4ม.ค.', '4 ม.ค.') WHERE GOVPOS_REFF like '%4ม.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'มี .ค.3', 'มี.ค. 3') WHERE GOVPOS_REFF like '%มี .ค.3%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'ก.ค ', 'ก.ค. ') WHERE GOVPOS_REFF like '%ก.ค %' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'มี.ค3', 'มี.ค. 3') WHERE GOVPOS_REFF like '%มี.ค3%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'ก.ย3', 'ก.ย. 3') WHERE GOVPOS_REFF like '%ก.ย3%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'ก. ย.', 'ก.ย.') WHERE GOVPOS_REFF like '%ก. ย.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'ก. พ.', 'ก.พ.') WHERE GOVPOS_REFF like '%ก. พ.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'ส. ค.', 'ส.ค.') WHERE GOVPOS_REFF like '%ส. ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'ก. ย.', 'ก.ย.') WHERE GOVPOS_REFF like '%ก. ย.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'พ. ย.', 'พ.ย.') WHERE GOVPOS_REFF like '%พ. ย.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'ก. ค.', 'ก.ค.') WHERE GOVPOS_REFF like '%ก. ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'เม. ย.', 'เม.ย.') WHERE GOVPOS_REFF like '%เม. ย.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '2ธ.ค.', '2 ธ.ค.') WHERE GOVPOS_REFF like '%2ธ.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'กค กค.', 'ก.ค.') WHERE GOVPOS_REFF like '%กค กค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'มค ', 'ม.ค. ') WHERE GOVPOS_REFF like '%มค %' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'มีค ', 'มี.ค. ') WHERE GOVPOS_REFF like '%มีค %' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '2ต.ค.', '2 ต.ค.') WHERE GOVPOS_REFF like '%2ต.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '7มี.ค.', '7 มี.ค.') WHERE GOVPOS_REFF like '%7มี.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '6ต.ค.', '6 ต.ค.') WHERE GOVPOS_REFF like '%6ต.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '1ต.ค.2', '1 ต.ค. 2') WHERE GOVPOS_REFF like '%1ต.ค.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'กห.ย.', 'ก.ย.') WHERE GOVPOS_REFF like '%กห.ย.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '7ก.ย.2', '7 ก.ย. 2') WHERE GOVPOS_REFF like '%7ก.ย.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '8ก.ค.', '8 ก.ค.') WHERE GOVPOS_REFF like '%8ก.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'มี .ค.', 'มี.ค.') WHERE GOVPOS_REFF like '%มี .ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '3ม.ค.2', '3 ม.ค. 2') WHERE GOVPOS_REFF like '%3ม.ค.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '1ต.ค.', '1 ต.ค.') WHERE GOVPOS_REFF like '%1ต.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '8ส.ค.', '8 ส.ค.') WHERE GOVPOS_REFF like '%8ส.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '0ก.ค.', '0 ก.ค.') WHERE GOVPOS_REFF like '%0ก.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '7ธ.ค.', '7 ธ.ค.') WHERE GOVPOS_REFF like '%7ธ.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '9ต.ค.', '9 ต.ค.') WHERE GOVPOS_REFF like '%9ต.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'มงค.', 'ม.ค.') WHERE GOVPOS_REFF like '%มงค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '6ก.ย.', '6 ก.ย.') WHERE GOVPOS_REFF like '%6ก.ย.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '6ต.ค.2', '6 ต.ค. 2') WHERE GOVPOS_REFF like '%6ต.ค.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '2ก.พ.', '2 ก.พ.') WHERE GOVPOS_REFF like '%2ก.พ.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '3ก.ย.', '3 ก.ย.') WHERE GOVPOS_REFF like '%3ก.ย.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '5ต.ค.2', '5 ต.ค. 2') WHERE GOVPOS_REFF like '%5ต.ค.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '3 1 มี.ค.', '31 มี.ค.') WHERE GOVPOS_REFF like '%3 1 มี.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '5ม.ค.', '5 ม.ค.') WHERE GOVPOS_REFF like '%5ม.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '7ต.ค.2', '7 ต.ค. 2') WHERE GOVPOS_REFF like '%7ต.ค.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '4ต.ค.2', '4 ต.ค. 2') WHERE GOVPOS_REFF like '%4ต.ค.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '8ก.ย.', '8 ก.ย.') WHERE GOVPOS_REFF like '%8ก.ย.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '2ส.ค.', '2 ส.ค.') WHERE GOVPOS_REFF like '%2ส.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '7ก.ย.3', '7 ก.ย. 3') WHERE GOVPOS_REFF like '%7ก.ย.3%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'มิ. ย.', 'มิ.ย.') WHERE GOVPOS_REFF like '%มิ. ย.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'ก.ย1', 'ก.ย. 1') WHERE GOVPOS_REFF like '%ก.ย1%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '2 1 มี.ค.3', '21 มี.ค. 3') WHERE GOVPOS_REFF like '%2 1 มี.ค.3%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '5ธ.ค.2', '5 ธ.ค. 2') WHERE GOVPOS_REFF like '%5ธ.ค.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'ลว. ลว.', 'ลว.') WHERE GOVPOS_REFF like '%ลว. ลว.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'ลฃว.', 'ลว.') WHERE GOVPOS_REFF like '%ลฃว.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '-ลว.', 'ลว.') WHERE GOVPOS_REFF like '%-ลว.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '- ลว.', 'ลว.') WHERE GOVPOS_REFF like '%- ลว.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '- ลว', 'ลว.') WHERE GOVPOS_REFF like '%- ลว%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '-ลว', 'ลว.') WHERE GOVPOS_REFF like '%-ลว%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'ลว .', 'ลว.') WHERE GOVPOS_REFF like '%ลว .%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'ลวฆ.', 'ลว.') WHERE GOVPOS_REFF like '%ลวฆ.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'ลส.', 'ลว.') WHERE GOVPOS_REFF like '%ลส.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'ลว. .', 'ลว.') WHERE GOVPOS_REFF like '%ลว. .%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '- ฃลว.', 'ลว.') WHERE GOVPOS_REFF like '%- ฃลว.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'พค5', 'พ.ค. 5') WHERE GOVPOS_REFF like '%พค5%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '5พ.ค.', '5 พ.ค.') WHERE GOVPOS_REFF like '%5พ.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '0ต.ค.', '0 ต.ค.') WHERE GOVPOS_REFF like '%0ต.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'ธ.ค4', 'ธ.ค. 4') WHERE GOVPOS_REFF like '%ธ.ค4%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '4ก.ย.', '4 ก.ย.') WHERE GOVPOS_REFF like '%4ก.ย.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'ธ. ค.', 'ธ.ค.') WHERE GOVPOS_REFF like '%ธ. ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '6ธ.ค.', '6 ธ.ค.') WHERE GOVPOS_REFF like '%6ธ.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'ส.คต.', 'ส.ค.') WHERE GOVPOS_REFF like '%ส.คต.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '1 9 มี.ค.3', '19 มี.ค. 3') WHERE GOVPOS_REFF like '%1 9 มี.ค.3%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'พดค.5', 'พ.ค. 5') WHERE GOVPOS_REFF like '%พดค.5%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'พ.ยง4', 'พ.ย. 4') WHERE GOVPOS_REFF like '%พ.ยง4%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'ก.ย4', 'ก.ย. 4') WHERE GOVPOS_REFF like '%ก.ย4%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'ก.ย2', 'ก.ย. 2') WHERE GOVPOS_REFF like '%ก.ย2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'ก.ค2', 'ก.ค. 2') WHERE GOVPOS_REFF like '%ก.ค2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'มี.ค2', 'มี.ค. 2') WHERE GOVPOS_REFF like '%มี.ค2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'ลว.0 24', 'ลว. 24') WHERE GOVPOS_REFF like '%ลว.0 24%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'ธ..ค.', 'ธ.ค.') WHERE GOVPOS_REFF like '%ธ..ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'ืก.ย.', 'ก.ย.') WHERE GOVPOS_REFF like '%ืก.ย.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'ก.ย.2', 'ก.ย. 2') WHERE GOVPOS_REFF like '%ก.ย.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'ลวง 28', 'ลว. 28') WHERE GOVPOS_REFF like '%ลวง 28%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'ส.ค4', 'ส.ค. 4') WHERE GOVPOS_REFF like '%ส.ค4%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '2 7 ก.ย.3', '27 ก.ย. 3') WHERE GOVPOS_REFF like '%2 7 ก.ย.3%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'พพ.ย.', 'พ.ย.') WHERE GOVPOS_REFF like '%พพ.ย.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'ทต.ค.', 'ต.ค.') WHERE GOVPOS_REFF like '%ทต.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'สค.5', 'ส.ค. 5') WHERE GOVPOS_REFF like '%สค.5%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'กก.ย.', 'ก.ย.') WHERE GOVPOS_REFF like '%กก.ย.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '9เม.ย.', '9 เม.ย.') WHERE GOVPOS_REFF like '%9เม.ย.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'ก3ย3', 'ก.ย.') WHERE GOVPOS_REFF like '%ก3ย3%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '60มิ.ย.', '6 มิ.ย.') WHERE GOVPOS_REFF like '%60มิ.ย.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'สค.2', 'ส.ค. 2') WHERE GOVPOS_REFF like '%สค.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'ก.ค.2', 'ก.ค. 2') WHERE GOVPOS_REFF like '%ก.ค.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'มิย ', 'มิ.ย. ') WHERE GOVPOS_REFF like '%มิย %' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '8มิย.', '8 มิ.ย.') WHERE GOVPOS_REFF like '%8มิย.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '170ตค ', '17 ต.ค. ') WHERE GOVPOS_REFF like '%170ตค %' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'ธ.ค2', 'ธ.ค. 2') WHERE GOVPOS_REFF like '%ธ.ค2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'ก .ย.', 'ก.ย.') WHERE GOVPOS_REFF like '%ก .ย.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'เมย.5', 'เม.ย. 5') WHERE GOVPOS_REFF like '%เมย.5%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'พึค.5', 'พ.ค. 5') WHERE GOVPOS_REFF like '%พึค.5%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '];. 19 d.r.', 'ลว. 19 ก.พ.') WHERE GOVPOS_REFF like '%];. 19 d.r.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'กย.5', 'ก.ย. 5') WHERE GOVPOS_REFF like '%กย.5%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '5ก.ค.2', '5 ก.ค. 2') WHERE GOVPOS_REFF like '%5ก.ค.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'ส.ค.2', 'ส.ค. 2') WHERE GOVPOS_REFF like '%ส.ค.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'ต.ค.2', 'ต.ค. 2') WHERE GOVPOS_REFF like '%ต.ค.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'ลว.0 12', 'ลว. 12') WHERE GOVPOS_REFF like '%ลว.0 12%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'กด.ย.', 'ก.ย.') WHERE GOVPOS_REFF like '%กด.ย.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, ' ลวง ', ' ลว. ') WHERE GOVPOS_REFF like '% ลวง %' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '9ก.ย.', '9 ก.ย.') WHERE GOVPOS_REFF like '%9ก.ย.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '7ก.ค.', '7 ก.ค.') WHERE GOVPOS_REFF like '%7ก.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '5ก.ย.', '5 ก.ย.') WHERE GOVPOS_REFF like '%5ก.ย.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '1ก.ค.', '1 ก.ค.') WHERE GOVPOS_REFF like '%1ก.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '9ก.ค.', '9 ก.ค.') WHERE GOVPOS_REFF like '%9ก.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '1ส.ค.', '1 ส.ค.') WHERE GOVPOS_REFF like '%1ส.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '5ก.ค.', '5 ก.ค.') WHERE GOVPOS_REFF like '%5ก.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '3ก.ค.', '3 ก.ค.') WHERE GOVPOS_REFF like '%3ก.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '4ก.ค.', '4 ก.ค.') WHERE GOVPOS_REFF like '%4ก.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '3ส.ค.', '3 ส.ค.') WHERE GOVPOS_REFF like '%3ส.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '1ก.ย.', '1 ก.ย.') WHERE GOVPOS_REFF like '%1ก.ย.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '2ก.ย.', '2 ก.ย.') WHERE GOVPOS_REFF like '%2ก.ย.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '2ก.ค.', '2 ก.ค.') WHERE GOVPOS_REFF like '%2ก.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '6ก.ค.', '6 ก.ค.') WHERE GOVPOS_REFF like '%6ก.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '4ส.ค.', '4 ส.ค.') WHERE GOVPOS_REFF like '%4ส.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '5ส.ค.', '5 ส.ค.') WHERE GOVPOS_REFF like '%5ส.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '6ส.ค.', '6 ส.ค.') WHERE GOVPOS_REFF like '%6ส.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '7ส.ค.', '7 ส.ค.') WHERE GOVPOS_REFF like '%7ส.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '0ส.ค.', '0 ส.ค.') WHERE GOVPOS_REFF like '%0ส.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '9ส.ค.', '9 ส.ค.') WHERE GOVPOS_REFF like '%9ส.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '7/ ก.ย.3', '7 ก.ย. 3') WHERE GOVPOS_REFF like '%7/ ก.ย.3%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'ลว. ก.ย.', 'ลว. 00 ก.ย.') WHERE GOVPOS_REFF like '%ลว. ก.ย.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '9ก.พ.2', '9 ก.พ. 2') WHERE GOVPOS_REFF like '%9ก.พ.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'ลว..', 'ลว.') WHERE GOVPOS_REFF like '%ลว..%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'ต.๕ง2', 'ต.ค. 2') WHERE GOVPOS_REFF like '%ต.๕ง2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '4มี.ค.', '4 มี.ค.') WHERE GOVPOS_REFF like '%4มี.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'พ.บ.', 'พ.ย.') WHERE GOVPOS_REFF like '%พ.บ.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '4ต.ค.3', '4 ต.ค. 3') WHERE GOVPOS_REFF like '%4ต.ค.3%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '่ต.ค.', 'ต.ค.') WHERE GOVPOS_REFF like '%่ต.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'มิิ.ย.', 'มิ.ย.') WHERE GOVPOS_REFF like '%มิิ.ย.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '- พ.ย. 45', '- 00 พ.ย. 45') WHERE GOVPOS_REFF like '%- พ.ย. 45%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '1 .ค. 2', '1 ธ.ค. 2') WHERE GOVPOS_REFF like '%1 .ค. 2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '3.ย.', '3 ก.ย.') WHERE GOVPOS_REFF like '%3.ย.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'ก.พ.2', 'ก.พ. 2') WHERE GOVPOS_REFF like '%ก.พ.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '8.ย.', '8 ก.ย.') WHERE GOVPOS_REFF like '%8.ย.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '1 ํ.ค.', '1 ธ.ค.') WHERE GOVPOS_REFF like '%1 ํ.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'ก.บ.', 'ก.ย.') WHERE GOVPOS_REFF like '%ก.บ.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'กค5', 'ก.ค. 5') WHERE GOVPOS_REFF like '%กค5%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '3พ.ย.2', '3 พ.ย. 2') WHERE GOVPOS_REFF like '%3พ.ย.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'ส.คง2', 'ส.ค. 2') WHERE GOVPOS_REFF like '%ส.คง2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '1 7 ก.ย.3', '17 ก.ย. 3') WHERE GOVPOS_REFF like '%1 7 ก.ย.3%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '21.ค.', '21 ธ.ค.') WHERE GOVPOS_REFF like '%21.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '- ล. ', '- ลว. ') WHERE GOVPOS_REFF like '%- ล. %' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '3พ.ย.', '3 พ.ย.') WHERE GOVPOS_REFF like '%3พ.ย.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '.ต.ค.', 'ต.ค.') WHERE GOVPOS_REFF like '%.ต.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'ป.ย.', 'ก.ย.') WHERE GOVPOS_REFF like '%ป.ย.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '18.ค.', '18 ก.ค.') WHERE GOVPOS_REFF like '%18.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '28 2541', '28 ก.ย. 2541') WHERE GOVPOS_REFF like '%28 2541%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'ตค.2', 'ต.ค. 2') WHERE GOVPOS_REFF like '%ตค.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'ส.ค.5', 'ส.ค. 5') WHERE GOVPOS_REFF like '%ส.ค.5%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'กค.5', 'ก.ค. 5') WHERE GOVPOS_REFF like '%กค.5%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'ธค.5', 'ธ.ค. 5') WHERE GOVPOS_REFF like '%ธค.5%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '3 .ค.', '3 ธ.ค.') WHERE GOVPOS_REFF like '%3 .ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'มิย.5', 'มิ.ย. 5') WHERE GOVPOS_REFF like '%มิย.5%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '4ก.พ.', '4 ก.พ.') WHERE GOVPOS_REFF like '%4ก.พ.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '0ก.พ.', '0 ก.พ.') WHERE GOVPOS_REFF like '%0ก.พ.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '8ก.พ.', '8 ก.พ.') WHERE GOVPOS_REFF like '%8ก.พ.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '6ก.พ.', '6 ก.พ.') WHERE GOVPOS_REFF like '%6ก.พ.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '1ก.พ.', '1 ก.พ.') WHERE GOVPOS_REFF like '%1ก.พ.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '3ก.พ.', '3 ก.พ.') WHERE GOVPOS_REFF like '%3ก.พ.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '5ก.พ.', '5 ก.พ.') WHERE GOVPOS_REFF like '%5ก.พ.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '9 ม.ย.2', '9 เม.ย. 2') WHERE GOVPOS_REFF like '%9 ม.ย.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '7มิ.ย.', '7 มิ.ย.') WHERE GOVPOS_REFF like '%7มิ.ย.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'พก.ค.', 'พ.ค.') WHERE GOVPOS_REFF like '%พก.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '1ธ.ค.2', '1 ธ.ค. 2') WHERE GOVPOS_REFF like '%1ธ.ค.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '1พ.ค.2', '1 พ.ค. 2') WHERE GOVPOS_REFF like '%1พ.ค.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '1ม.ค.2', '1 ม.ค. 2') WHERE GOVPOS_REFF like '%1ม.ค.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '1มิ.ย.2', '1 มิ.ย. 2') WHERE GOVPOS_REFF like '%1มิ.ย.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '1เม.ย.2', '1 เม.ย. 2') WHERE GOVPOS_REFF like '%1เม.ย.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '02092551', '02/09/2551') WHERE GOVPOS_REFF like '%02092551%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '2พ.ค.2', '2 พ.ค. 2') WHERE GOVPOS_REFF like '%2พ.ค.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '2พ.ย.2', '2 พ.ย. 2') WHERE GOVPOS_REFF like '%2พ.ย.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '2มิ.ย.2', '2 มิ.ย. 2') WHERE GOVPOS_REFF like '%2มิ.ย.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '2มี.ค.2', '2 มี.ค. 2') WHERE GOVPOS_REFF like '%2มี.ค.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '2เม.ย.2', '2 เม.ย. 2') WHERE GOVPOS_REFF like '%2เม.ย.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '3ธ.ค.2', '3 ธ.ค. 2') WHERE GOVPOS_REFF like '%3ธ.ค.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '3พ.ค.2', '3 พ.ค. 2') WHERE GOVPOS_REFF like '%3พ.ค.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '3มิ.ย.2', '3 มิ.ย. 2') WHERE GOVPOS_REFF like '%3มิ.ย.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '3เม.ย.2', '3 เม.ย. 2') WHERE GOVPOS_REFF like '%3เม.ย.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '4ธ.ค.2', '4 ธ.ค. 2') WHERE GOVPOS_REFF like '%4ธ.ค.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '4พ.ค.2', '4 พ.ค. 2') WHERE GOVPOS_REFF like '%4พ.ค.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '4พ.ย.2', '4 พ.ย. 2') WHERE GOVPOS_REFF like '%4พ.ย.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '4มิ.ย.2', '4 มิ.ย. 2') WHERE GOVPOS_REFF like '%4มิ.ย.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '4เม.ย.2', '4 เม.ย. 2') WHERE GOVPOS_REFF like '%4เม.ย.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '5พ.ย.2', '5 พ.ย. 2') WHERE GOVPOS_REFF like '%5พ.ย.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '5มิ.ย.2', '5 มิ.ย. 2') WHERE GOVPOS_REFF like '%5มิ.ย.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '5มี.ค.2', '5 มี.ค. 2') WHERE GOVPOS_REFF like '%5มี.ค.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '5เม.ย.2', '5 เม.ย. 2') WHERE GOVPOS_REFF like '%5เม.ย.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '6พ.ค.2', '6 พ.ค. 2') WHERE GOVPOS_REFF like '%6พ.ค.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '6พ.ย.2', '6 พ.ย. 2') WHERE GOVPOS_REFF like '%6พ.ย.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '6ม.ค.2', '6 ม.ค. 2') WHERE GOVPOS_REFF like '%6ม.ค.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '6มิ.ย.2', '6 มิ.ย. 2') WHERE GOVPOS_REFF like '%6มิ.ย.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '6เม.ย.2', '6 เม.ย. 2') WHERE GOVPOS_REFF like '%6เม.ย.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '07032554', '07/03/2554') WHERE GOVPOS_REFF like '%07032554%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '7พ.ค.2', '7 พ.ค. 2') WHERE GOVPOS_REFF like '%7พ.ค.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '7พ.ย.2', '7 พ.ย. 2') WHERE GOVPOS_REFF like '%7พ.ย.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '7ม.ค.2', '7 ม.ค. 2') WHERE GOVPOS_REFF like '%7ม.ค.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '7มิ.ย.2', '7 มิ.ย. 2') WHERE GOVPOS_REFF like '%7มิ.ย.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '8ธ.ค.2', '8 ธ.ค. 2') WHERE GOVPOS_REFF like '%8ธ.ค.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '8พ.ค.2', '8 พ.ค. 2') WHERE GOVPOS_REFF like '%8พ.ค.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '8พ.ย.2', '8 พ.ย. 2') WHERE GOVPOS_REFF like '%8พ.ย.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '8ม.ค.2', '8 ม.ค. 2') WHERE GOVPOS_REFF like '%8ม.ค.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '8มิ.ย.2', '8 มิ.ย. 2') WHERE GOVPOS_REFF like '%8มิ.ย.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '8มี.ค.2', '8 มี.ค. 2') WHERE GOVPOS_REFF like '%8มี.ค.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '8เม.ย.2', '8 เม.ย. 2') WHERE GOVPOS_REFF like '%8เม.ย.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '9ธ.ค.2', '9 ธ.ค. 2') WHERE GOVPOS_REFF like '%9ธ.ค.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '9พ.ค.2', '9 พ.ค. 2') WHERE GOVPOS_REFF like '%9พ.ค.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '9พ.ย.2', '9 พ.ย. 2') WHERE GOVPOS_REFF like '%9พ.ย.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '9ม.ค.2', '9 ม.ค. 2') WHERE GOVPOS_REFF like '%9ม.ค.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '9มิ.ย.2', '9 มิ.ย. 2') WHERE GOVPOS_REFF like '%9มิ.ย.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '10025117', '10/02/2517') WHERE GOVPOS_REFF like '%10025117%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '0พ.ค.2', '0 พ.ค. 2') WHERE GOVPOS_REFF like '%0พ.ค.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '0พ.ย.2', '0 พ.ย. 2') WHERE GOVPOS_REFF like '%0พ.ย.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '0ม.ค.2', '0 ม.ค. 2') WHERE GOVPOS_REFF like '%0ม.ค.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '0มิ.ย.2', '0 มิ.ย. 2') WHERE GOVPOS_REFF like '%0มิ.ย.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '0มี.ค.2', '0 มี.ค. 2') WHERE GOVPOS_REFF like '%0มี.ค.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '0เม.ย.2', '0 เม.ย. 2') WHERE GOVPOS_REFF like '%0เม.ย.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'ลว. ท19 มิ.ย. 2549', 'ลว. 19 มิ.ย. 2549') WHERE GOVPOS_REFF like '%ลว. ท19 มิ.ย. 2549%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'ลวง.25', 'ลว.25') WHERE GOVPOS_REFF like '%ลวง.25%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '0ธ.ค.2', '0 ธ.ค. 2') WHERE GOVPOS_REFF like '%0ธ.ค.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '2มี.ค.3', '2 มี.ค. 3') WHERE GOVPOS_REFF like '%2มี.ค.3%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '3ธ.ค.4', '3 ธ.ค. 4') WHERE GOVPOS_REFF like '%3ธ.ค.4%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '4มีค.5', '4 มี.ค. 5') WHERE GOVPOS_REFF like '%4มีค.5%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '4เม.ย.4', '4 เม.ย. 4') WHERE GOVPOS_REFF like '%4เม.ย.4%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '5มีค.5', '5 มี.ค. 5') WHERE GOVPOS_REFF like '%5มีค.5%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '7ก.ย.4', '7 ก.ย. 4') WHERE GOVPOS_REFF like '%7ก.ย.4%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '7มีค.5', '7 มี.ค. 5') WHERE GOVPOS_REFF like '%7มีค.5%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '4.มค.5', '4 ม.ค. 5') WHERE GOVPOS_REFF like '%4.มค.5%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '15ธ.ค.0245', '15 ธ.ค. 2545') WHERE GOVPOS_REFF like '%15ธ.ค.0245%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '6ตค.5', '6 ต.ค. 5') WHERE GOVPOS_REFF like '%6ตค.5%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '6พย.2', '6 พ.ย. 2') WHERE GOVPOS_REFF like '%6พย.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '8ม.ค.3', '8 ม.ค. 3') WHERE GOVPOS_REFF like '%8ม.ค.3%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '8มีค.5', '8 มี.ค. 5') WHERE GOVPOS_REFF like '%8มีค.5%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '19032552', '19/03/2552') WHERE GOVPOS_REFF like '%19032552%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '1ต.ค.', '1 ต.ค.') WHERE GOVPOS_REFF like '%1ต.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '2ต.ค.', '2 ต.ค.') WHERE GOVPOS_REFF like '%2ต.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '3ต.ค.', '3 ต.ค.') WHERE GOVPOS_REFF like '%3ต.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '01112548', '01/11/2548') WHERE GOVPOS_REFF like '%01112548%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '040530', '04/05/30') WHERE GOVPOS_REFF like '%040530%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '06112552', '06/11/2552') WHERE GOVPOS_REFF like '%06112552%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '0กย.2', '0 ก.ย. 2') WHERE GOVPOS_REFF like '%0กย.2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '22102552', '22/10/2552') WHERE GOVPOS_REFF like '%22102552%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '9กพ.1', '9 ก.พ. 1') WHERE GOVPOS_REFF like '%9กพ.1%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '19/052546', '19/05/2546') WHERE GOVPOS_REFF like '%19/052546%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '14/052552', '14/05/2552') WHERE GOVPOS_REFF like '%14/052552%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '26/พ.ย.', '26 พ.ย.') WHERE GOVPOS_REFF like '%26/พ.ย.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '30/พ.ย.', '30 พ.ย.') WHERE GOVPOS_REFF like '%30/พ.ย.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '28/พ.ย.', '28 พ.ย.') WHERE GOVPOS_REFF like '%28/พ.ย.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '23 มี.ค. 25544', '23 มี.ค. 2544') WHERE GOVPOS_REFF like '%23 มี.ค. 25544%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '02/102549', '02/10/2549') WHERE GOVPOS_REFF like '%02/102549%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'ที่30 ก.ย.45', '30 ก.ย. 45') WHERE GOVPOS_REFF like '%ที่30 ก.ย.45%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '22/ต.ค./2545', '22 ต.ค. 2545') WHERE GOVPOS_REFF like '%22/ต.ค./2545%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '23/092539', '23/09/2539') WHERE GOVPOS_REFF like '%23/092539%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '23/08/25416', '23/08/2516') WHERE GOVPOS_REFF like '%23/08/25416%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '01/042547', '01/04/2547') WHERE GOVPOS_REFF like '%01/042547%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '06/112538', '06/11/2538') WHERE GOVPOS_REFF like '%06/112538%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '30/092551', '30/09/2551') WHERE GOVPOS_REFF like '%30/092551%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '/ ก.ค. / 55', 'ก.ค. 55') WHERE GOVPOS_REFF like '%/ ก.ค. / 55%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '/ก.ค. / 55', 'ก.ค. 55') WHERE GOVPOS_REFF like '%/ก.ค. / 55%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '/ ส.ค. / 55', 'ส.ค. 55') WHERE GOVPOS_REFF like '%/ ส.ค. / 55%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '26/112530', '26/11/2530') WHERE GOVPOS_REFF like '%26/112530%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '14/02/40540', '14/02/2540') WHERE GOVPOS_REFF like '%14/02/40540%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '26/032541', '26/03/2541') WHERE GOVPOS_REFF like '%26/032541%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '2702/2524', '27/02/2524') WHERE GOVPOS_REFF like '%2702/2524%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '02/05/25490', '02/05/2549') WHERE GOVPOS_REFF like '%02/05/25490%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '31/ส.ค.', '31 ส.ค.') WHERE GOVPOS_REFF like '%31/ส.ค.%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '15 ม.ค. 587', '15 ม.ค. 57') WHERE GOVPOS_REFF like '%15 ม.ค. 587%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '16/11/0548', '16/11/2548') WHERE GOVPOS_REFF like '%16/11/0548%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '51/12/544', '05/11/2544') WHERE GOVPOS_REFF like '%51/12/544%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '41/02/544', '04/10/2544') WHERE GOVPOS_REFF like '%41/02/544%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '27-09/33', '27/09/33') WHERE GOVPOS_REFF like '%27-09/33%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '31/103/35', '31/03/35') WHERE GOVPOS_REFF like '%31/103/35%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '280930', '28/09/30') WHERE GOVPOS_REFF like '%280930%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '01/1025', '01/10/25') WHERE GOVPOS_REFF like '%01/1025%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '111/12/33', '11/12/33') WHERE GOVPOS_REFF like '%111/12/33%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '1ตค.1', '1 ต.ค. 1') WHERE GOVPOS_REFF like '%1ตค.1%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '230/09/20', '23/09/20') WHERE GOVPOS_REFF like '%230/09/20%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '01/102535', '01/10/2535') WHERE GOVPOS_REFF like '%01/102535%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '201/092540', '01/09/2540') WHERE GOVPOS_REFF like '%201/092540%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '29/0636', '29/06/36') WHERE GOVPOS_REFF like '%29/0636%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '3 เม.ย.25582', '3 เม.ย. 2552') WHERE GOVPOS_REFF like '%3 เม.ย.25582%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '2103/2518', '21/03/2518') WHERE GOVPOS_REFF like '%2103/2518%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '213/09/29', '23/09/29') WHERE GOVPOS_REFF like '%213/09/29%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '0706/42', '07/06/42') WHERE GOVPOS_REFF like '%0706/42%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '11 ธันวาคม 25522', '11 ธันวาคม 2552') WHERE GOVPOS_REFF like '%11 ธันวาคม 25522%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '21/012/30', '21/12/30') WHERE GOVPOS_REFF like '%21/012/30%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '02-03/42', '02/03/42') WHERE GOVPOS_REFF like '%02-03/42%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '09/04/252540', '09/04/2540') WHERE GOVPOS_REFF like '%09/04/252540%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '22/03/25492549', '22/03/2549') WHERE GOVPOS_REFF like '%22/03/25492549%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '27/.04/42', '27/04/42') WHERE GOVPOS_REFF like '%27/.04/42%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '001/05/24', '01/05/24') WHERE GOVPOS_REFF like '%001/05/24%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '01/108/33', '01/08/33') WHERE GOVPOS_REFF like '%01/108/33%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '11/1141', '11/11/41') WHERE GOVPOS_REFF like '%11/1141%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '16 ธ.ค. 565', '16 ธ.ค. 56') WHERE GOVPOS_REFF like '%16 ธ.ค. 565%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '19/102535', '19/10/2535') WHERE GOVPOS_REFF like '%19/102535%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '12/0133', '12/01/33') WHERE GOVPOS_REFF like '%12/0133%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '0410/32', '04/10/32') WHERE GOVPOS_REFF like '%0410/32%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '023/09/20', '23/09/20') WHERE GOVPOS_REFF like '%023/09/20%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '18/096/27', '18/09/27') WHERE GOVPOS_REFF like '%18/096/27%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '27/0482513', '27/04/2513') WHERE GOVPOS_REFF like '%27/0482513%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '2.1/03/18', '21/03/18') WHERE GOVPOS_REFF like '%2.1/03/18%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '30-06/15', '30/06/15') WHERE GOVPOS_REFF like '%30-06/15%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '30/0615', '30/06/15') WHERE GOVPOS_REFF like '%30/0615%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '03/0220', '03/02/20') WHERE GOVPOS_REFF like '%03/0220%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '130/10/21', '30/10/21') WHERE GOVPOS_REFF like '%130/10/21%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '12/0731', '12/07/31') WHERE GOVPOS_REFF like '%12/0731%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '25-06-16', '25/06/16') WHERE GOVPOS_REFF like '%25-06-16%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '16-05/22', '16/05/22') WHERE GOVPOS_REFF like '%16-05/22%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '118/09/18', '18/09/18') WHERE GOVPOS_REFF like '%118/09/18%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '18-09/18', '18/09/18') WHERE GOVPOS_REFF like '%18-09/18%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '1809/18', '18/09/18') WHERE GOVPOS_REFF like '%1809/18%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '17/01/838', '17/01/38') WHERE GOVPOS_REFF like '%17/01/838%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '261/10/32', '11/12/32') WHERE GOVPOS_REFF like '%261/10/32%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '30-30/05/2', '30/05/21') WHERE GOVPOS_REFF like '%30-30/05/2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '23/009/2', '23/09/29') WHERE GOVPOS_REFF like '%23/009/2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '23/0929', '23/09/29') WHERE GOVPOS_REFF like '%23/0929%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '04/132', '04/10/32') WHERE GOVPOS_REFF like '%04/132%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '0410/32', '04/10/32') WHERE GOVPOS_REFF like '%0410/32%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '28/09.31', '28/09/31') WHERE GOVPOS_REFF like '%28/09.31%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '28.09/31', '28/09/31') WHERE GOVPOS_REFF like '%28.09/31%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '28/0931', '28/09/31') WHERE GOVPOS_REFF like '%28/0931%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '28/096/31', '28/09/31') WHERE GOVPOS_REFF like '%28/096/31%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '07-05/17', '07/05/17') WHERE GOVPOS_REFF like '%07-05/17%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '07/105', '07/05/17') WHERE GOVPOS_REFF like '%07/105%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '02/0436', '02/04/36') WHERE GOVPOS_REFF like '%02/0436%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '-28-12/09/28', '/28-12/09/28') WHERE GOVPOS_REFF like '%-28-12/09/28%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '12-09-28', '12/09/28') WHERE GOVPOS_REFF like '%12-09-28%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '12.09/28', '12/09/28') WHERE GOVPOS_REFF like '%12.09/28%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '12/009/28', '12/09/28') WHERE GOVPOS_REFF like '%12/009/28%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '12/0928', '12/09/28') WHERE GOVPOS_REFF like '%12/0928%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '28-12/09/2', '12/09/28') WHERE GOVPOS_REFF like '%28-12/09/2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '28/0930', '28/09/30') WHERE GOVPOS_REFF like '%28/0930%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '-25-01/10/25', '/25-01/10/25') WHERE GOVPOS_REFF like '%-25-01/10/25%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '-25-01/10/25', '/25-01/10/25') WHERE GOVPOS_REFF like '%-25-01/10/25%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '-27-18/09/27', '/27-18/09/27') WHERE GOVPOS_REFF like '%-27-18/09/27%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '01-10-25', '01/10/25') WHERE GOVPOS_REFF like '%01-10-25%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '189/09/2', '18/09/27') WHERE GOVPOS_REFF like '%189/09/2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '01/10./22', '01/10/22') WHERE GOVPOS_REFF like '%01/10./22%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '01-10-19', '01/10/19') WHERE GOVPOS_REFF like '%01-10-19%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '01/103/4', '01/10/34') WHERE GOVPOS_REFF like '%01/103/4%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '-36-27/09/36', '/36-27/09/36') WHERE GOVPOS_REFF like '%-36-27/09/36%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '27/09.36', '27/09/36') WHERE GOVPOS_REFF like '%27/09.36%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '02/10 23', '02/10/23') WHERE GOVPOS_REFF like '%02/10 23%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '26-21/09/2', '21/09/26') WHERE GOVPOS_REFF like '%26-21/09/2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '401/10/2', '01/10/24') WHERE GOVPOS_REFF like '%401/10/2%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '01/1024', '01/10/24') WHERE GOVPOS_REFF like '%01/1024%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '23-09-29', '23/09/29') WHERE GOVPOS_REFF like '%23-09-29%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '234/09/29', '23/09/29') WHERE GOVPOS_REFF like '%234/09/29%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '120/9/28', '12/09/28') WHERE GOVPOS_REFF like '%120/9/28%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '-30-28/09/30', '/30-28/09/30') WHERE GOVPOS_REFF like '%-30-28/09/30%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '11/10.21', '11/10/21') WHERE GOVPOS_REFF like '%11/10.21%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '21/09.26', '21/09/26') WHERE GOVPOS_REFF like '%21/09.26%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '27-08-33', '27/08/33') WHERE GOVPOS_REFF like '%27-08-33%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '016/08/36', '16/08/36') WHERE GOVPOS_REFF like '%016/08/36%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '12-03/19', '12/03/19') WHERE GOVPOS_REFF like '%12-03/19%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '-07-22/08/27', '/27-22/08/27') WHERE GOVPOS_REFF like '%-07-22/08/27%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '24/1028', '24/10/28') WHERE GOVPOS_REFF like '%24/1028%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '14/121/30', '14/12/30') WHERE GOVPOS_REFF like '%14/121/30%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '-32-22/12/32', '/32-22/12/32') WHERE GOVPOS_REFF like '%-32-22/12/32%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '29/211/20', '29/11/20') WHERE GOVPOS_REFF like '%29/211/20%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '20/12.28', '20/12/28') WHERE GOVPOS_REFF like '%20/12.28%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '403/12/', '03/12/25') WHERE GOVPOS_REFF like '%403/12/%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '225/01/24', '22/01/24') WHERE GOVPOS_REFF like '%225/01/24%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '24/02/36', '248/02/36') WHERE GOVPOS_REFF like '%248/02/36%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '0912/26', '09/12/26') WHERE GOVPOS_REFF like '%0912/26%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '=04/12/23', '04/12/23') WHERE GOVPOS_REFF like '%=04/12/23%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '22-01-29', '22/01/29') WHERE GOVPOS_REFF like '%22-01-29%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '15/04./20', '15/04/20') WHERE GOVPOS_REFF like '%15/04./20%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '-32-24/04/32', '/32-24/04/32') WHERE GOVPOS_REFF like '%-32-24/04/32%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '18-02/26', '18/02/26') WHERE GOVPOS_REFF like '%18-02/26%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '0/106/31', '01/06/31') WHERE GOVPOS_REFF like '%0/106/31%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '15/01./33', '15/01/33') WHERE GOVPOS_REFF like '%15/01./33%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '32-11/08/3', '11/08/32') WHERE GOVPOS_REFF like '%32-11/08/3%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '09/072517', '09/07/2517') WHERE GOVPOS_REFF like '%09/072517%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '08/011/17', '08/11/17') WHERE GOVPOS_REFF like '%08/011/17%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '09/0724', '09/07/24') WHERE GOVPOS_REFF like '%09/0724%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '06-10/26', '06/10/26') WHERE GOVPOS_REFF like '%06-10/26%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '-18-05/09/18', '/18-05/09/18') WHERE GOVPOS_REFF like '%-18-05/09/18%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '2911/20', '29/11/20') WHERE GOVPOS_REFF like '%2911/20%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '-24-20/02/24', '/24-20/02/24') WHERE GOVPOS_REFF like '%-24-20/02/24%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '-.30/10/21', '-30/10/21') WHERE GOVPOS_REFF like '%-.30/10/21%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '-48-21/10/2548', '/48-21/10/2548') WHERE GOVPOS_REFF like '%-48-21/10/2548%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '18/092545', '18/09/2545') WHERE GOVPOS_REFF like '%18/092545%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'จว.27/07/31', 'ลว.27/07/31') WHERE GOVPOS_REFF like '%จว.27/07/31%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '14/102547', '14/10/2547') WHERE GOVPOS_REFF like '%14/102547%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '17/072529', '17/07/2529') WHERE GOVPOS_REFF like '%17/072529%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '12/052542', '12/05/2542') WHERE GOVPOS_REFF like '%12/052542%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '13/08/25552', '13/08/2552') WHERE GOVPOS_REFF like '%13/08/25552%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '25/-2/34', '25/02/34') WHERE GOVPOS_REFF like '%25/-2/34%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '02/0/34', '02/04/34') WHERE GOVPOS_REFF like '%02/0/34%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '01/00/34', '01/10/34') WHERE GOVPOS_REFF like '%01/00/34%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '28/-09//31', '28/09/31') WHERE GOVPOS_REFF like '%28/-09//31%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '02/00/25', '02/11/25') WHERE GOVPOS_REFF like '%02/00/25%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '01/0/24', '01/10/24') WHERE GOVPOS_REFF like '%01/0/24%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '01/0/22', '01/10/22') WHERE GOVPOS_REFF like '%01/0/22%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '29//09/15', '29/09/15') WHERE GOVPOS_REFF like '%29//09/15%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '18//09/18', '18/09/18') WHERE GOVPOS_REFF like '%18//09/18%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '18//09/27', '18/09/27') WHERE GOVPOS_REFF like '%18//09/27%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '16//09/34', '16/09/34') WHERE GOVPOS_REFF like '%16//09/34%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '05//09/18', '05/09/18') WHERE GOVPOS_REFF like '%05//09/18%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '2//07/2543', '28/07/2543') WHERE GOVPOS_REFF like '%2//07/2543%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '18//06/27', '18/06/27') WHERE GOVPOS_REFF like '%18//06/27%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, '10//02/20', '10/02/20') WHERE GOVPOS_REFF like '%10//02/20%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'xx', 'xx') WHERE GOVPOS_REFF like '%xx%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'xx', 'xx') WHERE GOVPOS_REFF like '%xx%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'xx', 'xx') WHERE GOVPOS_REFF like '%xx%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'xx', 'xx') WHERE GOVPOS_REFF like '%xx%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'xx', 'xx') WHERE GOVPOS_REFF like '%xx%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'xx', 'xx') WHERE GOVPOS_REFF like '%xx%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'xx', 'xx') WHERE GOVPOS_REFF like '%xx%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'xx', 'xx') WHERE GOVPOS_REFF like '%xx%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'xx', 'xx') WHERE GOVPOS_REFF like '%xx%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_REFF  = REPLACE(GOVPOS_REFF, 'xx', 'xx') WHERE GOVPOS_REFF like '%xx%' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_DATE  = '01102549' WHERE GOVPOS_DATE = ' 1102549' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_DATE  = '01042552' WHERE GOVPOS_DATE = ' 0104255' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		$cmd = " UPDATE GOVPOSITION_HISTORY SET GOVPOS_DATE  = '06122539' WHERE GOVPOS_DATE = ' 6122539' ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

	} // end if

	if ($command=='SLIP' || $command=='SLIP_EMP'){
		$cmd = " truncate table per_slip ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if ($command=='SLIP') $EMP_TYPE = "G";
		elseif ($command=='SLIP_EMP')  $EMP_TYPE = "E";

		$cmd = " INSERT INTO PER_SLIP ( SLIP_ID, PER_ID, SLIP_YEAR, SLIP_MONTH, PER_CARDNO, PN_NAME, PER_NAME, PER_SURNAME, DEPARTMENT_NAME,	
					ORG_NAME, BANK_CODE, BANK_NAME, BRANCH_CODE, BRANCH_NAME, PER_BANK_ACCOUNT, INCOME_01, INCOME_02, INCOME_03, INCOME_04, INCOME_05,
					INCOME_06, INCOME_07, INCOME_08, INCOME_09, INCOME_10, INCOME_11, INCOME_12, INCOME_13, INCOME_14, INCOME_15, INCOME_16, INCOME_17, INCOME_18,
					INCOME_19, INCOME_20, INCOME_NAME_01, EXTRA_INCOME_01, INCOME_NAME_02, EXTRA_INCOME_02, INCOME_NAME_03, EXTRA_INCOME_03,
					INCOME_NAME_04, EXTRA_INCOME_04, OTHER_INCOME, TOTAL_INCOME, DEDUCT_01, DEDUCT_02, DEDUCT_03, DEDUCT_04, DEDUCT_05, DEDUCT_06,
					DEDUCT_07, DEDUCT_08, DEDUCT_09, DEDUCT_10, DEDUCT_11, DEDUCT_12, DEDUCT_13, DEDUCT_14, DEDUCT_15, DEDUCT_16, DEDUCT_NAME_01,	
					EXTRA_DEDUCT_01, DEDUCT_NAME_02, EXTRA_DEDUCT_02, DEDUCT_NAME_03, EXTRA_DEDUCT_03, DEDUCT_NAME_04, EXTRA_DEDUCT_04,
					DEDUCT_NAME_05, EXTRA_DEDUCT_05, DEDUCT_NAME_06, EXTRA_DEDUCT_06, DEDUCT_NAME_07, EXTRA_DEDUCT_07, DEDUCT_NAME_08,	
					EXTRA_DEDUCT_08, OTHER_DEDUCT, TOTAL_DEDUCT, NET_INCOME, APPROVE_DATE, UPDATE_USER, UPDATE_DATE ) SELECT rownum, a.ID, substr(YYMM,1,2), 
					substr(YYMM,3,2), a.ID_CARD, a.TITLE, a.FNAME, a.LNAME, '$SESS_DEPARTMENT_NAME', DIVNAME, BANKCODE, BANKNAME, BRCHCODE,  BRCHNAME, BANK_ACC,
					C14, C15, C16, C17, C18, C19, C20, C21, C22, C23, C24, C25, C26, C27, C28, C29, C30, C31, C32, C33, C34, C35, C36, C37, C38, C39, C40, C41, C42, C43, C44, C45, C46, C47, 
					C48, C49, C50, C51, C52, C53, C54, C55, C56, C57, C58, C59, C60, C61, C62, C63, C64, C65, C66, C67, C68, C69, C70, C71, C72, C73, C74, C75, C76, C77, C78, C79, $UPDATE_USER, '$UPDATE_DATE' 
					FROM SUCCESS.DPAY_SLIPTXT a, SUCCESS.COMMON_HISTORY b
					WHERE a.ID=b.ID and a.EMP_TYPE='$EMP_TYPE' ";
		$db_dpis->send_cmd($cmd);
		//echo "$cmd<br>";

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_SLIP ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_SLIP - $COUNT_NEW<br>";
	} // end if

	if ($command=='COMMAND' || $command=='COMMAND_EMP'){
		$cmd = " truncate table per_comdtl ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " truncate table per_command ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " select max(COM_ID) as max_id from PER_COMMAND ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$COM_ID = $data[max_id] + 1;

// คำสั่ง 23661
		if ($command=='COMMAND')
			$cmd = " SELECT a.GEVA_NO, GEVA_ORDER, GEVA_YEAR, GEVA_TITLE, GEVA_DATE, GEVA_ID, GEVA_PRENAME, GEVA_FNAME, 
												GEVA_LNAME, GEVA_OLDPOSNO, GEVA_OLDWPOS, GEVA_OLDMPOS, GEVA_OLDDEPT, GEVA_OLDC, GEVA_OLDSTEP, 
												GEVA_OLDTYPE, GEVA_OLDPOS, GEVA_QUALIFY, GEVA_NEWPOSNO, GEVA_NEWWPOS, GEVA_NEWMPOS, 
												GEVA_NEWDEPT, GEVA_NEWC, GEVA_NEWSTEP, GEVA_NEWTYPE, GEVA_NEWPOS, GEVA_EFFECT_DATE, 
												GEVA_SIGNDATE, GEVA_REMARK, GEVA_PSR, GEVA_ASB, GEVA_NEWLEVEL1, GEVA_NEWLEVEL2, GEVA_NEWLEVEL3, 
												GEVA_NEWLEVEL4, GEVA_NEWLEVEL5, GEVA_OLDLEVEL1, GEVA_OLDLEVEL2, GEVA_OLDLEVEL3, GEVA_OLDLEVEL4, 
												GEVA_OLDLEVEL5, GEVA_REFCMDDATE, GEVE_REFDATE, GEVA_EVADATE, GEVA_FOOTNOTE, GEVA_CASE
							FROM GOVPOS_J11_EVATRANS a, COMMON_HISTORY b
							WHERE a.GEVA_ID=b.ID
							ORDER BY a.GEVA_NO, GEVA_ORDER ";
		elseif ($command=='COMMAND_EMP')
			$cmd = " SELECT a.ID, EMPADDR_TYPE as GOVADDR_TYPE, EMPADDR_LINE1 as GOVADDR_LINE1, EMPADDR_LINE2 as GOVADDR_LINE2, 
							EMPTELEPHONE as GOVTELEPHONE, EMPZIP_CODE as GOVZIP_CODE, DIST_CODE, PROV_CODE
							FROM EMPADDRESS a, EMPLOYEE_HISTORY b
							WHERE a.ID=b.ID
							ORDER BY a.ID, EMPADDR_TYPE ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "<br>";
		$MAX_ID = 1;
		while($data = $db_dpis35->get_array()){
			$PER_COMMAND++;
			$COM_NO = trim($data[GEVA_NO]);
			$COM_NO = str_replace(" ", "", $COM_NO);
			$COM_NO = str_replace(" ", "", $COM_NO);
			$COM_NO = str_replace(" ", "", $COM_NO);
			$CMD_SEQ = trim($data[GEVA_ORDER]);
			$COM_NAME = trim($data[GEVA_TITLE]);
			$COM_YEAR = trim($data[GEVA_YEAR]);
			$GEVA_DATE = trim($data[GEVA_DATE]);
			$COM_DATE = (substr($GEVA_DATE,4,4) - 543) . "-" . substr($GEVA_DATE,2,2) . "-" . substr($GEVA_DATE,0,2);
			$PER_ID = trim($data[GEVA_ID]);
			$CMD_EDUCATE  = trim($data[GEVA_QUALIFY]);
			$GEVA_EFFECT_DATE = trim($data[GEVA_EFFECT_DATE]);
			$CMD_DATE = (substr($GEVA_EFFECT_DATE,4,4) - 543) . "-" . substr($GEVA_EFFECT_DATE,2,2) . "-" . substr($GEVA_EFFECT_DATE,0,2);
			$CMD_POSITION  = trim($data[GEVA_OLDWPOS]);
			$GEVA_OLDMPOS = trim($data[GEVA_OLDMPOS]);
			if ($GEVA_OLDMPOS) $CMD_POSITION = "$CMD_POSITION\|$GEVA_OLDMPOS";
			$CMD_LEVEL = $LEVEL_NO = "";
			$GEVA_OLDC = trim($data[GEVA_OLDC]);
			if ($GEVA_OLDC=="1") $CMD_LEVEL = "01";
			elseif ($GEVA_OLDC=="2") $CMD_LEVEL = "02";
			elseif ($GEVA_OLDC=="3") $CMD_LEVEL = "03";
			elseif ($GEVA_OLDC=="4") $CMD_LEVEL = "04";
			elseif ($GEVA_OLDC=="5") $CMD_LEVEL = "05";
			elseif ($GEVA_OLDC=="6") $CMD_LEVEL = "06";
			elseif ($GEVA_OLDC=="7") $CMD_LEVEL = "07";
			elseif ($GEVA_OLDC=="8") $CMD_LEVEL = "08";
			elseif ($GEVA_OLDC=="9") $CMD_LEVEL = "09";
			elseif ($GEVA_OLDC=="10") $CMD_LEVEL = "10";
			elseif ($GEVA_OLDC=="11") $CMD_LEVEL = "11";
			elseif ($GEVA_OLDC=="21") $CMD_LEVEL = "O1";
			elseif ($GEVA_OLDC=="22") $CMD_LEVEL = "O2";
			elseif ($GEVA_OLDC=="23") $CMD_LEVEL = "O3";
			elseif ($GEVA_OLDC=="24") $CMD_LEVEL = "O4";
			elseif ($GEVA_OLDC=="31") $CMD_LEVEL = "K1";
			elseif ($GEVA_OLDC=="32") $CMD_LEVEL = "K2";
			elseif ($GEVA_OLDC=="33") $CMD_LEVEL = "K3";
			elseif ($GEVA_OLDC=="34") $CMD_LEVEL = "K4";
			elseif ($GEVA_OLDC=="35") $CMD_LEVEL = "K5";
			elseif ($GEVA_OLDC=="41") $CMD_LEVEL = "D1";
			elseif ($GEVA_OLDC=="42") $CMD_LEVEL = "D2";
			elseif ($GEVA_OLDC=="51") $CMD_LEVEL = "M1";
			elseif ($GEVA_OLDC=="52") $CMD_LEVEL = "M2";
			elseif ($GEVA_OLDC && $GEVA_OLDC!="0") echo "ระดับตำแหน่ง $GEVA_OLDC<br>";
			$CMD_ORG1 = $SESS_MINISTRY_NAME;
			$CMD_ORG2 = $SESS_DEPARTMENT_NAME;
			$CMD_ORG3 = trim($data[GEVA_OLDLEVEL1]);
			$CMD_ORG4 = trim($data[GEVA_OLDLEVEL2]);
			$CMD_ORG5 = trim($data[GEVA_OLDLEVEL3]);
			$CMD_ORG6 = trim($data[GEVA_OLDLEVEL4]);
			$CMD_ORG7 = trim($data[GEVA_OLDLEVEL5]);
			if (!$CMD_ORG2 && $CMD_ORG3) {
				$CMD_ORG2 = $CMD_ORG3;
				$CMD_ORG3 = "";
			}
			if (!$CMD_ORG3 && $CMD_ORG4) {
				$CMD_ORG3 = $CMD_ORG4;
				$CMD_ORG4 = "";
			}
			if (!$CMD_ORG4 && $CMD_ORG5) {
				$CMD_ORG4 = $CMD_ORG5;
				$CMD_ORG5 = "";
			}
			if (!$CMD_ORG5 && $CMD_ORG6) {
				$CMD_ORG5 = $CMD_ORG6;
				$CMD_ORG6 = "";
			}
			if (!$CMD_ORG6 && $CMD_ORG7) {
				$CMD_ORG6 = $CMD_ORG7;
				$CMD_ORG7 = "";
			}

			$CMD_OLD_SALARY = trim($data[GEVA_OLDSTEP]);
			$CMD_SALARY = trim($data[GEVA_NEWSTEP]);
			
			$GEVA_NEWC = trim($data[GEVA_NEWC]);
			if ($GEVA_NEWC=="1") $LEVEL_NO = "01";
			elseif ($GEVA_NEWC=="2") $LEVEL_NO = "02";
			elseif ($GEVA_NEWC=="3") $LEVEL_NO = "03";
			elseif ($GEVA_NEWC=="4") $LEVEL_NO = "04";
			elseif ($GEVA_NEWC=="5") $LEVEL_NO = "05";
			elseif ($GEVA_NEWC=="6") $LEVEL_NO = "06";
			elseif ($GEVA_NEWC=="7") $LEVEL_NO = "07";
			elseif ($GEVA_NEWC=="8") $LEVEL_NO = "08";
			elseif ($GEVA_NEWC=="9") $LEVEL_NO = "09";
			elseif ($GEVA_NEWC=="10") $LEVEL_NO = "10";
			elseif ($GEVA_NEWC=="11") $LEVEL_NO = "11";
			elseif ($GEVA_NEWC=="21") $LEVEL_NO = "O1";
			elseif ($GEVA_NEWC=="22") $LEVEL_NO = "O2";
			elseif ($GEVA_NEWC=="23") $LEVEL_NO = "O3";
			elseif ($GEVA_NEWC=="24") $LEVEL_NO = "O4";
			elseif ($GEVA_NEWC=="31") $LEVEL_NO = "K1";
			elseif ($GEVA_NEWC=="32") $LEVEL_NO = "K2";
			elseif ($GEVA_NEWC=="33") $LEVEL_NO = "K3";
			elseif ($GEVA_NEWC=="34") $LEVEL_NO = "K4";
			elseif ($GEVA_NEWC=="35") $LEVEL_NO = "K5";
			elseif ($GEVA_NEWC=="41") $LEVEL_NO = "D1";
			elseif ($GEVA_NEWC=="42") $LEVEL_NO = "D2";
			elseif ($GEVA_NEWC=="51") $LEVEL_NO = "M1";
			elseif ($GEVA_NEWC=="52") $LEVEL_NO = "M2";
			elseif ($GEVA_NEWC && $GEVA_NEWC!="0") echo "ระดับตำแหน่ง $GEVA_NEWC<br>";
			$LEVEL_NO_POS = $LEVEL_NO;
			
			$CMD_POS_NO = trim($data[GEVA_NEWPOSNO]);
			$ORG_NAME_WORK = trim($data[GEVA_NEWDEPT]);
			$CMD_NOTE1 = trim($data[GEVA_REMARK]);
			
			$COM_NOTE = trim($data[GEVA_FOOTNOTE]);
			$COM_PER_TYPE = 1;
			$COM_TYPE = "5021";
			$MOV_CODE = "11520";
			$COM_CONFIRM = 1;
			$ORG_ID = 0;
			$COM_LEVEL_SALP = "NULL";
			$POS_ID = $POEM_ID = $POEMS_ID = $POT_ID = $PL_CODE = $PN_CODE = $EP_CODE = "NULL"; 
			$PL_CODE_ASSIGN = $PN_CODE_ASSIGN = $EP_CODE_ASSIGN = $PER_CARDNO = $CMD_NOW = "NULL";

			$cmd = " insert into PER_COMMAND (COM_ID, COM_NO, COM_NAME, COM_DATE, COM_NOTE, COM_PER_TYPE, 
							  COM_TYPE, COM_CONFIRM, COM_STATUS, DEPARTMENT_ID, ORG_ID, COM_YEAR, COM_DOC_TYPE, COM_LEVEL_SALP, 
							  UPDATE_USER, UPDATE_DATE) 
							 VALUES ($MAX_ID, '$COM_NO', '$COM_NAME', '$COM_DATE', '$COM_NOTE', $COM_PER_TYPE, 
							 '$COM_TYPE', $COM_CONFIRM, '', $SESS_DEPARTMENT_ID, $ORG_ID, '$COM_YEAR', '$COM_DOC_TYPE', $COM_LEVEL_SALP, 
							 $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);

			$cmd1 = " SELECT COM_ID FROM PER_COMMAND WHERE COM_ID = $MAX_ID "; 
			$count_data = $db_dpis1->send_cmd($cmd1);
			if ($count_data) {
				$cmd = " insert into PER_COMDTL (COM_ID, CMD_SEQ, PER_ID, CMD_EDUCATE, CMD_DATE, CMD_POSITION, 
								CMD_LEVEL, CMD_ORG1, CMD_ORG2, CMD_ORG3, CMD_ORG4, CMD_ORG5, CMD_ORG6, CMD_ORG7, 
								CMD_ORG8, CMD_OLD_SALARY, PL_CODE, PN_CODE, EP_CODE, CMD_AC_NO, CMD_ACCOUNT, 
								POS_ID, POEM_ID, POEMS_ID, POT_ID, LEVEL_NO, CMD_SALARY, CMD_SPSALARY, PL_CODE_ASSIGN, 
								PN_CODE_ASSIGN, EP_CODE_ASSIGN, CMD_NOTE1, CMD_NOTE2, MOV_CODE, CMD_DATE2, 
								CMD_SAL_CONFIRM, PER_CARDNO, UPDATE_USER, UPDATE_DATE, PL_NAME_WORK, 
								ORG_NAME_WORK, CMD_LEVEL_POS, PM_CODE, CMD_POS_NO_NAME, CMD_POS_NO, 
								CMD_ES_CODE, ES_CODE, CMD_NOW)
								values ($MAX_ID, $CMD_SEQ, $PER_ID, '$CMD_EDUCATE', '$CMD_DATE', '$CMD_POSITION', 
								'$CMD_LEVEL', '$CMD_ORG1', '$CMD_ORG2', '$CMD_ORG3', '$CMD_ORG4', '$CMD_ORG5', 
								'$CMD_ORG6', '$CMD_ORG7', '$CMD_ORG8', $CMD_OLD_SALARY, $PL_CODE, $PN_CODE, 
								$EP_CODE, '$CMD_AC_NO', '$CMD_ACCOUNT', $POS_ID, $POEM_ID, $POEMS_ID, $POT_ID, '$LEVEL_NO', 
								$CMD_SALARY, 0, $PL_CODE_ASSIGN, $PN_CODE_ASSIGN, $EP_CODE_ASSIGN, '$CMD_NOTE1', 
								'$CMD_NOTE2', '$MOV_CODE', '$CMD_DATE2',  0, $PER_CARDNO, $UPDATE_USER, '$UPDATE_DATE', 
								'$PL_NAME_WORK', '$ORG_NAME_WORK', '$LEVEL_NO_POS', '$POS_PM_CODE', '$CMD_POS_NO_NAME', 
								'$CMD_POS_NO', '$CMD_ES_CODE', '$ES_CODE', $CMD_NOW) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd1 = " SELECT COM_ID FROM PER_COMDTL WHERE COM_ID = $MAX_ID "; 
				$count_data = $db_dpis1->send_cmd($cmd1);
				if (!$count_data) {
					echo "$cmd<br>==================<br>";
					$db_dpis->show_error();
					echo "<br>end ". ++$i  ."=======================<br>";
				}
				$MAX_ID++;
			} else {
				echo "$cmd<br>==================<br>";
				$db_dpis->show_error();
				echo "<br>end ". ++$i  ."=======================<br>";
			}  
		} // end while						

		$cmd = " select count(COM_ID) as COUNT_NEW from PER_COMMAND ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_COMMAND - $PER_COMMAND - $COUNT_NEW<br>";

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();
		$db_dpis351->free_result();
	} // end if

?>