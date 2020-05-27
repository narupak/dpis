<?
	include("../php_scripts/connect_database.php");
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	$UPDATE_DATE = date("Y-m-d H:i:s");

	$temp_date = explode("/", trim($START_DATE));
	$TEMP_START_DATE = ($temp_date)? ($temp_date[2]-543) ."-". $temp_date[1] ."-". $temp_date[0]. " 00:00:00" : ""; 
	$TMP_START_DATE = ($temp_date)? ($temp_date[2]) ."-". $temp_date[1] ."-". $temp_date[0]. " 00:00:00" : ""; 
	$temp_date = explode("/", trim($END_DATE));
	$TEMP_END_DATE = ($temp_date)? ($temp_date[2]-543) ."-". $temp_date[1] ."-". $temp_date[0] . " 23:59:59" : ""; 
	$TMP_END_DATE = ($temp_date)? ($temp_date[2]) ."-". $temp_date[1] ."-". $temp_date[0] . " 23:59:59" : ""; 

	if ($command=="CONFIRM") { 
		$cmd = " select BADGENUMBER, CHECKTIME, CHECKTYPE, VERIFYCODE, SENSORID from CHECKINOUT a, USERINFO b 
				 where a.USERID = b.USERID and CHECKTIME >= format('$TMP_START_DATE','yyyy-mm-dd hh:mm:ss') AND CHECKTIME <= format('$TMP_END_DATE','yyyy-mm-dd hh:mm:ss') 
				 order by BADGENUMBER, CHECKTIME ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
		while ( $data_att = $db_att->get_array() ) {
			$BADGENUMBER = $data_att[BADGENUMBER];
			$CHECKTIME = trim($data_att[CHECKTIME]);
			$CHECKTYPE = trim($data_att[CHECKTYPE]);
			$VERIFYCODE = $data_att[VERIFYCODE];
			$SENSORID = trim($data_att[SENSORID]);

			$cmd = " select PER_ID FROM PER_PERSONAL WHERE PER_OFFNO = '$BADGENUMBER' AND PER_STATUS = 1 ";
			$db_dpis->send_cmd($cmd);
//			echo $cmd;
//			$db_dpis->show_error();
			$data_dpis = $db_dpis->get_array();
			$PER_ID = $data_dpis[PER_ID];
			if (!$PER_ID && $TEMP_BADGENUMBER != $BADGENUMBER) {
				echo "รหัส $BADGENUMBER ไม่มีในระบบ<br>"; 
				$TEMP_BADGENUMBER = $BADGENUMBER;
			}

			$cmd = " select * FROM PER_TIME_ATTENDANCE WHERE PER_ID = $PER_ID AND TIME_STAMP = '$CHECKTIME' ";
			$count_data = $db_dpis->send_cmd($cmd);
//			echo $cmd;
//			$db_dpis->show_error();

			if (!$count_data){
				$cmd = " insert into PER_TIME_ATTENDANCE (PER_ID, TIME_STAMP, TA_CODE, UPDATE_USER, UPDATE_DATE) 
					 values ($PER_ID, '$CHECKTIME', '$SENSORID', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			}
		} // end while
// ยืนยันการมาปฏิบัติราชการ
		$cmd = " delete from PER_WORK_TIME where START_DATE >='$TEMP_START_DATE' AND END_DATE <= '$TEMP_END_DATE' ";
		$db_dpis->send_cmd($cmd);
		
		$cmd = " select a.PER_ID, min(TIME_STAMP) as TIME_STAMP, b.PER_NAME, b.PER_SURNAME from PER_TIME_ATTENDANCE a, PER_PERSONAL b 
				 where a.PER_ID = b.PER_ID and TIME_STAMP >='$TEMP_START_DATE' AND TIME_STAMP <= '$TEMP_END_DATE' 
				 group by a.PER_ID, substr(TIME_STAMP,1,10), b.PER_NAME, b.PER_SURNAME
				 order by a.PER_ID, substr(TIME_STAMP,1,10), b.PER_NAME, b.PER_SURNAME ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
//		echo $cmd;
		while ( $data = $db_dpis->get_array() ) {
			$PER_ID = $data[PER_ID];
			$TIME_STAMP = trim($data[TIME_STAMP]);
			$CHECK_DATE = substr($TIME_STAMP,0,10);
			$PER_NAME = trim($data[PER_NAME]);
			$PER_SURNAME = trim($data[PER_SURNAME]);

			$cmd = " select a.WC_CODE, b.WC_END FROM PER_WORK_CYCLEHIS a, PER_WORK_CYCLE b WHERE a.PER_ID = $PER_ID AND a.START_DATE <='$CHECK_DATE' AND a.END_DATE >= '$CHECK_DATE' AND a.WC_CODE = b.WC_CODE ";
			$db_dpis1->send_cmd($cmd);
//			echo $cmd;
//			$db_dpis1->show_error();
			$data1 = $db_dpis1->get_array();
			$WC_CODE = $data1[WC_CODE];
			$WC_END = $data1[WC_END];
			$TIME_STAMP_START = substr($TIME_STAMP,11,8); 
			$TIME_STAMP_END = substr($TIME_STAMP,0,11) . substr($WC_END,0,2) . ":" . substr($WC_END,2,2) . ":00"; 
			$WORK_DATE = substr($TIME_STAMP,0,10); 
			if (!$WC_CODE) echo "$PER_NAME $PER_SURNAME ไม่มีรอบการมาปฏิบัติราชการ<br>"; 

			$cmd = " select WL_CODE FROM PER_TIME_ATTENDANCE a, PER_TIME_ATT b WHERE a.PER_ID = $PER_ID AND a.TIME_STAMP = '$TIME_STAMP' AND a.TA_CODE = b.TA_CODE ";
			$db_dpis1->send_cmd($cmd);
//			echo $cmd;
//			$db_dpis1->show_error();
			$data1 = $db_dpis1->get_array();
			$WL_CODE = $data1[WL_CODE];

			$cmd = " select LATE_TIME FROM PER_WORK_LATE WHERE WL_CODE = '$WL_CODE' AND WC_CODE = '$WC_CODE' AND WORK_DATE = '$WORK_DATE' ";
			$db_dpis1->send_cmd($cmd);
//			echo $cmd;
//			$db_dpis1->show_error();
			$data1 = $db_dpis1->get_array();
			$LATE_TIME = $data1[LATE_TIME];
			$LATE_TIME = substr($LATE_TIME,0,2) . ":" . substr($LATE_TIME,2,2) . ":00";
			$ABSENT_FLAG = "NULL";
			if ($TIME_STAMP_START > $LATE_TIME) $ABSENT_FLAG = "3";

			$cmd = " select * FROM PER_WORK_TIME WHERE PER_ID = $PER_ID AND START_DATE = '$TIME_STAMP' ";
			$count_data = $db_dpis1->send_cmd($cmd);
//			$db_dpis1->show_error();

			if (!$count_data){
				$cmd = " select max(WT_ID) as max_id from PER_WORK_TIME ";
				$db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$data1 = array_change_key_case($data1, CASE_LOWER);
				$WT_ID = $data1[max_id] + 1;
		
				$cmd = " insert into PER_WORK_TIME (WT_ID, PER_ID, WL_CODE, WC_CODE, START_DATE, END_DATE, HOLIDAY_FLAG, ABSENT_FLAG, UPDATE_USER, UPDATE_DATE) 
					 values ($WT_ID, $PER_ID, '$WL_CODE', '$WC_CODE', '$TIME_STAMP', '$TIME_STAMP_END', NULL, $ABSENT_FLAG, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis1->send_cmd($cmd);
//				$db_dpis1->show_error();
			} 
		} // end while

//สร้างวันที่มาปฏิบัติราชการล่วงหน้า
		$cmd = " delete from PER_WORK_TIME where START_DATE >= '$TEMP_START_DATE' and END_DATE <= '$TEMP_END_DATE' and REMARK = 'สร้างข้อมูลอัตโนมัติ' ";
		$db_dpis->send_cmd($cmd);		
//		echo $cmd;
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
				$db_dpis1->send_cmd($cmd);
//				$db_dpis1->show_error();
				$data1 = $db_dpis1->get_array();
				$MAX_ID = $data1[MAX_ID] + 0;

				$cmd = " SELECT PER_ID, PER_TYPE, LEVEL_NO FROM PER_PERSONAL WHERE PER_STATUS = 1 ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
				while($data = $db_dpis->get_array()){
					$PER_ID = $data[PER_ID];
					$PER_TYPE = $data[PER_TYPE];
					$LEVEL_NO =  trim($data[LEVEL_NO]);
					$cmd = " select a.WC_CODE, b.WC_START, b.WC_END FROM PER_WORK_CYCLEHIS a, PER_WORK_CYCLE b WHERE a.PER_ID = $PER_ID AND a.START_DATE <='$TEMP_START_DATE' AND a.END_DATE >= '$TEMP_END_DATE' AND a.WC_CODE = b.WC_CODE ";
					$db_dpis1->send_cmd($cmd);
//				echo $cmd;
//				$db_dpis1->show_error();
			
					$data1 = $db_dpis1->get_array();
					$WC_CODE = $data1[WC_CODE];
					$WC_START = $data1[WC_START];
					$WC_END = $data1[WC_END];
					$TMP_STARTDATE = $TMP_DATE.' '.substr($WC_START,0,2) . ":" . substr($WC_START,2,2) . ":00"; 
					$TMP_ENDDATE = $TMP_DATE.' '.substr($WC_END,0,2) . ":" . substr($WC_END,2,2) . ":00"; 

					$cmd = " select * FROM PER_WORK_TIME WHERE PER_ID = $PER_ID AND substr(START_DATE,1,10) = '$TMP_DATE' ";
					$count_data = $db_dpis1->send_cmd($cmd);
//				$db_dpis1->show_error();

					if (!$count_data){
						$MAX_ID++;
						if ($PER_TYPE==1 && ($LEVEL_NO == '09' || $LEVEL_NO == '10' || $LEVEL_NO == '11' || $LEVEL_NO == 'O4' || $LEVEL_NO == 'K5' || $LEVEL_NO == 'D2' || $LEVEL_NO == 'M1' || $LEVEL_NO == 'M2')) $ABSENT_FLAG = "NULL";
						else $ABSENT_FLAG = 9;
						$cmd = " insert into PER_WORK_TIME (WT_ID, PER_ID, WL_CODE, WC_CODE, START_DATE, END_DATE, HOLIDAY_FLAG, ABSENT_FLAG, REMARK, UPDATE_USER, 
										  UPDATE_DATE) 
										  values ($MAX_ID, $PER_ID, '$WL_CODE', '$WC_CODE', '$TMP_STARTDATE', '$TMP_ENDDATE', NULL, $ABSENT_FLAG, 'สร้างข้อมูลอัตโนมัติ', $SESS_USERID, '$UPDATE_DATE') ";
						$db_dpis1->send_cmd($cmd);		
//					$db_dpis1->show_error();
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
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ยืนยันเวลาการมาปฏิบัติราชการ [$TEMP_START_DATE : $TEMP_END_DATE]");
	} // endif command==CONFIRM
?>