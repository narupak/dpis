<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include("../../php_scripts/connect_database.php");
include("../php_scripts/function_share.php");
$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
$db_dpis4 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

$LEVEL_NO  	= $_POST["LEVEL_NO"];
 $cmd="select LAYER_SALARY_MIN, LAYER_SALARY_MAX 
				from PER_LAYER 
				where LEVEL_NO = '$LEVEL_NO' and LAYER_SALARY_MIN is not null and LAYER_SALARY_MAX is not null and LAYER_ACTIVE = 1";
				$db_dpis3->send_cmd($cmd);
				$data = $db_dpis3->get_array();
				 echo json_encode($data);

?>
