<?php
include("../../php_scripts/connect_database.php");
include("../php_scripts/function_share.php");
//$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
header("content-type: application/x-javascript; charset=tis-620");

	$per_id = $_GET['PER_ID'];
	
	$startdate = $_GET['TIME_START']; //dd/mm/yyyy
	$startdate_arr = explode('/', $startdate);
	$startdate = ($startdate_arr[2]-543).'-'.$startdate_arr[1].'-'.$startdate_arr[0];
	$chk_startdate = ($startdate_arr[2]-543).$startdate_arr[1].$startdate_arr[0];
	
	if(!empty($_GET['TIME_END'])){
		$enddate = $_GET['TIME_END']; //dd/mm/yyyy
		$enddate_arr = explode('/', $enddate);
		$enddate = ($enddate_arr[2]-543).'-'.$enddate_arr[1].'-'.$enddate_arr[0];
		$chk_enddate = ($enddate_arr[2]-543).$enddate_arr[1].$enddate_arr[0];
	}else{
		$enddate = (date("Y")+1)."-".date("m-d");
		$chk_enddate = (date("Y")+1).date("md");
		
	}
	
	$REC_ID = $_GET['REC_ID'];


	if($chk_startdate>$chk_enddate){
			echo 0;
	}else{
			$cmd = " select  PER_ID from TA_SET_EXCEPTPER 
										WHERE REC_ID!=$REC_ID 
										AND END_DATE IS NOT NULL 
										AND CANCEL_FLAG=1
										AND PER_ID=$per_id  
										AND 	(   (START_DATE  BETWEEN '$startdate' and '$enddate')
			or  (END_DATE BETWEEN '$startdate' and '$enddate') 
								or ( '$startdate'  BETWEEN START_DATE and END_DATE )
								or ( '$enddate'  BETWEEN START_DATE and END_DATE ) 
			)
												 ";
			$count_duplicate = $db_dpis->send_cmd($cmd);
			if($count_duplicate > 0){
				 echo 1;
			}else{
				 echo 0;
			}
		
	}

?>
