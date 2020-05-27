<?
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");	
	include("php_scripts/function_list.php");		
	include("php_scripts/load_per_control.php");

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$UPDATE_DATE = date("Y-m-d H:i:s");

	if ($command == "CHECK" && trim($DEPARTMENT_ID)) {
		$alert_err = $alert_err_text = $non_promote = $non_promote_text = "";
		if($ORG_ID) $arr_search_condition[] = "(a.ORG_ID = $ORG_ID)";
		if(count($arr_search_condition)) $search_condition = " and ". implode(" and ", $arr_search_condition);
		$onmouse_event = " onMouseOver=\"this.className='table_body_over';\"  onMouseOut=\"this.className='table_body_out_salpromote_check';\" ";
		$cmd = " select a.POS_ID, a.POS_NO, a.PL_CODE, b.PL_NAME, a.ORG_ID, a.ORG_ID_1, a.ORG_ID_2, a.PT_CODE, b.PL_TYPE, 
				 a.CL_NAME, a.POS_SALARY, b.PL_CODE_NEW 
				 from PER_POSITION a, PER_LINE b 
				 where a.POS_STATUS = 1 and a.PL_CODE=b.PL_CODE and a.DEPARTMENT_ID = $DEPARTMENT_ID $search_condition
				 order by a.POS_ID ";				  
		$count_temp = $db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		$err_count = 0;
		while ($data = $db_dpis->get_array()) {
			$POS_ID = $data[POS_ID];
			$POS_NO = trim($data[POS_NO]);
			$PL_CODE = trim($data[PL_CODE]);
			$PL_CODE_NEW = trim($data[PL_CODE_NEW]);
			$PL_NAME = trim($data[PL_NAME]);
			$PL_TYPE = $data[PL_TYPE];
			$CL_NAME = trim($data[CL_NAME]);

			$cmd = " select PER_ID, LEVEL_NO as PER_LEVEL from PER_PERSONAL where POS_ID=$POS_ID ";				  
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$TMP_PER_ID = trim($data2[PER_ID]);
			$CMD_LEVEL = $data2[PER_LEVEL];
			if ($TMP_PER_ID) 
				$cmd = " select TIT_J_ID, TIT_J_NAME from HR_HEAD_COUNT a, HR_TITLE_J b, HR_EMPLOYEE c 
								  where a.HEAD_COUNT_ID = c.HC_HEAD_COUNT_ID and c.TITLE_J_TIT_J_ID = b.TIT_J_ID
								  and a.POS_CODE = '$POS_NO' and a.POS_TYPE = 1 and a.VAC_FLG <> '9' ";
			else
				$cmd = " select TIT_J_ID, TIT_J_NAME from HR_HEAD_COUNT a, HR_TITLE_J b 
								  where a.TITLE_J_TIT_J_ID = b.TIT_J_ID 
								  and a.POS_CODE = '$POS_NO' and a.POS_TYPE = 1 and a.VAC_FLG <> '9' ";
			$db_dpis35->send_cmd($cmd);
			$data2 = $db_dpis35->get_array();
			$PL_CODE_CPD = trim($data2[TIT_J_ID]);
			$PL_NAME_CPD = trim($data2[TIT_J_NAME]);
			
			$LEN_POS = strlen($POS_NO);
			$cmd = " select a.COM_ID, a.CMD_SEQ, a.CMD_POSITION, a.PL_CODE, b.PL_NAME from PER_COMDTL a, PER_LINE b 
							  where a.PL_CODE = b.PL_CODE and substr(CMD_POSITION,1,$LEN_POS)='$POS_NO' and 
							  substr(CMD_POSITION,$LEN_POS+1,2)='\|' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$COM_ID = $data2[COM_ID];
			$CMD_SEQ = $data2[CMD_SEQ];
			$CMD_POSITION = $data2[CMD_POSITION];
			$PL_CODE_CMD = $data2[PL_CODE];
			$PL_NAME_CMD = $data2[PL_NAME];

			if ($PL_NAME!=$PL_NAME_CPD) {
				$err_count++;
				$alert_err_text .= "		<span width='100%' $onmouse_event> $err_count .	เลขที่ตำแหน่ง $POS_NO ตำแหน่งในสายงาน $PL_CODE $PL_NAME $PL_CODE_CPD $PL_NAME_CPD </span>  \n ";
			} elseif ($PL_NAME!=$PL_NAME_CMD) {
				$err_count++;
				$alert_err_text .= "		<span width='100%' $onmouse_event> $err_count .	เลขที่ตำแหน่ง $POS_NO ตำแหน่งในสายงาน $PL_CODE $PL_NAME $PL_CODE_CMD $PL_NAME_CMD </span>  \n ";
				$CMD_POSITION = "$POS_NO\|$PL_NAME";
				$cmd = " update PER_COMDTL set CMD_POSITION = '$CMD_POSITION', PL_CODE = '$PL_CODE' 
								  where COM_ID = $COM_ID and CMD_SEQ=$CMD_SEQ ";
//				echo "$cmd<br>";
				$db_dpis2->send_cmd($cmd);
			}
		}	// while ($data = $db_dpis->get_array()) 

		if (!trim($alert_err_text)) { 
			$alert_success = "<table border='0' width='100%'><tr><td valign='bottom' align='center' class='label_alert'> ... เสร็จสิ้นการตรวจสอบ ... </td></tr></table>";
		} // end if
	}	// 	if ($command == "CHECK") {
?>