<?
	$time1 = time();
	include("../php_scripts/connect_database_dum.php");

	ini_set("max_execution_time", "18000");
	
//	session_cache_limiter("nocache");
//	session_cache_limiter("private");
//	session_start();

/*
	$cmd = "	   SELECT a.PER_ID, PER_NAME, PER_SURNAME, a.PN_CODE as PER_TITLE, 
									POH_ID, POH_EFFECTIVEDATE, b.MOV_CODE, POH_ENDDATE, POH_DOCNO, POH_DOCDATE, 
									POH_POS_NO, PM_CODE, b.LEVEL_NO, PL_CODE, b.PN_CODE, EP_CODE, PT_CODE, CT_CODE, 
									b.PV_CODE, AP_CODE, POH_ORGMGT, ORG_ID_1, ORG_ID_2, ORG_ID_3, POH_UNDER_ORG1, 
									POH_UNDER_ORG2, POH_ASS_ORG, POH_ASS_ORG1, POH_ASS_ORG2, POH_SALARY, 
									POH_SALARY_POS, POH_REMARK, POH_ORG1, POH_ORG2, POH_ORG3, POH_ORG_TRANSFER, 
									POH_ORG, POH_PM_NAME, POH_PL_NAME, POH_SEQ_NO, POH_LAST_POSITION, 
									POH_CMD_SEQ, POH_ISREAL, b.ES_CODE, POH_LEVEL_NO 
						FROM	PER_PERSONAL a, PER_POSITIONHIS b
						WHERE a.PER_ID=b.PER_ID  where PER_NAME like 'สม%' ";
*/
//	$cmd = "select * from PER_PERSONAL  where PER_NAME like 'สม%' ";
//	$cmd = "select * from PER_PERSONAL  where PER_TYPE = 1 ";
	$cmd = "select * from PER_PERSONAL";
	$count_data = $db_dpis->send_cmd($cmd);
	echo "[$cmd]<br><br>";

	ini_set("max_execution_time", 30);

	$time2 = time();
	$tdiff = $time2 - $time1;
	$s = $tdiff % 60;	// วินาที
	$m = floor($tdiff / 60);	// นาที
	$h = floor($m / 60);	// ชม.
	$m = $m % 60;	// นาที
	$show_lap = ($h?"$h ชม. ":"").($m?"$m นาที ":"")."$s วินาที";
	echo "<BR>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	echo "จำนวนข้อมูล $count_data รายการ<br>";
	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	echo "เริ่ม:".date("d-m-Y h:i:s",$time1)." จบ:".date("d-m-Y h:i:s",$time2)." ใช้เวลา $show_lap [$tdiff]<br>";
	echo "<BR>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	echo "จบการทำงาน<br>";
?>