<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$UPDATE_DATE = date("Y-m-d H:i:s");
	if (!$EX_AMT) $EX_AMT = "NULL";

	if($command=="REORDER"){
		foreach($ARR_ORDER as $CODE => $SEQ_NO){
		if($SEQ_NO=="") { $cmd = " update PER_EXTRATYPE set EX_SEQ_NO='' where EX_CODE='$CODE' "; }
		else {	$cmd = " update PER_EXTRATYPE set EX_SEQ_NO=$SEQ_NO where EX_CODE='$CODE' "; }
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			//echo "<br>".$cmd;
		} // end foreach

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > จัดลำดับ [$CODE : $SEQ_NO]");
	} // end if

	if ($command == "SETFLAG") {
		$setflagshow =  implode(",",$list_show_id);
//		echo "$setflagshow";
		$cmd = " update PER_EXTRATYPE set EX_ACTIVE = 0 where EX_CODE in (".stripslashes($current_list).") ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$cmd = " update PER_EXTRATYPE set EX_ACTIVE = 1 where EX_CODE in (".stripslashes($setflagshow).") ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการใช้งานข้อมูล");
	}
	
	if($command == "ADD" && trim($EX_CODE)){
		$cmd = " select EX_CODE, EX_NAME from PER_EXTRATYPE where EX_CODE=trim($EX_CODE) ";
		$count_duplicate = $db_dpis->send_cmd($cmd);
		if($count_duplicate <= 0){
			$cmd = " insert into PER_EXTRATYPE (EX_CODE, EX_NAME, EX_ACTIVE, UPDATE_USER, 
							  UPDATE_DATE, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_OTHERNAME, MGT_FLAG) 
							  values ('$EX_CODE', '$EX_NAME', $EX_ACTIVE, $SESS_USERID, '$UPDATE_DATE',
							  '$EX_SHORTNAME', '$EX_REMARK', $EX_AMT, '$EX_OTHERNAME', $MGT_FLAG) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [".trim($EX_CODE)." : ".$EX_NAME."]");
		}else{
			$data = $db_dpis->get_array();			
			$err_text = "รหัสข้อมูลซ้ำ [".$data[EX_CODE]." ".$data[EX_NAME]."]";
		} // endif
	}

	if($command == "UPDATE" && trim($EX_CODE)){
		$cmd = " update PER_EXTRATYPE set 
							EX_NAME='$EX_NAME', 
							EX_ACTIVE=$EX_ACTIVE, 
							EX_SHORTNAME='$EX_SHORTNAME', 
							EX_REMARK='$EX_REMARK', 
							EX_AMT=$EX_AMT, 
							EX_OTHERNAME='$EX_OTHERNAME', 
							MGT_FLAG=$MGT_FLAG, 
							UPDATE_USER=$SESS_USERID, 
							UPDATE_DATE='$UPDATE_DATE' 
						where EX_CODE='$EX_CODE' ";	 
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [".trim($EX_CODE)." : ".$EX_NAME."]");
	}
	
	if($command == "DELETE" && trim($EX_CODE)){
		$cmd = " select EX_NAME from PER_EXTRATYPE where EX_CODE='$EX_CODE' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$EX_NAME = $data[EX_NAME];
		//echo $cmd;
		
		$cmd = " delete from PER_EXTRATYPE where EX_CODE='$EX_CODE' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [".trim($EX_CODE)." : ".$EX_NAME."]");
	}
	
	if($UPD){
		$cmd = " select EX_NAME, EX_ACTIVE, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_OTHERNAME, MGT_FLAG, UPDATE_USER, UPDATE_DATE
						  from PER_EXTRATYPE where EX_CODE='$EX_CODE' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$EX_NAME = $data[EX_NAME];
		$EX_ACTIVE = $data[EX_ACTIVE];
		$EX_SHORTNAME = $data[EX_SHORTNAME];
		$EX_REMARK = $data[EX_REMARK];
		$EX_AMT = $data[EX_AMT];
		$EX_OTHERNAME = $data[EX_OTHERNAME];
		$MGT_FLAG = $data[MGT_FLAG];
		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);
	} // end if
	
	if( (!$UPD && !$DEL && !$err_text) ){
		$EX_CODE = "";
		$EX_NAME = "";
		$EX_ACTIVE = 1;
		$EX_SHORTNAME = "";
		$EX_REMARK = "";
		$EX_AMT = "";
		$EX_OTHERNAME = "";
		$MGT_FLAG = 0;
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
	} // end if
?>