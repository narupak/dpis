<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$cmd = " select * from $table ";
	$db_dpis->send_cmd($cmd);
	$field_list = $db_dpis->list_fields($table);
//	echo "<pre>  $cmd   ";		print_r($field_list);		echo "</pre>";
	unset($arr_fields);
	for($i=1; $i<=count($field_list); $i++) :
		$arr_fields[] = strtoupper($field_list[$i]["name"]);
	endfor;
//	echo (implode(", ", $arr_fields));
	
	$UPDATE_DATE = date("Y-m-d H:i:s");

	if($command == "ADD" && (trim($$arr_fields[0]) && trim($$arr_fields[1]))){
		$cmd = " select $arr_fields[0], $arr_fields[1] from $table where $arr_fields[0]='". trim($$arr_fields[0]) ."' and $arr_fields[1]=". $$arr_fields[1];
		//echo $cmd;
		$count_duplicate = $db_dpis->send_cmd($cmd);
		$db_dpis->send_cmd($cmd);
		if($count_duplicate <= 0){		   
			$cmd = " insert into $table (". implode(", ", $arr_fields) .") values ('".trim($$arr_fields[0])."', '".$$arr_fields[1]."') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
           	//echo $cmd;

			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [".trim($$arr_fields[0])." : ".$$arr_fields[1]."]");
		}else{
			$data = $db_dpis->get_array();			
			
			$cmd = " select MJT_NAME from PER_MAIN_JOB where MJT_ID=".$data[$arr_fields[1]];
			$db_dpis->send_cmd($cmd);
			$data1 = $db_dpis->get_array();
			$err_text = "รหัสข้อมูลซ้ำ [".$data[$arr_fields[0]]." ".$data1[MJT_NAME]."]";
		} // endif
	}
 
	if($command == "DELETE" && trim($$arr_fields[0]) && trim($$arr_fields[1])){
		$cmd = " delete from $table where $arr_fields[0]='".$$arr_fields[0]."' and $arr_fields[1]=".$$arr_fields[1];
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [".trim($$arr_fields[0])." : ".$$arr_fields[1]."]");
	}
	
	if( (!$UPD && !$DEL && !$err_text) ){
		$$arr_fields[0] = "";
		$$arr_fields[1] = "";
		$MJT_NAME = "";
	} // end if
?>