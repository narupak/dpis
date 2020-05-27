<?php
/*เพิ่มฟิลดิ์ สังกัดให้กับหน่วยงาน */
        $cmdChk ="  SELECT COUNT(COLUMN_NAME) AS CNT
                    FROM USER_TAB_COLS
                    WHERE TABLE_NAME = 'PER_POSITIONHIS'
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
                        (CASE WHEN (select typ.ot_name from per_org org1 ,per_org_type typ  
                         where org1.ot_code=typ.ot_code(+) 
                         and org1.org_id_ref in(1) 
                         and ph.POH_ORG1 = org1.org_name and ROWNUM=1) IS NULL 
                         THEN ' ' 
                         ELSE 
                         (select typ.ot_name from per_org org1 ,per_org_type typ  
                         where org1.ot_code=typ.ot_code(+) 
                         and org1.org_id_ref in(1) 
                         and ph.POH_ORG1 = org1.org_name and ROWNUM=1)
                         END) 
                    WHERE ph.OT_NAME1 is NULL";
            $db_dpis->send_cmd($cmdUpdate);
            $isCommit = true;
        }
        $cmdCnt2 ="SELECT COUNT(*) AS CNT2 FROM PER_POSITIONHIS WHERE OT_NAME2 IS NULL";
        $db_dpis->send_cmd($cmdCnt2);
        $dataCnt = $db_dpis->get_array();
        if($dataCnt[CNT2]>0){
            $cmdUpdate ="UPDATE PER_POSITIONHIS ph
                    SET ph.OT_NAME2 = 
                        (CASE WHEN 
                            (select typ.OT_name from per_org org1 ,per_org_type typ  
                             where org1.ot_code=typ.ot_code(+) 
                             and org1.org_id_ref in(select distinct org.org_id from per_org org where org.org_name =ph.POH_ORG1  ) 
                             and ph.POH_ORG2 = org1.org_name and ROWNUM=1)  IS NULL 
                        THEN ' ' 
                        ELSE
                            (select typ.OT_name from per_org org1 ,per_org_type typ  
                             where org1.ot_code=typ.ot_code(+) 
                             and org1.org_id_ref in(select distinct org.org_id from per_org org where org.org_name =ph.POH_ORG1  ) 
                             and ph.POH_ORG2 = org1.org_name and ROWNUM=1)
                        END) 
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
                        (CASE WHEN 
                            (select typ.OT_name from per_org org1 ,per_org_type typ  
                             where org1.ot_code=typ.ot_code(+) 
                             and org1.org_id_ref in(select distinct org.org_id from per_org org where org.org_name =ph.POH_ORG2  )
                             and ph.POH_ORG3 = org1.org_name and ROWNUM=1)  IS NULL 
                        THEN ' ' 
                        ELSE
                        (select typ.OT_name from per_org org1 ,per_org_type typ  
                         where org1.ot_code=typ.ot_code(+) 
                         and org1.org_id_ref in(select distinct org.org_id from per_org org where org.org_name =ph.POH_ORG2  )
                         and ph.POH_ORG3 = org1.org_name and ROWNUM=1)
                        END
                        
                         ) 
                    WHERE ph.OT_NAME3 is null";
            $db_dpis->send_cmd($cmdUpdate);
            $isCommit = true;
        }
        if($isCommit == true){
            $cmdCommit = "COMMIT";
            $db_dpis->send_cmd($cmdCommit);
        }
        /**/
?>
