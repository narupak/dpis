<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");	
	include("php_scripts/load_per_control.php");

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	switch($CTRL_TYPE){
		case 2 :
			$REQ_PV_CODE = $PV_CODE;
			$REQ_PV_NAME = $PV_NAME;
			break;
		case 3 :
			$REQ_MINISTRY_ID = $MINISTRY_ID;
			$REQ_MINISTRY_NAME = $MINISTRY_NAME;
			break;
		case 4 :
			$REQ_MINISTRY_ID = $MINISTRY_ID;
			$REQ_MINISTRY_NAME = $MINISTRY_NAME;
			$REQ_DEPARTMENT_ID = $DEPARTMENT_ID;
			$REQ_DEPARTMENT_NAME = $DEPARTMENT_NAME;
			break;
	} // end switch case

	switch($SESS_USERGROUP_LEVEL){
		case 2 :
			$REQ_PV_CODE = $PV_CODE;
			$REQ_PV_NAME = $PV_NAME;
			break;
		case 3 :
			$REQ_MINISTRY_ID = $MINISTRY_ID;
			$REQ_MINISTRY_NAME = $MINISTRY_NAME;
			break;
		case 4 :
			$REQ_MINISTRY_ID = $MINISTRY_ID;
			$REQ_MINISTRY_NAME = $MINISTRY_NAME;
			$REQ_DEPARTMENT_ID = $DEPARTMENT_ID;
			$REQ_DEPARTMENT_NAME = $DEPARTMENT_NAME;
			break;
		case 5 :
			$REQ_MINISTRY_ID = $MINISTRY_ID;
			$REQ_MINISTRY_NAME = $MINISTRY_NAME;
			$REQ_DEPARTMENT_ID = $DEPARTMENT_ID;
			$REQ_DEPARTMENT_NAME = $DEPARTMENT_NAME;
			$REQ_ORG_ID = $ORG_ID;
			$REQ_ORG_NAME = $ORG_NAME;
			break;
	} // end switch case

	if($MINISTRY_ID && !trim($MINISTRY_NAME)){
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MINISTRY_NAME = $data[ORG_NAME];

		$REQ_MINISTRY_ID = $MINISTRY_ID;
		$REQ_MINISTRY_NAME = $MINISTRY_NAME;
	} // end if

	if($DEPARTMENT_ID && !trim($DEPARTMENT_NAME)){
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$DEPARTMENT_NAME = $data[ORG_NAME];

		$REQ_DEPARTMENT_ID = $DEPARTMENT_ID;
		$REQ_DEPARTMENT_NAME = $DEPARTMENT_NAME;
	} // end if

	$UPDATE_DATE = date("Y-m-d H:i:s");
	
	if(trim($REQ_RESULT) == "") $REQ_RESULT = "NULL";
	if(trim($REQ_RESULT) != 1) $REQ_EFFECTIVE = "NULL";
	if(trim($REQ_ORG_ID_1) == "") $REQ_ORG_ID_1 = "NULL";
	if(trim($REQ_ORG_ID_2) == "") $REQ_ORG_ID_2 = "NULL";
	if(trim($REQ_ORG_ID_3) == "") $REQ_ORG_ID_3 = "NULL";
	if(trim($REQ_ORG_ID_4) == "") $REQ_ORG_ID_4 = "NULL";
	if(trim($REQ_ORG_ID_5) == "") $REQ_ORG_ID_5 = "NULL";
	$REQ_SALARY += 0;
	$REQ_MGTSALARY += 0;

	if(trim($REQ_EFF_DATE)){
		$arr_temp = explode("/", $REQ_EFF_DATE);
		$REQ_EFF_DATE = ($arr_temp[2] - 543) ."-". $arr_temp[1] ."-". $arr_temp[0];
	} // end if

	if($command == "ADD" && trim($REQ_SEQ) && trim($REQ_POS_NO)){
		$cmd = " select 	REQ_SEQ, REQ_POS_NO
				  		 from 		PER_REQ1_DTL1
						 where 	REQ_ID=$REQ_ID and trim(REQ_POS_NO)='". trim($REQ_POS_NO) ."' ";
		$count_duplicate = $db_dpis->send_cmd($cmd);
		if($count_duplicate <= 0){
			$cmd = " insert into PER_REQ1_DTL1 (REQ_ID, REQ_SEQ, REQ_POS_NO, REQ_EFF_DATE, REQ_RESULT, 
							REQ_EFFECTIVE, REQ_CONFIRM, OT_CODE, PL_CODE, PM_CODE, CL_NAME, PT_CODE, SKILL_CODE, PC_CODE, 
							REQ_CONDITION, REQ_SALARY, REQ_MGTSALARY, ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5,
							UPDATE_USER, UPDATE_DATE)
							values ($REQ_ID, $REQ_SEQ, '".trim($REQ_POS_NO)."', '".trim($REQ_EFF_DATE)."', $REQ_RESULT, $REQ_EFFECTIVE, 0,
							'$REQ_OT_CODE', '$REQ_PL_CODE', '$REQ_PM_CODE', '$REQ_CL_NAME', '$REQ_PT_CODE', '$REQ_SKILL_CODE', '$REQ_PC_CODE',
							'$REQ_CONDITION', $REQ_SALARY, $REQ_MGTSALARY, $REQ_ORG_ID, $REQ_ORG_ID_1, $REQ_ORG_ID_2, 
							$REQ_ORG_ID_3, $REQ_ORG_ID_4, $REQ_ORG_ID_5, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();

			$cmd = " insert into PER_REQ1_DTL2
							 (REQ_ID, REQ_SEQ, POS_ID, UPDATE_USER, UPDATE_DATE)
							 values
							 ($REQ_ID, $REQ_SEQ, $POS_ID, $SESS_USERID, '$UPDATE_DATE')
						  ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();

			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [".trim($REQ_SEQ)." : ".$REQ_POS_NO." : ".$REQ_PL_NAME."]");
		}else{
			$data = $db_dpis->get_array();			
			$err_text = "รหัสข้อมูลซ้ำ [".$data[REQ_SEQ]." ".$data[REQ_POS_NO]."]";

			if(trim($REQ_EFF_DATE)){
				$arr_temp = explode("-", $REQ_EFF_DATE);
				$REQ_EFF_DATE = ($arr_temp[0] + 543) ."-". $arr_temp[1] ."-". $arr_temp[2];
			} // end if
		} // endif
	}

	if($command == "UPDATE" && trim($REQ_SEQ) && trim($REQ_POS_NO)){
		$cmd = " update PER_REQ1_DTL1 set 
						 	REQ_POS_NO = '$REQ_POS_NO', 
							REQ_EFF_DATE = '$REQ_EFF_DATE',
							REQ_RESULT = $REQ_RESULT,
							REQ_EFFECTIVE = $REQ_EFFECTIVE,
							OT_CODE = '$REQ_OT_CODE',
							PL_CODE = '$REQ_PL_CODE',
							PM_CODE = '$REQ_PM_CODE',
							CL_NAME = '$REQ_CL_NAME',
							PT_CODE = '$REQ_PT_CODE',
							SKILL_CODE = '$REQ_SKILL_CODE',
							PC_CODE = '$REQ_PC_CODE',
							REQ_CONDITION = '$REQ_CONDITION',
							REQ_SALARY = $REQ_SALARY,
							REQ_MGTSALARY = $REQ_MGTSALARY,
							ORG_ID = $REQ_ORG_ID,
							ORG_ID_1 = $REQ_ORG_ID_1,
							ORG_ID_2 = $REQ_ORG_ID_2,
							ORG_ID_3 = $REQ_ORG_ID_3,
							ORG_ID_4 = $REQ_ORG_ID_4,
							ORG_ID_5 = $REQ_ORG_ID_5,
							UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' 
						 where REQ_ID=$REQ_ID and REQ_SEQ=$REQ_SEQ ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " select POS_ID from PER_REQ1_DTL2 where REQ_ID=$REQ_ID and REQ_SEQ=$REQ_SEQ ";
		$count_dtl2 = $db_dpis->send_cmd($cmd);
		if($count_dtl2){
			$cmd = " update PER_REQ1_DTL2 set 
								POS_ID=$POS_ID,
								UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE'  
							 where REQ_ID=$REQ_ID and REQ_SEQ=$REQ_SEQ ";
		}else{
			$cmd = " insert into PER_REQ1_DTL2 (REQ_ID, REQ_SEQ, POS_ID, UPDATE_USER, UPDATE_DATE)
							 values ($REQ_ID, $REQ_SEQ, $POS_ID, $SESS_USERID, '$UPDATE_DATE') ";
		} // end if
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [".trim($REQ_SEQ)." : ".$REQ_POS_NO." : ".$REQ_PL_NAME."]");
	}
	
	if($command == "DELETE" && trim($REQ_SEQ)){
		$cmd = " select REQ_POS_NO, PL_CODE from PER_REQ1_DTL1 where REQ_ID=$REQ_ID and REQ_SEQ=$REQ_SEQ ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$REQ_POS_NO = $data[REQ_POS_NO];
		$REQ_PL_CODE = $data[PL_CODE];
		
		$cmd = " select PL_NAME from PER_LINE where PL_CODE='$REQ_PL_CODE' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$REQ_PL_NAME = $data[PL_NAME];
		
		$cmd = " delete from PER_REQ1_DTL2 where REQ_ID=$REQ_ID and REQ_SEQ=$REQ_SEQ ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " delete from PER_REQ1_DTL1 where REQ_ID=$REQ_ID and REQ_SEQ=$REQ_SEQ ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [".trim($REQ_SEQ)." : ".$REQ_POS_NO." : ".$REQ_PL_NAME."]");
	}
	
	if($UPD || $VIEW){
		$cmd = " select 	REQ_SEQ, REQ_POS_NO, REQ_EFF_DATE, REQ_RESULT, REQ_EFFECTIVE,
										OT_CODE, PL_CODE, PM_CODE, CL_NAME, PT_CODE, SKILL_CODE, PC_CODE, 
										REQ_CONDITION, REQ_SALARY, REQ_MGTSALARY, ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5
				  		 from 		PER_REQ1_DTL1 
						 where 	REQ_ID=$REQ_ID and REQ_SEQ=$REQ_SEQ ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$REQ_SEQ = $data[REQ_SEQ];
		$REQ_POS_NO = $data[REQ_POS_NO];
		$REQ_EFF_DATE = substr($data[REQ_EFF_DATE], 0, 10);
		if($REQ_EFF_DATE){
			$arr_temp = explode("-", $REQ_EFF_DATE);
			$REQ_EFF_DATE = ($arr_temp[0] + 543) ."-". $arr_temp[1] ."-". $arr_temp[2];
		} // end if
		
		$REQ_EFFECTIVE = $data[REQ_EFFECTIVE];		
		$REQ_RESULT = $data[REQ_RESULT];		
		$REQ_OT_CODE = $data[OT_CODE];
		$cmd = " select OT_NAME from PER_OFF_TYPE where OT_CODE='$REQ_OT_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$REQ_OT_NAME = $data2[OT_NAME];

		$REQ_PL_CODE = $data[PL_CODE];
		$cmd = " select PL_NAME from PER_LINE where PL_CODE='$REQ_PL_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$REQ_PL_NAME = $data2[PL_NAME];

		$REQ_PM_CODE = $data[PM_CODE];
		$cmd = " select PM_NAME from PER_MGT where PM_CODE='$REQ_PM_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$REQ_PM_NAME = $data2[PM_NAME];

		$REQ_CL_NAME = $data[CL_NAME];
		$REQ_CL_CODE = $REQ_CL_NAME;
		
		$REQ_PT_CODE = $data[PT_CODE];
		$cmd = " select PT_NAME from PER_TYPE where PT_CODE='$REQ_PT_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$REQ_PT_NAME = $data2[PT_NAME];

		$REQ_SKILL_CODE = $data[SKILL_CODE];
		$cmd = " select a.SKILL_NAME, a.SG_CODE, b.SG_NAME from PER_SKILL a, PER_SKILL_GROUP b where a.SKILL_CODE='$REQ_SKILL_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$REQ_SKILL_NAME = $data2[SKILL_NAME];
		$REQ_SG_CODE = $data2[SG_CODE];
		$REQ_SG_NAME = $data2[SG_NAME];

		$REQ_PC_CODE = $data[PC_CODE];
		$cmd = " select PC_NAME from PER_CONDITION where PC_CODE='$REQ_PC_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$REQ_PC_NAME = $data2[PC_NAME];

		$REQ_CONDITION = $data[REQ_CONDITION];
		$REQ_SALARY = $data[REQ_SALARY];
		$REQ_MGTSALARY = $data[REQ_MGTSALARY];

		$REQ_ORG_ID = $data[ORG_ID];
		$cmd = " select ORG_NAME, CT_CODE, PV_CODE, AP_CODE, OT_CODE from PER_ORG where ORG_ID=$REQ_ORG_ID ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$REQ_ORG_NAME = $data2[ORG_NAME];
		$REQ_CT_CODE = $data2[CT_CODE];
		$REQ_PV_CODE = $data2[PV_CODE];
		$REQ_AP_CODE = $data2[AP_CODE];
		$REQ_ORG_OT_CODE = $data2[OT_CODE];

		$cmd = " select CT_NAME from PER_COUNTRY where CT_CODE='$REQ_CT_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$REQ_CT_NAME = $data2[CT_NAME];

		$cmd = " select PV_NAME from PER_PROVINCE where PV_CODE='$REQ_PV_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$REQ_PV_NAME = $data2[PV_NAME];

		$cmd = " select AP_NAME from PER_AMPHUR where AP_CODE='$REQ_AP_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$REQ_AP_NAME = $data2[AP_NAME];

		$cmd = " select OT_NAME from PER_ORG_TYPE where OT_CODE='$REQ_ORG_OT_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$REQ_ORG_OT_NAME = $data2[OT_NAME];

		$REQ_ORG_ID_1 = $data[ORG_ID_1];
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$REQ_ORG_ID_1 ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$REQ_ORG_NAME_1 = $data2[ORG_NAME];

		$REQ_ORG_ID_2 = $data[ORG_ID_2];
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$REQ_ORG_ID_2 ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$REQ_ORG_NAME_2 = $data2[ORG_NAME];

		$REQ_ORG_ID_3 = $data[ORG_ID_3];
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$REQ_ORG_ID_3 ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$REQ_ORG_NAME_3 = $data2[ORG_NAME];

		$REQ_ORG_ID_4 = $data[ORG_ID_4];
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$REQ_ORG_ID_4 ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$REQ_ORG_NAME_4 = $data2[ORG_NAME];

		$REQ_ORG_ID_5 = $data[ORG_ID_5];
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$REQ_ORG_ID_5 ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$REQ_ORG_NAME_5 = $data2[ORG_NAME];

		$cmd = " select 	a.POS_ID, b.POS_NO, b.OT_CODE, b.PL_CODE, b.PM_CODE, b.CL_NAME, b.PT_CODE, b.SKILL_CODE, b.PC_CODE, 
										b.POS_CONDITION, b.POS_SALARY, b.POS_MGTSALARY, b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2, b.ORG_ID_3, b.ORG_ID_4, b.ORG_ID_5
				  		 from 		PER_REQ1_DTL2 a, PER_POSITION b
						 where 	a.POS_ID=b.POS_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();

		$POS_ID = $data[POS_ID];
		$POS_NO = $data[POS_NO];

		$OT_CODE = $data[OT_CODE];
		$cmd = " select OT_NAME from PER_OFF_TYPE where OT_CODE='$OT_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$OT_NAME = $data2[OT_NAME];

		$PL_CODE = $data[PL_CODE];
		$cmd = " select PL_NAME from PER_LINE where PL_CODE='$PL_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$PL_NAME = $data2[PL_NAME];

		$PM_CODE = $data[PM_CODE];
		$cmd = " select PM_NAME from PER_MGT where PM_CODE='$PM_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$PM_NAME = $data2[PM_NAME];

		$CL_NAME = $data[CL_NAME];
		$CL_CODE = $CL_NAME;
		
		$PT_CODE = $data[PT_CODE];
		$cmd = " select PT_NAME from PER_TYPE where PT_CODE='$PT_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$PT_NAME = $data2[PT_NAME];

		$SKILL_CODE = $data[SKILL_CODE];
		$cmd = " select a.SKILL_NAME, a.SG_CODE, b.SG_NAME from PER_SKILL a, PER_SKILL_GROUP b where a.SKILL_CODE='$SKILL_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$SKILL_NAME = $data2[SKILL_NAME];
		$SG_CODE = $data2[SG_CODE];
		$SG_NAME = $data2[SG_NAME];

		$PC_CODE = $data[PC_CODE];
		$cmd = " select PC_NAME from PER_CONDITION where PC_CODE='$PC_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$PC_NAME = $data2[PC_NAME];

		$POS_CONDITION = $data[POS_CONDITION];
		$POS_SALARY = $data[POS_SALARY];
		$POS_MGTSALARY = $data[POS_MGTSALARY];

		$ORG_ID = $data[ORG_ID];
		$cmd = " select ORG_NAME, CT_CODE, PV_CODE, AP_CODE, OT_CODE from PER_ORG where ORG_ID=$ORG_ID ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$ORG_NAME = $data2[ORG_NAME];
		$CT_CODE = $data2[CT_CODE];
		$PV_CODE = $data2[PV_CODE];
		$AP_CODE = $data2[AP_CODE];
		$ORG_OT_CODE = $data2[OT_CODE];

		$cmd = " select CT_NAME from PER_COUNTRY where CT_CODE='$CT_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$CT_NAME = $data2[CT_NAME];

		$cmd = " select PV_NAME from PER_PROVINCE where PV_CODE='$PV_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$PV_NAME = $data2[PV_NAME];

		$cmd = " select AP_NAME from PER_AMPHUR where AP_CODE='$AP_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$AP_NAME = $data2[AP_NAME];

		$cmd = " select OT_NAME from PER_ORG_TYPE where OT_CODE='$ORG_OT_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$ORG_OT_NAME = $data2[OT_NAME];

		$ORG_ID_1 = $data[ORG_ID_1];
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_1 ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$ORG_NAME_1 = $data2[ORG_NAME];

		$ORG_ID_2 = $data[ORG_ID_2];
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_2 ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$ORG_NAME_2 = $data2[ORG_NAME];

		$ORG_ID_3 = $data[ORG_ID_3];
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_3 ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$ORG_NAME_3 = $data2[ORG_NAME];

		$ORG_ID_4 = $data[ORG_ID_4];
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_4 ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$ORG_NAME_4 = $data2[ORG_NAME];

		$ORG_ID_5 = $data[ORG_ID_5];
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_5 ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$ORG_NAME_5 = $data2[ORG_NAME];
	} // end if
	
	if( (!$UPD && !$DEL && !$VIEW && !$err_text) ){
		$REQ_SEQ = "";
		$cmd = " select max(REQ_SEQ) as MAX_SEQ from PER_REQ1_DTL1 where REQ_ID=$REQ_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$REQ_SEQ = $data[max_seq] + 1;
		
		$REQ_POS_NO = "";
		$REQ_EFF_DATE = "";
		$REQ_RESULT = "";
		$REQ_EFFECTIVE = "";
		$POS_ID = "";
		$POS_NO = "";
		
		$REQ_OT_NAME = "";
		$REQ_OT_CODE = "";
		$REQ_PL_NAME = "";
		$REQ_PL_CODE = "";
		$REQ_PM_NAME = "";
		$REQ_PM_CODE = "";
		$REQ_CL_NAME = "";
		$REQ_CL_CODE = "";
		$REQ_PT_NAME = "";
		$REQ_PT_CODE = "";
		$REQ_SG_NAME = "";
		$REQ_SG_CODE = "";
		$REQ_SKILL_NAME = "";
		$REQ_SKILL_CODE = "";
		$REQ_PC_NAME = "";
		$REQ_PC_CODE = "";
		$REQ_CONDITION = "";
		$REQ_SALARY = "";
		$REQ_MGTSALARY = "";
		$REQ_ORG_NAME = "";
		$REQ_ORG_ID = "";
		$REQ_ORG_NAME_1 = "";
		$REQ_ORG_ID_1 = "";
		$REQ_ORG_NAME_2 = "";
		$REQ_ORG_ID_2 = "";
		$REQ_ORG_NAME_3 = "";
		$REQ_ORG_ID_3 = "";
		$REQ_ORG_NAME_4 = "";
		$REQ_ORG_ID_4 = "";
		$REQ_ORG_NAME_5 = "";
		$REQ_ORG_ID_5 = "";
		$REQ_CT_NAME = "";
		$REQ_CT_CODE = "";
		$REQ_AP_NAME = "";
		$REQ_AP_CODE = "";
		$REQ_ORG_OT_CODE = "";
		$REQ_ORG_OT_NAME = "";
		if($CTRL_TYPE < 3 && $SESS_USERGROUP_LEVEL < 3){
			$REQ_MINISTRY_ID = "";
			$REQ_MINISTRY_NAME = "";
		} // end if
		if($CTRL_TYPE < 4 && $SESS_USERGROUP_LEVEL < 4){
			$REQ_DEPARTMENT_ID = "";
			$REQ_DEPARTMENT_NAME = "";
		} // end if
		if($CTRL_TYPE != 2 && $SESS_USERGROUP_LEVEL != 2){
			$REQ_PV_NAME = "";
			$REQ_PV_CODE = "";
		} // end if

		$OT_NAME = "";
		$OT_CODE = "";
		$PL_NAME = "";
		$PL_CODE = "";
		$PM_NAME = "";
		$PM_CODE = "";
		$CL_NAME = "";
		$CL_CODE = "";
		$PT_NAME = "";
		$PT_CODE = "";
		$SG_NAME = "";
		$SG_CODE = "";
		$SKILL_NAME = "";
		$SKILL_CODE = "";
		$PC_NAME = "";
		$PC_CODE = "";
		$POS_CONDITION = "";
		$POS_SALARY = "";
		$POS_MGTSALARY = "";
		$ORG_NAME_1 = "";
		$ORG_ID_1 = "";
		$ORG_NAME_2 = "";
		$ORG_ID_2 = "";
		$ORG_NAME_3 = "";
		$ORG_ID_3 = "";
		$ORG_NAME_4 = "";
		$ORG_ID_4 = "";
		$ORG_NAME_5 = "";
		$ORG_ID_5 = "";
		$CT_NAME = "";
		$CT_CODE = "";
		$AP_NAME = "";
		$AP_CODE = "";
		$ORG_OT_CODE = "";
		$ORG_OT_NAME = "";

		if($CTRL_TYPE < 3 && $SESS_USERGROUP_LEVEL < 3){
			$MINISTRY_ID = "";
			$MINISTRY_NAME = "";
		} // end if
		if($CTRL_TYPE < 4 && $SESS_USERGROUP_LEVEL < 4){
			$DEPARTMENT_ID = "";
			$DEPARTMENT_NAME = "";
		} // end if
		if($SESS_USERGROUP_LEVEL < 5){
			$ORG_ID = "";
			$ORG_NAME = "";
		} // end if
		if($CTRL_TYPE != 2 && $SESS_USERGROUP_LEVEL != 2){
			$PV_NAME = "";
			$PV_CODE = "";
		} // end if
	} // end if
?>