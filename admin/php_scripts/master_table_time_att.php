<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	
	$UPDATE_DATE = date("Y-m-d H:i:s");

	if ($command == "SETFLAG") {
		$setflagshow =  implode(",", $list_show_id);
//echo "$setflagshow<br>";
		$cmd = " update PER_TIME_ATT set TA_ACTIVE = 0 where TA_CODE in (".stripslashes($current_list).") ";
		$db_dpis->send_cmd($cmd);
//$db_dpis->show_error(); echo "<br>";
		$cmd = " update PER_TIME_ATT set TA_ACTIVE = 1 where TA_CODE in (".stripslashes($setflagshow).") ";
		$db_dpis->send_cmd($cmd);
//$db_dpis->show_error(); echo "<br>";

		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > ตั้งค่าการใช้งานข้อมูล");
	}
	
	$tmp_TA_CODE= intval(trim($TA_CODE));
	$tmp_TA_NAME= trim($TA_NAME);
	$tmp_WL_CODE= trim($WL_CODE);
	$tmp_TA_ACTIVE= $TA_ACTIVE;
	$tmp_RESYNC_FLAG= $RESYNC_FLAG;
	
	if($command == "ADD"){
		$cmd = " select TA_CODE,TA_NAME from PER_TIME_ATT where TA_CODE='". trim($tmp_TA_CODE) ."' OR TA_NAME='". $tmp_TA_NAME ."' ";
		$count_duplicate = $db_dpis->send_cmd($cmd);
		if($count_duplicate <= 0){
			$cmd = " insert into PER_TIME_ATT (TA_CODE,TA_NAME,WL_CODE,TA_ACTIVE,UPDATE_USER,UPDATE_DATE,RESYNC_FLAG) 
			      values ('$tmp_TA_CODE', '$tmp_TA_NAME', '$tmp_WL_CODE', ".$tmp_TA_ACTIVE.", $SESS_USERID, '$UPDATE_DATE','$tmp_RESYNC_FLAG') ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > เพิ่มข้อมูล [".$tmp_TA_CODE." : ".$tmp_TA_NAME."]");
		
			echo "<script>window.location='../admin/master_table_time_att.html?MENU_ID_LV0=$MENU_ID_LV0&MENU_ID_LV1=$MENU_ID_LV1&MENU_ID_LV2=$MENU_ID_LV2';</script>";

		}else{
			$data = $db_dpis->get_array();			
			$err_text = "หมายเลขเครื่องหรือชื่อเครื่องบันทึกเวลาซ้ำ [".$data[TA_CODE]." ".$data[TA_NAME]."]";
		} // endif
	}

	if($command == "UPDATE"){
		$cmd = " select TA_CODE,TA_NAME from PER_TIME_ATT where (TA_CODE='". trim($tmp_TA_CODE) ."' OR TA_NAME='". trim($tmp_TA_NAME) ."') AND (TA_NAME!='". $hidTA_NAME ."' AND TA_CODE!='". $HIDTA_CODE ."' )";
		$count_duplicate = $db_dpis->send_cmd($cmd);
		if($count_duplicate <= 0){
			$cmd = " update PER_TIME_ATT set TA_CODE='$tmp_TA_CODE', TA_NAME='$tmp_TA_NAME', WL_CODE='".$tmp_WL_CODE."', TA_ACTIVE=".$tmp_TA_ACTIVE.",  UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE',RESYNC_FLAG='$tmp_RESYNC_FLAG' where TA_CODE='".$HIDTA_CODE."' ";
			$db_dpis->send_cmd($cmd);
	//		$db_dpis->show_error();
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > แก้ไขข้อมูล [".$tmp_TA_CODE." : ".$tmp_TA_NAME."]");
		}else{
			$data = $db_dpis->get_array();			
			$err_text = "หมายเลขเครื่องหรือชื่อเครื่องบันทึกเวลาซ้ำ [".$data[TA_CODE]." ".$data[TA_NAME]."]";
			$UPD=1;
		} // endif
	}
	
	if($command == "DELETE"){
		$cmd = " select TA_NAME from PER_TIME_ATT where TA_CODE='".$tmp_TA_CODE."' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$del_TA_NAME = $data[TA_NAME];
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > ลบข้อมูล [".$tmp_TA_CODE." : ".$del_TA_NAME."]");
		
		
		$cmd = " delete from PER_TIME_ATT where TA_CODE='".$tmp_TA_CODE."'";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		echo "<script>window.location='../admin/master_table_time_att.html?MENU_ID_LV0=$MENU_ID_LV0&MENU_ID_LV1=$MENU_ID_LV1&MENU_ID_LV2=$MENU_ID_LV2';</script>";

	}
	
	if($UPD){
		$cmd = " select a.TA_NAME, a.WL_CODE, TA_ACTIVE, WL_NAME, a.UPDATE_USER, a.UPDATE_DATE,
				a.TA_CODE,a.RESYNC_FLAG
				 from PER_TIME_ATT a, PER_WORK_LOCATION b 
				 where TA_CODE='".$tmp_TA_CODE."' and a.WL_CODE = b.WL_CODE ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$TA_NAME = $data[TA_NAME];
		$WL_CODE = $data[WL_CODE];
		$WL_NAME = $data[WL_NAME];
		$TA_ACTIVE = $data[TA_ACTIVE];
		$RESYNC_FLAG = $data[RESYNC_FLAG];
		
		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);
	} // end if
	
	if( (!$UPD && !$DEL && !$err_text) ){
		$TA_CODE = "";
		$TA_NAME = "";
		$WL_CODE = "";
		$TA_ACTIVE = 1;
		$WL_NAME = "";
		$HIDTA_CODE = "";
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
	} // end if
?>