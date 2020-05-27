<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include("../../php_scripts/connect_database.php");
include("../php_scripts/function_share.php");
$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
$db_dpis4 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

$WC_CODE  	= $_POST["WC_CODE"];
 $cmd="select  trim(WC_START)as WC_START,trim(WC_END) as WC_END from PER_WORK_CYCLE where WC_CODE = $WC_CODE ";
				$db_dpis3->send_cmd($cmd);
				$data = $db_dpis3->get_array();
				 echo json_encode($data);

?>
