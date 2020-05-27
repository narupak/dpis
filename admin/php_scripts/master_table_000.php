   
<?	
	//include("php_scripts/session_start.php");
	//include("php_scripts/function_share.php");
	
	// ==== use for testing phase =====
	if($table=="PER_GROUP_N"){
		$DPISDB = "mysql";
		$db_dpis = $db;
	} // end if
	// ==========================
	$UPDATE_DATE = date("Y-m-d H:i:s");
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
	if (!$$arr_fields[5]) $$arr_fields[5] = "NULL";
	if($command == "ADD" && trim($$arr_fields[0])){
		$cmd = " select $arr_fields[0], $arr_fields[1] from $table where $arr_fields[0]='". trim($$arr_fields[0]) ."' OR $arr_fields[1]='". trim($$arr_fields[1]) ."' ";
		$count_duplicate = $db_dpis->send_cmd($cmd);
		//echo "cmd=$cmd<br>";
		if($count_duplicate <= 0){
		$cmd = " insert into $table (". implode(", ", $arr_fields) .") values ('".trim($$arr_fields[0])."', '".$$arr_fields[1]."', ".$$arr_fields[2].", 
		$SESS_USERID, '$UPDATE_DATE', ".$$arr_fields[5].", '".$$arr_fields[6]."') ";
		$db_dpis->send_cmd($cmd);
		//echo "cmd=$cmd<br>";
		//$db_dpis->show_error();
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > $menu_detail > เพิ่มข้อมูล [".trim($$arr_fields[0])." : ".$$arr_fields[1]."]");
			$err_text = "เพิ่มข้อมูล [".trim($$arr_fields[0])." : ".$$arr_fields[1]."]เรียบร้อยแล้ว";
			
		}else{
		$data = $db_dpis->get_array();			
		$err_text = "รหัสข้อมูลซ้ำหรือชื่อซ้ำ [".$data[$arr_fields[0]]." ".$data[$arr_fields[1]]."]";
		}  //endif
	}
?>