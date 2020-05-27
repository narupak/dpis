<?
/*		if ($COM_TYPE=='5240') {
			$cmd1 = " select  	a.PER_ID, PER_SALARY, EL_CODE, POS_ID, LEVEL_NO, a.PER_CARDNO 
					  from PER_PERSONAL a, PER_EDUCATE b
					  where 	PER_TYPE=$COM_PER_TYPE and PER_STATUS=1 and a.PER_ID=b.PER_ID and 
							b.EDU_TYPE like '%1%' and ((trim(b.EL_CODE) = '10' and PER_SALARY >= 5760 and PER_SALARY <= 9600) or (trim(b.EL_CODE) = '30' and PER_SALARY >= 7100 and PER_SALARY <= 11600) or (trim(b.EL_CODE) = '40' and PER_SALARY >= 7940 and PER_SALARY <= 13200) or (trim(b.EL_CODE) = '60' and PER_SALARY >= 9700 and PER_SALARY <= 16300) or (trim(b.EL_CODE) = '80' and PER_SALARY >= 13110 and PER_SALARY <= 21500))
					  order by PER_ID   ";
			$count_temp = $db_dpis->send_cmd($cmd1);
			//$db_dpis->show_error();
			//echo "$cmd1<br>===================<br>";
			$cmd_seq = 0;
			while ($data = $db_dpis->get_array()) {
					$cmd_seq++;
					$TMP_PER_ID = trim($data[PER_ID]);
					$CMD_OLD_SALARY = trim($data[PER_SALARY]);
					$EL_CODE = trim($data[EL_CODE]);
					if ($EL_CODE=="10") {
						if ($CMD_OLD_SALARY >= 5760 && $CMD_OLD_SALARY <= 7100) {
							$CMD_SALARY = $CMD_OLD_SALARY + 340;
							if ($CMD_SALARY > 7390) $CMD_SALARY = 7390;
						} elseif ($CMD_OLD_SALARY >= 7110 && $CMD_OLD_SALARY <= 8400) {
							$CMD_SALARY = $CMD_OLD_SALARY + 280;
							if ($CMD_SALARY > 8560) $CMD_SALARY = 8560;
						} elseif ($CMD_OLD_SALARY >= 8410 && $CMD_OLD_SALARY <= 9600) {
							$CMD_SALARY = $CMD_OLD_SALARY + 340;
							if ($CMD_SALARY > 9610) $CMD_SALARY = 9610;
						}
					} elseif ($EL_CODE=="30") {
						if ($CMD_OLD_SALARY >= 7100 && $CMD_OLD_SALARY <= 8600) {
							$CMD_SALARY = $CMD_OLD_SALARY + 200;
							if ($CMD_SALARY > 8760) $CMD_SALARY = 8760;
						} elseif ($CMD_OLD_SALARY >= 8610 && $CMD_OLD_SALARY <= 10200) {
							$CMD_SALARY = $CMD_OLD_SALARY + 150;
							if ($CMD_SALARY > 10310) $CMD_SALARY = 10310;
						} elseif ($CMD_OLD_SALARY >= 10210 && $CMD_OLD_SALARY <= 11600) {
							$CMD_SALARY = $CMD_OLD_SALARY + 100;
							if ($CMD_SALARY > 11610) $CMD_SALARY = 11610;
						}
					} elseif ($EL_CODE=="40") {
						if ($CMD_OLD_SALARY >= 7940 && $CMD_OLD_SALARY <= 8700) {
							$CMD_SALARY = $CMD_OLD_SALARY + 760;
							if ($CMD_SALARY > 9410) $CMD_SALARY = 9410;
						} elseif ($CMD_OLD_SALARY >= 8710 && $CMD_OLD_SALARY <= 9500) {
							$CMD_SALARY = $CMD_OLD_SALARY + 700;
							if ($CMD_SALARY > 10110) $CMD_SALARY = 10110;
						} elseif ($CMD_OLD_SALARY >= 9510 && $CMD_OLD_SALARY <= 10300) {
							$CMD_SALARY = $CMD_OLD_SALARY + 600;
							if ($CMD_SALARY > 10810) $CMD_SALARY = 10810;
						} elseif ($CMD_OLD_SALARY >= 10310 && $CMD_OLD_SALARY <= 11100) {
							$CMD_SALARY = $CMD_OLD_SALARY + 500;
							if ($CMD_SALARY > 11510) $CMD_SALARY = 11510;
						} elseif ($CMD_OLD_SALARY >= 11110 && $CMD_OLD_SALARY <= 11900) {
							$CMD_SALARY = $CMD_OLD_SALARY + 400;
							if ($CMD_SALARY > 12210) $CMD_SALARY = 12210;
						} elseif ($CMD_OLD_SALARY >= 11910 && $CMD_OLD_SALARY <= 12700) {
							$CMD_SALARY = $CMD_OLD_SALARY + 300;
							if ($CMD_SALARY > 12910) $CMD_SALARY = 12910;
						} elseif ($CMD_OLD_SALARY >= 12710 && $CMD_OLD_SALARY <= 13200) {
							$CMD_SALARY = $CMD_OLD_SALARY + 200;
							if ($CMD_SALARY > 13210) $CMD_SALARY = 13210;
						}
					} elseif ($EL_CODE=="60") {
						if ($CMD_OLD_SALARY >= 9700 && $CMD_OLD_SALARY <= 10300) {
							$CMD_SALARY = $CMD_OLD_SALARY + 2300;
							if ($CMD_SALARY > 12510) $CMD_SALARY = 12510;
						} elseif ($CMD_OLD_SALARY >= 10310 && $CMD_OLD_SALARY <= 10900) {
							$CMD_SALARY = $CMD_OLD_SALARY + 2200;
							if ($CMD_SALARY > 13010) $CMD_SALARY = 13010;
						} elseif ($CMD_OLD_SALARY >= 10910 && $CMD_OLD_SALARY <= 11500) {
							$CMD_SALARY = $CMD_OLD_SALARY + 2100;
							if ($CMD_SALARY > 13410) $CMD_SALARY = 13410;
						} elseif ($CMD_OLD_SALARY >= 11510 && $CMD_OLD_SALARY <= 12100) {
							$CMD_SALARY = $CMD_OLD_SALARY + 1900;
							if ($CMD_SALARY > 13910) $CMD_SALARY = 13910;
						} elseif ($CMD_OLD_SALARY >= 12110 && $CMD_OLD_SALARY <= 12700) {
							$CMD_SALARY = $CMD_OLD_SALARY + 1800;
							if ($CMD_SALARY > 14310) $CMD_SALARY = 14310;
						} elseif ($CMD_OLD_SALARY >= 12710 && $CMD_OLD_SALARY <= 13300) {
							$CMD_SALARY = $CMD_OLD_SALARY + 1600;
							if ($CMD_SALARY > 14710) $CMD_SALARY = 14710;
						} elseif ($CMD_OLD_SALARY >= 13310 && $CMD_OLD_SALARY <= 13900) {
							$CMD_SALARY = $CMD_OLD_SALARY + 1400;
							if ($CMD_SALARY > 15010) $CMD_SALARY = 15010;
						} elseif ($CMD_OLD_SALARY >= 13910 && $CMD_OLD_SALARY <= 14500) {
							$CMD_SALARY = $CMD_OLD_SALARY + 1100;
							if ($CMD_SALARY > 15310) $CMD_SALARY = 15310;
						} elseif ($CMD_OLD_SALARY >= 14510 && $CMD_OLD_SALARY <= 15100) {
							$CMD_SALARY = $CMD_OLD_SALARY + 800;
							if ($CMD_SALARY > 15610) $CMD_SALARY = 15610;
						} elseif ($CMD_OLD_SALARY >= 15110 && $CMD_OLD_SALARY <= 15700) {
							$CMD_SALARY = $CMD_OLD_SALARY + 500;
							if ($CMD_SALARY > 15910) $CMD_SALARY = 15910;
						} elseif ($CMD_OLD_SALARY >= 15710 && $CMD_OLD_SALARY <= 16300) {
							$CMD_SALARY = $CMD_OLD_SALARY + 200;
							if ($CMD_SALARY > 16310) $CMD_SALARY = 16310;
						}
					} elseif ($EL_CODE=="80") {
						if ($CMD_OLD_SALARY >= 13110 && $CMD_OLD_SALARY <= 13900) {
							$CMD_SALARY = $CMD_OLD_SALARY + 3090;
							if ($CMD_SALARY > 16810) $CMD_SALARY = 16810;
						} elseif ($CMD_OLD_SALARY >= 13910 && $CMD_OLD_SALARY <= 14700) {
							$CMD_SALARY = $CMD_OLD_SALARY + 2900;
							if ($CMD_SALARY > 17410) $CMD_SALARY = 17410;
						} elseif ($CMD_OLD_SALARY >= 14710 && $CMD_OLD_SALARY <= 15500) {
							$CMD_SALARY = $CMD_OLD_SALARY + 2700;
							if ($CMD_SALARY > 17910) $CMD_SALARY = 17910;
						} elseif ($CMD_OLD_SALARY >= 15510 && $CMD_OLD_SALARY <= 16300) {
							$CMD_SALARY = $CMD_OLD_SALARY + 2400;
							if ($CMD_SALARY > 18410) $CMD_SALARY = 18410;
						} elseif ($CMD_OLD_SALARY >= 16310 && $CMD_OLD_SALARY <= 17100) {
							$CMD_SALARY = $CMD_OLD_SALARY + 2100;
							if ($CMD_SALARY > 18910) $CMD_SALARY = 18910;
						} elseif ($CMD_OLD_SALARY >= 17110 && $CMD_OLD_SALARY <= 17900) {
							$CMD_SALARY = $CMD_OLD_SALARY + 1800;
							if ($CMD_SALARY > 19410) $CMD_SALARY = 19410;
						} elseif ($CMD_OLD_SALARY >= 17910 && $CMD_OLD_SALARY <= 18700) {
							$CMD_SALARY = $CMD_OLD_SALARY + 1500;
							if ($CMD_SALARY > 19910) $CMD_SALARY = 19910;
						} elseif ($CMD_OLD_SALARY >= 18710 && $CMD_OLD_SALARY <= 19500) {
							$CMD_SALARY = $CMD_OLD_SALARY + 1200;
							if ($CMD_SALARY > 20410) $CMD_SALARY = 20410;
						} elseif ($CMD_OLD_SALARY >= 19510 && $CMD_OLD_SALARY <= 20300) {
							$CMD_SALARY = $CMD_OLD_SALARY + 900;
							if ($CMD_SALARY > 20910) $CMD_SALARY = 20910;
						} elseif ($CMD_OLD_SALARY >= 20310 && $CMD_OLD_SALARY <= 21100) {
							$CMD_SALARY = $CMD_OLD_SALARY + 600;
							if ($CMD_SALARY > 21410) $CMD_SALARY = 21410;
						} elseif ($CMD_OLD_SALARY >= 21110 && $CMD_OLD_SALARY <= 21500) {
							$CMD_SALARY = $CMD_OLD_SALARY + 300;
							if ($CMD_SALARY > 21510) $CMD_SALARY = 21510;
						}
					}
					$CMD_SPSALARY = 0;
					$PER_CARDNO = trim($data[PER_CARDNO]);
					// 215=ประเภทปรับเงินเดือน, 21510=ปรับเงินเดือนตามคุณวุฒิ, 21520=ปรับเงินเดือนตามกฎหมาย
					$MOV_CODE = "215";
					$CMD_DATE = '2010-10-01';
					
					$CMD_LEVEL = $LEVEL_NO = $CMD_LEVEL1 = $data[LEVEL_NO];
					$POS_ID = (trim($data[POS_ID]))? $data[POS_ID] : 'NULL';
					$cmd = " 	select POS_NO, PL_NAME, ORG_ID, ORG_ID_1, ORG_ID_2 from PER_POSITION a, PER_LINE b 
									where POS_ID=$POS_ID and a.PL_CODE=b.PL_CODE ";
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();
					$POS_NO = trim($data2[POS_NO]);
					$PL_NAME = trim($data2[PL_NAME]);
					if ($POS_NO || $PL_NAME)
						$CMD_POSITION = "$POS_NO\|$PL_NAME";
					$ORG_ID_1 = (trim($data2[ORG_ID]))? trim($data2[ORG_ID]) : 0;
					$ORG_ID_2 = (trim($data2[ORG_ID_1]))? trim($data2[ORG_ID_1]) : 0;
					$ORG_ID_3 = (trim($data2[ORG_ID_2]))? trim($data2[ORG_ID_2]) : 0;
					$CMD_ORG3 = $CMD_ORG4 = $CMD_ORG5 = "";
					$cmd = " select ORG_ID, ORG_NAME from PER_ORG where ORG_ID IN ($ORG_ID_1, $ORG_ID_2, $ORG_ID_3) ";
					$db_dpis2->send_cmd($cmd);
					while ( $data2 = $db_dpis2->get_array() ) {
						$temp_id = trim($data2[ORG_ID]);
						$CMD_ORG3 = ($temp_id == $ORG_ID_1)?  trim($data2[ORG_NAME]) : $CMD_ORG3;
						$CMD_ORG4 = ($temp_id == $ORG_ID_2)?  trim($data2[ORG_NAME]) : $CMD_ORG4;
						$CMD_ORG5 = ($temp_id == $ORG_ID_3)?  trim($data2[ORG_NAME]) : $CMD_ORG5;						
					}
					
					$cmd = " insert into PER_COMDTL
										(	COM_ID, CMD_SEQ, PER_ID, CMD_DATE, CMD_POSITION, 
											CMD_LEVEL, CMD_ORG3, CMD_ORG4, CMD_ORG5, CMD_OLD_SALARY,  
											POS_ID, POEM_ID, POEMS_ID, LEVEL_NO, CMD_SALARY, CMD_SPSALARY, 
											CMD_NOTE1, CMD_NOTE2, MOV_CODE, CMD_SAL_CONFIRM, PER_CARDNO, 
											UPDATE_USER, UPDATE_DATE,CMD_LEVEL_POS)
									 values
										(	$COM_ID, $cmd_seq, $TMP_PER_ID, '$CMD_DATE', '$CMD_POSITION', 
											'$CMD_LEVEL', '$CMD_ORG3', '$CMD_ORG4', '$CMD_ORG5', $CMD_OLD_SALARY, 
											$POS_ID, NULL, NULL, '$LEVEL_NO', $CMD_SALARY, $CMD_SPSALARY,
											'$CMD_NOTE1', '$CMD_NOTE2', '$MOV_CODE', 0, '$PER_CARDNO', 
											$SESS_USERID, '$UPDATE_DATE', '$CMD_LEVEL1') ";
					$db_dpis1->send_cmd($cmd);
//					$db_dpis1->show_error();
//					echo "$cmd<br>==================<br>";
			}	// end while 
*/
/*
			$cmd1 = " select  	a.PER_ID, PER_SALARY, EL_CODE, POS_ID, LEVEL_NO, a.PER_CARDNO 
					  from PER_PERSONAL a, PER_POSITIONEDUCATE b
					  where 	PER_TYPE=$COM_PER_TYPE and PER_STATUS=1 and a.PER_ID=b.PER_ID and 
							b.EDU_TYPE like '%1%' and ((trim(b.EL_CODE) = '10' and PER_SALARY >= 5760 and PER_SALARY <= 9600) or (trim(b.EL_CODE) = '30' and PER_SALARY >= 7100 and PER_SALARY <= 11600) or (trim(b.EL_CODE) = '40' and PER_SALARY >= 7940 and PER_SALARY <= 13200) or (trim(b.EL_CODE) = '60' and PER_SALARY >= 9700 and PER_SALARY <= 16300) or (trim(b.EL_CODE) = '80' and PER_SALARY >= 13110 and PER_SALARY <= 21500))
					  order by PER_ID   ";
			$count_temp = $db_dpis->send_cmd($cmd1);
			//$db_dpis->show_error();
			//echo "$cmd1<br>===================<br>";
*/
		if ($DEPARTMENT_ID > 0) $where .= "and a.DEPARTMENT_ID = $DEPARTMENT_ID ";
		if ($ORG_ID_DTL && $ORG_ID_DTL != "NULL") $where .= " and b.ORG_ID = $ORG_ID_DTL ";		 
		if ($RPT_N) {
			$cmd1 = " select  	PER_ID, PER_SALARY, a.POEMS_ID, a.LEVEL_NO, PER_CARDNO 
					  from PER_PERSONAL a, PER_POS_EMPSER b
					  where 	a.POEMS_ID=b.POEMS_ID and PER_TYPE=$COM_PER_TYPE and PER_STATUS=1 $where
					  order by PER_ID ";
			$count_temp = $db_dpis->send_cmd($cmd1);
			//$db_dpis->show_error();
			//echo "$cmd1<br>===================<br>";
			$cmd_seq = 0;
			while ($data = $db_dpis->get_array()) {
					$cmd_seq++;
					$TMP_PER_ID = trim($data[PER_ID]);
					$CMD_OLD_SALARY = trim($data[PER_SALARY]);
					$CMD_SALARY = (ceil($CMD_OLD_SALARY * (($COM_SAL_PERCENT/100))  /10))*10 ;
					$CMD_SALARY = $CMD_SALARY + $CMD_OLD_SALARY;
					$CMD_SPSALARY = 0;
					$PER_CARDNO = trim($data[PER_CARDNO]);
					// 215=ประเภทปรับเงินเดือน, 21510=ปรับเงินเดือนตามคุณวุฒิ, 21520=ปรับเงินเดือนตามกฎหมาย
					$MOV_CODE = "21520";
					$CMD_DATE = $COM_EFFECTIVEDATE;
					
					$CMD_LEVEL = $LEVEL_NO = $CMD_LEVEL1 = $data[LEVEL_NO];
					$POEMS_ID = (trim($data[POEMS_ID]))? $data[POEMS_ID] : 'NULL';
					$cmd = " 	select POEMS_NO, EP_NAME, DEPARTMENT_ID, ORG_ID, ORG_ID_1, ORG_ID_2 from PER_POS_EMPSER a, PER_EMPSER_POS_NAME b 
									where POEMS_ID=$POEMS_ID and a.EP_CODE=b.EP_CODE ";
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();
					$CMD_POS_NO = trim($data2[POEMS_NO]);
					$CMD_POSITION = trim($data2[EP_NAME]);
					$DEPARTMENT_ID = (trim($data2[DEPARTMENT_ID]))? trim($data2[DEPARTMENT_ID]) : 0;
					$ORG_ID_1 = (trim($data2[ORG_ID]))? trim($data2[ORG_ID]) : 0;
					$ORG_ID_2 = (trim($data2[ORG_ID_1]))? trim($data2[ORG_ID_1]) : 0;
					$ORG_ID_3 = (trim($data2[ORG_ID_2]))? trim($data2[ORG_ID_2]) : 0;
					$CMD_ORG1 = $CMD_ORG2 = $CMD_ORG3 = $CMD_ORG4 = $CMD_ORG5 = "";
					$cmd = " select ORG_ID, ORG_NAME from PER_ORG where ORG_ID IN ($DEPARTMENT_ID, $ORG_ID_1, $ORG_ID_2, $ORG_ID_3) ";
					$db_dpis2->send_cmd($cmd);
					while ( $data2 = $db_dpis2->get_array() ) {
						$temp_id = trim($data2[ORG_ID]);
						$CMD_ORG2 = ($temp_id == $DEPARTMENT_ID)?  trim($data2[ORG_NAME]) : $CMD_ORG2;
						$CMD_ORG3 = ($temp_id == $ORG_ID_1)?  trim($data2[ORG_NAME]) : $CMD_ORG3;
						$CMD_ORG4 = ($temp_id == $ORG_ID_2)?  trim($data2[ORG_NAME]) : $CMD_ORG4;
						$CMD_ORG5 = ($temp_id == $ORG_ID_3)?  trim($data2[ORG_NAME]) : $CMD_ORG5;						
					}
					
					$cmd = " select ORG_ID_REF from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();
					$MINISTRY_ID = $data2[ORG_ID_REF];
					
					$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();
					$CMD_ORG1 = $data2[ORG_NAME];
					
					$cmd = " insert into PER_COMDTL (COM_ID, CMD_SEQ, PER_ID, CMD_DATE, CMD_POSITION, 
											CMD_LEVEL, CMD_ORG1, CMD_ORG2, CMD_ORG3, CMD_ORG4, CMD_ORG5, CMD_OLD_SALARY,  
											POS_ID, POEM_ID, POEMS_ID, LEVEL_NO, CMD_SALARY, CMD_SPSALARY, 
											CMD_NOTE1, CMD_NOTE2, MOV_CODE, CMD_SAL_CONFIRM, PER_CARDNO, 
											UPDATE_USER, UPDATE_DATE, CMD_LEVEL_POS, CMD_POS_NO_NAME, CMD_POS_NO)
									 values ($COM_ID, $cmd_seq, $TMP_PER_ID, '$CMD_DATE', '$CMD_POSITION', 
											'$CMD_LEVEL', '$CMD_ORG1', '$CMD_ORG2', '$CMD_ORG3', '$CMD_ORG4', '$CMD_ORG5', $CMD_OLD_SALARY, 
											NULL, NULL, $POEMS_ID, '$LEVEL_NO', $CMD_SALARY, $CMD_SPSALARY,
											'$CMD_NOTE1', '$CMD_NOTE2', '$MOV_CODE', 0, '$PER_CARDNO', 
											$SESS_USERID, '$UPDATE_DATE', '$CMD_LEVEL1', '$CMD_POS_NO_NAME', '$CMD_POS_NO') ";
					$db_dpis1->send_cmd($cmd);
//					$db_dpis1->show_error();
//					echo "$cmd<br>==================<br>";
			}	// end while
		} else {
			$cmd1 = " select  	PER_ID, PER_SALARY, a.LAYER_SALARY, b.LAYER_SALARY as NEW_SALARY, POEMS_ID, PER_CARDNO, 
							a.LAYER_TYPE, a.LEVEL_NO as PER_LEVEL, a.LAYER_NO, b.LAYER_TYPE, b.LEVEL_NO, b.LAYER_NO 
					  from 	PER_LAYER a, PER_LAYER_NEW b, PER_PERSONAL c 
					  where 	PER_TYPE=$COM_PER_TYPE and PER_STATUS=1 and a.LAYER_TYPE=b.LAYER_TYPE and 
							a.LEVEL_NO=b.LEVEL_NO and a.LAYER_NO=b.LAYER_NO and a.LAYER_SALARY=c.PER_SALARY  and a.LEVEL_NO = c.LEVEL_NO_SALARY and a.LAYER_TYPE = 1
					  order by PER_ID   ";
			$count_temp = $db_dpis->send_cmd($cmd1);
			//$db_dpis->show_error();
			//echo "$cmd1<br>===================<br>";
			$cmd_seq = 0;
			while ($data = $db_dpis->get_array()) {
					$cmd_seq++;
					$TMP_PER_ID = trim($data[PER_ID]);
					$CMD_OLD_SALARY = trim($data[PER_SALARY]);
					$CMD_SALARY = trim($data[NEW_SALARY]);
					$CMD_SPSALARY = 0;
					$PER_CARDNO = trim($data[PER_CARDNO]);
					// 215=ประเภทปรับเงินเดือน, 21510=ปรับเงินเดือนตามคุณวุฒิ, 21520=ปรับเงินเดือนตามกฎหมาย
					$MOV_CODE = "215";
					//$tmp_date = explode("-", substr(trim($data[SALP_DATE]), 0, 10));
					$CMD_DATE = trim($COM_DATE);
					
					$CMD_LEVEL = $LEVEL_NO = $CMD_LEVEL1 = $data[PER_LEVEL];
					$POEMS_ID = (trim($data[POEMS_ID]))? $data[POEMS_ID] : 'NULL';
					$cmd = " 	select POEMS_NO, EP_NAME, ORG_ID, ORG_ID_1, ORG_ID_2 from PER_POS_EMPSER a, PER_EMPSER_POS_NAME b 
									where POEMS_ID=$POEMS_ID and a.EP_CODE=b.EP_CODE ";
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();
					$CMD_POS_NO = trim($data2[POEMS_NO]);					
					$CMD_POSITION = trim($data2[EP_NAME]);
					$ORG_ID_1 = (trim($data2[ORG_ID]))? trim($data2[ORG_ID]) : 0;
					$ORG_ID_2 = (trim($data2[ORG_ID_1]))? trim($data2[ORG_ID_1]) : 0;
					$ORG_ID_3 = (trim($data2[ORG_ID_2]))? trim($data2[ORG_ID_2]) : 0;
					$CMD_ORG3 = $CMD_ORG4 = $CMD_ORG5 = "";
					$cmd = " select ORG_ID, ORG_NAME from PER_ORG where ORG_ID IN ($ORG_ID_1, $ORG_ID_2, $ORG_ID_3) ";
					$db_dpis2->send_cmd($cmd);
					while ( $data2 = $db_dpis2->get_array() ) {
						$temp_id = trim($data2[ORG_ID]);
						$CMD_ORG3 = ($temp_id == $ORG_ID_1)?  trim($data2[ORG_NAME]) : $CMD_ORG3;
						$CMD_ORG4 = ($temp_id == $ORG_ID_2)?  trim($data2[ORG_NAME]) : $CMD_ORG4;
						$CMD_ORG5 = ($temp_id == $ORG_ID_3)?  trim($data2[ORG_NAME]) : $CMD_ORG5;						
					}
	
					$cmd = " insert into PER_COMDTL (COM_ID, CMD_SEQ, PER_ID, CMD_DATE, CMD_POSITION, 
											CMD_LEVEL, CMD_ORG1, CMD_ORG2, CMD_ORG3, CMD_ORG4, CMD_ORG5, CMD_OLD_SALARY,  
											POS_ID, POEM_ID, POEMS_ID, LEVEL_NO, CMD_SALARY, CMD_SPSALARY, 
											CMD_NOTE1, CMD_NOTE2, MOV_CODE, CMD_SAL_CONFIRM, PER_CARDNO, 
											UPDATE_USER, UPDATE_DATE,CMD_LEVEL_POS, CMD_POS_NO_NAME, CMD_POS_NO)
									 values ($COM_ID, $cmd_seq, $TMP_PER_ID, '$CMD_DATE', '$CMD_POSITION', 
											'$CMD_LEVEL', '$CMD_ORG1', '$CMD_ORG2', '$CMD_ORG3', '$CMD_ORG4', '$CMD_ORG5', $CMD_OLD_SALARY, 
											$POS_ID, $POEM_ID, $POEMS_ID, '$LEVEL_NO', $CMD_SALARY, $CMD_SPSALARY,
											'$CMD_NOTE1', '$CMD_NOTE2', '$MOV_CODE', 0, '$PER_CARDNO', 
											$SESS_USERID, '$UPDATE_DATE', '$CMD_LEVEL1', '$CMD_POS_NO_NAME', '$CMD_POS_NO') ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
					//echo "$cmd<br>==================<br>";
			}	// end while
		}	// end if
		if ($count_temp)
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูลรายละเอียดบัญชีแนบท้ายคำสั่งปรับอัตราเงินเดือนใหม่     [".trim($COM_ID)." : ".$COM_NO." : ".$COM_TYPE."]");		
?>