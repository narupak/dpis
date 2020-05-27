<?
		// ให้ insert ข้อมูลจาก per_comdtl ไป per_personal และ per_positionhis
		$cmd = "  	select 	PER_ID, CMD_DATE, POS_ID, POEM_ID, POEMS_ID , POT_ID, LEVEL_NO, CMD_SALARY, 
								CMD_SPSALARY, MOV_CODE, PL_CODE_ASSIGN, PN_CODE_ASSIGN, 
								EP_CODE_ASSIGN, CMD_NOTE1, CMD_NOTE2, PER_CARDNO, PL_NAME_WORK, ORG_NAME_WORK, CMD_SEQ, EN_CODE,CMD_POS_NO_NAME, CMD_POS_NO
					from		PER_COMDTL 
					where	COM_ID=$COM_ID";
		$db_dpis->send_cmd($cmd);
		while ($data = $db_dpis->get_array()) {
			$tmp_PER_ID = trim($data[PER_ID]);
			$tmp_CMD_DATE = trim($data[CMD_DATE]);
			$tmp_POS_ID = trim($data[POS_ID]);
			$tmp_POEM_ID = trim($data[POEM_ID]);
			$tmp_POEMS_ID = trim($data[POEMS_ID]);
			$tmp_POT_ID = trim($data[POT_ID]);
			$tmp_LEVEL_NO = trim($data[LEVEL_NO]);
			$tmp_CMD_SALARY = trim($data[CMD_SALARY]);
			$tmp_CMD_SPSALARY = trim($data[CMD_SPSALARY]);
			$tmp_MOV_CODE = trim($data[MOV_CODE]);
			$tmp_PER_CARDNO = trim($data[PER_CARDNO]);
			$tmp_PL_CODE_ASSIGN = trim($data[PL_CODE_ASSIGN]);
			$tmp_PN_CODE_ASSIGN = trim($data[PN_CODE_ASSIGN]);
			$tmp_EP_CODE_ASSIGN = trim($data[EP_CODE_ASSIGN]);		
			$tmp_CMD_NOTE1 = (trim($data[CMD_NOTE1]))? str_replace("'", "&rsquo;", trim($data[CMD_NOTE1])) : "";
			$tmp_CMD_NOTE2 = (trim($data[CMD_NOTE2]))? str_replace("'", "&rsquo;", trim($data[CMD_NOTE2])) : "";
			$tmp_PL_NAME_WORK = trim($data[PL_NAME_WORK]);		
			$tmp_ORG_NAME_WORK = trim($data[ORG_NAME_WORK]);		
			$tmp_CMD_SEQ = trim($data[CMD_SEQ]);
			$tmp_EN_CODE = trim($data[EN_CODE]);
						
			$tmp_date = explode("-", $tmp_CMD_DATE);
			// 86400 วินาที = 1 วัน
			$before_cmd_date = (mktime(0, 0, 0, $tmp_date[1], substr($tmp_date[2],0,2), $tmp_date[0]) - 86400);
			$before_cmd_date = date("Y-m-d", $before_cmd_date);

			$PM_CODE = $PL_CODE = $LEVEL_NO = $PN_CODE = $EP_CODE = "";
			if (trim($tmp_POS_ID)) {								// ตำแหน่งข้าราชการ
				$cmd = "  select POS_NO,POS_NO_NAME, ORG_ID, ORG_ID_1, ORG_ID_2, PM_CODE, PL_CODE 
							   from PER_POSITION where POS_ID=$tmp_POS_ID  ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$POH_POS_NO = trim($data2[POS_NO]);
				$POH_POS_NO_NAME = trim($data2[POS_NO_NAME]);
				$PM_CODE = trim($data2[PM_CODE]);
				$PL_CODE = trim($data2[PL_CODE]);
			} elseif (trim($tmp_POEM_ID)) {						// ตำแหน่งลูกจ้างประจำ
				$cmd = "  select POEM_NO, POEM_NO_NAME, ORG_ID, ORG_ID_1, ORG_ID_2, PN_CODE 
							   from PER_POS_EMP where POEM_ID=$tmp_POEM_ID  ";				
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$POH_POS_NO = trim($data2[POEM_NO]);	
				$POH_POS_NO_NAME = trim($data2[POEM_NO_NAME]);				
				$PN_CODE = trim($data2[PN_CODE]);	
			} elseif (trim($tmp_POEMS_ID)) {						// ตำแหน่งพนักงานราชการ
				$cmd = "  select POEMS_NO, POEMS_NO_NAME, ORG_ID, ORG_ID_1, ORG_ID_2, EP_CODE 
							   from PER_POS_EMPSER where POEMS_ID=$tmp_POEMS_ID  ";	
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$POH_POS_NO = trim($data2[POEMS_NO]);		
				$POH_POS_NO_NAME = trim($data2[POEMS_NO_NAME]);			
				$EP_CODE = trim($data2[EP_CODE]);										   
			} elseif (trim($tmp_POT_ID)) {						// ตำแหน่งลูกจ้างชั่วคราว
				$cmd = "  select POT_NO, POT_NO_NAME, ORG_ID, ORG_ID_1, ORG_ID_2, TP_CODE 
							   from PER_POS_TEMP where POT_ID=$tmp_POT_ID  ";	
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$POH_POS_NO = trim($data2[POT_NO]);
				$POH_POS_NO_NAME = trim($data2[POT_NO_NAME]);
				$TP_CODE = trim($data2[TP_CODE]);				   
			}
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
			$CT_CODE = trim($data2[CT_CODE]);
			$PV_CODE = trim($data2[PV_CODE]);
			$AP_CODE = trim($data2[AP_CODE]);
			$cmd = "  select ORG_ID_REF from PER_ORG where ORG_ID=$ORG_ID_2 and OL_CODE='02'";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_ID_1 = trim($data2[ORG_ID_REF]);

			// update status of PER_PERSONAL 
			$cmd = " 	update PER_PERSONAL set MOV_CODE='$tmp_MOV_CODE', PER_SALARY=$tmp_CMD_SALARY,  
						UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE'
					where PER_ID=$tmp_PER_ID ";
			$db_dpis1->send_cmd($cmd);
//$db_dpis1->show_error();	
//echo "<br>";

			// update into PER_POSITION 
			$cmd = "	update PER_POSITION set POS_SALARY=$tmp_CMD_SALARY, 
						UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE'
					where POS_ID=$tmp_POS_ID ";
			$db_dpis1->send_cmd($cmd);
//$db_dpis1->show_error();	
//echo "<br>";	
				
//			update per_educate ตัด edu_type ที่เป็น 2 ออก
			$TMP_EDU_SEQ = "";
			$NEW_EDU_TYPE = "";
			$cmd = " select EDU_SEQ, EDU_TYPE from PER_EDUCATE where PER_ID=$tmp_PER_ID ";
			$db_dpis1->send_cmd($cmd);
			while ($data = $db_dpis1->get_array()) {
				if (strpos($data[EDU_TYPE], "||2||")!==false) {
					$TMP_EDU_SEQ = $data[EDU_SEQ];
					$NEW_EDU_TYPE = str_replace("||2||", "", $data[EDU_TYPE]);
					break;
				}
			}
			if ($TMP_EDU_SEQ) {
				$cmd = " update PER_EDUCATE set EDU_TYPE='$NEW_EDU_TYPE', 
								UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE'
								where PER_ID=$tmp_PER_ID and EDU_SEQ=$TMP_EDU_SEQ ";
				$db_dpis1->send_cmd($cmd);
			}
//			แล้ว update per_educate ที่เลือก en_code จาก per_comdtl เพิ่ม edu_type ||2||
			$TMP_EDU_SEQ = "";
			$NEW_EDU_TYPE = "";
			$cmd = " select * from PER_EDUCATE where PER_ID=$tmp_PER_ID and EN_CODE='$tmp_EN_CODE' ";
			$db_dpis1->send_cmd($cmd);
			$data = $db_dpis1->get_array();
			$TMP_EDU_SEQ = $data[EDU_SEQ];
			$NEW_EDU_TYPE=trim($data[EDU_TYPE])."||2||";
			if ($TMP_EDU_SEQ) {
				$cmd = " update PER_EDUCATE set EDU_TYPE='$NEW_EDU_TYPE', 
								UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE'
								where PER_ID=$tmp_PER_ID and EDU_SEQ=$TMP_EDU_SEQ ";
				$db_dpis1->send_cmd($cmd);
			}

			// update and insert into PER_SALARYHIS 
			$cmd = " select SAH_ID, SAH_SALARY, SAH_EFFECTIVEDATE from PER_SALARYHIS 
								where PER_ID=$tmp_PER_ID order by SAH_EFFECTIVEDATE desc, SAH_SALARY desc, SAH_DOCNO desc ";
			$db_dpis1->send_cmd($cmd);
			$data1 = $db_dpis1->get_array();
			$tmp_SAH_ID = trim($data1[SAH_ID]);
			$tmp_SAH_OLD_SALARY = $data1[SAH_SALARY] + 0;
			$tmp_SAH_EFFECTIVEDATE = trim($data1[SAH_EFFECTIVEDATE]);
			$tmp_SAH_ENDDATE = $tmp_CMD_DATE - 1;
			if (substr($before_cmd_date,0,10) < substr($tmp_SAH_EFFECTIVEDATE,0,10)) $before_cmd_date = $tmp_SAH_EFFECTIVEDATE;
			$cmd = " 	update PER_SALARYHIS set SAH_ENDDATE='$before_cmd_date' where SAH_ID=$tmp_SAH_ID";
			$db_dpis1->send_cmd($cmd);	
//$db_dpis1->show_error();							
//echo "<br>";

			$cmd = " update PER_SALARYHIS set SAH_LAST_SALARY='N' where PER_ID=$tmp_PER_ID";
			$db_dpis1->send_cmd($cmd);	

			$cmd = " select max(SAH_ID) as max_id from PER_SALARYHIS ";
			$db_dpis1->send_cmd($cmd);
			$data = $db_dpis1->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$SAH_ID = $data[max_id] + 1; 			 
			$cmd = "	insert into PER_SALARYHIS 
						(SAH_ID, PER_ID, SAH_EFFECTIVEDATE, MOV_CODE, SAH_SALARY, SAH_DOCNO, 
						SAH_DOCDATE, SAH_ENDDATE, SAH_POSITION, SAH_ORG, SAH_POS_NO, 
						SAH_PAY_NO, SAH_SEQ_NO, SAH_REMARK, LEVEL_NO, SAH_LAST_SALARY, SAH_CMD_SEQ, 
						SAH_OLD_SALARY, UPDATE_USER, UPDATE_DATE, PER_CARDNO, SAH_POS_NO_NAME) 
						values 
						($SAH_ID, $tmp_PER_ID, '$tmp_CMD_DATE', '$tmp_MOV_CODE', $tmp_CMD_SALARY, '$COM_NO',  
						'$tmp_COM_DATE', '', '$tmp_PL_NAME_WORK', '$tmp_ORG_NAME_WORK', '$POH_POS_NO', '$POH_POS_NO', 
							1, '$tmp_CMD_NOTE1', '$tmp_LEVEL_NO', 'Y', $tmp_CMD_SEQ, $tmp_SAH_OLD_SALARY, $SESS_USERID, '$UPDATE_DATE', '$tmp_PER_CARDNO', '$POH_POS_NO_NAME') ";
			$db_dpis1->send_cmd($cmd);
//$db_dpis1->show_error();	
//echo "<br>";		
		}	// 		while ($data = $db_dpis->get_array())		
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูลเงินเดือน และเพิ่มประวัติการรับเงินเดือนเมื่อยืนยันข้อมูลบัญชีแนบท้ายคำสั่งปรับอัตราเงินเดือนใหมให้ได้รับเงินเดือนตามวุฒิ [$COM_ID : $PER_ID : $SAH_ID]");

?>