<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");	

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);	

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	function list_kpi_competence () {
	global $db_list, $DPISDB, $PL_CP_CODE, $ISCS_FLAG, $CTRL_TYPE;
		$cmd = "	select		DISTINCT CP_CODE, CP_NAME 
									from		PER_COMPETENCE
									WHERE CP_MODEL=3 and CP_ACTIVE=1 
									order by CP_CODE, CP_NAME";
		$db_list->send_cmd($cmd);
		//$db_list->show_error();	
		if($PL_CP_CODE)	$arr_setcompetence =  explode("|", $PL_CP_CODE);
		//print_r($arr_setcompetence);
		while ($data_list = $db_list->get_array()) {
			//$data_list = array_change_key_case($data_list, CASE_LOWER);
			$cpcode = trim($data_list[CP_CODE]);
			$cpname= trim($data_list[CP_NAME]);
			$sel = (in_array($cpcode,$arr_setcompetence))?"checked":"";
			echo"<input type='checkbox' id='list_competence_id$cpcode' name='list_competence_id[]' value='$cpcode' $sel>&nbsp;$cpname<br>";
		}
		return $val;
	//echo "<pre>";		
	//print_r($data_list);
	//echo "</pre>";	
	}

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
	
	if($command=="REORDER"){
		foreach($ARR_ORDER as $PL_CODE => $PL_SEQ_NO){
		if($PL_SEQ_NO=="") { $cmd = " update PER_LINE set PL_SEQ_NO='' where PL_CODE='$PL_CODE' "; }
		else { $cmd = " update PER_LINE set PL_SEQ_NO=$PL_SEQ_NO where PL_CODE='$PL_CODE' ";  }
			$db_dpis->send_cmd($cmd);
		//	$db_dpis->show_error();
		} // end foreach

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > จัดลำดับตำแหน่งในสายงาน [$PL_CODE : $PL_NAME]");
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
	
	$setcompetence = "";
	if($list_competence_id)	$setcompetence =  implode("|", $list_competence_id);
	//echo "$setcompetence // ";
	
	if($command == "ADD" && trim($$arr_fields[0])){
		$cmd = " select $arr_fields[0], $arr_fields[1], $arr_fields[2], $arr_fields[3], $arr_fields[6], $PL_CODE_NEW from $table where $arr_fields[0]='". trim($$arr_fields[0]) ."' ";
		$count_duplicate = $db_dpis->send_cmd($cmd);
		if($count_duplicate <= 0){
		if($PL_SEQ_NO==''){
				//Access มันเป็น ชนิด Number ใส่เป็น เครื่องหมาย '' ไม่ได้ จะ Error data type
				if($DPISDB=="odbc"){
					$PL_SEQ_NO=0;
				}else if($DPISDB=="oci8" || $DPISDB=="mysql"){
					$PL_SEQ_NO="''";
				}
			}	
			$cmd = " insert into $table (". implode(", ", $arr_fields) .") values ('".trim($$arr_fields[0])."', '".$$arr_fields[1]."', '".$$arr_fields[2]."', ".$$arr_fields[3].", 
							$SESS_USERID, '$UPDATE_DATE', '".$$arr_fields[6]."', '$PL_CODE_NEW', '".$$arr_fields[8]."', '$CL_NAME', '$PL_CODE_DIRECT', 
							$LAYER_TYPE, $PL_SEQ_NO, '".$$arr_fields[13]."', '$LEVEL_NO_MIN', '$LEVEL_NO_MAX','$setcompetence', '".$$arr_fields[17]."') ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > $menu_detail เพิ่มข้อมูล [".trim($$arr_fields[0])." : ".$$arr_fields[1]."]");
			$success_sql = "เพิ่มข้อมูล [".trim($$arr_fields[0])." : ".$$arr_fields[1]."]เรียบร้อยแล้ว"; //กำหนดในส่วนของ php
		}else{
			$data = $db_dpis->get_array();			
			$err_text = "รหัสข้อมูลซ้ำ [".$data[$arr_fields[0]]." ".$data[$arr_fields[1]]." ".$data[$arr_fields[2]]."]";
		} // endif
	}

	if($command == "UPDATE" && trim($$arr_fields[0])){
		$cmd = " update $table set 
						$arr_fields[1]='".$$arr_fields[1]."', 
						$arr_fields[2]='".$$arr_fields[2]."', 
						$arr_fields[3]=".$$arr_fields[3].", 
						$arr_fields[6]='".$$arr_fields[6]."', 
						$arr_fields[13]='".$$arr_fields[13]."', 
						PL_CODE_NEW='$PL_CODE_NEW', 
						$arr_fields[8]='".$$arr_fields[8]."', 
						PL_CODE_DIRECT='$PL_CODE_DIRECT',  
						CL_NAME='$CL_NAME',  
						LAYER_TYPE=$LAYER_TYPE,  
						LEVEL_NO_MIN='$LEVEL_NO_MIN',  
						LEVEL_NO_MAX='$LEVEL_NO_MAX',
						PL_CP_CODE = '$setcompetence',  
						$arr_fields[17]='".$$arr_fields[17]."', 
						UPDATE_USER=$SESS_USERID, 
						UPDATE_DATE='$UPDATE_DATE'
						where $arr_fields[0]='".$$arr_fields[0]."' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
//		echo $cmd;

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
		$cmd = " select $arr_fields[1], $arr_fields[2], $arr_fields[3], $arr_fields[6], PL_CODE_NEW, $arr_fields[8], PL_CODE_DIRECT, 
						CL_NAME, LAYER_TYPE, $arr_fields[13], LEVEL_NO_MIN, LEVEL_NO_MAX , PL_CP_CODE, $arr_fields[17], UPDATE_USER, UPDATE_DATE
						from $table where $arr_fields[0]='".$$arr_fields[0]."' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$$arr_fields[1] = $data[$arr_fields[1]];
		if (substr($PL_CODE,0,1)=="9")
			$OCCUPATION_GROUP = $ARR_OCCUPATION_GROUP[substr($PL_CODE,2,1)];
		else
			$OCCUPATION_GROUP = $ARR_OCCUPATION_GROUP[substr($PL_CODE,1,1)];
		$$arr_fields[2] = $data[$arr_fields[2]];
		$$arr_fields[3] = $data[$arr_fields[3]];
		$$arr_fields[6] = $data[$arr_fields[6]];
		$PL_CODE_NEW = $data[PL_CODE_NEW];
		$$arr_fields[8] = $data[$arr_fields[8]];
		$PL_CODE_DIRECT = $data[PL_CODE_DIRECT];
		$CL_NAME = $data[CL_NAME];
		$CL_CODE = $CL_NAME;
		$LAYER_TYPE = $data[LAYER_TYPE];
		$$arr_fields[13] = $data[$arr_fields[13]];
		$LEVEL_NO_MIN = $data[LEVEL_NO_MIN];
		$LEVEL_NO_MAX = $data[LEVEL_NO_MAX];
		$PL_CP_CODE = $data[PL_CP_CODE];
		$$arr_fields[17] = $data[$arr_fields[17]];
		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);

		$cmd = " select PL_NAME from PER_LINE where PL_CODE = '$PL_CODE_NEW' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PL_NAME_NEW = $data[PL_NAME];

		$cmd = " select PL_NAME from PER_LINE where PL_CODE = '$PL_CODE_DIRECT' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PL_NAME_DIRECT = $data[PL_NAME];

		$cmd = " select LG_NAME from PER_LINE_GROUP where LG_CODE = '$LG_CODE' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$LG_NAME = $data[LG_NAME];
	} // end if
	
	if( (!$UPD && !$DEL && !$err_text) ){
		$$arr_fields[0] = "";
		$$arr_fields[1] = "";
		$$arr_fields[2] = "";
		$$arr_fields[3] = 1;
		$$arr_fields[6] = "0";
		$PL_CODE_NEW = "";
		$PL_NAME_NEW = "";
		$PL_CODE_DIRECT = "";
		$PL_NAME_DIRECT = "";
		$$arr_fields[8] = "";
		$LG_NAME = "";
		$CL_CODE = "";
		$CL_NAME = "";
		$LAYER_TYPE = 1;
		$$arr_fields[13] = "";
		$LEVEL_NO_MIN = "";
		$LEVEL_NO_MAX = "";
		$$arr_fields[17] = "";
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
	} // end if
?>