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
			$arr_fields[] = $field_list[$i]["name"];
		endfor;
	}elseif($DPISDB=="mysql"){
		for($i=0; $i<count($field_list); $i++) :
			$arr_fields[] = $field_list[$i]["name"];
		endfor;
	} // end if
//	echo (implode(", ", $arr_fields));
	
	$UPDATE_DATE = date("Y-m-d H:i:s");

	if ($command == "SETFLAG") {
		$setflagshow =  implode(",",$list_show_id);
//		echo "$setflagshow";
		$cmd = " update $table set $arr_fields[2] = 0 where $arr_fields[0] in (".stripslashes($current_list).") ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$cmd = " update $table set $arr_fields[2] = 1 where $arr_fields[0] in (".stripslashes($setflagshow).") ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ��駤�ҡ����ҹ������");
	}
	
	if($command == "ADD" && trim($$arr_fields[0])){
		$cmd = " select $arr_fields[0], $arr_fields[1] from $table where $arr_fields[0]='". trim($$arr_fields[0]) ."' ";
		$count_duplicate = $db_dpis->send_cmd($cmd);
		if($count_duplicate <= 0){
			$arr_insert_fields = array_diff($arr_fields, array($arr_fields[0]));
			//$cmd = " insert into $table (". implode(", ", $arr_insert_fields) .") values ('".$$arr_fields[1]."', ".$$arr_fields[2].") ";
			$cmd = " insert into $table (". implode(", ", $arr_fields) .") values (".trim($$arr_fields[0]).", '".$$arr_fields[1]."', '".$$arr_fields[2]."') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ���������� [".trim($$arr_fields[0])." : ".$$arr_fields[1]."]");
		}else{
			$data = $db_dpis->get_array();			
			$err_text = "���ʢ����ū�� [".$data[$arr_fields[0]]." ".$data[$arr_fields[1]]."]";
		} // endif
	}
	
	if($command == "UPDATE"){
		$cmd = " update $table set $arr_fields[1]='".$$arr_fields[1]."', $arr_fields[2]='".$$arr_fields[2]."' where $arr_fields[0]=".$$arr_fields[0]." ";		
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		//echo $cmd;
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ��䢢����� [".trim($$arr_fields[0])." : ".$$arr_fields[1]."]");
	}
	
	if($command == "DELETE" && trim($$arr_fields[0])){
		$cmd = " select $arr_fields[1] from $table where $arr_fields[0]=".$$arr_fields[0]." ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$$arr_fields[1] = $data[$arr_fields[1]];
		
		$cmd = " delete from $table where $arr_fields[0]='".$$arr_fields[0]."'";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ź������ [".trim($$arr_fields[0])." : ".$$arr_fields[1]."]");
	}
	
	if($UPD){
		$cmd = " select $arr_fields[1], $arr_fields[2], UPDATE_USER, UPDATE_DATE from $table where $arr_fields[0]='".$$arr_fields[0]."' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$$arr_fields[1] = $data[$arr_fields[1]];
		$$arr_fields[2] = $data[$arr_fields[2]];
		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);
	} // end if
	
	if( (!$UPD && !$DEL && !$err_text) ){
		$$arr_fields[0] = "";
		$$arr_fields[1] = "";
		$$arr_fields[2] = 1;
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
	} // end if
?>