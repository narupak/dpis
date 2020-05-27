<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$CREATE_DATE = "NOW()";
	$UPDATE_DATE = date("Y-m-d H:i:s");

	if( $command=='ALTER' ) {
		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('110-520001', 'Power Plant Management ����� TCS of Colombo Plan', '10', '�Թ���', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-01-12', '2009-04-03', NULL, '110', '110', NULL, '�� 1504.1/9901', '2008-09-26', NULL, NULL, NULL, '2008-11-03') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('110-520002', 'Progress to Proficiency English ����� ITEC Programme', '10', '�Թ���', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-01-02', '2009-03-26', NULL, '110', '110', NULL, '�� 1504.1/10205', '2008-10-06', NULL, NULL, NULL, '2008-12-20') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('110-520003', 'Renewable Energy and Energy Efficiency ����� ITEC Programme', '10', '�Թ���', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-01-05', '2009-01-23', NULL, '110', '110', NULL, '�� 1504.1/10348', '2008-10-09', NULL, NULL, NULL, '2008-12-21') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('110-520004', 'Small Hydropower Development', '10', '�Թ���', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	NULL, NULL, NULL, '110', '110', NULL, '�� 1504.1/10616', '2008-10-17', NULL, NULL, NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('110-520005', 'Gene Based Techniques for Research in Biotechnology', '10', '�Թ���', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-03-07', '2009-03-28', '�Թ���', '110', '110', NULL, '�� 1504.1/12579', '2008-12-23', NULL, NULL, NULL, '2009-01-14') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('110-520006', 'Post Graduate Diploma in Hydrology', '20', '�Թ���', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-07-21', '2010-07-20', '�Թ���', '110', '110', NULL, '�� 1504.1/2270', '2009-03-12', NULL, NULL, NULL, '2009-03-30') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('110-520007', 'Master of Tech in Hydrology', '20', '�Թ���', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-07-21', '2011-07-20', '�Թ���', '110', '110', NULL, '�� 1504.1/2270', '2009-03-12', NULL, NULL, NULL, '2009-03-30') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('110-520008', 'Master of Technology in Water Resources Development', '20', '�Թ���', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-07-24', '2011-07-23', '�Թ���', '110', '110', NULL, '�� 1504.1/2262', '2009-03-12', NULL, NULL, NULL, '2009-04-07') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('110-520009', 'Master of Technology in Irrigation Water Management', '20', '�Թ���', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-07-24', '2011-07-23', '�Թ���', '110', '110', NULL, '�� 1504.1/2262', '2009-03-12', NULL, NULL, NULL, '2009-04-07') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('110-520010', 'Post Graduate Diploma in Water Resources Development', '20', '�Թ���', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-07-24', '2010-07-23', '�Թ���', '110', '110', NULL, '�� 1504.1/2262', '2009-03-12', NULL, NULL, NULL, '2009-04-07') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('110-520011', 'Post Graduate Diploma in Irrigation Water Management', '20', '�Թ���', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-07-24', '2009-07-23', '�Թ���', '110', '110', NULL, '�� 1504.1/2262', '2009-03-12', NULL, NULL, NULL, '2009-04-07') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('110-520012', 'Training Methods and Skills for Managers', '10', '�Թ���', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-11-16', '2010-01-08', '�Թ���', '110', '110', NULL, '�� 1504.1/4758', '2009-05-22', NULL, NULL, NULL, '2009-06-29') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('110-520013', 'Capacity Building for Providing Alternative Livelihood Opportunities for Poor', '10', '�Թ���', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, 
						  NULL, NULL, NULL,	'2009-11-16', '2010-01-08', 'National Institute for Micro, Small and Medium Enterprises', '110', '110', NULL, '�� 1504.1/4757', '2009-05-22', NULL, 
						  NULL, NULL, '2009-06-29') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('110-520014', 'Renewable Energy and Energy Efficiency ����� ITEC Programme', '10', '�Թ���', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2010-01-04', '2010-01-22', 'The Energy and Resources Institute', '110', '110', NULL, '�� 1504.1/4432', '2009-05-15', NULL, NULL, NULL, '2009-08-10') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('110-520015', 'Promotion of Micro Enterprises ����� ITEC Programme', '10', '�Թ���', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2010-01-25', '2010-03-19', 'National Institute for Micro, Small and Medium Enterprises', '110', '110', NULL, '�� 1504.1/4813', '2009-05-26', NULL, 
						  NULL, NULL, '2009-09-14') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('110-520016', 'Women and Enterprise Development ����� ITEC Programme', '10', '�Թ���', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-12-01', '2010-01-22', 'The National Institute for Entrepreneurship and Small Business Development �Ҹ�ó�Ѱ�Թ���', '110', '110', NULL, '�� 1504.1/6747', 
						  '2009-07-10', NULL, NULL, NULL, '2009-07-27') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('110-520017', 'Trainers raining on Entrepreneurship and Promotion of Income Generation Activities ����� ITEC Programme', '10', '�Թ���', 1, $SESS_USERID, 
						  '$UPDATE_DATE', '2552', 1, NULL, NULL, NULL,	'2009-12-01', '2010-01-22', 'The National Institute for Entrepreneurship and Small Business Development �Ҹ�ó�Ѱ�Թ���', '110', '110', NULL, '�� 1504.1/6747', '2009-07-10', NULL, NULL, NULL, '2009-07-27') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('124-520001', 'Extension Methodology with Special Focus on Business and Production Planning for ASEAN Member Countries', '10', '������������Ѻ JICA', 1, 
						  $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL, NULL, NULL,	'2009-02-15', '2009-03-14', NULL, '124', '124', NULL, '�� 1504.1/10139', '2008-10-03', NULL, 
						  NULL, NULL, '2008-11-04') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('124-520002', 'Master Degree Programme 2009/2010 ', '21', 'Ἱ����� (CPS) �����Ѻ�������', 1, 
						  $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL, NULL, NULL,	'2009-07-00', NULL, '�.����Է����� Putra Malaysia (UPM)', '124', '124', NULL, '�� 1504.1/12257', '2008-12-12', NULL, NULL, NULL, '2009-01-27') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('124-520003', 'Integrated Environmental Planning and Management', '10', '�������', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL, NULL, NULL,	
						  '2009-06-18', '2009-07-17', 'National Institute of Public Administration (INTAN) ������������', '124', '124', NULL, '�� 1504.1/215', '2009-01-12', NULL, NULL, NULL, 
						  '2009-02-10') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('124-520004', 'Work Study for Productivity Improvement ', '10', 'CPS�����Ѻ�������', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL, NULL, NULL,	
						  '2009-05-25', '2009-06-10', NULL, '124', '124', NULL, '�� 1504.1/968', '2009-02-03', NULL, NULL, NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('124-520005', 'System Development for small and Medium Enterprise', '10', 'CPS�����Ѻ�������', 1, 
						  $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL, NULL, NULL,	'2009-05-25', '2009-06-10', 'Malaysia Productivity Corporation (MPC) ������������', '124', '124', 
						  NULL, '�� 1504.1/942', '2009-02-03', NULL, NULL, NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('140-520001', 'Managing a Market Economy in a Globalizing World', '10', '��', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 0, NULL,
						  NULL, NULL,	'2009-02-16', '2009-02-27', NULL, '140', '140', NULL, '�� 1504.2/10774', '2008-10-21', NULL, NULL, NULL, '2008-12-04') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('140-520002', 'Epidemiology and  Surveilacne of Zoonotic Transboundary Disease', '10', '��', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 0, NULL,
						  NULL, NULL,	'2009-01-26', '2009-03-30', '����ѵ�ᾷ���ʵ�� �.��§����', '140', '140', NULL, '�� 1504.2/10778', '2008-10-21', NULL, NULL, NULL, '2008-12-12') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('140-520003', 'Environment and Health Risk Assessment and Management of Toxic Chemicals', '10', '��', 1, $SESS_USERID, 
						  '$UPDATE_DATE', '2552', 0, NULL, NULL, NULL, '2008-12-01', '2008-12-19', NULL, '140', '140', NULL, '�� 1504.2/11088', '2008-10-31', NULL, NULL, NULL, '2008-11-12') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('140-520004', 'Waste Recycling for Sufficiency Agriculture', '10', '��', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 0, NULL,
						  NULL, NULL,	'2009-03-09', '2009-03-27', '�ٹ�����֡����н֡ͺ���ҹҪҵ� �.�����', '140', '140', NULL, '�� 1504.2/11853', '2008-11-26', NULL, NULL, NULL, '2009-01-23') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('140-520005', 'Managing a competitive Export Business', '10', '��', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 0, NULL,
						  NULL, NULL,	'2009-02-09', '2009-02-20', '��к����ø�áԨ �.�͡�ä��', '140', '140', NULL, '�� 1504.1/12215', '2008-12-11', NULL, NULL, NULL, '2009-01-05') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('140-520006', 'Sustainable Crop Production', '10', '��', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 0, NULL,
						  NULL, NULL,	'2009-05-04', '2009-06-11', '�ӹѡ�ҹ�ɵùҹҪҵ� �.�͹��', '140', '140', NULL, '�� 1504.2/12296', '2008-12-16', NULL, NULL, NULL, '2009-03-02') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('140-520007', 'Income Generation and Poverty Reduction for Development', '10', '��', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 0, NULL,
						  NULL, NULL,	'2009-03-16', '2009-04-10', 'ʶҺѹ��������������;Ѳ�����ɰ�Ԩ�������⢧ �.�͹��', '140', '140', NULL, '�� 1504.2/466', '2009-01-19', 
						  NULL, NULL, NULL, '2009-02-17') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('140-520008', 'Enhancing Competitiveness for Greater Extent of ASEAN and Worldwide Economic Integration', '10', '��', 1, $SESS_USERID, 
						  '$UPDATE_DATE', '2552', 0, NULL, NULL, NULL,	'2009-03-23', '2009-04-30', 'ʶҺѹ�����ҧ��������͡�ä����С�þѲ��', '140', '140', NULL, '�� 1504.2/627', '2009-01-22', 
						  NULL, NULL, NULL, '2009-02-20') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('140-520009', 'Training of Trainers on Community Leadership and Entrepreneurship for Agri-Graduates', '10', '��', 1, $SESS_USERID, 
						  '$UPDATE_DATE', '2552', 0, NULL, NULL, NULL,	'2009-06-21', '2009-07-08', '�ٹ�����֡����С�ý֡ͺ���ҹҪҵ� ����Է����������', '140', '140', NULL, '�� 1504.2/931', 
						  '2009-02-03', NULL, NULL, NULL, '2009-04-17') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('140-520010', 'Diversified Farming Practices Using Participatory Approach for Food Security and Safety', '10', '��', 1, $SESS_USERID, 
						  '$UPDATE_DATE', '2552', 0, NULL, NULL, NULL,	'2009-06-09', '2009-06-20', '����ɵ���ʵ�� ����Է�������§����', '140', '140', NULL, '�� 1504.2/3665', '2009-04-27', 
						  NULL, NULL, NULL, '2009-05-06') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('140-520011', 'Community-Based Micro-Finance and Income Generation Management for Poverty Alleviation', '10', '��', 1, $SESS_USERID, 
						  '$UPDATE_DATE', '2552', 0, NULL, NULL, NULL,	'2009-06-01', '2009-06-10', '������ɰ��ʵ�� ����Է������ɵ���ʵ��', '140', '140', NULL, '�� 1504.2/3741', 
						  '2009-04-28', NULL, NULL, NULL, '2009-05-13') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('140-520012', 'Strengthening Bio-Control Research and Extension', '10', '��', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 0, NULL,
						  NULL, NULL,	'2009-06-15', '2009-06-26', '����Ԫҡ���ɵ�', '140', '140', NULL, '�� 1504.2/5199', '2009-06-02', NULL, NULL, NULL, '2009-06-08') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('140-520013', 'Climate Change:Present and Future Challenges/Opportunities for Vulnerablr Asia-Pacific Countries', '10', '��', 1, $SESS_USERID, 
						  '$UPDATE_DATE', '2552', 0, NULL, NULL, NULL,	'2009-07-09', '2009-07-24', '�ٹ���Ԩ����н֡ͺ����ҹ����Ǵ����', '140', '140', NULL, '�� 1504.2/6239', '2009-06-25', 
						  NULL, NULL, NULL, '2009-06-30') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('140-520014', 'Grassroots Economic Development with One Tambon One Product (OTOP)', '10', '��', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 0, NULL,
						  NULL, NULL,	'2009-09-21', '2009-10-30', '����Է����� �ɵ���ʵ��', '140', '140', NULL, '�� 1504.2/7674', '2009-08-07', NULL, NULL, NULL, '2009-08-25') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('115-520001', 'Project Management for E-Government Promotion', '10', '�����', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-03-10', '2009-06-26', 'JICA Okinawa ����� �����', '115', '115', NULL, '��.1504.1/10964', '2008-10-29', NULL, NULL, NULL, '2008-12-01') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('115-520002', 'Organic Agriculture Technology for Environmental Conservation', '10', '�����', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-02-18', '2009-11-21', 'JICA Chubu ����ȭ����', '115', '115', NULL, '��.1504.1/12005', '2008-12-04', NULL, NULL, NULL, '2008-12-15') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('115-520003', 'Capacity Building of Local Government for Sustainable Fishery Development', '10', '�����', 1, $SESS_USERID, '$UPDATE_DATE', 
						  '2552', 1, NULL, NULL, NULL,	'2009-03-25', '2009-06-20', 'JICA Chubu ����ȭ����', '115', '115', NULL, '�� 1504.1/575', '2009-01-22', NULL, NULL, NULL, '2009-02-02') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('115-520004', 'Advance Bioindustry', '10', '�����', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-05-11', '2009-07-18', 'JICA Chubu ����ȭ����', '115', '115', NULL, '�� 1504.1/1317', '2009-02-13', NULL, NULL, NULL, '2009-03-09') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('115-520005', 'Advance Freshwater Aquaculture', '10', '�����', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-05-31', '2009-07-18', 'ʶҹ��Ԩ�¡���������§�ѵ���Ө״ �ҧ��', '115', '140', NULL, '�� 1501.2/2544', '2009-03-18', NULL, NULL, NULL, '2009-04-27') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('115-520006', 'Information Security for E-Government Promotion', '10', '�����', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-08-11', '2009-12-22', 'JICA Okinawa ����� �����', '115', '115', NULL, '�� 1504.1/2715', '2009-03-23', NULL, NULL, NULL, '2009-05-07') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('115-520007', 'Sustainable Livestock Production System', '10', '�����', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-07-20', '2009-09-19', 'JICA Obihiro ����� �����', '115', '115', NULL, '�� 1504.1/3977', '2009-05-01', NULL, NULL, NULL, '2009-05-29') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('115-520008', 'Expert on Flood-related Disaster Mitigation', '10', '�����', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-09-28', '2010-09-18', 'JICA Tsukuba ����� �����', '115', '115', NULL, '�� 1504.1/4150', '2009-05-07', NULL, NULL, NULL, '2009-05-29') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('115-520009', 'Plant Variety Protection', '10', '�����', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-08-23', '2009-12-31', 'JICA Tsukuba ����� �����', '115', '115', NULL, '�� 1504.1/4780', '2009-05-25', NULL, NULL, NULL, '2009-06-12') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('115-520010', 'Water Environmental Monitoring', '10', '�����', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-09-06', '2009-10-24', 'JICA Tokyo ����� �����', '115', '115', NULL, '�� 1504.1/6041', '2009-06-19', NULL, NULL, NULL, '2009-07-13') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('115-520011', 'Modernization of Irrigation Water Management for Sustainable Development', '10', '�����', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-09-07', '2009-10-02', 'JICA Tokyo ����� �����', '115', '115', NULL, '�� 1502.2/5719', '2009-06-15', NULL, NULL, NULL, '2009-07-24') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('115-520012', 'Enhancement of the Governmental Capacity on Water Environment in Asia Countries', '10', '�����', 1, $SESS_USERID, 
						  '$UPDATE_DATE', '2552', 1, NULL, NULL, NULL,	'2009-11-23', '2009-12-05', 'JICA Yokohama ����� �����', '115', '115', NULL, '�� 1504.1/7599', '2009-08-05', 
						  NULL, NULL, NULL, '2009-09-14') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('115-520013', 'Non-Conventional Water Resources and Environmental Management in Water Resources Countries', '10', '�����', 1, $SESS_USERID, 
						  '$UPDATE_DATE', '2552', 1, NULL, NULL, NULL,	'2009-11-30', '2009-12-11', '�Ҹ�ó�Ѱ�ԧ�����', '115', '136', NULL, '�� 1504.1/9850', '2009-10-12', NULL, NULL, NULL, 
						  '2009-10-22') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('115-520014', 'Japanese ODA Loans', '10', '�����', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2010-02-14', '2010-02-27', 'JICA Tokyo ����� �����', '115', '115', NULL, '�� 1504.1/10265', '2009-10-22', NULL, NULL, NULL, '2009-11-02') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('115-520015', 'Project Management for E-Government Promotion', '10', '�����', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2010-03-09', '2010-06-25', 'JICA Okinawa ����� �����', '115', '115', NULL, '�� 1504.1/9830', '2009-10-12', NULL, NULL, NULL, '2009-11-10') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, 
						  SCH_START_DATE2, SCH_END_DATE2, SCH_PLACE2, SCH_DEAD_LINE)
						  VALUES ('231-520001', 'Organic Agriculture Development', '10', '���ഹ', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-07-31', '2009-08-25', '� �Ҫ�ҳҨѡ����ഹ', '231', '231', NULL, '', '2008-12-04', NULL, NULL, NULL, '2009-03-04', '2009-03-17', '��', '2008-12-02') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, 
						  SCH_START_DATE2, SCH_END_DATE2, SCH_PLACE2, SCH_DEAD_LINE)
						  VALUES ('231-520002', 'Genetic Resources and Intellectual Property Rights', '10', '���ഹ', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-05-04', '2009-05-22', '� �Ҫ�ҳҨѡ����ഹ', '231', '231', NULL, '�� 1504.1/11832', '2008-11-26', NULL, NULL, NULL, 
						  '2010-01-24', '2010-01-29', '����ȷ�������Ѵͺ��', '2008-12-23') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, 
						  SCH_START_DATE2, SCH_END_DATE2, SCH_PLACE2, SCH_DEAD_LINE)
						  VALUES ('231-520003', 'Management of Hydro Power Development and Use', '10', '���ഹ', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-09-05', '2009-10-02', '� �Ҫ�ҳҨѡ����ഹ', '231', '231', NULL, '�� 1504.1/11854', '2008-11-26', NULL, NULL, NULL, 
						  '2010-04-12', '2010-04-24', '�������ѧ', '2009-01-26') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, 
						  SCH_START_DATE2, SCH_END_DATE2, SCH_PLACE2, SCH_DEAD_LINE)
						  VALUES ('231-520004', 'Integrated Water Resources Management', '10', '���ഹ', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-08-10', '2009-09-01', '� �Ҫ�ҳҨѡ����ഹ', '231', '231', NULL, '�� 1504.1/430', '2009-01-16', NULL, NULL, NULL, 
						  '2009-11-30', '2009-12-11', '� �Ҹ�ó�Ѱ��ЪҸԻ�»�ЪҪ����', '2009-02-16') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, 
						  SCH_START_DATE2, SCH_END_DATE2, SCH_PLACE2, SCH_DEAD_LINE)
						  VALUES ('231-520005', 'Plant Breeding and Seed Production', '10', '���ഹ', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-09-07', '2009-10-01', '� �Ҫ�ҳҨѡ����ഹ', '231', '231', NULL, '�� 1504.1/2650', '2009-03-20', NULL, NULL, NULL, 
						  '2010-03-15', '2010-03-26', '����ȷ�������Ѵͺ��', '2009-04-07') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, 
						  SCH_START_DATE2, SCH_END_DATE2, SCH_PLACE2, SCH_DEAD_LINE)
						  VALUES ('231-520006', 'Quality Infrastructure for Food Safety', '10', '���ഹ', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-09-20', '2009-10-16', '� �Ҫ�ҳҨѡ����ഹ', '231', '231', NULL, '�� 1504.1/2982', '2009-03-31', NULL, NULL, NULL, 
						  NULL, NULL, '����ȷ�������Ѵͺ��', '2009-06-08') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, 
						  SCH_START_DATE2, SCH_END_DATE2, SCH_PLACE2, SCH_DEAD_LINE)
						  VALUES ('231-520007', 'Transboundary Water Management ��ǧ��� 1 ��� ��ǧ��� 2', '10', '���ഹ', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2010-04-12', '2010-04-23', '� �Ҹ�ó�Ѱ������ԡ����Ҫ�ҳҨѡ���ҫ��Ź��', '231', '231', NULL, '�� 1504.1/9468', '2009-09-30', NULL, NULL, NULL, 
						  '2010-06-07', '2010-06-11', ' �Ҫ�ҳҨѡ����ഹ', '2009-10-16') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('119-520001', 'Capacity Development for Agricultural Cooperative Federation', '10', '�Ҹ�ó�Ѱ�����', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2008-06-12', '2008-06-28', '�Ҹ�ó�Ѱ�����', '119', '119', NULL, '�� 1504.1/2809', '2008-04-02', NULL, NULL, NULL, '2008-04-11') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('119-520002', 'Marine Environment Protection (APEC Joint Training)', '10', '�Ҹ�ó�Ѱ�����', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2008-06-29', '2008-07-16', 'Korea Ocean Research and Development Institute �Ҹ�ó�Ѱ�����', '119', '119', NULL, '�� 1504.1/3223', '2008-04-10', 
						  NULL, NULL, NULL, '2008-04-22') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('119-520003', 'Environmental Impact Assessment', '10', '�Ҹ�ó�Ѱ�����', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2008-09-16', '2008-10-02', 'Korea Envirnment Institute �Ҹ�ó�Ѱ�����', '119', '119', NULL, '�� 1504.1/3789', '2008-04-28', NULL, NULL, NULL, '2008-05-30') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('119-520004', 'Economic Empowerment for Rural Women', '10', '�Ҹ�ó�Ѱ�����', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2008-10-06', '2008-10-25', 'Asian Pacific Women s Information Network Center �Ҹ�ó�Ѱ�����', '119', '119', NULL, '�� 1504.1/4191', '2008-05-08', 
						  NULL, NULL, NULL, '2008-06-06') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('614-520001', 'Animal Production & Health', '10', '���Ի��', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-10-01', '2009-12-15', '���Ի��', '614', '614', NULL, '�� 1504.1/13030', '2008-12-30', NULL, NULL, NULL, '2009-02-27') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('614-520002', 'Land and Water Management', '10', '���Ի��', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-10-01', '2009-12-15', '���Ի��', '614', '614', NULL, '�� 1504.1/8159', '2009-08-20', NULL, NULL, NULL, '2009-10-17') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('614-520003', 'Cotton Production & technology', '10', '���Ի��', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-04-01', '2009-06-15', '���Ի��', '614', '614', NULL, '�� 1504.1/8131', '2009-08-08', NULL, NULL, NULL, '2009-10-24') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('614-520004', 'Fish Culture Development', '10', '���Ի��', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-10-01', '2009-12-15', '���Ի��', '614', '614', NULL, '�� 1504.1/8131', '2009-08-08', NULL, NULL, NULL, '2009-10-24') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('614-520005', 'Land and Water Management', '10', '���Ի��', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-10-01', '2009-12-15', '���Ի��', '614', '614', NULL, '�� 1504.1/8786', '2009-09-04', NULL, NULL, NULL, '2009-10-30') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('614-520006', 'Agricultural Services', '10', '���Ի��', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2010-04-01', '2010-06-15', '���Ի��', '614', '614', NULL, '�� 1504.1/8791', '2009-09-04', NULL, NULL, NULL, '2009-10-30') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('614-520007', 'Poultry Production & Health', '10', '���Ի��', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2010-07-10', '2010-09-25', '���Ի��', '614', '614', NULL, '�� 1504.1/8789', '2009-09-04', NULL, NULL, NULL, '2009-10-30') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('614-520008', 'Vegetable Production', '10', '���Ի��', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2010-02-15', '2010-04-30', '���Ի��', '614', '614', NULL, '�� 1504.1/8781', '2009-09-04', NULL, NULL, NULL, '2009-09-18') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('614-520009', 'Cotton Production & technology', '10', '���Ի��', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2010-04-01', '2010-06-15', '���Ի��', '614', '614', NULL, '�� 1504.1/8781', '2009-09-04', NULL, NULL, NULL, '2009-10-02') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('614-520010', 'Fish Culture Development', '10', '���Ի��', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2010-10-01', '2010-12-15', '���Ի��', '614', '614', NULL, '�� 1504.1/8781', '2009-09-04', NULL, NULL, NULL, '2009-10-16') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('114-520001', 'Research & Development of New Concepts in Integrated Pest Management', '10', '��������', 1, $SESS_USERID, 
						  '$UPDATE_DATE', '2552', 1, NULL, NULL, NULL, '2009-05-04', '2009-05-26', 'Hebrew University of Jerusalem �������������', '114', '114', NULL, '�� 1504.1/1397', 
						  '2009-02-16', NULL, NULL, NULL, '2009-03-10') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('114-520002', 'Water Management and Modern Irrigation Technologies', '10', '��������', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL, 
						  NULL, NULL, '2009-05-04', '2009-05-26', '�������������', '114', '114', NULL, '�� 1504.1/1633', '2009-02-24', NULL, NULL, NULL, '2009-03-11') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('114-520003', 'Management of Agricultural Resources', '10', '��������', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL, 
						  NULL, NULL, '2009-05-04', '2009-05-28', 'Hebrew University of Jerusalem �������������', '114', '114', NULL, '�� 1504.1/1853', '2009-03-03', 
						  NULL, NULL, NULL, '2009-03-24') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('114-520004', 'Agricultural Engineering Technology', '10', '��������', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL, 
						  NULL, NULL, '2009-11-03', '2009-11-26', 'Agricultural Engineering Organization �������������', '114', '114', NULL, '�� 1504.1/7208', '2009-07-23', 
						  NULL, NULL, NULL, '2009-08-10') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('114-520005', 'International Post-Graduate Course on Water and Health', '10', '��������', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL, 
						  NULL, NULL, '2009-10-26', '2009-11-19', 'Hebrew University of Jerusalem �������������', '114', '114', NULL, '�� 1504.1/8508', '2009-08-31', 
						  NULL, NULL, NULL, '2009-09-15') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('114-520006', 'Intensive Aquaculture Production', '10', '��������', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL, 
						  NULL, NULL, '2009-11-10', '2009-12-02', '�������������', '114', '114', NULL, '�� 1054.1/10047', '2009-09-15', NULL, NULL, NULL, '2009-10-26') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('107-520001', 'Traditional Chinese Veterinary Medicine and Technique', '10', '�չ', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-07-15', '2009-08-03', '�չ', '107', '107', NULL, '�� 1504.1/1850', '2009-03-03', NULL, NULL, NULL, '2009-04-20') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('107-520002', 'Tea Fine Processing Technology', '10', '�չ', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-09-07', '2009-09-26', '�չ', '107', '107', NULL, '�� 1504.1/1947', '2009-03-04', NULL, NULL, NULL, '2009-05-07') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('107-520003', 'Hybrid Rice Technology', '10', '�չ', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-06-10', '2009-06-29', '�չ', '107', '107', NULL, '�� 1504.1/2220', '2009-03-11', NULL, NULL, NULL, '2009-04-03') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('107-520004', 'Small Hydro Power and Sustainable Development of Rural Communities for Officials of Developing Countries', '10', '�չ', 1, $SESS_USERID, 
						  '$UPDATE_DATE', '2552', 1, NULL, NULL, NULL,	'2009-05-08', '2009-05-21', '�չ', '107', '107', NULL, '�� 1504.1/2498', '2009-03-17', NULL, NULL, NULL, '2009-04-07') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('107-520005', 'Small Hydropower Technology', '10', '�չ', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-05-14', '2009-06-24', '�չ', '107', '107', NULL, '�� 1504.1/2519', '2009-03-17', NULL, NULL, NULL, '2009-04-20') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('107-520006', 'Theory and Technology of Food Safety', '10', '�չ', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-10-18', '2009-10-31', '�չ', '107', '107', NULL, '�� 1504.1/2995', '2009-03-31', NULL, NULL, NULL, '2009-05-18') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('107-520007', 'New Technology of Comprehensive Utilization in Agricultural Products Processing', '10', '�չ', 1, $SESS_USERID, 
						  '$UPDATE_DATE', '2552', 1, NULL, NULL, NULL, '2009-06-18', '2009-07-15', '�չ', '107', '107', NULL, '�� 1504.1/2953', '2009-03-31', NULL, NULL, NULL, '2009-05-19') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('107-520008', 'High and New Technology of Biology and Medicine', '10', '�չ', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-07-01', '2009-09-22', '�չ', '107', '107', NULL, '�� 1504.1/3366', '2009-04-21', NULL, NULL, NULL, '2009-05-31') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('107-520009', 'Food Safety Management for Developing Countries', '10', '�չ', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-07-15', '2009-08-11', '�չ', '107', '107', NULL, '�� 1504.1/3985', '2009-05-01', NULL, NULL, NULL, '2009-06-09') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('107-520010', 'Biotechnology Application on Food Industries', '10', '�չ', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-09-01', '2009-11-23', '�չ', '107', '107', NULL, '�� 1504.1/4295', '2009-05-13', NULL, NULL, NULL, '2009-07-13') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('107-520011', 'Corn and Tuber Crops Processing Technology', '10', '�չ', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-07-20', '2009-09-13', '�չ', '107', '107', NULL, '�� 1504.1/4379', '2009-05-14', NULL, NULL, NULL, '2009-06-16') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('107-520012', 'Urban Horticulture and Vegetable Safety, Marketing, Postharvest and Processing Technology', '10', '�չ', 1, $SESS_USERID, 
						  '$UPDATE_DATE', '2552', 1, NULL, NULL, NULL, '2009-08-14', '2009-09-22', '�չ', '107', '107', NULL, '�� 1504.1/5696', '2009-06-15', NULL, NULL, NULL, '2009-07-03') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('107-520013', 'Training Course on Flower Technology', '10', '�չ', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-08-13', '2009-09-21', '�չ', '107', '107', NULL, '�� 1504.1/6970', '2009-07-15', NULL, NULL, NULL, '2009-07-24') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('107-520014', 'JUNCAO (Mushroom) Technology', '10', '�չ', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-09-25', '2009-11-12', '�չ', '107', '107', NULL, '�� 1504.1/7591', '2009-08-04', NULL, NULL, NULL, '2009-08-11') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('107-520015', 'Production and Processing of Tropical Crops', '10', '�չ', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-11-16', '2009-12-13', '�չ', '107', '107', NULL, '�� 1504.1/9084', '2009-09-17', NULL, NULL, NULL, '2009-09-28') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('140-520015', 'MI Sub-regional Research Cycle', '10', 'ʶҺѹ���;Ѳ�����ɰ�Ԩ�������⢧', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-05-25', '2010-03-24', 'ʶҺѹ��������������;Ѳ�����ɰ�Ԩ�������⢧ �ѧ��Ѵ�͹��', '140', '140', NULL, '�� 1504.1/2799', '2009-03-25', 
						  NULL, NULL, NULL, '2009-04-03') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('140-520016', 'Sustainable Rural Develoment', '10', 'ʶҺѹ���;Ѳ�����ɰ�Ԩ�������⢧', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-05-25', '2009-06-19', 'ʶҺѹ��������������;Ѳ�����ɰ�Ԩ�������⢧ ', '140', '140', NULL, '�� 1504.2/3373', '2009-04-21', NULL, NULL, NULL, '2009-05-11') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('140-520017', 'Strategic Human Resource Development for Effective Regional Cooperation', '10', 'ʶҺѹ���;Ѳ�����ɰ�Ԩ�������⢧', 1, $SESS_USERID, 
						  '$UPDATE_DATE', '2552', 1, NULL, NULL, NULL, '2009-07-20', '2009-08-07', 'ʶҺѹ��������������;Ѳ�����ɰ�Ԩ�������⢧ �ѧ��Ѵ�͹��', '140', '140', NULL, '�� 1504.1/4180', 
						  '2009-05-12', NULL, NULL, NULL, '2009-06-02') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('140-520018', 'Effective Project Management for Sustainable Development in the GMS', '10', 'ʶҺѹ���;Ѳ�����ɰ�Ԩ�������⢧', 1, $SESS_USERID, 
						  '$UPDATE_DATE', '2552', 1, NULL, NULL, NULL, '2009-08-17', '2009-09-04', 'ʶҺѹ��������������;Ѳ�����ɰ�Ԩ�������⢧ �ѧ��Ѵ�͹��', '140', '140', NULL, '�� 1504.1/4337', 
						  '2009-05-14', NULL, NULL, NULL, '2009-06-23') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('140-520019', '-', '21', 'UNESCO-Czech', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	NULL, NULL, NULL, '140', '140', NULL, 'ȸ. 0205/3633', '2008-10-20', NULL, NULL, NULL, '2008-12-01') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('140-520020', 'The Great Wall Co-Sponsored Fellowship Programme 2009/2010', '21', 'UNESCO-China', 1, $SESS_USERID, 
						  '$UPDATE_DATE', '2552', 1, NULL, NULL, NULL, NULL, NULL, NULL, '140', '140', NULL, 'ȸ. 0205/570', '2009-02-16', NULL, NULL, NULL, '2009-03-20') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

	}

?>