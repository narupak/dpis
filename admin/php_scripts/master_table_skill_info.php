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
	for($i=1; $i<=count($field_list); $i++) :
		$arr_fields[] = $field_list[$i]["name"];
	endfor;
//	echo (implode(", ", $arr_fields));
	
	$UPDATE_DATE = date("Y-m-d H:i:s");

	if($command == "ADD" && trim($$arr_fields[1])){
		$cmd = " select $arr_fields[0], $arr_fields[1] from $table where $arr_fields[1]='". trim($$arr_fields[1]) ."' ";
		$count_duplicate = $db_dpis->send_cmd($cmd);
		if($count_duplicate <= 0){
			$arr_insert_fields = array_diff($arr_fields, array($arr_fields[0]));
			
		$cmd = " select max(JOB_DES_ID) as max_id from SKILL_INFO ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$$arr_fields[0] = $data[max_id] + 1;	
			
			$cmd = " insert into $table (". implode(", ", $arr_fields) .") values (".trim($$arr_fields[0]).", '".$$arr_fields[1]."','".$$arr_fields[2]."', 
							$SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			//echo "$cmd <hr>";
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [".trim($$arr_fields[0])." : ".$$arr_fields[1]." : ".$$arr_fields[2]."]");
			$cmd = " select $arr_fields[0] from $table where $arr_fields[1]='".$$arr_fields[1]."' ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$$arr_fields[0] = $data[$arr_fields[0]];

			for($i=1; $i<=7; $i++){
				$cmd = " insert into SKILL_LEVEL ($arr_fields[0], job_des_level, job_des_level_description, UPDATE_USER, UPDATE_DATE) 
								 values (".$$arr_fields[0].", $i, '".${"skill_level_desc_".$i}."', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
				//echo $cmd."<HR>";
			} // end for
		}else{
			$data = $db_dpis->get_array();			
			$err_text = "รหัสข้อมูลซ้ำ [".$data[$arr_fields[0]]." ".$data[$arr_fields[1]]."]";
		} // endif
	}

	if($command == "UPDATE" && trim($$arr_fields[1])){
		$cmd = " update $table set $arr_fields[1]='".$$arr_fields[1]."', $arr_fields[2]='".$$arr_fields[2]."', 
							UPDATE_USER=$SESS_USERID, 
							UPDATE_DATE='$UPDATE_DATE'  
							where $arr_fields[0]=".$$arr_fields[0];
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [".trim($$arr_fields[0])." : ".$$arr_fields[1]." : ".$$arr_fields[2]."]");

		for($i=1; $i<=7; $i++){
			$cmd = " update SKILL_LEVEL set								
								job_des_level_description = '".${"skill_level_desc_".$i}."', 
								UPDATE_USER=$SESS_USERID, 
								UPDATE_DATE='$UPDATE_DATE' 
							 where $arr_fields[0]=".$$arr_fields[0]." and job_des_level=$i ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
		} // end for
	}
	
	if($command == "DELETE" && trim($$arr_fields[0])){
		$cmd = " select $arr_fields[1] from $table where $arr_fields[0]=".$$arr_fields[0];
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$$arr_fields[1] = $data[$arr_fields[1]];
		
		$cmd = " delete from SKILL_LEVEL where $arr_fields[0]=".$$arr_fields[0];
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		
		$cmd = " delete from $table where $arr_fields[0]=".$$arr_fields[0];
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [".trim($$arr_fields[0])." : ".$$arr_fields[1]."]");
	}
	
	if($UPD){
		$cmd = " select 	$arr_fields[1], $arr_fields[2], UPDATE_USER, UPDATE_DATE
				 		 from 		$table
				 		 where 	$arr_fields[0]=".$$arr_fields[0];
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
		
		for($i=1; $i<=7; $i++){
			$cmd = " select job_des_level_description from SKILL_LEVEL where $arr_fields[0]=".$$arr_fields[0]." and job_des_level=$i ";
			$db_dpis->send_cmd($cmd);			
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			${"skill_level_desc_".$i} = $data[job_des_level_description];
		} // end for
	} // end if
	
	if( (!$UPD && !$DEL && !$err_text) ){
		$$arr_fields[0] = "";
		$$arr_fields[1] = "";
		$$arr_fields[2] = "";
		for($i=1; $i<=7; $i++) ${"skill_level_desc_".$i} = "";
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
	} // end if
?>