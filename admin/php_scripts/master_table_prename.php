<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

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
//	echo (implode(", ", $arr_fields));
	
	$UPDATE_DATE = date("Y-m-d H:i:s");

	if($command=="REORDER"){
		foreach($ARR_ORDER as $PN_CODE => $PN_SEQ_NO){
		if($PN_SEQ_NO=="") { $cmd = " update PER_PRENAME set PN_SEQ_NO='' where PN_CODE='$PN_CODE' "; }
		else { $cmd = " update PER_PRENAME set PN_SEQ_NO=$PN_SEQ_NO where PN_CODE='$PN_CODE' ";  }
			$db_dpis->send_cmd($cmd);
		//	$db_dpis->show_error();
		} // end foreach

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > จัดลำดับคำนำหน้าชื่อ [$PN_CODE : $PN_NAME]");
	} // end if

	if ($command == "SETFLAG") {
		$setflagshow =  implode(",",$list_show_id);
		$cmd = " update $table set $arr_fields[4] = 0 where $arr_fields[0] in (".stripslashes($current_list).") ";
		$db_dpis->send_cmd($cmd);
		$cmd = " update $table set $arr_fields[4] = 1 where $arr_fields[0] in (".stripslashes($setflagshow).") ";
		$db_dpis->send_cmd($cmd);
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการใช้งานข้อมูล");
	}

	if($command == "ADD" && trim($$arr_fields[0])){
		$$arr_fields[1] = (trim($$arr_fields[1]))? "'".trim($$arr_fields[1])."'" : 'NULL';
		$$arr_fields[2] = (trim($$arr_fields[2]))? "'".trim($$arr_fields[2])."'" : 'NULL';
		$$arr_fields[3] = (trim($$arr_fields[3]))? "'".trim($$arr_fields[3])."'" : 'NULL';
		$$arr_fields[9] = (trim($$arr_fields[9]))? "'".trim($$arr_fields[9])."'" : 'NULL';
		$cmd = " select $arr_fields[0], $arr_fields[1], $arr_fields[2], $arr_fields[3], $arr_fields[4]  from $table where $arr_fields[0]='".trim($$arr_fields[0])."'";
		$count_duplicate = $db_dpis->send_cmd($cmd);
		//echo "cmd=$cmd<br>";
		if($count_duplicate <= 0){
			if($PN_SEQ_NO==''){
				//Access มันเป็น ชนิด Number ใส่เป็น เครื่องหมาย '' ไม่ได้ จะ Error data type
				if($DPISDB=="odbc"){
					$PN_SEQ_NO=0;
				}else if($DPISDB=="oci8" || $DPISDB=="mysql"){
					$PN_SEQ_NO="''";
				}
			}		
			$cmd = " insert into $table (". implode(", ", $arr_fields) .") 
							values ('".trim($$arr_fields[0])."', ".$$arr_fields[1].", ".$$arr_fields[2].", ".$$arr_fields[3].", ".$$arr_fields[4].", 
							$SESS_USERID, '$UPDATE_DATE', ".$$arr_fields[7].",$PN_SEQ_NO, ".$$arr_fields[9].") ";
			$db_dpis->send_cmd($cmd);
			//echo "cmd=$cmd<br>";
		//	$db_dpis->show_error();
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > $menu_detail.เพิ่มข้อมูล [".trim($$arr_fields[0])." : ".$$arr_fields[2]."]");
			$success_sql = "เพิ่มข้อมูล [".trim($$arr_fields[0])." : ".$$arr_fields[2]."]เรียบร้อยแล้ว";
		}else{
			$data = $db_dpis->get_array();			
			$err_text = "รหัสข้อมูลซ้ำหรือชื่อซ้ำ [".$data[$arr_fields[0]]." ".$data[$arr_fields[1]]." ".$data[$arr_fields[2]]." ".$data[$arr_fields[3]]."]";
		} // endif
	}

	if($command == "UPDATE" && trim($$arr_fields[0])){
		$$arr_fields[1] = (trim($$arr_fields[1]))? "'".trim($$arr_fields[1])."'" : 'NULL';
		$$arr_fields[2] = (trim($$arr_fields[2]))? "'".trim($$arr_fields[2])."'" : 'NULL';
		$$arr_fields[3] = (trim($$arr_fields[3]))? "'".trim($$arr_fields[3])."'" : 'NULL';	
		$$arr_fields[9] = (trim($$arr_fields[9]))? "'".trim($$arr_fields[9])."'" : 'NULL';
		$cmd = " update $table set 
							$arr_fields[1]=".$$arr_fields[1].", 
							$arr_fields[2]=".$$arr_fields[2].", 
							$arr_fields[3]=".$$arr_fields[3].", 
							$arr_fields[4]=".$$arr_fields[4].", 
							UPDATE_USER=$SESS_USERID, 
							UPDATE_DATE='$UPDATE_DATE', 
							$arr_fields[7]=".$$arr_fields[7].", 
							$arr_fields[9]=".$$arr_fields[9]." 
							where $arr_fields[0]='".$$arr_fields[0]."' ";		
		$db_dpis->send_cmd($cmd);
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [".trim($$arr_fields[0])." : ".$$arr_fields[1]." : ".$$arr_fields[2]." : ".$$arr_fields[3]."]");
	}


	if($command == "DELETE" && trim($$arr_fields[0])){
		$cmd = " select $arr_fields[1] from $table where $arr_fields[0]='".$$arr_fields[0]."' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$$arr_fields[1] = $data[$arr_fields[1]];
		
		$cmd = " delete from $table where $arr_fields[0]='".$$arr_fields[0]."'";
		$db_dpis->send_cmd($cmd);
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [".trim($$arr_fields[0])." : ".$$arr_fields[1]." : ".$$arr_fields[2]." : ".$$arr_fields[3]."]");
		
	}


	if($UPD){
		$cmd = " select $arr_fields[1], $arr_fields[2], $arr_fields[3], $arr_fields[4], $arr_fields[7], $arr_fields[9], UPDATE_USER, UPDATE_DATE  
				  from $table where $arr_fields[0]='".$$arr_fields[0]."' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$$arr_fields[1] = $data[$arr_fields[1]];
		$$arr_fields[2] = $data[$arr_fields[2]];
		$$arr_fields[3] = $data[$arr_fields[3]];
		$$arr_fields[4] = $data[$arr_fields[4]];
		$$arr_fields[7] = $data[$arr_fields[7]];
		$$arr_fields[9] = $data[$arr_fields[9]];
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
		$$arr_fields[3] = "";
		$$arr_fields[4] = 1;
		$$arr_fields[7] = 0;
		$$arr_fields[9] = "";
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
	} // end if
?>