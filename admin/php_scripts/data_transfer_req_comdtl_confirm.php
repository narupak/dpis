<?
		// ให้ insert ข้อมูลจาก per_comdtl ไป per_personal และ per_positionhis
		$cmd = "  	select 	PER_ID, CMD_DATE, POS_ID, POEM_ID, POEMS_ID,POT_ID, LEVEL_NO, CMD_SALARY, 
									CMD_SPSALARY, MOV_CODE, PL_CODE_ASSIGN, PN_CODE_ASSIGN, EP_CODE_ASSIGN, TP_CODE_ASSIGN,
									CMD_NOTE1, CMD_NOTE2, CMD_ORG1, CMD_ORG2, CMD_ORG3, CMD_ORG4, CMD_ORG5, 
									CMD_ORG6, CMD_ORG7, CMD_ORG8, PL_NAME_WORK, ORG_NAME_WORK, CMD_SEQ, CMD_NOW ,CMD_DATE4 
				from		PER_COMDTL 
				where	COM_ID=$COM_ID";
		$db_dpis->send_cmd($cmd);
	//	$db_dpis->show_error(); echo "<hr>";
		while ($data = $db_dpis->get_array()) {
			$tmp_PER_ID = $data[PER_ID];
			$tmp_CMD_DATE = trim($data[CMD_DATE]);
                        $tmp_CMD_DATE4 = trim($data[CMD_DATE4]);
			$tmp_POS_ID = $data[POS_ID];
			$tmp_POEM_ID = $data[POEM_ID];
			$tmp_POEMS_ID = $data[POEMS_ID];
			$tmp_POT_ID = $data[POT_ID];
			$tmp_LEVEL_NO = trim($data[LEVEL_NO]);
			$tmp_CMD_SALARY = $data[CMD_SALARY];
			$tmp_CMD_SPSALARY = $data[CMD_SPSALARY];
			$tmp_MOV_CODE = trim($data[MOV_CODE]);
			$tmp_PL_CODE_ASSIGN = trim($data[PL_CODE_ASSIGN]);
			$tmp_PN_CODE_ASSIGN = trim($data[PN_CODE_ASSIGN]);
			$tmp_EP_CODE_ASSIGN = trim($data[EP_CODE_ASSIGN]);		
			$tmp_TP_CODE_ASSIGN = trim($data[TP_CODE_ASSIGN]);	
			$tmp_CMD_NOTE1 = (trim($data[CMD_NOTE1]))? trim($data[CMD_NOTE1]) : "&rsquo;";
			$tmp_CMD_NOTE2 = (trim($data[CMD_NOTE2]))? trim($data[CMD_NOTE2]) : "&rsquo;";
			$tmp_PL_NAME_WORK = trim($data[PL_NAME_WORK]);		
			$tmp_ORG_NAME_WORK = trim($data[ORG_NAME_WORK]);		
			$tmp_CMD_SEQ = trim($data[CMD_SEQ]);
			$tmp_POH_ORG_TRANSFER = trim($data[CMD_ORG1]) . ' ' . trim($data[CMD_ORG2]) . ' ' . trim($data[CMD_ORG3]) . ' ' . trim($data[CMD_ORG4]) . ' ' . trim($data[CMD_ORG5]);			
			$tmp_CMD_NOW = trim($data[CMD_NOW]);
			if ($tmp_CMD_NOW=="Y") $tmp_CMD_DATE = $tmp_COM_DATE;
			
			$tmp_date = explode("-", $tmp_CMD_DATE);
			// 86400 วินาที = 1 วัน
			$before_cmd_date = (mktime(0, 0, 0, $tmp_date[1], substr($tmp_date[2],0,2), $tmp_date[0]) - 86400);
			$before_cmd_date = date("Y-m-d", $before_cmd_date);
			
			$PM_CODE = $PL_CODE = $LEVEL_NO = $PN_CODE = $EP_CODE =  $TP_CODE = "";
			if (trim($tmp_POS_ID)) {									// ตำแหน่งข้าราชการ
				$cmd = "  select POS_NO, POS_NO_NAME, DEPARTMENT_ID, ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5, PM_CODE, PL_CODE 
							   from PER_POSITION where POS_ID=$tmp_POS_ID  ";
				$db_dpis2->send_cmd($cmd);
				//$db_dpis2->show_error(); echo "<hr>";
				$data2 = $db_dpis2->get_array();
				$POH_POS_NO = trim($data2[POS_NO]);
				$POH_POS_NO_NAME = trim($data2[POS_NO_NAME]);
				$PM_CODE = trim($data2[PM_CODE]);
				$PL_CODE = trim($data2[PL_CODE]);
			} elseif (trim($tmp_POEM_ID)) {						// ตำแหน่งลูกจ้างประจำ
				$cmd = "  select POEM_NO, POEM_NO_NAME, DEPARTMENT_ID, ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5, PN_CODE 
							   from PER_POS_EMP where POEM_ID=$tmp_POEM_ID  ";				
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$POH_POS_NO = trim($data2[POEM_NO]);
				$POH_POS_NO_NAME = trim($data2[POEM_NO_NAME]);				
				$PN_CODE = trim($data2[PN_CODE]);	
			} elseif (trim($tmp_POEMS_ID)) {						// ตำแหน่งพนักงานราชการ
				$cmd = "  select POEMS_NO, POEMS_NO_NAME, DEPARTMENT_ID, ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5, EP_CODE 
							   from PER_POS_EMPSER where POEMS_ID=$tmp_POEMS_ID  ";	
				$db_dpis2->send_cmd($cmd);
				//$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$POH_POS_NO = trim($data2[POEMS_NO]);
				$POH_POS_NO_NAME = trim($data2[POEMS_NO_NAME]);				
				$EP_CODE = trim($data2[EP_CODE]);	
			} elseif (trim($tmp_POT_ID)) {						// ตำแหน่งลูกจ้างชั่วคราว
				$cmd = "  select POT_NO, POT_NO_NAME, DEPARTMENT_ID, ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5, TP_CODE 
							   from PER_POS_TEMP where POT_ID=$tmp_POT_ID  ";	
				$db_dpis2->send_cmd($cmd);
				//$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$POH_POS_NO = trim($data2[POT_NO]);
				$POH_POS_NO_NAME = trim($data2[POT_NO_NAME]);				
				$TP_CODE = trim($data2[TP_CODE]);	
			}				   

			$ORG_ID_2 = trim($data2[DEPARTMENT_ID]);	
			$ORG_ID_3 = trim($data2[ORG_ID]);		
			$ORG_ID_4 = trim($data2[ORG_ID_1]);
			$ORG_ID_5 = trim($data2[ORG_ID_2]);
			$ORG_ID_6 = trim($data2[ORG_ID_3]);		
			$ORG_ID_7 = trim($data2[ORG_ID_4]);
			$ORG_ID_8 = trim($data2[ORG_ID_5]);

			$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $ORG_ID_4 ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_NAME_4 = $data2[ORG_NAME];

			$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $ORG_ID_5 ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_NAME_5 = $data2[ORG_NAME];				

			$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $ORG_ID_6 ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_NAME_6 = $data2[ORG_NAME];

			$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $ORG_ID_7 ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_NAME_7 = $data2[ORG_NAME];				

			$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $ORG_ID_8 ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_NAME_8 = $data2[ORG_NAME];				

			$cmd = "  select CT_CODE, PV_CODE, AP_CODE from PER_ORG where ORG_ID=$ORG_ID_3 and OL_CODE='03'";
			$db_dpis2->send_cmd($cmd);
			//$db_dpis2->show_error();
			//echo "<hr>";
			$data2 = $db_dpis2->get_array();
			$CT_CODE = trim($data2[CT_CODE]);
			$PV_CODE = trim($data2[PV_CODE]);
			$AP_CODE = trim($data2[AP_CODE]);
			//echo "AP_CODE = $AP_CODE<hr>";
			$cmd = "  select ORG_ID_REF from PER_ORG where ORG_ID=$ORG_ID_2 and OL_CODE='02'";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_ID_1 = trim($data2[ORG_ID_REF]);
/*			$ORG_ID_1 = (trim($ORG_ID_1))? trim($ORG_ID_1) : "NULL";
			$ORG_ID_2 = (trim($ORG_ID_2))? trim($ORG_ID_2) : "NULL";
			$ORG_ID_3 = (trim($ORG_ID_3))? trim($ORG_ID_3) : "NULL";				
			$ORG_NAME_1 = $ORG_NAME_2 = $ORG_NAME_3 = ""; */
			$cmd = " select ORG_ID, ORG_NAME FROM PER_ORG where ORG_ID in ( $ORG_ID_1, $ORG_ID_2, $ORG_ID_3 ) ";
			$db_dpis2->send_cmd($cmd);
			while ($data2 = $db_dpis2->get_array()) {
				$ORG_NAME_1 = ($data2[ORG_ID] == $ORG_ID_1)? trim($data2[ORG_NAME]) : $ORG_NAME_1;
				$ORG_NAME_2 = ($data2[ORG_ID] == $ORG_ID_2)? trim($data2[ORG_NAME]) : $ORG_NAME_2;
				$ORG_NAME_3 = ($data2[ORG_ID] == $ORG_ID_3)? trim($data2[ORG_NAME]) : $ORG_NAME_3;
			}			
			if (!$tmp_POS_ID) $tmp_POS_ID = "NULL";
			if (!$tmp_POEM_ID) $tmp_POEM_ID = "NULL";
			if (!$tmp_POEMS_ID) $tmp_POEMS_ID = "NULL";
			if (!$tmp_POT_ID) $tmp_POT_ID = "NULL";
			if (!$tmp_CMD_SALARY) $tmp_CMD_SALARY = 0;
			if (!$tmp_CMD_SPSALARY) $tmp_CMD_SPSALARY = 0;
			$ES_CODE = "02";
			//==================================================
			
			$cmd = " update PER_PERSONAL set 
							POS_ID = $tmp_POS_ID, 
							POEM_ID = $tmp_POEM_ID, 
							POEMS_ID = $tmp_POEMS_ID, 
							POT_ID = $tmp_POT_ID, 
							LEVEL_NO = '$tmp_LEVEL_NO', 
							PER_SALARY = $tmp_CMD_SALARY,
							PER_SPSALARY = $tmp_CMD_SPSALARY, 
							PER_OCCUPYDATE = '$tmp_CMD_DATE', 
							MOV_CODE='$tmp_MOV_CODE', 
							ES_CODE='$ES_CODE', 
							PER_DOCNO='$COM_NO', 
							PER_DOCDATE='$tmp_COM_DATE', 
							PAY_ID = $tmp_POS_ID, 
							PER_STATUS=1,
							UPDATE_USER = $SESS_USERID, 
							UPDATE_DATE = '$UPDATE_DATE'
						where PER_ID=$tmp_PER_ID ";
			$db_dpis1->send_cmd($cmd);	
			//$db_dpis1->show_error();	echo "<hr>";
		//=====================================

			if ($MFA_FLAG==1) {
				$cmd = " 	update PER_PERSONAL set  DEPARTMENT_ID=$ORG_ID_2 where PER_ID=$tmp_PER_ID ";
				$db_dpis1->send_cmd($cmd);	
			}

			if ($tmp_POS_ID) {
				$cmd = " update PER_POSITION set POS_CHANGE_DATE='$tmp_CMD_DATE' where POS_ID=$tmp_POS_ID";
				$db_dpis1->send_cmd($cmd);	
				//$db_dpis1->show_error();				
				//echo "<br>";
			}

			// update and insert into PER_POSITIONHIS	
			$cmd = " select POH_ID from PER_POSITIONHIS where PER_ID=$tmp_PER_ID order by PER_ID, POH_EFFECTIVEDATE desc, POH_SEQ_NO desc ";
			$db_dpis1->send_cmd($cmd);
			$data1 = $db_dpis1->get_array();
			$tmp_POH_ID = trim($data1[POH_ID]);
			$cmd = " update PER_POSITIONHIS set POH_ENDDATE='$before_cmd_date' where POH_ID=$tmp_POH_ID";
			//$db_dpis1->send_cmd($cmd);	
			// === ยืนยันคำสั่งบรรจุ/รับโอนในกรณีที่มีประวัติการดำรงตำแหน่งเดิม ไม่ต้องปิด record ก่อนหน้าและให้ write record ใหม่ด้วย ?????????????
			// === ที่ให้แก้ตามคำพูดด้านบน จึง comment คำสั่งไม่ให้ส่ง sql ไป query 
			
			$POH_ORG_TRANSFER = "";
			if ($tmp_MOV_CODE=="105" || $tmp_MOV_CODE=="10510" || $tmp_MOV_CODE=="10520") 
				$POH_ORG_TRANSFER = trim($tmp_POH_ORG_TRANSFER);			

			$cmd = " update PER_POSITIONHIS set POH_LAST_POSITION='N' where PER_ID=$tmp_PER_ID";
			$db_dpis1->send_cmd($cmd);	

			$cmd = " select max(POH_ID) as max_id from PER_POSITIONHIS ";
			$db_dpis1->send_cmd($cmd);
			$data = $db_dpis1->get_array();
			$data= array_change_key_case($data, CASE_LOWER);
			$POH_ID = $data[max_id] + 1; 	
			
			$PM_CODE = (trim($PM_CODE))? "'$PM_CODE'" : "NULL";
			$LEVEL_NO = (trim($LEVEL_NO))? "'$LEVEL_NO'" : "NULL";
			$PT_CODE = (trim($PT_CODE))? "'$PT_CODE'" : "NULL";
			$CT_CODE_tmp = (trim($CT_CODE))? "'$CT_CODE'" : "'140'";
			$PV_CODE = (trim($_POST[PV_CODE]))? "'$_POST[PV_CODE]'" : "NULL";
			$AP_CODE = (trim($AP_CODE))? "'$AP_CODE'" : "NULL";
			$PER_CARDNO = (trim($PER_CARDNO))? "'$PER_CARDNO'" : "NULL";

			if ($COM_PER_TYPE==1) {	
				$PL_CODE = (trim($PL_CODE))? "'$PL_CODE'" : "NULL";	
				$PN_CODE = $EP_CODE = $TP_CODE =  "NULL";
			} elseif ($COM_PER_TYPE==2) {
				$PN_CODE = (trim($PN_CODE))? "'$PN_CODE'" : "NULL";	
				$PL_CODE = $EP_CODE = $TP_CODE =  "NULL";
			} elseif ($COM_PER_TYPE==3) {
				$EP_CODE = (trim($EP_CODE))? "'$EP_CODE'" : "NULL";	
				$PL_CODE = $PN_CODE = $TP_CODE = "NULL";
			} elseif ($COM_PER_TYPE==4) {
				$TP_CODE = (trim($TP_CODE))? "'$TP_CODE'" : "NULL";	
				$PL_CODE = $PN_CODE = $EP_CODE = "NULL";
			}
			
			$cmd = " 	insert into PER_POSITIONHIS (POH_ID, PER_ID, POH_EFFECTIVEDATE, MOV_CODE, 
								POH_DOCNO, POH_DOCDATE, POH_ENDDATE, POH_POS_NO, PM_CODE, LEVEL_NO, 
								PL_CODE, PN_CODE, EP_CODE, TP_CODE, CT_CODE, PV_CODE, AP_CODE, POH_ORGMGT, 
								ORG_ID_1, ORG_ID_2, ORG_ID_3, POH_UNDER_ORG1, POH_UNDER_ORG2, 
						   		POH_UNDER_ORG3, POH_UNDER_ORG4, POH_UNDER_ORG5, POH_SALARY, 
								POH_SALARY_POS, POH_REMARK, POH_REMARK1, POH_REMARK2, POH_ORG1, POH_ORG2, POH_ORG3, POH_PL_NAME, 
								POH_ORG, POH_CMD_SEQ, UPDATE_USER, UPDATE_DATE, POH_ORG_TRANSFER, 
								POH_SEQ_NO, POH_LAST_POSITION, ES_CODE, POH_LEVEL_NO, POH_ISREAL, POH_POS_NO_NAME)
						  		values ($POH_ID, $tmp_PER_ID, '$tmp_CMD_DATE', '$tmp_MOV_CODE', '$COM_NO', '$tmp_COM_DATE', '$tmp_CMD_DATE4', 
						   		'$POH_POS_NO', $PM_CODE, '$tmp_LEVEL_NO', $PL_CODE, $PN_CODE, $EP_CODE,  $TP_CODE,  
								$CT_CODE, $PV_CODE, $AP_CODE, '2', $ORG_ID_1, $ORG_ID_2, $ORG_ID_3, '$ORG_NAME_4', 
								'$ORG_NAME_5', '$ORG_NAME_6', '$ORG_NAME_7', '$ORG_NAME_8', $tmp_CMD_SALARY, 
								$tmp_CMD_SPSALARY, '$COM_NOTE', '$tmp_CMD_NOTE1', '$tmp_CMD_NOTE2', '$ORG_NAME_1', '$ORG_NAME_2', 
								'$ORG_NAME_3', '$tmp_PL_NAME_WORK', '$tmp_ORG_NAME_WORK', $tmp_CMD_SEQ, 
								$SESS_USERID, '$UPDATE_DATE', '$POH_ORG_TRANSFER', 1, 'Y', '02', '$tmp_LEVEL_NO', 'Y', '$POH_POS_NO_NAME') ";
			$db_dpis1->send_cmd($cmd);
			//echo "<hr>";
			//$db_dpis1->show_error();
			//echo "<hr>"; $db_dpis1->show_error(); 

			// update and insert into PER_SALARYHIS 
			$cmd = " select SAH_ID from PER_SALARYHIS where PER_ID=$tmp_PER_ID order by PER_ID, SAH_EFFECTIVEDATE desc, SAH_SEQ_NO desc ";
			$db_dpis1->send_cmd($cmd);
			$data1 = $db_dpis1->get_array();
			$tmp_SAH_ID = trim($data1[SAH_ID]);
			$tmp_SAH_ENDDATE = $tmp_CMD_DATE - 1;
			$cmd = " update PER_SALARYHIS set SAH_ENDDATE='$before_cmd_date' where SAH_ID=$tmp_SAH_ID";
			//$db_dpis1->send_cmd($cmd);	
			// === ยืนยันคำสั่งบรรจุ/รับโอนในกรณีที่มีประวัติการรับเงินเดือนเดิม ไม่ต้องปิด record ก่อนหน้าและให้ write record ใหม่ด้วย
			// === ที่ให้แก้ตามคำพูดด้านบน จึง comment คำสั่งไม่ให้ส่ง sql ไป query 	

			// === การบรรจุ/รับโอน ให้ตั้งค่าการเคลื่อนไหวเงินเดือนเป็น 21390 (เงินเดือนแรกบรรจุ)
			$cmd = " select MOV_CODE from PER_MOVMENT where trim(MOV_NAME)='เงินเดือนแรกบรรจุ' ";
			$db_dpis1->send_cmd($cmd);
			$data1 = $db_dpis1->get_array();
			$tmp_SALARY_MOV_CODE = trim($data1[MOV_CODE]); 
			if ($BKK_FLAG==1) {
				if (!$tmp_SALARY_MOV_CODE) $tmp_SALARY_MOV_CODE = "SA6002";
				$tmp_SM_CODE = "";
			} else { 
				if (!$tmp_SALARY_MOV_CODE) $tmp_SALARY_MOV_CODE = "21390";
				$tmp_SM_CODE = "15";
				
                                if ($tmp_MOV_CODE=="10140" || $tmp_MOV_CODE=="10150" || $tmp_MOV_CODE=="10160" || $tmp_MOV_CODE=="10170" || $tmp_MOV_CODE=="10180") 
					$tmp_SALARY_MOV_CODE = "21394";
				elseif ($tmp_MOV_CODE=="105" || $tmp_MOV_CODE=="10510" || $tmp_MOV_CODE=="10520" || $tmp_MOV_CODE=="10530" || $tmp_MOV_CODE=="10540") {
					$tmp_SALARY_MOV_CODE = "21395";
					$tmp_SM_CODE = "16";}
                                
			}
			
			$cmd = " update PER_SALARYHIS set SAH_LAST_SALARY='N' where PER_ID=$tmp_PER_ID";
			$db_dpis1->send_cmd($cmd);	

			$cmd = " select max(SAH_ID) as max_id from PER_SALARYHIS ";
			$db_dpis1->send_cmd($cmd);
			$data = $db_dpis1->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$SAH_ID = $data[max_id] + 1; 			 
			$cmd = "	insert into PER_SALARYHIS (SAH_ID, PER_ID, SAH_EFFECTIVEDATE, MOV_CODE, SAH_SALARY, SAH_DOCNO, 
								SAH_DOCDATE, SAH_ENDDATE, SAH_POSITION, SAH_ORG, EX_CODE, SAH_POS_NO, SAH_PAY_NO,
								SAH_SEQ_NO, LEVEL_NO, SAH_LAST_SALARY, SAH_CMD_SEQ, SM_CODE, UPDATE_USER, UPDATE_DATE) 
								values ($SAH_ID, $tmp_PER_ID, '$tmp_CMD_DATE', '$tmp_SALARY_MOV_CODE', $tmp_CMD_SALARY, '$COM_NO',  
								'$tmp_COM_DATE', '', '$tmp_PL_NAME_WORK', '$tmp_ORG_NAME_WORK', '024', '$POH_POS_NO', '$POH_POS_NO', 
								1, '$tmp_LEVEL_NO', 'Y', $tmp_CMD_SEQ, '$tmp_SM_CODE', $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis1->send_cmd($cmd);
			//echo "$cmd<br>";
			//	$db_dpis1->show_error();	
		}
                
?>