<?
$tmp_count_salpromote = $count_layer_no = $LAYER_NO = "";
$LAYER_SALARY = $LAYER_SALARY_MIN = $LAYER_SALARY_MIN = $UP_SALP_LAYER = $SALP_SALARY_NEW = 0;
$MAX_LAYER_NO = $MAX_LAYER_SALARY = 0;
// �� PT_CODE ����Ѻ�Թ��͹�дѺ ����� ������ ���������дѺ�٧
$cmd = "	select a.POS_NO , a.POS_NO_NAME, a.PT_CODE, b.LAYER_TYPE from PER_POSITION a, PER_LINE b 
				where POS_ID=$POS_ID and a.PL_CODE=b.PL_CODE ";	
$db_dpis1->send_cmd($cmd);
$data1 = $db_dpis1->get_array();
$POS_NO = trim($data1[POS_NO]);
$POS_NO_NAME = trim($data1[POS_NO_NAME]);
$PT_CODE = trim($data1[PT_CODE]);
// check ��� $PT_CODE �´٨ҡ table PER_TYPE 
if ($PT_CODE == 32 && $LEVEL_NO_SALARY=="11")		$LAYER_TYPE = 2;			// ���������дѺ�٧
else												$LAYER_TYPE = 1;			// �����
//  ���˹����§ҹ����� LAYER_TYPE = 2
if ($RPT_N) 
	if ($LEVEL_NO=="O3" || $LEVEL_NO =="K5")
		$LAYER_TYPE = trim($data1[LAYER_TYPE]);
	else
		$LAYER_TYPE = 1;

// ===== ��Ǩ�ͺ�Թ��͹���������� ��Т�鹷���˹�������� �����������ͧ�ʴ� alert ������䢡�͹ =====
$tmp_count_salpromote = "";
if ($RPT_N)
	$cmd = "	select 	LAYER_NO, LAYER_SALARY from PER_LAYER 
		where 	LAYER_TYPE=$LAYER_TYPE and LEVEL_NO='$LEVEL_NO' and LAYER_SALARY=$PER_SALARY
		order by 	LEVEL_NO, LAYER_NO   ";
else
	$cmd = "	select 	LAYER_NO, LAYER_SALARY from PER_LAYER 
		where 	LAYER_TYPE=$LAYER_TYPE and (LEVEL_NO='". str_pad($LEVEL_NO_SALARY, 2, "0", STR_PAD_LEFT) ."' or 
				LEVEL_NO='". str_pad(($LEVEL_NO_SALARY + 1), 2, "0", STR_PAD_LEFT) ."') and LAYER_SALARY=$PER_SALARY	
		order by 	LEVEL_NO, LAYER_NO   ";
$tmp_count_salpromote = $db_dpis1->send_cmd($cmd);
if ($tmp_count_salpromote <= 0) {
	$err_count++;
	$alert_err = true;
	$alert_err_text .= "	<tr class='$class' $onmouse_event  style='color:#FF0000'><td align='center'>$err_count</td><td>�ѵ���Թ��͹������������Т�鹷���˹�</td>
		<td align=\"center\">".$POS_NO_NAME.$POS_NO."</td>
		<td>$PER_NAME</td>
		<td>$LEVEL_NAME</td>
		<td align='center'>&nbsp;<a href='javascript:call_edit_personal($PER_ID);'><img src=\"images/b_edit.png\" border=\"0\" alt=\"��䢢����źؤ��\"></a></td></tr>";
} else {
	$data1 = $db_dpis1->get_array();
	$LAYER_NO = (trim($data1[LAYER_NO]))? trim($data1[LAYER_NO]) : 0;
	$LAYER_SALARY = trim($data1[LAYER_SALARY]);
}
$count_data=$err_count;

//echo ":: $PER_ID -> $PER_NAME :: count_layer_no=$count_layer_no || LAYER_NO=$LAYER_NO || LEVEL_NO=$LEVEL_NO || LEVEL_NO_SALARY=$LEVEL_NO_SALARY<br>";
//echo ">> LAYER_SALARY=$LAYER_SALARY || LAYER_SALARY_MIN=$LAYER_SALARY_MIN || LAYER_SALARY_MAX=$LAYER_SALARY_MAX<br>";

$non_promote = false; 
$non_promote_text = $SALP_REMARK = "";
$SALP_PERCENT = $SALP_SPSALARY = 0;
if(!$alert_err){
	// ===== ��Ǩ�ͺ �ѹ�ҵԴ���������� =====
	$cmd = " 	select 		PER_ID, sum(ABS_DAY) as DAY_SPOUSE, count(PER_ID) as TIME_SPOUSE, ABS_STARTDATE, ABS_ENDDATE  
					from 			PER_ABSENT
					where 		PER_ID=$PER_ID and AB_CODE = '09'
									$search_monthyear 
					group by 	PER_ID, ABS_STARTDATE, ABS_ENDDATE 
					having 		(sum(ABS_DAY) > 0) or (count(PER_ID) > 0) ";
	$db_dpis1->send_cmd($cmd);
	$data1 = $db_dpis1->get_array();
	$DAY_SPOUSE = $data1[DAY_SPOUSE];
	$TIME_SPOUSE = $data1[TIME_SPOUSE];
	$ABS_STARTDATE = $data1[ABS_STARTDATE];
	$ABS_ENDDATE = $data1[ABS_ENDDATE];	
	if ( ($DAY_SPOUSE > 365) ) {
		$non_promote = true;
		$non_promote_text .= "�������ö����͹����Թ��͹ $PER_NAME �� ���ͧ�ҡ�ҵԴ����������� �Թ�������ҷ���˹� ������ѹ��� $ABS_STARTDATE �֧�ѹ��� $ABS_ENDDATE";
		$SALP_YN = 0;
		$SALP_LEVEL = 0;	
		$SALP_REASON = 1;				
	}		// 	if ( ($DAY_SPOUSE > 365) ) 	

	// ===== ��Ǩ�ͺ�Թ��͹������ =====
	if ($RPT_N)
		$cmd = "	select 	max(LAYER_SALARY_FULL) as SALARY, LEVEL_NO from PER_LAYER 
			where 	LAYER_TYPE=$LAYER_TYPE and LEVEL_NO='$LEVEL_NO' and LAYER_SALARY_FULL is not null
			group by 	LEVEL_NO";
	else
		$cmd = "	select 	max(LAYER_NO), max(LAYER_SALARY) as SALARY, LEVEL_NO from PER_LAYER 
			where 	LAYER_TYPE=$LAYER_TYPE and LEVEL_NO='$LEVEL_NO_SALARY'
			group by 	LEVEL_NO";
	$db_dpis1->send_cmd($cmd);
	while ($data1 = $db_dpis1->get_array()) { 
		$MAX_LAYER_SALARY = $data1[SALARY] + 0;
		if ( $PER_SALARY == $MAX_LAYER_SALARY)	 {
//			$non_promote = true;
//			$non_promote_text .= "�ѵ���Թ��͹���������� �������ö����͹����Թ��͹�ͧ $PER_NAME ��";
//			$SALP_YN = 0;
			$SALP_YN = 1;
			$SALP_LEVEL = 0;
			$SALP_REASON = 2;	
		
			// �Թ��͹���������� ������Ѻ�Թ�ͺ᷹�����
			if ($SALQ_TYPE2 == 1)			$SALP_PERCENT = 2;
			elseif ($SALQ_TYPE2 == 2) 		$SALP_PERCENT = 4;
			$SALP_SPSALARY = (($PER_SALARY * $SALP_PERCENT) / 100);		
			if ($RPT_N) {
				$cmd = " select LAYER_SALARY_MIDPOINT, LAYER_SALARY_MIDPOINT1 from 	PER_LAYER 
								where LAYER_TYPE=$LAYER_TYPE and LEVEL_NO='$LEVEL_NO' and LAYER_SALARY=$PER_SALARY ";
				$db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				if ($SALQ_TYPE2 == 1) $SALP_SPSALARY = $data1[LAYER_SALARY_MIDPOINT] + 0;
				elseif ($SALQ_TYPE2 == 2) $SALP_SPSALARY = $data1[LAYER_SALARY_MIDPOINT1] + 0;
			}
		} 	// end if
	}		// end while 

	// ===== ��Ǩ�ͺ �ѹ�ҡԨ+�һ�������Թ 23 �ѹ 10 ���� �������Թ 18 �ѹ =====
	// ===== �һ��� + �ҡԨ =====
	$cmd = " 	select 		PER_ID, sum(ABS_DAY) as DAY_ILL, count(PER_ID) as TIME_ILL
			from 		PER_ABSENT
			where 	 PER_ID=$PER_ID and AB_CODE IN ('01', '03')
						$search_monthyear 							
			group by 		PER_ID
			having 		(sum(ABS_DAY) > 0) or (count(PER_ID) > 0) ";
	$db_dpis1->send_cmd($cmd);
	$data1 = $db_dpis1->get_array();
	$DAY_ILL = $data1[DAY_ILL];
	$TIME_ILL = $data1[TIME_ILL];
	if ( ($DAY_ILL > 45) || ($TIME_ILL > 20) ) {
		$non_promote = true;
		$non_promote_text .= "�������ö����͹����Թ��͹ $PER_NAME �� ���ͧ�ҡ�һ����Թ 23 �ѹ ���� �Թ 10 ����";
		$SALP_YN = 0;
		$SALP_LEVEL = 0;				
		$SALP_REASON = 0;				
	}
	
	// ===== ��� =====
	$cmd = " 	select 		PER_ID, count(PER_ID) as TIME_LATE
			from 		PER_ABSENT 
			where 	 PER_ID=$PER_ID and AB_CODE IN ('10')
						$search_monthyear 							
			group by 		PER_ID
			having 		count(PER_ID) > 0 ";
	$db_dpis1->send_cmd($cmd);	
	$data1 = $db_dpis1->get_array();
	$TIME_LATE = $data1[TIME_LATE];
	if ( $TIME_LATE > 36 )  {
		$non_promote = true;
		$non_promote_text .= "�������ö����͹����Թ��͹ $PER_NAME �� ���ͧ�ҡ����Թ 18 ����";
		$SALP_YN = 0;
		$SALP_LEVEL = 0;	
		$SALP_REASON = 0;
	}

/*
	// ===== ��Ǩ�ͺ��� ͺ��/�٧ҹ/������ =====
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
	
	$TIME_TRAINING = ($TRN_ENDDATE - $TRN_STARTDATE) / 2592000; // ���Թҷ���� = 30 �ѹ
	if ($TIME_TRAINING > 12) {
		$non_promote = true;
		$non_promote_text .= "�������ö����͹����Թ��͹ $PER_NAME �� ���ͧ�ҡ��ͺ��/�٧ҹ/������ �Թ�ӹǹ�ѹ����˹� ������ѹ��� $tmp_trn_startdate �֧�ѹ��� $tmp_trn_enddate ";
		$SALP_YN = 0;
		$SALP_LEVEL = 0;	
		$SALP_REASON = 3;
	}
*/

	// ===== ��Ǩ�ͺ��Һ�è��Թ 4 ��͹�����ѧ =====
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
	$MONTH_FILL = ($tmp_date_now - $tmp_date_effect) / 2592000;		// ���Թҷ���� = 30 �ѹ
	if ($MONTH_FILL < 4) {			// check ��Һ�è��Թ 4 ��͹�����ѧ
		$non_promote = true;
		$non_promote_text .= "�������ö����͹����Թ��͹ $PER_NAME �� ���ͧ�ҡ��è��ѧ����Թ 4 ��͹ �º�è��ѹ��� $tmp_effectivedate";
		$SALP_YN = 0;
		$SALP_LEVEL = 0;				
		$SALP_REASON = 4;
	}
	
	// ===== �ӹǹ�š������͹����Թ��͹
	// select LAYER_NO ��дѺ LEVEL_NO_SALARY �Ѩ�غѹ�ͧ����Ҫ���
	if ($RPT_N)
		$cmd = " select 	LAYER_NO, a.LEVEL_NO, a.LEVEL_NO_SALARY, PER_SALARY, LAYER_SALARY, LAYER_SALARY_MIDPOINT, 
				LAYER_SALARY_MIDPOINT1, LAYER_SALARY_MIDPOINT2, LAYER_SALARY_FULL, LAYER_SALARY_TEMP 
				from 	PER_PERSONAL a, PER_LAYER b
				where 	a.PER_ID=$PER_ID and LAYER_TYPE=$LAYER_TYPE and a.LEVEL_NO=b.LEVEL_NO and LAYER_SALARY=$PER_SALARY
				order by 	LAYER_NO ";
	else
		$cmd = " select 	LAYER_NO, a.LEVEL_NO, a.LEVEL_NO_SALARY, PER_SALARY, LAYER_SALARY 
				from 	PER_PERSONAL a, PER_LAYER b
				where 	a.PER_ID=$PER_ID and LAYER_TYPE=$LAYER_TYPE and a.LEVEL_NO_SALARY=b.LEVEL_NO and LAYER_SALARY=$PER_SALARY
				order by 	LAYER_NO ";
	$db_dpis1->send_cmd($cmd);
	$data1 = $db_dpis1->get_array();
	$CAL_LAYER_NO = trim($data1[LAYER_NO]);
	$CAL_LEVEL_NO = trim($data1[LEVEL_NO]);
	$CAL_LEVEL_NO_SALARY = trim($data1[LEVEL_NO_SALARY]);
	$CAL_LAYER_SALARY_MIDPOINT = $data1[LAYER_SALARY_MIDPOINT] + 0;
	$CAL_LAYER_SALARY_MIDPOINT1 = $data1[LAYER_SALARY_MIDPOINT1] + 0;
	$CAL_LAYER_SALARY_MIDPOINT2 = $data1[LAYER_SALARY_MIDPOINT2] + 0;
	$CAL_LAYER_SALARY_FULL = $data1[LAYER_SALARY_FULL] + 0;
	$CAL_LAYER_SALARY_TEMP = $data1[LAYER_SALARY_TEMP] + 0;
	if ($RPT_N) {
		if ($SALQ_TYPE2 == 1) $SALP_SALARY_NEW = $CAL_LAYER_SALARY_MIDPOINT;
		elseif ($SALQ_TYPE2 == 2) $SALP_SALARY_NEW = $CAL_LAYER_SALARY_MIDPOINT1;
		if ($CAL_LAYER_SALARY_FULL == $PER_SALARY) $SALP_SALARY_NEW = $CAL_LAYER_SALARY_FULL;
		if ($SALQ_TYPE2 == 1) 
			if ($CAL_LAYER_SALARY_MIDPOINT <= $PER_SALARY && $CAL_LAYER_SALARY_FULL != $PER_SALARY)
				$non_promote_text .= "����͹����Թ��͹�ͧ $PER_NAME �����ú����ӹǹ ���ͧ�ҡ�Թ�ӹǹ�Թ��͹������";
		elseif ($SALQ_TYPE2 == 2)
			if ($CAL_LAYER_SALARY_MIDPOINT1 <= $PER_SALARY && $CAL_LAYER_SALARY_FULL != $PER_SALARY)
				$non_promote_text .= "����͹����Թ��͹�ͧ $PER_NAME �����ú����ӹǹ ���ͧ�ҡ�Թ�ӹǹ�Թ��͹������";
	} else {
		if (trim($data1[LAYER_NO])) {				
			$UP_SALP_LAYER = $data1[LAYER_NO] + $SALP_LEVEL;
		// ����������дѺ LEVEL_NO_SALARY �����������дѺ LEVEL_NO_SALARY+1		
		} else {			
			$cmd = " select 	LAYER_NO, LEVEL_NO, LAYER_SALARY from PER_LAYER b
				  	where 	LAYER_TYPE=$LAYER_TYPE and LEVEL_NO='" .str_pad(($LEVEL_NO_SALARY + 1), 2, "0", STR_PAD_LEFT). "' and 
							LAYER_SALARY=$PER_SALARY 
					order by 	LAYER_NO ";
			$db_dpis1->send_cmd($cmd);
			$data1 = $db_dpis1->get_array();	
			$UP_SALP_LAYER = $CAL_LAYER_NO + $SALP_LEVEL;
		}
		// select ��� LAYER_NO �����������Թ�ӹǹ LAYER_NO �ͧ LEVEL_NO_SALARY ��� � �������� 
		// ��Ҩӹǹ��鹷������͹����٧���ҷ���������ԧ ���������֧�� MAX	
		$cmd = " select LAYER_NO, LAYER_SALARY
				from PER_LAYER where LEVEL_NO='$LEVEL_NO_SALARY' and LAYER_ACTIVE=1 order by LAYER_NO desc";
		$count_layer_no = $db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$MAX_LAYER_NO = trim($data2[LAYER_NO]);
		$MAX_LAYER_SALARY = trim($data2[LAYER_SALARY]);
		if ($UP_SALP_LAYER > $MAX_LAYER_NO) {
			$UP_SALP_LAYER = $MAX_LAYER_NO;
			$non_promote_text .= "����͹����Թ��͹�ͧ $PER_NAME �����ú����ӹǹ ���ͧ�ҡ�Թ�ӹǹ�Թ��͹������";
		} 

		// select �Թ��͹�����ҡѺ�ѵ�ҷ������͹ LAYER_NO ���� 
		$cmd = " 	select 	LAYER_SALARY from PER_LAYER 
			  	where 	LAYER_TYPE=$LAYER_TYPE and LEVEL_NO='$CAL_LEVEL_NO_SALARY' and LAYER_NO=$UP_SALP_LAYER 
			  	order by 	LAYER_SALARY ";
		$db_dpis1->send_cmd($cmd);
		$data1 = $db_dpis1->get_array();				
		$SALP_SALARY_NEW = $data1[LAYER_SALARY] + 0;
	//echo ":: $PER_ID -> $PER_NAME :: SALP_YN=$SALP_YN || LAYER_NO=$LAYER_NO || LEVEL_NO=$LEVEL_NO || LEVEL_NO_SALARY=$LEVEL_NO_SALARY <br>";
	//echo ">> PER_SALARY=$PER_SALARY || LAYER_SALARY=$LAYER_SALARY || UP_SALP_LAYER=$UP_SALP_LAYER || SALP_SALARY_NEW=$SALP_SALARY_NEW<br>";
	} 

	if ($SALP_SALARY_NEW == 0) 	$SALP_SALARY_NEW = $PER_SALARY;
	// �ѹ��������ռźѧ�Ѻ��
	if ($SALQ_TYPE2 == 1)			$SALP_DATE = ($SALQ_YEAR - 543) ."-04-01";
	elseif ($SALQ_TYPE2 == 2)		$SALP_DATE = ($SALQ_YEAR - 543) ."-10-01";
	if ($SALQ_TYPE1==1 && $SALQ_TYPE2==1) 			$tmp_SALQ_TYPE = 1;
	elseif ($SALQ_TYPE1==1 && $SALQ_TYPE2==2) 		$tmp_SALQ_TYPE = 2;
	elseif ($SALQ_TYPE1==2 && $SALQ_TYPE2==1) 		$tmp_SALQ_TYPE = 3;
	elseif ($SALQ_TYPE1==2 && $SALQ_TYPE2==2) 		$tmp_SALQ_TYPE = 4;
	elseif ($SALQ_TYPE1==3 && $SALQ_TYPE2==1) 		$tmp_SALQ_TYPE = 5;
	elseif ($SALQ_TYPE1==3 && $SALQ_TYPE2==2) 		$tmp_SALQ_TYPE = 6;
	$SALP_REMARK = (trim($non_promote_text))? $non_promote_text : "";
	if ($SALP_YN == 0) 	$SALP_SALARY_NEW = $PER_SALARY;
	
		// �ѹ�֡������ ŧ table PER_SALPROMOTE 
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