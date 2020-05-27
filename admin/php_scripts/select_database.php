<?
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

	if($command == "UPDATE"){
		$cmd = " update system_config set config_value='$CH_DPISDB' where config_name='DPISDB' ";
		$db->send_cmd($cmd);
		
		$cmd = " update system_config set config_value='$ch_dpisdb_host' where config_name='dpisdb_host' ";
		$db->send_cmd($cmd);

		$cmd = " update system_config set config_value='$ch_dpisdb_name' where config_name='dpisdb_name' ";
		$db->send_cmd($cmd);

		$cmd = " update system_config set config_value='$ch_dpisdb_user' where config_name='dpisdb_user' ";
		$db->send_cmd($cmd);

		$cmd = " update system_config set config_value='$ch_dpisdb_pwd' where config_name='dpisdb_pwd' ";
		$db->send_cmd($cmd);
		
		$DPISDB = $CH_DPISDB;
		$dpisdb_host = $ch_dpisdb_host;
		$dpisdb_name = $ch_dpisdb_name;
		$dpisdb_user = $ch_dpisdb_user;
		$dpisdb_pwd = $ch_dpisdb_pwd;
	} // end if
?>