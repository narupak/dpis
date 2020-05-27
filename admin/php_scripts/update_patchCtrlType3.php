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
							  VALUES ('08', 'พนักงานราชการทั่วไป', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_OFF_TYPE (OT_CODE, OT_NAME, OT_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('09', 'พนักงานราชการพิเศษ', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_OFF_TYPE (OT_CODE, OT_NAME, OT_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('10', 'ลูกจ้างชั่วคราว', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('230', 'ประเภทเลื่อนระดับพนักงานราชการ', 2, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('23010', 'เลื่อนระดับดี', 2, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('23020', 'เลื่อนระดับดีเด่น', 2, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('23030', 'ทำสัญญาจ้างครั้งแรก', 2, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('23040', 'ต่อสัญญา', 2, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

	// ความเชี่ยวชาญพิเศษ
			$code = array(	"001", "002", "003", "004", "005", "006", "007", "008", "009", "010", "011", "012", "013", "014", "015", "016", "017", 
											"018", "019", "020", "021", "022" );
			$desc = array(	"ด้านการศึกษา", "ด้านการแพทย์และสาธารณสุข", "ด้านเกษตร", "ด้านทรัพยากรธรรมชาติและสิ่งแวดล้อม", "ด้านวิทยาศาสตร์และเทคโนโลยี", 
											"ด้านวิศวกรรม", "ด้านสถาปัตยกรรม", "ด้านการเงิน การคลัง งบประมาณ", "ด้านสังคม", "ด้านกฏหมาย", "ด้านการปกครอง การเมือง", 
											"ด้านศิลปวัฒนธรรมและศาสนา", "ด้านการวางแผนพัฒนา", "ด้านพาณิชย์และบริการ", "ด้านความมั่นคง", "ด้านการบริหารจัดการและบริหารธุรกิจ", 
											"ด้านการประชาสัมพันธ์", "ด้านการคมนาคมและการสื่อสาร", "ด้านพลังงาน", "ด้านต่างประเทศ", "ด้านอุตสาหกรรม", "ด้านพิธีการ" );
			for ( $i=0; $i<count($code); $i++ ) { 
				$cmd = " INSERT INTO PER_SPECIAL_SKILLGRP (SS_CODE, SS_NAME, SS_ACTIVE, UPDATE_USER, UPDATE_DATE)
								  VALUES ('$code[$i]', '$desc[$i]', 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			} // end for

	// สมรรถนะในแต่ละกลุ่มงาน
			$code = array(	"01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12", "13", "14", "15", "16", "17", "18" );
			$desc = array(	"กลุ่มงานสนับสนุนทั่วไป", "กลุ่มงานสนับสนุนงานหลักทางเทคนิคเฉพาะด้าน", "กลุ่มงานให้คำปรึกษา", "กลุ่มงานบริหาร", 
											"กลุ่มงานนโยบายและวางแผน", "กลุ่มงานศึกษาวิจัยและพัฒนา", "กลุ่มงานข่าวกรองและสืบสวน", "กลุ่มงานออกแบบเพื่อพัฒนา", 
											"กลุ่มงานความสัมพันธ์ระหว่างประเทศ", "กลุ่มงานบังคับใช้กฏหมาย", "กลุ่มงานเผยแพร่ประชาสัมพันธ์", "กลุ่มงานส่งเสริมความรู้", 
											"กลุ่มงานบริการประชาชนด้านสุขภาพและสวัสดิภาพ", "กลุ่มงานบริการประชาชนทางศิลปวัฒนธรรม", 
											"กลุ่มงานบริการประชาชนทางเทคนิคเฉพาะด้าน", "กลุ่มงานเอกสารราชการและทะเบียน", "กลุ่มงานการปกครอง", "กลุ่มงานอนุรักษ์" );
			for ( $i=0; $i<count($code); $i++ ) { 
				$cmd = " INSERT INTO PER_JOB_FAMILY (JF_CODE, JF_NAME, JF_ACTIVE, UPDATE_USER, UPDATE_DATE)
								  VALUES ('$code[$i]', '$desc[$i]', 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			} // end for

			if($SCOMPETENCY[$PER_ID_DEPARTMENT_ID]!="Y"){
				$code = array(	"101", "102", "103", "104", "105", "201", "202", "203", "301", "302", "303", "304", "305", "306", "307", 
										"308", "309", "310", "311", "312", "313", "314", "315", "316", "317", "318", "319", "320" );
				$desc = array(	"การมุ่งผลสัมฤทธิ์", "บริการที่ดี", "การสั่งสมความเชี่ยวชาญในงานอาชีพ", "จริยธรรม", "ความร่วมแรงร่วมใจ", 
										"การวางแผนและการจัดระบบงาน", "การพัฒนาผู้ใต้บังคับบัญชา", "ความเป็นผู้นำ", "การคิดวิเคราะห์", "การมองภาพองค์รวม", 
										"การพัฒนาศักยภาพคน", "การสั่งการตามอำนาจหน้าที่", "การสืบเสาะหาข้อมูล", "ความเข้าใจข้อแตกต่างทางวัฒนธรรม", "ความเข้าใจผู้อื่น", 
										"ความเข้าใจองค์กรและระบบราชการ", "การดำเนินการเชิงรุก", "ความถูกต้องของงาน", "ความมั่นใจในตนเอง", "ความยืดหยุ่นผ่อนปรน", 
										"ศิลปะการสื่อสารจูงใจ", "สภาวะผู้นำ", "สุนทรียภาพทางศิลปะ", "วิสัยทัศน์", "การวางกลยุทธ์ภาครัฐ", "ศักยภาพเพื่อนำการปรับเปลี่ยน", 
										"การควบคุมตนเอง", "การให้อำนาจแก่ผู้อื่น" );
//				$meaning = array(	"NULL", "NULL", "NULL", "NULL", "NULL", "NULL", "NULL", "NULL", "NULL", "NULL", "NULL", "NULL", 
//										"NULL", "NULL", "NULL", "NULL", "NULL", "NULL", "NULL", "NULL", "NULL", "NULL", "NULL", "NULL", "NULL", 
//										"NULL", "NULL", "NULL" );
			} else {
				$code = array(	"101", "102", "103", "104", "105", "201", "202", "203", "204", "205", "206", "301", "302", "303", "304", "305", "306", 
										"307", "308", "309", "310", "311", "312", "313", "314", "315", "316", "317", "318" );
				$desc = array(	"การมุ่งผลสัมฤทธิ์", "บริการที่ดี", "การสั่งสมความเชี่ยวชาญในงานอาชีพ", "จริยธรรม", "ความร่วมแรงร่วมใจ", 
										"สภาวะผู้นำ", "วิสัยทัศน์", "การวางกลยุทธ์ภาครัฐ", "ศักยภาพเพื่อนำการปรับเปลี่ยน", "การควบคุมตนเอง", "การสอนงานและให้อำนาจแก่ผู้อื่น",  
										"การแก้ไขปัญหาแบบมืออาชีพ", "การให้ความรู้และการสร้างสัมพันธ์", "การกำกับติดตามอย่างสม่ำเสมอ", "การค้นหาและการบริหารจัดการข้อมูลความรู้",
										"การคิดวิเคราะห์และบูรณาการ", "การช่างสังเกต", "การดำเนินการเชิงรุก", "การบริหารทรัพยากร", "การยึดมั่นในหลักเกณฑ์", 
										"การวางแผนและความเข้าใจระบบงาน", "การสร้างให้เกิดการมีส่วนร่วมในทุกภาคส่วน", "การสร้างสายสัมพันธ์", "การสื่อสารโน้มน้าวจูงใจ", 
										"ความเข้าใจคนและยืดหยุ่นตามสถานการณ์", "ความคิดสร้างสรรค์", "ความเข้าใจในองค์กรและระบบงาน", "การวางแผนงานล่วงหน้า", 
										"ความถูกต้องของงาน" );
				$meaning = array(	"ความมุ่งมั่นจะปฏิบัติราชการให้ดีหรือให้เกินมาตรฐานที่มีอยู่ โดยมาตรฐานนี้อาจเป็นผลการปฏิบัติงานที่ผ่านมาของตนเอง หรือเกณฑ์วัดผลสัมฤทธิ์ที่ส่วนราชการกำหนดขึ้น อีกทั้งยังหมายรวมถึงการสร้างสรรค์พัฒนาผลงานหรือกระบวนการปฏิบัติงานตามเป้าหมายที่ยากและท้าทายชนิดที่อาจไม่เคยมีผู้ใดสามารถกระทำได้มาก่อน", "สมรรถนะนี้เน้นความตั้งใจและความพยายามของข้าราชการในการให้บริการเพื่อสนองความต้องการของประชาชนตลอดจนของหน่วยงานภาครัฐอื่นๆ ที่เกี่ยวข้อง", "ความขวนขวาย สนใจใฝ่รู้ เพื่อสั่งสมพัฒนาศักยภาพ ความรู้ความสามารถของตนในการปฏิบัติราชการ ด้วยการศึกษา ค้นคว้าหาความรู้ พัฒนาตนเองอย่างต่อเนื่อง อีกทั้งรู้จักพัฒนา ปรับปรุง ประยุกต์ใช้ความรู้เชิงวิชาการและเทคโนโลยีต่างๆ เข้ากับการปฏิบัติงานให้เกิดผลสัมฤทธิ์", "การครองตนและประพฤติปฏิบัติถูกต้องเหมาะสมทั้งตามหลักกฎหมายและคุณธรรมจริยธรรม ตลอดจนหลักแนวทางในวิชาชีพของตนโดยมุ่งประโยชน์ของประเทศชาติมากกว่าประโยชน์ส่วนตน  ทั้งนี้เพื่อธำรงรักษาศักดิ์ศรีแห่งอาชีพข้าราชการ อีกทั้งเพื่อเป็นกำลังสำคัญในการสนับสนุนผลักดันให้ภารกิจหลักภาครัฐบรรลุเป้าหมายที่กำหนดไว้", "สมรรถนะนี้เน้นที่ 1) ความตั้งใจที่จะทำงานร่วมกับผู้อื่น เป็นส่วนหนึ่งในทีมงาน หน่วยงาน หรือองค์กร โดยผู้ปฏิบัติมีฐานะเป็นสมาชิกในทีม มิใช่ในฐานะหัวหน้าทีม และ 2) ความสามารถในการสร้างและดำรงรักษาสัมพันธภาพกับสมาชิกในทีม", "ความตั้งใจหรือความสามารถในการเป็นผู้นำของกลุ่มคน ปกครอง รวมถึงการกำหนดทิศทาง วิสัยทัศน์ เป้าหมาย วิธีการทำงาน ให้ผู้ใต้บังคับบัญชาหรือทีมงานปฏิบัติงานได้อย่างราบรื่น เต็มประสิทธิภาพและบรรลุวัตถุประสงค์ขององค์กร", "ความสามารถให้ทิศทางที่ชัดเจนและก่อความร่วมแรงร่วมใจในหมู่ผู้ใต้บังคับบัญชาเพื่อนำพางานภาครัฐไปสู่จุดหมายร่วมกัน", "ความเข้าใจกลยุทธ์ภาครัฐและสามารถประยุกต์ใช้ในการกำหนดกลยุทธ์ของหน่วยงานตนได้ โดยความสามารถในการประยุกต์นี้รวมถึงความสามารถในการคาดการณ์ถึงทิศทางระบบราชการในอนาคต ตลอดจนผลกระทบของสถานการณ์ทั้งในและต่างประเทศที่เกิดขึ้น", "ความตั้งใจและความสามารถในการกระตุ้นผลักดันกลุ่มคนให้เกิดความต้องการจะปรับเปลี่ยนไปในแนวทางที่เป็นประโยชน์แก่ภาครัฐ รวมถึงการสื่อสารให้ผู้อื่นรับรู้ เข้าใจ และดำเนินการให้การปรับเปลี่ยนนั้นเกิดขึ้นจริง", "การระงับอารมณ์และพฤติกรรมอันไม่เหมาะสมเมื่อถูกยั่วยุ หรือเผชิญหน้ากับฝ่ายตรงข้าม เผชิญความไม่เป็นมิตร หรือทำงานภายใต้สภาวะความกดดัน รวมถึงความอดทนอดกลั้นเมื่อต้องอยู่ภายใต้สถานการณ์ที่ก่อความเครียดอย่างต่อเนื่อง", "ความตั้งใจจะส่งเสริมการเรียนรู้หรือการพัฒนาผู้อื่นในระยะยาว รวมถึงความเชื่อมั่นในความสามารถของผู้อื่น ดังนั้นจึงมอบหมายอำนาจและหน้าที่รับผิดชอบให้เพื่อให้ผู้อื่นมีอิสระในการสร้างสรรค์วิธีการของตนเพื่อบรรลุเป้าหมายในงาน", "ความสามารถในวิเคราะห์ปัญหาหรือเล็งเห็นปัญหา พร้อมทั้งลงมือจัดการกับปัญหานั้นๆ อย่างมีข้อมูล มีหลักการ และสามารถนำความเชี่ยวชาญ หรือแนวคิดในสายวิชาชีพมาประยุกต์ใช้ในการแก้ไขปัญหาได้อย่างมีประสิทธิภาพ", "มีพฤติกรรมที่มุ่งมั่น และตั้งใจที่จะนำภูมิปัญญา นวัตกรรม เทคโนโลยี ความเชี่ยวชาญ และองค์ความรู้ต่างๆ ไปส่งเสริม สนับสนุน และพัฒนาผู้ประกอบการ หรือเครือข่าย ควบคู่ไปกับการสร้าง พัฒนา และรักษาความสัมพันธ์อันดีกับผู้ประกอบการ หรือเครือข่าย เพื่อให้ผู้ประกอบการ หรือเครือข่าย มีความรู้ ความเข้าใจ และสามารถ นำไปใช้พัฒนาหน่วยงานให้มีประโยชน์ อีกทั้งเป็นการเสริมสร้างขีดความสามารถในการแข่งขันอย่างยั่งยืน", "เจตนาที่จะกำกับดูแล และติดตามการดำเนินงานต่างๆ ของผู้อื่นที่เกี่ยวข้องให้ปฏิบัติตามมาตรฐาน กฎระเบียบ หรือข้อบังคับที่กำหนดไว้ โดยอาศัยอำนาจตามระเบียบ กฎหมาย หรือตามตำแหน่งหน้าที่ที่มีอยู่อย่างเหมาะสมและมีประสิทธิภาพโดยมุ่งประโยชน์ของหน่วยงาน องค์กร หรือประเทศชาติเป็นสำคัญ", "ความสามารถในการสืบเสาะ เพื่อให้ได้ข้อมูลเฉพาะเจาะจง การไขปมปริศนาโดยซักถามโดยละเอียด หรือแม้แต่การหาข่าวทั่วไปจากสภาพแวดล้อมรอบตัวโดยคาดว่าอาจมีข้อมูลที่จะเป็นประโยชน์ต่อไปในอนาคต และนำข้อมูลที่ได้มานั้นมาประมวลและจัดการอย่างมีระบบ คุณลักษณะนี้อาจรวมถึงความสนใจใคร่รู้เกี่ยวกับสถานการณ์ ภูมิหลัง ประวัติความเป็นมา ประเด็น ปัญหา หรือเรื่องราวต่างๆ ที่เกี่ยวข้องหรือจำเป็นต่องานในหน้าที่", 
										"ความสามารถในการคิดวิเคราะห์และทำความเข้าใจในเชิงสังเคราะห์ รวมถึงการมองภาพรวมขององค์กร จนได้เป็นกรอบความคิดหรือแนวคิดใหม่ อันเป็นผลมาจากการสรุปรูปแบบ ประยุกต์แนวทางต่างๆ จากสถานการณ์หรือข้อมูลหลากหลาย และนานาทัศนะ", "มีพฤติกรรมในการตั้งข้อสงสัย ข้อสังเกต และสมมติฐานต่างๆ ตลอดจนมีพฤติกรรมในการช่างสังเกตสิ่งแวดล้อม ความเปลี่ยนแปลง ความผิดปกติ และความเป็นไปต่างๆ เพื่อนำไปคาดการณ์ วิเคราะห์ และประเมินเหตุการณ์ เรื่องราว สถานการณ์ ข้อมูล และสิ่งต่างๆ ที่เกิดขึ้นได้อย่างถูกต้อง", "การตระหนักหรือเล็งเห็นโอกาสหรือปัญหาอุปสรรคที่อาจเกิดขึ้นในอนาคต และวางแผน ลงมือกระทำการเพื่อเตรียมใช้ประโยชน์จากโอกาส หรือป้องกันปัญหา ตลอดจนพลิกวิกฤติต่างๆ ให้เป็นโอกาส", "การตระหนักเสมอถึงความคุ้มค่าระหว่างทรัพยากร (งบประมาณ เวลา กำลังคนเครื่องมือ อุปกรณ์ ฯลฯ) ที่ลงทุนไปหรือที่ใช้การปฏิบัติภารกิจ (Input) กับผลลัพธ์ที่ได้ (Output) และพยายามปรับปรุงหรือลดขั้นตอนการปฏิบัติงาน เพื่อพัฒนาให้การปฏิบัติงานเกิดความคุ้มค่าและมีประสิทธิภาพสูงสุด อาจหมายรวมถึงความสามารถในการจัดความสำคัญในการใช้เวลา ทรัพยากร และข้อมูลอย่างเหมาะสม และประหยัดค่าใช้จ่ายสูงสุด", "เจตนาที่จะกำกับดูแลให้ผู้อื่นหรือหน่วยงานอื่นปฏิบัติให้ได้ตามมาตรฐาน กฎระเบียบข้อบังคับที่กำหนดไว้ โดยอาศัยอำนาจตามระเบียบ กฎหมาย หรือตามหลักแนวทางในวิชาชีพของตนที่มีอยู่อย่างเหมาะสมและมีประสิทธิภาพโดยมุ่งประโยชน์ขององค์กร สังคม และประเทศโดยรวมเป็นสำคัญ ความสามารถนี้อาจรวมถึงการยืนหยัดในสิ่งที่ถูกต้องและความเด็ดขาดในการจัดการกับบุคคลหรือหน่วยงานที่ฝ่าฝืนกฎเกณฑ์ ระเบียบหรือมาตรฐานที่ตั้งไว้", "ความสามารถในการวางแผนอย่างเป็นหลักการให้สามารถนำไปปฏิบัติได้จริงและถูกต้อง โดยอาศัยความเข้าใจในเรื่องเทคโนโลยี ระบบ กระบวนการทำงาน และมาตรฐานการทำงานของตนและของหน่วยงานอื่นๆ ที่เกี่ยวข้อง", "การตระหนัก เต็มใจ ยอมรับ และเปิดโอกาสให้ผู้อื่น ประชาชน เครือข่าย กลุ่มบุคคล หรือหน่วยงานต่างๆ เข้ามามีส่วนร่วมในการดำเนินงานของหน่วยงาน หรือองค์กร เพื่อสร้างและส่งเสริมให้เกิดกระบวนการและกลไกการมีส่วนร่วมของทุกภาคส่วนอย่างแท้จริงและยั่งยืน", "ความสามารถในการรักษาและสร้างเครือข่ายพันธมิตรเชิงกลยุทธ์ (เช่น ผู้ประกอบการ สถาบันการศึกษา เจ้าหน้าที่ภาครัฐอื่นๆ เครือข่ายกลุ่มธุรกิจ กลุ่มที่ปรึกษาหรือผู้เชี่ยวชาญ คู่ค้า เป็นต้น) ที่ยั่งยืนและก่อให้เกิดความร่วมมือในการสรรสร้างประโยชน์สูงสุดร่วมกัน", "การใช้วาทศิลป์และกลยุทธ์ต่างๆ ในการสื่อสาร เจรจา โน้มน้าวเพื่อให้ผู้อื่นดำเนินการใดๆ ตามที่ตนหรือหน่วยงานประสงค์", "ความสามารถในการรับฟังและเข้าใจบุคคลหรือสถานการณ์ และพร้อมที่จะปรับเปลี่ยนให้สอดคล้องกับสถานการณ์หรือกลุ่มคนที่หลากหลาย ในขณะที่ยังคงปฏิบัติงานได้อย่างมีประสิทธิภาพและบรรลุผลตามเป้าหมายที่ตั้งไว้", "ความสามารถในการที่จะนำเสนอทางเลือก (Option) หรือแนวทางแก้ปัญหา (Solution) หรือสร้างนวัตกรรม หรือ ริเริ่มสร้างสรรค์กิจกรรมหรือสิ่งใหม่ๆ ที่จะเป็นประโยชน์ต่อองค์กร", "ความเข้าใจและสามารถประยุกต์ใช้ความสัมพันธ์เชื่อมโยงของเทคโนโลยี ระบบ กระบวนการทำงาน และมาตรฐานการทำงานของตนและของหน่วยงานอื่นๆ ที่เกี่ยวข้อง เพื่อประโยชน์ในการปฏิบัติหน้าที่ให้บรรลุผล ความเข้าใจนี้รวมถึงความสามารถในการมองภาพใหญ่ (Big Picture) และการคาดการณ์เพื่อเตรียมการรองรับการเปลี่ยนแปลงของสิ่งต่างๆ ต่อระบบและกระบวนการทำงาน", "ความสามารถในการวางแผนอย่างเป็นหลักการ โดยเน้นให้สามารถนำไปปฏิบัติได้จริงและถูกต้อง รวมถึงความสามารถในการบริหารจัดการโครงการต่างๆ ในความรับผิดชอบให้สามารถบรรลุเป้าหมายที่กำหนดไว้อย่างมีประสิทธิภาพสูงสุด", 
										"ความพยายามที่จะปฏิบัติงานให้ถูกต้องครบถ้วนตลอดจนลดข้อบกพร่องที่อาจจะเกิดขึ้น รวมถึงการควบคุมตรวจตราให้งานเป็นไปตามแผนที่วางไว้อย่างถูกต้องชัดเจน" );
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
							  VALUES ('9999', 'คส. 99.9', 'คำสั่งปรับบัญชีเงินเดือน', '05', 1) ";
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
							  VALUES ('001', 'ปลัดกระทรวงหรือเทียบเท่า', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " UPDATE PER_MGT SET PS_CODE = '001' WHERE PM_CODE IN ('0106', '0108', '0109') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_STATUS (PS_CODE, PS_NAME, PS_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('002', 'อธิบดีหรือเทียบเท่า', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " UPDATE PER_MGT SET PS_CODE = '002' WHERE PM_CODE IN ('0357', '0278', '0282') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_STATUS (PS_CODE, PS_NAME, PS_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('003', 'รองปลัดกระทรวงหรือเทียบเท่า', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " UPDATE PER_MGT SET PS_CODE = '003' WHERE PM_CODE IN ('0266', '0267', '0268') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_STATUS (PS_CODE, PS_NAME, PS_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('004', 'ผู้ว่าราชการจังหวัด', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " UPDATE PER_MGT SET PS_CODE = '004' WHERE PM_CODE IN ('0233') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_STATUS (PS_CODE, PS_NAME, PS_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('005', 'เอกอัครราชทูต', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " UPDATE PER_MGT SET PS_CODE = '005' WHERE PM_CODE IN ('0362', '0363', '0364', '0365') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_STATUS (PS_CODE, PS_NAME, PS_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('006', 'ผู้ตรวจราชการระดับกระทรวง', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " UPDATE PER_MGT SET PS_CODE = '006' WHERE PM_CODE IN ('0216', '0218', '0219') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_STATUS (PS_CODE, PS_NAME, PS_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('007', 'อื่นๆ (ตำแหน่งประเภทบริหารระดับสูง)', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_STATUS (PS_CODE, PS_NAME, PS_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('008', 'รองอธิบดีหรือเทียบเท่า', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " UPDATE PER_MGT SET PS_CODE = '008' WHERE PM_CODE IN ('0273', '0274', '0276') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_STATUS (PS_CODE, PS_NAME, PS_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('009', 'รองผู้ว่าราชการจังหวัดหรือเทียบเท่า', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " UPDATE PER_MGT SET PS_CODE = '009' WHERE PM_CODE IN ('0269') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_STATUS (PS_CODE, PS_NAME, PS_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('010', 'อัครราชทูต', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " UPDATE PER_MGT SET PS_CODE = '010' WHERE PM_CODE IN ('0359', '0360') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_STATUS (PS_CODE, PS_NAME, PS_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('011', 'อื่นๆ (ตำแหน่งประเภทบริหารระดับต้น)', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_STATUS (PS_CODE, PS_NAME, PS_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('012', 'ผอ.สำนักหรือเทียบเท่า', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " UPDATE PER_MGT SET PS_CODE = '012' WHERE PM_CODE IN ('0251', '0252', '0253') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_STATUS (PS_CODE, PS_NAME, PS_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('013', 'หัวหน้าส่วนราชการประจำจังหวัด', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_STATUS (PS_CODE, PS_NAME, PS_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('014', 'อื่นๆ (ตำแหน่งประเภทอำนวยการระดับสูง)', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_STATUS (PS_CODE, PS_NAME, PS_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('015', 'ผู้อำนวยการกองหรือเทียบเท่า', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " UPDATE PER_MGT SET PS_CODE = '015' WHERE PM_CODE IN ('0235', '0237', '0249') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_STATUS (PS_CODE, PS_NAME, PS_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('016', 'หัวหน้าส่วนราชการประจำจังหวัด', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_STATUS (PS_CODE, PS_NAME, PS_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('017', 'อื่นๆ (ตำแหน่งประเภทอำนวยการระดับต้น)', 1, $SESS_USERID, '$UPDATE_DATE') ";
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