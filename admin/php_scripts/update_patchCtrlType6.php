<?php 
add_field("PER_KPI_FORM", "PER_ID_REVIEW0","INTEGER", "10", "NULL");
			add_field("PER_LINE", "LAYER_TYPE","SINGLE", "1", "NULL");
			add_field("PER_COMMAND", "COM_LEVEL_SALP","INTEGER2", "2", "NULL");

			$cmd = " UPDATE PER_COMMAND SET COM_LEVEL_SALP = 1 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_POSITIONHIS ALTER POH_UNDER_ORG1 VARCHAR(255) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POSITIONHIS MODIFY POH_UNDER_ORG1 VARCHAR2(255) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_POSITIONHIS MODIFY POH_UNDER_ORG1 VARCHAR(255) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_POSITIONHIS ALTER POH_UNDER_ORG2 VARCHAR(255) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POSITIONHIS MODIFY POH_UNDER_ORG2 VARCHAR2(255) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_POSITIONHIS MODIFY POH_UNDER_ORG2 VARCHAR(255) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_ORG", "ORG_ENG_NAME","VARCHAR", "255", "NULL");
			add_field("PER_ORG_ASS", "ORG_ENG_NAME","VARCHAR", "255", "NULL");
			add_field("PER_KPI", "ORG_NAME","VARCHAR", "255", "NULL");
			add_field("PER_KPI", "UNDER_ORG_NAME1","VARCHAR", "255", "NULL");

			$cmd = " UPDATE PER_PERSONAL SET PER_OFFNO = TRIM(PER_OFFNO) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_PERSONL_PER_OFFNO ON PER_PERSONAL (PER_OFFNO) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_WORK_CYCLEHIS ON PER_WORK_CYCLEHIS (PER_ID, START_DATE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_WORK_TIME ON PER_WORK_TIME (PER_ID, START_DATE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_WORK_TIME1 ON PER_WORK_TIME (START_DATE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_SALQUOTADTL1 DROP CONSTRAINT PK_PER_SALQUOTADTL1 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DROP INDEX PK_PER_SALQUOTADTL1 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_SALQUOTADTL1 DROP CONSTRAINT FK1_PER_SALQUOTADTL1 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_SALQUOTADTL2 DROP CONSTRAINT PK_PER_SALQUOTADTL2 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DROP INDEX PK_PER_SALQUOTADTL2 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_SALQUOTADTL2 DROP CONSTRAINT FK1_PER_SALQUOTADTL2 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_SALPROMOTE DROP CONSTRAINT PK_PER_SALPROMOTE ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DROP INDEX PK_PER_SALPROMOTE ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_SALPROMOTE DROP CONSTRAINT FK1_PER_SALPROMOTE ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_SALQUOTA DROP CONSTRAINT PK_PER_SALQUOTA ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DROP INDEX PK_PER_SALQUOTA ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_SALQUOTA ADD CONSTRAINT PK_PER_SALQUOTA PRIMARY KEY (SALQ_YEAR, SALQ_TYPE, 
							DEPARTMENT_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_SALQUOTADTL1 ADD CONSTRAINT PK_PER_SALQUOTADTL1 PRIMARY KEY (SALQ_YEAR, 
							SALQ_TYPE, DEPARTMENT_ID, ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_SALQUOTADTL1 ADD CONSTRAINT FK1_PER_SALQUOTADTL1 FOREIGN KEY (SALQ_YEAR, 
							SALQ_TYPE, DEPARTMENT_ID) REFERENCES PER_SALQUOTA (SALQ_YEAR, SALQ_TYPE, DEPARTMENT_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_SALQUOTADTL2 ADD CONSTRAINT PK_PER_SALQUOTADTL2 PRIMARY KEY (SALQ_YEAR, 
							SALQ_TYPE, DEPARTMENT_ID, ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_SALQUOTADTL2 ADD CONSTRAINT FK1_PER_SALQUOTADTL2 FOREIGN KEY (SALQ_YEAR, 
							SALQ_TYPE, DEPARTMENT_ID) REFERENCES PER_SALQUOTA (SALQ_YEAR, SALQ_TYPE, DEPARTMENT_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_SALPROMOTE ADD CONSTRAINT PK_PER_SALPROMOTE PRIMARY KEY (SALQ_YEAR, 
							SALQ_TYPE, DEPARTMENT_ID, PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_SALPROMOTE ADD CONSTRAINT FK1_PER_SALPROMOTE FOREIGN KEY (SALQ_YEAR, 
							SALQ_TYPE, DEPARTMENT_ID) REFERENCES PER_SALQUOTA (SALQ_YEAR, SALQ_TYPE, DEPARTMENT_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_PRENAME", "RANK_FLAG","SINGLE", "1", "NULL");

			$cmd = " UPDATE PER_PRENAME SET RANK_FLAG = 0 WHERE RANK_FLAG IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " SELECT PN_CODE, PN_NAME FROM PER_PRENAME ORDER BY PN_NAME ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			while($data = $db_dpis->get_array()){
				$PN_CODE = $data[PN_CODE];
				$PN_NAME = $data[PN_NAME];
				if (strpos($PN_NAME,"ตำรวจ") !== false || strpos($PN_NAME,"ทหาร") !== false || strpos($PN_NAME,"พล") !== false || 
					strpos($PN_NAME,"สิบ") !== false || strpos($PN_NAME,"ร้อย") !== false || strpos($PN_NAME,"พัน") !== false || 
					strpos($PN_NAME,"จ่า") !== false || strpos($PN_NAME,"ดาบ") !== false || strpos($PN_NAME,"เรือ") !== false || 
					strpos($PN_NAME,"นาวา") !== false) {
					$cmd = " UPDATE PER_PRENAME SET RANK_FLAG = 1 WHERE PN_CODE = '$PN_CODE' ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
				} // end if
			} // end while						

			add_field("PER_POSITIONHIS", "POH_ORG_TRANSFER","VARCHAR", "255", "NULL");
			add_field("PER_COMDTL", "CMD_ORG_TRANSFER","VARCHAR", "255", "NULL");
			add_field("PER_COMDTL", "CMD_PERCENT","NUMBER", "6,3", "NULL");
			add_field("PER_KPI_FORM", "ORG_ID","INTEGER", "10", "NULL");
			add_field("PER_KPI_FORM", "TOTAL_SCORE","NUMBER", "5,2", "NULL");
			add_field("PER_KPI_FORM", "SALARY_FLAG","CHAR", "1", "NULL");
			add_field("PER_KPI_FORM", "ORG_ID_SALARY","INTEGER", "10", "NULL");
			add_field("PER_KPI_FORM", "KPI_FLAG","CHAR", "1", "NULL");
			add_field("PER_KPI_FORM", "ORG_ID_KPI","INTEGER", "10", "NULL");
			add_field("PER_KPI_FORM", "CHIEF_PER_ID","INTEGER", "10", "NULL");
			add_field("PER_KPI_FORM", "FRIEND_FLAG","CHAR", "1", "NULL");
			add_field("PER_KPI_FORM", "ORG_ID_1_SALARY","INTEGER", "10", "NULL");
			add_field("PER_KPI_FORM", "ORG_ID_ASS","INTEGER", "10", "NULL");

			$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, 
							  UPDATE_DATE)
							  VALUES ('21315', 'เลื่อนเงินเดือนระดับพอใช้', 2, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, 
							  UPDATE_DATE)
							  VALUES ('21325', 'เลื่อนเงินเดือนระดับดี', 2, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, 
							  UPDATE_DATE)
							  VALUES ('21335', 'เลื่อนเงินเดือนระดับดีมาก', 2, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, 
							  UPDATE_DATE)
							  VALUES ('21345', 'เลื่อนเงินเดือนระดับดีเด่น', 2, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, 
							  UPDATE_DATE)
							  VALUES ('21415', 'เงินเดือนเต็มขั้น ได้เงินค่าตอบแทนพิเศษ', 2, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_COMTYPE SET  COM_DESC = trim(COM_DESC) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_COMTYPE SET  COM_DESC = 'คำสั่งบรรจุผู้ทรงคุณวุฒิ ผู้เชี่ยวชาญ ผู้ชำนาญการ' 
							WHERE COM_DESC = 'คำสั่งบรรจุผู้ทรงคุณวุฒิ ผู้เชี่ยวชาญ ผู้ชำนาญ' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_COMTYPE SET  COM_DESC = 'คำสั่งบรรจุผู้ออกไปรับราชการทหารกลับเข้ารับราชการ' 
							WHERE COM_DESC = 'คำสั่งบรรจุข้าราชการพลเรือนสามัญที่ไปรับราชการทหารกลับเข้ารับราชการ' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_COMTYPE SET  COM_DESC = 'คำสั่งบรรจุผู้ไปปฏิบัติงานตามมติคณะรัฐมนตรีกลับเข้ารับราชการ' 
							WHERE COM_DESC = 'คำสั่งบรรจุข้าราชการพลเรือนสามัญที่ได้รับอนุมัติจากคณะรัฐมนตรีให้ออกจากราชการไปปฏิบัติงานใดๆกลับเข้ารับราชการ' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_COMTYPE SET  COM_DESC = 'คำสั่งบรรจุผู้เคยเป็นข้าราชการพลเรือนสามัญกลับเข้ารับราชการ' 
							WHERE COM_DESC = 'คำสั่งบรรจุข้าราชการพลเรือนสามัญกลับเข้ารับราชการ' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_COMTYPE SET  COM_DESC = 'คำสั่งเลื่อนผู้สอบคัดเลือกได้ และผู้ที่ได้รับคัดเลือก' 
							WHERE COM_DESC = 'คำสั่งเลื่อน(ผู้สอบคัดเลือก และผู้ที่ได้รับคัดเลือก)' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_COMTYPE SET  COM_DESC = 'คำสั่งบรรจุผู้เคยเป็นข้าราชการพลเรือนสามัญกลับเข้ารับราชการให้ดำรงตำแหน่งนักบริหาร และผู้ตรวจราชการ' 
							WHERE COM_DESC = 'คำสั่งบรรจุกลับเข้ารับราชการให้ดำรงตำแหน่งนักบริหาร และผู้ตรวจราชการ' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_COMTYPE SET  COM_DESC = 'คำสั่งบรรจุผู้เคยเป็นข้าราชการพลเรือนสามัญกลับเข้ารับราชการให้ดำรงตำแหน่งประเภทบริหารระดับกลาง' 
							WHERE COM_DESC = 'คำสั่งบรรจุกลับเข้ารับราชการให้ดำรงตำแหน่งประเภทบริหารระดับกลาง' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_COMTYPE SET  COM_DESC = 'คำสั่งย้ายไปดำรงตำแหน่ง ว. วช. ชช.' 
							WHERE COM_DESC = 'คำสั่งย้ายไปดำรงตำแหน่ง ว. วช. ชช' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_COMTYPE SET  COM_DESC = 'คำสั่งเลื่อนขึ้นดำรงตำแหน่ง ว. วช. ชช.' 
							WHERE COM_DESC = 'คำสั่งเลื่อนขึ้นดำรงตำแหน่ง ว. วช. ชช' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_COMTYPE SET  COM_DESC = 'คำสั่งบรรจุผู้เคยเป็นข้าราชการพลเรือนสามัญกลับเข้ารับราชการให้ดำรงตำแหน่ง ว. วช. ชช.' 
							WHERE COM_DESC = 'คำสั่งบรรจุกลับเข้ารับราชการให้ดำรงตำแหน่ง ว. วช. ชช' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_COMTYPE SET  COM_DESC = 'คำสั่งย้ายข้าราชการตาม ว 2/2540', COM_GROUP = '02'
							WHERE COM_TYPE = '0801' AND COM_NAME = 'คส. 8.1' AND COM_DESC = 'คำสั่งเลื่อนขึ้นดำรงตำแหน่ง' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_COMTYPE SET  COM_DESC = 'คำสั่งเลื่อนข้าราชการตาม ว 2/2540', COM_GROUP = '04' 
							WHERE COM_TYPE = '0802' AND COM_NAME = 'คส. 8.2' AND COM_DESC = 'คำสั่งรับโอนมาดำรงตำแหน่ง' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_COMTYPE SET  COM_DESC = 'คำสั่งรับโอนข้าราชการพลเรือนสามัญตาม ว 2/2540'
							WHERE COM_TYPE = '0803' AND COM_NAME = 'คส. 8.3' AND COM_DESC = 'คำสั่งบรรจุกลับเข้ารับราชการให้ดำรงตำแหน่ง' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " SELECT COM_TYPE FROM PER_COMTYPE WHERE COM_TYPE = '0804' ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) {
				$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_ACTIVE)
								VALUES ('0804', 'คส. 8.4', 'คำสั่งบรรจุผู้เคยเป็นข้าราชการพลเรือนสามัญกลับเข้ารับราชการตาม ว 2/2540', '01', 1) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_COMMAND SET  COM_TYPE = '0804' WHERE COM_TYPE = '0803' ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_COMMAND SET  COM_TYPE = '0803' WHERE COM_TYPE = '0802' ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_COMMAND SET  COM_TYPE = '0802' WHERE COM_TYPE = '0801' ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			}

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_ACTIVE)
							VALUES ('0900', 'คส. 9', 'คำสั่งให้ได้รับเงินเดือนตามวุฒิ (ปรับวุฒิ)', '05', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_ACTIVE)
							VALUES ('1101', 'คส. 11.1', 'คำสั่งให้ได้รับเงินเดือน', '05', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_ACTIVE)
							VALUES ('1102', 'คส. 11.2', 'คำสั่งให้ได้รับเงินเดือน (กรณีรับเงินเดือนต่างอันดับ)', '05', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_ACTIVE)
							VALUES ('1200', 'คส. 12', 'คำสั่งให้ข้าราชการรับเงินเดือนในอัตราทดแทน', '05', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_ACTIVE)
							VALUES ('1300', 'คส. 13', 'คำสั่งรักษาราชการแทน', '07', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_ACTIVE)
							VALUES ('1400', 'คส. 14', 'คำสั่งรักษาการในตำแหน่ง', '07', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_ACTIVE)
							VALUES ('1500', 'คส. 15', 'คำสั่งพักราชการและคำสั่งให้ออกจากราชการไว้ก่อน', '06', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_ACTIVE)
							VALUES ('1600', 'คส. 16', 'คำสั่งให้ข้าราชการประจำส่วนราชการ', '06', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_COMTYPE SET  COM_DESC = 'คำสั่งให้ข้าราชการออกจากราชการเพราะขาดคุณสมบัติทั่วไปหรือขาดคุณสมบัติเฉพาะสำหรับตำแหน่ง' 
							WHERE COM_DESC = 'คำสั่งให้ข้าราชการออกจากราชการเพราะขาดคุณสมบัติทั่วไปหรือคุณสมบัติเฉพาะสำหรับตำแหน่ง' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_ACTIVE)
							VALUES ('1706', 'คส. 17.6', 'คำสั่งให้ข้าราชการไปปฏิบัติงานตามมติคณะรัฐมนตรี', '06', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_ACTIVE)
							VALUES ('1707', 'คส. 17.7', 'คำสั่งให้ข้าราชการออกจากราชการเพื่อไปปฏิบัติงานตามมติคณะรัฐมนตรี', '06', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_ACTIVE)
							VALUES ('1800', 'คส. 18', 'คำสั่งยกเลิกคำสั่งเดิม', '08', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_ACTIVE)
							VALUES ('1900', 'คส. 19', 'คำสั่งเพื่อแก้ไขคำสั่งที่ผิดพลาด', '08', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_ACTIVE)
							VALUES ('2001', 'คส. 20.1', 'คำสั่งลงโทษภาคทัณฑ์ (มาตรา 103)', '09', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_ACTIVE)
							VALUES ('2002', 'คส. 20.2', 'คำสั่งลงโทษตัดเงินเดือน (มาตรา 103)', '09', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_ACTIVE)
							VALUES ('2003', 'คส. 20.3', 'คำสั่งลงโทษลดขั้นเงินเดือน (มาตรา 103)', '09', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_ACTIVE)
							VALUES ('2004', 'คส. 20.4', 'คำสั่งลงโทษ..........ออกจากราชการ (มาตรา 103)', '09', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_ACTIVE)
							VALUES ('2005', 'คส. 20.5', 'คำสั่งเพิ่มโทษ/ลดโทษ/งดโทษ/ยกโทษ (มาตรา 109 วรรคสอง)', '09', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_ACTIVE)
							VALUES ('2006', 'คส. 20.6', 'คำสั่งเพิ่มโทษ/ลดโทษ/งดโทษ/ยกโทษ (มาตรา 109 วรรคสามหรือวรรคเจ็ด)', '09', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_ACTIVE)
							VALUES ('2007', 'คส. 20.7', 'คำสั่งเพิ่มโทษ/ลดโทษ/งดโทษ/ยกโทษ ลงโทษ..........  (มาตรา 109 วรรคสี่)', '09', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_ACTIVE)
							VALUES ('2008', 'คส. 20.8', 'คำสั่งเพิ่มโทษ/ลดโทษ/งดโทษ/ยกโทษ ลงโทษ..........  (มาตรา 9)', '09', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_ACTIVE)
							VALUES ('2009', 'คส. 20.9', 'คำสั่งยกโทษ/งดโทษ/ลดโทษ/เพิ่มโทษ (มาตรา 125 (1) (2) (3))', '09', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_ACTIVE)
							VALUES ('2010', 'คส. 20.10', 'คำสั่งเพิ่มโทษ/ลดโทษ/งดโทษ/ยกโทษ และให้กลับเข้ารับราชการ  (มาตรา 125 (4), มาตรา 126)', '09', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_ACTIVE)
							VALUES ('1005', 'คส. 10.5', 'คำสั่งเลื่อนเงินเดือน', '05', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_PERSONAL", "PER_FILE_NO","VARCHAR", "25", "NULL");
			add_field("PER_PERSONAL", "PER_BANK_ACCOUNT","VARCHAR", "25", "NULL");
			add_field("PER_DECORATEHIS", "DEH_RECEIVE_FLAG","SINGLE", "1", "NULL");
			add_field("PER_DECORATEHIS", "DEH_RETURN_FLAG","SINGLE", "1", "NULL");
			add_field("PER_DECORATEHIS", "DEH_RETURN_DATE","VARCHAR", "19", "NULL");
			add_field("PER_DECORATEHIS", "DEH_RETURN_TYPE","SINGLE", "1", "NULL");

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_MGT ALTER PM_NAME VARCHAR(255) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_MGT MODIFY PM_NAME VARCHAR2(255) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_MGT MODIFY PM_NAME VARCHAR(255) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_PERFORMANCE_REVIEW ALTER PFR_NAME VARCHAR(255) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PERFORMANCE_REVIEW MODIFY PFR_NAME VARCHAR2(255) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_PERFORMANCE_REVIEW MODIFY PFR_NAME VARCHAR(255) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_SKILL ALTER SKILL_NAME VARCHAR(100) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SKILL MODIFY SKILL_NAME VARCHAR2(100) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_SKILL MODIFY SKILL_NAME VARCHAR(100) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_COMPETENCE", "CP_ENG_NAME","VARCHAR", "100", "NULL");
			add_field("PER_LAYER", "LAYER_EXTRA_MIDPOINT","NUMBER", "16,2", "NULL");
			add_field("PER_LAYER_NEW", "LAYER_EXTRA_MIDPOINT","NUMBER", "16,2", "NULL");
			add_field("PER_LAYER", "LAYER_EXTRA_MIDPOINT1","NUMBER", "16,2", "NULL");
			add_field("PER_LAYER_NEW", "LAYER_EXTRA_MIDPOINT1","NUMBER", "16,2", "NULL");
			add_field("PER_LAYER", "LAYER_EXTRA_MIDPOINT2","NUMBER", "16,2", "NULL");
			add_field("PER_LAYER_NEW", "LAYER_EXTRA_MIDPOINT2","NUMBER", "16,2", "NULL");
			add_field("PER_SCHOLARSHIP", "SCH_YEAR","VARCHAR", "4", "NULL");
			add_field("PER_SCHOLARSHIP", "SCH_TYPE","SINGLE", "1", "NULL");
			add_field("PER_SCHOLARSHIP", "SCH_CLASS","INTEGER2", "3", "NULL");
			add_field("PER_SCHOLARSHIP", "EN_CODE","VARCHAR", "10", "NULL");
			add_field("PER_SCHOLARSHIP", "EM_CODE","VARCHAR", "10", "NULL");
			add_field("PER_SCHOLARSHIP", "SCH_START_DATE","VARCHAR", "19", "NULL");
			add_field("PER_SCHOLARSHIP", "SCH_END_DATE","VARCHAR", "19", "NULL");
			add_field("PER_SCHOLARSHIP", "SCH_PLACE","VARCHAR", "200", "NULL");
			add_field("PER_SCHOLARSHIP", "CT_CODE_OWN","VARCHAR", "10", "NULL");
			add_field("PER_SCHOLARSHIP", "CT_CODE_GO","VARCHAR", "10", "NULL");
			add_field("PER_SCHOLARSHIP", "SCH_BUDGET","NUMBER", "16,2", "NULL");
			add_field("PER_SCHOLARSHIP", "SCH_APP_DOC_NO","VARCHAR", "50", "NULL");
			add_field("PER_SCHOLARSHIP", "SCH_DOC_DATE","VARCHAR", "19", "NULL");
			add_field("PER_SCHOLARSHIP", "SCH_APP_DATE","VARCHAR", "19", "NULL");
			add_field("PER_SCHOLARSHIP", "SCH_APP_PER_ID","INTEGER", "10", "NULL");
			add_field("PER_SCHOLARSHIP", "SCH_REMARK","VARCHAR", "200", "NULL");

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE  PER_SCHOLARSHIP DROP CONSTRAINT INXU1_PER_SCHOLARSHIP ";
			elseif($DPISDB=="oci8") 
				$cmd = " DROP INDEX INXU1_PER_SCHOLARSHIP ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_ORG ON PER_ORG (DEPARTMENT_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_COMPENSATION_TEST", "CP_CONFIRM","SINGLE", "1", "NULL");

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_KPI_COMPETENCE ALTER KC_EVALUATE NUMBER ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_KPI_COMPETENCE MODIFY KC_EVALUATE NUMBER(4,2) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_KPI_COMPETENCE MODIFY KC_EVALUATE DECIMAL(4,2) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_KPI_COMPETENCE", "KC_WEIGHT","NUMBER", "5,2", "NULL");
			add_field("PER_PERSONAL", "PER_ID_REF","INTEGER", "10", "NULL");
			add_field("PER_PERSONAL", "PER_ID_ASS_REF","INTEGER", "10", "NULL");

			$cmd = " update PER_CONTROL set CTRL_ALTER = 6 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
?>