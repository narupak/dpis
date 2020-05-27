<?
$LAYER_TYPE = 0;	// จาก table PER_LAYER กำหนดค่าให้เป็น 0 หมายถึงเป็นพนักงานราชการ
$tmp_count_salpromote = $count_layer_no = $LAYER_NO = "";
$LAYER_SALARY = $LAYER_SALARY_MIN = $LAYER_SALARY_MAX = $UP_SALP_LAYER = $SALP_SALARY_NEW = 0;
$MAX_LAYER_NO = $MAX_LAYER_SALARY = 0;
$arr_LEVEL_NO[0] = substr($LEVEL_NO_SALARY, 0, 1);
$arr_LEVEL_NO[1] = substr($LEVEL_NO_SALARY, -(strlen($LEVEL_NO_SALARY)-1)) + 1;
$LEVEL_NO_NEXT = $arr_LEVEL_NO[0] . $arr_LEVEL_NO[1];

$cmd = " select LAYER_NO, LAYER_SALARY
		from PER_LAYER where LEVEL_NO='$LEVEL_NO_SALARY' and LAYER_ACTIVE=1 order by LAYER_NO desc";
$count_layer_no = $db_dpis1->send_cmd($cmd);
$data1 = $db_dpis1->get_array();
$MAX_LAYER_NO = trim($data1[LAYER_NO]);
$MAX_LAYER_SALARY = trim($data1[LAYER_SALARY]);
if ($count_layer_no>0 && ($MAX_LAYER_NO != "0" && $MAX_LAYER_NO != "")) {
	// ===== ตรวจสอบเงินเดือนว่าอยู่ใน "แท่งและขั้น" ที่กำหนดหรือไม่ ถ้าไม่อยู่ต้องแสดง alert ให้ไปแก้ไขก่อน =====
	$cmd = "	select  	LAYER_NO, LAYER_SALARY 
			from 	PER_LAYER 
			where 	LAYER_TYPE=$LAYER_TYPE and LEVEL_NO='$LEVEL_NO_SALARY' and LAYER_SALARY=$PER_SALARY ";	
	$tmp_count_salpromote = $db_dpis1->send_cmd($cmd);
	// ถ้าไม่พบ record แต่พบค่า LAYER_NO ตามเงื่อนไข LEVEL_NO, LAYER_TYPE และ PER_SALARY ที่ต้องการค้นหา 
	if ($tmp_count_salpromote <= 0) {
		$err_count++;
		$alert_err = true;
		$alert_err_text .= "	<tr class='$class' $onmouse_event  style='color:#FF0000'><td align=\"center\">$err_count</td>อัตราเงินเดือนไม่อยู่ในแท่งและขั้นที่กำหนด</td><td>[ ระดับ " . level_no_format($LEVEL_NO_SALARY) . " : $PER_NAME ]</td><td align=\"center\">&nbsp;<a href='javascript:call_edit_personal($PER_ID);'><img src=\"images/b_edit.png\" border=\"0\" alt=\"แก้ไขข้อมูลบุคคล\"></a></td></tr>";
	} else {
		$data1 = $db_dpis1->get_array();
		$LAYER_NO = (trim($data1[LAYER_NO]))? trim($data1[LAYER_NO]) : 0;
		$LAYER_SALARY = trim($data1[LAYER_SALARY]);
		$LAYER_SALARY_MIN = 0;
		$LAYER_SALARY_MAX = 0;
	}
	
} elseif ($count_layer_no>0 && ($MAX_LAYER_NO == "0" || $MAX_LAYER_NO == "")) {
	// ===== ตรวจสอบเงินเดือนว่าอยู่ใน "ช่วง" ที่กำหนดหรือไม่ ถ้าไม่อยู่ต้องแสดง alert ให้ไปแก้ไขก่อน =====
	$cmd = "	select  LAYER_NO, LAYER_SALARY_MIN, LAYER_SALARY_MAX 
			from    PER_LAYER 
			where  LAYER_TYPE=$LAYER_TYPE and LEVEL_NO='$LEVEL_NO_SALARY' and 
				    (LAYER_SALARY_MIN<=$PER_SALARY and LAYER_SALARY_MAX>=$PER_SALARY) ";							
	$tmp_count_salpromote = $db_dpis1->send_cmd($cmd);
	if ($tmp_count_salpromote <= 0) {
		$err_count++;
		$alert_err = true;
		$alert_err_text .= "	<tr class='$class' $onmouse_event  style='color:#FF0000'><td align=\"center\">$err_count</td><td>อัตราเงินเดือนไม่อยู่ในช่วงที่กำหนด</td<td>[ ระดับ " . level_no_format($LEVEL_NO_SALARY) . " : $PER_NAME ]</td><td align=\"center\">&nbsp;<a href='javascript:call_edit_personal($PER_ID);'><img src=\"images/b_edit.png\" border=\"0\" alt=\"แก้ไขข้อมูลบุคคล\"></a></td></tr>";
	} else {
		$data1 = $db_dpis1->get_array();	
		$LAYER_NO = (trim($data1[LAYER_NO]))? trim($data1[LAYER_NO]) : 0;
		$LAYER_SALARY = $PER_SALARY;
		$LAYER_SALARY_MIN = trim($data1[LAYER_SALARY_MIN]) + 0;
		$LAYER_SALARY_MAX = trim($data1[LAYER_SALARY_MAX]) + 0;		
	}
}
$count_data=$err_count;
//echo ":: $PER_ID -> $PER_NAME :: count_layer_no=$count_layer_no || LAYER_NO=$LAYER_NO || LEVEL_NO=$LEVEL_NO<br>";
//echo ">> LAYER_SALARY=$LAYER_SALARY || LAYER_SALARY_MIN=$LAYER_SALARY_MIN || LAYER_SALARY_MAX=$LAYER_SALARY_MAX<br>";

$non_promote = false; 
$non_promote_text = $SALP_REMARK = "";
$SALP_PERCENT = $SALP_SPSALARY = 0;
if(!$alert_err){
	// ===== ตรวจสอบ วันลาติดตามคู่สมรส =====
	$cmd = " 	select 		PER_ID, sum(ABS_DAY) as DAY_SPOUSE, count(PER_ID) as TIME_SPOUSE, ABS_STARTDATE, ABS_ENDDATE  
			from 		PER_ABSENT
			where 		PER_ID=$PER_ID and AB_CODE = '09'
						$search_monthyear 
			group by 		PER_ID, ABS_STARTDATE, ABS_ENDDATE 
			having 		(sum(ABS_DAY) > 0) or (count(PER_ID) > 0) ";
	$db_dpis1->send_cmd($cmd);
	$data1 = $db_dpis1->get_array();
	$DAY_SPOUSE = $data1[DAY_SPOUSE];
	$TIME_SPOUSE = $data1[TIME_SPOUSE];
	$ABS_STARTDATE = $data1[ABS_STARTDATE];
	$ABS_ENDDATE = $data1[ABS_ENDDATE];	
	if ( ($DAY_SPOUSE > 365) ) {
		$non_promote = true;
		$non_promote_text .= "ไม่สามารถเลื่อนขั้นเงินเดือน $PER_NAME ได้ เนื่องจากลาติดมตามคู่สมรส เกินระยะเวลาที่กำหนด ตั้งแต่วันที่ $ABS_STARTDATE ถึงวันที่ $ABS_ENDDATE<br>";
		$SALP_YN = 0;
		$SALP_LEVEL = 0;	
		$SALP_REASON = 1;				
	}		// 	if ( ($DAY_SPOUSE > 365) ) 	

	// ===== ตรวจสอบเงินเดือนเต็มขั้น =====
	// ถ้าเงินเดือนเต็มขั้นแล้ว จะได้รับเงินตอบแทนพิเศษเป็น % ดังนี้
	if ( (trim($LAYER_NO) && ($PER_SALARY == $MAX_LAYER_SALARY)) ) {
//		$non_promote = true;
//		$non_promote_text .= "อัตราเงินเดือนเต็มขั้นแล้ว ไม่สามารถเลื่อนขั้นเงินเดือนของ $PER_NAME ได้<br>";
//		$SALP_YN = 0;
		$SALP_YN = 1;
		$SALP_LEVEL = 0;
		$SALP_REASON = 2;	

		if ($SALQ_TYPE2 == 1) 		$SALP_PERCENT = 2;
		elseif ($SALQ_TYPE2 == 2) 		$SALP_PERCENT = 4;
		$SALP_SPSALARY = (($PER_SALARY * $SALP_PERCENT) / 100);		
	} elseif ( (!trim($LAYER_NO) && ($PER_SALARY >= $LAYER_SALARY_MAX)) ) {
		$non_promote = true;
		$non_promote_text .= "อัตราเงินเดือนเต็มช่วงแล้ว ไม่สามารถเลื่อนเงินเดือนของ $PER_NAME ได้<br>";
		$SALP_YN = 0;
		$SALP_LEVEL = 0;
		$SALP_REASON = 2;	

		if ($SALQ_TYPE2 == 1) 		$SALP_PERCENT = 2;
		elseif ($SALQ_TYPE2 == 2) 		$SALP_PERCENT = 4;
		$SALP_SPSALARY = (($PER_SALARY * $SALP_PERCENT) / 100);			
	} 	// end if


	// ===== ตรวจสอบ วันลากิจ+ลาป่วยไม่เกิน 23 วัน 10 ครั้ง สายไม่เกิน 18 วัน =====
	// ===== ลาป่วย + ลากิจ =====
	$cmd = " 	select 		PER_ID, sum(ABS_DAY) as DAY_ILL, count(PER_ID) as TIME_ILL
			from 		PER_ABSENT
			where 		PER_ID=$PER_ID and AB_CODE IN ('01', '03')
						$search_monthyear 							
			group by 		PER_ID
			having 		(sum(ABS_DAY) > 0) or (count(PER_ID) > 0) ";
	$db_dpis1->send_cmd($cmd);
	$data1 = $db_dpis1->get_array();
	$DAY_ILL = $data1[DAY_ILL];
	$TIME_ILL = $data1[TIME_ILL];
	if ( ($DAY_ILL > 45) || ($TIME_ILL > 20) ) {
		$non_promote = true;
		$non_promote_text .= "ไม่สามารถเลื่อนขั้นเงินเดือน $PER_NAME ได้ เนื่องจากลาป่วยเกิน 23 วัน หรือ เกิน 10 ครั้ง<br>";
		$SALP_YN = 0;
		$SALP_LEVEL = 0;				
		$SALP_REASON = 0;				
	}
	
	// ===== สาย =====
	$cmd = " 	select 		PER_ID, count(PER_ID) as TIME_LATE
			from 		PER_ABSENT 
			where 		PER_ID=$PER_ID and AB_CODE IN ('10')
						$search_monthyear 							
			group by 		PER_ID
			having 		count(PER_ID) > 0 ";
	$db_dpis1->send_cmd($cmd);	
	$data1 = $db_dpis1->get_array();
	$TIME_LATE = $data1[TIME_LATE];
	if ( $TIME_LATE > 36 )  {
		$non_promote = true;
		$non_promote_text .= "ไม่สามารถเลื่อนขั้นเงินเดือน $PER_NAME ได้ เนื่องจากสายเกิน 18 ครั้ง<br>";
		$SALP_YN = 0;
		$SALP_LEVEL = 0;	
		$SALP_REASON = 0;
	}

/*
	// ===== ตรวจสอบการ อบรม/ดูงาน/สัมมนา =====
	unset($tmp_date, $tmp_date1);
	$cmd = " select PER_ID, TRN_STARTDATE, TRN_ENDDATE from PER_TRAINING where PER_ID=$PER_ID ";
	$db_dpis1->send_cmd($cmd);
	$data1 = $db_dpis1->get_array(); 
	$tmp_date = explode("-", substr($data1[TRN_STARTDATE], 0, 10));
	if (trim($data1[TRN_STARTDATE])) $tmp_trn_startdate = $tmp_date[2] ."-". $tmp_date[1] ."-". $tmp_date[0];	
	$TRN_STARTDATE = mktime(0, 0, 0, $tmp_date[1], $tmp_date[2], $tmp_date[0]);
	
	$tmp_date = explode("-", substr($data1[TRN_ENDDATE], 0, 10));			
	if (trim($data1[TRN_ENDDATE])) $tmp_trn_enddate = $tmp_date[2] ."-". $tmp_date[1] ."-". $tmp_date[0];		
	$TRN_ENDDATE = mktime(0, 0, 0, $tmp_date[1], $tmp_date[2], $tmp_date[0]);
	
	$TIME_TRAINING = ($TRN_ENDDATE - $TRN_STARTDATE) / 2592000; // ใช้วินาทีหาร = 30 วัน
	if ($TIME_TRAINING > 12) {
		$non_promote = true;
		$non_promote_text .= "ไม่สามารถเลื่อนขั้นเงินเดือน $PER_NAME ได้ เนื่องจากลาอบรม/ดูงาน/สัมมนา เกินจำนวนวันที่กำหนด ตั้งแต่วันที่ $tmp_trn_startdate ถึงวันที่ $tmp_trn_enddate <br>";
		$SALP_YN = 0;
		$SALP_LEVEL = 0;	
		$SALP_REASON = 3;
	}
*/

	// ===== ตรวจสอบว่าบรรจุเกิน 4 เดือนหรือยัง =====
	$cmd = "	select MOV_CODE, max(POH_EFFECTIVEDATE) as EFFECTIVEDATE from PER_POSITIONHIS 
					where PER_ID=$PER_ID and MOV_CODE in ('101', '10110', '10120', '10130', '10140', '105', '10510', '10520') 
					group by MOV_CODE ";
	$db_dpis1->send_cmd($cmd);
	$data1 = $db_dpis1->get_array();
	$tmp_date1 = explode("-", substr($data1[EFFECTIVEDATE], 0, 10));
	if (trim($data1[EFFECTIVEDATE])) $tmp_effectivedate = $tmp_date1[2] ."-". $tmp_date1[1] ."-". $tmp_date1[0];		

	$tmp_date2 = explode("-", date("Y-m-d"));	
	$tmp_date_effect =  mktime(0, 0, 0, $tmp_date1[1], $tmp_date1[2], $tmp_date1[0]);
	$tmp_date_now = mktime(0, 0, 0, $tmp_date2[1], $tmp_date2[2], $tmp_date2[0]);
	$MONTH_FILL = ($tmp_date_now - $tmp_date_effect) / 2592000;		// ใช้วินาทีหาร = 30 วัน
	if ($MONTH_FILL < 4) {			// check ว่าบรรจุเกิน 4 เดือนหรือยัง
		$non_promote = true;
		$non_promote_text .= "ไม่สามารถเลื่อนขั้นเงินเดือน $PER_NAME ได้ เนื่องจากบรรจุยังไม่เกิน 4 เดือน โดยบรรจุวันที่ $tmp_effectivedate<br>";
		$SALP_YN = 0;
		$SALP_LEVEL = 0;				
		$SALP_REASON = 4;
	}

	
	// ===== คำนวนผลการเลื่อนขั้นเงินเดือน
	if (trim($LAYER_NO)) {
		// ถ้ามี LAYER_NO จะได้รับเงินเดือนเพิ่มตามขั้นโดยบวกจำนวนขั้นที่ได้เลื่อน ตาม LEVEL_NO 
		$UP_SALP_LAYER = $LAYER_NO + $SALP_LEVEL;
		// ถ้าจำนวนขั้นที่เลื่อนขึ้นสูงกว่าที่มีอยู่จริง ให้เพิ่มได้ถึงแค่ MAX
		if ($UP_SALP_LAYER > $MAX_LAYER_NO) {
			$UP_SALP_LAYER = $MAX_LAYER_NO;
			$non_promote_text .= "เลื่อนขั้นเงินเดือนของ $PER_NAME ได้ไม่ครบตามจำนวน เนื่องจากเกินจำนวนเงินเดือนเต็มขั้น<br>";
		} 
		$cmd = " select LAYER_SALARY from PER_LAYER 
				where LAYER_TYPE=$LAYER_TYPE and LEVEL_NO='$LEVEL_NO_SALARY' and LAYER_NO='$UP_SALP_LAYER' 
				order by LAYER_SALARY ";
		$db_dpis1->send_cmd($cmd);
		$data1 = $db_dpis1->get_array();				
		$SALP_SALARY_NEW = $data1[LAYER_SALARY] + 0;
		
	} elseif (!trim($LAYER_NO)) {
		// ถ้าไม่มี LAYER_NO จะได้รับการเพิ่มเงินเดือนเป็น % ซึ่งตอนนี้ (04/2550) จะเพิ่มเงินเดือนเป็น % ทีละคนเอาเอง
	}
//echo ":: $PER_ID -> $PER_NAME :: SALP_YN=$SALP_YN || LAYER_NO=$LAYER_NO || LEVEL_NO=$LEVEL_NO <br>";
//echo ">> PER_SALARY=$PER_SALARY || LAYER_SALARY=$LAYER_SALARY || UP_SALP_LAYER=$UP_SALP_LAYER || SALP_SALARY_NEW=$SALP_SALARY_NEW<br>";


	if ($SALP_SALARY_NEW == 0) 	$SALP_SALARY_NEW = $PER_SALARY;
	// วันที่คำสั่งมีผลบังคับใช้
	if ($SALQ_TYPE2 == 1)				$SALP_DATE = ($SALQ_YEAR - 543) ."-04-01";
	elseif ($SALQ_TYPE2 == 2)			$SALP_DATE = ($SALQ_YEAR - 543) ."-10-01";
	if ($SALQ_TYPE1==1 && $SALQ_TYPE2==1) 			$tmp_SALQ_TYPE = 1;
	elseif ($SALQ_TYPE1==1 && $SALQ_TYPE2==2) 		$tmp_SALQ_TYPE = 2;
	elseif ($SALQ_TYPE1==2 && $SALQ_TYPE2==1) 		$tmp_SALQ_TYPE = 3;
	elseif ($SALQ_TYPE1==2 && $SALQ_TYPE2==2) 		$tmp_SALQ_TYPE = 4;
	elseif ($SALQ_TYPE1==3 && $SALQ_TYPE2==1) 		$tmp_SALQ_TYPE = 5;
	elseif ($SALQ_TYPE1==3 && $SALQ_TYPE2==2) 		$tmp_SALQ_TYPE = 6;
	$SALP_REMARK = (trim($non_promote_text))? $non_promote_text : "";
	
	// บันทึกข้อมูล ลง table PER_SALPROMOTE 
	$cmd = " 	insert into PER_SALPROMOTE
					(SALQ_YEAR, SALQ_TYPE, PER_ID, SALP_YN, SALP_LEVEL, SALP_SALARY_OLD, 
					 SALP_SALARY_NEW, SALP_PERCENT, SALP_SPSALARY, SALP_DATE, SALP_REMARK, 
					 SALP_REASON, DEPARTMENT_ID, PER_CARDNO, UPDATE_USER, UPDATE_DATE)
					values 
					('$SALQ_YEAR', $tmp_SALQ_TYPE, $PER_ID, $SALP_YN, $SALP_LEVEL, $PER_SALARY, 
					 $SALP_SALARY_NEW, $SALP_PERCENT, $SALP_SPSALARY, '$SALP_DATE', '$SALP_REMARK', 
					 $SALP_REASON, $DEPARTMENT_ID, $PER_CARDNO, $SESS_USERID, '$UPDATE_DATE')";
	$db_dpis1->send_cmd($cmd);
} // end if(!$alert_err){
//echo "==============<br>";
?>