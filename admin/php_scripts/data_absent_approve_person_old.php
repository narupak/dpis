<?
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");	
	include("php_scripts/load_per_control.php");	

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$UPDATE_DATE = date("Y-m-d H:i:s");
	
	if(!trim($REPLACE_PER_ID)) $REPLACE_PER_ID = "NULL";
	
	if ($command == "SETFLAG") {
		$setflaglist =  implode(",", $list_approver_id);

		$cmd = " update PER_PERSONAL set ABSENT_FLAG = 1 where PER_ID in ($current_list) ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " update PER_PERSONAL set ABSENT_FLAG = NULL where PER_ID in ($setflaglist) ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าสถานะของผู้อนุมัติการลา");
	}

	if( $command == "ADD" && trim($APPROVE_PER_ID) && trim($SELECTED_PER_ID) ){
		$cmd = " update PER_PERSONAL set 
							APPROVE_PER_ID=$APPROVE_PER_ID,
							UPDATE_USER = $SESS_USERID,
							UPDATE_DATE = '$UPDATE_DATE'
						 where PER_ID in ($SELECTED_PER_ID)
					  ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " update PER_PERSONAL set REPLACE_PER_ID = $REPLACE_PER_ID, ABSENT_FLAG = $ABSENT_FLAG where PER_ID = $APPROVE_PER_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูลผู้อนุมัติการลา [$APPROVE_PER_ID : $REPLACE_PER_ID : ($SELECTED_PER_ID)]");
	}

	if( $command == "UPDATE" && trim($APPROVE_PER_ID) && trim($SELECTED_PER_ID) ) {
		$cmd = " select PER_ID from PER_PERSONAL where APPROVE_PER_ID=$APPROVE_PER_ID ";
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $ARR_OLD_APPROVEE[] = $data[PER_ID];
		$OLD_SELECTED_PER_ID = implode(",", $ARR_OLD_APPROVEE);
		
		$cmd = " update PER_PERSONAL set 
							APPROVE_PER_ID = NULL
						 where PER_ID in ($OLD_SELECTED_PER_ID)
					  ";
		$db_dpis->send_cmd($cmd);
	//	$db_dpis->show_error();

		$cmd = " update PER_PERSONAL set 
							APPROVE_PER_ID=$APPROVE_PER_ID,
							UPDATE_USER = $SESS_USERID,
							UPDATE_DATE = '$UPDATE_DATE'
						 where PER_ID in ($SELECTED_PER_ID)
					  ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " update PER_PERSONAL set REPLACE_PER_ID = $REPLACE_PER_ID, ABSENT_FLAG = $ABSENT_FLAG where PER_ID = $APPROVE_PER_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูลผู้อนุมัติการลา [$APPROVE_PER_ID : $REPLACE_PER_ID : ($OLD_SELECTED_PER_ID) -> ($SELECTED_PER_ID)]");
	}
	
	if($command == "DELETE" && trim($APPROVE_PER_ID) ){
		$cmd = " select PER_ID from PER_PERSONAL where APPROVE_PER_ID=$APPROVE_PER_ID ";
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $ARR_APPROVEE[] = $data[PER_ID];
		$SELECTED_PER_ID = implode(",", $ARR_APPROVEE);

		$cmd = " update PER_PERSONAL set 
						 	APPROVE_PER_ID = NULL,
							UPDATE_USER = $SESS_USERID,
							UPDATE_DATE = '$UPDATE_DATE'
						 where PER_ID in ($SELECTED_PER_ID)
					  ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " update PER_PERSONAL set REPLACE_PER_ID = NULL, ABSENT_FLAG = NULL where PER_ID = $APPROVE_PER_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูลผู้อนุมัติการลา  [$APPROVE_PER_ID : $REPLACE_PER_ID : ($SELECTED_PER_ID)]");
	}

	if($UPD || $VIEW){
		$cmd = " select 	b.PN_NAME, a.PER_NAME, a.PER_SURNAME, c.ORG_ID, a.ABSENT_FLAG, a.REPLACE_PER_ID
						 from 		PER_PERSONAL a, PER_PRENAME b, PER_POSITION c
						 where 	a.PN_CODE=b.PN_CODE and a.POS_ID=c.POS_ID and a.PER_ID=$APPROVE_PER_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$APPROVE_PER_NAME = $data[PN_NAME].$data[PER_NAME]." ". $data[PER_SURNAME];
		$ABSENT_FLAG = $data[ABSENT_FLAG];
		$REPLACE_PER_ID = $data[REPLACE_PER_ID];
		$ORG_ID = $data[ORG_ID];
				
		$cmd = " select PN_NAME, PER_NAME, PER_SURNAME from PER_PERSONAL a, PER_PRENAME b where a.PN_CODE=b.PN_CODE and PER_ID=$REPLACE_PER_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$REPLACE_PER_NAME = $data[PN_NAME].$data[PER_NAME]." ". $data[PER_SURNAME];

		$cmd = " select ORG_ID_REF, ORG_NAME from PER_ORG where ORG_ID=$ORG_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$ORG_NAME = $data[ORG_NAME];
		$DEPARTMENT_ID = $data[ORG_ID_REF];
		
		$cmd = " select ORG_ID_REF, ORG_NAME from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$DEPARTMENT_NAME = $data[ORG_NAME];
		$MINISTRY_ID = $data[ORG_ID_REF];

		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MINISTRY_NAME = $data[ORG_NAME];

		$cmd = " select PER_ID from PER_PERSONAL where APPROVE_PER_ID=$APPROVE_PER_ID ";
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $ARR_APPROVEE[] = $data[PER_ID];
		$SELECTED_PER_ID = implode(",", $ARR_APPROVEE);	
	} // end if
	
	if( !$UPD && !$DEL && !$VIEW ){
		$APPROVE_PER_ID = "";
		$APPROVE_PER_NAME = "";
		$REPLACE_PER_ID = "";
		$REPLACE_PER_NAME = "";
		$SELECTED_PER_ID = "";
		$ABSENT_FLAG = "";
		
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
	} // end if	
	
	if($APPROVE_PER_ID)
		$cmd = " select PER_ID from PER_PERSONAL where APPROVE_PER_ID IS NOT NULL and APPROVE_PER_ID<>$APPROVE_PER_ID ";
	else
		$cmd = " select PER_ID from PER_PERSONAL where APPROVE_PER_ID IS NOT NULL ";
	$db_dpis->send_cmd($cmd);
//	echo "$cmd<br>";
//	$db_dpis->show_error();	
	while($data = $db_dpis->get_array()) $ARR_EXCEPT[] = $data[PER_ID];
	$EXCEPT_LIST = implode(",", $ARR_EXCEPT);	
//	echo "$EXCEPT_LIST";
?>