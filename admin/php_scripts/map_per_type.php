<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

//	$db_dpis_n = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	// ==== use for testing phase =====
	$db_dpis_n = new connect_db($db_host, $db_name, $db_user, $db_pwd);
	// ==========================

	$table = "PER_MAP_TYPE";
	
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
	
	if(trim($PT_CODE_N)){
		$cmd = " select PT_GROUP_N from PER_TYPE_N where PT_CODE_N='$PT_CODE_N' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$CH_PT_GROUP_N = $data[PT_GROUP_N];
	} // end if
	
	if($command == "ADD" && trim($$arr_fields[0]) && trim($$arr_fields[1]) && trim(${"CH_".$arr_fields[2]})){
		$cmd = " select $arr_fields[0], $arr_fields[1], $arr_fields[2], $arr_fields[3] from $table where $arr_fields[0]='". trim($$arr_fields[0]) ."' and $arr_fields[1]='". trim($$arr_fields[1]) ."' and $arr_fields[2]='".trim(${"CH_".$arr_fields[2]})."' ";
		$count_duplicate = $db->send_cmd($cmd);
		if($count_duplicate <= 0){			
			$cmd = " insert into $table (". implode(", ", $arr_fields) .") values ('".trim($$arr_fields[0])."', '".trim($$arr_fields[1])."', ".trim(${"CH_".$arr_fields[2]}).", '".$$arr_fields[3]."', $SESS_USERID, '$UPDATE_DATE') ";
			$db->send_cmd($cmd);
//			$db->show_error();

			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [".trim($$arr_fields[0])." : ".trim($$arr_fields[1])." : ".trim(${"CH_".$arr_fields[2]})."]");
		}else{
			$data = $db->get_array();
			$err_text = "รหัสข้อมูลซ้ำ [".$data[$arr_fields[0]]." ".$data[$arr_fields[1]]." ".$data[$arr_fields[2]]." ".$data[$arr_fields[3]]."]";
		} // endif
	}

	if($command == "UPDATE" && trim($$arr_fields[0]) && trim($$arr_fields[1]) && trim(${"CH_".$arr_fields[2]})){
		$cmd = " update $table set $arr_fields[0]='".trim($$arr_fields[0])."', $arr_fields[1]='".trim($$arr_fields[1])."', $arr_fields[2]=".trim(${"CH_".$arr_fields[2]}).", $arr_fields[3]='".$$arr_fields[3]."',  UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' where $arr_fields[0]='".$$arr_fields[0]."' and $arr_fields[1]='".$$arr_fields[1]."' and $arr_fields[2]=".$$arr_fields[2];
		$db->send_cmd($cmd);
//		$db->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [".trim($$arr_fields[0])." : ".trim($$arr_fields[1])." : ".trim(${"CH_".$arr_fields[2]})." : ".$$arr_fields[3]."]");
	}
	
	if($command == "DELETE" && trim($$arr_fields[0]) && trim($$arr_fields[1]) && trim($$arr_fields[2])){
		$cmd = " select $arr_fields[3] from $table where $arr_fields[0]='".$$arr_fields[0]."' and $arr_fields[1]='".$$arr_fields[1]."' and $arr_fields[2]=".$$arr_fields[2];
		$db->send_cmd($cmd);
		$data = $db->get_array();
		$$arr_fields[3] = $data[$arr_fields[3]];
		
		$cmd = " delete from $table where $arr_fields[0]='".$$arr_fields[0]."' and $arr_fields[1]='".$$arr_fields[1]."' and $arr_fields[2]=".$$arr_fields[2];
		$db->send_cmd($cmd);
//		$db->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [".trim($$arr_fields[0])." : ".$$arr_fields[1]." : ".$$arr_fields[2]." : ".$$arr_fields[3]."]");
	}
	
	if($UPD){
		$cmd = " select 	$arr_fields[0], $arr_fields[1], $arr_fields[2], $arr_fields[3]
						 from 		$table
						 where 	$arr_fields[0]='".$$arr_fields[0]."' and $arr_fields[1]='".$$arr_fields[1]."' ";
		$db->send_cmd($cmd);
//		$db->show_error();
		$data = $db->get_array();
		$$arr_fields[0] = trim($data[$arr_fields[0]]);
		$$arr_fields[1] = trim($data[$arr_fields[1]]);
		$$arr_fields[2] = trim($data[$arr_fields[2]]);
		$$arr_fields[3] = trim($data[$arr_fields[3]]);

		$cmd = " select PT_NAME from PER_TYPE where PT_CODE='".$$arr_fields[1]."' ";
		$db_dpis->send_cmd($cmd);
		$data_dpis = $db_dpis->get_array();
		$PT_NAME = $data_dpis[PT_NAME];

		$cmd = " select PT_NAME_N from PER_TYPE_N where PT_CODE_N='".$$arr_fields[3]."' ";
		$db_dpis_n->send_cmd($cmd);
		$data_dpis = $db_dpis_n->get_array();
		$PT_NAME_N = $data_dpis[PT_NAME_N];
	} // end if
	
	if( (!$UPD && !$DEL && !$err_text) ){
		$$arr_fields[0] = 1;
		$$arr_fields[1] = "";
		$$arr_fields[2] = "";
		$$arr_fields[3] = "";
		$PT_NAME = "";
		$PT_NAME_N = "";
	} // end if
?>