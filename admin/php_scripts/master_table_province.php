<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	if(!$CT_CODE) $CT_CODE = 140;						// �������

	$cmd = " select CT_NAME from PER_COUNTRY where CT_CODE='$CT_CODE' ";
	$db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data = $db_dpis->get_array();
	$CT_NAME = $data[CT_NAME];
			
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
		foreach($ARR_ORDER as $PV_CODE => $PV_SEQ_NO){
			if($PV_SEQ_NO=="") {  $cmd = " update PER_PROVINCE set PV_SEQ_NO='' where PV_CODE='$PV_CODE' "; }
		else {	$cmd = " update PER_PROVINCE set PV_SEQ_NO=$PV_SEQ_NO where PV_CODE='$PV_CODE' "; }
			$db_dpis->send_cmd($cmd);
		//	$db_dpis->show_error();
		} // end foreach

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > �Ѵ�ӴѺ�ѧ��Ѵ [$PV_CODE : $PV_NAME]");
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
		if ($menu_detail == "�ѧ��Ѵ >"){$$arr_fields[2] = $CT_CODE;}
		$cmd = " select $arr_fields[0], $arr_fields[1] from $table where $arr_fields[0]='". trim($$arr_fields[0]) ."' OR $arr_fields[1]='". trim($$arr_fields[1]) ."'";
		$count_duplicate = $db_dpis->send_cmd($cmd);
		if($count_duplicate <= 0){
		if($PV_SEQ_NO==''){
				//Access �ѹ�� ��Դ Number ����� ����ͧ���� '' ����� �� Error data type
				if($DPISDB=="odbc"){
					$PV_SEQ_NO=0;
				}else if($DPISDB=="oci8" || $DPISDB=="mysql"){
					$PV_SEQ_NO="''";
				}
			}		
			
			$cmd = " insert into $table (". implode(", ", $arr_fields) .") values ('".trim($$arr_fields[0])."', '".$$arr_fields[1]."', '".$$arr_fields[2]."', ".$$arr_fields[3].", 
							$SESS_USERID, '$UPDATE_DATE', $PV_SEQ_NO, '".$$arr_fields[7]."') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > $menu_detail ���������� [".trim($$arr_fields[0])." : ".$$arr_fields[1]."]");
			$success_sql = "���������� [".trim($$arr_fields[0])." : ".$$arr_fields[1]."]���º��������"; //��˹����ǹ�ͧ php
			
		}else{
			$data = $db_dpis->get_array();			
			$err_text = "���ʢ����ū�����ͪ��ͫ�� [".$data[$arr_fields[0]]." ".$data[$arr_fields[1]]."]";
		} // endif
	}

	if($command == "UPDATE" && trim($$arr_fields[0])){
		$cmd = " update $table set 
							$arr_fields[1]='".$$arr_fields[1]."', 
							$arr_fields[2]='".$CHANGE_CT_CODE."', 
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
		
		$cmd = " delete from $table where $arr_fields[0]='".$$arr_fields[0]."' ";
		$db_dpis->send_cmd($cmd);
		//		$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ź������ [".trim($$arr_fields[0])." : ".$$arr_fields[1]."]");
	}
	
	if($UPD){
		$cmd = " select 	$arr_fields[1], a.$arr_fields[2], $arr_fields[3], $arr_fields[7], CT_NAME, a.UPDATE_USER, a.UPDATE_DATE
				 		 from 		$table a, PER_COUNTRY b
				 		 where 	$arr_fields[0]='".$$arr_fields[0]."' and a.$arr_fields[2] = b.$arr_fields[2]";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$$arr_fields[1] = $data[$arr_fields[1]];
		$CHANGE_CT_CODE = $data[$arr_fields[2]];
		$CHANGE_CT_NAME = $data[CT_NAME];
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
		$CHANGE_CT_NAME = "";
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
	} // end if
?>  