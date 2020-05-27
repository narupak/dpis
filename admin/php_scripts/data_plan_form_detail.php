<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");	
	
	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	$cmd = " select PROJ_NAME, TPJ_BUDGET_YEAR from PER_TRAIN_PROJECT where PROJ_ID=$PROJ_ID ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$PROJ_NAME = $data[PROJ_NAME];
	$TPJ_BUDGET_YEAR = $data[TPJ_BUDGET_YEAR];

?>