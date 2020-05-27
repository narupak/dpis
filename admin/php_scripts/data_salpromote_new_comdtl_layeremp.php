<?
	if ($COM_CONDITION==3) {
		$COM_EFFECTIVEDATE = "2014-12-01";
		if ($DEPARTMENT_ID > 0) $where .= "and a.DEPARTMENT_ID = $DEPARTMENT_ID ";
		if ($ORG_ID_DTL && $ORG_ID_DTL != "NULL") $where .= " and b.ORG_ID = $ORG_ID_DTL ";		 
		$cmd1 = "  select 	PER_ID, PER_SALARY, a.POEM_ID, a.LEVEL_NO, a.PER_CARDNO, PER_STARTDATE, PER_OCCUPYDATE, PER_STATUS, PER_POSDATE, 
											MOV_CODE, b.PG_CODE_SALARY, PER_NAME, PER_SURNAME
							from 		PER_PERSONAL a, PER_POS_EMP b
							where 	a.POEM_ID=b.POEM_ID and PER_TYPE=2 and (PER_STATUS=1 or (PER_STATUS=2 and PER_POSDATE >= '$COM_EFFECTIVEDATE'))  
											$where
							order by PER_ID ";					  
		$count_temp = $db_dpis->send_cmd($cmd1);
		//echo "$cmd1<br>===================<br>===================<br>";
		//$db_dpis->show_error();
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

			$PG_CODE_SALARY = trim($data[PG_CODE_SALARY]);
			$cmd = " select LAYERE_NO, PG_CODE from PER_LAYEREMP where PG_CODE='$PG_CODE_SALARY' and LAYERE_SALARY=$CMD_OLD_SALARY ";
			$count_data = $db_dpis2->send_cmd($cmd);
			if (!$count_data) echo "กลุ่มบัญชีค่าจ้างไม่ถูกต้อง $data[PER_NAME] $data[PER_SURNAME]<br>";
			$data2 = $db_dpis2->get_array();
			$LAYERE_NO = trim($data2[LAYERE_NO]);
			$PG_CODE = trim($data2[PG_CODE]);

			$POEM_ID = (trim($data[POEM_ID]))? trim($data[POEM_ID]) : 'NULL';
			$cmd = " select PN_CODE from PER_POS_EMP where POEM_ID=$POEM_ID ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PN_CODE = trim($data2[PN_CODE]);

			$TMP_OLD_SALARY = $CMD_SALARY = $CMD_OLD_SALARY;
			$CMD_LEVEL = $LEVEL_NO = $CMD_LEVEL1 = trim($data[LEVEL_NO]);
			$PER_CARDNO = trim($data[PER_CARDNO]);
			$PER_STARTDATE = trim($data[PER_STARTDATE]);
			$PER_OCCUPYDATE = trim($data[PER_OCCUPYDATE]);
			$PER_STATUS = trim($data[PER_STATUS]);
			$PER_POSDATE = trim($data[PER_POSDATE]);
			$MOV_CODE = trim($data[MOV_CODE]);

			$CMD_POS_NO_NAME = $CMD_POS_NO = $PL_NAME = $PM_CODE = $PN_CODE = $EP_CODE = $CMD_ORG1 = $CMD_ORG2 = $CMD_ORG3 = $CMD_ORG4 = $CMD_ORG5 = "";
			$cmd = " 	select LEVEL_NO, POH_POS_NO_NAME, POH_POS_NO, PL_CODE, PM_CODE, PN_CODE, EP_CODE, 
											ORG_ID_1, ORG_ID_2, ORG_ID_3, POH_UNDER_ORG1, POH_UNDER_ORG2, POH_ORG1, POH_ORG2, POH_ORG3 
							from PER_POSITIONHIS a, PER_MOVMENT b 
							where PER_ID=$TMP_PER_ID and a.MOV_CODE=b.MOV_CODE and POH_EFFECTIVEDATE <= '$COM_EFFECTIVEDATE' and 
							MOV_SUB_TYPE!='0' and MOV_SUB_TYPE!='5' and MOV_SUB_TYPE!='7'
							order by POH_DOCDATE desc ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			if (trim($data2[LEVEL_NO])) $CMD_LEVEL = trim($data2[LEVEL_NO]);
			if (trim($data2[POH_POS_NO_NAME])) $CMD_POS_NO_NAME = trim($data2[POH_POS_NO_NAME]);
			if (trim($data2[POH_POS_NO]) && trim($data2[POH_POS_NO]) != "-") $CMD_POS_NO = trim($data2[POH_POS_NO]);
			if (trim($data2[PN_CODE])) {
				$PN_CODE = trim($data2[PN_CODE]);
				$cmd = " select PN_NAME from PER_POS_NAME where PN_CODE='$PN_CODE' ";
				$db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$PN_NAME = $data1[PN_NAME];
			}
			if (trim($data2[PM_CODE])) $PM_CODE = trim($data2[PM_CODE]);
			if (trim($data2[PL_CODE])) $PL_CODE = trim($data2[PL_CODE]);
			if (trim($data2[EP_CODE])) $EP_CODE = trim($data2[EP_CODE]);
			if (trim($data2[ORG_ID_1])) $MINISTRY_ID = trim($data2[ORG_ID_1]);
			if (trim($data2[ORG_ID_2])) $DEPARTMENT_ID = trim($data2[ORG_ID_2]);
			if (trim($data2[POH_ORG1])) $CMD_ORG1 = trim($data2[POH_ORG1]);
			if (trim($data2[POH_ORG2])) $DEPARTMENT_NAME = $CMD_ORG2 = trim($data2[POH_ORG2]);
			if (trim($data2[POH_ORG3])) $ORG_NAME = $CMD_ORG3 = trim($data2[POH_ORG3]);
			if (trim($data2[POH_UNDER_ORG1])) $ORG_NAME_1 = $CMD_ORG4 = trim($data2[POH_UNDER_ORG1]);
			if (trim($data2[POH_UNDER_ORG2])) $ORG_NAME_2 = $CMD_ORG5 = trim($data2[POH_UNDER_ORG2]);

			$cmd = " 	select MAX_SALARY from PER_POS_LEVEL_SALARY 
							where PN_CODE = '$PN_CODE' and LEVEL_NO='$CMD_LEVEL' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$OLD_SALARY_MAX = $data2[MAX_SALARY] + 0;

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
				$CMD_SALARY = $TMP_OLD_SALARY + $SAH_SALARY_EXTRA ;
				$TMP_OLD_SALARY = $CMD_SALARY;

				$cmd = " select LAYERE_NO from PER_LAYEREMP_NEW 
								where PG_CODE = '$PG_CODE' and LAYERE_SALARY <= $TMP_OLD_SALARY
								order by LAYERE_SALARY desc ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$LAYERE_NO = $data2[LAYERE_NO];
			}
			$LAYERE_NO += 1;
			$cmd = " select LAYERE_SALARY from PER_LAYEREMP_NEW 
							where PG_CODE = '$PG_CODE' and LAYERE_NO = '$LAYERE_NO' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$CMD_SALARY = $data2[LAYERE_SALARY];

			if ($PER_STATUS==2) {
				$cmd = " 	select MOV_SUB_TYPE from PER_MOVMENT where MOV_CODE='$MOV_CODE' and MOV_SUB_TYPE='94' ";
				$count_data = $db_dpis2->send_cmd($cmd);
				if ($count_data) $DEATH_FLAG = 1;

				$cmd = " 	select MOV_SUB_TYPE from PER_POSITIONHIS a, PER_MOVMENT b 
								where PER_ID=$TMP_PER_ID and a.MOV_CODE=b.MOV_CODE and MOV_SUB_TYPE='94' ";
				$count_data = $db_dpis2->send_cmd($cmd);
				if ($count_data) $DEATH_FLAG = 1;
			}

			$CMD_SPSALARY = 0;
			// 215=ประเภทปรับเงินเดือน, 21510=ปรับเงินเดือนตามคุณวุฒิ, 21520=ปรับเงินเดือนตามกฎหมาย
			if ($BKK_FLAG==1) $MOV_CODE = "66";
			else $MOV_CODE = "21520";
			$CMD_DATE = $COM_EFFECTIVEDATE;
			
			$cmd = " 	select POEM_NO_NAME, POEM_NO, PN_NAME, DEPARTMENT_ID, ORG_ID, ORG_ID_1, ORG_ID_2 from PER_POS_EMP a, PER_POS_NAME b 
								where POEM_ID=$POEM_ID and a.PN_CODE=b.PN_CODE ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			if (!$CMD_POS_NO_NAME) $CMD_POS_NO_NAME = trim($data2[POEM_NO_NAME]);
			if (!$CMD_POS_NO) $CMD_POS_NO = trim($data2[POEM_NO]);
			if (!$PN_NAME) $PN_NAME = trim($data2[PN_NAME]);
			$CMD_POSITION = $PN_NAME;
			if (!$DEPARTMENT_ID) $DEPARTMENT_ID = (trim($data2[DEPARTMENT_ID]))? trim($data2[DEPARTMENT_ID]) : 0;
			if (!$ORG_ID_1) $ORG_ID_1 = (trim($data2[ORG_ID]))? trim($data2[ORG_ID]) : 0;
			if (!$ORG_ID_2) $ORG_ID_2 = (trim($data2[ORG_ID_1]))? trim($data2[ORG_ID_1]) : 0;
			if (!$ORG_ID_3) $ORG_ID_3 = (trim($data2[ORG_ID_2]))? trim($data2[ORG_ID_2]) : 0;
			if (!$CMD_ORG3) {
				$cmd = " select ORG_ID, ORG_NAME from PER_ORG where ORG_ID IN ($DEPARTMENT_ID, $ORG_ID_1, $ORG_ID_2, $ORG_ID_3) ";
				$db_dpis2->send_cmd($cmd);
				while ( $data2 = $db_dpis2->get_array() ) {
					$temp_id = trim($data2[ORG_ID]);
					$CMD_ORG2 = ($temp_id == $DEPARTMENT_ID)?  trim($data2[ORG_NAME]) : $CMD_ORG2;
					$CMD_ORG3 = ($temp_id == $ORG_ID_1)?  trim($data2[ORG_NAME]) : $CMD_ORG3;
					$CMD_ORG4 = ($temp_id == $ORG_ID_2)?  trim($data2[ORG_NAME]) : $CMD_ORG4;
					$CMD_ORG5 = ($temp_id == $ORG_ID_3)?  trim($data2[ORG_NAME]) : $CMD_ORG5;						
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
				
			if ($PER_STARTDATE < $COM_EFFECTIVEDATE  && $PER_OCCUPYDATE < $COM_EFFECTIVEDATE && 
				($PER_STATUS==1 || ($PER_STATUS==2 && ($PER_POSDATE > $COM_EFFECTIVEDATE || ($DEATH_FLAG == 1 and $PER_POSDATE >= $COM_EFFECTIVEDATE))))) {
				$cmd = " insert into PER_COMDTL (COM_ID, CMD_SEQ, PER_ID, CMD_DATE, CMD_POSITION, 
										CMD_LEVEL, CMD_ORG1, CMD_ORG2, CMD_ORG3, CMD_ORG4, CMD_ORG5, CMD_OLD_SALARY,  
										PN_CODE, POS_ID, POEM_ID, POEMS_ID, LEVEL_NO, CMD_SALARY, CMD_SPSALARY, 
										CMD_NOTE1, CMD_NOTE2, MOV_CODE, CMD_SAL_CONFIRM, PER_CARDNO, 
										UPDATE_USER, UPDATE_DATE,CMD_LEVEL_POS, CMD_POS_NO_NAME, CMD_POS_NO)
								 values ($COM_ID, $cmd_seq, $TMP_PER_ID, '$CMD_DATE', '$CMD_POSITION', 
										'$CMD_LEVEL', '$CMD_ORG1', '$CMD_ORG2', '$CMD_ORG3', '$CMD_ORG4', '$CMD_ORG5', $CMD_OLD_SALARY, 
										'$PN_CODE', NULL, $POEM_ID, NULL, '$LEVEL_NO', $CMD_SALARY, $CMD_SPSALARY, 
										'$CMD_NOTE1', '$CMD_NOTE2', '$MOV_CODE', 0, '$PER_CARDNO', 
										$SESS_USERID, '$UPDATE_DATE','$CMD_LEVEL1', '$CMD_POS_NO_NAME', '$CMD_POS_NO') ";
				$db_dpis1->send_cmd($cmd);
				//echo "$cmd<br>==================<br>";
				//$db_dpis1->show_error();
			}
		}	// end while
	} else {
		if ($DEPARTMENT_ID > 0) $where .= "and a.DEPARTMENT_ID = $DEPARTMENT_ID ";
		if ($ORG_ID_DTL && $ORG_ID_DTL != "NULL") $where .= " and b.ORG_ID = $ORG_ID_DTL ";		 
		$cmd1 = "  select 	PER_ID, PER_SALARY, d.LAYERE_SALARY, e.LAYERE_SALARY as NEW_SALARY, a.LEVEL_NO, a.PER_CARDNO, 
											a.POEM_ID, d.LAYERE_NO, d.PG_CODE 
							from 		PER_PERSONAL a, PER_POS_EMP b, PER_LAYEREMP d, PER_LAYEREMP_NEW e
							where 	PER_TYPE=2 and PER_STATUS=1 and a.PER_SALARY=d.LAYERE_SALARY and 
											a.POEM_ID=b.POEM_ID and trim(b.PG_CODE_SALARY)=trim(d.PG_CODE) and trim(d.PG_CODE)=trim(e.PG_CODE) and d.LAYERE_NO=e.LAYERE_NO 
											$where
							order by PER_ID ";					  
		$count_temp = $db_dpis->send_cmd($cmd1);
		//echo "$cmd1<br>===================<br>===================<br>";
		//$db_dpis->show_error();
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
				else $MOV_CODE = "21520";
				$CMD_DATE = $COM_EFFECTIVEDATE;
				
				$CMD_LEVEL = $LEVEL_NO = $data[LEVEL_NO];
				$POEM_ID = (trim($data[POEM_ID]))? trim($data[POEM_ID]) : 'NULL';
				$cmd = " 	select POEM_NO_NAME, POEM_NO, PN_NAME, DEPARTMENT_ID, ORG_ID, ORG_ID_1, ORG_ID_2 from PER_POS_EMP a, PER_POS_NAME b 
									where POEM_ID=$POEM_ID and a.PN_CODE=b.PN_CODE ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$CMD_POS_NO_NAME = trim($data2[POEM_NO_NAME]);
				$CMD_POS_NO = trim($data2[POEM_NO]);
				$CMD_POSITION = trim($data2[PN_NAME]);
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
										NULL, $POEM_ID, NULL, '$LEVEL_NO', $CMD_SALARY, $CMD_SPSALARY, 
										'$CMD_NOTE1', '$CMD_NOTE2', '$MOV_CODE', 0, '$PER_CARDNO', 
										$SESS_USERID, '$UPDATE_DATE','$CMD_LEVEL1', '$CMD_POS_NO_NAME', '$CMD_POS_NO') ";
				$db_dpis1->send_cmd($cmd);
				//echo "$cmd<br>==================<br>";
				//$db_dpis1->show_error();
		}	// end while
	}	// end if
	if ($count_temp)
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูลรายละเอียดบัญชีแนบท้ายคำสั่งปรับอัตราเงินเดือนใหม่     [".trim($COM_ID)." : ".$COM_NO." : ".$COM_TYPE."]");		
?>