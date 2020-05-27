<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");	
	include("php_scripts/load_per_control.php");	

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	switch($CTRL_TYPE){
		case 2 :
			$PV_CODE = $PROVINCE_CODE;
			$PV_NAME = $PROVINCE_NAME;
			break;
		case 3 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			break;
		case 4 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			break;
	} // end switch case

	switch($SESS_USERGROUP_LEVEL){
		case 2 :
			$PV_CODE = $PROVINCE_CODE;
			$PV_NAME = $PROVINCE_NAME;
			break;
		case 3 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			break;
		case 4 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			break;
		case 5 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			$search_org_id = $ORG_ID;
			$search_org_name = $ORG_NAME;
			break;
	} // end switch case

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if(!isset($search_kf_year)){
		if(date("Y-m-d") <= date("Y")."-10-01") $search_kf_year = date("Y") + 543;
		else $search_kf_year = (date("Y") + 543) + 1;
	} // end if
	
	$UPDATE_DATE = date("Y-m-d H:i:s");	

	if(!$WEIGHT_OTHER) $WEIGHT_OTHER = "NULL";

	if($command == "UPDATE_SCORE"){
		foreach($TOTAL_SCORE as $update_id => $update_point) {
			$cmd = " update PER_KPI_FORM set 
							TOTAL_SCORE = '$update_point',
							SALARY_FLAG = '".$_salary_flag[$update_id]."',
							UPDATE_USER=$SESS_USERID, 
							UPDATE_DATE='$UPDATE_DATE' 
						 where KF_ID=$update_id ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขคะแนน [".$KF_ID." : ".trim($KF_YEAR)." : ".$KF_CYCLE." : ".$PER_NAME."]");
		}
	}
	
	if($command == "DELETE" && trim($KF_ID)){
		$cmd = " select 	KF_END_DATE, KF_CYCLE, PER_ID
				  		 from 		PER_KPI_FORM 
						 where 	KF_ID = $KF_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$KF_END_DATE = substr($data[KF_END_DATE], 0, 10);
		$KF_YEAR = substr($KF_END_DATE, 0, 4);
		$KF_CYCLE = $data[KF_CYCLE];
		$PER_ID = $data[PER_ID];
			
		$cmd = " select PN_CODE, PER_NAME, PER_SURNAME from PER_PERSONAL where PER_ID=$PER_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PN_CODE = $data[PN_CODE];
		$PER_NAME = $data[PER_NAME];
		$PER_SURNAME = $data[PER_SURNAME];
			
		$cmd = " select PN_NAME from PER_PRENAME where PN_CODE='$PN_CODE' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PN_NAME = $data[PN_NAME];
			
		$PER_FULLNAME = $PN_NAME . $PER_NAME . " " . $PER_SURNAME;
		
		$cmd = " delete from PER_PERFORMANCE_GOALS where KF_ID=$KF_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		
		$cmd = " delete from PER_KPI_COMPETENCE where KF_ID=$KF_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " delete from PER_IPIP where KF_ID=$KF_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " delete from PER_KPI_FORM where KF_ID=$KF_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [". $KF_ID ." : ".$KF_YEAR." ".$KF_CYCLE." ".$PER_FULLNAME."]");
	}
	
	if($command == 'COPY_ALL' || $command == 'COPY_TEST') {
		if($search_kf_cycle == 1){
			$KF_START_DATE = ($search_kf_year-544) . "-10-01";
			$KF_END_DATE = ($search_kf_year-543) . "-03-31";
		}elseif($search_kf_cycle == 2){
			$KF_START_DATE = ($search_kf_year-543) . "-04-01";
			$KF_END_DATE = ($search_kf_year-543) . "-09-30";
		} // end if
	
		if($search_org_id) $search_con = "b.ORG_ID = $search_org_id ";
		elseif($search_department_id) $search_con = "a.DEPARTMENT_ID = $search_department_id ";

		$cmd = " select max(KF_ID) as MAX_ID from PER_KPI_FORM ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$KF_ID = $data[MAX_ID] + 1;

		$cmd = " select max(KC_ID) as max_id from PER_KPI_COMPETENCE ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$KC_ID = $data[max_id] + 1;
	
		if($search_per_type==1) 
			if ($SESS_DEPARTMENT_NAME=="กรมการปกครอง")
				$cmd = "	select a.PER_ID, a.PER_CARDNO, a.PER_ID_REF, a.PER_ID_ASS_REF, a.POS_ID, b.ORG_ID, b.ORG_ID_1, 
									a.ORG_ID as ORG_ID_ASS 
									from PER_PERSONAL a, PER_POSITION b where a.PAY_ID = b.POS_ID and PER_STATUS = 1 and $search_con ";
			else
				$cmd = "	select a.PER_ID, a.PER_CARDNO, a.PER_ID_REF, a.PER_ID_ASS_REF, a.POS_ID, b.ORG_ID, b.ORG_ID_1, 
									a.ORG_ID as ORG_ID_ASS 
									from PER_PERSONAL a, PER_POSITION b where a.POS_ID = b.POS_ID and PER_STATUS = 1 and $search_con ";
		elseif($search_per_type==2) 
			$cmd = "	select a.PER_ID, a.PER_CARDNO, a.PER_ID_REF, a.PER_ID_ASS_REF, a.POEM_ID as POS_ID, b.ORG_ID, 
								b.ORG_ID_1, a.ORG_ID as ORG_ID_ASS 
								from PER_PERSONAL a, PER_POS_EMP b where a.POEM_ID = b.POEM_ID and PER_STATUS = 1 and $search_con ";
		elseif($search_per_type==3) 
			$cmd = "	select a.PER_ID, a.PER_CARDNO, a.PER_ID_REF, a.PER_ID_ASS_REF, a.POEMS_ID as POS_ID, b.ORG_ID, 
								b.ORG_ID_1, a.ORG_ID as ORG_ID_ASS 
								from PER_PERSONAL a, PER_POS_EMPSER b where a.POEMS_ID = b.POEMS_ID and PER_STATUS = 1 and 
								$search_con ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		while($data = $db_dpis->get_array()){
			$PER_ID = $data[PER_ID];
			$PER_CARDNO = $data[PER_CARDNO];
			$PER_ID_REF = $data[PER_ID_REF];
			$PER_ID_ASS_REF = $data[PER_ID_ASS_REF];
			if (!$PER_ID_REF) $PER_ID_REF = "NULL";
			$POS_ID = $data[POS_ID];
			$ORG_ID = $data[ORG_ID];
			$ORG_ID_1 = $data[ORG_ID_1];
			$ORG_ID_ASS = $data[ORG_ID_ASS];
			if (!$ORG_ID_1) $ORG_ID_1 = "NULL";
			if (!$ORG_ID_ASS) $ORG_ID_ASS = "NULL";
			$TOTAL_SCORE = rand(60,100);
			$cmd = " SELECT KF_ID, TOTAL_SCORE, KPI_FLAG FROM PER_KPI_FORM 
							  WHERE PER_ID = $PER_ID AND KF_CYCLE = $search_kf_cycle AND 
							  KF_START_DATE = '$KF_START_DATE' AND KF_END_DATE = '$KF_END_DATE' ";
			$count_data = $db_dpis1->send_cmd($cmd);
			//$db_dpis1->show_error();

			if ($count_data) {
				$data1 = $db_dpis1->get_array();
				$UPD_KF_ID = $data1[KF_ID];
				$old_TOTAL_SCORE = $data1[TOTAL_SCORE];
				$KPI_FLAG = $data1[KPI_FLAG];
				if ($old_TOTAL_SCORE==0 && $KPI_FLAG=="Y") {
					$cmd = " UPDATE PER_KPI_FORM SET TOTAL_SCORE = $TOTAL_SCORE WHERE KF_ID = $UPD_KF_ID ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
				}
			} else {
				if ($command == 'COPY_ALL') $TOTAL_SCORE = 0;
				$cmd = " insert into PER_KPI_FORM (KF_ID, PER_ID, PER_CARDNO, KF_CYCLE, KF_START_DATE, KF_END_DATE, 
								PER_ID_REVIEW, PER_ID_REVIEW0, PER_ID_REVIEW1, PER_ID_REVIEW2, DEPARTMENT_ID, UPDATE_USER, 
								UPDATE_DATE, ORG_ID, TOTAL_SCORE, SALARY_FLAG, ORG_ID_SALARY, KPI_FLAG, ORG_ID_KPI, 
								ORG_ID_1_SALARY, ORG_ID_ASS, PERFORMANCE_WEIGHT, COMPETENCE_WEIGHT, OTHER_WEIGHT) 
								values ($KF_ID, $PER_ID, '$PER_CARDNO', $search_kf_cycle, '$KF_START_DATE', '$KF_END_DATE', 	
								$PER_ID_REF, 	$PER_ID_REF, $PER_ID_REF, $PER_ID_REF, $search_department_id, $SESS_USERID, 
								'$UPDATE_DATE', $ORG_ID, $TOTAL_SCORE, 'Y', $ORG_ID, 'Y', $ORG_ID, $ORG_ID_1, $ORG_ID_ASS, 
								$WEIGHT_KPI, $WEIGHT_COMPETENCE, $WEIGHT_OTHER) ";
				$db_dpis1->send_cmd($cmd);
//				$db_dpis1->show_error();
				insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [".$KF_ID." : ".trim($search_kf_year)." : ".$search_kf_cycle." : ".$PER_CARDNO."]");

				// ==================== insert competence from kpi_position_competence  ==================== //
				if($search_per_type==1) {
					$cmd = " select		CP_CODE, PC_TARGET_LEVEL
									 from		PER_POSITION_COMPETENCE
									 where	POS_ID=$POS_ID and PC_TARGET_LEVEL > 0
									 order by CP_CODE ";
					$db_dpis1->send_cmd($cmd);
//					$db_dpis1->show_error();
					while($data1 = $db_dpis1->get_array()){
						$CP_CODE = $data1[CP_CODE];
						$PC_TARGET_LEVEL = $data1[PC_TARGET_LEVEL];
				
						$cmd = " insert into PER_KPI_COMPETENCE (KC_ID, KF_ID, CP_CODE, PC_TARGET_LEVEL, UPDATE_USER, 
											UPDATE_DATE) 
											values ($KC_ID, $KF_ID, '$CP_CODE', $PC_TARGET_LEVEL, $SESS_USERID, '$UPDATE_DATE') ";
						$db_dpis2->send_cmd($cmd);
//						$db_dpis2->show_error();
						$KC_ID++;
					} // end while
				} // endif($search_per_type==1) 
				$KF_ID++;
				// ==================== insert competence from kpi_position_competence  ==================== //
			}
		}
	}

	if($command == 'DELETE_TEST') {
		if($search_kf_cycle == 1){
			$KF_START_DATE = ($search_kf_year-544) . "-10-01";
			$KF_END_DATE = ($search_kf_year-543) . "-03-31";
		}elseif($search_kf_cycle == 2){
			$KF_START_DATE = ($search_kf_year-543) . "-04-01";
			$KF_END_DATE = ($search_kf_year-543) . "-09-30";
		} // end if
	
		if($search_org_id) $search_con = "b.ORG_ID = $search_org_id ";
		elseif($search_department_id) $search_con = "a.DEPARTMENT_ID = $search_department_id ";

		if($search_per_type==1) 
			$cmd = "	select a.PER_ID, a.PER_CARDNO, a.PER_ID_REF, a.PER_ID_ASS_REF, a.POS_ID, b.ORG_ID, b.ORG_ID_1, 
								a.ORG_ID as ORG_ID_ASS 
								from PER_PERSONAL a, PER_POSITION b where a.POS_ID = b.POS_ID and PER_STATUS = 1 and $search_con ";
		elseif($search_per_type==2) 
			$cmd = "	select a.PER_ID, a.PER_CARDNO, a.PER_ID_REF, a.PER_ID_ASS_REF, a.POEM_ID as POS_ID, b.ORG_ID, 
								b.ORG_ID_1, a.ORG_ID as ORG_ID_ASS 
								from PER_PERSONAL a, PER_POS_EMP b where a.POEM_ID = b.POEM_ID and PER_STATUS = 1 and $search_con ";
		elseif($search_per_type==3) 
			$cmd = "	select a.PER_ID, a.PER_CARDNO, a.PER_ID_REF, a.PER_ID_ASS_REF, a.POEMS_ID as POS_ID, b.ORG_ID, 
								b.ORG_ID_1, a.ORG_ID as ORG_ID_ASS 
								from PER_PERSONAL a, PER_POS_EMPSER b where a.POEMS_ID = b.POEMS_ID and PER_STATUS = 1 and 
								$search_con ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		while($data = $db_dpis->get_array()){
			$PER_ID = $data[PER_ID];
			$cmd = " UPDATE PER_KPI_FORM SET TOTAL_SCORE = 0 WHERE PER_ID = $PER_ID AND KF_CYCLE = $search_kf_cycle AND 
							  KF_START_DATE = '$KF_START_DATE' AND KF_END_DATE = '$KF_END_DATE' ";
			$db_dpis2->send_cmd($cmd);
			$data = $db_dpis2->get_array();
		}
	}

?>