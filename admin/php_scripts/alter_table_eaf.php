<?
		$cmd = " SELECT COUNT(EAF_ID) AS COUNT_DATA FROM EAF_MASTER ";
		$count_data = $db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		if (!$count_data) {
			if($DPISDB=="odbc") 
				$cmd = " CREATE TABLE EAF_MASTER(
				EAF_ID INTEGER NOT NULL,
				PL_CODE VARCHAR(10) NULL,	
				LEVEL_NO VARCHAR(10) NULL,	
				PT_CODE VARCHAR(10) NULL,	
				PM_CODE VARCHAR(10) NULL,	
				DEPARTMENT_ID INTEGER NULL,
				ORG_ID INTEGER NULL,
				EAF_NAME VARCHAR(100) NOT NULL,	
				EAF_ROLE MEMO NULL,	
				EAF_DATE VARCHAR(19) NOT NULL,		
				EAF_YEAR INTEGER2 NULL,
				EAF_MONTH INTEGER2 NULL,
				EAF_REMARK MEMO NULL,
				EAF_REF_ID INTEGER NULL,
				EAF_ACTIVE SINGLE NOT NULL,
				UPDATE_USER INTEGER2 NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_EAF_MASTER PRIMARY KEY (eaf_id)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE EAF_MASTER(
				EAF_ID NUMBER(10) NOT NULL,
				PL_CODE VARCHAR2(10) NULL,	
				LEVEL_NO VARCHAR2(10) NULL,	
				PT_CODE VARCHAR2(10) NULL,	
				PM_CODE VARCHAR2(10) NULL,	
				DEPARTMENT_ID NUMBER(10) NULL,
				ORG_ID NUMBER(10) NULL,
				EAF_NAME VARCHAR2(100) NOT NULL,	
				EAF_ROLE VARCHAR2(1000) NULL,	
				EAF_DATE VARCHAR2(19) NOT NULL,		
				EAF_YEAR NUMBER(2) NULL,
				EAF_MONTH NUMBER(2) NULL,
				EAF_REMARK VARCHAR2(1000) NULL,
				EAF_REF_ID NUMBER(10) NULL,
				EAF_ACTIVE NUMBER(1) NOT NULL,
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				CONSTRAINT PK_EAF_MASTER PRIMARY KEY (eaf_id)) ";
			elseif($DPISDB=="mysql")
				$cmd = " CREATE TABLE EAF_MASTER(
				EAF_ID INTEGER(10) NOT NULL,
				PL_CODE VARCHAR(10) NULL,	
				LEVEL_NO VARCHAR(10) NULL,	
				PT_CODE VARCHAR(10) NULL,	
				PM_CODE VARCHAR(10) NULL,	
				DEPARTMENT_ID INTEGER(10) NULL,
				ORG_ID INTEGER(10) NULL,
				EAF_NAME VARCHAR(100) NOT NULL,	
				EAF_ROLE TEXT NULL,	
				EAF_DATE VARCHAR(19) NOT NULL,		
				EAF_YEAR SMALLINT(2) NULL,
				EAF_MONTH SMALLINT(2) NULL,
				EAF_REMARK TEXT NULL,
				EAF_REF_ID INTEGER(10) NULL,
				EAF_ACTIVE SMALLINT(1) NOT NULL,
				UPDATE_USER SMALLINT(5) NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_EAF_MASTER PRIMARY KEY (eaf_id)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " SELECT COUNT(EC_ID) AS COUNT_DATA FROM EAF_COMPETENCE ";
		$count_data = $db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		if (!$count_data) {
			if($DPISDB=="odbc") 
				$cmd = " CREATE TABLE EAF_COMPETENCE(
				EC_ID INTEGER NOT NULL,
				EAF_ID INTEGER NOT NULL,
				CP_CODE VARCHAR(10) NOT NULL,	
				UPDATE_USER INTEGER2 NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_EAF_COMPETENCE PRIMARY KEY (ec_id)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE EAF_COMPETENCE(
				EC_ID NUMBER(10) NOT NULL,
				EAF_ID NUMBER(10) NOT NULL,
				CP_CODE VARCHAR2(10) NOT NULL,	
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				CONSTRAINT PK_EAF_COMPETENCE PRIMARY KEY (ec_id)) ";
			elseif($DPISDB=="mysql")
				$cmd = " CREATE TABLE EAF_COMPETENCE(
				EC_ID INTEGER(10) NOT NULL,
				EAF_ID INTEGER(10) NOT NULL,
				CP_CODE VARCHAR(10) NOT NULL,	
				UPDATE_USER SMALLINT(5) NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_EAF_COMPETENCE PRIMARY KEY (ec_id)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " SELECT COUNT(ELS_ID) AS COUNT_DATA FROM EAF_LEARNING_STRUCTURE ";
		$count_data = $db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		if (!$count_data) {
			if($DPISDB=="odbc") 
				$cmd = " CREATE TABLE EAF_LEARNING_STRUCTURE(
				ELS_ID INTEGER NOT NULL,
				EAF_ID INTEGER NOT NULL,
				ELS_SEQ_NO INTEGER2 NULL,
				MINISTRY_ID INTEGER NULL,
				DEPARTMENT_ID INTEGER NULL,
				ORG_ID INTEGER NULL,
				ORG_ID_1 INTEGER NULL,
				ORG_ID_2 INTEGER NULL,
				ELS_LEVEL SINGLE NULL,
				ELS_PERIOD INTEGER2 NULL,
				ELS_SEQ_NO INTEGER2 NULL,
				UPDATE_USER INTEGER2 NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_EAF_LEARNING_STRUCTURE PRIMARY KEY (els_id)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE EAF_LEARNING_STRUCTURE(
				ELS_ID NUMBER(10) NOT NULL,
				EAF_ID NUMBER(10) NOT NULL,
				ELS_SEQ_NO NUMBER(3) NULL,
				MINISTRY_ID NUMBER(10) NULL,
				DEPARTMENT_ID NUMBER(10) NULL,
				ORG_ID NUMBER(10) NULL,
				ORG_ID_1 NUMBER(10) NULL,
				ORG_ID_2 NUMBER(10) NULL,
				ELS_LEVEL NUMBER(1) NULL,
				ELS_PERIOD NUMBER(3) NULL,
				ELS_SEQ_NO NUMBER(3) NULL,
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				CONSTRAINT PK_EAF_LEARNING_STRUCTURE PRIMARY KEY (els_id)) ";
			elseif($DPISDB=="mysql")
				$cmd = " CREATE TABLE EAF_LEARNING_STRUCTURE(
				ELS_ID INTEGER(10) NOT NULL,
				EAF_ID INTEGER(10) NOT NULL,
				ELS_SEQ_NO SMALLINT(3) NULL,
				MINISTRY_ID INTEGER(10) NULL,
				DEPARTMENT_ID INTEGER(10) NULL,
				ORG_ID INTEGER(10) NULL,
				ORG_ID_1 INTEGER(10) NULL,
				ORG_ID_2 INTEGER(10) NULL,
				ELS_LEVEL SMALLINT(1) NULL,
				ELS_PERIOD SMALLINT(3) NULL,
				ELS_SEQ_NO SMALLINT(3) NULL,
				UPDATE_USER SMALLINT(5) NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_EAF_LEARNING_STRUCTURE PRIMARY KEY (els_id)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " SELECT COUNT(ELK_ID) AS COUNT_DATA FROM EAF_LEARNING_KNOWLEDGE ";
		$count_data = $db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		if (!$count_data) {
			if($DPISDB=="odbc") 
				$cmd = " CREATE TABLE EAF_LEARNING_KNOWLEDGE(
				ELK_ID NUMBER(10) NOT NULL,
				EAF_ID NUMBER(10) NOT NULL,
				ELS_ID NUMBER(10) NOT NULL,
				ELK_NAME VARCHAR2(1000) NOT NULL,	
				ELK_COACH VARCHAR2(1000) NULL,	
				ELK_BEHAVIOR VARCHAR2(1000) NULL,
				ELK_TRAIN VARCHAR2(1000) NULL,
				ELK_JOB VARCHAR2(1000) NULL,
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				CONSTRAINT PK_EAF_LEARNING_KNOWLEDGE PRIMARY KEY (elk_id)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE EAF_LEARNING_KNOWLEDGE(
				ELK_ID NUMBER(10) NOT NULL,
				EAF_ID NUMBER(10) NOT NULL,
				ELS_ID NUMBER(10) NOT NULL,
				ELK_NAME VARCHAR2(1000) NOT NULL,	
				ELK_COACH VARCHAR2(1000) NULL,	
				ELK_BEHAVIOR VARCHAR2(1000) NULL,
				ELK_TRAIN VARCHAR2(1000) NULL,
				ELK_JOB VARCHAR2(1000) NULL,
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				CONSTRAINT PK_EAF_LEARNING_KNOWLEDGE PRIMARY KEY (elk_id)) ";
			elseif($DPISDB=="mysql")
				$cmd = " CREATE TABLE EAF_LEARNING_KNOWLEDGE(
				ELK_ID NUMBER(10) NOT NULL,
				EAF_ID NUMBER(10) NOT NULL,
				ELS_ID NUMBER(10) NOT NULL,
				ELK_NAME VARCHAR2(1000) NOT NULL,	
				ELK_COACH VARCHAR2(1000) NULL,	
				ELK_BEHAVIOR VARCHAR2(1000) NULL,
				ELK_TRAIN VARCHAR2(1000) NULL,
				ELK_JOB VARCHAR2(1000) NULL,
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				CONSTRAINT PK_EAF_LEARNING_KNOWLEDGE PRIMARY KEY (elk_id)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " SELECT COUNT(EP_ID) AS COUNT_DATA FROM EAF_PERSONAL ";
		$count_data = $db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		if (!$count_data) {
			if($DPISDB=="odbc") 
				$cmd = " CREATE TABLE EAF_PERSONAL(
				EP_ID NUMBER(10) NOT NULL,
				PER_ID NUMBER(10) NOT NULL,
				EAF_ID NUMBER(10) NOT NULL,
				EP_YEAR VARCHAR2(10) NULL,	
				START_DATE VARCHAR2(19) NULL,		
				END_DATE VARCHAR2(19) NULL,		
				EP_REMARK VARCHAR2(1000) NULL,
				PER_ID_REVIEW NUMBER(10) NULL,
				AGREE_REVIEW VARCHAR2(1000) NULL,
				DIFF_REVIEW VARCHAR2(1000) NULL,
				PER_ID_REVIEW1 NUMBER(10) NULL,
				AGREE_REVIEW1 VARCHAR2(1000) NULL,
				DIFF_REVIEW1 VARCHAR2(1000) NULL,
				PER_ID_REVIEW2 NUMBER(10) NULL,
				AGREE_REVIEW2 VARCHAR2(1000) NULL,
				DIFF_REVIEW2 VARCHAR2(1000) NULL,
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				CONSTRAINT PK_EAF_PERSONAL PRIMARY KEY (ep_id)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE EAF_PERSONAL(
				EP_ID NUMBER(10) NOT NULL,
				PER_ID NUMBER(10) NOT NULL,
				EAF_ID NUMBER(10) NOT NULL,
				EP_YEAR VARCHAR2(10) NULL,	
				START_DATE VARCHAR2(19) NULL,		
				END_DATE VARCHAR2(19) NULL,		
				EP_REMARK VARCHAR2(1000) NULL,
				PER_ID_REVIEW NUMBER(10) NULL,
				AGREE_REVIEW VARCHAR2(1000) NULL,
				DIFF_REVIEW VARCHAR2(1000) NULL,
				PER_ID_REVIEW1 NUMBER(10) NULL,
				AGREE_REVIEW1 VARCHAR2(1000) NULL,
				DIFF_REVIEW1 VARCHAR2(1000) NULL,
				PER_ID_REVIEW2 NUMBER(10) NULL,
				AGREE_REVIEW2 VARCHAR2(1000) NULL,
				DIFF_REVIEW2 VARCHAR2(1000) NULL,
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				CONSTRAINT PK_EAF_PERSONAL PRIMARY KEY (ep_id)) ";
			elseif($DPISDB=="mysql")
				$cmd = " CREATE TABLE EAF_PERSONAL(
				EP_ID NUMBER(10) NOT NULL,
				PER_ID NUMBER(10) NOT NULL,
				EAF_ID NUMBER(10) NOT NULL,
				EP_YEAR VARCHAR2(10) NULL,	
				START_DATE VARCHAR2(19) NULL,		
				END_DATE VARCHAR2(19) NULL,		
				EP_REMARK VARCHAR2(1000) NULL,
				PER_ID_REVIEW NUMBER(10) NULL,
				AGREE_REVIEW VARCHAR2(1000) NULL,
				DIFF_REVIEW VARCHAR2(1000) NULL,
				PER_ID_REVIEW1 NUMBER(10) NULL,
				AGREE_REVIEW1 VARCHAR2(1000) NULL,
				DIFF_REVIEW1 VARCHAR2(1000) NULL,
				PER_ID_REVIEW2 NUMBER(10) NULL,
				AGREE_REVIEW2 VARCHAR2(1000) NULL,
				DIFF_REVIEW2 VARCHAR2(1000) NULL,
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				CONSTRAINT PK_EAF_PERSONAL PRIMARY KEY (ep_id)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " SELECT COUNT(EPD_ID) AS COUNT_DATA FROM EAF_PERSONAL_DETAIL ";
		$count_data = $db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		if (!$count_data) {
			if($DPISDB=="odbc") 
				$cmd = " CREATE TABLE EAF_PERSONAL_DETAIL(
				EPD_ID NUMBER(10) NOT NULL,
				EP_ID NUMBER(10) NOT NULL,
				ELK_ID NUMBER(10) NOT NULL,
				EPD_NAME VARCHAR2(1000) NOT NULL,	
				EPD_ORG VARCHAR2(1000) NULL,	
				EPD_BEHAVIOR VARCHAR2(1000) NULL,
				EPD_COACH VARCHAR2(1000) NULL,	
				EPD_TRAIN VARCHAR2(1000) NULL,
				EPD_JOB VARCHAR2(1000) NULL,
				EPD_EVALUATE CHAR(1) NULL,
				EPD_EVALUATE_REASON VARCHAR2(1000) NULL,
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				CONSTRAINT PK_EAF_PERSONAL_DETAIL PRIMARY KEY (epd_id)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE EAF_PERSONAL_DETAIL(
				EPD_ID NUMBER(10) NOT NULL,
				EP_ID NUMBER(10) NOT NULL,
				ELK_ID NUMBER(10) NOT NULL,
				EPD_NAME VARCHAR2(1000) NOT NULL,	
				EPD_ORG VARCHAR2(1000) NULL,	
				EPD_BEHAVIOR VARCHAR2(1000) NULL,
				EPD_COACH VARCHAR2(1000) NULL,	
				EPD_TRAIN VARCHAR2(1000) NULL,
				EPD_JOB VARCHAR2(1000) NULL,
				EPD_EVALUATE CHAR(1) NULL,
				EPD_EVALUATE_REASON VARCHAR2(1000) NULL,
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				CONSTRAINT PK_EAF_PERSONAL_DETAIL PRIMARY KEY (epd_id)) ";
			elseif($DPISDB=="mysql")
				$cmd = " CREATE TABLE EAF_PERSONAL_DETAIL(
				EPD_ID NUMBER(10) NOT NULL,
				EP_ID NUMBER(10) NOT NULL,
				ELK_ID NUMBER(10) NOT NULL,
				EPD_NAME VARCHAR2(1000) NOT NULL,	
				EPD_ORG VARCHAR2(1000) NULL,	
				EPD_BEHAVIOR VARCHAR2(1000) NULL,
				EPD_COACH VARCHAR2(1000) NULL,	
				EPD_TRAIN VARCHAR2(1000) NULL,
				EPD_JOB VARCHAR2(1000) NULL,
				EPD_EVALUATE CHAR(1) NULL,
				EPD_EVALUATE_REASON VARCHAR2(1000) NULL,
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				CONSTRAINT PK_EAF_PERSONAL_DETAIL PRIMARY KEY (epd_id)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " SELECT COUNT(EPS_ID) AS COUNT_DATA FROM EAF_PERSONAL_STRUCTURE ";
		$count_data = $db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		if (!$count_data) {
			if($DPISDB=="odbc") 
				$cmd = " CREATE TABLE EAF_PERSONAL_STRUCTURE(
				EPS_ID NUMBER(10) NOT NULL,
				EP_ID NUMBER(10) NOT NULL,
				EPS_SEQ_NO NUMBER(3) NULL,
				MINISTRY_ID NUMBER(10) NULL,
				DEPARTMENT_ID NUMBER(10) NULL,
				ORG_ID NUMBER(10) NULL,
				ORG_ID_1 NUMBER(10) NULL,
				ORG_ID_2 NUMBER(10) NULL,
				EPS_LEVEL NUMBER(1) NULL,
				EPS_PERIOD NUMBER(3) NULL,
				START_DATE VARCHAR2(19) NOT NULL,		
				END_DATE VARCHAR2(19) NOT NULL,		
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				CONSTRAINT PK_EAF_PERSONAL_STRUCTURE PRIMARY KEY (eps_id)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE EAF_PERSONAL_STRUCTURE(
				EPS_ID NUMBER(10) NOT NULL,
				EP_ID NUMBER(10) NOT NULL,
				EPS_SEQ_NO NUMBER(3) NULL,
				MINISTRY_ID NUMBER(10) NULL,
				DEPARTMENT_ID NUMBER(10) NULL,
				ORG_ID NUMBER(10) NULL,
				ORG_ID_1 NUMBER(10) NULL,
				ORG_ID_2 NUMBER(10) NULL,
				EPS_LEVEL NUMBER(1) NULL,
				EPS_PERIOD NUMBER(3) NULL,
				START_DATE VARCHAR2(19) NOT NULL,		
				END_DATE VARCHAR2(19) NOT NULL,		
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				CONSTRAINT PK_EAF_PERSONAL_STRUCTURE PRIMARY KEY (eps_id)) ";
			elseif($DPISDB=="mysql")
				$cmd = " CREATE TABLE EAF_PERSONAL_STRUCTURE(
				EPS_ID NUMBER(10) NOT NULL,
				EP_ID NUMBER(10) NOT NULL,
				EPS_SEQ_NO NUMBER(3) NULL,
				MINISTRY_ID NUMBER(10) NULL,
				DEPARTMENT_ID NUMBER(10) NULL,
				ORG_ID NUMBER(10) NULL,
				ORG_ID_1 NUMBER(10) NULL,
				ORG_ID_2 NUMBER(10) NULL,
				EPS_LEVEL NUMBER(1) NULL,
				EPS_PERIOD NUMBER(3) NULL,
				START_DATE VARCHAR2(19) NOT NULL,		
				END_DATE VARCHAR2(19) NOT NULL,		
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				CONSTRAINT PK_EAF_PERSONAL_STRUCTURE PRIMARY KEY (eps_id)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " SELECT COUNT(EPK_ID) AS COUNT_DATA FROM EAF_PERSONAL_KNOWLEDGE ";
		$count_data = $db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		if (!$count_data) {
			if($DPISDB=="odbc") 
				$cmd = " CREATE TABLE EAF_PERSONAL_KNOWLEDGE(
				EPK_ID NUMBER(10) NOT NULL,
				EP_ID NUMBER(10) NOT NULL,
				EPS_ID NUMBER(10) NOT NULL,
				EPK_NAME VARCHAR2(1000) NOT NULL,	
				EPK_COACH VARCHAR2(1000) NULL,	
				EPK_BEHAVIOR VARCHAR2(1000) NULL,
				EPK_TRAIN VARCHAR2(1000) NULL,
				EPK_JOB VARCHAR2(1000) NULL,
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				CONSTRAINT PK_EAF_PERSONAL_KNOWLEDGE PRIMARY KEY (epk_id)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE EAF_PERSONAL_KNOWLEDGE(
				EPK_ID NUMBER(10) NOT NULL,
				EP_ID NUMBER(10) NOT NULL,
				EPS_ID NUMBER(10) NOT NULL,
				EPK_NAME VARCHAR2(1000) NOT NULL,	
				EPK_COACH VARCHAR2(1000) NULL,	
				EPK_BEHAVIOR VARCHAR2(1000) NULL,
				EPK_TRAIN VARCHAR2(1000) NULL,
				EPK_JOB VARCHAR2(1000) NULL,
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				CONSTRAINT PK_EAF_PERSONAL_KNOWLEDGE PRIMARY KEY (epk_id)) ";
			elseif($DPISDB=="mysql")
				$cmd = " CREATE TABLE EAF_PERSONAL_KNOWLEDGE(
				EPK_ID NUMBER(10) NOT NULL,
				EP_ID NUMBER(10) NOT NULL,
				EPS_ID NUMBER(10) NOT NULL,
				EPK_NAME VARCHAR2(1000) NOT NULL,	
				EPK_COACH VARCHAR2(1000) NULL,	
				EPK_BEHAVIOR VARCHAR2(1000) NULL,
				EPK_TRAIN VARCHAR2(1000) NULL,
				EPK_JOB VARCHAR2(1000) NULL,
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				CONSTRAINT PK_EAF_PERSONAL_KNOWLEDGE PRIMARY KEY (epk_id)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV0 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
						  FLAG_SHOW, TYPE_LINKTO, LINKTO_TID, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
						  VALUES (1, 'TH', 49, 13, '��ͺ�����������ʺ��ó�', 'S', 'N', 0, $CREATE_DATE, $CREATE_BY, $CREATE_DATE, 
						  $CREATE_BY) ";
		$db->send_cmd($cmd);
		//$db->show_error();

		$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV0 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
						  FLAG_SHOW, 	TYPE_LINKTO, LINKTO_TID, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
						  VALUES (1, 'EN', 49, 13, '��ͺ�����������ʺ��ó�', 'S', 'N', 0, $CREATE_DATE, $CREATE_BY, $CREATE_DATE, 
						  $CREATE_BY) ";
		$db->send_cmd($cmd);
		//$db->show_error();

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'eaf_master.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 1, 'E01 ��ͺ�����������ʺ��ó� (EAF)', 'S', 'W', 'eaf_master.html', 0, 49, $CREATE_DATE, $CREATE_BY, 
							  $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 1, 'E01 ��ͺ�����������ʺ��ó� (EAF)', 'S', 'W', 'eaf_master.html', 0, 49, $CREATE_DATE, $CREATE_BY, 
							  $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'eaf_person.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 2, 'E02 ��鹷ҧ����Ҫվ����Ѻ����Ҫ��ü���ռ����ķ����٧', 'S', 'W', 'eaf_person.html', 0, 49, $CREATE_DATE, 
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 2, 'E02 ��鹷ҧ����Ҫվ����Ѻ����Ҫ��ü���ռ����ķ����٧', 'S', 'W', 'eaf_person.html', 0, 49, $CREATE_DATE, 
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'rpt_R012001.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 3, 'R1201 �ӹǹ����Ҫ��ü���ռ����ķ����٧ ��ṡ�����ǹ�Ҫ���', 'S', 'W', 'rpt_R012001.html', 0, 49, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 3, 'R1201 �ӹǹ����Ҫ��ü���ռ����ķ����٧ ��ṡ�����ǹ�Ҫ���', 'S', 'W', 'rpt_R012001.html', 0, 49, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'rpt_R012002.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 4, 'R1202 ��ͺ�����������ʺ��ó�ͧ��ǹ�Ҫ��� ��ṡ������˹觻�����', 'S', 'W', 'rpt_R012002.html', 0, 49, $CREATE_DATE,
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 4, 'R1202 ��ͺ�����������ʺ��ó�ͧ��ǹ�Ҫ��� ��ṡ������˹觻�����', 'S', 'W', 'rpt_R012002.html', 0, 49, $CREATE_DATE,
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'rpt_R012003.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 5, 'R1203 ��ͺ�����������ʺ��ó�ͧ��ǹ�Ҫ��� ��ṡ������˹��������', 'S', 'W', 'rpt_R012003.html', 0, 49, $CREATE_DATE,
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 5, 'R1203 ��ͺ�����������ʺ��ó�ͧ��ǹ�Ҫ��� ��ṡ������˹��������', 'S', 'W', 'rpt_R012003.html', 0, 49, $CREATE_DATE,
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'rpt_R012004.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 6, 'R1204 ��þѲ�Ң���Ҫ��ü���ռ����ķ����٧�����ͺ�����������ʺ��ó�ͧ��ǹ�Ҫ���', 'S', 'W', 'rpt_R012004.html', 0, 49, $CREATE_DATE,
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 6, 'R1204 ��þѲ�Ң���Ҫ��ü���ռ����ķ����٧�����ͺ�����������ʺ��ó�ͧ��ǹ�Ҫ���', 'S', 'W', 'rpt_R012004.html', 0, 49, $CREATE_DATE,
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'rpt_R012005.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 7, 'R1205 ��ͺ�����������ʺ��ó���ºؤ�Ţͧ����Ҫ��ü���ռ����ķ����٧', 'S', 'W', 'rpt_R012005.html', 0, 49, $CREATE_DATE,
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 7, 'R1205 ��ͺ�����������ʺ��ó���ºؤ�Ţͧ����Ҫ��ü���ռ����ķ����٧', 'S', 'W', 'rpt_R012005.html', 0, 49, $CREATE_DATE,
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'rpt_R012006.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 8, 'R1206 �Ҿ�����þѲ�Ң���Ҫ��ü���ռ����ķ����٧ ��ṡ�����ǹ�Ҫ��� �дѺ���', 'S', 'W', 'rpt_R012006.html', 0, 49, $CREATE_DATE,
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 8, 'R1206 �Ҿ�����þѲ�Ң���Ҫ��ü���ռ����ķ����٧ ��ṡ�����ǹ�Ҫ��� �дѺ���', 'S', 'W', 'rpt_R012006.html', 0, 49, $CREATE_DATE,
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}
?>