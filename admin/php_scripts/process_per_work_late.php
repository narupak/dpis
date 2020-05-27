<?
	include("../php_scripts/connect_database.php");
	include("php_scripts/session_start.php");

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$UPDATE_DATE = date("Y-m-d H:i:s");

	if ($command=="CONFIRM") { 

		$cmd = " delete from PER_WORK_LATE where WL_CODE = '$WL_CODE' and WC_CODE = '$WC_CODE' and WORK_DATE >= '$START_DATE' and 
						   WORK_DATE <= '$END_DATE' ";
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
	
		$TMP_STARTDATE = date("Y-m-d", mktime(0, 0, 0, $START_MONTH, $START_DAY, $START_YEAR));
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

				$cmd = " insert into PER_WORK_LATE (WL_CODE, WC_CODE, WORK_DATE, LATE_TIME, LATE_REMARK, UPDATE_USER, UPDATE_DATE) 
						 values ('$WL_CODE', '$WC_CODE', '$TMP_STARTDATE', '$LATE_TIME', NULL, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);		
//				$db_dpis->show_error();
			} // end if
			$TMP_STARTDATE = date("Y-m-d", mktime(0, 0, 0, $START_MONTH, ($START_DAY + 1), $START_YEAR));
			$arr_temp = explode("-", $TMP_STARTDATE);
			$START_DAY = $arr_temp[2];
			$START_MONTH = $arr_temp[1];
			$START_YEAR = $arr_temp[0];
		} // end while
		
	} // endif command==CONFIRM
?>