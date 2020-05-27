<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$cmd = " select * from $table ";
	$db_dpis->send_cmd($cmd);
	$field_list = $db_dpis->list_fields($table);
//	echo "<pre>";		print_r($field_list);		echo "</pre>";
	unset($arr_fields);
	if($DPISDB=="odbc" || $DPISDB=="oci8" || $DPISDB=="mssql"){
		for($i=1; $i<=count($field_list); $i++) :
			$arr_fields[] = strtoupper($field_list[$i]["name"]);
		endfor;
	}elseif($DPISDB=="mysql"){
		for($i=0; $i<count($field_list); $i++) :
			$arr_fields[] = strtoupper($field_list[$i]["name"]);
		endfor;
	} // end if
//	echo (implode(", ", $arr_fields));
	
	$UPDATE_DATE = date("Y-m-d H:i:s");

	if($command == "ADD" && trim($$arr_fields[1]) && trim($$arr_fields[2])){
		$cmd = " select $arr_fields[0], $arr_fields[1] from $table where $arr_fields[1]='". trim($$arr_fields[1]) ."' and $arr_fields[2]='". trim($$arr_fields[2]) ."' ";
		$count_duplicate = $db_dpis->send_cmd($cmd);
		if($count_duplicate <= 0){
			$arr_insert_fields = array_diff($arr_fields, array($arr_fields[0]));
			$$arr_fields[3] = 1;
			
		$cmd = " select max(POS_DES_ID) as max_id from POS_DES_INFO ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$$arr_fields[0] = $data[max_id] + 1;	
			
			//$cmd = " insert into $table (". implode(", ", $arr_insert_fields) .") values ('".$$arr_fields[1]."', '".$$arr_fields[2]."') ";
			$cmd = " insert into $table (". implode(", ", $arr_fields) .") values (".trim($$arr_fields[0]).", '".$$arr_fields[1]."', '".$$arr_fields[2]."', ".$$arr_fields[3].", $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [".trim($$arr_fields[0])." : ".$$arr_fields[1]." : ".$$arr_fields[2]."]");
		}else{
			$data = $db_dpis->get_array();			
			$err_text = "รหัสข้อมูลซ้ำ [".$$arr_fields[1]." ".$$arr_fields[2]."]";
		} // endif
	}

	if($command == "UPDATE" && trim($$arr_fields[1]) && trim($$arr_fields[2])){
		$cmd = " update $table set $arr_fields[1]='".$$arr_fields[1]."', $arr_fields[2]='".$$arr_fields[2]."' where $arr_fields[0]=".$$arr_fields[0];
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [".trim($$arr_fields[0])." : ".$$arr_fields[1]." : ".$$arr_fields[2]."]");
	}
	
	if($command == "DELETE" && trim($$arr_fields[0])){
		$cmd = " select $arr_fields[1], $arr_fields[2] from $table where $arr_fields[0]=".$$arr_fields[0];
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$$arr_fields[1] = $data[$arr_fields[1]];
		$$arr_fields[2] = $data[$arr_fields[2]];
		
		$cmd = " delete from $table where $arr_fields[0]=".$$arr_fields[0];
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [".trim($$arr_fields[0])." : ".$$arr_fields[1]." : ".$$arr_fields[2]."]");
	}
	
	if($UPD){
		$cmd = " select $arr_fields[1], $arr_fields[2] from $table where $arr_fields[0]=".$$arr_fields[0];
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$$arr_fields[1] = $data[$arr_fields[1]];
		$$arr_fields[2] = $data[$arr_fields[2]];
		
		$cmd = " select PL_NAME from PER_LINE where PL_CODE='".$$arr_fields[1]."' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PL_NAME = $data[PL_NAME];
	} // end if
	
	if( (!$UPD && !$DEL && !$err_text) ){
		$$arr_fields[0] = "";
		//$$arr_fields[1] = "";			//ใช้ไปหา LEVEL LIST
		//$PL_NAME = "";
		$$arr_fields[2] = "";
	} // end if
?>