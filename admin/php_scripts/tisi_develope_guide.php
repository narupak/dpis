<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$CREATE_DATE = "NOW()";
	$UPDATE_DATE = date("Y-m-d H:i:s");

	if( $command=='ALTER' ) {
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(1, 1, '101', 'Time Management', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(2, 2, '101', '��õ���������', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(3, 3, '101', '��û�Ѻ��ا��кǹ��÷ӧҹ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(4, 4, '101', 'Process Re-engineering�', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(5, 5, '101', '෤�Ԥ��õѴ�Թ㨴��� Game Theory', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(6, 1, '102', '������ҧ�Ե�ӹ֡㹧ҹ��ԡ�ü���Ѻ��ԡ��', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(7, 2, '102', '��ÿѧ��С�þѲ�ҡ���������', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(8, 3, '102', '������㨼�����', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(9, 4, '102', '��ú�ԡ�ô���� (Service Solution)', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(10, 5, '102', '����ҧἹ�ԧ���ط��', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(11, 1, '103', '෤�Ԥ����Ң����ŷ���ջ���Է���Ҿ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(12, 2, '103', '��èѴ��ä������', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(13, 3, '103', '��äԴ�ԧ�ѧ������', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(	14, 4, '103', '����������������Ǫҭ㹧ҹ�Ҫվ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(15, 5, '103', 'Learning Organization (LO)', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(16, 1, '104', 'On-the-Job Training', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(17, 2, '104', '�����㹪��Ե��Ш��ѹ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(18, 3, '104', '�����㹪��Ե��Ш��ѹ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(19, 4, '104', '�����㹪��Ե��Ш��ѹ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(20, 5, '104', '�����㹪��Ե��Ш��ѹ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(21, 1, '105', '�����������ҧ�ѡ��Ҿ��÷ӧҹ�繷��', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(22, 2, '105', '������ҧ����ҹ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(23, 3, '105', '��ú����÷���ҹ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(24, 4, '105', 'Strategic Partnership Management', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(25, 5, '105', '��ú����ä����Ѵ���', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(26, 1, '201', 'Time Management', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(27, 2, '201', '��õ��������´����Ը� Scorecard', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(28, 3, '201', '����м��� (Team Leadership)', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(29, 4, '201', '��õѴ�Թ����ҧ�١��ͧ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(30, 5, '201', '������ҧ��������������·�ȹ�', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(31, 1, '202', '෤�Ԥ��ù��ʹ�', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(32, 2, '202', '��ù��ʹ���С�þٴ����Ҹ�ó�����ҧ�վ�ѧ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(33, 3, '202', '���������Ǩ٧�', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(34, 4, '202', '��ú����ù�ѵ�������ҧ��ä�', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(35, 5, '202', '���ӷ��������·�ȹ�', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(36, 1, '203', '����Ѻ�ѧ���������á��ط��ͧ���', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(37, 2, '203', '��äԴ�ԧ���ط�� (Strategic Thinking)', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(38, 3, '203', '��èѴ�ӡ��ط��', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(39, 4, '203', 'Blue Ocean Strategy', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(40, 5, '203', '��ú������ԧ���ط�� �������ͧ���㹡�ú������ԧ���ط��', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(41, 1, '204', '��û�Ѻ���', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(42, 2, '204', '��äԴ�ԧ�ǡ (Positive Thinking)', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(43, 3, '204', '���������Ǩ٧�', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(44, 4, '204', '��ú����á������¹�ŧ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(45, 5, '204', '������������ͺ���ա�ͺ�����Դ ����������Ӥѭ�ͧ��äԴ�ԧ���ط��', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(46, 1, '205', '��þѲ�ҵ������ EQ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(47, 2, '205', '������㨼�����', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(48, 3, '205', '��ú�ԡ�ô���� (Service Solution)', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(49, 4, '205', '��äԴ��оĵԡ���', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(50, 5, '205', '��äԴ��оĵԡ���', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(51, 1, '206', '����͹�ҹ�дѺ��', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(52, 2, '206', '�Ե�Է����С�������˹�ҧҹ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(53, 3, '206', '����͹�ҹ�дѺ�٧', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(54, 4, '206', '����� Mentor ����', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(55, 5, '206', '������ҧ�Ѳ���������͹', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(56, 1, '301', '෤�Ԥ����ҧἹ�ӡѺ�Դ���', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(57, 2, '301', '෤�Ԥ��õԴ�����л����Թ��', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(58, 3, '301', '��ô٧ҹ�˹��§ҹ Best Practice', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(59, 4, '301', '෤�Ԥ��ú����ä����Ѵ���', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(60, 5, '301', '෤�Ԥ��ú����ä����Ѵ���', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(61, 1, '302', '���¸�������Һ�ó�ԪҪվ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(62, 2, '302', '7 �ػ����¢ͧ����ջ���Է�Լ��٧', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(63, 3, '302', '����� Mentor ����', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(64, 4, '302', '����š����¹���������л��ʺ��ó� (Knowledge & Experience Sharing)', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(65, 5, '302', '����š����¹���������л��ʺ��ó� (Knowledge & Experience Sharing)', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(66, 1, '303', '��äԴ�ԧ������������ԧ�ѧ������', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(67, 2, '303', '����������� �����䢻ѭ�� ��С�õѴ�Թ�', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(68, 3, '303', '��äԴ�ԧ���ط�� (Strategic Thinking)', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(69, 4, '303', '�����������ҹ��С�õѴ�Թ����ҧ���к�', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(70, 5, '303', '����š����¹���������л��ʺ��ó� (Knowledge & Experience Sharing)', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(71, 1, '304', '������ѡ�ٵá�ý֡ͺ��: �����������ͧ 5 �. ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(72, 2, '304', 'On-the-job training', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(73, 3, '304', '7 �ػ����¢ͧ����ջ���Է�Լ��٧', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(74, 4, '304', '��ú������ç��� (Project Management)', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(75, 5, '304', '����š����¹���������л��ʺ��ó� (Knowledge & Experience Sharing)', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(76, 1, '305', 'On-the-job training', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(77, 2, '305', '෤�Ԥ��ù��ʹ� ������������ ��Ф��й�', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(78, 3, '305', '������Է�ҡ÷���', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(79, 4, '305', 'Client Relationship Management', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(80, 5, '305', '��äԴ��оĵԡ���', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(81, 1, '306', '����ҧἹ�ҹ���ͧ��', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(82, 2, '306', '����ҧἹ�ҹ��С�õ���������', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(83, 3, '306', '����ҧἹ���ҧ�ջ���Է���Ҿ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(84, 4, '306', 'Scenario Planning ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(85, 5, '306', '����ҧἹ�ԧ���ط��', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(86, 1, '307', '������Ѿ�ҡ����ҧ�������', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(87, 2, '307', 'Process Re-engineering', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(88, 3, '307', '��õԴ�����л����Թ��', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(89, 4, '307', '��ú������ç��� (Project Management)', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(90, 5, '307', '��ú������ç��� (Project Management)', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(91, 1, '308', '��äԴ��оĵԡ���', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(92, 2, '308', '��äԴ��оĵԡ���', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(93, 3, '308', '���������� (Emotional Intelligence)', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(94, 4, '308', '��ú����á������¹�ŧ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(95, 5, '308', '����ҧἹ�ԧ���ط��', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(96, 1, '309', 'On-the-job training', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(97, 2, '309', '��äԴ�ԧ�ѧ������ ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(98, 3, '309', '��û�Ѻ��ا��кǹ��÷ӧҹ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(99, 4, '309', '��äԴ��оĵԡ���', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(100, 5, '309', '��äԴ��оĵԡ���', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(101, 1, '310', '෤�Ԥ��ù��ʹ�', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(102, 2, '310', 'Presentation Skill', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(103, 3, '310', '��ù��ʹ���С�þٴ����Ҹ�ó�����ҧ�վ�ѧ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(104, 4, '310', '���������Ǩ٧�', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(105, 5, '310', '�����������Ҹ�ó�Ẻ����ǹ�����ء�Ҥ��ǹ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(106, 1, '311', '�����Դ���ҧ��ä�', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(107, 2, '311', 'Process Re-Engineering ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(108, 3, '311', '��ú����ù�ѵ�������ҧ��ä�', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(109, 4, '311', '����ҧἹ�ԧ���ط��', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(110, 5, '311', '����ҧἹ�ԧ���ط��', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(111, 1, '312', 'On-the-Job Training', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(112, 2, '312', '��ú����ä����Ѵ���', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(113, 3, '312', 'Scenario Planning ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(114, 4, '312', '�����䢻ѭ�����ҧ���к� ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(115, 5, '312', '��ý֡ͺ��', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(116, 1, '313', '��ÿѧ��С������������ҧ�ջ���Է���Ҿ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(117, 2, '313', 'Building 2-way Communication', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(118, 3, '313', '෤�Ԥ����Ң����ŷ���ջ���Է���Ҿ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(119, 4, '313', '��èѴ��ä������ ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(120, 5, '313', '��ô٧ҹ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(121, 1, '314', '�����¹�ѧ�ҹ ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(122, 2, '314', '����ҧἹ�ҹ��С�õ���������', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(123, 3, '314', '��û�Ѻ��ا��кǹ�ҹ����ջ���Է���Ҿ��觢�� (Process Improvement)', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(124, 4, '314', 'Process Re-Engineering ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(125, 5, '314', '��ý֡ͺ��', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(126, 1, '315', '෤�Ԥ����Ң����ŷ���ջ���Է���Ҿ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(127, 2, '315', '����ͧ�����䢻ѭ�����ҧ���к�', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(128, 3, '315', 'Scenario Planning', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(129, 4, '315', '������ҧ�����Դ���ҧ��ä�', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(130, 5, '315', '��ý֡ͺ��', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
	}

?>