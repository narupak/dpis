<?
	$debug = 1;	
// ให้ insert ข้อมูลจาก per_comdtl ไป per_personal และ per_positionhis
		$cmd = "  	select 	PER_ID, CMD_DATE, CMD_POSITION, CMD_LEVEL, CMD_ORG1, CMD_ORG2, CMD_ORG3, 
						CMD_ORG4, CMD_ORG5, CMD_ORG6, CMD_ORG7, CMD_ORG8, PL_CODE, POS_ID, POEM_ID, POEMS_ID, POT_ID, LEVEL_NO, CMD_SALARY, 
						CMD_SPSALARY, MOV_CODE, PL_CODE_ASSIGN, PN_CODE_ASSIGN, EP_CODE_ASSIGN, TP_CODE_ASSIGN, CMD_NOTE1, CMD_NOTE2, PL_NAME_WORK, ORG_NAME_WORK, CMD_SEQ, 
						ES_CODE, CMD_POS_NO_NAME, CMD_POS_NO, CMD_ORG_TRANSFER, PM_CODE 
						from		PER_COMDTL 
						where	COM_ID=$COM_ID ";
		$db_dpis->send_cmd($cmd);
if($debug == 1){
echo "ดึงข้อมูลจาก แฟ้ม <pre> $cmd<br>";	
}
	
//$db_dpis->show_error();
		while ($data = $db_dpis->get_array()) {
			$tmp_PER_ID = trim($data[PER_ID]);
			$tmp_CMD_DATE = trim($data[CMD_DATE]);
			$tmp_CMD_POS_NO_NAME = trim($data[CMD_POS_NO_NAME]);
			$tmp_CMD_POS_NO = trim($data[CMD_POS_NO]);
			$tmp_CMD_POSITION = trim($data[CMD_POSITION]);
			$tmp_CMD_LEVEL = trim($data[CMD_LEVEL]);
			if($DPISDB=="mysql"){
				$tmp_org = explode("|", trim($data[CMD_ORG1]));
			}else{
				$tmp_org = explode("\|", trim($data[CMD_ORG1]));
			}
			$tmp_CMD_ORG1 = trim($tmp_org[0]);
			$ORG_ID_1 = (trim($tmp_org[1]))? trim($tmp_org[1]) : "NULL"; 
			if($DPISDB=="mysql"){
				$tmp_org = explode("|", trim($data[CMD_ORG2]));
			}else{
				$tmp_org = explode("\|", trim($data[CMD_ORG2]));
			}
			$tmp_CMD_ORG2 = trim($tmp_org[0]);
			$ORG_ID_2 = (trim($tmp_org[1]))? trim($tmp_org[1]) : "NULL"; 
			if($DPISDB=="mysql"){
				$tmp_org = explode("|", trim($data[CMD_ORG3]));
			}else{
				$tmp_org = explode("\|", trim($data[CMD_ORG3]));
			}
			$tmp_CMD_ORG3 = trim($tmp_org[0]);
			$ORG_ID_3 = (trim($tmp_org[1]))? trim($tmp_org[1]) : "NULL"; 
			if($DPISDB=="mysql"){
				$tmp_org = explode("|", trim($data[CMD_ORG4]));
			}else{
				$tmp_org = explode("\|", trim($data[CMD_ORG4]));
			}
			$tmp_CMD_ORG4 = trim($tmp_org[0]);
			$ORG_ID_4 = (trim($tmp_org[1]))? trim($tmp_org[1]) : "NULL"; 
			if($DPISDB=="mysql"){
				$tmp_org = explode("|", trim($data[CMD_ORG5]));
			}else{
				$tmp_org = explode("\|", trim($data[CMD_ORG5]));
			}
			$tmp_CMD_ORG5 = trim($tmp_org[0]);
			$ORG_ID_5 = (trim($tmp_org[1]))? trim($tmp_org[1]) : "NULL"; 
			if($DPISDB=="mysql"){
				$tmp_org = explode("|", trim($data[CMD_ORG6]));
			}else{
				$tmp_org = explode("\|", trim($data[CMD_ORG6]));
			}
			$tmp_CMD_ORG6 = trim($tmp_org[0]);
			$ORG_ID_6 = (trim($tmp_org[1]))? trim($tmp_org[1]) : "NULL"; 
			if($DPISDB=="mysql"){
				$tmp_org = explode("|", trim($data[CMD_ORG7]));
			}else{
				$tmp_org = explode("\|", trim($data[CMD_ORG7]));
			}
			$tmp_CMD_ORG7 = trim($tmp_org[0]);
			$ORG_ID_7 = (trim($tmp_org[1]))? trim($tmp_org[1]) : "NULL"; 
			if($DPISDB=="mysql"){
				$tmp_org = explode("|", trim($data[CMD_ORG8]));
			}else{
				$tmp_org = explode("\|", trim($data[CMD_ORG8]));
			}
			$tmp_CMD_ORG8 = trim($tmp_org[0]);
			$ORG_ID_8 = (trim($tmp_org[1]))? trim($tmp_org[1]) : "NULL"; 
			$tmp_PM_CODE = (trim($data[PM_CODE]))? "'".trim($data[PM_CODE])."'" : "NULL";
			$tmp_PL_CODE = (trim($data[PL_CODE]))? "'".trim($data[PL_CODE])."'" : "NULL";
			$tmp_POS_ID = (trim($data[POS_ID]))? trim($data[POS_ID]) : "NULL";
            $tmp_POEM_ID = (trim($data[POEM_ID]))? trim($data[POEM_ID]) : "NULL";
            $tmp_POEMS_ID = (trim($data[POEMS_ID]))? trim($data[POEMS_ID]) : "NULL";
            $tmp_POT_ID = (trim($data[POT_ID]))? trim($data[POT_ID]) : "NULL";
            
			$tmp_LEVEL_NO = trim($data[LEVEL_NO]);
			$tmp_CMD_SALARY = trim($data[CMD_SALARY]);
			$tmp_CMD_SPSALARY = trim($data[CMD_SPSALARY]);
			$tmp_MOV_CODE = trim($data[MOV_CODE]);
			$tmp_PL_CODE_ASSIGN = trim($data[PL_CODE_ASSIGN]);
            $tmp_PN_CODE_ASSIGN = trim($data[PN_CODE_ASSIGN]);
            $tmp_EP_CODE_ASSIGN = trim($data[EP_CODE_ASSIGN]);
            $tmp_TP_CODE_ASSIGN = trim($data[TP_CODE_ASSIGN]);
			$tmp_CMD_NOTE1 = (trim($data[CMD_NOTE1]))? str_replace("'", "&rsquo;", trim($data[CMD_NOTE1])) : "";
			$tmp_CMD_NOTE2 = (trim($data[CMD_NOTE2]))? str_replace("'", "&rsquo;", trim($data[CMD_NOTE2])) : "";
			$tmp_PL_NAME_WORK = trim($data[PL_NAME_WORK]);		
			$tmp_ORG_NAME_WORK = trim($data[ORG_NAME_WORK]);		
			$tmp_CMD_SEQ = trim($data[CMD_SEQ]);
			$tmp_ES_CODE = trim($data[ES_CODE]);
			$tmp_CMD_ORG_TRANSFER = trim($data[CMD_ORG_TRANSFER]);		
			
			$tmp_date = explode("-", $tmp_CMD_DATE);
			// 86400 วินาที = 1 วัน
			$before_cmd_date = (mktime(0, 0, 0, $tmp_date[1], substr($tmp_date[2],0,2), $tmp_date[0]) - 86400);
			$before_cmd_date = date("Y-m-d", $before_cmd_date);
			
			$PM_CODE = $LEVEL_NO = $PT_CODE = $POH_ASS_ORG = "";
			$PL_CODE = $PN_CODE = $EP_CODE = "NULL";			

			$cmd = " select LEVEL_SEQ_NO from PER_LEVEL where LEVEL_NO = '$tmp_CMD_LEVEL' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$tmp_CMD_LEVEL_SEQ = $data2[LEVEL_SEQ_NO];

			$cmd = " select LEVEL_NAME, LEVEL_SEQ_NO, POSITION_LEVEL from PER_LEVEL where LEVEL_NO = '$tmp_LEVEL_NO' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$tmp_LEVEL_NO_SEQ = $data2[LEVEL_SEQ_NO];
			$LEVEL_NAME = trim($data2[LEVEL_NAME]);
			$POSITION_LEVEL = $data2[POSITION_LEVEL];
			if (!$POSITION_LEVEL) $POSITION_LEVEL = $LEVEL_NAME;

			if($tmp_CMD_POS_NO_NAME) {	
				$cmd = "  select PM_CODE, POS_NO_NAME, POS_NO, PL_CODE, PT_CODE from PER_POSITION 
								where POS_NO_NAME='$tmp_CMD_POS_NO_NAME' and POS_NO='$tmp_CMD_POS_NO' "; }
			else { 
				$cmd = "  select PM_CODE, POS_NO_NAME, POS_NO, PL_CODE, PT_CODE from PER_POSITION 
								where  POS_NO='$tmp_CMD_POS_NO' ";  }
			$db_dpis2->send_cmd($cmd);	
			$data2 = $db_dpis2->get_array();
			$POS_NO = trim($data2[POS_NO]);
			$POH_POS_NO_NAME = trim($data2[POS_NO_NAME]);
			$POH_POS_NO = trim($data2[POS_NO]); 
			$PM_CODE = (trim($data2[PM_CODE]))? "'".trim($data2[PM_CODE])."'" : "NULL";
			$PL_CODE = "'".trim($data2[PL_CODE])."'";

			$cmd = " select PM_NAME from PER_MGT where PM_CODE = $PM_CODE ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PM_NAME = $data2[PM_NAME];

			$cmd = " select PL_NAME from PER_LINE where PL_CODE = $PL_CODE ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PL_NAME = $data2[PL_NAME];

			$cmd = " select PT_NAME from PER_TYPE where PT_CODE = $PT_CODE ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PT_NAME = $data2[PT_NAME];

			$PL_NAME_WORK = (trim($PM_NAME)?"$PM_NAME (":"") . (trim($PL_NAME)?($PL_NAME . $POSITION_LEVEL . (($PT_NAME != "ทั่วไป" && $tmp_LEVEL_NO >= 6)?"$PT_NAME":"")):"") . (trim($PM_NAME)?")":"");

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
			 			
			if ($ORG_ID_3) {
				$cmd = "  select ORG_ID_REF, CT_CODE, PV_CODE, AP_CODE from PER_ORG where ORG_ID=$ORG_ID_3 and OL_CODE='03'";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$ORG_ID_2 = (trim($data2[ORG_ID_REF]))? trim($data2[ORG_ID_REF]) : "NULL";			
				$CT_CODE = (trim($data2[CT_CODE]))? "'".trim($data2[CT_CODE])."'" : "140";
				$PV_CODE = (trim($data2[PV_CODE]))? "'".trim($data2[PV_CODE])."'" : "NULL";
				$AP_CODE = (trim($data2[AP_CODE]))? "'".trim($data2[AP_CODE])."'" : "NULL";
			}
			$cmd = "  select ORG_ID_REF, CT_CODE, PV_CODE, AP_CODE from PER_ORG where ORG_ID=$ORG_ID_2 and OL_CODE='02'";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_ID_1 = trim($data2[ORG_ID_REF]);
			if (!$CT_CODE) $CT_CODE = (trim($data2[CT_CODE]))? "'".trim($data2[CT_CODE])."'" : "140";
			if (!$PV_CODE) $PV_CODE = (trim($data2[PV_CODE]))? "'".trim($data2[PV_CODE])."'" : "NULL";
			if (!$AP_CODE) $AP_CODE = (trim($data2[AP_CODE]))? "'".trim($data2[AP_CODE])."'" : "NULL";
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
			
			if (!$tmp_CMD_ORG_TRANSFER && $ORG_NAME_1 && $ORG_NAME_2) {
				$tmp_CMD_ORG_TRANSFER = $ORG_NAME_3 . " " . $ORG_NAME_2 . " " . $ORG_NAME_1; 
			}

			// update status of PER_PERSONAL 
			$cmd = " 	update PER_PERSONAL set  
                                ES_CODE='$tmp_ES_CODE', 
                                MOV_CODE='$tmp_MOV_CODE', 
								PL_NAME_WORK='$tmp_PL_NAME_WORK', 
                                ORG_NAME_WORK='$tmp_ORG_NAME_WORK', 
								DEPARTMENT_ID=$ORG_ID_2, 
                                UPDATE_USER=$SESS_USERID, 
                                UPDATE_DATE='$UPDATE_DATE'
					where PER_ID=$tmp_PER_ID ";
			$db_dpis1->send_cmd($cmd);	
			//$db_dpis1->show_error();	
            if($debug == 1){
                echo  "อัปเดทที่คน<pre>".$cmd;
            }
			

			if (is_null($tmp_POS_ID)==false && $tmp_POS_ID!="NULL"){ 									// ตำแหน่งข้าราชการ
				$cmd = " update PER_POSITION set 
								PM_CODE=$tmp_PM_CODE,
								DEPARTMENT_ID=$ORG_ID_2, ORG_ID=$ORG_ID_3, ORG_ID_1=$ORG_ID_4, ORG_ID_2=$ORG_ID_5,
								ORG_ID_3=$ORG_ID_6, ORG_ID_4=$ORG_ID_7, ORG_ID_5=$ORG_ID_8,
								PL_CODE='$tmp_PL_CODE_ASSIGN',
								POS_SALARY=$tmp_CMD_SALARY, POS_CHANGE_DATE='$tmp_CMD_DATE', 
								UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE'								
						  where POS_ID=$tmp_POS_ID ";
				$db_dpis1->send_cmd($cmd);
			}elseif (is_null($tmp_POEM_ID)==false && $tmp_POEM_ID!="NULL"){ 						// ตำแหน่งลูกจ้างประจำ
				$cmd = " update PER_POS_EMP set 
								DEPARTMENT_ID=$ORG_ID_2, ORG_ID=$ORG_ID_3, ORG_ID_1=$ORG_ID_4, ORG_ID_2=$ORG_ID_5,
								ORG_ID_3=$ORG_ID_6, ORG_ID_4=$ORG_ID_7, ORG_ID_5=$ORG_ID_8,
								PN_CODE='$tmp_PN_CODE_ASSIGN'
						  where POEM_ID=$tmp_POEM_ID ";
				$db_dpis1->send_cmd($cmd);
			 if($debug == 1){
               echo "อัปเดทตำแหน่ง <pre>".$cmd; 
            }
				//	อัพเดท PER_POS_NAME 
				$cmd = " update PER_POS_NAME  set LEVEL_NO='$tmp_LEVEL_NO_POS'
								  where 	PN_CODE='$tmp_PN_CODE_ASSIGN' ";
				$db_dpis1->send_cmd($cmd);	
			}elseif (is_null($tmp_POEMS_ID)==false && $tmp_POEMS_ID!="NULL"){ 						// ตำแหน่งพนักงานราชการ
				$cmd = " update PER_POS_EMPSER set 
								DEPARTMENT_ID=$ORG_ID_2, ORG_ID=$ORG_ID_3, ORG_ID_1=$ORG_ID_4, ORG_ID_2=$ORG_ID_5,
								ORG_ID_3=$ORG_ID_6, ORG_ID_4=$ORG_ID_7, ORG_ID_5=$ORG_ID_8,
								EP_CODE ='$tmp_EP_CODE_ASSIGN'
						  where POEMS_ID=$tmp_POEMS_ID ";
				$db_dpis1->send_cmd($cmd);
				
				//	อัพเดท PER_EMPSER_POS_NAME 
				$cmd = " update PER_EMPSER_POS_NAME set LEVEL_NO='$tmp_LEVEL_NO_POS'
								  where 	EP_CODE ='$tmp_EP_CODE_ASSIGN' ";
				$db_dpis1->send_cmd($cmd);	
			}elseif (is_null($tmp_POT_ID)==false && $tmp_POT_ID!="NULL"){ 						// ตำแหน่งลูกจ้างชั่วคราว
				$cmd = " update PER_POS_TEMP set 
								DEPARTMENT_ID=$ORG_ID_2, ORG_ID=$ORG_ID_3, ORG_ID_1=$ORG_ID_4, ORG_ID_2=$ORG_ID_5,
								ORG_ID_3=$ORG_ID_6, ORG_ID_4=$ORG_ID_7, ORG_ID_5=$ORG_ID_8,
								TP_CODE='$tmp_TP_CODE_ASSIGN'
						  where POT_ID=$tmp_POT_ID ";
				$db_dpis1->send_cmd($cmd);
			}
           
//			echo "$cmd <br>";
			
			// update and insert into PER_POSITIONHIS	
			$cmd = " update PER_POSITIONHIS set POH_LAST_POSITION='N' where PER_ID=$tmp_PER_ID ";
			$db_dpis1->send_cmd($cmd);
			$data1 = $db_dpis1->get_array();

			$cmd = " select POH_ID from PER_POSITIONHIS where PER_ID=$tmp_PER_ID order by PER_ID, POH_EFFECTIVEDATE desc, POH_SEQ_NO desc ";
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
			$cmd = " 	insert into PER_POSITIONHIS (POH_ID, PER_ID, POH_EFFECTIVEDATE, MOV_CODE, POH_DOCNO, 
							POH_DOCDATE, POH_ENDDATE, POH_POS_NO_NAME, POH_POS_NO, PM_CODE, LEVEL_NO, PL_CODE, PN_CODE, 
							EP_CODE, PT_CODE, CT_CODE, PV_CODE, AP_CODE, POH_ORGMGT, ORG_ID_1, ORG_ID_2, 
							ORG_ID_3, POH_UNDER_ORG1, POH_UNDER_ORG2, POH_UNDER_ORG3, POH_UNDER_ORG4, 
							POH_UNDER_ORG5, POH_ASS_ORG, POH_SALARY, POH_SALARY_POS, POH_REMARK, POH_REMARK1, POH_REMARK2, 
							POH_ORG1, POH_ORG2, POH_ORG3, POH_PL_NAME, POH_ORG_TRANSFER, POH_ORG, POH_CMD_SEQ,
							POH_LAST_POSITION, POH_ISREAL, ES_CODE, POH_LEVEL_NO, UPDATE_USER, UPDATE_DATE)
							values ($POH_ID, $tmp_PER_ID, '$tmp_CMD_DATE', '$tmp_MOV_CODE', '$COM_NO', '$tmp_COM_DATE', 
							'', '$POH_POS_NO_NAME', '$POH_POS_NO', $tmp_PM_CODE, '$tmp_LEVEL_NO', $PL_CODE, $PN_CODE, 
							$EP_CODE, '$PT_CODE', $CT_CODE, $PV_CODE, $AP_CODE, '2', $ORG_ID_1, $ORG_ID_2, $ORG_ID_3, 
							'$ORG_NAME_4', '$ORG_NAME_5', '$ORG_NAME_6', '$ORG_NAME_7', '$ORG_NAME_8', '$POH_ASS_ORG', 
							$tmp_CMD_SALARY, $tmp_CMD_SPSALARY, '$COM_NOTE', '$tmp_CMD_NOTE1', '$tmp_CMD_NOTE2', 
							'$ORG_NAME_1', '$ORG_NAME_2', '$ORG_NAME_3', '$tmp_PL_NAME_WORK', '$tmp_CMD_ORG_TRANSFER', '$tmp_ORG_NAME_WORK', 
							$tmp_CMD_SEQ, 'Y', 'Y', '$tmp_ES_CODE', '$tmp_CMD_LEVEL', $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis1->send_cmd($cmd);
			//echo "<br>[$tmp_POS_ID - $tmp_POEM_ID	 - $tmp_POEMS_ID] :  $POH_POS_NO : $cmd<br><hr>";
			//$db_dpis1->show_error();
            if($debug == 1){
                echo  "อัปเดทประวัติตำแหน่ง<pre>".$cmd;
            }
		}	// end while 		

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มประวัติการดำรงตำแหน่งเมื่อยืนยันข้อมูลบัญชีแนบท้ายคำสั่งตัดโอนตำแหน่งและอัตราเงินเดือน [$COM_ID : $PER_ID : $POH_ID]");
?>