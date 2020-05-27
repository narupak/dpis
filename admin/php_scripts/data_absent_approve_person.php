<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");	
	include("php_scripts/load_per_control.php");	

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$PER_ID_DEPARTMENT_ID = $DEPARTMENT_ID;

	switch($CTRL_TYPE){
		case 2 :
			$search_pv_code = $PROVINCE_CODE;
			$search_pv_name = $PROVINCE_NAME;
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
			$search_pv_code = $PROVINCE_CODE;
			$search_pv_name = $PROVINCE_NAME;
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
			$search_org_id1 = $ORG_ID;
			$search_org_name1 = $ORG_NAME;
			break;
		case 6 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			$search_org_id = $ORG_ID;
			$search_org_name = $ORG_NAME;
			$search_org_id1 = $ORG_ID;
			$search_org_name1 = $ORG_NAME;
			$search_org_id_1 = $ORG_ID_1;
			$search_org_name_1 = $ORG_NAME_1;
			break;
	} // end switch case

	$UPDATE_DATE = date("Y-m-d H:i:s");	

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	$APPROVE_PL_NAME = $APPROVE_PL_NAME1 = $APPROVE_PL_NAME2 = "";
	$cmd = " select code, name_th, group_level, pv_code, org_id from user_group where id=$SESS_USERGROUP ";
	$db->send_cmd($cmd);
//	$db->show_error();
	$data = $db->get_array();
	$data = array_change_key_case($data, CASE_LOWER);
	$SESS_GROUPCODE = $data[code];
	
	if(($SESS_GROUPCODE == "BUREAU" || substr($SESS_GROUPCODE, 0, 7) == "BUREAU_" ) || $SESS_USERGROUP==3 && trim($SESS_PER_ID)){
		$cmd = " select 	a.PN_CODE, a.PER_NAME, a.PER_SURNAME, a.PER_CARDNO, a.LEVEL_NO, b.LEVEL_NAME, b.POSITION_LEVEL, 
					a.PER_SALARY, a.PER_TYPE, a.POS_ID, a.POEM_ID, a.POEMS_ID , a.DEPARTMENT_ID
				   from 		PER_PERSONAL  a, PER_LEVEL b
				   where 	PER_ID=$SESS_PER_ID and a.LEVEL_NO=b.LEVEL_NO ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PER_ID = $SESS_PER_ID;
		$PN_CODE = trim($data[PN_CODE]);
		$PER_NAME = trim($data[PER_NAME]) ." ". trim($data[PER_SURNAME]);
		$PER_CARDNO = trim($data[PER_CARDNO]);
		$LEVEL_NO = trim($data[LEVEL_NO]);
		$LEVEL_NAME = trim($data[LEVEL_NAME]);
		$POSITION_LEVEL = $data[POSITION_LEVEL];
		if (!$POSITION_LEVEL) $POSITION_LEVEL = $LEVEL_NAME;
		$PER_SALARY = $data[PER_SALARY];
		$PER_TYPE = $data[PER_TYPE];
		$PER_ID_DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);
		
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
			$PL_NAME = trim($PL_NAME)?($PL_NAME . $POSITION_LEVEL . ((trim($PT_NAME) != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"")):" ".$LEVEL_NAME;
		}elseif($PER_TYPE == 2){
			$POEM_ID = $data[POEM_ID];
			$cmd = " select 	b.PN_NAME, c.ORG_NAME 
							 from 		PER_POS_EMP a, PER_POS_NAME b, PER_ORG c
							 where	a.POEM_ID=$POEM_ID and a.PN_CODE=b.PN_CODE and a.ORG_ID=c.ORG_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$PL_NAME = trim($data[PN_NAME]);
			$ORG_NAME = trim($data[ORG_NAME]);
		}elseif($PER_TYPE==3){
			$POEMS_ID = $data[POEMS_ID];
			$cmd = " select 	b.EP_NAME, c.ORG_NAME 
							 from 		PER_POS_EMPSER a, PER_EMPSER_POS_NAME b, PER_ORG c
							 where	a.POEMS_ID=$POEMS_ID and a.EP_CODE=b.EP_CODE and a.ORG_ID=c.ORG_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$PL_NAME = trim($data[EP_NAME]);
			$ORG_NAME = trim($data[ORG_NAME]);
		} // end if
		
		$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='$PN_CODE' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PER_NAME = trim($data[PN_NAME]).$PER_NAME;

		$cmd = " select LEVEL_NAME from PER_LEVEL where trim(LEVEL_NO)='$LEVEL_NO' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$LEVEL_NAME = $data[LEVEL_NAME];
	}
	
	if(!isset($search_aa_year)){
		if(date("Y-m-d") <= date("Y")."-10-01") $search_aa_year = date("Y") + 543;
		else $search_aa_year = (date("Y") + 543) + 1;
	} // end if
	
	if (!$AA_CYCLE) 
		if (substr($UPDATE_DATE,5,2) > "09" || substr($UPDATE_DATE,5,2) < "04") $AA_CYCLE = 1;
		elseif (substr($UPDATE_DATE,5,2) > "03" && substr($UPDATE_DATE,5,2) < "10") $AA_CYCLE = 2;

	if($AA_START_DATE_1){
		$AA_CYCLE = 1;
		$AA_START_DATE =  save_date($AA_START_DATE_1);
		$AA_END_DATE =  save_date($AA_END_DATE_1);
	}elseif($AA_START_DATE_2){
		$AA_CYCLE = 2;
		$AA_START_DATE =  save_date($AA_START_DATE_2);
		$AA_END_DATE =  save_date($AA_END_DATE_2);
	} // end if
	
	if(!$PER_ID_APPROVE) $PER_ID_APPROVE = "NULL";
	if(!$PER_ID_APPROVE1) $PER_ID_APPROVE1 = "NULL";
	if(!$PER_ID_APPROVE2) $PER_ID_APPROVE2 = "NULL";
	
	if($command == "ADD" && trim($AA_YEAR) && trim($AA_CYCLE) && trim($PER_ID)){
		$cmd = " select		PER_TYPE, POS_ID, POEM_ID, POEMS_ID, POT_ID, ORG_ID, PER_ID_REF, PER_ID_ASS_REF
						 from			PER_PERSONAL
						 where		PER_ID = $PER_ID ";		
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$PER_TYPE = $data[PER_TYPE];
		if($PER_TYPE == 1){
			$POS_ID = $data[POS_ID];
			$table = "PER_POSITION";
			$field = "POS_ID";
		}elseif($PER_TYPE == 2){
			$POS_ID = $data[POEM_ID];
			$table = "PER_POS_EMP";
			$field = "POEM_ID";
		}elseif($PER_TYPE == 3){
			$POS_ID = $data[POEMS_ID]; 
			$table = "PER_POS_EMPSER";
			$field = "POEMS_ID";
		}elseif($PER_TYPE == 4){
			$POS_ID = $data[POT_ID];
			$table = "PER_POS_TEMP";
			$field = "POT_ID";
		}
		$ORG_ID_ASS = $data[ORG_ID];
		$PER_ID_REF = $data[PER_ID_REF];
		$PER_ID_ASS_REF = $data[PER_ID_ASS_REF];

		$cmd = " select	ORG_ID, ORG_ID_1 from $table where $field = $POS_ID ";		
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$ORG_ID = $data[ORG_ID];
		$ORG_ID_1 = $data[ORG_ID_1];
		if (!$ORG_ID_1) $ORG_ID_1 = "NULL";
		if (!$ORG_ID_ASS) $ORG_ID_ASS = "NULL";
		if (!$PER_ID_REF) $PER_ID_REF = "NULL";
		if (!$PER_ID_ASS_REF) $PER_ID_ASS_REF = "NULL";
			
		if($DPISDB == "odbc"){
			$cmd = " select 	AA_END_DATE, AA_CYCLE, PER_ID
					  		 from 		PER_ABSENT_APPROVE 
							 where 	LEFT(AA_END_DATE, 10) = '$AA_END_DATE' and  AA_CYCLE=$AA_CYCLE and PER_ID = $PER_ID ";
		}elseif($DPISDB == "oci8"){
			$cmd = " select 	AA_END_DATE, AA_CYCLE, PER_ID
					  		 from 		PER_ABSENT_APPROVE 
							 where 	SUBSTR(AA_END_DATE, 1, 10) = '$AA_END_DATE' and  AA_CYCLE=$AA_CYCLE and PER_ID = $PER_ID ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select 	AA_END_DATE, AA_CYCLE, PER_ID
					  		 from 		PER_ABSENT_APPROVE 
							 where 	LEFT(AA_END_DATE, 10) = '$AA_END_DATE' and  AA_CYCLE=$AA_CYCLE and PER_ID = $PER_ID ";
		} // end if
		$count_duplicate = $db_dpis->send_cmd($cmd);
		if($count_duplicate <= 0){
			$cmd = " select max(AA_ID) as max_id from PER_ABSENT_APPROVE ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$AA_ID = $data[max_id] + 1;
			
			$cmd = " insert into PER_ABSENT_APPROVE (AA_ID, PER_ID, PER_CARDNO, AA_CYCLE, AA_START_DATE, AA_END_DATE, 
							PER_ID_APPROVE, PER_ID_APPROVE1, PER_ID_APPROVE2, DEPARTMENT_ID, UPDATE_USER, UPDATE_DATE) 
							values ($AA_ID, $PER_ID, '$PER_CARDNO', $AA_CYCLE, '$AA_START_DATE', '$AA_END_DATE', 	$PER_ID_APPROVE, 
							$PER_ID_APPROVE1, $PER_ID_APPROVE2, $DEPARTMENT_ID, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();

			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [".$AA_ID." : ".trim($AA_YEAR)." : ".$AA_CYCLE." : ".$PER_NAME."]");

		}else{
			$data = $db_dpis->get_array();
			$AA_END_DATE = substr($data[AA_END_DATE], 0, 10);
			$AA_YEAR = substr($AA_END_DATE, 0, 4) + 543;
			$AA_CYCLE = $data[AA_CYCLE];
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

			$err_text = "รหัสข้อมูลซ้ำ [ปีงบประมาณ ".$AA_YEAR." ครั้งที่ ".$AA_CYCLE." ".$PER_FULLNAME."]";

			$AA_START_DATE_1 = "01/10/". ($AA_YEAR - 1);
			$AA_END_DATE_1 = "31/03/". $AA_YEAR;
			$AA_START_DATE_2 = "01/04/". $AA_YEAR;
			$AA_END_DATE_2 = "30/09/". $AA_YEAR;
		} // endif
	}

	if($command == "UPDATE" && trim($AA_ID)  && trim($AA_YEAR) && trim($AA_CYCLE) && trim($PER_ID)){
		if($DPISDB == "odbc"){
			$cmd = " select 	AA_END_DATE, AA_CYCLE, PER_ID
					  		 from 		PER_ABSENT_APPROVE 
							 where 	LEFT(AA_END_DATE, 10) = '$AA_END_DATE' and  AA_CYCLE=$AA_CYCLE and PER_ID = $PER_ID and AA_ID <> $AA_ID ";
		}elseif($DPISDB == "oci8"){
			$cmd = " select 	AA_END_DATE, AA_CYCLE, PER_ID
					  		 from 		PER_ABSENT_APPROVE 
							 where 	SUBSTR(AA_END_DATE, 1, 10) = '$AA_END_DATE' and  AA_CYCLE=$AA_CYCLE and PER_ID = $PER_ID and AA_ID <> $AA_ID ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select 	AA_END_DATE, AA_CYCLE, PER_ID
					  		 from 		PER_ABSENT_APPROVE 
							 where 	LEFT(AA_END_DATE, 10) = '$AA_END_DATE' and  AA_CYCLE=$AA_CYCLE and PER_ID = $PER_ID and AA_ID <> $AA_ID ";
		} // end if
		$count_duplicate = $db_dpis->send_cmd($cmd);
		if($count_duplicate <= 0){
			$cmd = " update PER_ABSENT_APPROVE set 
								AA_CYCLE=$AA_CYCLE,
								AA_START_DATE='".$AA_START_DATE."', 
								AA_END_DATE='".$AA_END_DATE."', 
								PER_ID=$PER_ID, 
								PER_CARDNO='".$PER_CARDNO."', 
								PER_ID_APPROVE=$PER_ID_APPROVE,
								PER_ID_APPROVE1=$PER_ID_APPROVE1,
								PER_ID_APPROVE2=$PER_ID_APPROVE2,
								UPDATE_USER=$SESS_USERID, 
								UPDATE_DATE='$UPDATE_DATE' 
							 where AA_ID=$AA_ID ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
	
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [".$AA_ID." : ".trim($AA_YEAR)." : ".$AA_CYCLE." : ".$PER_NAME."]");
		}else{
			$data = $db_dpis->get_array();
			$AA_END_DATE = substr($data[AA_END_DATE], 0, 10);
			$AA_YEAR = substr($AA_END_DATE, 0, 4) + 543;
			$AA_CYCLE = $data[AA_CYCLE];
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

			$err_text = "รหัสข้อมูลซ้ำ [".$AA_YEAR." ".$AA_CYCLE." ".$PER_FULLNAME."]";
			
			$AA_START_DATE_1 = "01/10/". ($AA_YEAR - 1);
			$AA_END_DATE_1 = "31/03/". $AA_YEAR;
			$AA_START_DATE_2 = "01/04/". $AA_YEAR;
			$AA_END_DATE_2 = "30/09/". $AA_YEAR;			
		} // end if
	}
	
	if($command == "DELETE" && trim($AA_ID)){
		$cmd = " select 	AA_END_DATE, AA_CYCLE, PER_ID
				  		 from 		PER_ABSENT_APPROVE 
						 where 	AA_ID = $AA_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$AA_END_DATE = substr($data[AA_END_DATE], 0, 10);
		$AA_YEAR = substr($AA_END_DATE, 0, 4);
		$AA_CYCLE = $data[AA_CYCLE];
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
		
		$cmd = " delete from PER_ABSENT_APPROVE where AA_ID=$AA_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [". $AA_ID ." : ".$AA_YEAR." ".$AA_CYCLE." ".$PER_FULLNAME."]");
	}
	//กำหนดค่าเริ่มต้นเมื่อเข้ามาเป็นคนนั้น จาก person_master_frame
	//if($_REQUEST[PER_ID]){ $PER_ID=$_REQUEST[PER_ID]; if(!trim($UPD)) $VIEW=1;  }
	//echo "sperid : ".$SESS_PER_ID." perid : ".$PER_ID;
	if($UPD || $VIEW){	// ดู / แก้ไข
		$cmd = " select 	AA_CYCLE, AA_START_DATE, AA_END_DATE, PER_ID, PER_CARDNO, PER_ID_APPROVE, 
						PER_ID_APPROVE1, PER_ID_APPROVE2
				  		 from 		PER_ABSENT_APPROVE
						 where 	AA_ID=$AA_ID ";
//		echo "<br>>> $cmd<br>";	
		$count = $db_dpis->send_cmd($cmd);
		if($count >0){
			$data = $db_dpis->get_array();
			$AA_CYCLE = trim($data[AA_CYCLE]);
			if($AA_CYCLE==1){	//ตรวจสอบรอบการลา
				$AA_START_DATE_1 = show_date_format($data[AA_START_DATE], 1);
				$AA_END_DATE_1 = show_date_format($data[AA_END_DATE], 1);
				$AA_YEAR = substr($AA_END_DATE_1, 6, 4);
			}else if($AA_CYCLE==2){
				$AA_START_DATE_2 = show_date_format($data[AA_START_DATE], 1);
				$AA_END_DATE_2 = show_date_format($data[AA_END_DATE], 1);
				$AA_YEAR = substr($AA_END_DATE_2, 6, 4);
			}

			$PER_ID = $data[PER_ID];
			$PER_CARDNO = trim($data[PER_CARDNO]);		
			$PER_ID_APPROVE = $data[PER_ID_APPROVE];
			$PER_ID_APPROVE1 = $data[PER_ID_APPROVE1];
			$PER_ID_APPROVE2 = $data[PER_ID_APPROVE2];
		}else{ //เข้ามา รายละเอียดข้าราชการ/ลูกจ้าง **ไม่มี AA_ID ระบุ จะกำหนดค่าเริ่มต้นของปีงบประมาณ / รอบการลา
			if(!$AA_YEAR){
				if(date("Y-m-d") <= date("Y")."-10-01") $AA_YEAR = date("Y") + 543;
				else $AA_YEAR = (date("Y") + 543) + 1;
			}
			$AA_START_DATE_1 = "01/10/". ($AA_YEAR - 1);
			$AA_END_DATE_1 = "31/03/". $AA_YEAR;
			$AA_START_DATE_2 = "01/04/". $AA_YEAR;
			$AA_END_DATE_2 = "30/09/". $AA_YEAR;
		}
		//echo $AA_CYCLE." $AA_YEAR : (".$AA_START_DATE_1."-".$AA_END_DATE_1.") + (".$AA_START_DATE_2."-".$AA_END_DATE_2.") ";

		// ถ้าไม่มี PER_ID จาก PER_ABSENT_APPROVE เอามาจาก GET / POST
		$cmd = " select 	PN_CODE,PER_CARDNO, PER_NAME, PER_SURNAME, LEVEL_NO, PER_SALARY, PER_TYPE, POS_ID, POEM_ID, POEMS_ID
						 from		PER_PERSONAL
						 where	PER_ID=$PER_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PN_CODE = trim($data[PN_CODE]);
		if(!trim($PER_CARDNO)) $PER_CARDNO = trim($data[PER_CARDNO]);
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
			$PL_NAME = trim($PL_NAME)?($PL_NAME . $POSITION_LEVEL . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"")):" ".$LEVEL_NAME;
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

		$cmd = " select 	PN_CODE, PER_NAME, PER_SURNAME, PER_TYPE, POS_ID, POEM_ID, POEMS_ID, LEVEL_NO
						 from		PER_PERSONAL
						 where	PER_ID=$PER_ID_APPROVE ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();	
		$data = $db_dpis->get_array();
		$APPROVE_PN_CODE = trim($data[PN_CODE]);
		$APPROVE_PER_NAME = trim($data[PER_NAME]);
		$APPROVE_PER_SURNAME = trim($data[PER_SURNAME]);
		$APPROVE_PER_TYPE = trim($data[PER_TYPE]);
		$APPROVE_POS_ID = trim($data[POS_ID]);
		$APPROVE_POEM_ID = trim($data[POEM_ID]);
		$APPROVE_POEMS_ID = trim($data[POEMS_ID]);
		$APPROVE_LEVEL_NO = trim($data[LEVEL_NO]);

		$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='$APPROVE_PN_CODE' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();	
		$data = $db_dpis->get_array();
		$APPROVE_PN_NAME = trim($data[PN_NAME]);
		
		$cmd = " select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$APPROVE_LEVEL_NO' ";
		$db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();	
		$data2 = $db_dpis2->get_array();
		$APPROVE_LEVEL_NAME = $data2[LEVEL_NAME];
		$APPROVE_POSITION_LEVEL = $data2[POSITION_LEVEL];
		if (!$APPROVE_POSITION_LEVEL) $APPROVE_POSITION_LEVEL = $APPROVE_LEVEL_NAME;
		
		$APPROVE_PER_NAME = $APPROVE_PN_NAME . $APPROVE_PER_NAME . " " . $APPROVE_PER_SURNAME;
		
		if($APPROVE_PER_TYPE==1){
			$cmd = " select 	b.PL_NAME, a.PT_CODE
							 from 		PER_POSITION a, PER_LINE b
							 where	a.POS_ID=$APPROVE_POS_ID and a.PL_CODE=b.PL_CODE ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();	
			$data = $db_dpis->get_array();
			$APPROVE_PL_NAME = trim($data[PL_NAME]);
			$APPROVE_PT_CODE = trim($data[PT_CODE]);
			$cmd = "	select PT_NAME from PER_TYPE where PT_CODE='$APPROVE_PT_CODE'";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$APPROVE_PT_NAME = trim($data2[PT_NAME]);
			if ($RPT_N)
			    $APPROVE_PL_NAME = (trim($APPROVE_PM_NAME) ?"$APPROVE_PM_NAME (":"") . (trim($APPROVE_PL_NAME)? "$APPROVE_PL_NAME$APPROVE_POSITION_LEVEL" : "") . (trim($APPROVE_PM_NAME) ?")":"");
			else
				$APPROVE_PL_NAME = (trim($APPROVE_PM_NAME) ?"$APPROVE_PM_NAME (":"") . (trim($APPROVE_PL_NAME)?($APPROVE_PL_NAME ." ". level_no_format($APPROVE_LEVEL_NO) . (($APPROVE_PT_NAME != "ทั่วไป" && $APPROVE_LEVEL_NO >= 6)?"$APPROVE_PT_NAME":"")):"") . (trim($APPROVE_PM_NAME) ?")":"");
		}elseif($APPROVE_PER_TYPE==2){
			$cmd = " select 	b.PN_NAME
							 from 		PER_POS_EMP a, PER_POS_NAME b
							 where	a.POEM_ID=$APPROVE_POEM_ID and a.PN_CODE=b.PN_CODE ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();	
			$data = $db_dpis->get_array();
			$APPROVE_PL_NAME = trim($data[PN_NAME]);
		}elseif($APPROVE_PER_TYPE==3){
			$cmd = " select 	b.EP_NAME
							 from 		PER_POS_EMPSER a, PER_EMPSER_POS_NAME b
							 where	a.POEMS_ID=$APPROVE_POEMS_ID and a.EP_CODE=b.EP_CODE ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();	
			$data = $db_dpis->get_array();
			$APPROVE_PL_NAME = trim($data[EP_NAME]);
		} // end if

		$cmd = " select 	PN_CODE, PER_NAME, PER_SURNAME, PER_TYPE, POS_ID, POEM_ID, POEMS_ID, LEVEL_NO
						 from		PER_PERSONAL
						 where	PER_ID=$PER_ID_APPROVE1 ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();	
		$data = $db_dpis->get_array();
		$APPROVE_PN_CODE1 = trim($data[PN_CODE]);
		$APPROVE_PER_NAME1 = trim($data[PER_NAME]);
		$APPROVE_PER_SURNAME1 = trim($data[PER_SURNAME]);
		$APPROVE_PER_TYPE1 = trim($data[PER_TYPE]);
		$APPROVE_POS_ID1 = trim($data[POS_ID]);
		$APPROVE_POEM_ID1 = trim($data[POEM_ID]);
		$APPROVE_POEMS_ID1 = trim($data[POEMS_ID]);
		$APPROVE_LEVEL_NO1 = trim($data[LEVEL_NO]);

		$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='$APPROVE_PN_CODE1' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();		
		$data = $db_dpis->get_array();
		$APPROVE_PN_NAME1 = trim($data[PN_NAME]);
		
		$cmd = " select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$APPROVE_LEVEL_NO1' ";
		$db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();		
		$data2 = $db_dpis2->get_array();
		$APPROVE_LEVEL_NAME1 = $data2[LEVEL_NAME];
		$APPROVE_POSITION_LEVEL1 = $data2[POSITION_LEVEL];
		if (!$APPROVE_POSITION_LEVEL1) $APPROVE_POSITION_LEVEL1 = $APPROVE_LEVEL_NAME1;
		
		$APPROVE_PER_NAME1 = $APPROVE_PN_NAME1 . $APPROVE_PER_NAME1 . " " . $APPROVE_PER_SURNAME1;
		
		if($APPROVE_PER_TYPE1==1){
			$cmd = " select 	b.PL_NAME, a.PT_CODE 
							 from 		PER_POSITION a, PER_LINE b
							 where	a.POS_ID=$APPROVE_POS_ID1 and a.PL_CODE=b.PL_CODE ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();	
			$data = $db_dpis->get_array();
			$APPROVE_PL_NAME1 = trim($data[PL_NAME]);
			$APPROVE_PT_CODE1 = trim($data[PT_CODE]);
			$cmd = "	select PT_NAME from PER_TYPE where PT_CODE='$APPROVE_PT_CODE1'";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$APPROVE_PT_NAME1 = trim($data2[PT_NAME]);
			if ($RPT_N)
			    $APPROVE_PL_NAME1 = (trim($APPROVE_PM_NAME1) ?"$APPROVE_PM_NAME1 (":"") . (trim($APPROVE_PL_NAME1)? "$APPROVE_PL_NAME1$APPROVE_POSITION_LEVEL1" : "") . (trim($APPROVE_PM_NAME1) ?")":"");
			else
				$APPROVE_PL_NAME1 = (trim($APPROVE_PM_NAME1) ?"$APPROVE_PM_NAME1 (":"") . (trim($APPROVE_PL_NAME1)?($APPROVE_PL_NAME1 ." ". level_no_format($APPROVE_LEVEL_NO1) . (($APPROVE_PT_NAME1 != "ทั่วไป" && $APPROVE_LEVEL_NO1 >= 6)?"$APPROVE_PT_NAME1":"")):"") . (trim($APPROVE_PM_NAME1) ?")":"");
		}elseif($APPROVE_PER_TYPE1==2){
			$cmd = " select 	b.PN_NAME
							 from 		PER_POS_EMP a, PER_POS_NAME b
							 where	a.POEM_ID=$APPROVE_POEM_ID1 and a.PN_CODE=b.PN_CODE ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();				
			$data = $db_dpis->get_array();
			$APPROVE_PL_NAME1 = trim($data[PN_NAME]);
		}elseif($APPROVE_PER_TYPE1==3){
			$cmd = " select 	b.EP_NAME
							 from 		PER_POS_EMPSER a, PER_EMPSER_POS_NAME b
							 where	a.POEMS_ID=$APPROVE_POEMS_ID1 and a.EP_CODE=b.EP_CODE ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$APPROVE_PL_NAME1 = trim($data[EP_NAME]);
		} // end if

		$cmd = " select 	PN_CODE, PER_NAME, PER_SURNAME, PER_TYPE, POS_ID, POEM_ID, POEMS_ID, LEVEL_NO
						 from		PER_PERSONAL
						 where	PER_ID=$PER_ID_APPROVE2 ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();		
		$data = $db_dpis->get_array();
		$APPROVE_PN_CODE2 = trim($data[PN_CODE]);
		$APPROVE_PER_NAME2 = trim($data[PER_NAME]);
		$APPROVE_PER_SURNAME2 = trim($data[PER_SURNAME]);
		$APPROVE_PER_TYPE2 = trim($data[PER_TYPE]);
		$APPROVE_POS_ID2 = trim($data[POS_ID]);
		$APPROVE_POEM_ID2 = trim($data[POEM_ID]);
		$APPROVE_POEMS_ID2 = trim($data[POEMS_ID]);
		$APPROVE_LEVEL_NO2 = trim($data[LEVEL_NO]);

		$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='$APPROVE_PN_CODE2' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();		
		$data = $db_dpis->get_array();
		$APPROVE_PN_NAME2 = trim($data[PN_NAME]);
		
		$cmd = " select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$APPROVE_LEVEL_NO2' ";
		$db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();	
		$data2 = $db_dpis2->get_array();
		$APPROVE_LEVEL_NAME2 = $data2[LEVEL_NAME];
		$APPROVE_POSITION_LEVEL2 = $data2[POSITION_LEVEL];
		if (!$APPROVE_POSITION_LEVEL2) $APPROVE_POSITION_LEVEL2 = $APPROVE_LEVEL_NAME2;
		
		$APPROVE_PER_NAME2 = $APPROVE_PN_NAME2 . $APPROVE_PER_NAME2 . " " . $APPROVE_PER_SURNAME2;
		
		if($APPROVE_PER_TYPE2==1){
			$cmd = " select 	b.PL_NAME, a.PT_CODE
							 from 		PER_POSITION a, PER_LINE b
							 where	a.POS_ID=$APPROVE_POS_ID2 and a.PL_CODE=b.PL_CODE ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();	
			$data = $db_dpis->get_array();
			$APPROVE_PL_NAME2 = trim($data[PL_NAME]);
			$APPROVE_PT_CODE2 = trim($data[PT_CODE]);
			$cmd = "	select PT_NAME from PER_TYPE where PT_CODE='$APPROVE_PT_CODE2'";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$APPROVE_PT_NAME2 = trim($data2[PT_NAME]);
			if ($RPT_N)
			    $APPROVE_PL_NAME2 = (trim($APPROVE_PM_NAME2) ?"$APPROVE_PM_NAME2 (":"") . (trim($APPROVE_PL_NAME2)? "$APPROVE_PL_NAME2$APPROVE_POSITION_LEVEL2" : "") . (trim($APPROVE_PM_NAME2) ?")":"");
			else
				$APPROVE_PL_NAME2 = (trim($APPROVE_PM_NAME2) ?"$APPROVE_PM_NAME2 (":"") . (trim($APPROVE_PL_NAME2)?($APPROVE_PL_NAME2 ." ". level_no_format($APPROVE_LEVEL_NO2) . (($APPROVE_PT_NAME2 != "ทั่วไป" && $APPROVE_LEVEL_NO2 >= 6)?"$APPROVE_PT_NAME2":"")):"") . (trim($APPROVE_PM_NAME2) ?")":"");
		}elseif($APPROVE_PER_TYPE2==2){
			$cmd = " select 	b.PN_NAME
							 from 		PER_POS_EMP a, PER_POS_NAME b
							 where	a.POEM_ID=$APPROVE_POEM_ID2 and a.PN_CODE=b.PN_CODE ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();				
			$data = $db_dpis->get_array();
			$APPROVE_PL_NAME2 = trim($data[PN_NAME]);
		}elseif($APPROVE_PER_TYPE2==3){
			$cmd = " select 	b.EP_NAME
							 from 		PER_POS_EMPSER a, PER_EMPSER_POS_NAME b
							 where	a.POEMS_ID=$APPROVE_POEMS_ID2 and a.EP_CODE=b.EP_CODE ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();				
			$data = $db_dpis->get_array();
			$APPROVE_PL_NAME2 = trim($data[EP_NAME]);
		} // end if

		$AA_START_DATE_1 = "01/10/". ($AA_YEAR - 1);
		$AA_END_DATE_1 = "31/03/". $AA_YEAR;
		$AA_START_DATE_2 = "01/04/". $AA_YEAR;
		$AA_END_DATE_2 = "30/09/". $AA_YEAR;
	} // end if
	
	if($command == "GENDATA" && trim($AA_YEAR) && trim($AA_CYCLE) && trim($search_per_type)){
		if (($CTRL_TYPE==2 || $CTRL_TYPE==3) && $SESS_USERID==1 && !$DEPARTMENT_ID) $where = "";
		else $where = "and DEPARTMENT_ID = $DEPARTMENT_ID";
		$cmd = " delete from PER_ABSENT_APPROVE where AA_CYCLE = $AA_CYCLE and AA_START_DATE = '$AA_START_DATE' and 
						AA_END_DATE = '$AA_END_DATE' and PER_ID in (select PER_ID from PER_PERSONAL where PER_TYPE = $search_per_type) $where ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " select max(AA_ID) as max_id from PER_ABSENT_APPROVE ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$AA_ID = $data[max_id] + 1;
			
		$cmd = " select		PER_ID, PER_CARDNO, PER_ID_REF, PER_ID_ASS_REF, PER_TYPE, 
											POS_ID, POEM_ID, POEMS_ID, PAY_ID, ORG_ID, LEVEL_NO, DEPARTMENT_ID
						 from			PER_PERSONAL
						 where		PER_TYPE = $search_per_type and PER_STATUS=1 $where 
						 order by	PER_ID ";		
		$db_dpis1->send_cmd($cmd);
//		$db_dpis1->show_error();
//		echo "$cmd<br>";
		while($data1 = $db_dpis1->get_array()){
			$PER_ID = $data1[PER_ID];
			$PER_CARDNO = $data1[PER_CARDNO];
			$PER_ID_REF = $data1[PER_ID_REF];
			$PER_ID_ASS_REF = $data1[PER_ID_ASS_REF];
			if (!$PER_ID_REF) $PER_ID_REF = "NULL";
			$PER_TYPE = $data1[PER_TYPE];
			if($PER_TYPE == 1) $POS_ID = $data1[POS_ID];
			elseif($PER_TYPE == 2) $POS_ID = $data1[POEM_ID];
			elseif($PER_TYPE == 3) $POS_ID = $data1[POEMS_ID];
			$PAY_ID = $data1[PAY_ID];
			$ORG_ID_ASS = $data1[ORG_ID];
			if (!$ORG_ID_ASS) $ORG_ID_ASS = "NULL";
			$LEVEL_NO = $data1[LEVEL_NO];
			$DEPARTMENT_ID = $data1[DEPARTMENT_ID];
			if($PER_TYPE==1) 
				if ($SESS_DEPARTMENT_NAME=="กรมการปกครอง")
					$cmd = " select	ORG_ID, ORG_ID_1 from PER_POSITION where POS_ID = $PAY_ID ";		
				else
					$cmd = " select	ORG_ID, ORG_ID_1 from PER_POSITION where POS_ID = $POS_ID ";		
			elseif($PER_TYPE==2) 
				$cmd = " select	ORG_ID, ORG_ID_1 from PER_POS_EMP where POEM_ID = $POS_ID ";		
			elseif($PER_TYPE==3) 
				$cmd = " select	ORG_ID, ORG_ID_1 from PER_POS_EMPSER where POEMS_ID = $POS_ID ";		
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$ORG_ID = $data[ORG_ID];
			$ORG_ID_1 = $data[ORG_ID_1];
			if (!$ORG_ID_1) $ORG_ID_1 = "NULL";
			
			$cmd = " insert into PER_ABSENT_APPROVE (AA_ID, PER_ID, PER_CARDNO, AA_CYCLE, AA_START_DATE, AA_END_DATE, 
							PER_ID_APPROVE, PER_ID_APPROVE1, PER_ID_APPROVE2, DEPARTMENT_ID, UPDATE_USER, UPDATE_DATE) 
							values ($AA_ID, $PER_ID, '$PER_CARDNO', $AA_CYCLE, '$AA_START_DATE', '$AA_END_DATE', 	$PER_ID_REF, 
							$PER_ID_REF, $PER_ID_REF, $DEPARTMENT_ID, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			$AA_ID++;
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [".$AA_ID." : ".trim($AA_YEAR)." : ".$AA_CYCLE." : ".$PER_CARDNO."]");

		} // end while
	}

	if( (!$UPD && !$DEL && !$VIEW && !$err_text) ){
		$AA_ID = "";
		if(date("Y-m-d") <= date("Y")."-10-01") $AA_YEAR = date("Y") + 543;
		else $AA_YEAR = (date("Y") + 543) + 1;
		$AA_START_DATE_1 = "01/10/". ($AA_YEAR - 1);
		$AA_END_DATE_1 = "31/03/". $AA_YEAR;
		$AA_START_DATE_2 = "01/04/". $AA_YEAR;
		$AA_END_DATE_2 = "30/09/". $AA_YEAR;
//		$AA_CYCLE = "";

		if($SESS_GROUPCODE != "BUREAU" && substr($SESS_GROUPCODE, 0, 7) != "BUREAU_"  && $SESS_USERGROUP!=3){
			$PER_ID = "";
			$PER_CARDNO = "";
			$PER_NAME = "";
			$PL_NAME = "";
			$LEVEL_NAME = "";
			$ORG_NAME = "";
			$PER_SALARY = "";
		} // end if

		$PER_ID_APPROVE = "";
		$APPROVE_PER_NAME = "";
		$APPROVE_PL_NAME = "";

		$PER_ID_APPROVE1 = "";
		$APPROVE_PER_NAME1 = "";
		$APPROVE_PL_NAME1 = "";

		$PER_ID_APPROVE2 = "";
		$APPROVE_PER_NAME2 = "";
		$APPROVE_PL_NAME2 = "";

		$search_per_type = "";
	} // end if
?>