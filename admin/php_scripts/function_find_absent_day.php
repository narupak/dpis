<?
function find_absent_day($STARTDATE, $STARTPERIOD, $ENDDATE, $ENDPERIOD, $AB_COUNT, $PER_ID, $chkSave) {
	global $DPISDB, $dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd;
	
	$db_dpis_spec = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$search_approve ="";
	if($linkfrom=="personal_absenthis"){
		$search_approve =" and APPROVE_FLAG=2";		//ดึงมาเฉพาะอันที่ ไม่ได้ถูก ไม่อนุญาต มา
	}

	//	นับจำนวนวันลา โดยไม่รวมวันหยุดเสาร์ - อาทิตย์ และวันหยุดราชการ (table) X
	//วันเริ่มต้น
	$STARTDATE = str_replace("-","/", $STARTDATE);
	$arr_temp = explode("/", $STARTDATE);
	if ((int)$arr_temp[2] > 100) {	// d/m/y
		if ((int)$arr_temp[2] > 2300) {	// เป็นปี พ.ศ	.
			$START_DAY = $arr_temp[0];
			$START_MONTH = $arr_temp[1];
			$START_YEAR = $arr_temp[2] - 543;
		} else {	// เป็นปี ค.ศ	.
			$START_DAY = $arr_temp[0];
			$START_MONTH = $arr_temp[1];
			$START_YEAR = $arr_temp[2];
		}
	} else {	// y/m/d
		if ((int)$arr_temp[0] > 2300) {	// เป็นปี พ.ศ	.
			$START_DAY = $arr_temp[2];
			$START_MONTH = $arr_temp[1];
			$START_YEAR = $arr_temp[0] - 543;
		} else {	// เป็นปี ค.ศ	.
			$START_DAY = $arr_temp[2];
			$START_MONTH = $arr_temp[1];
			$START_YEAR = $arr_temp[0];
		}
	}
	$STARTDATE = $START_YEAR ."-". $START_MONTH ."-". $START_DAY;
	$STARTDATE_ce_era = $START_YEAR ."-". $START_MONTH ."-". $START_DAY;
	
	//ถึงวันที่
	$ENDDATE = str_replace("-","/", $ENDDATE);
	$arr_temp = explode("/", $ENDDATE);
	if ((int)$arr_temp[2] > 100) {	// d/m/y
		if ((int)$arr_temp[2] > 2300) {	// เป็นปี พ.ศ	.
			$END_DAY = $arr_temp[0];
			$END_MONTH = $arr_temp[1];
			$END_YEAR = $arr_temp[2] - 543;
		} else {	// เป็นปี ค.ศ	.
			$END_DAY = $arr_temp[0];
			$END_MONTH = $arr_temp[1];
			$END_YEAR = $arr_temp[2];
		}
	} else {	// y/m/d
		if ((int)$arr_temp[0] > 2300) {	// เป็นปี พ.ศ	.
			$END_DAY = $arr_temp[2];
			$END_MONTH = $arr_temp[1];
			$END_YEAR = $arr_temp[0] - 543;
		} else {	// เป็นปี ค.ศ	.
			$END_DAY = $arr_temp[2];
			$END_MONTH = $arr_temp[1];
			$END_YEAR = $arr_temp[0];
		}
	}
	$ENDDATE = $END_YEAR ."-". $END_MONTH ."-". $END_DAY;
	$ENDDATE_ce_era = $END_YEAR ."-". $END_MONTH ."-". $END_DAY;	

//	echo "st ($STARTDATE_ce_era) - en ($ENDDATE_ce_era)<br>";
	if(!$INVALID_STARTDATE && !$INVALID_ENDDATE){
		//$ABSENT_DAY = 1;
		$TMP_STARTDATE = date("Y-m-d", mktime(0, 0, 0, $START_MONTH, $START_DAY, $START_YEAR));

		if($AB_COUNT == 2){		//ไม่นับวันหยุดเท่านั้น***		// PERIOD 1 = ครึ่งเช้า / 2 = ครึ่งบ่าย / 3 = ทั้งวัน
			$DAY_START_NUM = date("w", mktime(0, 0, 0, $START_MONTH, $START_DAY, $START_YEAR));
			$DAY_END_NUM = date("w", mktime(0, 0, 0, $END_MONTH, $END_DAY, $END_YEAR));
			if(($DAY_START_NUM == 0 || $DAY_START_NUM == 6)  && ($DAY_END_NUM == 0 || $DAY_END_NUM == 6)){			// เริ่มต้น / สิ้นสุด เป็นวันเสาร์ - อาทิตย์  (หานอก loop)
				$ABSENT_START_DAY = 0;
			}else if(($DAY_START_NUM == 0 || $DAY_START_NUM == 6)  && ($DAY_END_NUM != 0 || $DAY_END_NUM != 6)){	// เริ่มต้น เป็นวันเสาร์ - อาทิตย์  / สิ้นสุด เป็นวันธรรมดา (หานอก loop)
				$ABSENT_START_DAY = 0;
			}else if(($DAY_START_NUM != 0 || $DAY_START_NUM != 6)  && ($DAY_END_NUM == 0 || $DAY_END_NUM == 6)){	// เริ่มต้น  เป็นวันธรรมดา / สิ้นสุด เป็นวันเสาร์ - อาทิตย์  (หานอก loop)
				$ABSENT_START_DAY = 0;	
			}else if(($DAY_START_NUM != 0 && $DAY_START_NUM != 6)  && ($DAY_END_NUM != 0 && $DAY_END_NUM != 6)){	 // เริ่มต้น / สิ้นสุด ไม่เป็นวันเสาร์ - อาทิตย์  (หานอก loop)
//				$ABSENT_START_DAY = 1;	// ลองเอาออกก่อน ใน loop นับวันมันนับทุกวันอยู่แล้ว และ เช็ควันหยุดอยู่ใน loop อยู่แล้ว ไม่รู้ว่าส่วนนี้ทำทำไม
				$ABSENT_START_DAY = 0;
			}
			
			// หาวันเริ่มต้นเป็นวันหยุดหรือไม่ ?
			if($DPISDB=="odbc") 
				$cmd = " select HOL_NAME from PER_HOLIDAY where LEFT(HOL_DATE, 10)='$START_YEAR-$START_MONTH-$START_DAY' ";
			elseif($DPISDB=="oci8") 
				$cmd = " select HOL_NAME from PER_HOLIDAY where SUBSTR(HOL_DATE, 1, 10)='$START_YEAR-$START_MONTH-$START_DAY' ";
			elseif($DPISDB=="mysql")
				$cmd = " select HOL_NAME from PER_HOLIDAY where LEFT(HOL_DATE, 10)='$START_YEAR-$START_MONTH-$START_DAY' ";
			$IS_HOLIDAY_START = $db_dpis_spec->send_cmd($cmd);
			
			// หาวันสิ้นสุดเป็นวันหยุดหรือไม่ ?
			if($DPISDB=="odbc") 
				$cmd = " select HOL_NAME from PER_HOLIDAY where LEFT(HOL_DATE, 10)='$END_YEAR-$END_MONTH-$END_DAY' ";
			elseif($DPISDB=="oci8") 
				$cmd = " select HOL_NAME from PER_HOLIDAY where SUBSTR(HOL_DATE, 1, 10)='$END_YEAR-$END_MONTH-$END_DAY' ";
			elseif($DPISDB=="mysql")
				$cmd = " select HOL_NAME from PER_HOLIDAY where LEFT(HOL_DATE, 10)='$END_YEAR-$END_MONTH-$END_DAY' ";
			$IS_HOLIDAY_END = $db_dpis_spec->send_cmd($cmd);
			if($IS_HOLIDAY_START || $IS_HOLIDAY_END){		// ถ้าวันเริ่ม / สิ้นสุดเป็นวันหยุดราชการ  ก็ไม่นับตรงนี้
				$ABSENT_START_DAY = 0;
			}

			// หักครึ่งวัน	// PERIOD 1 = ครึ่งเช้า / 2 = ครึ่งบ่าย / 3 = ทั้งวัน
			// คิดคำนวณครึ่งวัน กรณีนี้ไม่นับครึ่งวันเพราะวันเริ่มต้น / สิ้นสุดเป็นวันหยุดอยู่แล้ว ไม่เอามาคิด	
			// ถ้า ว / ด / ป เริ่มและสิ้นสุดเป็นวันเดียวกันและเป็นครึ่งวัน ทั้งเช้า / บ่ายไม่ต้องหัก 0.5
            $period_err = 0;
            $ABSENT_2_STARTPERIOD = 0;
            $ABSENT_2_ENDPERIOD = 0;
			if(trim("$START_YEAR-$START_MONTH-$START_DAY")==trim("$END_YEAR-$END_MONTH-$END_DAY")){
				if($STARTPERIOD == 1 && $ENDPERIOD == 2){		// เริ่มต้น เป็นครึ่งเช้า และสิ้นสุด เป็นครึ่งบ่าย
                	$period_err = 1;	//	"วันเดียวกัน เริ่มครึ่งเช้า สิ้นสุดครึ่งบ่าย ถือเป็น 1 วัน โปรดแก้ไขเป็น 'ทั้งวัน'";
				} else if($STARTPERIOD == 2 && $ENDPERIOD == 1){		// เริ่มต้นและสิ้นสุด เป็นครึ่งวันทั้งคู่  [ให้ตัวใดตัวหนึ่งเป็น 0.5]
                	$period_err = 2;	//	"วันเดียวกัน เริ่มครึ่งบ่าย สิ้นสุดครึ่งเช้า ผิดรูปแบบ โปรดแก้ไขแก้ไข";
				} else if($STARTPERIOD != 3 && $ENDPERIOD != 3){		// เริ่มต้นและสิ้นสุด เป็นครึ่งวันทั้งคู่ และเท่ากัน ก็คือ ครึ่งวัน เช้าหรือบ่าย  [ให้ตัวใดตัวหนึ่งเป็น 0.5]
                    $ABSENT_2_STARTPERIOD = 0.5;
				}else{
					if($STARTPERIOD == 3 && ($ENDPERIOD == 1 || $ENDPERIOD == 2))		
                    	$period_err = 3; // "วันเดียวกัน เริ่มทั้งวัน สิ้นสุดครึ่งบ่าย ถือเป็น 1 วัน โปรดแก้ไขสิ้นสุดเป็น 'ทั้งวัน'";
					if($ENDPERIOD == 3 && ($STARTPERIOD == 1 || $STARTPERIOD == 2))	
                    	$period_err = 4; // "วันเดียวกัน เริ่มครึ่งเช้า สิ้นสุดทั้งวัน ถือเป็น 1 วัน โปรดแก้ไขเริ่มเป็น 'ทั้งวัน'";
				}
			}else{
				if($STARTPERIOD == 1){
					$period_err = 5;	// "เริ่มวันแรกเป็นครึ่งเช้า เว้นบ่าย ไม่ถูกต้อง โปรดทำแยกเป็นลาอีก 1 รายการ";
				} else if($STARTPERIOD == 2){		//  เริ่มวันแรกครึ่งบ่าย
					if(($DAY_START_NUM == 0 || $DAY_START_NUM == 6) || $IS_HOLIDAY_START){	// ถ้าเป็นวันหยุดราชการหรือนักขัตฤกษ์ ไม่นับวัน
						$ABSENT_2_STARTPERIOD = 0;
					}else{  // case ปกติ วันธรรมดา
						$ABSENT_2_STARTPERIOD = 0.5;
					}
				}
				if($ENDPERIOD == 2){
					$period_err = 6;	// "สิ้นสุดจบที่ครึ่งบ่าย เว้นเช้า ไม่ถูกต้อง โปรดทำแยกเป็นลาอีก 1 รายการ";
				} else if($ENDPERIOD == 1){
					if(($DAY_END_NUM == 0 || $DAY_END_NUM == 6) || $IS_HOLIDAY_END){	// ถ้าเป็นวันหยุดราชการหรือนักขัตฤกษ์ ไม่นับวัน
						$ABSENT_2_ENDPERIOD = 0;
					}else{  // case ปกติ วันธรรมดา
						$ABSENT_2_ENDPERIOD = 0.5;
					}
				}
			}
		}else if($AB_COUNT == 1){		// นับวันหยุดเท่านั้น
			$ABSENT_START_DAY = 1; 
			// ถ้า ว / ด / ป เริ่มและสิ้นสุดเป็นวันเดียวกันและเป็นครึ่งวัน ทั้งเช้า / บ่ายไม่ต้องหัก 0.5
            $period_err = "";
            $ABSENT_2_STARTPERIOD = 0;
            $ABSENT_2_ENDPERIOD = 0;
			if(trim("$START_YEAR-$START_MONTH-$START_DAY")==trim("$END_YEAR-$END_MONTH-$END_DAY")){
				if($STARTPERIOD == 1 && $ENDPERIOD == 2){		// เริ่มต้น เป็นครึ่งเช้า และสิ้นสุด เป็นครึ่งบ่าย
                	$period_err = 1;	// "วันเดียวกัน เริ่มครึ่งเช้า สิ้นสุดครึ่งบ่าย ถือเป็น 1 วัน\nโปรดแก้ไขเป็น 'ทั้งวัน'";
				} else if($STARTPERIOD == 2 && $ENDPERIOD == 1){		// เริ่มต้นและสิ้นสุด เป็นครึ่งวันทั้งคู่  [ให้ตัวใดตัวหนึ่งเป็น 0.5]
                	$period_err = 2;	// "วันเดียวกัน เริ่มครึ่งบ่าย สิ้นสุดครึ่งเช้า ผิดรูปแบบ\nโปรดแก้ไขให้ถูกต้อง";
				} else if($STARTPERIOD != 3 && $ENDPERIOD != 3){		// เริ่มต้นและสิ้นสุด เป็นครึ่งวันทั้งคู่ และเท่ากัน ก็คือ ครึ่งวัน เช้าหรือบ่าย  [ให้ตัวใดตัวหนึ่งเป็น 0.5]
                    $ABSENT_2_STARTPERIOD = 0.5;
				}else{
					if($STARTPERIOD == 3 && ($ENDPERIOD == 1 || $ENDPERIOD == 2))		
                    	$period_err = 3;	// "วันเดียวกัน เริ่มทั้งวัน สิ้นสุดครึ่งบ่าย ถือเป็น 1 วัน\nโปรดแก้ไขสิ้นสุดเป็น 'ทั้งวัน'";
					if($ENDPERIOD == 3 && ($STARTPERIOD == 1 || $STARTPERIOD == 2))	
                    	$period_err = 4;	// "วันเดียวกัน เริ่มครึ่งเช้า สิ้นสุดทั้งวัน ถือเป็น 1 วัน\nโปรดแก้ไขเริ่มเป็น 'ทั้งวัน'";
				}
			} else {	// วันไม่เท่ากัน
				if($STARTPERIOD == 1){		// เริ่มวันแรกเป็นครึ่งเช้า เว้นบ่าย ไม่ถูกต้อง โปรดทำแยกเป็นลาอีก 1 รายการ
					$period_err = 5;	// "เริ่มวันแรกเป็นครึ่งเช้า เว้นบ่าย ไม่ถูกต้อง\nโปรดทำแยกเป็นลาอีก 1 รายการ";
				} else if($ENDPERIOD == 2){		// สิ้นสุด เป็นครึ่งวันหลัง
					$period_err = 6;	// "สิ้นสุดจบที่ครึ่งบ่าย เว้นเช้า ไม่ถูกต้อง โปรดทำแยกเป็นลาอีก 1 รายการ";
				} else {
                	if($STARTPERIOD == 2){		// เริ่มต้น เป็นครึ่งวันหลัง
                    	$ABSENT_2_STARTPERIOD = 0.5;	
					}
                    if($ENDPERIOD == 1){		// สิ้นสุด เป็นครึ่งวันแรก
                    	$ABSENT_2_ENDPERIOD = 0.5;
					}
				}            
            }
		}
		
        $ABSENT_START_DAY = 0;	// loop ต่อไปนี้นับใหม่หมดทุกวันแล้ว ไม่จำเป็นต้องใช้ ABSENT_START_DAY อีก
		$TMP_ENDDATE = date("Y-m-d", mktime(0, 0, 0, $END_MONTH, ($END_DAY + 1), $END_YEAR));
		$arr_temp = explode("-", $TMP_ENDDATE);
		$END_DAY1 = $arr_temp[2];
		$END_MONTH1 = $arr_temp[1];
		$END_YEAR1 = $arr_temp[0];
		while($START_YEAR!=$END_YEAR1 || $START_MONTH!=$END_MONTH1 || $START_DAY!=$END_DAY1){
//			echo "START_YEAR(".$START_YEAR.")!=END_YEAR(".$END_YEAR1.") || START_MONTH(".$START_MONTH.")!=END_MONTH(".$END_MONTH1.") || START_DAY(".$START_DAY.")!=END_DAY(".$END_DAY1.")<br>";
			$DAY_NUM = date("w", mktime(0, 0, 0, $START_MONTH, $START_DAY, $START_YEAR));

			if($AB_COUNT == 2){		//ไม่นับวันหยุดเท่านั้น***
				if($DPISDB=="odbc") 
					$cmd = " select HOL_NAME from PER_HOLIDAY where LEFT(HOL_DATE, 10)='$START_YEAR-$START_MONTH-$START_DAY' ";
				elseif($DPISDB=="oci8") 
					$cmd = " select HOL_NAME from PER_HOLIDAY where SUBSTR(HOL_DATE, 1, 10)='$START_YEAR-$START_MONTH-$START_DAY' ";
				elseif($DPISDB=="mysql")
					$cmd = " select HOL_NAME from PER_HOLIDAY where LEFT(HOL_DATE, 10)='$START_YEAR-$START_MONTH-$START_DAY' ";
				$IS_HOLIDAY = $db_dpis_spec->send_cmd($cmd);

				if($DAY_NUM > 0 && $DAY_NUM < 6 && !$IS_HOLIDAY) $ABSENT_DAY++;
//				echo "ไม่นับวันหยุด ($ABSENT_DAY)<br>";
			}elseif($AB_COUNT == 1){	//นับ
				$ABSENT_DAY++;
//				echo "นับวันหยุด ($ABSENT_DAY)<br>";
			} // end if

			$TMP_STARTDATE = date("Y-m-d", mktime(0, 0, 0, $START_MONTH, ($START_DAY + 1), $START_YEAR));
			$arr_temp = explode("-", $TMP_STARTDATE);
			$START_DAY = $arr_temp[2];
			$START_MONTH = $arr_temp[1];
			$START_YEAR = $arr_temp[0];
		} // end while

		// สรุปรวมวันสุดท้าย //
		$ABSENT_DAY = ($ABSENT_DAY + $ABSENT_START_DAY);		

		// หักครึ่งวัน	// PERIOD 1 = ครึ่งเช้า / 2 = ครึ่งบ่าย / 3 = ทั้งวัน	//ถ้าไม่นับวันหยุด ให้ check ว่าวันที่เริ่มต้น / สิ้นสุดที่ลาครึ่งวัน(เช้า/บ่าย)นั้น เป็นวันหยุดราชการ/ เสาร์-อาทิตย์? ถ้าเป็นไม่ต้องเอามาคิด
		$ABSENT_DAY = ($ABSENT_DAY -  ($ABSENT_2_STARTPERIOD + $ABSENT_2_ENDPERIOD));

		/*** น่าจะผิด
		if($STARTPERIOD == 2 && $ENDPERIOD == 1) $ABSENT_DAY -= 0.5;
		if($STARTPERIOD != 3) $ABSENT_DAY -= 0.5;
		if($ENDPERIOD != 3 && $TMP_STARTDATE != $ENDDATE) $ABSENT_DAY -= 0.5;
		if($STARTPERIOD == 3 && $ENDPERIOD != 3 && $TMP_STARTDATE == $ENDDATE) $ABSENT_DAY -= 0.5;
		***/
		//$ABSENT_DAY = $ABSENT_DAY ."+".$STARTPERIOD."&&".$ENDPERIOD;
	} // end if

	// ======================================================
	// ===== START process เช็คว่ามีการลาซ้ำกับวันที่เคยลาไปแล้วหรือไม่ =====
	$count_confirm = $count_duplicate_date = 0;
	if ($ABS_ID) {
		if ($DPISDB == "odbc")		$absid_condition = " and (ABS_ID<>$ABS_id) ";
		elseif ($DPISDB == "oci8")	$absid_condition = " and (ABS_ID!=$ABS_id) ";
		elseif($DPISDB=="mysql") $absid_condition = " and (ABS_ID!=$ABS_id) ";
	}
	if ($STARTPERIOD == 1) {
		$startperiod_condition1 = " (ABS_STARTPERIOD in (1,3)) ";
		$startperiod_condition2 = " (ABS_ENDPERIOD in (1,3)) ";
	} elseif ($STARTPERIOD == 2) {
		$startperiod_condition1 = " (ABS_STARTPERIOD in (2, 3)) ";
		$startperiod_condition2 = " (ABS_ENDPERIOD in (2, 3)) ";
	} elseif ($STARTPERIOD == 3) { 
		$startperiod_condition1 = " (ABS_STARTPERIOD<=3) ";
		$startperiod_condition2 = " (ABS_ENDPERIOD<=3) ";
	}
	if ($ENDPERIOD == 1) {
		$endperiod_condition1 = " (ABS_STARTPERIOD in (1, 3)) ";
		$endperiod_condition2 = " (ABS_ENDPERIOD in (1, 3)) ";
	} elseif ($ENDPERIOD == 2) {
		$endperiod_condition1 = " (ABS_STARTPERIOD in (2, 3)) ";
		$endperiod_condition2 = " (ABS_ENDPERIOD in (2, 3)) ";
	} elseif ($ENDPERIOD == 3) {	
		$endperiod_condition1 = " (ABS_STARTPERIOD<=3) ";
		$endperiod_condition2 = " (ABS_ENDPERIOD<=3) ";
	}

	if ($chkSave == 1 || $chkSave == 2) {
		if ($chkSave == 1) {
			// ===== เช็คว่ามีการเพิ่มการลาในข้อมูลเดือนที่ยืนยันแล้วหรือไม่ =====	
//			$search_abs_startdate = (substr($STARTDATE, -4) - 543) ."-". substr($STARTDATE, 3, 2);
//			$search_abs_enddate = (substr($ENDDATE, -4) - 543) ."-". substr($ENDDATE, 3, 2);	
//			$cmd = "select ABS_MONTH from PER_ABSENT_CONF where ABS_MONTH like '$search_abs_startdate' or ABS_MONTH like '$search_abs_enddate' ";
//			$count_confirm = $db_dpis->send_cmd($cmd);
		}

		$arr_temp = explode("-", $STARTDATE_ce_era);
		$START_DAY = $arr_temp[2];
		$START_MONTH = $arr_temp[1];
		$START_YEAR = $arr_temp[0];
		$tmp = mktime(0, 0, 0, $START_MONTH, $START_DAY, $START_YEAR);
		$tmp_date = date("Y-m-d", $tmp);
		
		while ($tmp_date <= $ENDDATE_ce_era && !trim($count_duplicate_date)) {
			if ($tmp_date == $STARTDATE_ce_era) {			
				// ===== เช็ควันลาเริ่มต้น
				$cmd = "select PER_ID from PER_ABSENT
								 where PER_ID=$PER_ID $absid_condition $search_approve and 
								 ( '$tmp_date' = ABS_STARTDATE ) and $startperiod_condition1 ";	
				$count_duplicate_date = $db_dpis_spec->send_cmd($cmd);
				//echo "$cmd<br />count_duplicate_date=$count_duplicate_date<br />";
				if (!trim($count_duplicate_date)) {
					$cmd = "select PER_ID from PER_ABSENT
									 where PER_ID=$PER_ID $absid_condition $search_approve and 
									 ( '$tmp_date' = ABS_ENDDATE ) and $startperiod_condition2 ";	
					$count_duplicate_date = $db_dpis_spec->send_cmd($cmd);
					//echo "$cmd<br />count_duplicate_date=$count_duplicate_date<br />";
				}
			} elseif ($tmp_date == $ENDDATE_ce_era) {
				// ===== เช็ควันลาสิ้นสุด
				$cmd = "select PER_ID from PER_ABSENT
								 where PER_ID=$PER_ID $absid_condition $search_approve and 
								 ( '$tmp_date' = ABS_STARTDATE ) and $endperiod_condition1 ";	
				$count_duplicate_date = $db_dpis_spec->send_cmd($cmd);
				//echo "$cmd<br />count_duplicate_date=$count_duplicate_date<br />";
				if (!trim($count_duplicate_date)) {
					$cmd = "select PER_ID from PER_ABSENT
									 where PER_ID=$PER_ID $absid_condition $search_approve and 
									 ( '$tmp_date' = ABS_ENDDATE ) and $endperiod_condition2 ";	
					$count_duplicate_date = $db_dpis_spec->send_cmd($cmd);
					//echo "$cmd<br />count_duplicate_date=$count_duplicate_date<br />";
				}				
			} else {			
				// ===== เช็ควันลาว่าอยู่ในระหว่างวันลาเริ่มต้นและวันลาสิ้นสุดใน db		
				$cmd = "select PER_ID from PER_ABSENT
								where 	PER_ID=$PER_ID $absid_condition $search_approve and 
								ABS_STARTDATE <= '$tmp_date' and ABS_ENDDATE >= '$tmp_date' ";
				$count_duplicate_date = $db_dpis_spec->send_cmd($cmd);
				//echo "$cmd<br />count_duplicate_date=$count_duplicate_date<br />";				
			}	

//			echo ++$num . " วัน || $tmp_date != $ENDDATE_ce_era<br>";				
			$num_day++;
			$tmp = mktime(0, 0, 0, $START_MONTH, $START_DAY+$num_day, $START_YEAR);	
			$tmp_date = date("Y-m-d", $tmp);			
		}	// end while 
	}	// end if
	// ===== END process เช็คว่ามีการลาซ้ำกับวันที่เคยลาไปแล้วหรือไม่ ===== 
	// ====================================================
//<!--<script>-->
	if (!$INVALID_STARTDATE && !$INVALID_ENDDATE && !$count_confirm && !$count_duplicate_date && !$period_err) {
		// check วันลาพักผ่อนเกิน
		$result_sum_AB_CODE_04 = 1;
//		parent_document_form1_VAR_DAY = "$ABSENT_DAY";
		if($parent_document_form1_AB_CODE == '04' && ($parent_document_form1_AB_COUNT_04 > 0)){
			if(((float)$parent_document_form1_AB_COUNT_04 - (float)parent_document_form1_VAR_DAY) < 0){ // วันที่ลาพักผ่อนมา เกินวันที่ยังเหลือสิทธิ์
				$msg_sum_AB_CODE_04 = "ไม่สามารถลาพักผ่อนเกิน "+(int)$parent_document_form1_AB_COUNT_04+" วันได้";
				$result_sum_AB_CODE_04 = 2;
			}
		}

		if($result_sum_AB_CODE_04==1){
			if(($command=="ADD" || $command=="UPDATE") && ($chkSave==1 || $chkSave ==2)){
				$parent_document_form1_command = "$command";
//				$parent_document_form1_submit();
			}
		}else{	// 2
			if($msg_sum_AB_CODE_04)	echo "(".$msg_sum_AB_CODE_04.")";
		}
	} else if ($period_err) {
		$p_err = "$period_err";
		$p_err_str = "";
		switch($p_err)	{
			case "1" :
			  $p_err_str = "วันเดียวกัน เริ่มครึ่งเช้า สิ้นสุดครึ่งบ่าย ถือเป็น 1 วัน\nโปรดแก้ไขเป็น 'ทั้งวัน'";
			  break;
			case "2" :
			  $p_err_str = "วันเดียวกัน เริ่มครึ่งบ่าย สิ้นสุดครึ่งเช้า ไม่ถูกต้อง\nโปรดแก้ไข";
			  break;
			case "3" :
			  $p_err_str = "วันเดียวกัน เริ่มทั้งวัน สิ้นสุดครึ่งบ่าย ถือเป็น 1 วัน\nโปรดแก้ไขสิ้นสุดเป็น 'ทั้งวัน'";
			  break;
			case "4" :
			  $p_err_str = "วันเดียวกัน เริ่มครึ่งเช้า สิ้นสุดทั้งวัน ถือเป็น 1 วัน\nโปรดแก้ไขเริ่มเป็น 'ทั้งวัน'";
			  break;
			case "5" :
			  $p_err_str = "เริ่มวันแรกเป็นครึ่งเช้า เว้นบ่าย ไม่ถูกต้อง\nโปรดทำแยกเป็นลาอีก 1 รายการ";
			  break;
			case "6" :
			  $p_err_str = "สิ้นสุดจบที่ครึ่งบ่าย เว้นเช้า ไม่ถูกต้อง\nโปรดทำแยกเป็นลาอีก 1 รายการ";
			  break;
			default:
			  $p_err_str = "";
		}
//		echo "err-->".$p_err.",".$p_err_str."<br />";
		$parent_document_form1_VAR_DAY = '';
//		echo "".$p_err_str."<br />";
	} else if ($count_confirm) {
		echo "ไม่สามารถเพิ่มข้อมูลการลา/สาย/ขาดได้  เนื่องจากมีการยืนยันข้อมูลแล้ว<br>";
	} else if ($count_duplicate_date) {
		echo "พบข้อมูลการลาในวันที่ต้องการลาแล้ว กรุณาเลือกวันที่ใหม่อีกครั้ง<br>";
	} else {
		echo "INVALID_STARTDATE=$INVALID_STARTDATE, ".(($INVALID_STARTDATE && $INVALID_ENDDATE)? '<br>' : '' ).", ".$INVALID_ENDDATE."<br>";
	}
//<!--</ script>-->
	$ret = $ABSENT_DAY;
	
	return $ret;
}
?>
