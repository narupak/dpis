<?php 
$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET MENU_LABEL = 'R0495 รายชื่อข้าราชการ (โครงสร้างตามมอบหมายงาน)' WHERE MENU_LABEL = '`R0495 รายชื่อข้าราชการแยกตามโครงสร้างการมอบหมายงาน' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE PER_MOVMENT SET MOV_SUB_TYPE = 98 WHERE MOV_CODE = '11420' AND MOV_SUB_TYPE = 0 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_HEIR", "HEIR_TYPE","CHAR", "1", "NULL");
			add_field("PER_HEIR", "HEIR_ADDRESS","VARCHAR", "255", "NULL");
			add_field("PER_HEIR", "HEIR_PHONE","VARCHAR", "50", "NULL");
			add_field("PER_HEIR", "HEIR_ACTIVE","CHAR", "1", "NULL");
			add_field("PER_HEIR", "HEIR_SEQ","INTEGER2", "2", "NULL");

			$cmd = " UPDATE PER_HEIR SET HEIR_TYPE = 1 WHERE HEIR_TYPE IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_HEIR SET HEIR_STATUS = 1 WHERE HEIR_TYPE IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_EDUCMAJOR ALTER EM_NAME VARCHAR(255) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_EDUCMAJOR MODIFY EM_NAME VARCHAR2(255) ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_EDUCMAJOR MODIFY EM_NAME VARCHAR(255) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_PERFORMANCE_GOALS ALTER PG_EVALUATE NUMBER ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PERFORMANCE_GOALS MODIFY PG_EVALUATE NUMBER(3,2) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_PERFORMANCE_GOALS ALTER PG_EVALUATE DECIMAL(3,2) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_PERFORMANCE_GOALS ALTER KPI_TARGET_LEVEL1 NUMBER ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PERFORMANCE_GOALS MODIFY KPI_TARGET_LEVEL1 NUMBER(5,2) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_PERFORMANCE_GOALS ALTER KPI_TARGET_LEVEL1 DECIMAL(5,2) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_PERFORMANCE_GOALS ALTER KPI_TARGET_LEVEL2 NUMBER ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PERFORMANCE_GOALS MODIFY KPI_TARGET_LEVEL2 NUMBER(5,2) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_PERFORMANCE_GOALS ALTER KPI_TARGET_LEVEL2 DECIMAL(5,2) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_PERFORMANCE_GOALS ALTER KPI_TARGET_LEVEL3 NUMBER ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PERFORMANCE_GOALS MODIFY KPI_TARGET_LEVEL3 NUMBER(5,2) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_PERFORMANCE_GOALS ALTER KPI_TARGET_LEVEL3 DECIMAL(5,2) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_PERFORMANCE_GOALS ALTER KPI_TARGET_LEVEL4 NUMBER ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PERFORMANCE_GOALS MODIFY KPI_TARGET_LEVEL4 NUMBER(5,2) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_PERFORMANCE_GOALS ALTER KPI_TARGET_LEVEL4 DECIMAL(5,2) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_PERFORMANCE_GOALS ALTER KPI_TARGET_LEVEL5 NUMBER ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PERFORMANCE_GOALS MODIFY KPI_TARGET_LEVEL5 NUMBER(5,2) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_PERFORMANCE_GOALS ALTER KPI_TARGET_LEVEL5 DECIMAL(5,2) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DROP TABLE PER_POSTING ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " SELECT POST_DOCNO FROM PER_POSTINGHIS ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) {
				if($DPISDB=="odbc") 
					$cmd = " CREATE TABLE PER_POSTINGHIS(
					POST_ID INTEGER NOT NULL,	
					PER_ID INTEGER NOT NULL,	
					PER_CARDNO VARCHAR(13) NULL,
					POST_STARTDATE VARCHAR(19) NULL,
					POST_ENDDATE VARCHAR(19) NULL,
					POST_ENDTIME VARCHAR(5) NULL,
					POST_POSITION VARCHAR(100) NULL,	
					POST_ORG_NAME VARCHAR(255) NOT NULL,	
					POST_TEL VARCHAR(255) NULL,	
					POST_DOCNO VARCHAR(255) NULL,
					POST_DOCDATE VARCHAR(19) NULL,
					POST_REMARK MEMO NULL,
					AUDIT_FLAG CHAR(1) NULL,
					POST_SEQ_NO INTEGER2 NULL,
					UPDATE_USER INTEGER NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_POSTINGHIS PRIMARY KEY (POST_ID)) ";
				elseif($DPISDB=="oci8") 
					$cmd = " CREATE TABLE PER_POSTINGHIS(
					POST_ID NUMBER(10) NOT NULL,	
					PER_ID NUMBER(10) NOT NULL,	
					PER_CARDNO VARCHAR2(13) NULL,
					POST_STARTDATE VARCHAR2(19) NULL,
					POST_ENDDATE VARCHAR2(19) NULL,
					POST_ENDTIME VARCHAR2(5) NULL,
					POST_POSITION VARCHAR2(100) NULL,	
					POST_ORG_NAME VARCHAR2(255) NOT NULL,	
					POST_TEL VARCHAR2(255) NULL,	
					POST_DOCNO VARCHAR2(255) NULL,
					POST_DOCDATE VARCHAR2(19) NULL,
					POST_REMARK VARCHAR2(1000) NULL,
					AUDIT_FLAG CHAR(1) NULL,
					POST_SEQ_NO NUMBER(5) NULL,
					UPDATE_USER NUMBER(11) NOT NULL,
					UPDATE_DATE VARCHAR2(19) NOT NULL,		
					CONSTRAINT PK_PER_POSTINGHIS PRIMARY KEY (POST_ID)) ";
				elseif($DPISDB=="mysql")
					$cmd = " CREATE TABLE PER_POSTINGHIS(
					POST_ID INTEGER(10) NOT NULL,	
					PER_ID INTEGER(10) NOT NULL,	
					PER_CARDNO VARCHAR(13) NULL,
					POST_STARTDATE VARCHAR(19) NULL,
					POST_ENDDATE VARCHAR(19) NULL,
					POST_ENDTIME VARCHAR(5) NULL,
					POST_POSITION VARCHAR(100) NULL,	
					POST_ORG_NAME VARCHAR(255) NOT NULL,	
					POST_TEL VARCHAR(255) NULL,	
					POST_DOCNO VARCHAR(255) NULL,
					POST_DOCDATE VARCHAR(19) NULL,
					POST_REMARK TEXT NULL,
					AUDIT_FLAG CHAR(1) NULL,
					POST_SEQ_NO SMALLINT(5) NULL,
					UPDATE_USER INTEGER(11) NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_POSTINGHIS PRIMARY KEY (POST_ID)) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			}

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_KPI_FORM, PER_PERSONAL SET PER_KPI_FORM.LEVEL_NO = 
								  PER_PERSONAL.LEVEL_NO WHERE PER_PERSONAL.PER_ID = PER_KPI_FORM.PER_ID AND PER_KPI_FORM.LEVEL_NO IS NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_KPI_FORM A SET A.LEVEL_NO = 
								  (SELECT B.LEVEL_NO FROM PER_PERSONAL B WHERE A.PER_ID = B.PER_ID) WHERE A.LEVEL_NO IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'rpt_gmis.html' WHERE LINKTO_WEB = 'rpt_mpis.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_gmis.html' ";
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
								  VALUES (1, 'TH', $MAX_ID, 18, 'P1118 ระบบสารสนเทศเพื่อการวางแผนกำลังคนภาครัฐ (GMIS)', 'S', 'W', 'rpt_gmis.html', 0, 35, 251, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 18, 'P1118 ระบบสารสนเทศเพื่อการวางแผนกำลังคนภาครัฐ (GMIS)', 'S', 'W', 'rpt_gmis.html', 0, 35, 251, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " SELECT tempOrganize FROM GMIS_GPIS ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) {
				$cmd = " DROP TABLE GMIS1 ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " DROP TABLE GMIS1_FLOW_IN ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " DROP TABLE GMIS1_FLOW_OUT ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " DROP TABLE GMIS2 ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " CREATE TABLE GMIS_GPIS(
				tempMinistry VARCHAR2(100) NULL,	
				tempOrganize VARCHAR2(100) NULL,
				tempDivisionName VARCHAR2(100) NULL,
				tempOrganizeType VARCHAR2(50) NULL,		
				tempPositionNo VARCHAR2(20) NULL,
				tempManagePosition VARCHAR2(100) NULL,
				tempLine VARCHAR2(100) NULL,
				tempPositionType VARCHAR2(20) NULL,
				tempLevel VARCHAR2(100) NULL,
				tempClName VARCHAR2(50) NULL,		
				tempSkill VARCHAR2(100) NULL,		
				tempCountry VARCHAR2(50) NULL,		
				tempProvince VARCHAR2(50) NULL,		
				tempPositionStatus VARCHAR2(20) NULL,		
				tempClass VARCHAR2(30) NULL,		
				tempPrename VARCHAR2(30) NULL,		
				tempFirstName VARCHAR2(50) NULL,		
				tempLastName VARCHAR2(50) NULL,		
				tempCardNo VARCHAR2(13) NULL,		
				tempGender VARCHAR2(10) NULL,		
				tempStatusDisability VARCHAR2(10) NULL,		
				tempReligion VARCHAR2(20) NULL,
				tempBirthDate VARCHAR2(10) NULL,		
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
				tempStartDate VARCHAR2(10) NULL,		
				tempFlowDate VARCHAR2(10) NULL,		
				tempResignDate VARCHAR2(10) NULL,		
				tempPromoteDate VARCHAR2(10) NULL,		
				tempDecoration VARCHAR2(50) NULL,		
				tempUnion VARCHAR2(10) NULL,		
				tempResult1 VARCHAR2(50) NULL,		
				tempPercentSalary1 VARCHAR2(10) NULL,		
				tempResult2 VARCHAR2(50) NULL,		
				tempPercentSalary2 VARCHAR2(10) NULL,		
				CONSTRAINT PK_GMIS_GPIS PRIMARY KEY (tempPositionNo)) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " CREATE TABLE GMIS_GPIS_FLOW_IN(
				tempMinistry VARCHAR2(100) NULL,	
				tempOrganize VARCHAR2(100) NULL,
				tempDivisionName VARCHAR2(100) NULL,
				tempOrganizeType VARCHAR2(50) NULL,		
				tempPositionNo VARCHAR2(20) NULL,
				tempManagePosition VARCHAR2(100) NULL,
				tempLine VARCHAR2(100) NULL,
				tempPositionType VARCHAR2(20) NULL,
				tempLevel VARCHAR2(100) NULL,
				tempClName VARCHAR2(50) NULL,		
				tempSkill VARCHAR2(100) NULL,		
				tempCountry VARCHAR2(50) NULL,		
				tempProvince VARCHAR2(50) NULL,		
				tempPositionStatus VARCHAR2(20) NULL,		
				tempClass VARCHAR2(30) NULL,		
				tempPrename VARCHAR2(30) NULL,		
				tempFirstName VARCHAR2(50) NULL,		
				tempLastName VARCHAR2(50) NULL,		
				tempCardNo VARCHAR2(13) NULL,		
				tempGender VARCHAR2(10) NULL,		
				tempStatusDisability VARCHAR2(10) NULL,		
				tempReligion VARCHAR2(20) NULL,
				tempBirthDate VARCHAR2(10) NULL,		
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
				tempStartDate VARCHAR2(10) NULL,		
				tempFlowDate VARCHAR2(10) NULL,		
				tempResignDate VARCHAR2(10) NULL,		
				tempPromoteDate VARCHAR2(10) NULL,		
				tempDecoration VARCHAR2(50) NULL,		
				tempUnion VARCHAR2(10) NULL,		
				tempResult1 VARCHAR2(50) NULL,		
				tempPercentSalary1 VARCHAR2(10) NULL,		
				tempResult2 VARCHAR2(50) NULL,		
				tempPercentSalary2 VARCHAR2(10) NULL,		
				CONSTRAINT PK_GMIS_GPIS_FLOW_IN PRIMARY KEY (tempPositionNo, tempStartDate)) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " CREATE TABLE GMIS_GPIS_FLOW_OUT(
				tempMinistry VARCHAR2(100) NULL,	
				tempOrganize VARCHAR2(100) NULL,
				tempDivisionName VARCHAR2(100) NULL,
				tempOrganizeType VARCHAR2(50) NULL,		
				tempPositionNo VARCHAR2(20) NULL,
				tempManagePosition VARCHAR2(100) NULL,
				tempLine VARCHAR2(100) NULL,
				tempPositionType VARCHAR2(20) NULL,
				tempLevel VARCHAR2(100) NULL,
				tempClName VARCHAR2(50) NULL,		
				tempSkill VARCHAR2(100) NULL,		
				tempCountry VARCHAR2(50) NULL,		
				tempProvince VARCHAR2(50) NULL,		
				tempPositionStatus VARCHAR2(20) NULL,		
				tempClass VARCHAR2(30) NULL,		
				tempPrename VARCHAR2(30) NULL,		
				tempFirstName VARCHAR2(50) NULL,		
				tempLastName VARCHAR2(50) NULL,		
				tempCardNo VARCHAR2(13) NULL,		
				tempGender VARCHAR2(10) NULL,		
				tempStatusDisability VARCHAR2(10) NULL,		
				tempReligion VARCHAR2(20) NULL,
				tempBirthDate VARCHAR2(10) NULL,		
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
				tempStartDate VARCHAR2(10) NULL,		
				tempFlowDate VARCHAR2(10) NULL,		
				tempResignDate VARCHAR2(10) NULL,		
				tempPromoteDate VARCHAR2(10) NULL,		
				tempDecoration VARCHAR2(50) NULL,		
				tempUnion VARCHAR2(10) NULL,		
				tempResult1 VARCHAR2(50) NULL,		
				tempPercentSalary1 VARCHAR2(10) NULL,		
				tempResult2 VARCHAR2(50) NULL,		
				tempPercentSalary2 VARCHAR2(10) NULL,		
				CONSTRAINT PK_GMIS_GPIS_FLOW_OUT PRIMARY KEY (tempPositionNo, tempResignDate)) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
/* ลูกจ้าง
				$cmd = " CREATE TABLE GMIS_PEIS(
				tempMinistry VARCHAR2(100) NULL,	
				tempOrganize VARCHAR2(100) NULL,
				tempDivisionName VARCHAR2(100) NULL,
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
				tempLevel VARCHAR2(100) NULL,
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
				CONSTRAINT PK_GMIS_PEIS PRIMARY KEY ()) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); */
/* พนักงานราชการ
				$cmd = " CREATE TABLE GMIS_GEIS(
				tempMinistry VARCHAR2(100) NULL,	
				tempOrganize VARCHAR2(100) NULL,
				tempDivisionName VARCHAR2(100) NULL,
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
				tempLevel VARCHAR2(100) NULL,
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
				CONSTRAINT PK_GMIS_PEIS PRIMARY KEY ()) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); */
			}

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET MENU_LABEL = 'P0131 ภาพถ่าย/ลายเซ็น' WHERE MENU_LABEL = 'P0131 ภาพถ่าย/ลายเซ็นต์' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			add_field("PER_HEIR", "HEIR_RATIO","NUMBER", "5,2", "NULL");

			$cmd = " SELECT IMPORT_FILENAME FROM IMPORT_TEMP ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) {
				if($DPISDB=="odbc") 
					$cmd = " CREATE TABLE IMPORT_TEMP(
					IMPORT_FILENAME VARCHAR(40) NOT NULL,
					ROW_NUM INTEGER NOT NULL,
					DATA_PACK MEMO NULL,
					ERROR CHAR(1) NULL,		
					DATA_IN MEMO NULL,
					CONSTRAINT PK_IMPORT_TEMP PRIMARY KEY (IMPORT_FILENAME, ROW_NUM)) ";
				elseif($DPISDB=="oci8") 
					$cmd = " CREATE TABLE IMPORT_TEMP(
					IMPORT_FILENAME VARCHAR2(40) NOT NULL,
					ROW_NUM NUMBER(10) NOT NULL,
					DATA_PACK VARCHAR2(4000) NULL,
					ERROR CHAR(1) NULL,		
					DATA_IN VARCHAR2(4000) NULL,
					CONSTRAINT PK_IMPORT_TEMP PRIMARY KEY (IMPORT_FILENAME, ROW_NUM)) ";
				elseif($DPISDB=="mysql")
					$cmd = " CREATE TABLE IMPORT_TEMP(
					IMPORT_FILENAME VARCHAR(40) NOT NULL,
					ROW_NUM INTEGER(10) NOT NULL,
					DATA_PACK TEXT NULL,
					ERROR CHAR(1) NULL,		
					DATA_IN TEXT NULL,
					CONSTRAINT PK_IMPORT_TEMP PRIMARY KEY (IMPORT_FILENAME, ROW_NUM)) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			}

			add_field("PER_POSITION", "POS_VACANT_DATE","VARCHAR", "19", "NULL");

			$cmd = " UPDATE PER_POSITION SET POS_VACANT_DATE = POS_CHANGE_DATE WHERE POS_VACANT_DATE IS NULL AND POS_CHANGE_DATE IS NOT NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'import_file.html?form=per_slip_form.php' 
							  WHERE LINKTO_WEB = 'convert_texttoslip.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'import_file.html?form=per_decoratehis_form.php' 
							  WHERE LINKTO_WEB = 'select_soc_excel.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'import_file.html?form=per_salaryhis_form.php' 
							  WHERE LINKTO_WEB = 'select_cgd_salary_excel.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			add_field("PER_KPI_COMPETENCE", "KC_SELF","NUMBER", "4,2", "NULL");
			add_field("PER_ABSENTTYPE", "AB_TYPE","VARCHAR", "2", "NULL");
	
			$cmd = " UPDATE PER_ABSENTTYPE SET AB_TYPE = '01' WHERE AB_NAME = 'ลาป่วย' AND AB_TYPE IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_ABSENTTYPE SET AB_TYPE = '02' WHERE AB_NAME = 'ลาคลอดบุตร' AND AB_TYPE IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_ABSENTTYPE SET AB_TYPE = '03' WHERE AB_NAME = 'ลากิจส่วนตัว' AND AB_TYPE IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_ABSENTTYPE SET AB_TYPE = '04' WHERE AB_NAME = 'ลาพักผ่อน' AND AB_TYPE IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_ABSENTTYPE SET AB_TYPE = '05' WHERE AB_NAME = 'ลาอุปสมบทหรือการลาไปประกอบพิธีฮัจย์' AND AB_TYPE IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_ABSENTTYPE SET AB_TYPE = '06' WHERE AB_NAME = 'ลาเข้ารับการตรวจเลือกหรือเข้ารับการเตรียมพล' AND AB_TYPE IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_ABSENTTYPE SET AB_TYPE = '07' WHERE AB_NAME = 'ลาไปศึกษา ฝึกอบรม ปฏิบัติการวิจัย หรือดูงาน' AND AB_TYPE IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_ABSENTTYPE SET AB_TYPE = '08' WHERE AB_NAME = 'ลาไปปฏิบัติงานในองค์การระหว่างประเทศ' AND AB_TYPE IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_ABSENTTYPE SET AB_TYPE = '09' WHERE AB_NAME = 'ลาติดตามคู่สมรส' AND AB_TYPE IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_ABSENTTYPE SET AB_TYPE = '10' WHERE (AB_NAME = 'สาย' OR AB_NAME = 'มาสาย') AND AB_TYPE IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_ABSENTTYPE SET AB_TYPE = '11' WHERE AB_NAME = 'ลากิจส่วนตัวเพื่อเลี้ยงดูบุตร' AND AB_TYPE IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_ABSENTTYPE SET AB_TYPE = '12' WHERE AB_NAME = 'ลาป่วยจำเป็น' AND AB_TYPE IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_ABSENTTYPE SET AB_TYPE = '13' WHERE AB_NAME = 'ขาดราชการ' AND AB_TYPE IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_ABSENTTYPE SET AB_TYPE = '14' WHERE AB_NAME = 'ลาไปช่วยเหลือภริยาที่คลอดบุตร' AND AB_TYPE IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_ABSENTTYPE SET AB_TYPE = '15' WHERE AB_NAME = 'ลาไปฟื้นฟูสมรรถภาพด้านอาชีพ' AND AB_TYPE IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV1 SET TYPE_LINKTO = 'N', LINKTO_WEB = NULL WHERE TYPE_LINKTO = 'W' AND LINKTO_WEB = 'data_cancel_comdtl.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();
	
			$cmd = " select max(AS_ID) as max_id from PER_ABSENTSUM ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$AS_ID = $data[max_id] + 1;
					
			$cmd = " SELECT PER_ID, ABS_STARTDATE, ABS_ENDDATE, PER_CARDNO FROM PER_ABSENTHIS
							  WHERE ABS_STARTDATE IS NOT NULL AND ABS_ENDDATE IS NOT NULL
							  ORDER BY PER_ID, ABS_STARTDATE, ABS_ENDDATE ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			while($data = $db_dpis->get_array()){
				$PER_ID = $data[PER_ID];
				$ABS_STARTDATE = trim($data[ABS_STARTDATE]);
				$ABS_ENDDATE = trim($data[ABS_ENDDATE]);
				$PER_CARDNO = trim($data[PER_CARDNO]);

				if (substr($ABS_STARTDATE,5,2) > "09" || substr($ABS_STARTDATE,5,2) < "04") $AS_CYCLE = 1;
				elseif (substr($ABS_STARTDATE,5,2) > "03" && substr($ABS_STARTDATE,5,2) < "10")	$AS_CYCLE = 2;
				$AS_YEAR = substr($ABS_STARTDATE, 0, 4);
				if($AS_CYCLE==1){	//ตรวจสอบรอบการลา
					if (substr($ABS_STARTDATE,5,2) > "09") $AS_YEAR += 1;
					$START_DATE = ($AS_YEAR - 1) . "-10-01";
					$END_DATE = $AS_YEAR . "-03-31";
				}else if($AS_CYCLE==2){
					$START_DATE = $AS_YEAR . "-04-01";
					$END_DATE = $AS_YEAR . "-09-30"; 
				}
				$AS_YEAR = $AS_YEAR + 543;

				$cmd="select  AS_YEAR from PER_ABSENTSUM where PER_ID=$PER_ID and AS_YEAR = '$AS_YEAR' and AS_CYCLE = $AS_CYCLE ";
				$count=$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error(); echo "<hr>$cmd<br>";
				if(!$count) { 
					$cmd = " insert into PER_ABSENTSUM (AS_ID, PER_ID, AS_YEAR, AS_CYCLE, START_DATE,  END_DATE, PER_CARDNO, UPDATE_USER, UPDATE_DATE)
									values ($AS_ID, $PER_ID, '$AS_YEAR', $AS_CYCLE, '$START_DATE', '$END_DATE', '$PER_CARDNO', $SESS_USERID, '$UPDATE_DATE') ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
					$AS_ID++;
				}
			} // end while						

			if ($MFA_FLAG==1) {
				$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 
								  WHERE LINKTO_WEB = 'data_salfactor_comdtl.html' ";
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
									  VALUES (1, 'TH', $MAX_ID, 9, 'P0409 บัญชีแนบท้ายคำสั่งให้ได้รับเงินเดือนเพิ่มขึ้นตามปัจจัยที่กำหนด', 'S', 'W', 'data_salfactor_comdtl.html', 0, 35, 244, 
									  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();

					$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
									  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
									  CREATE_BY, UPDATE_DATE, UPDATE_BY)
									  VALUES (1, 'EN', $MAX_ID, 9, 'P0409 บัญชีแนบท้ายคำสั่งให้ได้รับเงินเดือนเพิ่มขึ้นตามปัจจัยที่กำหนด', 'S', 'W', 'data_salfactor_comdtl.html', 0, 35, 244, 
									  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();
				}

				$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO, COM_ACTIVE)
								VALUES ('5061', 'คส. 6.1', 'คำสั่งให้ข้าราชการได้รับเงินเดือนเพิ่มขึ้นตามปัจจัยที่กำหนด', '5061', 45, 1) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			}

			$cmd = " UPDATE PER_SALARYHIS SET SAH_KF_CYCLE = 1 WHERE SUBSTR(SAH_EFFECTIVEDATE,6,5) = '04-01' AND SAH_KF_CYCLE IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
	
			$cmd = " UPDATE PER_SALARYHIS SET SAH_KF_CYCLE = 2 WHERE SUBSTR(SAH_EFFECTIVEDATE,6,5) = '10-01' AND SAH_KF_CYCLE IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
	
			$cmd = " UPDATE PER_SALARYHIS SET SAH_KF_YEAR = TO_NUMBER(SUBSTR(SAH_EFFECTIVEDATE,1,4)) + 543
							 WHERE SUBSTR(SAH_EFFECTIVEDATE,6,5) < '10-01' AND SAH_KF_YEAR IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
	
			$cmd = " UPDATE PER_SALARYHIS SET SAH_KF_YEAR = TO_NUMBER(SUBSTR(SAH_EFFECTIVEDATE,1,4)) + 544
							 WHERE SUBSTR(SAH_EFFECTIVEDATE,6,5) >= '10-01' AND SAH_KF_YEAR IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
	
			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_SCHOLAR, PER_INSTITUTE SET PER_SCHOLAR.CT_CODE = 
								  PER_INSTITUTE.CT_CODE WHERE PER_INSTITUTE.INS_CODE = PER_SCHOLAR.INS_CODE AND 
								  PER_SCHOLAR.INS_CODE IS NOT NULL AND PER_SCHOLAR.CT_CODE IS NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_SCHOLAR A SET A.CT_CODE = 
								  (SELECT B.CT_CODE FROM PER_INSTITUTE B WHERE A.INS_CODE = B.INS_CODE) 
								  WHERE A.INS_CODE IS NOT NULL AND A.CT_CODE IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_STANDARD_COMPETENCE ON PER_STANDARD_COMPETENCE (LEVEL_NO, DEPARTMENT_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_FAMILY", "FML_OLD_SURNAME","VARCHAR", "100", "NULL");
			add_field("PER_PERSONAL", "PER_BIRTH_PLACE","VARCHAR", "100", "NULL");
			add_field("PER_PERSONAL", "PER_SCAR","VARCHAR", "100", "NULL");

			$cmd = " UPDATE PER_LEVEL SET POSITION_TYPE = 'กลุ่มงานบริการพื้นฐาน' WHERE LEVEL_NO in ('บ1', 'บ2', 'บ3') AND POSITION_TYPE IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
	
			$cmd = " UPDATE PER_LEVEL SET POSITION_TYPE = 'กลุ่มงานสนับสนุน' WHERE LEVEL_NO in ('ส1', 'ส2', 'ส3', 'ส4', 'ส5', 'ส6', 'ส7') AND POSITION_TYPE IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
	
			$cmd = " UPDATE PER_LEVEL SET POSITION_TYPE = 'กลุ่มงานช่าง' WHERE LEVEL_NO in ('ช1', 'ช2', 'ช3', 'ช4', 'ช5', 'ช6', 'ช7') AND POSITION_TYPE IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
	
			$cmd = " UPDATE PER_LEVEL SET POSITION_TYPE = 'กลุ่มงานเทคนิคพิเศษ' WHERE LEVEL_NO in ('ท1', 'ท2', 'ท3', 'ท4', 'ท5') AND POSITION_TYPE IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
	
			if ($MFA_FLAG==1) {
				$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE MENU_LABEL = 'P09 ประจำการในต่างประเทศ' ";
				$count_data = $db->send_cmd($cmd);
				//$db->show_error();
				if (!$count_data) {
					$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV1 ";
					$db->send_cmd($cmd);
					//$db->show_error();
					$data = $db->get_array();
					$MAX_ID1 = $data[MAX_ID] + 1;

					$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
									  FLAG_SHOW, TYPE_LINKTO, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, UPDATE_DATE, 
									  UPDATE_BY)
									  VALUES (1, 'TH', $MAX_ID1, 9, 'P09 ประจำการในต่างประเทศ', 'S', 'N', 0, 35, $CREATE_DATE, $UPDATE_BY, 
									  $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();

					$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
									  FLAG_SHOW, TYPE_LINKTO, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, UPDATE_DATE, 
									  UPDATE_BY)
									  VALUES (1, 'EN', $MAX_ID1, 9, 'P09 ประจำการในต่างประเทศ', 'S', 'N', 0, 35, $CREATE_DATE, $UPDATE_BY, 
									  $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();
				}

				$cmd = " SELECT MENU_ID FROM BACKOFFICE_MENU_BAR_LV1 WHERE MENU_LABEL = 'P09 ประจำการในต่างประเทศ' ";
				$db->send_cmd($cmd);
				//$db->show_error();
				$data = $db->get_array();
				$MENU_ID = $data[MENU_ID];

				$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'data_posting_inquire.html' ";
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
									  VALUES (1, 'TH', $MAX_ID, 1, 'P0901 การพิจารณาข้าราชการไปประจำการในต่างประเทศ', 'S', 'W', 'data_posting_inquire.html', 0, 35, $MENU_ID, 
									  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();

					$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
									  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
									  CREATE_BY, UPDATE_DATE, UPDATE_BY)
									  VALUES (1, 'EN', $MAX_ID, 1, 'P0901 การพิจารณาข้าราชการไปประจำการในต่างประเทศ', 'S', 'W', 'data_posting_inquire.html', 0, 35, $MENU_ID, 
									  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();
				}
			}

			add_field("PER_ORG", "ORG_ZONE","CHAR", "1", "NULL");
			add_field("PER_ORG_ASS", "ORG_ZONE","CHAR", "1", "NULL");
			add_field("IMPORT_TEMP", "DATA_IN","MEMO", "4000", "NULL");

			if ($ISCS_FLAG==1) {
				$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'rpt_R010022.html' ";
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
									  VALUES (1, 'TH', $MAX_ID, 22, 'H22 รายชื่อข้าราชการซึ่งจะมีอายุครบ 60 ปีบริบูรณ์', 'S', 'W', 
									  'rpt_R010022.html', 0, 43, $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();

					$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
									  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
									  UPDATE_DATE, UPDATE_BY)
									  VALUES (1, 'EN', $MAX_ID, 22, 'H22 รายชื่อข้าราชการซึ่งจะมีอายุครบ 60 ปีบริบูรณ์', 'S', 'W', 
									  'rpt_R010022.html', 0, 43, $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();
				}

				$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'rpt_R010023.html' ";
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
									  VALUES (1, 'TH', $MAX_ID, 23, 'H23 รายชื่อข้าราชการแสดงระยะเวลาการดำรงตำแหน่ง', 'S', 'W', 
									  'rpt_R010023.html', 0, 43, $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();

					$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
									  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
									  UPDATE_DATE, UPDATE_BY)
									  VALUES (1, 'EN', $MAX_ID, 23, 'H23 รายชื่อข้าราชการแสดงระยะเวลาการดำรงตำแหน่ง', 'S', 'W', 
									  'rpt_R010023.html', 0, 43, $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();
				}
			}

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_POSITIONHIS, PER_MGT SET PER_POSITIONHIS.POH_PM_NAME = 
								  PER_MGT.PM_NAME WHERE PER_MGT.PM_CODE = PER_POSITIONHIS.PM_CODE AND 
								  PER_POSITIONHIS.PM_CODE IS NOT NULL AND PER_POSITIONHIS.POH_PM_NAME IS NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_POSITIONHIS A SET A.POH_PM_NAME = 
								  (SELECT B.PM_NAME FROM PER_MGT B WHERE A.PM_CODE = B.PM_CODE) 
								  WHERE A.PM_CODE IS NOT NULL AND A.POH_PM_NAME IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_PERSONAL ALTER PER_EMAIL VARCHAR(100) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PERSONAL MODIFY PER_EMAIL VARCHAR2(100) ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_PERSONAL MODIFY PER_EMAIL VARCHAR(100) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_PERSONAL", "PER_RENEW","SINGLE", "1", "NULL");
			add_field("PER_ABSENTHIS", "ABS_DAY_MFA","NUMBER", "6,2", "NULL");
			add_field("PER_POSTINGHIS", "POST_REMARK_NAME","VARCHAR", "100", "NULL");
			add_field("PER_COMDTL", "CMD_DATE3","VARCHAR", "19", "NULL");

			if ($MFA_FLAG==1) {
				$cmd = " SELECT ORG_ID FROM PER_ORG_REMARK ";
				$count_data = $db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
				if (!$count_data) {
					if($DPISDB=="odbc") 
						$cmd = " CREATE TABLE PER_ORG_REMARK(
						ORG_ID INTEGER NOT NULL,
						SEQ_NO INTEGER2 NOT NULL,
						ORG_REMARK VARCHAR(255) NOT NULL,	
						OR_ACTIVE SINGLE NOT NULL,
						UPDATE_USER INTEGER NOT NULL,
						UPDATE_DATE VARCHAR(19) NOT NULL,		
						CONSTRAINT PK_PER_ORG_REMARK PRIMARY KEY (ORG_ID, SEQ_NO)) ";
					elseif($DPISDB=="oci8") 
						$cmd = " CREATE TABLE PER_ORG_REMARK(
						ORG_ID NUMBER(10) NOT NULL,
						SEQ_NO NUMBER(3) NULL,
						ORG_REMARK VARCHAR2(255) NOT NULL,	
						OR_ACTIVE NUMBER(1) NOT NULL,
						UPDATE_USER NUMBER(11) NOT NULL,
						UPDATE_DATE VARCHAR2(19) NOT NULL,		
						CONSTRAINT PK_PER_ORG_REMARK PRIMARY KEY (ORG_ID, SEQ_NO)) ";
					elseif($DPISDB=="mysql")
						$cmd = " CREATE TABLE PER_ORG_REMARK(
						ORG_ID INTEGER(10) NOT NULL,
						SEQ_NO SMALLINT(3) NULL,
						ORG_REMARK VARCHAR(255) NOT NULL,	
						OR_ACTIVE SMALLINT(1) NOT NULL,
						UPDATE_USER INTEGER(11) NOT NULL,
						UPDATE_DATE VARCHAR(19) NOT NULL,		
						CONSTRAINT PK_PER_ORG_REMARK PRIMARY KEY (ORG_ID, SEQ_NO)) ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				}

				$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'master_table_org_remark.html?table=PER_ORG_REMARK' ";
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
									  VALUES (1, 'TH', $MAX_ID, 5, 'M0205 หมายเหตุของสถานเอกอัครราชทูต/สถานกงสุลใหญ่', 'S', 'W', 'master_table_org_remark.html?table=PER_ORG_REMARK', 0, 9, 302, 
									  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();

					$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
									  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
									  CREATE_BY, UPDATE_DATE, UPDATE_BY)
									  VALUES (1, 'EN', $MAX_ID, 5, 'M0205 หมายเหตุของสถานเอกอัครราชทูต/สถานกงสุลใหญ่', 'S', 'W', 'master_table_org_remark.html?table=PER_ORG_REMARK', 0, 9, 302, 
									  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();
				}
			}

			$cmd = " SELECT COUNT(LEVEL_NO) AS COUNT_DATA FROM PER_STANDARD_COMPETENCE ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) {
				if($DPISDB=="odbc") 
					$cmd = " CREATE TABLE PER_STANDARD_COMPETENCE(
					LEVEL_NO VARCHAR(10) NOT NULL,
					CP_CODE VARCHAR(3) NOT NULL,	
					TARGET_LEVEL SINGLE NULL,
					UPDATE_USER INTEGER2 NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_STANDARD_COMPETENCE PRIMARY KEY (LEVEL_NO, CP_CODE)) ";
				elseif($DPISDB=="oci8") 
					$cmd = " CREATE TABLE PER_STANDARD_COMPETENCE(
					LEVEL_NO VARCHAR2(10) NOT NULL,
					CP_CODE VARCHAR2(3) NOT NULL,	
					TARGET_LEVEL NUMBER(1) NULL,
					UPDATE_USER NUMBER(5) NOT NULL,
					UPDATE_DATE VARCHAR2(19) NOT NULL,		
					CONSTRAINT PK_PER_STANDARD_COMPETENCE PRIMARY KEY (LEVEL_NO, CP_CODE)) ";
				elseif($DPISDB=="mysql")
					$cmd = " CREATE TABLE PER_STANDARD_COMPETENCE(
					LEVEL_NO VARCHAR(10) NOT NULL,
					CP_CODE VARCHAR(3) NOT NULL,	
					TARGET_LEVEL SMALLINT(1) NULL,
					UPDATE_USER SMALLINT(5) NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_STANDARD_COMPETENCE PRIMARY KEY (LEVEL_NO, CP_CODE)) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			} // end if

			$count_data = add_field("PER_STANDARD_COMPETENCE", "DEPARTMENT_ID","INTEGER", "10", "NULL");
			if (!$count_data) {
				if ($CTRL_TYPE == 4)	{
					$cmd = " UPDATE PER_STANDARD_COMPETENCE SET DEPARTMENT_ID=$DEPARTMENT_ID WHERE DEPARTMENT_ID IS NULL ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				} else {
					if ($DEPARTMENT_ID) $arr_dept[] = $DEPARTMENT_ID;
					elseif($CTRL_TYPE==1 || $CTRL_TYPE==2 || $CTRL_TYPE==3 || $BKK_FLAG==1){
						$cmd = " SELECT DISTINCT DEPARTMENT_ID FROM PER_POSITION WHERE DEPARTMENT_ID > 1 AND POS_STATUS = 1 ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
						while($data = $db_dpis->get_array()) $arr_dept[] = $data[DEPARTMENT_ID];
					}else $arr_dept[] = $DEPARTMENT_ID;

					for ( $j=0; $j<count($arr_dept); $j++ ) { 
						$level = array(	"O1", "O2", "O3", "O4", "K1", "K2", "K3", "K4", "K5", "D1", "D2", "M1", "M2" );
						if ($BKK_FLAG==1)
							$target = array(	1, 1, 2, 3, 1, 2, 3, 3, 3, 3, 3, 3, 3 );
						else
							$target = array(	1, 1, 2, 3, 1, 2, 3, 4, 5, 3, 4, 4, 5 );
						for ( $i=0; $i<count($level); $i++ ) { 
							$cmd = " SELECT CP_CODE FROM PER_COMPETENCE WHERE DEPARTMENT_ID = $arr_dept[$j] AND CP_MODEL in (1,3) ";
							$db_dpis2->send_cmd($cmd);
							//$db_dpis2->show_error();
							while($data2 = $db_dpis2->get_array()){
								$CP_CODE = trim($data2[CP_CODE]);
								if ($level[$i]=="O1" && substr($CP_CODE,0,1)=="3") $TARGET_LEVEL = 0; else $TARGET_LEVEL = $target[$i]; 
								$cmd = " INSERT INTO PER_STANDARD_COMPETENCE (LEVEL_NO, CP_CODE, DEPARTMENT_ID, TARGET_LEVEL, UPDATE_USER, 
												UPDATE_DATE)
												VALUES ('$level[$i]', '$CP_CODE', $arr_dept[$j], $TARGET_LEVEL,  $SESS_USERID, '$UPDATE_DATE') ";
								$db_dpis->send_cmd($cmd);
								//$db_dpis->show_error();
							} // end for
						} // end for
						
						if ($BKK_FLAG!=1) {
							$level = array(	"D1", "D2", "M1", "M2" );
							$target = array(	1, 2, 3, 4 );
							for ( $i=0; $i<count($level); $i++ ) { 
								$cmd = " SELECT CP_CODE FROM PER_COMPETENCE WHERE DEPARTMENT_ID = $arr_dept[$j] AND CP_MODEL = 2 ";
								$db_dpis2->send_cmd($cmd);
								//$db_dpis2->show_error();
								while($data2 = $db_dpis2->get_array()){
									$CP_CODE = trim($data2[CP_CODE]);
									$cmd = " INSERT INTO PER_STANDARD_COMPETENCE (LEVEL_NO, CP_CODE, DEPARTMENT_ID, TARGET_LEVEL, UPDATE_USER, 
													UPDATE_DATE)
													VALUES ('$level[$i]', '$CP_CODE', $arr_dept[$j], $target[$i],  $SESS_USERID, '$UPDATE_DATE') ";
									$db_dpis->send_cmd($cmd);
									//$db_dpis->show_error();
								} // end while
							} // end for
						}	// end if	
					} // end for
				}	// end if	

				$cmd = " DELETE FROM PER_STANDARD_COMPETENCE WHERE DEPARTMENT_ID IS NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				if($DPISDB=="oci8") {
					$cmd = " ALTER TABLE PER_STANDARD_COMPETENCE MODIFY DEPARTMENT_ID NOT NULL ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();

					$cmd = " ALTER TABLE PER_STANDARD_COMPETENCE DROP CONSTRAINT PK_PER_STANDARD_COMPETENCE ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();

					$cmd = " DROP INDEX PK_PER_STANDARD_COMPETENCE ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();

					$cmd = " ALTER TABLE PER_STANDARD_COMPETENCE ADD CONSTRAINT PK_PER_STANDARD_COMPETENCE PRIMARY KEY 
									  (LEVEL_NO, CP_CODE, DEPARTMENT_ID) ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				}
			}

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET MENU_LABEL = 'P0138 ประวัติการลาออก (ไม่นับอายุราชการ)' WHERE MENU_LABEL = 'P0138 ประวัติการไปประกอบอาชีพอื่น' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			add_field("PER_OTHER_OCCUPATION", "OO_STARTDATE","VARCHAR", "19", "NULL");
			add_field("PER_OTHER_OCCUPATION", "OO_ENDDATE","VARCHAR", "19", "NULL");
			add_field("PER_OTHER_OCCUPATION", "OO_DAY","NUMBER", "10,2", "NULL");

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_FAMILY ALTER OC_CODE VARCHAR(3) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_FAMILY MODIFY OC_CODE VARCHAR2(3) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_FAMILY MODIFY OC_CODE VARCHAR(3) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'import_file.html?form=per_salaryhis_new_form.php' ";
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
								  VALUES (1, 'TH', $MAX_ID, 19, 'P1119 นำเข้าข้อมูลการปรับบัญชีเงินเดือนใหม่', 'S', 'W', 'import_file.html?form=per_salaryhis_new_form.php', 0, 35, 251, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 19, 'P1119 นำเข้าข้อมูลการปรับบัญชีเงินเดือนใหม่', 'S', 'W', 'import_file.html?form=per_salaryhis_new_form.php', 0, 35, 251, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET FLAG_SHOW = 'H' 
							WHERE LINKTO_WEB in ('rpt_R005001.html', 'rpt_R005002.html', 'rpt_R005003.html', 'rpt_R005004.html', 'rpt_R005006.html', 'rpt_R005007.html') ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO PER_COMGROUP (COM_GROUP, CG_NAME, CG_ACTIVE, UPDATE_USER, UPDATE_DATE, CG_SEQ_NO)
							  VALUES ('528', 'คำสั่งตัดโอนตำแหน่งและอัตราเงินเดือน', 1, $SESS_USERID, '$UPDATE_DATE', 28) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO, COM_ACTIVE)
							VALUES ('5280', 'คส. 28', 'คำสั่งตัดโอนตำแหน่งและอัตราเงินเดือน', '528', 108, 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'data_move_pos_salary_comdtl.html' ";
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
								  VALUES (1, 'TH', $MAX_ID, 13, 'P0313 บัญชีแนบท้ายคำสั่งตัดโอนตำแหน่งและอัตราเงินเดือน', 'S', 'W', 
								  'data_move_pos_salary_comdtl.html', 0, 35, 243, $CREATE_DATE, $UPDATE_BY, 
								  $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 		
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 13, 'P0313 บัญชีแนบท้ายคำสั่งตัดโอนตำแหน่งและอัตราเงินเดือน', 'S', 'W', 
								  'data_move_pos_salary_comdtl.html', 0, 35, 243, $CREATE_DATE, $UPDATE_BY, 
								  $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, UPDATE_DATE, MOV_SUB_TYPE)
							  VALUES ('128', 'ประเภทตัดโอนตำแหน่งและอัตราเงินเดือน', 1, 1, $SESS_USERID, '$UPDATE_DATE', 2) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, UPDATE_DATE, MOV_SUB_TYPE)
							  VALUES ('12810', 'ตัดโอนตำแหน่งและอัตราเงินเดือน', 1, 1, $SESS_USERID, '$UPDATE_DATE', 2) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " SELECT min(MENU_ID) as MENU_ID, MENU_LABEL, LINKTO_WEB 
								FROM BACKOFFICE_MENU_BAR_LV1 
								WHERE LANGCODE='TH' 
								GROUP BY MENU_LABEL, LINKTO_WEB
								ORDER BY MENU_LABEL, LINKTO_WEB ";
			$db->send_cmd($cmd);
			//$db->show_error();
			while($data = $db->get_array()){
				$MENU_ID = trim($data[MENU_ID]);
				$MENU_LABEL = trim($data[MENU_LABEL]);
				$LINKTO_WEB = trim($data[LINKTO_WEB]);
				$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV1 
								WHERE MENU_ID!=$MENU_ID AND MENU_LABEL='$MENU_LABEL' AND LINKTO_WEB='$LINKTO_WEB' ";
				$db1->send_cmd($cmd);
				//$db1->show_error();
			} // end while

			$cmd = " SELECT min(MENU_ID) as MENU_ID, MENU_LABEL, LINKTO_WEB 
								FROM BACKOFFICE_MENU_BAR_LV2 
								WHERE LANGCODE='TH' 
								GROUP BY MENU_LABEL, LINKTO_WEB
								ORDER BY MENU_LABEL, LINKTO_WEB ";
			$db->send_cmd($cmd);
			//$db->show_error();
			while($data = $db->get_array()){
				$MENU_ID = trim($data[MENU_ID]);
				$MENU_LABEL = trim($data[MENU_LABEL]);
				$LINKTO_WEB = trim($data[LINKTO_WEB]);
				$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV2 
								WHERE MENU_ID!=$MENU_ID AND MENU_LABEL='$MENU_LABEL' AND LINKTO_WEB='$LINKTO_WEB' ";
				$db1->send_cmd($cmd);
				//$db1->show_error();
			} // end while

			$cmd = " CREATE INDEX IDX_SYSTEM_CONFIG ON SYSTEM_CONFIG (CONFIG_NAME) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'data_transfer_req_comdtl.html' 
							  WHERE LINKTO_WEB = 'data_all_comdtl_tab.html?from_parent=transfer_req_comdtl' OR LINKTO_WEB = 'data_transfer_req_comdtl_tab.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'data_move_req_comdtl.html' 
							  WHERE LINKTO_WEB = 'data_all_comdtl_tab.html?from_parent=move_req_comdtl' OR LINKTO_WEB = 'data_move_req_comdtl_tab.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'data_promote_e_p_comdtl.html' 
							  WHERE LINKTO_WEB = 'data_all_comdtl_tab.html?from_parent=promote_e_p_comdtl' OR LINKTO_WEB = 'data_promote_e_p_comdtl_tab.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'data_move_comdtl_N.html' 
							  WHERE LINKTO_WEB = 'data_all_comdtl_tab.html?from_parent=move_comdtl_N' OR LINKTO_WEB = 'data_move_comdtl_N_tab.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'data_salpromote_comdtl.html' 
							  WHERE LINKTO_WEB = 'data_all_comdtl_tab.html?from_parent=salpromote_comdtl' OR LINKTO_WEB = 'data_salpromote_comdtl_tab.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'data_retire_comdtl.html' 
							  WHERE LINKTO_WEB = 'data_all_comdtl_tab.html?from_parent=retire_comdtl' OR LINKTO_WEB = 'data_retire_comdtl_tab.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'data_acting_comdtl.html' 
							  WHERE LINKTO_WEB = 'data_all_comdtl_tab.html?from_parent=acting_comdtl' OR LINKTO_WEB = 'data_acting_comdtl_tab.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'data_assign_comdtl.html' 
							  WHERE LINKTO_WEB = 'data_all_comdtl_tab.html?from_parent=assign_comdtl' OR LINKTO_WEB = 'data_assign_comdtl_tab.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'command_improve_position.html' WHERE LINKTO_WEB = 'command_improve_position_tab.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'data_salreceive_comdtl.html' 
							  WHERE LINKTO_WEB = 'data_all_comdtl_tab.html?from_parent=salreceive_comdtl' OR LINKTO_WEB = 'data_salreceive_comdtl_tab.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'data_course.html' WHERE LINKTO_WEB = 'data_coursedtl_tab.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'data_help_comdtl.html' 
							  WHERE LINKTO_WEB = 'data_all_comdtl_tab.html?from_parent=help_comdtl' OR LINKTO_WEB = 'data_help_comdtl_tab.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'data_map_emp_comdtl.html' 
							  WHERE LINKTO_WEB = 'data_all_comdtl_tab.html?from_parent=map_emp_comdtl' OR LINKTO_WEB = 'data_map_emp_comdtl_tab.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV1 SET LINKTO_WEB = 'data_compensation_salpromote_comdtl_empser.html' 
							  WHERE LINKTO_WEB = 'data_all_comdtl_tab.html?from_parent=compensation_salpromote_comdtl_empser' OR LINKTO_WEB = 'data_compensation_salpromote_comdtl_empser_tab.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV1 SET LINKTO_WEB = 'data_compensation_salpromote_comdtl.html' 
							  WHERE LINKTO_WEB = 'data_all_comdtl_tab.html?from_parent=compensation_salpromote_comdtl' OR LINKTO_WEB = 'data_compensation_salpromote_comdtl_tab.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'data_move_salary.html' 
							  WHERE LINKTO_WEB = 'data_all_comdtl_tab.html?from_parent=move_salary' OR LINKTO_WEB = 'data_move_salary_tab.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'data_salpromote_new_comdtl.html' 
							  WHERE LINKTO_WEB = 'data_all_comdtl_tab.html?from_parent=salpromote_new_comdtl' OR LINKTO_WEB = 'data_salpromote_new_comdtl_tab.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'data_transfer_req_transfer.html' 
							  WHERE LINKTO_WEB = 'data_transfer_req_trans_serve_tab.html?from_parent=transfer' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'data_transfer_req_serveback.html' 
							  WHERE LINKTO_WEB = 'data_transfer_req_trans_serve_tab.html?from_parent=serve' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'data_move_req.html' WHERE LINKTO_WEB = 'data_move_req_tab.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'data_promote_e_p_inquire.html' WHERE LINKTO_WEB = 'data_promote_e_p_inquire_tab.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'personal_inquiry.html' WHERE LINKTO_WEB = 'personal_inquiry_tab.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'data_salpromote.html' WHERE LINKTO_WEB = 'data_salpromote_tab.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'convert_pic_new.html' WHERE LINKTO_WEB = 'convert_pic.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'data_scholar.html' WHERE LINKTO_WEB = 'data_scholar_tab.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'data_map_type_new_comdtl.html' 
							  WHERE LINKTO_WEB = 'data_all_comdtl_tab.html?from_parent=map_type_new_comdtl' OR LINKTO_WEB = 'data_map_type_new_comdtl_tab.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV1 SET LINKTO_WEB = 'rpt_R010.html?report=rpt_R010005' WHERE LINKTO_WEB = 'rpt_R010005.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_POSTINGHIS ALTER POST_POSITION MEMO ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POSTINGHIS MODIFY POST_POSITION VARCHAR2(1000) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_POSTINGHIS ALTER POST_POSITION TEXT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE GMIS_GPIS ALTER tempLevel VARCHAR(100) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE GMIS_GPIS MODIFY tempLevel VARCHAR2(100) ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE GMIS_GPIS MODIFY tempLevel VARCHAR(100) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE GMIS_GPIS_FLOW_IN ALTER tempLevel VARCHAR(100) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE GMIS_GPIS_FLOW_IN MODIFY tempLevel VARCHAR2(100) ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE GMIS_GPIS_FLOW_IN MODIFY tempLevel VARCHAR(100) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE GMIS_GPIS_FLOW_OUT ALTER tempLevel VARCHAR(100) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE GMIS_GPIS_FLOW_OUT MODIFY tempLevel VARCHAR2(100) ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE GMIS_GPIS_FLOW_OUT MODIFY tempLevel VARCHAR(100) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'personal_master.html?SEARCHHIS=personal_positionhis' 
							  WHERE LINKTO_WEB = 'personal_positionhis.html' or LINKTO_WEB like 'personal_master.html?SEARCHHIS=personal_positionhis%' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'personal_master.html?SEARCHHIS=personal_salary_sum' 
							  WHERE LINKTO_WEB = 'personal_salaryhis.html' or LINKTO_WEB like 'personal_master.html?SEARCHHIS=personal_salaryhis%' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'personal_master.html?SEARCHHIS=personal_extrahis' 
							  WHERE LINKTO_WEB = 'personal_extrahis.html' or LINKTO_WEB like 'personal_master.html?SEARCHHIS=personal_extrahis%' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'personal_master.html?SEARCHHIS=personal_educate' 
							  WHERE LINKTO_WEB = 'personal_educate.html' or LINKTO_WEB like 'personal_master.html?SEARCHHIS=personal_educate%' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'personal_master.html?SEARCHHIS=personal_training' 
							  WHERE LINKTO_WEB = 'personal_training.html' or LINKTO_WEB like 'personal_master.html?SEARCHHIS=personal_training%' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'personal_master.html?SEARCHHIS=personal_ability' 
							  WHERE LINKTO_WEB = 'personal_ability.html' or LINKTO_WEB like 'personal_master.html?SEARCHHIS=personal_ability%' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'personal_master.html?SEARCHHIS=personal_special_skill' 
							  WHERE LINKTO_WEB = 'personal_special_skill.html' or LINKTO_WEB like 'personal_master.html?SEARCHHIS=personal_special_skill%' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'personal_master.html?SEARCHHIS=personal_heir' 
							  WHERE LINKTO_WEB = 'personal_heir.html' or LINKTO_WEB like 'personal_master.html?SEARCHHIS=personal_heir%' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'personal_master.html?SEARCHHIS=personal_absenthis' 
							  WHERE LINKTO_WEB = 'personal_absenthis.html' or LINKTO_WEB like 'personal_master.html?SEARCHHIS=personal_absenthis%' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'personal_master.html?SEARCHHIS=personal_punishment' 
							  WHERE LINKTO_WEB = 'personal_punishment.html' or LINKTO_WEB like 'personal_master.html?SEARCHHIS=personal_punishment%' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'personal_master.html?SEARCHHIS=personal_servicehis' 
							  WHERE LINKTO_WEB = 'personal_servicehis.html' or LINKTO_WEB like 'personal_master.html?SEARCHHIS=personal_servicehis%' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'personal_master.html?SEARCHHIS=personal_rewardhis' 
							  WHERE LINKTO_WEB = 'personal_rewardhis.html' or LINKTO_WEB like 'personal_master.html?SEARCHHIS=personal_rewardhis%' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'personal_master.html?SEARCHHIS=personal_marrhis' 
							  WHERE LINKTO_WEB = 'personal_marrhis.html' or LINKTO_WEB like 'personal_master.html?SEARCHHIS=personal_marrhis%' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'personal_master.html?SEARCHHIS=personal_namehis' 
							  WHERE LINKTO_WEB = 'personal_namehis.html' or LINKTO_WEB like 'personal_master.html?SEARCHHIS=personal_namehis%' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'personal_master.html?SEARCHHIS=personal_decoratehis' 
							  WHERE LINKTO_WEB = 'personal_decoratehis.html' or LINKTO_WEB like 'personal_master.html?SEARCHHIS=personal_decoratehis%' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'personal_master.html?SEARCHHIS=personal_timehis' 
							  WHERE LINKTO_WEB = 'personal_timehis.html' or LINKTO_WEB like 'personal_master.html?SEARCHHIS=personal_timehis%' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'personal_master.html?SEARCHHIS=personal_extra_incomehis' 
							  WHERE LINKTO_WEB = 'personal_extra_incomehis.html' or LINKTO_WEB like 'personal_master.html?SEARCHHIS=personal_extra_incomehis%' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'personal_master.html?SEARCHHIS=personal_work_cyclehis' 
							  WHERE LINKTO_WEB = 'personal_work_cyclehis.html' or LINKTO_WEB like 'personal_master.html?SEARCHHIS=personal_work_cyclehis%' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'personal_master.html?SEARCHHIS=personal_work_time' 
							  WHERE LINKTO_WEB = 'personal_work_time.html' or LINKTO_WEB like 'personal_master.html?SEARCHHIS=personal_work_time%' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			add_field("PER_ORG", "ORG_ID_ASS","INTEGER", "10", "NULL");
			add_field("PER_ORG_ASS", "ORG_ID_ASS","INTEGER", "10", "NULL");
			if ($CTRL_TYPE==4) {
				$cmd = " SELECT a.ORG_ID, a.ORG_NAME, b.ORG_NAME as ORG_NAME_REF, c.ORG_NAME as ORG_NAME_REF1, 
													d.ORG_NAME as ORG_NAME_REF2, e.ORG_NAME as ORG_NAME_REF3 
								  FROM PER_ORG a, PER_ORG b, PER_ORG c, PER_ORG d, PER_ORG e
								  WHERE a.ORG_ID_REF = b.ORG_ID AND b.ORG_ID_REF = c.ORG_ID AND c.ORG_ID_REF = d.ORG_ID AND d.ORG_ID_REF = e.ORG_ID(+) AND 
													a.DEPARTMENT_ID = $SESS_DEPARTMENT_ID AND a.ORG_ID_ASS IS NULL AND a.ORG_ACTIVE = 1
								  ORDER BY a.ORG_NAME, b.ORG_NAME, c.ORG_NAME, d.ORG_NAME, e.ORG_NAME ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
				while($data = $db_dpis->get_array()){
					$ORG_ID = $data[ORG_ID];
					$ORG_NAME = trim($data[ORG_NAME]);
					$ORG_NAME_REF = trim($data[ORG_NAME_REF]);
					$ORG_NAME_REF1 = trim($data[ORG_NAME_REF1]);
					$ORG_NAME_REF2 = trim($data[ORG_NAME_REF2]);
					$ORG_NAME_REF3 = trim($data[ORG_NAME_REF3]);

					if ($ORG_NAME_REF==$SESS_DEPARTMENT_NAME && (substr($ORG_NAME,0,10)=="แขวงการทาง" || 
						substr($ORG_NAME,0,10)=="ศูนย์สร้าง" || substr($ORG_NAME,0,16)=="สำนักงานบำรุงทาง")) {	
						$cmd = " select  a.ORG_ID from PER_ORG_ASS a 
										where a.ORG_NAME='$ORG_NAME' and a.ORG_ACTIVE = 1 ";
						$count=$db_dpis1->send_cmd($cmd);
						if(!$count) { 
							//echo "<hr>$cmd<br>";
						} else {
							$data1 = $db_dpis1->get_array();
							$ORG_ID_ASS = $data1[ORG_ID];
							$cmd = " update PER_ORG set ORG_ID_ASS = $ORG_ID_ASS where ORG_ID = $ORG_ID ";
							$db_dpis1->send_cmd($cmd);
							//$db_dpis1->show_error();
						}
					} elseif ($ORG_NAME_REF==$SESS_DEPARTMENT_NAME || ($ORG_NAME_REF1==$SESS_DEPARTMENT_NAME && 
						(substr($ORG_NAME_REF,0,10)=="แขวงการทาง" || substr($ORG_NAME_REF,0,10)=="ศูนย์สร้าง" || substr($ORG_NAME_REF,0,16)=="สำนักงานบำรุงทาง"))) {	
						$cmd = " select  a.ORG_ID from PER_ORG_ASS a, PER_ORG_ASS b 
										where a.ORG_ID_REF = b.ORG_ID AND a.ORG_NAME='$ORG_NAME' and a.ORG_ACTIVE = 1 and b.ORG_NAME='$ORG_NAME_REF' ";
						$count=$db_dpis1->send_cmd($cmd);
						if(!$count) { 
							//echo "<hr>$cmd<br>";
						} else {
							$data1 = $db_dpis1->get_array();
							$ORG_ID_ASS = $data1[ORG_ID];
							$cmd = " update PER_ORG set ORG_ID_ASS = $ORG_ID_ASS where ORG_ID = $ORG_ID ";
							$db_dpis1->send_cmd($cmd);
							//$db_dpis1->show_error();
						}
					} elseif ($ORG_NAME_REF1==$SESS_DEPARTMENT_NAME || ($ORG_NAME_REF2==$SESS_DEPARTMENT_NAME && 
						(substr($ORG_NAME_REF1,0,10)=="แขวงการทาง" || substr($ORG_NAME_REF1,0,10)=="ศูนย์สร้าง" || substr($ORG_NAME_REF1,0,16)=="สำนักงานบำรุงทาง"))) {	
						$cmd = " select  a.ORG_ID from PER_ORG_ASS a, PER_ORG_ASS b, PER_ORG_ASS c 
										where a.ORG_ID_REF = b.ORG_ID AND b.ORG_ID_REF = c.ORG_ID AND a.ORG_NAME='$ORG_NAME' and a.ORG_ACTIVE = 1 and 
													b.ORG_NAME='$ORG_NAME_REF' and c.ORG_NAME='$ORG_NAME_REF1' ";
						$count=$db_dpis1->send_cmd($cmd);
						if(!$count) { 
							//echo "<hr>$cmd<br>";
						} else {
							$data1 = $db_dpis1->get_array();
							$ORG_ID_ASS = $data1[ORG_ID];
							$cmd = " update PER_ORG set ORG_ID_ASS = $ORG_ID_ASS where ORG_ID = $ORG_ID ";
							$db_dpis1->send_cmd($cmd);
							//$db_dpis1->show_error();
						}
					} elseif ($ORG_NAME_REF2==$SESS_DEPARTMENT_NAME || ($ORG_NAME_REF3==$SESS_DEPARTMENT_NAME && 
						(substr($ORG_NAME_REF2,0,10)=="แขวงการทาง" || substr($ORG_NAME_REF2,0,10)=="ศูนย์สร้าง" || substr($ORG_NAME_REF2,0,16)=="สำนักงานบำรุงทาง"))) {	
						$cmd = " select  a.ORG_ID from PER_ORG_ASS a, PER_ORG_ASS b, PER_ORG_ASS c, PER_ORG_ASS d 
										where a.ORG_ID_REF = b.ORG_ID AND b.ORG_ID_REF = c.ORG_ID AND c.ORG_ID_REF = d.ORG_ID AND a.ORG_NAME='$ORG_NAME' and a.ORG_ACTIVE = 1 and 
													b.ORG_NAME='$ORG_NAME_REF' and c.ORG_NAME='$ORG_NAME_REF1' and d.ORG_NAME='$ORG_NAME_REF2' ";
						$count=$db_dpis1->send_cmd($cmd);
						if(!$count) { 
							//echo "<hr>$cmd<br>";
						} else {
							$data1 = $db_dpis1->get_array();
							$ORG_ID_ASS = $data1[ORG_ID];
							$cmd = " update PER_ORG set ORG_ID_ASS = $ORG_ID_ASS where ORG_ID = $ORG_ID ";
							$db_dpis1->send_cmd($cmd);
							//$db_dpis1->show_error();
						}
					} else {	
						$cmd = " select  a.ORG_ID from PER_ORG_ASS a, PER_ORG_ASS b, PER_ORG_ASS c, PER_ORG_ASS d, PER_ORG_ASS e 
										where a.ORG_ID_REF = b.ORG_ID AND b.ORG_ID_REF = c.ORG_ID AND c.ORG_ID_REF = d.ORG_ID AND d.ORG_ID_REF = e.ORG_ID AND 
													a.ORG_NAME='$ORG_NAME' and a.ORG_ACTIVE = 1 and b.ORG_NAME='$ORG_NAME_REF' and c.ORG_NAME='$ORG_NAME_REF1' and d.ORG_NAME='$ORG_NAME_REF2' and e.ORG_NAME='$ORG_NAME_REF3' ";
						$count=$db_dpis1->send_cmd($cmd);
						if(!$count) { 
							//echo "<hr>$cmd<br>";
						} else {
							$data1 = $db_dpis1->get_array();
							$ORG_ID_ASS = $data1[ORG_ID];
							$cmd = " update PER_ORG set ORG_ID_ASS = $ORG_ID_ASS where ORG_ID = $ORG_ID ";
							$db_dpis1->send_cmd($cmd);
							//$db_dpis1->show_error();
						}
					}
				} // end while						
			} // end if						

			$cmd = " UPDATE PER_PERSONAL SET ORG_ID_1 = NULL WHERE ORG_ID = ORG_ID_1 AND ORG_ID_1 IS NOT NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
	
			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_SLIP ALTER BANK_CODE VARCHAR(10) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SLIP MODIFY BANK_CODE VARCHAR2(10) ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_SLIP MODIFY BANK_CODE VARCHAR(10) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE SET PL_NAME = 'นักจิตวิทยาคลินิก' WHERE PL_NAME = 'นักจิตวิทยาคลินิค' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_PERSONAL", "PER_LEVELDATE","VARCHAR", "19", "NULL");
			add_field("PER_PERSONAL", "PER_POSTDATE","VARCHAR", "19", "NULL");

			$cmd = " UPDATE PER_ABSENTHIS SET ABS_ENDPERIOD = ABS_STARTPERIOD 
							WHERE ABS_DAY = 0.5 AND ABS_STARTPERIOD != ABS_ENDPERIOD AND ABS_ENDPERIOD = 3 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_ABSENTHIS SET ABS_STARTPERIOD = ABS_ENDPERIOD 
							WHERE ABS_DAY = 0.5 AND ABS_STARTPERIOD != ABS_ENDPERIOD AND ABS_STARTPERIOD = 3 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			if ($SESS_DEPARTMENT_NAME!="สำนักงาน ก.พ." && ($SPKG1[$SESS_DEPARTMENT_ID]=="Y" || $RTF_FLAG==1)) {
				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV1 SET LINKTO_WEB = 'kpi_kpi_form_new.html' 
								  WHERE LINKTO_WEB = 'kpi_kpi_form.html' ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'personal_master_new.html' 
								  WHERE LINKTO_WEB = 'personal_master.html' ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " UPDATE PER_POSITION SET POS_NO = '99999' WHERE POS_NO = 'ขวัญจิตร' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " UPDATE PER_POSITION SET POS_NO = '99999' WHERE POS_NO = '1408 (ฉ)' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			if ($RTF_FLAG==1) {
				$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_R003014.html' ";
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
									  VALUES (1, 'TH', $MAX_ID, 14, 'R0314 จำนวนข้าราชการที่บรรจุใหม่ บรรจุกลับ รับโอน และการสูญเสียในกรณีต่าง ๆ', 'S', 'W', 
									  'rpt_R003014.html', 0, 36, 235, $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();

					$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
									  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
									  CREATE_BY, UPDATE_DATE, UPDATE_BY)
									  VALUES (1, 'EN', $MAX_ID, 14, 'R0314 จำนวนข้าราชการที่บรรจุใหม่ บรรจุกลับ รับโอน และการสูญเสียในกรณีต่าง ๆ', 'S', 'W', 
									  'rpt_R003014.html', 0, 36, 235, $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();
				}
			}

			$level = array("ปฏิบัติงาน", "ชำนาญงาน", "อาวุโส", "ทักษะพิเศษ", "ปฏิบัติการ", "ชำนาญการพิเศษ", "ชำนาญการ", "เชี่ยวชาญ","ทรงคุณวุฒิ", "ระดับต้น", "ระดับสูง", "ต้น", "สูง");
			for ( $i=0; $i<count($level); $i++ ) { 
				$cmd = " UPDATE PER_KPI_FORM SET PL_NAME = REPLACE(PL_NAME,'$level[$i]') WHERE PL_NAME like '%$level[$i]' ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_KPI_FORM SET REVIEW_PL_NAME = REPLACE(PL_NAME,'$level[$i]') WHERE REVIEW_PL_NAME like '%$level[$i]' ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_KPI_FORM SET REVIEW_PL_NAME0 = REPLACE(PL_NAME,'$level[$i]') WHERE REVIEW_PL_NAME0 like '%$level[$i]' ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_KPI_FORM SET REVIEW_PL_NAME1 = REPLACE(PL_NAME,'$level[$i]') WHERE REVIEW_PL_NAME1 like '%$level[$i]' ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_KPI_FORM SET REVIEW_PL_NAME2 = REPLACE(PL_NAME,'$level[$i]') WHERE REVIEW_PL_NAME2 like '%$level[$i]' ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			}
		
			$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, UPDATE_DATE, MOV_SUB_TYPE)
							  VALUES ('21440', 'เงินค่าตอบแทนพิเศษร้อยละ 6', 2, 1, $SESS_USERID, '$UPDATE_DATE', 4) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, UPDATE_DATE, MOV_SUB_TYPE)
							  VALUES ('21450', 'เงินค่าตอบแทนพิเศษร้อยละ 8', 2, 1, $SESS_USERID, '$UPDATE_DATE', 4) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, UPDATE_DATE, MOV_SUB_TYPE)
							  VALUES ('10850', 'แต่งตั้งให้ดำรงตำแหน่ง', 1, 1, $SESS_USERID, '$UPDATE_DATE', 99) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, 
							  UPDATE_DATE, MOV_SUB_TYPE)
							  VALUES ('240', 'เลื่อนขั้นค่าจ้าง', 2, 1, $SESS_USERID, '$UPDATE_DATE', 4) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, 
							  UPDATE_DATE, MOV_SUB_TYPE)
							  VALUES ('21740', 'ตัดค่าจ้าง 5%', 2, 1, $SESS_USERID, '$UPDATE_DATE', 4) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, 
							  UPDATE_DATE, MOV_SUB_TYPE)
							  VALUES ('21750', 'ตัดค่าจ้าง 10%', 2, 1, $SESS_USERID, '$UPDATE_DATE', 4) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, 
							  UPDATE_DATE, MOV_SUB_TYPE)
							  VALUES ('21760', 'ตัดค่าจ้างมากกว่า 10%', 2, 1, $SESS_USERID, '$UPDATE_DATE', 4) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, 
							  UPDATE_DATE, MOV_SUB_TYPE)
							  VALUES ('250', 'เงินเพิ่มพิเศษ', 2, 1, $SESS_USERID, '$UPDATE_DATE', 4) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, 
							  UPDATE_DATE, MOV_SUB_TYPE)
							  VALUES ('25010', 'เงินเพิ่มการครองชีพชั่วคราว', 2, 1, $SESS_USERID, '$UPDATE_DATE', 4) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, 
							  UPDATE_DATE, MOV_SUB_TYPE)
							  VALUES ('25020', 'เงินเพิ่มพิเศษสำหรับการสู้รบ', 2, 1, $SESS_USERID, '$UPDATE_DATE', 4) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, 
							  UPDATE_DATE, MOV_SUB_TYPE)
							  VALUES ('25030', 'เงินประจำตำแหน่ง', 2, 1, $SESS_USERID, '$UPDATE_DATE', 4) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, 
							  UPDATE_DATE, MOV_SUB_TYPE)
							  VALUES ('12820', 'ตัดโอนตำแหน่งและอัตราค่าจ้าง', 1, 1, $SESS_USERID, '$UPDATE_DATE', 2) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, 
							  UPDATE_DATE, MOV_SUB_TYPE)
							  VALUES ('11270', 'ลาป่วย ลากิจส่วนตัว ลาคลอด ลาอุปสมบท', 1, 1, $SESS_USERID, '$UPDATE_DATE', 99) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_COMDTL ALTER CMD_ACCOUNT VARCHAR(255) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COMDTL MODIFY CMD_ACCOUNT VARCHAR2(255) ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_COMDTL MODIFY CMD_ACCOUNT VARCHAR(255) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " select AS_ID, AS_YEAR, AS_CYCLE from PER_ABSENTSUM 
							where AS_YEAR=substr(END_DATE,1,4) or to_number(AS_YEAR)-1=substr(START_DATE,1,4) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			while($data = $db_dpis->get_array()){
				$AS_ID = $data[AS_ID];
				$AS_YEAR = trim($data[AS_YEAR]);
				$AS_CYCLE = $data[AS_CYCLE];
				$START_DATE_1 = ($AS_YEAR-1-543) . "-10-01";
				$END_DATE_1 = ($AS_YEAR-543) . "-03-31";
				$START_DATE_2 = ($AS_YEAR-543) . "-04-01";
				$END_DATE_2 = ($AS_YEAR-543) . "-09-30"; 

				if ($AS_CYCLE==1) {	
					$cmd = " update PER_ABSENTSUM set START_DATE = '$START_DATE_1', END_DATE = '$END_DATE_1' where AS_ID = $AS_ID ";
				} elseif ($AS_CYCLE==2) {	
					$cmd = " update PER_ABSENTSUM set START_DATE = '$START_DATE_2', END_DATE = '$END_DATE_2' where AS_ID = $AS_ID ";
				}
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
			} // end while						
			/*release 5.1.0.0 begin*/
			// UPDATE 2015 / 11 / 04 Alter for GMIS export
		   if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE GMIS_GPIS ALTER tempClName VARCHAR(100) ";
		   elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE GMIS_GPIS MODIFY tempClName VARCHAR2(100) ";
		   elseif($DPISDB=="mysql") 
			$cmd = " ALTER TABLE GMIS_GPIS MODIFY tempClName VARCHAR(100) ";
		   $db_dpis->send_cmd($cmd);
		   //$db_dpis->show_error();
		   if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE GMIS_GPIS_FLOW_IN ALTER tempClName VARCHAR(100) ";
		   elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE GMIS_GPIS_FLOW_IN MODIFY tempClName VARCHAR2(100) ";
		   elseif($DPISDB=="mysql") 
			$cmd = " ALTER TABLE GMIS_GPIS_FLOW_IN MODIFY tempClName VARCHAR(100) ";
		   $db_dpis->send_cmd($cmd);
		   //$db_dpis->show_error();
		   if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE GMIS_GPIS_FLOW_OUT ALTER tempClName VARCHAR(100) ";
		   elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE GMIS_GPIS_FLOW_OUT MODIFY tempClName VARCHAR2(100) ";
		   elseif($DPISDB=="mysql") 
			$cmd = " ALTER TABLE GMIS_GPIS_FLOW_OUT MODIFY tempClName VARCHAR(100) ";
		   $db_dpis->send_cmd($cmd);
		   //$db_dpis->show_error();
		   if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE GPIS1 ALTER tempClName VARCHAR(100) ";
		   elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE GPIS1 MODIFY tempClName VARCHAR2(100) ";
		   elseif($DPISDB=="mysql") 
			$cmd = " ALTER TABLE GPIS1 MODIFY tempClName VARCHAR(100) ";
		   $db_dpis->send_cmd($cmd);
		   //$db_dpis->show_error();
		   if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE GPIS1_FLOW_IN ALTER tempClName VARCHAR(100) ";
		   elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE GPIS1_FLOW_IN MODIFY tempClName VARCHAR2(100) ";
		   elseif($DPISDB=="mysql") 
			$cmd = " ALTER TABLE GPIS1_FLOW_IN MODIFY tempClName VARCHAR(100) ";
		   $db_dpis->send_cmd($cmd);
		   //$db_dpis->show_error();
		   if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE GPIS1_FLOW_OUT ALTER tempClName VARCHAR(100) ";
		   elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE GPIS1_FLOW_OUT MODIFY tempClName VARCHAR2(100) ";
		   elseif($DPISDB=="mysql") 
			$cmd = " ALTER TABLE GPIS1_FLOW_OUT MODIFY tempClName VARCHAR(100) ";
		   $db_dpis->send_cmd($cmd);
		   //$db_dpis->show_error();
		   if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE GMIS_GPIS ALTER tempEducationLevel VARCHAR(100) ";
		   elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE GMIS_GPIS MODIFY tempEducationLevel VARCHAR2(100) ";
		   elseif($DPISDB=="mysql") 
			$cmd = " ALTER TABLE GMIS_GPIS MODIFY tempEducationLevel VARCHAR(100) ";
		   $db_dpis->send_cmd($cmd);
		   //$db_dpis->show_error();
		   if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE GMIS_GPIS_FLOW_IN ALTER tempEducationLevel VARCHAR(100) ";
		   elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE GMIS_GPIS_FLOW_IN MODIFY tempEducationLevel VARCHAR2(100) ";
		   elseif($DPISDB=="mysql") 
			$cmd = " ALTER TABLE GMIS_GPIS_FLOW_IN MODIFY tempEducationLevel VARCHAR(100) ";
		   $db_dpis->send_cmd($cmd);
		   //$db_dpis->show_error();
		   if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE GMIS_GPIS_FLOW_OUT ALTER tempEducationLevel VARCHAR(100) ";
		   elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE GMIS_GPIS_FLOW_OUT MODIFY tempEducationLevel VARCHAR2(100) ";
		   elseif($DPISDB=="mysql") 
			$cmd = " ALTER TABLE GMIS_GPIS_FLOW_OUT MODIFY tempEducationLevel VARCHAR(100) ";
		   $db_dpis->send_cmd($cmd);
		   //$db_dpis->show_error();
		   if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE GPIS1 ALTER tempEducationLevel VARCHAR(100) ";
		   elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE GPIS1 MODIFY tempEducationLevel VARCHAR2(100) ";
		   elseif($DPISDB=="mysql") 
			$cmd = " ALTER TABLE GPIS1 MODIFY tempEducationLevel VARCHAR(100) ";
		   $db_dpis->send_cmd($cmd);
		   //$db_dpis->show_error();
		   if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE GPIS1_FLOW_IN ALTER tempEducationLevel VARCHAR(100) ";
		   elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE GPIS1_FLOW_IN MODIFY tempEducationLevel VARCHAR2(100) ";
		   elseif($DPISDB=="mysql") 
			$cmd = " ALTER TABLE GPIS1_FLOW_IN MODIFY tempEducationLevel VARCHAR(100) ";
		   $db_dpis->send_cmd($cmd);
		   //$db_dpis->show_error();
		   if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE GPIS1_FLOW_OUT ALTER tempEducationLevel VARCHAR(100) ";
		   elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE GPIS1_FLOW_OUT MODIFY tempEducationLevel VARCHAR2(100) ";
		   elseif($DPISDB=="mysql") 
			$cmd = " ALTER TABLE GPIS1_FLOW_OUT MODIFY tempEducationLevel VARCHAR(100) ";
		   $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();


		   if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE GMIS_GPIS ALTER TEMPRESULT1 VARCHAR(100) ";
		   elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE GMIS_GPIS MODIFY TEMPRESULT1 VARCHAR2(100) ";
		   elseif($DPISDB=="mysql") 
			$cmd = " ALTER TABLE GMIS_GPIS MODIFY TEMPRESULT1 VARCHAR(100) ";
		   $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

		   if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE GMIS_GPIS_FLOW_IN ALTER TEMPRESULT1 VARCHAR(100) ";
		   elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE GMIS_GPIS_FLOW_IN MODIFY TEMPRESULT1 VARCHAR2(100) ";
		   elseif($DPISDB=="mysql") 
			$cmd = " ALTER TABLE GMIS_GPIS_FLOW_IN MODIFY TEMPRESULT1 VARCHAR(100) ";
		   $db_dpis->send_cmd($cmd);
		   //$db_dpis->show_error();

			if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE GMIS_GPIS_FLOW_OUT ALTER TEMPRESULT1 VARCHAR(100) ";
		   elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE GMIS_GPIS_FLOW_OUT MODIFY TEMPRESULT1 VARCHAR2(100) ";
		   elseif($DPISDB=="mysql") 
			$cmd = " ALTER TABLE GMIS_GPIS_FLOW_OUT MODIFY TEMPRESULT1 VARCHAR(100) ";
		   $db_dpis->send_cmd($cmd);
		   //$db_dpis->show_error();

		   if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE GMIS_GPIS ALTER TEMPRESULT2 VARCHAR(100) ";
		   elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE GMIS_GPIS MODIFY TEMPRESULT2 VARCHAR2(100) ";
		   elseif($DPISDB=="mysql") 
			$cmd = " ALTER TABLE GMIS_GPIS MODIFY TEMPRESULT2 VARCHAR(100) ";
		   $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

		   if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE GMIS_GPIS_FLOW_IN ALTER TEMPRESULT2 VARCHAR(100) ";
		   elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE GMIS_GPIS_FLOW_IN MODIFY TEMPRESULT2 VARCHAR2(100) ";
		   elseif($DPISDB=="mysql") 
			$cmd = " ALTER TABLE GMIS_GPIS_FLOW_IN MODIFY TEMPRESULT2 VARCHAR(100) ";
		   $db_dpis->send_cmd($cmd);
		   //$db_dpis->show_error();

			if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE GMIS_GPIS_FLOW_OUT ALTER TEMPRESULT2 VARCHAR(100) ";
		   elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE GMIS_GPIS_FLOW_OUT MODIFY TEMPRESULT2 VARCHAR2(100) ";
		   elseif($DPISDB=="mysql") 
			$cmd = " ALTER TABLE GMIS_GPIS_FLOW_OUT MODIFY TEMPRESULT2 VARCHAR(100) ";
		   $db_dpis->send_cmd($cmd);
		   //$db_dpis->show_error();
		   
		   $cmd = " update PER_CONTROL set CTRL_ALTER = 15 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
?>