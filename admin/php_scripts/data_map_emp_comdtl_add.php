<?
		// ===== เริ่มต้น insert ข้อมูลจาก PER_POS_EMP เพื่อจัดระบบตำแหน่งลูกจ้างประจำ เข้าสู่ข้อมูลบัญชีแนบท้ายคำสั่ง (table PER_COMDTL) =====
		$cmd = " select a.POEM_ID, a.POEM_NO_NAME, a.POEM_NO, a.PN_CODE, b.PN_NAME, a.ORG_ID, a.ORG_ID_1, a.ORG_ID_2, b.PN_CODE_NEW, b.LEVEL_NO 
				 from PER_POS_EMP a, PER_POS_NAME b 
				 where a.POEM_STATUS = 1 and a.PN_CODE=b.PN_CODE and a.DEPARTMENT_ID = $DEPARTMENT_ID
				order by a.POEM_ID ";				  
		$count_temp = $db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
		$cmd_seq = 0;
		while ($data = $db_dpis->get_array()) {
			$POEM_ID = $data[POEM_ID];
			$cmd = " select PER_ID, PER_SALARY, LEVEL_NO as PER_LEVEL, PER_CARDNO
					 from PER_PERSONAL where POEM_ID=$POEM_ID and PER_STATUS = 1 ";				  
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$POEM_NO_NAME = trim($data[POEM_NO_NAME]);
			$POEM_NO = trim($data[POEM_NO]);
			$PN_CODE = trim($data[PN_CODE]);
			$PN_NAME = trim($data[PN_NAME]);
			$PN_CODE_NEW = trim($data[PN_CODE_NEW]);
			$LEVEL_NO = trim($data[LEVEL_NO]);
			$CMD_POSITION = $PN_NAME;
			$ORG_ID_1 = (trim($data[ORG_ID]))? trim($data[ORG_ID]) : 0;
			$ORG_ID_2 = (trim($data[ORG_ID_1]))? trim($data[ORG_ID_1]) : 0;
			$ORG_ID_3 = (trim($data[ORG_ID_2]))? trim($data[ORG_ID_2]) : 0;

			$cmd_seq++;
			$TMP_PER_ID = trim($data2[PER_ID]);
			$CMD_OLD_SALARY = trim($data2[PER_SALARY]);
			$CMD_SALARY = trim($data2[PER_SALARY]);
			$CMD_SPSALARY = 0;
			$TMP_SALP_LEVEL = trim($data2[PER_LEVEL]);
			$PER_CARDNO = trim($data2[PER_CARDNO]);
			if($BKK_FLAG==1) $MOV_CODE = "40";
			else $MOV_CODE = "10710";
			$CMD_DATE = trim($tmp_COM_DATE);
				
			$CMD_LEVEL = $data2[PER_LEVEL];
			$POEM_ID = (trim($POEM_ID))? $POEM_ID : 'NULL';
			$TMP_PER_ID = (trim($TMP_PER_ID))? $TMP_PER_ID : $POEM_ID+900000000;
			$POS_ID = 'NULL';
			$POEMS_ID = 'NULL';		
			$PN_CODE = trim($PN_CODE)? "'".$PN_CODE."'" : "NULL";
			$PN_CODE_NEW = trim($PN_CODE_NEW)? "'".$PN_CODE_NEW."'" : "NULL";
			$PL_CODE = $EP_CODE = "NULL";
			$PL_CODE_ASSIGN = $PN_CODE_ASSIGN = $EP_CODE_ASSIGN = "NULL";	
			$CMD_ORG3 = $CMD_ORG4 = $CMD_ORG5 = "";
			$cmd = " select ORG_ID, ORG_NAME from PER_ORG where ORG_ID IN ($ORG_ID_1, $ORG_ID_2, $ORG_ID_3) ";
			$db_dpis2->send_cmd($cmd);
			while ( $data2 = $db_dpis2->get_array() ) {
				$temp_id = trim($data2[ORG_ID]);
				$CMD_ORG3 = ($temp_id == $ORG_ID_1)?  trim($data2[ORG_NAME]) : $CMD_ORG3;
				$CMD_ORG4 = ($temp_id == $ORG_ID_2)?  trim($data2[ORG_NAME]) : $CMD_ORG4;
				$CMD_ORG5 = ($temp_id == $ORG_ID_3)?  trim($data2[ORG_NAME]) : $CMD_ORG5;						
			}
				
			$cmd = " select EN_CODE from PER_EDUCATE where PER_ID=$TMP_PER_ID and EDU_TYPE like '%1%' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$EN_CODE = (trim($data2[EN_CODE]))? "'".$data2[EN_CODE]."'" : "NULL";
				
			$LEVEL_NO = trim($LEVEL_NO)? "'".$LEVEL_NO."'" : "NULL";
			if (!$CMD_OLD_SALARY) $CMD_OLD_SALARY = 0; 
			if (!$CMD_SALARY) $CMD_SALARY = 0; 

			$cmd = " insert into PER_COMDTL (COM_ID, CMD_SEQ, PER_ID, EN_CODE, CMD_DATE, CMD_POSITION, 
							CMD_LEVEL, CMD_ORG1, CMD_ORG2, CMD_ORG3, CMD_ORG4, CMD_ORG5, 
							CMD_OLD_SALARY, PL_CODE, PN_CODE, EP_CODE, CMD_AC_NO, CMD_ACCOUNT, 
							POS_ID, POEM_ID, POEMS_ID, LEVEL_NO, CMD_SALARY, CMD_SPSALARY, 
							PL_CODE_ASSIGN, PN_CODE_ASSIGN, EP_CODE_ASSIGN, 
							CMD_NOTE1, CMD_NOTE2, MOV_CODE, CMD_DATE2, CMD_SAL_CONFIRM,   
							PER_CARDNO, CMD_POS_NO_NAME, CMD_POS_NO, UPDATE_USER, UPDATE_DATE )
							values ($COM_ID, $cmd_seq, $TMP_PER_ID, $EN_CODE, '$CMD_DATE', '$CMD_POSITION', 
							'$CMD_LEVEL', '$CMD_ORG1', '$CMD_ORG2', '$CMD_ORG3', '$CMD_ORG4', '$CMD_ORG5', 
							$CMD_OLD_SALARY, $PL_CODE, $PN_CODE, $EP_CODE, '$CMD_AC_NO', '$CMD_ACCOUNT', 
							$POS_ID, $POEM_ID, $POEMS_ID, $LEVEL_NO, $CMD_SALARY, 0, 
							$PL_CODE_ASSIGN, $PN_CODE_NEW, $EP_CODE_ASSIGN, 
							'$CMD_NOTE1', '$CMD_NOTE2', '$MOV_CODE', '$CMD_DATE2', 0, 
							'$PER_CARDNO', '$POEM_NO_NAME', '$POEM_NO', $SESS_USERID, '$UPDATE_DATE' ) ";			  
			$db_dpis1->send_cmd($cmd);
			//echo "$cmd<br>==================<br>";
			//$db_dpis1->show_error();
			//echo "<br>end ". ++$i  ."=======================<br>";
		}	// end while
		
		if ($count_temp)
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $ADD_PERSON_TITLE$COM_TYPE_NM [".trim($COM_ID)." : ".$COM_NO." : ".$COM_TYPE."]");
?>