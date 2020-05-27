<?
	include("../php_scripts/connect_database.php");
	include("php_scripts/session_start.php");

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$UPDATE_DATE = date("Y-m-d H:i:s");

	if ($command=="CONFIRM") { 

		$cmd = " delete from PER_WORK_TIME where START_DATE >= '$START_DATE' and END_DATE <= '$END_DATE' and REMARK = 'สร้างข้อมูลอัตโนมัติ' ";
		$db_dpis->send_cmd($cmd);		
//		$db_dpis->show_error();

		$arr_temp = explode("/", $START_DATE);
		$START_DAY = $arr_temp[0];
		$START_MONTH = $arr_temp[1];
		$START_YEAR = $arr_temp[2] - 543;

		$arr_temp = explode("/", $END_DATE);
		$END_DAY = $arr_temp[0];
		$END_MONTH = $arr_temp[1];
		$END_YEAR = $arr_temp[2] - 543;
	
		$TMP_DATE = date("Y-m-d", mktime(0, 0, 0, $START_MONTH, $START_DAY, $START_YEAR));
		while($START_YEAR!=$END_YEAR || $START_MONTH!=$END_MONTH || $START_DAY!=$END_DAY){
			$DAY_NUM = date("w", mktime(0, 0, 0, $START_MONTH, $START_DAY, $START_YEAR));
	
			if($DPISDB=="odbc") 
				$cmd = " select HOL_NAME from PER_HOLIDAY where LEFT(HOL_DATE, 10)='$START_YEAR-$START_MONTH-$START_DAY' ";
			elseif($DPISDB=="oci8") 
				$cmd = " select HOL_NAME from PER_HOLIDAY where SUBSTR(HOL_DATE, 1, 10)='$START_YEAR-$START_MONTH-$START_DAY' ";
			elseif($DPISDB=="mysql")
				$cmd = " select HOL_NAME from PER_HOLIDAY where LEFT(HOL_DATE, 10)='$START_YEAR-$START_MONTH-$START_DAY' ";
			$IS_HOLIDAY = $db_dpis->send_cmd($cmd);
		
			if($DAY_NUM > 0 && $DAY_NUM < 6 && !$IS_HOLIDAY) {

				$cmd = " SELECT max(WT_ID) as MAX_ID FROM PER_WORK_TIME ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$MAX_ID = $data2[MAX_ID] + 0;

				$cmd = " SELECT PER_ID FROM PER_PERSONAL WHERE LEVEL_NO > '08' AND LEVEL_NO < '12' AND PER_STATUS = 1 ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
				while($data = $db_dpis->get_array()){
					$PER_ID = $data[PER_ID];
					$cmd = " SELECT WC_CODE FROM PER_WORK_CYCLEHIS where PER_ID = $PER_ID and START_DATE >= '$START_DATE' and END_DATE <= '$END_DATE' ";
					$count_data = $db_dpis2->send_cmd($cmd);
//					$db_dpis2->show_error();
			
					if ($count_data) {
						$data2 = $db_dpis2->get_array();
						$WC_CODE = $data2[WC_CODE];

						$cmd = " SELECT WC_START, WC_END FROM PER_WORK_CYCLE WHERE WC_CODE = '$WC_CODE' ";
						$db_dpis2->send_cmd($cmd);
//						$db_dpis2->show_error();
						$data2 = $db_dpis2->get_array();
						$WC_START = $data2[WC_START];
						$WC_END = $data2[WC_END];
						$TMP_STARTDATE = $TMP_DATE.' '.$WC_START;
						$TMP_ENDDATE = $TMP_DATE.' '.$WC_END;

						$MAX_ID++;
						$cmd = " insert into PER_WORK_TIME (WT_ID, PER_ID, WC_CODE, START_DATE, END_DATE, HOLIDAY_FLAG, ABSENT_FLAG, REMARK, UPDATE_USER, 
										  UPDATE_DATE) 
										  values ($MAX_ID, $PER_ID, '$WC_CODE', '$TMP_STARTDATE', '$TMP_ENDDATE', NULL, NULL, 'สร้างข้อมูลอัตโนมัติ', $SESS_USERID, '$UPDATE_DATE') ";
						$db_dpis2->send_cmd($cmd);		
//					$db_dpis2->show_error();
					} // end if
				} // end while
			} // end if
			$TMP_DATE = date("Y-m-d", mktime(0, 0, 0, $START_MONTH, ($START_DAY + 1), $START_YEAR));
			$arr_temp = explode("-", $TMP_DATE);
			$TMP_STARTDATE = $TMP_DATE.' '.$WC_START;
			$TMP_ENDDATE = $TMP_DATE.' '.$WC_END;
			$START_DAY = $arr_temp[2];
			$START_MONTH = $arr_temp[1];
			$START_YEAR = $arr_temp[0];
		} // end while
		
	} // endif command==CONFIRM
?>