<?
$db_list = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

function list_country ($name, $val) {
	global $db_list;
	$cmd = "SELECT ct_code, ct_name FROM PER_COUNTRY 
			 WHERE ct_active = 1 ORDER BY ct_seq_no, ct_name";
	$db_list->send_cmd($cmd);
	echo "<select name=\"$name\" class=\"selectbox\">
		    <option value=''>== เลือกประเทศ ==</option>";
	while ($data_list = $db_list->get_array()) {
		$data_list = array_change_key_case($data_list, CASE_LOWER);
		$qm_arr[$data_list[ct_code]] = $data_list[ct_name];
		$sel = ($data_list[ct_code] == $val)? "selected" : "";
		echo "<option value='$data_list[ct_code]' $sel>$data_list[ct_name]</option>";
	}
	echo "</select>";
	return $val;
}

function list_province ($name, $val) {
	global $db_list;
	$cmd = "SELECT pv_code, pv_name FROM PER_PROVINCE
			 WHERE pv_active = 1
			 ORDER BY pv_seq_no, pv_name";
	$db_list->send_cmd($cmd);
	echo "<select name=\"$name\" class=\"selectbox\">
		    <option value=''>== เลือกจังหวัด ==</option>";
	while ($data_list = $db_list->get_array()) {
		$data_list = array_change_key_case($data_list, CASE_LOWER);
		$qm_arr[$data_list[pv_code]] = $data_list[pv_name];
		$sel = ($data_list[pv_code] == $val)? "selected" : "";
		echo "<option value='$data_list[pv_code]' $sel>$data_list[pv_name]</option>";
	}
	echo "</select>";
	return $val;
}

function list_per_status ($name, $val) {
	global $db_list;
	$cmd = "SELECT ps_code, ps_name FROM PER_STATUS
			 		WHERE ps_active = 1 
			 		ORDER BY ps_seq_no, ps_name";
	$db_list->send_cmd($cmd);
	echo "<select name=\"$name\" class=\"selectbox\">
		    		<option value=''>== เลือกฐานะของตำแหน่ง ==</option>";
	while ($data_list = $db_list->get_array()) {
		$data_list = array_change_key_case($data_list, CASE_LOWER);
		$qm_arr[$data_list[ps_code]] = $data_list[ps_name];
		$sel = ("'".trim($data_list[ps_code])."'" == "'".trim($val)."'")? "selected" : "";
//		$show = "(".$data_list[ps_code]." == ".$val.") [".(trim($data_list[ps_code]) == trim($val))."]";
//		echo "<option value='$data_list[ps_code]' $sel>$data_list[ps_name](".$show.")</option>";
		echo "<option value='$data_list[ps_code]' $sel>$data_list[ps_name]</option>";
	}
	echo "</select>";
	return $val;
}

function list_educlevel ($name, $val) {
	global $db_list;
	$cmd = "SELECT el_code, el_name FROM PER_EDUCLEVEL
			 WHERE el_active = 1 
			 ORDER BY el_seq_no, el_name";
	$db_list->send_cmd($cmd);
	echo "<select name=\"$name\" class=\"selectbox\">
		    <option value=''>== เลือกระดับการศึกษา ==</option>";
	while ($data_list = $db_list->get_array()) {
		$data_list = array_change_key_case($data_list, CASE_LOWER);
		$qm_arr[$data_list[el_code]] = $data_list[el_name];
		$sel = ($data_list[el_code] == $val)? "selected" : "";
		echo "<option value='$data_list[el_code]' $sel>$data_list[el_name]</option>";
	}
	echo "</select>";
	return $val;
}

function list_per_pos_group ($name, $val, $pg_name,$submit_flg) {
	global $db_list;
	if (!$pg_name) $pg_name = "pg_name";
	$cmd = " SELECT pg_code, $pg_name FROM PER_POS_GROUP 
			 WHERE pg_active = 1 
			 ORDER BY pg_seq_no, $pg_name ";
	$db_list->send_cmd($cmd);

	if ($pg_name){
		echo "<select name=\"$name\" class=\"selectbox\" "; if($submit_flg==1){	echo "onChange=\"form1.submit();\"";  } echo "><option value=''>== เลือกกลุ่มบัญชีค่าจ้าง ==</option>";
	}else{
		echo "<select name=\"$name\" class=\"selectbox\">
			    <option value=''>== เลือกหมวดตำแหน่งลูกจ้าง ==</option>";
	}
	while ($data_list = $db_list->get_array()) {
		$data_list = array_change_key_case($data_list, CASE_LOWER);
		$tmp_dat = trim($data_list[pg_code]);
		$qm_arr[$tmp_dat] = $data_list[$pg_name];
		echo $tmp_dat .'=='. $val;
		$sel = ($tmp_dat == trim($val))? "selected" : "";
		echo "<option value='$tmp_dat' $sel>$data_list[$pg_name]</option>";
	}
	echo "</select>";
	return $val;
}

function list_per_level ($name, $val, $type) {
	global $db_list, $DPISDB;
	$where = "";
	if ($type) $where = "and PER_TYPE = $type";
	$cmd = "	select LEVEL_NO,LEVEL_NAME FROM PER_LEVEL
				 		where LEVEL_ACTIVE = 1  $where
				 		order by PER_TYPE, LEVEL_SEQ_NO";
	$db_list->send_cmd($cmd);
	//$db_list->show_error();	
	echo "<select name=\"$name\" class=\"selectbox\">
		    <option value=''>== เลือกระดับตำแหน่ง ==</option>";
	while ($data_list = $db_list->get_array()) {
		//$data_list = array_change_key_case($data_list, CASE_LOWER);
		$tmp_dat = trim($data_list[LEVEL_NO]);
		$levelname= trim($data_list[LEVEL_NAME]);
		$qm_arr[$tmp_dat] = $tmp_dat;
		$sel = (($tmp_dat) == trim($val))? "selected" : "";
		echo "<option value='$tmp_dat' $sel>". $levelname ."</option>";
	}
	echo "</select>";
	return $val;
//echo "<pre>";		
//print_r($data_list);
//echo "</pre>";	
}

function list_per_type ($name, $val) {
	global $db_list;
	$cmd = "SELECT pt_code, pt_name FROM PER_TYPE
			 WHERE pt_active = 1 
			 ORDER BY pt_seq_no, pt_name";
	$db_list->send_cmd($cmd);
	echo "<select name=\"$name\" class=\"selectbox\">
		    <option value=''>== เลือกประเภทตำแหน่ง ==</option>";
	while ($data_list = $db_list->get_array()) {
		$data_list = array_change_key_case($data_list, CASE_LOWER);
		$tmp_dat = trim($data_list[pt_code]);	
		$qm_arr[$tmp_dat] = $data_list[pt_name];
		$sel = ($tmp_dat == $val)? "selected" : "";
		echo "<option value='$tmp_dat' $sel>$data_list[pt_name]</option>";
	}
	echo "</select>";
	return $val;
}

function list_per_extratype ($name, $val) {
	global $db_list;
	$cmd = "SELECT ex_code, ex_name FROM PER_EXTRATYPE
			 WHERE ex_active = 1 
			 ORDER BY ex_seq_no, ex_name";
	$db_list->send_cmd($cmd);
	echo "<select name=\"$name\" class=\"selectbox\">
		    <option value=''>== เลือกเงินเพิ่มพิเศษ ==</option>";
	while ($data_list = $db_list->get_array()) {
		$data_list = array_change_key_case($data_list, CASE_LOWER);
		$tmp_dat = trim($data_list[ex_code]);	
		$qm_arr[$tmp_dat] = $data_list[ex_name];
		$sel = ($tmp_dat == $val)? "selected" : "";
		echo "<option value='$tmp_dat' $sel>$data_list[ex_name]</option>";
	}
	echo "</select>";
	return $val;
}

function list_per_layer_salary ($name, $val, $dpisdb) {
	global $db_list;
	if ($dpisdb == "odbc")		$str_salary = "format(LAYER_SALARY, '###,###.00')";
	elseif ($dpisdb == "oci8")	$str_salary = "to_char(LAYER_SALARY, '999,999.00')";
	elseif($DPISDB=="mysql") $str_salary = "format(LAYER_SALARY, '###,###.00')";
	$cmd = "SELECT layer_type, level_no, layer_no, layer_salary, $str_salary as STR_SALARY FROM PER_LAYER 
			 WHERE layer_active = 1 and layer_no IS NOT NULL and layer_no <> 0 
			 ORDER BY layer_salary";
	$db_list->send_cmd($cmd);
	$db_list->show_error();
	echo "<select name=\"$name\" class=\"selectbox\">
		    <option value=''>== เลือกอัตราเงินเดือน ==</option>";
	while ($data_list = $db_list->get_array()) {
		$tmp_dat = trim($data_list[LAYER_SALARY]);	
		$sel = ($tmp_dat == $val)? "selected" : "";
		echo "<option value='$tmp_dat' $sel>$data_list[STR_SALARY]</option>";
	}
	echo "</select>";
	return $val;
}

function list_blood_group ($name, $val) {
	global $db_list;
	$cmd = " select BL_CODE, BL_NAME from PER_BLOOD where BL_ACTIVE=1 order by BL_SEQ_NO, BL_CODE ";
	$db_list->send_cmd($cmd);
	echo "<select name=\"$name\" class=\"selectbox\">
		    <option value=''>== เลือกหมู่โลหิต ==</option>";
	while ($data_list = $db_list->get_array()) {
		$tmp_dat = trim($data_list[BL_CODE]);	
		$sel = ($tmp_dat == $val)? "selected" : "";
		echo "<option value='$tmp_dat' $sel>$data_list[BL_CODE]</option>";
	}
	echo "</select>";
	return $val;
}

function list_worklocation ($name, $val) {
	global $db_list;
	$cmd = "SELECT wl_code, wl_name FROM PER_WORK_LOCATION
			 WHERE wl_active = 1 
			 ORDER BY wl_seq_no, wl_name";
	$db_list->send_cmd($cmd);
	echo "<select name=\"$name\" class=\"selectbox\">
		    <option value=''>== เลือกสถานที่ปฏิบัติราชการ ==</option>";
	while ($data_list = $db_list->get_array()) {
		$data_list = array_change_key_case($data_list, CASE_LOWER);
		$qm_arr[$data_list[el_code]] = $data_list[el_name];
		$sel = ($data_list[el_code] == $val)? "selected" : "";
		echo "<option value='$data_list[wl_code]' $sel>$data_list[wl_name]</option>";
	}
	echo "</select>";
	return $val;
}

function list_usergroup ($name, $val) {
	global $db;
	$cmd = "SELECT id, name_th FROM USER_GROUP
			 ORDER BY name_th";
	$db->send_cmd($cmd);
	echo "<select name=\"$name\" class=\"selectbox\">
		    <option value=''>== เลือกกลุ่มผู้ใช้งาน ==</option>";
	while ($data_list = $db->get_array()) {
		$data_list = array_change_key_case($data_list, CASE_LOWER);
		$qm_arr[$data_list[id]] = $data_list[name_th];
		$sel = ($data_list[id] == $val)? "selected" : "";
		echo "<option value='$data_list[id]' $sel>$data_list[name_th]</option>";
	}
	echo "</select>";
	return $val;
}

function list_per_comgroup ($name, $val) {
	global $db_list;
	$cmd = "SELECT com_group, cg_name FROM PER_COMGROUP
			 WHERE cg_active = 1 
			 ORDER BY cg_seq_no, com_group";
	$db_list->send_cmd($cmd);
	echo "<select name=\"$name\" class=\"selectbox\">
		    <option value=''>== เลือกแบบคำสั่ง ==</option>";
	while ($data_list = $db_list->get_array()) {
		$data_list = array_change_key_case($data_list, CASE_LOWER);
		$qm_arr[$data_list[com_group]] = $data_list[cg_name];
		$sel = ($data_list[com_group] == $val)? "selected" : "";
		echo "<option value='$data_list[com_group]' $sel>$data_list[cg_name]</option>";
	}
	echo "</select>";
	return $val;
}

function list_per_temp_pos_group ($name, $val) {
	global $db_list;
	$cmd = " SELECT tg_code, tg_name FROM PER_TEMP_POS_GROUP 
			 WHERE tg_active = 1 
			 ORDER BY tg_seq_no, tg_name ";
	$db_list->send_cmd($cmd);
	echo "<select name=\"$name\" class=\"selectbox\">
			    <option value=''>== เลือกหมวดตำแหน่งลูกจ้าง ==</option>";
	while ($data_list = $db_list->get_array()) {
		$data_list = array_change_key_case($data_list, CASE_LOWER);
		$tmp_dat = trim($data_list[tg_code]);
		$qm_arr[$tmp_dat] = $data_list[tg_name];
		$sel = ($tmp_dat == trim($val))? "selected" : "";
		echo "<option value='$tmp_dat' $sel>$data_list[tg_name]</option>";
	}
	echo "</select>";
	return $val;
}

?>