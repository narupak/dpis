<?
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");	
	include("php_scripts/load_per_control.php");	

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$UPDATE_DATE = date("Y-m-d H:i:s");
	$INV_STATUS = (trim($INV_STATUS))? $INV_STATUS : 1;
	
	if( $command == "ADD" && trim(!$INV_ID) ){
		$cmd = " select max(INV_ID) as max_id from PER_INVEST1 ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$INV_ID = $data[max_id] + 1;
		$INV_DETAIL = str_replace("'", "&rsquo;", $INV_DETAIL);			
		$INV_DESC = str_replace("'", "&rsquo;", $INV_DESC);

		$tmp_INV_DATE =  save_date($INV_DATE);
		
		$cmd = " insert into PER_INVEST1 
				(INV_ID, INV_NO, INV_DATE, INV_DESC, INV_STATUS, CRD_CODE, INV_DETAIL, DEPARTMENT_ID, UPDATE_USER, UPDATE_DATE) 
				VALUES 
				($INV_ID, '$INV_NO', '$tmp_INV_DATE', '$INV_DESC', $INV_STATUS, '$CRD_CODE', '$INV_DETAIL', $DEPARTMENT_ID, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูลการสอบข้อเท็จจริง [".trim($INV_ID)." : ".$INV_NO."]");
	}

	if( $command == "UPDATE" && trim($INV_ID) ) {
		$tmp_INV_DATE =  save_date($INV_DATE);
		$INV_DETAIL = str_replace("'", "&rsquo;", $INV_DETAIL);			
		$INV_DESC = str_replace("'", "&rsquo;", $INV_DESC);

		$cmd = " 	update PER_INVEST1 set  
							INV_ID=$INV_ID, INV_NO='$INV_NO', INV_DATE='$tmp_INV_DATE', INV_DESC='$INV_DESC', 
							INV_STATUS=$INV_STATUS, CRD_CODE='$CRD_CODE', INV_DETAIL='$INV_DETAIL', 
							UPDATE_USER=$SESS_USERID, 
							UPDATE_DATE='$UPDATE_DATE' 
						where INV_ID=$INV_ID  ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูลการสอบข้อเท็จจริง [".trim($INV_ID)." : ".$INV_NO."]");
	}
	
	if($command == "DELETE" && trim($INV_ID) ){
		$cmd = " delete from PER_INVEST1DTL where INV_ID=$INV_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
			
		$cmd = " delete from PER_INVEST1 where INV_ID=$INV_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูลการสอบข้อเท็จจริง [".trim($INV_ID)." : ".$INV_NO."]");
	}

	if($UPD || $VIEW){
		$cmd = " 	select 	INV_ID, INV_NO, INV_DATE, INV_DESC, INV_STATUS, CRD_CODE, INV_DETAIL, DEPARTMENT_ID
						from 		PER_INVEST1 
						where 	INV_ID=$INV_ID  "; 
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$DEPARTMENT_ID = $data[DEPARTMENT_ID];
		$CRD_CODE = trim($data[CRD_CODE]);
		$INV_NO = trim($data[INV_NO]);
		$INV_DESC = trim($data[INV_DESC]);
		$INV_STATUS = trim($data[INV_STATUS]);
		$INV_DETAIL = trim($data[INV_DETAIL]);
		$INV_DATE = show_date_format($data[INV_DATE], 1);

		$cmd = "	select CRD_NAME, CR_NAME from PER_CRIME_DTL a, PER_CRIME b 
						where a.CRD_CODE='$CRD_CODE' and a.CR_CODE=b.CR_CODE ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$CRD_NAME = $data_dpis2[CRD_NAME];		
		$CR_NAME = $data_dpis2[CR_NAME];				

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
		$INV_DESC = "";
		$INV_STATUS = 1;
		$CRD_CODE = "";
		$CRD_NAME = "";
		$CR_NAME = "";
		$INV_DETAIL = "";
		
	} // end if		
?>