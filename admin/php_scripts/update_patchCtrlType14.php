<?php 
if ($REPORT_GEN==1) {
				$cmd = " SELECT POS_ID FROM RPT_ADJUST_FORMAT ";
				$count_data = $db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
				if (!$count_data) {
					if($DPISDB=="odbc") 
						$cmd = " CREATE TABLE RPT_ADJUST_FORMAT(
						RPT_NAME VARCHAR(50) NULL,
						USER_ID INTEGER NOT NULL,	
						FORMAT_SEQ INTEGER2 NULL,
						FORMAT_NAME VARCHAR(255) NOT NULL,
						COLUMN_FORMAT MEMO NULL,
						UPDATE_USER INTEGER NOT NULL,
						UPDATE_DATE VARCHAR(19) NOT NULL,		
						CONSTRAINT PK_RPT_ADJUST_FORMAT PRIMARY KEY (RPT_NAME, USER_ID, FORMAT_SEQ)) ";
					elseif($DPISDB=="oci8") 
						$cmd = " CREATE TABLE RPT_ADJUST_FORMAT(
						RPT_NAME VARCHAR2(50) NULL,
						USER_ID NUMBER(10) NOT NULL,	
						FORMAT_SEQ NUMBER(5) NULL,
						FORMAT_NAME VARCHAR2(255) NOT NULL,
						COLUMN_FORMAT VARCHAR2(4000) NULL,
						UPDATE_USER NUMBER(11) NOT NULL,
						UPDATE_DATE VARCHAR2(19) NOT NULL,		
						CONSTRAINT PK_RPT_ADJUST_FORMAT PRIMARY KEY (RPT_NAME, USER_ID, FORMAT_SEQ)) ";
					elseif($DPISDB=="mysql")
						$cmd = " CREATE TABLE RPT_ADJUST_FORMAT(
						RPT_NAME VARCHAR(50) NULL,
						USER_ID INTEGER(10) NOT NULL,	
						FORMAT_SEQ SMALLINT(5) NULL,
						FORMAT_NAME VARCHAR(255) NOT NULL,
						COLUMN_FORMAT TEXT NULL,
						UPDATE_USER INTEGER(11) NOT NULL,
						UPDATE_DATE VARCHAR(19) NOT NULL,		
						CONSTRAINT PK_RPT_ADJUST_FORMAT PRIMARY KEY (RPT_NAME, USER_ID, FORMAT_SEQ)) ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				}
			}

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_REWARDHIS ALTER REW_CODE NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_REWARDHIS MODIFY REW_CODE NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_REWARDHIS MODIFY REW_CODE NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_ABSENTSUM", "AS_REMARK","MEMO", "2000", "NULL");
			add_field("PER_PRENAME", "PN_OTHERNAME","MEMO", "1000", "NULL");
			add_field("PER_RELIGION", "RE_OTHERNAME","MEMO", "1000", "NULL");
			add_field("PER_COUNTRY", "CT_OTHERNAME","MEMO", "1000", "NULL");
			add_field("PER_PROVINCE", "PV_OTHERNAME","MEMO", "1000", "NULL");
			add_field("PER_AMPHUR", "AP_OTHERNAME","MEMO", "1000", "NULL");
			add_field("PER_HEIRTYPE", "HR_OTHERNAME","MEMO", "1000", "NULL");
			add_field("PER_OFF_TYPE", "OT_OTHERNAME","MEMO", "1000", "NULL");
			add_field("PER_DIVORCE", "DV_OTHERNAME","MEMO", "1000", "NULL");
			add_field("PER_MARRIED", "MR_OTHERNAME","MEMO", "1000", "NULL");
			add_field("PER_BLOOD", "BL_OTHERNAME","MEMO", "1000", "NULL");
			add_field("PER_ATTACHMENT", "AT_OTHERNAME","MEMO", "1000", "NULL");
			add_field("PER_DISTRICT", "DT_OTHERNAME","MEMO", "1000", "NULL");
			add_field("PER_EXPENSE_BUDGET", "EB_OTHERNAME","MEMO", "1000", "NULL");
			add_field("PER_ORG_TYPE", "OT_OTHERNAME","MEMO", "1000", "NULL");
			add_field("PER_ORG_LEVEL", "OL_OTHERNAME","MEMO", "1000", "NULL");
			add_field("PER_LINE", "PL_OTHERNAME","MEMO", "1000", "NULL");
			add_field("PER_MGT", "PM_OTHERNAME","MEMO", "1000", "NULL");
			add_field("PER_TYPE", "PT_OTHERNAME","MEMO", "1000", "NULL");
			add_field("PER_CONDITION", "PC_OTHERNAME","MEMO", "1000", "NULL");
			add_field("PER_LEVEL", "LEVEL_OTHERNAME","MEMO", "1000", "NULL");
			add_field("PER_POS_GROUP", "PG_OTHERNAME","MEMO", "1000", "NULL");
			add_field("PER_POS_NAME", "PN_OTHERNAME","MEMO", "1000", "NULL");
			add_field("PER_CO_LEVEL", "CL_OTHERNAME","MEMO", "1000", "NULL");
			add_field("PER_STATUS", "PS_OTHERNAME","MEMO", "1000", "NULL");
			add_field("PER_EMPSER_POS_NAME", "EP_OTHERNAME","MEMO", "1000", "NULL");
			add_field("PER_MOVMENT", "MOV_OTHERNAME","MEMO", "1000", "NULL");
			add_field("PER_COMTYPE", "COM_OTHERNAME","MEMO", "1000", "NULL");
			add_field("PER_LINE_GROUP", "LG_OTHERNAME","MEMO", "1000", "NULL");
			add_field("PER_EMP_STATUS", "ES_OTHERNAME","MEMO", "1000", "NULL");
			add_field("PER_COMGROUP", "CG_OTHERNAME","MEMO", "1000", "NULL");
			add_field("PER_TEMP_POS_GROUP", "TG_OTHERNAME","MEMO", "1000", "NULL");
			add_field("PER_TEMP_POS_NAME", "TP_OTHERNAME","MEMO", "1000", "NULL");
			add_field("PER_EXTRATYPE", "EX_OTHERNAME","MEMO", "1000", "NULL");
			add_field("PER_SALARY_MOVMENT", "SM_OTHERNAME","MEMO", "1000", "NULL");
			add_field("PER_ABILITYGRP", "AL_OTHERNAME","MEMO", "1000", "NULL");
			add_field("PER_SKILL_GROUP", "SG_OTHERNAME","MEMO", "1000", "NULL");
			add_field("PER_SKILL", "SKILL_OTHERNAME","MEMO", "1000", "NULL");
			add_field("PER_SPECIAL_SKILLGRP", "SS_OTHERNAME","MEMO", "1000", "NULL");
			add_field("PER_ABSENTTYPE", "AB_OTHERNAME","MEMO", "1000", "NULL");
			add_field("PER_EDUCLEVEL", "EL_OTHERNAME","MEMO", "1000", "NULL");
			add_field("PER_EDUCNAME", "EN_OTHERNAME","MEMO", "1000", "NULL");
			add_field("PER_EDUCMAJOR", "EM_OTHERNAME","MEMO", "1000", "NULL");
			add_field("PER_TRAIN", "TR_OTHERNAME","MEMO", "1000", "NULL");
			add_field("PER_INSTITUTE", "INS_OTHERNAME","MEMO", "1000", "NULL");
			add_field("PER_SCHOLARTYPE", "ST_OTHERNAME","MEMO", "1000", "NULL");
			add_field("PER_PROJECT_GROUP", "PG_OTHERNAME","MEMO", "1000", "NULL");
			add_field("PER_PROJECT_PAYMENT", "PP_OTHERNAME","MEMO", "1000", "NULL");
			add_field("PER_SERVICE", "SV_OTHERNAME","MEMO", "1000", "NULL");
			add_field("PER_SERVICETITLE", "SRT_OTHERNAME","MEMO", "1000", "NULL");
			add_field("PER_DECORATION", "DC_OTHERNAME","MEMO", "1000", "NULL");
			add_field("PER_REWARD", "REW_OTHERNAME","MEMO", "1000", "NULL");
			add_field("PER_TIME", "TIME_OTHERNAME","MEMO", "1000", "NULL");
			add_field("PER_PENALTY", "PEN_OTHERNAME","MEMO", "1000", "NULL");
			add_field("PER_CRIME", "CR_OTHERNAME","MEMO", "1000", "NULL");
			add_field("PER_CRIME_DTL", "CRD_OTHERNAME","MEMO", "1000", "NULL");
			add_field("PER_PERFORMANCE", "PF_OTHERNAME","MEMO", "1000", "NULL");
			add_field("PER_GOODNESS", "GN_OTHERNAME","MEMO", "1000", "NULL");
			add_field("PER_PRACTICE", "PPT_OTHERNAME","MEMO", "1000", "NULL");
			add_field("PER_POS_EMPSER_FRAME", "PEF_OTHERNAME","MEMO", "1000", "NULL");
			add_field("PER_POS_STATUS", "PPS_OTHERNAME","MEMO", "1000", "NULL");
			add_field("PER_OCCUPATION", "OC_OTHERNAME","MEMO", "1000", "NULL");
			add_field("PER_WORK_LOCATION", "WL_OTHERNAME","MEMO", "1000", "NULL");

			$cmd = " UPDATE PER_MOVMENT SET MOV_SUB_TYPE = 8 WHERE MOV_CODE = '21510' AND MOV_SUB_TYPE = 4 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
	
			add_field("PER_EXTRATYPE", "MGT_FLAG","SINGLE", "1", "NULL");

			$cmd = " UPDATE PER_EXTRATYPE SET MGT_FLAG = 1 WHERE EX_NAME LIKE '%เงินประจำตำแหน่ง%' AND MGT_FLAG IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_EXTRATYPE SET MGT_FLAG = 1 WHERE EX_NAME LIKE '%เงินค่าตอบแทนนอกเหนือจากเงินเดือน%' AND MGT_FLAG IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_EXTRATYPE SET MGT_FLAG = 0 WHERE MGT_FLAG IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " select PER_ID, sum(PMH_AMT) as PER_MGTSALARY 
							  from PER_POS_MGTSALARYHIS a, PER_EXTRATYPE b 
							  where trim(a.EX_CODE)=trim(b.EX_CODE) and PMH_ACTIVE = 1 and MGT_FLAG = 1 and 
							  (PMH_ENDDATE is NULL or PMH_ENDDATE >= '$UPDATE_DATE')
							  group by PER_ID
							  order by PER_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			while($data = $db_dpis->get_array()){
				$PER_ID = $data[PER_ID];
				$PER_MGTSALARY = $data[PER_MGTSALARY];
				$cmd = " UPDATE PER_PERSONAL SET PER_MGTSALARY = $PER_MGTSALARY WHERE PER_ID = $PER_ID ";
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();

				$cmd = " select POS_ID from PER_PERSONAL where PER_ID = $PER_ID ";
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
				$data1 = $db_dpis1->get_array();
				$POS_ID = $data1[POS_ID];

				$cmd = " UPDATE PER_POSITION SET POS_MGTSALARY = $PER_MGTSALARY WHERE POS_ID = $POS_ID ";
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
			} // end while						

			$cmd = " select PER_ID, sum(EXH_AMT) as PER_SPSALARY 
							  from PER_EXTRAHIS a, PER_EXTRATYPE b 
							  where trim(a.EX_CODE)=trim(b.EX_CODE) and EXH_ACTIVE = 1 and 
							  (EXH_ENDDATE is NULL or EXH_ENDDATE >= '$UPDATE_DATE')
							  group by PER_ID
							  order by PER_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			while($data = $db_dpis->get_array()){
				$PER_ID = $data[PER_ID];
				$PER_SPSALARY = $data[PER_SPSALARY];
				$cmd = " UPDATE PER_PERSONAL SET PER_SPSALARY = $PER_SPSALARY WHERE PER_ID = $PER_ID ";
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
			} // end while						

			if ($ISCS_FLAG==1) {
				$cmd = " SELECT SE_ID FROM PER_SENIOR_EXCUSIVE ";
				$count_data = $db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
				if (!$count_data) {
					if($DPISDB=="odbc") 
						$cmd = " CREATE TABLE PER_SENIOR_EXCUSIVE(
						SE_ID INTEGER NOT NULL,
						SE_TYPE SINGLE NOT NULL,
						PER_ID INTEGER NULL,
						SE_CODE VARCHAR(10) NOT NULL,	
						SE_NO VARCHAR(10) NOT NULL,	
						PN_CODE VARCHAR(10) NULL,	
						SE_NAME VARCHAR(100) NOT NULL,
						SE_SURNAME VARCHAR(100) NOT NULL,
						SE_CARDNO VARCHAR(13) NULL,	
						SE_MINISTRY_NAME VARCHAR(100) NULL,	
						SE_DEPARTMENT_NAME VARCHAR(100) NULL,	
						SE_ORG_NAME VARCHAR(100) NULL,	
						SE_ORG_NAME1 VARCHAR(100) NULL,	
						SE_ORG_NAME2 VARCHAR(100) NULL,	
						SE_LINE VARCHAR(100) NULL,	
						LEVEL_NO VARCHAR(10) NULL,	
						SE_MGT VARCHAR(100) NULL,	
						SE_TRAIN_POSITION VARCHAR(255) NULL,	
						SE_TRAIN_MINISTRY VARCHAR(100) NULL,	
						SE_TRAIN_DEPARTMENT VARCHAR(100) NULL,	
						SE_PASS VARCHAR(10) NULL,	
						SE_YEAR VARCHAR(4) NULL,	
						SE_BIRTHDATE VARCHAR(19) NULL,	
						SE_STARTDATE VARCHAR(19) NULL,	
						SE_ENDDATE VARCHAR(19) NULL,	
						SE_TEL VARCHAR(50) NULL,	
						SE_FAX VARCHAR(50) NULL,	
						SE_MOBILE VARCHAR(50) NULL,	
						SE_EMAIL VARCHAR(50) NULL,	
						UPDATE_USER INTEGER NOT NULL,
						UPDATE_DATE VARCHAR(19) NOT NULL,		
						CONSTRAINT PK_PER_SENIOR_EXCUSIVE PRIMARY KEY (SE_ID)) ";
					elseif($DPISDB=="oci8") 
						$cmd = " CREATE TABLE PER_SENIOR_EXCUSIVE(
						SE_ID NUMBER(11) NOT NULL,
						SE_TYPE NUMBER(1) NOT NULL,
						PER_ID NUMBER(11) NULL,
						SE_CODE VARCHAR2(10) NOT NULL,	
						SE_NO VARCHAR2(10) NOT NULL,	
						PN_CODE VARCHAR2(10) NULL,	
						SE_NAME VARCHAR2(100) NOT NULL,
						SE_SURNAME VARCHAR2(100) NOT NULL,
						SE_CARDNO VARCHAR2(13) NULL,	
						SE_MINISTRY_NAME VARCHAR2(100) NULL,	
						SE_DEPARTMENT_NAME VARCHAR2(100) NULL,	
						SE_ORG_NAME VARCHAR2(100) NULL,	
						SE_ORG_NAME1 VARCHAR2(100) NULL,	
						SE_ORG_NAME2 VARCHAR2(100) NULL,	
						SE_LINE VARCHAR2(100) NULL,	
						LEVEL_NO VARCHAR2(10) NULL,	
						SE_MGT VARCHAR2(100) NULL,	
						SE_TRAIN_POSITION VARCHAR2(255) NULL,	
						SE_TRAIN_MINISTRY VARCHAR2(100) NULL,	
						SE_TRAIN_DEPARTMENT VARCHAR2(100) NULL,	
						SE_PASS VARCHAR2(10) NULL,	
						SE_YEAR VARCHAR2(4) NULL,	
						SE_BIRTHDATE VARCHAR2(19) NULL,	
						SE_STARTDATE VARCHAR2(19) NULL,	
						SE_ENDDATE VARCHAR2(19) NULL,	
						SE_TEL VARCHAR2(50) NULL,	
						SE_FAX VARCHAR2(50) NULL,	
						SE_MOBILE VARCHAR2(50) NULL,	
						SE_EMAIL VARCHAR2(50) NULL,	
						UPDATE_USER NUMBER(11) NOT NULL,
						UPDATE_DATE VARCHAR2(19) NOT NULL,		
						CONSTRAINT PK_PER_SENIOR_EXCUSIVE PRIMARY KEY (SE_ID)) ";
					elseif($DPISDB=="mysql")
						$cmd = " CREATE TABLE PER_SENIOR_EXCUSIVE(
						SE_ID INTEGER(11) NOT NULL,
						SE_TYPE SMALLINT(1) NOT NULL,
						PER_ID INTEGER(11) NULL,
						SE_CODE VARCHAR(10) NOT NULL,	
						SE_NO VARCHAR(10) NOT NULL,	
						PN_CODE VARCHAR(10) NULL,	
						SE_NAME VARCHAR(100) NOT NULL,
						SE_SURNAME VARCHAR(100) NOT NULL,
						SE_CARDNO VARCHAR(13) NULL,	
						SE_MINISTRY_NAME VARCHAR(100) NULL,	
						SE_DEPARTMENT_NAME VARCHAR(100) NULL,	
						SE_ORG_NAME VARCHAR(100) NULL,	
						SE_ORG_NAME1 VARCHAR(100) NULL,	
						SE_ORG_NAME2 VARCHAR(100) NULL,	
						SE_LINE VARCHAR(100) NULL,	
						LEVEL_NO VARCHAR(10) NULL,	
						SE_MGT VARCHAR(100) NULL,	
						SE_TRAIN_POSITION VARCHAR(255) NULL,	
						SE_TRAIN_MINISTRY VARCHAR(100) NULL,	
						SE_TRAIN_DEPARTMENT VARCHAR(100) NULL,	
						SE_PASS VARCHAR(10) NULL,	
						SE_YEAR VARCHAR(4) NULL,	
						SE_BIRTHDATE VARCHAR(19) NULL,	
						SE_STARTDATE VARCHAR(19) NULL,	
						SE_ENDDATE VARCHAR(19) NULL,	
						SE_TEL VARCHAR(50) NULL,	
						SE_FAX VARCHAR(50) NULL,	
						SE_MOBILE VARCHAR(50) NULL,	
						SE_EMAIL VARCHAR(50) NULL,	
						UPDATE_USER INTEGER(11) NOT NULL,
						UPDATE_DATE VARCHAR(19) NOT NULL,		
						CONSTRAINT PK_PER_SENIOR_EXCUSIVE PRIMARY KEY (SE_ID)) ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				}

				$cmd = " SELECT CA_ID FROM PER_MGT_COMPETENCY_ASSESSMENT ";
				$count_data = $db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
				if (!$count_data) {
					if($DPISDB=="odbc") 
						$cmd = " CREATE TABLE PER_MGT_COMPETENCY_ASSESSMENT(
						CA_ID INTEGER NOT NULL,
						CA_COURSE SINGLE NOT NULL,
						ORG_CODE VARCHAR(10) NOT NULL,	
						CA_SEQ INTEGER NULL,
						CA_CODE VARCHAR(10) NOT NULL,	
						CA_TYPE SINGLE NOT NULL,
						PER_ID INTEGER NULL,
						CA_TEST_DATE VARCHAR(19) NULL,	
						CA_APPROVE_DATE VARCHAR(19) NULL,	
						PN_CODE VARCHAR(10) NULL,	
						CA_NAME VARCHAR(100) NOT NULL,
						CA_SURNAME VARCHAR(100) NOT NULL,
						CA_CARDNO VARCHAR(13) NULL,	
						CA_CONSISTENCY INTEGER2 NULL,
						CA_SCORE_1 INTEGER2 NULL,
						CA_SCORE_2 INTEGER2 NULL,
						CA_SCORE_3 INTEGER2 NULL,
						CA_SCORE_4 INTEGER2 NULL,
						CA_SCORE_5 INTEGER2 NULL,
						CA_SCORE_6 INTEGER2 NULL,
						CA_SCORE_7 INTEGER2 NULL,
						CA_SCORE_8 INTEGER2 NULL,
						CA_SCORE_9 INTEGER2 NULL,
						CA_SCORE_10 INTEGER2 NULL,
						CA_SCORE_11 INTEGER2 NULL,
						CA_SCORE_12 INTEGER2 NULL,
						CA_MEAN NUMBER NULL,
						CA_MINISTRY_NAME VARCHAR(100) NULL,	
						CA_DEPARTMENT_NAME VARCHAR(100) NULL,	
						CA_ORG_NAME VARCHAR(100) NULL,	
						CA_ORG_NAME1 VARCHAR(100) NULL,	
						CA_ORG_NAME2 VARCHAR(100) NULL,	
						CA_LINE VARCHAR(100) NULL,	
						LEVEL_NO VARCHAR(10) NULL,	
						CA_MGT VARCHAR(100) NULL,	
						CA_POSITION VARCHAR(100) NULL,	
						CA_POS_NO VARCHAR(10) NULL,	
						CA_DOC_DATE VARCHAR(19) NULL,	
						CA_DOC_NO VARCHAR(20) NULL,	
						CA_NEW_SCORE_1 INTEGER2 NULL,
						CA_NEW_SCORE_2 INTEGER2 NULL,
						CA_NEW_SCORE_3 INTEGER2 NULL,
						CA_NEW_SCORE_4 INTEGER2 NULL,
						CA_NEW_SCORE_5 INTEGER2 NULL,
						CA_NEW_SCORE_6 INTEGER2 NULL,
						CA_NEW_SCORE_7 INTEGER2 NULL,
						CA_NEW_SCORE_8 INTEGER2 NULL,
						CA_NEW_SCORE_9 INTEGER2 NULL,
						CA_NEW_SCORE_10 INTEGER2 NULL,
						CA_NEW_SCORE_11 INTEGER2 NULL,
						CA_NEW_MEAN NUMBER NULL,
						CA_REMARK MEMO NULL,	
						UPDATE_USER INTEGER NOT NULL,
						UPDATE_DATE VARCHAR(19) NOT NULL,		
						CONSTRAINT PK_MGT_COMPETENCY_ASSESSMENT PRIMARY KEY (CA_ID)) ";
					elseif($DPISDB=="oci8") 
						$cmd = " CREATE TABLE PER_MGT_COMPETENCY_ASSESSMENT(
						CA_ID NUMBER(10) NOT NULL,
						CA_COURSE NUMBER(1) NOT NULL,
						ORG_CODE VARCHAR2(10) NOT NULL,	
						CA_SEQ NUMBER(11) NULL,
						CA_CODE VARCHAR2(10) NOT NULL,	
						CA_TYPE NUMBER(1) NOT NULL,
						PER_ID NUMBER(11) NULL,
						CA_TEST_DATE VARCHAR2(19) NULL,	
						CA_APPROVE_DATE VARCHAR2(19) NULL,	
						PN_CODE VARCHAR2(10) NULL,	
						CA_NAME VARCHAR2(100) NOT NULL,
						CA_SURNAME VARCHAR2(100) NOT NULL,
						CA_CARDNO VARCHAR2(13) NULL,	
						CA_CONSISTENCY NUMBER(3) NULL,
						CA_SCORE_1 NUMBER(3) NULL,
						CA_SCORE_2 NUMBER(3) NULL,
						CA_SCORE_3 NUMBER(3) NULL,
						CA_SCORE_4 NUMBER(3) NULL,
						CA_SCORE_5 NUMBER(3) NULL,
						CA_SCORE_6 NUMBER(3) NULL,
						CA_SCORE_7 NUMBER(3) NULL,
						CA_SCORE_8 NUMBER(3) NULL,
						CA_SCORE_9 NUMBER(3) NULL,
						CA_SCORE_10 NUMBER(3) NULL,
						CA_SCORE_11 NUMBER(3) NULL,
						CA_SCORE_12 NUMBER(3) NULL,
						CA_MEAN NUMBER(5,2) NULL,
						CA_MINISTRY_NAME VARCHAR2(100) NULL,	
						CA_DEPARTMENT_NAME VARCHAR2(100) NULL,	
						CA_ORG_NAME VARCHAR2(100) NULL,	
						CA_ORG_NAME1 VARCHAR2(100) NULL,	
						CA_ORG_NAME2 VARCHAR2(100) NULL,	
						CA_LINE VARCHAR2(100) NULL,	
						LEVEL_NO VARCHAR2(10) NULL,	
						CA_MGT VARCHAR2(100) NULL,	
						CA_POSITION VARCHAR2(100) NULL,	
						CA_POS_NO VARCHAR2(10) NULL,	
						CA_DOC_DATE VARCHAR2(19) NULL,	
						CA_DOC_NO VARCHAR2(20) NULL,	
						CA_NEW_SCORE_1 NUMBER(3) NULL,
						CA_NEW_SCORE_2 NUMBER(3) NULL,
						CA_NEW_SCORE_3 NUMBER(3) NULL,
						CA_NEW_SCORE_4 NUMBER(3) NULL,
						CA_NEW_SCORE_5 NUMBER(3) NULL,
						CA_NEW_SCORE_6 NUMBER(3) NULL,
						CA_NEW_SCORE_7 NUMBER(3) NULL,
						CA_NEW_SCORE_8 NUMBER(3) NULL,
						CA_NEW_SCORE_9 NUMBER(3) NULL,
						CA_NEW_SCORE_10 NUMBER(3) NULL,
						CA_NEW_SCORE_11 NUMBER(3) NULL,
						CA_NEW_MEAN NUMBER(5,2) NULL,
						CA_REMARK VARCHAR2(1000) NULL,	
						UPDATE_USER NUMBER(11) NOT NULL,
						UPDATE_DATE VARCHAR2(19) NOT NULL,		
						CONSTRAINT PK_MGT_COMPETENCY_ASSESSMENT PRIMARY KEY (CA_ID)) ";
					elseif($DPISDB=="mysql")
						$cmd = " CREATE TABLE PER_MGT_COMPETENCY_ASSESSMENT(
						CA_ID INTEGER(10) NOT NULL,
						CA_COURSE SMALLINT(1) NOT NULL,
						ORG_CODE VARCHAR(10) NOT NULL,	
						CA_SEQ INTEGER(11) NULL,
						CA_CODE VARCHAR(10) NOT NULL,	
						CA_TYPE SMALLINT(1) NOT NULL,
						PER_ID INTEGER(11) NULL,
						CA_TEST_DATE VARCHAR(19) NULL,	
						CA_APPROVE_DATE VARCHAR(19) NULL,	
						PN_CODE VARCHAR(10) NULL,	
						CA_NAME VARCHAR(100) NOT NULL,
						CA_SURNAME VARCHAR(100) NOT NULL,
						CA_CARDNO VARCHAR(13) NULL,	
						CA_CONSISTENCY SMALLINT(3) NULL,
						CA_SCORE_1 SMALLINT(3) NULL,
						CA_SCORE_2 SMALLINT(3) NULL,
						CA_SCORE_3 SMALLINT(3) NULL,
						CA_SCORE_4 SMALLINT(3) NULL,
						CA_SCORE_5 SMALLINT(3) NULL,
						CA_SCORE_6 SMALLINT(3) NULL,
						CA_SCORE_7 SMALLINT(3) NULL,
						CA_SCORE_8 SMALLINT(3) NULL,
						CA_SCORE_9 SMALLINT(3) NULL,
						CA_SCORE_10 SMALLINT(3) NULL,
						CA_SCORE_11 SMALLINT(3) NULL,
						CA_SCORE_12 SMALLINT(3) NULL,
						CA_MEAN DECIMAL(5,2) NULL,
						CA_MINISTRY_NAME VARCHAR(100) NULL,	
						CA_DEPARTMENT_NAME VARCHAR(100) NULL,	
						CA_ORG_NAME VARCHAR(100) NULL,	
						CA_ORG_NAME1 VARCHAR(100) NULL,	
						CA_ORG_NAME2 VARCHAR(100) NULL,	
						CA_LINE VARCHAR(100) NULL,	
						LEVEL_NO VARCHAR(10) NULL,	
						CA_MGT VARCHAR(100) NULL,	
						CA_POSITION VARCHAR(100) NULL,	
						CA_POS_NO VARCHAR(10) NULL,	
						CA_DOC_DATE VARCHAR(19) NULL,	
						CA_DOC_NO VARCHAR(20) NULL,	
						CA_NEW_SCORE_1 SMALLINT(3) NULL,
						CA_NEW_SCORE_2 SMALLINT(3) NULL,
						CA_NEW_SCORE_3 SMALLINT(3) NULL,
						CA_NEW_SCORE_4 SMALLINT(3) NULL,
						CA_NEW_SCORE_5 SMALLINT(3) NULL,
						CA_NEW_SCORE_6 SMALLINT(3) NULL,
						CA_NEW_SCORE_7 SMALLINT(3) NULL,
						CA_NEW_SCORE_8 SMALLINT(3) NULL,
						CA_NEW_SCORE_9 SMALLINT(3) NULL,
						CA_NEW_SCORE_10 SMALLINT(3) NULL,
						CA_NEW_SCORE_11 SMALLINT(3) NULL,
						CA_NEW_MEAN DECIMAL(5,2) NULL,
						CA_REMARK TEXT NULL,	
						UPDATE_USER INTEGER(11) NOT NULL,
						UPDATE_DATE VARCHAR(19) NOT NULL,		
						CONSTRAINT PK_MGT_COMPETENCY_ASSESSMENT PRIMARY KEY (CA_ID)) ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				}

				$cmd = " CREATE TABLE PER_VER_COMPETENCY_ASSESSMENT AS SELECT * FROM PER_MGT_COMPETENCY_ASSESSMENT WHERE CA_ID = 0 ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			}

			add_field("PER_POS_TEMP", "POT_NO_NAME","VARCHAR", "15", "NULL");
			add_field("PER_TEMP_POS_NAME", "LEVEL_NO","VARCHAR", "10", "NULL");

			$cmd = " SELECT COM_ID, CMD_SEQ, POS_ID, POEM_ID, POEMS_ID, POT_ID 
							FROM PER_COMDTL WHERE ORG_NAME_WORK IS NULL AND (POS_ID IS NOT NULL OR POEM_ID IS NOT NULL OR POEMS_ID IS NOT NULL OR POT_ID IS NOT NULL) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			while($data = $db_dpis->get_array()){
				$PL_NAME_WORK = $ORG_NAME_WORK = $LEVEL_NO_POS = $POSITION_LEVEL = $LEVEL_NAME = $DEPARTMENT_NAME = $ORG_NAME = $ORG_NAME_1 = $ORG_NAME_2 = $PL_NAME = $PM_NAME = $tmp_ORG_ID =  "";
				$COM_ID = $data[COM_ID];
				$CMD_SEQ = $data[CMD_SEQ];
				$POS_ID = $data[POS_ID];
				$POEM_ID = $data[POEM_ID];
				$POEMS_ID = $data[POEMS_ID];
				$POT_ID = $data[POT_ID];

				if (trim($POS_ID)) {
					$cmd = "	select	a.PL_CODE, PL_NAME, ORG_ID, ORG_ID_1, ORG_ID_2, DEPARTMENT_ID, LEVEL_NO, a.PM_CODE 
									from		PER_POSITION a, PER_LINE b 
									where	POS_ID=$POS_ID and a.PL_CODE=b.PL_CODE ";
				} elseif (trim($POEM_ID)) {
					$cmd = "	select	a.PN_CODE, PN_NAME, ORG_ID, ORG_ID_1, ORG_ID_2, DEPARTMENT_ID , LEVEL_NO
									from		PER_POS_EMP a, PER_POS_NAME b 
									where	POEM_ID=$POEM_ID and a.PN_CODE=b.PN_CODE";
				} elseif (trim($POEMS_ID)) {
					$cmd = "	select	a.EP_CODE, EP_NAME, ORG_ID, ORG_ID_1, ORG_ID_2, DEPARTMENT_ID , LEVEL_NO
									from		PER_POS_EMPSER a, PER_EMPSER_POS_NAME b 
									where	POEMS_ID=$POEMS_ID and a.EP_CODE=b.EP_CODE";
				} elseif (trim($POT_ID)) {
					$cmd = "	select	a.TP_CODE, TP_NAME, ORG_ID, ORG_ID_1, ORG_ID_2, DEPARTMENT_ID , LEVEL_NO
									from		PER_POS_TEMP a, PER_TEMP_POS_NAME b 
									where	POT_ID=$POT_ID and a.TP_CODE=b.TP_CODE";
				}
				$db_dpis1->send_cmd($cmd);		
				$data1 = $db_dpis1->get_array();
				$DEPARTMENT_ID = $data1[DEPARTMENT_ID];
				$POS_ORG_ID = $data1[ORG_ID];
				$POS_ORG_ID_1 = $data1[ORG_ID_1];
				$POS_ORG_ID_2 = $data1[ORG_ID_2];
				$LEVEL_NO_POS = trim($data1[LEVEL_NO]);
				if (trim($POS_ID)) {
					$PL_NAME = trim($data1[PL_NAME]); 
					$PM_CODE = trim($data1[PM_CODE]);
					$cmd = " select LEVEL_NAME, POSITION_LEVEL 
									from PER_PERSONAL a, PER_LEVEL b where a.POS_ID=$POS_ID and a.LEVEL_NO=b.LEVEL_NO ";
				} elseif (trim($POEM_ID)) {
					$PL_NAME = trim($data1[PN_NAME]); 
					$cmd = " select LEVEL_NAME, POSITION_LEVEL 
									from PER_PERSONAL a, PER_LEVEL b where a.POEM_ID=$POEM_ID and a.LEVEL_NO=b.LEVEL_NO ";
				} elseif (trim($POEMS_ID)) {
					$PL_NAME = trim($data1[EP_NAME]); 
					$cmd = " select LEVEL_NAME, POSITION_LEVEL 
									from PER_PERSONAL a, PER_LEVEL b where a.POEMS_ID=$POEMS_ID and a.LEVEL_NO=b.LEVEL_NO ";
				} elseif (trim($POT_ID)) {
					$PL_NAME = trim($data1[TP_NAME]); 
					$cmd = " select LEVEL_NAME, POSITION_LEVEL 
									from PER_PERSONAL a, PER_LEVEL b where a.POT_ID=$POT_ID and a.LEVEL_NO=b.LEVEL_NO ";
				} 

				if ($LEVEL_NO_POS) {
					$cmd = " select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$LEVEL_NO_POS' ";
				}
				$db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$LEVEL_NAME = $data1[LEVEL_NAME];
				$POSITION_LEVEL = $data1[POSITION_LEVEL];
				if (!$POSITION_LEVEL) $POSITION_LEVEL = $LEVEL_NAME;

				$cmd = " select OT_CODE from PER_ORG where ORG_ID=$POS_ORG_ID ";
				$db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$OT_CODE = trim($data1[OT_CODE]);
		
				if ($DEPARTMENT_ID) $tmp_ORG_ID[] =  $DEPARTMENT_ID;
				if ($POS_ORG_ID)			$tmp_ORG_ID[] =  $POS_ORG_ID;
				if ($POS_ORG_ID_1)	$tmp_ORG_ID[] =  $POS_ORG_ID_1;
				if ($POS_ORG_ID_2)	$tmp_ORG_ID[] =  $POS_ORG_ID_2;
				$search_org_id = implode(", ", $tmp_ORG_ID);
				
				$cmd = " select ORG_ID, ORG_NAME from PER_ORG 	where ORG_ID in ($search_org_id) ";
				$db_dpis1->send_cmd($cmd);		
				while ( $data1 = $db_dpis1->get_array() ) {
					$DEPARTMENT_NAME = ($DEPARTMENT_ID == trim($data1[ORG_ID]))? 		trim($data1[ORG_NAME]) : "$DEPARTMENT_NAME";
					$ORG_NAME = ($POS_ORG_ID == trim($data1[ORG_ID]))?  trim($data1[ORG_NAME]) : $ORG_NAME ;
					$ORG_NAME_1 = ($POS_ORG_ID_1 == trim($data1[ORG_ID]))?  trim($data1[ORG_NAME]) : $ORG_NAME_1 ;
					$ORG_NAME_2 = ($POS_ORG_ID_2 == trim($data1[ORG_ID]))?  trim($data1[ORG_NAME]) : $ORG_NAME_2 ;
				}
			
				$cmd = " select PM_NAME from PER_MGT where PM_CODE='$PM_CODE' ";
				$db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$PM_NAME = $data1[PM_NAME];
				
				if ($PM_NAME) $PL_NAME_WORK = $PM_NAME." (".$PL_NAME.$POSITION_LEVEL.")";
				else $PL_NAME_WORK = $PL_NAME.$POSITION_LEVEL;
				
				if ($ORG_NAME=="-") $ORG_NAME = "";		
				if ($ORG_NAME_1=="-") $ORG_NAME_1 = "";		
				if ($ORG_NAME_2=="-") $ORG_NAME_2 = "";		
				if ($OT_CODE == "03") 
					if (!$ORG_NAME_2 && !$ORG_NAME_1 && $DEPARTMENT_NAME=="กรมการปกครอง") 
						$ORG_NAME_WORK = "ที่ทำการปกครอง".$ORG_NAME." ".$ORG_NAME;
					else 
						$ORG_NAME_WORK = trim($ORG_NAME_2." ".$ORG_NAME_1." ".$ORG_NAME);
				elseif ($OT_CODE == "01") $ORG_NAME_WORK = trim($ORG_NAME_1." ".$ORG_NAME);
				else $ORG_NAME_WORK = trim($ORG_NAME_1." ".$ORG_NAME);

				$cmd = " UPDATE  PER_COMDTL SET PL_NAME_WORK = '$PL_NAME_WORK', ORG_NAME_WORK = '$ORG_NAME_WORK' 
								WHERE COM_ID = $COM_ID AND CMD_SEQ = $CMD_SEQ ";
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
				//echo "$cmd<br>";
			} // end while

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'data_course.html' WHERE LINKTO_WEB = 'data_coursedtl.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'structure_org.html' WHERE LINKTO_WEB = 'structure_by_law.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'structure_org.html?BYASS=Y' WHERE LINKTO_WEB = 'structure_by_assign.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE  PER_SALARYHIS SET  SAH_KF_CYCLE = 1 
							WHERE SAH_EFFECTIVEDATE >= '2010-04-01' AND SUBSTR(SAH_EFFECTIVEDATE,6,5) = '04-01' AND SAH_KF_CYCLE != 1 ";
			$db_dpis1->send_cmd($cmd);
			//$db_dpis1->show_error();

			$cmd = " UPDATE  PER_SALARYHIS SET  SAH_KF_CYCLE = 2 
							WHERE SAH_EFFECTIVEDATE >= '2010-04-01' AND SUBSTR(SAH_EFFECTIVEDATE,6,5) = '10-01' AND SAH_KF_CYCLE != 2 ";
			$db_dpis1->send_cmd($cmd);
			//$db_dpis1->show_error();

			$cmd = " SELECT LT_CODE FROM PER_LICENSE_TYPE ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) {
				if($DPISDB=="odbc") 
					$cmd = " CREATE TABLE PER_LICENSE_TYPE(
					LT_CODE VARCHAR(10) NOT NULL,	
					LT_NAME VARCHAR(100) NOT NULL,
					LT_REMARK MEMO NULL,
					LT_ACTIVE SINGLE NOT NULL,
					UPDATE_USER INTEGER NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					LT_SEQ_NO INTEGER2 NULL,
					LT_OTHERNAME MEMO NULL,
					CONSTRAINT PK_PER_LICENSE_TYPE PRIMARY KEY (LT_CODE)) ";
				elseif($DPISDB=="oci8") 
					$cmd = " CREATE TABLE PER_LICENSE_TYPE(
					LT_CODE VARCHAR2(10) NOT NULL,	
					LT_NAME VARCHAR2(100) NOT NULL,
					LT_REMARK VARCHAR2(2000) NULL,
					LT_ACTIVE NUMBER(1) NOT NULL,
					UPDATE_USER NUMBER(11) NOT NULL,
					UPDATE_DATE VARCHAR2(19) NOT NULL,		
					LT_SEQ_NO NUMBER(5) NULL,
					LT_OTHERNAME VARCHAR2(1000) NULL,
					CONSTRAINT PK_PER_LICENSE_TYPE PRIMARY KEY (LT_CODE)) ";
				elseif($DPISDB=="mysql")
					$cmd = " CREATE TABLE PER_LICENSE_TYPE(
					LT_CODE VARCHAR(10) NOT NULL,	
					LT_NAME VARCHAR(100) NOT NULL,
					LT_REMARK TEXT NULL,
					LT_ACTIVE SMALLINT(1) NOT NULL,
					UPDATE_USER INTEGER(11) NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					LT_SEQ_NO SMALLINT(5) NULL,
					LT_OTHERNAME TEXT NULL,
					CONSTRAINT PK_PER_LICENSE_TYPE PRIMARY KEY (LT_CODE)) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_LICENSE_TYPE (LT_CODE, LT_NAME, LT_REMARK, LT_ACTIVE, UPDATE_USER, UPDATE_DATE, LT_SEQ_NO)
								  VALUES ('01', 'ใบอนุญาตประกอบโรคศิลปะ', 'เป็นใบอนุญาตในการประกอบวิชาชีพที่กระทำหรือมุ่งหมายจะกระทำต่อมนุษย์เกี่ยวกับการตรวจโรค การวินิจฉัยโรค การบำบัดโรค การป้องกันโรค การส่งเสริมและการฟื้นฟูสุขภาพ และการผดุงครรภ์ แต่ไม่รวมถึงการประกอบวิชาชีพทางการแพทย์และสาธารณสุขอื่น ที่มีการตั้งสภาวิชาชีพเพื่อควบคุมดูแลในแต่ละวิชาชีพแล้ว ซึ่งใบอนุญาตประกอบโรคศิลปะออกโดยคณะกรรมการการประกอบโรคศิลปะ กองการประกอบโรคศิลปะ กระทรวงสาธารณสุข ในปัจจุบัน มีวิชาชีพที่ต้องขึ้นทะเบียนและได้รับใบอนุญาตประกอบโรคศิลปะ จากกองประกอบโรคศิลปะ กระทรวงสาธารณสุข[1] ดังนี้
 สาขากิจกรรมบำบัด
 สาขารังสีเทคนิค
 สาขาการแก้ไขความผิดปกติของการสื่อความหมาย
 สาขาเทคโนโลยีหัวใจและทรวงอก
 สาขาจิตวิทยาคลินิก
 สาขาการแพทย์แผนไทย
 สาขาการแพทย์แผนไทยประยุกต์
 สาขาการแพทย์แผนจีน
 สาขาทัศนมาตรศาสตร์
 สาขากายอุปกรณ์
', 1, $SESS_USERID, '$UPDATE_DATE', 1) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
		
				$cmd = " INSERT INTO PER_LICENSE_TYPE (LT_CODE, LT_NAME, LT_REMARK, LT_ACTIVE, UPDATE_USER, UPDATE_DATE, LT_SEQ_NO)
								  VALUES ('02', 'ใบอนุญาตประกอบวิชาชีพเวชกรรม', 'ออกโดย แพทยสภา', 1, $SESS_USERID, '$UPDATE_DATE', 2) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
		
				$cmd = " INSERT INTO PER_LICENSE_TYPE (LT_CODE, LT_NAME, LT_REMARK, LT_ACTIVE, UPDATE_USER, UPDATE_DATE, LT_SEQ_NO)
								  VALUES ('03', 'ใบอนุญาตประกอบวิชาชีพทันตกรรม', 'ออกโดย ทันตแพทยสภา', 1, $SESS_USERID, '$UPDATE_DATE', 3) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
		
				$cmd = " INSERT INTO PER_LICENSE_TYPE (LT_CODE, LT_NAME, LT_REMARK, LT_ACTIVE, UPDATE_USER, UPDATE_DATE, LT_SEQ_NO)
								  VALUES ('04', 'ใบอนุญาตประกอบวิชาชีพการสัตวแพทย์', 'ออกโดย สัตวแพทยสภา', 1, $SESS_USERID, '$UPDATE_DATE', 4) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
		
				$cmd = " INSERT INTO PER_LICENSE_TYPE (LT_CODE, LT_NAME, LT_REMARK, LT_ACTIVE, UPDATE_USER, UPDATE_DATE, LT_SEQ_NO)
								  VALUES ('05', 'ใบอนุญาตประกอบวิชาชีพเภสัชกรรม', 'ออกโดย สภาเภสัชกรรม', 1, $SESS_USERID, '$UPDATE_DATE', 5) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
		
				$cmd = " INSERT INTO PER_LICENSE_TYPE (LT_CODE, LT_NAME, LT_REMARK, LT_ACTIVE, UPDATE_USER, UPDATE_DATE, LT_SEQ_NO)
								  VALUES ('06', 'ใบอนุญาตประกอบวิชาชีพการพยาบาลและการผดุงครรภ์', 'ออกโดย สภาการพยาบาล', 1, $SESS_USERID, '$UPDATE_DATE', 6) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
		
				$cmd = " INSERT INTO PER_LICENSE_TYPE (LT_CODE, LT_NAME, LT_REMARK, LT_ACTIVE, UPDATE_USER, UPDATE_DATE, LT_SEQ_NO)
								  VALUES ('07', 'ใบอนุญาตประกอบวิชาชีพเทคนิคการแพทย์', 'ออกโดย สภาเทคนิคการแพทย์', 1, $SESS_USERID, '$UPDATE_DATE', 7) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
		
				$cmd = " INSERT INTO PER_LICENSE_TYPE (LT_CODE, LT_NAME, LT_REMARK, LT_ACTIVE, UPDATE_USER, UPDATE_DATE, LT_SEQ_NO)
								  VALUES ('08', 'ใบอนุญาตประกอบวิชาชีพกายภาพบำบัด', 'ออกโดย สภากายภาพบำบัด', 1, $SESS_USERID, '$UPDATE_DATE', 8) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
		
				$cmd = " INSERT INTO PER_LICENSE_TYPE (LT_CODE, LT_NAME, LT_REMARK, LT_ACTIVE, UPDATE_USER, UPDATE_DATE, LT_SEQ_NO)
								  VALUES ('09', 'ใบอนุญาตให้เป็นทนายความ', 'ออกโดย สภาทนายความ', 1, $SESS_USERID, '$UPDATE_DATE', 9) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
		
				$cmd = " INSERT INTO PER_LICENSE_TYPE (LT_CODE, LT_NAME, LT_REMARK, LT_ACTIVE, UPDATE_USER, UPDATE_DATE, LT_SEQ_NO)
								  VALUES ('10', 'ใบอนุญาตประกอบวิชาชีพวิศวกรรมควบคุม', 'ใบอนุญาตประกอบวิชาชีพวิศวกรรมควบคุมในประเทศไทย เป็นใบอนุญาตให้บุคคลประกอบวิชาชีพวิศวกรรมควบคุม ออกโดยสภาวิศวกร เริ่มต้นในประเทศไทยครั้งแรกตามพระราชบัญญัติวิชาชีพวิศวกรรม พ.ศ. 2505[1] ปัจจุบันบังคับใช้ตามพระราชบัญญัติวิศวกร พ.ศ. 2542[2] ประกอบด้วยสาขาวิศวกรรมควบคุม ได้แก่ วิศวกรรมโยธา วิศวกรรมไฟฟ้ากำลัง วิศวกรรมไฟฟ้าสื่อสาร วิศวกรรมเครื่องกล วิศวกรรมอุตสาหการ วิศวกรรมเหมืองแร่ วิศวกรรมเคมี และวิศวกรรมสิ่งแวดล้อม ผู้ประกอบวิชาชีพวิศวกรรมควบคุม แต่ละสาขาแบ่งเป็น 4 ระดับ คือ
 1.วุฒิวิศวกร
 2.สามัญวิศวกร
 3.ภาคีวิศวกร
 4.ภาคีวิศวกรพิเศษ
', 1, $SESS_USERID, '$UPDATE_DATE', 10) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
		
				$cmd = " INSERT INTO PER_LICENSE_TYPE (LT_CODE, LT_NAME, LT_REMARK, LT_ACTIVE, UPDATE_USER, UPDATE_DATE, LT_SEQ_NO)
								  VALUES ('11', 'ใบอนุญาตประกอบวิชาชีพสถาปัตยกรรม', 'ใบประกอบวิชาชีพสถาปัตยกรรม หรือเดิม (ก่อน พ.ร.บ. สภาสถาปนิก 2543) เรียกย่อว่า ก.ส. เป็นเอกสารรับรองของสถาปนิกในการออกแบบและเซ็นรับรองแบบ ที่รับรองโดยสภาสถาปนิก ในปัจจุบันมีอยู่ 4 สาขาด้วยกันได้แก่ สถาปัตยกรรมหลัก สถาปัตยกรรมภายในและมัณฑนศิลป์ สถาปัตยกรรมผังเมือง และ ภูมิสถาปัตยกรรม ใบประกอบวิชาชีพฯมี 4 ระดับเรียงจากสูงไปต่ำ คือ วุฒิสถาปนิก สามัญสถาปนิก ภาคีสถาปนิก และ สถาปนิกภาคีพิเศษขอบเขตงานที่ควบคุมในสาขาต่างๆ ถูกกำหนดไว้โดยในกฎกระทรวง (พ.ศ. 2549) ว่าด้วยการกำหนดวิชาชีพสถาปัตยกรรมควบคุม
 
กฎกระทรวงการกำหนดวิชาชีพสถาปัตยกรรมควบคุมนี้ ได้มีการกำหนดให้งานสถาปัตยกรรมควบคุมสาขาต่างๆ ทั้ง 4 สาขานั้น ผู้ดำเนินการงานออกแบบ ให้คำปรึกษา ตรวจสอบ ศึกษาโครงการ บริหารและอำนวยการก่อสร้าง ต้องเป็นสถาปนิก (แต่ละสาขา) ที่มีใบอนุญาตประกอบวิชาชีพในระดับขั้นต่างๆกันไป ตามขอบเขตของงาน
', 1, $SESS_USERID, '$UPDATE_DATE', 11) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
		
				$cmd = " INSERT INTO PER_LICENSE_TYPE (LT_CODE, LT_NAME, LT_REMARK, LT_ACTIVE, UPDATE_USER, UPDATE_DATE, LT_SEQ_NO)
								  VALUES ('12', 'ใบอนุญาตประกอบวิชาชีพทางการศึกษา', 'ใบอนุญาตประกอบวิชาชีพทางการศึกษา เป็นหลักฐานการอนุญาตให้ผู้ประกอบวิชาชีพควบคุมตามมาตรา 43 แห่งพระราชบัญญัติสภาครูและบุคลากรทางการศึกษา พ.ศ. 2546[1] เป็นผู้มีสิทธิ์ในการประกอบวิชาชีพ ซึ่งได้แก่ ครู ผู้บริหารสถานศึกษา ผู้บริหารการศึกษา และบุคลากรทางการศึกษาอื่น ซึ่งหน่วยงานที่มีหน้าที่ในการออกใบอนุญาตฯ คือ คุรุสภา ประเภทของใบอนุญาต
 ใบอนุญาตประกอบวิชาชีพครู
 ใบอนุญาตประกอบวิชาชีพผู้บริหารสถานศึกษา
 ใบอนุญาตประกอบวิชาชีพผู้บริหารการศึกษา
 ใบอนุญาตประกอบวิชาชีพบุคลากรทางการศึกษาอื่น
', 1, $SESS_USERID, '$UPDATE_DATE', 12) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
		
				$cmd = " INSERT INTO PER_LICENSE_TYPE (LT_CODE, LT_NAME, LT_REMARK, LT_ACTIVE, UPDATE_USER, UPDATE_DATE, LT_SEQ_NO)
								  VALUES ('13', 'ใบอนุญาตประกอบวิชาชีพวิทยาศาสตร์และเทคโนโลยีควบคุม', 'ออกโดย สภาวิชาชีพวิทยาศาสตร์และเทคโนโลยี', 1, $SESS_USERID, '$UPDATE_DATE', 13) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
		
			}
			
			$cmd = " SELECT LH_ID FROM PER_LICENSEHIS ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) {
				if($DPISDB=="odbc") 
					$cmd = " CREATE TABLE PER_LICENSEHIS(
					LH_ID INTEGER NOT NULL,	
					PER_ID INTEGER NOT NULL,	
					PER_CARDNO VARCHAR(13) NULL,
					LT_CODE VARCHAR(10) NULL,	
					LH_SUB_TYPE VARCHAR(255) NULL,
					LH_LICENSE_NO VARCHAR(100) NULL,
					LH_LICENSE_DATE VARCHAR(19) NULL,
					LH_EXPIRE_DATE VARCHAR(19) NULL,
					LH_SEQ_NO INTEGER2 NULL,
					LH_REMARK MEMO NULL,
					UPDATE_USER INTEGER NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_LICENSEHIS PRIMARY KEY (LH_ID)) ";
				elseif($DPISDB=="oci8") 
					$cmd = " CREATE TABLE PER_LICENSEHIS(
					LH_ID NUMBER(10) NOT NULL,	
					PER_ID NUMBER(10) NOT NULL,	
					PER_CARDNO VARCHAR2(13) NULL,
					LT_CODE VARCHAR2(10) NULL,	
					LH_SUB_TYPE VARCHAR2(255) NULL,
					LH_LICENSE_NO VARCHAR2(100) NULL,
					LH_LICENSE_DATE VARCHAR2(19) NULL,
					LH_EXPIRE_DATE VARCHAR2(19) NULL,
					LH_SEQ_NO NUMBER(2) NULL,
					LH_REMARK VARCHAR2(1000) NULL,
					UPDATE_USER NUMBER(11) NOT NULL,
					UPDATE_DATE VARCHAR2(19) NOT NULL,		
					CONSTRAINT PK_PER_LICENSEHIS PRIMARY KEY (LH_ID)) ";
				elseif($DPISDB=="mysql")
					$cmd = " CREATE TABLE PER_LICENSEHIS(
					LH_ID INTEGER(10) NOT NULL,	
					PER_ID INTEGER(10) NOT NULL,	
					PER_CARDNO VARCHAR(13) NULL,
					LT_CODE VARCHAR(10) NULL,	
					LH_SUB_TYPE VARCHAR(255) NULL,
					LH_LICENSE_NO VARCHAR(100) NULL,
					LH_LICENSE_DATE VARCHAR(19) NULL,
					LH_EXPIRE_DATE VARCHAR(19) NULL,
					LH_SEQ_NO SMALLINT(2) NULL,
					LH_REMARK TEXT NULL,
					UPDATE_USER INTEGER(11) NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_LICENSEHIS PRIMARY KEY (LH_ID)) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			}

			$cmd = " SELECT AR_ID FROM PER_APPROVE_RESOLUTION ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) {
				if($DPISDB=="odbc") 
					$cmd = " CREATE TABLE PER_APPROVE_RESOLUTION(
					AR_ID INTEGER NOT NULL,	
					PER_ID INTEGER NOT NULL,	
					PER_CARDNO VARCHAR(13) NULL,
					AR_DESC MEMO NULL,
					AR_REMARK MEMO NULL,
					UPDATE_USER INTEGER NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_APPROVE_RESOLUTION PRIMARY KEY (AR_ID)) ";
				elseif($DPISDB=="oci8") 
					$cmd = " CREATE TABLE PER_APPROVE_RESOLUTION(
					AR_ID NUMBER(10) NOT NULL,	
					PER_ID NUMBER(10) NOT NULL,	
					PER_CARDNO VARCHAR2(13) NULL,
					AR_DESC VARCHAR2(1000) NULL,
					AR_REMARK VARCHAR2(2000) NULL,
					UPDATE_USER NUMBER(11) NOT NULL,
					UPDATE_DATE VARCHAR2(19) NOT NULL,		
					CONSTRAINT PK_PER_APPROVE_RESOLUTION PRIMARY KEY (AR_ID)) ";
				elseif($DPISDB=="mysql")
					$cmd = " CREATE TABLE PER_APPROVE_RESOLUTION(
					AR_ID INTEGER(10) NOT NULL,	
					PER_ID INTEGER(10) NOT NULL,	
					PER_CARDNO VARCHAR(13) NULL,
					AR_DESC TEXT NULL,
					AR_REMARK TEXT NULL,
					UPDATE_USER INTEGER(11) NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_APPROVE_RESOLUTION PRIMARY KEY (AR_ID)) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			}

			$cmd = " SELECT EP_ID FROM PER_EXCELLENT_PERFORMANCE ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) {
				if($DPISDB=="odbc") 
					$cmd = " CREATE TABLE PER_EXCELLENT_PERFORMANCE(
					EP_ID INTEGER NOT NULL,	
					PER_ID INTEGER NOT NULL,	
					PER_CARDNO VARCHAR(13) NULL,
					EP_DESC MEMO NULL,
					EP_YEAR VARCHAR(4) NULL,
					EP_REMARK MEMO NULL,
					UPDATE_USER INTEGER NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_EXCELLENT_PERFORMANCE PRIMARY KEY (EP_ID)) ";
				elseif($DPISDB=="oci8") 
					$cmd = " CREATE TABLE PER_EXCELLENT_PERFORMANCE(
					EP_ID NUMBER(10) NOT NULL,	
					PER_ID NUMBER(10) NOT NULL,	
					PER_CARDNO VARCHAR2(13) NULL,
					EP_DESC VARCHAR2(1000) NULL,
					EP_YEAR VARCHAR2(4) NULL,
					EP_REMARK VARCHAR2(2000) NULL,
					UPDATE_USER NUMBER(11) NOT NULL,
					UPDATE_DATE VARCHAR2(19) NOT NULL,		
					CONSTRAINT PK_PER_EXCELLENT_PERFORMANCE PRIMARY KEY (EP_ID)) ";
				elseif($DPISDB=="mysql")
					$cmd = " CREATE TABLE PER_EXCELLENT_PERFORMANCE(
					EP_ID INTEGER(10) NOT NULL,	
					PER_ID INTEGER(10) NOT NULL,	
					PER_CARDNO VARCHAR(13) NULL,
					EP_DESC TEXT NULL,
					EP_YEAR VARCHAR(4) NULL,
					EP_REMARK TEXT NULL,
					UPDATE_USER INTEGER(11) NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_EXCELLENT_PERFORMANCE PRIMARY KEY (EP_ID)) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			}

			$cmd = " SELECT SH_ID FROM PER_SOLDIERHIS ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) {
				if($DPISDB=="odbc") 
					$cmd = " CREATE TABLE PER_SOLDIERHIS(
					SH_ID INTEGER NOT NULL,	
					PER_ID INTEGER NOT NULL,	
					PER_CARDNO VARCHAR(13) NULL,
					SH_DESC MEMO NULL,
					SH_YEAR VARCHAR(4) NULL,
					SH_REMARK MEMO NULL,
					UPDATE_USER INTEGER NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_SOLDIERHIS PRIMARY KEY (SH_ID)) ";
				elseif($DPISDB=="oci8") 
					$cmd = " CREATE TABLE PER_SOLDIERHIS(
					SH_ID NUMBER(10) NOT NULL,	
					PER_ID NUMBER(10) NOT NULL,	
					PER_CARDNO VARCHAR2(13) NULL,
					SH_DESC VARCHAR2(1000) NULL,
					SH_YEAR VARCHAR2(4) NULL,
					SH_REMARK VARCHAR2(2000) NULL,
					UPDATE_USER NUMBER(11) NOT NULL,
					UPDATE_DATE VARCHAR2(19) NOT NULL,		
					CONSTRAINT PK_PER_SOLDIERHIS PRIMARY KEY (SH_ID)) ";
				elseif($DPISDB=="mysql")
					$cmd = " CREATE TABLE PER_SOLDIERHIS(
					SH_ID INTEGER(10) NOT NULL,	
					PER_ID INTEGER(10) NOT NULL,	
					PER_CARDNO VARCHAR(13) NULL,
					SH_DESC TEXT NULL,
					SH_YEAR VARCHAR(4) NULL,
					SH_REMARK TEXT NULL,
					UPDATE_USER INTEGER(11) NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_SOLDIERHIS PRIMARY KEY (SH_ID)) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			}

			$cmd = " SELECT OO_ID FROM PER_OTHER_OCCUPATION ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) {
				if($DPISDB=="odbc") 
					$cmd = " CREATE TABLE PER_OTHER_OCCUPATION(
					OO_ID INTEGER NOT NULL,	
					PER_ID INTEGER NOT NULL,	
					PER_CARDNO VARCHAR(13) NULL,
					OC_CODE VARCHAR(10) NULL,
					OO_DESC MEMO NULL,
					OO_YEAR VARCHAR(4) NULL,
					OO_REMARK MEMO NULL,
					UPDATE_USER INTEGER NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_OTHER_OCCUPATION PRIMARY KEY (OO_ID)) ";
				elseif($DPISDB=="oci8") 
					$cmd = " CREATE TABLE PER_OTHER_OCCUPATION(
					OO_ID NUMBER(10) NOT NULL,	
					PER_ID NUMBER(10) NOT NULL,	
					PER_CARDNO VARCHAR2(13) NULL,
					OC_CODE VARCHAR2(10) NULL,
					OO_DESC VARCHAR2(1000) NULL,
					OO_YEAR VARCHAR2(4) NULL,
					OO_REMARK VARCHAR2(2000) NULL,
					UPDATE_USER NUMBER(11) NOT NULL,
					UPDATE_DATE VARCHAR2(19) NOT NULL,		
					CONSTRAINT PK_PER_OTHER_OCCUPATION PRIMARY KEY (OO_ID)) ";
				elseif($DPISDB=="mysql")
					$cmd = " CREATE TABLE PER_OTHER_OCCUPATION(
					OO_ID INTEGER(10) NOT NULL,	
					PER_ID INTEGER(10) NOT NULL,	
					PER_CARDNO VARCHAR(13) NULL,
					OC_CODE VARCHAR(10) NULL,
					OO_DESC TEXT NULL,
					OO_YEAR VARCHAR(4) NULL,
					OO_REMARK TEXT NULL,
					UPDATE_USER INTEGER(11) NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_OTHER_OCCUPATION PRIMARY KEY (OO_ID)) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			}

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_SERVICETITLE ALTER SRT_NAME MEMO ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SERVICETITLE MODIFY SRT_NAME VARCHAR2(2000) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_SERVICETITLE MODIFY SRT_NAME TEXT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " SELECT TC_CODE FROM PER_TEST_COURSE ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) {
				if($DPISDB=="odbc") 
					$cmd = " CREATE TABLE PER_TEST_COURSE (
					TC_CODE VARCHAR(10) NOT NULL,
					TC_NAME VARCHAR(100) NOT NULL,
					TC_ACTIVE SINGLE NOT NULL,
					UPDATE_USER INTEGER NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					TC_SEQ_NO INTEGER2 NULL,
					TC_OTHERNAME MEMO NULL,
					CONSTRAINT PK_PER_TEST_COURSE PRIMARY KEY (TC_CODE)) ";
				elseif($DPISDB=="oci8") 
					$cmd = " CREATE TABLE PER_TEST_COURSE (
					TC_CODE VARCHAR2(10) NOT NULL,
					TC_NAME VARCHAR2(100) NOT NULL,
					TC_ACTIVE NUMBER(1) NOT NULL,
					UPDATE_USER NUMBER(11) NOT NULL,
					UPDATE_DATE VARCHAR2(19) NOT NULL,		
					TC_SEQ_NO NUMBER(5) NULL,
					TC_OTHERNAME VARCHAR2(1000) NULL,
					CONSTRAINT PK_PER_TEST_COURSE PRIMARY KEY (TC_CODE)) ";
				elseif($DPISDB=="mysql")
					$cmd = " CREATE TABLE PER_TEST_COURSE (
					TC_CODE VARCHAR(10) NOT NULL,
					TC_NAME VARCHAR(100) NOT NULL,
					TC_ACTIVE SMALLINT(1) NOT NULL,
					UPDATE_USER INTEGER(11) NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					TC_SEQ_NO SMALLINT(5) NULL,
					TC_OTHERNAME TEXT NULL,
					CONSTRAINT PK_PER_TEST_COURSE PRIMARY KEY (TC_CODE)) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				if($DPISDB=="odbc") 
					$cmd = " CREATE TABLE PER_TEST_COURSEHIS(
					TCH_ID INTEGER NOT NULL,	
					PER_ID INTEGER NOT NULL,	
					PER_CARDNO VARCHAR(13) NULL,
					TC_CODE VARCHAR(10) NULL,	
					TCH_COURSE_NAME MEMO NULL,
					TCH_ORG MEMO NULL,
					TCH_PLACE MEMO NULL,
					TCH_TESTDATE VARCHAR(19) NULL,
					TCH_SEQ_NO INTEGER2 NULL,
					TCH_SCORE NUMBER NULL,
					TCH_LEVEL SINGLE NULL,
					TCH_POSTDATE VARCHAR(19) NULL,
					TCH_ENDDATE VARCHAR(19) NULL,
					TCH_REMARK MEMO NULL,
					UPDATE_USER INTEGER NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_TEST_COURSEHIS PRIMARY KEY (TCH_ID)) ";
				elseif($DPISDB=="oci8") 
					$cmd = " CREATE TABLE PER_TEST_COURSEHIS(
					TCH_ID NUMBER(10) NOT NULL,	
					PER_ID NUMBER(10) NOT NULL,	
					PER_CARDNO VARCHAR2(13) NULL,
					TC_CODE VARCHAR2(10) NULL,	
					TCH_COURSE_NAME VARCHAR2(1000) NULL,
					TCH_ORG VARCHAR2(1000) NULL,
					TCH_PLACE VARCHAR2(1000) NULL,
					TCH_TESTDATE VARCHAR2(19) NULL,
					TCH_SEQ_NO NUMBER(5) NULL,
					TCH_SCORE NUMBER(16,2) NULL,
					TCH_LEVEL NUMBER(1) NULL,
					TCH_POSTDATE VARCHAR2(19) NULL,
					TCH_ENDDATE VARCHAR2(19) NULL,
					TCH_REMARK VARCHAR2(1000) NULL,
					UPDATE_USER NUMBER(11) NOT NULL,
					UPDATE_DATE VARCHAR2(19) NOT NULL,		
					CONSTRAINT PK_PER_TEST_COURSEHIS PRIMARY KEY (TCH_ID)) ";
				elseif($DPISDB=="mysql")
					$cmd = " CREATE TABLE PER_TEST_COURSEHIS(
					TCH_ID INTEGER(10) NOT NULL,	
					PER_ID INTEGER(10) NOT NULL,	
					PER_CARDNO VARCHAR(13) NULL,
					TC_CODE VARCHAR(10) NULL,	
					TCH_COURSE_NAME TEXT NULL,
					TCH_ORG TEXT NULL,
					TCH_PLACE TEXT NULL,
					TCH_TESTDATE VARCHAR(19) NULL,
					TCH_SEQ_NO SMALLINT(5) NULL,
					TCH_SCORE DECIMAL(16,2) NOT NULL,
					TCH_LEVEL SMALLINT(1) NULL,
					TCH_POSTDATE VARCHAR(19) NULL,
					TCH_ENDDATE VARCHAR(19) NULL,
					TCH_REMARK TEXT NULL,
					UPDATE_USER INTEGER(11) NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_TEST_COURSEHIS PRIMARY KEY (TCH_ID)) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			}

			$cmd = " SELECT MG_CODE FROM PER_MINISTRY_GROUP ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) {
				if($DPISDB=="odbc") 
					$cmd = " CREATE TABLE PER_MINISTRY_GROUP (
					MG_CODE VARCHAR(10) NOT NULL,
					MG_NAME VARCHAR(100) NOT NULL,
					MG_ACTIVE SINGLE NOT NULL,
					UPDATE_USER INTEGER NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					MG_SEQ_NO INTEGER2 NULL,
					MG_OTHERNAME MEMO NULL,
					CONSTRAINT PK_PER_MINISTRY_GROUP PRIMARY KEY (MG_CODE)) ";
				elseif($DPISDB=="oci8") 
					$cmd = " CREATE TABLE PER_MINISTRY_GROUP (
					MG_CODE VARCHAR2(10) NOT NULL,
					MG_NAME VARCHAR2(100) NOT NULL,
					MG_ACTIVE NUMBER(1) NOT NULL,
					UPDATE_USER NUMBER(11) NOT NULL,
					UPDATE_DATE VARCHAR2(19) NOT NULL,		
					MG_SEQ_NO NUMBER(5) NULL,
					MG_OTHERNAME VARCHAR2(1000) NULL,
					CONSTRAINT PK_PER_MINISTRY_GROUP PRIMARY KEY (MG_CODE)) ";
				elseif($DPISDB=="mysql")
					$cmd = " CREATE TABLE PER_MINISTRY_GROUP (
					MG_CODE VARCHAR(10) NOT NULL,
					MG_NAME VARCHAR(100) NOT NULL,
					MG_ACTIVE SMALLINT(1) NOT NULL,
					UPDATE_USER INTEGER(11) NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					MG_SEQ_NO SMALLINT(5) NULL,
					MG_OTHERNAME TEXT NULL,
					CONSTRAINT PK_PER_MINISTRY_GROUP PRIMARY KEY (MG_CODE)) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_MINISTRY_GROUP (MG_CODE, MG_NAME, MG_ACTIVE, UPDATE_USER, UPDATE_DATE, MG_SEQ_NO)
								  VALUES ('01', 'กลุ่มกระทรวงด้านเศรษฐกิจ', 1, $SESS_USERID, '$UPDATE_DATE', 1) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
		
				$cmd = " INSERT INTO PER_MINISTRY_GROUP (MG_CODE, MG_NAME, MG_ACTIVE, UPDATE_USER, UPDATE_DATE, MG_SEQ_NO)
								  VALUES ('02', 'กลุ่มกระทรวงด้านสังคม', 1, $SESS_USERID, '$UPDATE_DATE', 2) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
		
				$cmd = " INSERT INTO PER_MINISTRY_GROUP (MG_CODE, MG_NAME, MG_ACTIVE, UPDATE_USER, UPDATE_DATE, MG_SEQ_NO)
								  VALUES ('03', 'กลุ่มกระทรวงด้านความมั่นคง และการต่างประเทศ', 1, $SESS_USERID, '$UPDATE_DATE', 3) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
		
				$cmd = " INSERT INTO PER_MINISTRY_GROUP (MG_CODE, MG_NAME, MG_ACTIVE, UPDATE_USER, UPDATE_DATE, MG_SEQ_NO)
								  VALUES ('04', 'กลุ่มกระทรวงด้านบริหารและส่วนราชการไม่สังกัดสำนักนายกรัฐมนตรี กระทรวงหรือทบวง', 1, $SESS_USERID, '$UPDATE_DATE', 4) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
		
			}

			$cmd = " SELECT PG_CODE FROM PER_PROVINCE_GROUP ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) {
				if($DPISDB=="odbc") 
					$cmd = " CREATE TABLE PER_PROVINCE_GROUP (
					PG_CODE VARCHAR(10) NOT NULL,
					PG_NAME VARCHAR(100) NOT NULL,
					PG_ACTIVE SINGLE NOT NULL,
					UPDATE_USER INTEGER NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					PG_SEQ_NO INTEGER2 NULL,
					PG_OTHERNAME MEMO NULL,
					CONSTRAINT PK_PER_PROVINCE_GROUP PRIMARY KEY (PG_CODE)) ";
				elseif($DPISDB=="oci8") 
					$cmd = " CREATE TABLE PER_PROVINCE_GROUP (
					PG_CODE VARCHAR2(10) NOT NULL,
					PG_NAME VARCHAR2(100) NOT NULL,
					PG_ACTIVE NUMBER(1) NOT NULL,
					UPDATE_USER NUMBER(11) NOT NULL,
					UPDATE_DATE VARCHAR2(19) NOT NULL,		
					PG_SEQ_NO NUMBER(5) NULL,
					PG_OTHERNAME VARCHAR2(1000) NULL,
					CONSTRAINT PK_PER_PROVINCE_GROUP PRIMARY KEY (PG_CODE)) ";
				elseif($DPISDB=="mysql")
					$cmd = " CREATE TABLE PER_PROVINCE_GROUP (
					PG_CODE VARCHAR(10) NOT NULL,
					PG_NAME VARCHAR(100) NOT NULL,
					PG_ACTIVE SMALLINT(1) NOT NULL,
					UPDATE_USER INTEGER(11) NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					PG_SEQ_NO SMALLINT(5) NULL,
					PG_OTHERNAME TEXT NULL,
					CONSTRAINT PK_PER_PROVINCE_GROUP PRIMARY KEY (PG_CODE)) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_PROVINCE_GROUP (PG_CODE, PG_NAME, PG_ACTIVE, UPDATE_USER, UPDATE_DATE, PG_SEQ_NO)
								  VALUES ('01', 'กลุ่มจังหวัดภาคกลาง', 1, $SESS_USERID, '$UPDATE_DATE', 1) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
		
				$cmd = " INSERT INTO PER_PROVINCE_GROUP (PG_CODE, PG_NAME, PG_ACTIVE, UPDATE_USER, UPDATE_DATE, PG_SEQ_NO)
								  VALUES ('02', 'กลุ่มจังหวัดภาคเหนือ', 1, $SESS_USERID, '$UPDATE_DATE', 2) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
		
				$cmd = " INSERT INTO PER_PROVINCE_GROUP (PG_CODE, PG_NAME, PG_ACTIVE, UPDATE_USER, UPDATE_DATE, PG_SEQ_NO)
								  VALUES ('03', 'กลุ่มจังหวัดภาคเหนือตอน', 1, $SESS_USERID, '$UPDATE_DATE', 3) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
		
				$cmd = " INSERT INTO PER_PROVINCE_GROUP (PG_CODE, PG_NAME, PG_ACTIVE, UPDATE_USER, UPDATE_DATE, PG_SEQ_NO)
								  VALUES ('04', 'กลุ่มจังหวัดภาคใต้', 1, $SESS_USERID, '$UPDATE_DATE', 4) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
		
				$cmd = " INSERT INTO PER_PROVINCE_GROUP (PG_CODE, PG_NAME, PG_ACTIVE, UPDATE_USER, UPDATE_DATE, PG_SEQ_NO)
								  VALUES ('05', 'กลุ่มจังหวัดภาคตะวันออกเฉียงเหนือ', 1, $SESS_USERID, '$UPDATE_DATE', 5) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
		
			}

			$cmd = " update per_personal set poem_id = null, poems_id = null, pot_id = null where per_type = 1 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " update per_personal set pos_id = null, poems_id = null, pot_id = null where per_type = 2 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " update per_personal set pos_id = null, poem_id = null, pot_id = null where per_type = 3 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " update per_personal set pos_id = null, poem_id = null, poems_id = null where per_type = 4 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " update PER_ASSESS_LEVEL set AL_CYCLE = 2 where PER_TYPE = 3 ";
			$db_dpis2->send_cmd($cmd);
			//$db_dpis2->show_error();

			$cmd = " CREATE INDEX IDX_PER_PERFORMANCE_GOALS_KF ON PER_PERFORMANCE_GOALS (KF_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_PERFORMANCE_GOALS_KPI ON PER_PERFORMANCE_GOALS (KPI_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_KPI_PFR ON PER_KPI (PFR_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
							  VALUES (0, 'E2', 0, 0, 1, $SESS_USERID, '$UPDATE_DATE', 7370, 23970) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
							  VALUES (0, 'E2', 0, 0, 1, $SESS_USERID, '$UPDATE_DATE', 7370, 23970) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DELETE FROM PER_LAYER WHERE LAYER_TYPE = 0 AND LEVEL_NO = 'E2' AND LAYER_NO > 0 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DELETE FROM PER_LAYER_NEW WHERE LAYER_TYPE = 0 AND LEVEL_NO = 'E2' AND LAYER_NO > 0 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DELETE FROM PER_LAYER WHERE LEVEL_NO = 'E6' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DELETE FROM PER_LAYER_NEW WHERE LEVEL_NO = 'E6' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_EMPSER_POS_NAME SET LEVEL_NO = 'E2' WHERE LEVEL_NO = 'E6' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_PERSONAL SET LEVEL_NO = 'E2' WHERE LEVEL_NO = 'E6' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " UPDATE PER_POSITIONHIS SET LEVEL_NO = 'E2' WHERE LEVEL_NO = 'E6' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " UPDATE PER_POSITIONHIS SET POH_LEVEL_NO = 'E2' WHERE POH_LEVEL_NO = 'E6' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " UPDATE PER_SALARYHIS SET LEVEL_NO = 'E2' WHERE LEVEL_NO = 'E6' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " UPDATE PER_COMDTL SET LEVEL_NO = 'E2' WHERE LEVEL_NO = 'E6' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " UPDATE PER_COMPENSATION_TEST_DTL SET LEVEL_NO = 'E2' WHERE LEVEL_NO = 'E6' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " DELETE FROM PER_LEVEL WHERE LEVEL_NO = 'E6' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LEVEL SET LEVEL_NAME = 'กลุ่มงานเทคนิคทั่วไป', LEVEL_SHORTNAME = 'เทคนิคทั่วไป', LEVEL_SEQ_NO = 62 WHERE LEVEL_NO = 'E2' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_COMPENSATION_TEST SET CP_CYCLE = 2 WHERE PER_TYPE = 3 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " UPDATE PER_PERSONAL SET OT_CODE = '08' WHERE PER_TYPE =  3 AND OT_CODE = '01' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " update PER_ASSESS_MAIN set AM_CYCLE = 2 where PER_TYPE = 3 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " update PER_ASSESS_LEVEL set AL_CYCLE = 2 where PER_TYPE = 3 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_DECORATION", "DC_FLAG","SINGLE", "1", "NULL");

			$cmd = " UPDATE PER_DECORATION SET DC_FLAG = 1 WHERE DC_CODE IN ('08','09','10','11','15','16','23','24','28','29','33','34','54','55','57','58') AND DC_FLAG IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_DECORATION SET DC_FLAG = 0 WHERE DC_FLAG IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE SYSTEM_CONFIG SET CONFIG_VALUE = '0' WHERE CONFIG_ID = 46 AND CONFIG_VALUE IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_TRAINING", "TRN_DOCNO","VARCHAR", "255", "NULL");
			add_field("PER_TRAINING", "TRN_DOCDATE","VARCHAR", "19", "NULL");
			add_field("PER_WORKFLOW_TRAINING", "TRN_DOCNO","VARCHAR", "255", "NULL");
			add_field("PER_WORKFLOW_TRAINING", "TRN_DOCDATE","VARCHAR", "19", "NULL");

			$cmd = " UPDATE PER_POSITIONHIS SET POH_LEVEL_NO = LEVEL_NO WHERE POH_LEVEL_NO IS NULL AND LEVEL_NO IS NOT NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMGROUP (COM_GROUP, CG_NAME, CG_ACTIVE, UPDATE_USER, UPDATE_DATE, CG_SEQ_NO)
							  VALUES ('525', 'คำสั่งจ่ายเงินรางวัลประจำปี', 1, $SESS_USERID, '$UPDATE_DATE', 25) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO, COM_ACTIVE) 
							  VALUES ('5250', 'คส. 25', 'คำสั่งจ่ายเงินรางวัลประจำปี', '525', 102, 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYEREMP SET LAYERE_SALARY = 9640, LAYERE_DAY = 419.15, LAYERE_HOUR = 59.90 
							  WHERE PG_CODE = '1000' AND LAYERE_NO = 16.5 AND LAYERE_SALARY = 9710 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYEREMP_NEW SET LAYERE_SALARY = 9640, LAYERE_DAY = 419.15, LAYERE_HOUR = 59.90 
							  WHERE PG_CODE = '1000' AND LAYERE_NO = 16.5 AND LAYERE_SALARY = 9710 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYEREMP SET LAYERE_SALARY = 9300, LAYERE_DAY = 404.35, LAYERE_HOUR = 57.80 
							  WHERE PG_CODE = '1000' AND LAYERE_NO = 15.5 AND LAYERE_SALARY = 9330 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYEREMP_NEW SET LAYERE_SALARY = 9300, LAYERE_DAY = 404.35, LAYERE_HOUR = 57.80 
							  WHERE PG_CODE = '1000' AND LAYERE_NO = 15.5 AND LAYERE_SALARY = 9330 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYEREMP SET LAYERE_SALARY = 8640, LAYERE_DAY = 374.65, LAYERE_HOUR = 53.70 
							  WHERE PG_CODE = '1000' AND LAYERE_NO = 13.5 AND LAYERE_SALARY = 8610 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYEREMP_NEW SET LAYERE_SALARY = 8640, LAYERE_DAY = 374.65, LAYERE_HOUR = 53.70 
							  WHERE PG_CODE = '1000' AND LAYERE_NO = 13.5 AND LAYERE_SALARY = 8610 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYEREMP SET LAYERE_SALARY = 6910, LAYERE_DAY = 300.45, LAYERE_HOUR = 42.95 
							  WHERE PG_CODE = '1000' AND LAYERE_NO = 8.5 AND LAYERE_SALARY = 6970 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYEREMP_NEW SET LAYERE_SALARY = 6910, LAYERE_DAY = 300.45, LAYERE_HOUR = 42.95 
							  WHERE PG_CODE = '1000' AND LAYERE_NO = 8.5 AND LAYERE_SALARY = 6970 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYEREMP SET LAYERE_SALARY = 11680, LAYERE_DAY = 507.85, LAYERE_HOUR = 72.55 
							  WHERE PG_CODE = '2000' AND LAYERE_NO = 9.5 AND LAYERE_SALARY = 11620 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYEREMP_NEW SET LAYERE_SALARY = 11680, LAYERE_DAY = 507.85, LAYERE_HOUR = 72.55 
							  WHERE PG_CODE = '2000' AND LAYERE_NO = 9.5 AND LAYERE_SALARY = 11620 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYEREMP SET LAYERE_SALARY = 19000, LAYERE_DAY = 826.10, LAYERE_HOUR = 118.05 
							  WHERE PG_CODE = '3000' AND LAYERE_NO = 15.5 AND LAYERE_SALARY = 18810 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYEREMP_NEW SET LAYERE_SALARY = 19000, LAYERE_DAY = 826.10, LAYERE_HOUR = 118.05 
							  WHERE PG_CODE = '3000' AND LAYERE_NO = 15.5 AND LAYERE_SALARY = 18810 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();			
			
			$cmd = " UPDATE PER_LAYEREMP SET LAYERE_SALARY = 15300, LAYERE_DAY = 665.25, LAYERE_HOUR = 95.05 
							  WHERE PG_CODE = '3000' AND LAYERE_NO = 11 AND LAYERE_SALARY = 15430 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYEREMP_NEW SET LAYERE_SALARY = 15300, LAYERE_DAY = 665.25, LAYERE_HOUR = 95.05 
							  WHERE PG_CODE = '3000' AND LAYERE_NO = 11 AND LAYERE_SALARY = 15430 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if ($BKK_FLAG==1) {
/*				$cmd = " UPDATE PER_LEVEL SET LEVEL_ACTIVE = 0 WHERE LEVEL_NO not in ('O1', 'O2', 'O3', 'O4', 'K1', 'K2', 'K3', 'K4', 'K5', 'D1', 'D2', 'M1', 'M2') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, 
								  UPDATE_DATE, MOV_SUB_TYPE)
								  VALUES ('81', 'เลื่อนเงินเดือนระดับพอใช้', 2, 1, $SESS_USERID, '$UPDATE_DATE', 4) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, 
								  UPDATE_DATE, MOV_SUB_TYPE)
								  VALUES ('82', 'เลื่อนเงินเดือนระดับดี', 2, 1, $SESS_USERID, '$UPDATE_DATE', 4) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, 
								  UPDATE_DATE, MOV_SUB_TYPE)
								  VALUES ('83', 'เลื่อนเงินเดือนระดับดีมาก', 2, 1, $SESS_USERID, '$UPDATE_DATE', 4) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, 
								  UPDATE_DATE, MOV_SUB_TYPE)
								  VALUES ('84', 'เลื่อนเงินเดือนระดับดีเด่น', 2, 1, $SESS_USERID, '$UPDATE_DATE', 4) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, 
								  UPDATE_DATE, MOV_SUB_TYPE)
								  VALUES ('85', 'เงินเดือนเต็มขั้น ได้เงินค่าตอบแทนพิเศษ', 2, 1, $SESS_USERID, '$UPDATE_DATE', 4) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, 
								  UPDATE_DATE, MOV_SUB_TYPE)
								  VALUES ('86', 'เงินรางวัลประจำปี', 2, 1, $SESS_USERID, '$UPDATE_DATE', 4) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " DELETE FROM PER_LEVEL WHERE LEVEL_NO in ('E1', 'E2', 'E3', 'E4', 'E5', 'E7', 'S1', 'S2', 'S3', 'O4') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET MENU_LABEL = 'R0102 จำนวนตำแหน่งจำแนกตามประเภทตำแหน่งตาม พรบ.2528' 
								  WHERE LINKTO_WEB = 'rpt_R001.html?report=rpt_R001002' ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET MENU_LABEL = 'P1111 นำเข้าข้อมูลเครื่องราชฯ จากสำนักเลขาธิการคณะรัฐมนตรี' 
								  WHERE LINKTO_WEB = 'select_soc_excel.html' ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET MENU_LABEL = REPLACE(MENU_LABEL, 'ข้าราชการ/ลูกจ้างประจำ', 'ข้าราชการ') 
								  WHERE MENU_LABEL LIKE '%ข้าราชการ/ลูกจ้างประจำ%' ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET MENU_LABEL = REPLACE(MENU_LABEL, 'ข้าราชการ/ลูกจ้าง', 'ข้าราชการ') 
								  WHERE MENU_LABEL LIKE '%ข้าราชการ/ลูกจ้าง%' ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " SELECT MJT_CODE FROM PER_MAIN_JOB_TYPE ";
				$count_data = $db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
				if (!$count_data) {
					if($DPISDB=="odbc") 
						$cmd = " CREATE TABLE PER_MAIN_JOB_TYPE(
						MJT_CODE VARCHAR(10) NOT NULL,	
						MJT_NAME MEMO NOT NULL,
						MJT_ACTIVE SINGLE NOT NULL,
						UPDATE_USER INTEGER NOT NULL,
						UPDATE_DATE VARCHAR(19) NOT NULL,		
						CONSTRAINT PK_PER_MAIN_JOB_TYPE PRIMARY KEY (MJT_CODE)) ";
					elseif($DPISDB=="oci8") 
						$cmd = " CREATE TABLE PER_MAIN_JOB_TYPE(
						MJT_CODE VARCHAR2(10) NOT NULL,	
						MJT_NAME VARCHAR2(1000) NOT NULL,
						MJT_ACTIVE NUMBER(1) NOT NULL,
						UPDATE_USER NUMBER(11) NOT NULL,
						UPDATE_DATE VARCHAR2(19) NOT NULL,		
						CONSTRAINT PK_PER_MAIN_JOB_TYPE PRIMARY KEY (MJT_CODE)) ";
					elseif($DPISDB=="mysql")
						$cmd = " CREATE TABLE PER_MAIN_JOB_TYPE(
						MJT_CODE VARCHAR(10) NOT NULL,	
						MJT_NAME TEXT NOT NULL,
						MJT_ACTIVE SMALLINT(1) NOT NULL,
						UPDATE_USER INTEGER(11) NOT NULL,
						UPDATE_DATE VARCHAR(19) NOT NULL,		
						CONSTRAINT PK_PER_MAIN_JOB_TYPE PRIMARY KEY (MJT_CODE)) ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();

					$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'master_table_main_job_type.html?table=PER_MAIN_JOB_TYPE' ";
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
										  VALUES (1, 'TH', $MAX_ID, 9, 'K09 ประเภทหน้าที่ความรับผิดชอบหลัก', 'S', 'W', 'master_table_main_job_type.html?table=PER_MAIN_JOB_TYPE', 0, 40, 
										  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
						$db->send_cmd($cmd);
						//$db->show_error();

						$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
										  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
										  UPDATE_DATE, UPDATE_BY)
										  VALUES (1, 'EN', $MAX_ID, 9, 'K09 ประเภทหน้าที่ความรับผิดชอบหลัก', 'S', 'W', 'master_table_main_job_type.html?table=PER_MAIN_JOB_TYPE', 0, 40, 
										  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
						$db->send_cmd($cmd);
						//$db->show_error();
					} 
				}

				$cmd = " SELECT PL_CODE FROM PER_MAIN_JOB ";
				$count_data = $db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
				if (!$count_data) {
					if($DPISDB=="odbc") 
						$cmd = " CREATE TABLE PER_MAIN_JOB(
						PL_CODE VARCHAR(10) NOT NULL,	
						MJT_CODE VARCHAR(10) NOT NULL,	
						CONSTRAINT PK_PER_MAIN_JOB PRIMARY KEY (PL_CODE, MJT_CODE)) ";
					elseif($DPISDB=="oci8") 
						$cmd = " CREATE TABLE PER_MAIN_JOB(
						PL_CODE VARCHAR2(10) NOT NULL,	
						MJT_CODE VARCHAR2(10) NOT NULL,	
						CONSTRAINT PK_PER_MAIN_JOB PRIMARY KEY (PL_CODE, MJT_CODE)) ";
					elseif($DPISDB=="mysql")
						$cmd = " CREATE TABLE PER_MAIN_JOB(
						PL_CODE VARCHAR(10) NOT NULL,	
						MJT_CODE VARCHAR(10) NOT NULL,	
						CONSTRAINT PK_PER_MAIN_JOB PRIMARY KEY (PL_CODE, MJT_CODE)) ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();

					$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'master_table_main_job.html?table=PER_MAIN_JOB' ";
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
										  VALUES (1, 'TH', $MAX_ID, 10, 'K10 หน้าที่ความรับผิดชอบหลัก', 'S', 'W', 'master_table_main_job.html?table=PER_MAIN_JOB', 0, 40, 
										  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
						$db->send_cmd($cmd);
						//$db->show_error();

						$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
										  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
										  UPDATE_DATE, UPDATE_BY)
										  VALUES (1, 'EN', $MAX_ID, 10, 'K10 หน้าที่ความรับผิดชอบหลัก', 'S', 'W', 'master_table_main_job.html?table=PER_MAIN_JOB', 0, 40, 
										  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
						$db->send_cmd($cmd);
						//$db->show_error();
					} 
				} */
			}

			add_field("PER_KPI_COMPETENCE", "KC_REMARK","MEMO", "2000", "NULL");

			$cmd = " SELECT LEVEL_NO_MIN FROM PER_LINE ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) {
				add_field("PER_LINE", "LEVEL_NO_MIN","VARCHAR", "10", "NULL");
				add_field("PER_LINE", "LEVEL_NO_MAX","VARCHAR", "10", "NULL");

				if ($BKK_FLAG==1) {
					$cmd = " UPDATE PER_LINE SET LG_CODE = '21002' WHERE PL_CODE in ('21001', '21003', '21012', '21022', '21032', '21042', '21052', '21062', '21072', '21082', '21092', '21102', 
									'21112', '21122', '21132', '21142', '21152', '21162', '21172', '21182', '21192', '21202', '21212', '21222') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();

					$cmd = " UPDATE PER_LINE SET LEVEL_NO_MIN = 'M1' WHERE PL_CODE in ('11001', '11002') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();

					$cmd = " UPDATE PER_LINE SET LEVEL_NO_MIN = 'D1' WHERE PL_CODE in ('21001', '21003', '21012', '21022', '21032', '21042', '21052', '21062', '21072', '21082', '21092', '21102', 
									'21112', '21122', '21132', '21142', '21152', '21162', '21172', '21182', '21192', '21202', '21212', '21222') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();

					$cmd = " UPDATE PER_LINE SET LEVEL_NO_MIN = 'K3', LEVEL_NO_MAX = 'K3' WHERE PL_CODE in ('31004') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();

					$cmd = " UPDATE PER_LINE SET LEVEL_NO_MIN = 'K1' WHERE PL_CODE in ('31001', '31002', '31003', '31005', '31006', '31007', '31008', '31009', '31010', '31011', '31012', 
									'31013', '32001', '32002', '32003', '32004', '32005', '32006', '33001', '33002', '34001', '35001', '36001', '36002', '36003', '36004', '36005', '36006', '36007', '36008', '36009', '36010', 
									'36011', '36012', '36013', '36014', '36015', '37001', '37002', '37003', '37004', '37005', '37006', '37007', '37008', '37009', '37010', '37011', '37012', '38001', '38002', '38003', '38004', 
									'38005', '38006', '38007', '38008', '38009', '38010', '38011', '38012', '38013', '38014') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();

					$cmd = " UPDATE PER_LINE SET LEVEL_NO_MIN = 'O1' WHERE PL_CODE in ('41001', '41002', '41003', '41004', '41005', '41006', '42001', '42002', '42003', '43001', '43002', '43003', 
									'44001', '45001', '46001', '46002', '46003', '46004', '46005', '46006', '46007', '46008', '46009', '46010', '46011', '47001', '47002', '47003', '47004', '47005', '47006', '47007', '47008', 
									'47009', '47010', '47011', '48001', '48002', '48003', '48004', '48005') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();

					$cmd = " UPDATE PER_LINE SET LEVEL_NO_MAX = 'M2' WHERE PL_CODE in ('11001', '11002') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();

					$cmd = " UPDATE PER_LINE SET LEVEL_NO_MAX = 'D2' WHERE PL_CODE in ('21001', '21003', '21012', '21022', '21032', '21042', '21052', '21062', '21072', '21082', '21092', '21102', 
									'21112', '21122', '21132', '21142', '21152', '21162', '21172', '21182', '21192', '21202', '21212', '21222') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();

					$cmd = " UPDATE PER_LINE SET LEVEL_NO_MAX = 'K3' WHERE PL_CODE in ('31001', '31002', '31005', '31006', '31007', '31009', '31010', '31011', '31012', '31013', '32001', '32003', 
									'32004', '32005', '32006', '33001', '33002', '34001', '35001', '36001', '36002', '36009', '36010', '36011', '36013', '36014', '36015', '37001', '37002', '37003', '37004', '37005', '37007', 
									'37008', '37010', '37011', '37012', '38001', '38002', '38003', '38004', '38005', '38006', '38007', '38008', '38009', '38010', '38011', '38012', '38013', '38014') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();

					$cmd = " UPDATE PER_LINE SET LEVEL_NO_MAX = 'K4' WHERE PL_CODE in ('31008', '32002', '36004', '36005', '36006', '36008', '36012', '37006', '37009') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();

					$cmd = " UPDATE PER_LINE SET LEVEL_NO_MAX = 'K5' WHERE PL_CODE in ('31003', '36003', '36007') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();

					$cmd = " UPDATE PER_LINE SET LEVEL_NO_MAX = 'O2' WHERE PL_CODE in ('41001', '41003', '41004', '41005', '41006', '42002', '43001', '43003', '45001', '46002', '46003', '46004', 
									'46005', '46006', '46007', '46008', '46010', '46011', '47001', '47002', '47003', '47005', '47007', '48003', '48004', '48005') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();

					$cmd = " UPDATE PER_LINE SET LEVEL_NO_MAX = 'O3' WHERE PL_CODE in ('41002', '42001', '42003', '43002', '44001', '46001', '46002', '46009', '47004', '47006', '47008', 
									'47009', '47010', '47011', '48001', '48002') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();

				} else {
					$cmd = " UPDATE PER_LINE SET LEVEL_NO_MIN = 'M1' WHERE PL_CODE in ('510108', '510109', '510209', '510307') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();

					$cmd = " UPDATE PER_LINE SET LEVEL_NO_MIN = 'D1' WHERE PL_CODE in ('510210', '510308', '511019', '511029', '512009', '512409', '512629', '520109', '520309', '520429', '520609', 
					'520829', '521009', '521709', '522009', '522209', '522729', '523509', '523909', '530309', '540109', '540209', '540319', '540329', '540509', '540709', '540919', '541009', '541119', '541449', '550109', 
					'550229', '550409', '550909', '551009', '551109', '551309', '551409', '551609', '551809', '560109', '560209', '560309', '560409', '560609', '560709', '561519', '562109', '570109', '570209', '570409', 
					'570509', '570609', '570709', '570909', '571009', '571319', '571419', '571519', '571529', '571629', '572709', '573519', '573809', '574009', '574429', '574519', '575109', '575309', '575409', '581109', 
					'581209', '581309', '581619', '581909', '582109', '582209', '582309', '582409', '584209', '584409') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();

					$cmd = " UPDATE PER_LINE SET LEVEL_NO_MIN = 'K1' WHERE PL_CODE in ('510403', '510404', '510703', '510903', '511013', '511014', '511104', '511203', '511503', '511723', '512003', 
					'512403', '512603', '512613', '512703', '512833', '512903', '513003', '513103', '513303', '513405', '513503', '520103', '520104', '520303', '520423', '520603', '520823', '520903', '521003', '521203', 
					'521423', '521523', '521603', '521703', '521903', '522003', '522103', '522203', '522423', '522513', '522723', '523203', '523503', '523623', '523903', '530103', '530403', '530503', '531813', '531923', 
					'532003', '532423', '532523', '540103', '540203', '540323', '540503', '540703', '541003', '541443', '541503', '550103', '550223', '550303', '550403', '550503', '550803', '550903', '551003', '551103', 
					'551303', '551403', '551603', '551803', '560104', '560105', '560204', '560304', '560403', '560603', '560703', '560813', '561203', '561204', '561514', '561523', '561723', '561803', '561913', '561914', 
					'561915', '562503', '563603', '570103', '570203', '570403', '570503', '570603', '570703', '570704', '570803', '570903', '571003', '571303', '571523', '571623', '572203', '572503', '572703', '573123', 
					'573803', '573813', '574003', '574213', '574423', '575003', '575103', '575303', '575403', '575612', '580103', '580403', '580513', '580723', '580823', '580913', '580923', '581103', '581203', '581303', 
					'581603', '581703', '581803', '581903', '582403', '582623', '582723', '582903', '583203', '583403', '583503', '583603', '584003', '584123', '584203', '584403', '584578', '9001', '9003', '561205') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();

					$cmd = " UPDATE PER_LINE SET LEVEL_NO_MIN = 'O1' WHERE PL_CODE in ('510501', '510502', '511211', '511612', '511712', '512212', '512302', '512812', '520212', '520412', '520502', 
					'520812', '521301', '521412', '521512', '522312', '522412', '522712', '523302', '523612', '530212', '530601', '531212', '531801', '531912', '532011', '532512', '532601', '540312', '540412', '540612', 
					'540912', '541112', '541412', '550212', '551512', '551712', '560802', '561502', '561712', '561902', '562102', '562212', '562312', '562712', '562802', '563502', '571412', '571512', '571612', '571812', 
					'571912', '572112', '572412', '572612', '572802', '572901', '573012', '573512', '573712', '574112', '574202', '574312', '574412', '574512', '574601', '574701', '574802', '574912', '575201', '575602', 
					'575702', '580502', '580712', '580812', '581001', '581501', '582001', '582101', '582201', '582301', '582612', '582712', '583312', '583712', '584101', '584301', '584401', '584555', '9002', '80010121') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();

					$cmd = " UPDATE PER_LINE SET LEVEL_NO_MIN = 'K2' WHERE PL_CODE in ('512626', '530306') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();

					$cmd = " UPDATE PER_LINE SET LEVEL_NO_MAX = 'M2' WHERE PL_CODE in ('510108', '510109', '510209', '510307') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();

					$cmd = " UPDATE PER_LINE SET LEVEL_NO_MAX = 'D2' WHERE PL_CODE in ('510210', '510308', '511019', '511029', '512009', '512409', '512629', '520109', '520309', '520429', '520609', 
					'520829', '521009', '521709', '522009', '522209', '522729', '523509', '523909', '530309', '540109', '540209', '540319', '540329', '540509', '540709', '540919', '541009', '541119', '541449', '550109', 
					'550229', '550409', '550909', '551009', '551109', '551309', '551409', '551609', '551809', '560109', '560209', '560309', '560409', '560609', '560709', '561519', '562109', '570109', '570209', '570409', 
					'570509', '570609', '570709', '570909', '571009', '571319', '571419', '571519', '571529', '571629', '572709', '573519', '573809', '574009', '574429', '574519', '575109', '575309', '575409', '581109', 
					'581209', '581309', '581619', '581909', '582109', '582209', '582309', '582409', '584209', '584409') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();

					$cmd = " UPDATE PER_LINE SET LEVEL_NO_MAX = 'K3' WHERE PL_CODE in ('510403', '510404', '511503', '511723', '512613', '512903', '513103', '521603', '521903', '522723', '530306', 
					'530403', '530503', '532003', '', '532523', '560105', '561913', '561915', '571523', '572503', '572703', '574213', '575003', '575612', '580403', '580723', '581203', '581303', '584203', '9001', '9003') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();

					$cmd = " UPDATE PER_LINE SET LEVEL_NO_MAX = 'K4' WHERE PL_CODE in ('511014', '511104', '511203', '512003', '512603', '512626', '512703', '512833', '513003', '513303', '520423', 
					'520603', '520823', '521703', '522203', '522513', '523203', '523503', '523623', '531813', '532423', '540203', '541003', '541443', '541503', '550223', '550303', '550403', '550503', '550803', '550903', 
					'551003', '551103', '551303', '551403', '551603', '551803', '560813', '561203', '561204', '561514', '561723', '561803', '561914', '563603', '570103', '570403', '570503', '570603', '570703', '570704', 
					'570803', '571003', '571303', '573123', '573813', '574003', '574423', '575103', '575303', '580513', '580823', '580913', '580923', '581703', '581803', '581903', '582623', '582723', '582903', '583403', 
					'583503', '583603', '584003', '584123', '584403', '584578') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();

					$cmd = " UPDATE PER_LINE SET LEVEL_NO_MAX = 'K5' WHERE PL_CODE in ('510703', '510903', '511013', '512403', '513405', '513503', '520103', '520104', '520303', '520903', '521003', 
					'521203', '521423', '521523', '522003', '522103', '522423', '523903', '530103', '531923', '540103', '540323', '540503', '540703', '550103', '560104', '560204', '560304', '560403', '560603', '560703', 
					'561523', '562503', '570203', '570903', '571623', '572203', '573803', '575403', '580103', '581103', '581603', '582403', '583203', '561205') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();

					$cmd = " UPDATE PER_LINE SET LEVEL_NO_MAX = 'O2' WHERE PL_CODE in ('510501', '510502', '511211', '530601', '532011', '561712', '561902', '562212', '562312', '562712', '562802', 
					'572112', '572802', '573712', '574601', '574701', '575201', '575602', '575702', '580502', '581501', '582001', '582612', '584301', '584555') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();

					$cmd = " UPDATE PER_LINE SET LEVEL_NO_MAX = 'O3' WHERE PL_CODE in ('511612', '511712', '512212', '512302', '512812', '520212', '520412', '520502', '520812', '521301', '521412', 
					'521512', '522312', '522412', '522712', '523302', '523612', '530212', '531212', '531801', '531912', '532512', '532601', '540312', '540412', '540612', '540912', '541112', '541412', '550212', '551512', 
					'551712', '560802', '561502', '562102', '563502', '571412', '571512', '571612', '571812', '571912', '572412', '572612', '572901', '573012', '573512', '574112', '574202', '574312', '574412', '574802', 
					'574912', '580712', '580812', '581001', '582712', '583312', '583712', '584101', '584401', '9002', '80010121') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();

					$cmd = " UPDATE PER_LINE SET LEVEL_NO_MAX = 'O4' WHERE PL_CODE in ('574512', '582101', '582201', '582301') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				}
			}

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_SERVICEHIS ALTER SRT_CODE NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SERVICEHIS MODIFY SRT_CODE NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_SERVICEHIS MODIFY SRT_CODE NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_SERVICEHIS", "SRH_SRT_NAME","MEMO", "2000", "NULL");

			$cmd = " INSERT INTO PER_COMGROUP (COM_GROUP, CG_NAME, CG_ACTIVE, UPDATE_USER, UPDATE_DATE, CG_SEQ_NO)
							  VALUES ('526', 'คำสั่งจัดคนลง', 1, $SESS_USERID, '$UPDATE_DATE', 26) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO, COM_ACTIVE) 
							  VALUES ('5026', 'คส. 2.6', 'คำสั่งจัดคนลง', '526', 25, 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_COMTYPE SET COM_GROUP = '526' WHERE COM_TYPE = '0206' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_EDUCATE, PER_EDUCNAME SET PER_EDUCATE.EL_CODE = 
								  PER_EDUCNAME.EL_CODE WHERE PER_EDUCNAME.EN_CODE = PER_EDUCATE.EN_CODE AND 
								  PER_EDUCATE.EN_CODE IS NOT NULL AND PER_EDUCATE.EL_CODE IS NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_EDUCATE A SET A.EL_CODE = 
								  (SELECT B.EL_CODE FROM PER_EDUCNAME B WHERE A.EN_CODE = B.EN_CODE) 
								  WHERE A.EN_CODE IS NOT NULL AND A.EL_CODE IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_EDUCATE, PER_INSTITUTE SET PER_EDUCATE.CT_CODE_EDU = 
								  PER_INSTITUTE.CT_CODE WHERE PER_INSTITUTE.INS_CODE = PER_EDUCATE.INS_CODE AND 
								  PER_EDUCATE.INS_CODE IS NOT NULL AND PER_EDUCATE.CT_CODE_EDU IS NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_EDUCATE A SET A.CT_CODE_EDU = 
								  (SELECT B.CT_CODE FROM PER_INSTITUTE B WHERE A.INS_CODE = B.INS_CODE) 
								  WHERE A.INS_CODE IS NOT NULL AND A.CT_CODE_EDU IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_EDUCATE SET CT_CODE_EDU = '140' WHERE CT_CODE_EDU IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 
							  WHERE LINKTO_WEB = 'master_table.html?table=PER_OCCUPATION' ";
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
								  VALUES (1, 'TH', $MAX_ID, 14, 'M0114 อาชีพ', 'S', 'W', 'master_table.html?table=PER_OCCUPATION', 0, 9, 296, $CREATE_DATE, $UPDATE_BY, 
								  $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 14, 'M0114 อาชีพ', 'S', 'W', 'master_table.html?table=PER_OCCUPATION', 0, 9, 296, $CREATE_DATE, $UPDATE_BY, 
								  $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}
				
			$cmd = " SELECT WORD_SEQ FROM THAIWORD_FORTHAICUT ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) {
				if($DPISDB=="odbc") 
					$cmd = " CREATE TABLE THAIWORD_FORTHAICUT(
					WORD_SEQ INTEGER NOT NULL,	
					THAIWORD VARCHAR(255) NOT NULL,
					WORD_GROUP VARCHAR(255) NULL,
					WORD_ACTIVE SINGLE NOT NULL,
					UPDATE_USER INTEGER NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_THAIWORD_FORTHAICUT PRIMARY KEY (WORD_SEQ)) ";
				elseif($DPISDB=="oci8") 
					$cmd = " CREATE TABLE THAIWORD_FORTHAICUT(
					WORD_SEQ NUMBER(10) NOT NULL,	
					THAIWORD VARCHAR2(255) NOT NULL,
					WORD_GROUP VARCHAR2(255) NULL,
					WORD_ACTIVE NUMBER(1) NOT NULL,
					UPDATE_USER NUMBER(11) NOT NULL,
					UPDATE_DATE VARCHAR2(19) NOT NULL,		
					CONSTRAINT PK_THAIWORD_FORTHAICUT PRIMARY KEY (WORD_SEQ)) ";
				elseif($DPISDB=="mysql")
					$cmd = " CREATE TABLE THAIWORD_FORTHAICUT(
					WORD_SEQ INTEGER(10) NOT NULL,	
					THAIWORD VARCHAR(255) NOT NULL,
					WORD_GROUP VARCHAR(255) NULL,
					WORD_ACTIVE SMALLINT(1) NOT NULL,
					UPDATE_USER INTEGER(11) NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_THAIWORD_FORTHAICUT PRIMARY KEY (WORD_SEQ)) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			}

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 
							  WHERE LINKTO_WEB = 'master_table_thaiword.html?table=THAIWORD_FORTHAICUT' ";
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
								  VALUES (1, 'TH', $MAX_ID, 15, 'M0115 การตัดคำสำหรับ PDF', 'S', 'W', 'master_table_thaiword.html?table=THAIWORD_FORTHAICUT', 0, 9, 296, $CREATE_DATE, $UPDATE_BY, 
								  $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 15, 'M0115 การตัดคำสำหรับ PDF', 'S', 'W', 'master_table_thaiword.html?table=THAIWORD_FORTHAICUT', 0, 9, 296, $CREATE_DATE, $UPDATE_BY, 
								  $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}
				
			if ($ISCS_FLAG==1) {
				$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_R006005.html' ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_R006007.html' ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_R006017.html' ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_R006018.html' ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'select_database_skill.html' ";
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
									  VALUES (1, 'TH', $MAX_ID, 3, 'C03 ถ่ายโอนข้อมูลด้านความเชี่ยวชาญพิเศษ', 'S', 'W', 'select_database_skill.html', 
									  0, 1, $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();

					$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
									  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
									  UPDATE_DATE, UPDATE_BY)
									  VALUES (1, 'EN', $MAX_ID, 3, 'C03 ถ่ายโอนข้อมูลด้านความเชี่ยวชาญพิเศษ', 'S', 'W', 'select_database_skill.html', 
									  0, 1, $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();
				}

				$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'select_iscs_excel.html' ";
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
									  VALUES (1, 'TH', $MAX_ID, 10, 'C10 ถ่ายโอนข้อมูลจากฐานข้อมูลผู้สำเร็จการฝึกอบรมหลักสูตร นบส.', 'S', 'W', 'select_iscs_excel.html', 
									  0, 1, $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();

					$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
									  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
									  UPDATE_DATE, UPDATE_BY)
									  VALUES (1, 'EN', $MAX_ID, 10, 'C10 ถ่ายโอนข้อมูลจากฐานข้อมูลผู้สำเร็จการฝึกอบรมหลักสูตร นบส.', 'S', 'W', 'select_iscs_excel.html', 
									  0, 1, $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();
				}

				$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'select_database_iscs.html' ";
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
									  VALUES (1, 'TH', $MAX_ID, 11, 'C11 ถ่ายโอนข้อมูลจากระบบประมวลผลสมรรถนะหลักทางการบริหาร', 'S', 'W', 'select_database_iscs.html', 
									  0, 1, $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();

					$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
									  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
									  UPDATE_DATE, UPDATE_BY)
									  VALUES (1, 'EN', $MAX_ID, 11, 'C11 ถ่ายโอนข้อมูลจากระบบประมวลผลสมรรถนะหลักทางการบริหาร', 'S', 'W', 'select_database_iscs.html', 
									  0, 1, $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();
				}

				$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'data_mgt_competency_assessment.html' ";
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
									  VALUES (1, 'TH', $MAX_ID, 17, 'K17 การประเมินสมรรถนะหลักทางการบริหาร', 'S', 'W', 'data_mgt_competency_assessment.html', 0, 40, 
									  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();

					$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
									  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
									  UPDATE_DATE, UPDATE_BY)
									  VALUES (1, 'EN', $MAX_ID, 17, 'K17 การประเมินสมรรถนะหลักทางการบริหาร', 'S', 'W', 'data_mgt_competency_assessment.html', 0, 40, 
									  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();
				} 

				$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'data_mgt_competency_assessment_ver.html' ";
				$count_data = $db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'set_mgt_competency_assessment.html' ";
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
									  VALUES (1, 'TH', $MAX_ID, 18, 'K18 ป้อนคะแนนผลการประเมินสมรรถนะหลักทางการบริหาร', 'S', 'W', 'set_mgt_competency_assessment.html', 0, 40, 
									  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();

					$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
									  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
									  UPDATE_DATE, UPDATE_BY)
									  VALUES (1, 'EN', $MAX_ID, 18, 'K18 ป้อนคะแนนผลการประเมินสมรรถนะหลักทางการบริหาร', 'S', 'W', 'set_mgt_competency_assessment.html', 0, 40, 
									  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();
				} 

				$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'data_mgt_competency_assessment_check.html' ";
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
									  VALUES (1, 'TH', $MAX_ID, 19, 'K19 ตรวจสอบการประเมินสมรรถนะหลักทางการบริหาร', 'S', 'W', 'data_mgt_competency_assessment_check.html', 0, 40, 
									  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();

					$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
									  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
									  UPDATE_DATE, UPDATE_BY)
									  VALUES (1, 'EN', $MAX_ID, 19, 'K19 ตรวจสอบการประเมินสมรรถนะหลักทางการบริหาร', 'S', 'W', 'data_mgt_competency_assessment_check.html', 0, 40, 
									  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();
				} 

			}

			$cmd = " UPDATE PER_EDUCNAME SET EN_NAME = REPLACE(EN_NAME, 'ป.', '') 
							WHERE EN_NAME LIKE 'ป.%' AND  EN_NAME LIKE '%บัณฑิต%' AND EN_NAME NOT LIKE '%ป.บัณฑิต%' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POSITION SET POS_SEQ_NO = POS_NO WHERE POS_SEQ_NO IS NULL AND to_number(replace(POS_NO,'-','')) < 100000 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_PERSONAL A SET A.PER_SEQ_NO = 
							  (SELECT B.POS_NO FROM PER_POSITION B WHERE A.POS_ID = B.POS_ID AND to_number(replace(POS_NO,'-','')) < 100000) 
							  WHERE A.POS_ID IS NOT NULL AND A.PER_SEQ_NO IS NULL AND PER_TYPE = 1 AND PER_STATUS = 1 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_EMP SET POEM_SEQ_NO = POEM_NO WHERE POEM_SEQ_NO IS NULL AND to_number(replace(POEM_NO,'-','')) < 100000 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_PERSONAL A SET A.PER_SEQ_NO = 
							  (SELECT B.POEM_NO FROM PER_POS_EMP B WHERE A.POEM_ID = B.POEM_ID AND to_number(replace(POEM_NO,'-','')) < 100000) 
							  WHERE A.POEM_ID IS NOT NULL AND A.PER_SEQ_NO IS NULL AND PER_TYPE = 2 AND PER_STATUS = 1 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_EMPSER SET POEMS_SEQ_NO = POEMS_NO WHERE POEMS_SEQ_NO IS NULL AND to_number(replace(POEMS_NO,'-','')) < 100000 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_PERSONAL A SET A.PER_SEQ_NO = 
							  (SELECT B.POEMS_NO FROM PER_POS_EMPSER B WHERE A.POEMS_ID = B.POEMS_ID AND to_number(replace(POEMS_NO,'-','')) < 100000) 
							  WHERE A.POEMS_ID IS NOT NULL AND A.PER_SEQ_NO IS NULL AND PER_TYPE = 3 AND PER_STATUS = 1 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_TEMP SET POT_SEQ_NO = POT_NO WHERE POT_SEQ_NO IS NULL AND to_number(replace(POT_NO,'-','')) < 100000 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_PERSONAL A SET A.PER_SEQ_NO = 
							  (SELECT B.POT_NO FROM PER_POS_TEMP B WHERE A.POT_ID = B.POT_ID AND to_number(replace(POT_NO,'-','')) < 100000) 
							  WHERE A.POT_ID IS NOT NULL AND A.PER_SEQ_NO IS NULL AND PER_TYPE = 4 AND PER_STATUS = 1 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_EDUCATE SET EL_CODE = TRIM(EL_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " SELECT AC_CODE FROM PER_ABSENTCOND ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) {
				if($DPISDB=="odbc") 
					$cmd = " CREATE TABLE PER_ABSENTCOND(
					AC_CODE VARCHAR(10) NOT NULL,	
					AC_NAME VARCHAR(100) NOT NULL,
					AC_FLAG SINGLE NOT NULL,
					AC_GOV_AGE NUMBER NULL,
					AC_DAY INTEGER2 NULL,
					AC_COLLECT INTEGER2 NULL,
					AC_SEQ_NO INTEGER2 NULL,
					AC_ACTIVE SINGLE NOT NULL,
					AC_OTHERNAME MEMO NULL,
					UPDATE_USER INTEGER NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_ABSENTCOND PRIMARY KEY (AC_CODE)) ";
				elseif($DPISDB=="oci8") 
					$cmd = " CREATE TABLE PER_ABSENTCOND(
					AC_CODE VARCHAR2(10) NOT NULL,	
					AC_NAME VARCHAR2(100) NOT NULL,
					AC_FLAG NUMBER(1) NOT NULL,
					AC_GOV_AGE NUMBER(4,2) NULL,
					AC_DAY NUMBER(2) NULL,
					AC_COLLECT NUMBER(2) NULL,
					AC_SEQ_NO NUMBER(5) NULL,
					AC_ACTIVE NUMBER(1) NOT NULL,
					AC_OTHERNAME VARCHAR2(1000) NULL,
					UPDATE_USER NUMBER(11) NOT NULL,
					UPDATE_DATE VARCHAR2(19) NOT NULL,		
					CONSTRAINT PK_PER_ABSENTCOND PRIMARY KEY (AC_CODE)) ";
				elseif($DPISDB=="mysql")
					$cmd = " CREATE TABLE PER_ABSENTCOND(
					AC_CODE VARCHAR(10) NOT NULL,	
					AC_NAME VARCHAR(100) NOT NULL,
					AC_FLAG SMALLINT(1) NOT NULL,
					AC_GOV_AGE DECIMAL(4,2) NULL,
					AC_DAY SMALLINT(2) NULL,
					AC_COLLECT SMALLINT(2) NULL,
					AC_SEQ_NO SMALLINT(5) NULL,
					AC_ACTIVE SMALLINT(1) NOT NULL,
					AC_OTHERNAME TEXT NULL,
					UPDATE_USER INTEGER(11) NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_ABSENTCOND PRIMARY KEY (AC_CODE)) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_ABSENTCOND (AC_CODE, AC_NAME, AC_FLAG, AC_GOV_AGE, AC_DAY, 
								  AC_COLLECT, AC_SEQ_NO, AC_ACTIVE, UPDATE_USER, UPDATE_DATE)
								  VALUES ('01', 'รับราชการไม่ถึง 6 เดือน', 1, 0.5, 0, 0, 1, 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_ABSENTCOND (AC_CODE, AC_NAME, AC_FLAG, AC_GOV_AGE, AC_DAY, 
								  AC_COLLECT, AC_SEQ_NO, AC_ACTIVE, UPDATE_USER, UPDATE_DATE)
								  VALUES ('02', 'รับราชการไม่เกิน 10 ปี', 1, 10, 10, 20, 2, 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_ABSENTCOND (AC_CODE, AC_NAME, AC_FLAG, AC_GOV_AGE, AC_DAY, 
								  AC_COLLECT, AC_SEQ_NO, AC_ACTIVE, UPDATE_USER, UPDATE_DATE)
								  VALUES ('03', 'รับราชการไม่น้อยกว่า 10 ปี', 2, 10, 10, 30, 3, 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			}

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'master_table_absentcond.html?table=PER_ABSENTCOND' ";
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
								  VALUES (1, 'TH', $MAX_ID, 5, 'M0605 เงื่อนไขการลาพักผ่อน', 'S', 'W', 'master_table_absentcond.html?table=PER_ABSENTCOND', 0, 9, 301, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 5, 'M0605 เงื่อนไขการลาพักผ่อน', 'S', 'W', 'master_table_absentcond.html?table=PER_ABSENTCOND', 0, 35, 301, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " SELECT VC_YEAR FROM PER_VACATION ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) {
				if($DPISDB=="odbc") 
					$cmd = " CREATE TABLE PER_VACATION(
					VC_YEAR VARCHAR(4) NOT NULL,	
					PER_ID INTEGER NOT NULL,
					VC_DAY NUMBER NULL,
					UPDATE_USER INTEGER NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_VACATION PRIMARY KEY (VC_YEAR, PER_ID)) ";
				elseif($DPISDB=="oci8") 
					$cmd = " CREATE TABLE PER_VACATION(
					VC_YEAR VARCHAR2(4) NOT NULL,	
					PER_ID NUMBER(10) NOT NULL,
					VC_DAY NUMBER(5,2) NULL,
					UPDATE_USER NUMBER(11) NOT NULL,
					UPDATE_DATE VARCHAR2(19) NOT NULL,		
					CONSTRAINT PK_PER_VACATION PRIMARY KEY (VC_YEAR, PER_ID)) ";
				elseif($DPISDB=="mysql")
					$cmd = " CREATE TABLE PER_VACATION(
					VC_YEAR VARCHAR(4) NOT NULL,	
					PER_ID INTEGER(10) NOT NULL,
					VC_DAY DECIMAL(5,2) NULL,
					UPDATE_USER INTEGER(11) NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_VACATION PRIMARY KEY (VC_YEAR, PER_ID)) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			}

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'personal_vacation.html' ";
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
								  VALUES (1, 'TH', $MAX_ID, 4, 'P0604 ตั้งค่าวันลาพักผ่อนสะสม', 'S', 'W', 'personal_vacation.html', 0, 35, 246, $CREATE_DATE, 
								  $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 4, 'P0604 ตั้งค่าวันลาพักผ่อนสะสม', 'S', 'W', 'personal_vacation.html', 0, 35, 246, $CREATE_DATE, 
								  $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			if ($BKK_FLAG != 1) {
				$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'master_table.html?table=PER_MINISTRY_GROUP' ";
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
									  VALUES (1, 'TH', $MAX_ID, 3, 'M0203 กลุ่มกระทรวง', 'S', 'W', 'master_table.html?table=PER_MINISTRY_GROUP', 0, 9, 302, 
									  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();

					$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
									  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
									  CREATE_BY, UPDATE_DATE, UPDATE_BY)
									  VALUES (1, 'EN', $MAX_ID, 3, 'M0203 กลุ่มกระทรวง', 'S', 'W', 'master_table.html?table=PER_MINISTRY_GROUP', 0, 9, 302, 
									  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();
				}

				$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'master_table.html?table=PER_PROVINCE_GROUP' ";
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
									  VALUES (1, 'TH', $MAX_ID, 4, 'M0204 กลุ่มจังหวัด', 'S', 'W', 'master_table.html?table=PER_PROVINCE_GROUP', 0, 9, 302, 
									  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();

					$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
									  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
									  CREATE_BY, UPDATE_DATE, UPDATE_BY)
									  VALUES (1, 'EN', $MAX_ID, 4, 'M0204 กลุ่มจังหวัด', 'S', 'W', 'master_table.html?table=PER_PROVINCE_GROUP', 0, 9, 302, 
									  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();
				}
			}

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'master_table_license_type.html?table=PER_LICENSE_TYPE' ";
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
								  VALUES (1, 'TH', $MAX_ID, 10, 'M0710 ใบอนุญาตประกอบวิชาชีพ', 'S', 'W', 'master_table_license_type.html?table=PER_LICENSE_TYPE', 0, 9, 293, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 10, 'M0710 ใบอนุญาตประกอบวิชาชีพ', 'S', 'W', 'master_table_license_type.html?table=PER_LICENSE_TYPE', 0, 9, 293, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'master_table.html?table=PER_TEST_COURSE' ";
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
								  VALUES (1, 'TH', $MAX_ID, 11, 'M0711 หลักสูตรการสอบ', 'S', 'W', 'master_table.html?table=PER_TEST_COURSE', 0, 9, 293, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 11, 'M0711 หลักสูตรการสอบ', 'S', 'W', 'master_table.html?table=PER_TEST_COURSE', 0, 9, 293, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			add_field("PER_ABILITY", "AUDIT_FLAG","CHAR", "1", "NULL");
			add_field("PER_ABSENTHIS", "AUDIT_FLAG","CHAR", "1", "NULL");
			add_field("PER_ABSENTSUM", "AUDIT_FLAG","CHAR", "1", "NULL");
			add_field("PER_ACTINGHIS", "AUDIT_FLAG","CHAR", "1", "NULL");
			add_field("PER_ADDRESS", "AUDIT_FLAG","CHAR", "1", "NULL");
			add_field("PER_APPROVE_RESOLUTION", "AUDIT_FLAG","CHAR", "1", "NULL");
			add_field("PER_CHILD", "AUDIT_FLAG","CHAR", "1", "NULL");
			add_field("PER_DECORATEHIS", "AUDIT_FLAG","CHAR", "1", "NULL");
			add_field("PER_EDUCATE", "AUDIT_FLAG","CHAR", "1", "NULL");
			add_field("PER_EXCELLENT_PERFORMANCE", "AUDIT_FLAG","CHAR", "1", "NULL");
			add_field("PER_EXTRA_INCOMEHIS", "AUDIT_FLAG","CHAR", "1", "NULL");
			add_field("PER_EXTRAHIS", "AUDIT_FLAG","CHAR", "1", "NULL");
			add_field("PER_FAMILY", "AUDIT_FLAG","CHAR", "1", "NULL");
			add_field("PER_HEIR", "AUDIT_FLAG","CHAR", "1", "NULL");
			add_field("PER_HOLIDAYHIS", "AUDIT_FLAG","CHAR", "1", "NULL");
			add_field("PER_KPI_FORM", "AUDIT_FLAG","CHAR", "1", "NULL");
			add_field("PER_LICENSEHIS", "AUDIT_FLAG","CHAR", "1", "NULL");
			add_field("PER_MARRHIS", "AUDIT_FLAG","CHAR", "1", "NULL");
			add_field("PER_NAMEHIS", "AUDIT_FLAG","CHAR", "1", "NULL");
			add_field("PER_OTHER_OCCUPATION", "AUDIT_FLAG","CHAR", "1", "NULL");
			add_field("PER_PERSONALPIC", "AUDIT_FLAG","CHAR", "1", "NULL");
			add_field("PER_POS_MGTSALARYHIS", "AUDIT_FLAG","CHAR", "1", "NULL");
			add_field("PER_POS_MOVE", "AUDIT_FLAG","CHAR", "1", "NULL");
			add_field("PER_POSITION", "AUDIT_FLAG","CHAR", "1", "NULL");
			add_field("PER_POSITIONHIS", "AUDIT_FLAG","CHAR", "1", "NULL");
			add_field("PER_PUNISHMENT", "AUDIT_FLAG","CHAR", "1", "NULL");
			add_field("PER_REWARDHIS", "AUDIT_FLAG","CHAR", "1", "NULL");
			add_field("PER_SALARYHIS", "AUDIT_FLAG","CHAR", "1", "NULL");
			add_field("PER_SCHOLAR", "AUDIT_FLAG","CHAR", "1", "NULL");
			add_field("PER_SENIOR_EXCUSIVE", "AUDIT_FLAG","CHAR", "1", "NULL");
			add_field("PER_SERVICEHIS", "AUDIT_FLAG","CHAR", "1", "NULL");
			add_field("PER_SLIP", "AUDIT_FLAG","CHAR", "1", "NULL");
			add_field("PER_SOLDIERHIS", "AUDIT_FLAG","CHAR", "1", "NULL");
			add_field("PER_SPECIAL_SKILL", "AUDIT_FLAG","CHAR", "1", "NULL");
			add_field("PER_TEST_COURSEHIS", "AUDIT_FLAG","CHAR", "1", "NULL");
			add_field("PER_TIMEHIS", "AUDIT_FLAG","CHAR", "1", "NULL");
			add_field("PER_TRAINING", "AUDIT_FLAG","CHAR", "1", "NULL");
			add_field("PER_WORK_CYCLEHIS", "AUDIT_FLAG","CHAR", "1", "NULL");
			add_field("PER_WORK_TIME", "AUDIT_FLAG","CHAR", "1", "NULL");

			if ($BKK_FLAG==1) {
//				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table.html?table=PER_ABILITYGRP' 
//								WHERE LINKTO_WEB = 'master_table_new.html?table=PER_ABILITYGRP' ";
				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_new.html?table=PER_ABILITYGRP' 
								WHERE LINKTO_WEB = 'master_table.html?table=PER_ABILITYGRP' ";
				$db->send_cmd($cmd);
				//$db->show_error();

//				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_absentcond.html?table=PER_ABSENTCOND' 
//								WHERE LINKTO_WEB = 'master_table_new.html?table=PER_ABSENTCOND' ";
				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_new.html?table=PER_ABSENTCOND' 
								WHERE LINKTO_WEB = 'master_table_absentcond.html?table=PER_ABSENTCOND' ";
				$db->send_cmd($cmd);
				//$db->show_error();

//				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_absenttype.html?table=PER_ABSENTTYPE' 
//								WHERE LINKTO_WEB = 'master_table_new.html?table=PER_ABSENTTYPE' ";
				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_new.html?table=PER_ABSENTTYPE' 
								WHERE LINKTO_WEB = 'master_table_absenttype.html?table=PER_ABSENTTYPE' ";
				$db->send_cmd($cmd);
				//$db->show_error();

//				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_amphur.html?table=PER_AMPHUR' 
//								WHERE LINKTO_WEB = 'master_table_new.html?table=PER_AMPHUR' ";
				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_new.html?table=PER_AMPHUR' 
								WHERE LINKTO_WEB = 'master_table_amphur.html?table=PER_AMPHUR' ";
				$db->send_cmd($cmd);
				//$db->show_error();

//				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_new.html?table=PER_ASSESS_MAIN' 
//								WHERE LINKTO_WEB = 'master_table_assess_main.html?table=PER_ASSESS_MAIN' ";
//				$db->send_cmd($cmd);
				//$db->show_error();

//				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_attachment.html?table=PER_ATTACHMENT' 
//								WHERE LINKTO_WEB = 'master_table_new.html?table=PER_ATTACHMENT' ";
				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_new.html?table=PER_ATTACHMENT' 
								WHERE LINKTO_WEB = 'master_table_attachment.html?table=PER_ATTACHMENT' ";
				$db->send_cmd($cmd);
				//$db->show_error();

//				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table.html?table=PER_BLOOD' 
//								WHERE LINKTO_WEB = 'master_table_new.html?table=PER_BLOOD' ";
				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_new.html?table=PER_BLOOD' 
								WHERE LINKTO_WEB = 'master_table.html?table=PER_BLOOD' ";
				$db->send_cmd($cmd);
				//$db->show_error();

//				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_co_level.html?table=PER_CO_LEVEL' 
//								WHERE LINKTO_WEB = 'master_table_new.html?table=PER_CO_LEVEL' ";
				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_new.html?table=PER_CO_LEVEL' 
								WHERE LINKTO_WEB = 'master_table_co_level.html?table=PER_CO_LEVEL' ";
				$db->send_cmd($cmd);
				//$db->show_error();

//				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table.html?table=PER_COMGROUP' 
//								WHERE LINKTO_WEB = 'master_table_new.html?table=PER_COMGROUP' ";
				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_new.html?table=PER_COMGROUP' 
								WHERE LINKTO_WEB = 'master_table.html?table=PER_COMGROUP' ";
				$db->send_cmd($cmd);
				//$db->show_error();

//				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_comtype.html?table=PER_COMTYPE' 
//								WHERE LINKTO_WEB = 'master_table_new.html?table=PER_COMTYPE' ";
				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_new.html?table=PER_COMTYPE' 
								WHERE LINKTO_WEB = 'master_table_comtype.html?table=PER_COMTYPE' ";
				$db->send_cmd($cmd);
				//$db->show_error();

//				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table.html?table=PER_CONDITION' 
//								WHERE LINKTO_WEB = 'master_table_new.html?table=PER_CONDITION' ";
				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_new.html?table=PER_CONDITION' 
								WHERE LINKTO_WEB = 'master_table.html?table=PER_CONDITION' ";
				$db->send_cmd($cmd);
				//$db->show_error();

//				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table.html?table=PER_COUNTRY' 
//								WHERE LINKTO_WEB = 'master_table_new.html?table=PER_COUNTRY' ";
				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_new.html?table=PER_COUNTRY' 
								WHERE LINKTO_WEB = 'master_table.html?table=PER_COUNTRY' ";
				$db->send_cmd($cmd);
				//$db->show_error();

//				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table.html?table=PER_CRIME' 
//								WHERE LINKTO_WEB = 'master_table_new.html?table=PER_CRIME' ";
				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_new.html?table=PER_CRIME' 
								WHERE LINKTO_WEB = 'master_table.html?table=PER_CRIME' ";
				$db->send_cmd($cmd);
				//$db->show_error();

//				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_new.html?table=PER_CRIME_DTL' 
//								WHERE LINKTO_WEB = 'master_table_crime_dtl.html?table=PER_CRIME_DTL' ";
//				$db->send_cmd($cmd);
				//$db->show_error();

//				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_new.html?table=PER_DECORATION' 
//								WHERE LINKTO_WEB = 'master_table_decoration.html?table=PER_DECORATION' ";
//				$db->send_cmd($cmd);
				//$db->show_error();

//				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_district.html?table=PER_DISTRICT' 
//								WHERE LINKTO_WEB = 'master_table_new.html?table=PER_DISTRICT' ";
				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_new.html?table=PER_DISTRICT' 
								WHERE LINKTO_WEB = 'master_table_district.html?table=PER_DISTRICT' ";
				$db->send_cmd($cmd);
				//$db->show_error();

//				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table.html?table=PER_DIVORCE' 
//								WHERE LINKTO_WEB = 'master_table_new.html?table=PER_DIVORCE' ";
				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_new.html?table=PER_DIVORCE' 
								WHERE LINKTO_WEB = 'master_table.html?table=PER_DIVORCE' ";
				$db->send_cmd($cmd);
				//$db->show_error();

//				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_educlevel.html?table=PER_EDUCLEVEL' 
//								WHERE LINKTO_WEB = 'master_table_new.html?table=PER_EDUCLEVEL' ";
				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_new.html?table=PER_EDUCLEVEL' 
								WHERE LINKTO_WEB = 'master_table_educlevel.html?table=PER_EDUCLEVEL' ";
				$db->send_cmd($cmd);
				//$db->show_error();

//				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_educmajor.html?table=PER_EDUCMAJOR' 
//								WHERE LINKTO_WEB = 'master_table_new.html?table=PER_EDUCMAJOR' ";
				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_new.html?table=PER_EDUCMAJOR' 
								WHERE LINKTO_WEB = 'master_table_educmajor.html?table=PER_EDUCMAJOR' ";
				$db->send_cmd($cmd);
				//$db->show_error();

//				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_educname.html?table=PER_EDUCNAME' 
//								WHERE LINKTO_WEB = 'master_table_new.html?table=PER_EDUCNAME' ";
				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_new.html?table=PER_EDUCNAME' 
								WHERE LINKTO_WEB = 'master_table_educname.html?table=PER_EDUCNAME' ";
				$db->send_cmd($cmd);
				//$db->show_error();

//				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_emp_status.html?table=PER_EMP_STATUS' 
//								WHERE LINKTO_WEB = 'master_table_new.html?table=PER_EMP_STATUS' ";
				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_new.html?table=PER_EMP_STATUS' 
								WHERE LINKTO_WEB = 'master_table_emp_status.html?table=PER_EMP_STATUS' ";
				$db->send_cmd($cmd);
				//$db->show_error();

//				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table.html?table=PER_EXPENSE_BUDGET' 
//								WHERE LINKTO_WEB = 'master_table_new.html?table=PER_EXPENSE_BUDGET' ";
				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_new.html?table=PER_EXPENSE_BUDGET' 
								WHERE LINKTO_WEB = 'master_table.html?table=PER_EXPENSE_BUDGET' ";
				$db->send_cmd($cmd);
				//$db->show_error();

//				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_extratype.html?table=PER_EXTRATYPE' 
//								WHERE LINKTO_WEB = 'master_table_new.html' ";
				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_new.html?table=PER_EXTRATYPE' 
								WHERE LINKTO_WEB = 'master_table_extratype.html' ";
				$db->send_cmd($cmd);
				//$db->show_error();

//				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table.html?table=PER_GOODNESS' 
//								WHERE LINKTO_WEB = 'master_table_new.html?table=PER_GOODNESS' ";
				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_new.html?table=PER_GOODNESS' 
								WHERE LINKTO_WEB = 'master_table.html?table=PER_GOODNESS' ";
				$db->send_cmd($cmd);
				//$db->show_error();

//				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table.html?table=PER_HEIRTYPE' 
//								WHERE LINKTO_WEB = 'master_table_new.html?table=PER_HEIRTYPE' ";
				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_new.html?table=PER_HEIRTYPE' 
								WHERE LINKTO_WEB = 'master_table.html?table=PER_HEIRTYPE' ";
				$db->send_cmd($cmd);
				//$db->show_error();

//				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_holiday.html?table=PER_HOLIDAY' 
//								WHERE LINKTO_WEB = 'master_table_new.html?table=PER_HOLIDAY' ";
				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_new.html?table=PER_HOLIDAY' 
								WHERE LINKTO_WEB = 'master_table_holiday.html?table=PER_HOLIDAY' ";
				$db->send_cmd($cmd);
				//$db->show_error();

//				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_new.html?table=PER_HOLIDAY_GROUP' 
//								WHERE LINKTO_WEB = 'master_table_holiday_group.html' ";
//				$db->send_cmd($cmd);
				//$db->show_error();

//				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_institute.html?table=PER_INSTITUTE' 
//								WHERE LINKTO_WEB = 'master_table_new.html?table=PER_INSTITUTE' ";
				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_new.html?table=PER_INSTITUTE' 
								WHERE LINKTO_WEB = 'master_table_institute.html?table=PER_INSTITUTE' ";
				$db->send_cmd($cmd);
				//$db->show_error();
/*
				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_jem_acc.html' 
								WHERE LINKTO_WEB = 'master_table_jem_acc.html' ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_jem_answer_info.html' 
								WHERE LINKTO_WEB = 'master_table_jem_answer_info.html' ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_jem_consistency_check.html' 
								WHERE LINKTO_WEB = 'master_table_jem_consistency_check.html' ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_jem_grade.html' 
								WHERE LINKTO_WEB = 'master_table_jem_grade.html' ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_jem_kh.html' 
								WHERE LINKTO_WEB = 'master_table_jem_kh.html' ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_jem_mapping.html' 
								WHERE LINKTO_WEB = 'master_table_jem_mapping.html' ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_jem_profile_check.html' 
								WHERE LINKTO_WEB = 'master_table_jem_profile_check.html' ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_jem_ps.html' 
								WHERE LINKTO_WEB = 'master_table_jem_ps.html' ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_jem_ps_kh.html' 
								WHERE LINKTO_WEB = 'master_table_jem_ps_kh.html' ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_jem_question_info.html' 
								WHERE LINKTO_WEB = 'master_table_jem_question_info.html' ";
				$db->send_cmd($cmd);
				//$db->show_error();
*/
//				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_new.html?table=PER_LAYER' 
//								WHERE LINKTO_WEB = 'master_table_layer.html?table=PER_LAYER' ";
//				$db->send_cmd($cmd);
				//$db->show_error();

//				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_new.html?table=PER_LAYER_NEW' 
//								WHERE LINKTO_WEB = 'master_table_layer.html?table=PER_LAYER_NEW' ";
//				$db->send_cmd($cmd);
				//$db->show_error();

//				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_new.html?table=PER_LAYEREMP_NEW' 
//								WHERE LINKTO_WEB = 'master_table_layeremp.html?table=PER_LAYEREMP_NEW' ";
//				$db->send_cmd($cmd);
				//$db->show_error();

//				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_level.html?table=PER_LEVEL' 
//								WHERE LINKTO_WEB = 'master_table_new.html?table=PER_LEVEL' ";
				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_new.html?table=PER_LEVEL' 
								WHERE LINKTO_WEB = 'master_table_level.html?table=PER_LEVEL' ";
				$db->send_cmd($cmd);
				//$db->show_error();

//				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_license_type.html?table=PER_LICENSE_TYPE' 
//								WHERE LINKTO_WEB = 'master_table_new.html?table=PER_LICENSE_TYPE' ";
				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_new.html?table=PER_LICENSE_TYPE' 
								WHERE LINKTO_WEB = 'master_table_license_type.html?table=PER_LICENSE_TYPE' ";
				$db->send_cmd($cmd);
				//$db->show_error();

//				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table.html?table=PER_LINE_GROUP' 
//								WHERE LINKTO_WEB = 'master_table_new.html?table=PER_LINE_GROUP' ";
				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_new.html?table=PER_LINE_GROUP' 
								WHERE LINKTO_WEB = 'master_table.html?table=PER_LINE_GROUP' ";
				$db->send_cmd($cmd);
				//$db->show_error();

//				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_line.html?table=PER_LINE' 
//								WHERE LINKTO_WEB = 'master_table_new.html?table=PER_LINE' ";
				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_new.html?table=PER_LINE' 
								WHERE LINKTO_WEB = 'master_table_line.html?table=PER_LINE' ";
				$db->send_cmd($cmd);
				//$db->show_error();

//				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table.html?table=PER_MARRIED' 
//								WHERE LINKTO_WEB = 'master_table_new.html?table=PER_MARRIED' ";
				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_new.html?table=PER_MARRIED' 
								WHERE LINKTO_WEB = 'master_table.html?table=PER_MARRIED' ";
				$db->send_cmd($cmd);
				//$db->show_error();

//				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table.html?table=PER_MINISTRY_GROUP' 
//								WHERE LINKTO_WEB = 'master_table_new.html?table=PER_MINISTRY_GROUP' ";
				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_new.html?table=PER_MINISTRY_GROUP' 
								WHERE LINKTO_WEB = 'master_table.html?table=PER_MINISTRY_GROUP' ";
				$db->send_cmd($cmd);
				//$db->show_error();

//				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_mgt.html?table=PER_MGT' 
//								WHERE LINKTO_WEB = 'master_table_new.html?table=PER_MGT' ";
				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_new.html?table=PER_MGT' 
								WHERE LINKTO_WEB = 'master_table_mgt.html?table=PER_MGT' ";
				$db->send_cmd($cmd);
				//$db->show_error();

//				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_mgt_competence.html?table=PER_MGT_COMPETENCE' 
//								WHERE LINKTO_WEB = 'master_table_new.html?table=PER_MGT_COMPETENCE' ";
				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_new.html?table=PER_MGT_COMPETENCE' 
								WHERE LINKTO_WEB = 'master_table_mgt_competence.html?table=PER_MGT_COMPETENCE' ";
				$db->send_cmd($cmd);
				//$db->show_error();

//				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_movment.html?table=PER_MOVMENT' 
//								WHERE LINKTO_WEB = 'master_table_new.html?table=PER_MOVMENT' ";
				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_new.html?table=PER_MOVMENT' 
								WHERE LINKTO_WEB = 'master_table_movment.html?table=PER_MOVMENT' ";
				$db->send_cmd($cmd);
				//$db->show_error();

//				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table.html?table=PER_OCCUPATION' 
//								WHERE LINKTO_WEB = 'master_table_new.html?table=PER_OCCUPATION' ";
				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_new.html?table=PER_OCCUPATION' 
								WHERE LINKTO_WEB = 'master_table.html?table=PER_OCCUPATION' ";
				$db->send_cmd($cmd);
				//$db->show_error();

//				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table.html?table=PER_OFF_TYPE' 
//								WHERE LINKTO_WEB = 'master_table_new.html?table=PER_OFF_TYPE' ";
				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_new.html?table=PER_OFF_TYPE' 
								WHERE LINKTO_WEB = 'master_table.html?table=PER_OFF_TYPE' ";
				$db->send_cmd($cmd);
				//$db->show_error();

//				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table.html?table=PER_ORG_LEVEL' 
//								WHERE LINKTO_WEB = 'master_table_new.html?table=PER_ORG_LEVEL' ";
				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_new.html?table=PER_ORG_LEVEL' 
								WHERE LINKTO_WEB = 'master_table.html?table=PER_ORG_LEVEL' ";
				$db->send_cmd($cmd);
				//$db->show_error();

//				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table.html?table=PER_ORG_TYPE' 
//								WHERE LINKTO_WEB = 'master_table_new.html?table=PER_ORG_TYPE' ";
				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_new.html?table=PER_ORG_TYPE' 
								WHERE LINKTO_WEB = 'master_table.html?table=PER_ORG_TYPE' ";
				$db->send_cmd($cmd);
				//$db->show_error();

//				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table.html?table=PER_PENALTY' 
//								WHERE LINKTO_WEB = 'master_table_new.html?table=PER_PENALTY' ";
				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_new.html?table=PER_PENALTY' 
								WHERE LINKTO_WEB = 'master_table.html?table=PER_PENALTY' ";
				$db->send_cmd($cmd);
				//$db->show_error();

//				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table.html?table=PER_PERFORMANCE' 
//								WHERE LINKTO_WEB = 'master_table_new.html?table=PER_PERFORMANCE' ";
				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_new.html?table=PER_PERFORMANCE' 
								WHERE LINKTO_WEB = 'master_table.html?table=PER_PERFORMANCE' ";
				$db->send_cmd($cmd);
				//$db->show_error();

//				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_new.html?table=PER_POS_GROUP' 
//								WHERE LINKTO_WEB = 'master_table_pos_group.html?table=PER_POS_GROUP' ";
//				$db->send_cmd($cmd);
				//$db->show_error();

//				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_pos_level_salary.html?table=PER_POS_LEVELSALARY' 
//								WHERE LINKTO_WEB = 'master_table_new.html?table=PER_POS_LEVELSALARY' ";
				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_new.html?table=PER_POS_LEVELSALARY' 
								WHERE LINKTO_WEB = 'master_table_pos_level_salary.html?table=PER_POS_LEVELSALARY' ";
				$db->send_cmd($cmd);
				//$db->show_error();

//				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_new.html?table=PER_POS_MGTSALARY.html' 
//								WHERE LINKTO_WEB = 'master_table_pos_mgtsalary.html' ";
//				$db->send_cmd($cmd);
				//$db->show_error();

//				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table.html?table=PER_POS_EMPSER_FRAME' 
//								WHERE LINKTO_WEB = 'master_table_new.html?table=PER_POS_EMPSER_FRAME' ";
				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_new.html?table=PER_POS_EMPSER_FRAME' 
								WHERE LINKTO_WEB = 'master_table.html?table=PER_POS_EMPSER_FRAME' ";
				$db->send_cmd($cmd);
				//$db->show_error();

//				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_new.html?table=PER_POS_NAME' 
//								WHERE LINKTO_WEB = 'master_table_pos_name.html?table=PER_POS_NAME' ";
//				$db->send_cmd($cmd);
				//$db->show_error();

//				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table.html?table=PER_POS_STATUS' 
//								WHERE LINKTO_WEB = 'master_table_new.html?table=PER_POS_STATUS' ";
				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_new.html?table=PER_POS_STATUS' 
								WHERE LINKTO_WEB = 'master_table.html?table=PER_POS_STATUS' ";
				$db->send_cmd($cmd);
				//$db->show_error();

//				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table.html?table=PER_PRACTICE' 
//								WHERE LINKTO_WEB = 'master_table_new.html?table=PER_PRACTICE' ";
				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_new.html?table=PER_PRACTICE' 
								WHERE LINKTO_WEB = 'master_table.html?table=PER_PRACTICE' ";
				$db->send_cmd($cmd);
				//$db->show_error();

//				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_prename.html?table=PER_PRENAME' 
//								WHERE LINKTO_WEB = 'master_table_new.html?table=PER_PRENAME' ";
				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_new.html?table=PER_PRENAME' 
								WHERE LINKTO_WEB = 'master_table_prename.html?table=PER_PRENAME' ";
				$db->send_cmd($cmd);
				//$db->show_error();

//				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table.html?table=PER_PROJECT_GROUP' 
//								WHERE LINKTO_WEB = 'master_table_new.html?table=PER_PROJECT_GROUP' ";
				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_new.html?table=PER_PROJECT_GROUP' 
								WHERE LINKTO_WEB = 'master_table.html?table=PER_PROJECT_GROUP' ";
				$db->send_cmd($cmd);
				//$db->show_error();

//				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table.html?table=PER_PROJECT_PAYMENT' 
//								WHERE LINKTO_WEB = 'master_table_new.html?table=PER_PROJECT_PAYMENT' ";
				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_new.html?table=PER_PROJECT_PAYMENT' 
								WHERE LINKTO_WEB = 'master_table.html?table=PER_PROJECT_PAYMENT' ";
				$db->send_cmd($cmd);
				//$db->show_error();

//				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_province.html?table=PER_PROVINCE' 
//								WHERE LINKTO_WEB = 'master_table_new.html?table=PER_PROVINCE' ";
				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_new.html?table=PER_PROVINCE' 
								WHERE LINKTO_WEB = 'master_table_province.html?table=PER_PROVINCE' ";
				$db->send_cmd($cmd);
				//$db->show_error();

//				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table.html?table=PER_PROVINCE_GROUP' 
//								WHERE LINKTO_WEB = 'master_table_new.html?table=PER_PROVINCE_GROUP' ";
				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_new.html?table=PER_PROVINCE_GROUP' 
								WHERE LINKTO_WEB = 'master_table.html?table=PER_PROVINCE_GROUP' ";
				$db->send_cmd($cmd);
				//$db->show_error();

//				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table.html?table=PER_RELIGION' 
//								WHERE LINKTO_WEB = 'master_table_new.html?table=PER_RELIGION' ";
				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_new.html?table=PER_RELIGION' 
								WHERE LINKTO_WEB = 'master_table.html?table=PER_RELIGION' ";
				$db->send_cmd($cmd);
				//$db->show_error();

//				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table.html?table=PER_REWARD' 
//								WHERE LINKTO_WEB = 'master_table_new.html?table=PER_REWARD' ";
				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_new.html?table=PER_REWARD' 
								WHERE LINKTO_WEB = 'master_table.html?table=PER_REWARD' ";
				$db->send_cmd($cmd);
				//$db->show_error();

//				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_salary_movment.html?table=PER_SALARY_MOVMENT' 
//								WHERE LINKTO_WEB = 'master_table_new.html?table=PER_SALARY_MOVMENT' ";
				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_new.html?table=PER_SALARY_MOVMENT' 
								WHERE LINKTO_WEB = 'master_table_salary_movment.html?table=PER_SALARY_MOVMENT' ";
				$db->send_cmd($cmd);
				//$db->show_error();

//				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_scholarship.html?table=PER_SCHOLARSHIP' 
//								WHERE LINKTO_WEB = 'master_table_new.html?table=PER_SCHOLARSHIP' ";
				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_new.html?table=PER_SCHOLARSHIP' 
								WHERE LINKTO_WEB = 'master_table_scholarship.html?table=PER_SCHOLARSHIP' ";
				$db->send_cmd($cmd);
				//$db->show_error();

//				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table.html?table=PER_SCHOLARTYPE' 
//								WHERE LINKTO_WEB = 'master_table_new.html?table=PER_SCHOLARTYPE' ";
				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_new.html?table=PER_SCHOLARTYPE' 
								WHERE LINKTO_WEB = 'master_table.html?table=PER_SCHOLARTYPE' ";
				$db->send_cmd($cmd);
				//$db->show_error();

//				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table.html?table=PER_SERVICE' 
//								WHERE LINKTO_WEB = 'master_table_new.html?table=PER_SERVICE' ";
				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_new.html?table=PER_SERVICE' 
								WHERE LINKTO_WEB = 'master_table.html?table=PER_SERVICE' ";
				$db->send_cmd($cmd);
				//$db->show_error();

//				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_servicetitle.html?table=PER_SERVICETITLE' 
//								WHERE LINKTO_WEB = 'master_table_new.html?table=PER_SERVICETITLE' ";
				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_new.html?table=PER_SERVICETITLE' 
								WHERE LINKTO_WEB = 'master_table_servicetitle.html?table=PER_SERVICETITLE' ";
				$db->send_cmd($cmd);
				//$db->show_error();

//				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_skill.html?table=PER_SKILL' 
//								WHERE LINKTO_WEB = 'master_table_new.html?table=PER_SKILL' ";
				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_new.html?table=PER_SKILL' 
								WHERE LINKTO_WEB = 'master_table_skill.html?table=PER_SKILL' ";
				$db->send_cmd($cmd);
				//$db->show_error();

//				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_skill_group.html?table=PER_SKILL_GROUP' 
//								WHERE LINKTO_WEB = 'master_table_new.html?table=PER_SKILL_GROUP' ";
				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_new.html?table=PER_SKILL_GROUP' 
								WHERE LINKTO_WEB = 'master_table_skill_group.html?table=PER_SKILL_GROUP' ";
				$db->send_cmd($cmd);
				//$db->show_error();

//				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_new.html?table=PER_SPECIAL_HOLIDAY' 
//								WHERE LINKTO_WEB = 'master_table_special_holiday.html' ";
//				$db->send_cmd($cmd);
				//$db->show_error();

//				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table.html?table=PER_SPECIAL_SKILLGRP' 
//								WHERE LINKTO_WEB = 'master_table_new.html?table=PER_SPECIAL_SKILLGRP' ";
				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_new.html?table=PER_SPECIAL_SKILLGRP' 
								WHERE LINKTO_WEB = 'master_table.html?table=PER_SPECIAL_SKILLGRP' ";
				$db->send_cmd($cmd);
				//$db->show_error();

//				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table.html?table=PER_STATUS' 
//								WHERE LINKTO_WEB = 'master_table_new.html?table=PER_STATUS' ";
				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_new.html?table=PER_STATUS' 
								WHERE LINKTO_WEB = 'master_table.html?table=PER_STATUS' ";
				$db->send_cmd($cmd);
				//$db->show_error();

//				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_new.html?table=PER_SQL' 
//								WHERE LINKTO_WEB = 'master_table_sql.html?table=PER_SQL' ";
//				$db->send_cmd($cmd);
				//$db->show_error();

//				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_temp_pos_group.html?table=PER_TEMP_POS_GROUP' 
//								WHERE LINKTO_WEB = 'master_table_new.html?table=PER_TEMP_POS_GROUP' ";
				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_new.html?table=PER_TEMP_POS_GROUP' 
								WHERE LINKTO_WEB = 'master_table_temp_pos_group.html?table=PER_TEMP_POS_GROUP' ";
				$db->send_cmd($cmd);
				//$db->show_error();

//				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_temp_pos_name.html?table=PER_TEMP_POS_NAME' 
//								WHERE LINKTO_WEB = 'master_table_new.html?table=PER_TEMP_POS_NAME' ";
				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_new.html?table=PER_TEMP_POS_NAME' 
								WHERE LINKTO_WEB = 'master_table_temp_pos_name.html?table=PER_TEMP_POS_NAME' ";
				$db->send_cmd($cmd);
				//$db->show_error();

//				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table.html?table=PER_TEST_COURSE' 
//								WHERE LINKTO_WEB = 'master_table_new.html?table=PER_TEST_COURSE' ";
				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_new.html?table=PER_TEST_COURSE' 
								WHERE LINKTO_WEB = 'master_table.html?table=PER_TEST_COURSE' ";
				$db->send_cmd($cmd);
				//$db->show_error();

//				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_thaiword.html?table=THAIWORD_FORTHAICUT' 
//								WHERE LINKTO_WEB = 'master_table_new.html?table=THAIWORD_FORTHAICUT&form_part=thaiword' ";
				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_new.html?table=THAIWORD_FORTHAICUT&form_part=thaiword' 
								WHERE LINKTO_WEB = 'master_table_thaiword.html?table=THAIWORD_FORTHAICUT' ";
				$db->send_cmd($cmd);
				//$db->show_error();

//				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_new.html?table=PER_TIME' 
//								WHERE LINKTO_WEB = 'master_table_time.html?table=PER_TIME' ";
//				$db->send_cmd($cmd);
				//$db->show_error();

//				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_train.html?table=PER_TRAIN' 
//								WHERE LINKTO_WEB = 'master_table_new.html?table=PER_TRAIN' ";
				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_new.html?table=PER_TRAIN' 
								WHERE LINKTO_WEB = 'master_table_train.html?table=PER_TRAIN' ";
				$db->send_cmd($cmd);
				//$db->show_error();

//				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_type.html?table=PER_TYPE' 
//								WHERE LINKTO_WEB = 'master_table_new.html?table=PER_TYPE' ";
				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_new.html?table=PER_TYPE' 
								WHERE LINKTO_WEB = 'master_table_type.html?table=PER_TYPE' ";
				$db->send_cmd($cmd);
				//$db->show_error();
/*
				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'PER_PROVINCE' 
								WHERE LINKTO_WEB = 'PER_PROVINCE' ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'PER_PROVINCE' 
								WHERE LINKTO_WEB = 'PER_PROVINCE' ";
				$db->send_cmd($cmd);
				//$db->show_error();
*/
			}

			add_field("PER_ORG", "MG_CODE","VARCHAR", "10", "NULL");
			add_field("PER_ORG", "PG_CODE","VARCHAR", "10", "NULL");
			add_field("PER_ORG_ASS", "MG_CODE","VARCHAR", "10", "NULL");
			add_field("PER_ORG_ASS", "PG_CODE","VARCHAR", "10", "NULL");
			add_field("PER_POSITIONHIS", "POH_SPECIALIST","VARCHAR", "255", "NULL");
			add_field("PER_SALARYHIS", "SAH_SPECIALIST","VARCHAR", "255", "NULL");

			if ($BKK_FLAG != 1) {
				$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'select_empser_excel.html' ";
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
									  VALUES (1, 'TH', $MAX_ID, 17, 'P1117 นำเข้าข้อมูลพนักงานราชการ', 'S', 'W', 'select_empser_excel.html', 0, 35, 251, 
									  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();

					$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
									  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
									  CREATE_BY, UPDATE_DATE, UPDATE_BY)
									  VALUES (1, 'EN', $MAX_ID, 17, 'P1117 นำเข้าข้อมูลพนักงานราชการ', 'S', 'W', 'select_empser_excel.html', 0, 35, 251, 
									  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();
				}
			}

			if ($CTRL_TYPE==4) {
				$cmd = " SELECT POS_ID FROM PER_POSITION a, PER_ORG b 
								  WHERE a.DEPARTMENT_ID = b.ORG_ID AND b.OL_CODE <> '02' ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
				while($data = $db_dpis->get_array()){
					$POS_ID = $data[POS_ID];

					$cmd = " UPDATE PER_POSITION SET DEPARTMENT_ID = $SESS_DEPARTMENT_ID WHERE POS_ID = $POS_ID ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
				} // end while						

				$cmd = " SELECT POEM_ID FROM PER_POS_EMP a, PER_ORG b 
								  WHERE a.DEPARTMENT_ID = b.ORG_ID AND b.OL_CODE <> '02' ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
				while($data = $db_dpis->get_array()){
					$POEM_ID = $data[POEM_ID];

					$cmd = " UPDATE PER_POS_EMP SET DEPARTMENT_ID = $SESS_DEPARTMENT_ID WHERE POEM_ID = $POEM_ID ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
				} // end while						

				$cmd = " SELECT POEMS_ID FROM PER_POS_EMPSER a, PER_ORG b 
								  WHERE a.DEPARTMENT_ID = b.ORG_ID AND b.OL_CODE <> '02' ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
				while($data = $db_dpis->get_array()){
					$POEMS_ID = $data[POEMS_ID];

					$cmd = " UPDATE PER_POS_EMPSER SET DEPARTMENT_ID = $SESS_DEPARTMENT_ID WHERE POEMS_ID = $POEMS_ID ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
				} // end while						

				$cmd = " SELECT POT_ID FROM PER_POS_TEMP a, PER_ORG b 
								  WHERE a.DEPARTMENT_ID = b.ORG_ID AND b.OL_CODE <> '02' ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
				while($data = $db_dpis->get_array()){
					$POT_ID = $data[POT_ID];

					$cmd = " UPDATE PER_POS_TEMP SET DEPARTMENT_ID = $SESS_DEPARTMENT_ID WHERE POT_ID = $POT_ID ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
				} // end while						

				$cmd = " SELECT PER_ID, PER_TYPE, POS_ID, POEM_ID, POEMS_ID, POT_ID FROM PER_PERSONAL a, PER_ORG b 
								  WHERE a.DEPARTMENT_ID = b.ORG_ID AND b.OL_CODE <> '02' ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
				while($data = $db_dpis->get_array()){
					$PER_ID = $data[PER_ID];
					$PER_TYPE = $data[PER_TYPE];
					$POS_ID = $data[POS_ID];
					$POEM_ID = $data[POEM_ID];
					$POEMS_ID = $data[POEMS_ID];
					$POT_ID = $data[POT_ID];

					if ($PER_TYPE==1) 
						$cmd = " SELECT DEPARTMENT_ID FROM PER_POSITION WHERE POS_ID = $POS_ID ";
					elseif ($PER_TYPE==2) 
						$cmd = " SELECT DEPARTMENT_ID FROM PER_POS_EMP WHERE POEM_ID = $POEM_ID ";
					elseif ($PER_TYPE==3) 
						$cmd = " SELECT DEPARTMENT_ID FROM PER_POS_EMPSER WHERE POEMS_ID = $POEMS_ID ";
					elseif ($PER_TYPE==4) 
						$cmd = " SELECT DEPARTMENT_ID FROM PER_POS_TEMP WHERE POT_ID = $POT_ID ";
					$db_dpis1->send_cmd($cmd);
					$data1 = $db_dpis1->get_array();
					$DEPT_ID = $data1[DEPARTMENT_ID];

					$cmd = " UPDATE PER_PERSONAL SET DEPARTMENT_ID = $DEPT_ID WHERE PER_ID = $PER_ID ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
				} // end while	
			}

			add_field("PER_KPI_FORM", "KF_SCORE_STATUS","SINGLE", "1", "NULL");

			if ($KPI_BUDGET_YEAR) {
				if ($KPI_CYCLE==1) $KF_START_DATE = ($KPI_BUDGET_YEAR-544)."-04-01";
				else $KF_START_DATE = ($KPI_BUDGET_YEAR-544)."-10-01";
				$cmd = " UPDATE PER_KPI_FORM SET KF_SCORE_STATUS = 1 WHERE KF_START_DATE <= '$KF_START_DATE' AND KF_SCORE_STATUS IS NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			} else {
				if ($KPI_SCORE_CONFIRM==1) {
					if (substr($UPDATE_DATE,5,2) > "09" || substr($UPDATE_DATE,5,2) < "04") $KF_START_DATE = (substr($UPDATE_DATE,0,4)-1)."-04-01";
					elseif (substr($UPDATE_DATE,5,2) > "03" && substr($UPDATE_DATE,5,2) < "10") $KF_START_DATE = (substr($UPDATE_DATE,0,4)-1)."-10-01";
					$cmd = " UPDATE PER_KPI_FORM SET KF_SCORE_STATUS = 1 WHERE KF_START_DATE <= '$KF_START_DATE' AND KF_SCORE_STATUS IS NULL ";
				} else {
					$cmd = " UPDATE PER_KPI_FORM SET KF_SCORE_STATUS = 1 WHERE KF_SCORE_STATUS IS NULL ";
				}
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			}

			$cmd = " SELECT PMH_ID FROM PER_POS_MGTSALARYHIS ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) {
				if($DPISDB=="odbc") 
					$cmd = " CREATE TABLE PER_POS_MGTSALARYHIS(
					PMH_ID INTEGER NOT NULL,	
					PER_ID INTEGER NOT NULL,	
					PER_CARDNO VARCHAR(13) NULL,
					PMH_EFFECTIVEDATE VARCHAR(19) NOT NULL,
					EX_CODE VARCHAR(10) NOT NULL,	
					PMH_AMT NUMBER NOT NULL,
					PMH_ENDDATE VARCHAR(19) NULL,
					PMH_ACTIVE SINGLE NULL,
					PMH_REMARK MEMO NULL,
					PMH_SEQ_NO INTEGER2 NULL,
					UPDATE_USER INTEGER NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_POS_MGTSALARYHIS PRIMARY KEY (PMH_ID)) ";
				elseif($DPISDB=="oci8") 
					$cmd = " CREATE TABLE PER_POS_MGTSALARYHIS(
					PMH_ID NUMBER(10) NOT NULL,	
					PER_ID NUMBER(10) NOT NULL,	
					PER_CARDNO VARCHAR2(13) NULL,
					PMH_EFFECTIVEDATE VARCHAR2(19) NOT NULL,
					EX_CODE VARCHAR2(10) NOT NULL,	
					PMH_AMT NUMBER(16,2) NOT NULL,
					PMH_ENDDATE VARCHAR2(19) NULL,
					PMH_ACTIVE NUMBER(1) NULL,
					PMH_REMARK VARCHAR2(1000) NULL,
					PMH_SEQ_NO NUMBER(5) NULL,
					UPDATE_USER NUMBER(11) NOT NULL,
					UPDATE_DATE VARCHAR2(19) NOT NULL,		
					CONSTRAINT PK_PER_POS_MGTSALARYHIS PRIMARY KEY (PMH_ID)) ";
				elseif($DPISDB=="mysql")
					$cmd = " CREATE TABLE PER_POS_MGTSALARYHIS(
					PMH_ID INTEGER(10) NOT NULL,	
					PER_ID INTEGER(10) NOT NULL,	
					PER_CARDNO VARCHAR(13) NULL,
					PMH_EFFECTIVEDATE VARCHAR(19) NOT NULL,
					EX_CODE VARCHAR(10) NOT NULL,	
					PMH_AMT DECIMAL(16,2) NOT NULL,
					PMH_ENDDATE VARCHAR(19) NULL,
					PMH_ACTIVE SMALLINT(1) NULL,
					PMH_REMARK TEXT NULL,
					PMH_SEQ_NO SMALLINT(5) NULL,
					UPDATE_USER INTEGER(11) NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_POS_MGTSALARYHIS PRIMARY KEY (PMH_ID)) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			}

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST ALTER CP_RESULT NUMBER ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST MODIFY CP_RESULT NUMBER(16,2) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST MODIFY CP_RESULT DECIMAL(16,2) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if ($SESS_DEPARTMENT_NAME!="เทศบาลนครนนทบุรี") {
				$cmd = " SELECT a.POS_ID, b.PL_CODE_NEW FROM PER_POSITION a, PER_LINE b 
								WHERE a.PL_CODE = b.PL_CODE and substr(a.PL_CODE,1,1) = '0' and b.PL_CODE_NEW is not null and POS_STATUS = 1 ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
				while($data = $db_dpis->get_array()){
					$POS_ID = $data[POS_ID];
					$PL_CODE_NEW = trim($data[PL_CODE_NEW]);

					$cmd = " UPDATE PER_POSITION SET PL_CODE = '$PL_CODE_NEW' WHERE POS_ID = $POS_ID ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
				} // end while
			}

			if ($BKK_FLAG==1) {
				$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'select_database_sarabun.html' ";
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
									  VALUES (1, 'TH', $MAX_ID, 3, 'C03 เลือกประเภทฐานข้อมูลสารบรรณ', 'S', 'W', 'select_database_sarabun.html', 
									  0, 1, $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();

					$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
									  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
									  UPDATE_DATE, UPDATE_BY)
									  VALUES (1, 'EN', $MAX_ID, 3, 'C03 เลือกประเภทฐานข้อมูลสารบรรณ', 'S', 'W', 'select_database_sarabun.html', 
									  0, 1, $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();
				}

				$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'select_bkk_excel.html' ";
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
									  VALUES (1, 'TH', $MAX_ID, 20, 'C20 ปรับปรุงข้อมูลให้เป็นปัจจุบัน', 'S', 'W', 'select_bkk_excel.html', 
									  0, 1, $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();

					$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
									  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
									  UPDATE_DATE, UPDATE_BY)
									  VALUES (1, 'EN', $MAX_ID, 20, 'C20 ปรับปรุงข้อมูลให้เป็นปัจจุบัน', 'S', 'W', 'select_bkk_excel.html', 
									  0, 1, $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();
				}

				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV1 set MENU_LABEL = 'H05 รายชื่อนักบริหารจำแนกตามหน่วยงาน' 
								  WHERE MENU_LABEL = 'H05 รายชื่อนักบริหารจำแนกตามส่วนราชการ' ";
				$count_data = $db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'kpi_org_score.html' ";
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
									  VALUES (1, 'TH', $MAX_ID, 11, 'K11 คะแนนการประเมินผลระดับหน่วยงาน', 'S', 'W', 'kpi_org_score.html', 0, 40, 
									  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();

					$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
									  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
									  UPDATE_DATE, UPDATE_BY)
									  VALUES (1, 'EN', $MAX_ID, 11, 'K11 คะแนนการประเมินผลระดับหน่วยงาน', 'S', 'W', 'kpi_org_score.html', 0, 40, 
									  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();
				} 

				$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'select_database_attendance.html' ";
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
									  VALUES (1, 'TH', $MAX_ID, 1, 'C19 เลือกประเภทฐานข้อมูล MIS / Daily Plans', 'S', 'W', 'select_database_attendance.html', 0, 1, 
									  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();

					$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
									  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
									  UPDATE_DATE, UPDATE_BY)
									  VALUES (1, 'EN', $MAX_ID, 1, 'C19 เลือกประเภทฐานข้อมูล MIS / Daily Plans', 'S', 'W', 'select_database_attendance.html', 0, 1, 
									  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();
				}

			}

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'personal_master.html?SEARCHHIS=personal_scholar' ";
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
								  VALUES (1, 'TH', $MAX_ID, 26, 'P0126 ประวัติการลาศึกษาต่อ', 'S', 'W', 'personal_master.html?SEARCHHIS=personal_scholar', 0, 35, 241, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 26, 'P0126 ประวัติการลาศึกษาต่อ', 'S', 'W', 'personal_master.html?SEARCHHIS=personal_scholar', 0, 35, 241, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'personal_master.html?SEARCHHIS=personal_kpi_form' ";
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
								  VALUES (1, 'TH', $MAX_ID, 27, 'P0127 ประวัติคะแนนผลการประเมินการปฏิบัติราชการ', 'S', 'W', 'personal_master.html?SEARCHHIS=personal_kpi_form', 0, 35, 241, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 27, 'P0127 ประวัติคะแนนผลการประเมินการปฏิบัติราชการ', 'S', 'W', 'personal_master.html?SEARCHHIS=personal_kpi_form', 0, 35, 241, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'personal_master.html?SEARCHHIS=personal_address' ";
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
								  VALUES (1, 'TH', $MAX_ID, 28, 'P0128 ที่อยู่', 'S', 'W', 'personal_master.html?SEARCHHIS=personal_address', 0, 35, 241, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 28, 'P0128 ที่อยู่', 'S', 'W', 'personal_master.html?SEARCHHIS=personal_address', 0, 35, 241, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'personal_master.html?SEARCHHIS=personal_family' ";
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
								  VALUES (1, 'TH', $MAX_ID, 29, 'P0129 ครอบครัว', 'S', 'W', 'personal_master.html?SEARCHHIS=personal_family', 0, 35, 241, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 29, 'P0129 ครอบครัว', 'S', 'W', 'personal_master.html?SEARCHHIS=personal_family', 0, 35, 241, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'personal_master.html?SEARCHHIS=personal_actinghis' ";
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
								  VALUES (1, 'TH', $MAX_ID, 30, 'P0130 ประวัติการรักษาราชการแทน/รักษาการในตำแหน่ง/มอบหมายงาน', 'S', 'W', 'personal_master.html?SEARCHHIS=personal_actinghis', 0, 35, 241, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 30, 'P0130 ประวัติการรักษาราชการแทน/รักษาการในตำแหน่ง/มอบหมายงาน', 'S', 'W', 'personal_master.html?SEARCHHIS=personal_actinghis', 0, 35, 241, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'personal_master.html?SEARCHHIS=personal_pichist' ";
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
								  VALUES (1, 'TH', $MAX_ID, 31, 'P0131 ภาพถ่าย/ลายเซ็น', 'S', 'W', 'personal_master.html?SEARCHHIS=personal_pichist', 0, 35, 241, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 31, 'P0131 ภาพถ่าย/ลายเซ็น', 'S', 'W', 'personal_master.html?SEARCHHIS=personal_pichist', 0, 35, 241, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'personal_master.html?SEARCHHIS=personal_holidayhis' ";
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
								  VALUES (1, 'TH', $MAX_ID, 32, 'P0132 วันหยุด (พิเศษ)', 'S', 'W', 'personal_master.html?SEARCHHIS=personal_holidayhis', 0, 35, 241, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 32, 'P0132 วันหยุด (พิเศษ)', 'S', 'W', 'personal_master.html?SEARCHHIS=personal_holidayhis', 0, 35, 241, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'personal_master.html?SEARCHHIS=personal_attachment' ";
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
								  VALUES (1, 'TH', $MAX_ID, 33, 'P0133 เอกสารหลักฐาน', 'S', 'W', 'personal_master.html?SEARCHHIS=personal_attachment', 0, 35, 241, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 33, 'P0133 เอกสารหลักฐาน', 'S', 'W', 'personal_master.html?SEARCHHIS=personal_attachment', 0, 35, 241, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'personal_master.html?SEARCHHIS=personal_licensehis' ";
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
								  VALUES (1, 'TH', $MAX_ID, 34, 'P0134 ประวัติใบอนุญาตประกอบวิชาชีพ', 'S', 'W', 'personal_master.html?SEARCHHIS=personal_licensehis', 0, 35, 241, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 34, 'P0134 ประวัติใบอนุญาตประกอบวิชาชีพ', 'S', 'W', 'personal_master.html?SEARCHHIS=personal_licensehis', 0, 35, 241, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'personal_master.html?SEARCHHIS=personal_approve_resolution' ";
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
								  VALUES (1, 'TH', $MAX_ID, 35, 'P0135 มติอนุมัติ อนุญาตต่าง ๆ', 'S', 'W', 'personal_master.html?SEARCHHIS=personal_approve_resolution', 0, 35, 241, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 35, 'P0135 มติอนุมัติ อนุญาตต่าง ๆ', 'S', 'W', 'personal_master.html?SEARCHHIS=personal_approve_resolution', 0, 35, 241, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'personal_master.html?SEARCHHIS=personal_excellent_performance' ";
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
								  VALUES (1, 'TH', $MAX_ID, 36, 'P0136 ผลงานดีเด่น', 'S', 'W', 'personal_master.html?SEARCHHIS=personal_excellent_performance', 0, 35, 241, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 36, 'P0136 ผลงานดีเด่น', 'S', 'W', 'personal_master.html?SEARCHHIS=personal_excellent_performance', 0, 35, 241, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'personal_master.html?SEARCHHIS=personal_soldierhis' ";
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
								  VALUES (1, 'TH', $MAX_ID, 37, 'P0137 ประวัติการรับราชการทหาร', 'S', 'W', 'personal_master.html?SEARCHHIS=personal_soldierhis', 0, 35, 241, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 37 'P0137 ประวัติการรับราชการทหาร', 'S', 'W', 'personal_master.html?SEARCHHIS=personal_soldierhis', 0, 35, 241, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'personal_master.html?SEARCHHIS=personal_other_occupation' ";
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
								  VALUES (1, 'TH', $MAX_ID, 38, 'P0138 ประวัติการไปประกอบอาชีพอื่น', 'S', 'W', 'personal_master.html?SEARCHHIS=personal_other_occupation', 0, 35, 241, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 38, 'P0138 ประวัติการไปประกอบอาชีพอื่น', 'S', 'W', 'personal_master.html?SEARCHHIS=personal_other_occupation', 0, 35, 241, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'personal_master.html?SEARCHHIS=personal_test_coursehis' ";
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
								  VALUES (1, 'TH', $MAX_ID, 39, 'P0139 ประวัติการสอบ', 'S', 'W', 'personal_master.html?SEARCHHIS=personal_test_coursehis', 0, 35, 241, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 39, 'P0139 ประวัติการสอบ', 'S', 'W', 'personal_master.html?SEARCHHIS=personal_test_coursehis', 0, 35, 241, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'personal_master.html?SEARCHHIS=personal_birthdate' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'personal_master.html?SEARCHHIS=personal_birthdate_change' ";
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
								  VALUES (1, 'TH', $MAX_ID, 40, 'P0140 ประวัติการแก้ไขวันเดือนปีเกิด', 'S', 'W', 'personal_master.html?SEARCHHIS=personal_birthdate_change', 0, 35, 241, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 40, 'P0140 ประวัติการแก้ไขวันเดือนปีเกิด', 'S', 'W', 'personal_master.html?SEARCHHIS=personal_birthdate_change', 0, 35, 241, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'personal_master.html?SEARCHHIS=personal_salaryslip' ";
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
								  VALUES (1, 'TH', $MAX_ID, 41, 'P0141 สลิปเงินเดือน', 'S', 'W', 'personal_master.html?SEARCHHIS=personal_salaryslip', 0, 35, 241, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 41, 'P0141 สลิปเงินเดือน', 'S', 'W', 'personal_master.html?SEARCHHIS=personal_salaryslip', 0, 35, 241, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'personal_master.html?SEARCHHIS=personal_kp7_borrow' ";
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
								  VALUES (1, 'TH', $MAX_ID, 42, 'P0142 ประวัติการยืม ก.พ.7', 'S', 'W', 'personal_master.html?SEARCHHIS=personal_kp7_borrow', 0, 35, 241, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 42, 'P0142 ประวัติการยืม ก.พ.7', 'S', 'W', 'personal_master.html?SEARCHHIS=personal_kp7_borrow', 0, 35, 241, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'personal_master.html?SEARCHHIS=personal_retire' ";
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
								  VALUES (1, 'TH', $MAX_ID, 43, 'P0143 $RETIRE_TITLE', 'S', 'W', 'personal_master.html?SEARCHHIS=personal_retire', 0, 35, 241, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 43, 'P0143 $RETIRE_TITLE', 'S', 'W', 'personal_master.html?SEARCHHIS=personal_retire', 0, 35, 241, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			if ($MFA_FLAG==1) {
				$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'personal_master.html?SEARCHHIS=personal_posting' ";
				$count_data = $db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'personal_master.html?SEARCHHIS=personal_postinghis' ";
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
									  VALUES (1, 'TH', $MAX_ID, 44, 'P0144 ประวัติการออกประจำการ', 'S', 'W', 'personal_master.html?SEARCHHIS=personal_postinghis', 0, 35, 241, 
									  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();

					$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
									  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
									  CREATE_BY, UPDATE_DATE, UPDATE_BY)
									  VALUES (1, 'EN', $MAX_ID, 44, 'P0144 ประวัติการออกประจำการ', 'S', 'W', 'personal_master.html?SEARCHHIS=personal_postinghis', 0, 35, 241, 
									  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();
				}
			}

/*
			$cmd = " SELECT DEPARTMENT_ID FROM PER_ASSESS_MAIN ";
			$count_data = $db_dpis->send_cmd($cmd);

			if (!$count_data) {
				$cmd = " ALTER TABLE PER_ASSESS_MAIN DROP CONSTRAINT PK_PER_ASSESS_MAIN ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " DROP INDEX PK_PER_ASSESS_MAIN ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				add_field("PER_ASSESS_MAIN", "AM_YEAR","VARCHAR", "4", "NULL");
				add_field("PER_ASSESS_MAIN", "AM_CYCLE","SINGLE", "1", "NULL");

				$cmd = " SELECT DISTINCT SUBSTR(CP_END_DATE,1,4) as CP_YEAR, CP_CYCLE, PER_TYPE FROM PER_COMPENSATION_TEST 
								WHERE SUBSTR(CP_END_DATE,1,4) != '2012' and CP_CYCLE != 2 ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
				while($data = $db_dpis->get_array()){
					$CP_YEAR = trim($data[CP_YEAR]) + 543;
					$CP_CYCLE = $data[CP_CYCLE];
					$PER_TYPE = $data[PER_TYPE];

					$cmd = " SELECT AM_CODE, AM_NAME, AM_POINT_MIN, AM_POINT_MAX, AM_ACTIVE, UPDATE_USER, UPDATE_DATE, AM_SHOW
									FROM PER_ASSESS_MAIN 
									WHERE PER_TYPE = $PER_TYPE AND AM_YEAR IS NULL AND AM_CYCLE IS NULL ";
					$db_dpis1->send_cmd($cmd);
					while($data_dpis1 = $db_dpis1->get_array()) {
						$AM_CODE = trim($data_dpis1[AM_CODE]);
						$AM_NAME = trim($data_dpis1[AM_NAME]);
						$AM_POINT_MIN = $data_dpis1[AM_POINT_MIN];
						$AM_POINT_MAX = $data_dpis1[AM_POINT_MAX];
						$AM_ACTIVE = $data_dpis1[AM_ACTIVE];
						$UPDATE_USER = $data_dpis1[UPDATE_USER];
						$UPDATE_DATE = trim($data_dpis1[UPDATE_DATE]);
						$AM_SHOW = $data_dpis1[AM_SHOW];
						if (!$AM_POINT_MIN) $AM_POINT_MIN = "NULL";
						if (!$AM_POINT_MAX) $AM_POINT_MAX = "NULL";
						if (!$AM_SHOW) $AM_SHOW = "NULL";

						$cmd = " insert into PER_ASSESS_MAIN (AM_YEAR, AM_CYCLE, AM_CODE, AM_NAME, AM_POINT_MIN, AM_POINT_MAX, 
										AM_ACTIVE, UPDATE_USER, UPDATE_DATE, AM_SHOW, PER_TYPE) 
										values ('$CP_YEAR', $CP_CYCLE, '$AM_CODE', '$AM_NAME', $AM_POINT_MIN, $AM_POINT_MAX, $AM_ACTIVE, 
										$UPDATE_USER, '$UPDATE_DATE', $AM_SHOW, $PER_TYPE) ";
						$db_dpis2->send_cmd($cmd);
						//$db_dpis2->show_error();
					} // end while						
				} // end while						

				$cmd = " update PER_ASSESS_MAIN set AM_YEAR = '2555', AM_CYCLE = 2 where AM_YEAR is NULL and AM_CYCLE is NULL ";
				$db_dpis2->send_cmd($cmd);
				//$db_dpis2->show_error();

				$cmd = " delete from PER_ASSESS_MAIN where AM_YEAR is NULL and AM_CYCLE is NULL ";
				$db_dpis2->send_cmd($cmd);
				//$db_dpis2->show_error();

				if($DPISDB=="oci8") {
					$cmd = " ALTER TABLE PER_ASSESS_MAIN MODIFY AM_YEAR NOT NULL ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();

					$cmd = " ALTER TABLE PER_ASSESS_MAIN MODIFY AM_CYCLE NOT NULL ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();

					$cmd = " ALTER TABLE PER_ASSESS_MAIN ADD CONSTRAINT PK_PER_ASSESS_MAIN PRIMARY KEY 
									  (AM_YEAR, AM_CYCLE, AM_CODE) ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				}
			}
*/
			if ($BKK_FLAG==1) {
/*				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'convert_pic_bkk.html' WHERE LINKTO_WEB = 'convert_pic_new.html' ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " UPDATE PER_COMTYPE SET COM_DESC = REPLACE(COM_DESC, 'ข้าราชการพลเรือน', 'ข้าราชการกรุงเทพมหานคร') 
								  WHERE COM_DESC LIKE '%ข้าราชการพลเรือน%' ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				add_field("POS_DES_INFO", "POS_DES_ACTIVE","SINGLE", "1", "NULL");
				add_field("POS_DES_INFO", "UPDATE_USER","INTEGER", "11", "NULL");
				add_field("POS_DES_INFO", "UPDATE_DATE","VARCHAR", "19", "NULL");
				add_field("KNOWLEDGE_INFO", "UPDATE_USER","INTEGER", "11", "NULL");
				add_field("KNOWLEDGE_INFO", "UPDATE_DATE","VARCHAR", "19", "NULL");
				add_field("KNOWLEDGE_LEVEL", "UPDATE_USER","INTEGER", "11", "NULL");
				add_field("KNOWLEDGE_LEVEL", "UPDATE_DATE","VARCHAR", "19", "NULL");
				add_field("SKILL_INFO", "UPDATE_USER","INTEGER", "11", "NULL");
				add_field("SKILL_INFO", "UPDATE_DATE","VARCHAR", "19", "NULL");
				add_field("SKILL_LEVEL", "UPDATE_USER","INTEGER", "11", "NULL");
				add_field("SKILL_LEVEL", "UPDATE_DATE","VARCHAR", "19", "NULL");
				add_field("EXP_INFO", "UPDATE_USER","INTEGER", "11", "NULL");
				add_field("EXP_INFO", "UPDATE_DATE","VARCHAR", "19", "NULL");
				add_field("COMPETENCY_INFO", "UPDATE_USER","INTEGER", "11", "NULL");
				add_field("COMPETENCY_INFO", "UPDATE_DATE","VARCHAR", "19", "NULL");
				add_field("COMPETENCY_LEVEL", "UPDATE_USER","INTEGER", "11", "NULL");
				add_field("COMPETENCY_LEVEL", "UPDATE_DATE","VARCHAR", "19", "NULL");
				add_field("CONFIG_WORKFLOW", "UPDATE_USER","INTEGER", "11", "NULL");
				add_field("CONFIG_WORKFLOW", "UPDATE_DATE","VARCHAR", "19", "NULL");
				add_field("CONFIG_JOB_EVALUATION", "UPDATE_USER","INTEGER", "11", "NULL");
				add_field("CONFIG_JOB_EVALUATION", "UPDATE_DATE","VARCHAR", "19", "NULL");
				add_field("ACCOUNTABILITY_LEVEL_TYPE", "UPDATE_USER","INTEGER", "11", "NULL");
				add_field("ACCOUNTABILITY_LEVEL_TYPE", "UPDATE_DATE","VARCHAR", "19", "NULL");
				add_field("ACCOUNTABILITY_TYPE", "UPDATE_USER","INTEGER", "11", "NULL");
				add_field("ACCOUNTABILITY_TYPE", "UPDATE_DATE","VARCHAR", "19", "NULL");
				add_field("ACCOUNTABILITY_INFO_PRIMARY", "UPDATE_USER","INTEGER", "11", "NULL");
				add_field("ACCOUNTABILITY_INFO_PRIMARY", "UPDATE_DATE","VARCHAR", "19", "NULL");
*/
			}

			if($db_type=="oci8")
				$cmd = " ALTER TABLE USER_GROUP MODIFY CODE VARCHAR2(50) ";
			else
				$cmd = " ALTER TABLE USER_GROUP ALTER CODE VARCHAR(50) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			if($db_type=="oci8")
				$cmd = " ALTER TABLE USER_GROUP MODIFY NAME_TH VARCHAR2(255) ";
			else
				$cmd = " ALTER TABLE USER_GROUP ALTER NAME_TH VARCHAR(255) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			if($db_type=="oci8")
				$cmd = " ALTER TABLE USER_GROUP MODIFY NAME_EN VARCHAR2(255) ";
			else
				$cmd = " ALTER TABLE USER_GROUP ALTER NAME_EN VARCHAR(255) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			add_field("PER_ADDRESS", "DT_CODE","VARCHAR", "10", "NULL");
			add_field("PER_ABSENT", "AUDIT_FLAG","SINGLE", "1", "NULL");
			add_field("PER_ABSENT", "AUDIT_PER_ID","INTEGER", "10", "NULL");
			add_field("PER_ABSENT", "REVIEW1_FLAG","SINGLE", "1", "NULL");
			add_field("PER_ABSENT", "REVIEW1_PER_ID","INTEGER", "10", "NULL");
			add_field("PER_ABSENT", "REVIEW2_FLAG","SINGLE", "1", "NULL");
			add_field("PER_ABSENT", "REVIEW2_PER_ID","INTEGER", "10", "NULL");
			add_field("PER_ABSENT", "CANCEL_FLAG","SINGLE", "1", "NULL");

			$cmd = " DELETE FROM PER_PERFORMANCE_GOALS WHERE KF_ID NOT IN (SELECT KF_ID FROM PER_KPI_FORM) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DELETE FROM PER_KPI_COMPETENCE WHERE KF_ID NOT IN (SELECT KF_ID FROM PER_KPI_FORM) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DELETE FROM PER_IPIP WHERE KF_ID NOT IN (SELECT KF_ID FROM PER_KPI_FORM) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_POSITIONHIS", "POH_REF_DOC","VARCHAR", "255", "NULL");
			add_field("PER_SALARYHIS", "SAH_REF_DOC","VARCHAR", "255", "NULL");

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_POSITIONHIS ALTER POH_POS_NO NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POSITIONHIS MODIFY POH_POS_NO NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_POSITIONHIS MODIFY POH_POS_NO NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " SELECT BC_ID FROM PER_BIRTHDATE_CHANGE ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) {
				if($DPISDB=="odbc") 
					$cmd = " CREATE TABLE PER_BIRTHDATE_CHANGE(
					BC_ID INTEGER NOT NULL,	
					PER_ID INTEGER NOT NULL,	
					PER_CARDNO VARCHAR(13) NULL,
					PER_BIRTHDATE VARCHAR(19) NOT NULL,
					PER_BIRTHDATE_NEW VARCHAR(19) NOT NULL,
					BC_BOOK_NO VARCHAR(255) NULL,	
					BC_BOOK_DATE VARCHAR(19) NULL,
					BC_REMARK MEMO NULL,
					AUDIT_FLAG CHAR(1) NULL,
					BC_APPROVE_FLAG SINGLE NULL,
					BC_SEQ_NO INTEGER2 NULL,
					UPDATE_USER INTEGER NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_BIRTHDATE_CHANGE PRIMARY KEY (BC_ID)) ";
				elseif($DPISDB=="oci8") 
					$cmd = " CREATE TABLE PER_BIRTHDATE_CHANGE(
					BC_ID NUMBER(10) NOT NULL,	
					PER_ID NUMBER(10) NOT NULL,	
					PER_CARDNO VARCHAR2(13) NULL,
					PER_BIRTHDATE VARCHAR2(19) NOT NULL,
					PER_BIRTHDATE_NEW VARCHAR2(19) NOT NULL,
					BC_BOOK_NO VARCHAR2(255) NULL,	
					BC_BOOK_DATE VARCHAR2(19) NULL,
					BC_REMARK VARCHAR2(1000) NULL,
					AUDIT_FLAG CHAR(1) NULL,
					BC_APPROVE_FLAG NUMBER(1) NULL,
					BC_SEQ_NO NUMBER(5) NULL,
					UPDATE_USER NUMBER(11) NOT NULL,
					UPDATE_DATE VARCHAR2(19) NOT NULL,		
					CONSTRAINT PK_PER_BIRTHDATE_CHANGE PRIMARY KEY (BC_ID)) ";
				elseif($DPISDB=="mysql")
					$cmd = " CREATE TABLE PER_BIRTHDATE_CHANGE(
					BC_ID INTEGER(10) NOT NULL,	
					PER_ID INTEGER(10) NOT NULL,	
					PER_CARDNO VARCHAR(13) NULL,
					PER_BIRTHDATE VARCHAR(19) NOT NULL,
					PER_BIRTHDATE_NEW VARCHAR(19) NOT NULL,
					BC_BOOK_NO VARCHAR(255) NULL,	
					BC_BOOK_DATE VARCHAR(19) NULL,
					BC_REMARK TEXT NULL,
					AUDIT_FLAG CHAR(1) NULL,
					BC_APPROVE_FLAG SMALLINT(1) NULL,
					BC_SEQ_NO SMALLINT(5) NULL,
					UPDATE_USER INTEGER(11) NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_BIRTHDATE_CHANGE PRIMARY KEY (BC_ID)) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			}

			$cmd = " SELECT KB_ID FROM PER_KP7_BORROW ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) {
				if($DPISDB=="odbc") 
					$cmd = " CREATE TABLE PER_KP7_BORROW(
					KB_ID INTEGER NOT NULL,	
					PER_ID INTEGER NOT NULL,	
					PER_CARDNO VARCHAR(13) NULL,
					KB_BORROWDATE VARCHAR(19) NULL,
					KB_RETURNDATE VARCHAR(19) NULL,
					KB_NAME VARCHAR(255) NOT NULL,	
					KB_OBJECTIVE MEMO NULL,
					KB_REMARK MEMO NULL,
					AUDIT_FLAG CHAR(1) NULL,
					KB_SEQ_NO INTEGER2 NULL,
					UPDATE_USER INTEGER NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_KP7_BORROW PRIMARY KEY (KB_ID)) ";
				elseif($DPISDB=="oci8") 
					$cmd = " CREATE TABLE PER_KP7_BORROW(
					KB_ID NUMBER(10) NOT NULL,	
					PER_ID NUMBER(10) NOT NULL,	
					PER_CARDNO VARCHAR2(13) NULL,
					KB_BORROWDATE VARCHAR2(19) NULL,
					KB_RETURNDATE VARCHAR2(19) NULL,
					KB_NAME VARCHAR2(255) NOT NULL,	
					KB_OBJECTIVE VARCHAR2(1000) NULL,
					KB_REMARK VARCHAR2(1000) NULL,
					AUDIT_FLAG CHAR(1) NULL,
					KB_SEQ_NO NUMBER(5) NULL,
					UPDATE_USER NUMBER(11) NOT NULL,
					UPDATE_DATE VARCHAR2(19) NOT NULL,		
					CONSTRAINT PK_PER_KP7_BORROW PRIMARY KEY (KB_ID)) ";
				elseif($DPISDB=="mysql")
					$cmd = " CREATE TABLE PER_KP7_BORROW(
					KB_ID INTEGER(10) NOT NULL,	
					PER_ID INTEGER(10) NOT NULL,	
					PER_CARDNO VARCHAR(13) NULL,
					KB_BORROWDATE VARCHAR(19) NULL,
					KB_RETURNDATE VARCHAR(19) NULL,
					KB_NAME VARCHAR(255) NOT NULL,	
					KB_OBJECTIVE TEXT NULL,
					KB_REMARK TEXT NULL,
					AUDIT_FLAG CHAR(1) NULL,
					KB_SEQ_NO SMALLINT(5) NULL,
					UPDATE_USER INTEGER(11) NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_PER_KP7_BORROW PRIMARY KEY (KB_ID)) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			}

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'rpt_R010001.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'rpt_R010002.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'rpt_R010003.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'rpt_R010004.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET MENU_LABEL = 'P0601 การลา' WHERE MENU_LABEL = 'P0601 การลา/สาย/ขาด' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'data_absent_approve.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			add_field("PER_POSITION", "PPT_CODE","VARCHAR", "10", "NULL");
			add_field("PER_POSITION", "POS_RETIRE","VARCHAR", "100", "NULL");
			add_field("PER_POSITION", "POS_RESERVE","VARCHAR", "100", "NULL");
			add_field("PER_POSITION", "POS_RESERVE_DESC","VARCHAR", "100", "NULL");
			add_field("PER_POSITION", "POS_RESERVE_DOCNO","VARCHAR", "100", "NULL");
			add_field("PER_POSITION", "POS_RETIRE_REMARK","VARCHAR", "100", "NULL");
			add_field("PER_LINE", "PL_CP_CODE","VARCHAR", "255", "NULL");
			//$db_dpis->show_error();

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'map_per_co_level.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'map_per_formula.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'map_per_line.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'map_per_position.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'map_per_type.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'master_table.html?table=PER_GROUP_N' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'master_table_layer_n.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'master_table_line_n.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'master_table_type_n.html?table=PER_TYPE_N' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'rpt_map_per_formula.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'rpt_map_per_position.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'rpt_map_per_type.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV0 WHERE MENU_LABEL = 'ตารางเทียบเคียง' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV0 WHERE MENU_LABEL = 'ประเภทตำแหน่ง/สายงาน (ใหม่)' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV0 WHERE MENU_LABEL = 'รายงานสรุป' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('022', 'เงินประจำตำแหน่ง (ตำแหน่งประเภทบริหาร ระดับต้น)', NULL, NULL, 10000, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('202', 'เงินค่าตอบแทนนอกเหนือจากเงินเดือน (ตำแหน่งประเภทบริหาร ระดับต้น)', NULL, NULL, 10000, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_SALARYHIS", "SAH_DOCNO_EDIT","VARCHAR", "255", "NULL");
			add_field("PER_SALARYHIS", "SAH_DOCDATE_EDIT","VARCHAR", "19", "NULL");

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_KPI ALTER KPI_WEIGHT INTEGER ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_KPI MODIFY KPI_WEIGHT NUMBER(10) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_KPI ALTER KPI_WEIGHT INTEGER(10) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="oci8") {
				$cmd = " ALTER TABLE PER_KPI MODIFY KPI_DEFINE VARCHAR2(4000) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			}

			add_field("PER_ABSENT", "SENDMAIL_FLAG","SINGLE", "1", "NULL");

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_PROJECT ALTER PJ_OBJECTIVE MEMO ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PROJECT MODIFY PJ_OBJECTIVE VARCHAR2(4000) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_PROJECT ALTER PJ_OBJECTIVE TEXT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_PROJECT ALTER PJ_TARGET MEMO ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PROJECT MODIFY PJ_TARGET VARCHAR2(4000) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_PROJECT ALTER PJ_TARGET TEXT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_COMDTL", "CMD_ORG_ASS3","VARCHAR", "100", "NULL");
			add_field("PER_COMDTL", "CMD_ORG_ASS4","VARCHAR", "100", "NULL");
			add_field("PER_COMDTL", "CMD_ORG_ASS5","VARCHAR", "100", "NULL");
			add_field("PER_COMDTL", "CMD_ORG_ASS6","VARCHAR", "100", "NULL");
			add_field("PER_COMDTL", "CMD_ORG_ASS7","VARCHAR", "100", "NULL");
			add_field("PER_COMDTL", "CMD_ORG_ASS8","VARCHAR", "100", "NULL");

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_KPI_FORM ALTER TOTAL_SCORE NUMBER ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_KPI_FORM MODIFY TOTAL_SCORE NUMBER(6,3) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_KPI_FORM MODIFY TOTAL_SCORE DECIMAL(6,3) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_MOVE_REQ", "DEPARTMENT_ID_1","INTEGER", "10", "NULL");
			add_field("PER_MOVE_REQ", "DEPARTMENT_ID_2","INTEGER", "10", "NULL");
			add_field("PER_MOVE_REQ", "DEPARTMENT_ID_3","INTEGER", "10", "NULL");
			add_field("PER_MOVE_REQ", "POS_NO_NAME_1","VARCHAR", "15", "NULL");
			add_field("PER_MOVE_REQ", "POS_NO_1","VARCHAR", "15", "NULL");
			add_field("PER_MOVE_REQ", "POS_NO_NAME_2","VARCHAR", "15", "NULL");
			add_field("PER_MOVE_REQ", "POS_NO_2","VARCHAR", "15", "NULL");
			add_field("PER_MOVE_REQ", "POS_NO_NAME_3","VARCHAR", "15", "NULL");
			add_field("PER_MOVE_REQ", "POS_NO_3","VARCHAR", "15", "NULL");

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV1 SET LINKTO_WEB = 'rpt_R010013.html' WHERE LINKTO_WEB = 'rpt_R010.html?report=rpt_R010013' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'kpi_kpi_inquire.html' ";
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
								  VALUES (1, 'TH', $MAX_ID, 14, 'K14 ค้นหายุทธศาตร์ และตัวชี้วัด', 'S', 'W', 'kpi_kpi_inquire.html', 0, 40, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
								  UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 14, 'K14 ค้นหายุทธศาตร์ และตัวชี้วัด', 'S', 'W', 'kpi_kpi_inquire.html', 0, 40, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			} 

			add_field("PER_PERFORMANCE_GOALS", "KF_SCORE_FLAG","SINGLE", "1", "NULL");

			$cmd = " UPDATE PER_PERFORMANCE_GOALS SET KF_SCORE_FLAG = 1 WHERE KF_SCORE_FLAG IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_PERFORMANCE_GOALS", "KPI_OTHER","MEMO", "2000", "NULL");

			$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, 
							  UPDATE_DATE, MOV_SUB_TYPE)
							  VALUES ('24010', 'เลื่อนขั้นค่าจ้าง รอบที่ 1 (0.5 ขั้น)', 2, 1, $SESS_USERID, '$UPDATE_DATE', 4) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, 
							  UPDATE_DATE, MOV_SUB_TYPE)
							  VALUES ('24020', 'เลื่อนขั้นค่าจ้าง รอบที่ 1 (1 ขั้น)', 2, 1, $SESS_USERID, '$UPDATE_DATE', 4) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, 
							  UPDATE_DATE, MOV_SUB_TYPE)
							  VALUES ('24030', 'เลื่อนขั้นค่าจ้าง รอบที่ 2 (1 ขั้น)', 2, 1, $SESS_USERID, '$UPDATE_DATE', 4) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, 
							  UPDATE_DATE, MOV_SUB_TYPE)
							  VALUES ('24040', 'เลื่อนขั้นค่าจ้าง รอบที่ 2 (1.5 ขั้น)', 2, 1, $SESS_USERID, '$UPDATE_DATE', 4) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, 
							  UPDATE_DATE, MOV_SUB_TYPE)
							  VALUES ('24050', 'เลื่อนขั้นค่าจ้าง รอบที่ 2 (0.5 ขั้น)', 2, 1, $SESS_USERID, '$UPDATE_DATE', 4) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_SERVICEHIS DROP CONSTRAINT FK2_PER_SERVICEHIS ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_SERVICE ALTER SV_CODE CHAR(10) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SERVICE MODIFY SV_CODE CHAR(10) ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_SERVICE MODIFY SV_CODE CHAR(10) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_SERVICETITLE ALTER SV_CODE CHAR(10) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SERVICETITLE MODIFY SV_CODE CHAR(10) ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_SERVICETITLE MODIFY SV_CODE CHAR(10) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_SERVICEHIS ALTER SV_CODE CHAR(10) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SERVICEHIS MODIFY SV_CODE CHAR(10) ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_SERVICEHIS MODIFY SV_CODE CHAR(10) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_SERVICEHIS ADD CONSTRAINT FK2_PER_SERVICEHIS 
							  FOREIGN KEY (SV_CODE) REFERENCES PER_SERVICE (SV_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " select config_name from system_config where config_id = 58 ";
			$count_data = $db->send_cmd($cmd);
			if ($count_data)
				$cmd = " update system_config set config_value = '1', 
									 where config_id = 58 and config_value is null ";
			else
				$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
								values (58, 'SLIP_DISPLAY', '1', 'รูปแบบสลิปเงินเดือน') ";
			$db->send_cmd($cmd);
			
			if ($SESS_DEPARTMENT_NAME=="กรมชลประทาน") {
				$cmd = " SELECT DCL_ID FROM PER_DISCIPLINE ";
				$count_data = $db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
				if (!$count_data) {
					$cmd = " CREATE TABLE PER_DISCIPLINE(
					DCL_ID NUMBER(10) NOT NULL,	
					DCL_RECEIVE_DATE VARCHAR2(19) NOT NULL,
					DCL_NO VARCHAR2(10) NOT NULL,
					DCL_DESC VARCHAR2(1000) NULL,
					DCL_DOC_DESC VARCHAR2(255) NULL,
					DCL_DOC_NO VARCHAR2(100) NULL,
					DCL_DOC_DATE VARCHAR2(19) NULL,
					DCL_MINISTRY_RESULT VARCHAR2(1000) NULL,
					DCL_OCSC_RESULT VARCHAR2(1000) NULL,
					DCL_COLLECT VARCHAR2(1000) NULL,
					DCL_REMARK VARCHAR2(1000) NULL,
					DEPARTMENT_ID NUMBER(10) NULL,	
					UPDATE_USER NUMBER(11) NOT NULL,
					UPDATE_DATE VARCHAR2(19) NOT NULL,		
					CONSTRAINT PK_PER_DISCIPLINE PRIMARY KEY (DCL_ID)) ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				}

				$cmd = " SELECT DCL_ID FROM PER_DISCIPLINE_PER ";
				$count_data = $db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
				if (!$count_data) {
					$cmd = " CREATE TABLE PER_DISCIPLINE_PER(
					DCL_ID NUMBER(10) NOT NULL,	
					PER_ID NUMBER(10) NOT NULL,	
					PER_CARDNO VARCHAR2(13) NULL,
					DCL_PL_NAME VARCHAR2(255) NULL,
					DCL_PM_NAME VARCHAR2(255) NULL,
					LEVEL_NO VARCHAR2(10) NULL,
					DCL_POS_NO VARCHAR2(15) NULL,
					DCL_ORG1 VARCHAR2(255) NULL,
					DCL_ORG2 VARCHAR2(255) NULL,
					DCL_ORG3 VARCHAR2(255) NULL,
					DCL_ORG4 VARCHAR2(255) NULL,
					DCL_ORG5 VARCHAR2(255) NULL,
					PEN_CODE VARCHAR2(10) NULL,
					UPDATE_USER NUMBER(11) NOT NULL,
					UPDATE_DATE VARCHAR2(19) NOT NULL,		
					CONSTRAINT PK_PER_DISCIPLINE_PER PRIMARY KEY (DCL_ID, PER_ID)) ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				}

				$cmd = " CREATE INDEX IDX_PER_DISCIPLINE_PER ON PER_DISCIPLINE_PER (PER_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " SELECT DCL_ID FROM PER_DISCIPLINE_DTL ";
				$count_data = $db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
				if (!$count_data) {
					$cmd = " CREATE TABLE PER_DISCIPLINE_DTL(
					DD_ID NUMBER(10) NOT NULL,	
					DCL_ID NUMBER(10) NOT NULL,	
					DD_TYPE NUMBER(2) NOT NULL,	
					DD_ASS_DATE VARCHAR2(19) NULL,
					DD_PER_ID NUMBER(10) NOT NULL,	
					DD_RECOMMEND_DATE VARCHAR2(19) NULL,
					DD_OUT_DATE VARCHAR2(19) NULL,
					DD_BACK_DATE VARCHAR2(19) NULL,
					DD_DOC_DESC VARCHAR2(255) NULL,
					DD_DOC_NO VARCHAR2(100) NULL,
					DD_DOC_DATE VARCHAR2(19) NULL,
					DD_REMARK VARCHAR2(1000) NULL,
					UPDATE_USER NUMBER(11) NOT NULL,
					UPDATE_DATE VARCHAR2(19) NOT NULL,		
					CONSTRAINT PK_PER_DISCIPLINE_DTL PRIMARY KEY (DD_ID)) ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				}

				$cmd = " CREATE INDEX IDX_PER_DISCIPLINE_DTL ON PER_DISCIPLINE_DTL (DCL_ID, DD_TYPE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " CREATE INDEX IDX_PER_DISCIPLINE_DTL_1 ON PER_DISCIPLINE_DTL (DD_PER_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'personal_discipline.html' ";
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
									  VALUES (1, 'TH', $MAX_ID, 3, 'P0703 การดำเนินการทางวินัย', 'S', 'W', 'personal_discipline.html', 0, 35, 247, $CREATE_DATE, 
									  $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();

					$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
									  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
									  CREATE_BY, UPDATE_DATE, UPDATE_BY)
									  VALUES (1, 'EN', $MAX_ID, 3, 'P0703 การดำเนินการทางวินัย', 'S', 'W', 'personal_discipline.html', 0, 35, 247, $CREATE_DATE, 
									  $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();
				}

				$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'master_table.html?table=PER_POS_RESERVE' ";
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
									  VALUES (1, 'TH', $MAX_ID, 5, 'M0205 สงวนตำแหน่ง', 'S', 'W', 'master_table.html?table=PER_POS_RESERVE', 0, 9, 302, 
									  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();

					$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
									  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
									  CREATE_BY, UPDATE_DATE, UPDATE_BY)
									  VALUES (1, 'EN', $MAX_ID, 5, 'M0205 สงวนตำแหน่ง', 'S', 'W', 'master_table.html?table=PER_POS_RESERVE', 0, 9, 302, 
									  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();
				}
			}

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_SALARYHIS ALTER SAH_DOCNO NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SALARYHIS MODIFY SAH_DOCNO NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_SALARYHIS MODIFY SAH_DOCNO NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_SALARYHIS ALTER SAH_DOCDATE NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SALARYHIS MODIFY SAH_DOCDATE NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_SALARYHIS MODIFY SAH_DOCDATE NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_POSITIONHIS", "POH_ACTH_SEQ","INTEGER2", "5", "NULL");
			add_field("PER_ABSENT", "CREATE_DATE","VARCHAR", "19", "NULL");
			add_field("PER_ABSENT", "AUDIT_DATE","VARCHAR", "19", "NULL");
			add_field("PER_ABSENT", "REVIEW1_DATE","VARCHAR", "19", "NULL");
			add_field("PER_ABSENT", "REVIEW2_DATE","VARCHAR", "19", "NULL");
			add_field("PER_ABSENT", "APPROVE_DATE","VARCHAR", "19", "NULL");
			add_field("PER_COMDTL", "CMD_NOW","CHAR", "1", "NULL");
			add_field("PER_KPI", "KPI_ASSESSOR_ID","INTEGER", "10", "NULL");
			add_field("PER_KPI", "KPI_MITI","VARCHAR", "10", "NULL");
			add_field("PER_PERSONAL", "PER_AUDIT_FLAG","SINGLE", "1", "NULL");

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_POSITION ALTER POS_DOC_NO VARCHAR(100) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POSITION MODIFY POS_DOC_NO VARCHAR2(100) ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_POSITION MODIFY POS_DOC_NO VARCHAR(100) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_POSITION", "PR_CODE","VARCHAR", "10", "NULL");
			add_field("PER_POSITION", "POS_RESERVE2","VARCHAR", "100", "NULL");

			$cmd = " SELECT PR_CODE FROM PER_POS_RESERVE ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) {
				if($DPISDB=="odbc") 
					$cmd = " CREATE TABLE PER_POS_RESERVE (
					PR_CODE VARCHAR(10) NOT NULL,
					PR_NAME VARCHAR(100) NOT NULL,
					PR_ACTIVE SINGLE NOT NULL,
					UPDATE_USER INTEGER NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					PR_SEQ_NO INTEGER2 NULL,
					PR_OTHERNAME MEMO NULL,
					CONSTRAINT PK_PER_POS_RESERVE PRIMARY KEY (PR_CODE)) ";
				elseif($DPISDB=="oci8") 
					$cmd = " CREATE TABLE PER_POS_RESERVE (
					PR_CODE VARCHAR2(10) NOT NULL,
					PR_NAME VARCHAR2(100) NOT NULL,
					PR_ACTIVE NUMBER(1) NOT NULL,
					UPDATE_USER NUMBER(11) NOT NULL,
					UPDATE_DATE VARCHAR2(19) NOT NULL,		
					PR_SEQ_NO NUMBER(5) NULL,
					PR_OTHERNAME VARCHAR2(1000) NULL,
					CONSTRAINT PK_PER_POS_RESERVE PRIMARY KEY (PR_CODE)) ";
				elseif($DPISDB=="mysql")
					$cmd = " CREATE TABLE PER_POS_RESERVE (
					PR_CODE VARCHAR(10) NOT NULL,
					PR_NAME VARCHAR(100) NOT NULL,
					PR_ACTIVE SMALLINT(1) NOT NULL,
					UPDATE_USER INTEGER(11) NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					PR_SEQ_NO SMALLINT(5) NULL,
					PR_OTHERNAME TEXT NULL,
					CONSTRAINT PK_PER_POS_RESERVE PRIMARY KEY (PR_CODE)) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			}

			add_field("PER_LICENSEHIS", "LH_MAJOR","VARCHAR", "100", "NULL");

			$cmd = " SELECT CN_CODE FROM PER_COM_NOTE ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) {
				if($DPISDB=="odbc") 
					$cmd = " CREATE TABLE PER_COM_NOTE (
					CN_CODE VARCHAR(10) NOT NULL,
					CN_NAME VARCHAR(100) NOT NULL,
					CN_TYPE SINGLE NOT NULL,
					CN_ACTIVE SINGLE NOT NULL,
					UPDATE_USER INTEGER NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CN_SEQ_NO INTEGER2 NULL,
					CN_OTHERNAME MEMO NULL,
					CONSTRAINT PK_PER_COM_NOTE PRIMARY KEY (CN_CODE)) ";
				elseif($DPISDB=="oci8") 
					$cmd = " CREATE TABLE PER_COM_NOTE (
					CN_CODE VARCHAR2(10) NOT NULL,
					CN_NAME VARCHAR2(100) NOT NULL,
					CN_TYPE NUMBER(1) NOT NULL,
					CN_ACTIVE NUMBER(1) NOT NULL,
					UPDATE_USER NUMBER(11) NOT NULL,
					UPDATE_DATE VARCHAR2(19) NOT NULL,		
					CN_SEQ_NO NUMBER(5) NULL,
					CN_OTHERNAME VARCHAR2(1000) NULL,
					CONSTRAINT PK_PER_COM_NOTE PRIMARY KEY (CN_CODE)) ";
				elseif($DPISDB=="mysql")
					$cmd = " CREATE TABLE PER_COM_NOTE (
					CN_CODE VARCHAR(10) NOT NULL,
					CN_NAME VARCHAR(100) NOT NULL,
					CN_TYPE SMALLINT(1) NOT NULL,
					CN_ACTIVE SMALLINT(1) NOT NULL,
					UPDATE_USER INTEGER(11) NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CN_SEQ_NO SMALLINT(5) NULL,
					CN_OTHERNAME TEXT NULL,
					CONSTRAINT PK_PER_COM_NOTE PRIMARY KEY (CN_CODE)) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			}

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 
							  WHERE LINKTO_WEB = 'master_table_com_note.html?table=PER_COM_NOTE' ";
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
								  VALUES (1, 'TH', $MAX_ID, 18, 'M0318 หมายเหตุแนบท้ายคำสั่ง', 'S', 'W', 'master_table_com_note.html?table=PER_COM_NOTE', 0, 9, 295, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 18, 'M0318 หมายเหตุแนบท้ายคำสั่ง', 'S', 'W', 'master_table_com_note.html?table=PER_COM_NOTE', 0, 9, 295, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " UPDATE PER_EDUCATE SET CT_CODE_EDU = trim(CT_CODE_EDU) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_MOVMENT ALTER MOV_SUB_TYPE INTEGER2 ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_MOVMENT MODIFY MOV_SUB_TYPE NUMBER(2) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_MOVMENT ALTER MOV_SUB_TYPE SMALLINT(2) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_MOVMENT SET MOV_SUB_TYPE = 10 WHERE MOV_CODE = '105' AND MOV_NAME = 'ประเภทรับโอน' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_MOVMENT SET MOV_SUB_TYPE = 10 WHERE MOV_CODE = '10510' AND MOV_NAME = 'รับโอนข้าราชการพลเรือนสามัญ' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_MOVMENT SET MOV_SUB_TYPE = 10 WHERE MOV_CODE = '10520' AND MOV_NAME = 'รับโอนข้าราชการประเภทอื่น' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_MOVMENT SET MOV_SUB_TYPE = 11 WHERE MOV_CODE = '10140' AND MOV_NAME = 'บรรจุกลับ' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_MOVMENT SET MOV_SUB_TYPE = 91 WHERE MOV_CODE = '106' AND MOV_NAME = 'ประเภทให้โอน' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_MOVMENT SET MOV_SUB_TYPE = 91 WHERE MOV_CODE = '10610' AND MOV_NAME = 'โอนไปเป็นข้าราชการพลเรือนสามัญ' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_MOVMENT SET MOV_SUB_TYPE = 91 WHERE MOV_CODE = '10620' AND MOV_NAME = 'โอนไปเป็นข้าราชการประเภทอื่น' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_MOVMENT SET MOV_SUB_TYPE = 90 WHERE MOV_CODE = '118' AND MOV_NAME = 'ประเภทลาออก' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_MOVMENT SET MOV_SUB_TYPE = 90 WHERE MOV_CODE = '11810' AND MOV_NAME = 'ลาออก' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_MOVMENT SET MOV_SUB_TYPE = 90 WHERE MOV_CODE = '11820' AND MOV_NAME = 'ลาออกเพื่อไปรับราชการทหาร' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_MOVMENT SET MOV_SUB_TYPE = 90 WHERE MOV_CODE = '11899' AND MOV_NAME = 'ลาออก (ไม่นับอายุราชการ)' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_MOVMENT SET MOV_SUB_TYPE = 92 WHERE MOV_CODE = '119' AND MOV_NAME = 'ประเภทเกษียณอายุ' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_MOVMENT SET MOV_SUB_TYPE = 92 WHERE MOV_CODE = '11910' AND MOV_NAME = 'เกษียณอายุ' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_MOVMENT SET MOV_SUB_TYPE = 93 WHERE MOV_CODE = '11830' AND MOV_NAME = 'ลาออกตามโครงการเกษียณก่อนกำหนด' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_MOVMENT SET MOV_SUB_TYPE = 95 WHERE MOV_CODE = '11893' AND MOV_NAME = 'การลงโทษทางวินัย' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_MOVMENT SET MOV_SUB_TYPE = 95 WHERE MOV_CODE = '120' AND MOV_NAME = 'ประเภทให้ออกจากราชการ' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_MOVMENT SET MOV_SUB_TYPE = 95 WHERE MOV_CODE = '12010' AND MOV_NAME = 'ออกจากราชการเหตุวินัย' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_MOVMENT SET MOV_SUB_TYPE = 95 WHERE MOV_CODE = '12020' AND MOV_NAME = 'ออกจากราชการเหตุอื่น' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_MOVMENT SET MOV_SUB_TYPE = 95 WHERE MOV_CODE = '12030' AND MOV_NAME = 'สั่งพักราชการ/สั่งให้ออกจากราชการไว้ก่อน' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_MOVMENT SET MOV_SUB_TYPE = 95 WHERE MOV_CODE = '121' AND MOV_NAME = 'ประเภทปลดออกจากราชการ' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_MOVMENT SET MOV_SUB_TYPE = 95 WHERE MOV_CODE = '12110' AND MOV_NAME = 'ปลดออกจากราชการ' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_MOVMENT SET MOV_SUB_TYPE = 95 WHERE MOV_CODE = '122' AND MOV_NAME = 'ประเภทไล่ออกจากราชการ' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_MOVMENT SET MOV_SUB_TYPE = 95 WHERE MOV_CODE = '12210' AND MOV_NAME = 'ไล่ออกจากราชการ' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_MOVMENT SET MOV_SUB_TYPE = 94 WHERE MOV_CODE = '123' AND MOV_NAME = 'ประเภทตาย' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_MOVMENT SET MOV_SUB_TYPE = 94 WHERE MOV_CODE = '12310' AND MOV_NAME = 'ตาย' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_MOVMENT SET MOV_SUB_TYPE = 41 WHERE MOV_CODE = '21315' AND MOV_NAME = 'เลื่อนเงินเดือนระดับพอใช้' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_MOVMENT SET MOV_SUB_TYPE = 42 WHERE MOV_CODE = '21325' AND MOV_NAME = 'เลื่อนเงินเดือนระดับดี' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_MOVMENT SET MOV_SUB_TYPE = 43 WHERE MOV_CODE = '21335' AND MOV_NAME = 'เลื่อนเงินเดือนระดับดีมาก' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_MOVMENT SET MOV_SUB_TYPE = 44 WHERE MOV_CODE = '21345' AND MOV_NAME = 'เลื่อนเงินเดือนระดับดีเด่น' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_MOVMENT SET MOV_SUB_TYPE = 45 WHERE (MOV_CODE = '21310' AND MOV_NAME = 'เลื่อนขั้นเงินเดือน 0.5 ขั้น') OR MOV_NAME LIKE '%เลื่อนขั้น 0.5 ขั้น%' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_MOVMENT SET MOV_SUB_TYPE = 46 WHERE (MOV_CODE = '21320' AND MOV_NAME = 'เลื่อนขั้นเงินเดือน 1 ขั้น') OR MOV_NAME LIKE '%เลื่อนขั้น 1 ขั้น%' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_MOVMENT SET MOV_SUB_TYPE = 47 WHERE (MOV_CODE = '21330' AND MOV_NAME = 'เลื่อนขั้นเงินเดือน 1.5 ขั้น') OR MOV_NAME LIKE '%เลื่อนขั้น 1.5 ขั้น%' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_MOVMENT SET MOV_SUB_TYPE = 48 WHERE (MOV_CODE = '21340' AND MOV_NAME = 'เลื่อนขั้นเงินเดือน 2 ขั้น') OR MOV_NAME LIKE '%เลื่อนขั้น 2 ขั้น%' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_MOVMENT SET MOV_SUB_TYPE = 49 WHERE MOV_NAME LIKE 'ไม่ได้เลื่อน%' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_MOVMENT SET MOV_SUB_TYPE = 81 WHERE MOV_CODE in ('215', '21520') AND MOV_NAME in ('ประเภทปรับเงินเดือน', 'ปรับเงินเดือนตามกฏหมาย') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, 
							  UPDATE_DATE, MOV_SUB_TYPE)
							  VALUES ('114', 'ประเภทยกเลิกคำสั่ง/แก้ไขคำสั่ง', 3, 1, $SESS_USERID, '$UPDATE_DATE', 0) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, 
							  UPDATE_DATE, MOV_SUB_TYPE)
							  VALUES ('11410', 'ยกเลิกคำสั่ง', 3, 1, $SESS_USERID, '$UPDATE_DATE', 0) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, 
							  UPDATE_DATE, MOV_SUB_TYPE)
							  VALUES ('11420', 'แก้ไขคำสั่ง', 3, 1, $SESS_USERID, '$UPDATE_DATE', 98) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " SELECT SIGN_ID FROM PER_SIGN ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) {
				if($DPISDB=="odbc") 
					$cmd = " CREATE TABLE PER_SIGN(
					SIGN_ID INTEGER NOT NULL,	
					SIGN_PER_TYPE SINGLE NOT NULL,
					PER_ID INTEGER NOT NULL,	
					SIGN_STARTDATE VARCHAR(19) NULL,
					SIGN_ENDDATE VARCHAR(19) NULL,
					SIGN_NAME VARCHAR(255) NOT NULL,	
					SIGN_POSITION MEMO NULL,
					SIGN_REMARK MEMO NULL,
					SIGN_TYPE CHAR(1) NULL,
					SIGN_SEQ_NO INTEGER2 NULL,
					DEPARTMENT_ID INTEGER NOT NULL,	
					UPDATE_USER INTEGER NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_SIGN PRIMARY KEY (SIGN_ID)) ";
				elseif($DPISDB=="oci8") 
					$cmd = " CREATE TABLE PER_SIGN(
					SIGN_ID NUMBER(10) NOT NULL,	
					SIGN_PER_TYPE NUMBER(1) NOT NULL,
					PER_ID NUMBER(10) NOT NULL,	
					SIGN_STARTDATE VARCHAR2(19) NULL,
					SIGN_ENDDATE VARCHAR2(19) NULL,
					SIGN_NAME VARCHAR2(255) NOT NULL,	
					SIGN_POSITION VARCHAR2(1000) NULL,
					SIGN_REMARK VARCHAR2(1000) NULL,
					SIGN_TYPE CHAR(1) NULL,
					SIGN_SEQ_NO NUMBER(5) NULL,
					DEPARTMENT_ID NUMBER(10) NULL,	
					UPDATE_USER NUMBER(11) NOT NULL,
					UPDATE_DATE VARCHAR2(19) NOT NULL,		
					CONSTRAINT PK_PER_SIGN PRIMARY KEY (SIGN_ID)) ";
				elseif($DPISDB=="mysql")
					$cmd = " CREATE TABLE PER_SIGN(
					SIGN_ID INTEGER(10) NOT NULL,	
					SIGN_PER_TYPE SMALLINT(1) NOT NULL,
					PER_ID INTEGER(10) NOT NULL,	
					SIGN_STARTDATE VARCHAR(19) NULL,
					SIGN_ENDDATE VARCHAR(19) NULL,
					SIGN_NAME VARCHAR(255) NOT NULL,	
					SIGN_POSITION TEXT NULL,
					SIGN_REMARK TEXT NULL,
					SIGN_TYPE CHAR(1) NULL,
					SIGN_SEQ_NO SMALLINT(5) NULL,
					DEPARTMENT_ID INTEGER(10) NULL,	
					UPDATE_USER INTEGER(11) NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_SIGN PRIMARY KEY (SIGN_ID)) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			}

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'personal_sign.html' ";
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
								  VALUES (1, 'TH', $MAX_ID, 15, 'C15 ผู้มีหน้าที่จ่ายเงิน', 'S', 'W', 'personal_sign.html', 
								  0, 1, $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
								  UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 15, 'C15 ผู้มีหน้าที่จ่ายเงิน', 'S', 'W', 'personal_sign.html', 
								  0, 1, $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_BIRTHDATE_CHANGE ALTER PER_BIRTHDATE NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_BIRTHDATE_CHANGE MODIFY PER_BIRTHDATE NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_BIRTHDATE_CHANGE MODIFY PER_BIRTHDATE NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_SIGN", "DEPARTMENT_ID","INTEGER", "10", "NULL");
			add_field("PER_COMMAND", "COM_YEAR","VARCHAR", "4", "NULL");

			if ($ISCS_FLAG!=1){
				$cmd = " SELECT MENU_ID FROM BACKOFFICE_MENU_BAR_LV1 WHERE MENU_LABEL = 'M11 การประเมินผล' ";
				$db->send_cmd($cmd);
				//$db->show_error();
				$data = $db->get_array();
				$MENU_ID = $data[MENU_ID];

				$cmd = " SELECT PARENT_ID_LV1, LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 
								  WHERE LINKTO_WEB = 'kpi_per_type_competence.html?table=PER_TYPE_COMPETENCE' ";
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
									  VALUES (1, 'TH', $MAX_ID, 4, 'M1104 การประเมินสมรรถนะจำแนกตามประเภทบุคลากร และ$DEPARTMENT_TITLE', 'S', 'W', 'kpi_per_type_competence.html?table=PER_TYPE_COMPETENCE', 0, 9, $MENU_ID, $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();

					$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
									  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
									  CREATE_BY, UPDATE_DATE, UPDATE_BY)
									  VALUES (1, 'EN', $MAX_ID, 4, 'M1104 การประเมินสมรรถนะจำแนกตามประเภทบุคลากร และ$DEPARTMENT_TITLE', 'S', 'W', 'kpi_per_type_competence.html?table=PER_TYPE_COMPETENCE', 0, 9, $MENU_ID, $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();
				} else {
					$data = $db->get_array();
					$PARENT_ID_LV1 = $data[MENU_ID];
					if ($PARENT_ID_LV1 != $MENU_ID) {
						$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET PARENT_ID_LV1=$MENU_ID 
										  WHERE LINKTO_WEB = 'kpi_per_type_competence.html?table=PER_TYPE_COMPETENCE' ";
						$count_data = $db->send_cmd($cmd);
						//$db->show_error();
					}
				}

				$cmd = " SELECT PER_TYPE FROM PER_TYPE_COMPETENCE ";
				$count_data = $db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
				if (!$count_data) {
					if($DPISDB=="odbc") 
						$cmd = " CREATE TABLE PER_TYPE_COMPETENCE(
						PER_TYPE SINGLE NOT NULL,	
						DEPARTMENT_ID INTEGER NOT NULL,
						CP_CODE VARCHAR(3) NOT NULL,	
						UPDATE_USER INTEGER NOT NULL,
						UPDATE_DATE VARCHAR(19) NOT NULL,		
						CONSTRAINT PK_PER_TYPE_COMPETENCE PRIMARY KEY (PER_TYPE, DEPARTMENT_ID, CP_CODE)) ";
					elseif($DPISDB=="oci8") 
						$cmd = " CREATE TABLE PER_TYPE_COMPETENCE(
						PER_TYPE NUMBER(1) NOT NULL,
						DEPARTMENT_ID NUMBER(10) NOT NULL,
						CP_CODE VARCHAR2(3) NOT NULL,	
						UPDATE_USER NUMBER(11) NOT NULL,
						UPDATE_DATE VARCHAR2(19) NOT NULL,		
						CONSTRAINT PK_PER_TYPE_COMPETENCE PRIMARY KEY (PER_TYPE, DEPARTMENT_ID, CP_CODE)) ";
					elseif($DPISDB=="mysql")
						$cmd = " CREATE TABLE PER_TYPE_COMPETENCE(
						PER_TYPE SMALLINT(1) NOT NULL,
						DEPARTMENT_ID INTEGER(10) NOT NULL,
						CP_CODE VARCHAR(3) NOT NULL,	
						UPDATE_USER INTEGER(11) NOT NULL,
						UPDATE_DATE VARCHAR(19) NOT NULL,		
						CONSTRAINT PK_PER_TYPE_COMPETENCE PRIMARY KEY (PER_TYPE, DEPARTMENT_ID, CP_CODE)) ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();

					$cmd = " SELECT DISTINCT DEPARTMENT_ID FROM PER_POSITION WHERE DEPARTMENT_ID > 0 AND POS_STATUS = 1 ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
					while($data = $db_dpis->get_array()){
						$TMP_DEPARTMENT_ID = $data[DEPARTMENT_ID];

						$cmd = " SELECT CP_CODE FROM PER_COMPETENCE WHERE DEPARTMENT_ID = $TMP_DEPARTMENT_ID ";
						$db_dpis2->send_cmd($cmd);
						//$db_dpis2->show_error();
						while($data2 = $db_dpis2->get_array()){
							$CP_CODE = trim($data2[CP_CODE]);
							$cmd = " INSERT INTO PER_TYPE_COMPETENCE (PER_TYPE, DEPARTMENT_ID, CP_CODE, UPDATE_USER, UPDATE_DATE) 
											VALUES(1, $TMP_DEPARTMENT_ID, '$CP_CODE', $SESS_USERID, '$UPDATE_DATE') ";
							$db_dpis1->send_cmd($cmd);
							//$db_dpis1->show_error();
							if ($BKK_FLAG!=1) {
								$cmd = " INSERT INTO PER_TYPE_COMPETENCE (PER_TYPE, DEPARTMENT_ID, CP_CODE, UPDATE_USER, UPDATE_DATE) 
												VALUES(3, $TMP_DEPARTMENT_ID, '$CP_CODE', $SESS_USERID, '$UPDATE_DATE') ";
								$db_dpis1->send_cmd($cmd);
							}
						} // end while						
					} // end while						
				}
			}

			add_field("PER_TRAINING", "TRN_PRINT","SINGLE", "1", "NULL");

			$cmd = " UPDATE PER_TRAINING SET TRN_PRINT = 1 WHERE TRN_PRINT IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_EMPSER_POS_NAME", "EP_CP_CODE","VARCHAR", "255", "NULL");

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST ALTER O_SALARY VARCHAR(20) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST MODIFY O_SALARY VARCHAR2(20) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST MODIFY O_SALARY VARCHAR(20) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST ALTER K_SALARY VARCHAR(20) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST MODIFY K_SALARY VARCHAR2(20) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST MODIFY K_SALARY VARCHAR(20) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST ALTER D_SALARY VARCHAR(20) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST MODIFY D_SALARY VARCHAR2(20) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST MODIFY D_SALARY VARCHAR(20) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST ALTER M_SALARY VARCHAR(20) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST MODIFY M_SALARY VARCHAR2(20) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST MODIFY M_SALARY VARCHAR(20) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_EDUCLEVEL", "EL_TYPE","CHAR", "1", "NULL");

			if ($BKK_FLAG==1) {
				$cmd = " UPDATE PER_EDUCLEVEL SET EL_TYPE = 1 WHERE EL_CODE IN ('PB01') AND EL_TYPE IS NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_EDUCLEVEL SET EL_TYPE = 2 WHERE EL_CODE IN ('BA01') AND EL_TYPE IS NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_EDUCLEVEL SET EL_TYPE = 3 WHERE EL_CODE IN ('MA01') AND EL_TYPE IS NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_EDUCLEVEL SET EL_TYPE = 4 WHERE EL_CODE IN ('PH01') AND EL_TYPE IS NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			} else {
				$cmd = " UPDATE PER_EDUCLEVEL SET EL_TYPE = 1 WHERE EL_CODE IN ('05', '10', '20', '30') AND EL_TYPE IS NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_EDUCLEVEL SET EL_TYPE = 2 WHERE EL_CODE IN ('40') AND EL_TYPE IS NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_EDUCLEVEL SET EL_TYPE = 3 WHERE EL_CODE IN ('60') AND EL_TYPE IS NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_EDUCLEVEL SET EL_TYPE = 4 WHERE EL_CODE IN ('80') AND EL_TYPE IS NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			}

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'kpi_distribute_1.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'kpi_distribute_2.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'kpi_distribute_3.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'kpi_distribute.html' ";
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
								  VALUES (1, 'TH', $MAX_ID, 21, 'K21 การกระจายตัวชี้วัด', 'S', 'W', 'kpi_distribute.html', 0, 40, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
								  UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 21, 'K21 การกระจายตัวชี้วัด', 'S', 'W', 'kpi_distribute.html', 0, 40, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			} 

			$cmd = " SELECT TG_CODE FROM PER_TRAIN_GROUP ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) {
				if($DPISDB=="odbc") 
					$cmd = " CREATE TABLE PER_TRAIN_GROUP (
					TG_CODE VARCHAR(10) NOT NULL,
					TG_NAME VARCHAR(100) NOT NULL,
					TG_ACTIVE SINGLE NOT NULL,
					UPDATE_USER INTEGER NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					TG_SEQ_NO INTEGER2 NULL,
					TG_OTHERNAME MEMO NULL,
					CONSTRAINT PK_PER_TRAIN_GROUP PRIMARY KEY (TG_CODE)) ";
				elseif($DPISDB=="oci8") 
					$cmd = " CREATE TABLE PER_TRAIN_GROUP (
					TG_CODE VARCHAR2(10) NOT NULL,
					TG_NAME VARCHAR2(100) NOT NULL,
					TG_ACTIVE NUMBER(1) NOT NULL,
					UPDATE_USER NUMBER(11) NOT NULL,
					UPDATE_DATE VARCHAR2(19) NOT NULL,		
					TG_SEQ_NO NUMBER(5) NULL,
					TG_OTHERNAME VARCHAR2(1000) NULL,
					CONSTRAINT PK_PER_TRAIN_GROUP PRIMARY KEY (TG_CODE)) ";
				elseif($DPISDB=="mysql")
					$cmd = " CREATE TABLE PER_TRAIN_GROUP (
					TG_CODE VARCHAR(10) NOT NULL,
					TG_NAME VARCHAR(100) NOT NULL,
					TG_ACTIVE SMALLINT(1) NOT NULL,
					UPDATE_USER INTEGER(11) NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					TG_SEQ_NO SMALLINT(5) NULL,
					TG_OTHERNAME TEXT NULL,
					CONSTRAINT PK_PER_TRAIN_GROUP PRIMARY KEY (TG_CODE)) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			}

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'master_table.html?table=PER_TRAIN_GROUP' ";
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
								  VALUES (1, 'TH', $MAX_ID, 12, 'M0712 กลุ่มหลักสูตรการฝึกอบรม', 'S', 'W', 'master_table.html?table=PER_TRAIN_GROUP', 0, 9, 293, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 12, 'M0712 กลุ่มหลักสูตรการฝึกอบรม', 'S', 'W', 'master_table.html?table=PER_TRAIN_GROUP', 0, 9, 293, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			add_field("PER_TRAIN", "TG_CODE","VARCHAR", "10", "NULL");

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_ORDER_DTL ALTER PT_CODE NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_ORDER_DTL MODIFY PT_CODE NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_ORDER_DTL MODIFY PT_CODE NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_PERSONAL", "PER_PROBATION_FLAG","SINGLE", "1", "NULL");

			$cmd = " SELECT KD_ID FROM PER_KPI_DISTRIBUTE ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) {
				if($DPISDB=="odbc") 
					$cmd = " CREATE TABLE PER_KPI_DISTRIBUTE(
					KD_ID INTEGER NOT NULL,	
					KD_TYPE CHAR(1) NOT NULL,
					KPI_YEAR VARCHAR(4) NOT NULL,
					KPI_ID INTEGER NOT NULL,	
					DEPARTMENT_ID INTEGER NULL,	
					ORG_ID INTEGER NULL,	
					ORG_ID_1 INTEGER NULL,	
					ORG_ID_2 INTEGER NULL,	
					KD_PER_ID INTEGER NULL,	
					KD_POS_ID INTEGER NULL,	
					KD_FLAG VARCHAR(2) NULL,
					KD_REMARK MEMO NULL,
					UPDATE_USER INTEGER NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_KPI_DISTRIBUTE PRIMARY KEY (KD_ID)) ";
				elseif($DPISDB=="oci8") 
					$cmd = " CREATE TABLE PER_KPI_DISTRIBUTE(
					KD_ID NUMBER(10) NOT NULL,	
					KD_TYPE CHAR(1) NOT NULL,
					KPI_YEAR VARCHAR2(4) NOT NULL,
					KPI_ID NUMBER(10) NOT NULL,	
					DEPARTMENT_ID NUMBER(10) NULL,	
					ORG_ID NUMBER(10) NULL,	
					ORG_ID_1 NUMBER(10) NULL,	
					ORG_ID_2 NUMBER(10) NULL,	
					KD_PER_ID NUMBER(10) NULL,	
					KD_POS_ID NUMBER(10) NULL,	
					KD_FLAG VARCHAR2(2) NULL,
					KD_REMARK VARCHAR2(1000) NULL,
					UPDATE_USER NUMBER(11) NOT NULL,
					UPDATE_DATE VARCHAR2(19) NOT NULL,		
					CONSTRAINT PK_PER_KPI_DISTRIBUTE PRIMARY KEY (KD_ID)) ";
				elseif($DPISDB=="mysql")
					$cmd = " CREATE TABLE PER_KPI_DISTRIBUTE(
					KD_ID INTEGER(10) NOT NULL,	
					KD_TYPE CHAR(1) NOT NULL,
					KPI_YEAR VARCHAR(4) NOT NULL,
					KPI_ID INTEGER(10) NOT NULL,	
					DEPARTMENT_ID INTEGER(10) NULL,	
					ORG_ID INTEGER(10) NULL,	
					ORG_ID_1 INTEGER(10) NULL,	
					ORG_ID_2 INTEGER(10) NULL,	
					KD_PER_ID INTEGER(10) NULL,	
					KD_POS_ID INTEGER(10) NULL,	
					KD_FLAG VARCHAR(2) NULL,
					KD_REMARK TEXT NULL,
					UPDATE_USER INTEGER(11) NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_KPI_DISTRIBUTE PRIMARY KEY (KD_ID)) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			}

			add_field("PER_POS_EMP", "LEVEL_NO","VARCHAR", "10", "NULL");

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_TIMEHIS ALTER TIMEH_REMARK VARCHAR(255) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_TIMEHIS MODIFY TIMEH_REMARK VARCHAR2(255) ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_TIMEHIS MODIFY TIMEH_REMARK VARCHAR(255) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_PERSONAL", "DEPARTMENT_ID_ASS","INTEGER", "10", "NULL");

			$cmd = " UPDATE PER_PERSONAL SET DEPARTMENT_ID_ASS=DEPARTMENT_ID WHERE DEPARTMENT_ID_ASS IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_SALARYHIS", "SAH_REMARK1","MEMO", "2000", "NULL");
			add_field("PER_SALARYHIS", "SAH_REMARK2","MEMO", "2000", "NULL");

			$cmd = " UPDATE PER_LAYEREMP SET LAYERE_SALARY = 7590, LAYERE_DAY = 330, LAYERE_HOUR = 47.15 
							  WHERE PG_CODE = '1000' AND LAYERE_NO = 10.5 AND LAYERE_SALARY = 7620 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYEREMP_NEW SET LAYERE_SALARY = 7590, LAYERE_DAY = 330, LAYERE_HOUR = 47.15 
							  WHERE PG_CODE = '1000' AND LAYERE_NO = 10.5 AND LAYERE_SALARY = 7620 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYEREMP SET LAYERE_SALARY = 8300, LAYERE_DAY = 360.90, LAYERE_HOUR = 51.55 
							  WHERE PG_CODE = '1000' AND LAYERE_NO = 12.5 AND LAYERE_SALARY = 8290 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYEREMP_NEW SET LAYERE_SALARY = 8300, LAYERE_DAY = 360.90, LAYERE_HOUR = 51.55 
							  WHERE PG_CODE = '1000' AND LAYERE_NO = 12.5 AND LAYERE_SALARY = 8290 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYEREMP SET LAYERE_SALARY = 9540, LAYERE_DAY = 414.80, LAYERE_HOUR = 59.25 
							  WHERE PG_CODE = '1000' AND LAYERE_NO = 16 AND LAYERE_SALARY = 9520 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYEREMP_NEW SET LAYERE_SALARY = 9540, LAYERE_DAY = 414.80, LAYERE_HOUR = 59.25 
							  WHERE PG_CODE = '1000' AND LAYERE_NO = 16 AND LAYERE_SALARY = 9520 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYEREMP SET LAYERE_SALARY = 10200, LAYERE_DAY = 443.50, LAYERE_HOUR = 63.35 
							  WHERE PG_CODE = '1000' AND LAYERE_NO = 18 AND LAYERE_SALARY = 10280 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYEREMP_NEW SET LAYERE_SALARY = 10200, LAYERE_DAY = 443.50, LAYERE_HOUR = 63.35 
							  WHERE PG_CODE = '1000' AND LAYERE_NO = 18 AND LAYERE_SALARY = 10280 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYEREMP SET LAYERE_SALARY = 13300, LAYERE_DAY = 578.30, LAYERE_HOUR = 82.65 
							  WHERE PG_CODE = '2000' AND LAYERE_NO = 12 AND LAYERE_SALARY = 13160 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYEREMP_NEW SET LAYERE_SALARY = 13300, LAYERE_DAY = 578.30, LAYERE_HOUR = 82.65 
							  WHERE PG_CODE = '2000' AND LAYERE_NO = 12 AND LAYERE_SALARY = 13160 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYEREMP SET LAYERE_SALARY = 16400, LAYERE_DAY = 713.05, LAYERE_HOUR = 101.90 
							  WHERE PG_CODE = '3000' AND LAYERE_NO = 12.5 AND LAYERE_SALARY = 16570 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYEREMP_NEW SET LAYERE_SALARY = 16400, LAYERE_DAY = 713.05, LAYERE_HOUR = 101.90 
							  WHERE PG_CODE = '3000' AND LAYERE_NO = 12.5 AND LAYERE_SALARY = 16570 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();			
			
			$cmd = " UPDATE PER_LAYEREMP SET LAYERE_SALARY = 20000, LAYERE_DAY = 869.60, LAYERE_HOUR = 124.25 
							  WHERE PG_CODE = '3000' AND LAYERE_NO = 17 AND LAYERE_SALARY = 19970 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYEREMP_NEW SET LAYERE_SALARY = 20000, LAYERE_DAY = 869.60, LAYERE_HOUR = 124.25 
							  WHERE PG_CODE = '3000' AND LAYERE_NO = 17 AND LAYERE_SALARY = 19970 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYEREMP SET LAYERE_SALARY = 8690, LAYERE_DAY = 377.85, LAYERE_HOUR = 54 
							  WHERE PG_CODE = '1000' AND LAYERE_NO = 13.5 AND LAYERE_SALARY = 8640 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYEREMP_NEW SET LAYERE_SALARY = 8690, LAYERE_DAY = 377.85, LAYERE_HOUR = 54 
							  WHERE PG_CODE = '1000' AND LAYERE_NO = 13.5 AND LAYERE_SALARY = 8640 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYEREMP SET LAYERE_SALARY = 9400, LAYERE_DAY = 408.70, LAYERE_HOUR = 58.40 
							  WHERE PG_CODE = '1000' AND LAYERE_NO = 15.5 AND LAYERE_SALARY = 9300 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYEREMP_NEW SET LAYERE_SALARY = 9400, LAYERE_DAY = 408.70, LAYERE_HOUR = 58.40 
							  WHERE PG_CODE = '1000' AND LAYERE_NO = 15.5 AND LAYERE_SALARY = 9300 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYEREMP SET LAYERE_SALARY = 10840, LAYERE_DAY = 471.30, LAYERE_HOUR = 67.35 
							  WHERE PG_CODE = '1000' AND LAYERE_NO = 19 AND LAYERE_SALARY = 10760 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYEREMP_NEW SET LAYERE_SALARY = 10840, LAYERE_DAY = 471.30, LAYERE_HOUR = 67.35 
							  WHERE PG_CODE = '1000' AND LAYERE_NO = 19 AND LAYERE_SALARY = 10760 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYEREMP SET LAYERE_SALARY = 11500, LAYERE_DAY = 500, LAYERE_HOUR = 71.45 
							  WHERE PG_CODE = '1000' AND LAYERE_NO = 20.5 AND LAYERE_SALARY = 11400 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYEREMP_NEW SET LAYERE_SALARY = 11500, LAYERE_DAY = 500, LAYERE_HOUR = 71.45 
							  WHERE PG_CODE = '1000' AND LAYERE_NO = 20.5 AND LAYERE_SALARY = 11400 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYEREMP SET LAYERE_SALARY = 11840, LAYERE_DAY = 514.80, LAYERE_HOUR = 73.55 
							  WHERE PG_CODE = '1000' AND LAYERE_NO = 21.5 AND LAYERE_SALARY = 11860 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYEREMP_NEW SET LAYERE_SALARY = 11840, LAYERE_DAY = 514.80, LAYERE_HOUR = 73.55 
							  WHERE PG_CODE = '1000' AND LAYERE_NO = 21.5 AND LAYERE_SALARY = 11860 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();			
			
			$cmd = " UPDATE PER_LAYEREMP SET LAYERE_SALARY = 17500, LAYERE_DAY = 760.90, LAYERE_HOUR = 108.70 
							  WHERE PG_CODE = '3000' AND LAYERE_NO = 13.5 AND LAYERE_SALARY = 17310 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYEREMP_NEW SET LAYERE_SALARY = 17500, LAYERE_DAY = 760.90, LAYERE_HOUR = 108.70 
							  WHERE PG_CODE = '3000' AND LAYERE_NO = 13.5 AND LAYERE_SALARY = 17310 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYEREMP SET LAYERE_SALARY = 21000, LAYERE_DAY = 913.05, LAYERE_HOUR = 130.45 
							  WHERE PG_CODE = '3000' AND LAYERE_NO = 18.5 AND LAYERE_SALARY = 21190 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYEREMP_NEW SET LAYERE_SALARY = 21000, LAYERE_DAY = 913.05, LAYERE_HOUR = 130.45 
							  WHERE PG_CODE = '3000' AND LAYERE_NO = 18.5 AND LAYERE_SALARY = 21190 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
/*
			$cmd = " UPDATE PER_POS_LEVEL_SALARY SET MIN_SALARY = 8690, MAX_SALARY = 14850 
							  WHERE PN_CODE = '1101' AND LEVEL_NO = 'บ1' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_LEVEL_SALARY SET MIN_SALARY = 8690 WHERE MIN_SALARY = 5340 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_LEVEL_SALARY SET MIN_SALARY = 9400 WHERE MIN_SALARY = 6140 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_LEVEL_SALARY SET MIN_SALARY = 11500 WHERE MIN_SALARY = 7460 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_LEVEL_SALARY SET MIN_SALARY = 15000 WHERE MIN_SALARY = 8340 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_LEVEL_SALARY SET MIN_SALARY = 17500 WHERE MIN_SALARY = 10190 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
*/
			if ($BKK_FLAG==1) {
				$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO, COM_ACTIVE)
								VALUES ('5042', 'คส. 4.2', 'คำสั่งเลื่อนและแต่งตั้งข้าราชการ', '504', 37, 1) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO, COM_ACTIVE)
								VALUES ('5043', 'คส. 4.3', 'คำสั่งแต่งตั้งข้าราชการ', '504', 37, 1) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			}

			$cmd = " UPDATE PER_LINE SET PL_NAME = 'นักกีฏวิทยา' WHERE PL_NAME = 'นักกีฎวิทยา' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$count_data = add_field("PER_LINE", "PL_ENGNAME","MEMO", "1000", "NULL");
			if (!$count_data) {
				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Executive' WHERE PL_NAME = 'นักบริหาร' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Governing Executive' WHERE PL_NAME = 'นักปกครอง' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Diplomatic Service Executive' WHERE PL_NAME = 'นักบริหารการทูต' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Inspector - General' WHERE PL_NAME = 'ผู้ตรวจราชการกระทรวง' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Director' WHERE PL_NAME = 'ผู้อำนวยการ' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Inspector' WHERE PL_NAME = 'ผู้ตรวจราชการกรม' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Krisdika Counsel' WHERE PL_NAME = 'นักกฎหมายกฤษฎีกา' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Diplomatic Service Officer' WHERE PL_NAME = 'นักการทูต' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Probation Officer' WHERE PL_NAME = 'พนักงานคุมประพฤติ' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'General Administration Officer' WHERE PL_NAME = 'นักจัดการงานทั่วไป' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Special Case Officer' WHERE PL_NAME = 'เจ้าหน้าที่คดีพิเศษ' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Human Resource Officer' WHERE PL_NAME = 'นักทรัพยากรบุคคล' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Licensing Officer' WHERE PL_NAME = 'นักทะเบียนวิชาชีพ' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Legal Officer' WHERE PL_NAME = 'นิติกร' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Director (Legal Affairs)' WHERE PL_NAME = 'ผู้อำนวยการเฉพาะด้าน (นิติการ)' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Governing Officer' WHERE PL_NAME = 'เจ้าพนักงานปกครอง' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Special Case Inquiry Official' WHERE PL_NAME = 'พนักงานสอบสวนคดีพิเศษ' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Director (Special Case Inquiry Official )' WHERE PL_NAME = 'ผู้อำนวยการเฉพาะด้าน (พนักงานสอบสวนคดีพิเศษ)' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Public Sector Development Officer' WHERE PL_NAME = 'นักพัฒนาระบบราชการ' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Plan and Policy Analyst' WHERE PL_NAME = 'นักวิเคราะห์นโยบายและแผน' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Computer Technical Officer' WHERE PL_NAME = 'นักวิชาการคอมพิวเตอร์' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Director (Computer Science)' WHERE PL_NAME = 'ผู้อำนวยการเฉพาะด้าน (วิชาการคอมพิวเตอร์)' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Penologist' WHERE PL_NAME = 'นักทัณฑวิทยา' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Information Technology Officer' WHERE PL_NAME = 'นักเทคโนโลยีสารสนเทศ' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Director (Information Technology)' WHERE PL_NAME = 'ผู้อำนวยการเฉพาะด้าน (วิชาการเทคโนโลยีสารสนเทศ)' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Supply Analyst' WHERE PL_NAME = 'นักวิชาการพัสดุ' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Justice Officer' WHERE PL_NAME = 'นักวิชาการยุติธรรม' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Local Government Study and Research Officer' WHERE PL_NAME = 'นักส่งเสริมการปกครองท้องถิ่น' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Statistician' WHERE PL_NAME = 'นักวิชาการสถิติ' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Director (Statistics)' WHERE PL_NAME = 'ผู้อำนวยการเฉพาะด้าน (วิชาการสถิติ)' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Development Cooperation Officer' WHERE PL_NAME = 'นักวิเทศสหการ' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Foreign Relations Officer' WHERE PL_NAME = 'นักวิเทศสัมพันธ์' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Investigator' WHERE PL_NAME = 'นักสืบสวนสอบสวน' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Scribe' WHERE PL_NAME = 'อาลักษณ์' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Real Property Procurement Officer' WHERE PL_NAME = 'เจ้าหน้าที่จัดผลประโยชน์' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Tax Audit Officer' WHERE PL_NAME = 'นักตรวจสอบภาษี' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Director (Tax Audit)' WHERE PL_NAME = 'ผู้อำนวยการเฉพาะด้าน (ตรวจสอบภาษี)' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Budget Analyst' WHERE PL_NAME = 'นักวิเคราะห์งบประมาณ' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'State Enterprise Analyst' WHERE PL_NAME = 'นักวิเคราะห์รัฐวิสาหกิจ' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Fiscal Analyst' WHERE PL_NAME = 'นักวิชาการคลัง' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Director (Fiscal Analysis)' WHERE PL_NAME = 'ผู้อำนวยการเฉพาะด้าน (วิชาการคลัง)' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Finance and Accounting Analyst' WHERE PL_NAME = 'นักวิชาการเงินและบัญชี' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Director (Finance and Accounting)' WHERE PL_NAME = 'ผู้อำนวยการเฉพาะด้าน (วิชาการเงินและบัญชี)' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Weights and Measures Technical Officer' WHERE PL_NAME = 'นักวิชาการชั่งตวงวัด' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Director (Weights and Measures)' WHERE PL_NAME = 'ผู้อำนวยการเฉพาะด้าน (วิชาการชั่งตวงวัด)' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Auditor หรือ Auditing Technical Officer' WHERE PL_NAME = 'นักวิชาการตรวจสอบบัญชี' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Director (Audit)' WHERE PL_NAME = 'ผู้อำนวยการเฉพาะด้าน (วิชาการตรวจสอบบัญชี)' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Internal Auditor' WHERE PL_NAME = 'นักวิชาการตรวจสอบภายใน' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Director (Internal Audit)' WHERE PL_NAME = 'ผู้อำนวยการเฉพาะด้าน (วิชาการตรวจสอบภายใน)' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Patent Examiner' WHERE PL_NAME = 'นักวิชาการตรวจสอบสิทธิบัตร' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Mineral Resources Technical Officer' WHERE PL_NAME = 'นักวิชาการทรัพยากรธรณี' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Accountant' WHERE PL_NAME = 'นักบัญชี' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Director (Accounting)' WHERE PL_NAME = 'ผู้อำนวยการเฉพาะด้าน (วิชาการบัญชี)' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Food Technologist' WHERE PL_NAME = 'นักวิชาการผลิตภัณฑ์อาหาร' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Director (Food Product)' WHERE PL_NAME = 'ผู้อำนวยการเฉพาะด้าน (วิชาการผลิตภัณฑ์อาหาร)' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Trade Officer' WHERE PL_NAME = 'นักวิชาการพาณิชย์' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Tax Economist' WHERE PL_NAME = 'นักวิชาการภาษี' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Director (Tax)' WHERE PL_NAME = 'ผู้อำนวยการเฉพาะด้าน (วิชาการภาษี)' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Standards Officer' WHERE PL_NAME = 'นักวิชาการมาตรฐาน' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Director (Standardization)' WHERE PL_NAME = 'ผู้อำนวยการเฉพาะด้าน (วิชาการมาตรฐาน)' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Customs Technical Officer' WHERE PL_NAME = 'นักวิชาการศุลกากร' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Economist' WHERE PL_NAME = 'เศรษฐกร' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Director (Economics)' WHERE PL_NAME = 'ผู้อำนวยการเฉพาะด้าน (วิชาการเศรษฐกิจ)' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Investment Promotion Officer' WHERE PL_NAME = 'นักวิชาการส่งเสริมการลงทุน' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Excise Technical Officer' WHERE PL_NAME = 'นักวิชาการสรรพสามิต' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Revenue Technical Officer' WHERE PL_NAME = 'นักวิชาการสรรพากร' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Cooperative Technical Officer' WHERE PL_NAME = 'นักวิชาการสหกรณ์' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Director (Cooperative)' WHERE PL_NAME = 'ผู้อำนวยการเฉพาะด้าน (วิชาการสหกรณ์)' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Industrial Technical Officer' WHERE PL_NAME = 'นักวิชาการอุตสาหกรรม' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Intelligence Officer' WHERE PL_NAME = 'นักการข่าว' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Navigator' WHERE PL_NAME = 'นักเดินเรือ' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Harbour Master' WHERE PL_NAME = 'เจ้าพนักงานตรวจท่า' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Ship Pilot Officer' WHERE PL_NAME = 'เจ้าพนักงานนำร่อง' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Director (Sea Pilot)' WHERE PL_NAME = 'ผู้อำนวยการเฉพาะด้าน (นำร่อง)' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Public Relations Officer' WHERE PL_NAME = 'นักประชาสัมพันธ์' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Transport Technical Officer' WHERE PL_NAME = 'นักวิชาการขนส่ง' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Dissemination Technical Officer' WHERE PL_NAME = 'นักวิชาการเผยแพร่' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Audio-Visual Technical Officer' WHERE PL_NAME = 'นักวิชาการโสตทัศนศึกษา' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Mass Communications Officer' WHERE PL_NAME = 'นักสื่อสารมวลชน' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Agricultural Research Officer' WHERE PL_NAME = 'นักวิชาการเกษตร' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Director (Agriculture)' WHERE PL_NAME = 'ผู้อำนวยการเฉพาะด้าน (วิชาการเกษตร)' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Land Reform Technical Officer' WHERE PL_NAME = 'นักวิชาการปฏิรูปที่ดิน' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Fishery Biologist' WHERE PL_NAME = 'นักวิชาการประมง' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Director (Fishery Science)' WHERE PL_NAME = 'ผู้อำนวยการเฉพาะด้าน (วิชาการประมง)' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Forestry Technical Officer' WHERE PL_NAME = 'นักวิชาการป่าไม้' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Director (Forestry)' WHERE PL_NAME = 'ผู้อำนวยการเฉพาะด้าน (วิชาการป่าไม้)' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Agricultural Extensionist' WHERE PL_NAME = 'นักวิชาการส่งเสริมการเกษตร' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Director (Agricultural Extension)' WHERE PL_NAME = 'ผู้อำนวยการเฉพาะด้าน (วิชาการส่งเสริมการเกษตร)' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Animal Husbandry Technical Officer' WHERE PL_NAME = 'นักวิชาการสัตวบาล' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Director (Animal Husbandry)' WHERE PL_NAME = 'ผู้อำนวยการเฉพาะด้าน (วิชาการสัตวบาล)' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Irrigation Engineer' WHERE PL_NAME = 'วิศวกรชลประทาน' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Director (Irrigation Engineering)' WHERE PL_NAME = 'ผู้อำนวยการเฉพาะด้าน (วิศวกรรมชลประทาน)' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Soil Surveyor' WHERE PL_NAME = 'นักสำรวจดิน' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Director (Soil Survey)' WHERE PL_NAME = 'ผู้อำนวยการเฉพาะด้าน (สำรวจดิน)' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Entomologist' WHERE PL_NAME = 'นักกีฏวิทยา' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Director (Entomology)' WHERE PL_NAME = 'ผู้อำนวยการเฉพาะด้าน (กีฏวิทยา)' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Radiation Biologist' WHERE PL_NAME = 'นักชีววิทยารังสี' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Geologist' WHERE PL_NAME = 'นักธรณีวิทยา' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Director (Geology)' WHERE PL_NAME = 'ผู้อำนวยการเฉพาะด้าน (ธรณีวิทยา)' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Forensic Scientist' WHERE PL_NAME = 'นักนิติวิทยาศาสตร์' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Director (Forensic Science)' WHERE PL_NAME = 'ผู้อำนวยการเฉพาะด้าน (นิติวิทยาศาสตร์)' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Nuclear Chemist' WHERE PL_NAME = 'นักนิวเคลียร์เคมี' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Nuclear Physicist' WHERE PL_NAME = 'นักนิวเคลียร์ฟิสิกส์' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Radiation Physicist' WHERE PL_NAME = 'นักฟิสิกส์รังสี' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Director (Radiation Physics)' WHERE PL_NAME = 'ผู้อำนวยการเฉพาะด้าน (ฟิสิกส์รังสี)' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Plant Pathologist' WHERE PL_NAME = 'นักวิชาการโรคพืช' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Director (Plant Pathology)' WHERE PL_NAME = 'ผู้อำนวยการเฉพาะด้าน (วิชาการโรคพืช)' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Hydrologist' WHERE PL_NAME = 'นักอุทกวิทยา' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Director (Hydrology)' WHERE PL_NAME = 'ผู้อำนวยการเฉพาะด้าน (วิชาการอุทกวิทยา)' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Scientist' WHERE PL_NAME = 'นักวิทยาศาสตร์' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Director (Science)' WHERE PL_NAME = 'ผู้อำนวยการเฉพาะด้าน (วิทยาศาสตร์)' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Nuclear Scientist' WHERE PL_NAME = 'นักวิทยาศาสตร์นิวเคลียร์' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Director (Nuclear Science)' WHERE PL_NAME = 'ผู้อำนวยการเฉพาะด้าน (วิทยาศาสตร์นิวเคลียร์)' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Zoologist' WHERE PL_NAME = 'นักสัตววิทยา' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Director (Zoology)' WHERE PL_NAME = 'ผู้อำนวยการเฉพาะด้าน (สัตววิทยา)' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Meteorologist' WHERE PL_NAME = 'นักอุตุนิยมวิทยา' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Director (Meteorology)' WHERE PL_NAME = 'ผู้อำนวยการเฉพาะด้าน (อุตุนิยมวิทยา)' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Physiotherapist' WHERE PL_NAME = 'นักกายภาพบำบัด' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Occupational Therapist' WHERE PL_NAME = 'นักกิจกรรมบำบัด' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Psychologist' WHERE PL_NAME = 'นักจิตวิทยา' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Clinical Psychologist' WHERE PL_NAME = 'นักจิตวิทยาคลินิค' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Dentist' WHERE PL_NAME = 'ทันตแพทย์' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Director (Dentist)' WHERE PL_NAME = 'ผู้อำนวยการเฉพาะด้าน (ทันตแพทย์)' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Medical Technologist' WHERE PL_NAME = 'นักเทคนิคการแพทย์' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Veterinarian' WHERE PL_NAME = 'นายสัตวแพทย์' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Director (Veterinarian)' WHERE PL_NAME = 'ผู้อำนวยการเฉพาะด้าน (นายสัตวแพทย์)' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Registered Nurse' WHERE PL_NAME = 'พยาบาลวิชาชีพ' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Medical Physician' WHERE PL_NAME = 'นายแพทย์' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Director (Physician)' WHERE PL_NAME = 'ผู้อำนวยการเฉพาะด้าน (แพทย์)' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Thai Traditional Medical Doctor' WHERE PL_NAME = 'นักการแพทย์แผนไทย' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Pharmacist' WHERE PL_NAME = 'เภสัชกร' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Director (Pharmacy)' WHERE PL_NAME = 'ผู้อำนวยการเฉพาะด้าน (เภสัชกรรม)' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Nutritionist' WHERE PL_NAME = 'นักโภชนาการ' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Radiological Technologist' WHERE PL_NAME = 'นักรังสีการแพทย์' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Nursing Technical Officer' WHERE PL_NAME = 'นักวิชาการพยาบาล' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Director (Nursing)' WHERE PL_NAME = 'ผู้อำนวยการเฉพาะด้าน (วิชาการพยาบาล)' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Public Health Technical Officer' WHERE PL_NAME = 'นักวิชาการสาธารณสุข' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Vocational Therapist' WHERE PL_NAME = 'นักอาชีวบำบัด' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Food and Drug Technical Officer' WHERE PL_NAME = 'นักวิชาการอาหารและยา' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Director (Food and Drug Science)' WHERE PL_NAME = 'ผู้อำนวยการเฉพาะด้าน (วิชาการอาหารและยา)' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Medical Scientist' WHERE PL_NAME = 'นักวิทยาศาสตร์การแพทย์' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Director (Medical Science)' WHERE PL_NAME = 'ผู้อำนวยการเฉพาะด้าน (วิทยาศาสตร์การแพทย์)' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Speech Pathologist, Audiologist หรือ Speech-Language Pathologist' WHERE PL_NAME = 'นักเวชศาสตร์การสื่อความหมาย' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Prosthetist and Orthotist' WHERE PL_NAME = 'นักกายอุปกรณ์' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Painter' WHERE PL_NAME = 'จิตรกร' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Director (Painting)' WHERE PL_NAME = 'ผู้อำนวยการเฉพาะด้าน (จิตรกรรม)' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Marine Engineer' WHERE PL_NAME = 'นายช่างกลเรือ' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Medical Photographer' WHERE PL_NAME = 'ช่างภาพการแพทย์' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Ship Surveyor' WHERE PL_NAME = 'เจ้าพนักงานตรวจเรือ' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Director (Ship Survey)' WHERE PL_NAME = 'ผู้อำนวยการเฉพาะด้าน (ตรวจเรือ)' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Aviation Safety Inspector' WHERE PL_NAME = 'นักตรวจสอบความปลอดภัยด้านการบิน' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Sculptor' WHERE PL_NAME = 'ประติมากร' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Director (Sculpture)' WHERE PL_NAME = 'ผู้อำนวยการเฉพาะด้าน (ประติมากรรม)' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Landscape Architect' WHERE PL_NAME = 'ภูมิสถาปนิก' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Interior Designer' WHERE PL_NAME = 'มัณฑนากร' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Director (Interior Design)' WHERE PL_NAME = 'ผู้อำนวยการเฉพาะด้าน (มัณฑนศิลป์)' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Mint Technical Officer' WHERE PL_NAME = 'นักวิชาการกษาปณ์' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Director (Mint)' WHERE PL_NAME = 'ผู้อำนวยการเฉพาะด้าน (วิชาการกษาปณ์)' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Academic Artist' WHERE PL_NAME = 'นักวิชาการช่างศิลป์' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Director (Academic Art)' WHERE PL_NAME = 'ผู้อำนวยการเฉพาะด้าน (วิชาการช่างศิลป์)' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Photogrammetrist' WHERE PL_NAME = 'นักวิชาการแผนที่ภาพถ่าย' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Director (Photogrammetry Technical)' WHERE PL_NAME = 'ผู้อำนวยการเฉพาะด้าน (วิชาการแผนที่ภาพถ่าย)' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Energy Technical Officer' WHERE PL_NAME = 'นักวิชาการพลังงาน' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Industrial Products Designer' WHERE PL_NAME = 'นักวิชาการออกแบบผลิตภัณฑ์' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Engineer' WHERE PL_NAME = 'วิศวกร' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Director (Engineering)' WHERE PL_NAME = 'ผู้อำนวยการเฉพาะด้าน (วิศวกรรม)' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Agricultural Engineer' WHERE PL_NAME = 'วิศวกรการเกษตร' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Director (Agricultural Engineering)' WHERE PL_NAME = 'ผู้อำนวยการเฉพาะด้าน (วิศวกรรมการเกษตร)' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Mechanical Engineer' WHERE PL_NAME = 'วิศวกรเครื่องกล' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Director (Mechanical Engineering)' WHERE PL_NAME = 'ผู้อำนวยการเฉพาะด้าน (วิศวกรรมเครื่องกล)' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Nuclear Engineer' WHERE PL_NAME = 'วิศวกรนิวเคลียร์' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Director (Nuclear Engineering)' WHERE PL_NAME = 'ผู้อำนวยการเฉพาะด้าน (วิศวกรรมนิวเคลียร์)' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Petroleum Engineer' WHERE PL_NAME = 'วิศวกรปิโตรเลียม' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Director (Petroleum Engineering)' WHERE PL_NAME = 'ผู้อำนวยการเฉพาะด้าน (วิศวกรรมปิโตรเลียม)' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Electrical Engineer' WHERE PL_NAME = 'วิศวกรไฟฟ้า' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Director (Electrical Engineering)' WHERE PL_NAME = 'ผู้อำนวยการเฉพาะด้าน (วิศวกรรมไฟฟ้า)' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Communicative Electrical Engineer' WHERE PL_NAME = 'วิศวกรไฟฟ้าสื่อสาร' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Civil Engineer' WHERE PL_NAME = 'วิศวกรโยธา' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Director (Civil Engineering)' WHERE PL_NAME = 'ผู้อำนวยการเฉพาะด้าน (วิศวกรรมโยธา)' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Survey Engineer' WHERE PL_NAME = 'วิศวกรรังวัด' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Director (Survey Engineering)' WHERE PL_NAME = 'ผู้อำนวยการเฉพาะด้าน (วิศวกรรมรังวัด)' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Metallurgical Engineer' WHERE PL_NAME = 'วิศวกรโลหการ' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Survey Engineer' WHERE PL_NAME = 'วิศวกรสำรวจ' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Director (Survey Engineering)' WHERE PL_NAME = 'ผู้อำนวยการเฉพาะด้าน (วิศวกรรมสำรวจ)' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Mining Engineer' WHERE PL_NAME = 'วิศวกรเหมืองแร่' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Director (Mining Engineering)' WHERE PL_NAME = 'ผู้อำนวยการเฉพาะด้าน (วิศวกรรมเหมืองแร่)' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Architect' WHERE PL_NAME = 'สถาปนิก' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Director (Architecture)' WHERE PL_NAME = 'ผู้อำนวยการเฉพาะด้าน (สถาปัตยกรรม)' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Town Planner' WHERE PL_NAME = 'นักผังเมือง' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Archivist' WHERE PL_NAME = 'นักจดหมายเหตุ' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Director (Archives)' WHERE PL_NAME = 'ผู้อำนวยการเฉพาะด้าน (จดหมายเหตุ)' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Librarian' WHERE PL_NAME = 'บรรณารักษ์' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Director (Librarian)' WHERE PL_NAME = 'ผู้อำนวยการเฉพาะด้าน (บรรณารักษ์)' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Archaeologist' WHERE PL_NAME = 'นักโบราณคดี' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Director (Archaeology)' WHERE PL_NAME = 'ผู้อำนวยการเฉพาะด้าน (โบราณคดี)' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Property Valuer' WHERE PL_NAME = 'นักประเมินราคาทรัพย์สิน' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Director (Property Valuation)' WHERE PL_NAME = 'ผู้อำนวยการเฉพาะด้าน (ประเมินราคาทรัพย์สิน)' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Sports Development Officer' WHERE PL_NAME = 'นักพัฒนาการกีฬา' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Tourism Development Officer' WHERE PL_NAME = 'นักพัฒนาการท่องเที่ยว' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Social Development Worker' WHERE PL_NAME = 'นักพัฒนาสังคม' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Curator' WHERE PL_NAME = 'ภัณฑารักษ์' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Director (Curator)' WHERE PL_NAME = 'ผู้อำนวยการเฉพาะด้าน (ภัณฑารักษ์)' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Ancient Language Official' WHERE PL_NAME = 'นักภาษาโบราณ' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Literateur' WHERE PL_NAME = 'นักวรรณศิลป์' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Town Planning Analyst' WHERE PL_NAME = 'นักวิเคราะห์ผังเมือง' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Land Expropriation Officer' WHERE PL_NAME = 'นักวิชาการจัดหาที่ดิน' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Director (Land Expropriation)' WHERE PL_NAME = 'ผู้อำนวยการเฉพาะด้าน (วิชาการจัดหาที่ดิน)' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Land Technical Officer' WHERE PL_NAME = 'นักวิชาการที่ดิน' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Community Development Specialist' WHERE PL_NAME = 'นักวิชาการพัฒนาชุมชน' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Skill Development Technical Officer' WHERE PL_NAME = 'นักวิชาการพัฒนาฝีมือแรงงาน' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Labour Specialist' WHERE PL_NAME = 'นักวิชาการแรงงาน' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Music and Drama Academician' WHERE PL_NAME = 'นักวิชาการละครและดนตรี' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Director (Music and Drama Academia)' WHERE PL_NAME = 'ผู้อำนวยการเฉพาะด้าน (วิชาการละครและดนตรี)' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Cultural Officer' WHERE PL_NAME = 'นักวิชาการวัฒนธรรม' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Religions Affairs Technical Officer' WHERE PL_NAME = 'นักวิชาการศาสนา' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Educator' WHERE PL_NAME = 'นักวิชาการศึกษา' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Special Educator' WHERE PL_NAME = 'นักวิชาการศึกษาพิเศษ' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Environmentalist' WHERE PL_NAME = 'นักวิชาการสิ่งแวดล้อม' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Vocational Training Technical Officer' WHERE PL_NAME = 'นักวิชาการอบรมและฝึกวิชาชีพ' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Instructor' WHERE PL_NAME = 'วิทยาจารย์' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Social Worker' WHERE PL_NAME = 'นักสังคมสงเคราะห์' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Literature Arts Official' WHERE PL_NAME = 'นักอักษรศาสตร์' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Director (Literature Arts)' WHERE PL_NAME = 'ผู้อำนวยการเฉพาะด้าน (อักษรศาสตร์)' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'General Service Officer หรือ Office Clerk' WHERE PL_NAME = 'เจ้าพนักงานธุรการ' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Supply Officer' WHERE PL_NAME = 'เจ้าพนักงานพัสดุ' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Correctional Officer' WHERE PL_NAME = 'เจ้าพนักงานราชทัณฑ์' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Medical Statistics Technician' WHERE PL_NAME = 'เจ้าพนักงานเวชสถิติ' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Statistical Officer' WHERE PL_NAME = 'เจ้าพนักงานสถิติ' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Assistant Scribe Officer' WHERE PL_NAME = 'เจ้าพนักงานอาลักษณ์' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Assistant Governing Officer' WHERE PL_NAME = 'เจ้าหน้าที่ปกครอง' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Local Government Contribution Officer' WHERE PL_NAME = 'เจ้าพนักงานส่งเสริมการปกครองท้องถิ่น' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Trade Personnel' WHERE PL_NAME = 'เจ้าพนักงานการพาณิชย์' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Finance and Accounting Officer' WHERE PL_NAME = 'เจ้าพนักงานการเงินและบัญชี' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Fiscal Officer' WHERE PL_NAME = 'เจ้าพนักงานการคลัง' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Weights and Measures Officer' WHERE PL_NAME = 'เจ้าพนักงานชั่งตวงวัด' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Money Checker' WHERE PL_NAME = 'เจ้าพนักงานดูเงิน' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Auditing Officer' WHERE PL_NAME = 'เจ้าพนักงานตรวจสอบบัญชี' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Mineral Resources Officer' WHERE PL_NAME = 'เจ้าพนักงานทรัพยากรธรณี' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Customs Officer' WHERE PL_NAME = 'เจ้าพนักงานศุลกากร' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Cooperative Promotion Officer' WHERE PL_NAME = 'เจ้าพนักงานส่งเสริมสหกรณ์' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Industrial Promotion Officer' WHERE PL_NAME = 'เจ้าพนักงานส่งเสริมอุตสาหกรรม' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Excise Officer' WHERE PL_NAME = 'เจ้าพนักงานสรรพสามิต' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Revenue Officer' WHERE PL_NAME = 'เจ้าพนักงานสรรพากร' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Assistant Intelligence Officer' WHERE PL_NAME = 'เจ้าพนักงานการข่าว' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Transport Officer' WHERE PL_NAME = 'เจ้าพนักงานขนส่ง' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Navigation Officer' WHERE PL_NAME = 'เจ้าพนักงานเดินเรือ' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Assistant Public Relations Officer' WHERE PL_NAME = 'เจ้าพนักงานเผยแพร่ประชาสัมพันธ์' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Telecommunications Officer' WHERE PL_NAME = 'เจ้าพนักงานสื่อสาร' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Aviation Communication Officer' WHERE PL_NAME = 'เจ้าพนักงานสื่อสารการบิน' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Audio-Visual Officer' WHERE PL_NAME = 'เจ้าพนักงานโสตทัศนศึกษา' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Announcer and Reporter' WHERE PL_NAME = 'ผู้ประกาศและรายงานข่าว' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Agricultural Officer' WHERE PL_NAME = 'เจ้าพนักงานการเกษตร' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Home Economics Officer' WHERE PL_NAME = 'เจ้าพนักงานเคหกิจการเกษตร' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Irrigation Technician' WHERE PL_NAME = 'นายช่างชลประทาน' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Director (Irrigation Operation)' WHERE PL_NAME = 'ผู้อำนวยการเฉพาะด้าน (ปฏิบัติงานช่างชลประทาน)' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Fishery Officer' WHERE PL_NAME = 'เจ้าพนักงานประมง' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Director (Fishery Operation)' WHERE PL_NAME = 'ผู้อำนวยการเฉพาะด้าน (ปฏิบัติงานประมง)' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Forestry Officer' WHERE PL_NAME = 'เจ้าพนักงานป่าไม้' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Animal Husbandry Officer' WHERE PL_NAME = 'เจ้าพนักงานสัตวบาล' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Director (Animal Husbandry Operation)' WHERE PL_NAME = 'ผู้อำนวยการเฉพาะด้าน (ปฏิบัติงานสัตวบาล)' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Scientific Officer' WHERE PL_NAME = 'เจ้าพนักงานวิทยาศาสตร์' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Meteorological Officer' WHERE PL_NAME = 'เจ้าพนักงานอุตุนิยมวิทยา' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Hydrological Officer' WHERE PL_NAME = 'เจ้าพนักงานอุทกวิทยา' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Dental Assistant' WHERE PL_NAME = 'เจ้าพนักงานทันตสาธารณสุข' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Pharmaceutical Assistant หรือ Pharmacy Technician' WHERE PL_NAME = 'เจ้าพนักงานเภสัชกรรม' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Nutrition Officer' WHERE PL_NAME = 'โภชนากร' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Radiographer Technician' WHERE PL_NAME = 'เจ้าพนักงานรังสีการแพทย์' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Medical Technician Assistant หรือ Medical Science Technician' WHERE PL_NAME = 'เจ้าพนักงานวิทยาศาสตร์การแพทย์' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Rehabilitation Medical Technician' WHERE PL_NAME = 'เจ้าพนักงานเวชกรรมฟื้นฟู' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Public Health Officer' WHERE PL_NAME = 'เจ้าพนักงานสาธารณสุข' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Vocational Therapy Technician' WHERE PL_NAME = 'เจ้าพนักงานอาชีวบำบัด' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Technical Nurse' WHERE PL_NAME = 'พยาบาลเทคนิค' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Veterinary Officer' WHERE PL_NAME = 'สัตวแพทย์' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Director (Veterinary Officer)' WHERE PL_NAME = 'ผู้อำนวยการเฉพาะด้าน (ปฏิบัติงานสัตวแพทย์)' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Costumer' WHERE PL_NAME = 'ช่างอาภรณ์' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Printer' WHERE PL_NAME = 'นายช่างพิมพ์' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Graphic Designer' WHERE PL_NAME = 'นายช่างศิลป์' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Art Technician' WHERE PL_NAME = 'นายช่างศิลปกรรม' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Director (Art Design)' WHERE PL_NAME = 'ผู้อำนวยการเฉพาะด้าน (ปฏิบัติงานช่างศิลปกรรม)' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Founder' WHERE PL_NAME = 'นายช่างหล่อ' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Prosthetic and Orthotic Technician' WHERE PL_NAME = 'ช่างกายอุปกรณ์' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Computer Operator' WHERE PL_NAME = 'เจ้าพนักงานเครื่องคอมพิวเตอร์' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Dredging Technician' WHERE PL_NAME = 'นายช่างขุดลอก' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Draftsman' WHERE PL_NAME = 'นายช่างเขียนแบบ' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Mechanic' WHERE PL_NAME = 'นายช่างเครื่องกล' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Vehicle Inspector' WHERE PL_NAME = 'นายช่างตรวจสภาพรถ' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Dental Technician' WHERE PL_NAME = 'ช่างทันตกรรม' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Technician' WHERE PL_NAME = 'นายช่างเทคนิค' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Director (Technician Operation)' WHERE PL_NAME = 'ผู้อำนวยการเฉพาะด้าน (ปฏิบัติงานช่างเทคนิค)' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Electrician' WHERE PL_NAME = 'นายช่างไฟฟ้า' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Photographer' WHERE PL_NAME = 'นายช่างภาพ' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Civil Works Technician' WHERE PL_NAME = 'นายช่างโยธา' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Director (Civil Works Operation)' WHERE PL_NAME = 'ผู้อำนวยการเฉพาะด้าน (ปฏิบัติงานช่างโยธา)' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Surveyor' WHERE PL_NAME = 'นายช่างรังวัด' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Director (Surveyor Operation)' WHERE PL_NAME = 'ผู้อำนวยการเฉพาะด้าน (ปฏิบัติงานช่างรังวัด)' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Metal Work Technician' WHERE PL_NAME = 'นายช่างโลหะ' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Surveyor' WHERE PL_NAME = 'นายช่างสำรวจ' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Mining Technician' WHERE PL_NAME = 'นายช่างเหมืองแร่' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Ship Design Technician' WHERE PL_NAME = 'นายช่างออกแบบเรือ' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Aeronautical Mechanic' WHERE PL_NAME = 'นายช่างอากาศยาน' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Factory Inspection Officer' WHERE PL_NAME = 'เจ้าพนักงานตรวจโรงงาน' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Calligrapher' WHERE PL_NAME = 'เจ้าพนักงานลิขิต' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Industrial Products Designing Officer' WHERE PL_NAME = 'เจ้าพนักงานออกแบบผลิตภัณฑ์' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Special Education Teacher' WHERE PL_NAME = 'ครูการศึกษาพิเศษ' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Singer' WHERE PL_NAME = 'คีตศิลปิน' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Director (Singing)' WHERE PL_NAME = 'ผู้อำนวยการเฉพาะด้าน (คีตศิลป์)' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Land Officer' WHERE PL_NAME = 'เจ้าพนักงานที่ดิน' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Musician' WHERE PL_NAME = 'ดุริยางคศิลปิน' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Director (Music)' WHERE PL_NAME = 'ผู้อำนวยการเฉพาะด้าน (ดุริยางคศิลป์)' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Dancer' WHERE PL_NAME = 'นาฏศิลปิน' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Director (Dance)' WHERE PL_NAME = 'ผู้อำนวยการเฉพาะด้าน (นาฏศิลป์)' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Religions Affairs Officer' WHERE PL_NAME = 'เจ้าพนักงานการศาสนา' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Assistant Archivist' WHERE PL_NAME = 'เจ้าพนักงานจดหมายเหตุ' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Assistant Property Valuer' WHERE PL_NAME = 'เจ้าพนักงานประเมินราคาทรัพย์สิน' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Community Development Officer' WHERE PL_NAME = 'เจ้าพนักงานพัฒนาชุมชน' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Skill Development Officer' WHERE PL_NAME = 'เจ้าพนักงานพัฒนาฝีมือแรงงาน' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Social Development Officer' WHERE PL_NAME = 'เจ้าพนักงานพัฒนาสังคม' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Museum Officer' WHERE PL_NAME = 'เจ้าพนักงานพิพิธภัณฑ์' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Labour Officer' WHERE PL_NAME = 'เจ้าพนักงานแรงงาน' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Cultural Affairs Operator' WHERE PL_NAME = 'เจ้าพนักงานวัฒนธรรม' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Library Service Officer' WHERE PL_NAME = 'เจ้าพนักงานห้องสมุด' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Vocational Training Officer' WHERE PL_NAME = 'เจ้าพนักงานอบรมและฝึกวิชาชีพ' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LINE SET PL_ENGNAME = 'Chaplain' WHERE PL_NAME = 'อนุศาสนาจารย์' and PL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			}

			$count_data = add_field("PER_LEVEL", "LEVEL_ENGNAME","MEMO", "1000", "NULL");
			if (!$count_data) {
				$cmd = " UPDATE PER_LEVEL SET LEVEL_ENGNAME = 'Operational Level' WHERE LEVEL_NO = 'O1' and LEVEL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LEVEL SET LEVEL_ENGNAME = 'Experienced Level' WHERE LEVEL_NO = 'O2' and LEVEL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LEVEL SET LEVEL_ENGNAME = 'Senior Level' WHERE LEVEL_NO = 'O3' and LEVEL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LEVEL SET LEVEL_ENGNAME = 'Highly Skilled Level' WHERE LEVEL_NO = 'O4' and LEVEL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LEVEL SET LEVEL_ENGNAME = 'Practitioner Level' WHERE LEVEL_NO = 'K1' and LEVEL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LEVEL SET LEVEL_ENGNAME = 'Professional Level' WHERE LEVEL_NO = 'K2' and LEVEL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LEVEL SET LEVEL_ENGNAME = 'Senior Professional Level' WHERE LEVEL_NO = 'K3' and LEVEL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LEVEL SET LEVEL_ENGNAME = 'Expert Level' WHERE LEVEL_NO = 'K4' and LEVEL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LEVEL SET LEVEL_ENGNAME = 'Advisory Level' WHERE LEVEL_NO = 'K5' and LEVEL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LEVEL SET LEVEL_ENGNAME = 'Primary Level' WHERE LEVEL_NO = 'D1' and LEVEL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LEVEL SET LEVEL_ENGNAME = 'Higher Level' WHERE LEVEL_NO = 'D2' and LEVEL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LEVEL SET LEVEL_ENGNAME = 'Primary Level' WHERE LEVEL_NO = 'M1' and LEVEL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LEVEL SET LEVEL_ENGNAME = 'Higher Level' WHERE LEVEL_NO = 'M2' and LEVEL_ENGNAME is NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			}
			add_field("PER_PERSONALPIC", "PIC_YEAR","VARCHAR", "4", "NULL");
			add_field("PER_TRAINING", "TRN_COST","NUMBER", "10,2", "NULL");

			if ($BKK_FLAG !=  1) {
				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
								  PL_TYPE, CL_NAME, LEVEL_NO_MIN, LEVEL_NO_MAX)
								  VALUES ('946001', 'เจ้าหน้าที่งานรักษาความสะอาด', 'จ.งานรักษาความสะอาด', 1, $SESS_USERID, '$UPDATE_DATE', 1, 
								  'ปฏิบัติงาน หรือ ชำนาญงาน หรือ อาวุโส', 'O1', 'O3') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
								  PL_TYPE, CL_NAME, LAYER_TYPE, LEVEL_NO_MIN, LEVEL_NO_MAX)
								  VALUES ('536020', 'นักเทคโนโลยีหัวใจและทรวงอก', 'นักเทคโนโลยีหัวใจและทรวงอก', 1, $SESS_USERID, '$UPDATE_DATE', 2, 
								  'ปฏิบัติการ หรือ ชำนาญการ หรือ ชำนาญการพิเศษ', 1, 'K1', 'K3') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 
			}

			$cmd = " INSERT INTO PER_DECORATION (DC_CODE, DC_SHORTNAME, DC_NAME, DC_ORDER, DC_TYPE, DC_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  DC_ENG_NAME, DC_FLAG)
							  VALUES ('01', 'ร.ม.ภ.', 'ราชมิตราภรณ์', 1, 1, 1, $SESS_USERID, '$UPDATE_DATE', 'The Most Auspicious Order of the Rajamitrabhorn.', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_DECORATION (DC_CODE, DC_SHORTNAME, DC_NAME, DC_ORDER, DC_TYPE, DC_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  DC_ENG_NAME, DC_FLAG)
							  VALUES ('02', 'ม.จ.ก.', 'มหาจักรีบรมราชวงศ์', 2, 1, 1, $SESS_USERID, '$UPDATE_DATE', 'Knight of the Most Illustrious Order of the Royal House of Chakri.', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_DECORATION (DC_CODE, DC_SHORTNAME, DC_NAME, DC_ORDER, DC_TYPE, DC_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  DC_ENG_NAME, DC_FLAG)
							  VALUES ('03', 'น.ร.', 'นพรัตนราชวราภรณ์', 3, 1, 1, $SESS_USERID, '$UPDATE_DATE', 'Knight of the Ancient and Auspicious Order of the Nine Gems.', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_DECORATION (DC_CODE, DC_SHORTNAME, DC_NAME, DC_ORDER, DC_TYPE, DC_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  DC_ENG_NAME, DC_FLAG)
							  VALUES ('04', 'ป.จ.ว.', 'ปฐมจุลจอมเกล้าวิเศษ', 4, 1, 1, $SESS_USERID, '$UPDATE_DATE', 'Knight Grand Cordon (Special Class) of the Most Illustrious Order of Chula Chom Klao.', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_DECORATION (DC_CODE, DC_SHORTNAME, DC_NAME, DC_ORDER, DC_TYPE, DC_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  DC_ENG_NAME, DC_FLAG)
							  VALUES ('05', 'ร.ว.', 'รัตนวราภรณ์', 5, 1, 1, $SESS_USERID, '$UPDATE_DATE', 'The Ratana Varabhorn Order of Merit.', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_DECORATION (DC_CODE, DC_SHORTNAME, DC_NAME, DC_ORDER, DC_TYPE, DC_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  DC_ENG_NAME, DC_FLAG)
							  VALUES ('06', 'ป.จ.', 'ปฐมจุลจอมเกล้า', 6, 1, 1, $SESS_USERID, '$UPDATE_DATE', 'Knight Grand Cross (First Class) of the Most Illustrious Order of Chula Chom Klao.', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_DECORATION (DC_CODE, DC_SHORTNAME, DC_NAME, DC_ORDER, DC_TYPE, DC_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  DC_ENG_NAME, DC_FLAG)
							  VALUES ('07', 'ส.ร.', 'รามาธิบดี ชั้นที่ ๑ (เสนางคะบดี)', 7, 1, 1, $SESS_USERID, '$UPDATE_DATE', 'Senangapati (Knight Grand Commander) of the Honourable Order of Rama.', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_DECORATION SET DC_ORDER = 8, DC_ENG_NAME = 'Knight Grand Cordon (Special Class) of the Most Exalted Order of the White Elephant.' 
							  WHERE DC_NAME = 'มหาปรมาภรณ์ช้างเผือก' AND DC_ENG_NAME IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_DECORATION SET DC_ORDER = 9, DC_ENG_NAME = 'Knight Grand Cordon (Special Class) of the Most Noble Order of the Crown of Thailand.' 
							  WHERE DC_NAME = 'มหาวชิรมงกุฏ' AND DC_ENG_NAME IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_DECORATION SET DC_ORDER = 10, DC_ENG_NAME = 'Knight Grand Cross (First Class) of the Most Exalted Order of the White Elephant.' 
							  WHERE DC_NAME = 'ประถมาภรณ์ช้างเผือก' AND DC_ENG_NAME IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_DECORATION SET DC_ORDER = 11, DC_ENG_NAME = 'Knight Grand Cross (First Class) of the Most Noble Order of the Crown of Thailand.' 
							  WHERE DC_NAME = 'ประถมาภรณ์มงกุฏไทย' AND DC_ENG_NAME IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_DECORATION (DC_CODE, DC_SHORTNAME, DC_NAME, DC_ORDER, DC_TYPE, DC_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  DC_ENG_NAME, DC_FLAG)
							  VALUES ('12', 'ป.ภ.', 'ปฐมดิเรกคุณาภรณ์', 12, 1, 1, $SESS_USERID, '$UPDATE_DATE', 'Knight Grand Cross (First Class) of the Most Admirable Order of the Diredgunabhorn.', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_DECORATION (DC_CODE, DC_SHORTNAME, DC_NAME, DC_ORDER, DC_TYPE, DC_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  DC_ENG_NAME, DC_FLAG)
							  VALUES ('13', 'ท.จ.ว.', 'ทุติยจุลจอมเกล้าวิเศษ', 13, 2, 1, $SESS_USERID, '$UPDATE_DATE', 'Knight Grand Commander (Second Class, higher grade) of the Most Illustrious Order of Chula Chom Klao.', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_DECORATION (DC_CODE, DC_SHORTNAME, DC_NAME, DC_ORDER, DC_TYPE, DC_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  DC_ENG_NAME, DC_FLAG)
							  VALUES ('14', 'ม.ร.', 'รามาธิบดี ชั้นที่ ๒ (มหาโยธิน)', 14, 2, 1, $SESS_USERID, '$UPDATE_DATE', 'Maha Yodhin (Knight Commander) of the Honorable Order of Rama.', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_DECORATION SET DC_ORDER = 15, DC_ENG_NAME = 'Knight Commander (Second Class) of the Most Exalted Order of the White Elephant.' 
							  WHERE DC_NAME = 'ทวีติยาภรณ์ช้างเผือก' AND DC_ENG_NAME IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_DECORATION SET DC_ORDER = 16, DC_ENG_NAME = 'Knight Commander (Second Class) of the Most Noble Order of the Crown of Thailand.' 
							  WHERE DC_NAME = 'ทวีติยาภรณ์มงกุฏไทย' AND DC_ENG_NAME IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_DECORATION (DC_CODE, DC_SHORTNAME, DC_NAME, DC_ORDER, DC_TYPE, DC_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  DC_ENG_NAME, DC_FLAG)
							  VALUES ('17', 'ท.ภ.', 'ทุติยดิเรกคุณาภรณ์', 17, 2, 1, $SESS_USERID, '$UPDATE_DATE', 'Knight Commander (Second Class) of the Most Admirable Order of the Direkgunabhorn.', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_DECORATION (DC_CODE, DC_SHORTNAME, DC_NAME, DC_ORDER, DC_TYPE, DC_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  DC_ENG_NAME, DC_FLAG)
							  VALUES ('18', 'ท.จ.', 'ทุติยจุลจอมเกล้า', 18, 2, 1, $SESS_USERID, '$UPDATE_DATE', 'Knight Commander (Second Class, lower grade) of the Most Illustrious Order of Chula Chom Klao.', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_DECORATION (DC_CODE, DC_SHORTNAME, DC_NAME, DC_ORDER, DC_TYPE, DC_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  DC_ENG_NAME, DC_FLAG)
							  VALUES ('19', 'ย.ร.', 'รามาธิบดี ชั้นที่ ๓ (โยธิน)', 19, 2, 1, $SESS_USERID, '$UPDATE_DATE', 'Yodhin (Commander) of the Honourable of Rama.', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_DECORATION (DC_CODE, DC_SHORTNAME, DC_NAME, DC_ORDER, DC_TYPE, DC_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  DC_ENG_NAME, DC_FLAG)
							  VALUES ('20', 'ว.ภ.', ' วัลลภาภรณ์', 20, 2, 1, $SESS_USERID, '$UPDATE_DATE', 'The Vallabhabhorn Order.', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_DECORATION (DC_CODE, DC_SHORTNAME, DC_NAME, DC_ORDER, DC_TYPE, DC_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  DC_ENG_NAME, DC_FLAG)
							  VALUES ('21', 'ต.จ.ว.', 'ตติยจุลจอมเกล้าวิเศษ', 21, 2, 1, $SESS_USERID, '$UPDATE_DATE', 'Grand Companion (Thrid Class, higher grade) of the Most Illustrious Order of Chula Chom Klao.', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_DECORATION (DC_CODE, DC_SHORTNAME, DC_NAME, DC_ORDER, DC_TYPE, DC_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  DC_ENG_NAME, DC_FLAG)
							  VALUES ('22', 'อ.ร.', 'รามาธิบดี ชั้นที่ ๔ (อัศวิน)', 22, 2, 1, $SESS_USERID, '$UPDATE_DATE', 'Asvin (Companion) of the Honourable Order of Rama.', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_DECORATION SET DC_ORDER = 23, DC_ENG_NAME = 'Commander (Third Class) of the Most Exalted Order of the White Elephant.' 
							  WHERE DC_NAME = 'ตริตาภรณ์ช้างเผือก' AND DC_ENG_NAME IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_DECORATION SET DC_ORDER = 24, DC_ENG_NAME = 'Commander (Third Class) of the Most Noble Order of the Crown of Thailand.' 
							  WHERE DC_NAME = 'ตริตาภรณ์มงกุฏไทย' AND DC_ENG_NAME IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_DECORATION (DC_CODE, DC_SHORTNAME, DC_NAME, DC_ORDER, DC_TYPE, DC_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  DC_ENG_NAME, DC_FLAG)
							  VALUES ('25', 'ต.ภ.', 'ตติยดิเรกคุณาภรณ์', 25, 2, 1, $SESS_USERID, '$UPDATE_DATE', 'Commander (Third Class) of the Most Admirable Order of the Direkgunabhorn.', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_DECORATION (DC_CODE, DC_SHORTNAME, DC_NAME, DC_ORDER, DC_TYPE, DC_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  DC_ENG_NAME, DC_FLAG)
							  VALUES ('26', 'รามกีรติ', 'รามกีรติ ลูกเสือสดุดีชั้นพิเศษ', 26, 2, 1, $SESS_USERID, '$UPDATE_DATE', 'The Boy Scout Citation Medal (Special Class).', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_DECORATION (DC_CODE, DC_SHORTNAME, DC_NAME, DC_ORDER, DC_TYPE, DC_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  DC_ENG_NAME, DC_FLAG)
							  VALUES ('27', 'ต.จ.', 'ตติยจุลจอมเกล้า', 27, 2, 1, $SESS_USERID, '$UPDATE_DATE', 'Companion (Third Class, Lower grade) of the Most Illustrious Order of Chula Chom Klao.', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_DECORATION SET DC_ORDER = 28, DC_ENG_NAME = 'Companion (Fourth Class) of the Most Exalted Order of the White Elephant.' 
							  WHERE DC_NAME = 'จัตุรถาภรณ์ช้างเผือก' AND DC_ENG_NAME IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_DECORATION SET DC_ORDER = 29, DC_ENG_NAME = 'Companion (Fourth Class) of the Most Noble Order of the Crown of Thailand.' 
							  WHERE DC_NAME = 'จัตุรถาภรณ์มงกุฏไทย' AND DC_ENG_NAME IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_DECORATION (DC_CODE, DC_SHORTNAME, DC_NAME, DC_ORDER, DC_TYPE, DC_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  DC_ENG_NAME, DC_FLAG)
							  VALUES ('30', 'จ.ภ.', 'จตุตถดิเรกคุณาภรณ์', 30, 2, 1, $SESS_USERID, '$UPDATE_DATE', 'Companion (Fourth Class) of the Most Admirable Order of the Direkgunabhorn.', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_DECORATION (DC_CODE, DC_SHORTNAME, DC_NAME, DC_ORDER, DC_TYPE, DC_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  DC_ENG_NAME, DC_FLAG)
							  VALUES ('31', 'ต.อ.จ.', 'ตติยานุจุลจอมเกล้า', 31, 2, 1, $SESS_USERID, '$UPDATE_DATE', 'Junior Companion of the Most Illustrious Order of Chula Chom Klao.', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_DECORATION SET DC_ORDER = 32, DC_ENG_NAME = 'Fourth Class of the Most Illustrious Order of Chula Chom Klao.' 
							  WHERE DC_NAME = 'จตุตถจุลจอมเกล้า' AND DC_ENG_NAME IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_DECORATION SET DC_ORDER = 33, DC_ENG_NAME = 'Member (Fifth Class) of the Most Exalted Order of the White Elephant.' 
							  WHERE DC_NAME = 'เบญจมาภรณ์ช้างเผือก' AND DC_ENG_NAME IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_DECORATION SET DC_ORDER = 34, DC_ENG_NAME = 'Member (Fifth Class) of the Most Noble Order of the Crown of Thailand.' 
							  WHERE DC_NAME = 'เบญจมาภรณ์มงกุฏไทย' AND DC_ENG_NAME IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_DECORATION (DC_CODE, DC_SHORTNAME, DC_NAME, DC_ORDER, DC_TYPE, DC_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  DC_ENG_NAME, DC_FLAG)
							  VALUES ('35', 'บ.ภ.', 'เบญจมดิเรกคุณาภรณ์', 35, 2, 1, $SESS_USERID, '$UPDATE_DATE', 'Member (Fifth Class) of the Most Admirable Order of the Direkgunabhorn.', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_DECORATION (DC_CODE, DC_SHORTNAME, DC_NAME, DC_ORDER, DC_TYPE, DC_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  DC_ENG_NAME, DC_FLAG)
							  VALUES ('36', 'ว.ม.ล.', 'ตราวชิรมาลา', 36, 2, 1, $SESS_USERID, '$UPDATE_DATE', 'Member of the Vajira Mala Order.', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_DECORATION (DC_CODE, DC_SHORTNAME, DC_NAME, DC_ORDER, DC_TYPE, DC_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  DC_ENG_NAME, DC_FLAG)
							  VALUES ('37', 'ร.ม.ก.', 'เหรียญรามมาลา เข็มกล้ากลางสมร', 1, 3, 1, $SESS_USERID, '$UPDATE_DATE', 'Member of The Rama Medal for Gallantry in Action of the Honourable Order of Rama.', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_DECORATION (DC_CODE, DC_SHORTNAME, DC_NAME, DC_ORDER, DC_TYPE, DC_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  DC_ENG_NAME, DC_FLAG)
							  VALUES ('38', 'ร.ม.', 'เหรียญรามมาลา', 2, 3, 1, $SESS_USERID, '$UPDATE_DATE', 'Member of The Rama Medal of the Honourable Order of Tama.', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_DECORATION (DC_CODE, DC_SHORTNAME, DC_NAME, DC_ORDER, DC_TYPE, DC_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  DC_ENG_NAME, DC_FLAG)
							  VALUES ('39', 'ร.ด.ม. (ห)', 'เหรียญกล้าหาญ หรือเหรียญดุษฎีมาลา เข็มกล้าหาญ', 3, 3, 1, $SESS_USERID, '$UPDATE_DATE', 'The Bravery Medal.', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_DECORATION (DC_CODE, DC_SHORTNAME, DC_NAME, DC_ORDER, DC_TYPE, DC_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  DC_ENG_NAME, DC_FLAG)
							  VALUES ('40', 'ช.ส.', 'เหรียญชัยสมรภูมิ', 4, 3, 1, $SESS_USERID, '$UPDATE_DATE', 'The Victory Medal.', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_DECORATION (DC_CODE, DC_SHORTNAME, DC_NAME, DC_ORDER, DC_TYPE, DC_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  DC_ENG_NAME, DC_FLAG)
							  VALUES ('41', 'ส.ช.๑', 'เหรียญพิทักษ์เสรีชน ชั้นที่ ๑', 5, 3, 1, $SESS_USERID, '$UPDATE_DATE', 'Freeman Safeguarding Medal (First Class).', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_DECORATION (DC_CODE, DC_SHORTNAME, DC_NAME, DC_ORDER, DC_TYPE, DC_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  DC_ENG_NAME, DC_FLAG)
							  VALUES ('42', 'ส.ช.๒/๑', 'เหรียญพิทักษ์เสรีชน ชั้นที่ ๒ ประเภทที่ ๑', 6, 3, 1, $SESS_USERID, '$UPDATE_DATE', 'Knight', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_DECORATION (DC_CODE, DC_SHORTNAME, DC_NAME, DC_ORDER, DC_TYPE, DC_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  DC_ENG_NAME, DC_FLAG)
							  VALUES ('43', 'ร.น.', 'เหรียญราชนิยม', 7, 3, 1, $SESS_USERID, '$UPDATE_DATE', 'The Rajaniyom Medal.', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_DECORATION (DC_CODE, DC_SHORTNAME, DC_NAME, DC_ORDER, DC_TYPE, DC_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  DC_ENG_NAME, DC_FLAG)
							  VALUES ('44', 'ร.ม.ฮ.', 'เหรียญปราบฮ่อ', 8, 3, 1, $SESS_USERID, '$UPDATE_DATE', 'The Haw Campaign Medal.', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_DECORATION (DC_CODE, DC_SHORTNAME, DC_NAME, DC_ORDER, DC_TYPE, DC_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  DC_ENG_NAME, DC_FLAG)
							  VALUES ('45', 'ร.ส.', 'เหรียญงานพระราชสงครามยุโรป', 9, 3, 1, $SESS_USERID, '$UPDATE_DATE', 'The War Medal of B.E. 2461', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_DECORATION (DC_CODE, DC_SHORTNAME, DC_NAME, DC_ORDER, DC_TYPE, DC_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  DC_ENG_NAME, DC_FLAG)
							  VALUES ('46', 'พ.ร.ธ.', 'เหรียญพิทักษ์รัฐธรรมนูญ', 10, 3, 1, $SESS_USERID, '$UPDATE_DATE', 'The Safeguarding the Constitution Medal.', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_DECORATION (DC_CODE, DC_SHORTNAME, DC_NAME, DC_ORDER, DC_TYPE, DC_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  DC_ENG_NAME, DC_FLAG)
							  VALUES ('47', 'ส.ช. ๒/๒', 'เหรียญพิทักษ์เสรีชน ชั้นที่ ๒ ประเภทที่ ๒', 11, 3, 1, $SESS_USERID, '$UPDATE_DATE', 'Freeman Safeguarding Medal (Second Class, Second Category).', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_DECORATION (DC_CODE, DC_SHORTNAME, DC_NAME, DC_ORDER, DC_TYPE, DC_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  DC_ENG_NAME, DC_FLAG)
							  VALUES ('48', 'ศ.ม.', 'เหรียญศานติมาลา', 12, 3, 1, $SESS_USERID, '$UPDATE_DATE', 'The Santi Mala Medal.', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_DECORATION (DC_CODE, DC_SHORTNAME, DC_NAME, DC_ORDER, DC_TYPE, DC_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  DC_ENG_NAME, DC_FLAG)
							  VALUES ('51', 'ร.ด.ม.', 'เหรียญดุษฎีมาลา เข็มราชการแผ่นดิน หรือเข็มศิลปวิทยา', 1, 4, 1, $SESS_USERID, '$UPDATE_DATE', 'The Dushdi Mala.', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_DECORATION (DC_CODE, DC_SHORTNAME, DC_NAME, DC_ORDER, DC_TYPE, DC_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  DC_ENG_NAME, DC_FLAG)
							  VALUES ('52', 'ช.ร.', 'เหรียญช่วยราชการเขตภายใน', 2, 4, 1, $SESS_USERID, '$UPDATE_DATE', 'The Medal of Service Rendered in the Interior.', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_DECORATION (DC_CODE, DC_SHORTNAME, DC_NAME, DC_ORDER, DC_TYPE, DC_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  DC_ENG_NAME, DC_FLAG)
							  VALUES ('53', 'ช.ด.', 'เหรียญราชการชายแดน', 3, 4, 1, $SESS_USERID, '$UPDATE_DATE', 'The Border Service Medal.', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_DECORATION SET DC_ORDER = 4, DC_TYPE = 4, DC_ENG_NAME = 'The Gold Medal (Sixth Class) of the White Elephant.' 
							  WHERE DC_NAME = 'เหรียญทองช้างเผือก' AND DC_ENG_NAME IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_DECORATION SET DC_ORDER = 5, DC_TYPE = 4, DC_ENG_NAME = 'The Gold Medal (Sixth Class) of the Crown of Thailand.' 
							  WHERE DC_NAME = 'เหรียญทองมงกุฎไทย' AND DC_ENG_NAME IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_DECORATION (DC_CODE, DC_SHORTNAME, DC_NAME, DC_ORDER, DC_TYPE, DC_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  DC_ENG_NAME, DC_FLAG)
							  VALUES ('56', 'ร.ท.ภ.', 'เหรียญทองดิเรกคุณาภรณ์', 6, 4, 1, $SESS_USERID, '$UPDATE_DATE', 'The Gold Medal (Sixth Class) of the Direkgunabhorn.', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_DECORATION SET DC_ORDER = 7, DC_TYPE = 4, DC_ENG_NAME = 'The Silver Medal (Seventh Class) of the White Elephant.' 
							  WHERE DC_NAME = 'เหรียญเงินช้างเผือก' AND DC_ENG_NAME IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_DECORATION SET DC_ORDER = 8, DC_TYPE = 4, DC_ENG_NAME = 'The Silver Medal (Seventh Class) of the Crown of Thailand.' 
							  WHERE DC_NAME = 'เหรียญเงินมงกุฎไทย' AND DC_ENG_NAME IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_DECORATION (DC_CODE, DC_SHORTNAME, DC_NAME, DC_ORDER, DC_TYPE, DC_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  DC_ENG_NAME, DC_FLAG)
							  VALUES ('59', 'ร.ง.ภ.', 'เหรียญเงินดิเรกคุณาภรณ์', 9, 4, 1, $SESS_USERID, '$UPDATE_DATE', 'The Silver Medal (Seventh Class) of the Direkgunabhorn.', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_DECORATION (DC_CODE, DC_SHORTNAME, DC_NAME, DC_ORDER, DC_TYPE, DC_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  DC_ENG_NAME, DC_FLAG)
							  VALUES ('60', 'ร.จ.ม.', 'เหรียญจักรมาลา', 10, 4, 1, $SESS_USERID, '$UPDATE_DATE', 'The Chakra Mala Medal.', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_DECORATION SET DC_ORDER = 10, DC_TYPE = 4, DC_ENG_NAME = 'The Chakrabarti Mala Medal.' 
							  WHERE DC_NAME = 'เหรียญจักรพรรดิมาลา' AND DC_ENG_NAME IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_DECORATION (DC_CODE, DC_SHORTNAME, DC_NAME, DC_ORDER, DC_TYPE, DC_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  DC_ENG_NAME, DC_FLAG)
							  VALUES ('62', 'ร.ศ.ท.', 'เหรียญศารทูลมาลา', 11, 4, 1, $SESS_USERID, '$UPDATE_DATE', 'The Salatul Mala Medal.', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_DECORATION (DC_CODE, DC_SHORTNAME, DC_NAME, DC_ORDER, DC_TYPE, DC_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  DC_ENG_NAME, DC_FLAG)
							  VALUES ('63', 'ร.บ.ม.', 'เหรียญบุษปมาลา', 12, 4, 1, $SESS_USERID, '$UPDATE_DATE', 'The Pushpa Mala Medal.', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_DECORATION (DC_CODE, DC_SHORTNAME, DC_NAME, DC_ORDER, DC_TYPE, DC_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  DC_ENG_NAME, DC_FLAG)
							  VALUES ('64', 'รมภ', 'เหรียญลูกเสือสรรเสริญ ชั้นที่ ๑', 13, 4, 1, $SESS_USERID, '$UPDATE_DATE', 'The Boy Scout Commendation Medal (First Class).', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_DECORATION (DC_CODE, DC_SHORTNAME, DC_NAME, DC_ORDER, DC_TYPE, DC_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  DC_ENG_NAME, DC_FLAG)
							  VALUES ('65', 'รมภ', 'เหรียญลูกเสือสรรเสริญ ชั้นที่ ๒', 14, 4, 1, $SESS_USERID, '$UPDATE_DATE', 'The Boy Scout Commendation Medal (Second Class).', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_DECORATION (DC_CODE, DC_SHORTNAME, DC_NAME, DC_ORDER, DC_TYPE, DC_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  DC_ENG_NAME, DC_FLAG)
							  VALUES ('66', 'รมภ', 'เหรียญลูกเสือสรรเสริญ ชั้นที่ ๓', 15, 4, 1, $SESS_USERID, '$UPDATE_DATE', 'The Boy Scout Commendation Medal (Third Class).', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_DECORATION (DC_CODE, DC_SHORTNAME, DC_NAME, DC_ORDER, DC_TYPE, DC_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  DC_ENG_NAME, DC_FLAG)
							  VALUES ('67', 'รมภ', 'เหรียญลูกเสือสดุดี ชั้นที่ ๑', 16, 4, 1, $SESS_USERID, '$UPDATE_DATE', 'The Boy Scout Citation Medal (First Class).', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_DECORATION (DC_CODE, DC_SHORTNAME, DC_NAME, DC_ORDER, DC_TYPE, DC_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  DC_ENG_NAME, DC_FLAG)
							  VALUES ('68', 'รมภ', 'เหรียญลูกเสือสดุดี ชั้นที่ ๒', 17, 4, 1, $SESS_USERID, '$UPDATE_DATE', 'The Boy Scout Citation Medal (Second Class).', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_DECORATION (DC_CODE, DC_SHORTNAME, DC_NAME, DC_ORDER, DC_TYPE, DC_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  DC_ENG_NAME, DC_FLAG)
							  VALUES ('69', 'รมภ', 'เหรียญลูกเสือสดุดี ชั้นที่ ๓', 18, 4, 1, $SESS_USERID, '$UPDATE_DATE', 'The Boy Scout Citation Medal (Third Class).', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_DECORATION (DC_CODE, DC_SHORTNAME, DC_NAME, DC_ORDER, DC_TYPE, DC_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  DC_ENG_NAME, DC_FLAG)
							  VALUES ('70', 'รมภ', 'เหรียญลูกเสือยั่งยืน', 19, 4, 1, $SESS_USERID, '$UPDATE_DATE', 'The Sustainable Scout Medal of Thailand', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 
/*
			$cmd = " INSERT INTO PER_DECORATION (DC_CODE, DC_SHORTNAME, DC_NAME, DC_ORDER, DC_TYPE, DC_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  DC_ENG_NAME, DC_FLAG)
							  VALUES ('01', 'รมภ', 'ราชมิตราภรณ์', 90, 1, 1, $SESS_USERID, '$UPDATE_DATE', 'Knight', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_DECORATION (DC_CODE, DC_SHORTNAME, DC_NAME, DC_ORDER, DC_TYPE, DC_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  DC_ENG_NAME, DC_FLAG)
							  VALUES ('01', 'รมภ', 'ราชมิตราภรณ์', 90, 1, 1, $SESS_USERID, '$UPDATE_DATE', 'Knight', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_DECORATION (DC_CODE, DC_SHORTNAME, DC_NAME, DC_ORDER, DC_TYPE, DC_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  DC_ENG_NAME, DC_FLAG)
							  VALUES ('01', 'รมภ', 'ราชมิตราภรณ์', 90, 1, 1, $SESS_USERID, '$UPDATE_DATE', 'Knight', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_DECORATION (DC_CODE, DC_SHORTNAME, DC_NAME, DC_ORDER, DC_TYPE, DC_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  DC_ENG_NAME, DC_FLAG)
							  VALUES ('01', 'รมภ', 'ราชมิตราภรณ์', 90, 1, 1, $SESS_USERID, '$UPDATE_DATE', 'Knight', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_DECORATION (DC_CODE, DC_SHORTNAME, DC_NAME, DC_ORDER, DC_TYPE, DC_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  DC_ENG_NAME, DC_FLAG)
							  VALUES ('01', 'รมภ', 'ราชมิตราภรณ์', 90, 1, 1, $SESS_USERID, '$UPDATE_DATE', 'Knight', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_DECORATION (DC_CODE, DC_SHORTNAME, DC_NAME, DC_ORDER, DC_TYPE, DC_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  DC_ENG_NAME, DC_FLAG)
							  VALUES ('01', 'รมภ', 'ราชมิตราภรณ์', 90, 1, 1, $SESS_USERID, '$UPDATE_DATE', 'Knight', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_DECORATION (DC_CODE, DC_SHORTNAME, DC_NAME, DC_ORDER, DC_TYPE, DC_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  DC_ENG_NAME, DC_FLAG)
							  VALUES ('01', 'รมภ', 'ราชมิตราภรณ์', 90, 1, 1, $SESS_USERID, '$UPDATE_DATE', 'Knight', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_DECORATION (DC_CODE, DC_SHORTNAME, DC_NAME, DC_ORDER, DC_TYPE, DC_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  DC_ENG_NAME, DC_FLAG)
							  VALUES ('01', 'รมภ', 'ราชมิตราภรณ์', 90, 1, 1, $SESS_USERID, '$UPDATE_DATE', 'Knight', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_DECORATION (DC_CODE, DC_SHORTNAME, DC_NAME, DC_ORDER, DC_TYPE, DC_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  DC_ENG_NAME, DC_FLAG)
							  VALUES ('01', 'รมภ', 'ราชมิตราภรณ์', 90, 1, 1, $SESS_USERID, '$UPDATE_DATE', 'Knight', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_DECORATION (DC_CODE, DC_SHORTNAME, DC_NAME, DC_ORDER, DC_TYPE, DC_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  DC_ENG_NAME, DC_FLAG)
							  VALUES ('01', 'รมภ', 'ราชมิตราภรณ์', 90, 1, 1, $SESS_USERID, '$UPDATE_DATE', 'Knight', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_DECORATION (DC_CODE, DC_SHORTNAME, DC_NAME, DC_ORDER, DC_TYPE, DC_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  DC_ENG_NAME, DC_FLAG)
							  VALUES ('01', 'รมภ', 'ราชมิตราภรณ์', 90, 1, 1, $SESS_USERID, '$UPDATE_DATE', 'Knight', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_DECORATION (DC_CODE, DC_SHORTNAME, DC_NAME, DC_ORDER, DC_TYPE, DC_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  DC_ENG_NAME, DC_FLAG)
							  VALUES ('01', 'รมภ', 'ราชมิตราภรณ์', 90, 1, 1, $SESS_USERID, '$UPDATE_DATE', 'Knight', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_DECORATION (DC_CODE, DC_SHORTNAME, DC_NAME, DC_ORDER, DC_TYPE, DC_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  DC_ENG_NAME, DC_FLAG)
							  VALUES ('01', 'รมภ', 'ราชมิตราภรณ์', 90, 1, 1, $SESS_USERID, '$UPDATE_DATE', 'Knight', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_DECORATION (DC_CODE, DC_SHORTNAME, DC_NAME, DC_ORDER, DC_TYPE, DC_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  DC_ENG_NAME, DC_FLAG)
							  VALUES ('01', 'รมภ', 'ราชมิตราภรณ์', 90, 1, 1, $SESS_USERID, '$UPDATE_DATE', 'Knight', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_DECORATION (DC_CODE, DC_SHORTNAME, DC_NAME, DC_ORDER, DC_TYPE, DC_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  DC_ENG_NAME, DC_FLAG)
							  VALUES ('01', 'รมภ', 'ราชมิตราภรณ์', 90, 1, 1, $SESS_USERID, '$UPDATE_DATE', 'Knight', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_DECORATION (DC_CODE, DC_SHORTNAME, DC_NAME, DC_ORDER, DC_TYPE, DC_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  DC_ENG_NAME, DC_FLAG)
							  VALUES ('01', 'รมภ', 'ราชมิตราภรณ์', 90, 1, 1, $SESS_USERID, '$UPDATE_DATE', 'Knight', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_DECORATION (DC_CODE, DC_SHORTNAME, DC_NAME, DC_ORDER, DC_TYPE, DC_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  DC_ENG_NAME, DC_FLAG)
							  VALUES ('01', 'รมภ', 'ราชมิตราภรณ์', 90, 1, 1, $SESS_USERID, '$UPDATE_DATE', 'Knight', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_DECORATION (DC_CODE, DC_SHORTNAME, DC_NAME, DC_ORDER, DC_TYPE, DC_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  DC_ENG_NAME, DC_FLAG)
							  VALUES ('01', 'รมภ', 'ราชมิตราภรณ์', 90, 1, 1, $SESS_USERID, '$UPDATE_DATE', 'Knight', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_DECORATION (DC_CODE, DC_SHORTNAME, DC_NAME, DC_ORDER, DC_TYPE, DC_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  DC_ENG_NAME, DC_FLAG)
							  VALUES ('01', 'รมภ', 'ราชมิตราภรณ์', 90, 1, 1, $SESS_USERID, '$UPDATE_DATE', 'Knight', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_DECORATION (DC_CODE, DC_SHORTNAME, DC_NAME, DC_ORDER, DC_TYPE, DC_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  DC_ENG_NAME, DC_FLAG)
							  VALUES ('01', 'รมภ', 'ราชมิตราภรณ์', 90, 1, 1, $SESS_USERID, '$UPDATE_DATE', 'Knight', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_DECORATION (DC_CODE, DC_SHORTNAME, DC_NAME, DC_ORDER, DC_TYPE, DC_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  DC_ENG_NAME, DC_FLAG)
							  VALUES ('01', 'รมภ', 'ราชมิตราภรณ์', 90, 1, 1, $SESS_USERID, '$UPDATE_DATE', 'Knight', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_DECORATION (DC_CODE, DC_SHORTNAME, DC_NAME, DC_ORDER, DC_TYPE, DC_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  DC_ENG_NAME, DC_FLAG)
							  VALUES ('01', 'รมภ', 'ราชมิตราภรณ์', 90, 1, 1, $SESS_USERID, '$UPDATE_DATE', 'Knight', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 
*/
			if ($BKK_FLAG==1 || $MFA_FLAG==1) {
				$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_R010034.html' ";
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
									  VALUES (1, 'TH', $MAX_ID, 4, 'R1034 จำนวนข้าราชการพลเรือน จำแนกตามสายงาน เพศ และระดับตำแหน่ง', 'S', 'W', 'rpt_R010034.html', 0, 36, 405, 
									  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();

					$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
									  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
									  CREATE_BY, UPDATE_DATE, UPDATE_BY)
									  VALUES (1, 'EN', $MAX_ID, 4, 'R1034 จำนวนข้าราชการพลเรือน จำแนกตามสายงาน เพศ และระดับตำแหน่ง', 'S', 'W', 'rpt_R010034.html', 0, 36, 405, 
									  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();
				}

				$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_R010035.html' ";
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
									  VALUES (1, 'TH', $MAX_ID, 5, 'R1035 จำนวนข้าราชการพลเรือน จำแนกตามตำแหน่งในการบริหารงาน เพศ และระดับตำแหน่ง', 'S', 'W', 'rpt_R010035.html', 0, 36, 405, 
									  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();

					$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
									  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
									  CREATE_BY, UPDATE_DATE, UPDATE_BY)
									  VALUES (1, 'EN', $MAX_ID, 5, 'R1035 จำนวนข้าราชการพลเรือน จำแนกตามตำแหน่งในการบริหารงาน เพศ และระดับตำแหน่ง', 'S', 'W', 'rpt_R010035.html', 0, 36, 405, 
									  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();
				}

				$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_R007011.html' ";
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
									  VALUES (1, 'TH', $MAX_ID, 11, 'R0711 รายงานสรุปการลา', 'S', 'W', 'rpt_R007011.html', 0, 36, 239, 
									  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();

					$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
									  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
									  CREATE_BY, UPDATE_DATE, UPDATE_BY)
									  VALUES (1, 'EN', $MAX_ID, 11, 'R0711 รายงานสรุปการลา', 'S', 'W', 'rpt_R007011.html', 0, 36, 239, 
									  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();
				}

				if ($MFA_FLAG==1) {
					$cmd = " DROP TABLE PER_HOCHIS ";
					$count_data = $db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();

					$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'personal_master.html?SEARCHHIS=personal_hochis' ";
					$count_data = $db->send_cmd($cmd);
					//$db->show_error();

					$cmd = " SELECT SC_ID FROM PER_SCHOLAR_ORDSPC ";
					$count_data = $db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
					if (!$count_data) {
						if($DPISDB=="odbc") 
							$cmd = " CREATE TABLE PER_SCHOLAR_ORDSPC(
							SC_ID INTEGER NOT NULL,	
							SO_TYPE CHAR(1) NOT NULL,
							SO_STARTDATE VARCHAR(19) NOT NULL,
							SO_ENDDATE VARCHAR(19) NULL,
							SO_MAJOR_DESC VARCHAR(255) NULL,
							SO_PLACE VARCHAR(255) NULL,
							CT_CODE VARCHAR(10) NULL,
							SO_FUND VARCHAR(255) NULL,
							SO_REMARK MEMO NULL,
							UPDATE_USER INTEGER NOT NULL,
							UPDATE_DATE VARCHAR(19) NOT NULL,		
							CONSTRAINT PK_PER_SCHOLAR_ORDSPC PRIMARY KEY (SC_ID, SO_TYPE, SO_STARTDATE)) ";
						elseif($DPISDB=="oci8") 
							$cmd = " CREATE TABLE PER_SCHOLAR_ORDSPC(
							SC_ID NUMBER(10) NOT NULL,	
							SO_TYPE CHAR(1) NOT NULL,
							SO_STARTDATE VARCHAR2(19) NOT NULL,
							SO_ENDDATE VARCHAR2(19) NULL,
							SO_MAJOR_DESC VARCHAR2(255) NULL,
							SO_PLACE VARCHAR2(255) NULL,
							CT_CODE VARCHAR2(10) NULL,
							SO_FUND VARCHAR2(255) NULL,
							SO_REMARK VARCHAR2(1000) NULL,
							UPDATE_USER NUMBER(11) NOT NULL,
							UPDATE_DATE VARCHAR2(19) NOT NULL,		
							CONSTRAINT PK_PER_SCHOLAR_ORDSPC PRIMARY KEY (SC_ID, SO_TYPE, SO_STARTDATE)) ";
						elseif($DPISDB=="mysql")
							$cmd = " CREATE TABLE PER_SCHOLAR_ORDSPC(
							SC_ID INTEGER(10) NOT NULL,	
							SO_TYPE CHAR(1) NOT NULL,
							SO_STARTDATE VARCHAR(19) NOT NULL,
							SO_ENDDATE VARCHAR(19) NULL,
							SO_MAJOR_DESC VARCHAR(255) NULL,
							SO_PLACE VARCHAR(255) NULL,
							CT_CODE VARCHAR(10) NULL,
							SO_FUND VARCHAR(255) NULL,
							SO_REMARK TEXT NULL,
							UPDATE_USER INTEGER(11) NOT NULL,
							UPDATE_DATE VARCHAR(19) NOT NULL,		
							CONSTRAINT PK_PER_SCHOLAR_ORDSPC PRIMARY KEY (SC_ID, SO_TYPE, SO_STARTDATE)) ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
					}

					$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'data_scholar_ordspc.html' ";
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
										  VALUES (1, 'TH', $MAX_ID, 4, 'P0804 คำขอพิเศษ', 'S', 'W', 'data_scholar_ordspc.html', 0, 35, 248, $CREATE_DATE, 
										  $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
						$db->send_cmd($cmd);
						//$db->show_error();

						$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
										  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
										  CREATE_BY, UPDATE_DATE, UPDATE_BY)
										  VALUES (1, 'EN', $MAX_ID, 4, 'P0804 คำขอพิเศษ', 'S', 'W', 'data_scholar_ordspc.html', 0, 35, 248, $CREATE_DATE, 
										  $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
						$db->send_cmd($cmd);
						//$db->show_error();
					}
				}

				$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO, COM_ACTIVE)
								VALUES ('5043', 'คส. 4.3', 'คำสั่งแต่งตั้งให้ข้าราชการไปดำรงตำแหน่ง', '504', 37, 1) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMGROUP (COM_GROUP, CG_NAME, CG_ACTIVE, UPDATE_USER, UPDATE_DATE, CG_SEQ_NO)
								  VALUES ('527', 'คำสั่งให้ข้าราชการไปปฏิบัติหน้าที่ราชการ', 1, $SESS_USERID, '$UPDATE_DATE', 27) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			
				$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO, COM_ACTIVE) 
								  VALUES ('5270', 'คส. 27', 'คำสั่งให้ข้าราชการไปปฏิบัติหน้าที่ราชการ', '527', 106, 1) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			}

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_R001.html?report=rpt_R001005' ";
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
								  VALUES (1, 'TH', $MAX_ID, 5, 'R0105 จำนวนตำแหน่งจำแนกตามระดับตำแหน่ง', 'S', 'W', 'rpt_R001.html?report=rpt_R001005', 0, 36, 233, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 5, 'R0105 จำนวนตำแหน่งจำแนกตามระดับตำแหน่ง', 'S', 'W', 'rpt_R001.html?report=rpt_R001005', 0, 36, 233, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			if ($RTF_FLAG==1) {
				$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_R001.html?report=rpt_R001006' ";
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
									  VALUES (1, 'TH', $MAX_ID, 6, 'R0106 จำนวนตำแหน่งจำแนกตามวุฒิการศึกษา', 'S', 'W', 'rpt_R001.html?report=rpt_R001006', 0, 36, 233, 
									  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();

					$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
									  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
									  CREATE_BY, UPDATE_DATE, UPDATE_BY)
									  VALUES (1, 'EN', $MAX_ID, 6, 'R0106 จำนวนตำแหน่งจำแนกตามวุฒิการศึกษา', 'S', 'W', 'rpt_R001.html?report=rpt_R001006', 0, 36, 233, 
									  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();
				}

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV0 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_TID, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'TH', 50, 14, 'ถ่ายโอนข้อมูล', 'S', 'N', 0, $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, 
								  $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV0 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, 	TYPE_LINKTO, LINKTO_TID, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', 50, 14, 'ถ่ายโอนข้อมูล', 'S', 'N', 0, $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, 
								  $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE MENU_LABEL = 'I01 โครงสร้างและตำแหน่ง' ";
				$count_data = $db->send_cmd($cmd);
				//$db->show_error();
				if (!$count_data) {
					$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV1 ";
					$db->send_cmd($cmd);
					//$db->show_error();
					$data = $db->get_array();
					$MAX_ID = $data[MAX_ID] + 1;
					$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
									  FLAG_SHOW, TYPE_LINKTO, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, UPDATE_DATE, 
									  UPDATE_BY)
									  VALUES (1, 'TH', $MAX_ID, 1, 'I01 โครงสร้างและตำแหน่ง', 'S', 'N', 0, 50, $CREATE_DATE, $UPDATE_BY, 
									  $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();

					$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
									  FLAG_SHOW, TYPE_LINKTO, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, UPDATE_DATE, 
									  UPDATE_BY)
									  VALUES (1, 'EN', $MAX_ID, 1, 'I01 โครงสร้างและตำแหน่ง', 'S', 'N', 0, 50, $CREATE_DATE, $UPDATE_BY, 
									  $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);	
					//$db->show_error();
				}

				$cmd = " SELECT MENU_ID FROM BACKOFFICE_MENU_BAR_LV1 WHERE MENU_LABEL = 'I01 โครงสร้างและตำแหน่ง' ";
				$db->send_cmd($cmd);
				//$db->show_error();
				$data = $db->get_array();
				$MENU_ID = $data[MENU_ID];

				$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'import_file.html?form=per_org_form.php' ";
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
									  VALUES (1, 'TH', $MAX_ID, 1, 'I0101 โครงสร้างส่วนราชการ', 'S', 'W', 'import_file.html?form=per_org_form.php', 0, 50, $MENU_ID, 
									  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();

					$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
									  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
									  CREATE_BY, UPDATE_DATE, UPDATE_BY)
									  VALUES (1, 'EN', $MAX_ID, 1, 'I0101 โครงสร้างส่วนราชการ', 'S', 'W', 'import_file.html?form=per_org_form.php', 0, 50, $MENU_ID, 
									  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();
				}

				$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'import_file.html?form=per_position_form.php' ";
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
									  VALUES (1, 'TH', $MAX_ID, 2, 'I0102 ตำแหน่งข้าราชการ', 'S', 'W', 'import_file.html?form=per_position_form.php', 0, 50, $MENU_ID, 
									  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();

					$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
									  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
									  CREATE_BY, UPDATE_DATE, UPDATE_BY)
									  VALUES (1, 'EN', $MAX_ID, 2, 'I0102 ตำแหน่งข้าราชการ', 'S', 'W', 'import_file.html?form=per_position_form.php', 0, 50, $MENU_ID, 
									  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();
				}

				$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'import_file.html?form=per_pos_emp_form.php' ";
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
									  VALUES (1, 'TH', $MAX_ID, 3, 'I0103 ตำแหน่งลูกจ้างประจำ', 'S', 'W', 'import_file.html?form=per_pos_emp_form.php', 0, 50, $MENU_ID, 
									  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();

					$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
									  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
									  CREATE_BY, UPDATE_DATE, UPDATE_BY)
									  VALUES (1, 'EN', $MAX_ID, 3, 'I0103 ตำแหน่งลูกจ้างประจำ', 'S', 'W', 'import_file.html?form=per_pos_emp_form.php', 0, 50, $MENU_ID, 
									  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();
				}

				$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'import_file.html?form=per_pos_empser_form.php' ";
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
									  VALUES (1, 'TH', $MAX_ID, 4, 'I0104 ตำแหน่งพนักงานราชการ', 'S', 'W', 'import_file.html?form=per_pos_empser_form.php', 0, 50, $MENU_ID, 
									  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();

					$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
									  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
									  CREATE_BY, UPDATE_DATE, UPDATE_BY)
									  VALUES (1, 'EN', $MAX_ID, 4, 'I0104 ตำแหน่งพนักงานราชการ', 'S', 'W', 'import_file.html?form=per_pos_empser_form.php', 0, 50, $MENU_ID, 
									  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();
				}

				$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'import_file.html?form=per_pos_temp_form.php' ";
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
									  VALUES (1, 'TH', $MAX_ID, 5, 'I0105 ตำแหน่งลูกจ้างชั่วคราว', 'S', 'W', 'import_file.html?form=per_pos_temp_form.php', 0, 50, $MENU_ID, 
									  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();

					$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
									  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
									  CREATE_BY, UPDATE_DATE, UPDATE_BY)
									  VALUES (1, 'EN', $MAX_ID, 5, 'I0105 ตำแหน่งลูกจ้างชั่วคราว', 'S', 'W', 'import_file.html?form=per_pos_temp_form.php', 0, 50, $MENU_ID, 
									  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();
				}

			}

			$cmd = " CREATE INDEX IDX_PER_ABSENTSUM ON PER_ABSENTSUM (PER_ID, AS_YEAR, AS_CYCLE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_POSITIONHIS", "POH_ASS_DEPARTMENT","VARCHAR", "100", "NULL");

			$cmd = " CREATE TABLE GPIS1(
			tempMinistry VARCHAR2(100) NULL,	
			Organzie VARCHAR2(100) NULL,
			DivisionName VARCHAR2(100) NULL,
			tempPositionNo VARCHAR2(20) NULL,
			tempLine VARCHAR2(100) NULL,
			tempPositionType VARCHAR2(20) NULL,
			tempLevel VARCHAR2(10) NULL,
			tempManagePosition VARCHAR2(100) NULL,
			tempSkill VARCHAR2(100) NULL,		
			tempOrganizeType VARCHAR2(50) NULL,		
			tempProvince VARCHAR2(50) NULL,		
			tempPositionStatus VARCHAR2(20) NULL,		
			tempPrename VARCHAR2(30) NULL,		
			tempFirstName VARCHAR2(50) NULL,		
			tempLastName VARCHAR2(50) NULL,		
			tempCardNo VARCHAR2(13) NULL,		
			tempGender VARCHAR2(10) NULL,		
			tempBirthDate VARCHAR2(10) NULL,		
			tempStartDate VARCHAR2(10) NULL,		
			tempSalary NUMBER(10) NULL,		
			tempPositionSalary NUMBER(10) NULL,		
			tempEducationLevel VARCHAR2(50) NULL,		
			tempEducationName VARCHAR2(100) NULL,		
			tempEducationMajor VARCHAR2(100) NULL,		
			tempGraduated VARCHAR2(100) NULL,		
			tempEducationCountry VARCHAR2(50) NULL,		
			tempScholarType VARCHAR2(100) NULL,		
			tempMovementType VARCHAR2(100) NULL,		
			tempMovementDate VARCHAR2(10) NULL,		
			tempClName VARCHAR2(50) NULL,		
			tempFlowDate VARCHAR2(10) NULL,		
			CONSTRAINT PK_GPIS1 PRIMARY KEY (tempPositionNo)) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE TABLE GPIS1_FLOW_IN(
			tempMinistry VARCHAR2(100) NULL,	
			Organzie VARCHAR2(100) NULL,
			DivisionName VARCHAR2(100) NULL,
			tempPositionNo VARCHAR2(20) NULL,
			tempLine VARCHAR2(100) NULL,
			tempPositionType VARCHAR2(20) NULL,
			tempLevel VARCHAR2(10) NULL,
			tempManagePosition VARCHAR2(100) NULL,
			tempSkill VARCHAR2(100) NULL,		
			tempOrganizeType VARCHAR2(50) NULL,		
			tempProvince VARCHAR2(50) NULL,		
			tempPositionStatus VARCHAR2(20) NULL,		
			tempPrename VARCHAR2(30) NULL,		
			tempFirstName VARCHAR2(50) NULL,		
			tempLastName VARCHAR2(50) NULL,		
			tempCardNo VARCHAR2(13) NULL,		
			tempGender VARCHAR2(10) NULL,		
			tempBirthDate VARCHAR2(10) NULL,		
			tempStartDate VARCHAR2(10) NULL,		
			tempSalary NUMBER(10) NULL,		
			tempPositionSalary NUMBER(10) NULL,		
			tempEducationLevel VARCHAR2(50) NULL,		
			tempEducationName VARCHAR2(100) NULL,		
			tempEducationMajor VARCHAR2(100) NULL,		
			tempGraduated VARCHAR2(100) NULL,		
			tempEducationCountry VARCHAR2(50) NULL,		
			tempScholarType VARCHAR2(100) NULL,		
			tempMovementType VARCHAR2(100) NULL,		
			tempMovementDate VARCHAR2(10) NULL,		
			tempClName VARCHAR2(50) NULL,		
			tempFlowDate VARCHAR2(10) NULL,		
			CONSTRAINT PK_GPIS1_FLOW_IN PRIMARY KEY (tempPositionNo)) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE TABLE GPIS1_FLOW_OUT(
			tempMinistry VARCHAR2(100) NULL,	
			Organzie VARCHAR2(100) NULL,
			DivisionName VARCHAR2(100) NULL,
			tempPositionNo VARCHAR2(20) NULL,
			tempLine VARCHAR2(100) NULL,
			tempPositionType VARCHAR2(20) NULL,
			tempLevel VARCHAR2(10) NULL,
			tempManagePosition VARCHAR2(100) NULL,
			tempSkill VARCHAR2(100) NULL,		
			tempOrganizeType VARCHAR2(50) NULL,		
			tempProvince VARCHAR2(50) NULL,		
			tempPositionStatus VARCHAR2(20) NULL,		
			tempPrename VARCHAR2(30) NULL,		
			tempFirstName VARCHAR2(50) NULL,		
			tempLastName VARCHAR2(50) NULL,		
			tempCardNo VARCHAR2(13) NULL,		
			tempGender VARCHAR2(10) NULL,		
			tempBirthDate VARCHAR2(10) NULL,		
			tempStartDate VARCHAR2(10) NULL,		
			tempSalary NUMBER(10) NULL,		
			tempPositionSalary NUMBER(10) NULL,		
			tempEducationLevel VARCHAR2(50) NULL,		
			tempEducationName VARCHAR2(100) NULL,		
			tempEducationMajor VARCHAR2(100) NULL,		
			tempGraduated VARCHAR2(100) NULL,		
			tempEducationCountry VARCHAR2(50) NULL,		
			tempScholarType VARCHAR2(100) NULL,		
			tempMovementType VARCHAR2(100) NULL,		
			tempMovementDate VARCHAR2(10) NULL,		
			tempClName VARCHAR2(50) NULL,		
			tempFlowDate VARCHAR2(10) NULL,		
			CONSTRAINT PK_GPIS1_FLOW_OUT PRIMARY KEY (tempPositionNo)) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
/*
			$cmd = " CREATE TABLE GPIS2(
			tempMinistry VARCHAR2(100) NULL,	
			Organzie VARCHAR2(100) NULL,
			DivisionName VARCHAR2(100) NULL,
			tempProvince VARCHAR2(50) NULL,		
			tempOrganizeType VARCHAR2(50) NULL,		
			tempPrename VARCHAR2(30) NULL,		
			tempFirstName VARCHAR2(50) NULL,		
			tempLastName VARCHAR2(50) NULL,		
			tempCardNo VARCHAR2(13) NULL,		
			tempGender VARCHAR2(10) NULL,		
			tempBirthDate VARCHAR2(10) NULL,		
			tempStartDate VARCHAR2(10) NULL,		
			tempPromoteDate VARCHAR2(10) NULL,		
			tempResignDate VARCHAR2(10) NULL,		
			tempPositionNo VARCHAR2(20) NULL,
			tempLine VARCHAR2(100) NULL,
			tempLevel VARCHAR2(10) NULL,
			tempPositionType VARCHAR2(20) NULL,
			tempRewardType NUMBER(10) NULL,		
			tempEducationName VARCHAR2(100) NULL,		
			tempEducationLevel VARCHAR2(50) NULL,		
			tempTraining VARCHAR2(100) NULL,
			tempDecoration VARCHAR2(100) NULL,		
			tempGuilty VARCHAR2(100) NULL,		
			tempPunish VARCHAR2(100) NULL,		
			tempScoreCompetence1 NUMBER(6,2) NULL,		
			tempScoreCompetence2 NUMBER(6,2) NULL,		
			tempResult1 VARCHAR2(100) NULL,		
			tempResult2 VARCHAR2(100) NULL,		
			percentSalary1 NUMBER(10,2) NULL,		
			percentSalary2 NUMBER(10,2) NULL,		
			groupSalary VARCHAR2(10) NULL,		
			CONSTRAINT PK_GPIS2 PRIMARY KEY ()) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
*/
			add_field("PER_SCHOLAR", "SC_MAJOR_DESC","VARCHAR", "255", "NULL");
			add_field("PER_SCHOLAR", "SC_RECEIVEDATE","VARCHAR", "19", "NULL");
			add_field("PER_SCHOLAR", "SC_EXPECTDATE","VARCHAR", "19", "NULL");
			add_field("PER_SCHOLAR", "SC_EL_TYPE","CHAR", "1", "NULL");
			add_field("PER_SCHOLAR", "SC_LANGUAGE","VARCHAR", "50", "NULL");
			add_field("PER_SCHOLAR", "EL_CODE1","VARCHAR", "10", "NULL");
			add_field("PER_SCHOLAR", "EN_CODE1","VARCHAR", "10", "NULL");
			add_field("PER_SCHOLAR", "EM_CODE1","VARCHAR", "10", "NULL");
			add_field("PER_SCHOLAR", "INS_CODE1","VARCHAR", "10", "NULL");
			add_field("PER_SCHOLAR", "SC_INSTITUTE1","VARCHAR", "255", "NULL");
			add_field("PER_SCHOLAR", "CT_CODE1","VARCHAR", "10", "NULL");
			add_field("PER_SCHOLAR", "SC_LANGUAGE1","VARCHAR", "50", "NULL");
			add_field("PER_SCHOLAR", "SC_STARTDATE1","VARCHAR", "19", "NULL");
			add_field("PER_SCHOLAR", "SC_ENDDATE1","VARCHAR", "19", "NULL");
			add_field("PER_SCHOLAR", "EL_CODE2","VARCHAR", "10", "NULL");
			add_field("PER_SCHOLAR", "EN_CODE2","VARCHAR", "10", "NULL");
			add_field("PER_SCHOLAR", "EM_CODE2","VARCHAR", "10", "NULL");
			add_field("PER_SCHOLAR", "INS_CODE2","VARCHAR", "10", "NULL");
			add_field("PER_SCHOLAR", "SC_INSTITUTE2","VARCHAR", "255", "NULL");
			add_field("PER_SCHOLAR", "CT_CODE2","VARCHAR", "10", "NULL");
			add_field("PER_SCHOLAR", "SC_LANGUAGE2","VARCHAR", "50", "NULL");
			add_field("PER_SCHOLAR", "SC_STARTDATE2","VARCHAR", "19", "NULL");
			add_field("PER_SCHOLAR", "SC_ENDDATE2","VARCHAR", "19", "NULL");

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_SCHOLAR ALTER EL_CODE NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SCHOLAR MODIFY EL_CODE NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_SCHOLAR MODIFY EL_CODE NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_SCHOLAR ALTER SC_STARTDATE NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SCHOLAR MODIFY SC_STARTDATE NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_SCHOLAR MODIFY SC_STARTDATE NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_SCHOLAR ALTER SC_ENDDATE NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SCHOLAR MODIFY SC_ENDDATE NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_SCHOLAR MODIFY SC_ENDDATE NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_KPI_COMPETENCE ON PER_KPI_COMPETENCE (KF_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_USER_DETAIL_GROUP ON USER_DETAIL (GROUP_ID) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " CREATE INDEX IDX_PER_SALQUOTA ON PER_SALQUOTA (DEPARTMENT_ID, SALQ_YEAR, SALQ_TYPE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_PERSONAL_14 ON PER_PERSONAL (PER_TYPE, PER_STATUS, DEPARTMENT_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_LAYER ON PER_LAYER (LEVEL_NO, LAYER_NO) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_CRIME_DTL ALTER CRD_NAME MEMO ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_CRIME_DTL MODIFY CRD_NAME VARCHAR2(4000) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_CRIME_DTL ALTER CRD_NAME TEXT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_TRAINING", "TRN_BACKDATE","VARCHAR", "19", "NULL");
			add_field("PER_TRAINING", "TRN_FLAG","CHAR", "1", "NULL");
			add_field("PER_TRAINING", "TRN_SHORTNAME","VARCHAR", "50", "NULL");

			$cmd = " INSERT INTO PER_NAMECARD (NC_ID, NC_NAME, NC_UNIT, NC_W, NC_H, NC_FORM, UPDATE_USER, UPDATE_DATE)
							VALUES (6, 'นามบัตรแบบของสำนักงาน ก.พ.', 'mm',89,55,'variable,pername1,35,15,30,cordia,,16,L,00,00,00,|variable,perposline,35,21,30,cordia,,12,L,F0,A2,00,|variable,org,15,39,78,cordia,,12,L,00,00,00,|image,images/OCSCbanner80h.jpg,2,2,30,10|variable,allphone,15,51,78,cordia,,10,L,00,00,00,|variable,email,43,26,40,cordia,,10,L,00,00,00,|variable,address2,15,44,60,cordia,,10,L,00,00,00,lines|text,www.ocsc.go.th,www.ocsc.go.th,65,3,30,cordia,,14,L,F0,A2,00|background,images/namecard_bg001.jpg,0,0,89,55|text,e-mail:,e-mail:,35,26,30,cordia,,10,L,00,00,00|line,line1,0,26,89,26,F0,A4,00,0|rect,box1,13,41,14,42,F0,A4,00,2,', $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($MSOCIETY_FLAG==1) {
				$cmd = " update PER_LINE set PL_SHORTNAME = replace(PL_SHORTNAME,'เจ้าพนักงาน','จพ.') where PL_SHORTNAME like 'เจ้าพนักงาน%' ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			}

			$cmd = " UPDATE PER_POSITIONHIS SET LEVEL_NO = POH_LEVEL_NO 
							WHERE POH_EFFECTIVEDATE > '1900-01-01' AND  POH_EFFECTIVEDATE < '$PT_DATE' AND LEVEL_NO IN $LIST_LEVEL_NO AND 
											 POH_LEVEL_NO IN ('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_SIGN ON PER_SIGN (DEPARTMENT_ID, SIGN_TYPE, SIGN_PER_TYPE, SIGN_STARTDATE, SIGN_ENDDATE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_KPI_FORM", "PER_SALARY","NUMBER", "16,2", "NULL");
			add_field("PER_KPI_FORM", "PL_NAME","VARCHAR", "255", "NULL");
			add_field("PER_KPI_FORM", "PM_NAME","VARCHAR", "255", "NULL");
			add_field("PER_KPI_FORM", "REVIEW_PN_NAME","VARCHAR", "50", "NULL");
			add_field("PER_KPI_FORM", "REVIEW_PER_NAME","VARCHAR", "255", "NULL");
			add_field("PER_KPI_FORM", "REVIEW_PL_NAME","VARCHAR", "255", "NULL");
			add_field("PER_KPI_FORM", "REVIEW_PM_NAME","VARCHAR", "255", "NULL");
			add_field("PER_KPI_FORM", "REVIEW_LEVEL_NO","VARCHAR", "10", "NULL");
			add_field("PER_KPI_FORM", "REVIEW_PN_NAME0","VARCHAR", "50", "NULL");
			add_field("PER_KPI_FORM", "REVIEW_PER_NAME0","VARCHAR", "255", "NULL");
			add_field("PER_KPI_FORM", "REVIEW_PL_NAME0","VARCHAR", "255", "NULL");
			add_field("PER_KPI_FORM", "REVIEW_PM_NAME0","VARCHAR", "255", "NULL");
			add_field("PER_KPI_FORM", "REVIEW_LEVEL_NO0","VARCHAR", "10", "NULL");
			add_field("PER_KPI_FORM", "REVIEW_PN_NAME1","VARCHAR", "50", "NULL");
			add_field("PER_KPI_FORM", "REVIEW_PER_NAME1","VARCHAR", "255", "NULL");
			add_field("PER_KPI_FORM", "REVIEW_PL_NAME1","VARCHAR", "255", "NULL");
			add_field("PER_KPI_FORM", "REVIEW_PM_NAME1","VARCHAR", "255", "NULL");
			add_field("PER_KPI_FORM", "REVIEW_LEVEL_NO1","VARCHAR", "10", "NULL");
			add_field("PER_KPI_FORM", "REVIEW_PN_NAME2","VARCHAR", "50", "NULL");
			add_field("PER_KPI_FORM", "REVIEW_PER_NAME2","VARCHAR", "255", "NULL");
			add_field("PER_KPI_FORM", "REVIEW_PL_NAME2","VARCHAR", "255", "NULL");
			add_field("PER_KPI_FORM", "REVIEW_PM_NAME2","VARCHAR", "255", "NULL");
			add_field("PER_KPI_FORM", "REVIEW_LEVEL_NO2","VARCHAR", "10", "NULL");

			$cmd = " UPDATE PER_KPI_FORM SET PL_NAME = REPLACE(PL_NAME, 'สูง', '') WHERE PL_NAME LIKE '%สูง' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_KPI_FORM SET PL_NAME = REPLACE(PL_NAME, 'ต้น', '') WHERE PL_NAME LIKE '%ต้น' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_KPI_FORM SET PL_NAME = REPLACE(PL_NAME, 'ทรงคุณวุฒิ', '') WHERE PL_NAME LIKE '%ทรงคุณวุฒิ' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_KPI_FORM SET PL_NAME = REPLACE(PL_NAME, 'เชี่ยวชาญ', '') WHERE PL_NAME LIKE '%เชี่ยวชาญ' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_KPI_FORM SET PL_NAME = REPLACE(PL_NAME, 'ชำนาญการพิเศษ', '') WHERE PL_NAME LIKE '%ชำนาญการพิเศษ' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_KPI_FORM SET PL_NAME = REPLACE(PL_NAME, 'ชำนาญการ', '') WHERE PL_NAME LIKE '%ชำนาญการ' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_KPI_FORM SET PL_NAME = REPLACE(PL_NAME, 'ปฏิบัติการ', '') WHERE PL_NAME LIKE '%ปฏิบัติการ' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_KPI_FORM SET PL_NAME = REPLACE(PL_NAME, 'ทักษะพิเศษ', '') WHERE PL_NAME LIKE '%ทักษะพิเศษ' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_KPI_FORM SET PL_NAME = REPLACE(PL_NAME, 'อาวุโส', '') WHERE PL_NAME LIKE '%อาวุโส' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_KPI_FORM SET PL_NAME = REPLACE(PL_NAME, 'ชำนาญงาน', '') WHERE PL_NAME LIKE '%ชำนาญงาน' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_KPI_FORM SET PL_NAME = REPLACE(PL_NAME, 'ปฏิบัติงาน', '') WHERE PL_NAME LIKE '%ปฏิบัติงาน' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_KPI_FORM SET REVIEW_PL_NAME = REPLACE(REVIEW_PL_NAME, 'สูง', '') WHERE REVIEW_PL_NAME LIKE '%สูง' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_KPI_FORM SET REVIEW_PL_NAME = REPLACE(REVIEW_PL_NAME, 'ต้น', '') WHERE REVIEW_PL_NAME LIKE '%ต้น' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_KPI_FORM SET REVIEW_PL_NAME = REPLACE(REVIEW_PL_NAME, 'ทรงคุณวุฒิ', '') WHERE REVIEW_PL_NAME LIKE '%ทรงคุณวุฒิ' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_KPI_FORM SET REVIEW_PL_NAME = REPLACE(REVIEW_PL_NAME, 'เชี่ยวชาญ', '') WHERE REVIEW_PL_NAME LIKE '%เชี่ยวชาญ' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_KPI_FORM SET REVIEW_PL_NAME = REPLACE(REVIEW_PL_NAME, 'ชำนาญการพิเศษ', '') WHERE REVIEW_PL_NAME LIKE '%ชำนาญการพิเศษ' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_KPI_FORM SET REVIEW_PL_NAME = REPLACE(REVIEW_PL_NAME, 'ชำนาญการ', '') WHERE REVIEW_PL_NAME LIKE '%ชำนาญการ' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_KPI_FORM SET REVIEW_PL_NAME = REPLACE(REVIEW_PL_NAME, 'ปฏิบัติการ', '') WHERE REVIEW_PL_NAME LIKE '%ปฏิบัติการ' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_KPI_FORM SET REVIEW_PL_NAME = REPLACE(REVIEW_PL_NAME, 'ทักษะพิเศษ', '') WHERE PL_NAME REVIEW_PL_NAME '%ทักษะพิเศษ' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_KPI_FORM SET REVIEW_PL_NAME = REPLACE(REVIEW_PL_NAME, 'อาวุโส', '') WHERE REVIEW_PL_NAME LIKE '%อาวุโส' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_KPI_FORM SET REVIEW_PL_NAME = REPLACE(REVIEW_PL_NAME, 'ชำนาญงาน', '') WHERE REVIEW_PL_NAME LIKE '%ชำนาญงาน' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_KPI_FORM SET REVIEW_PL_NAME = REPLACE(REVIEW_PL_NAME, 'ปฏิบัติงาน', '') WHERE REVIEW_PL_NAME LIKE '%ปฏิบัติงาน' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_KPI_FORM SET REVIEW_PL_NAME0 = REPLACE(REVIEW_PL_NAME0, 'สูง', '') WHERE REVIEW_PL_NAME0 LIKE '%สูง' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_KPI_FORM SET REVIEW_PL_NAME0 = REPLACE(REVIEW_PL_NAME0, 'ต้น', '') WHERE REVIEW_PL_NAME0 LIKE '%ต้น' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_KPI_FORM SET REVIEW_PL_NAME0 = REPLACE(REVIEW_PL_NAME0, 'ทรงคุณวุฒิ', '') WHERE REVIEW_PL_NAME0 LIKE '%ทรงคุณวุฒิ' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_KPI_FORM SET REVIEW_PL_NAME0 = REPLACE(REVIEW_PL_NAME0, 'เชี่ยวชาญ', '') WHERE REVIEW_PL_NAME0 LIKE '%เชี่ยวชาญ' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_KPI_FORM SET REVIEW_PL_NAME0 = REPLACE(REVIEW_PL_NAME0, 'ชำนาญการพิเศษ', '') WHERE REVIEW_PL_NAME0 LIKE '%ชำนาญการพิเศษ' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_KPI_FORM SET REVIEW_PL_NAME0 = REPLACE(REVIEW_PL_NAME0, 'ชำนาญการ', '') WHERE REVIEW_PL_NAME0 LIKE '%ชำนาญการ' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_KPI_FORM SET REVIEW_PL_NAME0 = REPLACE(REVIEW_PL_NAME0, 'ปฏิบัติการ', '') WHERE REVIEW_PL_NAME0 LIKE '%ปฏิบัติการ' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_KPI_FORM SET REVIEW_PL_NAME0 = REPLACE(REVIEW_PL_NAME0, 'ทักษะพิเศษ', '') WHERE PL_NAME REVIEW_PL_NAME0 '%ทักษะพิเศษ' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_KPI_FORM SET REVIEW_PL_NAME0 = REPLACE(REVIEW_PL_NAME0, 'อาวุโส', '') WHERE REVIEW_PL_NAME0 LIKE '%อาวุโส' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_KPI_FORM SET REVIEW_PL_NAME0 = REPLACE(REVIEW_PL_NAME0, 'ชำนาญงาน', '') WHERE REVIEW_PL_NAME0 LIKE '%ชำนาญงาน' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_KPI_FORM SET REVIEW_PL_NAME0 = REPLACE(REVIEW_PL_NAME0, 'ปฏิบัติงาน', '') WHERE REVIEW_PL_NAME0 LIKE '%ปฏิบัติงาน' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_KPI_FORM SET REVIEW_PL_NAME1 = REPLACE(REVIEW_PL_NAME1, 'สูง', '') WHERE REVIEW_PL_NAME1 LIKE '%สูง' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_KPI_FORM SET REVIEW_PL_NAME1 = REPLACE(REVIEW_PL_NAME1, 'ต้น', '') WHERE REVIEW_PL_NAME1 LIKE '%ต้น' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_KPI_FORM SET REVIEW_PL_NAME1 = REPLACE(REVIEW_PL_NAME1, 'ทรงคุณวุฒิ', '') WHERE REVIEW_PL_NAME1 LIKE '%ทรงคุณวุฒิ' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_KPI_FORM SET REVIEW_PL_NAME1 = REPLACE(REVIEW_PL_NAME1, 'เชี่ยวชาญ', '') WHERE REVIEW_PL_NAME1 LIKE '%เชี่ยวชาญ' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_KPI_FORM SET REVIEW_PL_NAME1 = REPLACE(REVIEW_PL_NAME1, 'ชำนาญการพิเศษ', '') WHERE REVIEW_PL_NAME1 LIKE '%ชำนาญการพิเศษ' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_KPI_FORM SET REVIEW_PL_NAME1 = REPLACE(REVIEW_PL_NAME1, 'ชำนาญการ', '') WHERE REVIEW_PL_NAME1 LIKE '%ชำนาญการ' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_KPI_FORM SET REVIEW_PL_NAME1 = REPLACE(REVIEW_PL_NAME1, 'ปฏิบัติการ', '') WHERE REVIEW_PL_NAME1 LIKE '%ปฏิบัติการ' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_KPI_FORM SET REVIEW_PL_NAME1 = REPLACE(REVIEW_PL_NAME1, 'ทักษะพิเศษ', '') WHERE PL_NAME REVIEW_PL_NAME1 '%ทักษะพิเศษ' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_KPI_FORM SET REVIEW_PL_NAME1 = REPLACE(REVIEW_PL_NAME1, 'อาวุโส', '') WHERE REVIEW_PL_NAME1 LIKE '%อาวุโส' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_KPI_FORM SET REVIEW_PL_NAME1 = REPLACE(REVIEW_PL_NAME1, 'ชำนาญงาน', '') WHERE REVIEW_PL_NAME1 LIKE '%ชำนาญงาน' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_KPI_FORM SET REVIEW_PL_NAME1 = REPLACE(REVIEW_PL_NAME1, 'ปฏิบัติงาน', '') WHERE REVIEW_PL_NAME1 LIKE '%ปฏิบัติงาน' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_KPI_FORM SET REVIEW_PL_NAME2 = REPLACE(REVIEW_PL_NAME2, 'สูง', '') WHERE REVIEW_PL_NAME2 LIKE '%สูง' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_KPI_FORM SET REVIEW_PL_NAME2 = REPLACE(REVIEW_PL_NAME2, 'ต้น', '') WHERE REVIEW_PL_NAME2 LIKE '%ต้น' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_KPI_FORM SET REVIEW_PL_NAME2 = REPLACE(REVIEW_PL_NAME2, 'ทรงคุณวุฒิ', '') WHERE REVIEW_PL_NAME2 LIKE '%ทรงคุณวุฒิ' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_KPI_FORM SET REVIEW_PL_NAME2 = REPLACE(REVIEW_PL_NAME2, 'เชี่ยวชาญ', '') WHERE REVIEW_PL_NAME2 LIKE '%เชี่ยวชาญ' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_KPI_FORM SET REVIEW_PL_NAME2 = REPLACE(REVIEW_PL_NAME2, 'ชำนาญการพิเศษ', '') WHERE REVIEW_PL_NAME2 LIKE '%ชำนาญการพิเศษ' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_KPI_FORM SET REVIEW_PL_NAME2 = REPLACE(REVIEW_PL_NAME2, 'ชำนาญการ', '') WHERE REVIEW_PL_NAME2 LIKE '%ชำนาญการ' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_KPI_FORM SET REVIEW_PL_NAME2 = REPLACE(REVIEW_PL_NAME2, 'ปฏิบัติการ', '') WHERE REVIEW_PL_NAME2 LIKE '%ปฏิบัติการ' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_KPI_FORM SET REVIEW_PL_NAME2 = REPLACE(REVIEW_PL_NAME2, 'ทักษะพิเศษ', '') WHERE PL_NAME REVIEW_PL_NAME2 '%ทักษะพิเศษ' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_KPI_FORM SET REVIEW_PL_NAME2 = REPLACE(REVIEW_PL_NAME2, 'อาวุโส', '') WHERE REVIEW_PL_NAME2 LIKE '%อาวุโส' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_KPI_FORM SET REVIEW_PL_NAME2 = REPLACE(REVIEW_PL_NAME2, 'ชำนาญงาน', '') WHERE REVIEW_PL_NAME2 LIKE '%ชำนาญงาน' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_KPI_FORM SET REVIEW_PL_NAME2 = REPLACE(REVIEW_PL_NAME2, 'ปฏิบัติงาน', '') WHERE REVIEW_PL_NAME2 LIKE '%ปฏิบัติงาน' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

	/*
			$cmd = " SELECT KF_ID, PER_ID, KF_START_DATE, PER_ID_REVIEW, PER_ID_REVIEW0, PER_ID_REVIEW1, PER_ID_REVIEW2 
							FROM PER_KPI_FORM 
							WHERE PER_SALARY IS NULL
							ORDER BY KF_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			while($data = $db_dpis->get_array()){
				$PL_NAME_WORK = $ORG_NAME_WORK = $LEVEL_NO_POS = $POSITION_LEVEL = $LEVEL_NAME = $DEPARTMENT_NAME = $ORG_NAME = $ORG_NAME_1 = $ORG_NAME_2 = $PL_NAME = $PM_NAME = $tmp_ORG_ID =  "";
				$KF_ID = $data[KF_ID];
				$PER_ID = $data[PER_ID];
				$KF_START_DATE = trim($data[KF_START_DATE]);
				$PER_ID_REVIEW = $data[PER_ID_REVIEW];
				$PER_ID_REVIEW0 = $data[PER_ID_REVIEW0];
				$PER_ID_REVIEW1 = $data[PER_ID_REVIEW1];
				$PER_ID_REVIEW2 = $data[PER_ID_REVIEW2];
		
				if (!$PER_ID_REVIEW) {
					$cmd = "	select	PN_NAME, PER_NAME, PER_SURNAME, PER_TYPE, POS_ID, POEM_ID, POEMS_ID, LEVEL_NO , PER_CARDNO
							 from		PER_PERSONAL a, PER_PRENAME b
							 where	PER_ID=$PER_ID_REVIEW and a.PN_CODE=b.PN_CODE ";
					$db_dpis1->send_cmd($cmd);		
					$data1 = $db_dpis1->get_array();
					$REVIEW_PER_NAME = $data1[PN_NAME] . $data1[PER_NAME] . " " . $data1[PER_SURNAME];
					$DEPARTMENT_ID = $data1[DEPARTMENT_ID];
					$POS_ORG_ID = $data1[ORG_ID];
					$POS_ORG_ID_1 = $data1[ORG_ID_1];
					$POS_ORG_ID_2 = $data1[ORG_ID_2];
					$LEVEL_NO = trim($data1[LEVEL_NO]);
					$PL_CODE = trim($data1[PL_CODE]);
					$PM_CODE = trim($data1[PM_CODE]);
				}

				$cmd = " select PL_NAME from PER_LINE where PL_CODE='$PL_CODE' ";
				$db_dpis1->send_cmd($cmd);		
				$data1 = $db_dpis1->get_array();
				$PL_NAME = trim($data1[PL_NAME]); 
				$PM_CODE = trim($data1[PM_CODE]);

				$cmd = " select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$LEVEL_NO' ";
				$db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$LEVEL_NAME = $data1[LEVEL_NAME];
				$POSITION_LEVEL = $data1[POSITION_LEVEL];
				if (!$POSITION_LEVEL) $POSITION_LEVEL = $LEVEL_NAME;

				$cmd = " select OT_CODE from PER_ORG where ORG_ID=$POS_ORG_ID ";
				$db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$OT_CODE = trim($data1[OT_CODE]);
		
				if ($DEPARTMENT_ID) $tmp_ORG_ID[] =  $DEPARTMENT_ID;
				if ($POS_ORG_ID)			$tmp_ORG_ID[] =  $POS_ORG_ID;
				if ($POS_ORG_ID_1)	$tmp_ORG_ID[] =  $POS_ORG_ID_1;
				if ($POS_ORG_ID_2)	$tmp_ORG_ID[] =  $POS_ORG_ID_2;
				$search_org_id = implode(", ", $tmp_ORG_ID);
				
				$cmd = " select ORG_ID, ORG_NAME from PER_ORG 	where ORG_ID in ($search_org_id) ";
				$db_dpis1->send_cmd($cmd);		
				while ( $data1 = $db_dpis1->get_array() ) {
					$DEPARTMENT_NAME = ($DEPARTMENT_ID == trim($data1[ORG_ID]))? 		trim($data1[ORG_NAME]) : "$DEPARTMENT_NAME";
					$ORG_NAME = ($POS_ORG_ID == trim($data1[ORG_ID]))?  trim($data1[ORG_NAME]) : $ORG_NAME ;
					$ORG_NAME_1 = ($POS_ORG_ID_1 == trim($data1[ORG_ID]))?  trim($data1[ORG_NAME]) : $ORG_NAME_1 ;
					$ORG_NAME_2 = ($POS_ORG_ID_2 == trim($data1[ORG_ID]))?  trim($data1[ORG_NAME]) : $ORG_NAME_2 ;
				}
			
				$cmd = " select PM_NAME from PER_MGT where PM_CODE='$PM_CODE' ";
				$db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$PM_NAME = $data1[PM_NAME]; 
				
				if ($PM_NAME) $PL_NAME_WORK = $PM_NAME." (".$PL_NAME.$POSITION_LEVEL.")";
				else $PL_NAME_WORK = $PL_NAME.$POSITION_LEVEL;
				
				if ($ORG_NAME=="-") $ORG_NAME = "";		
				if ($ORG_NAME_1=="-") $ORG_NAME_1 = "";		
				if ($ORG_NAME_2=="-") $ORG_NAME_2 = "";		
				if ($OT_CODE == "03") 
					if (!$ORG_NAME_2 && !$ORG_NAME_1 && $DEPARTMENT_NAME=="กรมการปกครอง") 
						$ORG_NAME_WORK = "ที่ทำการปกครอง".$ORG_NAME." ".$ORG_NAME;
					else 
						$ORG_NAME_WORK = trim($ORG_NAME_2." ".$ORG_NAME_1." ".$ORG_NAME);
				elseif ($OT_CODE == "01") $ORG_NAME_WORK = trim($ORG_NAME_2." ".$ORG_NAME_1." ".$ORG_NAME);
				else $ORG_NAME_WORK = trim($ORG_NAME_2." ".$ORG_NAME_1." ".$ORG_NAME);

				if ($PL_NAME_WORK && $ORG_NAME_WORK) {
					$cmd = " UPDATE  PER_KPI_FORM SET PL_NAME_WORK = '$PL_NAME_WORK', ORG_NAME_WORK = '$ORG_NAME_WORK' 
									WHERE KF_ID = $KF_ID ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
					//echo "$cmd<br>";
				}
			} // end while
	*/
			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_COM_NOTE ALTER CN_NAME MEMO ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COM_NOTE MODIFY CN_NAME VARCHAR2(2000) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_COM_NOTE ALTER CN_NAME TEXT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_COMMAND", "COM_DOC_TYPE","VARCHAR", "10", "NULL");
			add_field("PER_EDUCLEVEL", "EL_SHORTNAME","VARCHAR", "50", "NULL");
			add_field("PER_POS_NAME", "LEVEL_NO_MIN","VARCHAR", "10", "NULL");
			add_field("PER_POS_NAME", "LEVEL_NO_MAX","VARCHAR", "10", "NULL");
	
			$cmd = " update PER_CONTROL set CTRL_ALTER = 14 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
?>