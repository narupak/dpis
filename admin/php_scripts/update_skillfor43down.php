<?php /*beta skill*/
    $db_dpis = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
    $dbDpisUserAudit = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
    $db_dpis_PicDef = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
    $db_dpis_PER_CONTROL = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
    
    /*ตรวจสอบ create table*/
    $cmdChk ="  SELECT COUNT(*)AS CNT FROM user_tables WHERE  UPPER(TABLE_NAME) = 'PER_SPECIAL_SKILLMIN' ";
    $db_dpis->send_cmd($cmdChk);
    $dataChk = $db_dpis->get_array();
    if($dataChk[CNT]=="0"){
        $cmdA = "CREATE TABLE PER_SPECIAL_SKILLMIN(
                    SPMIN_CODE  VARCHAR2(13 BYTE) NOT NULL ,
                    SPMIN_TITLE  VARCHAR2(255) NULL,
                    SPMIN_DESC  VARCHAR2(2000) NULL,
                    SPMIN_ACTIVE NUMBER(1) NULL,
                    UPDATE_USER NUMBER(11) NOT NULL,
                    UPDATE_DATE VARCHAR2(19) NOT NULL,
                    CREATE_USER   NUMBER(11) NOT NULL,
                    CREATE_DATE   VARCHAR2(19) NOT NULL,
                    CONSTRAINT PK_PER_SPECIAL_SKILLMIN PRIMARY KEY (SPMIN_CODE))";
        $db_dpis->send_cmd($cmdA);
        $cmdA = "COMMIT";
        $db_dpis->send_cmd($cmdA);
    }
    $cmdChk ="  SELECT COUNT(*)AS CNT FROM user_tables WHERE  UPPER(TABLE_NAME) = 'PER_MAPPING_SKILLMIN' ";
    $db_dpis->send_cmd($cmdChk);
    $dataChk = $db_dpis->get_array();
    if($dataChk[CNT]=="0"){
        $cmdA = "CREATE TABLE PER_MAPPING_SKILLMIN(
                        SPMIN_CODE  VARCHAR2(13) NOT NULL ,
                        SS_CODE VARCHAR2(10) NOT NULL
                )";
        $db_dpis->send_cmd($cmdA);
        $cmdA = "create  index PER_MAPPING_SKILLMIN_IDX1 on PER_MAPPING_SKILLMIN(SPMIN_CODE, SS_CODE)";
        $db_dpis->send_cmd($cmdA);
        $cmdA = "COMMIT";
        $db_dpis->send_cmd($cmdA);
    }
    $cmdChk = "SELECT COUNT(*)AS CNT FROM user_tables WHERE  UPPER(TABLE_NAME) = 'PER_LEVELSKILL'";
    $db_dpis->send_cmd($cmdChk);
    $dataChk = $db_dpis->get_array();
    if($dataChk[CNT]=="0"){
        $cmdA = "create table PER_LEVELSKILL(
                LEVELSKILL_CODE	VARCHAR2(10)	not null,
                LEVELSKILL_NAME	VARCHAR2(100)	not null	,
                LS_ACTIVE		NUMBER(1)	default 1,
                UPDATE_USER		NUMBER(11),
                UPDATE_DATE		CHAR(19),
                SEQ_NO			NUMBER(5),
                CONSTRAINT PER_LEVELSKILL_PK PRIMARY KEY (LEVELSKILL_CODE),
                CONSTRAINT PER_LEVELSKILL_UK UNIQUE (LEVELSKILL_NAME)
                )";
        $db_dpis->send_cmd($cmdA);
        $cmdA = "COMMIT";
        $db_dpis->send_cmd($cmdA);
    }
          
    /*ตรวจสอบ เพิ่ม FLD*/
    
    $cmdChk ="  SELECT COUNT(COLUMN_NAME) AS CNT
                FROM USER_TAB_COLS
                WHERE  TABLE_NAME = 'PER_SPECIAL_SKILLGRP'
                AND UPPER(COLUMN_NAME) IN('REF_CODE')";
                $db_dpis->send_cmd($cmdChk);
                $dataChk = $db_dpis->get_array();
    if($dataChk[CNT]=="0"){
        $cmdA = "ALTER TABLE PER_SPECIAL_SKILLGRP ADD  REF_CODE CHAR(10)";
        $db_dpis->send_cmd($cmdA);
        $cmdA = "COMMIT";
        $db_dpis->send_cmd($cmdA);
        
    }
    /*เพิ่ม ตรวจสอบก่อนทำการแทรกคอลัมน์ 16/12/2016 */
     $cmdChk ="  SELECT COUNT(COLUMN_NAME) AS CNT
                FROM USER_TAB_COLS
                WHERE  TABLE_NAME = 'GMIS_GPIS'
                AND UPPER(COLUMN_NAME) 
                IN('TEMPMPERSPSKILL1','TEMPSPERSPSKILL1','TEMPSKILLLEVEL1','TEMPPERSPSKILLDES1',
                'TEMPMPERSPSKILL2','TEMPSPERSPSKILL2','TEMPSKILLLEVEL2','TEMPPERSPSKILLDES2',
                'TEMPMPERSPSKILL3','TEMPSPERSPSKILL3','TEMPSKILLLEVEL3','TEMPPERSPSKILLDES3')";
    $db_dpis->send_cmd($cmdChk);
    $dataChk = $db_dpis->get_array();
    if($dataChk[CNT]=="0"){
        $cmdA = "ALTER TABLE GMIS_GPIS ADD  TEMPMPERSPSKILL1 VARCHAR2(100)";
        $db_dpis->send_cmd($cmdA);
        $cmdA = "ALTER TABLE GMIS_GPIS ADD  TEMPSPERSPSKILL1 VARCHAR2(100)";
        $db_dpis->send_cmd($cmdA);
        $cmdA = "ALTER TABLE GMIS_GPIS ADD  TEMPSKILLLEVEL1 VARCHAR2(100)";
        $db_dpis->send_cmd($cmdA);
        $cmdA = "ALTER TABLE GMIS_GPIS ADD  TEMPPERSPSKILLDES1 VARCHAR2(2000)";
        $db_dpis->send_cmd($cmdA);
        $cmdA = "ALTER TABLE GMIS_GPIS ADD  TEMPMPERSPSKILL2 VARCHAR2(100)";
        $db_dpis->send_cmd($cmdA);
        $cmdA = "ALTER TABLE GMIS_GPIS ADD  TEMPSPERSPSKILL2 VARCHAR2(100)";
        $db_dpis->send_cmd($cmdA);
        $cmdA = "ALTER TABLE GMIS_GPIS ADD  TEMPSKILLLEVEL2 VARCHAR2(100)";
        $db_dpis->send_cmd($cmdA);
        $cmdA = "ALTER TABLE GMIS_GPIS ADD  TEMPPERSPSKILLDES2 VARCHAR2(2000)";
        $db_dpis->send_cmd($cmdA);
        $cmdA = "ALTER TABLE GMIS_GPIS ADD  TEMPMPERSPSKILL3 VARCHAR2(100)";
        $db_dpis->send_cmd($cmdA);
        $cmdA = "ALTER TABLE GMIS_GPIS ADD  TEMPSPERSPSKILL3 VARCHAR2(100)";
        $db_dpis->send_cmd($cmdA);
        $cmdA = "ALTER TABLE GMIS_GPIS ADD  TEMPSKILLLEVEL3 VARCHAR2(100)";
        $db_dpis->send_cmd($cmdA);
        $cmdA = "ALTER TABLE GMIS_GPIS ADD  TEMPPERSPSKILLDES3 VARCHAR2(2000)";
        $db_dpis->send_cmd($cmdA);
        $cmdA = "COMMIT";
        $db_dpis->send_cmd($cmdA);
    }

    /*นำข้อมูลเข้า*/
    //PER_LEVELSKILL
    $sql="TRUNCATE TABLE PER_LEVELSKILL";
    $db_dpis->send_cmd($sql);
    $myfile = fopen("php_scripts/gen_per_levelskill.txt", "r") or die("Unable to open file gen_per_levelskill.txt!");
    while(!feof($myfile)) {
        $sql=fgets($myfile);
        $db_dpis->send_cmd($sql);
        
    }
    $cmdA = "COMMIT";
    $db_dpis->send_cmd($cmdA);
    fclose($myfile);   
    
    $sql="TRUNCATE TABLE PER_SPECIAL_SKILLGRP";
    $db_dpis->send_cmd($sql);
    //PER_SPECIAL_SKILLGRP main
    $myfile2 = fopen("php_scripts/gen_per_special_skillgrp_main.txt", "r") or die("Unable to open file gen_per_special_skillgrp_main.txt!");
    while(!feof($myfile2)) {
        $sql=fgets($myfile2);
        $db_dpis->send_cmd($sql);
    }
    $cmdA = "COMMIT";
    $db_dpis->send_cmd($cmdA);
    fclose($myfile2); 
    
    //PER_SPECIAL_SKILLGRP sub
    
    $myfile3 = fopen("php_scripts/gen_per_special_skillgrp_sub.txt", "r") or die("Unable to open file gen_per_special_skillgrp_sub.txt!");
    while(!feof($myfile3)) {
        $sql=fgets($myfile3);
        $db_dpis->send_cmd($sql);
    }
    $cmdA = "COMMIT";
    $db_dpis->send_cmd($cmdA);
    fclose($myfile3); 
    
    //PER_SPECIAL_SKILLMIN
    $sql="TRUNCATE TABLE PER_SPECIAL_SKILLMIN";
    $db_dpis->send_cmd($sql);
    $myfile4 = fopen("php_scripts/gen_per_special_skillmin.txt", "r") or die("Unable to open file gen_per_special_skillmin.txt!");
    while(!feof($myfile4)) {
        $sql=fgets($myfile4);
        $db_dpis->send_cmd($sql);
    }
    $cmdA = "COMMIT";
    $db_dpis->send_cmd($cmdA);
    fclose($myfile4); 
    
    //PER_MAPPING_SKILLMIN
    $sql="TRUNCATE TABLE PER_MAPPING_SKILLMIN";
    $db_dpis->send_cmd($sql);
    $myfile5 = fopen("php_scripts/gen_per_mapping_skillmin.txt", "r") or die("Unable to open file gen_per_mapping_skillmin.txt!");
    while(!feof($myfile5)) {
        $sql=fgets($myfile5);
        $db_dpis->send_cmd($sql);
        //echo $sql.'<br>++++++++++++++++++++++++++<br>';
        
    }
    $cmdA = "COMMIT";
    $db_dpis->send_cmd($cmdA);
    fclose($myfile5); 

    
    /*insert menu*/
    $sql="UPDATE BACKOFFICE_MENU_BAR_LV2 
            SET menu_label='M0504 ด้านความเชี่ยวชาญพิเศษ(หลัก,ย่อย)'
            WHERE linkto_web ='master_table.html?table=PER_SPECIAL_SKILLGRP'";
			//echo $sql;
    $db_dpis->send_cmd($sql);
    
    $sql="SELECT MENU_LABEL FROM BACKOFFICE_MENU_BAR_LV2 WHERE linkto_web ='master_table_special_skillmin.html?table=PER_SPECIAL_SKILLMIN'";
    $count_menu= $db_dpis->send_cmd($sql);
    if(!$count_menu){
        $sqllink_th="INSERT INTO BACKOFFICE_MENU_BAR_LV2(FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_ICON, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, 
            LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY) 
            VALUES(1, 'TH', 457, 5, NULL, 'M0505 ด้านความเชี่ยวชาญพิเศษ(รอง)', 'S', 'W', 'master_table_special_skillmin.html?table=PER_SPECIAL_SKILLMIN', 
            0, 9, 299, sysdate, 1, sysdate, 1)";
        $db_dpis->send_cmd($sqllink_th);
        $sqllink_en="INSERT INTO BACKOFFICE_MENU_BAR_LV2(FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_ICON, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, 
            LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY) 
            VALUES(1, 'EN', 457, 5, NULL, 'M0505 ด้านความเชี่ยวชาญพิเศษ(รอง)', 'S', 'W', 'master_table_special_skillmin.html?table=PER_SPECIAL_SKILLMIN', 
            0, 9, 299, sysdate, 1, sysdate, 1)";
        $db_dpis->send_cmd($sqllink_en);
        
        $sql="INSERT INTO user_privilege(group_id,page_id,menu_id_lv0, 
                    menu_id_lv1,menu_id_lv2,menu_id_lv3,can_add,can_edit,can_del,can_inq,can_print,can_confirm,
                    can_audit,can_attach) 
            VALUES(1,1,9,299,457,0,'Y','Y','Y','Y','Y','Y','Y','Y')";
        $db_dpis->send_cmd($sql);
    }
    
    $sql="SELECT MENU_LABEL FROM BACKOFFICE_MENU_BAR_LV2 WHERE linkto_web ='master_table.html?table=PER_LEVELSKILL'";
    $count_menu= $db_dpis->send_cmd($sql);
    if(!$count_menu){
        $sqllink_th="INSERT INTO BACKOFFICE_MENU_BAR_LV2(FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_ICON, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, 
            LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY) 
            VALUES(1, 'TH', 456, 6, NULL, 'M0506 ระดับความเชี่ยวชาญ', 'S', 'W', 'master_table.html?table=PER_LEVELSKILL', 
            0, 9, 299, sysdate, 1, sysdate, 1)";
        $db_dpis->send_cmd($sqllink_th);
        $sqllink_en="INSERT INTO BACKOFFICE_MENU_BAR_LV2(FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_ICON, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, 
            LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY) 
            VALUES(1, 'EN', 456, 6, NULL, 'M0506 ระดับความเชี่ยวชาญ', 'S', 'W', 'master_table.html?table=PER_LEVELSKILL', 
            0, 9, 299, sysdate, 1, sysdate, 1)";
        $db_dpis->send_cmd($sqllink_en);
        
        $sql="INSERT INTO user_privilege(group_id,page_id,menu_id_lv0, 
                    menu_id_lv1,menu_id_lv2,menu_id_lv3,can_add,can_edit,can_del,can_inq,can_print,can_confirm,
                    can_audit,can_attach) 
            VALUES(1,1,9,299,456,0,'Y','Y','Y','Y','Y','Y','Y','Y')";
        $db_dpis->send_cmd($sql);
    }
?>
