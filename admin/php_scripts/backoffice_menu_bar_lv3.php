<?
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;

	$lv3_menu_label_th = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($lv3_menu_label_th)));
	$lv3_menu_label_en = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($lv3_menu_label_en)));
	if ($lv3_menu_label_en == "") $lv3_menu_label_en = $lv3_menu_label_th;	
	$lv3_menu_order = trim($lv3_menu_order) + 0;
	$linkto_web = trim($linkto_web);	
	$linkto_tid += 0;
	if($type_linkto=="N") $linkto_web = "";
	
	if($db_type=="mysql") {
		$update_date = "NOW()";
		$update_by = "'$SESS_USERID'";
	} elseif($db_type=="mssql") {
		$update_date = "GETDATE()";
		$update_by = $SESS_USERID;
	} elseif($db_type=="oci8" || $db_type=="odbc") {
		$update_date = date("Y-m-d H:i:s");
		$update_date = "'$update_date'";
		$update_by = $SESS_USERID;
	}
	
	if($command=="REORDER"){
		foreach($arr_menu_order as $lv3_menu_id => $lv3_menu_order){
			$cmd = " update backoffice_menu_bar_lv3 set menu_order='$lv3_menu_order' where parent_id_lv0 = $lv0_menu_id and parent_id_lv1 = $lv1_menu_id and parent_id_lv2=$lv2_menu_id and menu_id=$lv3_menu_id ";
			$db->send_cmd($cmd);
		} // end foreach

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > Reorder submenu [$lv2_menu_id : $lv2_menu_label_th]");
	} // end if

	if ($command == "SETFLAG") {
		$setflagshow =  implode(",",$list_show_id);
		$cmd = "	update backoffice_menu_bar_lv3 set flag_show = 'H' where fid = $CATE_FID and parent_id_lv0 = $lv0_menu_id and parent_id_lv1 = $lv1_menu_id and parent_id_lv2 = $lv2_menu_id and menu_id in (".stripslashes($current_list).") ";		
		$db->send_cmd($cmd);
		$cmd = "	update backoffice_menu_bar_lv3 set flag_show = 'S' where fid = $CATE_FID and parent_id_lv0 = $lv0_menu_id and parent_id_lv1 = $lv1_menu_id and parent_id_lv2 = $lv2_menu_id and menu_id in (".stripslashes($setflagshow).") ";
		$db->send_cmd($cmd);		

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการแสดงผลของเมนูย่อย");
	}

	if ($command == "ADD" && $lv3_menu_label_th != "") {
		if($db_type=="mysql") {
			$create_date = "NOW()";
			$create_by = "'$SESS_USERNAME'";
		} elseif($db_type=="mssql") {
			$create_date = "GETDATE()";
			$create_by = $SESS_USERID;
		} elseif($db_type=="oci8" || $db_type=="odbc") {
			$create_date = date("Y-m-d H:i:s");
			$create_date = "'$create_date'";
			$create_by = $SESS_USERID;
		}
		
		$cmd = " select max(menu_id) as max_id from backoffice_menu_bar_lv3 ";
		$db->send_cmd($cmd);
		$data = $db->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$lv3_menu_id = $data[max_id] + 1;
		$cmd = " insert into backoffice_menu_bar_lv3 (fid, langcode, menu_id, menu_order, menu_label, flag_show, type_linkto, 
						linkto_web, linkto_tid, parent_id_lv0, parent_id_lv1, parent_id_lv2, create_date, create_by, update_date, update_by)
						values ($CATE_FID, 'TH', $lv3_menu_id, $lv3_menu_order, '$lv3_menu_label_th', 'S', '$type_linkto', '$linkto_web', 
						$linkto_tid, $lv0_menu_id, $lv1_menu_id, $lv2_menu_id, $update_date ,$update_by, $update_date, $update_by) ";
		$db->send_cmd($cmd);
		
//		$db->show_error();
		$cmd = " insert into backoffice_menu_bar_lv3 (fid, langcode, menu_id, menu_order, menu_label, flag_show, type_linkto, 
						linkto_web, linkto_tid, parent_id_lv0, parent_id_lv1, parent_id_lv2, create_date, create_by, update_date, update_by)
						values ($CATE_FID, 'EN', $lv3_menu_id, $lv3_menu_order,' $lv3_menu_label_en', 'S', '$type_linkto', '$linkto_web', 
						$linkto_tid, $lv0_menu_id, $lv1_menu_id, $lv2_menu_id, $update_date, $update_by, $update_date, $update_by) ";
		$db->send_cmd($cmd);		
		
		$cmd = " insert into user_privilege (group_id, page_id, menu_id_lv0, menu_id_lv1, menu_id_lv2, menu_id_lv3, can_add, can_edit, can_del, can_inq, can_print, can_confirm)
						 values (1, 1, $lv0_menu_id, $lv1_menu_id, $lv2_menu_id, $lv3_menu_id, 'Y', 'Y', 'Y', 'Y', 'Y', 'Y') ";
		$db->send_cmd($cmd);
	
		$CLEAR = 1;
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มเมนูย่อย [$lv3_menu_label_th]");
	}
	
	if ($command == "UPDATE" && $lv3_menu_label_th != "") {
		$cmd = "	update backoffice_menu_bar_lv3 set
				menu_order = '$lv3_menu_order',
				menu_label = '$lv3_menu_label_th',
				type_linkto = '$type_linkto',
				linkto_web = '$linkto_web',
				parent_id_lv0 = '$change_parent_id_lv0',
				parent_id_lv1 = '$change_parent_id_lv1',
				parent_id_lv2 = '$change_parent_id_lv2',
				update_date = $update_date,
				update_by = $update_by
			where fid = $CATE_FID and menu_id = $lv3_menu_id and langcode = 'TH'";
		$db->send_cmd($cmd);
		
		$cmd = "	update backoffice_menu_bar_lv3 set
				menu_order = '$lv3_menu_order',
				menu_label = '$lv3_menu_label_en',
				type_linkto = '$type_linkto',
				linkto_web = '$linkto_web',
				parent_id_lv0 = '$change_parent_id_lv0',
				parent_id_lv1 = '$change_parent_id_lv1',
				parent_id_lv2 = '$change_parent_id_lv2',
				update_date = $update_date,
				update_by = $update_by
			where fid = $CATE_FID and menu_id = $lv3_menu_id and langcode = 'EN'";
		$db->send_cmd($cmd);		
//		$db->show_error();
		// update user privilege
		$cmd = " update user_privilege set 
						 menu_id_lv0=$change_parent_id_lv0, 
						 menu_id_lv1=$change_parent_id_lv1, 
						 menu_id_lv2=$change_parent_id_lv2 
						 where page_id=1 and menu_id_lv0=$lv0_menu_id and menu_id_lv1=$lv1_menu_id and 
						 menu_id_lv2=$lv2_menu_id and menu_id_lv3=$lv3_menu_id ";
		$db->send_cmd($cmd);
		
		$CLEAR = 1;
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขเมนูย่อย [$lv3_menu_label_th]");
	}
		
	if ($command == "DELETE" && $lv3_menu_id) {
		$cmd = " select menu_label from backoffice_menu_bar_lv3 
						where fid = $CATE_FID and menu_id = $lv3_menu_id and parent_id_lv0 = $lv0_menu_id and 
						parent_id_lv1 = $lv1_menu_id and parent_id_lv2 = $lv2_menu_id and langcode='TH' ";
		$db->send_cmd($cmd);
		$data = $db->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$lv1_menu_label_th = $data[menu_label];
		
		$cmd = " delete from user_privilege where page_id=1 and menu_id_lv2=$lv2_menu_id and menu_id_lv3=$lv3_menu_id ";
		$db->send_cmd($cmd);
	
		$cmd = " delete from backoffice_menu_bar_lv3 
						where fid = $CATE_FID and menu_id = $lv3_menu_id and parent_id_lv0 = $lv0_menu_id and 
						parent_id_lv1 = $lv1_menu_id and parent_id_lv2 = $lv2_menu_id";
		$db->send_cmd($cmd);

		$CLEAR = 1;
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบเมนูย่อย [$lv3_menu_label_th]");
	}
			
	if ($UPD) {
		$cmd = "select 		menu_order, menu_label, type_linkto, linkto_web, linkto_tid, update_by, update_date
						from 		backoffice_menu_bar_lv3 
						where 	fid = $CATE_FID and menu_id = $lv3_menu_id and langcode = 'TH' and 
						parent_id_lv0 = $lv0_menu_id and parent_id_lv1 = $lv1_menu_id and parent_id_lv2 = $lv2_menu_id";
		$db->send_cmd($cmd);
		$data = $db->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$lv3_menu_order = $data[menu_order];
		$lv3_menu_label_th =  $data[menu_label];
		$type_linkto =  $data[type_linkto];
		$linkto_web = $data[linkto_web];
		$linkto_tid = $data[linkto_tid];		
		$update_user = $data[update_by];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $update_user ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[update_date]), $DATE_DISPLAY);

		$cmd = "select 		menu_label
						from 		backoffice_menu_bar_lv3 
						where 	fid = $CATE_FID and menu_id = $lv3_menu_id and langcode = 'TH' and 
						parent_id_lv0 = $lv0_menu_id and parent_id_lv1 = $lv1_menu_id and parent_id_lv2 = $lv2_menu_id";
		$db->send_cmd($cmd);
		$data = $db->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$lv3_menu_label_en =  $data[menu_label];
	}
	if ( !$UPD && !$DEL ) {
		unset($lv3_menu_id);
		unset($lv3_menu_order);
		unset($lv3_menu_label_th);
		unset($lv3_menu_label_en);
		unset($type_linkto);
		unset($linkto_web);
		unset($linkto_tid);		
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
	}
	
	$FORMDATA_ACTION = 1;
	if (!$type_linkto) $type_linkto = "W";
	 $tmp = "type_linkto$type_linkto"; $$tmp = "checked";	 		 

?>