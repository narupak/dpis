<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;

	$UPDATE_DATE = date("Y-m-d H:i:s");
	$LET_LANG = (trim($LET_LANG))? trim($LET_LANG) : 1; 
	$LET_ASSIGN = (trim($LET_ASSIGN))? trim($LET_ASSIGN) : 0; 
	if ($command == "ADD" || $command == "UPDATE") {
		$LET_DATE =  save_date($LET_DATE);
	}

	if($command == "ADD" && trim(!$LET_ID)){
		$cmd = " select max(LET_ID) as max_id from PER_LETTER ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$LET_ID = $data[max_id] + 1;	
		
		$PER_ID += 0;
		$cmd = " insert into PER_LETTER 
				(LET_ID, LET_NO, PER_ID, LET_REASON, LET_DATE, PER_ID_SIGN1, LET_POSITION, 
				LET_ASSIGN, LET_SIGN, LET_LANG, DEPARTMENT_ID, UPDATE_USER, UPDATE_DATE)
				values 
				($LET_ID, '$LET_NO', $PER_ID, '$LET_REASON', '$LET_DATE', $PER_ID_SIGN1, '$LET_POSITION', 
				'$LET_ASSIGN', '$LET_SIGN', $LET_LANG, $DEPARTMENT_ID, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [".trim($LET_ID)." : ".$LET_NO." : ".$PER_ID." : "."]");
		
		$VIEW = 1;
	}

	if($command == "UPDATE" && trim($LET_ID)){
		$cmd = " update PER_LETTER set 
					LET_NO='$LET_NO', PER_ID=$PER_ID, LET_REASON='$LET_REASON', 
					LET_DATE='$LET_DATE', PER_ID_SIGN1=$PER_ID_SIGN1, LET_POSITION='$LET_POSITION', 
					LET_ASSIGN='$LET_ASSIGN', LET_SIGN='$LET_SIGN', LET_LANG=$LET_LANG,  
					UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' 
				where LET_ID=$LET_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [".trim($LET_ID)." : ".$LET_NO." : ".$PER_ID." : "."]");
	}


	if($command == "DELETE" && trim($LET_ID)){
		$cmd = " delete from PER_LETTER where LET_ID=$LET_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [".trim($LET_ID)." : ".$LET_NO." : ".$PER_ID." : "."]");
	}


	if(($UPD || $VIEW) && $LET_ID){
		$cmd = " select * from PER_LETTER where LET_ID=$LET_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$LET_NO = $data[LET_NO];
		$LET_REASON = trim($data[LET_REASON]);
		$LET_POSITION = trim($data[LET_POSITION]);
		$LET_ASSIGN = trim($data[LET_ASSIGN]);
		$LET_LANG = trim($data[LET_LANG]);
		$LET_SIGN = trim($data[LET_SIGN]);
		$LET_DATE = show_date_format($data[LET_DATE], 1);
		
		$PER_ID = $data[PER_ID];
		$PER_ID_SIGN1 = $data[PER_ID_SIGN1];		

		if ($PER_ID || $PER_ID_SIGN1) {
			$cmd = " select 	PER_ID, PN_NAME, PER_NAME, PER_SURNAME   
					from 	PER_PERSONAL a, PER_PRENAME b  
					where 	PER_ID IN ($PER_ID, $PER_ID_SIGN1) and a.PN_CODE=b.PN_CODE  ";
			$db_dpis2->send_cmd($cmd);
			while ( $data2 = $db_dpis2->get_array() ) {
				if ($PER_ID == trim($data2[PER_ID])) {
					$PER_NAME = $data2[PN_NAME] . $data2[PER_NAME] . " " . $data2[PER_SURNAME];
				}
				if ($PER_ID_SIGN1 == trim($data2[PER_ID])) {
					$PER_NAME_SIGN1 = $data2[PN_NAME] . $data2[PER_NAME] . " " . $data2[PER_SURNAME];
				}
			}
		}
		
		$DEPARTMENT_ID = $data[DEPARTMENT_ID];
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
	
	if( (!$UPD && !$VIEW && !$DEL) ){
		$LET_ID = "";
		$LET_NO = "";
		$PER_ID = "";
		$PER_NAME = "";
		$LET_REASON = "";
		$LET_DATE = "";
		$PER_ID_SIGN1 = "";
		$PER_NAME_SIGN1 = "";
		$LET_POSITION = "";
		$LET_ASSIGN = 1;
		$LET_SIGN = "";
		$LET_LANG = 1;
		
		if($CTRL_TYPE < 3){
			$MINISTRY_ID = "";
			$MINISTRY_NAME = "";
		} // end if
		if($CTRL_TYPE < 4){
			$DEPARTMENT_ID = "";
			$DEPARTMENT_NAME = "";
		} // end if
	} // end if

?>