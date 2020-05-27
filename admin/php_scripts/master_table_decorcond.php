<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
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

	$UPDATE_DATE = date("Y-m-d H:i:s");

	if($command == "ADD" && trim($$arr_fields[0])){
		$cmd = " select $arr_fields[0], $arr_fields[1], $arr_fields[2], $arr_fields[3], $arr_fields[4], $arr_fields[5]
				from $table 
				where 	$arr_fields[0]=".$$arr_fields[0]." and $arr_fields[1]='".$$arr_fields[1]."' and 
						$arr_fields[2]='".$$arr_fields[2]."'   ";
		$count_duplicate = $db_dpis->send_cmd($cmd);
		if($count_duplicate <= 0){
			$$arr_fields[7] += 0;		
			$cmd = " insert into $table (". implode(", ", $arr_fields) .") values ('".trim($$arr_fields[0])."', '".$$arr_fields[1]."', '".$$arr_fields[2]."', ".$$arr_fields[3].", ".$$arr_fields[4].", '".$$arr_fields[5]."', ".$$arr_fields[6].", ".$$arr_fields[7].", $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [".trim($$arr_fields[0])." : ".$$arr_fields[1]." : ".$$arr_fields[2]."]");
		}else{
			$data = $db_dpis->get_array();			
			$err_text = "รหัสข้อมูลซ้ำ [".$data[$arr_fields[0]]." ".$data[$arr_fields[1]]." ".$data[$arr_fields[2]]."]";
		} // endif
	}

	if($command == "UPDATE" && trim($$arr_fields[0])){
		$$arr_fields[7] += 0;
		$cmd = " update $table set $arr_fields[3]=".$$arr_fields[3].", $arr_fields[4]=".$$arr_fields[4].", $arr_fields[5]='".$$arr_fields[5]."', $arr_fields[6]=".$$arr_fields[6].", $arr_fields[7]=".$$arr_fields[7].", UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' 
				where $arr_fields[0]=".$$arr_fields[0]." and $arr_fields[1]='".$$arr_fields[1]."' and $arr_fields[2]='".$$arr_fields[2]."'  ";
		$db_dpis->send_cmd($cmd);

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [".trim($$arr_fields[0])." : ".$$arr_fields[1]." : ".$$arr_fields[2]."]");
	}
	
	if($command == "DELETE" && trim($$arr_fields[0])){
		$cmd = " delete from $table where $arr_fields[0]=".$$arr_fields[0]." and $arr_fields[1]='".$$arr_fields[1]."' and $arr_fields[2]='".$$arr_fields[2]."' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();


		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [".trim($$arr_fields[0])." : ".$$arr_fields[1]." : ".$$arr_fields[2]."]");
	}
	
	if($UPD){
		$cmd = " select 	$arr_fields[1], $arr_fields[2], $arr_fields[3], $arr_fields[4], $arr_fields[5], $arr_fields[6], $arr_fields[7]
				from 	$table 
				where 	$arr_fields[0]=".$$arr_fields[0]." and $arr_fields[1]='".$$arr_fields[1]."' and 
						$arr_fields[2]='".$$arr_fields[2]."'     ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$chk1_DCON_TYPE = $chk2_DCON_TYPE = "";
		if ($$arr_fields[0] == 1)			$chk1_DCON_TYPE = "checked";
		elseif ($$arr_fields[0] == 2)		$chk2_DCON_TYPE = "checked";		
		$$arr_fields[3] = $data[$arr_fields[3]];
		$$arr_fields[4] = $data[$arr_fields[4]];
		$$arr_fields[5] = $data[$arr_fields[5]];	
		$$arr_fields[6] = $data[$arr_fields[6]];
		$$arr_fields[7] = $data[$arr_fields[7]];	
		
		$DC_NAME = $DC_NAME_OLD = "";
		$cmd = "select DC_NAME, DC_CODE from PER_DECORATION where DC_CODE= '". $$arr_fields[2] ."' or DC_CODE= '" . $$arr_fields[5] . "'";
		$db_dpis2->send_cmd($cmd);
		while ($data_dpis2 = $db_dpis2->get_array()) {
			if ($data_dpis2[DC_CODE] == $$arr_fields[2])		$DC_NAME = $data_dpis2[DC_NAME];
			else											$DC_NAME_OLD = $data_dpis2[DC_NAME];
		}		
	} // end if
	
	if( (!$UPD && !$DEL && !$err_text) ){
		$$arr_fields[0] = 1;
		$chk1_DCON_TYPE = "checked";
		$$arr_fields[1] = "";
		$$arr_fields[2] = "";
		$$arr_fields[3] = "";
		$$arr_fields[4] = "";
		$$arr_fields[5] = "";	
		$$arr_fields[6] = "";	
		$$arr_fields[7] = 0;	
		$DC_NAME = $DC_NAME_OLD = "";		
	} // end if
?>