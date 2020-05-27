<? 
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd, $dpisdb_port);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd, $dpisdb_port);
	
	$UPDATE_DATE = date("Y-m-d H:i:s");
	if ($command == "ADD") {
		$date_min = (substr($search_date_min,6,4)-543)."-".substr($search_date_min,3,2)."-".substr($search_date_min,0,2);
        $date_max = (substr($search_date_max,6,4)-543)."-".substr($search_date_max,3,2)."-".substr($search_date_max,0,2);
        
		
			/*ºÑ¹·Ö¡ÊÃØ»*/
		$save_year=$search_year;
		$save_month=$search_month;
		$PAY_DATE =NULL;
		if($search_date){
			$PAY_DATE = (substr($search_date,6,4)-543)."-".substr($search_date,3,2)."-".substr($search_date,0,2);
		}
		

		$date_min = (substr($search_date_min,6,4)-543)."-".substr($search_date_min,3,2)."-".substr($search_date_min,0,2);
        $date_max = (substr($search_date_max,6,4)-543)."-".substr($search_date_max,3,2)."-".substr($search_date_max,0,2);
        $save_ALLOW_PER_ID="NULL";
		if($ALLOW_PER_ID){
			$save_ALLOW_PER_ID=$ALLOW_PER_ID;
		}
		
		$save_APPROVE_PER_ID="NULL";
		if($APPROVE_PER_ID){
			$save_APPROVE_PER_ID=$APPROVE_PER_ID;
		}
		$save_per_type=$per_type;
		
		$db_insert1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
		$cmd = " update TA_PER_OT_CONTROL set  
					
					BUDGET_YEAR=$save_year,
					PAY_MONTH=$save_month,
					PAY_DATE='$PAY_DATE',
					START_DATE='$date_min',
					END_DATE='$date_max',
					ALLOW_USER=$save_ALLOW_PER_ID,
					APPROVE_USER=$save_APPROVE_PER_ID,
					UPDATE_USER=$SESS_USERID,
					UPDATE_DATE='$UPDATE_DATE'
					WHERE CONTROL_ID=".$CONTROL_ID;
		$db_insert1->send_cmd($cmd);
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 >$MENU_TITLE_LV2 > á¡éä¢»Ô´ºÑ­ªÕàºÔ¡¨èÒÂ OT [".$CONTROL_ID." : ".$save_per_type." : ".$save_year." : ".$save_month." : ".$date_min." : ".$date_max."]");
		
		
		/*Update µÑÇºØ¤¤Å*/
		$db_insert = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
		$cmd = " select ot.PER_ID,ot.SET_FLAG,ot.OT_DATE
							 from  TA_PER_OT ot
							where  ot.CONTROL_ID=$CONTROL_ID";
		$count_page_data2 = $db_dpis2->send_cmd($cmd);
		if($count_page_data2){
			while($data2 = $db_dpis2->get_array()){
				if($data2[SET_FLAG]==1){
					$ALLOW_FLAG=1;
				}else{
					$ALLOW_FLAG=2;
				}
				$cmd = " update TA_PER_OT set 
							AUDIT_FLAG = 1,
							AUDIT_USER=$SESS_USERID,AUDIT_DATE = '$UPDATE_DATE',
							SET_FLAG =1,
							ALLOW_FLAG=$ALLOW_FLAG,ALLOW_USER=$save_ALLOW_PER_ID,ALLOW_DATE='$UPDATE_DATE',
							APPROVE_FLAG=$ALLOW_FLAG,APPROVE_USER=$save_APPROVE_PER_ID,
							APPROVE_DATE='$UPDATE_DATE',
							UPDATE_USER=$SESS_USERID,UPDATE_DATE = '$UPDATE_DATE'
							where CONTROL_ID=$CONTROL_ID AND PER_ID=".$data2[PER_ID]." AND OT_DATE='".$data2[OT_DATE]."'";
							//echo $cmd."<br>" ;
				$db_insert->send_cmd($cmd);
				
				
				
			}
		}
					
		
	}
	
	
	if ($command == "UPCONFIRM_FLAG") {
		$save_ALLOW_PER_ID="NULL";
		if($ALLOW_PER_ID){
			$save_ALLOW_PER_ID=$ALLOW_PER_ID;
		}
		
		$save_APPROVE_PER_ID="NULL";
		if($APPROVE_PER_ID){
			$save_APPROVE_PER_ID=$APPROVE_PER_ID;
		}
		
		$db_insert1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
		$cmd = " update TA_PER_OT_CONTROL set  
					CONFIRM_FLAG=1,
					ALLOW_USER=$save_ALLOW_PER_ID,
					APPROVE_USER=$save_APPROVE_PER_ID,
					UPDATE_USER=$SESS_USERID,
					UPDATE_DATE='$UPDATE_DATE'
					WHERE CONTROL_ID=".$CONTROL_ID;
		$db_insert1->send_cmd($cmd);
		
		/*Update µÑÇºØ¤¤Å*/
		$db_insert = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
		$cmd = " select ot.PER_ID,ot.SET_FLAG,ot.OT_DATE
							 from  TA_PER_OT ot
							where  ot.CONTROL_ID=$CONTROL_ID";
		$count_page_data2 = $db_dpis2->send_cmd($cmd);
		if($count_page_data2){
			while($data2 = $db_dpis2->get_array()){
				if($data2[SET_FLAG]==1){
					$ALLOW_FLAG=1;
				}else{
					$ALLOW_FLAG=2;
				}
				$cmd = " update TA_PER_OT set 
							ALLOW_USER=$save_ALLOW_PER_ID,
							ALLOW_DATE='$UPDATE_DATE',
							APPROVE_USER=$save_APPROVE_PER_ID,
							APPROVE_DATE='$UPDATE_DATE',
							UPDATE_USER=$SESS_USERID,UPDATE_DATE = '$UPDATE_DATE'
							where CONTROL_ID=$CONTROL_ID AND PER_ID=".$data2[PER_ID]." AND OT_DATE='".$data2[OT_DATE]."'";
							//echo $cmd."<br>" ;
				$db_insert->send_cmd($cmd);
				
				
				
			}
		}
		
		
		$HID_CONFIRM_FLAG = 1;
	}
	
	
  
?>