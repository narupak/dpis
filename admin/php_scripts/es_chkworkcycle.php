<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/session_start.php");
	include("../../php_scripts/function_share.php");
	include("../../php_scripts/load_per_control.php");			

    $db_dpis = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	 
	 $WC_CODE = $_GET['WC_CODE'];
	$CHKEND = $_GET['END_HH'].$_GET['END_II'];
	
	$cmd = " select WC_START from PER_WORK_CYCLE where WC_CODE='$WC_CODE' ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$WC_START = $data[WC_START];
	if($CHKEND<=$WC_START){
		$xx = 1; 
	}else{
		$xx = 0; 
	}
	
	echo $xx;
	 

?>