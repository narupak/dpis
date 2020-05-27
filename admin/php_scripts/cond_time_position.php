<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");	

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$UPDATE_DATE = date("Y-m-d H:i:s");

	if(trim($ASS_YEAR1) == "") $ASS_YEAR1 = "NULL";
	if(trim($ASS_YEAR2) == "") $ASS_YEAR2 = "NULL";

	if($command == "ADD" && trim($NEW_EL_CODE) && trim($NEW_LEVEL_NO)){
		$cmd = " select EL_CODE, LEVEL_NO
				  from PER_ASSIGN_YEAR where EL_CODE='". trim($NEW_EL_CODE) ."' and LEVEL_NO='". trim($NEW_LEVEL_NO) ."' ";
		$count_duplicate = $db_dpis->send_cmd($cmd);
		if($count_duplicate <= 0){
			$cmd = " insert into PER_ASSIGN_YEAR 
							(EL_CODE, LEVEL_NO, ASS_YEAR1, ASS_YEAR2, UPDATE_USER, UPDATE_DATE) 
							values 
							('".trim($NEW_EL_CODE)."', '".trim($NEW_LEVEL_NO)."', ".$ASS_YEAR1.", ".$ASS_YEAR2.", $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();

			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [".trim($EL_CODE)." : ".$LEVEL_NO." : ".$ASS_YEAR1." : ".$ASS_YEAR2."]");
		}else{
			$data = $db_dpis->get_array();
			$EL_CODE = trim($data[EL_CODE]);
			$LEVEL_NO = trim($data[LEVEL_NO]);

			$cmd = " select EL_NAME from PER_EDUCLEVEL where EL_CODE='$EL_CODE' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$EL_NAME = $data[EL_NAME];

			$cmd = " select LEVEL_NAME from PER_LEVEL where LEVEL_NO='$LEVEL_NO' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$LEVEL_NAME = $data[LEVEL_NAME];

			$err_text = "รหัสข้อมูลซ้ำ [".$EL_CODE." ".$EL_NAME." ".$LEVEL_NAME."]";
		} // endif
	}

	if($command == "UPDATE" && trim($NEW_EL_CODE) && trim($NEW_LEVEL_NO)){
		$cmd = " update PER_ASSIGN_YEAR set EL_CODE='".$NEW_EL_CODE."', LEVEL_NO='".$NEW_LEVEL_NO."', ASS_YEAR1=$ASS_YEAR1, ASS_YEAR2=$ASS_YEAR2, UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' where EL_CODE='$EL_CODE' and LEVEL_NO='$LEVEL_NO' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [".trim($EL_CODE)." => ".$NEW_EL_CODE." : ".$LEVEL_NO." => ".$NEW_LEVEL_NO." : ".$ASS_YEAR1." : ".$ASS_YEAR2."]");
	}
	
	if($command == "DELETE" && trim($EL_CODE) && trim($LEVEL_NO)){
		$cmd = " select EL_NAME from PER_EDUCLEVEL where EL_CODE='$EL_CODE' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$EL_NAME = $data[EL_NAME];

		$cmd = " select LEVEL_NAME from PER_LEVEL where LEVEL_NO='$LEVEL_NO' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$LEVEL_NAME = $data[LEVEL_NAME];
		
		$cmd = " delete from PER_ASSIGN_YEAR where EL_CODE='$EL_CODE' and LEVEL_NO='$LEVEL_NO' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [".$EL_CODE." ".$EL_NAME." ".$LEVEL_NAME."]");
	}
	
	if($UPD){
		$cmd = " select ASS_YEAR1, ASS_YEAR2 
				  from PER_ASSIGN_YEAR where EL_CODE='$EL_CODE' and LEVEL_NO='$LEVEL_NO' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$ASS_YEAR1 = $data[ASS_YEAR1];
		$ASS_YEAR2 = $data[ASS_YEAR2];

		$cmd = " select EL_NAME from PER_EDUCLEVEL where EL_CODE='$EL_CODE' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$EL_NAME = $data[EL_NAME];
	} // end if
	
	if( (!$UPD && !$DEL && !$err_text) ){
		$EL_CODE = "";
		$NEW_EL_CODE = "";
		$EL_NAME = "";
		$LEVEL_NO = "";
		$NEW_LEVEL_NO = "";
		$ASS_YEAR1 = "";
		$ASS_YEAR2 = "";
	} // end if
?>