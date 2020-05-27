<?
		// ให้ insert ข้อมูลจาก per_comdtl ไป per_personal และ per_positionhis
		/*เดิม*/
                /*$cmd = "  	select 	PER_ID, CMD_DATE, CMD_DATE2, CMD_POSITION, CMD_LEVEL, CMD_ORG1, CMD_ORG2, CMD_ORG3, 
						CMD_ORG4, CMD_ORG5, CMD_ORG6, CMD_ORG7, CMD_ORG8, PL_CODE, POS_ID, LEVEL_NO, CMD_SALARY, 
						CMD_SPSALARY, MOV_CODE, PL_CODE_ASSIGN, CMD_NOTE1, CMD_NOTE2, 	PL_NAME_WORK, ORG_NAME_WORK, CMD_SEQ, 
						ES_CODE, CMD_POS_NO_NAME, CMD_POS_NO, CMD_ORG_TRANSFER 
						from		PER_COMDTL 
						where	COM_ID=$COM_ID ";*/
                /*Release 5.1.0.6 Begin*/
                    $cmd = " select 	PER_ID, CMD_DATE, CMD_DATE2, CMD_POSITION, CMD_LEVEL, CMD_ORG1, CMD_ORG2, CMD_ORG3, 
                        CMD_ORG4, CMD_ORG5, CMD_ORG6, CMD_ORG7, CMD_ORG8, PL_CODE, POS_ID, LEVEL_NO, CMD_SALARY, 
                        CMD_SPSALARY, MOV_CODE, PL_CODE_ASSIGN, CMD_NOTE1, CMD_NOTE2, 	PL_NAME_WORK, ORG_NAME_WORK, CMD_SEQ, 
                        ES_CODE, CMD_POS_NO_NAME, CMD_POS_NO, CMD_ORG_TRANSFER ,
                        EP_CODE_ASSIGN,PN_CODE_ASSIGN,TP_CODE_ASSIGN
                    from		PER_COMDTL 
                    where	COM_ID=$COM_ID ";
                /*Release 5.1.0.6 End*/
		$db_dpis->send_cmd($cmd);
//echo "<pre>$cmd<br><br><br>";		
//$db_dpis->show_error();
		while ($data = $db_dpis->get_array()) {
			$tmp_PER_ID = trim($data[PER_ID]);
			$tmp_CMD_DATE = trim($data[CMD_DATE]);
			$tmp_CMD_DATE2 = trim($data[CMD_DATE2]);
			$tmp_CMD_POS_NO_NAME = trim($data[CMD_POS_NO_NAME]);
			$tmp_CMD_POS_NO = trim($data[CMD_POS_NO]);
                        
            $tmp_EP_CODE_ASSIGN = trim($data[EP_CODE_ASSIGN]);/*Release 5.1.0.6*/
            $tmp_PN_CODE_ASSIGN = trim($data[PN_CODE_ASSIGN]);/*Release 5.1.0.6*/
            $tmp_TP_CODE_ASSIGN = trim($data[TP_CODE_ASSIGN]);/*Release 5.1.0.6*/
                         
			$tmp_CMD_POSITION = trim($data[CMD_POSITION]);
			$tmp_CMD_LEVEL = trim($data[CMD_LEVEL]);
			if($DPISDB=="mysql"){
				$tmp_org = explode("|", trim($data[CMD_ORG1]));
			}else{
				$tmp_org = explode("\|", trim($data[CMD_ORG1]));
			}
			$tmp_CMD_ORG1 = trim($tmp_org[0]);
			$ORG_ID_1 = trim($tmp_org[1]);
			if($DPISDB=="mysql"){
				$tmp_org = explode("|", trim($data[CMD_ORG2]));
			}else{
				$tmp_org = explode("\|", trim($data[CMD_ORG2]));
			}
			$tmp_CMD_ORG2 = trim($tmp_org[0]);
			$ORG_ID_2 = trim($tmp_org[1]);
			if($DPISDB=="mysql"){
				$tmp_org = explode("|", trim($data[CMD_ORG3]));
			}else{
				$tmp_org = explode("\|", trim($data[CMD_ORG3]));
			}
			$tmp_CMD_ORG3 = trim($tmp_org[0]);
			$ORG_ID_3 = trim($tmp_org[1]);
			if($DPISDB=="mysql"){
				$tmp_org = explode("|", trim($data[CMD_ORG4]));
			}else{
				$tmp_org = explode("\|", trim($data[CMD_ORG4]));
			}
			$tmp_CMD_ORG4 = trim($tmp_org[0]);
			$ORG_ID_4 = trim($tmp_org[1]);
			if($DPISDB=="mysql"){
				$tmp_org = explode("|", trim($data[CMD_ORG5]));
			}else{
				$tmp_org = explode("\|", trim($data[CMD_ORG5]));
			}
			$tmp_CMD_ORG5 = trim($tmp_org[0]);
			$ORG_ID_5 = trim($tmp_org[1]);
			if($DPISDB=="mysql"){
				$tmp_org = explode("|", trim($data[CMD_ORG6]));
			}else{
				$tmp_org = explode("\|", trim($data[CMD_ORG6]));
			}
			$tmp_CMD_ORG6 = trim($tmp_org[0]);
			$ORG_ID_6 = trim($tmp_org[1]);
			if($DPISDB=="mysql"){
				$tmp_org = explode("|", trim($data[CMD_ORG7]));
			}else{
				$tmp_org = explode("\|", trim($data[CMD_ORG7]));
			}
			$tmp_CMD_ORG7 = trim($tmp_org[0]);
			$ORG_ID_7 = trim($tmp_org[1]);
			if($DPISDB=="mysql"){
				$tmp_org = explode("|", trim($data[CMD_ORG8]));
			}else{
				$tmp_org = explode("\|", trim($data[CMD_ORG8]));
			}
			$tmp_CMD_ORG8 = trim($tmp_org[0]);
			$ORG_ID_8 = trim($tmp_org[1]);
			$tmp_PM_CODE = (trim($data[PM_CODE]))? "'".trim($data[PM_CODE])."'" : "NULL";
			$tmp_PL_CODE = (trim($data[PL_CODE]))? "'".trim($data[PL_CODE])."'" : "NULL";
			$tmp_POS_ID = (trim($data[POS_ID]))? trim($data[POS_ID]) : "NULL";
			$tmp_LEVEL_NO = trim($data[LEVEL_NO]);
			$tmp_CMD_SALARY = trim($data[CMD_SALARY]);
			$tmp_CMD_SPSALARY = trim($data[CMD_SPSALARY]);
			$tmp_MOV_CODE = trim($data[MOV_CODE]);
			$tmp_PL_CODE_ASSIGN = trim($data[PL_CODE_ASSIGN]);
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
			$tmp_PM_CODE = (trim($data2[PM_CODE]))? "'".trim($data2[PM_CODE])."'" : "NULL";
			$tmp_PM_NAME = trim($data2[PM_NAME]);	
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
			if ($tmp_MOV_CODE=="11270") $tmp_ES_CODE = "06";
			else $tmp_ES_CODE = "05";

			// update status of PER_PERSONAL 
			$cmd = "update PER_PERSONAL set  ES_CODE='$tmp_ES_CODE', MOV_CODE='$tmp_MOV_CODE', 
								PL_NAME_WORK='$tmp_PL_NAME_WORK', ORG_NAME_WORK='$tmp_ORG_NAME_WORK', 
								UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE'
					where PER_ID=$tmp_PER_ID ";
			$db_dpis1->send_cmd($cmd);	
			//$db_dpis1->show_error();	
			//echo "<br>";

			// update and insert into PER_POSITIONHIS	
			$cmd = " update PER_POSITIONHIS set POH_LAST_POSITION='N' where PER_ID=$tmp_PER_ID ";
			$db_dpis1->send_cmd($cmd);
			//$data1 = $db_dpis1->get_array();

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
                        
                        //echo "COM_PER_TYPE:".$COM_PER_TYPE.",PN_CODE_ASSIGN:".$tmp_PN_CODE_ASSIGN."<br>";
                        /*Release 5.1.0.6 Begin*/
                        if(trim($COM_PER_TYPE)=="1"){
                            $PL_CODE =$PL_CODE; 
                            $PN_CODE ="NULL"; 
                            $EP_CODE ="NULL"; 
                            $TP_CODE ="NULL"; 
                            $PM_CODE = $PM_CODE;
                        }
                        if(trim($COM_PER_TYPE)=="2"){
                            $PL_CODE ="NULL"; 
                            $PN_CODE ="'".$tmp_PN_CODE_ASSIGN."'"; 
                            $EP_CODE ="NULL"; 
                            $TP_CODE ="NULL"; 
                            $PM_CODE ="NULL";
                        }
                        if(trim($COM_PER_TYPE)=="3"){
                            $PL_CODE ="NULL"; 
                            $PN_CODE ="NULL"; 
                            $EP_CODE = "'".$tmp_EP_CODE_ASSIGN."'"; 
                            $TP_CODE ="NULL"; 
                            $PM_CODE ="NULL";
                        }
                        if(trim($COM_PER_TYPE)=="4"){
                            $PL_CODE ="NULL"; 
                            $PN_CODE ="NULL"; 
                            $EP_CODE ="NULL"; 
                            $TP_CODE ="'".$tmp_TP_CODE_ASSIGN."'"; 
                            $PM_CODE ="NULL";
                        } 
/* 1/09/2561 */            
//--- ให้เอาค่า ส่วนราชการที่ไปช่วยราชการ บันทึกลง ที่โครงสร้างตามมอบหมาย ----
            $POH_ASS_MINISTRY  = $ORG_NAME_1;
            $POH_ASS_DEPARTMENT = $ORG_NAME_2;
            $POH_ASS_ORG   = $ORG_NAME_3;
            $POH_ASS_ORG1  = $ORG_NAME_4;
            $POH_ASS_ORG2  = $ORG_NAME_5;
            $POH_ASS_ORG3  = $ORG_NAME_6;
            $POH_ASS_ORG4  = $ORG_NAME_7;
            $POH_ASS_ORG5  = $ORG_NAME_8;
            
//--- เอาตำแหน่ง เดิมบันทึกลง โครงสร้างตามกฏหมาย  ----
    if($tmp_CMD_ORG1){// เอาชื่อหน่วยงานมาหา id ที่โครงสร้างตามกฎหมาย
        $cmd = " select ORG_ID from PER_ORG where trim(ORG_NAME) = trim('$tmp_CMD_ORG1') and trim(OL_CODE)='01' and org_id_ref=1";
			$db_dpis3->send_cmd($cmd);
			$data3 = $db_dpis3->get_array();
			$ORG_ID_1 = $data3[ORG_ID];
        
    }
    if($tmp_CMD_ORG2){
        $cmd = " select ORG_ID from PER_ORG where trim(ORG_NAME) = trim('$tmp_CMD_ORG2') and trim(OL_CODE)='02' 
                and org_id_ref IN
                (SELECT org_id FROM per_org WHERE trim(org_name) = trim('$tmp_CMD_ORG1') and trim(OL_CODE)='01' and org_id_ref=1) ";
			$db_dpis3->send_cmd($cmd);
			$data3 = $db_dpis3->get_array();
			$ORG_ID_2 = $data3[ORG_ID];
        
    }       
    if($tmp_CMD_ORG3){
        $cmd = " select ORG_ID from PER_ORG where trim(ORG_NAME) = trim('$tmp_CMD_ORG3') 
                     and trim(OL_CODE)='03' and org_id_ref IN
                    (SELECT org_id FROM per_org WHERE trim(org_name) = trim('$tmp_CMD_ORG2') and trim(OL_CODE)='02') ";
			$db_dpis3->send_cmd($cmd);
			$data3 = $db_dpis3->get_array();
			$ORG_ID_3 = $data3[ORG_ID];
        
	} 
	//echo "<pre>".$cmd;      
            $ORG_NAME_1 = $tmp_CMD_ORG1;
            $ORG_NAME_2 = $tmp_CMD_ORG2; 
            $ORG_NAME_3 = $tmp_CMD_ORG3;
            $ORG_NAME_4 = $tmp_CMD_ORG4;
            $ORG_NAME_5 = $tmp_CMD_ORG5;
            $ORG_NAME_6 = $tmp_CMD_ORG6;
            $ORG_NAME_7 = $tmp_CMD_ORG7;
            $ORG_NAME_8 = $tmp_CMD_ORG8;    
 //----------------------------------------------------------------------------------------------------------
            
          //  echo $POH_ASS_MINISTRY."| ".$POH_ASS_DEPARTMENT."| ".$POH_ASS_ORG."| ".$POH_ASS_ORG1."| ".$POH_ASS_ORG2."| ".$OH_ASS_ORG3."| ".$POH_ASS_ORG4."| ".$POH_ASS_ORG5;
  /*Release 5.1.0.6 End*/ 
    /*        
			$cmd = " 	insert into PER_POSITIONHIS (POH_ID, PER_ID, POH_EFFECTIVEDATE, MOV_CODE, POH_DOCNO, 
							POH_DOCDATE, POH_ENDDATE, POH_POS_NO_NAME, POH_POS_NO, PM_CODE, LEVEL_NO, PL_CODE, PN_CODE, 
							EP_CODE, PT_CODE, CT_CODE, PV_CODE, AP_CODE, POH_ORGMGT, ORG_ID_1, ORG_ID_2, 
							ORG_ID_3, POH_UNDER_ORG1, POH_UNDER_ORG2, POH_UNDER_ORG3, POH_UNDER_ORG4, 
							POH_UNDER_ORG5, POH_ASS_ORG, POH_SALARY, POH_SALARY_POS, POH_REMARK, POH_REMARK1, POH_REMARK2, 
							POH_ORG1, POH_ORG2, POH_ORG3, POH_PL_NAME, POH_ORG_TRANSFER, POH_ORG, POH_CMD_SEQ,
							POH_LAST_POSITION, POH_ISREAL, ES_CODE, POH_LEVEL_NO, UPDATE_USER, UPDATE_DATE,TP_CODE)
							values ($POH_ID, $tmp_PER_ID, '$tmp_CMD_DATE', '$tmp_MOV_CODE', '$COM_NO', '$tmp_COM_DATE', 
							'$tmp_CMD_DATE2', '$POH_POS_NO_NAME', '$POH_POS_NO', $PM_CODE, '$tmp_LEVEL_NO', $PL_CODE, $PN_CODE, 
							$EP_CODE, '$PT_CODE', $CT_CODE, $PV_CODE, $AP_CODE, '2', $ORG_ID_1, $ORG_ID_2, $ORG_ID_3, 
							'$ORG_NAME_4', '$ORG_NAME_5', '$ORG_NAME_6', '$ORG_NAME_7', '$ORG_NAME_8', '$POH_ASS_ORG', 
							$tmp_CMD_SALARY, $tmp_CMD_SPSALARY, '$COM_NOTE', '$tmp_CMD_NOTE1', '$tmp_CMD_NOTE2', 
							'$ORG_NAME_1', '$ORG_NAME_2', '$ORG_NAME_3', '$tmp_PL_NAME_WORK', '$tmp_CMD_ORG_TRANSFER', '$tmp_ORG_NAME_WORK', 
							$tmp_CMD_SEQ, 'Y', 'Y', '$tmp_ES_CODE', '$tmp_CMD_LEVEL', $SESS_USERID, '$UPDATE_DATE',$TP_CODE) ";
			$db_dpis1->send_cmd($cmd);
            //echo "<pre>".$cmd;
			//echo "<br>[$tmp_POS_ID - $tmp_POEM_ID	 - $tmp_POEMS_ID] :  $POH_POS_NO : $cmd<br><hr>";
			//$db_dpis1->show_error();
		}	// end while 		
    */    

                $cmd = " 	insert into PER_POSITIONHIS (POH_ID, PER_ID, POH_EFFECTIVEDATE, MOV_CODE, POH_DOCNO, 
							POH_DOCDATE, POH_ENDDATE, POH_POS_NO_NAME, POH_POS_NO, PM_CODE, LEVEL_NO, PL_CODE, PN_CODE, 
							EP_CODE, PT_CODE, CT_CODE, PV_CODE, AP_CODE, POH_ORGMGT, ORG_ID_1, ORG_ID_2, 
							ORG_ID_3, POH_UNDER_ORG1, POH_UNDER_ORG2, POH_UNDER_ORG3, POH_UNDER_ORG4, 
							POH_UNDER_ORG5, POH_SALARY, POH_SALARY_POS, POH_REMARK, POH_REMARK1, POH_REMARK2, 
							POH_ORG1, POH_ORG2, POH_ORG3, POH_PL_NAME, POH_ORG_TRANSFER, POH_ORG, POH_CMD_SEQ,
							POH_LAST_POSITION, POH_ISREAL, ES_CODE, POH_LEVEL_NO, UPDATE_USER, UPDATE_DATE,TP_CODE,
                            POH_ASS_MINISTRY, POH_ASS_DEPARTMENT, POH_ASS_ORG, POH_ASS_ORG1, POH_ASS_ORG2, POH_ASS_ORG3, POH_ASS_ORG4, POH_ASS_ORG5 )
							values ($POH_ID, $tmp_PER_ID, '$tmp_CMD_DATE', '$tmp_MOV_CODE', '$COM_NO', '$tmp_COM_DATE', 
							'$tmp_CMD_DATE2', '$POH_POS_NO_NAME', '$POH_POS_NO', $PM_CODE, '$tmp_LEVEL_NO', $PL_CODE, $PN_CODE, 
							$EP_CODE, '$PT_CODE', $CT_CODE, $PV_CODE, $AP_CODE, '2', $ORG_ID_1, $ORG_ID_2, $ORG_ID_3, 
							'$ORG_NAME_4', '$ORG_NAME_5', '$ORG_NAME_6', '$ORG_NAME_7', '$ORG_NAME_8', 
							$tmp_CMD_SALARY, $tmp_CMD_SPSALARY, '$COM_NOTE', '$tmp_CMD_NOTE1', '$tmp_CMD_NOTE2', 
							'$ORG_NAME_1', '$ORG_NAME_2', '$ORG_NAME_3', '$tmp_PL_NAME_WORK', '$tmp_CMD_ORG_TRANSFER', '$tmp_ORG_NAME_WORK', 
							$tmp_CMD_SEQ, 'Y', 'Y', '$tmp_ES_CODE', '$tmp_CMD_LEVEL', $SESS_USERID, '$UPDATE_DATE',$TP_CODE,
                            '$POH_ASS_MINISTRY', '$POH_ASS_DEPARTMENT', '$POH_ASS_ORG', '$POH_ASS_ORG1', '$POH_ASS_ORG2', '$OH_ASS_ORG3', '$POH_ASS_ORG4', '$POH_ASS_ORG5' ) ";
			$db_dpis1->send_cmd($cmd);
           // echo "<pre>".$cmd;
			//echo "<br>[$tmp_POS_ID - $tmp_POEM_ID	 - $tmp_POEMS_ID] :  $POH_POS_NO : $cmd<br><hr>";
			//$db_dpis1->show_error();
		}	// end while 		
                
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูลสถานะการดำรงตำแหน่ง และเพิ่มประวัติการช่วยราชการเมื่อยืนยันข้อมูลบัญชีแนบท้ายคำสั่งช่วยราชการ [$COM_ID : $PER_ID : $POH_ID]");
?>