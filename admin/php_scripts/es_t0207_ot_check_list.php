<? 
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd, $dpisdb_port);

	if($command=="DELETE"){
				$del_CONTROL_ID = $CONTROL_ID;
				$cmd = " select PER_TYPE,BUDGET_YEAR,PAY_MONTH,ORG_ID,DEPARTMENT_ID from TA_PER_OT_CONTROL 
							where CONTROL_ID=$del_CONTROL_ID
							AND ORG_ID=$HIDORG_ID 
						   AND NVL(DEPARTMENT_ID,-1)=$HIDDEPARTMENT_ID
						   AND NVL(ORG_LOWER1,-1)=$HIDORG_LOWER1
						   AND NVL(ORG_LOWER2,-1)=$HIDORG_LOWER2
						   AND NVL(ORG_LOWER3,-1)=$HIDORG_LOWER3";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				$del_PER_TYPE = $data[PER_TYPE];
				$del_BUDGET_YEAR = $data[BUDGET_YEAR];
				$del_PAY_MONTH = $data[PAY_MONTH];
				$del_ORG_ID = $data[ORG_ID];
				$del_DEPARTMENT_ID = $data[DEPARTMENT_ID];
				insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > Åº¢éÍÁÙÅÊÃØ»ºÑ­ªÕàºÔ¡¨èÒÂ OT [$del_PER_TYPE : $del_BUDGET_YEAR : $del_PAY_MONTH : $del_ORG_ID : $del_DEPARTMENT_ID ]");


				$cmd = " select ot.PER_ID,ot.SET_FLAG,ot.AUDIT_USER,ot.AUDIT_DATE,ot.OT_DATE
										 from  TA_PER_OT ot
										where  ot.CONTROL_ID=$CONTROL_ID
										AND ORG_ID=$HIDORG_ID 
									   AND NVL(DEPARTMENT_ID,-1)=$HIDDEPARTMENT_ID
									   AND NVL(ORG_LOWER1,-1)=$HIDORG_LOWER1
									   AND NVL(ORG_LOWER2,-1)=$HIDORG_LOWER2
									   AND NVL(ORG_LOWER3,-1)=$HIDORG_LOWER3
										";
					$count_page_data2 = $db_dpis2->send_cmd($cmd);
					if($count_page_data2){
						while($data2 = $db_dpis2->get_array()){
							if($data2[SET_FLAG]==1){
								$ALLOW_FLAG=1;
								$AUDIT_FLAG=1;
								$AUDIT_USER=$data2[AUDIT_USER];
								$AUDIT_DATE=$data2[AUDIT_DATE];
							}else{
								$ALLOW_FLAG=2;
								$AUDIT_FLAG=0;
								$AUDIT_USER="NULL";
								$AUDIT_DATE="NULL";
							}
							
							$db_dpis_up = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
							$cmd = " UPDATE TA_PER_OT	SET
										ALLOW_FLAG =0,ALLOW_USER=NULL,ALLOW_DATE=NULL,
										APPROVE_FLAG =0,APPROVE_USER=NULL,APPROVE_DATE=NULL,CONTROL_ID=NULL,
										AUDIT_FLAG=$AUDIT_FLAG,AUDIT_USER=$AUDIT_USER,AUDIT_DATE='$AUDIT_DATE'
							where CONTROL_ID=$del_CONTROL_ID AND PER_ID=".$data2[PER_ID]." AND OT_DATE='".$data2[OT_DATE]."'
										AND ORG_ID=$HIDORG_ID 
									   AND NVL(DEPARTMENT_ID,-1)=$HIDDEPARTMENT_ID
									   AND NVL(ORG_LOWER1,-1)=$HIDORG_LOWER1
									   AND NVL(ORG_LOWER2,-1)=$HIDORG_LOWER2
									   AND NVL(ORG_LOWER3,-1)=$HIDORG_LOWER3
							";
							$db_dpis_up->send_cmd($cmd);
							
				
							
						}
					}
				
				$cmd = " select  count(CONTROL_ID) AS CNT from TA_PER_OT where CONTROL_ID=$del_CONTROL_ID";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				if($data[CNT]<=0){
					$db_dpis_up1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
					$cmd_del = " delete from TA_PER_OT_CONTROL	where  CONTROL_ID=$del_CONTROL_ID";
					$db_dpis_up1->send_cmd($cmd_del);
				}

				echo "<script>window.location='../admin/es_t0207_ot_check_list.html?MENU_ID_LV0=$MENU_ID_LV0&MENU_ID_LV1=$MENU_ID_LV1&MENU_ID_LV2=$MENU_ID_LV2';</script>";
 	} // end if*/

	if($command=="NOCONFIRM"){
				$UPDATE_DATE = date("Y-m-d H:i:s");
				$del_CONTROL_ID = $CONTROL_ID;
				$cmd = " select PER_TYPE,BUDGET_YEAR,PAY_MONTH,ORG_ID,DEPARTMENT_ID from TA_PER_OT_CONTROL 
							where CONTROL_ID=$del_CONTROL_ID
							AND ORG_ID=$HIDORG_ID 
						   AND NVL(DEPARTMENT_ID,-1)=$HIDDEPARTMENT_ID
						   AND NVL(ORG_LOWER1,-1)=$HIDORG_LOWER1
						   AND NVL(ORG_LOWER2,-1)=$HIDORG_LOWER2
						   AND NVL(ORG_LOWER3,-1)=$HIDORG_LOWER3";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				$del_PER_TYPE = $data[PER_TYPE];
				$del_BUDGET_YEAR = $data[BUDGET_YEAR];
				$del_PAY_MONTH = $data[PAY_MONTH];
				$del_ORG_ID = $data[ORG_ID];
				$del_DEPARTMENT_ID = $data[DEPARTMENT_ID];
				insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > Â¡àÅÔ¡Â×¹ÂÑ¹ÊÃØ»ºÑ­ªÕàºÔ¡¨èÒÂ OT [$del_PER_TYPE : $del_BUDGET_YEAR : $del_PAY_MONTH : $del_ORG_ID : $del_DEPARTMENT_ID ] : $UPDATE_DATE ");

				$db_dpis_up1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
				$cmd_del = " update TA_PER_OT_CONTROL set 
				CONFIRM_FLAG=0,	
				UPDATE_USER=$SESS_USERID,UPDATE_DATE = '$UPDATE_DATE'
				where  CONTROL_ID=$del_CONTROL_ID";
				$db_dpis_up1->send_cmd($cmd_del);

				echo "<script>window.location='../admin/es_t0207_ot_check_list.html?MENU_ID_LV0=$MENU_ID_LV0&MENU_ID_LV1=$MENU_ID_LV1&MENU_ID_LV2=$MENU_ID_LV2';</script>";
 	} // end if*/


	
  
?>