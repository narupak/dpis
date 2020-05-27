<?
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	$UPDATE_DATE = date("Y-m-d H:i:s");

	if($command == "SAVE"){
		
		foreach($ARR_START_TEST as $ORG_ID => $START_TEST){
			$cmd = " delete from CONFIG_JOB_EVALUATION where ID=$ORG_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$END_TEST = $ARR_END_TEST[$ORG_ID];
			$END_APPROVE = $ARR_END_APPROVE[$ORG_ID];

			$START_TEST =  save_date($START_TEST);
			$END_TEST =  save_date($END_TEST);
			$END_APPROVE =  save_date($END_APPROVE);

			$cmd = " insert into CONFIG_JOB_EVALUATION
							 (ID, START_TEST, END_TEST, END_APPROVE)
							 values
							 ($ORG_ID, '$START_TEST', '$END_TEST', '$END_APPROVE') ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();

			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [$ORG_ID, $START_TEST, $END_TEST, $END_APPROVE]");
		} // loop foreach
	} // end if
?>