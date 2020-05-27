<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");	
	include("php_scripts/load_per_control.php");	

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	$cmd = " select code, name_th, group_level, pv_code, org_id from user_group where id=$SESS_USERGROUP ";
	$db->send_cmd($cmd);
//	$db->show_error();
	$data = $db->get_array();
	$SESS_GROUPCODE = $data[code];
	
	if(($SESS_GROUPCODE == "BUREAU" || substr($SESS_GROUPCODE, 0, 7) == "BUREAU_" ) || $SESS_USERGROUP==3){
		$cmd = " select 	PN_CODE, PER_NAME, PER_SURNAME, PER_CARDNO, LEVEL_NO, PER_SALARY, PER_TYPE, POS_ID 
				   from 		PER_PERSONAL 
				   where 	PER_ID=$SESS_PER_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
//		$search_per_name = trim($data[PER_NAME]);
//		$search_per_surname = trim($data[PER_SURNAME]);
		$PER_ID = $SESS_PER_ID;
		$PN_CODE = trim($data[PN_CODE]);
		$PER_NAME = trim($data[PER_NAME]) ." ". trim($data[PER_SURNAME]);;
		$PER_CARDNO = trim($data[PER_CARDNO]);
		$LEVEL_NO = trim($data[LEVEL_NO]);
		$PER_SALARY = $data[PER_SALARY];
		$PER_TYPE = $data[PER_TYPE];
		
		$POS_ID = $data[POS_ID];
		$cmd = " select 	a.ORG_ID, d.ORG_NAME, b.PL_NAME, a.PT_CODE
					   from 		PER_POSITION a, PER_LINE b, PER_ORG d
					   where 	a.POS_ID=$POS_ID and a.PL_CODE=b.PL_CODE and a.ORG_ID=d.ORG_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$ORG_NAME = trim($data[ORG_NAME]);
		$PL_NAME = trim($data[PL_NAME]);
		$PT_CODE = trim($data[PT_CODE]);
		$cmd = "	select PT_NAME from PER_TYPE where PT_CODE='$PT_CODE'";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$PT_NAME = trim($data2[PT_NAME]);
		$PL_NAME = trim($PL_NAME)?($PL_NAME ." ". level_no_format($LEVEL_NO) . ((trim($PT_NAME) != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"")):"ระดับ ".level_no_format($LEVEL_NO);
		
		$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='$PN_CODE' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PER_NAME = trim($data[PN_NAME]).$PER_NAME;

		$cmd = " select LEVEL_NAME from PER_LEVEL where trim(LEVEL_NO)='$LEVEL_NO' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$LEVEL_NAME = $data[LEVEL_NAME];
	} // end if

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
	
	if(!isset($search_ep_year)){
		if(date("Y-m-d") <= date("Y")."-10-01") $search_ep_year = date("Y") + 543;
		else $search_ep_year = (date("Y") + 543) + 1;
	} // end if
	
	$UPDATE_DATE = date("Y-m-d H:i:s");	

	if($command=="ADD" || $command=="UPDATE"){
		$START_DATE =  save_date($START_DATE);
		$END_DATE =  save_date($END_DATE);
	} // end if
	
	if($command == "ADD" && trim($EP_YEAR) && trim($PER_ID) && trim($EAF_ID)){
		if($DPISDB == "odbc"){
			$cmd = " select 	EP_YEAR, PER_ID, EAF_ID
					  		 from 		EAF_PERSONAL 
							 where 	EP_YEAR='$EP_YEAR' and PER_ID = $PER_ID and EAF_ID=$EAF_ID ";
		}elseif($DPISDB == "oci8"){
			$cmd = " select 	EP_YEAR, PER_ID, EAF_ID
					  		 from 		EAF_PERSONAL 
							 where 	EP_YEAR='$EP_YEAR' and PER_ID = $PER_ID and EAF_ID=$EAF_ID ";
		} // end if
		$count_duplicate = $db_dpis->send_cmd($cmd);
		if($count_duplicate <= 0){
			$cmd = " select max(EP_ID) as max_id from EAF_PERSONAL ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$EP_ID = $data[max_id] + 1;
			
			$cmd = " insert into EAF_PERSONAL 	(EP_ID, PER_ID, EAF_ID, EP_YEAR, START_DATE, END_DATE, 
							PER_ID_REVIEW, PER_ID_REVIEW1, PER_ID_REVIEW2, UPDATE_USER, UPDATE_DATE) 
							values ($EP_ID, $PER_ID, $EAF_ID, '$EP_YEAR', '$START_DATE', '$END_DATE', 
							'$PER_ID_REVIEW', '$PER_ID_REVIEW1', '$PER_ID_REVIEW2', $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();

			// copy all data which belong to EAF_ID from 
			// EAF_LEARNING_STRUCTURE -> EAF_PERSONAL_STRUCTURE
			// EAF_LEARNING_KNOWLEDGE -> EAF_PERSONAL_KNOWLEDGE
			// with reference key is EP_ID
			
			$cmd = " select 	ELS_ID, MINISTRY_ID, DEPARTMENT_ID, ORG_ID, ORG_ID_1, ORG_ID_2, ELS_LEVEL, ELS_PERIOD, ELS_SEQ_NO
							from		EAF_LEARNING_STRUCTURE
							where		EAF_ID = $EAF_ID
							order by ELS_ID ";
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()){
				$ELS_ID = $data[ELS_ID];
				$EPS_MINISTRY_ID = $data[MINISTRY_ID];
				$EPS_DEPARTMENT_ID = $data[DEPARTMENT_ID];
				$EPS_ORG_ID = $data[ORG_ID];
				$EPS_ORG_ID_1 = $data[ORG_ID_1];
				$EPS_ORG_ID_2 = $data[ORG_ID_2];
				$EPS_LEVEL = $data[ELS_LEVEL];
				$EPS_PERIOD = $data[ELS_PERIOD];
				$EPS_SEQ_NO = $data[ELS_SEQ_NO] + 0;
				
				$cmd = " select max(EPS_ID) as max_id from EAF_PERSONAL_STRUCTURE ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$data2 = array_change_key_case($data2, CASE_LOWER);
				$EPS_ID = $data2[max_id] + 1;
				
				if(!trim($EPS_ORG_ID)) $EPS_ORG_ID = "NULL";
				if(!trim($EPS_ORG_ID_1)) $EPS_ORG_ID_1 = "NULL";
				if(!trim($EPS_ORG_ID_2)) $EPS_ORG_ID_2 = "NULL";
				if(!trim($EPS_PERIOD)) $EPS_PERIOD = "NULL";

				$cmd = " insert into EAF_PERSONAL_STRUCTURE (EPS_ID, EP_ID, MINISTRY_ID, DEPARTMENT_ID, ORG_ID, ORG_ID_1, 
								ORG_ID_2, EPS_SEQ_NO, EPS_LEVEL, EPS_PERIOD, UPDATE_USER, UPDATE_DATE) 
								values ($EPS_ID, $EP_ID, $EPS_MINISTRY_ID, $EPS_DEPARTMENT_ID, $EPS_ORG_ID, $EPS_ORG_ID_1, 
								$EPS_ORG_ID_2, $EPS_SEQ_NO, $EPS_LEVEL, $EPS_PERIOD, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis2->send_cmd($cmd);
				
				$cmd = " select		ELK_NAME, ELK_COACH, ELK_BEHAVIOR, ELK_TRAIN, ELK_JOB
								from		EAF_LEARNING_KNOWLEDGE
								where		EAF_ID=$EAF_ID and ELS_ID=$ELS_ID
								order by ELK_ID ";
				$db_dpis2->send_cmd($cmd);
				while($data2 = $db_dpis2->get_array()){
					$EPK_NAME = $data2[ELK_NAME];
					$EPK_COACH = $data2[ELK_COACH];
					$EPK_BEHAVIOR = $data2[ELK_BEHAVIOR];
					$EPK_TRAIN = $data2[ELK_TRAIN];
					$EPK_JOB = $data2[ELK_JOB];
					
					$cmd = " select max(EPK_ID) as max_id from EAF_PERSONAL_KNOWLEDGE ";
					$db_dpis3->send_cmd($cmd);
					$data3 = $db_dpis3->get_array();
					$data3 = array_change_key_case($data3, CASE_LOWER);
					$EPK_ID = $data3[max_id] + 1;

					$EPK_NAME = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($EPK_NAME)));
					$EPK_BEHAVIOR = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($EPK_BEHAVIOR)));
					$EPK_COACH = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($EPK_COACH)));
					$EPK_TRAIN = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($EPK_TRAIN)));
					$EPK_JOB = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($EPK_JOB)));

					$cmd = " insert into EAF_PERSONAL_KNOWLEDGE (EPK_ID, EP_ID, EPS_ID, EPK_NAME, EPK_BEHAVIOR, EPK_COACH, 
									EPK_TRAIN, EPK_JOB, UPDATE_USER, UPDATE_DATE )
									values ($EPK_ID, $EP_ID, $EPS_ID, '$EPK_NAME', '$EPK_BEHAVIOR', '$EPK_COACH',
									'$EPK_TRAIN', '$EPK_JOB', $SESS_USERID, '$UPDATE_DATE' ) ";
					$db_dpis3->send_cmd($cmd);
				} // inner loop while
			} // outer loop while

			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [".$EP_ID." : ".trim($EP_YEAR)." : ".$PER_NAME." : ".$EAF_NAME."]");
		}else{
			$data = $db_dpis->get_array();
			$EP_YEAR = $data[EP_YEAR];
			$PER_ID = $data[PER_ID];
			$EAF_ID = $data[EAF_ID];
			
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

			$cmd = " select EAF_NAME from EAF_MASTER where EAF_ID=$EAF_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$EAF_NAME = $data[EAF_NAME];

			$err_text = "รหัสข้อมูลซ้ำ [ปีงบประมาณ ".$EP_YEAR." ".$PER_FULLNAME." ".$EAF_NAME."]";
		} // endif
	}

	if($command == "UPDATE" && trim($EP_ID)  && trim($EP_YEAR) && trim($PER_ID) && trim($EAF_ID)){
		if($DPISDB == "odbc"){
			$cmd = " select 	EP_YEAR, PER_ID, EAF_ID
					  		 from 		EAF_PERSONAL 
							 where 	EP_YEAR='$EP_YEAR' and PER_ID = $PER_ID and EAF_ID=$EAF_ID and EP_ID <> $EP_ID ";
		}elseif($DPISDB == "oci8"){
			$cmd = " select 	EP_YEAR, PER_ID, EAF_ID
					  		 from 		EAF_PERSONAL 
							 where 	EP_YEAR='$EP_YEAR' and PER_ID = $PER_ID and EAF_ID=$EAF_ID and EP_ID <> $EP_ID ";
		} // end if
		$count_duplicate = $db_dpis->send_cmd($cmd);
		if($count_duplicate <= 0){
			$cmd = " update EAF_PERSONAL set 
								EP_YEAR=$EP_YEAR,
								START_DATE= '$START_DATE', 
								END_DATE= '$END_DATE', 
								PER_ID=$PER_ID, 
								EAF_ID=$EAF_ID, 
								PER_ID_REVIEW=$PER_ID_REVIEW,
								PER_ID_REVIEW1=$PER_ID_REVIEW1,
								PER_ID_REVIEW2=$PER_ID_REVIEW2,
								UPDATE_USER=$SESS_USERID, 
								UPDATE_DATE='$UPDATE_DATE' 
							 where EP_ID=$EP_ID ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
	
			// delete all data (old data) which belong to EP_ID from 
			// EAF_PERSONAL_STRUCTURE, EAF_PERSONAL_KNOWLEDGE
			
			$cmd = " delete from EAF_PERSONAL_STRUCTURE where EP_ID=$EP_ID ";
			$db_dpis->send_cmd($cmd);
			
			$cmd = " delete from EAF_PERSONAL_KNOWLEDGE where EP_ID=$EP_ID ";
			$db_dpis->send_cmd($cmd);
			
			// copy all data (new data) which belong to EAF_ID from 
			// EAF_LEARNING_STRUCTURE -> EAF_PERSONAL_STRUCTURE
			// EAF_LEARNING_KNOWLEDGE -> EAF_PERSONAL_KNOWLEDGE
			// with reference key is EP_ID

			$cmd = " select 	ELS_ID, MINISTRY_ID, DEPARTMENT_ID, ORG_ID, ORG_ID_1, ORG_ID_2, ELS_LEVEL, ELS_PERIOD, ELS_SEQ_NO
							from		EAF_LEARNING_STRUCTURE
							where		EAF_ID = $EAF_ID
							order by ELS_ID ";
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()){
				$ELS_ID = $data[ELS_ID];
				$EPS_MINISTRY_ID = $data[MINISTRY_ID];
				$EPS_DEPARTMENT_ID = $data[DEPARTMENT_ID];
				$EPS_ORG_ID = $data[ORG_ID];
				$EPS_ORG_ID_1 = $data[ORG_ID_1];
				$EPS_ORG_ID_2 = $data[ORG_ID_2];
				$EPS_LEVEL = $data[ELS_LEVEL];
				$EPS_PERIOD = $data[ELS_PERIOD];
				$EPS_SEQ_NO = $data[ELS_SEQ_NO] + 0;
				
				$cmd = " select max(EPS_ID) as max_id from EAF_PERSONAL_STRUCTURE ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$data2 = array_change_key_case($data2, CASE_LOWER);
				$EPS_ID = $data2[max_id] + 1;
				
				if(!trim($EPS_ORG_ID)) $EPS_ORG_ID = "NULL";
				if(!trim($EPS_ORG_ID_1)) $EPS_ORG_ID_1 = "NULL";
				if(!trim($EPS_ORG_ID_2)) $EPS_ORG_ID_2 = "NULL";
				if(!trim($EPS_PERIOD)) $EPS_PERIOD = "NULL";

				$cmd = " insert into EAF_PERSONAL_STRUCTURE (EPS_ID, EP_ID, MINISTRY_ID, DEPARTMENT_ID, ORG_ID, ORG_ID_1, 
								ORG_ID_2, EPS_SEQ_NO, EPS_LEVEL, EPS_PERIOD, UPDATE_USER, UPDATE_DATE) 
								values ($EPS_ID, $EP_ID, $EPS_MINISTRY_ID, $EPS_DEPARTMENT_ID, $EPS_ORG_ID, $EPS_ORG_ID_1, 
								$EPS_ORG_ID_2, $EPS_SEQ_NO, $EPS_LEVEL, $EPS_PERIOD, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis2->send_cmd($cmd);
				
				$cmd = " select		ELK_NAME, ELK_COACH, ELK_BEHAVIOR, ELK_TRAIN, ELK_JOB
								from		EAF_LEARNING_KNOWLEDGE
								where		EAF_ID=$EAF_ID and ELS_ID=$ELS_ID
								order by ELK_ID ";
				$db_dpis2->send_cmd($cmd);
				while($data2 = $db_dpis2->get_array()){
					$EPK_NAME = $data2[ELK_NAME];
					$EPK_COACH = $data2[ELK_COACH];
					$EPK_BEHAVIOR = $data2[ELK_BEHAVIOR];
					$EPK_TRAIN = $data2[ELK_TRAIN];
					$EPK_JOB = $data2[ELK_JOB];
					
					$cmd = " select max(EPK_ID) as max_id from EAF_PERSONAL_KNOWLEDGE ";
					$db_dpis3->send_cmd($cmd);
					$data3 = $db_dpis3->get_array();
					$data3 = array_change_key_case($data3, CASE_LOWER);
					$EPK_ID = $data3[max_id] + 1;

					$EPK_NAME = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($EPK_NAME)));
					$EPK_BEHAVIOR = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($EPK_BEHAVIOR)));
					$EPK_COACH = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($EPK_COACH)));
					$EPK_TRAIN = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($EPK_TRAIN)));
					$EPK_JOB = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($EPK_JOB)));

					$cmd = " insert into EAF_PERSONAL_KNOWLEDGE (EPK_ID, EP_ID, EPS_ID, EPK_NAME, EPK_BEHAVIOR, EPK_COACH, 
									EPK_TRAIN, EPK_JOB, UPDATE_USER, UPDATE_DATE )
									values	($EPK_ID, $EP_ID, $EPS_ID, '$EPK_NAME', '$EPK_BEHAVIOR', '$EPK_COACH',
									'$EPK_TRAIN', '$EPK_JOB', $SESS_USERID, '$UPDATE_DATE' ) ";
					$db_dpis3->send_cmd($cmd);
				} // inner loop while
			} // outer loop while

			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [".$EP_ID." : ".trim($EP_YEAR)." : ".$PER_NAME." : ".$EAF_NAME."]");
		}else{
			$data = $db_dpis->get_array();
			$EP_YEAR = $data[EP_YEAR];
			$PER_ID = $data[PER_ID];
			$EAF_ID = $data[EAF_ID];
			
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

			$cmd = " select EAF_NAME from EAF_MASTER where EAF_ID=$EAF_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$EAF_NAME = $data[EAF_NAME];

			$err_text = "รหัสข้อมูลซ้ำ [ปีงบประมาณ ".$EP_YEAR." ".$PER_FULLNAME." ".$EAF_NAME."]";
		} // end if
	}
	
	if($command == "DELETE" && trim($EP_ID)){
		$cmd = " select 	EP_YEAR, PER_ID, EAF_ID
				  		 from 		EAF_PERSONAL 
						 where 	EP_ID = $EP_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$EP_YEAR = $data[EP_YEAR];
		$PER_ID = $data[PER_ID];
		$EAF_ID = $data[EAF_ID];
		
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

		$cmd = " select EAF_NAME from EAF_MASTER where EAF_ID=$EAF_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$EAF_NAME = $data[EAF_NAME];
		
		// delete all data (old data) which belong to EP_ID from 
		// EAF_PERSONAL_STRUCTURE, EAF_PERSONAL_KNOWLEDGE

		$cmd = " delete from EAF_PERSONAL_STRUCTURE where EP_ID=$EP_ID ";
		$db_dpis->send_cmd($cmd);
		
		$cmd = " delete from EAF_PERSONAL_KNOWLEDGE where EP_ID=$EP_ID ";
		$db_dpis->send_cmd($cmd);

		$cmd = " delete from EAF_PERSONAL_DETAIL where EP_ID=$EP_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		
		$cmd = " delete from EAF_PERSONAL where EP_ID=$EP_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [".$EP_ID." : ".trim($EP_YEAR)." : ".$PER_NAME." : ".$EAF_NAME."]");
	}
	
	if($UPD || $VIEW){
		$cmd = " select 	EP_YEAR, START_DATE, END_DATE, PER_ID, EAF_ID, PER_ID_REVIEW, PER_ID_REVIEW1, PER_ID_REVIEW2
				  		 from 		EAF_PERSONAL
						 where 	EP_ID=$EP_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$EP_YEAR = trim($data[EP_YEAR]);
		$START_DATE = show_date_format($data[START_DATE], 1);
		$END_DATE = show_date_format($data[END_DATE], 1);
		
		$PER_ID = $data[PER_ID];
		$EAF_ID = $data[EAF_ID];
		$PER_ID_REVIEW = $data[PER_ID_REVIEW];
		$PER_ID_REVIEW1 = $data[PER_ID_REVIEW1];
		$PER_ID_REVIEW2 = $data[PER_ID_REVIEW2];
		
		$cmd = " select 	PN_CODE, PER_NAME, PER_SURNAME, LEVEL_NO, PER_SALARY, PER_TYPE, POS_ID, DEPARTMENT_ID
						 from		PER_PERSONAL
						 where	PER_ID=$PER_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PN_CODE = trim($data[PN_CODE]);
		$PER_NAME = trim($data[PER_NAME]);
		$PER_SURNAME = trim($data[PER_SURNAME]);
		$LEVEL_NO = trim($data[LEVEL_NO]);
		$PER_SALARY = trim($data[PER_SALARY]);
		$PER_TYPE = trim($data[PER_TYPE]);
		$POS_ID = trim($data[POS_ID]);
		$DEPARTMENT_ID = $data[DEPARTMENT_ID];
		
		$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='$PN_CODE' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PN_NAME = trim($data[PN_NAME]);
		
		$PER_NAME = $PN_NAME . $PER_NAME . " " . $PER_SURNAME;
		
		$cmd = " select LEVEL_NAME from PER_LEVEL where trim(LEVEL_NO)='$LEVEL_NO' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$LEVEL_NAME = trim($data[LEVEL_NAME]);
		
		$cmd = " select ORG_NAME, ORG_ID_REF from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$DEPARTMENT_NAME = trim($data[ORG_NAME]);
		$MINISTRY_ID = $data[ORG_ID_REF];

		$cmd = " select ORG_NAME, ORG_ID_REF from PER_ORG where ORG_ID=$MINISTRY_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MINISTRY_NAME = trim($data[ORG_NAME]);

		$cmd = " select 	b.PL_NAME, c.ORG_NAME, a.PT_CODE 
						 from 		PER_POSITION a, PER_LINE b, PER_ORG c
						 where	a.POS_ID=$POS_ID and a.PL_CODE=b.PL_CODE and a.ORG_ID=c.ORG_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PL_NAME = trim($data[PL_NAME]);
		$ORG_NAME = trim($data[ORG_NAME]);
		$PT_CODE = trim($data[PT_CODE]);
		$cmd = "	select PT_NAME from PER_TYPE where PT_CODE='$PT_CODE'";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$PT_NAME = trim($data2[PT_NAME]);
 		$PL_NAME = (trim($PM_NAME) ?"$PM_NAME (":"") . (trim($PL_NAME)? "$PL_NAME $LEVEL_NAME" : "") . (trim($PM_NAME) ?")":"");
		
		$cmd = " select 	PN_CODE, PER_NAME, PER_SURNAME, PER_TYPE, POS_ID, LEVEL_NO
						 from		PER_PERSONAL
						 where	PER_ID=$PER_ID_REVIEW ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$REVIEW_PN_CODE = trim($data[PN_CODE]);
		$REVIEW_PER_NAME = trim($data[PER_NAME]);
		$REVIEW_PER_SURNAME = trim($data[PER_SURNAME]);
		$REVIEW_PER_TYPE = trim($data[PER_TYPE]);
		$REVIEW_POS_ID = trim($data[POS_ID]);
		$REVIEW_LEVEL_NO = trim($data[LEVEL_NO]);

		$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='$REVIEW_PN_CODE' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$REVIEW_PN_NAME = trim($data[PN_NAME]);
		
		$REVIEW_PER_NAME = $REVIEW_PN_NAME . $REVIEW_PER_NAME . " " . $REVIEW_PER_SURNAME;
		
		$cmd = " select LEVEL_NAME from PER_LEVEL where trim(LEVEL_NO)='$REVIEW_LEVEL_NO' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$REVIEW_LEVEL_NAME = trim($data[LEVEL_NAME]);
		
		$cmd = " select 	b.PL_NAME, a.PT_CODE 
						 from 		PER_POSITION a, PER_LINE b
						 where	a.POS_ID=$REVIEW_POS_ID and a.PL_CODE=b.PL_CODE ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$REVIEW_PL_NAME = trim($data[PL_NAME]);
		$REVIEW_PT_CODE = trim($data[PT_CODE]);
		$cmd = "	select PT_NAME from PER_TYPE where PT_CODE='$REVIEW_PT_CODE'";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$REVIEW_PT_NAME = trim($data2[PT_NAME]);
 		$REVIEW_PL_NAME = (trim($REVIEW_PM_NAME) ?"$REVIEW_PM_NAME (":"") . (trim($REVIEW_PL_NAME)? "$REVIEW_PL_NAME $REVIEW_LEVEL_NAME" : "") . (trim($REVIEW_PM_NAME) ?")":"");

		$cmd = " select 	PN_CODE, PER_NAME, PER_SURNAME, PER_TYPE, POS_ID, LEVEL_NO
						 from		PER_PERSONAL
						 where	PER_ID=$PER_ID_REVIEW1 ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$REVIEW_PN_CODE1 = trim($data[PN_CODE]);
		$REVIEW_PER_NAME1 = trim($data[PER_NAME]);
		$REVIEW_PER_SURNAME1 = trim($data[PER_SURNAME]);
		$REVIEW_PER_TYPE1 = trim($data[PER_TYPE]);
		$REVIEW_POS_ID1 = trim($data[POS_ID]);
		$REVIEW_LEVEL_NO1 = trim($data[LEVEL_NO]);

		$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='$REVIEW_PN_CODE1' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$REVIEW_PN_NAME1 = trim($data[PN_NAME]);
		
		$REVIEW_PER_NAME1 = $REVIEW_PN_NAME1 . $REVIEW_PER_NAME1 . " " . $REVIEW_PER_SURNAME1;
		
		$cmd = " select LEVEL_NAME from PER_LEVEL where trim(LEVEL_NO)='$REVIEW_LEVEL_NO1' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$REVIEW_LEVEL_NAME1 = trim($data[LEVEL_NAME]);
		
		$cmd = " select 	b.PL_NAME, a.PT_CODE
						 from 		PER_POSITION a, PER_LINE b
						 where	a.POS_ID=$REVIEW_POS_ID1 and a.PL_CODE=b.PL_CODE ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$REVIEW_PL_NAME1 = trim($data[PL_NAME]);
		$REVIEW_PT_CODE1 = trim($data[PT_CODE]);
		$cmd = "	select PT_NAME from PER_TYPE where PT_CODE='$REVIEW_PT_CODE1'";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$REVIEW_PT_NAME1 = trim($data2[PT_NAME]);
 		$REVIEW_PL_NAME1 = (trim($REVIEW_PM_NAME1) ?"$REVIEW_PM_NAME1 (":"") . (trim($REVIEW_PL_NAME1)? "$REVIEW_PL_NAME1 $REVIEW_LEVEL_NAME1" : "") . (trim($REVIEW_PM_NAME1) ?")":"");

		$cmd = " select 	PN_CODE, PER_NAME, PER_SURNAME, PER_TYPE, POS_ID, LEVEL_NO
						 from		PER_PERSONAL
						 where	PER_ID=$PER_ID_REVIEW2 ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$REVIEW_PN_CODE2 = trim($data[PN_CODE]);
		$REVIEW_PER_NAME2 = trim($data[PER_NAME]);
		$REVIEW_PER_SURNAME2 = trim($data[PER_SURNAME]);
		$REVIEW_PER_TYPE2 = trim($data[PER_TYPE]);
		$REVIEW_POS_ID2 = trim($data[POS_ID]);
		$REVIEW_LEVEL_NO2 = trim($data[LEVEL_NO]);

		$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='$REVIEW_PN_CODE1' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$REVIEW_PN_NAME2 = trim($data[PN_NAME]);
		
		$REVIEW_PER_NAME2 = $REVIEW_PN_NAME2 . $REVIEW_PER_NAME2 . " " . $REVIEW_PER_SURNAME2;
		
		$cmd = " select LEVEL_NAME from PER_LEVEL where trim(LEVEL_NO)='$REVIEW_LEVEL_NO2' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$REVIEW_LEVEL_NAME2 = trim($data[LEVEL_NAME]);
		
		$cmd = " select 	b.PL_NAME, a.PT_CODE
						 from 		PER_POSITION a, PER_LINE b
						 where	a.POS_ID=$REVIEW_POS_ID2 and a.PL_CODE=b.PL_CODE ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$REVIEW_PL_NAME2 = trim($data[PL_NAME]);
		$REVIEW_PT_CODE2 = trim($data[PT_CODE]);
		$cmd = "	select PT_NAME from PER_TYPE where PT_CODE='$REVIEW_PT_CODE2'";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$REVIEW_PT_NAME2 = trim($data2[PT_NAME]);
 		$REVIEW_PL_NAME2 = (trim($REVIEW_PM_NAME2) ?"$REVIEW_PM_NAME2 (":"") . (trim($REVIEW_PL_NAME2)? "$REVIEW_PL_NAME2 $REVIEW_LEVEL_NAME2" : "") . (trim($REVIEW_PM_NAME2) ?")":"");

		$cmd = " select		EAF_NAME, PL_CODE, LEVEL_NO, PM_CODE, PT_CODE
						from		EAF_MASTER
						where		EAF_ID=$EAF_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$EAF_NAME = $data[EAF_NAME];
		$EAF_PL_CODE = $data[PL_CODE];
		$EAF_LEVEL_NO = $data[LEVEL_NO];
		$EAF_PM_CODE = $data[PM_CODE];
		$EAF_PT_CODE = $data[PT_CODE];

		$cmd = " select PL_NAME from PER_LINE where PL_CODE='$EAF_PL_CODE' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$EAF_PL_NAME = $data[PL_NAME];

		$cmd = " select LEVEL_NAME from PER_LEVEL where LEVEL_NO='$EAF_LEVEL_NO' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$EAF_LEVEL_NAME = $data[LEVEL_NAME];

		$cmd = " select PM_NAME from PER_MGT where PM_CODE='$EAF_PM_CODE' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$EAF_PM_NAME = $data[PM_NAME];

		$cmd = " select PT_NAME from PER_TYPE where PT_CODE='$EAF_PT_CODE' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$EAF_PT_NAME = $data[PT_NAME];

	} // end if
	
	if( (!$UPD && !$DEL && !$VIEW && !$err_text) ){
		$EP_ID = "";
		if(date("Y-m-d") <= date("Y")."-10-01") $EP_YEAR = date("Y") + 543;
		else $EP_YEAR = (date("Y") + 543) + 1;
		$START_DATE = "";
		$END_DATE = "";

		if($SESS_GROUPCODE != "BUREAU" && substr($SESS_GROUPCODE, 0, 7) != "BUREAU_"  && $SESS_USERGROUP!=3){
			$PER_ID = "";
			$PER_CARDNO = "";
			$PER_NAME = "";
			$PL_NAME = "";
			$LEVEL_NAME = "";
			$ORG_NAME = "";
			$PER_SALARY = "";
		} // end if
		
		$EAF_ID = "";
		$EAF_NAME = "";
		$EAF_PL_NAME = "";
		$EAF_PT_NAME = "";
		$EAF_PM_NAME = "";
		$EAF_LEVEL_NAME = "";

		$PER_ID_REVIEW = "";
		$REVIEW_PER_NAME = "";
		$REVIEW_PL_NAME = "";

		$PER_ID_REVIEW1 = "";
		$REVIEW_PER_NAME1 = "";
		$REVIEW_PL_NAME1 = "";

		$PER_ID_REVIEW2 = "";
		$REVIEW_PER_NAME2 = "";
		$REVIEW_PL_NAME2 = "";
	} // end if
?>