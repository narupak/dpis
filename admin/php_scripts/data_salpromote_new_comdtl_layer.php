<?
      
	if(!$CMD_SPSALARY) $CMD_SPSALARY = 0;
//	if ($COM_TYPE=='5240') {
	if ($COM_CONDITION==2) {
		if ($DEPARTMENT_ID > 0) $where .= "and a.DEPARTMENT_ID = $DEPARTMENT_ID ";
		if ($ORG_ID_DTL && $ORG_ID_DTL != "NULL") $where .= " and c.ORG_ID = $ORG_ID_DTL ";		 
		$cmd1 = " select  	a.PER_ID, PER_SALARY, EL_CODE, a.POS_ID, a.LEVEL_NO, a.PER_CARDNO 
				  from PER_PERSONAL a, PER_EDUCATE b, PER_POSITION c
				  where 	PER_TYPE=$COM_PER_TYPE and PER_STATUS=1 and a.PER_ID=b.PER_ID and a.POS_ID=c.POS_ID and 
						b.EDU_TYPE like '%2%' and ((trim(b.EL_CODE) = '10' and PER_SALARY >= 6410 and PER_SALARY <= 15500) or (trim(b.EL_CODE) = '30' and PER_SALARY >= 7670 and PER_SALARY <= 18900) or (trim(b.EL_CODE) = '40' and PER_SALARY >= 9140 and PER_SALARY <= 21700) or (trim(b.EL_CODE) = '60' and PER_SALARY >= 12600 and PER_SALARY <= 26100) or (trim(b.EL_CODE) = '80' and PER_SALARY >= 17010 and PER_SALARY <= 35400)) $where
				  order by PER_ID ";
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
					if ($CMD_OLD_SALARY >= 6410 && $CMD_OLD_SALARY <= 8200) {
						$CMD_SALARY = $CMD_OLD_SALARY + 1210;
						if ($CMD_SALARY > 9210) $CMD_SALARY = 9210;
					} elseif ($CMD_OLD_SALARY >= 8210 && $CMD_OLD_SALARY <= 10000) {
						$CMD_SALARY = $CMD_OLD_SALARY + 1000;
						if ($CMD_SALARY > 10810) $CMD_SALARY = 10810;
					} elseif ($CMD_OLD_SALARY >= 10010 && $CMD_OLD_SALARY <= 11800) {
						$CMD_SALARY = $CMD_OLD_SALARY + 800;
						if ($CMD_SALARY > 12410) $CMD_SALARY = 12410;
					} elseif ($CMD_OLD_SALARY >= 11810 && $CMD_OLD_SALARY <= 13600) {
						$CMD_SALARY = $CMD_OLD_SALARY + 600;
						if ($CMD_SALARY > 14010) $CMD_SALARY = 14010;
					} elseif ($CMD_OLD_SALARY >= 13610 && $CMD_OLD_SALARY <= 15500) {
						$CMD_SALARY = $CMD_OLD_SALARY + 400;
						if ($CMD_SALARY > 15510) $CMD_SALARY = 15510;
					}
				} elseif ($EL_CODE=="30") {
					if ($CMD_OLD_SALARY >= 7670 && $CMD_OLD_SALARY <= 9500) {
						$CMD_SALARY = $CMD_OLD_SALARY + 1630;
						if ($CMD_SALARY > 10810) $CMD_SALARY = 10810;
					} elseif ($CMD_OLD_SALARY >= 9510 && $CMD_OLD_SALARY <= 11400) {
						$CMD_SALARY = $CMD_OLD_SALARY + 1300;
						if ($CMD_SALARY > 12410) $CMD_SALARY = 12410;
					} elseif ($CMD_OLD_SALARY >= 11410 && $CMD_OLD_SALARY <= 13300) {
						$CMD_SALARY = $CMD_OLD_SALARY + 1000;
						if ($CMD_SALARY > 14010) $CMD_SALARY = 14010;
					} elseif ($CMD_OLD_SALARY >= 13310 && $CMD_OLD_SALARY <= 15200) {
						$CMD_SALARY = $CMD_OLD_SALARY + 700;
						if ($CMD_SALARY > 15610) $CMD_SALARY = 15610;
					} elseif ($CMD_OLD_SALARY >= 15210 && $CMD_OLD_SALARY <= 17100) {
						$CMD_SALARY = $CMD_OLD_SALARY + 400;
						if ($CMD_SALARY > 17210) $CMD_SALARY = 17210;
					} elseif ($CMD_OLD_SALARY >= 17110 && $CMD_OLD_SALARY <= 18900) {
						$CMD_SALARY = $CMD_OLD_SALARY + 100;
						if ($CMD_SALARY > 18910) $CMD_SALARY = 18910;
					}
				} elseif ($EL_CODE=="40") {
					if ($CMD_OLD_SALARY >= 9140 && $CMD_OLD_SALARY <= 10900) {
						$CMD_SALARY = $CMD_OLD_SALARY + 2540;
						if ($CMD_SALARY > 13010) $CMD_SALARY = 13010;
					} elseif ($CMD_OLD_SALARY >= 10910 && $CMD_OLD_SALARY <= 12700) {
						$CMD_SALARY = $CMD_OLD_SALARY + 2100;
						if ($CMD_SALARY > 14410) $CMD_SALARY = 14410;
					} elseif ($CMD_OLD_SALARY >= 12710 && $CMD_OLD_SALARY <= 14500) {
						$CMD_SALARY = $CMD_OLD_SALARY + 1700;
						if ($CMD_SALARY > 15810) $CMD_SALARY = 15810;
					} elseif ($CMD_OLD_SALARY >= 14510 && $CMD_OLD_SALARY <= 16300) {
						$CMD_SALARY = $CMD_OLD_SALARY + 1300;
						if ($CMD_SALARY > 17210) $CMD_SALARY = 17210;
					} elseif ($CMD_OLD_SALARY >= 16310 && $CMD_OLD_SALARY <= 18100) {
						$CMD_SALARY = $CMD_OLD_SALARY + 900;
						if ($CMD_SALARY > 18610) $CMD_SALARY = 18610;
					} elseif ($CMD_OLD_SALARY >= 18110 && $CMD_OLD_SALARY <= 19900) {
						$CMD_SALARY = $CMD_OLD_SALARY + 500;
						if ($CMD_SALARY > 20010) $CMD_SALARY = 20010;
					} elseif ($CMD_OLD_SALARY >= 19910 && $CMD_OLD_SALARY <= 21700) {
						$CMD_SALARY = $CMD_OLD_SALARY + 100;
						if ($CMD_SALARY > 21710) $CMD_SALARY = 21710;
					}
				} elseif ($EL_CODE=="60") {
					if ($CMD_OLD_SALARY >= 12600 && $CMD_OLD_SALARY <= 14100) {
						$CMD_SALARY = $CMD_OLD_SALARY + 2700;
						if ($CMD_SALARY > 16510) $CMD_SALARY = 16510;
					} elseif ($CMD_OLD_SALARY >= 14110 && $CMD_OLD_SALARY <= 15600) {
						$CMD_SALARY = $CMD_OLD_SALARY + 2400;
						if ($CMD_SALARY > 17710) $CMD_SALARY = 17710;
					} elseif ($CMD_OLD_SALARY >= 15610 && $CMD_OLD_SALARY <= 17100) {
						$CMD_SALARY = $CMD_OLD_SALARY + 2100;
						if ($CMD_SALARY > 18910) $CMD_SALARY = 18910;
					} elseif ($CMD_OLD_SALARY >= 17110 && $CMD_OLD_SALARY <= 18600) {
						$CMD_SALARY = $CMD_OLD_SALARY + 1800;
						if ($CMD_SALARY > 20110) $CMD_SALARY = 20110;
					} elseif ($CMD_OLD_SALARY >= 18610 && $CMD_OLD_SALARY <= 20100) {
						$CMD_SALARY = $CMD_OLD_SALARY + 1500;
						if ($CMD_SALARY > 21310) $CMD_SALARY = 21310;
					} elseif ($CMD_OLD_SALARY >= 20110 && $CMD_OLD_SALARY <= 21600) {
						$CMD_SALARY = $CMD_OLD_SALARY + 1200;
						if ($CMD_SALARY > 22510) $CMD_SALARY = 22510;
					} elseif ($CMD_OLD_SALARY >= 21610 && $CMD_OLD_SALARY <= 23100) {
						$CMD_SALARY = $CMD_OLD_SALARY + 900;
						if ($CMD_SALARY > 23710) $CMD_SALARY = 23710;
					} elseif ($CMD_OLD_SALARY >= 23110 && $CMD_OLD_SALARY <= 24600) {
						$CMD_SALARY = $CMD_OLD_SALARY + 600;
						if ($CMD_SALARY > 24910) $CMD_SALARY = 24910;
					} elseif ($CMD_OLD_SALARY >= 24610 && $CMD_OLD_SALARY <= 26100) {
						$CMD_SALARY = $CMD_OLD_SALARY + 300;
						if ($CMD_SALARY > 26110) $CMD_SALARY = 26110;
					}
				} elseif ($EL_CODE=="80") {
					if ($CMD_OLD_SALARY >= 17010 && $CMD_OLD_SALARY <= 19600) {
						$CMD_SALARY = $CMD_OLD_SALARY + 1990;
						if ($CMD_SALARY > 21310) $CMD_SALARY = 21310;
					} elseif ($CMD_OLD_SALARY >= 19610 && $CMD_OLD_SALARY <= 22200) {
						$CMD_SALARY = $CMD_OLD_SALARY + 1700;
						if ($CMD_SALARY > 23610) $CMD_SALARY = 23610;
					} elseif ($CMD_OLD_SALARY >= 22210 && $CMD_OLD_SALARY <= 24800) {
						$CMD_SALARY = $CMD_OLD_SALARY + 1400;
						if ($CMD_SALARY > 25910) $CMD_SALARY = 25910;
					} elseif ($CMD_OLD_SALARY >= 24810 && $CMD_OLD_SALARY <= 27400) {
						$CMD_SALARY = $CMD_OLD_SALARY + 1100;
						if ($CMD_SALARY > 28210) $CMD_SALARY = 28210;
					} elseif ($CMD_OLD_SALARY >= 27410 && $CMD_OLD_SALARY <= 30000) {
						$CMD_SALARY = $CMD_OLD_SALARY + 800;
						if ($CMD_SALARY > 30510) $CMD_SALARY = 30510;
					} elseif ($CMD_OLD_SALARY >= 30010 && $CMD_OLD_SALARY <= 32600) {
						$CMD_SALARY = $CMD_OLD_SALARY + 500;
						if ($CMD_SALARY > 32810) $CMD_SALARY = 32810;
					} elseif ($CMD_OLD_SALARY >= 32610 && $CMD_OLD_SALARY <= 35400) {
						$CMD_SALARY = $CMD_OLD_SALARY + 200;
						if ($CMD_SALARY > 35410) $CMD_SALARY = 35410;
					}
				}
				$CMD_SPSALARY = 0;
				$PER_CARDNO = trim($data[PER_CARDNO]);
				// 215=ประเภทปรับเงินเดือน, 21510=ปรับเงินเดือนตามคุณวุฒิ, 21520=ปรับเงินเดือนตามกฎหมาย
				if ($BKK_FLAG==1) $MOV_CODE = "66";
				else $MOV_CODE = "215";
				$CMD_DATE = '2012-01-01';
				
				$CMD_LEVEL = $LEVEL_NO = $CMD_LEVEL1 = $data[LEVEL_NO];
				$POS_ID = (trim($data[POS_ID]))? $data[POS_ID] : 'NULL';
				$cmd = " 	select POS_NO_NAME, POS_NO, PL_NAME, DEPARTMENT_ID, ORG_ID, ORG_ID_1, ORG_ID_2 from PER_POSITION a, PER_LINE b 
								where POS_ID=$POS_ID and a.PL_CODE=b.PL_CODE ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$CMD_POS_NO_NAME = trim($data2[POS_NO_NAME]);
				$CMD_POS_NO = trim($data2[POS_NO]);
				$PL_NAME = trim($data2[PL_NAME]);
				$CMD_POSITION = $PL_NAME;
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
										UPDATE_USER, UPDATE_DATE,CMD_LEVEL_POS, CMD_POS_NO_NAME, CMD_POS_NO)
								 values ($COM_ID, $cmd_seq, $TMP_PER_ID, '$CMD_DATE', '$CMD_POSITION', 
										'$CMD_LEVEL', '$CMD_ORG1', '$CMD_ORG2', '$CMD_ORG3', '$CMD_ORG4', '$CMD_ORG5', $CMD_OLD_SALARY, 
										$POS_ID, NULL, NULL, '$LEVEL_NO', $CMD_SALARY, $CMD_SPSALARY,
										'$CMD_NOTE1', '$CMD_NOTE2', '$MOV_CODE', 0, '$PER_CARDNO', 
										$SESS_USERID, '$UPDATE_DATE', '$CMD_LEVEL1', '$CMD_POS_NO_NAME', '$CMD_POS_NO') ";
				$db_dpis1->send_cmd($cmd);
//					$db_dpis1->show_error();
//					echo "$cmd<br>==================<br>";
		}	// end while 
	} else {
            if ($COM_CONDITION==4){
                if ($DEPARTMENT_ID > 0) $where .= "and a.DEPARTMENT_ID = $DEPARTMENT_ID ";
		if ($ORG_ID_DTL && $ORG_ID_DTL != "NULL") $where .= " and c.ORG_ID = $ORG_ID_DTL ";
                $CMD_DATE = '2016-10-01';
                $MOV_CODE = "21520";/*ปรับเงินเดือนตามกฏหมาย*/
                $sql_sub="select   psh.PER_ID ,SAH_ID, SAH_EFFECTIVEDATE, pm.MOV_NAME, SAH_SALARY,SAH_SALARY_EXTRA, SAH_DOCNO, SAH_DOCDATE, SAH_ENDDATE, 
                                                   SAH_PERCENT_UP, SAH_SALARY_UP, LEVEL_NO, SAH_PAY_NO, SAH_POS_NO, SAH_POS_NO_NAME, 
                                                 SAH_LAST_SALARY, SM_CODE, AUDIT_FLAG, SAH_KF_YEAR, SAH_KF_CYCLE,psh.MOV_CODE
                                              from    PER_SALARYHIS psh, PER_MOVMENT pm 
                                              where  psh.MOV_CODE=pm.mov_code
                                              and SAH_EFFECTIVEDATE like '2016-10-01%'
                                              and SAH_SALARY_EXTRA is not null  and SAH_SALARY_EXTRA not in (0)
                                              AND pm.MOV_CODE in (select pm.MOV_CODE from PER_MOVMENT pm where pm.mov_name like 'เลื่อนเงินเดือน%' ) ";
                
		$cmd1 = " select a.PER_ID, PER_SALARY,  a.POS_ID, a.LEVEL_NO, a.PER_CARDNO ,p.SAH_SALARY,p.SAH_SALARY_EXTRA ,p.MOV_CODE
                            from PER_PERSONAL a,  ($sql_sub) p , PER_POSITION c
                            where PER_TYPE=$COM_PER_TYPE and PER_STATUS=1 
                              and a.PER_ID=p.PER_ID  and a.POS_ID=c.POS_ID 
                              $where  
                            order by PER_ID ";
		$count_temp = $db_dpis->send_cmd($cmd1);
		//$db_dpis->show_error();
		//echo "<pre>$cmd1<br>===================<br>";
		$cmd_seq = 0;
		while ($data = $db_dpis->get_array()) {
                    $cmd_seq++;
                    $TMP_PER_ID = trim($data[PER_ID]);
                    $CMD_OLD_SALARY = trim($data[PER_SALARY]);
                    $CMD_SPSALARY=trim($data[SAH_SALARY_EXTRA]);
                    
                   
                                      
                    
                    $CMD_LEVEL = $LEVEL_NO = $CMD_LEVEL1 = $data[LEVEL_NO];
                    
                    if(trim($LEVEL_NO)=='M2'){
                        $CMD_SALARY=$CMD_OLD_SALARY;
                        $CMD_SPSALARY=$CMD_SPSALARY;
                    }else{
                        $CMD_SALARY=$CMD_OLD_SALARY+$CMD_SPSALARY;
                        $MOD_CMD_SALARY =  fmod($CMD_SALARY,10);
                        if($MOD_CMD_SALARY!=0){
                            $CMD_SALARY = ($CMD_SALARY-$MOD_CMD_SALARY)+10;
                        }
                    }
                    
                    
                    $POS_ID = (trim($data[POS_ID]))? $data[POS_ID] : 'NULL';
                    $cmd = " 	select POS_NO_NAME, POS_NO, PL_NAME, DEPARTMENT_ID, ORG_ID, ORG_ID_1, ORG_ID_2 from PER_POSITION a, PER_LINE b 
                                                    where POS_ID=$POS_ID and a.PL_CODE=b.PL_CODE ";
                    $db_dpis2->send_cmd($cmd);
                    $data2 = $db_dpis2->get_array();
                    $CMD_POS_NO_NAME = trim($data2[POS_NO_NAME]);
                    $CMD_POS_NO = trim($data2[POS_NO]);
                    $PL_NAME = trim($data2[PL_NAME]);
                    $CMD_POSITION = $PL_NAME;
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
                    
                    
                    
                    $cmd = " 	select LEVEL_NO, POH_POS_NO_NAME, POH_POS_NO, PL_CODE, PM_CODE, PN_CODE, EP_CODE, 
                                    ORG_ID_1, ORG_ID_2, ORG_ID_3, POH_UNDER_ORG1, POH_UNDER_ORG2, POH_ORG1, POH_ORG2, POH_ORG3 
                            from PER_POSITIONHIS a, PER_MOVMENT b 
                            where PER_ID=$TMP_PER_ID and a.MOV_CODE=b.MOV_CODE and POH_EFFECTIVEDATE <= '$COM_EFFECTIVEDATE' and 
                            MOV_SUB_TYPE!='0' and MOV_SUB_TYPE!='5' and MOV_SUB_TYPE!='7'
                            order by POH_EFFECTIVEDATE desc, POH_SEQ_NO desc ";
                    $db_dpis2->send_cmd($cmd);
                    $data2 = $db_dpis2->get_array();
                    if (trim($data2[LEVEL_NO])) $CMD_LEVEL = trim($data2[LEVEL_NO]);
                    if (trim($data2[POH_POS_NO_NAME])) $CMD_POS_NO_NAME = trim($data2[POH_POS_NO_NAME]);
                    if (trim($data2[POH_POS_NO]) && trim($data2[POH_POS_NO]) != "-") $CMD_POS_NO = trim($data2[POH_POS_NO]);
                    if (trim($data2[PL_CODE])) {
                            $PL_CODE = trim($data2[PL_CODE]);
                            $cmd = " select PL_NAME from PER_LINE where PL_CODE='$PL_CODE' ";
                            $db_dpis1->send_cmd($cmd);
                            $data1 = $db_dpis1->get_array();
                            $PL_NAME = $data1[PL_NAME];
                    }
                    if (trim($data2[PM_CODE])) $PM_CODE = trim($data2[PM_CODE]);
                    if (trim($data2[PN_CODE])) $PN_CODE = trim($data2[PN_CODE]);
                    if (trim($data2[EP_CODE])) $EP_CODE = trim($data2[EP_CODE]);
                    if (trim($data2[ORG_ID_1])) $MINISTRY_ID = trim($data2[ORG_ID_1]);
                    if (trim($data2[ORG_ID_2])) $DEPARTMENT_ID = trim($data2[ORG_ID_2]);
                    if (trim($data2[POH_ORG1])) $CMD_ORG1 = trim($data2[POH_ORG1]);
                    if (trim($data2[POH_ORG2])) $DEPARTMENT_NAME = $CMD_ORG2 = trim($data2[POH_ORG2]);
//                    if (trim($data2[POH_ORG3])) $ORG_NAME = $CMD_ORG3 = trim($data2[POH_ORG3]);
//                    if (trim($data2[POH_UNDER_ORG1])) $ORG_NAME_1 = $CMD_ORG4 = trim($data2[POH_UNDER_ORG1]);
//                    if (trim($data2[POH_UNDER_ORG2])) $ORG_NAME_2 = $CMD_ORG5 = trim($data2[POH_UNDER_ORG2]);
                    
                    $cmd = " select PM_NAME from PER_MGT where PM_CODE='$PM_CODE' ";
                    $db_dpis2->send_cmd($cmd);
                    $data2 = $db_dpis2->get_array();
                    $PM_NAME = $data2[PM_NAME];
                    if ($PM_NAME) $CMD_POSITION = "$CMD_POSITION\|$PM_NAME";

                    $cmd = " select POSITION_LEVEL from PER_PERSONAL a, PER_LEVEL b where a.POS_ID=$POS_ID and a.LEVEL_NO=b.LEVEL_NO ";
                    $db_dpis2->send_cmd($cmd);
                    $data2 = $db_dpis2->get_array();
                    $POSITION_LEVEL = $data2[POSITION_LEVEL];

                    if ($PM_NAME) $PL_NAME_WORK = $PM_NAME." (".$PL_NAME.$POSITION_LEVEL.")";
                    else $PL_NAME_WORK = $PL_NAME.$POSITION_LEVEL;
                    
                    if ($ORG_NAME=="-") $ORG_NAME = "";
                    if ($ORG_NAME_1=="-") $ORG_NAME_1 = "";
                    if ($ORG_NAME_2=="-") $ORG_NAME_2 = "";
                    if ($OT_CODE == "03") 
                            if (!$ORG_NAME_2 && !$ORG_NAME_1 && $DEPARTMENT_NAME=="กรมการปกครอง") 
                                    $ORG_NAME_WORK = "ที่ทำการปกครอง".$ORG_NAME." ".$ORG_NAME;
                            else 
                                    $ORG_NAME_WORK = trim($ORG_NAME_2." ".$ORG_NAME_1." ".$ORG_NAME);
                    elseif ($OT_CODE == "01") $ORG_NAME_WORK = trim($ORG_NAME_2." ".$ORG_NAME_1." ".$ORG_NAME." ".$DEPARTMENT_NAME);
                    else $ORG_NAME_WORK = trim($ORG_NAME_2." ".$ORG_NAME_1." ".$ORG_NAME);
                    
                    
                    
                    $CMD_NOTE1='เงินตอบแทนพิเศษ '.$CMD_SPSALARY; /*เดิมไม่มี เพิ่มเติมเพื่อให้ดึงยอดแสดง ค่าตอบแทนพิเศษ*/
                    $CMD_NOTE2='โอนค่าตอบแทนพิเศษไปเป็นอัตราเงินเดือนใหม่';
                    if(trim($LEVEL_NO)=='M2'){
                        if($CMD_SPSALARY>0){
                            $CMD_SPSALARY=9999;
                        }
                    }
                    $cmd = " insert into PER_COMDTL (COM_ID, CMD_SEQ, PER_ID, CMD_DATE, CMD_POSITION, 
                                            CMD_LEVEL, CMD_ORG1, CMD_ORG2, CMD_ORG3, CMD_ORG4, CMD_ORG5, CMD_OLD_SALARY,  
                                            POS_ID, POEM_ID, POEMS_ID, LEVEL_NO, CMD_SALARY, CMD_SPSALARY, 
                                            CMD_NOTE1, CMD_NOTE2, MOV_CODE, CMD_SAL_CONFIRM, PER_CARDNO, 
                                            UPDATE_USER, UPDATE_DATE, PL_NAME_WORK, ORG_NAME_WORK, 
                                            CMD_LEVEL_POS, CMD_POS_NO_NAME, CMD_POS_NO)
                             values ($COM_ID, $cmd_seq, $TMP_PER_ID, '$CMD_DATE', '$CMD_POSITION', 
                                            '$CMD_LEVEL', '$CMD_ORG1', '$CMD_ORG2', '$CMD_ORG3', '$CMD_ORG4', '$CMD_ORG5', $CMD_OLD_SALARY, 
                                            $POS_ID, NULL, NULL, '$LEVEL_NO', $CMD_SALARY, 0,
                                            '$CMD_NOTE1', '$CMD_NOTE2', '$MOV_CODE', 0, '$PER_CARDNO', 
                                            $SESS_USERID, '$UPDATE_DATE', '$PL_NAME_WORK', '$ORG_NAME_WORK', 
                                            '$CMD_LEVEL1', '$CMD_POS_NO_NAME', '$CMD_POS_NO') ";
                    /*$cmd = " insert into PER_COMDTL (COM_ID, CMD_SEQ, PER_ID, CMD_DATE, CMD_POSITION, 
                                CMD_LEVEL, CMD_ORG1, CMD_ORG2, CMD_ORG3, CMD_ORG4, CMD_ORG5, CMD_OLD_SALARY,  
                                POS_ID, POEM_ID, POEMS_ID, LEVEL_NO, CMD_SALARY, CMD_SPSALARY, 
                                CMD_NOTE1, CMD_NOTE2, MOV_CODE, CMD_SAL_CONFIRM, PER_CARDNO, 
                                UPDATE_USER, UPDATE_DATE,CMD_LEVEL_POS, CMD_POS_NO_NAME, CMD_POS_NO,PL_NAME_WORK)
                             values ($COM_ID, $cmd_seq, $TMP_PER_ID, '$CMD_DATE', '$CMD_POSITION', 
                                '$CMD_LEVEL', '$CMD_ORG1', '$CMD_ORG2', '$CMD_ORG3', '$CMD_ORG4', '$CMD_ORG5', $CMD_OLD_SALARY, 
                                $POS_ID, NULL, NULL, '$LEVEL_NO', $CMD_SALARY, 0,
                                '$CMD_NOTE1', '$CMD_NOTE2', '$MOV_CODE', 0, '$PER_CARDNO', 
                                $SESS_USERID, '$UPDATE_DATE', '$CMD_LEVEL1', '$CMD_POS_NO_NAME', '$CMD_POS_NO') ";*/
                   $db_dpis1->send_cmd($cmd);
                  // echo '<pre>'.$cmd.'<br><br>';
                }
               // echo '<pre>'.$cmd1;
               // die('');
                
            }else{
		if ($COM_LEVEL_NO) {
			$arr_level_no = (array) null;
			foreach ($COM_LEVEL_NO as $search_level_no)	{
				if ($search_level_no) $arr_level_no[] = "'$search_level_no'";
		//		echo "search_level_no=$search_level_no<br>";
			}
			if (count($arr_level_no) > 0) $where .= "and trim(a.LEVEL_NO) in (".implode(",",$arr_level_no).") ";
		}
		if ($DEPARTMENT_ID > 0) $where .= "and a.DEPARTMENT_ID = $DEPARTMENT_ID ";
		if ($ORG_ID_DTL && $ORG_ID_DTL != "NULL") $where .= " and b.ORG_ID = $ORG_ID_DTL ";		 
		if ($RPT_N) {
			if ($COM_CONDITION==3) {
				$COM_SAL_PERCENT = 4;
				$COM_EFFECTIVEDATE = "2014-12-01";
			} else {
				$where .= " and PER_STATUS=1 "; 
			}
			if ($SESS_DEPARTMENT_NAME=="กรมการปกครอง") {
				if($DPISDB=="odbc") $order_str = "CLng(PAY_NO)";
				elseif($DPISDB=="oci8")  $order_str = "to_number(replace(PAY_NO,'-',''))";
				elseif($DPISDB=="mysql")  $order_str = "PAY_NO+0";
				$cmd1 = " select  	PER_ID, PER_SALARY, a.PAY_ID as POS_ID, a.LEVEL_NO, PER_CARDNO, PER_STARTDATE, PER_OCCUPYDATE, PER_STATUS, PER_POSDATE, MOV_CODE 
						  from PER_PERSONAL a, PER_POSITION b
						  where 	a.PAY_ID=b.POS_ID and PER_TYPE=$COM_PER_TYPE $where
						  order by $order_str ";
			} else {
				if($DPISDB=="odbc") $order_str = "CLng(POS_NO)";
				elseif($DPISDB=="oci8")  $order_str = "to_number(replace(POS_NO,'-',''))";
				elseif($DPISDB=="mysql")  $order_str = "POS_NO+0";
				$cmd1 = " select  	PER_ID, PER_SALARY, a.POS_ID, a.LEVEL_NO, PER_CARDNO, PER_STARTDATE, PER_OCCUPYDATE, PER_STATUS, PER_POSDATE, MOV_CODE
						  from PER_PERSONAL a, PER_POSITION b
						  where 	a.POS_ID=b.POS_ID and PER_TYPE=$COM_PER_TYPE $where 
						  order by $order_str ";
			}
			$count_temp = $db_dpis->send_cmd($cmd1);
			//$db_dpis->show_error();
			//echo "$cmd1<br>===================<br>";
			$cmd_seq = 0;
			while ($data = $db_dpis->get_array()) {
				$cmd_seq++;
				$TMP_PER_ID = trim($data[PER_ID]);
				$CMD_OLD_SALARY = trim($data[PER_SALARY]);
				$cmd = " select SAH_SALARY from PER_SALARYHIS where PER_ID=$TMP_PER_ID and SAH_EFFECTIVEDATE > '$COM_EFFECTIVEDATE' ";
				$count_data = $db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				if ($count_data) {
					$cmd = " select SAH_SALARY from PER_SALARYHIS where PER_ID=$TMP_PER_ID and SAH_EFFECTIVEDATE <= '$COM_EFFECTIVEDATE' 
									order by SAH_EFFECTIVEDATE desc, SAH_DOCDATE desc ";
					$count_data = $db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();
					if ($count_data) $CMD_OLD_SALARY = $data2[SAH_SALARY];
				}

				$TMP_OLD_SALARY = $CMD_SALARY = $CMD_OLD_SALARY;
				$CMD_LEVEL = $LEVEL_NO = $CMD_LEVEL1 = trim($data[LEVEL_NO]);
				$PER_CARDNO = trim($data[PER_CARDNO]);
				$PER_STARTDATE = trim($data[PER_STARTDATE]);
				$PER_OCCUPYDATE = trim($data[PER_OCCUPYDATE]);
				$PER_STATUS = trim($data[PER_STATUS]);
				$PER_POSDATE = trim($data[PER_POSDATE]);
				$MOV_CODE = trim($data[MOV_CODE]);

				$CMD_POS_NO_NAME = $CMD_POS_NO = $PL_NAME = $PM_CODE = $PN_CODE = $EP_CODE = $CMD_ORG1 = $CMD_ORG2 = $CMD_ORG3 = $CMD_ORG4 = $CMD_ORG5 = "";
				if ($COM_CONDITION==3) {
					$cmd = " 	select LEVEL_NO, POH_POS_NO_NAME, POH_POS_NO, PL_CODE, PM_CODE, PN_CODE, EP_CODE, 
													ORG_ID_1, ORG_ID_2, ORG_ID_3, POH_UNDER_ORG1, POH_UNDER_ORG2, POH_ORG1, POH_ORG2, POH_ORG3 
									from PER_POSITIONHIS a, PER_MOVMENT b 
									where PER_ID=$TMP_PER_ID and a.MOV_CODE=b.MOV_CODE and POH_EFFECTIVEDATE <= '$COM_EFFECTIVEDATE' and 
									MOV_SUB_TYPE!='0' and MOV_SUB_TYPE!='5' and MOV_SUB_TYPE!='7'
									order by POH_EFFECTIVEDATE desc, POH_SEQ_NO desc ";
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();
					if (trim($data2[LEVEL_NO])) $CMD_LEVEL = trim($data2[LEVEL_NO]);
					if (trim($data2[POH_POS_NO_NAME])) $CMD_POS_NO_NAME = trim($data2[POH_POS_NO_NAME]);
					if (trim($data2[POH_POS_NO]) && trim($data2[POH_POS_NO]) != "-") $CMD_POS_NO = trim($data2[POH_POS_NO]);
					if (trim($data2[PL_CODE])) {
						$PL_CODE = trim($data2[PL_CODE]);
						$cmd = " select PL_NAME from PER_LINE where PL_CODE='$PL_CODE' ";
						$db_dpis1->send_cmd($cmd);
						$data1 = $db_dpis1->get_array();
						$PL_NAME = $data1[PL_NAME];
					}
					if (trim($data2[PM_CODE])) $PM_CODE = trim($data2[PM_CODE]);
					if (trim($data2[PN_CODE])) $PN_CODE = trim($data2[PN_CODE]);
					if (trim($data2[EP_CODE])) $EP_CODE = trim($data2[EP_CODE]);
					if (trim($data2[ORG_ID_1])) $MINISTRY_ID = trim($data2[ORG_ID_1]);
					if (trim($data2[ORG_ID_2])) $DEPARTMENT_ID = trim($data2[ORG_ID_2]);
					if (trim($data2[POH_ORG1])) $CMD_ORG1 = trim($data2[POH_ORG1]);
					if (trim($data2[POH_ORG2])) $DEPARTMENT_NAME = $CMD_ORG2 = trim($data2[POH_ORG2]);
					if (trim($data2[POH_ORG3])) $ORG_NAME = $CMD_ORG3 = trim($data2[POH_ORG3]);
					if (trim($data2[POH_UNDER_ORG1])) $ORG_NAME_1 = $CMD_ORG4 = trim($data2[POH_UNDER_ORG1]);
					if (trim($data2[POH_UNDER_ORG2])) $ORG_NAME_2 = $CMD_ORG5 = trim($data2[POH_UNDER_ORG2]);
				}

				$cmd = " select LAYER_TYPE from PER_LINE where trim(PL_CODE)='$PL_CODE' ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$LAYER_TYPE = $data2[LAYER_TYPE] + 0;

				$cmd = " 	select LAYER_SALARY_MAX, LAYER_SALARY_FULL from PER_LAYER 
								where LAYER_TYPE = 0 and LEVEL_NO='$CMD_LEVEL' and LAYER_NO=0 ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
                                
                                /* Release 5.2.1.20 http://dpis.ocsc.go.th/Service/node/1918*/ 
                                // เดิม if ($LAYER_TYPE==1 && ($CMD_LEVEL == "O3" || $CMD_LEVEL == "K5") && $CMD_OLD_SALARY <= $data2[LAYER_SALARY_FULL]) 
				if ($LAYER_TYPE==1 && ($CMD_LEVEL == "O3" || $CMD_LEVEL == "K5") && $CMD_OLD_SALARY < $data2[LAYER_SALARY_FULL]) 
					$OLD_SALARY_MAX = $data2[LAYER_SALARY_FULL] + 0;
				else
					$OLD_SALARY_MAX = $data2[LAYER_SALARY_MAX] + 0;

				$EXTRA_FLAG = $DEATH_FLAG = $SAH_SALARY_EXTRA = $CMD_SPSALARY = 0;
				$CMD_NOTE1 = "";
				if ($COM_CONDITION==3 && $CMD_OLD_SALARY==$OLD_SALARY_MAX) {
					$EXTRA_FLAG = 1;
					$cmd = " 	select SAH_SALARY_EXTRA from PER_SALARYHIS 
									where PER_ID=$TMP_PER_ID and SAH_EFFECTIVEDATE='2014-10-01' and SAH_SALARY_EXTRA > 0
									order by SAH_DOCDATE desc, SAH_SALARY_EXTRA desc ";
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();
					$SAH_SALARY_EXTRA = $data2[SAH_SALARY_EXTRA] + 0;
					$CMD_NOTE1 = "เงินตอบแทนพิเศษ ".number_format($SAH_SALARY_EXTRA,2);

					if (!$SAH_SALARY_EXTRA) {
						$cmd = " select EXH_AMT	from PER_EXTRAHIS 
										where trim(EX_CODE)='018' and PER_ID=$TMP_PER_ID and EXH_ACTIVE = 1 and 
										(EXH_ENDDATE is NULL or EXH_ENDDATE >= '$UPDATE_DATE')
										order by EX_SEQ_NO desc ";
						$db_dpis2->send_cmd($cmd);
						$data2 = $db_dpis2->get_array();
						$SAH_SALARY_EXTRA = $data2[EXH_AMT] + 0;
					}
					$CMD_SALARY = (ceil(($TMP_OLD_SALARY + $SAH_SALARY_EXTRA)/10))*10 ;
					$TMP_OLD_SALARY = $CMD_SALARY;
				}

				if ($COM_CONDITION==3 && $PER_STATUS==2) {
					$cmd = " 	select MOV_SUB_TYPE from PER_MOVMENT where MOV_CODE='$MOV_CODE' and MOV_SUB_TYPE='94' ";
					$count_data = $db_dpis2->send_cmd($cmd);
					if ($count_data) $DEATH_FLAG = 1;

					$cmd = " 	select MOV_SUB_TYPE from PER_POSITIONHIS a, PER_MOVMENT b 
									where PER_ID=$TMP_PER_ID and a.MOV_CODE=b.MOV_CODE and MOV_SUB_TYPE='94' ";
					$count_data = $db_dpis2->send_cmd($cmd);
					if ($count_data) $DEATH_FLAG = 1;
				}

				if ($COM_CONDITION==1 || ($COM_CONDITION==3 && $PER_STARTDATE < $COM_EFFECTIVEDATE  && $PER_OCCUPYDATE < $COM_EFFECTIVEDATE && 
					($PER_STATUS==1 || ($PER_STATUS==2 && ($PER_POSDATE > $COM_EFFECTIVEDATE || ($DEATH_FLAG == 1 and $PER_POSDATE >= $COM_EFFECTIVEDATE)))) && 
					($CMD_LEVEL=="O1" || $CMD_LEVEL=="O2" || $CMD_LEVEL=="K1" || $CMD_LEVEL=="K2"))) {
					$CMD_SALARY = (ceil($TMP_OLD_SALARY * (($COM_SAL_PERCENT/100))  /10))*10 ;
					$CMD_SALARY = $CMD_SALARY + $TMP_OLD_SALARY;
				}
				// 215=ประเภทปรับเงินเดือน, 21510=ปรับเงินเดือนตามคุณวุฒิ, 21520=ปรับเงินเดือนตามกฎหมาย
				if ($SESS_DEPARTMENT_NAME=="กรมการปกครอง")
					$MOV_CODE = "1034";
				elseif ($BKK_FLAG==1) 
					$MOV_CODE = "SA2010";
				else
					$MOV_CODE = "21520";
				$CMD_DATE = $COM_EFFECTIVEDATE;
				
				$POS_ID = (trim($data[POS_ID]))? $data[POS_ID] : 'NULL';
				$cmd = " 	select POS_NO_NAME, POS_NO, PL_NAME, PM_CODE, DEPARTMENT_ID, ORG_ID, ORG_ID_1, ORG_ID_2 from PER_POSITION a, PER_LINE b 
								where POS_ID=$POS_ID and a.PL_CODE=b.PL_CODE ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				if (!$CMD_POS_NO_NAME) $CMD_POS_NO_NAME = trim($data2[POS_NO_NAME]);
				if (!$CMD_POS_NO) $CMD_POS_NO = trim($data2[POS_NO]);
				if (!$PL_NAME) $PL_NAME = trim($data2[PL_NAME]);
				$CMD_POSITION = $PL_NAME;
				if (!$PM_CODE) $PM_CODE = trim($data2[PM_CODE]);
				if (!$DEPARTMENT_ID) $DEPARTMENT_ID = (trim($data2[DEPARTMENT_ID]))? trim($data2[DEPARTMENT_ID]) : 0;
				if (!$ORG_ID_1) $ORG_ID_1 = (trim($data2[ORG_ID]))? trim($data2[ORG_ID]) : 0;
				if (!$ORG_ID_2) $ORG_ID_2 = (trim($data2[ORG_ID_1]))? trim($data2[ORG_ID_1]) : 0;
				if (!$ORG_ID_3) $ORG_ID_3 = (trim($data2[ORG_ID_2]))? trim($data2[ORG_ID_2]) : 0;
				if (!$CMD_ORG3) {
					$cmd = " select ORG_ID, ORG_NAME from PER_ORG where ORG_ID IN ($DEPARTMENT_ID, $ORG_ID_1, $ORG_ID_2, $ORG_ID_3) ";
					$db_dpis2->send_cmd($cmd);
					while ( $data2 = $db_dpis2->get_array() ) {
						$temp_id = trim($data2[ORG_ID]);
						$DEPARTMENT_NAME = $CMD_ORG2 = ($temp_id == $DEPARTMENT_ID)?  trim($data2[ORG_NAME]) : $CMD_ORG2;
						$ORG_NAME = $CMD_ORG3 = ($temp_id == $ORG_ID_1)?  trim($data2[ORG_NAME]) : $CMD_ORG3;
						$ORG_NAME_1 = $CMD_ORG4 = ($temp_id == $ORG_ID_2)?  trim($data2[ORG_NAME]) : $CMD_ORG4;
						$ORG_NAME_2 = $CMD_ORG5 = ($temp_id == $ORG_ID_3)?  trim($data2[ORG_NAME]) : $CMD_ORG5;						
					}
				}

				if (!$CMD_ORG1) {
					$cmd = " select ORG_ID_REF from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();
					$MINISTRY_ID = $data2[ORG_ID_REF];
					
					$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();
					$CMD_ORG1 = $data2[ORG_NAME];
				}
				
				$cmd = " select PM_NAME from PER_MGT where PM_CODE='$PM_CODE' ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$PM_NAME = $data2[PM_NAME];
				if ($PM_NAME) $CMD_POSITION = "$CMD_POSITION\|$PM_NAME";
				
				$cmd = " select POSITION_LEVEL from PER_PERSONAL a, PER_LEVEL b where a.POS_ID=$POS_ID and a.LEVEL_NO=b.LEVEL_NO ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$POSITION_LEVEL = $data2[POSITION_LEVEL];
				
				if ($PM_NAME) $PL_NAME_WORK = $PM_NAME." (".$PL_NAME.$POSITION_LEVEL.")";
				else $PL_NAME_WORK = $PL_NAME.$POSITION_LEVEL;
				
				if ($ORG_NAME=="-") $ORG_NAME = "";
				if ($ORG_NAME_1=="-") $ORG_NAME_1 = "";
				if ($ORG_NAME_2=="-") $ORG_NAME_2 = "";
				if ($OT_CODE == "03") 
					if (!$ORG_NAME_2 && !$ORG_NAME_1 && $DEPARTMENT_NAME=="กรมการปกครอง") 
						$ORG_NAME_WORK = "ที่ทำการปกครอง".$ORG_NAME." ".$ORG_NAME;
					else 
						$ORG_NAME_WORK = trim($ORG_NAME_2." ".$ORG_NAME_1." ".$ORG_NAME);
				elseif ($OT_CODE == "01") $ORG_NAME_WORK = trim($ORG_NAME_2." ".$ORG_NAME_1." ".$ORG_NAME." ".$DEPARTMENT_NAME);
				else $ORG_NAME_WORK = trim($ORG_NAME_2." ".$ORG_NAME_1." ".$ORG_NAME);
				
				if ($COM_CONDITION==3) {
					if ($CMD_LEVEL=="O1" || $CMD_LEVEL=="O2" || $CMD_LEVEL=="K1" || $CMD_LEVEL=="K2") $CMD_NOTE2 = "(ข)";
					else $CMD_NOTE2 = "(ก)";
				}
				if ($COM_CONDITION==1 || ($COM_CONDITION==3 && $PER_STARTDATE < $COM_EFFECTIVEDATE  && $PER_OCCUPYDATE < $COM_EFFECTIVEDATE && 
					($PER_STATUS==1 || ($PER_STATUS==2 && ($PER_POSDATE > $COM_EFFECTIVEDATE || ($DEATH_FLAG == 1 and $PER_POSDATE >= $COM_EFFECTIVEDATE)) && 
					($CMD_LEVEL=="O1" || $CMD_LEVEL=="O2" || $CMD_LEVEL=="K1" || $CMD_LEVEL=="K2" || $EXTRA_FLAG==1))))) {
					$cmd = " insert into PER_COMDTL (COM_ID, CMD_SEQ, PER_ID, CMD_DATE, CMD_POSITION, 
											CMD_LEVEL, CMD_ORG1, CMD_ORG2, CMD_ORG3, CMD_ORG4, CMD_ORG5, CMD_OLD_SALARY,  
											POS_ID, POEM_ID, POEMS_ID, LEVEL_NO, CMD_SALARY, CMD_SPSALARY, 
											CMD_NOTE1, CMD_NOTE2, MOV_CODE, CMD_SAL_CONFIRM, PER_CARDNO, 
											UPDATE_USER, UPDATE_DATE, PL_NAME_WORK, ORG_NAME_WORK, 
											CMD_LEVEL_POS, CMD_POS_NO_NAME, CMD_POS_NO)
									 values ($COM_ID, $cmd_seq, $TMP_PER_ID, '$CMD_DATE', '$CMD_POSITION', 
											'$CMD_LEVEL', '$CMD_ORG1', '$CMD_ORG2', '$CMD_ORG3', '$CMD_ORG4', '$CMD_ORG5', $CMD_OLD_SALARY, 
											$POS_ID, NULL, NULL, '$LEVEL_NO', $CMD_SALARY, $CMD_SPSALARY,
											'$CMD_NOTE1', '$CMD_NOTE2', '$MOV_CODE', 0, '$PER_CARDNO', 
											$SESS_USERID, '$UPDATE_DATE', '$PL_NAME_WORK', '$ORG_NAME_WORK', 
											'$CMD_LEVEL1', '$CMD_POS_NO_NAME', '$CMD_POS_NO') ";
					$db_dpis1->send_cmd($cmd);
//					$db_dpis1->show_error();
//					echo "$cmd<br>==================<br>";
				}
			}	// end while
		} else {
			$cmd1 = " select  	PER_ID, PER_SALARY, a.LAYER_SALARY, b.LAYER_SALARY as NEW_SALARY, 
							POS_ID, POEM_ID, POEMS_ID, PER_CARDNO, 
							a.LAYER_TYPE, a.LEVEL_NO as PER_LEVEL, a.LAYER_NO, 
							b.LAYER_TYPE, b.LEVEL_NO, b.LAYER_NO 
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
					if ($BKK_FLAG==1) $MOV_CODE = "66";
					else $MOV_CODE = "215";
					//$tmp_date = explode("-", substr(trim($data[SALP_DATE]), 0, 10));
					$CMD_DATE = trim($COM_DATE);
					
					$CMD_LEVEL = $LEVEL_NO = $CMD_LEVEL1 = $data[PER_LEVEL];
					$POS_ID = (trim($data[POS_ID]))? $data[POS_ID] : 'NULL';
					$POEM_ID = (trim($data[POEM_ID]))? $data[POEM_ID] : 'NULL';
					$POEMS_ID = (trim($data[POEMS_ID]))? $data[POEMS_ID] : 'NULL';
					if ($POS_ID && $POS_ID!="NULL") {
						$cmd = " 	select POS_NO_NAME, POS_NO, PL_NAME, DEPARTMENT_ID, ORG_ID, ORG_ID_1, ORG_ID_2 from PER_POSITION a, PER_LINE b 
										where POS_ID=$POS_ID and a.PL_CODE=b.PL_CODE ";
						$db_dpis2->send_cmd($cmd);
						$data2 = $db_dpis2->get_array();
						$CMD_POS_NO_NAME = trim($data2[POS_NO_NAME]);
						$CMD_POS_NO = trim($data2[POS_NO]);
						$PL_NAME = trim($data2[PL_NAME]);
						$CMD_POSITION = $PL_NAME;
					} elseif ($POEM_ID && $POEM_ID!="NULL") {
						$cmd = " 	select POEM_NO_NAME, POEM_NO, PN_NAME, DEPARTMENT_ID, ORG_ID, ORG_ID_1, ORG_ID_2 from PER_POS_EMP a, PER_POS_NAME b 
										where POEM_ID=$POEM_ID and a.PN_CODE=b.PN_CODE ";
						$db_dpis2->send_cmd($cmd);
						$data2 = $db_dpis2->get_array();
						$CMD_POS_NO_NAME = trim($data2[POEM_NO_NAME]);
						$CMD_POS_NO = trim($data2[POEM_NO]);
						$PN_NAME = trim($data2[PN_NAME]);
						$CMD_POSITION = $PN_NAME;
					} elseif ($POEMS_ID && $POEMS_ID!="NULL") {
						$cmd = " 	select POEMS_NO_NAME, POEMS_NO, EP_NAME, DEPARTMENT_ID, ORG_ID, ORG_ID_1, ORG_ID_2 from PER_POS_EMPSER a, PER_EMPSER_POS_NAME b 
										where POEMS_ID=$POEMS_ID and a.EP_CODE=b.EP_CODE ";
						$db_dpis2->send_cmd($cmd);
						$data2 = $db_dpis2->get_array();
						$CMD_POS_NO_NAME = trim($data2[POEMS_NO_NAME]);
						$CMD_POS_NO = trim($data2[POEMS_NO]);
						$EP_NAME = trim($data2[EP_NAME]);
						$CMD_POSITION = $EP_NAME;
					}
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
           } //END IF $COM_CONDITION==4
	}	// end if
	if ($count_temp)
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูลรายละเอียดบัญชีแนบท้ายคำสั่งปรับอัตราเงินเดือนใหม่     [".trim($COM_ID)." : ".$COM_NO." : ".$COM_TYPE."]");		
?>