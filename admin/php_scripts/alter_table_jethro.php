<?
		$cmd = " SELECT COUNT(PL_CODE) AS COUNT_DATA FROM POS_DES_INFO ";
		$count_data = $db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		if (!$count_data) {
			if($DPISDB=="odbc") 
				$cmd = " CREATE TABLE POS_DES_INFO(
				POS_DES_ID INTEGER NOT NULL,	
				PL_CODE VARCHAR(10) NOT NULL,	
				LEVEL_NO VARCHAR(10) NOT NULL,	
				POS_DES_ACTIVE SINGLE NOT NULL,
				UPDATE_USER INTEGER2 NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_POS_DES_INFO PRIMARY KEY (POS_DES_ID)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE POS_DES_INFO(
				POS_DES_ID NUMBER(10) NOT NULL,	
				PL_CODE VARCHAR2(10) NOT NULL,	
				LEVEL_NO VARCHAR2(10) NOT NULL,	
				POS_DES_ACTIVE NUMBER(1) NOT NULL,
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				CONSTRAINT PK_POS_DES_INFO PRIMARY KEY (POS_DES_ID)) ";
			elseif($DPISDB=="mysql")
				$cmd = " CREATE TABLE POS_DES_INFO(
				POS_DES_ID INTEGER(10) NOT NULL,	
				PL_CODE VARCHAR(10) NOT NULL,	
				LEVEL_NO VARCHAR(10) NOT NULL,	
				POS_DES_ACTIVE SMALLINT(1) NOT NULL,
				UPDATE_USER SMALLINT(5) NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_POS_DES_INFO PRIMARY KEY (POS_DES_ID)) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " SELECT MAX(POS_DES_ID) AS POS_DES_ID FROM POS_DES_INFO ";
			$db_dpis1->send_cmd($cmd);
			$data_dpis1 = $db_dpis1->get_array();
			$POS_DES_ID = $data_dpis1[POS_DES_ID] + 1;

			$cmd = " SELECT DISTINCT PL_CODE, LEVEL_NO FROM PER_POSITION WHERE POS_STATUS = 1 AND LEVEL_NO IS NOT NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			while($data = $db_dpis->get_array()){
				$PL_CODE = trim($data[PL_CODE]);
				$LEVEL_NO = trim($data[LEVEL_NO]);

				$cmd = " SELECT POS_DES_ID FROM POS_DES_INFO WHERE PL_CODE = '$PL_CODE' AND LEVEL_NO = '$LEVEL_NO' ";
				$count_data = $db_dpis1->send_cmd($cmd);
				//$db_dpis->show_error();
				if (!$count_data) {
					$cmd = " INSERT INTO POS_DES_INFO (POS_DES_ID, PL_CODE, LEVEL_NO, POS_DES_ACTIVE, UPDATE_USER, UPDATE_DATE) 
									VALUES($POS_DES_ID, '$PL_CODE', '$LEVEL_NO', 1, $SESS_USERID, '$UPDATE_DATE') ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
					$POS_DES_ID++;
				} // end if
			} // end while						
		} // end if

		$cmd = " SELECT COUNT(JOB_DES_ID) AS COUNT_DATA FROM KNOWLEDGE_INFO ";
		$count_data = $db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		if (!$count_data) {
			if($DPISDB=="odbc") 
				$cmd = " CREATE TABLE KNOWLEDGE_INFO(
				JOB_DES_ID INTEGER NOT NULL,	
				JOB_DES_NAME VARCHAR(100) NULL,	
				JOB_DES_DESCRIPTION MEMO NULL,	
				UPDATE_USER INTEGER2 NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_KNOWLEDGE_INFO PRIMARY KEY (JOB_DES_ID)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE KNOWLEDGE_INFO(
				JOB_DES_ID NUMBER(10) NOT NULL,	
				JOB_DES_NAME VARCHAR2(100) NULL,	
				JOB_DES_DESCRIPTION VARCHAR2(1000) NULL,	
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				CONSTRAINT PK_KNOWLEDGE_INFO PRIMARY KEY (JOB_DES_ID)) ";
			elseif($DPISDB=="mysql")
				$cmd = " CREATE TABLE KNOWLEDGE_INFO(
				JOB_DES_ID INTEGER(10) NOT NULL,	
				JOB_DES_NAME VARCHAR(100) NULL,	
				JOB_DES_DESCRIPTION TEXT NULL,	
				UPDATE_USER SMALLINT(5) NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_KNOWLEDGE_INFO PRIMARY KEY (JOB_DES_ID)) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end if

		$cmd = " INSERT INTO KNOWLEDGE_INFO (JOB_DES_ID, JOB_DES_NAME, JOB_DES_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(1, '������������㹧ҹ', '�дѺ�زԡ���֡����Ф�������Ǫҭ������㹧ҹ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO KNOWLEDGE_INFO (JOB_DES_ID, JOB_DES_NAME, JOB_DES_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(2, '�����������ͧ��������С�����º�Ҫ���', '������������ͧ�����µ�ʹ��������º��ҧ� ����ͧ��㹡�û�Ժѵԧҹ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " SELECT COUNT(JOB_DES_ID) AS COUNT_DATA FROM KNOWLEDGE_LEVEL ";
		$count_data = $db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		if (!$count_data) {
			if($DPISDB=="odbc") 
				$cmd = " CREATE TABLE KNOWLEDGE_LEVEL (
				JOB_DES_ID INTEGER NOT NULL,	
				JOB_DES_LEVEL SINGLE NOT NULL,	
				JOB_DES_LEVEL_DESCRIPTION MEMO NULL,
				UPDATE_USER INTEGER2 NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_KNOWLEDGE_LEVEL  PRIMARY KEY (JOB_DES_ID, JOB_DES_LEVEL)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE KNOWLEDGE_LEVEL (
				JOB_DES_ID NUMBER(10) NOT NULL,	
				JOB_DES_LEVEL NUMBER(1) NOT NULL,	
				JOB_DES_LEVEL_DESCRIPTION VARCHAR2(1000) NULL,
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				CONSTRAINT PK_KNOWLEDGE_LEVEL  PRIMARY KEY (JOB_DES_ID, JOB_DES_LEVEL)) ";
			elseif($DPISDB=="mysql") 
				$cmd = " CREATE TABLE KNOWLEDGE_LEVEL (
				JOB_DES_ID INTEGER(10) NOT NULL,	
				JOB_DES_LEVEL SMALLINT(1) NOT NULL,	
				JOB_DES_LEVEL_DESCRIPTION TEXT NULL,
				UPDATE_USER SMALLINT(5) NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_KNOWLEDGE_LEVEL  PRIMARY KEY (JOB_DES_ID, JOB_DES_LEVEL)) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end if

		$cmd = " INSERT INTO KNOWLEDGE_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(1, 1, '�дѺ��� 1 ��� ��§ҹ�Դ �زԡ���֡���дѺ����˹�������º���������ӡ��ҹ�� (����Ѻ��§ҹ�Դ �ô���زԡ���֡�һ�Ш���§ҹ��ҡ ���ҧ�زԡ���֡�һ�Ш���§ҹ�Դ)', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO KNOWLEDGE_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(1, 2, '�дѺ��� 2 ��� ��§ҹ�Դ ���Ѻ��ԭ�ҵ��������º���������ӡ��ҹ�� �������ö����ء�������������֡����㹡�û�Ժѵ�˹�ҷ����(����Ѻ��§ҹ�Դ �ô���زԡ���֡�һ�Ш���§ҹ��ҡ ���ҧ�زԡ���֡�һ�Ш���§ҹ�Դ)', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO KNOWLEDGE_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(1, 3, '�дѺ��� 3 ��� �դ��������дѺ 2 ����դ�������������ѡ��� �ǤԴ ��ɮբͧ�ҹ�����Ҫվ��軯Ժѵ������ա�������ö�����й������͹�����ҹ��', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO KNOWLEDGE_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(1, 4, '�дѺ��� 4 ��� �դ��������дѺ��� 3 ����դ����������������ҧ��ͧ������ǡѺ�ѡɳЧҹ ��ѡ��� �ǤԴ ��ɮբͧ�ҹ�����Ҫվ��軯Ժѵ����� �������ö���һ���ء���������ҡѺʶҹ��ó��ҧ� ��', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO KNOWLEDGE_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(1, 5, '�дѺ��� 5 ��� �դ��������дѺ��� 4 ����ջ��ʺ��ó���ҧ��ҧ���� �Ҫվ��軯Ժѵ�˹�ҷ������ ����ö���·ʹ����������Ѻ���͹�����ҹ��м����ѧ�Ѻ�ѭ����', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO KNOWLEDGE_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(1, 6, '�дѺ��� 6 ��� �դ��������дѺ��� 5 ����繷������Ѻ����繼������Ǫҭ�����Ҫվ��軯Ժѵ�˹�ҷ���������ͧ�ҡ�ջ��ʺ��ó���Ф���������֡�����С��ҧ��ҧ �繷���֡��㹡�û�Ժѵԧҹ���Ѻ˹��§ҹ������', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO KNOWLEDGE_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(1, 7, '�дѺ��� 7 ��� �դ��������дѺ��� 6 ����繷������Ѻ����繼������Ǫҭ�����Ҫվ��軯Ժѵ�˹�ҷ������ �繷���֡�������������˹��§ҹ���� ����дѺ�٧', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO KNOWLEDGE_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(2, 1, '�дѺ��� 1 ��� ���㨡�������������º�������Ǣ�ͧ�Ѻ�ҹ��Ш��ѹ��軯Ժѵ�����', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO KNOWLEDGE_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(2, 2, '�дѺ��� 2 ��� �դ�������дѺ��� 1 ������㨡�������������º�������Ǣ�ͧ���ҧ�֡�����з�Һ��Ҩ��Ҥӵͺ�ҡ����������բ��ʧ���㹧ҹ��軯Ժѵ�����', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO KNOWLEDGE_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(2, 3, '�дѺ��� 3 ��� �դ�������дѺ��� 2 �������ö��任���ء��������������ѭ�ҷ��Ѻ��͹ �ش��ͧ����㹡����� ���͵ͺ�Ӷ�����ʧ���㹧ҹ��軯Ժѵ��������˹��§ҹ���ͺؤ�ŷ������Ǣ�ͧ��', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO KNOWLEDGE_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(2, 4, '�дѺ��� 4 ��� �դ�������дѺ��� 3 ������� ��������������º���� ����դ�������ѹ��������§�Ѻ��������������º㹧ҹ�������ö�������й���������ӻ�֡����Ҿ����ҡ�Դ����繻ѭ��', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO KNOWLEDGE_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(2, 5, '�дѺ��� 5 ��� �դ�������дѺ��� 4 ����繼������Ǫҭ�ҧ������ ����ö�й� ���ӻ�֡�� �����������˵ؼ���зҧ���㹻�������ͻѭ�ҷ��������Դ��������ҧ�ջ���Է���Ҿ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO KNOWLEDGE_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(2, 6, '', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO KNOWLEDGE_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(2, 7, '', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " SELECT COUNT(JOB_DES_ID) AS COUNT_DATA FROM SKILL_INFO ";
		$count_data = $db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		if (!$count_data) {
			if($DPISDB=="odbc") 
				$cmd = " CREATE TABLE SKILL_INFO(
				JOB_DES_ID INTEGER NOT NULL,	
				JOB_DES_NAME VARCHAR(100) NULL,	
				JOB_DES_DESCRIPTION MEMO NULL,	
				UPDATE_USER INTEGER2 NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_SKILL_INFO PRIMARY KEY (JOB_DES_ID)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE SKILL_INFO(
				JOB_DES_ID NUMBER(10) NOT NULL,	
				JOB_DES_NAME VARCHAR2(100) NULL,	
				JOB_DES_DESCRIPTION VARCHAR2(1000) NULL,	
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				CONSTRAINT PK_SKILL_INFO PRIMARY KEY (JOB_DES_ID)) ";
			elseif($DPISDB=="mysql")
				$cmd = " CREATE TABLE SKILL_INFO(
				JOB_DES_ID INTEGER(10) NOT NULL,	
				JOB_DES_NAME VARCHAR(100) NULL,	
				JOB_DES_DESCRIPTION TEXT NULL,	
				UPDATE_USER SMALLINT(5) NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_SKILL_INFO PRIMARY KEY (JOB_DES_ID)) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end if

		$cmd = " INSERT INTO SKILL_INFO (JOB_DES_ID, JOB_DES_NAME, JOB_DES_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(1, '�ѡ�С�������������', '�ѡ��㹡��������������������ҧ� �����ҧ�ջ���Է���Ҿ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO SKILL_INFO (JOB_DES_ID, JOB_DES_NAME, JOB_DES_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(2, '�ѡ�С���������ѧ���', '�ѡ��㹡�ù������ѧ�������㹧ҹ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO SKILL_INFO (JOB_DES_ID, JOB_DES_NAME, JOB_DES_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(3, '�ѡ�С�äӹǳ', '�ѡ��㹡�÷Ӥ���������ФԴ�ӹǳ�����ŵ�ҧ� �����ҧ�١��ͧ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO SKILL_INFO (JOB_DES_ID, JOB_DES_NAME, JOB_DES_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(4, '�ѡ�С�ú����èѴ��ðҹ������', '����ö���Ǻ��������Ţͧ˹��§ҹ�����ҧ���к� ����繻Ѩ�غѹ�������� �дǡ���ä��Ңͧ������ͧ����������', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " SELECT COUNT(JOB_DES_ID) AS COUNT_DATA FROM SKILL_LEVEL ";
		$count_data = $db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		if (!$count_data) {
			if($DPISDB=="odbc") 
				$cmd = " CREATE TABLE SKILL_LEVEL (
				JOB_DES_ID INTEGER NOT NULL,	
				JOB_DES_LEVEL SINGLE NOT NULL,	
				JOB_DES_LEVEL_DESCRIPTION MEMO NULL,
				UPDATE_USER INTEGER2 NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_SKILL_LEVEL  PRIMARY KEY (JOB_DES_ID, JOB_DES_LEVEL)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE SKILL_LEVEL (
				JOB_DES_ID NUMBER(10) NOT NULL,	
				JOB_DES_LEVEL NUMBER(1) NOT NULL,	
				JOB_DES_LEVEL_DESCRIPTION VARCHAR2(1000) NULL,
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				CONSTRAINT PK_SKILL_LEVEL  PRIMARY KEY (JOB_DES_ID, JOB_DES_LEVEL)) ";
			elseif($DPISDB=="mysql") 
				$cmd = " CREATE TABLE SKILL_LEVEL (
				JOB_DES_ID INTEGER(10) NOT NULL,	
				JOB_DES_LEVEL SMALLINT(1) NOT NULL,	
				JOB_DES_LEVEL_DESCRIPTION TEXT NULL,
				UPDATE_USER SMALLINT(5) NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_SKILL_LEVEL  PRIMARY KEY (JOB_DES_ID, JOB_DES_LEVEL)) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end if

		$cmd = " INSERT INTO SKILL_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(1, 1, '�дѺ��� 1 ��� ����ö�ѹ�֡�������������ͧ�������������������͡�����к���������ҧ�ջ���Է���Ҿ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO SKILL_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(1, 2, '�дѺ��� 2 ��� �շѡ���дѺ��� 1 �������ö������������������鹾�鹰ҹ��', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO SKILL_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(1, 3, '�дѺ��� 3 ��� �շѡ���дѺ��� 2 �������ö����������ҧ � 㹡�û�Ժѵԧҹ��', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO SKILL_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(1, 4, '�дѺ��� 4 ��� �շѡ���дѺ��� 3 ������Ѻ�������Ѻ����繼��ӹҭ�����������ҹ�����Ш��˹��§ҹ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO SKILL_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(1, 5, '�дѺ��� 5 ��� �շѡ���дѺ��� 4 ���Ѻ�������Ѻ����繼������Ǫҭ�����������ҹ�����Ш��˹��§ҹ �������ö���ª�������ͫ������ҹ��������������������������������ҹ�����ʺ�ѭ����', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO SKILL_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(1, 6, '�дѺ��� 6 ��� �շѡ���дѺ��� 5 ����դ�����������������������Ǣ�ͧ㹧ҹ ����ö���һ���ء�������ª��㹧ҹ���', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO SKILL_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(1, 7, '�дѺ��� 7 ��� �շѡ���дѺ��� 6 ����դ�������Ǫҭ�����������ҧ������㹧ҹ������������� ����Ҩ�繻���ª�� ��ʹ������ö��¹��������͹��������ª��㹧ҹ��', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO SKILL_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(2, 1, '�дѺ��� 1 ��� ����ö�ٴ ��¹  �����ҹ�����ѧ�����дѺ���ͧ���������������������', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO SKILL_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(2, 2, '�дѺ��� 2 ��� ����ö�ٴ ��¹ ��ҹ��пѧ�����ѧ��� ��зӤ������������Ӥѭ�ͧ�����ҵ�ҧ���', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO SKILL_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(2, 3, '�дѺ��� 3 ��� �շѡ���дѺ��� 2 �������ö�������ѧ������͡�õԴ�������ѹ��㹡�û�Ժѵԧҹ���¶١��ѡ���ҡó�', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO SKILL_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(2, 4, '�дѺ��� 4 ��� �շѡ���дѺ��� 3 ����ö�������ѧ������͡�õԴ����������㹧ҹ���¶١��ա���ҡó�����ջ���Է���Ҿ�����������ЪѴਹ�ú��ǹ��������ʧ��', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO SKILL_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(2, 5, '�дѺ��� 5 ��� �շѡ���дѺ��� 4 ����ö�������ѧ������͡�õԴ����������㹧ҹ���¶١��ѡ���ҡó� �ա��駶١��ͧ���������ԧ�����������ԧ���ὧ����', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO SKILL_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(2, 6, '�дѺ��� 6 ��� �շѡ���дѺ��� 5 ��������ӹǹ�����ѧ�����ٻẺ��ҧ� ����ö����ء����㹧ҹ�����ҧ�١��ͧ�����ԧ���ҡó� �ԧ������ ��й��ὧ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO SKILL_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(2, 7, '�дѺ��� 7 ��� �դ�������Ǫҭ㹡���������ѧ������ҧ�֡��������§�Ѻ��Ңͧ���� ����ö����ء��������÷ء�ٻẺ�����ҧ���ͧ�������������¶١��ͧ �ա����դ�������Ǫҭ�Ѿ��੾�д�ҹ��Ң��ԪҢͧ�����ҧ�֡���', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO SKILL_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(3, 1, '�дѺ��� 1 ��� ����ö�ӹǳ��鹰ҹ�����ҧ���ͧ���� �Ǵ���� ��ж١��ͧ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO SKILL_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(3, 2, '�дѺ��� 2 ��� �շѡ���дѺ��� 1 �������ö�Ӥ������㨢����ŵ���Ţ�����ҧ�١��ͧ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO SKILL_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(3, 3, '�дѺ��� 3 ��� �շѡ���дѺ��� 2 �������ö������������ŵ���Ţ�����ҧ�ջ���Է���Ҿ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO SKILL_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(3, 4, '�дѺ��� 4 ��� �շѡ���дѺ��� 3 �������ö�к� ��ʹ����䢢�ͼԴ��Ҵ㹢����ŵ���Ţ ����������еդ��������ŵ���Ţ�����ҧ�ջ���Է���Ҿ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO SKILL_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(3, 5, '�дѺ��� 5 ��� �շѡ���дѺ��� 4 �������ö��ػ�� ���ʹͻ�����Ӥѭ� ��ʹ��������ʹ�������ҧ�ԧ�š��������������ŵ���Ţ��', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO SKILL_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(3, 6, '�дѺ��� 6 ��� �շѡ���дѺ��� 5 �������ö���ٵä�Ե��ʵ���ҧ�㹡��������������ŵ���Ţ����������� ���ʹ� ������ػ����繷��Ѻ��͹��', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO SKILL_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(3, 7, '�дѺ��� 7 ��� �շѡ���дѺ��� 6 �������ö�Ӣ����ŵ���Ţ��ҧ� ��������§����͸Ժ�ª��ᨧ�������дѺ���ط�� ��ʹ�����ʹͷҧ���͡ ��ʹժ�����µ�ҧ� ����繷���������§���', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO SKILL_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(4, 1, '�дѺ��� 1 ��� ����ö���Ǻ��������Ţͧ˹��§ҹ�����ҧ���к� ����繻Ѩ�غѹ�������� �дǡ���ä��Ңͧ������ͧ����������', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO SKILL_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(4, 2, '�дѺ��� 2 ��� �շѡ����дѺ��� 1 �������ö�ʴ��Ţ�������ٻẺ��ҧ� �� ��ҿ ��§ҹ ��� ��', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO SKILL_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(4, 3, '�дѺ��� 3 ��� �շѡ���дѺ��� 2 �������ö��˹��Ը��������� ��л����Թ�Ţ����������ҧ�١��ͧ�����', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO SKILL_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(4, 4, '�дѺ��� 4 ��� �շѡ���дѺ��� 3 �������ö��ػ�š���������� ���ʹͷҧ���͡ ��ʹբ������ ��� ����ҧ�ԧ�����ŷ��������', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO SKILL_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(4, 5, '�дѺ��� 5 ��� �շѡ���дѺ��� 4  �������ö������� �ʹͷҧ�͡��ԧ���ط��ͧ����ͧ��ҧ� ��������������� ����ҧ�ԧ�Ũҡ����������������', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO SKILL_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(4, 6, '�дѺ��� 6 ��� �շѡ���дѺ��� 5  �������ö��˹���ͺ �Ƿҧ ����Ըա�ú����èѴ��ðҹ�����Ţ�Ҵ�˭������ҧ���к�', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO SKILL_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(4, 7, '�дѺ��� 7 ��� �շѡ���дѺ��� 6 �������ö�͡Ẻ ���ͻ���ء����Ẻ���ͧ (Model) ��ҧ� ����㹡���������� �����èѴ��� ��������ª��ҡ�ҹ��������', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " SELECT COUNT(JOB_DES_ID) AS COUNT_DATA FROM EXP_INFO ";
		$count_data = $db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		if (!$count_data) {
			if($DPISDB=="odbc") 
				$cmd = " CREATE TABLE EXP_INFO(
				JOB_DES_ID INTEGER NOT NULL,	
				JOB_DES_NAME VARCHAR(100) NULL,	
				JOB_DES_DESCRIPTION MEMO NULL,	
				UPDATE_USER INTEGER2 NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_EXP_INFO PRIMARY KEY (JOB_DES_ID)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE EXP_INFO(
				JOB_DES_ID NUMBER(10) NOT NULL,	
				JOB_DES_NAME VARCHAR2(100) NULL,	
				JOB_DES_DESCRIPTION VARCHAR2(1000) NULL,	
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				CONSTRAINT PK_EXP_INFO PRIMARY KEY (JOB_DES_ID)) ";
			elseif($DPISDB=="mysql")
				$cmd = " CREATE TABLE EXP_INFO(
				JOB_DES_ID INTEGER(10) NOT NULL,	
				JOB_DES_NAME VARCHAR(100) NULL,	
				JOB_DES_DESCRIPTION TEXT NULL,	
				UPDATE_USER SMALLINT(5) NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_EXP_INFO PRIMARY KEY (JOB_DES_ID)) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end if

		$cmd = " INSERT INTO EXP_INFO (JOB_DES_ID, JOB_DES_NAME, JOB_DES_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(1, '�����繵�ͧ�ջ��ʺ��ó��ҡ�͹', '�����繵�ͧ�ջ��ʺ��ó��ҡ�͹', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO EXP_INFO (JOB_DES_ID, JOB_DES_NAME, JOB_DES_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(2, '���ʺ��ó�㹧ҹ�������Ǣ�ͧ', '�ջ��ʺ��ó�㹧ҹ�������Ǣ�ͧ���ӡ��ҷ���˹����', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " SELECT COUNT(JOB_DES_ID) AS COUNT_DATA FROM COMPETENCY_INFO ";
		$count_data = $db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		if (!$count_data) {
			if($DPISDB=="odbc") 
				$cmd = " CREATE TABLE COMPETENCY_INFO(
				JOB_DES_ID INTEGER NOT NULL,	
				JOB_DES_NAME VARCHAR(100) NULL,	
				JOB_DES_DESCRIPTION MEMO NULL,	
				COMPETENCY_TYPE VARCHAR(50) NULL,	
				UPDATE_USER INTEGER2 NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_COMPETENCY_INFO PRIMARY KEY (JOB_DES_ID)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE COMPETENCY_INFO(
				JOB_DES_ID NUMBER(10) NOT NULL,	
				JOB_DES_NAME VARCHAR2(100) NULL,	
				JOB_DES_DESCRIPTION VARCHAR2(1000) NULL,	
				COMPETENCY_TYPE VARCHAR2(50) NULL,	
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				CONSTRAINT PK_COMPETENCY_INFO PRIMARY KEY (JOB_DES_ID)) ";
			elseif($DPISDB=="mysql")
				$cmd = " CREATE TABLE COMPETENCY_INFO(
				JOB_DES_ID INTEGER(10) NOT NULL,	
				JOB_DES_NAME VARCHAR(100) NULL,	
				JOB_DES_DESCRIPTION TEXT NULL,	
				COMPETENCY_TYPE VARCHAR(50) NULL,	
				UPDATE_USER SMALLINT(5) NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_COMPETENCY_INFO PRIMARY KEY (JOB_DES_ID)) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end if

		$cmd = " INSERT INTO COMPETENCY_INFO (JOB_DES_ID, JOB_DES_NAME, JOB_DES_DESCRIPTION, COMPETENCY_TYPE, UPDATE_USER, UPDATE_DATE) 
						VALUES(1, '�����觼����ķ��� (Achievement Motivation-ACH)', '���������蹨л�Ժѵ��Ҫ���������������Թ�ҵðҹ��������� ���ҵðҹ����Ҩ�繼š�û�Ժѵԧҹ����ҹ�Ңͧ���ͧ ����ࡳ���Ѵ�����ķ�������ǹ�Ҫ��á�˹���� �ա����ѧ��������֧������ҧ��ä�Ѳ�Ҽŧҹ���͡�кǹ��û�Ժѵԧҹ���������·���ҡ��з�ҷ�ª�Դ����Ҩ������ռ�������ö��з����ҡ�͹', '���ö����ѡ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_INFO (JOB_DES_ID, JOB_DES_NAME, JOB_DES_DESCRIPTION, COMPETENCY_TYPE, UPDATE_USER, UPDATE_DATE) 
						VALUES(2, '��ԡ�÷��� (Service Mind-SERV)', '���ö�й���鹤���������Ф����������ͧ����Ҫ���㹡������ԡ������ʹͧ������ͧ��âͧ��ЪҪ���ʹ���ͧ˹��§ҹ�Ҥ�Ѱ����������Ǣ�ͧ', '���ö����ѡ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_INFO (JOB_DES_ID, JOB_DES_NAME, JOB_DES_DESCRIPTION, COMPETENCY_TYPE, UPDATE_USER, UPDATE_DATE) 
						VALUES(3, '����������������Ǫҭ㹧ҹ�Ҫվ (Expertise-EXP)', '�����ǹ���� ʹ������ ���������
�Ѳ���ѡ��Ҿ ��������������ö�ͧ��㹡�û�Ժѵ��Ҫ��� ���¡���֡�� �鹤����Ҥ������ �Ѳ�ҵ��ͧ���ҧ������ͧ �ա������ѡ�Ѳ�� ��Ѻ��ا ����ء�����������ԧ�Ԫҡ�����෤����յ�ҧ� ��ҡѺ��û�Ժѵԧҹ����Դ�����ķ���', '���ö����ѡ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_INFO (JOB_DES_ID, JOB_DES_NAME, JOB_DES_DESCRIPTION, COMPETENCY_TYPE, UPDATE_USER, UPDATE_DATE) 
						VALUES(4, '���¸��� (Integrity-ING)', '��ä�ͧ����л�оĵԻ�ԺѵԶ١��ͧ���������駵����ѡ��������Фس�������¸��� ��ʹ����ѡ�Ƿҧ��ԪҪվ�ͧ������觻���ª��ͧ����Ȫҵ��ҡ���һ���ª����ǹ��  ��駹�����͸�ç�ѡ���ѡ����������Ҫվ����Ҫ��� �ա��������繡��ѧ�Ӥѭ㹡��ʹѺʹع��ѡ�ѹ�����áԨ��ѡ�Ҥ�Ѱ�����������·���˹����', '���ö����ѡ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_INFO (JOB_DES_ID, JOB_DES_NAME, JOB_DES_DESCRIPTION, COMPETENCY_TYPE, UPDATE_USER, UPDATE_DATE) 
						VALUES(5, '���������ç����� (Teamwork-TW)', '���ö�й���鹷�� 1) �������㨷��зӧҹ�����Ѻ������ ����ǹ˹��㹷���ҹ ˹��§ҹ ����ͧ��� �¼�黯Ժѵ��հҹ�����Ҫԡ㹷�� ����㹰ҹ����˹�ҷ�� ��� 2) ��������ö㹡�����ҧ��д�ç�ѡ������ѹ��Ҿ�Ѻ��Ҫԡ㹷��', '���ö����ѡ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_INFO (JOB_DES_ID, JOB_DES_NAME, JOB_DES_DESCRIPTION, COMPETENCY_TYPE, UPDATE_USER, UPDATE_DATE) 
						VALUES(6, '��äԴ�������� (Analytical Thinking-AT)', '��÷Ӥ�������ʶҹ��ó� ����繻ѭ�� �ǤԴ ��ѡ��ɮ� ��� �¡��ᨡᨧᵡ������͡����ǹ����� ������������ʶҹ��ó���Т�鹵͹ ����֧��èѴ��Ǵ����ѭ������ʶҹ��ó����ҧ���к�����º ���º��º�������ҧ� ����ö�к�����������Դ��͹��ѧ ��ʹ���к��˵���м� ����ҷ��仢ͧ�óյ�ҧ���', '���ö�л�Шӡ�����ҹ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_INFO (JOB_DES_ID, JOB_DES_NAME, JOB_DES_DESCRIPTION, COMPETENCY_TYPE, UPDATE_USER, UPDATE_DATE) 
						VALUES(7, '����ͧ�Ҿͧ����� (Conceptual Thinking-CT)', '��äԴ��ԧ�ѧ������ �ͧ�Ҿͧ����������繡�ͺ�����Դ�����ǤԴ���� �ѹ�繼��Ҩҡ�����ػ�ٻẺ ����ء���Ƿҧ��ҧ�ҡʶҹ��ó����͢�������ҡ���� ��йҹҷ�ȹ�', '���ö�л�Шӡ�����ҹ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_INFO (JOB_DES_ID, JOB_DES_NAME, JOB_DES_DESCRIPTION, COMPETENCY_TYPE, UPDATE_USER, UPDATE_DATE) 
						VALUES(8, '��þѲ���ѡ��Ҿ�� (Caring & Developing Others-DEV)', '�������㨨��������������¹������͡�þѲ�Ҽ������������� ������鹷��ਵ�ҷ��оѲ�Ҽ�������мŷ���Դ����ҡ������§��Ժѵ�仵��˹�ҷ��', '���ö�л�Шӡ�����ҹ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_INFO (JOB_DES_ID, JOB_DES_NAME, JOB_DES_DESCRIPTION, COMPETENCY_TYPE, UPDATE_USER, UPDATE_DATE) 
						VALUES(9, '�����觡�õ���ӹҨ˹�ҷ�� (Holding People Accountable-HPA)', 'ਵ�ҷ��СӡѺ�����������蹻�Ժѵ���������ҵðҹ ������º��ͺѧ�Ѻ����˹���� ��������ӹҨ�������º������ ���͵�����˹�˹�ҷ�������������ҧ�����������ջ���Է���Ҿ����觻���ª��ͧͧ�����л���Ȫҵ����Ӥѭ �����觡�õ���ӹҨ˹�ҷ�����Ҩ����֧��Ó�͡����觔 ����յ�����дѺ��觧ҹ�á�Է���仨��֧�дѺ��èѴ��â���索Ҵ�Ѻ����ҽ׹', '���ö�л�Шӡ�����ҹ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_INFO (JOB_DES_ID, JOB_DES_NAME, JOB_DES_DESCRIPTION, COMPETENCY_TYPE, UPDATE_USER, UPDATE_DATE) 
						VALUES(10, '����׺�����Ң����� (Information Seeking-INF)', '����ʹ�����������ǡѺʶҹ��ó� ������ѧ ����ѵԤ������� ����� �ѭ�� ��������ͧ��ǵ�ҧ� �������Ǣ�ͧ���ͨ��繵�ͧҹ�˹�ҷ�� �س�ѡɳй���Ҩ����֧����׺���� ��������������੾����Ш� ���䢻����ȹ��«ѡ����������´ ������������Ң��Ƿ���仨ҡ��Ҿ�Ǵ�����ͺ����¤Ҵ����Ҩ�բ����ŷ����繻���ª������͹Ҥ�', '���ö�л�Шӡ�����ҹ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_INFO (JOB_DES_ID, JOB_DES_NAME, JOB_DES_DESCRIPTION, COMPETENCY_TYPE, UPDATE_USER, UPDATE_DATE) 
						VALUES(11, '�������㨢��ᵡ��ҧ�ҧ�Ѳ����� (Cultural Sensitivity-CS)', '�������˹ѡ�֧���ᵡ��ҧ�����ҧ�Ѳ������������ö����ء����������㨹�� �������ҧ����������  ����ѹ��Ҿ��ҧ�Ѳ����������Ե��������Ф�����������ѹ�������ҧ�Ҫ�ҳҨѡ�����йҹһ����', '���ö�л�Шӡ�����ҹ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_INFO (JOB_DES_ID, JOB_DES_NAME, JOB_DES_DESCRIPTION, COMPETENCY_TYPE, UPDATE_USER, UPDATE_DATE) 
						VALUES(12, '�������㨼����� (Interpersonal Understanding-IU)', '��������ö㹡���Ѻ�ѧ������㨷�駤������µç��Ф�������ὧ ��ʹ�������������ͧ�����Դ��ʹ���', '���ö�л�Шӡ�����ҹ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_INFO (JOB_DES_ID, JOB_DES_NAME, JOB_DES_DESCRIPTION, COMPETENCY_TYPE, UPDATE_USER, UPDATE_DATE) 
						VALUES(13, '��������ͧ�������к��Ҫ��� (Organizational Awareness-OA)', '���������������ö����ء�����������ѹ��������§�ͧ������ӹҨ��駷���繷ҧ����������繷ҧ����ͧ��âͧ�����ͧ������� �������Ǣ�ͧ���ͻ���ª��㹡�û�Ժѵ�˹�ҷ��������ؼ� �������㨹������֧��������ö�Ҵ��ó�����ҹ�º���Ҥ�Ѱ �ǤԴ�����ҧ������ͧ ���ɰ�Ԩ �ѧ�� ෤����� ��� ��ʹ���˵ء�ó�����ʶҹ��ó��ҧ����غѵԢ�鹨��ռŵ��ͧ��������áԨ��赹��Ժѵ��������ҧ��', '���ö�л�Шӡ�����ҹ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_INFO (JOB_DES_ID, JOB_DES_NAME, JOB_DES_DESCRIPTION, COMPETENCY_TYPE, UPDATE_USER, UPDATE_DATE) 
						VALUES(14, '��ô��Թ����ԧ�ء (Proactiveness-PROAC)', '��������繻ѭ�������͡�ʾ�������ŧ��ͨѴ��áѺ�ѭ�ҹ��� �������͡�ʷ���Դ�������Դ����ª���ͧҹ �����Ըա�÷�����ҧ��ä�����š����', '���ö�л�Шӡ�����ҹ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_INFO (JOB_DES_ID, JOB_DES_NAME, JOB_DES_DESCRIPTION, COMPETENCY_TYPE, UPDATE_USER, UPDATE_DATE) 
						VALUES(15, '�����١��ͧ�ͧ�ҹ (Concern for Order-CO)', '�������������л�Ժѵԧҹ���١��ͧ�ú��ǹ��ʹ��Ŵ��ͺ����ͧ����Ҩ���Դ��� �����駤�������������Դ�����Ѵਹ���㹺��ҷ˹�ҷ�� ������ ����º��ͺѧ�Ѻ ��鹵͹��ԺѵԵ�ҧ�', '���ö�л�Шӡ�����ҹ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_INFO (JOB_DES_ID, JOB_DES_NAME, JOB_DES_DESCRIPTION, COMPETENCY_TYPE, UPDATE_USER, UPDATE_DATE) 
						VALUES(16, '��������㹵��ͧ (Self Confidence-SCF)', '��������㹤�������ö �ѡ��Ҿ ��ʹ���Ԩ�ó�ҳ��õѴ�Թ㨢ͧ�����л�Ժѵԧҹ������ؼ� �������͡�Ըշ���ջ���Է���Ҿ㹡�û�Ժѵԧҹ ������䢻ѭ��������������ǧ ', '���ö�л�Шӡ�����ҹ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_INFO (JOB_DES_ID, JOB_DES_NAME, JOB_DES_DESCRIPTION, COMPETENCY_TYPE, UPDATE_USER, UPDATE_DATE) 
						VALUES(17, '�����״���蹼�͹�ù (Flexibility-FLX)', '��������ö㹡�û�Ѻ�����ҡѺʶҹ��ó���С�����������ҡ���� 㹢�з���ѧ����Ժѵԧҹ�����ҧ�� ����Է���Ҿ ���¤�������֧�������Ѻ�����Դ��繢ͧ������ ��л�Ѻ����¹�Ըա�������ʶҹ��ó��Ǵ��������¹�', '���ö�л�Шӡ�����ҹ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_INFO (JOB_DES_ID, JOB_DES_NAME, JOB_DES_DESCRIPTION, COMPETENCY_TYPE, UPDATE_USER, UPDATE_DATE) 
						VALUES(18, '��ŻС��������è٧� (Communication & Influencing-CI)', '�������㨷������ͤ������¡����¹ �ٴ �������͵�ҧ� ��ʹ����êѡ�٧ ���ҹ���� ������� �ؤ����� ��з��������蹻�зѺ� �����������ʹѺʹع�����Դ�ͧ��', '���ö�л�Шӡ�����ҹ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_INFO (JOB_DES_ID, JOB_DES_NAME, JOB_DES_DESCRIPTION, COMPETENCY_TYPE, UPDATE_USER, UPDATE_DATE) 
						VALUES(19, '����м��� (Leadership-LEAD)', '�����������ͤ�������ö㹡���繼��Ӣͧ������� ����ͧ ����֧��á�˹���ȷҧ ����·�ȹ� ������� �Ըա�÷ӧҹ �������ѧ�Ѻ�ѭ�����ͷ���ҹ��Ժѵԧҹ�����ҧ�Һ��� �������Է���Ҿ��к�����ѵ�ػ��ʧ��ͧͧ���', '���ö�л�Шӡ�����ҹ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_INFO (JOB_DES_ID, JOB_DES_NAME, JOB_DES_DESCRIPTION, COMPETENCY_TYPE, UPDATE_USER, UPDATE_DATE) 
						VALUES(20, '�ع�����Ҿ�ҧ��Ż� (Aesthetic Quality-AQ)', '�����Һ������ö�ʢͧ�ҹ��Ż��Сͺ�Ѻ��������繤س��Ңͧ�ҹ����ҹ��㹰ҹз�����͡�ѡɳ�����ô��ͧ�ҵ� ��й��һ�Ѻ��㹡�����ҧ��ä�ҹ��Ż�ͧ��', '���ö�л�Шӡ�����ҹ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_INFO (JOB_DES_ID, JOB_DES_NAME, JOB_DES_DESCRIPTION, COMPETENCY_TYPE, UPDATE_USER, UPDATE_DATE) 
						VALUES(21, '����·�ȹ� (Visioning - VIS)', '��������ö����ȷҧ���Ѵਹ��С�ͤ��������ç��������������ѧ�Ѻ�ѭ�����͹Ӿҧҹ�Ҥ�Ѱ����ش���������ѹ', '���ö�л�ШӼ�������', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_INFO (JOB_DES_ID, JOB_DES_NAME, JOB_DES_DESCRIPTION, COMPETENCY_TYPE, UPDATE_USER, UPDATE_DATE) 
						VALUES(22, '����ҧ���ط���Ҥ�Ѱ (Strategic Orientation-SO)', '�������㨡��ط���Ҥ�Ѱ�������ö����ء����㹡�á�˹����ط��ͧ˹��§ҹ���� �¤�������ö㹡�û���ء��������֧��������ö㹡�äҴ��ó�֧��ȷҧ�к��Ҫ����͹Ҥ� ��ʹ���š�з��ͧʶҹ��ó������е�ҧ����ȷ���Դ���', '���ö�л�ШӼ�������', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_INFO (JOB_DES_ID, JOB_DES_NAME, JOB_DES_DESCRIPTION, COMPETENCY_TYPE, UPDATE_USER, UPDATE_DATE) 
						VALUES(23, '�ѡ��Ҿ���͹ӡ�û�Ѻ����¹ (Change Leadership-CL)', '����������Ф�������ö㹡�á�е�鹼�ѡ�ѹ�����������Դ������ͧ��èл�Ѻ����¹���Ƿҧ����繻���ª�����Ҥ�Ѱ ����֧�������������������Ѻ��� ���� ��д��Թ�������û�Ѻ����¹����Դ��鹨�ԧ', '���ö�л�ШӼ�������', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_INFO (JOB_DES_ID, JOB_DES_NAME, JOB_DES_DESCRIPTION, COMPETENCY_TYPE, UPDATE_USER, UPDATE_DATE) 
						VALUES(24, '��äǺ������ͧ (Self Control-SCT)', '����ЧѺ��������оĵԡ����ѹ��������������Ͷ١������ ����༪ԭ˹�ҡѺ���µç���� ༪ԭ����������Ե� ���ͷӧҹ���������Ф������ѹ ����֧����ʹ��ʹ��������͵�ͧ���������ʶҹ��ó����ͤ������´���ҧ������ͧ', '���ö�л�ШӼ�������', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_INFO (JOB_DES_ID, JOB_DES_NAME, JOB_DES_DESCRIPTION, COMPETENCY_TYPE, UPDATE_USER, UPDATE_DATE) 
						VALUES(25, '�������ӹҨ������� (Empowering Others - EMP)', '�����������㹤�������ö�ͧ������ �ѧ��鹨֧�ͺ�����ӹҨ���˹�ҷ���Ѻ�Դ�ͺ����������������������㹡�����ҧ��ä��Ըա�âͧ�����ͺ�����������㹧ҹ', '���ö�л�ШӼ�������', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_INFO (JOB_DES_ID, JOB_DES_NAME, JOB_DES_DESCRIPTION, COMPETENCY_TYPE, UPDATE_USER, UPDATE_DATE) 
						VALUES(26, '�����Դ���ҧ��ä�', '�����Դ���ҧ��ä�', '���ö�л�Шӡ�����ҹ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " SELECT COUNT(JOB_DES_ID) AS COUNT_DATA FROM COMPETENCY_LEVEL ";
		$count_data = $db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		if (!$count_data) {
			if($DPISDB=="odbc") 
				$cmd = " CREATE TABLE COMPETENCY_LEVEL (
				JOB_DES_ID INTEGER NOT NULL,	
				JOB_DES_LEVEL SINGLE NOT NULL,	
				JOB_DES_LEVEL_DESCRIPTION MEMO NULL,
				UPDATE_USER INTEGER2 NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_COMPETENCY_LEVEL  PRIMARY KEY (JOB_DES_ID, JOB_DES_LEVEL)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE COMPETENCY_LEVEL (
				JOB_DES_ID NUMBER(10) NOT NULL,	
				JOB_DES_LEVEL NUMBER(1) NOT NULL,	
				JOB_DES_LEVEL_DESCRIPTION VARCHAR2(1000) NULL,
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				CONSTRAINT PK_COMPETENCY_LEVEL  PRIMARY KEY (JOB_DES_ID, JOB_DES_LEVEL)) ";
			elseif($DPISDB=="mysql") 
				$cmd = " CREATE TABLE COMPETENCY_LEVEL (
				JOB_DES_ID INTEGER(10) NOT NULL,	
				JOB_DES_LEVEL SMALLINT(1) NOT NULL,	
				JOB_DES_LEVEL_DESCRIPTION TEXT NULL,
				UPDATE_USER SMALLINT(5) NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_COMPETENCY_LEVEL  PRIMARY KEY (JOB_DES_ID, JOB_DES_LEVEL)) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end if

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(1, 0, '�дѺ��� 0 ��� ����ʴ����ö�д�ҹ������ҧ�Ѵਹ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(1, 1, '�дѺ��� 1 ��� �ʴ�����������㹡�÷ӧҹ���� �������ӧҹ�˹�ҷ��������ж١��ͧ 
�դ����ҹ�ʹ�� ��ѹ��������㹡�÷ӧҹ ��еç�������
�դ����Ѻ�Դ�ͺ㹧ҹ ����ö�觧ҹ������˹�����
�ʴ��͡��ҵ�ͧ��÷ӧҹ�����բ�� �� ����֧�Ըա�� ���͢��й����ҧ��е������� ʹ�������
�ʴ����������ԧ��Ѻ��ا�Ѳ������������觷��������Դ����٭���� �������͹����Է���Ҿ㹧ҹ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(1, 2, '�дѺ��� 2 ��� �ʴ����ö���дѺ��� 1 �������ö�ӧҹ��ŧҹ���������·���ҧ���
��˹��ҵðҹ �����������㹡�÷ӧҹ���������ŧҹ����
���蹵Դ����ŧҹ ��л����Թ�ŧҹ�ͧ�� ����ࡳ�����˹���� �������١�ѧ�Ѻ �� �����Ҽŧҹ�������ѧ ���͵�ͧ��Ѻ��ا���è֧�дբ��
�ӧҹ�����ŧҹ���������·����ѧ�Ѻ�ѭ�ҡ�˹� ����������¢ͧ˹��§ҹ����Ѻ�Դ�ͺ
�դ��������´�ͺ�ͺ������� ��Ǩ��Ҥ����١��ͧ�ͧ�ҹ ���������ҹ����դس�Ҿ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(1, 3, '�дѺ��� 3 ��� �ʴ����ö���дѺ��� 2 �������ö�ӧҹ��ŧҹ����ջ���Է���Ҿ�ҡ��觢��
��Ѻ��ا�Ըա�÷������ӧҹ��բ�� ���Ǣ�� �դس�Ҿ�բ�� �����ջ���Է���Ҿ�ҡ���
�ʹ����ͷ��ͧ�Ըա�÷ӧҹẺ�������ջ���Է���Ҿ�ҡ������� ���������ŧҹ�������˹����', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(1, 4, '�дѺ��� 4 ��� �ʴ����ö���дѺ��� 3 �������ö�Ѳ���Ըա�÷ӧҹ ���������ŧҹ���ⴴ�� ���ᵡ��ҧ���ҧ��������÷����ҡ�͹ 
��˹�������·���ҷ�� ���������ҡ ���ͷ������ŧҹ���ա���������ҧ�����Ѵ
�ӡ�þѲ���к� ��鹵͹ �Ըա�÷ӧҹ ���������ŧҹ���ⴴ�� ���ᵡ��ҧ��������÷����ҡ�͹', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(1, 5, '�дѺ��� 5 ��� �ʴ����ö���дѺ��� 4 �������ö�Ѵ�Թ��� �����դ�������§ �������ͧ��ú�����������
�Ѵ�Թ��� ���ա�äӹǳ������������ҧ�Ѵਹ ��д��Թ��� ��������Ҥ�Ѱ��л�ЪҪ������ª���٧�ش
�����èѴ�����з�������� ��ʹ����Ѿ�ҡ� ������������ª���٧�ش�����áԨ�ͧ˹��§ҹ�������ҧἹ���', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(2, 0, '�дѺ��� 0 ��� ����ʴ����ö�д�ҹ������ҧ�Ѵਹ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(2, 1, '�дѺ��� 1 ��� �ʴ���������㹡������ԡ��
����ú�ԡ�÷�����Ե� ���Ҿ ���㨵�͹�Ѻ
����ԡ�ô����Ѹ�����������ѹ�� ������ҧ������зѺ������Ѻ��ԡ�� 
�����й� ��Ф�µԴ�������ͧ ����ͼ���Ѻ��ԡ���դӶ�� ������¡��ͧ�������ǡѺ��áԨ�ͧ˹��§ҹ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(2, 2, '�дѺ��� 2 ��� �ʴ����ö���дѺ��� 1 �������ö����ԡ�÷�����Ѻ��ԡ�õ�ͧ�����
�������� ������� �ͧ��ú�ԡ�÷��١��ͧ �Ѵਹ�����Ѻ��ԡ�����ʹ�������ԡ��
��������Ѻ��ԡ�÷�Һ�����׺˹��㹡�ô��Թ����ͧ ���͢�鹵͹�ҹ��ҧ� �������ԡ������
����ҹ�ҹ����˹��§ҹ ��СѺ˹��§ҹ�������Ǣ�ͧ ����������Ѻ��ԡ�����Ѻ��ԡ�÷�������ͧ����Ǵ����', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(2, 3, '�дѺ��� 3 ��� �ʴ����ö���дѺ��� 2 ������㨪�����ѭ�����Ѻ����ԡ����
�Ѻ�繸��� ������ѭ���������Ƿҧ��䢻ѭ�ҷ���Դ��������Ѻ��ԡ�����ҧ�Ǵ����  ���� ���������§ ������� ���ͻѴ����
��´���������Ѻ��ԡ�����Ѻ�����֧��� ��йӢ�͢Ѵ��ͧ�� 㹡������ԡ�� (�����) 仾Ѳ�ҡ������ԡ��������觢��', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(2, 4, '�дѺ��� 4 ��� �ʴ����ö���дѺ��� 3 �������ԡ�÷���Թ�����Ҵ��ѧ��дѺ����� ����ͧ���������ͤ������������ҧ�ҡ 
������������Ѻ��ԡ�� ��੾������ͼ���Ѻ��ԡ�û��ʺ�����ҡ�Ӻҡ �� ���������Ф��������������㹡������ԡ�� ���ͪ��¼���Ѻ��ԡ����ѭ��
����������� ������� �������������Ǣ�ͧ�Ѻ�ҹ�����ѧ����ԡ������ ����繻���ª�������Ѻ��ԡ�� �����Ҽ���Ѻ��ԡ�è���������֧ ��������Һ�ҡ�͹
����ԡ�÷���Թ�����Ҵ��ѧ��дѺ�����', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(2, 5, '�дѺ��� 5 ��� �ʴ����ö���дѺ��� 4 �������ö�����������ԡ�÷��ç���������ͧ��÷�����ԧ�ͧ����Ѻ��ԡ���� 
���㨤����������ͤ�����ͧ��÷�����ԧ�ͧ����Ѻ��ԡ�� ���/���� ��������ǧ�Ң�������зӤ�����������ǡѺ�����������ͤ�����ͧ��÷�����ԧ�ͧ����Ѻ��ԡ��
�����йӷ���繻���ª�������Ѻ��ԡ�� ���͵ͺʹͧ�����������ͤ�����ͧ��÷�����ԧ�ͧ����Ѻ��ԡ��', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(3, 0, '�дѺ��� 0 ��� ����ʴ����ö�д�ҹ������ҧ�Ѵਹ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(3, 1, '�дѺ��� 1 ��� �ʴ�����ʹ���еԴ��������������� ��Ң��Ҫվ�ͧ��/�������Ǣ�ͧ 
��е�������㹡���֡���Ҥ������ ʹ�෤��������ͧ������������� ��Ң��Ҫվ�ͧ��
���蹷��ͧ�Ըա�÷ӧҹẺ���� ���;Ѳ�һ���Է���Ҿ��Ф�������������ö�ͧ��������觢��
�Դ���෤�����ͧ������������� �������ʹ��¡���׺�鹢����Ũҡ���觵�ҧ� �����繻���ª���͡�û�Ժѵ��Ҫ���', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(3, 2, '�дѺ��� 2 ��� �ʴ����ö���дѺ��� 1 ����դ��������Ԫҡ�� ���෤����������  ��Ң��Ҫվ�ͧ�� 
�ͺ�����ҷѹ෤���������ͧ������������� ��Ң��Ҫվ�ͧ����з������Ǣ�ͧ �����Ҩ�ռš�з���͡�û�Ժѵ�˹�ҷ��ͧ�� 
�Դ���������Է�ҡ�÷��ѹ���� ���෤����շ������Ǣ�ͧ�Ѻ�ҹ���ҧ������ͧ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(3, 3, '�дѺ��� 3 ��� �ʴ����ö���дѺ��� 2 �������ö�Ӥ������ �Է�ҡ�� ����෤���������� ������֡���һ�Ѻ��Ѻ��÷ӧҹ
���㨻������ѡ� ����Ӥѭ ��мš�з��ͧ�Է�ҡ�õ�ҧ� ���ҧ�֡���
����ö���Ԫҡ�� ������� ����෤���������� �һ���ء����㹡�û�Ժѵԧҹ��
����������������� �������� ��������繻���ª�� �����Ӥѭ�ͧͧ�������� ෤���������� �����觼š�з���ͧҹ�ͧ���͹Ҥ�', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(3, 4, '�дѺ��� 4 ��� �ʴ����ö���дѺ��� 3 ����֡�� �Ѳ�ҵ��ͧ����դ������ ��Ф�������Ǫҭ㹧ҹ�ҡ��� �����ԧ�֡ ����ԧ���ҧ���ҧ������ͧ
�դ�������������Ǫҭ�����ͧ�������ǡѺ�ҹ���´�ҹ (���Է�ҡ��) �������ö�Ӥ������任�Ѻ����黯Ժѵ������ҧ���ҧ��ҧ��ͺ����
����ö�Ӥ�������ԧ��óҡ�âͧ�����㹡�����ҧ����·�ȹ� ���͡�û�Ժѵԧҹ�͹Ҥ�
�ǹ�����Ҥ������������Ǣ�ͧ�Ѻ�ҹ����ԧ�֡����ԧ���ҧ���ҧ������ͧ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(3, 5, '�дѺ��� 5 ��� �ʴ����ö���дѺ��� 4 ���ʹѺʹع��÷ӧҹ�ͧ���ͧ��÷���鹤�������Ǫҭ��Է�ҡ�ô�ҹ��ҧ�
ʹѺʹع����Դ����ҡ����觡�þѲ�Ҥ�������Ǫҭ�ͧ��� ���¡�èѴ��÷�Ѿ�ҡ� ����ͧ��� �ػ�ó�������͵�͡�þѲ��
�����ʹѺʹع ���� ������ռ���ʴ��͡�֧�������㨷��оѲ�Ҥ�������Ǫҭ㹧ҹ
������·�ȹ�㹡�������繻���ª��ͧ෤����� ͧ�������� �����Է�ҡ������� ��͡�û�Ժѵԧҹ�͹Ҥ� ���ʹѺʹع�����������ա�ù��һ���ء�����˹��§ҹ���ҧ������ͧ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(4, 0, '�дѺ��� 0 ��� ����ʴ����ö�д�ҹ������ҧ�Ѵਹ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(4, 1, '�дѺ��� 1 ��� �դ��������ѵ���ب�Ե
��Ժѵ�˹�ҷ����¤��������  �����ѵ���ب�Ե �١��ͧ��駵����ѡ������ ���¸����������º�Թ��
�ʴ������Դ��繢ͧ�������ѡ�ԪҪվ���ҧ�Դ�µç仵ç��', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(4, 2, '�дѺ��� 2 ��� �ʴ����ö���дѺ��� 1 ������Ѩ�����Ͷ����
�ѡ���Ҩ� ���Ѩ�����Ͷ���� �ٴ���ҧ�÷����ҧ��� ���Դ��͹��ҧ���¡�����鵹�ͧ
�ըԵ�ӹ֡��Ф����Ҥ�����㹤����繢���Ҫ��� �ط���ç����ç㨼�ѡ�ѹ�����áԨ��ѡ�ͧ�����˹��§ҹ����ؼ� ����ʹѺʹع���������þѲ�һ���Ȫҵ�����ѧ����', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(4, 3, '�дѺ��� 3 ��� �ʴ����ö���дѺ��� 2 ����ִ������ѡ���
�ִ������ѡ�����Ш���Һ�ó�ͧ�ԪҪվ ������§ູ����ͤ�����ͼŻ���ª����ǹ��
������Ф����آʺ�µ�ʹ�������֧�����ǹ�����ͧ͢��ͺ���� ����������áԨ�˹�ҷ�����ķ�������Ӥѭ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(4, 4, '�дѺ��� 4 ��� �ʴ����ö���дѺ��� 3 ��и�ç�����١��ͧ
��ç�����١��ͧ �׹��Ѵ�Էѡ��Ż���ª����Ъ������§�ͧ����Ȫҵ�����ʶҹ��ó����Ҩ���ҧ�����Ӻҡ����
�Ѵ�Թ��˹�ҷ�� ��Ժѵ��Ҫ��ô��¤����١��ͧ ����� �繸��� ���Ţͧ��û�Ժѵ��Ҩ���ҧ�ѵ�����͡�ͤ������֧����������������Ǣ�ͧ�������»���ª��', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(4, 5, '�дѺ��� 5 ��� �ʴ����ö���дѺ��� 4 ����ط�ȵ����ͼ�ا�����صԸ���
��ç�����١��ͧ �׹��Ѵ�Էѡ��Ż���ª����Ъ������§�ͧ����Ȫҵ�����ʶҹ��ó����Ҩ����§��ͤ�����蹤�㹵��˹�˹�ҷ���çҹ �����Ҩ���§��µ�ͪ��Ե', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(5, 0, '�дѺ��� 0 ��� ����ʴ����ö�д�ҹ������ҧ�Ѵਹ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(5, 1, '�дѺ��� 1 ��� ��˹�ҷ��ͧ��㹷����������
�ӧҹ���ǹ��赹���Ѻ�ͺ����������� ʹѺʹع��õѴ�Թ�㹡���� 
��§ҹ�����Ҫԡ��Һ�����׺˹�Ңͧ��ô��Թ�ҹ㹡���� ���͢��������� ����繻���ª���͡�÷ӧҹ���ҧ������ͧ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(5, 2, '�дѺ��� 2 ��� �ʴ����ö���дѺ��� 1 ����������������㹡�÷ӧҹ�Ѻ���͹�����ҹ
���ҧ����ѹ��  ��ҡѺ������㹡�������
��������������� ������������͡Ѻ������㹷�����´�
����Ƕ֧���͹�����ҹ��ԧ���ҧ��ä�', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(5, 3, '�дѺ��� 3 ��� �ʴ����ö���дѺ��� 2 ��л���ҹ����������ͧ͢��Ҫԡ㹷��
�Ѻ�ѧ������繢ͧ��Ҫԡ㹷�� �������¹���ҡ������ ����֧�����ѧ�Ѻ�ѭ�� ��м�������ҹ
�����Ť����Դ��繵�ҧ� �����Сͺ��õѴ�Թ������ҧἹ�ҹ�����ѹ㹷��
����ҹ��������������ѹ��Ҿ�ѹ��㹷�� ����ʹѺʹع��÷ӧҹ�����ѹ����ջ���Է���Ҿ��觢��', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(5, 4, '�дѺ��� 4 ��� �ʴ����ö���дѺ��� 3 ���ʹѺʹع��Ъ�������ͧҹ���͹������������� �������ҹ���ʺ���������
����Ǫ�蹪������ѧ����͹�����ҹ�����ҧ��ԧ� 
�ʴ�������˵��ԡĵ� ��������������������͹�����ҹ������˵ب���������ͧ�����ͧ��
�ѡ���Ե��Ҿ�ѹ�աѺ���͹�����ҹ���ͪ�������͡ѹ����е�ҧ����ҹ���������ǧ�繻���ª������ǹ���', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(5, 5, '�дѺ��� 5 ��� �ʴ����ö���дѺ��� 4 �������ö�ӷ����黯Ժѵ���áԨ�����������
��������������Ѥ���繹��˹������ǡѹ㹷�� �����ӹ֧�����ͺ�������ͺ��ǹ�� 
���»���ҹ������� ���ͤ��������䢢�͢Ѵ��駷���Դ���㹷��
����ҹ����ѹ�� ���������ѭ���ѧ㨢ͧ������������ѧ�ѹ㹡�û�Ժѵ���áԨ�˭���µ�ҧ�������ؼ�', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(6, 0, '�дѺ��� 0 ��� ����ʴ����ö�д�ҹ������ҧ�Ѵਹ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(6, 1, '�дѺ��� 1 ��� ᵡ�ѭ��/�ҹ�͡����ǹ�����
�к���¡����觵�ҧ� ���ͻ�������µ�ҧ�����������§�ӴѺ��͹��ѧ
�ҧἹ�ҹ����ᵡ����繻ѭ���͡�繧ҹ ���͡Ԩ������ҧ�', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(6, 2, '�дѺ��� 2 ��� �ʴ����ö���дѺ��� 1 �����繤�������ѹ��������§��鹾�鹰ҹ�ͧ��ǹ��ҧ� �ͧ�ѭ��/�ҹ
�к�������������˵��繼���ѹ�ʶҹ��ó�˹���
�¡��Т�ʹբ�����¢ͧ����繵�ҧ���
�ҧἹ�ҹ���¨Ѵ���§�ҹ ���͡Ԩ������ҧ����ӴѺ�����Ӥѭ���ͤ�����觴�ǹ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(6, 3, '�дѺ��� 3 ��� �ʴ����ö���дѺ��� 2 �����繤�������ѹ��������§���Ѻ��͹�ͧ��ǹ��ҧ� �ͧ�ѭ��/�ҹ
������§�˵ػѨ��·��Ѻ��͹ �ҷ� �˵ء�ó�ó�˹���Ҩ�����˵������»�С�� ��������ö�������˵ء�ó��׺���ͧ�����»�С�� �ҷ� �˵� �. �������˵� �. �˵� �. �������˵� �. ������  �˵� �. ���)
�ҧἹ�ҹ�¡�˹��Ԩ���� ��鹵͹��ô��Թ�ҹ��ҧ� ����ռ������Ǣ�ͧ���½��������ҧ�ջ���Է���Ҿ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(6, 4, '�дѺ��� 4 ��� �ʴ����ö���дѺ��� 3 �������ö�Ҵ��ó���ǧ˹������ǡѺ�ѭ��/�ҹ�ҡ�������㨤�������ѹ��ͧ��ǹ��ҧ�
�¡���ͧ���Сͺ��ҧ� �ͧ����� �ѭ�ҷ�����˵ػѨ���������§�Ѻ��͹����������´㹪�鹵�ҧ� �ա���������������������ҧ�ͧ�ѭ������ʶҹ��ó�˹�������ѹ��ѹ���ҧ�� �Ҵ��ó���Ҩ����͡�� �����ػ��ä���ú�ҧ 
�ҧἹ�ҹ���Ѻ��͹�¡�˹��Ԩ���� ��鹵͹��ô��Թ�ҹ��ҧ�����˹��§ҹ���ͼ������Ǣ�ͧ���½��� ����֧�Ҵ��ó�ѭ�� �ػ��ä ����ҧ�Ƿҧ��û�ͧ�ѹ��������ǧ˹��', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(6, 5, '�дѺ��� 5 ��� �ʴ����ö���дѺ��� 4 ��С�˹�Ἱ�ҹ/��鹵͹��÷ӧҹ�ҡ����֡����������㹢�鹵�ҧ� ����������ҧ���͡����Ѻ��û�ͧ�ѹ/��䢻ѭ�ҷ���Դ���
������Ըա����������ҧ෤�Ԥ����������㹡���¡��л���繻ѭ�ҷ��Ѻ��͹�͡����ǹ�
��෤�Ԥ�������������ҡ�����ٻẺ�����ҷҧ���͡��ҧ� 㹡�õͺ�Ӷ�� ������ѭ�� ����֧�Ԩ�óҢ�ʹբ�����¢ͧ�ҧ���͡���зҧ
�ҧἹ�ҹ���Ѻ��͹�¡�˹��Ԩ���� ��鹵͹��ô��Թ�ҹ��ҧ� �����˹��§ҹ���ͼ������Ǣ�ͧ���½��� �Ҵ��ó�ѭ�� �ػ��ä �Ƿҧ��û�ͧ�ѹ��� �ա����ʹ��зҧ���͡��Т�ʹբ������������', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(7, 0, '�дѺ��� 0 ��� ����ʴ����ö�д�ҹ������ҧ�Ѵਹ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(7, 1, '�дѺ��� 1 ��� �顮��鹰ҹ�����
�顮��鹰ҹ ��ѡࡳ�� ��ʹ����ѡ���ѭ�ӹ֡�����㹡�û�Ժѵ�˹�ҷ�� �кػ���繻ѭ�� ������ѭ��㹧ҹ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(7, 2, '�дѺ��� 2 ��� �ʴ����ö���дѺ��� 1 ��л���ء����ʺ��ó�
�Ԩ�ó��ٻẺ�ͧ��������������ö�к������ �����кآ����ŷ��Ҵ������
����ء����ʺ��ó���к����¹�ʹյ����㹡�û�Ժѵ�˹�ҷ�� �кػ���繻ѭ�� ������ѭ��㹧ҹ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(7, 3, '�дѺ��� 3 ��� �ʴ����ö���дѺ��� 2 ��л���ء���ɮ������ǤԴ�Ѻ��͹
����ء���ɮ������ǤԴ���Ѻ��͹����Ԩ�ó�ʶҹ��ó�Ѩ�غѹ �кػ���繻ѭ��������ѭ��㹧ҹ�����ҧ�֡��� �º��� ���㹺ҧ�ó� �ǤԴ�����������ʶҹ��ó�����ʺ���������͹������դ�������Ǣ�ͧ������§�ѹ��¡���', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(7, 4, '�дѺ��� 4 ��� �ʴ����ö���дѺ��� 3 ���͸Ժ�»ѭ��/�ҹ��Ҿͧ�����
�Ԩ�ó�ʶҹ��ó� ����� ���ͻѭ�ҫѺ��͹���¡�ͺ�ǤԴ����ԸվԨ�ó�Ẻ�ͧ�Ҿͧ����� ���͸Ժ�����������������§���
�Ѵ����ѧ����������� ��ػ�ǤԴ ��ɮ� ͧ�������� ��� ���Ѻ��͹�繤�͸Ժ�·������ö�������§�������繻���ª���ͧҹ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(7, 5, '�дѺ��� 5 ��� �ʴ����ö���дѺ��� 4 ��ФԴ�͡��ͺ�������ҧ��ä�ͧ������������
�Դ�͡��ͺ �Ԩ�ó���觵�ҧ�㹧ҹ��������ͧ���ᵡ��ҧ �ѹ�������û�д�ɰ�Դ�� ������ҧ��ä���й��ʹ��ٻẺ �Ը� ��ʹ��ͧ������������������»�ҡ��ҡ�͹����繻���ª���ͧҹ �Ҥ�Ҫ��� �����ѧ����л���Ȫҵ������', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(8, 0, '�дѺ��� 0 ��� ����ʴ����ö�д�ҹ������ҧ�Ѵਹ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(8, 1, '�дѺ��� 1 ��� �������Ӥѭ�ͧ��þѲ���ѡ��Ҿ
�ǹ�������͡�ʾѲ�ҵ��ͧ ���ʹѺʹع�ѡ�ǹ����������������Ԩ�����Ѳ�Ҥ������ �ѡ��Ҿ ��� �����ͧ�������ǡѺ��áԨ��赹�Ѻ�Դ�ͺ����
���������Ҽ������դ������ʧ�� ����դ�������ö�������¹�����л�Ѻ��ا�Ѳ�ҵ��ͧ�������� ����', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(8, 2, '�дѺ��� 2 ����ʴ����ö���дѺ��� 1 ����͹�ҹ��������й�����ǡѺ�Ըջ�Ժѵԧҹ
�����й����ҧ�����´ ���/�����ҸԵ�Ըջ�Ժѵԧҹ �����繵�����ҧ
�͹�ҹ ���������йӷ��੾����Ш�����ǡѺ��þѲ�ҧҹ���͡�û�ԺѵԵ�
��������觢����� ��з�Ѿ�ҡ����� ������㹡�þѲ�Ңͧ������', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(8, 3, '�дѺ��� 3 ����ʴ����ö���дѺ��� 2 �������˵ؼŻ�Сͺ����͹/�й� ��������ʹѺʹع���� ����ǡѺ��û�Ժѵԧҹ �������駵�Ǩ�ͺ��Ҽ���Ѻ����͹�դ�������
����Ƿҧ �����ҸԵ��û�Ժѵԧҹ �����繵�����ҧ ��������͸Ժ���˵ؼŻ�Сͺ
�����ʹѺʹع ���͡�ê����������Ҥ��Ժѵ� �����������蹻�Ժѵԧҹ/��ԺѵԵ������ҧ�ջ���Է���Ҿ��觢�� (�� ʹѺʹع��Ѿ�ҡ� �ػ�ó� ������ ����й� 㹰ҹм���ջ��ʺ��ó��ҡ�͹���) �������������û�Ժѵ�
����Ӷ�� ���ͺ �������Ըա������ ���͵�Ǩ�ͺ��Ҽ��������㨤�͸Ժ���������', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(8, 4, '�дѺ��� 4 ����ʴ����ö���дѺ��� 3 �������͡�ʼ�������Ѳ�ҵ��ͧ
����͡�ʼ������ʹ����Ըա�����¹��� ��оѲ�Ҽš�û�ԺѵԹ͡�˹��仨ҡ�Ըջ�ԺѵԵ������
ʹѺʹع����ա�����¹�������š����¹�������㹧ҹ�����˹�ҷ������Ѻ�Դ�ͺ�����§����������§�ѹ ���;Ѳ�Ҥ�������ö ��л��ʺ��ó�㹡���ͧ�Ҿ����ͧ�ؤ�ҡ�', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(8, 5, '�дѺ��� 5 ����ʴ����ö���дѺ��� 4 ������ӵԪ��š�û�Ժѵ��������������þѲ�����ҧ������ͧ
���ӵԪ��š�û�Ժѵԧҹ/��ԺѵԵ��ͧ�������������������þѲ�ҡ�û�ԺѵԷ�������ͧ 
�Եԧ�ĵԡ������������������੾����Ш��繡ó�� �»��Ȩҡͤ�Ե�͵�Ǻؤ�� 
�������������繵�ͼš�û�ԺѵԻѨ�غѹ���ʴ������Ҵ��ѧ��ԧ�ǡ��Ҽ�����Ѻ�ӵԪ����ѡ��Ҿ��ШоѲ������㹷ҧ� 
�����йӷ����������Ѻ�ؤ�ԡ ����ʹ� ��������ö੾�кؤ�� ��� ���͡�û�Ѻ��ا�Ѳ�ҷ���������', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(9, 0, '�дѺ��� 0 ��� ����ʴ����ö�д�ҹ������ҧ�Ѵਹ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(9, 1, '�дѺ��� 1 ��� �������з���觵�ҧ� ����ҵðҹ ���͵��������º��ͺѧ�Ѻ����˹����
�������з���觵�ҧ� ����ҵðҹ���͵��������º��ͺѧ�Ѻ����˹����
�ͺ���§ҹ��Ш����������´�������蹴��Թ��� ���͵��ͧ����������仨Ѵ��çҹ�Ҫ������� ����ռ���ԧ���ط���ҡ���', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(9, 2, '�дѺ��� 2 ����ʴ����ö���дѺ��� 1 ��С�˹��ͺࢵ��ͨӡѴ㹡�á�з���觵�ҧ� 
����ʸ������¡��ͧ�ͧ����������˹��§ҹ�ӡѺ���� (�����Ҥ�Ѱ����͡��) ���Ҵ�˵ؼ����ͼԴ�������������º����ҧ���
��˹��Ѵ��Ҿĵԡ������͡�û�Ժѵԧҹ㴢ͧ�����ѧ�Ѻ�ѭ������˹��§ҹ������áӡѺ�����繷������Ѻ�����
��Ѻʶҹ��ó� ���ͨӡѴ�ҧ���͡�ͧ���������� ���ͺպ����������蹻�оĵԻ�Ժѵ�㹡�ͺ���١��ͧ�����������������º��Ժѵ�����', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(9, 3, '�дѺ��� 3 ����ʴ����ö���дѺ��� 2 ����������Ѻ��ا�ŧҹ��������ҵðҹ/��Ѻ�ҵðҹ���բ��
��˹��ҵðҹ�ŧҹẺ���� ᵡ��ҧ �����٧���
������任�Ѻ��ا�ŧҹ��ԧ����ҳ���ͤس�Ҿ������ࡳ���ҵðҹ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(9, 4, '�дѺ��� 4 ����ʴ����ö���дѺ��� 3 ��еԴ����Ǻ�����黯ԺѵԵ���ҵðҹ���͵�������¢�ͺѧ�Ѻ
���蹤Ǻ�����Ǩ��ҡ�û�Ժѵ�˹�ҷ��ͧ˹��§ҹ�������������áӡѺ�������������ҧ�١��ͧ���������
�͡����͹�ªѴ����Ҩ��Դ���â���ҡ�ŧҹ������ҵðҹ����˹�������͡�зӡ������Դ������', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(9, 5, '�дѺ��� 5 ����ʴ����ö���дѺ��� 4 ��ШѴ��áѺ�ŧҹ����������觼Դ���������ҧ�索Ҵ�ç仵ç��
���Ը�༪ԭ˹�����ҧ�Դ�µç仵ç������ͼ���������˹��§ҹ������áӡѺ�����ջѭ�Ҽŧҹ�������ͷӼԴ���������ҧ�����ç
����͡���ͨѺ���ŧ��㹡óշ������ѡ�ҹ�˵ؼ������', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(10, 0, '�дѺ��� 0 ��� ����ʴ����ö�д�ҹ������ҧ�Ѵਹ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(10, 1, '�дѺ��� 1 ��� �Ң�������дѺ��
�Ң������¡�ö���ҡ���������Ǣ�ͧ�µç
������ŷ�������� �����Ҩҡ���觢����ŷ������������', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(10, 2, '�дѺ��� 2 ��� �ʴ����ö���дѺ��� 1 ������Ըա���׺�����Ң�����
�׺���лѭ������ʶҹ��ó����ҧ�֡��駡��ҡ�õ�駤Ӷ������á�Ը�����
�׺���Шҡ��������Դ�Ѻ�˵ء�ó� ���ͻ���繻ѭ���ҡ����ش', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(10, 3, '�дѺ��� 3 ��� �ʴ����ö���дѺ��� 2 ����Ң�����Ẻ����֡
�ѡ����Ӷ������֡���ҧ������ͧ �����ҵ鹵ͧ͢ʶҹ��ó� �ѭ�� �����͡�ʷ���͹�����������ͧ�֡
�ͺ�����ȹФ����Դ��� ������ѧ����ѵԤ������� ���ʺ��ó� ��� �ҡ�����������������Ǣ�ͧ�µç', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(10, 4, '�дѺ��� 4 ��� �ʴ����ö���дѺ��� 3 ����׺�鹢��������ҧ���к�
���Թ����红����ŷ�����㹪�ǧ����˹��� ���ҧ���к�
�׺�鹨ҡ���觢����ŷ���š����ᵡ��ҧ�ҡ�á�Ը����ҷ����
ŧ����׺���Ԩ���ͧ �����ͺ�������������红��������ҧ�繡Ԩ���ѡɳШҡ˹ѧ��;���� �Ե���� �к��׺���������෤��������ʹ�� ��ʹ�����觢��ǵ�ҧ�', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(10, 5, '�дѺ��� 5 ��� �ʴ����ö���дѺ��� 4 ����ҧ�к�����׺�� �����Ң��������ҧ������ͧ
�ҧ�к�����׺�� ��������բ����ŷ��ѹ�˵ء�ó��͹��������ҧ������ͧ
��˹��ͺ�����������蹷ӡ���׺���Ң�����������ҧ���������繡Ԩ�ѵ�', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(11, 0, '�дѺ��� 0 ��� ����ʴ����ö�д�ҹ������ҧ�Ѵਹ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(11, 1, '�дѺ��� 1 ��� ��繤س�������������ʹ��Ѳ��������
�Ҥ�������Ѳ���������㹢�����ǡѹ����繤س�������ʴ�����ʹ����¹��� �Ѳ����� ����������� ���ླջ�ԺѵԢͧ���ҵԵ�ҧ�
����ʴ��ҡ�ôٶ١�Ѳ����������Ҵ��¡���
��繤�������㹡�û�Ѻ����¹�ĵԡ����ͧ������ʹ���ͧ�Ѻ��Ժ��ҧ�Ѳ������������¹�㹷���ҧ�', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(11, 2, '�дѺ��� 2 ��� �ʴ����ö���дѺ��� 1 ���������л�Ѻ�������ʹ���ͧ�Ѻ�Ѳ���������
��������ҷ ������ ��и���������ԺѵԢͧ�Ѳ��������ᵡ��ҧ��о�������Ѻ�������ʹ���ͧ�����׹
����������ʹ��Ҵ����Ըա����������ж��¤ӷ����������Ѻ�Ѳ������ͧ���ҵԵ�ҧ�', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(11, 3, '�дѺ��� 3 ��� �ʴ����ö���дѺ��� 2 ���������Ѳ�������ҧ� ���ҧ�֡���
���㨺�Ժ� ��к�÷Ѵ�ҹ���ὧ (unspoken norms) ������Ѳ�������ҧ�
�����ҡ�ҹ�ҧ�Ѳ��������ᵡ��ҧ�ѹ�ͧ�ؤ���ѹ����������Ըա�û�Ժѵԧҹ���ͤ����Դ��繢ͧ�ؤ��ᵡ��ҧ�ѹ
����ǹ��ػ�ؤ�Ũҡ���ʺ��ó����ͤ���ᵡ��ҧ�ҧ�Ѳ����� ���ͪҵ� ��Ҿѹ�������������������繻���ª��㹡��������÷Ӥ�������������ҧ��ä�����ķ��� ���ͻ���ª�������ѹ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(11, 4, '�дѺ��� 4 ��� �ʴ����ö���дѺ��� 3 ������ҧ��������Ѻ�Ѻ��ͷ�����ҧ����ᵡ��ҧ�ҧ�Ѳ�����
���ҧ��������Ѻ�Ѻ��� ����ҧ�������餹��ҧ�Ѳ����� ���ͻ���ҹ������������������ѹ�������ѹ��
�����������ʹѺʹع����Դ��÷ӧҹ�����ѹ �������������ѹ��Ҿ�����ҧ����ȷ���дѺ����Ҥ���о���Ҥ�', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(11, 5, '�дѺ��� 5 ��� �ʴ����ö���дѺ��� 4 ��л�ѺἹ�ҹ����Ըա�÷ӧҹ����ʹ���ͧ�Ѻ��Ժ��ҧ�Ѳ�����
�������¢�;Ծҷ�����ҧ�Ѳ��������ᵡ��ҧ�ѹ����鹰ҹ�ͧ�����������ҧ�֡���������Ѳ�����
��Ѻ����¹���ط�� ��ҷ� �����������ʹ���ͧ�Ѻ�Ѳ��������ᵡ��ҧ ���������ѡ��Ҿ㹡���èҵ���ͧ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(12, 0, '�дѺ��� 0 ��� ����ʴ����ö�д�ҹ������ҧ�Ѵਹ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(12, 1, '�дѺ��� 1 ��� ���㨤������·������蹵�ͧ����������
���㨤������·�������������ô������������͵�ҧ� ����ö�Ѻ㨤����� ��ػ����������ͧ�����١��ͧ�ú�����', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(12, 2, '�дѺ��� 2 ��� �ʴ����ö���дѺ��� 1 ��������������������֡��ФӾٴ
���㨷�������Ңͧ�����й���ԧ������ (�ҡ����ѧࡵ��Ѩ����� �� ��ҷҧ ����ʴ��͡�ҧ��˹�� ���͹�����§) �ͧ�����Դ��ʹ���', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(12, 3, '�дѺ��� 3 ��� �ʴ����ö���дѺ��� 2 ������㨤�������ὧ��ҡѻ��������ФӾٴ
����ö�դ����������ͧ�֡���������ʴ��͡���ҧ�Ѵਹ㹤Ӿٴ���͹�����§
���㨤����Դ �����ѧ�� ���ͤ�������֡�ͧ������ � ���Ң�й�� ��駷���ʴ��͡����§��硹��� ����������ʴ��͡��¡���
����ö�к��ѡɳй�������ͨش�����ҧ����ҧ˹�觢ͧ�����Դ��ʹ�����', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(12, 4, '�дѺ��� 4 ��� �ʴ����ö���дѺ��� 3 ������������㹡��������÷�駷���繤Ӿٴ ��Ф�����ҹ��ὧ㹡��������áѺ����������ҧ�������
�ʴ���������㹹�¢ͧ�ĵԡ�������������������֡�ͧ������
���������㹺ؤ�Ź������繻���ª��㹡�ü١�Ե÷Ӥ������ѡ ���͵Դ��ͻ���ҹ�ҹ��͡�ʵ�ҧ�', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(12, 5, '�дѺ��� 5 ��� �ʴ����ö���дѺ��� 4 ����������˵آͧ�ĵԡ���������
�ʴ������������ͧ�֡�֧���˵آͧ�ĵԡ��� ���ͻѭ�Ңͧ�����蹵�ʹ���������˵������ç�٧��������Ǣͧ�ĵԡ�����Ф�������֡�ͧ������
����ö�ʴ���ȹ��Է���繸�����������������ǡѺ�ش��͹��Шش�蹷ҧ�ĵԡ�������ѡɳй���¢ͧ������
�ըԵ�Է��㹡����������㨼����������繾�鹰ҹ㹡�÷ӧҹ�����ѹ ����èҷӤ������� ��������������������ԡ�� ����͹�ҹ ��þѲ�Һؤ�ҡ� ��� �����áԨ㹧ҹ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(13, 0, '�дѺ��� 0 ��� ����ʴ����ö�д�ҹ������ҧ�Ѵਹ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(13, 1, '�дѺ��� 1 ��� �����ç���ҧ���ҧ�繷ҧ��âͧͧ���
������¡�úѧ�Ѻ�ѭ������ç���ҧͧ��âͧ˹��§ҹ��赹�ѧ�Ѵ���� ������º ��ʹ����鹵͹��кǹ��û�Ժѵԧҹ��ҧ� ��йӤ������㨹������㹡�õԴ��ͻ���ҹ�ҹ ��§ҹ�� ��� �˹�ҷ����١��ͧ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(13, 2, '�дѺ��� 2 ��� �ʴ����ö���дѺ��� 1 ��������ç���ҧ���ҧ����繷ҧ��âͧͧ���
��������ѹ��Ҿ���ҧ����繷ҧ��������ҧ�ؤ���ͧ��� ��ʹ��������ӹҨ�Ѵ�Թ���м�����Է�Ծŵ�͡�õѴ�Թ���дѺ��ҧ� ��йӤ������㨹���������ª������觼����ķ�����л���ª��ͧ�Ҥ�Ҫ������Ӥѭ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(13, 3, '�дѺ��� 3 ��� �ʴ����ö���дѺ��� 2 ��������Ѳ�����ͧ���
�����Ѳ����� ���ླջ�Ժѵ� ��ҹ����ͧͧ����Ҥ�Ѱ����� �Ըա�������������ջ���Է���Ҿ ��� ��йӤ������㨹���������ª��㹧ҹ
���㨢�ͨӡѴ�ͧͧ��� �����������Ҩ��з�����������Ҩ��з�������ؼ����ʶҹ��ó��ҧ� ������Ԩ�óҴ��Թ��õ�ҧ�㹧ҹ���������з���������', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(13, 4, '�дѺ��� 4 ��� �ʴ����ö���дѺ��� 3 ������㨡���ʡ�����ͧ�ͧ��������Ҥ�Ѱ�����
���㨹�º���Ҥ�Ѱ��Ҿ��� �ա������㨼ŷ����յ��˹��§ҹ��к��ҷ˹�ҷ��ͧ�������Ҥ��º������Ҥ��Ժѵ�
���㨡���������ѹ��Ҿ�����ҧ�Ҥ������ͧ����к��Ҫ��� ������㹡�ü�ѡ�ѹ��áԨ�˹�ҷ���Ѻ�Դ�ͺ�����ҧ�ջ���Է���Ҿ㹨ѧ����͡�ʷ���������������ؼ��繻���ª������ǹ������Ἱ�ҹ����˹����', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(13, 5, '�дѺ��� 5 ��� �ʴ����ö���дѺ��� 4 �����������˵ؾ�鹰ҹ�ͧ�ĵԡ���ͧ���
��������˵ؾ�鹰ҹ�ͧ�ĵԡ���ͧ����˹��§ҹ�ͧ����Тͧ�Ҥ�Ѱ����� ��ʹ���ѭ�� ����͡�ʷ�������� ��йӤ������㨹���ҢѺ����͹��û�Ժѵԧҹ���ǹ��赹�����Ѻ�Դ�ͺ�������ҧ���к�
���㨻���繻ѭ�ҷҧ������ͧ ���ɰ�Ԩ �ѧ�� ��� ������������¹͡����ȷ���ռš�з���͹�º���Ҥ�Ѱ�����áԨ�ͧ˹��§ҹ�ͧ�� �������ŧ�ԡĵ�������͡�� ��˹��ش�׹��з�ҷյ����áԨ�˹�ҷ�������ҧ�ʹ���ͧ�����������觻���ª��ͧ�ҵ����Ӥѭ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(14, 0, '�дѺ��� 0 ��� ����ʴ����ö�д�ҹ������ҧ�Ѵਹ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(14, 1, '�дѺ��� 1 ��� ����͡�����ͻѭ������������ŧ��ʹ��Թ���
�������͡��㹢�й�����������ͷ��й��͡�ʹ���������ª��㹧ҹ
�����繻ѭ�� �ػ��ä ���ŧ������Ը����������ͪ��', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(14, 2, '�дѺ��� 2 ��� �ʴ����ö���дѺ��� 1 ��ШѴ��ûѭ��੾��˹�������˵��ԡĵ
ŧ������ҧ�Ѻ��������Դ�ѭ��੾��˹������������ԡĵ�
��зӡ����䢻ѭ�����ҧ��觴�ǹ㹢�з�褹��ǹ�˭����������ʶҹ��ó��͹��������ѭ�Ҥ��������ͧ
���ѡ��ԡ�ŧ �״���� ��йջ�й�������༪ԭ�ػ��ä
����Դ���ҧ ����Ѻ�����Դ�š���������ǡ�Ƿ���Ҩ�繻���ª���͡����䢻ѭ��', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(14, 3, '�дѺ��� 3 ��� �ʴ����ö���дѺ��� 2 ���ŧ��͡�зӡ����ǧ˹�� �������ҧ�͡�� ������ա����§�ѭ���������
�Ҵ��ó����ŧ��͡�зӡ����ǧ˹���������ҧ�͡�� ������ա����§�ѭ�ҷ���Ҩ�Դ�������������� 3 ��͹��ҧ˹��
���ͧ���Ըա�÷���š����㹡����䢻ѭ���������ҧ��ä������������Դ����ǧ�Ҫ���', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(14, 4, '�дѺ��� 4 ��� �ʴ����ö���дѺ��� 3 �������������ǧ˹�� �������ҧ�͡�� ������ա����§�ѭ����������
�Ҵ��ó����ŧ��͡�зӡ����ǧ˹���������ҧ�͡�� ������ա����§�ѭ�ҷ���Ҩ�Դ�������������� 4-12 ��͹��ҧ˹��
�Դ�͡��ͺ�������Ըա�÷���š����������ҧ��ä�㹡����䢻ѭ�ҷ��Ҵ��Ҩ��Դ����͹Ҥ�', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(14, 5, '�дѺ��� 5 ��� �ʴ����ö���дѺ��� 4 ����������ó���ǧ˹�� �������ҧ�͡�� ������ա����§�ѭ�����ҧ����׹
�Ҵ��ó����ŧ��͡�зӡ����ǧ˹���������ҧ�͡�� ������ա����§�ѭ�ҷ���Ҩ�Դ���������������ҡ���� 12 ��͹��ҧ˹��
���ҧ����ҡ�Ȣͧ��äԴ�����������Դ����˹��§ҹ��С�е��������͹�����ҹ�ʹͤ����Դ�����㹡�÷ӧҹ ������ѭ���������ҧ�͡����������', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(15, 0, '�дѺ��� 0 ��� ����ʴ����ö�д�ҹ������ҧ�Ѵਹ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(15, 1, '�дѺ��� 1 ��� ��ͧ��÷ӧҹ���١��ͧ��ЪѴਹ �ѡ������º
���㨷ӧҹ���١��ͧ ���Ҵ���º����
��������Դ����������º���º�������Ҿ�Ǵ������÷ӧҹ ��ԺѵԵ����ѡ 5 �.
��ԺѵԵ����鹵͹��û�Ժѵԧҹ �� ����º����ҧ���', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(15, 2, '�дѺ��� 2 ��� �ʴ����ö���дѺ��� 1 ��е�Ǩ�ҹ�����١��ͧ�ͧ�ҹ��赹�Ѻ�Դ�ͺ
��Ǩ�ҹ�����١��ͧ�ͧ�ҹ���ҧ�����´�ͺ�ͺ�������ҹ�դس�Ҿ��
��ͧ��÷�Һ�ҵðҹ�ͧ�ŧҹ���������´���ͨ��黯Ժѵ���١��ͧ
���˹ѡ�֧�����·���Ҩ���Դ��鹡Ѻ���ͧ����˹��§ҹ �ҡ�����Դ��Ҵ㹡�û�Ժѵԧҹ�ͧ��', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(15, 3, '�дѺ��� 3 ��� �ʴ����ö���дѺ��� 2 ��д��Ť����١��ͧ�ͧ�ҹ��駢ͧ����м����� (�������㹤����Ѻ�Դ�ͺ�ͧ��)
��Ǩ�ͺ�����١��ͧ������ͧ�ҹ�ͧ���ͧ
��Ǩ�ͺ�����١��ͧ������ͧ�ҹ�����蹵���ӹҨ˹�ҷ��  ���ԧ�ҵðҹ��û�Ժѵԧҹ ���͡����� ��ͺѧ�Ѻ ������º�������Ǣ�ͧ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(15, 4, '�дѺ��� 4 ��� �ʴ����ö���дѺ��� 3 ��е�Ǩ�ͺ��鹵͹��û�Ժѵԧҹ
��Ǩ�ͺ��Ҽ����蹻�ԺѵԵ����鹵͹��÷ӧҹ����ҧ����������
�����������Ъ�����������蹻�ԺѵԵ����鹵͹��÷ӧҹ����ҧ��� ���ͤ����١��ͧ�ͧ�ҹ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(15, 5, '�дѺ��� 5 ��� �ʴ����ö���дѺ��� 4 ��СӡѺ��Ǩ�ͺ�����١��ͧ��Фس�Ҿ�ͧ�����������ç��� ��С�û�Ժѵԧҹ�������´
��Ǩ�ͺ��������˹�Ңͧ�ç��õ����˹����ҷ���ҧ��� 
��Ǩ�ͺ�����١��ͧ��Фس�Ҿ�ͧ������ ���͡�û�Ժѵԧҹ�������´
�кآ�ͺ����ͧ���͢����ŷ��Ҵ���� ��СӡѺ��������Ң�����������������������Ѿ�����ͼŧҹ����դس�Ҿ���ࡳ�����˹�', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(16, 0, '�дѺ��� 0 ��� ����ʴ����ö�д�ҹ������ҧ�Ѵਹ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(16, 1, '�дѺ��� 1 ��� ��Ժѵԧҹ�����ӹҨ˹�ҷ��������ͧ�ա�áӡѺ����
��Ժѵԧҹ��������ͧ�ա�áӡѺ�������Դ
�Ѵ�Թ����ͧ���áԨ�����ͺࢵ�ӹҨ˹�ҷ���Ѻ�Դ�ͺ�ͧ��', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(16, 2, '�дѺ��� 2 ��� �ʴ����ö���дѺ��� 1 ��л�Ժѵԧҹ�˹�ҷ�����ҧ����
���ҵѴ�Թ�����ͧ���Ԩ�ó��¶���ǹ������Ҷ١��ͧ�˹�ҷ�� �������������͡�ѹ�� �����ռ�������繴��������ҧ
�ʴ��͡���ҧʧ��������㹡�û�Ժѵ�˹�ҷ������ʶҹ��ó����դ��������͹�����ҧ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(16, 3, '�дѺ��� 3 ��� �ʴ����ö���дѺ��� 2 �������㹤�������ö�ͧ��
�������㹤�������������ö ����ѡ��Ҿ�ͧ����Ҩ�����ö��Ժѵ�˹�ҷ�������ʺ���������
�Ҩ�ʴ������������ҧ�Դ��㹡�õѴ�Թ����ͤ�������ö�ͧ��', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(16, 4, '�дѺ��� 4 ��� �ʴ����ö���дѺ��� 3 �������㹡�÷ӧҹ����ҷ��/�����Դ�ͧ��
�ͺ�ҹ����ҷ�¤�������ö �������֡������Թ�շ����ӧҹ���
�ʴ������Դ��繢ͧ�����ҧ���Ҿ����������繴��¡Ѻ���ѧ�Ѻ�ѭ�� ���ͼ�����ӹҨ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(16, 5, '�дѺ��� 5 ��� �ʴ����ö���дѺ��� 4 ������㨷ӧҹ����ҷ���ҡ��С����ʴ��ش�׹�ͧ��
��������Ѻ���һ�Ժѵԧҹ����ҷ�� ��������§����ҡ
�����׹��Ѵ༪ԭ˹�ҡѺ���ѧ�Ѻ�ѭ�����ͼ�����ӹҨ �����ʴ��ش�׹�ͧ�����ҧ�Ѵਹ�ç仵ç���ҡ�����繴���㹻���繷���������Ӥѭ㹺��ҷ˹�ҷ��ͧ��', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(17, 0, '�дѺ��� 0 ��� ����ʴ����ö�д�ҹ������ҧ�Ѵਹ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(17, 1, '�дѺ��� 1 ��� �դ������ͧ���㹡�û�Ժѵԧҹ
��Ѻ����������ʺ�����ҡ�Ӻҡ�ҧ����Ҿ㹧ҹ ����ִ�Դ�Ѻ�����дǡʺ�� �ѵ���ʴ��ҹзҧ�ѧ�� �����дѺ������㹧ҹ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(17, 2, '�дѺ��� 2 ��� �ʴ����ö���дѺ��� 1 �������Ѻ�������繷��е�ͧ��Ѻ����¹
����Ѻ������㨤����Դ��繢ͧ������
���㨷�������¹�����Դ ��ȹ��� ��������Ѻ����������������ѡ�ҹ���Ѵ��駡Ѻ�����Դ���', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(17, 3, '�дѺ��� 3 ��� �ʴ����ö���дѺ��� 2 ����顮����º���ҧ�״����
�դ������ͧ���㹡�û�Ժѵ�˹�ҷ�� �顮ࡳ�� ��кǹ��û�Ժѵԧҹ���ҧ�״���� ���Ԩ�ó�ҳ㹡�û�Ѻ�����ҡѺʶҹ��ó�੾��˹�� ��������Դ�����ķ���㹡�û�Ժѵԧҹ���� ������������ѵ�ػ��ʧ��ͧͧ���', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(17, 4, '�дѺ��� 4 ��� �ʴ����ö���дѺ��� 3 ��л�Ѻ����¹�Ըա�ô��Թ�ҹ �������ҹ�ջ���Է���Ҿ
��Ѻ����¹�Ըա�ô��Թ�ҹ �����ҡѺʶҹ��ó� ���ѧ���������������
��Ѻ��䢡�����º��鹵͹��÷ӧҹ���������� ������������Է���Ҿ�ͧ˹��§ҹ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(17, 5, '�дѺ��� 5 ��� �ʴ����ö���дѺ��� 4 ��л�Ѻ����¹Ἱ���ط������� �������ҹ�ջ���Է���Ҿ
��ѺἹ���ط������� ���������������Ѻʶҹ��ó�੾��˹��
���ǹ �ѧ��¹� ������º����Ըա�÷ӧҹ�ͧ˹��§ҹ�������Ѻ�Դ�ͺ������������� ������������Է���Ҿ�ͧ˹��§ҹ
��Ѻ����¹ͧ��� ��¡�úѧ�Ѻ�ѭ�� �繡��੾�СԨ �����������ö�ͺʹͧ���ʶҹ��ó�੾��˹�������ҧ�������', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(18, 0, '�дѺ��� 0 ��� ����ʴ����ö�д�ҹ������ҧ�Ѵਹ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(18, 1, '�дѺ��� 1 ��� ���ʹ����ҧ�ç仵ç��
���ʹͤ���������ҧ�ç仵ç��㹡����Ի������͹��ʹͼŧҹ �Ҩ¡�˵ؼŤ�������  ������ ���ͤ���ʹ㨢ͧ���ѧ�һ�Сͺ��þٴ���͡�ù��ʹ� ����¡������ҧ������ٻ�����һ�Сͺ��ù��ʹ� �� �Ҿ��Сͺ���͡���ҸԵ �繵� ���ѧ�����Ѻ�ٻẺ��ù��ʹ͵������ʹ�����дѺ�ͧ���ѧ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(18, 2, '�дѺ��� 2 ��� �ʴ����ö���дѺ��� 1 ����������������鹵�㹡�è٧�
�ա������������ŷ����㹡�ù��ʹ����ҧ�ͺ�ͺ�����´����ǹ �Ҩ�ա�ù��ʹͻ���� ��ͤԴ��繷��ᵡ��ҧ�ѹ㹡�ú�����������Ի��� ���ͤ�����Ш�ҧ�������ͨ٧������繴���', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(18, 3, '�дѺ��� 3 ��� �ʴ����ö���дѺ��� 2 �������ŻС�è٧�
��Ѻ�ٻẺ��ù��ʹ������Ի��������������Ѻ����ʹ�����дѺ�ͧ���ѧ �Ҵ��ó�֧�š�з��ͧ��觷����ʹ�����Ҿ����ͧ���ٴ�����յ�ͼ��ѧ
���ٻẺ��ù��ʹͷ���ҧἹ�����ǧ˹�������ҧ�� ��蹵ҵ�������š���� ��������Դ�š�з���ͼ��ѧ㹷�ȷҧ��赹��ͧ��� �ա��駤Ҵ��ó�����������������ǧ˹�������Ѻ��͡Ѻ��ԡ����Ңͧ���ѧ����Ҩ�Դ���', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(18, 4, '�дѺ��� 4 ��� �ʴ����ö���дѺ��� 3 ������Է�Ծŷҧ����㹡�è٧�
�������㨼��ѧ�ҧ�������¡�êѡ�٧���١�� �� ����س �. �ʴ����س �. ��� �������س �.仺͡�س �. �����ա�ʹ˹�觔 �繵� �ա�û�Ѻ���Т�鹵͹㹡��������� ���ʹ� ��Ш٧������������Ѻ���ѧ���С���������������
��������Ǫҭ㹴�ҹ���� �Ҫ��������������è٧���Ŵ���觢��', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(18, 5, '�дѺ��� 5 ��� �ʴ����ö���дѺ��� 4 �������ط��Ѻ��͹㹡�è٧�
���ҧ��������ʹѺʹع�������ͧ��ѧ���͡���������� ���ͪ���ʹѺʹع��ѡ�ѹ�ǤԴ Ἱ�ҹ�ç��� ��� ������ķ����
������������ҧ��ͧ������ǡѺ��ԡ����Ңͧ����Ѻ��� �ĵԡ�������� �Ե�Է����Ū� ��� ����繻���ª��㹡��������è٧�', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(19, 0, '�дѺ��� 0 ��� ����ʴ����ö�д�ҹ������ҧ�Ѵਹ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(19, 1, '�дѺ��� 1 ��� �����á�û�Ъ�������Ф���駢�����ä��������������
��˹��������Ǣ��㹡�û�Ъ�� �ѵ�ػ��ʧ�� �Ǻ������� ���ᨡᨧ˹�ҷ���Ѻ�Դ�ͺ�����ؤ��㹡������
�駢�����ä�����������������Ѻ�š�з��ҡ��õѴ�Թ��Ѻ��Һ����������������繵�ͧ��з�
͸Ժ���˵ؼ�㹡�õѴ�Թ��������蹷�Һ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(19, 2, '�дѺ��� 2 ��� �ʴ����ö���дѺ��� 1 ����繼���㹡�÷ӧҹ�ͧ�����
���������������ӧҹ���ҧ�ջ���Է���Ҿ
ŧ��͡�зӡ�����ͪ������������Ժѵ�˹�ҷ�������ҧ�������Է���Ҿ
��˹�������� ��ȷҧ���Ѵਹ ���ç���ҧ���������� ���͡���������СѺ�ҹ �������Ըա������ ���ͪ������ҧ����з��з���������ӧҹ��բ��
�Դ㨡��ҧ�Ѻ�ѧ�����Դ��繢ͧ����������ʹѺʹع����������͡�кǹ��û�Ժѵԧҹ�ջ���Է���Ҿ��觢��
���ҧ��ѭ���ѧ�㹡�û�Ժѵԧҹ ��������͡�ʼ����ѧ�Ѻ�ѭ��㹡���ʴ��ѡ��Ҿ��÷ӧҹ���ҧ������ �������������Է���Ҿ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(19, 3, '�дѺ��� 3 ��� �ʴ����ö���дѺ��� 2 �������ô�����Ъ�������ͼ����ѧ�Ѻ�ѭ��
�繷���֡���������ô��ż����ѧ�Ѻ�ѭ��
����ͧ�����ѧ�Ѻ�ѭ����Ъ������§�ͧͧ���
�Ѵ�Һؤ�ҡ� ��Ѿ�ҡ� ���͢����ŷ���Ӥѭ����� �����ͧ��õ�ͧ��� ���������ʹѺʹع������������ѧ�Ѻ�ѭ��
����������������ѧ�Ѻ�ѭ�����㨶֧��û�Ѻ����¹����Դ�������ͧ�����Ф������繢ͧ��û�Ѻ����¹����', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(19, 4, '�дѺ��� 4 ��� �ʴ����ö���дѺ��� 3 ��л�оĵԵ����Ѻ�繼���
��˹�����������ԺѵԻ�Шӡ������л�оĵԵ���Ẻ���ҧ����������ѧ�Ѻ�ѭ��
�ִ��ѡ������Ժ��  (Good Governance) (�ԵԸ��� �س���� ����� ��������ǹ���� �����Ѻ�Դ�ͺ �����������) 㹡�û���ͧ�����ѧ�Ѻ�ѭ��
ʹѺʹع�������ǹ�����ͧ�����ѧ�Ѻ�ѭ��㹡���ط�ȵ����Ѻ��û�Ժѵԧҹ����ʹͧ��º�»������к������áԨ�Ҥ�Ѱ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(19, 5, '�дѺ��� 5 ��� �ʴ����ö���дѺ��� 4 ����ʴ�����·�ȹ���Ѵਹ��ͼ����ѧ�Ѻ�ѭ��
�����������·�ȹ����վ�ѧ ����ö���㨤�������ҧ�ç�ѹ�����������ѧ�Ѻ�ѭ������ö��Ժѵԧҹ�����áԨ���������ǧ���ԧ
�繼���㹡�û�Ѻ����¹�ͧͧ��� ��ѡ�ѹ����û�Ѻ����¹���Թ������ҧ�Һ�����л��ʺ�������������¡��ط������Ըմ��Թ��÷���������
������·�ȹ�㹡�������繡������¹�ŧ�͹Ҥ� ��������������ҧ���ط�����Ѻͧ���㹡���Ѻ��͡Ѻ�������¹�ŧ����', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(20, 0, '�дѺ��� 0 ��� ����ʴ����ö�д�ҹ������ҧ�Ѵਹ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(20, 1, '�дѺ��� 1 ��� �Һ���㹧ҹ��Ż�
��繤س�������ʴ�������蹪�㹧ҹ��Żе�ҧ� ����֧�ʴ������ѡ����ǧ�˹㹧ҹ��Ż�ͧ�ҵ�
ʹ������ҡ��������ǹ����㹡�����¹���  �Դ������ͻ�Ժѵԧҹ��Ż�ᢹ���ҧ� 
���蹽֡�����ҧ�����ӹҭ㹧ҹ��Ż�ͧ�����ҧ��������', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(20, 2, '�дѺ��� 2 ��� �ʴ����ö���дѺ��� 1 ��������ٻẺ��ҧ� �ͧ�ҹ��Ż�
�¡��Ф���ᵡ��ҧ�ͧ�ҹ��Ż��ٻẺ��ҧ� ���͸Ժ�����������Ѻ���֧������§���ͧ�ҹ��Ż�����ҹ����
�����ٻẺ���ҧ��ͧ�������繢���蹢ͧ�ҹ��Ż��ٻẺ��ҧ� ��й�任�Ѻ���þѲ�ҧҹ��Ż�ͧ��
���ҧ�Ե�ӹ֡㹤س�����Ż�������Դ���͹��ѡ���ǧ���ҧ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(20, 3, '�дѺ��� 3 ��� �ʴ����ö���дѺ��� 2 ��л���ء���������ҧ��ä�ҹ��Ż�
���Է�ԾŢͧ�ؤ���µ�ҧ� ����������ҧ�ҹ��Ż� �����ç�ѹ�������;�鹰ҹ㹡�äԴ�鹻�д�ɰ�ŧҹ��Ż�ͧ��
����ء����ʺ��ó���Ф������㹧ҹ��Ż�����㹡�����ҧ��çҹ��Ż�ͧ��', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(20, 4, '�дѺ��� 4 ��� �ʴ����ö���дѺ��� 3 ������ҧ�ç�ѹ����������§ҹ��Ż�
����ء����觷��ըҡ�ؤ��ҧ� ����㹡���ѧ��ä�ŧҹ�����ʹ� ������ç�ѹ�������������Դ�Ե�ӹ֡㹡��͹��ѡ����Ż�Ѳ�������
����ʵ��ҧ��Ż������ᢹ��Ҽ����ҹ �������ҧ��ä�ŧҹ �Ԩ�ó���ʵ���ҧ� �����ͧ���ᵡ��ҧ� �ѹ�����������ҧ��ä�ŧҹ��Ż���ᵡ��ҧ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(20, 5, '�дѺ��� 5 ��� �ʴ����ö���дѺ��� 4 ����ѧ��ä�ҹ��Ż������͡�ѡɳ�੾�е�
�ѧ��ä�ҹ��Ż������͡�ѡɳ�੾�з���繷������Ѻ�ǧ��� �����Ҩ��繡�äԴ���ҧ��ä�ҹ������ ����͹��ѡ������觢���������ͧ�ҹ��Ż������
�Դ�͡��ͺ �ٻẺ �Ը� ��ʹ����ͧ�������������ҹ�ҹ��Ż����ѧ��ä�ҹ������͡�ѡɳ�੾�е�', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(21, 0, '�дѺ��� 0 ��� ����ʴ����ö�д�ҹ������ҧ�Ѵਹ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(21, 1, '�дѺ��� 1 ��� ������§�ҹ��ҡѺ��Ժ��ͧ�Ҥ�Ѱ�����
������������������������觷����������ռ����ҧ�õ���Ҹ�ó�� ������������Ҿ����Ѵਹ������㨧��� ������������������Һ��ҷ�ͧ������Ǣ�ͧ�Ѻ��Ժ���������ҧ��
������§����·�ȹ�ͧ˹��§ҹ�Ѻ������� �ѵ�ػ��ʧ����С��ط��ͧ�Ҥ�Ѱ�������', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(21, 2, '�дѺ��� 2 ��� �ʴ����ö���дѺ��� 1 �����������������ǹ����㹡�á�˹�����·�ȹ�
�觻ѹ�����Ѻ�Դ�ͺ㹡�á�˹�����·�ȹ������������������������ǹ���������ʴ������Դ��繴���', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(21, 3, '�дѺ��� 3 ��� �ʴ����ö���дѺ��� 2 ��з��������·�ȹ����Ѻ�������Ѻ
���ҧ����������Ͷ�����������·�ȹ��¡����������ǧ���ҧ�˹��§ҹ��軯Ժѵ�˹�ҷ������
�觻ѹ��������������������¹͡˹��§ҹ ��ʹ�������Ң���������ҹ�鹨й����繾�鹰ҹ㹡�á�˹����ط��ͧ˹��§ҹ�����ҧ��', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(21, 4, '�дѺ��� 4 ��� �ʴ����ö���дѺ��� 3 ��������������·�ȹ�
���·ʹ����·�ȹ�ͧ˹��§ҹ�������Ѻ�Դ�ͺ��������Ըշ�����ҧ�ç�ѹ���� ������е������� ��Ф��������ç����������������·�ȹ���
������·�ȹ���㹡�á�˹��ش������з�ȷҧ����Ѻ��餹������� ��੾�����ҧ�������ǡ�ó�����ѧ༪ԭ�������¹�ŧ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(21, 5, '�дѺ��� 5 ��� �ʴ����ö���дѺ��� 4 ���������·�ȹ��Ҫ��¡�˹���º��㹧ҹ
�Դ�͡��ͺ ���ʹͤ����Դ�����������˹���º��㹧ҹ���ͻ���ª�������͡�ʢͧ�Ҥ�Ѱ�����Ҹ�ó����������ҧ�������ռ��㴤Դ�ҡ�͹', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(22, 0, '�дѺ��� 0 ��� ����ʴ����ö�д�ҹ������ҧ�Ѵਹ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(22, 1, '�дѺ��� 1 ��� ���㨡��ط���Ҥ�Ѱ
��������·�ȹ� ��áԨ ��º�� ���ط���Ҥ�Ѱ �ա�����������դ���������§�Ѻ��áԨ�ͧ˹��§ҹ��赹�����Ѻ�Դ�ͺ�������ҧ��
����ö��������ѭ�� �ػ��ä�����͡�ʢͧ˹��§ҹ��㹡�ú���ؼ����ķ�����', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(22, 2, '�дѺ��� 2 ��� �ʴ����ö���дѺ��� 1 ��л���ء����ʺ��ó�㹡�á�˹����ط���û�Ժѵԧҹ�ͧ˹��§ҹ
����ء����ʺ��ó���к����¹�ʹյ�����˹����ط��ͧ˹��§ҹ����ʹ���ͧ�Ѻ���ط���Ҥ�Ѱ �������ö�������áԨ����˹����
�����������������к��Ҫ����һ�Ѻ���ط�� �����ط��Ը�㹡�û�Ժѵԧҹ�ͧ˹��§ҹ�����������Ѻʶҹ��ó����㹷���Դ�����', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(22, 3, '�дѺ��� 3 ��� �ʴ����ö���дѺ��� 2 ��л���ء���ɮ������ǤԴ�Ѻ��͹㹡�á�˹����ط���û�Ժѵԧҹ�͹Ҥ�
����ء���ɮ� �����ǤԴ�Ѻ��͹㹡�äԴ��оѲ������������͡��ط��㹡�û�Ժѵԧҹ�ͧ˹��§ҹ��赹�����Ѻ�Դ�ͺ����
�Դ�ç�������Ἱ�ҹ�������ķ����ջ���ª��������ǵ�ͧҹ��赹�����Ѻ�Դ�ͺ����', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(22, 4, '�дѺ��� 4 ��� �ʴ����ö���дѺ��� 3 ���������§ʶҹ��ó�㹻�������͡�˹����ط��㹡�û�Ժѵԧҹ���㹻Ѩ�غѹ����͹Ҥ�
�����Թ���������§ʶҹ��ó� ����� ���ͻѭ�ҷҧ���ɰ�Ԩ �ѧ�� ������ͧ���㹻���ȷ��Ѻ��͹���¡�ͺ�ǤԴ����ԸվԨ�ó�Ẻ�ͧ�Ҿͧ����� ������㹡�á�˹����ط���Ҥ�Ѱ
�ԴἹ���͡��ط���ԧ�ء㹡�û�Ժѵԧҹ�ͧ˹��§ҹ ���͵ͺʹͧ�͡�����ͻ���繻ѭ�ҷ���Դ��鹨ҡʶҹ��ó����㹻���ȷ������¹�ŧ�', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(22, 5, '�дѺ��� 5 ��� �ʴ����ö���дѺ��� 4 ���������§ʶҹ��ó��š���͡�˹����ط��㹡�û�Ժѵԧҹ���㹻Ѩ�غѹ����͹Ҥ�
�����Թ���������§ʶҹ��ó� ����� ���ͻѭ�ҷҧ���ɰ�Ԩ �ѧ�� ������ͧ�ͧ�š ������㹡�á�˹����ط���Ҥ�Ѱ����ʹ���ͧ�Ѻ��Ժ��ͧ�����
�ԴἹ���͡��ط���ԧ�ء㹡�û�Ժѵԧҹ�ͧ˹��§ҹ ���͵ͺʹͧ�͡�����ͻ���繻ѭ�ҷ���Դ��鹨ҡʶҹ��ó��ҧ����ȷ������¹�ŧ�', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(23, 0, '�дѺ��� 0 ��� ����ʴ����ö�д�ҹ������ҧ�Ѵਹ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(23, 1, '�дѺ��� 1 ��� ��繤������繢ͧ��û�Ѻ����¹
��繤������繢ͧ��û�Ѻ����¹ ����ö��˹���ȷҧ��Тͺࢵ�ͧ��û�Ѻ����¹������Դ�������ͧ�����
���㨶֧��������㹡�û�Ѻ����¹ ��л�Ѻ�ĵԡ�������ʹ���ͧ�Ѻ��û�Ѻ����¹���', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(23, 2, '�дѺ��� 2 ��� �ʴ����ö���дѺ��� 1 ���ʹѺʹع�����������㨡�û�Ѻ����¹�����Դ���
��������������������㨶֧��û�Ѻ����¹����Դ�������ͧ��� ����������л���ª��ͧ��û�Ѻ����¹����
ʹѺʹع����������㹡�û�Ѻ����¹ͧ��� ���������ʹ����Ըա�÷��Ъ�������û�Ѻ����¹���Թ����ҧ�ջ���Է���Ҿ�ҡ���', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(23, 3, '�дѺ��� 3 ��� �ʴ����ö���дѺ��� 2 ��С�е�� ������ҧ�ç�٧�����������繤����Ӥѭ�ͧ��û�Ѻ����¹����Դ���
 ��е�� ������ҧ�ç�٧�����������繤����Ӥѭ�ͧ��û�Ѻ����¹����Դ�����������Դ��������ç���������Դ�������¹�ŧ��鹢�鹨�ԧ
����� ������ҧ�����Ѵਹ�¡��͸Ժ�����˵� �������� ����ª�� ��� �ͧ��û�Ѻ����¹����Դ�����������', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(23, 4, '�дѺ��� 4 ��� �ʴ����ö���дѺ��� 3 ��з������繪Ѵਹ��ҡ������¹�ŧ�������¹����ҧ�� ��дբ�����ҧ��
���º��º�����������觷���è��������觷���оĵԻ�Ժѵԡѹ������ᵡ��ҧ�ѹ���ҧ��
��ҷ�¤����Դ�ͧ������ �ʴ��������ɢͧ��ù���� ��л���ª��ͧ�������¹�ŧ�ҡ���ǡ�ó�Ѩ�غѹ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(23, 5, '�дѺ��� 5 ��� �ʴ����ö���дѺ��� 4 ����ҧἹ�ҹ��������Ѻ��û�Ѻ����¹�ͧ���
���ҧ����·�ȹ���Ъ�������繼����ķ���ҡ����������㹡�û�Ѻ����¹ͧ��÷����ѧ�д��Թ��� �ա��������Ἱ������ͧ�������ö�Ѻ��͡Ѻ�������¹�ŧ���� �����ҧ�ջ���Է���Ҿ�������������', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(24, 0, '�дѺ��� 0 ��� ����ʴ����ö�д�ҹ������ҧ�Ѵਹ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(24, 1, '�дѺ��� 1 ��� ����ʴ��ĵԡ����ѹ����������
����ʴ��ĵԡ���������Ҿ�������������� ��������֡��Ҷ١��е�鹷ҧ������ ������ö�ЧѺ��á�зӹ�������
ʹ��������ʴ��ĵԡ����ع�ѹ��ѹ���', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(24, 2, '�дѺ��� 2 ��� �ʴ����ö���дѺ��� 1 �����ա����§�������§ູʶҹ��ó��������Դ�����ع�ç�ҧ������ 
�Ҩ����§�͡仨ҡʶҹ��ó� (��������Դ�����ع�ç�ҧ������) ���Ǥ����ҡ��з��� �����Ҩ����¹��Ǣ��ʹ��� ������ش�ѡ���Ǥ�������ʧ�ʵ�������', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(24, 3, '�дѺ��� 3 ��� �ʴ����ö���дѺ��� 2 ����վĵԡ����ͺ�������ҧʧ� ���ж١�����بҡ���µç���� 
����֡�֧�����ع�ç�ҧ������������ҧ���ʹ��� ���͡�û�Ժѵԧҹ �� �����ø �����Դ��ѧ ���ͤ������ѹ ��������ʴ��͡�� �����ͺ�ع�ç���ж١�����بҡ���µç���� ����ѧ����ͧʵԻ�ԺѵԵ����������ҧʧ�', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(24, 4, '�дѺ��� 4 ��� �ʴ����ö���дѺ��� 3 ����վĵԡ����ͺ�������ҧ���ҧ��ä� �������ʶҹ��ó��������Դ�����ع�ç�ҧ������
����֡�֧�����ع�ç�ҧ������������ҧ���ʹ��� ���͡�û�Ժѵԧҹ ������ö���͡�Ըա���ʴ��͡㹷ҧ���ҧ��ä��������ʶҹ��ó����բ��', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(24, 5, '�дѺ��� 5 ��� �ʴ����ö���дѺ��� 4 ��ШѴ��ä������´���ҧ�ջ���Է���Ҿ
����ö��Ժѵԧҹ���͵ͺʹͧ���ҧ���ҧ��ä������Ф������ѹ������ͧ
����ö�Ѵ��áѺ�������´���ͼš�з�����Ҩ���Դ��鹨ҡ�����ع�ç�ҧ�����������ҧ�ջ���Է���Ҿ
�Ҩ����ء�����Ըա��੾�е� �����ҧἹ��ǧ˹�����ͨѴ��áѺ��������Ф������´����Ҩ���Դ���', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(25, 0, '�дѺ��� 0 ��� ����ʴ����ö�д�ҹ������ҧ�Ѵਹ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(25, 1, '�дѺ��� 1 ��� ����֡��ҵ����Է���ӹҨ㹧ҹ/����Ңͧ�ҹ
�Ѻ�Դ�ͺ��ͼŧҹ��á�зӢͧ���ͧ 
���ʹͷҧ��ѭ�� ������ʹ���ѭ��', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(25, 2, '�дѺ��� 2 ��� �ʴ����ö���дѺ��� 1 ��з��������ѧ�Ѻ�ѭ������֡��ҵ����ѡ��Ҿ
���㨢�ʹ���Т�ʹ��¢ͧ�����ѧ�Ѻ�ѭ����Ъ����˹�ҧ����ʹѺʹع���������ʹ����ⴴ��
����͡�ʼ����ѧ�Ѻ�ѭ�����ʴ��͡�֧�ѡ��Ҿ��ҹ�բͧ�������������������㹡�û�Ժѵ�˹�ҷ��', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(25, 3, '�дѺ��� 3 ��� �ʴ����ö���дѺ��� 2 �������͡�ʼ����ѧ�Ѻ�ѭ��㹡���ʴ���������ö㹡�÷ӧҹ
�ͺ���§ҹ��Ш� ��ʹ����Ѿ�ҡ÷����� �Ӫ������С��ʹѺʹع��ҧ� �������ҹ�����
��������������§��ҧ ������������蹵Ѵ�Թ�㹺ҧ����ͧ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(25, 4, '�дѺ��� 4 ��� �ʴ����ö���дѺ��� 3 ��Ъ��¢�Ѵ��ͨӡѴ�ͧ�����ѧ�Ѻ�ѭ�����;Ѳ���ѡ��Ҿ
���»�Ѻ����¹��ȹ����������繻Ѩ��¢Ѵ��ҧ��þѲ���ѡ��Ҿ�ͧ�����ѧ�Ѻ�ѭ�� 
�ըԵ�Է��㹡����Ҷ֧�Ե�����˵ؼ����ͧ��ѧ�ĵԡ����ͧ���кؤ�� ���͹���ʹѺʹع㹡�������ҧ�������� ��Ф�ҹ����ԧź��оѲ���ѡ��Ҿ㹡�÷ӧҹ���բ��', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(25, 5, '�дѺ��� 5 ��� �ʴ����ö���дѺ��� 4 ����Դ�͡���������ѧ�Ѻ�ѭ�������������еѴ�Թ��ͧ
�Դ�͡��������Ѻѧ�Ѻ�ѭ��������������������µ��ͧ�¡���ͺ�����ӹҨ�Ѵ�Թ���� 
���ҧ��������֡�Ѻ�Դ�ͺ㹧ҹ�����������¡��������ѧ�ҡ ��������������ʴ������㹧ҹ�Ӥѭ�', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(26, 0, '�дѺ��� 0 ��� ����ʴ����ö�д�ҹ������ҧ�Ѵਹ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(26, 1, '�дѺ��� 1', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(26, 2, '�дѺ��� 2', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(26, 3, '�дѺ��� 3', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(26, 4, '�дѺ��� 4', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(26, 5, '�дѺ��� 5', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " SELECT COUNT(WORKFLOW_ID) AS COUNT_DATA FROM CONFIG_WORKFLOW ";
		$count_data = $db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		if (!$count_data) {
			if($DPISDB=="odbc") 
				$cmd = " CREATE TABLE CONFIG_WORKFLOW (
				WORKFLOW_ID INTEGER NOT NULL,	
				LV_ID INTEGER NOT NULL,	
				LV_NAME VARCHAR(20) NOT NULL,	
				LV_DESCRIPTION MEMO NOT NULL,
				WORKFLOW_TIMES CHAR(1) NOT NULL,	
				UPDATE_USER INTEGER2 NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_CONFIG_WORKFLOW  PRIMARY KEY (WORKFLOW_ID)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE CONFIG_WORKFLOW (
				WORKFLOW_ID NUMBER(10) NOT NULL,	
				LV_ID NUMBER(10) NOT NULL,	
				LV_NAME VARCHAR2(20) NOT NULL,	
				LV_DESCRIPTION VARCHAR2(1000) NOT NULL,
				WORKFLOW_TIMES CHAR(1) NOT NULL,	
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				CONSTRAINT PK_CONFIG_WORKFLOW  PRIMARY KEY (WORKFLOW_ID)) ";
			elseif($DPISDB=="mysql") 
				$cmd = " CREATE TABLE CONFIG_WORKFLOW (
				WORKFLOW_ID INTEGER(10) NOT NULL,	
				LV_ID INTEGER(10) NOT NULL,	
				LV_NAME VARCHAR(20) NOT NULL,	
				LV_DESCRIPTION TEXT NOT NULL,
				WORKFLOW_TIMES CHAR(1) NOT NULL,	
				UPDATE_USER SMALLINT(5) NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_CONFIG_WORKFLOW  PRIMARY KEY (WORKFLOW_ID)) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end if

		$cmd = " INSERT INTO CONFIG_WORKFLOW (WORKFLOW_ID, LV_ID, LV_NAME, LV_DESCRIPTION, WORKFLOW_TIMES, UPDATE_USER, UPDATE_DATE) 
						VALUES(1, 1, 'NOT SPECIFY', '�ѧ����ա�á�˹����˹�����', '1', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO CONFIG_WORKFLOW (WORKFLOW_ID, LV_ID, LV_NAME, LV_DESCRIPTION, WORKFLOW_TIMES, UPDATE_USER, UPDATE_DATE) 
						VALUES(2, 2, 'O1', '����������� �дѺ��Ժѵԧҹ', '1', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO CONFIG_WORKFLOW (WORKFLOW_ID, LV_ID, LV_NAME, LV_DESCRIPTION, WORKFLOW_TIMES, UPDATE_USER, UPDATE_DATE) 
						VALUES(3, 3, 'O2', '����������� �дѺ�ӹҭ�ҹ', '1', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO CONFIG_WORKFLOW (WORKFLOW_ID, LV_ID, LV_NAME, LV_DESCRIPTION, WORKFLOW_TIMES, UPDATE_USER, UPDATE_DATE) 
						VALUES(4, 4, 'O3', '����������� �дѺ������', '1', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO CONFIG_WORKFLOW (WORKFLOW_ID, LV_ID, LV_NAME, LV_DESCRIPTION, WORKFLOW_TIMES, UPDATE_USER, UPDATE_DATE) 
						VALUES(5, 5, 'O4', '����������� �дѺ�ѡ�о����', '1', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO CONFIG_WORKFLOW (WORKFLOW_ID, LV_ID, LV_NAME, LV_DESCRIPTION, WORKFLOW_TIMES, UPDATE_USER, UPDATE_DATE) 
						VALUES(6, 6, 'K1', '�������Ԫҡ�� �дѺ��Ժѵԡ��', '1', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO CONFIG_WORKFLOW (WORKFLOW_ID, LV_ID, LV_NAME, LV_DESCRIPTION, WORKFLOW_TIMES, UPDATE_USER, UPDATE_DATE) 
						VALUES(7, 7, 'K2', '�������Ԫҡ�� �дѺ�ӹҭ���', '1', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO CONFIG_WORKFLOW (WORKFLOW_ID, LV_ID, LV_NAME, LV_DESCRIPTION, WORKFLOW_TIMES, UPDATE_USER, UPDATE_DATE) 
						VALUES(8, 8, 'K3', '�������Ԫҡ�� �дѺ�ӹҭ��þ����', '1', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO CONFIG_WORKFLOW (WORKFLOW_ID, LV_ID, LV_NAME, LV_DESCRIPTION, WORKFLOW_TIMES, UPDATE_USER, UPDATE_DATE) 
						VALUES(9, 9, 'K4', '�������Ԫҡ�� �дѺ����Ǫҭ', '1', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO CONFIG_WORKFLOW (WORKFLOW_ID, LV_ID, LV_NAME, LV_DESCRIPTION, WORKFLOW_TIMES, UPDATE_USER, UPDATE_DATE) 
						VALUES(10, 10, 'K5', '�������Ԫҡ�� �дѺ�ç�س�ز�', '1', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO CONFIG_WORKFLOW (WORKFLOW_ID, LV_ID, LV_NAME, LV_DESCRIPTION, WORKFLOW_TIMES, UPDATE_USER, UPDATE_DATE) 
						VALUES(11, 11, 'D1', '�������ӹ�¡�� �дѺ��', '1', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO CONFIG_WORKFLOW (WORKFLOW_ID, LV_ID, LV_NAME, LV_DESCRIPTION, WORKFLOW_TIMES, UPDATE_USER, UPDATE_DATE) 
						VALUES(12, 12, 'D2', '�������ӹ�¡�� �дѺ�٧', '1', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO CONFIG_WORKFLOW (WORKFLOW_ID, LV_ID, LV_NAME, LV_DESCRIPTION, WORKFLOW_TIMES, UPDATE_USER, UPDATE_DATE) 
						VALUES(13, 13, 'M1', '������������ �дѺ��', '1', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO CONFIG_WORKFLOW (WORKFLOW_ID, LV_ID, LV_NAME, LV_DESCRIPTION, WORKFLOW_TIMES, UPDATE_USER, UPDATE_DATE) 
						VALUES(14, 14, 'M2', '������������ �дѺ�٧', '1', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO CONFIG_WORKFLOW (WORKFLOW_ID, LV_ID, LV_NAME, LV_DESCRIPTION, WORKFLOW_TIMES, UPDATE_USER, UPDATE_DATE) 
						VALUES(15, 15, 'O1/O2', '����������� �дѺ��Ժѵԧҹ/����������� �дѺ�ӹҭ�ҹ', '1', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO CONFIG_WORKFLOW (WORKFLOW_ID, LV_ID, LV_NAME, LV_DESCRIPTION, WORKFLOW_TIMES, UPDATE_USER, UPDATE_DATE) 
						VALUES(16, 16, 'O2/O3', '����������� �дѺ�ӹҭ�ҹ/����������� �дѺ������', '1', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO CONFIG_WORKFLOW (WORKFLOW_ID, LV_ID, LV_NAME, LV_DESCRIPTION, WORKFLOW_TIMES, UPDATE_USER, UPDATE_DATE) 
						VALUES(19, 17, 'K1/K2', '�������Ԫҡ�� �дѺ��Ժѵԡ��/�������Ԫҡ�� �дѺ�ӹҭ���', '1', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO CONFIG_WORKFLOW (WORKFLOW_ID, LV_ID, LV_NAME, LV_DESCRIPTION, WORKFLOW_TIMES, UPDATE_USER, UPDATE_DATE) 
						VALUES(20, 18, 'K2/K3', '�������Ԫҡ�� �дѺ�ӹҭ���/�������Ԫҡ�� �дѺ�ӹҭ��þ����', '1', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO CONFIG_WORKFLOW (WORKFLOW_ID, LV_ID, LV_NAME, LV_DESCRIPTION, WORKFLOW_TIMES, UPDATE_USER, UPDATE_DATE) 
						VALUES(21, 19, 'K3/K4', '�������Ԫҡ�� �дѺ�ӹҭ��þ����/�������Ԫҡ�� �дѺ����Ǫҭ', '1', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO CONFIG_WORKFLOW (WORKFLOW_ID, LV_ID, LV_NAME, LV_DESCRIPTION, WORKFLOW_TIMES, UPDATE_USER, UPDATE_DATE) 
						VALUES(22, 20, 'K4/K5', '�������Ԫҡ�� �дѺ����Ǫҭ/�������Ԫҡ�� �дѺ�ç�س�ز�', '1', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " SELECT COUNT(ID) AS COUNT_DATA FROM CONFIG_JOB_EVALUATION ";
		$count_data = $db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		if (!$count_data) {
			if($DPISDB=="odbc") 
				$cmd = " CREATE TABLE CONFIG_JOB_EVALUATION (
				ID INTEGER NOT NULL,	
				START_TEST VARCHAR(19) NOT NULL,		
				END_TEST VARCHAR(19) NOT NULL,		
				END_APPROVE VARCHAR(19) NOT NULL,		
				UPDATE_USER INTEGER2 NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_CONFIG_JOB_EVALUATION  PRIMARY KEY (ID)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE CONFIG_JOB_EVALUATION (
				ID NUMBER(10) NOT NULL,	
				START_TEST VARCHAR2(19) NOT NULL,		
				END_TEST VARCHAR2(19) NOT NULL,		
				END_APPROVE VARCHAR2(19) NOT NULL,		
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				CONSTRAINT PK_CONFIG_JOB_EVALUATION  PRIMARY KEY (ID)) ";
			elseif($DPISDB=="mysql") 
				$cmd = " CREATE TABLE CONFIG_JOB_EVALUATION (
				ID INTEGER(10) NOT NULL,	
				START_TEST VARCHAR(19) NOT NULL,		
				END_TEST VARCHAR(19) NOT NULL,		
				END_APPROVE VARCHAR(19) NOT NULL,		
				UPDATE_USER SMALLINT(5) NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_CONFIG_JOB_EVALUATION  PRIMARY KEY (ID)) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end if

		$cmd = " SELECT ORG_ID FROM PER_ORG WHERE OL_CODE = '02' AND ORG_ACTIVE = 1 ORDER BY ORG_SEQ_NO, ORG_CODE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		while($data = $db_dpis->get_array()){
			$ORG_ID = $data[ORG_ID];

			$cmd = " SELECT ID FROM CONFIG_JOB_EVALUATION WHERE ID = $ORG_ID ";
			$count_data = $db_dpis1->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) {
				$cmd = " INSERT INTO CONFIG_JOB_EVALUATION (ID, START_TEST, END_TEST, END_APPROVE, UPDATE_USER, UPDATE_DATE) 
								VALUES($ORG_ID, '2006-01-01', '2012-12-31', '2010-12-31', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
			} // end if
		} // end while						

		$cmd = " SELECT COUNT(ACC_TYPE_ID) AS COUNT_DATA FROM ACCOUNTABILITY_LEVEL_TYPE ";
		$count_data = $db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		if (!$count_data) {
			if($DPISDB=="odbc") 
				$cmd = " CREATE TABLE ACCOUNTABILITY_LEVEL_TYPE (
				TYPE_OF_CIVIL_SERVICE CHAR(1) NOT NULL,
				ACC_TYPE_ID INTEGER NOT NULL,	
				UPDATE_USER INTEGER2 NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_ACCOUNTABILITY_LEVEL_TYPE  PRIMARY KEY (TYPE_OF_CIVIL_SERVICE, ACC_TYPE_ID)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE ACCOUNTABILITY_LEVEL_TYPE (
				TYPE_OF_CIVIL_SERVICE CHAR(1) NOT NULL,
				ACC_TYPE_ID NUMBER(10) NOT NULL,	
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				CONSTRAINT PK_ACCOUNTABILITY_LEVEL_TYPE  PRIMARY KEY (TYPE_OF_CIVIL_SERVICE, ACC_TYPE_ID)) ";
			elseif($DPISDB=="mysql") 
				$cmd = " CREATE TABLE ACCOUNTABILITY_LEVEL_TYPE (
				TYPE_OF_CIVIL_SERVICE CHAR(1) NOT NULL,
				ACC_TYPE_ID INTEGER(10) NOT NULL,	
				UPDATE_USER SMALLINT(5) NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_ACCOUNTABILITY_LEVEL_TYPE  PRIMARY KEY (TYPE_OF_CIVIL_SERVICE, ACC_TYPE_ID)) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end if

		$cmd = " INSERT INTO ACCOUNTABILITY_LEVEL_TYPE (TYPE_OF_CIVIL_SERVICE, ACC_TYPE_ID, UPDATE_USER, UPDATE_DATE) 
						VALUES('O', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO ACCOUNTABILITY_LEVEL_TYPE (TYPE_OF_CIVIL_SERVICE, ACC_TYPE_ID, UPDATE_USER, UPDATE_DATE) 
						VALUES('O', 2, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO ACCOUNTABILITY_LEVEL_TYPE (TYPE_OF_CIVIL_SERVICE, ACC_TYPE_ID, UPDATE_USER, UPDATE_DATE) 
						VALUES('O', 3, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO ACCOUNTABILITY_LEVEL_TYPE (TYPE_OF_CIVIL_SERVICE, ACC_TYPE_ID, UPDATE_USER, UPDATE_DATE) 
						VALUES('K', 4, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO ACCOUNTABILITY_LEVEL_TYPE (TYPE_OF_CIVIL_SERVICE, ACC_TYPE_ID, UPDATE_USER, UPDATE_DATE) 
						VALUES('K', 5, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO ACCOUNTABILITY_LEVEL_TYPE (TYPE_OF_CIVIL_SERVICE, ACC_TYPE_ID, UPDATE_USER, UPDATE_DATE) 
						VALUES('K', 6, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO ACCOUNTABILITY_LEVEL_TYPE (TYPE_OF_CIVIL_SERVICE, ACC_TYPE_ID, UPDATE_USER, UPDATE_DATE) 
						VALUES('K', 7, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO ACCOUNTABILITY_LEVEL_TYPE (TYPE_OF_CIVIL_SERVICE, ACC_TYPE_ID, UPDATE_USER, UPDATE_DATE) 
						VALUES('D', 4, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO ACCOUNTABILITY_LEVEL_TYPE (TYPE_OF_CIVIL_SERVICE, ACC_TYPE_ID, UPDATE_USER, UPDATE_DATE) 
						VALUES('D', 7, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO ACCOUNTABILITY_LEVEL_TYPE (TYPE_OF_CIVIL_SERVICE, ACC_TYPE_ID, UPDATE_USER, UPDATE_DATE) 
						VALUES('D', 8, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO ACCOUNTABILITY_LEVEL_TYPE (TYPE_OF_CIVIL_SERVICE, ACC_TYPE_ID, UPDATE_USER, UPDATE_DATE) 
						VALUES('D', 9, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO ACCOUNTABILITY_LEVEL_TYPE (TYPE_OF_CIVIL_SERVICE, ACC_TYPE_ID, UPDATE_USER, UPDATE_DATE) 
						VALUES('D', 10, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO ACCOUNTABILITY_LEVEL_TYPE (TYPE_OF_CIVIL_SERVICE, ACC_TYPE_ID, UPDATE_USER, UPDATE_DATE) 
						VALUES('M', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO ACCOUNTABILITY_LEVEL_TYPE (TYPE_OF_CIVIL_SERVICE, ACC_TYPE_ID, UPDATE_USER, UPDATE_DATE) 
						VALUES('M', 11, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO ACCOUNTABILITY_LEVEL_TYPE (TYPE_OF_CIVIL_SERVICE, ACC_TYPE_ID, UPDATE_USER, UPDATE_DATE) 
						VALUES('M', 12, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO ACCOUNTABILITY_LEVEL_TYPE (TYPE_OF_CIVIL_SERVICE, ACC_TYPE_ID, UPDATE_USER, UPDATE_DATE) 
						VALUES('M', 13, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " SELECT COUNT(ACC_TYPE_ID) AS COUNT_DATA FROM ACCOUNTABILITY_TYPE ";
		$count_data = $db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		if (!$count_data) {
			if($DPISDB=="odbc") 
				$cmd = " CREATE TABLE ACCOUNTABILITY_TYPE (
				ACC_TYPE_ID INTEGER NOT NULL,	
				ACC_TYPE_NAME VARCHAR(100) NULL,
				ACC_TYPE_ACTIVE CHAR(1) NOT NULL,
				UPDATE_USER INTEGER2 NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_ACCOUNTABILITY_TYPE  PRIMARY KEY (ACC_TYPE_ID)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE ACCOUNTABILITY_TYPE (
				ACC_TYPE_ID NUMBER(10) NOT NULL,	
				ACC_TYPE_NAME VARCHAR2(100) NULL,
				ACC_TYPE_ACTIVE CHAR(1) NOT NULL,
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				CONSTRAINT PK_ACCOUNTABILITY_TYPE  PRIMARY KEY (ACC_TYPE_ID)) ";
			elseif($DPISDB=="mysql") 
				$cmd = " CREATE TABLE ACCOUNTABILITY_TYPE (
				ACC_TYPE_ID INTEGER(10) NOT NULL,	
				ACC_TYPE_NAME VARCHAR(100) NULL,
				ACC_TYPE_ACTIVE CHAR(1) NOT NULL,
				UPDATE_USER SMALLINT(5) NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_ACCOUNTABILITY_TYPE  PRIMARY KEY (ACC_TYPE_ID)) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end if

		$cmd = " INSERT INTO ACCOUNTABILITY_TYPE (ACC_TYPE_ID, ACC_TYPE_NAME, ACC_TYPE_ACTIVE, UPDATE_USER, UPDATE_DATE) 
						VALUES(1, '��ҹ��Ժѵԡ��', '1', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO ACCOUNTABILITY_TYPE (ACC_TYPE_ID, ACC_TYPE_NAME, ACC_TYPE_ACTIVE, UPDATE_USER, UPDATE_DATE) 
						VALUES(2, '��ҹ��ԡ��', '1', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO ACCOUNTABILITY_TYPE (ACC_TYPE_ID, ACC_TYPE_NAME, ACC_TYPE_ACTIVE, UPDATE_USER, UPDATE_DATE) 
						VALUES(3, '�ҹ�ӡѺ����', '1', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO ACCOUNTABILITY_TYPE (ACC_TYPE_ID, ACC_TYPE_NAME, ACC_TYPE_ACTIVE, UPDATE_USER, UPDATE_DATE) 
						VALUES(4, '��ҹ��Ժѵԡ��/�ҹ����Ǫҭ੾�д�ҹ', '1', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO ACCOUNTABILITY_TYPE (ACC_TYPE_ID, ACC_TYPE_NAME, ACC_TYPE_ACTIVE, UPDATE_USER, UPDATE_DATE) 
						VALUES(5, '��ҹ�ҧἹ', '1', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO ACCOUNTABILITY_TYPE (ACC_TYPE_ID, ACC_TYPE_NAME, ACC_TYPE_ACTIVE, UPDATE_USER, UPDATE_DATE) 
						VALUES(6, '��ҹ����ҹ�ҹ', '1', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO ACCOUNTABILITY_TYPE (ACC_TYPE_ID, ACC_TYPE_NAME, ACC_TYPE_ACTIVE, UPDATE_USER, UPDATE_DATE) 
						VALUES(7, '��ҹ��ú�ԡ��', '1', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO ACCOUNTABILITY_TYPE (ACC_TYPE_ID, ACC_TYPE_NAME, ACC_TYPE_ACTIVE, UPDATE_USER, UPDATE_DATE) 
						VALUES(8, '��ҹ��������СӡѺ����', '1', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO ACCOUNTABILITY_TYPE (ACC_TYPE_ID, ACC_TYPE_NAME, ACC_TYPE_ACTIVE, UPDATE_USER, UPDATE_DATE) 
						VALUES(9, '��ҹ����ҧἹ', '1', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO ACCOUNTABILITY_TYPE (ACC_TYPE_ID, ACC_TYPE_NAME, ACC_TYPE_ACTIVE, UPDATE_USER, UPDATE_DATE) 
						VALUES(10, '��ҹ��û���ҹ�ҹ', '1', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO ACCOUNTABILITY_TYPE (ACC_TYPE_ID, ACC_TYPE_NAME, ACC_TYPE_ACTIVE, UPDATE_USER, UPDATE_DATE) 
						VALUES(11, '��ҹ�ҹ�����úؤ��', '1', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO ACCOUNTABILITY_TYPE (ACC_TYPE_ID, ACC_TYPE_NAME, ACC_TYPE_ACTIVE, UPDATE_USER, UPDATE_DATE) 
						VALUES(12, '��ҹ��èѴ��÷�Ѿ�ҡ����ͧ�����ҳ', '1', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO ACCOUNTABILITY_TYPE (ACC_TYPE_ID, ACC_TYPE_NAME, ACC_TYPE_ACTIVE, UPDATE_USER, UPDATE_DATE) 
						VALUES(13, '��ҹ�ҧἹ��÷ӧҹ', '1', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " SELECT COUNT(ACC_ID) AS COUNT_DATA FROM ACCOUNTABILITY_INFO_PRIMARY ";
		$count_data = $db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		if (!$count_data) {
			if($DPISDB=="odbc") 
				$cmd = " CREATE TABLE ACCOUNTABILITY_INFO_PRIMARY (
				ACC_ID INTEGER NOT NULL,	
				POS_DES_ID INTEGER NOT NULL,	
				ACC_TYPE_ID INTEGER NOT NULL,	
				ACC_DESCRIPTION MEMO NULL,
				UPDATE_USER INTEGER2 NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_ACCOUNTABILITY_INFO_PRIMARY  PRIMARY KEY (ACC_ID)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE ACCOUNTABILITY_INFO_PRIMARY (
				ACC_ID NUMBER(10) NOT NULL,	
				POS_DES_ID NUMBER(10) NOT NULL,	
				ACC_TYPE_ID NUMBER(10) NOT NULL,	
				ACC_DESCRIPTION VARCHAR2(1000) NULL,
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				CONSTRAINT PK_ACCOUNTABILITY_INFO_PRIMARY  PRIMARY KEY (ACC_ID)) ";
			elseif($DPISDB=="mysql") 
				$cmd = " CREATE TABLE ACCOUNTABILITY_INFO_PRIMARY (
				ACC_ID INTEGER(10) NOT NULL,	
				POS_DES_ID INTEGER(10) NOT NULL,	
				ACC_TYPE_ID INTEGER(10) NOT NULL,	
				ACC_DESCRIPTION TEXT NULL,
				UPDATE_USER SMALLINT(5) NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_ACCOUNTABILITY_INFO_PRIMARY  PRIMARY KEY (ACC_ID)) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end if

		$cmd = " INSERT INTO ACCOUNTABILITY_TYPE (ACC_TYPE_ID, ACC_TYPE_NAME, ACC_TYPE_ACTIVE, UPDATE_USER, UPDATE_DATE) 
						VALUES(1, '��ҹ��Ժѵԡ��', '1', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

/*
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
*/
		$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV0 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
						  FLAG_SHOW, TYPE_LINKTO, LINKTO_TID, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
						  VALUES (1, 'TH', 48, 7, '��û����Թ��ҧҹ', 'S', 'N', 0, $CREATE_DATE, $CREATE_BY, $CREATE_DATE, 
						  $CREATE_BY) ";
		$db->send_cmd($cmd);
		//$db->show_error();

		$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV0 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
						  FLAG_SHOW, 	TYPE_LINKTO, LINKTO_TID, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
						  VALUES (1, 'EN', 48, 7, '��û����Թ��ҧҹ', 'S', 'N', 0, $CREATE_DATE, $CREATE_BY, $CREATE_DATE, 
						  $CREATE_BY) ";
		$db->send_cmd($cmd);
		//$db->show_error();

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 
						  WHERE LINKTO_WEB = 'master_table_pos_des_info.html?table=POS_DES_INFO' ";
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
							  VALUES (1, 'TH', $MAX_ID, 1, 'J01 �ҵðҹ��˹����˹�', 'S', 'W', 
							  'master_table_pos_des_info.html?table=POS_DES_INFO', 0, 48, $CREATE_DATE, $CREATE_BY, 
							  $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 1, 'J01 �ҵðҹ��˹����˹�', 'S', 'W', 
							  'master_table_pos_des_info.html?table=POS_DES_INFO', 0, 48, $CREATE_DATE, $CREATE_BY, 
							  $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'master_table_knowledge_info.html?table=KNOWLEDGE_INFO' ";
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
							  VALUES (1, 'TH', $MAX_ID, 2, 'J02 �������', 'S', 'W', 'master_table_knowledge_info.html?table=KNOWLEDGE_INFO', 0, 48, $CREATE_DATE, 
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 2, 'J02 �������', 'S', 'W', 'master_table_knowledge_info.html?table=KNOWLEDGE_INFO', 0, 48, $CREATE_DATE, 
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 
						  WHERE LINKTO_WEB = 'master_table_skill_info.html?table=SKILL_INFO' ";
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
							  VALUES (1, 'TH', $MAX_ID, 3, 'J03 �ѡ��', 'S', 'W', 'master_table_skill_info.html?table=SKILL_INFO', 0, 48, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 3, 'J03 �ѡ��', 'S', 'W', 'master_table_skill_info.html?table=SKILL_INFO', 0, 48, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 
						  WHERE LINKTO_WEB = 'master_table_exp_info.html?table=EXP_INFO' ";
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
							  VALUES (1, 'TH', $MAX_ID, 4, 'J04 ���ʺ��ó�', 'S', 'W', 'master_table_exp_info.html?table=EXP_INFO', 0, 48, $CREATE_DATE,
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 4, 'J04 ���ʺ��ó�', 'S', 'W', 'master_table_exp_info.html?table=EXP_INFO', 0, 48, $CREATE_DATE,
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'master_table_competency_info.html?table=COMPETENCY_INFO' ";
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
							  VALUES (1, 'TH', $MAX_ID, 5, 'J05 ���ö��', 'S', 'W', 'master_table_competency_info.html?table=COMPETENCY_INFO', 0, 48, $CREATE_DATE,
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 5, 'J05 ���ö��', 'S', 'W', 'master_table_competency_info.html?table=COMPETENCY_INFO', 0, 48, $CREATE_DATE,
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'set_config_workflow.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 6, 'J06 ��駤�ҡ��͹��ѵԡ�û����Թ��ҧҹ', 'S', 'W', 'set_config_workflow.html', 0, 48, $CREATE_DATE,
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 6, 'J06 ��駤�ҡ��͹��ѵԡ�û����Թ��ҧҹ', 'S', 'W', 'set_config_workflow.html', 0, 48, $CREATE_DATE,
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'set_config_job_evaluation_all.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 7, 'J07 ��駤���������ҡ�û����Թ��ҧҹ�����ǹ�Ҫ���', 'S', 'W', 'set_config_job_evaluation_all.html', 0, 48, $CREATE_DATE,
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 7, 'J07 ��駤���������ҡ�û����Թ��ҧҹ�����ǹ�Ҫ���', 'S', 'W', 'set_config_job_evaluation_all.html', 0, 48, $CREATE_DATE,
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'master_table_new_level.html?table=PER_NEW_LEVEL' ";
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
							  VALUES (1, 'TH', $MAX_ID, 8, 'J08 ������/�дѺ���˹� (����)', 'S', 'W', 'master_table_new_level.html?table=PER_NEW_LEVEL', 0, 48, $CREATE_DATE,
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 8, 'J08 ������/�дѺ���˹� (����)', 'S', 'W', 'master_table_new_level.html?table=PER_NEW_LEVEL', 0, 48, $CREATE_DATE,
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'master_table_accountability_level_type.html?table=ACCOUNTABILITY_LEVEL_TYPE' ";
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
							  VALUES (1, 'TH', $MAX_ID, 9, 'J09 �����������/�дѺ���˹� (����)', 'S', 'W', 'master_table_accountability_level_type.html?table=ACCOUNTABILITY_LEVEL_TYPE', 0, 48, $CREATE_DATE,
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 9, 'J09 �����������/�дѺ���˹� (����)', 'S', 'W', 'master_table_accountability_level_type.html?table=ACCOUNTABILITY_LEVEL_TYPE', 0, 48, $CREATE_DATE,
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'master_table_nolog.html?table=ACCOUNTABILITY_TYPE' ";
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
							  VALUES (1, 'TH', $MAX_ID, 10, 'J10 ������˹�ҷ������Ѻ�Դ�ͺ�ͧ���˹�', 'S', 'W', 'master_table_nolog.html?table=ACCOUNTABILITY_TYPE', 0, 48, $CREATE_DATE,
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 10, 'J10 ������˹�ҷ������Ѻ�Դ�ͺ�ͧ���˹�', 'S', 'W', 'master_table_nolog.html?table=ACCOUNTABILITY_TYPE', 0, 48, $CREATE_DATE,
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE MENU_LABEL = 'M11 �����Թ��ҧҹ' ";
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
							  VALUES (1, 'TH', $MAX_ID1, 11, 'M11 �����Թ��ҧҹ', 'S', 'N', 0, 9, $CREATE_DATE, $CREATE_BY, 
							  $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, UPDATE_DATE, 
							  UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID1, 11, 'M11 �����Թ��ҧҹ', 'S', 'N', 0, 9, $CREATE_DATE, $CREATE_BY, 
							  $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT MENU_ID FROM BACKOFFICE_MENU_BAR_LV1 WHERE MENU_LABEL = 'M11 �����Թ��ҧҹ' ";
		$db->send_cmd($cmd);
		//$db->show_error();
		$data = $db->get_array();
		$MENU_ID = $data[MENU_ID];

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'master_table_jem_acc.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 1, 'M1101 ACC', 'S', 'W', 'master_table_jem_acc.html', 0, 9, $MENU_ID, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
							  CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 1, 'M1101 ACC', 'S', 'W', 'master_table_jem_acc.html', 0, 9, $MENU_ID, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'master_table_jem_answer_info.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 2, 'M1102 �Ӷ�� - �ӵͺẺ�����Թ��ҧҹ', 'S', 'W', 'master_table_jem_answer_info.html', 0, 9, $MENU_ID, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
							  CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 2, 'M1102 �Ӷ�� - �ӵͺẺ�����Թ��ҧҹ', 'S', 'W', 'master_table_jem_answer_info.html', 0, 9, $MENU_ID, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'master_table_jem_consistency_check.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 3, 'M1103 Consistency Check', 'S', 'W', 'master_table_jem_consistency_check.html', 0, 9, $MENU_ID, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
							  CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 3, 'M1103 Consistency Check', 'S', 'W', 'master_table_jem_consistency_check.html', 0, 9, $MENU_ID, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'master_table_jem_grade.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 4, 'M1104 Grade', 'S', 'W', 'master_table_jem_grade.html', 0, 9, $MENU_ID, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
							  CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 4, 'M1104 Grade', 'S', 'W', 'master_table_jem_grade.html', 0, 9, $MENU_ID, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'master_table_jem_kh.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 5, 'M1105 KH', 'S', 'W', 'master_table_jem_kh.html', 0, 9, $MENU_ID, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
							  CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 5, 'M1105 KH', 'S', 'W', 'master_table_jem_kh.html', 0, 9, $MENU_ID, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'master_table_jem_mapping.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 6, 'M1106 Mapping', 'S', 'W', 'master_table_jem_mapping.html', 0, 9, $MENU_ID, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
							  CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 6, 'M1106 Mapping', 'S', 'W', 'master_table_jem_mapping.html', 0, 9, $MENU_ID, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'master_table_jem_profile_check.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 7, 'M1107 Profile Check', 'S', 'W', 'master_table_jem_profile_check.html', 0, 9, $MENU_ID, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
							  CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 7, 'M1107 Profile Check', 'S', 'W', 'master_table_jem_profile_check.html', 0, 9, $MENU_ID, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'master_table_jem_ps.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 8, 'M1108 PS', 'S', 'W', 'master_table_jem_ps.html', 0, 9, $MENU_ID, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
							  CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 8, 'M1108 PS', 'S', 'W', 'master_table_jem_ps.html', 0, 9, $MENU_ID, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'master_table_jem_ps_kh.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 9, 'M1109 PS KH', 'S', 'W', 'master_table_jem_ps_kh.html', 0, 9, $MENU_ID, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
							  CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 9, 'M1109 PS KH', 'S', 'W', 'master_table_jem_ps_kh.html', 0, 9, $MENU_ID, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'master_table_jem_question_info.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 10, 'M1110 ��Ǣ�ͤӶ��Ẻ�����Թ��ҧҹ', 'S', 'W', 'master_table_jem_question_info.html', 0, 9, $MENU_ID, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
							  CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 10, 'M1110 ��Ǣ�ͤӶ��Ẻ�����Թ��ҧҹ', 'S', 'W', 'master_table_jem_question_info.html', 0, 9, $MENU_ID, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE MENU_LABEL = 'R11 ��§ҹ��û����Թ��ҧҹ' ";
		$count_data = $db->send_cmd($cmd);
		//$db->show_error();
		if (!$count_data) {
			$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV1 ";
			$db->send_cmd($cmd);
			//$db->show_error();
			$data = $db->get_array();
			$MENU_ID = $data[MAX_ID] + 1;

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, UPDATE_DATE, 
							  UPDATE_BY)
							  VALUES (1, 'TH', $MENU_ID, 11, 'R11 ��§ҹ��û����Թ��ҧҹ', 'S', 'N', 0, 36, $CREATE_DATE, $CREATE_BY, 
							  $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);	
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, UPDATE_DATE, 
							  UPDATE_BY)
							  VALUES (1, 'EN', $MENU_ID, 11, 'R11 ��§ҹ��û����Թ��ҧҹ', 'S', 'N', 0, 36, $CREATE_DATE, $CREATE_BY, 
							  $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);	
			//$db->show_error();

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_position_list.html' ";
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
								  VALUES (1, 'TH', $MAX_ID, 1, 'R1101 ��ª��͵��˹觢ͧ��ǹ�Ҫ���', 'S', 'W', 'rpt_position_list.html', 0, 36, $MENU_ID, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 1, 'R1101 ��ª��͵��˹觢ͧ��ǹ�Ҫ���', 'S', 'W', 'rpt_position_list.html', 0, 36, $MENU_ID, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'data_file_summary.html' ";
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
								  VALUES (1, 'TH', $MAX_ID, 2, 'R1102 �Ѵ�Ӣ����ź���ػ��������', 'S', 'W', 'data_file_summary.html', 0, 36, $MENU_ID, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 2, 'R1102 �Ѵ�Ӣ����ź���ػ��������', 'S', 'W', 'data_file_summary.html', 0, 36, $MENU_ID, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_executive_summary.html' ";
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
								  VALUES (1, 'TH', $MAX_ID, 3, 'R1103 ����ػ��������', 'S', 'W', 'rpt_executive_summary.html', 0, 36, $MENU_ID, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 3, 'R1103 ����ػ��������', 'S', 'W', 'rpt_executive_summary.html', 0, 36, $MENU_ID, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_evaluation_conclusion.html' ";
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
								  VALUES (1, 'TH', $MAX_ID, 4, 'R1104 ��ػ�š�û����Թ��ҧҹ', 'S', 'W', 'rpt_evaluation_conclusion.html', 0, 36, $MENU_ID, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 4, 'R1104 ��ػ�š�û����Թ��ҧҹ', 'S', 'W', 'rpt_evaluation_conclusion.html', 0, 36, $MENU_ID, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_position_count.html' ";
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
								  VALUES (1, 'TH', $MAX_ID, 5, 'R1105 �����Ũӹǹ���˹�', 'S', 'W', 'rpt_position_count.html', 0, 36, $MENU_ID, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 5, 'R1105 �����Ũӹǹ���˹�', 'S', 'W', 'rpt_position_count.html', 0, 36, $MENU_ID, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_evaluation_measurement.html' ";
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
								  VALUES (1, 'TH', $MAX_ID, 6, 'R1106 �Ѵ�����ҹ�к������Թ��ҧҹ', 'S', 'W', 'rpt_evaluation_measurement.html', 0, 36, $MENU_ID, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 6, 'R1106 �Ѵ�����ҹ�к������Թ��ҧҹ', 'S', 'W', 'rpt_evaluation_measurement.html', 0, 36, $MENU_ID, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}
		}
/*
		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'master_table_competency_info.html?table=COMPETENCY_INFO' ";
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
							  VALUES (1, 'TH', $MAX_ID, 5, 'J05 ���ö��', 'S', 'W', 'master_table_competency_info.html?table=COMPETENCY_INFO', 0, 48, $CREATE_DATE,
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 5, 'J05 ���ö��', 'S', 'W', 'master_table_competency_info.html?table=COMPETENCY_INFO', 0, 48, $CREATE_DATE,
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'master_table_competency_info.html?table=COMPETENCY_INFO' ";
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
							  VALUES (1, 'TH', $MAX_ID, 5, 'J05 ���ö��', 'S', 'W', 'master_table_competency_info.html?table=COMPETENCY_INFO', 0, 48, $CREATE_DATE,
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 5, 'J05 ���ö��', 'S', 'W', 'master_table_competency_info.html?table=COMPETENCY_INFO', 0, 48, $CREATE_DATE,
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'master_table_competency_info.html?table=COMPETENCY_INFO' ";
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
							  VALUES (1, 'TH', $MAX_ID, 5, 'J05 ���ö��', 'S', 'W', 'master_table_competency_info.html?table=COMPETENCY_INFO', 0, 48, $CREATE_DATE,
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 5, 'J05 ���ö��', 'S', 'W', 'master_table_competency_info.html?table=COMPETENCY_INFO', 0, 48, $CREATE_DATE,
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'master_table_competency_info.html?table=COMPETENCY_INFO' ";
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
							  VALUES (1, 'TH', $MAX_ID, 5, 'J05 ���ö��', 'S', 'W', 'master_table_competency_info.html?table=COMPETENCY_INFO', 0, 48, $CREATE_DATE,
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 5, 'J05 ���ö��', 'S', 'W', 'master_table_competency_info.html?table=COMPETENCY_INFO', 0, 48, $CREATE_DATE,
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'master_table_competency_info.html?table=COMPETENCY_INFO' ";
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
							  VALUES (1, 'TH', $MAX_ID, 5, 'J05 ���ö��', 'S', 'W', 'master_table_competency_info.html?table=COMPETENCY_INFO', 0, 48, $CREATE_DATE,
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 5, 'J05 ���ö��', 'S', 'W', 'master_table_competency_info.html?table=COMPETENCY_INFO', 0, 48, $CREATE_DATE,
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'master_table_competency_info.html?table=COMPETENCY_INFO' ";
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
							  VALUES (1, 'TH', $MAX_ID, 5, 'J05 ���ö��', 'S', 'W', 'master_table_competency_info.html?table=COMPETENCY_INFO', 0, 48, $CREATE_DATE,
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 5, 'J05 ���ö��', 'S', 'W', 'master_table_competency_info.html?table=COMPETENCY_INFO', 0, 48, $CREATE_DATE,
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'master_table_competency_info.html?table=COMPETENCY_INFO' ";
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
							  VALUES (1, 'TH', $MAX_ID, 5, 'J05 ���ö��', 'S', 'W', 'master_table_competency_info.html?table=COMPETENCY_INFO', 0, 48, $CREATE_DATE,
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 5, 'J05 ���ö��', 'S', 'W', 'master_table_competency_info.html?table=COMPETENCY_INFO', 0, 48, $CREATE_DATE,
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'master_table_competency_info.html?table=COMPETENCY_INFO' ";
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
							  VALUES (1, 'TH', $MAX_ID, 5, 'J05 ���ö��', 'S', 'W', 'master_table_competency_info.html?table=COMPETENCY_INFO', 0, 48, $CREATE_DATE,
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 5, 'J05 ���ö��', 'S', 'W', 'master_table_competency_info.html?table=COMPETENCY_INFO', 0, 48, $CREATE_DATE,
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'master_table_competency_info.html?table=COMPETENCY_INFO' ";
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
							  VALUES (1, 'TH', $MAX_ID, 5, 'J05 ���ö��', 'S', 'W', 'master_table_competency_info.html?table=COMPETENCY_INFO', 0, 48, $CREATE_DATE,
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 5, 'J05 ���ö��', 'S', 'W', 'master_table_competency_info.html?table=COMPETENCY_INFO', 0, 48, $CREATE_DATE,
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'master_table_competency_info.html?table=COMPETENCY_INFO' ";
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
							  VALUES (1, 'TH', $MAX_ID, 5, 'J05 ���ö��', 'S', 'W', 'master_table_competency_info.html?table=COMPETENCY_INFO', 0, 48, $CREATE_DATE,
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 5, 'J05 ���ö��', 'S', 'W', 'master_table_competency_info.html?table=COMPETENCY_INFO', 0, 48, $CREATE_DATE,
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'master_table_competency_info.html?table=COMPETENCY_INFO' ";
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
							  VALUES (1, 'TH', $MAX_ID, 5, 'J05 ���ö��', 'S', 'W', 'master_table_competency_info.html?table=COMPETENCY_INFO', 0, 48, $CREATE_DATE,
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 5, 'J05 ���ö��', 'S', 'W', 'master_table_competency_info.html?table=COMPETENCY_INFO', 0, 48, $CREATE_DATE,
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		} */
?>