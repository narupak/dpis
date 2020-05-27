<?
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

	if($command == "UPDATE"){
		$cmd = " update system_config set config_value='$CH_ATTDB' where config_name='ATTDB' ";
		$db->send_cmd($cmd);
		
		$cmd = " update system_config set config_value='$ch_attdb_host' where config_name='attdb_host' ";
		$db->send_cmd($cmd);

		$cmd = " update system_config set config_value='$ch_attdb_name' where config_name='attdb_name' ";
		$db->send_cmd($cmd);

		$cmd = " update system_config set config_value='$ch_attdb_user' where config_name='attdb_user' ";
		$db->send_cmd($cmd);

		$cmd = " update system_config set config_value='$ch_attdb_pwd' where config_name='attdb_pwd' ";
		$db->send_cmd($cmd);
		
		$ATTDB = $CH_ATTDB;
		$attdb_host = $ch_attdb_host;
		$attdb_name = $ch_attdb_name;
		$attdb_user = $ch_attdb_user;
		$attdb_pwd = $ch_attdb_pwd;
	} // end if
?>