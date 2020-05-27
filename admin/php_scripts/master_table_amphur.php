<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	if(!$CT_CODE){ 
		$CT_CODE = 140;											// �������
		if(!$PV_CODE) $PV_CODE = 1000;				// ��ا෾��ҹ��
	} // end if

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
		foreach($ARR_ORDER as $AP_CODE => $AP_SEQ_NO){
			if($AP_SEQ_NO=="") { $cmd = " update PER_AMPHUR set AP_SEQ_NO='' where AP_CODE='$AP_CODE' "; }
		else {	$cmd = " update PER_AMPHUR set AP_SEQ_NO=$AP_SEQ_NO where AP_CODE='$AP_CODE' "; }
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			//echo "<hr>".$cmd;
		} // end foreach

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > �Ѵ�ӴѺ����� [$AP_CODE : $AP_NAME]");
	} // end if

	if ($command == "SETFLAG") {
		$setflagshow =  implode(",",$list_show_id);
//		echo "$setflagshow";
		$cmd = " update $table set $arr_fields[3] = 0 where $arr_fields[0] in (".stripslashes($current_list).") ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$cmd = " update $table set $arr_fields[3] = 1 where $arr_fields[0] in (".stripslashes($setflagshow).") ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ��駤�ҡ����ҹ������");
	}
	
	if($command == "ADD" && trim($$arr_fields[0])){
		if ($menu_detail == "����� >"){$$arr_fields[2] = $PV_CODE1;}
		$cmd = " select $arr_fields[0], $arr_fields[1] from $table where $arr_fields[0]='". trim($$arr_fields[0]) ."' ";
		$count_duplicate = $db_dpis->send_cmd($cmd);
		if($count_duplicate <= 0){
			if($AP_SEQ_NO==''){
				//Access �ѹ�� ��Դ Number ����� ����ͧ���� '' ����� ��  Error data type
				if($DPISDB=="odbc"){
					$AP_SEQ_NO=0;
				}else if($DPISDB=="oci8" || $DPISDB=="mysql"){
					$AP_SEQ_NO="''";
				}
			}	
			$cmd = " insert into $table (". implode(", ", $arr_fields) .") values ('".trim($$arr_fields[0])."', '".$$arr_fields[1]."', '".$$arr_fields[2]."', ".$$arr_fields[3].", 
							$SESS_USERID, '$UPDATE_DATE',$AP_SEQ_NO, '".$$arr_fields[7]."') ";
			$db_dpis->send_cmd($cmd);
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > $menu_detail ���������� [".trim($$arr_fields[0])." : ".$$arr_fields[1]."]");
			$success_sql = "���������� [".trim($$arr_fields[0])." : ".$$arr_fields[1]."]���º��������"; //��˹����ǹ�ͧ php
		}else{
			$data = $db_dpis->get_array();			
			$err_text = "���ʢ����ū�����ͪ��ͫ�� [".$data[$arr_fields[0]].":".$data[$arr_fields[1]]."]";
		} // endif
	}

	if($command == "UPDATE" && trim($$arr_fields[0])){
		$cmd = " update $table set 
						$arr_fields[1]='".$$arr_fields[1]."', 
						$arr_fields[2]='".$CHANGE_PV_CODE."', 
						$arr_fields[3]=".$$arr_fields[3].", 
						$arr_fields[7]='".$$arr_fields[7]."', 
						UPDATE_USER=$SESS_USERID, 
						UPDATE_DATE='$UPDATE_DATE' 
						where $arr_fields[0]='".$$arr_fields[0]."' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ��䢢����� [".trim($$arr_fields[0])." : ".$$arr_fields[1]."]");
	}
	
	if($command == "DELETE" && trim($$arr_fields[0])){
		$cmd = " select $arr_fields[1] from $table where $arr_fields[0]='".$$arr_fields[0]."' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$$arr_fields[1] = $data[$arr_fields[1]];
		
		$cmd = " delete from $table where $arr_fields[0]='".$$arr_fields[0]."'";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ź������ [".trim($$arr_fields[0])." : ".$$arr_fields[1]."]");
		
	}
	
	if($UPD){
		$cmd = " select $arr_fields[1], a.$arr_fields[2], $arr_fields[3], $arr_fields[7], PV_NAME, a.UPDATE_USER, a.UPDATE_DATE
				 from $table a, PER_PROVINCE b
				 where $arr_fields[0]='".$$arr_fields[0]."' and a.$arr_fields[2] = b.$arr_fields[2]";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$$arr_fields[1] = $data[$arr_fields[1]];
		$CHANGE_PV_CODE = $data[$arr_fields[2]];
		$CHANGE_PV_NAME = $data[PV_NAME];
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
		$CHANGE_PV_NAME = "";
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
	} // end if
?>