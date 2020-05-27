<?
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");	

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$UPDATE_DATE = date("Y-m-d H:i:s");
	
	if($SUM_ID){
		$cmd = " select		a.SUM_YEAR, a.SUM_DATE, a.ORG_ID, b.ORG_NAME, b.ORG_ID_REF, b.OL_CODE
						 from		PER_SUM a, PER_ORG b
						 where	a.ORG_ID=b.ORG_ID and a.SUM_ID=$SUM_ID
					   ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$SUM_YEAR = $data[SUM_YEAR];
		$SUM_DATE = substr($data[SUM_DATE], 0, 10);
		$arr_temp = explode("-", $SUM_DATE);
		$SUM_DATE = $arr_temp[2]."/".$arr_temp[1]."/".($arr_temp[0] + 543);

		if(trim($data[OL_CODE])=="03"){
			$ORG_ID = $data[ORG_ID];
			$ORG_NAME = $data[ORG_NAME];
			$DEPARTMENT_ID =$data[ORG_ID_REF];
		}elseif(trim($data[OL_CODE])=="02"){
			$ORG_ID = "";
			$ORG_NAME = "";
			$DEPARTMENT_ID = $data[ORG_ID];
			$DEPARTMENT_NAME = $data[ORG_NAME];
			$MINISTRY_ID =$data[ORG_ID_REF];
		} // end if
		
		if($DEPARTMENT_ID){
			$cmd = " select ORG_ID_REF, ORG_NAME from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$DEPARTMENT_NAME = $data[ORG_NAME];
			$MINISTRY_ID = $data[ORG_ID_REF];
		} // end if

		if($MINISTRY_ID){ 
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$MINISTRY_NAME = $data[ORG_NAME];
		} // end if
	} // end if	
?>