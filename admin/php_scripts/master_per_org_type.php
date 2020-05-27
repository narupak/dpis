<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
/*	$cmd = " select * from PER_ORG_TYPE ";
	$db_dpis->send_cmd($cmd);
	$field_list = $db_dpis->list_fields($table);
//	echo "<pre>";		print_r($field_list);		echo "</pre>";
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

*/
	
	$UPDATE_DATE = date("Y-m-d H:i:s");

	if ($command == "SETFLAG") {
		$setflagshow =  implode(",", $list_show_id);
//echo "$setflagshow<br>";
		$cmd = " update PER_ORG_TYPE set OT_ACTIVE = 0 where OT_CODE in (".stripslashes($current_list).") ";
		$db_dpis->send_cmd($cmd);
//$db_dpis->show_error(); echo "<br>";
		$cmd = " update PER_ORG_TYPE set OT_ACTIVE = 1 where OT_CODE in (".stripslashes($setflagshow).") ";
		$db_dpis->send_cmd($cmd);
//$db_dpis->show_error(); echo "<br>";

		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการใช้งานข้อมูล");
	}
	
	if($command == "ADD" && trim($OT_CODE)){
		$cmd = " select OT_CODE, OT_NAME, OT_ACTIVE from PER_ORG_TYPE where OT_CODE='". trim($OT_CODE) ."' ";
		$count_duplicate = $db_dpis->send_cmd($cmd);
		if($count_duplicate <= 0){
			$cmd = " insert into PER_ORG_TYPE (OT_CODE, OT_NAME, OT_ACTIVE, UPDATE_USER, UPDATE_DATE) values ('".trim($OT_CODE)."', '".$OT_NAME."', ".$OT_ACTIVE.", $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			//echo $cmd;

			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [".trim($OT_CODE)." : ".$OT_NAME."]");
			$UPD = "";
			
		}else{
			$data = $db_dpis->get_array();			
			$err_text = "รหัสข้อมูลซ้ำ [".$data[OT_CODE]." ".$data[OT_NAME]."]";
		} // endif
	}

	if($command == "UPDATE" && trim($OT_CODE)){
		$cmd = " update PER_ORG_TYPE set OT_NAME='".$OT_NAME."', OT_ACTIVE=".$OT_ACTIVE.", UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' where OT_CODE='".$OT_CODE."' ";
		$db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();
	

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [".trim($OT_CODE)." : ".$OT_NAME." : ".$OT_ACTIVE."]");
		$UPD = "";
	}
	
	if($command == "DELETE" && trim($OT_CODE)){
		$cmd = " select OT_NAME from PER_ORG_TYPE where OT_CODE='".$OT_CODE."' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$OT_NAME = $data[OT_NAME];
		
		$cmd = " delete from PER_ORG_TYPE where OT_CODE='".$OT_CODE."' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [".trim($OT_CODE)." : ".$OT_NAME." : ".$OT_ACTIVE."]");
		$UPD = "";
	}
	
	if($command == "CANCEL"){
	
	$UPD = "";
	
	}
	
	
	if($UPD){
		$cmd = " select OT_NAME, OT_ACTIVE from PER_ORG_TYPE where OT_CODE='".$OT_CODE."' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$OT_NAME = $data[OT_NAME];
		$OT_ACTIVE = $data[OT_ACTIVE];
		
	} // end if
	
	if( (!$UPD && !$DEL && !$err_text) ){
		$OT_CODE = "";
		$OT_NAME = "";
		$OT_ACTIVE = 1;
		
	} // end if
?>