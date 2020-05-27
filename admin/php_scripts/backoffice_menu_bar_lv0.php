<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;

	$lv0_menu_label_th = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($lv0_menu_label_th)));
	$lv0_menu_label_en = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($lv0_menu_label_en)));	
	if ($lv0_menu_label_en == "") $lv0_menu_label_en = $lv0_menu_label_th;
	$lv0_menu_linkto = trim($lv0_menu_linkto);
	$lv0_menu_order = trim($lv0_menu_order) + 0;
	$linkto_web = trim($linkto_web);
	$linkto_tid	+= 0;
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
		foreach($arr_menu_order as $lv0_menu_id => $lv0_menu_order){
			$cmd = " update backoffice_menu_bar_lv0 set menu_order='$lv0_menu_order' where menu_id=$lv0_menu_id ";
			$db->send_cmd($cmd);
		} // end foreach

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > Reorder menu");
	} // end if

	if ($command == "SETFLAG") {
		$setflagshow =  implode(",",$list_show_id);
		$cmd = " update backoffice_menu_bar_lv0 set flag_show = 'H' where fid = $CATE_FID and menu_id in (".stripslashes($current_list).") ";
		$db->send_cmd($cmd);
		$cmd = " update backoffice_menu_bar_lv0 set flag_show = 'S' where fid = $CATE_FID and menu_id in (".stripslashes($setflagshow).") ";
		$db->send_cmd($cmd);
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการแสดงผลของเมนูหลัก");
	}
		
	if ($command == "ADD" && $lv0_menu_label_th != "") {
		$cmd = " select max(menu_id) as max_id from backoffice_menu_bar_lv0 ";
		$db->send_cmd($cmd);
		$data = $db->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$lv0_menu_id = $data[max_id] + 1;
		$cmd = " insert into backoffice_menu_bar_lv0 (fid, langcode, menu_id, menu_order, menu_icon, menu_label, 
						flag_show, type_linkto, linkto_web, linkto_tid, create_date, create_by, update_date, update_by)
						values ($CATE_FID, 'TH', $lv0_menu_id, $lv0_menu_order, '$lv0_menu_icon_name', '$lv0_menu_label_th', 
						'S', '$type_linkto', '$linkto_web', $linkto_tid, $update_date, $update_by, $update_date, $update_by) ";
		$db->send_cmd($cmd);
	
		$cmd = " insert into backoffice_menu_bar_lv0 (fid, langcode, menu_id, menu_order, menu_icon, menu_label, 
						flag_show, type_linkto, linkto_web, linkto_tid, create_date, create_by, update_date, update_by)
						values ($CATE_FID,'EN', $lv0_menu_id, $lv0_menu_order, '$lv0_menu_icon_name', '$lv0_menu_label_en', 
						'S', '$type_linkto', '$linkto_web', $linkto_tid, $update_date, $update_by, $update_date, $update_by) ";
		$db->send_cmd($cmd);
	
		$cmd = " insert into user_privilege (group_id, page_id, menu_id_lv0, menu_id_lv1, menu_id_lv2, menu_id_lv3, can_add, can_edit, can_del, can_inq, can_print, can_confirm)
						 values (1, 1, $lv0_menu_id, 0, 0, 0, 'Y', 'Y', 'Y', 'Y', 'Y', 'Y') ";
		$db->send_cmd($cmd);
		
		
		$CLEAR = 1;
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มเมนูหลัก [$lv0_menu_label_th]");
	}
	
	if ($command == "UPDATE" && $lv0_menu_label_th != "") {
		$cmd = "	update backoffice_menu_bar_lv0 set
				$upd_menu_icon
				menu_order = $lv0_menu_order,
				menu_label = '$lv0_menu_label_th',
				type_linkto = '$type_linkto',
				linkto_web = '$linkto_web',
				update_date = $update_date,
				update_by = $update_by
			where fid = $CATE_FID and menu_id = $lv0_menu_id and langcode = 'TH' ";
		$db->send_cmd($cmd);
		
		$cmd = "	update backoffice_menu_bar_lv0 set
				$upd_menu_icon
				menu_order = $lv0_menu_order,
				menu_label = '$lv0_menu_label_en',
				type_linkto = '$type_linkto',
				linkto_web = '$linkto_web',
				update_date = $update_date,
				update_by = $update_by
			where fid = $CATE_FID and menu_id = $lv0_menu_id and langcode = 'EN' ";
		$db->send_cmd($cmd);
		
		$CLEAR = 1;
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขเมนูหลัก [$lv0_menu_label_th]");
	}
		
	if ($command == "DELETE" && $lv0_menu_id) {
		$cmd = " select menu_label from backoffice_menu_bar_lv0 where fid = $CATE_FID and menu_id = $lv0_menu_id and langcode='TH' ";
		$db->send_cmd($cmd);
		$data = $db->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$lv0_menu_label_th = $data[menu_label];
		
		$cmd = "  delete from backoffice_menu_bar_lv3 where fid = $CATE_FID and parent_id_lv0 = $lv0_menu_id ";
		$db->send_cmd($cmd);
		
		$cmd = "  delete from backoffice_menu_bar_lv2 where fid = $CATE_FID and parent_id_lv0 = $lv0_menu_id ";
		$db->send_cmd($cmd);
		
		$cmd = " delete from backoffice_menu_bar_lv1 where fid = $CATE_FID and parent_id_lv0 = $lv0_menu_id ";
		$db->send_cmd($cmd);
		
		$cmd = " delete from user_privilege where page_id=1 and menu_id_lv0=$lv0_menu_id ";
		$db->send_cmd($cmd);
		
		$cmd = " delete from backoffice_menu_bar_lv0 where fid = $CATE_FID and menu_id = $lv0_menu_id ";
		$db->send_cmd($cmd);
		
		$CLEAR = 1;
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบเมนูหลัก [$lv0_menu_label_th]");
	}
			
	if ($UPD) {
		$cmd = " 	select 		menu_order, menu_icon, menu_label, type_linkto, linkto_web, linkto_tid, update_by, update_date 
							from 		backoffice_menu_bar_lv0 
							where 	fid = $CATE_FID and menu_id = $lv0_menu_id and langcode = 'TH' ";
		$db->send_cmd($cmd);
		$data = $db->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$lv0_menu_order = $data[menu_order];
		$lv0_menu_icon =  $data[menu_icon];		
		$lv0_menu_label_th =  $data[menu_label];
		$type_linkto =  $data[type_linkto];
		$linkto_web = $data[linkto_web];
		$linkto_tid = $data[linkto_tid];
		$update_user = $data[update_by];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $update_user ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[update_date]), $DATE_DISPLAY);

		$cmd = " select menu_label from backoffice_menu_bar_lv0 where fid = $CATE_FID and menu_id = $lv0_menu_id and langcode = 'EN' ";
		$db->send_cmd($cmd);
		$data = $db->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$lv0_menu_label_en =  $data[menu_label];
	}
	if ( !$UPD && !$DEL ) {
		unset($lv0_menu_id);
		unset($lv0_menu_order);
		unset($lv0_menu_icon);
		unset($lv0_menu_label_th);
		unset($lv0_menu_label_en);
		unset($lv0_menu_linkto);	
		unset($type_linkto);
		unset($linkto_web);
		unset($linkto_tid);
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
	}
	
	if (!$type_linkto) $type_linkto = "N";
	 $tmp = "type_linkto$type_linkto"; $$tmp = "checked";	
	 
?>