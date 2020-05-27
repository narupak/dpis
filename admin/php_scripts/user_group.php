<?
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");

	$db1 = new connect_db($db_host, $db_name, $db_user, $db_pwd);
//	echo "$SESS_USERGROUP_LEVEL :: $SESS_PROVINCE_CODE :: $SESS_MINISTRY_ID :: $SESS_DEPARTMENT_ID";
	
	$group_name_th = trim($group_name_th);
	$group_name_en = trim($group_name_en);
	$code = trim($code);
	if ($name_en == "") $group_name_en = $group_name_th;

	if($db_type=="mysql") {
		$update_date = "NOW()";
		$update_by = "'$SESS_USERNAME'";
	} elseif($db_type=="mssql") {
		$update_date = "GETDATE()";
		$update_by = $SESS_USERID;
	} elseif($db_type=="oci8" || $db_type=="odbc") {
		$update_date = date("Y-m-d H:i:s");
		$update_date = "'$update_date'";
		$update_by = $SESS_USERID;
	}
  	$sort_by = (isset($sort_by))?  $sort_by : 1;
	$sort_type = (isset($sort_type))?  $sort_type : "1:asc";
	$arrSort=explode(":",$sort_type);
	$SortType[$arrSort[0]]	=$arrSort[1];
  	if(!$order_by)	$order_by=1;
  	if(trim($search_code)) $arr_search_condition[] = "(CODE like '$search_code%')";
  	if(trim($search_name)) $arr_search_condition[] = "(NAME_TH like '%$search_name%' or NAME_EN like '%$search_name%')";
  	if(trim($search_username)) $arr_search_condition[] = "(USERNAME like '%$search_username%' or FULLNAME like '%$search_username%')";
        
       
        if(trim($selectFlag)){
            if(trim($selectFlag)!='1'){
                $arr_search_condition[] = "(USER_FLAG='".$selectFlag."' )";
            }          
        }
        
        if($CmdGroupLevel!=0){
            $arr_search_condition[] = " (group_level=$CmdGroupLevel) ";
        }

	$search_condition = "";
	if(count($arr_search_condition)) 
		if($db_type=="oci8") 
			$search_condition = " and " . implode(" and ", $arr_search_condition);
		elseif($db_type=="mysql")
			$search_condition = " where " . implode(" and ", $arr_search_condition);
	
	if($order_by==1){	//(ค่าเริ่มต้น) ลำดับที่
		$order_str = "group_seq_no $SortType[$order_by], code $SortType[$order_by]";
  	}elseif($order_by==2) {	//รหัส
		$order_str = "code ".$SortType[$order_by];
  	}elseif($order_by==3) {	//ชื่อ
		$order_str = "name_th ".$SortType[$order_by];
  	}elseif($order_by==4) {	//โครงสร้าง
		$order_str = "group_org_structure ".$SortType[$order_by];
	}

	if($command=="REORDER"){
		foreach($ARR_ORDER as $group_id => $group_seq_no){
		if($group_seq_no=="") { $cmd = " update user_group set group_seq_no='' where id='$group_id' "; }
		else { $cmd = " update user_group set group_seq_no=$group_seq_no where id='$group_id' ";  }
			$db->send_cmd($cmd);
		//	$db->show_error();
		} // end foreach

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > จัดลำดับคำนำหน้าชื่อ [$PN_CODE : $PN_NAME]");
	} // end if

	if ($command == "SETFLAG") {
		$setflagshow =  implode(",",$list_show_id);
		$cmd = " update user_group set group_active = 0 where id in (".stripslashes($current_list).") ";
		$db->send_cmd($cmd);
		//$db->show_error();
		//echo "1 - > ".$cmd;

		$cmd = " update user_group set group_active = 1 where id in (".stripslashes($setflagshow).") ";
		$db->send_cmd($cmd);
		//$db->show_error();
		//echo "<br>2 - > ".$cmd;
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการใช้งานข้อมูล");
	}

	if ($command == "ADD" && $group_name_th != "") {
		if (!$group_seq_no) $group_seq_no = "NULL";
		$cmd = " select max(id) as max_id from user_group ";
		$db->send_cmd($cmd);
		$data = $db->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$max_id = $data[max_id] + 1;
		$cmd = "insert into user_group (id, code, name_th, name_en, access_list, group_seq_no, create_date, create_by, update_date, update_by) 
						values ($max_id, '$code', '$group_name_th', '$group_name_en', ',1,' , $group_seq_no, $update_date, $update_by, $update_date, $update_by)";
		$db->send_cmd($cmd);
//		$db->show_error();
		$CLEAR = 1;
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มกลุ่มผู้ใช้งาน [$group_name_th]");
	}

	if ($command == "UPDATE" && $group_name_th != "") {
		if (!$group_seq_no) $group_seq_no = "NULL";
		$update_code = "";
		if($code){ $update_code = " code = '$code', "; }
		$cmd = "update user_group set
				$update_code
				name_th = '$group_name_th',
				name_en = '$group_name_en',
				group_seq_no = $group_seq_no,
				update_date = $update_date,
				update_by = $update_by
			where id = $group_id";
		$db->send_cmd($cmd);
//		$db->show_error();
		$CLEAR = 1;
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขกลุ่มผู้ใช้งาน [$group_name_th]");
	}

	if ($command == "DELETE" && $group_id) {
		$cmd = " select name_th from user_group where id=$group_id ";
		$db->send_cmd($cmd);
		$data = $db->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$group_name_th = $data[name_th];
		
                //$cmd = " delete from user_detail where group_id = $group_id "; //เดิม
                $cmd = " update user_detail set user_flag='N' where id = $group_id "; //Release 5.2.1.21
		$db->send_cmd($cmd);
		
		$cmd = " delete from user_privilege where group_id = $group_id ";
		$db->send_cmd($cmd);
		
		$cmd = " delete from user_group where id = $group_id ";
		$db->send_cmd($cmd);
		
		$CLEAR = 1;
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบกลุ่มผู้ใช้งาน [$group_name_th]");
	}

	if( $command=="COPY" && $group_id_list ){
		$cmd = " delete from user_privilege where group_id = $group_id ";
		$db->send_cmd($cmd);

		$cmd = " select page_id, menu_id_lv0, menu_id_lv1, menu_id_lv2, menu_id_lv3, can_add, can_edit, can_del, can_inq, can_print, can_confirm 
						  from user_privilege 
						  where group_id=$group_id_list ";
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
			
			$cmd = " insert into user_privilege (group_id, page_id, menu_id_lv0, menu_id_lv1, menu_id_lv2, menu_id_lv3, 
							  can_add, can_edit, can_del, can_inq, can_print, can_confirm)
							  values ($group_id, $page_id, $menu_id_lv0, $menu_id_lv1, $menu_id_lv2, $menu_id_lv3, 
							  '$can_add', '$can_edit', '$can_del', '$can_inq', '$can_print', '$can_confirm') ";
			$db1->send_cmd($cmd);
		}

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > คัดลอกสิทธิ์เมนูกลุ่มผู้ใช้งาน [$group_name_th]");
	}
		
	$cmd = "select id, name_th, url from user_section";
	$count_access_section = $db->send_cmd($cmd);
	if($count_access_section) :
		$i = 0;
		while($data = $db->get_array()) :
			$data = array_change_key_case($data, CASE_LOWER);
			$access_section[$i]["id"] = $data[id];
			$access_section[$i]["name"] = $data[name_th];
			$i++;
		endwhile;
	endif;

	if($db_type=="oci8") 
		$cmd = " select distinct a.id,code,name_th,name_en,access_list, group_level, org_id, pv_code,group_per_type, group_org_structure, group_active, group_seq_no 
						  from user_group a, user_detail b
						  where a.id = b.group_id(+)
						  $search_condition
						  order by $order_str";
	elseif($db_type=="mysql")
		$cmd = " select distinct a.id,code,name_th,name_en,access_list, group_level, org_id, pv_code,group_per_type, group_org_structure, group_active, group_seq_no 
						  from (
										user_group a
									) 	left join user_detail b on (a.id = b.group_id)
						  $search_condition
						  order by $order_str";
	//echo $cmd;
        $count_user_group = $db->send_cmd($cmd);
	if($count_user_group) :
		$i = 0;
		while($data = $db->get_array()) :
			$data = array_change_key_case($data, CASE_LOWER);
			$user_group[$i]["id"] = $data[id];
			$user_group[$i]["code"] = $data[code];
			$user_group[$i]["name_th"] = $data[name_th];
			$user_group[$i]["name_en"] = $data[name_en];
			$user_group[$i]["access_list"] = $data[access_list];
			$user_group[$i]["group_org_structure"] = $data[group_org_structure];
			$user_group[$i]["group_active"] = $data[group_active];
			$user_group[$i]["group_seq_no"] = $data[group_seq_no];

			$group_level = $data[group_level];
			if(!$group_level) $group_level = 4;
			$group_per_type = $data[group_per_type];
			$group_org_structure = $data[group_org_structure];
			$group_pv_code = $data[pv_code];
			$group_org_id = $data[org_id];

			$TMP_PV_CODE = $TMP_MINISTRY_ID = $TMP_DEPARTMENT_ID = $TMP_ORG_ID = $TMP_ORG_ID_1 = "";
			switch($group_level){
				case 2 :
					$TMP_PV_CODE = $group_pv_code;
					break;
				case 3 :
					$TMP_MINISTRY_ID = $group_org_id;
					break;
				case 4 :
					$TMP_DEPARTMENT_ID = $group_org_id;
					break;
				case 5 :
					$TMP_ORG_ID = $group_org_id;
					break;
				case 6 :
					$TMP_ORG_ID_1 = $group_org_id;
					break;
			} // end switch case

			if($TMP_ORG_ID_1){
				$cmd = " select ORG_NAME, ORG_SHORT, ORG_ID_REF from PER_ORG where ORG_ID=$TMP_ORG_ID_1 ";
				if($group_org_structure==1){	$cmd = str_replace("PER_ORG","PER_ORG_ASS",$cmd);	}
				$db_dpis->send_cmd($cmd);
		//		$db_dpis->show_error();
				$data_dpis = $db_dpis->get_array();
				if ($data_dpis[ORG_SHORT])	$user_group[$i]["name_org"] = $data_dpis[ORG_SHORT]."<br>&nbsp;";
				else $user_group[$i]["name_org"] = $data_dpis[ORG_NAME]."<br>&nbsp;";
				$TMP_ORG_ID = $data_dpis[ORG_ID_REF];	
			} // end if
		
			if($TMP_ORG_ID){
				$cmd = " select ORG_NAME, ORG_SHORT, ORG_ID_REF from PER_ORG where ORG_ID=$TMP_ORG_ID ";
				if($group_org_structure==1){	$cmd = str_replace("PER_ORG","PER_ORG_ASS",$cmd);	}
				$db_dpis->send_cmd($cmd);
		//		$db_dpis->show_error();
				$data_dpis = $db_dpis->get_array();
				if ($data_dpis[ORG_NAME] != "-")	{
					if ($data_dpis[ORG_SHORT])	$user_group[$i]["name_org"] .= $data_dpis[ORG_SHORT]."<br>&nbsp;";
					else $user_group[$i]["name_org"] .= $data_dpis[ORG_NAME]."<br>&nbsp;";
				}
				$TMP_DEPARTMENT_ID = $data_dpis[ORG_ID_REF];	
			} // end if
		
			if($TMP_DEPARTMENT_ID){	//***DEPARTMENT ไมใช้ตาราง PER_ORG_ASS
				$cmd = " select ORG_NAME, ORG_SHORT, ORG_ID_REF from PER_ORG where ORG_ID=$TMP_DEPARTMENT_ID ";
				$db_dpis->send_cmd($cmd);
		//		$db_dpis->show_error();
				$data_dpis = $db_dpis->get_array();
				if ($data_dpis[ORG_SHORT])	$user_group[$i]["name_org"] .= $data_dpis[ORG_SHORT]."<br>";
				else $user_group[$i]["name_org"] .= $data_dpis[ORG_NAME]."<br>";
		//		$TMP_MINISTRY_ID = $data_dpis[ORG_ID_REF];	
			} // end if
		
			if($TMP_MINISTRY_ID){	//***MINISTRY ไมใช้ตาราง PER_ORG_ASS
				$cmd = " select ORG_NAME, ORG_SHORT from PER_ORG where ORG_ID=$TMP_MINISTRY_ID ";
				$db_dpis->send_cmd($cmd);
		//		$db_dpis->show_error();
				$data_dpis = $db_dpis->get_array();
				if ($data_dpis[ORG_SHORT])	$user_group[$i]["name_org"] .= $data_dpis[ORG_SHORT];
				else $user_group[$i]["name_org"] .= $data_dpis[ORG_NAME];
			} // end if
		
			if($TMP_PV_CODE){
				$cmd = " select PV_NAME from PER_PROVINCE where PV_CODE='$TMP_PV_CODE' ";
				$db_dpis->send_cmd($cmd);
		//		$db_dpis->show_error();
				$data_dpis = $db_dpis->get_array();
				$user_group[$i]["name_org"] = $data_dpis[PV_NAME];
			} // end if
			$i++;
		endwhile;
	endif;

	if ($command == "SETACCESS") {
		for($i=0; $i<$count_user_group; $i++) :
			for($j=0; $j<$count_access_section; $j++) :
				$arr_section = "list_section_id_".$access_section[$j]["id"];
				if( in_array($user_group[$i]["id"], $$arr_section) ) :
					if($section_list[$user_group[$i]["id"]]) $section_list[$user_group[$i]["id"]] .= ",";
					$section_list[$user_group[$i]["id"]] .= $access_section[$j]["id"];
				endif;
			endfor;
			$cmd = "update user_group set access_list=',".$section_list[$user_group[$i]["id"]].",' where id=".$user_group[$i]["id"];
			$db->send_cmd($cmd);
//			$db->show_error();
		endfor;
//		print_r($section_list);		

		$cmd = "select id,code,name_th,name_en,access_list, group_org_structure, group_active, group_seq_no from user_group order by $order_str";
		$count_user_group = $db->send_cmd($cmd);
//		echo $cmd;
		if($count_user_group) :
			$i = 0;
			while($data = $db->get_array()) :
				$data = array_change_key_case($data, CASE_LOWER);
				$user_group[$i]["id"] = $data[id];
				$user_group[$i]["code"] = $data[code];
				$user_group[$i]["name_th"] = $data[name_th];
				$user_group[$i]["name_en"] = $data[name_en];
				$user_group[$i]["access_list"] = $data[access_list];
				$user_group[$i]["group_org_structure"] = $data[group_org_structure];
				$user_group[$i]["group_active"] = $data[group_active];
				$user_group[$i]["group_seq_no"] = $data[group_seq_no];
				$i++;
			endwhile;
		endif;

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าระดับการเข้าใช้งาน");
	}

	if ($UPD || $DEL) {
		$cmd = " select code,name_th,name_en,group_seq_no, update_by, update_date from user_group where id = $group_id";
		$db->send_cmd($cmd);
		while ($data = $db->get_array()) {
			$data = array_change_key_case($data, CASE_LOWER);
			$code = $data[code];
			$group_name_th = $data[name_th];
			$group_name_en = $data[name_en];
			$group_seq_no = $data[group_seq_no];
			$update_user = $data[update_by];
			$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $update_user ";
			$db->send_cmd($cmd);
			$data2 = $db->get_array();
			$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
			$SHOW_UPDATE_DATE = show_date_format(trim($data[update_date]), $DATE_DISPLAY);
		} // while
	} // if

	if ( !$UPD && !$DEL) {
		$code = "";
		unset($group_id);
		unset($group_name_th);
		unset($group_name_en);
		unset($icon_name);
		unset($group_seq_no);
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
	}

?>