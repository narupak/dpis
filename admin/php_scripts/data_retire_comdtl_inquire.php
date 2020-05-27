<?
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");	
	include("php_scripts/function_list.php");		

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$UPDATE_DATE = date("Y-m-d H:i:s");
	
	if( $command == "ADD" && trim(!$INV_ID) ){
		$cmd = " select max(INV_ID) as max_id from PER_INVEST1 ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$INV_ID = $data[max_id] + 1;

		$CO_STARTDATE =  (substr(trim($CO_STARTDATE), 6, 4) - 543) ."-". substr(trim($CO_STARTDATE), 3, 2) ."-". substr(trim($CO_STARTDATE), 0, 2);
		$CO_ENDDATE =  (substr(trim($CO_ENDDATE), 6, 4) - 543) ."-". substr(trim($CO_ENDDATE), 3, 2) ."-". substr(trim($CO_ENDDATE), 0, 2);

		$cmd = " insert into PER_INVEST1 
				(INV_ID, TR_CODE, CO_NO, CO_STARTDATE, CO_ENDDATE, CO_PLACE, CT_CODE, 
				CO_ORG, CO_FUND, CT_CODE_FUND, CO_TYPE, CO_CONFIRM, UPDATE_USER, UPDATE_DATE) 
				VALUES 
				($INV_ID, '$TR_CODE', '$CO_NO', '$CO_STARTDATE', '$CO_ENDDATE', '$CO_PLACE', '$CT_CODE', 
				'$CO_ORG', '$CO_FUND', '$CT_CODE_FUND', $CO_TYPE, $CO_CONFIRM, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ?????????????????? [".trim($INV_ID)." : ".$TR_CODE."]");
	}

	if( $command == "UPDATE" && trim($INV_ID) ) {
		$CO_STARTDATE =  (substr(trim($CO_STARTDATE), 6, 4) - 543) ."-". substr(trim($CO_STARTDATE), 3, 2) ."-". substr(trim($CO_STARTDATE), 0, 2);
		$CO_ENDDATE =  (substr(trim($CO_ENDDATE), 6, 4) - 543) ."-". substr(trim($CO_ENDDATE), 3, 2) ."-". substr(trim($CO_ENDDATE), 0, 2);

		$cmd = " update PER_INVEST1 set  
					TR_CODE='$TR_CODE', CO_NO='$CO_NO', CO_STARTDATE='$CO_STARTDATE', 
					CO_ENDDATE='$CO_ENDDATE', CO_PLACE='$CO_PLACE', CT_CODE='$CT_CODE', 
					CO_ORG='$CO_ORG', CO_FUND='$CO_FUND', CT_CODE_FUND='$CT_CODE_FUND', 
					CO_TYPE=$CO_TYPE, CO_CONFIRM=$CO_CONFIRM, UPDATE_USER=$SESS_USERID, 
					UPDATE_DATE='$UPDATE_DATE' 
				where INV_ID=$INV_ID  ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ?????????????????? [".trim($INV_ID)." : ".$TR_CODE."]");
	}
	
	if($command == "DELETE" && trim($INV_ID) ){
		$cmd = " delete from PER_INVEST1DTL where INV_ID=$INV_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
			
		$cmd = " delete from PER_INVEST1 where INV_ID=$INV_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ??????????????? [".trim($INV_ID)." : ".$TR_CODE."]");
	}


	if($UPD || $VIEW){
		$cmd = " select 	TR_CODE, CO_NO, CO_STARTDATE, CO_ENDDATE, CO_PLACE, CT_CODE, 
						CO_ORG, CO_FUND, CT_CODE_FUND, CO_TYPE, CO_CONFIRM
				from 	PER_INVEST1 
				where 	INV_ID=$INV_ID  "; 
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$CO_NO = trim($data[CO_NO]);
		$CO_PLACE = trim($data[CO_PLACE]);
		$CO_ORG = trim($data[CO_ORG]);
		$CO_FUND = trim($data[CO_FUND]);
		$CO_TYPE = trim($data[CO_TYPE]);
		$CO_CONFIRM = trim($data[CO_CONFIRM]);

		$CO_STARTDATE =  substr(trim($data[CO_STARTDATE]), 8, 2) ."/". substr(trim($data[CO_STARTDATE]), 5, 2) ."/". (substr(trim($data[CO_STARTDATE]), 0, 4) + 543);
		$CO_ENDDATE =  substr(trim($data[CO_ENDDATE]), 8, 2) ."/". substr(trim($data[CO_ENDDATE]), 5, 2) ."/". (substr(trim($data[CO_ENDDATE]), 0, 4) + 543);

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
		$COM_ID = "";
		$PER_TYPE = 1;
	} // end if		
?>