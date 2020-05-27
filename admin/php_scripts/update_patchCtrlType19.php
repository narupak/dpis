<?php
    $db_dpis = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
    $dbDpisUserAudit = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
    $db_dpis_PicDef = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
    $db_dpis_PER_CONTROL = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
    
    /*งานลา*/
    $cmdChk ="  SELECT COUNT(COLUMN_NAME) AS CNT
                FROM USER_TAB_COLS
                WHERE  TABLE_NAME = 'PER_ABSENT'
                  AND UPPER(COLUMN_NAME) IN('OABS_STARTDATE','OABS_STARTPERIOD','OABS_ENDDATE',
                                             'OABS_ENDPERIOD','OAPPROVE_PER_ID','OAPPROVE_DATE',
                                             'OAUDIT_PER_ID','OAUDIT_DATE','OREVIEW1_PER_ID',
                                             'OREVIEW1_DATE','OREVIEW2_PER_ID','OREVIEW2_DATE') ";
    $db_dpis->send_cmd($cmdChk);
    $dataChk = $db_dpis->get_array();
    if($dataChk[CNT]=="0"){
        $cmdA = "ALTER TABLE PER_ABSENT ADD (
                    OABS_STARTDATE	CHAR(19),
                    OABS_STARTPERIOD NUMBER(1),
                    OABS_ENDDATE CHAR(19),
                    OABS_ENDPERIOD NUMBER(1),
                    OAPPROVE_PER_ID NUMBER(10),
                    OAPPROVE_DATE VARCHAR2(19),
                    OAUDIT_PER_ID NUMBER(10),
                    OAUDIT_DATE VARCHAR2(19),
                    OREVIEW1_PER_ID NUMBER(10),
                    OREVIEW1_DATE VARCHAR2(19),
                    OREVIEW2_PER_ID	NUMBER(10),
                    OREVIEW2_DATE VARCHAR2(19)
                    )";
        $db_dpis->send_cmd($cmdA);
        $cmdA = "COMMIT";
        $db_dpis->send_cmd($cmdA);
    }
    
    /*งานค้นหาP0119 สอบถามข้อมูล*/
    /*เพิ่มฟิลดิ์ สังกัดให้กับหน่วยงาน */
    $cmdChk ="  SELECT COUNT(COLUMN_NAME) AS CNT
                FROM USER_TAB_COLS
                WHERE  TABLE_NAME = 'PER_POSITIONHIS'
                  AND UPPER( COLUMN_NAME) IN('OT_NAME1','OT_NAME2','OT_NAME3') ";
    $db_dpis->send_cmd($cmdChk);
    $dataChk = $db_dpis->get_array();
    if($dataChk[CNT]=="0"){
        $cmdA = "ALTER TABLE PER_POSITIONHIS ADD (
                    OT_NAME1 VARCHAR(100),
                    OT_NAME2 VARCHAR(100),
                    OT_NAME3 VARCHAR(100)
                    )";
        $db_dpis->send_cmd($cmdA);
        $cmdA = "COMMIT";
        $db_dpis->send_cmd($cmdA);
    }

    $cmdCnt1 ="SELECT COUNT(*) AS CNT1 FROM PER_POSITIONHIS WHERE OT_NAME1 IS NULL";
    $db_dpis->send_cmd($cmdCnt1);
    $dataCnt = $db_dpis->get_array();
    $isCommit = false;
    if($dataCnt[CNT1]>0){
        $cmdUpdate ="UPDATE PER_POSITIONHIS ph
                SET ph.OT_NAME1 = 
                    (select typ.OT_name from per_org org1 ,per_org_type typ  
                     where org1.ot_code=typ.ot_code(+) 
                     and org1.org_id_ref in(1) 
                     and ph.POH_ORG1 = org1.org_name ) 
                WHERE ph.OT_NAME1 is null";
        $db_dpis->send_cmd($cmdUpdate);
        $isCommit = true;
    }
    $cmdCnt2 ="SELECT COUNT(*) AS CNT2 FROM PER_POSITIONHIS WHERE OT_NAME2 IS NULL";
    $db_dpis->send_cmd($cmdCnt2);
    $dataCnt = $db_dpis->get_array();
    if($dataCnt[CNT2]>0){
        $cmdUpdate ="UPDATE PER_POSITIONHIS ph
                SET ph.OT_NAME2 = 
                    (select typ.OT_name from per_org org1 ,per_org_type typ  
                     where org1.ot_code=typ.ot_code(+) 
                     and org1.org_id_ref in(select distinct org.org_id from per_org org where org.org_name =ph.POH_ORG1  ) 
                     and ph.POH_ORG2 = org1.org_name ) 
                WHERE ph.OT_NAME2 is null";
        $db_dpis->send_cmd($cmdUpdate);
        $isCommit = true;
    }
    $cmdCnt3 ="SELECT COUNT(*) AS CNT3 FROM PER_POSITIONHIS WHERE OT_NAME3 IS NULL";
    $db_dpis->send_cmd($cmdCnt3);
    $dataCnt = $db_dpis->get_array();
    if($dataCnt[CNT3]>0){
        $cmdUpdate ="UPDATE PER_POSITIONHIS ph
                SET ph.OT_NAME3 = 
                    (select typ.OT_name from per_org org1 ,per_org_type typ  
                     where org1.ot_code=typ.ot_code(+) 
                     and org1.org_id_ref in(select distinct org.org_id from per_org org where org.org_name =ph.POH_ORG2  )
                     and ph.POH_ORG3 = org1.org_name ) 
                WHERE ph.OT_NAME3 is null";
        $db_dpis->send_cmd($cmdUpdate);
        $isCommit = true;
    }
    if($isCommit == true){
        $cmdCommit = "COMMIT";
        $db_dpis->send_cmd($cmdCommit);
    }
    /**/
    
    $cmdUpdate ="create  index TA_REQUESTTIME_IDX3 on TA_REQUESTTIME(PER_ID, START_FLAG)";
    $db_dpis->send_cmd($cmdUpdate);
    $cmdUpdate ="create  index TA_REQUESTTIME_IDX4 on TA_REQUESTTIME(PER_ID, END_FLAG)";
    $db_dpis->send_cmd($cmdUpdate);
    $cmdUpdate ="create  index TA_REQUESTTIME_IDX5 on TA_REQUESTTIME(PER_ID, APPROVE_FLAG)";
    $db_dpis->send_cmd($cmdUpdate);
    
    $cmdUpdate ="create  index PER_ABSENTHIS_IDX1 on PER_ABSENTHIS(PER_ID, ABS_STARTDATE)";
    $db_dpis->send_cmd($cmdUpdate);
    $cmdUpdate ="create  index PER_ABSENTHIS_IDX2 on PER_ABSENTHIS(PER_ID, ABS_ENDDATE)";
    $db_dpis->send_cmd($cmdUpdate);
    $cmdUpdate ="create  index PER_ABSENTHIS_IDX3 on PER_ABSENTHIS(ABS_STARTDATE, ABS_ENDDATE)";
    $db_dpis->send_cmd($cmdUpdate);
    $cmdUpdate ="create  index PER_ABSENTHIS_IDX4 on PER_ABSENTHIS(PER_ID, TRIM(AB_CODE))";
    $db_dpis->send_cmd($cmdUpdate);
    
    $cmdUpdate ="update PER_ABSENTTYPE  set AB_TYPE = trim(AB_CODE)  where AB_TYPE is null";
    $db_dpis->send_cmd($cmdUpdate);
    
    
    
    
    $cmd = " update PER_CONTROL set CTRL_ALTER = 19 ,UPDATE_DATE=SYSDATE ";
    $db_dpis_PER_CONTROL->send_cmd($cmd);
?>
