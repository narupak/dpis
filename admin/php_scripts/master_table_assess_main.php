<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;

	if($SESS_PER_TYPE==0){ 
			$PER_TYPE = (isset($PER_TYPE))?  $PER_TYPE : 1;	
			$search_per_type = (isset($search_per_type))?  $search_per_type : 1;	
	}
	if($search_per_type!="" && $search_per_type!=0){	$PER_TYPE = $search_per_type; } 
	$PER_TYPE = (trim($PER_TYPE))? $PER_TYPE : 1;	
	
//	if (!$AM_YEAR) $AM_YEAR = $KPI_BUDGET_YEAR;
//	if (!$AM_CYCLE) $AM_CYCLE = $KPI_CYCLE;
//	$AM_CYCLE = (trim($AM_CYCLE))? $AM_CYCLE : 1;			//เพิ่ม
//	$search_am_cycle = (trim($search_am_cycle))? $search_am_cycle : 
	
	$UPDATE_DATE = date("Y-m-d H:i:s");

	if ($command == "SETFLAG") {
		$unsetflagactive =  explode(",",$current_list);
		for($i = 0; $i < count($unsetflagactive); $i++) {
			$key = explode("|", $unsetflagactive[$i]);
			if ((int)$key[0] < 2000) break;	// เช็คว่าข้อมูลมี ตัวแรกเป็น ปี พ.ศ.
			$cmd = " update PER_ASSESS_MAIN set AM_ACTIVE = 0 where AM_YEAR='".$key[0]."' and AM_CYCLE=".$key[1]." and AM_CODE = '".$key[2]."' ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
//			echo "cmd1=$cmd<br>";
		}
		$setflagactive =  $list_active_id;
		for($i = 0; $i < count($setflagactive); $i++) {
			$key = explode("|", $setflagactive[$i]);
			if ((int)$key[0] < 2000) break;	// เช็คว่าข้อมูลมี ตัวแรกเป็น ปี พ.ศ.
			$cmd = " update PER_ASSESS_MAIN set AM_ACTIVE = 1 where AM_YEAR='".$key[0]."' and AM_CYCLE=".$key[1]." and AM_CODE = '".$key[2]."' ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
//			echo "cmd2=$cmd<br>";
		}
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการใช้งานข้อมูล");
	}
	
	if ($command == "SETSHOW") {
		$unsetflagshow =  explode(",",$current_list);
		for($i = 0; $i < count($unsetflagshow); $i++) {
//			echo "show unset $i-".$unsetflagshow[$i].", ";
			$key = explode("|", $unsetflagshow[$i]);
			if ((int)$key[0] < 2000) break;	// เช็คว่าข้อมูลมี ตัวแรกเป็น ปี พ.ศ.
			$cmd = " update PER_ASSESS_MAIN set AM_SHOW = 0 where AM_YEAR='".$key[0]."' and AM_CYCLE=".$key[1]." and AM_CODE = '".$key[2]."' ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
//			echo "cmd1=$cmd";
		}
		$setflagshow =  $list_show_id;
		for($i = 0; $i < count($setflagshow); $i++) {
//			echo " set $i-".$setflagshow[$i].", ";
			$key = explode("|", $setflagshow[$i]);
			if ((int)$key[0] < 2000) break;	// เช็คว่าข้อมูลมี ตัวแรกเป็น ปี พ.ศ.
			$cmd = " update PER_ASSESS_MAIN set AM_SHOW = 1 where AM_YEAR='".$key[0]."' and AM_CYCLE=".$key[1]." and AM_CODE = '".$key[2]."' ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
//			echo ", cmd2=$cmd<br>";
		}
//		echo "<br>";
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการใช้งานข้อมูล");
	}
	
	if (!$AM_POINT_MIN) $AM_POINT_MIN = "NULL";
	if (!$AM_POINT_MAX) $AM_POINT_MAX = "NULL";
	if($command == "ADD" && trim($AM_CODE)){
		$cmd = " select AM_NAME from PER_ASSESS_MAIN where AM_YEAR='$AM_YEAR' and AM_CYCLE=$AM_CYCLE and AM_CODE='$AM_CODE' ";
		$count_duplicate = $db_dpis->send_cmd($cmd);
		if($count_duplicate <= 0){
			$cmd = " insert into PER_ASSESS_MAIN (AM_YEAR, AM_CYCLE, AM_CODE, AM_NAME, AM_POINT_MIN, AM_POINT_MAX, 
							AM_ACTIVE, UPDATE_USER, UPDATE_DATE, PER_TYPE) 
							values ('$AM_YEAR', $AM_CYCLE, '$AM_CODE', '$AM_NAME', $AM_POINT_MIN, $AM_POINT_MAX, 1, $SESS_USERID, '$UPDATE_DATE', $PER_TYPE) ";
			$db_dpis->send_cmd($cmd);
//$db_dpis->show_error();
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [".trim($AM_CODE)." : ".$AM_NAME."]");
		}else{
			$data = $db_dpis->get_array();			
			$err_text = "รหัสข้อมูลซ้ำ [".$AM_CODE." ".$AM_NAME."]";
		} // endif
	}

	if($command == "UPDATE" && trim($AM_CODE)){
		$cmd = " update PER_ASSESS_MAIN set 
									AM_NAME = '$AM_NAME', 
									AM_POINT_MIN = $AM_POINT_MIN, 
									AM_POINT_MAX = $AM_POINT_MAX, 
									PER_TYPE = $PER_TYPE, 
									UPDATE_USER = $SESS_USERID, 
									UPDATE_DATE = '$UPDATE_DATE'  
									where AM_YEAR='$AM_YEAR' and AM_CYCLE=$AM_CYCLE and AM_CODE='$AM_CODE' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [".trim($AM_CODE)." : ".$AM_NAME."]");
	}
	
	if($command == "DELETE" && trim($AM_CODE)){
		if($AM_CYCLE_ACTION) $AM_CYCLE= $AM_CYCLE_ACTION;
		$cmd = " select AM_NAME from PER_ASSESS_MAIN where AM_YEAR = '$AM_YEAR' and AM_CYCLE = $AM_CYCLE and AM_CODE='$AM_CODE' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$AM_NAME = $data[AM_NAME];
		
		$cmd = " delete from PER_ASSESS_MAIN where AM_YEAR = '$AM_YEAR' and AM_CYCLE = $AM_CYCLE and AM_CODE='$AM_CODE' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [".trim($AM_YEAR)." : ".trim($AM_CYCLE)." : ".trim($AM_CODE)." : ".$AM_NAME."]");
	}
	
	if($command == "COPY" && trim($AM_COPY_FROMYEAR)){
		$cmd = " select AM_CODE, AM_NAME, AM_POINT_MIN, AM_POINT_MAX, AM_ACTIVE, UPDATE_USER, UPDATE_DATE, AM_SHOW  
						from PER_ASSESS_MAIN 
						where AM_YEAR='$AM_COPY_FROMYEAR' and AM_CYCLE=$AM_COPY_FROMCYCLE and PER_TYPE = $PER_TYPE ";
		$db_dpis->send_cmd($cmd);
		while ($data = $db_dpis->get_array()) {
			$AM_CODE = trim($data[AM_CODE]);
			$AM_NAME = trim($data[AM_NAME]);
			$AM_POINT_MIN = $data[AM_POINT_MIN];
			$AM_POINT_MAX = $data[AM_POINT_MAX];
			$AM_ACTIVE = $data[AM_ACTIVE];
			$UPDATE_USER = $data[UPDATE_USER];
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			$AM_SHOW = $data[AM_SHOW];
			if (!$AM_POINT_MIN) $AM_POINT_MIN = "NULL";
			if (!$AM_POINT_MAX) $AM_POINT_MAX = "NULL";
			if (!$AM_SHOW) $AM_SHOW = "NULL";
			
			$cmd = " insert into PER_ASSESS_MAIN (AM_YEAR, AM_CYCLE, AM_CODE, AM_NAME, AM_POINT_MIN, AM_POINT_MAX, 
							AM_ACTIVE, UPDATE_USER, UPDATE_DATE, PER_TYPE, AM_SHOW) 
							values ('$AM_COPY_TOYEAR', $AM_COPY_TOCYCLE, '$AM_CODE', '$AM_NAME', $AM_POINT_MIN, $AM_POINT_MAX, $AM_ACTIVE, $UPDATE_USER, '$UPDATE_DATE', $PER_TYPE, $AM_SHOW) ";
			$db_dpis1->send_cmd($cmd);
			//$db_dpis1->show_error();

			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [".trim($AM_YEAR)." : ".trim($AM_CYCLE)." : ".trim($AM_CODE)." : ".$AM_NAME."]");
		}
	}
	
	if($UPD){
		$cmd = " select AM_NAME, AM_POINT_MIN, AM_POINT_MAX, PER_TYPE, UPDATE_USER, UPDATE_DATE
						from PER_ASSESS_MAIN 
						where AM_YEAR='$AM_YEAR' and AM_CYCLE=$AM_CYCLE and AM_CODE='$AM_CODE' ";
		$db_dpis->send_cmd($cmd);
//echo "-> $cmd";
		$data = $db_dpis->get_array();
		$AM_NAME = $data[AM_NAME];
		$AM_POINT_MIN = $data[AM_POINT_MIN];
		$AM_POINT_MAX = $data[AM_POINT_MAX];
		$PER_TYPE = $data[PER_TYPE];
		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);
	} // end if
	
	if( (!$UPD && !$DEL && !$err_text) ){
		$AM_YEAR = "";
		$AM_CYCLE = "";  
		$AM_CODE = "";
		$AM_NAME = "";
		$AM_POINT_MIN = "";
		$AM_POINT_MAX = "";
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
	} // end if
?>