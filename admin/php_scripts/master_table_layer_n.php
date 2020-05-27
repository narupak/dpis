<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");
	
	// ==== use for testing phase =====
	//$DPISDB = "mysql";
	//$db_dpis = $db;
	// ==========================

	$table = "PER_LAYER_N";

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$cmd = " select * from $table ";
	$db_dpis->send_cmd($cmd);
	$field_list = $db_dpis->list_fields($table);
//	echo "<pre>";		print_r($field_list);		echo "</pre>";
	unset($arr_fields);
	if($DPISDB=="odbc" || $DPISDB=="oci8"){
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
//		echo "$setflagshow<br>";
		$cmd = " update $table set $arr_fields[7] = 0 where concat(trim($arr_fields[0]), concat('|', concat(trim($arr_fields[1]), concat('|', concat(trim($arr_fields[2]), concat('|', trim($arr_fields[3]))))))) in ($current_list) ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error(); echo "<br>$cmd<br>";
		$cmd = " update $table set $arr_fields[7] = 1 where concat(trim($arr_fields[0]), concat('|', concat(trim($arr_fields[1]), concat('|', concat(trim($arr_fields[2]), concat('|', trim($arr_fields[3]))))))) in ($setflagshow) ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error(); echo "<br>$cmd<br>";
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการใช้งานข้อมูล");
	}
	
	if($command == "ADD" && trim($$arr_fields[0]) && ($$arr_fields[1]) && ($rdo_layer_type) && isset($$arr_fields[3])){	
		$$arr_fields[3] += 0;  $$arr_fields[4] += 0;  $$arr_fields[5] += 0;  $$arr_fields[6] += 0;
		$cmd = " select $arr_fields[0] 
				  from $table where where $arr_fields[0]='".trim($$arr_fields[0])."' and $arr_fields[1] = '".$$arr_fields[1]."' and $arr_fields[2] = '".$$arr_fields[2]."' and $arr_fields[3] = '".$arr_fields[3]."' ";
		$count_duplicate = $db_dpis->send_cmd($cmd);
		if($count_duplicate <= 0){
			
			if ($rdo_layer_type == 1)			$$arr_fields[5] = $$arr_fields[6] = 0;
			elseif ($rdo_layer_type == 2)		$$arr_fields[3] = $$arr_fields[4] = 0;
			$cmd = " insert into $table (". implode(", ", $arr_fields) .") values ('".trim($$arr_fields[0])."', '".$$arr_fields[1]."', '".$rdo_layer_type."', ".$$arr_fields[3].", ".$$arr_fields[4].", ".$$arr_fields[5].", ".$$arr_fields[6].", ".$$arr_fields[7].", $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();

			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [".trim($$arr_fields[0])." : ".$$arr_fields[1]." : ".$$arr_fields[2]." : ".$$arr_fields[3]."]");
		}else{
			$data = $db_dpis->get_array();			
			$err_text = "รหัสข้อมูลซ้ำ [".$data[$arr_fields[0]]." ".$data[$arr_fields[1]]." ".$data[$arr_fields[2]]." ".$$arr_fields[3]."]";
		} // endif
	}

	if($command == "UPDATE" && trim($$arr_fields[0]) && ($$arr_fields[1]) && ($rdo_layer_type) && isset($$arr_fields[3])){	
		if ($rdo_layer_type == 1)			$$arr_fields[5] = $$arr_fields[6] = 0;
		elseif ($rdo_layer_type == 2)		$$arr_fields[3] = $$arr_fields[4] = 0;
		$cmd = " update $table set $arr_fields[2]='".$rdo_layer_type."', $arr_fields[3]='".$$arr_fields[3]."', $arr_fields[4]='".$$arr_fields[4]."', $arr_fields[5]='".$$arr_fields[5]."', $arr_fields[6]='".$$arr_fields[6]."', $arr_fields[7]='".$$arr_fields[7]."', UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' where $arr_fields[0]='".$$arr_fields[0]."' and $arr_fields[1] = '".$$arr_fields[1]."' and $arr_fields[2] = '".$old_layer_type."' and $arr_fields[3] = '".$old_layer_no_n."' ";
		$db_dpis->send_cmd($cmd);

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [".trim($$arr_fields[0])." : ".$$arr_fields[1]." : ".$$arr_fields[2]." : ".$$arr_fields[3]."]");
	}
	
	if($command == "DELETE" && trim($$arr_fields[0]) && ($$arr_fields[1]) && ($$arr_fields[2]) && isset($$arr_fields[3])){	
		$cmd = " delete from $table where $arr_fields[0]='".$$arr_fields[0]."' and $arr_fields[1] = '".$$arr_fields[1]."' and $arr_fields[2] = '".$$arr_fields[2]."' and $arr_fields[3] = ".$$arr_fields[3]." ";
		$db_dpis->send_cmd($cmd);

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [".trim($$arr_fields[0])." : ".$$arr_fields[1]." : ".$$arr_fields[2]." : ".$$arr_fields[3]."]");
	}
	
	if($UPD){
		$cmd = " select pln.$arr_fields[0], pln.$arr_fields[1], $arr_fields[2], $arr_fields[3], $arr_fields[4], $arr_fields[5], $arr_fields[6], $arr_fields[7], pgn.PT_GROUP_NAME, ptn.PT_NAME_N 
				from $table pln, PER_GROUP_N pgn, PER_TYPE_N ptn 
				where pln.PT_GROUP_N = pgn.PT_GROUP_N and pln.PT_CODE_N = ptn.PT_CODE_N  and  
				pln.$arr_fields[0]='".$$arr_fields[0]."' and pln.$arr_fields[1] = '".$$arr_fields[1]."' and $arr_fields[2] = '".$$arr_fields[2]."' and $arr_fields[3] = ".$$arr_fields[3]." ";
		$db_dpis->send_cmd($cmd);
//$db_dpis->show_error();
//echo "<br>$cmd<br>";		
		$data = $db_dpis->get_array();
		$$arr_fields[1] = trim($data[$arr_fields[1]]);
		$$arr_fields[2] = trim($data[$arr_fields[2]]);
		$$arr_fields[3] = $data[$arr_fields[3]];
		$$arr_fields[4] = $data[$arr_fields[4]];
		$$arr_fields[5] = $data[$arr_fields[5]];
		$$arr_fields[6] = $data[$arr_fields[6]];	
		$$arr_fields[7] = $data[$arr_fields[7]];	
		
		$PT_GROUP_NAME = $data[PT_GROUP_NAME];
		$PT_NAME_N = $data[PT_NAME_N];
		$old_layer_type = trim($data[$arr_fields[2]]);
		$old_layer_no_n = $data[$arr_fields[3]];
	} // end if
	
	if( (!$UPD && !$DEL && !$err_text) ){
		$$arr_fields[0] = "";
		$$arr_fields[1] = "";
		$$arr_fields[2] = 1;
		$$arr_fields[3] = "";
		$$arr_fields[4] = "";
		$$arr_fields[5] = "";
		$$arr_fields[6] = "";	
		$$arr_fields[7] = 1;	
		
		$PT_GROUP_NAME = "";
		$PT_NAME_N = "";
	} // end if
	
?>