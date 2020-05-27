<?php 
$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd, $dpisdb_port);
$UPDATE_DATE_SAVE = date("Y-m-d H:i:s");
if($command=="ADDAUDIT"){

    $valPER_CARDNO  = $CardNO;
    $valDEPARTMENT_ID = $search_department_id;
    $valORG_ASS_ID =$search_org_id;
    $valORG_LOWER1 = $search_org_id_1;
    
    if(empty($valORG_LOWER1)){
        $cmdDel = " DELETE FROM TA_PER_AUDIT 
                    WHERE PER_CARDNO='$valPER_CARDNO' 
                        AND DEPARTMENT_ID=$valDEPARTMENT_ID 
                        AND ORG_ASS_ID=$valORG_ASS_ID ";
        $db_dpis1->send_cmd($cmdDel);
    }else{
        $cmdDup = " SELECT PER_CARDNO FROM TA_PER_AUDIT 
                    WHERE PER_CARDNO='$valPER_CARDNO' 
                        AND DEPARTMENT_ID=$valDEPARTMENT_ID 
                        AND ORG_ASS_ID=$valORG_ASS_ID AND ORG_LOWER1 IS NULL";
        $cnt = $db_dpis1->send_cmd($cmdDup);
        if($cnt>0){
            $cmdDel = " DELETE FROM TA_PER_AUDIT 
                    WHERE PER_CARDNO='$valPER_CARDNO' 
                        AND DEPARTMENT_ID=$valDEPARTMENT_ID 
                        AND ORG_ASS_ID=$valORG_ASS_ID ";
            $db_dpis1->send_cmd($cmdDel);
        }
    }
    if(empty($valORG_LOWER1)){
        $valORG_LOWER1 = "NULL";
    }
    
    $cmdInt="INSERT INTO TA_PER_AUDIT (PER_CARDNO,DEPARTMENT_ID,ORG_ASS_ID,ORG_LOWER1,CREATE_USER,CREATE_DATE) 
        VALUES('$valPER_CARDNO',$valDEPARTMENT_ID,$valORG_ASS_ID,$valORG_LOWER1,$SESS_USERID,'$UPDATE_DATE_SAVE')";
    $db_dpis1->send_cmd($cmdInt);
    $command="";
}
if($command=="DELAUDIT"){
    $IDdECode = explode("_",base64_decode($IDEnCode));
    $valPER_CARDNO = $IDdECode[0];
    $valDEPARTMENT_ID = $IDdECode[1];
    $valORG_ASS_ID = $IDdECode[2];
    $valORG_LOWER1 = $IDdECode[3];
    if(empty($valORG_LOWER1)){
        $cmdDel = " DELETE FROM TA_PER_AUDIT WHERE PER_CARDNO='$valPER_CARDNO' AND DEPARTMENT_ID=$valDEPARTMENT_ID AND ORG_ASS_ID=$valORG_ASS_ID ";
    }else{
        $cmdDel = " DELETE FROM TA_PER_AUDIT 
                    WHERE PER_CARDNO='$valPER_CARDNO' 
                        AND DEPARTMENT_ID=$valDEPARTMENT_ID AND ORG_ASS_ID=$valORG_ASS_ID AND ORG_LOWER1=$valORG_LOWER1 ";
    }
    $db_dpis1->send_cmd($cmdDel);
    $command="";
}
?>
