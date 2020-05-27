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
		$cmd = " delete from PER_MAP_POSITION ";
		$db->send_cmd($cmd);
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูลทั้งหมด");
	} // end if
	
	if($command == "DELETE" && $POS_ID){
		$cmd = " select POS_NO, LEVEL_NO from PER_MAP_POSITION where POS_ID=$POS_ID ";
		$db->send_cmd($cmd);
		$data = $db->get_array();
		$POS_NO = $data[POS_NO];
		$LEVEL_NO = $data[LEVEL_NO];
		
		$cmd = " delete from PER_MAP_POSITION where POS_ID=$POS_ID ";
		$db->send_cmd($cmd);
//		$db->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [$POS_ID : $POS_NO : $LEVEL_NO]");
	} // end if
	
	if($command == "LOADDPIS"){
		ini_set("max_execution_time", 1800);

		$cmd = " delete from PER_MAP_POSITION ";
		$db->send_cmd($cmd);
		
		if($DPISDB=="odbc"){		
			$cmd = " select 		a.POS_ID, a.POS_NO, a.ORG_ID, a.OT_CODE, a.ORG_ID_1, a.ORG_ID_2, a.PM_CODE, a.PL_CODE, a.CL_NAME,
												a.POS_SALARY, a.POS_MGTSALARY, a.SKILL_CODE, a.PT_CODE, 
												a.POS_DATE, a.POS_GET_DATE, a.POS_CHANGE_DATE, a.POS_STATUS,
												b.PER_ID, b.LEVEL_NO as PERSON_LEVEL_NO, b.PER_STATUS
							 from 			PER_POSITION a
							 					left join PER_PERSONAL b on (a.POS_ID = b.POS_ID)
							 where		a.POS_STATUS=1
							 order by 	CInt(a.POS_NO) ";
		}elseif($DPISDB=="oci8"){
			$cmd = " select 		a.POS_ID, a.POS_NO, a.ORG_ID, a.OT_CODE, a.ORG_ID_1, a.ORG_ID_2, a.PM_CODE, a.PL_CODE, a.CL_NAME,
												a.POS_SALARY, a.POS_MGTSALARY, a.SKILL_CODE, a.PT_CODE, 
												a.POS_DATE, a.POS_GET_DATE, a.POS_CHANGE_DATE, a.POS_STATUS,
												b.PER_ID, b.LEVEL_NO as PERSON_LEVEL_NO, b.PER_STATUS
							 from 			PER_POSITION a, PER_PERSONAL b							 
							 where		a.POS_ID=b.POS_ID(+) and a.POS_STATUS=1
							 order by 	TO_NUMBER(a.POS_NO) ";
		} // end if

		$count_all = $db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		while($data_dpis = $db_dpis->get_array()){
			$POS_ID = $data_dpis[POS_ID];
			$POS_NO = trim($data_dpis[POS_NO]);

			$ORG_ID = trim($data_dpis[ORG_ID]);
			$OT_CODE = trim($data_dpis[OT_CODE]);
			$ORG_ID_1 = trim($data_dpis[ORG_ID_1]);
			$ORG_ID_2 = trim($data_dpis[ORG_ID_2]);

			$POS_SALARY = $data_dpis[POS_SALARY];
			$POS_MGTSALARY = $data_dpis[POS_MGTSALARY];
			$POS_DATE = trim($data_dpis[POS_DATE]);
			$POS_GET_DATE = trim($data_dpis[POS_GET_DATE]);
			$POS_CHANGE_DATE = trim($data_dpis[POS_CHANGE_DATE]);
			
			$PM_CODE = trim($data_dpis[PM_CODE]);
			$CL_NAME = $data_dpis[CL_NAME];
			$SKILL_CODE = trim($data_dpis[SKILL_CODE]);
			$PL_CODE = trim($data_dpis[PL_CODE]);
			$PT_CODE = trim($data_dpis[PT_CODE]);
			$LEVEL_NO = "";
						
			if($CL_NAME){
				$cmd = " select LEVEL_NO from PER_MAP_CO_LEVEL where CL_NAME='$CL_NAME' ";
				$db->send_cmd($cmd);
				$data = $db->get_array();
				$LEVEL_NO = trim($data[LEVEL_NO]);
			} // end if
			
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

			$PER_ID = trim($data_dpis[PER_ID]);
			$PERSON_LEVEL_NO = trim($data_dpis[PERSON_LEVEL_NO]);
			$PER_STATUS = trim($data_dpis[PER_STATUS]);
			
			if($PER_ID && $PER_STATUS==1){
				// มีบุคคลอยู่ในตำแหน่ง				
				$cmd = " select PT_CODE_N from PER_MAP_TYPE where trim(LEVEL_NO)='$PERSON_LEVEL_NO' and trim(PT_CODE)='$PT_CODE' ";
				$db->send_cmd($cmd);
				$data = $db->get_array();
				$PT_CODE_N = trim($data[PT_CODE_N]);
			}else{
				$PER_ID = 0;
				// ตำแหน่งว่าง
				$cmd = " select PT_CODE_N from PER_MAP_TYPE where trim(LEVEL_NO)='$LEVEL_NO' and trim(PT_CODE)='$PT_CODE' ";
				$db->send_cmd($cmd);
				$data = $db->get_array();
				$PT_CODE_N = trim($data[PT_CODE_N]);
			} // end if
									
			$cmd = " insert into PER_MAP_POSITION 
								(	POS_ID, POS_NO, PT_CODE_N, OT_CODE, ORG_ID, ORG_ID_1, ORG_ID_2, 
									PM_CODE, CL_NAME, LEVEL_NO, PL_CODE, PT_CODE,
									POS_SALARY, POS_MGTSALARY, SKILL_CODE,
									POS_DATE, POS_GET_DATE, POS_CHANGE_DATE, POS_STATUS,
									PER_ID, PERSON_LEVEL_NO,
									UPDATE_USER, UPDATE_DATE, INSERT_TYPE )
							 values
							 	(	$POS_ID, '$POS_NO', '$PT_CODE_N', '$OT_CODE', '$ORG_ID', '$ORG_ID_1', '$ORG_ID_2', 
									'$PM_CODE', '$CL_NAME', '$LEVEL_NO', '$PL_CODE', '$PT_CODE',
									'$POS_SALARY', '$POS_MGTSALARY', '$SKILL_CODE',
									'$POS_DATE', '$POS_GET_DATE', '$POS_CHANGE_DATE', 1,
									'$PER_ID', '$PERSON_LEVEL_NO',
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
		
		$cmd = " select			POS_ID, CL_NAME, PL_CODE, PT_CODE, PER_ID, PERSON_LEVEL_NO
						 from			PER_MAP_POSITION
						 order by		CONVERT(POS_NO, UNSIGNED)
					  ";
		$count_all = $db->send_cmd($cmd);
		while($data = $db->get_array()){
			$POS_ID = $data[POS_ID];
			$CL_NAME = trim($data[CL_NAME]);
			$PL_CODE = trim($data[PL_CODE]);
			$PT_CODE = trim($data[PT_CODE]);
			$LEVEL_NO = "";

			if($CL_NAME){
				$cmd = " select LEVEL_NO from PER_MAP_CO_LEVEL where trim(CL_NAME)='$CL_NAME' ";
				$db2->send_cmd($cmd);
				$data2 = $db2->get_array();
				$LEVEL_NO = trim($data2[LEVEL_NO]);
			} // end if

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

			$PER_ID = trim($data[PER_ID]);
			$PERSON_LEVEL_NO = trim($data[PERSON_LEVEL_NO]);
			
			if($PER_ID){
				// มีบุคคลอยู่ในตำแหน่ง				
				$cmd = " select PT_CODE_N from PER_MAP_TYPE where trim(LEVEL_NO)='$PERSON_LEVEL_NO' and trim(PT_CODE)='$PT_CODE' ";
				$db2->send_cmd($cmd);
				$data2 = $db2->get_array();
				$PT_CODE_N = trim($data2[PT_CODE_N]);
			}else{
				// ตำแหน่งว่าง
				$cmd = " select PT_CODE_N from PER_MAP_TYPE where trim(LEVEL_NO)='$LEVEL_NO' and trim(PT_CODE)='$PT_CODE' ";
				$db2->send_cmd($cmd);
				$data2 = $db2->get_array();
				$PT_CODE_N = trim($data2[PT_CODE_N]);
			} // end if
			
			$cmd = " update 	PER_MAP_POSITION set
											LEVEL_NO = '$LEVEL_NO',
											PT_CODE_N = '$PT_CODE_N'
							 where	POS_ID = $POS_ID ";
			$db2->send_cmd($cmd);
//			$db2->show_error();
		} // end while

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เทียบเคียงข้อมูลตามสูตร จำนวน $count_all รายการ");
		ini_set("max_execution_time", 30);
	} // end if
	
?>