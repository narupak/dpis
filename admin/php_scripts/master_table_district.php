<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");

	//print("<pre>");	print_r($_POST);	print("</pre>");	
	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	if(!$CT_CODE){ 
		$CT_CODE = 140;											// ประเทศไทย
		if(!$PV_CODE) $PV_CODE = 1000;				// กรุงเทพมหานคร
		if(!$AP_CODE) $AP_CODE = 1001;				// เขตพระนคร
	} // end if

	$cmd = " select * from $table ";	//      PER_DISTRICT
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
	
	$UPDATE_DATE = date("Y-m-d H:i:s");
	
	if($command=="REORDER"){
		foreach($ARR_ORDER as $DT_CODE => $DT_SEQ_NO){
			if($DT_SEQ_NO=="") { $cmd = " update PER_DISTRICT set DT_SEQ_NO='' where DT_CODE='$DT_CODE' "; }
			else {	$cmd = " update PER_DISTRICT set DT_SEQ_NO=$DT_SEQ_NO where DT_CODE='$DT_CODE' "; }
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			//echo "<hr>".$cmd;
		} // end foreach

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > จัดลำดับตำบล [$DT_CODE : $DT_NAME]");
	} // end if

	if ($command == "SETFLAG") {
		$setflagshow =  implode(",",$list_show_id);
//		echo "$setflagshow";
		$cmd = " update $table set DT_ACTIVE=0 where DT_CODE in (".stripslashes($current_list).") ";
		$db_dpis->send_cmd($cmd);
//$db_dpis->show_error();
		$cmd = " update $table set DT_ACTIVE=1 where DT_CODE in (".stripslashes($setflagshow).") ";
		$db_dpis->send_cmd($cmd);
//$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการใช้งานข้อมูล");
	}

	if($command == "ADD" && (trim($DT_CODE) && trim($AP_CODE))){
		$cmd = " select DT_CODE, DT_NAME from $table where DT_CODE='". trim($DT_CODE) ."' ";
		$count_duplicate = $db_dpis->send_cmd($cmd);
		if($count_duplicate <= 0){
			if($DT_SEQ_NO==''){
				//Access มันเป็น ชนิด Number ใส่เป็น เครื่องหมาย '' ไม่ได้ จะ Error data type
				if($DPISDB=="odbc"){
					$DT_SEQ_NO=0;
				}else if($DPISDB=="oci8" || $DPISDB=="mysql"){
					$DT_SEQ_NO="''";
				}
			}	
			$cmd = " insert into $table (DT_CODE, DT_NAME, PV_CODE, AP_CODE,ZIP_CODE, DT_SEQ_NO, DT_OTHERNAME, DT_ACTIVE, UPDATE_USER, UPDATE_DATE) 
							values ('$DT_CODE', '$DT_NAME', '$PV_CODE', '$AP_CODE', '$ZIP_CODE', $DT_SEQ_NO, '$DT_OTHERNAME', $DT_ACTIVE, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//echo "$cmd ";
			//$db_dpis->show_error();
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > $menu_detail เพิ่มข้อมูล [".trim($DT_CODE)." : ".$DT_CODE."]");
			$success_sql = "เพิ่มข้อมูล [".trim($$arr_fields[0])." : ".$$arr_fields[1]."]เรียบร้อยแล้ว"; //กำหนดในส่วนของ php
			
		}else{
			$data = $db_dpis->get_array();			
			$err_text = "รหัสข้อมูลซ้ำ [".$data[DT_CODE]." ".$data[DT_NAME]."]";
		} // endif
	}

	if($command == "UPDATE" && trim($$arr_fields[0])){
		$cmd = " update $table set 
						DT_NAME='$DT_NAME',
						ZIP_CODE='$ZIP_CODE', 
						PV_CODE='$CHANGE_PV_CODE', 
						AP_CODE='$CHANGE_AP_CODE', 
						DT_OTHERNAME='$DT_OTHERNAME',
						UPDATE_USER=$SESS_USERID, 
						UPDATE_DATE='$UPDATE_DATE' 
						where DT_CODE='".$DT_CODE."' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [".trim($DT_CODE)." : ".$DT_NAME."]");
	}
	
	if($command == "DELETE" && trim($$arr_fields[0])){
		$cmd = " select DT_NAME from $table where DT_CODE='".$DT_CODE."' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$DT_NAME = $data[DT_NAME];
		
		$cmd = " delete from $table where DT_CODE='".$DT_CODE."' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [".trim($DT_CODE)." : ".$DT_NAME."]");
	}
	
	$cmd = " select CT_NAME from PER_COUNTRY where CT_CODE='$CT_CODE' ";
	$db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data = $db_dpis->get_array();
	$CT_NAME = $data[CT_NAME];

	$cmd = " select PV_NAME from PER_PROVINCE where CT_CODE='$CT_CODE' and PV_CODE='$PV_CODE' ";
	$db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data = $db_dpis->get_array();
	$PV_NAME = $data[PV_NAME];
//	echo "PV_NAME = $PV_NAME <br>";

	 $cmd = " select  AP_NAME from PER_AMPHUR where PV_CODE='$PV_CODE' and AP_CODE='$AP_CODE' ";
	$db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data = $db_dpis->get_array();
	$AP_NAME = $data[AP_NAME];

	$cmd = " select DT_NAME from PER_DISTRICT where PV_CODE='$PV_CODE' and AP_CODE='$AP_CODE' and DT_CODE='$DT_CODE'";
	$db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data = $db_dpis->get_array();
	$DT_NAME = $data[DT_NAME];
	
	if($UPD){
		$cmd = " select DT_CODE, DT_NAME, PV_CODE, AP_CODE, ZIP_CODE, DT_SEQ_NO, DT_OTHERNAME, DT_ACTIVE, UPDATE_USER, UPDATE_DATE
						 from $table 
						 where (DT_CODE='".$$arr_fields[0]."') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		//echo "upd-$cmd<br>";
		$data = $db_dpis->get_array();
		$DT_CODE= $data[DT_CODE];
		$DT_NAME = $data[DT_NAME];
		$ZIP_CODE = $data[ZIP_CODE];
		$DT_ACTIVE = $data[DT_ACTIVE]; 
		$PV_CODE = $data[PV_CODE];
		$AP_CODE = $data[AP_CODE];
		$DT_OTHERNAME = $data[DT_OTHERNAME];
		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);
		
		$CHANGE_AP_CODE = $AP_CODE;
		$cmd = " select AP_NAME from PER_AMPHUR where AP_CODE='$CHANGE_AP_CODE'";
		$db_dpis->send_cmd($cmd);
		//	$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$CHANGE_AP_NAME = $data[AP_NAME];
		
		$CHANGE_PV_CODE = $PV_CODE;	
		$cmd = " select PV_NAME from PER_PROVINCE where PV_CODE='$CHANGE_PV_CODE'";
		$db_dpis->send_cmd($cmd);
		//	$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$CHANGE_PV_NAME = $data[PV_NAME];
	} // end if
	
	if( (!$UPD && !$DEL && !$err_text) ){
		$$arr_fields[0] = "";
		$$arr_fields[1] = "";
		$$arr_fields[4] = "";
		$$arr_fields[9] = "";
		$CHANGE_PV_CODE = "";	$CHANGE_PV_NAME = "";
		$CHANGE_AP_CODE = "";	$CHANGE_AP_NAME = "";
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
	} // end if
?>