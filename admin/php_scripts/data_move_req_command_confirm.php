<?

    // ให้ insert ข้อมูลจาก per_comdtl ไป per_personal และ per_positionhis
    $cmd = " select PER_ID, CMD_DATE, POS_ID, POEM_ID, POEMS_ID, POT_ID, CMD_LEVEL, LEVEL_NO, CMD_LEVEL_POS,
            CMD_SALARY, CMD_SPSALARY, MOV_CODE, PL_CODE_ASSIGN, PN_CODE_ASSIGN, EP_CODE_ASSIGN, 
            CMD_NOTE1, CMD_NOTE2, PL_NAME_WORK, ORG_NAME_WORK, CMD_SEQ, ES_CODE, CMD_POSITION, CMD_NOW ,
            CMD_ORG_ASS3,CMD_ORG_ASS4,CMD_ORG_ASS5,CMD_ORG_ASS6,CMD_ORG_ASS7,CMD_ORG_ASS8 
            from PER_COMDTL 
            where COM_ID=$COM_ID";
    $db_dpis->send_cmd($cmd);
    //echo "$cmd<br>";		
    //$db_dpis->show_error();
    //echo "<hr>";
   
    while ($data = $db_dpis->get_array()) {
        //cdgs
		$tmp_CMD_POSITION=trim($data[CMD_POSITION]);
        $tmp_PER_ID = trim($data[PER_ID]);
        $tmp_CMD_DATE = trim($data[CMD_DATE]);
        $tmp_POS_ID = (trim($data[POS_ID]))? trim($data[POS_ID]) : "NULL";
        $tmp_POEM_ID = (trim($data[POEM_ID]))? trim($data[POEM_ID]) : "NULL";
        $tmp_POEMS_ID = (trim($data[POEMS_ID]))? trim($data[POEMS_ID]) : "NULL";
        $tmp_POT_ID   = (trim($data[POT_ID]))? trim($data[POT_ID]) : "NULL";
        $tmp_CMD_LEVEL = trim($data[CMD_LEVEL]); // ระดับบุคคล		-> PER_PERSONAL
        $tmp_LEVEL_NO = trim($data[LEVEL_NO]); // ระดับตำแหน่ง (ตำแหน่งที่แต่งตั้ง)		-> PER_POSITION
        $tmp_LEVEL_NO_POS = trim($data[CMD_LEVEL_POS]);// ระดับตำแหน่ง (เดิม)		-> PER_POSITION
        $tmp_CMD_SALARY = trim($data[CMD_SALARY]);
        $tmp_CMD_SPSALARY = trim($data[CMD_SPSALARY]);
        $tmp_MOV_CODE = trim($data[MOV_CODE]);
        $tmp_PL_CODE_ASSIGN = trim($data[PL_CODE_ASSIGN]);
        $tmp_PN_CODE_ASSIGN = trim($data[PN_CODE_ASSIGN]);
        $tmp_EP_CODE_ASSIGN = trim($data[EP_CODE_ASSIGN]);		
        $tmp_CMD_NOTE1 = (trim($data[CMD_NOTE1]))? str_replace("'", "&rsquo;", trim($data[CMD_NOTE1])) : "";
        $tmp_CMD_NOTE2 = (trim($data[CMD_NOTE2]))? str_replace("'", "&rsquo;", trim($data[CMD_NOTE2])) : "";
        $tmp_PL_NAME_WORK = trim($data[PL_NAME_WORK]);		
        $tmp_ORG_NAME_WORK = trim($data[ORG_NAME_WORK]);		
        $tmp_CMD_SEQ = trim($data[CMD_SEQ]);
        $tmp_ES_CODE = trim($data[ES_CODE]);
        $tmp_SM_CODE = "11";
        $tmp_CMD_NOW = trim($data[CMD_NOW]);
                        
        /*Release 5.1.0.8 Begin*/
        $ORG_ID_ASS3='NULL';$ORG_NAME_ASS3='NULL';
        $ORG_ID_ASS4='NULL';$ORG_NAME_ASS4='NULL';
        $ORG_ID_ASS5='NULL';$ORG_NAME_ASS5='NULL';
        $ORG_ID_ASS6='NULL';$ORG_NAME_ASS6='NULL';
        $ORG_ID_ASS7='NULL';$ORG_NAME_ASS7='NULL';
        $ORG_ID_ASS8='NULL';$ORG_NAME_ASS8='NULL';
        if(!empty($data[CMD_ORG_ASS3])){
            $ARR_ORG_ASS3=explode("|",$data[CMD_ORG_ASS3]);
            $ORG_ID_ASS3=$ARR_ORG_ASS3[0];
            $ORG_NAME_ASS3 = "'".$ARR_ORG_ASS3[1]."'";
        }
        if(!empty($data[CMD_ORG_ASS4])){
            $ARR_ORG_ASS4=explode("|",$data[CMD_ORG_ASS4]);
            $ORG_ID_ASS4=$ARR_ORG_ASS4[0];
            $ORG_NAME_ASS4 = "'".$ARR_ORG_ASS4[1]."'";
        }
        if(!empty($data[CMD_ORG_ASS5])){
            $ARR_ORG_ASS5=explode("|",$data[CMD_ORG_ASS5]);
            $ORG_ID_ASS5=$ARR_ORG_ASS5[0];
            $ORG_NAME_ASS5 = "'".$ARR_ORG_ASS5[1]."'";
        }
        if(!empty($data[CMD_ORG_ASS6])){
            $ARR_ORG_ASS6=explode("|",$data[CMD_ORG_ASS6]);
            $ORG_ID_ASS6=$ARR_ORG_ASS6[0];
            $ORG_NAME_ASS6 = "'".$ARR_ORG_ASS6[1]."'";
        }
        if(!empty($data[CMD_ORG_ASS7])){
            $ARR_ORG_ASS7=explode("|",$data[CMD_ORG_ASS7]);
            $ORG_ID_ASS7=$ARR_ORG_ASS7[0];
            $ORG_NAME_ASS7 = "'".$ARR_ORG_ASS7[1]."'";
        }
        if(!empty($data[CMD_ORG_ASS8])){
            $ARR_ORG_ASS8=explode("|",$data[CMD_ORG_ASS8]);
            $ORG_ID_ASS8=$ARR_ORG_ASS8[0];
            $ORG_NAME_ASS8 = "'".$ARR_ORG_ASS8[1]."'";
        }
        /*Release 5.1.0.8 End*/
                                     
        if ($tmp_CMD_NOW=="Y") 
            $tmp_CMD_DATE = $tmp_COM_DATE;
			
        $tmp_date = explode("-", $tmp_CMD_DATE);
        // 86400 วินาที = 1 วัน
        $before_cmd_date = (mktime(0, 0, 0, $tmp_date[1], substr($tmp_date[2],0,2), $tmp_date[0]) - 86400);
        $before_cmd_date = date("Y-m-d", $before_cmd_date);

        // select PER_SALARY to compare with CMD_SALARY before insert into PER_SALARYHIS
        $PER_SALARY = "";
        $cmd = " select PER_SALARY from PER_PERSONAL where PER_ID=$tmp_PER_ID ";
        $db_dpis2->send_cmd($cmd);
        $data2 = $db_dpis2->get_array();
        $PER_SALARY = $data2[PER_SALARY] + 0;

        $PM_CODE = $LEVEL_NO = $PT_CODE = $POH_ASS_ORG = "";
        $PL_CODE = $PN_CODE = $EP_CODE = "NULL";			
        $PM_CODE = (trim($PM_CODE))? "'$PM_CODE'" : "NULL";

        $cmd = " select ORG_NAME from PER_PERSONAL a, PER_ORG_ASS b where PER_ID=$tmp_PER_ID and a.ORG_ID=b.ORG_ID ";
        $db_dpis2->send_cmd($cmd);
        $data2 = $db_dpis2->get_array();
        $POH_ASS_ORG = $data2[ORG_NAME];

        $POH_POS_NO="";
        if (trim($tmp_POS_ID) && $tmp_POS_ID!="NULL") {// ตำแหน่งข้าราชการ
            //อัพเดทวันที่ตำแหน่งว่าง
            $cmd = " update PER_POSITION set POS_CHANGE_DATE='$tmp_CMD_DATE' where POS_ID=$tmp_POS_ID  ";
            $db_dpis2->send_cmd($cmd);
            $cmd = "  select POS_NO, POS_NO_NAME, DEPARTMENT_ID, ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5, PM_CODE, PL_CODE, PT_CODE 
                                       from PER_POSITION where POS_ID=$tmp_POS_ID  "; 
            $db_dpis2->send_cmd($cmd);		
            $data2 = $db_dpis2->get_array();
            $POH_POS_NO = trim($data2[POS_NO]);
            $POH_POS_NO_NAME = trim($data2[POS_NO_NAME]);
            //$PM_CODE = trim($data2[PM_CODE]);
            $PM_CODE = (trim($data2[PM_CODE]))? "'".trim($data2[PM_CODE])."'" : "NULL";

            $PL_CODE = "'".trim($data2[PL_CODE])."'";
            $PT_CODE = trim($data2[PT_CODE]);
            //cdgs
			$strPosIDDpis=$tmp_POS_ID;
			//cdgs	
            
        } else if (trim($tmp_POEM_ID) && $tmp_POEM_ID!="NULL") {// ตำแหน่งลูกจ้างประจำ
            $cmd = "  select POEM_NO, POEM_NO_NAME, DEPARTMENT_ID, ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5, PN_CODE 
                                       from PER_POS_EMP where POEM_ID=$tmp_POEM_ID  ";				
            $db_dpis2->send_cmd($cmd);
            //$db_dpis2->show_error();
            //echo "<hr>";
            $data2 = $db_dpis2->get_array();
            $POH_POS_NO = trim($data2[POEM_NO]);
            $POH_POS_NO_NAME = trim($data2[POEM_NO_NAME]);				
            $PN_CODE = "'".trim($data2[PN_CODE])."'";
            //cdgs
			$strPosIDDpis=$tmp_POEM_ID;
			//cdgs
        } else if (trim($tmp_POEMS_ID) && $tmp_POEMS_ID!="NULL") {// ตำแหน่งพนักงานราชการ
            $cmd = "  select POEMS_NO, POEMS_NO_NAME, DEPARTMENT_ID, ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5, EP_CODE 
                                       from PER_POS_EMPSER where POEMS_ID='$tmp_POEMS_ID'  ";	
            $db_dpis2->send_cmd($cmd);
            //$db_dpis2->show_error();
            //echo "<hr>";
            $data2 = $db_dpis2->get_array();
            $POH_POS_NO = trim($data2[POEMS_NO]);
            $POH_POS_NO_NAME = trim($data2[POEMS_NO_NAME]);				
            $EP_CODE = "'".trim($data2[EP_CODE])."'";
            //cdgs
			$strPosIDDpis=$tmp_POEMS_ID;
			//cdgs 
        }else if (trim($tmp_POT_ID) && $tmp_POT_ID!="NULL") {// ตำแหน่งลูกจ้างชั่วคราว
            $cmd = "  select POT_NO, POT_NO_NAME, DEPARTMENT_ID, ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5, TP_CODE 
                                       from PER_POS_TEMP where POT_ID='$tmp_POT_ID'  ";	
            $db_dpis2->send_cmd($cmd);
            //$db_dpis2->show_error();
           // echo "<pre>".$cmd;
            $data2 = $db_dpis2->get_array();
            $POH_POS_NO = trim($data2[POT_NO]);
            $POH_POS_NO_NAME = trim($data2[POT_NO_NAME]);				
            $TP_CODE = "'".trim($data2[TP_CODE])."'"; 
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

        $cmd = "  select CT_CODE, PV_CODE, AP_CODE from PER_ORG where ORG_ID=$ORG_ID_3 ";
        $db_dpis2->send_cmd($cmd);
        //$db_dpis2->show_error();
        //	echo "<hr>";
        $data2 = $db_dpis2->get_array();
        $CT_CODE = (trim($data2[CT_CODE]))? "'".trim($data2[CT_CODE])."'" : "140";
        $PV_CODE = (trim($data2[PV_CODE]))? "'".trim($data2[PV_CODE])."'" : "NULL";
        $AP_CODE = (trim($data2[AP_CODE]))? "'".trim($data2[AP_CODE])."'" : "NULL";

        $cmd = "  select ORG_ID_REF from PER_ORG where ORG_ID=$ORG_ID_2 ";
        $db_dpis2->send_cmd($cmd);
        $data2 = $db_dpis2->get_array();
        $ORG_ID_1 = trim($data2[ORG_ID_REF]);
        /*		$ORG_ID_1 = (trim($ORG_ID_1))? trim($ORG_ID_1) : "NULL";
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
        if(empty($ORG_ID_ASS3)){$ORG_ID_ASS3='NULL';}
        if(empty($ORG_ID_ASS4)){$ORG_ID_ASS4='NULL';}
        if(empty($ORG_ID_ASS5)){$ORG_ID_ASS5='NULL';}
        if(empty($ORG_ID_ASS6)){$ORG_ID_ASS6='NULL';}
        if(empty($ORG_ID_ASS7)){$ORG_ID_ASS7='NULL';}
        if(empty($ORG_ID_ASS8)){$ORG_ID_ASS8='NULL';}
       if($cfm_salary_now==1){ 
        $cmd = " update PER_PERSONAL set  
                POS_ID=$tmp_POS_ID, 
                POEM_ID=$tmp_POEM_ID, 
                POEMS_ID=$tmp_POEMS_ID,
                POT_ID = $tmp_POT_ID,
                LEVEL_NO='$tmp_LEVEL_NO', 
                LEVEL_NO_SALARY='$tmp_LEVEL_NO', 
                PER_SALARY=$tmp_CMD_SALARY, 
                MOV_CODE='$tmp_MOV_CODE', 
                ES_CODE='$tmp_ES_CODE', 
                PER_DOCNO='$COM_NO', 
                PER_DOCDATE='$tmp_COM_DATE', 
                PER_STATUS=1, 
                UPDATE_USER=$SESS_USERID, 
                UPDATE_DATE='$UPDATE_DATE',
                ORG_ID=$ORG_ID_ASS3,    
                ORG_ID_1=$ORG_ID_ASS4,
                ORG_ID_2=$ORG_ID_ASS5,
                ORG_ID_3=$ORG_ID_ASS6,
                ORG_ID_4=$ORG_ID_ASS7,
                ORG_ID_5=$ORG_ID_ASS8
            where PER_ID=$tmp_PER_ID ";
            // PER_SALARY=$tmp_CMD_SALARY, PER_SPSALARY=$tmp_CMD_SPSALARY, 
           // echo '<pre>'.$cmd;
            $db_dpis1->send_cmd($cmd);	
            //$db_dpis1->show_error();	
            //echo "<br>";
       }
        if ($SESS_DEPARTMENT_NAME!="กรมการปกครอง") {
                $cmd = " 	update PER_PERSONAL set  PAY_ID=$tmp_POS_ID where PER_ID=$tmp_PER_ID ";
                $db_dpis1->send_cmd($cmd);	
        }
        if ($MFA_FLAG==1) {
                $cmd = " 	update PER_PERSONAL set  DEPARTMENT_ID=$ORG_ID_2 where PER_ID=$tmp_PER_ID ";
                $db_dpis1->send_cmd($cmd);	
        }

        // update ตำแหน่ง 																//////////////// ตำแหน่ง และ ปว.การดำรงตำแหน่ง	
        //29/03/2555 LEVEL_NO='$tmp_LEVEL_NO'
        if($cfm_salary_now==1){
        if (trim($tmp_POS_ID) && $tmp_POS_ID!="NULL") {// ตำแหน่งข้าราชการ
            $cmd = " update PER_POSITION set 
                        POS_SALARY=$tmp_CMD_SALARY, 
                        POS_CHANGE_DATE='$tmp_CMD_DATE', 
                        LEVEL_NO='$tmp_LEVEL_NO',
                        UPDATE_USER=$SESS_USERID, 
                        UPDATE_DATE='$UPDATE_DATE' ,
                        POS_DOC_NO='$COM_NO'
                    where POS_ID=$tmp_POS_ID ";
            $db_dpis1->send_cmd($cmd);
            //$db_dpis1->show_error();				
            //echo "$cmd <br>"; 
       
            if ($BKK_FLAG==1) {
                $cmd = " select max(CHANGEID) as max_id from DPIS_ORGCHANGE ";
                $db_dpis35->send_cmd($cmd);
                $data = $db_dpis35->get_array();
                $data = array_change_key_case($data, CASE_LOWER);		
                $CHANGEID = $data[max_id] + 1;

                $cmd = " insert into DPIS_ORGCHANGE (CHANGEID, PERID, MOVEPOSITION, MOVEORG, ISUSER, CHANGETYPE, CHANGEDATE, PXUPDATE, USERNAME, FULLNAME, MD5)
                        values ($CHANGEID, $tmp_PER_ID, NULL, $ORG_ID_3, 1, 'MOVE', to_date('$UPDATE_DATE','yyyy-mm-dd hh24:mi:ss'), 0, '$username', '$user_name', '$passwd') ";
                $db_dpis35->send_cmd($cmd);
                //$db_dpis35->show_error();
            }
        } else if (trim($tmp_POEM_ID) && $tmp_POEM_ID!="NULL") {// ตำแหน่งลูกจ้างประจำ
            $cmd = " update PER_POS_EMP set 
                        ORG_ID=$ORG_ID, 
                        ORG_ID_1=$ORG_ID_1, 
                        ORG_ID_2=$ORG_ID_2,
                        ORG_ID_3=$ORG_ID_3, 
                        ORG_ID_4=$ORG_ID_4, 
                        ORG_ID_2=$ORG_ID_5,
                        PN_CODE='$PN_CODE'
                  where POEM_ID=$tmp_POEM_ID ";
            $db_dpis1->send_cmd($cmd);							  

            //	อัพเดท PER_POS_NAME 
            $cmd = " update PER_POS_NAME  set LEVEL_NO='$tmp_LEVEL_NO'
                    where  PN_CODE='$PN_CODE' ";
            $db_dpis1->send_cmd($cmd);	
        } else if (trim($tmp_POEMS_ID) && $tmp_POEMS_ID!="NULL") {// ตำแหน่งพนักงานราชการ
            $cmd = " update PER_POS_EMPSER set 
                        ORG_ID=$ORG_ID, 
                        ORG_ID_1=$ORG_ID_1, 
                        ORG_ID_2=$ORG_ID_2,
                        ORG_ID_3=$ORG_ID_3, 
                        ORG_ID_4=$ORG_ID_4, 
                        ORG_ID_2=$ORG_ID_5,
                        EP_CODE='$EP_CODE'
                    where POEMS_ID=$tmp_POEMS_ID ";
            $db_dpis1->send_cmd($cmd);	
            //	อัพเดท PER_EMPSER_POS_NAME 
            $cmd = " update PER_EMPSER_POS_NAME set LEVEL_NO='$tmp_LEVEL_NO'
                    where EP_CODE='$EP_CODE' ";
            $db_dpis1->send_cmd($cmd);	
            
        } else if (trim($tmp_POT_ID) && $tmp_POT_ID!="NULL") {// ตำแหน่งลูกจ้างชั่วคราว
            $cmd = " update PER_POS_TEMP set 
                        ORG_ID=$ORG_ID, 
                        ORG_ID_1=$ORG_ID_1, 
                        ORG_ID_2=$ORG_ID_2,
                        ORG_ID_3=$ORG_ID_3, 
                        ORG_ID_4=$ORG_ID_4, 
                        ORG_ID_2=$ORG_ID_5,
                        TP_CODE='$TP_CODE'
                    where POT_ID=$tmp_POT_ID ";
            $db_dpis1->send_cmd($cmd);	
        }
    } //end cfm_salary_now		
        // update and insert into PER_POSITIONHIS	
       
        $cmd = " update PER_POSITIONHIS set POH_LAST_POSITION='N' where PER_ID=$tmp_PER_ID ";
        $db_dpis1->send_cmd($cmd);
        $data1 = $db_dpis1->get_array();
    
        //หา record ล่าสุดของวันที่มีผล
        $tmp_END_DATE = "";
        $cmd = " select POH_ID,POH_EFFECTIVEDATE  from PER_POSITIONHIS where PER_ID=$tmp_PER_ID order by PER_ID, POH_EFFECTIVEDATE desc, POH_SEQ_NO desc ";
        $db_dpis1->send_cmd($cmd);
        $data1 = $db_dpis1->get_array();
        $tmp_POH_ID = trim($data1[POH_ID]);
        $tmp_POH_EFFECTIVEDATE = trim($data1[POH_EFFECTIVEDATE]);
        if($tmp_POH_EFFECTIVEDATE==$tmp_CMD_DATE){//ถ้าวันที่มีผลที่เลือกมานี้ = วันที่มีผลของเรคคอร์ดล่าสุดก่อนหน้านี้ให้  POH_ENDDATE=วันที่มีผลเลย   //21/12/2011
            $tmp_END_DATE=$tmp_CMD_DATE;
        }else{
            $tmp_END_DATE=$before_cmd_date;
        }
       
        $cmd = " update PER_POSITIONHIS set POH_ENDDATE='$tmp_END_DATE' where POH_ID=$tmp_POH_ID";
        $db_dpis1->send_cmd($cmd);	
        //$db_dpis1->show_error();				
        //echo "<br>";
            
        $PT_CODE = (trim($PT_CODE))? "'$PT_CODE'" : "NULL";
        $cmd = " select max(POH_ID) as max_id from PER_POSITIONHIS ";
        $db_dpis1->send_cmd($cmd);
        $data = $db_dpis1->get_array();
        $data = array_change_key_case($data, CASE_LOWER);
        $POH_ID = $data[max_id] + 1; 

        
        $ORG_ID_1 = (trim($ORG_ID_1))? trim($ORG_ID_1) : "NULL";
        $ORG_ID_2 = (trim($ORG_ID_2))? trim($ORG_ID_2) : "NULL";
        $ORG_ID_3 = (trim($ORG_ID_3))? trim($ORG_ID_3) : "NULL";
        
        $cmd = "insert into PER_POSITIONHIS (POH_ID, PER_ID, POH_EFFECTIVEDATE, MOV_CODE, 
                    POH_DOCNO, POH_DOCDATE, POH_ENDDATE, POH_POS_NO, POH_POS_NO_NAME, PM_CODE, LEVEL_NO, 
                    PL_CODE, PN_CODE, EP_CODE, PT_CODE, CT_CODE, PV_CODE, AP_CODE, POH_ORGMGT,
                    ORG_ID_1, ORG_ID_2, ORG_ID_3, POH_UNDER_ORG1, POH_UNDER_ORG2, POH_UNDER_ORG3, 
                    POH_UNDER_ORG4, POH_UNDER_ORG5, POH_ASS_ORG, POH_SALARY, POH_SALARY_POS, 
                    POH_REMARK, POH_REMARK1, POH_REMARK2, POH_ORG1, POH_ORG2, POH_ORG3, POH_PL_NAME, POH_ORG, POH_CMD_SEQ,
                    POH_LAST_POSITION, POH_ISREAL, ES_CODE, POH_LEVEL_NO, UPDATE_USER, UPDATE_DATE,
                    POH_ASS_ORG1,POH_ASS_ORG2,POH_ASS_ORG3,POH_ASS_ORG4,POH_ASS_ORG5)
                values ($POH_ID, $tmp_PER_ID, '$tmp_CMD_DATE', '$tmp_MOV_CODE', '$COM_NO', '$tmp_COM_DATE', '', 
                    '$POH_POS_NO', '$POH_POS_NO_NAME', $PM_CODE, '$tmp_LEVEL_NO', $PL_CODE, $PN_CODE, $EP_CODE, $PT_CODE, 
                    $CT_CODE, $PV_CODE, $AP_CODE, '2',$ORG_ID_1, $ORG_ID_2, $ORG_ID_3, '$ORG_NAME_4', 
                    '$ORG_NAME_5', '$ORG_NAME_6', '$ORG_NAME_7', '$ORG_NAME_8', $ORG_NAME_ASS3, 
                    $tmp_CMD_SALARY, $tmp_CMD_SPSALARY, '$COM_NOTE', '$tmp_CMD_NOTE1', '$tmp_CMD_NOTE2', '$ORG_NAME_1', 
                    '$ORG_NAME_2', '$ORG_NAME_3', '$tmp_PL_NAME_WORK', '$tmp_ORG_NAME_WORK', 
                    $tmp_CMD_SEQ, 'Y', 'Y', '$tmp_ES_CODE', '$tmp_LEVEL_NO', $SESS_USERID, '$UPDATE_DATE',
                    $ORG_NAME_ASS4,$ORG_NAME_ASS5,$ORG_NAME_ASS6,$ORG_NAME_ASS7,$ORG_NAME_ASS8    ) ";
        $db_dpis1->send_cmd($cmd);
        //echo "<br><pre> $cmd"."<br><br>";
        //echo "<br>[$tmp_POS_ID - $tmp_POEM_ID	 - $tmp_POEMS_ID] :  $POH_POS_NO : $cmd<br><hr>";
        //$db_dpis1->show_error();
        /*cdgs*/
		$aa=f_promote_person(f_get_personID($tmp_PER_ID),$strPosIDDpis,$tmp_CMD_POSITION,$tmp_LEVEL_NO,$tmp_CMD_LEVEL,$tmp_CMD_DATE,f_get_movement_code($tmp_MOV_CODE),$tmp_CMD_DATE);
		/*cdgs*/

        // ถ้าเงินเดือนมีการเปลี่ยนแปลง ให้สร้าง record ใน PER_SALARYHIS ด้วย
        if($PER_SALARY != $tmp_CMD_SALARY){
            // update and insert into PER_SALARYHIS 
            if($cfm_salary_now == 1){//ติกเป็นเงินดือนล่าสุด
            $cmd = " update PER_SALARYHIS set SAH_LAST_SALARY='N' where PER_ID=$tmp_PER_ID ";
            $db_dpis1->send_cmd($cmd);
            $data1 = $db_dpis1->get_array();
           
                $tmp_sah_last_salary = 'Y';
            }else{
                $tmp_sah_last_salary = 'N';
            }

            $cmd = " select SAH_ID, SAH_SALARY from PER_SALARYHIS 
                                              where PER_ID=$tmp_PER_ID order by PER_ID, SAH_EFFECTIVEDATE desc, SAH_SALARY desc, SAH_DOCNO desc ";
            $db_dpis1->send_cmd($cmd);
            $data1 = $db_dpis1->get_array();
            $tmp_SAH_ID = trim($data1[SAH_ID]);
            $tmp_SAH_OLD_SALARY = $data1[SAH_SALARY] + 0;
            $tmp_SAH_ENDDATE = $tmp_CMD_DATE - 1;
            $cmd = " update PER_SALARYHIS set SAH_ENDDATE='$before_cmd_date' where SAH_ID=$tmp_SAH_ID";
            $db_dpis1->send_cmd($cmd);	
            //$db_dpis1->show_error();							
            //echo "<br>";
    /*				
            if(in_array($tmp_MOV_CODE, array('10410', '10420', '10430', '10440', '10450'))){
                    $tmp_SALARY_MOV_CODE = "21360";
                    // === การเลื่อนระดับ ให้ตั้งค่าการเคลื่อนไหวเงินเดือนเป็น 21360 (เลื่อนขั้นเงินเดือนเนื่องจากเลื่อนตำแหน่ง)
            }else{
                    $tmp_SALARY_MOV_CODE = $tmp_MOV_CODE;
            } // end if
    */
            $tmp_SALARY_MOV_CODE = $tmp_MOV_CODE;

            $cmd = " select max(SAH_ID) as max_id from PER_SALARYHIS ";
            $db_dpis1->send_cmd($cmd);
            $data = $db_dpis1->get_array();
            $data = array_change_key_case($data, CASE_LOWER);
            $SAH_ID = $data[max_id] + 1; 			 
            $cmd = "	insert into PER_SALARYHIS (SAH_ID, PER_ID, SAH_EFFECTIVEDATE, MOV_CODE, SAH_SALARY, 
                        SAH_DOCNO, SAH_DOCDATE, SAH_ENDDATE, SAH_POSITION, SAH_ORG, EX_CODE, SAH_POS_NO, 
                        SAH_PAY_NO, SAH_SEQ_NO, LEVEL_NO, SAH_LAST_SALARY, SM_CODE, SAH_CMD_SEQ, 
                        SAH_OLD_SALARY, UPDATE_USER, UPDATE_DATE, SAH_POS_NO_NAME) 
                    values ($SAH_ID, $tmp_PER_ID, '$tmp_CMD_DATE', '$tmp_SALARY_MOV_CODE', $tmp_CMD_SALARY, 
                        '$COM_NO', '$tmp_COM_DATE', '', '$tmp_PL_NAME_WORK', '$tmp_ORG_NAME_WORK', '024', '$POH_POS_NO', 
                        '$POH_POS_NO', 1, '$tmp_LEVEL_NO', '$tmp_sah_last_salary', '$tmp_SM_CODE', $tmp_CMD_SEQ, $tmp_SAH_OLD_SALARY, 
                        $SESS_USERID, '$UPDATE_DATE','$POH_POS_NO_NAME') ";
            $db_dpis1->send_cmd($cmd);
            //$db_dpis1->show_error();	
           // echo "<br>$cmd<br>==========<br>";
        } // end if -- insert PER_SALARYHIS
    }	// end while 		

    $cmd = " select CMD_POS_NO_NAME, CMD_POS_NO, POS_ID, CMD_DATE	from PER_COMDTL where COM_ID=$COM_ID";
    $db_dpis->send_cmd($cmd);
    while ($data = $db_dpis->get_array()) {
        $tmp_CMD_POS_NO_NAME = trim($data[CMD_POS_NO_NAME]);
        $tmp_CMD_POS_NO = trim($data[CMD_POS_NO]);
        $tmp_POS_ID = (trim($data[POS_ID]))? trim($data[POS_ID]) : "NULL";
        $tmp_CMD_DATE = trim($data[CMD_DATE]);
        if($cfm_salary_now == 1){//ติกเป็นเงินดือนล่าสุด
        if (trim($tmp_POS_ID) && $tmp_POS_ID!="NULL") {									
            $cmd = " select PER_ID from PER_PERSONAL a, PER_POSITION b
                                              where a.POS_ID=b.POS_ID and PER_TYPE=1 and PER_STATUS=1 and POS_NO_NAME='$tmp_CMD_POS_NO_NAME' and POS_NO='$tmp_CMD_POS_NO' ";
            $db_dpis1->send_cmd($cmd);
            $data1 = $db_dpis1->get_array();
            $tmp_PER_ID = trim($data1[PER_ID]);
           
            if (!$tmp_PER_ID) {
                if ($BKK_FLAG==1)
                    $cmd = " update PER_POSITION set POS_VACANT_DATE='$tmp_CMD_DATE'
                            where POS_NO_NAME='$tmp_CMD_POS_NO_NAME' and POS_NO='$tmp_CMD_POS_NO' ";
                else
                        $cmd = " update PER_POSITION set POS_VACANT_DATE='$tmp_CMD_DATE' where POS_NO='$tmp_CMD_POS_NO' ";
                
                $db_dpis1->send_cmd($cmd);			
                //$db_dpis1->show_error();				
            } // end if 
        }// end if
        } // end if cfm_salary_now
    }	// end while 		

    insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูลการดำรงตำแหน่ง และเพิ่มประวัติการดำรงตำแหน่งเมื่อยืนยันข้อมูลบัญชีแนบท้ายคำสั่งย้าย [$COM_ID : $PER_ID : $POH_ID : $SAH_ID]");
?>