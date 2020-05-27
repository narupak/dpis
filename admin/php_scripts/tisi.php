<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$CREATE_DATE = "NOW()";
	$UPDATE_DATE = date("Y-m-d H:i:s");

	if( $command=='ALTER' ) {
		$cmd = " DROP TABLE PER_COMPETENCE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " CREATE TABLE PER_COMPETENCE(
			CP_CODE VARCHAR(3) NOT NULL,	
			CP_NAME VARCHAR(100) NOT NULL,	
			CP_ENG_NAME VARCHAR(100) NOT NULL,	
			CP_MEANING MEMO NULL,	
			CP_MODEL SINGLE NOT NULL,
			CP_ASSESSMENT CHAR(1) NULL,
			CP_ACTIVE SINGLE NOT NULL,
			UPDATE_USER INTEGER2 NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_COMPETENCE PRIMARY KEY (cp_code)) ";
		elseif($DPISDB=="oci8") 
			$cmd = " CREATE TABLE PER_COMPETENCE(
			CP_CODE VARCHAR2(3) NOT NULL,	
			CP_NAME VARCHAR2(100) NOT NULL,	
			CP_ENG_NAME VARCHAR2(100) NOT NULL,	
			CP_MEANING VARCHAR2(1000) NULL,	
			CP_MODEL NUMBER(1) NOT NULL,
			CP_ASSESSMENT CHAR(1) NULL,
			CP_ACTIVE NUMBER(1) NOT NULL,
			UPDATE_USER NUMBER(5) NOT NULL,
			UPDATE_DATE VARCHAR2(19) NOT NULL,		
			CONSTRAINT PK_PER_COMPETENCE PRIMARY KEY (cp_code)) ";
		elseif($DPISDB=="mysql")
			$cmd = " CREATE TABLE PER_COMPETENCE(
			CP_CODE VARCHAR(3) NOT NULL,	
			CP_NAME VARCHAR(100) NOT NULL,	
			CP_ENG_NAME VARCHAR(100) NOT NULL,	
			CP_MEANING TEXT NULL,	
			CP_MODEL SMALLINT(1) NOT NULL,
			CP_ASSESSMENT CHAR(1) NULL,
			CP_ACTIVE SMALLINT(1) NOT NULL,
			UPDATE_USER SMALLINT(5) NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_COMPETENCE PRIMARY KEY (cp_code)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
						  UPDATE_USER, UPDATE_DATE)
						  VALUES ('101', '�����觼����ķ���', 'Achievement Motivation- ACH', '���������蹨л�Ժѵ��Ҫ���������������Թ�ҵðҹ��������� ���ҵðҹ����Ҩ�繼š�û�Ժѵԧҹ����ҹ�Ңͧ���ͧ ����ࡳ���Ѵ�����ķ�������ǹ�Ҫ��á�˹���� �ա����ѧ��������֧������ҧ��ä�Ѳ�Ҽŧҹ���͡�кǹ��û�Ժѵԧҹ���������·���ҡ��з�ҷ�ª�Դ����Ҩ������ռ�������ö��з����ҡ�͹', 1, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
						  UPDATE_USER, UPDATE_DATE)
						  VALUES ('102', '��ԡ�÷���', 'Service Mind- SERV', '���ö�й���鹤���������Ф����������ͧ����Ҫ���㹡������ԡ������ʹͧ������ͧ��âͧ��ЪҪ���ʹ���ͧ˹��§ҹ�Ҥ�Ѱ���� �������Ǣ�ͧ', 1, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
						  UPDATE_USER, UPDATE_DATE)
						  VALUES ('103', '����������������Ǫҭ㹧ҹ�Ҫվ', 'Expertise- EXP', '�����ǹ���� ʹ������ ����������Ѳ���ѡ��Ҿ ��������������ö�ͧ��㹡�û�Ժѵ��Ҫ��� ���¡���֡�� �鹤����Ҥ������ �Ѳ�ҵ��ͧ���ҧ������ͧ �ա������ѡ�Ѳ�� ��Ѻ��ا ����ء�����������ԧ�Ԫҡ�����෤����յ�ҧ� ��ҡѺ��û�Ժѵԧҹ����Դ�����ķ���', 1, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
						  UPDATE_USER, UPDATE_DATE)
						  VALUES ('104', '����ִ���㹤����١��ͧ�ͺ���� ��Ш��¸���', 'Expertise- EXP', '��ä�ͧ����л�оĵԻ�ԺѵԶ١��ͧ���������駵����ѡ��������Фس�������¸��� ��ʹ����ѡ�Ƿҧ��ԪҪվ�ͧ������觻���ª��ͧ����Ȫҵ��ҡ���һ���ª����ǹ��  ��駹�����͸�ç�ѡ���ѡ����������Ҫվ����Ҫ��� �ա��������繡��ѧ�Ӥѭ㹡��ʹѺʹع��ѡ�ѹ�����áԨ��ѡ�Ҥ�Ѱ�����������·���˹����', 1, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
						  UPDATE_USER, UPDATE_DATE)
						  VALUES ('105', '��÷ӧҹ�繷��', 'Teamwork- TW', '���ö�й���鹷�� 1) �������㨷��зӧҹ�����Ѻ������ ����ǹ˹��㹷���ҹ ˹��§ҹ ����ͧ��� �¼�黯Ժѵ��հҹ�����Ҫԡ㹷�� ����㹰ҹ����˹�ҷ�� ��� 2) ��������ö㹡�����ҧ��д�ç�ѡ������ѹ��Ҿ�Ѻ��Ҫԡ㹷��', 1, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
						  UPDATE_USER, UPDATE_DATE)
						  VALUES ('201', '����м���', 'Leadership- LEAD', '�����������ͤ�������ö㹡���繼��Ӣͧ������� ����ͧ ����֧��á�˹���ȷҧ ����·�ȹ� ������� �Ըա�÷ӧҹ �������ѧ�Ѻ�ѭ�����ͷ���ҹ��Ժѵԧҹ�����ҧ�Һ��� �������Է���Ҿ��к�����ѵ�ػ��ʧ��ͧͧ���', 2, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
						  UPDATE_USER, UPDATE_DATE)
						  VALUES ('202', '����·�ȹ�', 'Visioning- VIS', '��������ö����ȷҧ���Ѵਹ��С�ͤ��������ç��������������ѧ�Ѻ�ѭ�����͹Ӿҧҹ�Ҥ�Ѱ����ش���������ѹ', 2, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
						  UPDATE_USER, UPDATE_DATE)
						  VALUES ('203', '����ҧ���ط���Ҥ�Ѱ', 'Strategic Orientation- SO', '�������㨡��ط���Ҥ�Ѱ�������ö����ء����㹡�á�˹����ط��ͧ˹��§ҹ���� �¤�������ö㹡�û���ء��������֧��������ö㹡�äҴ��ó�֧��ȷҧ�к��Ҫ����͹Ҥ� ��ʹ���š�з��ͧʶҹ��ó������е�ҧ����ȷ���Դ���', 2, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
						  UPDATE_USER, UPDATE_DATE)
						  VALUES ('204', '�ѡ��Ҿ���͹ӡ�û�Ѻ����¹', 'Change Leadership- CL', '����������Ф�������ö㹡�á�е�鹼�ѡ�ѹ�����������Դ������ͧ��èл�Ѻ����¹���Ƿҧ����繻���ª�����Ҥ�Ѱ ����֧�������������������Ѻ��� ���� ��д��Թ�������û�Ѻ����¹����Դ��鹨�ԧ', 2, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
						  UPDATE_USER, UPDATE_DATE)
						  VALUES ('205', '��äǺ������ͧ', 'Self Control- SCT', '����ЧѺ��������оĵԡ����ѹ��������������Ͷ١������ ����༪ԭ˹�ҡѺ���µç���� ༪ԭ����������Ե� ���ͷӧҹ���������Ф������ѹ ����֧����ʹ��ʹ��������͵�ͧ���������ʶҹ��ó����ͤ������´���ҧ������ͧ', 2, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
						  UPDATE_USER, UPDATE_DATE)
						  VALUES ('206', '����͹�ҹ�������ӹҨ�������', 'Coaching and Empowering Others - CEMP', '�������㨨��������������¹������͡�þѲ�Ҽ������������� ����֧�����������㹤�������ö�ͧ������ �ѧ��鹨֧�ͺ�����ӹҨ���˹�ҷ���Ѻ�Դ�ͺ����������������������㹡�����ҧ��ä��Ըա�âͧ�����ͺ�����������㹧ҹ', 2, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
						  UPDATE_USER, UPDATE_DATE)
						  VALUES ('301', '��áӡѺ�Դ������ҧ��������', 'Monitoring and Overseeing- MO', 'ਵ�ҷ��СӡѺ���� ��еԴ�����ô��Թ�ҹ��ҧ� �ͧ�����蹷������Ǣ�ͧ��黯ԺѵԵ���ҵðҹ ������º ���͢�ͺѧ�Ѻ����˹���� ��������ӹҨ�������º ������ ���͵�����˹�˹�ҷ�������������ҧ�����������ջ���Է���Ҿ����觻���ª��ͧ˹��§ҹ ͧ��� ���ͻ���Ȫҵ����Ӥѭ', 3, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
						  UPDATE_USER, UPDATE_DATE)
						  VALUES ('302', '����ִ������ѡࡳ��', 'Acts with Integrity- AI', 'ਵ�ҷ��СӡѺ����������������˹��§ҹ��蹻�Ժѵ���������ҵðҹ ������º��ͺѧ�Ѻ����˹���� ��������ӹҨ�������º ������ ���͵����ѡ�Ƿҧ��ԪҪվ�ͧ��������������ҧ�����������ջ���Է���Ҿ����觻���ª��ͧͧ��� �ѧ�� ��л������������Ӥѭ ��������ö����Ҩ����֧����׹��Ѵ���觷��١��ͧ��Ф����索Ҵ㹡�èѴ��áѺ�ؤ������˹��§ҹ����ҽ׹��ࡳ�� ����º�����ҵðҹ��������', 3, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
						  UPDATE_USER, UPDATE_DATE)
						  VALUES ('303', '�������������С���ѧ������', 'Analytical and Synthesis Thinking - AST', '��������ö㹡�äԴ����������зӤ���������ԧ�ѧ������ ����֧����ͧ�Ҿ����ͧͧ��� �����繡�ͺ�����Դ�����ǤԴ���� �ѹ�繼��Ҩҡ�����ػ�ٻẺ ����ء���Ƿҧ��ҧ� �ҡʶҹ��ó����͢�������ҡ���� ��йҹҷ�ȹ�', 3, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
						  UPDATE_USER, UPDATE_DATE)
						  VALUES ('304', '�����١��ͧ�ͧ�ҹ', 'Accuracy and Order- AO', '�������������л�Ժѵԧҹ���١��ͧ�ú��ǹ��ʹ��Ŵ��ͺ����ͧ����Ҩ���Դ��� ����֧��äǺ�����Ǩ������ҹ��仵��Ἱ����ҧ������ҧ�١��ͧ�Ѵਹ', 3, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
						  UPDATE_USER, UPDATE_DATE)
						  VALUES ('305', '��������������С�����ҧ����ѹ��', 'Providing Knowledge and Building Relationships- PKBR', '�վĵԡ������������ ��е��㨷��й����Իѭ�� ��ѵ���� ෤����� ��������Ǫҭ ���ͧ���������ҧ� �������� ʹѺʹع ��оѲ�Ҽ���Сͺ��� �������͢��� �Ǻ���仡Ѻ������ҧ �Ѳ�� ����ѡ�Ҥ�������ѹ���ѹ�աѺ����Сͺ��� �������͢��� ����������Сͺ��� �������͢��� �դ������ �������� �������ö �����Ѳ��˹��§ҹ����ջ���ª�� �ա����繡����������ҧ�մ��������ö㹡���觢ѹ���ҧ����׹', 3, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
						  UPDATE_USER, UPDATE_DATE)
						  VALUES ('306', '����ҧἹ��С�èѴ���', 'Planning and Organizing- PO', '��������ö㹡���ҧἹ���ҧ����ѡ��� �����������ö��任�Ժѵ����ԧ��ж١��ͧ ����֧��������ö㹡�ú����èѴ����ç��õ�ҧ� 㹤����Ѻ�Դ�ͺ�������ö�����������·���˹�������ҧ�ջ���Է���Ҿ�٧�ش', 3, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
						  UPDATE_USER, UPDATE_DATE)
						  VALUES ('307', '��ú����÷�Ѿ�ҡ�', 'Resource Management- RM', '��õ��˹ѡ���Ͷ֧����������������ҧ��Ѿ�ҡ� (������ҳ ���� ���ѧ������ͧ��� �ػ�ó� ���) ���ŧ�ع����ͷ�����û�Ժѵ���áԨ (Input) �Ѻ���Ѿ������ (Output) ��о�������Ѻ��ا����Ŵ��鹵͹��û�Ժѵԧҹ ���;Ѳ������û�Ժѵԧҹ�Դ���������������ջ���Է���Ҿ�٧�ش �Ҩ��������֧��������ö㹡�èѴ�����Ӥѭ㹡�������� ��Ѿ�ҡ� ��Т��������ҧ������� ��л����Ѵ���������٧�ش', 3, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
						  UPDATE_USER, UPDATE_DATE)
						  VALUES ('308', '�������㨤�����״���蹵��ʶҹ��ó�', 'Understanding People and Adaptability- UPA', '��������ö㹡���Ѻ�ѧ������㨺ؤ������ʶҹ��ó� ��о�������л�Ѻ����¹����ʹ���ͧ�Ѻʶҹ��ó����͡�����������ҡ���� 㹢�з���ѧ����Ժѵԧҹ�����ҧ�ջ���Է���Ҿ��к���ؼŵ��������·�������', 3, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
						  UPDATE_USER, UPDATE_DATE)
						  VALUES ('309', '���������ͧ�������к��ҹ', 'Organization and Process Understanding - OPU', '���������������ö����ء�����������ѹ��������§�ͧ෤����� �к� ��кǹ��÷ӧҹ ����ҵðҹ��÷ӧҹ�ͧ����Тͧ˹��§ҹ���� �������Ǣ�ͧ ���ͻ���ª��㹡�û�Ժѵ�˹�ҷ��������ؼ� �������㨹������֧��������ö㹡���ͧ�Ҿ�˭� (Big Picture) ��С�äҴ��ó��������������ͧ�Ѻ�������¹�ŧ�ͧ��觵�ҧ� ����к���С�кǹ��÷ӧҹ', 3, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
						  UPDATE_USER, UPDATE_DATE)
						  VALUES ('310', '����������������Ǩ٧�', 'Communication & Influencing- CI', '������ҷ��Ż���С��ط���ҧ� 㹡��������� �è� ������������������蹴��Թ����� �����赹����˹��§ҹ���ʧ��', 3, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
						  UPDATE_USER, UPDATE_DATE)
						  VALUES ('311', '�����Դ���ҧ��ä�', 'Innovation- INV', '��������ö㹡�÷��й��ʹͷҧ���͡ (Option) �����Ƿҧ��ѭ�� (Solution) �������ҧ��ѵ���� ���� ����������ҧ��ä�Ԩ���������������� �����繻���ª����ͧ���', 3, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
						  UPDATE_USER, UPDATE_DATE)
						  VALUES ('312', '��ô��Թ����ԧ�ء', 'Proactiveness- PRO', '��õ��˹ѡ�����������͡�����ͻѭ���ػ��ä����Ҩ�Դ����͹Ҥ� ����ҧἹ ŧ��͡�зӡ����������������ª��ҡ�͡�� ���ͻ�ͧ�ѹ�ѭ�� ��ʹ����ԡ�ԡĵԵ�ҧ� ������͡��', 3, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
						  UPDATE_USER, UPDATE_DATE)
						  VALUES ('313', '��ä�����С�ú����èѴ��â����Ť������', 'Information Seeking and Management� ISM', '��������ö㹡���׺���� ��������������੾����Ш� ���䢻����ȹ��«ѡ����������´ ������������Ң��Ƿ���仨ҡ��Ҿ�Ǵ�����ͺ����¤Ҵ����Ҩ�բ����ŷ����繻���ª������͹Ҥ� ��йӢ����ŷ�����ҹ���һ�������ШѴ������ҧ���к� �س�ѡɳй���Ҩ����֧����ʹ�����������ǡѺʶҹ��ó� ������ѧ ����ѵԤ������� ����� �ѭ�� ��������ͧ��ǵ�ҧ� �������Ǣ�ͧ���ͨ��繵�ͧҹ�˹�ҷ��', 3, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
						  UPDATE_USER, UPDATE_DATE)
						  VALUES ('314', '����ҧἹ����ʹ���ͧ�Ѻͧ���', 'Planning with Organization Understanding - POU', '��������ö㹡���ҧἹ���ҧ����ѡ����������ö��任�Ժѵ����ԧ��ж١��ͧ ������¤������������ͧ෤����� �к� ��кǹ��÷ӧҹ ����ҵðҹ��÷ӧҹ�ͧ����Тͧ˹��§ҹ���� �������Ǣ�ͧ', 3, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
						  UPDATE_USER, UPDATE_DATE)
						  VALUES ('315', '�����䢻ѭ��Ẻ����Ҫվ', 'Professional Problem Solving � PPS', '��������ö���������ѭ�����������繻ѭ�� ��������ŧ��ͨѴ��áѺ�ѭ�ҹ��� ���ҧ�բ����� ����ѡ��� �������ö�Ӥ�������Ǫҭ �����ǤԴ�����ԪҪվ�һ���ء����㹡����䢻ѭ�������ҧ�ջ���Է���Ҿ', 3, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$code = array(	"101", "102", "103", "104", "105", "201", "202", "203", "204", "205", "206", "301", "302", "303", "304", "305", "306", 
										"307", "308", "309", "310", "311", "312", "313", "314", "315" );
		for ( $i=0; $i<count($code); $i++ ) { 
			$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
							  UPDATE_DATE)
							  VALUES ('$code[$i]', 0, '����ʴ����ö�д�ҹ������ҧ�Ѵਹ', NULL, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end for

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('101', 1, '�ʴ�����������㹡�÷ӧҹ����', '�������ӧҹ�˹�ҷ��������ж١��ͧ 
�դ����ҹ�ʹ�� ��ѹ��������㹡�÷ӧҹ ��еç�������
�դ����Ѻ�Դ�ͺ㹧ҹ ����ö�觧ҹ������˹�����
�ʴ��͡��ҵ�ͧ��÷ӧҹ�����բ�� �� ����֧�Ըա�� ���͢��й����ҧ��е������� ʹ�������
�ʴ����������ԧ��Ѻ��ا�Ѳ������������觷��������Դ����٭���� �������͹����Է���Ҿ㹧ҹ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('101', 2, '�ʴ����ö���дѺ��� 1 �������ö�ӧҹ��ŧҹ���������·���ҧ���', '��˹��ҵðҹ �����������㹡�÷ӧҹ���������ŧҹ����
���蹵Դ����ŧҹ ��л����Թ�ŧҹ�ͧ�� ����ࡳ�����˹���� �������١�ѧ�Ѻ �� �����Ҽŧҹ�������ѧ ���͵�ͧ��Ѻ��ا���è֧�дբ��
�ӧҹ�����ŧҹ���������·����ѧ�Ѻ�ѭ�ҡ�˹� ����������¢ͧ˹��§ҹ����Ѻ�Դ�ͺ
�դ��������´�ͺ�ͺ������� ��Ǩ��Ҥ����١��ͧ�ͧ�ҹ ���������ҹ����դس�Ҿ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('101', 3, '�ʴ����ö���дѺ��� 2 �������ö�ӧҹ��ŧҹ����ջ���Է���Ҿ�ҡ��觢��', '��Ѻ��ا�Ըա�÷������ӧҹ��բ�� ���Ǣ�� �դس�Ҿ�բ�� �����ջ���Է���Ҿ�ҡ���
�ʹ����ͷ��ͧ�Ըա�÷ӧҹẺ�������ջ���Է���Ҿ�ҡ������� ���������ŧҹ�������˹����', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('101', 4, '�ʴ����ö���дѺ��� 3 �������ö�Ѳ���Ըա�÷ӧҹ ���������ŧҹ���ⴴ�� ���ᵡ��ҧ���ҧ��������÷����ҡ�͹', '��˹�������·���ҷ�� ���������ҡ ���ͷ������ŧҹ���ա���������ҧ�����Ѵ
�ӡ�þѲ���к� ��鹵͹ �Ըա�÷ӧҹ ���������ŧҹ���ⴴ�� ���ᵡ��ҧ��������÷����ҡ�͹', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('101', 5, '�ʴ����ö���дѺ��� 4 �������ö�Ѵ�Թ��� �����դ�������§ �������ͧ��ú�����������', '�Ѵ�Թ��� ���ա�äӹǳ������������ҧ�Ѵਹ ��д��Թ��� ��������Ҥ�Ѱ��л�ЪҪ������ª���٧�ش
�����èѴ�����з�������� ��ʹ����Ѿ�ҡ� ������������ª���٧�ش�����áԨ�ͧ˹��§ҹ�������ҧἹ���', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('102', 1, '�ʴ���������㹡������ԡ��', '����ú�ԡ�÷�����Ե� ���Ҿ ���㨵�͹�Ѻ
����ԡ�ô����Ѹ�����������ѹ�� ������ҧ������зѺ������Ѻ��ԡ�� 
�����й� ��Ф�µԴ�������ͧ ����ͼ���Ѻ��ԡ���դӶ�� ������¡��ͧ�������ǡѺ��áԨ�ͧ˹��§ҹ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('102', 2, '�ʴ����ö���дѺ��� 1 �������ö����ԡ�÷�����Ѻ��ԡ�õ�ͧ�����', '�������� ������� �ͧ��ú�ԡ�÷��١��ͧ �Ѵਹ�����Ѻ��ԡ�����ʹ�������ԡ��
��������Ѻ��ԡ�÷�Һ�����׺˹��㹡�ô��Թ����ͧ ���͢�鹵͹�ҹ��ҧ� �������ԡ������
����ҹ�ҹ����˹��§ҹ ��СѺ˹��§ҹ�������Ǣ�ͧ ����������Ѻ��ԡ�����Ѻ��ԡ�÷�������ͧ����Ǵ����', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('102', 3, '�ʴ����ö���дѺ��� 2 ������㨪�����ѭ�����Ѻ����ԡ����', '�Ѻ�繸��� ������ѭ���������Ƿҧ��䢻ѭ�ҷ���Դ��������Ѻ��ԡ�����ҧ�Ǵ����  ���� ���������§ ������� ���ͻѴ����
��´���������Ѻ��ԡ�����Ѻ�����֧��� ��йӢ�͢Ѵ��ͧ�� 㹡������ԡ�� (�����) 仾Ѳ�ҡ������ԡ��������觢��', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('102', 4, '�ʴ����ö���дѺ��� 3 �������ԡ�÷���Թ�����Ҵ��ѧ��дѺ����� ����ͧ���������ͤ������������ҧ�ҡ', '������������Ѻ��ԡ�� ��੾������ͼ���Ѻ��ԡ�û��ʺ�����ҡ�Ӻҡ �� ���������Ф��������������㹡������ԡ�� ���ͪ��¼���Ѻ��ԡ����ѭ��
����������� ������� �������������Ǣ�ͧ�Ѻ�ҹ�����ѧ����ԡ������ ����繻���ª�������Ѻ��ԡ�� �����Ҽ���Ѻ��ԡ�è���������֧ ��������Һ�ҡ�͹
����ԡ�÷���Թ�����Ҵ��ѧ��дѺ�����', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('102', 5, '�ʴ����ö���дѺ��� 4 �������ö�����������ԡ�÷��ç���������ͧ��÷�����ԧ�ͧ����Ѻ��ԡ����', '���㨤����������ͤ�����ͧ��÷�����ԧ�ͧ����Ѻ��ԡ�� ���/���� ��������ǧ�Ң�������зӤ�����������ǡѺ�����������ͤ�����ͧ��÷�����ԧ�ͧ����Ѻ��ԡ��
�����йӷ���繻���ª�������Ѻ��ԡ�� ���͵ͺʹͧ�����������ͤ�����ͧ��÷�����ԧ�ͧ����Ѻ��ԡ��', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('102', 6, '�ʴ����ö���дѺ��� 5 �������ö����ԡ�÷���繻���ª�����ҧ���ԧ�������׹���Ѻ����Ѻ��ԡ��', '�����繼Ż���ª������Դ��鹡Ѻ����Ѻ��ԡ���������� �������ö����¹�ŧ�Ը����͢�鹵͹�������ԡ�� ����������Ѻ��ԡ�������ª���٧�ش
��ԺѵԵ��繷���֡�ҷ�����Ѻ��ԡ������ҧ�  ��ʹ������ǹ����㹡�õѴ�Թ㨢ͧ����Ѻ��ԡ��
����ö�����������ǹ��Ƿ���Ҩᵡ��ҧ仨ҡ�Ըա�� ���͢�鹵͹������Ѻ��ԡ�õ�ͧ��� ��������ʹ���ͧ�Ѻ�������� �ѭ�� �͡�� ��� �����繻���ª�����ҧ���ԧ�����������������Ѻ��ԡ��', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('103', 1, '�ʴ�����ʹ���еԴ��������������� ��Ң��Ҫվ�ͧ��/�������Ǣ�ͧ', '��е�������㹡���֡���Ҥ������ ʹ�෤��������ͧ������������� ��Ң��Ҫվ�ͧ��
���蹷��ͧ�Ըա�÷ӧҹẺ���� ���;Ѳ�һ���Է���Ҿ��Ф�������������ö�ͧ��������觢��
�Դ���෤�����ͧ������������� �������ʹ��¡���׺�鹢����Ũҡ���觵�ҧ� �����繻���ª���͡�û�Ժѵ��Ҫ���', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('103', 2, '�ʴ����ö���дѺ��� 1 ����դ��������Ԫҡ�� ���෤���������� ��Ң��Ҫվ�ͧ��', '�ͺ�����ҷѹ෤���������ͧ������������� ��Ң��Ҫվ�ͧ����з������Ǣ�ͧ �����Ҩ�ռš�з���͡�û�Ժѵ�˹�ҷ��ͧ�� 
�Դ���������Է�ҡ�÷��ѹ���� ���෤����շ������Ǣ�ͧ�Ѻ�ҹ���ҧ������ͧ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('103', 3, '�ʴ����ö���дѺ��� 2 �������ö�Ӥ������ �Է�ҡ�� ����෤���������� ������֡���һ�Ѻ��Ѻ��÷ӧҹ', '���㨻������ѡ� ����Ӥѭ ��мš�з��ͧ�Է�ҡ�õ�ҧ� ���ҧ�֡���
����ö���Ԫҡ�� ������� ����෤���������� �һ���ء����㹡�û�Ժѵԧҹ��
����������������� �������� ��������繻���ª�� �����Ӥѭ�ͧͧ�������� ෤���������� �����觼š�з���ͧҹ�ͧ���͹Ҥ�', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('103', 4, '�ʴ����ö���дѺ��� 3 ����֡�� �Ѳ�ҵ��ͧ����դ������ ��Ф�������Ǫҭ㹧ҹ�ҡ��鹷����ԧ�֡ ����ԧ���ҧ���ҧ������ͧ', '�դ�������������Ǫҭ�����ͧ�������ǡѺ�ҹ���´�ҹ (���Է�ҡ��) �������ö�Ӥ������任�Ѻ����黯Ժѵ������ҧ���ҧ��ҧ��ͺ����
����ö�Ӥ�������ԧ��óҡ�âͧ�����㹡�����ҧ����·�ȹ� ���͡�û�Ժѵԧҹ�͹Ҥ�
�ǹ�����Ҥ������������Ǣ�ͧ�Ѻ�ҹ����ԧ�֡����ԧ���ҧ���ҧ������ͧ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('103', 5, '�ʴ����ö���дѺ��� 4 ���ʹѺʹع��÷ӧҹ�ͧ���ͧ��÷���鹤�������Ǫҭ��Է�ҡ�ô�ҹ��ҧ�', 'ʹѺʹع����Դ����ҡ����觡�þѲ�Ҥ�������Ǫҭ�ͧ��� ���¡�èѴ��÷�Ѿ�ҡ� ����ͧ��� �ػ�ó�������͵�͡�þѲ��
�����ʹѺʹع ���� ������ռ���ʴ��͡�֧�������㨷��оѲ�Ҥ�������Ǫҭ㹧ҹ
������·�ȹ�㹡�������繻���ª��ͧ෤����� ͧ�������� �����Է�ҡ������� ��͡�û�Ժѵԧҹ�͹Ҥ� ���ʹѺʹع�����������ա�ù��һ���ء�����˹��§ҹ���ҧ������ͧ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('104', 1, '�դ��������ѵ���ب�Ե', '��Ժѵ�˹�ҷ����¤��������  �����ѵ���ب�Ե �١��ͧ��駵����ѡ������ ���¸����������º�Թ��
�ʴ������Դ��繢ͧ�������ѡ�ԪҪվ���ҧ�Դ�µç仵ç��', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('104', 2, '�ʴ����ö���дѺ��� 1 ������Ѩ�����Ͷ����', '�ѡ���Ҩ� ���Ѩ�����Ͷ���� �ٴ���ҧ�÷����ҧ��� ���Դ��͹��ҧ���¡�����鵹�ͧ
�ըԵ�ӹ֡��Ф����Ҥ�����㹤����繢���Ҫ��� �ط���ç����ç㨼�ѡ�ѹ�����áԨ��ѡ�ͧ�����˹��§ҹ����ؼ� ����ʹѺʹع���������þѲ�һ���Ȫҵ�����ѧ����', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('104', 3, '�ʴ����ö���дѺ��� 2 ����ִ������ѡ���', '�ִ������ѡ�����Ш���Һ�ó�ͧ�ԪҪվ ������§ູ����ͤ�����ͼŻ���ª����ǹ��
������Ф����آʺ�µ�ʹ�������֧�����ǹ�����ͧ͢��ͺ���� ����������áԨ�˹�ҷ�����ķ�������Ӥѭ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('104', 4, '�ʴ����ö���дѺ��� 3 ��и�ç�����١��ͧ', '��ç�����١��ͧ �׹��Ѵ�Էѡ��Ż���ª����Ъ������§�ͧ����Ȫҵ�����ʶҹ��ó����Ҩ���ҧ�����Ӻҡ����
�Ѵ�Թ��˹�ҷ�� ��Ժѵ��Ҫ��ô��¤����١��ͧ ����� �繸��� ���Ţͧ��û�Ժѵ��Ҩ���ҧ�ѵ�����͡�ͤ������֧����������������Ǣ�ͧ�������»���ª��', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('104', 5, '�ʴ����ö���дѺ��� 4 ����ط�ȵ����ͼ�ا�����صԸ���', '��ç�����١��ͧ �׹��Ѵ�Էѡ��Ż���ª����Ъ������§�ͧ����Ȫҵ�����ʶҹ��ó����Ҩ����§��ͤ�����蹤�㹵��˹�˹�ҷ���çҹ �����Ҩ����§��µ�ͪ��Ե', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('105', 1, '��˹�ҷ��ͧ��㹷����������', '�ӧҹ���ǹ��赹���Ѻ�ͺ����������� ʹѺʹع��õѴ�Թ�㹡���� 
��§ҹ�����Ҫԡ��Һ�����׺˹�Ңͧ��ô��Թ�ҹ㹡���� ���͢��������� ����繻���ª���͡�÷ӧҹ���ҧ������ͧ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('105', 2, '�ʴ����ö���дѺ��� 1 ����������������㹡�÷ӧҹ�Ѻ���͹�����ҹ', '���ҧ����ѹ��  ��ҡѺ������㹡�������
��������������� ������������͡Ѻ������㹷�����´�
����Ƕ֧���͹�����ҹ��ԧ���ҧ��ä�', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('105', 3, '�ʴ����ö���дѺ��� 2 ��л���ҹ����������ͧ͢��Ҫԡ㹷��', '�Ѻ�ѧ������繢ͧ��Ҫԡ㹷�� �������¹���ҡ������ ����֧�����ѧ�Ѻ�ѭ�� ��м�������ҹ
�����Ť����Դ��繵�ҧ� �����Сͺ��õѴ�Թ������ҧἹ�ҹ�����ѹ㹷��
����ҹ��������������ѹ��Ҿ�ѹ��㹷�� ����ʹѺʹع��÷ӧҹ�����ѹ����ջ���Է���Ҿ��觢��', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('105', 4, '�ʴ����ö���дѺ��� 3 ���ʹѺʹع��Ъ�������ͧҹ���͹������������� �������ҹ���ʺ���������', '����Ǫ�蹪������ѧ����͹�����ҹ�����ҧ��ԧ� 
�ʴ�������˵��ԡĵ� ��������������������͹�����ҹ������˵ب���������ͧ�����ͧ��
�ѡ���Ե��Ҿ�ѹ�աѺ���͹�����ҹ���ͪ�������͡ѹ����е�ҧ� ���ҹ���������ǧ�繻���ª������ǹ���', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('105', 5, '�ʴ����ö���дѺ��� 4 �������ö�ӷ����黯Ժѵ���áԨ�����������', '��������������Ѥ���繹��˹������ǡѹ㹷�� �����ӹ֧�����ͺ�������ͺ��ǹ�� 
���»���ҹ������� ���ͤ��������䢢�͢Ѵ��駷���Դ���㹷��
����ҹ����ѹ�� ���������ѭ���ѧ㨢ͧ������������ѧ�ѹ㹡�û�Ժѵ���áԨ�˭���µ�ҧ� ������ؼ�', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('201', 1, '�����á�û�Ъ�������Ф���駢�����ä��������������', '����ö���Թ��û�Ъ����� �¡�˹��������Ǣ��㹡�û�Ъ�� �ѵ�ػ��ʧ�� �Ǻ������� ���ᨡᨧ˹�ҷ���Ѻ�Դ�ͺ�����ؤ��㹡������
�����駢�����ä�������������˵ؼ������ѧ�Ѻ�ѭ���Ѻ��Һ����������������繵�ͧ��з� ��������դ������㨵ç�ѹ�������û�Ժѵԧҹ㹷�ȷҧ���ǡѹ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('201', 2, '�ʴ����ö���дѺ��� 1 ����繼���㹡�÷ӧҹ�ͧ�����', '��˹�������� ��ȷҧ���Ѵਹ ���ç���ҧ���������� ���͡���������СѺ�ҹ �������Ըա������ ���ͪ������ҧ����з��з���������ӧҹ��բ��
����ǤӪ��� ��������ͤԴ��繵Ԫ�������ҧ��ä� �������������������ӧҹ���ҧ�ջ���Է���Ҿ
ŧ��͡�зӡ���繵�����ҧ���ͪ������������Ժѵ�˹�ҷ�������ҧ�������Է���Ҿ
���͡���������СѺ�ҹ ��С�˹����Ѿ����Ѵਹ����Чҹ����ͺ���� ���ͪ������ҧ�������������ӧҹ��բ�������ջ���Է���Ҿ���
���ҧ��ѭ���ѧ�㹡�û�Ժѵԧҹ ��������͡�ʼ����ѧ�Ѻ�ѭ��㹡���ʴ��ѡ��Ҿ��÷ӧҹ���ҧ������ �������������Է���Ҿ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('201', 3, '�ʴ����ö���дѺ��� 2 �������ô�����Ъ�������ͼ����ѧ�Ѻ�ѭ��', '�Ѻ�ѧ����繻ѭ�� ����Ѻ�繷���֡��㹡�ô��ż����ѧ�Ѻ�ѭ���������ö��Ժѵԧҹ���¤����آ����ջ���Է���Ҿ�٧�ش
�Ѵ�Һؤ�ҡ� ��Ѿ�ҡ� ���͢����ŷ���Ӥѭ�����㹡�û�Ժѵԧҹ������ص������������������ʹѺʹع������������ѧ�Ѻ�ѭ��
���� ����ͧ��Ъ���������������ѧ�Ѻ�ѭ�����㨶֧��û�Ѻ����¹����Դ�������ͧ�����Ф������繢ͧ��û�Ѻ����¹����', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('201', 4, '�ʴ����ö���дѺ��� 3 ��л�оĵԵ����Ѻ�繼���', '��˹�����������ԺѵԻ�Шӡ������л�оĵԵ���Ẻ���ҧ����������ѧ�Ѻ�ѭ��
�ִ��ѡ������Ժ��  (Good Governance) (�ԵԸ��� �س���� ����� ��������ǹ���� �����Ѻ�Դ�ͺ �����������) 㹡�û���ͧ�����ѧ�Ѻ�ѭ��
ʹѺʹع�������ǹ�����ͧ�����ѧ�Ѻ�ѭ��㹡���ط�ȵ����Ѻ��û�Ժѵԧҹ����ʹͧ��º�»������к������áԨ�Ҥ�Ѱ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('201', 5, '�ʴ����ö���дѺ��� 4 ��йӼ����ѧ�Ѻ�ѭ������������ѹ��Ԩ������Ǣͧͧ���', '����ö���㨤�������ҧ�ç�ѹ�����������ѧ�Ѻ�ѭ������Դ�����������������㹡�û�Ժѵԧҹ�����áԨ���������ǧ�����ҧ������
�繼���㹡�����ҧ��ä��������� ���ͧ��� ��м�ѡ�ѹ���ͧ��á�������������¹�ŧ����Һ�����л��ʺ��������稴��¡��ط������Ըմ��Թ��÷���������
���ҧ���������·�ȹ�㹡�á�˹��ش������з�ȷҧ����Ѻ��餹������� ��੾�����ҧ�������ǡ�ó�����ѧ༪ԭ�������¹�ŧ
�����繡������¹�ŧ�͹Ҥ� ��������������ҧ���ط�����Ѻͧ���㹡���Ѻ��͡Ѻ�������¹�ŧ����', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('202', 1, '������§�ҹ��ҡѺ��Ժ��ͧ�Ҥ�Ѱ�����', '������������������������觷����������ռ����ҧ�õ�ͻ�ЪҪ� �Ҹ�ó�� ����˹��§ҹ���ҧ�� 
����ö��������Ҿ������������·��Ѵਹ�ͧ˹��§ҹ���ͧ��� ��������������������Һ��ҷ�ͧ������Ǣ�ͧ�Ѻ��Ժ���������ҧ��
������§����·�ȹ�ͧ˹��§ҹ�Ѻ������� �ѵ�ػ��ʧ����С��ط��ͧ�Ҥ�Ѱ�������', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('202', 2, '�ʴ����ö���дѺ��� 1 ��з��������·�ȹ����Ѻ�������Ѻ', '�觻ѹ�����Ѻ�Դ�ͺ㹡�á�˹�Ἱ��ô��Թ������Ƿ���ʹ���ͧ�Ѻ����·�ȹ���������������ǹ���������ʴ������Դ����������ҧ����Դ�������Ѻ��й�����ԧ
���ҧ����������Ͷ�����������·�ȹ��¡����������ǧ���ҧ�˹��§ҹ��軯Ժѵ�˹�ҷ������
�觻ѹ��������������������¹͡˹��§ҹ ��ʹ�������Ң���������ҹ�鹨й����繾�鹰ҹ㹡�á�˹����ط��ͧ˹��§ҹ�����ҧ��', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('202', 3, '�ʴ����ö���дѺ��� 2 ��������������·�ȹ�', '���·ʹ����·�ȹ�ͧ˹��§ҹ�������Ѻ�Դ�ͺ��������Ըշ�����ҧ�ç�ѹ���� ������е������� ��Ф��������ç����������������·�ȹ���
������·�ȹ���㹡�á�˹��ش������з�ȷҧ����Ѻ��餹������� ��੾�����ҧ�������ǡ�ó�����ѧ༪ԭ�������¹�ŧ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('202', 4, '�ʴ����ö���дѺ��� 3 ���������·�ȹ��Ҫ��¡�˹���º��㹧ҹ', '�Դ�͡��ͺ ���ʹͤ����Դ�����������˹���º��㹧ҹ���ͻ���ª�������͡�ʢͧ�Ҥ�Ѱ�����Ҹ�ó����������ҧ�������ռ��㴤Դ�ҡ�͹', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('202', 5, '�ʴ����ö���дѺ��� 4 ��Фӹ֧�֧����·�ȹ��дѺ�š', '��˹����������з�ȷҧ ���ͧҹ��Ҥ�Ѱ���¤����������ҧ�������������������ҹ���ʹ���ͧ�Ѻ��Ժ��ͧ�������㹻�ЪҤ��š���ҧ��
�Ҵ��ó������ʶҹ��ó�㹻�����Ҩ���Ѻ�š�з����ҧ�èҡ�������¹�ŧ�ء��ҹ������������¹͡����� ����ʹ͡��ط������������Ȫҵ����Ѻ����ª���٧�ش�ҡ�������¹�ŧ���', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('203', 1, '���㨡��ط���Ҥ�Ѱ', '������áԨ ��º�� ���ط���Ҥ�Ѱ���ͧ��÷���ѧ�Ѵ �ա�����������դ���������§�Ѻ��áԨ�ͧ˹��§ҹ��赹�����Ѻ�Դ�ͺ���������ҧ��
����ö��������ѭ�� �ػ��ä�����͡�ʢͧ˹��§ҹ��㹡�ú���ؼ����ķ�����', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('203', 2, '�ʴ����ö���дѺ��� 1 ��л���ء����ʺ��ó�㹡�á�˹����ط���û�Ժѵԧҹ�ͧ˹��§ҹ', '����ء����ʺ��ó���к����¹�ʹյ�����˹����ط��ͧ˹��§ҹ����ʹ���ͧ�Ѻ���ط���Ҥ�Ѱ �������ö�������áԨ����˹����
�����������������к��Ҫ��������������Ѻ���ط�� �����ط��Ը��ԧ�ء㹡�û�Ժѵԧҹ�ͧ˹��§ҹ�����������Ѻʶҹ��ó����㹷���Դ�����', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('203', 3, '�ʴ����ö���дѺ��� 2 ��л���ء���ɮ������ǤԴ�Ѻ��͹㹡�á�˹����ط���û�Ժѵԧҹ�͹Ҥ�', '����ء���ɮ� �����ǤԴ�Ѻ��͹����հҹ�Ҩҡͧ�����������͢������ԧ��Шѡ�� 㹡�äԴ��оѲ������������͡��ط��㹡�û�Ժѵԧҹ�ͧͧ�������˹��§ҹ��赹�����Ѻ�Դ�ͺ����
����ء�� Best Practice ���ͼš���Ԩ�µ�ҧ� �ҡ�˹��ç�������Ἱ�ҹ�ԧ���ط��������ķ����ջ���ª��������ǵ��ͧ��������˹��§ҹ��赹�����Ѻ�Դ�ͺ����', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('203', 4, '�ʴ����ö���дѺ��� 3 ���������§ʶҹ��ó�㹻�������͡�˹����ط��㹡�û�Ժѵԧҹ���㹻Ѩ�غѹ����͹Ҥ�', '�����Թ����ѧ������ʶҹ��ó� ����� ���ͻѭ�ҷҧ���ɰ�Ԩ �ѧ�� ������ͧ���㹻�������ͧ͢�š���Ѻ��͹���¡�ͺ�ǤԴ����ԸվԨ�ó�Ẻ�ͧ�Ҿͧ����� ������㹡�á�˹����ط���Ҥ�Ѱ����˹��§ҹ��赹�����Ѻ�Դ�ͺ�������ͺʹͧ�������¹�ŧ�ѧ����������ҧ�ջ���Է���Ҿ�٧�ش
�Ҵ��ó�ʶҹ��ó��͹Ҥ���С�˹����ط�� Ἱ���͹�º���ԧ�ط���ʵ�����͵ͺʹͧ�͡�����ͻ���繻ѭ�ҷ���Դ��鹨ҡʶҹ��ó����㹻�������͵�ҧ����ȷ������¹�ŧ��������ͧ��ú���ص���ѹ��Ԩ������Ѻ�Դ�ͺ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('203', 5, '�ʴ����ö���дѺ��� 4 ��к�óҡ��ͧ����������������㹧ҹ���ط���Ҥ�Ѱ', '��ä����ҧ��к�óҡ��ͧ����������������㹧ҹ���ط���Ҥ�Ѱ �¾Ԩ�óҨҡ��Ժ����������Ҿ�����л�Ѻ���������� ��Ժѵ����ԧ
�Դ��л�Ѻ����¹��ȷҧ�ͧ���ط���þѲ�һ������Ҿ��� ����繡��ط����������¼�ѡ�ѹ����Դ��þѲ�����ҧ������ͧ�������׹�����', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('204', 1, '��繤������繢ͧ��û�Ѻ����¹', '��繤������繢ͧ��û�Ѻ����¹ ��л�Ѻ�ĵԡ�������Ἱ��÷ӧҹ����ʹ���ͧ�Ѻ��û�Ѻ����¹����Դ�������ͧ���
�����������Ѻ�֧�������� ��ȷҧ��Тͺࢵ�ͧ��û�Ѻ����¹ /����¹�ŧ ��е���㹡�����¹��������������ö��Ѻ�������ʹ���ͧ�Ѻ��û�Ѻ����¹/����¹�ŧ�����', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('204', 2, '�ʴ����ö���дѺ��� 1 ���ʹѺʹع�����������㨡�û�Ѻ����¹�����Դ���', '��������������������㨶֧��û�Ѻ����¹����Դ�������ͧ��� ����������л���ª��ͧ��û�Ѻ����¹����
ʹѺʹع����������㹡�û�Ѻ����¹ͧ��� ������������ǹ�����Ӥѭ㹡���ʹ����Ըա�÷��Ъ�������û�Ѻ����¹���Թ����ҧ�ջ���Է���Ҿ�ҡ���', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('204', 3, '�ʴ����ö���дѺ��� 2 ��С�е�� ������ҧ�ç�٧�����������繤����Ӥѭ�ͧ��û�Ѻ����¹����Դ���', '��е�� ������ҧ�ç�٧�����������繤����Ӥѭ�ͧ��û�Ѻ����¹����Դ�����������Դ��������ç���������Դ�������¹�ŧ��鹢�鹨�ԧ
����� ������ҧ�����Ѵਹ�¡��͸Ժ�����˵� �������� ����ª�� ��� �ͧ��û�Ѻ����¹����Դ�����������
���º��º�����������觷���è��������觷���оĵԻ�Ժѵԡѹ������ᵡ��ҧ�ѹ���ҧ��', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('204', 4, '�ʴ����ö���дѺ��� 3 ����ҧἹ�ҹ��������Ѻ��û�Ѻ����¹�ͧ��è��������繪Ѵਹ��ҡ������¹�ŧ����繻���ª����ͧ������ҧ��', '�ҧἹ���ҧ���к���Ъ�������繼����ķ���ҡ��û�Ѻ����¹ͧ��÷����ѧ�д��Թ��� 
�����Ἱ��ú����á������¹�ŧ��еԴ���Ἱ�ҹ���ҧ���������������ͧ�������ö�Ѻ��͡Ѻ�������¹�ŧ���� �����ҧ�ջ���Է���Ҿ�٧�ش
���ҧ�ç�٧������ʹѺʹع������ҧ�������Ѻ�ҡ����ҷ���������ɢͧ��ù���������繶֧����ª��ͧ�������¹�ŧ�ҡ���ǡ�ó�Ѩ�غѹ�����ҡ����ǹ����㹡������¹�ŧ���', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('204', 5, '�ʴ����ö���дѺ��� 4 ��д��Թ��õ��Ἱ����Դ��û�Ѻ����¹���ҧ�ջ���Է���Ҿ����������', '�繼���㹡�û�Ѻ����¹�ͧͧ��� ��м�ѡ�ѹ���ҧ��ԧ�ѧ����û�Ѻ����¹���Թ������ҧ�Һ�����л��ʺ����������٧�ش
��ء��ѭ���ѧ� ������ҧ��ѷ�Ҥ���������� 㹡�âѺ����͹����Դ��û�Ѻ����¹/����¹�ŧ���ҧ�ջ���Է���Ҿ�٧�ش��ͧ��������Ѱ�����', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('205', 1, '����ʴ��ĵԡ����ѹ����������', '����ʴ��ĵԡ���������Ҿ�������������� ��������֡��Ҷ١��е�鹷ҧ������ ������ö�ЧѺ��á�зӹ�������
ʹ��������ʴ��ĵԡ����ع�ѹ��ѹ���', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('205', 2, '�ʴ����ö���дѺ��� 1 �����ա����§�������§ູʶҹ��ó��������Դ�����ع�ç�ҧ������', '�Ҩ����§�͡仨ҡʶҹ��ó� (��������Դ�����ع�ç�ҧ������) ���Ǥ����ҡ��з��� �����Ҩ����¹��Ǣ��ʹ��� ������ش�ѡ���Ǥ�������ʧ�ʵ�������', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('205', 3, '�ʴ����ö���дѺ��� 2 ����վĵԡ����ͺ�������ҧʧ��������ҧ��ä� ���ж١�����بҡ���µç����', '����֡�֧�����ع�ç�ҧ������������ҧ���ʹ��� ���͡�û�Ժѵԧҹ �� �����ø �����Դ��ѧ ���ͤ������ѹ ��������ʴ��͡�� �����ͺ�ع�ç���ж١�����بҡ���µç���� ����ѧ����ͧʵԻ�ԺѵԵ����������ҧʧ�
����֡�֧�����ع�ç�ҧ������������ҧ���ʹ��� ���͡�û�Ժѵԧҹ ������ö���͡�Ըա���ʴ��͡㹷ҧ���ҧ��ä��������ʶҹ��ó����բ��
����������������˵����������ͧ�����ҧ�֡��� ������������������Ը��ʴ��͡��������������������������ͧ���ռ���ԧź��駵�͵��ͧ��м�����', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('205', 4, '�ʴ����ö���дѺ��� 3 ��ШѴ��ä������´���ҧ�ջ���Է���Ҿ', '����ö��Ժѵԧҹ���͵ͺʹͧ���ҧ���ҧ��ä������Ф������ѹ������ͧ
����ö�Ѵ��áѺ�������´���ͼš�з�����Ҩ���Դ��鹨ҡ�����ع�ç�ҧ�����������ҧ�ջ���Է���Ҿ
�Ҩ����ء�����Ըա��੾�е� �����ҧἹ��ǧ˹�����ͨѴ��áѺ��������Ф������´����Ҩ���Դ���
�ͧ�š���ҧ������к����èѴ���������ͧ�������ҧ�ջ���Է���Ҿ����Ŵ�������´��駵�͵��ͧ��м�������ҹ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('205', 5, '�ʴ����ö���дѺ��� 4 �����Ҫ����������¤�������', '���ҧ�������ع�ç��駻ǧ �¡�þ������Ӥ������㨵��˵� ���㨵��ͧ ����ʶҹ��ó� ������㨤��ó� ��ʹ����Ժ���лѨ����Ǵ������ҧ� �Ҩ����������ͻ�����ҧ������ó�
����ͧ�����ѧ�Ѻ�ѭ�Ҵ��¤�������ҡ�س�����繸��� �����������ͧ������������Ф�����ҹ�ѹ��������١��ͧ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('206', 1, '�͹�ҹ��������й�����ǡѺ�Ըջ�Ժѵԧҹ', '�����й����ҧ�����´ ���/�����ҸԵ�Ըջ�Ժѵԧҹ �����繵�����ҧ
�͹�ҹ ���������йӷ��੾����Ш�����ǡѺ��þѲ�ҧҹ���͡�û�ԺѵԵ�
��������觢����� ��з�Ѿ�ҡ����� ������㹡�þѲ�Ңͧ������', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('206', 2, '�ʴ����ö���дѺ��� 1 ��е��㨾Ѳ���������ѧ�Ѻ�ѭ�����ѡ��Ҿ', '���㨢�ʹ���Т�ʹ��¢ͧ�����ѧ�Ѻ�ѭ���������ö���ӻ�֡�Ҫ�����Ƿҧ㹡�þѲ�����������ʹ����ⴴ�����ͻ�Ѻ��ا��ʹ������Ŵŧ
����͡�ʼ����ѧ�Ѻ�ѭ�����ʴ��͡�֧�ѡ��Ҿ��ҹ�բͧ�������������������㹡�û�Ժѵ�˹�ҷ��', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('206', 3, '�ʴ����ö���дѺ��� 2 ����ҧἹ�������͡�ʼ����ѧ�Ѻ�ѭ���ʴ���������ö㹡�÷ӧҹ���ҧ���к�', '�ҧἹ���ҧ���к�㹡�þѲ�Һؤ�ҡ� �� �ҧἹ��þѲ����ºؤ���������� �ҧἹ��ع���¹�ҹ  �繵�
�ҧἹ����ͺ���§ҹ��������������ջ���ª�� ����֧���͡��㹡�ý֡ͺ�� �Ѳ�����ͻ��ʺ��ó����� ���ҧ������������ʹѺʹع������¹�����С�þѲ�Ңͧ������
��������������§��ҧ ������������蹵Ѵ�Թ�㹺ҧ����ͧ㹧ҹ��Ш� ��駹�����ͼ��������͡��㹡�þѲ����к����èѴ��çҹ���µ��ͧ 
�Դ�͡��������Ѻѧ�Ѻ�ѭ��������������������µ��ͧ�¡���ͺ�����ӹҨ�Ѵ�Թ�㹺ҧ����ͧ���������áӡѺ����', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('206', 4, '�ʴ����ö���дѺ��� 3 ��Ъ��¢�Ѵ��ͨӡѴ�ͧ�����ѧ�Ѻ�ѭ�����;Ѳ���ѡ��Ҿ', '���ҷ������ӻ�֡�ҷ���з�͹�ŧҹ����ѡ��Ҿ������ԧ�ͧ������ �������ö�кؤ����������ͤ�����ͧ���㹡�ý֡ͺ�����;Ѳ�� ����繻���ª���ͧҹ ��Т�Ѵ��ͨӡѴ�ͧ�ؤ�ż����
����ö��Ѻ����¹��ȹ��� �ؤ�ԡ�Ҿ �����ٻẺ�������Ե�������繻Ѩ��¢Ѵ��ҧ��þѲ���ѡ��Ҿ�ͧ�����ѧ�Ѻ�ѭ�� 
�ըԵ�Է��㹡����Ҷ֧�Ե�����˵ؼ����ͧ��ѧ�ĵԡ����ͧ���кؤ�� ���͹���ʹѺʹع㹡�������ҧ�������� ��Ф�ҹ����ԧź��оѲ���ѡ��Ҿ㹡�÷ӧҹ���բ��', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('206', 5, '�ʴ����ö���дѺ��� 4 ������ҧ�Ѳ�������÷ӧҹ�������ӹҨ����͹�ҹ', 'ʹѺʹع��������Ѳ�������÷ӧҹ�������ӹҨ����ա���͹�ҹ�ѹ�ͧ ���;Ѳ�ҡ�������ѹ�ͧ�ؤ�ҡ�����ͧ��� �¡�����ҧ����ҡ�ȡ�÷ӧҹ�������͵���Ѳ������ѧ����� ��ʹ���Ѵ�ҷ�Ѿ�ҡ���С��ʹѺʹع��ҧ� ��������騹�Դ��þѲ��������¹������ҧ���к��ͧ���
��ѡ�ѹ������ҧ���������Դ�Ѳ�������觡�����¹��� ����֧���Թ������ҧ���ٻ���� ����óç�� ������� ��ѡ�ѹ Ἱ��þѲ�ҷ�Ѿ�ҡúؤ���˹��§ҹ�ͧ�����ҧ���к� �µ��˹ѡ�֧�����Ӥѭ�ͧ��ѧ�ѡ��Ҿ�ͧ��㹡�þѲ���ѧ����л���Ȫҵ�', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('301', 1, '���˹ѡ ��繤����Ӥѭ ��л���ª��ͧ��áӡѺ�Դ�����ô��Թ�ҹ��ҧ� �ͧ������', '���˹ѡ ��繤����Ӥѭ �������� ��л���ª��ͧ��áӡѺ�Դ�����ô��Թ�ҹ��ҧ� �ͧ�����蹷������Ǣ�ͧ㹧ҹ �����������蹻�ԺѵԵ���ҵðҹ ������º ���͢�ͺѧ�Ѻ����˹���� �����������Դ����ª��㹡�ô��Թ�ҹ�ͧ���ͧ ˹��§ҹ ����ͧ���', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('301', 2, '�ʴ����ö���дѺ��� 1 ��С�е�������㹡�áӡѺ�Դ�����ô��Թ�ҹ��ҧ� �ͧ������', '�ʴ��ĵԡ�����е�������㹡�áӡѺ�Դ�����ô��Թ�ҹ�ͧ�����蹷������Ǣ�ͧ㹧ҹ �����������蹻�ԺѵԵ���ҵðҹ ������º ���͢�ͺѧ�Ѻ����˹��������Դ����ª��㹡�ô��Թ�ҹ�ͧ���ͧ ˹��§ҹ ����ͧ��� �������ö�кؤ������ ���ͤ�������˹��㹡�ô��Թ�ҹ��ҧ� �ͧ��������', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('301', 3, '�ʴ����ö���дѺ��� 2 ��СӡѺ�Դ�����ô��Թ�ҹ��ҧ� �ͧ���������ҧ��������', '���Թ��áӡѺ�Դ�����ô��Թ�ҹ��ҧ� �ͧ���������ҧ�������� ��������� �������ö�������� ����кآ����� ����稨�ԧ ���˵� ��觼Դ���� ��Ф�������¹�ŧ��ҧ� ����Դ��������ҧ�١��ͧ ���͹������ô��Թ��õ�ҧ� �����ҧ�١��ͧ ������� ����ʹ���ͧ�Ѻ�ҵðҹ ������º ���͢�ͺѧ�Ѻ����˹����
��Ѻʶҹ��ó� ��кǹ��� �����Ըա�õ�ҧ� ���ͨӡѴ�ҧ���͡�ͧ������ �������ͺպ����������蹻�Ժѵ�㹡�ͺ���١��ͧ�����������������º��ԺѵԷ���˹����', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('301', 4, '�ʴ����ö���дѺ��� 3 ��СӡѺ�Դ��� ��е�Ǩ�ͺ�����١��ͧ�ͧ��ô��Թ�ҹ��ҧ� �ͧ���������ҧ���Դ', '���Ǩ �ӡѺ �Դ��� ��е�Ǩ�ͺ��ô��Թ�ҹ��ҧ� �ͧ���������ҧ���Դ�����ԧ�֡ �������������� ������ �Ԩ�� �����ػ�š�ô��Թ��� ��õͺʹͧ ��С������ԡ�õ�ҧ� ���١��ͧ ������� ����ʹ���ͧ�Ѻ�ҵðҹ ������º ���͢�ͺѧ�Ѻ����˹����
���蹤Ǻ��� ��Ǩ��� ��е�Ǩ�ͺ�����١��ͧ�ͧ��ô��Թ�ҹ��ҧ� 㹷ء��鹵͹���ҧ�����´�ͧ�����蹷������Ǣ�ͧ㹧ҹ�����仵���ҵðҹ ������º ���͢�ͺѧ�Ѻ����˹����
�͡����͹ (�ªѴ����Ҩ��Դ���â���ҡ��������軯ԺѵԵ���ҵðҹ����˹�������͡�зӡ������Դ������) �����觡������Ѻ��ا��ô��Թ�ҹ��ҧ� ��ԧ����ҳ���ͤس�Ҿ���١��ͧ����ҵðҹ ������º ���͢�ͺѧ�Ѻ����˹����', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('301', 5, '�ʴ����ö���дѺ��� 4 ��ШѴ��áѺ��ô��Թ�ҹ��ҧ� ������� ���١��ͧ ������觼Դ���������ҧ�索Ҵ�ç仵ç��', '���Թ������ҧ�ç仵ç�� �������Ը�༪ԭ˹�����ҧ�索Ҵ����ͼ���������˹��§ҹ������áӡѺ�����ա�ô��Թ�ҹ��ҧ� ������� ���١��ͧ ���ͷӼԴ���������ҧ�����ç 
��˹� ���ͻ�Ѻ�ҵðҹ ��ͺѧ�Ѻ ���͡�����º��ҧ� �������Ǣ�ͧ���ᵡ��ҧ ��ҷ�� �����٧��� (����������Ǵ��������¹�) ��������������ؤ�ҡ��Դ��þѲ�Ҥ�������ö����٧���', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('302', 1, '��з���觵�ҧ� ����ҵðҹ ���͵��������º��ͺѧ�Ѻ����˹����', '��Ժѵ�˹�ҷ����¤��������  �١��ͧ��駵����ѡ����������º��ͺѧ�Ѻ����˹����
�ִ�����ѡ�������Ƿҧ�����ѡ�ԪҪվ���ҧ��������
�Դ�¢����������˵ؼ����ҧ�ç仵ç��', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('302', 2, '�ʴ����ö���дѺ��� 1 ����ִ�����Ƿҧ���͢ͺࢵ��ͨӡѴ㹡�á�з���觵�ҧ�', '����ʸ������¡��ͧ�ͧ����������˹��§ҹ�������Ǣ�ͧ ���Ҵ�˵ؼ����ͼԴ������º�����Ƿҧ��º�·���ҧ���
���Թ������ҧ���Դ��͹ �������ҧ���¡�����鵹�ͧ���ͼ����ѧ�Ѻ�ѭ�����ͤ����ѡ����˹��§ҹ������ô����ҡ�ա�ô��Թ�ҹ�������Ѻ�����', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('302', 3, '�ʴ����ö���дѺ��� 2 ��еԴ����Ǻ�����黯ԺѵԵ���ҵðҹ���͵�������¢�ͺѧ�Ѻ', '���蹤Ǻ�����Ǩ��ҡ�ô��Թ��âͧ˹��§ҹ�������Ѻ�Դ�ͺ���������ҧ�١��ͧ���������º�����Ƿҧ��º�·���ҧ���
�͡����͹���;�������йջ�й�����ҧ�Ѵ����Ҩ��Դ���â���ҡ�ŧҹ������ҵðҹ����˹�������͡�зӡ������Դ������º�����Ƿҧ��º�·���ҧ���', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('302', 4, '�ʴ����ö���дѺ��� 3 ����Ѻ�Դ�ͺ���觷������㹡�ô���', '���ҵѴ�Թ��˹�ҷ�� ����� ����ͧ���ͻ�йջ�й�����ؤ������˹��§ҹ����ҽ׹��ࡳ�� ����º ��º�������ҵðҹ��������任�Ѻ��ا�ŧҹ��ԧ����ҳ���ͤس�Ҿ������ࡳ���ҵðҹ  �����ҼŢͧ��õѴ�Թ��Ҩ���ҧ�ѵ�����͡�ͤ������֧����������������Ǣ�ͧ�������»���ª��
��������Ѻ�����Դ��Ҵ��ШѴ��ä��������Դ��Ҵ���Ѵ��ŧ�', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('302', 5, '�ʴ����ö���дѺ��� 4 ��ШѴ��áѺ�ŧҹ����������觼Դ������º���ҧ�索Ҵ�ç仵ç��', '���Ը�༪ԭ˹�����ҧ�Դ�µç仵ç������ͼ���������˹��§ҹ������áӡѺ���� �ջѭ�Ҽŧҹ�������ͷӼԴ������º���ҧ�����ç
�׹��Ѵ�Էѡ��Ż���ª������ࡳ��ͧͧ��� ����ʶҹ��ó����Ҩ����§��ͤ�����蹤�㹵��˹�˹�ҷ���çҹ �����Ҩ����§��µ�ͪ��Ե', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('303', 1, '����Ἱ��й�º�¢ͧͧ�������˹��§ҹ��ҧ� �ͧ���', '���㨹�º�� ���ط��ͧ˹��§ҹ ����ͧ��� �������ö�Ӥ������㨹������������ѭ�� �ػ��ä �͡�ʢͧ˹��§ҹ ����ͧ����͡�繻��������� ��', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('303', 2, '�ʴ����ö���дѺ��� 1 ��л���ء��������� �ٻẺ���ͻ��ʺ��ó��������ʹ������Ƿҧ��ҧ� 㹧ҹ', '����ö�кػѭ���ʶҹ��ó�Ѩ�غѹ����Ҩ�դ�������¤�֧ ���͵�ҧ�ҡ���ʺ��ó����»��ʺ�����˹�����ʹ������Ƿҧ (Implication) �ԧ���ط����ʹѺʹع���ͧ�������˹��§ҹ��ҧ� �������Ǣ�ͧ�������áԨ����˹����������黯Ժѵԡ������������Ѻʶҹ��ó����Դ�����', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('303', 3, '�ʴ����ö���дѺ��� 2 ��л���ء���ɮ������ǤԴ�Ѻ��͹㹡�þԨҳ�ʶҹ��ó� ���͡�˹�Ἱ�ҹ���͢���ʹ͵�ҧ�', '����ء���ɮ� �����ǤԴ�Ѻ��͹����հҹ�Ҩҡͧ�����������͢������ԧ��Шѡ�� 㹡�þԨ�ó�ʶҹ��ó� �¡��Т�ʹբ�����¢ͧ����繵�ҧ� 㹡�û�Ժѵԧҹ�ͧͧ�������˹��§ҹ��赹�����Ѻ�Դ�ͺ����
����ö���ǤԴ��ҧ� ������¹�����������§͸Ժ���˵ؼŤ������� �¡��Т�ʹ� ��Т�����¢ͧ�ѭ�� ʶҹ��ó� ��� �繻���繵�ҧ� �����ҧ���˵��ռ�
����ء�� Best Practice ���ͼš���Ԩ�µ�ҧ� �ҡ�˹��ç�������Ἱ�ҹ�������ķ����ջ���ª����ͧ������ͧҹ��赹�����Ѻ�Դ�ͺ����', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('303', 4, '�ʴ����ö���дѺ��� 3 ���������§ʶҹ��ó�㹻������е�ҧ��������͡�˹�Ἱ�����ҧ���ػ�����', '�����Թ����ѧ������ʶҹ��ó� ����� ���ͻѭ�ҷҧ���ɰ�Ԩ �ѧ�� ������ͧ���㹻������е�ҧ����ȷ��Ѻ��͹���¡�ͺ�ǤԴ����ԸվԨ�ó�Ẻ�ͧ�Ҿͧ����� ������㹡�á�˹�Ἱ���͹�º�¢ͧͧ�������˹��§ҹ��赹�����Ѻ�Դ�ͺ����
�к�������������˵��繼���ѹ�ʶҹ��ó�˹��� ��дѺ˹��§ҹ/ͧ���/����� �����¡��Т�ʹբ�����¢ͧ����繵�ҧ� ����֧͸Ժ�ª��ᨧʶҹ��ó���Ѻ��͹�ѧ������������ö�繷��������', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('303', 5, '�ʴ����ö���дѺ��� 4 ������ҧ��ä���к�óҡ��ͧ����������������㹧ҹ���ط��', '��ä����ҧ��к�óҡ��ͧ����������������㹧ҹ���ط�� �¾Ԩ�óҨҡ��Ժ������������к��ص��ˡ�����Ҿ�����л�Ѻ���������� ��Ժѵ����ԧ
��������ѭ������������֡��駶֧��Ѫ���ǤԴ���ͧ��ѧ�ͧ��������ͷҧ���͡��ҧ� ���Ѻ��͹ �ѹ�������û�д�ɰ�Դ�� ������ҧ��ä���й��ʹ��ٻẺ �Ը� ��ʹ��ͧ������������������»�ҡ��ҡ�͹����繻���ª����ͧ��� �����ѧ����л���Ȫҵ������', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('304', 1, '��ͧ��÷ӧҹ���١��ͧ��ЪѴਹ', '���㨷ӧҹ���١��ͧ ���Ҵ���º����
�����´����ǹ㹡�û�ԺѵԵ����鹵͹��û�Ժѵԧҹ �� ����º����ҧ���
�ʴ��ػ������ѡ����������º���º���·��㹧ҹ����������Ǵ�����ͺ��� �ҷ� �Ѵ����º��зӧҹ ��к���ǳ�ӹѡ�ҹ��赹��Ժѵ�˹�ҷ������ ������������������Թ�Ԩ�������ͤ���������º�ͧʶҹ���ӧҹ �ҷ� �Ԩ���� 5 �. ���¤�����Ѥ�� ��е������� ���', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('304', 2, '�ʴ����ö���дѺ��� 1 ��е�Ǩ�ҹ�����١��ͧ�ͧ�ҹ��赹�Ѻ�Դ�ͺ', '��Ǩ�ҹ�����١��ͧ�ͧ�ҹ���ҧ�����´�ͺ�ͺ �������ҹ�դ����١��ͧ�٧�ش
Ŵ��ͼԴ��Ҵ������Դ������Ǩҡ����������', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('304', 3, '�ʴ����ö���дѺ��� 2 ��д��Ť����١��ͧ�ͧ�ҹ��駢ͧ����м����� (�������㹤����Ѻ�Դ�ͺ�ͧ��)', '��Ǩ�ͺ�����١��ͧ������ͧ�ҹ�ͧ���ͧ ����������բ�ͼԴ��Ҵ��С���� ���
��Ǩ�ͺ�����١��ͧ������ͧ�ҹ������ (�����ѧ�Ѻ�ѭ�� �������˹�ҷ��������Ǣ�ͧ����˹��§ҹ����ͧ���)  ���ԧ�ҵðҹ��û�Ժѵԧҹ ���͡����� ��ͺѧ�Ѻ ������º�������Ǣ�ͧ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('304', 4, '�ʴ����ö���дѺ��� 3 ��СӡѺ��Ǩ�ͺ��鹵͹��û�Ժѵԧҹ�������´', '��Ǩ�ͺ��Ҽ����蹻�ԺѵԵ����鹵͹��÷ӧҹ����ҧ���������������������Ъ�����������蹻�ԺѵԵ����鹵͹��÷ӧҹ����ҧ��� ���ͤ����١��ͧ�ͧ�ҹ
��Ǩ�ͺ��������˹����Ф����١��ͧ/�س�Ҿ�ͧ���Ѿ��ͧ�ç��õ����˹����ҷ���ҧ��� 
�кآ�ͺ����ͧ���͢����ŷ��Ҵ���� ��СӡѺ��������Ң�����������������������Ѿ�����ͼŧҹ����դس�Ҿ���ࡳ�����˹�', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('304', 5, '�ʴ����ö���дѺ��� 4 ������ҧ�����Ѵਹ�ͧ�����١��ͧ��Фس�Ҿ�ͧ��鹵͹��÷ӧҹ���ͼŧҹ�����ç����������´', '���ҧ�����Ѵਹ�ͧ�����١��ͧ��Фس�Ҿ�ͧ��鹵͹��÷ӧҹ���ͼŧҹ�����ç����������´���ͤǺ����������蹻�ԺѵԵ����鹵͹��÷ӧҹ����ҧ������ҧ�١��ͧ����Դ��������ʵ�Ǩ�ͺ�� 
���ҧ�к�����Ըա�÷������ö�ӡѺ��Ǩ�ͺ��������˹����Ф����١��ͧ/�س�Ҿ�ͧ�ŧҹ���͢�鹵͹㹡�û�Ժѵԧҹ�ͧ������ ����˹��§ҹ��� �����ҧ��������', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('305', 1, '���㨤������� �����Ӥѭ ���/������ǧ���͡�� ��Ъ�ͧ�ҧ㹡����������� ��С�þѲ�Ҽ���Сͺ��� �������͢���', '���㨤������� ��Ф����Ӥѭ㹡�����ͧ�������� ���й� ���Իѭ�� ��ѵ���� ���෤����յ�ҧ� ����繻���ª�������Сͺ��� �������͢��� ����������Сͺ��� �������͢����Դ�����������ҧ������� �������� �������ö���������Դ����ª���˹��§ҹ��
��ǧ���͡�� ��Ъ�ͧ�ҧ㹡�����ͧ�������� ���й� ���Իѭ�� ��ѵ���� ���෤����յ�ҧ� �����Сͺ��� �������͢��� ����ਵ�ҷ��Ъ���������Сͺ��� �������͢����Դ��þѲ�Ҥ������ ��������ö �������ö�����մ��������ö㹡���觢ѹ��', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('305', 2, '�ʴ����ö���дѺ��� 1 ������ͧ�������� ���Իѭ�� ��ѵ���� ���෤����շ�������ҧ���ҧ� �����Сͺ��� �������͢���', '������� ������ͧ�������� ���й� ���Իѭ�� ��ѵ���� ���෤����յ�ҧ� ��������ҧ���ҧ� �����Сͺ��� �������͢��� ����������Сͺ��� �������͢�������ö��任�Ѻ��л���ء�����˹��§ҹ����Դ����ª��', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('305', 3, '�ʴ����ö���дѺ��� 2 ��л�Ѻ����¹෤�Ԥ ����Ƿҧ㹡����������� ��С�þѲ�������ҧ�١��ͧ ������� ����ʹ���ͧ���������ͧ���㹡�û�Сͺ������ҧ���ԧ', '��Ѻ����¹෤�Ԥ �Ըա�� �ٻẺ ����Ƿҧ㹡�þѲ�� ��С�������������١��ͧ ������� ����ʹ���ͧ���������ͧ��� ���͡�ô��Թ�ҹ�ҧ��áԨ�ͧ����Сͺ��� �������͢������ҧ���ԧ  
���������¹ ŧ��鹷�� ��������������Сͺ��� �������͢������ҧ�������� �������ö���ͧ�������� ���й� ���Իѭ�� ��ѵ���� ���෤����յ�ҧ� �����ҧ�١��ͧ ������� �����ʹ���ͧ���������ͧ��âͧ����Сͺ��� �������͢������ҧ���ԧ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('305', 4, '�ʴ����ö���дѺ��� 3 �������������� ��ͨӡѴ �͡�� ���ͤҴ��ó� ����������ǧ˹�� ���������й� ����Ƿҧ㹡�þѲ�ҡ�û�Сͺ�������������', '����������� ��ͨӡѴ ��ͺ����ͧ ����͡�� ��� ����繼š�з���͡�þѲ����С����������Է���Ҿ㹡�û�Сͺ��âͧ����Сͺ��� �������͢��� �������ö����ء�� ��оѲ��ͧ�������� ���й� ���Իѭ�� ��ѵ���� ���෤����յ�ҧ� �����ҧ�١��ͧ ������� ������������þѲ�ҡ�û�Сͺ�����������
�Ҵ��ó���ǧ˹���ѹ�Ҩ�������Դ�ѭ�� �ػ��ä ����͡��㹡�þѲ�������������Է���Ҿ �ѡ��Ҿ ��Ф�������ö�ͧ����Сͺ��� �������͢����������� �����駴��Թ������ �Ѳ�� ����������ҷҧ�Ѻ��͡Ѻ�ѭ�� �ػ��ä �����͡�ʹ��� �����ҧ�ջ���Է���Ҿ
���ҧ �ѡ�� ����դ�������ѹ���ѹ�� ������鹡Ѻ����Сͺ��� �������͢���㹤����Ѻ�Դ�ͺ ����������ö�Ѳ�� ��е���ʹ������� ��ѵ���� ���෤���������� ����繻���ª��㹡�û�Сͺ������ҧ���ԧ �����������', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('305', 5, '�ʴ����ö���дѺ��� 4 ��С�˹���º����С�ͺ��þѲ���Ҥ��áԨ�ص��ˡ�����Ҿ������ʹ���ͧ�Ѻ���ǡ�ó����ɰ�Ԩ ����ص��ˡ����ͧ�����', '��˹���º�� Ἱ�ط���ʵ�� ���ط�� ��С�ͺ��þѲ�Ҽ���Сͺ��� �������͢�����Ҿ����ͧ��������ʹ���ͧ�Ѻ���ǡ�ó����ɰ�Ԩ ����ص��ˡ����ͧ����� �������ö��任�Ժѵ����ԧ (Implementation) ����ʹѺʹع��������մ��������ö㹡���觢ѹ�ҧ��ä�����ҧ����׹
��١�Ե�ӹ֡ ��е�� �������������ء˹��§ҹ�ͧ��õ��˹ѡ ��繤����Ӥѭ ��д��Թ��ô�ҹ������������оѲ�Ҽ���Сͺ����������͢������ҧ���ٻ���� ����¡�дѺ�ҵðҹ��þѲ���ص��ˡ����ͧ���������Ժ�����ջ���Է���Ҿ�٧�ش
�ҧἹ �Ѳ��Ἱ��þѲ�� ������������ ���й� ��С�þѲ���»���ء�� Best Practice �š���Ԩ�� ��������Ǫҭ ���ͻ��ʺ��ó��ѹ��ǹҹ 㹡�á�˹�Ἱ�ҹ��ҹ������������оѲ�Ҽ���Сͺ��� �������͢��������ҧ�١��ͧ ������� ��й�任�Ժѵ����ԧ (Implementation) ��', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('306', 1, '�ҧἹ�ҹ�͡����ǹ�����', '�ҧἹ�ҹ�繢�鹵͹���ҧ�Ѵਹ �ռ��Ѿ�� ��觷���ͧ�Ѵ����� ��СԨ������ҧ� �����Դ������ҧ�١��ͧ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('306', 2, '�ʴ����ö���дѺ��� 1 �������ӴѺ�����Ӥѭ���ͤ�����觴�ǹ�ͧ�ҹ', '�ҧἹ�ҹ���¨Ѵ���§�ҹ ���͡Ԩ������ҧ� ����ӴѺ�����Ӥѭ���ͤ�����觴�ǹ 
�Ѵ�ӴѺ�ͧ�ҹ��м��Ѿ����ç��������������ö�Ѵ����ç���������ص��Ἱ������ҷ���ҧ����� 
���������Ң�ʹ� ���������мŵ�����ͧ�ͧἹ�ҹ����ҧ ��������ö�ҧἹ�ҹ���������ҧ�ջ���Է���Ҿ�ҡ���', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('306', 3, '�ʴ����ö���дѺ��� 2 ����ҧἹ����������§�ҹ���͡Ԩ������ҧ� ����դ����Ѻ��͹����������ص��Ἱ����˹������', '�ҧἹ�ҹ�¡�˹��Ԩ���� ��鹵͹��ô��Թ�ҹ��ҧ� ����ռ������Ǣ�ͧ���½��������ҧ�ջ���Է���Ҿ
�ҧἹ�ҹ����դ���������§���ͫѺ��͹�ѹ����� �ҹ��������� �ç����¡�˹��Ԩ���� ��鹵͹��ô��Թ�ҹ��ҧ� ���ʹѺʹع������Ѵ��駡ѹ�����ҧ�ջ���Է���Ҿ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('306', 4, '�ʴ����ö���дѺ��� 3 �������ö�Ҵ��ó���ǧ˹������ǡѺ�ѭ��/�ҹ���������ҧ���͡����Ѻ��û�ͧ�ѹ/��䢻ѭ�ҷ���Դ���', '�ҧἹ�ҹ���Ѻ��͹�¡�˹��Ԩ���� ��鹵͹��ô��Թ�ҹ��ҧ� �����˹��§ҹ���ͼ������Ǣ�ͧ���½��� ����֧�Ҵ��ó�ѭ�� �ػ��ä ����ҧ�Ƿҧ��û�ͧ�ѹ��������ǧ˹�� �ա����ʹ��зҧ���͡��Т�ʹբ������������
�����Ἱ�Ѻ��͡Ѻ������Ҵ��ó���������ҧ�Ѵ�������ջ���Է���Ҿ
�ҧἹ�ҹ����դ���������§���ͫѺ��͹�ѹ����� �ҹ��������� �ç����¡�˹��Ԩ���� ��鹵͹��ô��Թ�ҹ��ҧ� ���ʹѺʹع������Ѵ��駡ѹ�����ҧ�ջ���Է���Ҿ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('306', 5, '�ʴ����ö���дѺ��� 4 ��л�Ѻ���ط���Ἱ�����ҡѺʶҹ��ó�੾��˹�ҹ�����ҧ���к�', '��Ѻ���ط������ҧἹ���ҧ�Ѵ���������к������ҡѺʶҹ��ó����Դ������ҧ���Ҵ�Դ ������ѭ�� �ػ��ä �������ҧ�͡�ʹ�����ҧ�ջ���Է���Ҿ�٧�ش����������ö������ѵ�ػ��ʧ��������·���˹�������ҧ�ջ���Է���Ҿ�٧�ش', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('307', 1, '��Ժѵԧҹ�¤ӹ֧�֧�������������Ф������·���Դ���', '���˹ѡ�֧�������������Ф������µ�ҧ� �����Դ���㹡�û�Ժѵԧҹ
��Ժѵԧҹ�����кǹ��â�鹵͹����˹���� �����������ö���Ѿ�ҡ�����Թ�ͺࢵ����˹�', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('307', 2, '�ʴ����ö���дѺ��� 1 ��л�Ժѵԧҹ�¤ӹ֧�֧�������·���Դ��� ����դ�������������Ŵ�����������ͧ��', '���˹ѡ��ФǺ����������·���Դ���㹡�û�Ժѵԧҹ���դ�������������Ŵ�������µ�ҧ� �����Դ���
�Ѵ��ç�����ҳ �������� ��Ѿ�ҡ÷�����������ҧ�ӡѴ�������������Դ����ª��㹡�û�Ժѵԧҹ���ҧ�٧�ش', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('307', 3, '�ʴ����ö���дѺ��� 2 ��С�˹�������Ѿ�ҡ��������ѹ��Ѻ���Ѿ�����ͧ���', '�����Թ�Ť����ջ���Է���Ҿ�ͧ��ô��Թ�ҹ����ҹ�����ͻ�Ѻ��ا��èѴ��÷�Ѿ�ҡ������ż�Ե���������� �����ա�÷ӧҹ����ջ���Է���Ҿ�ҡ��� �����դ������·��Ŵŧ
�кآ�ͺ����ͧ ���������ʹ� ������¢ͧ��кǹ��á�÷ӧҹ��С�˹�������Ѿ�ҡ÷������ѹ��Ѻ���Ѿ�����ͧ������ͧ�Ż���ª��ͧ��� ����ѡ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('307', 4, '�ʴ����ö���дѺ��� 3 ���������§���ͻ���ҹ��ú����÷�Ѿ�ҡ������ѹ�����ҧ˹��§ҹ��������Դ������Ѿ�ҡ÷���������٧�ش', '���͡��Ѻ��ا��кǹ��÷ӧҹ����Դ����Է���Ҿ�٧�ش�Ѻ����˹��§ҹ �������з���кǹ��÷ӧҹ��ҧ� ���㹡��
�ҧἹ���������§��áԨ�ͧ˹��§ҹ���ͧ�Ѻ˹��§ҹ��� (Synergy) ������������Ѿ�ҡâͧ˹��§ҹ�������Ǣ�ͧ�������Դ����ª���٧�ش
��˹����/����������á�кǹ��á�ú����÷�Ѿ�ҡ÷���ʹ���ͧ�ѹ���Ƿ��ͧ��� ���������մ��������ö�ͧͧ���', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('307', 5, '�ʴ����ö���дѺ��� 4 ����ʹ͡�кǹ�������� 㹡�÷ӧҹ����ջ���Է���Ҿ��觢����������Դ��þѲ�ҷ������׹', '�Ѳ�ҡ�кǹ�������� �����������·�ȹ� ��������Ǫҭ ��л��ʺ��ó��ҧ� �һ���ء��㹡�кǹ��÷ӧҹ ����Ŵ���С�ú����çҹ�������ö���Թ�ҹ�����ҧ�ջ���Է���Ҿ�٧�ش
����ö�����ż�Ե�������ҧ��ä�ҹ���� ���ⴴ��ᵡ��ҧ���Ѻ˹��§ҹ ���ͧ��� �����Ѿ�ҡ�������', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('308', 1, '�դ����״����㹡�û�Ժѵ�˹�ҷ��', '���㨤������¢ͧ���Դ���������� �������ö��Ѻ��÷ӧҹ�����ͧ�������ʹ���ͧ�Ѻ������ͧ�����', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('308', 2, '�ʴ����ö���дѺ��� 1 ������㨺ؤ������ʶҹ��ó��������о��������Ѻ�������繷��е�ͧ��Ѻ����¹', '���� ����Ѻ ������㨤����Դ��繢ͧ�����蹷����ԧ��������й���ԧ������ 
���㨷�������¹�����Դ ��ȹ��� ��зӧҹ������ص��������� �����ʶҹ��ó��Ѻ����¹� �� ���Ѻ�������������͢�ͤԴ�������ҡ�������Ǫҭ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('308', 3, '�ʴ����ö���дѺ��� 2 ������㨤�������ὧ�ͧ�ؤ�����ʶҹ��ó�������͡��Ժѵԧҹ���ҧ�״����', '���Ԩ�ó�ҳ㹡�û�Ѻ�����ҡѺʶҹ��ó�੾��˹�� ��������Դ�����ķ���㹡�û�Ժѵԧҹ ����������������ѵ�ػ��ʧ��ͧ˹��§ҹ���ͧ͢��з�ǧ�
����ö�դ����������ͧ�֡���������ʴ��͡���ҧ�Ѵਹ�ͧ�ؤ������ʶҹ��ó����Դ��� ���ǻ�Ѻ�������ʹ���ͧ �����������Ѻ�Ѻ���кؤ������ʶҹ��ó�ѧ����������ҧ�ջ���Է���Ҿ
����ö���͡�ҧ���͡ �Ըա�� ���͡�кǹ����һ�Ѻ��Ѻʶҹ��ó���੾����Ш������ҧ�ջ���Է���Ҿ ���������ŧҹ����', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('308', 4, '�ʴ����ö���дѺ��� 3 ��������������ԧ�֡��ͺؤ������ʶҹ��ó��һ�Ѻ����¹�Ըա�ô��Թ�ҹ�����ҹ����ջ���Է���Ҿ�٧�ش', '������������ҧ�֡���㹺ؤ������ʶҹ��ó��ҧ� ����繻���ª��㹷ӧҹ�����ŧҹ����ջ���Է���Ҿ�٧�ش
��Ѻ����¹�Ըա�ô��Թ�ҹ ����º��鹵͹�����ѡɳС�û���ҹ�ҹ�ͧ˹��§ҹ����ͧ��� �����ҡѺʶҹ��ó� ���ѧ�����������������ҧ�ջ���Է���Ҿ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('308', 5, '�ʴ����ö���дѺ��� 4 ��л�Ѻ����¹Ἱ���ط������� �������ҹ�ջ���Է���Ҿ', '��ѺἹ���ط������� ���������������Ѻʶҹ��ó�੾��˹��
�ըԵ�Է��㹡����������㨼������ʶҹ��ó��ҧ� �����繾�鹰ҹ㹡���èҷӤ������� ���ʹ��Թ�ҹ���������áԨ�ͧ˹��§ҹ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('309', 1, '����෤����� �к� ��кǹ��÷ӧҹ����ҵðҹ㹧ҹ�ͧ��', '����෤����� �к� ��кǹ��÷ӧҹ����ҵðҹ㹧ҹ��赹�ѧ�Ѵ���� �����駡�����º ��ʹ����鹵͹��кǹ��û�Ժѵԧҹ��ҧ� ��йӤ������㨹������㹡�û�Ժѵԧҹ �Դ��ͻ���ҹ�ҹ ������§ҹ�� ��� �˹�ҷ����١��ͧ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('309', 2, '�ʴ����ö���дѺ��� 1 ������㨤�������ѹ��������§�ͧ�к���С�кǹ��÷ӧҹ�ͧ���Ѻ˹��§ҹ���� ���Դ������ҧ�Ѵਹ', '�������������§෤����� �к� ��кǹ��÷ӧҹ ��鹵͹��кǹ��û�Ժѵԧҹ��ҧ� �ͧ���Ѻ˹��§ҹ��蹷��Դ��ʹ������ҧ�١��ͧ ����֧�Ӥ������㨹��������������÷ӧҹ�����ҧ�ѹ������ҧ�ջ���Է���Ҿ����ʹ�Ѻ�ѹ�٧�ش', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('309', 3, '�ʴ����ö���дѺ��� 2 �������ö�ͧ�Ҿ������ǻ�Ѻ����¹���ͻ�Ѻ��ا�к�����ջ���Է���Ҿ���', '���㨢�ͨӡѴ�ͧ෤�Ԥ �к����͡�кǹ��÷ӧҹ�ͧ������˹��§ҹ���� ���Դ��ʹ��� ������������㴷���á�з����ͻ�Ѻ����¹���ͻ�Ѻ��ا�к��������ö�ӧҹ�����ҧ�ջ���Է���Ҿ�٧����� 
�������ʶҹ��ó���ᵡ��ҧ�ҡ�������ö��������㨼ŵ�����ͧ��Ф�������ѹ��������§�ͧ�к���С�кǹ��÷ӧҹ ���͹�����䢻ѭ�������ҧ��������ѹ����', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('309', 4, '�ʴ����ö���дѺ��� 3 ������㨡������¹͡�Ѻ�š�з���������෤����� �к����͡�кǹ��÷ӧҹ�ͧ˹��§ҹ', '���㨡��������ʶҹ��ó���¹͡ (�� ��º���Ҥ�Ѱ��Ҿ��� �������¹�ŧ��ص��ˡ���) �������ö�Ӥ������㨹����������Ѻ������ʹ��Թ��á������¹�ŧ�����ҧ�����������Դ����ª���٧�ش 
�֡�����¹��������������ͤ����Դ��Ҵ�ͧ�к����͡�кǹ��á�÷ӧҹ�������Ǣ�ͧ��й��һ�Ѻ��Ѻ��÷ӧҹ�ͧ˹��§ҹ���ҧ�������', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('309', 5, '�ʴ����ö���дѺ��� 4 ������㨤�����ͧ��÷�����ԧ�ͧͧ���', '����ʶҹТͧ�к� ෤����� ��С�кǹ��á�÷ӧҹ�ͧ��� ���ҧ��ͧ�� ������ö��˹�������ͧ������ʹ��Թ�������¹�ŧ��Ҿ����������ͧ����Ժ����ҧ����׹ 
�����������ö�кبش�׹��Ф�������ö㹡�þѲ����ԧ�к� ෤����� ��кǹ��÷ӧҹ�����ҵðҹ��÷ӧҹ��ԧ��óҡ���к� (Holistic Vew) �ͧͧ���', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('310', 1, '���ʹ͢��������ҧ�ç仵ç��', '���ʹ͢����� ͸Ժ�� ���ᨧ��������´����ѧ���ҧ�ç仵ç�����ԧ�����ŷ�������� ���Ҩ�ѧ�����ա�û�Ѻ㨤�������Ըա������ʹ���ͧ�Ѻ����ʹ���кؤ�ԡ�ѡɳТͧ���ѧ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('310', 2, '�ʴ����ö���дѺ��� 1 ����è������������������ѡ�������˵ؼ�', '�������ù��ʹ͢����������ҧ�� ���������������è�����������¡��ѡ�������˵ؼŷ������Ǣ�ͧ�һ�Сͺ��ù��ʹ����ҧ�բ�鹵͹
������������è�����������¡��ѡ�������˵ؼŷ������Ǣ�ͧ��͸Ժ�»�Сͺ��ù��ʹ����ҧ�բ�鹵͹', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('310', 3, '�ʴ����ö���дѺ��� 2 ����èҵ���ͧ���͹��ʹ͢������»�Ѻ�������ʹ���ͧ�Ѻ���ѧ���Ӥѭ', '����ء����������� ����ʹ㨢ͧ���ѧ����繻���ª����è��ʹ͢����� ���ʹ������è��¤Ҵ��ó�֧��ԡ����� �š�з������յ�ͼ��ѧ����ѡ
����ö���ʹͷҧ���͡�����������ػ㹡���è��ѹ�繻���ª�������ͧ����', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('310', 4, '�ʴ����ö���дѺ��� 3 �������ط����������è٧㨷ҧ����', '��������㨺ؤ������ͧ�������繻���ª���¡�ù���Һؤ�ŷ��������ͼ������Ǫҭ��ʹѺʹع������è�������Ǩ٧㨻��ʺ������������չ��˹ѡ�ҡ��觢��
��ѡ��㹡���������㨷ҧ���� �������������ķ���ѧ���ʧ���¤ӹ֧�֧�š�з���Ф�������֡�ͧ���������Ӥѭ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('310', 5, '�ʴ����ö���дѺ��� 4 �������ط����Ѻ��͹㹡�è٧�', '���ҧ���������������ʹѺʹع������è����������չ��˹ѡ������ķ���������觢��
����ء������ѡ�Ե�Է����Ū����ͨԵ�Է�ҡ��������繻���ª��㹡���è�������Ǩ٧������ҧ�ջ���Է���Ҿ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('311', 1, 'ʹѺʹع�����Դ���ҧ��ä����������ͧ�Ը����� �����ҷ�᷹�Ըա�÷�����������㹡�û�Ժѵԧҹ���ҧ�������������', '���㨷�������Ѻ��л�Ѻ��ǵ�ͤ�������������ҧ��ä������������ ����ʹѺʹع���˹��§ҹ�����������·���˹�
�ʴ�����ʧ�����������е�ͧ��÷��ͧ�Ըա������� ����Ҩ�觼���黯Ժѵԧҹ��բ��
���㨷�������������֡���Ըա�÷���š�������Ҩ���һ���ء����㹡�û�Ժѵԧҹ�����ҧ�������', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('311', 2, '�ʴ����ö���дѺ��� 1 ������ҧ��ä�������蹻�Ѻ��ا��кǹ��÷ӧҹ�ͧ�����ҧ��������', '���蹻�Ѻ��ا��кǹ��÷ӧҹ�ͧ�����ҧ��������  
����¹�ŧ�ٻẺ���͢�鹵͹��÷ӧҹ����� ����ʹ���ͧ���ʹѺʹع˹��§ҹ�������ö�����������������ҧ�ջ���Է���Ҿ�ҡ���', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('311', 3, '�ʴ����ö���дѺ��� 2 ��ФԴ�͡��ͺ���ͻ�Ѻ����¹��ô��Թ�ҹ�����˹��§ҹ�������ҹ�ջ���Է���Ҿ', '����ء������ʺ��ó�㹡�÷ӧҹ�һ�Ѻ����¹�Ըա�ô��Թ�ҹ �����ҡѺʶҹ��ó� ���ѧ��������������ҧ�ջ���Է���Ҿ�٧�ش
���ӡѴ���ͧ����Ѻ�ǤԴ�����������ѹ ������з��ͧ�Ըա������� �һ�Ѻ�������º��鹵͹��÷ӧҹ���������� ������������Է���Ҿ�ͧ˹��§ҹ
���ʹͷҧ���͡ (Option) �����Ƿҧ��ѭ�� (Solution) 㹧ҹ�ͧ�����ҧ���ҧ��ä��͹���л�֡�Ҽ��ѧ�Ѻ�ѭ��', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('311', 4, '�ʴ����ö���дѺ��� 3 ������ҧ��ä��������� �ͧ���', '����ء����ͧ�������� ��ɮ� �����ǤԴ������Ѻ�������Ѻ���ʹͷҧ���͡ (Option) �����Ƿҧ��ѭ�� (Solution) 㹡�þѲ��ͧ�������ջ���Է���Ҿ�٧���
����������ҧ��ä��Ƿҧ����� 㹡�û�Ժѵԧҹ���ʹ��Թ��õ�ҧ� ���ͧ�������ö����ؾѹ��Ԩ�����ҧ�ջ���Է���Ҿ�����դس�Ҿ�٧��� ���Ƿҧ����� ���� Best Practice ����Ҩ�����������ͧ������� ����Ҥ�Ѱ�����͡�� ��з�����е�ҧ�����', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('311', 5, '�ʴ����ö���дѺ��� 4 ������ҧ��ѵ������к��ص��ˡ����ͧ����������', '�Դ�͡��ͺ �Ԩ�ó���觵�ҧ� 㹧ҹ��������ͧ���ᵡ��ҧ �ѹ���������Ԩ�� ��û�д�ɰ�Դ�� ���͡�����ҧ��ä� ���͹��ʹ͵�Ẻ �ٵ� �ٻẺ �Ը� ��ʹ��ͧ������������������»�ҡ��ҡ�͹����繻���ª�����к��ص��ˡ��������ѧ����л���Ȫҵ������
ʹѺʹع����Դ����ҡ����觤����Դ���ҧ��ä��������ҧ�͡������ҧ��áԨ�ͧ��� ���¡�������ʹѺʹع�ҧ��Ѿ�ҡ� ���ͨѴ�Ԩ������ҧ� ���Ъ��¡�е������Դ����ʴ��͡�ҧ�����Դ���ҧ��ä�', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('312', 1, '��õͺʹͧ�����ҧ�Ǵ���� ������������˵��ԡĵ� ����ʶҹ��ó����', '�ͺʹͧ���ҧ�Ǵ���� ������������������˵��ԡĵ������ʶҹ��ó�������������ѹ��ͤ�����觴�ǹ�ͧʶҹ��ó����', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('312', 2, '�ʴ����ö���дѺ��� 1 ��е��˹ѡ�֧�ѭ�������͡�����ŧ��͡�зӡ�����������', '���˹ѡ�֧�ѭ�������͡��㹢�й�����ŧ��͡�зӡ��������������ʶҹ��ó���������ͧ ���ͻ�����͡����ش���� �ա������ѡ��ԡ�ŧ�Ըա�� ��кǹ��õ�ҧ� �����������ö��䢻ѭ�� ���������ª��ҡ�͡�ʹ�������ҧ�ջ���Է���Ҿ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('312', 3, '�ʴ����ö���дѺ��� 2 ����������͡�����ͻѭ�ҷ���Ҩ�Դ������������� (����ҳ 1-3 ��͹��ҧ˹��)', '�Ҵ��ó���������繻ѭ�������͡�ʷ���Ҩ�Դ������������ 1-3 ��͹�Ѵ�ҡ�Ѩ�غѹ ���ŧ��͡�зӡ����ǧ˹�����ͻ�ͧ�ѹ�ѭ�� �������ҧ�͡���ʶҹ��ó���� �ա����Դ���ҧ�Ѻ�ѧ�Ƿҧ��Ф����Դ��ҡ�����ѹ�Ҩ�繻���ª���͡�û�ͧ�ѹ�ѭ��', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('312', 4, '�ʴ����ö���дѺ��� 3 ����������͡�����ͻѭ�ҷ���Ҩ�Դ���������С�ҧ (����ҳ 4-12 ��͹��ҧ˹��)', '�Ҵ��ó���������繻ѭ�������͡�ʷ���Ҩ�Դ������������ 4-12 ��͹�Ѵ�ҡ�Ѩ�غѹ �������������ǧ˹�����ͻ�ͧ�ѹ�ѭ�� �������ҧ�͡���ʶҹ��ó���� ��ʹ�����ͧ����������Ըա�� �ǤԴ����� ����Ҩ�繻���ª��㹡�û�ͧ�ѹ�ѭ��������ҧ�͡���͹Ҥ�', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('312', 5, '�ʴ����ö���дѺ��� 4 �������������ǧ˹�����ͻ�ͧ�ѹ�ѭ��������ҧ�͡����������', '�Ҵ��ó���������繻ѭ�������͡�ʷ���Ҩ�Դ������������������������ǧ˹�����ͻ�ͧ�ѹ�ѭ�� �������ҧ�͡�� �ա��駡�е�����������Դ������е������鹵�͡�û�ͧ�ѹ�����䢻ѭ���������ҧ�͡�����ͧ�����������', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('313', 1, '�Ң�������дѺ������ʴ��Ţ�������', '����ö�Ң������¡�ö���ҡ���������Ǣ�ͧ�µç ���������ŷ�������� �����Ҩҡ���觢����ŷ�����������������ػ�Ţ����������ʴ��Ţ�������ٻẺ��ҧ� �� ��ҿ ��§ҹ �����ҧ�١��ͧ �ú��ǹ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('313', 2, '�ʴ����ö���дѺ��� 1 ������Ըա���׺�����Ң��������ͨѺ����������蹤����ͧ���������ͻѭ����', '����ö�׺���лѭ������ʶҹ��ó����ҧ�֡��駡��ҡ�õ�駤Ӷ������á�Ը����� �����׺���Шҡ�������Ǫҭ ����������ҫ�������ͻ���繢ͧ������ ��й������ͻ��������ҹ���ҨѴ����������� �����Թ������Դ�����ŷ���֡����ҡ����ش', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('313', 3, '�ʴ����ö���дѺ��� 2 ����Ң���������ͧ�֡ (Insights)', '���������ͺ�������֡���ҧ������ͧ (�� �ҡ˹ѧ��� ˹ѧ��;���� �Ե���� �к��׺���������෤��������ʹ�� ��ʹ�����觢��ǵ�ҧ�) ����������㨶֧����ͧ��ȹФ����Դ��繷��ᵡ��ҧ �鹵ͧ͢ʶҹ��ó� �ѭ�� �����͡�ʷ���͹�����������ͧ�֡ ��йӤ�����������ҹ���һ����Թ�� ��еդ����繢����������ҧ�١��ͧ����Դ����ª���͡�û�Ժѵԧҹ�٧�ش', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('313', 4, '�ʴ����ö���дѺ��� 3 ����׺�鹢��������ҧ���к�����������͢����ŷ��Ҵ�������ͤҴ��ó������ҧ�չ���Ӥѭ', '�Ѵ�ӡ���Ԩ������ҧ�ԧ�ҡ�����ŷ�������������׺�鹨ҡ���觢����ŷ���š����ᵡ��ҧ�ҡ�á�Ը����ҷ�������ҧ���к�������仵����ѡ��÷ҧʶԵ� ��йӼŷ���������������͢����ŷ��Ҵ���� ���;�ҡó��������ҧẺ���ͧ (model) �������ҧ�к� (system formula) �����ҧ�١��ͧ������к�', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('313', 5, '�ʴ����ö���дѺ��� 4 ����ҧ�к�����׺�� �����Ң��������ҧ������ͧ', '�ҧ�к�����׺�� ��������բ����ŷ��ѹ�˵ء�ó��͹��������ҧ������ͧ�������ö�͡Ẻ ���͡�� ���ͻ���ء���Ըա��㹡�èѴ��Ẻ���ͧ�����к���ҧ� �����ҧ�١��ͧ����չ���Ӥѭ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('314', 1, '�ҧἹ�ҹ����鹰ҹ�ͧ��������෤����� �к� ��кǹ��÷ӧҹ����ҵðҹ㹧ҹ�ͧ��', '����෤����� �к� ��кǹ��÷ӧҹ����ҵðҹ㹧ҹ��赹�ѧ�Ѵ���� �����駡�����º ��ʹ����鹵͹��кǹ��û�Ժѵԧҹ��ҧ� ��йӤ������㨹������㹡���ҧἹ�ҹ����繢�鹵͹����Դ���Ѿ�����ҧ�Ѵਹ��ж١��ͧ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('314', 2, '�ʴ����ö���дѺ��� 1 ����ҧἹ�ҹ��������Ӥѭ�ͧ�ҹ����鹰ҹ�ͧ�������㨤�������ѹ��������§�ͧ�к���С�кǹ��÷ӧҹ�ͧ���Ѻ˹��§ҹ���� ���Դ������ҧ�Ѵਹ', '�������������§෤����� �к� ��кǹ��÷ӧҹ ��鹵͹��кǹ��û�Ժѵԧҹ��ҧ� �ͧ���Ѻ˹��§ҹ��蹷��Դ��ʹ������ҧ�١��ͧ ��й����ҧἹ�Ѵ�ӴѺ�ͧ�ҹ��м��Ѿ�������������ö����ص���������������ҷ���ҧ��������ҧ�ջ���Է���Ҿ�٧�ش
���������Ң�ʹ� ���������мŵ�����ͧ�ͧἹ�ҹ����ҧ����¡�������������������§�ͧ��кǹ�ҹ��ҧ� �ͧ�����˹��§ҹ�������Ǣ�ͧ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('314', 3, '�ʴ����ö���дѺ��� 2 �������ö�ͧ�Ҿ��������ҧἹ����������§�ҹ���͡Ԩ������ҧ� ����դ����Ѻ��͹����������ص��Ἱ����˹������ �������ͻ�Ѻ��ا�к�����ջ���Է���Ҿ�����', '���㨢�ͨӡѴ�ͧ෤�Ԥ �к����͡�кǹ��÷ӧҹ�ͧ������˹��§ҹ���� ���Դ��ʹ��� ����ҧἹ�ҹ����դ���������§���ͫѺ��͹�ѹ����� �ҹ��������� �ç����������ҹ����ص��������� ������������Դ��û�Ѻ����¹���ͻ�Ѻ��ا�к��������ö�ӧҹ�����ҧ�ջ���Է���Ҿ�٧�����', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('314', 4, '�ʴ����ö���дѺ��� 3 ������㨡������¹͡�Ѻ�š�з���������෤����� �к����͡�кǹ��÷ӧҹ�ͧ˹��§ҹ��ФҴ��ó���ǧ˹������������ҧ���͡����Ѻ��û�ͧ�ѹ/��䢻ѭ�ҷ���Դ���', '���㨡��������ʶҹ��ó���¹͡ (�� ��º���Ҥ�Ѱ��Ҿ��� �������¹�ŧ��ص��ˡ���) �������ö�Ӥ������㨹����������Ѻ������ʹ��Թ��á������¹�ŧ�����ҧ�����������Դ����ª���٧�ش 
�֡�����¹��������������ͤ����Դ��Ҵ�ͧ�к����͡�кǹ��á�÷ӧҹ�������Ǣ�ͧ���������Ἱ�Ѻ��͡Ѻ������Ҵ��ó���������ҧ�Ѵ�������ջ���Է���Ҿ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('314', 5, '�ʴ����ö���дѺ��� 4 ������㨤�����ͧ��÷�����ԧ�ͧͧ��� ��С�з�ǧ� ��л�Ѻ���ط���Ἱ�����ҡѺʶҹ��ó�੾��˹�ҹ�����ҧ���к�', '����ʶҹТͧ�к� ෤����� ��С�кǹ��á�÷ӧҹ�ͧͧ��� ��С�з�ǧ� ���ҧ��ͧ�� ������ö��Ѻ���ط������ҧἹ���ҧ�Ѵ��� ���ʹ��Թ�������¹�ŧ��Ҿ������ͧ��� ��С�з�ǧ� �Ժ����ҧ����׹', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('315', 1, '�Դ����Ҥ����������ǤԴ����� �����ԪҪվ ������㹡���������������䢻ѭ��������鹷���Դ���', '��е�������㹡���֡���Ҥ����������෤���������� ��Ң��Ҫվ�ͧ������㹧ҹ�ͧ�ӹѡ ���͹���������Դ����ª��㹡����䢻ѭ��
�������������Ҫվ�ͧ��㹡��ŧ������ ����������繻ѭ�������ػ��ä������ͪ��', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('315', 2, '�ʴ����ö���дѺ��� 1 �������������еѴ�Թ����ҧ�բ���������˵ؼ�㹡�èѴ��ûѭ�ҷ���Դ���', '������������� ������˵ؼŵ���ǤԴ �����ѡ�����ԪҪվ ���͵Ѵ�Թ㨴��Թ�����䢻ѭ�ҷ���Դ������ҧ�ջ���Է���Ҿ�٧�ش
��ԡ�ŧ���ͻ���ء���Ƿҧ㹡����ѭ�� ����ҧ�ԧ�ҡ������ ��ѡ��� ����ǤԴ�����ԪҪվ ���ͻ��ʺ��ó�㹡�÷ӧҹ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('315', 3, '�ʴ����ö���дѺ��� 2 �����������ѭ�ҷ���ҹ�� ����ҧἹ��ǧ˹�����ҧ���к� ���ͻ�ͧ�ѹ������ա����§�ѭ��', '������������� �ѭ�� ����ʶҹ��ó������ҧ�ͺ��ҹ (������»��ʺ��ó� ��Ф�������Ǫҭ���������������Ҫվ) �������ҧἹ ��ФҴ��ó�š�з������Դ������ҧ���к� ���ͻ�ͧ�ѹ�����ա����§�ѭ�ҷ���Ҩ�Դ��� 
�ҧἹ ��з��ͧ���Ըա�� ͧ�������� ����෤���������� �����Ҫվ 㹡�û�ͧ�ѹ ��ա����§������䢻ѭ������Դ����˹��§ҹ����ͧ���', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('315', 4, '�ʴ����ö���дѺ��� 3 ��м����ҹ�ǤԴ��ԧ���Է�ҡ��������ա����§ ��ͧ�ѹ������䢻ѭ�ҷ��������������������', '�������� ��м����ҹ��ʵ������� ᢹ� (������»��ʺ��ó� ��������Ǫҭ����駡��ҧ����֡ �����駤�������ö����� (Charisma)) ������䢻ѭ�ҫ���դ����Ѻ��͹������������������û�ͧ�ѹ������ա����§�ѭ������������
�Դ�͡��ͺ ��������ç��� ���͡�кǹ��÷ӧҹ��ҧ� ��ѡɳк�óҡ������˹��§ҹ/�����ԪҪվ ������䢻ѭ�ҷ��Ҵ��Ҩ��Դ����͹Ҥ�', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('315', 5, '�ʴ����ö���дѺ��� 4 ��л�Ѻ����¹�������ҧ��������Ǫҭ�����Ҫվ/���Է�ҡ�� ������������ա����§�ѭ�����ҧ����׹', '��Ѻ����¹ (Reshape) ͧ�������ա�ú�óҡ����ԧ�ԪҪվ ��������դ�������Ǫҭ�����Ҫվ���ҧ���ԧ �����������ö��� ��ͧ�ѹ�����ա����§�ѭ�ҷ���ռš�з��٧�����դ����Ѻ��͹�٧�ͧͧ��������ҧ����׹
�繼��ӷ�����Ѻ�������Ѻ����繼������Ǫҭ�����Ҫվ�������ö��ͧ�ѹ �����ա����§�ѭ�ҷ���ռš�з��ԧ��º�� ��С��ط��ͧͧ��������ҧ�ջ���Է���Ҿ�٧�ش ��������ö��䢻ѭ�ҷ���ռš�з� ���ԡĵ�������͡�� ����Դ����ª�����ҧ����׹��ͧ�����������', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(1, '101', 1, '����Ѻ��û����Թ�դ����ҹ�ʹ����Т�ѹ��������㹡�÷ӧҹ�˹�ҷ������ �١��ͧ ����������稵�����ҷ���˹����', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(2, '101', 2, '����Ѻ��û����Թ�ӧҹ�����ŧҹ���������¢ͧ˹��§ҹ����Ѻ�Դ�ͺ', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(3, '101', 3, '����Ѻ��û����Թ��Ѻ��ا�Ըա�����͡�кǹ��÷������ӧҹ��բ�� ���Ǣ�� �դس�Ҿ�բ�� �����ջ���Է���Ҿ�ҡ���', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(4, '101', 4, '����Ѻ��û����Թ�Ѳ���к� ��鹵͹ �Ըա�÷ӧҹ�˹��§ҹ ���. ����㹡�з�ǧ�ص��ˡ��� ���������ŧҹ���ⴴ������ᵡ��ҧ���ҧ��������÷����ҡ�͹', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(5, '101', 5, '����Ѻ��û����Թ���ҵѴ�Թ� �����ҡ�õѴ�Թ㨹�鹨��դ�������§ �����������������¢ͧͧ������ա�äӹǳ������������ҧ�Ѵਹ ��������Ҥ�Ѱ��л�ЪҪ������ª���٧�ش', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(6, '102', 1, '����Ѻ��û����Թ����ԡ�������������Ţ�����÷��١��ͧ ���Ҿ ������Ե������Ѻ��ԡ��', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(7, '102', 2, '����Ѻ��û����Թ�Ѻ�繸��� ������ѭ�� �������Ƿҧ��䢻ѭ�ҷ���Դ��������Ѻ��ԡ�����ҧ�Ǵ���� ���� ���������§ ������Ѻ��ԡ�����Ѻ�����֧����٧�ش', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(8, '102', 3, '����Ѻ��û����Թ�������������ԡ�÷���Թ�����Ҵ��ѧ�ͧ����Ѻ��ԡ�� ����ͧ���������ͤ������������ҧ�ҡ�繾����', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(9, '102', 4, '����Ѻ��û����Թ���㨤����������ͤ�����ͧ��÷�����ԧ�ͧ����Ѻ��ԡ�� ������ö�����й����ͺ�ԡ�÷���繻���ª���͹Ҥ������Ѻ��ԡ�� �����������ͧ��', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(10, '102', 5, '����Ѻ��û����Թ����ԡ�÷���繻���ª�����ҧ���ԧ����������������Ѻ��ԡ��', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(11, '103', 1, '����Ѻ��û����Թ��е�������㹡���֡���Ҥ������ ෤��������ͧ������������� ��Ң��Ҫվ�ͧ��', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(12, '103', 2, '����Ѻ��û����Թ�ͺ�����ҷѹ෤���������ͧ������������� ��Ң��Ҫվ�ͧ���������ö�ͺ�Ӷ���������Ǣ�ͧ�����ҧ�١��ͧ', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(13, '103', 3, '����Ѻ��û����Թ�������������������ö���Ԫҡ�� ������� ����෤���������� �ҵ���ʹ���ͻ���ء����㹡�û�Ժѵԧҹ�˹��§ҹ�ͧ����', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(14, '103', 4, '����Ѻ��û����Թ�Ѳ�ҵ��ͧ�����Ѻ�������Ѻ����դ������ ��Ф�������Ǫҭ㹧ҹ����Ҫվ�����ԧ�֡ ����ԧ���ҧ���ҧ������ͧ', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(15, '103', 5, '����Ѻ��û����ԹʹѺʹع����Դ����ҡ����觡�þѲ�Ҥ�������Ǫҭ��Է�ҡ�õ�ҧ� ����ͧ��� �����駺����èѴ������ͧ��ù�෤����� ������� �����Է�ҡ������� ����㹡�û�Ժѵ�˹�ҷ���Ҫ���㹧ҹ���ҧ������ͧ', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(16, '104', 1, '����Ѻ��û����Թ��Ժѵ�˹�ҷ����¤��������ѵ���ب�Ե ������͡��Ժѵ� �١��ͧ��駵����ѡ������ ���¸��� �������º�Թ��', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(17, '104', 2, '����Ѻ��û����Թ�ѡ���Ҩ� ���Ѩ�����Ͷ���� �ٴ���ҧ�÷����ҧ��� ���Դ��͹��ҧ���¡�����鵹�ͧ���ͤ����� ������ѡ', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(18, '104', 3, '����Ѻ��û����Թ�ִ������ѡ��� ����Һ�ó����ԪҪվ ��Ш���Ң���Ҫ��� ��������§ູ����ͤ�� ���ͼŻ���ª����ǹ��', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(19, '104', 4, '����Ѻ��û����Թ�׹��Ѵ���ͤ����١��ͧ ��С��ҵѴ�Թ� ����觾Էѡ��Ż���ª��ͧ�ҧ�Ҫ��� ��������ʶҹ��ó����Ҩ���ҧ�����Ӻҡ���� ���������Ѿ���Ҩ���ҧ�ѵ�����͡�ͤ������֧����������������Ǣ�ͧ�������»���ª��', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(20, '104', 5, '����Ѻ��û����Թ�ط�ȵ����ͤ����صԸ��� ����׹��Ѵ�Էѡ��Ż���ª����Ъ������§�ͧ����Ȫҵ�����ѡ ����ʶҹ��ó����Ҩ����§��ͤ�����蹤�㹵��˹�˹�ҷ���çҹ �����Ҩ����§��µ�ͪ��Ե', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(21, '105', 1, '����Ѻ��û����Թ��˹�ҷ��ͧ���ͧ㹰ҹ���ǹ˹�觢ͧ����ҹ�������� ���ʹѺʹع�����ŷ���繻���ª��㹡�÷ӧҹ�����ҹ�������͹�����ҹ', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(22, '105', 2, '����Ѻ��û����Թ��������������� ���������������� ��Ф������������㹡�÷ӧҹ�ͧ���͹�����ҹ�������ҧ��', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(23, '105', 3, '����Ѻ��û����Թ����ҹ��������������ѹ��Ҿ�ѹ��㹷�� ����ʹѺʹع��÷ӧҹ�����ѹ����ջ���Է���Ҿ��觢�� ����֧������Ѻ�ѧ������蹻����Ť�����繢ͧ��Ҫԡ㹷�����ͻ�Сͺ��õѴ�Թ������ҧἹ�����ѹ�˹��§ҹ', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(24, '105', 4, '����Ѻ��û����Թ�ѡ���Ե��Ҿ ����Ǫ�蹪������ѧ����͹�����ҹ ����ʴ�������˵��ԡĵ� ���ͪ�����������ҹ��Ҿ����ͧ˹��§ҹ�����ʺ��������� ������ͧ�������ͧ��', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(25, '105', 5, '����Ѻ��û����Թ�ӷ����黯Ժѵ���áԨ����������� ����������ҧ�������Ѥ��㹷�� ����ҹ����ѹ�� ������ҧ��ѭ���ѧ����㹷��', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(26, '201', 1, '����Ѻ��û����Թ���Թ��û�Ъ�������仵������º ���� �ѵ�ػ��ʧ�� ������� ��ʹ������駢�����ä�������µ�ʹ�����������Ǣ�ͧ��Һ���ҧ��������', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(27, '201', 2, '����Ѻ��û����Թ�繼��ӷ�����������÷ӧҹ�ͧ��������ӧҹ�����ҧ�������Է���Ҿ������ӹҨ���ҧ�صԸ���', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(28, '201', 3, '����Ѻ��û����Թ�繷���֡�� ����ô��� ��������ͷ���ҹ���ҧ�����������ö ��л���ͧ�������§�ͧ����ҹ���˹��§ҹ', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(29, '201', 4, '����Ѻ��û����Թ��оĵԵ����Ѻ�繼��ӷ��� ����ִ��ѡ������Ժ��  (Good Governance) (�ԵԸ��� �س���� ����� ��������ǹ���� �����Ѻ�Դ�ͺ �����������) 㹡�û���ͧ�����ѧ�Ѻ�ѭ��', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(30, '201', 5, '����Ѻ��û����Թ����ö���㨤� ���ҧ�ç�ѹ���� ��йӾҷ���ҹ����������ѹ��Ԩ������Ǣͧͧ���', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(31, '202', 1, '����Ѻ��û����Թ��������������·�ȹ�ͧͧ��� ����������ö͸Ժ������������������ҧҹ��������������Ǣ�ͧ���͵ͺʹͧ�������·�ȹ�ͧͧ������ҧ��', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(32, '202', 2, '����Ѻ��û����Թ͸Ժ������������������������·�ȹ�ͧͧ��� �������š����¹������ ����Ѻ�ѧ�����Դ��繢ͧ���������ͻ�Сͺ��á�˹�����·�ȹ�', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(33, '202', 3, '����Ѻ��û����Թ���ҧ�ç�٧����������Դ�������㨷��л�Ժѵ�˹�ҷ��������·�ȹ� ���������ӻ�֡���й�����Ҫԡ㹷���֧�Ƿҧ㹡�÷ӧҹ���ִ�������·�ȹ����������¢ͧͧ������Ӥѭ', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(34, '202', 4, '����Ѻ��û����Թ��˹���º������ʹ���ͧ�Ѻ����·�ȹ�ͧͧ��� ���͵ͺʹͧ��͡�ù�����·�ȹ�������������', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(35, '202', 5, '����Ѻ��û����Թ��˹�����·�ȹ� ������� ��з�ȷҧ㹡�û�Ժѵ�˹�ҷ��ͧͧ��������������·�ȹ��дѺ�����', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(36, '203', 1, '����Ѻ��û����Թ��� ������㨹�º�� ��������áԨ�Ҥ�Ѱ ����դ���������§�Ѻ˹�ҷ������Ѻ�Դ�ͺ�ͧ˹��§ҹ���ҧ�� �������ö��������ѭ�� �ػ��ä�����͡�ʢͧ˹��§ҹ��', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(37, '203', 2, '����Ѻ��û����Թ�ӻ��ʺ��ó��һ���ء����㹡�á�˹����ط��ͧ˹��§ҹ��赹�����Ѻ�Դ�ͺ����ʹ���ͧ�Ѻ���ط���Ҥ�Ѱ��', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(38, '203', 3, '����Ѻ��û����Թ����ء�����ɮ� �Ƿҧ��ԺѵԷ����ʺ��������� (Best Practice) �š���Ԩ�µ�ҧ� �����ǤԴ�Ѻ��͹����㹡�á�˹����ط��ͧ˹��§ҹ��赹�����Ѻ�Դ�ͺ��', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(39, '203', 4, '����Ѻ��û����Թ��˹����ط�����ʹ���ͧ�Ѻʶҹ��ó��ҧ� ����Դ��� �»����Թ����ѧ������ʶҹ��ó� ����� ���ͻѭ�ҷҧ���ɰ�Ԩ �ѧ�� ������ͧ���㹻�������ͧ͢�š���ͧ�Ҿ��ѡɳ�ͧ����� ������㹡�á�˹����ط���Ҥ�Ѱ����ͧ���������ؾѹ��Ԩ�ͧͧ�����', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(40, '203', 5, '����Ѻ��û����Թ������� ���ҧ��ä� ��к�óҡ��ͧ������������� 㹡�á�˹����ط���Ҥ�Ѱ �¾Ԩ�óҨҡ��Ժ���Ҿ��� ��ʹ����Ѻ����¹��ȷҧ ��С��ط��ͧ��� ���;Ѳ�һ�������ҧ������ͧ', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(41, '204', 1, '����Ѻ��û����Թ��繤������� ���� �������Ѻ�֧��û�Ѻ����¹ �����駻�Ѻ�ĵԡ�������Ἱ��÷ӧҹ����ʹ���ͧ�Ѻ��û�Ѻ����¹����', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(42, '204', 2, '����Ѻ��û����Թ��������������������㨶֧����������л���ª��ͧ��û�Ѻ����¹�����Դ��� ���������ʹ����Ըա���������ǹ����㹡�û�Ѻ����¹��������¹�ŧ�ѧ�����', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(43, '204', 3, '����Ѻ��û����Թ��е�� ������ҧ�ç�٧�����������繤����Ӥѭ�ͧ��û�Ѻ����¹ ��������Դ���������ç����� �������鹡�����ҧ������������Դ����������ѧ�������Ѻ�������¹�ŧ���', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(44, '204', 4, '����Ѻ��û����Թ�ҧἹ�ҹ���ҧ���к� �����ͧ�Ѻ��û�Ѻ����¹�ͧ��� ��еԴ�����ú����á������¹�ŧ���ҧ��������', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(45, '204', 5, '����Ѻ��û����Թ��ѡ�ѹ����Դ��û�Ѻ����¹���ҧ�ջ���Է���Ҿ��л��ʺ��������� ���������ҧ��ѭ���ѧ� ��Ф����������㹡�âѺ����͹����Դ��û�Ѻ����¹���ҧ�ջ���Է���Ҿ', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(46, '205', 1, '����Ѻ��û����Թ����ʴ��ĵԡ������������Ҿ��������������㹷ءʶҹ��ó�', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(47, '205', 2, '����Ѻ��û����Թ�����ҷѹ������ͧ���ͧ ��ФǺ��������������ʶҹ��ó������ҧ������� ���Ҩ��ա����§�ҡʶҹ��ó�������§��͡���Դ�����ع�ç��� �����Ҩ����¹��Ǣ��ʹ��� ������ش�ѡ���Ǥ�������ʧ�ʵ�������', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(48, '205', 3, '����Ѻ��û����Թ����֡��֧�����ع�ç�ҧ������������ҧ���ʹ��� ���͡�û�Ժѵԧҹ �������ö����·��Ҩ� �Ըա���ʴ��͡���������� ���ͻ�Ժѵԧҹ���������ҧʧ� �����������Դ����ԧź��駵�͵��ͧ��м�����', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(49, '205', 4, '����Ѻ��û����Թ�����èѴ��������� �������´ ���ͼŷ���Ҩ�Դ��鹨ҡ���С��ѹ�ҧ�����������ҧ�ջ���Է���Ҿ', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(50, '205', 5, '����Ѻ��û����Թ��Ҫ���������ع�ç���¤������������䢷����˵آͧ�ѭ�� �����駺�Ժ���лѨ����Ǵ������ҧ� ��ʹ������ö�Ǻ���������ͧ���ͧ ����֧����餹���� ����������ʧ�ŧ�� ����ʶҹ��ó���֧���´�ҡ', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(51, '206', 1, '����Ѻ��û����Թ�͹�ҹ �����й����ҧ�����´ �����ҸԵ����ǡѺ�Ըջ�Ժѵԧҹ ������㹡�þѲ�ҡ�û�Ժѵԧҹ', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(52, '206', 2, '����Ѻ��û����Թ���㨾Ѳ�Ҽ����ѧ�Ѻ�ѭ��������ѡ��Ҿ �����ӻ�֡�Ҫ�����Ƿҧ㹡�þѲ���������������ʹ���л�Ѻ��ا��ʹ������Ŵŧ �������ҧ��������㹡�û�Ժѵԧҹ', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(53, '206', 3, '����Ѻ��û����Թ�ҧἹ��������͡�ʼ����ѧ�Ѻ�ѭ���ʴ���������ö㹡�÷ӧҹ ����֧����͡�ʼ����ѧ�Ѻ�ѭ�����Ѻ��ý֡ͺ�� ���;Ѳ�����ҧ�������� ����ʹѺʹع������¹���', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(54, '206', 4, '����Ѻ��û����Թ��Ѻ����¹��ȹ��� ��Ъ�����䢻ѭ�ҷ�����ػ��ä��͡�þѲ���ѡ��Ҿ�ͧ�����ѧ�Ѻ�ѭ����', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(55, '206', 5, '����Ѻ��û����Թ���ҧ���ʹѺʹع���ͧ������к�����͹�ҹ��С���ͺ����˹�ҷ������Ѻ�Դ�ͺ���ҧ���к� ������������Ѳ�������觡�����¹������ҧ������ͧ�ͧ���', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(56, '301', 1, '����Ѻ��û����Թ���˹ѡ ��繤����Ӥѭ �������� ���ͻ���ª��ͧ��áӡѺ�Դ�����ô��Թ�ҹ��ҧ� �ͧ������ �����������蹻�ԺѵԵ���ҵðҹ ������º ���͢�ͺѧ�Ѻ����˹����', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(57, '301', 2, '����Ѻ��û����Թ��е�������㹡�áӡѺ�Դ�����ô��Թ�ҹ��ҧ� �ͧ������ �����������蹻�ԺѵԵ���ҵðҹ ������º ���͢�ͺѧ�Ѻ����˹����', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(58, '301', 3, '����Ѻ��û����Թ�ӡѺ�Դ�����ô��Թ��õ�ҧ� �ͧ���������ҧ�������� ���������� ����������ö��Ѻ��кǹ��� �����Ըա�õ�ҧ� �����������蹻�ԺѵԵ���ҵðҹ ������º ���͢�ͺѧ�Ѻ����˹�������ҧ�١��ͧ', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(59, '301', 4, '����Ѻ��û����Թ�ӡѺ�Դ��� ��е�Ǩ�ͺ�����١��ͧ��ô��Թ�ҹ��ҧ� �ͧ������㹷ء��鹵͹���ҧ�����´ ������Դ �������͡����͹ �����觡������Ѻ��ا��ô��Թ�ҹ��ҧ� ��ԧ����ҳ��Фس�Ҿ���١��ͧ�ҵðҹ ������º ���͢�ͺѧ�Ѻ����˹�������ҧ�١��ͧ', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(60, '301', 5, '����Ѻ��û����Թ���Թ��� ��ШѴ������ҧ�ç仵ç�� �������Ը�༪ԭ˹�����ҧ�索Ҵ����ͼ���������˹��§ҹ������áӡѺ�����ա�ô��Թ�ҹ��ҧ� ������� ���١��ͧ ���ͷӼԴ���������ҧ�����ç �����駡�˹� ��л�Ѻ�ҵðҹ ��ͺѧ�Ѻ ��С�����º��ҧ� �������Ǣ�ͧ���ᵡ��ҧ ��ҷ�� �����٧��� ��������������ؤ�ҡ��Դ��þѲ�Ҵ�ҹ��áӡѺ�Դ���', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(61, '302', 1, '����Ѻ��û����Թ��Ժѵ�˹�ҷ����¤��������  �١��ͧ��駵���ҵðҹ ��ѡ�������������º��ͺѧ�Ѻ����˹����', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(62, '302', 2, '����Ѻ��û����Թ���ҷ��л���ʸ������¡��ͧ/����ʹͷ��Դ������º �����������蹻�Ժѵ�˹�ҷ�������Դ��͹�����ҧ���¡�����鵹�ͧ���ͼ����蹷�����ѡ', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(63, '302', 3, '����Ѻ��û����Թ���蹡ӡѺ�Ǻ�����Ǩ��ҡ�ô��Թ��âͧ�ؤ������˹��§ҹ�������Ѻ�Դ�ͺ���������ҧ�١��ͧ���������º�����Ƿҧ��º�·���ҧ���', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(64, '302', 4, '����Ѻ��û����Թ����༪ԭ˹����е���ͧ���ؤ������˹��§ҹ����ҽ׹��ࡳ�� ����º ��º�������ҵðҹ��������任�Ѻ��ا��� �������Ҩ�С������Դ�ѵ�����ͤ������֧����������������Ǣ�ͧ', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(65, '302', 5, '����Ѻ��û����Թ�׹��Ѵ�Էѡ��Ż���ª������ࡳ����С�����º�ͧͧ��� ����ʶҹ��ó����Ҩ����§��ͤ�����蹤�㹵��˹�˹�ҷ���çҹ �����Ҩ����§��µ�ͪ��Ե', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(66, '303', 1, '����Ѻ��û����Թ���㨹�º�� ���ط��ͧ��з�ǧ�ص��ˡ��� ��� ���. �������ö�Ӥ������㨹������������ѭ�� �ػ��ä�����͡�ʢͧͧ�������˹��§ҹ�͡�繻��������� ��', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(67, '303', 2, '����Ѻ��û����Թ����ء��������� �ٻẺ���ͻ��ʺ��ó��������ʹ������Ƿҧ��ҧ� 㹧ҹ�����ҧ�ջ���Է���Ҿ�٧�ش', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(68, '303', 3, '����Ѻ��û����Թ����ء�� Best Practice ��ɮ������ǤԴ����� ���Ѻ��͹㹡�á�˹�Ἱ�ҹ���͢���ʹ͵�ҧ� �����ҧ�ջ���Է���Ҿ�٧�ش', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(69, '303', 4, '����Ѻ��û����Թ�����Թ����ѧ������ʶҹ��ó����ͻѭ�ҷҧ���ɰ�Ԩ �ѧ�� ������ͧ���㹻������е�ҧ����ȷ��Ѻ��͹ ������㹡�á�˹�Ἱ���͹�º�¢ͧ˹��§ҹ ����ͧ��������ҧ�ջ���Է���Ҿ�٧�ش', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(70, '303', 5, '����Ѻ��û����Թ��������ѭ�� ���ʶҹ��ó�����������֡����¾Ԩ�óҨҡ��Ժ�������� �ѧ�� ���ɰ�Ԩ �к��ص��ˡ�����Ҿ������Ѻ��͹ �ѹ�������û�д�ɰ�Դ�� ���ҧ��ä� ��й��ʹ�ͧ������������� �������»�ҡ��ҡ�͹����繻���ª����ͧ��� �����ѧ����л���Ȫҵ������', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(71, '304', 1, '����Ѻ��û����Թ���㨷ӧҹ���١��ͧ �����´����ǹ ������Ҵ���º����', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(72, '304', 2, '����Ѻ��û����Թ���蹵�Ǩ�ҹ�����١��ͧ�ͧ�ҹ���ҧ�����´�ͺ�ͺ�������ҹ����բ�ͼԴ��Ҵ�����դ����١��ͧ�٧�ش', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(73, '304', 3, '����Ѻ��û����Թ��Ǩ�ͺ�����١��ͧ�ͧ�ҹ�ͧ����м����蹷������㹤����Ѻ�Դ�ͺ �������ҹ����բ�ͼԴ��Ҵ�����դ����١��ͧ�٧�ش', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(74, '304', 4, '����Ѻ��û����Թ�ӡѺ�Դ�����������˹����Ф����١��ͧ��ԧ�س�Ҿ�ͧ���Ѿ��ͧ�ç�������ࡳ�����͡�˹����ҷ���ҧ������ҧ�ջ���Է���Ҿ�٧�ش', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(75, '304', 5, '����Ѻ��û����Թ���ҧ�к��������ö�ӡѺ��Ǩ�ͺ��������˹�� �����١��ͧ �س�Ҿ�ͧ�ŧҹ㹡�û�Ժѵԧҹ�ͧ������ ����˹��§ҹ����� ���������ҧ�����Ѵਹ�ͧ�����١��ͧ��Фس�Ҿ�ͧ��鹵͹��÷ӧҹ���ͼŧҹ�����ç����������´���ͤǺ����������蹻�ԺѵԵ����鹵͹��÷ӧҹ����ҧ������ҧ�١��ͧ����Դ��������ʵ�Ǩ�ͺ��', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(76, '305', 1, '����Ѻ��û����Թ��繤������� �����ǧ���͡�����ͪ�ͧ�ҧ㹡�����ͧ�������� ���й� ���Իѭ�� ��ѵ���� ���෤����յ�ҧ� �����Сͺ��� �������͢���', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(77, '305', 2, '����Ѻ��û����Թ������� ������ͧ�������� ���й� ���Իѭ�� ��ѵ���� ���෤����յ�ҧ� �����Сͺ��� �������͢��� �����������ö��任�Ѻ��л���ء��������Դ����ª����', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(78, '305', 3, '����Ѻ��û����Թŧ��鹷����������������Сͺ��� �������͢������ҧ�������� �������ö��Ѻ����¹෤�Ԥ �Ըա�� �ٻẺ����Ƿҧ㹡����������� ��С�þѲ�������ҧ�١��ͧ ������� ����ʹ���ͧ���������ͧ������ҧ���ԧ', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(79, '305', 4, '����Ѻ��û����Թ����������� ��ͨӡѴ�����͡��㹡����������Է���Ҿ㹡�ô��Թ�ҹ�ͧ����Сͺ��� �������͢��� ������ö����ء�� ��оѲ��ͧ�������� ���й� ��ѵ���� ���෤����յ�ҧ� �����ҧ�١��ͧ ������� ������������þѲ�ҡ�ô��Թ�ҹ��������', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(80, '305', 5, '����Ѻ��û����Թ��˹���º�� Ἱ�ط���ʵ�� ���ط�� ��С�ͺ��þѲ�Ҽ���Сͺ��� �������͢�����Ҿ������ʹ���ͧ�Ѻ���ǡ�ó����ɰ�Ԩ ����ص��ˡ����ͧ����� �������ö��任�Ժѵ����ԧ (Implementation)', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(81, '306', 1, '����Ѻ��û����Թ�ҧἹ�ҹ�繢�鹵͹���ҧ�Ѵਹ �ռ��Ѿ�� ��觷���ͧ�Ѵ����� ��СԨ������ҧ� �����Դ������ҧ�١��ͧ', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(82, '306', 2, '����Ѻ��û����Թ�Ѵ�ӴѺ�ͧ�ҹ��м��Ѿ����ç��� ���������������Ң�ʹ� ���������мŵ�����ͧ�ͧἹ�ҹ�ʹյ ��������ö�ҧἹ�ҹ���������ҧ�ջ���Է���Ҿ�٧�ش', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(83, '306', 3, '����Ѻ��û����Թ�ҧἹ�ҹ����դ���������§���ͫѺ��͹�ѹ����� �ҹ��������� �ç��� �����ҧ�ջ���Է���Ҿ �ʹ���ͧ ������Ѵ��駡ѹ', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(84, '306', 4, '����Ѻ��û����Թ�ҧἹ��ФҴ��ó���ǧ˹������ǡѺ�ѭ�����͡Ԩ���������˹��§ҹ���ͼ������Ǣ�ͧ���½��� ����������ҧ���͡����Ѻ��û�ͧ�ѹ/��䢻ѭ�ҷ���Դ��������ҧ�ջ���Է���Ҿ', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(85, '306', 5, '����Ѻ��û����Թ��Ѻ���ط�� Ἱ�ҹ ����ҧἹ���ҧ�Ѵ���������к������ҡѺʶҹ��ó����Դ������ҧ���Ҵ�Դ ������ѭ�� �ػ��ä �������ҧ�͡�ʹ��� ���ҧ�դس�Ҿ�٧�ش�������ö������ѵ�ػ��ʧ�� ���������·���˹�������ҧ�ջ���Է���Ҿ�٧�ش', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(86, '307', 1, '����Ѻ��û����Թ��Ժѵԧҹ�¤ӹ֧�֧�������������Ф������·���Դ���', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(87, '307', 2, '����Ѻ��û����ԹŴ�������µ�ҧ� �����Դ��� ��ШѴ��ç�����ҳ �������� ���ͷ�Ѿ�ҡ÷�����������ҧ�ӡѴ�������������Դ����ª��㹡�û�Ժѵԧҹ���ҧ�٧�ش', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(88, '307', 3, '����Ѻ��û����Թ�����Թ�Ť����ջ���Է���Ҿ�ͧ��ô��Թ�ҹ����ҹ�����ͻ�Ѻ��ا��èѴ��÷�Ѿ�ҡ������ż�Ե���������� �����ա�÷ӧҹ����ջ���Է���Ҿ�ҡ��� �����դ������·��Ŵŧ', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(89, '307', 4, '����Ѻ��û����Թ�ҧἹ���������§��áԨ�ͧ˹��§ҹ���ͧ�Ѻ˹��§ҹ��� (Synergy) ������������Ѿ�ҡâͧ˹��§ҹ�������Ǣ�ͧ�������Դ����ª���٧�ش', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(90, '307', 5, '����Ѻ��û����Թ�Ѳ�ҡ�кǹ�������� �����������·�ȹ� ��������Ǫҭ ��л��ʺ��ó��һ���ء��㹡�÷ӧҹ ����Ŵ���С�ú����çҹ�����ҧ�ջ���Է���Ҿ�٧�ش ��������ż�Ե�������ҧ��ä�ҹ������ⴴ��ᵡ��ҧ���Ѻͧ��� �����Ѿ�ҡ�������', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(91, '308', 1, '����Ѻ��û����Թ�դ������㨤������·����Դ���������� �������ö��Ѻ��÷ӧҹ�����ͧ�������ʹ���ͧ�Ѻ������ͧ�����', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(92, '308', 2, '����Ѻ��û����Թ���� ����Ѻ ������㨤����Դ��繢ͧ�����蹷����ԧ��������й���ԧ������ �����ʶҹ��ó��Ѻ����¹� �����������ö�ӧҹ����������·���˹�������ҧ�ջ���Է���Ҿ', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(93, '308', 3, '����Ѻ��û����Թ�դ����������ͧ�֡���������ʴ��͡���ҧ�Ѵਹ�ͧ�ؤ������ʶҹ��ó����Դ��� ���ǻ�Ѻ�������ʹ���ͧ �����������Ѻ�Ѻ���кؤ������ʶҹ��ó�ѧ����������ҧ�ջ���Է���Ҿ', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(94, '308', 4, '����Ѻ��û����Թ�����������ԧ�֡��ͺؤ������ʶҹ��ó��һ�Ѻ����¹�Ըա�ô��Թ�ҹ�˹��§ҹ���������ķ������ջ���Է���Ҿ�٧�ش', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(95, '308', 5, '����Ѻ��û����Թ��ѺἹ���ط������� ���������������Ѻʶҹ��ó�੾��˹�� �����駻���ء����ѡ�Ե�Է��㹡����������㨼������ʶҹ��ó��ҧ� �����繾�鹰ҹ㹡���èҷӤ������� ���ʹ��Թ�ҹ���������áԨ�ͧͧ���', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(96, '309', 1, '����Ѻ��û����Թ����෤����� �к� ��кǹ��÷ӧҹ ������º����ҵðҹ�˹��§ҹ����ѧ�Ѵ', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(97, '309', 2, '����Ѻ��û����Թ�������������§෤����� �к� ��кǹ��÷ӧҹ �˹��§ҹ������ö���һ�Ѻ����������÷ӧҹ������ҧ�ջ���Է���Ҿ', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
	
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(98, '309', 3, '����Ѻ��û����Թ�ͧ�Ҿ��� ������㨢�ͨӡѴ�ͧ෤����� �к����͡�кǹ��÷ӧҹ�˹��§ҹ �����ʹ͡�û�Ѻ����¹���ͻ�Ѻ��ا����÷ӧҹ������ҧ�ջ���Է���Ҿ�٧���', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(99, '309', 4, '����Ѻ��û����Թ���㨡������¹͡�Ѻ�š�з���������෤����� �к����͡�кǹ��÷ӧҹ�ͧ˹��§ҹ ���ǹ����ҧἹ��ФҴ��ó���ǧ˹������������ҧ���͡����Ѻ��û�ͧ�ѹ/��䢻ѭ�ҷ���Դ���', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(100, '309', 5, '����Ѻ��û����Թ����ʶҹТͧ�к� ෤����� ��С�кǹ��á�÷ӧҹ�ͧͧ������ҧ��ͧ�� ��С�˹�������ͧ������ʹ��Թ�������¹�ŧ��Ҿ��� �������ͧ����Ժ����ҧ����׹', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(101, '310', 1, '����Ѻ��û����Թ���ʹ͢����� ͸Ժ�� ���ͪ��ᨧ��������´����ѧ���ҧ�ç仵ç�����ԧ�����ŷ����������', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(102, '310', 2, '����Ѻ��û����Թ�ѡ���������ù��ʹ͢����������ҧ�� ���������������è�����������¡��ѡ�������˵ؼŷ������Ǣ�ͧ�һ�Сͺ��ù��ʹ����ҧ�բ�鹵͹', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(103, '310', 3, '����Ѻ��û����Թ����ء����������� ����ʹ㨢ͧ���ѧ����繻���ª����è��ʹ͢����� ���ʹ������è��¤Ҵ��ó�֧��ԡ����� �š�з������յ�ͼ��ѧ����ѡ', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(104, '310', 4, '����Ѻ��û����Թ�ҧ���ط�������ѡ��㹡���������㨷ҧ���� �������������ķ���ѧ���ʧ���¤ӹ֧�֧�š�з���Ф�������֡�ͧ���������Ӥѭ', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(105, '310', 5, '����Ѻ��û����Թ���ҧ����������� ��л���ء������ѡ�Ե�Է����Ū����ͨԵ�Է�ҡ��������繻���ª��㹡���è�������Ǩ٧�����չ��˹ѡ ���ķ���������觢�� ����ջ���Է���Ҿ�٧�ش', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(106, '311', 1, '����Ѻ��û����Թ�������ʹѺʹع�����Դ���ҧ��ä� ��������ͧ�Ը����� �����ҷ�᷹�Ըա�÷�����������㹡�û�Ժѵԧҹ���ҧ�������������', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(107, '311', 2, '����Ѻ��û����Թ�������ҧ��ä���л�Ѻ��ا��кǹ��÷ӧҹ�ͧ�����ҧ��������', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(108, '311', 3, '����Ѻ��û����Թ�Դ�͡��ͺ���ͻ�Ѻ����¹��÷ӧҹ ��й��ʹͷҧ���͡ (Option) �����Ƿҧ��ѭ�� (Solution) 㹧ҹ�ͧ�����ҧ���ҧ��ä�', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(109, '311', 4, '����Ѻ��û����Թ����������ҧ��ä��������� �����Ƿҧ����� 㹡�û�Ժѵԧҹ���ʹ��Թ��õ�ҧ� ���ͧ������ҧ�ջ���Է���Ҿ ����դس�Ҿ�٧���', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(110, '311', 5, '����Ѻ��û����Թ���ҧ��ѵ������к��ص��ˡ����ͧ����� ��ʹ��ͧ������������������»�ҡ��ҡ�͹����繻���ª�����к��ص��ˡ��������ѧ����л���Ȫҵ������', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(111, '312', 1, '����Ѻ��û����Թ�ͺʹͧ ������ѭ�������ҧ�Ǵ���� ������������˵��ԡĵ� ����ʶҹ��ó����', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
	
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(112, '312', 2, '����Ѻ��û����Թ���ѡ��ԡ�ŧ�Ըա�� ��кǹ��õ�ҧ� �����������ö��䢻ѭ�� ���������ª��ҡ�͡�ʹ�������ҧ�ջ���Է���Ҿ�٧�ش', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(113, '312', 3, '����Ѻ��û����Թ�Ҵ��ó���������繻ѭ�������͡�ʷ���Ҩ�Դ������������ 1-3 ��͹�Ѵ�ҡ�Ѩ�غѹ ���ŧ��͡�зӡ����ǧ˹�����ͻ�ͧ�ѹ�ѭ�� �������ҧ�͡���ʶҹ��ó���� �����ҧ�ջ���Է���Ҿ�٧�ش', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(114, '312', 4, '����Ѻ��û����Թ�Ҵ��ó���������繻ѭ�������͡�ʷ���Ҩ�Դ������������ 4-12 ��͹�Ѵ�ҡ�Ѩ�غѹ ������������������Ըա�������ǤԴ����� ��ǧ˹�� ����Ҩ���繻���ª��㹡�û�ͧ�ѹ�ѭ��������ҧ�͡���͹Ҥ�', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(115, '312', 5, '����Ѻ��û����Թ�Ҵ��ó���������繻ѭ�������͡�ʷ���Ҩ�Դ������������������������ǧ˹�����ͻ�ͧ�ѹ�ѭ�� �������ҧ�͡�� �ա��駡�е�����������Դ������е������鹵�͡�û�ͧ�ѹ�����䢻ѭ���������ҧ�͡�����ͧ�����������', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(116, '313', 1, '����Ѻ��û����Թ�Ң����������ػ�Ţ����������ʴ��Ţ�������ٻẺ��ҧ� �� ��ҿ ��§ҹ �����ҧ�١��ͧ��Фú��ǹ', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(117, '313', 2, '����Ѻ��û����Թ�׺���лѭ������ʶҹ��ó����ҧ�֡��� �����ҫ�������ͻ���繢ͧ������ �������ö���ҨѴ��� �������� ��л����Թ������Դ����ª����˹��§ҹ��', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(118, '313', 3, '����Ѻ��û����Թ���蹤��Ң���������֡���ҧ������ͧ ����������㨶֧����ͧ ��ȹ� �鹵ͧ͢�ѭ�� �����͡�ʷ���͹�����������ͧ�֡ ������ö���ҨѴ��� �������� ��л����Թ������Դ����ª���٧�ش��˹��§ҹ��', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(119, '313', 4, '����Ѻ��û����Թ�Ѵ�ӡ���Ԩ�����ҧ���к�������仵����ѡ��÷ҧʶԵ� ��йӼŷ�������Ҿ�ҡó��������ҧẺ���ͧ�������ҧ�к�����Դ����ª���٧�ش���˹��§ҹ��', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(120, '313', 5, '����Ѻ��û����Թ�ҧ�к�����׺�� ��������բ����ŷ��ѹ�˵ء�ó��͹��������ҧ������ͧ�������ö�͡Ẻ ���͡�� ���ͻ���ء���Ըա��㹡�èѴ��Ẻ���ͧ�����к���ҧ� �����ҧ�١��ͧ����չ���Ӥѭ', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(121, '314', 1, '����Ѻ��û����Թ�ҧἹ�ҹ����鹰ҹ�ͧ��������෤����� �к� ��кǹ��÷ӧҹ����ҵðҹ㹧ҹ', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(122, '314', 2, '����Ѻ��û����Թ���������ʹ� ���������мŵ�����ͧ�ͧ෤����� �к� ��С�кǹ��÷ӧҹ��ҧ� �ͧ˹��§ҹ�����һ�Сͺ����ҧἹ�Ѵ�ӴѺ�����Ӥѭ��м��Ѿ��ͧ�ҹ�����ҧ�ջ���Է���Ҿ�٧�ش', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(123, '314', 3, '����Ѻ��û����Թ�ͧ�Ҿ��������ҧἹ����������§�ҹ���͡Ԩ������ҧ� ����դ����Ѻ��͹ ����������ص��������·��˹��§ҹ��˹���������ҧ�ջ���Է���Ҿ�٧�ش', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(124, '314', 4, '����Ѻ��û����Թ���㨡������¹͡�Ѻ�š�з���������෤����� �к����͡�кǹ��÷ӧҹ�ͧ˹��§ҹ ���ǹ����ҧἹ��ФҴ��ó���ǧ˹������������ҧ���͡����Ѻ��û�ͧ�ѹ/��䢻ѭ�ҷ���Դ���', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(125, '314', 5, '����Ѻ��û����Թ����ʶҹТͧ�к� ෤����� ��С�кǹ��á�÷ӧҹ�ͧ ���. ��С�з�ǧ�ص��ˡ������ҧ��ͧ�� ������ö��Ѻ���ط������ҧἹ���ҧ�Ѵ��� ���ʹ��Թ�������¹�ŧ��Ҿ������ͧ��� ��С�з�ǧ� �Ժ����ҧ����׹', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(126, '315', 1, '����Ѻ��û����Թ�Դ����Ҥ����������ǤԴ����� �����ԪҪվ ��������������ͻ��ʺ��ó�㹡���������� ��С��ŧ�����䢻ѭ���������鹷���Դ���', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(127, '315', 2, '����Ѻ��û����Թ�������� ��ԡ�ŧ ����ء�� ��еѴ�Թ����ҧ�բ���������˵ؼ�㹡�èѴ��ûѭ�ҷ���Դ��������ҧ�ջ���Է���Ҿ', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(128, '315', 3, '����Ѻ��û����Թ��������ѭ�ҷ���ҹ�� �Ҵ��ó�š�з������Դ��� ����ҧἹ��ǧ˹�����ҧ���к� ���ͻ�ͧ�ѹ������ա����§�ѭ���˹��§ҹ ���� ���.', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(129, '315', 4, '����Ѻ��û����Թ�������� ��м����ҹ (Integrate) �ǤԴ��ԧ���Է�ҡ�� ������ա����§ ��ͧ�ѹ ������䢻ѭ�ҷ���դ����Ѻ��͹������������������������ҧ�ջ���Է���Ҿ', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(130, '315', 5, '����Ѻ��û����Թ��Ѻ����¹ͧ�������ա�ú�óҡ����ԧ�ԪҪվ������ҧ����դ�������Ǫҭ�����Ҫվ���ҧ���ԧ ��������ö��� ��ͧ�ѹ�����ա����§�ѭ�ҷ���ռš�з��٧�����դ����Ѻ��͹�٧�ͧͧ��������ҧ����׹ �������繼��ӷ���դ�������Ǫҭ�����Ҫվ�������ö��ͧ�ѹ �����ա����§�ѭ�ҷ���ռš�з��ԧ��º�� ��С��ط��ͧͧ�������Դ����ª�����ҧ����׹��ͧ�����������', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$level = array(	"O1", "O2", "O3", "O4", "K1", "K2", "K3", "K4", "K5", "D1", "D2", "M1", "M2" );
		$code = array(	"101", "102", "103", "104", "105", "301", "302", "303", "304", "305", "306", "307", "308", "309", "310", "311", "312", 
										"313", "314", "315" );
		$target = array(	1, 1, 2, 3, 1, 2, 3, 4, 5, 3, 4, 4, 5 );
		for ( $i=0; $i<count($level); $i++ ) { 
			for ( $j=0; $j<count($code); $j++ ) { 
				if ($level[$i]=="O1" && substr($code[$j],0,1)=="3") $TARGET_LEVEL = 0; else $TARGET_LEVEL = $target[$i]; 
				$cmd = " INSERT INTO PER_STANDARD_COMPETENCE (LEVEL_NO, CP_CODE, TARGET_LEVEL, UPDATE_USER, 
								UPDATE_DATE)
								VALUES ('$level[$i]', '$code[$j]', $TARGET_LEVEL,  $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			} // end for
		} // end for
		
		$level = array(	"D1", "D2", "M1", "M2" );
		$code = array(	"201", "202", "203", "204", "205", "206" );
		$target = array(	1, 2, 3, 4 );
		for ( $i=0; $i<count($level); $i++ ) { 
			for ( $j=0; $j<count($code); $j++ ) { 
				$cmd = " INSERT INTO PER_STANDARD_COMPETENCE (LEVEL_NO, CP_CODE, TARGET_LEVEL, UPDATE_USER, 
								UPDATE_DATE)
								VALUES ('$level[$i]', '$code[$j]', $target[$i],  $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			} // end for
		} // end for
		
		$code = array(	"520412 ��Ҿ�ѡ�ҹ����Թ��кѭ��", 
										"523612 ��Ҿ�ѡ�ҹ��Ѿ�ҡøó�", 
										"511612 ��Ҿ�ѡ�ҹ��á��", 
										"511712 ��Ҿ�ѡ�ҹ��ʴ�", 
										"550212 ��Ҿ�ѡ�ҹ�Է����ʵ��", 
										"512212 ��Ҿ�ѡ�ҹʶԵ�", 
										"522003 ���ɰ��", 
										"511104 �ѡ�Ѵ��çҹ�����", 
										"510903 �ѡ��Ѿ�ҡúؤ��", 
										"551303 �ѡ�ó��Է��", 
										"510703 �ѡ���������º�����Ἱ", 
										"520423 �ѡ�Ԫҡ���Թ��кѭ��", 
										"532423 �ѡ�Ԫҡ�������", 
										"532523 �ѡ�Ԫҡ���ʵ��ȹ�֡��", 
										"511013 �ѡ�Ԫҡ�ä���������", 
										"520603 �ѡ�Ԫҡ�õ�Ǩ�ͺ����", 
										"523623 �ѡ�Ԫҡ�÷�Ѿ�ҡøó�", 
										"511723 �ѡ�Ԫҡ�þ�ʴ�", 
										"512003 �ѡ�Ԫҡ��ʶԵ�", 
										"584003 �ѡ�Ԫҡ������Ǵ����", 
										"523203 �ѡ�Ԫҡ���ص��ˡ���", 
										"550103 �ѡ�Է����ʵ��", 
										"512603 �ѡ�׺�ǹ�ͺ�ǹ", 
										"574312 ��ª�ҧ��¹Ẻ", 
										"571912 ��ª�ҧ����ͧ��", 
										"573712 ��ª�ҧ����ͧ���", 
										"571412 ��ª�ҧ�¸�", 
										"572412 ��ª�ҧ����", 
										"573012 ��ª�ҧ俿��", 
										"571512 ��ª�ҧ�ѧ�Ѵ", 
										"512403 �Եԡ�", 
										"510308 ����ӹ�¡��",
										"570103 ���ǡ�", 
										"570403 ���ǡ�����ͧ��", 
										"570603 ���ǡ�����ͧ���", 
										"570203 ���ǡ��¸�", 
										"570803 ���ǡ���ˡ��", 
										"570503 ���ǡ�俿��" ); 
												
		for ( $i=0; $i<count($code); $i++ ) { 
			if ($i == 0) $desc = array(	"302", "304", "309" ); 
			elseif ($i == 1) $desc = array(	"302", "304", "309", "312", "313" );
			elseif ($i == 2) $desc = array(	"302", "304", "309", "312" );
			elseif ($i == 3) $desc = array(	"304", "307", "309" );
			elseif ($i == 4) $desc = array(	"303", "304", "305", "309", "312", "313", "315" );
			elseif ($i == 5) $desc = array(	"303", "309", "313" );
			elseif ($i == 6) $desc = array(	"303", "313", "314" );
			elseif ($i == 7) $desc = array(	"301", "302", "303", "304", "305", "312", "314", "315" );
			elseif ($i == 8) $desc = array(	"304", "308", "314" );
			elseif ($i == 9) $desc = array(	"302", "303", "304", "305", "313", "314", "315" );
			elseif ($i == 10) $desc = array(	"303", "304", "312", "313", "314" );
			elseif ($i == 11) $desc = array(	"302", "304", "314" );
			elseif ($i == 12) $desc = array(	"304", "310", "311" );
			elseif ($i == 13) $desc = array(	"304", "310", "311" );
			elseif ($i == 14) $desc = array(	"303", "313", "314" );
			elseif ($i == 15) $desc = array(	"302", "303", "304", "313", "314" );
			elseif ($i == 16) $desc = array(	"302", "303", "304", "314" );
			elseif ($i == 17) $desc = array(	"304", "307", "309" );
			elseif ($i == 18) $desc = array(	"303", "313", "314" );
			elseif ($i == 19) $desc = array(	"301", "303", "305", "306", "311", "312", "313", "314", "315" );
			elseif ($i == 20) $desc = array(	"302", "303", "304", "313", "314", "315" );
			elseif ($i == 21) $desc = array(	"301", "303", "304", "305", "312", "313", "314", "315" );
			elseif ($i == 22) $desc = array(	"302", "304", "313" );
			elseif ($i == 23) $desc = array(	"303", "304", "305", "309", "313" );
			elseif ($i == 24) $desc = array(	"303", "304", "306", "309", "311", "312", "315" );
			elseif ($i == 25) $desc = array(	"301", "304", "305", "309", "312", "313", "315" );
			elseif ($i == 26) $desc = array(	"306", "311", "315" );
			elseif ($i == 27) $desc = array(	"303", "304", "309" );
			elseif ($i == 28) $desc = array(	"303", "304", "305", "309", "314", "315" );
			elseif ($i == 29) $desc = array(	"301", "304", "305", "309", "312", "313", "315" );
			elseif ($i == 30) $desc = array(	"302", "304", "313", "314", "315" );
			elseif ($i == 31) $desc = array(	"201", "202", "203", "204", "205", "206", "301", "302", "303", "304", "305", "312", "313", "314", "315" );
			elseif ($i == 32) $desc = array(	"303", "304", "314", "315" );
			elseif ($i == 33) $desc = array(	"303", "313", "314", "315" );
			elseif ($i == 34) $desc = array(	"301", "302", "303", "304", "305", "313", "314", "315" );
			elseif ($i == 35) $desc = array(	"303", "313", "315" );
			elseif ($i == 36) $desc = array(	"303", "305", "313", "314", "315" );
			elseif ($i == 37) $desc = array(	"303", "304", "314" );

			for ( $j=0; $j<count($code); $j++ ) { 
				$PL_CODE = substr($code[$i],0,6);
				$cmd = " INSERT INTO PER_LINE_COMPETENCE (PL_CODE, CP_CODE, LC_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES ('$PL_CODE', '$desc[$j]', 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			} // end for
		}

		$cmd = " SELECT DISTINCT PL_CODE, a.ORG_ID FROM PER_POSITION a, PER_PERSONAL b 
						WHERE a.POS_ID = b.POS_ID AND PER_STATUS = 1 AND a.PL_CODE <> '510108' ORDER BY PL_CODE, a.ORG_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		while($data = $db_dpis->get_array()){
			$PL_CODE = trim($data[PL_CODE]);
			$ORG_ID = $data[ORG_ID];
			$code = "";
			if ($PL_CODE=="510308") { // ����ӹ�¡��
				if ($ORG_ID==9489) $code = array(	"303", "304", "312" ); // �ӹѡ�����á�ҧ
				elseif ($ORG_ID==9492) $code = array(	"303", "305", "315" ); // �ӹѡ�ҹ�ص��ˡ�����鹰ҹ��С������ͧ���ࢵ 1
				elseif ($ORG_ID==9493) $code = array(	"303", "305", "315" ); // �ӹѡ�ҹ�ص��ˡ�����鹰ҹ��С������ͧ���ࢵ 2
				elseif ($ORG_ID==9494) $code = array(	"303", "305", "315" ); // �ӹѡ�ҹ�ص��ˡ�����鹰ҹ��С������ͧ���ࢵ 3
				elseif ($ORG_ID==9495) $code = array(	"301", "305", "315" ); // �ӹѡ����������Ǵ����
				elseif ($ORG_ID==9496) $code = array(	"303", "314", "315" ); // �ӹѡ����ͧ�����������ҹ
				elseif ($ORG_ID==9497) $code = array(	"303", "314", "315" ); // �ӹѡ�ص��ˡ�����鹰ҹ
				elseif ($ORG_ID==9802) $code = array(	"303", "313", "316" ); // �ӹѡ�������ط���ʵ��
				elseif ($ORG_ID==12970) $code = array(	"303", "305", "313" ); // �ӹѡ�Ũ�ʵԡ��
			} elseif ($PL_CODE=="510703") { // �ѡ���������º�����Ἱ
				if ($ORG_ID==9802) $code = array(	"303", "313", "314" ); // �ӹѡ�������ط���ʵ��
			} elseif ($PL_CODE=="510903") { // �ѡ��Ѿ�ҡúؤ��
				if ($ORG_ID==9489) $code = array(	"304", "308", "314" ); // �ӹѡ�����á�ҧ
			} elseif ($PL_CODE=="511013") { // �ѡ�Ԫҡ�ä���������
				if ($ORG_ID==9802) $code = array(	"303", "313", "314" ); // �ӹѡ�������ط���ʵ��
			} elseif ($PL_CODE=="511104") { // �ѡ�Ѵ��çҹ�����
				if ($ORG_ID==9489) $code = array(	"304", "312", "314" ); // �ӹѡ�����á�ҧ
				elseif ($ORG_ID==9491) $code = array(	"304", "312", "314" ); // �ӹѡ���ǡ�����п�鹿پ�鹷��
				elseif ($ORG_ID==9492) $code = array(	"304", "312", "314" ); // �ӹѡ�ҹ�ص��ˡ�����鹰ҹ��С������ͧ���ࢵ 1
				elseif ($ORG_ID==9493) $code = array(	"304", "312", "314" ); // �ӹѡ�ҹ�ص��ˡ�����鹰ҹ��С������ͧ���ࢵ 2
				elseif ($ORG_ID==9494) $code = array(	"304", "312", "314" ); // �ӹѡ�ҹ�ص��ˡ�����鹰ҹ��С������ͧ���ࢵ 3
				elseif ($ORG_ID==9495) $code = array(	"304", "312", "314" ); // �ӹѡ����������Ǵ����
				elseif ($ORG_ID==9496) $code = array(	"304", "312", "314" ); // �ӹѡ����ͧ�����������ҹ
				elseif ($ORG_ID==9497) $code = array(	"304", "312", "314" ); // �ӹѡ�ص��ˡ�����鹰ҹ
				elseif ($ORG_ID==12970) $code = array(	"304", "312", "314" ); // �ӹѡ�Ũ�ʵԡ��
			} elseif ($PL_CODE=="511612") { // ��Ҿ�ѡ�ҹ��á��
				if ($ORG_ID==9489) $code = array(	"304", "309", "312" ); // �ӹѡ�����á�ҧ
				elseif ($ORG_ID==9490) $code = array(	"304", "309", "312" ); // �ӹѡ������
				elseif ($ORG_ID==9491) $code = array(	"304", "309", "312" ); // �ӹѡ���ǡ�����п�鹿پ�鹷��
				elseif ($ORG_ID==9492) $code = array(	"304", "309", "312" ); // �ӹѡ�ҹ�ص��ˡ�����鹰ҹ��С������ͧ���ࢵ 1
				elseif ($ORG_ID==9493) $code = array(	"304", "309", "312" ); // �ӹѡ�ҹ�ص��ˡ�����鹰ҹ��С������ͧ���ࢵ 2
				elseif ($ORG_ID==9494) $code = array(	"304", "309", "312" ); // �ӹѡ�ҹ�ص��ˡ�����鹰ҹ��С������ͧ���ࢵ 3
				elseif ($ORG_ID==9495) $code = array(	"304", "309", "312" ); // �ӹѡ����������Ǵ����
				elseif ($ORG_ID==9496) $code = array(	"304", "309", "312" ); // �ӹѡ����ͧ�����������ҹ
				elseif ($ORG_ID==9497) $code = array(	"304", "309", "312" ); // �ӹѡ�ص��ˡ�����鹰ҹ
				elseif ($ORG_ID==9802) $code = array(	"304", "309", "312" ); // �ӹѡ�������ط���ʵ��
				elseif ($ORG_ID==9803) $code = array(	"304", "309", "312" ); // �������Ǩ�ͺ����
				elseif ($ORG_ID==12970) $code = array(	"304", "309", "312" ); // �ӹѡ�Ũ�ʵԡ��
			} elseif ($PL_CODE=="511712") { // ��Ҿ�ѡ�ҹ��ʴ�
				if ($ORG_ID==9489) $code = array(	"302", "304", "309" ); // �ӹѡ�����á�ҧ
			} elseif ($PL_CODE=="511723") { // �ѡ�Ԫҡ�þ�ʴ�
				if ($ORG_ID==9489) $code = array(	"302", "304", "309" ); // �ӹѡ�����á�ҧ
			} elseif ($PL_CODE=="512003") { // �ѡ�Ԫҡ��ʶԵ�
				if ($ORG_ID==9802) $code = array(	"303", "313", "314" ); // �ӹѡ�������ط���ʵ��
			} elseif ($PL_CODE=="512212") { // ��Ҿ�ѡ�ҹʶԵ�
				if ($ORG_ID==9802) $code = array(	"303", "309", "313" ); // �ӹѡ�������ط���ʵ��
			} elseif ($PL_CODE=="512403") { // �Եԡ�
				if ($ORG_ID==9489) $code = array(	"302", "304", "314" ); // �ӹѡ�����á�ҧ
				elseif ($ORG_ID==9490) $code = array(	"302", "304", "313" ); // �ӹѡ������
			} elseif ($PL_CODE=="512603") { // �ѡ�׺�ǹ�ͺ�ǹ
				if ($ORG_ID==9490) $code = array(	"302", "313", "317" ); // �ӹѡ������
			} elseif ($PL_CODE=="512409") { // ����ӹ�¡��੾�д�ҹ (�Եԡ��)
				if ($ORG_ID==9490) $code = array(	"302", "304", "313" ); // �ӹѡ������
			} elseif ($PL_CODE=="520412") { // ��Ҿ�ѡ�ҹ����Թ��кѭ��
				if ($ORG_ID==9489) $code = array(	"302", "304", "309" ); // �ӹѡ�����á�ҧ
				elseif ($ORG_ID==9490) $code = array(	"302", "304", "309" ); // �ӹѡ������
				elseif ($ORG_ID==9491) $code = array(	"302", "304", "309" ); // �ӹѡ���ǡ�����п�鹿پ�鹷��
				elseif ($ORG_ID==9492) $code = array(	"302", "304", "309" ); // �ӹѡ�ҹ�ص��ˡ�����鹰ҹ��С������ͧ���ࢵ 1
				elseif ($ORG_ID==9493) $code = array(	"302", "304", "309" ); // �ӹѡ�ҹ�ص��ˡ�����鹰ҹ��С������ͧ���ࢵ 2
				elseif ($ORG_ID==9494) $code = array(	"302", "304", "309" ); // �ӹѡ�ҹ�ص��ˡ�����鹰ҹ��С������ͧ���ࢵ 3
				elseif ($ORG_ID==9496) $code = array(	"302", "304", "309" ); // �ӹѡ����ͧ�����������ҹ
			} elseif ($PL_CODE=="520423") { // �ѡ�Ԫҡ���Թ��кѭ��
				if ($ORG_ID==9489) $code = array(	"302", "304", "314" ); // �ӹѡ�����á�ҧ
				elseif ($ORG_ID==9492) $code = array(	"302", "304", "314" ); // �ӹѡ�ҹ�ص��ˡ�����鹰ҹ��С������ͧ���ࢵ 1
			} elseif ($PL_CODE=="520603") { // �ѡ�Ԫҡ�õ�Ǩ�ͺ����
				if ($ORG_ID==9803) $code = array(	"302", "304", "314" ); // �������Ǩ�ͺ����
			} elseif ($PL_CODE=="522003") { // ���ɰ��
				if ($ORG_ID==9488) $code = array(	"303", "313", "314" ); // ��ǹ��ҧ
				elseif ($ORG_ID==9496) $code = array(	"303", "313", "314" ); // �ӹѡ����ͧ�����������ҹ
				elseif ($ORG_ID==9802) $code = array(	"303", "313", "314" ); // �ӹѡ�������ط���ʵ��
			} elseif ($PL_CODE=="523203") { // �ѡ�Ԫҡ���ص��ˡ���
				if ($ORG_ID==9490) $code = array(	"302", "304", "313" ); // �ӹѡ������
				elseif ($ORG_ID==9496) $code = array(	"302", "304", "314" ); // �ӹѡ����ͧ�����������ҹ
				elseif ($ORG_ID==9802) $code = array(	"303", "313", "316" ); // �ӹѡ�������ط���ʵ��
			} elseif ($PL_CODE=="523612") { // ��Ҿ�ѡ�ҹ��Ѿ�ҡøó�
				if ($ORG_ID==9490) $code = array(	"302", "304", "309" ); // �ӹѡ������
				elseif ($ORG_ID==9496) $code = array(	"302", "304", "309" ); // �ӹѡ����ͧ�����������ҹ
			} elseif ($PL_CODE=="523623") { // �ѡ�Ԫҡ�÷�Ѿ�ҡøó�
				if ($ORG_ID==9496) $code = array(	"302", "304", "314" ); // �ӹѡ����ͧ�����������ҹ
				elseif ($ORG_ID==9497) $code = array(	"302", "304", "314" ); // �ӹѡ�ص��ˡ�����鹰ҹ
			} elseif ($PL_CODE=="532423") { // �ѡ�Ԫҡ�������
				if ($ORG_ID==9489) $code = array(	"304", "310", "311" ); // �ӹѡ�����á�ҧ
			} elseif ($PL_CODE=="532523") { // �ѡ�Ԫҡ���ʵ��ȹ�֡��
				if ($ORG_ID==9489) $code = array(	"304", "310", "311" ); // �ӹѡ�����á�ҧ
			} elseif ($PL_CODE=="550103") { // �ѡ�Է����ʵ��
				if ($ORG_ID==9492) $code = array(	"301", "305", "313" ); // �ӹѡ�ҹ�ص��ˡ�����鹰ҹ��С������ͧ���ࢵ 1
				elseif ($ORG_ID==9493) $code = array(	"301", "305", "313" ); // �ӹѡ�ҹ�ص��ˡ�����鹰ҹ��С������ͧ���ࢵ 2
				elseif ($ORG_ID==9494) $code = array(	"301", "305", "313" ); // �ӹѡ�ҹ�ص��ˡ�����鹰ҹ��С������ͧ���ࢵ 3
				elseif ($ORG_ID==9497) $code = array(	"303", "314", "315" ); // �ӹѡ�ص��ˡ�����鹰ҹ
				elseif ($ORG_ID==12970) $code = array(	"303", "305", "313" ); // �ӹѡ�Ũ�ʵԡ��
			} elseif ($PL_CODE=="550212") { // ��Ҿ�ѡ�ҹ�Է����ʵ��
				if ($ORG_ID==9495) $code = array(	"305", "313", "315" ); // �ӹѡ����������Ǵ����
				elseif ($ORG_ID==9497) $code = array(	"304", "309", "317" ); // �ӹѡ�ص��ˡ�����鹰ҹ
				elseif ($ORG_ID==12970) $code = array(	"304", "305", "313" ); // �ӹѡ�Ũ�ʵԡ��
			} elseif ($PL_CODE=="551303") { // �ѡ�ó��Է��
				if ($ORG_ID==9492) $code = array(	"303", "304", "313" ); // �ӹѡ�ҹ�ص��ˡ�����鹰ҹ��С������ͧ���ࢵ 1
				elseif ($ORG_ID==9493) $code = array(	"303", "304", "313" ); // �ӹѡ�ҹ�ص��ˡ�����鹰ҹ��С������ͧ���ࢵ 2
				elseif ($ORG_ID==9494) $code = array(	"303", "304", "313" ); // �ӹѡ�ҹ�ص��ˡ�����鹰ҹ��С������ͧ���ࢵ 3
				elseif ($ORG_ID==9496) $code = array(	"303", "304", "313" ); // �ӹѡ����ͧ�����������ҹ
			} elseif ($PL_CODE=="570103") { // ���ǡ�
				if ($ORG_ID==9497) $code = array(	"303", "314", "315" ); // �ӹѡ�ص��ˡ�����鹰ҹ
			} elseif ($PL_CODE=="570109") { // ����ӹ�¡��੾�д�ҹ (���ǡ���)
				if ($ORG_ID==9491) $code = array(	"303", "314", "315" ); // �ӹѡ���ǡ�����п�鹿پ�鹷��
			} elseif ($PL_CODE=="570203") { // ���ǡ��¸�
				if ($ORG_ID==9491) $code = array(	"303", "313", "315" ); // �ӹѡ���ǡ�����п�鹿پ�鹷��
			} elseif ($PL_CODE=="570403") { // ���ǡ�����ͧ��
				if ($ORG_ID==9491) $code = array(	"303", "313", "315" ); // �ӹѡ���ǡ�����п�鹿پ�鹷��
				elseif ($ORG_ID==9497) $code = array(	"303", "314", "315" ); // �ӹѡ�ص��ˡ�����鹰ҹ
				elseif ($ORG_ID==12970) $code = array(	"303", "305", "313" ); // �ӹѡ�Ũ�ʵԡ��
			} elseif ($PL_CODE=="570503") { // ���ǡ�俿��
				if ($ORG_ID==9497) $code = array(	"303", "304", "314" ); // �ӹѡ�ص��ˡ�����鹰ҹ
			} elseif ($PL_CODE=="570603") { // ���ǡ�����ͧ���
				if ($ORG_ID==9488) $code = array(	"303", "313", "314" ); // ��ǹ��ҧ
				elseif ($ORG_ID==9492) $code = array(	"305", "314", "315" ); // �ӹѡ�ҹ�ص��ˡ�����鹰ҹ��С������ͧ���ࢵ 1
				elseif ($ORG_ID==9493) $code = array(	"305", "314", "315" ); // �ӹѡ�ҹ�ص��ˡ�����鹰ҹ��С������ͧ���ࢵ 2
				elseif ($ORG_ID==9494) $code = array(	"305", "314", "315" ); // �ӹѡ�ҹ�ص��ˡ�����鹰ҹ��С������ͧ���ࢵ 3
				elseif ($ORG_ID==9496) $code = array(	"303", "313", "314" ); // �ӹѡ����ͧ�����������ҹ
				elseif ($ORG_ID==9497) $code = array(	"303", "314", "315" ); // �ӹѡ�ص��ˡ�����鹰ҹ
				elseif ($ORG_ID==9802) $code = array(	"303", "305", "313" ); // �ӹѡ�������ط���ʵ��
				elseif ($ORG_ID==12970) $code = array(	"303", "305", "313" ); // �ӹѡ�Ũ�ʵԡ��
			} elseif ($PL_CODE=="570803") { // ���ǡ���ˡ��
				if ($ORG_ID==9488) $code = array(	"303", "313", "314" ); // ��ǹ��ҧ
				elseif ($ORG_ID==9493) $code = array(	"305", "314", "315" ); // �ӹѡ�ҹ�ص��ˡ�����鹰ҹ��С������ͧ���ࢵ 2
				elseif ($ORG_ID==9497) $code = array(	"303", "314", "315" ); // �ӹѡ�ص��ˡ�����鹰ҹ
				elseif ($ORG_ID==9802) $code = array(	"303", "313", "316" ); // �ӹѡ�������ط���ʵ��
				elseif ($ORG_ID==12970) $code = array(	"303", "305", "313" ); // �ӹѡ�Ũ�ʵԡ��
			} elseif ($PL_CODE=="571412") { // ��ª�ҧ�¸�
				if ($ORG_ID==9491) $code = array(	"306", "311", "315" ); // �ӹѡ���ǡ�����п�鹿پ�鹷��
			} elseif ($PL_CODE=="571512") { // ��ª�ҧ�ѧ�Ѵ
				if ($ORG_ID==9491) $code = array(	"304", "309", "312" ); // �ӹѡ���ǡ�����п�鹿پ�鹷��
				elseif ($ORG_ID==9492) $code = array(	"304", "309", "313" ); // �ӹѡ�ҹ�ص��ˡ�����鹰ҹ��С������ͧ���ࢵ 1
				elseif ($ORG_ID==9494) $code = array(	"304", "309", "313" ); // �ӹѡ�ҹ�ص��ˡ�����鹰ҹ��С������ͧ���ࢵ 3
			} elseif ($PL_CODE=="571912") { // ��ª�ҧ����ͧ��
				if ($ORG_ID==9491) $code = array(	"306", "311", "315" ); // �ӹѡ���ǡ�����п�鹿پ�鹷��
				elseif ($ORG_ID==9497) $code = array(	"304", "309", "315" ); // �ӹѡ�ص��ˡ�����鹰ҹ
			} elseif ($PL_CODE=="572412") { // ��ª�ҧ����
				if ($ORG_ID==9497) $code = array(	"304", "309", "315" ); // �ӹѡ�ص��ˡ�����鹰ҹ
			} elseif ($PL_CODE=="573012") { // ��ª�ҧ俿��
				if ($ORG_ID==9497) $code = array(	"304", "314", "315" ); // �ӹѡ�ص��ˡ�����鹰ҹ
			} elseif ($PL_CODE=="573712") { // ��ª�ҧ����ͧ���
				if ($ORG_ID==9492) $code = array(	"304", "313", "318" ); // �ӹѡ�ҹ�ص��ˡ�����鹰ҹ��С������ͧ���ࢵ 1
				elseif ($ORG_ID==9494) $code = array(	"301", "305", "315" ); // �ӹѡ�ҹ�ص��ˡ�����鹰ҹ��С������ͧ���ࢵ 3
				elseif ($ORG_ID==9495) $code = array(	"301", "305", "315" ); // �ӹѡ����������Ǵ����
				elseif ($ORG_ID==9496) $code = array(	"303", "305", "315" ); // �ӹѡ����ͧ�����������ҹ
				elseif ($ORG_ID==9497) $code = array(	"304", "309", "315" ); // �ӹѡ�ص��ˡ�����鹰ҹ
				elseif ($ORG_ID==9802) $code = array(	"303", "305", "313" ); // �ӹѡ�������ط���ʵ��
				elseif ($ORG_ID==12970) $code = array(	"304", "305", "313" ); // �ӹѡ�Ũ�ʵԡ��
			} elseif ($PL_CODE=="574312") { // ��ª�ҧ��¹Ẻ
				if ($ORG_ID==9497) $code = array(	"304", "309", "315" ); // �ӹѡ�ص��ˡ�����鹰ҹ
				elseif ($ORG_ID==12970) $code = array(	"304", "305", "313" ); // �ӹѡ�Ũ�ʵԡ��
			} elseif ($PL_CODE=="584003") { // �ѡ�Ԫҡ������Ǵ����
				if ($ORG_ID==9491) $code = array(	"306", "311", "315" ); // �ӹѡ���ǡ�����п�鹿پ�鹷��
				elseif ($ORG_ID==9495) $code = array(	"303", "355", "315" ); // �ӹѡ����������Ǵ����
				elseif ($ORG_ID==12970) $code = array(	"303", "305", "313" ); // �ӹѡ�Ũ�ʵԡ��
			}		
			for ( $i=0; $i<count($code); $i++ ) { 
				$cmd = " INSERT INTO PER_LINE_COMPETENCE (PL_CODE, ORG_ID, CP_CODE, LC_ACTIVE, UPDATE_USER, UPDATE_DATE) 
								VALUES('$PL_CODE', $ORG_ID, '$code[$i]', 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis1->send_cmd($cmd);
//				$db_dpis1->show_error();
			} // end for
		} // end while				
		
		$cmd = " delete from PER_POSITION_COMPETENCE ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " delete from PER_KPI_COMPETENCE ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " select	POS_ID, PL_CODE, ORG_ID from PER_POSITION where POS_STATUS=1 order by POS_NO ";		
		$db_dpis1->send_cmd($cmd);
//		$db_dpis1->show_error();
		while($data1 = $db_dpis1->get_array()){
			$POS_ID = $data1[POS_ID];
			$PL_CODE = trim($data1[PL_CODE]);
			$ORG_ID = $data1[ORG_ID];

			$cmd = " select	CP_CODE from	PER_LINE_COMPETENCE where PL_CODE='$PL_CODE' and ORG_ID=$ORG_ID order by CP_CODE ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			while($data = $db_dpis->get_array()){
				$CP_CODE = $data[CP_CODE];
				
				$cmd = " select TARGET_LEVEL from PER_STANDARD_COMPETENCE a, PER_PERSONAL b
								where b.POS_ID = $POS_ID and a.LEVEL_NO = b.LEVEL_NO and CP_CODE = '$CP_CODE' "; 
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$TARGET_LEVEL = $data2[TARGET_LEVEL] + 0;
				
				$cmd = " insert into PER_POSITION_COMPETENCE (POS_ID, CP_CODE, PC_TARGET_LEVEL, DEPARTMENT_ID, 
								  UPDATE_USER, UPDATE_DATE) 
								  values ($POS_ID, '$CP_CODE', $TARGET_LEVEL, $DEPARTMENT_ID, $SESS_USERID, '$UPDATE_DATE') "; 
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
			} // end while
		} // end while

		$cmd = " select max(KC_ID) as max_id from PER_KPI_COMPETENCE ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$data2 = array_change_key_case($data2, CASE_LOWER);
		$KC_ID = $data2[max_id] + 1;
		
		$cmd = " select		KF_ID, CP_CODE, PC_TARGET_LEVEL
						 from		PER_POSITION a, PER_POSITION_COMPETENCE b, PER_PERSONAL c, PER_KPI_FORM d
						 where	a.POS_ID=b.POS_ID and a.POS_ID = c.POS_ID and c.PER_ID = d.PER_ID and PC_TARGET_LEVEL > 0
						 order by a.POS_ID, CP_CODE ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		while($data = $db_dpis->get_array()){
			$KF_ID = $data[KF_ID];
			$CP_CODE = $data[CP_CODE];
			$PC_TARGET_LEVEL = $data[PC_TARGET_LEVEL];
				
			$cmd = " select CP_CODE from PER_KPI_COMPETENCE where KF_ID=$KF_ID and trim(CP_CODE)='$CP_CODE' ";
			$count_duplicate = $db_dpis2->send_cmd($cmd);
			if($count_duplicate <= 0){
				$cmd = " insert into PER_KPI_COMPETENCE (KC_ID, KF_ID, CP_CODE, PC_TARGET_LEVEL, UPDATE_USER, 
								UPDATE_DATE) 
								values ($KC_ID, $KF_ID, '$CP_CODE', $PC_TARGET_LEVEL, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$KC_ID++;
			}
		} // end while

		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						PD_GUIDE_DESCRIPTION2, UPDATE_USER, UPDATE_DATE) 
						VALUES(1, 1, '101', '����ͺ���§ҹ��С����ع���¹�ҹ
�ͺ���§ҹ���˹�ҷ������Ѻ�Դ�ͺ�ͧ���кؤ�� ���ա�á�˹����ҡ�����ͺ�ҹ����Чҹ���ҧ�Ѵਹ
�����������Դ�͡�����ؤ�ҡ����ʴ������Դ������͢���ʹ��е�ҧ� ��������������Ǣ�ͧ�Ѻ˹�ҷ������Ѻ�Դ�ͺ ������������������ö�Ѳ�ҧҹ�ͧ��������觢�����
�����������Դ�͡�����ؤ�ҡ����ʴ������Դ�����ҵ��ͧ������ö���ҧ�س�����л���ª����������ҹ���ͧ��������ҧ�� ����ͺ���§ҹ���� �������ʹ㨢ͧ���кؤ��
�ͺ���§ҹ������ѡɳ������������������������ö�Դ����� ���º��º��������˹���� �����������Ѻ�ӵԪ�����ǡѺ�ŧҹ㹷ѹ��
����� (Coaching)
���� �й� ���ӻ�֡������ͧ�Ըա�á�÷ӧҹ��������ҵðҹ�ͧ�ҹ����ҵðҹ�ͧͧ���
��ý֡ͺ��
Time Management', '������ѡ�ٵá�ý֡ͺ��: Time Management 
�ѵ�ػ��ʧ��
���������֡ͺ���ա�ͺ�����Դ ����ͧ��ú����èѴ������� 
��ͺ�����Ңͧ��ѡ�ٵ�
��������ѡ��Сͺ���� ��ú��������������繶֧
��ͺ�ǤԴ㹡�ú����èѴ���������л���ª��ͧ��ú����èѴ�������
��ͼԴ��Ҵ��ҧ� ��������������� (��ú������˵����Ҵ��ѧ (Crisis) ���ú�ǹ�����Ѿ�� �������ҧἹ��ǧ˹�� ����觧ҹ �������Թ�µ�͵��ͧ �����������ö���л���ʸ ��ú����èѴ��á�û�Ъ�� ��ú����çҹ�͡��� (Paperwork) ���������÷������ �繵�)
��кǹ������෤�Ԥ㹡����䢢�ͼԴ��Ҵ�����������ö�����èѴ������������ҧ�ջ���Է���Ҿ�٧�ش
�������Ңͧ��ѡ�ٵ�
8 �������
�Ƿҧ��û����Թ��
������ͺ�� �ж١�����Թ������������ý֡ͺ�� �������ش��ý֡ͺ��������дѺ��þѲ�Ҥ�������������� ��§� �͡�ҡ��������ѧ�Ѻ�ѭ�һ����Թ���㹡�û�Ժѵԧҹ��ԧ�������ö�����èѴ����������ҡ������§�', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
/*
			$cmd = " select PER_ID from PER_PERSONAL where PER_CARDNO='$PER_CARDNO' or (PER_NAME = '$PER_NAME' and PER_SURNAME = '$PER_SURNAME') ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$PER_ID = $data[PER_ID];
			
			$cmd = " update PER_KPI_FORM set CHIEF_PER_ID = $CHIEF_PER_ID where PER_ID=$PER_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();		
*/		
	}

?>