<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 50;
	$start_record = ($current_page - 1) * $data_per_page;		
	
	$UPDATE_DATE = date("Y-m-d H:i:s");

	$cmd = " select PL_CODE, LEVEL_NO from POS_DES_INFO where POS_DES_ID=$POS_DES_ID ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$PL_CODE = trim($data[PL_CODE]);
	$LEVEL_NO = trim($data[LEVEL_NO]);
	
	$cmd = " select 	a.ACC_TYPE_ID, b.ACC_TYPE_NAME
					 from		ACCOUNTABILITY_LEVEL_TYPE a, ACCOUNTABILITY_TYPE b
					 where	a.ACC_TYPE_ID=b.ACC_TYPE_ID and a.PL_CODE='$PL_CODE' and a.LEVEL_NO='$LEVEL_NO'
					 order by a.ACC_TYPE_ID ";
	$db_dpis->send_cmd($cmd);
	while($data = $db_dpis->get_array()) { 
		$ARR_ACCOUNT_TYPE[] = $data[ACC_TYPE_ID];
		$ARR_ACCOUNT_TYPE_NAME[$data[ACC_TYPE_ID]] = $data[ACC_TYPE_NAME];
	} // loop while
	
	if(!$SUBPAGE) $SUBPAGE = 1;
	$JOB_TYPE = "a";

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

			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > บันทึกข้อมูล [".trim($POS_DES_ID)." : ".$POS_JOB_DES_INFO."]");
		} // end if
		
		$cmd = " select POS_JOB_DES_INFO from POS_JOB_DES_INFO where POS_DES_ID=$POS_DES_ID and POS_JOB_DES_TYPE='$JOB_TYPE' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$POS_JOB_DES_INFO = $data[POS_JOB_DES_INFO];
	}elseif($SUBPAGE >= 2){
		if($command=="ADD" && trim($ACC_DESCRIPTION)){
			$cmd = " select max(ACC_ID) as MAX_ID from ACCOUNTABILITY_INFO_PRIMARY ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$MAX_ID = $data[MAX_ID] + 1;

			$cmd = " insert into ACCOUNTABILITY_INFO_PRIMARY (ACC_ID, POS_DES_ID, ACC_TYPE_ID, ACC_DESCRIPTION, UPDATE_USER, UPDATE_DATE)
							 values ($MAX_ID, $POS_DES_ID, ".$ARR_ACCOUNT_TYPE[($SUBPAGE-2)].", '$ACC_DESCRIPTION', $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [".trim($POS_DES_ID)." : ".$ARR_ACCOUNT_TYPE[($SUBPAGE-2)]." : ".$ACC_DESCRIPTION."]");
		} // command ADD

		if($command=="UPDATE" && $ACC_ID){
			$cmd = " update ACCOUNTABILITY_INFO_PRIMARY set
							ACC_DESCRIPTION = '$ACC_DESCRIPTION',
							UPDATE_USER=$SESS_USERID, 
							UPDATE_DATE='$UPDATE_DATE' 
							 where ACC_ID=$ACC_ID ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [".trim($POS_DES_ID)." : ".$ARR_ACCOUNT_TYPE[($SUBPAGE-2)]." : ".$ACC_ID." : ".$ACC_DESCRIPTION."]");
			
			unset($ACC_ID);
		} // command UPDATE
		
		if($command=="DELETE" && $ACC_ID){
			$cmd = " delete from ACCOUNTABILITY_INFO_PRIMARY where ACC_ID=$ACC_ID ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [".trim($POS_DES_ID)." : ".$ARR_ACCOUNT_TYPE[($SUBPAGE-2)]." : ".$ACC_ID."]");
			
			unset($ACC_ID);
		} // command DELETE
		
		if(($UPD || $VIEW) && $ACC_ID){
			$cmd = " select ACC_DESCRIPTION from ACCOUNTABILITY_INFO_PRIMARY where ACC_ID=$ACC_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$ACC_DESCRIPTION = $data[ACC_DESCRIPTION];
		} // end if
		
		if( (!$UPD && !$DEL && !$VIEW && !$err_text) || !$ACC_ID ){
			$ACC_ID = "";
			$ACC_DESCRIPTION = "";
		} // end if
	} // end if
?>