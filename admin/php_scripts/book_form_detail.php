<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");	
	include("php_scripts/load_per_control.php");	
	
	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	$cmd = " select 	PG_END_DATE, PG_CYCLE, PER_ID, DEPARTMENT_ID
			   		 from 		PER_PERFORMANCE_GOODNESS 
			   		 where 	PG_ID = $PG_ID ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$PG_END_DATE = substr($data[PG_END_DATE], 0, 10);
	$PG_YEAR = substr($PG_END_DATE, 0, 4) + 543;
	$PG_CYCLE = $data[PG_CYCLE];
	$PER_ID = $data[PER_ID];
	$DEPARTMENT_ID = $data[DEPARTMENT_ID];
			
	$cmd = " select PN_CODE, PER_NAME, PER_SURNAME from PER_PERSONAL where PER_ID=$PER_ID ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$PN_CODE = $data[PN_CODE];
	$PER_NAME = $data[PER_NAME];
	$PER_SURNAME = $data[PER_SURNAME];
			
	$cmd = " select PN_NAME from PER_PRENAME where PN_CODE='$PN_CODE' ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$PN_NAME = $data[PN_NAME];
			
	$PER_FULLNAME = $PN_NAME . $PER_NAME . " " . $PER_SURNAME;
	
	$cmd = " select ORG_ID_REF, ORG_NAME from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$DEPARTMENT_NAME = $data[ORG_NAME];
	$MINISTRY_ID = $data[ORG_ID_REF];

	$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$MINISTRY_NAME = $data[ORG_NAME];
?>