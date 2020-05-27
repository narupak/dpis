<?
		if ($search_org_id && $search_org_id != "NULL") $where .= " and b.ORG_ID = $search_org_id ";		 
		if ($DEPARTMENT_ID > 0) $where .= "and a.DEPARTMENT_ID = $DEPARTMENT_ID ";
		$cmd1 = " select  	PER_ID, PER_SALARY, a.POS_ID, a.LEVEL_NO, PER_CARDNO, a.DEPARTMENT_ID 
				  from PER_PERSONAL a, PER_POSITION b
				  where 	a.POS_ID=b.POS_ID and PER_TYPE=$COM_PER_TYPE and PER_STATUS=1 $where
				  order by PER_ID   ";
		$count_temp = $db_dpis->send_cmd($cmd1);
		//$db_dpis->show_error();
		//echo "$cmd1<br>===================<br>";
		$cmd_seq = 0;
		while ($data = $db_dpis->get_array()) {
			$cmd_seq++;
			$TMP_PER_ID = trim($data[PER_ID]);
			$TMP_DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);
			$CMD_OLD_SALARY = trim($data[PER_SALARY]);
			$CMD_LEVEL = $LEVEL_NO = $CMD_LEVEL1 = $data[LEVEL_NO];
			$CMD_DATE2 = ($COM_YEAR - 543) . "-00-00";
			$tmp_COM_YEAR = $COM_YEAR - 543;

			$SAH_SALARY_EXTRA = $NEW_SALARY_EXTRA =	$CMD_SPSALARY = 0;
			$cmd = " 	select sum(KPI_SCORE) as KPI_SCORE from PER_KPI 
							where KPI_YEAR=$COM_YEAR and DEPARTMENT_ID=$TMP_DEPARTMENT_ID ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$KPI_SCORE = $data2[KPI_SCORE] + 0;

			$cmd = " select TOTAL_SCORE from PER_KPI_FORM where PER_ID = $TMP_PER_ID and KF_CYCLE = 1 and substr(KF_END_DATE,1,4) = '$tmp_COM_YEAR' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$TOTAL_SCORE1 = $data2[TOTAL_SCORE];

			$cmd = " select TOTAL_SCORE from PER_KPI_FORM where PER_ID = $TMP_PER_ID and KF_CYCLE = 2 and substr(KF_END_DATE,1,4) = '$tmp_COM_YEAR' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$TOTAL_SCORE2 = $data2[TOTAL_SCORE];

			$cmd = " select BR_TIMES 
							from PER_BONUS_RULE where BR_YEAR='$COM_YEAR' and BR_TYPE = '$COM_PER_TYPE' and $KPI_SCORE between BR_ORG_POINT_MIN and BR_ORG_POINT_MAX and $TOTAL_SCORE1 between BR_PER_POINT_MIN and BR_PER_POINT_MAX ";
			$db_dpis2->send_cmd($cmd);
			while($data2 = $db_dpis2->get_array()) {	
				$BR_TIMES1 = $data2[BR_TIMES];
			}

			$cmd = " select BR_TIMES 
							from PER_BONUS_RULE where BR_YEAR='$COM_YEAR' and BR_TYPE = '$COM_PER_TYPE' and $KPI_SCORE between BR_ORG_POINT_MIN and BR_ORG_POINT_MAX and $TOTAL_SCORE2 between BR_PER_POINT_MIN and BR_PER_POINT_MAX ";
			$db_dpis2->send_cmd($cmd);
			while($data2 = $db_dpis2->get_array()) {	
				$BR_TIMES2 = $data2[BR_TIMES];
			}

			$CMD_SALARY = (ceil($CMD_OLD_SALARY * (($BR_TIMES/100))  /10))*10 ;
			$CMD_SALARY = $CMD_SALARY + $CMD_OLD_SALARY;
			$PER_CARDNO = trim($data[PER_CARDNO]);
			// 215=ประเภทปรับเงินเดือน, 21510=ปรับเงินเดือนตามคุณวุฒิ, 21520=ปรับเงินเดือนตามกฎหมาย
			if ($BKK_FLAG==1) 
				$MOV_CODE = "SA4017";
			else
				$MOV_CODE = "21520"; // ยังไม่ได้ Set
			$CMD_DATE = $CMD_DATE;
			
			$POS_ID = (trim($data[POS_ID]))? $data[POS_ID] : 'NULL';
			$cmd = " 	select POS_NO_NAME, POS_NO, PL_NAME, PM_CODE, DEPARTMENT_ID, ORG_ID, ORG_ID_1, ORG_ID_2 from PER_POSITION a, PER_LINE b 
							where POS_ID=$POS_ID and a.PL_CODE=b.PL_CODE ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$CMD_POS_NO_NAME = trim($data2[POS_NO_NAME]);
			$CMD_POS_NO = trim($data2[POS_NO]);
			$PL_NAME = trim($data2[PL_NAME]);
			$CMD_POSITION = $PL_NAME;
			$PM_CODE = trim($data2[PM_CODE]);
			$DEPARTMENT_ID = (trim($data2[DEPARTMENT_ID]))? trim($data2[DEPARTMENT_ID]) : 0;
			$ORG_ID_1 = (trim($data2[ORG_ID]))? trim($data2[ORG_ID]) : 0;
			$ORG_ID_2 = (trim($data2[ORG_ID_1]))? trim($data2[ORG_ID_1]) : 0;
			$ORG_ID_3 = (trim($data2[ORG_ID_2]))? trim($data2[ORG_ID_2]) : 0;
			$CMD_ORG3 = $CMD_ORG4 = $CMD_ORG5 = "";
			$cmd = " select ORG_ID, ORG_NAME from PER_ORG where ORG_ID IN ($DEPARTMENT_ID, $ORG_ID_1, $ORG_ID_2, $ORG_ID_3) ";
			$db_dpis2->send_cmd($cmd);
			while ( $data2 = $db_dpis2->get_array() ) {
				$temp_id = trim($data2[ORG_ID]);
				$CMD_ORG2 = ($temp_id == $DEPARTMENT_ID)?  trim($data2[ORG_NAME]) : $CMD_ORG2;
				$ORG_NAME = $CMD_ORG3 = ($temp_id == $ORG_ID_1)?  trim($data2[ORG_NAME]) : $CMD_ORG3;
				$ORG_NAME_1 = $CMD_ORG4 = ($temp_id == $ORG_ID_2)?  trim($data2[ORG_NAME]) : $CMD_ORG4;
				$ORG_NAME_2 = $CMD_ORG5 = ($temp_id == $ORG_ID_3)?  trim($data2[ORG_NAME]) : $CMD_ORG5;						
			}

			$cmd = " select ORG_ID_REF from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$MINISTRY_ID = $data2[ORG_ID_REF];
			
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$CMD_ORG1 = $data2[ORG_NAME];
			
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
			elseif ($OT_CODE == "01") $ORG_NAME_WORK = trim($ORG_NAME_2." ".$ORG_NAME_1." ".$ORG_NAME." ".$CMD_ORG2);
			else $ORG_NAME_WORK = trim($ORG_NAME_2." ".$ORG_NAME_1." ".$ORG_NAME);
			
			$cmd = " insert into PER_COMDTL (COM_ID, CMD_SEQ, PER_ID, CMD_DATE, CMD_POSITION, 
									CMD_LEVEL, CMD_ORG1, CMD_ORG2, CMD_ORG3, CMD_ORG4, CMD_ORG5, CMD_OLD_SALARY,  
									POS_ID, POEM_ID, POEMS_ID, LEVEL_NO, CMD_SALARY, CMD_SPSALARY, 
									CMD_NOTE1, CMD_NOTE2, MOV_CODE, CMD_SAL_CONFIRM, PER_CARDNO, 
									UPDATE_USER, UPDATE_DATE, PL_NAME_WORK, ORG_NAME_WORK, 
									CMD_LEVEL_POS, CMD_POS_NO_NAME, CMD_POS_NO, CMD_DATE2)
							 values ($COM_ID, $cmd_seq, $TMP_PER_ID, '$CMD_DATE', '$CMD_POSITION', 
									'$CMD_LEVEL', '$CMD_ORG1', '$CMD_ORG2', '$CMD_ORG3', '$CMD_ORG4', '$CMD_ORG5', $CMD_OLD_SALARY, 
									$POS_ID, NULL, NULL, '$LEVEL_NO', $CMD_SALARY, $CMD_SPSALARY,
									'$CMD_NOTE1', '$CMD_NOTE2', '$MOV_CODE', 0, '$PER_CARDNO', 
									$SESS_USERID, '$UPDATE_DATE', '$PL_NAME_WORK', '$ORG_NAME_WORK', 
									'$CMD_LEVEL1', '$CMD_POS_NO_NAME', '$CMD_POS_NO', '$CMD_DATE2') ";
			$db_dpis1->send_cmd($cmd);
//					$db_dpis1->show_error();
//					echo "$cmd<br>==================<br>";
		}	// end while
		if ($count_temp)
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูลรายละเอียดบัญชีแนบท้ายคำสั่งเงินรางวัลประจำปี [".trim($COM_ID)." : ".$COM_NO." : ".$COM_TYPE."]");		
?>