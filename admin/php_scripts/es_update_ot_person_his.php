<? 
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	$UPDATE_DATE = date("Y-m-d H:i:s");
	if ($command == "SETFLAG_AUDIT") {

		if(count($list_audit_id)>0){
			$db_insert1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
			for($i=0;$i<=count($list_audit_id);$i++){
				if(!empty($list_audit_id[$i])){
					$val =  explode("_",$list_audit_id[$i]);
					$cmd = " update TA_PER_OT set SET_FLAG = 1,ALLOW_FLAG=0,APPROVE_FLAG=0,
									CONTROL_ID=NULL,
									UPDATE_USER=$SESS_USERID,UPDATE_DATE = '$UPDATE_DATE'
									where CONTROL_ID =$CONTROL_ID AND OT_DATE='".$val[1]."' AND PER_ID=".$val[0];
									
					$db_insert1->send_cmd($cmd);
					//insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 >$MENU_TITLE_LV2 > บันทึกข้อมูลตรวจทานการทำ OT [".$val[0]." : ".$val[1]."]");

				}
				
			}
		}
		
		$command ="";
		
		/*echo "<script type='text/javascript'>parent.refresh_opener('1<::>1');</script>";*/
		
	}
	
	
	if ($command == "SETFLAG_AUDIT_UN") {
		
		if(count($list_audit_un)>0){
			$db_insert1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
			for($i=0;$i<=count($list_audit_un);$i++){
				if(!empty($list_audit_un[$i])){
					$val =  explode("_",$list_audit_un[$i]);
					$cmd = " update TA_PER_OT set SET_FLAG = 1,ALLOW_FLAG=1,APPROVE_FLAG=1,
									UPDATE_USER=$SESS_USERID,UPDATE_DATE = '$UPDATE_DATE',
									CONTROL_ID =$CONTROL_ID
									where   OT_DATE='".$val[1]."' AND PER_ID=".$val[0];
									
					$db_insert1->send_cmd($cmd);
	
				}
				
			}
		}
		
	$command ="";
		
		/*echo "<script type='text/javascript'>parent.refresh_opener('1<::>1');</script>";*/
		
	}
	

  
?>