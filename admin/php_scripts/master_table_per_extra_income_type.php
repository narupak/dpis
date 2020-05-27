<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$cmd = " select * from PER_EXTRA_INCOME_TYPE ";
	$db_dpis->send_cmd($cmd);
	$field_list = $db_dpis->list_fields(PER_EXTRA_INCOME_TYPE);
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
	if (!$EXIN_SEQ_NO) $EXIN_SEQ_NO = "NULL";

	if($command=="REORDER"){
		foreach($ARR_ORDER as $CODE => $SEQ_NO){
		if($SEQ_NO=="") { $cmd = " update PER_EXTRA_INCOME_TYPE set EXIN_SEQ_NO='' where EXIN_CODE='$CODE' "; }
		else {	$cmd = " update PER_EXTRA_INCOME_TYPE set EXIN_SEQ_NO=$SEQ_NO where EXIN_CODE='$CODE' "; }
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			//echo "<br>".$cmd;
		} // end foreach

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > จัดลำดับ [$CODE : $SEQ_NO]");
	} // end if

	if ($command == "SETFLAG") {
		$setflagshow =  implode(",",$list_show_id);
//		echo "$setflagshow";
		$cmd = " update PER_EXTRA_INCOME_TYPE set EXIN_ACTIVE = 0 where EXIN_CODE in (".stripslashes($current_list).") ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$cmd = " update PER_EXTRA_INCOME_TYPE set EXIN_ACTIVE = 1 where EXIN_CODE in (".stripslashes($setflagshow).") ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการใช้งานข้อมูล");
	}
	
	if($command == "ADD" && trim($EXIN_CODE)){
		$cmd = " select EXIN_CODE, EXIN_NAME from PER_EXTRA_INCOME_TYPE where EXIN_CODE='". trim($EXIN_CODE) ."' ";
		$count_duplicate = $db_dpis->send_cmd($cmd);
		if($count_duplicate <= 0){
			$cmd = " insert into PER_EXTRA_INCOME_TYPE (EXIN_CODE,EXIN_NAME,UPDATE_USER,UPDATE_DATE,EXIN_ACTIVE,EXIN_SEQ_NO) values ('$EXIN_CODE','$EXIN_NAME',$SESS_USERID,'$UPDATE_DATE',$EXIN_ACTIVE,$EXIN_SEQ_NO) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [".trim($EXIN_CODE)." : ".$EXIN_NAME."]");
		}else{
			$data = $db_dpis->get_array();			
			$err_text = "รหัสข้อมูลซ้ำ [".$data[EXIN_CODE]." ".$data[EXIN_NAME]."]";
		} // endif
	}

	if($command == "UPDATE" && trim($EXIN_CODE)){
		$cmd = " update PER_EXTRA_INCOME_TYPE set EXIN_NAME='".$EXIN_NAME."', EXIN_ACTIVE=".$EXIN_ACTIVE.", UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE', EXIN_SEQ_NO=".$EXIN_SEQ_NO." where EXIN_CODE='".$EXIN_CODE."' ";		
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [".trim($EXIN_CODE)." : ".$EXIN_NAME."]");
	}
	
	if($command == "DELETE" && trim($EXIN_CODE)){
		$cmd = " select EXIN_NAME from PER_EXTRA_INCOME_TYPE where EXIN_CODE='".$EXIN_CODE."' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$EXIN_NAME = $data[EXIN_NAME];
		//echo $cmd;
		
		$cmd = " delete from PER_EXTRA_INCOME_TYPE where EXIN_CODE='".$EXIN_CODE."' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [".trim($EXIN_CODE)." : ".$EXIN_NAME."]");
	}
	
	if($UPD){
		$cmd = " select EXIN_NAME, EXIN_ACTIVE, EXIN_SEQ_NO from PER_EXTRA_INCOME_TYPE where EXIN_CODE='".$EXIN_CODE."' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$EXIN_NAME = $data[EXIN_NAME];
		$EXIN_ACTIVE = $data[EXIN_ACTIVE];
		$EXIN_SEQ_NO = $data[EXIN_SEQ_NO];
	} // end if
	
	if( (!$UPD && !$DEL && !$err_text) ){
		$EXIN_CODE = "";
		$EXIN_NAME = "";
		$EXIN_ACTIVE = 1;
		$EXIN_SEQ_NO = 0;
	} // end if
?>