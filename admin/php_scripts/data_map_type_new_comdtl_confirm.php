<?
		// ให้ insert ข้อมูลจาก per_comdtl ไป per_personal และ per_positionhis
		$cmd = "  	select 	PER_ID, CMD_DATE, POS_ID, a.LEVEL_NO, CMD_SALARY, 
							CMD_SPSALARY, MOV_CODE, PL_CODE_ASSIGN, PN_CODE_ASSIGN, 
							EP_CODE_ASSIGN, CMD_NOTE1, PL_CODE, b.LEVEL_NAME 
					from		PER_COMDTL a, PER_LEVEL b 
					where	COM_ID=$COM_ID and a.LEVEL_NO = b.LEVEL_NO ";
		$db_dpis->send_cmd($cmd);
//echo "$cmd<br>";		
//$db_dpis->show_error();
		while ($data = $db_dpis->get_array()) {
			$tmp_PER_ID = trim($data[PER_ID]);
			$tmp_CMD_DATE = trim($data[CMD_DATE]);
			$tmp_POS_ID = (trim($data[POS_ID]))? trim($data[POS_ID]) : "NULL";
			$tmp_LEVEL_NO = trim($data[LEVEL_NO]);
			$tmp_LEVEL_NAME = trim($data[LEVEL_NAME]);
			$tmp_CMD_SALARY = trim($data[CMD_SALARY]);
			$tmp_CMD_SPSALARY = trim($data[CMD_SPSALARY]);
			$tmp_MOV_CODE = trim($data[MOV_CODE]);
			$tmp_PL_CODE_ASSIGN = trim($data[PL_CODE_ASSIGN]);
			$tmp_PN_CODE_ASSIGN = trim($data[PN_CODE_ASSIGN]);
			$tmp_EP_CODE_ASSIGN = trim($data[EP_CODE_ASSIGN]);		
			$tmp_CMD_NOTE1 = (trim($data[CMD_NOTE1]))? str_replace("'", "&rsquo;", trim($data[CMD_NOTE1])) : "";
			$tmp_PL_CODE = trim($data[PL_CODE]);
			
			$cmd = " select PL_TYPE from PER_LINE where trim(PL_CODE)='$tmp_PL_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PL_TYPE = trim($data2[PL_TYPE]);
			
			$cmd = " select CL_NAME from PER_LINE where trim(PL_CODE)='$tmp_PL_CODE_ASSIGN' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$tmp_CL_NAME = trim($data2[CL_NAME]);
			
			$tmp_date = explode("-", $tmp_CMD_DATE);
			// 86400 วินาที = 1 วัน
			$before_cmd_date = (mktime(0, 0, 0, $tmp_date[1], substr($tmp_date[2],0,2), $tmp_date[0]) - 86400);
			$before_cmd_date = date("Y-m-d", $before_cmd_date);
			
			$PM_CODE = $LEVEL_NO = $PT_CODE = $POH_ASS_ORG = "";
			$PL_CODE = $PN_CODE = $EP_CODE = "NULL";
			$cmd = " select ORG_NAME from PER_PERSONAL a, PER_ORG_ASS b where PER_ID=$tmp_PER_ID and a.ORG_ID=b.ORG_ID ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$POH_ASS_ORG = $data2[ORG_NAME];	

			$cmd = "  select POS_NO, ORG_ID, ORG_ID_1, ORG_ID_2, PM_CODE, PL_CODE, PT_CODE, CL_NAME 
							  from PER_POSITION where POS_ID=$tmp_POS_ID  ";
			$db_dpis2->send_cmd($cmd);		
			$data2 = $db_dpis2->get_array();
			$POH_POS_NO = trim($data2[POS_NO]);
			$PM_CODE = (trim($data2[PM_CODE]))? "'".trim($data2[PM_CODE])."'" : "NULL";
			$PL_CODE = "'".trim($data2[PL_CODE])."'";
			$PT_CODE = trim($data2[PT_CODE]);
			if ($PT_CODE == '12') $tmp_PT_CODE = "11"; else $tmp_PT_CODE = $PT_CODE;
			$CMD_CL_NAME = trim($data2[CL_NAME]);
			
			$ORG_ID_3 = trim($data2[ORG_ID]);	
			$ORG_ID_4 = trim($data2[ORG_ID_1]);
			$ORG_ID_5 = trim($data2[ORG_ID_2]);
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $ORG_ID_4 ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_NAME_4 = $data2[ORG_NAME];

			$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $ORG_ID_5 ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_NAME_5 = $data2[ORG_NAME];				
			 			
			$cmd = "  select ORG_ID_REF, CT_CODE, PV_CODE, AP_CODE from PER_ORG where ORG_ID=$ORG_ID_3 and OL_CODE='03'";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_ID_2 = trim($data2[ORG_ID_REF]);
			$CT_CODE = (trim($data2[CT_CODE]))? "'".trim($data2[CT_CODE])."'" : "NULL";
			$PV_CODE = (trim($data2[PV_CODE]))? "'".trim($data2[PV_CODE])."'" : "NULL";
			$AP_CODE = (trim($data2[AP_CODE]))? "'".trim($data2[AP_CODE])."'" : "NULL";
			$cmd = "  select ORG_ID_REF from PER_ORG where ORG_ID=$ORG_ID_2 and OL_CODE='02'";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_ID_1 = trim($data2[ORG_ID_REF]);
			$ORG_ID_1 = (trim($ORG_ID_1))? trim($ORG_ID_1) : "NULL";
			$ORG_ID_2 = (trim($ORG_ID_2))? trim($ORG_ID_2) : "NULL";
			$ORG_ID_3 = (trim($ORG_ID_3))? trim($ORG_ID_3) : "NULL";				
			$ORG_NAME_1 = $ORG_NAME_2 = $ORG_NAME_3 = "";
			$cmd = " select ORG_ID, ORG_NAME FROM PER_ORG where ORG_ID in ( $ORG_ID_1, $ORG_ID_2, $ORG_ID_3 ) ";
			$db_dpis2->send_cmd($cmd);
			while ($data2 = $db_dpis2->get_array()) {
				$ORG_NAME_1 = ($data2[ORG_ID] == $ORG_ID_1)? trim($data2[ORG_NAME]) : $ORG_NAME_1;
				$ORG_NAME_2 = ($data2[ORG_ID] == $ORG_ID_2)? trim($data2[ORG_NAME]) : $ORG_NAME_2;
				$ORG_NAME_3 = ($data2[ORG_ID] == $ORG_ID_3)? trim($data2[ORG_NAME]) : $ORG_NAME_3;
			}
			
			$cmd = " select LEVEL_NO_MIN, LEVEL_NO_MAX from PER_CO_LEVEL where trim(CL_NAME)='$CMD_CL_NAME' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$LEVEL_NO_MIN = trim($data2[LEVEL_NO_MIN]);
			$LEVEL_NO_MAX = trim($data2[LEVEL_NO_MAX]);
			for ($LVL=$LEVEL_NO_MIN;$LVL<=$LEVEL_NO_MAX;$LVL++){
				$LVL = str_pad(trim($LVL), 2, "0", STR_PAD_LEFT);
				$cmd = " select NEW_LEVEL_NO from PER_MAP_POS where LEVEL_NO='$LVL' and PL_TYPE=$PL_TYPE ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$NEW_LEVEL_NO = $data2[NEW_LEVEL_NO];
				if ($PL_TYPE==2 || $PL_TYPE==3 || $PL_TYPE==5) 
					if ($LEVEL_NO_MIN=="08" && $PT_CODE=="31") $NEW_LEVEL_NO = "D1";
					elseif ($LEVEL_NO_MIN=="09" && $PT_CODE=="32") $NEW_LEVEL_NO = "D2";
		
				if ($LVL == $LEVEL_NO_MIN+0) $NEW_LEVEL_NO_MIN = $NEW_LEVEL_NO;
				if ($LVL == $LEVEL_NO_MAX+0) $NEW_LEVEL_NO_MAX = $NEW_LEVEL_NO;
				$cmd = " select LEVEL_NAME from PER_LEVEL where trim(LEVEL_NO)='$NEW_LEVEL_NO' ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$LEVEL_NAME = trim($data2[LEVEL_NAME]);
				$arr_temp = explode(" ", $LEVEL_NAME);
				$LEVEL_NAME = $arr_temp[1];
				if ($NEW_LEVEL_NO=="D1") $LEVEL_NAME = "อำนวยการระดับต้น";
				elseif ($NEW_LEVEL_NO=="D2") $LEVEL_NAME = "อำนวยการระดับสูง";
				elseif ($NEW_LEVEL_NO=="M1") $LEVEL_NAME = "บริหารระดับต้น";
				elseif ($NEW_LEVEL_NO=="M2") $LEVEL_NAME = "บริหารระดับสูง";
				else $LEVEL_NAME = str_replace("ระดับ", "", $LEVEL_NAME);

				if ($LEVEL_NAME != $OLD_LEVEL_NAME) {
					if ($LEVEL_NO_MIN==$LVL)
						$NEW_LEVEL_NAME = $LEVEL_NAME;
					else
						$NEW_LEVEL_NAME = (trim($LEVEL_NAME)?($NEW_LEVEL_NAME .' หรือ '. $LEVEL_NAME):$NEW_LEVEL_NAME);
					$OLD_LEVEL_NAME = $LEVEL_NAME;
				}
			}

			$cmd = " select LEVEL_NO_MIN, LEVEL_NO_MAX from PER_CO_LEVEL where trim(CL_NAME)='$NEW_LEVEL_NAME' ";
			$count_data = $db_dpis2->send_cmd($cmd);
			if (!$count_data) {
				$cmd = " INSERT INTO PER_CO_LEVEL (CL_NAME, LEVEL_NO_MIN, LEVEL_NO_MAX, CL_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('$NEW_LEVEL_NAME', '$NEW_LEVEL_NO_MIN', '$NEW_LEVEL_NO_MAX', 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
			}

			// update status of PER_PERSONAL 
			$cmd = " 	update PER_PERSONAL set  POS_ID=$tmp_POS_ID, LEVEL_NO='$tmp_LEVEL_NO', MOV_CODE='$tmp_MOV_CODE', 
								PER_STATUS=1, UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE'
					where PER_ID=$tmp_PER_ID ";
			// PER_SALARY=$tmp_CMD_SALARY, PER_SPSALARY=$tmp_CMD_SPSALARY, 					
			$db_dpis1->send_cmd($cmd);	
			//$db_dpis1->show_error();	
			//echo "<br>";

		// insert into PER_POS_MOVE 
			$cmd = " insert into PER_POS_MOVE
								(POS_ID, POS_DATE, OT_CODE, PL_CODE, PM_CODE, CL_NAME, PT_CODE, 
								SKILL_CODE, PC_CODE, POS_CONDITION, POS_SALARY, POS_MGTSALARY, 
								ORG_ID, ORG_ID_1, ORG_ID_2, POS_STATUS, POS_DOC_NO, POS_REMARK,
								UPDATE_USER, UPDATE_DATE)
							 select POS_ID, '$tmp_CMD_DATE', OT_CODE, PL_CODE, PM_CODE, CL_NAME, PT_CODE,
								SKILL_CODE, PC_CODE, POS_CONDITION, POS_SALARY, POS_MGTSALARY,
								ORG_ID, ORG_ID_1, ORG_ID_2, POS_STATUS, POS_DOC_NO, POS_REMARK,
								$SESS_USERID, '$UPDATE_DATE' from PER_POSITION where POS_ID=$tmp_POS_ID ";
			$db_dpis1->send_cmd($cmd);
			//$db_dpis1->show_error();
			//echo "<br>";
				
			// update status of PER_POSITION 
			$cmd = " update PER_POSITION set PL_CODE='$tmp_PL_CODE_ASSIGN', CL_NAME='$NEW_LEVEL_NAME', PT_CODE='$tmp_PT_CODE',
								POS_SALARY=$tmp_CMD_SALARY, POS_CHANGE_DATE='$tmp_CMD_DATE', 
								UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE'								
						  where POS_ID=$tmp_POS_ID ";
			$db_dpis1->send_cmd($cmd);	
			//$db_dpis1->show_error();				
			//echo "<br>";
			
			// update and insert into PER_POSITIONHIS	
			$cmd = " select POH_ID from PER_POSITIONHIS where PER_ID=$tmp_PER_ID order by PER_ID, POH_EFFECTIVEDATE desc ";
			$db_dpis1->send_cmd($cmd);
			$data1 = $db_dpis1->get_array();
			$tmp_POH_ID = trim($data1[POH_ID]);
			$cmd = " update PER_POSITIONHIS set POH_ENDDATE='$before_cmd_date' where POH_ID=$tmp_POH_ID";
			$db_dpis1->send_cmd($cmd);	
			//$db_dpis1->show_error();				
			//echo "<br>";
			
			$cmd = " select max(POH_ID) as max_id from PER_POSITIONHIS ";
			$db_dpis1->send_cmd($cmd);
			$data = $db_dpis1->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$POH_ID = $data[max_id] + 1; 			 
			$cmd = " 	insert into PER_POSITIONHIS 
						  	(POH_ID, PER_ID, POH_EFFECTIVEDATE, MOV_CODE, POH_DOCNO, POH_DOCDATE, POH_ENDDATE, 
						   	POH_POS_NO, PM_CODE, LEVEL_NO, PL_CODE, PN_CODE, EP_CODE, PT_CODE, 
							CT_CODE, PV_CODE, AP_CODE, POH_ORGMGT, ORG_ID_1, ORG_ID_2, ORG_ID_3, 
							POH_UNDER_ORG1, POH_UNDER_ORG2, POH_ASS_ORG, 
						   	POH_SALARY, POH_SALARY_POS, POH_REMARK, 
							POH_ORG1, POH_ORG2, POH_ORG3, 
							UPDATE_USER, UPDATE_DATE)
						  	values 
						  	($POH_ID, $tmp_PER_ID, '$tmp_CMD_DATE', '$tmp_MOV_CODE', '$COM_NO', '$tmp_COM_DATE', '', 
						   	'$POH_POS_NO', $PM_CODE, '$tmp_LEVEL_NO', '$tmp_PL_CODE_ASSIGN', $PN_CODE, $EP_CODE, '$PT_CODE', 
							$CT_CODE, $PV_CODE, $AP_CODE, '2', $ORG_ID_1, $ORG_ID_2, $ORG_ID_3, 
							'$ORG_NAME_4', '$ORG_NAME_5', '$POH_ASS_ORG', 
						   	$tmp_CMD_SALARY, $tmp_CMD_SPSALARY, '$tmp_CMD_NOTE1', 
							'$ORG_NAME_1', '$ORG_NAME_2', '$ORG_NAME_3', 
							$SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis1->send_cmd($cmd);
			//$db_dpis1->show_error();
			//echo "<br>============================<br>";

		}	// end while 		

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูลการดำรงตำแหน่ง และเพิ่มประวัติการดำรงตำแหน่งเมื่อยืนยันข้อมูลบัญชีแนบท้ายคำสั่งจัดคนลงตาม พรบ. ใหม่ [$COM_ID : $PER_ID : $POH_ID]");
?>