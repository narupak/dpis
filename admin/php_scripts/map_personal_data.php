<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	
//	$db_dpis_n = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	// ==== use for testing phase =====
	$db_dpis_n = new connect_db($db_host, $db_name, $db_user, $db_pwd);
	// ==========================

	$cmd = " select 	a.ORG_ID, b.ORG_NAME
					 from 		PER_CONTROL a, PER_ORG b
					 where	a.ORG_ID=b.ORG_ID
				   ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$DEPARTMENT_ID = $data[ORG_ID];
	$DEPARTMENT_NAME = $data[ORG_NAME];
	
	if(!$PER_GENDER) $PER_GENDER = 1;
	
	$UPDATE_DATE = date("Y-m-d H:i:s");
	
	if($command=="ADD" || $command=="UPDATE"){
		if($PER_BIRTHDATE){
			$arr_temp = explode("/", $PER_BIRTHDATE);
			$PER_BIRTHDATE = ($arr_temp[2] - 543) ."-". str_pad($arr_temp[1], 2, "0", STR_PAD_LEFT) ."-". str_pad($arr_temp[0], 2, "0", STR_PAD_LEFT);
		} // end if
		if($PER_STARTDATE){
			$arr_temp = explode("/", $PER_STARTDATE);
			$PER_STARTDATE = ($arr_temp[2] - 543) ."-". str_pad($arr_temp[1], 2, "0", STR_PAD_LEFT) ."-". str_pad($arr_temp[0], 2, "0", STR_PAD_LEFT);
		} // end if
		if($PER_OCCUPYDATE){
			$arr_temp = explode("/", $PER_OCCUPYDATE);
			$PER_OCCUPYDATE = ($arr_temp[2] - 543) ."-". str_pad($arr_temp[1], 2, "0", STR_PAD_LEFT) ."-". str_pad($arr_temp[0], 2, "0", STR_PAD_LEFT);
		} // end if
		if($PER_RETIREDATE){
			$arr_temp = explode("/", $PER_RETIREDATE);
			$PER_RETIREDATE = ($arr_temp[2] - 543) ."-". str_pad($arr_temp[1], 2, "0", STR_PAD_LEFT) ."-". str_pad($arr_temp[0], 2, "0", STR_PAD_LEFT);
		} // end if
		if($POS_DATE){
			$arr_temp = explode("/", $POS_DATE);
			$POS_DATE = ($arr_temp[2] - 543) ."-". str_pad($arr_temp[1], 2, "0", STR_PAD_LEFT) ."-". str_pad($arr_temp[0], 2, "0", STR_PAD_LEFT);
		} // end if
		if($POS_GET_DATE){
			$arr_temp = explode("/", $POS_GET_DATE);
			$POS_GET_DATE = ($arr_temp[2] - 543) ."-". str_pad($arr_temp[1], 2, "0", STR_PAD_LEFT) ."-". str_pad($arr_temp[0], 2, "0", STR_PAD_LEFT);
		} // end if
		if($POS_CHANGE_DATE){
			$arr_temp = explode("/", $POS_CHANGE_DATE);
			$POS_CHANGE_DATE = ($arr_temp[2] - 543) ."-". str_pad($arr_temp[1], 2, "0", STR_PAD_LEFT) ."-". str_pad($arr_temp[0], 2, "0", STR_PAD_LEFT);
		} // end if
	} // end if
	
	if($command=="ADD"){
		$cmd = " select max(PER_ID) from PER_FORMULA ";
		$db->send_cmd($cmd);
		$data = $db->get_data();
		$PER_ID = $data[0] + 1;
		
		$cmd = " insert into PER_FORMULA 
							(	PER_ID, LEVEL_NO, PL_CODE, PT_CODE, PT_CODE_N, POS_ID, POS_NO,
								PN_CODE, PER_NAME, PER_SURNAME, PER_ENG_NAME, PER_ENG_SURNAME, 
								OT_CODE, PER_CARDNO, PER_OFFNO, PER_GENDER, PER_TYPE, PER_STATUS,
								PER_BIRTHDATE, PER_STARTDATE, PER_OCCUPYDATE, PER_RETIREDATE,
								POS_SALARY, POS_MGTSALARY, POS_DATE, POS_GET_DATE, POS_CHANGE_DATE, 
								PM_CODE, CL_NAME, SKILL_CODE, ORG_ID, ORG_ID_1, ORG_ID_2, 
								EN_CODE, EM_CODE, PV_CODE, CT_CODE, ABILITY,
								UPDATE_USER, UPDATE_DATE, INSERT_TYPE )
						 values
						 	(	$PER_ID, '$LEVEL_NO', '$PL_CODE', '$PT_CODE', '$PT_CODE_N', $POS_ID, '$POS_NO',
								'$PN_CODE', '$PER_NAME', '$PER_SURNAME', '$PER_ENG_NAME', '$PER_ENG_SURNAME', 
								'$OT_CODE', '$PER_CARDNO', '$PER_OFFNO', $PER_GENDER, 1, 1, 
								'$PER_BIRTHDATE', '$PER_STARTDATE', '$PER_OCCUPYDATE', '$PER_RETIREDATE',
								$POS_SALARY, $POS_MGTSALARY, '$POS_DATE', '$POS_GET_DATE', '$POS_CHANGE_DATE', 
								'$PM_CODE', '$CL_NAME', '$SKILL_CODE', '$ORG_ID', '$ORG_ID_1', '$ORG_ID_2', 
								'$EN_CODE', '$EM_CODE', '$PV_CODE', '$CT_CODE', '$ABILITY',
								$SESS_USERID, '$UPDATE_DATE', 'M' )
						  ";
		$db->send_cmd($cmd);
//		$db->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > เพิ่มข้อมูลข้าราชการ [ $PER_ID : $PER_NAME $PER_SURNAME ]");
	} // end if

	if($command=="UPDATE" && $PER_ID){
		$cmd = " update PER_FORMULA  set
							LEVEL_NO = '$LEVEL_NO', 
							PL_CODE = '$PL_CODE', 
							PT_CODE = '$PT_CODE', 
							PT_CODE_N = '$PT_CODE_N', 
							POS_ID = $POS_ID, 
							POS_NO = '$POS_NO',
							PN_CODE = '$PN_CODE', 
							PER_NAME = '$PER_NAME', PER_SURNAME = '$PER_SURNAME', 
							PER_ENG_NAME = '$PER_ENG_NAME', PER_ENG_SURNAME = '$PER_ENG_SURNAME', 
							OT_CODE = '$OT_CODE', 
							PER_CARDNO = '$PER_CARDNO', 
							PER_OFFNO = '$PER_OFFNO', 
							PER_GENDER = $PER_GENDER, 
							PER_BIRTHDATE = '$PER_BIRTHDATE', 
							PER_STARTDATE = '$PER_STARTDATE', 
							PER_OCCUPYDATE = '$PER_OCCUPYDATE', 
							PER_RETIREDATE = '$PER_RETIREDATE',
							POS_SALARY = '$POS_SALARY', 
							POS_MGTSALARY = '$POS_MGTSALARY', 
							POS_DATE = '$POS_DATE', 
							POS_GET_DATE = '$POS_GET_DATE', 
							POS_CHANGE_DATE = '$POS_CHANGE_DATE', 
							PM_CODE = '$PM_CODE', 
							CL_NAME = '$CL_NAME', 
							SKILL_CODE = '$SKILL_CODE', 
							ORG_ID = '$ORG_ID', ORG_ID_1 = '$ORG_ID_1', ORG_ID_2 = '$ORG_ID_2', 
							EN_CODE = '$EN_CODE', EM_CODE = '$EM_CODE', 
							PV_CODE = '$PV_CODE', CT_CODE = '$CT_CODE', 
							ABILITY = '$ABILITY',
							UPDATE_USER = $SESS_USERID, UPDATE_DATE = '$UPDATE_DATE'
						 where PER_ID=$PER_ID
					  ";
		$db->send_cmd($cmd);
//		$db->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > แก้ไขข้อมูลข้าราชการ [ $PER_ID : $PER_NAME $PER_SURNAME ]");
	} // end if
	
	if($command=="DELETE" && $PER_ID){
		$cmd = " select	 PER_NAME, PER_SURNAME from PER_FORMULA where PER_ID=$PER_ID ";
		$db->send_cmd($cmd);
		$data = $db->get_array();
		$PER_NAME = $data[PER_NAME];
		$PER_SURNAME = $data[PER_SURNAME];

		$cmd = " delete from PER_FORMULA where PER_ID=$PER_ID ";	
		$db->send_cmd($cmd);
			
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > ลบข้อมูลข้าราชการ [ $PER_ID : $PER_NAME $PER_SURNAME ]");		
	} // end if

	if($PER_ID){
		$cmd = "	select		LEVEL_NO, PL_CODE, PT_CODE, PT_CODE_N, POS_ID, POS_NO,
											PN_CODE, PER_NAME, PER_SURNAME, PER_ENG_NAME, PER_ENG_SURNAME,
											OT_CODE, PER_CARDNO, PER_OFFNO, PER_GENDER,
											PER_BIRTHDATE, PER_STARTDATE, PER_OCCUPYDATE, PER_RETIREDATE,
											POS_SALARY, POS_MGTSALARY, POS_DATE, POS_GET_DATE, POS_CHANGE_DATE,
											PM_CODE, CL_NAME, SKILL_CODE, ORG_ID, ORG_ID_1, ORG_ID_2, 
											EN_CODE, EM_CODE, PV_CODE, CT_CODE, ABILITY
							from		PER_FORMULA
							where		PER_ID=$PER_ID
					   ";
		$db->send_cmd($cmd);
//		$db->show_error();
		$data = $db->get_array();
		$LEVEL_NO = trim($data[LEVEL_NO]);
		$POS_ID = trim($data[POS_ID]);
		$POS_NO = trim($data[POS_NO]);
/*
		$cmd = " select POS_NO from PER_POSITION where POS_ID=$POS_ID ";
		$db_dpis->send_cmd($cmd);
		$data_dpis = $db_dpis->get_array();
		$POS_NO = trim($data_dpis[POS_NO]);
*/		
		$PL_CODE = trim($data[PL_CODE]);
		$cmd = " select PL_NAME from PER_LINE where trim(PL_CODE)='$PL_CODE' ";
		$db_dpis->send_cmd($cmd);
		$data_dpis = $db_dpis->get_array();
		$PL_NAME = trim($data_dpis[PL_NAME]);
		
		$PT_CODE = trim($data[PT_CODE]);
		$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE)='$PT_CODE' ";
		$db_dpis->send_cmd($cmd);
		$data_dpis = $db_dpis->get_array();
		$PT_NAME = trim($data_dpis[PT_NAME]);

		$PT_CODE_N = trim($data[PT_CODE_N]);
		$cmd = " select PT_NAME_N from PER_TYPE_N where trim(PT_CODE_N)='$PT_CODE_N' ";
		$db_dpis_n->send_cmd($cmd);
		$data_dpis = $db_dpis_n->get_array();
		$PT_NAME_N = trim($data_dpis[PT_NAME_N]);
		
		$PN_CODE = trim($data[PN_CODE]);
		$PER_NAME = trim($data[PER_NAME]);
		$PER_SURNAME = trim($data[PER_SURNAME]);
		$PER_ENG_NAME = trim($data[PER_ENG_NAME]);
		$PER_ENG_SURNAME = trim($data[PER_ENG_SURNAME]);

		$OT_CODE = trim($data[OT_CODE]);
		$cmd = " select OT_NAME from PER_OFF_TYPE where trim(OT_CODE)='$OT_CODE' ";
		$db_dpis->send_cmd($cmd);
		$data_dpis = $db_dpis->get_array();
		$OT_NAME = trim($data_dpis[OT_NAME]);

		$PER_CARDNO = trim($data[PER_CARDNO]);
		$PER_OFFNO = trim($data[PER_OFFNO]);
		$PER_GENDER = trim($data[PER_GENDER]);
		
		$PER_BIRTHDATE = trim($data[PER_BIRTHDATE]);
		if($PER_BIRTHDATE){
			$arr_temp = explode("-", substr($PER_BIRTHDATE, 0, 10));
			$PER_BIRTHDATE = ($arr_temp[0] + 543) ."-". $arr_temp[1] ."-". $arr_temp[2];
		} // end if
		
		$PER_STARTDATE = trim($data[PER_STARTDATE]);
		if($PER_STARTDATE){
			$arr_temp = explode("-", substr($PER_STARTDATE, 0, 10));
			$PER_STARTDATE = ($arr_temp[0] + 543) ."-". $arr_temp[1] ."-". $arr_temp[2];
		} // end if

		$PER_OCCUPYDATE = trim($data[PER_OCCUPYDATE]);
		if($PER_OCCUPYDATE){
			$arr_temp = explode("-", substr($PER_OCCUPYDATE, 0, 10));
			$PER_OCCUPYDATE = ($arr_temp[0] + 543) ."-". $arr_temp[1] ."-". $arr_temp[2];
		} // end if

		$PER_RETIREDATE = trim($data[PER_RETIREDATE]);
		if($PER_RETIREDATE){
			$arr_temp = explode("-", substr($PER_RETIREDATE, 0, 10));
			$PER_RETIREDATE = ($arr_temp[0] + 543) ."-". $arr_temp[1] ."-". $arr_temp[2];
		} // end if

		$POS_SALARY = trim($data[POS_SALARY]);
		$POS_MGTSALARY = trim($data[POS_MGTSALARY]);
		$POS_DATE = trim($data[POS_DATE]);
		if($POS_DATE){
			$arr_temp = explode("-", substr($POS_DATE, 0, 10));
			$POS_DATE = ($arr_temp[0] + 543) ."-". $arr_temp[1] ."-". $arr_temp[2];
		} // end if

		$POS_GET_DATE = trim($data[POS_GET_DATE]);
		if($POS_GET_DATE){
			$arr_temp = explode("-", substr($POS_GET_DATE, 0, 10));
			$POS_GET_DATE = ($arr_temp[0] + 543) ."-". $arr_temp[1] ."-". $arr_temp[2];
		} // end if

		$POS_CHANGE_DATE = trim($data[POS_CHANGE_DATE]);
		if($POS_CHANGE_DATE){
			$arr_temp = explode("-", substr($POS_CHANGE_DATE, 0, 10));
			$POS_CHANGE_DATE = ($arr_temp[0] + 543) ."-". $arr_temp[1] ."-". $arr_temp[2];
		} // end if

		$PM_CODE = trim($data[PM_CODE]);
		$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$PM_CODE' ";
		$db_dpis->send_cmd($cmd);
		$data_dpis = $db_dpis->get_array();
		$PM_NAME = trim($data_dpis[PM_NAME]);

		$CL_NAME = trim($data[CL_NAME]);
		$CL_CODE = $CL_NAME;

		$SKILL_CODE = trim($data[SKILL_CODE]);
		$cmd = " select SKILL_NAME from PER_SKILL where trim(SKILL_CODE)='$SKILL_CODE' ";
		$db_dpis->send_cmd($cmd);
		$data_dpis = $db_dpis->get_array();
		$SKILL_NAME = trim($data_dpis[SKILL_NAME]);
		
		$ORG_ID = trim($data[ORG_ID]);
		$cmd = " select ORG_NAME from PER_ORG where OL_CODE='03' and ORG_ID=$ORG_ID ";
		$db_dpis->send_cmd($cmd);
		$data_dpis = $db_dpis->get_array();
		$ORG_NAME = trim($data_dpis[ORG_NAME]);
		
		$ORG_ID_1 = trim($data[ORG_ID_1]);
		$cmd = " select ORG_NAME from PER_ORG where OL_CODE='04' and ORG_ID=$ORG_ID_1 ";
		$db_dpis->send_cmd($cmd);
		$data_dpis = $db_dpis->get_array();
		$ORG_NAME_1 = trim($data_dpis[ORG_NAME]);

		$ORG_ID_2 = trim($data[ORG_ID_2]); 
		$cmd = " select ORG_NAME from PER_ORG where OL_CODE='05' and ORG_ID=$ORG_ID_2 ";
		$db_dpis->send_cmd($cmd);
		$data_dpis = $db_dpis->get_array();
		$ORG_NAME_2 = trim($data_dpis[ORG_NAME]);
		
		$EN_CODE = trim($data[EN_CODE]);
		$cmd = " select EN_NAME from PER_EDUCNAME where trim(EN_CODE)='$EN_CODE' ";
		$db_dpis->send_cmd($cmd);
		$data_dpis = $db_dpis->get_array();
		$EN_NAME = trim($data_dpis[EN_NAME]);

		$EM_CODE = trim($data[EM_CODE]);
		$cmd = " select EM_NAME from PER_EDUCMAJOR where trim(EM_CODE)='$EM_CODE' ";
		$db_dpis->send_cmd($cmd);
		$data_dpis = $db_dpis->get_array();
		$EM_NAME = trim($data_dpis[EM_NAME]);

		$PV_CODE = trim($data[PV_CODE]);
		$cmd = " select PV_NAME from PER_PROVINCE where trim(PV_CODE)='$PV_CODE' ";
		$db_dpis->send_cmd($cmd);
		$data_dpis = $db_dpis->get_array();
		$PV_NAME = trim($data_dpis[PV_NAME]);

		$CT_CODE = trim($data[CT_CODE]);
		$cmd = " select CT_NAME from PER_COUNTRY where trim(CT_CODE)='$CT_CODE' ";
		$db_dpis->send_cmd($cmd);
		$data_dpis = $db_dpis->get_array();
		$CT_NAME = trim($data_dpis[CT_NAME]);
		
		$ABILITY = trim($data[ABILITY]);
	} // end if
?>