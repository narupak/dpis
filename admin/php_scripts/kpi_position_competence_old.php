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
	if($DPISDB=="mysql"){
		unset($arr_fields);
		for($i=0; $i<=count($field_list); $i++) :
			$arr_fields[] = $field_list[$i]["name"];
		endfor;
	}else{	
		unset($arr_fields);
		for($i=1; $i<=count($field_list); $i++) :
			$arr_fields[] = $field_list[$i]["name"];
		endfor;
	}
//	echo (implode(", ", $arr_fields));
	
	$UPDATE_DATE = date("Y-m-d H:i:s");

	if ($command == "SETFLAG") {
		$setflagshow =  implode(",",$list_show_id);
//echo "$setflagshow<br>";
		$cmd = " update $table set $arr_fields[4] = 0 where $arr_fields[0] in ($current_list) ";
		$db_dpis->send_cmd($cmd);
//$db_dpis->show_error(); echo "<br>";
		$cmd = " update $table set $arr_fields[4] = 1 where $arr_fields[0] in ($setflagshow) ";
		$db_dpis->send_cmd($cmd);
//$db_dpis->show_error(); echo "<br>";
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการใช้งานข้อมูล");
	}
	
	if($command == "ADD" && trim($$arr_fields[0])){
		$cmd = " 	select $arr_fields[0], $arr_fields[1], $arr_fields[2], $arr_fields[3]  from $table 
						where $arr_fields[0]='". trim($$arr_fields[0]) ."' and $arr_fields[1]=". trim($$arr_fields[1]);
		$count_duplicate = $db_dpis->send_cmd($cmd);
		if($count_duplicate <= 0){
			$cmd = " insert into $table (". implode(", ", $arr_fields) .") values ('".trim($$arr_fields[0])."', ".$$arr_fields[1].", '".$$arr_fields[2]."', $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			$db_dpis->show_error();

			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [".trim($$arr_fields[0])." : ".$$arr_fields[1]."]");
		}else{
			$data = $db_dpis->get_array();			
			$err_text = "รหัสข้อมูลซ้ำ [".$data[$arr_fields[0]]." ".$data[$arr_fields[1]]."]";
		} // endif
	}

	if($command == "UPDATE" && trim($$arr_fields[0])){
		$cmd = " 	update $table set $arr_fields[0]='".$$arr_fields[0]."', $arr_fields[1]=".$$arr_fields[1].", $arr_fields[2]='".$$arr_fields[2]."', UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' 
							where $arr_fields[0]=".$$arr_fields[0]." and $arr_fields[1]='".$$arr_fields[1]."'";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [".trim($$arr_fields[0])." : ".$$arr_fields[1]."]");
	}
	
	if($command == "DELETE" && trim($$arr_fields[0])){
		$cmd = " delete from $table where $arr_fields[0]='". trim($$arr_fields[0]) ."' and $arr_fields[1]=". trim($$arr_fields[1]);
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [".trim($$arr_fields[0])." : ".$$arr_fields[1]."]");
	}
	
	if($UPD){
		$cmd = "	  select 		a.$arr_fields[0], a.$arr_fields[1], $arr_fields[2], CP_NAME, PL_NAME, POS_NO   
						  from 		$table a, PER_POSITION b, PER_COMPETENCE c, PER_LINE d
						  where 		a.$arr_fields[0]=b.$arr_fields[0] and 
										a.$arr_fields[1]=c.$arr_fields[1] and 
										b.PL_CODE=d.PL_CODE and 
						  				a.$arr_fields[0]=".$$arr_fields[0]." and a.$arr_fields[1]=".$$arr_fields[1]."										
										$search_condition 
						  order by 	a.$arr_fields[0], a.$arr_fields[1], $arr_fields[2]  ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$$arr_fields[1] = $data[$arr_fields[1]];
		$$arr_fields[2] = $data[$arr_fields[2]];
		$$arr_fields[3] = $data[$arr_fields[3]];
		$$arr_fields[4] = $data[$arr_fields[4]];
		$CP_NAME = $data[CP_NAME];
		$POS_NO = $data[POS_NO];
		$PL_NAME = $data[PL_NAME];
	} // end if
	
	if( (!$UPD && !$DEL && !$err_text) ){
		$$arr_fields[0] = "";
		$$arr_fields[1] = "";
		$$arr_fields[2] = "";
		
		$CP_NAME = "";
		$POS_NO = "";
		$PL_NAME = "";
	} // end if
?>