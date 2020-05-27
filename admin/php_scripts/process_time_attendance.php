<?	
	include("../php_scripts/connect_database.php");
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	$UPDATE_DATE = date("Y-m-d H:i:s");

	$temp_date = explode("/", trim($START_DATE));
	$TEMP_START_DATE = ($temp_date)? ($temp_date[2]-543) ."-". $temp_date[1] ."-". $temp_date[0]. " 00:00:00" : ""; 
	$temp_date = explode("/", trim($END_DATE));
	$TEMP_END_DATE = ($temp_date)? ($temp_date[2]-543) ."-". $temp_date[1] ."-". $temp_date[0] . " 23:59:59" : ""; 

	if($command == "CONFIRM"){
//		$cmd = " delete from PER_WORK_TIME where START_DATE=$TR_ID and DEPARTMENT_ID=$DEPARTMENT_ID ";
//		$db_dpis->send_cmd($cmd);
//		$data = $db_dpis->get_array();
		
		$cmd = " select PER_ID, min(TIME_STAMP) as TIME_STAMP from PER_TIME_ATTENDANCE where TIME_STAMP >='$TEMP_START_DATE' AND TIME_STAMP <= '$TEMP_END_DATE' group by PER_ID, substr(TIME_STAMP,1,10) ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
//		echo $cmd;
		while ( $data = $db_dpis->get_array() ) {
			$PER_ID = $data[PER_ID];
			$TIME_STAMP = trim($data[TIME_STAMP]);
			$CHECK_DATE = substr($TIME_STAMP,0,10);

			$cmd = " select a.WC_CODE, b.WC_END FROM PER_WORK_CYCLEHIS a, PER_WORK_CYCLE b WHERE a.PER_ID = $PER_ID AND a.START_DATE <='$CHECK_DATE' AND a.END_DATE >= '$CHECK_DATE' AND a.WC_CODE = b.WC_CODE ";
			$db_dpis1->send_cmd($cmd);
//			echo $cmd;
//			$db_dpis1->show_error();
			$data1 = $db_dpis1->get_array();
			$WC_CODE = $data1[WC_CODE];
			$WC_END = $data1[WC_END];
			$TEMP_END_DATE = substr($TIME_STAMP,0,11) . substr($WC_END,0,2) . ":" . substr($WC_END,2,2) . ":00"; 

			$cmd = " select * FROM PER_WORK_TIME WHERE PER_ID = $PER_ID AND START_DATE = '$TIME_STAMP' ";
			$count_data = $db_dpis1->send_cmd($cmd);
//			$db_dpis1->show_error();

			if (!$count_data){
				$cmd = " select max(WT_ID) as max_id from PER_WORK_TIME ";
				$db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$data1 = array_change_key_case($data1, CASE_LOWER);
				$WT_ID = $data1[max_id] + 1;
		
				$cmd = " insert into PER_WORK_TIME (WT_ID, PER_ID, WC_CODE, START_DATE, END_DATE, HOLIDAY_FLAG, ABSENT_FLAG, UPDATE_USER, UPDATE_DATE) 
					 values ($WT_ID, $PER_ID, '$WC_CODE', '$TIME_STAMP', '$TEMP_END_DATE', NULL, NULL, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis1->send_cmd($cmd);
//				$db_dpis1->show_error();
			} 
		} // end while

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ยืนยันเวลาการมาปฏิบัติราชการ [$TEMP_START_DATE : $TEMP_END_DATE]");
	} // end if
?>