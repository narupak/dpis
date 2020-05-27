<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$table = "PER_COMPETENCY_TEST";
	
	$cmd = " SELECT * from $table ";
	$db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();
	$field_list = $db_dpis->list_fields($table);
	//echo "<pre>";		print_r($field_list);		echo "</pre>";
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
	
	$UPDATE_DATE = date("Y-m-d H:i:s");
	if ($command == "SETFLAG") {
		$setflagshow =  "'".implode("','",$cpt_active)."'";
		$current_list =  str_replace("\\", "", $current_list); 
		if($current_list){
			$cmd = " update per_competency_test set CPT_ACTIVE = 0 WHERE CPT_CODE in (".stripslashes($current_list).") ";
			//echo($cmd);
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			}
		if($setflagshow){
			$cmd = " update per_competency_test set CPT_ACTIVE = 1 WHERE CPT_CODE in (".stripslashes($setflagshow).") ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();	
			}
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการแสดงผลของแบบประเมินสมรรถนะ");
		}

	if($command == "ADD" && trim($$arr_fields[1]) && trim($$arr_fields[2])){
		$cmd = " 	SELECT  $arr_fields[1], $arr_fields[2] FROM $table 
						WHERE $arr_fields[1]='". trim($$arr_fields[1]) ."' AND $arr_fields[2]='". trim($$arr_fields[2])."'";
		$count_duplicate = $db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		if($count_duplicate <= 0){
			$cmd = " INSERT INTO $table (". implode(", ", $arr_fields) .") VALUES ('".$$arr_fields[0]."', '".$$arr_fields[1]."', '".$$arr_fields[2]."', 1, $SESS_USERID, '$UPDATE_DATE') ";
			//echo($cmd);
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [".trim($$arr_fields[1])." : ".$$arr_fields[2]."]");
			$SELECTED_QS_ID=explode(",", $SELECTED_QS_ID);
			$i=0;
			foreach($SELECTED_QS_ID as $QS_ID){
				$i++;
				$cmd = " INSERT INTO PER_COMPETENCY_DTL (CPT_CODE,  QS_ID, SEQ_NO,UPDATE_USER, UPDATE_DATE) 
								  VALUES ('".$$arr_fields[0]."', $QS_ID, $i, $SESS_USERID, '$UPDATE_DATE')";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
				}//foreach($SELECTED_QS_ID as $QS_ID => $QS_LIST){
			}else{
			$data = $db_dpis->get_array();			
			$err_text = "รหัสข้อมูลซ้ำ [".$data[$arr_fields[1]]." ".$data[$arr_fields[2]]."]";
			} // endif
		}

	if($command == "UPDATE" && trim($$arr_fields[0])){
		$cmd = " 	UPDATE $table SET $arr_fields[1]='".$$arr_fields[1]."', $arr_fields[2]='".$$arr_fields[2]."', UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' 
						WHERE $arr_fields[0]='".$$arr_fields[0]."' ";
		$db_dpis->send_cmd($cmd);
		$OLD_SELECTED_QS=explode(",",$OLD_SELECTED_QS_ID[$$arr_fields[0]]);
		$SELECTED_QS_ID=explode(",", $SELECTED_QS_ID);
		$NEW_QS_ID = array_diff($SELECTED_QS_ID, $OLD_SELECTED_QS);
		$DEL_QS_ID = array_diff($OLD_SELECTED_QS, $SELECTED_QS_ID);
		$DEL_QS_ID="'".implode("','", $DEL_QS_ID)."'";
		$cmd = " DELETE from PER_COMPETENCY_DTL WHERE $arr_fields[0]='". trim($$arr_fields[0]) ."' AND QS_ID IN (".$DEL_QS_ID.") ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		$i=0;		
		foreach($NEW_QS_ID as $QS_ID){
			$i++;
			$cmd = " INSERT INTO PER_COMPETENCY_DTL (CPT_CODE,  QS_ID, SEQ_NO,UPDATE_USER, UPDATE_DATE) 
							  VALUES ('".$$arr_fields[0]."', $QS_ID, $i, $SESS_USERID, '$UPDATE_DATE')";
			$db_dpis->send_cmd($cmd);
			}//foreach($NEW_QS_ID as $QS_ID){
		//$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [".trim($$arr_fields[0])." : ".$$arr_fields[1]." : ".$$arr_fields[2]."]");
	}
	if($command == "DELETE" && trim($$arr_fields[0])){
		$cmd = " DELETE from $table WHERE $arr_fields[0]='". trim($$arr_fields[0]) ."' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$cmd = " DELETE from PER_COMPETENCY_DTL WHERE $arr_fields[0]='". trim($$arr_fields[0]) ."' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [".trim($$arr_fields[0])." : ".$$arr_fields[1]."]");
	}
	
	if($UPD){
		$cmd ="select	a.$arr_fields[0],a.$arr_fields[1],a.$arr_fields[2], $arr_fields[3], a.$arr_fields[3], CP_NAME 
								from $table a, PER_COMPETENCE b
					where	a.$arr_fields[2]=b.$arr_fields[2] and CPT_CODE ='$CPT_CODE'
					order by a.$arr_fields[0], a.$arr_fields[1] ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$$arr_fields[0] = $data[$arr_fields[0]];
		$$arr_fields[1] = $data[$arr_fields[1]];
		$$arr_fields[2] = $data[$arr_fields[2]];
		$CP_NAME = $data[CP_NAME];
		
		$cmd = " SELECT QS_ID FROM PER_COMPETENCY_DTL WHERE CPT_CODE='".$CPT_CODE."' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		while($data = $db_dpis->get_array()){ $ARR_SELECTED_QS_ID[] = $data[QS_ID];}
		$SELECTED_QS_ID = implode(",", $ARR_SELECTED_QS_ID);
	} // end if
	
	if( (!$UPD && !$DEL && !$err_text) ){
		$$arr_fields[0] = "";
		$$arr_fields[1] = "";
		$$arr_fields[2] = "";
		$CP_NAME = "";
		$SELECTED_QS_ID = "";
	} // if( (!$UPD && !$DEL && !$err_text) ){
?>