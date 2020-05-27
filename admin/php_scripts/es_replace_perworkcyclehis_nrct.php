<?php
    include("php_scripts/session_start.php");


    
    $db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
    $db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
    if($command=="UPDATE"){
		
		$sql="update PER_WORK_CYCLEHIS set  WC_CODE='02' where WC_CODE!='02'";
		$db_dpis->send_cmd($sql);
        $command="";
    }
?>
