<?
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");	
	include("php_scripts/function_list.php");		

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$UPDATE_DATE = date("Y-m-d H:i:s");
	
	$cmd = "	select 		SALQ_YEAR, SALQ_TYPE from PER_SALQUOTA  
					order by	SALQ_YEAR desc, SALQ_TYPE desc";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$SALQ_YEAR = $data[SALQ_YEAR];
	$SALQ_TYPE = $data[SALQ_TYPE];
	
	if ($SALQ_TYPE == 1) {
		$SALQ_TYPE1 = 1;
		$SALQ_TYPE2 = 1;
	} elseif ($SALQ_TYPE == 2) {
		$SALQ_TYPE1 = 1;
		$SALQ_TYPE2 = 2;
	} elseif ($SALQ_TYPE == 3) {
		$SALQ_TYPE1 = 2;
		$SALQ_TYPE2 = 1;	
	} elseif ($SALQ_TYPE == 4) {
		$SALQ_TYPE1 = 2;
		$SALQ_TYPE2 = 2;	
	}			
	
	
?>