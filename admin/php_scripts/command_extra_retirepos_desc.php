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
			$RETIRE_PV_CODE = $DROP_PV_CODE = $REQ_PV_CODE = $PV_CODE;
			$RETIRE_PV_NAME = $DROP_PV_NAME = $REQ_PV_NAME = $PV_NAME;
			break;
		case 3 :
			$RETIRE_MINISTRY_ID = $DROP_MINISTRY_ID = $REQ_MINISTRY_ID = $MINISTRY_ID;
			$RETIRE_MINISTRY_NAME = $DROP_MINISTRY_NAME = $REQ_MINISTRY_NAME = $MINISTRY_NAME;
			break;
		case 4 :
			$RETIRE_MINISTRY_ID = $DROP_MINISTRY_ID = $REQ_MINISTRY_ID = $MINISTRY_ID;
			$RETIRE_MINISTRY_NAME = $DROP_MINISTRY_NAME = $REQ_MINISTRY_NAME = $MINISTRY_NAME;
			$RETIRE_DEPARTMENT_ID = $DROP_DEPARTMENT_ID = $REQ_DEPARTMENT_ID = $DEPARTMENT_ID;
			$RETIRE_DEPARTMENT_NAME = $DROP_DEPARTMENT_NAME = $REQ_DEPARTMENT_NAME = $DEPARTMENT_NAME;
			break;
	} // end switch case

	switch($SESS_USERGROUP_LEVEL){
		case 2 :
			$RETIRE_PV_CODE = $DROP_PV_CODE = $REQ_PV_CODE = $PV_CODE;
			$RETIRE_PV_NAME = $DROP_PV_NAME = $REQ_PV_NAME = $PV_NAME;
			break;
		case 3 :
			$RETIRE_MINISTRY_ID = $DROP_MINISTRY_ID = $REQ_MINISTRY_ID = $MINISTRY_ID;
			$RETIRE_MINISTRY_NAME = $DROP_MINISTRY_NAME = $REQ_MINISTRY_NAME = $MINISTRY_NAME;
			break;
		case 4 :
			$RETIRE_MINISTRY_ID = $DROP_MINISTRY_ID = $REQ_MINISTRY_ID = $MINISTRY_ID;
			$RETIRE_MINISTRY_NAME = $DROP_MINISTRY_NAME = $REQ_MINISTRY_NAME = $MINISTRY_NAME;
			$RETIRE_DEPARTMENT_ID = $DROP_DEPARTMENT_ID = $REQ_DEPARTMENT_ID = $DEPARTMENT_ID;
			$RETIRE_DEPARTMENT_NAME = $DROP_DEPARTMENT_NAME = $REQ_DEPARTMENT_NAME = $DEPARTMENT_NAME;
			break;
		case 5 :
			$RETIRE_MINISTRY_ID = $DROP_MINISTRY_ID = $REQ_MINISTRY_ID = $MINISTRY_ID;
			$RETIRE_MINISTRY_NAME = $DROP_MINISTRY_NAME = $REQ_MINISTRY_NAME = $MINISTRY_NAME;
			$RETIRE_DEPARTMENT_ID = $DROP_DEPARTMENT_ID = $REQ_DEPARTMENT_ID = $DEPARTMENT_ID;
			$RETIRE_DEPARTMENT_NAME = $DROP_DEPARTMENT_NAME = $REQ_DEPARTMENT_NAME = $DEPARTMENT_NAME;
			$RETIRE_ORG_ID = $DROP_ORG_ID = $REQ_ORG_ID = $ORG_ID;
			$RETIRE_ORG_NAME = $DROP_ORG_NAME = $REQ_ORG_NAME = $ORG_NAME;
			break;
	} // end switch case
	
	if($MINISTRY_ID && !trim($MINISTRY_NAME)){
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MINISTRY_NAME = $data[ORG_NAME];

		$RETIRE_MINISTRY_ID = $DROP_MINISTRY_ID = $REQ_MINISTRY_ID = $MINISTRY_ID;
		$RETIRE_MINISTRY_NAME = $DROP_MINISTRY_NAME = $REQ_MINISTRY_NAME = $MINISTRY_NAME;
	} // end if

	if($DEPARTMENT_ID && !trim($DEPARTMENT_NAME)){
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$DEPARTMENT_NAME = $data[ORG_NAME];

		$RETIRE_DEPARTMENT_ID = $DROP_DEPARTMENT_ID = $REQ_DEPARTMENT_ID = $DEPARTMENT_ID;
		$RETIRE_DEPARTMENT_NAME = $DROP_DEPARTMENT_NAME = $REQ_DEPARTMENT_NAME = $DEPARTMENT_NAME;
	} // end if

	$UPDATE_DATE = date("Y-m-d H:i:s");
		
	if($REQ_POS_STATUS == ""){
		$POS_ID_REQ = "";
		$REQ_POS_NO = "";
		$REQ_OT_CODE = "";
		$REQ_PL_CODE = "";
		$REQ_PM_CODE = "";
		$REQ_CL_NAME = "";
		$REQ_PT_CODE = "";
		$REQ_SKILL_CODE = "";
		$REQ_PC_CODE = "";
		$REQ_CONDITION = "";
		$REQ_SALARY = "";
		$REQ_MGTSALARY = "";
		$REQ_ORG_ID = "";
		$REQ_ORG_ID_1 = "";
		$REQ_ORG_ID_2 = "";
		$REQ_ORG_ID_3 = "";
		$REQ_ORG_ID_4 = "";
		$REQ_ORG_ID_5 = "";
	}elseif($REQ_POS_STATUS == 0){
		$POS_ID_REQ = $POS_ID_RETIRE;
		$REQ_POS_NO = $RETIRE_POS_NO;
		$REQ_OT_CODE = $RETIRE_OT_CODE;
		$REQ_PL_CODE = $RETIRE_PL_CODE;
		$REQ_PM_CODE = $RETIRE_PM_CODE;
		$REQ_CL_NAME = $RETIRE_CL_NAME;
		$REQ_PT_CODE = $RETIRE_PT_CODE;
		$REQ_SKILL_CODE = $RETIRE_SKILL_CODE;
		$REQ_PC_CODE = $RETIRE_PC_CODE;
		$REQ_CONDITION = $RETIRE_POS_CONDITION;
		$REQ_SALARY = $RETIRE_POS_SALARY;
		$REQ_MGTSALARY = $RETIRE_POS_MGTSALARY;
		$REQ_ORG_ID = $RETIRE_ORG_ID;
		$REQ_ORG_ID_1 = $RETIRE_ORG_ID_1;
		$REQ_ORG_ID_2 = $RETIRE_ORG_ID_2;
		$REQ_ORG_ID_3 = $RETIRE_ORG_ID_3;
		$REQ_ORG_ID_4 = $RETIRE_ORG_ID_4;
		$REQ_ORG_ID_5 = $RETIRE_ORG_ID_5;
	}elseif($REQ_POS_STATUS == 1){
		$POS_ID_REQ = "";
	} // end if

	if(trim($REQ_RESULT) == "") $REQ_RESULT = "NULL";
	if(trim($REQ_EFFECTIVE) == "") $REQ_EFFECTIVE = "NULL";
	if(trim($POS_ID_DROP) == "") $POS_ID_DROP = "NULL";
	if(trim($POS_ID_REQ) == "") $POS_ID_REQ = "NULL";
	if(trim($REQ_ORG_ID) == "") $REQ_ORG_ID = "NULL";
	if(trim($REQ_ORG_ID_1) == "") $REQ_ORG_ID_1 = "NULL";
	if(trim($REQ_ORG_ID_2) == "") $REQ_ORG_ID_2 = "NULL";
	if(trim($REQ_ORG_ID_3) == "") $REQ_ORG_ID_3 = "NULL";
	if(trim($REQ_ORG_ID_4) == "") $REQ_ORG_ID_4 = "NULL";
	if(trim($REQ_ORG_ID_5) == "") $REQ_ORG_ID_5 = "NULL";
	if(trim($REQ_SALARY) == "") $REQ_SALARY = "NULL";
	if(trim($REQ_MGTSALARY) == "") $REQ_MGTSALARY = "NULL";

	if(trim($REQ_EFF_DATE)){
		$arr_temp = explode("/", $REQ_EFF_DATE);
		$REQ_EFF_DATE = ($arr_temp[2] - 543) ."-". $arr_temp[1] ."-". $arr_temp[0];
	} // end if

	if($command == "ADD" && trim($REQ_SEQ) && trim($POS_ID_RETIRE)){
		$cmd = " select 	a.REQ_SEQ, a.POS_ID_RETIRE, b.POS_NO
				  		 from 		PER_REQ2_DTL a, PER_POSITION b
						 where 	a.REQ_ID=$REQ_ID and a.POS_ID_RETIRE=$POS_ID_RETIRE and a.POS_ID_RETIRE=b.POS_ID ";
		$count_duplicate = $db_dpis->send_cmd($cmd);
		if($count_duplicate <= 0){
			if($POS_ID_DROP && $REQ_RESULT==1){
				$cmd = " select PER_ID, PER_NAME, PER_SURNAME from PER_PERSONAL where POS_ID=$POS_ID_DROP and PER_TYPE=1 and PER_STATUS=1 ";
				$count_person_drop = $db_dpis->send_cmd($cmd);
				if($count_person_drop){ 
					$data = $db_dpis->get_array();
					$err_text = "ไม่สามารถอนุมัติได้ เนื่องจากตำแหน่งเลขที่ $DROP_POS_NO ยังมีคนครองอยู่ [$data[PER_NAME] $data[PER_SURNAME]]";
				} // end if
			} // end if
			
			if(!$err_text){
				$cmd = " insert into PER_REQ2_DTL (REQ_ID, REQ_SEQ, REQ_POS_NO, REQ_EFF_DATE, REQ_RESULT, REQ_EFFECTIVE, 
								POS_ID_REQ, POS_ID_RETIRE, POS_ID_DROP,	OT_CODE, PL_CODE, PM_CODE, CL_NAME, PT_CODE, SKILL_CODE, PC_CODE, 
								REQ_CONDITION, REQ_SALARY, REQ_MGTSALARY, ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5,
								UPDATE_USER, UPDATE_DATE)
								values ($REQ_ID, $REQ_SEQ, '".trim($REQ_POS_NO)."', '".trim($REQ_EFF_DATE)."', $REQ_RESULT, $REQ_EFFECTIVE,
								$POS_ID_REQ, $POS_ID_RETIRE, $POS_ID_DROP, '$REQ_OT_CODE', '$REQ_PL_CODE', '$REQ_PM_CODE', 
								'$REQ_CL_NAME', '$REQ_PT_CODE', '$REQ_SKILL_CODE', '$REQ_PC_CODE', '$REQ_CONDITION', $REQ_SALARY, 
								$REQ_MGTSALARY, $REQ_ORG_ID, $REQ_ORG_ID_1, $REQ_ORG_ID_2, $REQ_ORG_ID_3, $REQ_ORG_ID_4, $REQ_ORG_ID_5,
								$SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
	
				insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [".trim($REQ_SEQ)." : ".$RETIRE_POS_NO." : ".$RETIRE_PL_NAME."]");
			}else{
				if(trim($REQ_EFF_DATE)){
					$arr_temp = explode("-", $REQ_EFF_DATE);
					$REQ_EFF_DATE = ($arr_temp[0] + 543) ."-". $arr_temp[1] ."-". $arr_temp[2];
				} // end if
			} // end if
		}else{
			$data = $db_dpis->get_array();			
			$err_text = "รหัสข้อมูลซ้ำ [".$data[REQ_SEQ]." ".$data[POS_NO]."]";

			if(trim($REQ_EFF_DATE)){
				$arr_temp = explode("-", $REQ_EFF_DATE);
				$REQ_EFF_DATE = ($arr_temp[0] + 543) ."-". $arr_temp[1] ."-". $arr_temp[2];
			} // end if
		} // endif
	}

	if($command == "UPDATE" && trim($REQ_SEQ) && trim($POS_ID_RETIRE)){
		if($POS_ID_DROP && $REQ_RESULT==1){
			$cmd = " select PER_ID, PER_NAME, PER_SURNAME from PER_PERSONAL where POS_ID=$POS_ID_DROP and PER_TYPE=1 and PER_STATUS=1 ";
			$count_person_drop = $db_dpis->send_cmd($cmd);
			if($count_person_drop){ 
				$data = $db_dpis->get_array();
				$err_text = "ไม่สามารถอนุมัติได้ เนื่องจากตำแหน่งเลขที่ $DROP_POS_NO ยังมีคนครองอยู่ [$data[PER_NAME] $data[PER_SURNAME]]";
			} // end if
		} // end if

		if(!$err_text){
			$cmd = " update PER_REQ2_DTL set 
								POS_ID_REQ = $POS_ID_REQ,
								POS_ID_RETIRE = $POS_ID_RETIRE,
								POS_ID_DROP = $POS_ID_DROP,
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
//			$db_dpis->show_error();
	
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [".trim($REQ_SEQ)." : ".$RETIRE_POS_NO." : ".$RETIRE_PL_NAME."]");
		}else{
			$UPD = 1;

			if(trim($REQ_EFF_DATE)){
				$arr_temp = explode("-", $REQ_EFF_DATE);
				$REQ_EFF_DATE = ($arr_temp[0] + 543) ."-". $arr_temp[1] ."-". $arr_temp[2];
			} // end if
		} // end if
	}
	
	if($command == "DELETE" && trim($REQ_SEQ)){
		$cmd = " select b.POS_NO, b.PL_CODE from PER_REQ2_DTL a, PER_POSITION b where REQ_ID=$REQ_ID and REQ_SEQ=$REQ_SEQ ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$RETIRE_POS_NO = $data[POS_NO];
		$RETIRE_PL_CODE = $data[PL_CODE];
		
		$cmd = " select PL_NAME from PER_LINE where PL_CODE='$REQ_PL_CODE' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$RETIRE_PL_NAME = $data[PL_NAME];
		
		$cmd = " delete from PER_REQ2_DTL where REQ_ID=$REQ_ID and REQ_SEQ=$REQ_SEQ ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [".trim($REQ_SEQ)." : ".$RETIRE_POS_NO." : ".$RETIRE_PL_NAME."]");
	}
	
	if($UPD || $VIEW){
		$cmd = " select 	REQ_SEQ, REQ_EFF_DATE, REQ_RESULT, REQ_EFFECTIVE,
										POS_ID_RETIRE, POS_ID_DROP, POS_ID_REQ, REQ_POS_NO,
										REQ_POS_NO, OT_CODE, PL_CODE, PM_CODE, CL_NAME, PT_CODE, SKILL_CODE, PC_CODE, 
										REQ_CONDITION, REQ_SALARY, REQ_MGTSALARY, ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5
				  		 from 		PER_REQ2_DTL 
						 where 	REQ_ID=$REQ_ID and REQ_SEQ=$REQ_SEQ ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$REQ_SEQ = $data[REQ_SEQ];
		$REQ_EFF_DATE = substr($data[REQ_EFF_DATE], 0, 10);
		if($REQ_EFF_DATE){
			$arr_temp = explode("-", $REQ_EFF_DATE);
			$REQ_EFF_DATE = ($arr_temp[0] + 543) ."-". $arr_temp[1] ."-". $arr_temp[2];
		} // end if		
		$REQ_EFFECTIVE = $data[REQ_EFFECTIVE];		
		$REQ_RESULT = $data[REQ_RESULT];		
		
		$POS_ID_RETIRE = $data[POS_ID_RETIRE];
		$POS_ID_DROP = $data[POS_ID_DROP];
		$POS_ID_REQ = $data[POS_ID_REQ];
		$REQ_POS_NO = trim($data[REQ_POS_NO]);

		if($REQ_POS_NO && !$POS_ID_REQ) $REQ_POS_STATUS = 1;
		elseif($REQ_POS_NO && $POS_ID_REQ) $REQ_POS_STATUS = 0;
		elseif(!$REQ_POS_NO && !$POS_ID_REQ) $REQ_POS_STATUS = "";

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
		$REQ_ORG_NAME_2 = $data2[ORG_NAME];

		$cmd = " select 	POS_NO, OT_CODE, PL_CODE, PM_CODE, CL_NAME, PT_CODE, SKILL_CODE, PC_CODE, 
										POS_CONDITION, POS_SALARY, POS_MGTSALARY, ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5
				  		 from 		PER_POSITION
						 where 	POS_ID = $POS_ID_RETIRE ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();

		$RETIRE_POS_NO = $data[POS_NO];

		$RETIRE_OT_CODE = $data[OT_CODE];
		$cmd = " select OT_NAME from PER_OFF_TYPE where OT_CODE='$RETIRE_OT_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$RETIRE_OT_NAME = $data2[OT_NAME];

		$RETIRE_PL_CODE = $data[PL_CODE];
		$cmd = " select PL_NAME from PER_LINE where PL_CODE='$RETIRE_PL_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$RETIRE_PL_NAME = $data2[PL_NAME];

		$RETIRE_PM_CODE = $data[PM_CODE];
		$cmd = " select PM_NAME from PER_MGT where PM_CODE='$RETIRE_PM_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$RETIRE_PM_NAME = $data2[PM_NAME];

		$RETIRE_CL_NAME = $data[CL_NAME];
		$RETIRE_CL_CODE = $RETIRE_CL_NAME;
		
		$RETIRE_PT_CODE = $data[PT_CODE];
		$cmd = " select PT_NAME from PER_TYPE where PT_CODE='$RETIRE_PT_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$RETIRE_PT_NAME = $data2[PT_NAME];

		$RETIRE_SKILL_CODE = $data[SKILL_CODE];
		$cmd = " select a.SKILL_NAME, a.SG_CODE, b.SG_NAME from PER_SKILL a, PER_SKILL_GROUP b where a.SKILL_CODE='$RETIRE_SKILL_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$RETIRE_SKILL_NAME = $data2[SKILL_NAME];
		$RETIRE_SG_CODE = $data2[SG_CODE];
		$RETIRE_SG_NAME = $data2[SG_NAME];

		$RETIRE_PC_CODE = $data[PC_CODE];
		$cmd = " select PC_NAME from PER_CONDITION where PC_CODE='$RETIRE_PC_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$RETIRE_PC_NAME = $data2[PC_NAME];

		$RETIRE_POS_CONDITION = $data[POS_CONDITION];
		$RETIRE_POS_SALARY = $data[POS_SALARY];
		$RETIRE_POS_MGTSALARY = $data[POS_MGTSALARY];

		$RETIRE_ORG_ID = $data[ORG_ID];
		$cmd = " select ORG_NAME, CT_CODE, PV_CODE, AP_CODE, OT_CODE from PER_ORG where ORG_ID=$RETIRE_ORG_ID ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$RETIRE_ORG_NAME = $data2[ORG_NAME];
		$RETIRE_CT_CODE = $data2[CT_CODE];
		$RETIRE_PV_CODE = $data2[PV_CODE];
		$RETIRE_AP_CODE = $data2[AP_CODE];
		$RETIRE_ORG_OT_CODE = $data2[OT_CODE];

		$cmd = " select CT_NAME from PER_COUNTRY where CT_CODE='$RETIRE_CT_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$RETIRE_CT_NAME = $data2[CT_NAME];

		$cmd = " select PV_NAME from PER_PROVINCE where PV_CODE='$RETIRE_PV_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$RETIRE_PV_NAME = $data2[PV_NAME];

		$cmd = " select AP_NAME from PER_AMPHUR where AP_CODE='$RETIRE_AP_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$RETIRE_AP_NAME = $data2[AP_NAME];

		$cmd = " select OT_NAME from PER_ORG_TYPE where OT_CODE='$RETIRE_ORG_OT_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$RETIRE_ORG_OT_NAME = $data2[OT_NAME];

		$RETIRE_ORG_ID_1 = $data[ORG_ID_1];
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$RETIRE_ORG_ID_1 ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$RETIRE_ORG_NAME_1 = $data2[ORG_NAME];

		$RETIRE_ORG_ID_2 = $data[ORG_ID_2];
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$RETIRE_ORG_ID_2 ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$RETIRE_ORG_NAME_2 = $data2[ORG_NAME];

		$RETIRE_ORG_ID_3 = $data[ORG_ID_3];
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$RETIRE_ORG_ID_3 ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$RETIRE_ORG_NAME_3 = $data2[ORG_NAME];

		$RETIRE_ORG_ID_4 = $data[ORG_ID_4];
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$RETIRE_ORG_ID_4 ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$RETIRE_ORG_NAME_4 = $data2[ORG_NAME];

		$RETIRE_ORG_ID_5 = $data[ORG_ID_5];
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$RETIRE_ORG_ID_5 ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$RETIRE_ORG_NAME_5 = $data2[ORG_NAME];

		$cmd = " select 	POS_NO, OT_CODE, PL_CODE, PM_CODE, CL_NAME, PT_CODE, SKILL_CODE, PC_CODE, 
										POS_CONDITION, POS_SALARY, POS_MGTSALARY, ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5
				  		 from 		PER_POSITION
						 where 	POS_ID = $POS_ID_DROP ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();

		$DROP_POS_NO = $data[POS_NO];

		$DROP_OT_CODE = $data[OT_CODE];
		$cmd = " select OT_NAME from PER_OFF_TYPE where OT_CODE='$DROP_OT_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$DROP_OT_NAME = $data2[OT_NAME];

		$DROP_PL_CODE = $data[PL_CODE];
		$cmd = " select PL_NAME from PER_LINE where PL_CODE='$DROP_PL_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$DROP_PL_NAME = $data2[PL_NAME];

		$DROP_PM_CODE = $data[PM_CODE];
		$cmd = " select PM_NAME from PER_MGT where PM_CODE='$DROP_PM_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$DROP_PM_NAME = $data2[PM_NAME];

		$DROP_CL_NAME = $data[CL_NAME];
		$DROP_CL_CODE = $DROP_CL_NAME;
		
		$DROP_PT_CODE = $data[PT_CODE];
		$cmd = " select PT_NAME from PER_TYPE where PT_CODE='$DROP_PT_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$DROP_PT_NAME = $data2[PT_NAME];

		$DROP_SKILL_CODE = $data[SKILL_CODE];
		$cmd = " select a.SKILL_NAME, a.SG_CODE, b.SG_NAME from PER_SKILL a, PER_SKILL_GROUP b where a.SKILL_CODE='$DROP_SKILL_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$DROP_SKILL_NAME = $data2[SKILL_NAME];
		$DROP_SG_CODE = $data2[SG_CODE];
		$DROP_SG_NAME = $data2[SG_NAME];

		$DROP_PC_CODE = $data[PC_CODE];
		$cmd = " select PC_NAME from PER_CONDITION where PC_CODE='$DROP_PC_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$DROP_PC_NAME = $data2[PC_NAME];

		$DROP_POS_CONDITION = $data[POS_CONDITION];
		$DROP_POS_SALARY = $data[POS_SALARY];
		$DROP_POS_MGTSALARY = $data[POS_MGTSALARY];

		$DROP_ORG_ID = $data[ORG_ID];
		$cmd = " select ORG_NAME, CT_CODE, PV_CODE, AP_CODE, OT_CODE from PER_ORG where ORG_ID=$DROP_ORG_ID ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$DROP_ORG_NAME = $data2[ORG_NAME];
		$DROP_CT_CODE = $data2[CT_CODE];
		$DROP_PV_CODE = $data2[PV_CODE];
		$DROP_AP_CODE = $data2[AP_CODE];
		$DROP_ORG_OT_CODE = $data2[OT_CODE];

		$cmd = " select CT_NAME from PER_COUNTRY where CT_CODE='$DROP_CT_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$DROP_CT_NAME = $data2[CT_NAME];

		$cmd = " select PV_NAME from PER_PROVINCE where PV_CODE='$DROP_PV_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$DROP_PV_NAME = $data2[PV_NAME];

		$cmd = " select AP_NAME from PER_AMPHUR where AP_CODE='$DROP_AP_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$DROP_AP_NAME = $data2[AP_NAME];

		$cmd = " select OT_NAME from PER_ORG_TYPE where OT_CODE='$DROP_ORG_OT_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$DROP_ORG_OT_NAME = $data2[OT_NAME];

		$DROP_ORG_ID_1 = $data[ORG_ID_1];
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$DROP_ORG_ID_1 ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$DROP_ORG_NAME_1 = $data2[ORG_NAME];

		$DROP_ORG_ID_2 = $data[ORG_ID_2];
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$DROP_ORG_ID_2 ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$DROP_ORG_NAME_2 = $data2[ORG_NAME];
		
		$DROP_ORG_ID_3 = $data[ORG_ID_3];
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$DROP_ORG_ID_3 ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$DROP_ORG_NAME_3 = $data2[ORG_NAME];

		$DROP_ORG_ID_4 = $data[ORG_ID_4];
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$DROP_ORG_ID_4 ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$DROP_ORG_NAME_4 = $data2[ORG_NAME];
		
		$DROP_ORG_ID_5 = $data[ORG_ID_5];
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$DROP_ORG_ID_5 ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$DROP_ORG_NAME_5 = $data2[ORG_NAME];
		
		if($POS_ID_DROP){			
			$cmd = " select PER_ID from PER_PERSONAL where POS_ID=$POS_ID_DROP and PER_TYPE=1 and PER_STATUS=1 ";
			$DROP_IS_NULL = $db_dpis->send_cmd($cmd);
		} // end if
	} // end if
	
	if( (!$UPD && !$DEL && !$VIEW && !$err_text) ){
		$REQ_SEQ = "";
		$cmd = " select max(REQ_SEQ) as MAX_SEQ from PER_REQ2_DTL where REQ_ID=$REQ_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$REQ_SEQ = $data[max_seq] + 1;
		
		$REQ_POS_NO = "";
		$REQ_EFF_DATE = "";
		$REQ_RESULT = "";
		$REQ_EFFECTIVE = "";
		$REQ_POS_STATUS = "";

		$POS_ID_REQ = "";
		$REQ_POS_NO = "";		
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
		$REQ_PV_NAME = "";
		$REQ_PV_CODE = "";
		$REQ_AP_NAME = "";
		$REQ_AP_CODE = "";
		$REQ_ORG_OT_CODE = "";
		$REQ_ORG_OT_NAME = "";

		$POS_ID_RETIRE = "";
		$RETIRE_POS_NO = "";		
		$RETIRE_OT_NAME = "";
		$RETIRE_OT_CODE = "";
		$RETIRE_PL_NAME = "";
		$RETIRE_PL_CODE = "";
		$RETIRE_PM_NAME = "";
		$RETIRE_PM_CODE = "";
		$RETIRE_CL_NAME = "";
		$RETIRE_CL_CODE = "";
		$RETIRE_PT_NAME = "";
		$RETIRE_PT_CODE = "";
		$RETIRE_SG_NAME = "";
		$RETIRE_SG_CODE = "";
		$RETIRE_SKILL_NAME = "";
		$RETIRE_SKILL_CODE = "";
		$RETIRE_PC_NAME = "";
		$RETIRE_PC_CODE = "";
		$RETIRE_POS_CONDITION = "";
		$RETIRE_POS_SALARY = "";
		$RETIRE_POS_MGTSALARY = "";
		$RETIRE_ORG_NAME = "";
		$RETIRE_ORG_ID = "";
		$RETIRE_ORG_NAME_1 = "";
		$RETIRE_ORG_ID_1 = "";
		$RETIRE_ORG_NAME_2 = "";
		$RETIRE_ORG_ID_2 = "";
		$RETIRE_ORG_NAME_3 = "";
		$RETIRE_ORG_ID_3 = "";
		$RETIRE_ORG_NAME_4 = "";
		$RETIRE_ORG_ID_4 = "";
		$RETIRE_ORG_NAME_5 = "";
		$RETIRE_ORG_ID_5 = "";
		$RETIRE_CT_NAME = "";
		$RETIRE_CT_CODE = "";
		$RETIRE_PV_NAME = "";
		$RETIRE_PV_CODE = "";
		$RETIRE_AP_NAME = "";
		$RETIRE_AP_CODE = "";
		$RETIRE_ORG_OT_CODE = "";
		$RETIRE_ORG_OT_NAME = "";

		$POS_ID_DROP = "";
		$DROP_POS_NO = "";		
		$DROP_OT_NAME = "";
		$DROP_OT_CODE = "";
		$DROP_PL_NAME = "";
		$DROP_PL_CODE = "";
		$DROP_PM_NAME = "";
		$DROP_PM_CODE = "";
		$DROP_CL_NAME = "";
		$DROP_CL_CODE = "";
		$DROP_PT_NAME = "";
		$DROP_PT_CODE = "";
		$DROP_SG_NAME = "";
		$DROP_SG_CODE = "";
		$DROP_SKILL_NAME = "";
		$DROP_SKILL_CODE = "";
		$DROP_PC_NAME = "";
		$DROP_PC_CODE = "";
		$DROP_POS_CONDITION = "";
		$DROP_POS_SALARY = "";
		$DROP_POS_MGTSALARY = "";
		$DROP_ORG_NAME = "";
		$DROP_ORG_ID = "";
		$DROP_ORG_NAME_1 = "";
		$DROP_ORG_ID_1 = "";
		$DROP_ORG_NAME_2 = "";
		$DROP_ORG_ID_2 = "";
		$DROP_ORG_NAME_3 = "";
		$DROP_ORG_ID_3 = "";
		$DROP_ORG_NAME_4 = "";
		$DROP_ORG_ID_4 = "";
		$DROP_ORG_NAME_5 = "";
		$DROP_ORG_ID_5 = "";
		$DROP_CT_NAME = "";
		$DROP_CT_CODE = "";
		$DROP_PV_NAME = "";
		$DROP_PV_CODE = "";
		$DROP_AP_NAME = "";
		$DROP_AP_CODE = "";
		$DROP_ORG_OT_CODE = "";
		$DROP_ORG_OT_NAME = "";
	} // end if
?>