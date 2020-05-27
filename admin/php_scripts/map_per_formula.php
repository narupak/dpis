<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

//	$db_dpis_n = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	// ==== use for testing phase =====
	$db_dpis_n = new connect_db($db_host, $db_name, $db_user, $db_pwd);
	// ==========================

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$UPDATE_DATE = date("Y-m-d H:i:s");
			
	if($command == "DELETEALL"){
		$cmd = " delete from PER_FORMULA ";
		$db->send_cmd($cmd);
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูลทั้งหมด");
	} // end if
	
	if($command == "DELETE" && $PER_ID){
		$cmd = " select LEVEL_NO from PER_FORMULA where PER_ID=$PER_ID";
		$db->send_cmd($cmd);
		$data = $db->get_array();
		$LEVEL_NO = $data[LEVEL_NO];
		
		$cmd = " delete from PER_FORMULA where PER_ID=$PER_ID";
		$db->send_cmd($cmd);
//		$db->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [$PER_ID : $LEVEL_NO]");
	} // end if
	
	if($command == "LOADDPIS"){
		ini_set("max_execution_time", 1800);

		$cmd = " delete from PER_FORMULA ";
		$db->send_cmd($cmd);
				
		$cmd = " select 		a.PER_ID, a.LEVEL_NO, a.POS_ID, 
											a.PN_CODE, a.PER_NAME, a.PER_SURNAME, a.PER_ENG_NAME, a.PER_ENG_SURNAME, 
											a.OT_CODE, a.PER_CARDNO, a.PER_OFFNO, a.PER_GENDER, a.PER_TYPE, a.PER_STATUS,
											a.PER_BIRTHDATE, a.PER_STARTDATE, a.PER_OCCUPYDATE, a.PER_RETIREDATE, 
											b.POS_NO, b.POS_SALARY, b.POS_MGTSALARY, b.POS_DATE, b.POS_GET_DATE, b.POS_CHANGE_DATE,
											b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2, b.PL_CODE, b.PM_CODE, b.PT_CODE, b.CL_NAME, b.SKILL_CODE
						 from 			PER_PERSONAL a, PER_POSITION b
						 where		a.POS_ID=b.POS_ID and a.PER_TYPE=1 and a.PER_STATUS=1
						 order by 	a.PER_NAME, a.PER_SURNAME ";
		$count_all = $db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		while($data_dpis = $db_dpis->get_array()){
			$PER_ID = $data_dpis[PER_ID];
			$LEVEL_NO = trim($data_dpis[LEVEL_NO]);
			$POS_ID = $data_dpis[POS_ID];
			$POS_NO = trim($data_dpis[POS_NO]);

			$PN_CODE = trim($data_dpis[PN_CODE]);
			$PER_NAME = trim($data_dpis[PER_NAME]);
			$PER_SURNAME = trim($data_dpis[PER_SURNAME]);
			$PER_ENG_NAME = trim($data_dpis[PER_ENG_NAME]);
			$PER_ENG_SURNAME = trim($data_dpis[PER_ENG_SURNAME]);
			$OT_CODE = trim($data_dpis[OT_CODE]);
			$PER_CARDNO = trim($data_dpis[PER_CARDNO]);
			$PER_OFFNO = trim($data_dpis[PER_OFFNO]);
			$PER_GENDER = $data_dpis[PER_GENDER];
			$PER_TYPE = $data_dpis[PER_TYPE];
			$PER_STATUS = $data_dpis[PER_STATUS];

			$PER_BIRTHDATE = trim($data_dpis[PER_BIRTHDATE]);
			$PER_STARTDATE = trim($data_dpis[PER_STARTDATE]);
			$PER_OCCUPYDATE = trim($data_dpis[PER_OCCUPYDATE]);
			$PER_RETIREDATE = trim($data_dpis[PER_RETIREDATE]);
			
			$POS_SALARY = $data_dpis[POS_SALARY];
			$POS_MGTSALARY = $data_dpis[POS_MGTSALARY];
			$POS_DATE = trim($data_dpis[POS_DATE]);
			$POS_GET_DATE = trim($data_dpis[POS_GET_DATE]);
			$POS_CHANGE_DATE = trim($data_dpis[POS_CHANGE_DATE]);

			$ORG_ID = trim($data_dpis[ORG_ID]);
			$ORG_ID_1 = trim($data_dpis[ORG_ID_1]);
			$ORG_ID_2 = trim($data_dpis[ORG_ID_2]);
			$PM_CODE = trim($data_dpis[PM_CODE]);
			$CL_NAME = trim($data_dpis[CL_NAME]);
			$SKILL_CODE = trim($data_dpis[SKILL_CODE]);
			$PL_CODE = trim($data_dpis[PL_CODE]);
			$PT_CODE = trim($data_dpis[PT_CODE]);
			
//			if($PL_CODE && $PL_CODE!="011103" && $PL_CODE!="010903"){
			if($PL_CODE){
				$cmd = " select PL_GROUP from PER_MAP_LINE where trim(PL_CODE)='$PL_CODE' ";
				$db->send_cmd($cmd);
				$data = $db->get_array();
				$PL_GROUP = trim($data[PL_GROUP]);
				if($PT_CODE=="11"){
					if($PL_GROUP == 2) $PT_CODE = "12";
				}else{
					if($PL_GROUP == 1) $PT_CODE = "11";
				}
			} // end if
			
			$cmd = " select PT_CODE_N from PER_MAP_TYPE where trim(LEVEL_NO)='$LEVEL_NO' and trim(PT_CODE)='$PT_CODE' ";
			$db->send_cmd($cmd);
			$data = $db->get_array();
			$PT_CODE_N = trim($data[PT_CODE_N]);
			
			$cmd = " select EN_CODE, EM_CODE from PER_EDUCATE where PER_ID=$PER_ID and EDU_TYPE like '%||2||%' ";
			$db_dpis2->send_cmd($cmd);
			$data = $db_dpis2->get_array();
			$EN_CODE = trim($data[EN_CODE]);
			$EM_CODE = trim($data[EM_CODE]);
			
			$cmd = " select PV_CODE, CT_CODE from PER_ORG where ORG_ID=$ORG_ID ";
			$db_dpis2->send_cmd($cmd);
			$data = $db_dpis2->get_array();
			$PV_CODE = trim($data[PV_CODE]);
			$CT_CODE = trim($data[CT_CODE]);	
			
			$ABILITY = "";
			$cmd = " select ABI_DESC from PER_ABILITY where PER_ID=$PER_ID ";
			$db_dpis2->send_cmd($cmd);
			while($data = $db_dpis2->get_array()){
				if($ABILITY) $ABILITY .= ", ";
				$ABILITY .= trim($data[ABI_DESC]);
			} // end while
						
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
									$SESS_USERID, '$UPDATE_DATE', 'A' )
						  ";
			$db->send_cmd($cmd);
//			$db->show_error();
		} // end while
	
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > นำเข้าข้อมูลจาก DPIS 3.0 จำนวน $count_all รายการ");
		ini_set("max_execution_time", 30);
	} // end if	

	if($command == "RECALCULATE"){
		$db2 = new connect_db($db_host, $db_name, $db_user, $db_pwd);
		ini_set("max_execution_time", 1800);
		
		$cmd = " select			PER_ID, PL_CODE, PT_CODE, LEVEL_NO
						 from			PER_FORMULA
						 order by		PER_NAME, PER_SURNAME
					  ";
		$count_all = $db->send_cmd($cmd);
		while($data = $db->get_array()){
			$PER_ID = trim($data[PER_ID]);
			$PL_CODE = trim($data[PL_CODE]);
			$PT_CODE = trim($data[PT_CODE]);
			$LEVEL_NO = trim($data[LEVEL_NO]);

			if($PL_CODE){
				$cmd = " select PL_GROUP from PER_MAP_LINE where trim(PL_CODE)='$PL_CODE' ";
				$db2->send_cmd($cmd);
				$data2 = $db2->get_array();
				$PL_GROUP = trim($data2[PL_GROUP]);
				if($PT_CODE=="11"){
					if($PL_GROUP == 2) $PT_CODE = "12";
				}else{
					if($PL_GROUP == 1) $PT_CODE = "11";
				}
			} // end if
			
			$cmd = " select PT_CODE_N from PER_MAP_TYPE where trim(LEVEL_NO)='$LEVEL_NO' and trim(PT_CODE)='$PT_CODE' ";
			$db2->send_cmd($cmd);
			$data2 = $db2->get_array();
			$PT_CODE_N = trim($data2[PT_CODE_N]);
			
			$cmd = " update 	PER_FORMULA set
											PT_CODE_N = '$PT_CODE_N'
							 where	PER_ID = $PER_ID ";
			$db2->send_cmd($cmd);
//			$db2->show_error();
		} // end while

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เทียบเคียงข้อมูลตามสูตร จำนวน $count_all รายการ");
		ini_set("max_execution_time", 30);
	} // end if

?>