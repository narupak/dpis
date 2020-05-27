<?php

include("php_scripts/session_start.php");
include("php_scripts/function_share.php");
include("php_scripts/load_per_control.php");

$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
$UPDATE_DATE = date("Y-m-d H:i:s");

set_time_limit(0); //  เซ็ตเวลาให้ออกรายงานได้นานขึ้น
ini_set("memory_limit", "-1");
ini_set("max_execution_time", 0);

if ($db_type == "oci8"){
    $cmdChk="SELECT COUNT(*) AS CNT FROM USER_TAB_COLS WHERE TABLE_NAME = 'USER_DETAIL' AND UPPER(COLUMN_NAME) = 'USER_FLAG'";
    $db->send_cmd($cmdChk);
    $dataChk = $db->get_array();
    if($dataChk[CNT]=="0"){
       $cmdA=" ALTER TABLE USER_DETAIL ADD USER_FLAG CHAR(1) DEFAULT 'Y' ";
       $db->send_cmd($cmdA);
       $cmdA = "COMMIT";
       $db->send_cmd($cmdA);
    }
}elseif ($db_type == "mysql"){
   $cmdChk="SELECT user_flag FROM user_detail"; 
   $count_data = $db->send_cmd($cmdChk);
   if (!$count_data) {
       $cmdA=" ALTER TABLE USER_DETAIL ADD USER_FLAG CHAR(1) DEFAULT 'Y' ";
       $db->send_cmd($cmdA);
   }
}

$path_data_err = 'php_scripts/C16_XLogs.txt';
function write_errtofile($logfile, $txt_err) {
    /*if (is_file($logfile)) {
        unlink($logfile);
    }
    $fp = fopen($logfile, 'a');
    fwrite($fp, $txt_err);
    fclose($fp);*/
}
//write_errtofile($path_data_err, $txt_err);
if ($command == 'ALTER') {
    unlink($path_data_err);
    $cmd = " SELECT COUNT(FID) AS COUNT_DATA FROM BACKOFFICE_MENU_BAR_LV0 ";
    $count_data = $db_dpis->send_cmd($cmd);
    //$db_dpis->show_error();
    if (!$count_data) {
        if ($DPISDB == "odbc")
            $cmd = " CREATE TABLE BACKOFFICE_MENU_BAR_LV0(
				FID INTEGER NULL,
				LANGCODE CHAR(2) NOT NULL,
				MENU_ID INTEGER NOT NULL,
				MENU_ORDER INTEGER NULL,
				MENU_ICON VARCHAR(255) NULL,
				MENU_LABEL VARCHAR(255) NULL,
				FLAG_SHOW CHAR(1) NULL,
				TYPE_LINKTO CHAR(1) NULL,
				LINKTO_WEB VARCHAR(255) NULL,
				LINKTO_TID INTEGER NULL,
				CREATE_DATE VARCHAR(19) NULL,
				CREATE_BY INTEGER NULL,
				UPDATE_DATE VARCHAR(19) NULL,
				UPDATE_BY INTEGER NULL,
				CONSTRAINT PK_BACKOFFICE_MENU_BAR_LV0 PRIMARY KEY (LANGCODE, MENU_ID)) ";
        elseif ($DPISDB == "oci8")
            $cmd = " CREATE TABLE BACKOFFICE_MENU_BAR_LV0(
				FID NUMBER(11) NULL,
				LANGCODE CHAR(2) NOT NULL,
				MENU_ID NUMBER(11) NOT NULL,
				MENU_ORDER NUMBER(11) NULL,
				MENU_ICON VARCHAR2(255) NULL,
				MENU_LABEL VARCHAR2(255) NULL,
				FLAG_SHOW CHAR(1) NULL,
				TYPE_LINKTO CHAR(1) NULL,
				LINKTO_WEB VARCHAR2(255) NULL,
				LINKTO_TID NUMBER(11) NULL,
				CREATE_DATE VARCHAR2(19) NULL,
				CREATE_BY NUMBER(11) NULL,
				UPDATE_DATE VARCHAR2(19) NULL,
				UPDATE_BY NUMBER(11) NULL,
				CONSTRAINT PK_BACKOFFICE_MENU_BAR_LV0 PRIMARY KEY (LANGCODE, MENU_ID)) ";
        $db_dpis->send_cmd($cmd);
        //$db_dpis->show_error();
    }

    $cmd = " delete from BACKOFFICE_MENU_BAR_LV0 ";
    $db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();

    $cmd = " select FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_ICON, MENU_LABEL, FLAG_SHOW,
							TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY
						  from BACKOFFICE_MENU_BAR_LV0 order by	MENU_ID, LANGCODE ";
    $db->send_cmd($cmd);
    while ($data = $db->get_array()) {
        $data = array_change_key_case($data, CASE_LOWER);
        $FID = $data[fid];
        $LANGCODE = $data[langcode];
        $MENU_ID = $data[menu_id];
        $MENU_ORDER = $data[menu_order];
        $MENU_ICON = $data[menu_icon];
        $MENU_LABEL = $data[menu_label];
        $FLAG_SHOW = $data[flag_show];
        $TYPE_LINKTO = $data[type_linkto];
        $LINKTO_WEB = $data[linkto_web];
        $LINKTO_TID = $data[linkto_tid];
        $CREATE_DATE = $data[create_date];
        $CREATE_BY = $data[create_by];
        $UPDATE_DATE = $data[update_date];
        $UPDATE_BY = $data[update_by];
        if (!$CREATE_BY)
            $CREATE_BY = 1;
        if (!$UPDATE_BY)
            $UPDATE_BY = 1;
        $cmd = " insert into BACKOFFICE_MENU_BAR_LV0 (FID, LANGCODE, MENU_ID, MENU_ORDER,
							  MENU_ICON, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID,
							 CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  values ($FID, '$LANGCODE', $MENU_ID, $MENU_ORDER, '$MENU_ICON', '$MENU_LABEL',
							  '$FLAG_SHOW', '$TYPE_LINKTO', '$LINKTO_WEB', $LINKTO_TID,
							 '$CREATE_DATE', $CREATE_BY, '$UPDATE_DATE', $UPDATE_BY) ";
        $db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
    } // end while

    write_errtofile($path_data_err, "SUCCESS BACKOFFICE_MENU_BAR_LV0 \n ");

    $cmd = " SELECT COUNT(FID) AS COUNT_DATA FROM BACKOFFICE_MENU_BAR_LV1 ";
    $count_data = $db_dpis->send_cmd($cmd);
    //$db_dpis->show_error();
    if (!$count_data) {
        if ($DPISDB == "odbc")
            $cmd = " CREATE TABLE BACKOFFICE_MENU_BAR_LV1(
				FID INTEGER NULL,
				LANGCODE CHAR(2) NOT NULL,
				MENU_ID INTEGER NOT NULL,
				MENU_ORDER INTEGER NULL,
				MENU_ICON VARCHAR(255) NULL,
				MENU_LABEL VARCHAR(255) NULL,
				FLAG_SHOW CHAR(1) NULL,
				TYPE_LINKTO CHAR(1) NULL,
				LINKTO_WEB VARCHAR(255) NULL,
				LINKTO_TID INTEGER NULL,
				PARENT_ID_LV0 INTEGER NULL,
				CREATE_DATE VARCHAR(19) NULL,
				CREATE_BY INTEGER NULL,
				UPDATE_DATE VARCHAR(19) NULL,
				UPDATE_BY INTEGER NULL,
				CONSTRAINT PK_BACKOFFICE_MENU_BAR_LV1 PRIMARY KEY (LANGCODE, MENU_ID)) ";
        elseif ($DPISDB == "oci8")
            $cmd = " CREATE TABLE BACKOFFICE_MENU_BAR_LV1(
				FID NUMBER(11) NULL,
				LANGCODE CHAR(2) NOT NULL,
				MENU_ID NUMBER(11) NOT NULL,
				MENU_ORDER NUMBER(11) NULL,
				MENU_ICON VARCHAR2(255) NULL,
				MENU_LABEL VARCHAR2(255) NULL,
				FLAG_SHOW CHAR(1) NULL,
				TYPE_LINKTO CHAR(1) NULL,
				LINKTO_WEB VARCHAR2(255) NULL,
				LINKTO_TID NUMBER(11) NULL,
				PARENT_ID_LV0 NUMBER(11) NULL,
				CREATE_DATE VARCHAR2(19) NULL,
				CREATE_BY NUMBER(11) NULL,
				UPDATE_DATE VARCHAR2(19) NULL,
				UPDATE_BY NUMBER(11) NULL,
				CONSTRAINT PK_BACKOFFICE_MENU_BAR_LV1 PRIMARY KEY (LANGCODE, MENU_ID)) ";
        $db_dpis->send_cmd($cmd);
        //$db_dpis->show_error();
    }

    $cmd = " delete from BACKOFFICE_MENU_BAR_LV1 ";
    $db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();

    $cmd = " select FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_ICON, MENU_LABEL, FLAG_SHOW,
							TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY
						  from BACKOFFICE_MENU_BAR_LV1 order by	MENU_ID, LANGCODE, MENU_ORDER ";
    $db->send_cmd($cmd);
    while ($data = $db->get_array()) {
        $data = array_change_key_case($data, CASE_LOWER);
        $FID = $data[fid];
        $LANGCODE = $data[langcode];
        $MENU_ID = $data[menu_id];
        $MENU_ORDER = $data[menu_order];
        $MENU_ICON = $data[menu_icon];
        $MENU_LABEL = $data[menu_label];
        $FLAG_SHOW = $data[flag_show];
        $TYPE_LINKTO = $data[type_linkto];
        $LINKTO_WEB = $data[linkto_web];
        $LINKTO_TID = $data[linkto_tid];
        $PARENT_ID_LV0 = $data[parent_id_lv0];
        $CREATE_DATE = $data[create_date];
        $CREATE_BY = $data[create_by];
        $UPDATE_DATE = $data[update_date];
        $UPDATE_BY = $data[update_by];
        if (!$CREATE_BY)
            $CREATE_BY = 1;
        if (!$UPDATE_BY)
            $UPDATE_BY = 1;
        $cmd = " insert into BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER,
							  MENU_ICON, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID,
							  PARENT_ID_LV0, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  values ($FID, '$LANGCODE', $MENU_ID, $MENU_ORDER, '$MENU_ICON', '$MENU_LABEL',
							  '$FLAG_SHOW', '$TYPE_LINKTO', '$LINKTO_WEB', $LINKTO_TID, $PARENT_ID_LV0,
							 '$CREATE_DATE', $CREATE_BY, '$UPDATE_DATE', $UPDATE_BY) ";
        $db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
    } // end while

    write_errtofile($path_data_err, " SUCCESS BACKOFFICE_MENU_BAR_LV1 \n " );

    $cmd = " SELECT COUNT(FID) AS COUNT_DATA FROM BACKOFFICE_MENU_BAR_LV2 ";
    $count_data = $db_dpis->send_cmd($cmd);
    //$db_dpis->show_error();
    if (!$count_data) {
        if ($DPISDB == "odbc")
            $cmd = " CREATE TABLE BACKOFFICE_MENU_BAR_LV2(
				FID INTEGER NULL,
				LANGCODE CHAR(2) NOT NULL,
				MENU_ID INTEGER NOT NULL,
				MENU_ORDER INTEGER NULL,
				MENU_ICON VARCHAR(255) NULL,
				MENU_LABEL VARCHAR(255) NULL,
				FLAG_SHOW CHAR(1) NULL,
				TYPE_LINKTO CHAR(1) NULL,
				LINKTO_WEB VARCHAR(255) NULL,
				LINKTO_TID INTEGER NULL,
				PARENT_ID_LV0 INTEGER NULL,
				PARENT_ID_LV1 INTEGER NULL,
				CREATE_DATE VARCHAR(19) NULL,
				CREATE_BY INTEGER NULL,
				UPDATE_DATE VARCHAR(19) NULL,
				UPDATE_BY INTEGER NULL,
				CONSTRAINT PK_BACKOFFICE_MENU_BAR_LV2 PRIMARY KEY (LANGCODE, MENU_ID)) ";
        elseif ($DPISDB == "oci8")
            $cmd = " CREATE TABLE BACKOFFICE_MENU_BAR_LV2(
				FID NUMBER(11) NULL,
				LANGCODE CHAR(2) NOT NULL,
				MENU_ID NUMBER(11) NOT NULL,
				MENU_ORDER NUMBER(11) NULL,
				MENU_ICON VARCHAR2(255) NULL,
				MENU_LABEL VARCHAR2(255) NULL,
				FLAG_SHOW CHAR(1) NULL,
				TYPE_LINKTO CHAR(1) NULL,
				LINKTO_WEB VARCHAR2(255) NULL,
				LINKTO_TID NUMBER(11) NULL,
				PARENT_ID_LV0 NUMBER(11) NULL,
				PARENT_ID_LV1 NUMBER(11) NULL,
				CREATE_DATE VARCHAR2(19) NULL,
				CREATE_BY NUMBER(11) NULL,
				UPDATE_DATE VARCHAR2(19) NULL,
				UPDATE_BY NUMBER(11) NULL,
				CONSTRAINT PK_BACKOFFICE_MENU_BAR_LV2 PRIMARY KEY (LANGCODE, MENU_ID)) ";
        $db_dpis->send_cmd($cmd);
        //$db_dpis->show_error();
    }

    $cmd = " delete from BACKOFFICE_MENU_BAR_LV2 ";
    $db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();

    $cmd = " select FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_ICON, MENU_LABEL, FLAG_SHOW,
							TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY
						  from BACKOFFICE_MENU_BAR_LV2 order by	MENU_ID, LANGCODE, MENU_ORDER ";
    $db->send_cmd($cmd);
    while ($data = $db->get_array()) {
        $data = array_change_key_case($data, CASE_LOWER);
        $FID = $data[fid];
        $LANGCODE = $data[langcode];
        $MENU_ID = $data[menu_id];
        $MENU_ORDER = $data[menu_order];
        $MENU_ICON = $data[menu_icon];
        $MENU_LABEL = $data[menu_label];
        $FLAG_SHOW = $data[flag_show];
        $TYPE_LINKTO = $data[type_linkto];
        $LINKTO_WEB = $data[linkto_web];
        $LINKTO_TID = $data[linkto_tid];
        $PARENT_ID_LV0 = $data[parent_id_lv0];
        $PARENT_ID_LV1 = $data[parent_id_lv1];
        $CREATE_DATE = $data[create_date];
        $CREATE_BY = $data[create_by];
        $UPDATE_DATE = $data[update_date];
        $UPDATE_BY = $data[update_by];
        if (!$CREATE_BY)
            $CREATE_BY = 1;
        if (!$UPDATE_BY)
            $UPDATE_BY = 1;
        $cmd = " insert into BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER,
							  MENU_ICON, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID,
							  PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  values ($FID, '$LANGCODE', $MENU_ID, $MENU_ORDER, '$MENU_ICON', '$MENU_LABEL',
							  '$FLAG_SHOW', '$TYPE_LINKTO', '$LINKTO_WEB', $LINKTO_TID, $PARENT_ID_LV0, $PARENT_ID_LV1,
							 '$CREATE_DATE', $CREATE_BY, '$UPDATE_DATE', $UPDATE_BY) ";
        $db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
    } // end while

    write_errtofile($path_data_err, " SUCCESS BACKOFFICE_MENU_BAR_LV2 \n ");

    $cmd = " SELECT COUNT(FID) AS COUNT_DATA FROM BACKOFFICE_MENU_BAR_LV3 ";
    $count_data = $db_dpis->send_cmd($cmd);
    //$db_dpis->show_error();
    if (!$count_data) {
        if ($DPISDB == "odbc")
            $cmd = " CREATE TABLE BACKOFFICE_MENU_BAR_LV3(
				FID INTEGER NULL,
				LANGCODE CHAR(2) NOT NULL,
				MENU_ID INTEGER NOT NULL,
				MENU_ORDER INTEGER NULL,
				MENU_ICON VARCHAR(255) NULL,
				MENU_LABEL VARCHAR(255) NULL,
				FLAG_SHOW CHAR(1) NULL,
				TYPE_LINKTO CHAR(1) NULL,
				LINKTO_WEB VARCHAR(255) NULL,
				LINKTO_TID INTEGER NULL,
				PARENT_ID_LV0 INTEGER NULL,
				PARENT_ID_LV1 INTEGER NULL,
				PARENT_ID_LV2 INTEGER NULL,
				CREATE_DATE VARCHAR(19) NULL,
				CREATE_BY INTEGER NULL,
				UPDATE_DATE VARCHAR(19) NULL,
				UPDATE_BY INTEGER NULL,
				CONSTRAINT PK_BACKOFFICE_MENU_BAR_LV3 PRIMARY KEY (LANGCODE, MENU_ID)) ";
        elseif ($DPISDB == "oci8")
            $cmd = " CREATE TABLE BACKOFFICE_MENU_BAR_LV3(
				FID NUMBER(11) NULL,
				LANGCODE CHAR(2) NOT NULL,
				MENU_ID NUMBER(11) NOT NULL,
				MENU_ORDER NUMBER(11) NULL,
				MENU_ICON VARCHAR2(255) NULL,
				MENU_LABEL VARCHAR2(255) NULL,
				FLAG_SHOW CHAR(1) NULL,
				TYPE_LINKTO CHAR(1) NULL,
				LINKTO_WEB VARCHAR2(255) NULL,
				LINKTO_TID NUMBER(11) NULL,
				PARENT_ID_LV0 NUMBER(11) NULL,
				PARENT_ID_LV1 NUMBER(11) NULL,
				PARENT_ID_LV2 NUMBER(11) NULL,
				CREATE_DATE VARCHAR2(19) NULL,
				CREATE_BY NUMBER(11) NULL,
				UPDATE_DATE VARCHAR2(19) NULL,
				UPDATE_BY NUMBER(11) NULL,
				CONSTRAINT PK_BACKOFFICE_MENU_BAR_LV3 PRIMARY KEY (LANGCODE, MENU_ID)) ";
        $db_dpis->send_cmd($cmd);
        //$db_dpis->show_error();
    }

    $cmd = " delete from BACKOFFICE_MENU_BAR_LV3 ";
    $db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();

    $cmd = " select FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_ICON, MENU_LABEL, FLAG_SHOW,
							TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, PARENT_ID_LV2,
							CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY
						  from BACKOFFICE_MENU_BAR_LV3 order by	MENU_ID, LANGCODE, MENU_ORDER ";
    $db->send_cmd($cmd);
    while ($data = $db->get_array()) {
        $data = array_change_key_case($data, CASE_LOWER);
        $FID = $data[fid];
        $LANGCODE = $data[langcode];
        $MENU_ID = $data[menu_id];
        $MENU_ORDER = $data[menu_order];
        $MENU_ICON = $data[menu_icon];
        $MENU_LABEL = $data[menu_label];
        $FLAG_SHOW = $data[flag_show];
        $TYPE_LINKTO = $data[type_linkto];
        $LINKTO_WEB = $data[linkto_web];
        $LINKTO_TID = $data[linkto_tid];
        $PARENT_ID_LV0 = $data[parent_id_lv0];
        $PARENT_ID_LV1 = $data[parent_id_lv1];
        $PARENT_ID_LV2 = $data[parent_id_lv2];
        $CREATE_DATE = $data[create_date];
        $CREATE_BY = $data[create_by];
        $UPDATE_DATE = $data[update_date];
        $UPDATE_BY = $data[update_by];
        if (!$CREATE_BY)
            $CREATE_BY = 1;
        if (!$UPDATE_BY)
            $UPDATE_BY = 1;
        $cmd = " insert into BACKOFFICE_MENU_BAR_LV3 (FID, LANGCODE, MENU_ID, MENU_ORDER,
							  MENU_ICON, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID,
							  PARENT_ID_LV0, PARENT_ID_LV1, PARENT_ID_LV2, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  values ($FID, '$LANGCODE', $MENU_ID, $MENU_ORDER, '$MENU_ICON', '$MENU_LABEL',
							  '$FLAG_SHOW', '$TYPE_LINKTO', '$LINKTO_WEB', $LINKTO_TID, $PARENT_ID_LV0, $PARENT_ID_LV1,
							 $PARENT_ID_LV2, '$CREATE_DATE', $CREATE_BY, '$UPDATE_DATE', $UPDATE_BY) ";
        $db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
    } // end while

    write_errtofile($path_data_err, " SUCCESS BACKOFFICE_MENU_BAR_LV3 \n ");

    $cmd = " SELECT COUNT(ID) AS COUNT_DATA FROM EDITOR_ATTACHMENT ";
    $count_data = $db_dpis->send_cmd($cmd);
    //$db_dpis->show_error();
    if (!$count_data) {
        if ($DPISDB == "odbc")
            $cmd = " CREATE TABLE EDITOR_ATTACHMENT(
				ID INTEGER NOT NULL,
				REAL_FILENAME VARCHAR(255) NULL,
				FILE_TYPE VARCHAR(255) NULL,
				DESCRIPTION VARCHAR(50) NULL,
				SHOW_FILENAME MEMO NULL,
				FILE_SIZE VARCHAR(20) NULL,
				USER_ID INTEGER NULL,
				USER_GROUP_ID INTEGER NULL,
				MENU_ID_LV0 INTEGER NULL,
				MENU_ID_LV1 INTEGER NULL,
				MENU_ID_LV2 INTEGER NULL,
				MENU_ID_LV3 INTEGER NULL,
				CREATE_DATE VARCHAR(19) NULL,
				CREATE_BY INTEGER NULL,
				UPDATE_DATE VARCHAR(19) NULL,
				UPDATE_BY INTEGER NULL,
				CONSTRAINT PK_EDITOR_ATTACHMENT PRIMARY KEY (ID)) ";
        elseif ($DPISDB == "oci8")
            $cmd = " CREATE TABLE EDITOR_ATTACHMENT(
				ID NUMBER(11) NOT NULL,
				REAL_FILENAME VARCHAR2(255) NULL,
				SHOW_FILENAME VARCHAR2(255) NULL,
				FILE_TYPE VARCHAR2(50) NULL,
				DESCRIPTION VARCHAR2(2000) NULL,
				FILE_SIZE VARCHAR2(20) NULL,
				USER_ID NUMBER(11) NULL,
				USER_GROUP_ID NUMBER(11) NULL,
				MENU_ID_LV0 NUMBER(11) NULL,
				MENU_ID_LV1 NUMBER(11) NULL,
				MENU_ID_LV2 NUMBER(11) NULL,
				MENU_ID_LV3 NUMBER(11) NULL,
				CREATE_DATE VARCHAR2(19) NULL,
				CREATE_BY NUMBER(11) NULL,
				UPDATE_DATE VARCHAR2(19) NULL,
				UPDATE_BY NUMBER(11) NULL,
				CONSTRAINT PK_EDITOR_ATTACHMENT PRIMARY KEY (ID)) ";
        $db_dpis->send_cmd($cmd);
        //$db_dpis->show_error();
    }

    $cmd = " delete from EDITOR_ATTACHMENT ";
    $db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();

    $cmd = " select a.ID, REAL_FILENAME, SHOW_FILENAME, FILE_TYPE, DESCRIPTION, SIZE, USER_ID,
							USER_GROUP_ID, MENU_ID_LV0, MENU_ID_LV1, MENU_ID_LV2, MENU_ID_LV3,
							a.CREATE_DATE, a.UPDATE_DATE, b.ID as UPDATE_BY
						  from (
										EDITOR_ATTACHMENT a
										left join USER_DETAIL b on (a.UPDATE_BY=b.USERNAME)	)
						  order by a.ID ";
    $db->send_cmd($cmd);
    while ($data = $db->get_array()) {
        $data = array_change_key_case($data, CASE_LOWER);
        $ID = $data[id];
        $REAL_FILENAME = $data[real_filename];
        $SHOW_FILENAME = $data[show_filename];
        $FILE_TYPE = $data[file_type];
        $DESCRIPTION = $data[description];
        $FILE_SIZE = $data[size];
        $USER_ID = $data[user_id];
        $USER_GROUP_ID = $data[user_group_id];
        $MENU_ID_LV0 = $data[menu_id_lv0];
        $MENU_ID_LV1 = $data[menu_id_lv1];
        $MENU_ID_LV2 = $data[menu_id_lv2];
        $MENU_ID_LV3 = $data[menu_id_lv3];
        $CREATE_DATE = $data[create_date];
        $UPDATE_DATE = $data[update_date];
        $UPDATE_BY = $data[update_by];
        if (!$MENU_ID_LV0)
            $MENU_ID_LV0 = "NULL";
        if (!$MENU_ID_LV1)
            $MENU_ID_LV1 = "NULL";
        if (!$MENU_ID_LV2)
            $MENU_ID_LV2 = "NULL";
        if (!$MENU_ID_LV3)
            $MENU_ID_LV3 = "NULL";
        if (!$CREATE_BY)
            $CREATE_BY = 1;
        if (!$UPDATE_BY)
            $UPDATE_BY = 1;
        $cmd = " insert into EDITOR_ATTACHMENT (ID, REAL_FILENAME, SHOW_FILENAME, FILE_TYPE,
							  DESCRIPTION, FILE_SIZE, USER_ID, USER_GROUP_ID, MENU_ID_LV0,
							  MENU_ID_LV1, MENU_ID_LV2, MENU_ID_LV3, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  values ($ID, '$REAL_FILENAME', '$SHOW_FILENAME', '$FILE_TYPE', '$DESCRIPTION',
							  '$FILE_SIZE', $USER_ID, $USER_GROUP_ID, $MENU_ID_LV0, $MENU_ID_LV1,
							 $MENU_ID_LV2, $MENU_ID_LV3, '$CREATE_DATE', $CREATE_BY, '$UPDATE_DATE', $UPDATE_BY) ";
        $db_dpis2->send_cmd($cmd);
        //$db_dpis2->show_error();
    } // end while

    write_errtofile($path_data_err, " SUCCESS EDITOR_ATTACHMENT \n ");

    $cmd = " SELECT COUNT(ID) AS COUNT_DATA FROM EDITOR_IMAGE ";
    $count_data = $db_dpis->send_cmd($cmd);
    //$db_dpis->show_error();
    if (!$count_data) {
        if ($DPISDB == "odbc")
            $cmd = " CREATE TABLE EDITOR_IMAGE(
				ID INTEGER NOT NULL,
				REAL_FILENAME VARCHAR(255) NULL,
				FILE_TYPE VARCHAR(255) NULL,
				DESCRIPTION VARCHAR(50) NULL,
				SHOW_FILENAME MEMO NULL,
				WIDTH NUMBER NULL,
				HEIGHT NUMBER NULL,
				FILE_SIZE VARCHAR(20) NULL,
				USER_ID INTEGER NULL,
				USER_GROUP_ID INTEGER NULL,
				MENU_ID_LV0 INTEGER NULL,
				MENU_ID_LV1 INTEGER NULL,
				MENU_ID_LV2 INTEGER NULL,
				MENU_ID_LV3 INTEGER NULL,
				CREATE_DATE VARCHAR(19) NULL,
				CREATE_BY INTEGER NULL,
				UPDATE_DATE VARCHAR(19) NULL,
				UPDATE_BY INTEGER NULL,
				CONSTRAINT PK_EDITOR_IMAGE PRIMARY KEY (ID)) ";
        elseif ($DPISDB == "oci8")
            $cmd = " CREATE TABLE EDITOR_IMAGE(
				ID NUMBER(11) NOT NULL,
				REAL_FILENAME VARCHAR2(255) NULL,
				SHOW_FILENAME VARCHAR2(255) NULL,
				FILE_TYPE VARCHAR2(50) NULL,
				DESCRIPTION VARCHAR2(2000) NULL,
				WIDTH NUMBER(16,2) NULL,
				HEIGHT NUMBER(16,2) NULL,
				FILE_SIZE VARCHAR2(20) NULL,
				USER_ID NUMBER(11) NULL,
				USER_GROUP_ID NUMBER(11) NULL,
				MENU_ID_LV0 NUMBER(11) NULL,
				MENU_ID_LV1 NUMBER(11) NULL,
				MENU_ID_LV2 NUMBER(11) NULL,
				MENU_ID_LV3 NUMBER(11) NULL,
				CREATE_DATE VARCHAR2(19) NULL,
				CREATE_BY NUMBER(11) NULL,
				UPDATE_DATE VARCHAR2(19) NULL,
				UPDATE_BY NUMBER(11) NULL,
				CONSTRAINT PK_EDITOR_IMAGE PRIMARY KEY (ID)) ";
        $db_dpis->send_cmd($cmd);
        //$db_dpis->show_error();
    }

    $cmd = " delete from EDITOR_IMAGE ";
    $db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();

    $cmd = " select a.ID, REAL_FILENAME, SHOW_FILENAME, FILE_TYPE, DESCRIPTION, WIDTH, HEIGHT,
							SIZE, USER_ID, USER_GROUP_ID, MENU_ID_LV0, MENU_ID_LV1, MENU_ID_LV2, MENU_ID_LV3,
							a.CREATE_DATE, a.UPDATE_DATE, b.ID as UPDATE_BY
						  from (
										EDITOR_IMAGE a
										left join USER_DETAIL b on (a.UPDATE_BY=b.USERNAME)	)
						  order by a.ID ";
    $db->send_cmd($cmd);
    while ($data = $db->get_array()) {
        $data = array_change_key_case($data, CASE_LOWER);
        $ID = $data[id];
        $REAL_FILENAME = $data[real_filename];
        $SHOW_FILENAME = $data[show_filename];
        $FILE_TYPE = $data[file_type];
        $DESCRIPTION = $data[description];
        $WIDTH = $data[width];
        $HEIGHT = $data[height];
        $FILE_SIZE = $data[size];
        $USER_ID = $data[user_id];
        $USER_GROUP_ID = $data[user_group_id];
        $MENU_ID_LV0 = $data[menu_id_lv0];
        $MENU_ID_LV1 = $data[menu_id_lv1];
        $MENU_ID_LV2 = $data[menu_id_lv2];
        $MENU_ID_LV3 = $data[menu_id_lv3];
        $CREATE_DATE = $data[create_date];
        $UPDATE_DATE = $data[update_date];
        $UPDATE_BY = $data[update_by];
        if (!$MENU_ID_LV0)
            $MENU_ID_LV0 = "NULL";
        if (!$MENU_ID_LV1)
            $MENU_ID_LV1 = "NULL";
        if (!$MENU_ID_LV2)
            $MENU_ID_LV2 = "NULL";
        if (!$MENU_ID_LV3)
            $MENU_ID_LV3 = "NULL";
        if (!$CREATE_BY)
            $CREATE_BY = 1;
        if (!$UPDATE_BY)
            $UPDATE_BY = 1;
        $cmd = " insert into EDITOR_IMAGE (ID, REAL_FILENAME, SHOW_FILENAME, FILE_TYPE,
							  DESCRIPTION, WIDTH, HEIGHT, FILE_SIZE, USER_ID, USER_GROUP_ID, MENU_ID_LV0,
							  MENU_ID_LV1, MENU_ID_LV2, MENU_ID_LV3, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  values ($ID, '$REAL_FILENAME', '$SHOW_FILENAME', '$FILE_TYPE', '$DESCRIPTION', $WIDTH,
							  $HEIGHT, '$FILE_SIZE', $USER_ID, $USER_GROUP_ID, $MENU_ID_LV0, $MENU_ID_LV1,
							 $MENU_ID_LV2, $MENU_ID_LV3, '$CREATE_DATE', $CREATE_BY, '$UPDATE_DATE', $UPDATE_BY) ";
        $db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
    } // end while

    write_errtofile($path_data_err, " SUCCESS EDITOR_IMAGE \n ");

    $cmd = " SELECT COUNT(SITE_ID) AS COUNT_DATA FROM SITE_INFO ";
    $count_data = $db_dpis->send_cmd($cmd);
    //	$db_dpis->show_error();
    if (!$count_data) {
        if ($DPISDB == "odbc")
            $cmd = " CREATE TABLE SITE_INFO(
				SITE_ID VARCHAR(10) NOT NULL,
				ORG_ID VARCHAR(10) NULL,
				DPISDB VARCHAR(50) NULL,
				DPISDB_HOST VARCHAR(50) NULL,
				DPISDB_NAME VARCHAR(50) NULL,
				DPISDB_USER VARCHAR(50) NULL,
				DPISDB_PWD VARCHAR(50) NULL,
				SITE_NAME VARCHAR(100) NULL,
				SITE_BG_LEFT VARCHAR(100) NULL,
				SITE_BG_LEFT_X INTEGER2 NULL,
				SITE_BG_LEFT_Y INTEGER2 NULL,
				SITE_BG_LEFT_W INTEGER2 NULL,
				SITE_BG_LEFT_H INTEGER2 NULL,
				SITE_BG_LEFT_ALPHA NUMBER NULL,
				SITE_BG VARCHAR(100) NULL,
				SITE_BG_X INTEGER2 NULL,
				SITE_BG_Y INTEGER2 NULL,
				SITE_BG_W INTEGER2 NULL,
				SITE_BG_H INTEGER2 NULL,
				SITE_BG_ALPHA NUMBER NULL,
				SITE_BG_RIGHT VARCHAR(100) NULL,
				SITE_BG_RIGHT_X INTEGER2 NULL,
				SITE_BG_RIGHT_Y INTEGER2 NULL,
				SITE_BG_RIGHT_W INTEGER2 NULL,
				SITE_BG_RIGHT_H INTEGER2 NULL,
				SITE_BG_RIGHT_ALPHA NUMBER NULL,
				HEAD_FONT_NAME VARCHAR(100) NULL,
				HEAD_FONT_SIZE INTEGER2 NULL,
				CSS_NAME VARCHAR(100) NULL,
				SITE_LEVEL SINGLE NULL,
				PV_CODE VARCHAR(10) NULL,
				HEAD_HEIGHT INTEGER2 NULL,
				UPDATE_USER INTEGER2 NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,
				CONSTRAINT PK_SITE_INFO PRIMARY KEY (SITE_ID)) ";
        elseif ($DPISDB == "oci8")
            $cmd = " CREATE TABLE SITE_INFO(
				SITE_ID VARCHAR2(10) NOT NULL,
				ORG_ID VARCHAR2(10) NULL,
				DPISDB VARCHAR2(50) NULL,
				DPISDB_HOST VARCHAR2(50) NULL,
				DPISDB_NAME VARCHAR2(50) NULL,
				DPISDB_USER VARCHAR2(50) NULL,
				DPISDB_PWD VARCHAR2(50) NULL,
				SITE_NAME VARCHAR2(100) NULL,
				SITE_BG_LEFT VARCHAR2(100) NULL,
				SITE_BG_LEFT_X NUMBER(5) NULL,
				SITE_BG_LEFT_Y NUMBER(5) NULL,
				SITE_BG_LEFT_W NUMBER(5) NULL,
				SITE_BG_LEFT_H NUMBER(5) NULL,
				SITE_BG_LEFT_ALPHA NUMBER(4,2) NULL,
				SITE_BG VARCHAR2(100) NULL,
				SITE_BG_X NUMBER(5) NULL,
				SITE_BG_Y NUMBER(5) NULL,
				SITE_BG_W NUMBER(5) NULL,
				SITE_BG_H NUMBER(5) NULL,
				SITE_BG_ALPHA NUMBER(4,2) NULL,
				SITE_BG_RIGHT VARCHAR2(100) NULL,
				SITE_BG_RIGHT_X NUMBER(5) NULL,
				SITE_BG_RIGHT_Y NUMBER(5) NULL,
				SITE_BG_RIGHT_W NUMBER(5) NULL,
				SITE_BG_RIGHT_H NUMBER(5) NULL,
				SITE_BG_RIGHT_ALPHA NUMBER(4,2) NULL,
				HEAD_FONT_NAME VARCHAR2(100) NULL,
				HEAD_FONT_SIZE NUMBER(5) NULL,
				CSS_NAME VARCHAR2(100) NULL,
				SITE_LEVEL NUMBER(1) NULL,
				PV_CODE VARCHAR2(10) NULL,
				HEAD_HEIGHT NUMBER(5) NULL,
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,
				CONSTRAINT PK_SITE_INFO PRIMARY KEY (SITE_ID)) ";
        $db_dpis->send_cmd($cmd);
        //$db_dpis->show_error();
    }

    $cmd = " delete from SITE_INFO ";
    $db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();

    $cmd = " select SITE_ID, ORG_ID, DPISDB, DPISDB_HOST, DPISDB_NAME, DPISDB_USER, DPISDB_PWD,
						SITE_NAME, SITE_BG_LEFT, SITE_BG_LEFT_X, SITE_BG_LEFT_Y, SITE_BG_LEFT_W, SITE_BG_LEFT_H,
						SITE_BG_LEFT_ALPHA, SITE_BG, SITE_BG_X, SITE_BG_Y, SITE_BG_W, SITE_BG_H, SITE_BG_ALPHA,
						SITE_BG_RIGHT, SITE_BG_RIGHT_X, SITE_BG_RIGHT_Y, SITE_BG_RIGHT_W, SITE_BG_RIGHT_H,
						SITE_BG_RIGHT_ALPHA, HEAD_FONT_NAME, HEAD_FONT_SIZE, CSS_NAME, SITE_LEVEL, PV_CODE,
						HEAD_HEIGHT, UPDATE_USER, UPDATE_DATE
						  from SITE_INFO order by SITE_ID ";
    $db->send_cmd($cmd);
    while ($data = $db->get_array()) {
        $data = array_change_key_case($data, CASE_LOWER);
        $SITE_ID = $data[site_id];
        $ORG_ID = $data[org_id];
        $TMP_DPISDB = $data[dpisdb];
        $TMP_DPISDB_HOST = $data[dpisdb_host];
        $TMP_DPISDB_NAME = $data[dpisdb_name];
        $TMP_DPISDB_USER = $data[dpisdb_user];
        $TMP_DPISDB_PWD = $data[dpisdb_pwd];
        $SITE_NAME = $data[site_name];
        $SITE_BG_LEFT = $data[site_bg_left];
        $SITE_BG_LEFT_X = $data[site_bg_left_x];
        $SITE_BG_LEFT_Y = $data[site_bg_left_y];
        $SITE_BG_LEFT_W = $data[site_bg_left_w];
        $SITE_BG_LEFT_H = $data[site_bg_left_h];
        $SITE_BG_LEFT_ALPHA = $data[site_bg_left_alpha];
        $SITE_BG = $data[site_bg_left];
        $SITE_BG_X = $data[site_bg_x];
        $SITE_BG_Y = $data[site_bg_y];
        $SITE_BG_W = $data[site_bg_w];
        $SITE_BG_H = $data[site_bg_h];
        $SITE_BG_ALPHA = $data[site_bg_alpha];
        $SITE_BG_RIGHT = $data[site_bg_right];
        $SITE_BG_RIGHT_X = $data[site_bg_right_x];
        $SITE_BG_RIGHT_Y = $data[site_bg_right_y];
        $SITE_BG_RIGHT_W = $data[site_bg_right_w];
        $SITE_BG_RIGHT_H = $data[site_bg_right_h];
        $SITE_BG_RIGHT_ALPHA = $data[site_bg_right_alpha];
        $HEAD_FONT_NAME = $data[head_font_name];
        $HEAD_FONT_SIZE = $data[head_font_size];
        $CSS_NAME = $data[css_name];
        $SITE_LEVEL = $data[site_level];
        $PV_CODE = $data[pv_code];
        $HEAD_HEIGHT = $data[head_height];
        $UPDATE_USER = $data[update_user];
        $UPDATE_DATE = $data[update_date];
        if (!$SITE_BG_LEFT_X)
            $SITE_BG_LEFT_X = "NULL";
        if (!$SITE_BG_LEFT_Y)
            $SITE_BG_LEFT_Y = "NULL";
        if (!$SITE_BG_LEFT_W)
            $SITE_BG_LEFT_W = "NULL";
        if (!$SITE_BG_LEFT_H)
            $SITE_BG_LEFT_H = "NULL";
        if (!$SITE_BG_LEFT_ALPHA)
            $SITE_BG_LEFT_ALPHA = "NULL";
        if (!$SITE_BG_X)
            $SITE_BG_X = "NULL";
        if (!$SITE_BG_Y)
            $SITE_BG_Y = "NULL";
        if (!$SITE_BG_W)
            $SITE_BG_W = "NULL";
        if (!$SITE_BG_H)
            $SITE_BG_H = "NULL";
        if (!$SITE_BG_ALPHA)
            $SITE_BG_ALPHA = "NULL";
        if (!$SITE_BG_RIGHT_X)
            $SITE_BG_RIGHT_X = "NULL";
        if (!$SITE_BG_RIGHT_Y)
            $SITE_BG_RIGHT_Y = "NULL";
        if (!$SITE_BG_RIGHT_W)
            $SITE_BG_RIGHT_W = "NULL";
        if (!$SITE_BG_RIGHT_H)
            $SITE_BG_RIGHT_H = "NULL";
        if (!$SITE_BG_RIGHT_ALPHA)
            $SITE_BG_RIGHT_ALPHA = "NULL";
        if (!$HEAD_FONT_SIZE)
            $HEAD_FONT_SIZE = "NULL";
        if (!$SITE_LEVEL)
            $SITE_LEVEL = "NULL";
        if (!$HEAD_HEIGHT)
            $HEAD_HEIGHT = "NULL";
        if (!$UPDATE_USER)
            $UPDATE_USER = 1;
        $cmd = " insert into SITE_INFO (SITE_ID, ORG_ID, DPISDB, DPISDB_HOST, DPISDB_NAME, DPISDB_USER, DPISDB_PWD,
						SITE_NAME, SITE_BG_LEFT, SITE_BG_LEFT_X, SITE_BG_LEFT_Y, SITE_BG_LEFT_W, SITE_BG_LEFT_H,
						SITE_BG_LEFT_ALPHA, SITE_BG, SITE_BG_X, SITE_BG_Y, SITE_BG_W, SITE_BG_H, SITE_BG_ALPHA,
						SITE_BG_RIGHT, SITE_BG_RIGHT_X, SITE_BG_RIGHT_Y, SITE_BG_RIGHT_W, SITE_BG_RIGHT_H,
						SITE_BG_RIGHT_ALPHA, HEAD_FONT_NAME, HEAD_FONT_SIZE, CSS_NAME, SITE_LEVEL, PV_CODE,
						HEAD_HEIGHT, UPDATE_USER, UPDATE_DATE)
						values ('$SITE_ID', '$ORG_ID', '$TMP_DPISDB', '$TMP_DPISDB_HOST', '$TMP_DPISDB_NAME', '$TMP_DPISDB_USER', '$TMP_DPISDB_PWD',
						'$SITE_NAME', '$SITE_BG_LEFT', $SITE_BG_LEFT_X, $SITE_BG_LEFT_Y, $SITE_BG_LEFT_W, $SITE_BG_LEFT_H,
						$SITE_BG_LEFT_ALPHA, '$SITE_BG', $SITE_BG_X, $SITE_BG_Y, $SITE_BG_W, $SITE_BG_H, $SITE_BG_ALPHA,
						'$SITE_BG_RIGHT', $SITE_BG_RIGHT_X, $SITE_BG_RIGHT_Y, $SITE_BG_RIGHT_W, $SITE_BG_RIGHT_H,
						$SITE_BG_RIGHT_ALPHA, '$HEAD_FONT_NAME', $HEAD_FONT_SIZE, '$CSS_NAME', $SITE_LEVEL, '$PV_CODE',
						$HEAD_HEIGHT, $UPDATE_USER, '$UPDATE_DATE') ";
        $db_dpis2->send_cmd($cmd);
        //$db_dpis2->show_error();
    } // end while

    write_errtofile($path_data_err, " SUCCESS SITE_INFO \n ");

    $cmd = " SELECT COUNT(CONFIG_ID) AS COUNT_DATA FROM SYSTEM_CONFIG ";
    $count_data = $db_dpis->send_cmd($cmd);
    //$db_dpis->show_error();
    if (!$count_data) {
        if ($DPISDB == "odbc")
            $cmd = " CREATE TABLE SYSTEM_CONFIG(
				CONFIG_ID INTEGER NOT NULL,
				CONFIG_NAME VARCHAR(255) NULL,
				CONFIG_VALUE MEMO NULL,
				CONFIG_REMARK VARCHAR(255) NULL,
				CONSTRAINT PK_SYSTEM_CONFIG PRIMARY KEY (CONFIG_ID)) ";
        elseif ($DPISDB == "oci8")
            $cmd = " CREATE TABLE SYSTEM_CONFIG(
				CONFIG_ID NUMBER(11) NOT NULL,
				CONFIG_NAME VARCHAR2(255) NULL,
				CONFIG_VALUE VARCHAR2(2000) NULL,
				CONFIG_REMARK VARCHAR2(255) NULL,
				CONSTRAINT PK_SYSTEM_CONFIG PRIMARY KEY (CONFIG_ID)) ";
        $db_dpis->send_cmd($cmd);
        //$db_dpis->show_error();
    }

    $cmd = " delete from SYSTEM_CONFIG ";
    $db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();

    $cmd = " select CONFIG_ID, CONFIG_NAME, CONFIG_VALUE, CONFIG_REMARK
						  from SYSTEM_CONFIG order by	CONFIG_ID ";
    $db->send_cmd($cmd);
    while ($data = $db->get_array()) {
        $data = array_change_key_case($data, CASE_LOWER);
        $CONFIG_ID = $data[config_id];
        $CONFIG_NAME = $data[config_name];
        $CONFIG_VALUE = $data[config_value];
        $CONFIG_REMARK = $data[config_remark];
        $cmd = " insert into SYSTEM_CONFIG (CONFIG_ID, CONFIG_NAME, CONFIG_VALUE, CONFIG_REMARK)
							  values ($CONFIG_ID, '$CONFIG_NAME', '$CONFIG_VALUE', '$CONFIG_REMARK') ";
        $db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
    } // end while

    write_errtofile($path_data_err, " SUCCESS SYSTEM_CONFIG \n ");

    $cmd = " SELECT COUNT(ID) AS COUNT_DATA FROM USER_DETAIL ";
    $count_data = $db_dpis->send_cmd($cmd);
    //$db_dpis->show_error();
    if (!$count_data) {
        if ($DPISDB == "odbc")
            $cmd = " CREATE TABLE USER_DETAIL(
				ID INTEGER NOT NULL,
				GROUP_ID INTEGER NULL,
				USERNAME VARCHAR(255) NULL,
				PASSWORD VARCHAR(255) NULL,
				INHERIT_GROUP VARCHAR(255) NULL,
				USER_LINK_ID INTEGER NULL,
				FULLNAME VARCHAR(255) NULL,
				ADDRESS VARCHAR(255) NULL,
				DISTRICT_ID INTEGER NULL,
				AMPHUR_ID INTEGER NULL,
				PROVINCE_ID INTEGER NULL,
				EMAIL VARCHAR(255) NULL,
				TEL VARCHAR(255) NULL,
				FAX VARCHAR(255) NULL,
				TITLENAME VARCHAR(50) NULL,
				PASSWORD_LAST_UPDATE VARCHAR(19) NULL,
				CREATE_DATE VARCHAR(19) NULL,
				CREATE_BY INTEGER NULL,
				UPDATE_DATE VARCHAR(19) NULL,
				UPDATE_BY INTEGER NULL,
                                USER_FLAG CHAR(1) DEFAULT 'Y',
				CONSTRAINT PK_USER_DETAIL PRIMARY KEY (ID)) ";
        elseif ($DPISDB == "oci8")
            $cmd = " CREATE TABLE USER_DETAIL(
				ID NUMBER(11) NOT NULL,
				GROUP_ID NUMBER(11) NULL,
				USERNAME VARCHAR2(255) NULL,
				PASSWORD VARCHAR2(255) NULL,
				INHERIT_GROUP VARCHAR2(255) NULL,
				USER_LINK_ID NUMBER(11) NULL,
				FULLNAME VARCHAR2(255) NULL,
				ADDRESS VARCHAR2(255) NULL,
				DISTRICT_ID NUMBER(11) NULL,
				AMPHUR_ID NUMBER(11) NULL,
				PROVINCE_ID NUMBER(11) NULL,
				EMAIL VARCHAR2(255) NULL,
				TEL VARCHAR2(255) NULL,
				FAX VARCHAR2(255) NULL,
				TITLENAME VARCHAR2(50) NULL,
				PASSWORD_LAST_UPDATE VARCHAR2(19) NULL,
				CREATE_DATE VARCHAR2(19) NULL,
				CREATE_BY NUMBER(11) NULL,
				UPDATE_DATE VARCHAR2(19) NULL,
				UPDATE_BY NUMBER(11) NULL,
                                USER_FLAG CHAR(1) DEFAULT 'Y',
				CONSTRAINT PK_USER_DETAIL PRIMARY KEY (ID)) ";
        $db_dpis->send_cmd($cmd);
        //$db_dpis->show_error();
    }

    $cmd = " CREATE INDEX IDX_USER_DETAIL ON USER_DETAIL (USERNAME) ";
    $db_dpis->send_cmd($cmd);
    //$db_dpis->show_error() ;

    $cmd = " delete from USER_DETAIL ";
    $db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();

    $cmd = " select ID, GROUP_ID, USERNAME, PASSWORD, INHERIT_GROUP, USER_LINK_ID, FULLNAME,
							ADDRESS, DISTRICT_ID, AMPHUR_ID, PROVINCE_ID, EMAIL, TEL, FAX, TITLENAME,
							PASSWORD_LAST_UPDATE, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY, USER_FLAG
						  from USER_DETAIL order by ID ";
    $db->send_cmd($cmd);
    while ($data = $db->get_array()) {
        $data = array_change_key_case($data, CASE_LOWER);
        $ID = $data[id];
        $GROUP_ID = $data[group_id];
        $USERNAME = $data[username];
        $PASSWORD = $data[password];
        $INHERIT_GROUP = $data[inherit_group];
        $USER_LINK_ID = $data[user_link_id];
        $FULLNAME = $data[fullname];
        $ADDRESS = $data[address];
        $DISTRICT_ID = $data[district_id];
        $AMPHUR_ID = $data[amphur_id];
        $PROVINCE_ID = $data[province_id];
        $EMAIL = $data[email];
        $TEL = $data[tel];
        $FAX = $data[fax];
        $TITLENAME = $data[titlename];
        $PASSWORD_LAST_UPDATE = $data[password_last_update];
        $CREATE_DATE = $data[create_date];
        $CREATE_BY = $data[create_by];
        $CREATE_BY = 1;
        $UPDATE_DATE = $data[update_date];
        $UPDATE_BY = $data[update_by];
        $UPDATE_BY = 1;
        $USER_FLAG = $data[user_flag];
        if (!$USER_LINK_ID) 
            $USER_LINK_ID = "NULL";
        if (!$DISTRICT_ID)
            $DISTRICT_ID = "NULL";
        if (!$AMPHUR_ID)
            $AMPHUR_ID = "NULL";
        if (!$PROVINCE_ID)
            $PROVINCE_ID = "NULL";
        $cmd = " insert into USER_DETAIL (ID, GROUP_ID, USERNAME, PASSWORD, INHERIT_GROUP, USER_LINK_ID, FULLNAME,
							ADDRESS, DISTRICT_ID, AMPHUR_ID, PROVINCE_ID, EMAIL, TEL, FAX, TITLENAME,
							PASSWORD_LAST_UPDATE, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY, USER_FLAG)
							  values ($ID, $GROUP_ID, '$USERNAME', '$PASSWORD', '$INHERIT_GROUP', $USER_LINK_ID,
							  '$FULLNAME', '$ADDRESS', $DISTRICT_ID, $AMPHUR_ID, $PROVINCE_ID, '$EMAIL', '$TEL', '$FAX',
							  '$TITLENAME', '$PASSWORD_LAST_UPDATE', '$CREATE_DATE', $CREATE_BY, '$UPDATE_DATE', $UPDATE_BY, '$USER_FLAG' ) ";
        $db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
    } // end while

    write_errtofile($path_data_err, " SUCCESS USER_DETAIL \n ");

    $cmd = " SELECT COUNT(ID) AS COUNT_DATA FROM USER_GROUP ";
    $count_data = $db_dpis->send_cmd($cmd);
    //$db_dpis->show_error();
    if (!$count_data) {
        if ($DPISDB == "odbc")
            $cmd = " CREATE TABLE USER_GROUP(
				ID INTEGER NOT NULL,
				CODE VARCHAR(10) NULL,
				NAME_TH VARCHAR(50) NULL,
				NAME_EN VARCHAR(50) NULL,
				ACCESS_LIST MEMO NULL,
				GROUP_LEVEL INTEGER NULL,
				ORG_ID INTEGER NULL,
				PV_CODE VARCHAR(50) NULL,
				DPISDB CHAR(1) NULL,
				DPISDB_HOST VARCHAR(50) NULL,
				DPISDB_NAME VARCHAR(50) NULL,
				DPISDB_USER VARCHAR(50) NULL,
				DPISDB_PWD VARCHAR(50) NULL,
				GROUP_PER_TYPE SINGLE NULL,
				GROUP_ORG_STRUCTURE SINGLE NULL,
				GROUP_ACTIVE SINGLE NULL,
				GROUP_SEQ_NO INTEGER2 NULL,
				LEVEL_NO_LIST VARCHAR(255) NULL,
				CREATE_DATE VARCHAR(19) NULL,
				CREATE_BY INTEGER NULL,
				UPDATE_DATE VARCHAR(19) NULL,
				UPDATE_BY INTEGER NULL,
				CONSTRAINT PK_USER_GROUP PRIMARY KEY (ID)) ";
        elseif ($DPISDB == "oci8")
            $cmd = " CREATE TABLE USER_GROUP(
				ID NUMBER(11) NOT NULL,
				CODE VARCHAR2(10) NULL,
				NAME_TH VARCHAR2(50) NULL,
				NAME_EN VARCHAR2(50) NULL,
				ACCESS_LIST VARCHAR2(2000) NULL,
				GROUP_LEVEL NUMBER(11) NULL,
				ORG_ID NUMBER(11) NULL,
				PV_CODE VARCHAR2(10) NULL,
				DPISDB CHAR(1) NULL,
				DPISDB_HOST VARCHAR2(50) NULL,
				DPISDB_NAME VARCHAR2(50) NULL,
				DPISDB_USER VARCHAR2(50) NULL,
				DPISDB_PWD VARCHAR2(50) NULL,
				GROUP_PER_TYPE NUMBER(1) NULL,
				GROUP_ORG_STRUCTURE NUMBER(1) NULL,
				GROUP_ACTIVE NUMBER(1) NULL,
				GROUP_SEQ_NO NUMBER(5) NULL,
				LEVEL_NO_LIST VARCHAR(255) NULL,
				CREATE_DATE VARCHAR2(19) NULL,
				CREATE_BY NUMBER(11) NULL,
				UPDATE_DATE VARCHAR2(19) NULL,
				UPDATE_BY NUMBER(11) NULL,
				CONSTRAINT PK_USER_GROUP PRIMARY KEY (ID)) ";
        $db_dpis->send_cmd($cmd);
        //$db_dpis->show_error();
    }

    $cmd = " delete from USER_GROUP ";
    $db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();

    $cmd = " select ID, CODE, NAME_TH, NAME_EN, ACCESS_LIST, GROUP_LEVEL, ORG_ID,
							PV_CODE, GROUP_PER_TYPE, GROUP_ORG_STRUCTURE, GROUP_ACTIVE, GROUP_SEQ_NO,
							LEVEL_NO_LIST, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY
						  from USER_GROUP order by ID ";
    $db->send_cmd($cmd);
    while ($data = $db->get_array()) {
        $data = array_change_key_case($data, CASE_LOWER);
        $ID = $data[id];
        $CODE = $data[code];
        $NAME_TH = $data[name_th];
        $NAME_EN = $data[name_en];
        $ACCESS_LIST = $data[access_list];
        $tmp_GROUP_LEVEL = $data[group_level];
        $tmp_ORG_ID = $data[org_id];
        $tmp_PV_CODE = $data[pv_code];
        $tmp_DPISDB = $data[dpisdb];
        $tmp_DPISDB_HOST = $data[dpisdb_host];
        $tmp_DPISDB_NAME = $data[dpisdb_name];
        $tmp_DPISDB_USER = $data[dpisdb_user];
        $tmp_DPISDB_PWD = $data[dpisdb_pwd];
        $tmp_GROUP_PER_TYPE = $data[group_per_type];
        $tmp_GROUP_ORG_STRUCTURE = $data[group_org_structure];
        $tmp_GROUP_ACTIVE = $data[group_active];
        $tmp_GROUP_SEQ_NO = $data[group_seq_no];
        $tmp_LEVEL_NO_LIST = $data[level_no_list];
        $CREATE_DATE = $data[create_date];
        $CREATE_BY = $data[create_by];
        $CREATE_BY = 1;
        $UPDATE_DATE = $data[update_date];
        $UPDATE_BY = $data[update_by];
        $UPDATE_BY = 1;
        if (!$tmp_GROUP_LEVEL)
            $tmp_GROUP_LEVEL = "NULL";
        if (!$tmp_ORG_ID)
            $tmp_ORG_ID = "NULL";
        if (!$tmp_GROUP_PER_TYPE)
            $tmp_GROUP_PER_TYPE = "NULL";
        if (!$tmp_GROUP_ORG_STRUCTURE)
            $tmp_GROUP_ORG_STRUCTURE = 2;
        if (!$tmp_GROUP_ACTIVE)
            $tmp_GROUP_ACTIVE = 1;
        if (!$tmp_GROUP_SEQ_NO)
            $tmp_GROUP_SEQ_NO = "NULL";
        $cmd = " insert into USER_GROUP (ID, CODE, NAME_TH, NAME_EN, ACCESS_LIST, GROUP_LEVEL, ORG_ID,
							PV_CODE, DPISDB, DPISDB_HOST, DPISDB_NAME, DPISDB_USER, DPISDB_PWD,
							GROUP_PER_TYPE, GROUP_ORG_STRUCTURE, GROUP_ACTIVE, GROUP_SEQ_NO,
							LEVEL_NO_LIST, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							values ($ID, '$CODE', '$NAME_TH', '$NAME_EN', '$ACCESS_LIST', $tmp_GROUP_LEVEL, $tmp_ORG_ID,
							'$tmp_PV_CODE', '$tmp_DPISDB', '$tmp_DPISDB_HOST', '$tmp_DPISDB_NAME', '$tmp_DPISDB_USER', '$tmp_DPISDB_PWD',
							$tmp_GROUP_PER_TYPE, $tmp_GROUP_ORG_STRUCTURE, $tmp_GROUP_ACTIVE, $tmp_GROUP_SEQ_NO,
							'tmp_LEVEL_NO_LIST', '$CREATE_DATE', $CREATE_BY, '$UPDATE_DATE', $UPDATE_BY) ";
        $db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
    } // end while

    write_errtofile($path_data_err, " SUCCESS USER_GROUP \n ");

    $cmd = " SELECT COUNT(LOG_ID) AS COUNT_DATA FROM USER_LAST_ACCESS ";
    $count_data = $db_dpis->send_cmd($cmd);
    //$db_dpis->show_error();
    if (!$count_data) {
        if ($DPISDB == "odbc")
            $cmd = " CREATE TABLE USER_LAST_ACCESS(
				LOG_ID INTEGER NOT NULL,
				USER_ID INTEGER NOT NULL,
				USERNAME VARCHAR(255) NULL,
				FULLNAME VARCHAR(255) NULL,
				LAST_LOGIN VARCHAR(19) NULL,
				FROM_IP VARCHAR(50) NULL,
				CONSTRAINT PK_USER_LAST_ACCESS PRIMARY KEY (LOG_ID)) ";
        elseif ($DPISDB == "oci8")
            $cmd = " CREATE TABLE USER_LAST_ACCESS(
				LOG_ID NUMBER(11) NOT NULL,
				USER_ID NUMBER(11) NOT NULL,
				USERNAME VARCHAR2(255) NULL,
				FULLNAME VARCHAR2(255) NULL,
				LAST_LOGIN VARCHAR2(19) NULL,
				FROM_IP VARCHAR2(50) NULL,
				CONSTRAINT PK_USER_LAST_ACCESS PRIMARY KEY (LOG_ID)) ";
        $db_dpis->send_cmd($cmd);
        //$db_dpis->show_error();
    }

    $cmd = " delete from USER_LAST_ACCESS ";
    $db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();

    $cmd = " select LOG_ID, USER_ID, USERNAME, FULLNAME, LAST_LOGIN, FROM_IP
						  from USER_LAST_ACCESS order by LOG_ID ";
    $db->send_cmd($cmd);
    while ($data = $db->get_array()) {
        $data = array_change_key_case($data, CASE_LOWER);
        $LOG_ID = $data[log_id];
        $USER_ID = $data[user_id];
        $USERNAME = $data[username];
        $FULLNAME = $data[fullname];
        $LAST_LOGIN = $data[last_login];
        $FROM_IP = $data[from_ip];
        $cmd = " insert into USER_LAST_ACCESS (LOG_ID, USER_ID, USERNAME, FULLNAME, LAST_LOGIN, FROM_IP)
							  values ($LOG_ID, $USER_ID, '$USERNAME', '$FULLNAME', '$LAST_LOGIN', '$FROM_IP') ";
        $db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
    } // end while

    write_errtofile($path_data_err, " SUCCESS USER_LAST_ACCESS \n ");

    $cmd = " SELECT COUNT(GROUP_ID) AS COUNT_DATA FROM USER_PRIVILEGE ";
    $count_data = $db_dpis->send_cmd($cmd);
    //$db_dpis->show_error();
    if (!$count_data) {
        if ($DPISDB == "odbc")
            $cmd = " CREATE TABLE USER_PRIVILEGE(
				GROUP_ID INTEGER NOT NULL,
				PAGE_ID INTEGER NOT NULL,
				MENU_ID_LV0 INTEGER NOT NULL,
				MENU_ID_LV1 INTEGER NOT NULL,
				MENU_ID_LV2 INTEGER NOT NULL,
				MENU_ID_LV3 INTEGER NOT NULL,
				CAN_ADD CHAR(1) NULL,
				CAN_EDIT CHAR(1) NULL,
				CAN_DEL CHAR(1) NULL,
				CAN_INQ CHAR(1) NULL,
				CAN_PRINT CHAR(1) NULL,
				CAN_CONFIRM CHAR(1) NULL,
				CREATE_DATE VARCHAR(19) NULL,
				CREATE_BY INTEGER NULL,
				UPDATE_DATE VARCHAR(19) NULL,
				UPDATE_BY INTEGER NULL,
				CONSTRAINT PK_USER_PRIVILEGE PRIMARY KEY (GROUP_ID, PAGE_ID, MENU_ID_LV0, MENU_ID_LV1, MENU_ID_LV2, MENU_ID_LV3)) ";
        elseif ($DPISDB == "oci8")
            $cmd = " CREATE TABLE USER_PRIVILEGE(
				GROUP_ID NUMBER(11) NOT NULL,
				PAGE_ID NUMBER(11) NOT NULL,
				MENU_ID_LV0 NUMBER(11) NOT NULL,
				MENU_ID_LV1 NUMBER(11) NOT NULL,
				MENU_ID_LV2 NUMBER(11) NOT NULL,
				MENU_ID_LV3 NUMBER(11) NOT NULL,
				CAN_ADD CHAR(1) NULL,
				CAN_EDIT CHAR(1) NULL,
				CAN_DEL CHAR(1) NULL,
				CAN_INQ CHAR(1) NULL,
				CAN_PRINT CHAR(1) NULL,
				CAN_CONFIRM CHAR(1) NULL,
				CREATE_DATE VARCHAR2(19) NULL,
				CREATE_BY NUMBER(11) NULL,
				UPDATE_DATE VARCHAR2(19) NULL,
				UPDATE_BY NUMBER(11) NULL,
				CONSTRAINT PK_USER_PRIVILEGE PRIMARY KEY (GROUP_ID, PAGE_ID, MENU_ID_LV0, MENU_ID_LV1, MENU_ID_LV2, MENU_ID_LV3)) ";
        $db_dpis->send_cmd($cmd);
        //$db_dpis->show_error();
    }

    $cmd = " delete from USER_PRIVILEGE ";
    $db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();

    $cmd = " select GROUP_ID, PAGE_ID, MENU_ID_LV0, MENU_ID_LV1, MENU_ID_LV2, MENU_ID_LV3,
							CAN_ADD, CAN_EDIT, CAN_DEL, CAN_INQ, CAN_PRINT, CAN_CONFIRM,
							CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY
						  from USER_PRIVILEGE order by	GROUP_ID, PAGE_ID, MENU_ID_LV0, MENU_ID_LV1, MENU_ID_LV2, MENU_ID_LV3 ";
    $db->send_cmd($cmd);
    while ($data = $db->get_array()) {
        $data = array_change_key_case($data, CASE_LOWER);
        $GROUP_ID = $data[group_id];
        $PAGE_ID = $data[page_id];
        $MENU_ID_LV0 = $data[menu_id_lv0];
        $MENU_ID_LV1 = $data[menu_id_lv1];
        $MENU_ID_LV2 = $data[menu_id_lv2];
        $MENU_ID_LV3 = $data[menu_id_lv3];
        $CAN_ADD = $data[can_add];
        $CAN_EDIT = $data[can_edit];
        $CAN_DEL = $data[can_del];
        $CAN_INQ = $data[can_inq];
        $CAN_PRINT = $data[can_print];
        $CAN_CONFIRM = $data[can_confirm];
        $CREATE_DATE = $data[create_date];
        $CREATE_BY = $data[create_by];
        $UPDATE_DATE = $data[update_date];
        $UPDATE_BY = $data[update_by];
        if (!$CREATE_BY)
            $CREATE_BY = 1;
        if (!$UPDATE_BY)
            $UPDATE_BY = 1;
        $cmd = " insert into USER_PRIVILEGE (GROUP_ID, PAGE_ID, MENU_ID_LV0, MENU_ID_LV1,
							MENU_ID_LV2, MENU_ID_LV3, CAN_ADD, CAN_EDIT, CAN_DEL, CAN_INQ,
							CAN_PRINT, CAN_CONFIRM, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  values ($GROUP_ID, $PAGE_ID, $MENU_ID_LV0, $MENU_ID_LV1, $MENU_ID_LV2, $MENU_ID_LV3,
							  '$CAN_ADD', '$CAN_EDIT', '$CAN_DEL', '$CAN_INQ', '$CAN_PRINT', '$CAN_CONFIRM',
							  '$CREATE_DATE', $CREATE_BY, '$UPDATE_DATE', $UPDATE_BY) ";
        $db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
    } // end while

    write_errtofile($path_data_err, " SUCCESS USER_PRIVILEGE \n ");

    $cmd = " SELECT COUNT(ID) AS COUNT_DATA FROM USER_SECTION ";
    $count_data = $db_dpis->send_cmd($cmd);
    //$db_dpis->show_error();
    if (!$count_data) {
        if ($DPISDB == "odbc")
            $cmd = " CREATE TABLE USER_SECTION(
				ID INTEGER NOT NULL,
				CODE VARCHAR(10) NULL,
				NAME_TH VARCHAR(255) NULL,
				NAME_EN VARCHAR(255) NULL,
				URL VARCHAR(255) NULL,
				CREATE_DATE VARCHAR(19) NULL,
				CREATE_BY INTEGER NULL,
				UPDATE_DATE VARCHAR(19) NULL,
				UPDATE_BY INTEGER NULL,
				CONSTRAINT PK_USER_SECTION PRIMARY KEY (ID)) ";
        elseif ($DPISDB == "oci8")
            $cmd = " CREATE TABLE USER_SECTION(
				ID NUMBER(11) NOT NULL,
				CODE VARCHAR2(10) NULL,
				NAME_TH VARCHAR2(255) NULL,
				NAME_EN VARCHAR2(255) NULL,
				URL VARCHAR2(255) NULL,
				CREATE_DATE VARCHAR2(19) NULL,
				CREATE_BY NUMBER(11) NULL,
				UPDATE_DATE VARCHAR2(19) NULL,
				UPDATE_BY NUMBER(11) NULL,
				CONSTRAINT PK_USER_SECTION PRIMARY KEY (ID)) ";
        $db_dpis->send_cmd($cmd);
        //$db_dpis->show_error();
    }

    $cmd = " delete from USER_SECTION ";
    $db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();

    $cmd = " select ID, CODE, NAME_TH, NAME_EN, URL, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY
						  from USER_SECTION order by ID ";
    $db->send_cmd($cmd);
    while ($data = $db->get_array()) {
        $data = array_change_key_case($data, CASE_LOWER);
        $ID = $data[id];
        $CODE = $data[code];
        $NAME_TH = $data[name_th];
        $NAME_EN = $data[name_en];
        $URL = $data[url];
        $CREATE_DATE = $data[create_date];
        $CREATE_BY = $data[create_by];
        $UPDATE_DATE = $data[update_date];
        $UPDATE_BY = $data[update_by];
        if (!$CREATE_BY)
            $CREATE_BY = 1;
        if (!$UPDATE_BY)
            $UPDATE_BY = 1;
        $cmd = " insert into USER_SECTION (ID, CODE, NAME_TH, NAME_EN, URL,
							  CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  values ($ID, '$CODE', '$NAME_TH', '$NAME_EN', '$URL',
							  '$CREATE_DATE', $CREATE_BY, '$UPDATE_DATE', $UPDATE_BY) ";
        $db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
    } // end while

    write_errtofile($path_data_err, " SUCCESS USER_SECTION \n ");

    $cmd = " SELECT COUNT(ID) AS COUNT_DATA FROM USER_SECTION ";
    $count_data = $db_dpis->send_cmd($cmd);
    //$db_dpis->show_error();
    if ($count_data) {
        $cmd = " select config_value from system_config where config_name='DPISDB' ";
        $db->send_cmd($cmd);
        $data = $db->get_array();
        $data = array_change_key_case($data, CASE_LOWER);
        $db_type = $data[config_value];

        $cmd = " select config_value from system_config where config_name='dpisdb_host' ";
        $db->send_cmd($cmd);
        $data = $db->get_array();
        $data = array_change_key_case($data, CASE_LOWER);
        $db_host = $data[config_value];

        $cmd = " select config_value from system_config where config_name='dpisdb_name' ";
        $db->send_cmd($cmd);
        $data = $db->get_array();
        $data = array_change_key_case($data, CASE_LOWER);
        $db_name = $data[config_value];

        $cmd = " select config_value from system_config where config_name='dpisdb_user' ";
        $db->send_cmd($cmd);
        $data = $db->get_array();
        $data = array_change_key_case($data, CASE_LOWER);
        $db_user = $data[config_value];

        $cmd = " select config_value from system_config where config_name='dpisdb_pwd' ";
        $db->send_cmd($cmd);
        $data = $db->get_array();
        $data = array_change_key_case($data, CASE_LOWER);
        $db_pwd = $data[config_value];

        $strFileName = "../php_scripts/db_connect_var.php";
        $objFopen = fopen($strFileName, 'w');
        fwrite($objFopen, "<?\n");
        fwrite($objFopen, "\t\$db_type = \"$db_type\";\n");
        fwrite($objFopen, "\t\$db_host = \"$db_host\";\n");
        fwrite($objFopen, "\t\$db_name = \"$db_name\";\n");
        fwrite($objFopen, "\t\$db_user = \"$db_user\";\n");
        fwrite($objFopen, "\t\$db_pwd = \"$db_pwd\";\n");
        fwrite($objFopen, "?>");
        if ($objFopen) {
            $err = "";
        } else {
            $err = "ไม่สามารถสร้างแฟ้ม db_connect_var.php ได้";
        }
        fclose($objFopen);
    }

    write_errtofile($path_data_err, " SUCCESS DB_CONNECT_VAR \n ");

    $cmd = " SELECT COUNT(LOG_ID) AS COUNT_DATA FROM USER_LOG ";
    $count_data = $db_dpis->send_cmd($cmd);
    //$db_dpis->show_error();
    if (!$count_data) {
        if ($DPISDB == "odbc")
            $cmd = " CREATE TABLE USER_LOG(
				LOG_ID INTEGER NOT NULL,
				USER_ID INTEGER NOT NULL,
				USERNAME VARCHAR(255) NULL,
				FULLNAME VARCHAR(255) NULL,
				LOG_DETAIL MEMO NULL,
				LOG_DATE VARCHAR(19) NULL,
				CONSTRAINT PK_USER_LOG PRIMARY KEY (LOG_ID)) ";
        elseif ($DPISDB == "oci8")
            $cmd = " CREATE TABLE USER_LOG(
				LOG_ID NUMBER(11) NOT NULL,
				USER_ID NUMBER(11) NOT NULL,
				USERNAME VARCHAR2(255) NULL,
				FULLNAME VARCHAR2(255) NULL,
				LOG_DETAIL VARCHAR2(2000) NULL,
				LOG_DATE VARCHAR2(19) NULL,
				CONSTRAINT PK_USER_LOG PRIMARY KEY (LOG_ID)) ";
        $db_dpis->send_cmd($cmd);
        //$db_dpis->show_error();
    }

    $cmd = " delete from USER_LOG ";
    $db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();

    $cmd = " select LOG_ID, USER_ID, USERNAME, FULLNAME, LOG_DETAIL, LOG_DATE
						  from USER_LOG order by LOG_ID ";
    $db->send_cmd($cmd);
    while ($data = $db->get_array()) {
        $data = array_change_key_case($data, CASE_LOWER);
        $LOG_ID = $data[log_id];
        $USER_ID = $data[user_id];
        $USERNAME = $data[username];
        $FULLNAME = $data[fullname];
        $LOG_DETAIL = $data[log_detail];
        $LOG_DATE = $data[log_date];
        $cmd = " insert into USER_LOG (LOG_ID, USER_ID, USERNAME, FULLNAME, LOG_DETAIL, LOG_DATE)
							  values ($LOG_ID, $USER_ID, '$USERNAME', '$FULLNAME', '$LOG_DETAIL', '$LOG_DATE') ";
        $db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
    } // end while
    write_errtofile($path_data_err, " SUCCESS USER_LOG \n ");
}
