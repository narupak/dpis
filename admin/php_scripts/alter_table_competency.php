<?
/*		$cmd = " DROP TABLE PER_LINE_COMPETENCE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
*/
		$cmd = " SELECT COUNT(PL_CODE) AS COUNT_DATA FROM PER_LINE_COMPETENCE ";
		$count_data = $db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		if (!$count_data) {
			if($DPISDB=="odbc") 
				$cmd = " CREATE TABLE PER_LINE_COMPETENCE(
				PL_CODE VARCHAR(10) NOT NULL,	
				ORG_ID INTEGER NOT NULL,	
				CP_CODE VARCHAR(3) NOT NULL,	
				LC_ACTIVE SINGLE NOT NULL,
				UPDATE_USER INTEGER2 NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_LINE_COMPETENCE PRIMARY KEY (PL_CODE, ORG_ID, CP_CODE)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE PER_LINE_COMPETENCE(
				PL_CODE VARCHAR2(10) NOT NULL,	
				ORG_ID NUMBER(10) NOT NULL,	
				CP_CODE VARCHAR2(3) NOT NULL,	
				LC_ACTIVE NUMBER(1) NOT NULL,
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				CONSTRAINT PK_PER_LINE_COMPETENCE PRIMARY KEY (PL_CODE, ORG_ID, CP_CODE)) ";
			elseif($DPISDB=="mysql")
				$cmd = " CREATE TABLE PER_LINE_COMPETENCE(
				PL_CODE VARCHAR(10) NOT NULL,	
				ORG_ID INTEGER(10) NOT NULL,	
				CP_CODE VARCHAR(3) NOT NULL,	
				LC_ACTIVE SMALLINT(1) NOT NULL,
				UPDATE_USER SMALLINT(5) NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_LINE_COMPETENCE PRIMARY KEY (PL_CODE, ORG_ID, CP_CODE)) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " SELECT DISTINCT PL_CODE, ORG_ID FROM PER_POSITION WHERE POS_STATUS = 1 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			while($data = $db_dpis->get_array()){
				$PL_CODE = trim($data[PL_CODE]);
				$ORG_ID = $data[ORG_ID];
				$code = array(	"101", "102", "103", "104", "105", "106" );
				for ( $i=0; $i<count($code); $i++ ) { 
					$cmd = " INSERT INTO PER_LINE_COMPETENCE (PL_CODE, ORG_ID, CP_CODE, LC_ACTIVE, UPDATE_USER, UPDATE_DATE) 
									VALUES('$PL_CODE', $ORG_ID, '$code[$i]', 1, $SESS_USERID, '$UPDATE_DATE') ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
				} // end for
			} // end while						

			$cmd = " SELECT DISTINCT PL_CODE, a.ORG_ID FROM PER_POSITION a, PER_PERSONAL b 
							WHERE a.POS_ID = b.POS_ID AND b.LEVEL_NO IN ('D1', 'D2', 'M1', 'M2') AND PER_STATUS = 1 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			while($data = $db_dpis->get_array()){
				$PL_CODE = trim($data[PL_CODE]);
				$ORG_ID = $data[ORG_ID];
				$code = array(	"201", "202", "203", "204", "205", "206" );
				for ( $i=0; $i<count($code); $i++ ) { 
					$cmd = " INSERT INTO PER_LINE_COMPETENCE (PL_CODE, ORG_ID, CP_CODE, LC_ACTIVE, UPDATE_USER, UPDATE_DATE) 
									VALUES('$PL_CODE', $ORG_ID, '$code[$i]', 1, $SESS_USERID, '$UPDATE_DATE') ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
				} // end for
			} // end while						
		} // end if

		$cmd = " SELECT COUNT(PER_ID) AS COUNT_DATA FROM PER_COMPETENCY_ASSESSMENT ";
		$count_data = $db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		if (!$count_data) {
			if($DPISDB=="odbc") 
				$cmd = " CREATE TABLE PER_COMPETENCY_ASSESSMENT(
				PER_ID INTEGER NOT NULL,	
				CHIEF_PER_ID INTEGER NOT NULL,	
				FRIEND_PER_ID MEMO NULL,	
				SUB_PER_ID MEMO NULL,	
				UPDATE_USER INTEGER2 NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_COMPETENCY_ASSESSMENT PRIMARY KEY (PER_ID)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE PER_COMPETENCY_ASSESSMENT(
				PER_ID NUMBER(10) NOT NULL,	
				CHIEF_PER_ID NUMBER(10) NOT NULL,	
				FRIEND_PER_ID VARCHAR2(1000) NULL,	
				SUB_PER_ID VARCHAR2(1000) NULL,	
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				CONSTRAINT PK_PER_COMPETENCY_ASSESSMENT PRIMARY KEY (PER_ID)) ";
			elseif($DPISDB=="mysql")
				$cmd = " CREATE TABLE PER_COMPETENCY_ASSESSMENT(
				PER_ID INTEGER(10) NOT NULL,	
				CHIEF_PER_ID INTEGER(10) NOT NULL,	
				FRIEND_PER_ID TEXT NULL,	
				SUB_PER_ID TEXT NULL,	
				UPDATE_USER SMALLINT(5) NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_COMPETENCY_ASSESSMENT PRIMARY KEY (PER_ID)) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end if

		$cmd = " SELECT COUNT(QS_ID) AS COUNT_DATA FROM PER_QUESTION_STOCK ";
		$count_data = $db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		if (!$count_data) {
			if($DPISDB=="odbc") 
				$cmd = " CREATE TABLE PER_QUESTION_STOCK (
				QS_ID INTEGER NOT NULL,	
				CP_CODE VARCHAR(3) NULL,	
				CL_NO SINGLE NOT NULL,	
				QS_NAME MEMO NOT NULL,
				QS_SCORE1 NUMBER NULL,
				QS_SCORE2 NUMBER NULL,
				QS_SCORE3 NUMBER NULL,
				QS_SCORE4 NUMBER NULL,
				QS_SCORE5 NUMBER NULL,
				QS_SCORE6 NUMBER NULL,
				UPDATE_USER INTEGER2 NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_QUESTION_STOCK  PRIMARY KEY (QS_ID)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE PER_QUESTION_STOCK (
				QS_ID NUMBER(10) NOT NULL,	
				CP_CODE VARCHAR2(3) NULL,	
				CL_NO NUMBER(1) NOT NULL,	
				QS_NAME VARCHAR2(1000) NOT NULL,
				QS_SCORE1 NUMBER(5,2) NULL,
				QS_SCORE2 NUMBER(5,2) NULL,
				QS_SCORE3 NUMBER(5,2) NULL,
				QS_SCORE4 NUMBER(5,2) NULL,
				QS_SCORE5 NUMBER(5,2) NULL,
				QS_SCORE6 NUMBER(5,2) NULL,
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				CONSTRAINT PK_PER_QUESTION_STOCK  PRIMARY KEY (QS_ID)) ";
			elseif($DPISDB=="mysql") 
				$cmd = " CREATE TABLE PER_QUESTION_STOCK (
				QS_ID INTEGER(10) NOT NULL,	
				CP_CODE VARCHAR(3) NULL,	
				CL_NO SMALLINT(1) NOT NULL,	
				QS_NAME TEXT NOT NULL,
				QS_SCORE1 DECIMAL(5,2) NULL,
				QS_SCORE2 DECIMAL(5,2) NULL,
				QS_SCORE3 DECIMAL(5,2) NULL,
				QS_SCORE4 DECIMAL(5,2) NULL,
				QS_SCORE5 DECIMAL(5,2) NULL,
				QS_SCORE6 DECIMAL(5,2) NULL,
				UPDATE_USER SMALLINT(5) NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_QUESTION_STOCK  PRIMARY KEY (QS_ID)) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end if

		$cmd = " SELECT COUNT(CPT_CODE) AS COUNT_DATA FROM PER_COMPETENCY_TEST ";
		$count_data = $db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		if (!$count_data) {
			if($DPISDB=="odbc") 
				$cmd = " CREATE TABLE PER_COMPETENCY_TEST (
				CPT_CODE VARCHAR(10) NOT NULL,	
				CPT_NAME VARCHAR(255) NOT NULL,
				CP_CODE VARCHAR(3) NOT NULL,	
				CPT_ACTIVE SINGLE NOT NULL,
				UPDATE_USER INTEGER2 NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_COMPETENCY_TEST  PRIMARY KEY (CPT_CODE)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE PER_COMPETENCY_TEST (
				CPT_CODE VARCHAR2(10) NOT NULL,	
				CPT_NAME VARCHAR2(255) NOT NULL,
				CP_CODE VARCHAR2(3) NOT NULL,	
				CPT_ACTIVE NUMBER(1) NOT NULL,
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				CONSTRAINT PK_PER_COMPETENCY_TEST  PRIMARY KEY (CPT_CODE)) ";
			elseif($DPISDB=="mysql") 
				$cmd = " CREATE TABLE PER_COMPETENCY_TEST (
				CPT_CODE VARCHAR(10) NOT NULL,	
				CPT_NAME VARCHAR(255) NOT NULL,
				CP_CODE VARCHAR(3) NOT NULL,	
				CPT_ACTIVE SMALLINT(1) NOT NULL,
				UPDATE_USER SMALLINT(5) NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_COMPETENCY_TEST  PRIMARY KEY (CPT_CODE)) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end if

		$cmd = " SELECT COUNT(CPT_CODE) AS COUNT_DATA FROM PER_COMPETENCY_DTL ";
		$count_data = $db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		if (!$count_data) {
			if($DPISDB=="odbc") 
				$cmd = " CREATE TABLE PER_COMPETENCY_DTL (
				CPT_CODE VARCHAR(10) NOT NULL,	
				SEQ_NO INTEGER2 NOT NULL,
				QS_ID INTEGER NULL,
				UPDATE_USER INTEGER2 NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_COMPETENCY_DTL  PRIMARY KEY (CPT_CODE, SEQ_NO)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE PER_COMPETENCY_DTL (
				CPT_CODE VARCHAR2(10) NOT NULL,	
				SEQ_NO NUMBER(3) NULL,
				QS_ID NUMBER(10) NOT NULL,
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				CONSTRAINT PK_PER_COMPETENCY_DTL  PRIMARY KEY (CPT_CODE, SEQ_NO)) ";
			elseif($DPISDB=="mysql") 
				$cmd = " CREATE TABLE PER_COMPETENCY_DTL (
				CPT_CODE VARCHAR(10) NOT NULL,	
				SEQ_NO SMALLINT(3) NULL,
				QS_ID INTEGER(10) NULL,
				UPDATE_USER SMALLINT(5) NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_COMPETENCY_DTL  PRIMARY KEY (CPT_CODE, SEQ_NO)) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end if

		$cmd = " SELECT COUNT(CF_ID) AS COUNT_DATA FROM PER_COMPETENCY_FORM ";
		$count_data = $db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		if (!$count_data) {
			if($DPISDB=="odbc") 
				$cmd = " CREATE TABLE PER_COMPETENCY_FORM (
				CF_ID INTEGER NOT NULL,	
				KF_ID INTEGER NOT NULL,	
				CF_TYPE SINGLE NOT NULL,
				CF_PER_ID INTEGER NOT NULL,	
				CF_SCORE VARCHAR(255) NULL,
				CF_STATUS SINGLE NOT NULL,
				UPDATE_USER INTEGER2 NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_COMPETENCY_FORM  PRIMARY KEY (CF_ID)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE PER_COMPETENCY_FORM (
				CF_ID NUMBER(10) NOT NULL,	
				KF_ID NUMBER(10) NOT NULL,	
				CF_TYPE NUMBER(1) NOT NULL,
				CF_PER_ID NUMBER(10) NOT NULL,	
				CF_SCORE VARCHAR2(255) NULL,
				CF_STATUS NUMBER(1) NOT NULL,
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				CONSTRAINT PK_PER_COMPETENCY_FORM  PRIMARY KEY (CF_ID)) ";
			elseif($DPISDB=="mysql") 
				$cmd = " CREATE TABLE PER_COMPETENCY_FORM (
				CF_ID INTEGER(10) NOT NULL,	
				KF_ID INTEGER(10) NOT NULL,	
				CF_TYPE SMALLINT(1) NOT NULL,
				CF_PER_ID INTEGER(10) NOT NULL,	
				CF_SCORE VARCHAR(255) NULL,
				CF_STATUS SMALLINT(1) NOT NULL,
				UPDATE_USER SMALLINT(5) NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_COMPETENCY_FORM  PRIMARY KEY (CF_ID)) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end if

		$cmd = " SELECT COUNT(CF_ID) AS COUNT_DATA FROM PER_COMPETENCY_ANSWER ";
		$count_data = $db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		if (!$count_data) {
			if($DPISDB=="odbc") 
				$cmd = " CREATE TABLE PER_COMPETENCY_ANSWER (
				CF_ID INTEGER NOT NULL,	
				QS_ID INTEGER NOT NULL,	
				CA_ANSWER SINGLE NOT NULL,
				CA_DESCRIPTION MEMO NULL,
				UPDATE_USER INTEGER2 NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_COMPETENCY_ANSWER  PRIMARY KEY (CF_ID, QS_ID)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE PER_COMPETENCY_ANSWER (
				CF_ID NUMBER(10) NOT NULL,	
				QS_ID NUMBER(10) NOT NULL,	
				CA_ANSWER NUMBER(1) NOT NULL,
				CA_DESCRIPTION VARCHAR2(1000) NULL,
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				CONSTRAINT PK_PER_COMPETENCY_ANSWER  PRIMARY KEY (CF_ID, QS_ID)) ";
			elseif($DPISDB=="mysql") 
				$cmd = " CREATE TABLE PER_COMPETENCY_ANSWER (
				CF_ID INTEGER(10) NOT NULL,	
				QS_ID INTEGER(10) NOT NULL,	
				CA_ANSWER SMALLINT(1) NOT NULL,
				CA_DESCRIPTION TEXT NULL,
				UPDATE_USER SMALLINT(5) NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_COMPETENCY_ANSWER  PRIMARY KEY (CF_ID, QS_ID)) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end if

		$cmd = " SELECT COUNT(PER_ID) AS COUNT_DATA FROM PER_DEVELOPE_PLAN ";
		$count_data = $db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		if (!$count_data) {
			if($DPISDB=="odbc") 
				$cmd = " CREATE TABLE PER_DEVELOPE_PLAN(
				PD_PLAN_ID INTEGER NOT NULL,	
				PD_PLAN_KF_ID INTEGER NOT NULL,	
				PD_GUIDE_ID INTEGER NULL,	
				PLAN_FREE_TEXT MEMO NULL,	
				PD_PLAN_START VARCHAR(19) NULL,		
				PD_PLAN_END VARCHAR(19) NULL,		
				UPDATE_USER INTEGER2 NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_DEVELOPE_PLAN PRIMARY KEY (PD_PLAN_ID)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE PER_DEVELOPE_PLAN(
				PD_PLAN_ID NUMBER(10) NOT NULL,	
				PD_PLAN_KF_ID NUMBER(10) NOT NULL,	
				PD_GUIDE_ID NUMBER(10) NULL,	
				PLAN_FREE_TEXT VARCHAR2(1000) NULL,	
				PD_PLAN_START VARCHAR2(19) NULL,		
				PD_PLAN_END VARCHAR2(19) NULL,		
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				CONSTRAINT PK_PER_DEVELOPE_PLAN PRIMARY KEY (PD_PLAN_ID)) ";
			elseif($DPISDB=="mysql")
				$cmd = " CREATE TABLE PER_DEVELOPE_PLAN(
				PD_PLAN_ID INTEGER(10) NOT NULL,	
				PD_PLAN_KF_ID INTEGER(10) NOT NULL,	
				PD_GUIDE_ID INTEGER(10) NULL,	
				PLAN_FREE_TEXT TEXT NULL,	
				PD_PLAN_START VARCHAR(19) NULL,		
				PD_PLAN_END VARCHAR(19) NULL,		
				UPDATE_USER SMALLINT(5) NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_DEVELOPE_PLAN PRIMARY KEY (PD_PLAN_ID)) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end if

		$cmd = " SELECT COUNT(PER_ID) AS COUNT_DATA FROM PER_DEVELOPE_GUIDE ";
		$count_data = $db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		if (!$count_data) {
			if($DPISDB=="odbc") 
				$cmd = " CREATE TABLE PER_DEVELOPE_GUIDE(
				PD_GUIDE_ID INTEGER NOT NULL,	
				PD_GUIDE_LEVEL INTEGER NOT NULL,	
				PD_GUIDE_COMPETENCE VARCHAR(3) NOT NULL,		
				PD_GUIDE_DESCRIPTION1 MEMO NULL,	
				PD_GUIDE_DESCRIPTION2 MEMO NOT NULL,		
				UPDATE_USER INTEGER2 NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_DEVELOPE_GUIDE PRIMARY KEY (PD_GUIDE_ID)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE PER_DEVELOPE_GUIDE(
				PD_GUIDE_ID NUMBER(10) NOT NULL,	
				PD_GUIDE_LEVEL NUMBER(10) NOT NULL,	
				PD_GUIDE_COMPETENCE VARCHAR2(3) NOT NULL,		
				PD_GUIDE_DESCRIPTION1 VARCHAR2(2000) NULL,	
				PD_GUIDE_DESCRIPTION2 VARCHAR2(2000) NOT NULL,		
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				CONSTRAINT PK_PER_DEVELOPE_GUIDE PRIMARY KEY (PD_GUIDE_ID)) ";
			elseif($DPISDB=="mysql")
				$cmd = " CREATE TABLE PER_DEVELOPE_GUIDE(
				PD_GUIDE_ID INTEGER(10) NOT NULL,	
				PD_GUIDE_LEVEL INTEGER(10) NOT NULL,	
				PD_GUIDE_COMPETENCE VARCHAR(3) NOT NULL,		
				PD_GUIDE_DESCRIPTION1 TEXT NULL,	
				PD_GUIDE_DESCRIPTION2 TEXT NOT NULL,		
				UPDATE_USER SMALLINT(5) NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_DEVELOPE_GUIDE PRIMARY KEY (PD_GUIDE_ID)) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end if

		if($SCOMPETENCY[$PER_ID_DEPARTMENT_ID]!="Y"){
/*			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV0 WHERE MENU_LABEL = 'การประเมินสมรรถนะ' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV1 
							  WHERE LINKTO_WEB = 'kpi_line_competence.html?table=PER_LINE_COMPETENCE' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV1 
							  WHERE LINKTO_WEB = 'kpi_standard_competence.html?table=PER_STANDARD_COMPETENCE' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'structure_by_per.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV1 
							  WHERE LINKTO_WEB = 'data_competency_assessment_person.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'data_competency_question.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'data_competency_test.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error(); */
		}else{
			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV0 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_TID, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'TH', 47, 8, 'การประเมินสมรรถนะ', 'S', 'N', 0, $CREATE_DATE, $CREATE_BY, $CREATE_DATE, 
							  $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV0 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, 	TYPE_LINKTO, LINKTO_TID, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', 47, 8, 'การประเมินสมรรถนะ', 'S', 'N', 0, $CREATE_DATE, $CREATE_BY, $CREATE_DATE, 
							  $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 
							  WHERE LINKTO_WEB = 'kpi_line_competence.html?table=PER_LINE_COMPETENCE' ";
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
								  VALUES (1, 'TH', $MAX_ID, 1, 'K21 สมรรถนะประจำตำแหน่งงาน', 'S', 'W', 
								  'kpi_line_competence.html?table=PER_LINE_COMPETENCE', 0, 47, $CREATE_DATE, $CREATE_BY, 
								  $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
								  UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 1, 'K21 สมรรถนะของแต่ละสายงาน', 'S', 'W', 
								  'kpi_line_competence.html?table=PER_LINE_COMPETENCE', 0, 47, $CREATE_DATE, $CREATE_BY, 
								  $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'develop_guide.html' ";
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
								  VALUES (1, 'TH', $MAX_ID, 2, 'K22 กิจกรรมเพื่อการพัฒนา', 'S', 'W', 'develop_guide.html', 0, 47, $CREATE_DATE, 
								  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
								  UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 2, 'K22 กิจกรรมเพื่อการพัฒนา', 'S', 'W', 'develop_guide.html', 0, 47, $CREATE_DATE, 
								  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}
/*
			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 
							  WHERE LINKTO_WEB = 'data_competency_assessment_person.html' ";
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
								  VALUES (1, 'TH', $MAX_ID, 4, 'K24 ผู้ประเมินและผู้ถูกประเมิน', 'S', 'W', 'data_competency_assessment_person.html', 0, 47, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
								  UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 4, 'K24 ผู้ประเมินและผู้ถูกประเมิน', 'S', 'W', 'data_competency_assessment_person.html', 0, 47, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}
*/
			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 
							  WHERE LINKTO_WEB = 'data_competency_question.html' ";
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
								  VALUES (1, 'TH', $MAX_ID, 5, 'K25 คำถามของแบบประเมินสมรรถนะ', 'S', 'W', 'data_competency_question.html', 0, 47, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
								  UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 5, 'K25 คำถามของแบบประเมินสมรรถนะ', 'S', 'W', 'data_competency_question.html', 0, 47, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}
/*
			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 
							  WHERE LINKTO_WEB = 'data_competency_test.html' ";
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
								  VALUES (1, 'TH', $MAX_ID, 6, 'K26 แบบประเมินสมรรถนะ', 'S', 'W', 'data_competency_test.html', 0, 47, $CREATE_DATE, 
								  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
								  UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 6, 'K26 แบบประเมินสมรรถนะ', 'S', 'W', 'data_competency_test.html', 0, 47, $CREATE_DATE, 
								  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}
*/
			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 
							  WHERE LINKTO_WEB = 'master_table_pos_type.html' ";
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
								  VALUES (1, 'TH', $MAX_ID, 7, 'K27 % การประเมินสมรรถนะ', 'S', 'W', 'master_table_pos_type.html', 0, 47, $CREATE_DATE,
								  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
								  UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 7, 'K27 % การประเมินสมรรถนะ', 'S', 'W', 'master_table_pos_type.html', 0, 47, $CREATE_DATE,
								  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'kpi_cycle_list.html' ";
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
								  VALUES (1, 'TH', $MAX_ID, 8, 'K28 รายงานภาพรวมการประเมินสมรรถนะในแต่ละหน่วยงาน', 'S', 'W', 'kpi_cycle_list.html', 0, 47, $CREATE_DATE,
								  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
								  UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 8, 'K28 รายงานภาพรวมการประเมินสมรรถนะในแต่ละหน่วยงาน', 'S', 'W', 'kpi_cycle_list.html', 0, 47, $CREATE_DATE,
								  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}
/*
			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'rpt_R001001.html' ";
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
								  VALUES (1, 'TH', $MAX_ID, 11, 'K31 รายงานผลการประเมินสมรรถนะรายบุคคล', 'S', 'W', 'rpt_R001001.html', 0, 47, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
								  UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 11, 'K31 รายงานผลการประเมินสมรรถนะรายบุคคล', 'S', 'W', 'rpt_R001001.html', 0, 47, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'rpt_R001002.html' ";
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
								  VALUES (1, 'TH', $MAX_ID, 12, 'K32 รายงานผลการประเมินสมรรถนะ จำแนกตามสมรรถะ', 'S', 'W', 'rpt_R001002.html', 0, 47, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
								  UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 12, 'K32 รายงานผลการประเมินสมรรถนะ จำแนกตามสมรรถะ', 'S', 'W', 'rpt_R001002.html', 0, 47, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'rpt_R001003.html' ";
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
								  VALUES (1, 'TH', $MAX_ID, 13, 'K33 รายงานร้อยละของบุคลากรที่ต้องการพัฒนา จำแนกตามสังกัด', 'S', 'W', 'rpt_R001003.html', 0, 47, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
								  UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 13, 'K33 รายงานร้อยละของบุคลากรที่ต้องการพัฒนา จำแนกตามสังกัด', 'S', 'W', 'rpt_R001003.html', 0, 47, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}
*/
		} // end if การประเมินสมรรถนะ

?>