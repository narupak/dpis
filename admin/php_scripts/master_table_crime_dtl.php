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
		foreach($ARR_ORDER as $CRD_CODE => $CRD_SEQ_NO){
		if($CRD_SEQ_NO=="") { $cmd = " update PER_CRIME_DTL set CRD_SEQ_NO='' where CRD_CODE='$CRD_CODE' "; }
		else { $cmd = " update PER_CRIME_DTL set CRD_SEQ_NO=$CRD_SEQ_NO where CRD_CODE='$CRD_CODE' ";  }
			$db_dpis->send_cmd($cmd);
		//	$db_dpis->show_error();
		} // end foreach

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > จัดลำดับกรณีความผิด [$CRD_CODE : $CRD_NAME]");
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
		$cmd = " select $arr_fields[0], $arr_fields[1], $arr_fields[2], $arr_fields[3] from $table 
                    where $arr_fields[0]='". trim($$arr_fields[0]) ."' 
                        OR ($arr_fields[1] ='".trim($$arr_fields[1])."' AND trim($arr_fields[2]) ='".trim($$arr_fields[2])."')"; 
                //AND $arr_fields[1] ='".trim($arr_fields[1])."' 
               //echo $cmd;
		$count_duplicate = $db_dpis->send_cmd($cmd);
		if($count_duplicate <= 0){
		if($CRD_SEQ_NO==''){
				//Access มันเป็น ชนิด Number ใส่เป็น เครื่องหมาย '' ไม่ได้ จะ Error data type
				if($DPISDB=="odbc"){
					$CRD_SEQ_NO=0;
				}else if($DPISDB=="oci8" || $DPISDB=="mysql"){
					$CRD_SEQ_NO="''";
				}
			}	
			$cmd = " insert into $table (". implode(", ", $arr_fields) .") 
                                values ('".substr(trim($$arr_fields[0]), 0,10)."', 
                                        '".substr(trim($$arr_fields[1]), 0,4000)."', 
                                        '".trim($$arr_fields[2])."', 
                                        ".$$arr_fields[3].", 
                                        $SESS_USERID, 
                                        '$UPDATE_DATE',
                                        $CRD_SEQ_NO, 
                                        '".$$arr_fields[7]."' ) ";
			$db_dpis->send_cmd($cmd);
                       // echo $cmd;
//			$db_dpis->show_error();
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 >$MENU_TITLE_LV2 > $menu_detail เพิ่มข้อมูล [".trim($$arr_fields[0])." : ".$$arr_fields[1]."]");
			$success_sql = "เพิ่มข้อมูล [".trim($$arr_fields[0])." : ".$$arr_fields[1]."]เรียบร้อยแล้ว"; //กำหนดในส่วนของ php
		}else{
			$data = $db_dpis->get_array();			
			$err_text = "รหัสซ้ำ หรือ ชื่อวินัยซ้ำในฐานความผิดเดียวกัน [".$data[$arr_fields[0]]." ".$data[$arr_fields[1]]." ".$data[$arr_fields[2]]."]";
		} // endif
	}

	if($command == "UPDATE" && trim($$arr_fields[0])){
		$cmd = " update $table set 
						$arr_fields[1]='".$$arr_fields[1]."', 
						$arr_fields[2]='".$$arr_fields[2]."', 
						$arr_fields[3]=".$$arr_fields[3].",  
						$arr_fields[7]='".$$arr_fields[7]."', 
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
		$cmd = " select 	$arr_fields[1], a.$arr_fields[2], $arr_fields[3], $arr_fields[7], CR_NAME, a.UPDATE_USER, a.UPDATE_DATE
				 		 from 		$table a, PER_CRIME b
				 		 where 	$arr_fields[0]='".$$arr_fields[0]."' and a.$arr_fields[2] = b.$arr_fields[2]";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$$arr_fields[1] = $data[$arr_fields[1]];
		$CR_CODE = $data[$arr_fields[2]];
		$CR_NAME = $data[CR_NAME];
		$$arr_fields[3] = $data[$arr_fields[3]];
		$$arr_fields[7] = $data[$arr_fields[7]];
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
		$$arr_fields[3] = 1;
		$$arr_fields[7] = "";
		$CR_NAME = "";
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
	} // end if
?>