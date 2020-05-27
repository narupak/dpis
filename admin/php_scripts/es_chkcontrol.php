<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/session_start.php");
	include("../../php_scripts/function_share.php");
	include("../../php_scripts/load_per_control.php");			

    $db_dpis = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	 
	$Save_search_year = $_GET['search_year'];
	$Save_search_month = $_GET['search_month'];
	$Save_search_org_id = $_GET['search_org_id'];
	
	$cmd1 = " select count(CONTROL_ID) AS CNT from PER_WORK_TIME_CONTROL 
		                  where CLOSE_YEAR=$Save_search_year 
						  and CLOSE_MONTH=$Save_search_month
						  and DEPARTMENT_ID=$Save_search_org_id and APPROVE_DATE IS NOT NULL ";
	$db_dpis->send_cmd($cmd1);
	$data = $db_dpis->get_array();
	if($data[CNT]>0){
		$STR="APPROVE";
	}else{
		
		$cmd2 = " select count(CONTROL_ID) AS CNT from PER_WORK_TIME_CONTROL 
		                  where CLOSE_YEAR=$Save_search_year 
						  and CLOSE_MONTH=$Save_search_month
						  and DEPARTMENT_ID=$Save_search_org_id and CLOSE_DATE IS NOT NULL ";
		$db_dpis1->send_cmd($cmd2);
		$data2 = $db_dpis1->get_array();
		if($data2[CNT]>0){
			$STR="CLOSE";
		}else{
			
			$cmd3 = " select count(CONTROL_ID) AS CNT from PER_WORK_TIME_CONTROL 
		                  where CLOSE_YEAR=$Save_search_year 
						  and CLOSE_MONTH=$Save_search_month
						  and DEPARTMENT_ID=$Save_search_org_id and PROCESS_DATE IS NOT NULL ";
			$db_dpis2->send_cmd($cmd3);
			$data3 = $db_dpis2->get_array();
			if($data3[CNT]>0){
				$STR="PROCESS";
			}else{
				$STR="NEW";
			}
			
		}
		
	}
	
	echo $STR;
	 

?>