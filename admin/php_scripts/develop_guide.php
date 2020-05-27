<?
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

//	echo "$SESS_USERGROUP_LEVEL :: $SESS_PROVINCE_CODE :: $SESS_MINISTRY_ID :: $SESS_DEPARTMENT_ID";
	
	$guide_desc = trim($guide_desc);
	$guide_id = trim($guide_id);

	$UPDATE_DATE = date("Y-m-d H:i:s");

	if ($command == "ADD") {
		$cmd = " select max(pd_guide_id) as max_id from per_develope_guide ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
//		$db_dpis->show_error();
		$data = array_change_key_case($data, CASE_LOWER);
		$max_id = $data[max_id] + 1;
//		echo "max_id=$max_id<br>";
		$cmd = "insert into per_develope_guide
						(pd_guide_id, pd_guide_level, pd_guide_competence, pd_guide_description1, pd_guide_description2, update_user, update_date ) values 
						($max_id, $guide_level, $guide_cp, '$guide_desc1', '$guide_desc2', $SESS_USERID,'$UPDATE_DATE')";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$CLEAR = 1;

//		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มกลุ่มผู้ใช้งาน [$group_name_th]");
	}

	if ($command == "UPDATE") {
		$update_code = "";
		if($guide_id){ $update_code = " pd_guide_id = $guide_id, "; }
		$cmd = "update per_develope_guide set
						$update_code
						pd_guide_level = $guide_level,
						pd_guide_competence = '$guide_cp',
						pd_guide_description1 = '$guide_desc1',
						pd_guide_description2 = '$guide_desc2',
						update_date = '$UPDATE_DATE',
						update_user = $SESS_USERID
						where pd_guide_id = $guide_id";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		$CLEAR = 1;

//		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขกลุ่มผู้ใช้งาน [$group_name_th]");
	}

	if ($command == "DELETE" && $guide_id) {
		$cmd = " select pd_guide_description from per_develope_guide where pd_guide_id=$guide_id ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$guide_desc = $data[pd_guide_description];
		
		$cmd = " delete from per_develope_guide where pd_guide_id = $guide_id ";
		$db_dpis->send_cmd($cmd);

		$CLEAR = 1;

//		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบกลุ่มผู้ใช้งาน [$group_name_th]");
	}

	$cmd = "select pd_guide_id, pd_guide_level, pd_guide_competence, pd_guide_description1, pd_guide_description2
					from per_develope_guide 
					order by pd_guide_id, pd_guide_level, pd_guide_competence";
	$count_develop_guide = $db_dpis->send_cmd($cmd);
	if($count_develop_guide) :
		$i = 0;
		while($data = $db_dpis->get_array()) :
			$data = array_change_key_case($data, CASE_LOWER);
			$ARR_GUIDE_ID[] = $data[pd_guide_id];
			$guide_row[$i]["id"] = $data[pd_guide_id];
			$cmd = " select * from PER_COMPETENCE where CP_CODE='$data[pd_guide_competence]' ";
			$db_dpis2->send_cmd($cmd);
			//$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$guide_row[$i]["level"] = $data[pd_guide_level];
			$guide_row[$i]["cp_name"] = $data2[CP_NAME];
			$guide_row[$i]["desc1"] = $data[pd_guide_description1];
			$guide_row[$i]["desc2"] = $data[pd_guide_description2];
			$i++;
		endwhile;
	endif;

	if ($UPD || $DEL) {
		$cmd = " select pd_guide_id, pd_guide_level, pd_guide_competence, pd_guide_description1, pd_guide_description2
						from per_develope_guide 
						where pd_guide_id=$guide_id ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$guide_level = $data[pd_guide_level];
		$guide_cp = $data[pd_guide_competence];
		$guide_desc1 = $data[pd_guide_description1];
		$guide_desc2 = $data[pd_guide_description2];
		$cmd = " select * from PER_COMPETENCE where CP_CODE='$guide_cp' ";
		$db_dpis2->send_cmd($cmd);
		//$db_dpis2->show_error();
		$data2 = $db_dpis2->get_array();
		$guide_cp_name = $data2[CP_NAME];
	} // if

	if ( !$UPD && !$DEL) {
		$guide_id = "";
		unset($guide_id);
		unset($guide_level);
		unset($guide_cp);
		unset($guide_cp_name);
		unset($guide_desc1);
		unset($guide_desc2);
	}

?>