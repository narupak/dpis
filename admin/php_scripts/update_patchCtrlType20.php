<?php
    $db_dpis = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
    $dbDpisUserAudit = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
    $db_dpis_PicDef = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
    $db_dpis_PER_CONTROL = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
    
    $cmdChk ="  SELECT COUNT(COLUMN_NAME) AS CNT
                FROM USER_TAB_COLS
                WHERE TABLE_NAME = 'PER_ABSENT'
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
    
    /*---------------------------------------*/
    $cmdChk ="  SELECT COUNT(COLUMN_NAME) AS CNT
                FROM USER_TAB_COLS
                WHERE TABLE_NAME = 'PER_ABSENT'
                  AND UPPER(COLUMN_NAME) IN('ORI_ABS_STARTDATE','ORI_ABS_STARTPERIOD',
                                            'ORI_ABS_ENDDATE','ORI_ABS_ENDPERIOD') ";
    $db_dpis->send_cmd($cmdChk);
    $dataChk = $db_dpis->get_array();
    if($dataChk[CNT]=="0"){
        $cmdA = "ALTER TABLE PER_ABSENT ADD (
                    ORI_ABS_STARTDATE CHAR(19),
                    ORI_ABS_STARTPERIOD NUMBER(1),
                    ORI_ABS_ENDDATE CHAR(19),
                    ORI_ABS_ENDPERIOD NUMBER(1)
                )";
        $db_dpis->send_cmd($cmdA);
        $cmdA = "COMMIT";
        $db_dpis->send_cmd($cmdA);
    }
     /*---------------------------------------*/
    $cmdChk ="  SELECT COUNT(COLUMN_NAME) AS CNT
                FROM USER_TAB_COLS
                WHERE  TABLE_NAME = 'PER_ABSENT'
                  AND UPPER(COLUMN_NAME) IN('ORI_ABS_DAY','OABS_DAY')";
    $db_dpis->send_cmd($cmdChk);
    $dataChk = $db_dpis->get_array();
    if($dataChk[CNT]=="0"){
        $cmdA = "ALTER TABLE PER_ABSENT ADD (ORI_ABS_DAY NUMBER(8,2),OABS_DAY NUMBER(8,2))";
        $db_dpis->send_cmd($cmdA);
        $cmdA = "COMMIT";
        $db_dpis->send_cmd($cmdA);
    }
     /*---------------------------------------*/
    $cmdChk ="SELECT COUNT(COLUMN_NAME) AS CNT
              FROM USER_TAB_COLS
              WHERE TABLE_NAME = 'PER_ACTINGHIS'
                AND UPPER( COLUMN_NAME) IN('ACTH_ORG6_ASSIGN','ACTH_ORG7_ASSIGN','ACTH_ORG8_ASSIGN')";
    $db_dpis->send_cmd($cmdChk);
    $dataChk = $db_dpis->get_array();
    if($dataChk[CNT]=="0"){
        $cmdA = "ALTER TABLE PER_ACTINGHIS ADD ACTH_ORG6_ASSIGN VARCHAR(100)";
        $db_dpis->send_cmd($cmdA);
        $cmdA = "ALTER TABLE PER_ACTINGHIS ADD ACTH_ORG7_ASSIGN VARCHAR(100)";
        $db_dpis->send_cmd($cmdA);
        $cmdA = "ALTER TABLE PER_ACTINGHIS ADD ACTH_ORG8_ASSIGN VARCHAR(100)";
        $db_dpis->send_cmd($cmdA);
    }
     /*---------------------------------------*/
    /*ตรวจสอบก่อนทำการแทรกคอลัมน์*/
        $cmdChk ="  SELECT COUNT(COLUMN_NAME) AS CNT
                    FROM USER_TAB_COLS
                    WHERE  TABLE_NAME = 'PER_LAYER'
                      AND UPPER(COLUMN_NAME) IN('LAYER_SALARY_TEMPUP')";
        $db_dpis->send_cmd($cmdChk);
        $dataChk = $db_dpis->get_array();
        if($dataChk[CNT]=="0"){
            $cmdA = "ALTER TABLE PER_LAYER ADD  LAYER_SALARY_TEMPUP NUMBER(16,2)";
            $db_dpis->send_cmd($cmdA);
            $cmdA = "COMMIT";
            $db_dpis->send_cmd($cmdA);
            //ทั่วไป-->ปฏิบัติงาน
            $cmd = " update PER_LAYER set LAYER_SALARY_TEMPUP=38750 where LAYER_TYPE = 0 and LEVEL_NO = 'O1' and LAYER_NO = 0 ";
            $db_dpis->send_cmd($cmd);
            //ทั่วไป-->ชำนาญงาน
            $cmd = " update PER_LAYER set LAYER_SALARY_TEMPUP=54820 where LAYER_TYPE = 0 and LEVEL_NO = 'O2' and LAYER_NO = 0 ";
            $db_dpis->send_cmd($cmd);
            //ทั่วไป-->อาวุโส
            $cmd = " update PER_LAYER set LAYER_SALARY_TEMPUP=69040 where LAYER_TYPE = 0 and LEVEL_NO = 'O3' and LAYER_NO = 0 ";
            $db_dpis->send_cmd($cmd);
            //ทั่วไป-->ทักษะพิเศษ
            $cmd = " update PER_LAYER set LAYER_SALARY_TEMPUP=74320 where LAYER_TYPE = 0 and LEVEL_NO = 'O4' and LAYER_NO = 0 ";
            $db_dpis->send_cmd($cmd);
            //วิชาการ-->ปฏิบัติการ    
            $cmd = " update PER_LAYER set LAYER_SALARY_TEMPUP=43600 where LAYER_TYPE = 0 and LEVEL_NO = 'K1' and LAYER_NO = 0 ";
            $db_dpis->send_cmd($cmd);
            //วิชาการ-->ชำนาญการ
            $cmd = " update PER_LAYER set LAYER_SALARY_TEMPUP=58390 where LAYER_TYPE = 0 and LEVEL_NO = 'K2' and LAYER_NO = 0 ";
            $db_dpis->send_cmd($cmd);
            //วิชาการ-->ชำนาญการพิเศษ
            $cmd = " update PER_LAYER set LAYER_SALARY_TEMPUP=69040 where LAYER_TYPE = 0 and LEVEL_NO = 'K3' and LAYER_NO = 0 ";
            $db_dpis->send_cmd($cmd);
            //วิชาการ-->เชี่ยวชาญ
            $cmd = " update PER_LAYER set LAYER_SALARY_TEMPUP=74320 where LAYER_TYPE = 0 and LEVEL_NO = 'K4' and LAYER_NO = 0 ";
            $db_dpis->send_cmd($cmd);
            //วิชาการ-->ทรงคุณวุฒิ
            $cmd = " update PER_LAYER set LAYER_SALARY_TEMPUP=76800 where LAYER_TYPE = 0 and LEVEL_NO = 'K5' and LAYER_NO = 0 ";
            $db_dpis->send_cmd($cmd);
            //อำนวยการ-->ต้น
            $cmd = " update PER_LAYER set LAYER_SALARY_TEMPUP=70360 where LAYER_TYPE = 0 and LEVEL_NO = 'D1' and LAYER_NO = 0 ";
            $db_dpis->send_cmd($cmd);
            //อำนวยการ-->สูง
            $cmd = " update PER_LAYER set LAYER_SALARY_TEMPUP=74320 where LAYER_TYPE = 0 and LEVEL_NO = 'D2' and LAYER_NO = 0 ";
            $db_dpis->send_cmd($cmd);
            //บริหาร-->ต้น
            $cmd = " update PER_LAYER set LAYER_SALARY_TEMPUP=76800 where LAYER_TYPE = 0 and LEVEL_NO = 'M1' and LAYER_NO = 0 ";
            $db_dpis->send_cmd($cmd);
            //บริหาร-->สูง
            $cmd = " update PER_LAYER set LAYER_SALARY_TEMPUP=76800 where LAYER_TYPE = 0 and LEVEL_NO = 'M2' and LAYER_NO = 0 ";
            $db_dpis->send_cmd($cmd);
            $cmdA = "COMMIT";
            $db_dpis->send_cmd($cmdA);
        }
     /*---------------------------------------*/
    /*ตรวจสอบก่อนทำการแทรกคอลัมน์*/
    $cmdChk ="  SELECT COUNT(COLUMN_NAME) AS CNT
                FROM USER_TAB_COLS
                WHERE  TABLE_NAME = 'PER_COMPETENCE'
                  AND UPPER(COLUMN_NAME) IN('DEF_WEIGHT')";
    $db_dpis->send_cmd($cmdChk);
    $dataChk = $db_dpis->get_array();
    if($dataChk[CNT]=="0"){
        $cmdA = "ALTER TABLE PER_COMPETENCE ADD (DEF_WEIGHT	NUMBER(5,2))";
        $db_dpis->send_cmd($cmdA);
        $cmdA = "COMMIT";
        $db_dpis->send_cmd($cmdA);
    }
     /*---------------------------------------*/
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

    /**/
     /*---------------------------------------*/
        $cmdModify = "select column_name, data_type ,data_length
				 from user_tab_columns
				 where table_name = 'PER_SPECIAL_SKILL'
				 and column_name = 'SPS_EMPHASIZE'";
	 $db_dpis2->send_cmd($cmdModify);
	 $dataModify = $db_dpis2->get_array_array();
	 $data_length = $dataModify[2];
	 if($data_length < 2000){
		 $cmdModify = "ALTER TABLE PER_SPECIAL_SKILL modify SPS_EMPHASIZE VARCHAR2(2000)";
		 $db_dpis2->send_cmd($cmdModify);
		 $db_dpis2->send_cmd("COMMIT");
	 }
     /*---------------------------------------*/
         $cmdChk ="  SELECT COUNT(COLUMN_NAME) AS CNT
				         FROM USER_TAB_COLS
				         WHERE  TABLE_NAME = 'PER_SPECIAL_SKILLGRP'
				         AND UPPER(COLUMN_NAME) IN('REF_CODE')";
	 $db_dpis->send_cmd($cmdChk);
	 $dataChk = $db_dpis->get_array();
	 if($dataChk[CNT]=="0"){
		 $cmdA = "ALTER TABLE PER_SPECIAL_SKILLGRP ADD REF_CODE CHAR(10)";
		 $db_dpis->send_cmd($cmdA);
		 $cmdA = "COMMIT";
		 $db_dpis->send_cmd($cmdA);
	 }
   
    $cmd = " update PER_CONTROL set CTRL_ALTER = 20 ,UPDATE_DATE=SYSDATE ";
    $db_dpis_PER_CONTROL->send_cmd($cmd);
?>
