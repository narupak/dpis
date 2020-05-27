<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

//	echo "command=$command<br>";
	$cmd = " select * from $table ";
	$db_dpis->send_cmd($cmd);
	$field_list = $db_dpis->list_fields($table);
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

	if ($command == "COPY") {
		$cmd = " delete from $table where WORD_GROUP in ('DEPARTMENT_NAME', 'ORG_NAME', 'DIVISION_NAME', 'JOB_NAME') ";
		$db_dpis->send_cmd($cmd);

		$cmd = " select MAX($arr_fields[0]) as max_seq from $table ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$last_seq = $data['max_seq']+1;

		$cmd = " select distinct OL_CODE, ORG_NAME from PER_ORG 
						 where OL_CODE in ('02', '03','04','05') and ORG_ACTIVE=1 and ORG_NAME != '-'
						 order by ORG_NAME ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		
		while($data = $db_dpis->get_array()){
			$OL_CODE = trim($data[OL_CODE]);
			if ($OL_CODE=="02") $OL_NAME = "DEPARTMENT_NAME";
			elseif ($OL_CODE=="03") $OL_NAME = "ORG_NAME";
			elseif ($OL_CODE=="04") $OL_NAME = "DIVISION_NAME";
			elseif ($OL_CODE=="05") $OL_NAME = "JOB_NAME";
			$ORG_NAME = trim($data[ORG_NAME]);
			$cmd = " insert into $table (". implode(", ", $arr_fields) .") values ($last_seq, '$ORG_NAME', '$OL_NAME', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$last_seq++;
		} // end while  

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > คัดลอกจากโครงสร้างส่วนราชการ");
	}
	
	if ($command == "SETFLAG") {
		$setflagshow =  implode(",",$list_show_id);
		$cmd = " update $table set $arr_fields[3] = 0 where $arr_fields[0] in (".stripslashes($current_list).") ";
		$db_dpis->send_cmd($cmd);
		$cmd = " update $table set $arr_fields[3] = 1 where $arr_fields[0] in (".stripslashes($setflagshow).") ";
		$db_dpis->send_cmd($cmd);
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการใช้งานข้อมูล");
	}

	if($command == "ADD" && trim($$arr_fields[0])){
		$$arr_fields[1] = (trim($$arr_fields[1]))? "".trim($$arr_fields[1])."" : 'NULL';
		$$arr_fields[2] = (trim($$arr_fields[2]))? "".trim($$arr_fields[2])."" : 'NULL';
		$cmd = " select $arr_fields[0], $arr_fields[1], $arr_fields[2], $arr_fields[3]  from $table where $arr_fields[0]=". trim($$arr_fields[0]) ." ";
		$count_duplicate = $db_dpis->send_cmd($cmd);
//		echo "ADD entry seq cmd=$cmd<br>";
		if($count_duplicate <= 0){	// ถ้ารหัสไม่ซ้ำ
			$cmd = " select $arr_fields[0], $arr_fields[1], $arr_fields[2], $arr_fields[3]  from $table where $arr_fields[1]='".trim($$arr_fields[1])."'";
			$count_duplicate = $db_dpis->send_cmd($cmd);
			if($count_duplicate <= 0){		// ถ้า คำไทยไม่ซ้ำ
				$cmd = " insert into $table (". implode(", ", $arr_fields) .") values (".trim($$arr_fields[0]).", '".$$arr_fields[1]."', '".$$arr_fields[2]."', ".$$arr_fields[3].", $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
			//	$db_dpis->show_error();
				insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [".trim($$arr_fields[0])." : '".$$arr_fields[1]."' : '".$$arr_fields[2]."' : ".$$arr_fields[3]."]");
			}else{
				$data = $db_dpis->get_array();			
				$err_text = "คำไทยซ้ำ [".$data[$arr_fields[0]]." ".$data[$arr_fields[1]]." ".$data[$arr_fields[2]]." ".$data[$arr_fields[3]]."]";
			}
		}else{
			$data = $db_dpis->get_array();			
			$err_text = "รหัสข้อมูลซ้ำ [".$data[$arr_fields[0]]." ".$data[$arr_fields[1]]." ".$data[$arr_fields[2]]." ".$data[$arr_fields[3]]."]";
		} // endif
	} else if ($command == "ADD") {
		$cmd = " select $arr_fields[0], $arr_fields[1], $arr_fields[2], $arr_fields[3]  from $table where $arr_fields[1]='".trim($$arr_fields[1])."'";
		$count_duplicate = $db_dpis->send_cmd($cmd);
//		echo "cmd=$cmd ($count_duplicate)<br>";
		if($count_duplicate <= 0){		// ถ้า คำไทยไม่ซ้ำ
			$cmd = " select MAX($arr_fields[0]) as max_seq from $table ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$last_seq = $data['max_seq']+1;
	//		echo "ADD gen seq cmd=$cmd<br>";
	
			$$arr_fields[1] = (trim($$arr_fields[1]))? "".trim($$arr_fields[1])."" : 'NULL';
			$$arr_fields[2] = (trim($$arr_fields[2]))? "".trim($$arr_fields[2])."" : 'NULL';
			
			$cmd = " insert into $table (". implode(", ", $arr_fields) .") values (".$last_seq.", '".$$arr_fields[1]."', '".$$arr_fields[2]."', ".$$arr_fields[3].", $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
		//	$db_dpis->show_error();
//			echo "insert--$cmd<br>";
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [".$last_seq." : '".$$arr_fields[1]."' : ".$$arr_fields[2]." : ".$$arr_fields[3]."]");
		} else {
			$data = $db_dpis->get_array();			
			$err_text = "คำไทยซ้ำ [".$data[$arr_fields[0]]." ".$data[$arr_fields[1]]." ".$data[$arr_fields[2]]." ".$data[$arr_fields[3]]."]";
		} // endif
	}

	if($command == "UPDATE" && trim($$arr_fields[0])){
		$$arr_fields[1] = (trim($$arr_fields[1]))? "".trim($$arr_fields[1])."" : 'NULL';
		$$arr_fields[2] = (trim($$arr_fields[2]))? "".trim($$arr_fields[2])."" : 'NULL';
		$cmd = " update $table set $arr_fields[1]='".$$arr_fields[1]."', $arr_fields[2]='".$$arr_fields[2]."', $arr_fields[3]=".$$arr_fields[3].", UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' where $arr_fields[0]=".$$arr_fields[0]." ";		
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
//		echo "update--$cmd<br>";
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [".trim($$arr_fields[0])." : '".$$arr_fields[1]."' : '".$$arr_fields[2]."' : ".$$arr_fields[3]."]");
	}


	if($command == "DELETE" && trim($$arr_fields[0])){
		$cmd = " select $arr_fields[1] from $table where $arr_fields[0]=".$$arr_fields[0]." ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$$arr_fields[1] = $data[$arr_fields[1]];
		
		$cmd = " delete from $table where $arr_fields[0]=".$$arr_fields[0]."";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [".trim($$arr_fields[0])." : '".$$arr_fields[1]."' : '".$$arr_fields[2]."' : ".$$arr_fields[3]."]");
	}


	if($UPD){
		$cmd = " select $arr_fields[1], $arr_fields[2], $arr_fields[3], UPDATE_USER, UPDATE_DATE from $table where $arr_fields[0]='".$$arr_fields[0]."' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$$arr_fields[1] = $data[$arr_fields[1]];
		$$arr_fields[2] = $data[$arr_fields[2]];
		$$arr_fields[3] = $data[$arr_fields[3]];
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
		$$arr_fields[2] = "";
		$$arr_fields[3] = 0;
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
	} // end if
?>