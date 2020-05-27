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
		$cmd = " select a.POS_ID, a.POS_NO_NAME, a.POS_NO, a.PL_CODE, b.PL_NAME, a.ORG_ID, a.ORG_ID_1, a.ORG_ID_2, a.PT_CODE, b.PL_TYPE, 
				 a.CL_NAME, a.POS_SALARY, b.PL_CODE_NEW 
				 from PER_POSITION a, PER_LINE b 
				 where a.POS_STATUS = 1 and a.PL_CODE=b.PL_CODE and a.DEPARTMENT_ID = $DEPARTMENT_ID $search_condition
				 order by a.POS_ID ";				  
		$count_temp = $db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		$err_count = 0;
		while ($data = $db_dpis->get_array()) {
			$POS_ID = $data[POS_ID];
			$POS_NO_NAME = trim($data[POS_NO_NAME]);
			$POS_NO = trim($data[POS_NO]);
			$cmd = " select PER_ID, PER_NAME, PER_SURNAME, LEVEL_NO as PER_LEVEL from PER_PERSONAL where POS_ID=$POS_ID and PER_TYPE=1 ";				  
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PL_CODE = trim($data[PL_CODE]);
			$PL_CODE_NEW = trim($data[PL_CODE_NEW]);
			$PL_NAME = trim($data[PL_NAME]);
			$PL_TYPE = $data[PL_TYPE];
			$CL_NAME = trim($data[CL_NAME]);
			$TMP_PER_ID = trim($data2[PER_ID]);
			$CMD_LEVEL = $data2[PER_LEVEL];
			$PER_NAME = trim($data2[PER_NAME]) . " " . trim($data2[PER_SURNAME]);
			$TMP_PER_ID = (trim($TMP_PER_ID))? $TMP_PER_ID : $POS_ID+900000000;

			$cmd = " select CL_NAME from PER_LINE where trim(PL_CODE)='$PL_CODE_NEW' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$CL_NAME_NEW = trim($data2[CL_NAME]);
			
			if ($TMP_PER_ID > 900000000){
				$cmd = " select LEVEL_NO_MIN, LEVEL_NO_MAX from PER_CO_LEVEL where CL_NAME='$CL_NAME' ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$CMD_LEVEL = trim($data2[LEVEL_NO_MAX]);
			}	
					
			$cmd = " select NEW_LEVEL_NO from PER_MAP_POS where LEVEL_NO='$CMD_LEVEL' and PL_TYPE=$PL_TYPE ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$LEVEL_NO = $data2[NEW_LEVEL_NO];

			if ($PL_TYPE==2 || $PL_TYPE==3 || $PL_TYPE==5) 
				if ($CMD_LEVEL=="08" && $PT_CODE=="31") $LEVEL_NO = "D1";
				elseif ($CMD_LEVEL=="09" && $PT_CODE=="32") $LEVEL_NO = "D2";

			if ($PL_TYPE==0) {
				$err_count++;
				$alert_err_text .= "	<tr class='$class' $onmouse_event><td align='center'>$err_count</td><td>$CL_NAME</td>
					<td align=\"center\">$POS_NO_NAME$POS_NO</td>
					<td>$PER_NAME</td>
					<td>$CMD_LEVEL</td>
					<td>$PL_CODE $PL_NAME</td>
					<td align='center'>&nbsp;<a href='javascript:call_edit_personal($PER_ID);'><img src=\"images/b_edit.png\" border=\"0\" alt=\"แก้ไขข้อมูลบุคคล\"></a></td></tr>";
			} elseif (!$CL_NAME_NEW) {
				$err_count++;
				$alert_err_text .= "	<tr class='$class' $onmouse_event><td align='center'>$err_count</td><td>$CL_NAME</td>
					<td align=\"center\">$POS_NO_NAME$POS_NO</td>
					<td>$PER_NAME ไม่มีรหัสใหม่</td>
					<td>$CMD_LEVEL</td>
					<td>$PL_CODE $PL_NAME</td>
					<td align='center'>&nbsp;<a href='javascript:call_edit_personal($PER_ID);'><img src=\"images/b_edit.png\" border=\"0\" alt=\"แก้ไขข้อมูลบุคคล\"></a></td></tr>";
			}
		}	// while ($data = $db_dpis->get_array()) 

		if (!trim($alert_err_text)) { 
			$alert_success = "<table border='0' width='100%'><tr><td valign='bottom' align='center' class='label_alert'> ... เสร็จสิ้นการตรวจสอบการจัดคนลงตาม พรบ.ใหม่ ... </td></tr></table>";
		} // end if
	}	// 	if ($command == "CHECK") {
?>