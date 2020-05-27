<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$CREATE_DATE = "NOW()";
	$UPDATE_DATE = date("Y-m-d H:i:s");

	if( $command=='ALTER' ) {
/*		
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
			CP_ACTIVE SMALLINT(1) NOT NULL,
			UPDATE_USER SMALLINT(5) NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_COMPETENCE PRIMARY KEY (cp_code)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ACTIVE, 
						  UPDATE_USER, UPDATE_DATE)
						  VALUES ('101', 'การมุ่งผลสัมฤทธิ์', 'Achievement Motivation- ACH', 'ความมุ่งมั่นจะปฏิบัติราชการให้ดีหรือให้เกินมาตรฐานที่มีอยู่ โดยมาตรฐานนี้อาจเป็นผลการปฏิบัติงานที่ผ่านมาของตนเอง หรือเกณฑ์วัดผลสัมฤทธิ์ที่ส่วนราชการกำหนดขึ้น อีกทั้งยังหมายรวมถึงการสร้างสรรค์พัฒนาผลงานหรือกระบวนการปฏิบัติงานตามเป้าหมายที่ยากและท้าทายชนิดที่อาจไม่เคยมีผู้ใดสามารถกระทำได้มาก่อน', 1, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ACTIVE, 
						  UPDATE_USER, UPDATE_DATE)
						  VALUES ('102', 'บริการที่ดี', 'Service Mind- SERV', 'สมรรถนะนี้เน้นความตั้งใจและความพยายามของข้าราชการในการให้บริการเพื่อสนองความต้องการของประชาชนตลอดจนของหน่วยงานภาครัฐอื่นๆ ที่เกี่ยวข้อง', 1, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ACTIVE, 
						  UPDATE_USER, UPDATE_DATE)
						  VALUES ('103', 'การสั่งสมความเชี่ยวชาญในงานอาชีพ', 'Expertise- EXP', 'ความขวนขวาย สนใจใฝ่รู้ เพื่อสั่งสมพัฒนาศักยภาพ ความรู้ความสามารถของตนในการปฏิบัติราชการ ด้วยการศึกษา ค้นคว้าหาความรู้ พัฒนาตนเองอย่างต่อเนื่อง อีกทั้งรู้จักพัฒนา ปรับปรุง ประยุกต์ใช้ความรู้เชิงวิชาการและเทคโนโลยีต่างๆ เข้ากับการปฏิบัติงานให้เกิดผลสัมฤทธิ์', 1, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ACTIVE, 
						  UPDATE_USER, UPDATE_DATE)
						  VALUES ('104', 'การยึดมั่นในความถูกต้องชอบธรรม และจริยธรรม', 'Expertise- EXP', 'การครองตนและประพฤติปฏิบัติถูกต้องเหมาะสมทั้งตามหลักกฎหมายและคุณธรรมจริยธรรม ตลอดจนหลักแนวทางในวิชาชีพของตนโดยมุ่งประโยชน์ของประเทศชาติมากกว่าประโยชน์ส่วนตน  ทั้งนี้เพื่อธำรงรักษาศักดิ์ศรีแห่งอาชีพข้าราชการ อีกทั้งเพื่อเป็นกำลังสำคัญในการสนับสนุนผลักดันให้ภารกิจหลักภาครัฐบรรลุเป้าหมายที่กำหนดไว้', 1, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ACTIVE, 
						  UPDATE_USER, UPDATE_DATE)
						  VALUES ('105', 'การทำงานเป็นทีม', 'Teamwork- TW', 'สมรรถนะนี้เน้นที่ 1) ความตั้งใจที่จะทำงานร่วมกับผู้อื่น เป็นส่วนหนึ่งในทีมงาน หน่วยงาน หรือองค์กร โดยผู้ปฏิบัติมีฐานะเป็นสมาชิกในทีม มิใช่ในฐานะหัวหน้าทีม และ 2) ความสามารถในการสร้างและดำรงรักษาสัมพันธภาพกับสมาชิกในทีม', 1, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ACTIVE, 
						  UPDATE_USER, UPDATE_DATE)
						  VALUES ('201', 'สภาวะผู้นำ', 'Leadership- LEAD', 'ความตั้งใจหรือความสามารถในการเป็นผู้นำของกลุ่มคน ปกครอง รวมถึงการกำหนดทิศทาง วิสัยทัศน์ เป้าหมาย วิธีการทำงาน ให้ผู้ใต้บังคับบัญชาหรือทีมงานปฏิบัติงานได้อย่างราบรื่น เต็มประสิทธิภาพและบรรลุวัตถุประสงค์ขององค์กร', 2, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ACTIVE, 
						  UPDATE_USER, UPDATE_DATE)
						  VALUES ('202', 'วิสัยทัศน์', 'Visioning- VIS', 'ความสามารถให้ทิศทางที่ชัดเจนและก่อความร่วมแรงร่วมใจในหมู่ผู้ใต้บังคับบัญชาเพื่อนำพางานภาครัฐไปสู่จุดหมายร่วมกัน', 2, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ACTIVE, 
						  UPDATE_USER, UPDATE_DATE)
						  VALUES ('203', 'การวางกลยุทธ์ภาครัฐ', 'Strategic Orientation- SO', 'ความเข้าใจกลยุทธ์ภาครัฐและสามารถประยุกต์ใช้ในการกำหนดกลยุทธ์ของหน่วยงานตนได้ โดยความสามารถในการประยุกต์นี้รวมถึงความสามารถในการคาดการณ์ถึงทิศทางระบบราชการในอนาคต ตลอดจนผลกระทบของสถานการณ์ทั้งในและต่างประเทศที่เกิดขึ้น', 2, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ACTIVE, 
						  UPDATE_USER, UPDATE_DATE)
						  VALUES ('204', 'ศักยภาพเพื่อนำการปรับเปลี่ยน', 'Change Leadership- CL', 'ความตั้งใจและความสามารถในการกระตุ้นผลักดันกลุ่มคนให้เกิดความต้องการจะปรับเปลี่ยนไปในแนวทางที่เป็นประโยชน์แก่ภาครัฐ รวมถึงการสื่อสารให้ผู้อื่นรับรู้ เข้าใจ และดำเนินการให้การปรับเปลี่ยนนั้นเกิดขึ้นจริง', 2, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ACTIVE, 
						  UPDATE_USER, UPDATE_DATE)
						  VALUES ('205', 'การควบคุมตนเอง', 'Self Control- SCT', 'การระงับอารมณ์และพฤติกรรมอันไม่เหมาะสมเมื่อถูกยั่วยุ หรือเผชิญหน้ากับฝ่ายตรงข้าม เผชิญความไม่เป็นมิตร หรือทำงานภายใต้สภาวะความกดดัน รวมถึงความอดทนอดกลั้นเมื่อต้องอยู่ภายใต้สถานการณ์ที่ก่อความเครียดอย่างต่อเนื่อง', 2, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ACTIVE, 
						  UPDATE_USER, UPDATE_DATE)
						  VALUES ('206', 'การสอนงานและให้อำนาจแก่ผู้อื่น', 'Coaching and Empowering Others - CEMP', 'ความตั้งใจจะส่งเสริมการเรียนรู้หรือการพัฒนาผู้อื่นในระยะยาว รวมถึงความเชื่อมั่นในความสามารถของผู้อื่น ดังนั้นจึงมอบหมายอำนาจและหน้าที่รับผิดชอบให้เพื่อให้ผู้อื่นมีอิสระในการสร้างสรรค์วิธีการของตนเพื่อบรรลุเป้าหมายในงาน', 2, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
*/
		$cmd = " UPDATE PER_COMPETENCE SET CP_NAME = 'การแก้ปัญหาและสร้างโอกาส', CP_ENG_NAME = 'Initiative- INT', 
		CP_MEANING = 'การตระหนักหรือเล็งเห็นโอกาสหรือปัญหาอุปสรรคที่อาจเกิดขึ้นในอนาคต และวางแผน ลงมือกระทำการเพื่อเตรียมใช้ประโยชน์จากโอกาส หรือป้องกันปัญหา ตลอดจนพลิกวิกฤติต่างๆ ให้เป็นโอกาส'
						  WHERE CP_CODE = '301' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE SET CP_NAME = 'ความเข้าใจอุตสาหกรรมพื้นที่', CP_ENG_NAME = 'Industry and Business Awareness- IBA', 
		CP_MEANING = 'ความสามารถในการใช้ความเข้าใจที่ถูกต้องด้านเศรษฐกิจการอุตสาหกรรม การดำเนินธุรกิจ และภาพรวมทั้งหมดของจังหวัดที่รับผิดชอบมาปรับปรุง และบริหารจัดการให้ผู้ประกอบการในพื้นที่มีผลประกอบการและผลกำไรที่ดี รวมถึงการเพื่อให้สามารถพัฒนาธุรกิจของจังหวัดให้มีประสิทธิภาพอย่างสูงสุด'
						  WHERE CP_CODE = '302' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE SET CP_NAME = 'การคิดวิเคราะห์', CP_ENG_NAME = 'Analytical Thinking- AT', 
		CP_MEANING = 'ความสามารถในการทำความเข้าใจในสถานการณ์ ประเด็น ปัญหา โดยคิดวิเคราะห์ออกเป็นส่วนย่อยๆ เป็นรายการ หรือเป็นขั้นตอน และเห็นความสัมพันธ์ของสถานการณ์ ประเด็น หรือปัญหาที่เกิดขึ้นโดยรู้ถึงสาเหตุ และผลกระทบของสถานการณ์ ประเด็น หรือปัญหาที่อาจเกิดขึ้นได้'
						  WHERE CP_CODE = '303' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE SET CP_NAME = 'การคิดวิเคราะห์และการแก้ไขปัญหาแบบมืออาชีพ', CP_ENG_NAME = 'Analyze and Solve Problems Professionally – ASP', 
		CP_MEANING = 'ความสามารถในวิเคราะห์ปัญหาหรือเล็งเห็นปัญหา พร้อมทั้งลงมือจัดการกับปัญหานั้นๆ อย่างมีข้อมูล มีหลักการ หรือมีแนวคิดในสายวิชาชีพ โดยความมุ่งมั่นที่จะสร้างอนาคตให้กับหน่วยงาน องค์กร ผู้ประกอบการหรือสังคมไทย'
						  WHERE CP_CODE = '304' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE SET CP_NAME = 'การบริหารจัดการทรัพยากร', CP_ENG_NAME = 'Resource Management- RM', 
		CP_MEANING = 'การตระหนักเสมอถึงความคุ้มค่าระหว่างทรัพยากร (งบประมาณ เวลา กำลังคนเครื่องมือ อุปกรณ์ ฯลฯ) ที่ลงทุนไปหรือที่ใช้การปฏิบัติภารกิจ (Input) กับผลลัพธ์ที่ได้ (Output) และพยายามปรับปรุงหรือลดขั้นตอนการปฏิบัติงาน เพื่อพัฒนาให้การปฏิบัติงานเกิดความคุ้มค่าและมีประสิทธิภาพสูงสุด อาจหมายรวมถึงความสามารถในการจัดความสำคัญในการใช้เวลา ทรัพยากร และข้อมูลอย่างเหมาะสม และประหยัดค่าใช้จ่ายสูงสุด'
						  WHERE CP_CODE = '305' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE SET CP_NAME = 'การพัฒนาศักยภาพคน', CP_ENG_NAME = 'Developing Others- DO', 
		CP_MEANING = 'การส่งเสริม สนับสนุน และการพัฒนาความรู้ความสามารถผู้อื่น โดยมีเจตนามุ่งเน้นพัฒนาศักยภาพของบุคลากรในระยะยาว ทั้งเพื่อประโยชน์ในงานของบุคคลเหล่านั้นและประโยชน์ของกระทรวงฯ'
						  WHERE CP_CODE = '306' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE SET CP_NAME = 'การมองภาพองค์รวม', CP_ENG_NAME = 'Conceptual Thinking- CT', 
		CP_MEANING = 'ความสามารถในการคิดเชิงสังเคราะห์ เห็นแบบแผน และความสัมพันธ์ และระบุประเด็นสำคัญในสถานการณ์ที่อาจเกี่ยวข้อง หรือไม่เกี่ยวข้องกันโดยใช้กรอบความคิด ความคิดสร้างสรรค์ หรือหลักการอุปนัยเป็นที่ตั้ง'
						  WHERE CP_CODE = '307' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE SET CP_NAME = 'การยึดมั่นในหลักเกณฑ์', CP_ENG_NAME = 'Acts with Integrity- AI', 
		CP_MEANING = 'เจตนาที่จะกำกับดูแลให้ผู้อื่นหรือหน่วยงานอื่นปฏิบัติให้ได้ตามมาตรฐาน กฎระเบียบข้อบังคับที่กำหนดไว้ โดยอาศัยอำนาจตามระเบียบ กฎหมาย หรือตามหลักแนวทางในวิชาชีพของตนที่มีอยู่อย่างเหมาะสมและมีประสิทธิภาพโดยมุ่งประโยชน์ของกระทรวง สังคมและประเทศโดยรวมเป็นสำคัญ ความสามารถนี้อาจรวมถึงการยืนหยัดในสิ่งที่ถูกต้องและความเด็ดขาดในการจัดการกับบุคคลหรือหน่วยงานที่ฝ่าฝีนกฏเกณฑ์ ระเบียบหรือมาตรฐานที่ตั้งไว้'
						  WHERE CP_CODE = '308' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE SET CP_NAME = 'การวางแผนล่วงหน้า', CP_ENG_NAME = 'Planning and Organizing- PO', 
		CP_MEANING = 'ความสามารถในการวางแผนอย่างเป็นหลักการ โดยเน้นให้สามารถนำไปปฏิบัติได้จริงและถูกต้อง รวมถึงความสามารถในการบริหารจัดการโครงการต่างๆ ในความรับผิดชอบให้สามารถบรรลุเป้าหมายที่กำหนดไว้อย่างมีประสิทธิภาพสูงสุด'
						  WHERE CP_CODE = '309' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE SET CP_NAME = 'การวิเคราะห์และบูรณาการ', CP_ENG_NAME = 'Synthesis Thinking - ST', 
		CP_MEANING = 'ความสามารถในการคิดวิเคราะห์และทำความเข้าใจในเชิงสังเคราะห์ รวมถึงการมองภาพรวมขององค์กร จนได้เป็นกรอบความคิดหรือแนวคิดใหม่ อันเป็นผลมาจากการสรุปรูปแบบ ประยุกต์แนวทางต่างๆจากสถานการณ์หรือข้อมูลหลากหลาย และนานาทัศนะ'
						  WHERE CP_CODE = '310' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE SET CP_NAME = 'การสร้างสายสัมพันธ์', CP_ENG_NAME = 'Strategic Partnership- SP', 
		CP_MEANING = 'ความสามารถในการรักษาและสร้างเครือข่ายพันธมิตรเชิงกลยุทธ์ (เช่น ผู้ประกอบการ สถาบันการศึกษา เจ้าหน้าที่ภาครัฐอื่นๆ เครือข่ายกลุ่มธุรกิจ กลุ่มที่ปรึกษาหรือผู้เชี่ยวชาญ คู่ค้า ฯลฯ) ที่ยั่งยืนและก่อให้เกิดความร่วมมือในการสรรสร้างประโยชน์สูงสุดร่วมกัน'
						  WHERE CP_CODE = '311' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE SET CP_NAME = 'การสืบเสาะหาข้อมูล', CP_ENG_NAME = 'Information Seeking and Management– ISM', 
		CP_MEANING = 'ความสามารถในการสืบเสาะ เพื่อให้ได้ข้อมูลเฉพาะเจาะจง การไขปมปริศนาโดยซักถามโดยละเอียด หรือแม้แต่การหาข่าวทั่วไปจากสภาพแวดล้อมรอบตัวโดยคาดว่าอาจมีข้อมูลที่จะเป็นประโยชน์ต่อไปในอนาคต และนำข้อมูลที่ได้มานั้นมาประมวลและจัดการอย่างมีระบบ คุณลักษณะนี้อาจรวมถึงความสนใจใคร่รู้เกี่ยวกับสถานการณ์ ภูมิหลัง ประวัติความเป็นมา ประเด็น ปัญหา หรือเรื่องราวต่างๆ ที่เกี่ยวข้องหรือจำเป็นต่องานในหน้าที่'
						  WHERE CP_CODE = '312' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE SET CP_NAME = 'การสื่อสารโน้มน้าวจูงใจ', CP_ENG_NAME = 'Communication & Influencing- CI', 
		CP_MEANING = 'การใช้วาทศิลป์และกลยุทธ์ต่างๆ ในการสื่อสาร เจรจา โน้มน้าวเพื่อให้ผู้อื่นดำเนินการใดๆ ตามที่ตนหรือหน่วยงานประสงค์'
						  WHERE CP_CODE = '313' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE SET CP_NAME = 'การสื่อสารประชาสัมพันธ์และให้ความรู้', CP_ENG_NAME = 'Communication and Education-CD', 
		CP_MEANING = 'ความสามารถในการที่จะรับข่าวสาร คิดวิเคราะห์และถ่ายทอดหรือนำเสนอข้อมูลให้เข้าใจตรงกันและเกิดความรู้สึกที่ดีต่อกัน รวมทั้งความสามารถในการสรุปความ และอธิบายความคิดอย่างชัดเจน มั่นใจ จนทำให้ผู้ได้รับข่าวสารเกิดความเข้าใจและนำไปปฏิบัติได้'
						  WHERE CP_CODE = '314' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE SET CP_NAME = 'ความเข้าใจผู้อื่น', CP_ENG_NAME = 'Interpersonal Understanding- IU', 
		CP_MEANING = 'ความสามารถในการรับฟังและเข้าใจทั้งความหมายตรงและความหมายแฝง ตลอดจนสภาวะอารมณ์ของผู้ที่ติดต่อด้วย โดยผู้ติดต่ออาจเป็นได้ทั้งบุคลากรในกระทรวงฯ ลูกค้า กลุ่มผลประโยชน์ (stakeholders) รัฐบาล หรือองค์กรเอกชนต่างๆ'
						  WHERE CP_CODE = '315' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE SET CP_NAME = 'ความเข้าใจองค์กรและการวางแผน', CP_ENG_NAME = 'Planning with Organization Understanding - POU', 
		CP_MEANING = 'ความสามารถในการวางแผนอย่างเป็นหลักการให้สามารถนำไปปฏิบัติได้จริงและถูกต้อง โดยอาศัยความเข้าใจในเรื่องเทคโนโลยี ระบบ กระบวนการทำงาน และมาตรฐานการทำงานของตนและของหน่วยงานอื่นๆ ที่เกี่ยวข้อง'
						  WHERE CP_CODE = '316' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE SET CP_NAME = 'ความเข้าใจองค์กรและระบบงาน', CP_ENG_NAME = 'Organization and Process Understanding - OPU', 
		CP_MEANING = 'ความเข้าใจและสามารถประยุกต์ใช้ความสัมพันธ์เชื่อมโยงของเทคโนโลยี ระบบ กระบวนการทำงาน และมาตรฐานการทำงานของตนและของหน่วยงานอื่นๆ ที่เกี่ยวข้อง เพื่อประโยชน์ในการปฏิบัติหน้าที่ให้บรรลุผล ความเข้าใจนี้รวมถึงความสามารถในการมองภาพใหญ่ (Big Picture) และการคาดการณ์เพื่อเตรียมการรองรับการเปลี่ยนแปลงของสิ่งต่างๆ ต่อระบบและกระบวนการทำงาน'
						  WHERE CP_CODE = '317' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE SET CP_NAME = 'ความถูกต้องของงาน', CP_ENG_NAME = 'Accuracy and Order- AO', 
		CP_MEANING = 'ความพยายามที่จะปฏิบัติงานให้ถูกต้องครบถ้วนตลอดจนลดข้อบกพร่องที่อาจจะเกิดขึ้น รวมถึงการควบคุมตรวจตราให้งานเป็นไปตามแผนที่วางไว้อย่างถูกต้องชัดเจน'
						  WHERE CP_CODE = '318' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ACTIVE, 
						  UPDATE_USER, UPDATE_DATE)
						  VALUES ('319', 'ความยืดหยุ่นผ่อนปรน', 'Flexibility- FLX', 'ความสามารถในการปรับตัวเข้ากับสถานการณ์และกลุ่มคนที่หลากหลาย ในขณะที่ยังคงปฏิบัติงานได้อย่างมีประสิทธิภาพ หมายความรวมถึงการยอมรับความคิดเห็นของผู้อื่น และปรับเปลี่ยนวิธีการเมื่อสถานการณ์แวดล้อมเปลี่ยนไป', 3, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ACTIVE, 
						  UPDATE_USER, UPDATE_DATE)
						  VALUES ('320', 'ความสามารถในการปรับเปลี่ยนตามสถานการณ์', 'Adaptability- ADP', 'ความสามารถในการรับฟังและเข้าใจบุคคลหรือสถานการณ์ และพร้อมที่จะปรับเปลี่ยนให้สอดคล้องกับสถานการณ์หรือกลุ่มคนที่หลากหลาย ในขณะที่ยังคงปฏิบัติงานได้อย่างมีประสิทธิภาพและบรรลุผลตามเป้าหมายที่ตั้งไว้', 3, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ACTIVE, 
						  UPDATE_USER, UPDATE_DATE)
						  VALUES ('321', 'ความคิดสร้างสรรค์', 'Innovation- INV', 'ความสามารถในการที่จะนำเสนอทางเลือก (Option) หรือแนวทางแก้ปัญหา (Solution) หรือสร้างนวัตกรรม หรือ ริเริ่มสร้างสรรค์กิจกรรมหรือสิ่งใหม่ๆ ที่จะเป็นประโยชน์ต่อกระทรวงฯ', 3, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$code = array(	"101", "102", "103", "104", "105", "201", "202", "203", "204", "205", "206", "301", "302", "303", "304", "305", "306", 
										"307", "308", "309", "310", "311", "312", "313", "314", "315", "316", "317", "318", "319", "320", "321" );
		for ( $i=0; $i<count($code); $i++ ) { 
			$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
							  UPDATE_DATE)
							  VALUES ('$code[$i]', 0, 'ไม่แสดงสมรรถนะด้านนี้อย่างชัดเจน', NULL, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end for
/*
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
เป็นผู้นำในการสร้างสรรสิ่งใหม่ๆ ให้องค์กร และผลักดันให้องค์กรก้าวไปสู่การเปลี่ยนแปลงที่ราบรื่นและประสบความสำเร็จด้วยกลยุทธ์และวิธีดำเนินการที่เหมาะสม
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
ประยุกต์ best practice หรือผลการวิจัยต่างๆ มากำหนดโครงการหรือแผนงานเชิงกลยุทธ์ที่ผลสัมฤทธิ์มีประโยชน์ระยะยาวต่อกระทรวงฯ หรือหน่วยงานที่ตนดูแลรับผิดชอบอยู่', $SESS_USERID, '$UPDATE_DATE') ";
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
สร้างแรงจูงใจให้ผู้สนับสนุนและสร้างการยอมรับจากผู้ท้าทายให้เห็นโทษของการนิ่งเฉยและเห็นถึงประโยชน์ของการเปลี่ยนแปลงจากสภาวการณ์ปัจจุบันและอยากมีส่วนร่วมในการเปลี่ยนนั้น', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('204', 5, 'แสดงสมรรถนะระดับที่ 4 และดำเนินการตามแผนให้เกิดการปรับเปลี่ยนอย่างมีประสิทธิภาพและเหมาะสม', 'เป็นผู้นำในการปรับเปลี่ยนขององค์กร และผลักดันอย่างจริงจังให้การปรับเปลี่ยนดำเนินไปได้อย่างราบรื่นและประสบความสำเร็จสูงสุด
ปลุกขวัญกำลังใจ และสร้างศรัทธาความเชื่อมั่น ในการขับเคลื่อนให้เกิดการปรับเปลี่ยน/เปลี่ยนแปลงอย่างมีประสิทธิภาพสูงสุดแก่องค์กรและภาครัฐโดยรวม', $SESS_USERID, '$UPDATE_DATE') ";
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
						  VALUES ('205', 5, 'แสดงสมรรถนะระดับที่ 4 และเอาชนะอารมณ์ด้วยความเข้าใจ', 'ละวางอารมณ์รุนแรงทั้งปวง โดยการพยายามทำความเข้าใจต้นเหตุ  เข้าใจตนเอง เข้าใจสถานการณ์ และเข้าใจคู่กรณี ตลอดจนบริบทและปัจจัยแวดล้อมต่างๆ อาจให้อภัยหรือปล่อยวางได้ตามแต่กรณี
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
						  VALUES ('206', 4, 'แสดงสมรรถนะระดับที่ 3 และช่วยขจัดข้อจำกัดของผู้ใต้บังคับบัญชาเพื่อพัฒนาศักยภาพ', 'กล้าที่จะให้คำปรึกษาที่สะท้อนผลงานและศักยภาพที่แท้จริงของผู้อื่น และสามารถระบุความจำเป็นหรือความต้องการในการฝึกอบรมหรือพัฒนาที่เป็นประโยชน์ต่องาน และขจัดข้อจำกัดของบุคคลผู้นั้น
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
*/
		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'การตอบสนองได้อย่างรวดเร็ว และเด็ดเดี่ยวในเหตุวิกฤติ หรือสถานการณ์จำเป็น', 
						  CL_MEANING = 'ตอบสนองอย่างรวดเร็ว และเด็ดเดี่ยวเมื่อมีเหตุวิกฤติหรือในสถานการณ์ที่จำเป็นเพื่อให้ทันต่อความเร่งด่วนของสถานการณ์นั้นๆ'
						  WHERE CP_CODE = '301' AND CL_NO = 1 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'แสดงสมรรถนะระดับที่ 1 และตระหนักถึงปัญหาหรือโอกาสและลงมือกระทำการโดยไม่รีรอ', 
						  CL_MEANING = 'ตระหนักถึงปัญหาหรือโอกาสในขณะนั้นและลงมือกระทำการโดยไม่รีรอให้สถานการณ์คลี่คลายไปเอง หรือปล่อยโอกาสหลุดลอยไป อีกทั้งรู้จักพลิกแพลงวิธีการ กระบวนการต่างๆ เพื่อให้สามารถแก้ไขปัญหา หรือใช้ประโยชน์จากโอกาสนั้นได้อย่างมีประสิทธิภาพ'
						  WHERE CP_CODE = '301' AND CL_NO = 2 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'แสดงสมรรถนะระดับที่ 2 และเล็งเห็นโอกาสหรือปัญหาที่อาจเกิดขึ้นได้ในระยะใกล้ (ประมาณ 1-3 เดือนข้างหน้า)', 
						  CL_MEANING = 'คาดการณ์และเล็งเห็นปัญหาหรือโอกาสที่อาจเกิดขึ้นในระยะเวลา 1-3 เดือนถัดจากปัจจุบัน และลงมือกระทำการล่วงหน้าเพื่อป้องกันปัญหา หรือสร้างโอกาสในสถานการณ์นั้นๆ อีกทั้งเปิดกว้างรับฟังแนวทางและความคิดหลากหลายอันอาจเป็นประโยชน์ต่อการป้องกันปัญหา'
						  WHERE CP_CODE = '301' AND CL_NO = 3 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'แสดงสมรรถนะระดับที่ 3 และเล็งเห็นโอกาสหรือปัญหาที่อาจเกิดขึ้นได้ในระยะกลาง (ประมาณ 4-12 เดือนข้างหน้า)', 
						  CL_MEANING = 'คาดการณ์และเล็งเห็นปัญหาหรือโอกาสที่อาจเกิดขึ้นในระยะเวลา 4-12 เดือนถัดจากปัจจุบัน และเตรียมการล่วงหน้าเพื่อป้องกันปัญหา หรือสร้างโอกาสในสถานการณ์นั้นๆ ตลอดจนทดลองและเสาะหาวิธีการ แนวคิดใหม่ๆ ที่อาจเป็นประโยชน์ในการป้องกันปัญหาและสร้างโอกาสในอนาคต'
						  WHERE CP_CODE = '301' AND CL_NO = 4 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'แสดงสมรรถนะระดับที่ 4 และเตรียมการล่วงหน้าเพื่อป้องกันปัญหาและสร้างโอกาสในระยะยาว', 
						  CL_MEANING = 'คาดการณ์และเล็งเห็นปัญหาหรือโอกาสที่อาจเกิดขึ้นในระยะยาวและเตรียมการล่วงหน้าเพื่อป้องกันปัญหา หรือสร้างโอกาส อีกทั้งกระตุ้นให้ผู้อื่นเกิดความกระตือรือร้นต่อการป้องกันและแก้ไขปัญหาเพื่อสร้างโอกาสให้องค์กรในระยะยาว'
						  WHERE CP_CODE = '301' AND CL_NO = 5 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'เข้าใจสภาพแวดล้อมทั่วไปของอุตสาหกรรมและธุรกิจในพื้นที่', 
						  CL_MEANING = 'มีความเข้าใจสภาพแวดล้อมทั่วไปของอุตสาหกรรมและธุรกิจของผู้ประกอบการในพื้นที่ เช่น ลักษณะสินค้า เทคโนโลยี ผลิตภัณฑ์ และการบริการ เป็นต้น'
						  WHERE CP_CODE = '302' AND CL_NO = 1 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'แสดงสมรรถนะระดับที่ 1และเข้าใจสาเหตุ และปัจจัยที่จะนำไปสู่ความสำเร็จต่ออุตสาหกรรมและธุรกิจในพื้นที่', 
						  CL_MEANING = 'มีความเข้าใจถึงสาเหตุ ปัจจัย แนวทาง วิธีการต่างๆ ที่มีผลกระทบทำให้ผู้ประกอบการในพื้นที่ประสบความสำเร็จ
มีความเข้าใจเศรษฐกิจ สังคม และตลาด ณ  ปัจจุบันของพื้นที่ ทั้งในเชิงโอกาส และความท้าทายที่อาจมีผลกระทบต่อธุรกิจของผู้ประกอบการ และสามารถติดตามการเปลี่ยนแปลงทางเทคโนโลยี แนวโน้ม หรือการแข่งขันนั้นๆ ที่มีผลต่อผู้ประกอบการได้'
						  WHERE CP_CODE = '302' AND CL_NO = 2 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'แสดงสมรรถนะระดับที่ 2 และสามารถปรับความเข้าใจอุตสาหกรรมในพื้นที่มาปรับปรุงเพื่อสร้างประโยชน์ให้กับผู้ประกอบการรายย่อย', 
						  CL_MEANING = 'ปรับความรู้ และความเข้าใจด้านอุตสาหกรรม เศรษฐกิจหรือองค์ความรู้และเทคโนโลยีใหม่ๆ มาปรับปรุงวิธีการ กระบวนการทำงาน และขั้นตอนการปฏิบัติงานของผู้ประกอบการในพื้นที่ได้อย่างมีประสิทธิภาพ
สามารถพัฒนา หรือออกแบบแผนงานธุรกิจที่ส่งเสริมการพัฒนาผลิตภัณฑ์ และการบริการของผู้ประกอบการให้มีคุณภาพ และประสิทธิภาพมากขึ้น'
						  WHERE CP_CODE = '302' AND CL_NO = 3 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'แสดงสมรรถนะระดับที่ 3 และสามารถปรับความเข้าใจทางอุตสาหกรรมมาสร้างประโยชน์ให้กับจังหวัดในพื้นที่ที่รับผิดชอบ', 
						  CL_MEANING = 'สามารถควบคุม บริหารจัดการทรัพยากรต่างๆ ภายในอุตสาหกรรมจังหวัดในความรับผิดชอบของตนโดยอยู่บนพื้นฐานของความเข้าใจในเชิงธุรกิจ อุตสาหกรรม เศรษฐศาสตร์ สังคม ฯลฯ เพื่อสร้างรายได้ หรือประโยชน์ต่างๆ ให้กับผู้ประกอบการและประชาชนโดยรวมในจังหวัด 
สามารถระบุข้อบกพร่อง วิเคราะห์ข้อดี ข้อเสียของกระบวนการการทำงานที่อาจทำให้เกิดผลกระทบต่อการลงทุนหรือการประกอบอุตสาหกรรมในพื้นที่ และเสนอแนะวิธีการและผลักดันให้เกิดการพัฒนาอุตสาหกรรมอย่างเป็นรูปธรรมเพื่อประโยชน์โดยรวมให้แก่จังหวัดที่รับผิดชอบ'
						  WHERE CP_CODE = '302' AND CL_NO = 4 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'แสดงสมรรถนะระดับที่ 4 และกระตุ้น และปลูกจิตสำนึกเรื่องการพัฒนาอุตสาหกรรมทั้งประเทศ', 
						  CL_MEANING = 'มีบทบาทสำคัญในการปรับปรุงและกระตุ้นให้ทุกหน่วยงานตื่นตัว ลงมือดำเนินการด้านการพัฒนาอุตสาหกรรม และ/หรือฝึกฝนพัฒนาบุคลากรของตนในเชิงพัฒนาอุตสาหกรรม การลงทุน การคำนวณความคุ้มค่าด้านการลงทุน การใช้ทรัพยากรเงิน แรงงาน เวลา ฯลฯ ให้คุ้มค่าและเกิดประโยชน์อย่างสูงสุด เพื่อให้ประเทศสามารถแข่งขันกับนานาประเทศได้ดียิ่งขึ้น บริการลูกค้าได้ดียิ่งขึ้น ลดค่าใช้จ่ายจำนวนมาก และสร้างผลกำไรได้เพิ่มมากขึ้น ฯลฯ'
						  WHERE CP_CODE = '302' AND CL_NO = 5 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'แตกและแยกแยะปัญหา แนวคิด ประเด็น สถานการณ์ หลักการ ทฤษฎี ฯลฯ ออกเป็นประเด็นย่อยๆ', 
						  CL_MEANING = 'แยกแยะหรือแตกปัญหา แนวคิด ประเด็น สถานการณ์ หลักการ ทฤษฎี ฯลฯ ออกเป็นประเด็นย่อยๆ โดยยังไม่คำนึงถึงลำดับความสำคัญ
จัดทำและระบุรายการหรือปัญหา แนวคิด ประเด็น สถานการณ์ หลักการ ทฤษฎีต่างๆ เป็นข้อๆ แต่อาจยังไม่ได้จัดลำดับก่อนหลัง'
						  WHERE CP_CODE = '303' AND CL_NO = 1 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'แสดงสมรรถนะระดับที่ 1 และจัดลำดับความสำคัญของประเด็น ปัญหา แนวคิด ประเด็น สถานการณ์ หลักการ ทฤษฎี ฯลฯ ได้', 
						  CL_MEANING = 'แยกแยะหรือแตกปัญหา แนวคิด ประเด็น สถานการณ์ หลักการ ทฤษฎี ฯลฯ ออกเป็นประเด็นย่อยๆ และจัดเรียงงาน กิจกรรมต่างๆ ตามลำดับความสำคัญก่อนหลังเพื่อประโยชน์ในการดำเนินการต่อไปตามความเร่งด่วนหรือความจำเป็น
เข้าใจและระบุขั้นตอน ลำดับก่อนหลังของประเด็นต่างๆ ได้ ตั้งข้อสังเกต ระบุข้อบกพร่องของขั้นตอนงานได้อันเป็นผลจากความเข้าใจในลำดับความสำคัญหรือลำดับก่อนหลังของสิ่งต่างๆ'
						  WHERE CP_CODE = '303' AND CL_NO = 2 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'แสดงวามสามารถระดับที่ 2 และเข้าใจและเชื่อมโยงความสัมพันธ์เบื้องต้นของปัญหา แนวคิด ประเด็น สถานการณ์ หลักการ ทฤษฎี ฯลฯ ได้', 
						  CL_MEANING = 'เชื่อมโยงความสัมพันธ์อย่างง่ายๆ ระหว่างเหตุและผลที่ก่อให้เกิดเป็นปัญหาได้
ระบุได้ว่าอะไรเป็นเหตุเป็นผลแก่กันในสถานการณ์หนึ่งๆ หรือแยกแยะข้อดีข้อเสียของประเด็นต่างๆ ได้
อธิบายเหตุผลความเป็นมา แยกแยะข้อดี และข้อเสียของปัญหา สถานการณ์ ฯลฯ เป็นประเด็นต่างๆ ได้อย่างมีเหตุมีผล'
						  WHERE CP_CODE = '303' AND CL_NO = 3 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'แสดงสมรรถนะระดับที่ 3 และเข้าใจและเชื่อมโยงความสัมพันธ์ที่ซับซ้อนของปัญหา แนวคิด ประเด็น สถานการณ์ หลักการ ทฤษฎี ฯลฯ ได้', 
						  CL_MEANING = 'แยกแยะและเชื่อมโยงประเด็น ปัญหา หรือปัจจัยต่างๆ ที่ซับซ้อนได้ในหลายๆ แง่มุม เช่น เหตุ ก. นำไปสู่ เหตุ ข. เหตุ ข. นำไปสู่เหตุ ค. และนำไปสู่เหตุ ค. ฯลฯ
แยกแยะองค์ประกอบต่างๆ ของประเด็น ปัญหาที่มีเหตุปัจจัยเชื่อมโยงซับซ้อนเป็นรายละเอียดในชั้นต่างๆ อีกทั้งวิเคราะห์ว่าแง่มุมต่างๆ ของปัญหาหรือสถานการณ์หนึ่งๆสัมพันธ์กันอย่างไร คาดการณ์ว่าจะมีโอกาส หรืออุปสรรคอะไรบ้าง'
						  WHERE CP_CODE = '303' AND CL_NO = 4 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'แสดงสมรรถนะระดับที่ 4 และใช้เทคนิคและความรู้เฉพาะด้านในการคิดวิเคราะห์', 
						  CL_MEANING = 'ประยุกต์ใช้ความรู้ ความเชี่ยวชาญ เทคนิคเฉพาะด้าน เช่น หลักสถิติขั้นสูง ความเชี่ยวชาญเฉพาะสาขาที่เกี่ยวข้องกับผลิตภัณฑ์หรือบริการมาวิเคราะห์ประเด็น หรือปัญหาต่างๆ ในงานอันทำให้ได้ข้อสรุปหรือคำตอบที่ไม่อาจบรรลุได้ด้วยวิธีปรกติธรรมดาทั่วไป
วิเคราะห์ปัญหาในแง่มุมที่ลึกซึ้งถึงปรัชญาแนวคิดเบื้องหลังของประเด็นหรือทางเลือกต่างๆ ที่ซับซ้อนเหล่านั้น'
						  WHERE CP_CODE = '303' AND CL_NO = 5 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'ติดตามหาความรู้และแนวคิดใหม่ๆ ในสายวิชาชีพ เพื่อใช้ในการวิเคราะห์และแก้ไขปัญหาระยะสั้นที่เกิดขึ้น', 
						  CL_MEANING = 'กระตือรือร้นในการศึกษาหาความรู้หรือเทคโนโลยีใหม่ๆ ในสาขาของตนหรือในงานของสำนัก เพื่อนำมาใช้ให้เกิดประโยชน์ในการแก้ไขปัญหาที่เกิดขึ้น
ใช้ความรู้หรือประสบการณ์ในการลงมือแก้ไขเมื่อเล็งเห็นปัญหาหรืออุปสรรคโดยไม่รอช้า'
						  WHERE CP_CODE = '304' AND CL_NO = 1 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'แสดงสมรรถนะระดับที่ 1 และวิเคราะห์และตัดสินในอย่างมีข้อมูลและเหตุผลในการจัดการปัญหาที่เกิดขึ้น', 
						  CL_MEANING = 'วิเคราะห์ข้อมูล และหาเหตุผลตามแนวคิดในวิชาชีพ เพื่อตัดสินใจดำเนินการแก้ไขปัญหาที่เกิดขึ้นอย่างมีประสิทธิภาพสูงสุด
พลิกแพลงหรือประยุกต์แนวทางในการแก้ปัญหา โดยอ้างอิงจากข้อมูลและแนวคิดในสายวิชาชีพหรือประสบการณ์ในการทำงานในพื้นที่'
						  WHERE CP_CODE = '304' AND CL_NO = 2 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'แสดงสมรรถนะระดับที่ 2 และวิเคราะห์ปัญหาที่ผ่านมา และวางแผนล่วงหน้าอย่างเป็นระบบ เพื่อป้องกันหรือหลีกเลี่ยงปัญหา', 
						  CL_MEANING = 'วิเคราะห์ข้อมูล วางแผนและคาดการณ์ผลกระทบที่จะเกิดขึ้นอบ่างเป็นระบบ เพื่อป้องกันและหลีกเลี่ยงปัญหาที่อาจเกิดขึ้น
วางแผนและทดลองใช้วิธีการหรือองค์ความรู้หรือเทคโนโลยีใหม่ๆ ในการป้องกัน หลีกเลี่ยงหรือแก้ไขปัญหาให้เกิดขึ้นในหน่วยงานหรือองค์กร'
						  WHERE CP_CODE = '304' AND CL_NO = 3 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'แสดงสมรรถนะระดับที่ 3 และผสมผสานแนวคิดในเชิงสหวิทยาการเพื่อหลีกเลี่ยง ป้องกันหรือแก้ไขปัญหาทั้งในระยะสั้นและระยะยาว', 
						  CL_MEANING = 'วิเคราะห์ และผสมผสานศาสตร์หลายๆ แขนง เพื่อแก้ไขปัญหาซึ่งมีความซับซ้อนในระยะสั้นและเตรียมการป้องกันหรือหลีกเลี่ยงปัญหาในระยะยาวได้
คิดนอกกรอบ ริเริ่มโครงการหรือกระบวนการทำงานต่างๆ ในลักษณะบูรณาการหลายหน่วยงาน/หลายวิชาชีพ เพื่อแก้ไขปัญหาที่คาดว่าจะเกิดขึ้นในอนาคต'
						  WHERE CP_CODE = '304' AND CL_NO = 4 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'แสดงสมรรถนะระดับที่ 4 และปรับเปลี่ยนหรือสร้างความเชี่ยวชาญในสายอาชีพ/สหวิทยาการ เพื่อแก้ไขและหลีกเลี่ยงปัญหาอย่างยั่งยืน', 
						  CL_MEANING = 'ปรับเปลี่ยน (Reshape) องค์กรให้มีการบูรณาการในเชิงวิชาชีพ หรือให้มีความเชี่ยวชาญในสายอาชีพ เพื่อให้สามารถแก้ไข ป้องกันและหลีกเลี่ยงปัญหาที่มีผลกระทบสูงหรือมีความซับซ้อนสูงได้อย่างยั่งยืน
สร้างบรรยากาศของการคิดริเริ่มหรือข้อเสนอใหม่ๆ ให้เกิดขึ้นในหน่วยงาน เพื่อแก้ปัญหาหรือสร้างโอกาสในระยะยาว'
						  WHERE CP_CODE = '304' AND CL_NO = 5 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'ปฏิบัติงานโดยคำนึงถึงความคุ้มค่าและค่าใช้จ่ายที่เกิดขึ้น', 
						  CL_MEANING = 'ตระหนักถึงความคุ้มค่าและค่าใช้จ่ายต่างๆ ที่จะเกิดขึ้นในการปฏิบัติงาน
ปฏิบัติงานตามกระบวนการขั้นตอนที่กำหนดไว้ เพื่อให้สามารถใช้ทรัพยากรไม่เกินขอบเขตที่กำหนด'
						  WHERE CP_CODE = '305' AND CL_NO = 1 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'แสดงสมรรถนะระดับที่ 1 และปฏิบัติงานโดยคำนึงถึงค่าใช้จ่ายที่เกิดขึ้น และมีความพยายามที่จะลดค่าใช้จ่ายเบื้องต้น', 
						  CL_MEANING = 'ตระหนักและควบคุมค่าใช้จ่ายที่เกิดขึ้นในการปฏิบัติงานโดยมีความพยายามที่จะลดค่าใช้จ่ายต่างๆ ที่จะเกิดขึ้น
จัดสรรงบประมาณ ค่าใช้จ่าย ทรัพยากรที่มีอยู่อย่างจำกัดให้คุ้มค่าและเกิดประโยชน์ในการปฏิบัติงานอย่างสูงสุด'
						  WHERE CP_CODE = '305' AND CL_NO = 2 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'แสดงสมรรถนะระดับที่ 2 และกำหนดการใช้ทรัพยากรให้สัมพันธ์กับผลลัพธ์ที่ต้องการ', 
						  CL_MEANING = 'ประเมินผลความมีประสิทธิภาพของการดำเนินงานที่ผ่านมาเพื่อปรับปรุงการจัดสรรทรัพยากรให้ได้ผลผลิตที่เพิ่มขึ้น หรือมีการทำงานที่มีประสิทธิภาพมากขึ้น หรือมีค่าใช้จ่ายที่ลดลง
ระบุข้อบกพร่อง วิเคราะห์ข้อดี ข้อเสียของกระบวนการการทำงานและกำหนดการใช้ทรัพยากรที่สัมพันธ์กับผลลัพธ์ที่ต้องการโดยมองผลประโยชน์ของกระทรวงฯ เป็นหลัก'
						  WHERE CP_CODE = '305' AND CL_NO = 3 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'แสดงสมรรถนะระดับที่ 3 และเชื่อมโยงหรือประสานการบริหารทรัพยากรร่วมกันระหว่างหน่วยงานเพื่อให้เกิดการใช้ทรัพยากรที่คุ้มค่าสูงสุด', 
						  CL_MEANING = 'เลือกปรับปรุงกระบวนการทำงานที่เกิดประสิทธิภาพสูงสุดกับหลายหน่วยงาน และไม่กระทบกระบวนการทำงานต่างๆ ภายในกระทรวงฯ
วางแผนและเชื่อมโยงภารกิจของหน่วยงานตนเองกับหน่วยงานอื่น (Synergy) เพื่อให้การใช้ทรัพยากรของหน่วยงานที่เกี่ยวข้องทั้งหมดเกิดประโยชน์สูงสุด
กำหนดและ/หรือสื่อสารกระบวนการการบริหารทรัพยากรที่สอดคล้องกันทั่วทั้งองค์กร เพื่อเพิ่มขีดความสามารถของกระทรวงฯ'
						  WHERE CP_CODE = '305' AND CL_NO = 4 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'แสดงสมรรถนะระดับที่ 4 และเสนอกระบวนการใหม่ๆ ในการทำงานให้มีประสิทธิภาพยิ่งขึ้นเพื่อให้เกิดการพัฒนาที่ยั่งยืน', 
						  CL_MEANING = 'พัฒนากระบวนการใหม่ๆ โดยอาศัยวิสัยทัศน์ ความเชี่ยวชาญ และประสบการณ์ต่างๆ มาประยุกต์ในกระบวนการทำงาน เพื่อลดภาระการบริหารงานให้สามารถดำเนินงานได้อย่างมีประสิทธิภาพสูงสุด
สามารถเพิ่มผลผลิตหรือสร้างสรรค์งานใหม่ ที่โดดเด่นแตกต่างให้กับหน่วยงานหรือกระทรวงฯ โดยใช้ทรัพยากรเท่าเดิม'
						  WHERE CP_CODE = '305' AND CL_NO = 5 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'เชื่อมั่นว่าผู้อื่นสามารถพัฒนาความรู้ ความสามารถได้', 
						  CL_MEANING = 'แสดงความเชื่อมั่นว่าผู้อื่นสามารถจะเรียนรู้ ปรับปรุงผลงาน และพัฒนาศักยภาพตนเองได้'
						  WHERE CP_CODE = '306' AND CL_NO = 1 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'แสดงสมรรถนะระดับที่ 1 และสอนงานหรือให้คำแนะนำเกี่ยวกับวิธีการปฏิบัติงาน', 
						  CL_MEANING = 'สามารถสอนงานในรายละเอียด หรือให้คำแนะนำที่เฉพาะเจาะจงที่เกี่ยวกับวิธีการปฏิบัติงานโดยมุ่งพัฒนาขีดความสามารถของบุคคลนั้น'
						  WHERE CP_CODE = '306' AND CL_NO = 2 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'แสดงสมรรถนะระดับที่ 2 และสามารถให้เหตุผลประกอบการสอนและคำแนะนำ และให้ความสนันสนุนในด้านต่างๆ เพื่อให้ปฏิบัติงานได้ง่ายขึ้น', 
						  CL_MEANING = 'ให้แนวทางที่เป็นประโยขน์ หรือสาธิตวิธีปฏิบัติงานเพื่อเป็นตัวอย่างในการปฏิบัติงานจริง พร้อมทั้งอธิบายเหตุผลประกอบการสอนและการพัฒนาบุคลากร
ให้การสนับสนุนและช่วยเหลือผู้อื่นเพื่อให้งานง่ายขึ้น โดยการสนับสนุนด้านทรัพยากร อุปกรณ์ ข้อมูล หรือให้คำแนะนำในฐานะที่เป็นผู้เชี่ยวชาญในงานนั้นๆ'
						  WHERE CP_CODE = '306' AND CL_NO = 3 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'แสดงสมรรถนะระดับที่ 3 และให้คำติชมเรื่องผลงานอย่างตรงไปตรงมาเพื่อการพัฒนาที่ต่อเนื่อง', 
						  CL_MEANING = 'ติชมผลการปฏิบัติงานอย่างตรงไปตรงมาทั้งด้านบวกและด้านลบโดยปราศจากอคติส่วนตัว เพื่อส่งเสริมให้มีการพัฒนาความรู้ ความสามารถและปรับปรุงผลงานอย่างต่อเนื่อง
แสดงความคาดหวังในด้านบวกว่าบุคคลนั้นๆ จะสามารถพัฒนาตนเองให้ดีขึ้นได้ และให้คำแนะนำที่เฉพาะเจาะจง สอดคล้องกับบุคลิก ความสนใจ และความสามารถเฉพาะบุคคล เพื่อปรับปรุงพัฒนาความรู้และความสามารถได้อย่างเหมาะสม'
						  WHERE CP_CODE = '306' AND CL_NO = 4 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'แสดงสมรรถนะระดับที่ 4 และการพัฒนาศักยภาพบุคลากรในระยะยาวเพื่อเพิ่มผลงานที่มีประสิทธิภาพต่อสำนักงานปลัดกระทรวง', 
						  CL_MEANING = 'มอบหมายงานที่เหมาะสม มีประโยชน์ และท้าทายความสามารถ มองหาโอกาสในการพัฒนาขีดความสามารถและประสบการณ์อื่นๆ เพื่อสนับสนุนให้บุคลากรสามารถเรียนรู้และพัฒนาความสามารถได้อย่างต่อเนื่อง
รณรงค์ ส่งเสริม และผลักดันให้มีแผนหรือโครงการพัฒนาความรู้ความสามารถของบุคลากรอย่างเป็นรูปธรรม เพื่อสร้างวัฒนธรรมองค์กรให้มีการส่งเสริมการเรียนรู้อย่างเป็นระบบ'
						  WHERE CP_CODE = '306' AND CL_NO = 5 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'ใช้กฏเกณฑ์ขั้นพื้นฐานในการพิจารณาสถานการณ์', 
						  CL_MEANING = 'ใช้กฎเกณฑ์ขั้นพื้นฐาน สามัญสำนึก หรือประสบการณ์ในการระบุปัญหา เมื่อเหตุการณ์ที่ประสบนั้นมีลักษณะเหมือนกับที่เคยประสบมา'
						  WHERE CP_CODE = '307' AND CL_NO = 1 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'แสดงสมรรถนะระดับที่ 1 และประยุกต์ใช้ประสบการณ์ในการระบุประเด็น และพิจารณาสถานการณ์', 
						  CL_MEANING = 'สามารถระบุปัญหาในสถานการณ์ปัจจุบันที่อาจมีความคล้ายคลึง หรือต่างจากประสบการณ์ที่เคยประสบมาโดยสามารถระบุความเหมือน และความแตกต่างนั้นๆ ได้โดยการพิจารณาข้อมูล การมองเห็นแบบแผน แนวโน้ม หรือระบุสิ่งที่ขาดหายไปในสถานการณ์นั้นๆ ได้'
						  WHERE CP_CODE = '307' AND CL_NO = 2 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'แสดงสมรรถนะระดับที่ 2 และประยุกต์ใช้ความรู้ทางทฤษฎี และประสบการณ์ในการพิจารณาสถานการณ์', 
						  CL_MEANING = 'สามารถนำความรู้ในทางทฤษฎี และประสบการณ์หลากหลายในการพิจารณาสถานการณ์ปัจจุบัน
สามารถปรับปรุง หรือเปลี่ยนแบบแผน หรือวิธีการที่ได้เรียนรู้มา และปรับใช้ในสถานการณ์ได้อย่างเหมาะสม'
						  WHERE CP_CODE = '307' AND CL_NO = 3 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'แสดงสมรรถนะระดับที่ 3 และมองเห็นสถานการณ์ที่ซับซ้อนได้อย่างทะลุปรุโปร่ง', 
						  CL_MEANING = 'มองเห็นสถานการณ์ซับซ้อนได้อย่างทะลุปรุโปร่ง และสามารถอธิบายให้เป็นที่เข้าใจได้
สามารถประกอบความคิด ประเด็น และการสังเกตการณ์ในการอธิบายสถานการณ์ที่ยากให้สามารถเป็นที่เข้าใจได้'
						  WHERE CP_CODE = '307' AND CL_NO = 4 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'แสดงสมรรถนะระดับที่ 4 และค้นพบกรอบความคิดใหม่ หรือการคิดนอกกรอบ', 
						  CL_MEANING = 'คิดนอกกรอบ การสร้างกรอบความคิดใหม่ๆ ที่อาจไม่เป็นที่รู้จักของผู้อื่น และไม่ได้มาจากการเรียนหรือประสบการณ์มาใช้ในการอธิบายสถานการณ์ หรือคลี่คลายปัญหา
พิจารณาสิ่งต่างๆ ในงานด้วยมุมมองที่แตกต่าง อันนำไปสู่การประดิษฐ์คิดค้น การสร้างสรรค์และนำเสนอรูปแบบ วิธี ตลอดจนองค์ความรู้ใหม่ที่ไม่เคยปรากฎมาก่อนและเป็นประโยชน์ต่อกระทรวงฯ หรือสังคมและประเทศชาติโดยรวม'
						  WHERE CP_CODE = '307' AND CL_NO = 5 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'กระทำสิ่งต่างๆ ตามมาตรฐาน หรือตามกฎระเบียบข้อบังคับที่กำหนดไว้', 
						  CL_MEANING = 'ปฏิบัติหน้าที่ด้วยความโปร่งใส  ถูกต้องทั้งตามหลักกฎหมายระเบียบข้อบังคับที่กำหนดไว้
ยึดถือหลักการและแนวทางตามหลักวิชาชีพอย่างสม่ำเสมอ
เปิดเผยข้อมูลหรือเหตุผลอย่างตรงไปตรงมา'
						  WHERE CP_CODE = '308' AND CL_NO = 1 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'แสดงสมรรถนะระดับที่ 1 และยึดมั่นในแนวทางหรือขอบเขตข้อจำกัดในการกระทำสิ่งต่างๆ', 
						  CL_MEANING = 'ปฏิเสธข้อเรียกร้องของผู้อื่นหรือหน่วยงานที่เกี่ยวข้อง ที่ขาดเหตุผลหรือผิดกฎระเบียบหรือแนวทางนโยบายที่วางไว้
ดำเนินการอย่างไม่บิดเบือน โดยไม่อ้างข้อยกเว้นให้ตนเองหรือผู้ใต้บังคับบัญชาหรือคนรู้จักหรือหน่วยงานภายใต้การดูแลหากมีการดำเนินงานที่ยอมรับไม่ได้'
						  WHERE CP_CODE = '308' AND CL_NO = 2 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'แสดงสมรรถนะระดับที่ 2 และติดตามควบคุมให้ปฏิบัติตามมาตรฐานหรือตามกฎหมายข้อบังคับ', 
						  CL_MEANING = 'หมั่นควบคุมตรวจตราการดำเนินการของหน่วยงานที่ดูแลรับผิดชอบให้เป็นไปอย่างถูกต้องตามกฎระเบียบหรือแนวทางนโยบายที่วางไว้
ออกคำเตือนหรือพยายามประนีประนอมอย่างชัดแจ้งว่าจะเกิดอะไรขึ้นหากผลงานไม่ได้มาตรฐานที่กำหนดไว้หรือกระทำการละเมิดกฎระเบียบหรือแนวทางนโยบายที่วางไว้'
						  WHERE CP_CODE = '308' AND CL_NO = 3 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'แสดงสมรรถนะระดับที่ 3 และรับผิดชอบในสิ่งที่อยู่ในการดูแล', 
						  CL_MEANING = 'กล้าตัดสินใจในหน้าที่ โดยสั่ง ต่อรองหรือประนีประนอมให้บุคคลหรือหน่วยงานที่ฝ่าฝืนกฎเกณฑ์ ระเบียบ นโยบายหรือมาตรฐานที่ตั้งไว้ไปปรับปรุงผลงานในเชิงปริมาณหรือคุณภาพให้เข้าเกณฑ์มาตรฐาน  แม้ว่าผลของการตัดสินใจอาจสร้างศัตรูหรือก่อความไม่พึงพอใจให้แก่ผู้ที่เกี่ยวข้องหรือเสียประโยชน์
กล้ายอมรับความผิดพลาดและจัดการความความผิดพลาดที่จัดทำลงไป'
						  WHERE CP_CODE = '308' AND CL_NO = 4 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'แสดงสมรรถนะระดับที่ 4 และจัดการกับผลงานไม่ดีหรือสิ่งผิดกฎระเบียบอย่างเด็ดขาดตรงไปตรงมา', 
						  CL_MEANING = 'ใช้วิธีเผชิญหน้าอย่างเปิดเผยตรงไปตรงมาเมื่อผู้อื่นหรือหน่วยงานภายใต้การกำกับดูแล มีปัญหาผลงานไม่ดีหรือทำผิดกฎระเบียบอย่างร้ายแรง
ยืนหยัดพิทักษ์ผลประโยชน์ตามกฎเกณฑ์ของกระทรวงฯ แม้ในสถานการณ์ที่อาจเสี่ยงต่อความมั่นคงในตำแหน่งหน้าที่การงาน หรืออาจเสียงภัยต่อชีวิต'
						  WHERE CP_CODE = '308' AND CL_NO = 5 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'วางแผนงานออกเป็นส่วนย่อยๆ', 
						  CL_MEANING = 'วางแผนงานเป็นขั้นตอนอย่างชัดเจน มีผลลัพธ์ สิ่งที่ต้องจัดเตรียม และกิจกรรมต่างๆ ที่จะเกิดขึ้นอย่างถูกต้อง'
						  WHERE CP_CODE = '309' AND CL_NO = 1 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'แสดงสมรรถนะระดับที่ 1 และเห็นลำดับความสำคัญหรือความเร่งด่วนของงาน', 
						  CL_MEANING = 'วางแผนงานได้โดยจัดเรียงงาน หรือกิจกรรมต่างๆ ตามลำดับความสำคัญหรือความเร่งด่วน 
จัดลำดับของงานและผลลัพธ์ในโครงการเพื่อให้สามารถจัดการโครงการให้บรรลุตามแผนและเวลาที่วางไว้ได้ 
วิเคราะห์หาข้อดี ข้อเสียและผลต่อเนื่องของแผนงานที่วาง เพื่อสามารถวางแผนงานใหม่ได้อย่างมีประสิทธิภาพมากขึ้น'
						  WHERE CP_CODE = '309' AND CL_NO = 2 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'แสดงสมรรถนะระดับที่ 2 และวางแผนหรือเชื่อมโยงงานหรือกิจกรรมต่างๆ ที่มีความซับซ้อนเพื่อให้บรรลุตามแผนที่กำหนดไว้ได้', 
						  CL_MEANING = 'วางแผนงานโดยกำหนดกิจกรรม ขั้นตอนการดำเนินงานต่างๆ ที่มีผู้เกี่ยวข้องหลายฝ่ายได้อย่างมีประสิทธิภาพ
วางแผนงานที่มีความเชื่อมโยงหรือซับซ้อนกันหลายๆ งานหรือหลายๆ โครงการโดยกำหนดกิจกรรม ขั้นตอนการดำเนินงานต่างๆ ที่สนับสนุนและไม่ขัดแย้งกันกันได้อย่างมีประสิทธิภาพ'
						  WHERE CP_CODE = '309' AND CL_NO = 3 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'แสดงสมรรถนะระดับที่ 3 และสามารถคาดการณ์ล่วงหน้าเกี่ยวกับปัญหา/งานและเตรียมทางเลือกสำหรับการป้องกัน/แก้ไขปัญหาที่เกิดขึ้น', 
						  CL_MEANING = 'วางแผนงานที่ซับซ้อนโดยกำหนดกิจกรรม ขั้นตอนการดำเนินงานต่างๆ ที่มีหน่วยงานหรือผู้เกี่ยวข้องหลายฝ่าย รวมถึงคาดการณ์ปัญหา อุปสรรค และวางแนวทางการป้องกันแก้ไขไว้ล่วงหน้า อีกทั้งเสนอแนะทางเลือกและข้อดีข้อเสียไว้ให้
เตรียมแผนรับมือกับสิ่งไม่คาดการณ์ไว้ได้อย่างรัดกุมและมีประสิทธิภาพ
วางแผนงานที่มีความเชื่อมโยงหรือซับซ้อนกันหลายๆ งานหรือหลายๆ โครงการโดยกำหนดกิจกรรม ขั้นตอนการดำเนินงานต่างๆ ที่สนับสนุนและไม่ขัดแย้งกันได้อย่างมีประสิทธิภาพ'
						  WHERE CP_CODE = '309' AND CL_NO = 4 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'แสดงสมรรถนะระดับที่ 4 และปรับกลยุทธ์ในแผนให้เข้ากับสถานการณ์เฉพาะหน้านั้นอย่างเป็นระบบ', 
						  CL_MEANING = 'ปรับกลยุทธ์และวางแผนอย่างรัดกุมและเป็นระบบให้เข้ากับสถานการณ์ที่เกิดขึ้นอย่างไม่คาดคิด เพื่อแก้ปัญหา อุปสรรค หรือสร้างโอกาสนั้นอย่างมีประสิทธิภาพสูงสุดและให้สามารถบรรลุวัตถุประสงค์เป้าหมายที่กำหนดไว้อย่างมีประสิทธิภาพสูงสุด'
						  WHERE CP_CODE = '309' AND CL_NO = 5 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'เข้าใจแผนและนโยบายขององค์กรและหน่วยงานต่างๆ ในองค์กร', 
						  CL_MEANING = 'เข้าใจนโยบาย กลยุทธ์ขององค์กร และสามารถนำความเข้าใจนั้นมาวิเคราะห์ปัญหา อุปสรรคหรือโอกาสขององค์กรหรือหน่วยงานออกเป็นประเด็นย่อยๆ ได้'
						  WHERE CP_CODE = '310' AND CL_NO = 1 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'แสดงสมรรถนะระดับที่ 1 และประยุกต์ความเข้าใจ รูปแบบหรือประสบการณ์ไปสู่ข้อเสนอหรือแนวทางต่างๆ ในงาน', 
						  CL_MEANING = 'สามารถระบุปัญหาในสถานการณ์ปัจจุบันที่อาจมีความคล้ายคลึง หรือต่างจากประสบการณ์ที่เคยประสบมาใช้กำหนดข้อเสนอหรือแนวทาง (implication) เชิงกลยุทธ์ที่สนับสนุนให้องค์กรหรือหน่วยงานต่างๆ ที่เกี่ยวข้องบรรลุภารกิจที่กำหนดไว้หรือให้ดปฏิบัติการได้เหมาะสมกับสถานการณ์ที่เกิดขึ้นได้'
						  WHERE CP_CODE = '310' AND CL_NO = 2 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'แสดงสมรรถนะระดับที่ 2 และประยุกต์ทฤษฎีหรือแนวคิดซับซ้อนในการพิจาณาสถานกาณ์ หรือกำหนดแผนงานหรือข้อเสนอต่างๆ', 
						  CL_MEANING = 'ประยุกต์ทฤษฎี หรือแนวคิดซับซ้อนที่มีฐานมาจากองค์ความรู้หรือข้อมูลเชิงประจักษ์ ในการพิจารณาสถานการณ์ แยกแยะข้อดีข้อเสียของประเด็นต่างๆ ในการปฏิบัติงานขององค์กรหรือหน่วยงานที่ตนดูแลรับผิดชอบอยู่
สามารถใช้แนวคิดต่างๆ ที่เรียนรู้มาเชื่อมโยงอธิบายเหตุผลความเป็นมา แยกแยะข้อดี และข้อเสียของปัญหา สถานการณ์ ฯลฯ เป็นประเด็นต่างๆ ได้อย่างมีเหตุมีผล
ประยุกต์ best practice หรือผลการวิจัยต่างๆ มากำหนดโครงการหรือแผนงานที่ผลสัมฤทธิ์มีประโยชน์ต่อองค์กรหรืองานที่ตนดูแลรับผิดชอบอยู่'
						  WHERE CP_CODE = '310' AND CL_NO = 3 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'แสดงสมรรถนะระดับที่ 3 และเชื่อมโยงสถานการณ์ในประเทศและต่างประเทศเพื่อกำหนดแผนได้อย่างทะลุปรุโปร่ง', 
						  CL_MEANING = 'ประเมินและสังเคราะห์สถานการณ์ ประเด็น หรือปัญหาทางเศรษฐกิจ สังคม การเมืองภายในประเทศและต่างประเทศที่ซับซ้อนด้วยกรอบแนวคิดและวิธีพิจารณาแบบมองภาพองค์รวม เพื่อใช้ในการกำหนดแผนหรือนโยบายขององค์กรหรือหน่วยงานที่ตนดูแลรับผิดชอบอยู่
ระบุได้ว่าอะไรเป็นเหตุเป็นผลแก่กันในสถานการณ์หนึ่งๆ ในระดับหน่วยงาน/องค์กร/ประเทศ แล้วแยกแยะข้อดีข้อเสียของประเด็นต่างๆ รวมถึงอธิบายชี้แจงสถานการณ์ที่ซับซ้อนดังกล่าวให้สามารถเป็นที่เข้าใจได้'
						  WHERE CP_CODE = '310' AND CL_NO = 4 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'แสดงสมรรถนะระดับที่ 4 และสร้างสรรค์และบูรณาการองค์ความรู้ใหม่มาใช้ในงานกลยุทธ์', 
						  CL_MEANING = 'สรรค์สร้างและบูรณาการองค์ความรู้ใหม่มาใช้ในงานกลยุทธ์ โดยพิจารณาจากบริบทประเทศไทยและระบบอุตสาหกรรมในภาพรวมและปรับให้เหมาะสม ปฏิบัติได้จริง
วิเคราะห์ปัญหาในแง่มุมที่ลึกซึ้งถึงปรัชญาแนวคิดเบื้องหลังของประเด็นหรือทางเลือกต่างๆ ที่ซับซ้อน อันนำไปสู่การประดิษฐ์คิดค้น การสร้างสรรค์และนำเสนอรูปแบบ วิธี ตลอดจนองค์ความรู้ใหม่ที่ไม่เคยปรากฎมาก่อนและเป็นประโยชน์ต่อกระทรวงฯ หรือสังคมและประเทศชาติโดยรวม'
						  WHERE CP_CODE = '310' AND CL_NO = 5 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'สร้างสัมพันธภาพที่ดีกับพันธมิตรเชิงกลยุทธ์', 
						  CL_MEANING = 'ผูกมิตรและสร้างสัมพันธภาพที่ดีระหว่างกระทรวงฯ/สำนักงานปลัดกับเครือข่ายพันธมิตรเชิงกลยุทธ์ (เช่น ผู้ประกอบการ สถาบันการศึกษา เครือข่ายกลุ่มธุรกิจ กลุ่มที่ปรึกษาหรือผู้เชี่ยวชาญ ฯลฯ) รวมทั้งสื่อสารข้อมูลหรือข่าวสารที่จำเป็นให้พันธมิตรได้เข้าใจและรับรู้อยู่เสมอ'
						  WHERE CP_CODE = '311' AND CL_NO = 1 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'แสดงสมรรถนะระดับที่ 1 และรับฟังประเด็นความรู้สึกหรือความต้องการของพันธมิตรอย่างตั้งใจเพื่อนำไปสู่การปรับปรุงที่ดีขึ้นทั้งสองฝ่าย', 
						  CL_MEANING = 'พยายามทำความเข้าใจปัญหาหรือประเด็นความรู้สึกหรือความต้องการของพันธมิตรอย่างตั้งใจเพื่อนำไปสู่การปรับปรุงที่ดีขึ้นทั้งสองฝ่าย
แลกเปลี่ยนข้อมูลหรือความคิดเห็นกันระหว่าง 2 ฝ่ายอย่างสม่ำเสมอเพื่อมุ่งหวังให้เกิดความเข้าใจและความร่วมมือที่ดีต่อกันมากยิ่งขึ้น'
						  WHERE CP_CODE = '311' AND CL_NO = 2 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'แสดงสมรรถนะระดับที่ 2 และรักษาความร่วมมือหรือความสัมพันธ์ที่ดีกับพันธมิตรรวมถึงให้ความช่วยเหลือในโอกาสที่จำเป็นเพื่อให้งานสำเร็จลุล่วง', 
						  CL_MEANING = 'หมั่นรักษาความสัมพันธ์หรือความร่วมมือที่ดีกับพันธมิตร (เช่น ผู้ประกอบการ สถาบันการศึกษา) ผ่านกิจกรรมหรือการติดต่อสัมพันธ์ที่สร้างสรรค์
มุ่งมั่นให้การสนับสนุนหรือความช่วยเหลือระหว่างกันเพื่อให้งานที่ตั้งเป้าหมายร่วมกันประสบความสำเร็จ
ใส่ใจรักษาสายสัมพันธ์ที่ดีให้ต่อเนื่องและยาวนานโดยการไต่ถามทุกข์สุข หรือติดต่อเยี่ยมเยือนสม่ำเสมอ เอื้อเฟื้อให้ความช่วยเหลือหรือให้คำแนะนำที่เป็นประโยชน์ด้วยความจริงใจในโอกาสต่างๆ ที่เหมาะสม'
						  WHERE CP_CODE = '311' AND CL_NO = 3 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'แสดงสมรรถนะระดับที่ 3 และสร้างผลประโยชน์ร่วมกันอย่างยั่งยืนโดยการผสานสมดุลระหว่างเป้าหมายระยะสั้นและเป้าหมายระยะยาว', 
						  CL_MEANING = 'เน้นสรรสร้างผลประโยชน์ร่วมกันระหว่างกระทรวงฯ กับพันธมิตร แม้ต้องเสียประโยชน์ในระยะสั้น แต่สามารถสร้างมิตรภาพและผลประโยชน์ร่วมกันในระยะยาว'
						  WHERE CP_CODE = '311' AND CL_NO = 4 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'แสดงสมรรถนะระดับที่ 4 และสร้างมิตรภาพจนเกิดเป็นความไว้ใจและเป็นที่รู้จักกว้างขวาง', 
						  CL_MEANING = 'รักษามิตรภาพกับพันธมิตรในระยะยาวจนเกิดเป็นความไว้ใจและเป็นพลังที่เข็มแข็งเพื่อสรรสร้างให้เกิดประโยชน์สูงสุดร่วมกัน
เป็นที่รู้จักอย่างกว้างขวางในแวดวงที่เกี่ยวข้อง ด้วยความไว้วางใจและความรู้สึกที่ดี อันเป็นผลมาจากการผูกมิตรและสานสัมพันธ์ ตลอดจนสานประโยชน์อย่างจริงใจแก่พันธมิตรประเภทต่างๆ จำนวนมากมาเป็นเวลานาน'
						  WHERE CP_CODE = '311' AND CL_NO = 5 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'หาข้อมูลในระดับต้นและแสดงผลข้อมูลได้', 
						  CL_MEANING = 'สามารถหาข้อมูลโดยการถามจากผู้ที่เกี่ยวข้องโดยตรง การใช้ข้อมูลที่มีอยู่ หรือหาจากแหล่งข้อมูลที่มีอยู่แล้วและสรุปผลข้อมูลเพื่อแสดงผลข้อมูลในรูปแบบต่างๆ เช่น กราฟ รายงาน ได้อย่างถูกต้อง ครบถ้วน'
						  WHERE CP_CODE = '312' AND CL_NO = 1 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'แสดงสมรรถนะระดับที่ 1 และใช้วิธีการสืบเสาะหาข้อมูลเพื่อจับประเด็นหรือแก่นความของข้อมูลหรือปัญหาได้', 
						  CL_MEANING = 'สามารถสืบเสาะปัญหาหรือสถานการณ์อย่างลึกซึ้งกว่าการตั้งคำถามตามปรกติธรรมดา หรือสืบเสาะจากผู้เชี่ยวชาญ เพื่อให้ได้มาซึ่งแก่นหรือประเด็นของเนื้อหา และนำแก่นหรือประเด็นเหล่านั้นมาจัดการวิเคราะห์ ประเมินผลให้เกิดข้อมูลที่ลึกซึ้งมากที่สุด'
						  WHERE CP_CODE = '312' AND CL_NO = 2 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'แสดงสมรรถนะระดับที่ 2 และหาข้อมูลในเบื้องลึก (Insights)', 
						  CL_MEANING = 'ค้นหาหรือสอบถามเจาะลึกอย่างต่อเนื่อง (เช่น จากหนังสือ หนังสือพิมพ์ นิตยสาร ระบบสืบค้นโดยอาศัยเทคโนโลยีสารสนเทศ ตลอดจนแหล่งข่าวต่างๆ) เพื่อให้เข้าใจถึงมุมมองทัศนะความคิดเห็นที่แตกต่าง ต้นตอของสถานการณ์ ปัญหา หรือโอกาสที่ซ่อนเร้นอยู่ในเบื้องลึก และนำความเข้าใจเหล่านั้นมาประเมินผล และตีความเป็นข้อมูลได้อย่างถูกต้องและเกิดประโยชน์ต่อการปฏิบัติงานสูงสุด'
						  WHERE CP_CODE = '312' AND CL_NO = 3 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'แสดงสมรรถนะระดับที่ 3 และสืบค้นข้อมูลอย่างเป็นระบบให้เชื่อมต่อข้อมูลที่ขาดหายไปหรือคาดการณ์ได้อย่างมีนัยสำคัญ', 
						  CL_MEANING = 'จัดทำการวิจัยโดยอ้างอิงจากข้อมูลที่มีอยู่หรือสืบค้นจากแหล่งข้อมูลที่แปลกใหม่แตกต่างจากปรกติธรรมดาทั่วไปอย่างเป็นระบบหรือเป็นไปตามหลักการทางสถิติ และนำผลที่ได้นั้นมาเชื่อมต่อข้อมูลที่ขาดหายไป หรือพยากรณ์หรือสร้างแบบจำลอง (model) หรือสร้างระบบ (system formula) ได้อย่างถูกต้องและเป็นระบบ'
						  WHERE CP_CODE = '312' AND CL_NO = 4 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'แสดงสมรรถนะระดับที่ 4 และวางระบบการสืบค้น เพื่อหาข้อมูลอย่างต่อเนื่อง', 
						  CL_MEANING = 'วางระบบการสืบค้น เพื่อให้มีข้อมูลที่ทันเหตุการณ์ป้อนเข้ามาอย่างต่อเนื่องและสามารถออกแบบ เลือกใช้ หรือประยุกต์วิธีการในการจัดทำแบบจำลองหรือระบบต่างๆ ได้อย่างถูกต้องและมีนัยสำคัญ'
						  WHERE CP_CODE = '312' AND CL_NO = 5 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'นำเสนอข้อมูลอย่างตรงไปตรงมา', 
						  CL_MEANING = 'นำเสนอข้อมูล อธิบาย ชี้แจงรายละเอียดแก่ผู้ฟังอย่างตรงไปตรงมาโดยอิงข้อมูลที่มีอยู่ แต่อาจยังมิได้มีการปรับใจความและวิธีการให้สอดคล้องกับความสนใจและบุคลิกลักษณะของผู้ฟัง'
						  WHERE CP_CODE = '313' AND CL_NO = 1 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'แสดงสมรรถนะระดับที่ 1 และเจรจาโน้มน้าวใจโดยอาศัยหลักการและเหตุผล', 
						  CL_MEANING = 'เตรียมการนำเสนอข้อมูลเป็นอย่างดี และใช้ความพยายามเจรจาโน้มน้าวใจโดยยกหลักการและเหตุผลที่เกี่ยวข้องมาประกอบการนำเสนออย่างมีขั้นตอน
ใช้ความพยายามเจรจาโน้มน้าวใจโดยยกหลักการและเหตุผลที่เกี่ยวข้องมาอธิบายประกอบการนำเสนออย่างมีขั้นตอน'
						  WHERE CP_CODE = '313' AND CL_NO = 2 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'แสดงสมรรถนะระดับที่ 2 และเจรจาต่อรองหรือนำเสนอข้อมูลโดยปรับสารให้สอดคล้องกับผู้ฟังเป็นสำคัญ', 
						  CL_MEANING = 'ประยุกต์ใช้ความเข้าใจ ความสนใจของผู้ฟังให้เป็นประโยชน์ในเจรจาเสนอข้อมูล นำเสนอหรือเจรจาโดยคาดการณ์ถึงปฏิกิริยา ผลกระทบที่จะมีต่อผู้ฟังเป็นหลัก
สามารถนำเสนอทางเลือกหรือให้ข้อสรุปในการเจรจาอันเป็นประโยชน์แก่ทั้งสองฝ่าย'
						  WHERE CP_CODE = '313' AND CL_NO = 3 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'แสดงสมรรถนะระดับที่ 3 และใช้กลยุทธ์การสื่อสารจูงใจทางอ้อม', 
						  CL_MEANING = 'ใช้ความเข้าใจบุคคลหรือองค์กรให้เป็นประโยชน์โดยการนำเอาบุคคลที่สามหรือผู้เชี่ยวชาญมาสนับสนุนให้การเจรจาโน้มน้าวจูงใจประสบผลสำเร็จหรือมีน้ำหนักมากยิ่งขึ้น
ใช้ทักษะในการโน้มน้าวใจทางอ้อม เพื่อให้ได้ผลสัมฤทธิ์ดังประสงค์โดยคำนึงถึงผลกระทบและความรู้สึกของผู้อื่นเป็นสำคัญ'
						  WHERE CP_CODE = '313' AND CL_NO = 4 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'แสดงสมรรถนะระดับที่ 4 และใช้กลยุทธ์ที่ซับซ้อนในการจูงใจ', 
						  CL_MEANING = 'สร้างกลุ่มแนวร่วมเพื่อสนับสนุนให้การเจรจาโน้มน้าวใจมีน้ำหนักและสัมฤทธิ์ผลได้ดียิ่งขึ้น
ประยุกต์ใช้หลักจิตวิทยามวลชนหรือจิตวิทยากลุ่มให้เป็นประโยชน์ในการเจรจาโน้มน้าวจูงใจได้อย่างมีประสิทธิภาพ'
						  WHERE CP_CODE = '313' AND CL_NO = 5 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'นำเสนออย่างตรงไปตรงมา', 
						  CL_MEANING = 'นำเสนอความเห็นอย่างตรงไปตรงมา ชัดเจนและถูกต้องในการอภิปรายหรือนำเสนอผลงานเพื่อให้ผู้รับนำไปใช้ต่อได้
ใช้ภาษาที่เหมาะสมในการสื่อสาร รวมถึงตอบสนองต่อคำสั่ง/ งาน/ นโยบาย/ ระเบียบ/ ข้อบังคับที่เกี่ยวข้องกับงานในหน้าที่ตลอดจนความคิดเห็นของบุคคลต่างๆ ได้อย่างถูกต้อง'
						  WHERE CP_CODE = '314' AND CL_NO = 1 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'แสดงความสามารถระดับที่ 1 และสามารถอธิบาย สื่อสารให้ผู้ฟังเข้าใจได้โดยง่าย', 
						  CL_MEANING = 'มีการสื่อสารอย่างเป็นระบบ เพื่อให้เกิดความเข้าใจที่ถูกต้องตรงกันและสามารถอธิบาย ชี้แจง สื่อสารงาน/ นโยบาย/ ระเบียบ/ ข้อบังคับให้ผู้ฟังเข้าใจได้โดยง่าย
ฟัง อ่าน จับประเด็นและสรุปประเด็นทั้งความหมายโดยตรงและโดยนัยได้อย่างถูกต้อง
สามารถสื่อสารความหมาย ใจความสำคัญของเอกสาร แนวคิดต่างๆ ให้ผู้อื่นเข้าใจในได้ในภาษาที่เหมาะสมกับผู้รับสาร
นำเสนอข้อมูล ความคิดผ่านสื่อต่างๆ เช่นการเขียนบันทึก/ รายงาน/ e-mail อย่างชัดเจน ครอบคลุมเนื้อหาสาระและง่ายต่อการเข้าใจ'
						  WHERE CP_CODE = '314' AND CL_NO = 2 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'แสดงความสามารถระดับที่ 2 และใช้ศิลปะการจูงใจเป็นผู้ฟังที่ดี', 
						  CL_MEANING = 'ปรับรูปแบบการนำเสนอและอภิปรายให้เหมาะสมกับความสนใจและระดับของผู้ฟังคาดการณ์ถึงผลกระทบของสิ่งที่นำเสนอและภาพพจน์ของผู้พูดที่จะมีต่อผู้ฟัง
ฟังอย่างตั้งใจและสามารถตั้งคำถามเพื่อให้เกิดความเข้าใจอย่างถ่องแท้เกี่ยวกับเนื้อหาสาระต่างๆ และนำมาสรุปเนื้อหาเชิงลึก (Insight) ให้ผู้อื่นเข้าใจโดยมิได้ร้องขอ
คาดการณ์ได้ว่าสิ่งใดควรมีการสื่อสารล่วงหน้า และนำเสนอข้อมูลหรือความเห็นที่เพิ่มคุณค่าหรือความรู้ต่อการประชุมหรือการตัดสินใจของผู้ที่เกี่ยวข้อง'
						  WHERE CP_CODE = '314' AND CL_NO = 3 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'แสดงความสามารถระดับที่ 3 และใช้และสามารถสื่อสารกับผู้ฟังที่มีภูมิหลังต่างกันให้เข้าใจได้อย่างชัดเจน', 
						  CL_MEANING = 'วิเคราะห์สถานการณ์และกลุ่มผู้รับสารได้อย่างถ่องแท้จนสามารถปรับภาษาที่ใช้ในการสื่อสารและวิธีการนำเสนอให้เหมาะสมกับผู้ฟังกลุ่มต่างๆได้อย่างมีประสิทธิภาพ
สรุปแก่นและสื่อสารเนื้อหาต่างๆ ที่มีความยุงยากซับซ้อนโดยคำนึงถึงความต้องการ ความเหมาะสม ระดับความเข้าใจและภูมิหลังของผู้รับสารกลุ่มต่างๆเป็นสำคัญ
ควบคุมอารมณ์และใช้ถ้อยคำ สำนวนภาษาที่ดีในการสื่อสาร รวมถึงชี้แจงทำความเข้าใจอย่างตรงไปตรงมา ชัดเจนและครอบคลุม แม้ในสถานการณ์ที่มีความขัดแย้งหรือมีความเห็นหลากหลายได้อย่างมีประสิทธิภาพสูง
โน้มน้าว สรุปประเด็นและสื่อสารถ่ายทอดหรือนำเสนอข้อมูลที่เสริมสร้างประโยชน์หรือลดปัญหาได้อย่างมีประสืทธิภาพต่อส่วนรวมให้กลุ่มบุคคลหรือหน่วยงานต่างๆ ยอมรับหรือปฏิบัติตามด้วยความเต็มใจ'
						  WHERE CP_CODE = '314' AND CL_NO = 4 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'แสดงความสามารถระดับที่ 4 และใช้กลยุทธ์ซับซ้อนในการจูงใจ', 
						  CL_MEANING = 'ใช้ความเข้าใจกับปัจจัยภายนอกต่างๆ เช่น การเมือง เศรษฐกิจ สังคม นโยบายของกระทรวง ฯลฯ ในการสร้างแนวร่วมหรือส่งผลกระทบต่อผู้ฟัง
ชี้แจงอธิบายนโยบาย/ กลยุทธ์/ แผนงาน ภายใต้อำนาจ หน้าที่ ความรับผิดชอบให้ผู้เกี่ยวข้องในวงกว้างทั้งในและนอกกระทรวงเข้าใจได้อย่างถูกต้อง ชัดเจน เพื่อให้เกิดการยอมรับหรือปฏิบัติตาม
ผลักดันและสนับสนุนการเจรจาต่อรองที่ต่อรองที่ก่อให้เกิดประโยชน์และยอมรับร่วมกัน
ถ่ายทอดหรือนำเสนอข้อมูลที่เสริมสร้างประโยชน์ต่อส่วนรวม และสร้างภาพลักษณ์ที่ดีของกระทรวงต่อสังคมโดยรวม'
						  WHERE CP_CODE = '314' AND CL_NO = 5 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'เข้าใจความหมายที่ผู้อื่นต้องการสื่อสาร', 
						  CL_MEANING = 'เข้าใจความหมายที่ผู้ติดต่อสื่อสารด้วยภาษาโดยสื่อต่างๆ สามารถจับใจความได้ สรุปเนื้อหาเรื่องราวได้ถูกต้องครบประเด็น'
						  WHERE CP_CODE = '315' AND CL_NO = 1 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'แสดงสมรรถนะระดับที่ 1 และเข้าใจอารมณ์ความรู้สึกและคำพูด', 
						  CL_MEANING = 'เข้าใจทั้งเนื้อหาของสารและนัยเชิงอารมณ์ (จากการสังเกตอวัจนภาษา เช่น ท่าทาง การแสดงออกทางสีหน้า หรือน้ำเสียง) ของผู้ที่ติดต่อด้วย'
						  WHERE CP_CODE = '315' AND CL_NO = 2 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'แสดงสมรรถนะระดับที่ 2 และเข้าใจความหมายแฝงในอากัปกิริยาและคำพูด', 
						  CL_MEANING = 'สามารถตีความหมายเบื้องลึกที่ไม่ได้แสดงออกอย่างชัดเจนในคำพูดหรือน้ำเสียง
เข้าใจความคิด ความกังวล หรือความรู้สึกของผู้อื่น ณ เวลาขณะนั้น ทั้งที่แสดงออกมาเพียงเล็กน้อย หรือไม่ได้แสดงออกเลยก็ตาม
สามารถระบุลักษณะนิสัยหรือจุดเด่นอย่างใดอย่างหนึ่งของผู้ที่ติดต่อด้วยได้'
						  WHERE CP_CODE = '315' AND CL_NO = 3 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'แสดงสมรรถนะระดับที่ 3 และใช้ความเข้าใจในการสื่อสารทั้งที่เป็นคำพูด และความหมายนัยแฝงในการสื่อสารกับผู้นั้นได้อย่างเหมาะสม', 
						  CL_MEANING = 'แสดงความเข้าใจในนัยของพฤติกรรมและอารมณ์ความรู้สึกของผู้อื่น
ใช้ความเข้าใจในบุคคลนั้นให้เป็นประโยชน์ในการผูกมิตรทำความรู้จัก หรือติดต่อประสานงานในโอกาสต่างๆ'
						  WHERE CP_CODE = '315' AND CL_NO = 4 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'แสดงสมรรถนะระดับที่ 4 และเข้าใจสาเหตุของพฤติกรรมผู้อื่น', 
						  CL_MEANING = 'แสดงความเข้าใจเบื้องลึกถึงสาเหตุของพฤติกรรม หรือปัญหาของผู้อื่นตลอดจนเข้าใจสาเหตุหรือแรงจูงใจในระยะยาวของพฤติกรรมและความรู้สึกของผู้อื่น
สามารถแสดงทัศนคติที่เป็นธรรมและเหมาะสมเกี่ยวกับจุดอ่อนและจุดเด่นทางพฤติกรรมและลักษณะนิสัยของผู้อื่น
มีจิตวิทยาในการใช้ความเข้าใจผู้อื่นเพื่อเป็นพื้นฐานในการทำงานร่วมกัน การเจรจาทำความเข้าใจ การให้ความรู้หรือให้บริการ การสอนงาน การพัฒนาบุคลากร ฯลฯ ตามภารกิจในงาน'
						  WHERE CP_CODE = '315' AND CL_NO = 5 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'วางแผนงานบนพื้นฐานของความเข้าใจเทคโนโลยี ระบบ กระบวนการทำงานและมาตรฐานในงานของตน', 
						  CL_MEANING = 'เข้าใจเทคโนโลยี ระบบ กระบวนการทำงานและมาตรฐานในงานที่ตนสังกัดอยู่ รวมทั้งกฎระเบียบ ตลอดจนขั้นตอนกระบวนการปฏิบัติงานต่างๆ และนำความเข้าใจนี้มาใช้ในการวางแผนงานให้เป็นขั้นตอนและเกิดผลลัพธ์อย่างชัดเจนและถูกต้อง'
						  WHERE CP_CODE = '316' AND CL_NO = 1 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'แสดงสมรรถนะระดับที่ 1 และวางแผนงานตามความสำคัญของงานบนพื้นฐานของความเข้าใจความสัมพันธ์เชื่อมโยงของระบบและกระบวนการทำงานของตนกับหน่วยงานอื่นๆ ที่ติดต่ออย่างชัดเจน', 
						  CL_MEANING = 'เข้าใจและเชื่อมโยงเทคโนโลยี ระบบ กระบวนการทำงาน ขั้นตอนกระบวนการปฏิบัติงานต่างๆ ของตนกับหน่วยงานอื่นที่ติดต่อด้วยอย่างถูกต้อง และนำมาวางแผนจัดลำดับของงานและผลลัพธ์เพื่อให้สามารถบรรลุตามเป้าหมายและเวลาที่วางไว้ได้อย่างมีประสิทธิภาพสูงสุด
วิเคราะห์หาข้อดี ข้อเสียและผลต่อเนื่องของแผนงานที่วางไว้โดยการวิเคราะห์ความเชื่อมโยงของกระบวนงานต่างๆ ของตนและหน่วยงานที่เกี่ยวข้อง'
						  WHERE CP_CODE = '316' AND CL_NO = 2 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'แสดงสมรรถนะระดับที่ 2 และสามารถมองภาพรวมแล้ววางแผนหรือเชื่อมโยงงานหรือกิจกรรมต่างๆ ที่มีความซับซ้อนเพื่อให้บรรลุตามแผนที่กำหนดไว้ได้ 
หรือเพื่อปรับปรุงระบบให้มีประสิทธิภาพขึ้นได้', 
						  CL_MEANING = 'เข้าใจข้อจำกัดของเทคนิค ระบบหรือกระบวนการทำงานของตนหรือหน่วยงานอื่นๆ ที่ติดต่อด้วย และวางแผนงานที่มีความเชื่อมโยงหรือซับซ้อนกันหลายๆ งานหรือหลายๆ โครงการเพื่อให้งานบรรลุตามเป้าหมาย หรือเพื่อให้เกิดการปรับเปลี่ยนหรือปรับปรุงระบบให้สามารถทำงานได้อย่างมีประสิทธิภาพสูงขึ้นได้'
						  WHERE CP_CODE = '316' AND CL_NO = 3 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'แสดงสมรรถนะระดับที่ 3 และเข้าใจกระแสภายนอกกับผลกระทบโดยรวมต่อเทคโนโลยี ระบบหรือกระบวนการทำงานของหน่วยงานและคาดการณ์ล่วงหน้าเพื่อเตรียมทางเลือกสำหรับการป้องกัน/แก้ไขปัญหาที่เกิดขึ้น', 
						  CL_MEANING = ''
						  WHERE CP_CODE = '316' AND CL_NO = 4 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'แสดงสมรรถนะระดับที่ 4 และเข้าใจความต้องการที่แท้จริงของกระทรวงฯ และปรับกลยุทธ์ในแผนให้เข้ากับสถานการณ์เฉพาะหน้านั้นอย่างเป็นระบบ', 
						  CL_MEANING = 'เข้าใจสถานะของระบบ เทคโนโลยี และกระบวนการการทำงานของกระทรวงฯ อย่างถ่องแท้ จนสามารถปรับกลยุทธ์และวางแผนอย่างรัดกุม เพื่อดำเนินการเปลี่ยนแปลงในภาพรวมให้กระทรวงเติบโตอย่างยั่งยืน'
						  WHERE CP_CODE = '316' AND CL_NO = 5 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'เข้าใจเทคโนโลยี ระบบ กระบวนการทำงานและมาตรฐานในงานของตน', 
						  CL_MEANING = 'เข้าใจเทคโนโลยี ระบบ กระบวนการทำงานและมาตรฐานในงานที่ตนสังกัดอยู่ รวมทั้งกฎระเบียบ ตลอดจนขั้นตอนกระบวนการปฏิบัติงานต่างๆ และนำความเข้าใจนี้มาใช้ในการปฏิบัติงาน ติดต่อประสานงาน หรือรายงานผล ฯลฯ ในหน้าที่ได้ถูกต้อง'
						  WHERE CP_CODE = '317' AND CL_NO = 1 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'แสดงสมรรถนะระดับที่ 1 และเข้าใจความสัมพันธ์เชื่อมโยงของระบบและกระบวนการทำงานของตนกับหน่วยงานอื่นๆ ที่ติดต่ออย่างชัดเจน', 
						  CL_MEANING = 'เข้าใจและเชื่อมโยงเทคโนโลยี ระบบ กระบวนการทำงาน ขั้นตอนกระบวนการปฏิบัติงานต่างๆ ของตนกับหน่วยงานอื่นที่ติดต่อด้วยอย่างถูกต้อง รวมถึงนำความเข้าใจนี้มาใช้เพื่อให้การทำงานระหว่างกันเป็นไปอย่างมีประสิทธิภาพและสอดรับกันสูงสุด'
						  WHERE CP_CODE = '317' AND CL_NO = 2 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'แสดงสมรรถนะระดับที่ 2 และสามารถมองภาพรวมแล้วปรับเปลี่ยนหรือปรับปรุงระบบให้มีประสิทธิภาพขึ้น', 
						  CL_MEANING = 'เข้าใจข้อจำกัดของเทคนิค ระบบหรือกระบวนการทำงานของตนหรือหน่วยงานอื่นๆ ที่ติดต่อด้วย และรู้ว่าสิ่งใดที่ควรกระทำเพื่อปรับเปลี่ยนหรือปรับปรุงระบบให้สามารถทำงานได้อย่างมีประสิทธิภาพสูงขึ้นได้ 
เมื่อเจอสถานการณ์ที่แตกต่างจากเดิมสามารถใช้ความเข้าใจผลต่อเนื่องและความสัมพันธ์เชื่อมโยงของระบบและกระบวนการทำงาน เพื่อนำมาแก้ไขปัญหาได้อย่างเหมาะสมทันเวลา'
						  WHERE CP_CODE = '317' AND CL_NO = 3 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'แสดงสมรรถนะระดับที่ 3 และเข้าใจกระแสภายนอกกับผลกระทบโดยรวมต่อเทคโนโลยี ระบบหรือกระบวนการทำงานของหน่วยงาน', 
						  CL_MEANING = 'เข้าใจกระแสหรือสถานการณ์ภายนอก (เช่น นโยบายภาครัฐในภาพรวม การเปลี่ยนแปลงในอุตสาหกรรม) และสามารถนำความเข้าใจนั้นมาเตรียมรับมือหรือดำเนินการการเปลี่ยนแปลงได้อย่างเหมาะสมและเกิดประโยชน์สูงสุด 
ศึกษาเรียนรู้ความสำเร็จหรือความผิดพลาดของระบบหรือกระบวนการการทำงานที่เกี่ยวข้องและนำมาปรับใช้กับการทำงานของหน่วยงานอย่างเหมาะสม'
						  WHERE CP_CODE = '317' AND CL_NO = 4 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'แสดงสมรรถนะระดับที่ 4 และเข้าใจความต้องการที่แท้จริงของกระทรวงฯ', 
						  CL_MEANING = 'เข้าใจสถานะของระบบ เทคโนโลยี และกระบวนการการทำงานของกระทรวงฯ อย่างถ่องแท้ จนสามารถกำหนดความต้องการหรือดำเนินการเปลี่ยนแปลงในภาพรวมเพื่อให้กระทรวงฯ เติบโตอย่างยั่งยืน 
เข้าใจและสามารถระบุจุดยืนและความสามารถในการพัฒนาในเชิงระบบ เทคโนโลยี กระบวนการทำงานหรือมาตรฐานการทำงานในเชิงบูรณาการระบบ (holistic view) ของกระทรวงฯ'
						  WHERE CP_CODE = '317' AND CL_NO = 5 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'ต้องการทำงานให้ถูกต้องและชัดเจน', 
						  CL_MEANING = 'ตั้งใจทำงานให้ถูกต้อง สะอาดเรียบร้อย
ละเอียดถี่ถ้วนในการปฏิบัติตามขั้นตอนการปฏิบัติงาน กฎ ระเบียบที่วางไว้
แสดงอุปนิสัยรักความเป็นระเบียบเรียบร้อยทั้งในงานและในสภาวะแวดล้อมรอบตัว อาทิ จัดระเบียบโต๊ะทำงาน และบริเวณสำนักงานที่ตนปฏิบัติหน้าที่อยู่ ริเริ่มหรือร่วมดำเนินกิจกรรมเพื่อความเป็นระเบียบของสถานที่ทำงาน อาทิ กิจกรรม 5 ส. ด้วยความสมัครใจ กระตือรือร้น ฯลฯ'
						  WHERE CP_CODE = '318' AND CL_NO = 1 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'แสดงสมรรถนะระดับที่ 1 และตรวจทานความถูกต้องของงานที่ตนรับผิดชอบ', 
						  CL_MEANING = 'ตรวจทานความถูกต้องของงานอย่างละเอียดรอบคอบเพื่อให้งานมีความถูกต้องสูงสุด
ลดข้อผิดพลาดที่เคยเกิดขึ้นแล้วจากความไม่ตั้งใจ'
						  WHERE CP_CODE = '318' AND CL_NO = 2 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'แสดงสมรรถนะระดับที่ 2 และดูแลความถูกต้องของงานทั้งของตนและผู้อื่น (ที่อยู่ในความรับผิดชอบของตน)', 
						  CL_MEANING = 'ตรวจสอบความถูกต้องโดยรวมของงานของตนเอง
ตรวจสอบความถูกต้องโดยรวมของงานผู้อื่นหรือหน่วยงานอื่นตามอำนาจหน้าที่  โดยอิงมาตรฐานการปฏิบัติงาน หรือกฎหมาย ข้อบังคับ กฎระเบียบที่เกี่ยวข้อง'
						  WHERE CP_CODE = '318' AND CL_NO = 3 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'แสดงสมรรถนะระดับที่ 3 และกำกับตรวจสอบขั้นตอนการปฏิบัติงานโดยละเอียด', 
						  CL_MEANING = 'ตรวจสอบว่าผู้อื่นปฏิบัติตามขั้นตอนการทำงานที่วางไว้หรือไม่ให้ความเห็นและชี้แนะให้ผู้อื่นปฏิบัติตามขั้นตอนการทำงานที่วางไว้ เพื่อความถูกต้องของงาน
ตรวจสอบความก้าวหน้าและความถูกต้อง/คุณภาพของผลลัพธ์ของโครงการตามกำหนดเวลาที่วางไว้ 
ระบุข้อบกพร่องหรือข้อมูลที่ขาดหายไป และกำกับดูแลให้หาข้อมูลเพิ่มเติมเพื่อให้ได้ผลลัพธ์หรือผลงานที่มีคุณภาพตามเกณฑ์ที่กำหนด'
						  WHERE CP_CODE = '318' AND CL_NO = 4 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMPETENCE_LEVEL SET CL_NAME = 'แสดงสมรรถนะระดับที่ 4 และสร้างความชัดเจนของความถูกต้องและคุณภาพของขั้นตอนการทำงานหรือผลงานหรือโครงการโดยละเอียด', 
						  CL_MEANING = 'สร้างความชัดเจนของความถูกต้องและคุณภาพของขั้นตอนการทำงานหรือผลงานหรือโครงการโดยละเอียดเพื่อควบคุมให้ผู้อื่นปฏิบัติตามขั้นตอนการทำงานที่วางไว้อย่างถูกต้องและเกิดความโปร่งใสตรวจสอบได้ 
สร้างระบบและวิธีการที่สามารถกำกับตรวจสอบความก้าวหน้าและความถูกต้อง/คุณภาพของผลงานหรือขั้นตอนในการปฏิบัติงานของผู้อื่น หรือหน่วยงานอื่น ได้อย่างสม่ำเสมอ'
						  WHERE CP_CODE = '318' AND CL_NO = 5 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('319', 1, 'มีความคล่องตัวในการปฏิบัติงาน', 'ปรับตัวได้แม้ประสบความยากลำบากทางกายภาพในงาน ไม่ยึดติดกับความสะดวกสบาย วัตถุแสดงฐานะทางสังคม หรือระดับอาวุโสในงาน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('319', 2, 'แสดงสมรรถนะระดับที่ 1 และยอมรับความจำเป็นที่จะต้องปรับเปลี่ยน', 'เต็มใจ ยอมรับ และเข้าใจความคิดเห็นของผู้อื่น
เต็มใจที่จะเปลี่ยนความคิด ทัศนคติ เมื่อได้รับข้อมูลใหม่หรือหลักฐานที่ขัดแย้งกับความคิดเดิม
ยอมรับและปรับตัวต่อสิ่งใหม่ เพื่อสนับสนุนให้องค์กรบรรลุเป้าหมายที่กำหนด', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('319', 3, 'แสดงสมรรถนะระดับที่ 2 และใช้กฎระเบียบอย่างยืดหยุ่น', 'มีความคล่องตัวในการปฏิบัติหน้าที่ ใช้กฎเกณฑ์ กระบวนการปฏิบัติงานอย่างยืดหยุ่น มีวิจารณญาณในการปรับให้เข้ากับสถานการณ์เฉพาะหน้า เพื่อให้เกิดผลสัมฤทธิ์ในการปฏิบัติงานหรือ เพื่อให้บรรลุวัตถุประสงค์ของหน่วยงานหรือของกระทรวงฯ
ปรับตัวและสามารถประพฤติปฏิบัติตนให้สอดคล้อง และเหมาะสมกับแต่ละสถานการณ์ต่างๆ ได้อย่างมีประสิทธิภาพ
สามารถเลือกทางเลือก วิธีการ หรือกระบวนการโดยปกติทั่วไปมาปรับใช้กับสถานการณ์ที่เฉพาะเจาะจงได้อย่างมีประสิทธิภาพ เพื่อให้ได้ผลงานที่ดี', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('319', 4, 'แสดงสมรรถนะระดับที่ 3 และปรับเปลี่ยนวิธีการดำเนินงาน เพื่อให้งานมีประสิทธิภาพ', 'ปรับเปลี่ยนวิธีการดำเนินงาน ให้เข้ากับสถานการณ์ แต่ยังคงเป้าหมายเดิมไว้
ปรับแก้ไขกฎระเบียบขั้นตอนการทำงานที่ล้าสมัย เพื่อเพิ่มประสิทธิภาพของหน่วยงาน
ประยุกต์ใช้ความรู้ ทักษะ หรือประสบการณ์ในการทำงานมาปรับเปลี่ยนวิธีการดำเนินงาน ขั้นตอนการปฏิบัติงานให้เข้ากับสถานการณ์และเหตุการณ์ต่างๆ ที่เกิดขึ้น เพื่อให้ได้งานที่มีประสิทธิภาพ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('319', 5, 'แสดงสมรรถนะระดับที่ 4 และปรับเปลี่ยนแผนกลยุทธ์ทั้งหมด เพื่อให้งานมีประสิทธิภาพ', 'ปรับแผนกลยุทธ์ทั้งหมด เพื่อให้เหมาะสมกับสถานการณ์เฉพาะหน้า
ทบทวน สังคายนา กฎระเบียบและวิธีการทำงานของหน่วยงานที่ดูแลรับผิดชอบอยู่ใหม่ทั้งหมด เพื่อเพิ่มประสิทธิภาพของหน่วยงาน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('320', 1, 'มีความยืดหยุ่นในการปฏิบัติหน้าที่', 'เข้าใจความหมายที่ผู้ติดต่อสื่อสาร และสามารถปรับการทำงานให้คล่องตัวและสอดคล้องกับความต้องการได้', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('320', 2, 'แสดงสมรรถนะระดับที่ 1 และเข้าใจบุคคลหรือสถานการณ์ได้ง่ายและพร้อมยอมรับความจำเป็นที่จะต้องปรับเปลี่ยน', 'เต็มใจ ยอมรับ และเข้าใจความคิดเห็นของผู้อื่นทั้งในเชิงเนื้อหาและนัยเชิงอารมณ์ 
เต็มใจที่จะเปลี่ยนความคิด ทัศนคติ และทำงานให้บรรลุตามเป้าหมาย เมื่อสถานการณ์ปรับเปลี่ยนไป เช่นได้รับข้อมูลใหม่หรือข้อคิดเห็นใหม่จากผู้เชี่ยวชาญ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('320', 3, 'แสดงสมรรถนะระดับที่ 2 และเข้าใจความหมายแฝงของบุคคลและสถานการณ์และเลือกปฏิบัติงานอย่างยืดหยุ่น', 'มีวิจารณญาณในการปรับให้เข้ากับสถานการณ์เฉพาะหน้า เพื่อให้เกิดผลสัมฤทธิ์ในการปฏิบัติงานหรือ เพื่อให้บรรลุวัตถุประสงค์ของหน่วยงานหรือของกระทรวงฯ
สามารถตีความหมายเบื้องลึกที่ไม่ได้แสดงออกอย่างชัดเจนของบุคคลหรือสถานการณ์ที่เกิดขึ้น แล้วปรับตัวให้สอดคล้อง และเหมาะสมกับกับแต่ละบุคคลหรือสถานการณ์ดังกล่าวได้อย่างมีประสิทธิภาพ
สามารถเลือกทางเลือก วิธีการ หรือกระบวนการมาปรับใช้กับสถานการณ์ที่เฉพาะเจาะจงได้อย่างมีประสิทธิภาพ เพื่อให้ได้ผลงานที่ดี', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('320', 4, 'แสดงสมรรถนะระดับที่ 3 และใช้ความเข้าใจในเชิงลึกต่อบุคคลหรือสถานการณ์มาปรับเปลี่ยนวิธีการดำเนินงานให้ได้งานที่มีประสิทธิภาพสูงสุด', 'ใช้ความเข้าใจอย่างลึกซึ้งในบุคคลหรือสถานการณ์ต่างๆ ให้เป็นประโยชน์ในทำงานให้ได้ผลงานที่มีประสิทธิภาพสูงสุด
ปรับเปลี่ยนวิธีการดำเนินงาน ระเบียบขั้นตอนหรือลักษณะการประสานงานของหน่วยงานหรือองค์กร ให้เข้ากับสถานการณ์ แต่ยังคงเป้าหมายเดิมได้อย่างมีประสิทธิภาพ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('320', 5, 'แสดงสมรรถนะระดับที่ 4 และปรับเปลี่ยนแผนกลยุทธ์ทั้งหมด เพื่อให้งานมีประสิทธิภาพ', 'ปรับแผนกลยุทธ์ทั้งหมด เพื่อให้เหมาะสมกับสถานการณ์เฉพาะหน้า
มีจิตวิทยาในการใช้ความเข้าใจผู้อื่นในสถานการณ์ต่างๆ เพื่อเป็นพื้นฐานในการเจรจาทำความเข้าใจ หรือดำเนินงานไห้ได้ตามภารกิจของหน่วยงาน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('321', 1, 'สนับสนุนความคิดสร้างสรรค์และยอมทดลองวิธีอื่นๆ เพื่อมาทดแทนวิธีการที่ใช้อยู่เดิมในการปฏิบัติงานอย่างเต็มใจและใคร่รู้', 'เต็มใจที่จะยอมรับและปรับตัวต่อความริเริ่มสร้างสรรค์หรือสิ่งใหม่ เพื่อสนับสนุนให้หน่วยงาน/สำนักงานปลัดกระทรวงบรรลุเป้าหมายที่กำหนด
แสดงความสงสัยใคร่รู้และต้องการทดลองวิธีการใหม่ๆ ที่อาจส่งผลให้ปฏิบัติงานได้ดีขึ้น
เต็มใจที่จะเสาะหาและศึกษาวิธีการที่แปลกใหม่ที่อาจนำมาประยุกต์ใช้ในการปฏิบัติงานได้อย่างเหมาะสม', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('321', 2, 'แสดงสมรรถนะระดับที่ 1 และสร้างสรรค์และหมั่นปรับปรุงกระบวนการทำงานของตนอย่างสม่ำเสมอ', 'หมั่นปรับปรุงกระบวนการทำงานของตนอย่างสม่ำเสมอ  
แสดงการเปลี่ยนแปลงในรูปแบบหรือขั้นตอนการทำงานใหม่ๆ ที่สอดคล้องและสนับสนุนให้หน่วยงานสามารถบรรลุเป้าหมายได้อย่างมีประสิทธิภาพมากขึ้น', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('321', 3, 'แสดงสมรรถนะระดับที่ 2 และคิดนอกกรอบเพื่อปรับเปลี่ยนการดำเนินงานใหม่ในหน่วยงานเพื่อให้งานมีประสิทธิภาพ', 'ประยุกต์ใช้ประสบการณ์ในการทำงานมาปรับเปลี่ยนวิธีการดำเนินงาน ให้เข้ากับสถานการณ์ แต่ยังคงเป้าหมายได้อย่างมีประสิทธิภาพสูงสุด
ไม่จำกัดตนเองอยู่กับแนวคิดดั้งเดิมที่ใช้กัน พร้อมจะทดลองวิธีการใหม่ๆ มาปรับแก้ไขระเบียบขั้นตอนการทำงานที่ล้าสมัย เพื่อเพิ่มประสิทธิภาพของหน่วยงาน
นำเสนอทางเลือก (Option) หรือแนวทางแก้ปัญหา (Solution) ในงานของตนอย่างสร้างสรรค์ก่อนที่จะปรึกษาผู้บังคับบัญชา', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('321', 4, 'แสดงสมรรถนะระดับที่ 3 และทำสิ่งใหม่ในกระทรวงฯ', 'ประยุกต์ใช้องค์ความรู้ ทฤษฎี หรือแนวคิดที่ได้รับการยอมรับมาเสนอทางเลือก (Option) หรือแนวทางแก้ปัญหา (Solution) ในการพัฒนากระทรวงฯ ให้มีประสิทธิภาพสูงขึ้น
ริเริ่มสร้างสรรค์แนวทางใหม่ๆ ในการปฏิบัติงานหรือดำเนินการต่างๆ ให้กระทรวงฯ สามารถบรรลุพันธกิจได้อย่างมีประสิทธิภาพหรือมีคุณภาพสูงขึ้น โดยแนวทางใหม่ๆ หรือ best practice นี้อาจมีอยู่แล้วในองค์กรอื่นๆ ทั้งภาครัฐหรือเอกชน และทั้งในและต่างประเทศ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('321', 5, 'แสดงสมรรถนะระดับที่ 4 และทำสิ่งใหม่ในระบบอุตสาหกรรมของประเทศโดยรวม', 'คิดนอกกรอบ พิจารณาสิ่งต่างๆ ในงานด้วยมุมมองที่แตกต่าง อันนำไปสู่การวิจัย การประดิษฐ์คิดค้น หรือการสร้างสรรค์ เพื่อนำเสนอต้นแบบ สูตร รูปแบบ วิธี ตลอดจนองค์ความรู้ใหม่ที่ไม่เคยปรากฎมาก่อนและเป็นประโยชน์ต่อระบบอุตสาหกรรมหรือสังคมและประเทศชาติโดยรวม
สนับสนุนให้เกิดบรรยากาศแห่งความคิดสร้างสรรค์หรือสร้างโอกาสใหม่ทางธุรกิจในกระทรวงฯ ด้วยการให้การสนับสนุนทางทรัพยากร หรือจัดกิจกรรมต่างๆ ที่จะช่วยกระตุ้นให้เกิดการแสดงออกทางความคิดสร้างสรรค์', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
/*
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
						VALUES(4, '101', 4, 'ผู้รับการประเมินพัฒนาระบบ ขั้นตอน วิธีการทำงานในหน่วยงาน สำนักงานปลัดกระทรวงอุตสาหกรรม หรือในกระทรวงอุตสาหกรรม เพื่อให้ได้ผลงานที่โดดเด่นหรือแตกต่างอย่างไม่เคยมีใครทำได้มาก่อน', 
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
*/		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินตอบสนอง หรือแก้ปัญหาได้อย่างรวดเร็ว และเด็ดเดี่ยวในเหตุวิกฤติ หรือสถานการณ์จำเป็น'
						WHERE CP_CODE = '301' AND CL_NO = 1 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินรู้จักพลิกแพลงวิธีการ กระบวนการต่างๆ เพื่อให้สามารถแก้ไขปัญหา หรือใช้ประโยชน์จากโอกาสนั้นได้อย่างมีประสิทธิภาพสูงสุด'
						WHERE CP_CODE = '301' AND CL_NO = 2 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินคาดการณ์และเล็งเห็นปัญหาหรือโอกาสที่อาจเกิดขึ้นในระยะเวลา 1-3 เดือนถัดจากปัจจุบัน และลงมือกระทำการล่วงหน้าเพื่อป้องกันปัญหา หรือสร้างโอกาสในสถานการณ์นั้นๆ ได้อย่างมีประสิทธิภาพสูงสุด'
						WHERE CP_CODE = '301' AND CL_NO = 3 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินคาดการณ์และเล็งเห็นปัญหาหรือโอกาสที่อาจเกิดขึ้นในระยะเวลา 4-12 เดือนถัดจากปัจจุบัน และเตรียมการเสาะหาวิธีการหรือแนวคิดใหม่ๆ ล่วงหน้า ที่อาจจะเป็นประโยชน์ในการป้องกันปัญหาและสร้างโอกาสในอนาคต'
						WHERE CP_CODE = '301' AND CL_NO = 4 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินคาดการณ์และเล็งเห็นปัญหาหรือโอกาสที่อาจเกิดขึ้นในระยะยาวและเตรียมการล่วงหน้าเพื่อป้องกันปัญหา หรือสร้างโอกาส อีกทั้งกระตุ้นให้ผู้อื่นเกิดความกระตือรือร้นต่อการป้องกันและแก้ไขปัญหาเพื่อสร้างโอกาสให้องค์กรในระยะยาว'
						WHERE CP_CODE = '301' AND CL_NO = 5 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินเข้าใจสภาพแวดล้อมทั่วไปของอุตสาหกรรมและธุรกิจของผู้ประกอบการในพื้นที่ที่รับผิดชอบ เช่น ลักษณะสินค้า เทคโนโลยี ผลิตภัณฑ์ และการบริการ เป็นต้น'
						WHERE CP_CODE = '302' AND CL_NO = 1 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินเข้าใจสาเหตุ เศรษฐกิจ สังคม และตลาด ณ  ปัจจุบันของพื้นที่ที่รับผิดชอบ รวมทั้งปัจจัยและผลกระทบต่างๆ ที่จะนำไปสู่ความสำเร็จต่ออุตสาหกรรมและธุรกิจในพื้นที่ที่รับผิดชอบ'
						WHERE CP_CODE = '302' AND CL_NO = 2 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินปรับความรู้ ความเข้าใจด้านอุตสาหกรรม เศรษฐกิจ และองค์ความรู้ใหม่ๆ มาปรับปรุงเพื่อสร้างประโยชน์ให้ผู้ประกอบการ และพัฒนาหรือ ออกแบบแผนงานธุรกิจที่ส่งเสริมการพัฒนาผลิตภัณฑ์ และการบริการของผู้ประกอบการ'
						WHERE CP_CODE = '302' AND CL_NO = 3 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินปรับความเข้าใจทางอุตสาหกรรมมาสร้างประโยชน์ให้กับจังหวัดในพื้นที่ที่รับผิดชอบ และบริหารจัดการทรัพยากรภายในอุตสาหกรรมจังหวัดให้เกิดประโยชน์กับผู้ประกอบการและประชาชนโดยรวมในจังหวัดอย่างแท้จริง'
						WHERE CP_CODE = '302' AND CL_NO = 4 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินกระตุ้น และปลูกจิตสำนึกเรื่องการพัฒนาอุตสาหกรรมทั้งประเทศให้ทุกหน่วยงานตื่นตัว และลงมือดำเนินการด้านการพัฒนาอุตสาหกรรม รวมทั้งฝึกฝนพัฒนาบุคลากรในเชิงพัฒนาอุตสาหกรรม เช่น การลงทุน การคำนวณความคุ้มค่าด้านการลงทุน เป็นต้น ให้คุ้มค่าและเกิดประโยชน์อย่างสูงสุด เพื่อให้ประเทศสามารถแข่งขันกับนานาประเทศได้ดียิ่งขึ้น'
						WHERE CP_CODE = '302' AND CL_NO = 5 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินวิเคราะห์และแยกแยะประเด็นปัญหา แนวคิด สถานการณ์หรือหลักการต่างๆ ได้อย่างดี'
						WHERE CP_CODE = '303' AND CL_NO = 1 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินจัดลำดับความสำคัญ ตั้งข้อสังเกตหรือสมมติฐานและวิเคราะห์หาข้อดีข้อเสียของปัญหา ประเด็น สถานการณ์หรือหลักการต่างๆ ได้อย่างดี'
						WHERE CP_CODE = '303' AND CL_NO = 2 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินวิเคราะห์เชื่อมโยงหลายๆ ปัจจัยและอธิบายเหตุผลที่มาที่ไปของปมปัญหา ประเด็น สถานการณ์หรือหลักการต่างๆ ได้อย่างดี'
						WHERE CP_CODE = '303' AND CL_NO = 3 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินวิเคราะห์ทุกแง่มุมของปัญหา ประเด็น สถานการณ์หรือหลักการต่างๆ พร้อมทั้งเสนอข้อแก้ไขและคาดการณ์ว่าจะมีโอกาส หรืออุปสรรคอะไรบ้างเกิดขึ้นได้'
						WHERE CP_CODE = '303' AND CL_NO = 4 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินประยุกต์ใช้ความรู้ ความเชี่ยวชาญ และเทคนิคเฉพาะด้าน เช่น หลักสถิติขั้นสูง ความเชี่ยวชาญเฉพาะสาขาที่เกี่ยวข้องกับผลิตภัณฑ์หรือบริการ เป็นต้น มาวิเคราะห์ประเด็น หรือปัญหาต่างๆ ในแง่มุมที่ลึกซึ้งและซับซ้อนอันทำให้ได้ข้อสรุปหรือคำตอบที่ไม่อาจบรรลุได้ด้วยวิธีปรกติธรรมดาทั่วไป'
						WHERE CP_CODE = '303' AND CL_NO = 5 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินติดตามหาความรู้และแนวคิดใหม่ๆ ในสายวิชาชีพ และใช้ความรู้หรือประสบการณ์ในการวิเคราะห์ และการลงมือแก้ไขปัญหาในระยะสั้นที่เกิดขึ้น'
						WHERE CP_CODE = '304' AND CL_NO = 1 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินวิเคราะห์ พลิกแพลง ประยุกต์ และตัดสินใจอย่างมีข้อมูลและเหตุผลในการจัดการปัญหาที่เกิดขึ้นได้อย่างมีประสิทธิภาพ'
						WHERE CP_CODE = '304' AND CL_NO = 2 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินวิเคราะห์ปัญหาที่ผ่านมา คาดการณ์ผลกระทบที่จะเกิดขึ้น และวางแผนล่วงหน้าอย่างเป็นระบบ เพื่อป้องกันหรือหลีกเลี่ยงปัญหาในหน่วยงาน หรือสำนักงานปลัดกระทรวงอุตสาหกรรม'
						WHERE CP_CODE = '304' AND CL_NO = 3 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินวิเคราะห์ และผสมผสาน (Integrate) แนวคิดในเชิงสหวิทยาการ เพื่อหลีกเลี่ยง ป้องกัน หรือแก้ไขปัญหาที่มีความซับซ้อนทั้งในระยะสั้นและระยะยาวอย่างมีประสิทธิภาพ'
						WHERE CP_CODE = '304' AND CL_NO = 4 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินปรับเปลี่ยนองค์กรให้มีการบูรณาการในเชิงวิชาชีพและสร้างให้มีความเชี่ยวชาญในสายอาชีพอย่างแท้จริง เพื่อสามารถแก้ไข ป้องกันและหลีกเลี่ยงปัญหาที่มีผลกระทบสูงหรือมีความซับซ้อนสูงขององค์กรได้อย่างยั่งยืน รวมทั้งเป็นผู้นำที่มีความเชี่ยวชาญในสายอาชีพที่สามารถป้องกัน และหลีกเลี่ยงปัญหาที่มีผลกระทบเชิงนโยบาย และกลยุทธ์ขององค์กรให้เกิดประโยชน์อย่างยั่งยืนแก่องค์กรในระยะยาว'
						WHERE CP_CODE = '304' AND CL_NO = 5 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินปฏิบัติงานโดยคำนึงถึงความคุ้มค่าและค่าใช้จ่ายที่เกิดขึ้น'
						WHERE CP_CODE = '305' AND CL_NO = 1 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินลดค่าใช้จ่ายต่างๆ ที่จะเกิดขึ้น และจัดสรรงบประมาณ ค่าใช้จ่าย หรือทรัพยากรที่มีอยู่อย่างจำกัดให้คุ้มค่าและเกิดประโยชน์ในการปฏิบัติงานอย่างสูงสุด'
						WHERE CP_CODE = '305' AND CL_NO = 2 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินประเมินผลความมีประสิทธิภาพของการดำเนินงานที่ผ่านมาเพื่อปรับปรุงการจัดสรรทรัพยากรให้ได้ผลผลิตที่เพิ่มขึ้น หรือมีการทำงานที่มีประสิทธิภาพมากขึ้น หรือมีค่าใช้จ่ายที่ลดลง'
						WHERE CP_CODE = '305' AND CL_NO = 3 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินวางแผนและเชื่อมโยงภารกิจของหน่วยงานตนเองกับหน่วยงานอื่น (Synergy) เพื่อให้การใช้ทรัพยากรของหน่วยงานที่เกี่ยวข้องทั้งหมดเกิดประโยชน์สูงสุด'
						WHERE CP_CODE = '305' AND CL_NO = 4 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินพัฒนากระบวนการใหม่ๆ โดยอาศัยวิสัยทัศน์ ความเชี่ยวชาญ และประสบการณ์มาประยุกต์ในการทำงาน เพื่อลดภาระการบริหารงานได้อย่างมีประสิทธิภาพสูงสุด และเพิ่มผลผลิตหรือสร้างสรรค์งานใหม่ที่โดดเด่นแตกต่างให้กับองค์กร โดยใช้ทรัพยากรเท่าเดิม'
						WHERE CP_CODE = '305' AND CL_NO = 5 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินแสดงความเชื่อมั่นว่าผู้อื่นสามารถจะเรียนรู้ ปรับปรุงผลงาน และพัฒนาศักยภาพและความสามารถของตนเองได้'
						WHERE CP_CODE = '306' AND CL_NO = 1 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินสอนงานในรายละเอียดหรือให้คำแนะนำที่เฉพาะเจาะจงที่เกี่ยวกับวิธีการปฏิบัติงานโดยมุ่งพัฒนาขีดความสามารถของบุคคลนั้น'
						WHERE CP_CODE = '306' AND CL_NO = 2 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินให้เหตุผลและแนวทางที่เป็นประโยชน์ในการประกอบการสอนและคำแนะนำ หรือสาธิตวิธีปฏิบัติงานเพื่อเป็นตัวอย่างในการปฏิบัติงานจริง รวมทั้งให้ความสนันสนุนในด้านต่างๆ เช่น ทรัพยากร อุปกรณ์ ข้อมูล เป็นต้น เพื่อให้ปฏิบัติงานได้ง่ายขึ้น'
						WHERE CP_CODE = '306' AND CL_NO = 3 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินให้คำติชมเรื่องผลงานอย่างตรงไปตรงมาทั้งด้านบวกและด้านลบโดยปราศจากอคติส่วนตัว เพื่อส่งเสริมให้มีการพัฒนาความรู้ ความสามารถและปรับปรุงผลงานอย่างต่อเนื่อง รวมทั้งให้คำแนะนำที่เฉพาะเจาะจง สอดคล้องกับบุคลิก ความสนใจ และความสามารถเฉพาะบุคคล เพื่อปรับปรุงพัฒนาความรู้และความสามารถได้อย่างเหมาะสม'
						WHERE CP_CODE = '306' AND CL_NO = 4 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินพัฒนาศักยภาพบุคลากรในระยะยาว เพื่อเพิ่มผลงานที่มีประสิทธิภาพต่อสำนักงานปลัดกระทรวงอุตสาหกรรมอย่างต่อเนื่อง รวมทั้งรณรงค์ ส่งเสริม และผลักดันให้มีแผนหรือโครงการพัฒนาความรู้ความสามารถของบุคลากรอย่างเป็นรูปธรรม เพื่อสร้างวัฒนธรรมองค์กรให้มีการส่งเสริมการเรียนรู้อย่างเป็นระบบ'
						WHERE CP_CODE = '306' AND CL_NO = 5 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินใช้กฏเกณฑ์ขั้นพื้นฐาน สามัญสำนึก หรือประสบการณ์ในการระบุปัญหา และพิจารณาสถานการณ์ เมื่อเหตุการณ์ที่ประสบนั้นมีลักษณะเหมือนกับที่เคยประสบมา'
						WHERE CP_CODE = '307' AND CL_NO = 1 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินประยุกต์ใช้ประสบการณ์ในการระบุประเด็น และพิจารณาสถานการณ์ และสามารถระบุปัญหาในสถานการณ์ปัจจุบันที่อาจมีความคล้ายคลึง หรือต่างจากประสบการณ์ที่เคยประสบมาได้ โดยการพิจารณาข้อมูล การมองเห็นแบบแผน แนวโน้ม หรือระบุสิ่งที่ขาดหายไปในสถานการณ์นั้นๆ ได้'
						WHERE CP_CODE = '307' AND CL_NO = 2 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินประยุกต์ใช้ความรู้ทางทฤษฎี และประสบการณ์ที่หลากหลายในการพิจารณาสถานการณ์ และสามารถปรับปรุง หรือเปลี่ยนแบบแผน หรือวิธีการที่ได้เรียนรู้มา และปรับใช้ในสถานการณ์ได้อย่างเหมาะสม'
						WHERE CP_CODE = '307' AND CL_NO = 3 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินกล้าเผชิญหน้าและต่อรองให้บุคคลหรือหน่วยงานที่ฝ่าฝืนกฎเกณฑ์ ระเบียบ นโยบายหรือมาตรฐานที่ตั้งไว้ไปปรับปรุงแก้ไข แม้ว่าอาจจะก่อให้เกิดศัตรูหรือความไม่พึงพอใจให้แก่ผู้ที่เกี่ยวข้อง'
						WHERE CP_CODE = '307' AND CL_NO = 4 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินยืนหยัดพิทักษ์ผลประโยชน์ตามกฎเกณฑ์และกฎระเบียบขององค์กร แม้ในสถานการณ์ที่อาจเสี่ยงต่อความมั่นคงในตำแหน่งหน้าที่การงาน หรืออาจเสี่ยงภัยต่อชีวิต'
						WHERE CP_CODE = '307' AND CL_NO = 5 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินปฏิบัติหน้าที่ด้วยความโปร่งใส  ถูกต้องทั้งตามมาตรฐาน หลักกฎหมายและระเบียบข้อบังคับที่กำหนดไว้'
						WHERE CP_CODE = '308' AND CL_NO = 1 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินกล้าที่จะปฏิเสธข้อเรียกร้อง/ข้อเสนอที่ผิดกฎระเบียบ รวมทั้งมุ่งมั่นปฏิบัติหน้าที่โดยไม่บิดเบือนและอ้างข้อยกเว้นให้ตนเองหรือผู้อื่นที่รู้จัก'
						WHERE CP_CODE = '308' AND CL_NO = 2 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินหมั่นกำกับควบคุมตรวจตราการดำเนินการของบุคคลหรือหน่วยงานที่ดูแลรับผิดชอบให้เป็นไปอย่างถูกต้องตามกฎระเบียบหรือแนวทางนโยบายที่วางไว้'
						WHERE CP_CODE = '308' AND CL_NO = 3 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินกล้าเผชิญหน้าและต่อรองให้บุคคลหรือหน่วยงานที่ฝ่าฝืนกฎเกณฑ์ ระเบียบ นโยบายหรือมาตรฐานที่ตั้งไว้ไปปรับปรุงแก้ไข แม้ว่าอาจจะก่อให้เกิดศัตรูหรือความไม่พึงพอใจให้แก่ผู้ที่เกี่ยวข้อง'
						WHERE CP_CODE = '308' AND CL_NO = 4 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินยืนหยัดพิทักษ์ผลประโยชน์ตามกฎเกณฑ์และกฎระเบียบขององค์กร แม้ในสถานการณ์ที่อาจเสี่ยงต่อความมั่นคงในตำแหน่งหน้าที่การงาน หรืออาจเสี่ยงภัยต่อชีวิต'
						WHERE CP_CODE = '308' AND CL_NO = 5 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินวางแผนงานเป็นขั้นตอนอย่างชัดเจน มีผลลัพธ์ สิ่งที่ต้องจัดเตรียม และกิจกรรมต่างๆ ที่จะเกิดขึ้นอย่างถูกต้อง'
						WHERE CP_CODE = '309' AND CL_NO = 1 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินจัดลำดับของงานและผลลัพธ์ในโครงการ รวมทั้งวิเคราะห์หาข้อดี ข้อเสียและผลต่อเนื่องของแผนงานในอดีต เพื่อสามารถวางแผนงานใหม่ได้อย่างมีประสิทธิภาพสูงสุด'
						WHERE CP_CODE = '309' AND CL_NO = 2 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินวางแผนงานที่มีความเชื่อมโยงหรือซับซ้อนกันหลายๆ งานหรือหลายๆ โครงการ ได้อย่างมีประสิทธิภาพ สอดคล้อง และไม่ขัดแย้งกัน'
						WHERE CP_CODE = '309' AND CL_NO = 3 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินวางแผนและคาดการณ์ล่วงหน้าเกี่ยวกับปัญหาหรือกิจกรรมที่มีหน่วยงานหรือผู้เกี่ยวข้องหลายฝ่าย แล้วเตรียมทางเลือกสำหรับการป้องกัน/แก้ไขปัญหาที่เกิดขึ้นได้อย่างมีประสิทธิภาพ'
						WHERE CP_CODE = '309' AND CL_NO = 4 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินปรับกลยุทธ์ แผนงาน และวางแผนอย่างรัดกุมและเป็นระบบให้เข้ากับสถานการณ์ที่เกิดขึ้นอย่างไม่คาดคิด เพื่อแก้ปัญหา อุปสรรค หรือสร้างโอกาสนั้นๆ อย่างมีคุณภาพสูงสุดและสามารถบรรลุวัตถุประสงค์ และเป้าหมายที่กำหนดไว้อย่างมีประสิทธิภาพสูงสุด'
						WHERE CP_CODE = '309' AND CL_NO = 5 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินเข้าใจนโยบาย กลยุทธ์ของกระทรวงอุตสาหกรรม และ สำนักงานปลัดกระทรวงอุตสาหกรรม และสามารถนำความเข้าใจนั้นมาวิเคราะห์ปัญหา อุปสรรคหรือโอกาสขององค์กรหรือหน่วยงานออกเป็นประเด็นย่อยๆ ได้'
						WHERE CP_CODE = '310' AND CL_NO = 1 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินประยุกต์ความเข้าใจ รูปแบบหรือประสบการณ์ไปสู่ข้อเสนอหรือแนวทางต่างๆ ในงานได้อย่างมีประสิทธิภาพสูงสุด'
						WHERE CP_CODE = '310' AND CL_NO = 2 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินประยุกต์ Best Practice ทฤษฎีหรือแนวคิดใหม่ๆ ที่ซับซ้อนในการกำหนดแผนงานหรือข้อเสนอต่างๆ ได้อย่างมีประสิทธิภาพสูงสุด'
						WHERE CP_CODE = '310' AND CL_NO = 3 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินประเมินและสังเคราะห์สถานการณ์หรือปัญหาทางเศรษฐกิจ สังคม การเมืองภายในประเทศและต่างประเทศที่ซับซ้อน เพื่อใช้ในการกำหนดแผนหรือนโยบายของหน่วยงาน หรือองค์กรได้อย่างมีประสิทธิภาพสูงสุด'
						WHERE CP_CODE = '310' AND CL_NO = 4 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินวิเคราะห์ปัญหา และสถานการณ์ในแง่มุมที่ลึกซึ้งโดยพิจารณาจากบริบทประเทศไทย สังคม เศรษฐกิจ ระบบอุตสาหกรรมในภาพรวมที่ซับซ้อน อันนำไปสู่การประดิษฐ์คิดค้น สร้างสรรค์ และนำเสนอองค์ความรู้ใหม่ๆ ที่ไม่เคยปรากฏมาก่อนและเป็นประโยชน์ต่อองค์กร หรือสังคมและประเทศชาติโดยรวม'
						WHERE CP_CODE = '310' AND CL_NO = 5 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินผูกมิตรและสร้างสัมพันธภาพที่ดีระหว่างเครือข่ายพันธมิตรเชิงกลยุทธ์ (เช่น ภาครัฐอื่นๆ ผู้ประกอบการ สถาบันการศึกษา เป็นต้น) รวมทั้งสื่อสารข้อมูลหรือข่าวสารที่จำเป็นให้พันธมิตรได้เข้าใจและรับรู้อยู่เสมอ'
						WHERE CP_CODE = '311' AND CL_NO = 1 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินเข้าใจปัญหาหรือประเด็นความรู้สึกหรือความต้องการของพันธมิตรอย่างลึกซึ้ง และหาทางปรับปรุง เพื่อประโยชน์และความสัมพันธ์ที่ดีขึ้นทั้งสองฝ่าย'
						WHERE CP_CODE = '311' AND CL_NO = 2 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินคิดแนวทางหรือกิจกรรมใหม่ๆ เพื่อช่วยเหลือและรักษาความร่วมมือหรือความสัมพันธ์ที่ดีกับพันธมิตรได้ยามจำเป็น ทั้งนี้เพื่อให้งานสำเร็จลุล่วง'
						WHERE CP_CODE = '311' AND CL_NO = 3 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินเน้นสรรสร้างผลประโยชน์ร่วมกันระหว่างสำนักงานปลัดกระทรวงอุตสาหกรรม กับเครือข่ายพันธมิตร แม้ต้องเสียประโยชน์ในระยะสั้น แต่สามารถสร้างมิตรภาพและผลประโยชน์ร่วมกันในระยะยาว'
						WHERE CP_CODE = '311' AND CL_NO = 4 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินสร้าง และรักษามิตรภาพกับเครือข่ายพันธมิตรในระยะยาวจนเกิดเป็นความไว้ใจและเป็นพลังที่เข็มแข็งเพื่อสรรสร้างให้เกิดประโยชน์สูงสุดร่วมกัน รวมทั้งเป็นที่รู้จักอย่างกว้างขวางในแวดวงที่เกี่ยวข้องอันเป็นผลมาจากการผูกมิตรและสานสัมพันธ์อย่างจริงใจแก่พันธมิตรประเภทต่างๆ จำนวนมากมาเป็นเวลานาน'
						WHERE CP_CODE = '311' AND CL_NO = 5 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินหาข้อมูลและสรุปผลข้อมูลเพื่อแสดงผลข้อมูลในรูปแบบต่างๆ เช่น กราฟ รายงาน ได้อย่างถูกต้องและครบถ้วน'
						WHERE CP_CODE = '312' AND CL_NO = 1 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินสืบเสาะปัญหาหรือสถานการณ์อย่างลึกซึ้ง จนได้มาซึ่งแก่นหรือประเด็นของเนื้อหา ที่สามารถนำมาจัดการ วิเคราะห์ และประเมินผลให้เกิดประโยชน์แก่หน่วยงานได้'
						WHERE CP_CODE = '312' AND CL_NO = 2 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินหมั่นค้นหาข้อมูลเจาะลึกอย่างต่อเนื่อง เพื่อให้เข้าใจถึงมุมมอง ทัศนะ ต้นตอของปัญหา หรือโอกาสที่ซ่อนเร้นอยู่ในเบื้องลึก จนสามารถนำมาจัดการ วิเคราะห์ และประเมินผลให้เกิดประโยชน์สูงสุดแก่หน่วยงานได้'
						WHERE CP_CODE = '312' AND CL_NO = 3 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินจัดทำการวิจัยอย่างเป็นระบบหรือเป็นไปตามหลักการทางสถิติ และนำผลที่ได้นั้นมาพยากรณ์หรือสร้างแบบจำลองหรือสร้างระบบที่เกิดประโยชน์สูงสุดต่อหน่วยงานได้'
						WHERE CP_CODE = '312' AND CL_NO = 4 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินวางระบบการสืบค้น เพื่อให้มีข้อมูลที่ทันเหตุการณ์ป้อนเข้ามาอย่างต่อเนื่องและสามารถออกแบบ เลือกใช้ หรือประยุกต์วิธีการในการจัดทำแบบจำลองหรือระบบต่างๆ ได้อย่างถูกต้องและมีนัยสำคัญ'
						WHERE CP_CODE = '312' AND CL_NO = 5 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินนำเสนอข้อมูล อธิบาย หรือชี้แจงรายละเอียดแก่ผู้ฟังอย่างตรงไปตรงมาโดยอิงข้อมูลที่มีอยู่ได้'
						WHERE CP_CODE = '313' AND CL_NO = 1 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินมักจะเตรียมการนำเสนอข้อมูลเป็นอย่างดี และใช้ความพยายามเจรจาโน้มน้าวใจโดยยกหลักการและเหตุผลที่เกี่ยวข้องมาประกอบการนำเสนออย่างมีขั้นตอน'
						WHERE CP_CODE = '313' AND CL_NO = 2 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินประยุกต์ใช้ความเข้าใจ ความสนใจของผู้ฟังให้เป็นประโยชน์ในเจรจาเสนอข้อมูล นำเสนอหรือเจรจาโดยคาดการณ์ถึงปฏิกิริยา ผลกระทบที่จะมีต่อผู้ฟังเป็นหลัก'
						WHERE CP_CODE = '313' AND CL_NO = 3 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินวางกลยุทธ์และใช้ทักษะในการโน้มน้าวใจทางอ้อม เพื่อให้ได้ผลสัมฤทธิ์ดังประสงค์โดยคำนึงถึงผลกระทบและความรู้สึกของผู้อื่นเป็นสำคัญ'
						WHERE CP_CODE = '313' AND CL_NO = 4 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินสร้างกลุ่มแนวร่วม และประยุกต์ใช้หลักจิตวิทยามวลชนหรือจิตวิทยากลุ่มให้เป็นประโยชน์ในการเจรจาโน้มน้าวจูงใจให้มีน้ำหนัก สัมฤทธิ์ผลได้ดียิ่งขึ้น และมีประสิทธิภาพสูงสุด'
						WHERE CP_CODE = '313' AND CL_NO = 5 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินนำเสนอความเห็นอย่างตรงไปตรงมา ชัดเจนและถูกต้องในการอภิปรายหรือนำเสนอผลงานเพื่อให้ผู้รับนำไปใช้ต่อได้ รวมทั้งใช้ภาษาที่ถูกต้องและเหมาะสมในการสื่อสาร'
						WHERE CP_CODE = '314' AND CL_NO = 1 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินอธิบาย ชี้แจง สื่อสารงาน ความหมาย ใจความสำคัญ นโยบาย ระเบียบ หรือข้อบังคับให้ผู้ฟังเข้าใจได้โดยง่าย และฟัง อ่าน จับประเด็นและสรุปประเด็นทั้งความหมายโดยตรงและโดยนัยได้อย่างถูกต้อง และชัดเจน'
						WHERE CP_CODE = '314' AND CL_NO = 2 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินเป็นผู้ฟังที่ดี สามารถตั้งคำถามเพื่อให้เกิดความเข้าใจอย่างถ่องแท้เกี่ยวกับเนื้อหาสาระต่างๆ และนำมาสรุปเนื้อหาเชิงลึก (Insight) ให้ผู้อื่นเข้าใจได้ รวมทั้งปรับรูปแบบการนำเสนอและอภิปรายให้เหมาะสมกับความสนใจและระดับของผู้ฟัง ตลอดจนคาดการณ์ถึงผลกระทบของสิ่งที่นำเสนอและภาพพจน์ของผู้พูดที่จะมีต่อผู้ฟังได้อย่างถูกต้อง'
						WHERE CP_CODE = '314' AND CL_NO = 3 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินวิเคราะห์สถานการณ์และกลุ่มผู้รับสารได้อย่างถ่องแท้จนสามารถปรับภาษาที่ใช้ในการสื่อสารและวิธีการนำเสนอให้เหมาะสมกับผู้ฟังกลุ่มต่างๆ ได้อย่างมีประสิทธิภาพ รวมทั้งใช้ถ้อยคำ สำนวนภาษา และสื่อสารถ่ายทอดที่เสริมสร้างประโยชน์หรือลดปัญหาได้อย่างมีประสิทธิภาพให้กลุ่มบุคคลหรือหน่วยงานต่างๆ ยอมรับหรือปฏิบัติตามด้วยความเต็มใจได้'
						WHERE CP_CODE = '314' AND CL_NO = 4 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินใช้ความเข้าใจด้านการเมือง เศรษฐกิจ สังคม หรือนโยบายของกระทรวงอุตสาหกรรมในการสร้างแนวร่วมหรือส่งผลกระทบต่อผู้ฟัง และถ่ายทอดหรือนำเสนอข้อมูลที่เสริมสร้างประโยชน์ต่อส่วนรวม และสร้างภาพลักษณ์ที่ดีของสำนักงานปลัดกระทรวงอุตสาหกรรม หรือกระทรวงอุตสาหกรรมต่อสังคมโดยรวม'
						WHERE CP_CODE = '314' AND CL_NO = 5 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินเข้าใจความหมายที่ผู้อื่นต้องการสื่อสาร และสามารถจับใจความ และสรุปเนื้อหาเรื่องราวได้ถูกต้องและครบประเด็น'
						WHERE CP_CODE = '315' AND CL_NO = 1 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินเข้าใจทั้งเนื้อหาของสารและนัยเชิงอารมณ์ (จากการสังเกต อวัจนภาษา เช่น ท่าทาง การแสดงออกทางสีหน้า หรือน้ำเสียง เป็นต้น) ของผู้ที่ติดต่อด้วย'
						WHERE CP_CODE = '315' AND CL_NO = 2 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินเข้าใจความหมายแฝงในอากัปกิริยาและคำพูดที่ไม่ได้แสดงออกอย่างชัดเจนของผู้อื่น และสามารถระบุลักษณะนิสัยหรือจุดเด่นอย่างใดอย่างหนึ่งของผู้ที่ติดต่อด้วยได้'
						WHERE CP_CODE = '315' AND CL_NO = 3 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินเข้าใจในนัยของพฤติกรรมและอารมณ์ความรู้สึกของผู้อื่น และใช้ความเข้าใจในการสื่อสารทั้งที่เป็นคำพูด และความหมายนัยแฝงในการสื่อสารกับผู้นั้นได้อย่างถูกต้องและเหมาะสม'
						WHERE CP_CODE = '315' AND CL_NO = 4 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินเข้าใจสาเหตุเบื้องลึกหรือปัญหาของพฤติกรรมผู้อื่น ตลอดจนเข้าใจสาเหตุหรือแรงจูงใจในระยะยาวของพฤติกรรมและความรู้สึกของผู้อื่น และมีจิตวิทยาในการใช้ความเข้าใจผู้อื่นเพื่อเป็นพื้นฐานในการทำงานร่วมกัน การเจรจาทำความเข้าใจ การให้ความรู้หรือให้บริการ การสอนงาน หรือการพัฒนาบุคลากรตามภารกิจในงาน'
						WHERE CP_CODE = '315' AND CL_NO = 5 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินวางแผนงานบนพื้นฐานของความเข้าใจเทคโนโลยี ระบบ กระบวนการทำงานและมาตรฐานในงาน'
						WHERE CP_CODE = '316' AND CL_NO = 1 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินวิเคราะห์ข้อดี ข้อเสียและผลต่อเนื่องของเทคโนโลยี ระบบ และกระบวนการทำงานต่างๆ ของหน่วยงานเพื่อมาประกอบการวางแผนจัดลำดับความสำคัญและผลลัพธ์ของงานได้อย่างมีประสิทธิภาพสูงสุด'
						WHERE CP_CODE = '316' AND CL_NO = 2 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินมองภาพรวมแล้ววางแผนหรือเชื่อมโยงงานหรือกิจกรรมต่างๆ ที่มีความซับซ้อน เพื่อให้บรรลุตามเป้าหมายที่หน่วยงานกำหนดไว้ได้อย่างมีประสิทธิภาพสูงสุด'
						WHERE CP_CODE = '316' AND CL_NO = 3 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินเข้าใจกระแสภายนอกกับผลกระทบโดยรวมต่อเทคโนโลยี ระบบหรือกระบวนการทำงานของหน่วยงาน แล้วนำมาวางแผนและคาดการณ์ล่วงหน้าเพื่อเตรียมทางเลือกสำหรับการป้องกัน/แก้ไขปัญหาที่เกิดขึ้น'
						WHERE CP_CODE = '316' AND CL_NO = 4 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินเข้าใจสถานะของระบบ เทคโนโลยี และกระบวนการการทำงานของ สำนักงานปลัดกระทรวงอุตสาหกรรม และกระทรวงอุตสาหกรรมอย่างถ่องแท้ จนสามารถปรับกลยุทธ์และวางแผนอย่างรัดกุม เพื่อดำเนินการเปลี่ยนแปลงในภาพรวมให้องค์กร และกระทรวงอุตสาหกรรมเติบโตอย่างยั่งยืน'
						WHERE CP_CODE = '316' AND CL_NO = 5 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินเข้าใจเทคโนโลยี ระบบ กระบวนการทำงาน กฎระเบียบและมาตรฐานในหน่วยงานที่สังกัด'
						WHERE CP_CODE = '317' AND CL_NO = 1 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินเข้าใจและเชื่อมโยงเทคโนโลยี ระบบ กระบวนการทำงาน ในหน่วยงานจนสามารถนำมาปรับใช้เพื่อให้การทำงานเป็นไปอย่างมีประสิทธิภาพ'
						WHERE CP_CODE = '317' AND CL_NO = 2 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินมองภาพรวม และเข้าใจข้อจำกัดของเทคโนโลยี ระบบหรือกระบวนการทำงานในหน่วยงาน แล้วเสนอการปรับเปลี่ยนหรือปรับปรุงให้การทำงานเป็นไปอย่างมีประสิทธิภาพสูงขึ้น'
						WHERE CP_CODE = '317' AND CL_NO = 3 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินเข้าใจกระแสภายนอกกับผลกระทบโดยรวมต่อเทคโนโลยี ระบบหรือกระบวนการทำงานของหน่วยงาน แล้วนำมาวางแผนและคาดการณ์ล่วงหน้าเพื่อเตรียมทางเลือกสำหรับการป้องกัน/แก้ไขปัญหาที่เกิดขึ้น'
						WHERE CP_CODE = '317' AND CL_NO = 4 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินเข้าใจสถานะของระบบ เทคโนโลยี และกระบวนการการทำงานขององค์กรอย่างถ่องแท้ และกำหนดความต้องการหรือดำเนินการเปลี่ยนแปลงในภาพรวม เพื่อให้องค์กรเติบโตอย่างยั่งยืน'
						WHERE CP_CODE = '317' AND CL_NO = 5 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินตั้งใจทำงานให้ถูกต้อง ละเอียดถี่ถ้วน และสะอาดเรียบร้อย'
						WHERE CP_CODE = '318' AND CL_NO = 1 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินหมั่นตรวจทานความถูกต้องของงานอย่างละเอียดรอบคอบเพื่อให้งานไม่มีข้อผิดพลาดหรือมีความถูกต้องสูงสุด'
						WHERE CP_CODE = '318' AND CL_NO = 2 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินตรวจสอบความถูกต้องของงานของตนและผู้อื่นที่อยู่ในความรับผิดชอบ เพื่อให้งานไม่มีข้อผิดพลาดหรือมีความถูกต้องสูงสุด'
						WHERE CP_CODE = '318' AND CL_NO = 3 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินกำกับติดตามความก้าวหน้าและความถูกต้องในเชิงคุณภาพของผลลัพธ์ของโครงการได้ตามเกณฑ์หรือกำหนดเวลาที่วางไว้อย่างมีประสิทธิภาพสูงสุด'
						WHERE CP_CODE = '318' AND CL_NO = 4 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " UPDATE PER_QUESTION_STOCK SET QS_NAME = 'ผู้รับการประเมินสร้างระบบที่สามารถกำกับตรวจสอบความก้าวหน้า ความถูกต้อง คุณภาพของผลงานในการปฏิบัติงานของผู้อื่น หรือหน่วยงานอื่นได้ รวมทั้งสร้างความชัดเจนของความถูกต้องและคุณภาพของขั้นตอนการทำงานหรือผลงานหรือโครงการโดยละเอียด เพื่อควบคุมให้ผู้อื่นปฏิบัติตามขั้นตอนการทำงานที่วางไว้อย่างถูกต้องและเกิดความโปร่งใสตรวจสอบได้'
						WHERE CP_CODE = '318' AND CL_NO = 5 ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(146, '319', 1, 'ผู้รับการประเมินมีความคล่องตัวในการปฏิบัติงาน และปรับตัวได้ แม้ประสบความยากลำบากทางกายภาพในงาน ไม่ยึดติดกับความสะดวกสบาย วัตถุแสดงฐานะทางสังคม หรือระดับอาวุโสในงาน', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(147, '319', 2, 'ผู้รับการประเมินเต็มใจ และยอมรับความจำเป็นที่จะต้องปรับเปลี่ยน เมื่อได้รับข้อมูลใหม่หรือหลักฐานที่ขัดแย้งกับความคิดเดิม เพื่อสนับสนุนให้องค์กรบรรลุเป้าหมายที่กำหนด', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
	
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(148, '319', 3, 'ผู้รับการประเมินใช้กฎเกณฑ์ กฎระเบียบ และกระบวนการปฏิบัติงานอย่างยืดหยุ่น และสามารถเลือกทางเลือก วิธีการ หรือกระบวนการต่างๆ มาปรับใช้กับสถานการณ์ที่เฉพาะเจาะจงได้อย่างมีประสิทธิภาพ เพื่อให้ได้ผลงานที่ดี', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(149, '319', 4, 'ผู้รับการประเมินปรับเปลี่ยนวิธีการดำเนินงานให้เข้ากับสถานการณ์ หรือปรับแก้ไขกฎระเบียบขั้นตอนการทำงานที่ล้าสมัย เพื่อให้งานมีประสิทธิภาพ', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(150, '319', 5, 'ผู้รับการประเมินปรับเปลี่ยนกฎระเบียบ วิธีการทำงาน และแผนกลยุทธ์ทั้งหมด เพื่อให้เหมาะสมกับสถานการณ์เฉพาะหน้า และเพิ่มประสิทธิภาพของหน่วยงาน และองค์กร', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(151, '320', 1, 'ผู้รับการประเมินมีความเข้าใจความหมายที่ผู้ติดต่อสื่อสาร และสามารถปรับการทำงานให้คล่องตัวและสอดคล้องกับความต้องการได้', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(152, '320', 2, 'ผู้รับการประเมินเต็มใจ ยอมรับ และเข้าใจความคิดเห็นของผู้อื่นทั้งในเชิงเนื้อหาและนัยเชิงอารมณ์ เมื่อสถานการณ์ปรับเปลี่ยนไป เพื่อให้สามารถทำงานได้ตามเป้าหมายที่กำหนดไว้อย่างมีประสิทธิภาพ', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(153, '320', 3, 'ผู้รับการประเมินตีความหมายเบื้องลึกที่ไม่ได้แสดงออกอย่างชัดเจนของบุคคลหรือสถานการณ์ที่เกิดขึ้น แล้วปรับตัวให้สอดคล้อง และเหมาะสมกับกับแต่ละบุคคลหรือสถานการณ์ดังกล่าวได้อย่างมีประสิทธิภาพ', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(154, '320', 4, 'ผู้รับการประเมินใช้ความเข้าใจในเชิงลึกต่อบุคคลหรือสถานการณ์มาปรับเปลี่ยนวิธีการดำเนินงานในหน่วยงานให้ได้ผลสัมฤทธิ์ที่มีประสิทธิภาพสูงสุด', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(155, '320', 5, 'ผู้รับการประเมินปรับแผนกลยุทธ์ทั้งหมด เพื่อให้เหมาะสมกับสถานการณ์เฉพาะหน้า รวมทั้งประยุกต์หลักจิตวิทยาในการใช้ความเข้าใจผู้อื่นในสถานการณ์ต่างๆ เพื่อเป็นพื้นฐานในการเจรจาทำความเข้าใจ หรือดำเนินงานไห้ได้ตามภารกิจขององค์กร', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(156, '321', 1, 'ผู้รับการประเมินเต็มใจและสนับสนุนความคิดสร้างสรรค์ โดยยอมทดลองวิธีอื่นๆ เพื่อมาทดแทนวิธีการที่ใช้อยู่เดิมในการปฏิบัติงานอย่างเต็มใจและใคร่รู้', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(157, '321', 2, 'ผู้รับการประเมินหมั่นสร้างสรรค์และปรับปรุงกระบวนการทำงานของตนอย่างสม่ำเสมอ', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(158, '321', 3, 'ผู้รับการประเมินคิดนอกกรอบเพื่อปรับเปลี่ยนการทำงาน และนำเสนอทางเลือก (Option) หรือแนวทางแก้ปัญหา (Solution) ในงานของตนอย่างสร้างสรรค์', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(159, '321', 4, 'ผู้รับการประเมินริเริ่มสร้างสรรค์สิ่งใหม่ๆ หรือแนวทางใหม่ๆ ในการปฏิบัติงานหรือดำเนินการต่างๆ ให้องค์กรอย่างมีประสิทธิภาพ และมีคุณภาพสูงขึ้น', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE,CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
						QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
						VALUES(160, '321', 5, 'ผู้รับการประเมินสร้างนวัตกรรมในระบบอุตสาหกรรมของประเทศ ตลอดจนองค์ความรู้ใหม่ที่ไม่เคยปรากฏมาก่อนและเป็นประโยชน์ต่อระบบอุตสาหกรรมหรือสังคมและประเทศชาติโดยรวม', 
						0, 0, 1, 1, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		
		$level = array(	"O1", "O2", "O3", "O4", "K1", "K2", "K3", "K4", "K5", "D1", "D2", "M1", "M2" );
		$code = array(	"101", "102", "103", "104", "105", "301", "302", "303", "304", "305", "306", "307", "308", "309", "310", "311", "312", 
										"313", "314", "315", "316", "317", "318", "319", "320", "321" );
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
		
		$cmd = " SELECT DISTINCT PL_CODE, a.ORG_ID FROM PER_POSITION a, PER_PERSONAL b 
						WHERE a.POS_ID = b.POS_ID AND PER_STATUS = 1 AND a.PL_CODE NOT IN ('510108','510209') 
						AND a.DEPARTMENT_ID = $DEPARTMENT_ID
						ORDER BY PL_CODE, a.ORG_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		while($data = $db_dpis->get_array()){
			$PL_CODE = trim($data[PL_CODE]);
			$ORG_ID = $data[ORG_ID];
			$code = "";
			if ($PL_CODE=="510308") { // ผู้อำนวยการ
				if ($ORG_ID==13225) $code = array(	"301", "305", "308" ); // สำนักบริหารกลาง
				elseif ($ORG_ID==12711) $code = array(	"301", "303", "312" ); // ศูนย์เทคโนโลยีสารสนเทศและการสื่อสาร
				elseif ($ORG_ID==13238) $code = array(	"310", "312", "316" ); // สำนักตรวจและประเมินผล
				elseif ($ORG_ID==12714) $code = array(	"310", "312", "316" ); // สำนักนโยบายและยุทธศาสตร์
				elseif ($ORG_ID==12713) $code = array(	"301", "302", "311" ); // สำนักงานที่ปรึกษาด้านอุตสาหกรรมในต่างประเทศ
				else $code = array(	"301", "302", "311" ); // สำนักงานอุตสาหกรรมจังหวัด
			} elseif ($PL_CODE=="510703") { // นักวิเคราะห์นโยบายและแผน
				if ($ORG_ID==12707) $code = array(	"310", "316", "321" ); // ส่วนกลาง
				elseif ($ORG_ID==12714) $code = array(	"310", "312", "316" ); // สำนักนโยบายและยุทธศาสตร์
				elseif ($ORG_ID==13238) $code = array(	"310", "312", "316" ); // สำนักตรวจและประเมินผล
				elseif ($ORG_ID==12711) $code = array(	"301", "312", "321" ); // ศูนย์เทคโนโลยีสารสนเทศและการสื่อสาร
				else $code = array(	"302", "310", "316" ); // สำนักงานอุตสาหกรรมจังหวัด
			} elseif ($PL_CODE=="510903") { // นักทรัพยากรบุคคล
				if ($ORG_ID==13225) $code = array(	"308", "318", "320" ); // สำนักบริหารกลาง
			} elseif ($PL_CODE=="511013") { // นักวิชาการคอมพิวเตอร์
				if ($ORG_ID==12711) $code = array(	"301", "303", "312" ); // ศูนย์เทคโนโลยีสารสนเทศและการสื่อสาร
			} elseif ($PL_CODE=="511104") { // นักจัดการงานทั่วไป
				if ($ORG_ID==13225) $code = array(	"301", "318", "319" ); // สำนักบริหารกลาง
				elseif ($ORG_ID==13238) $code = array(	"301", "318", "319" ); // สำนักตรวจและประเมินผล
				elseif ($ORG_ID==12714) $code = array(	"301", "318", "319" ); // สำนักนโยบายและยุทธศาสตร์
			} elseif ($PL_CODE=="511612") { // เจ้าพนักงานธุรการ
				if ($ORG_ID==13225) $code = array(	"301", "317", "318" ); // สำนักบริหารกลาง
				elseif ($ORG_ID==12711) $code = array(	"301", "317", "318" ); // ศูนย์เทคโนโลยีสารสนเทศและการสื่อสาร
				elseif ($ORG_ID==13238) $code = array(	"301", "317", "318" ); // สำนักตรวจและประเมินผล
				elseif ($ORG_ID==12714) $code = array(	"305", "317", "318" ); // สำนักนโยบายและยุทธศาสตร์
				else $code = array(	"301", "318", "319" ); // สำนักงานอุตสาหกรรมจังหวัด
			} elseif ($PL_CODE=="511712") { // เจ้าพนักงานพัสดุ
				if ($ORG_ID==13225) $code = array(	"305", "308", "318" ); // สำนักบริหารกลาง
			} elseif ($PL_CODE=="511723") { // นักวิชาการพัสดุ
				if ($ORG_ID==13225) $code = array(	"305", "308", "318" ); // สำนักบริหารกลาง
			} elseif ($PL_CODE=="512403") { // นิติกร
				if ($ORG_ID==12707) $code = array(	"303", "308", "318" ); // ส่วนกลาง
				elseif ($ORG_ID==13225) $code = array(	"303", "308", "315", "318" ); // สำนักบริหารกลาง
			} elseif ($PL_CODE=="520412") { // เจ้าพนักงานการเงินและบัญชี
				if ($ORG_ID==13225) $code = array(	"305", "308", "318" ); // สำนักบริหารกลาง
				else $code = array(	"308", "317", "318" ); // สำนักงานอุตสาหกรรมจังหวัด
			} elseif ($PL_CODE=="520423") { // นักวิชาการเงินและบัญชี
				if ($ORG_ID==13225) $code = array(	"305", "308", "318" ); // สำนักบริหารกลาง
			} elseif ($PL_CODE=="520603") { // นักวิชาการตรวจสอบภายใน
				if ($ORG_ID==12707) $code = array(	"308", "317", "318" ); // ส่วนกลาง
			} elseif ($PL_CODE=="523203") { // นักวิชาการอุตสาหกรรม
				if ($ORG_ID==12707) $code = array(	"304", "311", "314" ); // ส่วนกลาง
				elseif ($ORG_ID==12713) $code = array(	"304", "311", "313" ); // สำนักงานที่ปรึกษาด้านอุตสาหกรรมในต่างประเทศ
				else $code = array(	"304", "311", "314" ); // สำนักงานอุตสาหกรรมจังหวัด
			} elseif ($PL_CODE=="523302") { // เจ้าพนักงานส่งเสริมอุตสาหกรรม
				if ($ORG_ID==13238) $code = array(	"310", "312", "316" ); // สำนักตรวจและประเมินผล
			} elseif ($PL_CODE=="523612") { // เจ้าพนักงานทรัพยากรธรณี
				$code = array(	"308", "317", "318" ); // สำนักงานอุตสาหกรรมจังหวัด
			} elseif ($PL_CODE=="523623") { // นักวิชาการทรัพยากรธรณี
				$code = array(	"304", "308", "317" ); // สำนักงานอุตสาหกรรมจังหวัด
			} elseif ($PL_CODE=="531813") { // นักประชาสัมพันธ์
				if ($ORG_ID==13225) $code = array(	"309", "311", "313" ); // สำนักบริหารกลาง
			} elseif ($PL_CODE=="532512") { // เจ้าพนักงานโสตทัศนศึกษา
				if ($ORG_ID==13225) $code = array(	"301", "318", "319" ); // สำนักบริหารกลาง
			} elseif ($PL_CODE=="570103") { // วิศวกร
				$code = array(	"304", "308", "317" ); // สำนักงานอุตสาหกรรมจังหวัด
			} elseif ($PL_CODE=="570603") { // วิศวกรเหมืองแร่
				$code = array(	"304", "308", "317" ); // สำนักงานอุตสาหกรรมจังหวัด
			} elseif ($PL_CODE=="570803") { // วิศวกรโลหการ
				$code = array(	"304", "308", "317" ); // สำนักงานอุตสาหกรรมจังหวัด
			} elseif ($PL_CODE=="571512") { // นายช่างรังวัด
				$code = array(	"301", "317", "318" ); // สำนักงานอุตสาหกรรมจังหวัด
			} elseif ($PL_CODE=="572412") { // นายช่างโลหะ
				$code = array(	"301", "317", "318" ); // สำนักงานอุตสาหกรรมจังหวัด
			} elseif ($PL_CODE=="572802") { // เจ้าพนักงานตรวจโรงงาน
				$code = array(	"301", "308", "318" ); // สำนักงานอุตสาหกรรมจังหวัด
			} elseif ($PL_CODE=="573512") { // นายช่างเทคนิค
				$code = array(	"301", "317", "318" ); // สำนักงานอุตสาหกรรมจังหวัด
			} elseif ($PL_CODE=="573712") { // นายช่างเหมืองแร่
				$code = array(	"301", "317", "318" ); // สำนักงานอุตสาหกรรมจังหวัด
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

		$cmd = " select	POS_ID, PL_CODE, ORG_ID from PER_POSITION where POS_STATUS=1 and DEPARTMENT_ID = $DEPARTMENT_ID order by POS_NO ";		
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
	}

?>