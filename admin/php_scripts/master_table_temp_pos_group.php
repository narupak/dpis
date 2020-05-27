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
		foreach($ARR_ORDER as $TG_CODE => $TG_SEQ_NO){
		if($TG_SEQ_NO=="") { $cmd = " update PER_TEMP_POS_GROUP set TG_SEQ_NO='' where TG_CODE='$TG_CODE' "; }
		else { $cmd = " update PER_TEMP_POS_GROUP set TG_SEQ_NO=$TG_SEQ_NO where TG_CODE='$TG_CODE' ";  }
			$db_dpis->send_cmd($cmd);
		//	$db_dpis->show_error();
		} // end foreach

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > �Ѵ�ӴѺ��Ǵ���˹��١��ҧ [$TG_CODE : $TG_NAME]");
	} // end if

	if ($command == "SETFLAG") {
		$setflagshow =  implode(",",$list_show_id);
		$cmd = " update $table set $arr_fields[3] = 0 where $arr_fields[0] in (".stripslashes($current_list).") ";
		$db_dpis->send_cmd($cmd);
		$cmd = " update $table set $arr_fields[3] = 1 where $arr_fields[0] in (".stripslashes($setflagshow).") ";
		$db_dpis->send_cmd($cmd);
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ��駤�ҡ����ҹ������");
	}

	if($command == "ADD" && trim($$arr_fields[0])){
		$$arr_fields[1] = (trim($$arr_fields[1]))? "'".trim($$arr_fields[1])."'" : 'NULL';
		$$arr_fields[6] = (trim($$arr_fields[6]))? "'".trim($$arr_fields[6])."'" : 'NULL';
		$cmd = " select $arr_fields[0], $arr_fields[1], $arr_fields[2]  from $table where $arr_fields[0]='". trim($$arr_fields[0]) ."' ";
		$count_duplicate = $db_dpis->send_cmd($cmd);
		if($count_duplicate <= 0){
		if($TG_SEQ_NO==''){
				//Access �ѹ�� ��Դ Number ����� ����ͧ���� '' ����� �� Error data type
				if($DPISDB=="odbc"){
					$TG_SEQ_NO=0;
				}else if($DPISDB=="oci8" || $DPISDB=="mysql"){
					$TG_SEQ_NO="''";
				}
			}	
			$cmd = " insert into $table (". implode(", ", $arr_fields) .") values ('".trim($$arr_fields[0])."', ".$$arr_fields[1].",$TG_SEQ_NO, ".$$arr_fields[3].", 
							$SESS_USERID,'$UPDATE_DATE', ".$$arr_fields[6].") ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > $menu_detail ���������� [".trim($$arr_fields[0])." : ".$$arr_fields[1]." : ".$$arr_fields[2]."]");
			$success_sql = "���������� [".trim($$arr_fields[0])." : ".$$arr_fields[1]."]���º��������"; //��˹����ǹ�ͧ php
			
		}else{
			$data = $db_dpis->get_array();			
			$err_text = "���ʢ����ū�� [".$data[$arr_fields[0]]." ".$data[$arr_fields[1]]." ".$data[$arr_fields[2]]."]";
		} // endif
	}

	if($command == "UPDATE" && trim($$arr_fields[0])){ 
		$$arr_fields[1] = (trim($$arr_fields[1]))? "'".trim($$arr_fields[1])."'" : 'NULL';
		$$arr_fields[6] = (trim($$arr_fields[6]))? "'".trim($$arr_fields[6])."'" : 'NULL';
		$cmd = " update $table set 
						$arr_fields[1]=".$$arr_fields[1].", 
						$arr_fields[3]=".$$arr_fields[3].", 
						$arr_fields[6]=".$$arr_fields[6].", 
						UPDATE_USER=$SESS_USERID, 
						UPDATE_DATE='$UPDATE_DATE' 
						where $arr_fields[0]='".$$arr_fields[0]."' ";		
		$db_dpis->send_cmd($cmd);
		//echo $cmd;
		//$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ��䢢����� [".trim($$arr_fields[0])." : ".$$arr_fields[1]." : ".$$arr_fields[2]."]");
	}

	if($command == "DELETE" && trim($$arr_fields[0])){
		$cmd = " select $arr_fields[1] from $table where $arr_fields[0]='".$$arr_fields[0]."' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$$arr_fields[1] = $data[$arr_fields[1]];
		
		$cmd = " delete from $table where $arr_fields[0]='".$$arr_fields[0]."' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ź������ [".trim($$arr_fields[0])." : ".$$arr_fields[1]." : ".$$arr_fields[2]."]");
	}

	if($UPD){
		$cmd = " select $arr_fields[1], $arr_fields[3], $arr_fields[6], UPDATE_USER, UPDATE_DATE
						from $table where $arr_fields[0]='".$$arr_fields[0]."' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$$arr_fields[1] = $data[$arr_fields[1]];
		$$arr_fields[3] = $data[$arr_fields[3]];
		$$arr_fields[6] = $data[$arr_fields[6]];
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
		$$arr_fields[6] = "";
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
	} // end if
?>