<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");	
	include("php_scripts/load_per_control.php");

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	if ($ORD_CONFIRM=="1" && !$ORD_SEQ) { $ORD_SEQ = "1"; $VIEW_DTL="1"; }
	if (!$ORD_SEQ && $UPD_DTL=="1") { $ORD_SEQ = "1"; $UPD_DTL=""; $VIEW_DTL=""; }
//	echo ">> ORD_ID=$ORD_ID, ORD_SEQ=$ORD_SEQ, ORD_CONFIRM=$ORD_CONFIRM, UPD_DTL=$UPD_DTL, VIEW_DTL=$VIEW_DTL<br>";

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	switch($CTRL_TYPE){
		case 2 :
			$OLD_PV_CODE = $PV_CODE;
			$OLD_PV_NAME = $PV_NAME;
			break;
		case 3 :
			$OLD_MINISTRY_ID = $MINISTRY_ID;
			$OLD_MINISTRY_NAME = $MINISTRY_NAME;
			break;
		case 4 :
			$OLD_MINISTRY_ID = $MINISTRY_ID;
			$OLD_MINISTRY_NAME = $MINISTRY_NAME;
			$OLD_DEPARTMENT_ID = $DEPARTMENT_ID;
			$OLD_DEPARTMENT_NAME = $DEPARTMENT_NAME;				
			break;
	} // end switch case

	switch($SESS_USERGROUP_LEVEL){
		case 2 :
			$OLD_PV_CODE = $PV_CODE;
			$OLD_PV_NAME = $PV_NAME;
			break;
		case 3 :
			$OLD_MINISTRY_ID = $MINISTRY_ID;
			$OLD_MINISTRY_NAME = $MINISTRY_NAME;
			break;
		case 4 :
			$OLD_MINISTRY_ID = $MINISTRY_ID;
			$OLD_MINISTRY_NAME = $MINISTRY_NAME;
			$OLD_DEPARTMENT_ID = $DEPARTMENT_ID;
			$OLD_DEPARTMENT_NAME = $DEPARTMENT_NAME;				
			break;
		case 5 :
			$OLD_MINISTRY_ID = $MINISTRY_ID;
			$OLD_MINISTRY_NAME = $MINISTRY_NAME;
			$OLD_DEPARTMENT_ID = $DEPARTMENT_ID;
			$OLD_DEPARTMENT_NAME = $DEPARTMENT_NAME;				
			$OLD_ORG_ID = $ORG_ID;
			$OLD_ORG_NAME = $ORG_NAME;
			break;
	} // end switch case
	
	if($MINISTRY_ID && !trim($OLD_MINISTRY_NAME)){
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MINISTRY_NAME = $data[ORG_NAME];

		$OLD_MINISTRY_ID = $MINISTRY_ID;
		$OLD_MINISTRY_NAME = $MINISTRY_NAME;
	} // end if

	if($DEPARTMENT_ID && !trim($OLD_DEPARTMENT_NAME)){
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$DEPARTMENT_NAME = $data[ORG_NAME];

		$OLD_DEPARTMENT_ID = $DEPARTMENT_ID;
		$OLD_DEPARTMENT_NAME = $DEPARTMENT_NAME;
	} // end if

	$UPDATE_DATE = date("Y-m-d H:i:s");
	
	$cmd = " select ORD_CONFIRM from PER_ORDER_POS where ORD_ID=$ORD_ID ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$ORD_CONFIRM = $data[ORD_CONFIRM];
	
	if(trim($ORD_RESULT) == "") $ORD_RESULT = "NULL";
	if(trim($ORD_DTL_RESULT) == "") $ORD_DTL_RESULT = "NULL";
	if(trim($ORG_ID) == "") $ORG_ID = "NULL";
	if(trim($ORG_ID_1) == "") $ORG_ID_1 = "NULL";
	if(trim($ORG_ID_2) == "") $ORG_ID_2 = "NULL";
	if(trim($ORG_ID_3) == "") $ORG_ID_3 = "NULL";
	if(trim($ORG_ID_4) == "") $ORG_ID_4 = "NULL";
	if(trim($ORG_ID_5) == "") $ORG_ID_5 = "NULL";
	$PT_CODE = (trim($PT_CODE))? "'".$PT_CODE."'" : "NULL";
	$PC_CODE = (trim($PC_CODE))? "'".$PC_CODE."'" : "NULL";
	$PM_CODE = (trim($PM_CODE))? "'".$PM_CODE."'" : "NULL";
	$ORD_SALARY += 0;
	$ORD_MGTSALARY += 0;

	$ORD_EFF_DATE =  save_date($ORD_EFF_DATE);
	$ORD_DTL_DATE =  save_date($ORD_DTL_DATE);

	if($command == "ADD" && trim($ORD_SEQ) && trim($ORD_POS_NO)){
		$cmd = " select 	ORD_SEQ, ORD_POS_NO
				  		 from 		PER_ORDER_POS_DTL 
						 where 	ORD_ID=$ORD_ID and ORD_POS_NO_NAME = '$ORD_POS_NO_NAME' and trim(ORD_POS_NO)='". trim($ORD_POS_NO) ."' ";
		//echo $cmd;
		$count_duplicate = $db_dpis->send_cmd($cmd);
		if($count_duplicate <= 0){
			if(!$CL_NAME)	$CL_NAME = "";
			$cmd = " insert into PER_ORDER_POS_DTL (ORD_ID, ORD_SEQ, ORD_POS_NO_NAME, ORD_POS_NO, ORD_EFF_DATE, ORD_RESULT, POS_ID_OLD,
							OT_CODE, PL_CODE, PM_CODE, CL_NAME, PT_CODE, SKILL_CODE, PC_CODE, 
							ORD_CONDITION, ORD_SALARY, ORD_MGTSALARY, ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5,
							LEVEL_NO, ORD_REMARK, UPDATE_USER, UPDATE_DATE, ORD_DTL_NO, ORD_DTL_DATE, ORD_DTL_RESULT)
							values ($ORD_ID, $ORD_SEQ, '$ORD_POS_NO_NAME', '".trim($ORD_POS_NO)."', '".trim($ORD_EFF_DATE)."', $ORD_RESULT, $POS_ID_OLD,
							'".trim($OT_CODE)."', '".trim($PL_CODE)."', ".trim($PM_CODE).", '$CL_NAME', ".trim($PT_CODE).", '$SKILL_CODE', ".trim($PC_CODE).",
							'$ORD_CONDITION', $ORD_SALARY, $ORD_MGTSALARY, $ORG_ID, $ORG_ID_1, $ORG_ID_2, $ORG_ID_3, $ORG_ID_4, $ORG_ID_5,
							'$LEVEL_NO', '$ORD_REMARK', $SESS_USERID, '$UPDATE_DATE', '$ORD_DTL_NO', '$ORD_DTL_DATE', $ORD_DTL_RESULT) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [".trim($ORD_SEQ)." : ".$ORD_POS_NO." : ".$POS_ID_OLD." : ".$PL_NAME."]");
		}else{
			$data = $db_dpis->get_array();			
			$err_text = "รหัสข้อมูลซ้ำ [".$data[ORD_SEQ]." ".$data[ORD_POS_NO]."]";

			$ORD_EFF_DATE = show_date_format($ORD_EFF_DATE, 1);
			$ORD_DTL_DATE = show_date_format($ORD_DTL_DATE, 1);
		} // endif
	}

	if($command == "UPDATE" && trim($ORD_SEQ) && trim($ORD_POS_NO)){
		$cmd = " update PER_ORDER_POS_DTL set 
						 	ORD_POS_NO_NAME = '$ORD_POS_NO_NAME', 
						 	ORD_POS_NO = '$ORD_POS_NO', 
							ORD_EFF_DATE = '$ORD_EFF_DATE',
							ORD_RESULT = $ORD_RESULT,
							POS_ID_OLD = $POS_ID_OLD,
							OT_CODE = '$OT_CODE',
							PL_CODE = '$PL_CODE',
							PM_CODE = $PM_CODE,
							CL_NAME = '$CL_NAME',
							PT_CODE = $PT_CODE,
							SKILL_CODE = '$SKILL_CODE',
							PC_CODE = $PC_CODE,
							ORD_CONDITION = '$ORD_CONDITION',
							ORD_SALARY = $ORD_SALARY,
							ORD_MGTSALARY = $ORD_MGTSALARY,
							ORG_ID = $ORG_ID,
							ORG_ID_1 = $ORG_ID_1,
							ORG_ID_2 = $ORG_ID_2,
							ORG_ID_3 = $ORG_ID_3,
							ORG_ID_4 = $ORG_ID_4,
							ORG_ID_5 = $ORG_ID_5,
							LEVEL_NO = '$LEVEL_NO',
							ORD_REMARK = '$ORD_REMARK',
							ORD_DTL_NO = '$ORD_DTL_NO',
							ORD_DTL_DATE = '$ORD_DTL_DATE',
							ORD_DTL_RESULT = $ORD_DTL_RESULT,
							UPDATE_USER=$SESS_USERID, 
							UPDATE_DATE='$UPDATE_DATE' 
						 where ORD_ID=$ORD_ID and ORD_SEQ=$ORD_SEQ ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [".trim($ORD_SEQ)." : ".$ORD_POS_NO." : ".$POS_ID_OLD." : ".$PL_NAME."]");
	}
	
	if($command == "DELETE" && trim($ORD_SEQ)){
		$cmd = " select ORD_POS_NO, POS_ID_OLD from PER_ORDER_POS_DTL where ORD_ID=$ORD_ID and ORD_SEQ=$ORD_SEQ ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$ORD_POS_NO = $data[ORD_POS_NO];
		$POS_ID_OLD = $data[POS_ID_OLD];
		
		$cmd = " select b.PL_NAME from PER_POSITION a, PER_LINE b where POS_ID=$POS_ID_OLD ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PL_NAME = $data[PL_NAME];
		
		$cmd = " delete from PER_ORDER_POS_DTL where ORD_ID=$ORD_ID and ORD_SEQ=$ORD_SEQ ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [".trim($ORD_SEQ)." : ".$ORD_POS_NO." : ".$POS_ID_OLD." : ".$PL_NAME."]");
		$ORD_SEQ = "";
		$UPD_DTL = "";
	}
	
	if(($UPD_DTL || $VIEW_DTL) && $ORD_SEQ) {
		$cmd = " select 	ORD_SEQ, ORD_POS_NO_NAME, ORD_POS_NO, ORD_EFF_DATE, ORD_RESULT, POS_ID_OLD, OT_CODE, PL_CODE, 
										PM_CODE, CL_NAME,  PT_CODE, SKILL_CODE, PC_CODE, ORD_CONDITION, ORD_SALARY, ORD_MGTSALARY, ORG_ID, 
										ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5, LEVEL_NO, ORD_REMARK, ORD_DTL_NO, ORD_DTL_DATE, 
										ORD_DTL_RESULT, UPDATE_USER, UPDATE_DATE
				  		 from 		PER_ORDER_POS_DTL 
						 where 	ORD_ID=$ORD_ID and ORD_SEQ=$ORD_SEQ ";
		$db_dpis->send_cmd($cmd);
//		echo "cmd=$cmd<br>";
		if ($data = $db_dpis->get_array()) {
			$ORD_SEQ = $data[ORD_SEQ];
			$ORD_POS_NO_NAME = $data[ORD_POS_NO_NAME];
			$ORD_POS_NO = $data[ORD_POS_NO];
			$ORD_EFF_DATE = show_date_format($data[ORD_EFF_DATE], 1);
			$ORD_RESULT = $data[ORD_RESULT];		
			$ORD_REMARK = $data[ORD_REMARK];		
			$ORD_DTL_NO = $data[ORD_DTL_NO];		
			$ORD_DTL_DATE = show_date_format($data[ORD_DTL_DATE], 1);
			$ORD_DTL_RESULT = $data[ORD_DTL_RESULT];		
			$UPDATE_USER = $data[UPDATE_USER];
			$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
			$db->send_cmd($cmd);
			$data2 = $db->get_array();
			$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
			$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);
		
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
		
			$LEVEL_NO = $data[LEVEL_NO];
			$cmd = " select LEVEL_NAME from PER_LEVEL where LEVEL_NO='$LEVEL_NO' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$LEVEL_NAME = $data2[LEVEL_NAME];

			$PT_CODE = $data[PT_CODE];
			$cmd = " select PT_NAME from PER_TYPE where PT_CODE='$PT_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PT_NAME = $data2[PT_NAME];

			$SKILL_CODE = $data[SKILL_CODE];
			$cmd = " select a.SKILL_NAME, a.SG_CODE, b.SG_NAME from PER_SKILL a, PER_SKILL_GROUP b 
							  where a.SKILL_CODE='$SKILL_CODE' and a.SG_CODE=b.SG_CODE ";
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

			$ORD_CONDITION = $data[ORD_CONDITION];
			$ORD_SALARY = $data[ORD_SALARY];
			$ORD_MGTSALARY = $data[ORD_MGTSALARY];

			$ORG_ID = $data[ORG_ID];
			$ORG_ID_1 = $data[ORG_ID_1];
			$ORG_ID_2 = $data[ORG_ID_2];
			$ORG_ID_3 = $data[ORG_ID_3];
			$ORG_ID_4 = $data[ORG_ID_4];
			$ORG_ID_5 = $data[ORG_ID_5];
			if ($ORG_ID_5) $TMP_ORG_ID = $ORG_ID_5;
			elseif ($ORG_ID_4) $TMP_ORG_ID = $ORG_ID_4;
			elseif ($ORG_ID_3) $TMP_ORG_ID = $ORG_ID_3;
			elseif ($ORG_ID_2) $TMP_ORG_ID = $ORG_ID_2;
			elseif ($ORG_ID_1) $TMP_ORG_ID = $ORG_ID_1;
			elseif ($ORG_ID) $TMP_ORG_ID = $ORG_ID;
			$cmd = " select ORG_NAME, CT_CODE, PV_CODE, AP_CODE, OT_CODE from PER_ORG where ORG_ID=$TMP_ORG_ID ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
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

			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_NAME = $data2[ORG_NAME];

			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_1 ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_NAME_1 = $data2[ORG_NAME];

			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_2 ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_NAME_2 = $data2[ORG_NAME];

			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_3 ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_NAME_3 = $data2[ORG_NAME];

			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_4 ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_NAME_4 = $data2[ORG_NAME];

			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_5 ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_NAME_5 = $data2[ORG_NAME];

			$POS_ID_OLD = $data[POS_ID_OLD];		
			$cmd = " select 	OT_CODE, PL_CODE, PM_CODE, CL_NAME,  PT_CODE, SKILL_CODE, PC_CODE, POS_CONDITION,
											POS_SALARY, POS_MGTSALARY, ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5, LEVEL_NO, POS_REMARK
					  		 from 		PER_POSITION 
							 where 	POS_ID=$POS_ID_OLD 	  ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();

			$OLD_OT_CODE = $data[OT_CODE];
			$cmd = " select OT_NAME from PER_OFF_TYPE where OT_CODE='$OLD_OT_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$OLD_OT_NAME = $data2[OT_NAME];

			$OLD_PL_CODE = $data[PL_CODE];
			$cmd = " select PL_NAME from PER_LINE where PL_CODE='$OLD_PL_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$OLD_PL_NAME = $data2[PL_NAME];

			$OLD_PM_CODE = $data[PM_CODE];
			$cmd = " select PM_NAME from PER_MGT where PM_CODE='$OLD_PM_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$OLD_PM_NAME = $data2[PM_NAME];

			$OLD_CL_NAME = $data[CL_NAME];
			$OLD_CL_CODE = $OLD_CL_NAME;
		
			$OLD_LEVEL_NO = $data[LEVEL_NO];
			$cmd = " select LEVEL_NAME from PER_LEVEL where LEVEL_NO='$OLD_LEVEL_NO' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$OLD_LEVEL_NAME = $data2[LEVEL_NAME];

			$OLD_PT_CODE = $data[PT_CODE];
			$cmd = " select PT_NAME from PER_TYPE where PT_CODE='$OLD_PT_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$OLD_PT_NAME = $data2[PT_NAME];

			$OLD_SKILL_CODE = $data[SKILL_CODE];
			$cmd = " select a.SKILL_NAME, a.SG_CODE, b.SG_NAME from PER_SKILL a, PER_SKILL_GROUP b 
							  where a.SKILL_CODE='$OLD_SKILL_CODE' and a.SG_CODE=b.SG_CODE ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$OLD_SKILL_NAME = $data2[SKILL_NAME];
			$OLD_SG_CODE = $data2[SG_CODE];
			$OLD_SG_NAME = $data2[SG_NAME];

			$OLD_PC_CODE = $data[PC_CODE];
			$cmd = " select PC_NAME from PER_CONDITION where PC_CODE='$OLD_PC_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$OLD_PC_NAME = $data2[PC_NAME];

			$OLD_POS_CONDITION = $data[POS_CONDITION];
			$OLD_POS_REMARK = $data[POS_REMARK];
			$OLD_POS_SALARY = $data[POS_SALARY];
			$OLD_POS_MGTSALARY = $data[POS_MGTSALARY];

			$OLD_ORG_ID = $data[ORG_ID];
			$OLD_ORG_ID_1 = $data[ORG_ID_1];
			$OLD_ORG_ID_2 = $data[ORG_ID_2];
			$OLD_ORG_ID_3 = $data[ORG_ID_3];
			$OLD_ORG_ID_4 = $data[ORG_ID_4];
			$OLD_ORG_ID_5 = $data[ORG_ID_5];
			if ($OLD_ORG_ID_5) $TMP_ORG_ID = $OLD_ORG_ID_5;
			elseif ($OLD_ORG_ID_4) $TMP_ORG_ID = $OLD_ORG_ID_4;
			elseif ($OLD_ORG_ID_3) $TMP_ORG_ID = $OLD_ORG_ID_3;
			elseif ($OLD_ORG_ID_2) $TMP_ORG_ID = $OLD_ORG_ID_2;
			elseif ($OLD_ORG_ID_1) $TMP_ORG_ID = $OLD_ORG_ID_1;
			elseif ($OLD_ORG_ID) $TMP_ORG_ID = $OLD_ORG_ID;
			$cmd = " select ORG_NAME, CT_CODE, PV_CODE, AP_CODE, OT_CODE from PER_ORG where ORG_ID=$TMP_ORG_ID ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$OLD_CT_CODE = $data2[CT_CODE];
			$OLD_PV_CODE = $data2[PV_CODE];
			$OLD_AP_CODE = $data2[AP_CODE];
			$OLD_ORG_OT_CODE = $data2[OT_CODE];

			$cmd = " select CT_NAME from PER_COUNTRY where CT_CODE='$OLD_CT_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$OLD_CT_NAME = $data2[CT_NAME];

			$cmd = " select PV_NAME from PER_PROVINCE where PV_CODE='$OLD_PV_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$OLD_PV_NAME = $data2[PV_NAME];

			$cmd = " select AP_NAME from PER_AMPHUR where AP_CODE='$OLD_AP_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$OLD_AP_NAME = $data2[AP_NAME];

			$cmd = " select OT_NAME from PER_ORG_TYPE where OT_CODE='$OLD_ORG_OT_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$OLD_ORG_OT_NAME = $data2[OT_NAME];

			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$OLD_ORG_ID ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$OLD_ORG_NAME = $data2[ORG_NAME];

			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$OLD_ORG_ID_1 ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$OLD_ORG_NAME_1 = $data2[ORG_NAME];

			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$OLD_ORG_ID_2 ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$OLD_ORG_NAME_2 = $data2[ORG_NAME];

			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$OLD_ORG_ID_3 ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$OLD_ORG_NAME_3 = $data2[ORG_NAME];

			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$OLD_ORG_ID_4 ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$OLD_ORG_NAME_4 = $data2[ORG_NAME];
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$OLD_ORG_ID_5 ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$OLD_ORG_NAME_5 = $data2[ORG_NAME];
		} else { // อ่าน PER_ORDER_POS_DTL ไม่พบ
			$UPD_DTL = "";
			$DEL = "";
			$VIEW_DTL = "";
		}
	} // end if
	
	if( (!$UPD_DTL && !$DEL && !$VIEW_DTL && !$err_text) ){
		$ORD_SEQ = "";
		$cmd = " select max(ORD_SEQ) as MAX_SEQ from PER_ORDER_POS_DTL where ORD_ID=$ORD_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$ORD_SEQ = $data[max_seq] + 1;
//		echo "NEW_SEQ=$ORD_SEQ cmd=$cmd<br>";
		
		$ORD_POS_NO_NAME = "";
		$ORD_POS_NO = "";
		$ORD_EFF_DATE = show_date_format($ORD_EFF_DATE, 1);
		$ORD_RESULT = "1";
		$ORD_REMARK = "";
		$POS_ID_OLD = "";
		$ORD_DTL_NO = "";
		$ORD_DTL_DATE = show_date_format($ORD_DTL_DATE, 1);
		$ORD_DTL_RESULT = "1";
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
		
		$OT_NAME = "";
		$OT_CODE = "";
		$PL_NAME = "";
		$PL_CODE = "";
		$PM_NAME = "";
		$PM_CODE = "";
		$CL_NAME = "";
		$CL_CODE = "";
		$LEVEL_NAME = "";
		$LEVEL_NO = "";
		$PT_NAME = "";
		$PT_CODE = "";
		$SG_NAME = "";
		$SG_CODE = "";
		$SKILL_NAME = "";
		$SKILL_CODE = "";
		$PC_NAME = "";
		$PC_CODE = "";
		$ORD_CONDITION = "";
		$ORD_SALARY = "";
		$ORD_MGTSALARY = "";
		$ORG_NAME = "";
		$ORG_ID = "";
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
		$PV_NAME = "";
		$PV_CODE = "";
		$AP_NAME = "";
		$AP_CODE = "";
		$ORG_OT_CODE = "";
		$ORG_OT_NAME = "";

		$OLD_OT_NAME = "";
		$OLD_PL_NAME = "";
		$OLD_PM_NAME = "";
		$OLD_CL_NAME = "";
		$OLD_LEVEL_NAME = "";
		$OLD_PT_NAME = "";
		$OLD_SG_NAME = "";
		$OLD_SKILL_NAME = "";
		$OLD_PC_NAME = "";
		$OLD_POS_CONDITION = "";
		$OLD_POS_SALARY = "";
		$OLD_POS_MGTSALARY = "";
		$OLD_ORG_NAME = "";
		$OLD_ORG_NAME_1 = "";
		$OLD_ORG_NAME_2 = "";
		$OLD_ORG_NAME_3 = "";
		$OLD_ORG_NAME_4 = "";
		$OLD_ORG_NAME_5 = "";
		$OLD_CT_NAME = "";
		$OLD_PV_NAME = "";
		$OLD_AP_NAME = "";
		$OLD_ORG_OT_NAME = "";
	} // end if
?>