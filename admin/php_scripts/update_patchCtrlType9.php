<?php 
$cmd = " SELECT PD_PLAN_ID FROM PER_DEVELOPE_PLAN ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) {
				if($DPISDB=="odbc") 
					$cmd = " CREATE TABLE PER_DEVELOPE_PLAN (
					PD_PLAN_ID INTEGER NOT NULL,
					PD_PLAN_KF_ID INTEGER NOT NULL,
					PD_GUIDE_ID INTEGER NOT NULL,
					PLAN_FREE_TEXT MEMO NULL,
					PD_PLAN_START VARCHAR(19) NULL,
					PD_PLAN_END VARCHAR(19) NULL,
					UPDATE_USER INTEGER NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_DEVELOPE_PLAN  PRIMARY KEY (PD_PLAN_ID)) ";
				elseif($DPISDB=="oci8") 
					$cmd = " CREATE TABLE PER_DEVELOPE_PLAN (
					PD_PLAN_ID NUMBER(10) NOT NULL,
					PD_PLAN_KF_ID NUMBER(10) NOT NULL,
					PD_GUIDE_ID NUMBER(10) NOT NULL,
					PLAN_FREE_TEXT VARCHAR2(2000) NULL,
					PD_PLAN_START VARCHAR2(19) NULL,
					PD_PLAN_END VARCHAR2(19) NULL,
					UPDATE_USER NUMBER(11) NOT NULL,
					UPDATE_DATE VARCHAR2(19) NOT NULL,		
					CONSTRAINT PK_PER_DEVELOPE_PLAN  PRIMARY KEY (PD_PLAN_ID)) ";
				elseif($DPISDB=="mysql") 
					$cmd = " CREATE TABLE PER_DEVELOPE_PLAN (
					PD_PLAN_ID INTEGER(10) NOT NULL,
					PD_PLAN_KF_ID INTEGER(10) NOT NULL,
					PD_GUIDE_ID INTEGER(10) NOT NULL,
					PLAN_FREE_TEXT TEXT NULL,
					PD_PLAN_START VARCHAR(19) NULL,
					PD_PLAN_END VARCHAR(19) NULL,
					UPDATE_USER INTEGER(11) NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_DEVELOPE_PLAN  PRIMARY KEY (PD_PLAN_ID)) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			} // end if

			$cmd = " SELECT PD_GUIDE_ID FROM PER_DEVELOPE_GUIDE ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) {
				if($DPISDB=="odbc") 
					$cmd = " CREATE TABLE PER_DEVELOPE_GUIDE (
					PD_GUIDE_ID INTEGER NOT NULL,
					PD_GUIDE_LEVEL INTEGER2 NOT NULL,
					PD_GUIDE_COMPETENCE VARCHAR(3) NULL,
					PD_GUIDE_DESCRIPTION1 MEMO NULL,
					PD_GUIDE_DESCRIPTION2 MEMO NULL,
					UPDATE_USER INTEGER NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_DEVELOPE_GUIDE  PRIMARY KEY (PD_GUIDE_ID)) ";
				elseif($DPISDB=="oci8") 
					$cmd = " CREATE TABLE PER_DEVELOPE_GUIDE (
					PD_GUIDE_ID NUMBER(10) NOT NULL,
					PD_GUIDE_LEVEL NUMBER(3) NOT NULL,
					PD_GUIDE_COMPETENCE VARCHAR2(3) NULL,
					PD_GUIDE_DESCRIPTION1 VARCHAR2(2000) NULL,
					PD_GUIDE_DESCRIPTION2 VARCHAR2(2000) NULL,
					UPDATE_USER NUMBER(11) NOT NULL,
					UPDATE_DATE VARCHAR2(19) NOT NULL,		
					CONSTRAINT PK_PER_DEVELOPE_GUIDE  PRIMARY KEY (PD_GUIDE_ID)) ";
				elseif($DPISDB=="mysql") 
					$cmd = " CREATE TABLE PER_DEVELOPE_GUIDE (
					PD_GUIDE_ID INTEGER(10) NOT NULL,
					PD_GUIDE_LEVEL SMALLINT(3) NOT NULL,
					PD_GUIDE_COMPETENCE VARCHAR(3) NULL,
					PD_GUIDE_DESCRIPTION1 TEXT NULL,
					PD_GUIDE_DESCRIPTION2 TEXT NULL,
					UPDATE_USER INTEGER(11) NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_DEVELOPE_GUIDE  PRIMARY KEY (PD_GUIDE_ID)) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			} // end if

			add_field("PER_ORG", "ORG_DOPA_CODE","VARCHAR", "10", "NULL");
			add_field("PER_ORG_ASS", "ORG_DOPA_CODE","VARCHAR", "10", "NULL");
	
			$cmd = " SELECT ES_CODE FROM PER_EMP_STATUS ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) {
				if($DPISDB=="odbc") 
					$cmd = " CREATE TABLE PER_EMP_STATUS(
					ES_CODE VARCHAR(10) NOT NULL,	
					ES_NAME VARCHAR(100) NOT NULL,
					ES_REMARK VARCHAR(255) NULL,
					ES_ACTIVE SINGLE NOT NULL,
					UPDATE_USER INTEGER NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					ES_SEQ_NO INTEGER2 NULL,
					CONSTRAINT PK_PER_EMP_STATUS PRIMARY KEY (ES_CODE)) ";
				elseif($DPISDB=="oci8") 
					$cmd = " CREATE TABLE PER_EMP_STATUS(
					ES_CODE VARCHAR2(10) NOT NULL,	
					ES_NAME VARCHAR2(100) NOT NULL,
					ES_REMARK VARCHAR2(255) NULL,
					ES_ACTIVE NUMBER(1) NOT NULL,
					UPDATE_USER NUMBER(11) NOT NULL,
					UPDATE_DATE VARCHAR2(19) NOT NULL,		
					ES_SEQ_NO NUMBER(5) NULL,
					CONSTRAINT PK_PER_EMP_STATUS PRIMARY KEY (ES_CODE)) ";
				elseif($DPISDB=="mysql")
					$cmd = " CREATE TABLE PER_EMP_STATUS(
					ES_CODE VARCHAR(10) NOT NULL,	
					ES_NAME VARCHAR(100) NOT NULL,
					ES_REMARK VARCHAR(255) NULL,
					ES_ACTIVE SMALLINT(1) NOT NULL,
					UPDATE_USER INTEGER(11) NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					ES_SEQ_NO SMALLINT(5) NULL,
					CONSTRAINT PK_PER_EMP_STATUS PRIMARY KEY (ES_CODE)) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_EMP_STATUS (ES_CODE, ES_NAME, ES_REMARK, ES_ACTIVE, UPDATE_USER, UPDATE_DATE)
								  VALUES ('02', 'ตรงตามตำแหน่ง', 'ระดับหรือคุณสมบัติของข้าราชการหรือลูกจ้างตรงกับตำแหน่งที่ครอง', 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			
				$cmd = " INSERT INTO PER_EMP_STATUS (ES_CODE, ES_NAME, ES_REMARK, ES_ACTIVE, UPDATE_USER, UPDATE_DATE)
								  VALUES ('03', 'รักษาการในตำแหน่งระดับที่ต่ำกว่า', 'ระดับของข้าราชการสูงกว่าระดับที่ครอง', 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
				
				$cmd = " INSERT INTO PER_EMP_STATUS (ES_CODE, ES_NAME, ES_REMARK, ES_ACTIVE, UPDATE_USER, UPDATE_DATE)
								  VALUES ('04', 'รักษาการในตำแหน่งที่เท่ากัน', NULL, 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			
				$cmd = " INSERT INTO PER_EMP_STATUS (ES_CODE, ES_NAME, ES_REMARK, ES_ACTIVE, UPDATE_USER, UPDATE_DATE)
								  VALUES ('05', 'ช่วยราชการ', 'ช่วยราชการ ไม่ได้ปฏิบัติงานในตำแหน่งที่ดำรงตำแหน่งจริง', 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			
				$cmd = " INSERT INTO PER_EMP_STATUS (ES_CODE, ES_NAME, ES_REMARK, ES_ACTIVE, UPDATE_USER, UPDATE_DATE)
								  VALUES ('06', 'ปฏิบัติหน้าที่', NULL, 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			
				$cmd = " INSERT INTO PER_EMP_STATUS (ES_CODE, ES_NAME, ES_REMARK, ES_ACTIVE, UPDATE_USER, UPDATE_DATE)
								  VALUES ('07', 'รักษาการในตำแหน่งระดับที่สูงกว่า', NULL, 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			
				$cmd = " INSERT INTO PER_EMP_STATUS (ES_CODE, ES_NAME, ES_REMARK, ES_ACTIVE, UPDATE_USER, UPDATE_DATE)
								  VALUES ('08', 'ลาศึกษาต่อ', 'ข้าราชการใช้สิทธิลาศึกษาต่อในระดับต่าง ๆ ตามระเบียบ', 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			
				$cmd = " INSERT INTO PER_EMP_STATUS (ES_CODE, ES_NAME, ES_REMARK, ES_ACTIVE, UPDATE_USER, UPDATE_DATE)
								  VALUES ('09', 'พักราชการ', 'อาศัยอำนาจตามความในมาตรา 107 แห่ง พ.ร.บ.ระเบียบข้าราชการพลเรือน พ.ศ.2535', 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			
				$cmd = " INSERT INTO PER_EMP_STATUS (ES_CODE, ES_NAME, ES_REMARK, ES_ACTIVE, UPDATE_USER, UPDATE_DATE)
								  VALUES ('10', 'ประจำกรม', 'สถานที่ปฏิบัติงานตามหมายเหตุ กรม', 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			
				$cmd = " INSERT INTO PER_EMP_STATUS (ES_CODE, ES_NAME, ES_REMARK, ES_ACTIVE, UPDATE_USER, UPDATE_DATE)
								  VALUES ('11', 'ประจำสำนัก/กอง', 'ประจำสำนัก หรือ ประจำกอง', 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			
				$cmd = " INSERT INTO PER_EMP_STATUS (ES_CODE, ES_NAME, ES_REMARK, ES_ACTIVE, UPDATE_USER, UPDATE_DATE)
								  VALUES ('12', 'ประจำจังหวัด', 'ประจำจังหวัด', 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			
				$cmd = " INSERT INTO PER_EMP_STATUS (ES_CODE, ES_NAME, ES_REMARK, ES_ACTIVE, UPDATE_USER, UPDATE_DATE)
								  VALUES ('26', 'รักษาราชการแทน', NULL, 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			
				$cmd = " UPDATE PER_PERSONAL SET ES_CODE = '02' WHERE ES_CODE IS NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			
				$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 
								  WHERE LINKTO_WEB = 'master_table_emp_status.html?table=PER_EMP_STATUS' ";
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
									  VALUES (1, 'TH', $MAX_ID, 14, 'M0314 สถานะการดำรงตำแหน่ง', 'S', 'W', 'master_table_emp_status.html?table=PER_EMP_STATUS', 0, 9, 295, 
									  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();

					$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
									  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
									  CREATE_BY, UPDATE_DATE, UPDATE_BY)
									  VALUES (1, 'EN', $MAX_ID, 14, 'M0314 สถานะการดำรงตำแหน่ง', 'S', 'W', 'master_table_emp_status.html?table=PER_EMP_STATUS', 0, 9, 295, 
									  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();
				}

				add_field("PER_PERSONAL", "ES_CODE","VARCHAR", "10", "NULL");
			}
		
			add_field("PER_PERSONAL", "PL_NAME_WORK","VARCHAR", "255", "NULL");
			add_field("PER_PERSONAL", "ORG_NAME_WORK","VARCHAR", "255", "NULL");
			add_field("PER_MESSAGE_USER", "MSG_READ","VARCHAR", "19", "NULL");

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE  PER_DECORATEHIS DROP CONSTRAINT INXU1_PER_DECORATEHIS ";
			elseif($DPISDB=="oci8") 
				$cmd = " DROP INDEX INXU1_PER_DECORATEHIS ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX INXU1_PER_DECORATEHIS ON PER_DECORATEHIS (PER_ID, DC_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_PERFORMANCE_GOALS ALTER KPI_WEIGHT NUMBER ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PERFORMANCE_GOALS MODIFY KPI_WEIGHT NUMBER(6,2) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_PERFORMANCE_GOALS MODIFY KPI_WEIGHT DECIMAL(6,2) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " SELECT COM_GROUP FROM PER_COMGROUP ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) {
				if($DPISDB=="odbc") 
					$cmd = " CREATE TABLE PER_COMGROUP(
					COM_GROUP VARCHAR(10) NOT NULL,	
					CG_NAME VARCHAR(100) NOT NULL,
					CG_ACTIVE SINGLE NOT NULL,
					UPDATE_USER INTEGER NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CG_SEQ_NO INTEGER2 NULL,
					CONSTRAINT PK_PER_COMGROUP PRIMARY KEY (COM_GROUP)) ";
				elseif($DPISDB=="oci8") 
					$cmd = " CREATE TABLE PER_COMGROUP(
					COM_GROUP VARCHAR2(10) NOT NULL,	
					CG_NAME VARCHAR2(100) NOT NULL,
					CG_ACTIVE NUMBER(1) NOT NULL,
					UPDATE_USER NUMBER(11) NOT NULL,
					UPDATE_DATE VARCHAR2(19) NOT NULL,		
					CG_SEQ_NO NUMBER(5) NULL,
					CONSTRAINT PK_PER_COMGROUP PRIMARY KEY (COM_GROUP)) ";
				elseif($DPISDB=="mysql")
					$cmd = " CREATE TABLE PER_COMGROUP(
					COM_GROUP VARCHAR(10) NOT NULL,	
					CG_NAME VARCHAR(100) NOT NULL,
					CG_ACTIVE SMALLINT(1) NOT NULL,
					UPDATE_USER INTEGER(11) NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CG_SEQ_NO SMALLINT(5) NULL,
					CONSTRAINT PK_PER_COMGROUP PRIMARY KEY (COM_GROUP)) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMGROUP (COM_GROUP, CG_NAME, CG_ACTIVE, UPDATE_USER, UPDATE_DATE, CG_SEQ_NO)
								  VALUES ('01', 'บรรจุ', 1, $SESS_USERID, '$UPDATE_DATE', 91) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			
				$cmd = " INSERT INTO PER_COMGROUP (COM_GROUP, CG_NAME, CG_ACTIVE, UPDATE_USER, UPDATE_DATE, CG_SEQ_NO)
								  VALUES ('02', 'ย้าย', 1, $SESS_USERID, '$UPDATE_DATE', 92) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			
				$cmd = " INSERT INTO PER_COMGROUP (COM_GROUP, CG_NAME, CG_ACTIVE, UPDATE_USER, UPDATE_DATE, CG_SEQ_NO)
								  VALUES ('03', 'เลื่อนระดับ', 1, $SESS_USERID, '$UPDATE_DATE', 93) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			
				$cmd = " INSERT INTO PER_COMGROUP (COM_GROUP, CG_NAME, CG_ACTIVE, UPDATE_USER, UPDATE_DATE, CG_SEQ_NO)
								  VALUES ('04', 'เลื่อนตำแหน่ง', 1, $SESS_USERID, '$UPDATE_DATE', 94) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			
				$cmd = " INSERT INTO PER_COMGROUP (COM_GROUP, CG_NAME, CG_ACTIVE, UPDATE_USER, UPDATE_DATE, CG_SEQ_NO)
								  VALUES ('05', 'เลื่อนขั้นเงินเดือน', 1, $SESS_USERID, '$UPDATE_DATE', 95) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			
				$cmd = " INSERT INTO PER_COMGROUP (COM_GROUP, CG_NAME, CG_ACTIVE, UPDATE_USER, UPDATE_DATE, CG_SEQ_NO)
								  VALUES ('06', 'ออกจากราชการ', 1, $SESS_USERID, '$UPDATE_DATE', 96) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			
				$cmd = " INSERT INTO PER_COMGROUP (COM_GROUP, CG_NAME, CG_ACTIVE, UPDATE_USER, UPDATE_DATE, CG_SEQ_NO)
								  VALUES ('07', 'รักษาราชการ', 1, $SESS_USERID, '$UPDATE_DATE', 97) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			
				$cmd = " INSERT INTO PER_COMGROUP (COM_GROUP, CG_NAME, CG_ACTIVE, UPDATE_USER, UPDATE_DATE, CG_SEQ_NO)
								  VALUES ('08', 'ยกเลิก/แก้ไขคำสั่ง', 1, $SESS_USERID, '$UPDATE_DATE', 98) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			
				$cmd = " INSERT INTO PER_COMGROUP (COM_GROUP, CG_NAME, CG_ACTIVE, UPDATE_USER, UPDATE_DATE, CG_SEQ_NO)
								  VALUES ('09', 'ลงโทษ', 1, $SESS_USERID, '$UPDATE_DATE', 99) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			
				$cmd = " INSERT INTO PER_COMGROUP (COM_GROUP, CG_NAME, CG_ACTIVE, UPDATE_USER, UPDATE_DATE, CG_SEQ_NO)
								  VALUES ('501', 'คำสั่งบรรจุ', 1, $SESS_USERID, '$UPDATE_DATE', 1) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			
				$cmd = " INSERT INTO PER_COMGROUP (COM_GROUP, CG_NAME, CG_ACTIVE, UPDATE_USER, UPDATE_DATE, CG_SEQ_NO)
								  VALUES ('502', 'คำสั่งย้าย', 1, $SESS_USERID, '$UPDATE_DATE', 2) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			
				$cmd = " INSERT INTO PER_COMGROUP (COM_GROUP, CG_NAME, CG_ACTIVE, UPDATE_USER, UPDATE_DATE, CG_SEQ_NO)
								  VALUES ('503', 'คำสั่งรับโอน', 1, $SESS_USERID, '$UPDATE_DATE', 3) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			
				$cmd = " INSERT INTO PER_COMGROUP (COM_GROUP, CG_NAME, CG_ACTIVE, UPDATE_USER, UPDATE_DATE, CG_SEQ_NO)
								  VALUES ('504', 'คำสั่งเลื่อน', 1, $SESS_USERID, '$UPDATE_DATE', 4) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			
				$cmd = " INSERT INTO PER_COMGROUP (COM_GROUP, CG_NAME, CG_ACTIVE, UPDATE_USER, UPDATE_DATE, CG_SEQ_NO)
								  VALUES ('505', 'คำสั่งแต่งตั้งข้าราชการให้ดำรงตำแหน่งประเภทบริหาร', 1, $SESS_USERID, '$UPDATE_DATE', 5) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			
				$cmd = " INSERT INTO PER_COMGROUP (COM_GROUP, CG_NAME, CG_ACTIVE, UPDATE_USER, UPDATE_DATE, CG_SEQ_NO)
								  VALUES ('506', 'คำสั่งให้ข้าราชการได้รับเงินเดือนตามคุณวุฒิ', 1, $SESS_USERID, '$UPDATE_DATE', 6) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			
				$cmd = " INSERT INTO PER_COMGROUP (COM_GROUP, CG_NAME, CG_ACTIVE, UPDATE_USER, UPDATE_DATE, CG_SEQ_NO)
								  VALUES ('507', 'คำสั่งเลื่อนขั้นเงินเดือน', 1, $SESS_USERID, '$UPDATE_DATE', 7) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			
				$cmd = " INSERT INTO PER_COMGROUP (COM_GROUP, CG_NAME, CG_ACTIVE, UPDATE_USER, UPDATE_DATE, CG_SEQ_NO)
								  VALUES ('508', 'คำสั่งให้ข้าราชการได้รับเงินเดือน', 1, $SESS_USERID, '$UPDATE_DATE', 8) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			
				$cmd = " INSERT INTO PER_COMGROUP (COM_GROUP, CG_NAME, CG_ACTIVE, UPDATE_USER, UPDATE_DATE, CG_SEQ_NO)
								  VALUES ('509', 'คำสั่งให้ข้าราชการรักษาราชการแทน', 1, $SESS_USERID, '$UPDATE_DATE', 9) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			
				$cmd = " INSERT INTO PER_COMGROUP (COM_GROUP, CG_NAME, CG_ACTIVE, UPDATE_USER, UPDATE_DATE, CG_SEQ_NO)
								  VALUES ('510', 'คำสั่งให้ข้าราชการรักษาการในตำแหน่ง', 1, $SESS_USERID, '$UPDATE_DATE', 10) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			
				$cmd = " INSERT INTO PER_COMGROUP (COM_GROUP, CG_NAME, CG_ACTIVE, UPDATE_USER, UPDATE_DATE, CG_SEQ_NO)
								  VALUES ('511', 'คำสั่งให้ออกจากราชการและการอนุญาตให้ข้าราชการลาออกจากราชการ', 1, $SESS_USERID, '$UPDATE_DATE', 11) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			
				$cmd = " INSERT INTO PER_COMGROUP (COM_GROUP, CG_NAME, CG_ACTIVE, UPDATE_USER, UPDATE_DATE, CG_SEQ_NO)
								  VALUES ('512', 'คำสั่งเพื่อแก้ไขคำสั่งที่ผิดพลาด', 1, $SESS_USERID, '$UPDATE_DATE', 12) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			
				$cmd = " INSERT INTO PER_COMGROUP (COM_GROUP, CG_NAME, CG_ACTIVE, UPDATE_USER, UPDATE_DATE, CG_SEQ_NO)
								  VALUES ('513', 'คำสั่งยกเลิกคำสั่งเดิม', 1, $SESS_USERID, '$UPDATE_DATE', 13) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			
				$cmd = " INSERT INTO PER_COMGROUP (COM_GROUP, CG_NAME, CG_ACTIVE, UPDATE_USER, UPDATE_DATE, CG_SEQ_NO)
								  VALUES ('514', 'คำสั่งตามมาตรการปรับปรุงอัตรากำลังของส่วนราชการ (โครงการเกษียณอายุก่อนกำหนด)', 1, $SESS_USERID, '$UPDATE_DATE', 14) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			
				$cmd = " INSERT INTO PER_COMGROUP (COM_GROUP, CG_NAME, CG_ACTIVE, UPDATE_USER, UPDATE_DATE, CG_SEQ_NO)
								  VALUES ('521', 'คำสั่งมอบหมายให้ปฏิบัติราชการ/ปฏิบัติราชการแทน', 1, $SESS_USERID, '$UPDATE_DATE', 21) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			
				$cmd = " INSERT INTO PER_COMGROUP (COM_GROUP, CG_NAME, CG_ACTIVE, UPDATE_USER, UPDATE_DATE, CG_SEQ_NO)
								  VALUES ('522', 'คำสั่งช่วยราชการ', 1, $SESS_USERID, '$UPDATE_DATE', 22) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			
				if($DPISDB=="odbc") 
					$cmd = " ALTER TABLE PER_COMTYPE ALTER COM_GROUP CHAR(10) ";
				elseif($DPISDB=="oci8") 
					$cmd = " ALTER TABLE PER_COMTYPE MODIFY COM_GROUP CHAR(10) ";
				elseif($DPISDB=="mysql") 
					$cmd = " ALTER TABLE PER_COMTYPE MODIFY COM_GROUP CHAR(10) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			}

			$cmd = " UPDATE PER_TRAINING SET TRN_PASS = 1 WHERE TRN_PASS IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE  PER_COMTYPE DROP CONSTRAINT INXU1_PER_COMTYPE ";
			elseif($DPISDB=="oci8") 
				$cmd = " DROP INDEX INXU1_PER_COMTYPE ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX INXU1_PER_COMTYPE ON PER_COMTYPE (COM_NAME) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE  PER_COMTYPE DROP CONSTRAINT INXU2_PER_COMTYPE ";
			elseif($DPISDB=="oci8") 
				$cmd = " DROP INDEX INXU2_PER_COMTYPE ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX INXU2_PER_COMTYPE ON PER_COMTYPE (COM_DESC) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_COMTYPE SET COM_SEQ_NO = 102 WHERE COM_TYPE = '0101' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO, COM_ACTIVE)
							VALUES ('5011', 'คส. 1.1', 'คำสั่งบรรจุผู้สอบแข่งขันได้', '501', 2, 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO, COM_ACTIVE)
							VALUES ('5012', 'คส. 1.2', 'คำสั่งบรรจุผู้ได้รับคัดเลือก', '501', 4, 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO, COM_ACTIVE)
							VALUES ('5013', 'คส. 1.3', 'คำสั่งบรรจุตำแหน่งประเภทวิชาการ ระดับชำนาญการ ชำนาญการพิเศษ เชี่ยวชาญ และทรงคุณวุฒิ หรือตำแหน่งประเภททั่วไป ระดับทักษะพิเศษ', '501', 6, 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO, COM_ACTIVE)
							VALUES ('5014', 'คส. 1.4', 'คำสั่งรับโอนพนักงานส่วนท้องถิ่น และเจ้าหน้าที่ของหน่วยงานอื่นของรัฐ', '501', 8, 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO, COM_ACTIVE)
							VALUES ('5015', 'คส. 1.5', 'คำสั่งรับโอนพนักงานส่วนท้องถิ่น และเจ้าหน้าที่ของหน่วยงานอื่นของรัฐผู้สอบแข่งขันได้', '501', 10, 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO, COM_ACTIVE)
							VALUES ('5016', 'คส. 1.6', 'คำสั่งบรรจุผู้ออกไปรับราชการทหารกลับเข้ารับราชการ', '501', 12, 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO, COM_ACTIVE)
							VALUES ('5017', 'คส. 1.7', 'คำสั่งบรรจุผู้ไปปฏิบัติงานตามมติคณะรัฐมนตรีกลับเข้ารับราชการ', '501', 14, 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO, COM_ACTIVE)
							VALUES ('5018', 'คส. 1.8', 'คำสั่งบรรจุผู้เคยเป็นข้าราชการพลเรือนสามัญกลับเข้ารับราชการ', '501', 16, 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO, COM_ACTIVE)
							VALUES ('5019', 'คส. 1.9', 'คำสั่งบรรจุผู้เคยเป็นพนักงานส่วนท้องถิ่น หรือข้าราชการประเภทอื่นกลับเข้ารับราชการ', '501', 18, 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO, COM_ACTIVE)
							VALUES ('5021', 'คส. 2.1', 'คำสั่งย้าย', '502', 20, 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO, COM_ACTIVE)
							VALUES ('5022', 'คส. 2.2', 'คำสั่งย้ายข้าราชการพลเรือนสามัญซึ่งได้รับวุฒิเพิ่มขึ้น', '502', 22, 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO, COM_ACTIVE)
							VALUES ('5023', 'คส. 2.3', 'คำสั่งแต่งตั้งผู้มีคุณสมบัติไม่ตรงตามคุณสมบัติเฉพาะสำหรับตำแหน่งให้กลับไปดำรงตำแหน่งในประเภทเดิม ระดับเดิม', '502', 24, 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO, COM_ACTIVE)
							VALUES ('5031', 'คส. 3.1', 'คำสั่งรับโอนข้าราชการพลเรือนสามัญ', '503', 26, 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO, COM_ACTIVE)
							VALUES ('5032', 'คส. 3.2', 'คำสั่งรับโอนข้าราชการพลเรือนสามัญผู้ได้รับวุฒิเพิ่มขึ้น', '503', 28, 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO, COM_ACTIVE)
							VALUES ('5033', 'คส. 3.3', 'คำสั่งรับโอนข้าราชการพลเรือนสามัญมาดำรงตำแหน่งในระดับที่สูงขึ้น', '503', 30, 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO, COM_ACTIVE)
							VALUES ('5034', 'คส. 3.4', 'คำสั่งรับโอนข้าราชการพลเรือนสามัญผู้สอบแข่งขันได้', '503', 32, 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO, COM_ACTIVE)
							VALUES ('5035', 'คส. 3.5', 'คำสั่งให้โอน', '511', 34, 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO, COM_ACTIVE)
							VALUES ('5041', 'คส. 4.1', 'คำสั่งเลื่อนข้าราชการ', '504', 36, 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO, COM_ACTIVE)
							VALUES ('5051', 'คส. 5.1', 'คำสั่งย้ายไปดำรงตำแหน่งประเภทบริหาร', '505', 38, 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO, COM_ACTIVE)
							VALUES ('5052', 'คส. 5.2', 'คำสั่งรับโอนมาดำรงตำแหน่งประเภทบริหาร', '505', 40, 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO, COM_ACTIVE)
							VALUES ('5053', 'คส. 5.3', 'คำสั่งบรรจุผู้เคยเป็นข้าราชการพลเรือนสามัญกลับเข้ารับราชการให้ดำรงตำแหน่งประเภทบริหาร', '505', 42, 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO, COM_ACTIVE)
							VALUES ('5060', 'คส. 6', 'คำสั่งให้ข้าราชการได้รับเงินเดือนตามคุณวุฒิ (ปรับวุฒิในตำแหน่งเดิม)', '506', 44, 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO, COM_ACTIVE)
							VALUES ('5071', 'คส. 7.1', 'คำสั่งเลื่อนเงินเดือนข้าราชการ', '507', 46, 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO, COM_ACTIVE)
							VALUES ('5072', 'คส. 7.2', 'คำสั่งเลื่อนเงินเดือน (กรณีครบเกษียณอายุ)', '507', 48, 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO, COM_ACTIVE)
							VALUES ('5073', 'คส. 7.3', 'คำสั่งให้ได้รับเงินค่าตอบแทนพิเศษ', '507', 49, 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO, COM_ACTIVE)
							VALUES ('5081', 'คส. 8.1', 'คำสั่งให้ข้าราชการได้รับเงินเดือน', '508', 50, 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO, COM_ACTIVE)
							VALUES ('5090', 'คส. 9', 'คำสั่งให้ข้าราชการรักษาราชการแทน', '509', 52, 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO, COM_ACTIVE)
							VALUES ('5100', 'คส. 10', 'คำสั่งให้ข้าราชการรักษาการในตำแหน่ง', '510', 54, 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO, COM_ACTIVE)
							VALUES ('5111', 'คส. 11.1', 'คำสั่งอนุญาตให้ข้าราชการลาออกจากราชการ', '511', 56, 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO, COM_ACTIVE)
							VALUES ('5112', 'คส. 11.2', 'คำสั่งให้ข้าราชการออกจากราชการเพราะผลการทดลองปฏิบัติหน้าที่ราชการต่ำกว่าเกณฑ์หรือมาตรฐานที่กำหนด', '511', 58, 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO, COM_ACTIVE)
							VALUES ('5113', 'คส. 11.3', 'คำสั่งให้ข้าราชการออกจากราชการเพราะขาดคุณสมบัติทั่วไปหรือมีลักษณะต้องห้ามหรือขาดคุณสมบัติเฉพาะสำหรับตำแหน่ง', '511', 60, 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO, COM_ACTIVE)
							VALUES ('5114', 'คส. 11.4', 'คำสั่งให้ออกจากราชการเพื่อให้ได้รับบำเหน็จบำนาญเหตุทดแทน', '511', 62, 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO, COM_ACTIVE)
							VALUES ('5115', 'คส. 11.5', 'คำสั่งให้ออกจากราชการเพื่อไปรับราชการทหาร', '511', 64, 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO, COM_ACTIVE)
							VALUES ('5116', 'คส. 11.6', 'คำสั่งให้ข้าราชการไปปฏิบัติงานตามมติคณะรัฐมนตรี', '511', 66, 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO, COM_ACTIVE)
							VALUES ('5117', 'คส. 11.7', 'คำสั่งให้ข้าราชการออกจากราชการเพื่อไปปฏิบัติงานตามมติคณะรัฐมนตรี', '511', 68, 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO, COM_ACTIVE)
							VALUES ('5120', 'คส. 12', 'คำสั่งเพื่อแก้ไขคำสั่งที่ผิดพลาด', '512', 70, 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO, COM_ACTIVE)
							VALUES ('5130', 'คส. 13', 'คำสั่งยกเลิกคำสั่งเดิม', '513', 72, 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO, COM_ACTIVE)
							VALUES ('5141', 'คส. 14.1', 'คำสั่งอนุญาตให้ข้าราชการลาออกจากราชการตามมาตรการปรับปรุงอัตรากำลังของส่วนราชการ (โครงการเกษียณอายุก่อนกำหนด) ปีงบประมาณ พ.ศ.2553', '514', 74, 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO, COM_ACTIVE)
							VALUES ('5142', 'คส. 14.2', 'คำสั่งเลื่อนเงินเดือนข้าราชการผู้เข้าร่วมมาตรการปรับปรุงอัตรากำลังของส่วนราชการ (โครงการเกษียณอายุก่อนกำหนด) ปีงบประมาณ พ.ศ.2553', '514', 76, 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO, COM_ACTIVE)
							VALUES ('5211', 'คส. 21.1', 'คำสั่งมอบหมายให้ปฏิบัติราชการ', '521', 92, 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO, COM_ACTIVE)
							VALUES ('5212', 'คส. 21.2', 'คำสั่งปฏิบัติราชการแทน', '521', 94, 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO, COM_ACTIVE)
							VALUES ('5220', 'คส. 22', 'คำสั่งช่วยราชการ', '522', 96, 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 
							  WHERE LINKTO_WEB = 'master_table.html?table=PER_COMGROUP' ";
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
								  VALUES (1, 'TH', $MAX_ID, 15, 'M0315 แบบคำสั่ง', 'S', 'W', 'master_table.html?table=PER_COMGROUP', 0, 9, 295, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 15, 'M0315 แบบคำสั่ง', 'S', 'W', 'master_table.html?table=PER_COMGROUP', 0, 9, 295, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST ALTER CP_BUDGET NUMBER ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST MODIFY CP_BUDGET NUMBER(16,2) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST MODIFY CP_BUDGET DECIMAL(16,2) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_COMPENSATION_TEST_DTL", "CD_MIDPOINT","NUMBER", "16,2", "NULL");

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE  PER_COURSE DROP CONSTRAINT INXU1_PER_COURSE ";
			elseif($DPISDB=="oci8") 
				$cmd = " DROP INDEX INXU1_PER_COURSE ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX INXU1_PER_COURSE ON PER_COURSE (TR_CODE, CO_NO) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_KPI", "KPI_RESULT","MEMO", "2000", "NULL");
			add_field("PER_PERFORMANCE_GOALS", "PG_REMARK","MEMO", "2000", "NULL");

			$cmd = " UPDATE PER_TRAINING SET TRN_PASS =  1 WHERE TRN_PASS IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_COMDTL", "CMD_MIDPOINT","NUMBER", "16,2", "NULL");

			$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('113', 'มอบหมายให้ปฏิบัติราชการ/ปฏิบัติราชการแทน', 1, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('11310', 'มอบหมายให้ปฏิบัติราชการ', 1, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('11320', 'ปฏิบัติราชการแทน', 1, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE  PER_MARRHIS DROP CONSTRAINT INXU1_PER_MARRHIS ";
			elseif($DPISDB=="oci8") 
				$cmd = " DROP INDEX INXU1_PER_MARRHIS ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX INXU1_PER_MARRHIS ON PER_MARRHIS (PER_ID, MAH_SEQ) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " SELECT ACTH_ID FROM PER_ACTINGHIS ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) {
				if($DPISDB=="odbc") 
					$cmd = " CREATE TABLE PER_ACTINGHIS(
					ACTH_ID INTEGER NOT NULL,	
					PER_ID INTEGER NOT NULL,	
					PER_CARDNO VARCHAR(13) NULL,
					ACTH_EFFECTIVEDATE VARCHAR(19) NOT NULL,
					MOV_CODE VARCHAR(10) NOT NULL,
					ACTH_ENDDATE VARCHAR(19) NULL,
					ACTH_DOCNO VARCHAR(255) NULL,
					ACTH_DOCDATE VARCHAR(19) NULL,
					ACTH_POS_NO VARCHAR(15) NULL,
					PM_CODE VARCHAR(10) NULL,
					ACTH_PM_NAME VARCHAR(255) NULL,
					LEVEL_NO VARCHAR(10) NULL,
					PL_CODE VARCHAR(10) NULL,
					ACTH_PL_NAME VARCHAR(255) NULL,
					ACTH_ORG1 VARCHAR(255) NULL,
					ACTH_ORG2 VARCHAR(255) NULL,
					ACTH_ORG3 VARCHAR(255) NULL,
					ACTH_ORG4 VARCHAR(255) NULL,
					ACTH_ORG5 VARCHAR(255) NULL,
					ACTH_POS_NO_ASSIGN VARCHAR(15) NULL,
					PM_CODE_ASSIGN VARCHAR(10) NULL,
					ACTH_PM_NAME_ASSIGN VARCHAR(255) NULL,
					LEVEL_NO_ASSIGN VARCHAR(10) NULL,
					PL_CODE_ASSIGN VARCHAR(10) NULL,
					ACTH_PL_NAME_ASSIGN VARCHAR(255) NULL,
					ACTH_ORG1_ASSIGN VARCHAR(255) NULL,
					ACTH_ORG2_ASSIGN VARCHAR(255) NULL,
					ACTH_ORG3_ASSIGN VARCHAR(255) NULL,
					ACTH_ORG4_ASSIGN VARCHAR(255) NULL,
					ACTH_ORG5_ASSIGN VARCHAR(255) NULL,
					ACTH_ASSIGN MEMO NULL,
					ACTH_REMARK MEMO NULL,
					ACTH_SEQ_NO INTEGER2 NULL,
					UPDATE_USER INTEGER NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_ACTINGHIS PRIMARY KEY (ACTH_ID)) ";
				elseif($DPISDB=="oci8") 
					$cmd = " CREATE TABLE PER_ACTINGHIS(
					ACTH_ID NUMBER(10) NOT NULL,	
					PER_ID NUMBER(10) NOT NULL,	
					PER_CARDNO VARCHAR2(13) NULL,
					ACTH_EFFECTIVEDATE VARCHAR2(19) NOT NULL,
					MOV_CODE VARCHAR2(10) NOT NULL,
					ACTH_ENDDATE VARCHAR2(19) NULL,
					ACTH_DOCNO VARCHAR2(255) NULL,
					ACTH_DOCDATE VARCHAR2(19) NULL,
					ACTH_POS_NO VARCHAR2(15) NULL,
					PM_CODE VARCHAR2(10) NULL,
					ACTH_PM_NAME VARCHAR2(255) NULL,
					LEVEL_NO VARCHAR2(10) NULL,
					PL_CODE VARCHAR2(10) NULL,
					ACTH_PL_NAME VARCHAR2(255) NULL,
					ACTH_ORG1 VARCHAR2(255) NULL,
					ACTH_ORG2 VARCHAR2(255) NULL,
					ACTH_ORG3 VARCHAR2(255) NULL,
					ACTH_ORG4 VARCHAR2(255) NULL,
					ACTH_ORG5 VARCHAR2(255) NULL,
					ACTH_POS_NO_ASSIGN VARCHAR2(15) NULL,
					PM_CODE_ASSIGN VARCHAR2(10) NULL,
					ACTH_PM_NAME_ASSIGN VARCHAR2(255) NULL,
					LEVEL_NO_ASSIGN VARCHAR2(10) NULL,
					PL_CODE_ASSIGN VARCHAR2(10) NULL,
					ACTH_PL_NAME_ASSIGN VARCHAR2(255) NULL,
					ACTH_ORG1_ASSIGN VARCHAR2(255) NULL,
					ACTH_ORG2_ASSIGN VARCHAR2(255) NULL,
					ACTH_ORG3_ASSIGN VARCHAR2(255) NULL,
					ACTH_ORG4_ASSIGN VARCHAR2(255) NULL,
					ACTH_ORG5_ASSIGN VARCHAR2(255) NULL,
					ACTH_ASSIGN VARCHAR2(1000) NULL,
					ACTH_REMARK VARCHAR2(1000) NULL,
					ACTH_SEQ_NO NUMBER(5) NULL,
					UPDATE_USER NUMBER(11) NOT NULL,
					UPDATE_DATE VARCHAR2(19) NOT NULL,		
					CONSTRAINT PK_PER_ACTINGHIS PRIMARY KEY (ACTH_ID)) ";
				elseif($DPISDB=="mysql")
					$cmd = " CREATE TABLE PER_ACTINGHIS(
					ACTH_ID INTEGER(10) NOT NULL,	
					PER_ID INTEGER(10) NOT NULL,	
					PER_CARDNO VARCHAR(13) NULL,
					ACTH_EFFECTIVEDATE VARCHAR(19) NOT NULL,
					MOV_CODE VARCHAR(10) NOT NULL,
					ACTH_ENDDATE VARCHAR(19) NULL,
					ACTH_DOCNO VARCHAR(255) NULL,
					ACTH_DOCDATE VARCHAR(19) NULL,
					ACTH_POS_NO VARCHAR(15) NULL,
					PM_CODE VARCHAR(10) NULL,
					ACTH_PM_NAME VARCHAR(255) NULL,
					LEVEL_NO VARCHAR(10) NULL,
					PL_CODE VARCHAR(10) NULL,
					ACTH_PL_NAME VARCHAR(255) NULL,
					ACTH_ORG1 VARCHAR(255) NULL,
					ACTH_ORG2 VARCHAR(255) NULL,
					ACTH_ORG3 VARCHAR(255) NULL,
					ACTH_ORG4 VARCHAR(255) NULL,
					ACTH_ORG5 VARCHAR(255) NULL,
					ACTH_POS_NO_ASSIGN VARCHAR(15) NULL,
					PM_CODE_ASSIGN VARCHAR(10) NULL,
					ACTH_PM_NAME_ASSIGN VARCHAR(255) NULL,
					LEVEL_NO_ASSIGN VARCHAR(10) NULL,
					PL_CODE_ASSIGN VARCHAR(10) NULL,
					ACTH_PL_NAME_ASSIGN VARCHAR(255) NULL,
					ACTH_ORG1_ASSIGN VARCHAR(255) NULL,
					ACTH_ORG2_ASSIGN VARCHAR(255) NULL,
					ACTH_ORG3_ASSIGN VARCHAR(255) NULL,
					ACTH_ORG4_ASSIGN VARCHAR(255) NULL,
					ACTH_ORG5_ASSIGN VARCHAR(255) NULL,
					ACTH_ASSIGN TEXT NULL,
					ACTH_REMARK TEXT NULL,
					ACTH_SEQ_NO SMALLINT(5) NULL,
					UPDATE_USER INTEGER(11) NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_ACTINGHIS PRIMARY KEY (ACTH_ID)) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			}

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_PUNISHMENT ALTER PUN_ENDDATE NULL ";
			elseif($DPISDB=="oci8" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_PUNISHMENT MODIFY PUN_ENDDATE NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_WORKFLOW_PUNISHMENT ALTER PUN_ENDDATE NULL ";
			elseif($DPISDB=="oci8" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_WORKFLOW_PUNISHMENT MODIFY PUN_ENDDATE NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST ALTER SF_CODE_O MEMO ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST MODIFY SF_CODE_O VARCHAR2(2000) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST MODIFY SF_CODE_O TEXT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST ALTER SF_CODE_K MEMO ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST MODIFY SF_CODE_K VARCHAR2(2000) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST MODIFY SF_CODE_K TEXT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST ALTER SF_CODE_D MEMO ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST MODIFY SF_CODE_D VARCHAR2(2000) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST MODIFY SF_CODE_D TEXT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST ALTER SF_CODE_M MEMO ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST MODIFY SF_CODE_M VARCHAR2(2000) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST MODIFY SF_CODE_M TEXT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST ALTER SUM_QTY MEMO ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST MODIFY SUM_QTY VARCHAR2(2000) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST MODIFY SUM_QTY TEXT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST ALTER SUM_SALARY MEMO ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST MODIFY SUM_SALARY VARCHAR2(2000) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST MODIFY SUM_SALARY TEXT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, 
							  UPDATE_DATE)
							  VALUES ('21415', 'เงินเดือนเต็มขั้น ได้เงินค่าตอบแทนพิเศษ', 2, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 
							  WHERE LINKTO_WEB = 'data_cancel_comdtl.html' ";
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
								  VALUES (1, 'TH', $MAX_ID, 1, 'P1701 บัญชีแนบท้ายคำสั่งยกเลิกคำสั่งเดิม', 'S', 'W', 'data_cancel_comdtl.html', 0, 35, 404, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 1, 'P1701 บัญชีแนบท้ายคำสั่งยกเลิกคำสั่งเดิม', 'S', 'W', 'data_cancel_comdtl.html', 0, 35, 404, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 
							  WHERE LINKTO_WEB = 'data_edit_comdtl.html' ";
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
								  VALUES (1, 'TH', $MAX_ID, 2, 'P1702 บัญชีแนบท้ายคำสั่งแก้ไขคำสั่งที่ผิดพลาด', 'S', 'W', 'data_edit_comdtl.html', 0, 35,
								  404, $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
	
				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 2, 'P1702 บัญชีแนบท้ายคำสั่งแก้ไขคำสั่งที่ผิดพลาด', 'S', 'W', 'data_edit_comdtl.html', 0, 35,
								  404, $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_R006000.html' ";
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
								  VALUES (1, 'TH', $MAX_ID, 0, 'R0600 รายงานการเลื่อนเงินเดือนรายบุคคล', 'S', 'W', 'rpt_R006000.html', 0, 36, 238, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 0, 'R0600 รายงานการเลื่อนเงินเดือนรายบุคคล', 'S', 'W', 'rpt_R006000.html', 0, 36, 238, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			add_field("PER_SALARYHIS", "SAH_SALARY_MIDPOINT","NUMBER", "16,2", "NULL");
			add_field("PER_POSITION", "POS_ORGMGT","VARCHAR", "255", "NULL");
			add_field("PER_PERSONAL", "PER_DOCNO","VARCHAR", "255", "NULL");
			add_field("PER_PERSONAL", "PER_DOCDATE","VARCHAR", "19", "NULL");
			add_field("PER_COMMAND", "COM_STATUS","CHAR", "1", "NULL");

			$cmd = " CREATE INDEX IDX_PER_EDUCATE_1 ON PER_EDUCATE (PER_ID, EDU_TYPE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_ORG_1 ON PER_ORG (ORG_ID, ORG_ID_REF) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_ORG_ASS_1 ON PER_ORG_ASS (DEPARTMENT_ID, OL_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_PERSONAL_1 ON PER_PERSONAL (POS_ID, PER_STATUS) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_PERSONAL_2 ON PER_PERSONAL (POS_ID, ORG_ID, PER_STATUS) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_PERSONAL_3 ON PER_PERSONAL (POS_ID, PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_PERSONAL_4 ON PER_PERSONAL (POEM_ID, PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_PERSONAL_5 ON PER_PERSONAL (POS_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_PERSONAL_6 ON PER_PERSONAL (POEM_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_PERSONAL_7 ON PER_PERSONAL (POS_ID, PN_CODE, PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_PERSONAL_8 ON PER_PERSONAL (POEM_ID, PN_CODE, PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_POS_EMP_1 ON PER_POS_EMP (POEM_ID, ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_POS_EMPSER_1 ON PER_POS_EMPSER (POEMS_ID, ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_POSITION_1 ON PER_POSITION (ORG_ID, CL_NAME, POS_ID, DEPARTMENT_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_POSITION_2 ON PER_POSITION (ORG_ID, CL_NAME, POS_GET_DATE, DEPARTMENT_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_POSITION_3 ON PER_POSITION (POS_ID, POS_STATUS, DEPARTMENT_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_POSITION_4 ON PER_POSITION (ORG_ID, CL_NAME, POS_ID, POS_GET_DATE, DEPARTMENT_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_POSITION_5 ON PER_POSITION (ORG_ID, CL_NAME) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_POSITION_6 ON PER_POSITION (POS_ID, ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_POSITIONHIS_1 ON PER_POSITIONHIS (PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_POSITIONHIS_2 ON PER_POSITIONHIS (PER_ID, PL_CODE, PT_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_POSITIONHIS_3 ON PER_POSITIONHIS (PER_ID, PN_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_POSITIONHIS_4 ON PER_POSITIONHIS (PER_ID, EP_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_POSITIONHIS_5 ON PER_POSITIONHIS (PER_ID, POH_EFFECTIVEDATE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_SALARYHIS_1 ON PER_SALARYHIS (PER_ID, SAH_EFFECTIVEDATE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_COURSE ALTER CO_NO VARCHAR(15) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COURSE MODIFY CO_NO VARCHAR2(15) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_COURSE MODIFY CO_NO VARCHAR(15) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_COURSE", "CO_PROJECT_NAME","VARCHAR", "255", "NULL");

			$cmd = " ALTER TABLE user_detail ADD titlename varchar(50) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			add_field("PER_LEVEL", "POSITION_TYPE","VARCHAR", "100", "NULL");
			add_field("PER_LEVEL", "POSITION_LEVEL","VARCHAR", "100", "NULL");
			add_field("PER_COMDTL", "PL_NAME_WORK","VARCHAR", "255", "NULL");
			add_field("PER_COMDTL", "ORG_NAME_WORK","VARCHAR", "255", "NULL");

			$cmd = " CREATE INDEX IDX_PER_KPI_FORM ON PER_KPI_FORM (KF_CYCLE, KF_START_DATE, KF_END_DATE, DEPARTMENT_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_KPI_FORM_1 ON PER_KPI_FORM (PER_ID, KF_CYCLE, KF_START_DATE, KF_END_DATE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_SQL ALTER SQL_CMD MEMO ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SQL MODIFY SQL_CMD VARCHAR2(2000) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_SQL MODIFY SQL_CMD TEXT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_SALARYHIS", "SAH_KF_YEAR","VARCHAR", "4", "NULL");
			add_field("PER_SALARYHIS", "SAH_KF_CYCLE","SINGLE", "1", "NULL");
			add_field("PER_SALARYHIS", "SAH_TOTAL_SCORE","NUMBER", "5,2", "NULL");

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'structure_by_law_per.html' ";
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
								  VALUES (1, 'TH', $MAX_ID, 4, 'S0104 โครงสร้างการบังคับบัญชาตามกฏหมาย', 'S', 'W', 'structure_by_law_per.html', 0, 34, 253, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 4, 'S0104 โครงสร้างการบังคับบัญชาตามกฏหมาย', 'S', 'W', 'structure_by_law_per.html', 0, 34, 253, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'structure_by_assign_per.html' ";
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
								  VALUES (1, 'TH', $MAX_ID, 5, 'S0105 โครงสร้างการบังคับบัญชาตามมอบหมายงาน', 'S', 'W', 'structure_by_assign_per.html', 0, 34, 253, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 5, 'S0105 โครงสร้างการบังคับบัญชาตามมอบหมายงาน', 'S', 'W', 'structure_by_assign_per.html', 0, 34, 253, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " UPDATE PER_POSITION SET PAY_NO = POS_NO WHERE PAY_NO IS NULL OR PAY_NO = 'NULL' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_COMDTL ALTER CMD_PERCENT NUMBER ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COMDTL MODIFY CMD_PERCENT NUMBER(7,4) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_COMDTL MODIFY CMD_PERCENT DECIMAL(7,4) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_ASSESS_LEVEL ALTER AL_PERCENT NUMBER ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_ASSESS_LEVEL MODIFY AL_PERCENT NUMBER(7,4) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_ASSESS_LEVEL MODIFY AL_PERCENT DECIMAL(7,4) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST_DTL ALTER CD_PERCENT NUMBER ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST_DTL MODIFY CD_PERCENT NUMBER(7,4) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST_DTL MODIFY CD_PERCENT DECIMAL(7,4) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_SALARYHIS ALTER SAH_PERCENT_UP NUMBER ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SALARYHIS MODIFY SAH_PERCENT_UP NUMBER(7,4) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_SALARYHIS MODIFY SAH_PERCENT_UP DECIMAL(7,4) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_COMPENSATION_TEST_DTL", "CD_OLD_SALARY","NUMBER", "16,2", "NULL");

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'dpis_to_text.html' ";
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
								  VALUES (1, 'TH', $MAX_ID, 12, 'C12 ถ่ายโอนข้อมูลกรม -> จังหวัด', 'S', 'W', 'dpis_to_text.html', 0, 1, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
								  UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 12, 'C12 ถ่ายโอนข้อมูลกรม -> จังหวัด', 'S', 'W', 'dpis_to_text.html', 0, 1, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'text_to_ppis.html' ";
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
								  VALUES (1, 'TH', $MAX_ID, 13, 'C13 รับโอนข้อมูลจากกรม', 'S', 'W', 'text_to_ppis.html', 0, 1, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
								  UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 13, 'C13 รับโอนข้อมูลจากกรม', 'S', 'W', 'text_to_ppis.html', 0, 1, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE  PER_PRENAME DROP CONSTRAINT INXU2_PER_PRENAME ";
			elseif($DPISDB=="oci8") 
				$cmd = " DROP INDEX INXU2_PER_PRENAME ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_ABILITY", "ABI_REMARK","MEMO", "2000", "NULL");
			add_field("PER_WORKFLOW_ABILITY", "ABI_REMARK","MEMO", "2000", "NULL");
			add_field("PER_SPECIAL_SKILL", "SPS_REMARK","MEMO", "2000", "NULL");
			add_field("PER_WORKFLOW_SPECIAL_SKILL", "SPS_REMARK","MEMO", "2000", "NULL");
			add_field("PER_HEIR", "HEIR_REMARK","MEMO", "2000", "NULL");
			add_field("PER_WORKFLOW_HEIR", "HEIR_REMARK","MEMO", "2000", "NULL");
			add_field("PER_ABSENTHIS", "ABS_REMARK","MEMO", "2000", "NULL");
			add_field("PER_WORKFLOW_ABSENTHIS", "ABS_REMARK","MEMO", "2000", "NULL");
			add_field("PER_PUNISHMENT", "PUN_REMARK","MEMO", "2000", "NULL");
			add_field("PER_WORKFLOW_PUNISHMENT", "PUN_REMARK","MEMO", "2000", "NULL");
			add_field("PER_COMDTL", "CMD_EDUCATE","VARCHAR", "100", "NULL");
			add_field("PER_POSITION", "LEVEL_NO","VARCHAR", "10", "NULL");
			add_field("PER_PERSONAL", "PER_EFFECTIVEDATE","VARCHAR", "19", "NULL");
			add_field("PER_PERSONAL", "PER_POS_REASON","VARCHAR", "255", "NULL");
			add_field("PER_PERSONAL", "PER_POS_YEAR","VARCHAR", "4", "NULL");
			add_field("PER_PERSONAL", "PER_POS_DOCTYPE","VARCHAR", "255", "NULL");
			add_field("PER_PERSONAL", "PER_POS_DOCNO","VARCHAR", "255", "NULL");
			add_field("PER_PERSONAL", "PER_POS_ORG","VARCHAR", "255", "NULL");
			add_field("PER_PERSONAL", "PER_ORDAIN_DETAIL","VARCHAR", "255", "NULL");
			add_field("PER_POSITIONHIS", "POH_LAST_POSITION","CHAR", "1", "NULL");
			add_field("PER_SALARYHIS", "SAH_LAST_SALARY","CHAR", "1", "NULL");

			$cmd = " SELECT SM_CODE FROM PER_SALARY_MOVMENT ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) {
				if($DPISDB=="odbc") 
					$cmd = " CREATE TABLE PER_SALARY_MOVMENT(
					SM_CODE VARCHAR(10) NOT NULL,	
					SM_NAME VARCHAR(100) NOT NULL,
					SM_REMARK VARCHAR(255) NULL,
					SM_FACTOR NUMBER NULL,
					SM_ACTIVE SINGLE NOT NULL,
					UPDATE_USER INTEGER NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					SM_SEQ_NO INTEGER2 NULL,
					CONSTRAINT PK_PER_SALARY_MOVMENT PRIMARY KEY (SM_CODE)) ";
				elseif($DPISDB=="oci8") 
					$cmd = " CREATE TABLE PER_SALARY_MOVMENT(
					SM_CODE VARCHAR2(10) NOT NULL,	
					SM_NAME VARCHAR2(100) NOT NULL,
					SM_REMARK VARCHAR2(255) NULL,
					SM_FACTOR NUMBER(5,2) NULL,
					SM_ACTIVE NUMBER(1) NOT NULL,
					UPDATE_USER NUMBER(11) NOT NULL,
					UPDATE_DATE VARCHAR2(19) NOT NULL,		
					SM_SEQ_NO NUMBER(5) NULL,
					CONSTRAINT PK_PER_SALARY_MOVMENT PRIMARY KEY (SM_CODE)) ";
				elseif($DPISDB=="mysql")
					$cmd = " CREATE TABLE PER_SALARY_MOVMENT(
					SM_CODE VARCHAR(10) NOT NULL,	
					SM_NAME VARCHAR(100) NOT NULL,
					SM_REMARK VARCHAR(255) NULL,
					SM_FACTOR DECIMAL(5,2) NULL,
					SM_ACTIVE SMALLINT(1) NOT NULL,
					UPDATE_USER INTEGER(11) NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					SM_SEQ_NO SMALLINT(5) NULL,
					CONSTRAINT PK_PER_SALARY_MOVMENT PRIMARY KEY (SM_CODE)) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				add_field("PER_SALARYHIS", "SM_CODE","VARCHAR", "10", "NULL");

				$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 
								  WHERE LINKTO_WEB = 'master_table_salary_movment.html?table=PER_SALARY_MOVMENT' ";
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
									  VALUES (1, 'TH', $MAX_ID, 8, 'M0408 จำนวนขั้นเงินเดือน', 'S', 'W', 'master_table_salary_movment.html?table=PER_SALARY_MOVMENT', 0, 9, 
									  297, $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();

					$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
									  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
									  CREATE_BY, UPDATE_DATE, UPDATE_BY)
									  VALUES (1, 'EN', $MAX_ID, 8, 'M0408 จำนวนขั้นเงินเดือน', 'S', 'W', 'master_table_salary_movment.html?table=PER_SALARY_MOVMENT', 0, 9, 
									  297, $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();
				}

				$cmd = " INSERT INTO PER_SALARY_MOVMENT (SM_CODE, SM_NAME, SM_REMARK, SM_FACTOR, SM_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE, SM_SEQ_NO)
								  VALUES ('1', 'ครึ่งขั้น', 'เลื่อนขั้นเงินเดือนประจำครึ่งปี 0.5 ขั้น', 0.5, 1, $SESS_USERID, '$UPDATE_DATE', 1) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_SALARY_MOVMENT (SM_CODE, SM_NAME, SM_REMARK, SM_FACTOR, SM_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE, SM_SEQ_NO)
								  VALUES ('2', 'หนึ่งขั้น', 'เลื่อนขั้นเงินเดือนประจำครึ่งปี 1.0 ขั้น กรณีพิเศษ', 1, 1, $SESS_USERID, '$UPDATE_DATE', 2) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_SALARY_MOVMENT (SM_CODE, SM_NAME, SM_REMARK, SM_FACTOR, SM_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE, SM_SEQ_NO)
								  VALUES ('3', 'หนึ่งขั้นครึ่ง', 'เลื่อนขั้นเงินเดือนประจำครึ่งปี 1.5 ขั้น กรณีพิเศษ', 1.5, 1, $SESS_USERID, '$UPDATE_DATE', 3) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_SALARY_MOVMENT (SM_CODE, SM_NAME, SM_REMARK, SM_FACTOR, SM_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE, SM_SEQ_NO)
								  VALUES ('4', 'สองขั้น', 'เลื่อนขั้นเงินเดือนประจำครึ่งปี 2.0 ขั้น กรณีพิเศษ', 2, 1, $SESS_USERID, '$UPDATE_DATE', 4) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_SALARY_MOVMENT (SM_CODE, SM_NAME, SM_REMARK, SM_FACTOR, SM_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE, SM_SEQ_NO)
								  VALUES ('5', 'เต็มขั้น (2%)', NULL, 0.5, 1, $SESS_USERID, '$UPDATE_DATE', 5) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_SALARY_MOVMENT (SM_CODE, SM_NAME, SM_REMARK, SM_FACTOR, SM_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE, SM_SEQ_NO)
								  VALUES ('6', 'ลดครึ่งขั้น', 'ลงโทษลดขั้นเงินเดือน 0.5 ขั้น', -0.5, 1, $SESS_USERID, '$UPDATE_DATE', 6) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_SALARY_MOVMENT (SM_CODE, SM_NAME, SM_REMARK, SM_FACTOR, SM_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE, SM_SEQ_NO)
								  VALUES ('7', 'ลดหนึ่งขั้น', 'ลงโทษลดขั้นเงินเดือน 1.0 ขั้น', -1, 1, $SESS_USERID, '$UPDATE_DATE', 7) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_SALARY_MOVMENT (SM_CODE, SM_NAME, SM_REMARK, SM_FACTOR, SM_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE, SM_SEQ_NO)
								  VALUES ('8', 'ตัดเงินเดือน', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE', 8) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_SALARY_MOVMENT (SM_CODE, SM_NAME, SM_REMARK, SM_FACTOR, SM_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE, SM_SEQ_NO)
								  VALUES ('9', 'ตัดโอน', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE', 9) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_SALARY_MOVMENT (SM_CODE, SM_NAME, SM_REMARK, SM_FACTOR, SM_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE, SM_SEQ_NO)
								  VALUES ('10', 'งดเลื่อนขั้น', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE', 10) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_SALARY_MOVMENT (SM_CODE, SM_NAME, SM_REMARK, SM_FACTOR, SM_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE, SM_SEQ_NO)
								  VALUES ('11', 'เลื่อนระดับ', NULL', 0, 1, $SESS_USERID, '$UPDATE_DATE', 11) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_SALARY_MOVMENT (SM_CODE, SM_NAME, SM_REMARK, SM_FACTOR, SM_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE, SM_SEQ_NO)
								  VALUES ('12', '-', 'ปรับอัตราเงินเดือนตาม มติ ค.ร.ม. ฯลฯ', 0, 1, $SESS_USERID, '$UPDATE_DATE', 12) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_SALARY_MOVMENT (SM_CODE, SM_NAME, SM_REMARK, SM_FACTOR, SM_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE, SM_SEQ_NO)
								  VALUES ('14', 'ปรับวุฒิ', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE', 14) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_SALARY_MOVMENT (SM_CODE, SM_NAME, SM_REMARK, SM_FACTOR, SM_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE, SM_SEQ_NO)
								  VALUES ('15', 'บรรจุ', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE', 15) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_SALARY_MOVMENT (SM_CODE, SM_NAME, SM_REMARK, SM_FACTOR, SM_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE, SM_SEQ_NO)
								  VALUES ('16', 'รับโอน', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE', 16) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_SALARY_MOVMENT (SM_CODE, SM_NAME, SM_REMARK, SM_FACTOR, SM_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE, SM_SEQ_NO)
								  VALUES ('17', 'เต็มขั้น (4%)', NULL, 1, 1, $SESS_USERID, '$UPDATE_DATE', 17) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_SALARY_MOVMENT (SM_CODE, SM_NAME, SM_REMARK, SM_FACTOR, SM_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE, SM_SEQ_NO)
								  VALUES ('18', 'เต็มขั้น (6%)', NULL, 1.5, 1, $SESS_USERID, '$UPDATE_DATE', 18) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_SALARY_MOVMENT (SM_CODE, SM_NAME, SM_REMARK, SM_FACTOR, SM_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE, SM_SEQ_NO)
								  VALUES ('19', 'ปรับขั้นต่ำ', 'ปรับเงินเดือนผู้ยังไม่ถึงขั้นต่ำของระดับตามบัญชีเงินเดือน', 0, 1, $SESS_USERID, '$UPDATE_DATE', 19) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			}

			add_field("PER_MOVMENT", "MOV_SUB_TYPE","INTEGER2", "2", "NULL");
	
			$cmd = " UPDATE PER_MOVMENT SET MOV_SUB_TYPE = 1 WHERE MOV_TYPE = 1 AND 
							(SUBSTR(MOV_CODE,1,3) = '101' OR SUBSTR(MOV_CODE,1,3) = '105' OR SUBSTR(MOV_CODE,1,3) = '108') AND MOV_SUB_TYPE IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
	
			$cmd = " UPDATE PER_MOVMENT SET MOV_SUB_TYPE = 2 WHERE MOV_TYPE = 1 AND 
							SUBSTR(MOV_CODE,1,3) = '103' AND MOV_SUB_TYPE IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
	
			$cmd = " UPDATE PER_MOVMENT SET MOV_SUB_TYPE = 3 WHERE MOV_TYPE = 1 AND 
							(SUBSTR(MOV_CODE,1,3) = '104' OR SUBSTR(MOV_CODE,1,3) = '124') AND MOV_SUB_TYPE IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
	
			$cmd = " UPDATE PER_MOVMENT SET MOV_SUB_TYPE = 4 WHERE MOV_TYPE = 2 AND 
							SUBSTR(MOV_CODE,1,1) = '2' AND MOV_SUB_TYPE IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
	
			$cmd = " UPDATE PER_MOVMENT SET MOV_SUB_TYPE = 5 WHERE MOV_TYPE = 1 AND 
							(SUBSTR(MOV_CODE,1,3) = '109' OR SUBSTR(MOV_CODE,1,3) = '110' OR SUBSTR(MOV_CODE,1,3) = '111' OR 
							SUBSTR(MOV_CODE,1,3) = '113') AND MOV_SUB_TYPE IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
	
			$cmd = " UPDATE PER_MOVMENT SET MOV_SUB_TYPE = 6 WHERE MOV_TYPE = 1 AND SUBSTR(MOV_CODE,1,3) = '101' AND MOV_SUB_TYPE IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
	/*
			$cmd = " UPDATE PER_MOVMENT SET MOV_SUB_TYPE = 7 WHERE MOV_TYPE = 1 AND SUBSTR(MOV_CODE,1,3) = '101' AND MOV_SUB_TYPE IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
	*/
			$cmd = " UPDATE PER_MOVMENT SET MOV_SUB_TYPE = 9 WHERE MOV_TYPE = 1 AND 
							(SUBSTR(MOV_CODE,1,3) = '106' OR SUBSTR(MOV_CODE,1,3) = '118' OR SUBSTR(MOV_CODE,1,3) = '119' OR 
							SUBSTR(MOV_CODE,1,3) = '120' OR SUBSTR(MOV_CODE,1,3) = '121' OR SUBSTR(MOV_CODE,1,3) = '122' OR 
							SUBSTR(MOV_CODE,1,3) = '123') AND MOV_SUB_TYPE IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
	
			add_field("PER_POSITIONHIS", "POH_SEQ_NO","INTEGER2", "5", "NULL");
			add_field("PER_SALARYHIS", "SAH_SEQ_NO","INTEGER2", "5", "NULL");
			add_field("PER_POS_MOVE", "POS_SEQ_NO","INTEGER2", "5", "NULL");
			add_field("PER_POS_MOVE", "PAY_NO","VARCHAR", "15", "NULL");
			add_field("PER_POS_MOVE", "POS_ORGMGT","VARCHAR", "255", "NULL");
			add_field("PER_POS_MOVE", "LEVEL_NO","VARCHAR", "10", "NULL");
			add_field("PER_EXTRATYPE", "EX_AMT","NUMBER", "16,2", "NULL");
			add_field("PER_EXTRAHIS", "EXH_ACTIVE","SINGLE", "1", "NULL");
			add_field("PER_COMDTL", "CMD_LEVEL_POS","VARCHAR", "10", "NULL");

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_POSITIONHIS ALTER POH_REMARK NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POSITIONHIS MODIFY POH_REMARK NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_POSITIONHIS MODIFY POH_REMARK NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_SCHOLAR ALTER SC_REMARK MEMO ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SCHOLAR MODIFY SC_REMARK VARCHAR2(2000) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_SCHOLAR MODIFY SC_REMARK TEXT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_SCHOLAR", "SC_GRADE","NUMBER", "6,2", "NULL");
			add_field("PER_SCHOLAR", "SC_HONOR","VARCHAR", "100", "NULL");
			add_field("PER_SCHOLAR", "SC_DOCNO","VARCHAR", "255", "NULL");
			add_field("PER_SCHOLAR", "SC_DOCDATE","VARCHAR", "19", "NULL");
			add_field("PER_SCHOLAR", "SC_INSTITUTE","VARCHAR", "255", "NULL");
			add_field("PER_SCHOLAR", "CT_CODE","VARCHAR", "10", "NULL");
			add_field("PER_SCHOLAR", "SC_FUND","VARCHAR", "255", "NULL");

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_SCHOLAR ALTER INS_CODE NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SCHOLAR MODIFY INS_CODE NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_SCHOLAR MODIFY INS_CODE NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_R010032.html' ";
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
								  VALUES (1, 'TH', $MAX_ID, 2, 'R1032 จำนวนข้าราชการพลเรือน จำแนกตามระดับตำแหน่ง เพศ และสังกัด', 'S', 'W', 'rpt_R010032.html', 0, 36, 405, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 2, 'R1032 จำนวนข้าราชการพลเรือน จำแนกตามระดับตำแหน่ง เพศ และสังกัด', 'S', 'W', 'rpt_R010032.html', 0, 36, 405, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_R010036.html' ";
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
								  VALUES (1, 'TH', $MAX_ID, 6, 'R1036 กรอบอัตรากำลังข้าราชการพลเรือน จำแนกตามสายงาน ระดับตำแหน่ง คนครอง และอัตราว่าง (ฐานข้อมูลจากอัตราเงินเดือนตั้งจ่าย)', 'S', 'W', 
								  'rpt_R010036.html', 0, 36, 405, $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 6, 'R1036 กรอบอัตรากำลังข้าราชการพลเรือน จำแนกตามสายงาน ระดับตำแหน่ง คนครอง และอัตราว่าง (ฐานข้อมูลจากอัตราเงินเดือนตั้งจ่าย)', 'S', 'W', 
								  'rpt_R010036.html', 0, 36, 405, $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			add_field("PER_POSITIONHIS", "POH_ISREAL","CHAR", "1", "NULL");

			$cmd = " UPDATE PER_POSITIONHIS SET POH_ISREAL = 'Y' WHERE POH_ISREAL IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_PERSONAL", "PER_POS_ORGMGT","VARCHAR", "255", "NULL");
			add_field("PER_POSITIONHIS", "POH_ORG_DOPA_CODE","VARCHAR", "20", "NULL");
			add_field("PER_SALARYHIS", "SAH_ORG_DOPA_CODE","VARCHAR", "20", "NULL");
	
			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_MARRHIS ALTER MAH_MARRY_DATE NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_MARRHIS MODIFY MAH_MARRY_DATE NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_MARRHIS MODIFY MAH_MARRY_DATE NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_NAMEHIS ALTER NH_DATE NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_NAMEHIS MODIFY NH_DATE NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_NAMEHIS MODIFY NH_DATE NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="oci8") {
				$cmd = " ALTER TABLE PER_MESSAGE MODIFY MSG_DETAIL VARCHAR2(4000) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			}

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_EDUCNAME ALTER EN_SHORTNAME VARCHAR(100) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_EDUCNAME MODIFY EN_SHORTNAME VARCHAR2(100) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_EDUCNAME MODIFY EN_SHORTNAME VARCHAR(100) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " SELECT AT_CODE FROM PER_ATTACHMENT ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) {
				if($DPISDB=="odbc") 
					$cmd = " CREATE TABLE PER_ATTACHMENT(
					AT_CODE VARCHAR(10) NOT NULL,	
					AT_NAME VARCHAR(100) NOT NULL,
					AT_REMARK VARCHAR(255) NULL,
					AT_ACTIVE SINGLE NOT NULL,
					UPDATE_USER INTEGER NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					AT_SEQ_NO INTEGER2 NULL,
					CONSTRAINT PK_PER_ATTACHMENT PRIMARY KEY (AT_CODE)) ";
				elseif($DPISDB=="oci8") 
					$cmd = " CREATE TABLE PER_ATTACHMENT(
					AT_CODE VARCHAR2(10) NOT NULL,	
					AT_NAME VARCHAR2(100) NOT NULL,
					AT_REMARK VARCHAR2(255) NULL,
					AT_ACTIVE NUMBER(1) NOT NULL,
					UPDATE_USER NUMBER(11) NOT NULL,
					UPDATE_DATE VARCHAR2(19) NOT NULL,		
					AT_SEQ_NO NUMBER(5) NULL,
					CONSTRAINT PK_PER_ATTACHMENT PRIMARY KEY (AT_CODE)) ";
				elseif($DPISDB=="mysql")
					$cmd = " CREATE TABLE PER_ATTACHMENT(
					AT_CODE VARCHAR(10) NOT NULL,	
					AT_NAME VARCHAR(100) NOT NULL,
					AT_REMARK VARCHAR(255) NULL,
					AT_ACTIVE SMALLINT(1) NOT NULL,
					UPDATE_USER INTEGER(11) NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					AT_SEQ_NO SMALLINT(5) NULL,
					CONSTRAINT PK_PER_ATTACHMENT PRIMARY KEY (AT_CODE)) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 
								  WHERE LINKTO_WEB = 'master_table_attachment.html?table=PER_ATTACHMENT' ";
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
									  VALUES (1, 'TH', $MAX_ID, 11, 'M0111 เอกสารแนบ', 'S', 'W', 'master_table_attachment.html?table=PER_ATTACHMENT', 0, 9, 296, 
									  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();

					$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
									  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
									  CREATE_BY, UPDATE_DATE, UPDATE_BY)
									  VALUES (1, 'EN', $MAX_ID, 11, 'M0111 เอกสารแนบ', 'S', 'W', 'master_table_attachment.html?table=PER_ATTACHMENT', 0, 9, 296, 
									  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();
				}
			}

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_R004000_select_person.html' ";
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
								  VALUES (1, 'TH', $MAX_ID, 0, 'R0400 ก.พ.7 อิเล็คทรอนิกส์', 'S', 'W', 'rpt_R004000_select_person.html', 0, 36,
								  236, $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 0, 'R0400 ก.พ.7 อิเล็คทรอนิกส์', 'S', 'W', 'rpt_R004000_select_person.html', 0, 36,
								  236, $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " update PER_CONTROL set CTRL_ALTER = 9 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
?>