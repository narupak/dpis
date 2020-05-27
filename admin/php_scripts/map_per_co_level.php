<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

//	$db_dpis_n = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	// ==== use for testing phase =====
	$db_dpis_n = new connect_db($db_host, $db_name, $db_user, $db_pwd);
	// ==========================

	$table = "PER_MAP_CO_LEVEL";
	
	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$cmd = " select * from $table ";
	$db->send_cmd($cmd);
	$field_list = $db->list_fields($table);
//	echo "<pre>";		print_r($field_list);		echo "</pre>";
	unset($arr_fields);
	for($i=0; $i<count($field_list); $i++) :
		$arr_fields[] = $field_list[$i]["name"];
	endfor;
//	echo (implode(", ", $arr_fields));
	
	$UPDATE_DATE = date("Y-m-d H:i:s");
	
	if($command == "DELETEALL"){
		$cmd = " delete from $table ";
		$db->send_cmd($cmd);
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูลทั้งหมด");
	} // end if

	if($command == "LOADDPIS"){
		ini_set("max_execution_time", 1800);

		$cmd = " delete from $table ";
		$db->send_cmd($cmd);

		$cmd = " select CL_NAME, LEVEL_NO_MIN from PER_CO_LEVEL order by CL_NAME ";
		$count_all = $db_dpis->send_cmd($cmd);
		while($data_dpis = $db_dpis->get_array()){
			$CL_NAME = trim($data_dpis[CL_NAME]);
			$LEVEL_NO = "";
//			$LEVEL_NO = trim($data_dpis[LEVEL_NO_MIN]);

			$cmd = " insert into PER_MAP_CO_LEVEL (CL_NAME, LEVEL_NO, UPDATE_USER, UPDATE_DATE) values ('$CL_NAME', '$LEVEL_NO', $SESS_USERID, '$UPDATE_DATE') ";
			$db->send_cmd($cmd);			
//			$db->show_error();
		} // end while
	
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > นำเข้าข้อมูลจาก DPIS 3.0 จำนวน $count_all รายการ");
		ini_set("max_execution_time", 30);
	} // end if	

	if($command == "ADD" && trim($$arr_fields[0])){
		$cmd = " select $arr_fields[0], $arr_fields[1] from $table where trim($arr_fields[0])='". trim($$arr_fields[0]) ."' ";
		$count_duplicate = $db->send_cmd($cmd);
		if($count_duplicate <= 0){			
			$cmd = " insert into $table (". implode(", ", $arr_fields) .") values ('". trim($$arr_fields[0]) ."', '". $$arr_fields[1] ."', $SESS_USERID, '$UPDATE_DATE') ";
			$db->send_cmd($cmd);
//			$db->show_error();

			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [".trim($$arr_fields[0])." : ".trim($$arr_fields[1])."]");
		}else{
			$data = $db->get_array();
			$err_text = "รหัสข้อมูลซ้ำ [".$data[$arr_fields[0]]." ".$data[$arr_fields[1]]."]";
		} // endif
	}

	if($command == "UPDATE" && trim($$arr_fields[0])){
		$cmd = " update $table set $arr_fields[0]='". trim($$arr_fields[0]) ."', $arr_fields[1]='". $$arr_fields[1] ."',  UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' where trim($arr_fields[0])='". trim($$arr_fields[0]) ."' ";
		$db->send_cmd($cmd);
//		$db->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [".trim($$arr_fields[0])." : ".trim($$arr_fields[1])."]");
	}
	
	if($command == "DELETE" && trim($$arr_fields[0])){
		$cmd = " select $arr_fields[1] from $table where trim($arr_fields[0])='". trim($$arr_fields[0]) ."' ";
		$db->send_cmd($cmd);
		$data = $db->get_array();
		$$arr_fields[1] = $data[$arr_fields[1]];
		
		$cmd = " delete from $table where trim($arr_fields[0])='". trim($$arr_fields[0]) ."' ";
		$db->send_cmd($cmd);
//		$db->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [".trim($$arr_fields[0])." : ".$$arr_fields[1]."]");
	}
	
	if($UPD){
		$cmd = " select 	$arr_fields[0], $arr_fields[1] 
						 from 		$table
						 where 	trim($arr_fields[0])='". trim($$arr_fields[0]) ."' ";
		$db->send_cmd($cmd);
		$data = $db->get_array();
		$$arr_fields[0] = $data[$arr_fields[0]];
		$$arr_fields[1] = $data[$arr_fields[1]];
	} // end if
	
	if( (!$UPD && !$DEL && !$err_text) ){
		$$arr_fields[0] = "";
		$$arr_fields[1] = "";
	} // end if
?>