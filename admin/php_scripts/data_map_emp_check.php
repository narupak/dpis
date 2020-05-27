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
	
	$class = "table_body";
	$UPDATE_DATE = date("Y-m-d H:i:s");

	if ($command == "CHECK" && trim($DEPARTMENT_ID)) {
		$alert_err = $alert_err_text = $non_promote = $non_promote_text = "";
		if($ORG_ID) $arr_search_condition[] = "(a.ORG_ID = $ORG_ID)";
		if(count($arr_search_condition)) $search_condition = " and ". implode(" and ", $arr_search_condition);
		$onmouse_event = " onMouseOver=\"this.className='table_body_over';\"  onMouseOut=\"this.className='$class';\" ";
		$cmd = " select a.POEM_ID, a.POEM_NO_NAME, a.POEM_NO, a.PN_CODE, b.PN_NAME, a.ORG_ID, a.ORG_ID_1, a.ORG_ID_2, b.PN_CODE_NEW , b.LEVEL_NO
				 from PER_POS_EMP a, PER_POS_NAME b 
				 where a.POEM_STATUS = 1 and a.PN_CODE=b.PN_CODE and a.DEPARTMENT_ID = $DEPARTMENT_ID $search_condition
				 order by a.POEM_NO_NAME, a.POEM_NO+0 ";				  
		$count_temp = $db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		$err_count = 0;
		while ($data = $db_dpis->get_array()) {
			$POEM_ID = $data[POEM_ID];
			$POEM_NO_NAME = trim($data[POEM_NO_NAME]);
			$POEM_NO = trim($data[POEM_NO]);
			$PN_CODE = trim($data[PN_CODE]);
			$PN_CODE_NEW = trim($data[PN_CODE_NEW]);
			$PN_NAME = trim($data[PN_NAME]);
			$LEVEL_NO = trim($data[LEVEL_NO]);

			$cmd = " select PER_ID, PER_NAME, PER_SURNAME, LEVEL_NO as PER_LEVEL from PER_PERSONAL where POEM_ID=$POEM_ID and PER_TYPE=2 ";				  
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$CMD_LEVEL = $data2[PER_LEVEL];
			$PER_NAME = trim($data2[PER_NAME]) . " " . trim($data2[PER_SURNAME]);

			if (!$PN_CODE_NEW || !$LEVEL_NO) {
				$err_count++;
				$alert_err_text .= "	<tr class='$class' $onmouse_event><td align='center'>$err_count</td>
					<td align=\"center\">$POEM_NO_NAME$POEM_NO</td>
					<td>$PER_NAME ไม่มีรหัสใหม่</td>
					<td>$LEVEL_NO</td>
					<td>$PN_CODE $PN_NAME</td>
					<td align='center'>&nbsp;<a href='javascript:call_edit_personal($PER_ID);'><img src=\"images/b_edit.png\" border=\"0\" alt=\"แก้ไขข้อมูลบุคคล\"></a></td></tr>";
			}
		}	// while ($data = $db_dpis->get_array()) 

		if (!trim($alert_err_text)) { 
			$alert_success = "<table border='0' width='100%'><tr><td valign='bottom' align='center' class='label_alert'> ... เสร็จสิ้นการตรวจสอบการจัดระบบตำแหน่งลูกจ้างประจำ  ... </td></tr></table>";
		} // end if
	}	// 	if ($command == "CHECK") {
?>