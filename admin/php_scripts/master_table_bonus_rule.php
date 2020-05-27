<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");	
	include("php_scripts/load_per_control.php");	

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$UPDATE_DATE = date("Y-m-d H:i:s");
	if (!$BR_TIMES) $BR_TIMES = "NULL";

	if ($command == "SETFLAG") {
		$setflagshow =  implode(",",$list_show_id);
//		echo "$setflagshow";
		$cmd = " update PER_BONUS_RULE set BR_ACTIVE = 0 where BR_YEAR = '$BR_YEAR' and BR_CODE in (".stripslashes($current_list).") ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$cmd = " update PER_BONUS_RULE set BR_ACTIVE = 1 where BR_YEAR = '$BR_YEAR' and BR_CODE in (".stripslashes($setflagshow).") ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการใช้งานข้อมูล");
	}
	
	if($command == "ADD" && trim($BR_YEAR) && trim($BR_NAME)){
		$cmd = " select BR_NAME from PER_BONUS_RULE where BR_YEAR='$BR_YEAR' and BR_CODE='$BR_CODE' ";
		$count_duplicate = $db_dpis->send_cmd($cmd);
		if($count_duplicate <= 0){
			$zero = "00000";
			$cmd = " select count(BR_CODE) as MAX_ID from PER_BONUS_RULE where BR_YEAR='$BR_YEAR' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$cnt_chk = 1;
			$inc = 1;
			while($cnt_chk > 0) {
				$max = (string)((int)$data[MAX_ID]+$inc);
				$maxlen = strlen($max);
				$NEW_BR_CODE = substr($zero,0,5-$maxlen).$max;
//				echo "gen max-$cmd (".$data[MAX_ID].", $NEW_BR_CODE)<br>";
				$cmd = " select * from PER_BONUS_RULE where BR_YEAR='$BR_YEAR' and BR_CODE='$NEW_BR_CODE' ";
				$cnt_chk = $db_dpis->send_cmd($cmd);
				$inc++;
			}			
			$cmd = " insert into PER_BONUS_RULE (BR_YEAR, BR_CODE, BR_TYPE, BR_NAME, BR_ORG_POINT_MIN, BR_ORG_POINT_MAX, 
							BR_PER_POINT_MIN, BR_PER_POINT_MAX, BR_TIMES, BR_ACTIVE, UPDATE_USER, UPDATE_DATE) 
							values ('$BR_YEAR', '$NEW_BR_CODE', '$BR_TYPE', '$BR_NAME', $BR_ORG_POINT_MIN, $BR_ORG_POINT_MAX, 
							$BR_PER_POINT_MIN, $BR_PER_POINT_MAX, $BR_TIMES, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
//			echo "ADD-$cmd<br>";

			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [".trim($BR_YEAR)." : ".trim($BR_CODE)." : ".$BR_NAME."]");
			$BR_YEAR = "";
			$BR_CODE = "";
			$BR_NAME = "";
			$BR_TYPE = "";
			$BR_ORG_POINT_MIN = "";
			$BR_ORG_POINT_MAX = "";
			$BR_PER_POINT_MIN = "";
			$BR_PER_POINT_MAX = "";
			$BR_TIMES = "";
			unset($SHOW_UPDATE_USER);
			unset($SHOW_UPDATE_DATE);
			$err_text = "";
			
		}else{
			$data = $db_dpis->get_array();			
			$err_text = "รหัสข้อมูลซ้ำ [".trim($BR_YEAR)." ".$BR_CODE." ".$BR_NAME."]";
		} // endif
	}

	if($command == "UPDATE" && trim($BR_YEAR) && trim($BR_CODE)){
		$cmd = " update PER_BONUS_RULE set 
									BR_TYPE = '$BR_TYPE', 
									BR_NAME = '$BR_NAME', 
									BR_ORG_POINT_MIN = $BR_ORG_POINT_MIN, 
									BR_ORG_POINT_MAX = $BR_ORG_POINT_MAX, 
									BR_PER_POINT_MIN = $BR_PER_POINT_MIN, 
									BR_PER_POINT_MAX = $BR_PER_POINT_MAX, 
									BR_TIMES = $BR_TIMES, 
									UPDATE_USER = $SESS_USERID, 
									UPDATE_DATE = '$UPDATE_DATE'  
									where BR_YEAR='$BR_YEAR' and BR_CODE='$BR_CODE' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$UPD=1;
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [".trim($BR_YEAR)." : ".trim($BR_CODE)." : ".$BR_NAME."]");
		
	}
//	echo "$command && $BR_YEAR && $BR_CODE, UPD=$UPD<br>";
	
	if($command == "DELETE" && trim($BR_YEAR) && trim($BR_CODE)){
		$cmd = " select BR_NAME from PER_BONUS_RULE where BR_YEAR='$BR_YEAR' and BR_CODE='$BR_CODE' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$BR_NAME = $data[BR_NAME];
		//echo $cmd;
		
		$cmd = " delete from PER_BONUS_RULE where BR_YEAR='$BR_YEAR' and BR_CODE='$BR_CODE' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [".trim($BR_YEAR)." : ".trim($BR_CODE)." : ".$BR_NAME."]");
	}
	
	if($UPD){
		$cmd = " select BR_TYPE, BR_NAME, BR_ORG_POINT_MIN, BR_ORG_POINT_MAX, BR_PER_POINT_MIN, BR_PER_POINT_MAX, BR_TIMES, UPDATE_USER, UPDATE_DATE 
						from PER_BONUS_RULE where BR_YEAR='$BR_YEAR' and BR_CODE='$BR_CODE' ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
		$data = $db_dpis->get_array();
		$BR_TYPE = $data[BR_TYPE];
		$BR_NAME = $data[BR_NAME];
		$BR_ORG_POINT_MIN = $data[BR_ORG_POINT_MIN];
		$BR_ORG_POINT_MAX = $data[BR_ORG_POINT_MAX];
		$BR_PER_POINT_MIN = $data[BR_PER_POINT_MIN];
		$BR_PER_POINT_MAX = $data[BR_PER_POINT_MAX];
		$BR_TIMES = $data[BR_TIMES];
		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);
	} // end if
	
	if( (!$UPD && !$DEL && !$err_text) ){
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
		//ถ้าใส่ไว้ เวลาเลือก ประเภทบุคลากร เพื่อให้ไปดึงค่า ระดับผลการประเมินหลัก ของข้าราชการ/ลูกจ้าง/พนง ค่าที่กรอกไว้ก่อนหน้าจะถูกเคลียร์ค่าไปหมด
		/*$BR_CODE = "";
		$BR_TYPE = "";
		$BR_NAME = "";
		$BR_ORG_POINT_MIN = "";
		$BR_ORG_POINT_MAX = "";
		$BR_PER_POINT_MIN = "";
		$BR_PER_POINT_MAX = "";
		$BR_TIMES = ""; */
	} // end if
?>