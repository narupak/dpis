<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 50;
	$start_record = ($current_page - 1) * $data_per_page;		
	
	$UPDATE_DATE = date("Y-m-d H:i:s");

	if(!$SUBPAGE) $SUBPAGE = 1;
	$JOB_TYPE = "k";

	if($SUBPAGE==1){
		if($command=="SAVE"){
			$cmd = " select POS_JOB_DES_ID from POS_JOB_DES_INFO where POS_DES_ID=$POS_DES_ID and POS_JOB_DES_TYPE='$JOB_TYPE' ";
			$count_info = $db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$POS_JOB_DES_ID = $data[POS_JOB_DES_ID];
			if(!$POS_JOB_DES_ID){
				$cmd = " SELECT max(POS_JOB_DES_ID) as MAX_ID FROM POS_JOB_DES_INFO ";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				$MAX_ID = $data[MAX_ID] + 1;

				$cmd = " insert into POS_JOB_DES_INFO (POS_JOB_DES_ID, POS_DES_ID, POS_JOB_DES_INFO, POS_JOB_DES_TYPE, UPDATE_USER, UPDATE_DATE)
								 values ($MAX_ID, $POS_DES_ID, '$POS_JOB_DES_INFO', '$JOB_TYPE', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			}else{
				$cmd = " update POS_JOB_DES_INFO set
								POS_JOB_DES_INFO='$POS_JOB_DES_INFO',
								UPDATE_USER=$SESS_USERID, 
								UPDATE_DATE='$UPDATE_DATE' 
								 where POS_JOB_DES_ID=$POS_JOB_DES_ID ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			} // end if

			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > �ѹ�֡������ [".trim($POS_DES_ID)." : ".$POS_JOB_DES_INFO."]");
		} // end if
		
		$cmd = " select POS_JOB_DES_INFO from POS_JOB_DES_INFO where POS_DES_ID=$POS_DES_ID and POS_JOB_DES_TYPE='$JOB_TYPE' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$POS_JOB_DES_INFO = $data[POS_JOB_DES_INFO];
	}elseif($SUBPAGE==2){
		if($command=="ADD" && trim($JOB_DES_ID)!="" && trim($JOB_DES_LEVEL)!=""){
			$cmd = " select max(POS_JOB_DES_PRI_ID) as MAX_ID from POS_JOB_DES_PRIMARY ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$MAX_ID = $data[MAX_ID] + 1;

			$cmd = " insert into POS_JOB_DES_PRIMARY (POS_JOB_DES_PRI_ID, POS_DES_ID, JOB_DES_ID, JOB_DES_LEVEL, JOB_TYPE, UPDATE_USER, UPDATE_DATE)
							 values ($MAX_ID, $POS_DES_ID, $JOB_DES_ID, $JOB_DES_LEVEL, '$JOB_TYPE', $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ���������� [".trim($POS_DES_ID)." : ".$JOB_DES_ID." : ".$JOB_DES_LEVEL."]");
		} // command ADD

		if($command=="UPDATE" && $POS_JOB_DES_PRI_ID){
			$cmd = " update POS_JOB_DES_PRIMARY set
							JOB_DES_ID = $JOB_DES_ID, 
							JOB_DES_LEVEL = $JOB_DES_LEVEL,
							UPDATE_USER=$SESS_USERID, 
							UPDATE_DATE='$UPDATE_DATE' 
							 where POS_JOB_DES_PRI_ID=$POS_JOB_DES_PRI_ID ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ��䢢����� [".trim($POS_DES_ID)." : ".$POS_JOB_DES_PRI_ID." : ".$JOB_DES_ID." : ".$JOB_DES_LEVEL."]");
			
			unset($POS_JOB_DES_PRI_ID);
		} // command UPDATE
		
		if($command=="DELETE" && $POS_JOB_DES_PRI_ID){
			$cmd = " delete from POS_JOB_DES_PRIMARY where POS_JOB_DES_PRI_ID=$POS_JOB_DES_PRI_ID ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ź������ [".trim($POS_DES_ID)." : ".$POS_JOB_DES_PRI_ID."]");
			
			unset($POS_JOB_DES_PRI_ID);
		} // command DELETE
		
		if(($UPD || $VIEW) && $POS_JOB_DES_PRI_ID){
			$cmd = " select 	a.JOB_DES_ID, b.JOB_DES_NAME, a.JOB_DES_LEVEL, c.JOB_DES_LEVEL_DESCRIPTION 
							 from 		POS_JOB_DES_PRIMARY a, KNOWLEDGE_INFO b, KNOWLEDGE_LEVEL c
							 where 	POS_JOB_DES_PRI_ID=$POS_JOB_DES_PRI_ID and a.JOB_DES_ID=b.JOB_DES_ID 
											and a.JOB_DES_ID=c.JOB_DES_ID and a.JOB_DES_LEVEL=c.JOB_DES_LEVEL ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$JOB_DES_ID = $data[JOB_DES_ID];
			$JOB_DES_NAME = $data[JOB_DES_NAME];
			$JOB_DES_LEVEL = $data[JOB_DES_LEVEL];
			$JOB_DES_LEVEL_DESCRIPTION = $data[JOB_DES_LEVEL_DESCRIPTION];
		} // end if
		
		if( (!$UPD && !$DEL && !$VIEW && !$err_text) || !$POS_JOB_DES_PRI_ID ){
			$POS_JOB_DES_PRI_ID = "";
			$JOB_DES_ID = "";
			$JOB_DES_NAME = "";
			$JOB_DES_LEVEL = "";
			$JOB_DES_LEVEL_DESCRIPTION = "";
		} // end if
	} // end if
?>