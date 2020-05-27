<?php
if (!$BUTTON_DISPLAY) {
				$cmd = " replace into system_config (config_id, config_name, config_value, config_remark) 
								values (30, 'BUTTON_DISPLAY', '1', 'การแสดงผลปุ่มกด') ";
				$db->send_cmd($cmd);
			}

			$cmd = " ALTER TABLE PER_POSITION DROP COLUMN ORG_FLAG_KPI ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_POSITION DROP COLUMN ORG_ID_KPI ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_POSITION DROP COLUMN ORG_FLAG_SALARY ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_POSITION DROP COLUMN ORG_ID_SALARY ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE  PER_PERSONAL DROP COLUMN HIP_FLAG ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_EXTRAHIS DROP COLUMN EXH_BOOK_NO ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_EXTRAHIS DROP COLUMN EXH_BOOK_DATE ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REWARDHIS DROP COLUMN REH_BOOK_NO ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REWARDHIS DROP COLUMN REH_BOOK_DATE ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE user_group ADD dpisdb char(1) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			if($db_type=="odbc") 
				$cmd = " ALTER TABLE user_group ADD dpisdb_host varchar(50) ";
			elseif($db_type=="oci8") 
				$cmd = " ALTER TABLE user_group ADD dpisdb_host varchar2(50) ";
			elseif($db_type=="mysql")
				$cmd = " ALTER TABLE user_group ADD dpisdb_host varchar(50) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			if($db_type=="odbc") 
				$cmd = " ALTER TABLE user_group ADD dpisdb_name varchar(50) ";
			elseif($db_type=="oci8") 
				$cmd = " ALTER TABLE user_group ADD dpisdb_name varchar2(50) ";
			elseif($db_type=="mysql")
				$cmd = " ALTER TABLE user_group ADD dpisdb_name varchar(50) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			if($db_type=="odbc") 
				$cmd = " ALTER TABLE user_group ADD dpisdb_user varchar(50) ";
			elseif($db_type=="oci8") 
				$cmd = " ALTER TABLE user_group ADD dpisdb_user varchar2(50) ";
			elseif($db_type=="mysql")
				$cmd = " ALTER TABLE user_group ADD dpisdb_user varchar(50) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			if($db_type=="odbc") 
				$cmd = " ALTER TABLE user_group ADD dpisdb_pwd varchar(50) ";
			elseif($db_type=="oci8") 
				$cmd = " ALTER TABLE user_group ADD dpisdb_pwd varchar2(50) ";
			elseif($db_type=="mysql")
				$cmd = " ALTER TABLE user_group ADD dpisdb_pwd varchar(50) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			add_field("PER_ASSESS_MAIN", "PER_TYPE","SINGLE", "1", "NULL");

			$cmd = " UPDATE PER_ASSESS_MAIN SET PER_TYPE = 1 WHERE PER_TYPE IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_ASSESS_LEVEL", "PER_TYPE","SINGLE", "1", "NULL");

			$cmd = " UPDATE PER_ASSESS_LEVEL SET PER_TYPE = 1 WHERE PER_TYPE IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_COMPENSATION_TEST", "PER_TYPE","SINGLE", "1", "NULL");

			$cmd = " UPDATE PER_COMPENSATION_TEST SET PER_TYPE = 1 WHERE PER_TYPE IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " SELECT POS_ID FROM PER_POS_MGTSALARY ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) {
				if($DPISDB=="odbc") 
					$cmd = " CREATE TABLE PER_POS_MGTSALARY (
					POS_ID INTEGER NOT NULL,
					EX_CODE VARCHAR(10) NOT NULL,
					POS_STARTDATE VARCHAR(19) NULL,
					POS_STATUS SINGLE NULL,
					UPDATE_USER INTEGER NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,
					CONSTRAINT PK_PER_POS_MGTSALARY PRIMARY KEY (POS_ID, EX_CODE)) ";
				elseif($DPISDB=="oci8") 
					$cmd = " CREATE TABLE PER_POS_MGTSALARY (
					POS_ID NUMBER(10) NOT NULL,
					EX_CODE VARCHAR2(10) NOT NULL,
					POS_STARTDATE VARCHAR2(19) NULL,
					POS_STATUS NUMBER(1) NULL,
					UPDATE_USER NUMBER(11) NOT NULL,
					UPDATE_DATE VARCHAR2(19) NOT NULL,
					CONSTRAINT PK_PER_POS_MGTSALARY PRIMARY KEY (POS_ID, EX_CODE)) ";
				elseif($DPISDB=="mysql")
					$cmd = " CREATE TABLE PER_POS_MGTSALARY (
					POS_ID INTEGER(10) NOT NULL,
					EX_CODE VARCHAR(10) NOT NULL,
					POS_STARTDATE VARCHAR(19) NULL,
					POS_STATUS SMALLINT(1) NULL,
					UPDATE_USER INTEGER(11) NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,
					CONSTRAINT PK_PER_POS_MGTSALARY PRIMARY KEY (POS_ID, EX_CODE)) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			}

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST ALTER SF_CODE_O NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST MODIFY SF_CODE_O NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST MODIFY SF_CODE_O NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST ALTER SF_CODE_K NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST MODIFY SF_CODE_K NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST MODIFY SF_CODE_K NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST ALTER SF_CODE_D NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST MODIFY SF_CODE_D NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST MODIFY SF_CODE_D NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST ALTER SF_CODE_M NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST MODIFY SF_CODE_M NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST MODIFY SF_CODE_M NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LEVEL SET POSITION_LEVEL = LEVEL_NAME WHERE POSITION_LEVEL IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_DECORATEHIS", "DEH_POSITION","VARCHAR", "255", "NULL");
			add_field("PER_DECORATEHIS", "DEH_ORG","VARCHAR", "255", "NULL");
			add_field("PER_WORKFLOW_DECORATEHIS", "DEH_POSITION","VARCHAR", "255", "NULL");
			add_field("PER_WORKFLOW_DECORATEHIS", "DEH_ORG","VARCHAR", "255", "NULL");
			add_field("PER_COMDTL", "CMD_SEQ_NO","INTEGER", "6", "NULL");

			$cmd = " ALTER TABLE PER_EXTRAHIS DROP CONSTRAINT FK2_PER_EXTRAHIS ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_EXTRATYPE ALTER EX_CODE CHAR(10) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_EXTRATYPE MODIFY EX_CODE CHAR(10) ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_EXTRATYPE MODIFY EX_CODE CHAR(10) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_EXTRAHIS ALTER EX_CODE CHAR(10) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_EXTRAHIS MODIFY EX_CODE CHAR(10) ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_EXTRAHIS MODIFY EX_CODE CHAR(10) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_EXTRAHIS ADD CONSTRAINT FK2_PER_EXTRAHIS 
							  FOREIGN KEY (EX_CODE) REFERENCES PER_EXTRATYPE (EX_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_POS_GROUP", "PG_NAME_SALARY","VARCHAR", "100", "NULL");

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_pos_group.html?table=PER_POS_GROUP' 
							  WHERE LINKTO_WEB = 'master_table.html?table=PER_POS_GROUP' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE PER_POS_GROUP SET PG_NAME_SALARY = 'กลุ่มที่ 1' WHERE PG_CODE = '1000' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_GROUP SET PG_NAME_SALARY = 'กลุ่มที่ 2' WHERE PG_CODE = '2000' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_GROUP SET PG_NAME_SALARY = 'กลุ่มที่ 3' WHERE PG_CODE = '3000' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_GROUP SET PG_NAME_SALARY = 'กลุ่มที่ 4' WHERE PG_CODE = '4000' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_GROUP SET PG_NAME_SALARY = PG_NAME WHERE PG_NAME_SALARY IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_POSITIONHIS", "ES_CODE","VARCHAR", "10", "NULL");
			add_field("PER_POSITIONHIS", "POH_LEVEL_NO","VARCHAR", "10", "NULL");
	
			$cmd = " UPDATE PER_POSITIONHIS SET POH_LEVEL_NO = LEVEL_NO WHERE POH_LEVEL_NO IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
	
			$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, 
							  UPDATE_DATE, MOV_SUB_TYPE)
							  VALUES ('21305', 'เลื่อนเงินเดือน', 2, 1, $SESS_USERID, '$UPDATE_DATE', 4) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, 
							  UPDATE_DATE, MOV_SUB_TYPE)
							  VALUES ('21375', 'ไม่ได้เลื่อนเงินเดือน', 2, 1, $SESS_USERID, '$UPDATE_DATE', 4) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_KPI_FORM", "KF_STATUS","SINGLE", "1", "NULL");

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 
							  WHERE LINKTO_WEB = 'personal_inquiry_dopa.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();
			if (!$count_data) {
				$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV2 ";
				$db->send_cmd($cmd);
				//$db->show_error();
				$data = $db->get_array();
				$MAX_ID = $data[MAX_ID] + 1;
				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'TH', $MAX_ID, 22, 'P0122 สอบถามข้อมูล (กรมการปกครอง)', 'S', 'W', 'personal_inquiry_dopa.html', 0, 35, 241, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 22, 'P0122 สอบถามข้อมูล (กรมการปกครอง)', 'S', 'W', 'personal_inquiry_dopa.html', 0, 35, 241, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			add_field("PER_COMDTL", "CMD_ES_CODE","VARCHAR", "10", "NULL");
			add_field("PER_COMDTL", "ES_CODE","VARCHAR", "10", "NULL");
			add_field("PER_COMDTL", "PM_CODE","VARCHAR", "10", "NULL");
	
			$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, UPDATE_DATE, MOV_SUB_TYPE)
							  VALUES ('21395', 'เงินเดือนรับโอน', 2, 1, $SESS_USERID, '$UPDATE_DATE', 4) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, UPDATE_DATE, MOV_SUB_TYPE)
							  VALUES ('21394', 'เงินเดือนบรรจุกลับ', 2, 1, $SESS_USERID, '$UPDATE_DATE', 4) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			add_field("PER_SALARYHIS", "SAH_OLD_SALARY","NUMBER", "16,2", "NULL");

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE  PER_COMMAND DROP CONSTRAINT INXU1_PER_COMMAND ";
			elseif($DPISDB=="oci8") 
				$cmd = " DROP INDEX INXU1_PER_COMMAND ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX INXU1_PER_COMMAND ON PER_COMMAND (COM_NO) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO, COM_ACTIVE) 
							  VALUES ('5240', 'คส. 24', 'คำสั่งปรับอัตราเงินเดือนแรกบรรจุ', '524', 100, 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_COMTYPE SET COM_GROUP = '524' WHERE COM_TYPE = '9999' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_ORDER_DTL", "LEVEL_NO","VARCHAR", "10", "NULL");
			add_field("PER_ORDER_DTL", "ORD_REMARK","MEMO", "2000", "NULL");
			add_field("PER_COURSE", "CO_COURSE_NAME","VARCHAR", "255", "NULL");
			add_field("PER_COURSE", "CO_DEGREE_RECEIVE","VARCHAR", "100", "NULL");
			add_field("PER_COURSE", "CO_BOOK_NO","VARCHAR", "255", "NULL");
			add_field("PER_COURSE", "CO_BOOK_DATE","VARCHAR", "19", "NULL");
			add_field("PER_PERSONAL", "PER_POS_DOCDATE","VARCHAR", "19", "NULL");
			add_field("PER_PERSONAL", "PER_POS_DESC","VARCHAR", "255", "NULL");
			add_field("PER_PERSONAL", "PER_POS_REMARK","VARCHAR", "255", "NULL");
			add_field("PER_PERSONAL", "PER_BOOK_NO","VARCHAR", "255", "NULL");
			add_field("PER_PERSONAL", "PER_BOOK_DATE","VARCHAR", "19", "NULL");

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_POSITIONHIS ALTER POH_DOCNO NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POSITIONHIS MODIFY POH_DOCNO NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_POSITIONHIS MODIFY POH_DOCNO NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_POSITIONHIS ALTER POH_DOCDATE NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POSITIONHIS MODIFY POH_DOCDATE NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_POSITIONHIS MODIFY POH_DOCDATE NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_WORKFLOW_POSITIONHIS ALTER POH_DOCNO NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_WORKFLOW_POSITIONHIS MODIFY POH_DOCNO NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_WORKFLOW_POSITIONHIS MODIFY POH_DOCNO NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_WORKFLOW_POSITIONHIS ALTER POH_DOCDATE NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_WORKFLOW_POSITIONHIS MODIFY POH_DOCDATE NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_WORKFLOW_POSITIONHIS MODIFY POH_DOCDATE NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_POSITION ALTER POS_MGTSALARY NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POSITION MODIFY POS_MGTSALARY NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_POSITION MODIFY POS_MGTSALARY NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_POSITION ALTER PT_CODE NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POSITION MODIFY PT_CODE NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_POSITION MODIFY PT_CODE NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_POS_MOVE ALTER POS_MGTSALARY NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POS_MOVE MODIFY POS_MGTSALARY NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_POS_MOVE MODIFY POS_MGTSALARY NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_POS_MOVE ALTER PT_CODE NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POS_MOVE MODIFY PT_CODE NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_POS_MOVE MODIFY PT_CODE NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_POSITIONHIS", "POH_CMD_SEQ","INTEGER", "6", "NULL");
			add_field("PER_SALARYHIS", "SAH_CMD_SEQ","INTEGER", "6", "NULL");

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_POSITIONHIS ALTER POH_CMD_SEQ INTEGER ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POSITIONHIS MODIFY POH_CMD_SEQ NUMBER(6) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_POSITIONHIS ALTER POH_CMD_SEQ INTEGER(6) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_SALARYHIS ALTER SAH_CMD_SEQ INTEGER ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SALARYHIS MODIFY SAH_CMD_SEQ NUMBER(6) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_SALARYHIS ALTER SAH_CMD_SEQ INTEGER(6) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_PERSONAL_9 ON PER_PERSONAL (PAY_ID, PER_STATUS) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_ORG_2 ON PER_ORG (ORG_DOPA_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_ORG_3 ON PER_ORG (ORG_ID_REF) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_PERSONAL_10 ON PER_PERSONAL (POEMS_ID, PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_PERSONAL_11 ON PER_PERSONAL (POEMS_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_PERSONAL_12 ON PER_PERSONAL (POEMS_ID, PN_CODE, PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_PERSONAL_13 ON PER_PERSONAL (DEPARTMENT_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_PERSONAL SET PER_FILE_NO = NULL WHERE PER_FILE_NO = 'NULL' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_PERSONAL SET PER_BANK_ACCOUNT = NULL WHERE PER_BANK_ACCOUNT = 'NULL' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_ORG_LEVEL SET OL_SEQ_NO = 1 WHERE OL_CODE = '01' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " UPDATE PER_ORG_LEVEL SET OL_SEQ_NO = 2 WHERE OL_CODE = '02' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " UPDATE PER_ORG_LEVEL SET OL_SEQ_NO = 3 WHERE OL_CODE = '03' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " UPDATE PER_ORG_LEVEL SET OL_SEQ_NO = 4 WHERE OL_CODE = '04' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " UPDATE PER_ORG_LEVEL SET OL_SEQ_NO = 5 WHERE OL_CODE = '05' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_ORG_LEVEL (OL_CODE, OL_NAME, OL_ACTIVE, UPDATE_USER, UPDATE_DATE, OL_SEQ_NO)
							  VALUES ('06', 'ต่ำกว่าสำนัก/กอง 3 ระดับ', 1, $SESS_USERID, '$UPDATE_DATE', 6) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_ORG_LEVEL (OL_CODE, OL_NAME, OL_ACTIVE, UPDATE_USER, UPDATE_DATE, OL_SEQ_NO)
							  VALUES ('07', 'ต่ำกว่าสำนัก/กอง 4 ระดับ', 1, $SESS_USERID, '$UPDATE_DATE', 7) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_ORG_LEVEL (OL_CODE, OL_NAME, OL_ACTIVE, UPDATE_USER, UPDATE_DATE, OL_SEQ_NO)
							  VALUES ('08', 'ต่ำกว่าสำนัก/กอง 5 ระดับ', 1, $SESS_USERID, '$UPDATE_DATE', 8) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			add_field("PER_POSITION", "ORG_ID_3","INTEGER", "10", "NULL");
			add_field("PER_POSITION", "ORG_ID_4","INTEGER", "10", "NULL");
			add_field("PER_POSITION", "ORG_ID_5","INTEGER", "10", "NULL");
			add_field("PER_POS_EMP", "ORG_ID_3","INTEGER", "10", "NULL");
			add_field("PER_POS_EMP", "ORG_ID_4","INTEGER", "10", "NULL");
			add_field("PER_POS_EMP", "ORG_ID_5","INTEGER", "10", "NULL");
			add_field("PER_POS_EMPSER", "ORG_ID_3","INTEGER", "10", "NULL");
			add_field("PER_POS_EMPSER", "ORG_ID_4","INTEGER", "10", "NULL");
			add_field("PER_POS_EMPSER", "ORG_ID_5","INTEGER", "10", "NULL");
			add_field("PER_POS_MOVE", "ORG_ID_3","INTEGER", "10", "NULL");
			add_field("PER_POS_MOVE", "ORG_ID_4","INTEGER", "10", "NULL");
			add_field("PER_POS_MOVE", "ORG_ID_5","INTEGER", "10", "NULL");
			add_field("PER_COMDTL", "CMD_ORG6","VARCHAR", "100", "NULL");
			add_field("PER_COMDTL", "CMD_ORG7","VARCHAR", "100", "NULL");
			add_field("PER_COMDTL", "CMD_ORG8","VARCHAR", "100", "NULL");
			add_field("PER_ORDER_DTL", "ORG_ID_3","INTEGER", "10", "NULL");
			add_field("PER_ORDER_DTL", "ORG_ID_4","INTEGER", "10", "NULL");
			add_field("PER_ORDER_DTL", "ORG_ID_5","INTEGER", "10", "NULL");

			if($DPISDB=="odbc") 
				$cmd = " UPDATE PER_SALARYHIS SET SAH_EFFECTIVEDATE = LEFT(SAH_EFFECTIVEDATE, 10) ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_SALARYHIS SET SAH_EFFECTIVEDATE = SUBSTR(SAH_EFFECTIVEDATE, 1, 10) ";
			elseif($DPISDB=="mysql")
				$cmd = " UPDATE PER_SALARYHIS SET SAH_EFFECTIVEDATE = LEFT(SAH_EFFECTIVEDATE, 10) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_POSITION DROP CONSTRAINT FK7_PER_POSITION ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_POSITION ALTER CL_NAME VARCHAR(100) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POSITION MODIFY CL_NAME VARCHAR2(100) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_POSITION MODIFY CL_NAME VARCHAR(100) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " SELECT COUNT(PER_ID) AS COUNT_DATA FROM PER_FAMILY ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$COUNT_DATA = $data[COUNT_DATA] + 0;

			$cmd = " SELECT FML_ID FROM PER_FAMILY ";
			$count_data = $db_dpis->send_cmd($cmd);

			if (!$count_data && $COUNT_DATA == 0) {
				$cmd = " DROP TABLE PER_FAMILY ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " DROP TABLE PER_WORKFLOW_FAMILY ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			}

			if($DPISDB=="odbc") 
				$cmd = " CREATE TABLE PER_FAMILY (
				FML_ID INTEGER NOT NULL,
				PER_ID INTEGER NOT NULL,
				FML_SEQ INTEGER2 NULL,
				FML_TYPE SINGLE NOT NULL,
				PN_CODE VARCHAR(3) NULL,
				FML_NAME VARCHAR(100) NULL,
				FML_SURNAME VARCHAR(100) NULL,
				FML_CARDNO VARCHAR(15) NULL,
				FML_GENDER SINGLE NULL,
				FML_BIRTHDATE VARCHAR(19) NULL,
				FML_ALIVE SINGLE NULL,
				RE_CODE VARCHAR(2) NULL,
				OC_CODE VARCHAR(3) NULL,
				OC_OTHER VARCHAR(100) NULL,
				FML_BY SINGLE NULL,
				FML_BY_OTHER VARCHAR(100) NULL,
				FML_DOCTYPE SINGLE NULL,
				FML_DOCNO VARCHAR(20) NULL,
				FML_DOCDATE VARCHAR(19) NULL,
				MR_CODE VARCHAR(2) NULL,
				MR_DOCTYPE SINGLE NULL,
				MR_DOCNO VARCHAR(20) NULL,
				MR_DOCDATE VARCHAR(19) NULL,
				MR_DOC_PV_CODE VARCHAR(10) NULL,
				PV_CODE VARCHAR(10) NULL,
				POST_CODE VARCHAR(5) NULL,
				FML_INCOMPETENT SINGLE NULL,
				IN_DOCTYPE SINGLE NULL,
				IN_DOCNO VARCHAR(20) NULL,
				IN_DOCDATE VARCHAR(19) NULL,
				UPDATE_USER INTEGER NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,
				CONSTRAINT PK_PER_FAMILY PRIMARY KEY (FML_ID)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE PER_FAMILY (
				FML_ID NUMBER(10) NOT NULL,
				PER_ID NUMBER(10) NOT NULL,
				FML_SEQ NUMBER(3) NULL,
				FML_TYPE NUMBER(1) NOT NULL,
				PN_CODE VARCHAR2(3) NULL,
				FML_NAME VARCHAR2(100) NULL,
				FML_SURNAME VARCHAR2(100) NULL,
				FML_CARDNO VARCHAR2(15) NULL,
				FML_GENDER NUMBER(1) NULL,
				FML_BIRTHDATE VARCHAR2(19) NULL,
				FML_ALIVE NUMBER(1) NULL,
				RE_CODE VARCHAR2(2) NULL,
				OC_CODE VARCHAR2(3) NULL,
				OC_OTHER VARCHAR2(100) NULL,
				FML_BY NUMBER(1) NULL,
				FML_BY_OTHER VARCHAR2(100) NULL,
				FML_DOCTYPE NUMBER(1) NULL,
				FML_DOCNO VARCHAR2(20) NULL,
				FML_DOCDATE VARCHAR2(19) NULL,
				MR_CODE VARCHAR2(2) NULL,
				MR_DOCTYPE NUMBER(1) NULL,
				MR_DOCNO VARCHAR2(20) NULL,
				MR_DOCDATE VARCHAR2(19) NULL,
				MR_DOC_PV_CODE VARCHAR2(10) NULL,
				PV_CODE VARCHAR2(10) NULL,
				POST_CODE VARCHAR2(5) NULL,
				FML_INCOMPETENT NUMBER(1) NULL,
				IN_DOCTYPE NUMBER(1) NULL,
				IN_DOCNO VARCHAR2(20) NULL,
				IN_DOCDATE VARCHAR2(19) NULL,
				UPDATE_USER NUMBER(11) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,
				CONSTRAINT PK_PER_FAMILY PRIMARY KEY (FML_ID)) ";
			elseif($DPISDB=="mysql")
				$cmd = " CREATE TABLE PER_FAMILY (
				FML_ID INTEGER(10) NOT NULL,
				PER_ID INTEGER(10) NOT NULL,
				FML_SEQ SMALLINT(3) NULL,
				FML_TYPE SMALLINT(1) NOT NULL,
				PN_CODE VARCHAR(3) NULL,
				FML_NAME VARCHAR(100) NULL,
				FML_SURNAME VARCHAR(100) NULL,
				FML_CARDNO VARCHAR(15) NULL,
				FML_GENDER SMALLINT(1) NULL,
				FML_BIRTHDATE VARCHAR(19) NULL,
				FML_ALIVE SMALLINT(1) NULL,
				RE_CODE VARCHAR(2) NULL,
				OC_CODE VARCHAR(3) NULL,
				OC_OTHER VARCHAR(100) NULL,
				FML_BY SMALLINT(1) NULL,
				FML_BY_OTHER VARCHAR(100) NULL,
				FML_DOCTYPE SMALLINT(1) NULL,
				FML_DOCNO VARCHAR(20) NULL,
				FML_DOCDATE VARCHAR(19) NULL,
				MR_CODE VARCHAR(2) NULL,
				MR_DOCTYPE SMALLINT(1) NULL,
				MR_DOCNO VARCHAR(20) NULL,
				MR_DOCDATE VARCHAR(19) NULL,
				MR_DOC_PV_CODE VARCHAR(10) NULL,
				PV_CODE VARCHAR(10) NULL,
				POST_CODE VARCHAR(5) NULL,
				FML_INCOMPETENT SMALLINT(1) NULL,
				IN_DOCTYPE SMALLINT(1) NULL,
				IN_DOCNO VARCHAR(20) NULL,
				IN_DOCDATE VARCHAR(19) NULL,
				UPDATE_USER INTEGER(11) NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,
				CONSTRAINT PK_PER_FAMILY PRIMARY KEY (FML_ID)) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " SELECT * INTO PER_WORKFLOW_NAMEHIS FROM PER_NAMEHIS WHERE UPDATE_DATE = 'x' ";
			elseif($DPISDB=="oci8" || $DPISDB=="mysql") 
				$cmd = " CREATE TABLE PER_WORKFLOW_NAMEHIS AS SELECT * FROM PER_NAMEHIS WHERE UPDATE_DATE = 'x' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_WORKFLOW_NAMEHIS ADD CONSTRAINT PK_PER_WORKFLOW_NAMEHIS  
							  PRIMARY KEY (NH_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_WORKFLOW_NAMEHIS", "NH_WF_STATUS","VARCHAR", "2", "NULL");

			if($DPISDB=="odbc") 
				$cmd = " SELECT * INTO PER_WORKFLOW_EDUCATE FROM PER_EDUCATE WHERE UPDATE_DATE = 'x' ";
			elseif($DPISDB=="oci8" || $DPISDB=="mysql") 
				$cmd = " CREATE TABLE PER_WORKFLOW_EDUCATE AS SELECT * FROM PER_EDUCATE WHERE UPDATE_DATE = 'x' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_WORKFLOW_EDUCATE ADD CONSTRAINT PK_PER_WORKFLOW_EDUCATE  
							  PRIMARY KEY (EDU_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_WORKFLOW_EDUCATE", "EDU_WF_STATUS","VARCHAR", "2", "NULL");

			if($DPISDB=="odbc") 
				$cmd = " SELECT * INTO PER_WORKFLOW_MARRHIS FROM PER_MARRHIS WHERE UPDATE_DATE = 'x' ";
			elseif($DPISDB=="oci8" || $DPISDB=="mysql") 
				$cmd = " CREATE TABLE PER_WORKFLOW_MARRHIS AS SELECT * FROM PER_MARRHIS WHERE UPDATE_DATE = 'x' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_WORKFLOW_MARRHIS ADD CONSTRAINT PK_PER_WORKFLOW_MARRHIS  
							  PRIMARY KEY (MAH_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_WORKFLOW_MARRHIS", "MAH_WF_STATUS","VARCHAR", "2", "NULL");

			if($DPISDB=="odbc") 
				$cmd = " SELECT * INTO PER_WORKFLOW_DECORATEHIS FROM PER_DECORATEHIS WHERE UPDATE_DATE = 'x' ";
			elseif($DPISDB=="oci8" || $DPISDB=="mysql") 
				$cmd = " CREATE TABLE PER_WORKFLOW_DECORATEHIS AS SELECT * FROM PER_DECORATEHIS WHERE UPDATE_DATE = 'x' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_WORKFLOW_DECORATEHIS ADD CONSTRAINT PK_PER_WORKFLOW_DECORATEHIS  
							  PRIMARY KEY (DEH_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_WORKFLOW_DECORATEHIS", "DEH_WF_STATUS","VARCHAR", "2", "NULL");

			if($DPISDB=="odbc") 
				$cmd = " SELECT * INTO PER_WORKFLOW_TRAINING FROM PER_TRAINING WHERE UPDATE_DATE = 'x' ";
			elseif($DPISDB=="oci8" || $DPISDB=="mysql") 
				$cmd = " CREATE TABLE PER_WORKFLOW_TRAINING AS SELECT * FROM PER_TRAINING WHERE UPDATE_DATE = 'x' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_WORKFLOW_TRAINING ADD CONSTRAINT PK_PER_WORKFLOW_TRAINING  
							  PRIMARY KEY (TRN_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_WORKFLOW_TRAINING", "TRN_WF_STATUS","VARCHAR", "2", "NULL");

			if($DPISDB=="odbc") 
				$cmd = " SELECT * INTO PER_WORKFLOW_REWARDHIS FROM PER_REWARDHIS WHERE UPDATE_DATE = 'x' ";
			elseif($DPISDB=="oci8" || $DPISDB=="mysql") 
				$cmd = " CREATE TABLE PER_WORKFLOW_REWARDHIS AS SELECT * FROM PER_REWARDHIS WHERE UPDATE_DATE = 'x' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_WORKFLOW_REWARDHIS ADD CONSTRAINT PK_PER_WORKFLOW_REWARDHIS  
							  PRIMARY KEY (REH_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_WORKFLOW_REWARDHIS", "REH_WF_STATUS","VARCHAR", "2", "NULL");

			if($DPISDB=="odbc") 
				$cmd = " SELECT * INTO PER_WORKFLOW_POSITIONHIS FROM PER_POSITIONHIS WHERE UPDATE_DATE = 'x' ";
			elseif($DPISDB=="oci8" || $DPISDB=="mysql") 
				$cmd = " CREATE TABLE PER_WORKFLOW_POSITIONHIS AS SELECT * FROM PER_POSITIONHIS WHERE UPDATE_DATE = 'x' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_WORKFLOW_POSITIONHIS ADD CONSTRAINT PK_PER_WORKFLOW_POSITIONHIS  
							  PRIMARY KEY (POH_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_WORKFLOW_POSITIONHIS", "POH_WF_STATUS","VARCHAR", "2", "NULL");
			add_field("PER_WORKFLOW_POSITIONHIS", "POH_LAST_POSITION","CHAR", "1", "NULL");
			add_field("PER_WORKFLOW_POSITIONHIS", "POH_CMD_SEQ","INTEGER", "6", "NULL");

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_WORKFLOW_POSITIONHIS ALTER POH_SEQ_NO INTEGER2 ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_WORKFLOW_POSITIONHIS MODIFY POH_SEQ_NO NUMBER(5) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_WORKFLOW_POSITIONHIS ALTER POH_SEQ_NO SMALLINT(5) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_WORKFLOW_POSITIONHIS ALTER POH_REMARK NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_WORKFLOW_POSITIONHIS MODIFY POH_REMARK NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_WORKFLOW_POSITIONHIS MODIFY POH_REMARK NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_WORKFLOW_POSITIONHIS", "POH_ISREAL","CHAR", "1", "NULL");

			$cmd = " UPDATE PER_WORKFLOW_POSITIONHIS SET POH_ISREAL = 'Y' WHERE POH_ISREAL IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_WORKFLOW_POSITIONHIS", "POH_ORG_DOPA_CODE","VARCHAR", "20", "NULL");
			add_field("PER_WORKFLOW_POSITIONHIS", "ES_CODE","VARCHAR", "10", "NULL");
 			add_field("PER_WORKFLOW_POSITIONHIS", "POH_LEVEL_NO","VARCHAR", "10", "NULL");
	
			$cmd = " UPDATE PER_WORKFLOW_POSITIONHIS SET POH_LEVEL_NO = LEVEL_NO WHERE POH_LEVEL_NO IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
	
			add_field("PER_WORKFLOW_POSITIONHIS", "TP_CODE","VARCHAR", "10", "NULL");

			if($DPISDB=="odbc") 
				$cmd = " SELECT * INTO PER_WORKFLOW_SALARYHIS FROM PER_SALARYHIS WHERE UPDATE_DATE = 'x' ";
			elseif($DPISDB=="oci8" || $DPISDB=="mysql") 
				$cmd = " CREATE TABLE PER_WORKFLOW_SALARYHIS AS SELECT * FROM PER_SALARYHIS WHERE UPDATE_DATE = 'x' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_WORKFLOW_SALARYHIS ADD CONSTRAINT PK_PER_WORKFLOW_SALARYHIS  
							  PRIMARY KEY (SAH_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_WORKFLOW_SALARYHIS", "SAH_SALARY_MIDPOINT","NUMBER", "16,2", "NULL");
			add_field("PER_WORKFLOW_SALARYHIS", "SAH_KF_YEAR","VARCHAR", "4", "NULL");
			add_field("PER_WORKFLOW_SALARYHIS", "SAH_KF_CYCLE","SINGLE", "1", "NULL");
			add_field("PER_WORKFLOW_SALARYHIS", "SAH_TOTAL_SCORE","NUMBER", "5,2", "NULL");
			add_field("PER_WORKFLOW_SALARYHIS", "SAH_PERCENT_UP","NUMBER", "7,4", "NULL");
			add_field("PER_WORKFLOW_SALARYHIS", "SAH_LAST_SALARY","CHAR", "1", "NULL");
			add_field("PER_WORKFLOW_SALARYHIS", "SM_CODE","VARCHAR", "10", "NULL");
			add_field("PER_WORKFLOW_SALARYHIS", "SAH_SEQ_NO","INTEGER2", "5", "NULL");
			add_field("PER_WORKFLOW_SALARYHIS", "SAH_ORG_DOPA_CODE","VARCHAR", "20", "NULL");
			add_field("PER_WORKFLOW_SALARYHIS", "SAH_OLD_SALARY","NUMBER", "16,2", "NULL");
			add_field("PER_WORKFLOW_SALARYHIS", "SAH_CMD_SEQ","INTEGER", "6", "NULL");
			add_field("PER_WORKFLOW_SALARYHIS", "SAH_WF_STATUS","VARCHAR", "2", "NULL");

			if($DPISDB=="odbc") 
				$cmd = " SELECT * INTO PER_WORKFLOW_ABILITY FROM PER_ABILITY WHERE UPDATE_DATE = 'x' ";
			elseif($DPISDB=="oci8" || $DPISDB=="mysql") 
				$cmd = " CREATE TABLE PER_WORKFLOW_ABILITY AS SELECT * FROM PER_ABILITY WHERE UPDATE_DATE = 'x' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_WORKFLOW_ABILITY ADD CONSTRAINT PK_PER_WORKFLOW_ABILITY  
							  PRIMARY KEY (ABI_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_WORKFLOW_ABILITY", "ABI_WF_STATUS","VARCHAR", "2", "NULL");

			if($DPISDB=="odbc") 
				$cmd = " SELECT * INTO PER_WORKFLOW_SPECIAL_SKILL FROM PER_SPECIAL_SKILL WHERE UPDATE_DATE = 'x' ";
			elseif($DPISDB=="oci8" || $DPISDB=="mysql") 
				$cmd = " CREATE TABLE PER_WORKFLOW_SPECIAL_SKILL AS SELECT * FROM PER_SPECIAL_SKILL WHERE UPDATE_DATE = 'x' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_WORKFLOW_SPECIAL_SKILL ADD CONSTRAINT PK_PER_WORKFLOW_SPECIAL_SKILL  
							  PRIMARY KEY (SPS_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_WORKFLOW_SPECIAL_SKILL", "SPS_WF_STATUS","VARCHAR", "2", "NULL");

			if($DPISDB=="odbc") 
				$cmd = " SELECT * INTO PER_WORKFLOW_ABSENTHIS FROM PER_ABSENTHIS WHERE UPDATE_DATE = 'x' ";
			elseif($DPISDB=="oci8" || $DPISDB=="mysql") 
				$cmd = " CREATE TABLE PER_WORKFLOW_ABSENTHIS AS SELECT * FROM PER_ABSENTHIS WHERE UPDATE_DATE = 'x' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_WORKFLOW_ABSENTHIS ADD CONSTRAINT PK_PER_WORKFLOW_ABSENTHIS  
							  PRIMARY KEY (ABS_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_WORKFLOW_ABSENTHIS", "ABS_WF_STATUS","VARCHAR", "2", "NULL");

			if($DPISDB=="odbc") 
				$cmd = " SELECT * INTO PER_WORKFLOW_PUNISHMENT FROM PER_PUNISHMENT WHERE UPDATE_DATE = 'x' ";
			elseif($DPISDB=="oci8" || $DPISDB=="mysql") 
				$cmd = " CREATE TABLE PER_WORKFLOW_PUNISHMENT AS SELECT * FROM PER_PUNISHMENT WHERE UPDATE_DATE = 'x' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_WORKFLOW_PUNISHMENT ADD CONSTRAINT PK_PER_WORKFLOW_PUNISHMENT  
							  PRIMARY KEY (PUN_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_WORKFLOW_PUNISHMENT", "PUN_WF_STATUS","VARCHAR", "2", "NULL");

			if($DPISDB=="odbc") 
				$cmd = " SELECT * INTO PER_WORKFLOW_SERVICEHIS FROM PER_SERVICEHIS WHERE UPDATE_DATE = 'x' ";
			elseif($DPISDB=="oci8" || $DPISDB=="mysql") 
				$cmd = " CREATE TABLE PER_WORKFLOW_SERVICEHIS AS SELECT * FROM PER_SERVICEHIS WHERE UPDATE_DATE = 'x' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_WORKFLOW_SERVICEHIS ADD CONSTRAINT PK_PER_WORKFLOW_SERVICEHIS  
							  PRIMARY KEY (SRH_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_WORKFLOW_SERVICEHIS", "SRH_WF_STATUS","VARCHAR", "2", "NULL");

			if($DPISDB=="odbc") 
				$cmd = " SELECT * INTO PER_WORKFLOW_TIMEHIS FROM PER_TIMEHIS WHERE UPDATE_DATE = 'x' ";
			elseif($DPISDB=="oci8" || $DPISDB=="mysql") 
				$cmd = " CREATE TABLE PER_WORKFLOW_TIMEHIS AS SELECT * FROM PER_TIMEHIS WHERE UPDATE_DATE = 'x' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_WORKFLOW_TIMEHIS ADD CONSTRAINT PK_PER_WORKFLOW_TIMEHIS  
							  PRIMARY KEY (TIMEH_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_WORKFLOW_TIMEHIS", "TIMEH_WF_STATUS","VARCHAR", "2", "NULL");

			if($DPISDB=="odbc") 
				$cmd = " SELECT * INTO PER_WORKFLOW_FAMILY FROM PER_FAMILY WHERE UPDATE_DATE = 'x' ";
			elseif($DPISDB=="oci8" || $DPISDB=="mysql") 
				$cmd = " CREATE TABLE PER_WORKFLOW_FAMILY AS SELECT * FROM PER_FAMILY WHERE UPDATE_DATE = 'x' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_WORKFLOW_FAMILY ADD CONSTRAINT PK_PER_WORKFLOW_FAMILY  
							  PRIMARY KEY (FML_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_WORKFLOW_FAMILY", "FML_WF_STATUS","VARCHAR", "2", "NULL");

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'personal_positionhis_check.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'personal_salaryhis_check.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 
							  WHERE LINKTO_WEB = 'personal_check.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();
			if (!$count_data) {
				$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV2 ";
				$db->send_cmd($cmd);
				//$db->show_error();
				$data = $db->get_array();
				$MAX_ID = $data[MAX_ID] + 1;
				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'TH', $MAX_ID, 23, 'P0123 ตรวจสอบประวัติการดำรงตำแหน่ง/การรับเงินเดือน', 'S', 'W', 'personal_check.html', 0, 35, 241, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 23, 'P0123 ตรวจสอบประวัติการดำรงตำแหน่ง/การรับเงินเดือน', 'S', 'W', 'personal_check.html', 0, 35, 241, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV2 
							  WHERE MENU_LABEL = 'P0124 ค้นหาประวัติบุคลากร' AND LINKTO_WEB = 'personal_check.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 
							  WHERE LINKTO_WEB = 'personal_master_desc_excel.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();
			if (!$count_data) {
				$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV2 ";
				$db->send_cmd($cmd);
				//$db->show_error();
				$data = $db->get_array();
				$MAX_ID = $data[MAX_ID] + 1;
				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'TH', $MAX_ID, 24, 'P0124 ค้นหาประวัติบุคลากร', 'S', 'W', 'personal_master_desc_excel.html', 0, 35, 241, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 24, 'P0124 ค้นหาประวัติบุคลากร', 'S', 'W', 'personal_master_desc_excel.html', 0, 35, 241, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			add_field("PER_EDUCATE", "CT_CODE_EDU","VARCHAR", "10", "NULL");
			add_field("PER_WORKFLOW_EDUCATE", "CT_CODE_EDU","VARCHAR", "10", "NULL");

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_WORKFLOW_EDUCATE, PER_INSTITUTE SET PER_WORKFLOW_EDUCATE.CT_CODE_EDU = 
								  PER_INSTITUTE.CT_CODE WHERE PER_INSTITUTE.INS_CODE = PER_WORKFLOW_EDUCATE.INS_CODE AND 
								  PER_WORKFLOW_EDUCATE.INS_CODE IS NOT NULL AND PER_WORKFLOW_EDUCATE.CT_CODE_EDU IS NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_WORKFLOW_EDUCATE A SET A.CT_CODE_EDU = 
								  (SELECT B.CT_CODE FROM PER_INSTITUTE B WHERE A.INS_CODE = B.INS_CODE) 
								  WHERE A.INS_CODE IS NOT NULL AND A.CT_CODE_EDU IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_WORKFLOW_EDUCATE SET CT_CODE_EDU = '140' WHERE CT_CODE_EDU IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMGROUP (COM_GROUP, CG_NAME, CG_ACTIVE, UPDATE_USER, UPDATE_DATE, CG_SEQ_NO)
							  VALUES ('523', 'คำสั่งตัดโอนอัตราเงินเดือน', 1, $SESS_USERID, '$UPDATE_DATE', 23) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_COMGROUP (COM_GROUP, CG_NAME, CG_ACTIVE, UPDATE_USER, UPDATE_DATE, CG_SEQ_NO)
							  VALUES ('531', 'ประกาศ', 1, $SESS_USERID, '$UPDATE_DATE', 31) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_COMGROUP (COM_GROUP, CG_NAME, CG_ACTIVE, UPDATE_USER, UPDATE_DATE, CG_SEQ_NO)
							  VALUES ('532', 'สัญญา', 1, $SESS_USERID, '$UPDATE_DATE', 32) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO, COM_ACTIVE)
							VALUES ('5230', 'คส. 23', 'คำสั่งตัดโอนอัตราเงินเดือน', '523', 98, 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 set MENU_LABEL = 'P1112 ระบบสารสนเทศพนักงานราชการ (GEIS)' WHERE LINKTO_WEB = 'rpt_geis.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'set_initial_site.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();
			if (!$count_data) {
				$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV1 ";
				$db->send_cmd($cmd);
				//$db->show_error();
				$data = $db->get_array();
				$MAX_ID = $data[MAX_ID] + 1;
				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
								  UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'TH', $MAX_ID, 14, 'C14 กำหนดรูปแบบการออกแบบหน้าจอ', 'S', 'W', 'set_initial_site.html', 0, 1, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
								  UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 14, 'C14 กำหนดรูปแบบการออกแบบหน้าจอ', 'S', 'W', 'set_initial_site.html', 0, 1, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			add_field("PER_PERSONAL", "PER_CONTACT_COUNT","INTEGER2", "2", "NULL");
			add_field("PER_PERSONAL", "PER_DISABILITY","SINGLE", "1", "NULL");

			$cmd = " UPDATE PER_PERSONAL SET PER_DISABILITY =  1 WHERE PER_DISABILITY IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV1 set MENU_LABEL = 'P1001 หนังสือรับรอง/นามบัตร' WHERE MENU_LABEL = 'P1001 หนังสือรับรอง' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 
							  WHERE LINKTO_WEB = 'per_namecard_design.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();
			if (!$count_data) {
				$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV2 ";
				$db->send_cmd($cmd);
				//$db->show_error();
				$data = $db->get_array();
				$MAX_ID = $data[MAX_ID] + 1;
				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'TH', $MAX_ID, 2, 'P1002 ออกแบบนามบัตร', 'S', 'W', 'per_namecard_design.html', 0, 35, 250, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 2, 'P1002 ออกแบบนามบัตร', 'S', 'W', 'per_namecard_design.html', 0, 35, 250, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 
							  WHERE LINKTO_WEB = 'per_namecard_print.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();
			if (!$count_data) {
				$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV2 ";
				$db->send_cmd($cmd);
				//$db->show_error();
				$data = $db->get_array();
				$MAX_ID = $data[MAX_ID] + 1;
				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'TH', $MAX_ID, 3, 'P1003 พิมพ์นามบัตร', 'S', 'W', 'per_namecard_print.html', 0, 35, 250, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 3, 'P1003 พิมพ์นามบัตร', 'S', 'W', 'per_namecard_print.html', 0, 35, 250, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " update PER_CONTROL set CTRL_ALTER = 10 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
?>