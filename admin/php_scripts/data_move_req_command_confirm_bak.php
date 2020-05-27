<?
		// ให้ insert ข้อมูลจาก per_comdtl ไป per_personal และ per_positionhis
		$cmd = "  	select 	PER_ID, CMD_DATE, POS_ID, POEM_ID, POEMS_ID, LEVEL_NO, CMD_SALARY, 
									CMD_SPSALARY, MOV_CODE, PL_CODE_ASSIGN, PN_CODE_ASSIGN, 
									EP_CODE_ASSIGN, CMD_NOTE1 
						from		PER_COMDTL 
						where	COM_ID=$COM_ID";
		$db_dpis->send_cmd($cmd);
//echo "$cmd<br>";		
//$db_dpis->show_error();
		while ($data = $db_dpis->get_array()) {
			$tmp_PER_ID = trim($data[PER_ID]);
			$tmp_CMD_DATE = trim($data[CMD_DATE]);
			$tmp_POS_ID = (trim($data[POS_ID]))? trim($data[POS_ID]) : "NULL";
			$tmp_POEM_ID = (trim($data[POEM_ID]))? trim($data[POEM_ID]) : "NULL";
			$tmp_POEMS_ID = (trim($data[POEMS_ID]))? trim($data[POEMS_ID]) : "NULL";
			$tmp_LEVEL_NO = trim($data[LEVEL_NO]);
			$tmp_CMD_SALARY = trim($data[CMD_SALARY]);
			$tmp_CMD_SPSALARY = trim($data[CMD_SPSALARY]);
			$tmp_MOV_CODE = trim($data[MOV_CODE]);
			$tmp_PL_CODE_ASSIGN = trim($data[PL_CODE_ASSIGN]);
			$tmp_PN_CODE_ASSIGN = trim($data[PN_CODE_ASSIGN]);
			$tmp_EP_CODE_ASSIGN = trim($data[EP_CODE_ASSIGN]);		
			$tmp_CMD_NOTE1 = (trim($data[CMD_NOTE1]))? str_replace("'", "&rsquo;", trim($data[CMD_NOTE1])) : "&rsquo;";
			
			$tmp_date = explode("-", $tmp_CMD_DATE);
			// 86400 วินาที = 1 วัน
			$before_cmd_date = (mktime(0, 0, 0, $tmp_date[1], substr($tmp_date[2],0,2), $tmp_date[0]) - 86400);
			$before_cmd_date = date("Y-m-d", $before_cmd_date);
			
			$PM_CODE = $PL_CODE = $LEVEL_NO = $PN_CODE = $EP_CODE = $PT_CODE = $POH_ASS_ORG = "";			
			$cmd = " select ORG_NAME from PER_PERSONAL a, PER_ORG_ASS b where PER_ID=$tmp_PER_ID and a.ORG_ID=b.ORG_ID ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$POH_ASS_ORG = $data2[ORG_NAME];
			
			if (trim($tmp_POS_ID)) {									// ตำแหน่งช้าราชการ
				$cmd = "  select POS_NO, ORG_ID, ORG_ID_1, ORG_ID_2, PM_CODE, PL_CODE, PT_CODE 
							   from PER_POSITION where POS_ID=$tmp_POS_ID  ";
				$db_dpis2->send_cmd($cmd);		
				$data2 = $db_dpis2->get_array();
				$POH_POS_NO = trim($data2[POS_NO]);
				$PM_CODE = trim($data2[PM_CODE]);
				$PL_CODE = trim($data2[PL_CODE]);
				$PT_CODE = trim($data2[PT_CODE]);
			} elseif (trim($tmp_POEM_ID)) {						// ตำแหน่งลูกจ้างประจำ
				$cmd = "  select POEM_NO, ORG_ID, ORG_ID_1, ORG_ID_2, PN_CODE 
							   from PER_POS_EMP where POS_ID=$tmp_POEM_ID  ";				
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$POH_POS_NO = trim($data2[POEM_NO]);				
				$PN_CODE = trim($data2[PN_CODE]);	
			} elseif (trim($tmp_POEMS_ID)) {						// ตำแหน่งพนักงานราชการ
				$cmd = "  select POEMS_NO, ORG_ID, ORG_ID_1, ORG_ID_2, EP_CODE 
							   from PER_POS_EMPSER where POS_ID=$tmp_POEMS_ID  ";	
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$POH_POS_NO = trim($data2[POEMS_NO]);				
				$EP_CODE = trim($data2[EP_CODE]);										   
			}
			$ORG_ID_3 = trim($data2[ORG_ID]);	
			if (trim($data2[ORG_ID_1])) $ORG_ID_45[] = trim($data2[ORG_ID_1]);
			if (trim($data2[ORG_ID_2])) $ORG_ID_45[] = trim($data2[ORG_ID_2]);
			$search_org_id = implode(", ", $ORG_ID_45);
			$cmd = "  select ORG_ID, ORG_NAME from PER_ORG where ORG_ID in ($search_org_id)";
			$db_dpis2->send_cmd($cmd);
			while ($data2 = $db_dpis2->get_array()) {
				$ORG_NAME_4 = ($data2[ORG_ID] == $ORG_ID_45[0])? "$data2[ORG_NAME]" : "";
				$ORG_NAME_5 = ($data2[ORG_ID] == $ORG_ID_45[1])? "$data2[ORG_NAME]" : "";				
			} 			
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
			// ===== หาชื่อของกระทรวง กรม กรอง
			$ORG_NAME_1 = $ORG_NAME_2 = $ORG_NAME_3 = "";
			$cmd = " select ORG_ID, ORG_NAME FROM PER_ORG where ORG_ID in ( $ORG_ID_1, $ORG_ID_2, $ORG_ID_3 ) ";
			$db_dpis2->send_cmd($cmd);
			while ($data2 = $db_dpis2->get_array()) {
				$ORG_NAME_1 = ($data2[ORG_ID] == $ORG_ID_1)? trim($data2[ORG_NAME]) : $ORG_NAME_1;
				$ORG_NAME_2 = ($data2[ORG_ID] == $ORG_ID_2)? trim($data2[ORG_NAME]) : $ORG_NAME_2;
				$ORG_NAME_3 = ($data2[ORG_ID] == $ORG_ID_3)? trim($data2[ORG_NAME]) : $ORG_NAME_3;
			}
			
			// update status of PER_PERSONAL 
			$cmd = " 	update PER_PERSONAL set  POS_ID=$tmp_POS_ID, POEM_ID=$tmp_POEM_ID, 
										POEMS_ID=$tmp_POEMS_ID, LEVEL_NO='$tmp_LEVEL_NO', MOV_CODE='$tmp_MOV_CODE', 
										PER_STATUS=1, PER_SALARY=$tmp_CMD_SALARY, PER_SPSALARY=$tmp_CMD_SPSALARY, 
										UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE'
							where PER_ID=$tmp_PER_ID ";
			$db_dpis1->send_cmd($cmd);	
//$db_dpis1->show_error();	
//echo "<br>";
			// update status of PER_POSITION 
			$cmd = " update PER_POSITION set 
								POS_SALARY=$tmp_CMD_SALARY, POS_CHANGE_DATE='$tmp_CMD_DATE', 
								UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE'								
						  where POS_ID=$tmp_POS_ID ";
			$db_dpis1->send_cmd($cmd);	
//$db_dpis1->show_error();				
//echo "<br>";
			// update status of PER_POS_EMP
			$cmd = " update PER_POS_EMP set 
								ORG_ID=$ORG_ID_1, ORG_ID_1=$ORG_ID_2, ORG_ID_2=$ORG_ID_3,
								PN_CODE='$PN_CODE'
						  where POEM_ID=$tmp_POEM_ID ";
			$db_dpis1->send_cmd($cmd);							  
			// update status of PER_POS_EMPSER 
			$cmd = " update PER_POS_EMP set 
								ORG_ID=$ORG_ID_1, ORG_ID_1=$ORG_ID_2, ORG_ID_2=$ORG_ID_3,
								EP_CODE='$EP_CODE'
						  where POEMS_ID=$tmp_POEMS_ID ";
			$db_dpis1->send_cmd($cmd);	
			
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
						  	($POH_ID, $tmp_PER_ID, '$tmp_CMD_DATE', '$tmp_MOV_CODE', '$COM_NO', '$COM_DATE', '', 
						   	'$POH_POS_NO', '$PM_CODE', '$tmp_LEVEL_NO', '$PL_CODE', '$PN_CODE', '$EP_CODE', '$PT_CODE', 
							'$CT_CODE', '$PV_CODE', '$AP_CODE', '2', $ORG_ID_1, $ORG_ID_2, $ORG_ID_3, 
							'$ORG_NAME_4', '$ORG_NAME_5', '$POH_ASS_ORG', 
						   	$tmp_CMD_SALARY, $tmp_CMD_SPSALARY, '$tmp_CMD_NOTE1', 
							'$ORG_NAME_1', '$ORG_NAME_2', '$ORG_NAME_3', 
							$SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis1->send_cmd($cmd);
//echo "<br>";
//$db_dpis1->show_error();

			// update and insert into PER_SALARYHIS 
			$cmd = " select SAH_ID from PER_SALARYHIS where PER_ID=$tmp_PER_ID order by PER_ID, SAH_EFFECTIVEDATE desc ";
			$db_dpis1->send_cmd($cmd);
			$data1 = $db_dpis1->get_array();
			$tmp_SAH_ID = trim($data1[SAH_ID]);
			$tmp_SAH_ENDDATE = $tmp_CMD_DATE - 1;
			$cmd = " update PER_SALARYHIS set SAH_ENDDATE='$before_cmd_date' where SAH_ID=$tmp_SAH_ID";
			$db_dpis1->send_cmd($cmd);	
//$db_dpis1->show_error();							
//echo "<br>";

			$cmd = " select max(SAH_ID) as max_id from PER_SALARYHIS ";
			$db_dpis1->send_cmd($cmd);
			$data = $db_dpis1->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$SAH_ID = $data[max_id] + 1; 			 
			$cmd = "	insert into PER_SALARYHIS 
							(SAH_ID, PER_ID, SAH_EFFECTIVEDATE, MOV_CODE, SAH_SALARY, SAH_DOCNO, 
							SAH_DOCDATE, SAH_ENDDATE, UPDATE_USER, UPDATE_DATE) 
							values 
							($SAH_ID, $tmp_PER_ID, '$tmp_CMD_DATE', '$tmp_MOV_CODE', $tmp_CMD_SALARY, '$COM_NO',  
							'$COM_DATE', '', $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis1->send_cmd($cmd);
//$db_dpis1->show_error();	
//echo "<br>";			
		}	// end while 		

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูลการดำรงตำแหน่ง และเพิ่มประวัติการดำรงตำแหน่งเมื่อยืนยันข้อมูลบัญชีแนบท้ายคำสั่งย้าย [$COM_ID : $PER_ID : $POH_ID : $SAH_ID]");
?>