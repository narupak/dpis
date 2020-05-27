<?
$christian_era = (trim($DE_YEAR))? $DE_YEAR - 543 : $NOW_YEAR ; 
$budget_year =  $christian_era -  1 ."-10-01";

$tmp_LEVEL_STARTDATE = "" ; 
$list_old_dc_code = 0;
unset($arr_old_dc_code, $arr_old_dc_code_date);

// หาวันที่เริ่มต้นระดับปัจจุบัน
$cmd = "	select		min(POH_EFFECTIVEDATE) as PER_LEVEL_DATE
				from			PER_POSITIONHIS
				where		LEVEL_NO='$tmp_LEVEL_NO' and PER_ID=$tmp_PER_ID " ;
$db_dpis2->send_cmd($cmd);
$data2 = $db_dpis2->get_array();
$tmp_LEVEL_STARTDATE = substr(trim($data2[PER_LEVEL_DATE]), 0, 10);

// หาเครื่องราชฯ ที่เคยได้รับมาแล้ว
$cmd = "	select		PER_ID, DC_CODE, DE_DATE 
				from 			PER_DECOR a, PER_DECORDTL b 
				where		a.DE_ID=b.DE_ID and PER_ID=$tmp_PER_ID ";
$db_dpis2->send_cmd($cmd);
while ($data2 = $db_dpis2->get_array()) {
	$tmp_old_dc_code = trim($data2[DC_CODE]) + 0;
	if ($tmp_old_dc_code)
		$list_old_dc_code = (trim($list_old_dc_code))? ", $tmp_old_dc_code" : "$tmp_old_dc_code";
		$arr_old_dc_code[] = $tmp_old_dc_code;
		$arr_old_dc_code_date[$tmp_old_dc_code] =  substr($data2[DE_DATE], 0, 10);
		// คำนวนอายุตั้งแต่ที่ได้รับเครื่องราชฯ นี้
		$tmp_amount_date[$tmp_old_dc_code] = date_difference("$budget_year", $arr_old_dc_code_date[$tmp_old_dc_code], "full");
		$arr_dc_code_old_startdate[$tmp_old_dc_code] = explode(" ", $tmp_amount_date);
}
//echo "เครื่องราชฯ ที่เคยได้รับมาแล้ว ของ ";

// คำนวนอายุราชการ
$tmp_amount_date = date_difference("$budget_year", $tmp_PER_STARTDATE, "full");
$arr_per_startdate = explode(" ", $tmp_amount_date);
// คำนวนอายุระดับปัจจุบัน
$tmp_amount_date = date_difference("$budget_year", $tmp_LEVEL_STARTDATE, "full");
$arr_level_startdate = explode(" ", $tmp_amount_date);

/*
echo "$tmp_PER_ID ( $tmp_PER_NAME ) => $list_old_dc_code <br>";
echo " -> $tmp_LEVEL_NO || $budget_year - $tmp_PER_STARTDATE";
echo " => $tmp_amount_date || $tmp_date[0] $tmp_date[2] $tmp_date[4]";
echo " <br> - LEVEL_STARTDATE=$tmp_LEVEL_STARTDATE";
echo " || $list_old_dc_code";
*/



// 1=ชั้นสายสะพาย 
// 2=ชั้นต่ำกว่าสายสะพาย
// 3=เหรียญตรา
$count_arr_DC_TYPE = count(${"arr_DC_TYPE$tmp_LEVEL_NO"}) ;
for ($i=0; $i<$count_arr_DC_TYPE; $i++) {
		$dc_type_tmp = ${"arr_DC_TYPE$tmp_LEVEL_NO"}[$i];
		$dc_code_tmp = ${"arr_DC_CODE$tmp_LEVEL_NO"}[$i];
		$dc_code_old_tmp = ${"arr_DC_CODE_OLD$tmp_LEVEL_NO"}[$dc_type_tmp][$dc_code_tmp];
		$dcon_time1_tmp = ${"arr_DCON_TIME1$tmp_LEVEL_NO"}[$dc_type_tmp][$dc_code_tmp] + 0; 
		$dcon_time2_tmp = ${"arr_DCON_TIME2$tmp_LEVEL_NO"}[$dc_type_tmp][$dc_code_tmp] + 0; 
		$dcon_time3_tmp = ${"arr_DCON_TIME3$tmp_LEVEL_NO"}[$dc_type_tmp][$dc_code_tmp] + 0; 

//echo "arr_level_startdate[0] = " . $arr_level_startdate[0] . " == ". ${$name_arr_DCON_TIME2}[$dc_type_tmp][$dc_code_tmp] . "<br>";
		$privilege1 = $privilege2 = $privilege3 = $privilege4 = false;
/*	
echo " case $dc_type_tmp -> " . $dc_code_tmp . " => (" . $dc_code_old_tmp;
echo " '$arr_per_startdate[0]>=$dcon_time1_tmp' '$arr_level_startdate[0]>=$dcon_time2_tmp' '$dc_code_old_tmp' ' ";
echo $arr_dc_code_old_startdate[$dc_code_old_tmp][0] .">=". $dcon_time3_tmp . "' in ";
echo $arr_old_dc_code_date[$dc_code_old_tmp] .") " ;
*/
		if (in_array($dc_code_tmp, $arr_old_dc_code)) {
//			echo " พบว่าได้รับเครื่องราชฯ นี้แล้ว <br>";	
			break;
		}

		if ($dcon_time1_tmp <= 0 || $arr_per_startdate[0] >= $dcon_time1_tmp) {
//				echo "1. || ";	
				$privilege1 = true;
		}  
		if ($dcon_tim2_tmp <= 0 || $arr_level_startdate[0] >= $dcon_time2_tmp) {
//				echo "2. || ";
				$privilege2 = true;
		}  
		if (strlen($dc_code_old_tmp) <= 0 || in_array($dc_code_old_tmp, $arr_old_dc_code)) {
//				echo "3. || ";
				$privilege3 = true;
		} 
		if ($dcon_time3_tmp <= 0 || $arr_dc_code_old_startdate[$dc_code_old_tmp][0] >= $dcon_time3_tmp) {
//				echo "4. || ";
				$privilege4 = true;
		}  
		
		if ($DE_ID && $privilege1==true && $privilege2==true && $privilege3==true && $privilege4==true) {
			$cmd = " insert into PER_DECORDTL (DE_ID, DC_CODE, PER_ID, UPDATE_USER, UPDATE_DATE, PER_CARDNO)
				 values ($DE_ID, '$dc_code_tmp', $tmp_PER_ID, $SESS_USERID, '$UPDATE_DATE', $tmp_PER_CARDNO) ";
//echo "<br><font color=red>$cmd</font>";
			$db_dpis1->send_cmd($cmd);
		}
//echo "<br>";			
			
}		// end for 

//echo "=========================<br>";
?>










