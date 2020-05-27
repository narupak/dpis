<?
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");	
	include("php_scripts/load_per_control.php");	

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$UPDATE_DATE = date("Y-m-d H:i:s");
	
	if( $command == "ADD" && trim(!$INV_ID) ){
		$cmd = " select max(INV_ID) as max_id from PER_INVEST2 ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$INV_ID = $data[max_id] + 1;
		$INV_APP_RESULT = str_replace("'", "&rsquo;", $INV_APP_RESULT);
		$INV_ID_REF = (trim($INV_ID_REF))? $INV_ID_REF : 'NULL';
		$INV_DATE =  save_date($INV_DATE);
		$INV_APP_DATE =  save_date($INV_APP_DATE);

		$cmd = " insert into PER_INVEST2 
				(INV_ID, INV_NO, INV_DATE, INV_ID_REF, INV_APPEAL, INV_APP_DATE, INV_APP_RESULT, DEPARTMENT_ID, UPDATE_USER, UPDATE_DATE) 
				VALUES 
				($INV_ID, '$INV_NO', '$INV_DATE', $INV_ID_REF, $INV_APPEAL, '$INV_APP_DATE', '$INV_APP_RESULT', $DEPARTMENT_ID, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูลการสอบทางวินัย [".trim($INV_ID)." : ".$PER_ID."]");
	}

	if( $command == "UPDATE" && trim($INV_ID) ) {
		$INV_APP_RESULT = str_replace("'", "&rsquo;", $INV_APP_RESULT);
		$INV_ID_REF = (trim($INV_ID_REF))? $INV_ID_REF : 'NULL';
		$temp_date = trim($INV_DATE);
		$INV_DATE =  save_date($INV_DATE);
		$INV_APP_DATE =  save_date($INV_APP_DATE);

		$cmd = " update PER_INVEST2 set  
					INV_NO='$INV_NO', INV_DATE='$INV_DATE', INV_ID_REF=$INV_ID_REF, INV_APPEAL=$INV_APPEAL, 
					INV_APP_DATE='$INV_APP_DATE', INV_APP_RESULT='$INV_APP_RESULT',
					UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' 
				where INV_ID=$INV_ID  ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูลการสอบทางวินัย [".trim($INV_ID)." : ".$PER_ID."]");
	}
	
	if($command == "DELETE" && trim($INV_ID) ){
		$cmd = " delete from PER_INVEST2DTL where INV_ID=$INV_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
			
		$cmd = " delete from PER_INVEST2 where INV_ID=$INV_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูลการสอบทางวินัย [".trim($INV_ID)." : ".$PER_ID."]");
	}


	if($UPD || $VIEW){
		$cmd = " 	select 	INV_ID, INV_NO, INV_DATE, INV_ID_REF, INV_APPEAL, INV_APP_DATE, INV_APP_RESULT, DEPARTMENT_ID
				from 	PER_INVEST2 
				where 	INV_ID=$INV_ID  "; 
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$DEPARTMENT_ID = $data[DEPARTMENT_ID];
		$INV_NO = trim($data[INV_NO]);
		$INV_ID_REF = trim($data[INV_ID_REF]);
		$INV_APPEAL = trim($data[INV_APPEAL]);
		$INV_APP_RESULT = trim($data[INV_APP_RESULT]);
		$INV_DATE = show_date_format($data[INV_DATE], 1);
		$INV_APP_DATE = show_date_format($data[INV_APP_DATE], 1);

		$cmd = " select ORG_ID_REF, ORG_NAME from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$DEPARTMENT_NAME = $data[ORG_NAME];
		$MINISTRY_ID = $data[ORG_ID_REF];
		
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MINISTRY_NAME = $data[ORG_NAME];
	} // end if
	
	if( !$UPD && !$DEL && !$VIEW ){
		$INV_ID = "";
		$INV_NO = "";
		$INV_DATE = "";
		$INV_ID_REF = "";
		$INV_REF_NO = "";
		$INV_APPEAL = "";
		$INV_APP_DATE = "";
		$INV_APP_RESULT = "";
		$PER_ID = "";
	} // end if		
?>