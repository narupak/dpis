<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

// �Ţ�����˹觤�ͧ 2 �� select pos_id, count(*) from per_personal where per_type=1 and per_status=1 group by pos_id having count(*) > 1

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

// �Ӻ� 7494
		$cmd = " DELETE FROM PER_DISTRICT ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

// ����� 963 �Ҵ 1
		$cmd = " DELETE FROM PER_AMPHUR ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

// �ѧ��Ѵ 77
		$cmd = " DELETE FROM PER_PROVINCE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

// ����� 188
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
/* // �زԡ���֡�� 544
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
			if (strpos($EDUCATION_NAME,"��ɮպѳ�Ե") !== false || strpos($EDUCATION_NAME,"DOCTOR OF") !== false) $EL_CODE = "80";
			elseif (strpos($EDUCATION_NAME,"��Һѳ�Ե") !== false || strpos($EDUCATION_NAME,"MASTER OF") !== false) $EL_CODE = "60";
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
		
// ����������Ҫ��� 12
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
	
// �Ң��Ԫ��͡ 1013
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
// ���˹觾�ѡ�ҹ�Ҫ��� 587
		$cmd = " DELETE FROM PER_EMPSER_POS_NAME ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " SELECT [����ʵ��˹�] AS EP_CODE, [���˹觻Ѩ�غѹ] AS EP_NAME, [����ʡ����] AS LEVEL_NO, [��Ҩ�ҧ�����] AS CATEGORY_SAL_NAME, 
						  [��áԨ] AS SALARY_START, [��͵��˹�] AS EP_OTHERNAME, [˹�ҷ������Ѻ�Դ�ͺ] AS SALARY_END, [�س���ѵ�੾�е��˹�] AS SALARY_DAY_END, [��Ҩ�ҧ��鹵��] AS FLAG_USE, [��Ҩ�ҧ����٧] AS CLUSTER_CODE, [���͡�����ҹ] AS LEVEL_NAME, [���͡�����ҹ 1] AS LEVEL_NAME1
						  FROM [����ʵ��˹� �á]
						  ORDER BY [����ʵ��˹�] ";
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
// �ѭ���ѵ���Թ��͹ 1863
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
			if ($MP_CEE_CODE=="�.") $LAYER_TYPE = 2;
			elseif ($MP_CEE_CODE=="�.") $LAYER_TYPE = 1;

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
// �ӹ�˹�Ҫ��� 521
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
		
// �������������͹��� 69
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
		
// �������ͧ�Ҫ�� 18 �Ҵ 19 ����­�ҪҴ
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
						 VALUES ('19', '����­�ҪҴ���Ҥس��鹷�� 3', '����­�ҪҴ���Ҥس��鹷�� 3', 
						 19, 3, 1, 19, $UPDATE_USER, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
*/		
/*		
// ����������� 14
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

// �������ɷҧ�Թ�� 27
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

// �����դ����ͺ 4
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

// �дѺ����֡�� 5
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
		
// ʶҺѹ����֡�� 73
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
// �ӹǹ����Թ��͹ 14
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

// OK �ç���ҧ 
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

		$cmd = " SELECT [�����ѧ�Ѵ] AS ORG_CODE, [�ǧ/�ٹ�� �] AS DEPT_NAME, [�ӹѡ/�ͧ] AS SKILL2, [�ѧ�Ѵ�����] AS SKILL3, [�ѧ�Ѵ] AS ORG_NAME1, [����ѧ�Ѵ] AS ORG_SHORTNAME, [�ѧ�Ѵ���] AS SKILL4, [�����ѧ�Ѵ����] AS SKILL5, [��ͪ����ѧ�Ѵ����] AS SKILL6, [�����ѧ�Ѵ���] AS SKILL7, [�ѧ�Ѵ���1] AS SKILL8, [�ԡ���·ҧ��ѧ] AS SKILL9, [�ѧ�Ѵ����͹���] AS SKILL10, [�ѧ�Ѵ���] AS SKILL11, [�ѧ�Ѵ������ҹ] AS SKILL12, [�ѧ�Ѵ��ǹ] AS OT_NAME, [�Ԩ����] AS SKILL14, [��¡�úѧ�Ѻ�ѭ������] AS SKILL15, [�ѧ��Ѵ] AS PV_NAME, [�ѧ��Ѵ1] AS SKILL16, [�ӴѺ��ѧ�ѧ��Ѵ ��] AS SKILL17, [�ż�Ե] AS SKILL18, [�ӴѺ�ż�Ե] AS SKILL19, [�ѧ�Ѵ⺹��] AS SKILL20, [�ӴѺ�ż�Ե������] AS SKILL21, [�ҹ��Ǵ] AS SKILL22, [��§ҹ] AS SKILL23, [�����ѧ�Ѵ�����] AS SKILL24, [�����ѧ�Ѵ�����] AS SKILL25, [�ѧ�Ѵ�������ࢵ] AS SKILL26, [�ѧ�Ѵ����� ��������� ��] AS SKILL27, [�ѧ�Ѵ�������� ��] AS ORG_NAME 
						FROM [������ѧ�Ѵ �á] 
						WHERE [�����ѧ�Ѵ] IS NOT NULL 
						ORDER BY [�����ѧ�Ѵ] ";
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
				if (!$ORG_ID_REF && $DEPT_NAME=="�ǧ��÷ҧ�صôԵ���� 1") $ORG_ID_REF = 14126;
				elseif (!$ORG_ID_REF && $DEPT_NAME=="�ǧ��÷ҧ�صôԵ���� 2") $ORG_ID_REF = 14126;
				elseif (!$ORG_ID_REF && $DEPT_NAME=="�ǧ��÷ҧ�ء�����") $ORG_ID_REF = 14132;
				elseif (!$ORG_ID_REF && $DEPT_NAME=="�ǧ��÷ҧ���Թ���") $ORG_ID_REF = 14139;
				if ($ORG_ID_REF) $OL_CODE = "04";
			}

			$cmd = " select PV_CODE from PER_PROVINCE where PV_NAME = '$PV_NAME' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$TF_PROV = trim($data2[PV_CODE]);

			$OT_CODE = "01"; 
			if ($OT_NAME=="�����Ҥ") $OT_CODE = "02"; 
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

		$cmd = " SELECT  [���ʧҹ] AS SECTION_CODE, [�ҹ] AS ORG_NAME, [����] AS SECTION_NAME 
						FROM [���ʧҹ �á] 
						WHERE [����] IS NOT NULL 
						ORDER BY [���ʧҹ] ";
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
			if ($ORG_NAME=="�ӹѡ�Ѳ���к�������") $ORG_NAME = "�ӹѡ�ҹ�Ѳ���к�������";
			if ($ORG_NAME=="��ǹ�����çҹ����Ժѵ�") $ORG_NAME = "�ٹ���غѵ����";
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

// ���˹觾�ѡ�ҹ�Ҫ��� 30292 - 30292

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

		$cmd = " SELECT [�ӴѺ���] AS POEMS_ID, [ʶҹТͧ���˹� �á] AS POS_STATUS, [����غ���˹� �á] AS SKILL2, [�� �á] AS SKILL3, [���˹��Ţ��� (Ũ)] AS ORG_NAME, [���˹��Ţ��� ( �� )] AS POEMS_NO, [�ӹ�˹�Ҫ���] AS SKILL4, [����] AS SKILL5, [���ʡ��] AS SKILL6, [���������ѧ���] AS SKILL7, [��ҵͺ᷹�ա�͹] AS SKILL8, [��Ҥ�ͧ�վ�ա�͹] AS SKILL9, [��������ա�͹������] AS SKILL10, [������͹�ա�͹] AS SKILL11, [��ҵͺ᷹�Ѩ�غѹ] AS SKILL12, [��Ҥ�ͧ�վ�Ѩ�غѹ] AS SKILL13, [��������ջѨ�غѹ������] AS SKILL14, [������͹�ջѨ�غѹ] AS SKILL15, [��ҵͺ᷹��˹��] AS PV_CODE, [��Ҥ�ͧ�վ��˹��] AS SKILL16, [���������˹�ҷ�����] AS SKILL17, [���ʵ��˹� �á] AS EP_CODE, [���ʧҹ �á] AS SECTION_CODE, [�����ѧ�Ѵ �á] AS DEPARTMENT_CODE, [������ѧ�Ѵ���ͻ���ª�� �á] AS SKILL21, [��������任�Ժѵԧҹ�ͧ �á] AS SKILL22, [��ç���˹������] AS SKILL23, [���˹� �á ��ҧ������ѹ���] AS SKILL24, [����ͧ���˹� �á ��͹��ҧ] AS SKILL25, [���������ѵ�ҡ��ѧ] AS SKILL26, [������ѵ�ҡ��ѧ�����] AS SKILL27, [��˹��ͧ1] AS SKILL28, [��˹��ͧ2] AS SKILL29, [��˹��ͧ3] AS SKILL30, [��˹��ͧ4] AS SKILL31, [��˹��ͧ5] AS SKILL32, [��˹��ͧ6] AS SKILL33, [��˹��ͧ7] AS SKILL34, [��˹��ͧ8] AS SKILL35, [��˹��ͧ9] AS SKILL36, [��˹��ͧ10] AS SKILL37, [��˹��ͧ11] AS SKILL38, [��˹��ͧ12] AS SKILL39, [��˹��ͧ13] AS SKILL40, [��˹��ͧ14] AS SKILL41, [��˹��ͧ15] AS SKILL42, [��˹��ͧ16] AS SKILL43, [��˹��ͧ17] AS SKILL44, [��˹��ͧ18] AS SKILL45, [�١��ҧ��ШӤ�ͧ��ͺ���³����] AS SKILL46, [�ѹ�����˹� �á ��ҧ] AS SKILL47, [�շ����˹� �á ��ҧ] AS SKILL48, [��͹�����˹� �á ��ҧ] AS SKILL49, [��������͹��� �á] AS SKILL50, [��������͹������� �á] AS SKILL51, [���ʧ���� �á] AS SKILL52, [���ʧ�������� �á] AS SKILL53, [⺹�� �á] AS SKILL54, [���ʤ����Թ⺹�� �á] AS SKILL55, [���ʷ����] AS SKILL56, [�����˵�] AS POEMS_REMARK, [���ʻ�Ѻ �á] AS SKILL58, [������ѧ��Ѻ �á] AS SKILL59, [���ʾ���� �á] AS SKILL60, [�ѹ��͹���Դ �á] AS SKILL61, [�ѹ��� �á �Դ] AS SKILL62, [��͹��� �á �Դ] AS SKILL63, [�շ�� �á �Դ] AS SKILL64, [�ѹ������³����] AS SKILL65, [�ѹ����ҧ] AS SKILL66, [��͹����ҧ] AS SKILL67, [�շ���ҧ] AS SKILL68, [�ѹ��͹�շ���ҧ] AS SKILL69, [�ѹ����ҧ�����á] AS PER_STARTDATE, [�շ���ҧ�����á] AS SKILL71, [�ѹ����ҧ��ѭ�һѨ�غѹ] AS PER_OCCUPYDATE, [�ѹ����ҧ �á] AS SKILL73, [�ѹ����ش�ѭ�Ҩ�ҧ �á] AS SKILL74, [�������ҡ�è�ҧ �á] AS SKILL75, [�ѭ�Ҩ�ҧ���駷��] AS PER_CONTACT_COUNT, [�������觨�ҧ���] AS PER_DOCNO, [�����Ţ�ѵû�ЪҪ� �á] AS SKILL78, [�س�ز� �á] AS SKILL79, [�����ҧ��ѡ�ҹ�Ҫ������������] AS SKILL80, [���˹���ҧ���ͧ�ҡ] AS SKILL81, [���˹���ҧ������ѹ���] AS SKILL82, [��Ժѵԧҹ���ͻ���ª���������] AS SKILL83, [��������任�Ժѵԧҹ] AS SKILL84, [�غ��ԡ���˹觵�������] AS SKILL85, [����͹��ǵ�������] AS SKILL86, [����ͧ���˹觡�͹��ҧ] AS SKILL87, [����¹���͹��ʡ�������] AS SKILL88, [���Ѻ�ɷҧ�Թ�������] AS SKILL89, [������� 1] AS SKILL90, [������� 2] AS SKILL91, [������� 3] AS SKILL92, [������� 4] AS SKILL93, [������� 5] AS SKILL94, [������� 6] AS SKILL95, [�ҹ���µ������] AS SKILL96, [�����˵ص��˹�] AS SKILL97, [����͹�� 52] AS SKILL98, [����͹�� 51] AS SKILL99, [����͹�� 50] AS SKILLa1, [����͹�� 53] AS SKILLa2, [����͹�� 54] AS SKILLa3, [����͹�� 55] AS SKILLa4, [����͹�� 56] AS SKILLa5, [����͹�� 57] AS SKILLa6, [�زԷ������Ѥþ�ѡ�ҹ�Ҫ���] AS SKILLa7, [�����زԡ���֡�ҷ����㹡�è�ҧ] AS SKILLa8, [�����ç���¹�ͧ�زԷ�����ҧ] AS SKILLa9, [�ز��٧�ش��診��] AS SKILLb1, [�����ç���¹��診���٧�ش] AS SKILLb2, [�������ҡ�è�ҧ] AS SKILLb3, [�������������¹��ҹ] AS SKILLb4, [�ѵ�Ҥ�ҵͺ᷹�Ѩ�غѹ] AS SKILLb5, [������͹�ա�͹ %] AS SKILLb6, [�ѵ�Ҥ�ҵͺ᷹�ա�͹] AS SKILLb7, [������͹�ջѨ�غѹ %] AS SKILLb8, [�ѵ�Ҥ�ҵͺ᷹��˹��] AS SKILLb9, [�Թ����͹������Ѻ�ջѨ�غѹ] AS SKILLc1, [�����ѧ�Ѵ�ӹǹǧ�Թ����͹���] AS SKILLc2, [���Ѻ�Թ����͹��������ͧ���] AS SKILLc3, [��·������Թ����] AS SKILLc4, [���ʤ�Ҩ�ҧ �á] AS SKILLc5, [����ʢ�鹤�Ҩ�ҧ �á �ա�͹] AS SKILLc6, [�á ������͹��鹻ա�͹] AS SKILLc7, [����ʢ�鹤�Ҩ�ҧ �á �ջѨ�غѹ] AS SKILLc8, [�á ������͹��鹻Ѩ�غѹ] AS SKILLc9, [����ʢ�鹤�Ҩ�ҧ �á ��˹��] AS SKILLd1, [��ѡ�ҹ�Ҫ������Է������͹��� 1 ���Ҥ�] AS SKILLd2, [��ѡ�ҹ�Ҫ��÷����ӹǳǧ�Թ����͹��� 1 �ѹ��¹] AS SKILLd3, [�����ѧ�Ѵ˹��§ҹ��������͹���] AS SKILLd4, [�������¹�ŧ���˹�] AS SKILLd5, [��Ҩ�ҧ����� 2557  �Դ������ҳ] AS SKILLd6, [��Ҩ�ҧ���� 1 �� 57] AS SKILLd7
						FROM [��ͺ �á] 
						ORDER BY [�ӴѺ���] ";
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

			$cmd = " SELECT [����ʵ��˹�] AS EP_CODE, [���˹觻Ѩ�غѹ] AS EP_NAME, [����ʡ����] AS LEVEL_NO, [��Ҩ�ҧ�����] AS CATEGORY_SAL_NAME, 
							  [��áԨ] AS SALARY_START, [��͵��˹�] AS EP_OTHERNAME, [˹�ҷ������Ѻ�Դ�ͺ] AS SALARY_END, [�س���ѵ�੾�е��˹�] AS SALARY_DAY_END, [��Ҩ�ҧ��鹵��] AS POEM_MIN_SALARY, [��Ҩ�ҧ����٧] AS POEM_MAX_SALARY, [���͡�����ҹ] AS LEVEL_NAME, [���͡�����ҹ 1] AS LEVEL_NAME1
							  FROM [����ʵ��˹� �á]
							  WHERE [����ʵ��˹�] = $EP_CODE
							  ORDER BY [����ʵ��˹�] ";
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

// ��ѡ�ҹ�Ҫ��� 27062 - 27062
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

		$cmd = " SELECT [�ӴѺ���] AS PER_ID, [ʶҹТͧ���˹� �á] AS POEM_STATUS, [����غ���˹� �á] AS POEMS_REMARK, [�� �á] AS PER_GENDER, 
						  [���˹��Ţ��� (Ũ)] AS POEM_NO, [���˹��Ţ��� ( �� )] AS POEMS_NO, [�ӹ�˹�Ҫ���] AS PN_NAME, [����] AS PER_NAME, [���ʡ��] AS PER_SURNAME, 
						  [���������ѧ���] AS PER_ENG_NAME, [��ҵͺ᷹�ա�͹] AS LAST_SALARY, [��Ҥ�ͧ�վ�ա�͹] AS LAST_COST_OF_LIVING, [��������ա�͹������] AS LAST_TOTAL_INCOME, 
						  [������͹�ա�͹] AS LAST_PERCENT, [��ҵͺ᷹�Ѩ�غѹ] AS PER_SALARY, [��Ҥ�ͧ�վ�Ѩ�غѹ] AS COST_OF_LIVING, [��������ջѨ�غѹ������] AS TOTAL_INCOME, 
						  [������͹�ջѨ�غѹ] AS CURR_PERCENT, [��ҵͺ᷹��˹��] AS NEXT_SALARY, [��Ҥ�ͧ�վ��˹��] AS NEXT_COST_OF_LIVING, [���������˹�ҷ�����] AS NEXT_TOTAL_INCOME, 
						  [���ʵ��˹� �á] AS EP_CODE, [���ʧҹ �á] AS ORG_ID_1, [�����ѧ�Ѵ �á] AS ORG_ID, [������ѧ�Ѵ���ͻ���ª�� �á] AS ORG_ID_ASS, [��������任�Ժѵԧҹ�ͧ �á] AS NULL1, 
						  [��ç���˹������] AS TEXT_STARTDATE, [���˹� �á ��ҧ������ѹ���] AS POEMS_REMARK1, [����ͧ���˹� �á ��͹��ҧ] AS OLD_PER_NAME, [���������ѵ�ҡ��ѧ] AS POEMS_REMARK2, 
						  [������ѵ�ҡ��ѧ�����] AS POEMS_REMARK3, [��˹��ͧ1] AS OTHER1, [��˹��ͧ2] AS OTHER2, [��˹��ͧ3] AS OTHER3, [��˹��ͧ4] AS OTHER4, [��˹��ͧ5] AS OTHER5, 
						  [��˹��ͧ6] AS OTHER6, [��˹��ͧ7] AS OTHER7, [��˹��ͧ8] AS OTHER8, [��˹��ͧ9] AS OTHER9, [��˹��ͧ10] AS OTHER10, [��˹��ͧ11] AS OTHER11, 
						  [��˹��ͧ12] AS OTHER12, [��˹��ͧ13] AS OTHER13, [��˹��ͧ14] AS OTHER14, [��˹��ͧ15] AS OTHER15, [��˹��ͧ16] AS OTHER16, [��˹��ͧ17] AS OTHER17, 
						  [��˹��ͧ18] AS OTHER18, [�١��ҧ��ШӤ�ͧ��ͺ���³����] AS PER_RETIREYEAR, [�ѹ�����˹� �á ��ҧ] AS DAY1, [�շ����˹� �á ��ҧ] AS YEAR1, [��͹�����˹� �á ��ҧ] AS MONTH1, 
						  [��������͹��� �á] AS NULL2, [��������͹������� �á] AS NULL3, [���ʧ���� �á] AS NULL4, [���ʧ�������� �á] AS NULL5, [⺹�� �á] AS NULL6, [���ʤ����Թ⺹�� �á] AS NULL7, 
						  [���ʷ����] AS NORMAL, [�����˵�] AS DATE1, [���ʻ�Ѻ �á] AS NULL8, [������ѧ��Ѻ �á] AS NULL9, [���ʾ���� �á] AS NULL10, [�ѹ��͹���Դ �á] AS PER_BIRTHDATE, 
						  [�ѹ��� �á �Դ] AS BIRTH_DAY, [��͹��� �á �Դ] AS BIRTH_MONTH, [�շ�� �á �Դ] AS BIRTH_YEAR, [�ѹ������³����] AS PER_RETIREDATE, [�ѹ����ҧ] AS EFFECTIVE_DAY, 
						  [��͹����ҧ] AS EFFECTIVE_MONTH, [�շ���ҧ] AS EFFECTIVE_YEAR, [�ѹ��͹�շ���ҧ] AS POH_EFFECTIVEDATE, [�ѹ����ҧ�����á] AS PER_STARTDATE, 
						  [�շ���ҧ�����á] AS START_YEAR, [�ѹ����ҧ��ѭ�һѨ�غѹ] AS PER_OCCUPYDATE, [�ѹ����ҧ �á] AS POH_EFFECTIVEDATE2, [�ѹ����ش�ѭ�Ҩ�ҧ �á] AS POH_ENDDATE, [�������ҡ�è�ҧ �á] AS EP_OTHERNAME, [�ѭ�Ҩ�ҧ���駷��] AS PER_CONTACT_COUNT, [�������觨�ҧ���] AS PER_DOCNO, [�����Ţ�ѵû�ЪҪ� �á] AS PER_CARDNO, [�س�ز� �á] AS EM_NAME, [�����ҧ��ѡ�ҹ�Ҫ������������] AS LEVEL_NO, [���˹���ҧ���ͧ�ҡ] AS CATEGORY_SAL_NAME, 
						  [���˹���ҧ������ѹ���] AS SALARY_START, [��Ժѵԧҹ���ͻ���ª���������] AS EP_OTHERNAME1, [��������任�Ժѵԧҹ] AS SALARY_END, [�غ��ԡ���˹觵�������] AS SALARY_DAY_END, [����͹��ǵ�������] AS CLUSTER_CODE1, [����ͧ���˹觡�͹��ҧ] AS LEVEL_NAME2, [����¹���͹��ʡ�������] AS LEVEL_NAME3, [���Ѻ�ɷҧ�Թ�������] AS EP_NAME1, [������� 1] AS LEVEL_NO1, [������� 2] AS LEVEL_NO2, [������� 3] AS LEVEL_NO3, [������� 4] AS LEVEL_NO4, [������� 5] AS LEVEL_NO5, [������� 6] AS LEVEL_NO6, [�ҹ���µ������] AS CATEGORY_SAL_NAME1, 
						  [�����˵ص��˹�] AS SALARY_START1, [����͹�� 52] AS EP_OTHERNAME2, [����͹�� 51] AS CLUSTER_CODE2, [����͹�� 50] AS LEVEL_NAME4, [����͹�� 53] AS LEVEL_NAME5, [����͹�� 54] AS EP_NAME2, [����͹�� 55] AS LEVEL_NO7, [����͹�� 56] AS CATEGORY_SAL_NAME2, 
						  [����͹�� 57] AS SALARY_START2, [�زԷ������Ѥþ�ѡ�ҹ�Ҫ���] AS EP_OTHERNAME3, [�����زԡ���֡�ҷ����㹡�è�ҧ] AS SALARY_END1, [�����ç���¹�ͧ�زԷ�����ҧ] AS SALARY_DAY_END1, [�ز��٧�ش��診��] AS CLUSTER_CODE3, [�����ç���¹��診���٧�ش] AS LEVEL_NAME6, [�������ҡ�è�ҧ] AS LEVEL_NAME7, [�������������¹��ҹ] AS EP_NAME3, [�ѵ�Ҥ�ҵͺ᷹�Ѩ�غѹ] AS LEVEL_NO8, [������͹�ա�͹ %] AS CATEGORY_SAL_NAME3, 
						  [�ѵ�Ҥ�ҵͺ᷹�ա�͹] AS SALARY_START3, [������͹�ջѨ�غѹ %] AS EP_OTHERNAME4, [�ѵ�Ҥ�ҵͺ᷹��˹��] AS CLUSTER_CODE4, [�Թ����͹������Ѻ�ջѨ�غѹ] AS LEVEL_NAME8, [�����ѧ�Ѵ�ӹǹǧ�Թ����͹���] AS LEVEL_NAME9, [���Ѻ�Թ����͹��������ͧ���] AS EP_NAME4, [��·������Թ����] AS LEVEL_NO9, [���ʤ�Ҩ�ҧ �á] AS CATEGORY_SAL_NAME4, 
						  [����ʢ�鹤�Ҩ�ҧ �á �ա�͹] AS SALARY_START4, [�á ������͹��鹻ա�͹] AS EP_OTHERNAME5, [����ʢ�鹤�Ҩ�ҧ �á �ջѨ�غѹ] AS CLUSTER_CODE5, [�á ������͹��鹻Ѩ�غѹ] AS LEVEL_NAME10, [����ʢ�鹤�Ҩ�ҧ �á ��˹��] AS LEVEL_NAME11, [��ѡ�ҹ�Ҫ������Է������͹��� 1 ���Ҥ�] AS EP_NAME5, [��ѡ�ҹ�Ҫ��÷����ӹǳǧ�Թ����͹��� 1 �ѹ��¹] AS LEVEL_NO10, [�����ѧ�Ѵ˹��§ҹ��������͹���] AS CATEGORY_SAL_NAME5, 
						  [�������¹�ŧ���˹�] AS SALARY_START5, [��Ҩ�ҧ����� 2557  �Դ������ҳ] AS EP_OTHERNAME6, [��Ҩ�ҧ���� 1 �� 57] AS LEVEL_NO11
						  FROM [��ͺ �á]
						  WHERE [����] IS NOT NULL and [���ʡ��] IS NOT NULL 
						  ORDER BY [�ӴѺ���] ";
		$db_dpis35->send_cmd($cmd);
//							WHERE and [���˹��Ţ��� ( �� )] = 3
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
			elseif ($RELIGION=="�ط�") $RE_CODE = '1';
			else $RE_CODE = $RELIGION;
			$BLOOD = trim($data[BLOOD]);
			if ($BLOOD=="1") $PER_BLOOD = "A";
			elseif ($BLOOD=="2") $PER_BLOOD = "B";
			elseif ($BLOOD=="4") $PER_BLOOD = "O";
			elseif ($BLOOD=="3" || $BLOODGRP=="�� ��") $PER_BLOOD = "AB";
			else $PER_BLOOD = "-";
			$PER_CONTACT_COUNT = trim($data[PER_CONTACT_COUNT]);
			$FLAG_KBK = trim($data[FLAG_KBK]); 
			if ($FLAG_KBK=="1") $PER_MEMBER = 1;
			else  $PER_MEMBER = 0;

			$PER_MGTSALARY = 0; // ����

			$POS_ID = "NULL";
			$POEM_ID = "NULL";
			$POEMS_ID = "NULL";
			$POT_ID = "NULL";
			$PER_ORGMGT = 0;

			$cmd = " SELECT [����ʵ��˹�] AS EP_CODE, [���˹觻Ѩ�غѹ] AS EP_NAME, [����ʡ����] AS LEVEL_NO, [��Ҩ�ҧ�����] AS CATEGORY_SAL_NAME, 
							  [��áԨ] AS SALARY_START, [��͵��˹�] AS EP_OTHERNAME, [˹�ҷ������Ѻ�Դ�ͺ] AS SALARY_END, [�س���ѵ�੾�е��˹�] AS SALARY_DAY_END, [��Ҩ�ҧ��鹵��] AS POEM_MIN_SALARY, [��Ҩ�ҧ����٧] AS POEM_MAX_SALARY, [���͡�����ҹ] AS LEVEL_NAME, [���͡�����ҹ 1] AS LEVEL_NAME1
							  FROM [����ʵ��˹� �á]
							  WHERE [����ʵ��˹�] = $EP_CODE
							  ORDER BY [����ʵ��˹�] ";
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
			$MOV_CODE = "99999"; // ����
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

		$cmd = " SELECT [�ӴѺ���] AS PER_ID, [����¹���͹��ʡ�������] AS NH_REMARK
						  FROM [��ͺ �á]
						  WHERE [����] IS NOT NULL and [���ʡ��] IS NOT NULL and [����¹���͹��ʡ�������] IS NOT NULL 
						  ORDER BY [�ӴѺ���] ";
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

// ����ѵԡ���֡�Ң���Ҫ��� 46774
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
				if ($QUA_NAME=="�����ʵ���ص��ˡ����ѳ�Ե") $EN_CODE = "4010022";
				elseif ($QUA_NAME=="�Է����ʵ��ѳ�Ե") $EN_CODE = "4010061";
				elseif ($QUA_NAME=="ᾷ����ʵúѳ�Ե") $EN_CODE = "4010049";
				elseif ($QUA_NAME=="�Է����ʵ����Һѳ�Ե") $EN_CODE = "6010054";
				elseif ($QUA_NAME=="�Ѳ���������ʵ��") $EN_CODE = "6010027";
				elseif ($QUA_NAME=="��Ѫ�������ʹ�") $EN_CODE = "4010129";
				elseif ($QUA_NAME=="��èѴ����Ҥ�Ѱ����Ҥ�͡��") $EN_CODE = "6010002";
				elseif ($QUA_NAME=="͹ػ�ԭ����Ż��ʵ��") $EN_CODE = "2010080";
				elseif ($QUA_NAME=="�ѹ�ᾷ����ʵúѳ�Ե") $EN_CODE = "4010030";
				elseif ($QUA_NAME=="�ѧ��ʧ��������ʵ��") $EN_CODE = "4010156";
				elseif ($QUA_NAME=="Master of Arts") $EN_CODE = "6029001";
				elseif ($QUA_NAME=="Doctor of Engineering") $EN_CODE = "8010037";
				elseif ($QUA_NAME=="�Ѳ���ѧ��") $EN_CODE = "6010030";
				elseif ($QUA_NAME=="෤��������������á���֡��") $EN_CODE = "6010016";
				elseif ($QUA_NAME=="�������ʵ��") $EN_CODE = "4010053";
				elseif ($QUA_NAME=="����ʶһѵ¡�����ʵúѳ�Ե") $EN_CODE = "4010168";
				elseif (strpos($QUA_NAME,"���") !== false || strpos($QUA_NAME,"��С�ȹ�ºѵ��ԪҪվ����٧") !== false) $EN_CODE = "3010000";
				elseif (strpos($QUA_NAME,"�Ƿ") !== false || strpos($QUA_NAME,"��С�ȹ�ºѵ��ԪҪվ෤�Ԥ") !== false) $EN_CODE = "2010000";
				elseif (strpos($QUA_NAME,"�Ǫ") !== false || strpos($QUA_NAME,"��С�ȹ�ºѵ��ԪҪվ") !== false) $EN_CODE = "1010000";
				elseif ($QUA_NAME=="�. 4") $EN_CODE = "0510080";
				elseif ($QUA_NAME=="�. 6") $EN_CODE = "0510082";
				elseif ($QUA_NAME=="�.7." || $QUA_NAME=="�. 7") $EN_CODE = "0510083";
				elseif ($QUA_NAME=="�. 3") $EN_CODE = "0510087";
				elseif ($QUA_NAME=="�. 6" || $QUA_NAME=="�Ѹ���֡�һ���� 6") $EN_CODE = "0510090";
				elseif ($QUA_NAME=="�. 8") $EN_CODE = "0510092";
				elseif ($QUA_NAME=="�.�. 3" || $QUA_NAME=="��.3") $EN_CODE = "0510095";
				elseif ($QUA_NAME=="�.�. 4" || $QUA_NAME=="��.4") $EN_CODE = "0510096";
				elseif ($QUA_NAME=="�.�. 5" || $QUA_NAME=="��.5" || $QUA_NAME=="�.�.5.") $EN_CODE = "0510097";
				elseif ($QUA_NAME=="�.�.5(�Է����ʵ��)" || $QUA_NAME=="�.�.5 �Է����ʵ��") $EN_CODE = "0510099";
				elseif ($QUA_NAME=="��.�. (�¸�)" || $QUA_NAME=="��.�(�¸�)" || $QUA_NAME=="�.�.�.(�¸�)" || $QUA_NAME=="��.� (�¸�)" || 
					$QUA_NAME=="�Ⱥ. (�¸�)" || $QUA_NAME=="�Ⱥ.(�¸�)" || $QUA_NAME=="��.�. (�¸�) ���õԹ���" || $QUA_NAME=="���ǡ�����ʵúѳ�Ե(�¸�)" || 
					$QUA_NAME=="��.�.(�¸�)���õԹ����ѹ�Ѻ2" || $QUA_NAME=="�Ⱥ.(�¸�) ���õԹ����ѹ�Ѻ2" || $QUA_NAME=="��.�.(�¸�) ���õԹ����ѹ�Ѻ2") $EN_CODE = "4010109";
				elseif ($QUA_NAME=="��.�(��èѴ��÷����)" || $QUA_NAME=="��.�.��èѴ��÷����" || $QUA_NAME=="��.�. (��èѴ��÷����)") $EN_CODE = "4010145";
				elseif ($QUA_NAME=="Ƿ�.(���)" || $QUA_NAME=="Ƿ.�. (���)") $EN_CODE = "4010091";
				elseif ($QUA_NAME=="Ƿ�.(෤����ص��ˡ���)" || $QUA_NAME=="Ƿ.�. (෤������ص��ˡ���)" || $QUA_NAME=="Ƿ.�. ෤�������ص��ˡ���") $EN_CODE = "4010063";
				elseif ($QUA_NAME=="Ƿ.�. (�ó��Է��)") $EN_CODE = "4010066";
				elseif ($QUA_NAME=="Ƿ.� (������ʵ��)" || $QUA_NAME=="Ƿ�.(������ʵ��)" || $QUA_NAME=="Ƿ.�. (������ʵ��)") $EN_CODE = "4010073";
				elseif ($QUA_NAME=="Ƿ.�.ʶԵ�" || $QUA_NAME=="Ƿ.�. (ʶԵ�)") $EN_CODE = "4010096";
				elseif ($QUA_NAME=="�Է����ʵúѳ�Ե (��Ե��ʵ��)" || $QUA_NAME=="Ƿ.�(��Ե��ʵ��)" || $QUA_NAME=="Ƿ.�. (��Ե��ʵ��)") $EN_CODE = "4010090";
				elseif ($QUA_NAME=="Ƿ�.(�֡����ʵ��)" || $QUA_NAME=="Ƿ.� �֡����ʵ��") $EN_CODE = "4010080";
				elseif ($QUA_NAME=="��.�" || $QUA_NAME=="�Ⱥ." || $QUA_NAME=="���ǡ�����ʵ��" || $QUA_NAME=="��ԭ�����ǡ�����ʵ��ѳ�Ե") $EN_CODE = "4010103";
				elseif ($QUA_NAME=="��.�. (��èѴ��çҹ������ҧ)" || $QUA_NAME=="��.�.��èѴ��çҹ������ҧ" || $QUA_NAME=="��.�. ��èѴ��çҹ������ҧ" || 
					$QUA_NAME=="���.(��èѴ��çҹ������ҧ)" || $QUA_NAME=="��.� (��èѴ��çҹ������ҧ)" || $QUA_NAME=="��.�.(��èѴ��á�����ҧ)") $EN_CODE = "4010026";
				elseif ($QUA_NAME=="�.�. (��û���ͧ)") $EN_CODE = "4010170";
				elseif ($QUA_NAME=="�Ѱ�����ʹ��ʵ���Һѳ�Ե(��º���Ҹ�ó�)" || $QUA_NAME=="�Ң��Ԫ� ��º����øó� û.�.") $EN_CODE = "6010055";
				elseif ($QUA_NAME=="��.�.��þѲ�Ҫ����" || $QUA_NAME=="��Ż��ʵ��ѳ�Ե �Ԫ��͡��þѲ�Ҫ����") $EN_CODE = "4010173";
				elseif ($QUA_NAME=="������ʵ��ѳ�Ե") $EN_CODE = "4010035";
				elseif ($QUA_NAME=="��." || $QUA_NAME=="�.�" || $QUA_NAME=="�Ե���ʵ��ѳ�Ե") $EN_CODE = "4010034";
				elseif ($QUA_NAME=="��.�.(�Ѱ�����ʹ��ʵ��)" || $QUA_NAME=="�Ѳ���������ʵ����Һѳ�Ե(�Ѱ�����ʵ��)" || $QUA_NAME=="��.�.�Ѱ�����ʹ��ʵ��") $EN_CODE = "6010039";
				elseif ($QUA_NAME=="��.�.���ǡ����¸�" || $QUA_NAME=="���ǡ�����ʵ��ѳ�Ե (���ǡ����¸�)" || $QUA_NAME=="�.��� (���ǡ����¸�)") $EN_CODE = "4010171";
				elseif ($QUA_NAME=="��.�.(����ͧ��)" || $QUA_NAME=="��.� (����ͧ��)" || $QUA_NAME=="���ǡ�����ʵúѳ�Ե(��.�.����ͧ��)") $EN_CODE = "4010169";
				elseif ($QUA_NAME=="�Ѱ�����ʹ��ʵ���Һѳ�Ե(û.�.)��èѴ����Ҥ�Ѱ����͡��" || $QUA_NAME=="��ԭ����Ѱ�����ʹ��ʵ�� ��Һѳ�Ե") $EN_CODE = "6010049";
				elseif ($QUA_NAME=="��.�.���ǡ����¸�" || $QUA_NAME=="���ǡ�����ʵ���Һѳ�Ե(���ǡ����¸�)") $EN_CODE = "6010084";
				elseif ($QUA_NAME=="�Ե���ʵúѳ�Ե(�.�.)") $EN_CODE = "4010034";
				elseif ($QUA_NAME=="��.�.�ѭ��") $EN_CODE = "4010175";
				elseif ($QUA_NAME=="��.�. (�Ѱ��ʵ��)") $EN_CODE = "4010140";
				elseif ($QUA_NAME=="��ԭ��� ��èѴ����Ҥ�Ѱ����Ҥ�͡����Һѳ�Ե") $EN_CODE = "6010002";
				elseif ($QUA_NAME=="�.�. (�ѧ���֡��)") $EN_CODE = "4010021";
				elseif ($QUA_NAME=="��.�" || $QUA_NAME=="���ǡ�����ʵ���Һѳ�Ե (��.�.)") $EN_CODE = "6010083";
				elseif ($QUA_NAME=="��.�. (����)") $EN_CODE = "4010172";
				elseif ($QUA_NAME=="��Ż��ʵ���Һѳ�Ե ��.�.") $EN_CODE = "6010089";
				elseif ($QUA_NAME=="��.�. (��ú����÷����)") $EN_CODE = "4010176";
				elseif ($QUA_NAME=="��.�(�¸�)") $EN_CODE = "6010117";
				elseif ($QUA_NAME=="�.�. (�Ѱ�����ʹ��ʵ��)") $EN_CODE = "4010177";
				elseif ($QUA_NAME=="�.�. (��û���ͧ)") $EN_CODE = "6010118";
				elseif ($QUA_NAME=="��.�") $EN_CODE = "6010027";
				elseif ($QUA_NAME=="�ͺ. (�¸�)") $EN_CODE = "4010178";
				elseif ($QUA_NAME=="��Ż��ʵ���Һѳ�Ե(��º����С���ҧἹ�ѧ��)") $EN_CODE = "6010095";
				elseif ($QUA_NAME=="��С�ȹ�ºѵü�ا�����") $EN_CODE = "2010029";
				elseif ($QUA_NAME=="��С�ȹ�ºѵþ�Һ�ż�ا�����" || $QUA_NAME=="��С�ȹ�ºѵþ�Һ����м�ا�����") $EN_CODE = "2010036";
				elseif ($QUA_NAME=="��С�ȹ�ºѵü����¾�Һ��") $EN_CODE = "0510030";
				elseif ($QUA_NAME=="��С�ȹ�ºѵä��෤�Ԥ����٧" || $QUA_NAME=="���.(��С�ȹ�ºѵä��෤�Ԥ����٧)" || $QUA_NAME=="��С�ȹ�ºѵä��෤�Ԥ����٧ (���.)") $EN_CODE = " 4010001";
				elseif ($QUA_NAME=="��С�ȹ�ºѵþ�Һ����ʵ����м�ا��������٧") $EN_CODE = "4010009";
				elseif ($QUA_NAME=="��С�ȹ�ºѵ��Ԫ��ѧ��෤�Ԥ") $EN_CODE = "2010060";
				elseif ($QUA_NAME=="��С�ȹ�ºѵ� �.6(�Ѩ�غѹ)") $EN_CODE = "0510108";
				elseif ($QUA_NAME=="��С�ȹ�ºѵá�êŻ�зҹ") $EN_CODE = "3010016";
				elseif ($QUA_NAME=="�.6(�Ѩ�غѹ)") $EN_CODE = "0510110";
				elseif ($QUA_NAME=="��С�ȹ�ºѵ��Ѹ���֡�ҵ͹����(�.6)" || $QUA_NAME=="��С�ȹ�ºѵ��Ѹ��֡�ҵ͹���� (�.6)") $EN_CODE = "0510113";
				elseif ($QUA_NAME=="��С�ȹ�ºѵû�ж��֡�һշ��6" || $QUA_NAME=="��С�ȹ�ºѵû�ж��֡��(�.6)") $EN_CODE = "0510100";
				elseif ($QUA_NAME=="��С�ȹպѵû�ж��֡�ҵ͹����(�.6)") $EN_CODE = "0510114";
				elseif ($QUA_NAME=="��С�ȹ�ºѵ��Ѹ���֡�ҵ͹��(�.3)") $EN_CODE = "0510111";
				elseif ($QUA_NAME=="���ǡ�����ʵúѳ�Ե(��.�.���ǡ�������ͧ��)" || $QUA_NAME=="���ǡ�����ʵúѳ�Ե(���ǡ�������ͧ��)") $EN_CODE = "4010061";
				elseif ($QUA_NAME=="�.���") $EN_CODE = "4010000";
				elseif ($QUA_NAME=="�Է����ʵ��ѳ�Ե") $EN_CODE = "4010061";
				elseif ($QUA_NAME=="�Է����ʵ��ѳ�Ե") $EN_CODE = "4010061";
				elseif ($QUA_NAME=="�Է����ʵ��ѳ�Ե") $EN_CODE = "4010061";
				elseif ($QUA_NAME=="�Է����ʵ��ѳ�Ե") $EN_CODE = "4010061";
				elseif ($QUA_NAME=="�Է����ʵ��ѳ�Ե") $EN_CODE = "4010061";
				elseif ($QUA_NAME=="�Է����ʵ��ѳ�Ե") $EN_CODE = "4010061";
				elseif ($QUA_NAME=="�Է����ʵ��ѳ�Ե") $EN_CODE = "4010061";
				elseif ($QUA_NAME=="�Է����ʵ��ѳ�Ե") $EN_CODE = "4010061";
				elseif ($QUA_NAME=="�Է����ʵ��ѳ�Ե") $EN_CODE = "4010061";
				elseif ($QUA_NAME=="�Է����ʵ��ѳ�Ե") $EN_CODE = "4010061";
				elseif ($QUA_NAME=="�Է����ʵ��ѳ�Ե") $EN_CODE = "4010061";

				if (!$EN_CODE) {
					$cmd = " select EN_CODE from PER_EDUCNAME where EN_NAME = '$QUA_NAME' or EN_SHORTNAME = '$QUA_NAME' ";
					$db_dpis2->send_cmd($cmd);
		//			$db_dpis2->show_error();
					$data2 = $db_dpis2->get_array();
					$EN_CODE = $data2[EN_CODE];
				}
				if (!$EN_CODE && $QUA_NAME) echo "�زԡ���֡�� $QUA_CODE - $QUA_NAME<br>";
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
				if ($MAJOR_NAME=="��º���Ҹ�ó� ��С�ú������ç���") $EM_CODE = "51051100";
				elseif ($QUA_NAME=="�Ǫ. ��ҧ�ص��ˡ���") $EM_CODE = "80020100";
				elseif ($QUA_NAME=="�Ǫ (���Ǩ)") $EM_CODE = "80011100";
				elseif ($QUA_NAME=="�Ǫ. (�ԨԵ���Ż�-��¹)") $EM_CODE = "32000101";
				elseif ($QUA_NAME=="�Ǫ.(�ˡ�����ʵ��)") $EM_CODE = "57000100";
				elseif ($MAJOR_NAME=="��èѴ����Ҥ�Ѱ����͡����Һѳ�Ե") $EM_CODE = "54008101";
//				elseif ($MAJOR_NAME=="ᾷ����ʵúѳ�Ե") $EM_CODE = "4010049";
//				elseif ($MAJOR_NAME=="�Է����ʵ����Һѳ�Ե") $EM_CODE = "6010054";
//				elseif ($MAJOR_NAME=="�Է����ʵ��ѳ�Ե") $EM_CODE = "4010061";
//				elseif ($MAJOR_NAME=="�Է����ʵ��ѳ�Ե") $EM_CODE = "4010061";
//				elseif ($MAJOR_NAME=="�Է����ʵ��ѳ�Ե") $EM_CODE = "4010061";
//				elseif ($MAJOR_NAME=="�Է����ʵ��ѳ�Ե") $EM_CODE = "4010061";
//				elseif ($MAJOR_NAME=="�Է����ʵ��ѳ�Ե") $EM_CODE = "4010061";
//				if (!$EM_CODE) echo "�Ң��Ԫ��͡ $MAJOR_CODE - $MAJOR_NAME<br>";
			}
	
			$CT_CODE_EDU = trim($data[COUNTRY_CODE]);
			$INSTITUTE_CODE = trim($data[INS_CODE]);
			$cmd = " SELECT INS_NAME FROM INSTITUTION WHERE INS_CODE = '$INSTITUTE_CODE' ";
			$db_dpis351->send_cmd($cmd);
			$data1 = $db_dpis351->get_array();
			$INS_NAME = trim($data1[INS_NAME]);
			$INSTITUTE_NAME = str_replace("�.�.", "�ç���¹", $INS_NAME);

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
			if ($EDU_LEV_CODE==1) $EL_CODE = "80"; // ��ԭ���͡��ҧ�����
			elseif ($EDU_LEV_CODE==2) $EL_CODE = "80"; // ��ԭ���͡㹻����
			elseif ($EDU_LEV_CODE==3) $EL_CODE = "60"; // ��ԭ��ⷵ�ҧ�����
			elseif ($EDU_LEV_CODE==4) $EL_CODE = "60"; // ��ԭ���㹻����
			elseif ($EDU_LEV_CODE==5) $EL_CODE = "40"; // ��ԭ�ҵ�յ�ҧ�����
			elseif ($EDU_LEV_CODE==6) $EL_CODE = "40"; // ��ԭ�ҵ��㹻����
			elseif ($EDU_LEV_CODE==7) $EL_CODE = "35"; // ͹ػ�ԭ��
			elseif ($EDU_LEV_CODE==8) $EL_CODE = "30"; // ���.
			elseif ($EDU_LEV_CODE==9) $EL_CODE = "10"; // �Ǫ.	
			elseif ($EDU_LEV_CODE==10) $EL_CODE = "99"; // ͹غ��1
			elseif ($EDU_LEV_CODE==11) $EL_CODE = "99"; // ��� �
			elseif ($EDU_LEV_CODE==12) $EL_CODE = "20"; // �Ƿ.
			elseif ($EDU_LEV_CODE==13) $EL_CODE = "40"; // ���.
			elseif ($EDU_LEV_CODE==14) $EL_CODE = "02"; // �Ѹ���֡�ҵ͹����
			elseif ($EDU_LEV_CODE==15) $EL_CODE = "01"; // �Ѹ���֡�ҵ͹��
			elseif ($EDU_LEV_CODE==16) $EL_CODE = "99"; // 
			elseif ($EDU_LEV_CODE==19) $EL_CODE = "10"; // ��С�ȹ�ºѵ�
			elseif ($EDU_LEV_CODE==20) $EL_CODE = "02"; // ���.	
			elseif ($EDU_LEV_CODE==21) $EL_CODE = "30"; // ��С�ȹ�ºѵê���٧
			elseif ($EDU_LEV_CODE==30) $EL_CODE = "03"; // �.1
			elseif ($EDU_LEV_CODE==31) $EL_CODE = "03"; // �.2
			elseif ($EDU_LEV_CODE==32) $EL_CODE = "03"; // �.3
			elseif ($EDU_LEV_CODE==33) $EL_CODE = "03"; // �.4
			elseif ($EDU_LEV_CODE==34) $EL_CODE = "03"; // �.5
			elseif ($EDU_LEV_CODE==35) $EL_CODE = "03"; // �.6
			elseif ($EDU_LEV_CODE==36) $EL_CODE = "03"; // �.7
			elseif ($EDU_LEV_CODE==40) $EL_CODE = "01"; // �.1
			elseif ($EDU_LEV_CODE==41) $EL_CODE = "01"; // �.2
			elseif ($EDU_LEV_CODE==42) $EL_CODE = "01"; // �.3
			elseif ($EDU_LEV_CODE==43) $EL_CODE = "02"; // �.4
			elseif ($EDU_LEV_CODE==44) $EL_CODE = "02"; // �.5
			elseif ($EDU_LEV_CODE==45) $EL_CODE = "02"; // �.6
			elseif ($EDU_LEV_CODE==46) $EL_CODE = "02"; // �.7
			elseif ($EDU_LEV_CODE==47) $EL_CODE = "02"; // �.8
			elseif ($EDU_LEV_CODE==50) $EL_CODE = "01"; // �.�.1
			elseif ($EDU_LEV_CODE==51) $EL_CODE = "01"; // �.�.2
			elseif ($EDU_LEV_CODE==52) $EL_CODE = "01"; // �.�.3
			elseif ($EDU_LEV_CODE==53) $EL_CODE = "02"; // �.�.4
			elseif ($EDU_LEV_CODE==54) $EL_CODE = "02"; // �.�.5
			elseif ($EDU_LEV_CODE==55) $EL_CODE = "02"; // �.�.6
			elseif ($EDU_LEV_CODE==60) $EL_CODE = "01"; // �.1(�Ѩ�غѹ)
			elseif ($EDU_LEV_CODE==61) $EL_CODE = "01"; // �.2(�Ѩ�غѹ)
			elseif ($EDU_LEV_CODE==62) $EL_CODE = "01"; // �.3(�Ѩ�غѹ)
			elseif ($EDU_LEV_CODE==63) $EL_CODE = "02"; // �.4(�Ѩ�غѹ)
			elseif ($EDU_LEV_CODE==64) $EL_CODE = "02"; // �.5(�Ѩ�غѹ)
			elseif ($EDU_LEV_CODE==65) $EL_CODE = "02"; // �.6(�Ѩ�غѹ)
			elseif ($EDU_LEV_CODE==66) $EL_CODE = "40"; // ��С�ȹ�ºѵþ�Һ����ʵ����м�ا��������٧
			elseif ($EDU_LEV_CODE==67) $EL_CODE = "40"; // ��С�ȹ�ºѵä��෤�Ԥ����٧(���.)
			elseif ($EDU_LEV_CODE==68) $EL_CODE = "30"; // ��С�ȹ�ºѵá�êŻ�зҹ

			if ($QUA_NAME=="��.�.(�¸�)���õԹ����ѹ�Ѻ2" || $QUA_NAME=="�Ⱥ.(�¸�) ���õԹ����ѹ�Ѻ2" || $QUA_NAME=="��.�.(�¸�) ���õԹ����ѹ�Ѻ2") $xxEN_CODE = "���õԹ����ѹ�Ѻ 2";
			elseif($QUA_NAME=="��.�. (�¸�) ���õԹ���") $xxEN_CODE = "���õԹ���";

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
// �����դ����ͺ 1576 ok
		$cmd = " truncate table per_rewardhis ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

// �����դ����ͺ����Ҫ��� 223 
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
// ����� 53515 ok
		$cmd = " truncate table per_absenthis ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

// ����Ң���Ҫ��� 23713
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
// ��ػ�ѹ�� 
		$cmd = " truncate table per_absentsum ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

// ��ػ�ѹ�Ң���Ҫ��� 3289
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

// ����������Ҫ��� 23661
		$cmd = " SELECT [�ӴѺ���] AS PER_ID, [�������������¹��ҹ] AS ADR_ROAD
						  FROM [��ͺ �á]
						  WHERE [����] IS NOT NULL and [���ʡ��] IS NOT NULL and [�������������¹��ҹ] IS NOT NULL 
						  ORDER BY [�ӴѺ���] ";
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
			if ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ (�Ǫ.)  ��ҧ�ص��ˡ���  �Ң��ԪҪ�ҧ俿�ҡ��ѧ  �ç���¹��áԨ�Ҫ���  �.�͹��") {
				$EL_CODE = "10";
				$EN_CODE = "1010041";
				$EM_CODE = "81026101";
				$EDU_INSTITUTE = "�ç���¹��áԨ�Ҫ���  �.�͹��";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ (�Ǫ.)  �ҳԪ¡���  �Ң��ԪҾ�Ԫ¡��  �Ң��Ԫҡ�úѭ�� �ç���¹�ѧ⤹��Ԫ¡��  �.ʡŹ��") {
				$EL_CODE = "10";
				$EN_CODE = "1010043";
				$EM_CODE = "81026101";
				$EDU_INSTITUTE = "�ç���¹�ѧ⤹��Ԫ¡��  �.ʡŹ��";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ (�Ǫ.) ��ҧ�ص��ˡ���  �ҢҪ�ҧ������ҧ  �Է�����෤�Ԥ���") {
				$EL_CODE = "10";
				$EN_CODE = "1010041";
				$EM_CODE = "81026101";
				$EDU_INSTITUTE = "�Է�����෤�Ԥ���";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ (�Ǫ.) ��ҧ�ص��ˡ���  �Ң��Ԫ�����ͧ��  �Ңҧҹ��ҧ¹��  �Է�����෤�Ԥ��ѧ  �.��ѧ") {
				$EL_CODE = "10";
				$EN_CODE = "1010041";
				$EM_CODE = "81026101";
				$EDU_INSTITUTE = "�Է�����෤�Ԥ��ѧ  �.��ѧ";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ (�Ǫ.) ��ҧ�ص��ˡ���  �Ң��ԪҪ�ҧ���ç�ҹ     �Է�������þѴ��ҧ������-�����  �.�ؾ�ó����") {
				$EL_CODE = "10";
				$EN_CODE = "1010041";
				$EM_CODE = "81026101";
				$EDU_INSTITUTE = "�Է�������þѴ��ҧ������-�����  �.�ؾ�ó����";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ (�Ǫ.) ��ҧ�ص��ˡ���  �Ң��ԪҪ�ҧ������ҧ  �ç���¹���෤�Ԥ�شøҹ�  �.�شøҹ�") {
				$EL_CODE = "10";
				$EN_CODE = "1010041";
				$EM_CODE = "81026101";
				$EDU_INSTITUTE = "�ç���¹���෤�Ԥ�شøҹ�  �.�شøҹ�";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ (�Ǫ.) ��ҧ�ص��ˡ���  �Ң��ԪҪ�ҧ������ҧ  �Է����¡���Ҫվ�֧���  �.˹ͧ���") {
				$EL_CODE = "10";
				$EN_CODE = "1010041";
				$EM_CODE = "81026101";
				$EDU_INSTITUTE = "�Է����¡���Ҫվ�֧���  �.˹ͧ���";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ (�Ǫ.) ��ҧ�ص��ˡ���  �Ң��ԪҪ�ҧ������ҧ  �Է�����෤�Ԥ�������  �.�������") {
				$EL_CODE = "10";
				$EN_CODE = "1010041";
				$EM_CODE = "81026101";
				$EDU_INSTITUTE = "�Է�����෤�Ԥ�������  �.�������";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ (�Ǫ.) ��ҧ�ص��ˡ���  �Ң��ԪҪ�ҧ������ҧ  �Է�����෤�Ԥപ�ش�  �.�غ��Ҫ�ҹ�") {
				$EL_CODE = "10";
				$EN_CODE = "1010041";
				$EM_CODE = "81026101";
				$EDU_INSTITUTE = "�Է�����෤�Ԥപ�ش�  �.�غ��Ҫ�ҹ�";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ (�Ǫ.) ��ҧ�ص��ˡ���  �Ң��ԪҪ�ҧ������ҧ  �Է�����෤�Ԥ�ԨԵ�  �.�ԨԵ�") {
				$EL_CODE = "10";
				$EN_CODE = "1010041";
				$EM_CODE = "81026101";
				$EDU_INSTITUTE = "�Է�����෤�Ԥ�ԨԵ�  �.�ԨԵ�";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ (�Ǫ.) ��ҧ�ص��ˡ���  �Ң��ԪҪ�ҧ俿�ҡ��ѧ  �ç���¹���෤�Ԥ�Ҥ���ѹ�͡��§�˹��  �.�غ��Ҫ�ҹ�") {
				$EL_CODE = "10";
				$EN_CODE = "1010041";
				$EM_CODE = "81026101";
				$EDU_INSTITUTE = "�ç���¹���෤�Ԥ�Ҥ���ѹ�͡��§�˹��  �.�غ��Ҫ�ҹ�";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ (�Ǫ.) ��ҧ�ص��ˡ���  �Ң��ԪҪ�ҧ俿�ҡ��ѧ  �ç���¹��Ԫ¡�ê�ҧ෤�Ԥ�ӹ���³�  �.ž����") {
				$EL_CODE = "10";
				$EN_CODE = "1010041";
				$EM_CODE = "81026101";
				$EDU_INSTITUTE = "�ç���¹��Ԫ¡�ê�ҧ෤�Ԥ�ӹ���³�  �.ž����";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ (�Ǫ.) ��ҧ�ص��ˡ���  �Ң��ԪҪ�ҧ¹��   �Է�����෤�Ԥ�����  �.�����") {
				$EL_CODE = "10";
				$EN_CODE = "1010041";
				$EM_CODE = "81026101";
				$EDU_INSTITUTE = "�Է�����෤�Ԥ�����  �.�����";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ (�Ǫ.) ��ҧ�ص��ˡ���  �Ң��ԪҪ�ҧ俿�ҡ��ѧ  ෤������Ҥ���ѹ�͡��§�˹��  �.�͹��") {
				$EL_CODE = "10";
				$EN_CODE = "1010041";
				$EM_CODE = "81026101";
				$EDU_INSTITUTE = " ෤������Ҥ���ѹ�͡��§�˹��  �.�͹��";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ (�Ǫ.) �����ø�áԨ  �Ң��Ԫҡ�úѭ��  �ç���¹��þ�Ԫ¡��  �.�����ո����Ҫ") {
				$EL_CODE = "10";
				$EN_CODE = "1010041";
				$EM_CODE = "81026101";
				$EDU_INSTITUTE = "�ç���¹��þ�Ԫ¡��  �.�����ո����Ҫ";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ (�Ǫ.) �����ø�áԨ  �Ң��Ԫҡ�úѭ��  �ç���¹��Ԫ¡�â͹��  �.�͹��") {
				$EL_CODE = "10";
				$EN_CODE = "1010041";
				$EM_CODE = "81026101";
				$EDU_INSTITUTE = "�ç���¹��Ԫ¡�â͹��  �.�͹��";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ (�Ǫ.) Ἱ���ҧ���ç�ҹ  �ç���¹෤�Ԥ�����  �.�����") {
				$EL_CODE = "10";
				$EN_CODE = "1010041";
				$EM_CODE = "81026101";
				$EDU_INSTITUTE = "�ç���¹෤�Ԥ�����  �.�����";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ (�Ǫ.) ��Ԫ¡���  �ç���¹��Ԫ¡����⢷��") {
				$EL_CODE = "10";
				$EN_CODE = "1010041";
				$EM_CODE = "81026101";
				$EDU_INSTITUTE = "�ç���¹��Ԫ¡����⢷��";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ (�Ǫ.) �ҳԪ¡���  �ç���¹෤����������  �.ž����") {
				$EL_CODE = "10";
				$EN_CODE = "1010041";
				$EM_CODE = "81026101";
				$EDU_INSTITUTE = "�ç���¹෤����������  �.ž����";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ (�Ǫ.) �ҳԪ¡���  �Ң��Ԫҡ�úѭ��  �ç���¹��áԨ�Ҫ���  �.�͹��") {
				$EL_CODE = "10";
				$EN_CODE = "1010041";
				$EM_CODE = "81026101";
				$EDU_INSTITUTE = "�ç���¹��áԨ�Ҫ���  �.�͹��";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ (�Ǫ.) �ҳԪ¡���  �Ң��Ԫҡ�úѭ��  �ç���¹���෤�Ԥ�Ҥ���ѹ�͡��§�˹��  �.�غ��Ҫ�ҹ�") {
				$EL_CODE = "10";
				$EN_CODE = "1010041";
				$EM_CODE = "81026101";
				$EDU_INSTITUTE = "�ç���¹���෤�Ԥ�Ҥ���ѹ�͡��§�˹��  �.�غ��Ҫ�ҹ�";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ (�Ǫ.) �ҳԪ¡���  �Ң��ԪҾ�Ԫ¡��  �Է����¡���Ҫվ�ѧ�áѧ��  �.��ШǺ���բѹ��") {
				$EL_CODE = "10";
				$EN_CODE = "1010041";
				$EM_CODE = "81026101";
				$EDU_INSTITUTE = "�Է����¡���Ҫվ�ѧ�áѧ��  �.��ШǺ���բѹ��";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ (�Ǫ.) �ҳԪ¡���  �Ң��ԪҾ�Ԫ¡�� (��â��)  �ç���¹��Ԫ¡��෾�Է��  �.��û��") {
				$EL_CODE = "10";
				$EN_CODE = "1010041";
				$EM_CODE = "81026101";
				$EDU_INSTITUTE = "�ç���¹��Ԫ¡��෾�Է��  �.��û��";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ (�Ǫ.) �ҳԪ¡���  �Ң��ԪҾ�Ԫ¡�� �ç���¹��áԨ�Ҫ���  �.�͹��") {
				$EL_CODE = "10";
				$EN_CODE = "1010041";
				$EM_CODE = "81026101";
				$EDU_INSTITUTE = "�ç���¹��áԨ�Ҫ���  �.�͹��";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ����٧ (���.)  ��ҧ�ص��ˡ���  �Ң��Ԫ�����ͧ��  �Ңҧҹ෤�Ԥ�ҹ¹��  �ç���¹���෤�Ԥ�ҹ��  �.��§����") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�ç���¹���෤�Ԥ�ҹ��  �.��§����";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ����٧ (���.)  ��ҧ�ص��ˡ���  �Ң��ԪҪ�ҧ俿�ҡ��ѧ  �Է�������þѴ��ҧ���Թ���  �.���Թ���") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�Է�������þѴ��ҧ���Թ���  �.���Թ���";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ����٧ (���.)  ��ҧ�ص��ˡ���  �Ң��ԪҪ�ҧ¹��  �ç���¹����ɮ��෤����ժ�ҧ�ص��ˡ���  �.����ɮ��ҹ�") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�ç���¹����ɮ��෤����ժ�ҧ�ص��ˡ���  �.����ɮ��ҹ�";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ����٧ (���.)  ��ҧ�ص��ˡ���  �Ң��ԪҪ�ҧ�¸�  �Ңҧҹ�¸�  �Է�����෤�Ԥ�غ��Ҫ�ҹ�  �.�غ��Ҫ�ҹ�") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�Է�����෤�Ԥ�غ��Ҫ�ҹ�  �.�غ��Ҫ�ҹ�";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ����٧ (���.)  �����ø�áԨ  �Ң��Ԫҡ�úѭ��  �ç���¹�˵�ѧ�Ҫ���  �.��ѧ") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�ç���¹�˵�ѧ�Ҫ���  �.��ѧ";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ����٧ (���.)  �����ø�áԨ  �Ң��Ԫҡ�úѭ��  �Է������Ҫ���֡�ҹ����ո����Ҫ  �.�����ո����Ҫ") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�Է������Ҫ���֡�ҹ����ո����Ҫ  �.�����ո����Ҫ";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ����٧ (���.)  �����ø�áԨ  �Ң��Ԫҡ�úѭ��  �Է������Ҫ���֡���������  �.�������") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�Է������Ҫ���֡���������  �.�������";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ����٧ (���.)  �����ø�áԨ  �Ң��ԪҤ����������áԨ  �Է������Ҫ���֡������ɮ��ҹ�  �.����ɮ��ҹ�") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�Է������Ҫ���֡������ɮ��ҹ�  �.����ɮ��ҹ�";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ����٧ (���.)  �����ø�áԨ  �Ң��ԪҤ����������áԨ  �Ңҧҹ෤������ӹѡ�ҹ  �Է������Ҫ���֡�Ҿ�ɳ��š  �.��ɳ��š") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�Է������Ҫ���֡�Ҿ�ɳ��š  �.��ɳ��š";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ����٧ (���.) �ɵá���  �Ң��Ԫ��ɵá���  �Է������ɵá����������  �.�������") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�Է������ɵá����������  �.�������";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ����٧ (���.) �ˡ�����ʵ��  Ἱ��Ԫ�������������ҡ��") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ����٧ (���.) ��ҧ�ص��ˡ��  ����Ԫ��¸�  ʢ��ԪҪ�ҧ���Ǩ  ʶҺѹ෤������Ҫ�����Է��ࢵ���Ѿ") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "ʶҺѹ෤������Ҫ�����Է��ࢵ���Ѿ";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ����٧ (���.) ��ҧ�ص��ˡ���  �Ң�俿�ҡ��ѧ  �Ңҧҹ�ҹ�Դ���俿��  �Է����¡���Ҫվ���Թ��ҪԹ��ء�����  �.�ء�����") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�Է����¡���Ҫվ���Թ��ҪԹ��ء�����  �.�ء�����";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ����٧ (���.) ��ҧ�ص��ˡ���  �Ң��Ԫҡ�á�����ҧ  �Ңҧҹ෤�Ԥ��á�����ҧ  �Է�����෤�Ԥ��ҹ") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�Է�����෤�Ԥ��ҹ";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ����٧ (���.) ��ҧ�ص��ˡ���  �Ң��Ԫ�����ͧ��  �Ңҧҹ෤�Ԥ�ҹ¹��  �Է�����෤�Ԥ��ɳ��š  �.��ɳ��š") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�Է�����෤�Ԥ��ɳ��š  �.��ɳ��š";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ����٧ (���.) ��ҧ�ص��ˡ���  �Ң��Ԫ�����ͧ��  �Ңҧҹ෤�Ԥ�ҹ¹��  �Է�������þѴ��ҧ����Թ���  �.����Թ���") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�Է�������þѴ��ҧ����Թ���  �.����Թ���";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ����٧ (���.) ��ҧ�ص��ˡ���  �Ң��ԪҪ�ҧ           ������ҧ  �Է�����෤�Ԥ���") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�Է�����෤�Ԥ���";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ����٧ (���.) ��ҧ�ص��ˡ���  �Ң��ԪҪ�ҧ������ҧ  �ç���¹���෤�Ԥ�ҹ��  �.��§����") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�ç���¹���෤�Ԥ�ҹ��  �.��§����";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ����٧ (���.) ��ҧ�ص��ˡ���  �Ң��ԪҪ�ҧ������ҧ  �Է�����෤�Ԥ�͹��") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�Է�����෤�Ԥ�͹��";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ����٧ (���.) ��ҧ�ص��ˡ���  �Ң��ԪҪ�ҧ������ҧ  �Է�����෤�Ԥ��§����  �.��§����") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�Է�����෤�Ԥ��§����  �.��§����";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ����٧ (���.) ��ҧ�ص��ˡ���  �Ң��ԪҪ�ҧ������ҧ  �Է�����෤�Ԥ���Ե  ��ا෾") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�Է�����෤�Ԥ���Ե  ��ا෾";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ����٧ (���.) ��ҧ�ص��ˡ���  �Ң��ԪҪ�ҧ������ҧ  �Է�����෤�Ԥྪú���  �.ྪú���") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�Է�����෤�Ԥྪú���  �.ྪú���";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ����٧ (���.) ��ҧ�ص��ˡ���  �Ң��ԪҪ�ҧ������ҧ  �Է�����෤�Ԥ���Թ���  �.���Թ���") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�Է�����෤�Ԥ���Թ���  �.���Թ���";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ����٧ (���.) ��ҧ�ص��ˡ���  �Ң��ԪҪ�ҧ������ҧ �Ңҧҹ෤�Ԥ������ҧ  �Է�������þѴ��ҧ��û��  �.��û��") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�Է�������þѴ��ҧ��û��  �.��û��";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ����٧ (���.) ��ҧ�ص��ˡ���  �Ң��ԪҪ�ҧ俿�ҡ��ѧ  �Ңҧҹ�Դ���俿��  �Է����¡���Ҫվ⾸��ͧ  �.��ҧ�ͧ") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�Է����¡���Ҫվ⾸��ͧ  �.��ҧ�ͧ";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ����٧ (���.) ��ҧ�ص��ˡ���  �Ң��ԪҪ�ҧ俿�ҡ��ѧ  �Ңҧҹ�Դ���俿��  �Է�����෤�Ԥ�صôԵ��  �.�صôԵ��") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�Է�����෤�Ԥ�صôԵ��  �.�صôԵ��";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ����٧ (���.) ��ҧ�ص��ˡ���  �Ң��ԪҪ�ҧ俿�ҡ��ѧ�Ңҧҹ����ͧ��俿��  �Է�����෤�Ԥ��ɳ��š  �.��ɳ��š") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�Է�����෤�Ԥ��ɳ��š  �.��ɳ��š";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ����٧ (���.) ��ҧ�ص��ˡ���  �Ң��ԪҪ�ҧ¹��  �ç���¹෤������Ҥ���ѹ�͡��§�˹��  �.�͹��") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�ç���¹෤������Ҥ���ѹ�͡��§�˹��  �.�͹��";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ����٧ (���.) ��ҧ�ص��ˡ���  �Ң��ԪҪ�ҧ¹��  �ç���¹��չ��෤�����  �.�����ո����Ҫ") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�ç���¹��չ��෤�����  �.�����ո����Ҫ";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ����٧ (���.) ��ҧ�ص��ˡ���  �Ң��ԪҪ�ҧ¹��  �Է����¡���Ҫվ�֧���   �.˹ͧ���") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�Է����¡���Ҫվ�֧���   �.˹ͧ���";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ����٧ (���.) ��ҧ�ص��ˡ���  �Ң��ԪҪ�ҧ¹��  �Է�����෤�Ԥ����ɮ��ҹ�  �.����ɮ��ҹ�") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�Է�����෤�Ԥ����ɮ��ҹ�  �.����ɮ��ҹ�";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ����٧ (���.) ��ҧ�ص��ˡ���  �Ң��ԪҪ�ҧ�¸�  ෤������Ҥ���ѹ�͡��§�˹�� �.�͹��") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "෤������Ҥ���ѹ�͡��§�˹�� �.�͹��";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ����٧ (���.) ��ҧ�ص��ˡ���  �Ң��ԪҪ�ҧ�¸�  �Ңҧҹ�¸�  �Է�����෤�Ԥ����Թ���") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�Է�����෤�Ԥ����Թ���";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ����٧ (���.) ��ҧ�ص��ˡ���  �Ң��ԪҪ�ҧ�¸�  �Ңҧҹ�¸�  �Է�����෤�Ԥ��ɳ��š  �.��ɳ��š") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�Է�����෤�Ԥ��ɳ��š  �.��ɳ��š";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ����٧ (���.) ��ҧ�ص��ˡ���  �Ң��ԪҪ�ҧ���Ǩ  �Է�����෤�Ԥ��ɳ��š  �.��ɳ��š") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�Է�����෤�Ԥ��ɳ��š  �.��ɳ��š";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ����٧ (���.) ��ҧ�ص��ˡ���  �Ң��ԪҪ�ҧ����硷�͹ԡ��  �Է����¡���Ҫվ�ѧ�áѧ��  �.��ШǺ���բѹ��") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�Է����¡���Ҫվ�ѧ�áѧ��  �.��ШǺ���բѹ��";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ����٧ (���.) ��ҧ�ص��ˡ���  �Ң��ԪҪ�ҧ����硷�͹ԡ��෤�Ԥ����������  �Է�����෤�Ԥ���") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�Է�����෤�Ԥ���";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ����٧ (���.) ��ҧ�ص��ˡ���  �Ң��Ԫ�俿��  �Ңҧҹ෤�Ԥ����������  ʶҺѹ෤������Ҫ����  �Է��ࢵ���Ѿ") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "ʶҺѹ෤������Ҫ����  �Է��ࢵ���Ѿ";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ����٧ (���.) �����ø�áԨ  �Ң��Ԫҡ�èѴ��÷�Ѿ�ҡ�������  �Է����¡���Ҫվ˹ͧ�ا���  �.����Թ���") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�Է����¡���Ҫվ˹ͧ�ا���  �.����Թ���";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ����٧ (���.) �����ø�áԨ  �Ң��Ԫҡ�õ�Ҵ         �ç���¹��Ԫ¡����§���  �.��§���") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�ç���¹��Ԫ¡����§���  �.��§���";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ����٧ (���.) �����ø�áԨ  �Ң��Ԫҡ�õ�Ҵ  �ç���¹�صôԵ��෤�����  �.�صôԵ��") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�ç���¹�صôԵ��෤�����  �.�صôԵ��";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ����٧ (���.) �����ø�áԨ  �Ң��Ԫҡ�õ�Ҵ  �Է�����෤�Ԥ�������  �.�������") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�Է�����෤�Ԥ�������  �.�������";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ����٧ (���.) �����ø�áԨ  �Ң��Ԫҡ�õ�Ҵ  �Է����¾�Ԫ¡�ø�����  ��ا෾") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�Է����¾�Ԫ¡�ø�����  ��ا෾";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ����٧ (���.) �����ø�áԨ  �Ң��Ԫҡ�õ�Ҵ  �Է������Ҫ���֡���ӻҧ  �.�ӻҧ") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�Է������Ҫ���֡���ӻҧ  �.�ӻҧ";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ����٧ (���.) �����ø�áԨ  �Ң��Ԫҡ�úѭ��   �ç���¹෤������Ҫ���֡���غ��Ҫ�ҹ�  �.�غ��Ҫ�ҹ�") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�ç���¹෤������Ҫ���֡���غ��Ҫ�ҹ�  �.�غ��Ҫ�ҹ�";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ����٧ (���.) �����ø�áԨ  �Ң��Ԫҡ�úѭ��  �ç���¹�͹��෤����վ�Ԫ¡��  �.�͹��") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�ç���¹�͹��෤����վ�Ԫ¡��  �.�͹��";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ����٧ (���.) �����ø�áԨ  �Ң��Ԫҡ�úѭ��  �ç���¹�͹�蹺����ø�áԨ  �.�͹��") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�ç���¹�͹�蹺����ø�áԨ  �.�͹��";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ����٧ (���.) �����ø�áԨ  �Ң��Ԫҡ�úѭ��  �ç���¹෤������Ҥ���ѹ�͡��§�˹��  �.�͹��") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�ç���¹෤������Ҥ���ѹ�͡��§�˹��  �.�͹��";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ����٧ (���.) �����ø�áԨ  �Ң��Ԫҡ�úѭ��  �ç���¹���෤�Ԥ�Ҥ���ѹ�͡��§�˹��  �.�غ��Ҫ�ҹ�") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�ç���¹���෤�Ԥ�Ҥ���ѹ�͡��§�˹��  �.�غ��Ҫ�ҹ�";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ����٧ (���.) �����ø�áԨ  �Ң��Ԫҡ�úѭ��  �ç���¹��Ԫ¡�þ�ҭ����������  �.�������") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�ç���¹��Ԫ¡�þ�ҭ����������  �.�������";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ����٧ (���.) �����ø�áԨ  �Ң��Ԫҡ�úѭ��  �ç���¹��Ԫ¡������Թ  �.��ШǺ���բѹ��") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�ç���¹��Ԫ¡������Թ  �.��ШǺ���բѹ��";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ����٧ (���.) �����ø�áԨ  �Ң��Ԫҡ�úѭ��  �ç���¹�ѵ���Ԫ¡��  ��ا෾") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�ç���¹�ѵ���Ԫ¡��  ��ا෾";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ����٧ (���.) �����ø�áԨ  �Ң��Ԫҡ�úѭ��  �ç���¹�Ҵ�˭��ӹ���Է������ø�áԨ  �.ʧ���") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�ç���¹�Ҵ�˭��ӹ���Է������ø�áԨ  �.ʧ���";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ����٧ (���.) �����ø�áԨ  �Ң��Ԫҡ�úѭ��  �Է����¡���Ҫվ������  �.��þ��") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�Է����¡���Ҫվ������  �.��þ��";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ����٧ (���.) �����ø�áԨ  �Ң��Ԫҡ�úѭ��  �Է����¡���Ҫվ��������§  �.�����ͧ�͹") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�Է����¡���Ҫվ��������§  �.�����ͧ�͹";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ����٧ (���.) �����ø�áԨ  �Ң��Ԫҡ�úѭ��  �Է�����෤�Ԥ��þ��  �.��þ��") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�Է�����෤�Ԥ��þ��  �.��þ��";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ����٧ (���.) �����ø�áԨ  �Ң��Ԫҡ�úѭ��  �Է�����෤�Ԥ�Ҫ����  �.�Ҫ����") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�Է�����෤�Ԥ�Ҫ����  �.�Ҫ����";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ����٧ (���.) �����ø�áԨ  �Ң��Ԫҡ�úѭ��  �Է�����෤�ԤʡŹ��  �.ʡŹ��") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�Է�����෤�ԤʡŹ��  �.ʡŹ��";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ����٧ (���.) �����ø�áԨ  �Ң��Ԫҡ�úѭ��  �Է������Ҫ���֡�Ҿ�ɳ��š  �.��ɳ��š") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�Է������Ҫ���֡�Ҿ�ɳ��š  �.��ɳ��š";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ����٧ (���.) �����ø�áԨ  �Ң��Ԫҡ�úѭ��  �Է������Ҫ���֡����⢷��  �.��⢷��") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�Է������Ҫ���֡����⢷��  �.��⢷��";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ����٧ (���.) �����ø�áԨ  �Ң��Ԫҡ�úѭ�� �ç���¹෤�Ԥ��Ԫ¡���ѹ�Ծ�  �.�شøҹ�") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�ç���¹෤�Ԥ��Ԫ¡���ѹ�Ծ�  �.�شøҹ�";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ����٧ (���.) �����ø�áԨ  �Ң��Ԫҡ�úѭ�� �ç���¹෤�Ԥ�Ҥ���ѹ�͡��§�˹��  �.ʡŹ��") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�ç���¹෤�Ԥ�Ҥ���ѹ�͡��§�˹��  �.ʡŹ��";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ����٧ (���.) �����ø�áԨ  �Ң��Ԫҡ���Ţ�          �ء��  �Է�����෤�Ԥž����  �.ž����") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�Է�����෤�Ԥž����  �.ž����";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ����٧ (���.) �����ø�áԨ  �Ң��Ԫҡ���Ţҹء��  �Է�����෤�Ԥ�Ӿٹ  �.�Ӿٹ") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�Է�����෤�Ԥ�Ӿٹ  �.�Ӿٹ";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ����٧ (���.) �����ø�áԨ  �Ң��Ԫҡ���Ţҹء��  �Է������Ҫ���֡�����") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�Է������Ҫ���֡�����";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ����٧ (���.) �����ø�áԨ  �Ң��Ԫҡ���Ţҹء��  ʶҺѹ෤������Ҫ����  �Է��ࢵ���Ѿ") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "ʶҺѹ෤������Ҫ����  �Է��ࢵ���Ѿ";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ����٧ (���.) �����ø�áԨ  �Ң��ԪҤ����������áԨ  �ç���¹�¾ѹ�쾧��෤�����  �.�����") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�ç���¹�¾ѹ�쾧��෤�����  �.�����";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ����٧ (���.) �����ø�áԨ  �Ң��ԪҤ����������áԨ  �ç���¹෤�Ԥ��Ҿ����  ��ا෾") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�ç���¹෤�Ԥ��Ҿ����  ��ا෾";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ����٧ (���.) �����ø�áԨ  �Ң��ԪҤ����������áԨ  �ç���¹��Ԫ¡������Թ  �.��ШǺ���բѹ��") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�ç���¹��Ԫ¡������Թ  �.��ШǺ���բѹ��";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ����٧ (���.) �����ø�áԨ  �Ң��ԪҤ����������áԨ  �ç���¹���෤�Թ����๪����෤�����˹ͧ���  �.˹ͧ���") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�ç���¹���෤�Թ����๪����෤�����˹ͧ���  �.˹ͧ���";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ����٧ (���.) �����ø�áԨ  �Ң��ԪҤ����������áԨ  �Է����¡���Ҫվ���ҧᴹ�Թ  �.ʡŹ��") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�Է����¡���Ҫվ���ҧᴹ�Թ  �.ʡŹ��";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ����٧ (���.) �����ø�áԨ  �Ң��ԪҤ����������áԨ  �Է�����෤�Ԥ�����  �.�����") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�Է�����෤�Ԥ�����  �.�����";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ����٧ (���.) �����ø�áԨ  �Ң��ԪҤ����������áԨ  �Է�����෤�Ԥപ�ش�  �.�غ��Ҫ�ҹ�") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�Է�����෤�Ԥപ�ش�  �.�غ��Ҫ�ҹ�";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ����٧ (���.) �����ø�áԨ  �Ң��ԪҤ����������áԨ  �Է�����෤�Ԥྪú�ó�  �.ྪú�ó�") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�Է�����෤�Ԥྪú�ó�  �.ྪú�ó�";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ����٧ (���.) �����ø�áԨ  �Ң��ԪҤ����������áԨ  �Է������Ҫ���֡���غ��Ҫ�ҹ�  �.�غ��Ҫ�ҹ�") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�Է������Ҫ���֡���غ��Ҫ�ҹ�  �.�غ��Ҫ�ҹ�";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ����٧ (���.) �����ø�áԨ  �Ң��ԪҤ����������áԨ  �Ңҧҹ෤������ӹѡ�ҹ  �Է�����෤�Ԥ�������  �.�������") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�Է�����෤�Ԥ�������  �.�������";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ෤�Ԥ (�Ƿ.) �ɵá���  �ç���¹������ʴ��෤����������ä��  �.�����ä��") {
				$EL_CODE = "20";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�ç���¹������ʴ��෤����������ä��  �.�����ä��";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ෤�Ԥ (�Ƿ.) ��ҧ�ص��ˡ���  �Ң�෤�Ԥ���ǡ������Ť��͹Ԥ��  �Է�����෤�Ԥ���Թ���  �.���Թ���") {
				$EL_CODE = "20";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�Է�����෤�Ԥ���Թ���  �.���Թ���";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ෤�Ԥ (�Ƿ.) �����ø�áԨ  �Ң��Ԫҡ�úѭ��  �ç���¹෤�Ԥ��Ԫ¡����§����  �.��§����") {
				$EL_CODE = "20";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�ç���¹෤�Ԥ��Ԫ¡����§����  �.��§����";
			} elseif ($ADR_ROAD=="��С�ȹ�ºѵ��ԪҪվ෤�Ԥ (�Ƿ.) �����ø�áԨ  �Ң��Ԫҡ�úѭ��  �Է�����෤�Ԥ�����  �.�����") {
				$EL_CODE = "20";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�Է�����෤�Ԥ�����  �.�����";
			} elseif ($ADR_ROAD=="��ж��շ�� 6  �ç���¹��ҹ����֡  �.��§����") {
				$EL_CODE = "05";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�ç���¹��ҹ����֡  �.��§����";
			} elseif ($ADR_ROAD=="��ж��֡��  �ç���¹��ҹ��ҧ�ѵ�  �.�ӻҧ") {
				$EL_CODE = "05";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�ç���¹��ҹ��ҧ�ѵ�  �.�ӻҧ";
			} elseif ($ADR_ROAD=="��ж��֡��  �ٹ�����֡�ҹ͡�ç���¹ �.���ͧ  �.�͹��") {
				$EL_CODE = "05";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�ٹ�����֡�ҹ͡�ç���¹ �.���ͧ  �.�͹��";
			} elseif ($ADR_ROAD=="��ж��֡�һշ�� 4  �ç���¹��ҹ��觢��ǵ͡ ( ͧ���ú�������ǹ�ѧ��Ѵ )  �.��§����") {
				$EL_CODE = "05";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�ç���¹��ҹ��觢��ǵ͡ ( ͧ���ú�������ǹ�ѧ��Ѵ )  �.��§����";
			} elseif ($ADR_ROAD=="��ж��֡�һշ�� 4  �ç���¹��ҹ���������������Ե��Ҿ 161  �.���ç  �.�ط���þ����  �.�������") {
				$EL_CODE = "05";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�ç���¹��ҹ���������������Ե��Ҿ 161  �.���ç  �.�ط���þ����  �.�������";
			} elseif ($ADR_ROAD=="��ж��֡�һշ�� 4  �ç���¹��ҹ�������  �.�ѹ��ҧ  �.�ѹ��ҵͧ  �.��§����") {
				$EL_CODE = "05";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�ç���¹��ҹ�������  �.�ѹ��ҧ  �.�ѹ��ҵͧ  �.��§����";
			} elseif ($ADR_ROAD=="��ж��֡�һշ�� 6  �ç���¹��ҹ���ԧ�� (�Ѱ���) �.���ͧ  �.��ҹ") {
				$EL_CODE = "05";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�ç���¹��ҹ���ԧ�� (�Ѱ���) �.���ͧ  �.��ҹ";
			} elseif ($ADR_ROAD=="��ж��֡�һշ��ˡ  �ٹ���ԡ�á���֡�ҹ͡�ç���¹������٧�Թ  �.���") {
				$EL_CODE = "05";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�ٹ���ԡ�á���֡�ҹ͡�ç���¹������٧�Թ  �.���";
			} elseif ($ADR_ROAD=="��ԭ�ҵ�չ�����ʵúѳ�Ե  �Ԫ��͡��û�Ъ�����ѹ����С���ɳ�  �Է����µ��ѹ�͡��§�˹�� �.�͹��") {
				$EL_CODE = "40";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�Է����µ��ѹ�͡��§�˹�� �.�͹��";
			} elseif ($ADR_ROAD=="��ԭ�ҵ���Ѱ��ʵúѳ�Ե  ����Է����Ҫ�ѯ������ä�") {
				$EL_CODE = "40";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "����Է����Ҫ�ѯ������ä�";
			} elseif ($ADR_ROAD=="�Ѹ���֡�ҵ͹��   �ٹ���ԡ�á���֡�ҹ͡�ç���¹ �.�غ��Ҫ�ҹ�  �.�غ��Ҫ�ҹ�") {
				$EL_CODE = "01";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�ٹ���ԡ�á���֡�ҹ͡�ç���¹ �.�غ��Ҫ�ҹ�  �.�غ��Ҫ�ҹ�";
			} elseif ($ADR_ROAD=="�Ѹ���֡�ҵ͹��  �ç�¹���Է��  �.�͹��") {
				$EL_CODE = "01";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�ç�¹���Է��  �.�͹��";
			} elseif ($ADR_ROAD=="�Ѹ���֡�ҵ͹��  �ç�¹��ä���Է�Ҥ�  �.��Ҵ  �.���ͧ  �.�����ä��") {
				$EL_CODE = "01";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�ç�¹��ä���Է�Ҥ�  �.��Ҵ  �.���ͧ  �.�����ä��";
			} elseif ($ADR_ROAD=="�Ѹ���֡�ҵ͹��  �ç���¹�ѡäӤ�ҷ�  �.����ͧ  �.���ͧ  �.�Ӿٹ") {
				$EL_CODE = "01";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�ç���¹�ѡäӤ�ҷ�  �.����ͧ  �.���ͧ  �.�Ӿٹ";
			} elseif ($ADR_ROAD=="�Ѹ���֡�ҵ͹��  �ç���¹�ح�Ѳ�� ( �Ѱ��� )  �.���ͧ  �.����Ҫ����") {
				$EL_CODE = "01";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�ç���¹�ح�Ѳ�� ( �Ѱ��� )  �.���ͧ  �.����Ҫ����";
			} elseif ($ADR_ROAD=="�Ѹ���֡�ҵ͹��  �ç���¹�����ز�  �.�Ӿٹ") {
				$EL_CODE = "01";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�ç���¹�����ز�  �.�Ӿٹ";
			} elseif ($ADR_ROAD=="�Ѹ���֡�ҵ͹��  �ç���¹˹ͧ������ҷ��Ż�  �.�����ո����Ҫ") {
				$EL_CODE = "01";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�ç���¹˹ͧ������ҷ��Ż�  �.�����ո����Ҫ";
			} elseif ($ADR_ROAD=="�Ѹ���֡�ҵ͹��  �ç���¹������ҾԷ�Ҥ� (�Ѱ���)  �.�������  �.ྪú�ó�") {
				$EL_CODE = "01";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�ç���¹������ҾԷ�Ҥ� (�Ѱ���)  �.�������  �.ྪú�ó�";
			} elseif ($ADR_ROAD=="�Ѹ���֡�ҵ͹��  �ç���¹�Թ���Է�Ҥ�  �.˹ͧ���") {
				$EL_CODE = "01";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�ç���¹�Թ���Է�Ҥ�  �.˹ͧ���";
			} elseif ($ADR_ROAD=="�Ѹ���֡�ҵ͹��  �ٹ�����֡�ҹ͡�к���С���֡�ҵ���Ѹ�����  �.��û��  �.��û��") {
				$EL_CODE = "01";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�ٹ�����֡�ҹ͡�к���С���֡�ҵ���Ѹ�����  �.��û��  �.��û��";
			} elseif ($ADR_ROAD=="�Ѹ���֡�ҵ͹��  �ٹ�����֡�ҹ͡�ç���¹ �.�شøҹ�") {
				$EL_CODE = "01";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�ٹ�����֡�ҹ͡�ç���¹ �.�شøҹ�";
			} elseif ($ADR_ROAD=="�Ѹ���֡�ҵ͹��  �ٹ���ԡ�á���֡�ҹ͡�ç���¹ �.��Ф�  �.�ӻҧ") {
				$EL_CODE = "01";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�ٹ���ԡ�á���֡�ҹ͡�ç���¹ �.��Ф�  �.�ӻҧ";
			} elseif ($ADR_ROAD=="�Ѹ���֡�ҵ͹��  �ٹ���ԡ�á���֡�ҹ͡�ç���¹ �.���ͧ�ӻҧ  �.�ӻҧ") {
				$EL_CODE = "01";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�ٹ���ԡ�á���֡�ҹ͡�ç���¹ �.���ͧ�ӻҧ  �.�ӻҧ";
			} elseif ($ADR_ROAD=="�Ѹ���֡�ҵ͹��  �ٹ���ԡ�á���֡�ҹ͡�ç���¹ �.�ѹ��ᾧ  �.��§����") {
				$EL_CODE = "01";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�ٹ���ԡ�á���֡�ҹ͡�ç���¹ �.�ѹ��ᾧ  �.��§����";
			} elseif ($ADR_ROAD=="�Ѹ���֡�ҵ͹��  �ٹ���ԡ�á���֡�ҹ͡�ç���¹����;ҹ  �.��§���") {
				$EL_CODE = "01";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�ٹ���ԡ�á���֡�ҹ͡�ç���¹����;ҹ  �.��§���";
			} elseif ($ADR_ROAD=="�Ѹ���֡�ҵ͹����  �ç���¹�ѹ���Ԫ��  �.�����ä��") {
				$EL_CODE = "02";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�ç���¹�ѹ���Ԫ��  �.�����ä��";
			} elseif ($ADR_ROAD=="�Ѹ���֡�ҵ͹����  �ç���¹�ѹ�������  �.�������") {
				$EL_CODE = "02";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�ç���¹�ѹ�������  �.�������";
			} elseif ($ADR_ROAD=="�Ѹ���֡�ҵ͹����  �ç���¹������ʴ���֡��  �.�����ä��") {
				$EL_CODE = "02";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�ç���¹������ʴ���֡��  �.�����ä��";
			} elseif ($ADR_ROAD=="�Ѹ���֡�ҵ͹����  �ç���¹�س˭ԧ���ͧ����  �.ྪú���") {
				$EL_CODE = "02";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�ç���¹�س˭ԧ���ͧ����  �.ྪú���";
			} elseif ($ADR_ROAD=="�Ѹ���֡�ҵ͹����  �ç���¹�����Է�����  �.��§����") {
				$EL_CODE = "02";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�ç���¹�����Է�����  �.��§����";
			} elseif ($ADR_ROAD=="�Ѹ���֡�ҵ͹����  �ç���¹���ͧ���§  �.�Ҵ������  �.����Ѫ�����  �.��⢷��") {
				$EL_CODE = "02";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�ç���¹���ͧ���§  �.�Ҵ������  �.����Ѫ�����  �.��⢷��";
			} elseif ($ADR_ROAD=="�Ѹ���֡�ҵ͹����  �ç���¹�ѵ���ɮ����ا  �.�Ҫ����") {
				$EL_CODE = "02";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�ç���¹�ѵ���ɮ����ا  �.�Ҫ����";
			} elseif ($ADR_ROAD=="�Ѹ���֡�ҵ͹����  �ç���¹˹ͧ�ҹ�Է��  �.˹ͧ�ҹ  �.�شøҹ�") {
				$EL_CODE = "02";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�ç���¹˹ͧ�ҹ�Է��  �.˹ͧ�ҹ  �.�شøҹ�";
			} elseif ($ADR_ROAD=="�Ѹ���֡�ҵ͹����  �ç���¹��ͧ�͹�֡��  �.�����ͧ�͹") {
				$EL_CODE = "02";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�ç���¹��ͧ�͹�֡��  �.�����ͧ�͹";
			} elseif ($ADR_ROAD=="�Ѹ���֡�ҵ͹����  �ç���¹�����ѹ�Է�����  �.������  �.��ط�ʧ����") {
				$EL_CODE = "02";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�ç���¹�����ѹ�Է�����  �.������  �.��ط�ʧ����";
			} elseif ($ADR_ROAD=="�Ѹ���֡�ҵ͹����  �ٹ�����֡�ҹ͡�к���С���֡�ҵ���Ѹ����� �.���ͧ�͹��  �.�͹��") {
				$EL_CODE = "02";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�ٹ�����֡�ҹ͡�к���С���֡�ҵ���Ѹ����� �.���ͧ�͹��  �.�͹��";
			} elseif ($ADR_ROAD=="�Ѹ���֡�ҵ͹����  �ٹ�����֡�ҹ͡�ç���¹ �.��ɳ��š") {
				$EL_CODE = "02";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�ٹ�����֡�ҹ͡�ç���¹ �.��ɳ��š";
			} elseif ($ADR_ROAD=="�Ѹ���֡�ҵ͹����  �ٹ���ԡ�á���֡�ҹ͡�ç���¹ �.������  �.��§����") {
				$EL_CODE = "02";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�ٹ���ԡ�á���֡�ҹ͡�ç���¹ �.������  �.��§����";
			} elseif ($ADR_ROAD=="�Ѹ���֡�ҵ͹����  �ٹ���ԡ�á���֡�ҹ͡�ç���¹ �.��ó�ҹԤ�  �.ʡŹ��") {
				$EL_CODE = "02";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�ٹ���ԡ�á���֡�ҹ͡�ç���¹ �.��ó�ҹԤ�  �.ʡŹ��";
			} elseif ($ADR_ROAD=="�Ѹ���֡�ҵ͹����  �ٹ���ԡ�á���֡�ҹ͡�ç���¹ �.������  �.��§����") {
				$EL_CODE = "02";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�ٹ���ԡ�á���֡�ҹ͡�ç���¹ �.������  �.��§����";
			} elseif ($ADR_ROAD=="�Ѹ���֡�ҵ͹����  �ٹ���ԡ�á���֡�ҹ͡�ç���¹ �.���ä�š  �.��⢷��") {
				$EL_CODE = "02";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�ٹ���ԡ�á���֡�ҹ͡�ç���¹ �.���ä�š  �.��⢷��";
			} elseif ($ADR_ROAD=="�Ѹ���֡�ҵ͹����  �ٹ���ԡ�á���֡�ҹ͡�ç���¹ �.�����ѡ  �.ྪú�ó�") {
				$EL_CODE = "02";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�ٹ���ԡ�á���֡�ҹ͡�ç���¹ �.�����ѡ  �.ྪú�ó�";
			} elseif ($ADR_ROAD=="�Ѹ���֡�ҵ͹����  �ٹ���ԡ�á���֡�ҹ͡�ç���¹�������§��  �.�����") {
				$EL_CODE = "02";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�ٹ���ԡ�á���֡�ҹ͡�ç���¹�������§��  �.�����";
			} elseif ($ADR_ROAD=="�Ѹ���֡�ҵ͹����  �ٹ���ԡ�á���֡�ҹ͡�ç���¹��������ͧ��§���  �.��§���") {
				$EL_CODE = "02";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�ٹ���ԡ�á���֡�ҹ͡�ç���¹��������ͧ��§���  �.��§���";
			} elseif ($ADR_ROAD=="�Ѹ���֡�ҵ͹���� �ٹ�����֡�ҹ͡�ç���¹�ѧ��Ѵ��ҹ") {
				$EL_CODE = "02";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�ٹ�����֡�ҹ͡�ç���¹�ѧ��Ѵ��ҹ";
			} elseif ($ADR_ROAD=="͹ػ�ԭ����Ż��ʵ��  �Ң��Ԫ���Ż��ʵ��  �Ԫ��͡��ú����ø�áԨ  ʶҺѹ�Ҫ�Ѯ�����ä��  �.�����ä��") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "ʶҺѹ�Ҫ�Ѯ�����ä��  �.�����ä��";
			} elseif ($ADR_ROAD=="͹ػ�ԭ����Ż��ʵ�� ( �.��. ) �����ø�áԨ��С�èѴ���  ������Ԫҡ�èѴ��÷����  �Է����¤��ྪú�ó�  �.ྪú�ó�") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "�Է����¤��ྪú�ó�  �.ྪú�ó�";
			} elseif ($ADR_ROAD=="͹ػ�ԭ����Ż���ʵ��  ������Ԫҡ�ú����ø�áԨ  ʶҺѹ�Ҫ�Ѯ�ӻҧ  �.�ӻҧ") {
				$EL_CODE = "30";
				$EN_CODE = "3010021";
				$EM_CODE = "82005100";
				$EDU_INSTITUTE = "ʶҺѹ�Ҫ�Ѯ�ӻҧ  �.�ӻҧ";
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

// ��ͺ���Ǣ���Ҫ��� 70253 
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
			if (substr($FATHER_NAME, 0, 3)=="���") {
				$PN_CODE = "003";
				$FATHER_NAME = substr($FATHER_NAME, 3);
			} elseif (substr($FATHER_NAME, 0, 3)=="�ҧ") {
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
			if (substr($MATHER_NAME, 0, 3)=="���") {
				$PN_CODE = "003";
				$MATHER_NAME = substr($MATHER_NAME, 3);
			} elseif (substr($MATHER_NAME, 0, 3)=="�ҧ") {
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
			if (substr($MATE_NAME, 0, 3)=="���") {
				$PN_CODE = "003";
				$MATE_NAME = substr($MATE_NAME, 3);
			} elseif (substr($MATE_NAME, 0, 3)=="�ҧ") {
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

// ��ͺ�����١��ҧ��Ш� 32365 
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

// ��ͺ���Ǿ�ѡ�ҹ�Ҫ��� 3258 
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
// �ص� 
// �صâ���Ҫ��� 8714 
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

// �ص��١��ҧ��Ш� 11010 
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
// �������������͹��� 72 ok *************************************************************************
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
// ��ô�ç���˹� 571904

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

		$cmd = " SELECT [�ӴѺ���] AS PER_ID, [���˹��Ţ��� ( �� )] AS SAH_POS_NO, [����ͧ���] AS FULLNAME, [���˹� �á � 1 �� 54] AS SAH_POSITION54, 
						  [��Ҩ�ҧ �á � 1 �� 54] AS POEM_NO, [��Ҩ�ҧ �á � 30 �� 54] AS SAH_OLD_SALARY54, [��Ҩ�ҧ �á � 1 �� 54] AS SAH_SALARY54, [������͹ 1 �� 54 ��� %] AS SAH_PERCENT_UP, 
						  [1 �� 54 �á ������͹] AS PER_SURNAME, 
						  [1 �� 54 �á ���������͹����] AS SAH_REMARK54, [˹��§ҹ�ʹ�����͹��� 1 �� 54 %] AS ORG_ID54, [˹��§ҹ�ʹ�����͹��� 1 �� 54 ���Թ] AS LAST_COST_OF_LIVING, [����ʹ�����͹��� 1 �� 54 %] AS LAST_TOTAL_INCOME, 
						  [����ʹ�����͹��� 1 �� 54 ���Թ] AS LAST_PERCENT, [���˹��§ҹ���������% 1 �� 54] AS PER_SALARY, [���˹��§ҹ����������Թ���͹��� 1 �� 54] AS COST_OF_LIVING, [�ѵ�Ҥ�Ҩ�ҧ���˹��§ҹ������ 1 �� 54] AS TOTAL_INCOME, 
						  [����ʹ�����͹��� 1 �� 54 %] AS CURR_PERCENT, [����ʹ�����͹��� 1 �� 54 ���Թ] AS NEXT_SALARY, [����ʹ�����͹��� 1 �� 54 ������ %] AS SAH_PERCENT_UP54, [����ʹ�����͹��� 1 �� 54 ���Թ������] AS SAH_SALARY_UP54, 
						  [�Թ������ä�ͧ�վ���Ǥ���  1  �� 54] AS EP_CODE, [������Թ���������� 1 �� 54] AS ORG_ID_1, [���˹� �á � 1 �� 55] AS SAH_POSITION55_UP, [��ҵͺ᷹ �á � 31 �� 54] AS ORG_ID_ASS, [��ҵͺ᷹ �á � 1 �� 55] AS NULL1, 
						  [��ҵͺ᷹�á��è�] AS TEXT_STARTDATE, [�Թ���·�����Ѻ 1 �� 55] AS POEMS_REMARK1, [�ѵ���á��è����� 1 �� 55] AS OLD_PER_NAME, [�Թ������ä�ͧ���Ǥ���  1 �� 55] AS POEMS_REMARK2, 
						  [������Թ���������� 1 �� 55] AS POEMS_REMARK3, [���˹� �á � 1 �� 55] AS SAH_POSITION55, [��Ҩ�ҧ �á � 1 �� 55] AS OTHER2, [��Ҩ�ҧ �á � 30 �� 55] AS SAH_OLD_SALARY55, [��Ҩ�ҧ �á � 1 �� 55] AS SAH_SALARY55, [1 �� 55 �á ������͹] AS OTHER5, 
						  [1 �� 55 �á ���������͹����] AS SAH_REMARK55, [��ṹ������Ѻ��û����Թ 1 �� 55] AS OTHER7, [˹��§ҹ�ʹ�����͹��� 1 �� 55 %] AS OTHER8, [˹��§ҹ�ʹ�����͹��� 1 �� 55 ���Թ] AS OTHER9, [�ʹ����ͧ˹��§ҹ] AS OTHER10, [����˹��§ҹ������] AS OTHER11, 
						  [����ʹ�����͹��� 1 �� 55 %] AS OTHER12, [����ʹ�����͹��� 1 �� 55 ���Թ] AS OTHER13, [�ʹ�����Ҩ�ҧ�ͧ��� �� %] AS OTHER14, [�ʹ�����Ҩ�ҧ�ͧ���] AS OTHER15, [������·�����] AS OTHER16, [����ʹ�����͹��� 1 �� 55 %] AS OTHER17, 
						  [����ʹ�����͹��� 1 �� 55 ���Թ] AS OTHER18, [����ʹ�����͹��� 1 �� 55 ������ %] AS PER_RETIREYEAR, [੾�з�����Ѻ�Թ ����͹��� 1 �� 55 ���Թ] AS DAY1, [����ʹ�����͹��� 1 �� 55 ���Թ������] AS YEAR1, [�Թ������ä�ͧ�վ���Ǥ���  1  �� 55] AS MONTH1, 
						  [������Թ���������� 1 �� 55] AS NULL2, [�ӹǹǧ�Թ����͹��� 1 �� 55] AS NULL3, [੾�м�����Ѻ�������͹��ҵͺ᷹ 1 �� 55] AS NULL4, [�ʹ����ѵ�Ҥ�Ҩ�ҧ������ 6 ��ͧ����Թ] AS NULL5, [���˹� �á � 1 �� 56] AS SAH_POSITION56, [��Ҩ�ҧ �á � 1 �� 56] AS NULL7, 
						  [��Ҩ�ҧ �á � 30 �� 56] AS SAH_OLD_SALARY56, [��Ҩ�ҧ �á � 1 �� 56] AS SAH_SALARY56, [1 �� 56 �á ������͹] AS NULL8, [1 �� 56 �á ���������͹����] AS SAH_REMARK56, [��ṹ�����ķ���ͧ�ҹ������Ѻ��û����Թ 1 ��� 56] AS NULL10, [��ṹ�����Թ���ö�Тͧ�ҹ������Ѻ��û����Թ 1 ��� 56] AS PER_BIRTHDATE, 
						  [��ṹ���㹡�û����Թ 1 ��� 56] AS BIRTH_DAY, [��ṹ�����ķ���ͧ�ҹ������Ѻ��û����Թ 1 �� 56] AS BIRTH_MONTH, [��ṹ�����Թ���ö�Тͧ�ҹ������Ѻ��û����Թ 1 �� 56] AS BIRTH_YEAR, [��ṹ���㹡�û����Թ 1 �� 56] AS PER_RETIREDATE, [��ṹ�����駻� 2556] AS EFFECTIVE_DAY, 
						  [����˹��§ҹ������ 1 �� 56] AS EFFECTIVE_MONTH, [˹��§ҹ�ʹ�����͹��� 1 �� 56 %] AS EFFECTIVE_YEAR, [˹��§ҹ�ʹ�����͹��� 1 �� 56 ���Թ] AS POH_EFFECTIVEDATE, [˹��§ҹ�ʹ�����͹��� 1 �� 56 ������Թ������] AS PER_STARTDATE, 
						  [����ʹ�����͹��� 1 �� 56 %] AS START_YEAR, [����ʹ�����͹��� 1 �� 56 ���Թ] AS PER_OCCUPYDATE, [����ʹ�����͹��� 1 �� 56 % ���������] AS POH_EFFECTIVEDATE2, [����ʹ�����͹��� 1 �� 56 ���Թ���������] AS POH_ENDDATE, [����ʹ�����͹��鹡óվ���� 1 �� 56] AS EP_OTHERNAME, [������·����� 1 �� 56] AS PER_CONTACT_COUNT, [���˹��§ҹ���������% 1 �� 56] AS PER_DOCNO, [���˹��§ҹ����������Թ����͹��� 1 �� 56 ���Թ] AS PER_CARDNO, [���˹��§ҹ����������Թ����͹��� 1 �� 56 ������Թ������] AS EM_NAME, [����ʹ�����͹��� 1 �� 56 %] AS LEVEL_NO, [����ʹ�����͹��� 1 �� 56 ���Թ] AS CATEGORY_SAL_NAME, 
						  [����ʹ�����͹��� 1 �� 56 ������ %] AS SALARY_START, [੾�з�����Ѻ�Թ ����͹��� 1 �� 56 ���Թ] AS EP_OTHERNAME1, [����ʹ�����͹��� 1 �� 56 ���Թ������] AS SALARY_END, [�Թ������ä�ͧ�վ���Ǥ���  1  �� 56] AS SALARY_DAY_END, [������Թ���������� 1 �� 56] AS CLUSTER_CODE1, [��ѡ�ҹ�Ҫ������Է������͹��� 1 ���Ҥ� 2556] AS LEVEL_NAME2, [��ѡ�ҹ�Ҫ��÷����ӹǳǧ�Թ����͹��� 1 �ѹ��¹ 2556] AS LEVEL_NAME3, [�����ѧ�Ѵ˹��§ҹ��������͹��� 1  ��  2556] AS EP_NAME1, [�ʹ����ѵ�Ҥ�Ҩ�ҧ�� 2556 ������ 6 ��ͧ����Թ] AS LEVEL_NO1, [�ʹ����ѵ�Ҥ�Ҩ�ҧ�� 2556 ������ 6 �Թ��ͧ����Թ] AS LEVEL_NO2, [���˹� �á � 1 �� 57] AS SAH_POSITION57, [��Ҩ�ҧ �á � 1 �� 57] AS LEVEL_NO4, [��Ҩ�ҧ �á � 30 �� 57] AS SAH_OLD_SALARY57, [��Ҩ�ҧ �á � 1 �� 57] AS SAH_SALARY57, [1 �� 57 �á ������͹] AS CATEGORY_SAL_NAME1, 
						  [1 �� 57 �á ���������͹����] AS SAH_REMARK57, [��ṹ�����ķ���ͧ�ҹ������Ѻ��û����Թ 1 ��� 57] AS EP_OTHERNAME2, [��ṹ�����Թ���ö�Тͧ�ҹ������Ѻ��û����Թ 1 ��� 57] AS CLUSTER_CODE2, [��ṹ���㹡�û����Թ 1 ��� 57] AS LEVEL_NAME4, [��ṹ�����ķ���ͧ�ҹ������Ѻ��û����Թ 1 �� 567] AS LEVEL_NAME5, [��ṹ�����Թ���ö�Тͧ�ҹ������Ѻ��û����Թ 1 �� 57] AS EP_NAME2, [��ṹ���㹡�û����Թ 1 �� 57] AS LEVEL_NO7, [��ṹ�����駻� 2557] AS CATEGORY_SAL_NAME2, 
						  [����˹��§ҹ������ 1 �� 57] AS SALARY_START2, [˹��§ҹ�ʹ�����͹��� 1 �� 57 %] AS EP_OTHERNAME3, [˹��§ҹ�ʹ�����͹��� 1 �� 57 ���Թ] AS SALARY_END1, [����ʹ�����͹��� 1 �� 57 %] AS SALARY_DAY_END1, [����ʹ�����͹��� 1 �� 57 ���Թ] AS CLUSTER_CODE3, [������·����� 1 �� 57] AS LEVEL_NAME6, [����ʹ�����͹��� 1 �� 57 %] AS LEVEL_NAME7, [����ʹ�����͹��� 1 �� 57 ���Թ] AS EP_NAME3, [����ʹ�����͹��� 1 �� 57 ������ %] AS LEVEL_NO8, [����ʹ�����͹��� 1 �� 57 ���Թ������] AS CATEGORY_SAL_NAME3, 
						  [�Թ������ä�ͧ�վ���Ǥ���  1  �� 57] AS SALARY_START3, [������Թ���������� 1 �� 57] AS EP_OTHERNAME4, [��ѡ�ҹ�Ҫ������Է������͹��� 1 ���Ҥ� 2557] AS CLUSTER_CODE4, [��ѡ�ҹ�Ҫ��÷����ӹǳǧ�Թ����͹��� 1 �ѹ��¹ 2557] AS LEVEL_NAME8, [�����ѧ�Ѵ˹��§ҹ��������͹��� 1  ��  2557] AS LEVEL_NAME9
						  FROM [��ͺ3 ����͹ 54 - 57]
						  WHERE [���˹��Ţ��� ( �� )] IS NOT NULL and [����ͧ���] IS NOT NULL 
						  ORDER BY [�ӴѺ���] ";

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
			if ($PM_CODE=="0" && $PM_NAME=="�������֡�Ҹԡ��") $PM_CODE = "50031";
			if ($PM_CODE=="20214") $PM_CODE = "";
			if ($PM_CODE=="�") $PM_CODE = "";
			if ($PM_CODE=="60011") $PM_CODE = "";
			if ($PM_CODE=="�") $PM_CODE = "";

			$POH_ORG1 = "��з�ǧ���Ҥ�";
			$POH_ORG2 = "����ҧ��ǧ";
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

			if ($GOVPOS_COMCODE=="0") $MOV_CODE = "6"; // �Դ **********************
			elseif ($GOVPOS_COMCODE=="1") $MOV_CODE = "102"; // ���ͧ��Ժѵ��Ҫ���
			elseif ($GOVPOS_COMCODE=="10") $MOV_CODE = "21310"; // 0.5 ���
			elseif ($GOVPOS_COMCODE=="11") $MOV_CODE = "21330"; // 1.5 ���
			elseif ($GOVPOS_COMCODE=="12") $MOV_CODE = "213"; // ����͹����Թ��͹
			elseif ($GOVPOS_COMCODE=="13") $MOV_CODE = "11010"; // �ѡ���Ҫ���᷹
			elseif ($GOVPOS_COMCODE=="14") $MOV_CODE = "106"; // �͹� ******************
			elseif ($GOVPOS_COMCODE=="15") $MOV_CODE = "11810"; // ���͡
			elseif ($GOVPOS_COMCODE=="16") $MOV_CODE = "11020"; // �ѡ�ҡ��㹵��˹�
			elseif ($GOVPOS_COMCODE=="17") $MOV_CODE = "21370"; // ������Թ��͹ ************************
			elseif ($GOVPOS_COMCODE=="18") $MOV_CODE = "103"; // ���¢���Ҫ���
			elseif ($GOVPOS_COMCODE=="19") $MOV_CODE = "99999"; // �觵�����˹�ҷ���ʴػ�Ш��ç��� *************
			elseif ($GOVPOS_COMCODE=="2") $MOV_CODE = "10210"; // �鹷��ͧ��Ժѵ��Ҫ���
			elseif ($GOVPOS_COMCODE=="21") $MOV_CODE = "105"; // �͹��
			elseif ($GOVPOS_COMCODE=="22") $MOV_CODE = "10140"; // ��Ѻ����Ѻ�Ҫ�������
			elseif ($GOVPOS_COMCODE=="23") $MOV_CODE = "216"; // Ŵ����Թ��͹
			elseif ($GOVPOS_COMCODE=="24") $MOV_CODE = "21510"; // ��Ѻ�Թ��͹����ز�
			elseif ($GOVPOS_COMCODE=="25") $MOV_CODE = "12410"; // Ŵ�дѺ
			elseif ($GOVPOS_COMCODE=="26") $MOV_CODE = "12310"; // ���
			elseif ($GOVPOS_COMCODE=="27") $MOV_CODE = "11910"; // ���³
			elseif ($GOVPOS_COMCODE=="28") $MOV_CODE = "12210"; // ����͡
			elseif ($GOVPOS_COMCODE=="29") $MOV_CODE = "106"; // ����͹
			elseif ($GOVPOS_COMCODE=="3") $MOV_CODE = "21520"; // ��Ѻ�ѵ���Թ��͹
			elseif ($GOVPOS_COMCODE=="30") $MOV_CODE = "11420"; // ��䢤����
			elseif ($GOVPOS_COMCODE=="31") $MOV_CODE = "21540"; // ��Ѻ�Թ���������Ѻ������ú 
			elseif ($GOVPOS_COMCODE=="32") $MOV_CODE = "21380"; // ����͹����Թ��͹����Ҫ��ü�����³���� ***************************
			elseif ($GOVPOS_COMCODE=="33") $MOV_CODE = "11410"; // ¡��ԡ��ú�è� ***********************
			elseif ($GOVPOS_COMCODE=="34") $MOV_CODE = "21370"; // ���������͹���
			elseif ($GOVPOS_COMCODE=="35") $MOV_CODE = "21320"; // 1 ���
			elseif ($GOVPOS_COMCODE=="36") $MOV_CODE = "21420"; // 2%
			elseif ($GOVPOS_COMCODE=="37") $MOV_CODE = "21430"; // 4%
			elseif ($GOVPOS_COMCODE=="38") $MOV_CODE = "11410"; // ¡��ԡ����Ѻ�͹ *******************
			elseif ($GOVPOS_COMCODE=="39") $MOV_CODE = "120"; // ����͡
			elseif ($GOVPOS_COMCODE=="4") $MOV_CODE = "99999"; // �Ѵ�͹���˹� ********************
			elseif ($GOVPOS_COMCODE=="40") $MOV_CODE = "12110"; // �Ŵ�͡
			elseif ($GOVPOS_COMCODE=="41") $MOV_CODE = "104"; // ����͹����Ҫ���
			elseif ($GOVPOS_COMCODE=="42") $MOV_CODE = "11830"; // ���³���ء�͹��˹�
			elseif ($GOVPOS_COMCODE=="43") $MOV_CODE = "99999"; // ��䢵Ѵ�͹���˹�
			elseif ($GOVPOS_COMCODE=="5") $MOV_CODE = "21410"; // ������
			elseif ($GOVPOS_COMCODE=="6") $MOV_CODE = "21350"; // ����͹����Թ��͹����Ҫ��áóվ����
			elseif ($GOVPOS_COMCODE=="7") $MOV_CODE = "11310"; // ��Ժѵ��Ҫ��� *****************
			elseif ($GOVPOS_COMCODE=="8") $MOV_CODE = "21340"; // 2 ���
			elseif ($GOVPOS_COMCODE=="9") $MOV_CODE = "21305"; // ����͹�Թ��͹
			elseif ($GOVPOS_COMCODE=="99") $MOV_CODE = "99999"; // 
			elseif ($GOVPOS_COMCODE=="999") $MOV_CODE = "99999"; // 
			else { 
				$MOV_CODE = "99999"; 
				if ($FLAG_TO_NAME) echo "�������������͹���->$GOVPOS_COMCODE<br>"; 
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
			if ($REMARK=="����͹��� 0.5 ���" || $REMARK=="��Ѻ���觢��" || $REMARK=="��Ѻ0.5���" || $REMARK=="��Ѻ0.5 ���" || $REMARK=="��Ѻ 0.5���" || 
				$REMARK=="��Ѻ 0.5 ���" || $REMARK=="��Ѻ .5���" || $REMARK=="��Ѻ .5 ���" || $REMARK=="��Ѻ  0.5 ���" || $REMARK=="��Ѻ  0.5  ���") 
				$SM_CODE = "2";
			elseif ($REMARK=="˹�觢��" || $REMARK=="����͹˹�觢��" || $REMARK=="����͹��� 1 ���" || $REMARK=="��Ѻ˹�觢��" || $REMARK=="��Ѻ1���" || 
				$REMARK=="��Ѻ1 ���" || $REMARK=="��Ѻ 1���" || $REMARK=="��Ѻ 1 ���" || $REMARK=="��Ѻ 1.0���" || $REMARK=="��Ѻ 1.0 ���" || 
				$REMARK=="��Ѻ  1 ���" || $REMARK=="��Ѻ  1  ���") 
				$SM_CODE = "3";
			elseif ($REMARK=="˹�觢�鹤���" || $REMARK=="����͹��� 1.5 ���" || $REMARK=="��Ѻ˹�觢�鹤���" || $REMARK=="��Ѻ1.5���" || $REMARK=="��Ѻ 1.5���" || 
				$REMARK=="��Ѻ 1.5 ���") 
				$SM_CODE = "4";
			elseif ($REMARK=="�ͧ���" || $REMARK=="����͹��� 2 ���" || $REMARK=="��Ѻ�ͧ���" || $REMARK=="��Ѻ 2 ���" || $REMARK=="��Ѻ 2.0 ���" || 
				$REMARK=="��Ѻ  2  ���" ) 
				$SM_CODE = "5";
			elseif ($REMARK=="���������͹���") 
				$SM_CODE = "1";
			elseif ($FLAG_TO_NAME=="�Թ�ͺ᷹����� 2 %" || $REMARK=="�Թ�ͺ᷹����� 2 %") 
				{ $SM_CODE = "8"; $EX_CODE = "015"; } 
			elseif ($FLAG_TO_NAME=="�Թ�ͺ᷹����� 4 %" || $REMARK=="�Թ�ͺ᷹����� 4 %" || $REMARK=="��Ѻ 4 �����繵�") 
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
// ��Ѻ��ا����ѵԡ���Ѻ�Թ��͹ *******************************************************
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
// ��Ѻ��ا����ѵԡ�ô�ç���˹� ***************************************************
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