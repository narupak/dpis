<?php 
$cmd = " DROP TABLE PER_SALARY_FORMULA ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET FLAG_SHOW = 'H' WHERE LINKTO_WEB = 'command_distribute_position.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();
	
			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET FLAG_SHOW = 'H' WHERE LINKTO_WEB = 'command_usual_retirepos.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();
	
			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET FLAG_SHOW = 'H' WHERE LINKTO_WEB = 'command_extra_retirepos.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();
	
			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET FLAG_SHOW = 'H' WHERE LINKTO_WEB = 'command_assign_position.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();
	
			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET FLAG_SHOW = 'H' WHERE LINKTO_WEB = 'structure_assign_person_list.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();
	
			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV1 SET FLAG_SHOW = 'H' WHERE MENU_LABEL = 'S03 เงื่อนไขการมอบอำนาจ' ";
			$db->send_cmd($cmd);
			//$db->show_error();
	
			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_ABILITY ALTER ABI_DESC MEMO ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_ABILITY MODIFY ABI_DESC VARCHAR2(2000) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_ABILITY MODIFY ABI_DESC TEXT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_PROVINCE (PV_CODE, PV_NAME, CT_CODE, PV_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('3800', 'บึงกาฬ', '140', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_POS_MGTSALARY ALTER EX_CODE VARCHAR(10) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POS_MGTSALARY MODIFY EX_CODE VARCHAR2(10) ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_POS_MGTSALARY MODIFY EX_CODE VARCHAR(10) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($db_type=="odbc") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_LEFT_X INTEGER2 ";
			elseif($db_type=="oci8") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_LEFT_X NUMBER(5) ";
			elseif($db_type=="mysql") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_LEFT_X SMALLINT(5) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			if($db_type=="odbc") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_LEFT_Y INTEGER2 ";
			elseif($db_type=="oci8") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_LEFT_Y NUMBER(5) ";
			elseif($db_type=="mysql") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_LEFT_Y SMALLINT(5) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			if($db_type=="odbc") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_LEFT_W INTEGER2 ";
			elseif($db_type=="oci8") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_LEFT_W NUMBER(5) ";
			elseif($db_type=="mysql") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_LEFT_W SMALLINT(5) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			if($db_type=="odbc") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_LEFT_H INTEGER2 ";
			elseif($db_type=="oci8") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_LEFT_H NUMBER(5) ";
			elseif($db_type=="mysql") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_LEFT_H SMALLINT(5) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			if($db_type=="odbc") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_LEFT_ALPHA NUMBER ";
			elseif($db_type=="oci8") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_LEFT_ALPHA NUMBER(4,2) ";
			elseif($db_type=="mysql")
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_LEFT_ALPHA DECIMAL(4,2) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			if($db_type=="odbc") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_X INTEGER2 ";
			elseif($db_type=="oci8") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_X NUMBER(5) ";
			elseif($db_type=="mysql") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_X SMALLINT(5) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			if($db_type=="odbc") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_Y INTEGER2 ";
			elseif($db_type=="oci8") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_Y NUMBER(5) ";
			elseif($db_type=="mysql") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_Y SMALLINT(5) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			if($db_type=="odbc") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_W INTEGER2 ";
			elseif($db_type=="oci8") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_W NUMBER(5) ";
			elseif($db_type=="mysql") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_W SMALLINT(5) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			if($db_type=="odbc") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_H INTEGER2 ";
			elseif($db_type=="oci8") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_H NUMBER(5) ";
			elseif($db_type=="mysql") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_H SMALLINT(5) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			if($db_type=="odbc") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_ALPHA NUMBER ";
			elseif($db_type=="oci8") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_ALPHA NUMBER(4,2) ";
			elseif($db_type=="mysql")
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_ALPHA DECIMAL(4,2) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			if($db_type=="odbc") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_RIGHT_X INTEGER2 ";
			elseif($db_type=="oci8") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_RIGHT_X NUMBER(5) ";
			elseif($db_type=="mysql") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_RIGHT_X SMALLINT(5) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			if($db_type=="odbc") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_RIGHT_Y INTEGER2 ";
			elseif($db_type=="oci8") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_RIGHT_Y NUMBER(5) ";
			elseif($db_type=="mysql") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_RIGHT_Y SMALLINT(5) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			if($db_type=="odbc") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_RIGHT_W INTEGER2 ";
			elseif($db_type=="oci8") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_RIGHT_W NUMBER(5) ";
			elseif($db_type=="mysql") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_RIGHT_W SMALLINT(5) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			if($db_type=="odbc") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_RIGHT_H INTEGER2 ";
			elseif($db_type=="oci8") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_RIGHT_H NUMBER(5) ";
			elseif($db_type=="mysql") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_RIGHT_H SMALLINT(5) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			if($db_type=="odbc") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_RIGHT_ALPHA NUMBER ";
			elseif($db_type=="oci8") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_RIGHT_ALPHA NUMBER(4,2) ";
			elseif($db_type=="mysql")
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_RIGHT_ALPHA DECIMAL(4,2) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			if($db_type=="odbc") 
				$cmd = " ALTER TABLE SITE_INFO ADD HEAD_HEIGHT INTEGER2 ";
			elseif($db_type=="oci8") 
				$cmd = " ALTER TABLE SITE_INFO ADD HEAD_HEIGHT NUMBER(5) ";
			elseif($db_type=="mysql") 
				$cmd = " ALTER TABLE SITE_INFO ADD HEAD_HEIGHT SMALLINT(5) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE PER_TRAINING SET TRN_BOOK_DATE = NULL WHERE TRN_BOOK_DATE = '-543--' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV1 SET MENU_LABEL = 'C15 นำเข้าฐานข้อมูล ปค.' 
							  WHERE LINKTO_WEB = 'select_dopa_excel.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " SELECT PER_PICSEQ FROM PER_PERSONALPIC ";
			$count_data = $db_dpis->send_cmd($cmd);

			if (!$count_data) {
				$cmd = " DROP TABLE PER_PERSONALPIC ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				if($DPISDB=="odbc") 
					$cmd = " CREATE TABLE PER_PERSONALPIC(
					PER_ID INTEGER NOT NULL,	
					PER_PICSEQ INTEGER NOT NULL,
					PER_CARDNO VARCHAR(13) NULL,	
					PER_GENNAME VARCHAR(20) NULL,
					PER_PICPATH VARCHAR(255) NULL,
					PER_PICSAVEDATE VARCHAR(19) NULL,
					PIC_SHOW CHAR(1) NULL,
					PIC_REMARK MEMO NULL,
					PIC_SERVER_ID INTEGER NOT NULL,
					PIC_SIGN INTEGER NULL ,
					UPDATE_USER INTEGER NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_PERSONALPIC PRIMARY KEY (PER_ID, PER_PICSEQ)) ";
				elseif($DPISDB=="oci8") 
					$cmd = " CREATE TABLE PER_PERSONALPIC(
					PER_ID NUMBER(10) NOT NULL,	
					PER_PICSEQ NUMBER(10) NOT NULL,
					PER_CARDNO VARCHAR2(13) NULL,	
					PER_GENNAME VARCHAR2(20) NULL,
					PER_PICPATH VARCHAR2(255) NULL,
					PER_PICSAVEDATE VARCHAR2(19) NULL,
					PIC_SHOW CHAR(1) NULL,
					PIC_REMARK VARCHAR2(2000) NULL,
					PIC_SERVER_ID NUMBER(10) NOT NULL,
					PIC_SIGN NUMBER(5) NULL,
					UPDATE_USER NUMBER(11) NOT NULL,
					UPDATE_DATE VARCHAR2(19) NOT NULL,		
					CONSTRAINT PK_PER_PERSONALPIC PRIMARY KEY (PER_ID, PER_PICSEQ)) ";
				elseif($DPISDB=="mysql")
					$cmd = " CREATE TABLE PER_PERSONALPIC(
					PER_ID INTEGER(10) NOT NULL,	
					PER_PICSEQ INTEGER(10) NOT NULL,
					PER_CARDNO VARCHAR(13) NULL,	
					PER_GENNAME VARCHAR(20) NULL,
					PER_PICPATH VARCHAR(255) NULL,
					PER_PICSAVEDATE VARCHAR(19) NULL,
					PIC_SHOW CHAR(1) NULL,
					PIC_REMARK TEXT NULL,
					PIC_SERVER_ID INTEGER(10) NOT NULL,	
					PIC_SIGN INTEGER(5) NOT NULL,
					UPDATE_USER INTEGER(11) NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_PERSONALPIC PRIMARY KEY (PER_ID, PER_PICSEQ)) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " SELECT PER_ID, PER_CARDNO, PER_NAME, PER_SURNAME FROM PER_PERSONAL WHERE PER_STATUS <> 3 ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
				while($data = $db_dpis->get_array()){
					$PER_ID = $data[PER_ID];
					$PER_CARDNO = $data[PER_CARDNO];
					$PER_GENNAME = personal_gen_name($data[PER_NAME],$data[PER_SURNAME]);
					$PER_GENNAME = "9999".$PER_GENNAME;

					if ($PER_CARDNO) {
						$cmd = " INSERT INTO PER_PERSONALPIC (PER_ID, PER_PICSEQ, PER_CARDNO, PER_GENNAME, 
										PER_PICPATH, PER_PICSAVEDATE, PIC_SHOW, PIC_REMARK,PIC_SERVER_ID, PIC_SIGN, UPDATE_USER, UPDATE_DATE)
										VALUES ($PER_ID, 1, '$PER_CARDNO', '$PER_GENNAME', '../attachment/pic_personal/', '$UPDATE_DATE', 1, 
										NULL, 0, 0 ,$SESS_USERID, '$UPDATE_DATE') ";
						$db_dpis1->send_cmd($cmd);
						//$db_dpis1->show_error();
					}
				} // end while						
				if (is_dir($SRC_DIR)) trace_dir($SRC_DIR);
			}

			add_field("PER_REQ3_DTL", "ORG_ID_3","INTEGER", "10", "NULL");
			add_field("PER_REQ3_DTL", "ORG_ID_4","INTEGER", "10", "NULL");
			add_field("PER_REQ3_DTL", "ORG_ID_5","INTEGER", "10", "NULL");
			add_field("PER_REQ2_DTL", "ORG_ID_3","INTEGER", "10", "NULL");
			add_field("PER_REQ2_DTL", "ORG_ID_4","INTEGER", "10", "NULL");
			add_field("PER_REQ2_DTL", "ORG_ID_5","INTEGER", "10", "NULL");
			add_field("PER_REQ1_DTL1", "ORG_ID_3","INTEGER", "10", "NULL");
			add_field("PER_REQ1_DTL1", "ORG_ID_4","INTEGER", "10", "NULL");
			add_field("PER_REQ1_DTL1", "ORG_ID_5","INTEGER", "10", "NULL");
			add_field("PER_POSITIONHIS", "POH_UNDER_ORG3","VARCHAR", "255", "NULL");
			add_field("PER_POSITIONHIS", "POH_UNDER_ORG4","VARCHAR", "255", "NULL");
			add_field("PER_POSITIONHIS", "POH_UNDER_ORG5","VARCHAR", "255", "NULL");
			add_field("PER_POSITIONHIS", "POH_ASS_ORG3","VARCHAR", "100", "NULL");
			add_field("PER_POSITIONHIS", "POH_ASS_ORG4","VARCHAR", "100", "NULL");
			add_field("PER_POSITIONHIS", "POH_ASS_ORG5","VARCHAR", "100", "NULL");
			add_field("PER_WORKFLOW_POSITIONHIS", "POH_UNDER_ORG3","VARCHAR", "255", "NULL");
			add_field("PER_WORKFLOW_POSITIONHIS", "POH_UNDER_ORG4","VARCHAR", "255", "NULL");
			add_field("PER_WORKFLOW_POSITIONHIS", "POH_UNDER_ORG5","VARCHAR", "255", "NULL");
			add_field("PER_WORKFLOW_POSITIONHIS", "POH_ASS_ORG3","VARCHAR", "100", "NULL");
			add_field("PER_WORKFLOW_POSITIONHIS", "POH_ASS_ORG4","VARCHAR", "100", "NULL");
			add_field("PER_WORKFLOW_POSITIONHIS", "POH_ASS_ORG5","VARCHAR", "100", "NULL");

			$cmd = " UPDATE PER_POSITIONHIS SET POH_SEQ_NO = 1 WHERE POH_SEQ_NO IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_SALARYHIS SET SAH_SEQ_NO = 1 WHERE SAH_SEQ_NO IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_POSITION ALTER CL_NAME NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POSITION MODIFY CL_NAME NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_POSITION MODIFY CL_NAME NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_POS_EMP ALTER POEM_NO VARCHAR(15) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POS_EMP MODIFY POEM_NO VARCHAR2(15) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_POS_EMP MODIFY POEM_NO VARCHAR(15) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_POS_EMPSER ALTER POEMS_NO VARCHAR(15) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POS_EMPSER MODIFY POEMS_NO VARCHAR2(15) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_POS_EMPSER MODIFY POEMS_NO VARCHAR(15) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 
							  WHERE LINKTO_WEB = 'master_table_district.html?table=PER_DISTRICT' ";
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
								  VALUES (1, 'TH', $MAX_ID, 12, 'M0112 ตำบล', 'S', 'W', 'master_table_district.html?table=PER_DISTRICT', 0, 9, 296, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 12, 'M0112 ตำบล', 'S', 'W', 'master_table_district.html?table=PER_DISTRICT', 0, 9, 296, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " UPDATE PER_COMPENSATION_TEST SET CP_CONFIRM = 0 WHERE CP_CONFIRM IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_POSITIONHIS", "POH_REMARK1","MEMO", "2000", "NULL");
			add_field("PER_POSITIONHIS", "POH_REMARK2","MEMO", "2000", "NULL");

			$cmd = " UPDATE PER_SALARYHIS SET SM_CODE = '1' WHERE MOV_CODE IN ('21310', '21351') AND SM_CODE IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_SALARYHIS SET SM_CODE = '2' WHERE MOV_CODE IN ('21320', '21352') AND SM_CODE IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_SALARYHIS SET SM_CODE = '3' WHERE MOV_CODE IN ('21330', '21353') AND SM_CODE IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_SALARYHIS SET SM_CODE = '4' WHERE MOV_CODE IN ('21340', '21354') AND SM_CODE IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET FLAG_SHOW = 'H' 
							WHERE LINKTO_WEB in ('rpt_R004004.html', 'rpt_R004005.html', 'rpt_R006012.html', 'rpt_R006013.html', 'rpt_R010001.html', 'rpt_R010002.html', 'rpt_R010003.html', 'rpt_R010004.html') ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " CREATE INDEX IDX_PER_SALARYHIS_2 ON PER_SALARYHIS (SAH_EFFECTIVEDATE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_POSITIONHIS_6 ON PER_POSITIONHIS (POH_EFFECTIVEDATE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="oci8") {
				$cmd = " SELECT TNAME FROM TAB WHERE TABTYPE = 'TABLE' AND TNAME LIKE 'PER_%' ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
				while ( $data = $db_dpis->get_array() ) {
					$TNAME = trim($data[TNAME]);
					$cmd = " ALTER TABLE $TNAME MODIFY UPDATE_USER NUMBER(11) ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
				} // end while
			} // end if

			add_field("PER_PERSONAL", "PER_UNION","SINGLE", "1", "NULL");
			add_field("PER_PERSONAL", "PER_UNIONDATE","VARCHAR", "19", "NULL");
			add_field("PER_POSITION", "POS_NO_NAME","VARCHAR", "15", "NULL");
			add_field("PER_ORDER_DTL", "ORD_POS_NO_NAME","VARCHAR", "15", "NULL");
			add_field("PER_REQ3_DTL", "REQ_POS_NO_NAME","VARCHAR", "15", "NULL");
			add_field("PER_REQ2_DTL", "REQ_POS_NO_NAME","VARCHAR", "15", "NULL");
			add_field("PER_REQ1_DTL1", "REQ_POS_NO_NAME","VARCHAR", "15", "NULL");
			add_field("PER_POSITIONHIS", "POH_POS_NO_NAME","VARCHAR", "15", "NULL");
			add_field("PER_SALARYHIS", "SAH_POS_NO_NAME","VARCHAR", "15", "NULL");
			add_field("PER_ACTINGHIS", "ACTH_POS_NO_NAME","VARCHAR", "15", "NULL");
			add_field("PER_ACTINGHIS", "ACTH_POS_NO_NAME_ASSIGN","VARCHAR", "15", "NULL");
			add_field("PER_WORKFLOW_POSITIONHIS", "POH_POS_NO_NAME","VARCHAR", "15", "NULL");
			add_field("PER_WORKFLOW_SALARYHIS", "SAH_POS_NO_NAME","VARCHAR", "15", "NULL");
			add_field("PER_POS_EMP", "POEM_NO_NAME","VARCHAR", "15", "NULL");
			add_field("PER_POS_EMPSER", "POEMS_NO_NAME","VARCHAR", "15", "NULL");

			$cmd = " DELETE FROM PER_POSITION_COMPETENCE WHERE DEPARTMENT_ID = 0 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_PERSONAL", "PER_JOB","MEMO", "2000", "NULL");

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE  PER_EDUCNAME DROP CONSTRAINT INXU2_PER_EDUCNAME ";
			elseif($DPISDB=="oci8") 
				$cmd = " DROP INDEX INXU2_PER_EDUCNAME ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE  PER_EDUCMAJOR DROP CONSTRAINT INXU1_PER_EDUCMAJOR ";
			elseif($DPISDB=="oci8") 
				$cmd = " DROP INDEX INXU1_PER_EDUCMAJOR ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX INXU2_PER_EDUCNAME ON PER_EDUCNAME (EN_NAME) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX INXU1_PER_EDUCMAJOR ON PER_EDUCMAJOR (EM_NAME) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_FAMILY ADD CONSTRAINT FK1_PER_FAMILY 
							  FOREIGN KEY (PER_ID) REFERENCES PER_PERSONAL (PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_KPI", "KPI_EVALUATE1","SINGLE", "1", "NULL");
			add_field("PER_KPI", "KPI_EVALUATE2","SINGLE", "1", "NULL");
			add_field("PER_KPI", "KPI_EVALUATE3","SINGLE", "1", "NULL");

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'user_profile.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'convert_pic_new.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'select_database_excel.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'setscreen_form.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " SELECT PL_CODE FROM PER_POS_LEVEL_SALARY ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) {
				if($DPISDB=="odbc") 
					$cmd = " CREATE TABLE PER_POS_LEVEL_SALARY(
					PN_CODE VARCHAR(10) NOT NULL,	
					LEVEL_NO VARCHAR(10) NOT NULL,	
					MIN_SALARY NUMBER NOT NULL,	
					MAX_SALARY NUMBER NOT NULL,		
					POSITION_SPEC MEMO NULL,
					UPDATE_USER INTEGER NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_POS_LEVEL_SALARY PRIMARY KEY (PN_CODE, LEVEL_NO)) ";
				elseif($DPISDB=="oci8") 
					$cmd = " CREATE TABLE PER_POS_LEVEL_SALARY(
					PN_CODE VARCHAR2(10) NOT NULL,	
					LEVEL_NO VARCHAR2(10) NOT NULL,	
					MIN_SALARY NUMBER(16,2) NOT NULL,	
					MAX_SALARY NUMBER(16,2) NOT NULL,	
					POSITION_SPEC VARCHAR2(2000) NULL,
					UPDATE_USER NUMBER(11) NOT NULL,
					UPDATE_DATE VARCHAR2(19) NOT NULL,		
					CONSTRAINT PK_PER_POS_LEVEL_SALARY PRIMARY KEY (PN_CODE, LEVEL_NO)) ";
				elseif($DPISDB=="mysql")
					$cmd = " CREATE TABLE PER_POS_LEVEL_SALARY(
					PN_CODE VARCHAR(10) NOT NULL,	
					LEVEL_NO VARCHAR(10) NOT NULL,	
					MIN_SALARY DECIMAL(16,2) NOT NULL,	
					MAX_SALARY DECIMAL(16,2) NOT NULL,	
					POSITION_SPEC TEXT NULL,
					UPDATE_USER INTEGER(11) NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_POS_LEVEL_SALARY PRIMARY KEY (PN_CODE, LEVEL_NO)) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 
								  WHERE LINKTO_WEB = 'master_table_pos_level_salary.html?table=PER_POS_LEVELSALARY' ";
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
									  VALUES (1, 'TH', $MAX_ID, 9, 'M0409 ตำแหน่งและอัตราค่าจ้างของลูกจ้างประจำ', 'S', 'W', 'master_table_pos_level_salary.html?table=PER_POS_LEVELSALARY', 0, 9, 
									  297, $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();

					$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
									  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
									  CREATE_BY, UPDATE_DATE, UPDATE_BY)
									  VALUES (1, 'EN', $MAX_ID, 9, 'M0409 ตำแหน่งและอัตราค่าจ้างของลูกจ้างประจำ', 'S', 'W', 'master_table_pos_level_salary.html?table=PER_POS_LEVELSALARY', 0, 9, 
									  297, $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();
				}
			}

// DPIS 5 *****************************************************************
			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'data_move_req_inquire.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'data_scholar_inquire.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'data_promote_e_p_quality.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'train_plan.html' WHERE LINKTO_WEB = 'train_plan_tab.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'master_table_extra_income_type.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 
							  WHERE LINKTO_WEB = 'data_salpromote_comdtl.html' ";
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
								  VALUES (1, 'TH', $MAX_ID, 4, 'P0404 บัญชีแนบท้ายคำสั่งเลื่อนขั้นเงินเดือน', 'S', 'W', 'data_salpromote_comdtl.html', 0, 35, 244, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 4, 'P0404 บัญชีแนบท้ายคำสั่งเลื่อนขั้นเงินเดือน', 'S', 'W', 'data_salpromote_comdtl.html', 0, 35, 244, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SEQ_NO, EX_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  SELECT 'E'||TRIM(EXIN_CODE), EXIN_NAME, EXIN_SEQ_NO, EXIN_ACTIVE, UPDATE_USER, UPDATE_DATE FROM PER_EXTRA_INCOME_TYPE ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();

			$cmd = " select max(EXH_ID) as max_id from PER_EXTRAHIS ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$EXH_ID = $data[max_id] + 1;
		
			$cmd = " SELECT PER_ID, EXINH_EFFECTIVEDATE, EXIN_CODE, EXINH_AMT, EXINH_ENDDATE, PER_CARDNO, UPDATE_USER, UPDATE_DATE 
							  FROM PER_EXTRA_INCOMEHIS ORDER BY EXINH_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			while($data = $db_dpis->get_array()){
				$PER_ID = $data[PER_ID];
				$EXINH_EFFECTIVEDATE = trim($data[EXINH_EFFECTIVEDATE]);
				$EXIN_CODE = "E" . trim($data[EXIN_CODE]);
				$EXINH_AMT = $data[EXINH_AMT];
				$EXINH_ENDDATE = trim($data[EXINH_ENDDATE]);
				$PER_CARDNO = (trim($data[PER_CARDNO]))? "'".trim($data[PER_CARDNO])."'" : "NULL";		
				$UPDATE_USER = $data[UPDATE_USER];
				$UPDATE_DATE = trim($data[UPDATE_DATE]);
				if (!$EXINH_AMT) $EXINH_AMT = "NULL";

				$cmd = " INSERT INTO PER_EXTRAHIS (EXH_ID, PER_ID, EXH_EFFECTIVEDATE, EX_CODE, EXH_AMT, EXH_ENDDATE, PER_CARDNO, 
						UPDATE_USER, UPDATE_DATE)
						values ($EXH_ID, $PER_ID, '$EXINH_EFFECTIVEDATE', '$EXIN_CODE', $EXINH_AMT, '$EXINH_ENDDATE', $PER_CARDNO, 
						$UPDATE_USER, '$UPDATE_DATE') ";
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
				$EXH_ID++;
			}

			$cmd = " DELETE FROM PER_EXTRA_INCOMEHIS ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_ORG DROP CONSTRAINT FK4_PER_ORG ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_ORG_ASS DROP CONSTRAINT FK4_PER_ORG_ASS ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_SUM_DTL1 DROP CONSTRAINT FK2_PER_SUM_DTL1 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_ORG DROP CONSTRAINT FK3_PER_ORG ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_ORG_ASS DROP CONSTRAINT FK3_PER_ORG_ASS ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_SUM_DTL5 DROP CONSTRAINT FK2_PER_SUM_DTL5 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_ORG DROP COLUMN OS_CODE ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_ORG DROP COLUMN OP_CODE ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_ORG_ASS DROP COLUMN OS_CODE ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_ORG_ASS DROP COLUMN OP_CODE ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_SUM_DTL1 DROP COLUMN OS_CODE ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_SUM_DTL5 DROP COLUMN OP_CODE ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
/*
			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'master_table_layer.html?table=PER_LAYER_NEW' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'master_table_layeremp.html?table=PER_LAYEREMP_NEW' ";
			$db->send_cmd($cmd);
			//$db->show_error();
*/
			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'master_table.html?table=PER_ORG_STAT' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'master_table.html?table=PER_ORG_PROVINCE' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'master_table_pos_mgtsalary.html' AND MENU_LABEL = 'S0205 เงินประจำตำแหน่ง' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_pos_mgtsalary.html', MENU_LABEL = 'M0402 เงินตามตำแหน่ง'
							WHERE LINKTO_WEB = 'master_table_mgtsalary.html?table=PER_MGTSALARY' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'data_bonusquota.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'data_bonuspromote.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " SELECT PARENT_ID_LV0, PARENT_ID_LV1 FROM BACKOFFICE_MENU_BAR_LV2 
							  WHERE LINKTO_WEB = 'master_table_scholarship.html?table=PER_SCHOLARSHIP' AND PARENT_ID_LV0 != 9 ";
			$count_data = $db->send_cmd($cmd);
			if ($count_data) {
				$data = $db->get_array();
				$PARENT_ID_LV0 = $data[PARENT_ID_LV0];
				$PARENT_ID_LV1 = $data[PARENT_ID_LV1];

				$cmd = " UPDATE USER_PRIVILEGE SET MENU_ID_LV0 = 9, MENU_ID_LV1 = 293
								WHERE MENU_ID_LV0 = $PARENT_ID_LV0 AND MENU_ID_LV1 = $PARENT_ID_LV1 ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET MENU_ORDER = 9, MENU_LABEL = 'M0709 ทุนการศึกษา', PARENT_ID_LV0 = 9, PARENT_ID_LV1 = 293
								WHERE LINKTO_WEB = 'master_table_scholarship.html?table=PER_SCHOLARSHIP' ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'data_decor.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'master_table_decorcond.html?table=PER_DECORCOND' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV1 WHERE MENU_LABEL = 'P09 ขอพระราชทานเครื่องราชฯ' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_gfmis.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'convert_todbf.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET MENU_LABEL = 'C0702 เรียกคืนข้อมูล (เปลี่ยนประเภทฐานข้อมูล)'	WHERE LINKTO_WEB = 'manage_data_restore.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET MENU_LABEL = 'C0703 สำเนาฐานข้อมูล (เปลี่ยนประเภทฐานข้อมูล)'	WHERE LINKTO_WEB = 'manage_data_backup.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'select_database_org.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV1 SET MENU_LABEL = 'C12 ถ่ายโอนข้อมูลกรม -> จังหวัด'	WHERE LINKTO_WEB = 'dpis_to_text.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV1 SET MENU_LABEL = 'C13 รับโอนข้อมูลจากกรม' WHERE LINKTO_WEB = 'text_to_ppis.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV1 SET MENU_LABEL = 'C16 ปรับเปลี่ยนฐานข้อมูล DPIS 4 -> DPIS 5' WHERE LINKTO_WEB = 'mysql_to_oracle.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'data_promote_c_lead_inquire.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'data_promote_c_end_inquire.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'data_promote_c_comdtl.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'cond_level_salary.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'cond_assign_type.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'cond_time_position.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV1 WHERE MENU_LABEL = 'S03 เงื่อนไขมอบอำนาจ' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'data_file_summary.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'data_file_savesummary.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'data_file_makesummary.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV1 WHERE MENU_LABEL = 'P12 ข้อมูลสรุป' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_map_per_position.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_map_per_type.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_map_per_formula.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'data_early_retire.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_R020010.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'structure_file_transfer.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV1 WHERE MENU_LABEL = 'รายงานสรุป' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'master_table.html?table=PER_JOB_FAMILY' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'kpi_job_competence.html?table=PER_JOB_COMPETENCE' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'master_table_map_org.html ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'master_table_map_code.html ";
			$db->send_cmd($cmd);
			//$db->show_error();

			// update PER_RETIREDATE
			$cmd = " select PER_ID, PER_BIRTHDATE, PER_RETIREDATE from PER_PERSONAL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			while($data = $db_dpis->get_array()){
				$PER_ID = $data[PER_ID];
				$PER_BIRTHDATE = trim($data[PER_BIRTHDATE]);
				if($PER_BIRTHDATE){
					$arr_temp = explode("-", $PER_BIRTHDATE);
					$RETIRE_YEAR = $arr_temp[0] + 60;
					if($arr_temp[1] > '10' || ($arr_temp[1] == '10' && $arr_temp[2] > '01')) $RETIRE_YEAR += 1;
					$PER_RETIREDATE = $RETIRE_YEAR."-10-01";
					if($PER_RETIREDATE != trim($data[PER_RETIREDATE])){
						$cmd = "update PER_PERSONAL set PER_RETIREDATE='$PER_RETIREDATE' where PER_ID=$PER_ID";
						$db_dpis1->send_cmd($cmd);
						//$db_dpis1->show_error();
					} // end if
				} // end if
			} // end while
			
			$cmd = " SELECT PC_ID FROM PER_PERCARD ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) {
				if($DPISDB=="odbc") 
					$cmd = " CREATE TABLE PER_PERCARD(
					PC_ID INTEGER NOT NULL,
					PC_NAME VARCHAR(100) NOT NULL,
					PC_UNIT VARCHAR(2) NOT NULL,	
					PC_W INTEGER2 NULL,
					PC_H INTEGER2 NULL,
					PC_FORM MEMO NOT NULL,
					UPDATE_USER INTEGER NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_PERCARD PRIMARY KEY (PC_ID)) ";
				elseif($DPISDB=="oci8") 
					$cmd = " CREATE TABLE PER_PERCARD(
					PC_ID NUMBER(10) NOT NULL,
					PC_NAME VARCHAR2(100) NOT NULL,
					PC_UNIT VARCHAR2(2) NOT NULL,	
					PC_W NUMBER(5) NULL,
					PC_H NUMBER(5) NULL,
					PC_FORM VARCHAR2(4000) NOT NULL,
					UPDATE_USER NUMBER(11) NOT NULL,
					UPDATE_DATE VARCHAR2(19) NOT NULL,		
					CONSTRAINT PK_PER_PERCARD PRIMARY KEY (PC_ID)) ";
				elseif($DPISDB=="mysql")
					$cmd = " CREATE TABLE PER_PERCARD(
					PC_ID INTEGER(10) NOT NULL,
					PC_NAME VARCHAR(100) NOT NULL,
					PC_UNIT VARCHAR(2) NOT NULL,	
					PC_W SMALLINT(5) NULL,
					PC_H SMALLINT(5) NULL,
					PC_FORM TEXT NOT NULL,
					UPDATE_USER INTEGER(11) NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_PERCARD PRIMARY KEY (PC_ID)) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_PERCARD (PC_ID, PC_NAME, PC_UNIT, PC_W, PC_H, PC_FORM, UPDATE_USER, UPDATE_DATE)
								VALUES (1, 'บัตรข้าราชการแบบที่ 1', 'mm', 178, 55, 'image,images/logo.jpg,3,2,10,14|variable,address1,90,45,87,angsa,,11,C,F4,09,E6,|variable,perposline,5,46,40,angsa,,12,L,F2,69,56,|text,สำนักนายกรัฐมนตรี,MahartThai,13,4,40,angsa,11,11,L,00,00,|variable,org,13,12,40,angsa,,11,L,0E,09,80,lines|rect,box1,1,1,88,54,FA,D5,D7,0,|variable,allphone,90,50,87,angsa,,10,C,06,76,18,|text,สำนักงาน ก.พ.,pokKlong,13,7,40,angsa,11,11,undefined,00,00,|line,line1,14,12,65,12,CF,D0,FA,0|variable,pername1,5,41,40,angsab,,14,L,00,00,00,|per_pic,images/my_preview.jpg,66,2,20,26|rect,box2,90,1,177,54,D2,D6,EA,0,|text,สิทธ์การใช้บัตร,Condition,92,4,86,angsa,,12,C,92,8F,8F|text,1.ใช้เพื่อเป็นการระบุตัวตนของผู้ถือบัตร,new eng text,97,10,82,angsa,,11,L,97,99,FF|text,2.ใช้เพื่อเป็นทางผ่านในส่วนพื้นที่ที่กำหนดสิทธิเฉพาะ,new eng text,97,14,82,angsa,,11,L,97,99,FF|variable,email,45,50,42,angsa,,10,R,83,83,89,', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_PERCARD (PC_ID, PC_NAME, PC_UNIT, PC_W, PC_H, PC_FORM, UPDATE_USER, UPDATE_DATE)
								VALUES (2, 'บัตรข้าราชการแบบที่ 2', 'mm', 178, 55, 'variable,pername1,2,43,63,angsab,,16,R,00,00,00,|variable,address2,92,45,85,angsa,,11,C,06,6D,0D,|variable,allphone,92,49,86,angsab,,11,C,00,00,00,|variable,perposline,2,49,63,angsa,,12,R,F3,0B,EB,|variable,email,92,41,85,angsab,,10,C,F9,06,23,|image,images/logo.jpg,2,2,10,14|line,line1,12,12,87,12,C3,BB,BB,0|text,สำนักนายกรัฐมนตรี สำนักงาน ก.พ.,MahartThai Prok Klong,47,7,40,angsa,12,11,undefined,07,05,69|variable,org,27,12,60,angsa,,11,R,07,12,68,lines|text,1.ใช้เพื่อเป็นการระบุตัวตนของผู้ถือบัตร,new eng text,97,10,82,angsa,,11,L,D9,87,BD|text,สิทธ์การใช้บัตร,Condition,92,4,86,angsa,,12,C,75,75,B3|text,2.ใช้เพื่อเป็นทางผ่านในส่วนพื้นที่ที่กำหนดสิทธิเฉพาะ,new eng text,97,14,82,angsa,,11,L,D9,87,BD|per_pic,images/my_preview.jpg,66,28,20,26', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_PERCARD (PC_ID, PC_NAME, PC_UNIT, PC_W, PC_H, PC_FORM, UPDATE_USER, UPDATE_DATE)
								VALUES (3, 'บัตรข้าราชการแบบที่ 3', 'mm', 178, 55, 'image,images/logo.jpg,78,2,10,14|variable,address2,46,37,40,angsa,,11,R,07,77,0A,lines|variable,perposline,2,8,30,angsa,,12,L,26,13,D8,|variable,allphone,47,46,40,angsa,,10,R,11,0E,A2,|variable,email,57,49,30,angsa,,12,R,E7,12,12,|variable,org,38,10,40,angsa,,11,R,1A,16,D5,lines|text,สำนักนายกรัฐมนตรี,MahartThai,55,3.5,23,angsa,11,10,R,00,00,|rect,box1,11,16,45,28,F9,E1,E1,0,|rect,box2,40,24,55,36,C6,F6,C8,0,|rect,box3,52,21,78,33,E1,DD,FA,0,|variable,pername1,2,2,35,angsa,,16,L,00,00,00,|per_pic,images/my_preview.jpg,3,27,20,26|rect,box4,95,5,173,50,FC,BD,A9,0,|text,สิทธ์การใช้บัตร,new eng text,92,6,86,angsa,,12,C,8C,73,73|text,1.ใช้เพื่อเป็นการระบุตัวตนของผู้ถือบัตร,new eng text,97,12,82,angsa,,11,L,47,3B,E5|text,2.ใช้เพื่อเป็นทางผ่านในส่วนพื้นที่ที่กำหนดสิทธิเฉพาะ,new eng text,97,17,86,angsa,,11,L,47,3B,E5|text,สำนักงาน ก.พ.,new eng text,55,6,23,angsa,,10,R,00,00,00', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_PERCARD (PC_ID, PC_NAME, PC_UNIT, PC_W, PC_H, PC_FORM, UPDATE_USER, UPDATE_DATE)
								VALUES (4, 'บัตรข้าราชการแบบที่ 4', 'mm', 178, 55, 'variable,pername1,47,2,40,angsa,,16,R,00,00,00,|image,images/logo.jpg,2,2,10,14|variable,perposline,47,34,40,angsa,,12,R,00,00,00,|variable,address2,92,42,86,angsa,,10,C,00,00,00,lines|variable,email,92,50,86,angsa,,12,C,00,00,00,|variable,allphone,92,46,86,angsa,,9,C,00,00,00,|variable,org,2,50,60,angsa,,11,L,00,00,00,lines|text,สำนักนายกรัฐมนตรี สำนักงาน ก.พ.,new eng text,2,46.5,40,angsa,,10,L,00,00,00|per_pic,images/my_preview.jpg,65,8,20,26|rect,box1,95,5,174,38,C5,BE,BE,0,|text,สิทธ์การใช้บัตร,new eng text,92,6,86,angsa,,12,C,6F,6C,6C|text,1.ใช้เพื่อเป็นการระบุตัวตนของผู้ถือบัตร,new eng text,97,12,82,angsa,,11,L,A2,9D,9D|text,2.ใช้เพื่อเป็นทางผ่านในส่วนพื้นที่ที่กำหนดสิทธิเฉพาะ,new eng text,97,17,82,angsa,,11,L,A2,9D,9D', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_PERCARD (PC_ID, PC_NAME, PC_UNIT, PC_W, PC_H, PC_FORM, UPDATE_USER, UPDATE_DATE)
								VALUES (5, 'บัตรข้าราชการแบบที่ 5', 'mm', 178, 55, 'image,images/logo.jpg,3,2,10,14|variable,address1,90,45,87,angsa,,11,C,F4,09,E6,|variable,perposline,37,45,50,angsa,,12,R,F2,69,56,|text,สำนักนายกรัฐมนตรี,MahartThai,2,17,40,angsa,11,11,L,00,00,|variable,org,13,7,40,angsa,,11,L,0E,09,80,lines|rect,box1,1,1,88,54,FA,D5,D7,0,|variable,allphone,90,49.5,87,angsa,,10,C,06,76,18,|text,สำนักงาน ก.พ.,pokKlong,2,20,40,angsa,11,11,undefined,00,00,|line,line1,50,10,87,10,CF,D0,FA,0|variable,pername1,37,41,50,angsab,,14,R,00,00,00,|per_pic,images/my_preview.jpg,66,14,20,26|rect,box2,95,7,173,44,D2,D6,EA,0,|text,สิทธ์การใช้บัตร,Condition,92,4,86,angsa,,12,C,92,8F,8F|text,1.ใช้เพื่อเป็นการระบุตัวตนของผู้ถือบัตร,new eng text,97,10,82,angsa,,11,L,73,CB,77|text,2.ใช้เพื่อเป็นทางผ่านในส่วนพื้นที่ที่กำหนดสิทธิเฉพาะ,new eng text,97,14,81,angsa,,11,L,73,CB,77|variable,email,45,49.7,42,angsa,,10,R,83,83,89,', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				if($DPISDB=="odbc") 
					$cmd = " CREATE TABLE PER_PERSONAL_CARD(
					PER_ID INTEGER NOT NULL,
					PC_ID INTEGER NOT NULL,
					PC_PER_FORM MEMO NOT NULL,
					UPDATE_USER INTEGER NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_PERSONAL_CARD PRIMARY KEY (PER_ID, PC_ID)) ";
				elseif($DPISDB=="oci8") 
					$cmd = " CREATE TABLE PER_PERSONAL_CARD(
					PER_ID NUMBER(10) NOT NULL,
					PC_ID NUMBER(10) NOT NULL,
					PC_PER_FORM VARCHAR2(4000) NOT NULL,
					UPDATE_USER NUMBER(11) NOT NULL,
					UPDATE_DATE VARCHAR2(19) NOT NULL,		
					CONSTRAINT PK_PER_PERSONAL_CARD PRIMARY KEY (PER_ID, PC_ID)) ";
				elseif($DPISDB=="mysql")
					$cmd = " CREATE TABLE PER_PERSONAL_CARD(
					PER_ID INTEGER(10) NOT NULL,
					PC_ID INTEGER(10) NOT NULL,
					PC_PER_FORM TEXT NOT NULL,
					UPDATE_USER INTEGER(11) NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_PERSONAL_CARD PRIMARY KEY (PER_ID, PC_ID)) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'per_offno_print.html' ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'per_percard_design.html' ";
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
									  VALUES (1, 'TH', $MAX_ID, 4, 'P1004 ออกแบบบัตรประจำตัวเจ้าหน้าที่ของรัฐ', 'S', 'W', 'per_percard_design.html', 0, 35, 250, 
									  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();

					$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
									  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
									  CREATE_BY, UPDATE_DATE, UPDATE_BY)
									  VALUES (1, 'EN', $MAX_ID, 4, 'P1004 ออกแบบบัตรประจำตัวเจ้าหน้าที่ของรัฐ', 'S', 'W', 'per_percard_design.html', 0, 35, 250, 
									  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();
				}

				$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'per_percard_print.html' ";
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
									  VALUES (1, 'TH', $MAX_ID, 5, 'P1005 พิมพ์บัตรประจำตัวเจ้าหน้าที่ของรัฐ', 'S', 'W', 'per_percard_print.html', 0, 35, 250, 
									  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();

					$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
									  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
									  CREATE_BY, UPDATE_DATE, UPDATE_BY)
									  VALUES (1, 'EN', $MAX_ID, 5, 'P1005 พิมพ์บัตรประจำตัวเจ้าหน้าที่ของรัฐ', 'S', 'W', 'per_percard_print.html', 0, 35, 250, 
									  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();
				}
			}
			
			$cmd = " SELECT HG_CODE FROM PER_HOLIDAY_GROUP ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) {
				if($DPISDB=="odbc") 
					$cmd = " CREATE TABLE PER_HOLIDAY_GROUP(
					HG_ID  INTEGER NOT NULL,
					HG_CODE VARCHAR(10) NOT NULL,	
					HG_NAME VARCHAR(100) NOT NULL,
					HG_SEQ_NO INTEGER2 NULL,
					HG_ACTIVE SINGLE NOT NULL,
					UPDATE_USER INTEGER NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_HOLIDAY_GROUP PRIMARY KEY (HG_ID)) ";
				elseif($DPISDB=="oci8") 
					$cmd = " CREATE TABLE PER_HOLIDAY_GROUP(
					HG_ID NUMBER(10) NOT NULL,
					HG_CODE VARCHAR2(10) NOT NULL,	
					HG_NAME VARCHAR2(100) NOT NULL,
					HG_SEQ_NO NUMBER(5) NULL,
					HG_ACTIVE NUMBER(1) NOT NULL,
					UPDATE_USER NUMBER(11) NOT NULL,
					UPDATE_DATE VARCHAR2(19) NOT NULL,		
					CONSTRAINT PK_PER_HOLIDAY_GROUP PRIMARY KEY (HG_ID)) ";
				elseif($DPISDB=="mysql")
					$cmd = " CREATE TABLE PER_HOLIDAY_GROUP(
					HG_ID  INTEGER(10) NOT NULL,
					HG_CODE VARCHAR(10) NOT NULL,	
					HG_NAME VARCHAR(100) NOT NULL,
					HG_SEQ_NO SMALLINT(5) NULL,
					HG_ACTIVE SMALLINT(1) NOT NULL,
					UPDATE_USER INTEGER(11) NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_HOLIDAY_GROUP PRIMARY KEY (HG_ID)) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				if($DPISDB=="odbc") 
					$cmd = " CREATE TABLE PER_SPECIAL_HOLIDAY(
					HG_ID  INTEGER NOT NULL,	
					HOLS_DATE VARCHAR(19) NOT NULL,
					HOLS_NAME VARCHAR(100) NULL,
					HOLS_SEQ_NO INTEGER2 NULL,
					UPDATE_USER INTEGER NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_SPECIAL_HOLIDAY PRIMARY KEY (HG_ID, HOLS_DATE)) ";
				elseif($DPISDB=="oci8") 
					$cmd = " CREATE TABLE PER_SPECIAL_HOLIDAY(
					HG_ID NUMBER(10) NOT NULL,	
					HOLS_DATE VARCHAR2(19) NOT NULL,
					HOLS_NAME VARCHAR2(100) NULL,
					HOLS_SEQ_NO NUMBER(5) NULL,
					UPDATE_USER NUMBER(11) NOT NULL,
					UPDATE_DATE VARCHAR2(19) NOT NULL,		
					CONSTRAINT PK_PER_PER_SPECIAL_HOLIDAY PRIMARY KEY (HG_ID, HOLS_DATE)) ";
				elseif($DPISDB=="mysql")
					$cmd = " CREATE TABLE PER_SPECIAL_HOLIDAY(
					HG_ID  INTEGER(10) NOT NULL,	
					HOLS_DATE VARCHAR(19) NOT NULL,
					HOLS_NAME VARCHAR(100) NULL,
					HOLS_SEQ_NO SMALLINT(5) NULL,
					UPDATE_USER INTEGER(11) NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_SPECIAL_HOLIDAY PRIMARY KEY (HG_ID, HOLS_DATE)) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				if($DPISDB=="odbc") 
					$cmd = " CREATE TABLE PER_HOLIDAYHIS(
					HH_ID INTEGER NOT NULL,
					PER_ID INTEGER NOT NULL,
					HG_CODE VARCHAR(10) NOT NULL,	
					START_DATE VARCHAR(19) NOT NULL,
					END_DATE VARCHAR(19) NULL,
					REMARK VARCHAR(100) NULL,
					UPDATE_USER INTEGER NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_HOLIDAYHIS PRIMARY KEY (HH_ID)) ";
				elseif($DPISDB=="oci8") 
					$cmd = " CREATE TABLE PER_HOLIDAYHIS(
					HH_ID NUMBER(10) NOT NULL,
					PER_ID NUMBER(10) NOT NULL,
					HG_CODE VARCHAR2(10) NOT NULL,	
					START_DATE VARCHAR2(19) NOT NULL,
					END_DATE VARCHAR2(19) NULL,
					REMARK VARCHAR2(100) NULL,
					UPDATE_USER NUMBER(11) NOT NULL,
					UPDATE_DATE VARCHAR2(19) NOT NULL,		
					CONSTRAINT PK_PER_PER_HOLIDAYHIS PRIMARY KEY (HH_ID)) ";
				elseif($DPISDB=="mysql")
					$cmd = " CREATE TABLE PER_HOLIDAYHIS(
					HH_ID INTEGER(10) NOT NULL,
					PER_ID INTEGER(10) NOT NULL,
					HG_CODE VARCHAR(10) NOT NULL,	
					START_DATE VARCHAR(19) NOT NULL,
					END_DATE VARCHAR(19) NULL,
					REMARK VARCHAR(100) NULL,
					UPDATE_USER INTEGER(11) NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_HOLIDAYHIS PRIMARY KEY (HH_ID)) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_HOLIDAY_GROUP (HG_ID, HG_CODE, HG_NAME, HG_SEQ_NO, HG_ACTIVE, UPDATE_USER, UPDATE_DATE)
								  VALUES (1, '01', 'อาทิตย์-จันทร์', 1, 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_HOLIDAY_GROUP (HG_ID, HG_CODE, HG_NAME, HG_SEQ_NO, HG_ACTIVE, UPDATE_USER, UPDATE_DATE)
								  VALUES (2, '12', 'จันทร์-อังคาร', 2, 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_HOLIDAY_GROUP (HG_ID, HG_CODE, HG_NAME, HG_SEQ_NO, HG_ACTIVE, UPDATE_USER, UPDATE_DATE)
								  VALUES (3, '23', 'อังคาร-พุธ', 3, 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_HOLIDAY_GROUP (HG_ID, HG_CODE, HG_NAME, HG_SEQ_NO, HG_ACTIVE, UPDATE_USER, UPDATE_DATE)
								  VALUES (4, '34', 'พุธ-พฤหัสบดี', 4, 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_HOLIDAY_GROUP (HG_ID, HG_CODE, HG_NAME, HG_SEQ_NO, HG_ACTIVE, UPDATE_USER, UPDATE_DATE)
								  VALUES (5, '45', 'พฤหัสบดี-ศุกร์', 5, 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_HOLIDAY_GROUP (HG_ID, HG_CODE, HG_NAME, HG_SEQ_NO, HG_ACTIVE, UPDATE_USER, UPDATE_DATE)
								  VALUES (6, '56', 'ศุกร์-เสาร์', 6, 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_HOLIDAY_GROUP (HG_ID, HG_CODE, HG_NAME, HG_SEQ_NO, HG_ACTIVE, UPDATE_USER, UPDATE_DATE)
								  VALUES (7, '60', 'เสาร์-อาทิตย์', 7, 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			}

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'master_table_holiday_group.html' ";
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
								  VALUES (1, 'TH', $MAX_ID, 3, 'M0603 กลุ่มวันหยุด', 'S', 'W', 'master_table_holiday_group.html', 0, 9, 301, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 3, 'M0603 กลุ่มวันหยุด', 'S', 'W', 'master_table_holiday_group.html', 0, 35, 301, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'master_table_special_holiday.html' ";
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
								  VALUES (1, 'TH', $MAX_ID, 4, 'M0604 ปฏิทินวันหยุด (พิเศษ)', 'S', 'W', 'master_table_special_holiday.html', 0, 9, 301, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 4, 'M0604 ปฏิทินวันหยุด (พิเศษ)', 'S', 'W', 'master_table_special_holiday.html', 0, 35, 301, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			add_field("PER_PERSONAL", "ORG_ID_1","INTEGER", "10", "NULL");
			add_field("PER_PERSONAL", "ORG_ID_2","INTEGER", "10", "NULL");
			add_field("PER_PERSONAL", "ORG_ID_3","INTEGER", "10", "NULL");
			add_field("PER_PERSONAL", "ORG_ID_4","INTEGER", "10", "NULL");
			add_field("PER_PERSONAL", "ORG_ID_5","INTEGER", "10", "NULL");

			$cmd = " SELECT PER_ID, ORG_ID FROM PER_PERSONAL WHERE ORG_ID IS NOT NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			while($data = $db_dpis->get_array()){
				$PER_ID = $data[PER_ID];
				$ORG_ID = $data[ORG_ID];
				$cmd = " SELECT OL_CODE, ORG_ID_REF FROM PER_ORG_ASS WHERE ORG_ID = $ORG_ID ";
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
				$data1 = $db_dpis1->get_array();
				$OL_CODE = trim($data1[OL_CODE]);
				$ORG_ID_REF = $data1[ORG_ID_REF];
				if ($OL_CODE=="04") {
					$cmd = " UPDATE PER_PERSONAL SET ORG_ID = $ORG_ID_REF, ORG_ID_1 = $ORG_ID WHERE PER_ID = $PER_ID ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
				} elseif ($OL_CODE=="05") {
					$cmd = " UPDATE PER_PERSONAL SET ORG_ID_1 = $ORG_ID_REF, ORG_ID_2 = $ORG_ID WHERE PER_ID = $PER_ID ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
					$cmd = " SELECT OL_CODE, ORG_ID_REF FROM PER_ORG_ASS WHERE ORG_ID = $ORG_ID_REF ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
					$data1 = $db_dpis1->get_array();
					$OL_CODE = trim($data1[OL_CODE]);
					$ORG_ID_REF = $data1[ORG_ID_REF];
					if ($OL_CODE=="04") {
						$cmd = " UPDATE PER_PERSONAL SET ORG_ID = $ORG_ID_REF WHERE PER_ID = $PER_ID ";
						$db_dpis1->send_cmd($cmd);
						//$db_dpis1->show_error();
					} // end if
				} // end if
			} // end while						

			$cmd = " UPDATE USER_GROUP SET GROUP_ORG_STRUCTURE =  2 WHERE GROUP_ORG_STRUCTURE IS NULL ";
			$db->send_cmd($cmd);
			//$db->show_error();

			add_field("PER_COMTYPE", "COM_ACTIVE","SINGLE", "1", "NULL");

			$cmd = " UPDATE PER_COMTYPE SET COM_ACTIVE =  1 WHERE COM_ACTIVE IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " update PER_CONTROL set CTRL_ALTER = 12 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
?>