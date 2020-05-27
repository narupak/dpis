<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

// เลขที่ตำแหน่งครอง 2 คน select pos_id, count(*) from per_personal where per_type=1 and per_status=1 group by pos_id having count(*) > 1

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis351 = new connect_dpis35($dpis35db_host, $dpis35db_name, $dpis35db_user, $dpis35db_pwd);
	$UPDATE_DATE = date("Y-m-d H:i:s");
	$UPDATE_USER = 99999;

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

		$cmd = " DELETE FROM PER_POS_EMPSER ";
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

		$cmd = " UPDATE พนักงาน SET [กอง/สำนัก] = 'สำนักการสาธารณสุขและสิ่งแวดล้อม' WHERE [กอง/สำนัก] = 'สำนักการสาธารณสุขและสิ่งแลดล้อม' OR [กอง/สำนัก] = 'สำนักการสาธารณสุขและสิงแวดล้อม' ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();

		$cmd = " UPDATE พนักงาน SET [ส่วน] = 'บริการสาธารณสุขและสิ่งแวดล้อม' WHERE [ส่วน] = 'บริการสาธารณสุขและสิงแวดล้อม' ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();

		$cmd = " UPDATE พนักงาน SET [ส่วน] = 'ส่งเสริมสาธารณสุขและสิ่งแวดล้อม' WHERE [ส่วน] = 'ส่งเสริมสาธารณสุขและสิงแวดล้อม' ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();

		$cmd = " UPDATE พนักงาน SET [ฝ่าย] = 'ส่งเสริมสาธารณสุขและสิ่งแวดล้อม' WHERE [ฝ่าย] = 'ส่งเสริมสาธารณสุขและสิงแวดล้อม' ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();

		$cmd = " SELECT DISTINCT [กอง/สำนัก] AS DEPT_NAME FROM พนักงาน ";
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
			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = '$DEPT_SUBOF' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$ORG_ID_REF = $data2[NEW_CODE];
			if ($ORG_ID_REF) $OL_CODE = "04";
			else $ORG_ID_REF = $SESS_DEPARTMENT_ID;

			$OT_CODE = "01"; 
			$AP_CODE = "NULL";
			if (!$TF_PROV || $TF_PROV=="0") $TF_PROV = "1200";
			else $TF_PROV .= "00";
			$CT_CODE = "140";
			$ORG_ACTIVE = 1;
			if (!$DEPT_ORDER) $DEPT_ORDER = "NULL";

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
							VALUES ('PER_ORG', '$DEPT_NAME', '$MAX_ID', $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$MAX_ID++;
			$PER_ORG2++;

		} // end while						

		$cmd = " select count(ORG_ID) as COUNT_NEW from PER_ORG where DEPARTMENT_ID = $SESS_DEPARTMENT_ID and OL_CODE = '03' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_ORG - $PER_ORG2 - $COUNT_NEW<br>";

		$cmd = " SELECT DISTINCT [กอง/สำนัก] AS ORG_NAME, [ส่วน] AS ORG_NAME_1 FROM พนักงาน ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		//echo "<br>";
		while($data = $db_dpis35->get_array()){
			$ORG_NAME = trim($data[ORG_NAME]);
			$SECTION_CODE = trim($data[SECTION_CODE]);
			$ORG_NAME_1 = trim($data[ORG_NAME_1]);
			$SECTION_SUBOF = trim($data[SECTION_SUBOF]);
			$SECTION_ORDER = trim($data[SECTION_ORDER]);
			$SECTION_OLDNEWFLAG = trim($data[SECTION_OLDNEWFLAG]);
			$CHANGE_FROM = trim($data[CHANGE_FROM]);
			$SECTION_INOUTFLAG = trim($data[SECTION_INOUTFLAG]);
			$DATE_UPDATE_SECTION = trim($data[DATE_UPDATE_SECTION]);
			$DATE_DELETE_SECTION = trim($data[DATE_DELETE_SECTION]);

			$OL_CODE = "04"; 
			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = '$ORG_NAME' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$ORG_ID_REF = $data2[NEW_CODE];

			$cmd = " select PV_CODE from PER_ORG where ORG_ID = $ORG_ID_REF ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PV_CODE = trim($data2[PV_CODE]);

			$OT_CODE = "01"; 
			$AP_CODE = "NULL";
			$CT_CODE = "140";
			$ORG_ACTIVE = 1;
			if (!$ORG_NAME_1) $ORG_NAME_1 = "-";

			$cmd = " INSERT INTO PER_ORG (ORG_ID, ORG_CODE, ORG_NAME, ORG_SHORT, OL_CODE, OT_CODE,  
							AP_CODE, PV_CODE, CT_CODE, ORG_ID_REF, ORG_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							DEPARTMENT_ID, ORG_ENG_NAME)
							VALUES ($MAX_ID, '$SECTION_CODE', '$ORG_NAME_1', NULL, '$OL_CODE', '$OT_CODE',  
							$AP_CODE, '$PV_CODE', '$CT_CODE', $ORG_ID_REF, $ORG_ACTIVE, $UPDATE_USER, '$UPDATE_DATE', 
							$SESS_DEPARTMENT_ID, '$SECTION_NAME_E') ";
			$db_dpis->send_cmd($cmd);

			$cmd1 = " SELECT ORG_ID FROM PER_ORG WHERE ORG_ID = $MAX_ID "; 
			$count_data = $db_dpis1->send_cmd($cmd1);
			if (!$count_data) {
				echo "$cmd<br>==================<br>";
				$db_dpis->show_error();
				echo "<br>end ". ++$i  ."=======================<br>";
			}

			$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
							VALUES ('PER_ORG', '$ORG_NAME$ORG_NAME_1', '$MAX_ID', $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$MAX_ID++;
			$PER_ORG3++;

		} // end while						

		$cmd = " select count(ORG_ID) as COUNT_NEW from PER_ORG where DEPARTMENT_ID = $SESS_DEPARTMENT_ID and OL_CODE = '04' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_ORG - $PER_ORG3 - $COUNT_NEW<br>";

		$cmd = " SELECT DISTINCT [กอง/สำนัก] AS ORG_NAME, [ส่วน] AS ORG_NAME_1, [ฝ่าย] AS ORG_NAME_2 FROM พนักงาน ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		//echo "<br>";
		while($data = $db_dpis35->get_array()){
			$ORG_NAME = trim($data[ORG_NAME]);
			$SECTION_CODE = trim($data[SECTION_CODE]);
			$ORG_NAME_1 = trim($data[ORG_NAME_1]);
			$ORG_NAME_2 = trim($data[ORG_NAME_2]);
			$SECTION_ORDER = trim($data[SECTION_ORDER]);
			$SECTION_OLDNEWFLAG = trim($data[SECTION_OLDNEWFLAG]);
			$CHANGE_FROM = trim($data[CHANGE_FROM]);
			$SECTION_INOUTFLAG = trim($data[SECTION_INOUTFLAG]);
			$DATE_UPDATE_SECTION = trim($data[DATE_UPDATE_SECTION]);
			$DATE_DELETE_SECTION = trim($data[DATE_DELETE_SECTION]);

			$OL_CODE = "05"; 
			if (!$ORG_NAME_1) $ORG_NAME_1 = "-";
			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = '$ORG_NAME$ORG_NAME_1' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$ORG_ID_REF = $data2[NEW_CODE];

			$cmd = " select PV_CODE from PER_ORG where ORG_ID = $ORG_ID_REF ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PV_CODE = trim($data2[PV_CODE]);

			$OT_CODE = "01"; 
			$AP_CODE = "NULL";
			$CT_CODE = "140";
			$ORG_ACTIVE = 1;
			if (!$ORG_NAME_2) $ORG_NAME_2 = "-";

			$cmd = " INSERT INTO PER_ORG (ORG_ID, ORG_CODE, ORG_NAME, ORG_SHORT, OL_CODE, OT_CODE,  
							AP_CODE, PV_CODE, CT_CODE, ORG_ID_REF, ORG_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							DEPARTMENT_ID, ORG_ENG_NAME)
							VALUES ($MAX_ID, '$SECTION_CODE', '$ORG_NAME_2', NULL, '$OL_CODE', '$OT_CODE',  
							$AP_CODE, '$PV_CODE', '$CT_CODE', $ORG_ID_REF, $ORG_ACTIVE, $UPDATE_USER, '$UPDATE_DATE', 
							$SESS_DEPARTMENT_ID, '$SECTION_NAME_E') ";
			$db_dpis->send_cmd($cmd);

			$cmd1 = " SELECT ORG_ID FROM PER_ORG WHERE ORG_ID = $MAX_ID "; 
			$count_data = $db_dpis1->send_cmd($cmd1);
			if (!$count_data) {
				echo "$cmd<br>==================<br>";
				$db_dpis->show_error();
				echo "<br>end ". ++$i  ."=======================<br>";
			}

			$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
							VALUES ('PER_ORG', '$ORG_NAME$ORG_NAME_1$ORG_NAME_2', '$MAX_ID', $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$MAX_ID++;
			$PER_ORG4++;

		} // end while						

		$cmd = " select count(ORG_ID) as COUNT_NEW from PER_ORG where DEPARTMENT_ID = $SESS_DEPARTMENT_ID and OL_CODE = '05' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_ORG - $PER_ORG4 - $COUNT_NEW<br>";

		$cmd = " SELECT DISTINCT [กอง/สำนัก] AS ORG_NAME, [ส่วน] AS ORG_NAME_1, [ฝ่าย] AS ORG_NAME_2, [กลุ่มงาน] AS ORG_NAME_3 FROM พนักงาน ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		//echo "<br>";
		while($data = $db_dpis35->get_array()){
			$ORG_NAME = trim($data[ORG_NAME]);
			$SECTION_CODE = trim($data[SECTION_CODE]);
			$ORG_NAME_1 = trim($data[ORG_NAME_1]);
			$ORG_NAME_2 = trim($data[ORG_NAME_2]);
			$ORG_NAME_3 = trim($data[ORG_NAME_3]);
			$SECTION_OLDNEWFLAG = trim($data[SECTION_OLDNEWFLAG]);
			$CHANGE_FROM = trim($data[CHANGE_FROM]);
			$SECTION_INOUTFLAG = trim($data[SECTION_INOUTFLAG]);
			$DATE_UPDATE_SECTION = trim($data[DATE_UPDATE_SECTION]);
			$DATE_DELETE_SECTION = trim($data[DATE_DELETE_SECTION]);

			$OL_CODE = "06"; 
			if (!$ORG_NAME_1) $ORG_NAME_1 = "-";
			if (!$ORG_NAME_2) $ORG_NAME_2 = "-";
			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = '$ORG_NAME$ORG_NAME_1$ORG_NAME_2' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$ORG_ID_REF = $data2[NEW_CODE];

			$cmd = " select PV_CODE from PER_ORG where ORG_ID = $ORG_ID_REF ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PV_CODE = trim($data2[PV_CODE]);

			$OT_CODE = "01"; 
			$AP_CODE = "NULL";
			$CT_CODE = "140";
			$ORG_ACTIVE = 1;
			if (!$ORG_NAME_3) $ORG_NAME_3 = "-";

			$cmd = " INSERT INTO PER_ORG (ORG_ID, ORG_CODE, ORG_NAME, ORG_SHORT, OL_CODE, OT_CODE,  
							AP_CODE, PV_CODE, CT_CODE, ORG_ID_REF, ORG_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							DEPARTMENT_ID, ORG_ENG_NAME)
							VALUES ($MAX_ID, '$SECTION_CODE', '$ORG_NAME_3', NULL, '$OL_CODE', '$OT_CODE',  
							$AP_CODE, '$PV_CODE', '$CT_CODE', $ORG_ID_REF, $ORG_ACTIVE, $UPDATE_USER, '$UPDATE_DATE', 
							$SESS_DEPARTMENT_ID, '$SECTION_NAME_E') ";
			$db_dpis->send_cmd($cmd);

			$cmd1 = " SELECT ORG_ID FROM PER_ORG WHERE ORG_ID = $MAX_ID "; 
			$count_data = $db_dpis1->send_cmd($cmd1);
			if (!$count_data) {
				echo "$cmd<br>==================<br>";
				$db_dpis->show_error();
				echo "<br>end ". ++$i  ."=======================<br>";
			}

			$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
							VALUES ('PER_ORG', '$ORG_NAME_1$ORG_NAME_2$ORG_NAME_3', '$MAX_ID', $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$MAX_ID++;
			$PER_ORG5++;

		} // end while						

		$cmd = " select count(ORG_ID) as COUNT_NEW from PER_ORG where DEPARTMENT_ID = $SESS_DEPARTMENT_ID and OL_CODE = '06' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_ORG - $PER_ORG5 - $COUNT_NEW<br>";

		$cmd = " SELECT DISTINCT [กอง/สำนัก] AS ORG_NAME, [ส่วน] AS ORG_NAME_1, [ฝ่าย] AS ORG_NAME_2, [กลุ่มงาน] AS ORG_NAME_3, [งาน] AS ORG_NAME_4 FROM พนักงาน ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		//echo "<br>";
		while($data = $db_dpis35->get_array()){
			$ORG_NAME = trim($data[ORG_NAME]);
			$ORG_NAME_1 = trim($data[ORG_NAME_1]);
			$ORG_NAME_2 = trim($data[ORG_NAME_2]);
			$ORG_NAME_3 = trim($data[ORG_NAME_3]);
			$ORG_NAME_4 = trim($data[ORG_NAME_4]);

			$OL_CODE = "07"; 
			if (!$ORG_NAME_1) $ORG_NAME_1 = "-";
			if (!$ORG_NAME_2) $ORG_NAME_2 = "-";
			if (!$ORG_NAME_3) $ORG_NAME_3 = "-";
			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = '$ORG_NAME_1$ORG_NAME_2$ORG_NAME_3' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$ORG_ID_REF = $data2[NEW_CODE];

			$cmd = " select PV_CODE from PER_ORG where ORG_ID = $ORG_ID_REF ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PV_CODE = trim($data2[PV_CODE]);

			$OT_CODE = "01"; 
			$AP_CODE = "NULL";
			$CT_CODE = "140";
			$ORG_ACTIVE = 1;
			if (!$ORG_NAME_4) $ORG_NAME_4 = "-";

			$cmd = " INSERT INTO PER_ORG (ORG_ID, ORG_CODE, ORG_NAME, ORG_SHORT, OL_CODE, OT_CODE,  
							AP_CODE, PV_CODE, CT_CODE, ORG_ID_REF, ORG_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							DEPARTMENT_ID, ORG_ENG_NAME)
							VALUES ($MAX_ID, '$SECTION_CODE', '$ORG_NAME_4', NULL, '$OL_CODE', '$OT_CODE',  
							$AP_CODE, '$PV_CODE', '$CT_CODE', $ORG_ID_REF, $ORG_ACTIVE, $UPDATE_USER, '$UPDATE_DATE', 
							$SESS_DEPARTMENT_ID, '$SECTION_NAME_E') ";
			$db_dpis->send_cmd($cmd);

			$cmd1 = " SELECT ORG_ID FROM PER_ORG WHERE ORG_ID = $MAX_ID "; 
			$count_data = $db_dpis1->send_cmd($cmd1);
			if (!$count_data) {
				echo "$cmd<br>==================<br>";
				$db_dpis->show_error();
				echo "<br>end ". ++$i  ."=======================<br>";
			}

			$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
							VALUES ('PER_ORG', '$ORG_NAME_2$ORG_NAME_3$ORG_NAME_4', '$MAX_ID', $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$MAX_ID++;
			$PER_ORG6++;

		} // end while						

		$cmd = " select count(ORG_ID) as COUNT_NEW from PER_ORG where DEPARTMENT_ID = $SESS_DEPARTMENT_ID and OL_CODE = '07' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_ORG - $PER_ORG6 - $COUNT_NEW<br>";

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
			elseif ($table[$i]=="per_personal")
				$cmd = " delete from $table[$i] where per_type = 1 ";
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

		$cmd = " UPDATE พนักงาน SET [ตำแหน่ง] = 'เจ้าหน้าที่ธุรการ' WHERE [ตำแหน่ง] = 'เจ้าหน้าที่ธูรการ' ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();

		$cmd = " UPDATE พนักงาน SET [ตำแหน่ง] = 'เจ้าหน้าที่ป้องกันและบรรเทาสาธารณภัย' WHERE [ตำแหน่ง] = 'เจ้าหน้าที่ป้องกันและบรรเทา      สาธารณภัย' or 
						[ตำแหน่ง] = 'เจ้าหน้าที่ป้องกันและบรรเทา     สาธารณภัย' or [ตำแหน่ง] = 'เจ้าหน้าที่ป้องกันและ             บรรเทาสาธารณภัย' or
						[ตำแหน่ง] = 'เจ้าหน้าที่ป้องกันและบรรเทา        สาธารณภัย' ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();

		$cmd = " UPDATE พนักงาน SET [ตำแหน่ง] = 'เจ้าพนักงานป้องกันและบรรเทาสาธารณภัย' WHERE [ตำแหน่ง] = 'เจ้าพนักงานป้องกันและ      บรรเทาสาธารณภัย' or 
						[ตำแหน่ง] = 'เจ้าพนักงานป้องกันและบรรเทา   สาธารณภัย' or [ตำแหน่ง] = 'เจ้าพนักงานป้องกันและบรรเทา    สาธารณภัย' ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();

		$cmd = " UPDATE พนักงาน SET [ตำแหน่ง] = 'วิศวกรเครื่องกล' WHERE [ตำแหน่ง] = 'วิศวเครื่องกล' ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();

		$cmd = " UPDATE พนักงาน SET [ตำแหน่ง] = 'นักวิชาการเงินและบัญชี' WHERE [ตำแหน่ง] = 'นักวิชาการและบัญชี' or [ตำแหน่ง] = 'นักวิชาการการเงินและบัญชี' ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();

		$POS_ID = 1;

		$cmd = " SELECT [เลขที่ตำแหน่ง] AS POS_NO, [ตำแหน่ง] AS PL_NAME, [อัตราเงินเดือน] AS POS_SALARY, [ระดับ] AS LEVEL_NAME, 
						[กอง/สำนัก] AS ORG_NAME, [ส่วน] AS ORG_NAME_1, [ฝ่าย] AS ORG_NAME_2, [กลุ่มงาน] AS ORG_NAME_3, [งาน] AS ORG_NAME_4 
						FROM พนักงาน 
						ORDER BY [เลขที่ตำแหน่ง] ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "$cmd<br>";
		while($data = $db_dpis35->get_array()){
			$PER_POSITION++;
			$POS_NO = trim($data[POS_NO]);
			$POS_NO = str_replace("29.", "9", $POS_NO);
			if ($POS_NO=="ข 0001") $POS_NO = "0001";
			$PL_NAME = trim($data[PL_NAME]);
			$POS_SALARY = trim($data[POS_SALARY]);
			$LEVEL_NAME = trim($data[LEVEL_NAME]);
			$ORG_NAME = trim($data[ORG_NAME]);
			$ORG_NAME_1 = trim($data[ORG_NAME_1]);
			$ORG_NAME_2 = trim($data[ORG_NAME_2]);
			$ORG_NAME_3 = trim($data[ORG_NAME_3]);
			$ORG_NAME_4 = trim($data[ORG_NAME_4]);
			if (!$ORG_NAME_1) $ORG_NAME_1 = "-";
			if (!$ORG_NAME_2) $ORG_NAME_2 = "-";
			if (!$ORG_NAME_3) $ORG_NAME_3 = "-";
			if (!$ORG_NAME_4) $ORG_NAME_4 = "-";
			$LEVEL_NO = $PT_CODE = "";
			if ($LEVEL_NAME=="1") {
				$LEVEL_NO = "01";
				$PT_CODE = "11";
			} elseif ($LEVEL_NAME=="2") {
				$LEVEL_NO = "02";
				$PT_CODE = "11";
			} elseif ($LEVEL_NAME=="3") {
				$LEVEL_NO = "03";
				$PT_CODE = "11";
			} elseif ($LEVEL_NAME=="4") {
				$LEVEL_NO = "04";
				$PT_CODE = "11";
			} elseif ($LEVEL_NAME=="5") {
				$LEVEL_NO = "05";
				$PT_CODE = "11";
			} elseif ($LEVEL_NAME=="6") {
				$LEVEL_NO = "06";
				$PT_CODE = "11";
			} elseif ($LEVEL_NAME=="6ว" || $LEVEL_NAME=="6 ว") {
				$LEVEL_NO = "06";
				$PT_CODE = "12";
			} elseif ($LEVEL_NAME=="7") {
				$LEVEL_NO = "07";
				$PT_CODE = "11";
			} elseif ($LEVEL_NAME=="7ว" || $LEVEL_NAME=="7 ว") {
				$LEVEL_NO = "07";
				$PT_CODE = "12";
			} elseif ($LEVEL_NAME=="7วช" || $LEVEL_NAME=="7 วช") {
				$LEVEL_NO = "07";
				$PT_CODE = "21";
			} elseif ($LEVEL_NAME=="8") {
				$LEVEL_NO = "08";
				$PT_CODE = "11";
			} elseif ($LEVEL_NAME=="8วช" || $LEVEL_NAME=="8 วช") {
				$LEVEL_NO = "08";
				$PT_CODE = "21";
			} elseif ($LEVEL_NAME=="9") {
				$LEVEL_NO = "09";
				$PT_CODE = "11";
			} 	elseif ($LEVEL_NAME=="คศ.2") {
				$LEVEL_NO = "xx";
				$PT_CODE = "11";
			} elseif ($LEVEL_NAME=="คศ.3") {
				$LEVEL_NO = "xx";
				$PT_CODE = "11";
			} else echo "ระดับตำแหน่ง $LEVEL_NAME<br>";

			$OT_CODE = '01';
			$POS_STATUS = 1;

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = '$ORG_NAME' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$ORG_ID = $data2[NEW_CODE];

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = '$ORG_NAME$ORG_NAME_1' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$ORG_ID_1 = $data2[NEW_CODE];

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = '$ORG_NAME_1$ORG_NAME_2' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$ORG_ID_2 = $data2[NEW_CODE];

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = '$ORG_NAME_1$ORG_NAME_2$ORG_NAME_3' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$ORG_ID_3 = $data2[NEW_CODE];

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = '$ORG_NAME_2$ORG_NAME_3$ORG_NAME_4' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$ORG_ID_4 = $data2[NEW_CODE];

			$cmd = " select PL_CODE from PER_LINE where PL_NAME = '$PL_NAME' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PL_CODE = $data2[PL_CODE];
			$PM_CODE = "";
			if (!$PL_CODE) {
				if (strpos($PL_NAME,"หัวหน้าสำนัก") !== false) $PM_CODE = "0352";
				elseif (strpos($PL_NAME,"หัวหน้าฝ่าย") !== false || strpos($PL_NAME,"หัวน้าฝ่าย") !== false) $PM_CODE = "0340";
				elseif (strpos($PL_NAME,"หัหวน้ากลุ่มงาน") !== false || strpos($PL_NAME,"หัวหน้ากลุ่มงาน") !== false) $PM_CODE = "0353";
				elseif (strpos($PL_NAME,"หัวหน้าแขวง") !== false) $PM_CODE = "0349";
				elseif (strpos($PL_NAME,"รองผู้อำนวยการสำนัก") !== false) $PM_CODE = "0239";
				elseif (strpos($PL_NAME,"ผู้อำนวยการสำนัก") !== false) $PM_CODE = "0251";
				elseif (strpos($PL_NAME,"ผู้อำนวยการส่วน") !== false) $PM_CODE = "0250";
				elseif (strpos($PL_NAME,"ผู้อำนวยการกอง") !== false) $PM_CODE = "0235";

				if ($PL_NAME=="หัวหน้าสำนักปลัดเทศบาล       (นักบริหารงานทั่วไป)") $PL_CODE = "010109";
				elseif ($PL_NAME=="หัวหน้าฝ่ายป้องกันและบรรเทา    สาธารณภัย") $PL_CODE = "010109";
				elseif ($PL_NAME=="หัวหน้าฝ่ายรักษาความสงบเรียบร้อยและความมั่นคง                    (นักบริหารงานทั่วไป)") $PL_CODE = "010109";
				elseif ($PL_NAME=="หัวหน้าฝ่ายทะเบียนและบัตรประจำตัวประชาชน                 (นักบริหารงานทั่วไป)") $PL_CODE = "010109";
				elseif ($PL_NAME=="หัวหน้าฝ่ายบริหารงานทั่วไป     (นักบริหารงานทั่วไป)") $PL_CODE = "010109";
				elseif ($PL_NAME=="หัวหน้าฝ่ายบริหารงานทั่วไป     (นักบริหารทั่วไป)") $PL_CODE = "010109";
				elseif ($PL_NAME=="ผู้อำนวยการสำนักการคลัง       (นักบริหารงานการคลัง)") $PL_CODE = "010110";
				elseif ($PL_NAME=="ผู้อำนวยการส่วนบริหารการคลัง   (นักบริหารงานการคลัง)") $PL_CODE = "010110";
				elseif ($PL_NAME=="หัวหน้าฝ่ายการเงินและบัญชี     (นักบริหารงานการคลัง)") $PL_CODE = "010110";
				elseif ($PL_NAME=="หัวหน้าฝ่ายระเบียบการคลัง      (นักบริหารงานการคลัง)") $PL_CODE = "010110";
				elseif ($PL_NAME=="หัวหน้าฝ่ายพัสดุและทรัพย์สิน    (นักบริหารงานการคลัง)") $PL_CODE = "010110";
				elseif ($PL_NAME=="หัวหน้าฝ่ายพัฒนารายได้         (นักบริหารการคลัง)") $PL_CODE = "010110";
				elseif ($PL_NAME=="หัวหน้าฝ่ายผลประโยชน์และกิจการพาณิชย์  (นักบริหารงานการคลัง)") $PL_CODE = "010110";
				elseif ($PL_NAME=="ผู้อำนวยการสำนักการช่าง        (นักบริหารงานช่าง)") $PL_CODE = "010111";
				elseif ($PL_NAME=="ผู้อำนวยการส่วนควบคุมอาคารและผังเมือง  (นักบริหารงานช่าง)") $PL_CODE = "010111";
				elseif ($PL_NAME=="ผู้อำนวยการส่วนควบคุมการก่อสร้าง (นักบริหารงานช่าง)") $PL_CODE = "010111";
				elseif ($PL_NAME=="ผู้อำนวยการส่วนการโยธาและสุขาภิบาล  (นักบริหารงานช่าง)") $PL_CODE = "010111";
				elseif ($PL_NAME=="หัวหน้าฝ่ายจัดการคุณภาพน้ำ     (นักบริหารงานช่างสุขาภิบาล)") $PL_CODE = "010112";
				elseif ($PL_NAME=="ผู้อำนวยการสำนัการสาธารณสุขและสิ่งแวดล้อม                   (นักบริหารงานสาธารณสุข)") $PL_CODE = "010113";
				elseif ($PL_NAME=="ผู้อำนวยการสำนักการสาธารณสุขและสิ่งแวดล้อม (นักบริหารงานสาธารณสุข)") $PL_CODE = "010113";
				elseif ($PL_NAME=="ผู้อำนวยการส่วนส่งเสริมสาธารณสุขและสิ่งแวดล้อม                   (นักบริหารงานสาธารณสุขและ    สิ่งแวดล้อม)") $PL_CODE = "010114";
				elseif ($PL_NAME=="หัวหน้าฝ่ายส่งเสริมสาธารณสุขและสิงแวดล้อม   (นักบริหารงาน สาธารณสุข)") $PL_CODE = "010113";
				elseif ($PL_NAME=="ผู้อำนวยการส่วนบริการสาธารณสุขและสิ่งแวดล้อม                   (นักบริหารงานสาธารณสุข)") $PL_CODE = "010113";
				elseif ($PL_NAME=="หัวหน้าฝ่ายบริการสิ่งแวดล้อม   (นักบริหารงานสาธารณสุข)") $PL_CODE = "010113";
				elseif ($PL_NAME=="หัหวน้ากลุ่มงานศูนย์บริการสาธารณสุข  (นายเพทย์)") $PL_CODE = "060104";
				elseif ($PL_NAME=="หัวหน้ากลุ่มงานทันตสาธารณสุข  (ทันตแพทย์)") $PL_CODE = "060204";
				elseif ($PL_NAME=="ทันตสาธารณสุข") $PL_CODE = "062802";
				elseif ($PL_NAME=="หัวหน้ากลุ่มงานบริการสัตวแพทย์  (นายสัตวแพทย์)") $PL_CODE = "060304";
				elseif ($PL_NAME=="ผู้อำนวยการกองวิชาการและแผนงาน   (นักบริหารงานทั่วไป 8)") $PL_CODE = "010109";
				elseif ($PL_NAME=="หัวหน้าฝ่ายแผนงานและงบประมาณ(นักบริหารงานทั่วไป )") $PL_CODE = "010109";
				elseif ($PL_NAME=="หัวหน้าฝ่ายบริการและเผยแพร่วิชาการ   (นักบริหารงานทั่วไป )") $PL_CODE = "010109";
				elseif ($PL_NAME=="ผู้อำนวยการสำนักการศึกษา     (นักบริหารการศึกษา)") $PL_CODE = "010115";
				elseif ($PL_NAME=="รองผู้อำนวยการสำนักการศึกษา (นักบริหารการศึกษา)") $PL_CODE = "010115";
				elseif ($PL_NAME=="หัวหน้าฝ่ายกิจการโรงเรียน       (นักบริหารการศึกษา)") $PL_CODE = "010115";
				elseif ($PL_NAME=="หัวหน้าฝ่ายวิชาการ               (นักบริหารการศึกษา)") $PL_CODE = "010115";
				elseif ($PL_NAME=="หัวน้าฝ่ายกิจการเด็กและเยาวชน (นักบริหารการศึกษา)") $PL_CODE = "010115";
				elseif ($PL_NAME=="ศึกษานิเทศก์ชำนาญการ") $PL_CODE = "092320";
				elseif ($PL_NAME=="ศึกษานิเทศก์ชำนาญการพิเศษ  (หัวหน้าหน่วยศึกษานิเทศก์)") $PL_CODE = "092320";
				elseif ($PL_NAME=="หัวหน้าฝ่ายสังคมสงเคราะห์      (นักบริหารงานสวัสดิการสังคม)") $PL_CODE = "010116";
				elseif ($PL_NAME=="หัวหน้าฝ่ายพัฒนาชุมชน         (นักบริหารงานสวัสดิการสังคม)") $PL_CODE = "010116";
				elseif ($PL_NAME=="หัวหน้าแขวงท่าทราย             (นักบริหารงานทั่วไป)") $PL_CODE = "010109";
				elseif ($PL_NAME=="หัวหน้าฝ่ายทะเบียนและบัตรประจำตัวประชาชน                (นักบริหารงานทั่วไป)") $PL_CODE = "010109";
				elseif ($PL_NAME=="หัวหน้าฝ่ายการคลัง               (นักบริหารงานการคลัง)") $PL_CODE = "010110";
				elseif ($PL_NAME=="เจ้าหน้าที่บริหารงานทะเบียนและบัตรประจำตัวประชาชน" || $PL_NAME=="010215003") $PL_CODE = "013701";
				elseif ($PL_NAME=="หัวหน้าแขวงสวนใหญ่            (นักบริหารงานทั่วไป)") $PL_CODE = "010109";
				else echo "ตำแหน่งในสายงาน $PL_NAME<br>";
			}
	
			$PM_CODE = trim($PM_CODE)? "'".$PM_CODE."'" : "NULL";
			if (!$ORG_ID) $ORG_ID = 1;
			if (!$ORG_ID_1) $ORG_ID_1 = "NULL";
			if (!$ORG_ID_2) $ORG_ID_2 = "NULL";
			if (!$ORG_ID_3) $ORG_ID_3 = "NULL";
			if (!$ORG_ID_4) $ORG_ID_4 = "NULL";
			if (!$POS_SALARY) $POS_SALARY = 0;
			$POS_MGTSALARY = 0;

			$cmd = " INSERT INTO PER_POSITION (POS_ID, ORG_ID, POS_NO, OT_CODE, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, PM_CODE, PL_CODE, 
							CL_NAME, POS_SALARY, POS_MGTSALARY, PT_CODE, POS_CONDITION, POS_REMARK, POS_DATE, 
							POS_GET_DATE, POS_CHANGE_DATE, POS_STATUS, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID, 
							LEVEL_NO)
							VALUES ($POS_ID, $ORG_ID, '$POS_NO', '$OT_CODE', $ORG_ID_1, $ORG_ID_2, $ORG_ID_4, $ORG_ID_4, $PM_CODE, '$PL_CODE', 
							'$CL_NAME', $POS_SALARY, $POS_MGTSALARY, '$PT_CODE', '$POS_CONDITION', '$POS_REMARK', 
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
			elseif ($table[$i]=="per_personal")
				$cmd = " delete from $table[$i] where per_type = 2 ";
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

		$cmd = " UPDATE ลูกจ้างประจำ SET [กอง/สำนัก] = 'สำนักการสาธารณสุขและสิ่งแวดล้อม' WHERE [กอง/สำนัก] = 'นักการสาธารณสุขและสิ่งแวดล้อม' ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();

		$cmd = " UPDATE ลูกจ้างประจำ SET [กอง/สำนัก] = 'กองวิชาการและแผนงาน' WHERE [กอง/สำนัก] = 'วิชาการและแผนงาน' ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();

		$cmd = " select max(POEM_ID) as MAX_ID from PER_POS_EMP ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$POEM_ID = $data[MAX_ID] + 1;

		$cmd = " SELECT [เลขที่ตำแหน่ง] AS POEM_NO, [ตำแหน่ง] AS PN_NAME, [อัตราค่าตอบแทน] AS POEM_MAX_SALARY, 
						[กอง/สำนัก] AS ORG_NAME, [ส่วน] AS ORG_NAME_1, [ฝ่าย] AS ORG_NAME_2, [กลุ่มงาน] AS ORG_NAME_3, [งาน] AS ORG_NAME_4 
						FROM ลูกจ้างประจำ 
						ORDER BY [เลขที่ตำแหน่ง] ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "$cmd<br>";
		while($data = $db_dpis35->get_array()){
			$PER_POS_EMP++;
			$POEM_NO = trim($data[POEM_NO]);
			$POEM_NO = str_replace("-", "", $POEM_NO);
			$PN_NAME = trim($data[PN_NAME]);
			$POEM_MAX_SALARY = trim($data[POEM_MAX_SALARY]);
			$ORG_NAME = trim($data[ORG_NAME]);
			$ORG_NAME_1 = trim($data[ORG_NAME_1]);
			$ORG_NAME_2 = trim($data[ORG_NAME_2]);
			$ORG_NAME_3 = trim($data[ORG_NAME_3]);
			$ORG_NAME_4 = trim($data[ORG_NAME_4]);
			if (!$ORG_NAME_1) $ORG_NAME_1 = "-";
			if (!$ORG_NAME_2) $ORG_NAME_2 = "-";
			if (!$ORG_NAME_3) $ORG_NAME_3 = "-";
			if (!$ORG_NAME_4) $ORG_NAME_4 = "-";

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = '$ORG_NAME' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$ORG_ID = $data2[NEW_CODE];

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = '$ORG_NAME$ORG_NAME_1' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$ORG_ID_1 = $data2[NEW_CODE];

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = '$ORG_NAME_1$ORG_NAME_2' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$ORG_ID_2 = $data2[NEW_CODE];

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = '$ORG_NAME_1$ORG_NAME_2$ORG_NAME_3' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$ORG_ID_3 = $data2[NEW_CODE];

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = '$ORG_NAME_2$ORG_NAME_3$ORG_NAME_4' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$ORG_ID_4 = $data2[NEW_CODE];

			$cmd = " select PN_CODE from PER_POS_NAME where PN_NAME = trim('$PN_NAME') order by PN_SEQ_NO ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PN_CODE = $data2[PN_CODE];
			if (!$PN_CODE) echo "ชื่อตำแหน่งลูกจ้างประจำ $PN_NAME - $cmd<br>";
			$LEVEL_NO = "บ1";
	
			if (!$POEM_MIN_SALARY) $POEM_MIN_SALARY = 0;
			if (!$POEM_MAX_SALARY) $POEM_MAX_SALARY = 0;
			if (!$POEM_STATUS) $POEM_STATUS = 1;

			if (!$ORG_ID) $ORG_ID = 1;
			if (!$ORG_ID_1) $ORG_ID_1 = "NULL";
			if (!$ORG_ID_2) $ORG_ID_2 = "NULL";

			$cmd = " INSERT INTO PER_POS_EMP (POEM_ID, ORG_ID, POEM_NO, ORG_ID_1, ORG_ID_2, PN_CODE, POEM_MIN_SALARY, 
							  POEM_MAX_SALARY, POEM_STATUS, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID, POEM_REMARK, POEM_NO_NAME, LEVEL_NO)
							  VALUES ($POEM_ID, $ORG_ID, '$POEM_NO', $ORG_ID_1, $ORG_ID_2, '$PN_CODE', $POEM_MIN_SALARY, $POEM_MAX_SALARY, 
							  $POEM_STATUS, $UPDATE_USER, '$UPDATE_DATE', $SESS_DEPARTMENT_ID, '$POEM_REMARK', '$POEM_NO_NAME', '$LEVEL_NO') ";
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
			elseif ($table[$i]=="per_personal")
				$cmd = " delete from $table[$i] where per_type = 3 ";
			else
				$cmd = " truncate table $table[$i] ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end for

// ตำแหน่งพนักงานจ้าง 30292 - 30292

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

		$cmd = " UPDATE พนักงานจ้างทั่วไป SET [กอง/สำนัก] = 'สำนักการสาธารณสุขและสิ่งแวดล้อม' WHERE [กอง/สำนัก] = 'นักการสาธารณสุขและสิ่งแวดล้อม' ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();

		$cmd = " select max(POEMS_ID) as MAX_ID from PER_POS_EMPSER ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$POEMS_ID = $data[MAX_ID] + 1;

		$cmd = " SELECT [เลขที่ตำแหน่ง] AS POEMS_NO, [ตำแหน่ง] AS EP_NAME, [อัตราค่าตอบแทน] AS POEM_MAX_SALARY, 
						[กอง/สำนัก] AS ORG_NAME, [ส่วน] AS ORG_NAME_1, [ฝ่าย] AS ORG_NAME_2, [กลุ่มงาน] AS ORG_NAME_3, [งาน] AS ORG_NAME_4 
						FROM พนักงานจ้างทั่วไป 
						ORDER BY [เลขที่ตำแหน่ง] ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "$cmd<br>";
		while($data = $db_dpis35->get_array()){
			$PER_POS_EMPSER++;
			$POEMS_NO = trim($data[POEMS_NO]);
			$POEMS_NO = str_replace("-", "", $POEMS_NO);
			$EP_NAME = trim($data[EP_NAME]);
			$POEM_MAX_SALARY = trim($data[POEM_MAX_SALARY]);
			$ORG_NAME = trim($data[ORG_NAME]);
			$ORG_NAME_1 = trim($data[ORG_NAME_1]);
			$ORG_NAME_2 = trim($data[ORG_NAME_2]);
			$ORG_NAME_3 = trim($data[ORG_NAME_3]);
			$ORG_NAME_4 = trim($data[ORG_NAME_4]);
			if (!$ORG_NAME_1) $ORG_NAME_1 = "-";
			if (!$ORG_NAME_2) $ORG_NAME_2 = "-";
			if (!$ORG_NAME_3) $ORG_NAME_3 = "-";
			if (!$ORG_NAME_4) $ORG_NAME_4 = "-";

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = '$ORG_NAME' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$ORG_ID = $data2[NEW_CODE];

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = '$ORG_NAME$ORG_NAME_1' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$ORG_ID_1 = $data2[NEW_CODE];

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = '$ORG_NAME_1$ORG_NAME_2' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$ORG_ID_2 = $data2[NEW_CODE];

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = '$ORG_NAME_1$ORG_NAME_2$ORG_NAME_3' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$ORG_ID_3 = $data2[NEW_CODE];

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = '$ORG_NAME_2$ORG_NAME_3$ORG_NAME_4' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$ORG_ID_4 = $data2[NEW_CODE];

			$cmd = " select EP_CODE from PER_EMPSER_POS_NAME where EP_NAME = trim('$EP_NAME') order by EP_SEQ_NO ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$EP_CODE = $data2[EP_CODE];
			if (!$EP_CODE) echo "ชื่อตำแหน่งพนักงานจ้างทั่วไป $EP_NAME - $cmd<br>";
			$LEVEL_NO = "E1";

			if (!$POEM_MIN_SALARY) $POEM_MIN_SALARY = 0;
			if (!$POEM_MAX_SALARY) $POEM_MAX_SALARY = 0;
			if (!$POEM_STATUS) $POEM_STATUS = 1;

			if (!$ORG_ID) $ORG_ID = 1;
			if (!$ORG_ID_1) $ORG_ID_1 = "NULL";
			if (!$ORG_ID_2) $ORG_ID_2 = "NULL";

			$cmd = " INSERT INTO PER_POS_EMPSER (POEMS_ID, ORG_ID, POEMS_NO, ORG_ID_1, ORG_ID_2, EP_CODE, POEM_MIN_SALARY, 
							  POEM_MAX_SALARY, POEM_STATUS, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID, POEMS_REMARK)
							  VALUES ($POEMS_ID, $ORG_ID, '$POEMS_NO', $ORG_ID_1, $ORG_ID_2, '$EP_CODE', $POEM_MIN_SALARY, $POEM_MAX_SALARY, 
							  $POEM_STATUS, $UPDATE_USER, '$UPDATE_DATE', $SESS_DEPARTMENT_ID, '$POEMS_REMARK') ";
			$db_dpis->send_cmd($cmd);

			$cmd1 = " SELECT POEMS_ID FROM PER_POS_EMPSER WHERE POEMS_ID = $POEMS_ID "; 
			$count_data = $db_dpis1->send_cmd($cmd1);
			if (!$count_data) {
				echo "$cmd<br>==================<br>";
				$db_dpis->show_error();
				echo "<br>end ". ++$i  ."=======================<br>";
			}

			$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
							VALUES ('PER_POS_EMPSER', '$POEMS_NO', '$POEMS_ID', $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$POEMS_ID++;
		} // end while						

		$cmd = " UPDATE พนักงานจ้างตามภารกิจ SET [กอง/สำนัก] = 'สำนักปลัดเทศบาล' WHERE [กอง/สำนัก] = 'ปลัดเทศบาล' ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();

		$cmd = " UPDATE พนักงานจ้างตามภารกิจ SET [ตำแหน่ง] = 'ผู้ช่วยเจ้าหน้าที่ป้องกันและบรรเทาสาธารณภัย' WHERE [ตำแหน่ง] = 'ผู้ช่วยเจ้าหน้าที่ป้องกันฯ' ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();

		$cmd = " UPDATE พนักงานจ้างตามภารกิจ SET [ตำแหน่ง] = 'พนักงานขับรถยนต์' WHERE [ตำแหน่ง] = 'พนักงานขับรถนต์' or [ตำแหน่ง] = 'พนักงานขับรถนยนต์' ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();

		$cmd = " UPDATE พนักงานจ้างตามภารกิจ SET [ตำแหน่ง] = 'ครูสอนวิชาดนตรี' WHERE [ตำแหน่ง] = 'ครูสอวิชาดนตรี' ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();

		$cmd = " UPDATE พนักงานจ้างตามภารกิจ SET [ตำแหน่ง] = 'ครูสอนวิชาภาษาอังกฤษ' WHERE [ตำแหน่ง] = 'ครูสอนวิชาภาษอังกฤษ' ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();

		$cmd = " UPDATE พนักงานจ้างตามภารกิจ SET [ตำแหน่ง] = 'ผู้ช่วยนายช่างเขียนแบบ' WHERE [ตำแหน่ง] = 'ผู้ช่วยนายช่งเขียนแบบ' ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();

		$cmd = " UPDATE พนักงานจ้างตามภารกิจ SET [ตำแหน่ง] = 'ช่างเครื่องสูบน้ำ' WHERE [ตำแหน่ง] = 'ช่างเครืองสูบน้ำ' ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();

		$cmd = " UPDATE พนักงานจ้างตามภารกิจ SET [ตำแหน่ง] = 'พนักงานขับเครื่องจักรกลขนาดหนัก' WHERE [ตำแหน่ง] = 'พนักงานขับเตรื่องจักรกลขนาดหนัก' ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();

		$cmd = " UPDATE พนักงานจ้างตามภารกิจ SET [ตำแหน่ง] = 'แม่ครัว' WHERE [ตำแหน่ง] = 'แม่ตรัว' ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();

		$cmd = " UPDATE พนักงานจ้างตามภารกิจ SET [ตำแหน่ง] = 'ผู้ช่วยนักวิชาการสุขาภิบาล' WHERE [ตำแหน่ง] = 'ผู้ช้วยนักวิชาการสุขาภิบาล' ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();

		$cmd = " select max(POEMS_ID) as MAX_ID from PER_POS_EMPSER ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$POEMS_ID = $data[MAX_ID] + 1;

		$cmd = " SELECT [เลขที่ตำแหน่ง] AS POEMS_NO, [ตำแหน่ง] AS EP_NAME, [อัตราค่าตอบแทน] AS POEM_MAX_SALARY, 
						[กอง/สำนัก] AS ORG_NAME, [ส่วน] AS ORG_NAME_1, [ฝ่าย] AS ORG_NAME_2, [กลุ่มงาน] AS ORG_NAME_3, [งาน] AS ORG_NAME_4 
						FROM พนักงานจ้างตามภารกิจ 
						ORDER BY [เลขที่ตำแหน่ง] ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "$cmd<br>";
		while($data = $db_dpis35->get_array()){
			$PER_POS_EMPSER++;
			$POEMS_NO = trim($data[POEMS_NO]);
			$POEMS_NO = str_replace("-", "", $POEMS_NO);
			$EP_NAME = trim($data[EP_NAME]);
			$POEM_MAX_SALARY = trim($data[POEM_MAX_SALARY]);
			$ORG_NAME = trim($data[ORG_NAME]);
			$ORG_NAME_1 = trim($data[ORG_NAME_1]);
			$ORG_NAME_2 = trim($data[ORG_NAME_2]);
			$ORG_NAME_3 = trim($data[ORG_NAME_3]);
			$ORG_NAME_4 = trim($data[ORG_NAME_4]);
			if (!$ORG_NAME_1) $ORG_NAME_1 = "-";
			if (!$ORG_NAME_2) $ORG_NAME_2 = "-";
			if (!$ORG_NAME_3) $ORG_NAME_3 = "-";
			if (!$ORG_NAME_4) $ORG_NAME_4 = "-";

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = '$ORG_NAME' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$ORG_ID = $data2[NEW_CODE];

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = '$ORG_NAME$ORG_NAME_1' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$ORG_ID_1 = $data2[NEW_CODE];

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = '$ORG_NAME_1$ORG_NAME_2' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$ORG_ID_2 = $data2[NEW_CODE];

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = '$ORG_NAME_1$ORG_NAME_2$ORG_NAME_3' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$ORG_ID_3 = $data2[NEW_CODE];

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = '$ORG_NAME_2$ORG_NAME_3$ORG_NAME_4' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$ORG_ID_4 = $data2[NEW_CODE];

			$cmd = " select EP_CODE from PER_EMPSER_POS_NAME where EP_NAME = trim('$EP_NAME') order by EP_SEQ_NO ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$EP_CODE = $data2[EP_CODE];
			if (!$EP_CODE) echo "ชื่อตำแหน่งพนักงานจ้างตามภารกิจ $EP_NAME - $cmd<br>";
			$LEVEL_NO = "S1";

			if (!$POEM_MIN_SALARY) $POEM_MIN_SALARY = 0;
			if (!$POEM_MAX_SALARY) $POEM_MAX_SALARY = 0;
			if (!$POEM_STATUS) $POEM_STATUS = 1;

			if (!$ORG_ID) $ORG_ID = 1;
			if (!$ORG_ID_1) $ORG_ID_1 = "NULL";
			if (!$ORG_ID_2) $ORG_ID_2 = "NULL";

			$cmd = " INSERT INTO PER_POS_EMPSER (POEMS_ID, ORG_ID, POEMS_NO, ORG_ID_1, ORG_ID_2, EP_CODE, POEM_MIN_SALARY, 
							  POEM_MAX_SALARY, POEM_STATUS, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID, POEMS_REMARK)
							  VALUES ($POEMS_ID, $ORG_ID, '$POEMS_NO', $ORG_ID_1, $ORG_ID_2, '$EP_CODE', $POEM_MIN_SALARY, $POEM_MAX_SALARY, 
							  $POEM_STATUS, $UPDATE_USER, '$UPDATE_DATE', $SESS_DEPARTMENT_ID, '$POEMS_REMARK') ";
			$db_dpis->send_cmd($cmd);

			$cmd1 = " SELECT POEMS_ID FROM PER_POS_EMPSER WHERE POEMS_ID = $POEMS_ID "; 
			$count_data = $db_dpis1->send_cmd($cmd1);
			if (!$count_data) {
				echo "$cmd<br>==================<br>";
				$db_dpis->show_error();
				echo "<br>end ". ++$i  ."=======================<br>";
			}

			$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
							VALUES ('PER_POS_EMPSER', '$POEMS_NO', '$POEMS_ID', $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$POEMS_ID++;
		} // end while						

		$cmd = " select count(POEMS_ID) as COUNT_NEW from PER_POS_EMPSER ";
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
		$db_dpis35->free_result();
		$db_dpis351->free_result();
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
		$db_dpis35->free_result();
		$db_dpis351->free_result();

		$PER_ID = $PER_TYPE = 1;
		$cmd = " SELECT [เลขที่ตำแหน่ง] AS POS_NO, [คำนำหน้านาม] AS PN_NAME, [นาม] AS PER_NAME, [นามสกุล] AS PER_SURNAME, [ตำแหน่ง] AS PL_NAME, 
						[อัตราเงินเดือน] AS PER_SALARY, [ระดับ] AS LEVEL_NAME, [กอง/สำนัก] AS ORG_NAME, [ส่วน] AS ORG_NAME_1, [ฝ่าย] AS ORG_NAME_2, 
						[กลุ่มงาน] AS ORG_NAME_3, [งาน] AS ORG_NAME_4, [วัน/เดือน/ปั เกิด] AS BIRTH_DATE, [ที่อยู่] AS ADDRESS, [วัน/เดือน/ปั ที่บรรจุ] AS START_DATE, 
						[วัน/เดือน/ปี เกษียณ] AS RETIRE_DATE, [ชื่อคู่สมรส] AS SPOUSE_NAME, [ชื่อบิดา-มาดา] AS FATHER_NAME 
						FROM พนักงาน 
						ORDER BY [เลขที่ตำแหน่ง] ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "<br>";
		while($data = $db_dpis35->get_array()){
			$PER_PERSONAL++;
			$POS_NO = trim($data[POS_NO]);
			$POS_NO = str_replace("29.", "9", $POS_NO);
			if ($POS_NO=="ข 0001") $POS_NO = "0001";
			$PN_NAME = trim($data[PN_NAME]);
			$PER_NAME = trim($data[PER_NAME]);
			$PER_SURNAME = trim($data[PER_SURNAME]);
			$OT_CODE = "01";
			$PER_GENDER = "";
			if ($PN_NAME=="นาย" || $PN_NAME== "จ.อ." || $PN_NAME== "ว่าที่ ร.ต.") $PER_GENDER = 1;
			elseif ($PN_NAME=="นาง" || $PN_NAME=="นางสาว") $PER_GENDER = 2;
			$PER_SALARY = trim($data[PER_SALARY]);
			$LEVEL_NAME = trim($data[LEVEL_NAME]);
			$ORG_NAME = trim($data[ORG_NAME]);
			$ORG_NAME_1 = trim($data[ORG_NAME_1]);
			$ORG_NAME_2 = trim($data[ORG_NAME_2]);
			$ORG_NAME_3 = trim($data[ORG_NAME_3]);
			$ORG_NAME_4 = trim($data[ORG_NAME_4]);
			if (!$ORG_NAME_1) $ORG_NAME_1 = "-";
			if (!$ORG_NAME_2) $ORG_NAME_2 = "-";
			if (!$ORG_NAME_3) $ORG_NAME_3 = "-";
			if (!$ORG_NAME_4) $ORG_NAME_4 = "-";
			$LEVEL_NO = "";
			if ($LEVEL_NAME=="1") $LEVEL_NO = "01";
			elseif ($LEVEL_NAME=="2") $LEVEL_NO = "02";
			elseif ($LEVEL_NAME=="3") $LEVEL_NO = "03";
			elseif ($LEVEL_NAME=="4") $LEVEL_NO = "04";
			elseif ($LEVEL_NAME=="5") $LEVEL_NO = "05";
			elseif ($LEVEL_NAME=="6" || $LEVEL_NAME=="6ว" || $LEVEL_NAME=="6 ว") $LEVEL_NO = "06";
			elseif ($LEVEL_NAME=="7" || $LEVEL_NAME=="7ว" || $LEVEL_NAME=="7 ว" || $LEVEL_NAME=="7วช" || $LEVEL_NAME=="7 วช") $LEVEL_NO = "07";
			elseif ($LEVEL_NAME=="8" || $LEVEL_NAME=="8วช" || $LEVEL_NAME=="8 วช") $LEVEL_NO = "08";
			elseif ($LEVEL_NAME=="9") $LEVEL_NO = "09";
			elseif ($LEVEL_NAME=="คศ.2") $LEVEL_NO = "xx";
			elseif ($LEVEL_NAME=="คศ.3") $LEVEL_NO = "xx";
			else echo "ระดับตำแหน่ง $LEVEL_NAME<br>";

			$PER_BIRTHDATE = $PER_RETIREDATE = "";
			if ($BIRTH_DATE) {
				$BIRTH_DATE = trim($data[BIRTH_DATE]);
				$RGTIRE_DATE = trim($data[RGTIRE_DATE]); 
				$BIRTH_YEAR = substr($BIRTH_DATE, 4, 4) - 543;
				$RETIRE_YEAR = $BIRTH_YEAR + 60;
				$PER_BIRTHDATE = ($BIRTH_DATE)? ($BIRTH_YEAR ."-". substr($BIRTH_DATE, 2, 2) ."-". substr($BIRTH_DATE, 0, 2)) : "";
				if(substr($BIRTH_DATE, 2, 2) > 10 || (substr($BIRTH_DATE, 2, 2)==10 && substr($BIRTH_DATE, 0, 2) > "01"))    
					$RETIRE_YEAR += 1;
				$PER_RETIREDATE = $RETIRE_YEAR."-10-01"; 
			}
			$ADDRESS = trim($data[ADDRESS]);
			$SPOUSE_NAME = trim($data[SPOUSE_NAME]);
			$FATHER_NAME = trim($data[FATHER_NAME]);
			$RE_CODE = '01';
			if ($PN_NAME=="นาง") $MR_CODE = '2';
			else $MR_CODE = "1";
			$PER_STATUS = 1;
			$POS_NO = trim($data[POS_NO]);
			$PER_SALARY = $data[PER_SALARY]+0;
			$PER_MEMBER = 0;
			$PER_MGTSALARY = 0; 

			if ($PN_NAME=="นาย") $PN_CODE = "003";
			elseif ($PN_NAME== "จ.อ.") $PN_CODE = "374";
			elseif ($PN_NAME=="ว่าที่ ร.ต.") $PN_CODE = "219";
			elseif ($PN_NAME=="นาง") $PN_CODE = "005";
			elseif ($PN_NAME=="นางสาว") $PN_CODE = "004";
			else echo "คำนำหน้าชื่อ $PN_NAME<br>";
	
			$POS_ID = "NULL";
			$POEM_ID = "NULL";
			$POEMS_ID = "NULL";
			$POT_ID = "NULL";
			$PER_ORGMGT = 0;
			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_POSITION' AND OLD_CODE = '$POS_NO' ";
			$db_dpis2->send_cmd($cmd);
			//$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$POS_ID = $data2[NEW_CODE] + 0;

			if ($POS_ORGMGT) $PER_ORGMGT = 1;
				
			$ASS_ORG_ID = "NULL";
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

			$MOV_CODE = "99999"; // รอแก้
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
							VALUES ($PER_ID, $PER_TYPE, '$OT_CODE', '$PN_CODE', trim('$PER_NAME'), trim('$PER_SURNAME'), 
							trim('$PER_ENG_NAME'), trim('$PER_ENG_SURNAME'), $ASS_ORG_ID, $POS_ID, $POEM_ID, $POEMS_ID, $POT_ID, 
							$LEVEL_NO, $PER_ORGMGT, $PER_SALARY, $PER_MGTSALARY, 0, $PER_GENDER, '$MR_CODE', '$PER_CARDNO', 
							'$PER_OFFNO', '$PER_TAXNO', '$PER_BLOOD', '$RE_CODE', '$PER_BIRTHDATE', 	'$PER_RETIREDATE', '$PER_STARTDATE', 
							'$PER_OCCUPYDATE', '$PER_POSDATE', '$PN_CODE_F', trim('$PER_FATHERNAME'), 
							trim('$PER_FATHERSURNAME'), '$PN_CODE_M', trim('$PER_MOTHERNAME'), 
							trim('$PER_MOTHERSURNAME'), '$PV_CODE', '$MOV_CODE', $PER_ORDAIN, '$ORDAINDETAIL', $PER_SOLDIER, 
							$PER_MEMBER, '$PER_MEMBERDATE', $PER_STATUS, NULL, NULL, $SESS_DEPARTMENT_ID, $LEVEL_NO, $UPDATE_USER, 
							'$UPDATE_DATE', NULL, NULL, NULL, '$PER_REMARK', '$PER_START_ORG', $PAY_ID, '$ES_CODE', 
							'$OLDSECNAME', '$OLDPOSNAME', '$PER_DOCNO', '$PER_DOCDATE', $PER_COOPERATIVE, '$COOPDETAIL',
							'$PER_EFFECTIVEDATE', '$PER_POS_REASON', '$PER_POS_YEAR', '$PER_POS_DOCTYPE', '$PER_POS_DOCNO', 
							'$PER_POS_ORG') ";
			$db_dpis->send_cmd($cmd);

			$cmd1 = " SELECT PER_ID FROM PER_PERSONAL WHERE PER_ID = $PER_ID "; 
			$count_data = $db_dpis1->send_cmd($cmd1);
			if (!$count_data) {
				echo "$cmd<br>==================<br>";
				$db_dpis->show_error();
				echo "<br>end ". ++$i  ."=======================<br>";
			}

			$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
							VALUES ('PER_PERSONAL', '$POS_NO', '$PER_ID', $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$PER_ID++;
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_PERSONAL where PER_TYPE = 1 ";
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
		$db_dpis35->free_result();
		$db_dpis351->free_result();
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
		$db_dpis35->free_result();
		$db_dpis351->free_result();

		$cmd = " select max(PER_ID) as MAX_ID from PER_PERSONAL ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PER_ID = $data[MAX_ID] + 1;

		$PER_TYPE = 2;
		$cmd = " SELECT [เลขที่ตำแหน่ง] AS POEM_NO, [คำนำหน้านาม] AS PN_NAME, [นาม] AS PER_NAME, [นามสกุล] AS PER_SURNAME, [ตำแหน่ง] AS POEM_NAME, 
						[อัตราค่าตอบแทน] AS PER_SALARY, [กอง/สำนัก] AS ORG_NAME, [ส่วน] AS ORG_NAME_1, [ฝ่าย] AS ORG_NAME_2, 
						[กลุ่มงาน] AS ORG_NAME_3, [งาน] AS ORG_NAME_4, [วัน/เดือน/ปั เกิด] AS BIRTH_DATE, [ที่อยู่] AS ADDRESS, [วัน/เดือน/ปั ที่บรรจุ] AS START_DATE, 
						[ชื่อคู่สมรส] AS SPOUSE_NAME, [ชื่อบิดา-มาดา] AS FATHER_NAME 
						FROM ลูกจ้างประจำ 
						ORDER BY [เลขที่ตำแหน่ง] ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "<br>";
		while($data = $db_dpis35->get_array()){
			$PER_PERSONAL++;
			$POEM_NO = trim($data[POEM_NO]);
			$POEM_NO = str_replace("-", "", $POEM_NO);
			$PN_NAME = trim($data[PN_NAME]);
			$PER_NAME = trim($data[PER_NAME]);
			$PER_SURNAME = trim($data[PER_SURNAME]);
			$OT_CODE = "07";
			$PER_GENDER = "";
			if ($PN_NAME=="นาย") $PER_GENDER = 1;
			elseif ($PN_NAME=="นาง" || $PN_NAME=="นางสาว") $PER_GENDER = 2;
			$PER_SALARY = trim($data[PER_SALARY]);
			$ORG_NAME = trim($data[ORG_NAME]);
			$ORG_NAME_1 = trim($data[ORG_NAME_1]);
			$ORG_NAME_2 = trim($data[ORG_NAME_2]);
			$ORG_NAME_3 = trim($data[ORG_NAME_3]);
			$ORG_NAME_4 = trim($data[ORG_NAME_4]);
			if (!$ORG_NAME_1) $ORG_NAME_1 = "-";
			if (!$ORG_NAME_2) $ORG_NAME_2 = "-";
			if (!$ORG_NAME_3) $ORG_NAME_3 = "-";
			if (!$ORG_NAME_4) $ORG_NAME_4 = "-";
			$LEVEL_NO = "บ1";

			$PER_BIRTHDATE = $PER_RETIREDATE = "";
			if ($BIRTH_DATE) {
				$BIRTH_DATE = trim($data[BIRTH_DATE]);
				$RGTIRE_DATE = trim($data[RGTIRE_DATE]); 
				$BIRTH_YEAR = substr($BIRTH_DATE, 4, 4) - 543;
				$RETIRE_YEAR = $BIRTH_YEAR + 60;
				$PER_BIRTHDATE = ($BIRTH_DATE)? ($BIRTH_YEAR ."-". substr($BIRTH_DATE, 2, 2) ."-". substr($BIRTH_DATE, 0, 2)) : "";
				if(substr($BIRTH_DATE, 2, 2) > 10 || (substr($BIRTH_DATE, 2, 2)==10 && substr($BIRTH_DATE, 0, 2) > "01"))    
					$RETIRE_YEAR += 1;
				$PER_RETIREDATE = $RETIRE_YEAR."-10-01"; 
			}
			$ADDRESS = trim($data[ADDRESS]);
			$SPOUSE_NAME = trim($data[SPOUSE_NAME]);
			$FATHER_NAME = trim($data[FATHER_NAME]);
			$RE_CODE = '01';
			if ($PN_NAME=="นาง") $MR_CODE = '2';
			else $MR_CODE = "1";
			$PER_STATUS = 1;
			$POS_NO = trim($data[POS_NO]);
			$PER_SALARY = $data[PER_SALARY]+0;
			$PER_MEMBER = 0;
			$PER_MGTSALARY = 0; 

			if ($PN_NAME=="นาย") $PN_CODE = "003";
			elseif ($PN_NAME== "จ.อ.") $PN_CODE = "374";
			elseif ($PN_NAME=="ว่าที่ ร.ต.") $PN_CODE = "219";
			elseif ($PN_NAME=="นาง") $PN_CODE = "005";
			elseif ($PN_NAME=="นางสาว") $PN_CODE = "004";
			else echo "คำนำหน้าชื่อ $PN_NAME<br>";
	
			$POS_ID = "NULL";
			$POEM_ID = "NULL";
			$POEMS_ID = "NULL";
			$POT_ID = "NULL";
			$PER_ORGMGT = 0;
			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_POS_EMP' AND OLD_CODE = '$POEM_NO' ";
			$db_dpis2->send_cmd($cmd);
			//$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$POEM_ID = $data2[NEW_CODE] + 0;

			if ($POS_ORGMGT) $PER_ORGMGT = 1;
				
			$ASS_ORG_ID = "NULL";
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

			$MOV_CODE = "99999"; // รอแก้
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
							VALUES ($PER_ID, $PER_TYPE, '$OT_CODE', '$PN_CODE', trim('$PER_NAME'), trim('$PER_SURNAME'), 
							trim('$PER_ENG_NAME'), trim('$PER_ENG_SURNAME'), $ASS_ORG_ID, $POS_ID, $POEM_ID, $POEMS_ID, $POT_ID, 
							$LEVEL_NO, $PER_ORGMGT, $PER_SALARY, $PER_MGTSALARY, 0, $PER_GENDER, '$MR_CODE', '$PER_CARDNO', 
							'$PER_OFFNO', '$PER_TAXNO', '$PER_BLOOD', '$RE_CODE', '$PER_BIRTHDATE', 	'$PER_RETIREDATE', '$PER_STARTDATE', 
							'$PER_OCCUPYDATE', '$PER_POSDATE', '$PN_CODE_F', trim('$PER_FATHERNAME'), 
							trim('$PER_FATHERSURNAME'), '$PN_CODE_M', trim('$PER_MOTHERNAME'), 
							trim('$PER_MOTHERSURNAME'), '$PV_CODE', '$MOV_CODE', $PER_ORDAIN, '$ORDAINDETAIL', $PER_SOLDIER, 
							$PER_MEMBER, '$PER_MEMBERDATE', $PER_STATUS, NULL, NULL, $SESS_DEPARTMENT_ID, $LEVEL_NO, $UPDATE_USER, 
							'$UPDATE_DATE', NULL, NULL, NULL, '$PER_REMARK', '$PER_START_ORG', $PAY_ID, '$ES_CODE', 
							'$OLDSECNAME', '$OLDPOSNAME', '$PER_DOCNO', '$PER_DOCDATE', $PER_COOPERATIVE, '$COOPDETAIL',
							'$PER_EFFECTIVEDATE', '$PER_POS_REASON', '$PER_POS_YEAR', '$PER_POS_DOCTYPE', '$PER_POS_DOCNO', 
							'$PER_POS_ORG') ";
			$db_dpis->send_cmd($cmd);

			$cmd1 = " SELECT PER_ID FROM PER_PERSONAL WHERE PER_ID = $PER_ID "; 
			$count_data = $db_dpis1->send_cmd($cmd1);
			if (!$count_data) {
				echo "$cmd<br>==================<br>";
				$db_dpis->show_error();
				echo "<br>end ". ++$i  ."=======================<br>";
			}

			$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
							VALUES ('PER_PERSONAL', '$POS_NO', '$PER_ID', $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$PER_ID++; 
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
		$PER_ID = $data[MAX_ID] + 1;

		$PER_TYPE = 3;
		$cmd = " SELECT [เลขที่ตำแหน่ง] AS POEMS_NO, [คำนำหน้านาม] AS PN_NAME, [นาม] AS PER_NAME, [นามสกุล] AS PER_SURNAME, [ตำแหน่ง] AS EP_NAME, 
						[อัตราค่าตอบแทน] AS PER_SALARY, [กอง/สำนัก] AS ORG_NAME, [ส่วน] AS ORG_NAME_1, [ฝ่าย] AS ORG_NAME_2, 
						[กลุ่มงาน] AS ORG_NAME_3, [งาน] AS ORG_NAME_4, [วัน/เดือน/ปั เกิด] AS BIRTH_DATE, [ที่อยู่] AS ADDRESS, [วัน/เดือน/ปั ที่บรรจุ] AS START_DATE, 
						[ชื่อคู่สมรส] AS SPOUSE_NAME, [ชื่อบิดา-มาดา] AS FATHER_NAME 
						FROM พนักงานจ้างทั่วไป 
						ORDER BY [เลขที่ตำแหน่ง] ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "<br>";
		while($data = $db_dpis35->get_array()){
			$PER_PERSONAL++;
			$POEMS_NO = trim($data[POEMS_NO]);
			$POEMS_NO = str_replace("-", "", $POEMS_NO);
			$PN_NAME = trim($data[PN_NAME]);
			$PER_NAME = trim($data[PER_NAME]);
			$PER_SURNAME = trim($data[PER_SURNAME]);
			$OT_CODE = "08";
			$PER_GENDER = "";
			if ($PN_NAME=="นาย" || $PN_NAME=="ว่าที่ ร.ต.") $PER_GENDER = 1;
			elseif ($PN_NAME=="นาง" || $PN_NAME=="นางสาว") $PER_GENDER = 2;
			$PER_SALARY = trim($data[PER_SALARY]);
			$ORG_NAME = trim($data[ORG_NAME]);
			$ORG_NAME_1 = trim($data[ORG_NAME_1]);
			$ORG_NAME_2 = trim($data[ORG_NAME_2]);
			$ORG_NAME_3 = trim($data[ORG_NAME_3]);
			$ORG_NAME_4 = trim($data[ORG_NAME_4]);
			if (!$ORG_NAME_1) $ORG_NAME_1 = "-";
			if (!$ORG_NAME_2) $ORG_NAME_2 = "-";
			if (!$ORG_NAME_3) $ORG_NAME_3 = "-";
			if (!$ORG_NAME_4) $ORG_NAME_4 = "-";
			$LEVEL_NO = "E1";

			$PER_BIRTHDATE = $PER_RETIREDATE = "";
			if ($BIRTH_DATE) {
				$BIRTH_DATE = trim($data[BIRTH_DATE]);
				$RGTIRE_DATE = trim($data[RGTIRE_DATE]); 
				$BIRTH_YEAR = substr($BIRTH_DATE, 4, 4) - 543;
				$RETIRE_YEAR = $BIRTH_YEAR + 60;
				$PER_BIRTHDATE = ($BIRTH_DATE)? ($BIRTH_YEAR ."-". substr($BIRTH_DATE, 2, 2) ."-". substr($BIRTH_DATE, 0, 2)) : "";
				if(substr($BIRTH_DATE, 2, 2) > 10 || (substr($BIRTH_DATE, 2, 2)==10 && substr($BIRTH_DATE, 0, 2) > "01"))    
					$RETIRE_YEAR += 1;
				$PER_RETIREDATE = $RETIRE_YEAR."-10-01"; 
			}
			$ADDRESS = trim($data[ADDRESS]);
			$SPOUSE_NAME = trim($data[SPOUSE_NAME]);
			$FATHER_NAME = trim($data[FATHER_NAME]);
			$RE_CODE = '01';
			if ($PN_NAME=="นาง") $MR_CODE = '2';
			else $MR_CODE = "1";
			$PER_STATUS = 1;
			$POS_NO = trim($data[POS_NO]);
			$PER_SALARY = $data[PER_SALARY]+0;
			$PER_MEMBER = 0;
			$PER_MGTSALARY = 0; 

			if ($PN_NAME=="นาย") $PN_CODE = "003";
			elseif ($PN_NAME== "จ.อ.") $PN_CODE = "374";
			elseif ($PN_NAME=="ว่าที่ ร.ต.") $PN_CODE = "219";
			elseif ($PN_NAME=="นาง") $PN_CODE = "005";
			elseif ($PN_NAME=="นางสาว") $PN_CODE = "004";
			else echo "คำนำหน้าชื่อ $PN_NAME<br>";
	
			$POS_ID = "NULL";
			$POEM_ID = "NULL";
			$POEMS_ID = "NULL";
			$POT_ID = "NULL";
			$PER_ORGMGT = 0;
			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_POS_EMPSER' AND OLD_CODE = '$POEMS_NO' ";
			$db_dpis2->send_cmd($cmd);
			//$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$POEMS_ID = $data2[NEW_CODE] + 0;

			if ($POS_ORGMGT) $PER_ORGMGT = 1;
				
			$ASS_ORG_ID = "NULL";
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

			$MOV_CODE = "99999"; // รอแก้
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
							VALUES ($PER_ID, $PER_TYPE, '$OT_CODE', '$PN_CODE', trim('$PER_NAME'), trim('$PER_SURNAME'), 
							trim('$PER_ENG_NAME'), trim('$PER_ENG_SURNAME'), $ASS_ORG_ID, $POS_ID, $POEM_ID, $POEMS_ID, $POT_ID, 
							$LEVEL_NO, $PER_ORGMGT, $PER_SALARY, $PER_MGTSALARY, 0, $PER_GENDER, '$MR_CODE', '$PER_CARDNO', 
							'$PER_OFFNO', '$PER_TAXNO', '$PER_BLOOD', '$RE_CODE', '$PER_BIRTHDATE', 	'$PER_RETIREDATE', '$PER_STARTDATE', 
							'$PER_OCCUPYDATE', '$PER_POSDATE', '$PN_CODE_F', trim('$PER_FATHERNAME'), 
							trim('$PER_FATHERSURNAME'), '$PN_CODE_M', trim('$PER_MOTHERNAME'), 
							trim('$PER_MOTHERSURNAME'), '$PV_CODE', '$MOV_CODE', $PER_ORDAIN, '$ORDAINDETAIL', $PER_SOLDIER, 
							$PER_MEMBER, '$PER_MEMBERDATE', $PER_STATUS, NULL, NULL, $SESS_DEPARTMENT_ID, $LEVEL_NO, $UPDATE_USER, 
							'$UPDATE_DATE', NULL, NULL, NULL, '$PER_REMARK', '$PER_START_ORG', $PAY_ID, '$ES_CODE', 
							'$OLDSECNAME', '$OLDPOSNAME', '$PER_DOCNO', '$PER_DOCDATE', $PER_COOPERATIVE, '$COOPDETAIL',
							'$PER_EFFECTIVEDATE', '$PER_POS_REASON', '$PER_POS_YEAR', '$PER_POS_DOCTYPE', '$PER_POS_DOCNO', 
							'$PER_POS_ORG') ";
			$db_dpis->send_cmd($cmd);

			$cmd1 = " SELECT PER_ID FROM PER_PERSONAL WHERE PER_ID = $PER_ID "; 
			$count_data = $db_dpis1->send_cmd($cmd1);
			if (!$count_data) {
				echo "$cmd<br>==================<br>";
				$db_dpis->show_error();
				echo "<br>end ". ++$i  ."=======================<br>";
			}

			$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
							VALUES ('PER_PERSONAL', '$$POEMS_NO', '$$PER_ID', $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$PER_ID++;
		} // end while						

		$cmd = " SELECT [เลขที่ตำแหน่ง] AS POEMS_NO, [คำนำหน้านาม] AS PN_NAME, [นาม] AS PER_NAME, [นามสกุล] AS PER_SURNAME, [ตำแหน่ง] AS EP_NAME, 
						[อัตราค่าตอบแทน] AS PER_SALARY, [กอง/สำนัก] AS ORG_NAME, [ส่วน] AS ORG_NAME_1, [ฝ่าย] AS ORG_NAME_2, 
						[กลุ่มงาน] AS ORG_NAME_3, [งาน] AS ORG_NAME_4, [วัน/เดือน/ปั เกิด] AS BIRTH_DATE, [ที่อยู่] AS ADDRESS, [วัน/เดือน/ปั ที่บรรจุ] AS START_DATE, 
						[ชื่อคู่สมรส] AS SPOUSE_NAME, [ชื่อบิดา-มาดา] AS FATHER_NAME 
						FROM พนักงานจ้างตามภารกิจ 
						ORDER BY [เลขที่ตำแหน่ง] ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "<br>";
		while($data = $db_dpis35->get_array()){
			$PER_PERSONAL++;
			$POEMS_NO = trim($data[POEMS_NO]);
			$POEMS_NO = str_replace("-", "", $POEMS_NO);
			$PN_NAME = trim($data[PN_NAME]);
			$PER_NAME = trim($data[PER_NAME]);
			$PER_SURNAME = trim($data[PER_SURNAME]);
			$OT_CODE = "09";
			$PER_GENDER = "";
			if ($PN_NAME=="นาย" || $PN_NAME=="ว่าที่ ร.ต.") $PER_GENDER = 1;
			elseif ($PN_NAME=="นาง" || $PN_NAME=="นางสาว") $PER_GENDER = 2;
			$PER_SALARY = trim($data[PER_SALARY]);
			$ORG_NAME = trim($data[ORG_NAME]);
			$ORG_NAME_1 = trim($data[ORG_NAME_1]);
			$ORG_NAME_2 = trim($data[ORG_NAME_2]);
			$ORG_NAME_3 = trim($data[ORG_NAME_3]);
			$ORG_NAME_4 = trim($data[ORG_NAME_4]);
			if (!$ORG_NAME_1) $ORG_NAME_1 = "-";
			if (!$ORG_NAME_2) $ORG_NAME_2 = "-";
			if (!$ORG_NAME_3) $ORG_NAME_3 = "-";
			if (!$ORG_NAME_4) $ORG_NAME_4 = "-";
			$LEVEL_NO = "S1";

			$PER_BIRTHDATE = $PER_RETIREDATE = "";
			if ($BIRTH_DATE) {
				$BIRTH_DATE = trim($data[BIRTH_DATE]);
				$RGTIRE_DATE = trim($data[RGTIRE_DATE]); 
				$BIRTH_YEAR = substr($BIRTH_DATE, 4, 4) - 543;
				$RETIRE_YEAR = $BIRTH_YEAR + 60;
				$PER_BIRTHDATE = ($BIRTH_DATE)? ($BIRTH_YEAR ."-". substr($BIRTH_DATE, 2, 2) ."-". substr($BIRTH_DATE, 0, 2)) : "";
				if(substr($BIRTH_DATE, 2, 2) > 10 || (substr($BIRTH_DATE, 2, 2)==10 && substr($BIRTH_DATE, 0, 2) > "01"))    
					$RETIRE_YEAR += 1;
				$PER_RETIREDATE = $RETIRE_YEAR."-10-01"; 
			}
			$ADDRESS = trim($data[ADDRESS]);
			$SPOUSE_NAME = trim($data[SPOUSE_NAME]);
			$FATHER_NAME = trim($data[FATHER_NAME]);
			$RE_CODE = '01';
			if ($PN_NAME=="นาง") $MR_CODE = '2';
			else $MR_CODE = "1";
			$PER_STATUS = 1;
			$POS_NO = trim($data[POS_NO]);
			$PER_SALARY = $data[PER_SALARY]+0;
			$PER_MEMBER = 0;
			$PER_MGTSALARY = 0; 

			if ($PN_NAME=="นาย") $PN_CODE = "003";
			elseif ($PN_NAME== "จ.อ.") $PN_CODE = "374";
			elseif ($PN_NAME=="ว่าที่ ร.ต.") $PN_CODE = "219";
			elseif ($PN_NAME=="นาง") $PN_CODE = "005";
			elseif ($PN_NAME=="นางสาว") $PN_CODE = "004";
			else echo "คำนำหน้าชื่อ $PN_NAME<br>";
	
			$POS_ID = "NULL";
			$POEM_ID = "NULL";
			$POEMS_ID = "NULL";
			$POT_ID = "NULL";
			$PER_ORGMGT = 0;
			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_POS_EMPSER' AND OLD_CODE = '$POEMS_NO' ";
			$db_dpis2->send_cmd($cmd);
			//$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$POEMS_ID = $data2[NEW_CODE] + 0;

			if ($POS_ORGMGT) $PER_ORGMGT = 1;
				
			$ASS_ORG_ID = "NULL";
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

			$MOV_CODE = "99999"; // รอแก้
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
							VALUES ($PER_ID, $PER_TYPE, '$OT_CODE', '$PN_CODE', trim('$PER_NAME'), trim('$PER_SURNAME'), 
							trim('$PER_ENG_NAME'), trim('$PER_ENG_SURNAME'), $ASS_ORG_ID, $POS_ID, $POEM_ID, $POEMS_ID, $POT_ID, 
							$LEVEL_NO, $PER_ORGMGT, $PER_SALARY, $PER_MGTSALARY, 0, $PER_GENDER, '$MR_CODE', '$PER_CARDNO', 
							'$PER_OFFNO', '$PER_TAXNO', '$PER_BLOOD', '$RE_CODE', '$PER_BIRTHDATE', 	'$PER_RETIREDATE', '$PER_STARTDATE', 
							'$PER_OCCUPYDATE', '$PER_POSDATE', '$PN_CODE_F', trim('$PER_FATHERNAME'), 
							trim('$PER_FATHERSURNAME'), '$PN_CODE_M', trim('$PER_MOTHERNAME'), 
							trim('$PER_MOTHERSURNAME'), '$PV_CODE', '$MOV_CODE', $PER_ORDAIN, '$ORDAINDETAIL', $PER_SOLDIER, 
							$PER_MEMBER, '$PER_MEMBERDATE', $PER_STATUS, NULL, NULL, $SESS_DEPARTMENT_ID, $LEVEL_NO, $UPDATE_USER, 
							'$UPDATE_DATE', NULL, NULL, NULL, '$PER_REMARK', '$PER_START_ORG', $PAY_ID, '$ES_CODE', 
							'$OLDSECNAME', '$OLDPOSNAME', '$PER_DOCNO', '$PER_DOCDATE', $PER_COOPERATIVE, '$COOPDETAIL',
							'$PER_EFFECTIVEDATE', '$PER_POS_REASON', '$PER_POS_YEAR', '$PER_POS_DOCTYPE', '$PER_POS_DOCNO', 
							'$PER_POS_ORG') ";
			$db_dpis->send_cmd($cmd);

			$cmd1 = " SELECT PER_ID FROM PER_PERSONAL WHERE PER_ID = $PER_ID "; 
			$count_data = $db_dpis1->send_cmd($cmd1);
			if (!$count_data) {
				echo "$cmd<br>==================<br>";
				$db_dpis->show_error();
				echo "<br>end ". ++$i  ."=======================<br>";
			}

			$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
							VALUES ('PER_PERSONAL', '$$POEMS_NO', '$$PER_ID', $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$PER_ID++;
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
		$db_dpis35->free_result();
		$db_dpis351->free_result();
	} // end if
	
	if( $command=='DECORATE' ){
// เครื่องราชย์ 
		$cmd = " truncate table per_decoratehis ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

// เครื่องราชย์ข้าราชการ 45069
		$cmd = " SELECT [เลขที่ตำแหน่ง] AS POS_NO, [เครื่องราชอิสริยาภรณ์  ประกาศเกียรติคุณ  และเหรียญเชิชูเกียรติที] AS DC_NAME, 
						[วันที่ได้รับ] AS DEH_DATE, [วันส่งคืน] AS DEH_RETURN_DATE, [เอกสารอ้างอิง] AS DEH_GAZETTE
						FROM เครื่องราชย์ 
						ORDER BY [เลขที่ตำแหน่ง] ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "<br>";
		$MAX_ID = 1;
		while($data = $db_dpis35->get_array()){
			$PER_DECORATEHIS++;    
			$POS_NO = trim($data[POS_NO]);
			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_POSITION' AND OLD_CODE = '$POS_NO' ";
			$db_dpis->send_cmd($cmd);
			$db_dpis->show_error();
			$data_dpis = $db_dpis->get_array();
			$POS_ID = $data_dpis[NEW_CODE] + 0;
			if ($POS_ID > 0) {
				$cmd = " select PER_ID from PER_PERSONAL where POS_ID=$POS_ID ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$PER_ID = $data2[PER_ID] + 0;

				if ($PER_ID > 0) {
					$DC_NAME = trim($data[DC_NAME]);
					if ($DC_NAME=="ขอ จ.ช. ปี 2554" || $DC_NAME=="ขอ จ.ช. ปี2554") $DC_CODE = "28";
					elseif ($DC_NAME=="ขอ จ.ช. ปี 2555") $DC_CODE = "28";
					elseif ($DC_NAME=="ขอ จ.ม. ปี 2554" || $DC_NAME=="ขอ จ.ม.ปี 54") $DC_CODE = "29";
					elseif ($DC_NAME=="ขอ ต.ช. ปี พ.ศ.2555") $DC_CODE = "23";
					elseif ($DC_NAME=="ขอ ต.ม.  ปี พ.ศ.2555" || $DC_NAME=="ขอ ต.ม. ปี 2555" || $DC_NAME=="ขอ ต.ม.ปี2555") $DC_CODE = "24";
					elseif ($DC_NAME=="ขอ ต.ม. ปี 2554") $DC_CODE = "24";
					elseif ($DC_NAME=="ขอ บ.ม.ปี 54") $DC_CODE = "34";
					elseif ($DC_NAME=="ขอ บช.ปี 54") $DC_CODE = "33";
					elseif ($DC_NAME=="ขอเหรียญจักรดิมาลา ปี 2554") $DC_CODE = "61";
					elseif ($DC_NAME=="จตุรถาภรณ์ช้างเผือก (จ.ช.)" || $DC_NAME=="จัตุรถาภรณ์ช้างเผือก (จ.ช.)") $DC_CODE = "28";
					elseif ($DC_NAME=="จตุรถาภรณ์มงกุฎไทย (จ.ม.)" || $DC_NAME=="จัตุรถาภรณ์มงกุฎไทย (จ.ม)") $DC_CODE = "29";
					elseif ($DC_NAME=="ตริตราภรณ์ช้างเผือก (ต.ช.)" || $DC_NAME=="ตริตาภรณ์ช้างเผือก (ต.ช.)"|| $DC_NAME=="ตริตาภรณืช้างเผือก (ต.ช.)") $DC_CODE = "23";
					elseif ($DC_NAME=="ตริตราภรณ์มงกุฎไทย (ต.ม.)" || $DC_NAME=="ตริตาภรณ์มงกุฎไทย (จ.ม.)" || $DC_NAME=="ตริตาภรณ์มงกุฎไทย (ต.ม.)" || $DC_NAME=="ตริตาภรณืมงกุฎไทย (ต.ม.)") $DC_CODE = "24";
					elseif ($DC_NAME=="ทวีตริยาภรณ์ช้างเผือก (ท.ช.)" || $DC_NAME=="ทวีติยาภรณ์ช้างเผือก" || $DC_NAME=="ทวีติยาภรณ์ช้างเผือก (ท.ช.)" || $DC_NAME=="ทวีติยาภรณืช้างเผือก (ท.ช.)") $DC_CODE = "15";
					elseif ($DC_NAME=="ทวีตริตาภรณ์มงกุฎไทย (ต.ม.)" || $DC_NAME=="ทวีติยาภรณ์มงกุฎไทย (ท.ม)." || $DC_NAME=="ทวีติยาภรณ์มงกุฎไทย (ท.ม.)" || $DC_NAME=="ทวีติยาภรณ์มงกูฏไทย (ต.ม.)" || $DC_NAME=="ทวีติยาภรณ์มงกูฏไทย (ท.ม.)") $DC_CODE = "16";
					elseif ($DC_NAME=="เบญจมาภรณืมงกุฎไทย (บ.ม.)") $DC_CODE = "34";
					elseif ($DC_NAME=="ประกาศเกียรติคุณ  ชั้น 2  พ.ศ.2545") $DC_CODE = "82";
					elseif ($DC_NAME=="ประกาศเกียรติคุณ ชั้น 2  ปีพ.ศ.2549" || $DC_NAME=="ประกาศเกียรติคุณ ชั้น 2  พ.ศ.2549" || $DC_NAME=="ประกาศเกียรติคุณชั้น 2 ปี พ.ศ.2549") $DC_CODE = "82";
					elseif ($DC_NAME=="ประกาศเกียรติคุณชั้น 2 พ.ศ.2544") $DC_CODE = "82";
					elseif ($DC_NAME=="ประกาศเกียรติคุณชั้น 3 (2549)") $DC_CODE = "83";
					elseif ($DC_NAME=="ประกาศเกียรติคุณชั้น 3 (2552)") $DC_CODE = "83";
					elseif ($DC_NAME=="ประกาศเกียรติคุณชั้น 3 ประจำปี 2539") $DC_CODE = "83";
					elseif ($DC_NAME=="ประกาศเกียรติคุณชั้น 3 ปี 2548") $DC_CODE = "83";
					elseif ($DC_NAME=="ประกาศเกียรติคุณชั้น 3 ปี พ.ศ.2544") $DC_CODE = "83";
					elseif ($DC_NAME=="ประกาศเกียรติคุณชั้น 3 ปี พ.ศ.2551") $DC_CODE = "83";
					elseif ($DC_NAME=="ประกาศเกียรติคุณชั้น 3 พ.ศ.2535") $DC_CODE = "83";
					elseif ($DC_NAME=="มหาวชิรมงกุฎ (ม.ว.ม.)") $DC_CODE = "09";
					elseif ($DC_NAME=="เหรียญจักรพรรดิมาลา") $DC_CODE = "61";
					$DEH_DATE = trim($data[DEH_DATE]);
					$DEH_DATE = ($DEH_DATE)? ((substr($DEH_DATE, 4, 4) - 543) ."-". substr($DEH_DATE, 2, 2) ."-". substr($DEH_DATE, 0, 2)) : "-";
					$DEH_GAZETTE = trim($data[DEH_GAZETTE]);
					$DEH_RETURN_DATE = trim($data[DEH_RETURN_DATE]);

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
					} else {
						$MAX_ID++;
						$PER_DECORATEHIS = $MAX_ID;    
					}
				}
			} else echo "$POS_NO<br>";
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

	if( $command=='EDUCATE' ){
		$cmd = " truncate table per_educate ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE ประวัติการศึกษา SET [วุฒิการศึกษา] = 'ครุศาสตรบัณฑิต' WHERE [วุฒิการศึกษา] = 'ครุศาสตร์บัณฑิต' ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();

		$cmd = " UPDATE ประวัติการศึกษา SET [วุฒิการศึกษา] = 'นิติศาสตรบัณฑิต' WHERE [วุฒิการศึกษา] = 'นิติศาสตร์บัณฑิต' ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();

		$cmd = " UPDATE ประวัติการศึกษา SET [วุฒิการศึกษา] = 'นิเทศศาสตรบัณฑิต' WHERE [วุฒิการศึกษา] = 'นิเทศศาสตร์บัณฑิต' ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();

		$cmd = " UPDATE ประวัติการศึกษา SET [วุฒิการศึกษา] = 'รัฐประศาสนศาสตรมหาบัณฑิต' WHERE [วุฒิการศึกษา] = 'รัฐประศาสนศาสตร์มหาบัณฑิต' ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();

		$cmd = " UPDATE ประวัติการศึกษา SET [วุฒิการศึกษา] = 'รัฐศาสตรมหาบัณฑิต' WHERE [วุฒิการศึกษา] = 'รัฐศาสตร์มหาบัณฑิต' ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();

		$cmd = " UPDATE ประวัติการศึกษา SET [วุฒิการศึกษา] = 'วิทยาศาสตรบัณฑิต' WHERE [วุฒิการศึกษา] = 'วิทยาศาสตร์บัณฑิต' ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();

		$cmd = " UPDATE ประวัติการศึกษา SET [วุฒิการศึกษา] = 'วิทยาศาสตรมหาบัณฑิต' WHERE [วุฒิการศึกษา] = 'วิทยาศาสตร์มหาบัณฑิต' ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();

		$cmd = " UPDATE ประวัติการศึกษา SET [วุฒิการศึกษา] = 'ศิลปศาสตรบัณฑิต' WHERE [วุฒิการศึกษา] = 'ศิลปศาสตร์บัณฑิต' ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();

		$cmd = " UPDATE ประวัติการศึกษา SET [วุฒิการศึกษา] = 'ศิลปศาสตรมหาบัณฑิต' WHERE [วุฒิการศึกษา] = 'ศิลปศาสตร์มหาบัณฑิต' ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();

		$cmd = " UPDATE ประวัติการศึกษา SET [วุฒิการศึกษา] = 'ศึกษาศาสตรบัณฑิต' WHERE [วุฒิการศึกษา] = 'ศึกษาศาสตร์บัณฑิต' ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();

		$cmd = " UPDATE ประวัติการศึกษา SET [วุฒิการศึกษา] = 'เศรษฐศาสตรมหาบัณฑิต' WHERE [วุฒิการศึกษา] = 'เศรษฐศาสตร์มหาบัณฑิต' ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
 
		$cmd = " UPDATE ประวัติการศึกษา SET [วุฒิการศึกษา] = 'ศิลปศาสตรมหาบัณฑิต' WHERE [วุฒิการศึกษา] = 'ศิลปะศาสตร์มหาบัณฑิต' ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();

// ประวัติการศึกษาข้าราชการ 46774
		$cmd = " SELECT [เลขที่ตำแหน่ง] AS POS_NO, [วุฒิการศึกษา] AS EN_NAME, [สถานศึกษา] AS INS_NAME, [เมื่อ พุทธศักราช] AS EDU_YEAR
						FROM ประวัติการศึกษา 
						ORDER BY [เลขที่ตำแหน่ง] ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "<br>";
		$MAX_ID = 1;
		while($data = $db_dpis35->get_array()){
			$PER_EDUCATE++;    
			$EDU_SEQ = 0;
			$POS_NO = trim($data[POS_NO]);
			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_POSITION' AND OLD_CODE = '$POS_NO' ";
			$db_dpis->send_cmd($cmd);
			$db_dpis->show_error();
			$data_dpis = $db_dpis->get_array();
			$POS_ID = $data_dpis[NEW_CODE] + 0;
			if ($POS_ID > 0) {
				$cmd = " select PER_ID from PER_PERSONAL where POS_ID=$POS_ID ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$PER_ID = $data2[PER_ID] + 0;

				if ($PER_ID > 0) {
					$EDU_SEQ++;
					$EN_NAME = trim($data[EN_NAME]);
					$EDU_REMARK = $EN_NAME;
					$INS_NAME = trim($data[INS_NAME]);
					$EDU_YEAR = trim($data[EDU_YEAR]);
					$EN_CODE = $EM_CODE = $INS_CODE = "";
					$cmd = " select EN_CODE from PER_EDUCNAME where EN_NAME='$EN_NAME' ";
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();
					$EN_CODE = trim($data2[EN_CODE]);
					if (!$EN_CODE) {
						if ($EN_NAME=="ครุศาสตร์บัณฑิต (เอกคอมพิวเตอร์การศึกษา)") {
							$EN_CODE = "4010018";
							$EM_CODE = "65001102";
						} elseif ($EN_NAME=="เนติบัณฑิต  สมัยที่ 56  ปีการศึกษา 2546") {
							$EN_CODE = "0010001";
						} elseif ($EN_NAME=="บริหารธุรกิจบัณฑิต  (สาขาวิชาบริหารธุรกิจบัณฑิต)") {
							$EN_CODE = "4010038";
						} elseif ($EN_NAME=="บริหารธุรกิจบัณฑิต (การจัดการทั่วไป)") {
							$EN_CODE = "4010174";
							$EM_CODE = "54020100";
						} elseif ($EN_NAME=="บริหารธุรกิจบัณฑิต (การบัญชี)") {
							$EN_CODE = "4010041";
							$EM_CODE = "55009100";
						} elseif ($EN_NAME=="ประกาศนียบัตรการพยาบาล") {
							$EN_CODE = "2010041";
						} elseif ($EN_NAME=="ประกาศนียบัตรมัธยมศึกษาตอนต้น") {
							$EN_CODE = "0510111";
						} elseif ($EN_NAME=="ประกาศนียบัตรมัธยมศึกษาตอนปลาย") {
							$EN_CODE = "0510113";
						} elseif ($EN_NAME=="ประกาศนียบัตรวิชาชีพชั้นสูง (การบัญชี)" || $EN_NAME=="ประกาศนียบีตรวิชาชีพชั้นสูง (การบัญชี)") {
							$EN_CODE = "3010000";
							$EM_CODE = "55009100";
						} elseif ($EN_NAME=="ประกาศนียบีตรวิชาชีพ (การบัญชี)") {
							$EN_CODE = "1010000";
							$EM_CODE = "55009100";
						} elseif ($EN_NAME=="ประถมศึกษา") {
							$EN_CODE = "0510112";
						} elseif ($EN_NAME=="ปริญญาตรี การจัดการทั่วไป") {
							$EN_CODE = "4010000";
							$EM_CODE = "54020100";
						} elseif ($EN_NAME=="ปวช (ช่างก่อสร้าง)") {
							$EN_CODE = "1010000";
							$EM_CODE = "80001101";
						} elseif ($EN_NAME=="ปวส (ควบคุมงานก่อสร้าง)") {
							$EN_CODE = "3010000";
							$EM_CODE = "80005101";
						} elseif ($EN_NAME=="มัธยมศึกษา") {
							$EN_CODE = "6010030";
						} elseif ($EN_NAME=="มัธยมศึกษาตอนปลาย") {
							$EN_CODE = "0510076";
						} elseif ($EN_NAME=="รัฐศาสตร์มหาบัณฑิต (บริหารรัฐกิจ)") {
							$EN_CODE = "6010050";
							$EM_CODE = "54009100";
						} elseif ($EN_NAME=="วิทยาศาสตร์บัณฑิต (สุขาภิบาล)") {
							$EN_CODE = "4010061";
							$EM_CODE = "75009100";
						} elseif ($EN_NAME=="วิทยาศาสตร์มหาบัณฑิต (สาธารณสุขศาสตร์)") {
							$EN_CODE = "6010076";
							$EM_CODE = "75005100";
						} elseif ($EN_NAME=="ศิลปศาสตร์บัณฑิต (การจัดการทั่วไป)") {
							$EN_CODE = "4010145";
							$EM_CODE = "54020100";
						} elseif ($EN_NAME=="ศิลปศาสตร์บัณฑิต (บริหารรัฐกิจ)") {
							$EN_CODE = "4010123";
							$EM_CODE = "54009100";
						} elseif ($EN_NAME=="ศิลปศาสตร์บัณฑิต (รัฐประศาสนศาสตร์)") {
							$EN_CODE = "4010123";
							$EM_CODE = "54008100";
						} elseif ($EN_NAME=="ศิลปศาสตร์บัณฑิต (รัฐศาสตร์ เอกบริหารรัฐกิจ)" || $EN_NAME=="ศิลปศาสตร์บัณฑิต (รัฐศาสตร์) สาขาบริหารรัฐกิจ") {
							$EN_CODE = "4010140";
							$EM_CODE = "54009100";
						} elseif ($EN_NAME=="ศิลปศาสตร์บัณฑิต (รัฐศาสตร์)") {
							$EN_CODE = "4010140";
							$EM_CODE = "54000100";
						} elseif ($EN_NAME=="ศิลปศาสตร์มหาบัณฑิต (นโยบายและการวางแผนสังคม)") {
							$EN_CODE = "6010095";
							$EM_CODE = "51048100";
						} elseif ($EN_NAME=="ศิลปศาสตร์มหาบัณฑิต (รัฐศาสตร์)") {
							$EN_CODE = "6010089";
							$EM_CODE = "54000100";
						} elseif ($EN_NAME=="ศึกษาศาสตร์บัณฑิต สาขาการสอนภาษาอังกฤษ") {
							$EN_CODE = "4010142";
							$EM_CODE = "17033100";
						} elseif ($EN_NAME=="อนุปริญญา ศิลปศาสตร์") {
							$EN_CODE = "2010080";
						}

						if (!$EN_CODE && $EN_NAME) {
							$i++;
							echo "$i วุฒิการศึกษา $EN_NAME<br>";
						}
					}
			
					$EDU_INSTITUTE = "";
					$cmd = " select INS_CODE from PER_INSTITUTE where INS_NAME = '$INS_NAME' ";
					$db_dpis2->send_cmd($cmd);
		//			$db_dpis2->show_error();
					$data2 = $db_dpis2->get_array();
					$INS_CODE = $data2[INS_CODE];
					if (!$INS_CODE) $EDU_INSTITUTE = $INS_NAME;
			
					if ($EDU_YEAR=="2544") {
						$EDU_ENDYEAR = "2544"; 
					} elseif ($EDU_YEAR=="2546") {
						$EDU_ENDYEAR = "2546"; 
					} elseif ($EDU_YEAR=="31 พ.ค.2534") {
						$EDU_ENDYEAR = "2534"; 
						$EDU_ENDDATE = "1991-05-31"; 
					} elseif ($EDU_YEAR=="5 มิ.ย.2537") {
						$EDU_ENDYEAR = "2537"; 
						$EDU_ENDDATE = "1994-06-05"; 
					} elseif ($EDU_YEAR=="9 พ.ค.-19 ก.ค.2538") {
						$EDU_ENDYEAR = "2538"; 
						$EDU_ENDDATE = "1995-07-19"; 
					} elseif ($EDU_YEAR=="พ.ศ.2511-2512") {
						$EDU_STARTYEAR = "2511"; 
						$EDU_ENDYEAR = "2512"; 
					} elseif ($EDU_YEAR=="พ.ศ.2511-2515") {
						$EDU_STARTYEAR = "2511"; 
						$EDU_ENDYEAR = "2515"; 
					} elseif ($EDU_YEAR=="พ.ศ.2513-2514") {
						$EDU_STARTYEAR = "2513"; 
						$EDU_ENDYEAR = "2514"; 
					} elseif ($EDU_YEAR=="พ.ศ.2514-2515") {
						$EDU_STARTYEAR = "2514"; 
						$EDU_ENDYEAR = "2515"; 
					} elseif ($EDU_YEAR=="พ.ศ.2516-2519") {
						$EDU_STARTYEAR = "2516"; 
						$EDU_ENDYEAR = "2519"; 
					} elseif ($EDU_YEAR=="พ.ศ.2517-2518") {
						$EDU_STARTYEAR = "2517"; 
						$EDU_ENDYEAR = "2518"; 
					} elseif ($EDU_YEAR=="พ.ศ.2519") {
						$EDU_ENDYEAR = "2519"; 
					} elseif ($EDU_YEAR=="พ.ศ.2522-2528") {
						$EDU_STARTYEAR = "2522"; 
						$EDU_ENDYEAR = "2528"; 
					} elseif ($EDU_YEAR=="พ.ศ.2523") {
						$EDU_ENDYEAR = "2523"; 
					} elseif ($EDU_YEAR=="พ.ศ.2528-2534") {
						$EDU_STARTYEAR = "2528"; 
						$EDU_ENDYEAR = "2534"; 
					} elseif ($EDU_YEAR=="พ.ศ.2529-2532") {
						$EDU_STARTYEAR = "2529"; 
						$EDU_ENDYEAR = "2532"; 
					} elseif ($EDU_YEAR=="พ.ศ.2530-2532") {
						$EDU_STARTYEAR = "2530"; 
						$EDU_ENDYEAR = "2532"; 
					} elseif ($EDU_YEAR=="พ.ศ.2533-2535") {
						$EDU_STARTYEAR = "2533"; 
						$EDU_ENDYEAR = "2535"; 
					} elseif ($EDU_YEAR=="พ.ศ.2533-2538") {
						$EDU_STARTYEAR = "2533"; 
						$EDU_ENDYEAR = "2538"; 
					} elseif ($EDU_YEAR=="พ.ศ.2534-2538") {
						$EDU_STARTYEAR = "2534"; 
						$EDU_ENDYEAR = "2538"; 
					} elseif ($EDU_YEAR=="พ.ศ.2535-2539") {
						$EDU_STARTYEAR = "2535"; 
						$EDU_ENDYEAR = "2539"; 
					} elseif ($EDU_YEAR=="พ.ศ.2537-2539") {
						$EDU_STARTYEAR = "2537"; 
						$EDU_ENDYEAR = "2539"; 
					} elseif ($EDU_YEAR=="พ.ศ.2537-2541") {
						$EDU_STARTYEAR = "2537"; 
						$EDU_ENDYEAR = "2541"; 
					} elseif ($EDU_YEAR=="พ.ศ.2538-2541") {
						$EDU_STARTYEAR = "2538"; 
						$EDU_ENDYEAR = "2541"; 
					} elseif ($EDU_YEAR=="พ.ศ.2538-2542") {
						$EDU_STARTYEAR = "2538"; 
						$EDU_ENDYEAR = "2542"; 
					} elseif ($EDU_YEAR=="พ.ศ.2539-2541") {
						$EDU_STARTYEAR = "2539"; 
						$EDU_ENDYEAR = "2541"; 
					} elseif ($EDU_YEAR=="พ.ศ.2539-2542") {
						$EDU_STARTYEAR = "2539"; 
						$EDU_ENDYEAR = "2542"; 
					} elseif ($EDU_YEAR=="พ.ศ.2540") {
						$EDU_ENDYEAR = "2540"; 
					} elseif ($EDU_YEAR=="พ.ศ.2540-2546") {
						$EDU_STARTYEAR = "2540"; 
						$EDU_ENDYEAR = "2546"; 
					} elseif ($EDU_YEAR=="พ.ศ.2541-2543") {
						$EDU_STARTYEAR = "2541"; 
						$EDU_ENDYEAR = "2543"; 
					} elseif ($EDU_YEAR=="พ.ศ.2542-2544") {
						$EDU_STARTYEAR = "2542"; 
						$EDU_ENDYEAR = "2544"; 
					} elseif ($EDU_YEAR=="พ.ศ.2544-2548") {
						$EDU_STARTYEAR = "2544"; 
						$EDU_ENDYEAR = "2548"; 
					} elseif ($EDU_YEAR=="พ.ศ.2545-2547") {
						$EDU_STARTYEAR = "2545"; 
						$EDU_ENDYEAR = "2547"; 
					} elseif ($EDU_YEAR=="พ.ศ.2546") {
						$EDU_ENDYEAR = "2546"; 
					} elseif ($EDU_YEAR=="พ.ศ.2546-2547") {
						$EDU_STARTYEAR = "2546"; 
						$EDU_ENDYEAR = "2547"; 
					} elseif ($EDU_YEAR=="พ.ศ.2547-2549") {
						$EDU_STARTYEAR = "2547"; 
						$EDU_ENDYEAR = "2549"; 
					} elseif ($EDU_YEAR=="พ.ศ.2548") {
						$EDU_ENDYEAR = "2548"; 
					} elseif ($EDU_YEAR=="พ.ศ.2549-2551") {
						$EDU_STARTYEAR = "2549"; 
						$EDU_ENDYEAR = "2551"; 
					} elseif ($EDU_YEAR=="พ.ศ.2550-2551") {
						$EDU_STARTYEAR = "2550"; 
						$EDU_ENDYEAR = "2551"; 
					} elseif ($EDU_YEAR=="พ.ศ.2551-2552") {
						$EDU_STARTYEAR = "2551"; 
						$EDU_ENDYEAR = "2552"; 
					} elseif ($EDU_YEAR=="พ.ศ.3523-2537") {
						$EDU_STARTYEAR = "2533"; 
						$EDU_ENDYEAR = "2537"; 
					} elseif ($EDU_YEAR=="พ.ศ2545") {
						$EDU_ENDYEAR = "2545"; 
					}

					$CT_CODE_EDU = "140";
					$EDU_TYPE = "";
					if ($FLAG_EDUCATION == "3") $EDU_TYPE .= "4";
					if ($FLAG_EDUCATION == "2") $EDU_TYPE .= "3";
					if ($FLAG_EDUCATION == "1") $EDU_TYPE .= "1";
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
					} else {
						$MAX_ID++;
						$PER_EDUCATE = $MAX_ID;    
					}
				}
			} else echo "$POS_NO<br>"; 
		} // end while						

// ประวัติการศึกษาลูกจ้างประจำ 37598
		$cmd = " SELECT [เลขที่ตำแหน่ง] AS POEM_NO, [วุฒิการศึกษา] AS EN_NAME, [สถานศึกษา] AS INS_NAME, [เมื่อ พุทธศักราช] AS EDU_YEAR
						FROM ประวัติการศึกษาลูกจ้างประจำ 
						ORDER BY [เลขที่ตำแหน่ง] ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "<br>";
		while($data = $db_dpis35->get_array()){
			$PER_EDUCATE++;    
			$EDU_SEQ = 0;
			$POEM_NO = trim($data[POEM_NO]);
			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_POS_EMP' AND OLD_CODE = '$POEM_NO' ";
			$db_dpis->send_cmd($cmd);
			$db_dpis->show_error();
			$data_dpis = $db_dpis->get_array();
			$POEM_ID = $data_dpis[NEW_CODE] + 0;
			if ($POEM_ID > 0) {
				$cmd = " select PER_ID from PER_PERSONAL where POEM_ID=$POEM_ID ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$PER_ID = $data2[PER_ID] + 0;

				if ($PER_ID > 0) {
					$EDU_SEQ++;
					$EN_NAME = trim($data[EN_NAME]);
					$EDU_REMARK = $EN_NAME;
					$INS_NAME = trim($data[INS_NAME]);
					$EDU_YEAR = trim($data[EDU_YEAR]);
					$EN_CODE = $EM_CODE = $INS_CODE = "";
					$cmd = " select EN_CODE from PER_EDUCNAME where EN_NAME='$EN_NAME' ";
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();
					$EN_CODE = trim($data2[EN_CODE]);
					if (!$EN_CODE) {
						if ($EN_NAME=="ประกาศนียบัตรวิชาชีพชั้นสูง") {
							$EN_CODE = "3010000";
						} elseif ($EN_NAME=="มัธนมศึกษาตอนต้น") {
							$EN_CODE = "0510111";
						}

						if (!$EN_CODE && $EN_NAME) {
							$i++;
							echo "$i วุฒิการศึกษา $EN_NAME<br>";
						}
					}
			
					$EDU_INSTITUTE = "";
					$cmd = " select INS_CODE from PER_INSTITUTE where INS_NAME = '$INS_NAME' ";
					$db_dpis2->send_cmd($cmd);
		//			$db_dpis2->show_error();
					$data2 = $db_dpis2->get_array();
					$INS_CODE = $data2[INS_CODE];
					if (!$INS_CODE) $EDU_INSTITUTE = $INS_NAME;
			
					if ($EDU_YEAR=="พ.ศ.2520-2525") {
						$EDU_STARTYEAR = "2520"; 
						$EDU_ENDYEAR = "2525"; 
					} elseif ($EDU_YEAR=="พ.ศ.2517-2519") {
						$EDU_STARTYEAR = "2517"; 
						$EDU_ENDYEAR = "2519"; 
					}

					$CT_CODE_EDU = "140";
					$EDU_TYPE = "";
					if ($FLAG_EDUCATION == "3") $EDU_TYPE .= "4";
					if ($FLAG_EDUCATION == "2") $EDU_TYPE .= "3";
					if ($FLAG_EDUCATION == "1") $EDU_TYPE .= "1";
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
					} else {
						$MAX_ID++;
						$PER_EDUCATE = $MAX_ID;    
					}
				}
			} else echo "$POEM_NO<br>"; 
		} // end while						

// ประวัติการศึกษาพนักงานจ้างทั่วไป 37598
		$cmd = " SELECT [เลขที่ตำแหน่ง] AS POEMS_NO, [วุฒิการศึกษา] AS EN_NAME, [สถานศึกษา] AS INS_NAME, [เมื่อ พุทธศักราช] AS EDU_YEAR
						FROM พนักงานจ้างทั่วไป 
						ORDER BY [เลขที่ตำแหน่ง] ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "<br>";
		while($data = $db_dpis35->get_array()){
			$PER_EDUCATE++;    
			$EDU_SEQ = 0;
			$POEMS_NO = trim($data[POEMS_NO]);
			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_POS_EMPSER' AND OLD_CODE = '$POEMS_NO' ";
			$db_dpis->send_cmd($cmd);
			$db_dpis->show_error();
			$data_dpis = $db_dpis->get_array();
			$POEMS_ID = $data_dpis[NEW_CODE] + 0;
			if ($POEMS_ID > 0) {
				$cmd = " select PER_ID from PER_PERSONAL where POEMS_ID=$POEMS_ID ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$PER_ID = $data2[PER_ID] + 0;

				if ($PER_ID > 0) {
					$EDU_SEQ++;
					$EN_NAME = trim($data[EN_NAME]);
					$EDU_REMARK = $EN_NAME;
					$INS_NAME = trim($data[INS_NAME]);
					$EDU_YEAR = trim($data[EDU_YEAR]);
					$EN_CODE = $EM_CODE = $INS_CODE = "";
					$cmd = " select EN_CODE from PER_EDUCNAME where EN_NAME='$EN_NAME' ";
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();
					$EN_CODE = trim($data2[EN_CODE]);
					if (!$EN_CODE && $EN_NAME) {
						$i++;
						echo "$i วุฒิการศึกษา $EN_NAME<br>";
					}
			
					$EDU_INSTITUTE = "";
					$cmd = " select INS_CODE from PER_INSTITUTE where INS_NAME = '$INS_NAME' ";
					$db_dpis2->send_cmd($cmd);
		//			$db_dpis2->show_error();
					$data2 = $db_dpis2->get_array();
					$INS_CODE = $data2[INS_CODE];
					if (!$INS_CODE) $EDU_INSTITUTE = $INS_NAME;
			
					if ($EDU_YEAR=="พ.ศ.2545-2547") {
						$EDU_STARTYEAR = "2545"; 
						$EDU_ENDYEAR = "2547"; 
					}

					$CT_CODE_EDU = "140";
					$EDU_TYPE = "";
					if ($FLAG_EDUCATION == "3") $EDU_TYPE .= "4";
					if ($FLAG_EDUCATION == "2") $EDU_TYPE .= "3";
					if ($FLAG_EDUCATION == "1") $EDU_TYPE .= "1";
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
					} else {
						$MAX_ID++;
						$PER_EDUCATE = $MAX_ID;    
					}
				}
			} else echo "$POEM_NO<br>"; 
		} // end while						

		$cmd = " UPDATE ประวัติการศึกษาพนักงานจ้างตามภารกิจ SET [วุฒิการศึกษา] = 'นิติศาสตรบัณฑิต' WHERE [วุฒิการศึกษา] = 'นิติศาสตร์บัณฑิต' ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();

		$cmd = " UPDATE ประวัติการศึกษาพนักงานจ้างตามภารกิจ SET [วุฒิการศึกษา] = 'นิติศาสตรบัณฑิต' WHERE [วุฒิการศึกษา] = 'นิติศาสตร์บัณฑิต' ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();

// ประวัติการศึกษาพนักงานจ้างตามภารกิจ 2202
		$cmd = " SELECT [เลขที่ตำแหน่ง] AS POEMS_NO, [วุฒิการศึกษา] AS EN_NAME, [สถานศึกษา] AS INS_NAME, [เมื่อ พุทธศักราช] AS EDU_YEAR
						FROM ประวัติการศึกษาพนักงานจ้างตามภารกิจ 
						ORDER BY [เลขที่ตำแหน่ง] ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "<br>";
		$MAX_ID = 1;
		while($data = $db_dpis35->get_array()){
			$PER_EDUCATE++;    
			$EDU_SEQ = 0;
			$POEMS_NO = trim($data[POEMS_NO]);
			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_POS_EMPSER' AND OLD_CODE = '$POEMS_NO' ";
			$db_dpis->send_cmd($cmd);
			$db_dpis->show_error();
			$data_dpis = $db_dpis->get_array();
			$POEMS_ID = $data_dpis[NEW_CODE] + 0;
			if ($POEMS_ID > 0) {
				$cmd = " select PER_ID from PER_PERSONAL where POEMS_ID=$POEMS_ID ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$PER_ID = $data2[PER_ID] + 0;

				if ($PER_ID > 0) {
					$EDU_SEQ++;
					$EN_NAME = trim($data[EN_NAME]);
					$EDU_REMARK = $EN_NAME;
					$INS_NAME = trim($data[INS_NAME]);
					$EDU_YEAR = trim($data[EDU_YEAR]);
					$EN_CODE = $EM_CODE = $INS_CODE = "";
					$cmd = " select EN_CODE from PER_EDUCNAME where EN_NAME='$EN_NAME' ";
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();
					$EN_CODE = trim($data2[EN_CODE]);
					if (!$EN_CODE) {
						if ($EN_NAME=="บริหารธุรกิจบัณฑิต  สาขาระบบสารสนเทศฯ-พัฒนาซอฟแวร์" || $EN_NAME=="บริหารธุรกิจบัณฑิต สาขาระบบสารสนเทศทางคอมพิวเตอร์-พัฒนาซอฟแวร์" || 
							$EN_NAME=="บริหารธุรกิจบัณฑิต สาขาระบบสารสนเทศฯ-พัฒนาซอฟแวร์") {
							$EN_CODE = "4010018";
							$EM_CODE = "65001102";
						} elseif ($EN_NAME=="ประกาศนียบัตรวิชาชีพ  ช่างเชื่อมโลหะ") {
							$EN_CODE = "0010001";
						} elseif ($EN_NAME=="ประกาศนียบัตรวิชาชีพ (คอมพิวเตอร์ธุรกิจ)") {
							$EN_CODE = "4010038";
						} elseif ($EN_NAME=="ประกาศนียบัตรวิชาชีพ และประกาศฯยบัตรวิชาชีพชั้นสูง") {
							$EN_CODE = "4010174";
							$EM_CODE = "54020100";
						} elseif ($EN_NAME=="ประกาศนียบัตรวิชาชีพชั้นสูง") {
							$EN_CODE = "4010041";
							$EM_CODE = "55009100";
						} elseif ($EN_NAME=="ประกาศนียบัตรวิชาชีพชั้นสูง (การบัญชี)") {
							$EN_CODE = "2010041";
						} elseif ($EN_NAME=="ประกาศนียบัตรวิชาชีพชั้นสูง (คอมพิวเตอร์ธุรกิจ)" || $EN_NAME=="ประกาศนียบัตรวิชาชีพชั้นสูง เอกคอมพิวเตอร์ธุรกิจ" || $EN_NAME=="ประกาศนียบัตรวิชาชีพชั้นสูง แผนกคอมพิวเตอร์ธุรกิจ") {
							$EN_CODE = "1010000";
							$EM_CODE = "80001101";
						} elseif ($EN_NAME=="ประกาศนียบัตรวิชาชีพชั้นสูง (เครื่องกล)") {
							$EN_CODE = "0510113";
						} elseif ($EN_NAME=="ประกาศนียบัตรวิชาชีพชั้นสูง (ช่างยนต์)") {
							$EN_CODE = "3010000";
							$EM_CODE = "55009100";
						} elseif ($EN_NAME=="ประกาศนียบัตรวิชาชีพชั้นสูง ช่างเทคนิคโลหะ") {
							$EN_CODE = "1010000";
							$EM_CODE = "55009100";
						} elseif ($EN_NAME=="ประกาศนียบัตรวิชาชีพชั้นสูง สาขาบริหารธุรกิจ  แผนกคอมพิวเตอร์ธุรกิจ" || $EN_NAME=="ประกาศนียบัตรวิชาชีพชั้นสูง สาขาบริหารธุรกิจ แผนกคอมพิวเตอร์ธุรกิจ") {
							$EN_CODE = "4010000";
							$EM_CODE = "54020100";
						} elseif ($EN_NAME=="ประถมศึกษา") {
							$EN_CODE = "3010000";
							$EM_CODE = "80005101";
						} elseif ($EN_NAME=="ปริญญาตรี  คอมพิวเตอร์ธุรกิจ" || $EN_NAME=="ปริญญาตรี (คอมพิวเตอร์ธุรกิจ)") {
							$EN_CODE = "6010076";
							$EM_CODE = "75005100";
						} elseif ($EN_NAME=="ปริญญาตรี  บริหารธุรกิจการตลาด") {
							$EN_CODE = "0510076";
						} elseif ($EN_NAME=="ปริญญาตรี  ศิลปะศาสตร์ (นิเทศศาสตร์  วิทยุ-โทรทัศน์)" || $EN_NAME=="ศิลปศาสตร์บัณฑิต  นิเทศศาสตร์ (วิทยุ-โทรทัศน์)") {
							$EN_CODE = "6010089";
							$EM_CODE = "54000100";
						} elseif ($EN_NAME=="ปริญญาตรี  เอกการจัดการทั่วไป" || $EN_NAME=="ปริญญาตรี เอกการจัดการทั่วไป") {
							$EN_CODE = "4010061";
							$EM_CODE = "75009100";
						} elseif ($EN_NAME=="ปริญญาตรี (บริหารทรัพยากรมนุษย์)") {
							$EN_CODE = "4010145";
							$EM_CODE = "54020100";
						} elseif ($EN_NAME=="ปริญญาโท (รัฐประศาสนศาสตร์มหาบัณฑิต)" || $EN_NAME=="ปริญญาโท รัฐประศาสนศาสตรบัณฑิต") {
							$EN_CODE = "4010123";
							$EM_CODE = "54009100";
						} elseif ($EN_NAME=="มัธมศึกษา" || $EN_NAME=="มัธยมศึกษา") {
							$EN_CODE = "4010123";
							$EM_CODE = "54008100";
						} elseif ($EN_NAME=="มัธยมศึกษาตอนปลาย") {
							$EN_CODE = "4010140";
							$EM_CODE = "54009100";
						} elseif ($EN_NAME=="ศิลปศาสตร์บัณฑิต  นิเทศศาสตร์" || $EN_NAME=="ศิลปศาสตร์บัณฑิต (นิเทศศาสตร์)") {
							$EN_CODE = "4010140";
							$EM_CODE = "54000100";
						} elseif ($EN_NAME=="ศิลปศาสตร์บัณฑิต - สื่อสารมวลชน" || $EN_NAME=="ศิลปะศาสตร์บัณฑิต (เอกสื่อสารมวลชน)") {
							$EN_CODE = "6010095";
							$EM_CODE = "51048100";
						} elseif ($EN_NAME=="ศิลปศาสตรมหาบัณฑิต สาขาการบริหารรัฐกิจและกฎหมาย") {
							$EN_CODE = "4010142";
							$EM_CODE = "17033100";
						}

						if (!$EN_CODE && $EN_NAME) {
							$i++;
							echo "$i วุฒิการศึกษา $EN_NAME<br>";
						}
					}
			
					$EDU_INSTITUTE = "";
					$cmd = " select INS_CODE from PER_INSTITUTE where INS_NAME = '$INS_NAME' ";
					$db_dpis2->send_cmd($cmd);
		//			$db_dpis2->show_error();
					$data2 = $db_dpis2->get_array();
					$INS_CODE = $data2[INS_CODE];
					if (!$INS_CODE) $EDU_INSTITUTE = $INS_NAME;
			
					if ($EDU_YEAR=="2544-2548") {
						$EDU_STARTYEAR = "2544"; 
						$EDU_ENDYEAR = "2548"; 
					} elseif ($EDU_YEAR=="2549-2550") {
						$EDU_STARTYEAR = "2549"; 
						$EDU_ENDYEAR = "2550"; 
					} elseif ($EDU_YEAR=="พ.ศ.2520-2552") {
						$EDU_STARTYEAR = "2520"; 
						$EDU_ENDYEAR = "2522"; 
					} elseif ($EDU_YEAR=="พ.ศ.2522-2527") {
						$EDU_STARTYEAR = "2522"; 
						$EDU_ENDYEAR = "2527"; 
					} elseif ($EDU_YEAR=="พ.ศ.2533-2539") {
						$EDU_STARTYEAR = "2533"; 
						$EDU_ENDYEAR = "2539"; 
					} elseif ($EDU_YEAR=="พ.ศ.2536-2538") {
						$EDU_STARTYEAR = "2536"; 
						$EDU_ENDYEAR = "2538"; 
					} elseif ($EDU_YEAR=="พ.ศ.2537-2542") {
						$EDU_STARTYEAR = "2537"; 
						$EDU_ENDYEAR = "2542"; 
					} elseif ($EDU_YEAR=="พ.ศ.2537-2543") {
						$EDU_STARTYEAR = "2537"; 
						$EDU_ENDYEAR = "2543"; 
					} elseif ($EDU_YEAR=="พ.ศ.2514-2515") {
						$EDU_STARTYEAR = "2514"; 
						$EDU_ENDYEAR = "2515"; 
					} elseif ($EDU_YEAR=="พ.ศ.2516-2519") {
						$EDU_STARTYEAR = "2516"; 
						$EDU_ENDYEAR = "2519"; 
					} elseif ($EDU_YEAR=="พ.ศ.2517-2518") {
						$EDU_STARTYEAR = "2517"; 
						$EDU_ENDYEAR = "2518"; 
					} elseif ($EDU_YEAR=="พ.ศ.2519") {
						$EDU_ENDYEAR = "2519"; 
					} elseif ($EDU_YEAR=="พ.ศ.2522-2528") {
						$EDU_STARTYEAR = "2522"; 
						$EDU_ENDYEAR = "2528"; 
					} elseif ($EDU_YEAR=="พ.ศ.2523") {
						$EDU_ENDYEAR = "2523"; 
					} elseif ($EDU_YEAR=="พ.ศ.2528-2534") {
						$EDU_STARTYEAR = "2528"; 
						$EDU_ENDYEAR = "2534"; 
					} elseif ($EDU_YEAR=="พ.ศ.2529-2532") {
						$EDU_STARTYEAR = "2529"; 
						$EDU_ENDYEAR = "2532"; 
					} elseif ($EDU_YEAR=="พ.ศ.2530-2532") {
						$EDU_STARTYEAR = "2530"; 
						$EDU_ENDYEAR = "2532"; 
					} elseif ($EDU_YEAR=="พ.ศ.2533-2535") {
						$EDU_STARTYEAR = "2533"; 
						$EDU_ENDYEAR = "2535"; 
					} elseif ($EDU_YEAR=="พ.ศ.2533-2538") {
						$EDU_STARTYEAR = "2533"; 
						$EDU_ENDYEAR = "2538"; 
					} elseif ($EDU_YEAR=="พ.ศ.2534-2538") {
						$EDU_STARTYEAR = "2534"; 
						$EDU_ENDYEAR = "2538"; 
					} elseif ($EDU_YEAR=="พ.ศ.2535-2539") {
						$EDU_STARTYEAR = "2535"; 
						$EDU_ENDYEAR = "2539"; 
					} elseif ($EDU_YEAR=="พ.ศ.2537-2539") {
						$EDU_STARTYEAR = "2537"; 
						$EDU_ENDYEAR = "2539"; 
					} elseif ($EDU_YEAR=="พ.ศ.2537-2541") {
						$EDU_STARTYEAR = "2537"; 
						$EDU_ENDYEAR = "2541"; 
					} elseif ($EDU_YEAR=="พ.ศ.2538-2541") {
						$EDU_STARTYEAR = "2538"; 
						$EDU_ENDYEAR = "2541"; 
					} elseif ($EDU_YEAR=="พ.ศ.2538-2542") {
						$EDU_STARTYEAR = "2538"; 
						$EDU_ENDYEAR = "2542"; 
					} elseif ($EDU_YEAR=="พ.ศ.2539-2541") {
						$EDU_STARTYEAR = "2539"; 
						$EDU_ENDYEAR = "2541"; 
					} elseif ($EDU_YEAR=="พ.ศ.2539-2542") {
						$EDU_STARTYEAR = "2539"; 
						$EDU_ENDYEAR = "2542"; 
					} elseif ($EDU_YEAR=="พ.ศ.2540") {
						$EDU_ENDYEAR = "2540"; 
					} elseif ($EDU_YEAR=="พ.ศ.2540-2546") {
						$EDU_STARTYEAR = "2540"; 
						$EDU_ENDYEAR = "2546"; 
					} elseif ($EDU_YEAR=="พ.ศ.2541-2543") {
						$EDU_STARTYEAR = "2541"; 
						$EDU_ENDYEAR = "2543"; 
					} elseif ($EDU_YEAR=="พ.ศ.2542-2544") {
						$EDU_STARTYEAR = "2542"; 
						$EDU_ENDYEAR = "2544"; 
					} elseif ($EDU_YEAR=="พ.ศ.2544-2548") {
						$EDU_STARTYEAR = "2544"; 
						$EDU_ENDYEAR = "2548"; 
					} elseif ($EDU_YEAR=="พ.ศ.2545-2547") {
						$EDU_STARTYEAR = "2545"; 
						$EDU_ENDYEAR = "2547"; 
					} elseif ($EDU_YEAR=="พ.ศ.2546") {
						$EDU_ENDYEAR = "2546"; 
					} elseif ($EDU_YEAR=="พ.ศ.2546-2547") {
						$EDU_STARTYEAR = "2546"; 
						$EDU_ENDYEAR = "2547"; 
					} elseif ($EDU_YEAR=="พ.ศ.2547-2549") {
						$EDU_STARTYEAR = "2547"; 
						$EDU_ENDYEAR = "2549"; 
					} elseif ($EDU_YEAR=="พ.ศ.2548") {
						$EDU_ENDYEAR = "2548"; 
					} elseif ($EDU_YEAR=="พ.ศ.2549-2551") {
						$EDU_STARTYEAR = "2549"; 
						$EDU_ENDYEAR = "2551"; 
					} elseif ($EDU_YEAR=="พ.ศ.2550-2551") {
						$EDU_STARTYEAR = "2550"; 
						$EDU_ENDYEAR = "2551"; 
					} elseif ($EDU_YEAR=="พ.ศ.2551-2552") {
						$EDU_STARTYEAR = "2551"; 
						$EDU_ENDYEAR = "2552"; 
					} elseif ($EDU_YEAR=="พ.ศ.3523-2537") {
						$EDU_STARTYEAR = "2533"; 
						$EDU_ENDYEAR = "2537"; 
					} elseif ($EDU_YEAR=="พ.ศ2545") {
						$EDU_ENDYEAR = "2545"; 
					}

					$CT_CODE_EDU = "140";
					$EDU_TYPE = "";
					if ($FLAG_EDUCATION == "3") $EDU_TYPE .= "4";
					if ($FLAG_EDUCATION == "2") $EDU_TYPE .= "3";
					if ($FLAG_EDUCATION == "1") $EDU_TYPE .= "1";
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
					} else {
						$MAX_ID++;
						$PER_EDUCATE = $MAX_ID;    
					}
				}
			} else echo "$POS_NO<br>"; 
		} // end while						

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

	if( $command=='ADDRESS' ){
		$cmd = " truncate table per_address ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

// ที่อยู่ข้าราชการ 23661
		$cmd = " SELECT a.ID, GOVADDR_TYPE, GOVADDR_LINE1, GOVADDR_LINE2, GOVTELEPHONE, GOVZIP_CODE, DIST_CODE, PROV_CODE
						FROM GOVADDRESS a, COMMON_HISTORY b
						WHERE a.ID=b.ID
						ORDER BY a.ID, GOVADDR_TYPE ";
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
			if (trim($data[PROV_CODE])) $PV_CODE = trim($data[PROV_CODE])."00";
			$AP_CODE = trim($data[DIST_CODE]);

			$cmd = " INSERT INTO PER_ADDRESS(ADR_ID, PER_ID, ADR_TYPE, ADR_NO, ADR_ROAD, ADR_SOI, ADR_MOO, ADR_VILLAGE, 
							ADR_BUILDING, ADR_DISTRICT, AP_CODE, PV_CODE, ADR_HOME_TEL, ADR_OFFICE_TEL, ADR_FAX, ADR_MOBILE, 
							ADR_EMAIL, ADR_POSTCODE, ADR_REMARK, UPDATE_USER, UPDATE_DATE, DT_CODE)
							VALUES ($MAX_ID, $PER_ID, $ADR_TYPE, '$ADR_NO', '$ADR_ROAD', '$ADR_SOI', '$ADR_MOO', '$ADR_VILLAGE', 
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

	if( $command=='ADDRESS_EMP' ){
		$cmd = " truncate table per_address ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

// ที่อยู่ลูกจ้างประจำ 42834
		$cmd = " SELECT a.ID, EMPADDR_TYPE, EMPADDR_LINE1, EMPADDR_LINE2, EMPTELEPHONE, EMPZIP_CODE, DIST_CODE, PROV_CODE
						FROM EMPADDRESS a, COMMON_HISTORY b
						WHERE a.ID=b.ID
						ORDER BY a.ID, EMPADDR_TYPE ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "<br>";
		while($data = $db_dpis35->get_array()){
			$PER_ADDRESS++;
			$PER_ID = trim($data[ID]);
			$ADR_TYPE = trim($data[GOVADDR_TYPE]);
			if ($ADR_TYPE==0) $ADR_TYPE = 4;
			$ADR_ROAD = trim($data[GOVADDR_LINE1]);
			$ADR_DISTRICT = trim($data[GOVADDR_LINE2]);
			$ADR_HOME_TEL = trim($data[GOVTELEPHONE]);
			$ADR_POSTCODE = trim($data[GOVZIP_CODE]);
			if (trim($data[PROV_CODE])) $PV_CODE = trim($data[PROV_CODE])."00";
			$AP_CODE = trim($data[DIST_CODE]);

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

	if( $command=='FAMILY1' ){
		$cmd = " truncate table per_family ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

// ครอบครัวข้าราชการ 70253 
		$cmd = " SELECT a.ID, FATHER_NAME, MATHER_NAME, OMATHER_LNAME, PARENT_ADDR, MATE_NAME, OMATE_LNAME, WEDDING_DATE, CHILD_NO, EMER_ADDRESS
						  FROM COMMON_DETAIL a, COMMON_HISTORY b
						WHERE a.ID=b.ID AND ID_TYPE='G'
						  ORDER BY a.ID ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "<br>";
		$MAX_ID = 1;
		while($data = $db_dpis35->get_array()){
			$PER_ID = trim($data[ID]);
			$FATHER_NAME = trim($data[FATHER_NAME]);
			$PN_CODE = "";
			if (substr($FATHER_NAME, 0, 3)=="นาย") {
				$PN_CODE = "003";
				$FATHER_NAME = substr($FATHER_NAME, 3);
			} elseif (substr($FATHER_NAME, 0, 3)=="นาง") {
				$PN_CODE = "004";
				$FATHER_NAME = substr($FATHER_NAME, 3);
			}
			$MATHER_NAME = trim($data[MATHER_NAME]);
			$OMATHER_LNAME = trim($data_dpis[OMATHER_LNAME]);
			$PARENT_ADDR = trim($data[PARENT_ADDR]); 
			$MATE_NAME = trim($data[MATE_NAME]);
			$OMATE_LNAME = trim($data[OMATE_LNAME]); 
			$WEDDING_DATE = trim($data[WEDDING_DATE]);
			$CHILD_NO = trim($data[CHILD_NO]);
			$EMER_ADDRESS = trim($data[EMER_ADDRESS]);
			if ($FATHER_NAME || $FML_SURNAME) {
				$FML_GENDER = 1;
				$FML_TYPE = 1;
				$FML_ALIVE = 1;

				$cmd = " insert into PER_FAMILY (FML_ID, PER_ID, FML_TYPE, PN_CODE, FML_NAME, FML_SURNAME, FML_CARDNO, 
								FML_GENDER, FML_BIRTHDATE,	FML_ALIVE, RE_CODE, MR_CODE, UPDATE_USER, UPDATE_DATE)
								values ($MAX_ID, $PER_ID, $FML_TYPE, '$PN_CODE', '$FATHER_NAME', '$FML_SURNAME',  '$FML_CARDNO', 
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
			if (substr($MATHER_NAME, 0, 3)=="นาย") {
				$PN_CODE = "003";
				$MATHER_NAME = substr($MATHER_NAME, 3);
			} elseif (substr($MATHER_NAME, 0, 3)=="นาง") {
				$PN_CODE = "004";
				$MATHER_NAME = substr($MATHER_NAME, 3);
			}
			$FML_GENDER = 2;
			$FML_TYPE = 2;
			$FML_ALIVE = 1;

			if ($MATHER_NAME || $FML_SURNAME) {
				$cmd = " insert into PER_FAMILY (FML_ID, PER_ID, FML_TYPE, PN_CODE, FML_NAME, FML_SURNAME, FML_CARDNO, 
								FML_GENDER, FML_BIRTHDATE,	FML_ALIVE, RE_CODE, MR_CODE, UPDATE_USER, UPDATE_DATE)
								values ($MAX_ID, $PER_ID, $FML_TYPE, '$PN_CODE', '$MATHER_NAME', '$FML_SURNAME',  '$FML_CARDNO', 
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
			if (substr($MATE_NAME, 0, 3)=="นาย") {
				$PN_CODE = "003";
				$MATE_NAME = substr($MATE_NAME, 3);
			} elseif (substr($MATE_NAME, 0, 3)=="นาง") {
				$PN_CODE = "004";
				$MATE_NAME = substr($MATE_NAME, 3);
			}
			if ($PER_GENDER == 2) $FML_GENDER = 1;
			elseif ($PER_GENDER == 1) $FML_GENDER = 2;
			$FML_TYPE = 3;
			$FML_ALIVE = 1;

			if ($MATE_NAME || $FML_SURNAME) {
				$cmd = " insert into PER_FAMILY (FML_ID, PER_ID, FML_TYPE, PN_CODE, FML_NAME, FML_SURNAME, FML_CARDNO, 
								FML_GENDER, FML_BIRTHDATE,	FML_ALIVE, RE_CODE, MR_CODE, UPDATE_USER, UPDATE_DATE)
								values ($MAX_ID, $PER_ID, $FML_TYPE, '$PN_CODE', '$MATE_NAME', '$FML_SURNAME',  '$FML_CARDNO', 
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
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "<br>";
		$PER_FAMILY = 0;
		while($data = $db_dpis35->get_array()){
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
		$db_dpis35->free_result();
		$db_dpis351->free_result();
	} // end if

	if( $command=='FAMILY3' ){
		$cmd = " select max(FML_ID) as MAX_ID from PER_FAMILY ";
		$db_dpis1->send_cmd($cmd);
		$data1 = $db_dpis1->get_array();
		$MAX_ID = $data1[MAX_ID] + 1;

// ครอบครัวพนักงานราชการ 3258 
		$cmd = " SELECT ID, FATHER_RANK_CODE, FATHER_FNAME, FATHER_LNAME, MOTHER_RANK_CODE, 
						  MOTHER_FNAME, MOTHER_LNAME, SPOUSE_ID, SPOUSE_FNAME, SPOUSE_LNAME, 
						  MARRIAGE_STATE, LIFE_SPOUSE, SUN_NO, SPOUSE_RANK_CODE, USER_CREATE, 
						  to_char(CREATE_DATE,'yyyy-mm-dd') as CREATE_DATE, USER_UPDATE, 
						  to_char(UPDATE_DATE,'yyyy-mm-dd') as UPDATE_DATE
						  FROM HR_PERSONAL_EMPTEMP_FAMILY 
						  ORDER BY ID ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "<br>";
		$PER_FAMILY = 0;
		while($data = $db_dpis35->get_array()){
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
		$db_dpis35->free_result();
		$db_dpis351->free_result(); 
	} // end if

	if( $command=='POSITIONHIS' ){
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

		$cmd = " SELECT [เลขที่ตำแหน่ง] AS POS_NO, [วัน เดือน ปี] AS POS_DATE, [ตำแหน่ง] AS PL_NAME, [ระดับ] AS LEVEL_NO, [อัตราเงินเดือน] AS SALARY, [คำสั่ง] AS COMMAND
						FROM ตำแหน่งและอัตราเงินเดือน 
						ORDER BY [เลขที่ตำแหน่ง], [ลำดับ] ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "<br>";
		while($data = $db_dpis35->get_array()){
			$POS_NO = trim($data[POS_NO]);
			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_POSITION' AND OLD_CODE = '$POS_NO' ";
			$db_dpis->send_cmd($cmd);
			$db_dpis->show_error();
			$data_dpis = $db_dpis->get_array();
			$POS_ID = $data_dpis[NEW_CODE] + 0;
			if ($POS_ID > 0) {
				$cmd = " select PER_ID from PER_PERSONAL where POS_ID=$POS_ID ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$PER_ID = $data2[PER_ID] + 0;

				if ($PER_ID > 0) {
					$EFFECTIVEDATE = trim($data[POS_DATE]);
					$DOCNO = trim($data[GOVPOS_REFF]);
					$GOVPOS_YEAR = trim($data[GOVPOS_YEAR]);
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

					$PL_NAME = trim($data[PL_NAME]);
					$cmd = " select PL_CODE from PER_LINE where PL_NAME = '$PL_NAME' order by PL_CODE ";
					$db_dpis->send_cmd($cmd);
					$data_dpis = $db_dpis->get_array();
					$PL_CODE = trim($data_dpis[PL_CODE]);
					if (!$PL_CODE) {
						if ($PL_NAME=="นักวิชาประชาสัมพันธ์") $PL_CODE = "031703";
						elseif ($PL_NAME=="ปลัดเทศบาล (นักบริหารงานเทศบาล)") {
							$PL_CODE = "010117";
							$PM_CODE = "";
						} elseif ($PL_NAME=="ผู้อำนวยการกองวิชาการและแผนงาน       (นักบริหารงานทั่วไป 8)" || $PL_NAME=="ผู้อำนวยการกองวิชาการและแผนงาน (นักบริหารงานทั่วไป)") {
							$PL_CODE = "010109";
							$PM_CODE = "";
						} elseif ($PL_NAME=="รองปลัดเทศบาล (นักบริหารงานเทศบา" || $PL_NAME=="รองปลัดเทศบาล (นักบริหารงานเทศบาล)") {
							$PL_CODE = "010117"; 
							$PM_CODE = "";
						} elseif ($PL_NAME=="หัวหน้าฝ่ายบริการและประชาสัมพันธ์ (นักบริหารงานทั่วไป)") {
							$PL_CODE = "010109"; 
							$PM_CODE = "";
						} elseif ($PL_NAME=="หัวหน้าฝ่ายบริการและเผยแพร่วิชาการ (นักบริหารงานทั่วไป )") {
							$PL_CODE = "010109"; 
							$PM_CODE = "";
						} elseif ($PL_NAME=="หัวหน้าฝ่ายแผนงานและงบประมาณ") {
							$PL_CODE = ""; 
							$PM_CODE = "";
						} elseif ($PL_NAME=="หัวหน้าฝ่ายแผนงานและงบประมาณ            (นักบริหารงานทั่วไป 7)" || $PL_NAME=="หัวหน้าฝ่ายแผนงานและงบประมาณ (นักบริหารงานทั่วไป)" || 
							$PL_NAME=="หัวหน้าฝ่ายแผนงานและงบประมาณ(นักบริหารงานทั่วไป)") {
							$PL_CODE = "010109"; 
							$PM_CODE = "";
						} elseif ($PL_NAME=="หัวหน้าสำนักปลัดเทศบาล (นักบริหารงานทั่วไป)") {
							$PL_CODE = "010109"; 
							$PM_CODE = "";
						} else echo "ตำแหน่ง $PL_NAME<br>";
					}

					$MPOS_CODE = trim($data[MPOS_CODE]);
					$MPOS_NAME = trim($data[MPOS_NAME]);
					$PM_CODE = "";
					if ($MPOS_NAME) { 
						$cmd = " select PM_CODE from PER_MGT where PM_NAME = '$MPOS_NAME' ";
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

					$POH_ORG1 = "ราชการบริหารส่วนท้องถิ่น";
					$POH_ORG2 = "เทศบาลนครนนทบุรี";
					$POH_ORG3 = trim($data[DEPT_NAME]);
					$POH_ORG3 = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($POH_ORG3)));
					$POH_ORG3 = str_replace("'", "", trim($POH_ORG3));
					$POH_UNDER_ORG1 = trim($data[SECTION_NAME]);
					$POH_UNDER_ORG1 = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($POH_UNDER_ORG1)));
					$POH_UNDER_ORG1 = str_replace("'", "", trim($POH_UNDER_ORG1));
					$POH_UNDER_ORG2 = trim($data[JOB_NAME]);
					$POH_UNDER_ORG2 = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($POH_UNDER_ORG2)));
					$POH_UNDER_ORG2 = str_replace("'", "", trim($POH_UNDER_ORG2));
					$POH_ORG = trim((($POH_UNDER_ORG2=="-") ? "":$POH_UNDER_ORG2)." ".(($POH_UNDER_ORG1=="-") ? "":$POH_UNDER_ORG1)." ".
													(($POH_ORG3=="-") ? "":$POH_ORG3)." ".$POH_ORG2);
					$SALARY = $data[SALARY];
					$SAH_SALARY_UP = $data[GOVPOS_MONEY_UP];
					$SAH_SALARY_EXTRA = $data[GOVPOS_MONEY_SPEC];
					$SAH_PERCENT_UP = $data[GOVPOS_PRAMEAUN_PERCENT];
					$FLAG_TO_NAME = trim($data[FLAG_TO_NAME]);
					$SPECIALIST_NAME = trim($data[SPECIALIST_NAME]);
					$REMARK = trim($data[COMMAND]);
					$FLAG_TO_NAME_CODE = trim($data[FLAG_TO_NAME_CODE]);
					$POH_SALARY_POS = 0;
					$GOVPOS_COMCODE = trim($data[GOVPOS_COMCODE]);

					$SM_CODE = "";
					if (strpos($REMARK,"แต่งตั้งพนักงานตามโครงสร้างและแผนอัตรากำลังใหม่") !== false) $MOV_CODE = "10710"; // จัดคนลง **********************
					elseif (strpos($REMARK,"บรรจุ") !== false) $MOV_CODE = "10110"; // บรรจุ
					elseif (strpos($REMARK,"0.5 ขั้น") !== false) {
						$MOV_CODE = "21310"; // 0.5 ขั้น
						$SM_CODE = "2";
					} elseif ($REMARK=="เลื่อนขั้น 1563/2555" || $REMARK=="เลื่อนขั้น 1563/2555") $MOV_CODE = "213"; // เลื่อนขั้นเงินเดือน
					elseif (strpos($REMARK,"รับโอน") !== false) $MOV_CODE = "105"; // รับโอน
					elseif (strpos($REMARK,"ปรับเงินเดือน") !== false || strpos($REMARK,"ปรับอัตราเงินเดือน") !== false) $MOV_CODE = "21520"; // ปรับเงินเดือนตามกฏหมาย
					elseif (strpos($REMARK,"ไม่ได้รับการเลื่อนขั้นเงินเดือน") !== false) {
						$MOV_CODE = "21370"; // ไม่ได้เลื่อนขั้น
						$SM_CODE = "1";
					} elseif (strpos($REMARK,"1 ขั้น") !== false) {
						$MOV_CODE = "21320"; // 1 ขั้น
						$SM_CODE = "3";
					} elseif (strpos($REMARK,"4 %") !== false || strpos($REMARK,"4%") !== false) {
						$MOV_CODE = "21430"; // 4%
						$SM_CODE = "9";  
						$EX_CODE = "016";
					}	elseif (strpos($REMARK,"เลื่อนระดับและแต่งตั้งพนักงานเทศบาล") !== false) $MOV_CODE = "104"; // เลื่อนข้าราชการ
					else { 
						$MOV_CODE = "99999"; 
						if ($FLAG_TO_NAME) echo "ประเภทการเคลื่อนไหว->$GOVPOS_COMCODE<br>"; 
					}

					$cmd = " select MOV_TYPE from PER_MOVMENT where MOV_CODE = '$MOV_CODE' ";
					$db_dpis->send_cmd($cmd);
		//				$db_dpis->show_error();
					$data_dpis = $db_dpis->get_array();
					$MOV_TYPE = trim($data_dpis[MOV_TYPE]);

					$SALARYHIS = $POSITIONHIS = "";		
					$EX_CODE = "024";
					if ($MOV_TYPE==1 || $MOV_TYPE==3)	$POSITIONHIS = 1;		
					if ($MOV_TYPE==2 || $MOV_TYPE==3)	$SALARYHIS = 1;

					$LEVEL_NO = str_pad(trim($data[GOVPOS_C]), 2, "0", STR_PAD_LEFT);
					$LEVEL_NO = trim($LEVEL_NO)? "'".$LEVEL_NO."'" : "NULL";
					if ($LEVEL_NO=='"21"') $LEVEL_NO = '"O1"';
					if ($LEVEL_NO=='"22"') $LEVEL_NO = '"O2"';
					if ($LEVEL_NO=='"23"') $LEVEL_NO = '"O3"';
					if ($LEVEL_NO=='"24"') $LEVEL_NO = '"O4"';
					if ($LEVEL_NO=='"31"') $LEVEL_NO = '"K1"';
					if ($LEVEL_NO=='"32"') $LEVEL_NO = '"K2"';
					if ($LEVEL_NO=='"33"') $LEVEL_NO = '"K3"';
					if ($LEVEL_NO=='"34"') $LEVEL_NO = '"K4"';
					if ($LEVEL_NO=='"35"') $LEVEL_NO = '"K5"';
					if ($LEVEL_NO=='"41"') $LEVEL_NO = '"D1"';
					if ($LEVEL_NO=='"42"') $LEVEL_NO = '"D2"';
					if ($LEVEL_NO=='"51"') $LEVEL_NO = '"M1"';
					if ($LEVEL_NO=='"52"') $LEVEL_NO = '"M2"';
					if ($LEVEL_NO!="'01'" && $LEVEL_NO!=="'02'" && $LEVEL_NO!="'03'" && $LEVEL_NO!="'04'" && $LEVEL_NO!="'05'" && $LEVEL_NO!="'06'" && 
						$LEVEL_NO!="'07'" && $LEVEL_NO!="'08'" && $LEVEL_NO!="'09'" && $LEVEL_NO!="'10'" && $LEVEL_NO!="'11'" && $LEVEL_NO!="'O1'" && 
						$LEVEL_NO!="'O2'" && $LEVEL_NO!="'O3'" && $LEVEL_NO!="'K1'" && $LEVEL_NO!="'K2'" && $LEVEL_NO!="'K3'" && $LEVEL_NO!="'K4'" && 
						$LEVEL_NO!="'K5'" && $LEVEL_NO!="'D1'" && $LEVEL_NO!="'D2'" && $LEVEL_NO!="'M1'" && $LEVEL_NO!="'M2'") $LEVEL_NO = "NULL";
					$MP_FLAG_CURRENT = trim($data[MP_FLAG_CURRENT]);
					if ($MP_FLAG_CURRENT=="1") $LAST_TRANSACTION = "Y"; 
					else $LAST_TRANSACTION = "N";

					$POH_ISREAL = "Y";
					$ORDERID = $data[ORDERID];
					$ES_CODE = "02";
					$POH_SEQ_NO = $data[ORDERPIORITY];
					$POH_CMD_SEQ = $data[ORDERTH]; 

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
						} else {
							$MAX_POH_ID++;
							$PER_POSITIONHIS++;
						}
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
						} else {
							$MAX_SAH_ID++;
							$PER_SALARYHIS++;
						}
					}
				}
			} else echo "$POS_NO<br>"; 
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

	if( $command=='POSEMPHIS' ){
// การดำรงตำแหน่ง 976426
// delete from HR_POSITION_EMP where ID not in (select ID from HR_PERSONAL_EMP)
// update HR_POSITION_EMP set WORK_LINE_CODE = NULL where CATEGORY_SAL_CODE||WORK_LINE_CODE not in (select CATEGORY_SAL_CODE||WORK_LINE_CODE from HR_WORKLINE_EMP)
// update HR_POSITION_EMP set FLAG_TO_NAME_CODE = NULL where FLAG_TO_NAME_CODE not in (select FLAG_TO_NAME_CODE from HR_FLAG_TO_NAME_EMP)
// select  distinct WORK_LINE_CODE, WORK_LINE_NAME from HR_POSITION_EMP minus
// select WORK_LINE_CODE, WORK_LINE_NAME from HR_WORK_LINE_CODE

// select FLAG_TO_NAME_CODE, count(*) from hr_position_emp group by FLAG_TO_NAME_CODE order by to_number(FLAG_TO_NAME_CODE)

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
						elseif ($FLAG_TO_NAME=="จ้างและแต่งตั้งพนักงานราชการ") $MOV_CODE = "41"; 
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
		$db_dpis35->free_result();
		$db_dpis351->free_result();
	} // end if

	if( $command=='TRAIN' ){
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

		$cmd = " SELECT [เลขที่ตำแหน่ง] AS POS_NO, [สถานที่ดูงาน  หรือฝึกอบรม] AS TRN_PLACE, [วุฒิที่ได้รับ] AS TRN_DEGREE_RECEIVE, [ตั้งแต่ - ถึง] AS TRN_DATE
						FROM [ประวัติการดูงาน  หรือฝึกอบรม] 
						ORDER BY [เลขที่ตำแหน่ง] ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "<br>";
		while($data = $db_dpis35->get_array()){
			$POS_NO = trim($data[POS_NO]);
			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_POSITION' AND OLD_CODE = '$POS_NO' ";
			$db_dpis->send_cmd($cmd);
			$db_dpis->show_error();
			$data_dpis = $db_dpis->get_array();
			$POS_ID = $data_dpis[NEW_CODE] + 0;
			if ($POS_ID > 0) {
				$cmd = " select PER_ID from PER_PERSONAL where POS_ID=$POS_ID ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$PER_ID = $data2[PER_ID] + 0;

				if ($PER_ID > 0) {
					$TR_TYPE = 1;
					$TRN_PLACE = trim($data[TRN_PLACE]);
					$TRN_DEGREE_RECEIVE = trim($data[TRN_DEGREE_RECEIVE]);
					$TRN_DATE = trim($data[TRN_DATE]);
					if ($TRN_DATE=="10 พ.ค.28") {
						$TRN_STARTDATE = "1985-05-10"; 
						$TRN_ENDDATE = "1985-05-10"; 
					} elseif ($TRN_DATE=="12-14 มี.ค.40") {
						$TRN_STARTDATE = "1997-03-12"; 
						$TRN_ENDDATE = "1997-03-14"; 
					} elseif ($TRN_DATE=="12-16 ก.ย.37") {
						$TRN_STARTDATE = "1994-09-12"; 
						$TRN_ENDDATE = "1994-09-16"; 
					} elseif ($TRN_DATE=="1-3 มิ.ย.48") {
						$TRN_STARTDATE = "2005-06-01"; 
						$TRN_ENDDATE = "2005-06-03"; 
					} elseif ($TRN_DATE=="14-16 มิ.ย.49") {
						$TRN_STARTDATE = "2006-06-14"; 
						$TRN_ENDDATE = "2006-06-16"; 
					} elseif ($TRN_DATE=="15 ก.ค.34 -8 ก.ย.34") {
						$TRN_STARTDATE = "1991-07-15"; 
						$TRN_ENDDATE = "1991-09-08"; 
					} elseif ($TRN_DATE=="15 เม.ย.39- 10 พ.ค.39") {
						$TRN_STARTDATE = "1996-04-15"; 
						$TRN_ENDDATE = "1996-05-10"; 
					} elseif ($TRN_DATE=="15-26 ส.ค.48") {
						$TRN_STARTDATE = "2005-08-15"; 
						$TRN_ENDDATE = "2005-08-26"; 
					} elseif ($TRN_DATE=="16-18 ก.ค.45") {
						$TRN_STARTDATE = "2002-07-16"; 
						$TRN_ENDDATE = "2002-07-18"; 
					} elseif ($TRN_DATE=="17 ก.ย.-12 ต.ค.50") {
						$TRN_STARTDATE = "2007-09-17"; 
						$TRN_ENDDATE = "2007-10-12"; 
					} elseif ($TRN_DATE=="18 ก.ค.46") {
						$TRN_STARTDATE = "2003-07-18"; 
						$TRN_ENDDATE = "2003-07-18"; 
					} elseif ($TRN_DATE=="18 ก.พ.-14 มี.ค.51") {
						$TRN_STARTDATE = "2008-02-18"; 
						$TRN_ENDDATE = "2008-03-14"; 
					} elseif ($TRN_DATE=="18 มี.ค.39 -19 มี.ค.39") {
						$TRN_STARTDATE = "1996-03-18"; 
						$TRN_ENDDATE = "1996-03-19"; 
					} elseif ($TRN_DATE=="20 พ.ค.-7 มิ.ย.49") {
						$TRN_STARTDATE = "2006-05-20"; 
						$TRN_ENDDATE = "2006-06-07"; 
					} elseif ($TRN_DATE=="20 ส.ค.-14 ก.ย.50") {
						$TRN_STARTDATE = "2007-08-20"; 
						$TRN_ENDDATE = "2007-09-14"; 
					} elseif ($TRN_DATE=="21 และ 28 ม.ค.2538") {
						$TRN_STARTDATE = "1995-01-21"; 
						$TRN_ENDDATE = "1995-01-28"; 
					} elseif ($TRN_DATE=="21-23 พ.ค.40") {
						$TRN_STARTDATE = "1997-05-21"; 
						$TRN_ENDDATE = "1997-05-23"; 
					} elseif ($TRN_DATE=="23 พ.ค.51") {
						$TRN_STARTDATE = "2008-05-23"; 
						$TRN_ENDDATE = "2008-05-23"; 
					} elseif ($TRN_DATE=="23,31 สิงหาคม 2540") {
						$TRN_STARTDATE = "1997-08-23"; 
						$TRN_ENDDATE = "1997-08-31"; 
					} elseif ($TRN_DATE=="24 ม.ค.43-17 ก.พ.43") {
						$TRN_STARTDATE = "2000-01-24"; 
						$TRN_ENDDATE = "2000-02-17"; 
					} elseif ($TRN_DATE=="25-28 พ.ค.52") {
						$TRN_STARTDATE = "2009-05-25"; 
						$TRN_ENDDATE = "2009-05-28"; 
					} elseif ($TRN_DATE=="26 พ.ค.40 - 4 ก.ค.40") {
						$TRN_STARTDATE = "1997-05-26"; 
						$TRN_ENDDATE = "1997-07-04"; 
					} elseif ($TRN_DATE=="26 มี.ค.47") {
						$TRN_STARTDATE = "2004-03-26"; 
						$TRN_ENDDATE = "2004-03-26"; 
					} elseif ($TRN_DATE=="27 พ.ย.34-25 ม.ค.35") {
						$TRN_STARTDATE = "1991-11-27"; 
						$TRN_ENDDATE = "1992-01-25"; 
					} elseif ($TRN_DATE=="27-30 มี.ค.50") {
						$TRN_STARTDATE = "2007-03-27"; 
						$TRN_ENDDATE = "2007-03-30"; 
					} elseif ($TRN_DATE=="30 ก.ย.36") {
						$TRN_STARTDATE = "1993-09-30"; 
						$TRN_ENDDATE = "1993-09-30"; 
					} elseif ($TRN_DATE=="31 พ.ค.38") {
						$TRN_STARTDATE = "1995-05-31"; 
						$TRN_ENDDATE = "1995-05-31"; 
					} elseif ($TRN_DATE=="31 ส.ค.49-3 ก.ย.49") {
						$TRN_STARTDATE = "2006-08-31"; 
						$TRN_ENDDATE = "2006-09-03"; 
					} elseif ($TRN_DATE=="3-14 ก.ค.49") {
						$TRN_STARTDATE = "2006-07-03"; 
						$TRN_ENDDATE = "2006-07-14"; 
					} elseif ($TRN_DATE=="3-22 ธ.ค.37") {
						$TRN_STARTDATE = "1994-12-03"; 
						$TRN_ENDDATE = "1994-12-22"; 
					} elseif ($TRN_DATE=="3-4 เม.ย.38") {
						$TRN_STARTDATE = "1995-04-03"; 
						$TRN_ENDDATE = "1995-04-04"; 
					} elseif ($TRN_DATE=="4-12 พ.ค.48") {
						$TRN_STARTDATE = "2516"; 
						$TRN_ENDDATE = "2519"; 
					} elseif ($TRN_DATE=="5-13 ตุลาคม 2538") {
						$TRN_STARTDATE = "2005-10-05"; 
						$TRN_ENDDATE = "2005-10-13"; 
					} elseif ($TRN_DATE=="5-30 มิ.ย.43") {
						$TRN_STARTDATE = "2000-06-05"; 
						$TRN_ENDDATE = "2000-06-30"; 
					} elseif ($TRN_DATE=="8 มิ.ย.50") {
						$TRN_STARTDATE = "2007-06-08"; 
						$TRN_ENDDATE = "2007-06-08"; 
					} elseif ($TRN_DATE=="8-9 ก.พ.51") {
						$TRN_STARTDATE = "2008-02-08"; 
						$TRN_ENDDATE = "2008-02-09"; 
					} elseif ($TRN_DATE=="9 ก.พ.42-6 มี.ค.42") {
						$TRN_STARTDATE = "1999-02-09"; 
						$TRN_ENDDATE = "1999-03-06"; 
					} elseif ($TRN_DATE=="9 พ.ค.38-19 ก.ค.38") {
						$TRN_STARTDATE = "1995-05-09"; 
						$TRN_ENDDATE = "1995-07-19"; 
					} elseif ($TRN_DATE=="9 มี.ค.2535-3 เม.ย.2535") {
						$TRN_STARTDATE = "1992-03-09"; 
						$TRN_ENDDATE = "1992-04-03"; 
					}
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
					} else {
						$MAX_ID++;
						$PER_TRAINING++;
					}
				}
			} else echo "$POS_NO<br>"; 
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

?>