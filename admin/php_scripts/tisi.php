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
						  VALUES ('101', 'การมุ่งผลสัมฤทธิ์', 'Achievement Motivation- ACH', 'ความมุ่งมั่นจะปฏิบัติราชการให้ดีหรือให้เกินมาตรฐานที่มีอยู่ โดยมาตรฐานนี้อาจเป็นผลการปฏิบัติงานที่ผ่านมาของตนเอง หรือเกณฑ์วัดผลสัมฤทธิ์ที่ส่วนราชการกำหนดขึ้น อีกทั้งยังหมายรวมถึงการสร้างสรรค์พัฒนาผลงานหรือกระบวนการปฏิบัติงานตามเป้าหมายที่ยากและท้าทายชนิดที่อาจไม่เคยมีผู้ใดสามารถกระทำได้มาก่อน', 1, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
						  UPDATE_USER, UPDATE_DATE)
						  VALUES ('102', 'บริการที่ดี', 'Service Mind- SERV', 'สมรรถนะนี้เน้นความตั้งใจและความพยายามของข้าราชการในการให้บริการเพื่อสนองความต้องการของประชาชนตลอดจนของหน่วยงานภาครัฐอื่นๆ ที่เกี่ยวข้อง', 1, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
						  UPDATE_USER, UPDATE_DATE)
						  VALUES ('103', 'การสั่งสมความเชี่ยวชาญในงานอาชีพ', 'Expertise- EXP', 'ความขวนขวาย สนใจใฝ่รู้ เพื่อสั่งสมพัฒนาศักยภาพ ความรู้ความสามารถของตนในการปฏิบัติราชการ ด้วยการศึกษา ค้นคว้าหาความรู้ พัฒนาตนเองอย่างต่อเนื่อง อีกทั้งรู้จักพัฒนา ปรับปรุง ประยุกต์ใช้ความรู้เชิงวิชาการและเทคโนโลยีต่างๆ เข้ากับการปฏิบัติงานให้เกิดผลสัมฤทธิ์', 1, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
						  UPDATE_USER, UPDATE_DATE)
						  VALUES ('104', 'การยึดมั่นในความถูกต้องชอบธรรม และจริยธรรม', 'Expertise- EXP', 'การครองตนและประพฤติปฏิบัติถูกต้องเหมาะสมทั้งตามหลักกฎหมายและคุณธรรมจริยธรรม ตลอดจนหลักแนวทางในวิชาชีพของตนโดยมุ่งประโยชน์ของประเทศชาติมากกว่าประโยชน์ส่วนตน  ทั้งนี้เพื่อธำรงรักษาศักดิ์ศรีแห่งอาชีพข้าราชการ อีกทั้งเพื่อเป็นกำลังสำคัญในการสนับสนุนผลักดันให้ภารกิจหลักภาครัฐบรรลุเป้าหมายที่กำหนดไว้', 1, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
						  UPDATE_USER, UPDATE_DATE)
						  VALUES ('105', 'การทำงานเป็นทีม', 'Teamwork- TW', 'สมรรถนะนี้เน้นที่ 1) ความตั้งใจที่จะทำงานร่วมกับผู้อื่น เป็นส่วนหนึ่งในทีมงาน หน่วยงาน หรือองค์กร โดยผู้ปฏิบัติมีฐานะเป็นสมาชิกในทีม มิใช่ในฐานะหัวหน้าทีม และ 2) ความสามารถในการสร้างและดำรงรักษาสัมพันธภาพกับสมาชิกในทีม', 1, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
						  UPDATE_USER, UPDATE_DATE)
						  VALUES ('201', 'สภาวะผู้นำ', 'Leadership- LEAD', 'ความตั้งใจหรือความสามารถในการเป็นผู้นำของกลุ่มคน ปกครอง รวมถึงการกำหนดทิศทาง วิสัยทัศน์ เป้าหมาย วิธีการทำงาน ให้ผู้ใต้บังคับบัญชาหรือทีมงานปฏิบัติงานได้อย่างราบรื่น เต็มประสิทธิภาพและบรรลุวัตถุประสงค์ขององค์กร', 2, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
						  UPDATE_USER, UPDATE_DATE)
						  VALUES ('202', 'วิสัยทัศน์', 'Visioning- VIS', 'ความสามารถให้ทิศทางที่ชัดเจนและก่อความร่วมแรงร่วมใจในหมู่ผู้ใต้บังคับบัญชาเพื่อนำพางานภาครัฐไปสู่จุดหมายร่วมกัน', 2, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
						  UPDATE_USER, UPDATE_DATE)
						  VALUES ('203', 'การวางกลยุทธ์ภาครัฐ', 'Strategic Orientation- SO', 'ความเข้าใจกลยุทธ์ภาครัฐและสามารถประยุกต์ใช้ในการกำหนดกลยุทธ์ของหน่วยงานตนได้ โดยความสามารถในการประยุกต์นี้รวมถึงความสามารถในการคาดการณ์ถึงทิศทางระบบราชการในอนาคต ตลอดจนผลกระทบของสถานการณ์ทั้งในและต่างประเทศที่เกิดขึ้น', 2, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
						  UPDATE_USER, UPDATE_DATE)
						  VALUES ('204', 'ศักยภาพเพื่อนำการปรับเปลี่ยน', 'Change Leadership- CL', 'ความตั้งใจและความสามารถในการกระตุ้นผลักดันกลุ่มคนให้เกิดความต้องการจะปรับเปลี่ยนไปในแนวทางที่เป็นประโยชน์แก่ภาครัฐ รวมถึงการสื่อสารให้ผู้อื่นรับรู้ เข้าใจ และดำเนินการให้การปรับเปลี่ยนนั้นเกิดขึ้นจริง', 2, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
						  UPDATE_USER, UPDATE_DATE)
						  VALUES ('205', 'การควบคุมตนเอง', 'Self Control- SCT', 'การระงับอารมณ์และพฤติกรรมอันไม่เหมาะสมเมื่อถูกยั่วยุ หรือเผชิญหน้ากับฝ่ายตรงข้าม เผชิญความไม่เป็นมิตร หรือทำงานภายใต้สภาวะความกดดัน รวมถึงความอดทนอดกลั้นเมื่อต้องอยู่ภายใต้สถานการณ์ที่ก่อความเครียดอย่างต่อเนื่อง', 2, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
						  UPDATE_USER, UPDATE_DATE)
						  VALUES ('206', 'การสอนงานและให้อำนาจแก่ผู้อื่น', 'Coaching and Empowering Others - CEMP', 'ความตั้งใจจะส่งเสริมการเรียนรู้หรือการพัฒนาผู้อื่นในระยะยาว รวมถึงความเชื่อมั่นในความสามารถของผู้อื่น ดังนั้นจึงมอบหมายอำนาจและหน้าที่รับผิดชอบให้เพื่อให้ผู้อื่นมีอิสระในการสร้างสรรค์วิธีการของตนเพื่อบรรลุเป้าหมายในงาน', 2, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
						  UPDATE_USER, UPDATE_DATE)
						  VALUES ('301', 'การกำกับติดตามอย่างสม่ำเสมอ', 'Monitoring and Overseeing- MO', 'เจตนาที่จะกำกับดูแล และติดตามการดำเนินงานต่างๆ ของผู้อื่นที่เกี่ยวข้องให้ปฏิบัติตามมาตรฐาน กฎระเบียบ หรือข้อบังคับที่กำหนดไว้ โดยอาศัยอำนาจตามระเบียบ กฎหมาย หรือตามตำแหน่งหน้าที่ที่มีอยู่อย่างเหมาะสมและมีประสิทธิภาพโดยมุ่งประโยชน์ของหน่วยงาน องค์กร หรือประเทศชาติเป็นสำคัญ', 3, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
						  UPDATE_USER, UPDATE_DATE)
						  VALUES ('302', 'การยึดมั่นในหลักเกณฑ์', 'Acts with Integrity- AI', 'เจตนาที่จะกำกับดูแลให้ผู้อื่นหรือหน่วยงานอื่นปฏิบัติให้ได้ตามมาตรฐาน กฎระเบียบข้อบังคับที่กำหนดไว้ โดยอาศัยอำนาจตามระเบียบ กฎหมาย หรือตามหลักแนวทางในวิชาชีพของตนที่มีอยู่อย่างเหมาะสมและมีประสิทธิภาพโดยมุ่งประโยชน์ขององค์กร สังคม และประเทศโดยรวมเป็นสำคัญ ความสามารถนี้อาจรวมถึงการยืนหยัดในสิ่งที่ถูกต้องและความเด็ดขาดในการจัดการกับบุคคลหรือหน่วยงานที่ฝ่าฝืนกฎเกณฑ์ ระเบียบหรือมาตรฐานที่ตั้งไว้', 3, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
						  UPDATE_USER, UPDATE_DATE)
						  VALUES ('303', 'การวิเคราะห์และการสังเคราะห์', 'Analytical and Synthesis Thinking - AST', 'ความสามารถในการคิดวิเคราะห์และทำความเข้าใจในเชิงสังเคราะห์ รวมถึงการมองภาพรวมขององค์กร จนได้เป็นกรอบความคิดหรือแนวคิดใหม่ อันเป็นผลมาจากการสรุปรูปแบบ ประยุกต์แนวทางต่างๆ จากสถานการณ์หรือข้อมูลหลากหลาย และนานาทัศนะ', 3, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
						  UPDATE_USER, UPDATE_DATE)
						  VALUES ('304', 'ความถูกต้องของงาน', 'Accuracy and Order- AO', 'ความพยายามที่จะปฏิบัติงานให้ถูกต้องครบถ้วนตลอดจนลดข้อบกพร่องที่อาจจะเกิดขึ้น รวมถึงการควบคุมตรวจตราให้งานเป็นไปตามแผนที่วางไว้อย่างถูกต้องชัดเจน', 3, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
						  UPDATE_USER, UPDATE_DATE)
						  VALUES ('305', 'การให้ความรู้และการสร้างสัมพันธ์', 'Providing Knowledge and Building Relationships- PKBR', 'มีพฤติกรรมที่มุ่งมั่น และตั้งใจที่จะนำภูมิปัญญา นวัตกรรม เทคโนโลยี ความเชี่ยวชาญ และองค์ความรู้ต่างๆ ไปส่งเสริม สนับสนุน และพัฒนาผู้ประกอบการ หรือเครือข่าย ควบคู่ไปกับการสร้าง พัฒนา และรักษาความสัมพันธ์อันดีกับผู้ประกอบการ หรือเครือข่าย เพื่อให้ผู้ประกอบการ หรือเครือข่าย มีความรู้ ความเข้าใจ และสามารถ นำไปใช้พัฒนาหน่วยงานให้มีประโยชน์ อีกทั้งเป็นการเสริมสร้างขีดความสามารถในการแข่งขันอย่างยั่งยืน', 3, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
						  UPDATE_USER, UPDATE_DATE)
						  VALUES ('306', 'การวางแผนและการจัดการ', 'Planning and Organizing- PO', 'ความสามารถในการวางแผนอย่างเป็นหลักการ โดยเน้นให้สามารถนำไปปฏิบัติได้จริงและถูกต้อง รวมถึงความสามารถในการบริหารจัดการโครงการต่างๆ ในความรับผิดชอบให้สามารถบรรลุเป้าหมายที่กำหนดไว้อย่างมีประสิทธิภาพสูงสุด', 3, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
						  UPDATE_USER, UPDATE_DATE)
						  VALUES ('307', 'การบริหารทรัพยากร', 'Resource Management- RM', 'การตระหนักเสมอถึงความคุ้มค่าระหว่างทรัพยากร (งบประมาณ เวลา กำลังคนเครื่องมือ อุปกรณ์ ฯลฯ) ที่ลงทุนไปหรือที่ใช้การปฏิบัติภารกิจ (Input) กับผลลัพธ์ที่ได้ (Output) และพยายามปรับปรุงหรือลดขั้นตอนการปฏิบัติงาน เพื่อพัฒนาให้การปฏิบัติงานเกิดความคุ้มค่าและมีประสิทธิภาพสูงสุด อาจหมายรวมถึงความสามารถในการจัดความสำคัญในการใช้เวลา ทรัพยากร และข้อมูลอย่างเหมาะสม และประหยัดค่าใช้จ่ายสูงสุด', 3, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
						  UPDATE_USER, UPDATE_DATE)
						  VALUES ('308', 'ความเข้าใจคนและยืดหยุ่นตามสถานการณ์', 'Understanding People and Adaptability- UPA', 'ความสามารถในการรับฟังและเข้าใจบุคคลหรือสถานการณ์ และพร้อมที่จะปรับเปลี่ยนให้สอดคล้องกับสถานการณ์หรือกลุ่มคนที่หลากหลาย ในขณะที่ยังคงปฏิบัติงานได้อย่างมีประสิทธิภาพและบรรลุผลตามเป้าหมายที่ตั้งไว้', 3, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
						  UPDATE_USER, UPDATE_DATE)
						  VALUES ('309', 'ความเข้าใจในองค์กรและระบบงาน', 'Organization and Process Understanding - OPU', 'ความเข้าใจและสามารถประยุกต์ใช้ความสัมพันธ์เชื่อมโยงของเทคโนโลยี ระบบ กระบวนการทำงาน และมาตรฐานการทำงานของตนและของหน่วยงานอื่นๆ ที่เกี่ยวข้อง เพื่อประโยชน์ในการปฏิบัติหน้าที่ให้บรรลุผล ความเข้าใจนี้รวมถึงความสามารถในการมองภาพใหญ่ (Big Picture) และการคาดการณ์เพื่อเตรียมการรองรับการเปลี่ยนแปลงของสิ่งต่างๆ ต่อระบบและกระบวนการทำงาน', 3, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
						  UPDATE_USER, UPDATE_DATE)
						  VALUES ('310', 'การสื่อสารโน้มน้าวจูงใจ', 'Communication & Influencing- CI', 'การใช้วาทศิลป์และกลยุทธ์ต่างๆ ในการสื่อสาร เจรจา โน้มน้าวเพื่อให้ผู้อื่นดำเนินการใดๆ ตามที่ตนหรือหน่วยงานประสงค์', 3, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
						  UPDATE_USER, UPDATE_DATE)
						  VALUES ('311', 'ความคิดสร้างสรรค์', 'Innovation- INV', 'ความสามารถในการที่จะนำเสนอทางเลือก (Option) หรือแนวทางแก้ปัญหา (Solution) หรือสร้างนวัตกรรม หรือ ริเริ่มสร้างสรรค์กิจกรรมหรือสิ่งใหม่ๆ ที่จะเป็นประโยชน์ต่อองค์กร', 3, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
						  UPDATE_USER, UPDATE_DATE)
						  VALUES ('312', 'การดำเนินการเชิงรุก', 'Proactiveness- PRO', 'การตระหนักหรือเล็งเห็นโอกาสหรือปัญหาอุปสรรคที่อาจเกิดขึ้นในอนาคต และวางแผน ลงมือกระทำการเพื่อเตรียมใช้ประโยชน์จากโอกาส หรือป้องกันปัญหา ตลอดจนพลิกวิกฤติต่างๆ ให้เป็นโอกาส', 3, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
						  UPDATE_USER, UPDATE_DATE)
						  VALUES ('313', 'การค้นหาและการบริหารจัดการข้อมูลความรู้', 'Information Seeking and Management– ISM', 'ความสามารถในการสืบเสาะ เพื่อให้ได้ข้อมูลเฉพาะเจาะจง การไขปมปริศนาโดยซักถามโดยละเอียด หรือแม้แต่การหาข่าวทั่วไปจากสภาพแวดล้อมรอบตัวโดยคาดว่าอาจมีข้อมูลที่จะเป็นประโยชน์ต่อไปในอนาคต และนำข้อมูลที่ได้มานั้นมาประมวลและจัดการอย่างมีระบบ คุณลักษณะนี้อาจรวมถึงความสนใจใคร่รู้เกี่ยวกับสถานการณ์ ภูมิหลัง ประวัติความเป็นมา ประเด็น ปัญหา หรือเรื่องราวต่างๆ ที่เกี่ยวข้องหรือจำเป็นต่องานในหน้าที่', 3, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
						  UPDATE_USER, UPDATE_DATE)
						  VALUES ('314', 'การวางแผนที่สอดคล้องกับองค์กร', 'Planning with Organization Understanding - POU', 'ความสามารถในการวางแผนอย่างเป็นหลักการให้สามารถนำไปปฏิบัติได้จริงและถูกต้อง โดยอาศัยความเข้าใจในเรื่องเทคโนโลยี ระบบ กระบวนการทำงาน และมาตรฐานการทำงานของตนและของหน่วยงานอื่นๆ ที่เกี่ยวข้อง', 3, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
						  UPDATE_USER, UPDATE_DATE)
						  VALUES ('315', 'การแก้ไขปัญหาแบบมืออาชีพ', 'Professional Problem Solving – PPS', 'ความสามารถในวิเคราะห์ปัญหาหรือเล็งเห็นปัญหา พร้อมทั้งลงมือจัดการกับปัญหานั้นๆ อย่างมีข้อมูล มีหลักการ และสามารถนำความเชี่ยวชาญ หรือแนวคิดในสายวิชาชีพมาประยุกต์ใช้ในการแก้ไขปัญหาได้อย่างมีประสิทธิภาพ', 3, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$code = array(	"101", "102", "103", "104", "105", "201", "202", "203", "204", "205", "206", "301", "302", "303", "304", "305", "306", 
										"307", "308", "309", "310", "311", "312", "313", "314", "315" );
		for ( $i=0; $i<count($code); $i++ ) { 
			$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
							  UPDATE_DATE)
							  VALUES ('$code[$i]', 0, 'ไม่แสดงสมรรถนะด้านนี้อย่างชัดเจน', NULL, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end for

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('101', 1, 'แสดงความพยายามในการทำงานให้ดี', 'พยายามทำงานในหน้าที่ให้ดีและถูกต้อง 
มีความมานะอดทน ขยันหมั่นเพียรในการทำงาน และตรงต่อเวลา
มีความรับผิดชอบในงาน สามารถส่งงานได้ตามกำหนดเวลา
แสดงออกว่าต้องการทำงานให้ได้ดีขึ้น เช่น ถามถึงวิธีการ หรือขอแนะนำอย่างกระตือรือร้น สนใจใคร่รู้
แสดงความเห็นในเชิงปรับปรุงพัฒนาเมื่อเห็นสิ่งที่ก่อให้เกิดการสูญเปล่า หรือหย่อนประสิทธิภาพในงาน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('101', 2, 'แสดงสมรรถนะระดับที่ 1 และสามารถทำงานได้ผลงานตามเป้าหมายที่วางไว้', 'กำหนดมาตรฐาน หรือเป้าหมายในการทำงานเพื่อให้ได้ผลงานที่ดี
หมั่นติดตามผลงาน และประเมินผลงานของตน โดยใช้เกณฑ์ที่กำหนดขึ้น โดยไม่ได้ถูกบังคับ เช่น ถามว่าผลงานดีหรือยัง หรือต้องปรับปรุงอะไรจึงจะดีขึ้น
ทำงานได้ตามผลงานตามเป้าหมายที่ผู้บังคับบัญชากำหนด หรือเป้าหมายของหน่วยงานที่รับผิดชอบ
มีความละเอียดรอบคอบเอาใจใส่ ตรวจตราความถูกต้องของงาน เพื่อให้ได้งานที่มีคุณภาพ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('101', 3, 'แสดงสมรรถนะระดับที่ 2 และสามารถทำงานได้ผลงานที่มีประสิทธิภาพมากยิ่งขึ้น', 'ปรับปรุงวิธีการที่ทำให้ทำงานได้ดีขึ้น เร็วขึ้น มีคุณภาพดีขึ้น หรือมีประสิทธิภาพมากขึ้น
เสนอหรือทดลองวิธีการทำงานแบบใหม่ที่มีประสิทธิภาพมากกว่าเดิม เพื่อให้ได้ผลงานตามที่กำหนดไว้', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('101', 4, 'แสดงสมรรถนะระดับที่ 3 และสามารถพัฒนาวิธีการทำงาน เพื่อให้ได้ผลงานที่โดดเด่น และแตกต่างอย่างไม่เคยมีใครทำได้มาก่อน', 'กำหนดเป้าหมายที่ท้าทาย และเป็นไปได้ยาก เพื่อทำให้ได้ผลงานที่ดีกว่าเดิมอย่างเห็นได้ชัด
ทำการพัฒนาระบบ ขั้นตอน วิธีการทำงาน เพื่อให้ได้ผลงานที่โดดเด่น และแตกต่างไม่เคยมีใครทำได้มาก่อน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('101', 5, 'แสดงสมรรถนะระดับที่ 4 และสามารถตัดสินใจได้ แม้จะมีความเสี่ยง เพื่อให้องค์กรบรรลุเป้าหมาย', 'ตัดสินใจได้ โดยมีการคำนวณผลได้ผลเสียอย่างชัดเจน และดำเนินการ เพื่อให้ภาครัฐและประชาชนได้ประโยชน์สูงสุด
บริหารจัดการและทุ่มเทเวลา ตลอดจนทรัพยากร เพื่อให้ได้ประโยชน์สูงสุดต่อภารกิจของหน่วยงานตามที่วางแผนไว้', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('102', 1, 'แสดงความเต็มใจในการให้บริการ', 'ให้การบริการที่เป็นมิตร สุภาพ เต็มใจต้อนรับ
ให้บริการด้วยอัธยาศัยไมตรีอันดี และสร้างความประทับใจแก่ผู้รับบริการ 
ให้คำแนะนำ และคอยติดตามเรื่อง เมื่อผู้รับบริการมีคำถาม ข้อเรียกร้องที่เกี่ยวกับภารกิจของหน่วยงาน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('102', 2, 'แสดงสมรรถนะระดับที่ 1 และสามารถให้บริการที่ผู้รับบริการต้องการได้', 'ให้ข้อมูล ข่าวสาร ของการบริการที่ถูกต้อง ชัดเจนแก่ผู้รับบริการได้ตลอดการให้บริการ
แจ้งให้ผู้รับบริการทราบความคืบหน้าในการดำเนินเรื่อง หรือขั้นตอนงานต่างๆ ที่ให้บริการอยู่
ประสานงานภายในหน่วยงาน และกับหน่วยงานที่เกี่ยวข้อง เพื่อให้ผู้รับบริการได้รับบริการที่ต่อเนื่องและรวดเร็ว', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('102', 3, 'แสดงสมรรถนะระดับที่ 2 และเต็มใจช่วยแก้ปัญหาให้กับผู้บริการได้', 'รับเป็นธุระ ช่วยแก้ปัญหาหรือหาแนวทางแก้ไขปัญหาที่เกิดขึ้นแก่ผู้รับบริการอย่างรวดเร็ว  เต็มใจ ไม่บ่ายเบี่ยง ไม่แก้ตัว หรือปัดภาระ
คอยดูแลให้ผู้รับบริการได้รับความพึงพอใจ และนำข้อขัดข้องใดๆ ในการให้บริการ (ถ้ามี) ไปพัฒนาการให้บริการให้ดียิ่งขึ้น', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('102', 4, 'แสดงสมรรถนะระดับที่ 3 และให้บริการที่เกินความคาดหวังในระดับทั่วไป แม้ต้องใช้เวลาหรือความพยายามอย่างมาก', 'ให้เวลาแก่ผู้รับบริการ โดยเฉพาะเมื่อผู้รับบริการประสบความยากลำบาก เช่น ให้เวลาและความพยายามพิเศษในการให้บริการ เพื่อช่วยผู้รับบริการแก้ปัญหา
คอยให้ข้อมูล ข่าวสาร ความรู้ที่เกี่ยวข้องกับงานที่กำลังให้บริการอยู่ ซึ่งเป็นประโยชน์แก่ผู้รับบริการ แม้ว่าผู้รับบริการจะไม่ได้ถามถึง หรือไม่ทราบมาก่อน
ให้บริการที่เกินความคาดหวังในระดับทั่วไป', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('102', 5, 'แสดงสมรรถนะระดับที่ 4 และสามารถเข้าใจและให้บริการที่ตรงตามความต้องการที่แท้จริงของผู้รับบริการได้', 'เข้าใจความจำเป็นหรือความต้องการที่แท้จริงของผู้รับบริการ และ/หรือ ใช้เวลาแสวงหาข้อมูลและทำความเข้าใจเกี่ยวกับความจำเป็นหรือความต้องการที่แท้จริงของผู้รับบริการ
ให้คำแนะนำที่เป็นประโยชน์แก่ผู้รับบริการ เพื่อตอบสนองความจำเป็นหรือความต้องการที่แท้จริงของผู้รับบริการ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('102', 6, 'แสดงสมรรถนะระดับที่ 5 และสามารถให้บริการที่เป็นประโยชน์อย่างแท้จริงและยั่งยืนให้กับผู้รับบริการ', 'เล็งเห็นผลประโยชน์ที่จะเกิดขึ้นกับผู้รับบริการในระยะยาว และสามารถเปลี่ยนแปลงวิธีหรือขั้นตอนการให้บริการ เพื่อให้ผู้รับบริการได้ประโยชน์สูงสุด
ปฏิบัติตนเป็นที่ปรึกษาที่ผู้รับบริการไว้วางใจ  ตลอดจนมีส่วนช่วยในการตัดสินใจของผู้รับบริการ
สามารถให้ความเห็นส่วนตัวที่อาจแตกต่างไปจากวิธีการ หรือขั้นตอนที่ผู้รับบริการต้องการ เพื่อให้สอดคล้องกับความจำเป็น ปัญหา โอกาส ฯลฯ เพื่อเป็นประโยชน์อย่างแท้จริงหรือในระยะยาวแก่ผู้รับบริการ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('103', 1, 'แสดงความสนใจและติดตามความรู้ใหม่ๆ ในสาขาอาชีพของตน/ที่เกี่ยวข้อง', 'กระตือรือร้นในการศึกษาหาความรู้ สนใจเทคโนโลยีและองค์ความรู้ใหม่ๆ ในสาขาอาชีพของตน
หมั่นทดลองวิธีการทำงานแบบใหม่ เพื่อพัฒนาประสิทธิภาพและความรู้ความสามารถของตนให้ดียิ่งขึ้น
ติดตามเทคโนโลยีองค์ความรู้ใหม่ๆ อยู่เสมอด้วยการสืบค้นข้อมูลจากแหล่งต่างๆ ที่จะเป็นประโยชน์ต่อการปฏิบัติราชการ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('103', 2, 'แสดงสมรรถนะระดับที่ 1 และมีความรู้ในวิชาการ และเทคโนโลยีใหม่ๆ ในสาขาอาชีพของตน', 'รอบรู้เท่าทันเทคโนโลยีหรือองค์ความรู้ใหม่ๆ ในสาขาอาชีพของตนและที่เกี่ยวข้อง หรืออาจมีผลกระทบต่อการปฏิบัติหน้าที่ของตน 
ติดตามแนวโน้มวิทยาการที่ทันสมัย และเทคโนโลยีที่เกี่ยวข้องกับงานอย่างต่อเนื่อง', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('103', 3, 'แสดงสมรรถนะระดับที่ 2 และสามารถนำความรู้ วิทยาการ หรือเทคโนโลยีใหม่ๆ ที่ได้ศึกษามาปรับใช้กับการทำงาน', 'เข้าใจประเด็นหลักๆ นัยสำคัญ และผลกระทบของวิทยาการต่างๆ อย่างลึกซึ้ง
สามารถนำวิชาการ ความรู้ หรือเทคโนโลยีใหม่ๆ มาประยุกต์ใช้ในการปฏิบัติงานได้
สั่งสมความรู้ใหม่ๆ อยู่เสมอ และเล็งเห็นประโยชน์ ความสำคัญขององค์ความรู้ เทคโนโลยีใหม่ๆ ที่จะส่งผลกระทบต่องานของตนในอนาคต', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('103', 4, 'แสดงสมรรถนะระดับที่ 3 และศึกษา พัฒนาตนเองให้มีความรู้ และความเชี่ยวชาญในงานมากขึ้นทั้งในเชิงลึก และเชิงกว้างอย่างต่อเนื่อง', 'มีความรู้ความเชี่ยวชาญในเรื่องที่เกี่ยวกับงานหลายด้าน (สหวิทยาการ) และสามารถนำความรู้ไปปรับใช้ให้ปฏิบัติได้อย่างกว้างขวางครอบคลุม
สามารถนำความรู้เชิงบูรณาการของตนไปใช้ในการสร้างวิสัยทัศน์ เพื่อการปฏิบัติงานในอนาคต
ขวนขวายหาความรู้ที่เกี่ยวข้องกับงานทั้งเชิงลึกและเชิงกว้างอย่างต่อเนื่อง', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('103', 5, 'แสดงสมรรถนะระดับที่ 4 และสนับสนุนการทำงานของคนในองค์กรที่เน้นความเชี่ยวชาญในวิทยาการด้านต่างๆ', 'สนับสนุนให้เกิดบรรยากาศแห่งการพัฒนาความเชี่ยวชาญในองค์กร ด้วยการจัดสรรทรัพยากร เครื่องมือ อุปกรณ์ที่เอื้อต่อการพัฒนา
ให้การสนับสนุน ชมเชย เมื่อมีผู้แสดงออกถึงความตั้งใจที่จะพัฒนาความเชี่ยวชาญในงาน
มีวิสัยทัศน์ในการเล็งเห็นประโยชน์ของเทคโนโลยี องค์ความรู้ หรือวิทยาการใหม่ๆ ต่อการปฏิบัติงานในอนาคต และสนับสนุนส่งเสริมให้มีการนำมาประยุกต์ใช้ในหน่วยงานอย่างต่อเนื่อง', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('104', 1, 'มีความซื่อสัตย์สุจริต', 'ปฏิบัติหน้าที่ด้วยความโปร่งใส  ซื่อสัตย์สุจริต ถูกต้องทั้งตามหลักกฎหมาย จริยธรรมและระเบียบวินัย
แสดงความคิดเห็นของตนตามหลักวิชาชีพอย่างเปิดเผยตรงไปตรงมา', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('104', 2, 'แสดงสมรรถนะระดับที่ 1 และมีสัจจะเชื่อถือได้', 'รักษาวาจา มีสัจจะเชื่อถือได้ พูดอย่างไรทำอย่างนั้น ไม่บิดเบือนอ้างข้อยกเว้นให้ตนเอง
มีจิตสำนึกและความภาคภูมิใจในความเป็นข้าราชการ อุทิศแรงกายแรงใจผลักดันให้ภารกิจหลักของตนและหน่วยงานบรรลุผล เพื่อสนับสนุนส่งเสริมการพัฒนาประเทศชาติและสังคมไทย', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('104', 3, 'แสดงสมรรถนะระดับที่ 2 และยึดมั่นในหลักการ', 'ยึดมั่นในหลักการและจรรยาบรรณของวิชาชีพ ไม่เบี่ยงเบนด้วยอคติหรือผลประโยชน์ส่วนตน
เสียสละความสุขสบายตลอดจนความพึงพอใจส่วนตนหรือของครอบครัว โดยมุ่งให้ภารกิจในหน้าที่สัมฤทธิ์ผลเป็นสำคัญ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('104', 4, 'แสดงสมรรถนะระดับที่ 3 และธำรงความถูกต้อง', 'ธำรงความถูกต้อง ยืนหยัดพิทักษ์ผลประโยชน์และชื่อเสียงของประเทศชาติแม้ในสถานการณ์ที่อาจสร้างความลำบากใจให้
ตัดสินใจในหน้าที่ ปฏิบัติราชการด้วยความถูกต้อง โปร่งใส เป็นธรรม แม้ผลของการปฏิบัติอาจสร้างศัตรูหรือก่อความไม่พึงพอใจให้แก่ผู้ที่เกี่ยวข้องหรือเสียประโยชน์', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('104', 5, 'แสดงสมรรถนะระดับที่ 4 และอุทิศตนเพื่อผดุงความยุติธรรม', 'ธำรงความถูกต้อง ยืนหยัดพิทักษ์ผลประโยชน์และชื่อเสียงของประเทศชาติแม้ในสถานการณ์ที่อาจเสี่ยงต่อความมั่นคงในตำแหน่งหน้าที่การงาน หรืออาจเสี่ยงภัยต่อชีวิต', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('105', 1, 'ทำหน้าที่ของตนในทีมให้สำเร็จ', 'ทำงานในส่วนที่ตนได้รับมอบหมายได้สำเร็จ สนับสนุนการตัดสินใจในกลุ่ม 
รายงานให้สมาชิกทราบความคืบหน้าของการดำเนินงานในกลุ่ม หรือข้อมูลอื่นๆ ที่เป็นประโยชน์ต่อการทำงานอย่างต่อเนื่อง', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('105', 2, 'แสดงสมรรถนะระดับที่ 1 และให้ความร่วมมือในการทำงานกับเพื่อนร่วมงาน', 'สร้างสัมพันธ์  เข้ากับผู้อื่นในกลุ่มได้ดี
เอื้อเฟื้อเผื่อแผ่ ให้ความร่วมมือกับผู้อื่นในทีมด้วยดี
กล่าวถึงเพื่อนร่วมงานในเชิงสร้างสรรค์', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('105', 3, 'แสดงสมรรถนะระดับที่ 2 และประสานความร่วมมือของสมาชิกในทีม', 'รับฟังความเห็นของสมาชิกในทีม เต็มใจเรียนรู้จากผู้อื่น รวมถึงผู้ใต้บังคับบัญชา และผู้ร่วมงาน
ประมวลความคิดเห็นต่างๆ มาใช้ประกอบการตัดสินใจหรือวางแผนงานร่วมกันในทีม
ประสานและส่งเสริมสัมพันธภาพอันดีในทีม เพื่อสนับสนุนการทำงานร่วมกันให้มีประสิทธิภาพยิ่งขึ้น', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('105', 4, 'แสดงสมรรถนะระดับที่ 3 และสนับสนุนและช่วยเหลืองานเพื่อนร่วมทีมคนอื่นๆ เพื่อให้งานประสบความสำเร็จ', 'กล่าวชื่นชมให้กำลังใจเพื่อนร่วมงานได้อย่างจริงใจ 
แสดงน้ำใจในเหตุวิกฤติ ให้ความช่วยเหลือแก่เพื่อนร่วมงานที่มีเหตุจำเป็นโดยไม่ต้องให้ร้องขอ
รักษามิตรภาพอันดีกับเพื่อนร่วมงานเพื่อช่วยเหลือกันในวาระต่างๆ ให้งานสำเร็จลุล่วงเป็นประโยชน์ต่อส่วนรวม', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('105', 5, 'แสดงสมรรถนะระดับที่ 4 และสามารถนำทีมให้ปฏิบัติภารกิจให้ได้ผลสำเร็จ', 'ส่งเสริมความสามัคคีเป็นน้ำหนึ่งใจเดียวกันในทีม โดยไม่คำนึงความชอบหรือไม่ชอบส่วนตน 
ช่วยประสานรอยร้าว หรือคลี่คลายแก้ไขข้อขัดแย้งที่เกิดขึ้นในทีม
ประสานสัมพันธ์ ส่งเสริมขวัญกำลังใจของทีมเพื่อรวมพลังกันในการปฏิบัติภารกิจใหญ่น้อยต่างๆ ให้บรรลุผล', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('201', 1, 'บริหารการประชุมได้ดีและคอยแจ้งข่าวสารความเป็นไปอยู่เสมอ', 'สามารถดำเนินการประชุมได้ดี โดยกำหนดประเด็นหัวข้อในการประชุม วัตถุประสงค์ ควบคุมเวลา และแจกแจงหน้าที่รับผิดชอบให้แก่บุคคลในกลุ่มได้
หมั่นแจ้งข่าวสารความเป็นไปรวมทั้งเหตุผลให้ผู้บังคับบัญชารับทราบอยู่เสมอแม้ไม่จำเป็นต้องกระทำ เพื่อให้มีความเข้าใจตรงกันนำไปสู่การปฏิบัติงานในทิศทางเดียวกัน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('201', 2, 'แสดงสมรรถนะระดับที่ 1 และเป็นผู้นำในการทำงานของกลุ่ม', 'กำหนดเป้าหมาย ทิศทางที่ชัดเจน ใช้โครงสร้างที่เหมาะสม เลือกคนให้เหมาะกับงาน หรือใช้วิธีการอื่นๆ เพื่อช่วยสร้างสภาวะที่จะทำให้กลุ่มทำงานได้ดีขึ้น
กล่าวคำชมเชย หรือให้ข้อคิดเห็นติชมที่สร้างสรรค์ เพื่อส่งเสริมให้กลุ่มทำงานอย่างมีประสิทธิภาพ
ลงมือกระทำการเป็นตัวอย่างเพื่อช่วยให้กลุ่มปฏิบัติหน้าที่ได้อย่างเต็มประสิทธิภาพ
เลือกคนให้เหมาะกับงาน และกำหนดผลลัพธ์ที่ชัดเจนในแต่ละงานที่มอบหมาย เพื่อช่วยสร้างเสริมให้กลุ่มทำงานได้ดีขึ้นหรือมีประสิทธิภาพขึ้น
สร้างขวัญกำลังใจในการปฏิบัติงาน หรือให้โอกาสผู้ใต้บังคับบัญชาในการแสดงศักยภาพการทำงานอย่างเต็มที่ เพื่อเสริมประสิทธิภาพ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('201', 3, 'แสดงสมรรถนะระดับที่ 2 และให้การดูแลและช่วยเหลือผู้ใต้บังคับบัญชา', 'รับฟังประเด็นปัญหา และรับเป็นที่ปรึกษาในการดูแลผู้ใต้บังคับบัญชาให้สามารถปฏิบัติงานด้วยความสุขและมีประสิทธิภาพสูงสุด
จัดหาบุคลากร ทรัพยากร หรือข้อมูลที่สำคัญมาให้ในการปฏิบัติงานให้บรรลุตามเป้าหมายเพื่อให้การสนับสนุนที่จำเป็นแก่ผู้ใต้บังคับบัญชา
ดูแล ปกป้องและช่วยเหลือให้ผู้ใต้บังคับบัญชาเข้าใจถึงการปรับเปลี่ยนที่เกิดขึ้นภายในองค์กรและความจำเป็นของการปรับเปลี่ยนนั้นๆ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('201', 4, 'แสดงสมรรถนะระดับที่ 3 และประพฤติตนสมกับเป็นผู้นำ', 'กำหนดธรรมเนียมปฏิบัติประจำกลุ่มและประพฤติตนเป็นแบบอย่างที่ดีแก่ผู้ใต้บังคับบัญชา
ยึดหลักธรรมาภิบาล  (Good Governance) (นิติธรรม คุณธรรม โปร่งใส ความมีส่วนร่วม ความรับผิดชอบ ความคุ้มค่า) ในการปกครองผู้ใต้บังคับบัญชา
สนับสนุนการมีส่วนร่วมของผู้ใต้บังคับบัญชาในการอุทิศตนให้กับการปฏิบัติงานเพื่อสนองนโยบายประเทศและบรรลุภารกิจภาครัฐ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('201', 5, 'แสดงสมรรถนะระดับที่ 4 และนำผู้ใต้บังคับบัญชาให้ก้าวไปสู่พันธกิจระยะยาวขององค์กร', 'สามารถรวมใจคนและสร้างแรงบันดาลใจให้ผู้ใต้บังคับบัญชาให้เกิดความมุ่งมั่นและเต็มใจในการปฏิบัติงานให้ภารกิจสำเร็จลุล่วงได้อย่างดีเลิศ
เป็นผู้นำในการสร้างสรรค์สิ่งใหม่ๆ ให้องค์กร และผลักดันให้องค์กรก้าวไปสู่การเปลี่ยนแปลงที่ราบรื่นและประสบความสำเร็จด้วยกลยุทธ์และวิธีดำเนินการที่เหมาะสม
สร้างและใช้วิสัยทัศน์ในการกำหนดจุดร่วมและทิศทางสำหรับผู้คนทั้งหลาย โดยเฉพาะอย่างยิ่งในสภาวการณ์ที่กำลังเผชิญการเปลี่ยนแปลง
เล็งเห็นการเปลี่ยนแปลงในอนาคต และเตรียมการสร้างกลยุทธ์ให้กับองค์กรในการรับมือกับการเปลี่ยนแปลงนั้นๆ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('202', 1, 'เชื่อมโยงงานเข้ากับบริบทของภาครัฐโดยรวม', 'สื่อสารให้ผู้อื่นเข้าใจว่าสิ่งที่ทำอยู่นั้นมีผลอย่างไรต่อประชาชน สาธารณชน หรือหน่วยงานอย่างไร 
สามารถสื่อสารภาพรวมและเป้าหมายที่ชัดเจนของหน่วยงานและองค์กร จนช่วยให้ผู้อื่นเข้าใจว่าบทบาทของตนเกี่ยวข้องกับบริบทโดยรวมอย่างไร
เชื่อมโยงวิสัยทัศน์ของหน่วยงานกับเป้าหมาย วัตถุประสงค์และกลยุทธ์ของภาครัฐโดยรวมได้', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('202', 2, 'แสดงสมรรถนะระดับที่ 1 และทำให้วิสัยทัศน์ได้รับการยอมรับ', 'แบ่งปันความรับผิดชอบในการกำหนดแผนการดำเนินระยะยาวที่สอดคล้องกับวิสัยทัศน์ให้ผู้อื่นได้มีส่วนร่วมหรือแสดงความคิดเห็นเพื่อสร้างให้เกิดการยอมรับและนำไปใช้จริง
สร้างความน่าเชื่อถือให้แก่วิสัยทัศน์โดยการสื่อสารในวงกว้างในหน่วยงานที่ปฏิบัติหน้าที่อยู่
แบ่งปันข้อมูลแนวโน้มภายในและภายนอกหน่วยงาน ตลอดจนชี้ว่าข้อมูลเหล่านั้นจะนำมาเป็นพื้นฐานในการกำหนดกลยุทธ์ของหน่วยงานได้อย่างไร', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('202', 3, 'แสดงสมรรถนะระดับที่ 2 และสื่อสารวิสัยทัศน์', 'ถ่ายทอดวิสัยทัศน์ของหน่วยงานที่ดูแลรับผิดชอบอยู่ด้วยวิธีที่สร้างแรงบันดาลใจ ความกระตือรือร้น และความร่วมแรงร่วมใจให้บรรลุวิสัยทัศน์นั้น
ใช้วิสัยทัศน์นั้นในการกำหนดจุดร่วมและทิศทางสำหรับผู้คนทั้งหลาย โดยเฉพาะอย่างยิ่งในสภาวการณ์ที่กำลังเผชิญการเปลี่ยนแปลง', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('202', 4, 'แสดงสมรรถนะระดับที่ 3 และใช้วิสัยทัศน์มาช่วยกำหนดนโยบายในงาน', 'คิดนอกกรอบ นำเสนอความคิดใหม่เพื่อใช้กำหนดนโยบายในงานเพื่อประโยชน์หรือโอกาสของภาครัฐหรือสาธารณชนโดยรวมอย่างที่ไม่มีผู้ใดคิดมาก่อน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('202', 5, 'แสดงสมรรถนะระดับที่ 4 และคำนึงถึงวิสัยทัศน์ระดับโลก', 'กำหนดเป้าหมายและทิศทาง เพื่องานในภาครัฐด้วยความเข้าใจอย่างแจ่มแจ้งว่าเป้าหมายเหล่านั้นสอดคล้องกับบริบทของประเทศไทยในประชาคมโลกอย่างไร
คาดการณ์ได้ว่าสถานการณ์ในประเทศอาจได้รับผลกระทบอย่างไรจากการเปลี่ยนแปลงทุกด้านทั้งภายในและภายนอกประเทศ และเสนอกลยุทธ์เพื่อให้ประเทศชาติได้รับประโยชน์สูงสุดจากการเปลี่ยนแปลงนั้น', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('203', 1, 'เข้าใจกลยุทธ์ภาครัฐ', 'เข้าใจภารกิจ นโยบาย กลยุทธ์ภาครัฐและองค์กรที่สังกัด อีกทั้งเข้าใจว่ามีความเกี่ยวโยงกับภารกิจของหน่วยงานที่ตนดูแลรับผิดชอบอยู่ได้อย่างไร
สามารถวิเคราะห์ปัญหา อุปสรรคหรือโอกาสของหน่วยงานตนในการบรรลุผลสัมฤทธิ์ได้', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('203', 2, 'แสดงสมรรถนะระดับที่ 1 และประยุกต์ประสบการณ์ในการกำหนดกลยุทธ์การปฏิบัติงานของหน่วยงาน', 'ประยุกต์ประสบการณ์และบทเรียนในอดีตมาใช้กำหนดกลยุทธ์ของหน่วยงานให้สอดคล้องกับกลยุทธ์ภาครัฐ และสามารถบรรลุภารกิจที่กำหนดไว้
ใช้ความรู้ความเข้าใจในระบบราชการมาวิเคราะห์ปรับกลยุทธ์ หรือยุทธวิธีเชิงรุกในการปฏิบัติงานของหน่วยงานให้เหมาะสมกับสถานการณ์ภายในที่เกิดขึ้นได้', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('203', 3, 'แสดงสมรรถนะระดับที่ 2 และประยุกต์ทฤษฎีหรือแนวคิดซับซ้อนในการกำหนดกลยุทธ์การปฏิบัติงานในอนาคต', 'ประยุกต์ทฤษฎี หรือแนวคิดซับซ้อนที่มีฐานมาจากองค์ความรู้หรือข้อมูลเชิงประจักษ์ ในการคิดและพัฒนาเป้าหมายหรือกลยุทธ์ในการปฏิบัติงานขององค์กรหรือหน่วยงานที่ตนดูแลรับผิดชอบอยู่
ประยุกต์ Best Practice หรือผลการวิจัยต่างๆ มากำหนดโครงการหรือแผนงานเชิงกลยุทธ์ที่ผลสัมฤทธิ์มีประโยชน์ระยะยาวต่อองค์การหรือหน่วยงานที่ตนดูแลรับผิดชอบอยู่', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('203', 4, 'แสดงสมรรถนะระดับที่ 3 และเชื่อมโยงสถานการณ์ในประเทศเพื่อกำหนดกลยุทธ์ในการปฏิบัติงานทั้งในปัจจุบันและในอนาคต', 'ประเมินและสังเคราะห์สถานการณ์ ประเด็น หรือปัญหาทางเศรษฐกิจ สังคม การเมืองภายในประเทศหรือของโลกที่ซับซ้อนด้วยกรอบแนวคิดและวิธีพิจารณาแบบมองภาพองค์รวม เพื่อใช้ในการกำหนดกลยุทธ์ภาครัฐหรือหน่วยงานที่ตนดูแลรับผิดชอบอยู่ให้ตอบสนองการเปลี่ยนแปลงดังกล่าวได้อย่างมีประสิทธิภาพสูงสุด
คาดการณ์สถานการณ์ในอนาคตและกำหนดกลยุทธ์ แผนหรือนโยบายเชิงยุทธศาสตร์เพื่อตอบสนองโอกาสหรือประเด็นปัญหาที่เกิดขึ้นจากสถานการณ์ภายในประเทศหรือต่างประเทศที่เปลี่ยนแปลงไปเพื่อให้องค์กรบรรลุตามพันธกิจที่ได้รับผิดชอบ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('203', 5, 'แสดงสมรรถนะระดับที่ 4 และบูรณาการองค์ความรู้ใหม่มาใช้ในงานกลยุทธ์ภาครัฐ', 'สรรค์สร้างและบูรณาการองค์ความรู้ใหม่มาใช้ในงานกลยุทธ์ภาครัฐ โดยพิจารณาจากบริบทประเทศไทยในภาพรวมและปรับให้เหมาะสม ปฏิบัติได้จริง
คิดและปรับเปลี่ยนทิศทางของกลยุทธ์การพัฒนาประเทศในภาพรวม ให้เป็นกลยุทธ์ใหม่ที่ช่วยผลักดันให้เกิดการพัฒนาอย่างต่อเนื่องและยั่งยืนขึ้นได้', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('204', 1, 'เห็นความจำเป็นของการปรับเปลี่ยน', 'เห็นความจำเป็นของการปรับเปลี่ยน และปรับพฤติกรรมหรือแผนการทำงานให้สอดคล้องกับการปรับเปลี่ยนที่เกิดขึ้นภายในองค์กร
เข้าใจและยอมรับถึงความจำเป็น ทิศทางและขอบเขตของการปรับเปลี่ยน /เปลี่ยนแปลง และตั้งใจในการเรียนรู้เพื่อให้สามารถปรับตัวให้สอดคล้องกับการปรับเปลี่ยน/เปลี่ยนแปลงนั้นได้', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('204', 2, 'แสดงสมรรถนะระดับที่ 1 และสนับสนุนให้ผู้อื่นเข้าใจการปรับเปลี่ยนที่จะเกิดขึ้น', 'ช่วยเหลือให้ผู้อื่นเข้าใจถึงการปรับเปลี่ยนที่เกิดขึ้นภายในองค์กร ความจำเป็นและประโยชน์ของการปรับเปลี่ยนนั้นๆ
สนับสนุนความพยายามในการปรับเปลี่ยนองค์กร พร้อมทั้งมีส่วนร่วมสำคัญในการเสนอแนะวิธีการที่จะช่วยให้การปรับเปลี่ยนดำเนินไปอย่างมีประสิทธิภาพมากขึ้น', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('204', 3, 'แสดงสมรรถนะระดับที่ 2 และกระตุ้น และสร้างแรงจูงใจให้ผู้อื่นเห็นความสำคัญของการปรับเปลี่ยนที่เกิดขึ้น', 'กระตุ้น และสร้างแรงจูงใจให้ผู้อื่นเห็นความสำคัญของการปรับเปลี่ยนที่เกิดขึ้นเพื่อให้เกิดการร่วมแรงร่วมใจให้เกิดการเปลี่ยนแปลงนั้นขึ้นจริง
เน้นย้ำ และสร้างความชัดเจนโดยการอธิบายสาเหตุ ความจำเป็น ประโยชน์ ฯลฯ ของการปรับเปลี่ยนที่เกิดขึ้นอยู่เสมอ
เปรียบเทียบให้เห็นว่าสิ่งที่ควรจะเป็นและสิ่งที่ประพฤติปฏิบัติกันอยู่นั้นแตกต่างกันอย่างไร', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('204', 4, 'แสดงสมรรถนะระดับที่ 3 และวางแผนงานที่ดีสำหรับการปรับเปลี่ยนในองค์กรจนทำให้เห็นชัดเจนว่าการเปลี่ยนแปลงนั้นเป็นประโยชน์ต่อองค์กรอย่างไร', 'วางแผนอย่างเป็นระบบและชี้ให้เห็นผลสัมฤทธิ์จากการปรับเปลี่ยนองค์กรที่กำลังจะดำเนินการ 
เตรียมแผนการบริหารการเปลี่ยนแปลงและติดตามแผนงานอย่างสม่ำเสมอเพื่อให้องค์กรสามารถรับมือกับการเปลี่ยนแปลงนั้นๆ ได้อย่างมีประสิทธิภาพสูงสุด
สร้างแรงจูงใจให้ผู้สนับสนุนและสร้างการยอมรับจากผู้ท้าทายให้เห็นโทษของการนิ่งเฉยและเห็นถึงประโยชน์ของการเปลี่ยนแปลงจากสภาวการณ์ปัจจุบันและอยากมีส่วนร่วมในการเปลี่ยนแปลงนั้น', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('204', 5, 'แสดงสมรรถนะระดับที่ 4 และดำเนินการตามแผนให้เกิดการปรับเปลี่ยนอย่างมีประสิทธิภาพและเหมาะสม', 'เป็นผู้นำในการปรับเปลี่ยนขององค์กร และผลักดันอย่างจริงจังให้การปรับเปลี่ยนดำเนินไปได้อย่างราบรื่นและประสบความสำเร็จสูงสุด
ปลุกขวัญกำลังใจ และสร้างศรัทธาความเชื่อมั่น ในการขับเคลื่อนให้เกิดการปรับเปลี่ยน/เปลี่ยนแปลงอย่างมีประสิทธิภาพสูงสุดแก่องค์กรลาภครัฐโดยรวม', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('205', 1, 'ไม่แสดงพฤติกรรมอันไม่เหมาะสม', 'ไม่แสดงพฤติกรรมไม่สุภาพหรือไม่เหมาะสม แม้จะรู้สึกว่าถูกกระตุ้นทางอารมณ์ แต่สามารถระงับการกระทำนั้นไว้ได้
อดกลั้นไม่แสดงพฤติกรรมหุนหันพลันแล่น', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('205', 2, 'แสดงสมรรถนะระดับที่ 1 และหลีกเลี่ยงหรือเบี่ยงเบนสถานการณ์ที่ทำให้เกิดความรุนแรงทางอารมณ์', 'อาจเลี่ยงออกไปจากสถานการณ์ (ที่ทำให้เกิดความรุนแรงทางอารมณ์) ชั่วคราวหากกระทำได้ หรืออาจเปลี่ยนหัวข้อสนทนา หรือหยุดพักชั่วคราวเพื่อสงบสติอารมณ์', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('205', 3, 'แสดงสมรรถนะระดับที่ 2 และมีพฤติกรรมตอบโต้ได้อย่างสงบหรือสร้างสรรค์ แม้จะถูกยั่วยุจากฝ่ายตรงข้าม', 'รู้สึกถึงความรุนแรงทางอารมณ์ในระหว่างการสนทนา หรือการปฏิบัติงาน เช่น ความโกรธ ความผิดหวัง หรือความกดดัน แต่ไม่ได้แสดงออกมา ไม่โต้ตอบรุนแรงแม้จะถูกยั่วยุจากฝ่ายตรงข้าม และยังคงครองสติปฏิบัติตนต่อไปได้อย่างสงบ
รู้สึกถึงความรุนแรงทางอารมณ์ในระหว่างการสนทนา หรือการปฏิบัติงาน แต่สามารถเลือกวิธีการแสดงออกในทางสร้างสรรค์เพื่อแก้ไขสถานการณ์ให้ดีขึ้น
เข้าใจอารมณ์และสาเหตุแห่งอารมณ์ของตนอย่างลึกซึ้ง และเตรียมการหรือหาวิธีแสดงออกที่เหมาะสมเพื่อไม่ให้อารมณ์ของตนมีผลในเชิงลบทั้งต่อตนเองและผู้อื่น', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('205', 4, 'แสดงสมรรถนะระดับที่ 3 และจัดการความเครียดอย่างมีประสิทธิภาพ', 'สามารถปฏิบัติงานหรือตอบสนองอย่างสร้างสรรค์ในสภาวะความกดดันต่อเนื่อง
สามารถจัดการกับความเครียดหรือผลกระทบที่อาจจะเกิดขึ้นจากความรุนแรงทางอารมณ์ได้อย่างมีประสิทธิภาพ
อาจประยุกต์ใช้วิธีการเฉพาะตน หรือวางแผนล่วงหน้าเพื่อจัดการกับอารมณ์และความเครียดที่อาจจะเกิดขึ้น
มองโลกอย่างเข้าใจและบริหารจัดการอารมณ์ของตนได้อย่างมีประสิทธิภาพเพื่อลดความเครียดทั้งต่อตนเองและผู้ร่วมงาน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('205', 5, 'แสดงสมรรถนะระดับที่ 4 และเอาชนะอารมณ์ด้วยความเข้าใจ', 'ละวางอารมณ์รุนแรงทั้งปวง โดยการพยายามทำความเข้าใจต้นเหตุ เข้าใจตนเอง เข้าใจสถานการณ์ และเข้าใจคู่กรณี ตลอดจนบริบทและปัจจัยแวดล้อมต่างๆ อาจให้อภัยหรือปล่อยวางได้ตามแต่กรณี
ปกครองผู้ใต้บังคับบัญชาด้วยความเมตตากรุณาและเป็นธรรม ส่งเสริมการมองผู้อื่นในแง่ดีและความสมานฉันท์ในหมู่ลูกน้อง', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('206', 1, 'สอนงานและให้คำแนะนำเกี่ยวกับวิธีปฏิบัติงาน', 'ให้คำแนะนำอย่างละเอียด และ/หรือสาธิตวิธีปฏิบัติงาน เพื่อเป็นตัวอย่าง
สอนงาน หรือให้คำแนะนำที่เฉพาะเจาะจงเกี่ยวกับการพัฒนางานหรือการปฏิบัติตน
ชี้แนะแหล่งข้อมูล และทรัพยากรอื่นๆ เพื่อใช้ในการพัฒนาของผู้อื่น', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('206', 2, 'แสดงสมรรถนะระดับที่ 1 และตั้งใจพัฒนาให้ผู้ใต้บังคับบัญชามีศักยภาพ', 'เข้าใจข้อดีและข้อด้อยของผู้ใต้บังคับบัญชาและสามารถให้คำปรึกษาชี้แนะแนวทางในการพัฒนาส่งเสริมข้อดีให้โดดเด่นหรือปรับปรุงข้อด้อยให้ลดลง
ให้โอกาสผู้ใต้บังคับบัญชาได้แสดงออกถึงศักยภาพด้านดีของตนเพื่อเสริมความมั่นใจในการปฏิบัติหน้าที่', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('206', 3, 'แสดงสมรรถนะระดับที่ 2 และวางแผนและให้โอกาสผู้ใต้บังคับบัญชาแสดงความสามารถในการทำงานอย่างเป็นระบบ', 'วางแผนอย่างเป็นระบบในการพัฒนาบุคลากร เช่น วางแผนการพัฒนารายบุคคลในระยะยาว วางแผนหมุนเวียนงาน  เป็นต้น
วางแผนและมอบหมายงานที่เหมาะสมและมีประโยชน์ รวมถึงหาโอกาสในการฝึกอบรม พัฒนาหรือประสบการณ์อื่นๆ อย่างสม่ำเสมอเพื่อสนับสนุนการเรียนรู้และการพัฒนาของผู้อื่น
พร้อมจะยอมเสี่ยงบ้าง โดยยอมให้ผู้อื่นตัดสินใจในบางเรื่องในงานประจำ ทั้งนี้เพื่อผู้อื่นมีโอกาสในการพัฒนาและบริหารจัดการงานด้วยตนเอง 
เปิดโอกาสให้ผู้ใตับังคับบัญชาได้ริเริ่มสิ่งใหม่ด้วยตนเองโดยการมอบหมายอำนาจตัดสินใจในบางเรื่องให้ภายใต้การกำกับดูแล', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('206', 4, 'แสดงสมรรถนะระดับที่ 3 และช่วยขจัดข้อจำกัดของผู้ใต้บังคับบัญชาเพื่อพัฒนาศักยภาพ', 'กล้าที่จะให้คำปรึกษาที่สะท้อนผลงานและศักยภาพที่แท้จริงของผู้อื่น และสามารถระบุความจำเป็นหรือความต้องการในการฝึกอบรมหรือพัฒนา ที่เป็นประโยชน์ต่องาน และขจัดข้อจำกัดของบุคคลผู้นั้น
สามารถปรับเปลี่ยนทัศนคติ บุคลิกภาพ หรือรูปแบบการใช้ชีวิตเดิมที่เป็นปัจจัยขัดขวางการพัฒนาศักยภาพของผู้ใต้บังคับบัญชา 
มีจิตวิทยาในการเข้าถึงจิตใจและเหตุผลเบื้องหลังพฤติกรรมของแต่ละบุคคล เพื่อนำมาสนับสนุนในการล้มล้างความเชื่อ และค่านิยมเชิงลบและพัฒนาศักยภาพในการทำงานให้ดีขึ้น', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('206', 5, 'แสดงสมรรถนะระดับที่ 4 และสร้างวัฒนธรรมการทำงานที่ให้อำนาจและสอนงาน', 'สนับสนุนส่งเสริมวัฒนธรรมการทำงานที่ให้อำนาจและมีการสอนงานกันเอง เพื่อพัฒนาการร่วมกันของบุคลากรภายในองค์กร โดยการสร้างบรรยากาศการทำงานที่เอื้อต่อวัฒนธรรมดังกล่าว ตลอดจนจัดหาทรัพยากรและการสนับสนุนต่างๆ ที่จำเป็นให้จนเกิดการพัฒนาและเรียนรู้อย่างเป็นระบบในองค์กร
ผลักดันและสร้างเสริมให้เกิดวัฒนธรรมแห่งการเรียนรู้ รวมถึงดำเนินการอย่างเป็นรูปธรรม เพื่อรณรงค์ ส่งเสริม ผลักดัน แผนการพัฒนาทรัพยากรบุคคลในหน่วยงานของตนอย่างเป็นระบบ โดยตระหนักถึงความสำคัญของพลังศักยภาพของคนในการพัฒนาสังคมและประเทศชาติ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('301', 1, 'ตระหนัก เห็นความสำคัญ และประโยชน์ของการกำกับติดตามการดำเนินงานต่างๆ ของผู้อื่น', 'ตระหนัก เห็นความสำคัญ ความจำเป็น และประโยชน์ของการกำกับติดตามการดำเนินงานต่างๆ ของผู้อื่นที่เกี่ยวข้องในงาน เพื่อให้ผู้อื่นปฏิบัติตามมาตรฐาน กฎระเบียบ หรือข้อบังคับที่กำหนดไว้ และเพื่อให้เกิดประโยชน์ในการดำเนินงานของตนเอง หน่วยงาน หรือองค์กร', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('301', 2, 'แสดงสมรรถนะระดับที่ 1 และกระตือรือร้นในการกำกับติดตามการดำเนินงานต่างๆ ของผู้อื่น', 'แสดงพฤติกรรมกระตือรือร้นในการกำกับติดตามการดำเนินงานของผู้อื่นที่เกี่ยวข้องในงาน เพื่อให้ผู้อื่นปฏิบัติตามมาตรฐาน กฎระเบียบ หรือข้อบังคับที่กำหนดไว้ให้เกิดประโยชน์ในการดำเนินงานของตนเอง หน่วยงาน หรือองค์กร และสามารถระบุความเป็นไป หรือความก้าวหน้าในการดำเนินงานต่างๆ ของผู้อื่นได้', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('301', 3, 'แสดงสมรรถนะระดับที่ 2 และกำกับติดตามการดำเนินงานต่างๆ ของผู้อื่นอย่างสม่ำเสมอ', 'ดำเนินการกำกับติดตามการดำเนินงานต่างๆ ของผู้อื่นอย่างสม่ำเสมอ และเป็นระยะ และสามารถวิเคราะห์ และระบุข้อมูล ข้อเท็จจริง สาเหตุ สิ่งผิดปกติ และความเปลี่ยนแปลงต่างๆ ที่เกิดขึ้นได้อย่างถูกต้อง เพื่อนำไปสู่การดำเนินการต่างๆ ได้อย่างถูกต้อง เหมาะสม และสอดคล้องกับมาตรฐาน กฎระเบียบ หรือข้อบังคับที่กำหนดไว้
ปรับสถานการณ์ กระบวนการ หรือวิธีการต่างๆ เพื่อจำกัดทางเลือกของผู้อื่น หรือเพื่อบีบคั้นให้ผู้อื่นปฏิบัติในกรอบที่ถูกต้องตามกฎหมายหรือระเบียบปฏิบัติที่กำหนดไว้', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('301', 4, 'แสดงสมรรถนะระดับที่ 3 และกำกับติดตาม และตรวจสอบความถูกต้องของการดำเนินงานต่างๆ ของผู้อื่นอย่างใกล้ชิด', 'สำรวจ กำกับ ติดตาม และตรวจสอบการดำเนินงานต่างๆ ของผู้อื่นอย่างใกล้ชิดและในเชิงลึก รวมทั้งวิเคราะห์ ประมวล วิจัย และสรุปผลการดำเนินการ การตอบสนอง และการให้บริการต่างๆ ที่ถูกต้อง เหมาะสม และสอดคล้องกับมาตรฐาน กฎระเบียบ หรือข้อบังคับที่กำหนดไว้
หมั่นควบคุม ตรวจตรา และตรวจสอบความถูกต้องของการดำเนินงานต่างๆ ในทุกขั้นตอนอย่างละเอียดของผู้อื่นที่เกี่ยวข้องในงานให้เป็นไปตามมาตรฐาน กฎระเบียบ หรือข้อบังคับที่กำหนดไว้
ออกคำเตือน (โดยชัดแจ้งว่าจะเกิดอะไรขึ้นหากผู้อื่นไม่ปฏิบัติตามมาตรฐานที่กำหนดไว้หรือกระทำการละเมิดกฎหมาย) และสั่งการให้ปรับปรุงการดำเนินงานต่างๆ ในเชิงปริมาณหรือคุณภาพให้ถูกต้องตามมาตรฐาน กฎระเบียบ หรือข้อบังคับที่กำหนดไว้', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('301', 5, 'แสดงสมรรถนะระดับที่ 4 และจัดการกับการดำเนินงานต่างๆ ที่ไม่ดี ไม่ถูกต้อง หรือสิ่งผิดกฎหมายอย่างเด็ดขาดตรงไปตรงมา', 'ดำเนินการอย่างตรงไปตรงมา หรือใช้วิธีเผชิญหน้าอย่างเด็ดขาดเมื่อผู้อื่นหรือหน่วยงานภายใต้การกำกับดูแลมีการดำเนินงานต่างๆ ที่ไม่ดี ไม่ถูกต้อง หรือทำผิดกฎหมายอย่างร้ายแรง 
กำหนด หรือปรับมาตรฐาน ข้อบังคับ หรือกฎระเบียบต่างๆ ที่เกี่ยวข้องให้แตกต่าง ท้าทาย หรือสูงขึ้น (เมื่อสภาวแวดล้อมเปลี่ยนไป) เพื่อส่งเสริมให้บุคลากรเกิดการพัฒนาความสามารถให้สูงขึ้น', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('302', 1, 'กระทำสิ่งต่างๆ ตามมาตรฐาน หรือตามกฎระเบียบข้อบังคับที่กำหนดไว้', 'ปฏิบัติหน้าที่ด้วยความโปร่งใส  ถูกต้องทั้งตามหลักกฎหมายระเบียบข้อบังคับที่กำหนดไว้
ยึดถือหลักการและแนวทางตามหลักวิชาชีพอย่างสม่ำเสมอ
เปิดเผยข้อมูลหรือเหตุผลอย่างตรงไปตรงมา', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('302', 2, 'แสดงสมรรถนะระดับที่ 1 และยึดมั่นในแนวทางหรือขอบเขตข้อจำกัดในการกระทำสิ่งต่างๆ', 'ปฏิเสธข้อเรียกร้องของผู้อื่นหรือหน่วยงานที่เกี่ยวข้อง ที่ขาดเหตุผลหรือผิดกฎระเบียบหรือแนวทางนโยบายที่วางไว้
ดำเนินการอย่างไม่บิดเบือน โดยไม่อ้างข้อยกเว้นให้ตนเองหรือผู้ใต้บังคับบัญชาหรือคนรู้จักหรือหน่วยงานภายใต้การดูแลหากมีการดำเนินงานที่ยอมรับไม่ได้', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('302', 3, 'แสดงสมรรถนะระดับที่ 2 และติดตามควบคุมให้ปฏิบัติตามมาตรฐานหรือตามกฎหมายข้อบังคับ', 'หมั่นควบคุมตรวจตราการดำเนินการของหน่วยงานที่ดูแลรับผิดชอบให้เป็นไปอย่างถูกต้องตามกฎระเบียบหรือแนวทางนโยบายที่วางไว้
ออกคำเตือนหรือพยายามประนีประนอมอย่างชัดแจ้งว่าจะเกิดอะไรขึ้นหากผลงานไม่ได้มาตรฐานที่กำหนดไว้หรือกระทำการละเมิดกฎระเบียบหรือแนวทางนโยบายที่วางไว้', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('302', 4, 'แสดงสมรรถนะระดับที่ 3 และรับผิดชอบในสิ่งที่อยู่ในการดูแล', 'กล้าตัดสินใจในหน้าที่ โดยสั่ง ต่อรองหรือประนีประนอมให้บุคคลหรือหน่วยงานที่ฝ่าฝืนกฎเกณฑ์ ระเบียบ นโยบายหรือมาตรฐานที่ตั้งไว้ไปปรับปรุงผลงานในเชิงปริมาณหรือคุณภาพให้เข้าเกณฑ์มาตรฐาน  แม้ว่าผลของการตัดสินใจอาจสร้างศัตรูหรือก่อความไม่พึงพอใจให้แก่ผู้ที่เกี่ยวข้องหรือเสียประโยชน์
กล้ายอมรับความผิดพลาดและจัดการความความผิดพลาดที่จัดทำลงไป', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('302', 5, 'แสดงสมรรถนะระดับที่ 4 และจัดการกับผลงานไม่ดีหรือสิ่งผิดกฎระเบียบอย่างเด็ดขาดตรงไปตรงมา', 'ใช้วิธีเผชิญหน้าอย่างเปิดเผยตรงไปตรงมาเมื่อผู้อื่นหรือหน่วยงานภายใต้การกำกับดูแล มีปัญหาผลงานไม่ดีหรือทำผิดกฎระเบียบอย่างร้ายแรง
ยืนหยัดพิทักษ์ผลประโยชน์ตามกฎเกณฑ์ขององค์กร แม้ในสถานการณ์ที่อาจเสี่ยงต่อความมั่นคงในตำแหน่งหน้าที่การงาน หรืออาจเสี่ยงภัยต่อชีวิต', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('303', 1, 'เข้าใจแผนและนโยบายขององค์กรหรือหน่วยงานต่างๆ ในองค์กร', 'เข้าใจนโยบาย กลยุทธ์ของหน่วยงาน หรือองค์กร และสามารถนำความเข้าใจนั้นมาวิเคราะห์ปัญหา อุปสรรค โอกาสของหน่วยงาน หรือองค์กรออกเป็นประเด็นย่อยๆ ได้', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('303', 2, 'แสดงสมรรถนะระดับที่ 1 และประยุกต์ความเข้าใจ รูปแบบหรือประสบการณ์ไปสู่ข้อเสนอหรือแนวทางต่างๆ ในงาน', 'สามารถระบุปัญหาในสถานการณ์ปัจจุบันที่อาจมีความคล้ายคลึง หรือต่างจากประสบการณ์ที่เคยประสบมาใช้กำหนดข้อเสนอหรือแนวทาง (Implication) เชิงกลยุทธ์ที่สนับสนุนให้องค์กรหรือหน่วยงานต่างๆ ที่เกี่ยวข้องบรรลุภารกิจที่กำหนดไว้หรือให้ปฏิบัติการได้เหมาะสมกับสถานการณ์ที่เกิดขึ้นได้', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('303', 3, 'แสดงสมรรถนะระดับที่ 2 และประยุกต์ทฤษฎีหรือแนวคิดซับซ้อนในการพิจาณาสถานการณ์ หรือกำหนดแผนงานหรือข้อเสนอต่างๆ', 'ประยุกต์ทฤษฎี หรือแนวคิดซับซ้อนที่มีฐานมาจากองค์ความรู้หรือข้อมูลเชิงประจักษ์ ในการพิจารณาสถานการณ์ แยกแยะข้อดีข้อเสียของประเด็นต่างๆ ในการปฏิบัติงานขององค์กรหรือหน่วยงานที่ตนดูแลรับผิดชอบอยู่
สามารถใช้แนวคิดต่างๆ ที่เรียนรู้มาเชื่อมโยงอธิบายเหตุผลความเป็นมา แยกแยะข้อดี และข้อเสียของปัญหา สถานการณ์ ฯลฯ เป็นประเด็นต่างๆ ได้อย่างมีเหตุมีผล
ประยุกต์ Best Practice หรือผลการวิจัยต่างๆ มากำหนดโครงการหรือแผนงานที่ผลสัมฤทธิ์มีประโยชน์ต่อองค์กรหรืองานที่ตนดูแลรับผิดชอบอยู่', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('303', 4, 'แสดงสมรรถนะระดับที่ 3 และเชื่อมโยงสถานการณ์ในประเทศและต่างประเทศเพื่อกำหนดแผนได้อย่างทะลุปรุโปร่ง', 'ประเมินและสังเคราะห์สถานการณ์ ประเด็น หรือปัญหาทางเศรษฐกิจ สังคม การเมืองภายในประเทศและต่างประเทศที่ซับซ้อนด้วยกรอบแนวคิดและวิธีพิจารณาแบบมองภาพองค์รวม เพื่อใช้ในการกำหนดแผนหรือนโยบายขององค์กรหรือหน่วยงานที่ตนดูแลรับผิดชอบอยู่
ระบุได้ว่าอะไรเป็นเหตุเป็นผลแก่กันในสถานการณ์หนึ่งๆ ในระดับหน่วยงาน/องค์กร/ประเทศ แล้วแยกแยะข้อดีข้อเสียของประเด็นต่างๆ รวมถึงอธิบายชี้แจงสถานการณ์ที่ซับซ้อนดังกล่าวให้สามารถเป็นที่เข้าใจได้', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('303', 5, 'แสดงสมรรถนะระดับที่ 4 และสร้างสรรค์และบูรณาการองค์ความรู้ใหม่มาใช้ในงานกลยุทธ์', 'สรรค์สร้างและบูรณาการองค์ความรู้ใหม่มาใช้ในงานกลยุทธ์ โดยพิจารณาจากบริบทประเทศไทยและระบบอุตสาหกรรมในภาพรวมและปรับให้เหมาะสม ปฏิบัติได้จริง
วิเคราะห์ปัญหาในแง่มุมที่ลึกซึ้งถึงปรัชญาแนวคิดเบื้องหลังของประเด็นหรือทางเลือกต่างๆ ที่ซับซ้อน อันนำไปสู่การประดิษฐ์คิดค้น การสร้างสรรค์และนำเสนอรูปแบบ วิธี ตลอดจนองค์ความรู้ใหม่ที่ไม่เคยปรากฏมาก่อนและเป็นประโยชน์ต่อองค์กร หรือสังคมและประเทศชาติโดยรวม', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('304', 1, 'ต้องการทำงานให้ถูกต้องและชัดเจน', 'ตั้งใจทำงานให้ถูกต้อง สะอาดเรียบร้อย
ละเอียดถี่ถ้วนในการปฏิบัติตามขั้นตอนการปฏิบัติงาน กฎ ระเบียบที่วางไว้
แสดงอุปนิสัยรักความเป็นระเบียบเรียบร้อยทั้งในงานและในสภาวะแวดล้อมรอบตัว อาทิ จัดระเบียบโต๊ะทำงาน และบริเวณสำนักงานที่ตนปฏิบัติหน้าที่อยู่ ริเริ่มหรือร่วมดำเนินกิจกรรมเพื่อความเป็นระเบียบของสถานที่ทำงาน อาทิ กิจกรรม 5 ส. ด้วยความสมัครใจ กระตือรือร้น ฯลฯ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('304', 2, 'แสดงสมรรถนะระดับที่ 1 และตรวจทานความถูกต้องของงานที่ตนรับผิดชอบ', 'ตรวจทานความถูกต้องของงานอย่างละเอียดรอบคอบ เพื่อให้งานมีความถูกต้องสูงสุด
ลดข้อผิดพลาดที่เคยเกิดขึ้นแล้วจากความไม่ตั้งใจ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('304', 3, 'แสดงสมรรถนะระดับที่ 2 และดูแลความถูกต้องของงานทั้งของตนและผู้อื่น (ที่อยู่ในความรับผิดชอบของตน)', 'ตรวจสอบความถูกต้องโดยรวมของงานของตนเอง เพื่อมิให้มีข้อผิดพลาดประการใดๆ เลย
ตรวจสอบความถูกต้องโดยรวมของงานผู้อื่น (ผู้ใต้บังคับบัญชา หรือเจ้าหน้าที่ที่เกี่ยวข้องภายในหน่วยงานหรือองค์กร)  โดยอิงมาตรฐานการปฏิบัติงาน หรือกฎหมาย ข้อบังคับ กฎระเบียบที่เกี่ยวข้อง', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('304', 4, 'แสดงสมรรถนะระดับที่ 3 และกำกับตรวจสอบขั้นตอนการปฏิบัติงานโดยละเอียด', 'ตรวจสอบว่าผู้อื่นปฏิบัติตามขั้นตอนการทำงานที่วางไว้หรือไม่ให้ความเห็นและชี้แนะให้ผู้อื่นปฏิบัติตามขั้นตอนการทำงานที่วางไว้ เพื่อความถูกต้องของงาน
ตรวจสอบความก้าวหน้าและความถูกต้อง/คุณภาพของผลลัพธ์ของโครงการตามกำหนดเวลาที่วางไว้ 
ระบุข้อบกพร่องหรือข้อมูลที่ขาดหายไป และกำกับดูแลให้หาข้อมูลเพิ่มเติมเพื่อให้ได้ผลลัพธ์หรือผลงานที่มีคุณภาพตามเกณฑ์ที่กำหนด', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('304', 5, 'แสดงสมรรถนะระดับที่ 4 และสร้างความชัดเจนของความถูกต้องและคุณภาพของขั้นตอนการทำงานหรือผลงานหรือโครงการโดยละเอียด', 'สร้างความชัดเจนของความถูกต้องและคุณภาพของขั้นตอนการทำงานหรือผลงานหรือโครงการโดยละเอียดเพื่อควบคุมให้ผู้อื่นปฏิบัติตามขั้นตอนการทำงานที่วางไว้อย่างถูกต้องและเกิดความโปร่งใสตรวจสอบได้ 
สร้างระบบและวิธีการที่สามารถกำกับตรวจสอบความก้าวหน้าและความถูกต้อง/คุณภาพของผลงานหรือขั้นตอนในการปฏิบัติงานของผู้อื่น หรือหน่วยงานอื่น ได้อย่างสม่ำเสมอ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('305', 1, 'เข้าใจความจำเป็น ความสำคัญ และ/หรือแสวงหาโอกาส และช่องทางในการให้ความรู้ และการพัฒนาผู้ประกอบการ หรือเครือข่าย', 'เข้าใจความจำเป็น และความสำคัญในการให้องค์ความรู้ คำแนะนำ ภูมิปัญญา นวัตกรรม และเทคโนโลยีต่างๆ ที่เป็นประโยชน์แก่ผู้ประกอบการ หรือเครือข่าย เพื่อให้ผู้ประกอบการ หรือเครือข่ายเกิดการเสริมสร้างความรู้ ความเข้าใจ และสามารถนำไปใช้ให้เกิดประโยชน์ในหน่วยงานได้
แสวงหาโอกาส และช่องทางในการให้องค์ความรู้ คำแนะนำ ภูมิปัญญา นวัตกรรม และเทคโนโลยีต่างๆ แก่ผู้ประกอบการ หรือเครือข่าย โดยมีเจตนาที่จะช่วยให้ผู้ประกอบการ หรือเครือข่ายเกิดการพัฒนาความรู้ ความสามารถ และสามารถเพิ่มขีดความสามารถในการแข่งขันได้', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('305', 2, 'แสดงสมรรถนะระดับที่ 1 และให้องค์ความรู้ ภูมิปัญญา นวัตกรรม และเทคโนโลยีทั่วไปอย่างกว้างๆ แก่ผู้ประกอบการ หรือเครือข่าย', 'ริเริ่ม และให้องค์ความรู้ คำแนะนำ ภูมิปัญญา นวัตกรรม และเทคโนโลยีต่างๆ ทั่วไปอย่างกว้างๆ แก่ผู้ประกอบการ หรือเครือข่าย เพื่อให้ผู้ประกอบการ หรือเครือข่ายสามารถนำไปปรับและประยุกต์ใช้ในหน่วยงานให้เกิดประโยชน์', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('305', 3, 'แสดงสมรรถนะระดับที่ 2 และปรับเปลี่ยนเทคนิค และแนวทางในการให้ความรู้ และการพัฒนาได้อย่างถูกต้อง เหมาะสม และสอดคล้องตามความต้องการในการประกอบการอย่างแท้จริง', 'ปรับเปลี่ยนเทคนิค วิธีการ รูปแบบ และแนวทางในการพัฒนา และการให้ความรู้ได้ถูกต้อง เหมาะสม และสอดคล้องตามความต้องการ หรือการดำเนินงานทางธุรกิจของผู้ประกอบการ หรือเครือข่ายอย่างแท้จริง  
เยื่ยมเยียน ลงพื้นที่ และไปมาหาสู่ผู้ประกอบการ หรือเครือข่ายอย่างสม่ำเสมอ และสามารถให้องค์ความรู้ คำแนะนำ ภูมิปัญญา นวัตกรรม และเทคโนโลยีต่างๆ ได้อย่างถูกต้อง เหมาะสม แและสอดคล้องตามความต้องการของผู้ประกอบการ หรือเครือข่ายอย่างแท้จริง', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('305', 4, 'แสดงสมรรถนะระดับที่ 3 และเล็งเห็นแนวโน้ม ข้อจำกัด โอกาส หรือคาดการณ์ เตรียมการล่วงหน้า เพื่อให้คำแนะนำ และแนวทางในการพัฒนาการประกอบการได้ในระยะยาว', 'เล็งเห็นแนวโน้ม ข้อจำกัด ข้อบกพร่อง และโอกาส ฯลฯ ที่เป็นผลกระทบต่อการพัฒนาและการเพิ่มประสิทธิภาพในการประกอบการของผู้ประกอบการ หรือเครือข่าย และสามารถประยุกต์ และพัฒนาองค์ความรู้ คำแนะนำ ภูมิปัญญา นวัตกรรม และเทคโนโลยีต่างๆ ได้อย่างถูกต้อง เหมาะสม และส่งเสริมการพัฒนาการประกอบการในระยะยาว
คาดการณ์ล่วงหน้าอันอาจก่อให้เกิดปัญหา อุปสรรค และโอกาสในการพัฒนาและเพิ่มประสิทธิภาพ ศักยภาพ และความสามารถของผู้ประกอบการ หรือเครือข่ายในระยะยาว รวมทั้งดำเนินการแก้ไข พัฒนา หรือเตรียมหาทางรับมือกับปัญหา อุปสรรค หรือโอกาสนั้นๆ ได้อย่างมีประสิทธิภาพ
สร้าง รักษา และมีความสัมพันธ์อันดี และแน่นแฟ้นกับผู้ประกอบการ หรือเครือข่ายในความรับผิดชอบ รวมทั้งสามารถพัฒนา และต่อยอดความรู้ นวัตกรรม และเทคโนโลยีใหม่ๆ ที่เป็นประโยชน์ในการประกอบการอย่างแท้จริง และในระยะยาว', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('305', 5, 'แสดงสมรรถนะระดับที่ 4 และกำหนดนโยบายและกรอบการพัฒนาภาคธุรกิจอุตสาหกรรมในภาพรวมได้สอดคล้องกับสภาวการณ์เศรษฐกิจ และอุตสาหกรรมของประเทศ', 'กำหนดนโยบาย แผนยุทธศาสตร์ กลยุทธ์ และกรอบการพัฒนาผู้ประกอบการ หรือเครือข่ายในภาพรวมของประเทศได้สอดคล้องกับสภาวการณ์เศรษฐกิจ และอุตสาหกรรมของประเทศ และสามารถนำไปปฏิบัติใช้จริง (Implementation) เพื่อสนับสนุนและเพิ่มขีดความสามารถในการแข่งขันทางการค้าอย่างยั่งยืน
ปลูกจิตสำนึก กระตุ้น และส่งเสริมให้ทุกหน่วยงานในองค์กรตระหนัก เห็นความสำคัญ และดำเนินการด้านการส่งเสริมและพัฒนาผู้ประกอบการหรือเครือข่ายอย่างเป็นรูปธรรม เพื่อยกระดับมาตรฐานการพัฒนาอุตสาหกรรมของประเทศให้เติบโตและมีประสิทธิภาพสูงสุด
วางแผน พัฒนาแผนการพัฒนา และให้ความรู้ คำแนะนำ และการพัฒนาโดยประยุกต์ Best Practice ผลการวิจัย ความเชี่ยวชาญ หรือประสบการณ์อันยาวนาน ในการกำหนดแผนงานด้านการส่งเสริมและพัฒนาผู้ประกอบการ หรือเครือข่ายได้อย่างถูกต้อง เหมาะสม และนำไปปฏิบัติใช้จริง (Implementation) ได้', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('306', 1, 'วางแผนงานออกเป็นส่วนย่อยๆ', 'วางแผนงานเป็นขั้นตอนอย่างชัดเจน มีผลลัพธ์ สิ่งที่ต้องจัดเตรียม และกิจกรรมต่างๆ ที่จะเกิดขึ้นอย่างถูกต้อง', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('306', 2, 'แสดงสมรรถนะระดับที่ 1 และเห็นลำดับความสำคัญหรือความเร่งด่วนของงาน', 'วางแผนงานได้โดยจัดเรียงงาน หรือกิจกรรมต่างๆ ตามลำดับความสำคัญหรือความเร่งด่วน 
จัดลำดับของงานและผลลัพธ์ในโครงการเพื่อให้สามารถจัดการโครงการให้บรรลุตามแผนและเวลาที่วางไว้ได้ 
วิเคราะห์หาข้อดี ข้อเสียและผลต่อเนื่องของแผนงานที่วาง เพื่อสามารถวางแผนงานใหม่ได้อย่างมีประสิทธิภาพมากขึ้น', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('306', 3, 'แสดงสมรรถนะระดับที่ 2 และวางแผนหรือเชื่อมโยงงานหรือกิจกรรมต่างๆ ที่มีความซับซ้อนเพื่อให้บรรลุตามแผนที่กำหนดไว้ได้', 'วางแผนงานโดยกำหนดกิจกรรม ขั้นตอนการดำเนินงานต่างๆ ที่มีผู้เกี่ยวข้องหลายฝ่ายได้อย่างมีประสิทธิภาพ
วางแผนงานที่มีความเชื่อมโยงหรือซับซ้อนกันหลายๆ งานหรือหลายๆ โครงการโดยกำหนดกิจกรรม ขั้นตอนการดำเนินงานต่างๆ ที่สนับสนุนและไม่ขัดแย้งกันได้อย่างมีประสิทธิภาพ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('306', 4, 'แสดงสมรรถนะระดับที่ 3 และสามารถคาดการณ์ล่วงหน้าเกี่ยวกับปัญหา/งานและเตรียมทางเลือกสำหรับการป้องกัน/แก้ไขปัญหาที่เกิดขึ้น', 'วางแผนงานที่ซับซ้อนโดยกำหนดกิจกรรม ขั้นตอนการดำเนินงานต่างๆ ที่มีหน่วยงานหรือผู้เกี่ยวข้องหลายฝ่าย รวมถึงคาดการณ์ปัญหา อุปสรรค และวางแนวทางการป้องกันแก้ไขไว้ล่วงหน้า อีกทั้งเสนอแนะทางเลือกและข้อดีข้อเสียไว้ให้
เตรียมแผนรับมือกับสิ่งไม่คาดการณ์ไว้ได้อย่างรัดกุมและมีประสิทธิภาพ
วางแผนงานที่มีความเชื่อมโยงหรือซับซ้อนกันหลายๆ งานหรือหลายๆ โครงการโดยกำหนดกิจกรรม ขั้นตอนการดำเนินงานต่างๆ ที่สนับสนุนและไม่ขัดแย้งกันได้อย่างมีประสิทธิภาพ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('306', 5, 'แสดงสมรรถนะระดับที่ 4 และปรับกลยุทธ์ในแผนให้เข้ากับสถานการณ์เฉพาะหน้านั้นอย่างเป็นระบบ', 'ปรับกลยุทธ์และวางแผนอย่างรัดกุมและเป็นระบบให้เข้ากับสถานการณ์ที่เกิดขึ้นอย่างไม่คาดคิด เพื่อแก้ปัญหา อุปสรรค หรือสร้างโอกาสนั้นอย่างมีประสิทธิภาพสูงสุดและให้สามารถบรรลุวัตถุประสงค์เป้าหมายที่กำหนดไว้อย่างมีประสิทธิภาพสูงสุด', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('307', 1, 'ปฏิบัติงานโดยคำนึงถึงความคุ้มค่าและค่าใช้จ่ายที่เกิดขึ้น', 'ตระหนักถึงความคุ้มค่าและค่าใช้จ่ายต่างๆ ที่จะเกิดขึ้นในการปฏิบัติงาน
ปฏิบัติงานตามกระบวนการขั้นตอนที่กำหนดไว้ เพื่อให้สามารถใช้ทรัพยากรไม่เกินขอบเขตที่กำหนด', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('307', 2, 'แสดงสมรรถนะระดับที่ 1 และปฏิบัติงานโดยคำนึงถึงค่าใช้จ่ายที่เกิดขึ้น และมีความพยายามที่จะลดค่าใช้จ่ายเบื้องต้น', 'ตระหนักและควบคุมค่าใช้จ่ายที่เกิดขึ้นในการปฏิบัติงานโดยมีความพยายามที่จะลดค่าใช้จ่ายต่างๆ ที่จะเกิดขึ้น
จัดสรรงบประมาณ ค่าใช้จ่าย ทรัพยากรที่มีอยู่อย่างจำกัดให้คุ้มค่าและเกิดประโยชน์ในการปฏิบัติงานอย่างสูงสุด', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('307', 3, 'แสดงสมรรถนะระดับที่ 2 และกำหนดการใช้ทรัพยากรให้สัมพันธ์กับผลลัพธ์ที่ต้องการ', 'ประเมินผลความมีประสิทธิภาพของการดำเนินงานที่ผ่านมาเพื่อปรับปรุงการจัดสรรทรัพยากรให้ได้ผลผลิตที่เพิ่มขึ้น หรือมีการทำงานที่มีประสิทธิภาพมากขึ้น หรือมีค่าใช้จ่ายที่ลดลง
ระบุข้อบกพร่อง วิเคราะห์ข้อดี ข้อเสียของกระบวนการการทำงานและกำหนดการใช้ทรัพยากรที่สัมพันธ์กับผลลัพธ์ที่ต้องการโดยมองผลประโยชน์ของกรม เป็นหลัก', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('307', 4, 'แสดงสมรรถนะระดับที่ 3 และเชื่อมโยงหรือประสานการบริหารทรัพยากรร่วมกันระหว่างหน่วยงานเพื่อให้เกิดการใช้ทรัพยากรที่คุ้มค่าสูงสุด', 'เลือกปรับปรุงกระบวนการทำงานที่เกิดประสิทธิภาพสูงสุดกับหลายหน่วยงาน และไม่กระทบกระบวนการทำงานต่างๆ ภายในกรม
วางแผนและเชื่อมโยงภารกิจของหน่วยงานตนเองกับหน่วยงานอื่น (Synergy) เพื่อให้การใช้ทรัพยากรของหน่วยงานที่เกี่ยวข้องทั้งหมดเกิดประโยชน์สูงสุด
กำหนดและ/หรือสื่อสารกระบวนการการบริหารทรัพยากรที่สอดคล้องกันทั่วทั้งองค์กร เพื่อเพิ่มขีดความสามารถขององค์กร', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('307', 5, 'แสดงสมรรถนะระดับที่ 4 และเสนอกระบวนการใหม่ๆ ในการทำงานให้มีประสิทธิภาพยิ่งขึ้นเพื่อให้เกิดการพัฒนาที่ยั่งยืน', 'พัฒนากระบวนการใหม่ๆ โดยอาศัยวิสัยทัศน์ ความเชี่ยวชาญ และประสบการณ์ต่างๆ มาประยุกต์ในกระบวนการทำงาน เพื่อลดภาระการบริหารงานให้สามารถดำเนินงานได้อย่างมีประสิทธิภาพสูงสุด
สามารถเพิ่มผลผลิตหรือสร้างสรรค์งานใหม่ ที่โดดเด่นแตกต่างให้กับหน่วยงาน และองค์กร โดยใช้ทรัพยากรเท่าเดิม', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('308', 1, 'มีความยืดหยุ่นในการปฏิบัติหน้าที่', 'เข้าใจความหมายของผู้ติดต่อสื่อสาร และสามารถปรับการทำงานให้คล่องตัวและสอดคล้องกับความต้องการได้', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('308', 2, 'แสดงสมรรถนะระดับที่ 1 และเข้าใจบุคคลหรือสถานการณ์ได้ง่ายและพร้อมยอมรับความจำเป็นที่จะต้องปรับเปลี่ยน', 'เต็มใจ ยอมรับ และเข้าใจความคิดเห็นของผู้อื่นทั้งในเชิงเนื้อหาและนัยเชิงอารมณ์ 
เต็มใจที่จะเปลี่ยนความคิด ทัศนคติ และทำงานให้บรรลุตามเป้าหมาย เมื่อสถานการณ์ปรับเปลี่ยนไป เช่น ได้รับข้อมูลใหม่หรือข้อคิดเห็นใหม่จากผู้เชี่ยวชาญ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('308', 3, 'แสดงสมรรถนะระดับที่ 2 และเข้าใจความหมายแฝงของบุคคลและสถานการณ์และเลือกปฏิบัติงานอย่างยืดหยุ่น', 'มีวิจารณญาณในการปรับให้เข้ากับสถานการณ์เฉพาะหน้า เพื่อให้เกิดผลสัมฤทธิ์ในการปฏิบัติงาน หรือเพื่อให้บรรลุวัตถุประสงค์ของหน่วยงานหรือของกระทรวงฯ
สามารถตีความหมายเบื้องลึกที่ไม่ได้แสดงออกอย่างชัดเจนของบุคคลหรือสถานการณ์ที่เกิดขึ้น แล้วปรับตัวให้สอดคล้อง และเหมาะสมกับกับแต่ละบุคคลหรือสถานการณ์ดังกล่าวได้อย่างมีประสิทธิภาพ
สามารถเลือกทางเลือก วิธีการ หรือกระบวนการมาปรับใช้กับสถานการณ์ที่เฉพาะเจาะจงได้อย่างมีประสิทธิภาพ เพื่อให้ได้ผลงานที่ดี', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('308', 4, 'แสดงสมรรถนะระดับที่ 3 และใช้ความเข้าใจในเชิงลึกต่อบุคคลหรือสถานการณ์มาปรับเปลี่ยนวิธีการดำเนินงานให้ได้งานที่มีประสิทธิภาพสูงสุด', 'ใช้ความเข้าใจอย่างลึกซึ้งในบุคคลหรือสถานการณ์ต่างๆ ให้เป็นประโยชน์ในทำงานให้ได้ผลงานที่มีประสิทธิภาพสูงสุด
ปรับเปลี่ยนวิธีการดำเนินงาน ระเบียบขั้นตอนหรือลักษณะการประสานงานของหน่วยงานหรือองค์กร ให้เข้ากับสถานการณ์ แต่ยังคงเป้าหมายเดิมได้อย่างมีประสิทธิภาพ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('308', 5, 'แสดงสมรรถนะระดับที่ 4 และปรับเปลี่ยนแผนกลยุทธ์ทั้งหมด เพื่อให้งานมีประสิทธิภาพ', 'ปรับแผนกลยุทธ์ทั้งหมด เพื่อให้เหมาะสมกับสถานการณ์เฉพาะหน้า
มีจิตวิทยาในการใช้ความเข้าใจผู้อื่นในสถานการณ์ต่างๆ เพื่อเป็นพื้นฐานในการเจรจาทำความเข้าใจ หรือดำเนินงานไห้ได้ตามภารกิจของหน่วยงาน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('309', 1, 'เข้าใจเทคโนโลยี ระบบ กระบวนการทำงานและมาตรฐานในงานของตน', 'เข้าใจเทคโนโลยี ระบบ กระบวนการทำงานและมาตรฐานในงานที่ตนสังกัดอยู่ รวมทั้งกฎระเบียบ ตลอดจนขั้นตอนกระบวนการปฏิบัติงานต่างๆ และนำความเข้าใจนี้มาใช้ในการปฏิบัติงาน ติดต่อประสานงาน หรือรายงานผล ฯลฯ ในหน้าที่ได้ถูกต้อง', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('309', 2, 'แสดงสมรรถนะระดับที่ 1 และเข้าใจความสัมพันธ์เชื่อมโยงของระบบและกระบวนการทำงานของตนกับหน่วยงานอื่นๆ ที่ติดต่ออย่างชัดเจน', 'เข้าใจและเชื่อมโยงเทคโนโลยี ระบบ กระบวนการทำงาน ขั้นตอนกระบวนการปฏิบัติงานต่างๆ ของตนกับหน่วยงานอื่นที่ติดต่อด้วยอย่างถูกต้อง รวมถึงนำความเข้าใจนี้มาใช้เพื่อให้การทำงานระหว่างกันเป็นไปอย่างมีประสิทธิภาพและสอดรับกันสูงสุด', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('309', 3, 'แสดงสมรรถนะระดับที่ 2 และสามารถมองภาพรวมแล้วปรับเปลี่ยนหรือปรับปรุงระบบให้มีประสิทธิภาพขึ้น', 'เข้าใจข้อจำกัดของเทคนิค ระบบหรือกระบวนการทำงานของตนหรือหน่วยงานอื่นๆ ที่ติดต่อด้วย และรู้ว่าสิ่งใดที่ควรกระทำเพื่อปรับเปลี่ยนหรือปรับปรุงระบบให้สามารถทำงานได้อย่างมีประสิทธิภาพสูงขึ้นได้ 
เมื่อเจอสถานการณ์ที่แตกต่างจากเดิมสามารถใช้ความเข้าใจผลต่อเนื่องและความสัมพันธ์เชื่อมโยงของระบบและกระบวนการทำงาน เพื่อนำมาแก้ไขปัญหาได้อย่างเหมาะสมทันเวลา', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('309', 4, 'แสดงสมรรถนะระดับที่ 3 และเข้าใจกระแสภายนอกกับผลกระทบโดยรวมต่อเทคโนโลยี ระบบหรือกระบวนการทำงานของหน่วยงาน', 'เข้าใจกระแสหรือสถานการณ์ภายนอก (เช่น นโยบายภาครัฐในภาพรวม การเปลี่ยนแปลงในอุตสาหกรรม) และสามารถนำความเข้าใจนั้นมาเตรียมรับมือหรือดำเนินการการเปลี่ยนแปลงได้อย่างเหมาะสมและเกิดประโยชน์สูงสุด 
ศึกษาเรียนรู้ความสำเร็จหรือความผิดพลาดของระบบหรือกระบวนการการทำงานที่เกี่ยวข้องและนำมาปรับใช้กับการทำงานของหน่วยงานอย่างเหมาะสม', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('309', 5, 'แสดงสมรรถนะระดับที่ 4 และเข้าใจความต้องการที่แท้จริงขององค์กร', 'เข้าใจสถานะของระบบ เทคโนโลยี และกระบวนการการทำงานของกรม อย่างถ่องแท้ จนสามารถกำหนดความต้องการหรือดำเนินการเปลี่ยนแปลงในภาพรวมเพื่อให้องค์กรเติบโตอย่างยั่งยืน 
เข้าใจและสามารถระบุจุดยืนและความสามารถในการพัฒนาในเชิงระบบ เทคโนโลยี กระบวนการทำงานหรือมาตรฐานการทำงานในเชิงบูรณาการระบบ (Holistic Vew) ขององค์กร', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('310', 1, 'นำเสนอข้อมูลอย่างตรงไปตรงมา', 'นำเสนอข้อมูล อธิบาย ชี้แจงรายละเอียดแก่ผู้ฟังอย่างตรงไปตรงมาโดยอิงข้อมูลที่มีอยู่ แต่อาจยังมิได้มีการปรับใจความและวิธีการให้สอดคล้องกับความสนใจและบุคลิกลักษณะของผู้ฟัง', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('310', 2, 'แสดงสมรรถนะระดับที่ 1 และเจรจาโน้มน้าวใจโดยอาศัยหลักการและเหตุผล', 'เตรียมการนำเสนอข้อมูลเป็นอย่างดี และใช้ความพยายามเจรจาโน้มน้าวใจโดยยกหลักการและเหตุผลที่เกี่ยวข้องมาประกอบการนำเสนออย่างมีขั้นตอน
ใช้ความพยายามเจรจาโน้มน้าวใจโดยยกหลักการและเหตุผลที่เกี่ยวข้องมาอธิบายประกอบการนำเสนออย่างมีขั้นตอน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('310', 3, 'แสดงสมรรถนะระดับที่ 2 และเจรจาต่อรองหรือนำเสนอข้อมูลโดยปรับสารให้สอดคล้องกับผู้ฟังเป็นสำคัญ', 'ประยุกต์ใช้ความเข้าใจ ความสนใจของผู้ฟังให้เป็นประโยชน์ในเจรจาเสนอข้อมูล นำเสนอหรือเจรจาโดยคาดการณ์ถึงปฏิกิริยา ผลกระทบที่จะมีต่อผู้ฟังเป็นหลัก
สามารถนำเสนอทางเลือกหรือให้ข้อสรุปในการเจรจาอันเป็นประโยชน์แก่ทั้งสองฝ่าย', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('310', 4, 'แสดงสมรรถนะระดับที่ 3 และใช้กลยุทธ์การสื่อสารจูงใจทางอ้อม', 'ใช้ความเข้าใจบุคคลหรือองค์กรให้เป็นประโยชน์โดยการนำเอาบุคคลที่สามหรือผู้เชี่ยวชาญมาสนับสนุนให้การเจรจาโน้มน้าวจูงใจประสบผลสำเร็จหรือมีน้ำหนักมากยิ่งขึ้น
ใช้ทักษะในการโน้มน้าวใจทางอ้อม เพื่อให้ได้ผลสัมฤทธิ์ดังประสงค์โดยคำนึงถึงผลกระทบและความรู้สึกของผู้อื่นเป็นสำคัญ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('310', 5, 'แสดงสมรรถนะระดับที่ 4 และใช้กลยุทธ์ที่ซับซ้อนในการจูงใจ', 'สร้างกลุ่มแนวร่วมเพื่อสนับสนุนให้การเจรจาโน้มน้าวใจมีน้ำหนักและสัมฤทธิ์ผลได้ดียิ่งขึ้น
ประยุกต์ใช้หลักจิตวิทยามวลชนหรือจิตวิทยากลุ่มให้เป็นประโยชน์ในการเจรจาโน้มน้าวจูงใจได้อย่างมีประสิทธิภาพ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('311', 1, 'สนับสนุนความคิดสร้างสรรค์และยอมทดลองวิธีอื่นๆ เพื่อมาทดแทนวิธีการที่ใช้อยู่เดิมในการปฏิบัติงานอย่างเต็มใจและใคร่รู้', 'เต็มใจที่จะยอมรับและปรับตัวต่อความริเริ่มสร้างสรรค์หรือสิ่งใหม่ เพื่อสนับสนุนให้หน่วยงานบรรลุเป้าหมายที่กำหนด
แสดงความสงสัยใคร่รู้และต้องการทดลองวิธีการใหม่ๆ ที่อาจส่งผลให้ปฏิบัติงานได้ดีขึ้น
เต็มใจที่จะเสาะหาและศึกษาวิธีการที่แปลกใหม่ที่อาจนำมาประยุกต์ใช้ในการปฏิบัติงานได้อย่างเหมาะสม', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('311', 2, 'แสดงสมรรถนะระดับที่ 1 และสร้างสรรค์และหมั่นปรับปรุงกระบวนการทำงานของตนอย่างสม่ำเสมอ', 'หมั่นปรับปรุงกระบวนการทำงานของตนอย่างสม่ำเสมอ  
เปลี่ยนแปลงรูปแบบหรือขั้นตอนการทำงานใหม่ๆ ที่สอดคล้องและสนับสนุนหน่วยงานให้สามารถบรรลุเป้าหมายได้อย่างมีประสิทธิภาพมากขึ้น', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('311', 3, 'แสดงสมรรถนะระดับที่ 2 และคิดนอกกรอบเพื่อปรับเปลี่ยนการดำเนินงานใหม่ในหน่วยงานเพื่อให้งานมีประสิทธิภาพ', 'ประยุกต์ใช้ประสบการณ์ในการทำงานมาปรับเปลี่ยนวิธีการดำเนินงาน ให้เข้ากับสถานการณ์ แต่ยังคงเป้าหมายได้อย่างมีประสิทธิภาพสูงสุด
ไม่จำกัดตนเองอยู่กับแนวคิดดั้งเดิมที่ใช้กัน พร้อมจะทดลองวิธีการใหม่ๆ มาปรับแก้ไขระเบียบขั้นตอนการทำงานที่ล้าสมัย เพื่อเพิ่มประสิทธิภาพของหน่วยงาน
นำเสนอทางเลือก (Option) หรือแนวทางแก้ปัญหา (Solution) ในงานของตนอย่างสร้างสรรค์ก่อนที่จะปรึกษาผู้บังคับบัญชา', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('311', 4, 'แสดงสมรรถนะระดับที่ 3 และสร้างสรรค์สิ่งใหม่ๆ ในองค์กร', 'ประยุกต์ใช้องค์ความรู้ ทฤษฎี หรือแนวคิดที่ได้รับการยอมรับมาเสนอทางเลือก (Option) หรือแนวทางแก้ปัญหา (Solution) ในการพัฒนาองค์กรให้มีประสิทธิภาพสูงขึ้น
ริเริ่มสร้างสรรค์แนวทางใหม่ๆ ในการปฏิบัติงานหรือดำเนินการต่างๆ ให้องค์กรสามารถบรรลุพันธกิจได้อย่างมีประสิทธิภาพหรือมีคุณภาพสูงขึ้น โดยแนวทางใหม่ๆ หรือ Best Practice นี้อาจมีอยู่แล้วในองค์กรอื่นๆ ทั้งภาครัฐหรือเอกชน และทั้งในและต่างประเทศ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('311', 5, 'แสดงสมรรถนะระดับที่ 4 และสร้างนวัตกรรมในระบบอุตสาหกรรมของประเทศโดยรวม', 'คิดนอกกรอบ พิจารณาสิ่งต่างๆ ในงานด้วยมุมมองที่แตกต่าง อันนำไปสู่การวิจัย การประดิษฐ์คิดค้น หรือการสร้างสรรค์ เพื่อนำเสนอต้นแบบ สูตร รูปแบบ วิธี ตลอดจนองค์ความรู้ใหม่ที่ไม่เคยปรากฏมาก่อนและเป็นประโยชน์ต่อระบบอุตสาหกรรมหรือสังคมและประเทศชาติโดยรวม
สนับสนุนให้เกิดบรรยากาศแห่งความคิดสร้างสรรค์หรือสร้างโอกาสใหม่ทางธุรกิจในองค์กร ด้วยการให้การสนับสนุนทางทรัพยากร หรือจัดกิจกรรมต่างๆ ที่จะช่วยกระตุ้นให้เกิดการแสดงออกทางความคิดสร้างสรรค์', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('312', 1, 'การตอบสนองได้อย่างรวดเร็ว และเด็ดเดี่ยวในเหตุวิกฤติ หรือสถานการณ์จำเป็น', 'ตอบสนองอย่างรวดเร็ว และเด็ดเดี่ยวเมื่อมีเหตุวิกฤติหรือในสถานการณ์ที่จำเป็นเพื่อให้ทันต่อความเร่งด่วนของสถานการณ์นั้นๆ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('312', 2, 'แสดงสมรรถนะระดับที่ 1 และตระหนักถึงปัญหาหรือโอกาสและลงมือกระทำการโดยไม่รีรอ', 'ตระหนักถึงปัญหาหรือโอกาสในขณะนั้นและลงมือกระทำการโดยไม่รีรอให้สถานการณ์คลี่คลายไปเอง หรือปล่อยโอกาสหลุดลอยไป อีกทั้งรู้จักพลิกแพลงวิธีการ กระบวนการต่างๆ เพื่อให้สามารถแก้ไขปัญหา หรือใช้ประโยชน์จากโอกาสนั้นได้อย่างมีประสิทธิภาพ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('312', 3, 'แสดงสมรรถนะระดับที่ 2 และเล็งเห็นโอกาสหรือปัญหาที่อาจเกิดขึ้นได้ในระยะใกล้ (ประมาณ 1-3 เดือนข้างหน้า)', 'คาดการณ์และเล็งเห็นปัญหาหรือโอกาสที่อาจเกิดขึ้นในระยะเวลา 1-3 เดือนถัดจากปัจจุบัน และลงมือกระทำการล่วงหน้าเพื่อป้องกันปัญหา หรือสร้างโอกาสในสถานการณ์นั้นๆ อีกทั้งเปิดกว้างรับฟังแนวทางและความคิดหลากหลายอันอาจเป็นประโยชน์ต่อการป้องกันปัญหา', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('312', 4, 'แสดงสมรรถนะระดับที่ 3 และเล็งเห็นโอกาสหรือปัญหาที่อาจเกิดขึ้นได้ในระยะกลาง (ประมาณ 4-12 เดือนข้างหน้า)', 'คาดการณ์และเล็งเห็นปัญหาหรือโอกาสที่อาจเกิดขึ้นในระยะเวลา 4-12 เดือนถัดจากปัจจุบัน และเตรียมการล่วงหน้าเพื่อป้องกันปัญหา หรือสร้างโอกาสในสถานการณ์นั้นๆ ตลอดจนทดลองและเสาะหาวิธีการ แนวคิดใหม่ๆ ที่อาจเป็นประโยชน์ในการป้องกันปัญหาและสร้างโอกาสในอนาคต', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('312', 5, 'แสดงสมรรถนะระดับที่ 4 และเตรียมการล่วงหน้าเพื่อป้องกันปัญหาและสร้างโอกาสในระยะยาว', 'คาดการณ์และเล็งเห็นปัญหาหรือโอกาสที่อาจเกิดขึ้นในระยะยาวและเตรียมการล่วงหน้าเพื่อป้องกันปัญหา หรือสร้างโอกาส อีกทั้งกระตุ้นให้ผู้อื่นเกิดความกระตือรือร้นต่อการป้องกันและแก้ไขปัญหาเพื่อสร้างโอกาสให้องค์กรในระยะยาว', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('313', 1, 'หาข้อมูลในระดับต้นและแสดงผลข้อมูลได้', 'สามารถหาข้อมูลโดยการถามจากผู้ที่เกี่ยวข้องโดยตรง การใช้ข้อมูลที่มีอยู่ หรือหาจากแหล่งข้อมูลที่มีอยู่แล้วและสรุปผลข้อมูลเพื่อแสดงผลข้อมูลในรูปแบบต่างๆ เช่น กราฟ รายงาน ได้อย่างถูกต้อง ครบถ้วน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('313', 2, 'แสดงสมรรถนะระดับที่ 1 และใช้วิธีการสืบเสาะหาข้อมูลเพื่อจับประเด็นหรือแก่นความของข้อมูลหรือปัญหาได้', 'สามารถสืบเสาะปัญหาหรือสถานการณ์อย่างลึกซึ้งกว่าการตั้งคำถามตามปรกติธรรมดา หรือสืบเสาะจากผู้เชี่ยวชาญ เพื่อให้ได้มาซึ่งแก่นหรือประเด็นของเนื้อหา และนำแก่นหรือประเด็นเหล่านั้นมาจัดการวิเคราะห์ ประเมินผลให้เกิดข้อมูลที่ลึกซึ้งมากที่สุด', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('313', 3, 'แสดงสมรรถนะระดับที่ 2 และหาข้อมูลในเบื้องลึก (Insights)', 'ค้นหาหรือสอบถามเจาะลึกอย่างต่อเนื่อง (เช่น จากหนังสือ หนังสือพิมพ์ นิตยสาร ระบบสืบค้นโดยอาศัยเทคโนโลยีสารสนเทศ ตลอดจนแหล่งข่าวต่างๆ) เพื่อให้เข้าใจถึงมุมมองทัศนะความคิดเห็นที่แตกต่าง ต้นตอของสถานการณ์ ปัญหา หรือโอกาสที่ซ่อนเร้นอยู่ในเบื้องลึก และนำความเข้าใจเหล่านั้นมาประเมินผล และตีความเป็นข้อมูลได้อย่างถูกต้องและเกิดประโยชน์ต่อการปฏิบัติงานสูงสุด', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('313', 4, 'แสดงสมรรถนะระดับที่ 3 และสืบค้นข้อมูลอย่างเป็นระบบให้เชื่อมต่อข้อมูลที่ขาดหายไปหรือคาดการณ์ได้อย่างมีนัยสำคัญ', 'จัดทำการวิจัยโดยอ้างอิงจากข้อมูลที่มีอยู่หรือสืบค้นจากแหล่งข้อมูลที่แปลกใหม่แตกต่างจากปรกติธรรมดาทั่วไปอย่างเป็นระบบหรือเป็นไปตามหลักการทางสถิติ และนำผลที่ได้นั้นมาเชื่อมต่อข้อมูลที่ขาดหายไป หรือพยากรณ์หรือสร้างแบบจำลอง (model) หรือสร้างระบบ (system formula) ได้อย่างถูกต้องและเป็นระบบ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('313', 5, 'แสดงสมรรถนะระดับที่ 4 และวางระบบการสืบค้น เพื่อหาข้อมูลอย่างต่อเนื่อง', 'วางระบบการสืบค้น เพื่อให้มีข้อมูลที่ทันเหตุการณ์ป้อนเข้ามาอย่างต่อเนื่องและสามารถออกแบบ เลือกใช้ หรือประยุกต์วิธีการในการจัดทำแบบจำลองหรือระบบต่างๆ ได้อย่างถูกต้องและมีนัยสำคัญ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('314', 1, 'วางแผนงานบนพื้นฐานของความเข้าใจเทคโนโลยี ระบบ กระบวนการทำงานและมาตรฐานในงานของตน', 'เข้าใจเทคโนโลยี ระบบ กระบวนการทำงานและมาตรฐานในงานที่ตนสังกัดอยู่ รวมทั้งกฎระเบียบ ตลอดจนขั้นตอนกระบวนการปฏิบัติงานต่างๆ และนำความเข้าใจนี้มาใช้ในการวางแผนงานให้เป็นขั้นตอนและเกิดผลลัพธ์อย่างชัดเจนและถูกต้อง', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('314', 2, 'แสดงสมรรถนะระดับที่ 1 และวางแผนงานตามความสำคัญของงานบนพื้นฐานของความเข้าใจความสัมพันธ์เชื่อมโยงของระบบและกระบวนการทำงานของตนกับหน่วยงานอื่นๆ ที่ติดต่ออย่างชัดเจน', 'เข้าใจและเชื่อมโยงเทคโนโลยี ระบบ กระบวนการทำงาน ขั้นตอนกระบวนการปฏิบัติงานต่างๆ ของตนกับหน่วยงานอื่นที่ติดต่อด้วยอย่างถูกต้อง และนำมาวางแผนจัดลำดับของงานและผลลัพธ์เพื่อให้สามารถบรรลุตามเป้าหมายและเวลาที่วางไว้ได้อย่างมีประสิทธิภาพสูงสุด
วิเคราะห์หาข้อดี ข้อเสียและผลต่อเนื่องของแผนงานที่วางไว้โดยการวิเคราะห์ความเชื่อมโยงของกระบวนงานต่างๆ ของตนและหน่วยงานที่เกี่ยวข้อง', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('314', 3, 'แสดงสมรรถนะระดับที่ 2 และสามารถมองภาพรวมแล้ววางแผนหรือเชื่อมโยงงานหรือกิจกรรมต่างๆ ที่มีความซับซ้อนเพื่อให้บรรลุตามแผนที่กำหนดไว้ได้ หรือเพื่อปรับปรุงระบบให้มีประสิทธิภาพขึ้นได้', 'เข้าใจข้อจำกัดของเทคนิค ระบบหรือกระบวนการทำงานของตนหรือหน่วยงานอื่นๆ ที่ติดต่อด้วย และวางแผนงานที่มีความเชื่อมโยงหรือซับซ้อนกันหลายๆ งานหรือหลายๆ โครงการเพื่อให้งานบรรลุตามเป้าหมาย หรือเพื่อให้เกิดการปรับเปลี่ยนหรือปรับปรุงระบบให้สามารถทำงานได้อย่างมีประสิทธิภาพสูงขึ้นได้', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('314', 4, 'แสดงสมรรถนะระดับที่ 3 และเข้าใจกระแสภายนอกกับผลกระทบโดยรวมต่อเทคโนโลยี ระบบหรือกระบวนการทำงานของหน่วยงานและคาดการณ์ล่วงหน้าเพื่อเตรียมทางเลือกสำหรับการป้องกัน/แก้ไขปัญหาที่เกิดขึ้น', 'เข้าใจกระแสหรือสถานการณ์ภายนอก (เช่น นโยบายภาครัฐในภาพรวม การเปลี่ยนแปลงในอุตสาหกรรม) และสามารถนำความเข้าใจนั้นมาเตรียมรับมือหรือดำเนินการการเปลี่ยนแปลงได้อย่างเหมาะสมและเกิดประโยชน์สูงสุด 
ศึกษาเรียนรู้ความสำเร็จหรือความผิดพลาดของระบบหรือกระบวนการการทำงานที่เกี่ยวข้องเพื่อเตรียมแผนรับมือกับสิ่งไม่คาดการณ์ไว้ได้อย่างรัดกุมและมีประสิทธิภาพ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('314', 5, 'แสดงสมรรถนะระดับที่ 4 และเข้าใจความต้องการที่แท้จริงขององค์กร และกระทรวงฯ และปรับกลยุทธ์ในแผนให้เข้ากับสถานการณ์เฉพาะหน้านั้นอย่างเป็นระบบ', 'เข้าใจสถานะของระบบ เทคโนโลยี และกระบวนการการทำงานขององค์กร และกระทรวงฯ อย่างถ่องแท้ จนสามารถปรับกลยุทธ์และวางแผนอย่างรัดกุม เพื่อดำเนินการเปลี่ยนแปลงในภาพรวมให้องค์กร และกระทรวงฯ เติบโตอย่างยั่งยืน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('315', 1, 'ติดตามหาความรู้และแนวคิดใหม่ๆ ในสายวิชาชีพ เพื่อใช้ในการวิเคราะห์และแก้ไขปัญหาระยะสั้นที่เกิดขึ้น', 'กระตือรือร้นในการศึกษาหาความรู้หรือเทคโนโลยีใหม่ๆ ในสาขาอาชีพของตนหรือในงานของสำนัก เพื่อนำมาใช้ให้เกิดประโยชน์ในการแก้ไขปัญหา
ใช้ความรู้ในสายอาชีพของตนในการลงมือแก้ไข เมื่อเล็งเห็นปัญหาหรืออุปสรรคโดยไม่รอช้า', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('315', 2, 'แสดงสมรรถนะระดับที่ 1 และวิเคราะห์และตัดสินใจอย่างมีข้อมูลและเหตุผลในการจัดการปัญหาที่เกิดขึ้น', 'วิเคราะห์ข้อมูล และหาเหตุผลตามแนวคิด และหลักการในวิชาชีพ เพื่อตัดสินใจดำเนินการแก้ไขปัญหาที่เกิดขึ้นอย่างมีประสิทธิภาพสูงสุด
พลิกแพลงหรือประยุกต์แนวทางในการแก้ปัญหา โดยอ้างอิงจากข้อมูล หลักการ และแนวคิดในสายวิชาชีพ หรือประสบการณ์ในการทำงาน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('315', 3, 'แสดงสมรรถนะระดับที่ 2 และวิเคราะห์ปัญหาที่ผ่านมา และวางแผนล่วงหน้าอย่างเป็นระบบ เพื่อป้องกันหรือหลีกเลี่ยงปัญหา', 'วิเคราะห์ข้อมูล ปัญหา หรือสถานการณ์ได้อย่างรอบด้าน (โดยอาศัยประสบการณ์ และความเชี่ยวชาญที่สั่งสมมาในสายอาชีพ) รวมทั้งวางแผน และคาดการณ์ผลกระทบที่จะเกิดขึ้นอย่างเป็นระบบ เพื่อป้องกันและหลีกเลี่ยงปัญหาที่อาจเกิดขึ้น 
วางแผน และทดลองใช้วิธีการ องค์ความรู้ หรือเทคโนโลยีใหม่ๆ ในสายอาชีพ ในการป้องกัน หลีกเลี่ยงหรือแก้ไขปัญหาให้เกิดขึ้นในหน่วยงานหรือองค์กร', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('315', 4, 'แสดงสมรรถนะระดับที่ 3 และผสมผสานแนวคิดในเชิงสหวิทยาการเพื่อหลีกเลี่ยง ป้องกันหรือแก้ไขปัญหาทั้งในระยะสั้นและระยะยาว', 'วิเคราะห์ และผสมผสานศาสตร์หลายๆ แขนง (โดยอาศัยประสบการณ์ ความเชี่ยวชาญที่ทั้งกว้างและลึก รวมทั้งความสามารถพิเศษ (Charisma)) เพื่อแก้ไขปัญหาซึ่งมีความซับซ้อนในระยะสั้นและเตรียมการป้องกันหรือหลีกเลี่ยงปัญหาในระยะยาวได้
คิดนอกกรอบ ริเริ่มโครงการ หรือกระบวนการทำงานต่างๆ ในลักษณะบูรณาการหลายหน่วยงาน/หลายวิชาชีพ เพื่อแก้ไขปัญหาที่คาดว่าจะเกิดขึ้นในอนาคต', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('315', 5, 'แสดงสมรรถนะระดับที่ 4 และปรับเปลี่ยนหรือสร้างความเชี่ยวชาญในสายอาชีพ/สหวิทยาการ เพื่อแก้ไขและหลีกเลี่ยงปัญหาอย่างยั่งยืน', 'ปรับเปลี่ยน (Reshape) องค์กรให้มีการบูรณาการในเชิงวิชาชีพ หรือให้มีความเชี่ยวชาญในสายอาชีพอย่างแท้จริง เพื่อให้สามารถแก้ไข ป้องกันและหลีกเลี่ยงปัญหาที่มีผลกระทบสูงหรือมีความซับซ้อนสูงขององค์กรได้อย่างยั่งยืน
เป็นผู้นำที่ได้รับการยอมรับว่าเป็นผู้เชี่ยวชาญในสายอาชีพที่สามารถป้องกัน และหลีกเลี่ยงปัญหาที่มีผลกระทบเชิงนโยบาย และกลยุทธ์ขององค์กรได้อย่างมีประสิทธิภาพสูงสุด หรือสามารถแก้ไขปัญหาที่มีผลกระทบ แปรวิกฤติให้เป็นโอกาส และเกิดประโยชน์อย่างยั่งยืนแก่องค์กรในระยะยาว', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(1, '101', 1, 'ผู้รับการประเมินมีความมานะอดทนและขยันหมั่นเพียรในการทำงานในหน้าที่ให้ดี ถูกต้อง และแล้วเสร็จตามเวลาที่กำหนดไว้', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(2, '101', 2, 'ผู้รับการประเมินทำงานได้ตามผลงานตามเป้าหมายของหน่วยงานที่รับผิดชอบ', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(3, '101', 3, 'ผู้รับการประเมินปรับปรุงวิธีการหรือกระบวนการที่ทำให้ทำงานได้ดีขึ้น เร็วขึ้น มีคุณภาพดีขึ้น หรือมีประสิทธิภาพมากขึ้น', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(4, '101', 4, 'ผู้รับการประเมินพัฒนาระบบ ขั้นตอน วิธีการทำงานในหน่วยงาน สมอ. หรือในกระทรวงอุตสาหกรรม เพื่อให้ได้ผลงานที่โดดเด่นหรือแตกต่างอย่างไม่เคยมีใครทำได้มาก่อน', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(5, '101', 5, 'ผู้รับการประเมินกล้าตัดสินใจ แม้ว่าการตัดสินใจนั้นจะมีความเสี่ยง เพื่อให้บรรลุเป้าหมายขององค์กรโดยมีการคำนวณผลได้ผลเสียอย่างชัดเจน เพื่อให้ภาครัฐและประชาชนได้ประโยชน์สูงสุด', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(6, '102', 1, 'ผู้รับการประเมินให้บริการหรือให้ข้อมูลข่าวสารที่ถูกต้อง สุภาพ และเป็นมิตรแก่ผู้รับบริการ', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(7, '102', 2, 'ผู้รับการประเมินรับเป็นธุระ ช่วยแก้ปัญหา หรือหาแนวทางแก้ไขปัญหาที่เกิดขึ้นแก่ผู้รับบริการอย่างรวดเร็ว เต็มใจ ไม่บ่ายเบี่ยง จนผู้รับบริการได้รับความพึงพอใจสูงสุด', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(8, '102', 3, 'ผู้รับการประเมินเน้นและหมั่นให้บริการที่เกินความคาดหวังของผู้รับบริการ แม้ต้องใช้เวลาหรือความพยายามอย่างมากเป็นพิเศษ', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(9, '102', 4, 'ผู้รับการประเมินเข้าใจความจำเป็นหรือความต้องการที่แท้จริงของผู้รับบริการ จนสามารถให้คำแนะนำหรือบริการที่เป็นประโยชน์ในอนาคตแก่ผู้รับบริการ แม้จะไม่ได้ร้องขอ', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(10, '102', 5, 'ผู้รับการประเมินให้บริการที่เป็นประโยชน์อย่างแท้จริงและในระยะยาวแก่ผู้รับบริการ', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(11, '103', 1, 'ผู้รับการประเมินกระตือรือร้นในการศึกษาหาความรู้ เทคโนโลยีและองค์ความรู้ใหม่ๆ ในสาขาอาชีพของตน', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(12, '103', 2, 'ผู้รับการประเมินรอบรู้เท่าทันเทคโนโลยีหรือองค์ความรู้ใหม่ๆ ในสาขาอาชีพของตนและสามารถตอบคำถามที่เกี่ยวข้องได้อย่างถูกต้อง', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(13, '103', 3, 'ผู้รับการประเมินสั่งสมความรู้และสามารถนำวิชาการ ความรู้ หรือเทคโนโลยีใหม่ๆ มาต่อยอดหรือประยุกต์ใช้ในการปฏิบัติงานในหน่วยงานของตนได้', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(14, '103', 4, 'ผู้รับการประเมินพัฒนาตนเองจนได้รับการยอมรับว่ามีความรู้ และความเชี่ยวชาญในงานสายอาชีพทั้งในเชิงลึก และเชิงกว้างอย่างต่อเนื่อง', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(15, '103', 5, 'ผู้รับการประเมินสนับสนุนให้เกิดบรรยากาศแห่งการพัฒนาความเชี่ยวชาญในวิทยาการต่างๆ ภายในองค์กร รวมทั้งบริหารจัดการให้องค์กรนำเทคโนโลยี ความรู้ หรือวิทยาการใหม่ๆ มาใช้ในการปฏิบัติหน้าที่ราชการในงานอย่างต่อเนื่อง', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(16, '104', 1, 'ผู้รับการประเมินปฏิบัติหน้าที่ด้วยความซื่อสัตย์สุจริต ไม่เลือกปฏิบัติ ถูกต้องทั้งตามหลักกฎหมาย จริยธรรม และระเบียบวินัย', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(17, '104', 2, 'ผู้รับการประเมินรักษาวาจา มีสัจจะเชื่อถือได้ พูดอย่างไรทำอย่างนั้น ไม่บิดเบือนอ้างข้อยกเว้นให้ตนเองหรือคนอื่นๆ ที่รู้จัก', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(18, '104', 3, 'ผู้รับการประเมินยึดมั่นในหลักการ จรรยาบรรณแห่งวิชาชีพ และจรรยาข้าราชการ โดยไม่เบี่ยงเบนด้วยอคติ หรือผลประโยชน์ส่วนตน', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(19, '104', 4, 'ผู้รับการประเมินยืนหยัดเพื่อความถูกต้อง และกล้าตัดสินใจ โดยมุ่งพิทักษ์ผลประโยชน์ของทางราชการ แม้อยู่ในสถานการณ์ที่อาจสร้างความลำบากใจให้ หรือแม้ผลลัพธ์อาจสร้างศัตรูหรือก่อความไม่พึงพอใจให้แก่ผู้ที่เกี่ยวข้องหรือเสียประโยชน์', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(20, '104', 5, 'ผู้รับการประเมินอุทิศตนเพื่อความยุติธรรม และยืนหยัดพิทักษ์ผลประโยชน์และชื่อเสียงของประเทศชาติเป็นหลัก แม้ในสถานการณ์ที่อาจเสี่ยงต่อความมั่นคงในตำแหน่งหน้าที่การงาน หรืออาจเสี่ยงภัยต่อชีวิต', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(21, '105', 1, 'ผู้รับการประเมินทำหน้าที่ของตนเองในฐานะส่วนหนึ่งของทีมงานให้สำเร็จ และสนับสนุนข้อมูลที่เป็นประโยชน์ในการทำงานแก่ทีมงานหรือเพื่อนร่วมงาน', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(22, '105', 2, 'ผู้รับการประเมินเอื้อเฟื้อเผื่อแผ่ และให้ความร่วมมือ และความช่วยเหลือในการทำงานของเพื่อนร่วมงานได้เป็นอย่างดี', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(23, '105', 3, 'ผู้รับการประเมินประสานและส่งเสริมสัมพันธภาพอันดีในทีม เพื่อสนับสนุนการทำงานร่วมกันให้มีประสิทธิภาพยิ่งขึ้น รวมถึงพร้อมรับฟังและหมั่นประมวลความเห็นของสมาชิกในทีมเพื่อประกอบการตัดสินใจหรือวางแผนร่วมกันในหน่วยงาน', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(24, '105', 4, 'ผู้รับการประเมินรักษามิตรภาพ กล่าวชื่นชมให้กำลังใจเพื่อนร่วมงาน และแสดงน้ำใจในเหตุวิกฤติ เพื่อช่วยเหลือให้งานในภาพรวมของหน่วยงานให้ประสบความสำเร็จ โดยไม่ต้องรอให้ร้องขอ', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(25, '105', 5, 'ผู้รับการประเมินนำทีมให้ปฏิบัติภารกิจให้ได้ผลสำเร็จ โดยเสริมสร้างความสามัคคีในทีม ประสานสัมพันธ์ และสร้างขวัญกำลังใจภายในทีม', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(26, '201', 1, 'ผู้รับการประเมินดำเนินการประชุมให้เป็นไปตามระเบียบ วาระ วัตถุประสงค์ และเวลา ตลอดจนคอยแจ้งข่าวสารความเป็นไปโดยตลอดให้ผู้ที่เกี่ยวข้องทราบอย่างสม่ำเสมอ', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(27, '201', 2, 'ผู้รับการประเมินเป็นผู้นำที่ส่งเสริมการทำงานของกลุ่มให้ทำงานได้อย่างเต็มประสิทธิภาพและใช้อำนาจอย่างยุติธรรม', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(28, '201', 3, 'ผู้รับการประเมินเป็นที่ปรึกษา ให้การดูแล ช่วยเหลือทีมงานอย่างเต็มความสามารถ และปกป้องชื่อเสียงของทีมงานและหน่วยงาน', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(29, '201', 4, 'ผู้รับการประเมินประพฤติตนสมกับเป็นผู้นำที่ดี และยึดหลักธรรมาภิบาล  (Good Governance) (นิติธรรม คุณธรรม โปร่งใส ความมีส่วนร่วม ความรับผิดชอบ ความคุ้มค่า) ในการปกครองผู้ใต้บังคับบัญชา', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(30, '201', 5, 'ผู้รับการประเมินสามารถรวมใจคน สร้างแรงบันดาลใจ และนำพาทีมงานให้ก้าวไปสู่พันธกิจระยะยาวขององค์กร', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(31, '202', 1, 'ผู้รับการประเมินรู้และเข้าใจวิสัยทัศน์ขององค์กร รวมทั้งสามารถอธิบายให้ผู้อื่นเข้าใจได้ว่างานที่ทำอยู่นั้นเกี่ยวข้องหรือตอบสนองต่อวิสัยทัศน์ขององค์กรอย่างไร', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(32, '202', 2, 'ผู้รับการประเมินอธิบายให้ผู้อื่นรู้และเข้าใจวิสัยทัศน์ขององค์กร รวมทั้งแลกเปลี่ยนข้อมูล และรับฟังความคิดเห็นของผู้อื่นเพื่อประกอบการกำหนดวิสัยทัศน์', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(33, '202', 3, 'ผู้รับการประเมินสร้างแรงจูงใจให้ผู้อื่นเกิดความเต็มใจที่จะปฏิบัติหน้าที่ตามวิสัยทัศน์ รวมทั้งให้คำปรึกษาแนะนำแก่สมาชิกในทีมถึงแนวทางในการทำงานโดยยึดถือวิสัยทัศน์และเป้าหมายขององค์กรเป็นสำคัญ', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(34, '202', 4, 'ผู้รับการประเมินกำหนดนโยบายให้สอดคล้องกับวิสัยทัศน์ขององค์กร เพื่อตอบสนองต่อการนำวิสัยทัศน์ไปสู่ความสำเร็จ', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(35, '202', 5, 'ผู้รับการประเมินกำหนดวิสัยทัศน์ เป้าหมาย และทิศทางในการปฏิบัติหน้าที่ขององค์กรให้บรรลุวิสัยทัศน์ระดับประเทศ', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(36, '203', 1, 'ผู้รับการประเมินรู้ และเข้าใจนโยบาย รวมทั้งภารกิจภาครัฐ ว่ามีความเกี่ยวโยงกับหน้าที่ความรับผิดชอบของหน่วยงานอย่างไร และสามารถวิเคราะห์ปัญหา อุปสรรคหรือโอกาสของหน่วยงานได้', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(37, '203', 2, 'ผู้รับการประเมินนำประสบการณ์มาประยุกต์ใช้ในการกำหนดกลยุทธ์ของหน่วยงานที่ตนดูแลรับผิดชอบให้สอดคล้องกับกลยุทธ์ภาครัฐได้', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(38, '203', 3, 'ผู้รับการประเมินประยุกต์ใช้ทฤษฎี แนวทางปฏิบัติที่ประสบความสำเร็จ (Best Practice) ผลการวิจัยต่างๆ หรือแนวคิดซับซ้อนมาใช้ในการกำหนดกลยุทธ์ของหน่วยงานที่ตนดูแลรับผิดชอบได้', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(39, '203', 4, 'ผู้รับการประเมินกำหนดกลยุทธ์ที่สอดคล้องกับสถานการณ์ต่างๆ ที่เกิดขึ้น โดยประเมินและสังเคราะห์สถานการณ์ ประเด็น หรือปัญหาทางเศรษฐกิจ สังคม การเมืองภายในประเทศหรือของโลกโดยมองภาพในลักษณะองค์รวม เพื่อใช้ในการกำหนดกลยุทธ์ภาครัฐหรือองค์กรให้บรรลุพันธกิจขององค์กรได้', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(40, '203', 5, 'ผู้รับการประเมินริเริ่ม สร้างสรรค์ และบูรณาการองค์ความรู้ใหม่ๆ ในการกำหนดกลยุทธ์ภาครัฐ โดยพิจารณาจากบริบทในภาพรวม ตลอดจนปรับเปลี่ยนทิศทาง และกลยุทธ์องค์กร เพื่อพัฒนาประเทศอย่างต่อเนื่อง', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(41, '204', 1, 'ผู้รับการประเมินเห็นความจำเป็น เข้าใจ และยอมรับถึงการปรับเปลี่ยน รวมทั้งปรับพฤติกรรมหรือแผนการทำงานให้สอดคล้องกับการปรับเปลี่ยนนั้นๆ', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(42, '204', 2, 'ผู้รับการประเมินช่วยเหลือให้ผู้อื่นเข้าใจถึงความจำเป็นและประโยชน์ของการปรับเปลี่ยนที่จะเกิดขึ้น พร้อมทั้งเสนอแนะวิธีการและมีส่วนร่วมในการปรับเปลี่ยนหรือเปลี่ยนแปลงดังกล่าว', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(43, '204', 3, 'ผู้รับการประเมินกระตุ้น และสร้างแรงจูงใจให้ผู้อื่นเห็นความสำคัญของการปรับเปลี่ยน เพื่อให้เกิดความร่วมแรงร่วมใจ รวมทั้งเน้นการสร้างความเข้าใจให้เกิดขึ้นแก่ผู้ที่ยังไม่ยอมรับการเปลี่ยนแปลงนั้น', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(44, '204', 4, 'ผู้รับการประเมินวางแผนงานอย่างเป็นระบบ เพื่อรองรับการปรับเปลี่ยนในองค์กร และติดตามการบริหารการเปลี่ยนแปลงอย่างสม่ำเสมอ', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(45, '204', 5, 'ผู้รับการประเมินผลักดันให้เกิดการปรับเปลี่ยนอย่างมีประสิทธิภาพและประสบความสำเร็จ รวมทั้งสร้างขวัญกำลังใจ และความเชื่อมั่นในการขับเคลื่อนให้เกิดการปรับเปลี่ยนอย่างมีประสิทธิภาพ', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(46, '205', 1, 'ผู้รับการประเมินไม่แสดงพฤติกรรมที่ไม่สุภาพหรือไม่เหมาะสมในทุกสถานการณ์', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(47, '205', 2, 'ผู้รับการประเมินรู้เท่าทันอารมณ์ของตนเอง และควบคุมอารมณ์ในแต่ละสถานการณ์ได้อย่างเหมาะสม โดยอาจหลีกเลี่ยงจากสถานการณ์ที่เสี่ยงต่อการเกิดความรุนแรงขึ้น หรืออาจเปลี่ยนหัวข้อสนทนา หรือหยุดพักชั่วคราวเพื่อสงบสติอารมณ์', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(48, '205', 3, 'ผู้รับการประเมินรู้สึกได้ถึงความรุนแรงทางอารมณ์ในระหว่างการสนทนา หรือการปฏิบัติงาน และสามารถใช้ถ้อยทีวาจา วิธีการแสดงออกที่เหมาะสม หรือปฏิบัติงานต่อไปได้อย่างสงบ เพื่อไม่ให้เกิดผลในเชิงลบทั้งต่อตนเองและผู้อื่น', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(49, '205', 4, 'ผู้รับการประเมินบริหารจัดการอารมณ์ ความเครียด หรือผลที่อาจเกิดขึ้นจากภาวะกดดันทางอารมณ์ได้อย่างมีประสิทธิภาพ', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(50, '205', 5, 'ผู้รับการประเมินเอาชนะอารมณ์รุนแรงด้วยความเข้าใจและแก้ไขที่ต้นเหตุของปัญหา รวมทั้งบริบทและปัจจัยแวดล้อมต่างๆ ตลอดจนสามารถควบคุมอารมณ์ของตนเอง รวมถึงทำให้คนอื่นๆ มีอารมณ์ที่สงบลงได้ แม้ในสถานการณ์ที่ตึงเครียดมาก', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(51, '206', 1, 'ผู้รับการประเมินสอนงาน ให้คำแนะนำอย่างละเอียด หรือสาธิตเกี่ยวกับวิธีปฏิบัติงาน เพื่อใช้ในการพัฒนาการปฏิบัติงาน', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(52, '206', 2, 'ผู้รับการประเมินตั้งใจพัฒนาผู้ใต้บังคับบัญชาให้มีศักยภาพ โดยให้คำปรึกษาชี้แนะแนวทางในการพัฒนาหรือส่งเสริมข้อดีและปรับปรุงข้อด้อยให้ลดลง เพื่อสร้างความมั่นใจในการปฏิบัติงาน', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(53, '206', 3, 'ผู้รับการประเมินวางแผนเพื่อให้โอกาสผู้ใต้บังคับบัญชาแสดงความสามารถในการทำงาน รวมถึงให้โอกาสผู้ใต้บังคับบัญชาได้รับการฝึกอบรม หรือพัฒนาอย่างสม่ำเสมอ เพื่อสนับสนุนการเรียนรู้', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(54, '206', 4, 'ผู้รับการประเมินปรับเปลี่ยนทัศนคติ และช่วยแก้ไขปัญหาที่เป็นอุปสรรคต่อการพัฒนาศักยภาพของผู้ใต้บังคับบัญชาได้', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(55, '206', 5, 'ผู้รับการประเมินสร้างและสนับสนุนให้องค์กรมีระบบการสอนงานและการมอบหมายหน้าที่ความรับผิดชอบอย่างเป็นระบบ รวมทั้งให้มีวัฒนธรรมแห่งการเรียนรู้อย่างต่อเนื่องในองค์กร', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(56, '301', 1, 'ผู้รับการประเมินตระหนัก เห็นความสำคัญ ความจำเป็น หรือประโยชน์ของการกำกับติดตามการดำเนินงานต่างๆ ของผู้อื่น เพื่อให้ผู้อื่นปฏิบัติตามมาตรฐาน กฎระเบียบ หรือข้อบังคับที่กำหนดไว้', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(57, '301', 2, 'ผู้รับการประเมินกระตือรือร้นในการกำกับติดตามการดำเนินงานต่างๆ ของผู้อื่น เพื่อให้ผู้อื่นปฏิบัติตามมาตรฐาน กฎระเบียบ หรือข้อบังคับที่กำหนดไว้', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(58, '301', 3, 'ผู้รับการประเมินกำกับติดตามการดำเนินการต่างๆ ของผู้อื่นอย่างสม่ำเสมอ และเป็นระยะๆ รวมทั้งสามารถปรับกระบวนการ หรือวิธีการต่างๆ เพื่อให้ผู้อื่นปฏิบัติตามมาตรฐาน กฎระเบียบ หรือข้อบังคับที่กำหนดไว้อย่างถูกต้อง', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(59, '301', 4, 'ผู้รับการประเมินกำกับติดตาม และตรวจสอบความถูกต้องการดำเนินงานต่างๆ ของผู้อื่นในทุกขั้นตอนอย่างละเอียด และใกล้ชิด รวมทั้งออกคำเตือน และสั่งการให้ปรับปรุงการดำเนินงานต่างๆ ในเชิงปริมาณและคุณภาพให้ถูกต้องมาตรฐาน กฎระเบียบ หรือข้อบังคับที่กำหนดไว้อย่างถูกต้อง', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(60, '301', 5, 'ผู้รับการประเมินดำเนินการ และจัดการอย่างตรงไปตรงมา หรือใช้วิธีเผชิญหน้าอย่างเด็ดขาดเมื่อผู้อื่นหรือหน่วยงานภายใต้การกำกับดูแลมีการดำเนินงานต่างๆ ที่ไม่ดี ไม่ถูกต้อง หรือทำผิดกฎหมายอย่างร้ายแรง รวมทั้งกำหนด และปรับมาตรฐาน ข้อบังคับ และกฎระเบียบต่างๆ ที่เกี่ยวข้องให้แตกต่าง ท้าทาย หรือสูงขึ้น เพื่อส่งเสริมให้บุคลากรเกิดการพัฒนาด้านการกำกับติดตาม', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(61, '302', 1, 'ผู้รับการประเมินปฏิบัติหน้าที่ด้วยความโปร่งใส  ถูกต้องทั้งตามมาตรฐาน หลักกฎหมายและระเบียบข้อบังคับที่กำหนดไว้', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(62, '302', 2, 'ผู้รับการประเมินกล้าที่จะปฏิเสธข้อเรียกร้อง/ข้อเสนอที่ผิดกฎระเบียบ รวมทั้งมุ่งมั่นปฏิบัติหน้าที่โดยไม่บิดเบือนและอ้างข้อยกเว้นให้ตนเองหรือผู้อื่นที่รู้จัก', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(63, '302', 3, 'ผู้รับการประเมินหมั่นกำกับควบคุมตรวจตราการดำเนินการของบุคคลหรือหน่วยงานที่ดูแลรับผิดชอบให้เป็นไปอย่างถูกต้องตามกฎระเบียบหรือแนวทางนโยบายที่วางไว้', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(64, '302', 4, 'ผู้รับการประเมินกล้าเผชิญหน้าและต่อรองให้บุคคลหรือหน่วยงานที่ฝ่าฝืนกฎเกณฑ์ ระเบียบ นโยบายหรือมาตรฐานที่ตั้งไว้ไปปรับปรุงแก้ไข แม้ว่าอาจจะก่อให้เกิดศัตรูหรือความไม่พึงพอใจให้แก่ผู้ที่เกี่ยวข้อง', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(65, '302', 5, 'ผู้รับการประเมินยืนหยัดพิทักษ์ผลประโยชน์ตามกฎเกณฑ์และกฎระเบียบขององค์กร แม้ในสถานการณ์ที่อาจเสี่ยงต่อความมั่นคงในตำแหน่งหน้าที่การงาน หรืออาจเสี่ยงภัยต่อชีวิต', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(66, '303', 1, 'ผู้รับการประเมินเข้าใจนโยบาย กลยุทธ์ของกระทรวงอุตสาหกรรม และ สมอ. และสามารถนำความเข้าใจนั้นมาวิเคราะห์ปัญหา อุปสรรคหรือโอกาสขององค์กรหรือหน่วยงานออกเป็นประเด็นย่อยๆ ได้', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(67, '303', 2, 'ผู้รับการประเมินประยุกต์ความเข้าใจ รูปแบบหรือประสบการณ์ไปสู่ข้อเสนอหรือแนวทางต่างๆ ในงานได้อย่างมีประสิทธิภาพสูงสุด', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(68, '303', 3, 'ผู้รับการประเมินประยุกต์ Best Practice ทฤษฎีหรือแนวคิดใหม่ๆ ที่ซับซ้อนในการกำหนดแผนงานหรือข้อเสนอต่างๆ ได้อย่างมีประสิทธิภาพสูงสุด', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(69, '303', 4, 'ผู้รับการประเมินประเมินและสังเคราะห์สถานการณ์หรือปัญหาทางเศรษฐกิจ สังคม การเมืองภายในประเทศและต่างประเทศที่ซับซ้อน เพื่อใช้ในการกำหนดแผนหรือนโยบายของหน่วยงาน หรือองค์กรได้อย่างมีประสิทธิภาพสูงสุด', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(70, '303', 5, 'ผู้รับการประเมินวิเคราะห์ปัญหา และสถานการณ์ในแง่มุมที่ลึกซึ้งโดยพิจารณาจากบริบทประเทศไทย สังคม เศรษฐกิจ ระบบอุตสาหกรรมในภาพรวมที่ซับซ้อน อันนำไปสู่การประดิษฐ์คิดค้น สร้างสรรค์ และนำเสนอองค์ความรู้ใหม่ๆ ที่ไม่เคยปรากฏมาก่อนและเป็นประโยชน์ต่อองค์กร หรือสังคมและประเทศชาติโดยรวม', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(71, '304', 1, 'ผู้รับการประเมินตั้งใจทำงานให้ถูกต้อง ละเอียดถี่ถ้วน และสะอาดเรียบร้อย', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(72, '304', 2, 'ผู้รับการประเมินหมั่นตรวจทานความถูกต้องของงานอย่างละเอียดรอบคอบเพื่อให้งานไม่มีข้อผิดพลาดหรือมีความถูกต้องสูงสุด', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(73, '304', 3, 'ผู้รับการประเมินตรวจสอบความถูกต้องของงานของตนและผู้อื่นที่อยู่ในความรับผิดชอบ เพื่อให้งานไม่มีข้อผิดพลาดหรือมีความถูกต้องสูงสุด', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(74, '304', 4, 'ผู้รับการประเมินกำกับติดตามความก้าวหน้าและความถูกต้องในเชิงคุณภาพของผลลัพธ์ของโครงการได้ตามเกณฑ์หรือกำหนดเวลาที่วางไว้อย่างมีประสิทธิภาพสูงสุด', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(75, '304', 5, 'ผู้รับการประเมินสร้างระบบที่สามารถกำกับตรวจสอบความก้าวหน้า ความถูกต้อง คุณภาพของผลงานในการปฏิบัติงานของผู้อื่น หรือหน่วยงานอื่นได้ รวมทั้งสร้างความชัดเจนของความถูกต้องและคุณภาพของขั้นตอนการทำงานหรือผลงานหรือโครงการโดยละเอียดเพื่อควบคุมให้ผู้อื่นปฏิบัติตามขั้นตอนการทำงานที่วางไว้อย่างถูกต้องและเกิดความโปร่งใสตรวจสอบได้', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(76, '305', 1, 'ผู้รับการประเมินเห็นความจำเป็น และแสวงหาโอกาสหรือช่องทางในการให้องค์ความรู้ คำแนะนำ ภูมิปัญญา นวัตกรรม และเทคโนโลยีต่างๆ แก่ผู้ประกอบการ หรือเครือข่าย', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(77, '305', 2, 'ผู้รับการประเมินริเริ่ม และให้องค์ความรู้ คำแนะนำ ภูมิปัญญา นวัตกรรม และเทคโนโลยีต่างๆ แก่ผู้ประกอบการ หรือเครือข่าย เพื่อให้สามารถนำไปปรับและประยุกต์ใช้ให้เกิดประโยชน์ได้', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(78, '305', 3, 'ผู้รับการประเมินลงพื้นที่และไปมาหาสู่ผู้ประกอบการ หรือเครือข่ายอย่างสม่ำเสมอ และสามารถปรับเปลี่ยนเทคนิค วิธีการ รูปแบบและแนวทางในการให้ความรู้ และการพัฒนาได้อย่างถูกต้อง เหมาะสม และสอดคล้องตามความต้องการอย่างแท้จริง', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(79, '305', 4, 'ผู้รับการประเมินเล็งเห็นแนวโน้ม ข้อจำกัดหรือโอกาสในการเพิ่มประสิทธิภาพในการดำเนินงานของผู้ประกอบการ หรือเครือข่าย จนสามารถประยุกต์ และพัฒนาองค์ความรู้ คำแนะนำ นวัตกรรม และเทคโนโลยีต่างๆ ได้อย่างถูกต้อง เหมาะสม และส่งเสริมการพัฒนาการดำเนินงานในระยะยาว', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(80, '305', 5, 'ผู้รับการประเมินกำหนดนโยบาย แผนยุทธศาสตร์ กลยุทธ์ และกรอบการพัฒนาผู้ประกอบการ หรือเครือข่ายในภาพรวมได้สอดคล้องกับสภาวการณ์เศรษฐกิจ และอุตสาหกรรมของประเทศ และสามารถนำไปปฏิบัติใช้จริง (Implementation)', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(81, '306', 1, 'ผู้รับการประเมินวางแผนงานเป็นขั้นตอนอย่างชัดเจน มีผลลัพธ์ สิ่งที่ต้องจัดเตรียม และกิจกรรมต่างๆ ที่จะเกิดขึ้นอย่างถูกต้อง', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(82, '306', 2, 'ผู้รับการประเมินจัดลำดับของงานและผลลัพธ์ในโครงการ รวมทั้งวิเคราะห์หาข้อดี ข้อเสียและผลต่อเนื่องของแผนงานในอดีต เพื่อสามารถวางแผนงานใหม่ได้อย่างมีประสิทธิภาพสูงสุด', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(83, '306', 3, 'ผู้รับการประเมินวางแผนงานที่มีความเชื่อมโยงหรือซับซ้อนกันหลายๆ งานหรือหลายๆ โครงการ ได้อย่างมีประสิทธิภาพ สอดคล้อง และไม่ขัดแย้งกัน', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(84, '306', 4, 'ผู้รับการประเมินวางแผนและคาดการณ์ล่วงหน้าเกี่ยวกับปัญหาหรือกิจกรรมที่มีหน่วยงานหรือผู้เกี่ยวข้องหลายฝ่าย แล้วเตรียมทางเลือกสำหรับการป้องกัน/แก้ไขปัญหาที่เกิดขึ้นได้อย่างมีประสิทธิภาพ', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(85, '306', 5, 'ผู้รับการประเมินปรับกลยุทธ์ แผนงาน และวางแผนอย่างรัดกุมและเป็นระบบให้เข้ากับสถานการณ์ที่เกิดขึ้นอย่างไม่คาดคิด เพื่อแก้ปัญหา อุปสรรค หรือสร้างโอกาสนั้นๆ อย่างมีคุณภาพสูงสุดและสามารถบรรลุวัตถุประสงค์ และเป้าหมายที่กำหนดไว้อย่างมีประสิทธิภาพสูงสุด', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(86, '307', 1, 'ผู้รับการประเมินปฏิบัติงานโดยคำนึงถึงความคุ้มค่าและค่าใช้จ่ายที่เกิดขึ้น', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(87, '307', 2, 'ผู้รับการประเมินลดค่าใช้จ่ายต่างๆ ที่จะเกิดขึ้น และจัดสรรงบประมาณ ค่าใช้จ่าย หรือทรัพยากรที่มีอยู่อย่างจำกัดให้คุ้มค่าและเกิดประโยชน์ในการปฏิบัติงานอย่างสูงสุด', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(88, '307', 3, 'ผู้รับการประเมินประเมินผลความมีประสิทธิภาพของการดำเนินงานที่ผ่านมาเพื่อปรับปรุงการจัดสรรทรัพยากรให้ได้ผลผลิตที่เพิ่มขึ้น หรือมีการทำงานที่มีประสิทธิภาพมากขึ้น หรือมีค่าใช้จ่ายที่ลดลง', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(89, '307', 4, 'ผู้รับการประเมินวางแผนและเชื่อมโยงภารกิจของหน่วยงานตนเองกับหน่วยงานอื่น (Synergy) เพื่อให้การใช้ทรัพยากรของหน่วยงานที่เกี่ยวข้องทั้งหมดเกิดประโยชน์สูงสุด', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(90, '307', 5, 'ผู้รับการประเมินพัฒนากระบวนการใหม่ๆ โดยอาศัยวิสัยทัศน์ ความเชี่ยวชาญ และประสบการณ์มาประยุกต์ในการทำงาน เพื่อลดภาระการบริหารงานได้อย่างมีประสิทธิภาพสูงสุด และเพิ่มผลผลิตหรือสร้างสรรค์งานใหม่ที่โดดเด่นแตกต่างให้กับองค์กร โดยใช้ทรัพยากรเท่าเดิม', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(91, '308', 1, 'ผู้รับการประเมินมีความเข้าใจความหมายที่ผู้ติดต่อสื่อสาร และสามารถปรับการทำงานให้คล่องตัวและสอดคล้องกับความต้องการได้', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(92, '308', 2, 'ผู้รับการประเมินเต็มใจ ยอมรับ และเข้าใจความคิดเห็นของผู้อื่นทั้งในเชิงเนื้อหาและนัยเชิงอารมณ์ เมื่อสถานการณ์ปรับเปลี่ยนไป เพื่อให้สามารถทำงานได้ตามเป้าหมายที่กำหนดไว้อย่างมีประสิทธิภาพ', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(93, '308', 3, 'ผู้รับการประเมินตีความหมายเบื้องลึกที่ไม่ได้แสดงออกอย่างชัดเจนของบุคคลหรือสถานการณ์ที่เกิดขึ้น แล้วปรับตัวให้สอดคล้อง และเหมาะสมกับกับแต่ละบุคคลหรือสถานการณ์ดังกล่าวได้อย่างมีประสิทธิภาพ', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(94, '308', 4, 'ผู้รับการประเมินใช้ความเข้าใจในเชิงลึกต่อบุคคลหรือสถานการณ์มาปรับเปลี่ยนวิธีการดำเนินงานในหน่วยงานให้ได้ผลสัมฤทธิ์ที่มีประสิทธิภาพสูงสุด', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(95, '308', 5, 'ผู้รับการประเมินปรับแผนกลยุทธ์ทั้งหมด เพื่อให้เหมาะสมกับสถานการณ์เฉพาะหน้า รวมทั้งประยุกต์หลักจิตวิทยาในการใช้ความเข้าใจผู้อื่นในสถานการณ์ต่างๆ เพื่อเป็นพื้นฐานในการเจรจาทำความเข้าใจ หรือดำเนินงานไห้ได้ตามภารกิจขององค์กร', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(96, '309', 1, 'ผู้รับการประเมินเข้าใจเทคโนโลยี ระบบ กระบวนการทำงาน กฎระเบียบและมาตรฐานในหน่วยงานที่สังกัด', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(97, '309', 2, 'ผู้รับการประเมินเข้าใจและเชื่อมโยงเทคโนโลยี ระบบ กระบวนการทำงาน ในหน่วยงานจนสามารถนำมาปรับใช้เพื่อให้การทำงานเป็นไปอย่างมีประสิทธิภาพ', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
	
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(98, '309', 3, 'ผู้รับการประเมินมองภาพรวม และเข้าใจข้อจำกัดของเทคโนโลยี ระบบหรือกระบวนการทำงานในหน่วยงาน แล้วเสนอการปรับเปลี่ยนหรือปรับปรุงให้การทำงานเป็นไปอย่างมีประสิทธิภาพสูงขึ้น', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(99, '309', 4, 'ผู้รับการประเมินเข้าใจกระแสภายนอกกับผลกระทบโดยรวมต่อเทคโนโลยี ระบบหรือกระบวนการทำงานของหน่วยงาน แล้วนำมาวางแผนและคาดการณ์ล่วงหน้าเพื่อเตรียมทางเลือกสำหรับการป้องกัน/แก้ไขปัญหาที่เกิดขึ้น', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(100, '309', 5, 'ผู้รับการประเมินเข้าใจสถานะของระบบ เทคโนโลยี และกระบวนการการทำงานขององค์กรอย่างถ่องแท้ และกำหนดความต้องการหรือดำเนินการเปลี่ยนแปลงในภาพรวม เพื่อให้องค์กรเติบโตอย่างยั่งยืน', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(101, '310', 1, 'ผู้รับการประเมินนำเสนอข้อมูล อธิบาย หรือชี้แจงรายละเอียดแก่ผู้ฟังอย่างตรงไปตรงมาโดยอิงข้อมูลที่มีอยู่ได้', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(102, '310', 2, 'ผู้รับการประเมินมักจะเตรียมการนำเสนอข้อมูลเป็นอย่างดี และใช้ความพยายามเจรจาโน้มน้าวใจโดยยกหลักการและเหตุผลที่เกี่ยวข้องมาประกอบการนำเสนออย่างมีขั้นตอน', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(103, '310', 3, 'ผู้รับการประเมินประยุกต์ใช้ความเข้าใจ ความสนใจของผู้ฟังให้เป็นประโยชน์ในเจรจาเสนอข้อมูล นำเสนอหรือเจรจาโดยคาดการณ์ถึงปฏิกิริยา ผลกระทบที่จะมีต่อผู้ฟังเป็นหลัก', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(104, '310', 4, 'ผู้รับการประเมินวางกลยุทธ์และใช้ทักษะในการโน้มน้าวใจทางอ้อม เพื่อให้ได้ผลสัมฤทธิ์ดังประสงค์โดยคำนึงถึงผลกระทบและความรู้สึกของผู้อื่นเป็นสำคัญ', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(105, '310', 5, 'ผู้รับการประเมินสร้างกลุ่มแนวร่วม และประยุกต์ใช้หลักจิตวิทยามวลชนหรือจิตวิทยากลุ่มให้เป็นประโยชน์ในการเจรจาโน้มน้าวจูงใจให้มีน้ำหนัก สัมฤทธิ์ผลได้ดียิ่งขึ้น และมีประสิทธิภาพสูงสุด', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(106, '311', 1, 'ผู้รับการประเมินเต็มใจและสนับสนุนความคิดสร้างสรรค์ โดยยอมทดลองวิธีอื่นๆ เพื่อมาทดแทนวิธีการที่ใช้อยู่เดิมในการปฏิบัติงานอย่างเต็มใจและใคร่รู้', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(107, '311', 2, 'ผู้รับการประเมินหมั่นสร้างสรรค์และปรับปรุงกระบวนการทำงานของตนอย่างสม่ำเสมอ', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(108, '311', 3, 'ผู้รับการประเมินคิดนอกกรอบเพื่อปรับเปลี่ยนการทำงาน และนำเสนอทางเลือก (Option) หรือแนวทางแก้ปัญหา (Solution) ในงานของตนอย่างสร้างสรรค์', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(109, '311', 4, 'ผู้รับการประเมินริเริ่มสร้างสรรค์สิ่งใหม่ๆ หรือแนวทางใหม่ๆ ในการปฏิบัติงานหรือดำเนินการต่างๆ ให้องค์กรอย่างมีประสิทธิภาพ และมีคุณภาพสูงขึ้น', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(110, '311', 5, 'ผู้รับการประเมินสร้างนวัตกรรมในระบบอุตสาหกรรมของประเทศ ตลอดจนองค์ความรู้ใหม่ที่ไม่เคยปรากฏมาก่อนและเป็นประโยชน์ต่อระบบอุตสาหกรรมหรือสังคมและประเทศชาติโดยรวม', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(111, '312', 1, 'ผู้รับการประเมินตอบสนอง หรือแก้ปัญหาได้อย่างรวดเร็ว และเด็ดเดี่ยวในเหตุวิกฤติ หรือสถานการณ์จำเป็น', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
	
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(112, '312', 2, 'ผู้รับการประเมินรู้จักพลิกแพลงวิธีการ กระบวนการต่างๆ เพื่อให้สามารถแก้ไขปัญหา หรือใช้ประโยชน์จากโอกาสนั้นได้อย่างมีประสิทธิภาพสูงสุด', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(113, '312', 3, 'ผู้รับการประเมินคาดการณ์และเล็งเห็นปัญหาหรือโอกาสที่อาจเกิดขึ้นในระยะเวลา 1-3 เดือนถัดจากปัจจุบัน และลงมือกระทำการล่วงหน้าเพื่อป้องกันปัญหา หรือสร้างโอกาสในสถานการณ์นั้นๆ ได้อย่างมีประสิทธิภาพสูงสุด', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(114, '312', 4, 'ผู้รับการประเมินคาดการณ์และเล็งเห็นปัญหาหรือโอกาสที่อาจเกิดขึ้นในระยะเวลา 4-12 เดือนถัดจากปัจจุบัน และเตรียมการเสาะหาวิธีการหรือแนวคิดใหม่ๆ ล่วงหน้า ที่อาจจะเป็นประโยชน์ในการป้องกันปัญหาและสร้างโอกาสในอนาคต', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(115, '312', 5, 'ผู้รับการประเมินคาดการณ์และเล็งเห็นปัญหาหรือโอกาสที่อาจเกิดขึ้นในระยะยาวและเตรียมการล่วงหน้าเพื่อป้องกันปัญหา หรือสร้างโอกาส อีกทั้งกระตุ้นให้ผู้อื่นเกิดความกระตือรือร้นต่อการป้องกันและแก้ไขปัญหาเพื่อสร้างโอกาสให้องค์กรในระยะยาว', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(116, '313', 1, 'ผู้รับการประเมินหาข้อมูลและสรุปผลข้อมูลเพื่อแสดงผลข้อมูลในรูปแบบต่างๆ เช่น กราฟ รายงาน ได้อย่างถูกต้องและครบถ้วน', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(117, '313', 2, 'ผู้รับการประเมินสืบเสาะปัญหาหรือสถานการณ์อย่างลึกซึ้ง จนได้มาซึ่งแก่นหรือประเด็นของเนื้อหา ที่สามารถนำมาจัดการ วิเคราะห์ และประเมินผลให้เกิดประโยชน์แก่หน่วยงานได้', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(118, '313', 3, 'ผู้รับการประเมินหมั่นค้นหาข้อมูลเจาะลึกอย่างต่อเนื่อง เพื่อให้เข้าใจถึงมุมมอง ทัศนะ ต้นตอของปัญหา หรือโอกาสที่ซ่อนเร้นอยู่ในเบื้องลึก จนสามารถนำมาจัดการ วิเคราะห์ และประเมินผลให้เกิดประโยชน์สูงสุดแก่หน่วยงานได้', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(119, '313', 4, 'ผู้รับการประเมินจัดทำการวิจัยอย่างเป็นระบบหรือเป็นไปตามหลักการทางสถิติ และนำผลที่ได้นั้นมาพยากรณ์หรือสร้างแบบจำลองหรือสร้างระบบที่เกิดประโยชน์สูงสุดต่อหน่วยงานได้', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(120, '313', 5, 'ผู้รับการประเมินวางระบบการสืบค้น เพื่อให้มีข้อมูลที่ทันเหตุการณ์ป้อนเข้ามาอย่างต่อเนื่องและสามารถออกแบบ เลือกใช้ หรือประยุกต์วิธีการในการจัดทำแบบจำลองหรือระบบต่างๆ ได้อย่างถูกต้องและมีนัยสำคัญ', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(121, '314', 1, 'ผู้รับการประเมินวางแผนงานบนพื้นฐานของความเข้าใจเทคโนโลยี ระบบ กระบวนการทำงานและมาตรฐานในงาน', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(122, '314', 2, 'ผู้รับการประเมินวิเคราะห์ข้อดี ข้อเสียและผลต่อเนื่องของเทคโนโลยี ระบบ และกระบวนการทำงานต่างๆ ของหน่วยงานเพื่อมาประกอบการวางแผนจัดลำดับความสำคัญและผลลัพธ์ของงานได้อย่างมีประสิทธิภาพสูงสุด', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(123, '314', 3, 'ผู้รับการประเมินมองภาพรวมแล้ววางแผนหรือเชื่อมโยงงานหรือกิจกรรมต่างๆ ที่มีความซับซ้อน เพื่อให้บรรลุตามเป้าหมายที่หน่วยงานกำหนดไว้ได้อย่างมีประสิทธิภาพสูงสุด', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(124, '314', 4, 'ผู้รับการประเมินเข้าใจกระแสภายนอกกับผลกระทบโดยรวมต่อเทคโนโลยี ระบบหรือกระบวนการทำงานของหน่วยงาน แล้วนำมาวางแผนและคาดการณ์ล่วงหน้าเพื่อเตรียมทางเลือกสำหรับการป้องกัน/แก้ไขปัญหาที่เกิดขึ้น', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(125, '314', 5, 'ผู้รับการประเมินเข้าใจสถานะของระบบ เทคโนโลยี และกระบวนการการทำงานของ สมอ. และกระทรวงอุตสาหกรรมอย่างถ่องแท้ จนสามารถปรับกลยุทธ์และวางแผนอย่างรัดกุม เพื่อดำเนินการเปลี่ยนแปลงในภาพรวมให้องค์กร และกระทรวงฯ เติบโตอย่างยั่งยืน', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(126, '315', 1, 'ผู้รับการประเมินติดตามหาความรู้และแนวคิดใหม่ๆ ในสายวิชาชีพ และใช้ความรู้หรือประสบการณ์ในการวิเคราะห์ และการลงมือแก้ไขปัญหาในระยะสั้นที่เกิดขึ้น', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(127, '315', 2, 'ผู้รับการประเมินวิเคราะห์ พลิกแพลง ประยุกต์ และตัดสินใจอย่างมีข้อมูลและเหตุผลในการจัดการปัญหาที่เกิดขึ้นได้อย่างมีประสิทธิภาพ', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(128, '315', 3, 'ผู้รับการประเมินวิเคราะห์ปัญหาที่ผ่านมา คาดการณ์ผลกระทบที่จะเกิดขึ้น และวางแผนล่วงหน้าอย่างเป็นระบบ เพื่อป้องกันหรือหลีกเลี่ยงปัญหาในหน่วยงาน หรือ สมอ.', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(129, '315', 4, 'ผู้รับการประเมินวิเคราะห์ และผสมผสาน (Integrate) แนวคิดในเชิงสหวิทยาการ เพื่อหลีกเลี่ยง ป้องกัน หรือแก้ไขปัญหาที่มีความซับซ้อนทั้งในระยะสั้นและระยะยาวอย่างมีประสิทธิภาพ', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(130, '315', 5, 'ผู้รับการประเมินปรับเปลี่ยนองค์กรให้มีการบูรณาการในเชิงวิชาชีพและสร้างให้มีความเชี่ยวชาญในสายอาชีพอย่างแท้จริง เพื่อสามารถแก้ไข ป้องกันและหลีกเลี่ยงปัญหาที่มีผลกระทบสูงหรือมีความซับซ้อนสูงขององค์กรได้อย่างยั่งยืน รวมทั้งเป็นผู้นำที่มีความเชี่ยวชาญในสายอาชีพที่สามารถป้องกัน และหลีกเลี่ยงปัญหาที่มีผลกระทบเชิงนโยบาย และกลยุทธ์ขององค์กรให้เกิดประโยชน์อย่างยั่งยืนแก่องค์กรในระยะยาว', 
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
		
		$code = array(	"520412 เจ้าพนักงานการเงินและบัญชี", 
										"523612 เจ้าพนักงานทรัพยากรธรณี", 
										"511612 เจ้าพนักงานธุรการ", 
										"511712 เจ้าพนักงานพัสดุ", 
										"550212 เจ้าพนักงานวิทยาศาสตร์", 
										"512212 เจ้าพนักงานสถิติ", 
										"522003 เศรษฐกร", 
										"511104 นักจัดการงานทั่วไป", 
										"510903 นักทรัพยากรบุคคล", 
										"551303 นักธรณีวิทยา", 
										"510703 นักวิเคราะห์นโยบายและแผน", 
										"520423 นักวิชาการเงินและบัญชี", 
										"532423 นักวิชาการเผยแพร่", 
										"532523 นักวิชาการโสตทัศนศึกษา", 
										"511013 นักวิชาการคอมพิวเตอร์", 
										"520603 นักวิชาการตรวจสอบภายใน", 
										"523623 นักวิชาการทรัพยากรธรณี", 
										"511723 นักวิชาการพัสดุ", 
										"512003 นักวิชาการสถิติ", 
										"584003 นักวิชาการสิ่งแวดล้อม", 
										"523203 นักวิชาการอุตสาหกรรม", 
										"550103 นักวิทยาศาสตร์", 
										"512603 นักสืบสวนสอบสวน", 
										"574312 นายช่างเขียนแบบ", 
										"571912 นายช่างเครื่องกล", 
										"573712 นายช่างเหมืองแร่", 
										"571412 นายช่างโยธา", 
										"572412 นายช่างโลหะ", 
										"573012 นายช่างไฟฟ้า", 
										"571512 นายช่างรังวัด", 
										"512403 นิติกร", 
										"510308 ผู้อำนวยการ",
										"570103 วิศวกร", 
										"570403 วิศวกรเครื่องกล", 
										"570603 วิศวกรเหมืองแร่", 
										"570203 วิศวกรโยธา", 
										"570803 วิศวกรโลหการ", 
										"570503 วิศวกรไฟฟ้า" ); 
												
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
			if ($PL_CODE=="510308") { // ผู้อำนวยการ
				if ($ORG_ID==9489) $code = array(	"303", "304", "312" ); // สำนักบริหารกลาง
				elseif ($ORG_ID==9492) $code = array(	"303", "305", "315" ); // สำนักงานอุตสาหกรรมพื้นฐานและการเหมืองแร่เขต 1
				elseif ($ORG_ID==9493) $code = array(	"303", "305", "315" ); // สำนักงานอุตสาหกรรมพื้นฐานและการเหมืองแร่เขต 2
				elseif ($ORG_ID==9494) $code = array(	"303", "305", "315" ); // สำนักงานอุตสาหกรรมพื้นฐานและการเหมืองแร่เขต 3
				elseif ($ORG_ID==9495) $code = array(	"301", "305", "315" ); // สำนักบริหารสิ่งแวดล้อม
				elseif ($ORG_ID==9496) $code = array(	"303", "314", "315" ); // สำนักเหมืองแร่และสัมปทาน
				elseif ($ORG_ID==9497) $code = array(	"303", "314", "315" ); // สำนักอุตสาหกรรมพื้นฐาน
				elseif ($ORG_ID==9802) $code = array(	"303", "313", "316" ); // สำนักบริหารยุทธศาสตร์
				elseif ($ORG_ID==12970) $code = array(	"303", "305", "313" ); // สำนักโลจิสติกส์
			} elseif ($PL_CODE=="510703") { // นักวิเคราะห์นโยบายและแผน
				if ($ORG_ID==9802) $code = array(	"303", "313", "314" ); // สำนักบริหารยุทธศาสตร์
			} elseif ($PL_CODE=="510903") { // นักทรัพยากรบุคคล
				if ($ORG_ID==9489) $code = array(	"304", "308", "314" ); // สำนักบริหารกลาง
			} elseif ($PL_CODE=="511013") { // นักวิชาการคอมพิวเตอร์
				if ($ORG_ID==9802) $code = array(	"303", "313", "314" ); // สำนักบริหารยุทธศาสตร์
			} elseif ($PL_CODE=="511104") { // นักจัดการงานทั่วไป
				if ($ORG_ID==9489) $code = array(	"304", "312", "314" ); // สำนักบริหารกลาง
				elseif ($ORG_ID==9491) $code = array(	"304", "312", "314" ); // สำนักวิศวกรรมและฟื้นฟูพื้นที่
				elseif ($ORG_ID==9492) $code = array(	"304", "312", "314" ); // สำนักงานอุตสาหกรรมพื้นฐานและการเหมืองแร่เขต 1
				elseif ($ORG_ID==9493) $code = array(	"304", "312", "314" ); // สำนักงานอุตสาหกรรมพื้นฐานและการเหมืองแร่เขต 2
				elseif ($ORG_ID==9494) $code = array(	"304", "312", "314" ); // สำนักงานอุตสาหกรรมพื้นฐานและการเหมืองแร่เขต 3
				elseif ($ORG_ID==9495) $code = array(	"304", "312", "314" ); // สำนักบริหารสิ่งแวดล้อม
				elseif ($ORG_ID==9496) $code = array(	"304", "312", "314" ); // สำนักเหมืองแร่และสัมปทาน
				elseif ($ORG_ID==9497) $code = array(	"304", "312", "314" ); // สำนักอุตสาหกรรมพื้นฐาน
				elseif ($ORG_ID==12970) $code = array(	"304", "312", "314" ); // สำนักโลจิสติกส์
			} elseif ($PL_CODE=="511612") { // เจ้าพนักงานธุรการ
				if ($ORG_ID==9489) $code = array(	"304", "309", "312" ); // สำนักบริหารกลาง
				elseif ($ORG_ID==9490) $code = array(	"304", "309", "312" ); // สำนักกฎหมาย
				elseif ($ORG_ID==9491) $code = array(	"304", "309", "312" ); // สำนักวิศวกรรมและฟื้นฟูพื้นที่
				elseif ($ORG_ID==9492) $code = array(	"304", "309", "312" ); // สำนักงานอุตสาหกรรมพื้นฐานและการเหมืองแร่เขต 1
				elseif ($ORG_ID==9493) $code = array(	"304", "309", "312" ); // สำนักงานอุตสาหกรรมพื้นฐานและการเหมืองแร่เขต 2
				elseif ($ORG_ID==9494) $code = array(	"304", "309", "312" ); // สำนักงานอุตสาหกรรมพื้นฐานและการเหมืองแร่เขต 3
				elseif ($ORG_ID==9495) $code = array(	"304", "309", "312" ); // สำนักบริหารสิ่งแวดล้อม
				elseif ($ORG_ID==9496) $code = array(	"304", "309", "312" ); // สำนักเหมืองแร่และสัมปทาน
				elseif ($ORG_ID==9497) $code = array(	"304", "309", "312" ); // สำนักอุตสาหกรรมพื้นฐาน
				elseif ($ORG_ID==9802) $code = array(	"304", "309", "312" ); // สำนักบริหารยุทธศาสตร์
				elseif ($ORG_ID==9803) $code = array(	"304", "309", "312" ); // กลุ่มตรวจสอบภายใน
				elseif ($ORG_ID==12970) $code = array(	"304", "309", "312" ); // สำนักโลจิสติกส์
			} elseif ($PL_CODE=="511712") { // เจ้าพนักงานพัสดุ
				if ($ORG_ID==9489) $code = array(	"302", "304", "309" ); // สำนักบริหารกลาง
			} elseif ($PL_CODE=="511723") { // นักวิชาการพัสดุ
				if ($ORG_ID==9489) $code = array(	"302", "304", "309" ); // สำนักบริหารกลาง
			} elseif ($PL_CODE=="512003") { // นักวิชาการสถิติ
				if ($ORG_ID==9802) $code = array(	"303", "313", "314" ); // สำนักบริหารยุทธศาสตร์
			} elseif ($PL_CODE=="512212") { // เจ้าพนักงานสถิติ
				if ($ORG_ID==9802) $code = array(	"303", "309", "313" ); // สำนักบริหารยุทธศาสตร์
			} elseif ($PL_CODE=="512403") { // นิติกร
				if ($ORG_ID==9489) $code = array(	"302", "304", "314" ); // สำนักบริหารกลาง
				elseif ($ORG_ID==9490) $code = array(	"302", "304", "313" ); // สำนักกฎหมาย
			} elseif ($PL_CODE=="512603") { // นักสืบสวนสอบสวน
				if ($ORG_ID==9490) $code = array(	"302", "313", "317" ); // สำนักกฎหมาย
			} elseif ($PL_CODE=="512409") { // ผู้อำนวยการเฉพาะด้าน (นิติการ)
				if ($ORG_ID==9490) $code = array(	"302", "304", "313" ); // สำนักกฎหมาย
			} elseif ($PL_CODE=="520412") { // เจ้าพนักงานการเงินและบัญชี
				if ($ORG_ID==9489) $code = array(	"302", "304", "309" ); // สำนักบริหารกลาง
				elseif ($ORG_ID==9490) $code = array(	"302", "304", "309" ); // สำนักกฎหมาย
				elseif ($ORG_ID==9491) $code = array(	"302", "304", "309" ); // สำนักวิศวกรรมและฟื้นฟูพื้นที่
				elseif ($ORG_ID==9492) $code = array(	"302", "304", "309" ); // สำนักงานอุตสาหกรรมพื้นฐานและการเหมืองแร่เขต 1
				elseif ($ORG_ID==9493) $code = array(	"302", "304", "309" ); // สำนักงานอุตสาหกรรมพื้นฐานและการเหมืองแร่เขต 2
				elseif ($ORG_ID==9494) $code = array(	"302", "304", "309" ); // สำนักงานอุตสาหกรรมพื้นฐานและการเหมืองแร่เขต 3
				elseif ($ORG_ID==9496) $code = array(	"302", "304", "309" ); // สำนักเหมืองแร่และสัมปทาน
			} elseif ($PL_CODE=="520423") { // นักวิชาการเงินและบัญชี
				if ($ORG_ID==9489) $code = array(	"302", "304", "314" ); // สำนักบริหารกลาง
				elseif ($ORG_ID==9492) $code = array(	"302", "304", "314" ); // สำนักงานอุตสาหกรรมพื้นฐานและการเหมืองแร่เขต 1
			} elseif ($PL_CODE=="520603") { // นักวิชาการตรวจสอบภายใน
				if ($ORG_ID==9803) $code = array(	"302", "304", "314" ); // กลุ่มตรวจสอบภายใน
			} elseif ($PL_CODE=="522003") { // เศรษฐกร
				if ($ORG_ID==9488) $code = array(	"303", "313", "314" ); // ส่วนกลาง
				elseif ($ORG_ID==9496) $code = array(	"303", "313", "314" ); // สำนักเหมืองแร่และสัมปทาน
				elseif ($ORG_ID==9802) $code = array(	"303", "313", "314" ); // สำนักบริหารยุทธศาสตร์
			} elseif ($PL_CODE=="523203") { // นักวิชาการอุตสาหกรรม
				if ($ORG_ID==9490) $code = array(	"302", "304", "313" ); // สำนักกฎหมาย
				elseif ($ORG_ID==9496) $code = array(	"302", "304", "314" ); // สำนักเหมืองแร่และสัมปทาน
				elseif ($ORG_ID==9802) $code = array(	"303", "313", "316" ); // สำนักบริหารยุทธศาสตร์
			} elseif ($PL_CODE=="523612") { // เจ้าพนักงานทรัพยากรธรณี
				if ($ORG_ID==9490) $code = array(	"302", "304", "309" ); // สำนักกฎหมาย
				elseif ($ORG_ID==9496) $code = array(	"302", "304", "309" ); // สำนักเหมืองแร่และสัมปทาน
			} elseif ($PL_CODE=="523623") { // นักวิชาการทรัพยากรธรณี
				if ($ORG_ID==9496) $code = array(	"302", "304", "314" ); // สำนักเหมืองแร่และสัมปทาน
				elseif ($ORG_ID==9497) $code = array(	"302", "304", "314" ); // สำนักอุตสาหกรรมพื้นฐาน
			} elseif ($PL_CODE=="532423") { // นักวิชาการเผยแพร่
				if ($ORG_ID==9489) $code = array(	"304", "310", "311" ); // สำนักบริหารกลาง
			} elseif ($PL_CODE=="532523") { // นักวิชาการโสตทัศนศึกษา
				if ($ORG_ID==9489) $code = array(	"304", "310", "311" ); // สำนักบริหารกลาง
			} elseif ($PL_CODE=="550103") { // นักวิทยาศาสตร์
				if ($ORG_ID==9492) $code = array(	"301", "305", "313" ); // สำนักงานอุตสาหกรรมพื้นฐานและการเหมืองแร่เขต 1
				elseif ($ORG_ID==9493) $code = array(	"301", "305", "313" ); // สำนักงานอุตสาหกรรมพื้นฐานและการเหมืองแร่เขต 2
				elseif ($ORG_ID==9494) $code = array(	"301", "305", "313" ); // สำนักงานอุตสาหกรรมพื้นฐานและการเหมืองแร่เขต 3
				elseif ($ORG_ID==9497) $code = array(	"303", "314", "315" ); // สำนักอุตสาหกรรมพื้นฐาน
				elseif ($ORG_ID==12970) $code = array(	"303", "305", "313" ); // สำนักโลจิสติกส์
			} elseif ($PL_CODE=="550212") { // เจ้าพนักงานวิทยาศาสตร์
				if ($ORG_ID==9495) $code = array(	"305", "313", "315" ); // สำนักบริหารสิ่งแวดล้อม
				elseif ($ORG_ID==9497) $code = array(	"304", "309", "317" ); // สำนักอุตสาหกรรมพื้นฐาน
				elseif ($ORG_ID==12970) $code = array(	"304", "305", "313" ); // สำนักโลจิสติกส์
			} elseif ($PL_CODE=="551303") { // นักธรณีวิทยา
				if ($ORG_ID==9492) $code = array(	"303", "304", "313" ); // สำนักงานอุตสาหกรรมพื้นฐานและการเหมืองแร่เขต 1
				elseif ($ORG_ID==9493) $code = array(	"303", "304", "313" ); // สำนักงานอุตสาหกรรมพื้นฐานและการเหมืองแร่เขต 2
				elseif ($ORG_ID==9494) $code = array(	"303", "304", "313" ); // สำนักงานอุตสาหกรรมพื้นฐานและการเหมืองแร่เขต 3
				elseif ($ORG_ID==9496) $code = array(	"303", "304", "313" ); // สำนักเหมืองแร่และสัมปทาน
			} elseif ($PL_CODE=="570103") { // วิศวกร
				if ($ORG_ID==9497) $code = array(	"303", "314", "315" ); // สำนักอุตสาหกรรมพื้นฐาน
			} elseif ($PL_CODE=="570109") { // ผู้อำนวยการเฉพาะด้าน (วิศวกรรม)
				if ($ORG_ID==9491) $code = array(	"303", "314", "315" ); // สำนักวิศวกรรมและฟื้นฟูพื้นที่
			} elseif ($PL_CODE=="570203") { // วิศวกรโยธา
				if ($ORG_ID==9491) $code = array(	"303", "313", "315" ); // สำนักวิศวกรรมและฟื้นฟูพื้นที่
			} elseif ($PL_CODE=="570403") { // วิศวกรเครื่องกล
				if ($ORG_ID==9491) $code = array(	"303", "313", "315" ); // สำนักวิศวกรรมและฟื้นฟูพื้นที่
				elseif ($ORG_ID==9497) $code = array(	"303", "314", "315" ); // สำนักอุตสาหกรรมพื้นฐาน
				elseif ($ORG_ID==12970) $code = array(	"303", "305", "313" ); // สำนักโลจิสติกส์
			} elseif ($PL_CODE=="570503") { // วิศวกรไฟฟ้า
				if ($ORG_ID==9497) $code = array(	"303", "304", "314" ); // สำนักอุตสาหกรรมพื้นฐาน
			} elseif ($PL_CODE=="570603") { // วิศวกรเหมืองแร่
				if ($ORG_ID==9488) $code = array(	"303", "313", "314" ); // ส่วนกลาง
				elseif ($ORG_ID==9492) $code = array(	"305", "314", "315" ); // สำนักงานอุตสาหกรรมพื้นฐานและการเหมืองแร่เขต 1
				elseif ($ORG_ID==9493) $code = array(	"305", "314", "315" ); // สำนักงานอุตสาหกรรมพื้นฐานและการเหมืองแร่เขต 2
				elseif ($ORG_ID==9494) $code = array(	"305", "314", "315" ); // สำนักงานอุตสาหกรรมพื้นฐานและการเหมืองแร่เขต 3
				elseif ($ORG_ID==9496) $code = array(	"303", "313", "314" ); // สำนักเหมืองแร่และสัมปทาน
				elseif ($ORG_ID==9497) $code = array(	"303", "314", "315" ); // สำนักอุตสาหกรรมพื้นฐาน
				elseif ($ORG_ID==9802) $code = array(	"303", "305", "313" ); // สำนักบริหารยุทธศาสตร์
				elseif ($ORG_ID==12970) $code = array(	"303", "305", "313" ); // สำนักโลจิสติกส์
			} elseif ($PL_CODE=="570803") { // วิศวกรโลหการ
				if ($ORG_ID==9488) $code = array(	"303", "313", "314" ); // ส่วนกลาง
				elseif ($ORG_ID==9493) $code = array(	"305", "314", "315" ); // สำนักงานอุตสาหกรรมพื้นฐานและการเหมืองแร่เขต 2
				elseif ($ORG_ID==9497) $code = array(	"303", "314", "315" ); // สำนักอุตสาหกรรมพื้นฐาน
				elseif ($ORG_ID==9802) $code = array(	"303", "313", "316" ); // สำนักบริหารยุทธศาสตร์
				elseif ($ORG_ID==12970) $code = array(	"303", "305", "313" ); // สำนักโลจิสติกส์
			} elseif ($PL_CODE=="571412") { // นายช่างโยธา
				if ($ORG_ID==9491) $code = array(	"306", "311", "315" ); // สำนักวิศวกรรมและฟื้นฟูพื้นที่
			} elseif ($PL_CODE=="571512") { // นายช่างรังวัด
				if ($ORG_ID==9491) $code = array(	"304", "309", "312" ); // สำนักวิศวกรรมและฟื้นฟูพื้นที่
				elseif ($ORG_ID==9492) $code = array(	"304", "309", "313" ); // สำนักงานอุตสาหกรรมพื้นฐานและการเหมืองแร่เขต 1
				elseif ($ORG_ID==9494) $code = array(	"304", "309", "313" ); // สำนักงานอุตสาหกรรมพื้นฐานและการเหมืองแร่เขต 3
			} elseif ($PL_CODE=="571912") { // นายช่างเครื่องกล
				if ($ORG_ID==9491) $code = array(	"306", "311", "315" ); // สำนักวิศวกรรมและฟื้นฟูพื้นที่
				elseif ($ORG_ID==9497) $code = array(	"304", "309", "315" ); // สำนักอุตสาหกรรมพื้นฐาน
			} elseif ($PL_CODE=="572412") { // นายช่างโลหะ
				if ($ORG_ID==9497) $code = array(	"304", "309", "315" ); // สำนักอุตสาหกรรมพื้นฐาน
			} elseif ($PL_CODE=="573012") { // นายช่างไฟฟ้า
				if ($ORG_ID==9497) $code = array(	"304", "314", "315" ); // สำนักอุตสาหกรรมพื้นฐาน
			} elseif ($PL_CODE=="573712") { // นายช่างเหมืองแร่
				if ($ORG_ID==9492) $code = array(	"304", "313", "318" ); // สำนักงานอุตสาหกรรมพื้นฐานและการเหมืองแร่เขต 1
				elseif ($ORG_ID==9494) $code = array(	"301", "305", "315" ); // สำนักงานอุตสาหกรรมพื้นฐานและการเหมืองแร่เขต 3
				elseif ($ORG_ID==9495) $code = array(	"301", "305", "315" ); // สำนักบริหารสิ่งแวดล้อม
				elseif ($ORG_ID==9496) $code = array(	"303", "305", "315" ); // สำนักเหมืองแร่และสัมปทาน
				elseif ($ORG_ID==9497) $code = array(	"304", "309", "315" ); // สำนักอุตสาหกรรมพื้นฐาน
				elseif ($ORG_ID==9802) $code = array(	"303", "305", "313" ); // สำนักบริหารยุทธศาสตร์
				elseif ($ORG_ID==12970) $code = array(	"304", "305", "313" ); // สำนักโลจิสติกส์
			} elseif ($PL_CODE=="574312") { // นายช่างเขียนแบบ
				if ($ORG_ID==9497) $code = array(	"304", "309", "315" ); // สำนักอุตสาหกรรมพื้นฐาน
				elseif ($ORG_ID==12970) $code = array(	"304", "305", "313" ); // สำนักโลจิสติกส์
			} elseif ($PL_CODE=="584003") { // นักวิชาการสิ่งแวดล้อม
				if ($ORG_ID==9491) $code = array(	"306", "311", "315" ); // สำนักวิศวกรรมและฟื้นฟูพื้นที่
				elseif ($ORG_ID==9495) $code = array(	"303", "355", "315" ); // สำนักบริหารสิ่งแวดล้อม
				elseif ($ORG_ID==12970) $code = array(	"303", "305", "313" ); // สำนักโลจิสติกส์
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
						VALUES(1, 1, '101', 'การมอบหมายงานและการหมุนเวียนงาน
มอบหมายงานตามหน้าที่ความรับผิดชอบของแต่ละบุคคล แต่มีการกำหนดเวลาการส่งมอบงานในแต่ละงานอย่างชัดเจน
ส่งเสริมและเปิดโอกาสให้บุคลากรได้แสดงความคิดเห็นหรือข้อเสนอแนะต่างๆ เพิ่มเติมที่เกี่ยวข้องกับหน้าที่ความรับผิดชอบ เพื่อส่งเสริมให้สามารถพัฒนางานของตนให้ดียิ่งขึ้นไปได้
ส่งเสริมและเปิดโอกาสให้บุคลากรได้แสดงความคิดเห็นว่าตนเองจะสามารถสร้างคุณค่าและประโยชน์ที่ดีให้แก่งานและองค์กรได้อย่างไร และมอบหมายงานนั้นๆ ตามความสนใจของแต่ละบุคคล
มอบหมายงานที่มีลักษณะเป็นเป้าหมายระยะสั้นและสามารถติดตามผล เปรียบเทียบความก้าวหน้าได้ พร้อมทั้งได้รับคำติชมเกี่ยวกับผลงานในทันที
การโค้ช (Coaching)
ดูแล แนะนำ ให้คำปรึกษาเรื่องวิธีการการทำงานให้ได้ตามมาตรฐานของงานและมาตรฐานขององค์กร
การฝึกอบรม
Time Management', 'ชื่อหลักสูตรการฝึกอบรม: Time Management 
วัตถุประสงค์
เพื่อให้ผู้ฝึกอบรมมีกรอบความคิด เรื่องการบริหารจัดการเวลา 
กรอบเนื้อหาของหลักสูตร
เนื้อหาหลักประกอบด้วย การบรรยายเพื่อให้เห็นถึง
กรอบแนวคิดในการบริหารจัดการเวลาและประโยชน์ของการบริหารจัดการเวลา
ข้อผิดพลาดต่างๆ ที่ทำให้เสียเวลา (การบริหารเหตุไม่คาดหวัง (Crisis) การรบกวนโดยโทรศัพท์ การไม่วางแผนล่วงหน้า การแบ่งงาน การไม่วินัยต่อตนเอง ความไม่สามารถที่จะปฏิเสธ การบริหารจัดการการประชุม การบริหารงานเอกสาร (Paperwork) การสื่อสารที่ไม่ดี เป็นต้น)
กระบวนการและเทคนิคในการแก้ไขข้อผิดพลาดเพื่อให้สามารถบริหารจัดการเวลาได้อย่างมีประสิทธิภาพสูงสุด
ระยะเวลาของหลักสูตร
8 ชั่วโมง
แนวทางการประเมินผล
ผู้เข้าอบรม จะถูกประเมินเมื่อเริ่มการฝึกอบรม และสิ้นสุดการฝึกอบรมว่ามีระดับการพัฒนาความเข้าใจหรือไม่ เพียงใด นอกจากนั้นให้ผู้บังคับบัญชาประเมินว่าในการปฏิบัติงานจริงนั้นสามารถบริหารจัดการเวลาได้มากน้อยเพียงใด', $SESS_USERID, '$UPDATE_DATE') ";
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