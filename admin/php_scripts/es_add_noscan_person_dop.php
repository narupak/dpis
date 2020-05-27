
<? 
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis_add = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis_del = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
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
	
		$UPDATE_DATE = date("Y-m-d H:i:s");
		$START_DATE_save=$TIME_START;
		$END_DATE_save=$TIME_END;
		$FLAG_PRINT_save=$FLAG_PRINT;
		$REMARK_save=$REMARK;
		
		if($TIME_END){
			$END_DATE_chk = $TIME_END;
		}else{
			$END_DATE_chk = $TIME_START;
		}
		
	
  		if($command=="NEW"){
			$HideID_save= explode(",",$HideID);	
			foreach ($HideID_save as $value) {
				
				
				// ลบข้อมูลเก่า
				// ทำวันเริ่มต้นก่อน
				
				$cmd = " DELETE FROM  TA_SET_EXCEPTPER  
							WHERE END_DATE IS NOT NULL 
							AND CANCEL_FLAG=1
							AND PER_ID=$value  
							AND 	(   (START_DATE  BETWEEN '$START_DATE_save' and '$END_DATE_chk')
                            or  (END_DATE BETWEEN '$START_DATE_save' and '$END_DATE_chk') 
         					or ( '$START_DATE_save'  BETWEEN START_DATE and END_DATE )
    						or ( '$END_DATE_chk'  BETWEEN START_DATE and END_DATE ) 
                            ) ";
				$db_dpis_del->send_cmd($cmd);
				

				$cmd = " select PER_CARDNO,PER_TYPE  from PER_PERSONAL WHERE PER_ID=".$value;
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				$data = array_change_key_case($data, CASE_LOWER);
				$PER_TYPE_save = trim($data[per_type]);
				$PER_CARDNO_save = trim($data[per_cardno]);
				
				
				$cmd = " insert into TA_SET_EXCEPTPER (REC_ID, PER_ID,PER_TYPE, PER_CARDNO, START_DATE, END_DATE,FLAG_PRINT , REMARK,CREATE_USER,CREATE_DATE, UPDATE_USER, UPDATE_DATE)
								  values (TA_SET_EXCEPTPER_SEQ.NEXTVAL, $value,$PER_TYPE_save, '$PER_CARDNO_save', '$START_DATE_save', '$END_DATE_save',$FLAG_PRINT_save, '$REMARK_save', $SESS_USERID, SYSDATE, $SESS_USERID, SYSDATE)   ";
				$db_dpis_add->send_cmd($cmd);
		//		$db_dpis->show_error();
		
				$cmd = " SELECT TA_SET_EXCEPTPER_SEQ.currval AS CURID FROM dual ";
				$db_dpis_add->send_cmd($cmd);
				$data = $db_dpis_add->get_array();
				$REC_ID = $data[CURID];
				
				insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > เพิ่มข้อมูลบุคคลที่ไม่ต้องลงเวลา [$value : $REC_ID : $START_DATE_save]");
				
				
				
				
			} // foreach ($HideID_save as $value) {
		echo "<script type='text/javascript'>parent.refresh_opener('es_add_noscan_person_dop<::>1');</script>";
 	} // end if($command=="NEW"){ 
	
	
	if($command=="SCRIPT"){
			$HideID_save= explode(",",$HideID);
			foreach ($HideID_save as $value) {
				

				$cmd = " select  PER_ID from TA_SET_EXCEPTPER 
							WHERE END_DATE IS NOT NULL 
							AND CANCEL_FLAG=1
							AND PER_ID=$value  
							AND 	(   (START_DATE  BETWEEN '$START_DATE_save' and '$END_DATE_chk')
                            or  (END_DATE BETWEEN '$START_DATE_save' and '$END_DATE_chk') 
         					or ( '$START_DATE_save'  BETWEEN START_DATE and END_DATE )
    						or ( '$END_DATE_chk'  BETWEEN START_DATE and END_DATE ) 
                            ) ";
				$count_duplicate = $db_dpis->send_cmd($cmd);
				if($count_duplicate <= 0){
					
					
					$cmd = " select PER_CARDNO,PER_TYPE  from PER_PERSONAL WHERE PER_ID=".$value;
					$db_dpis->send_cmd($cmd);
					$data = $db_dpis->get_array();
					$data = array_change_key_case($data, CASE_LOWER);
					$PER_TYPE_save = trim($data[per_type]);
					$PER_CARDNO_save = trim($data[per_cardno]);
					
					
					$cmd = " insert into TA_SET_EXCEPTPER (REC_ID, PER_ID,PER_TYPE, PER_CARDNO, START_DATE, END_DATE,FLAG_PRINT , REMARK,CREATE_USER,CREATE_DATE, UPDATE_USER, UPDATE_DATE)
									  values (TA_SET_EXCEPTPER_SEQ.NEXTVAL, $value,$PER_TYPE_save, '$PER_CARDNO_save', '$START_DATE_save', '$END_DATE_save',$FLAG_PRINT_save, '$REMARK_save', $SESS_USERID, SYSDATE, $SESS_USERID, SYSDATE)   ";
					$db_dpis_add->send_cmd($cmd);
			
					$cmd = " SELECT TA_SET_EXCEPTPER_SEQ.currval AS CURID FROM dual ";
					$db_dpis_add->send_cmd($cmd);
					$data = $db_dpis_add->get_array();
					$REC_ID = $data[CURID];
					
					insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > เพิ่มข้อมูลบุคคลที่ไม่ต้องลงเวลา [$value : $REC_ID : $START_DATE_save]");
					
				
				}
				
				
				
				
			} // end foreach ($HideID_save as $value) {
				
				
		echo "<script type='text/javascript'>parent.refresh_opener('es_add_noscan_person_dop<::>1');</script>";

 	} // end if($command=="SCRIPT"){ 
	
	
	
	
	
  
?>