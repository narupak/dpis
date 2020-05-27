<?
	if ($COM_CONDITION==2) {
		if ($DEPARTMENT_ID > 0) $where .= "and DEPARTMENT_ID = $DEPARTMENT_ID";
		$search_budget_year = substr($tmp_COM_DATE,0,4);
		if($DPISDB=="odbc") 
			$where .= "and (LEFT(trim(PER_RETIREDATE), 10) > '".($search_budget_year - 1)."-10-01' and LEFT(trim(PER_RETIREDATE), 10) <= '".$search_budget_year."-10-01')";
		elseif($DPISDB=="oci8") 
			$where .= "and (SUBSTR(trim(PER_RETIREDATE), 1, 10) > '".($search_budget_year - 1)."-10-01' and SUBSTR(trim(PER_RETIREDATE), 1, 10) <= '".$search_budget_year."-10-01')";
		elseif($DPISDB=="mysql") 
			$where .= "and (LEFT(trim(PER_RETIREDATE), 10) > '".($search_budget_year - 1)."-10-01' and LEFT(trim(PER_RETIREDATE), 10) <= '".$search_budget_year."-10-01')";

		$cmd1 = " select  	PER_ID, PER_SALARY, POS_ID, POEM_ID, LEVEL_NO, PER_CARDNO 
						  from PER_PERSONAL
						  where 	PER_TYPE=$COM_PER_TYPE and PER_STATUS=1 $where
						  order by PER_ID   ";
		$count_temp = $db_dpis->send_cmd($cmd1);
		//$db_dpis->show_error();
		//echo "$cmd1<br>===================<br>";
		$cmd_seq = 0;
		while ($data = $db_dpis->get_array()) {
			$cmd_seq++;
			$TMP_PER_ID = trim($data[PER_ID]);
			$CMD_SALARY = $CMD_OLD_SALARY = trim($data[PER_SALARY]);
			$CMD_LEVEL = $LEVEL_NO = $CMD_LEVEL1 = $data[LEVEL_NO];
			$PER_CARDNO = trim($data[PER_CARDNO]);
			if ($BKK_FLAG==1) 
				$MOV_CODE = "17";
			else
				$MOV_CODE = "11910";
			$CMD_DATE = $search_budget_year . '-10-01';
			
			$POS_ID = (trim($data[POS_ID]))? $data[POS_ID] : 'NULL';
			$POEM_ID = (trim($data[POEM_ID]))? $data[POEM_ID] : 'NULL';
			if ($COM_PER_TYPE==1) {
				$cmd = " 	select POS_NO_NAME, POS_NO, PL_NAME, PM_CODE, ORG_ID, ORG_ID_1, ORG_ID_2 
									from PER_POSITION a, PER_LINE b 
									where POS_ID=$POS_ID and a.PL_CODE=b.PL_CODE ";
			} elseif ($COM_PER_TYPE==2) {
				$cmd = " 	select POEM_NO_NAME as POS_NO_NAME, POEM_NO as POS_NO, PN_NAME as PL_NAME, ORG_ID, ORG_ID_1, ORG_ID_2 
									from PER_POS_EMP a, PER_POS_NAME b 
									where POEM_ID=$POEM_ID and a.PN_CODE=b.PN_CODE ";
			}
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$CMD_POS_NO_NAME = trim($data2[POS_NO_NAME]);
			$CMD_POS_NO = trim($data2[POS_NO]);
			$PL_NAME = trim($data2[PL_NAME]);
			$CMD_POSITION = $PL_NAME;
			$ORG_ID_1 = (trim($data2[ORG_ID]))? trim($data2[ORG_ID]) : 0;
			$ORG_ID_2 = (trim($data2[ORG_ID_1]))? trim($data2[ORG_ID_1]) : 0;
			$ORG_ID_3 = (trim($data2[ORG_ID_2]))? trim($data2[ORG_ID_2]) : 0;
			$CMD_ORG3 = $CMD_ORG4 = $CMD_ORG5 = "";
			$cmd = " select ORG_ID, ORG_NAME from PER_ORG where ORG_ID IN ($ORG_ID_1, $ORG_ID_2, $ORG_ID_3) ";
			$db_dpis2->send_cmd($cmd);
			while ( $data2 = $db_dpis2->get_array() ) {
				$temp_id = trim($data2[ORG_ID]);
				$ORG_NAME = $CMD_ORG3 = ($temp_id == $ORG_ID_1)?  trim($data2[ORG_NAME]) : $CMD_ORG3;
				$ORG_NAME_1 = $CMD_ORG4 = ($temp_id == $ORG_ID_2)?  trim($data2[ORG_NAME]) : $CMD_ORG4;
				$ORG_NAME_2 = $CMD_ORG5 = ($temp_id == $ORG_ID_3)?  trim($data2[ORG_NAME]) : $CMD_ORG5;						
			}

			$cmd = " select ORG_NAME, ORG_ID_REF from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$DEPARTMENT_NAME = $data2[ORG_NAME];
			$MINISTRY_ID = $data2[ORG_ID_REF];
			
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$MINISTRY_NAME = $data2[ORG_NAME];
			
			$cmd = " select PM_NAME from PER_MGT where PM_CODE='$PM_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PM_NAME = $data2[PM_NAME];
			
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
				if (!$ORG_NAME_2 && !$ORG_NAME_1 && $DEPARTMENT_NAME=="¡ÃÁ¡ÒÃ»¡¤ÃÍ§") 
					$ORG_NAME_WORK = "·Õè·Ó¡ÒÃ»¡¤ÃÍ§".$ORG_NAME." ".$ORG_NAME;
				else 
					$ORG_NAME_WORK = trim($ORG_NAME_2." ".$ORG_NAME_1." ".$ORG_NAME);
			elseif ($OT_CODE == "01") $ORG_NAME_WORK = trim($ORG_NAME_2." ".$ORG_NAME_1." ".$ORG_NAME." ".$DEPARTMENT_NAME);
			else $ORG_NAME_WORK = trim($ORG_NAME_2." ".$ORG_NAME_1." ".$ORG_NAME);
			
			$cmd = " insert into PER_COMDTL (COM_ID, CMD_SEQ, PER_ID, CMD_DATE, CMD_POSITION, 
									CMD_LEVEL, CMD_ORG1, CMD_ORG2, CMD_ORG3, CMD_ORG4, CMD_ORG5, CMD_OLD_SALARY,  
									POS_ID, POEM_ID, POEMS_ID, LEVEL_NO, CMD_SALARY, CMD_SPSALARY, 
									CMD_NOTE1, CMD_NOTE2, MOV_CODE, CMD_SAL_CONFIRM, PER_CARDNO, 
									UPDATE_USER, UPDATE_DATE, PL_NAME_WORK, ORG_NAME_WORK, 
									CMD_LEVEL_POS, CMD_POS_NO_NAME, CMD_POS_NO)
							 values ($COM_ID, $cmd_seq, $TMP_PER_ID, '$CMD_DATE', '$CMD_POSITION', 
									'$CMD_LEVEL', '$MINISTRY_NAME', '$DEPARTMENT_NAME', '$ORG_NAME', '$ORG_NAME_1', '$ORG_NAME_2', $CMD_OLD_SALARY, 
									$POS_ID, NULL, NULL, '$LEVEL_NO', $CMD_SALARY, 0,
									'$CMD_NOTE1', '$CMD_NOTE2', '$MOV_CODE', 0, '$PER_CARDNO', 
									$SESS_USERID, '$UPDATE_DATE', '$PL_NAME_WORK', '$ORG_NAME_WORK', 
									'$CMD_LEVEL1', '$CMD_POS_NO_NAME', '$CMD_POS_NO') ";
			$db_dpis1->send_cmd($cmd);
//			$db_dpis1->show_error();
//			echo "$cmd<br>==================<br>";
		}	// end while

		if ($count_temp)
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $ADD_PERSON_TITLE$COM_TYPE_NM [".trim($COM_ID)." : ".$COM_NO." : ".$COM_TYPE."]");
	}
?>