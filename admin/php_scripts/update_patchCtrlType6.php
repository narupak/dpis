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
				if (strpos($PN_NAME,"���Ǩ") !== false || strpos($PN_NAME,"����") !== false || strpos($PN_NAME,"��") !== false || 
					strpos($PN_NAME,"�Ժ") !== false || strpos($PN_NAME,"����") !== false || strpos($PN_NAME,"�ѹ") !== false || 
					strpos($PN_NAME,"���") !== false || strpos($PN_NAME,"�Һ") !== false || strpos($PN_NAME,"����") !== false || 
					strpos($PN_NAME,"����") !== false) {
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
							  VALUES ('21315', '����͹�Թ��͹�дѺ����', 2, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, 
							  UPDATE_DATE)
							  VALUES ('21325', '����͹�Թ��͹�дѺ��', 2, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, 
							  UPDATE_DATE)
							  VALUES ('21335', '����͹�Թ��͹�дѺ���ҡ', 2, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, 
							  UPDATE_DATE)
							  VALUES ('21345', '����͹�Թ��͹�дѺ����', 2, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, 
							  UPDATE_DATE)
							  VALUES ('21415', '�Թ��͹������ ���Թ��ҵͺ᷹�����', 2, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_COMTYPE SET  COM_DESC = trim(COM_DESC) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_COMTYPE SET  COM_DESC = '����觺�èؼ��ç�س�ز� �������Ǫҭ ���ӹҭ���' 
							WHERE COM_DESC = '����觺�èؼ��ç�س�ز� �������Ǫҭ ���ӹҭ' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_COMTYPE SET  COM_DESC = '����觺�èؼ���͡��Ѻ�Ҫ��÷��á�Ѻ����Ѻ�Ҫ���' 
							WHERE COM_DESC = '����觺�èآ���Ҫ��þ����͹���ѭ�����Ѻ�Ҫ��÷��á�Ѻ����Ѻ�Ҫ���' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_COMTYPE SET  COM_DESC = '����觺�èؼ��任�Ժѵԧҹ�����Ԥ���Ѱ����ա�Ѻ����Ѻ�Ҫ���' 
							WHERE COM_DESC = '����觺�èآ���Ҫ��þ����͹���ѭ������Ѻ͹��ѵԨҡ����Ѱ���������͡�ҡ�Ҫ���任�Ժѵԧҹ���Ѻ����Ѻ�Ҫ���' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_COMTYPE SET  COM_DESC = '����觺�èؼ�����繢���Ҫ��þ����͹���ѭ��Ѻ����Ѻ�Ҫ���' 
							WHERE COM_DESC = '����觺�èآ���Ҫ��þ����͹���ѭ��Ѻ����Ѻ�Ҫ���' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_COMTYPE SET  COM_DESC = '���������͹����ͺ�Ѵ���͡�� ��м�������Ѻ�Ѵ���͡' 
							WHERE COM_DESC = '���������͹(����ͺ�Ѵ���͡ ��м�������Ѻ�Ѵ���͡)' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_COMTYPE SET  COM_DESC = '����觺�èؼ�����繢���Ҫ��þ����͹���ѭ��Ѻ����Ѻ�Ҫ�������ç���˹觹ѡ������ ��м���Ǩ�Ҫ���' 
							WHERE COM_DESC = '����觺�èء�Ѻ����Ѻ�Ҫ�������ç���˹觹ѡ������ ��м���Ǩ�Ҫ���' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_COMTYPE SET  COM_DESC = '����觺�èؼ�����繢���Ҫ��þ����͹���ѭ��Ѻ����Ѻ�Ҫ�������ç���˹觻������������дѺ��ҧ' 
							WHERE COM_DESC = '����觺�èء�Ѻ����Ѻ�Ҫ�������ç���˹觻������������дѺ��ҧ' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_COMTYPE SET  COM_DESC = '���������仴�ç���˹� �. Ǫ. ��.' 
							WHERE COM_DESC = '���������仴�ç���˹� �. Ǫ. ��' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_COMTYPE SET  COM_DESC = '���������͹��鹴�ç���˹� �. Ǫ. ��.' 
							WHERE COM_DESC = '���������͹��鹴�ç���˹� �. Ǫ. ��' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_COMTYPE SET  COM_DESC = '����觺�èؼ�����繢���Ҫ��þ����͹���ѭ��Ѻ����Ѻ�Ҫ�������ç���˹� �. Ǫ. ��.' 
							WHERE COM_DESC = '����觺�èء�Ѻ����Ѻ�Ҫ�������ç���˹� �. Ǫ. ��' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_COMTYPE SET  COM_DESC = '��������¢���Ҫ��õ�� � 2/2540', COM_GROUP = '02'
							WHERE COM_TYPE = '0801' AND COM_NAME = '��. 8.1' AND COM_DESC = '���������͹��鹴�ç���˹�' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_COMTYPE SET  COM_DESC = '���������͹����Ҫ��õ�� � 2/2540', COM_GROUP = '04' 
							WHERE COM_TYPE = '0802' AND COM_NAME = '��. 8.2' AND COM_DESC = '������Ѻ�͹�Ҵ�ç���˹�' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_COMTYPE SET  COM_DESC = '������Ѻ�͹����Ҫ��þ����͹���ѭ��� � 2/2540'
							WHERE COM_TYPE = '0803' AND COM_NAME = '��. 8.3' AND COM_DESC = '����觺�èء�Ѻ����Ѻ�Ҫ�������ç���˹�' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " SELECT COM_TYPE FROM PER_COMTYPE WHERE COM_TYPE = '0804' ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) {
				$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_ACTIVE)
								VALUES ('0804', '��. 8.4', '����觺�èؼ�����繢���Ҫ��þ����͹���ѭ��Ѻ����Ѻ�Ҫ��õ�� � 2/2540', '01', 1) ";
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
							VALUES ('0900', '��. 9', '�����������Ѻ�Թ��͹����ز� (��Ѻ�ز�)', '05', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_ACTIVE)
							VALUES ('1101', '��. 11.1', '�����������Ѻ�Թ��͹', '05', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_ACTIVE)
							VALUES ('1102', '��. 11.2', '�����������Ѻ�Թ��͹ (�ó��Ѻ�Թ��͹��ҧ�ѹ�Ѻ)', '05', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_ACTIVE)
							VALUES ('1200', '��. 12', '�����������Ҫ����Ѻ�Թ��͹��ѵ�ҷ�᷹', '05', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_ACTIVE)
							VALUES ('1300', '��. 13', '������ѡ���Ҫ���᷹', '07', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_ACTIVE)
							VALUES ('1400', '��. 14', '������ѡ�ҡ��㹵��˹�', '07', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_ACTIVE)
							VALUES ('1500', '��. 15', '����觾ѡ�Ҫ�����Ф��������͡�ҡ�Ҫ�������͹', '06', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_ACTIVE)
							VALUES ('1600', '��. 16', '�����������Ҫ��û�Ш���ǹ�Ҫ���', '06', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_COMTYPE SET  COM_DESC = '�����������Ҫ����͡�ҡ�Ҫ������ТҴ�س���ѵԷ�������͢Ҵ�س���ѵ�੾������Ѻ���˹�' 
							WHERE COM_DESC = '�����������Ҫ����͡�ҡ�Ҫ������ТҴ�س���ѵԷ�������ͤس���ѵ�੾������Ѻ���˹�' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_ACTIVE)
							VALUES ('1706', '��. 17.6', '�����������Ҫ���任�Ժѵԧҹ�����Ԥ���Ѱ�����', '06', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_ACTIVE)
							VALUES ('1707', '��. 17.7', '�����������Ҫ����͡�ҡ�Ҫ�������任�Ժѵԧҹ�����Ԥ���Ѱ�����', '06', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_ACTIVE)
							VALUES ('1800', '��. 18', '�����¡��ԡ��������', '08', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_ACTIVE)
							VALUES ('1900', '��. 19', '�����������䢤���觷��Դ��Ҵ', '08', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_ACTIVE)
							VALUES ('2001', '��. 20.1', '�����ŧ���Ҥ�ѳ�� (�ҵ�� 103)', '09', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_ACTIVE)
							VALUES ('2002', '��. 20.2', '�����ŧ�ɵѴ�Թ��͹ (�ҵ�� 103)', '09', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_ACTIVE)
							VALUES ('2003', '��. 20.3', '�����ŧ��Ŵ����Թ��͹ (�ҵ�� 103)', '09', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_ACTIVE)
							VALUES ('2004', '��. 20.4', '�����ŧ��..........�͡�ҡ�Ҫ��� (�ҵ�� 103)', '09', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_ACTIVE)
							VALUES ('2005', '��. 20.5', '�����������/Ŵ��/����/¡�� (�ҵ�� 109 ��ä�ͧ)', '09', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_ACTIVE)
							VALUES ('2006', '��. 20.6', '�����������/Ŵ��/����/¡�� (�ҵ�� 109 ��ä���������ä��)', '09', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_ACTIVE)
							VALUES ('2007', '��. 20.7', '�����������/Ŵ��/����/¡�� ŧ��..........  (�ҵ�� 109 ��ä���)', '09', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_ACTIVE)
							VALUES ('2008', '��. 20.8', '�����������/Ŵ��/����/¡�� ŧ��..........  (�ҵ�� 9)', '09', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_ACTIVE)
							VALUES ('2009', '��. 20.9', '�����¡��/����/Ŵ��/������ (�ҵ�� 125 (1) (2) (3))', '09', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_ACTIVE)
							VALUES ('2010', '��. 20.10', '�����������/Ŵ��/����/¡�� �������Ѻ����Ѻ�Ҫ���  (�ҵ�� 125 (4), �ҵ�� 126)', '09', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_ACTIVE)
							VALUES ('1005', '��. 10.5', '���������͹�Թ��͹', '05', 1) ";
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