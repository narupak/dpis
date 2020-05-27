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
/* // วุฒิการศึกษา 544
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
*/
// ตำแหน่งพนักงานราชการ 587
		$cmd = " DELETE FROM PER_EMPSER_POS_NAME ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " SELECT [ระหัสตำแหน่ง] AS EP_CODE, [ตำแหน่งปัจจุบัน] AS EP_NAME, [ระหัสกลุ่ม] AS LEVEL_NO, [ค่าจ้างกลุ่ม] AS CATEGORY_SAL_NAME, 
						  [ภารกิจ] AS SALARY_START, [ย่อตำแหน่ง] AS EP_OTHERNAME, [หน้าที่ความรับผิดชอบ] AS SALARY_END, [คุณสมบัติเฉพาะตำแหน่ง] AS SALARY_DAY_END, [ค่าจ้างขั้นต่ำ] AS FLAG_USE, [ค่าจ้างขั้นสูง] AS CLUSTER_CODE, [ชื่อกลุ่มงาน] AS LEVEL_NAME, [ชื่อกลุ่มงาน 1] AS LEVEL_NAME1
						  FROM [ระหัสตำแหน่ง พรก]
						  ORDER BY [ระหัสตำแหน่ง] ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "<br>";
		$SEQ_NO = 1;
		while($data = $db_dpis35->get_array()){
			$PER_EMPSER_POS_NAME++;
			$EP_CODE = trim($data[EP_CODE]);
			$EP_NAME = trim($data[EP_NAME]);
			$EP_OTHERNAME = trim($data[EP_OTHERNAME]);
			$LEVEL_NO = trim($data[LEVEL_NO]);
			if ($LEVEL_NO=="1") $LEVEL_NO = "E1";
			elseif ($LEVEL_NO=="2") $LEVEL_NO = "E2";
			elseif ($LEVEL_NO=="3") $LEVEL_NO = "E3";
			elseif ($LEVEL_NO=="4") $LEVEL_NO = "E4";
			elseif ($LEVEL_NO=="5") $LEVEL_NO = "E5";
			elseif ($LEVEL_NO=="6") $LEVEL_NO = "S";

			$cmd = " INSERT INTO PER_EMPSER_POS_NAME(EP_CODE, EP_NAME, LEVEL_NO, EP_DECOR, EP_ACTIVE, EP_SEQ_NO, UPDATE_USER, UPDATE_DATE, EP_OTHERNAME)
							 VALUES ('$EP_CODE', '$EP_NAME', '$LEVEL_NO', 1, 1, $SEQ_NO, $UPDATE_USER, '$UPDATE_DATE', '$EP_OTHERNAME') ";
			$db_dpis->send_cmd($cmd);
//			echo "$cmd<br>==================<br>";
//			$db_dpis->show_error();
//			echo "<br>end ". ++$i  ."=======================<br>";
			$SEQ_NO++;
		} // end while						

		$cmd = " select count(EP_CODE) as COUNT_NEW from PER_EMPSER_POS_NAME ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_EMPSER_POS_NAME - $PER_EMPSER_POS_NAME - $COUNT_NEW<br>";

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
/*
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

//		$cmd = " ALTER TABLE PER_PERSONALPIC DISABLE CONSTRAINTS FK1_PER_PERSONALPIC ";
//		$db_dpis->send_cmd($cmd);
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
			if ($table[$i]=="per_bonusquota" || $table[$i]=="per_scholar" || $table[$i]=="per_salquota" || $table[$i]=="per_command" || $table[$i]=="per_decor" || $table[$i]=="per_course")
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

		$cmd = " SELECT [รหัสสังกัด] AS ORG_CODE, [แขวง/ศูนย์ ฯ] AS DEPT_NAME, [สำนัก/กอง] AS SKILL2, [สังกัดคำสั่ง] AS SKILL3, [สังกัด] AS ORG_NAME1, [ย่อสังกัด] AS ORG_SHORTNAME, [สังกัดเต็ม] AS SKILL4, [ชื่อสังกัดใหม่] AS SKILL5, [ย่อชื่อสังกัดใหม่] AS SKILL6, [รหัสสังกัดรวม] AS SKILL7, [สังกัดเต็ม1] AS SKILL8, [เบิกจ่ายทางคลัง] AS SKILL9, [สังกัดเลื่อนขั้น] AS SKILL10, [สังกัดย่อ] AS SKILL11, [สังกัดกลุ่มงาน] AS SKILL12, [สังกัดส่วน] AS OT_NAME, [กิจกรรม] AS SKILL14, [สายการบังคับบัญชาใหม่] AS SKILL15, [จังหวัด] AS PV_NAME, [จังหวัด1] AS SKILL16, [ลำดับคลังจังหวัด กพ] AS SKILL17, [ผลผลิต] AS SKILL18, [ลำดับผลผลิต] AS SKILL19, [สังกัดโบนัส] AS SKILL20, [ลำดับผลผลิตทั้งหมด] AS SKILL21, [งานหมวด] AS SKILL22, [สายงาน] AS SKILL23, [รหัสสังกัดพี่ปุ๊] AS SKILL24, [ชื่อสังกัดพี่ปุ๊] AS SKILL25, [สังกัดรวมแต่ละเขต] AS SKILL26, [สังกัดเดิมส่ง ข้อมูลให้ กพ] AS SKILL27, [สังกัดข้อมูลส่ง กพ] AS ORG_NAME 
						FROM [ระหัสสังกัด พรก] 
						WHERE [รหัสสังกัด] IS NOT NULL 
						ORDER BY [รหัสสังกัด] ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		//echo "<br>";
		while($data = $db_dpis35->get_array()){
			$DEPT_CODE = trim($data[ORG_CODE]);
			$DEPT_NAME = trim($data[DEPT_NAME]);
			$ORG_NAME = trim($data[ORG_NAME]);
			$DEPT_SUBOF = trim($data[DEPT_SUBOF]);
			$DEPT_ORDER = trim($data[ORG_CODE]);
			$DEPT_WHEREBUDGET = trim($data[DEPT_WHEREBUDGET]);
			$DEPT_OLDNEWFLAG = trim($data[DEPT_OLDNEWFLAG]);
			$DEPT_AREA = trim($data[DEPT_AREA]);
			$DEPT_INOUTFLAG = trim($data[DEPT_INOUTFLAG]);
			$CHANGE_FROM = trim($data[CHANGE_FROM]);
			$DEPT_SHORTNAME = trim($data[ORG_SHORTNAME]);
			$DATE_UPDATE_DEPT = trim($data[DATE_UPDATE_DEPT]);
			$DATE_DELETE_DEPT = trim($data[DATE_DELETE_DEPT]);
			if ($DATE_DELETE_DEPT) $DATE_DELETE_DEPT = save_date($DATE_DELETE_DEPT);
			$DEPT_CHECK = trim($data[DEPT_CHECK]);
			$TYPE_FLAG = trim($data[TYPE_FLAG]);
			$DEPT_LINE = trim($data[DEPT_LINE]);
			$SORT_DEPT_LINE = trim($data[SORT_DEPT_LINE]);
			$FLAG_CONTROL = trim($data[FLAG_CONTROL]);
			$TF_PROV = trim($data[TF_PROV]);
			$PV_NAME = trim($data[PV_NAME]);
			$DEPT_SUBOF_KP = trim($data[DEPT_SUBOF_KP]);
			$OT_NAME = trim($data[OT_NAME]);

			$OL_CODE = "03"; 
			$ORG_ID_REF = $SESS_DEPARTMENT_ID;
			if ($DEPT_NAME != $ORG_NAME) {
				$cmd = " select ORG_ID from PER_ORG where ORG_NAME = '$ORG_NAME' ";
				$db_dpis2->send_cmd($cmd);
	//			$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$ORG_ID_REF = $data2[ORG_ID];
				if (!$ORG_ID_REF && $DEPT_NAME=="แขวงการทางอุตรดิตถ์ที่ 1") $ORG_ID_REF = 14126;
				elseif (!$ORG_ID_REF && $DEPT_NAME=="แขวงการทางอุตรดิตถ์ที่ 2") $ORG_ID_REF = 14126;
				elseif (!$ORG_ID_REF && $DEPT_NAME=="แขวงการทางมุกดาหาร") $ORG_ID_REF = 14132;
				elseif (!$ORG_ID_REF && $DEPT_NAME=="แขวงการทางสุรินทร์") $ORG_ID_REF = 14139;
				if ($ORG_ID_REF) $OL_CODE = "04";
			}

			$cmd = " select PV_CODE from PER_PROVINCE where PV_NAME = '$PV_NAME' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$TF_PROV = trim($data2[PV_CODE]);

			$OT_CODE = "01"; 
			if ($OT_NAME=="ภูมิภาค") $OT_CODE = "02"; 
			$AP_CODE = "NULL";
			if (!$TF_PROV) $TF_PROV = "1000";
			$CT_CODE = "140";
			$ORG_ACTIVE = 1;

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

		$cmd = " select count(ORG_ID) as COUNT_NEW from PER_ORG where DEPARTMENT_ID = $SESS_DEPARTMENT_ID and OL_CODE in ('03', '04') ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_ORG - $PER_ORG2 - $COUNT_NEW<br>";

		$cmd = " SELECT  [รหัสงาน] AS SECTION_CODE, [งาน] AS ORG_NAME, [ฝ่าย] AS SECTION_NAME 
						FROM [รหัสงาน พรก] 
						WHERE [ฝ่าย] IS NOT NULL 
						ORDER BY [รหัสงาน] ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		//echo "<br>";
		while($data = $db_dpis35->get_array()){
			$SECTION_CODE = trim($data[SECTION_CODE]);
			$SECTION_NAME = trim($data[SECTION_NAME]);
			$ORG_NAME = trim($data[ORG_NAME]);
			$arr_temp = explode(" ", $ORG_NAME);
			if ($arr_temp[2]) $ORG_NAME = $arr_temp[2];
			else $ORG_NAME = $arr_temp[1];
			$SECTION_ORDER = trim($data[SECTION_CODE]);

			$OL_CODE = "04"; 
			if ($ORG_NAME=="สำนักพัฒนาระบบบริหาร") $ORG_NAME = "สำนักงานพัฒนาระบบบริหาร";
			if ($ORG_NAME=="ส่วนบริหารงานภัยภิบัติ") $ORG_NAME = "ศูนย์อุบัติภัย";
			$cmd = " select ORG_ID from PER_ORG where ORG_NAME like '%$ORG_NAME%' and DEPARTMENT_ID = $SESS_DEPARTMENT_ID ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$ORG_ID_REF = $data2[ORG_ID];
//			if ($ORG_ID_REF) $OL_CODE = "05";
				if (!$ORG_ID_REF) echo "PER_ORG - $ORG_NAME - $SECTION_NAME<br>";

			$cmd = " select PV_CODE, OT_CODE from PER_ORG where ORG_ID = $ORG_ID_REF ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PV_CODE = trim($data2[PV_CODE]);
			$OT_CODE = trim($data2[OT_CODE]);

			$AP_CODE = "NULL";
			$CT_CODE = "140";
			$ORG_ACTIVE = 1;

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
							VALUES ('PER_ORG', '$SECTION_CODE', '$MAX_ID', $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$MAX_ID++;
			$PER_ORG3++;

		} // end while						

		$cmd = " select count(ORG_ID) as COUNT_NEW from PER_ORG where DEPARTMENT_ID = $SESS_DEPARTMENT_ID and OL_CODE in ('04') ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_ORG - $PER_ORG3 - $COUNT_NEW<br>";

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

//		$cmd = " ALTER TABLE PER_PERSONALPIC ENABLE CONSTRAINTS FK1_PER_PERSONALPIC ";
//		$db_dpis->send_cmd($cmd);
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

//		$cmd = " ALTER TABLE PER_PERSONALPIC DISABLE CONSTRAINTS FK1_PER_PERSONALPIC ";
//		$db_dpis->send_cmd($cmd);
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

		$cmd = " select max(POEMS_ID) as MAX_ID from PER_POS_EMPSER ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$POEMS_ID = $data[MAX_ID] + 1;

		$cmd = " SELECT [ลำดับที่] AS POEMS_ID, [สถานะของตำแหน่ง พรก] AS POS_STATUS, [การยุบตำแหน่ง พรก] AS SKILL2, [เพศ พรก] AS SKILL3, [ตำแหน่งเลขที่ (ลจ)] AS ORG_NAME, [ตำแหน่งเลขที่ ( พร )] AS POEMS_NO, [คำนำหน้าชื่อ] AS SKILL4, [ชื่อ] AS SKILL5, [นามสกุล] AS SKILL6, [ชื่อภาษาอังกฤษ] AS SKILL7, [ค่าตอบแทนปีก่อน] AS SKILL8, [ค่าครองชีพปีก่อน] AS SKILL9, [รวมรายได้ปีก่อนทั้งสิ้น] AS SKILL10, [ได้เลื่อนปีก่อน] AS SKILL11, [ค่าตอบแทนปัจจุบัน] AS SKILL12, [ค่าครองชีพปัจจุบัน] AS SKILL13, [รวมรายได้ปีปัจจุบันทั้งสิ้น] AS SKILL14, [ได้เลื่อนปีปัจจุบัน] AS SKILL15, [ค่าตอบแทนปีหน้า] AS PV_CODE, [ค่าครองชีพปีหน้า] AS SKILL16, [รวมรายได้ปีหน้าทั้งสิ้น] AS SKILL17, [รหัสตำแหน่ง พรก] AS EP_CODE, [รหัสงาน พรก] AS SECTION_CODE, [รหัสสังกัด พรก] AS DEPARTMENT_CODE, [ระหัสสังกัดเพื่อประโยชน์ พรก] AS SKILL21, [ระยะเวลาไปปฏิบัติงานของ พรก] AS SKILL22, [ดำรงตำแหน่งเมื่อ] AS SKILL23, [ตำแหน่ง พรก ว่างตั้งแต่วันที่] AS SKILL24, [คนครองตำแหน่ง พรก ก่อนว่าง] AS SKILL25, [การเกลี่ยอัตรากำลัง] AS SKILL26, [เกลี่ยอัตรากำลังตั้งแต่] AS SKILL27, [กำหนดเอง1] AS SKILL28, [กำหนดเอง2] AS SKILL29, [กำหนดเอง3] AS SKILL30, [กำหนดเอง4] AS SKILL31, [กำหนดเอง5] AS SKILL32, [กำหนดเอง6] AS SKILL33, [กำหนดเอง7] AS SKILL34, [กำหนดเอง8] AS SKILL35, [กำหนดเอง9] AS SKILL36, [กำหนดเอง10] AS SKILL37, [กำหนดเอง11] AS SKILL38, [กำหนดเอง12] AS SKILL39, [กำหนดเอง13] AS SKILL40, [กำหนดเอง14] AS SKILL41, [กำหนดเอง15] AS SKILL42, [กำหนดเอง16] AS SKILL43, [กำหนดเอง17] AS SKILL44, [กำหนดเอง18] AS SKILL45, [ลูกจ้างประจำครองกรอบเกษียณอายุ] AS SKILL46, [วันที่ตำแหน่ง พรก ว่าง] AS SKILL47, [ปีที่ตำแหน่ง พรก ว่าง] AS SKILL48, [เดือนที่ตำแหน่ง พรก ว่าง] AS SKILL49, [รหัสเลื่อนขั้น พรก] AS SKILL50, [รหัสเลื่อนขั้นย่อย พรก] AS SKILL51, [รหัสงดขั้น พรก] AS SKILL52, [รหัสงดขั้นย่อย พรก] AS SKILL53, [โบนัส พรก] AS SKILL54, [รหัสครั้งเงินโบนัส พรก] AS SKILL55, [รหัสทั่วไป] AS SKILL56, [หมายเหตุ] AS POEMS_REMARK, [รหัสปรับ พรก] AS SKILL58, [รหัสหลังปรับ พรก] AS SKILL59, [รหัสพิเศษ พรก] AS SKILL60, [วันเดือนปีเกิด พรก] AS SKILL61, [วันที่ พรก เกิด] AS SKILL62, [เดือนที่ พรก เกิด] AS SKILL63, [ปีที่ พรก เกิด] AS SKILL64, [วันที่เกษียณอายุ] AS SKILL65, [วันที่จ้าง] AS SKILL66, [เดือนที่จ้าง] AS SKILL67, [ปีที่จ้าง] AS SKILL68, [วันเดือนปีที่จ้าง] AS SKILL69, [วันที่จ้างครั้งแรก] AS PER_STARTDATE, [ปีที่จ้างครั้งแรก] AS SKILL71, [วันที่จ้างในสัญญาปัจจุบัน] AS PER_OCCUPYDATE, [วันที่จ้าง พรก] AS SKILL73, [วันสิ้นสุดสัญญาจ้าง พรก] AS SKILL74, [ระยะเวลาการจ้าง พรก] AS SKILL75, [สัญญาจ้างครั้งที่] AS PER_CONTACT_COUNT, [ตามคำสั่งจ้างที่] AS PER_DOCNO, [หมายเลขบัตรประชาชน พรก] AS SKILL78, [คุณวุฒิ พรก] AS SKILL79, [รวมจ้างพนักงานราชการมาแล้วรวม] AS SKILL80, [ตำแหน่งว่างเนื่องจาก] AS SKILL81, [ตำแหน่งว่างตั้งแต่วันที่] AS SKILL82, [ปฏิบัติงานเพื่อประโยชน์ตามคำสั่ง] AS SKILL83, [ระยะเวลาไปปฏิบัติงาน] AS SKILL84, [ยุบเลิกตำแหน่งตามคำสั่ง] AS SKILL85, [เคลื่อนไหวตามคำสั่ง] AS SKILL86, [คนครองตำแหน่งก่อนว่าง] AS SKILL87, [เปลี่ยนชื่อนามสกุลเมื่อ] AS SKILL88, [ได้รับโทษทางวินัยเมื่อ] AS SKILL89, [ตั้งใหม่ 1] AS SKILL90, [ตั้งใหม่ 2] AS SKILL91, [ตั้งใหม่ 3] AS SKILL92, [ตั้งใหม่ 4] AS SKILL93, [ตั้งใหม่ 5] AS SKILL94, [ตั้งใหม่ 6] AS SKILL95, [งานฝ่ายตั้งใหม่] AS SKILL96, [หมายเหตุตำแหน่ง] AS SKILL97, [เลื่อนปี 52] AS SKILL98, [เลื่อนปี 51] AS SKILL99, [เลื่อนปี 50] AS SKILLa1, [เลื่อนปี 53] AS SKILLa2, [เลื่อนปี 54] AS SKILLa3, [เลื่อนปี 55] AS SKILLa4, [เลื่อนปี 56] AS SKILLa5, [เลื่อนปี 57] AS SKILLa6, [วุฒิที่ใช้สมัครพนักงานราชการ] AS SKILLa7, [ชื่อวุฒิการศึกษาที่ใช้ในการจ้าง] AS SKILLa8, [ชื่อโรงเรียนของวุฒิที่ใช้จ้าง] AS SKILLa9, [วุฒิสูงสุดที่จบมา] AS SKILLb1, [ชื่อโรงเรียนที่จบมาสูงสุด] AS SKILLb2, [ระยะเวลาการจ้าง] AS SKILLb3, [ที่อยู่ตามทะเบียนบ้าน] AS SKILLb4, [อัตราค่าตอบแทนปัจจุบัน] AS SKILLb5, [ได้เลื่อนปีก่อน %] AS SKILLb6, [อัตราค่าตอบแทนปีก่อน] AS SKILLb7, [ได้เลื่อนปีปัจจุบัน %] AS SKILLb8, [อัตราค่าตอบแทนปีหน้า] AS SKILLb9, [เงินเลื่อนขั้นได้รับปีปัจจุบัน] AS SKILLc1, [รหัสสังกัดคำนวนวงเงินเลื่อนขั้น] AS SKILLc2, [ได้รับเงินเลื่อนขั้นเพิ่มของสาย] AS SKILLc3, [สายที่ให้เงินเพิ่ม] AS SKILLc4, [รหัสค่าจ้าง พรก] AS SKILLc5, [ระหัสขั้นค่าจ้าง พรก ปีก่อน] AS SKILLc6, [พรก ได้เลื่อนขั้นปีก่อน] AS SKILLc7, [ระหัสขั้นค่าจ้าง พรก ปีปัจจุบัน] AS SKILLc8, [พรก ได้เลื่อนขั้นปัจจุบัน] AS SKILLc9, [ระหัสขั้นค่าจ้าง พรก ปีหน้า] AS SKILLd1, [พนักงานราชการมีสิทธิเลื่อนขั้น 1 ตุลาคม] AS SKILLd2, [พนักงานราชการที่ใช้คำนวณวงเงินเลื่อนขั้น 1 กันยายน] AS SKILLd3, [รหัสสังกัดหน่วยงานที่ขอเลื่อนขั้น] AS SKILLd4, [การเปลี่ยนแปลงตำแหน่ง] AS SKILLd5, [ค่าจ้างใหม่ปี 2557  คิดงบประมาณ] AS SKILLd6, [ค่าจ้างใหม่ 1 มก 57] AS SKILLd7
						FROM [กรอบ พรก] 
						ORDER BY [ลำดับที่] ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "$cmd<br>";
		while($data = $db_dpis35->get_array()){
			$PER_POS_EMPSER++;
			$POEMS_NO = trim($data[POEMS_NO]);
			$POS_STATUS = trim($data[POS_STATUS]);
			$POEM_STATUS = 1;
			if ($POS_STATUS==3) $POEM_STATUS = 0;
			$DEPARTMENT_CODE = trim($data[DEPARTMENT_CODE]);
			$SECTION_CODE = trim($data[SECTION_CODE]);
			$EP_CODE = trim($data[EP_CODE]);
			$POEMS_REMARK = trim($data[POEMS_REMARK]);

			$cmd = " SELECT [ระหัสตำแหน่ง] AS EP_CODE, [ตำแหน่งปัจจุบัน] AS EP_NAME, [ระหัสกลุ่ม] AS LEVEL_NO, [ค่าจ้างกลุ่ม] AS CATEGORY_SAL_NAME, 
							  [ภารกิจ] AS SALARY_START, [ย่อตำแหน่ง] AS EP_OTHERNAME, [หน้าที่ความรับผิดชอบ] AS SALARY_END, [คุณสมบัติเฉพาะตำแหน่ง] AS SALARY_DAY_END, [ค่าจ้างขั้นต่ำ] AS POEM_MIN_SALARY, [ค่าจ้างขั้นสูง] AS POEM_MAX_SALARY, [ชื่อกลุ่มงาน] AS LEVEL_NAME, [ชื่อกลุ่มงาน 1] AS LEVEL_NAME1
							  FROM [ระหัสตำแหน่ง พรก]
							  WHERE [ระหัสตำแหน่ง] = $EP_CODE
							  ORDER BY [ระหัสตำแหน่ง] ";
			$db_dpis351->send_cmd($cmd);
	//		$db_dpis351->show_error();
	//		echo "$cmd<br>";
			$data1 = $db_dpis351->get_array();
			$POEM_MIN_SALARY = $data1[POEM_MIN_SALARY];
			$POEM_MAX_SALARY = $data1[POEM_MAX_SALARY];
			if (!$POEM_MIN_SALARY) $POEM_MIN_SALARY = 0;
			if (!$POEM_MAX_SALARY) $POEM_MAX_SALARY = 0;

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = '$DEPARTMENT_CODE' ";
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

			if ($POEM_STATUS==1) {
				$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
								VALUES ('PER_POS_EMPSER', '$POEMS_NO', '$POEMS_ID', $UPDATE_USER, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			}

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

//		$cmd = " ALTER TABLE PER_PERSONALPIC ENABLE CONSTRAINTS FK1_PER_PERSONALPIC ";
//		$db_dpis->send_cmd($cmd);
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

//		$cmd = " ALTER TABLE PER_PERSONALPIC DISABLE CONSTRAINTS FK1_PER_PERSONALPIC ";
//		$db_dpis->send_cmd($cmd);
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
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_PERSONAL' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_PERSONAL WHERE PER_TYPE = 3 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();
		$db_dpis351->free_result();

		$cmd = " SELECT [ลำดับที่] AS PER_ID, [สถานะของตำแหน่ง พรก] AS POEM_STATUS, [การยุบตำแหน่ง พรก] AS POEMS_REMARK, [เพศ พรก] AS PER_GENDER, 
						  [ตำแหน่งเลขที่ (ลจ)] AS POEM_NO, [ตำแหน่งเลขที่ ( พร )] AS POEMS_NO, [คำนำหน้าชื่อ] AS PN_NAME, [ชื่อ] AS PER_NAME, [นามสกุล] AS PER_SURNAME, 
						  [ชื่อภาษาอังกฤษ] AS PER_ENG_NAME, [ค่าตอบแทนปีก่อน] AS LAST_SALARY, [ค่าครองชีพปีก่อน] AS LAST_COST_OF_LIVING, [รวมรายได้ปีก่อนทั้งสิ้น] AS LAST_TOTAL_INCOME, 
						  [ได้เลื่อนปีก่อน] AS LAST_PERCENT, [ค่าตอบแทนปัจจุบัน] AS PER_SALARY, [ค่าครองชีพปัจจุบัน] AS COST_OF_LIVING, [รวมรายได้ปีปัจจุบันทั้งสิ้น] AS TOTAL_INCOME, 
						  [ได้เลื่อนปีปัจจุบัน] AS CURR_PERCENT, [ค่าตอบแทนปีหน้า] AS NEXT_SALARY, [ค่าครองชีพปีหน้า] AS NEXT_COST_OF_LIVING, [รวมรายได้ปีหน้าทั้งสิ้น] AS NEXT_TOTAL_INCOME, 
						  [รหัสตำแหน่ง พรก] AS EP_CODE, [รหัสงาน พรก] AS ORG_ID_1, [รหัสสังกัด พรก] AS ORG_ID, [ระหัสสังกัดเพื่อประโยชน์ พรก] AS ORG_ID_ASS, [ระยะเวลาไปปฏิบัติงานของ พรก] AS NULL1, 
						  [ดำรงตำแหน่งเมื่อ] AS TEXT_STARTDATE, [ตำแหน่ง พรก ว่างตั้งแต่วันที่] AS POEMS_REMARK1, [คนครองตำแหน่ง พรก ก่อนว่าง] AS OLD_PER_NAME, [การเกลี่ยอัตรากำลัง] AS POEMS_REMARK2, 
						  [เกลี่ยอัตรากำลังตั้งแต่] AS POEMS_REMARK3, [กำหนดเอง1] AS OTHER1, [กำหนดเอง2] AS OTHER2, [กำหนดเอง3] AS OTHER3, [กำหนดเอง4] AS OTHER4, [กำหนดเอง5] AS OTHER5, 
						  [กำหนดเอง6] AS OTHER6, [กำหนดเอง7] AS OTHER7, [กำหนดเอง8] AS OTHER8, [กำหนดเอง9] AS OTHER9, [กำหนดเอง10] AS OTHER10, [กำหนดเอง11] AS OTHER11, 
						  [กำหนดเอง12] AS OTHER12, [กำหนดเอง13] AS OTHER13, [กำหนดเอง14] AS OTHER14, [กำหนดเอง15] AS OTHER15, [กำหนดเอง16] AS OTHER16, [กำหนดเอง17] AS OTHER17, 
						  [กำหนดเอง18] AS OTHER18, [ลูกจ้างประจำครองกรอบเกษียณอายุ] AS PER_RETIREYEAR, [วันที่ตำแหน่ง พรก ว่าง] AS DAY1, [ปีที่ตำแหน่ง พรก ว่าง] AS YEAR1, [เดือนที่ตำแหน่ง พรก ว่าง] AS MONTH1, 
						  [รหัสเลื่อนขั้น พรก] AS NULL2, [รหัสเลื่อนขั้นย่อย พรก] AS NULL3, [รหัสงดขั้น พรก] AS NULL4, [รหัสงดขั้นย่อย พรก] AS NULL5, [โบนัส พรก] AS NULL6, [รหัสครั้งเงินโบนัส พรก] AS NULL7, 
						  [รหัสทั่วไป] AS NORMAL, [หมายเหตุ] AS DATE1, [รหัสปรับ พรก] AS NULL8, [รหัสหลังปรับ พรก] AS NULL9, [รหัสพิเศษ พรก] AS NULL10, [วันเดือนปีเกิด พรก] AS PER_BIRTHDATE, 
						  [วันที่ พรก เกิด] AS BIRTH_DAY, [เดือนที่ พรก เกิด] AS BIRTH_MONTH, [ปีที่ พรก เกิด] AS BIRTH_YEAR, [วันที่เกษียณอายุ] AS PER_RETIREDATE, [วันที่จ้าง] AS EFFECTIVE_DAY, 
						  [เดือนที่จ้าง] AS EFFECTIVE_MONTH, [ปีที่จ้าง] AS EFFECTIVE_YEAR, [วันเดือนปีที่จ้าง] AS POH_EFFECTIVEDATE, [วันที่จ้างครั้งแรก] AS PER_STARTDATE, 
						  [ปีที่จ้างครั้งแรก] AS START_YEAR, [วันที่จ้างในสัญญาปัจจุบัน] AS PER_OCCUPYDATE, [วันที่จ้าง พรก] AS POH_EFFECTIVEDATE2, [วันสิ้นสุดสัญญาจ้าง พรก] AS POH_ENDDATE, [ระยะเวลาการจ้าง พรก] AS EP_OTHERNAME, [สัญญาจ้างครั้งที่] AS PER_CONTACT_COUNT, [ตามคำสั่งจ้างที่] AS PER_DOCNO, [หมายเลขบัตรประชาชน พรก] AS PER_CARDNO, [คุณวุฒิ พรก] AS EM_NAME, [รวมจ้างพนักงานราชการมาแล้วรวม] AS LEVEL_NO, [ตำแหน่งว่างเนื่องจาก] AS CATEGORY_SAL_NAME, 
						  [ตำแหน่งว่างตั้งแต่วันที่] AS SALARY_START, [ปฏิบัติงานเพื่อประโยชน์ตามคำสั่ง] AS EP_OTHERNAME1, [ระยะเวลาไปปฏิบัติงาน] AS SALARY_END, [ยุบเลิกตำแหน่งตามคำสั่ง] AS SALARY_DAY_END, [เคลื่อนไหวตามคำสั่ง] AS CLUSTER_CODE1, [คนครองตำแหน่งก่อนว่าง] AS LEVEL_NAME2, [เปลี่ยนชื่อนามสกุลเมื่อ] AS LEVEL_NAME3, [ได้รับโทษทางวินัยเมื่อ] AS EP_NAME1, [ตั้งใหม่ 1] AS LEVEL_NO1, [ตั้งใหม่ 2] AS LEVEL_NO2, [ตั้งใหม่ 3] AS LEVEL_NO3, [ตั้งใหม่ 4] AS LEVEL_NO4, [ตั้งใหม่ 5] AS LEVEL_NO5, [ตั้งใหม่ 6] AS LEVEL_NO6, [งานฝ่ายตั้งใหม่] AS CATEGORY_SAL_NAME1, 
						  [หมายเหตุตำแหน่ง] AS SALARY_START1, [เลื่อนปี 52] AS EP_OTHERNAME2, [เลื่อนปี 51] AS CLUSTER_CODE2, [เลื่อนปี 50] AS LEVEL_NAME4, [เลื่อนปี 53] AS LEVEL_NAME5, [เลื่อนปี 54] AS EP_NAME2, [เลื่อนปี 55] AS LEVEL_NO7, [เลื่อนปี 56] AS CATEGORY_SAL_NAME2, 
						  [เลื่อนปี 57] AS SALARY_START2, [วุฒิที่ใช้สมัครพนักงานราชการ] AS EP_OTHERNAME3, [ชื่อวุฒิการศึกษาที่ใช้ในการจ้าง] AS SALARY_END1, [ชื่อโรงเรียนของวุฒิที่ใช้จ้าง] AS SALARY_DAY_END1, [วุฒิสูงสุดที่จบมา] AS CLUSTER_CODE3, [ชื่อโรงเรียนที่จบมาสูงสุด] AS LEVEL_NAME6, [ระยะเวลาการจ้าง] AS LEVEL_NAME7, [ที่อยู่ตามทะเบียนบ้าน] AS EP_NAME3, [อัตราค่าตอบแทนปัจจุบัน] AS LEVEL_NO8, [ได้เลื่อนปีก่อน %] AS CATEGORY_SAL_NAME3, 
						  [อัตราค่าตอบแทนปีก่อน] AS SALARY_START3, [ได้เลื่อนปีปัจจุบัน %] AS EP_OTHERNAME4, [อัตราค่าตอบแทนปีหน้า] AS CLUSTER_CODE4, [เงินเลื่อนขั้นได้รับปีปัจจุบัน] AS LEVEL_NAME8, [รหัสสังกัดคำนวนวงเงินเลื่อนขั้น] AS LEVEL_NAME9, [ได้รับเงินเลื่อนขั้นเพิ่มของสาย] AS EP_NAME4, [สายที่ให้เงินเพิ่ม] AS LEVEL_NO9, [รหัสค่าจ้าง พรก] AS CATEGORY_SAL_NAME4, 
						  [ระหัสขั้นค่าจ้าง พรก ปีก่อน] AS SALARY_START4, [พรก ได้เลื่อนขั้นปีก่อน] AS EP_OTHERNAME5, [ระหัสขั้นค่าจ้าง พรก ปีปัจจุบัน] AS CLUSTER_CODE5, [พรก ได้เลื่อนขั้นปัจจุบัน] AS LEVEL_NAME10, [ระหัสขั้นค่าจ้าง พรก ปีหน้า] AS LEVEL_NAME11, [พนักงานราชการมีสิทธิเลื่อนขั้น 1 ตุลาคม] AS EP_NAME5, [พนักงานราชการที่ใช้คำนวณวงเงินเลื่อนขั้น 1 กันยายน] AS LEVEL_NO10, [รหัสสังกัดหน่วยงานที่ขอเลื่อนขั้น] AS CATEGORY_SAL_NAME5, 
						  [การเปลี่ยนแปลงตำแหน่ง] AS SALARY_START5, [ค่าจ้างใหม่ปี 2557  คิดงบประมาณ] AS EP_OTHERNAME6, [ค่าจ้างใหม่ 1 มก 57] AS LEVEL_NO11
						  FROM [กรอบ พรก]
						  WHERE [ชื่อ] IS NOT NULL and [นามสกุล] IS NOT NULL 
						  ORDER BY [ลำดับที่] ";
		$db_dpis35->send_cmd($cmd);
//							WHERE and [ตำแหน่งเลขที่ ( พร )] = 3
//		$db_dpis35->show_error();
//		echo "<br>";
		while($data = $db_dpis35->get_array()){
			$PER_PERSONAL++;
			$PER_TYPE = 3;
			$PER_ID = trim($data[PER_ID]);
			$POEM_STATUS = trim($data[POEM_STATUS]);
			$POEMS_REMARK = trim($data[POEMS_REMARK]);
			$PER_GENDER = trim($data[PER_GENDER]);
			$POEMS_NO = trim($data[POEMS_NO]);
			$PN_NAME = trim($data[PN_NAME]);
			$PER_NAME = trim($data[PER_NAME]);
			$PER_SURNAME = trim($data[PER_SURNAME]);
			$PER_ENG_NAME = trim($data[PER_ENG_NAME]);
			$PER_ENG_NAME = str_replace("  ", " ", trim($data[PER_ENG_NAME]));
			$arr_temp = explode(" ", $PER_ENG_NAME);
			if ($arr_temp[2]) {
				$PER_ENG_SURNAME = $arr_temp[2];
				$PER_ENG_NAME = $arr_temp[1];
			} elseif ($arr_temp[1]) {
				$PER_ENG_SURNAME = $arr_temp[1];
				$PER_ENG_NAME = $arr_temp[0];
			}

			$PER_SALARY = $data[PER_SALARY]+0;
			$EP_CODE = trim($data[EP_CODE]);
			$ORG_ID_1 = trim($data[ORG_ID_1]);
			$ORG_ID = trim($data[ORG_ID]);
			$ORG_ID_ASS = trim($data[ORG_ID_ASS]);
			$PER_STARTDATE = save_date(trim($data[PER_STARTDATE]));
			$PER_BIRTHDATE = save_date(trim($data[PER_BIRTHDATE]));
			$PER_RETIREDATE = save_date((trim($data[PER_RETIREDATE])-543)."-10-01"); 
			$POH_EFFECTIVEDATE = save_date(trim($data[POH_EFFECTIVEDATE]));
			$$PER_DOCNO = trim($data[PER_DOCNO]);
			$PER_CARDNO = str_replace(" ", "", trim($data[PER_CARDNO]));
			$PER_OCCUPYDATE = save_date(trim($data[PER_OCCUPYDATE]));
			$POS_NUM_CODE = trim($data[POS_NUM_CODE]);
			$POS_NO = $POS_NUM_NAME.$POS_NUM_CODE;
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
			$PER_CONTACT_COUNT = trim($data[PER_CONTACT_COUNT]);
			$FLAG_KBK = trim($data[FLAG_KBK]); 
			if ($FLAG_KBK=="1") $PER_MEMBER = 1;
			else  $PER_MEMBER = 0;

			$PER_MGTSALARY = 0; // รอแก้

			$POS_ID = "NULL";
			$POEM_ID = "NULL";
			$POEMS_ID = "NULL";
			$POT_ID = "NULL";
			$PER_ORGMGT = 0;

			$cmd = " SELECT [ระหัสตำแหน่ง] AS EP_CODE, [ตำแหน่งปัจจุบัน] AS EP_NAME, [ระหัสกลุ่ม] AS LEVEL_NO, [ค่าจ้างกลุ่ม] AS CATEGORY_SAL_NAME, 
							  [ภารกิจ] AS SALARY_START, [ย่อตำแหน่ง] AS EP_OTHERNAME, [หน้าที่ความรับผิดชอบ] AS SALARY_END, [คุณสมบัติเฉพาะตำแหน่ง] AS SALARY_DAY_END, [ค่าจ้างขั้นต่ำ] AS POEM_MIN_SALARY, [ค่าจ้างขั้นสูง] AS POEM_MAX_SALARY, [ชื่อกลุ่มงาน] AS LEVEL_NAME, [ชื่อกลุ่มงาน 1] AS LEVEL_NAME1
							  FROM [ระหัสตำแหน่ง พรก]
							  WHERE [ระหัสตำแหน่ง] = $EP_CODE
							  ORDER BY [ระหัสตำแหน่ง] ";
			$db_dpis351->send_cmd($cmd);
	//		$db_dpis351->show_error();
	//		echo "$cmd<br>";
			$data1 = $db_dpis351->get_array();
			$LEVEL_NO = str_pad(trim($data1[LEVEL_NO]), 2, "E", STR_PAD_LEFT);

			$cmd = " select PN_CODE from PER_PRENAME where PN_NAME = '$PN_NAME' or PN_SHORTNAME = '$PN_NAME' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PN_CODE = $data2[PN_CODE];

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_POS_EMPSER' AND OLD_CODE = '$POEMS_NO' ";
			$db_dpis2->send_cmd($cmd);
			//$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$POEMS_ID = $data2[NEW_CODE] + 0;

			$cmd = " select DEPARTMENT_ID from PER_POS_EMPSER where POEMS_ID = $POEMS_ID ";
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
			if ($POEMS_ID==0) $POEMS_ID = "NULL";
			if (!$PAY_ID) $PAY_ID = "NULL";
			if (!$OT_CODE) $OT_CODE = "01";
			if (!$MR_CODE) $MR_CODE = "9";
			if (!$PER_STARTDATE) $PER_STARTDATE = "-";
			if (!$PER_CONTACT_COUNT) $PER_CONTACT_COUNT = "NULL";

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
			$MOV_CODE = "99999"; // รอแก้
			$ES_CODE = "02"; 
			$RE_CODE = '00';
			$PER_STATUS = 1;

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
							PER_EFFECTIVEDATE, PER_POS_REASON, PER_POS_YEAR, PER_POS_DOCTYPE, PER_POS_DOCNO, PER_POS_ORG, PER_CONTACT_COUNT)
							VALUES ($PER_ID, $PER_TYPE, '$OT_CODE', '$PN_CODE', trim('$PER_NAME'), trim('$PER_SURNAME'), 
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
							'$PER_POS_ORG', $PER_CONTACT_COUNT) ";
			$db_dpis->send_cmd($cmd);

			$cmd1 = " SELECT PER_ID FROM PER_PERSONAL WHERE PER_ID = $PER_ID "; 
			$count_data = $db_dpis1->send_cmd($cmd1);
			if (!$count_data) {
				echo "$cmd<br>==================<br>";
				$db_dpis->show_error();
				echo "<br>end ". ++$i  ."=======================<br>";
			}

			$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
							VALUES ('PER_PERSONAL', '$PER_ID', '$PER_ID', $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
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

//		$cmd = " ALTER TABLE PER_PERSONALPIC ENABLE CONSTRAINTS FK1_PER_PERSONALPIC ";
//		$db_dpis->send_cmd($cmd);
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
	
	if( $command=='NAME' ){
		$cmd = " truncate table per_namehis ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " SELECT [ลำดับที่] AS PER_ID, [เปลี่ยนชื่อนามสกุลเมื่อ] AS NH_REMARK
						  FROM [กรอบ พรก]
						  WHERE [ชื่อ] IS NOT NULL and [นามสกุล] IS NOT NULL and [เปลี่ยนชื่อนามสกุลเมื่อ] IS NOT NULL 
						  ORDER BY [ลำดับที่] ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "<br>";
		while($data = $db_dpis35->get_array()){
			$PER_NAMEHIS++; 
			$PER_ID = trim($data[PER_ID]);
			$cmd = " select PN_CODE, PER_NAME, PER_SURNAME from PER_PERSONAL	where PER_ID = $PER_ID ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PN_CODE = $data2[PN_CODE];
			$NH_NAME = trim($data2[PER_NAME]);
			$NH_SURNAME = trim($data2[PER_SURNAME]);
			$NH_REMARK = trim($data[NH_REMARK]);
			$NH_REMARK = str_replace('"', '', trim($data[NH_REMARK]));
			$NH_DOCNO = "-";

			$cmd = " INSERT INTO PER_NAMEHIS(NH_ID, PER_ID, NH_DATE, PN_CODE, NH_NAME, NH_SURNAME, NH_DOCNO, 
							UPDATE_USER, UPDATE_DATE, PER_CARDNO, NH_ORG, PN_CODE_NEW, NH_NAME_NEW, NH_SURNAME_NEW, 
							NH_BOOK_NO, NH_BOOK_DATE, NH_REMARK)
							VALUES ($PER_ID, $PER_ID, '$NH_DATE', '$PN_CODE', '$NH_NAME', '$NH_SURNAME', '$NH_DOCNO', 
							$UPDATE_USER, '$UPDATE_DATE', '$PER_CARDNO', '$NH_ORG', '$PN_CODE_NEW', '$NH_NAME_NEW', '$NH_SURNAME_NEW', 
							'$NH_BOOK_NO', '$NH_BOOK_DATE', '$NH_REMARK') ";
			$db_dpis->send_cmd($cmd);

			$cmd1 = " SELECT NH_ID FROM PER_NAMEHIS WHERE NH_ID = $PER_ID "; 
			$count_data = $db_dpis1->send_cmd($cmd1);
			if (!$count_data) {
				echo "$cmd<br>==================<br>";
				$db_dpis->show_error();
				echo "<br>end ". ++$i  ."=======================<br>";
			}
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

	if( $command=='EDUCATE' ){
		$cmd = " truncate table per_educate ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

// ประวัติการศึกษาข้าราชการ 46774
		$cmd = " SELECT a.ID, GOVED_ORDER, INS_CODE, MAJOR_CODE, EDU_LEV_CODE, C_CODE, EDU_F_CODE, 
						GOVED_BEGIN_YEAR, GOVED_END_YEAR, GOVED_TYPE, GOVED_REM, QUA_CODE, GOVED_WORK_QUA, 
						GOVED_HIGH_QUA, GOVED_ENTRY_QUA, GOVED_DET 
						FROM GOVEDU_HISTORY a, COMMON_HISTORY b
						WHERE a.ID=b.ID
						ORDER BY ID, GOVED_ORDER ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "<br>";
		$MAX_ID = 1;
		while($data = $db_dpis35->get_array()){
			$PER_ID = trim($data[ID]);
			$EDU_SEQ = $data[GOVED_ORDER];
			$QUA_CODE = trim($data[QUA_CODE]);
			$GOVED_DET = trim($data[GOVED_DET]);
			$EDU_REMARK = trim($data[GOVED_DET]);
			$EN_CODE = "";
			if ($QUA_CODE || $GOVED_DET) {
				if ($GOVED_DET) {
					$QUA_NAME = trim($data[GOVED_DET]);
				} else {
					$cmd = " SELECT QUA_NAME FROM QUALIFICATION WHERE QUA_CODE = '$QUA_CODE' ";
					$db_dpis351->send_cmd($cmd);
					$data1 = $db_dpis351->get_array();
					$QUA_NAME = trim($data1[QUA_NAME]);
				}
				if ($QUA_NAME=="ครุศาสตร์อุตสาหกรรมบัณฑิต") $EN_CODE = "4010022";
				elseif ($QUA_NAME=="วิทยาศาสตร์บัณฑิต") $EN_CODE = "4010061";
				elseif ($QUA_NAME=="แพทย์ศาสตรบัณฑิต") $EN_CODE = "4010049";
				elseif ($QUA_NAME=="วิทยาศาสตร์มหาบัณฑิต") $EN_CODE = "6010054";
				elseif ($QUA_NAME=="พัฒนบริหารศาสตร์") $EN_CODE = "6010027";
				elseif ($QUA_NAME=="ปรัชญาและศาสนา") $EN_CODE = "4010129";
				elseif ($QUA_NAME=="การจัดการภาครัฐและภาคเอกชน") $EN_CODE = "6010002";
				elseif ($QUA_NAME=="อนุปริญญาศิลปศาสตร์") $EN_CODE = "2010080";
				elseif ($QUA_NAME=="ทันตแพทย์ศาสตรบัณฑิต") $EN_CODE = "4010030";
				elseif ($QUA_NAME=="สังคมสงเคราะห์ศาสตร์") $EN_CODE = "4010156";
				elseif ($QUA_NAME=="Master of Arts") $EN_CODE = "6029001";
				elseif ($QUA_NAME=="Doctor of Engineering") $EN_CODE = "8010037";
				elseif ($QUA_NAME=="พัฒนาสังคม") $EN_CODE = "6010030";
				elseif ($QUA_NAME=="เทคโนโลยีและสื่อสารการศึกษา") $EN_CODE = "6010016";
				elseif ($QUA_NAME=="มนุษยศาสตร์") $EN_CODE = "4010053";
				elseif ($QUA_NAME=="ภูมิสถาปัตยกรรมศาสตรบัณฑิต") $EN_CODE = "4010168";
				elseif (strpos($QUA_NAME,"ปวส") !== false || strpos($QUA_NAME,"ประกาศนียบัตรวิชาชีพชั้นสูง") !== false) $EN_CODE = "3010000";
				elseif (strpos($QUA_NAME,"ปวท") !== false || strpos($QUA_NAME,"ประกาศนียบัตรวิชาชีพเทคนิค") !== false) $EN_CODE = "2010000";
				elseif (strpos($QUA_NAME,"ปวช") !== false || strpos($QUA_NAME,"ประกาศนียบัตรวิชาชีพ") !== false) $EN_CODE = "1010000";
				elseif ($QUA_NAME=="ป. 4") $EN_CODE = "0510080";
				elseif ($QUA_NAME=="ป. 6") $EN_CODE = "0510082";
				elseif ($QUA_NAME=="ป.7." || $QUA_NAME=="ป. 7") $EN_CODE = "0510083";
				elseif ($QUA_NAME=="ม. 3") $EN_CODE = "0510087";
				elseif ($QUA_NAME=="ม. 6" || $QUA_NAME=="มัธยมศึกษาปี่ที่ 6") $EN_CODE = "0510090";
				elseif ($QUA_NAME=="ม. 8") $EN_CODE = "0510092";
				elseif ($QUA_NAME=="ม.ศ. 3" || $QUA_NAME=="มศ.3") $EN_CODE = "0510095";
				elseif ($QUA_NAME=="ม.ศ. 4" || $QUA_NAME=="มศ.4") $EN_CODE = "0510096";
				elseif ($QUA_NAME=="ม.ศ. 5" || $QUA_NAME=="มศ.5" || $QUA_NAME=="ม.ศ.5.") $EN_CODE = "0510097";
				elseif ($QUA_NAME=="ม.ศ.5(วิทยาศาสตร์)" || $QUA_NAME=="ม.ศ.5 วิทยาศาสตร์") $EN_CODE = "0510099";
				elseif ($QUA_NAME=="วศ.บ. (โยธา)" || $QUA_NAME=="วศ.บ(โยธา)" || $QUA_NAME=="ว.ศ.บ.(โยธา)" || $QUA_NAME=="วศ.บ (โยธา)" || 
					$QUA_NAME=="วศบ. (โยธา)" || $QUA_NAME=="วศบ.(โยธา)" || $QUA_NAME=="วศ.บ. (โยธา) เกียรตินิยม" || $QUA_NAME=="วิศวกรรมศาสตรบัณฑิต(โยธา)" || 
					$QUA_NAME=="วศ.บ.(โยธา)เกียรตินิยมอันดับ2" || $QUA_NAME=="วศบ.(โยธา) เกียรตินิยมอันดับ2" || $QUA_NAME=="วศ.บ.(โยธา) เกียรตินิยมอันดับ2") $EN_CODE = "4010109";
				elseif ($QUA_NAME=="ศศ.บ(การจัดการทั่วไป)" || $QUA_NAME=="ศศ.บ.การจัดการทั้วไป" || $QUA_NAME=="ศศ.บ. (การจัดการทั่วไป)") $EN_CODE = "4010145";
				elseif ($QUA_NAME=="วทบ.(เคมี)" || $QUA_NAME=="วท.บ. (เคมี)") $EN_CODE = "4010091";
				elseif ($QUA_NAME=="วทบ.(เทคโนยีอุตสาหกรรม)" || $QUA_NAME=="วท.บ. (เทคโนโลยีอุตสาหกรรม)" || $QUA_NAME=="วท.บ. เทคโนโลยี่อุตสาหกรรม") $EN_CODE = "4010063";
				elseif ($QUA_NAME=="วท.บ. (ธรณีวิทยา)") $EN_CODE = "4010066";
				elseif ($QUA_NAME=="วท.บ (ภูมิศาสตร์)" || $QUA_NAME=="วทบ.(ภูมิศาสตร์)" || $QUA_NAME=="วท.บ. (ภูมิศาสตร์)") $EN_CODE = "4010073";
				elseif ($QUA_NAME=="วท.บ.สถิติ" || $QUA_NAME=="วท.บ. (สถิติ)") $EN_CODE = "4010096";
				elseif ($QUA_NAME=="วิทยาศาสตรบัณฑิต (คณิตศาสตร์)" || $QUA_NAME=="วท.บ(คณิตศาสตร์)" || $QUA_NAME=="วท.บ. (คณิตศาสตร์)") $EN_CODE = "4010090";
				elseif ($QUA_NAME=="วทบ.(ศึกษาศาสตร์)" || $QUA_NAME=="วท.บ ศึกษาศาสตร์") $EN_CODE = "4010080";
				elseif ($QUA_NAME=="วศ.บ" || $QUA_NAME=="วศบ." || $QUA_NAME=="วิศวกรรมศาสตร์" || $QUA_NAME=="ปริญญาวิศวกรรมศาสตร์บัณฑิต") $EN_CODE = "4010103";
				elseif ($QUA_NAME=="บธ.บ. (การจัดการงานก่อสร้าง)" || $QUA_NAME=="บธ.บ.การจัดการงานก่อสร้าง" || $QUA_NAME=="บธ.บ. การจัดการงานก่อสร้าง" || 
					$QUA_NAME=="บธบ.(การจัดการงานก่อสร้าง)" || $QUA_NAME=="บธ.บ (การจัดการงานก่อสร้าง)" || $QUA_NAME=="บธ.บ.(การจัดการก่อสร้าง)") $EN_CODE = "4010026";
				elseif ($QUA_NAME=="ร.บ. (การปกครอง)") $EN_CODE = "4010170";
				elseif ($QUA_NAME=="รัฐประศาสนศาสตรมหาบัณฑิต(นโยบายสาธารณะ)" || $QUA_NAME=="สาขาวิชา นโยบายสารธรณะ รป.ม.") $EN_CODE = "6010055";
				elseif ($QUA_NAME=="ศศ.บ.การพัฒนาชุมชน" || $QUA_NAME=="ศิลปศาสตร์บัณฑิต วิชาเอกการพัฒนาชุมชน") $EN_CODE = "4010173";
				elseif ($QUA_NAME=="นิเทศศาสตร์บัณฑิต") $EN_CODE = "4010035";
				elseif ($QUA_NAME=="นบ." || $QUA_NAME=="น.บ" || $QUA_NAME=="นิติศาสตร์บัณฑิต") $EN_CODE = "4010034";
				elseif ($QUA_NAME=="พบ.ม.(รัฐประศาสนศาสตร์)" || $QUA_NAME=="พัฒนบริหารศาสตร์มหาบัณฑิต(รัฐประศาสตร์)" || $QUA_NAME=="พบ.ม.รัฐประศาสนศาสตร์") $EN_CODE = "6010039";
				elseif ($QUA_NAME=="วศ.บ.วิศวกรรมโยธา" || $QUA_NAME=="วิศวกรรมศาสตร์บัณฑิต (วิศวกรรมโยธา)" || $QUA_NAME=="ป.ตรี (วิศวกรรมโยธา)") $EN_CODE = "4010171";
				elseif ($QUA_NAME=="วศ.บ.(เครื่องกล)" || $QUA_NAME=="วศ.บ (เครื่องกล)" || $QUA_NAME=="วิศวกรรมศาสตรบัณฑิต(วศ.บ.เครื่องกล)") $EN_CODE = "4010169";
				elseif ($QUA_NAME=="รัฐประศาสนศาสตรมหาบัณฑิต(รป.ม.)การจัดการภาครัฐและเอกชน" || $QUA_NAME=="ปริญญาโทรัฐประศาสนศาสตร์ มหาบัณฑิต") $EN_CODE = "6010049";
				elseif ($QUA_NAME=="วศ.ม.วิศวกรรมโยธา" || $QUA_NAME=="วิศวกรรมศาสตรมหาบัณฑิต(วิศวกรรมโยธา)") $EN_CODE = "6010084";
				elseif ($QUA_NAME=="นิติศาสตรบัณฑิต(น.บ.)") $EN_CODE = "4010034";
				elseif ($QUA_NAME=="บธ.บ.บัญชี") $EN_CODE = "4010175";
				elseif ($QUA_NAME=="ศศ.บ. (รัฐศาสตร์)") $EN_CODE = "4010140";
				elseif ($QUA_NAME=="ปริญญาโท การจัดการภาครัฐและภาคเอกชนมหาบัณฑิต") $EN_CODE = "6010002";
				elseif ($QUA_NAME=="ค.บ. (สังคมศึกษา)") $EN_CODE = "4010021";
				elseif ($QUA_NAME=="วศ.ม" || $QUA_NAME=="วิศวกรรมศาสตรมหาบัณฑิต (วศ.ม.)") $EN_CODE = "6010083";
				elseif ($QUA_NAME=="วศ.บ. (ขนส่ง)") $EN_CODE = "4010172";
				elseif ($QUA_NAME=="ศิลปศาสตรมหาบัณฑิต ศศ.ม.") $EN_CODE = "6010089";
				elseif ($QUA_NAME=="บธ.บ. (การบริหารทั่วไป)") $EN_CODE = "4010176";
				elseif ($QUA_NAME=="วศ.ม(โยธา)") $EN_CODE = "6010117";
				elseif ($QUA_NAME=="ร.บ. (รัฐประศาสนศาสตร์)") $EN_CODE = "4010177";
				elseif ($QUA_NAME=="ร.ม. (การปกครอง)") $EN_CODE = "6010118";
				elseif ($QUA_NAME=="พบ.ม") $EN_CODE = "6010027";
				elseif ($QUA_NAME=="คอบ. (โยธา)") $EN_CODE = "4010178";
				elseif ($QUA_NAME=="ศิลปศาสตรมหาบัณฑิต(นโยบายและการวางแผนสังคม)") $EN_CODE = "6010095";
				elseif ($QUA_NAME=="ประกาศนียบัตรผดุงครรภ์") $EN_CODE = "2010029";
				elseif ($QUA_NAME=="ประกาศนียบัตรพยาบาลผดุงครรภ์" || $QUA_NAME=="ประกาศนียบัตรพยาบาลและผดุงครรภ์") $EN_CODE = "2010036";
				elseif ($QUA_NAME=="ประกาศนียบัตรผู้ช่วยพยาบาล") $EN_CODE = "0510030";
				elseif ($QUA_NAME=="ประกาศนียบัตรครูเทคนิคชั้นสูง" || $QUA_NAME=="ปทส.(ประกาศนียบัตรครูเทคนิคชั้นสูง)" || $QUA_NAME=="ประกาศนียบัตรครูเทคนิคชั้นสูง (ปทส.)") $EN_CODE = " 4010001";
				elseif ($QUA_NAME=="ประกาศนียบัตรพยาบาลศาสตร์และผดุงครรภ์ชั้นสูง") $EN_CODE = "4010009";
				elseif ($QUA_NAME=="ประกาศนียบัตรวิชารังสีเทคนิค") $EN_CODE = "2010060";
				elseif ($QUA_NAME=="ประกาศนียบัตร ม.6(ปัจจุบัน)") $EN_CODE = "0510108";
				elseif ($QUA_NAME=="ประกาศนียบัตรการชลประทาน") $EN_CODE = "3010016";
				elseif ($QUA_NAME=="ม.6(ปัจจุบัน)") $EN_CODE = "0510110";
				elseif ($QUA_NAME=="ประกาศนียบัตรมัธยมศึกษาตอนปลาย(ม.6)" || $QUA_NAME=="ประกาศนียบัตรมัธยศึกษาตอนปลาย (ม.6)") $EN_CODE = "0510113";
				elseif ($QUA_NAME=="ประกาศนียบัตรประถมศึกษาปีที่6" || $QUA_NAME=="ประกาศนียบัตรประถมศึกษา(ป.6)") $EN_CODE = "0510100";
				elseif ($QUA_NAME=="ประกาศนีบัตรประถมศึกษาตอนปลาย(ป.6)") $EN_CODE = "0510114";
				elseif ($QUA_NAME=="ประกาศนียบัตรมัธยมศึกษาตอนต้น(ม.3)") $EN_CODE = "0510111";
				elseif ($QUA_NAME=="วิศวกรรมศาสตรบัณฑิต(วศ.บ.วิศวกรรมเครื่องกล)" || $QUA_NAME=="วิศวกรรมศาสตรบัณฑิต(วิศวกรรมเครื่องกล)") $EN_CODE = "4010061";
				elseif ($QUA_NAME=="ป.ตรี") $EN_CODE = "4010000";
				elseif ($QUA_NAME=="วิทยาศาสตร์บัณฑิต") $EN_CODE = "4010061";
				elseif ($QUA_NAME=="วิทยาศาสตร์บัณฑิต") $EN_CODE = "4010061";
				elseif ($QUA_NAME=="วิทยาศาสตร์บัณฑิต") $EN_CODE = "4010061";
				elseif ($QUA_NAME=="วิทยาศาสตร์บัณฑิต") $EN_CODE = "4010061";
				elseif ($QUA_NAME=="วิทยาศาสตร์บัณฑิต") $EN_CODE = "4010061";
				elseif ($QUA_NAME=="วิทยาศาสตร์บัณฑิต") $EN_CODE = "4010061";
				elseif ($QUA_NAME=="วิทยาศาสตร์บัณฑิต") $EN_CODE = "4010061";
				elseif ($QUA_NAME=="วิทยาศาสตร์บัณฑิต") $EN_CODE = "4010061";
				elseif ($QUA_NAME=="วิทยาศาสตร์บัณฑิต") $EN_CODE = "4010061";
				elseif ($QUA_NAME=="วิทยาศาสตร์บัณฑิต") $EN_CODE = "4010061";
				elseif ($QUA_NAME=="วิทยาศาสตร์บัณฑิต") $EN_CODE = "4010061";

				if (!$EN_CODE) {
					$cmd = " select EN_CODE from PER_EDUCNAME where EN_NAME = '$QUA_NAME' or EN_SHORTNAME = '$QUA_NAME' ";
					$db_dpis2->send_cmd($cmd);
		//			$db_dpis2->show_error();
					$data2 = $db_dpis2->get_array();
					$EN_CODE = $data2[EN_CODE];
				}
				if (!$EN_CODE && $QUA_NAME) echo "วุฒิการศึกษา $QUA_CODE - $QUA_NAME<br>";
			}
	
			$MAJOR_CODE = trim($data[MAJOR_CODE]);
			$EM_CODE = "";
			if ($MAJOR_CODE=='9999') $EM_CODE = $MAJOR_CODE;
			elseif ($MAJOR_CODE) {
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
			
			$FLAG_EDUCATION = trim($data[FLAG_EDUCATION]);
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

	if( $command=='ABSENT' ){
// การลา 53515 ok
		$cmd = " truncate table per_absenthis ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

// การลาข้าราชการ 23713
		$cmd = " SELECT a.ID, a.GOVABSENT_ORDER, a.ABSENT_TYPE_CODE, GOVABSENT_AMOUNT, GOVABSENT_NOTE, GOVABSENT_REFF, 
						  GOVABSENT_HALFDAY_FLAG, GOVABSENT_TYPEPERMIT, GOVABSENT_PERMITDATE, DATE_START, DATE_END, AMOUNT, 
						  HALFDAY_FLAG, GOVABSENT_MEMO 
						  FROM GOVABSENT_DATE a, GOVABSENT_DATE_DETAIL b
						  WHERE a.ID=b.ID and a.GOVABSENT_ORDER=b.GOVABSENT_ORDER and a.ABSENT_TYPE_CODE=b.ABSENT_TYPE_CODE
						  ORDER BY a.ID, a.GOVABSENT_ORDER, a.ABSENT_TYPE_CODE ";
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
				$ABSENT_TYPE = $data[LEAVE_CODE];
				$ABS_ENDDATE = trim($data[LEAVE_TO]);
				$ABS_DAY = trim($data[DAY_TOTAL]);
				$ABS_REMARK = trim($data[LEAVE_MEMO])." ".trim($data[REPRESENTATIVE]);
				if (!$ABS_DAY) $ABS_DAY = "NULL";

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
		$cmd = " SELECT [ลำดับที่] AS PER_ID, [ที่อยู่ตามทะเบียนบ้าน] AS ADR_ROAD
						  FROM [กรอบ พรก]
						  WHERE [ชื่อ] IS NOT NULL and [นามสกุล] IS NOT NULL and [ที่อยู่ตามทะเบียนบ้าน] IS NOT NULL 
						  ORDER BY [ลำดับที่] ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "<br>";
		$MAX_ID = $EDU_ID = 1;
		while($data = $db_dpis35->get_array()){
			$PER_ADDRESS++;
			$PER_ID = trim($data[PER_ID]);
			$ADR_TYPE = 2;
			$ADR_ROAD = trim($data[ADR_ROAD]);
			$arr_temp1 = $arr_temp2 = $arr_temp3 = $arr_temp4 = $arr_temp5 = $arr_temp6 = "";
			$arr_temp = explode("6.", $ADR_ROAD);
			if ($arr_temp[1]) $arr_temp6 = trim($arr_temp[1]);
			$ADR_ROAD = $arr_temp[0];
			$arr_temp = explode("5.", $ADR_ROAD);
			if ($arr_temp[1]) $arr_temp5 = trim($arr_temp[1]);
			$ADR_ROAD = $arr_temp[0];
			$arr_temp = explode("4.", $ADR_ROAD);
			if ($arr_temp[1]) $arr_temp4 = trim($arr_temp[1]);
			$ADR_ROAD = $arr_temp[0];
			$arr_temp = explode("3.", $ADR_ROAD);
			if ($arr_temp[1]) $arr_temp3 = trim($arr_temp[1]);
			$ADR_ROAD = $arr_temp[0];
			$arr_temp = explode("2.", $ADR_ROAD);
			if ($arr_temp[1]) $arr_temp2 = trim($arr_temp[1]);
			$ADR_ROAD = $arr_temp[0];
			$arr_temp = explode("1.", $ADR_ROAD);
			if ($arr_temp[1]) $arr_temp1 = trim($arr_temp[1]);
			$ADR_ROAD = $arr_temp[0];
			if (!$ADR_ROAD) {
				$ADR_ROAD = $arr_temp1;
				$arr_temp1 = $arr_temp2;
				$arr_temp2 = $arr_temp3;
				$arr_temp3 = $arr_temp4;
				$arr_temp4 = $arr_temp5;
				$arr_temp5 = $arr_temp6;
			}
			echo "$ADR_ROAD-1 $arr_temp1-2 $arr_temp2-3 $arr_temp3-4 $arr_temp4-5 $arr_temp5-6 $arr_temp6<br>";
			$ADR_DISTRICT = trim($data[GOVADDR_LINE2]);
			$ADR_HOME_TEL = trim($data[GOVTELEPHONE]);
			$ADR_POSTCODE = trim($data[GOVZIP_CODE]);
			if (trim($data[PROV_CODE])) $PV_CODE = trim($data[PROV_CODE])."00";
			$AP_CODE = trim($data[DIST_CODE]);

			$EL_CODE = $EN_CODE = $EM_CODE = $EDU_INSTITUTE = "";
			if ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพ (ปวช.)  ช่างอุตสาหกรรม  สาขาวิชาช่างไฟฟ้ากำลัง  โรงเรียนธุรกิจอาชีวะ  จ.ขอนแก่น") {
				$EL_CODE = "10";
				$EN_CODE = "1010041";
				$EM_CODE = "81026101";
				$EDU_INSTITUTE = "โรงเรียนธุรกิจอาชีวะ  จ.ขอนแก่น";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพ (ปวช.)  พาณิชยกรรม  สาขาวิชาพณิชยการ  สาขาวิชาการบัญชี โรงเรียนพังโคนพณิชยการ  จ.สกลนคร") {
				$EL_CODE = "10";
				$EN_CODE = "1010043";
				$EM_CODE = "81026101";
				$EDU_INSTITUTE = "โรงเรียนพังโคนพณิชยการ  จ.สกลนคร";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพ (ปวช.) ช่างอุตสาหกรรม  สาขาช่างก่อสร้าง  วิทยาลัยเทคนิคแพร่") {
				$EL_CODE = "10";
				$EN_CODE = "1010041";
				$EM_CODE = "81026101";
				$EDU_INSTITUTE = "วิทยาลัยเทคนิคแพร่";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพ (ปวช.) ช่างอุตสาหกรรม  สาขาวิชาเครื่องกล  สาขางานช่างยนต์  วิทยาลัยเทคนิคตรัง  จ.ตรัง") {
				$EL_CODE = "10";
				$EN_CODE = "1010041";
				$EM_CODE = "81026101";
				$EDU_INSTITUTE = "วิทยาลัยเทคนิคตรัง  จ.ตรัง";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพ (ปวช.) ช่างอุตสาหกรรม  สาขาวิชาช่างกลโรงงาน     วิทยาลัยสารพัดช่างบรรหาร-แจ่มใส  จ.สุพรรณบุรี") {
				$EL_CODE = "10";
				$EN_CODE = "1010041";
				$EM_CODE = "81026101";
				$EDU_INSTITUTE = "วิทยาลัยสารพัดช่างบรรหาร-แจ่มใส  จ.สุพรรณบุรี";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพ (ปวช.) ช่างอุตสาหกรรม  สาขาวิชาช่างก่อสร้าง  โรงเรียนโปลีเทคนิคอุดรธานี  จ.อุดรธานี") {
				$EL_CODE = "10";
				$EN_CODE = "1010041";
				$EM_CODE = "81026101";
				$EDU_INSTITUTE = "โรงเรียนโปลีเทคนิคอุดรธานี  จ.อุดรธานี";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพ (ปวช.) ช่างอุตสาหกรรม  สาขาวิชาช่างก่อสร้าง  วิทยาลัยการอาชีพบึงกาฬ  จ.หนองคาย") {
				$EL_CODE = "10";
				$EN_CODE = "1010041";
				$EM_CODE = "81026101";
				$EDU_INSTITUTE = "วิทยาลัยการอาชีพบึงกาฬ  จ.หนองคาย";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพ (ปวช.) ช่างอุตสาหกรรม  สาขาวิชาช่างก่อสร้าง  วิทยาลัยเทคนิคชัยภูมิ  จ.ชัยภูมิ") {
				$EL_CODE = "10";
				$EN_CODE = "1010041";
				$EM_CODE = "81026101";
				$EDU_INSTITUTE = "วิทยาลัยเทคนิคชัยภูมิ  จ.ชัยภูมิ";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพ (ปวช.) ช่างอุตสาหกรรม  สาขาวิชาช่างก่อสร้าง  วิทยาลัยเทคนิคเดชอุดม  จ.อุบลราชธานี") {
				$EL_CODE = "10";
				$EN_CODE = "1010041";
				$EM_CODE = "81026101";
				$EDU_INSTITUTE = "วิทยาลัยเทคนิคเดชอุดม  จ.อุบลราชธานี";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพ (ปวช.) ช่างอุตสาหกรรม  สาขาวิชาช่างก่อสร้าง  วิทยาลัยเทคนิคพิจิตร  จ.พิจิตร") {
				$EL_CODE = "10";
				$EN_CODE = "1010041";
				$EM_CODE = "81026101";
				$EDU_INSTITUTE = "วิทยาลัยเทคนิคพิจิตร  จ.พิจิตร";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพ (ปวช.) ช่างอุตสาหกรรม  สาขาวิชาช่างไฟฟ้ากำลัง  โรงเรียนโปลีเทคนิคภาคตะวันออกเฉียงเหนือ  จ.อุบลราชธานี") {
				$EL_CODE = "10";
				$EN_CODE = "1010041";
				$EM_CODE = "81026101";
				$EDU_INSTITUTE = "โรงเรียนโปลีเทคนิคภาคตะวันออกเฉียงเหนือ  จ.อุบลราชธานี";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพ (ปวช.) ช่างอุตสาหกรรม  สาขาวิชาช่างไฟฟ้ากำลัง  โรงเรียนพณิชยการช่างเทคนิคลำนารายณ์  จ.ลพบุรี") {
				$EL_CODE = "10";
				$EN_CODE = "1010041";
				$EM_CODE = "81026101";
				$EDU_INSTITUTE = "โรงเรียนพณิชยการช่างเทคนิคลำนารายณ์  จ.ลพบุรี";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพ (ปวช.) ช่างอุตสาหกรรม  สาขาวิชาช่างยนต์   วิทยาลัยเทคนิคชุมพร  จ.ชุมพร") {
				$EL_CODE = "10";
				$EN_CODE = "1010041";
				$EM_CODE = "81026101";
				$EDU_INSTITUTE = "วิทยาลัยเทคนิคชุมพร  จ.ชุมพร";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพ (ปวช.) ช่างอุตสาหกรรม  สาขิวิชาช่างไฟฟ้ากำลัง  เทคโนโลยีภาคตะวันออกเฉียงเหนือ  จ.ขอนแก่น") {
				$EL_CODE = "10";
				$EN_CODE = "1010041";
				$EM_CODE = "81026101";
				$EDU_INSTITUTE = " เทคโนโลยีภาคตะวันออกเฉียงเหนือ  จ.ขอนแก่น";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพ (ปวช.) บริหารธุรกิจ  สาขาวิชาการบัญชี  โรงเรียนนครพณิชยการ  จ.นครศรีธรรมราช") {
				$EL_CODE = "10";
				$EN_CODE = "1010041";
				$EM_CODE = "81026101";
				$EDU_INSTITUTE = "โรงเรียนนครพณิชยการ  จ.นครศรีธรรมราช";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพ (ปวช.) บริหารธุรกิจ  สาขาวิชาการบัญชี  โรงเรียนพณิชยการขอนแก่น  จ.ขอนแก่น") {
				$EL_CODE = "10";
				$EN_CODE = "1010041";
				$EM_CODE = "81026101";
				$EDU_INSTITUTE = "โรงเรียนพณิชยการขอนแก่น  จ.ขอนแก่น";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพ (ปวช.) แผนกช่างกลโรงงาน  โรงเรียนเทคนิคชุมพร  จ.ชุมพร") {
				$EL_CODE = "10";
				$EN_CODE = "1010041";
				$EM_CODE = "81026101";
				$EDU_INSTITUTE = "โรงเรียนเทคนิคชุมพร  จ.ชุมพร";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพ (ปวช.) พณิชยกรรม  โรงเรียนพณิชยการสุโขทัย") {
				$EL_CODE = "10";
				$EN_CODE = "1010041";
				$EM_CODE = "81026101";
				$EDU_INSTITUTE = "โรงเรียนพณิชยการสุโขทัย";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพ (ปวช.) พาณิชยกรรม  โรงเรียนเทคโนโลยีละโว้  จ.ลพบุรี") {
				$EL_CODE = "10";
				$EN_CODE = "1010041";
				$EM_CODE = "81026101";
				$EDU_INSTITUTE = "โรงเรียนเทคโนโลยีละโว้  จ.ลพบุรี";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพ (ปวช.) พาณิชยกรรม  สาขาวิชาการบัญชี  โรงเรียนธุรกิจอาชีวะ  จ.ขอนแก่น") {
				$EL_CODE = "10";
				$EN_CODE = "1010041";
				$EM_CODE = "81026101";
				$EDU_INSTITUTE = "โรงเรียนธุรกิจอาชีวะ  จ.ขอนแก่น";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพ (ปวช.) พาณิชยกรรม  สาขาวิชาการบัญชี  โรงเรียนโปลีเทคนิคภาคตะวันออกเฉียงเหนือ  จ.อุบลราชธานี") {
				$EL_CODE = "10";
				$EN_CODE = "1010041";
				$EM_CODE = "81026101";
				$EDU_INSTITUTE = "โรงเรียนโปลีเทคนิคภาคตะวันออกเฉียงเหนือ  จ.อุบลราชธานี";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพ (ปวช.) พาณิชยกรรม  สาขาวิชาพณิชยการ  วิทยาลัยการอาชีพวังไกรกังวล  จ.ประจวบคีรีขันธ์") {
				$EL_CODE = "10";
				$EN_CODE = "1010041";
				$EM_CODE = "81026101";
				$EDU_INSTITUTE = "วิทยาลัยการอาชีพวังไกรกังวล  จ.ประจวบคีรีขันธ์";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพ (ปวช.) พาณิชยกรรม  สาขาวิชาพณิชยการ (การขาย)  โรงเรียนพณิชยการเทพสิทธา  จ.นครปฐม") {
				$EL_CODE = "10";
				$EN_CODE = "1010041";
				$EM_CODE = "81026101";
				$EDU_INSTITUTE = "โรงเรียนพณิชยการเทพสิทธา  จ.นครปฐม";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพ (ปวช.) พาณิชยกรรม  สาขาวิชาพณิชยการ โรงเรียนธุรกิจอาชีวะ  จ.ขอนแก่น") {
				$EL_CODE = "10";
				$EN_CODE = "1010041";
				$EM_CODE = "81026101";
				$EDU_INSTITUTE = "โรงเรียนธุรกิจอาชีวะ  จ.ขอนแก่น";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพชั้นสูง (ปวส.)  ช่างอุตสาหกรรม  สาขาวิชาเครื่องกล  สาขางานเทคนิคยานยนต์  โรงเรียนโปลีเทคนิคลานนา  จ.เชียงใหม่") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "โรงเรียนโปลีเทคนิคลานนา  จ.เชียงใหม่";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพชั้นสูง (ปวส.)  ช่างอุตสาหกรรม  สาขาวิชาช่างไฟฟ้ากำลัง  วิทยาลัยสารพัดช่างสุรินทร์  จ.สุรินทร์") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "วิทยาลัยสารพัดช่างสุรินทร์  จ.สุรินทร์";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพชั้นสูง (ปวส.)  ช่างอุตสาหกรรม  สาขาวิชาช่างยนต์  โรงเรียนสุราษฎร์เทคโนโลยีช่างอุตสาหกรรม  จ.สุราษฎร์ธานี") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "โรงเรียนสุราษฎร์เทคโนโลยีช่างอุตสาหกรรม  จ.สุราษฎร์ธานี";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพชั้นสูง (ปวส.)  ช่างอุตสาหกรรม  สาขาวิชาช่างโยธา  สาขางานโยธา  วิทยาลัยเทคนิคอุบลราชธานี  จ.อุบลราชธานี") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "วิทยาลัยเทคนิคอุบลราชธานี  จ.อุบลราชธานี";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพชั้นสูง (ปวส.)  บริหารธุรกิจ  สาขาวิชาการบัญชี  โรงเรียนสหตรังอาชีวะ  จ.ตรัง") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "โรงเรียนสหตรังอาชีวะ  จ.ตรัง";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพชั้นสูง (ปวส.)  บริหารธุรกิจ  สาขาวิชาการบัญชี  วิทยาลัยอาชีวศึกษานครศรีธรรมราช  จ.นครศรีธรรมราช") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "วิทยาลัยอาชีวศึกษานครศรีธรรมราช  จ.นครศรีธรรมราช";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพชั้นสูง (ปวส.)  บริหารธุรกิจ  สาขาวิชาการบัญชี  วิทยาลัยอาชีวศึกษาร้อยเอ็ด  จ.ร้อยเอ็ด") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "วิทยาลัยอาชีวศึกษาร้อยเอ็ด  จ.ร้อยเอ็ด";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพชั้นสูง (ปวส.)  บริหารธุรกิจ  สาขาวิชาคอมพิวเตอร์ธุรกิจ  วิทยาลัยอาชีวศึกษาสุราษฎร์ธานี  จ.สุราษฎร์ธานี") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "วิทยาลัยอาชีวศึกษาสุราษฎร์ธานี  จ.สุราษฎร์ธานี";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพชั้นสูง (ปวส.)  บริหารธุรกิจ  สาขาวิชาคอมพิวเตอร์ธุรกิจ  สาขางานเทคโนโลยีสำนักงาน  วิทยาลัยอาชีวศึกษาพิษณุโลก  จ.พิษณุโลก") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "วิทยาลัยอาชีวศึกษาพิษณุโลก  จ.พิษณุโลก";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพชั้นสูง (ปวส.) เกษตรกรรม  สาขาวิชาเกษตรกรรม  วิทยาลัยเกษตรกรรมชัยภูมิ  จ.ชัยภูมิ") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "วิทยาลัยเกษตรกรรมชัยภูมิ  จ.ชัยภูมิ";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพชั้นสูง (ปวส.) คหกรรมศาสตร์  แผนกวิชาอาหารและโภชนาการ") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพชั้นสูง (ปวส.) ช่างอุตสาหกรม  คณะวิชาโยธา  สขาวิชาช่างสำรวจ  สถาบันเทคโนโลยีราชมงคลวิทยาเขตพายัพ") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "สถาบันเทคโนโลยีราชมงคลวิทยาเขตพายัพ";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพชั้นสูง (ปวส.) ช่างอุตสาหกรรม  สาขาไฟฟ้ากำลัง  สาขางานงานติดตั้งไฟฟ้า  วิทยาลัยการอาชีพนวมินทราชินีมุกดาหาร  จ.มุกดาหาร") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "วิทยาลัยการอาชีพนวมินทราชินีมุกดาหาร  จ.มุกดาหาร";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพชั้นสูง (ปวส.) ช่างอุตสาหกรรม  สาขาวิชาการก่อสร้าง  สาขางานเทคนิคการก่อสร้าง  วิทยาลัยเทคนิคน่าน") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "วิทยาลัยเทคนิคน่าน";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพชั้นสูง (ปวส.) ช่างอุตสาหกรรม  สาขาวิชาเครื่องกล  สาขางานเทคนิคยานยนต์  วิทยาลัยเทคนิคพิษณุโลก  จ.พิษณุโลก") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "วิทยาลัยเทคนิคพิษณุโลก  จ.พิษณุโลก";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพชั้นสูง (ปวส.) ช่างอุตสาหกรรม  สาขาวิชาเครื่องกล  สาขางานเทคนิคยานยนต์  วิทยาลัยสารพัดช่างกาฬสินธุ์  จ.กาฬสินธุ์") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "วิทยาลัยสารพัดช่างกาฬสินธุ์  จ.กาฬสินธุ์";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพชั้นสูง (ปวส.) ช่างอุตสาหกรรม  สาขาวิชาช่าง           ก่อสร้าง  วิทยาลัยเทคนิคแพร่") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "วิทยาลัยเทคนิคแพร่";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพชั้นสูง (ปวส.) ช่างอุตสาหกรรม  สาขาวิชาช่างก่อสร้าง  โรงเรียนโปลิเทคนิคลานนา  จ.เชียงใหม่") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "โรงเรียนโปลิเทคนิคลานนา  จ.เชียงใหม่";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพชั้นสูง (ปวส.) ช่างอุตสาหกรรม  สาขาวิชาช่างก่อสร้าง  วิทยาลัยเทคนิคขอนแก่น") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "วิทยาลัยเทคนิคขอนแก่น";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพชั้นสูง (ปวส.) ช่างอุตสาหกรรม  สาขาวิชาช่างก่อสร้าง  วิทยาลัยเทคนิคเชียงใหม่  จ.เชียงใหม่") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "วิทยาลัยเทคนิคเชียงใหม่  จ.เชียงใหม่";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพชั้นสูง (ปวส.) ช่างอุตสาหกรรม  สาขาวิชาช่างก่อสร้าง  วิทยาลัยเทคนิคดุสิต  กรุงเทพ") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "วิทยาลัยเทคนิคดุสิต  กรุงเทพ";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพชั้นสูง (ปวส.) ช่างอุตสาหกรรม  สาขาวิชาช่างก่อสร้าง  วิทยาลัยเทคนิคเพชรบุรี  จ.เพชรบุรี") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "วิทยาลัยเทคนิคเพชรบุรี  จ.เพชรบุรี";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพชั้นสูง (ปวส.) ช่างอุตสาหกรรม  สาขาวิชาช่างก่อสร้าง  วิทยาลัยเทคนิคสุรินทร์  จ.สุรินทร์") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "วิทยาลัยเทคนิคสุรินทร์  จ.สุรินทร์";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพชั้นสูง (ปวส.) ช่างอุตสาหกรรม  สาขาวิชาช่างก่อสร้าง สาขางานเทคนิคก่อสร้าง  วิทยาลัยสารพัดช่างนครปฐม  จ.นครปฐม") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "วิทยาลัยสารพัดช่างนครปฐม  จ.นครปฐม";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพชั้นสูง (ปวส.) ช่างอุตสาหกรรม  สาขาวิชาช่างไฟฟ้ากำลัง  สาขางานติดตั้งไฟฟ้า  วิทยาลัยการอาชีพโพธิ์ทอง  จ.อ่างทอง") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "วิทยาลัยการอาชีพโพธิ์ทอง  จ.อ่างทอง";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพชั้นสูง (ปวส.) ช่างอุตสาหกรรม  สาขาวิชาช่างไฟฟ้ากำลัง  สาขางานติดตั้งไฟฟ้า  วิทยาลัยเทคนิคอุตรดิตถ์  จ.อุตรดิตถ์") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "วิทยาลัยเทคนิคอุตรดิตถ์  จ.อุตรดิตถ์";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพชั้นสูง (ปวส.) ช่างอุตสาหกรรม  สาขาวิชาช่างไฟฟ้ากำลังสาขางานเครื่องกลไฟฟ้า  วิทยาลัยเทคนิคพิษณุโลก  จ.พิษณุโลก") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "วิทยาลัยเทคนิคพิษณุโลก  จ.พิษณุโลก";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพชั้นสูง (ปวส.) ช่างอุตสาหกรรม  สาขาวิชาช่างยนต์  โรงเรียนเทคโนโลยีภาคตะวันออกเฉียงเหนือ  จ.ขอนแก่น") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "โรงเรียนเทคโนโลยีภาคตะวันออกเฉียงเหนือ  จ.ขอนแก่น";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพชั้นสูง (ปวส.) ช่างอุตสาหกรรม  สาขาวิชาช่างยนต์  โรงเรียนศรีนครเทคโนโลยี  จ.นครศรีธรรมราช") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "โรงเรียนศรีนครเทคโนโลยี  จ.นครศรีธรรมราช";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพชั้นสูง (ปวส.) ช่างอุตสาหกรรม  สาขาวิชาช่างยนต์  วิทยาลัยการอาชีพบึงกาฬ   จ.หนองคาย") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "วิทยาลัยการอาชีพบึงกาฬ   จ.หนองคาย";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพชั้นสูง (ปวส.) ช่างอุตสาหกรรม  สาขาวิชาช่างยนต์  วิทยาลัยเทคนิคสุราษฎร์ธานี  จ.สุราษฎร์ธานี") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "วิทยาลัยเทคนิคสุราษฎร์ธานี  จ.สุราษฎร์ธานี";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพชั้นสูง (ปวส.) ช่างอุตสาหกรรม  สาขาวิชาช่างโยธา  เทคโนโลยีภาคตะวันออกเฉียงเหนือ จ.ขอนแก่น") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "เทคโนโลยีภาคตะวันออกเฉียงเหนือ จ.ขอนแก่น";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพชั้นสูง (ปวส.) ช่างอุตสาหกรรม  สาขาวิชาช่างโยธา  สาขางานโยธา  วิทยาลัยเทคนิคกาฬสินธุ์") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "วิทยาลัยเทคนิคกาฬสินธุ์";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพชั้นสูง (ปวส.) ช่างอุตสาหกรรม  สาขาวิชาช่างโยธา  สาขางานโยธา  วิทยาลัยเทคนิคพิษณุโลก  จ.พิษณุโลก") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "วิทยาลัยเทคนิคพิษณุโลก  จ.พิษณุโลก";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพชั้นสูง (ปวส.) ช่างอุตสาหกรรม  สาขาวิชาช่างสำรวจ  วิทยาลัยเทคนิคพิษณุโลก  จ.พิษณุโลก") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "วิทยาลัยเทคนิคพิษณุโลก  จ.พิษณุโลก";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพชั้นสูง (ปวส.) ช่างอุตสาหกรรม  สาขาวิชาช่างอิเล็กทรอนิกส์  วิทยาลัยการอาชีพวังไกรกังวล  จ.ประจวบคีรีขันธ์") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "วิทยาลัยการอาชีพวังไกรกังวล  จ.ประจวบคีรีขันธ์";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพชั้นสูง (ปวส.) ช่างอุตสาหกรรม  สาขาวิชาช่างอิเล็กทรอนิกส์เทคนิคคอมพิวเตอร์  วิทยาลัยเทคนิคแพร่") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "วิทยาลัยเทคนิคแพร่";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพชั้นสูง (ปวส.) ช่างอุตสาหกรรม  สาขาวิชาไฟฟ้า  สาขางานเทคนิคคอมพิวเตอร์  สถาบันเทคโนโลยีราชมงคล  วิทยาเขตพายัพ") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "สถาบันเทคโนโลยีราชมงคล  วิทยาเขตพายัพ";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพชั้นสูง (ปวส.) บริหารธุรกิจ  สาขาวิชาการจัดการทรัพยากรมนุษย์  วิทยาลัยการอาชีพหนองกุงศรี  จ.กาฬสินธุ์") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "วิทยาลัยการอาชีพหนองกุงศรี  จ.กาฬสินธุ์";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพชั้นสูง (ปวส.) บริหารธุรกิจ  สาขาวิชาการตลาด         โรงเรียนพณิชยการเชียงราย  จ.เชียงราย") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "โรงเรียนพณิชยการเชียงราย  จ.เชียงราย";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพชั้นสูง (ปวส.) บริหารธุรกิจ  สาขาวิชาการตลาด  โรงเรียนอุตรดิตถ์เทคโนโลยี  จ.อุตรดิตถ์") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "โรงเรียนอุตรดิตถ์เทคโนโลยี  จ.อุตรดิตถ์";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพชั้นสูง (ปวส.) บริหารธุรกิจ  สาขาวิชาการตลาด  วิทยาลัยเทคนิคศรีสะเกษ  จ.ศรีสะเกษ") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "วิทยาลัยเทคนิคศรีสะเกษ  จ.ศรีสะเกษ";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพชั้นสูง (ปวส.) บริหารธุรกิจ  สาขาวิชาการตลาด  วิทยาลัยพณิชยการธนบุรี  กรุงเทพ") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "วิทยาลัยพณิชยการธนบุรี  กรุงเทพ";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพชั้นสูง (ปวส.) บริหารธุรกิจ  สาขาวิชาการตลาด  วิทยาลัยอาชีวศึกษาลำปาง  จ.ลำปาง") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "วิทยาลัยอาชีวศึกษาลำปาง  จ.ลำปาง";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพชั้นสูง (ปวส.) บริหารธุรกิจ  สาขาวิชาการบัญชี   โรงเรียนเทคโนโลยีอาชีวศึกษาอุบลราชธานี  จ.อุบลราชธานี") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "โรงเรียนเทคโนโลยีอาชีวศึกษาอุบลราชธานี  จ.อุบลราชธานี";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพชั้นสูง (ปวส.) บริหารธุรกิจ  สาขาวิชาการบัญชี  โรงเรียนขอนแก่นเทคโนโลยีพณิชยการ  จ.ขอนแก่น") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "โรงเรียนขอนแก่นเทคโนโลยีพณิชยการ  จ.ขอนแก่น";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพชั้นสูง (ปวส.) บริหารธุรกิจ  สาขาวิชาการบัญชี  โรงเรียนขอนแก่นบริหารธุรกิจ  จ.ขอนแก่น") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "โรงเรียนขอนแก่นบริหารธุรกิจ  จ.ขอนแก่น";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพชั้นสูง (ปวส.) บริหารธุรกิจ  สาขาวิชาการบัญชี  โรงเรียนเทคโนโลยีภาคตะวันออกเฉียงเหนือ  จ.ขอนแก่น") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "โรงเรียนเทคโนโลยีภาคตะวันออกเฉียงเหนือ  จ.ขอนแก่น";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพชั้นสูง (ปวส.) บริหารธุรกิจ  สาขาวิชาการบัญชี  โรงเรียนโปลีเทคนิคภาคตะวันออกเฉียงเหนือ  จ.อุบลราชธานี") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "โรงเรียนโปลีเทคนิคภาคตะวันออกเฉียงเหนือ  จ.อุบลราชธานี";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพชั้นสูง (ปวส.) บริหารธุรกิจ  สาขาวิชาการบัญชี  โรงเรียนพณิชยการพลาญชัยร้อยเอ็ด  จ.ร้อยเอ็ด") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "โรงเรียนพณิชยการพลาญชัยร้อยเอ็ด  จ.ร้อยเอ็ด";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพชั้นสูง (ปวส.) บริหารธุรกิจ  สาขาวิชาการบัญชี  โรงเรียนพณิชยการหัวหิน  จ.ประจวบคีรีขันธ์") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "โรงเรียนพณิชยการหัวหิน  จ.ประจวบคีรีขันธ์";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพชั้นสูง (ปวส.) บริหารธุรกิจ  สาขาวิชาการบัญชี  โรงเรียนรัตนพณิชยการ  กรุงเทพ") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "โรงเรียนรัตนพณิชยการ  กรุงเทพ";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพชั้นสูง (ปวส.) บริหารธุรกิจ  สาขาวิชาการบัญชี  โรงเรียนหาดใหญ่อำนวยวิทย์บริหารธุรกิจ  จ.สงขลา") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "โรงเรียนหาดใหญ่อำนวยวิทย์บริหารธุรกิจ  จ.สงขลา";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพชั้นสูง (ปวส.) บริหารธุรกิจ  สาขาวิชาการบัญชี  วิทยาลัยการอาชีพนาหว้า  จ.นครพนม") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "วิทยาลัยการอาชีพนาหว้า  จ.นครพนม";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพชั้นสูง (ปวส.) บริหารธุรกิจ  สาขาวิชาการบัญชี  วิทยาลัยการอาชีพแม่สะเรียง  จ.แม่ฮ่องสอน") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "วิทยาลัยการอาชีพแม่สะเรียง  จ.แม่ฮ่องสอน";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพชั้นสูง (ปวส.) บริหารธุรกิจ  สาขาวิชาการบัญชี  วิทยาลัยเทคนิคนครพนม  จ.นครพนม") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "วิทยาลัยเทคนิคนครพนม  จ.นครพนม";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพชั้นสูง (ปวส.) บริหารธุรกิจ  สาขาวิชาการบัญชี  วิทยาลัยเทคนิคราชบุรี  จ.ราชบุรี") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "วิทยาลัยเทคนิคราชบุรี  จ.ราชบุรี";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพชั้นสูง (ปวส.) บริหารธุรกิจ  สาขาวิชาการบัญชี  วิทยาลัยเทคนิคสกลนคร  จ.สกลนคร") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "วิทยาลัยเทคนิคสกลนคร  จ.สกลนคร";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพชั้นสูง (ปวส.) บริหารธุรกิจ  สาขาวิชาการบัญชี  วิทยาลัยอาชีวศึกษาพิษณุโลก  จ.พิษณุโลก") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "วิทยาลัยอาชีวศึกษาพิษณุโลก  จ.พิษณุโลก";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพชั้นสูง (ปวส.) บริหารธุรกิจ  สาขาวิชาการบัญชี  วิทยาลัยอาชีวศึกษาสุโขทัย  จ.สุโขทัย") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "วิทยาลัยอาชีวศึกษาสุโขทัย  จ.สุโขทัย";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพชั้นสูง (ปวส.) บริหารธุรกิจ  สาขาวิชาการบัญชี โรงเรียนเทคนิคพณิชยการสันติพล  จ.อุดรธานี") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "โรงเรียนเทคนิคพณิชยการสันติพล  จ.อุดรธานี";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพชั้นสูง (ปวส.) บริหารธุรกิจ  สาขาวิชาการบัญชี โรงเรียนเทคนิคภาคตะวันออกเฉียงเหนือ  จ.สกลนคร") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "โรงเรียนเทคนิคภาคตะวันออกเฉียงเหนือ  จ.สกลนคร";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพชั้นสูง (ปวส.) บริหารธุรกิจ  สาขาวิชาการเลขา          นุการ  วิทยาลัยเทคนิคลพบุรี  จ.ลพบุรี") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "วิทยาลัยเทคนิคลพบุรี  จ.ลพบุรี";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพชั้นสูง (ปวส.) บริหารธุรกิจ  สาขาวิชาการเลขานุการ  วิทยาลัยเทคนิคลำพูน  จ.ลำพูน") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "วิทยาลัยเทคนิคลำพูน  จ.ลำพูน";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพชั้นสูง (ปวส.) บริหารธุรกิจ  สาขาวิชาการเลขานุการ  วิทยาลัยอาชีวศึกษาแพร่") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "วิทยาลัยอาชีวศึกษาแพร่";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพชั้นสูง (ปวส.) บริหารธุรกิจ  สาขาวิชาการเลขานุการ  สถาบันเทคโนโลยีราชมงคล  วิทยาเขตพายัพ") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "สถาบันเทคโนโลยีราชมงคล  วิทยาเขตพายัพ";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพชั้นสูง (ปวส.) บริหารธุรกิจ  สาขาวิชาคอมพิวเตอร์ธุรกิจ  โรงเรียนไชยพันธ์พงษ์เทคโนโลยี  จ.พะเยา") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "โรงเรียนไชยพันธ์พงษ์เทคโนโลยี  จ.พะเยา";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพชั้นสูง (ปวส.) บริหารธุรกิจ  สาขาวิชาคอมพิวเตอร์ธุรกิจ  โรงเรียนเทคนิคเจ้าพระยา  กรุงเทพ") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "โรงเรียนเทคนิคเจ้าพระยา  กรุงเทพ";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพชั้นสูง (ปวส.) บริหารธุรกิจ  สาขาวิชาคอมพิวเตอร์ธุรกิจ  โรงเรียนพณิชยการหัวหิน  จ.ประจวบคีรีขันธ์") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "โรงเรียนพณิชยการหัวหิน  จ.ประจวบคีรีขันธ์";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพชั้นสูง (ปวส.) บริหารธุรกิจ  สาขาวิชาคอมพิวเตอร์ธุรกิจ  โรงเรียนเอ็นเทคอินเตอร์เนชั่นแนลเทคโนโลยีหนองคาย  จ.หนองคาย") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "โรงเรียนเอ็นเทคอินเตอร์เนชั่นแนลเทคโนโลยีหนองคาย  จ.หนองคาย";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพชั้นสูง (ปวส.) บริหารธุรกิจ  สาขาวิชาคอมพิวเตอร์ธุรกิจ  วิทยาลัยการอาชีพสว่างแดนดิน  จ.สกลนคร") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "วิทยาลัยการอาชีพสว่างแดนดิน  จ.สกลนคร";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพชั้นสูง (ปวส.) บริหารธุรกิจ  สาขาวิชาคอมพิวเตอร์ธุรกิจ  วิทยาลัยเทคนิคชุมพร  จ.ชุมพร") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "วิทยาลัยเทคนิคชุมพร  จ.ชุมพร";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพชั้นสูง (ปวส.) บริหารธุรกิจ  สาขาวิชาคอมพิวเตอร์ธุรกิจ  วิทยาลัยเทคนิคเดชอุดม  จ.อุบลราชธานี") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "วิทยาลัยเทคนิคเดชอุดม  จ.อุบลราชธานี";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพชั้นสูง (ปวส.) บริหารธุรกิจ  สาขาวิชาคอมพิวเตอร์ธุรกิจ  วิทยาลัยเทคนิคเพชรบูรณ์  จ.เพชรบูรณ์") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "วิทยาลัยเทคนิคเพชรบูรณ์  จ.เพชรบูรณ์";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพชั้นสูง (ปวส.) บริหารธุรกิจ  สาขาวิชาคอมพิวเตอร์ธุรกิจ  วิทยาลัยอาชีวศึกษาอุบลราชธานี  จ.อุบลราชธานี") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "วิทยาลัยอาชีวศึกษาอุบลราชธานี  จ.อุบลราชธานี";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพชั้นสูง (ปวส.) บริหารธุรกิจ  สาขาวิชาคอมพิวเตอร์ธุรกิจ  สาขางานเทคโนโลยีสำนักงาน  วิทยาลัยเทคนิคศรีสะเกษ  จ.ศรีสะเกษ") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "วิทยาลัยเทคนิคศรีสะเกษ  จ.ศรีสะเกษ";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพเทคนิค (ปวท.) เกษตรกรรม  โรงเรียนคณาสวัสดิ์เทคโนโลยีมหาสารคาม  จ.มหาสารคาม") {
				$EL_CODE = "20";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "โรงเรียนคณาสวัสดิ์เทคโนโลยีมหาสารคาม  จ.มหาสารคาม";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพเทคนิค (ปวท.) ช่างอุตสาหกรรม  สาขาเทคนิควิศวกรรมอิเลคทรอนิคส์  วิทยาลัยเทคนิคสุรินทร์  จ.สุรินทร์") {
				$EL_CODE = "20";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "วิทยาลัยเทคนิคสุรินทร์  จ.สุรินทร์";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพเทคนิค (ปวท.) บริหารธุรกิจ  สาขาวิชาการบัญชี  โรงเรียนเทคนิคพณิชยการเชียงใหม่  จ.เชียงใหม่") {
				$EL_CODE = "20";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "โรงเรียนเทคนิคพณิชยการเชียงใหม่  จ.เชียงใหม่";
			} elseif ($ADR_ROAD=="ประกาศนียบัตรวิชาชีพเทคนิค (ปวท.) บริหารธุรกิจ  สาขาวิชาการบัญชี  วิทยาลัยเทคนิคชุมพร  จ.ชุมพร") {
				$EL_CODE = "20";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "วิทยาลัยเทคนิคชุมพร  จ.ชุมพร";
			} elseif ($ADR_ROAD=="ประถมปีที่ 6  โรงเรียนบ้านแม่ศึก  จ.เชียงใหม่") {
				$EL_CODE = "05";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "โรงเรียนบ้านแม่ศึก  จ.เชียงใหม่";
			} elseif ($ADR_ROAD=="ประถมศึกษา  โรงเรียนบ้านห้างฉัตร  จ.ลำปาง") {
				$EL_CODE = "05";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "โรงเรียนบ้านห้างฉัตร  จ.ลำปาง";
			} elseif ($ADR_ROAD=="ประถมศึกษา  ศูนย์การศึกษานอกโรงเรียน อ.เมือง  จ.ขอนแก่น") {
				$EL_CODE = "05";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "ศูนย์การศึกษานอกโรงเรียน อ.เมือง  จ.ขอนแก่น";
			} elseif ($ADR_ROAD=="ประถมศึกษาปีที่ 4  โรงเรียนบ้านทุ่งข้าวตอก ( องค์การบริหารส่วนจังหวัด )  จ.เชียงใหม่") {
				$EL_CODE = "05";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "โรงเรียนบ้านทุ่งข้าวตอก ( องค์การบริหารส่วนจังหวัด )  จ.เชียงใหม่";
			} elseif ($ADR_ROAD=="ประถมศึกษาปีที่ 4  โรงเรียนบ้านส้มป่อยศรีวิศรมิตรภาพ 161  ต.สำโรง  อ.อุทุมพรพิสัย  จ.ศรีสะเกษ") {
				$EL_CODE = "05";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "โรงเรียนบ้านส้มป่อยศรีวิศรมิตรภาพ 161  ต.สำโรง  อ.อุทุมพรพิสัย  จ.ศรีสะเกษ";
			} elseif ($ADR_ROAD=="ประถมศึกษาปีที่ 4  โรงเรียนบ้านห้วยส้ม  ต.สันกลาง  อ.สันป่าตอง  จ.เชียงใหม่") {
				$EL_CODE = "05";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "โรงเรียนบ้านห้วยส้ม  ต.สันกลาง  อ.สันป่าตอง  จ.เชียงใหม่";
			} elseif ($ADR_ROAD=="ประถมศึกษาปีที่ 6  โรงเรียนบ้านยาสิงห์ (รัฐบาล) อ.เมือง  จ.น่าน") {
				$EL_CODE = "05";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "โรงเรียนบ้านยาสิงห์ (รัฐบาล) อ.เมือง  จ.น่าน";
			} elseif ($ADR_ROAD=="ประถมศึกษาปีที่หก  ศูนย์บริการการศึกษานอกโรงเรียนอำเภอสูงเนิน  จ.แพร่") {
				$EL_CODE = "05";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "ศูนย์บริการการศึกษานอกโรงเรียนอำเภอสูงเนิน  จ.แพร่";
			} elseif ($ADR_ROAD=="ปริญญาตรีนิเทศศาสตรบัณฑิต  วิชาเอกการประชาสัมพันธ์และการโฆษณา  วิทยาลัยตะวันออกเฉียงเหนือ จ.ขอนแก่น") {
				$EL_CODE = "40";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "วิทยาลัยตะวันออกเฉียงเหนือ จ.ขอนแก่น";
			} elseif ($ADR_ROAD=="ปริญญาตรีรัฐศาสตรบัณฑิต  มหาวิทลัยราชภัฏนครสวรรค์") {
				$EL_CODE = "40";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "มหาวิทลัยราชภัฏนครสวรรค์";
			} elseif ($ADR_ROAD=="มัธยมศึกษาตอนต้น   ศูนย์บริการการศึกษานอกโรงเรียน อ.อุบลราชธานี  จ.อุบลราชธานี") {
				$EL_CODE = "01";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "ศูนย์บริการการศึกษานอกโรงเรียน อ.อุบลราชธานี  จ.อุบลราชธานี";
			} elseif ($ADR_ROAD=="มัธยมศึกษาตอนต้น  โรงเยนผ่วิทยา  จ.ขอนแก่น") {
				$EL_CODE = "01";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "โรงเยนผ่วิทยา  จ.ขอนแก่น";
			} elseif ($ADR_ROAD=="มัธยมศึกษาตอนต้น  โรงเยนสารคามพิทยาคม  ต.ตลาด  อ.เมือง  จ.มหาสารคาม") {
				$EL_CODE = "01";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "โรงเยนสารคามพิทยาคม  ต.ตลาด  อ.เมือง  จ.มหาสารคาม";
			} elseif ($ADR_ROAD=="มัธยมศึกษาตอนต้น  โรงเรียนจักรคำคณาทร  ต.ในเมือง  อ.เมือง  จ.ลำพูน") {
				$EL_CODE = "01";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "โรงเรียนจักรคำคณาทร  ต.ในเมือง  อ.เมือง  จ.ลำพูน";
			} elseif ($ADR_ROAD=="มัธยมศึกษาตอนต้น  โรงเรียนบุญวัฒนา ( รัฐบาล )  อ.เมือง  จ.นครราชสีมา") {
				$EL_CODE = "01";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "โรงเรียนบุญวัฒนา ( รัฐบาล )  อ.เมือง  จ.นครราชสีมา";
			} elseif ($ADR_ROAD=="มัธยมศึกษาตอนต้น  โรงเรียนเมธีวุฒิ  จ.ลำพูน") {
				$EL_CODE = "01";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "โรงเรียนเมธีวุฒิ  จ.ลำพูน";
			} elseif ($ADR_ROAD=="มัธยมศึกษาตอนต้น  โรงเรียนหนองนกประสาทศิลป์  จ.นครศรีธรรมราช") {
				$EL_CODE = "01";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "โรงเรียนหนองนกประสาทศิลป์  จ.นครศรีธรรมราช";
			} elseif ($ADR_ROAD=="มัธยมศึกษาตอนต้น  โรงเรียนหล่มเก่าพิทยาคม (รัฐบาล)  อ.หล่มเก่า  จ.เพชรบูรณ์") {
				$EL_CODE = "01";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "โรงเรียนหล่มเก่าพิทยาคม (รัฐบาล)  อ.หล่มเก่า  จ.เพชรบูรณ์";
			} elseif ($ADR_ROAD=="มัธยมศึกษาตอนต้น  โรงเรียนหินโงมพิทยาคม  จ.หนองคาย") {
				$EL_CODE = "01";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "โรงเรียนหินโงมพิทยาคม  จ.หนองคาย";
			} elseif ($ADR_ROAD=="มัธยมศึกษาตอนต้น  ศูนย์การศึกษานอกระบบและการศึกษาตามอัธยาศัย  อ.นครปฐม  จ.นครปฐม") {
				$EL_CODE = "01";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "ศูนย์การศึกษานอกระบบและการศึกษาตามอัธยาศัย  อ.นครปฐม  จ.นครปฐม";
			} elseif ($ADR_ROAD=="มัธยมศึกษาตอนต้น  ศูนย์การศึกษานอกโรงเรียน จ.อุดรธานี") {
				$EL_CODE = "01";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "ศูนย์การศึกษานอกโรงเรียน จ.อุดรธานี";
			} elseif ($ADR_ROAD=="มัธยมศึกษาตอนต้น  ศูนย์บริการการศึกษานอกโรงเรียน อ.เกาะคา  จ.ลำปาง") {
				$EL_CODE = "01";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "ศูนย์บริการการศึกษานอกโรงเรียน อ.เกาะคา  จ.ลำปาง";
			} elseif ($ADR_ROAD=="มัธยมศึกษาตอนต้น  ศูนย์บริการการศึกษานอกโรงเรียน อ.เมืองลำปาง  จ.ลำปาง") {
				$EL_CODE = "01";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "ศูนย์บริการการศึกษานอกโรงเรียน อ.เมืองลำปาง  จ.ลำปาง";
			} elseif ($ADR_ROAD=="มัธยมศึกษาตอนต้น  ศูนย์บริการการศึกษานอกโรงเรียน อ.สันกำแพง  จ.เชียงใหม่") {
				$EL_CODE = "01";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "ศูนย์บริการการศึกษานอกโรงเรียน อ.สันกำแพง  จ.เชียงใหม่";
			} elseif ($ADR_ROAD=="มัธยมศึกษาตอนต้น  ศูนย์บริการการศึกษานอกโรงเรียนอำเภอพาน  จ.เชียงราย") {
				$EL_CODE = "01";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "ศูนย์บริการการศึกษานอกโรงเรียนอำเภอพาน  จ.เชียงราย";
			} elseif ($ADR_ROAD=="มัธยมศึกษาตอนปลาย  โรงเรียนกันทรวิชัย  จ.มหาสารคาม") {
				$EL_CODE = "02";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "โรงเรียนกันทรวิชัย  จ.มหาสารคาม";
			} elseif ($ADR_ROAD=="มัธยมศึกษาตอนปลาย  โรงเรียนกันทรารมณ์  จ.ศรีสะเกษ") {
				$EL_CODE = "02";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "โรงเรียนกันทรารมณ์  จ.ศรีสะเกษ";
			} elseif ($ADR_ROAD=="มัธยมศึกษาตอนปลาย  โรงเรียนคณาสวัสดิ์ศึกษา  จ.มหาสารคาม") {
				$EL_CODE = "02";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "โรงเรียนคณาสวัสดิ์ศึกษา  จ.มหาสารคาม";
			} elseif ($ADR_ROAD=="มัธยมศึกษาตอนปลาย  โรงเรียนคุณหญิงเนื่องบุรี  จ.เพชรบุรี") {
				$EL_CODE = "02";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "โรงเรียนคุณหญิงเนื่องบุรี  จ.เพชรบุรี";
			} elseif ($ADR_ROAD=="มัธยมศึกษาตอนปลาย  โรงเรียนดาราวิทยาลัย  จ.เชียงใหม่") {
				$EL_CODE = "02";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "โรงเรียนดาราวิทยาลัย  จ.เชียงใหม่";
			} elseif ($ADR_ROAD=="มัธยมศึกษาตอนปลาย  โรงเรียนเมืองเชลียง  ต.หาดเสี้ยว  อ.ศรีสัชนาลัย  จ.สุโขทัย") {
				$EL_CODE = "02";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "โรงเรียนเมืองเชลียง  ต.หาดเสี้ยว  อ.ศรีสัชนาลัย  จ.สุโขทัย";
			} elseif ($ADR_ROAD=="มัธยมศึกษาตอนปลาย  โรงเรียนรัตนราษฎร์บำรุง  จ.ราชบุรี") {
				$EL_CODE = "02";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "โรงเรียนรัตนราษฎร์บำรุง  จ.ราชบุรี";
			} elseif ($ADR_ROAD=="มัธยมศึกษาตอนปลาย  โรงเรียนหนองหานวิทยา  อ.หนองหาน  จ.อุดรธานี") {
				$EL_CODE = "02";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "โรงเรียนหนองหานวิทยา  อ.หนองหาน  จ.อุดรธานี";
			} elseif ($ADR_ROAD=="มัธยมศึกษาตอนปลาย  โรงเรียนห้องสอนศึกษา  จ.แม่ฮ่องสอน") {
				$EL_CODE = "02";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "โรงเรียนห้องสอนศึกษา  จ.แม่ฮ่องสอน";
			} elseif ($ADR_ROAD=="มัธยมศึกษาตอนปลาย  โรงเรียนอัมพวันวิทยาลัย  อ.อัมพวา  จ.สมุทรสงคราม") {
				$EL_CODE = "02";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "โรงเรียนอัมพวันวิทยาลัย  อ.อัมพวา  จ.สมุทรสงคราม";
			} elseif ($ADR_ROAD=="มัธยมศึกษาตอนปลาย  ศูนย์การศึกษานอกระบบและการศึกษาตามอัธยาศัย อ.เมืองขอนแก่น  จ.ขอนแก่น") {
				$EL_CODE = "02";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "ศูนย์การศึกษานอกระบบและการศึกษาตามอัธยาศัย อ.เมืองขอนแก่น  จ.ขอนแก่น";
			} elseif ($ADR_ROAD=="มัธยมศึกษาตอนปลาย  ศูนย์การศึกษานอกโรงเรียน จ.พิษณุโลก") {
				$EL_CODE = "02";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "ศูนย์การศึกษานอกโรงเรียน จ.พิษณุโลก";
			} elseif ($ADR_ROAD=="มัธยมศึกษาตอนปลาย  ศูนย์บริการการศึกษานอกโรงเรียน อ.ดอยเต่า  จ.เชียงใหม่") {
				$EL_CODE = "02";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "ศูนย์บริการการศึกษานอกโรงเรียน อ.ดอยเต่า  จ.เชียงใหม่";
			} elseif ($ADR_ROAD=="มัธยมศึกษาตอนปลาย  ศูนย์บริการการศึกษานอกโรงเรียน อ.พรรณนานิคม  จ.สกลนคร") {
				$EL_CODE = "02";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "ศูนย์บริการการศึกษานอกโรงเรียน อ.พรรณนานิคม  จ.สกลนคร";
			} elseif ($ADR_ROAD=="มัธยมศึกษาตอนปลาย  ศูนย์บริการการศึกษานอกโรงเรียน อ.แม่ริม  จ.เชียงใหม่") {
				$EL_CODE = "02";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "ศูนย์บริการการศึกษานอกโรงเรียน อ.แม่ริม  จ.เชียงใหม่";
			} elseif ($ADR_ROAD=="มัธยมศึกษาตอนปลาย  ศูนย์บริการการศึกษานอกโรงเรียน อ.สวรรคโลก  จ.สุโขทัย") {
				$EL_CODE = "02";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "ศูนย์บริการการศึกษานอกโรงเรียน อ.สวรรคโลก  จ.สุโขทัย";
			} elseif ($ADR_ROAD=="มัธยมศึกษาตอนปลาย  ศูนย์บริการการศึกษานอกโรงเรียน อ.หล่มสัก  จ.เพชรบูรณ์") {
				$EL_CODE = "02";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "ศูนย์บริการการศึกษานอกโรงเรียน อ.หล่มสัก  จ.เพชรบูรณ์";
			} elseif ($ADR_ROAD=="มัธยมศึกษาตอนปลาย  ศูนย์บริการการศึกษานอกโรงเรียนอำเภอเชียงคำ  จ.พะเยา") {
				$EL_CODE = "02";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "ศูนย์บริการการศึกษานอกโรงเรียนอำเภอเชียงคำ  จ.พะเยา";
			} elseif ($ADR_ROAD=="มัธยมศึกษาตอนปลาย  ศูนย์บริการการศึกษานอกโรงเรียนอำเภอเมืองเชียงราย  จ.เชียงราย") {
				$EL_CODE = "02";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "ศูนย์บริการการศึกษานอกโรงเรียนอำเภอเมืองเชียงราย  จ.เชียงราย";
			} elseif ($ADR_ROAD=="มัธยมศึกษาตอนปลาย ศูนย์การศึกษานอกโรงเรียนจังหวัดน่าน") {
				$EL_CODE = "02";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "ศูนย์การศึกษานอกโรงเรียนจังหวัดน่าน";
			} elseif ($ADR_ROAD=="อนุปริญญาศิลปศาสตร์  สาขาวิชาศิลปศาสตร์  วิชาเอกการบริหารธุรกิจ  สถาบันราชภัฎมหาสารคาม  จ.มหาสารคาม") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "สถาบันราชภัฎมหาสารคาม  จ.มหาสารคาม";
			} elseif ($ADR_ROAD=="อนุปริญญาศิลปศาสตร์ ( อ.ศศ. ) บริหารธุรกิจและการจัดการ  โปรแกรมวิชาการจัดการทั่วไป  วิทยาลัยครูเพชรบูรณ์  จ.เพชรบูรณ์") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "วิทยาลัยครูเพชรบูรณ์  จ.เพชรบูรณ์";
			} elseif ($ADR_ROAD=="อนุปริญญาศิลปะศาสตร์  โปรแกรมวิชาการบริหารธุรกิจ  สถาบันราชภัฎลำปาง  จ.ลำปาง") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "สถาบันราชภัฎลำปาง  จ.ลำปาง";
			}

			$EDU_SEQ = 1;
			if (($INS_CODE || $EDU_INSTITUTE) && !$CT_CODE_EDU) $CT_CODE_EDU = "140";
			$EDU_TYPE = "";
			if ($GOVED_HIGH_QUA == "1") $EDU_TYPE .= "4";
			if ($GOVED_WORK_QUA == "1") $EDU_TYPE .= "2";
			if ($GOVED_ENTRY_QUA == "1") $EDU_TYPE .= "1";
			if (!$EDU_TYPE) $EDU_TYPE = "3";

			if (!$EDU_ENDYEAR) $EDU_ENDYEAR = '-';
			if (!$EDU_STARTYEAR) $EDU_STARTYEAR = '-';
			if (!$EDU_GRADE) $EDU_GRADE = "NULL";
			$EN_CODE = (trim($EN_CODE))? "'" . $EN_CODE . "'"  : "NULL";
			$EM_CODE = (trim($EM_CODE))? "'" . $EM_CODE . "'"  : "NULL";
			$INS_CODE = "NULL";
			$EDU_REMARK = $ADR_ROAD;
			if ($EN_CODE && $EN_CODE != "NULL") {
				$cmd = " INSERT INTO PER_EDUCATE(EDU_ID, PER_ID, EDU_SEQ, EDU_STARTYEAR, EDU_ENDYEAR, ST_CODE, CT_CODE, 
								EDU_FUND, EN_CODE, EM_CODE, INS_CODE, EDU_TYPE, UPDATE_USER, UPDATE_DATE, PER_CARDNO, EL_CODE, EDU_ENDDATE,
								EDU_GRADE, EDU_HONOR, EDU_BOOK_NO, EDU_BOOK_DATE, EDU_REMARK, EDU_INSTITUTE, CT_CODE_EDU)
								VALUES ($EDU_ID, $PER_ID, $EDU_SEQ, '$EDU_STARTYEAR', '$EDU_ENDYEAR', NULL, '$CT_CODE_EDU', NULL, $EN_CODE, $EM_CODE, 
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
				$EDU_ID++;
			} else {
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

	if( $command=='FAMILY1' ){
		$cmd = " truncate table per_family ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

// ครอบครัวข้าราชการ 70253 
		$cmd = " SELECT a.ID, FATHER_NAME, MATHER_NAME, OMATHER_LNAME, PARENT_ADDR, MATE_NAME, OMATE_LNAME, WEDDING_DATE, CHILD_NO, EMER_ADDRESS
						  FROM COMMON_DETAIL a, COMMON_HISTORY b
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

		$cmd = " SELECT a.ID, GOVCHILD_ORDER, GOVCHILD_NAME, GOVCHILD_DATE
						  FROM GOV_CHILD a, COMMON_HISTORY b
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

// บุตรลูกจ้างประจำ 11010 
		$cmd = " SELECT a.ID, EMPCHILD_ORDER, EMPCHILD_NAME, EMPCHILD_DATE
						  FROM EMP_CHILD a, COMMON_HISTORY b
						  WHERE a.ID=b.ID
						  ORDER BY a.ID ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "<br>";
		while($data = $db_dpis35->get_array()){
			$PER_ID = trim($data[ID]);
			$FML_TYPE = 4;
			$EMPCHILD_NAME = trim($data[EMPCHILD_NAME]); 
			$EMPCHILD_DATE = trim($data[EMPCHILD_DATE]);
			$FML_BIRTHDATE = ($EMPCHILD_DATE)? ((substr($EMPCHILD_DATE, 4, 4) - 543) ."-". substr($EMPCHILD_DATE, 2, 2) ."-". substr($EMPCHILD_DATE, 0, 2)) : "";
			$FML_NAME = trim($data[EMPCHILD_NAME]);
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
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "<br>";
		while($data = $db_dpis35->get_array()){
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
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "<br>";
		while($data = $db_dpis35->get_array()){
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

		$cmd = " SELECT [ลำดับที่] AS PER_ID, [ตำแหน่งเลขที่ ( พร )] AS SAH_POS_NO, [คนครองเดิม] AS FULLNAME, [ตำแหน่ง พรก ณ 1 ตค 54] AS SAH_POSITION54, 
						  [ค่าจ้าง พรก ณ 1 กย 54] AS POEM_NO, [ค่าจ้าง พรก ณ 30 กย 54] AS SAH_OLD_SALARY54, [ค่าจ้าง พรก ณ 1 ตค 54] AS SAH_SALARY54, [ได้เลื่อน 1 ตค 54 รวม %] AS SAH_PERCENT_UP, 
						  [1 ตค 54 พรก ได้เลื่อน] AS PER_SURNAME, 
						  [1 ตค 54 พรก ไม่ได้เลื่อนเพราะ] AS SAH_REMARK54, [หน่วยงานเสนอเลื่อนขั้น 1 ตค 54 %] AS ORG_ID54, [หน่วยงานเสนอเลื่อนขั้น 1 ตค 54 เป็นเงิน] AS LAST_COST_OF_LIVING, [สายเสนอเลื่อนขั้น 1 ตค 54 %] AS LAST_TOTAL_INCOME, 
						  [สายเสนอเลื่อนขั้น 1 ตค 54 เป็นเงิน] AS LAST_PERCENT, [รวมหน่วยงานและสายให้% 1 ตค 54] AS PER_SALARY, [รวมหน่วยงานและสายให้เงินเล่อนขั้น 1 ตค 54] AS COST_OF_LIVING, [อัตราค่าจ้างรวมหน่วยงานและสาย 1 ตค 54] AS TOTAL_INCOME, 
						  [กรมเสนอเลื่อนขั้น 1 ตค 54 %] AS CURR_PERCENT, [กรมเสนอเลื่อนขั้น 1 ตค 54 เป็นเงิน] AS NEXT_SALARY, [รวมเสนอเลื่อนขั้น 1 ตค 54 ทั้งสิ้น %] AS SAH_PERCENT_UP54, [รวมเสนอเลื่อนขั้น 1 ตค 54 เป็นเงินทั้งสิ้น] AS SAH_SALARY_UP54, 
						  [เงินเพิ่มการครองชีพชั่วคราว  1  ตค 54] AS EP_CODE, [รวมเป็นเงินรายได้ทั้งสิ้น 1 ตค 54] AS ORG_ID_1, [ตำแหน่ง พรก ณ 1 มค 55] AS SAH_POSITION55_UP, [ค่าตอบแทน พรก ณ 31 ธค 54] AS ORG_ID_ASS, [ค่าตอบแทน พรก ณ 1 มค 55] AS NULL1, 
						  [ค่าตอบแทนแรกบรรจุ] AS TEXT_STARTDATE, [เงินชดเชยที่ได้รับ 1 มค 55] AS POEMS_REMARK1, [อัตราแรกบรรจุใหม่ 1 มค 55] AS OLD_PER_NAME, [เงินเพิ่มการครองชั่วคราว  1 มค 55] AS POEMS_REMARK2, 
						  [รวมเป็นเงินรายได้ทั้งสิ้น 1 มค 55] AS POEMS_REMARK3, [ตำแหน่ง พรก ณ 1 ตค 55] AS SAH_POSITION55, [ค่าจ้าง พรก ณ 1 กย 55] AS OTHER2, [ค่าจ้าง พรก ณ 30 กย 55] AS SAH_OLD_SALARY55, [ค่าจ้าง พรก ณ 1 ตค 55] AS SAH_SALARY55, [1 ตค 55 พรก ได้เลื่อน] AS OTHER5, 
						  [1 ตค 55 พรก ไม่ได้เลื่อนเพราะ] AS SAH_REMARK55, [คะแนนที่ได้รับการประเมิน 1 ตค 55] AS OTHER7, [หน่วยงานเสนอเลื่อนขั้น 1 ตค 55 %] AS OTHER8, [หน่วยงานเสนอเลื่อนขั้น 1 ตค 55 เป็นเงิน] AS OTHER9, [ยอดรวมของหน่วยงาน] AS OTHER10, [รหัสหน่วยงานที่ให้] AS OTHER11, 
						  [สายเสนอเลื่อนขั้น 1 ตค 55 %] AS OTHER12, [สายเสนอเลื่อนขั้น 1 ตค 55 เป็นเงิน] AS OTHER13, [ยอดรวมค่าจ้างของสาย เป็น %] AS OTHER14, [ยอดรวมค่าจ้างของสาย] AS OTHER15, [รหัสสายที่ให้] AS OTHER16, [กรมเสนอเลื่อนขั้น 1 ตค 55 %] AS OTHER17, 
						  [กรมเสนอเลื่อนขั้น 1 ตค 55 เป็นเงิน] AS OTHER18, [รวมเสนอเลื่อนขั้น 1 ตค 55 ทั้งสิ้น %] AS PER_RETIREYEAR, [เฉพาะที่ได้รับเงิน เลื่อนขั้น 1 ตค 55 เป็นเงิน] AS DAY1, [รวมเสนอเลื่อนขั้น 1 ตค 55 เป็นเงินทั้งสิ้น] AS YEAR1, [เงินเพิ่มการครองชีพชั่วคราว  1  ตค 55] AS MONTH1, 
						  [รวมเป็นเงินรายได้ทั้งสิ้น 1 ตค 55] AS NULL2, [คำนวนวงเงินเลื่อนขั้น 1 ตค 55] AS NULL3, [เฉพาะผู้ได้รับการเลื่อนค่าตอบแทน 1 ตค 55] AS NULL4, [ยอดรวมอัตราค่าจ้างร้อยละ 6 ต้องไม่เกิน] AS NULL5, [ตำแหน่ง พรก ณ 1 ตค 56] AS SAH_POSITION56, [ค่าจ้าง พรก ณ 1 กย 56] AS NULL7, 
						  [ค่าจ้าง พรก ณ 30 กย 56] AS SAH_OLD_SALARY56, [ค่าจ้าง พรก ณ 1 ตค 56] AS SAH_SALARY56, [1 ตค 56 พรก ได้เลื่อน] AS NULL8, [1 ตค 56 พรก ไม่ได้เลื่อนเพราะ] AS SAH_REMARK56, [คะแนนผลสัมฤทธิ์ของงานที่ได้รับการประเมิน 1 เมย 56] AS NULL10, [คะแนนประเมินสมรรถนะของงานที่ได้รับการประเมิน 1 เมย 56] AS PER_BIRTHDATE, 
						  [คะแนนรวมในการประเมิน 1 เมย 56] AS BIRTH_DAY, [คะแนนผลสัมฤทธิ์ของงานที่ได้รับการประเมิน 1 ตค 56] AS BIRTH_MONTH, [คะแนนประเมินสมรรถนะของงานที่ได้รับการประเมิน 1 ตค 56] AS BIRTH_YEAR, [คะแนนรวมในการประเมิน 1 ตค 56] AS PER_RETIREDATE, [คะแนนรวมทั้งปี 2556] AS EFFECTIVE_DAY, 
						  [รหัสหน่วยงานที่ให้ 1 ตค 56] AS EFFECTIVE_MONTH, [หน่วยงานเสนอเลื่อนขั้น 1 ตค 56 %] AS EFFECTIVE_YEAR, [หน่วยงานเสนอเลื่อนขั้น 1 ตค 56 เป็นเงิน] AS POH_EFFECTIVEDATE, [หน่วยงานเสนอเลื่อนขั้น 1 ตค 56 รวมเป็นเงินทั้งสิ้น] AS PER_STARTDATE, 
						  [สายเสนอเลื่อนขั้น 1 ตค 56 %] AS START_YEAR, [สายเสนอเลื่อนขั้น 1 ตค 56 เป็นเงิน] AS PER_OCCUPYDATE, [สายเสนอเลื่อนขั้น 1 ตค 56 % รวมทั้งสิ้น] AS POH_EFFECTIVEDATE2, [สายเสนอเลื่อนขั้น 1 ตค 56 เป็นเงินรวมทั้งสิ้น] AS POH_ENDDATE, [สายเสนอเลื่อนขั้นกรณีพิเศษ 1 ตค 56] AS EP_OTHERNAME, [รหัสสายที่ให้ 1 ตค 56] AS PER_CONTACT_COUNT, [รวมหน่วยงานและสายให้% 1 ตค 56] AS PER_DOCNO, [รวมหน่วยงานและสายให้เงินเลื่อนขั้น 1 ตค 56 เป็นเงิน] AS PER_CARDNO, [รวมหน่วยงานและสายให้เงินเลื่อนขั้น 1 ตค 56 รวมเป็นเงินทั้งสิ้น] AS EM_NAME, [กรมเสนอเลื่อนขั้น 1 ตค 56 %] AS LEVEL_NO, [กรมเสนอเลื่อนขั้น 1 ตค 56 เป็นเงิน] AS CATEGORY_SAL_NAME, 
						  [รวมเสนอเลื่อนขั้น 1 ตค 56 ทั้งสิ้น %] AS SALARY_START, [เฉพาะที่ได้รับเงิน เลื่อนขั้น 1 ตค 56 เป็นเงิน] AS EP_OTHERNAME1, [รวมเสนอเลื่อนขั้น 1 ตค 56 เป็นเงินทั้งสิ้น] AS SALARY_END, [เงินเพิ่มการครองชีพชั่วคราว  1  ตค 56] AS SALARY_DAY_END, [รวมเป็นเงินรายได้ทั้งสิ้น 1 ตค 56] AS CLUSTER_CODE1, [พนักงานราชการมีสิทธิเลื่อนขั้น 1 ตุลาคม 2556] AS LEVEL_NAME2, [พนักงานราชการที่ใช้คำนวณวงเงินเลื่อนขั้น 1 กันยายน 2556] AS LEVEL_NAME3, [รหัสสังกัดหน่วยงานที่ขอเลื่อนขั้น 1  ตค  2556] AS EP_NAME1, [ยอดรวมอัตราค่าจ้างปี 2556 ร้อยละ 6 ต้องไม่เกิน] AS LEVEL_NO1, [ยอดรวมอัตราค่าจ้างปี 2556 ร้อยละ 6 เงินต้องไม่เกิน] AS LEVEL_NO2, [ตำแหน่ง พรก ณ 1 ตค 57] AS SAH_POSITION57, [ค่าจ้าง พรก ณ 1 กย 57] AS LEVEL_NO4, [ค่าจ้าง พรก ณ 30 กย 57] AS SAH_OLD_SALARY57, [ค่าจ้าง พรก ณ 1 ตค 57] AS SAH_SALARY57, [1 ตค 57 พรก ได้เลื่อน] AS CATEGORY_SAL_NAME1, 
						  [1 ตค 57 พรก ไม่ได้เลื่อนเพราะ] AS SAH_REMARK57, [คะแนนผลสัมฤทธิ์ของงานที่ได้รับการประเมิน 1 เมย 57] AS EP_OTHERNAME2, [คะแนนประเมินสมรรถนะของงานที่ได้รับการประเมิน 1 เมย 57] AS CLUSTER_CODE2, [คะแนนรวมในการประเมิน 1 เมย 57] AS LEVEL_NAME4, [คะแนนผลสัมฤทธิ์ของงานที่ได้รับการประเมิน 1 ตค 567] AS LEVEL_NAME5, [คะแนนประเมินสมรรถนะของงานที่ได้รับการประเมิน 1 ตค 57] AS EP_NAME2, [คะแนนรวมในการประเมิน 1 ตค 57] AS LEVEL_NO7, [คะแนนรวมทั้งปี 2557] AS CATEGORY_SAL_NAME2, 
						  [รหัสหน่วยงานที่ให้ 1 ตค 57] AS SALARY_START2, [หน่วยงานเสนอเลื่อนขั้น 1 ตค 57 %] AS EP_OTHERNAME3, [หน่วยงานเสนอเลื่อนขั้น 1 ตค 57 เป็นเงิน] AS SALARY_END1, [สายเสนอเลื่อนขั้น 1 ตค 57 %] AS SALARY_DAY_END1, [สายเสนอเลื่อนขั้น 1 ตค 57 เป็นเงิน] AS CLUSTER_CODE3, [รหัสสายที่ให้ 1 ตค 57] AS LEVEL_NAME6, [กรมเสนอเลื่อนขั้น 1 ตค 57 %] AS LEVEL_NAME7, [กรมเสนอเลื่อนขั้น 1 ตค 57 เป็นเงิน] AS EP_NAME3, [รวมเสนอเลื่อนขั้น 1 ตค 57 ทั้งสิ้น %] AS LEVEL_NO8, [รวมเสนอเลื่อนขั้น 1 ตค 57 เป็นเงินทั้งสิ้น] AS CATEGORY_SAL_NAME3, 
						  [เงินเพิ่มการครองชีพชั่วคราว  1  ตค 57] AS SALARY_START3, [รวมเป็นเงินรายได้ทั้งสิ้น 1 ตค 57] AS EP_OTHERNAME4, [พนักงานราชการมีสิทธิเลื่อนขั้น 1 ตุลาคม 2557] AS CLUSTER_CODE4, [พนักงานราชการที่ใช้คำนวณวงเงินเลื่อนขั้น 1 กันยายน 2557] AS LEVEL_NAME8, [รหัสสังกัดหน่วยงานที่ขอเลื่อนขั้น 1  ตค  2557] AS LEVEL_NAME9
						  FROM [กรอบ3 เลื่อน 54 - 57]
						  WHERE [ตำแหน่งเลขที่ ( พร )] IS NOT NULL and [คนครองเดิม] IS NOT NULL 
						  ORDER BY [ลำดับที่] ";

		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "$cmd<br>";
		while($data = $db_dpis35->get_array()){
			$PER_ID = trim($data[PER_ID]);
			$GOVPOS_DATE = trim($data[GOVPOS_DATE]);
			$EFFECTIVEDATE = ($GOVPOS_DATE)? ((substr($GOVPOS_DATE, 4, 4) - 543) ."-". substr($GOVPOS_DATE, 2, 2) ."-". substr($GOVPOS_DATE, 0, 2)) : "-";
			$DOCNO = trim($data[GOVPOS_REFF]);
			$GOVPOS_YEAR = trim($data[GOVPOS_YEAR]);
			if (trim($CUR_YEAR)) $DOCNO .= '/'.trim($CUR_YEAR);
			$DOCDATE = trim($data[MP_COMMAND_DATE]);

			$DOCNO_EDIT = trim($data[POS_NUM_CODE_SIT_ABB_EDIT]).trim($data[MP_COMMAND_NUM_EDIT]);
			$CUR_YEAR_EDIT = trim($data[CUR_YEAR_EDIT]);
			if (trim($CUR_YEAR_EDIT)) $DOCNO_EDIT .= '/'.trim($CUR_YEAR_EDIT);
			$DOCDATE_EDIT = trim($data[MP_COMMAND_DATE_EDIT]);

			$SAH_POS_NO = trim($data[SAH_POS_NO]);
			$WPOS_CODE = trim($data[WPOS_CODE]);
			$WPOS_NAME = trim($data[WPOS_NAME]);
			$cmd = " select PL_CODE from PER_LINE where PL_NAME = '$WPOS_NAME' ";
			$db_dpis->send_cmd($cmd);
			$data_dpis = $db_dpis->get_array();
			$PL_CODE = trim($data_dpis[PL_CODE]);
			if ($PL_CODE=="-") $PL_CODE = "";

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

			$POH_ORG1 = "กระทรวงคมนาคม";
			$POH_ORG2 = "กรมทางหลวง";
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
			$cmd = " SELECT ORG_NAME  FROM PER_POS_EMPSER a, PER_ORG b where a.ORG_ID=b.ORG_ID and POEMS_NO = '$SAH_POS_NO' ";
			$db_dpis->send_cmd($cmd);
			$data_dpis = $db_dpis->get_array();
			$POH_ORG = trim($data_dpis[ORG_NAME]);

			$cmd = " SELECT LEVEL_NO  FROM PER_PERSONAL where PER_ID = $PER_ID ";
			$db_dpis->send_cmd($cmd);
			$data_dpis = $db_dpis->get_array();
			$LEVEL_NO = trim($data_dpis[LEVEL_NO]);

			$SAH_SALARY54 = $data[SAH_SALARY54]+0;
			$SAH_OLD_SALARY54 = $data[SAH_OLD_SALARY54];
			$SAH_SALARY_UP54 = $data[SAH_SALARY_UP54];
			$SAH_SALARY_EXTRA54 = $data[SAH_SALARY_EXTRA54];
			$SAH_PERCENT_UP54 = $data[SAH_PERCENT_UP54];
			$SAH_POSITION54 = trim($data[SAH_POSITION54]);

			$SAH_SALARY55 = $data[SAH_SALARY55]+0;
			$SAH_OLD_SALARY55 = $data[SAH_OLD_SALARY55];
			$SAH_SALARY_UP55 = $data[SAH_SALARY_UP55];
			$SAH_SALARY_EXTRA55 = $data[SAH_SALARY_EXTRA55];
			$SAH_PERCENT_UP55 = $data[SAH_PERCENT_UP55];
			$SAH_POSITION55 = trim($data[SAH_POSITION55]);

			$SAH_SALARY56 = $data[SAH_SALARY56]+0;
			$SAH_OLD_SALARY56 = $data[SAH_OLD_SALARY56];
			$SAH_SALARY_UP56 = $data[SAH_SALARY_UP56];
			$SAH_SALARY_EXTRA56 = $data[SAH_SALARY_EXTRA56];
			$SAH_PERCENT_UP56 = $data[SAH_PERCENT_UP56];
			$SAH_POSITION56 = trim($data[SAH_POSITION56]);

			$SAH_SALARY57 = $data[SAH_SALARY57]+0;
			$SAH_OLD_SALARY57 = $data[SAH_OLD_SALARY57];
			$SAH_SALARY_UP57 = $data[SAH_SALARY_UP57];
			$SAH_SALARY_EXTRA57 = $data[SAH_SALARY_EXTRA57];
			$SAH_PERCENT_UP57 = $data[SAH_PERCENT_UP57];
			$SAH_POSITION57 = trim($data[SAH_POSITION57]);

			$SPECIALIST_NAME = trim($data[SPECIALIST_NAME]);
			$REMARK = trim($data[GOVPOS_DET]);
			$FLAG_TO_NAME_CODE = trim($data[FLAG_TO_NAME_CODE]);
			$POH_SALARY_POS = 0;
			$GOVPOS_COMCODE = trim($data[GOVPOS_COMCODE]);

			if ($GOVPOS_COMCODE=="0") $MOV_CODE = "6"; // ผิด **********************
			elseif ($GOVPOS_COMCODE=="1") $MOV_CODE = "102"; // ทดลองปฏิบัติราชการ
			elseif ($GOVPOS_COMCODE=="10") $MOV_CODE = "21310"; // 0.5 ขั้น
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

			$SALARYHIS54 = $SALARYHIS55 = $SALARYHIS56 = $SALARYHIS57 = "";		
			$EX_CODE = "024";
//			if ($MOV_TYPE==1 || $MOV_TYPE==3)	$POSITIONHIS = 1;		
			if ($SAH_SALARY54 > 0)	$SALARYHIS54 = 1;
			if ($SAH_SALARY55 > 0)	$SALARYHIS55 = 1;
			if ($SAH_SALARY56 > 0)	$SALARYHIS56 = 1;
			if ($SAH_SALARY57 > 0)	$SALARYHIS57 = 1;

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
			if (!$SAH_POS_NO) $SAH_POS_NO = "-";
			if (!$DOCNO) $DOCNO = "-";
			if (!$DOCDATE) $DOCDATE = "-";
			if (!$SALARY) $SALARY = 0;
			if (!$SEQ_NO) $SEQ_NO = 1;
			if (!$CMD_SEQ || $CMD_SEQ > 20000) $CMD_SEQ = "NULL";

			if (!$SAH_PERCENT_UP54) $SAH_PERCENT_UP54 = "NULL";
			if (!$SAH_SALARY_UP54) $SAH_SALARY_UP54 = "NULL";
			if (!$SAH_SALARY_EXTRA54) $SAH_SALARY_EXTRA54 = "NULL";
			if (!$SAH_PERCENT_UP55) $SAH_PERCENT_UP55 = "NULL";
			if (!$SAH_SALARY_UP55) $SAH_SALARY_UP55 = "NULL";
			if (!$SAH_SALARY_EXTRA55) $SAH_SALARY_EXTRA55 = "NULL";
			if (!$SAH_PERCENT_UP56) $SAH_PERCENT_UP56 = "NULL";
			if (!$SAH_SALARY_UP56) $SAH_SALARY_UP56 = "NULL";
			if (!$SAH_SALARY_EXTRA56) $SAH_SALARY_EXTRA56 = "NULL";
			if (!$SAH_PERCENT_UP57) $SAH_PERCENT_UP57 = "NULL";
			if (!$SAH_SALARY_UP57) $SAH_SALARY_UP57 = "NULL";
			if (!$SAH_SALARY_EXTRA57) $SAH_SALARY_EXTRA57 = "NULL";

			$ORG_ID_1 = "NULL";
			$ORG_ID_2 = "NULL";
			$ORG_ID_3 = "NULL";
/*
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
*/			
			$MOV_CODE = "213";
			if ($SALARYHIS54) {
				$PER_SALARYHIS++;
				$SAH_EFFECTIVEDATE = "2011-10-01";
				$SAH_KF_YEAR = "2554";
				$cmd = " INSERT INTO PER_SALARYHIS(SAH_ID, PER_ID, SAH_EFFECTIVEDATE, MOV_CODE, SAH_SALARY, 
								SAH_DOCNO, SAH_DOCDATE, SAH_ENDDATE, UPDATE_USER, UPDATE_DATE, PER_CARDNO, SAH_PERCENT_UP, 
								SAH_SALARY_UP, SAH_SALARY_EXTRA, SAH_SEQ_NO, SAH_REMARK, LEVEL_NO, SAH_POS_NO_NAME, SAH_POS_NO, 
								SAH_POSITION, SAH_ORG, EX_CODE, SAH_PAY_NO, SAH_KF_YEAR, SAH_LAST_SALARY, SM_CODE, SAH_CMD_SEQ, SAH_ORG_DOPA_CODE, SAH_OLD_SALARY)
								VALUES ($MAX_SAH_ID, $PER_ID, '$SAH_EFFECTIVEDATE', '$MOV_CODE', $SAH_SALARY54, '$DOCNO', 
								'$DOCDATE', NULL, $UPDATE_USER, '$UPDATE_DATE', '$PER_CARDNO', $SAH_PERCENT_UP54, $SAH_SALARY_UP54, 
								$SAH_SALARY_EXTRA54, $SEQ_NO, '$SAH_REMARK54', '$LEVEL_NO', '$POS_NO_NAME', '$SAH_POS_NO', 
								'$SAH_POSITION54', '$POH_ORG', '$EX_CODE', '$SAH_POS_NO', '$SAH_KF_YEAR', '$LAST_TRANSACTION', '$SM_CODE', $CMD_SEQ, '$SAH_ORG_DOPA_CODE', $SAH_OLD_SALARY54) ";
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
			if ($SALARYHIS55) {
				$PER_SALARYHIS++;
				$SAH_EFFECTIVEDATE = "2012-10-01";
				$SAH_KF_YEAR = "2555";
				$cmd = " INSERT INTO PER_SALARYHIS(SAH_ID, PER_ID, SAH_EFFECTIVEDATE, MOV_CODE, SAH_SALARY, 
								SAH_DOCNO, SAH_DOCDATE, SAH_ENDDATE, UPDATE_USER, UPDATE_DATE, PER_CARDNO, SAH_PERCENT_UP, 
								SAH_SALARY_UP, SAH_SALARY_EXTRA, SAH_SEQ_NO, SAH_REMARK, LEVEL_NO, SAH_POS_NO_NAME, SAH_POS_NO, 
								SAH_POSITION, SAH_ORG, EX_CODE, SAH_PAY_NO, SAH_KF_YEAR, SAH_LAST_SALARY, SM_CODE, SAH_CMD_SEQ, SAH_ORG_DOPA_CODE, SAH_OLD_SALARY)
								VALUES ($MAX_SAH_ID, $PER_ID, '$SAH_EFFECTIVEDATE', '$MOV_CODE', $SAH_SALARY55, '$DOCNO', 
								'$DOCDATE', NULL, $UPDATE_USER, '$UPDATE_DATE', '$PER_CARDNO', $SAH_PERCENT_UP55, $SAH_SALARY_UP55, 
								$SAH_SALARY_EXTRA55, $SEQ_NO, '$SAH_REMARK55', '$LEVEL_NO', '$POS_NO_NAME', '$SAH_POS_NO', 
								'$SAH_POSITION55', '$POH_ORG', '$EX_CODE', '$SAH_POS_NO', '$SAH_KF_YEAR', '$LAST_TRANSACTION', '$SM_CODE', $CMD_SEQ, '$SAH_ORG_DOPA_CODE', $SAH_OLD_SALARY55) ";
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
			if ($SALARYHIS56) {
				$PER_SALARYHIS++;
				$SAH_EFFECTIVEDATE = "2013-10-01";
				$SAH_KF_YEAR = "2556";
				$cmd = " INSERT INTO PER_SALARYHIS(SAH_ID, PER_ID, SAH_EFFECTIVEDATE, MOV_CODE, SAH_SALARY, 
								SAH_DOCNO, SAH_DOCDATE, SAH_ENDDATE, UPDATE_USER, UPDATE_DATE, PER_CARDNO, SAH_PERCENT_UP, 
								SAH_SALARY_UP, SAH_SALARY_EXTRA, SAH_SEQ_NO, SAH_REMARK, LEVEL_NO, SAH_POS_NO_NAME, SAH_POS_NO, 
								SAH_POSITION, SAH_ORG, EX_CODE, SAH_PAY_NO, SAH_KF_YEAR, SAH_LAST_SALARY, SM_CODE, SAH_CMD_SEQ, SAH_ORG_DOPA_CODE, SAH_OLD_SALARY)
								VALUES ($MAX_SAH_ID, $PER_ID, '$SAH_EFFECTIVEDATE', '$MOV_CODE', $SAH_SALARY56, '$DOCNO', 
								'$DOCDATE', NULL, $UPDATE_USER, '$UPDATE_DATE', '$PER_CARDNO', $SAH_PERCENT_UP56, $SAH_SALARY_UP56, 
								$SAH_SALARY_EXTRA56, $SEQ_NO, '$SAH_REMARK56', '$LEVEL_NO', '$POS_NO_NAME', '$SAH_POS_NO', 
								'$SAH_POSITION56', '$POH_ORG', '$EX_CODE', '$SAH_POS_NO', '$SAH_KF_YEAR', '$LAST_TRANSACTION', '$SM_CODE', $CMD_SEQ, '$SAH_ORG_DOPA_CODE', $SAH_OLD_SALARY56) ";
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
			if ($SALARYHIS57) {
				$PER_SALARYHIS++;
				$SAH_EFFECTIVEDATE = "2014-10-01";
				$SAH_KF_YEAR = "2557";
				$cmd = " INSERT INTO PER_SALARYHIS(SAH_ID, PER_ID, SAH_EFFECTIVEDATE, MOV_CODE, SAH_SALARY, 
								SAH_DOCNO, SAH_DOCDATE, SAH_ENDDATE, UPDATE_USER, UPDATE_DATE, PER_CARDNO, SAH_PERCENT_UP, 
								SAH_SALARY_UP, SAH_SALARY_EXTRA, SAH_SEQ_NO, SAH_REMARK, LEVEL_NO, SAH_POS_NO_NAME, SAH_POS_NO, 
								SAH_POSITION, SAH_ORG, EX_CODE, SAH_PAY_NO, SAH_KF_YEAR, SAH_LAST_SALARY, SM_CODE, SAH_CMD_SEQ, SAH_ORG_DOPA_CODE, SAH_OLD_SALARY)
								VALUES ($MAX_SAH_ID, $PER_ID, '$SAH_EFFECTIVEDATE', '$MOV_CODE', $SAH_SALARY57, '$DOCNO', 
								'$DOCDATE', NULL, $UPDATE_USER, '$UPDATE_DATE', '$PER_CARDNO', $SAH_PERCENT_UP57, $SAH_SALARY_UP57, 
								$SAH_SALARY_EXTRA57, $SEQ_NO, '$SAH_REMARK57', '$LEVEL_NO', '$POS_NO_NAME', '$SAH_POS_NO', 
								'$SAH_POSITION57', '$POH_ORG', '$EX_CODE', '$SAH_POS_NO', '$SAH_KF_YEAR', '$LAST_TRANSACTION', '$SM_CODE', $CMD_SEQ, '$SAH_ORG_DOPA_CODE', $SAH_OLD_SALARY57) ";
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