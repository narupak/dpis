<?
// �� EG_CODE ����Ѻ�Թ��͹��дѺ �ͧ��ѡ�ҹ�Ҫ��û������˹
$cmd = "	select a.EG_CODE from PER_LAYER_EMPSER a, PER_EMPSER_GROUP b where POEMS_ID=$POEMS_ID and a.EG_CODE=b.EG_CODE ";	
$db_dpis1->send_cmd($cmd);
$data1 = $db_dpis1->get_array();
$EG_CODE = $data1[EG_CODE];

// ��Ǩ�ͺ�Թ��͹���������� ��Т�鹷���˹�������� �����������ͧ�ʴ� alert ������䢡�͹
$cmd = "	select LAYERES_NO, LAYERES_SALARY as SALARY from PER_LAYER_EMPSER 
				where LAYER_SALARY=$PER_SALARY	  ";
$tmp_count_salpromote = $db_dpis1->send_cmd($cmd);
if ($tmp_count_salpromote <= 0) {
	$err_count++;
	$alert_err = true;
	$alert_err_text .= "		<span width='100%' $onmouse_event  style='color:#FF0000'> $err_count .	�ѵ���Թ��͹������������Т�鹷���˹�		[ �дѺ " . level_no_format($LEVEL_NO) . " : $PER_NAME ] </span>  \n ";
}

$non_promote = false; 
$non_promote_text = $SALP_REMARK = "";
$SALP_PERCENT = $SALP_SPSALARY = 0;
if(!$alert_err){
	// ��Ǩ�ͺ �ѹ�ҵԴ����������
	$cmd = " 	select 		PER_ID, sum(ABS_DAY) as DAY_SPOUSE, count(PER_ID) as TIME_SPOUSE 
					from 			PER_ABSENT
					where 		AB_CODE = '09' and PER_ID=$PER_ID
									$search_monthyear 
					group by 	PER_ID
					having 		(sum(ABS_DAY) > 0) or (count(PER_ID) > 0) ";
	$db_dpis1->send_cmd($cmd);
//echo "$cmd<br>";
	$data1 = $db_dpis1->get_array();
	$DAY_SPOUSE = $data1[DAY_SPOUSE];
	$TIME_SPOUSE = $data1[TIME_SPOUSE];
	if ( ($DAY_SPOUSE > 365) ) {
//$db_dpis1->show_error();	
		$non_promote = true;
		$non_promote_text .= "�������ö����͹����Թ��͹ $PER_NAME �� ���ͧ�ҡ�ҵԴ����������� �Թ�������ҷ���˹�<br>";
		$SALP_YN = 0;
		$SALP_LEVEL = 0;	
		$SALP_REASON = 1;				
	}		// 	if ( ($DAY_SPOUSE > 365) ) 	

	// ��Ǩ�ͺ�Թ��͹������
	$cmd = "	select max(LAYER_NO), max(LAYER_SALARY) as SALARY from PER_LAYER 
					where LEVEL_NO='$LEVEL_NO' and LAYER_TYPE=$LAYER_TYPE ";
	$db_dpis1->send_cmd($cmd);
	$data1 = $db_dpis1->get_array();
	$LAYER_SALARY = $data1[SALARY] + 0;
//echo "$PER_NAME -> PER_SALARY=$PER_SALARY == LAYER_SALARY=$LAYER_SALARY<br>";			
	if ( $PER_SALARY == $LAYER_SALARY)	 {
		$non_promote = true;
		$non_promote_text .= "�ѵ���Թ��͹���������� �������ö����͹����Թ��͹�ͧ $PER_NAME ��<br>";
		$SALP_YN = 0;
		$SALP_LEVEL = 0;
		$SALP_REASON = 2;	
		
		// �Թ��͹���������� ������Ѻ�Թ�ͺ᷹�����
		if ($SALQ_TYPE2 == 1) {
				$SALP_PERCENT = 2;
		} elseif ($SALQ_TYPE2 == 2) {
				$SALP_PERCENT = 4;
		}
		$SALP_SPSALARY = (($PER_SALARY * $SALP_PERCENT) / 100);			
	}

	// ��Ǩ�ͺ �ѹ�ҡԨ+�һ�������Թ 23 �ѹ 10 ���� �������Թ 18 �ѹ
	//  �һ��� + �ҡԨ
	$cmd = " 	select 		PER_ID, sum(ABS_DAY) as DAY_ILL, count(PER_ID) as TIME_ILL
					from 			PER_ABSENT
					where 		AB_CODE IN ('01', '03') and PER_ID=$PER_ID
									$search_monthyear 							
					group by 	PER_ID
					having 		(sum(ABS_DAY) > 0) or (count(PER_ID) > 0) ";
	$db_dpis1->send_cmd($cmd);
//echo "$cmd<br>";			
	$data1 = $db_dpis1->get_array();
	$DAY_ILL = $data1[DAY_ILL];
	$TIME_ILL = $data1[TIME_ILL];
	if ( ($DAY_ILL > 45) || ($TIME_ILL > 20) ) {
//$db_dpis1->show_error();	
		$non_promote = true;
		$non_promote_text .= "�������ö����͹����Թ��͹ $PER_NAME �� ���ͧ�ҡ�һ����Թ 23 �ѹ ���� �Թ 10 ����<br>";
		$SALP_YN = 0;
		$SALP_LEVEL = 0;				
		$SALP_REASON = 0;				
	}
	// ��� 
	$cmd = " 	select 		PER_ID, count(PER_ID) as TIME_LATE
					from 			PER_ABSENT 
					where 		AB_CODE IN ('10') and PER_ID=$PER_ID
									$search_monthyear 							
					group by 	PER_ID
					having 		count(PER_ID) > 0 ";
	$db_dpis1->send_cmd($cmd);	
//echo "$cmd<br>";			
	$data1 = $db_dpis1->get_array();
	$TIME_LATE = $data1[TIME_LATE];
	if ( $TIME_LATE > 36 )  {
//$db_dpis1->show_error();
		$non_promote = true;
		$non_promote_text .= "�������ö����͹����Թ��͹ $PER_NAME �� ���ͧ�ҡ����Թ 18 ����<br>";
		$SALP_YN = 0;
		$SALP_LEVEL = 0;	
		$SALP_REASON = 0;
	}

	// ��Ǩ�ͺ��� ͺ��/�٧ҹ/������
	$cmd = " select PER_ID, TRN_STARTDATE, TRN_ENDDATE from PER_TRAINING where PER_ID=$PER_ID ";
	$db_dpis1->send_cmd($cmd);
	$data1 = $db_dpis1->get_array(); 
	$tmp_date = explode("-", substr($data1[TRN_STARTDATE], 0, 10));
	$TRN_STARTDATE = mktime(0, 0, 0, $tmp_date[1], $tmp_date[2], $tmp_date[0]);
	$tmp_date = explode("-", substr($data1[TRN_ENDDATE], 0, 10));			
	$TRN_ENDDATE = mktime(0, 0, 0, $tmp_date[1], $tmp_date[2], $tmp_date[0]);
	$TIME_TRAINING = ($TRN_ENDDATE - $TRN_STARTDATE) / 2592000; // ���Թҷ���� = 30 �ѹ
//echo "TIME_TRAINING = $TIME_TRAINING<br>";			
	if ($TIME_TRAINING > 12) {
		$non_promote = true;
		$non_promote_text .= "�������ö����͹����Թ��͹ $PER_NAME �� ���ͧ�ҡ��ͺ��/�٧ҹ/������ �Թ�ӹǹ�ѹ����˹�<br>";
		$SALP_YN = 0;
		$SALP_LEVEL = 0;	
		$SALP_REASON = 3;
	}

	// ��Ǩ�ͺ��Һ�è��Թ 4 ��͹�����ѧ
	$cmd = "	select MOV_CODE, max(POH_EFFECTIVEDATE) as EFFECTIVEDATE from PER_POSITIONHIS 
					where MOV_CODE in ('101', '10110', '10120', '10130', '10140', '105', '10510', '10520') and PER_ID=$PER_ID 
					group by MOV_CODE ";
	$db_dpis1->send_cmd($cmd);
	$data1 = $db_dpis1->get_array();
	$tmp_date1 = explode("-", substr($data1[EFFECTIVEDATE], 0, 10));
	$tmp_date2 = explode("-", date("Y-m-d"));	
	$tmp_date_effect =  mktime(0, 0, 0, $tmp_date1[1], $tmp_date1[2], $tmp_date1[0]);
	$tmp_date_now = mktime(0, 0, 0, $tmp_date2[1], $tmp_date2[2], $tmp_date2[0]);
	$MONTH_FILL = ($tmp_date_now - $tmp_date_effect) / 2592000;		// ���Թҷ���� = 30 �ѹ
//echo "MONTH_FILL = $MONTH_FILL<br>";
	if ($MONTH_FILL < 4) {
//$db_dpis1->show_error();
		$non_promote = true;
		$non_promote_text .= "�������ö����͹����Թ��͹ $PER_NAME �� ���ͧ�ҡ��è��ѧ����Թ 4 ��͹<br>";
		$SALP_YN = 0;
		$SALP_LEVEL = 0;				
		$SALP_REASON = 4;
	}


	// �ӹǹ�š������͹����Թ��͹
	$cmd = " select c.EG_CODE, LAYERES_NO, LAYERES_SALARY, PER_CARDNO   
				  from PER_PERSONAL a, PER_POS_EMPSER b, PER_EMPSER_POS_NAME c, PER_LAYER_EMPSER d 
				  where a.PER_ID=$PER_ID and a.POEMS_ID=b.POEMS_ID and b.PN_CODE=c.PN_CODE and c.EG_CODE=d.EG_CODE
				  order by LAYERES_NO ";
	$db_dpis1->send_cmd($cmd);
	$data1 = $db_dpis1->get_array();
	
	$PER_CARDNO = trim($data1[PER_CARDNO])?	"'".$data1[PER_CARDNO]."'" : "NULL";	
	$UP_SALP_LEVEL = $data1[LAYERES_NO] + $SALP_LEVEL;
//echo " $PER_ID :: LAYER_NO=$data1[LAYER_NO] || LEVEL_NO=$data1[LEVEL_NO] || PER_SALARY=$data1[PER_SALARY] || LAYER_SALARY=$data1[LAYER_SALARY] <br>";
	$cmd = " select LAYERES_SALARY from PER_LAYER_EMPSER
				  where EG_CODE=$data1[EG_CODE] and LAYERES_NO='$UP_SALP_LEVEL'  
				  order by LAYERES_SALARY ";
	$db_dpis1->send_cmd($cmd);
	$data1 = $db_dpis1->get_array();				
	$SALP_SALARY_NEW = $data1[LAYERES_SALARY] + 0;				


	if ($SALP_SALARY_NEW == 0) 	$SALP_SALARY_NEW = $PER_SALARY;
	// �ѹ��������ռźѧ�Ѻ��
	if ($SALQ_TYPE2 == 1)			$SALP_DATE = ($SALQ_YEAR - 543) ."-04-01";
	elseif ($SALQ_TYPE2 == 2)		$SALP_DATE = ($SALQ_YEAR - 543) ."-10-01";
	if ($SALQ_TYPE1==1 && $SALQ_TYPE2==1) 				$tmp_SALQ_TYPE = 1;
	elseif ($SALQ_TYPE1==1 && $SALQ_TYPE2==2) 		$tmp_SALQ_TYPE = 2;
	elseif ($SALQ_TYPE1==2 && $SALQ_TYPE2==1) 		$tmp_SALQ_TYPE = 3;
	elseif ($SALQ_TYPE1==2 && $SALQ_TYPE2==2) 		$tmp_SALQ_TYPE = 4;
	elseif ($SALQ_TYPE1==3 && $SALQ_TYPE2==1) 		$tmp_SALQ_TYPE = 5;
	elseif ($SALQ_TYPE1==3 && $SALQ_TYPE2==2) 		$tmp_SALQ_TYPE = 6;	
	$SALP_REMARK = (trim($non_promote_text))? $non_promote_text : "";	
	
	// �ѹ�֡������ ŧ table PER_SALPROMOTE 
	$cmd = " 	insert into PER_SALPROMOTE
					(SALQ_YEAR, SALQ_TYPE, PER_ID, SALP_YN, SALP_LEVEL, SALP_SALARY_OLD, 
					 SALP_SALARY_NEW, SALP_PERCENT, SALP_SPSALARY, SALP_DATE, SALP_REMARK, 
					 SALP_REASON, DEPARTMENT_ID, UPDATE_USER, UPDATE_DATE)
					values 
					('$SALQ_YEAR', $tmp_SALQ_TYPE, $PER_ID, $SALP_YN, $SALP_LEVEL, $PER_SALARY, 
					 $SALP_SALARY_NEW, $SALP_PERCENT, $SALP_SPSALARY, '$SALP_DATE', '$SALP_REMARK', 
					 $SALP_REASON, $DEPARTMENT_ID, $SESS_USERID, '$UPDATE_DATE')";
	$db_dpis1->send_cmd($cmd);
	//$db_dpis1->show_error();
	//echo "<br>";					
} // end if(!$alert_err){

?>