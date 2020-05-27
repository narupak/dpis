<?
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	
	if($command == "SAVE"){
	$UPDATE_DATE = date("Y-m-d H:i:s");

		foreach($SET_WORKFLOW as $WORKFLOW_ID => $WORKFLOW_TIMES){
			$cmd = " update CONFIG_WORKFLOW set
								WORKFLOW_TIMES = $WORKFLOW_TIMES
							 where WORKFLOW_ID=$WORKFLOW_ID ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();

			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [$WORKFLOW_ID :: $WORKFLOW_TIMES]");
		} // loop foreach		
	} // end if

	$cmd = " select WORKFLOW_ID, LV_ID, LV_NAME, LV_DESCRIPTION, WORKFLOW_TIMES from CONFIG_WORKFLOW order by WORKFLOW_ID ";
	$db_dpis->send_cmd($cmd);
	while($data = $db_dpis->get_array()){
		$ARR_WORKFLOW[$data[WORKFLOW_ID]] = $data[LV_DESCRIPTION];
		$ARR_WORKFLOW_TIMES[$data[WORKFLOW_ID]] = $data[WORKFLOW_TIMES];
	} // loop while
?>