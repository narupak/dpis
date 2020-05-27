<?
	include("../../php_scripts/connect_database.php");
	include("function_share.php");
	include("function_find_absent_day.php");
	
	if($DPISDB=="odbc"){
		$cmd = " select	 a.abs_id, a.per_id, per_name, per_surname, abs_startdate, abs_startperiod, abs_enddate, abs_endperiod, abs_day, ab_count
							from		(	
											(	
												PER_ABSENTHIS a 
												join PER_PERSONAL b on (a.PER_ID = b.PER_ID)  
											) join PER_ABSENTTYPE c on (a.AB_CODE=c.AB_CODE)
										)
							where	a.PER_ID < 5000
							order by	a.PER_ID ";
	}elseif($DPISDB=="oci8"){
		$cmd = " select		a.abs_id, a.per_id, per_name, per_surname, abs_startdate, abs_startperiod, abs_enddate, abs_endperiod, abs_day, ab_count
							 from			PER_ABSENTHIS a, PER_PERSONAL b, PER_ABSENTTYPE c
							 where		(a.PER_ID = b.PER_ID) and (a.AB_CODE=c.AB_CODE) and a.PER_ID < 5000
							 order by		a.PER_ID ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select	 a.abs_id, a.per_id, per_name, per_surname, abs_startdate, abs_startperiod, abs_enddate, abs_endperiod, abs_day, ab_count
							from		(	
											(	
												PER_ABSENTHIS a 
												join PER_PERSONAL b on (a.PER_ID = b.PER_ID)  
											) join PER_ABSENTTYPE c on (a.AB_CODE=c.AB_CODE)
										)
							where	a.PER_ID < 5000
							order by	a.PER_ID ";
	} // end if
	$count_abs = $db_dpis->send_cmd($cmd);
	echo "$cmd ($count_abs)<br>";
	$ab_count = 1;	// 1=นับวันหยุด  2=ไม่นับวันหยุด
	$chkSave = 0;	// คำนวนเฉย ๆ ไม่มีการ save
	$i = 0;
	while($data = $db_dpis->get_array()) {
		$data = array_change_key_case($data, CASE_LOWER);
		$abs_id = $data[abs_id];
		$per_id = $data[per_id];
		$ab_code = $data[ab_code];
		$abs_startdate = $data[abs_startdate];
		$abs_startperiod = $data[abs_startperiod];
		$abs_enddate = $data[abs_enddate];
		$abs_endperiod = $data[abs_endperiod];
		$abs_day = $data[abs_day];
		$ab_count = $data[ab_count];
		$days = find_absent_day($abs_startdate, $abs_startperiod, $abs_enddate, $abs_endperiod, $ab_count, $per_id, $chkSave);
		$i++;
		if ($abs_day==$days)
			echo "True.....i=".$i."==$per_id==>".$abs_startdate."[$abs_startperiod]-".$abs_enddate."[$abs_endperiod]==>".$days."<br>";
		else
			echo "False....i=".$i."==$per_id==>".$abs_startdate."[$abs_startperiod]-".$abs_enddate."[$abs_endperiod]==>".$days."($abs_day)<br>";
	}
?>