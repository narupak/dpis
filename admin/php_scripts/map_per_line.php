<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

//	$db_dpis_n = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	// ==== use for testing phase =====
	$db_dpis_n = new connect_db($db_host, $db_name, $db_user, $db_pwd);
	// ==========================

	$table = "PER_MAP_LINE";
	
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

		$cmd = " select PL_CODE from PER_LINE order by PL_CODE ";
		$count_all = $db_dpis->send_cmd($cmd);
		while($data_dpis = $db_dpis->get_array()){
			$PL_CODE = $data_dpis[PL_CODE];
//			$cmd = " select PL_CODE from PER_MAP_LINE where trim(PL_CODE)='$PL_CODE' ";
//			$count_line = $db->send_cmd($cmd);
//			if(!$count_line){
				$cmd = " insert into PER_MAP_LINE (PL_CODE, PL_GROUP, PL_CODE_N, UPDATE_USER, UPDATE_DATE) values ('$PL_CODE', '', '', $SESS_USERID, '$UPDATE_DATE') ";
				$db->send_cmd($cmd);			
//				$db->show_error();
//			} // end if
		} // end while
	
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > นำเข้าข้อมูลจาก DPIS 3.0 จำนวน $count_all รายการ");
		ini_set("max_execution_time", 30);
	} // end if	

	if($command == "ADD" && trim($$arr_fields[0])){
		$cmd = " select $arr_fields[0], $arr_fields[1], $arr_fields[2] from $table where $arr_fields[0]='". trim($$arr_fields[0]) ."' ";
		$count_duplicate = $db->send_cmd($cmd);
		if($count_duplicate <= 0){			
			$cmd = " insert into $table (". implode(", ", $arr_fields) .") values ('".trim($$arr_fields[0])."', '".trim($$arr_fields[1])."', '".trim($$arr_fields[2])."', $SESS_USERID, '$UPDATE_DATE') ";
			$db->send_cmd($cmd);
//			$db->show_error();

			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [".trim($$arr_fields[0])." : ".trim($$arr_fields[1])." : ".trim($$arr_fields[2])."]");
		}else{
			$data = $db->get_array();
			$err_text = "รหัสข้อมูลซ้ำ [".$data[$arr_fields[0]]." ".$data[$arr_fields[1]]." ".$data[$arr_fields[2]]."]";
		} // endif
	}

	if($command == "UPDATE" && trim($$arr_fields[0])){
		$cmd = " update $table set $arr_fields[0]='".trim($$arr_fields[0])."', $arr_fields[1]='".trim($$arr_fields[1])."', $arr_fields[2]='".trim($$arr_fields[2])."',  UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' where $arr_fields[0]='".$$arr_fields[0]."' ";
		$db->send_cmd($cmd);
//		$db->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [".trim($$arr_fields[0])." : ".trim($$arr_fields[1])." : ".trim($$arr_fields[2])."]");
	}
	
	if($command == "DELETE" && trim($$arr_fields[0])){
		$cmd = " select $arr_fields[1], $arr_fields[2] from $table where $arr_fields[0]='".$$arr_fields[0]."' ";
		$db->send_cmd($cmd);
		$data = $db->get_array();
		$$arr_fields[1] = $data[$arr_fields[1]];
		$$arr_fields[2] = $data[$arr_fields[2]];
		
		$cmd = " delete from $table where $arr_fields[0]='".$$arr_fields[0]."' ";
		$db->send_cmd($cmd);
//		$db->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [".trim($$arr_fields[0])." : ".$$arr_fields[1]." : ".$$arr_fields[2]."]");
	}
	
	if($UPD){
		$cmd = " select 	$arr_fields[0], $arr_fields[1], $arr_fields[2] 
						 from 		$table
						 where 	$arr_fields[0]='".$$arr_fields[0]."' ";
		$db->send_cmd($cmd);
		$data = $db->get_array();
		$$arr_fields[0] = $data[$arr_fields[0]];
		$$arr_fields[1] = $data[$arr_fields[1]];
		$$arr_fields[2] = $data[$arr_fields[2]];

		$cmd = " select PL_NAME from PER_LINE where PL_CODE='".$$arr_fields[0]."' ";
		$db_dpis->send_cmd($cmd);
		$data_dpis = $db_dpis->get_array();
		$PL_NAME = $data_dpis[PL_NAME];

		$cmd = " select PL_NAME_N from PER_LINE_N where PL_CODE_N='".$$arr_fields[1]."' ";
		$db_dpis_n->send_cmd($cmd);
		$data_dpis = $db_dpis_n->get_array();
		$PL_NAME_N = $data_dpis[PL_NAME_N];
	} // end if
	
	if( (!$UPD && !$DEL && !$err_text) ){
		$$arr_fields[0] = "";
		$$arr_fields[1] = "";
		$$arr_fields[2] = "";
		$PL_NAME = "";
		$PL_NAME_N = "";
	} // end if
?>