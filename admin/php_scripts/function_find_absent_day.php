<?
function find_absent_day($STARTDATE, $STARTPERIOD, $ENDDATE, $ENDPERIOD, $AB_COUNT, $PER_ID, $chkSave) {
	global $DPISDB, $dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd;
	
	$db_dpis_spec = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$search_approve ="";
	if($linkfrom=="personal_absenthis"){
		$search_approve =" and APPROVE_FLAG=2";		//�֧��੾���ѹ��� �����١ ���͹حҵ ��
	}

	//	�Ѻ�ӹǹ�ѹ�� ���������ѹ��ش����� - �ҷԵ�� ����ѹ��ش�Ҫ��� (table) X
	//�ѹ�������
	$STARTDATE = str_replace("-","/", $STARTDATE);
	$arr_temp = explode("/", $STARTDATE);
	if ((int)$arr_temp[2] > 100) {	// d/m/y
		if ((int)$arr_temp[2] > 2300) {	// �繻� �.�	.
			$START_DAY = $arr_temp[0];
			$START_MONTH = $arr_temp[1];
			$START_YEAR = $arr_temp[2] - 543;
		} else {	// �繻� �.�	.
			$START_DAY = $arr_temp[0];
			$START_MONTH = $arr_temp[1];
			$START_YEAR = $arr_temp[2];
		}
	} else {	// y/m/d
		if ((int)$arr_temp[0] > 2300) {	// �繻� �.�	.
			$START_DAY = $arr_temp[2];
			$START_MONTH = $arr_temp[1];
			$START_YEAR = $arr_temp[0] - 543;
		} else {	// �繻� �.�	.
			$START_DAY = $arr_temp[2];
			$START_MONTH = $arr_temp[1];
			$START_YEAR = $arr_temp[0];
		}
	}
	$STARTDATE = $START_YEAR ."-". $START_MONTH ."-". $START_DAY;
	$STARTDATE_ce_era = $START_YEAR ."-". $START_MONTH ."-". $START_DAY;
	
	//�֧�ѹ���
	$ENDDATE = str_replace("-","/", $ENDDATE);
	$arr_temp = explode("/", $ENDDATE);
	if ((int)$arr_temp[2] > 100) {	// d/m/y
		if ((int)$arr_temp[2] > 2300) {	// �繻� �.�	.
			$END_DAY = $arr_temp[0];
			$END_MONTH = $arr_temp[1];
			$END_YEAR = $arr_temp[2] - 543;
		} else {	// �繻� �.�	.
			$END_DAY = $arr_temp[0];
			$END_MONTH = $arr_temp[1];
			$END_YEAR = $arr_temp[2];
		}
	} else {	// y/m/d
		if ((int)$arr_temp[0] > 2300) {	// �繻� �.�	.
			$END_DAY = $arr_temp[2];
			$END_MONTH = $arr_temp[1];
			$END_YEAR = $arr_temp[0] - 543;
		} else {	// �繻� �.�	.
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

		if($AB_COUNT == 2){		//���Ѻ�ѹ��ش��ҹ��***		// PERIOD 1 = ������� / 2 = ���觺��� / 3 = ����ѹ
			$DAY_START_NUM = date("w", mktime(0, 0, 0, $START_MONTH, $START_DAY, $START_YEAR));
			$DAY_END_NUM = date("w", mktime(0, 0, 0, $END_MONTH, $END_DAY, $END_YEAR));
			if(($DAY_START_NUM == 0 || $DAY_START_NUM == 6)  && ($DAY_END_NUM == 0 || $DAY_END_NUM == 6)){			// ������� / ����ش ���ѹ����� - �ҷԵ��  (�ҹ͡ loop)
				$ABSENT_START_DAY = 0;
			}else if(($DAY_START_NUM == 0 || $DAY_START_NUM == 6)  && ($DAY_END_NUM != 0 || $DAY_END_NUM != 6)){	// ������� ���ѹ����� - �ҷԵ��  / ����ش ���ѹ������ (�ҹ͡ loop)
				$ABSENT_START_DAY = 0;
			}else if(($DAY_START_NUM != 0 || $DAY_START_NUM != 6)  && ($DAY_END_NUM == 0 || $DAY_END_NUM == 6)){	// �������  ���ѹ������ / ����ش ���ѹ����� - �ҷԵ��  (�ҹ͡ loop)
				$ABSENT_START_DAY = 0;	
			}else if(($DAY_START_NUM != 0 && $DAY_START_NUM != 6)  && ($DAY_END_NUM != 0 && $DAY_END_NUM != 6)){	 // ������� / ����ش ������ѹ����� - �ҷԵ��  (�ҹ͡ loop)
//				$ABSENT_START_DAY = 1;	// �ͧ����͡��͹ � loop �Ѻ�ѹ�ѹ�Ѻ�ء�ѹ�������� ��� ���ѹ��ش����� loop �������� �����������ǹ���ӷ���
				$ABSENT_START_DAY = 0;
			}
			
			// ���ѹ����������ѹ��ش������� ?
			if($DPISDB=="odbc") 
				$cmd = " select HOL_NAME from PER_HOLIDAY where LEFT(HOL_DATE, 10)='$START_YEAR-$START_MONTH-$START_DAY' ";
			elseif($DPISDB=="oci8") 
				$cmd = " select HOL_NAME from PER_HOLIDAY where SUBSTR(HOL_DATE, 1, 10)='$START_YEAR-$START_MONTH-$START_DAY' ";
			elseif($DPISDB=="mysql")
				$cmd = " select HOL_NAME from PER_HOLIDAY where LEFT(HOL_DATE, 10)='$START_YEAR-$START_MONTH-$START_DAY' ";
			$IS_HOLIDAY_START = $db_dpis_spec->send_cmd($cmd);
			
			// ���ѹ����ش���ѹ��ش������� ?
			if($DPISDB=="odbc") 
				$cmd = " select HOL_NAME from PER_HOLIDAY where LEFT(HOL_DATE, 10)='$END_YEAR-$END_MONTH-$END_DAY' ";
			elseif($DPISDB=="oci8") 
				$cmd = " select HOL_NAME from PER_HOLIDAY where SUBSTR(HOL_DATE, 1, 10)='$END_YEAR-$END_MONTH-$END_DAY' ";
			elseif($DPISDB=="mysql")
				$cmd = " select HOL_NAME from PER_HOLIDAY where LEFT(HOL_DATE, 10)='$END_YEAR-$END_MONTH-$END_DAY' ";
			$IS_HOLIDAY_END = $db_dpis_spec->send_cmd($cmd);
			if($IS_HOLIDAY_START || $IS_HOLIDAY_END){		// ����ѹ����� / ����ش���ѹ��ش�Ҫ���  �����Ѻ�ç���
				$ABSENT_START_DAY = 0;
			}

			// �ѡ�����ѹ	// PERIOD 1 = ������� / 2 = ���觺��� / 3 = ����ѹ
			// �Դ�ӹǳ�����ѹ �óչ�����Ѻ�����ѹ�����ѹ������� / ����ش���ѹ��ش�������� �������ҤԴ	
			// ��� � / � / � ������������ش���ѹ���ǡѹ����繤����ѹ ������ / ��������ͧ�ѡ 0.5
            $period_err = 0;
            $ABSENT_2_STARTPERIOD = 0;
            $ABSENT_2_ENDPERIOD = 0;
			if(trim("$START_YEAR-$START_MONTH-$START_DAY")==trim("$END_YEAR-$END_MONTH-$END_DAY")){
				if($STARTPERIOD == 1 && $ENDPERIOD == 2){		// ������� �繤������ �������ش �繤��觺���
                	$period_err = 1;	//	"�ѹ���ǡѹ ������������ ����ش���觺��� ����� 1 �ѹ �ô����� '����ѹ'";
				} else if($STARTPERIOD == 2 && $ENDPERIOD == 1){		// ��������������ش �繤����ѹ��駤��  [�����㴵��˹���� 0.5]
                	$period_err = 2;	//	"�ѹ���ǡѹ ��������觺��� ����ش������� �Դ�ٻẺ �ô������";
				} else if($STARTPERIOD != 3 && $ENDPERIOD != 3){		// ��������������ش �繤����ѹ��駤�� �����ҡѹ ���� �����ѹ ������ͺ���  [�����㴵��˹���� 0.5]
                    $ABSENT_2_STARTPERIOD = 0.5;
				}else{
					if($STARTPERIOD == 3 && ($ENDPERIOD == 1 || $ENDPERIOD == 2))		
                    	$period_err = 3; // "�ѹ���ǡѹ ���������ѹ ����ش���觺��� ����� 1 �ѹ �ô�������ش�� '����ѹ'";
					if($ENDPERIOD == 3 && ($STARTPERIOD == 1 || $STARTPERIOD == 2))	
                    	$period_err = 4; // "�ѹ���ǡѹ ������������ ����ش����ѹ ����� 1 �ѹ �ô���������� '����ѹ'";
				}
			}else{
				if($STARTPERIOD == 1){
					$period_err = 5;	// "������ѹ�á�繤������ ��鹺��� ���١��ͧ �ô���¡�����ա 1 ��¡��";
				} else if($STARTPERIOD == 2){		//  ������ѹ�á���觺���
					if(($DAY_START_NUM == 0 || $DAY_START_NUM == 6) || $IS_HOLIDAY_START){	// ������ѹ��ش�Ҫ������͹ѡ�ѵġ�� ���Ѻ�ѹ
						$ABSENT_2_STARTPERIOD = 0;
					}else{  // case ���� �ѹ������
						$ABSENT_2_STARTPERIOD = 0.5;
					}
				}
				if($ENDPERIOD == 2){
					$period_err = 6;	// "����ش�������觺��� ������ ���١��ͧ �ô���¡�����ա 1 ��¡��";
				} else if($ENDPERIOD == 1){
					if(($DAY_END_NUM == 0 || $DAY_END_NUM == 6) || $IS_HOLIDAY_END){	// ������ѹ��ش�Ҫ������͹ѡ�ѵġ�� ���Ѻ�ѹ
						$ABSENT_2_ENDPERIOD = 0;
					}else{  // case ���� �ѹ������
						$ABSENT_2_ENDPERIOD = 0.5;
					}
				}
			}
		}else if($AB_COUNT == 1){		// �Ѻ�ѹ��ش��ҹ��
			$ABSENT_START_DAY = 1; 
			// ��� � / � / � ������������ش���ѹ���ǡѹ����繤����ѹ ������ / ��������ͧ�ѡ 0.5
            $period_err = "";
            $ABSENT_2_STARTPERIOD = 0;
            $ABSENT_2_ENDPERIOD = 0;
			if(trim("$START_YEAR-$START_MONTH-$START_DAY")==trim("$END_YEAR-$END_MONTH-$END_DAY")){
				if($STARTPERIOD == 1 && $ENDPERIOD == 2){		// ������� �繤������ �������ش �繤��觺���
                	$period_err = 1;	// "�ѹ���ǡѹ ������������ ����ش���觺��� ����� 1 �ѹ\n�ô����� '����ѹ'";
				} else if($STARTPERIOD == 2 && $ENDPERIOD == 1){		// ��������������ش �繤����ѹ��駤��  [�����㴵��˹���� 0.5]
                	$period_err = 2;	// "�ѹ���ǡѹ ��������觺��� ����ش������� �Դ�ٻẺ\n�ô������١��ͧ";
				} else if($STARTPERIOD != 3 && $ENDPERIOD != 3){		// ��������������ش �繤����ѹ��駤�� �����ҡѹ ���� �����ѹ ������ͺ���  [�����㴵��˹���� 0.5]
                    $ABSENT_2_STARTPERIOD = 0.5;
				}else{
					if($STARTPERIOD == 3 && ($ENDPERIOD == 1 || $ENDPERIOD == 2))		
                    	$period_err = 3;	// "�ѹ���ǡѹ ���������ѹ ����ش���觺��� ����� 1 �ѹ\n�ô�������ش�� '����ѹ'";
					if($ENDPERIOD == 3 && ($STARTPERIOD == 1 || $STARTPERIOD == 2))	
                    	$period_err = 4;	// "�ѹ���ǡѹ ������������ ����ش����ѹ ����� 1 �ѹ\n�ô���������� '����ѹ'";
				}
			} else {	// �ѹ�����ҡѹ
				if($STARTPERIOD == 1){		// ������ѹ�á�繤������ ��鹺��� ���١��ͧ �ô���¡�����ա 1 ��¡��
					$period_err = 5;	// "������ѹ�á�繤������ ��鹺��� ���١��ͧ\n�ô���¡�����ա 1 ��¡��";
				} else if($ENDPERIOD == 2){		// ����ش �繤����ѹ��ѧ
					$period_err = 6;	// "����ش�������觺��� ������ ���١��ͧ �ô���¡�����ա 1 ��¡��";
				} else {
                	if($STARTPERIOD == 2){		// ������� �繤����ѹ��ѧ
                    	$ABSENT_2_STARTPERIOD = 0.5;	
					}
                    if($ENDPERIOD == 1){		// ����ش �繤����ѹ�á
                    	$ABSENT_2_ENDPERIOD = 0.5;
					}
				}            
            }
		}
		
        $ABSENT_START_DAY = 0;	// loop ���仹��Ѻ��������ء�ѹ���� �����繵�ͧ�� ABSENT_START_DAY �ա
		$TMP_ENDDATE = date("Y-m-d", mktime(0, 0, 0, $END_MONTH, ($END_DAY + 1), $END_YEAR));
		$arr_temp = explode("-", $TMP_ENDDATE);
		$END_DAY1 = $arr_temp[2];
		$END_MONTH1 = $arr_temp[1];
		$END_YEAR1 = $arr_temp[0];
		while($START_YEAR!=$END_YEAR1 || $START_MONTH!=$END_MONTH1 || $START_DAY!=$END_DAY1){
//			echo "START_YEAR(".$START_YEAR.")!=END_YEAR(".$END_YEAR1.") || START_MONTH(".$START_MONTH.")!=END_MONTH(".$END_MONTH1.") || START_DAY(".$START_DAY.")!=END_DAY(".$END_DAY1.")<br>";
			$DAY_NUM = date("w", mktime(0, 0, 0, $START_MONTH, $START_DAY, $START_YEAR));

			if($AB_COUNT == 2){		//���Ѻ�ѹ��ش��ҹ��***
				if($DPISDB=="odbc") 
					$cmd = " select HOL_NAME from PER_HOLIDAY where LEFT(HOL_DATE, 10)='$START_YEAR-$START_MONTH-$START_DAY' ";
				elseif($DPISDB=="oci8") 
					$cmd = " select HOL_NAME from PER_HOLIDAY where SUBSTR(HOL_DATE, 1, 10)='$START_YEAR-$START_MONTH-$START_DAY' ";
				elseif($DPISDB=="mysql")
					$cmd = " select HOL_NAME from PER_HOLIDAY where LEFT(HOL_DATE, 10)='$START_YEAR-$START_MONTH-$START_DAY' ";
				$IS_HOLIDAY = $db_dpis_spec->send_cmd($cmd);

				if($DAY_NUM > 0 && $DAY_NUM < 6 && !$IS_HOLIDAY) $ABSENT_DAY++;
//				echo "���Ѻ�ѹ��ش ($ABSENT_DAY)<br>";
			}elseif($AB_COUNT == 1){	//�Ѻ
				$ABSENT_DAY++;
//				echo "�Ѻ�ѹ��ش ($ABSENT_DAY)<br>";
			} // end if

			$TMP_STARTDATE = date("Y-m-d", mktime(0, 0, 0, $START_MONTH, ($START_DAY + 1), $START_YEAR));
			$arr_temp = explode("-", $TMP_STARTDATE);
			$START_DAY = $arr_temp[2];
			$START_MONTH = $arr_temp[1];
			$START_YEAR = $arr_temp[0];
		} // end while

		// ��ػ����ѹ�ش���� //
		$ABSENT_DAY = ($ABSENT_DAY + $ABSENT_START_DAY);		

		// �ѡ�����ѹ	// PERIOD 1 = ������� / 2 = ���觺��� / 3 = ����ѹ	//������Ѻ�ѹ��ش ��� check ����ѹ���������� / ����ش����Ҥ����ѹ(���/����)��� ���ѹ��ش�Ҫ���/ �����-�ҷԵ��? ���������ͧ����ҤԴ
		$ABSENT_DAY = ($ABSENT_DAY -  ($ABSENT_2_STARTPERIOD + $ABSENT_2_ENDPERIOD));

		/*** ��ҨмԴ
		if($STARTPERIOD == 2 && $ENDPERIOD == 1) $ABSENT_DAY -= 0.5;
		if($STARTPERIOD != 3) $ABSENT_DAY -= 0.5;
		if($ENDPERIOD != 3 && $TMP_STARTDATE != $ENDDATE) $ABSENT_DAY -= 0.5;
		if($STARTPERIOD == 3 && $ENDPERIOD != 3 && $TMP_STARTDATE == $ENDDATE) $ABSENT_DAY -= 0.5;
		***/
		//$ABSENT_DAY = $ABSENT_DAY ."+".$STARTPERIOD."&&".$ENDPERIOD;
	} // end if

	// ======================================================
	// ===== START process ������ա���ҫ�ӡѺ�ѹ������������������� =====
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
			// ===== ������ա�����������㹢�������͹����׹�ѹ����������� =====	
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
				// ===== ���ѹ���������
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
				// ===== ���ѹ������ش
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
				// ===== ���ѹ���������������ҧ�ѹ�������������ѹ������ش� db		
				$cmd = "select PER_ID from PER_ABSENT
								where 	PER_ID=$PER_ID $absid_condition $search_approve and 
								ABS_STARTDATE <= '$tmp_date' and ABS_ENDDATE >= '$tmp_date' ";
				$count_duplicate_date = $db_dpis_spec->send_cmd($cmd);
				//echo "$cmd<br />count_duplicate_date=$count_duplicate_date<br />";				
			}	

//			echo ++$num . " �ѹ || $tmp_date != $ENDDATE_ce_era<br>";				
			$num_day++;
			$tmp = mktime(0, 0, 0, $START_MONTH, $START_DAY+$num_day, $START_YEAR);	
			$tmp_date = date("Y-m-d", $tmp);			
		}	// end while 
	}	// end if
	// ===== END process ������ա���ҫ�ӡѺ�ѹ������������������� ===== 
	// ====================================================
//<!--<script>-->
	if (!$INVALID_STARTDATE && !$INVALID_ENDDATE && !$count_confirm && !$count_duplicate_date && !$period_err) {
		// check �ѹ�Ҿѡ��͹�Թ
		$result_sum_AB_CODE_04 = 1;
//		parent_document_form1_VAR_DAY = "$ABSENT_DAY";
		if($parent_document_form1_AB_CODE == '04' && ($parent_document_form1_AB_COUNT_04 > 0)){
			if(((float)$parent_document_form1_AB_COUNT_04 - (float)parent_document_form1_VAR_DAY) < 0){ // �ѹ����Ҿѡ��͹�� �Թ�ѹ����ѧ������Է���
				$msg_sum_AB_CODE_04 = "�������ö�Ҿѡ��͹�Թ "+(int)$parent_document_form1_AB_COUNT_04+" �ѹ��";
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
			  $p_err_str = "�ѹ���ǡѹ ������������ ����ش���觺��� ����� 1 �ѹ\n�ô����� '����ѹ'";
			  break;
			case "2" :
			  $p_err_str = "�ѹ���ǡѹ ��������觺��� ����ش������� ���١��ͧ\n�ô���";
			  break;
			case "3" :
			  $p_err_str = "�ѹ���ǡѹ ���������ѹ ����ش���觺��� ����� 1 �ѹ\n�ô�������ش�� '����ѹ'";
			  break;
			case "4" :
			  $p_err_str = "�ѹ���ǡѹ ������������ ����ش����ѹ ����� 1 �ѹ\n�ô���������� '����ѹ'";
			  break;
			case "5" :
			  $p_err_str = "������ѹ�á�繤������ ��鹺��� ���١��ͧ\n�ô���¡�����ա 1 ��¡��";
			  break;
			case "6" :
			  $p_err_str = "����ش�������觺��� ������ ���١��ͧ\n�ô���¡�����ա 1 ��¡��";
			  break;
			default:
			  $p_err_str = "";
		}
//		echo "err-->".$p_err.",".$p_err_str."<br />";
		$parent_document_form1_VAR_DAY = '';
//		echo "".$p_err_str."<br />";
	} else if ($count_confirm) {
		echo "�������ö���������š����/���/�Ҵ��  ���ͧ�ҡ�ա���׹�ѹ����������<br>";
	} else if ($count_duplicate_date) {
		echo "�������š������ѹ����ͧ��������� ��س����͡�ѹ��������ա����<br>";
	} else {
		echo "INVALID_STARTDATE=$INVALID_STARTDATE, ".(($INVALID_STARTDATE && $INVALID_ENDDATE)? '<br>' : '' ).", ".$INVALID_ENDDATE."<br>";
	}
//<!--</ script>-->
	$ret = $ABSENT_DAY;
	
	return $ret;
}
?>
