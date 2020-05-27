<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$UPDATE_DATE = date("Y-m-d H:i:s");

	if (!$SEFT_RATIO) $SEFT_RATIO = "NULL";
	if (!$CHIEF_RATIO) $CHIEF_RATIO = "NULL";
	if (!$FRIEND_RATIO) $FRIEND_RATIO = "NULL";
	if (!$SUB_RATIO) $SUB_RATIO = "NULL";
	
	if($command == "ADD" && trim($POS_TYPE) && trim($POS_NAME)){
		$cmd = " 	SELECT  POS_NAME FROM PER_POS_TYPE WHERE POS_TYPE='$POS_TYPE' ";
		$count_duplicate = $db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		if($count_duplicate <= 0){
			$cmd = " INSERT INTO PER_POS_TYPE (POS_TYPE, POS_NAME, SEFT_RATIO, CHIEF_RATIO, FRIEND_RATIO, SUB_RATIO, 
							  UPDATE_USER, UPDATE_DATE) 
							  VALUES ('$POS_TYPE', '$POS_NAME', $SEFT_RATIO, $CHIEF_RATIO, $FRIEND_RATIO, $SUB_RATIO, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [$POS_TYPE : $POS_NAME]");
		}else{
			$data = $db_dpis->get_array();			
			$err_text = "รหัสข้อมูลซ้ำ [$POS_TYPE $data[POS_NAME]]";
		} // endif
	}

	if($command == "UPDATE" && trim($POS_TYPE)){
		$cmd = " 	UPDATE PER_POS_TYPE SET 
								POS_NAME='$POS_NAME', 
								SEFT_RATIO=$SEFT_RATIO, 
								CHIEF_RATIO=$CHIEF_RATIO, 
								FRIEND_RATIO=$FRIEND_RATIO, 
								SUB_RATIO=$SUB_RATIO, 
								UPDATE_USER=$SESS_USERID, 
								UPDATE_DATE='$UPDATE_DATE' 
						WHERE POS_TYPE='$POS_TYPE' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [$POS_TYPE : $POS_NAME]");
	}

	if($command == "DELETE" && trim($POS_TYPE)){
		$cmd = " delete from PER_POS_TYPE where POS_TYPE='$POS_TYPE' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [$POS_TYPE : $POS_NAME]");
	}
	
	if($UPD){
		$cmd = "	SELECT 	POS_NAME, SEFT_RATIO, CHIEF_RATIO, FRIEND_RATIO, SUB_RATIO, UPDATE_USER, UPDATE_DATE 
							FROM 		PER_POS_TYPE
							WHERE 	POS_TYPE='$POS_TYPE' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$POS_NAME = $data[POS_NAME];
		$SEFT_RATIO = $data[SEFT_RATIO];
		$CHIEF_RATIO = $data[CHIEF_RATIO];
		$FRIEND_RATIO = $data[FRIEND_RATIO];
		$SUB_RATIO = $data[SUB_RATIO];
		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);
	} // end if
	
	if( (!$UPD && !$DEL && !$err_text) ){
		$POS_TYPE = "";
		$POS_NAME = "";
		$SEFT_RATIO = "";
		$CHIEF_RATIO = "";
		$FRIEND_RATIO = "";
		$SUB_RATIO = "";
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
	} // end if 
?>