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
						VALUES(2, 2, '101', 'การตั้งเป้าหมาย', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(3, 3, '101', 'การปรับปรุงกระบวนการทำงาน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(4, 4, '101', 'Process Re-engineering่', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(5, 5, '101', 'เทคนิคการตัดสินใจด้วย Game Theory', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(6, 1, '102', 'การสร้างจิตสำนึกในงานบริการผู้รับบริการ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(7, 2, '102', 'การฟังและการพัฒนาการสื่อสาร', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(8, 3, '102', 'การเข้าใจผู้อื่น', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(9, 4, '102', 'การบริการด้วยใจ (Service Solution)', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(10, 5, '102', 'การวางแผนเชิงกลยุทธ์', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(11, 1, '103', 'เทคนิคการหาข้อมูลที่มีประสิทธิภาพ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(12, 2, '103', 'การจัดการความรู้', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(13, 3, '103', 'การคิดเชิงสังเคราะห์', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(	14, 4, '103', 'การสั่งสมความเชี่ยวชาญในงานอาชีพ', $SESS_USERID, '$UPDATE_DATE') ";
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
						VALUES(17, 2, '104', 'ธรรมะในชีวิตประจำวัน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(18, 3, '104', 'ธรรมะในชีวิตประจำวัน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(19, 4, '104', 'ธรรมะในชีวิตประจำวัน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(20, 5, '104', 'ธรรมะในชีวิตประจำวัน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(21, 1, '105', 'การเสริมสร้างศักยภาพการทำงานเป็นทีม', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(22, 2, '105', 'การสร้างทีมงาน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(23, 3, '105', 'การบริหารทีมงาน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(24, 4, '105', 'Strategic Partnership Management', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(25, 5, '105', 'การบริหารความขัดแย้ง', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(26, 1, '201', 'Time Management', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(27, 2, '201', 'การตั้งเป้าหมายด้วยวิธี Scorecard', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(28, 3, '201', 'สภาวะผู้นำ (Team Leadership)', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(29, 4, '201', 'การตัดสินใจอย่างถูกต้อง', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(30, 5, '201', 'การสร้างและสื่อสารวิสัยทัศน์', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(31, 1, '202', 'เทคนิคการนำเสนอ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(32, 2, '202', 'การนำเสนอและการพูดต่อสาธารณชนอย่างมีพลัง', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(33, 3, '202', 'การโน้มน้าวจูงใจ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(34, 4, '202', 'การบริหารนวัตกรรมสร้างสรรค์', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(35, 5, '202', 'ผู้นำที่มีวิสัยทัศน์', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(36, 1, '203', 'การรับฟังการสื่อสารกลยุทธ์องค์กร', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(37, 2, '203', 'การคิดเชิงกลยุทธ์ (Strategic Thinking)', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(38, 3, '203', 'การจัดทำกลยุทธ์', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(39, 4, '203', 'Blue Ocean Strategy', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(40, 5, '203', 'การบริหารเชิงกลยุทธ์ และเครื่องมือในการบริหารเชิงกลยุทธ์', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(41, 1, '204', 'การปรับตัว', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(42, 2, '204', 'การคิดเชิงบวก (Positive Thinking)', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(43, 3, '204', 'การโน้มน้าวจูงใจ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(44, 4, '204', 'การบริหารการเปลี่ยนแปลง', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(45, 5, '204', 'เพื่อให้ผู้เข้าอบรมมีกรอบความคิด และให้ความสำคัญของการคิดเชิงกลยุทธ์', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(46, 1, '205', 'การพัฒนาตนให้มี EQ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(47, 2, '205', 'การเข้าใจผู้อื่น', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(48, 3, '205', 'การบริการด้วยใจ (Service Solution)', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(49, 4, '205', 'การคิดและพฤติกรรม', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(50, 5, '205', 'การคิดและพฤติกรรม', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(51, 1, '206', 'การสอนงานระดับต้น', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(52, 2, '206', 'จิตวิทยาและการเป็นหัวหน้างาน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(53, 3, '206', 'การสอนงานระดับสูง', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(54, 4, '206', 'การเป็น Mentor ที่ดี', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(55, 5, '206', 'การสร้างวัฒนธรรมการสอน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(56, 1, '301', 'เทคนิคการวางแผนกำกับติดตาม', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(57, 2, '301', 'เทคนิคการติดตามและประเมินผล', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(58, 3, '301', 'การดูงานในหน่วยงาน Best Practice', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(59, 4, '301', 'เทคนิคการบริหารความขัดแย้ง', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(60, 5, '301', 'เทคนิคการบริหารความขัดแย้ง', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(61, 1, '302', 'จริยธรรมจรรยาบรรณวิชาชีพ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(62, 2, '302', '7 อุปนิสัยของผู้มีประสิทธิผลสูง', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(63, 3, '302', 'การเป็น Mentor ที่ดี', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(64, 4, '302', 'การแลกเปลี่ยนความรู้และประสบการณ์ (Knowledge & Experience Sharing)', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(65, 5, '302', 'การแลกเปลี่ยนความรู้และประสบการณ์ (Knowledge & Experience Sharing)', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(66, 1, '303', 'การคิดเชิงวิเคราะห์และเชิงสังเคราะห์', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(67, 2, '303', 'การวิเคราะห์ การแก้ไขปัญหา และการตัดสินใจ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(68, 3, '303', 'การคิดเชิงกลยุทธ์ (Strategic Thinking)', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(69, 4, '303', 'การวิเคราะห์งานและการตัดสินใจอย่างเป็นระบบ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(70, 5, '303', 'การแลกเปลี่ยนความรู้และประสบการณ์ (Knowledge & Experience Sharing)', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(71, 1, '304', 'ชื่อหลักสูตรการฝึกอบรม: ความรู้เรื่อง 5 ส. ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(72, 2, '304', 'On-the-job training', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(73, 3, '304', '7 อุปนิสัยของผู้มีประสิทธิผลสูง', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(74, 4, '304', 'การบริหารโครงการ (Project Management)', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(75, 5, '304', 'การแลกเปลี่ยนความรู้และประสบการณ์ (Knowledge & Experience Sharing)', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(76, 1, '305', 'On-the-job training', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(77, 2, '305', 'เทคนิคการนำเสนอ การให้ความรู้ และคำแนะนำ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(78, 3, '305', 'การเป็นวิทยากรที่ดี', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(79, 4, '305', 'Client Relationship Management', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(80, 5, '305', 'การคิดและพฤติกรรม', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(81, 1, '306', 'การวางแผนงานเบื้องต้น', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(82, 2, '306', 'การวางแผนงานและการตั้งเป้าหมาย', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(83, 3, '306', 'การวางแผนอย่างมีประสิทธิภาพ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(84, 4, '306', 'Scenario Planning ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(85, 5, '306', 'การวางแผนเชิงกลยุทธ์', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(86, 1, '307', 'การใช้ทรัพยากรอย่างคุ้มค่า', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(87, 2, '307', 'Process Re-engineering', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(88, 3, '307', 'การติดตามและประเมินผล', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(89, 4, '307', 'การบริหารโครงการ (Project Management)', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(90, 5, '307', 'การบริหารโครงการ (Project Management)', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(91, 1, '308', 'การคิดและพฤติกรรม', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(92, 2, '308', 'การคิดและพฤติกรรม', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(93, 3, '308', 'เชาว์อารมณ์ (Emotional Intelligence)', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(94, 4, '308', 'การบริหารการเปลี่ยนแปลง', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(95, 5, '308', 'การวางแผนเชิงกลยุทธ์', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(96, 1, '309', 'On-the-job training', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(97, 2, '309', 'การคิดเชิงสังเคราะห์ ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(98, 3, '309', 'การปรับปรุงกระบวนการทำงาน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(99, 4, '309', 'การคิดและพฤติกรรม', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(100, 5, '309', 'การคิดและพฤติกรรม', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(101, 1, '310', 'เทคนิคการนำเสนอ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(102, 2, '310', 'Presentation Skill', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(103, 3, '310', 'การนำเสนอและการพูดต่อสาธารณชนอย่างมีพลัง', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(104, 4, '310', 'การโน้มน้าวจูงใจ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(105, 5, '310', 'การสื่อสารสาธารณะแบบมีส่วนร่วมทุกภาคส่วน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(106, 1, '311', 'ความคิดสร้างสรรค์', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(107, 2, '311', 'Process Re-Engineering ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(108, 3, '311', 'การบริหารนวัตกรรมสร้างสรรค์', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(109, 4, '311', 'การวางแผนเชิงกลยุทธ์', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(110, 5, '311', 'การวางแผนเชิงกลยุทธ์', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(111, 1, '312', 'On-the-Job Training', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(112, 2, '312', 'การบริหารความขัดแย้ง', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(113, 3, '312', 'Scenario Planning ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(114, 4, '312', 'การแก้ไขปัญหาอย่างเป็นระบบ ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(115, 5, '312', 'การฝึกอบรม', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(116, 1, '313', 'การฟังและการสื่อสารอย่างมีประสิทธิภาพ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(117, 2, '313', 'Building 2-way Communication', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(118, 3, '313', 'เทคนิคการหาข้อมูลที่มีประสิทธิภาพ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(119, 4, '313', 'การจัดการความรู้ ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(120, 5, '313', 'การดูงาน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(121, 1, '314', 'การเขียนผังงาน ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(122, 2, '314', 'การวางแผนงานและการตั้งเป้าหมาย', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(123, 3, '314', 'การปรับปรุงกระบวนงานให้มีประสิทธิภาพยิ่งขึ้น (Process Improvement)', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(124, 4, '314', 'Process Re-Engineering ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(125, 5, '314', 'การฝึกอบรม', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(126, 1, '315', 'เทคนิคการหาข้อมูลที่มีประสิทธิภาพ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(127, 2, '315', 'เรื่องการแก้ไขปัญหาอย่างเป็นระบบ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(128, 3, '315', 'Scenario Planning', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(129, 4, '315', 'การสร้างความคิดสร้างสรรค์', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_DEVELOPE_GUIDE (PD_GUIDE_ID, PD_GUIDE_LEVEL, PD_GUIDE_COMPETENCE, PD_GUIDE_DESCRIPTION1, 
						UPDATE_USER, UPDATE_DATE) 
						VALUES(130, 5, '315', 'การฝึกอบรม', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
	}

?>