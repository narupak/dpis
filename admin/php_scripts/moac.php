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
						  VALUES ('110-520001', 'Power Plant Management ภายใต้ TCS of Colombo Plan', '10', 'อินเดีย', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-01-12', '2009-04-03', NULL, '110', '110', NULL, 'กต 1504.1/9901', '2008-09-26', NULL, NULL, NULL, '2008-11-03') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('110-520002', 'Progress to Proficiency English ภายใต้ ITEC Programme', '10', 'อินเดีย', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-01-02', '2009-03-26', NULL, '110', '110', NULL, 'กต 1504.1/10205', '2008-10-06', NULL, NULL, NULL, '2008-12-20') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('110-520003', 'Renewable Energy and Energy Efficiency ภายใต้ ITEC Programme', '10', 'อินเดีย', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-01-05', '2009-01-23', NULL, '110', '110', NULL, 'กต 1504.1/10348', '2008-10-09', NULL, NULL, NULL, '2008-12-21') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('110-520004', 'Small Hydropower Development', '10', 'อินเดีย', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	NULL, NULL, NULL, '110', '110', NULL, 'กต 1504.1/10616', '2008-10-17', NULL, NULL, NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('110-520005', 'Gene Based Techniques for Research in Biotechnology', '10', 'อินเดีย', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-03-07', '2009-03-28', 'อินเดีย', '110', '110', NULL, 'กต 1504.1/12579', '2008-12-23', NULL, NULL, NULL, '2009-01-14') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('110-520006', 'Post Graduate Diploma in Hydrology', '20', 'อินเดีย', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-07-21', '2010-07-20', 'อินเดีย', '110', '110', NULL, 'กต 1504.1/2270', '2009-03-12', NULL, NULL, NULL, '2009-03-30') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('110-520007', 'Master of Tech in Hydrology', '20', 'อินเดีย', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-07-21', '2011-07-20', 'อินเดีย', '110', '110', NULL, 'กต 1504.1/2270', '2009-03-12', NULL, NULL, NULL, '2009-03-30') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('110-520008', 'Master of Technology in Water Resources Development', '20', 'อินเดีย', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-07-24', '2011-07-23', 'อินเดีย', '110', '110', NULL, 'กต 1504.1/2262', '2009-03-12', NULL, NULL, NULL, '2009-04-07') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('110-520009', 'Master of Technology in Irrigation Water Management', '20', 'อินเดีย', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-07-24', '2011-07-23', 'อินเดีย', '110', '110', NULL, 'กต 1504.1/2262', '2009-03-12', NULL, NULL, NULL, '2009-04-07') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('110-520010', 'Post Graduate Diploma in Water Resources Development', '20', 'อินเดีย', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-07-24', '2010-07-23', 'อินเดีย', '110', '110', NULL, 'กต 1504.1/2262', '2009-03-12', NULL, NULL, NULL, '2009-04-07') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('110-520011', 'Post Graduate Diploma in Irrigation Water Management', '20', 'อินเดีย', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-07-24', '2009-07-23', 'อินเดีย', '110', '110', NULL, 'กต 1504.1/2262', '2009-03-12', NULL, NULL, NULL, '2009-04-07') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('110-520012', 'Training Methods and Skills for Managers', '10', 'อินเดีย', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-11-16', '2010-01-08', 'อินเดีย', '110', '110', NULL, 'กต 1504.1/4758', '2009-05-22', NULL, NULL, NULL, '2009-06-29') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('110-520013', 'Capacity Building for Providing Alternative Livelihood Opportunities for Poor', '10', 'อินเดีย', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, 
						  NULL, NULL, NULL,	'2009-11-16', '2010-01-08', 'National Institute for Micro, Small and Medium Enterprises', '110', '110', NULL, 'กต 1504.1/4757', '2009-05-22', NULL, 
						  NULL, NULL, '2009-06-29') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('110-520014', 'Renewable Energy and Energy Efficiency ภายใต้ ITEC Programme', '10', 'อินเดีย', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2010-01-04', '2010-01-22', 'The Energy and Resources Institute', '110', '110', NULL, 'กต 1504.1/4432', '2009-05-15', NULL, NULL, NULL, '2009-08-10') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('110-520015', 'Promotion of Micro Enterprises ภายใต้ ITEC Programme', '10', 'อินเดีย', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2010-01-25', '2010-03-19', 'National Institute for Micro, Small and Medium Enterprises', '110', '110', NULL, 'กต 1504.1/4813', '2009-05-26', NULL, 
						  NULL, NULL, '2009-09-14') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('110-520016', 'Women and Enterprise Development ภายใต้ ITEC Programme', '10', 'อินเดีย', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-12-01', '2010-01-22', 'The National Institute for Entrepreneurship and Small Business Development สาธารณรัฐอินเดีย', '110', '110', NULL, 'กต 1504.1/6747', 
						  '2009-07-10', NULL, NULL, NULL, '2009-07-27') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('110-520017', 'Trainers raining on Entrepreneurship and Promotion of Income Generation Activities ภายใต้ ITEC Programme', '10', 'อินเดีย', 1, $SESS_USERID, 
						  '$UPDATE_DATE', '2552', 1, NULL, NULL, NULL,	'2009-12-01', '2010-01-22', 'The National Institute for Entrepreneurship and Small Business Development สาธารณรัฐอินเดีย', '110', '110', NULL, 'กต 1504.1/6747', '2009-07-10', NULL, NULL, NULL, '2009-07-27') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('124-520001', 'Extension Methodology with Special Focus on Business and Production Planning for ASEAN Member Countries', '10', 'มาเลเซียร่วมกับ JICA', 1, 
						  $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL, NULL, NULL,	'2009-02-15', '2009-03-14', NULL, '124', '124', NULL, 'กต 1504.1/10139', '2008-10-03', NULL, 
						  NULL, NULL, '2008-11-04') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('124-520002', 'Master Degree Programme 2009/2010 ', '21', 'แผนโคลัมโบ (CPS) ร่วมกับมาเลเซีย', 1, 
						  $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL, NULL, NULL,	'2009-07-00', NULL, 'ณ.มหาวิทยาลัย Putra Malaysia (UPM)', '124', '124', NULL, 'กต 1504.1/12257', '2008-12-12', NULL, NULL, NULL, '2009-01-27') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('124-520003', 'Integrated Environmental Planning and Management', '10', 'มาเลเซีย', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL, NULL, NULL,	
						  '2009-06-18', '2009-07-17', 'National Institute of Public Administration (INTAN) ประเทศมาเลเซีย', '124', '124', NULL, 'กต 1504.1/215', '2009-01-12', NULL, NULL, NULL, 
						  '2009-02-10') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('124-520004', 'Work Study for Productivity Improvement ', '10', 'CPSร่วมกับมาเลเซีย', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL, NULL, NULL,	
						  '2009-05-25', '2009-06-10', NULL, '124', '124', NULL, 'กต 1504.1/968', '2009-02-03', NULL, NULL, NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('124-520005', 'System Development for small and Medium Enterprise', '10', 'CPSร่วมกับมาเลเซีย', 1, 
						  $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL, NULL, NULL,	'2009-05-25', '2009-06-10', 'Malaysia Productivity Corporation (MPC) ประเทศมาเลเซีย', '124', '124', 
						  NULL, 'กต 1504.1/942', '2009-02-03', NULL, NULL, NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('140-520001', 'Managing a Market Economy in a Globalizing World', '10', 'ไทย', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 0, NULL,
						  NULL, NULL,	'2009-02-16', '2009-02-27', NULL, '140', '140', NULL, 'กต 1504.2/10774', '2008-10-21', NULL, NULL, NULL, '2008-12-04') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('140-520002', 'Epidemiology and  Surveilacne of Zoonotic Transboundary Disease', '10', 'ไทย', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 0, NULL,
						  NULL, NULL,	'2009-01-26', '2009-03-30', 'คณะสัตวแพทยศาสตร์ ม.เชียงใหม่', '140', '140', NULL, 'กต 1504.2/10778', '2008-10-21', NULL, NULL, NULL, '2008-12-12') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('140-520003', 'Environment and Health Risk Assessment and Management of Toxic Chemicals', '10', 'ไทย', 1, $SESS_USERID, 
						  '$UPDATE_DATE', '2552', 0, NULL, NULL, NULL, '2008-12-01', '2008-12-19', NULL, '140', '140', NULL, 'กต 1504.2/11088', '2008-10-31', NULL, NULL, NULL, '2008-11-12') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('140-520004', 'Waste Recycling for Sufficiency Agriculture', '10', 'ไทย', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 0, NULL,
						  NULL, NULL,	'2009-03-09', '2009-03-27', 'ศูนย์การศึกษาและฝึกอบรมนานาชาติ ม.แม่โจ้', '140', '140', NULL, 'กต 1504.2/11853', '2008-11-26', NULL, NULL, NULL, '2009-01-23') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('140-520005', 'Managing a competitive Export Business', '10', 'ไทย', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 0, NULL,
						  NULL, NULL,	'2009-02-09', '2009-02-20', 'คณะบริหารธุรกิจ ม.หอการค้า', '140', '140', NULL, 'กต 1504.1/12215', '2008-12-11', NULL, NULL, NULL, '2009-01-05') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('140-520006', 'Sustainable Crop Production', '10', 'ไทย', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 0, NULL,
						  NULL, NULL,	'2009-05-04', '2009-06-11', 'สำนักงานเกษตรนานาชาติ ม.ขอนแก่น', '140', '140', NULL, 'กต 1504.2/12296', '2008-12-16', NULL, NULL, NULL, '2009-03-02') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('140-520007', 'Income Generation and Poverty Reduction for Development', '10', 'ไทย', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 0, NULL,
						  NULL, NULL,	'2009-03-16', '2009-04-10', 'สถาบันความร่วมมือเพื่อพัฒนาเศรษฐกิจลุ่มน้ำโขง จ.ขอนแก่น', '140', '140', NULL, 'กต 1504.2/466', '2009-01-19', 
						  NULL, NULL, NULL, '2009-02-17') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('140-520008', 'Enhancing Competitiveness for Greater Extent of ASEAN and Worldwide Economic Integration', '10', 'ไทย', 1, $SESS_USERID, 
						  '$UPDATE_DATE', '2552', 0, NULL, NULL, NULL,	'2009-03-23', '2009-04-30', 'สถาบันระหว่างประเทศเพื่อการค้าและการพัฒนา', '140', '140', NULL, 'กต 1504.2/627', '2009-01-22', 
						  NULL, NULL, NULL, '2009-02-20') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('140-520009', 'Training of Trainers on Community Leadership and Entrepreneurship for Agri-Graduates', '10', 'ไทย', 1, $SESS_USERID, 
						  '$UPDATE_DATE', '2552', 0, NULL, NULL, NULL,	'2009-06-21', '2009-07-08', 'ศูนย์การศึกษาและการฝึกอบรมนานาชาติ มหาวิทยาลัยแม่โจ้', '140', '140', NULL, 'กต 1504.2/931', 
						  '2009-02-03', NULL, NULL, NULL, '2009-04-17') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('140-520010', 'Diversified Farming Practices Using Participatory Approach for Food Security and Safety', '10', 'ไทย', 1, $SESS_USERID, 
						  '$UPDATE_DATE', '2552', 0, NULL, NULL, NULL,	'2009-06-09', '2009-06-20', 'คณะเกษตรศาสตร์ มหาวิทยาลัยเชียงใหม่', '140', '140', NULL, 'กต 1504.2/3665', '2009-04-27', 
						  NULL, NULL, NULL, '2009-05-06') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('140-520011', 'Community-Based Micro-Finance and Income Generation Management for Poverty Alleviation', '10', 'ไทย', 1, $SESS_USERID, 
						  '$UPDATE_DATE', '2552', 0, NULL, NULL, NULL,	'2009-06-01', '2009-06-10', 'คณะเศรษฐศาสตร์ มหาวิทยาลัยเกษตรศาสตร์', '140', '140', NULL, 'กต 1504.2/3741', 
						  '2009-04-28', NULL, NULL, NULL, '2009-05-13') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('140-520012', 'Strengthening Bio-Control Research and Extension', '10', 'ไทย', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 0, NULL,
						  NULL, NULL,	'2009-06-15', '2009-06-26', 'กรมวิชาการเกษตร', '140', '140', NULL, 'กต 1504.2/5199', '2009-06-02', NULL, NULL, NULL, '2009-06-08') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('140-520013', 'Climate Change:Present and Future Challenges/Opportunities for Vulnerablr Asia-Pacific Countries', '10', 'ไทย', 1, $SESS_USERID, 
						  '$UPDATE_DATE', '2552', 0, NULL, NULL, NULL,	'2009-07-09', '2009-07-24', 'ศูนย์วิจัยและฝึกอบรมด้านสิ่งแวดล้อม', '140', '140', NULL, 'กต 1504.2/6239', '2009-06-25', 
						  NULL, NULL, NULL, '2009-06-30') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('140-520014', 'Grassroots Economic Development with One Tambon One Product (OTOP)', '10', 'ไทย', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 0, NULL,
						  NULL, NULL,	'2009-09-21', '2009-10-30', 'มหาวิทยาลัย เกษตรศาสตร์', '140', '140', NULL, 'กต 1504.2/7674', '2009-08-07', NULL, NULL, NULL, '2009-08-25') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('115-520001', 'Project Management for E-Government Promotion', '10', 'ญี่ปุ่น', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-03-10', '2009-06-26', 'JICA Okinawa ประเทศ ญี่ปุ่น', '115', '115', NULL, 'กต.1504.1/10964', '2008-10-29', NULL, NULL, NULL, '2008-12-01') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('115-520002', 'Organic Agriculture Technology for Environmental Conservation', '10', 'ญี่ปุ่น', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-02-18', '2009-11-21', 'JICA Chubu ประเทศญี่ปุ่น', '115', '115', NULL, 'กต.1504.1/12005', '2008-12-04', NULL, NULL, NULL, '2008-12-15') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('115-520003', 'Capacity Building of Local Government for Sustainable Fishery Development', '10', 'ญี่ปุ่น', 1, $SESS_USERID, '$UPDATE_DATE', 
						  '2552', 1, NULL, NULL, NULL,	'2009-03-25', '2009-06-20', 'JICA Chubu ประเทศญี่ปุ่น', '115', '115', NULL, 'กต 1504.1/575', '2009-01-22', NULL, NULL, NULL, '2009-02-02') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('115-520004', 'Advance Bioindustry', '10', 'ญี่ปุ่น', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-05-11', '2009-07-18', 'JICA Chubu ประเทศญี่ปุ่น', '115', '115', NULL, 'กต 1504.1/1317', '2009-02-13', NULL, NULL, NULL, '2009-03-09') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('115-520005', 'Advance Freshwater Aquaculture', '10', 'ญี่ปุ่น', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-05-31', '2009-07-18', 'สถานีวิจัยการเพาะเลี้ยงสัตว์น้ำจืด บางไทร', '115', '140', NULL, 'กต 1501.2/2544', '2009-03-18', NULL, NULL, NULL, '2009-04-27') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('115-520006', 'Information Security for E-Government Promotion', '10', 'ญี่ปุ่น', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-08-11', '2009-12-22', 'JICA Okinawa ประเทศ ญี่ปุ่น', '115', '115', NULL, 'กต 1504.1/2715', '2009-03-23', NULL, NULL, NULL, '2009-05-07') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('115-520007', 'Sustainable Livestock Production System', '10', 'ญี่ปุ่น', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-07-20', '2009-09-19', 'JICA Obihiro ประเทศ ญี่ปุ่น', '115', '115', NULL, 'กต 1504.1/3977', '2009-05-01', NULL, NULL, NULL, '2009-05-29') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('115-520008', 'Expert on Flood-related Disaster Mitigation', '10', 'ญี่ปุ่น', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-09-28', '2010-09-18', 'JICA Tsukuba ประเทศ ญี่ปุ่น', '115', '115', NULL, 'กต 1504.1/4150', '2009-05-07', NULL, NULL, NULL, '2009-05-29') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('115-520009', 'Plant Variety Protection', '10', 'ญี่ปุ่น', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-08-23', '2009-12-31', 'JICA Tsukuba ประเทศ ญี่ปุ่น', '115', '115', NULL, 'กต 1504.1/4780', '2009-05-25', NULL, NULL, NULL, '2009-06-12') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('115-520010', 'Water Environmental Monitoring', '10', 'ญี่ปุ่น', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-09-06', '2009-10-24', 'JICA Tokyo ประเทศ ญี่ปุ่น', '115', '115', NULL, 'กต 1504.1/6041', '2009-06-19', NULL, NULL, NULL, '2009-07-13') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('115-520011', 'Modernization of Irrigation Water Management for Sustainable Development', '10', 'ญี่ปุ่น', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-09-07', '2009-10-02', 'JICA Tokyo ประเทศ ญี่ปุ่น', '115', '115', NULL, 'กต 1502.2/5719', '2009-06-15', NULL, NULL, NULL, '2009-07-24') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('115-520012', 'Enhancement of the Governmental Capacity on Water Environment in Asia Countries', '10', 'ญี่ปุ่น', 1, $SESS_USERID, 
						  '$UPDATE_DATE', '2552', 1, NULL, NULL, NULL,	'2009-11-23', '2009-12-05', 'JICA Yokohama ประเทศ ญี่ปุ่น', '115', '115', NULL, 'กต 1504.1/7599', '2009-08-05', 
						  NULL, NULL, NULL, '2009-09-14') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('115-520013', 'Non-Conventional Water Resources and Environmental Management in Water Resources Countries', '10', 'ญี่ปุ่น', 1, $SESS_USERID, 
						  '$UPDATE_DATE', '2552', 1, NULL, NULL, NULL,	'2009-11-30', '2009-12-11', 'สาธารณรัฐสิงค์โปร์', '115', '136', NULL, 'กต 1504.1/9850', '2009-10-12', NULL, NULL, NULL, 
						  '2009-10-22') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('115-520014', 'Japanese ODA Loans', '10', 'ญี่ปุ่น', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2010-02-14', '2010-02-27', 'JICA Tokyo ประเทศ ญี่ปุ่น', '115', '115', NULL, 'กต 1504.1/10265', '2009-10-22', NULL, NULL, NULL, '2009-11-02') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('115-520015', 'Project Management for E-Government Promotion', '10', 'ญี่ปุ่น', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2010-03-09', '2010-06-25', 'JICA Okinawa ประเทศ ญี่ปุ่น', '115', '115', NULL, 'กต 1504.1/9830', '2009-10-12', NULL, NULL, NULL, '2009-11-10') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, 
						  SCH_START_DATE2, SCH_END_DATE2, SCH_PLACE2, SCH_DEAD_LINE)
						  VALUES ('231-520001', 'Organic Agriculture Development', '10', 'สวีเดน', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-07-31', '2009-08-25', 'ณ ราชอาณาจักรสวีเดน', '231', '231', NULL, '', '2008-12-04', NULL, NULL, NULL, '2009-03-04', '2009-03-17', 'ไทย', '2008-12-02') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, 
						  SCH_START_DATE2, SCH_END_DATE2, SCH_PLACE2, SCH_DEAD_LINE)
						  VALUES ('231-520002', 'Genetic Resources and Intellectual Property Rights', '10', 'สวีเดน', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-05-04', '2009-05-22', 'ณ ราชอาณาจักรสวีเดน', '231', '231', NULL, 'กต 1504.1/11832', '2008-11-26', NULL, NULL, NULL, 
						  '2010-01-24', '2010-01-29', 'ประเทศที่ร่วมจัดอบรม', '2008-12-23') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, 
						  SCH_START_DATE2, SCH_END_DATE2, SCH_PLACE2, SCH_DEAD_LINE)
						  VALUES ('231-520003', 'Management of Hydro Power Development and Use', '10', 'สวีเดน', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-09-05', '2009-10-02', 'ณ ราชอาณาจักรสวีเดน', '231', '231', NULL, 'กต 1504.1/11854', '2008-11-26', NULL, NULL, NULL, 
						  '2010-04-12', '2010-04-24', 'แจ้งภายหลัง', '2009-01-26') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, 
						  SCH_START_DATE2, SCH_END_DATE2, SCH_PLACE2, SCH_DEAD_LINE)
						  VALUES ('231-520004', 'Integrated Water Resources Management', '10', 'สวีเดน', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-08-10', '2009-09-01', 'ณ ราชอาณาจักรสวีเดน', '231', '231', NULL, 'กต 1504.1/430', '2009-01-16', NULL, NULL, NULL, 
						  '2009-11-30', '2009-12-11', 'ณ สาธารณรัฐประชาธิปไตยประชาชนลาว', '2009-02-16') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, 
						  SCH_START_DATE2, SCH_END_DATE2, SCH_PLACE2, SCH_DEAD_LINE)
						  VALUES ('231-520005', 'Plant Breeding and Seed Production', '10', 'สวีเดน', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-09-07', '2009-10-01', 'ณ ราชอาณาจักรสวีเดน', '231', '231', NULL, 'กต 1504.1/2650', '2009-03-20', NULL, NULL, NULL, 
						  '2010-03-15', '2010-03-26', 'ประเทศที่ร่วมจัดอบรม', '2009-04-07') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, 
						  SCH_START_DATE2, SCH_END_DATE2, SCH_PLACE2, SCH_DEAD_LINE)
						  VALUES ('231-520006', 'Quality Infrastructure for Food Safety', '10', 'สวีเดน', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-09-20', '2009-10-16', 'ณ ราชอาณาจักรสวีเดน', '231', '231', NULL, 'กต 1504.1/2982', '2009-03-31', NULL, NULL, NULL, 
						  NULL, NULL, 'ประเทศที่ร่วมจัดอบรม', '2009-06-08') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, 
						  SCH_START_DATE2, SCH_END_DATE2, SCH_PLACE2, SCH_DEAD_LINE)
						  VALUES ('231-520007', 'Transboundary Water Management ช่วงที่ 1 และ ช่วงที่ 2', '10', 'สวีเดน', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2010-04-12', '2010-04-23', 'ณ สาธารณรัฐโมซัมบิกและราชอาณาจักรสวาซิแลนด์', '231', '231', NULL, 'กต 1504.1/9468', '2009-09-30', NULL, NULL, NULL, 
						  '2010-06-07', '2010-06-11', ' ราชอาณาจักรสวีเดน', '2009-10-16') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('119-520001', 'Capacity Development for Agricultural Cooperative Federation', '10', 'สาธารณรัฐเกาหลี', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2008-06-12', '2008-06-28', 'สาธารณรัฐเกาหลี', '119', '119', NULL, 'กต 1504.1/2809', '2008-04-02', NULL, NULL, NULL, '2008-04-11') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('119-520002', 'Marine Environment Protection (APEC Joint Training)', '10', 'สาธารณรัฐเกาหลี', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2008-06-29', '2008-07-16', 'Korea Ocean Research and Development Institute สาธารณรัฐเกาหลี', '119', '119', NULL, 'กต 1504.1/3223', '2008-04-10', 
						  NULL, NULL, NULL, '2008-04-22') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('119-520003', 'Environmental Impact Assessment', '10', 'สาธารณรัฐเกาหลี', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2008-09-16', '2008-10-02', 'Korea Envirnment Institute สาธารณรัฐเกาหลี', '119', '119', NULL, 'กต 1504.1/3789', '2008-04-28', NULL, NULL, NULL, '2008-05-30') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('119-520004', 'Economic Empowerment for Rural Women', '10', 'สาธารณรัฐเกาหลี', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2008-10-06', '2008-10-25', 'Asian Pacific Women s Information Network Center สาธารณรัฐเกาหลี', '119', '119', NULL, 'กต 1504.1/4191', '2008-05-08', 
						  NULL, NULL, NULL, '2008-06-06') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('614-520001', 'Animal Production & Health', '10', 'อียิปต์', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-10-01', '2009-12-15', 'อียิปต์', '614', '614', NULL, 'กต 1504.1/13030', '2008-12-30', NULL, NULL, NULL, '2009-02-27') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('614-520002', 'Land and Water Management', '10', 'อียิปต์', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-10-01', '2009-12-15', 'อียิปต์', '614', '614', NULL, 'กต 1504.1/8159', '2009-08-20', NULL, NULL, NULL, '2009-10-17') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('614-520003', 'Cotton Production & technology', '10', 'อียิปต์', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-04-01', '2009-06-15', 'อียิปต์', '614', '614', NULL, 'กต 1504.1/8131', '2009-08-08', NULL, NULL, NULL, '2009-10-24') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('614-520004', 'Fish Culture Development', '10', 'อียิปต์', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-10-01', '2009-12-15', 'อียิปต์', '614', '614', NULL, 'กต 1504.1/8131', '2009-08-08', NULL, NULL, NULL, '2009-10-24') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('614-520005', 'Land and Water Management', '10', 'อียิปต์', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-10-01', '2009-12-15', 'อียิปต์', '614', '614', NULL, 'กต 1504.1/8786', '2009-09-04', NULL, NULL, NULL, '2009-10-30') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('614-520006', 'Agricultural Services', '10', 'อียิปต์', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2010-04-01', '2010-06-15', 'อียิปต์', '614', '614', NULL, 'กต 1504.1/8791', '2009-09-04', NULL, NULL, NULL, '2009-10-30') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('614-520007', 'Poultry Production & Health', '10', 'อียิปต์', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2010-07-10', '2010-09-25', 'อียิปต์', '614', '614', NULL, 'กต 1504.1/8789', '2009-09-04', NULL, NULL, NULL, '2009-10-30') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('614-520008', 'Vegetable Production', '10', 'อียิปต์', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2010-02-15', '2010-04-30', 'อียิปต์', '614', '614', NULL, 'กต 1504.1/8781', '2009-09-04', NULL, NULL, NULL, '2009-09-18') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('614-520009', 'Cotton Production & technology', '10', 'อียิปต์', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2010-04-01', '2010-06-15', 'อียิปต์', '614', '614', NULL, 'กต 1504.1/8781', '2009-09-04', NULL, NULL, NULL, '2009-10-02') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('614-520010', 'Fish Culture Development', '10', 'อียิปต์', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2010-10-01', '2010-12-15', 'อียิปต์', '614', '614', NULL, 'กต 1504.1/8781', '2009-09-04', NULL, NULL, NULL, '2009-10-16') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('114-520001', 'Research & Development of New Concepts in Integrated Pest Management', '10', 'อิสราเอล', 1, $SESS_USERID, 
						  '$UPDATE_DATE', '2552', 1, NULL, NULL, NULL, '2009-05-04', '2009-05-26', 'Hebrew University of Jerusalem ประเทศอิสราเอล', '114', '114', NULL, 'กต 1504.1/1397', 
						  '2009-02-16', NULL, NULL, NULL, '2009-03-10') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('114-520002', 'Water Management and Modern Irrigation Technologies', '10', 'อิสราเอล', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL, 
						  NULL, NULL, '2009-05-04', '2009-05-26', 'ประเทศอิสราเอล', '114', '114', NULL, 'กต 1504.1/1633', '2009-02-24', NULL, NULL, NULL, '2009-03-11') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('114-520003', 'Management of Agricultural Resources', '10', 'อิสราเอล', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL, 
						  NULL, NULL, '2009-05-04', '2009-05-28', 'Hebrew University of Jerusalem ประเทศอิสราเอล', '114', '114', NULL, 'กต 1504.1/1853', '2009-03-03', 
						  NULL, NULL, NULL, '2009-03-24') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('114-520004', 'Agricultural Engineering Technology', '10', 'อิสราเอล', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL, 
						  NULL, NULL, '2009-11-03', '2009-11-26', 'Agricultural Engineering Organization ประเทศอิสราเอล', '114', '114', NULL, 'กต 1504.1/7208', '2009-07-23', 
						  NULL, NULL, NULL, '2009-08-10') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('114-520005', 'International Post-Graduate Course on Water and Health', '10', 'อิสราเอล', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL, 
						  NULL, NULL, '2009-10-26', '2009-11-19', 'Hebrew University of Jerusalem ประเทศอิสราเอล', '114', '114', NULL, 'กต 1504.1/8508', '2009-08-31', 
						  NULL, NULL, NULL, '2009-09-15') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('114-520006', 'Intensive Aquaculture Production', '10', 'อิสราเอล', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL, 
						  NULL, NULL, '2009-11-10', '2009-12-02', 'ประเทศอิสราเอล', '114', '114', NULL, 'กต 1054.1/10047', '2009-09-15', NULL, NULL, NULL, '2009-10-26') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('107-520001', 'Traditional Chinese Veterinary Medicine and Technique', '10', 'จีน', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-07-15', '2009-08-03', 'จีน', '107', '107', NULL, 'กต 1504.1/1850', '2009-03-03', NULL, NULL, NULL, '2009-04-20') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('107-520002', 'Tea Fine Processing Technology', '10', 'จีน', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-09-07', '2009-09-26', 'จีน', '107', '107', NULL, 'กต 1504.1/1947', '2009-03-04', NULL, NULL, NULL, '2009-05-07') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('107-520003', 'Hybrid Rice Technology', '10', 'จีน', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-06-10', '2009-06-29', 'จีน', '107', '107', NULL, 'กต 1504.1/2220', '2009-03-11', NULL, NULL, NULL, '2009-04-03') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('107-520004', 'Small Hydro Power and Sustainable Development of Rural Communities for Officials of Developing Countries', '10', 'จีน', 1, $SESS_USERID, 
						  '$UPDATE_DATE', '2552', 1, NULL, NULL, NULL,	'2009-05-08', '2009-05-21', 'จีน', '107', '107', NULL, 'กต 1504.1/2498', '2009-03-17', NULL, NULL, NULL, '2009-04-07') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('107-520005', 'Small Hydropower Technology', '10', 'จีน', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-05-14', '2009-06-24', 'จีน', '107', '107', NULL, 'กต 1504.1/2519', '2009-03-17', NULL, NULL, NULL, '2009-04-20') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('107-520006', 'Theory and Technology of Food Safety', '10', 'จีน', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-10-18', '2009-10-31', 'จีน', '107', '107', NULL, 'กต 1504.1/2995', '2009-03-31', NULL, NULL, NULL, '2009-05-18') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('107-520007', 'New Technology of Comprehensive Utilization in Agricultural Products Processing', '10', 'จีน', 1, $SESS_USERID, 
						  '$UPDATE_DATE', '2552', 1, NULL, NULL, NULL, '2009-06-18', '2009-07-15', 'จีน', '107', '107', NULL, 'กต 1504.1/2953', '2009-03-31', NULL, NULL, NULL, '2009-05-19') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('107-520008', 'High and New Technology of Biology and Medicine', '10', 'จีน', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-07-01', '2009-09-22', 'จีน', '107', '107', NULL, 'กต 1504.1/3366', '2009-04-21', NULL, NULL, NULL, '2009-05-31') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('107-520009', 'Food Safety Management for Developing Countries', '10', 'จีน', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-07-15', '2009-08-11', 'จีน', '107', '107', NULL, 'กต 1504.1/3985', '2009-05-01', NULL, NULL, NULL, '2009-06-09') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('107-520010', 'Biotechnology Application on Food Industries', '10', 'จีน', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-09-01', '2009-11-23', 'จีน', '107', '107', NULL, 'กต 1504.1/4295', '2009-05-13', NULL, NULL, NULL, '2009-07-13') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('107-520011', 'Corn and Tuber Crops Processing Technology', '10', 'จีน', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-07-20', '2009-09-13', 'จีน', '107', '107', NULL, 'กต 1504.1/4379', '2009-05-14', NULL, NULL, NULL, '2009-06-16') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('107-520012', 'Urban Horticulture and Vegetable Safety, Marketing, Postharvest and Processing Technology', '10', 'จีน', 1, $SESS_USERID, 
						  '$UPDATE_DATE', '2552', 1, NULL, NULL, NULL, '2009-08-14', '2009-09-22', 'จีน', '107', '107', NULL, 'กต 1504.1/5696', '2009-06-15', NULL, NULL, NULL, '2009-07-03') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('107-520013', 'Training Course on Flower Technology', '10', 'จีน', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-08-13', '2009-09-21', 'จีน', '107', '107', NULL, 'กต 1504.1/6970', '2009-07-15', NULL, NULL, NULL, '2009-07-24') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('107-520014', 'JUNCAO (Mushroom) Technology', '10', 'จีน', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-09-25', '2009-11-12', 'จีน', '107', '107', NULL, 'กต 1504.1/7591', '2009-08-04', NULL, NULL, NULL, '2009-08-11') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('107-520015', 'Production and Processing of Tropical Crops', '10', 'จีน', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-11-16', '2009-12-13', 'จีน', '107', '107', NULL, 'กต 1504.1/9084', '2009-09-17', NULL, NULL, NULL, '2009-09-28') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('140-520015', 'MI Sub-regional Research Cycle', '10', 'สถาบันเพื่อพัฒนาเศรษฐกิจลุ่มน้ำโขง', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-05-25', '2010-03-24', 'สถาบันความร่วมมือเพื่อพัฒนาเศรษฐกิจลุ่มน้ำโขง จังหวัดขอนแก่น', '140', '140', NULL, 'กต 1504.1/2799', '2009-03-25', 
						  NULL, NULL, NULL, '2009-04-03') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('140-520016', 'Sustainable Rural Develoment', '10', 'สถาบันเพื่อพัฒนาเศรษฐกิจลุ่มน้ำโขง', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	'2009-05-25', '2009-06-19', 'สถาบันความร่วมมือเพื่อพัฒนาเศรษฐกิจลุ่มน้ำโขง ', '140', '140', NULL, 'กต 1504.2/3373', '2009-04-21', NULL, NULL, NULL, '2009-05-11') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('140-520017', 'Strategic Human Resource Development for Effective Regional Cooperation', '10', 'สถาบันเพื่อพัฒนาเศรษฐกิจลุ่มน้ำโขง', 1, $SESS_USERID, 
						  '$UPDATE_DATE', '2552', 1, NULL, NULL, NULL, '2009-07-20', '2009-08-07', 'สถาบันความร่วมมือเพื่อพัฒนาเศรษฐกิจลุ่มน้ำโขง จังหวัดขอนแก่น', '140', '140', NULL, 'กต 1504.1/4180', 
						  '2009-05-12', NULL, NULL, NULL, '2009-06-02') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('140-520018', 'Effective Project Management for Sustainable Development in the GMS', '10', 'สถาบันเพื่อพัฒนาเศรษฐกิจลุ่มน้ำโขง', 1, $SESS_USERID, 
						  '$UPDATE_DATE', '2552', 1, NULL, NULL, NULL, '2009-08-17', '2009-09-04', 'สถาบันความร่วมมือเพื่อพัฒนาเศรษฐกิจลุ่มน้ำโขง จังหวัดขอนแก่น', '140', '140', NULL, 'กต 1504.1/4337', 
						  '2009-05-14', NULL, NULL, NULL, '2009-06-23') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('140-520019', '-', '21', 'UNESCO-Czech', 1, $SESS_USERID, '$UPDATE_DATE', '2552', 1, NULL,
						  NULL, NULL,	NULL, NULL, NULL, '140', '140', NULL, 'ศธ. 0205/3633', '2008-10-20', NULL, NULL, NULL, '2008-12-01') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SCHOLARSHIP (SCH_CODE, SCH_NAME, ST_CODE, SCH_OWNER, SCH_ACTIVE, UPDATE_USER, UPDATE_DATE,
						  SCH_YEAR, SCH_TYPE, SCH_CLASS, EN_CODE, EM_CODE, SCH_START_DATE, SCH_END_DATE, SCH_PLACE, CT_CODE_OWN,
						  CT_CODE_GO, SCH_BUDGET, SCH_APP_DOC_NO, SCH_DOC_DATE, SCH_APP_DATE, SCH_APP_PER_ID, SCH_REMARK, SCH_DEAD_LINE)
						  VALUES ('140-520020', 'The Great Wall Co-Sponsored Fellowship Programme 2009/2010', '21', 'UNESCO-China', 1, $SESS_USERID, 
						  '$UPDATE_DATE', '2552', 1, NULL, NULL, NULL, NULL, NULL, NULL, '140', '140', NULL, 'ศธ. 0205/570', '2009-02-16', NULL, NULL, NULL, '2009-03-20') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

	}

?>