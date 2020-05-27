<?
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");	

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$UPDATE_DATE = date("Y-m-d H:i:s");
	
	if( $command == "ADD" && trim(!$CO_ID) ){
		$cmd = " select max(CO_ID) as max_id from PER_COURSE ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$CO_ID = $data[max_id] + 1;

		$CO_STARTDATE =  save_date($CO_STARTDATE);
		$CO_ENDDATE =  save_date($CO_ENDDATE);

		$cmd = " insert into PER_COURSE 
				(CO_ID, TR_CODE, CO_NO, CO_STARTDATE, CO_ENDDATE, CO_PLACE, CT_CODE, 
				CO_ORG, CO_FUND, CT_CODE_FUND, CO_TYPE, CO_CONFIRM, UPDATE_USER, UPDATE_DATE) 
				VALUES 
				($CO_ID, '$TR_CODE', '$CO_NO', '$CO_STARTDATE', '$CO_ENDDATE', '$CO_PLACE', '$CT_CODE', 
				'$CO_ORG', '$CO_FUND', '$CT_CODE_FUND', $CO_TYPE, $CO_CONFIRM, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูลการอบรม [".trim($CO_ID)." : ".$TR_CODE."]");
	}

	if( $command == "UPDATE" && trim($CO_ID) ) {
		$CO_STARTDATE =  save_date($CO_STARTDATE);
		$CO_ENDDATE =  save_date($CO_ENDDATE);

		$cmd = " update PER_COURSE set  
					TR_CODE='$TR_CODE', CO_NO='$CO_NO', CO_STARTDATE='$CO_STARTDATE', 
					CO_ENDDATE='$CO_ENDDATE', CO_PLACE='$CO_PLACE', CT_CODE='$CT_CODE', 
					CO_ORG='$CO_ORG', CO_FUND='$CO_FUND', CT_CODE_FUND='$CT_CODE_FUND', 
					CO_TYPE=$CO_TYPE, CO_CONFIRM=$CO_CONFIRM, UPDATE_USER=$SESS_USERID, 
					UPDATE_DATE='$UPDATE_DATE' 
				where CO_ID=$CO_ID  ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูลการอบรม [".trim($CO_ID)." : ".$TR_CODE."]");
	}
	
	if($command == "DELETE" && trim($CO_ID) ){
		$cmd = " delete from PER_COURSEDTL where CO_ID=$CO_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
			
		$cmd = " delete from PER_COURSE where CO_ID=$CO_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูลการอบรม [".trim($CO_ID)." : ".$TR_CODE."]");
	}


	if($UPD || $VIEW){
		$cmd = " select 	TR_CODE, CO_NO, CO_STARTDATE, CO_ENDDATE, CO_PLACE, CT_CODE, 
						CO_ORG, CO_FUND, CT_CODE_FUND, CO_TYPE, CO_CONFIRM
				from 	PER_COURSE 
				where 	CO_ID=$CO_ID  "; 
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$CO_NO = trim($data[CO_NO]);
		$CO_PLACE = trim($data[CO_PLACE]);
		$CO_ORG = trim($data[CO_ORG]);
		$CO_FUND = trim($data[CO_FUND]);
		$CO_TYPE = trim($data[CO_TYPE]);
		$CO_CONFIRM = trim($data[CO_CONFIRM]);

		$CO_STARTDATE = show_date_format($data[CO_STARTDATE], 1);
		$CO_ENDDATE = show_date_format($data[CO_ENDDATE], 1);

		$TR_NAME = $CT_NAME = $CT_NAME_FUND = "";
		$TR_CODE = trim($data[TR_CODE]);
		$cmd = "select TR_NAME from PER_TRAIN where TR_CODE='$TR_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$TR_NAME = $data_dpis2[TR_NAME];
		
		$CT_CODE = trim($data[CT_CODE]);
		$CT_CODE_FUND = trim($data[CT_CODE_FUND]);				
		$cmd = "select CT_CODE, CT_NAME from PER_COUNTRY where CT_CODE='$CT_CODE' or CT_CODE='$CT_CODE_FUND' ";
		$db_dpis2->send_cmd($cmd);
		while ( $data_dpis2 = $db_dpis2->get_array() ) {
			if ( $CT_CODE == $data_dpis2[CT_CODE] )			$CT_NAME = $data_dpis2[CT_NAME];
			if ( $CT_CODE_FUND == $data_dpis2[CT_CODE] )		$CT_NAME_FUND = $data_dpis2[CT_NAME];			
		}
	} // end if
	
	if( !$UPD && !$DEL && !$VIEW ){
		$CO_ID = "";
		$TR_CODE = "";
		$TR_NAME = "";
		$CO_NO = "";
		$CO_STARTDATE = "";
		$CO_ENDDATE = "";
		$CO_PLACE = "";
		$CT_CODE = "";
		$CT_NAME = "";
		$CO_ORG = "";
		$CO_FUND = "";
		$CT_CODE_FUND = "";
		$CT_NAME_FUND = "";
		$CO_TYPE = "";
		$CO_CONFIRM = "";
	} // end if		
?>