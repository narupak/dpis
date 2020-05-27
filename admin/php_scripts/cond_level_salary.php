<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");	

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
		
	$UPDATE_DATE = date("Y-m-d H:i:s");

	if($command == "ADD" && trim($LEVEL_NO) && trim($ASS_SALARY)){
		$cmd = " select LEVEL_NO, ASS_SALARY from PER_ASSIGN_S where LEVEL_NO='". trim($LEVEL_NO) ."' ";
		$count_duplicate = $db_dpis->send_cmd($cmd);
		if($count_duplicate <= 0){
			$cmd = " insert into PER_ASSIGN_S 
							(LEVEL_NO, ASS_SALARY, UPDATE_USER, UPDATE_DATE) 
							values 
							('".trim($LEVEL_NO)."', $ASS_SALARY, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();

			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [".trim($LEVEL_NO)." : ".$ASS_SALARY."]");
		}else{
			$data = $db_dpis->get_array();			
			$err_text = "รหัสข้อมูลซ้ำ [".$data[LEVEL_NO]." ".$data[ASS_SALARY]."]";
		} // endif
	}

	if($command == "UPDATE" && trim($LEVEL_NO) && trim($ASS_SALARY)){
		$cmd = " update PER_ASSIGN_S set LEVEL_NO='$LEVEL_NO', ASS_SALARY=$ASS_SALARY, UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' where LEVEL_NO='$LEVEL_NO' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [".trim($LEVEL_NO)." : ".$ASS_SALARY."]");
	}
	
	if($command == "DELETE" && trim($LEVEL_NO)){
		$cmd = " select ASS_SALARY from PER_ASSIGN_S where LEVEL_NO='$LEVEL_NO' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$ASS_SALARY = $data[ASS_SALARY];
		
		$cmd = " delete from PER_ASSIGN_S where LEVEL_NO='$LEVEL_NO' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [".trim($LEVEL_NO)." : ".$ASS_SALARY."]");
	}
	
	if($UPD){
		$cmd = " select ASS_SALARY from PER_ASSIGN_S where LEVEL_NO='$LEVEL_NO' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$ASS_SALARY = $data[ASS_SALARY];
	} // end if
	
	if( (!$UPD && !$DEL && !$err_text) ){
		$LEVEL_NO = "";
		$ASS_SALARY = "";
	} // end if
?>