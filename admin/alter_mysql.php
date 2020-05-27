<?	
	include("../php_scripts/connect_database.php");

	$cmd = " ALTER TABLE USER_GROUP ADD group_level INT ";
	$db->send_cmd($cmd);
	//$db->show_error()   ;

	$cmd = " ALTER TABLE USER_GROUP ADD org_id INT ";
	$db->send_cmd($cmd);
	//$db->show_error()   ;

	$cmd = " ALTER TABLE USER_GROUP ADD pv_code VARCHAR(10) ";
	$db->send_cmd($cmd);
	//$db->show_error()   ;

	echo "ประมวลผลเรียบร้อยแล้ว";

?>