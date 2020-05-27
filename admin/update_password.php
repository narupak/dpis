<?	
	include("../php_scripts/connect_database.php");

	$db1 = new connect_db($db_host, $db_name, $db_user, $db_pwd);
	$db2 = new connect_db($db_host, $db_name, $db_user, $db_pwd);

	$cmd = " select ID from USER_DETAIL where USERNAME like 'M%' ";
	$db->send_cmd($cmd);
	while($data = $db->get_array()) { 
		$ID = $data[ID];
		$PASSWORD = md5('iscs');
		
		$cmd = " update USER_DETAIL set PASSWORD = '$PASSWORD' where ID = $ID ";
		$db1->send_cmd($cmd);
		//$db1->show_error()   ;
	}

	$new_group_id = 43;
	
	$cmd = " select ID from USER_GROUP where CODE like 'M%' and ID <> 43 ";
	$db2->send_cmd($cmd);
	while($data2 = $db2->get_array()) { 
	$old_group_id = $data2[ID];

	$cmd = " delete from user_privilege where group_id = $old_group_id ";
	$db->send_cmd($cmd);
	//$db->show_error()   ;

	$cmd = " select page_id, menu_id_lv0, menu_id_lv1, menu_id_lv2, menu_id_lv3, can_add, can_edit, can_del, can_inq, can_print, can_confirm 
					  from 	user_privilege where group_id = $new_group_id ";
	$db->send_cmd($cmd);
	while($data = $db->get_array()) { 
		$data = array_change_key_case($data, CASE_LOWER);
		$page_id = $data[page_id];
		$menu_id_lv0 = $data[menu_id_lv0];
		$menu_id_lv1 = $data[menu_id_lv1];
		$menu_id_lv2 = $data[menu_id_lv2];
		$menu_id_lv3 = $data[menu_id_lv3];
		$can_add = $data[can_add];
		$can_edit = $data[can_edit];
		$can_del = $data[can_del];
		$can_inq = $data[can_inq];
		$can_print = $data[can_print];
		$can_confirm = $data[can_confirm];

		$cmd = " insert into user_privilege (group_id, page_id, menu_id_lv0, menu_id_lv1, menu_id_lv2, menu_id_lv3, can_add, can_edit, 
						  can_del, can_inq, can_print, can_confirm)
						  values ($old_group_id, $page_id, $menu_id_lv0, $menu_id_lv1, $menu_id_lv2, $menu_id_lv3, '$can_add', '$can_edit', 
						  '$can_del', '$can_inq', '$can_print', '$can_confirm') ";
		$db1->send_cmd($cmd);
		//$db1->show_error()   ;
	}
	}

	echo "ประมวลผลเรียบร้อยแล้ว";

?>