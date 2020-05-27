<?php
include("../../php_scripts/connect_database.php");
include("../php_scripts/function_share.php");
$type = $_GET['type'];
$kf_id = $_GET['kf_id'];
$ACCEPT_DATE = date("Y-m-d H:i:s");
if($type=="-1"){$type="NULL";}
$cmd = " UPDATE PER_KPI_FORM SET ACCEPT_FLAG=".$type.", ACCEPT_DATE ="."'".$ACCEPT_DATE."'"." WHERE KF_ID=".$kf_id;
$db_dpis->send_cmd($cmd);
?>
