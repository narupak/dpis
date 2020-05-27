<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");	
	include("php_scripts/load_per_control.php");	

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	$cmd = " select code, name_th, group_level, pv_code, org_id from user_group where id=$SESS_USERGROUP ";
	$db->send_cmd($cmd);
//	$db->show_error();
	$data = $db->get_array();
	$data = array_change_key_case($data, CASE_LOWER);
	$SESS_GROUPCODE = $data[code];
	
	if(($SESS_GROUPCODE == "BUREAU" || substr($SESS_GROUPCODE, 0, 7) == "BUREAU_" ) || $SESS_USERGROUP==3){
		$cmd = " select 	PN_CODE, PER_NAME, PER_SURNAME, PER_CARDNO, LEVEL_NO, PER_SALARY, PER_TYPE, POS_ID, POEM_ID, POEMS_ID 
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
		
		if($PER_TYPE == 1){
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
		}elseif($PER_TYPE == 2){
		}elseif($PER_TYPE == 3){
		} // end if
		
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
		case 6 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			$search_org_id = $ORG_ID;
			$search_org_name = $ORG_NAME;
			$search_org_id_1 = $ORG_ID_1;
			$search_org_name_1 = $ORG_NAME_1;
			break;
	} // end switch case

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	if(!$PG_YEAR)	$PG_YEAR = $KPI_BUDGET_YEAR;
	if(!$search_pg_year)	$search_pg_year = $KPI_BUDGET_YEAR;
	if(!isset($search_pg_year)){
		if(date("Y-m-d") <= date("Y")."-10-01") $search_pg_year = date("Y") + 543;
		else $search_pg_year = (date("Y") + 543) + 1;
	} // end if
	
	if (!$PG_CYCLE) $PG_CYCLE = $KPI_CYCLE;
	if(!isset($search_pg_cycle)){
		$search_pg_cycle = array(1, 2);
	} // end if
	
	$UPDATE_DATE = date("Y-m-d H:i:s");	

	if($PG_CYCLE == 1){
		$PG_START_DATE = save_date($PG_START_DATE_1);
		$PG_END_DATE = save_date($PG_END_DATE_1);
	}elseif($PG_CYCLE == 2){
		$PG_START_DATE = save_date($PG_START_DATE_2);
		$PG_END_DATE = save_date($PG_END_DATE_2);
	} // end if
	
	if(!$PER_ID_REVIEW) $PER_ID_REVIEW = "NULL";
	if(!$PER_ID_REVIEW1) $PER_ID_REVIEW1 = "NULL";
	
	if($command == "ADD" && trim($PG_YEAR) && trim($PG_CYCLE) && trim($PER_ID)){
		if($DPISDB == "odbc"){
			$cmd = " select 	PG_END_DATE, PG_CYCLE, PER_ID
					  		 from 		PER_PERFORMANCE_GOODNESS 
							 where 	LEFT(PG_END_DATE, 10) = '$PG_END_DATE' and  PG_CYCLE=$PG_CYCLE and PER_ID = $PER_ID ";
		}elseif($DPISDB == "oci8"){
			$cmd = " select 	PG_END_DATE, PG_CYCLE, PER_ID
					  		 from 		PER_PERFORMANCE_GOODNESS 
							 where 	SUBSTR(PG_END_DATE, 1, 10) = '$PG_END_DATE' and  PG_CYCLE=$PG_CYCLE and PER_ID = $PER_ID ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select 	PG_END_DATE, PG_CYCLE, PER_ID
					  		 from 		PER_PERFORMANCE_GOODNESS 
							 where 	LEFT(PG_END_DATE, 10) = '$PG_END_DATE' and  PG_CYCLE=$PG_CYCLE and PER_ID = $PER_ID ";
		} // end if
		$count_duplicate = $db_dpis->send_cmd($cmd);
		if($count_duplicate <= 0){
			$cmd = " select max(PG_ID) as max_id from PER_PERFORMANCE_GOODNESS ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$PG_ID = $data[max_id] + 1;
			
			$cmd = " insert into PER_PERFORMANCE_GOODNESS 
							(PG_ID, PER_ID, PER_CARDNO, PG_CYCLE, PG_START_DATE, PG_END_DATE, PER_ID_REVIEW, 
							PER_ID_REVIEW1, DEPARTMENT_ID, UPDATE_USER, UPDATE_DATE) 
							values 
							($PG_ID, $PER_ID, '$PER_CARDNO', $PG_CYCLE, '$PG_START_DATE', '$PG_END_DATE', 
							$PER_ID_REVIEW, $PER_ID_REVIEW1, $DEPARTMENT_ID, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();

			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [".$PG_ID." : ".trim($PG_YEAR)." : ".$PG_CYCLE." : ".$PER_NAME."]");

			$cmd = " select PF_CODE from PER_PERFORMANCE where PF_ACTIVE=1 order by PF_CODE ";
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()){
				$PF_CODE = $data[PF_CODE];

				$cmd = " select max(PD_ID) as max_id from PER_PERFORMANCE_DTL ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$data2 = array_change_key_case($data2, CASE_LOWER);
				$PD_ID = $data2[max_id] + 1;

				$cmd = " insert into PER_PERFORMANCE_DTL 
								 (PD_ID, PG_ID, PF_CODE, UPDATE_USER, UPDATE_DATE) 
									values 
								 ($PD_ID, $PG_ID, '$PF_CODE', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis2->send_cmd($cmd);
			} // end while

			$cmd = " select GN_CODE from PER_GOODNESS where GN_ACTIVE=1 order by GN_CODE ";
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()){
				$GN_CODE = $data[GN_CODE];

				$cmd = " select max(GD_ID) as max_id from PER_GOODNESS_DTL ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$data2 = array_change_key_case($data2, CASE_LOWER);
				$GD_ID = $data2[max_id] + 1;

				$cmd = " insert into PER_GOODNESS_DTL 
								 (GD_ID, PG_ID, GN_CODE, UPDATE_USER, UPDATE_DATE) 
									values 
								 ($GD_ID, $PG_ID, '$GN_CODE', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis2->send_cmd($cmd);
			} // end while
		}else{
			$data = $db_dpis->get_array();
			$PG_END_DATE = substr($data[PG_END_DATE], 0, 10);
			$PG_YEAR = substr($PG_END_DATE, 0, 4) + 543;
			$PG_CYCLE = $data[PG_CYCLE];
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

			$err_text = "รหัสข้อมูลซ้ำ [ปีงบประมาณ ".$PG_YEAR." ครั้งที่ ".$PG_CYCLE." ".$PER_FULLNAME."]";

			$PG_START_DATE_1 = "01/10/". ($PG_YEAR - 1);
			$PG_END_DATE_1 = "31/03/". $PG_YEAR;
			$PG_START_DATE_2 = "01/04/". $PG_YEAR;
			$PG_END_DATE_2 = "30/09/". $PG_YEAR;
		} // endif
	}

	if($command == "UPDATE" && trim($PG_ID)  && trim($PG_YEAR) && trim($PG_CYCLE) && trim($PER_ID)){
		if($DPISDB == "odbc"){
			$cmd = " select 	PG_END_DATE, PG_CYCLE, PER_ID
					  		 from 		PER_PERFORMANCE_GOODNESS 
							 where 	LEFT(PG_END_DATE, 10) = '$PG_END_DATE' and PG_CYCLE=$PG_CYCLE and PER_ID = $PER_ID and PG_ID <> $PG_ID ";
		}elseif($DPISDB == "oci8"){
			$cmd = " select 	PG_END_DATE, PG_CYCLE, PER_ID
					  		 from 		PER_PERFORMANCE_GOODNESS 
							 where 	SUBSTR(PG_END_DATE, 1, 10) = '$PG_END_DATE' and PG_CYCLE=$PG_CYCLE and PER_ID = $PER_ID and PG_ID <> $PG_ID ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select 	PG_END_DATE, PG_CYCLE, PER_ID
					  		 from 		PER_PERFORMANCE_GOODNESS 
							 where 	LEFT(PG_END_DATE, 10) = '$PG_END_DATE' and PG_CYCLE=$PG_CYCLE and PER_ID = $PER_ID and PG_ID <> $PG_ID ";
		} // end if
		$count_duplicate = $db_dpis->send_cmd($cmd);
		if($count_duplicate <= 0){
			$cmd = " update PER_PERFORMANCE_GOODNESS set 
								PG_CYCLE=$PG_CYCLE,
								PG_START_DATE='".$PG_START_DATE."', 
								PG_END_DATE='".$PG_END_DATE."', 
								PER_ID=$PER_ID, 
								PER_CARDNO='".$PER_CARDNO."', 
								PER_ID_REVIEW=$PER_ID_REVIEW,
								PER_ID_REVIEW1=$PER_ID_REVIEW1,
								UPDATE_USER=$SESS_USERID, 
								UPDATE_DATE='$UPDATE_DATE' 
							 where PG_ID=$PG_ID ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
	
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [".$PG_ID." : ".trim($PG_YEAR)." : ".$PG_CYCLE." : ".$PER_NAME."]");
		}else{
			$data = $db_dpis->get_array();
			$PG_END_DATE = substr($data[PG_END_DATE], 0, 10);
			$PG_YEAR = substr($PG_END_DATE, 0, 4) + 543;
			$PG_CYCLE = $data[PG_CYCLE];
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

			$err_text = "รหัสข้อมูลซ้ำ [".$PG_YEAR." ".$PG_CYCLE." ".$PER_FULLNAME."]";
			
			$PG_START_DATE_1 = "01/10/". ($PG_YEAR - 1);
			$PG_END_DATE_1 = "31/03/". $PG_YEAR;
			$PG_START_DATE_2 = "01/04/". $PG_YEAR;
			$PG_END_DATE_2 = "30/09/". $PG_YEAR;			
		} // end if
	}
	
	if($command == "DELETE" && trim($PG_ID)){
		$cmd = " select 	PG_END_DATE, PG_CYCLE, PER_ID
				  		 from 		PER_PERFORMANCE_GOODNESS 
						 where 	PG_ID = $PG_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PG_END_DATE = substr($data[PG_END_DATE], 0, 10);
		$PG_YEAR = substr($PG_END_DATE, 0, 4);
		$PG_CYCLE = $data[PG_CYCLE];
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
		
		$cmd = " delete from PER_PERFORMANCE_DTL where PG_ID=$PG_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		
		$cmd = " delete from PER_GOODNESS_DTL where PG_ID=$PG_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " delete from PER_TRAINING_DTL where PG_ID=$PG_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " delete from PER_PERFORMANCE_GOODNESS where PG_ID=$PG_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [". $PG_ID ." : ".$PG_YEAR." ".$PG_CYCLE." ".$PER_FULLNAME."]");
	}
	
	if($UPD || $VIEW){
		$cmd = " select 	PG_CYCLE, PG_START_DATE, PG_END_DATE, PER_ID, PER_CARDNO, PER_ID_REVIEW, PER_ID_REVIEW1
				  		 from 		PER_PERFORMANCE_GOODNESS
						 where 	PG_ID=$PG_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PG_CYCLE = trim($data[PG_CYCLE]);
		$PG_START_DATE = show_date_format($data[PG_START_DATE], 1);
		$PG_END_DATE = show_date_format($data[PG_END_DATE], 1);
		$PG_YEAR = substr($data[PG_END_DATE], 0, 4) + 543;
		
		$PER_ID = $data[PER_ID];
		$PER_CARDNO = trim($data[PER_CARDNO]);		
		$PER_ID_REVIEW = $data[PER_ID_REVIEW];
		$PER_ID_REVIEW1 = $data[PER_ID_REVIEW1];
		$PER_ID_REVIEW2 = $data[PER_ID_REVIEW2];
		
		$cmd = " select 	PN_CODE, PER_NAME, PER_SURNAME, LEVEL_NO, PER_SALARY, PER_TYPE, POS_ID, POEM_ID, POEMS_ID
						 from		PER_PERSONAL
						 where	PER_ID=$PER_ID
					   ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PN_CODE = trim($data[PN_CODE]);
		$PER_NAME = trim($data[PER_NAME]);
		$PER_SURNAME = trim($data[PER_SURNAME]);
		$LEVEL_NO = trim($data[LEVEL_NO]);
		$PER_SALARY = trim($data[PER_SALARY]);
		$PER_TYPE = trim($data[PER_TYPE]);
		$POS_ID = trim($data[POS_ID]);
		$POEM_ID = trim($data[POEM_ID]);
		$POEMS_ID = trim($data[POEMS_ID]);
		
		$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='$PN_CODE' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PN_NAME = trim($data[PN_NAME]);
		
		$PER_NAME = $PN_NAME . $PER_NAME . " " . $PER_SURNAME;
		
		$cmd = " select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where trim(LEVEL_NO)='$LEVEL_NO' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$LEVEL_NAME = trim($data[LEVEL_NAME]);
		$POSITION_LEVEL = $data[POSITION_LEVEL];
		if (!$POSITION_LEVEL) $POSITION_LEVEL = $LEVEL_NAME;

		if($PER_TYPE==1){
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
			if ($RPT_N)
			  $PL_NAME = (trim($PM_NAME) ?"$PM_NAME (":"") . (trim($PL_NAME)? "$PL_NAME$POSITION_LEVEL" : "") . (trim($PM_NAME) ?")":"");
			else
			    $PL_NAME = (trim($PM_NAME) ?"$PM_NAME (":"") . (trim($PL_NAME)?($PL_NAME ." ". level_no_format($LEVEL_NO) . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"")):"") . (trim($PM_NAME) ?")":"");
		}elseif($PER_TYPE==2){
			$cmd = " select 	b.PN_NAME, c.ORG_NAME 
							 from 		PER_POS_EMP a, PER_POS_NAME b, PER_ORG c
							 where	a.POEM_ID=$POEM_ID and a.PN_CODE=b.PN_CODE and a.ORG_ID=c.ORG_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$PL_NAME = trim($data[PN_NAME]);
			$ORG_NAME = trim($data[ORG_NAME]);
		}elseif($PER_TYPE==3){
			$cmd = " select 	b.EP_NAME, c.ORG_NAME 
							 from 		PER_POS_EMPSER a, PER_EMPSER_POS_NAME b, PER_ORG c
							 where	a.POEMS_ID=$POEMS_ID and a.EP_CODE=b.EP_CODE and a.ORG_ID=c.ORG_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$PL_NAME = trim($data[EP_NAME]);
			$ORG_NAME = trim($data[ORG_NAME]);
		} // end if
		if($PER_ID_REVIEW){
			$cmd = " select 	PN_CODE, PER_NAME, PER_SURNAME, PER_TYPE, POS_ID, POEM_ID, POEMS_ID, LEVEL_NO
							 from		PER_PERSONAL
							 where	PER_ID=$PER_ID_REVIEW ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$REVIEW_PN_CODE = trim($data[PN_CODE]);
			$REVIEW_PER_NAME = trim($data[PER_NAME]);
			$REVIEW_PER_SURNAME = trim($data[PER_SURNAME]);
			$REVIEW_PER_TYPE = trim($data[PER_TYPE]);
			$REVIEW_POS_ID = trim($data[POS_ID]);
			$REVIEW_POEM_ID = trim($data[POEM_ID]);
			$REVIEW_POEMS_ID = trim($data[POEMS_ID]);
			$REVIEW_LEVEL_NO = trim($data[LEVEL_NO]);

			$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='$REVIEW_PN_CODE' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$REVIEW_PN_NAME = trim($data[PN_NAME]);
		}
		$cmd = " select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$REVIEW_LEVEL_NO' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$REVIEW_LEVEL_NAME = $data2[LEVEL_NAME];
		$REVIEW_POSITION_LEVEL = $data2[POSITION_LEVEL];
		if (!$REVIEW_POSITION_LEVEL) $REVIEW_POSITION_LEVEL = $REVIEW_LEVEL_NAME;
		
		$REVIEW_PER_NAME = $REVIEW_PN_NAME . $REVIEW_PER_NAME . " " . $REVIEW_PER_SURNAME;
		
		if($REVIEW_PER_TYPE==1){
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
			if ($RPT_N)
			    $REVIEW_PL_NAME = (trim($REVIEW_PM_NAME) ?"$REVIEW_PM_NAME (":"") . (trim($REVIEW_PL_NAME)? "$REVIEW_PL_NAME$REVIEW_POSITION_LEVEL" : "") . (trim($REVIEW_PM_NAME) ?")":"");
			else
				$REVIEW_PL_NAME = (trim($REVIEW_PM_NAME) ?"$REVIEW_PM_NAME (":"") . (trim($REVIEW_PL_NAME)?($REVIEW_PL_NAME ." ". level_no_format($REVIEW_LEVEL_NO) . (($REVIEW_PT_NAME != "ทั่วไป" && $REVIEW_LEVEL_NO >= 6)?"$REVIEW_PT_NAME":"")):"") . (trim($REVIEW_PM_NAME) ?")":"");
		}elseif($REVIEW_PER_TYPE==2){
			$cmd = " select 	b.PN_NAME
							 from 		PER_POS_EMP a, PER_POS_NAME b
							 where	a.POEM_ID=$REVIEW_POEM_ID and a.PN_CODE=b.PN_CODE ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$REVIEW_PL_NAME = trim($data[PN_NAME]);
		}elseif($REVIEW_PER_TYPE==3){
			$cmd = " select 	b.EP_NAME
							 from 		PER_POS_EMPSER a, PER_EMPSER_POS_NAME b
							 where	a.POEMS_ID=$REVIEW_POEMS_ID and a.EP_CODE=b.EP_CODE ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$REVIEW_PL_NAME = trim($data[EP_NAME]);
		} // end if
		if($PER_ID_REVIEW1){
			$cmd = " select 	PN_CODE, PER_NAME, PER_SURNAME, PER_TYPE, POS_ID, POEM_ID, POEMS_ID, LEVEL_NO
							 from		PER_PERSONAL
							 where	PER_ID=$PER_ID_REVIEW1 ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$REVIEW_PN_CODE1 = trim($data[PN_CODE]);
			$REVIEW_PER_NAME1 = trim($data[PER_NAME]);
			$REVIEW_PER_SURNAME1 = trim($data[PER_SURNAME]);
			$REVIEW_PER_TYPE1 = trim($data[PER_TYPE]);
			$REVIEW_POS_ID1 = trim($data[POS_ID]);
			$REVIEW_POEM_ID1 = trim($data[POEM_ID]);
			$REVIEW_POEMS_ID1 = trim($data[POEMS_ID]);
			$REVIEW_LEVEL_NO1 = trim($data[LEVEL_NO]);

			$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='$REVIEW_PN_CODE1' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$REVIEW_PN_NAME1 = trim($data[PN_NAME]);
		}
		$cmd = " select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$REVIEW_LEVEL_NO1' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$REVIEW_LEVEL_NAME1 = $data2[LEVEL_NAME];
		$REVIEW_POSITION_LEVEL1 = $data2[POSITION_LEVEL];
		if (!$REVIEW_POSITION_LEVEL1) $REVIEW_POSITION_LEVEL1 = $REVIEW_LEVEL_NAME1;
		
		$REVIEW_PER_NAME1 = $REVIEW_PN_NAME1 . $REVIEW_PER_NAME1 . " " . $REVIEW_PER_SURNAME1;
		
		if($REVIEW_PER_TYPE1==1){
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
			if ($RPT_N)
			    $REVIEW_PL_NAME1 = (trim($REVIEW_PM_NAME1) ?"$REVIEW_PM_NAME1 (":"") . (trim($REVIEW_PL_NAME1)? "$REVIEW_PL_NAME1$REVIEW_POSITION_LEVEL1" : "") . (trim($REVIEW_PM_NAME1) ?")":"");
			else
				$REVIEW_PL_NAME1 = (trim($REVIEW_PM_NAME1) ?"$REVIEW_PM_NAME1 (":"") . (trim($REVIEW_PL_NAME1)?($REVIEW_PL_NAME1 ." ". level_no_format($REVIEW_LEVEL_NO1) . (($REVIEW_PT_NAME1 != "ทั่วไป" && $REVIEW_LEVEL_NO1 >= 6)?"$REVIEW_PT_NAME1":"")):"") . (trim($REVIEW_PM_NAME1) ?")":"");
		}elseif($REVIEW_PER_TYPE1==2){
			$cmd = " select 	b.PN_NAME
							 from 		PER_POS_EMP a, PER_POS_NAME b
							 where	a.POEM_ID=$REVIEW_POEM_ID1 and a.PN_CODE=b.PN_CODE ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$REVIEW_PL_NAME1 = trim($data[PN_NAME]);
		}elseif($REVIEW_PER_TYPE1==3){
			$cmd = " select 	b.EP_NAME
							 from 		PER_POS_EMPSER a, PER_EMPSER_POS_NAME b
							 where	a.POEMS_ID=$REVIEW_POEMS_ID1 and a.EP_CODE=b.EP_CODE ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$REVIEW_PL_NAME1 = trim($data[EP_NAME]);
		} // end if

		$PG_START_DATE_1 = "01/10/". ($PG_YEAR - 1);
		$PG_END_DATE_1 = "31/03/". $PG_YEAR;
		$PG_START_DATE_2 = "01/04/". $PG_YEAR;
		$PG_END_DATE_2 = "30/09/". $PG_YEAR;
	} // end if
	
	if( (!$UPD && !$DEL && !$VIEW && !$err_text) ){
		$PG_ID = "";
		$PG_CYCLE = "";
		if (!$PG_CYCLE) $PG_CYCLE = $KPI_CYCLE;
		if(!$PG_YEAR)	$PG_YEAR = $KPI_BUDGET_YEAR;
		if(!$PG_YEAR){
			if(date("Y-m-d") <= date("Y")."-10-01") $PG_YEAR = date("Y") + 543;
			else $PG_YEAR = (date("Y") + 543) + 1;
		}
		$PG_START_DATE_1 = "01/10/". ($PG_YEAR - 1);
		$PG_END_DATE_1 = "31/03/". $PG_YEAR;
		$PG_START_DATE_2 = "01/04/". $PG_YEAR;
		$PG_END_DATE_2 = "30/09/". $PG_YEAR;

		if($SESS_GROUPCODE != "BUREAU" && substr($SESS_GROUPCODE, 0, 7) != "BUREAU_"  && $SESS_USERGROUP!=3){
			$PER_ID = "";
			$PER_CARDNO = "";
			$PER_NAME = "";
			$PL_NAME = "";
			$LEVEL_NAME = "";
			$ORG_NAME = "";
			$PER_SALARY = "";
		} // end if

		$PER_ID_REVIEW = "";
		$REVIEW_PER_NAME = "";
		$REVIEW_PL_NAME = "";

		$PER_ID_REVIEW1 = "";
		$REVIEW_PER_NAME1 = "";
		$REVIEW_PL_NAME1 = "";
	} // end if
?>