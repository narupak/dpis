<? 
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	$UPDATE_DATE = date("Y-m-d H:i:s");

	if($command=="UPDATE"){
		$db_dpis_up = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
		$cmd = "  UPDATE  PER_TIME_ATT SET	LOCK_STATUS='$HIDLOCK_STATUS',
						UPDATE_USER=$SESS_USERID,UPDATE_DATE='$UPDATE_DATE'
						 where TA_CODE= '$HIDTA_CODE' ";
		$db_dpis_up->send_cmd($cmd);
		
		if($HIDLOCK_STATUS=="N"){$TMPLOCK_STATUS="Unlock";}else{$TMPLOCK_STATUS="Lock";}
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > $TMPLOCK_STATUS [$HIDTA_CODE : $HIDTA_NAME]");

		echo "<script>window.location='../admin/es_t0302_check_att.html?MENU_ID_LV0=$MENU_ID_LV0&MENU_ID_LV1=$MENU_ID_LV1&MENU_ID_LV2=$MENU_ID_LV2';</script>";

 	} // end if
	
	
	if ($command == "SETFLAG_ALLOW") {

		if($list_audit_all=="1"){ /* บันทึกทั้งหมด*/
		
				if(trim($search_code)) $arr_search_condition[] = "(a.TA_CODE like '".trim($search_code)."%')";
				if(trim($search_name)) $arr_search_condition[] = "(a.TA_NAME like '%".trim($search_name)."%')";
				if(trim($search_wl_code)) $arr_search_condition[] = "(a.WL_CODE = '".trim($search_wl_code)."')";
				$search_condition = "";
				if(count($arr_search_condition)) $search_condition = " and " . implode(" and ", $arr_search_condition);
				
				$db_insert = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
				$cmd = " select a.TA_CODE
					from PER_TIME_ATT a, PER_WORK_LOCATION b 
					where a.WL_CODE = b.WL_CODE $search_condition ";
					$count_page_data = $db_dpis->send_cmd($cmd);
					if($count_page_data){
						while($data = $db_dpis->get_array()){
							$cmd = " update PER_TIME_ATT set RESYNC_FLAG = 'Y',
										UPDATE_USER=$SESS_USERID,UPDATE_DATE = '$UPDATE_DATE'
										where TA_CODE=".$data[TA_CODE];
							$db_insert->send_cmd($cmd);

						}
				 	}
		
			
		}else{  /*กรณีที่เลือกบางรายการ*/
		
			if(count($list_allow_id)>0){
				$chkID=  "0";
				for($i=0;$i<=count($list_allow_id);$i++){
					if(!empty($list_allow_id[$i])){
						$cmd = " update PER_TIME_ATT set RESYNC_FLAG = 'Y',
										UPDATE_USER=$SESS_USERID,UPDATE_DATE = '$UPDATE_DATE'
										where TA_CODE=".$list_allow_id[$i];
						$db_dpis->send_cmd($cmd);
						$chkID= $chkID.",".$list_allow_id[$i];
					}
				}
				
				$cmd = " update PER_TIME_ATT set RESYNC_FLAG = 'N',
								UPDATE_USER=$SESS_USERID,UPDATE_DATE = '$UPDATE_DATE'
								where TA_CODE not in(".$chkID.")";
				$db_dpis->send_cmd($cmd);
				
			}else{
				
				$cmd = " update PER_TIME_ATT set RESYNC_FLAG = 'N',
								UPDATE_USER=$SESS_USERID,UPDATE_DATE = '$UPDATE_DATE' ";
				$db_dpis->send_cmd($cmd);
				
			}
			
			
		
		}
		
		$command = "";		

	}
	
	
	
  
?>