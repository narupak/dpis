<?
$db_list = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

function list_per_work_location($name, $val) {
	global $db_list;
	$cmd = "SELECT WL_CODE, WL_NAME FROM PER_WORK_LOCATION 
			 WHERE WL_ACTIVE = 1 ORDER BY WL_SEQ_NO, WL_NAME ";
	$db_list->send_cmd($cmd);
	echo "<select name=\"$name\" class=\"selectbox\">
		    <option value=''>== เลือกสถานที่ปฏิบัติงาน ==</option>";
	while ($data_list = $db_list->get_array()) {
		//$data_list = array_change_key_case($data_list, CASE_LOWER);
		$qm_arr[$data_list[WL_CODE]] = $data_list[WL_NAME];
		$sel = ($data_list[WL_CODE] == $val)? "selected" : "";
		echo "<option value='$data_list[WL_CODE]' $sel>$data_list[WL_NAME]</option>";
	}
	echo "</select>";
	return $val;
}


function list_per_work_cycle($name, $val) {
	global $db_list;
	$cmd = "SELECT WC_CODE, WC_NAME FROM PER_WORK_CYCLE 
			 WHERE WC_ACTIVE = 1 ORDER BY WC_SEQ_NO, WC_NAME ";
	$db_list->send_cmd($cmd);
	echo "<select name=\"$name\" class=\"selectbox\">
		    <option value=''>== เลือกรอบการมาปฏิบัติงาน ==</option>";
	while ($data_list = $db_list->get_array()) {
		//$data_list = array_change_key_case($data_list, CASE_LOWER);
		$qm_arr[$data_list[WC_CODE]] = $data_list[WC_NAME];
		$sel = ($data_list[WC_CODE] == $val)? "selected" : "";
		echo "<option value='$data_list[WC_CODE]' $sel>$data_list[WC_NAME]</option>";
	}
	echo "</select>";
	return $val;
}



?>