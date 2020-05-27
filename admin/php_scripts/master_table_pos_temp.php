<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");
	
	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
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
	
	if(!isset($show_topic)) $show_topic = 1;
	
	$UPDATE_DATE = date("Y-m-d H:i:s");
	
	if(!trim($ORG_ID_1)) $ORG_ID_1 = "NULL";
	if(!trim($ORG_ID_2)) $ORG_ID_2 = "NULL";
	if(!trim($ORG_ID_3)) $ORG_ID_3 = "NULL";
	if(!trim($ORG_ID_4)) $ORG_ID_4 = "NULL";
	if(!trim($ORG_ID_5)) $ORG_ID_5 = "NULL";
	if(!trim($POT_MIN_SALARY)) $POT_MIN_SALARY = "NULL";
	if(!trim($POT_MAX_SALARY)) $POT_MAX_SALARY = "NULL";
	
	if ($command == "SETFLAG") {
		$setflagshow =  implode(",",$list_show_id);
//		echo "$setflagshow";
		$cmd = " update PER_POS_TEMP set POT_STATUS = 0 where POT_ID in (".stripslashes($current_list).") ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$cmd = " update PER_POS_TEMP set POT_STATUS = 1 where POT_ID in (".stripslashes($setflagshow).") ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการใช้งานข้อมูล");
	}

	if($command=="ADD" && $POT_NO && $DEPARTMENT_ID){
		$cmd = " select POT_ID, POT_NO , POT_STATUS from PER_POS_TEMP where trim(POT_NO)='". trim($POT_NO) ."' and DEPARTMENT_ID=$DEPARTMENT_ID ";
		$count_duplicate = $db_dpis->send_cmd($cmd);
                
                $COMMA =","; 
                $idx = 0;
                $POEM_NO_CHKS = "";
                while ($dataCHK = $db_dpis->get_array()){
                    $CHK_STATUS .=$dataCHK[POT_STATUS].$COMMA;
                    $POT_NO_CHKS = $dataCHK[POT_NO];
                }
                $CHK_EX = explode($COMMA,$CHK_STATUS,-1);
		if($count_duplicate <= 0 || !in_array("1", $CHK_EX)){
			$cmd = " select max(POT_ID) as max_id from PER_POS_TEMP ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);			
			$POT_ID = $data[max_id] + 1;
			
			$cmd = " insert into PER_POS_TEMP (POT_ID, POT_NO, TP_CODE, ORG_ID, ORG_ID_1, ORG_ID_2, 
							  ORG_ID_3, ORG_ID_4, ORG_ID_5, POT_MIN_SALARY, POT_MAX_SALARY, POT_STATUS, 
							  DEPARTMENT_ID, UPDATE_USER, UPDATE_DATE, POT_REMARK, POT_NO_NAME )
							  values ($POT_ID, '$POT_NO', '$TP_CODE', $ORG_ID, $ORG_ID_1, $ORG_ID_2, $ORG_ID_3, 
							  $ORG_ID_4, $ORG_ID_5, $POT_MIN_SALARY, $POT_MAX_SALARY, $POT_STATUS, 
							  $DEPARTMENT_ID, $SESS_USERID, '$UPDATE_DATE', '$POT_REMARK' , '$POT_NO_NAME') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > เพิ่มข้อมูลตำแหน่งลูกจ้างชั่วคราว [ $DEPARTMENT_ID : $POT_ID : $POT_NO ]");
			$action_result = "<p><FONT SIZE='3' COLOR='#0000FF' align='center'>เพิ่มข้อมูลเลขที่ตำแหน่ง ".$POT_NO_NAME.$POT_NO." เรียบร้อยแล้ว</FONT></p>";
			
		}else{
			$data = $db_dpis->get_array();
			$err_text = "เลขที่ตำแหน่งซ้ำ [".$POT_NO_CHKS."]";

			if($ORG_ID_1 == "NULL") $ORG_ID_1 = "";
			if($ORG_ID_2 == "NULL") $ORG_ID_2 = "";
			if($ORG_ID_3 == "NULL") $ORG_ID_3 = "";
			if($ORG_ID_4 == "NULL") $ORG_ID_4 = "";
			if($ORG_ID_5 == "NULL") $ORG_ID_5 = "";
		} // end if
	} // end if

	if($command=="UPDATE" && $POT_ID && $POT_NO && $DEPARTMENT_ID){
		$cmd = " update PER_POS_TEMP set
							POT_NO = '$POT_NO',
							TP_CODE = '$TP_CODE', 
							POT_MIN_SALARY = $POT_MIN_SALARY, 
							POT_MAX_SALARY = $POT_MAX_SALARY, 
							DEPARTMENT_ID = $DEPARTMENT_ID, 
							ORG_ID = $ORG_ID, 
							ORG_ID_1 = $ORG_ID_1, 
							ORG_ID_2 = $ORG_ID_2, 
							ORG_ID_3 = $ORG_ID_3, 
							ORG_ID_4 = $ORG_ID_4, 
							ORG_ID_5 = $ORG_ID_5, 
							POT_REMARK = '$POT_REMARK', 
							POT_STATUS = '$POT_STATUS', 
							POT_NO_NAME = '$POT_NO_NAME',
							UPDATE_USER = $SESS_USERID, 
							UPDATE_DATE = '$UPDATE_DATE'
						 where POT_ID=$POT_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > แก้ไขข้อมูลตำแหน่งลูกจ้างชั่วคราว [ $DEPARTMENT_ID : $POT_ID : $POT_NO ]");
	} // end if

	if($command == "DELETE" && $POT_ID){
		$cmd = " select POT_NO from PER_POS_TEMP where POT_ID=$POT_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$POT_NO = $data[POT_NO];
		
		$cmd = " delete from PER_POS_TEMP where POT_ID=$POT_ID";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูลตำแหน่งลูกจ้างชั่วคราว [$DEPARTMENT_ID : $POT_ID : $POT_NO]");
	} // end if		
	
	if($UPD || $VIEW){
		$cmd = "	select	POT_NO, a.TP_CODE, b.TP_NAME, DEPARTMENT_ID, ORG_ID, ORG_ID_1, 
											ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5, POT_MIN_SALARY, POT_MAX_SALARY, 
											POT_STATUS, POT_REMARK, a.UPDATE_USER, a.UPDATE_DATE, POT_NO_NAME
							from		PER_POS_TEMP a, PER_TEMP_POS_NAME b
							where	a.TP_CODE=b.TP_CODE and POT_ID=$POT_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$POT_NO = trim($data[POT_NO]);
		$POT_NO_NAME  = trim($data[POT_NO_NAME]);
		$TP_CODE = trim($data[TP_CODE]);
		$TP_NAME = trim($data[TP_NAME]);
		
		if($SESS_USERGROUP_LEVEL < 5){
			$ORG_ID = trim($data[ORG_ID]);
			$cmd = " select ORG_NAME, ORG_ID_REF from PER_ORG where ORG_ID=$ORG_ID ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$ORG_NAME = trim($data_dpis2[ORG_NAME]);
		} // end if
		
		if($CTRL_TYPE < 4 && $SESS_USERGROUP_LEVEL < 4){
			$DEPARTMENT_ID = $data[DEPARTMENT_ID];
			$cmd = " select ORG_NAME, ORG_ID_REF from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$DEPARTMENT_NAME = $data_dpis2[ORG_NAME];
			
			if($CTRL_TYPE < 3 && $SESS_USERGROUP_LEVEL < 3){
				$MINISTRY_ID = $data_dpis2[ORG_ID_REF];
				$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
				$db_dpis2->send_cmd($cmd);
				$data_dpis2 = $db_dpis2->get_array();
				$MINISTRY_NAME = $data_dpis2[ORG_NAME];
			} // end if
		} // end if
		
		$ORG_ID_1 = trim($data[ORG_ID_1]);
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_1 ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$ORG_NAME_1 = trim($data_dpis2[ORG_NAME]);

		$ORG_ID_2 = trim($data[ORG_ID_2]); 
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_2 ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$ORG_NAME_2 = trim($data_dpis2[ORG_NAME]);

		$ORG_ID_3 = trim($data[ORG_ID_3]);
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_3 ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$ORG_NAME_3 = trim($data_dpis2[ORG_NAME]);

		$ORG_ID_4 = trim($data[ORG_ID_4]); 
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_4 ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$ORG_NAME_4 = trim($data_dpis2[ORG_NAME]);

		$ORG_ID_5 = trim($data[ORG_ID_5]); 
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_5 ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$ORG_NAME_5 = trim($data_dpis2[ORG_NAME]);

		$POT_MIN_SALARY = trim($data[POT_MIN_SALARY]);
		$POT_MAX_SALARY = trim($data[POT_MAX_SALARY]);
		$POT_STATUS = trim($data[POT_STATUS]);
		$POT_REMARK = trim($data[POT_REMARK]);
		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);
	} // end if

	if( (!$UPD && !$DEL && !$VIEW && !$err_text) ){
		$POT_ID = "";
		$POT_NO = "";
		$TP_CODE = "";
		$TP_NAME = "";
		if($CTRL_TYPE < 3 && $SESS_USERGROUP_LEVEL < 3){
			$MINISTRY_ID = "";
			$MINISTRY_NAME = "";
		} 
		if($CTRL_TYPE < 4 && $SESS_USERGROUP_LEVEL < 4){
			$DEPARTMENT_ID = "";
			$DEPARTMENT_NAME = "";
		}
		if($SESS_USERGROUP_LEVEL < 5){
			$ORG_ID = "";
			$ORG_NAME = "";
		} 
		if($SESS_USERGROUP_LEVEL < 6){
			$ORG_ID_1 = "";
			$ORG_NAME_1 = "";
		}
		$ORG_ID_2 = "";
		$ORG_NAME_2 = "";
		$ORG_ID_3 = "";
		$ORG_NAME_3 = "";
		$ORG_ID_4 = "";
		$ORG_NAME_4 = "";
		$ORG_ID_5 = "";
		$ORG_NAME_5 = "";
		$POT_MIN_SALARY = "";
		$POT_MAX_SALARY = "";
		$POT_STATUS = 1;
		$POT_REMARK = "";
		$SHOW_UPDATE_USER = "";
		$SHOW_UPDATE_DATE = "";
	} // end if	
?>