<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$cmd = " select * from $table ";
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
	
/*	if($command=="REORDER"){
		foreach($ARR_ORDER as $PT_CODE => $MS_SEQ_NO){
		if($MS_SEQ_NO=="") { $cmd = " update PER_MGTSALARY set  MS_SEQ_NO='' where PT_CODE='$PT_CODE' "; }
		else { $cmd = " update PER_MGTSALARY set MS_SEQ_NO=$MS_SEQ_NO where PT_CODE='$PT_CODE' ";  }
			$db_dpis->send_cmd($cmd);
			$db_dpis->show_error();
			echo "<hr>".$cmd;
		} // end foreach

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > จัดลำดับเงินประจำตำแหน่ง [$PT_CODE : $MS_SEQ_NO]");
	} // end if
	
	*/

	if ($command == "SETFLAG") {
		$setflagshow =  implode(",", $list_show_id);
//		echo "$setflagshow<br>";
		if($DPISDB=="oci8") $cmd = " update $table set $arr_fields[3] = 0 where concat(trim($arr_fields[0]), concat('|', trim($arr_fields[1]))) in (".stripslashes($current_list).") ";
		elseif($DPISDB=="odbc") $cmd = " update $table set $arr_fields[3] = 0 where trim($arr_fields[0]) + '|' + trim($arr_fields[1]) in (".stripslashes($current_list).") ";
		elseif($DPISDB=="mysql") $cmd = " update $table set $arr_fields[3] = 0 where trim($arr_fields[0]) + '|' + trim($arr_fields[1]) in (".stripslashes($current_list).") ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error(); echo "<br>";
		if($DPISDB=="oci8") $cmd = " update $table set $arr_fields[3] = 1 where concat(trim($arr_fields[0]), concat('|', trim($arr_fields[1]))) in (".stripslashes($setflagshow).") ";
		elseif($DPISDB=="odbc") $cmd = " update $table set $arr_fields[3] = 1 where trim($arr_fields[0]) + '|' + trim($arr_fields[1]) in (".stripslashes($setflagshow).") ";
		elseif($DPISDB=="mysql") $cmd = " update $table set $arr_fields[3] = 1 where trim($arr_fields[0]) + '|' + trim($arr_fields[1]) in (".stripslashes($setflagshow).") ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error(); echo "<br>";

		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการใช้งานข้อมูล");
	}
	
	if($command == "ADD" && trim($$arr_fields[0])){
		$cmd = " select $arr_fields[0], $arr_fields[1], $arr_fields[2], $arr_fields[3] from $table where $arr_fields[0]='".$$arr_fields[0]."' and $arr_fields[1]='".$$arr_fields[1]."' ";
		$count_duplicate = $db_dpis->send_cmd($cmd);
		if($count_duplicate <= 0){
		if($MS_SEQ_NO==''){
				//Access มันเป็น ชนิด Number ใส่เป็น เครื่องหมาย '' ไม่ได้ จะ Error data type
				if($DPISDB=="odbc"){
					$MS_SEQ_NO=0;
				}else if($DPISDB=="oci8" || $DPISDB=="mysql"){
					$MS_SEQ_NO="''";
				}
			}	
			$cmd = " insert into $table (". implode(", ", $arr_fields) .") values ('".trim($$arr_fields[0])."', '".$$arr_fields[1]."', '".$$arr_fields[2]."', ".$$arr_fields[3].", $SESS_USERID, '$UPDATE_DATE',$MS_SEQ_NO) ";
			$db_dpis->send_cmd($cmd);
		//	$db_dpis->show_error();
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [".trim($$arr_fields[0])." : ".$$arr_fields[1]." : ".$$arr_fields[2]."]");
		}else{
			$data = $db_dpis->get_array();			
			$err_text = "รหัสข้อมูลซ้ำ [".$data[$arr_fields[0]]." ".$data[$arr_fields[1]]." ".$data[$arr_fields[2]]."]";
		} // endif
	}

	if($command == "UPDATE" && trim($$arr_fields[0])){
		$cmd = " update $table set $arr_fields[2]='".$$arr_fields[2]."', $arr_fields[3]=".$$arr_fields[3].",  UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' where $arr_fields[0]='".$$arr_fields[0]."' and $arr_fields[1]='".$$arr_fields[1]."'";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [".trim($$arr_fields[0])." : ".$$arr_fields[1]." : ".$$arr_fields[2]."]");
	}
	
	if($command == "DELETE" && trim($$arr_fields[0]) && trim($$arr_fields[1])){
		$cmd = " select $arr_fields[2] from $table where $arr_fields[0]='".$$arr_fields[0]."' and $arr_fields[1]='".$$arr_fields[1]."' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$$arr_fields[2] = $data[$arr_fields[2]];
		
		$cmd = " delete from $table where $arr_fields[0]='".$$arr_fields[0]."' and $arr_fields[1]='".$$arr_fields[1]."' ";
		$db_dpis->send_cmd($cmd);
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [".trim($$arr_fields[0])." : ".$$arr_fields[1]." : ".$$arr_fields[2]."]");
	}
	
	if($UPD){
		$cmd = " select a.$arr_fields[0], $arr_fields[1], $arr_fields[2], $arr_fields[3], PT_NAME
						 from 	$table a, PER_TYPE b 
						 where a.$arr_fields[0] = b.$arr_fields[0] and a.$arr_fields[0]='".$$arr_fields[0]."' and $arr_fields[1] = '".$$arr_fields[1]."' ";
		$db_dpis->send_cmd($cmd);
		//echo($cmd);
		$data = $db_dpis->get_array();
		$$arr_fields[0] = trim($data[$arr_fields[0]]);
		$$arr_fields[1] = trim($data[$arr_fields[1]]);
		$$arr_fields[2] = $data[$arr_fields[2]];
		$$arr_fields[3] = $data[$arr_fields[3]];
		$PT_NAME = $data[PT_NAME];
	} // end if
	
	if( (!$UPD && !$DEL && !$err_text) ){
		$$arr_fields[0] = "";
		$PT_NAME = "";
		$$arr_fields[1] = "";
		$$arr_fields[2] = "";
		$$arr_fields[3] = 1;
	} // end if
?>