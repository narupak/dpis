<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");	
	
	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	$cmd = " select 	EP_YEAR, PER_ID, EAF_ID
			   from 		EAF_PERSONAL 
			   where 	EP_ID = $EP_ID ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$EP_YEAR = $data[EP_YEAR];
	$PER_ID = $data[PER_ID];
	$EAF_ID = $data[EAF_ID];
			
	$cmd = " select PN_CODE, PER_NAME, PER_SURNAME, DEPARTMENT_ID from PER_PERSONAL where PER_ID=$PER_ID ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$PN_CODE = $data[PN_CODE];
	$PER_NAME = $data[PER_NAME];
	$PER_SURNAME = $data[PER_SURNAME];
	$DEPARTMENT_ID = $data[DEPARTMENT_ID];
			
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
	
	$cmd = " select EAF_NAME from EAF_MASTER where EAF_ID=$EAF_ID ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$EAF_NAME = $data[EAF_NAME];
?>