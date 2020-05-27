<?php

    $db_dpis = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
    $dbDpisUserAudit = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
    $db_dpis_PicDef = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
    $db_dpis_PER_CONTROL = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

    $cmdCreate = " CREATE TABLE TA_PER_AUDIT(
        PER_CARDNO    VARCHAR2(13 BYTE) NOT NULL ENABLE,
        DEPARTMENT_ID NUMBER(10,0) NOT NULL ENABLE,
        ORG_ASS_ID    NUMBER(10,0) NOT NULL ENABLE,
        ORG_LOWER1    NUMBER(10,0),
        ORG_LOWER2    NUMBER(10,0),
        ORG_LOWER3    NUMBER(10,0),
        ORG_LOWER4    NUMBER(10,0),
        ORG_LOWER5    NUMBER(10,0),
        CREATE_USER   NUMBER(11,0),
        CREATE_DATE   VARCHAR2(19 BYTE)
      )";
    $db_dpis->send_cmd($cmdCreate);

    $cmdUserAudit = "SELECT PER_CARDNO,ORG_ID,ORG_ID_1 FROM PER_PERSONAL WHERE PER_AUDIT_FLAG=1";
    $dbDpisUserAudit->send_cmd($cmdUserAudit);
    while($dataUserAudit = $dbDpisUserAudit->get_array()){
        $valPER_CARDNO=$dataUserAudit[PER_CARDNO];
        $valORG_ASS_ID=$dataUserAudit[ORG_ID];
        $valORG_LOWER1=$dataUserAudit[ORG_ID_1];
        if(empty($valORG_LOWER1)){$valORG_LOWER1="NULL";}
        
        $cmdOrgRef = "SELECT DISTINCT ORG_ID_REF FROM PER_ORG WHERE ORG_ID IN($valORG_ASS_ID)";
        $db_dpis->send_cmd($cmdOrgRef);
        $dataOrgRef = $db_dpis->get_array();
        $valDEPARTMENT_ID = $dataOrgRef[ORG_ID_REF];

        $cmd =" INSERT INTO TA_PER_AUDIT(PER_CARDNO,DEPARTMENT_ID,ORG_ASS_ID,ORG_LOWER1,CREATE_USER,CREATE_DATE) 
                VALUES('$valPER_CARDNO',$valDEPARTMENT_ID,$valORG_ASS_ID,$valORG_LOWER1,$SESS_USERID, to_char(SYSDATE,'yyyy-mm-dd'))";
        $db_dpis->send_cmd($cmd);
    }
    
    $cmd = " update PER_CONTROL set CTRL_ALTER = 18 ,UPDATE_DATE=SYSDATE ";
    $db_dpis_PER_CONTROL->send_cmd($cmd);
?>
