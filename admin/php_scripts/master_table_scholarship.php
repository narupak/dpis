<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$UPDATE_DATE = date("Y-m-d H:i:s");
	
	if ($command == "SETFLAG") {
		$setflagshow =  implode(",",$list_show_id);
//echo "$setflagshow<br>";
		$cmd = " update PER_SCHOLARSHIP set SCH_ACTIVE = 0 where SCH_CODE in (".stripslashes($current_list).") ";
		$db_dpis->send_cmd($cmd);
//$db_dpis->show_error(); echo "<br>";
		$cmd = " update PER_SCHOLARSHIP set SCH_ACTIVE = 1 where SCH_CODE in (".stripslashes($setflagshow).") ";
		$db_dpis->send_cmd($cmd);
//$db_dpis->show_error(); echo "<br>";
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการใช้งานข้อมูล");
	}	

	if ($command == "ADD" || $command == "UPDATE") {
		$SCH_START_DATE =  save_date($SCH_START_DATE);
		$SCH_END_DATE =  save_date($SCH_END_DATE);
		$SCH_DOC_DATE =  save_date($SCH_DOC_DATE);
		$SCH_APP_DATE =  save_date($SCH_APP_DATE);
		$SCH_START_DATE2 =  save_date($SCH_START_DATE2);
		$SCH_END_DATE2 =  save_date($SCH_END_DATE2);
		$SCH_DEAD_LINE =  save_date($SCH_DEAD_LINE);
		if (!$SCH_CLASS) $SCH_CLASS = "NULL";
		if (!$SCH_BUDGET) $SCH_BUDGET = "NULL";
		if (!$SCH_APP_PER_ID) $SCH_APP_PER_ID = "NULL";
		
	}

	if($command == "ADD" && trim($SCH_CODE)){
		$SCH_CODE = trim($SCH_CODE);
		$cmd = " select SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE
				from PER_SCHOLARSHIP 
				where 	SCH_CODE='$SCH_CODE' ";
		$count_duplicate = $db_dpis->send_cmd($cmd);
		if($count_duplicate <= 0){
		$SCH_CODE = trim($SCH_CODE);
			$cmd = " insert into PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
							SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
							CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, 
							SCH_START_DATE2, SCH_END_DATE2, SCH_PLACE2, SCH_DEAD_LINE) 
							values ('$SCH_CODE', '$SCH_NAME', '$ST_CODE', '$SCH_OWNER', $SCH_ACTIVE, $SESS_USERID, '$UPDATE_DATE',
							'$SCH_YEAR', $SCH_TYPE, $SCH_CLASS, '$EN_CODE', '$EM_CODE', '$SCH_START_DATE', '$SCH_END_DATE', '$SCH_PLACE', '$CT_CODE_OWN',
							'$CT_CODE_GO', $SCH_BUDGET, '$SCH_APP_DOC_NO', '$SCH_DOC_DATE', '$SCH_APP_DATE', $SCH_APP_PER_ID, '$SCH_REMARK', 
							'$SCH_START_DATE2', '$SCH_END_DATE2', '$SCH_PLACE2', '$SCH_DEAD_LINE') ";
			$db_dpis->send_cmd($cmd);
			//echo $cmd;
			//$db_dpis->show_error();
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [trim($SCH_CODE) : $SCH_NAME : $ST_CODE]");
		}else{
			$data = $db_dpis->get_array();			
			$err_text = "รหัสข้อมูลซ้ำ [$data[SCH_CODE] $data[SCH_NAME] $data[ST_CODE]]";
		} // endif
	}

	if($command == "UPDATE" && trim($SCH_CODE)){
		$cmd = " update PER_SCHOLARSHIP set 
										SCH_NAME='$SCH_NAME', 
										ST_CODE='$ST_CODE', 
										SCH_OWNER='$SCH_OWNER', 
										SCH_ACTIVE=$SCH_ACTIVE, 
										UPDATE_USER=$SESS_USERID, 
										UPDATE_DATE='$UPDATE_DATE', 
										SCH_YEAR='$SCH_YEAR', 
										SCH_TYPE=$SCH_TYPE, 
										SCH_CLASS=$SCH_CLASS, 
										EN_CODE='$EN_CODE', 
										EM_CODE='$EM_CODE', 
										SCH_START_DATE='$SCH_START_DATE', 
										SCH_END_DATE='$SCH_END_DATE', 
										SCH_PLACE='$SCH_PLACE', 
										CT_CODE_OWN='$CT_CODE_OWN', 
										CT_CODE_GO='$CT_CODE_GO', 
										SCH_BUDGET=$SCH_BUDGET, 
										SCH_APP_DOC_NO='$SCH_APP_DOC_NO', 
										SCH_DOC_DATE='$SCH_DOC_DATE', 
										SCH_APP_DATE='$SCH_APP_DATE', 
										SCH_APP_PER_ID=$SCH_APP_PER_ID, 
										SCH_REMARK='$SCH_REMARK',
										SCH_START_DATE2='$SCH_START_DATE2', 
										SCH_END_DATE2='$SCH_END_DATE2', 
										SCH_PLACE2='$SCH_PLACE2', 
										SCH_DEAD_LINE='$SCH_DEAD_LINE'
				where SCH_CODE='$SCH_CODE' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [trim($SCH_CODE) : $SCH_NAME : $ST_CODE]");
	}
	
	if($command == "DELETE" && trim($SCH_CODE)){
		$cmd = " delete from PER_SCHOLARSHIP where SCH_CODE='$SCH_CODE' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [trim($SCH_CODE) : $SCH_NAME : $ST_CODE]");
	}
	
	if($UPD){
		$cmd = " select 	SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, 
						  SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN, CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, 
						  SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_START_DATE2, SCH_END_DATE2, 
						  SCH_PLACE2, SCH_DEAD_LINE, UPDATE_USER, UPDATE_DATE 
				from 	PER_SCHOLARSHIP 
				where 	SCH_CODE='$SCH_CODE' "; 
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		//$db_dpis->show_error();
		$SCH_NAME = $data[SCH_NAME];
		$ST_CODE = $data[ST_CODE];
		$SCH_OWNER = $data[SCH_OWNER];
		$SCH_ACTIVE = $data[SCH_ACTIVE];
		$SCH_YEAR = $data[SCH_YEAR];
		$SCH_TYPE = $data[SCH_TYPE];
		$SCH_CLASS = $data[SCH_CLASS];
		$EN_CODE = $data[EN_CODE];
		$EM_CODE = $data[EM_CODE];
		$SCH_START_DATE = $data[SCH_START_DATE];
		$SCH_END_DATE = $data[SCH_END_DATE];
		$SCH_PLACE = $data[SCH_PLACE];
		$CT_CODE_OWN = $data[CT_CODE_OWN];
		$CT_CODE_GO = $data[CT_CODE_GO];
		$SCH_BUDGET = $data[SCH_BUDGET];
		$SCH_APP_DOC_NO = $data[SCH_APP_DOC_NO];
		$SCH_DOC_DATE = $data[SCH_DOC_DATE];
		$SCH_APP_DATE = $data[SCH_APP_DATE];
		$SCH_APP_PER_ID = $data[SCH_APP_PER_ID];
		$SCH_REMARK = $data[SCH_REMARK];
		$SCH_START_DATE2 = $data[SCH_START_DATE2];
		$SCH_END_DATE2 = $data[SCH_END_DATE2];
		$SCH_PLACE2 = $data[SCH_PLACE2];
		$SCH_DEAD_LINE = $data[SCH_DEAD_LINE];
		
		$SCH_START_DATE = show_date_format($data[SCH_START_DATE], 1);
		$SCH_END_DATE = show_date_format($data[SCH_END_DATE], 1);
		$SCH_DOC_DATE = show_date_format($data[SCH_DOC_DATE], 1);
		$SCH_APP_DATE = show_date_format($data[SCH_APP_DATE], 1);
		$SCH_START_DATE2 = show_date_format($data[SCH_START_DATE2], 1);
		$SCH_END_DATE2 = show_date_format($data[SCH_END_DATE2], 1);
		$SCH_DEAD_LINE = show_date_format($data[SCH_DEAD_LINE], 1);

		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);

		$ST_NAME = "";
		$cmd = "select ST_NAME from PER_SCHOLARTYPE where ST_CODE= '$ST_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$ST_NAME = $data_dpis2[ST_NAME];
		
		$EN_NAME = "";
		$cmd = "select EN_NAME from PER_EDUCNAME where EN_CODE= '$EN_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$EN_NAME = $data_dpis2[EN_NAME];
		
		$EM_NAME = "";
		$cmd = "select EM_NAME from PER_EDUCMAJOR where EM_CODE= '$EM_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$EM_NAME = $data_dpis2[EM_NAME];
		
		$CT_NAME_OWN = "";
		$cmd = "select CT_NAME from PER_COUNTRY where CT_CODE= '$CT_CODE_OWN' ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$CT_NAME_OWN = $data_dpis2[CT_NAME];
		
		$CT_NAME_GO = "";
		$cmd = "select CT_NAME from PER_COUNTRY where CT_CODE= '$CT_CODE_GO' ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$CT_NAME_GO = $data_dpis2[CT_NAME];
		
		$SCH_APP_PER_NAME = "";
		$cmd = "select PN_NAME, PER_NAME, PER_SURNAME from PER_PERSONAL a, PER_PRENAME b 
						where a.PN_CODE = b.PN_CODE and PER_ID = $SCH_APP_PER_ID ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$SCH_APP_PER_NAME = trim($data_dpis2[PN_NAME]) . trim($data_dpis2[PER_NAME]) . " " . trim($data_dpis2[PER_SURNAME]);
	} // end if
	
	if( (!$UPD && !$DEL && !$err_text) ){
		$SCH_CODE = "";
		$SCH_NAME = "";
		$ST_CODE = "";
		$ST_NAME = "";
		$SCH_OWNER = "";
		$SCH_ACTIVE = 1;
		$SCH_YEAR = "";
		$SCH_TYPE = "";
		$SCH_CLASS = "";
		$EN_CODE = "";
		$EN_NAME = "";
		$EM_CODE = "";
		$EM_NAME = "";
		$SCH_START_DATE = "";
		$SCH_END_DATE = "";
		$SCH_PLACE = "";
		$CT_CODE_OWN = "";
		$CT_NAME_OWN = "";
		$CT_CODE_GO = "";
		$CT_NAME_GO = "";
		$SCH_BUDGET = "";
		$SCH_APP_DOC_NO = "";
		$SCH_DOC_DATE = "";
		$SCH_APP_DATE = "";
		$SCH_APP_PER_ID = "";
		$SCH_APP_PER_NAME = "";
		$SCH_REMARK = "";
		$SCH_START_DATE2 = "";
		$SCH_END_DATE2 = "";
		$SCH_PLACE2 = "";
		$SCH_DEAD_LINE = "";
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
	} // end if
?>