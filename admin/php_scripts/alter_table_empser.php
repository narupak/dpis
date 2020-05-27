<?
			$cmd = " UPDATE PER_LEVEL SET LEVEL_SHORTNAME = 'บริการ', LEVEL_SEQ_NO = 61 WHERE LEVEL_NO = 'E1' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LEVEL SET LEVEL_NAME = 'กลุ่มงานเทคนิคทั่วไป', LEVEL_SHORTNAME = 'เทคนิคทั่วไป', LEVEL_SEQ_NO = 62 WHERE LEVEL_NO = 'E2' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LEVEL SET LEVEL_SHORTNAME = 'บริหารทั่วไป', LEVEL_SEQ_NO = 63 WHERE LEVEL_NO = 'E3' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LEVEL SET LEVEL_SHORTNAME = 'วิชาชีพเฉพาะ', LEVEL_SEQ_NO = 64 WHERE LEVEL_NO = 'E4' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LEVEL SET LEVEL_SHORTNAME = 'เชี่ยวชาญเฉพาะ', LEVEL_SEQ_NO = 65 WHERE LEVEL_NO = 'E5' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DELETE FROM PER_LEVEL WHERE LEVEL_NO = 'E6' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LEVEL SET LEVEL_SHORTNAME = 'เทคนิคพิเศษ', LEVEL_SEQ_NO = 67 WHERE LEVEL_NO = 'E7' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LEVEL SET LEVEL_SHORTNAME = 'ระดับทั่วไป', LEVEL_SEQ_NO = 71 WHERE LEVEL_NO = 'S1' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LEVEL SET LEVEL_SHORTNAME = 'ระดับประเทศ', LEVEL_SEQ_NO = 72 WHERE LEVEL_NO = 'S2' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LEVEL SET LEVEL_SHORTNAME = 'ระดับสากล', LEVEL_SEQ_NO = 73 WHERE LEVEL_NO = 'S3' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE MENU_LABEL = 'M10 พนักงานราชการ' ";
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
								  VALUES (1, 'TH', $MAX_ID1, 10, 'M10 พนักงานราชการ', 'S', 'N', 0, 9, $CREATE_DATE, $CREATE_BY, 
								  $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, UPDATE_DATE, 
								  UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID1, 10, 'M10 พนักงานราชการ', 'S', 'N', 0, 9, $CREATE_DATE, $CREATE_BY, 
								  $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, LEVEL_NAME, PER_TYPE, LEVEL_SEQ_NO)
							  VALUES ('E7', 1, $SESS_USERID, '$UPDATE_DATE', 'กลุ่มงานเทคนิคพิเศษ',3, 67) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DROP TABLE PER_PRACTICE ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			if($DPISDB=="odbc") 
				$cmd = " CREATE TABLE PER_PRACTICE(
				PPT_CODE VARCHAR(10) NOT NULL,	
				PPT_NAME VARCHAR(100) NOT NULL,
				PPT_ACTIVE SINGLE NOT NULL,
				UPDATE_USER INTEGER2 NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				PPT_SEQ_NO INTEGER2 NULL,
				CONSTRAINT PK_PER_PRACTICE PRIMARY KEY (PPT_CODE)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE PER_PRACTICE(
				PPT_CODE VARCHAR2(10) NOT NULL,	
				PPT_NAME VARCHAR2(100) NOT NULL,
				PPT_ACTIVE NUMBER(1) NOT NULL,
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				PPT_SEQ_NO NUMBER(5) NULL,
				CONSTRAINT PK_PER_PRACTICE PRIMARY KEY (PPT_CODE)) ";
			elseif($DPISDB=="mysql")
				$cmd = " CREATE TABLE PER_PRACTICE(
				PPT_CODE VARCHAR(10) NOT NULL,	
				PPT_NAME VARCHAR(100) NOT NULL,
				PPT_ACTIVE SMALLINT(1) NOT NULL,
				UPDATE_USER SMALLINT(5) NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				PPT_SEQ_NO SMALLINT(5) NULL,
				CONSTRAINT PK_PER_PRACTICE PRIMARY KEY (PPT_CODE)) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_PRACTICE (PPT_CODE, PPT_NAME, PPT_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('1', 'ภารกิจหลัก', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_PRACTICE (PPT_CODE, PPT_NAME, PPT_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('2', 'ภารกิจรอง', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_PRACTICE (PPT_CODE, PPT_NAME, PPT_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('3', 'ภารกิจสนับสนุน', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " SELECT MENU_ID FROM BACKOFFICE_MENU_BAR_LV1 WHERE MENU_LABEL = 'M10 พนักงานราชการ' ";
			$db->send_cmd($cmd);
			//$db->show_error();
			$data = $db->get_array();
			$MENU_ID = $data[MENU_ID];

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 
							  WHERE LINKTO_WEB = 'master_table.html?table=PER_PRACTICE' ";
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
								  VALUES (1, 'TH', $MAX_ID, 1, 'M1001 ประเภทภารกิจ', 'S', 'W', 'master_table.html?table=PER_PRACTICE', 0, 9, $MENU_ID, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 1, 'M1001 ประเภทภารกิจ', 'S', 'W', 'master_table.html?table=PER_PRACTICE', 0, 9, $MENU_ID, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			if($DPISDB=="odbc") 
				$cmd = " CREATE TABLE PER_POS_EMPSER_FRAME(
				PEF_CODE VARCHAR(10) NOT NULL,	
				PEF_NAME VARCHAR(100) NOT NULL,
				PEF_ACTIVE SINGLE NOT NULL,
				UPDATE_USER INTEGER2 NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				PEF_SEQ_NO INTEGER2 NULL,
				CONSTRAINT PK_PER_POS_EMPSER_FRAME PRIMARY KEY (PEF_CODE)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE PER_POS_EMPSER_FRAME(
				PEF_CODE VARCHAR2(10) NOT NULL,	
				PEF_NAME VARCHAR2(100) NOT NULL,
				PEF_ACTIVE NUMBER(1) NOT NULL,
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				PEF_SEQ_NO NUMBER(5) NULL,
				CONSTRAINT PK_PER_POS_EMPSER_FRAME PRIMARY KEY (PEF_CODE)) ";
			elseif($DPISDB=="mysql")
				$cmd = " CREATE TABLE PER_POS_EMPSER_FRAME(
				PEF_CODE VARCHAR(10) NOT NULL,	
				PEF_NAME VARCHAR(100) NOT NULL,
				PEF_ACTIVE SMALLINT(1) NOT NULL,
				UPDATE_USER SMALLINT(5) NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				PEF_SEQ_NO SMALLINT(5) NULL,
				CONSTRAINT PK_PER_POS_EMPSER_FRAME PRIMARY KEY (PEF_CODE)) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_EMPSER_FRAME (PEF_CODE, PEF_NAME, PEF_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('1', 'กรอบอัตรากำลัง 4 ปี', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_POS_EMPSER_FRAME (PEF_CODE, PEF_NAME, PEF_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('2', 'กรอบอัตรากำลังตามมติ ครม. 5 ต.ค. 47 (กลุ่ม 2)', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_POS_EMPSER_FRAME (PEF_CODE, PEF_NAME, PEF_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('3', 'กรอบอัตรากำลังตามประกาศ คพร. ข้อ 19', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_POS_EMPSER_FRAME (PEF_CODE, PEF_NAME, PEF_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('4', 'กรอบอัตรากำลังตามมติ ครม. กรณีจังหวัดชายแดนภาคใต้', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 
							  WHERE LINKTO_WEB = 'master_table.html?table=PER_POS_EMPSER_FRAME' ";
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
								  VALUES (1, 'TH', $MAX_ID, 2, 'M1002 ประเภทกรอบอัตรากำลัง', 'S', 'W', 'master_table.html?table=PER_POS_EMPSER_FRAME', 0, 9, $MENU_ID, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 2, 'M1002 ประเภทกรอบอัตรากำลัง', 'S', 'W', 'master_table.html?table=PER_POS_EMPSER_FRAME', 0, 9, $MENU_ID, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			if($DPISDB=="odbc") 
				$cmd = " CREATE TABLE PER_POS_STATUS(
				PPS_CODE VARCHAR(10) NOT NULL,	
				PPS_NAME VARCHAR(100) NOT NULL,
				PPS_ACTIVE SINGLE NOT NULL,
				UPDATE_USER INTEGER2 NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				PPS_SEQ_NO INTEGER2 NULL,
				CONSTRAINT PK_PER_POS_STATUS PRIMARY KEY (PPS_CODE)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE PER_POS_STATUS(
				PPS_CODE VARCHAR2(10) NOT NULL,	
				PPS_NAME VARCHAR2(100) NOT NULL,
				PPS_ACTIVE NUMBER(1) NOT NULL,
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				PPS_SEQ_NO NUMBER(5) NULL,
				CONSTRAINT PK_PER_POS_STATUS PRIMARY KEY (PPS_CODE)) ";
			elseif($DPISDB=="mysql")
				$cmd = " CREATE TABLE PER_POS_STATUS(
				PPS_CODE VARCHAR(10) NOT NULL,	
				PPS_NAME VARCHAR(100) NOT NULL,
				PPS_ACTIVE SMALLINT(1) NOT NULL,
				UPDATE_USER SMALLINT(5) NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				PPS_SEQ_NO SMALLINT(5) NULL,
				CONSTRAINT PK_PER_POS_STATUS PRIMARY KEY (PPS_CODE)) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_STATUS (PPS_CODE, PPS_NAME, PPS_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('1', 'ตำแหน่งที่มีคนครอง', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_POS_STATUS (PPS_CODE, PPS_NAME, PPS_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('2', 'ตำแหน่งว่างที่มีงบประมาณในการจ้างแล้ว', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_POS_STATUS (PPS_CODE, PPS_NAME, PPS_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('3', 'ตำแหน่งว่างที่ไม่มีงบประมาณในการจ้าง', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_POS_STATUS (PPS_CODE, PPS_NAME, PPS_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('4', 'ตำแหน่งที่มีลูกจ้างประจำครองอยู่', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 
							  WHERE LINKTO_WEB = 'master_table.html?table=PER_POS_STATUS' ";
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
								  VALUES (1, 'TH', $MAX_ID, 3, 'M1003 สถานะของตำแหน่ง', 'S', 'W', 'master_table.html?table=PER_POS_STATUS', 0, 9, $MENU_ID, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 3, 'M1003 สถานะของตำแหน่ง', 'S', 'W', 'master_table.html?table=PER_POS_STATUS', 0, 9, $MENU_ID, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " DROP TABLE PER_END_REASON ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, UPDATE_DATE, MOV_SUB_TYPE)
							  VALUES ('11891', 'สอบบรรจุเข้ารับราชการได้', 1, 1, $SESS_USERID, '$UPDATE_DATE', 9) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, UPDATE_DATE, MOV_SUB_TYPE)
							  VALUES ('11892', 'ผลการประเมินผลการปฏิบัติงานต่ำกว่าเกณฑ์', 1, 1, $SESS_USERID, '$UPDATE_DATE', 9) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, UPDATE_DATE, MOV_SUB_TYPE)
							  VALUES ('11893', 'การลงโทษทางวินัย', 1, 1, $SESS_USERID, '$UPDATE_DATE', 9) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, UPDATE_DATE, MOV_SUB_TYPE)
							  VALUES ('11894', 'สาเหตุอื่น ๆ', 1, 1, $SESS_USERID, '$UPDATE_DATE', 9) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'master_table.html?table=PER_END_REASON' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_POS_EMPSER ADD PPT_CODE VARCHAR(10) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POS_EMPSER ADD PPT_CODE VARCHAR2(10) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_POS_EMPSER ADD PEF_CODE VARCHAR(10) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POS_EMPSER ADD PEF_CODE VARCHAR2(10) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_POS_EMPSER ADD PPS_CODE VARCHAR(10) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POS_EMPSER ADD PPS_CODE VARCHAR2(10) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_EMPSER SET PPS_CODE = 1 WHERE POEMS_ID IN 
							(SELECT POEMS_ID FROM PER_PERSONAL WHERE PER_TYPE = 3 AND PER_STATUS =  1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$level = array(	"E1", "E2", "E3", "E4", "E5", "E7", "S1", "S2", "S3" );
			$code = array(	"101", "102", "103", "104", "105", "301", "302", "303", "304", "305", "306", "307", "308", "309", "310", "311", "312", 
											"313", "314", "315", "316" );
			$target = array(	1, 1, 2, 3, 4, 1, 1, 3, 4, 5 );
			for ( $i=0; $i<count($level); $i++ ) { 
				for ( $j=0; $j<count($code); $j++ ) { 
					$cmd = " INSERT INTO PER_STANDARD_COMPETENCE (LEVEL_NO, CP_CODE, TARGET_LEVEL, UPDATE_USER, 
									UPDATE_DATE)
									VALUES ('$level[$i]', '$code[$j]', $TARGET_LEVEL,  $SESS_USERID, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				} // end for
			} // end for
		
			$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
							  VALUES (0, 'E1', 0, 0, 1, $SESS_USERID, '$UPDATE_DATE', 6100, 18500) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
							  VALUES (0, 'E2', 0, 0, 1, $SESS_USERID, '$UPDATE_DATE', 7010, 22820) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
							  VALUES (0, 'E7', 0, 0, 1, $SESS_USERID, '$UPDATE_DATE', 12230, 56940) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
							  VALUES (0, 'E3', 0, 0, 1, $SESS_USERID, '$UPDATE_DATE', 9530, 31770) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
							  VALUES (0, 'E4', 0, 0, 1, $SESS_USERID, '$UPDATE_DATE', 10330, 40790) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
							  VALUES (0, 'E5', 0, 0, 1, $SESS_USERID, '$UPDATE_DATE', 35880, 65090) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYER SET LAYER_SALARY = 18500 WHERE LAYER_TYPE = 0 AND LEVEL_NO = 'E1' AND LAYER_NO = 23 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYER SET LAYER_SALARY = 17630 WHERE LAYER_TYPE = 0 AND LEVEL_NO = 'E1' AND LAYER_NO = 22 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYER SET LAYER_SALARY = 16760 WHERE LAYER_TYPE = 0 AND LEVEL_NO = 'E1' AND LAYER_NO = 21 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYER SET LAYER_SALARY = 15890 WHERE LAYER_TYPE = 0 AND LEVEL_NO = 'E1' AND LAYER_NO = 20 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYER SET LAYER_SALARY = 15040 WHERE LAYER_TYPE = 0 AND LEVEL_NO = 'E1' AND LAYER_NO = 19 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYER SET LAYER_SALARY = 14320 WHERE LAYER_TYPE = 0 AND LEVEL_NO = 'E1' AND LAYER_NO = 18 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYER SET LAYER_SALARY = 13620 WHERE LAYER_TYPE = 0 AND LEVEL_NO = 'E1' AND LAYER_NO = 17 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYER SET LAYER_SALARY = 12930 WHERE LAYER_TYPE = 0 AND LEVEL_NO = 'E1' AND LAYER_NO = 16 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYER SET LAYER_SALARY = 12230 WHERE LAYER_TYPE = 0 AND LEVEL_NO = 'E1' AND LAYER_NO = 15 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYER SET LAYER_SALARY = 11640 WHERE LAYER_TYPE = 0 AND LEVEL_NO = 'E1' AND LAYER_NO = 14 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYER SET LAYER_SALARY = 11080 WHERE LAYER_TYPE = 0 AND LEVEL_NO = 'E1' AND LAYER_NO = 13 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYER SET LAYER_SALARY = 10530 WHERE LAYER_TYPE = 0 AND LEVEL_NO = 'E1' AND LAYER_NO = 12 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYER SET LAYER_SALARY = 9990 WHERE LAYER_TYPE = 0 AND LEVEL_NO = 'E1' AND LAYER_NO = 11 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYER SET LAYER_SALARY = 9530 WHERE LAYER_TYPE = 0 AND LEVEL_NO = 'E1' AND LAYER_NO = 10 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYER SET LAYER_SALARY = 9080 WHERE LAYER_TYPE = 0 AND LEVEL_NO = 'E1' AND LAYER_NO = 9 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYER SET LAYER_SALARY = 8610 WHERE LAYER_TYPE = 0 AND LEVEL_NO = 'E1' AND LAYER_NO = 8 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYER SET LAYER_SALARY = 8160 WHERE LAYER_TYPE = 0 AND LEVEL_NO = 'E1' AND LAYER_NO = 7 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYER SET LAYER_SALARY = 7770 WHERE LAYER_TYPE = 0 AND LEVEL_NO = 'E1' AND LAYER_NO = 6 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYER SET LAYER_SALARY = 7400 WHERE LAYER_TYPE = 0 AND LEVEL_NO = 'E1' AND LAYER_NO = 5 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYER SET LAYER_SALARY = 7010 WHERE LAYER_TYPE = 0 AND LEVEL_NO = 'E1' AND LAYER_NO = 4 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYER SET LAYER_SALARY = 6640 WHERE LAYER_TYPE = 0 AND LEVEL_NO = 'E1' AND LAYER_NO = 3 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYER SET LAYER_SALARY = 6380 WHERE LAYER_TYPE = 0 AND LEVEL_NO = 'E1' AND LAYER_NO = 2 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYER SET LAYER_SALARY = 6100 WHERE LAYER_TYPE = 0 AND LEVEL_NO = 'E1' AND LAYER_NO = 1 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DELETE FROM PER_LAYER WHERE LAYER_TYPE = 0 AND LEVEL_NO = 'E2' AND LAYER_NO > 0 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYER SET LAYER_SALARY_MIN = 9530, LAYER_SALARY_MAX = 31770 
								WHERE LAYER_TYPE = 0 AND LEVEL_NO = 'E3' AND LAYER_NO = 0 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYER SET LAYER_SALARY_MIN = 9530, LAYER_SALARY_MAX = 40790 
								WHERE LAYER_TYPE = 0 AND LEVEL_NO = 'E4' AND LAYER_NO = 0 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYER SET LAYER_SALARY_MIN = 16760, LAYER_SALARY_MAX = 65090 
								WHERE LAYER_TYPE = 0 AND LEVEL_NO = 'E5' AND LAYER_NO = 0 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYER SET LAYER_SALARY_MAX = 104000 WHERE LAYER_TYPE = 0 AND LEVEL_NO = 'S1' AND LAYER_NO = 0 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYER SET LAYER_SALARY_MAX = 156000 WHERE LAYER_TYPE = 0 AND LEVEL_NO = 'S2' AND LAYER_NO = 0 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYER SET LAYER_SALARY_MAX = 208000 WHERE LAYER_TYPE = 0 AND LEVEL_NO = 'S3' AND LAYER_NO = 0 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EDUCLEVEL (EL_CODE, EL_NAME, EL_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('09', 'ทักษะประสบการณ์ที่ใช้แทนวุฒิการศึกษา', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_POS_EMPSER ADD POEMS_SKILL VARCHAR(255) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POS_EMPSER ADD POEMS_SKILL VARCHAR2(255) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_POS_EMPSER ADD POEMS_SOUTH SINGLE NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POS_EMPSER ADD POEMS_SOUTH NUMBER(1) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_POS_EMPSER ADD POEMS_SOUTH SMALLINT(1) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_EMPSER SET POEMS_SOUTH = 1 WHERE PEF_CODE = 4 AND POEMS_SOUTH IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

?>