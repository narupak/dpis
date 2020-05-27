<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");	

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$UPDATE_DATE = date("Y-m-d H:i:s");

	if($command == "ADD" && trim($ASS_CODE) && trim($LEVEL_NO_MIN) && trim($LEVEL_NO_MAX)){
		$cmd = " select ASS_CODE, ASS_NAME, LEVEL_NO_MIN, LEVEL_NO_MAX
				  from PER_ASSIGN where ASS_CODE='". trim($ASS_CODE) ."' ";
		$count_duplicate = $db_dpis->send_cmd($cmd);
		
		$cmd = "select LEVEL_NAME from PER_LEVEL where LEVEL_NO='$LEVEL_NO_MIN'";
			$db_dpis->send_cmd($cmd);
			$levelmin = $db_dpis->get_array();
			$LEVEL_NAME_MIN=$levelmin[LEVEL_NAME];
		//	$db_dpis->show_error();
			
			$cmd = "select LEVEL_NAME from PER_LEVEL where LEVEL_NO='$LEVEL_NO_MAX'";
			$db_dpis->send_cmd($cmd);
			$levelmax = $db_dpis->get_array();
			$LEVEL_NAME_MAX=$levelmax[LEVEL_NAME];   
				  
		
		if($count_duplicate <= 0){
			$cmd = " select max(ASS_ID) as max_id from PER_ASSIGN ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$ASS_ID = $data[max_id] + 1;
						
			$cmd = " insert into PER_ASSIGN 
							(ASS_ID, ASS_CODE, ASS_NAME, LEVEL_NO_MIN, LEVEL_NO_MAX, UPDATE_USER, UPDATE_DATE) 
							values 
							($ASS_ID, '".trim($ASS_CODE)."', '".$ASS_NAME."', '".$LEVEL_NO_MIN."', '".$LEVEL_NO_MAX."', $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [".trim($ASS_CODE)." : ".$ASS_NAME." : ".$LEVEL_NO_MIN." : ".$LEVEL_NO_MAX."]");
		}else{
			$data = $db_dpis->get_array();			
			$err_text = "รหัสข้อมูลซ้ำ [".$data[ASS_CODE]." ".$data[ASS_NAME]." ".$LEVEL_NAME_MIN." ".$LEVEL_NAME_MAX."]";
		} // endif
	}

	if($command == "UPDATE" && trim($ASS_ID) && trim($ASS_CODE) && trim($LEVEL_NO_MIN) && trim($LEVEL_NO_MAX)){
		$cmd = " update PER_ASSIGN set ASS_CODE='".$ASS_CODE."', ASS_NAME='".$ASS_NAME."', LEVEL_NO_MIN='".$LEVEL_NO_MIN."', LEVEL_NO_MAX='".$LEVEL_NO_MAX."', UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' where ASS_ID=$ASS_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [".trim($ASS_CODE)." : ".$ASS_NAME." : ".$LEVEL_NO_MIN." : ".$LEVEL_NO_MAX."]");
	}
	
	if($command == "DELETE" && trim($ASS_ID)){
		$cmd = " select ASS_CODE, ASS_NAME, LEVEL_NO_MIN, LEVEL_NO_MAX from PER_ASSIGN where ASS_ID=$ASS_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$ASS_CODE = $data[ASS_CODE];
		$ASS_NAME = $data[ASS_NAME];
		$LEVEL_NO_MIN = $data[LEVEL_NO_MIN];
		$LEVEL_NO_MAX = $data[LEVEL_NO_MAX];
		
		$cmd = " delete from PER_ASSIGN_DTL where ASS_ID=$ASS_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		
		$cmd = " delete from PER_ASSIGN where ASS_ID=$ASS_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [".trim($ASS_CODE)." : ".$ASS_NAME." : ".$LEVEL_NO_MIN." : ".$LEVEL_NO_MAX."]");
	}
	
	if($UPD){
		$cmd = " select ASS_CODE, ASS_NAME, LEVEL_NO_MIN, LEVEL_NO_MAX 
				  from PER_ASSIGN where ASS_ID=$ASS_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$ASS_CODE = $data[ASS_CODE];
		$ASS_NAME = $data[ASS_NAME];
		$LEVEL_NO_MIN = $data[LEVEL_NO_MIN];
		$LEVEL_NO_MAX = $data[LEVEL_NO_MAX];
	} // end if
	
	if( (!$UPD && !$DEL && !$err_text) ){
		$ASS_ID = "";
		$ASS_CODE = "";
		$ASS_NAME = "";
		$LEVEL_NO_MIN = "";
		$LEVEL_NO_MAX = "";
	} // end if
?>