<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");

//	$db_dpis_n = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	// ==== use for testing phase =====
	$db_dpis_n = new connect_db($db_host, $db_name, $db_user, $db_pwd);
	// ==========================

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	//	echo "$CTRL_TYPE	+	$SESS_USERGROUP_LEVEL   [ $SESS_ORG_STRUCTURE  ==> $ORG_ID +++  $ORG_NAME || $search_org_id + $search_org_name ] ";

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	//$data_per_page = 200; /*เดิม*/
	$start_record = ($current_page - 1) * $data_per_page;
	
	$UPDATE_DATE = date("Y-m-d H:i:s");

	switch($CTRL_TYPE){
		case 2 :
			$search_pv_code = $PROVINCE_CODE;
			$search_pv_name = $PROVINCE_NAME;
			$PROVINCE_CODE = $PROVINCE_CODE;
			$PROVINCE_NAME = $PROVINCE_NAME;
			break;
		case 3 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$MAIN_MINISTRY_ID = $MINISTRY_ID;
			$MAIN_MINISTRY_NAME = $MINISTRY_NAME;
			break;
		case 4 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			$MAIN_MINISTRY_ID = $MINISTRY_ID;
			$MAIN_MINISTRY_NAME = $MINISTRY_NAME;
			$MAIN_DEPARTMENT_ID = $DEPARTMENT_ID;
			$MAIN_DEPARTMENT_NAME = $DEPARTMENT_NAME;
			break;
	} // end switch case

	switch($SESS_USERGROUP_LEVEL){								
		case 2 :
			$search_pv_code = $PROVINCE_CODE;
			$search_pv_name = $PROVINCE_NAME;
			$PROVINCE_CODE = $PROVINCE_CODE;
			$PROVINCE_NAME = $PROVINCE_NAME;
			break;
		case 3 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$MAIN_MINISTRY_ID = $MINISTRY_ID;
			$MAIN_MINISTRY_NAME = $MINISTRY_NAME;
			break;
		case 4 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			$MAIN_MINISTRY_ID = $MINISTRY_ID;
			$MAIN_MINISTRY_NAME = $MINISTRY_NAME;
			$MAIN_DEPARTMENT_ID = $DEPARTMENT_ID;
			$MAIN_DEPARTMENT_NAME = $DEPARTMENT_NAME;
			break;
		case 5 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			$search_org_id = $ORG_ID;
			$search_org_name = $ORG_NAME;
			$MAIN_MINISTRY_ID = $MINISTRY_ID;
			$MAIN_MINISTRY_NAME = $MINISTRY_NAME;
			$MAIN_DEPARTMENT_ID = $DEPARTMENT_ID;
			$MAIN_DEPARTMENT_NAME = $DEPARTMENT_NAME;
			$MAIN_ORG_ID = $ORG_ID;
			$MAIN_ORG_NAME = $ORG_NAME;
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
			$MAIN_MINISTRY_ID = $MINISTRY_ID;
			$MAIN_MINISTRY_NAME = $MINISTRY_NAME;
			$MAIN_DEPARTMENT_ID = $DEPARTMENT_ID;
			$MAIN_DEPARTMENT_NAME = $DEPARTMENT_NAME;
			$MAIN_ORG_ID = $ORG_ID;
			$MAIN_ORG_NAME = $ORG_NAME;
			break;
	} // end switch case

	if($command == "UPDATE_ORG_ASS"){
		foreach($ass_per_idx as $key => $per_id_ass) {	//****
			if(!${"department_id_ass_".$per_id_ass}) ${"department_id_ass_".$per_id_ass} = $MAIN_DEPARTMENT_ID;
			if(!${"org_id_ass_".$per_id_ass}) ${"org_id_ass_".$per_id_ass} = "NULL";
			if(!${"org_id_ass_1_".$per_id_ass}) ${"org_id_ass_1_".$per_id_ass} = "NULL";
			if(!${"org_id_ass_2_".$per_id_ass}) ${"org_id_ass_2_".$per_id_ass} = "NULL";
			if(!${"org_id_ass_3_".$per_id_ass}) ${"org_id_ass_3_".$per_id_ass} = "NULL";;
			if(!${"org_id_ass_4_".$per_id_ass}) ${"org_id_ass_4_".$per_id_ass} = "NULL";
			if(!${"org_id_ass_5_".$per_id_ass}) ${"org_id_ass_5_".$per_id_ass} = "NULL";

			$cmd = " update PER_PERSONAL set 
							DEPARTMENT_ID_ASS=${"department_id_ass_".$per_id_ass},
							ORG_ID=${"org_id_ass_".$per_id_ass},
							ORG_ID_1=${"org_id_ass_1_".$per_id_ass},
							ORG_ID_2=${"org_id_ass_2_".$per_id_ass},
							ORG_ID_3=${"org_id_ass_3_".$per_id_ass},
							ORG_ID_4=${"org_id_ass_4_".$per_id_ass},
							ORG_ID_5=${"org_id_ass_5_".$per_id_ass},
							UPDATE_USER=$SESS_USERID, 
							UPDATE_DATE='$UPDATE_DATE' 
						 where PER_ID=$per_id_ass ";
			
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
//			echo $cmd . "<br>";
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขสำนัก/กองตามมอบหมายงาน [".$per_id_ass." : ".$value."]");
		
		}
		$command = "SEARCH";
	}

?>