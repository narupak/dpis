<?	
	include("../php_scripts/connect_database.php");
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");
	include("php_scripts/load_per_control.php");

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$UPDATE_DATE = date("Y-m-d H:i:s");

	if($command=="GENERATE"){
		 $db_insert = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
		 
		$date_min = (substr($TIME_STAMP_START,6,4)-543)."-".substr($TIME_STAMP_START,3,2)."-".substr($TIME_STAMP_START,0,2);
        $date_max = (substr($TIME_STAMP_END,6,4)-543)."-".substr($TIME_STAMP_END,3,2)."-".substr($TIME_STAMP_END,0,2);
		
		 $cmd = " select ot.PER_ID,ot.OT_DATE
										 from  TA_PER_OT ot
										where  ot.AUDIT_FLAG = 0 AND (ot.OT_DATE between '$date_min' AND '$date_max' )";
					$count_page_data2 = $db_dpis2->send_cmd($cmd);
					if($count_page_data2){
						while($data2 = $db_dpis2->get_array()){

							$cmd = " update TA_PER_OT set 
										START_TIME='1630',END_TIME='2030'
										UPDATE_USER=$SESS_USERID,UPDATE_DATE = '$UPDATE_DATE'
										where PER_ID=".$data2[PER_ID]." AND OT_DATE='".$data2[OT_DATE]."'";
										//echo $cmd."<br>" ;
							//$db_insert->send_cmd($cmd);
							
							
							
						}
					}
						
		
   }
	
	
?>