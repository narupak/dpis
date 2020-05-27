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
	if($DPISDB=="mysql"){
		for($i=0; $i<count($field_list); $i++) :
			$arr_fields[] = $field_list[$i]["name"];
		endfor;
	} else {
		for($i=1; $i<=count($field_list); $i++) :
			$arr_fields[] = $field_list[$i]["name"];
		endfor;
	}
//	echo (implode(", ", $arr_fields));
	
	$UPDATE_DATE = date("Y-m-d H:i:s");

	if ($command == "SETFLAG") {
		$setflagshow =  implode(",",$list_show_id);
//		echo "$setflagshow";
		$cmd = " update $table set $arr_fields[1] = 0 where $arr_fields[0] in (".stripslashes($current_list).") ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$cmd = " update $table set $arr_fields[1] = 1 where $arr_fields[0] in (".stripslashes($setflagshow).") ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการใช้งานข้อมูล");
	}
	if (!$LEVEL_SEQ_NO) $LEVEL_SEQ_NO = 0;
	
	if($command == "ADD" && trim($$arr_fields[0])){
		$cmd = " select $arr_fields[0], $arr_fields[1] from $table where $arr_fields[0]='". trim($$arr_fields[0]) ."' ";
		$count_duplicate = $db_dpis->send_cmd($cmd);
		if($count_duplicate <= 0){
			$cmd = " insert into $table (". implode(", ", $arr_fields) .") values ('".trim($$arr_fields[0])."', ".$$arr_fields[1].", $SESS_USERID, '$UPDATE_DATE', 
						'".$$arr_fields[4]."', ".$$arr_fields[5].", '".$$arr_fields[6]."', $LEVEL_SEQ_NO, '$POSITION_TYPE', '$POSITION_LEVEL', '".$$arr_fields[10]."', '".$$arr_fields[11]."') ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > $menu_detail เพิ่มข้อมูล [".trim($$arr_fields[0])." : ".$$arr_fields[1]."]");
			$success_sql = "เพิ่มข้อมูล [".trim($$arr_fields[0])." : ".$$arr_fields[1]."]เรียบร้อยแล้ว"; //กำหนดในส่วนของ php
			
		}else{
			$data = $db_dpis->get_array();			
			$err_text = "รหัสข้อมูลซ้ำ [".$data[$arr_fields[0]]." ".$data[$arr_fields[1]]."]";
		} // endif
	}

	if($command == "UPDATE" && trim($$arr_fields[0])){
		$cmd = " update $table set $arr_fields[1]=".$$arr_fields[1].", 
		$arr_fields[4]='".$$arr_fields[4]."',
		 $arr_fields[5]=".$$arr_fields[5].",
		 $arr_fields[6]='".$$arr_fields[6]."',
		 $arr_fields[7]=$LEVEL_SEQ_NO, 
		 $arr_fields[8]='$POSITION_TYPE', 
		 $arr_fields[9]='$POSITION_LEVEL', 
		 $arr_fields[10]='".$$arr_fields[10]."',
		 $arr_fields[11]='".$$arr_fields[11]."',
		 UPDATE_USER=$SESS_USERID, 
		 UPDATE_DATE='$UPDATE_DATE' 
		where $arr_fields[0]='".$$arr_fields[0]."' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		//echo $cmd;
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [".trim($$arr_fields[0])." : ".$$arr_fields[1]."]");
	}
	
	if($command == "DELETE" && trim($$arr_fields[0])){
		$cmd = " select $arr_fields[1] from $table where $arr_fields[0]='".$$arr_fields[0]."' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$$arr_fields[1] = $data[$arr_fields[1]];
		
		$cmd = " delete from $table where $arr_fields[0]='".$$arr_fields[0]."' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [".trim($$arr_fields[0])." : ".$$arr_fields[1]."]");
	}
	
	if($UPD){
		$cmd = " select $arr_fields[1], $arr_fields[4], $arr_fields[5], $arr_fields[6], $arr_fields[7], $arr_fields[8], $arr_fields[9], $arr_fields[10], $arr_fields[11], UPDATE_USER, UPDATE_DATE 
						from $table where $arr_fields[0]='".$$arr_fields[0]."' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$$arr_fields[1] = $data[$arr_fields[1]];
		$$arr_fields[4] = $data[$arr_fields[4]];	
		$$arr_fields[5] = $data[$arr_fields[5]];	
		$$arr_fields[6] = $data[$arr_fields[6]];	
		$$arr_fields[7] = $data[$arr_fields[7]];	
		$$arr_fields[8] = $data[$arr_fields[8]];	
		$$arr_fields[9] = $data[$arr_fields[9]];	
		$$arr_fields[10] = $data[$arr_fields[10]];	
		$$arr_fields[11] = $data[$arr_fields[11]];	
		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);
	} // end if
	
	if( (!$UPD && !$DEL && !$err_text) ){
		$$arr_fields[0] = "";
		$$arr_fields[1] = 1;
		$$arr_fields[4] = "";
		$$arr_fields[5] = 1;
		$$arr_fields[6] = "";
		$$arr_fields[7] = "";
		$$arr_fields[8] = "";
		$$arr_fields[9] = "";
		$$arr_fields[10] = "";
		$$arr_fields[11] = "";
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
	} // end if
?>