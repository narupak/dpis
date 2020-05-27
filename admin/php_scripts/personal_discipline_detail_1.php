<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");	

//	echo "DCL_ID=$DCL_ID, DD_ID=$DD_ID<br>";
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 50;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$UPDATE_DATE = date("Y-m-d H:i:s");

	$UPD = "";
	if(!$DD_ID) $DD_ID = 0;
	$cmd = "	SELECT 	DD_ASS_DATE, DD_PER_ID
						FROM		PER_DISCIPLINE_DTL 
						WHERE	(DCL_ID=$DCL_ID and DD_TYPE=$DD_TYPE) or (DD_ID=$DD_ID) ";
	$count_data = $db_dpis->send_cmd($cmd);
	if ($count_data) $UPD = 1;

	if( !$DD_TYPE ) $DD_TYPE = 1;
	if ($command=="ADD" || $command=="UPDATE") {
		if($DD_ASS_DATE) $DD_ASS_DATE = save_date($DD_ASS_DATE); 
		if($DD_RECOMMEND_DATE) $DD_RECOMMEND_DATE = save_date($DD_RECOMMEND_DATE); 
		if($DD_OUT_DATE) $DD_OUT_DATE = save_date($DD_OUT_DATE); 
		if($DD_BACK_DATE) $DD_BACK_DATE = save_date($DD_BACK_DATE); 
		if($DD_DOC_DATE) $DD_DOC_DATE = save_date($DD_DOC_DATE); 
	}

	if($command=="ADD" && $DCL_ID && $DD_TYPE && trim($DD_PER_ID)){
		$cmd = " select max(DD_ID) as max_id from PER_DISCIPLINE_DTL ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$DD_ID = $data[max_id] + 1;
		
		$cmd = " 	insert into PER_DISCIPLINE_DTL (DD_ID, DCL_ID, DD_TYPE, DD_ASS_DATE, DD_PER_ID, DD_RECOMMEND_DATE, 
							DD_OUT_DATE, DD_BACK_DATE, DD_DOC_DESC, DD_DOC_NO, DD_DOC_DATE, DD_REMARK, UPDATE_USER, UPDATE_DATE)
							values ($DD_ID, $DCL_ID, $DD_TYPE, '$DD_ASS_DATE', $DD_PER_ID, '$DD_RECOMMEND_DATE',
							'$DD_OUT_DATE', '$DD_BACK_DATE', '$DD_DOC_DESC', '$DD_DOC_NO', '$DD_DOC_DATE', '$DD_REMARK', $SESS_USERID, '$UPDATE_DATE')   ";
		$db_dpis->send_cmd($cmd);
//			echo "xxxxxxxxxx".$cmd;				
//			$db_dpis->show_error();
	
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มการดำเนินการทางวินัย [$DD_ID : $DCL_ID : $DD_TYPE : $DD_ASS_DATE]");
	} // end if

	if($command=="UPDATE" && (($DCL_ID && $DD_TYPE) || $DD_ID) && trim($DD_PER_ID)){
		if(!$DD_ID) $DD_ID = 0;
		$cmd = " UPDATE PER_DISCIPLINE_DTL SET
							DD_ASS_DATE='$DD_ASS_DATE', 
							DD_PER_ID=$DD_PER_ID,
							DD_RECOMMEND_DATE='$DD_RECOMMEND_DATE',
							DD_OUT_DATE='$DD_OUT_DATE',
							DD_BACK_DATE='$DD_BACK_DATE',
							DD_DOC_DESC='$DD_DOC_DESC',
							DD_DOC_NO='$DD_DOC_NO',
							DD_DOC_DATE='$DD_DOC_DATE',
							DD_REMARK='$DD_REMARK',
							UPDATE_USER=$SESS_USERID, 
							UPDATE_DATE='$UPDATE_DATE'
						WHERE (DCL_ID=$DCL_ID and DD_TYPE=$DD_TYPE) or (DD_ID=$DD_ID) ";
		$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขการดำเนินการทางวินัย [$DD_ID : $DCL_ID : $DD_TYPE : $DD_ASS_DATE]");
	} // end if
	
	if($command=="DELETE" && (($DCL_ID && $DD_TYPE) || $DD_ID)){
		if(!$DD_ID) $DD_ID = 0;
		$cmd = " select DD_ASS_DATE from PER_DISCIPLINE_DTL where (DCL_ID=$DCL_ID and DD_TYPE=$DD_TYPE) or (DD_ID=$DD_ID) ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$DD_ASS_DATE = $data[DD_ASS_DATE];
		
		$cmd = " delete from PER_DISCIPLINE_DTL where DCL_ID=$DCL_ID and DD_ID=$DD_ID ";
		$db_dpis->send_cmd($cmd);

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบการดำเนินการทางวินัย [$DD_ID : $DD_TYPE : $DD_ASS_DATE]");
	} // end if

	if(($UPD || $VIEW) && (($DCL_ID && $DD_TYPE) || $DD_ID)){
		if(!$DD_ID) $DD_ID = 0;
		$cmd = "	SELECT 	DD_ASS_DATE, DD_PER_ID, DD_RECOMMEND_DATE, DD_OUT_DATE, 
												DD_BACK_DATE, DD_DOC_DESC, DD_DOC_NO, DD_DOC_DATE, DD_REMARK
							FROM		PER_DISCIPLINE_DTL a
							WHERE	(DCL_ID=$DCL_ID and DD_TYPE=$DD_TYPE) or (DD_ID=$DD_ID) ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$DD_ASS_DATE = show_date_format($data[DD_ASS_DATE],1); 
		$DD_RECOMMEND_DATE = show_date_format($data[DD_RECOMMEND_DATE],1); 
		$DD_OUT_DATE = show_date_format($data[DD_OUT_DATE],1); 
		$DD_BACK_DATE = show_date_format($data[DD_BACK_DATE],1); 
		$DD_DOC_DATE = show_date_format($data[DD_DOC_DATE],1); 
		$DD_DOC_DESC = trim($data[DD_DOC_DESC]);
		$DD_DOC_NO = trim($data[DD_DOC_NO]);
		$DD_REMARK = trim($data[DD_REMARK]);

		$DD_PER_ID = $data[DD_PER_ID];
		$cmd = " select PN_CODE, PER_NAME, PER_SURNAME from PER_PERSONAL where PER_ID=$DD_PER_ID ";
//		echo "DD_PER_ID = $cmd<br>";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$DD_PER_NAME = trim($data[PER_NAME]) ." ". trim($data[PER_SURNAME]);
		$PN_CODE = $data[PN_CODE];

		$cmd = " select PN_NAME from PER_PRENAME where PN_CODE='$PN_CODE' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$DD_PER_NAME = trim($data[PN_NAME]) . $DD_PER_NAME;
	} // end if
	
	if( !$UPD && !$VIEW){
		unset($DD_ID);
		unset($DD_ASS_DATE);
		unset($DD_PER_ID);
		unset($DD_PER_NAME);
		unset($DD_RECOMMEND_DATE);
		unset($DD_OUT_DATE);
		unset($DD_BACK_DATE);
		unset($DD_DOC_DESC);
		unset($DD_DOC_NO);
		unset($DD_DOC_DATE);
		unset($DD_REMARK);
	} // end if 
?>