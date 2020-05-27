<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");
	
//	$db_dpis_n = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	// ==== use for testing phase =====
	$db_dpis_n = new connect_db($db_host, $db_name, $db_user, $db_pwd);
	// ==========================

	$UPDATE_DATE = date("Y-m-d H:i:s");
	
	if($command=="ADD" || $command=="UPDATE"){
		$LEVEL_NO = str_pad($LEVEL_NO, 2, "0", STR_PAD_LEFT);
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

	if($command=="ADD" && $POS_NO){
		$cmd = " select POS_ID, POS_NO from PER_MAP_POSITION where trim(POS_NO)='". trim($POS_NO) ."' ";
		$count_duplicate = $db->send_cmd($cmd);
		if($count_duplicate <= 0){
			$cmd = " select max(POS_ID) from PER_MAP_POSITION ";
			$db->send_cmd($cmd);
			$data = $db->get_data();
			$POS_ID = $data[0] + 1;
			
			$cmd = " insert into PER_MAP_POSITION 
								(	POS_ID, POS_NO, PT_CODE_N, OT_CODE, ORG_ID, ORG_ID_1, ORG_ID_2,
									PM_CODE, PL_CODE, CL_NAME, LEVEL_NO, SKILL_CODE, PT_CODE,
									POS_SALARY, POS_MGTSALARY, POS_DATE, POS_GET_DATE, POS_CHANGE_DATE, POS_STATUS,
									PER_ID, PERSON_LEVEL_NO,
									UPDATE_USER, UPDATE_DATE, INSERT_TYPE )
							 values
								(	$POS_ID, '$POS_NO', '$PT_CODE_N', '$OT_CODE', '$ORG_ID', '$ORG_ID_1', '$ORG_ID_2',
									'$PM_CODE', '$PL_CODE', '$CL_NAME', '$LEVEL_NO', '$SKILL_CODE', '$PT_CODE',
									'$POS_SALARY', '$POS_MGTSALARY', '$POS_DATE', '$POS_GET_DATE', '$POS_CHANGE_DATE', 1,
									'', '',
									$SESS_USERID, '$UPDATE_DATE', 'M' )
							  ";
			$db->send_cmd($cmd);
//			$db->show_error();
			
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > เพิ่มข้อมูลตำแหน่ง [ $POS_ID : $POS_NO ]");
		}else{
			$data = $db->get_array();
			$err_text = "เลขที่ตำแหน่งซ้ำ [".$data[POS_NO]."]";
		} // end if
	} // end if

	if($command=="UPDATE" && $POS_ID){
		$cmd = " update PER_MAP_POSITION  set
							POS_NO = '$POS_NO',
							OT_CODE = '$OT_CODE', 
							PL_CODE = '$PL_CODE', 
							PM_CODE = '$PM_CODE', 
							CL_NAME = '$CL_NAME', 
							SKILL_CODE = '$SKILL_CODE', 
							PT_CODE = '$PT_CODE', 
							PT_CODE_N = '$PT_CODE_N', 
							LEVEL_NO = '$LEVEL_NO', 
							POS_CHANGE_DATE = '$POS_CHANGE_DATE', 
							POS_DATE = '$POS_DATE', 
							POS_GET_DATE = '$POS_GET_DATE', 
							POS_SALARY = '$POS_SALARY', 
							POS_MGTSALARY = '$POS_MGTSALARY', 
							ORG_ID = '$ORG_ID', ORG_ID_1 = '$ORG_ID_1', ORG_ID_2 = '$ORG_ID_2', 
							UPDATE_USER = $SESS_USERID, UPDATE_DATE = '$UPDATE_DATE'
						 where POS_ID=$POS_ID
					  ";
		$db->send_cmd($cmd);
//		$db->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > แก้ไขข้อมูลตำแหน่ง [ $POS_ID : $POS_NO ]");
	} // end if
	
	if($command=="DELETE" && $POS_ID){
		$cmd = " select	 POS_NO from PER_MAP_POSITION where POS_ID=$POS_ID ";
		$db->send_cmd($cmd);
		$data = $db->get_array();
		$POS_NO = $data[POS_NO];

		$cmd = " delete from PER_MAP_POSITION where POS_ID=$POS_ID ";	
		$db->send_cmd($cmd);
			
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > ลบข้อมูลตำแหน่ง [ $POS_ID : $POS_NO ]");		
	} // end if

	if($POS_ID){
		$cmd = "	select		POS_NO, OT_CODE, PT_CODE_N, ORG_ID, ORG_ID_1, ORG_ID_2,
											PM_CODE, PL_CODE, CL_NAME, LEVEL_NO, SKILL_CODE, PT_CODE,
											POS_SALARY, POS_MGTSALARY, POS_DATE, POS_GET_DATE, POS_CHANGE_DATE,
											PER_ID, PERSON_LEVEL_NO
							from		PER_MAP_POSITION
							where		POS_ID=$POS_ID
					   ";
		$db->send_cmd($cmd);
//		$db->show_error();
		$data = $db->get_array();
		$POS_NO = trim($data[POS_NO]);
		
		$OT_CODE = trim($data[OT_CODE]);
		$cmd = " select OT_NAME from PER_OFF_TYPE where trim(OT_CODE)='$OT_CODE' ";
		$db_dpis->send_cmd($cmd);
		$data_dpis = $db_dpis->get_array();
		$OT_NAME = trim($data_dpis[OT_NAME]);

		$PT_CODE_N = trim($data[PT_CODE_N]);
		$cmd = " select PT_NAME_N from PER_TYPE_N where trim(PT_CODE_N)='$PT_CODE_N' ";
		$db_dpis_n->send_cmd($cmd);
		$data_dpis = $db_dpis_n->get_array();
		$PT_NAME_N = trim($data_dpis[PT_NAME_N]);

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

		$PM_CODE = trim($data[PM_CODE]);
		$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$PM_CODE' ";
		$db_dpis->send_cmd($cmd);
		$data_dpis = $db_dpis->get_array();
		$PM_NAME = trim($data_dpis[PM_NAME]);

		$PL_CODE = trim($data[PL_CODE]);
		$cmd = " select PL_NAME from PER_LINE where trim(PL_CODE)='$PL_CODE' ";
		$db_dpis->send_cmd($cmd);
		$data_dpis = $db_dpis->get_array();
		$PL_NAME = trim($data_dpis[PL_NAME]);
		
		$CL_NAME = trim($data[CL_NAME]);
		$CL_CODE = $CL_NAME;
		$LEVEL_NO = level_no_format(trim($data[LEVEL_NO]));

		$SKILL_CODE = trim($data[SKILL_CODE]);
		$cmd = " select SKILL_NAME from PER_SKILL where trim(SKILL_CODE)='$SKILL_CODE' ";
		$db_dpis->send_cmd($cmd);
		$data_dpis = $db_dpis->get_array();
		$SKILL_NAME = trim($data_dpis[SKILL_NAME]);

		$PT_CODE = trim($data[PT_CODE]);
		$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE)='$PT_CODE' ";
		$db_dpis->send_cmd($cmd);
		$data_dpis = $db_dpis->get_array();
		$PT_NAME = trim($data_dpis[PT_NAME]);
		
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
		
		$PER_ID = trim($data[PER_ID]);
		$PERSON_LEVEL_NO = level_no_format(trim($data[PERSON_LEVEL_NO]));

		if($PER_ID){
			$cmd = " select 	b.PN_NAME, a.PER_NAME, a.PER_SURNAME 
							 from 		PER_PERSONAL a, PER_PRENAME b 
							 where 	a.PN_CODE=b.PN_CODE and PER_ID=$PER_ID ";
			$db_dpis->send_cmd($cmd);
			$data_dpis = $db_dpis->get_array();
			$FULLNAME = (trim($data_dpis[PN_NAME])?($data_dpis[PN_NAME]." "):"") . $data_dpis[PER_NAME] ." ". $data_dpis[PER_SURNAME];
		} // end if
	} // end if
?>