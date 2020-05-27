<?php
$db_dpis = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
    $dbDpisUserAudit = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
    $db_dpis_PicDef = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
    $db_dpis_PER_CONTROL = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
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
