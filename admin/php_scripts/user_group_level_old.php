<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	
	if($command == "UPDATE"){
		if($db_type=="mysql") {
			$update_date = "NOW()";
			$update_by = "'$SESS_USERNAME'";
		} elseif($db_type=="mssql") {
			$update_date = "GETDATE()";
			$update_by = $SESS_USERID;
		} elseif($db_type=="oci8" || $db_type=="odbc") {
			$update_date = date("Y-m-d H:i:s");
			$update_by = $SESS_USERID;
		}
		switch($group_level){
			case 1 :
				$CH_PROVINCE_CODE = "";
				$CH_ORG_ID = 0;
				break;
			case 2 :
				$CH_ORG_ID = 0;
				break;
			case 3 :
				$CH_PROVINCE_CODE = "";
				$CH_ORG_ID = $CH_MINISTRY_ID;
				break;
			case 4 :
				$CH_PROVINCE_CODE = "";
				$CH_ORG_ID = $CH_DEPARTMENT_ID;
				break;
			case 5 :
				$CH_PROVINCE_CODE = "";
				$CH_ORG_ID = $CH_ORG_ID;
				break;
			case 6 :
				$CH_PROVINCE_CODE = "";
				$CH_ORG_ID = $CH_ORG_ID_1;
				break;
		} // end switch case
  
		$cmd = " update user_group set 
							group_level = $group_level,
							pv_code = '$CH_PROVINCE_CODE',
							org_id = $CH_ORG_ID,
							update_by = $update_by,
							update_date = '$update_date',
							group_per_type=$group_per_type,
							group_org_structure=$group_org_structure
						 where id=$group_id ";
		$db->send_cmd($cmd);
//		$db->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [$group_id : $PROVINCE_CODE => $CH_PROVINCE_CODE ; $MINISTRY_ID => $CH_MINISTRY_ID ; $DEPARTMENT_ID => $CH_DEPARTMENT_ID ; $ORG_ID => $CH_ORG_ID]");
	}	//end if($command == "UPDATE")	
	
	if(trim($group_id)){	//เพื่อแสดงข้อมุล
		$cmd = " select code, name_th, group_level, pv_code, org_id,group_per_type, group_org_structure from user_group where id=$group_id ";
		$db->send_cmd($cmd);
	//	echo $cmd;
	//	$db->show_error();
		$data = $db->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$group_code = $data[code];
		$group_name = $data[name_th];
		$group_level = $data[group_level];
		$group_per_type = $data[group_per_type];
		$group_org_structure = $data[group_org_structure];
		if(!$group_level) $group_level = 4;
		$group_pv_code = $data[pv_code];
		$group_org_id = $data[org_id];
	}

	// =================================== PER_CONTROL ==================================//
	$cmd = " select CTRL_TYPE, PV_CODE, ORG_ID from PER_CONTROL ";
	$db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data = $db_dpis->get_array();
	$CTRL_TYPE = $data[CTRL_TYPE];
	if(!$CTRL_TYPE) $CTRL_TYPE = 4;
	
	switch($CTRL_TYPE){
		case 2 :
			$CTRL_PROVINCE_CODE = $data[PV_CODE];
			break;
		case 3 :
			$CTRL_MINISTRY_ID = $data[ORG_ID];
			break;
		case 4 :
			$CTRL_DEPARTMENT_ID = $data[ORG_ID];
			break;
		case 5 :
			$CTRL_ORG_ID = $data[ORG_ID];
			break;
		case 6 :
			$CTRL_ORG_ID_1 = $data[ORG_ID];
			break;
	} // end switch case

	if($CTRL_ORG_ID_1){
		$cmd = " select ORG_NAME, ORG_ID_REF from PER_ORG where ORG_ID=$CTRL_ORG_ID_1 ";
		if($group_org_structure==1){	$cmd = str_replace("PER_ORG","PER_ORG_ASS",$cmd);	}
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$CTRL_ORG_NAME_1 = $data[ORG_NAME];
		$CTRL_ORG_ID = $data[ORG_ID_REF];	
	} // end if
	
	if($CTRL_ORG_ID){
		$cmd = " select ORG_NAME, ORG_ID_REF from PER_ORG where ORG_ID=$CTRL_ORG_ID ";
		if($group_org_structure==1){	$cmd = str_replace("PER_ORG","PER_ORG_ASS",$cmd);	}
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$CTRL_ORG_NAME = $data[ORG_NAME];
		$CTRL_DEPARTMENT_ID = $data[ORG_ID_REF];	
	} // end if

	if($CTRL_DEPARTMENT_ID){
		$cmd = " select ORG_NAME, ORG_ID_REF from PER_ORG where ORG_ID=$CTRL_DEPARTMENT_ID ";
		if($group_org_structure==1){	$cmd = str_replace("PER_ORG","PER_ORG_ASS",$cmd);	}
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$CTRL_DEPARTMENT_NAME = $data[ORG_NAME];
		$CTRL_MINISTRY_ID = $data[ORG_ID_REF];	
	} // end if

	if($CTRL_MINISTRY_ID){
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$CTRL_MINISTRY_ID ";
		if($group_org_structure==1){	$cmd = str_replace("PER_ORG","PER_ORG_ASS",$cmd);	}
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$CTRL_MINISTRY_NAME = $data[ORG_NAME];
	} // end if

	if($CTRL_PROVINCE_CODE){
		$cmd = " select PV_NAME from PER_PROVINCE where PV_CODE='$CTRL_PROVINCE_CODE' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$CTRL_PROVINCE_NAME = $data[PV_NAME];
	} // end if
	// =================================== PER_CONTROL ==================================//	

//	echo "$CTRL_TYPE :: $ctrl_type<br>";
	if(!$command){
		switch($CTRL_TYPE){
			case 2 :
				$CTRL_PROVINCE_CODE = $CTRL_PROVINCE_CODE;
				$CTRL_PROVINCE_NAME = $CTRL_PROVINCE_NAME;
				break;
			case 3 :
				$CTRL_MINISTRY_ID = $CTRL_MINISTRY_ID;
				$CTRL_MINISTRY_NAME = $CTRL_MINISTRY_NAME;
				break;
			case 4 :
				$CTRL_MINISTRY_ID = $CTRL_MINISTRY_ID;
				$CTRL_MINISTRY_NAME = $CTRL_MINISTRY_NAME;
				$CTRL_DEPARTMENT_ID = $CTRL_DEPARTMENT_ID;
				$CTRL_DEPARTMENT_NAME = $CTRL_DEPARTMENT_NAME;
				break;
			case 5 :
				$CTRL_MINISTRY_ID = $CTRL_MINISTRY_ID;
				$CTRL_MINISTRY_NAME = $CTRL_MINISTRY_NAME;
				$CTRL_DEPARTMENT_ID = $CTRL_DEPARTMENT_ID;
				$CTRL_DEPARTMENT_NAME = $CTRL_DEPARTMENT_NAME;
				$CTRL_ORG_ID = $CTRL_ORG_ID;
				$CTRL_ORG_NAME = $CTRL_ORG_NAME;
				break;
			case 6 :
				$CTRL_MINISTRY_ID = $CTRL_MINISTRY_ID;
				$CTRL_MINISTRY_NAME = $CTRL_MINISTRY_NAME;
				$CTRL_DEPARTMENT_ID = $CTRL_DEPARTMENT_ID;
				$CTRL_DEPARTMENT_NAME = $CTRL_DEPARTMENT_NAME;
				$CTRL_ORG_ID = $CTRL_ORG_ID;
				$CTRL_ORG_NAME = $CTRL_ORG_NAME;
				$CTRL_ORG_ID_1 = $CTRL_ORG_ID_1;
				$CTRL_ORG_NAME_1 = $CTRL_ORG_NAME_1;
				break;
		} // end switch case
	} // end if

		if($SESS_USERGROUP_LEVEL != $group_level){
			session_unregister("SESS_USERGROUP_LEVEL");
			$SESS_USERGROUP_LEVEL = $group_level;
			session_register("SESS_USERGROUP_LEVEL");
		} // end if
		if($SESS_PROVINCE_CODE != $CH_PROVINCE_CODE){
			session_unregister("SESS_PROVINCE_CODE");
			session_unregister("SESS_PROVINCE_NAME");
			if($group_level==2){ 
				$SESS_PROVINCE_CODE = $CH_PROVINCE_CODE;	
				$SESS_PROVINCE_NAME = $PROVINCE_NAME;	
			}else{ 
				$SESS_PROVINCE_CODE = "";
				$SESS_PROVINCE_NAME = "";
			} // end if
			session_register("SESS_PROVINCE_CODE");
			session_register("SESS_PROVINCE_NAME");
		} // end if
		if($SESS_MINISTRY_ID != $CH_MINISTRY_ID){
			session_unregister("SESS_MINISTRY_ID");
			session_unregister("SESS_MINISTRY_NAME");
			if($group_level==3 || $group_level==4 || $group_level==5){ 
				$SESS_MINISTRY_ID = $CH_MINISTRY_ID;
				$SESS_MINISTRY_NAME = $MINISTRY_NAME;
			}else{ 
				$SESS_MINISTRY_ID = "";
				$SESS_MINISTRY_NAME = "";
			} // end if
			session_register("SESS_MINISTRY_ID");
			session_register("SESS_MINISTRY_NAME");
		} // end if
		if($SESS_DEPARTMENT_ID != $CH_DEPARTMENT_ID){
			session_unregister("SESS_DEPARTMENT_ID");
			session_unregister("SESS_DEPARTMENT_NAME");
			if($group_level==4 || $group_level==5){
				$SESS_DEPARTMENT_ID = $CH_DEPARTMENT_ID;
				$SESS_DEPARTMENT_NAME = $DEPARTMENT_NAME;
			}else{
				$SESS_DEPARTMENT_ID = "";
				$SESS_DEPARTMENT_NAME = "";
			} // end if
			session_register("SESS_DEPARTMENT_ID");
			session_register("SESS_DEPARTMENT_NAME");
		} // end if
		if($SESS_ORG_ID != $CH_ORG_ID){
			session_unregister("SESS_ORG_ID");
			session_unregister("SESS_ORG_NAME");
			if($group_level==5){
				$SESS_ORG_ID = $CH_ORG_ID;
				$SESS_ORG_NAME = $ORG_NAME;
			}else{
				$SESS_ORG_ID = "";
				$SESS_ORG_NAME = "";
			} // end if
			session_register("SESS_ORG_ID");
			session_register("SESS_ORG_NAME");
		} // end if
		if($SESS_ORG_ID_1 != $CH_ORG_ID_1){
				session_unregister("SESS_ORG_ID_1");
				session_unregister("SESS_ORG_NAME_1");
				if($group_level==6){
					$SESS_ORG_ID_1 = $CH_ORG_ID_1;
					$SESS_ORG_NAME_1 = $ORG_NAME_1;
				}else{
					$SESS_ORG_ID_1 = "";
					$SESS_ORG_NAME_1 = "";
				} // end if
				session_register("SESS_ORG_ID1");
				session_register("SESS_ORG_NAME1");
		} // end if

	$PROVINCE_CODE = "";
	$CH_PROVINCE_CODE = "";
	$PROVINCE_NAME = "";
	$MINISTRY_ID = "";
	$CH_MINISTRY_ID = "";
	$MINISTRY_NAME = "";
	$DEPARTMENT_ID = "";
	$CH_DEPARTMENT_ID = "";
	$DEPARTMENT_NAME = "";
	$CH_ORG_ID = "";
	$ORG_NAME = "";
	$CH_ORG_ID_1 = "";
	$ORG_NAME_1 = "";

	switch($group_level){
		case 2 :
			$PROVINCE_CODE = $group_pv_code;
			break;
		case 3 :
			$MINISTRY_ID = $group_org_id;
			break;
		case 4 :
			$DEPARTMENT_ID = $group_org_id;
			break;
		case 5 :
			$ORG_ID = $group_org_id;
			break;
		case 6 :
			$ORG_ID_1 = $group_org_id;
			break;
	} // end switch case
	$SESS_PER_TYPE=$group_per_type;
	session_register("SESS_PER_TYPE");
	
	if($ORG_ID_1){
		$cmd = " select ORG_NAME, ORG_ID_REF from PER_ORG where ORG_ID=$ORG_ID_1 ";
		if($group_org_structure==1){	$cmd = str_replace("PER_ORG","PER_ORG_ASS",$cmd);	}
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$ORG_NAME_1 = $data[ORG_NAME];
		$ORG_ID = $data[ORG_ID_REF];	
	} // end if

	if($ORG_ID){
		$cmd = " select ORG_NAME, ORG_ID_REF from PER_ORG where ORG_ID=$ORG_ID ";
		if($group_org_structure==1){	$cmd = str_replace("PER_ORG","PER_ORG_ASS",$cmd);	}
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$ORG_NAME = $data[ORG_NAME];
		$DEPARTMENT_ID = $data[ORG_ID_REF];	
	} // end if

	if($DEPARTMENT_ID){
		$cmd = " select ORG_NAME, ORG_ID_REF from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
		if($group_org_structure==1){	$cmd = str_replace("PER_ORG","PER_ORG_ASS",$cmd);	}
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$DEPARTMENT_NAME = $data[ORG_NAME];
		$MINISTRY_ID = $data[ORG_ID_REF];	
	} // end if

	if($MINISTRY_ID){
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
		if($group_org_structure==1){	$cmd = str_replace("PER_ORG","PER_ORG_ASS",$cmd);	}
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$MINISTRY_NAME = $data[ORG_NAME];
	} // end if

	if($PROVINCE_CODE){
		$cmd = " select PV_NAME from PER_PROVINCE where PV_CODE='$PROVINCE_CODE' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$PROVINCE_NAME = $data[PV_NAME];
	} // end if

/******
	echo "[[$group_org_id] - $group_org_structure]<br> ";
	echo "CTRL[$CTRL_TYPE] : $CTRL_PROVINCE_CODE=>$CTRL_PROVINCE_NAME - $CTRL_MINISTRY_ID=>$CTRL_MINISTRY_NAME - $CTRL_DEPARTMENT_ID=>$CTRL_DEPARTMENT_NAME - $CTRL_ORG_ID=>$CTRL_ORG_NAME - $CTRL_ORG_ID_1=>$CTRL_ORG_NAME_1<br>";
	echo "ORG[$group_level] : $PROVINCE_CODE=>$PROVINCE_NAME - $MINISTRY_ID=>$MINISTRY_NAME - $DEPARTMENT_ID=>$DEPARTMENT_NAME - $ORG_ID=>$ORG_NAME - $ORG_ID_1=>$ORG_NAME_1<br>";
******/
?>