<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");	

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$cmd = " select ASS_CODE, ASS_NAME, LEVEL_NO_MIN, LEVEL_NO_MAX from PER_ASSIGN where ASS_ID=$ASS_ID ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$ASS_CODE = $data[ASS_CODE];
	$ASS_NAME = $data[ASS_NAME];
	$LEVEL_NO_MIN = $data[LEVEL_NO_MIN];
	$LEVEL_NO_MAX = $data[LEVEL_NO_MAX];
		
	$UPDATE_DATE = date("Y-m-d H:i:s");

	if($command == "ADD" && trim($ASS_ID) && trim($NEW_PL_CODE)){
		$cmd = " select PL_CODE from PER_ASSIGN_DTL where ASS_ID=$ASS_ID and PL_CODE='". trim($NEW_PL_CODE) ."' ";
		$count_duplicate = $db_dpis->send_cmd($cmd);
		if($count_duplicate <= 0){
			$cmd = " insert into PER_ASSIGN_DTL 
							(ASS_ID, PL_CODE, UPDATE_USER, UPDATE_DATE) 
							values 
							($ASS_ID, '$NEW_PL_CODE', $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();

			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [".trim($ASS_CODE)." : ".$ASS_NAME." : ".$NEW_PL_CODE."]");
		}else{
			$data = $db_dpis->get_array();
			$PL_CODE = trim($data[PL_CODE]);

			$cmd = " select PL_NAME from PER_LINE where PL_CODE='$PL_CODE' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$PL_NAME = $data[PL_NAME];
			
			$err_text = "รหัสข้อมูลซ้ำ [$PL_CODE - $PL_NAME]";
		} // endif
	}

	if($command == "UPDATE" && trim($ASS_ID) && trim($NEW_PL_CODE)){
		$cmd = " update PER_ASSIGN_DTL set PL_CODE='$NEW_PL_CODE', UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' where ASS_ID=$ASS_ID and PL_CODE='$PL_CODE' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [".$PL_CODE." : ".$NEW_PL_CODE."]");
	}
	
	if($command == "DELETE" && trim($ASS_ID) && trim($PL_CODE)){
		$cmd = " select PL_NAME from PER_LINE where PL_CODE='$PL_CODE' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PL_NAME = $data[PL_NAME];
		
		$cmd = " delete from PER_ASSIGN_DTL where ASS_ID=$ASS_ID and PL_CODE='$PL_CODE' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [$ASS_CODE : $ASS_NAME : $PL_CODE : $PL_NAME]");
	}
	
	if($UPD){
		$cmd = " select PL_CODE from PER_ASSIGN_DTL where ASS_ID=$ASS_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$PL_CODE = trim($data[PL_CODE]);
		
		$cmd = " select PL_NAME from PER_LINE where PL_CODE='$PL_CODE' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$PL_NAME = $data[PL_NAME];
	} // end if
	
	if( (!$UPD && !$DEL && !$err_text) ){
		$PL_CODE = "";
		$NEW_PL_CODE = "";
		$PL_NAME = "";
	} // end if
?>