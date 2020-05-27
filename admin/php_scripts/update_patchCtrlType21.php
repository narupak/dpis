<?php /*beta skill*/
    $db_dpis = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
    $dbDpisUserAudit = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
    $db_dpis_PicDef = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
    $db_dpis_PER_CONTROL = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
    
    $get_sysdata=date('Y-m-d');
   /*ส่วนเพิ่มเติมเรื่องภาษี*/
$sql="SELECT linkto_web FROM BACKOFFICE_MENU_BAR_LV2 WHERE linkto_web ='import_file.html?form=per_taxhis.php'";
$count_menu= $db->send_cmd($sql);
if(empty($count_menu)){
    $sqllink_th="INSERT INTO BACKOFFICE_MENU_BAR_LV2(FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_ICON, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, 
            LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY) 
            VALUES(1, 'TH', 458, 20, NULL, 'P1120 นำเข้าข้อมูลหนังสือรับรองการหักภาษี (กรมบัญชีกลาง)', 'S', 'W', 'import_file.html?form=per_taxhis.php', 
            0, 35, 251, '".$get_sysdata."', 1, '".$get_sysdata."', 1)";
    $db->send_cmd($sqllink_th);
    $sqllink_en="INSERT INTO BACKOFFICE_MENU_BAR_LV2(FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_ICON, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, 
        LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY) 
            VALUES (1, 'EN', 458, 20, NULL, 'P1120 นำเข้าข้อมูลหนังสือรับรองการหักภาษี (กรมบัญชีกลาง)', 'S', 'W', 'import_file.html?form=per_taxhis.php', 
            0, 35, 251, '".$get_sysdata."', 1, '".$get_sysdata."', 1)";
    $db->send_cmd($sqllink_en);
    
    $sql="INSERT INTO user_privilege(group_id,page_id,menu_id_lv0, 
                    menu_id_lv1,menu_id_lv2,menu_id_lv3,can_add,can_edit,can_del,can_inq,can_print,can_confirm,
                    can_audit,can_attach) 
            VALUES(1,1,35,251,458,0,'Y','Y','Y','Y','Y','Y','Y','Y')";
    $db->send_cmd($sql);
}

$sql="SELECT linkto_web FROM BACKOFFICE_MENU_BAR_LV2 WHERE linkto_web ='personal_master.html?SEARCHHIS=per_taxhis'";
$count_menu= $db->send_cmd($sql);
if(empty($count_menu)){
    $sqllink_th="INSERT INTO BACKOFFICE_MENU_BAR_LV2(FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_ICON, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, 
                    LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY) 
          VALUES(1, 'TH', 459, 45, NULL, 'P0145 ประวัติหนังสือรับรองการหักภาษี (กรมบัญชีกลาง)', 'S', 'W', 'personal_master.html?SEARCHHIS=per_taxhis', 
                    0, 35, 241, '".$get_sysdata."', 1, '".$get_sysdata."', 1)";
    $db->send_cmd($sqllink_th);
    $sqllink_en="INSERT INTO BACKOFFICE_MENU_BAR_LV2(FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_ICON, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, 
                    LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY) 
            VALUES(1, 'EN', 459, 45, NULL, 'P0145 ประวัติหนังสือรับรองการหักภาษี (กรมบัญชีกลาง)', 'S', 'W', 'personal_master.html?SEARCHHIS=per_taxhis', 
            0, 35, 241, '".$get_sysdata."', 1, '".$get_sysdata."', 1)";
    $db->send_cmd($sqllink_en);
    $sql="INSERT INTO user_privilege(group_id,page_id,menu_id_lv0, 
                    menu_id_lv1,menu_id_lv2,menu_id_lv3,can_add,can_edit,can_del,can_inq,can_print,can_confirm,
                    can_audit,can_attach) 
            VALUES(1,1,35,241,459,0,'Y','Y','Y','Y','Y','Y','Y','Y')";
    $db->send_cmd($sql);
}   

    $cmdChk ="  SELECT COUNT(COLUMN_NAME) AS CNT
                FROM USER_TAB_COLS
                WHERE TABLE_NAME = 'PER_POSITIONHIS'
                  AND UPPER(COLUMN_NAME) IN('POH_ASS_MINISTRY') ";
    $db_dpis->send_cmd($cmdChk);
    $dataChk = $db_dpis->get_array();
    if($dataChk[CNT]=="0"){
        $cmdA = "ALTER TABLE PER_POSITIONHIS add(POH_ASS_MINISTRY varchar2(255) )";
        $db_dpis->send_cmd($cmdA);
        $cmdA = "COMMIT";
        $db_dpis->send_cmd($cmdA);
    }
    $cmdA = "ALTER TABLE PER_ORG_ASS modify ORG_NAME VARCHAR2(255)";
    $db_dpis->send_cmd($cmdA);
    $cmdA = "COMMIT";
    $db_dpis->send_cmd($cmdA);
    $cmdA = "ALTER TABLE PER_ORG_ASS modify ORG_SHORT VARCHAR2(255)";
    $db_dpis->send_cmd($cmdA);
    $cmdA = "COMMIT";
    $db_dpis->send_cmd($cmdA);

    
    /**/
    $cmd = " update PER_CONTROL set CTRL_ALTER = 21 ,UPDATE_DATE=to_char(sysdate,'YYYY-MM-DD HH:MI:ss') ";
    $db_dpis_PER_CONTROL->send_cmd($cmd);
    $cmdA = "COMMIT";
    $db->send_cmd($cmdA);
    
?>
