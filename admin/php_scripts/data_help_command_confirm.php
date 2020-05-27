<?
		// ให้ insert ข้อมูลจาก per_comdtl ไป per_personal และ per_positionhis
		$cmd = "  	select 	PER_ID, CMD_DATE, POS_ID, POEM_ID, POEMS_ID,CMD_POSITION, 
									CMD_ORG1, CMD_ORG2, CMD_ORG3, CMD_ORG4, CMD_ORG5,  
									LEVEL_NO, CMD_SALARY,	CMD_SPSALARY, MOV_CODE, PL_CODE_ASSIGN, PN_CODE_ASSIGN, 
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
			//##################
			$tmp_data = explode("\|", trim($data[CMD_POSITION]));
			$CMD_POSPOEM_NO = $tmp_data[0];
			$CMD_POSITION = $tmp_data[1];
			$tmp_POS_NO = $tmp_data[2];		//เลขที่ตำแหน่งใหม่
			//==================================================
			$CMD_ORG1 = trim($data[CMD_ORG1]); 
			$CMD_ORG2 = trim($data[CMD_ORG2]); 
			
			$tmp_org3 = explode("\|", trim($data[CMD_ORG3]));
			$CMD_ORG3 = trim($tmp_org3[0]);
			$ORG_ID = (trim($tmp_org3[1]))? trim($tmp_org3[1]) : "NULL"; //--ORG_ID
			
			$tmp_org4= explode("\|", trim($data[CMD_ORG4]));
			$CMD_ORG4 = trim($tmp_org4[0]);
			$ORG_ID_1 = (trim($tmp_org4[1]))? trim($tmp_org4[1]) : "NULL"; //--ORG_ID_1
			
			$tmp_org5 = explode("\|", trim($data[CMD_ORG5]));
			$CMD_ORG5 = trim($tmp_org5[0]);
			$ORG_ID_2 = (trim($tmp_org5[1]))? trim($tmp_org5[1]) : "NULL"; //--ORG_ID_2

			$tmp_LEVEL_NO = trim($data[LEVEL_NO]);
			$tmp_CMD_SALARY = trim($data[CMD_SALARY]);
			$tmp_CMD_SPSALARY = trim($data[CMD_SPSALARY]);
			$tmp_MOV_CODE = trim($data[MOV_CODE]);
			$tmp_PL_CODE_ASSIGN = trim($data[PL_CODE_ASSIGN]);
			$tmp_PN_CODE_ASSIGN = trim($data[PN_CODE_ASSIGN]);
			$tmp_EP_CODE_ASSIGN = trim($data[EP_CODE_ASSIGN]);
			$tmp_CMD_NOTE1 = (trim($data[CMD_NOTE1]))? str_replace("'", "&rsquo;", trim($data[CMD_NOTE1])) : "&rsquo;";

			// update status of PER_PERSONAL 
			$cmd = " 	update PER_PERSONAL set  POS_ID=$tmp_POS_ID, POEM_ID=$tmp_POEM_ID, POEMS_ID=$tmp_POEMS_ID, 
								LEVEL_NO='$tmp_LEVEL_NO', LEVEL_NO_SALARY='$tmp_LEVEL_NO', MOV_CODE='$tmp_MOV_CODE', 
								PER_STATUS=1, UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE'
					where PER_ID=$tmp_PER_ID ";
			// PER_SALARY=$tmp_CMD_SALARY, PER_SPSALARY=$tmp_CMD_SPSALARY, 					
			$db_dpis1->send_cmd($cmd);
//$db_dpis1->show_error();	
//echo "<br>";
			// update status of PER_POSITION 
			$cmd = " update PER_POSITION set 
								POS_NO='$tmp_POS_NO',
								ORG_ID=$ORG_ID, ORG_ID_1=$ORG_ID_1, ORG_ID_2=$ORG_ID_2,
								PL_CODE='$tmp_PL_CODE_ASSIGN',
								POS_SALARY=$tmp_CMD_SALARY, POS_CHANGE_DATE='$tmp_CMD_DATE', 
								UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE'								
						  where POS_ID=$tmp_POS_ID ";
			$db_dpis1->send_cmd($cmd);
//$db_dpis1->show_error();				
//echo "<br>";
			// update status of PER_POS_EMP
			$cmd = " update PER_POS_EMP set 
								POEM_NO='$tmp_POS_NO',
								ORG_ID=$ORG_ID, ORG_ID_1=$ORG_ID_1, ORG_ID_2=$ORG_ID_2,
								PN_CODE='$tmp_PN_CODE_ASSIGN'
						  where POEM_ID=$tmp_POEM_ID ";
			$db_dpis1->send_cmd($cmd);
//$db_dpis1->show_error();	
//echo "<br>";
			// update status of PER_POS_EMPSER 
			$cmd = " update PER_POS_EMPSER set 
								POEMS_NO='$tmp_POS_NO',
								ORG_ID=$ORG_ID, ORG_ID_1=$ORG_ID_1, ORG_ID_2=$ORG_ID_2,
								EP_CODE ='$tmp_EP_CODE_ASSIGN'
						  where POEMS_ID=$tmp_POEMS_ID ";
			$db_dpis1->send_cmd($cmd);
//$db_dpis1->show_error();				
//echo "<br>";
			
			$tmp_date = explode("-", $tmp_CMD_DATE);
			// 86400 วินาที = 1 วัน
			$before_cmd_date = (mktime(0, 0, 0, $tmp_date[1], substr($tmp_date[2],0,2), $tmp_date[0]) - 86400);
			$before_cmd_date = date("Y-m-d", $before_cmd_date);
			
			$PM_CODE = $LEVEL_NO = $PT_CODE = $POH_ASS_ORG = "";
			$PL_CODE = $PN_CODE = $EP_CODE = "NULL";			
			$cmd = " select ORG_NAME from PER_PERSONAL a, PER_ORG_ASS b where PER_ID=$tmp_PER_ID and a.ORG_ID=b.ORG_ID ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$POH_POS_NO = $tmp_POS_NO;
			$POH_ASS_ORG = $data2[ORG_NAME];

			if (is_null($tmp_POS_ID)==false) {									// ตำแหน่งข้าราชการ
				$cmd = "  select POS_NO, ORG_ID, ORG_ID_1, ORG_ID_2, PM_CODE, PL_CODE, PT_CODE 
							   from PER_POSITION where POS_ID=$tmp_POS_ID  ";
				$db_dpis2->send_cmd($cmd);
				//echo $cmd."<hr>";
				$data2 = $db_dpis2->get_array();
				$PM_CODE = trim($data2[PM_CODE]);
				$PM_CODE = (trim($PM_CODE))? trim($PM_CODE) : "NULL"; 
				
				$PL_CODE = "'".trim($data2[PL_CODE])."'";
				$PT_CODE = trim($data2[PT_CODE]);
			} elseif (is_null($tmp_POEM_ID)==false) {						// ตำแหน่งลูกจ้างประจำ
				$cmd = "  select POEM_NO, ORG_ID, ORG_ID_1, ORG_ID_2, PN_CODE 
							   from PER_POS_EMP where POEM_ID=$tmp_POEM_ID  ";	
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$PN_CODE = "'".trim($data2[PN_CODE])."'";
			} elseif (is_null($tmp_POEMS_ID)==false) {						// ตำแหน่งพนักงานราชการ
				$cmd = "  select POEMS_NO, ORG_ID, ORG_ID_1, ORG_ID_2, EP_CODE 
							   from PER_POS_EMPSER where POEMS_ID=$tmp_POEMS_ID  ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$EP_CODE = "'".trim($data2[EP_CODE])."'"; 
			}
			$ORG_ID_3 = $ORG_ID;	 
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
			$ORG_ID_2 = (trim($data2[ORG_ID_REF]))? trim($data2[ORG_ID_REF]) : "NULL";			
			$CT_CODE = (trim($data2[CT_CODE]))? "'".trim($data2[CT_CODE])."'" : "NULL";
			$PV_CODE = (trim($data2[PV_CODE]))? "'".trim($data2[PV_CODE])."'" : "NULL";
			$AP_CODE = (trim($data2[AP_CODE]))? "'".trim($data2[AP_CODE])."'" : "NULL";
			$cmd = "  select ORG_ID_REF from PER_ORG where ORG_ID=$ORG_ID_2 and OL_CODE='02'";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_ID_1 = trim($data2[ORG_ID_REF]);
			// ===== หาชื่อของกระทรวง กรม กอง
			$ORG_ID_1 = (trim($ORG_ID_1))? trim($ORG_ID_1) : 0;
			$ORG_ID_2 = (trim($ORG_ID_2))? trim($ORG_ID_2) : 0;
			$ORG_ID_3 = (trim($ORG_ID_3))? trim($ORG_ID_3) : 0;				
			$ORG_NAME_1 = $ORG_NAME_2 = $ORG_NAME_3 = "";
			$cmd = " select ORG_ID, ORG_NAME FROM PER_ORG where ORG_ID in ( $ORG_ID_1, $ORG_ID_2, $ORG_ID_3 ) ";
			$db_dpis2->send_cmd($cmd);
			while ($data2 = $db_dpis2->get_array()) {
				$ORG_NAME_1 = ($data2[ORG_ID] == $ORG_ID_1)? trim($data2[ORG_NAME]) : $ORG_NAME_1;
				$ORG_NAME_2 = ($data2[ORG_ID] == $ORG_ID_2)? trim($data2[ORG_NAME]) : $ORG_NAME_2;
				$ORG_NAME_3 = ($data2[ORG_ID] == $ORG_ID_3)? trim($data2[ORG_NAME]) : $ORG_NAME_3;
			}
			
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
						   	'$POH_POS_NO', $PM_CODE, '$tmp_LEVEL_NO', $PL_CODE, $PN_CODE, $EP_CODE, '$PT_CODE', 
							$CT_CODE, $PV_CODE, $AP_CODE, '2', $ORG_ID_1, $ORG_ID_2, $ORG_ID_3, 
							'$ORG_NAME_4', '$ORG_NAME_5', '$POH_ASS_ORG', 
						   	$tmp_CMD_SALARY, $tmp_CMD_SPSALARY, '$tmp_CMD_NOTE1', 
							'$ORG_NAME_1', '$ORG_NAME_2', '$ORG_NAME_3', 
							$SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis1->send_cmd($cmd);
//echo "<br>";
//$db_dpis1->show_error();

		}	// end while

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูลการดำรงตำแหน่ง และเพิ่มประวัติการดำรงตำแหน่งเมื่อยืนยันข้อมูลบัญชีแนบท้ายคำสั่งย้าย [$COM_ID : $PER_ID : $POH_ID : $SAH_ID]");
?>