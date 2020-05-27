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
	
	if($command=="REORDER"){
		foreach($ARR_ORDER as $TR_CODE => $TR_SEQ_NO){
		if($TR_SEQ_NO=="") { $cmd = " update PER_TRAIN set TR_SEQ_NO='' where TR_CODE='$TR_CODE' "; }
		else { $cmd = " update PER_TRAIN set TR_SEQ_NO=$TR_SEQ_NO where TR_CODE='$TR_CODE' ";  }
			$db_dpis->send_cmd($cmd);
		//	$db_dpis->show_error();
		} // end foreach

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > จัดลำดับหลักสูตรการฝึกอบรม ดูงาน [$TR_CODE : $TR_NAME]");
	} // end if

	if ($command == "SETFLAG") {
		$setflagshow =  implode(",", $list_show_id);
//echo "$setflagshow<br>";
		$cmd = " update $table set $arr_fields[3] = 0 where $arr_fields[0] in (".stripslashes($current_list).") ";
		$db_dpis->send_cmd($cmd);
//$db_dpis->show_error(); echo "<br>";
		$cmd = " update $table set $arr_fields[3] = 1 where $arr_fields[0] in (".stripslashes($setflagshow).") ";
		$db_dpis->send_cmd($cmd);
//$db_dpis->show_error(); echo "<br>";

		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการใช้งานข้อมูล");
	}
	
	if($command == "ADD" && trim($$arr_fields[0])){
		$cmd = " select $arr_fields[0], $arr_fields[1], $arr_fields[2], $arr_fields[3] from $table where $arr_fields[0]='". trim($$arr_fields[0]) ."' or $arr_fields[2]='". trim($$arr_fields[2]) ."' ";
		$count_duplicate = $db_dpis->send_cmd($cmd);
		if($count_duplicate <= 0){
			if($TR_SEQ_NO==''){
				//Access มันเป็น ชนิด Number ใส่เป็น เครื่องหมาย '' ไม่ได้ จะ Error data type
				if($DPISDB=="odbc"){
					$TR_SEQ_NO=0;
				}else if($DPISDB=="oci8" || $DPISDB=="mysql"){
					$TR_SEQ_NO="''";
				}
			}
			$$arr_fields[2] = str_replace("'", "&rsquo;", $$arr_fields[2]);
			$cmd = " insert into $table (". implode(", ", $arr_fields) .") values ('".trim($$arr_fields[0])."', ".$$arr_fields[1].", '".$$arr_fields[2]."', ".$$arr_fields[3].", $SESS_USERID, '$UPDATE_DATE',$TR_SEQ_NO, '".$$arr_fields[7]."', '$TG_CODE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [".trim($$arr_fields[0])." : ".$$arr_fields[1]."]");
		}else{
			$data = $db_dpis->get_array();			
			$err_text = "รหัส/ชื่อข้อมูลซ้ำ [".$data[$arr_fields[0]]." ".$data[$arr_fields[1]]." ".$data[$arr_fields[2]]."]";
		} // endif
	}

	if($command == "UPDATE" && trim($$arr_fields[0])){
		$$arr_fields[2] = str_replace("'", "&rsquo;", $$arr_fields[2]);	
		$cmd = " update $table set 
						$arr_fields[1]='".$$arr_fields[1]."', 
						$arr_fields[2]='".$$arr_fields[2]."', 
						$arr_fields[3]=".$$arr_fields[3].",  
						$arr_fields[7]='".$$arr_fields[7]."', 
						TG_CODE='$TG_CODE', 
						UPDATE_USER=$SESS_USERID, 
						UPDATE_DATE='$UPDATE_DATE' 
						where $arr_fields[0]='".$$arr_fields[0]."' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [".trim($$arr_fields[0])." : ".$$arr_fields[1]." : ".$$arr_fields[2]."]");
	}
	
	if($command == "DELETE" && trim($$arr_fields[0])){
		$cmd = " select $arr_fields[1] from $table where $arr_fields[0]='".$$arr_fields[0]."' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$$arr_fields[1] = $data[$arr_fields[1]];
		
		$cmd = " delete from $table where $arr_fields[0]='".$$arr_fields[0]."' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [".trim($$arr_fields[0])." : ".$$arr_fields[1]." : ".$$arr_fields[2]."]");
	}
	
	if($UPD){
		$cmd = " select $arr_fields[1], $arr_fields[2], $arr_fields[3], $arr_fields[7], TG_CODE, UPDATE_USER, UPDATE_DATE from $table where $arr_fields[0]='".$$arr_fields[0]."' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$$arr_fields[1] = $data[$arr_fields[1]];
		$$arr_fields[2] = $data[$arr_fields[2]];
		$$arr_fields[3] = $data[$arr_fields[3]];
		$$arr_fields[7] = $data[$arr_fields[7]];
		$TG_CODE = $data[TG_CODE];
		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);
		
		$cmd = " select TG_NAME from PER_TRAIN_GROUP where TG_CODE='$TG_CODE' "; 
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$TG_NAME = $data[TG_NAME];
	} // end if
	
	if( (!$UPD && !$DEL && !$err_text) ){
		$$arr_fields[0] = "";
		$$arr_fields[1] = 1;
		$$arr_fields[2] = "";
		$$arr_fields[3] = 1;
		$$arr_fields[7] = "";
		$TG_CODE = "";
		$TG_NAME = "";
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
	} // end if
?>