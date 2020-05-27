<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	
	$UPDATE_DATE = date("Y-m-d H:i:s");
	if($command=="REORDER"){
		if (!$WL_SEQ_NO) $WL_SEQ_NO = "NULL";
		foreach($ARR_ORDER as $CODE => $SEQ_NO){
			if($SEQ_NO=="") { 
				$cmd = " update PER_WORK_LOCATION set WL_SEQ_NO='' where WL_CODE='$CODE' "; 
				}else {	
				$cmd = " update PER_WORK_LOCATION set WL_SEQ_NO=$SEQ_NO where WL_CODE='$CODE' "; 
			}
			$db_dpis->send_cmd($cmd);
		} // end foreach

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > จัดลำดับ [$CODE : $SEQ_NO]");
	} // end if

	if ($command == "SETFLAG") {
		$setflagshow =  implode(",",$list_show_id);
//		echo "$setflagshow";
		$cmd = " update PER_WORK_LOCATION set WL_ACTIVE = 0 where WL_CODE in (".stripslashes($current_list).") ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$cmd = " update PER_WORK_LOCATION set WL_ACTIVE = 1 where WL_CODE in (".stripslashes($setflagshow).") ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > ตั้งค่าการใช้งานข้อมูล");
	}
	
	
	$tmp_WL_CODE= trim($WL_CODE);
	$tmp_WL_NAME= trim($WL_NAME);
	$tmp_WL_ACTIVE= $WL_ACTIVE;
	$tmp_WL_OTHERNAME= trim($WL_OTHERNAME);
	
	if($command == "ADD"){
		
		$tmp_WL_SEQ_NO= "NULL";
		$cmd = " select WL_CODE, WL_NAME from PER_WORK_LOCATION where WL_CODE='". $tmp_WL_CODE ."' OR WL_NAME='". $tmp_WL_NAME ."' ";
//		$cmd = " select WL_CODE, WL_NAME from PER_WORK_LOCATION where WL_CODE=':xx' OR WL_NAME='". $tmp_WL_NAME ."' ";
		//oci_bind_by_name($db_dpis,":xx",$tmp_WL_CODE);
		$count_duplicate = $db_dpis->send_cmd($cmd);
		//echo "cmd=$cmd<br>";
		if($count_duplicate <= 0){
			$cmd = " insert into PER_WORK_LOCATION (WL_CODE,WL_NAME,WL_ACTIVE,
										UPDATE_USER,UPDATE_DATE,WL_SEQ_NO,WL_OTHERNAME) 
						values ('$tmp_WL_CODE', '$tmp_WL_NAME', $tmp_WL_ACTIVE, 
							           $SESS_USERID, '$UPDATE_DATE', ".$tmp_WL_SEQ_NO.", '$tmp_WL_OTHERNAME') ";
			$db_dpis->send_cmd($cmd);
			//echo $cmd;
			//$db_dpis->show_error();
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > เพิ่มข้อมูล [".trim($tmp_WL_CODE)." : ".$tmp_WL_NAME."]");
		
			echo "<script>window.location='../admin/es_t02_frm.html?MENU_ID_LV0=$MENU_ID_LV0&MENU_ID_LV1=$MENU_ID_LV1&MENU_ID_LV2=$MENU_ID_LV2';</script>";
		
		}else{
			$data = $db_dpis->get_array();			
			$err_text = "รหัสหรือสถานที่ปฏิบัติราชการซ้ำ [".$data[WL_CODE]." ".$data[WL_NAME]."]";
		} // endif
		
		
	}

	if($command == "UPDATE"){
		$cmd = " select WL_CODE, WL_NAME from PER_WORK_LOCATION where  WL_NAME='". $tmp_WL_NAME ."' AND WL_NAME!='". $hidWL_NAME ."'";
		$count_duplicate = $db_dpis->send_cmd($cmd);
		//echo "cmd=$cmd<br>";
		if($count_duplicate <= 0){
			$cmd = " update PER_WORK_LOCATION set 
								WL_NAME='".$tmp_WL_NAME."', 
								WL_ACTIVE=".$tmp_WL_ACTIVE.", 
								WL_OTHERNAME='".$tmp_WL_OTHERNAME."', 
								UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' 
								where WL_CODE='".$tmp_WL_CODE."' ";	
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			//echo $cmd;
			insert_log("$MENU_TITLE_LV0 > $MENUT_ITLE_LV1 > $MENU_TITLE_LV2 > แก้ไขข้อมูล [".trim($tmp_WL_CODE)." : ".$tmp_WL_NAME."]");
		}else{
			$data = $db_dpis->get_array();			
			$err_text = "สถานที่ปฏิบัติราชการซ้ำ [".$data[WL_CODE]." ".$data[WL_NAME]."]";
			$UPD=1;
		} // endif
	}
	
	if($command == "DELETE"){
		$cmd = " select WL_NAME from PER_WORK_LOCATION where WL_CODE='".$tmp_WL_CODE."' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$del_WL_NAME = $data[WL_NAME];
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > ลบข้อมูล [".trim($tmp_WL_CODE)." : ".$del_WL_NAME."]");
		//echo $cmd;
		
		$cmd = " delete from PER_WORK_LOCATION where WL_CODE='".$tmp_WL_CODE."' "; 
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		
		echo "<script>window.location='../admin/es_t02_frm.html?MENU_ID_LV0=$MENU_ID_LV0&MENU_ID_LV1=$MENU_ID_LV1&MENU_ID_LV2=$MENU_ID_LV2';</script>";
		
	}
	
	if($UPD){
		
		$cmd = " select WL_NAME, WL_ACTIVE, WL_SEQ_NO, WL_OTHERNAME, UPDATE_USER, UPDATE_DATE from PER_WORK_LOCATION where WL_CODE='".$WL_CODE."' "; 
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$WL_NAME = $data[WL_NAME];
		$WL_ACTIVE = $data[WL_ACTIVE];
		$WL_SEQ_NO = $data[WL_SEQ_NO];
		$WL_OTHERNAME = $data[WL_OTHERNAME];
		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);
	} // end if
	
	if( (!$UPD && !$DEL && !$err_text) ){
		$WL_CODE = "";
		$WL_NAME = "";
		$WL_ACTIVE = 1;
		$WL_SEQ_NO = 0;
		$WL_OTHERNAME = "";
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
	} // end if
	
	
?>