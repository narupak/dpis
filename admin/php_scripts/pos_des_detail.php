<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");

	$cmd = " select		b.PL_NAME, c.LEVEL_NAME
					 from		POS_DES_INFO a, PER_LINE b, PER_LEVEL c
					 where	a.POS_DES_ID=$POS_DES_ID and trim(a.PL_CODE)=trim(b.PL_CODE) and a.LEVEL_NO=c.LEVEL_NO ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$PL_NAME = $data[PL_NAME];
	$LEVEL_NAME = $data[LEVEL_NAME];
?>                                  