<?
		// ให้ insert ข้อมูลจาก per_comdtl ไป per_personal และ per_positionhis
		$cmd = "  	select 	PER_ID, CMD_DATE, POS_ID, CMD_LEVEL_POS, CMD_SALARY, CMD_SPSALARY, 
									MOV_CODE, PL_CODE_ASSIGN, PN_CODE_ASSIGN, EP_CODE_ASSIGN, 
									CMD_NOTE1, PL_NAME_WORK, ORG_NAME_WORK, CMD_SEQ, ES_CODE 
						from		PER_COMDTL 
						where	COM_ID=$COM_ID";
		$db_dpis->send_cmd($cmd);
//echo "$cmd<br>";		
//$db_dpis->show_error();
		while ($data = $db_dpis->get_array()) {
			$tmp_PER_ID = trim($data[PER_ID]);
			$tmp_CMD_DATE = trim($data[CMD_DATE]);
			$tmp_POS_ID = (trim($data[POS_ID]))? trim($data[POS_ID]) : "NULL";
			$tmp_LEVEL_NO = trim($data[CMD_LEVEL_POS]);
			$tmp_CMD_SALARY = trim($data[CMD_SALARY]);
			$tmp_CMD_SPSALARY = trim($data[CMD_SPSALARY]);
			$tmp_MOV_CODE = trim($data[MOV_CODE]);
			$tmp_PL_CODE_ASSIGN = trim($data[PL_CODE_ASSIGN]);
			$tmp_PN_CODE_ASSIGN = trim($data[PN_CODE_ASSIGN]);
			$tmp_EP_CODE_ASSIGN = trim($data[EP_CODE_ASSIGN]);		
			$tmp_CMD_NOTE1 = (trim($data[CMD_NOTE1]))? str_replace("'", "&rsquo;", trim($data[CMD_NOTE1])) : "";
			$tmp_PL_NAME_WORK = trim($data[PL_NAME_WORK]);		
			$tmp_ORG_NAME_WORK = trim($data[ORG_NAME_WORK]);		
			$tmp_CMD_SEQ = trim($data[CMD_SEQ]);
			$tmp_ES_CODE = trim($data[ES_CODE]);
			$tmp_SM_CODE = "9";
			
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
			
			$POH_POS_NO="";
			//อัพเดทวันที่ตำแหน่งว่าง
			$cmd = " update PER_POSITION set POS_CHANGE_DATE='$tmp_CMD_DATE' where POS_ID=$tmp_POS_ID  ";
			$db_dpis2->send_cmd($cmd);
		
			$cmd = "  select POS_NO, ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5, PM_CODE, PL_CODE, PT_CODE 
						   from PER_POSITION where POS_ID='$tmp_POS_ID'  ";
			$db_dpis2->send_cmd($cmd);		
			$data3 = $db_dpis2->get_array();
			$POH_POS_NO = trim($data3[POS_NO]);
			//$PM_CODE = trim($data3[PM_CODE]);
			$PM_CODE = (trim($data3[PM_CODE]))? "'".trim($data3[PM_CODE])."'" : "NULL";
			
			$PL_CODE = "'".trim($data3[PL_CODE])."'";
			$PT_CODE = trim($data3[PT_CODE]);
			$ORG_ID_3 = trim($data3[ORG_ID]);	
			$ORG_ID_4 = trim($data3[ORG_ID_1]);
			$ORG_ID_5 = trim($data3[ORG_ID_2]);
			$ORG_ID_6 = trim($data3[ORG_ID_3]);	
			$ORG_ID_7 = trim($data3[ORG_ID_4]);
			$ORG_ID_8 = trim($data3[ORG_ID_5]);

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
			 			
			$cmd = "  select ORG_ID_REF, CT_CODE, PV_CODE, AP_CODE from PER_ORG where ORG_ID=$ORG_ID_3 and OL_CODE='03'";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_ID_2 = (trim($data2[ORG_ID_REF]))? trim($data2[ORG_ID_REF]) : "NULL";			
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
			
			// update status of PER_PERSONAL 
			$cmd = " 	update PER_PERSONAL set  PAY_ID=$tmp_POS_ID, MOV_CODE='$tmp_MOV_CODE', PER_DOCNO='$COM_NO', 
								PER_DOCDATE='$tmp_COM_DATE', UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE'
					where PER_ID=$tmp_PER_ID and PER_STATUS=1 ";
			$db_dpis1->send_cmd($cmd);	
//			$db_dpis1->show_error();	
//			echo "<br>";

			$cmd = " update PER_POSITION set 
								POS_SALARY=$tmp_CMD_SALARY, POS_CHANGE_DATE='$tmp_CMD_DATE', 
								UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE'								
						  where POS_ID=$tmp_POS_ID ";
			$db_dpis1->send_cmd($cmd);	
//			$db_dpis1->show_error();				
//			echo "<br>";
			
			// update and insert into PER_SALARYHIS 
			$cmd = " select SAH_ID, SAH_SALARY from PER_SALARYHIS 
							  where PER_ID=$tmp_PER_ID order by PER_ID, SAH_EFFECTIVEDATE desc, SAH_SALARY desc, SAH_DOCNO desc ";
			$db_dpis1->send_cmd($cmd);
			$data1 = $db_dpis1->get_array();
			$tmp_SAH_ID = trim($data1[SAH_ID]);
			$tmp_SAH_OLD_SALARY = $data1[SAH_SALARY] + 0;
			$tmp_SAH_ENDDATE = $tmp_CMD_DATE - 1;
			$cmd = " update PER_SALARYHIS set SAH_ENDDATE='$before_cmd_date' where SAH_ID=$tmp_SAH_ID";
			$db_dpis1->send_cmd($cmd);	

			$cmd = " update PER_SALARYHIS set SAH_LAST_SALARY='N' where PER_ID=$tmp_PER_ID";
			$db_dpis1->send_cmd($cmd);	

			$cmd = " select max(SAH_ID) as max_id from PER_SALARYHIS ";
			$db_dpis1->send_cmd($cmd);
			$data = $db_dpis1->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$SAH_ID = $data[max_id] + 1; 			 
			$cmd = "	insert into PER_SALARYHIS (SAH_ID, PER_ID, SAH_EFFECTIVEDATE, MOV_CODE, SAH_SALARY, 
								SAH_DOCNO, SAH_DOCDATE, SAH_ENDDATE, SAH_POSITION, SAH_ORG, EX_CODE, 
								SAH_POS_NO, SAH_PAY_NO, SAH_SEQ_NO, LEVEL_NO, SAH_LAST_SALARY, SM_CODE, 
								SAH_CMD_SEQ, SAH_OLD_SALARY, UPDATE_USER, UPDATE_DATE, SAH_REMARK) 
								values ($SAH_ID, $tmp_PER_ID, '$tmp_CMD_DATE', '$tmp_MOV_CODE', $tmp_CMD_SALARY, 
								'$COM_NO',  '$tmp_COM_DATE', '', '$tmp_PL_NAME_WORK', '$tmp_ORG_NAME_WORK', '024', 
								'$POH_POS_NO', '$POH_POS_NO', 1, '$tmp_LEVEL_NO', 'Y', '$tmp_SM_CODE', $tmp_CMD_SEQ, 
								$tmp_SAH_OLD_SALARY, $SESS_USERID, '$UPDATE_DATE', '$tmp_CMD_NOTE1') ";
			$db_dpis1->send_cmd($cmd);
//			echo "$cmd<br>";
//			$db_dpis1->show_error();	
		}				

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูลการดำรงตำแหน่ง และเพิ่มประวัติการดำรงตำแหน่งเมื่อยืนยันข้อมูลบัญชีแนบท้ายคำสั่งตัดโอนอัตราเงินเดือนข้าราชการ [$COM_ID : $PER_ID : $POH_ID : $SAH_ID]");
?>