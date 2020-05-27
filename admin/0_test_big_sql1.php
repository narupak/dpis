<?
	$time1 = time();
	
	$dpisdb_host	= 	"localhost";
	$dpisdb_name	= 	"ocsc";
	$dpisdb_user	= 	"dopa";
	$dpisdb_pwd	= 	"dopa";

	ini_set("max_execution_time", "1800");
	
//	session_cache_limiter("nocache");
//	session_cache_limiter("private");
//	session_start();
/*
	$database = "(DESCRIPTION =
												 (ADDRESS =
													 (PROTOCOL = TCP)
														 (HOST = $dpisdb_host)
														 (PORT = 1521)
													 )
												   (CONNECT_DATA = (SERVICE_NAME = $dpisdb_name))
												  )";
	$database = $dpisdb_host."/".$dpisdb_name;
	$conn = oci_pconnect($dpisdb_user, $dpisdb_pwd, 'localhost/ocsc'); 
	if (!$conn) {
		echo "connect fail!!!<br>";
	}
	
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

//	$cmd = "select * from PER_PERSONAL  where PER_NAME like 'สม%' ";
	$cmd = "select * from PER_PERSONAL  where PER_TYPE = 1 ";
	$stmt = oci_parse($conn, $cmd);
	oci_execute($stmt);
	$num_rows = oci_num_rows($stmt);

	echo "[$cmd]<br><br>";

	oci_free_statement($stmt);
	oci_close($conn);
*/	
	if (!function_exists('oci_pconnect')) {
    	echo "*** not exist function ***<br>";
	} else {
	    $toReturn = oci_pconnect('dopa', 'dopa', 'ocsc'); 
    	if ($testRes = @oci_parse($toReturn, 'select * from PER_PERSONAL')) 
	      if (@oci_execute($testRes)) 
    	    while ($data = @oci_fetch_array($testRes)) {
				for($i=0; $i < count($data); $i++)
		    		echo "".$data[$i].",";
				echo "<br>";
			}
		$num_rows = oci_num_rows($testRes);
	    oci_close($toReturn); 
	}
	ini_set("max_execution_time", 30);

	$time2 = time();
	$tdiff = $time2 - $time1;
	$s = $tdiff % 60;	// วินาที
	$m = floor($tdiff / 60);	// นาที
	$h = floor($m / 60);	// ชม.
	$m = $m % 60;	// นาที
	$show_lap = ($h?"$h ชม. ":"").($m?"$m นาที ":"")."$s วินาที";
	echo "<BR>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	echo "จำนวนข้อมูล $num_rows รายการ<br>";
	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	echo "เริ่ม:".date("d-m-Y h:i:s",$time1)." จบ:".date("d-m-Y h:i:s",$time2)." ใช้เวลา $show_lap [$tdiff]<br>";
	echo "<BR>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	echo "จบการทำงาน<br>";
?>