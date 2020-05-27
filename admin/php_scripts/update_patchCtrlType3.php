<?php
		if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_EDUCATE ALTER EDU_TYPE VARCHAR(20) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_EDUCATE MODIFY EDU_TYPE VARCHAR2(20) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_EDUCATE MODIFY EDU_TYPE VARCHAR(20) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " UPDATE PER_EDUCATE SET EDU_TYPE = '||'+EDU_TYPE+'||' ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_EDUCATE SET EDU_TYPE = '||'||EDU_TYPE||'||' ";
			elseif($DPISDB=="mysql")
				$cmd = " UPDATE PER_EDUCATE SET EDU_TYPE = '||'||EDU_TYPE||'||' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_CONTROL", "CTRL_TYPE","SINGLE", "1", "NULL");
			add_field("PER_CONTROL", "PV_CODE","VARCHAR", "10", "NULL");

			$cmd = " ALTER TABLE PER_CONTROL DROP CONSTRAINT FK1_PER_CONTROL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_CONTROL ALTER ORG_ID INTEGER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_CONTROL MODIFY ORG_ID NUMBER(10) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_CONTROL MODIFY ORG_ID INTEGER(10) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_OFF_TYPE (OT_CODE, OT_NAME, OT_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('08', '��ѡ�ҹ�Ҫ��÷����', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_OFF_TYPE (OT_CODE, OT_NAME, OT_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('09', '��ѡ�ҹ�Ҫ��þ����', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_OFF_TYPE (OT_CODE, OT_NAME, OT_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('10', '�١��ҧ���Ǥ���', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('230', '����������͹�дѺ��ѡ�ҹ�Ҫ���', 2, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('23010', '����͹�дѺ��', 2, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('23020', '����͹�дѺ����', 2, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('23030', '���ѭ�Ҩ�ҧ�����á', 2, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('23040', '����ѭ��', 2, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

	// ��������Ǫҭ�����
			$code = array(	"001", "002", "003", "004", "005", "006", "007", "008", "009", "010", "011", "012", "013", "014", "015", "016", "017", 
											"018", "019", "020", "021", "022" );
			$desc = array(	"��ҹ����֡��", "��ҹ���ᾷ������Ҹ�ó�آ", "��ҹ�ɵ�", "��ҹ��Ѿ�ҡø����ҵ��������Ǵ����", "��ҹ�Է����ʵ�����෤�����", 
											"��ҹ���ǡ���", "��ҹʶһѵ¡���", "��ҹ����Թ ��ä�ѧ ������ҳ", "��ҹ�ѧ��", "��ҹ������", "��ҹ��û���ͧ ������ͧ", 
											"��ҹ��Ż�Ѳ����������ʹ�", "��ҹ����ҧἹ�Ѳ��", "��ҹ�ҳԪ����к�ԡ��", "��ҹ������蹤�", "��ҹ��ú����èѴ�����к����ø�áԨ", 
											"��ҹ��û�Ъ�����ѹ��", "��ҹ��ä��Ҥ���С���������", "��ҹ��ѧ�ҹ", "��ҹ��ҧ�����", "��ҹ�ص��ˡ���", "��ҹ�Ըա��" );
			for ( $i=0; $i<count($code); $i++ ) { 
				$cmd = " INSERT INTO PER_SPECIAL_SKILLGRP (SS_CODE, SS_NAME, SS_ACTIVE, UPDATE_USER, UPDATE_DATE)
								  VALUES ('$code[$i]', '$desc[$i]', 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			} // end for

	// ���ö������С�����ҹ
			$code = array(	"01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12", "13", "14", "15", "16", "17", "18" );
			$desc = array(	"������ҹʹѺʹع�����", "������ҹʹѺʹع�ҹ��ѡ�ҧ෤�Ԥ੾�д�ҹ", "������ҹ���ӻ�֡��", "������ҹ������", 
											"������ҹ��º������ҧἹ", "������ҹ�֡���Ԩ����оѲ��", "������ҹ���ǡ�ͧ����׺�ǹ", "������ҹ�͡Ẻ���;Ѳ��", 
											"������ҹ��������ѹ�������ҧ�����", "������ҹ�ѧ�Ѻ�顯����", "������ҹ������Ъ�����ѹ��", "������ҹ��������������", 
											"������ҹ��ԡ�û�ЪҪ���ҹ�آ�Ҿ������ʴ��Ҿ", "������ҹ��ԡ�û�ЪҪ��ҧ��Ż�Ѳ�����", 
											"������ҹ��ԡ�û�ЪҪ��ҧ෤�Ԥ੾�д�ҹ", "������ҹ�͡����Ҫ�����з���¹", "������ҹ��û���ͧ", "������ҹ͹��ѡ��" );
			for ( $i=0; $i<count($code); $i++ ) { 
				$cmd = " INSERT INTO PER_JOB_FAMILY (JF_CODE, JF_NAME, JF_ACTIVE, UPDATE_USER, UPDATE_DATE)
								  VALUES ('$code[$i]', '$desc[$i]', 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			} // end for

			if($SCOMPETENCY[$PER_ID_DEPARTMENT_ID]!="Y"){
				$code = array(	"101", "102", "103", "104", "105", "201", "202", "203", "301", "302", "303", "304", "305", "306", "307", 
										"308", "309", "310", "311", "312", "313", "314", "315", "316", "317", "318", "319", "320" );
				$desc = array(	"�����觼����ķ���", "��ԡ�÷���", "����������������Ǫҭ㹧ҹ�Ҫվ", "���¸���", "���������ç�����", 
										"����ҧἹ��С�èѴ�к��ҹ", "��þѲ�Ҽ����ѧ�Ѻ�ѭ��", "�����繼���", "��äԴ��������", "����ͧ�Ҿͧ�����", 
										"��þѲ���ѡ��Ҿ��", "�����觡�õ���ӹҨ˹�ҷ��", "����׺�����Ң�����", "�������㨢��ᵡ��ҧ�ҧ�Ѳ�����", "�������㨼�����", 
										"��������ͧ�������к��Ҫ���", "��ô��Թ����ԧ�ء", "�����١��ͧ�ͧ�ҹ", "��������㹵��ͧ", "�����״���蹼�͹�ù", 
										"��ŻС��������è٧�", "����м���", "�ع�����Ҿ�ҧ��Ż�", "����·�ȹ�", "����ҧ���ط���Ҥ�Ѱ", "�ѡ��Ҿ���͹ӡ�û�Ѻ����¹", 
										"��äǺ������ͧ", "�������ӹҨ�������" );
//				$meaning = array(	"NULL", "NULL", "NULL", "NULL", "NULL", "NULL", "NULL", "NULL", "NULL", "NULL", "NULL", "NULL", 
//										"NULL", "NULL", "NULL", "NULL", "NULL", "NULL", "NULL", "NULL", "NULL", "NULL", "NULL", "NULL", "NULL", 
//										"NULL", "NULL", "NULL" );
			} else {
				$code = array(	"101", "102", "103", "104", "105", "201", "202", "203", "204", "205", "206", "301", "302", "303", "304", "305", "306", 
										"307", "308", "309", "310", "311", "312", "313", "314", "315", "316", "317", "318" );
				$desc = array(	"�����觼����ķ���", "��ԡ�÷���", "����������������Ǫҭ㹧ҹ�Ҫվ", "���¸���", "���������ç�����", 
										"����м���", "����·�ȹ�", "����ҧ���ط���Ҥ�Ѱ", "�ѡ��Ҿ���͹ӡ�û�Ѻ����¹", "��äǺ������ͧ", "����͹�ҹ�������ӹҨ�������",  
										"�����䢻ѭ��Ẻ����Ҫվ", "��������������С�����ҧ����ѹ��", "��áӡѺ�Դ������ҧ��������", "��ä�����С�ú����èѴ��â����Ť������",
										"��äԴ����������к�óҡ��", "��ê�ҧ�ѧࡵ", "��ô��Թ����ԧ�ء", "��ú����÷�Ѿ�ҡ�", "����ִ������ѡࡳ��", 
										"����ҧἹ��Ф��������к��ҹ", "������ҧ����Դ�������ǹ����㹷ء�Ҥ��ǹ", "������ҧ�������ѹ��", "����������������Ǩ٧�", 
										"�������㨤�����״���蹵��ʶҹ��ó�", "�����Դ���ҧ��ä�", "���������ͧ�������к��ҹ", "����ҧἹ�ҹ��ǧ˹��", 
										"�����١��ͧ�ͧ�ҹ" );
				$meaning = array(	"���������蹨л�Ժѵ��Ҫ���������������Թ�ҵðҹ��������� ���ҵðҹ����Ҩ�繼š�û�Ժѵԧҹ����ҹ�Ңͧ���ͧ ����ࡳ���Ѵ�����ķ�������ǹ�Ҫ��á�˹���� �ա����ѧ��������֧������ҧ��ä�Ѳ�Ҽŧҹ���͡�кǹ��û�Ժѵԧҹ���������·���ҡ��з�ҷ�ª�Դ����Ҩ������ռ�������ö��з����ҡ�͹", "���ö�й���鹤���������Ф����������ͧ����Ҫ���㹡������ԡ������ʹͧ������ͧ��âͧ��ЪҪ���ʹ���ͧ˹��§ҹ�Ҥ�Ѱ���� �������Ǣ�ͧ", "�����ǹ���� ʹ������ ����������Ѳ���ѡ��Ҿ ��������������ö�ͧ��㹡�û�Ժѵ��Ҫ��� ���¡���֡�� �鹤����Ҥ������ �Ѳ�ҵ��ͧ���ҧ������ͧ �ա������ѡ�Ѳ�� ��Ѻ��ا ����ء�����������ԧ�Ԫҡ�����෤����յ�ҧ� ��ҡѺ��û�Ժѵԧҹ����Դ�����ķ���", "��ä�ͧ����л�оĵԻ�ԺѵԶ١��ͧ���������駵����ѡ��������Фس�������¸��� ��ʹ����ѡ�Ƿҧ��ԪҪվ�ͧ������觻���ª��ͧ����Ȫҵ��ҡ���һ���ª����ǹ��  ��駹�����͸�ç�ѡ���ѡ����������Ҫվ����Ҫ��� �ա��������繡��ѧ�Ӥѭ㹡��ʹѺʹع��ѡ�ѹ�����áԨ��ѡ�Ҥ�Ѱ�����������·���˹����", "���ö�й���鹷�� 1) �������㨷��зӧҹ�����Ѻ������ ����ǹ˹��㹷���ҹ ˹��§ҹ ����ͧ��� �¼�黯Ժѵ��հҹ�����Ҫԡ㹷�� ����㹰ҹ����˹�ҷ�� ��� 2) ��������ö㹡�����ҧ��д�ç�ѡ������ѹ��Ҿ�Ѻ��Ҫԡ㹷��", "�����������ͤ�������ö㹡���繼��Ӣͧ������� ����ͧ ����֧��á�˹���ȷҧ ����·�ȹ� ������� �Ըա�÷ӧҹ �������ѧ�Ѻ�ѭ�����ͷ���ҹ��Ժѵԧҹ�����ҧ�Һ��� �������Է���Ҿ��к�����ѵ�ػ��ʧ��ͧͧ���", "��������ö����ȷҧ���Ѵਹ��С�ͤ��������ç��������������ѧ�Ѻ�ѭ�����͹Ӿҧҹ�Ҥ�Ѱ����ش���������ѹ", "�������㨡��ط���Ҥ�Ѱ�������ö����ء����㹡�á�˹����ط��ͧ˹��§ҹ���� �¤�������ö㹡�û���ء��������֧��������ö㹡�äҴ��ó�֧��ȷҧ�к��Ҫ����͹Ҥ� ��ʹ���š�з��ͧʶҹ��ó������е�ҧ����ȷ���Դ���", "����������Ф�������ö㹡�á�е�鹼�ѡ�ѹ�����������Դ������ͧ��èл�Ѻ����¹���Ƿҧ����繻���ª�����Ҥ�Ѱ ����֧�������������������Ѻ��� ���� ��д��Թ�������û�Ѻ����¹����Դ��鹨�ԧ", "����ЧѺ��������оĵԡ����ѹ��������������Ͷ١������ ����༪ԭ˹�ҡѺ���µç���� ༪ԭ����������Ե� ���ͷӧҹ���������Ф������ѹ ����֧����ʹ��ʹ��������͵�ͧ���������ʶҹ��ó����ͤ������´���ҧ������ͧ", "�������㨨��������������¹������͡�þѲ�Ҽ������������� ����֧�����������㹤�������ö�ͧ������ �ѧ��鹨֧�ͺ�����ӹҨ���˹�ҷ���Ѻ�Դ�ͺ����������������������㹡�����ҧ��ä��Ըա�âͧ�����ͺ�����������㹧ҹ", "��������ö���������ѭ�����������繻ѭ�� ��������ŧ��ͨѴ��áѺ�ѭ�ҹ��� ���ҧ�բ����� ����ѡ��� �������ö�Ӥ�������Ǫҭ �����ǤԴ�����ԪҪվ�һ���ء����㹡����䢻ѭ�������ҧ�ջ���Է���Ҿ", "�վĵԡ������������ ��е��㨷��й����Իѭ�� ��ѵ���� ෤����� ��������Ǫҭ ���ͧ���������ҧ� �������� ʹѺʹع ��оѲ�Ҽ���Сͺ��� �������͢��� �Ǻ���仡Ѻ������ҧ �Ѳ�� ����ѡ�Ҥ�������ѹ���ѹ�աѺ����Сͺ��� �������͢��� ����������Сͺ��� �������͢��� �դ������ �������� �������ö �����Ѳ��˹��§ҹ����ջ���ª�� �ա����繡����������ҧ�մ��������ö㹡���觢ѹ���ҧ����׹", "ਵ�ҷ��СӡѺ���� ��еԴ�����ô��Թ�ҹ��ҧ� �ͧ�����蹷������Ǣ�ͧ��黯ԺѵԵ���ҵðҹ ������º ���͢�ͺѧ�Ѻ����˹���� ��������ӹҨ�������º ������ ���͵�����˹�˹�ҷ�������������ҧ�����������ջ���Է���Ҿ����觻���ª��ͧ˹��§ҹ ͧ��� ���ͻ���Ȫҵ����Ӥѭ", "��������ö㹡���׺���� ��������������੾����Ш� ���䢻����ȹ��«ѡ����������´ ������������Ң��Ƿ���仨ҡ��Ҿ�Ǵ�����ͺ����¤Ҵ����Ҩ�բ����ŷ����繻���ª������͹Ҥ� ��йӢ����ŷ�����ҹ���һ�������ШѴ������ҧ���к� �س�ѡɳй���Ҩ����֧����ʹ�����������ǡѺʶҹ��ó� ������ѧ ����ѵԤ������� ����� �ѭ�� ��������ͧ��ǵ�ҧ� �������Ǣ�ͧ���ͨ��繵�ͧҹ�˹�ҷ��", 
										"��������ö㹡�äԴ����������зӤ���������ԧ�ѧ������ ����֧����ͧ�Ҿ����ͧͧ��� �����繡�ͺ�����Դ�����ǤԴ���� �ѹ�繼��Ҩҡ�����ػ�ٻẺ ����ء���Ƿҧ��ҧ� �ҡʶҹ��ó����͢�������ҡ���� ��йҹҷ�ȹ�", "�վĵԡ���㹡�õ�駢��ʧ��� ����ѧࡵ �������԰ҹ��ҧ� ��ʹ���վĵԡ���㹡�ê�ҧ�ѧࡵ����Ǵ���� ��������¹�ŧ �����Դ���� ��Ф�����仵�ҧ� ���͹�令Ҵ��ó� �������� ��л����Թ�˵ء�ó� ����ͧ��� ʶҹ��ó� ������ �����觵�ҧ� ����Դ��������ҧ�١��ͧ", "��õ��˹ѡ�����������͡�����ͻѭ���ػ��ä����Ҩ�Դ����͹Ҥ� ����ҧἹ ŧ��͡�зӡ����������������ª��ҡ�͡�� ���ͻ�ͧ�ѹ�ѭ�� ��ʹ����ԡ�ԡĵԵ�ҧ� ������͡��", "��õ��˹ѡ���Ͷ֧����������������ҧ��Ѿ�ҡ� (������ҳ ���� ���ѧ������ͧ��� �ػ�ó� ���) ���ŧ�ع����ͷ�����û�Ժѵ���áԨ (Input) �Ѻ���Ѿ������ (Output) ��о�������Ѻ��ا����Ŵ��鹵͹��û�Ժѵԧҹ ���;Ѳ������û�Ժѵԧҹ�Դ���������������ջ���Է���Ҿ�٧�ش �Ҩ��������֧��������ö㹡�èѴ�����Ӥѭ㹡�������� ��Ѿ�ҡ� ��Т��������ҧ������� ��л����Ѵ���������٧�ش", "ਵ�ҷ��СӡѺ����������������˹��§ҹ��蹻�Ժѵ���������ҵðҹ ������º��ͺѧ�Ѻ����˹���� ��������ӹҨ�������º ������ ���͵����ѡ�Ƿҧ��ԪҪվ�ͧ��������������ҧ�����������ջ���Է���Ҿ����觻���ª��ͧͧ��� �ѧ�� ��л������������Ӥѭ ��������ö����Ҩ����֧����׹��Ѵ���觷��١��ͧ��Ф����索Ҵ㹡�èѴ��áѺ�ؤ������˹��§ҹ����ҽ׹��ࡳ�� ����º�����ҵðҹ��������", "��������ö㹡���ҧἹ���ҧ����ѡ����������ö��任�Ժѵ����ԧ��ж١��ͧ ������¤������������ͧ෤����� �к� ��кǹ��÷ӧҹ ����ҵðҹ��÷ӧҹ�ͧ����Тͧ˹��§ҹ���� �������Ǣ�ͧ", "��õ��˹ѡ ���� ����Ѻ ����Դ�͡���������� ��ЪҪ� ���͢��� ������ؤ�� ����˹��§ҹ��ҧ� ���������ǹ����㹡�ô��Թ�ҹ�ͧ˹��§ҹ ����ͧ��� �������ҧ��������������Դ��кǹ�����С�䡡������ǹ�����ͧ�ء�Ҥ��ǹ���ҧ���ԧ�������׹", "��������ö㹡���ѡ��������ҧ���͢��¾ѹ��Ե��ԧ���ط�� (�� ����Сͺ��� ʶҺѹ����֡�� ���˹�ҷ���Ҥ�Ѱ���� ���͢��¡������áԨ ���������֡�����ͼ������Ǫҭ ����� �繵�) �������׹��С������Դ�����������㹡��������ҧ����ª���٧�ش�����ѹ", "������ҷ��Ż���С��ط���ҧ� 㹡��������� �è� ������������������蹴��Թ����� �����赹����˹��§ҹ���ʧ��", "��������ö㹡���Ѻ�ѧ������㨺ؤ������ʶҹ��ó� ��о�������л�Ѻ����¹����ʹ���ͧ�Ѻʶҹ��ó����͡�����������ҡ���� 㹢�з���ѧ����Ժѵԧҹ�����ҧ�ջ���Է���Ҿ��к���ؼŵ��������·�������", "��������ö㹡�÷��й��ʹͷҧ���͡ (Option) �����Ƿҧ��ѭ�� (Solution) �������ҧ��ѵ���� ���� ����������ҧ��ä�Ԩ���������������� �����繻���ª����ͧ���", "���������������ö����ء�����������ѹ��������§�ͧ෤����� �к� ��кǹ��÷ӧҹ ����ҵðҹ��÷ӧҹ�ͧ����Тͧ˹��§ҹ���� �������Ǣ�ͧ ���ͻ���ª��㹡�û�Ժѵ�˹�ҷ��������ؼ� �������㨹������֧��������ö㹡���ͧ�Ҿ�˭� (Big Picture) ��С�äҴ��ó��������������ͧ�Ѻ�������¹�ŧ�ͧ��觵�ҧ� ����к���С�кǹ��÷ӧҹ", "��������ö㹡���ҧἹ���ҧ����ѡ��� �����������ö��任�Ժѵ����ԧ��ж١��ͧ ����֧��������ö㹡�ú����èѴ����ç��õ�ҧ� 㹤����Ѻ�Դ�ͺ�������ö�����������·���˹�������ҧ�ջ���Է���Ҿ�٧�ش", 
										"�������������л�Ժѵԧҹ���١��ͧ�ú��ǹ��ʹ��Ŵ��ͺ����ͧ����Ҩ���Դ��� ����֧��äǺ�����Ǩ������ҹ��仵��Ἱ����ҧ������ҧ�١��ͧ�Ѵਹ" );
			}
			for ( $i=0; $i<count($code); $i++ ) { 
				$cp_model = substr($code[$i],0,1);
				$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_MEANING, CP_MODEL, CP_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE)
								  VALUES ('$code[$i]', '$desc[$i]', '$meaning[$i]', $cp_model , 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			} // end for

			add_field("PER_LEVEL", "LEVEL_NAME","VARCHAR", "100", "NULL");
			add_field("PER_LEVEL", "PER_TYPE","SINGLE", "1", "NULL");
			add_field("PER_LAYER", "LAYER_SALARY_MIN","NUMBER", "16,2", "NULL");
			add_field("PER_LAYER", "LAYER_SALARY_MAX","NUMBER", "16,2", "NULL");
			add_field("PER_TRAINING", "TRN_NO","VARCHAR", "15", "NULL");

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_POSITION ALTER POS_CHANGE_DATE NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POSITION MODIFY POS_CHANGE_DATE NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_POSITION MODIFY POS_CHANGE_DATE NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POSITION SET POS_NO = TRIM(POS_NO) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POSITIONHIS SET POH_POS_NO = TRIM(POH_POS_NO) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_PERSONAL", "LEVEL_NO_SALARY","VARCHAR", "10", "NULL");

			$cmd = " UPDATE PER_PERSONAL SET LEVEL_NO_SALARY = LEVEL_NO WHERE LEVEL_NO_SALARY IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_COMDTL", "LEVEL_NO_SALARY","VARCHAR", "10", "NULL");
			$cmd = " UPDATE PER_COMDTL SET LEVEL_NO_SALARY = LEVEL_NO WHERE LEVEL_NO_SALARY IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_MOVE_REQ", "ORG_ID_REF_1","INTEGER", "10", "NULL");
			add_field("PER_MOVE_REQ", "ORG_ID_REF_2","INTEGER", "10", "NULL");
			add_field("PER_MOVE_REQ", "ORG_ID_REF_3","INTEGER", "10", "NULL");

			$cmd = " SELECT MV_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3 FROM PER_MOVE_REQ ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			while($data = $db_dpis->get_array()){
				$MV_ID = $data[MV_ID];
				$ORG_ID_1 = $data[ORG_ID_1];
				$ORG_ID_2 = $data[ORG_ID_2];
				$ORG_ID_3 = $data[ORG_ID_3];
				if ($ORG_ID_1) {
					$cmd = " SELECT ORG_ID_REF FROM PER_ORG WHERE ORG_ID = $ORG_ID_1 ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
					$data1 = $db_dpis1->get_array();
					$ORG_ID_REF_1 = $data1[ORG_ID_REF];
					if ($ORG_ID_REF_1) {
						$cmd = " UPDATE PER_MOVE_REQ SET ORG_ID_REF_1 = $ORG_ID_REF_1 WHERE MV_ID = $MV_ID ";
						$db_dpis1->send_cmd($cmd);
						//$db_dpis1->show_error();
					} // end if
				} // end if
				if ($ORG_ID_2) {
					$cmd = " SELECT ORG_ID_REF FROM PER_ORG WHERE ORG_ID = $ORG_ID_2 ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
					$data1 = $db_dpis1->get_array();
					$ORG_ID_REF_2 = $data1[ORG_ID_REF];
					if ($ORG_ID_REF_2) {
						$cmd = " UPDATE PER_MOVE_REQ SET ORG_ID_REF_2 = $ORG_ID_REF_2 WHERE MV_ID = $MV_ID ";
						$db_dpis1->send_cmd($cmd);
						//$db_dpis1->show_error();
					} // end if
				} // end if
				if ($ORG_ID_3) {
					$cmd = " SELECT ORG_ID_REF FROM PER_ORG WHERE ORG_ID = $ORG_ID_3 ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
					$data1 = $db_dpis1->get_array();
					$ORG_ID_REF_3 = $data1[ORG_ID_REF];
					if ($ORG_ID_REF_3) {
						$cmd = " UPDATE PER_MOVE_REQ SET ORG_ID_REF_3 = $ORG_ID_REF_3 WHERE MV_ID = $MV_ID ";
						$db_dpis1->send_cmd($cmd);
						//$db_dpis1->show_error();
					} // end if
				} // end if
			} // end while						

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_ACTIVE) 
							  VALUES ('9999', '��. 99.9', '����觻�Ѻ�ѭ���Թ��͹', '05', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POSITION SET POS_MGTSALARY = 0 WHERE POS_MGTSALARY IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			
			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE  PER_PRENAME DROP CONSTRAINT INXU1_PER_PRENAME ";
			elseif($DPISDB=="oci8") 
				$cmd = " DROP INDEX INXU1_PER_PRENAME ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE  PER_PERSONAL SET PER_ORDAIN = 0 WHERE PER_ORDAIN IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE  PER_PERSONAL SET PER_SOLDIER = 0 WHERE PER_SOLDIER IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE  PER_PERSONAL SET PER_MEMBER = 0 WHERE PER_MEMBER IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " CREATE TABLE PER_SUM_DTL10(
				SUM_ID INTEGER NOT NULL,	
				PL_CODE VARCHAR(10) NOT NULL,	
				LEVEL_NO VARCHAR(10) NOT NULL,	
				EL_CODE VARCHAR(10) NOT NULL,	
				SUM_QTY_M INTEGER NULL,	
				SUM_QTY_F INTEGER NULL,	
				UPDATE_USER INTEGER NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_SUM_DTL10 PRIMARY KEY (SUM_ID, PL_CODE, LEVEL_NO, EL_CODE)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE PER_SUM_DTL10(
				SUM_ID NUMBER(10) NOT NULL,	
				PL_CODE VARCHAR2(10) NOT NULL,	
				LEVEL_NO VARCHAR2(10) NOT NULL,	
				EL_CODE VARCHAR2(10) NOT NULL,	
				SUM_QTY_M NUMBER(10) NULL,	
				SUM_QTY_F NUMBER(10) NULL,	
				UPDATE_USER NUMBER(11) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				CONSTRAINT PK_PER_SUM_DTL10 PRIMARY KEY (SUM_ID, PL_CODE, LEVEL_NO, EL_CODE)) ";
			elseif($DPISDB=="mysql")
				$cmd = " CREATE TABLE PER_SUM_DTL10(
				SUM_ID INTEGER(10) NOT NULL,	
				PL_CODE VARCHAR(10) NOT NULL,	
				LEVEL_NO VARCHAR(10) NOT NULL,	
				EL_CODE VARCHAR(10) NOT NULL,	
				SUM_QTY_M INTEGER(10) NULL,	
				SUM_QTY_F INTEGER(10) NULL,	
				UPDATE_USER INTEGER(11) NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_SUM_DTL10 PRIMARY KEY (SUM_ID, PL_CODE, LEVEL_NO, EL_CODE)) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_STATUS (PS_CODE, PS_NAME, PS_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('001', '��Ѵ��з�ǧ������º���', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " UPDATE PER_MGT SET PS_CODE = '001' WHERE PM_CODE IN ('0106', '0108', '0109') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_STATUS (PS_CODE, PS_NAME, PS_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('002', '͸Ժ��������º���', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " UPDATE PER_MGT SET PS_CODE = '002' WHERE PM_CODE IN ('0357', '0278', '0282') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_STATUS (PS_CODE, PS_NAME, PS_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('003', '�ͧ��Ѵ��з�ǧ������º���', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " UPDATE PER_MGT SET PS_CODE = '003' WHERE PM_CODE IN ('0266', '0267', '0268') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_STATUS (PS_CODE, PS_NAME, PS_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('004', '�������Ҫ��èѧ��Ѵ', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " UPDATE PER_MGT SET PS_CODE = '004' WHERE PM_CODE IN ('0233') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_STATUS (PS_CODE, PS_NAME, PS_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('005', '�͡�Ѥ��Ҫ�ٵ', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " UPDATE PER_MGT SET PS_CODE = '005' WHERE PM_CODE IN ('0362', '0363', '0364', '0365') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_STATUS (PS_CODE, PS_NAME, PS_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('006', '����Ǩ�Ҫ����дѺ��з�ǧ', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " UPDATE PER_MGT SET PS_CODE = '006' WHERE PM_CODE IN ('0216', '0218', '0219') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_STATUS (PS_CODE, PS_NAME, PS_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('007', '���� (���˹觻������������дѺ�٧)', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_STATUS (PS_CODE, PS_NAME, PS_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('008', '�ͧ͸Ժ��������º���', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " UPDATE PER_MGT SET PS_CODE = '008' WHERE PM_CODE IN ('0273', '0274', '0276') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_STATUS (PS_CODE, PS_NAME, PS_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('009', '�ͧ�������Ҫ��èѧ��Ѵ������º���', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " UPDATE PER_MGT SET PS_CODE = '009' WHERE PM_CODE IN ('0269') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_STATUS (PS_CODE, PS_NAME, PS_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('010', '�Ѥ��Ҫ�ٵ', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " UPDATE PER_MGT SET PS_CODE = '010' WHERE PM_CODE IN ('0359', '0360') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_STATUS (PS_CODE, PS_NAME, PS_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('011', '���� (���˹觻������������дѺ��)', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_STATUS (PS_CODE, PS_NAME, PS_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('012', '��.�ӹѡ������º���', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " UPDATE PER_MGT SET PS_CODE = '012' WHERE PM_CODE IN ('0251', '0252', '0253') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_STATUS (PS_CODE, PS_NAME, PS_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('013', '���˹����ǹ�Ҫ��û�ШӨѧ��Ѵ', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_STATUS (PS_CODE, PS_NAME, PS_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('014', '���� (���˹觻������ӹ�¡���дѺ�٧)', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_STATUS (PS_CODE, PS_NAME, PS_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('015', '����ӹ�¡�áͧ������º���', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " UPDATE PER_MGT SET PS_CODE = '015' WHERE PM_CODE IN ('0235', '0237', '0249') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_STATUS (PS_CODE, PS_NAME, PS_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('016', '���˹����ǹ�Ҫ��û�ШӨѧ��Ѵ', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_STATUS (PS_CODE, PS_NAME, PS_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('017', '���� (���˹觻������ӹ�¡���дѺ��)', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_LEVEL", "LEVEL_SHORTNAME","VARCHAR", "20", "NULL");
			add_field("PER_LEVEL", "LEVEL_SEQ_NO","INTEGER2", "5", "NULL");

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_PERSONAL ALTER PER_TAXNO VARCHAR(13) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PERSONAL MODIFY PER_TAXNO VARCHAR2(13) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_PERSONAL MODIFY PER_TAXNO VARCHAR(13) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_PERSONAL", "PER_NICKNAME","VARCHAR", "20", "NULL");
			add_field("PER_PERSONAL", "PER_HOME_TEL","VARCHAR", "50", "NULL");
			add_field("PER_PERSONAL", "PER_OFFICE_TEL","VARCHAR", "30", "NULL");
			add_field("PER_PERSONAL", "PER_FAX","VARCHAR", "20", "NULL");
			add_field("PER_PERSONAL", "PER_MOBILE","VARCHAR", "20", "NULL");
			add_field("PER_PERSONAL", "PER_EMAIL","VARCHAR", "30", "NULL");
			add_field("PER_SALARYHIS", "SAH_PERCENT_UP","NUMBER", "5,2", "NULL");
			add_field("PER_SALARYHIS", "SAH_SALARY_UP","NUMBER", "16,2", "NULL");
			add_field("PER_SALARYHIS", "SAH_SALARY_EXTRA","NUMBER", "16,2", "NULL");

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_SALARYHIS ALTER SAH_DOCNO VARCHAR(255) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SALARYHIS MODIFY SAH_DOCNO VARCHAR2(255) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_SALARYHIS MODIFY SAH_DOCNO VARCHAR(255) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_POSITIONHIS ALTER POH_DOCNO VARCHAR(255) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POSITIONHIS MODIFY POH_DOCNO VARCHAR2(255) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_POSITIONHIS MODIFY POH_DOCNO VARCHAR(255) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_POSITIONHIS ALTER POH_REMARK MEMO ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POSITIONHIS MODIFY POH_REMARK VARCHAR2(2000) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_POSITIONHIS MODIFY POH_REMARK TEXT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_TRAIN ALTER TR_NAME MEMO ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_TRAIN MODIFY TR_NAME VARCHAR2(1000) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_TRAIN MODIFY TR_NAME TEXT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_LAYER", "LAYER_SALARY_TEMP","NUMBER", "16,2", "NULL");
			add_field("PER_LAYER_NEW", "LAYER_SALARY_TEMP","NUMBER", "16,2", "NULL");
			add_field("PER_LAYER", "LAYER_SALARY_FULL","NUMBER", "16,2", "NULL");
			add_field("PER_LAYER_NEW", "LAYER_SALARY_FULL","NUMBER", "16,2", "NULL");
			add_field("PER_LAYER", "LAYER_SALARY_MIDPOINT","NUMBER", "16,2", "NULL");
			add_field("PER_LAYER_NEW", "LAYER_SALARY_MIDPOINT","NUMBER", "16,2", "NULL");
			add_field("PER_LAYER", "LAYER_SALARY_MIDPOINT1","NUMBER", "16,2", "NULL");
			add_field("PER_LAYER_NEW", "LAYER_SALARY_MIDPOINT1","NUMBER", "16,2", "NULL");
			add_field("PER_LAYER", "LAYER_SALARY_MIDPOINT2","NUMBER", "16,2", "NULL");
			add_field("PER_LAYER_NEW", "LAYER_SALARY_MIDPOINT2","NUMBER", "16,2", "NULL");

			$cmd = " update PER_CONTROL set CTRL_ALTER = 3 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
?>