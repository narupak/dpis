<?
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

	if($command == "SAVE" && trim($START_TEST) && trim($END_TEST) && trim($END_APPROVE)){
		$UPDATE_DATE = date("Y-m-d H:i:s");

		$START_TEST =  save_date($START_TEST);
		$END_TEST =  save_date($END_TEST);
		$END_APPROVE =  save_date($END_APPROVE);

		$cmd = " select from CONFIG_JOB_EVALUATION where ID = 0 ";
		$count_data = $db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		if ($count_data)
			$cmd = " update CONFIG_JOB_EVALUATION set START_TEST = '$START_TEST', END_TEST = '$END_TEST', END_APPROVE = '$END_APPROVE'			 where ID = 0 ";
		else
			$cmd = " insert into CONFIG_JOB_EVALUATION
						 (ID, START_TEST, END_TEST, END_APPROVE)
						 values
						 (0, '$START_TEST', '$END_TEST', '$END_APPROVE') ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [$START_TEST, $END_TEST, $END_APPROVE]");
		
	} // end if

	$cmd = " select START_TEST, END_TEST, END_APPROVE from CONFIG_JOB_EVALUATION where ID=0 ";
	$db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data = $db_dpis->get_array();

	$START_TEST = show_date_format($data[START_TEST], 1);
	$END_TEST = show_date_format($data[END_TEST], 1);
	$END_APPROVE = show_date_format($data[END_APPROVE], 1);
?>