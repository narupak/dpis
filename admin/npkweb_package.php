<?	
	include("../php_scripts/connect_database.php");

	$cmd = " SELECT max(CONFIG_ID) as MAX_ID FROM SYSTEM_CONFIG ";
	$db->send_cmd($cmd);
	//$db->show_error();
	$data = $db->get_array();
	$MAX_ID = $data[MAX_ID] + 1;

	$CONFIG_VALUE = md5("PKG1".$SESS_DEPARTMENT_ID);
	$cmd = " INSERT INTO SYSTEM_CONFIG (CONFIG_ID, CONFIG_NAME, CONFIG_VALUE, CONFIG_REMARK)
					VALUES ($MAX_ID, 'PKG1', '$CONFIG_VALUE', 'Package 1') ";
	$db->send_cmd($cmd);
	//$db->show_error()   ;

	echo "ประมวลผลเรียบร้อยแล้ว";

?>