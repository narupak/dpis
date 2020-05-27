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
						VALUES(1, 'ความรู้ที่จำเป็นในงาน', 'ระดับวุฒิการศึกษาและความเชี่ยวชาญที่จำเป็นในงาน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO KNOWLEDGE_INFO (JOB_DES_ID, JOB_DES_NAME, JOB_DES_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(2, 'ความรู้เรื่องกฎหมายและกฎระเบียบราชการ', 'ความรู้ในเรื่องกฎหมายตลอดจนกฎระเบียบต่างๆ ที่ต้องใช้ในการปฏิบัติงาน', $SESS_USERID, '$UPDATE_DATE') ";
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
						VALUES(1, 1, 'ระดับที่ 1 คือ สายงานเปิด วุฒิการศึกษาระดับที่กำหนดหรือเทียบเท่าได้ไม่ต่ำกว่านี้ (สำหรับสายงานปิด โปรดดูวุฒิการศึกษาประจำสายงานได้จาก ตารางวุฒิการศึกษาประจำสายงานปิด)', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO KNOWLEDGE_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(1, 2, 'ระดับที่ 2 คือ สายงานเปิด ได้รับปริญญาตรีหรือเทียบเท่าได้ไม่ต่ำกว่านี้ และสามารถประยุกต์ใช้ความรู้ที่ศึกษามาในการปฏิบัติหน้าที่ได้(สำหรับสายงานปิด โปรดดูวุฒิการศึกษาประจำสายงานได้จาก ตารางวุฒิการศึกษาประจำสายงานปิด)', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO KNOWLEDGE_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(1, 3, 'ระดับที่ 3 คือ มีความรู้ในระดับ 2 และมีความเข้าใจและในหลักการ แนวคิด ทฤษฎีของงานในสายอาชีพที่ปฏิบัติอยู่อีกทั้งสามารถให้คำแนะนำแก่เพื่อนร่วมงานได้', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO KNOWLEDGE_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(1, 4, 'ระดับที่ 4 คือ มีความรู้ในระดับที่ 3 และมีความรู้ความเข้าใจอย่างถ่องแท้เกี่ยวกับลักษณะงาน หลักการ แนวคิด ทฤษฎีของงานในสายอาชีพที่ปฏิบัติอยู่ และสามารถนำมาประยุกต์ใช้ให้เข้ากับสถานการณ์ต่างๆ ได้', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO KNOWLEDGE_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(1, 5, 'ระดับที่ 5 คือ มีความรู้ในระดับที่ 4 และมีประสบการณ์กว้างขวางในสาย อาชีพที่ปฏิบัติหน้าที่อยู่ สามารถถ่ายทอดความรู้ให้กับเพื่อนร่วมงานและผู้ใต้บังคับบัญชาได้', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO KNOWLEDGE_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(1, 6, 'ระดับที่ 6 คือ มีความรู้ในระดับที่ 5 และเป็นที่ยอมรับว่าเป็นผู้เชี่ยวชาญในสายอาชีพที่ปฏิบัติหน้าที่อยู่เนื่องจากมีประสบการณ์และความรู้ที่ลึกซึ้งและกว้างขวาง เป็นที่ปรึกษาในการปฏิบัติงานให้กับหน่วยงานอื่นๆได้', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO KNOWLEDGE_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(1, 7, 'ระดับที่ 7 คือ มีความรู้ในระดับที่ 6 และเป็นที่ยอมรับว่าเป็นผู้เชี่ยวชาญในสายอาชีพที่ปฏิบัติหน้าที่อยู่ เป็นที่ปรึกษาอาวุโสให้แก่หน่วยงานอื่นๆ ได้ในระดับสูง', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO KNOWLEDGE_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(2, 1, 'ระดับที่ 1 คือ เข้าใจกฎหมายหรือระเบียบที่เกี่ยวข้องกับงานประจำวันที่ปฏิบัติอยู่', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO KNOWLEDGE_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(2, 2, 'ระดับที่ 2 คือ มีความรู้ระดับที่ 1 และเข้าใจกฎหมายหรือระเบียบที่เกี่ยวข้องอย่างลึกซึ้งและทราบว่าจะหาคำตอบจากที่ใดเมื่อมีข้อสงสัยในงานที่ปฏิบัติอยู่', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO KNOWLEDGE_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(2, 3, 'ระดับที่ 3 คือ มีความรู้ระดับที่ 2 และสามารถนำไปประยุกต์เพื่อวิเคราะห์ปัญหาที่ซับซ้อน อุดช่องโหว่ในกฏหมาย หรือตอบคำถามข้อสงสัยในงานที่ปฏิบัติอยู่ให้หน่วยงานหรือบุคคลที่เกี่ยวข้องได้', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO KNOWLEDGE_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(2, 4, 'ระดับที่ 4 คือ มีความรู้ระดับที่ 3 และเข้าใจ กฎหมายหรือระเบียบอื่นๆ ที่มีความสัมพันธ์เชื่อมโยงกับกฎหมายหรือระเบียบในงานและสามารถนำมาใช้แนะนำหรือให้คำปรึกษาในภาพรวมหากเกิดประเด็นปัญหา', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO KNOWLEDGE_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(2, 5, 'ระดับที่ 5 คือ มีความรู้ระดับที่ 4 และเป็นผู้เชี่ยวชาญทางกฎหมาย สามารถแนะนำ ให้คำปรึกษา วิเคราะห์หาเหตุผลและทางแก้ไขในประเด็นหรือปัญหาที่ไม่เคยเกิดขึ้นได้อย่างมีประสิทธิภาพ', $SESS_USERID, '$UPDATE_DATE') ";
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
						VALUES(1, 'ทักษะการใช้คอมพิวเตอร์', 'ทักษะในการใช้โปรแกรมคอมพิวเตอร์ต่างๆ ได้อย่างมีประสิทธิภาพ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO SKILL_INFO (JOB_DES_ID, JOB_DES_NAME, JOB_DES_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(2, 'ทักษะการใช้ภาษาอังกฤษ', 'ทักษะในการนำภาษาอังกฤษมาใช้ในงาน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO SKILL_INFO (JOB_DES_ID, JOB_DES_NAME, JOB_DES_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(3, 'ทักษะการคำนวณ', 'ทักษะในการทำความเข้าใจและคิดคำนวณข้อมูลต่างๆ ได้อย่างถูกต้อง', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO SKILL_INFO (JOB_DES_ID, JOB_DES_NAME, JOB_DES_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(4, 'ทักษะการบริหารจัดการฐานข้อมูล', 'สามารถเก็บรวบรวมข้อมูลของหน่วยงานได้อย่างเป็นระบบ และเป็นปัจจุบันอยู่เสมอ สะดวกแก่การค้นหาของผู้ที่ต้องการใช้ข้อมูล', $SESS_USERID, '$UPDATE_DATE') ";
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
						VALUES(1, 1, 'ระดับที่ 1 คือ สามารถบันทึกข้อมูลเข้าเครื่องคอมพิวเตอร์ตามที่คู่มือการใช้ระบุไว้ได้อย่างมีประสิทธิภาพ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO SKILL_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(1, 2, 'ระดับที่ 2 คือ มีทักษะระดับที่ 1 และสามารถใช้โปรแกรมคอมพิวเตอร์ขั้นพื้นฐานได้', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO SKILL_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(1, 3, 'ระดับที่ 3 คือ มีทักษะระดับที่ 2 และสามารถใช้ในโปรแกรมต่าง ๆ ในการปฏิบัติงานได้', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO SKILL_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(1, 4, 'ระดับที่ 4 คือ มีทักษะระดับที่ 3 และได้รับการยอมรับว่าเป็นผู้ชำนาญในโปรแกรมที่ใช้งานอยู่ประจำในหน่วยงาน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO SKILL_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(1, 5, 'ระดับที่ 5 คือ มีทักษะระดับที่ 4 ได้รับการยอมรับว่าเป็นผู้เชี่ยวชาญในโปรแกรมที่ใช้งานอยู่ประจำในหน่วยงาน และสามารถช่วยชี้แนะหรือซ่อมแซมงานที่ใช้โปรแกรมคอมพิวเตอร์ให้แก่ผู้ร่วมงานที่ประสบปัญหาได้', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO SKILL_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(1, 6, 'ระดับที่ 6 คือ มีทักษะระดับที่ 5 และมีความรู้ในโปรแกรมใหม่ๆที่เกี่ยวข้องในงาน สามารถนำมาประยุกต์ใช้ประโยชน์ในงานได้ดี', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO SKILL_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(1, 7, 'ระดับที่ 7 คือ มีทักษะระดับที่ 6 และมีความเชี่ยวชาญทั้งในโปรแกรมต่างๆที่จำเป็นในงานและโปรแกรมใหม่ๆ ที่อาจเป็นประโยชน์ ตลอดจนสามารถเขียนโปรแกรมเพื่อนำมาใช้ประโยชน์ในงานได้', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO SKILL_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(2, 1, 'ระดับที่ 1 คือ สามารถพูด เขียน  และอ่านภาษาอังกฤษในระดับเบื้องต้นและสื่อสารให้เข้าใจได้', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO SKILL_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(2, 2, 'ระดับที่ 2 คือ สามารถพูด เขียน อ่านและฟังภาษาอังกฤษ และทำความเข้าใจสาระสำคัญของเนื้อหาต่างๆได้', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO SKILL_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(2, 3, 'ระดับที่ 3 คือ มีทักษะระดับที่ 2 และสามารถใช้ภาษาอังกฤษเพื่อการติดต่อสัมพันธ์ในการปฏิบัติงานได้โดยถูกหลักไวยากรณ์', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO SKILL_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(2, 4, 'ระดับที่ 4 คือ มีทักษะระดับที่ 3 สามารถใช้ภาษาอังกฤษเพื่อการติดต่อสื่อสารในงานได้โดยถูกหลีกไวยากรณ์และมีประสิทธิภาพได้เนื้อหาสาระชัดเจนครบถ้วนตามที่ประสงค์', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO SKILL_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(2, 5, 'ระดับที่ 5 คือ มีทักษะระดับที่ 4 สามารถใช้ภาษาอังกฤษเพื่อการติดต่อสื่อสารในงานได้โดยถูกหลักไวยากรณ์ อีกทั้งถูกต้องเหมาะสมในเชิงเนื้อหาและในเชิงนัยแฝงด้วย', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO SKILL_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(2, 6, 'ระดับที่ 6 คือ มีทักษะระดับที่ 5 และเข้าใจสำนวนภาษาอังกฤษในรูปแบบต่างๆ สามารถประยุกต์ใช้ในงานได้อย่างถูกต้องทั้งในเชิงไวยากรณ์ เชิงเนื้อหา และนัยแฝง', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO SKILL_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(2, 7, 'ระดับที่ 7 คือ มีความเชี่ยวชาญในการใช้ภาษาอังกฤษอย่างลึกซึ้งใกล้เคียงกับเจ้าของภาษา สามารถประยุกต์ใช้โวหารทุกรูปแบบได้อย่างคล่องแคล่วและสละสลวยถูกต้อง อีกทั้งมีความเชี่ยวชาญศัพท์เฉพาะด้านในสาขาวิชาของตนอย่างลึกซึ้ง', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO SKILL_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(3, 1, 'ระดับที่ 1 คือ สามารถคำนวณพื้นฐานได้อย่างคล่องแคล่ว รวดเร็ว และถูกต้อง', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO SKILL_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(3, 2, 'ระดับที่ 2 คือ มีทักษะระดับที่ 1 และสามารถทำความเข้าใจข้อมูลตัวเลขได้อย่างถูกต้อง', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO SKILL_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(3, 3, 'ระดับที่ 3 คือ มีทักษะระดับที่ 2 และสามารถวิเคราะห์ข้อมูลตัวเลขได้อย่างมีประสิทธิภาพ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO SKILL_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(3, 4, 'ระดับที่ 4 คือ มีทักษะระดับที่ 3 และสามารถระบุ ตลอดจนแก้ไขข้อผิดพลาดในข้อมูลตัวเลข วิเคราะห์และตีความข้อมูลตัวเลขได้อย่างมีประสิทธิภาพ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO SKILL_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(3, 5, 'ระดับที่ 5 คือ มีทักษะระดับที่ 4 และสามารถสรุปผล นำเสนอประเด็นสำคัญๆ ตลอดจนให้ข้อเสนอแนะโดยอ้างอิงผลการวิเคราะห์ข้อมูลตัวเลขได้', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO SKILL_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(3, 6, 'ระดับที่ 6 คือ มีทักษะระดับที่ 5 และสามารถใช้สูตรคณิตศาสตร์ต่างๆในการวิเคราะห์ข้อมูลตัวเลขเพื่อหาแนวโน้ม นำเสนอ หรือสรุปประเด็นที่ซับซ้อนได้', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO SKILL_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(3, 7, 'ระดับที่ 7 คือ มีทักษะระดับที่ 6 และสามารถนำข้อมูลตัวเลขต่างๆ มาเชื่อมโยงเพื่ออธิบายชี้แจงประเด็นในระดับกลยุทธ์ ตลอดจนนำเสนอทางเลือก ข้อดีช้อเสียต่างๆ ให้เป็นที่เข้าใจได้โดยง่าย', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO SKILL_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(4, 1, 'ระดับที่ 1 คือ สามารถเก็บรวบรวมข้อมูลของหน่วยงานได้อย่างเป็นระบบ และเป็นปัจจุบันอยู่เสมอ สะดวกแก่การค้นหาของผู้ที่ต้องการใช้ข้อมูล', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO SKILL_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(4, 2, 'ระดับที่ 2 คือ มีทักษะในระดับที่ 1 และสามารถแสดงผลข้อมูลในรูปแบบต่างๆ เช่น กราฟ รายงาน ฯลฯ ได้', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO SKILL_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(4, 3, 'ระดับที่ 3 คือ มีทักษะระดับที่ 2 และสามารถกำหนดวิธีวิเคราะห์ และประเมินผลข้อมูลได้อย่างถูกต้องแม่นยำ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO SKILL_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(4, 4, 'ระดับที่ 4 คือ มีทักษะระดับที่ 3 และสามารถสรุปผลการวิเคราะห์ นำเสนอทางเลือก ข้อดีข้อเสีย ฯลฯ โดยอ้างอิงข้อมูลที่มีอยู่', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO SKILL_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(4, 5, 'ระดับที่ 5 คือ มีทักษะระดับที่ 4  และสามารถชี้ประเด็น เสนอทางออกในเชิงกลยุทธ์ของเรื่องต่างๆ ที่วิเคราะห์อยู่ โดยอ้างอิงผลจากการวิเคราะห์ข้อมูล', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO SKILL_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(4, 6, 'ระดับที่ 6 คือ มีทักษะระดับที่ 5  และสามารถกำหนดกรอบ แนวทาง และวิธีการบริหารจัดการฐานข้อมูลขนาดใหญ่ได้อย่างเป็นระบบ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO SKILL_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(4, 7, 'ระดับที่ 7 คือ มีทักษะระดับที่ 6 และสามารถออกแบบ หรือประยุกต์ใช้แบบจำลอง (Model) ต่างๆ มาใช้ในการวิเคราะห์ บริหารจัดการ และใช้ประโยชน์จากฐานข้อมูลได้', $SESS_USERID, '$UPDATE_DATE') ";
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
						VALUES(1, 'ไม่จำเป็นต้องมีประสบการณ์มาก่อน', 'ไม่จำเป็นต้องมีประสบการณ์มาก่อน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO EXP_INFO (JOB_DES_ID, JOB_DES_NAME, JOB_DES_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(2, 'ประสบการณ์ในงานที่เกี่ยวข้อง', 'มีประสบการณ์ในงานที่เกี่ยวข้องไม่ตำกว่าที่กำหนดไว้', $SESS_USERID, '$UPDATE_DATE') ";
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
						VALUES(1, 'การมุ่งผลสัมฤทธิ์ (Achievement Motivation-ACH)', 'ความมุ่งมั่นจะปฏิบัติราชการให้ดีหรือให้เกินมาตรฐานที่มีอยู่ โดยมาตรฐานนี้อาจเป็นผลการปฏิบัติงานที่ผ่านมาของตนเอง หรือเกณฑ์วัดผลสัมฤทธิ์ที่ส่วนราชการกำหนดขึ้น อีกทั้งยังหมายรวมถึงการสร้างสรรค์พัฒนาผลงานหรือกระบวนการปฏิบัติงานตามเป้าหมายที่ยากและท้าทายชนิดที่อาจไม่เคยมีผู้ใดสามารถกระทำได้มาก่อน', 'สมรรถนะหลัก', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_INFO (JOB_DES_ID, JOB_DES_NAME, JOB_DES_DESCRIPTION, COMPETENCY_TYPE, UPDATE_USER, UPDATE_DATE) 
						VALUES(2, 'บริการที่ดี (Service Mind-SERV)', 'สมรรถนะนี้เน้นความตั้งใจและความพยายามของข้าราชการในการให้บริการเพื่อสนองความต้องการของประชาชนตลอดจนของหน่วยงานภาครัฐอื่นๆที่เกี่ยวข้อง', 'สมรรถนะหลัก', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_INFO (JOB_DES_ID, JOB_DES_NAME, JOB_DES_DESCRIPTION, COMPETENCY_TYPE, UPDATE_USER, UPDATE_DATE) 
						VALUES(3, 'การสั่งสมความเชี่ยวชาญในงานอาชีพ (Expertise-EXP)', 'ความขวนขวาย สนใจใฝ่รู้ เพื่อสั่งสม
พัฒนาศักยภาพ ความรู้ความสามารถของตนในการปฏิบัติราชการ ด้วยการศึกษา ค้นคว้าหาความรู้ พัฒนาตนเองอย่างต่อเนื่อง อีกทั้งรู้จักพัฒนา ปรับปรุง ประยุกต์ใช้ความรู้เชิงวิชาการและเทคโนโลยีต่างๆ เข้ากับการปฏิบัติงานให้เกิดผลสัมฤทธิ์', 'สมรรถนะหลัก', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_INFO (JOB_DES_ID, JOB_DES_NAME, JOB_DES_DESCRIPTION, COMPETENCY_TYPE, UPDATE_USER, UPDATE_DATE) 
						VALUES(4, 'จริยธรรม (Integrity-ING)', 'การครองตนและประพฤติปฏิบัติถูกต้องเหมาะสมทั้งตามหลักกฎหมายและคุณธรรมจริยธรรม ตลอดจนหลักแนวทางในวิชาชีพของตนโดยมุ่งประโยชน์ของประเทศชาติมากกว่าประโยชน์ส่วนตน  ทั้งนี้เพื่อธำรงรักษาศักดิ์ศรีแห่งอาชีพข้าราชการ อีกทั้งเพื่อเป็นกำลังสำคัญในการสนับสนุนผลักดันให้ภารกิจหลักภาครัฐบรรลุเป้าหมายที่กำหนดไว้', 'สมรรถนะหลัก', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_INFO (JOB_DES_ID, JOB_DES_NAME, JOB_DES_DESCRIPTION, COMPETENCY_TYPE, UPDATE_USER, UPDATE_DATE) 
						VALUES(5, 'ความร่วมแรงร่วมใจ (Teamwork-TW)', 'สมรรถนะนี้เน้นที่ 1) ความตั้งใจที่จะทำงานร่วมกับผู้อื่น เป็นส่วนหนึ่งในทีมงาน หน่วยงาน หรือองค์กร โดยผู้ปฏิบัติมีฐานะเป็นสมาชิกในทีม มิใช่ในฐานะหัวหน้าทีม และ 2) ความสามารถในการสร้างและดำรงรักษาสัมพันธภาพกับสมาชิกในทีม', 'สมรรถนะหลัก', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_INFO (JOB_DES_ID, JOB_DES_NAME, JOB_DES_DESCRIPTION, COMPETENCY_TYPE, UPDATE_USER, UPDATE_DATE) 
						VALUES(6, 'การคิดวิเคราะห์ (Analytical Thinking-AT)', 'การทำความเข้าใจสถานการณ์ ประเด็นปัญหา แนวคิด หลักทฤษฎี ฯลฯ โดยการแจกแจงแตกประเด็นออกเป็นส่วนย่อยๆ หรือวิเคราะห์สถานการณ์ทีละขั้นตอน รวมถึงการจัดหมวดหมู่ปัญหาหรือสถานการณ์อย่างเป็นระบบระเบียบ เปรียบเทียบแง่มุมต่างๆ สามารถระบุได้ว่าอะไรเกิดก่อนหลัง ตลอดจนระบุเหตุและผล ที่มาที่ไปของกรณีต่างๆได้', 'สมรรถนะประจำกลุ่มงาน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_INFO (JOB_DES_ID, JOB_DES_NAME, JOB_DES_DESCRIPTION, COMPETENCY_TYPE, UPDATE_USER, UPDATE_DATE) 
						VALUES(7, 'การมองภาพองค์รวม (Conceptual Thinking-CT)', 'การคิดในเชิงสังเคราะห์ มองภาพองค์รวมจนได้เป็นกรอบความคิดหรือแนวคิดใหม่ อันเป็นผลมาจากการสรุปรูปแบบ ประยุกต์แนวทางต่างๆจากสถานการณ์หรือข้อมูลหลากหลาย และนานาทัศนะ', 'สมรรถนะประจำกลุ่มงาน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_INFO (JOB_DES_ID, JOB_DES_NAME, JOB_DES_DESCRIPTION, COMPETENCY_TYPE, UPDATE_USER, UPDATE_DATE) 
						VALUES(8, 'การพัฒนาศักยภาพคน (Caring & Developing Others-DEV)', 'ความตั้งใจจะส่งเสริมการเรียนรู้หรือการพัฒนาผู้อื่นในระยะยาว โดยมุ่งเน้นที่เจตนาที่จะพัฒนาผู้อื่นและผลที่เกิดขึ้นมากกว่าเพียงปฏิบัติไปตามหน้าที่', 'สมรรถนะประจำกลุ่มงาน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_INFO (JOB_DES_ID, JOB_DES_NAME, JOB_DES_DESCRIPTION, COMPETENCY_TYPE, UPDATE_USER, UPDATE_DATE) 
						VALUES(9, 'การสั่งการตามอำนาจหน้าที่ (Holding People Accountable-HPA)', 'เจตนาที่จะกำกับดูแลให้ผู้อื่นปฏิบัติให้ได้ตามมาตรฐาน กฎระเบียบข้อบังคับที่กำหนดไว้ โดยอาศัยอำนาจตามระเบียบกฎหมาย หรือตามตำแหน่งหน้าที่ที่มีอยู่อย่างเหมาะสมและมีประสิทธิภาพโดยมุ่งประโยชน์ขององค์กรและประเทศชาติเป็นสำคัญ การสั่งการตามอำนาจหน้าที่นี้อาจรวมถึงการ“ออกคำสั่ง” ซึ่งมีตั้งแต่ระดับสั่งงานปรกติทั่วไปจนถึงระดับการจัดการขั้นเด็ดขาดกับผู้ฝ่าฝืน', 'สมรรถนะประจำกลุ่มงาน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_INFO (JOB_DES_ID, JOB_DES_NAME, JOB_DES_DESCRIPTION, COMPETENCY_TYPE, UPDATE_USER, UPDATE_DATE) 
						VALUES(10, 'การสืบเสาะหาข้อมูล (Information Seeking-INF)', 'ความสนใจใคร่รู้เกี่ยวกับสถานการณ์ ภูมิหลัง ประวัติความเป็นมา ประเด็น ปัญหา หรือเรื่องราวต่างๆ ที่เกี่ยวข้องหรือจำเป็นต่องานในหน้าที่ คุณลักษณะนี้อาจรวมถึงการสืบเสาะ เพื่อให้ได้ข้อมูลเฉพาะเจาะจง การไขปมปริศนาโดยซักถามโดยละเอียด หรือแม้แต่การหาข่าวทั่วไปจากสภาพแวดล้อมรอบตัวโดยคาดว่าอาจมีข้อมูลที่จะเป็นประโยชน์ต่อไปในอนาคต', 'สมรรถนะประจำกลุ่มงาน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_INFO (JOB_DES_ID, JOB_DES_NAME, JOB_DES_DESCRIPTION, COMPETENCY_TYPE, UPDATE_USER, UPDATE_DATE) 
						VALUES(11, 'ความเข้าใจข้อแตกต่างทางวัฒนธรรม (Cultural Sensitivity-CS)', 'ความตระหนักถึงข้อแตกต่างระหว่างวัฒนธรรมและสามารถประยุกต์ใช้ความเข้าใจนี้ เพื่อสร้างและส่งเสริม  สัมพันธภาพต่างวัฒนธรรมเพื่อมิตรไมตรีและความร่วมมืออันดีระหว่างราชอาณาจักรไทยและนานาประเทศ', 'สมรรถนะประจำกลุ่มงาน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_INFO (JOB_DES_ID, JOB_DES_NAME, JOB_DES_DESCRIPTION, COMPETENCY_TYPE, UPDATE_USER, UPDATE_DATE) 
						VALUES(12, 'ความเข้าใจผู้อื่น (Interpersonal Understanding-IU)', 'ความสามารถในการรับฟังและเข้าใจทั้งความหมายตรงและความหมายแฝง ตลอดจนสภาวะอารมณ์ของผู้ที่ติดต่อด้วย', 'สมรรถนะประจำกลุ่มงาน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_INFO (JOB_DES_ID, JOB_DES_NAME, JOB_DES_DESCRIPTION, COMPETENCY_TYPE, UPDATE_USER, UPDATE_DATE) 
						VALUES(13, 'ความเข้าใจองค์กรและระบบราชการ (Organizational Awareness-OA)', 'ความเข้าใจและสามารถประยุกต์ใช้ความสัมพันธ์เชื่อมโยงของกระแสอำนาจทั้งที่เป็นทางการและไม่เป็นทางการในองค์กรของตนและองค์กรอื่นๆ ที่เกี่ยวข้องเพื่อประโยชน์ในการปฏิบัติหน้าที่ให้บรรลุผล ความเข้าใจนี้รวมถึงความสามารถคาดการณ์ได้ว่านโยบายภาครัฐ แนวคิดใหม่ๆทางการเมือง เศรษฐกิจ สังคม เทคโนโลยี ฯลฯ ตลอดจนเหตุการณ์หรือสถานการณ์ต่างๆที่อุบัติขึ้นจะมีผลต่อองค์กรและภารกิจที่ตนปฏิบัติอยู่อย่างไร', 'สมรรถนะประจำกลุ่มงาน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_INFO (JOB_DES_ID, JOB_DES_NAME, JOB_DES_DESCRIPTION, COMPETENCY_TYPE, UPDATE_USER, UPDATE_DATE) 
						VALUES(14, 'การดำเนินการเชิงรุก (Proactiveness-PROAC)', 'การเล็งเห็นปัญหาหรือโอกาสพร้อมทั้งลงมือจัดการกับปัญหานั้นๆ หรือใช้โอกาสที่เกิดขึ้นให้เกิดประโยชน์ต่องาน ด้วยวิธีการที่สร้างสรรค์และแปลกใหม่', 'สมรรถนะประจำกลุ่มงาน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_INFO (JOB_DES_ID, JOB_DES_NAME, JOB_DES_DESCRIPTION, COMPETENCY_TYPE, UPDATE_USER, UPDATE_DATE) 
						VALUES(15, 'ความถูกต้องของงาน (Concern for Order-CO)', 'ความพยายามที่จะปฏิบัติงานให้ถูกต้องครบถ้วนตลอดจนลดข้อบกพร่องที่อาจจะเกิดขึ้น รวมทั้งความพยายามให้เกิดความชัดเจนขึ้นในบทบาทหน้าที่ กฎหมาย ระเบียบข้อบังคับ ขั้นตอนปฏิบัติต่างๆ', 'สมรรถนะประจำกลุ่มงาน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_INFO (JOB_DES_ID, JOB_DES_NAME, JOB_DES_DESCRIPTION, COMPETENCY_TYPE, UPDATE_USER, UPDATE_DATE) 
						VALUES(16, 'ความมั่นใจในตนเอง (Self Confidence-SCF)', 'ความมั่นใจในความสามารถ ศักยภาพ ตลอดจนวิจารณญาณการตัดสินใจของตนที่จะปฏิบัติงานให้บรรลุผล หรือเลือกวิธีที่มีประสิทธิภาพในการปฏิบัติงาน หรือแก้ไขปัญหาให้สำเร็จลุล่วง ', 'สมรรถนะประจำกลุ่มงาน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_INFO (JOB_DES_ID, JOB_DES_NAME, JOB_DES_DESCRIPTION, COMPETENCY_TYPE, UPDATE_USER, UPDATE_DATE) 
						VALUES(17, 'ความยืดหยุ่นผ่อนปรน (Flexibility-FLX)', 'ความสามารถในการปรับตัวเข้ากับสถานการณ์และกลุ่มคนที่หลากหลาย ในขณะที่ยังคงปฏิบัติงานได้อย่างมี ประสิทธิภาพ หมายความรวมถึงการยอมรับความคิดเห็นของผู้อื่น และปรับเปลี่ยนวิธีการเมื่อสถานการณ์แวดล้อมเปลี่ยนไป', 'สมรรถนะประจำกลุ่มงาน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_INFO (JOB_DES_ID, JOB_DES_NAME, JOB_DES_DESCRIPTION, COMPETENCY_TYPE, UPDATE_USER, UPDATE_DATE) 
						VALUES(18, 'ศิลปะการสื่อสารจูงใจ (Communication & Influencing-CI)', 'ความตั้งใจที่จะสื่อความด้วยการเขียน พูด โดยใช้สื่อต่างๆ ตลอดจนการชักจูง หว่านล้อม โน้มน้าว บุคคลอื่น และทำให้ผู้อื่นประทับใจ หรือเพื่อให้สนับสนุนความคิดของตน', 'สมรรถนะประจำกลุ่มงาน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_INFO (JOB_DES_ID, JOB_DES_NAME, JOB_DES_DESCRIPTION, COMPETENCY_TYPE, UPDATE_USER, UPDATE_DATE) 
						VALUES(19, 'สภาวะผู้นำ (Leadership-LEAD)', 'ความตั้งใจหรือความสามารถในการเป็นผู้นำของกลุ่มคน ปกครอง รวมถึงการกำหนดทิศทาง วิสัยทัศน์ เป้าหมาย วิธีการทำงาน ให้ผู้ใต้บังคับบัญชาหรือทีมงานปฏิบัติงานได้อย่างราบรื่น เต็มประสิทธิภาพและบรรลุวัตถุประสงค์ขององค์กร', 'สมรรถนะประจำกลุ่มงาน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_INFO (JOB_DES_ID, JOB_DES_NAME, JOB_DES_DESCRIPTION, COMPETENCY_TYPE, UPDATE_USER, UPDATE_DATE) 
						VALUES(20, 'สุนทรียภาพทางศิลปะ (Aesthetic Quality-AQ)', 'ความซาบซึ้งในอรรถรสของงานศิลป์ประกอบกับการเล็งเห็นคุณค่าของงานเหล่านั้นในฐานะที่เป็นเอกลักษณ์และมรดกของชาติ และนำมาปรับใช้ในการสร้างสรรค์งานศิลป์ของตน', 'สมรรถนะประจำกลุ่มงาน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_INFO (JOB_DES_ID, JOB_DES_NAME, JOB_DES_DESCRIPTION, COMPETENCY_TYPE, UPDATE_USER, UPDATE_DATE) 
						VALUES(21, 'วิสัยทัศน์ (Visioning - VIS)', 'ความสามารถให้ทิศทางที่ชัดเจนและก่อความร่วมแรงร่วมใจในหมู่ผู้ใต้บังคับบัญชาเพื่อนำพางานภาครัฐไปสู่จุดหมายร่วมกัน', 'สมรรถนะประจำผู้บริหาร', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_INFO (JOB_DES_ID, JOB_DES_NAME, JOB_DES_DESCRIPTION, COMPETENCY_TYPE, UPDATE_USER, UPDATE_DATE) 
						VALUES(22, 'การวางกลยุทธ์ภาครัฐ (Strategic Orientation-SO)', 'ความเข้าใจกลยุทธ์ภาครัฐและสามารถประยุกต์ใช้ในการกำหนดกลยุทธ์ของหน่วยงานตนได้ โดยความสามารถในการประยุกต์นี้รวมถึงความสามารถในการคาดการณ์ถึงทิศทางระบบราชการในอนาคต ตลอดจนผลกระทบของสถานการณ์ทั้งในและต่างประเทศที่เกิดขึ้น', 'สมรรถนะประจำผู้บริหาร', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_INFO (JOB_DES_ID, JOB_DES_NAME, JOB_DES_DESCRIPTION, COMPETENCY_TYPE, UPDATE_USER, UPDATE_DATE) 
						VALUES(23, 'ศักยภาพเพื่อนำการปรับเปลี่ยน (Change Leadership-CL)', 'ความตั้งใจและความสามารถในการกระตุ้นผลักดันกลุ่มคนให้เกิดความต้องการจะปรับเปลี่ยนไปในแนวทางที่เป็นประโยชน์แก่ภาครัฐ รวมถึงการสื่อสารให้ผู้อื่นรับรู้ เข้าใจ และดำเนินการให้การปรับเปลี่ยนนั้นเกิดขึ้นจริง', 'สมรรถนะประจำผู้บริหาร', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_INFO (JOB_DES_ID, JOB_DES_NAME, JOB_DES_DESCRIPTION, COMPETENCY_TYPE, UPDATE_USER, UPDATE_DATE) 
						VALUES(24, 'การควบคุมตนเอง (Self Control-SCT)', 'การระงับอารมณ์และพฤติกรรมอันไม่เหมาะสมเมื่อถูกยั่วยุ หรือเผชิญหน้ากับฝ่ายตรงข้าม เผชิญความไม่เป็นมิตร หรือทำงานภายใต้สภาวะความกดดัน รวมถึงความอดทนอดกลั้นเมื่อต้องอยู่ภายใต้สถานการณ์ที่ก่อความเครียดอย่างต่อเนื่อง', 'สมรรถนะประจำผู้บริหาร', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_INFO (JOB_DES_ID, JOB_DES_NAME, JOB_DES_DESCRIPTION, COMPETENCY_TYPE, UPDATE_USER, UPDATE_DATE) 
						VALUES(25, 'การให้อำนาจแก่ผู้อื่น (Empowering Others - EMP)', 'ความเชื่อมั่นในความสามารถของผู้อื่น ดังนั้นจึงมอบหมายอำนาจและหน้าที่รับผิดชอบให้เพื่อให้ผู้อื่นมีอิสระในการสร้างสรรค์วิธีการของตนเพื่อบรรลุเป้าหมายในงาน', 'สมรรถนะประจำผู้บริหาร', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_INFO (JOB_DES_ID, JOB_DES_NAME, JOB_DES_DESCRIPTION, COMPETENCY_TYPE, UPDATE_USER, UPDATE_DATE) 
						VALUES(26, 'ความคิดสร้างสรรค์', 'ความคิดสร้างสรรค์', 'สมรรถนะประจำกลุ่มงาน', $SESS_USERID, '$UPDATE_DATE') ";
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
						VALUES(1, 0, 'ระดับที่ 0 คือ ไม่แสดงสมรรถนะด้านนี้อย่างชัดเจน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(1, 1, 'ระดับที่ 1 คือ แสดงความพยายามในการทำงานให้ดี พยายามทำงานในหน้าที่ให้ดีและถูกต้อง 
มีความมานะอดทน ขยันหมั่นเพียรในการทำงาน และตรงต่อเวลา
มีความรับผิดชอบในงาน สามารถส่งงานได้ตามกำหนดเวลา
แสดงออกว่าต้องการทำงานให้ได้ดีขึ้น เช่น ถามถึงวิธีการ หรือขอแนะนำอย่างกระตือรือร้น สนใจใคร่รู้
แสดงความเห็นในเชิงปรับปรุงพัฒนาเมื่อเห็นสิ่งที่ก่อให้เกิดการสูญเปล่า หรือหย่อนประสิทธิภาพในงาน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(1, 2, 'ระดับที่ 2 คือ แสดงสมรรถนะระดับที่ 1 และสามารถทำงานได้ผลงานตามเป้าหมายที่วางไว้
กำหนดมาตรฐาน หรือเป้าหมายในการทำงานเพื่อให้ได้ผลงานที่ดี
หมั่นติดตามผลงาน และประเมินผลงานของตน โดยใช้เกณฑ์ที่กำหนดขึ้น โดยไม่ได้ถูกบังคับ เช่น ถามว่าผลงานดีหรือยัง หรือต้องปรับปรุงอะไรจึงจะดีขึ้น
ทำงานได้ตามผลงานตามเป้าหมายที่ผู้บังคับบัญชากำหนด หรือเป้าหมายของหน่วยงานที่รับผิดชอบ
มีความละเอียดรอบคอบเอาใจใส่ ตรวจตราความถูกต้องของงาน เพื่อให้ได้งานที่มีคุณภาพ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(1, 3, 'ระดับที่ 3 คือ แสดงสมรรถนะระดับที่ 2 และสามารถทำงานได้ผลงานที่มีประสิทธิภาพมากยิ่งขึ้น
ปรับปรุงวิธีการที่ทำให้ทำงานได้ดีขึ้น เร็วขึ้น มีคุณภาพดีขึ้น หรือมีประสิทธิภาพมากขึ้น
เสนอหรือทดลองวิธีการทำงานแบบใหม่ที่มีประสิทธิภาพมากกว่าเดิม เพื่อให้ได้ผลงานตามที่กำหนดไว้', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(1, 4, 'ระดับที่ 4 คือ แสดงสมรรถนะระดับที่ 3 และสามารถพัฒนาวิธีการทำงาน เพื่อให้ได้ผลงานที่โดดเด่น และแตกต่างอย่างไม่เคยมีใครทำได้มาก่อน 
กำหนดเป้าหมายที่ท้าทาย และเป็นไปได้ยาก เพื่อทำให้ได้ผลงานที่ดีกว่าเดิมอย่างเห็นได้ชัด
ทำการพัฒนาระบบ ขั้นตอน วิธีการทำงาน เพื่อให้ได้ผลงานที่โดดเด่น และแตกต่างไม่เคยมีใครทำได้มาก่อน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(1, 5, 'ระดับที่ 5 คือ แสดงสมรรถนะระดับที่ 4 และสามารถตัดสินใจได้ แม้จะมีความเสี่ยง เพื่อให้องค์กรบรรลุเป้าหมาย
ตัดสินใจได้ โดยมีการคำนวณผลได้ผลเสียอย่างชัดเจน และดำเนินการ เพื่อให้ภาครัฐและประชาชนได้ประโยชน์สูงสุด
บริหารจัดการและทุ่มเทเวลา ตลอดจนทรัพยากร เพื่อให้ได้ประโยชน์สูงสุดต่อภารกิจของหน่วยงานตามที่วางแผนไว้', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(2, 0, 'ระดับที่ 0 คือ ไม่แสดงสมรรถนะด้านนี้อย่างชัดเจน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(2, 1, 'ระดับที่ 1 คือ แสดงความเต็มใจในการให้บริการ
ให้การบริการที่เป็นมิตร สุภาพ เต็มใจต้อนรับ
ให้บริการด้วยอัธยาศัยไมตรีอันดี และสร้างความประทับใจแก่ผู้รับบริการ 
ให้คำแนะนำ และคอยติดตามเรื่อง เมื่อผู้รับบริการมีคำถาม ข้อเรียกร้องที่เกี่ยวกับภารกิจของหน่วยงาน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(2, 2, 'ระดับที่ 2 คือ แสดงสมรรถนะระดับที่ 1 และสามารถให้บริการที่ผู้รับบริการต้องการได้
ให้ข้อมูล ข่าวสาร ของการบริการที่ถูกต้อง ชัดเจนแก่ผู้รับบริการได้ตลอดการให้บริการ
แจ้งให้ผู้รับบริการทราบความคืบหน้าในการดำเนินเรื่อง หรือขั้นตอนงานต่างๆ ที่ให้บริการอยู่
ประสานงานภายในหน่วยงาน และกับหน่วยงานที่เกี่ยวข้อง เพื่อให้ผู้รับบริการได้รับบริการที่ต่อเนื่องและรวดเร็ว', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(2, 3, 'ระดับที่ 3 คือ แสดงสมรรถนะระดับที่ 2 และเต็มใจช่วยแก้ปัญหาให้กับผู้บริการได้
รับเป็นธุระ ช่วยแก้ปัญหาหรือหาแนวทางแก้ไขปัญหาที่เกิดขึ้นแก่ผู้รับบริการอย่างรวดเร็ว  เต็มใจ ไม่บ่ายเบี่ยง ไม่แก้ตัว หรือปัดภาระ
คอยดูแลให้ผู้รับบริการได้รับความพึงพอใจ และนำข้อขัดข้องใดๆ ในการให้บริการ (ถ้ามี) ไปพัฒนาการให้บริการให้ดียิ่งขึ้น', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(2, 4, 'ระดับที่ 4 คือ แสดงสมรรถนะระดับที่ 3 และให้บริการที่เกินความคาดหวังในระดับทั่วไป แม้ต้องใช้เวลาหรือความพยายามอย่างมาก 
ให้เวลาแก่ผู้รับบริการ โดยเฉพาะเมื่อผู้รับบริการประสบความยากลำบาก เช่น ให้เวลาและความพยายามพิเศษในการให้บริการ เพื่อช่วยผู้รับบริการแก้ปัญหา
คอยให้ข้อมูล ข่าวสาร ความรู้ที่เกี่ยวข้องกับงานที่กำลังให้บริการอยู่ ซึ่งเป็นประโยชน์แก่ผู้รับบริการ แม้ว่าผู้รับบริการจะไม่ได้ถามถึง หรือไม่ทราบมาก่อน
ให้บริการที่เกินความคาดหวังในระดับทั่วไป', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(2, 5, 'ระดับที่ 5 คือ แสดงสมรรถนะระดับที่ 4 และสามารถเข้าใจและให้บริการที่ตรงตามความต้องการที่แท้จริงของผู้รับบริการได้ 
เข้าใจความจำเป็นหรือความต้องการที่แท้จริงของผู้รับบริการ และ/หรือ ใช้เวลาแสวงหาข้อมูลและทำความเข้าใจเกี่ยวกับความจำเป็นหรือความต้องการที่แท้จริงของผู้รับบริการ
ให้คำแนะนำที่เป็นประโยชน์แก่ผู้รับบริการ เพื่อตอบสนองความจำเป็นหรือความต้องการที่แท้จริงของผู้รับบริการ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(3, 0, 'ระดับที่ 0 คือ ไม่แสดงสมรรถนะด้านนี้อย่างชัดเจน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(3, 1, 'ระดับที่ 1 คือ แสดงความสนใจและติดตามความรู้ใหม่ๆ ในสาขาอาชีพของตน/ที่เกี่ยวข้อง 
กระตือรือร้นในการศึกษาหาความรู้ สนใจเทคโนโลยีและองค์ความรู้ใหม่ๆ ในสาขาอาชีพของตน
หมั่นทดลองวิธีการทำงานแบบใหม่ เพื่อพัฒนาประสิทธิภาพและความรู้ความสามารถของตนให้ดียิ่งขึ้น
ติดตามเทคโนโลยีองค์ความรู้ใหม่ๆ อยู่เสมอด้วยการสืบค้นข้อมูลจากแหล่งต่างๆ ที่จะเป็นประโยชน์ต่อการปฏิบัติราชการ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(3, 2, 'ระดับที่ 2 คือ แสดงสมรรถนะระดับที่ 1 และมีความรู้ในวิชาการ และเทคโนโลยีใหม่ๆ  ในสาขาอาชีพของตน 
รอบรู้เท่าทันเทคโนโลยีหรือองค์ความรู้ใหม่ๆ ในสาขาอาชีพของตนและที่เกี่ยวข้อง หรืออาจมีผลกระทบต่อการปฏิบัติหน้าที่ของตน 
ติดตามแนวโน้มวิทยาการที่ทันสมัย และเทคโนโลยีที่เกี่ยวข้องกับงานอย่างต่อเนื่อง', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(3, 3, 'ระดับที่ 3 คือ แสดงสมรรถนะระดับที่ 2 และสามารถนำความรู้ วิทยาการ หรือเทคโนโลยีใหม่ๆ ที่ได้ศึกษามาปรับใช้กับการทำงาน
เข้าใจประเด็นหลักๆ นัยสำคัญ และผลกระทบของวิทยาการต่างๆ อย่างลึกซึ้ง
สามารถนำวิชาการ ความรู้ หรือเทคโนโลยีใหม่ๆ มาประยุกต์ใช้ในการปฏิบัติงานได้
สั่งสมความรู้ใหม่ๆ อยู่เสมอ และเล็งเห็นประโยชน์ ความสำคัญขององค์ความรู้ เทคโนโลยีใหม่ๆ ที่จะส่งผลกระทบต่องานของตนในอนาคต', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(3, 4, 'ระดับที่ 4 คือ แสดงสมรรถนะระดับที่ 3 และศึกษา พัฒนาตนเองให้มีความรู้ และความเชี่ยวชาญในงานมากขึ้น ทั้งในเชิงลึก และเชิงกว้างอย่างต่อเนื่อง
มีความรู้ความเชี่ยวชาญในเรื่องที่เกี่ยวกับงานหลายด้าน (สหวิทยาการ) และสามารถนำความรู้ไปปรับใช้ให้ปฏิบัติได้อย่างกว้างขวางครอบคลุม
สามารถนำความรู้เชิงบูรณาการของตนไปใช้ในการสร้างวิสัยทัศน์ เพื่อการปฏิบัติงานในอนาคต
ขวนขวายหาความรู้ที่เกี่ยวข้องกับงานทั้งเชิงลึกและเชิงกว้างอย่างต่อเนื่อง', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(3, 5, 'ระดับที่ 5 คือ แสดงสมรรถนะระดับที่ 4 และสนับสนุนการทำงานของคนในองค์กรที่เน้นความเชี่ยวชาญในวิทยาการด้านต่างๆ
สนับสนุนให้เกิดบรรยากาศแห่งการพัฒนาความเชี่ยวชาญในองค์กร ด้วยการจัดสรรทรัพยากร เครื่องมือ อุปกรณ์ที่เอื้อต่อการพัฒนา
ให้การสนับสนุน ชมเชย เมื่อมีผู้แสดงออกถึงความตั้งใจที่จะพัฒนาความเชี่ยวชาญในงาน
มีวิสัยทัศน์ในการเล็งเห็นประโยชน์ของเทคโนโลยี องค์ความรู้ หรือวิทยาการใหม่ๆ ต่อการปฏิบัติงานในอนาคต และสนับสนุนส่งเสริมให้มีการนำมาประยุกต์ใช้ในหน่วยงานอย่างต่อเนื่อง', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(4, 0, 'ระดับที่ 0 คือ ไม่แสดงสมรรถนะด้านนี้อย่างชัดเจน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(4, 1, 'ระดับที่ 1 คือ มีความซื่อสัตย์สุจริต
ปฏิบัติหน้าที่ด้วยความโปร่งใส  ซื่อสัตย์สุจริต ถูกต้องทั้งตามหลักกฎหมาย จริยธรรมและระเบียบวินัย
แสดงความคิดเห็นของตนตามหลักวิชาชีพอย่างเปิดเผยตรงไปตรงมา', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(4, 2, 'ระดับที่ 2 คือ แสดงสมรรถนะระดับที่ 1 และมีสัจจะเชื่อถือได้
รักษาวาจา มีสัจจะเชื่อถือได้ พูดอย่างไรทำอย่างนั้น ไม่บิดเบือนอ้างข้อยกเว้นให้ตนเอง
มีจิตสำนึกและความภาคภูมิใจในความเป็นข้าราชการ อุทิศแรงกายแรงใจผลักดันให้ภารกิจหลักของตนและหน่วยงานบรรลุผล เพื่อสนับสนุนส่งเสริมการพัฒนาประเทศชาติและสังคมไทย', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(4, 3, 'ระดับที่ 3 คือ แสดงสมรรถนะระดับที่ 2 และยึดมั่นในหลักการ
ยึดมั่นในหลักการและจรรยาบรรณของวิชาชีพ ไม่เบี่ยงเบนด้วยอคติหรือผลประโยชน์ส่วนตน
เสียสละความสุขสบายตลอดจนความพึงพอใจส่วนตนหรือของครอบครัว โดยมุ่งให้ภารกิจในหน้าที่สัมฤทธิ์ผลเป็นสำคัญ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(4, 4, 'ระดับที่ 4 คือ แสดงสมรรถนะระดับที่ 3 และธำรงความถูกต้อง
ธำรงความถูกต้อง ยืนหยัดพิทักษ์ผลประโยชน์และชื่อเสียงของประเทศชาติแม้ในสถานการณ์ที่อาจสร้างความลำบากใจให้
ตัดสินใจในหน้าที่ ปฏิบัติราชการด้วยความถูกต้อง โปร่งใส เป็นธรรม แม้ผลของการปฏิบัติอาจสร้างศัตรูหรือก่อความไม่พึงพอใจให้แก่ผู้ที่เกี่ยวข้องหรือเสียประโยชน์', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(4, 5, 'ระดับที่ 5 คือ แสดงสมรรถนะระดับที่ 4 และอุทิศตนเพื่อผดุงความยุติธรรม
ธำรงความถูกต้อง ยืนหยัดพิทักษ์ผลประโยชน์และชื่อเสียงของประเทศชาติแม้ในสถานการณ์ที่อาจเสี่ยงต่อความมั่นคงในตำแหน่งหน้าที่การงาน หรืออาจเสียงภัยต่อชีวิต', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(5, 0, 'ระดับที่ 0 คือ ไม่แสดงสมรรถนะด้านนี้อย่างชัดเจน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(5, 1, 'ระดับที่ 1 คือ ทำหน้าที่ของตนในทีมให้สำเร็จ
ทำงานในส่วนที่ตนได้รับมอบหมายได้สำเร็จ สนับสนุนการตัดสินใจในกลุ่ม 
รายงานให้สมาชิกทราบความคืบหน้าของการดำเนินงานในกลุ่ม หรือข้อมูลอื่นๆ ที่เป็นประโยชน์ต่อการทำงานอย่างต่อเนื่อง', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(5, 2, 'ระดับที่ 2 คือ แสดงสมรรถนะระดับที่ 1 และให้ความร่วมมือในการทำงานกับเพื่อนร่วมงาน
สร้างสัมพันธ์  เข้ากับผู้อื่นในกลุ่มได้ดี
เอื้อเฟื้อเผื่อแผ่ ให้ความร่วมมือกับผู้อื่นในทีมด้วยดี
กล่าวถึงเพื่อนร่วมงานในเชิงสร้างสรรค์', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(5, 3, 'ระดับที่ 3 คือ แสดงสมรรถนะระดับที่ 2 และประสานความร่วมมือของสมาชิกในทีม
รับฟังความเห็นของสมาชิกในทีม เต็มใจเรียนรู้จากผู้อื่น รวมถึงผู้ใต้บังคับบัญชา และผู้ร่วมงาน
ประมวลความคิดเห็นต่างๆ มาใช้ประกอบการตัดสินใจหรือวางแผนงานร่วมกันในทีม
ประสานและส่งเสริมสัมพันธภาพอันดีในทีม เพื่อสนับสนุนการทำงานร่วมกันให้มีประสิทธิภาพยิ่งขึ้น', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(5, 4, 'ระดับที่ 4 คือ แสดงสมรรถนะระดับที่ 3 และสนับสนุนและช่วยเหลืองานเพื่อนร่วมทีมคนอื่นๆ เพื่อให้งานประสบความสำเร็จ
กล่าวชื่นชมให้กำลังใจเพื่อนร่วมงานได้อย่างจริงใจ 
แสดงน้ำใจในเหตุวิกฤติ ให้ความช่วยเหลือแก่เพื่อนร่วมงานที่มีเหตุจำเป็นโดยไม่ต้องให้ร้องขอ
รักษามิตรภาพอันดีกับเพื่อนร่วมงานเพื่อช่วยเหลือกันในวาระต่างๆให้งานสำเร็จลุล่วงเป็นประโยชน์ต่อส่วนรวม', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(5, 5, 'ระดับที่ 5 คือ แสดงสมรรถนะระดับที่ 4 และสามารถนำทีมให้ปฏิบัติภารกิจให้ได้ผลสำเร็จ
ส่งเสริมความสามัคคีเป็นน้ำหนึ่งใจเดียวกันในทีม โดยไม่คำนึงความชอบหรือไม่ชอบส่วนตน 
ช่วยประสานรอยร้าว หรือคลี่คลายแก้ไขข้อขัดแย้งที่เกิดขึ้นในทีม
ประสานสัมพันธ์ ส่งเสริมขวัญกำลังใจของทีมเพื่อรวมพลังกันในการปฏิบัติภารกิจใหญ่น้อยต่างๆให้บรรลุผล', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(6, 0, 'ระดับที่ 0 คือ ไม่แสดงสมรรถนะด้านนี้อย่างชัดเจน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(6, 1, 'ระดับที่ 1 คือ แตกปัญหา/งานออกเป็นส่วนย่อยๆ
ระบุรายการสิ่งต่างๆ หรือประเด็นย่อยต่างๆได้โดยไม่เรียงลำดับก่อนหลัง
วางแผนงานได้โดยแตกประเด็นปัญหาออกเป็นงาน หรือกิจกรรมต่างๆ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(6, 2, 'ระดับที่ 2 คือ แสดงสมรรถนะระดับที่ 1 และเห็นความสัมพันธ์เชื่อมโยงขั้นพื้นฐานของส่วนต่างๆ ของปัญหา/งาน
ระบุได้ว่าอะไรเป็นเหตุเป็นผลแก่กันในสถานการณ์หนึ่งๆ
แยกแยะข้อดีข้อเสียของประเด็นต่างๆได้
วางแผนงานได้โดยจัดเรียงงาน หรือกิจกรรมต่างๆตามลำดับความสำคัญหรือความเร่งด่วน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(6, 3, 'ระดับที่ 3 คือ แสดงสมรรถนะระดับที่ 2 และเห็นความสัมพันธ์เชื่อมโยงที่ซับซ้อนของส่วนต่างๆ ของปัญหา/งาน
เชื่อมโยงเหตุปัจจัยที่ซับซ้อน อาทิ เหตุการณ์กรณีหนึ่งอาจมีสาเหตุได้หลายประการ หรือสามารถนำไปสู่เหตุการณ์สืบเนื่องได้หลายประการ อาทิ เหตุ ก. นำไปสู่เหตุ ข. เหตุ ข. นำไปสู่เหตุ ค. นำไปสู่  เหตุ ง. ฯลฯ)
วางแผนงานโดยกำหนดกิจกรรม ขั้นตอนการดำเนินงานต่างๆ ที่มีผู้เกี่ยวข้องหลายฝ่ายได้อย่างมีประสิทธิภาพ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(6, 4, 'ระดับที่ 4 คือ แสดงสมรรถนะระดับที่ 3 และสามารถคาดการณ์ล่วงหน้าเกี่ยวกับปัญหา/งานจากความเข้าใจความสัมพันธ์ของส่วนต่างๆ
แยกแยะองค์ประกอบต่างๆ ของประเด็น ปัญหาที่มีเหตุปัจจัยเชื่อมโยงซับซ้อนเป็นรายละเอียดในชั้นต่างๆ อีกทั้งวิเคราะห์ว่าแง่มุมต่างๆของปัญหาหรือสถานการณ์หนึ่งๆสัมพันธ์กันอย่างไร คาดการณ์ว่าจะมีโอกาส หรืออุปสรรคอะไรบ้าง 
วางแผนงานที่ซับซ้อนโดยกำหนดกิจกรรม ขั้นตอนการดำเนินงานต่างๆที่มีหน่วยงานหรือผู้เกี่ยวข้องหลายฝ่าย รวมถึงคาดการณ์ปัญหา อุปสรรค และวางแนวทางการป้องกันแก้ไขไว้ล่วงหน้า', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(6, 5, 'ระดับที่ 5 คือ แสดงสมรรถนะระดับที่ 4 และกำหนดแผนงาน/ขั้นตอนการทำงานจากการศึกษาวิเคราะห์ในขั้นต่างๆ เพื่อเตรียมทางเลือกสำหรับการป้องกัน/แก้ไขปัญหาที่เกิดขึ้น
ใช้กรรมวิธีการวิเคราะห์ทางเทคนิคที่เหมาะสมในการแยกแยะประเด็นปัญหาที่ซับซ้อนออกเป็นส่วนๆ
ใช้เทคนิคการวิเคราะห์หลากหลายรูปแบบเพื่อหาทางเลือกต่างๆ ในการตอบคำถาม หรือแก้ปัญหา รวมถึงพิจารณาข้อดีข้อเสียของทางเลือกแต่ละทาง
วางแผนงานที่ซับซ้อนโดยกำหนดกิจกรรม ขั้นตอนการดำเนินงานต่างๆ ที่มีหน่วยงานหรือผู้เกี่ยวข้องหลายฝ่าย คาดการณ์ปัญหา อุปสรรค แนวทางการป้องกันแก้ไข อีกทั้งเสนอแนะทางเลือกและข้อดีข้อเสียไว้ให้', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(7, 0, 'ระดับที่ 0 คือ ไม่แสดงสมรรถนะด้านนี้อย่างชัดเจน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(7, 1, 'ระดับที่ 1 คือ ใช้กฎพื้นฐานทั่วไป
ใช้กฎพื้นฐาน หลักเกณฑ์ ตลอดจนหลักสามัญสำนึกทั่วไปในการปฏิบัติหน้าที่ ระบุประเด็นปัญหา หรือแก้ปัญหาในงาน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(7, 2, 'ระดับที่ 2 คือ แสดงสมรรถนะระดับที่ 1 และประยุกต์ประสบการณ์
พิจารณารูปแบบของข้อมูลแล้วสามารถระบุแนวโน้ม หรือระบุข้อมูลที่ขาดหายไปได้
ประยุกต์ประสบการณ์และบทเรียนในอดีตมาใช้ในการปฏิบัติหน้าที่ ระบุประเด็นปัญหา หรือแก้ปัญหาในงาน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(7, 3, 'ระดับที่ 3 คือ แสดงสมรรถนะระดับที่ 2 และประยุกต์ทฤษฎีหรือแนวคิดซับซ้อน
ประยุกต์ทฤษฎีหรือแนวคิดที่ซับซ้อนมาใช้พิจารณาสถานการณ์ปัจจุบัน ระบุประเด็นปัญหาหรือแก้ปัญหาในงานได้อย่างลึกซึ้ง แยบคาย แม้ในบางกรณี แนวคิดที่นำมาใช้และสถานการณ์ที่ประสบอยู่ดูเหมือนจะไม่มีความเกี่ยวข้องเชื่อมโยงกันเลยก็ตาม', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(7, 4, 'ระดับที่ 4 คือ แสดงสมรรถนะระดับที่ 3 และอธิบายปัญหา/งานในภาพองค์รวม
พิจารณาสถานการณ์ ประเด็น หรือปัญหาซับซ้อนด้วยกรอบแนวคิดและวิธีพิจารณาแบบมองภาพองค์รวม และอธิบายให้ผู้อื่นเข้าใจได้โดยง่าย
จัดการสังเคราะห์ข้อมูล สรุปแนวคิด ทฤษฎี องค์ความรู้ ฯลฯ ที่ซับซ้อนเป็นคำอธิบายที่สามารถเข้าใจได้โดยง่ายและเป็นประโยชน์ต่องาน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(7, 5, 'ระดับที่ 5 คือ แสดงสมรรถนะระดับที่ 4 และคิดนอกกรอบเพื่อสร้างสรรค์องค์ความรู้ใหม่
คิดนอกกรอบ พิจารณาสิ่งต่างๆในงานด้วยมุมมองที่แตกต่าง อันนำไปสู่การประดิษฐ์คิดค้น การสร้างสรรค์และนำเสนอรูปแบบ วิธี ตลอดจนองค์ความรู้ใหม่ที่ไม่เคยปรากฎมาก่อนและเป็นประโยชน์ต่องาน ภาคราชการ หรือสังคมและประเทศชาติโดยรวม', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(8, 0, 'ระดับที่ 0 คือ ไม่แสดงสมรรถนะด้านนี้อย่างชัดเจน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(8, 1, 'ระดับที่ 1 คือ ให้ความสำคัญของการพัฒนาศักยภาพ
ขวนขวายหาโอกาสพัฒนาตนเอง และสนับสนุนชักชวนให้ผู้อื่นเข้าร่วมกิจกรรมพัฒนาความรู้ ศักยภาพ ฯลฯ ในเรื่องที่เกี่ยวกับภารกิจที่ตนรับผิดชอบอยู่
เชื่อมั่นว่าผู้อื่นมีความประสงค์ และมีความสามารถที่จะเรียนรู้และปรับปรุงพัฒนาตนเองให้ดียิ่งๆ ขึ้นไป', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(8, 2, 'ระดับที่ 2 คือแสดงสมรรถนะระดับที่ 1 และสอนงานและให้คำแนะนำเกี่ยวกับวิธีปฏิบัติงาน
ให้คำแนะนำอย่างละเอียด และ/หรือสาธิตวิธีปฏิบัติงาน เพื่อเป็นตัวอย่าง
สอนงาน หรือให้คำแนะนำที่เฉพาะเจาะจงเกี่ยวกับการพัฒนางานหรือการปฏิบัติตน
ชี้แนะแหล่งข้อมูล และทรัพยากรอื่นๆ เพื่อใช้ในการพัฒนาของผู้อื่น', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(8, 3, 'ระดับที่ 3 คือแสดงสมรรถนะระดับที่ 2 และให้เหตุผลประกอบการสอน/แนะนำ และให้การสนับสนุนอื่นๆ เกี่ยวกับการปฏิบัติงาน พร้อมทั้งตรวจสอบว่าผู้รับการสอนมีความเข้าใจ
ให้แนวทาง หรือสาธิตการปฏิบัติงาน เพื่อเป็นตัวอย่าง พร้อมทั้งอธิบายเหตุผลประกอบ
ให้การสนับสนุน หรือการช่วยเหลือในภาคปฏิบัติ เพื่อให้ผู้อื่นปฏิบัติงาน/ปฏิบัติตนได้อย่างมีประสิทธิภาพยิ่งขึ้น (เช่น สนับสนุนทรัพยากร อุปกรณ์ ข้อมูล ข้อแนะนำ ในฐานะผู้มีประสบการณ์มาก่อนฯลฯ) ที่เหมาะสมแก่การปฏิบัติ
ถามคำถาม ทดสอบ หรือใช้วิธีการอื่นๆ เพื่อตรวจสอบว่าผู้อื่นเข้าใจคำอธิบายหรือไม่', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(8, 4, 'ระดับที่ 4 คือแสดงสมรรถนะระดับที่ 3 และให้โอกาสผู้อื่นได้พัฒนาตนเอง
ให้โอกาสผู้อื่นเสนอแนะวิธีการเรียนรู้ และพัฒนาผลการปฏิบัตินอกเหนือไปจากวิธีปฏิบัติตามปกติ
สนับสนุนให้มีการเรียนรู้และแลกเปลี่ยนความรู้ในงานที่มีหน้าที่ความรับผิดชอบใกล้เคียงหรือเชื่อมโยงกัน เพื่อพัฒนาความสามารถ และประสบการณ์ในการมองภาพรวมของบุคลากร', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(8, 5, 'ระดับที่ 5 คือแสดงสมรรถนะระดับที่ 4 และให้คำติชมผลการปฏิบัติเพื่อส่งเสริมการพัฒนาอย่างต่อเนื่อง
ให้คำติชมผลการปฏิบัติงาน/ปฏิบัติตนของผู้อื่นเพื่อส่งเสริมการพัฒนาการปฏิบัติที่ต่อเนื่อง 
ติติงพฤติกรรมที่ไม่เหมาะสมโดยเฉพาะเจาะจงเป็นกรณีไป โดยปราศจากอคติต่อตัวบุคคล 
เมื่อให้ความเห็นต่อผลการปฏิบัติปัจจุบันก็แสดงความคาดหวังในเชิงบวกว่าผู้ได้รับคำติชมมีศักยภาพและจะพัฒนาไปได้ดีในทางใด 
ให้คำแนะนำที่เหมาะสมกับบุคลิก ความสนใจ ความสามารถเฉพาะบุคคล ฯลฯ เพื่อการปรับปรุงพัฒนาที่เหมาะสม', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(9, 0, 'ระดับที่ 0 คือ ไม่แสดงสมรรถนะด้านนี้อย่างชัดเจน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(9, 1, 'ระดับที่ 1 คือ สั่งให้กระทำสิ่งต่างๆ ตามมาตรฐาน หรือตามกฎระเบียบข้อบังคับที่กำหนดไว้
สั่งให้กระทำสิ่งต่างๆ ตามมาตรฐานหรือตามกฎระเบียบข้อบังคับที่กำหนดไว้
มอบหมายงานประจำในรายละเอียดให้ผู้อื่นดำเนินการ เพื่อตนเองจะได้มีเวลาไปจัดการงานราชการอื่นๆ ที่มีผลในเชิงกลยุทธ์มากขึ้น', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(9, 2, 'ระดับที่ 2 คือแสดงสมรรถนะระดับที่ 1 และกำหนดขอบเขตข้อจำกัดในการกระทำสิ่งต่างๆ 
ปฏิเสธข้อเรียกร้องของผู้อื่นหรือหน่วยงานกำกับดูแล (ทั้งในภาครัฐและเอกชน) ที่ขาดเหตุผลหรือผิดกฎหมายและระเบียบที่วางไว้
กำหนดชัดว่าพฤติกรรมหรือการปฏิบัติงานใดของผู้ใต้บังคับบัญชาหรือหน่วยงานภายใต้การกำกับดูแลเป็นที่ยอมรับไม่ได้
ปรับสถานการณ์ เพื่อจำกัดทางเลือกของผู้อื่นหรือ เพื่อบีบคั้นให้ผู้อื่นประพฤติปฏิบัติในกรอบที่ถูกต้องตามกฎหมายหรือระเบียบปฏิบัติอื่นๆ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(9, 3, 'ระดับที่ 3 คือแสดงสมรรถนะระดับที่ 2 และสั่งให้ปรับปรุงผลงานให้ได้ตามมาตรฐาน/ปรับมาตรฐานให้ดีขึ้น
กำหนดมาตรฐานผลงานแบบใหม่ แตกต่าง หรือสูงขึ้น
สั่งให้ไปปรับปรุงผลงานในเชิงปริมาณหรือคุณภาพให้เข้าเกณฑ์มาตรฐาน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(9, 4, 'ระดับที่ 4 คือแสดงสมรรถนะระดับที่ 3 และติดตามควบคุมให้ปฏิบัติตามมาตรฐานหรือตามกฎหมายข้อบังคับ
หมั่นควบคุมตรวจตราการปฏิบัติหน้าที่ของหน่วยงานที่อยู่ภายใต้การกำกับดูแลให้เป็นไปอย่างถูกต้องตามกฎหมาย
ออกคำเตือนโดยชัดแจ้งว่าจะเกิดอะไรขึ้นหากผลงานไม่ได้มาตรฐานที่กำหนดไว้หรือกระทำการละเมิดกฎหมาย', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(9, 5, 'ระดับที่ 5 คือแสดงสมรรถนะระดับที่ 4 และจัดการกับผลงานไม่ดีหรือสิ่งผิดกฎหมายอย่างเด็ดขาดตรงไปตรงมา
ใช้วิธีเผชิญหน้าอย่างเปิดเผยตรงไปตรงมาเมื่อผู้อื่นหรือหน่วยงานภายใต้การกำกับดูแลมีปัญหาผลงานไม่ดีหรือทำผิดกฎหมายอย่างร้ายแรง
ไล่ออกหรือจับกุมลงโทษในกรณีที่มีหลักฐานเหตุผลสมควร', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(10, 0, 'ระดับที่ 0 คือ ไม่แสดงสมรรถนะด้านนี้อย่างชัดเจน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(10, 1, 'ระดับที่ 1 คือ หาข้อมูลในระดับต้น
หาข้อมูลโดยการถามจากผู้ที่เกี่ยวข้องโดยตรง
ใช้ข้อมูลที่มีอยู่ หรือหาจากแหล่งข้อมูลที่มีอยู่แล้ว', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(10, 2, 'ระดับที่ 2 คือ แสดงสมรรถนะระดับที่ 1 และใช้วิธีการสืบเสาะหาข้อมูล
สืบเสาะปัญหาหรือสถานการณ์อย่างลึกซึ้งกว่าการตั้งคำถามตามปรกติธรรมดา
สืบเสาะจากผู้ที่ใกล้ชิดกับเหตุการณ์ หรือประเด็นปัญหามากที่สุด', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(10, 3, 'ระดับที่ 3 คือ แสดงสมรรถนะระดับที่ 2 และหาข้อมูลแบบเจาะลึก
ซักถามคำถามเจาะลึกอย่างต่อเนื่อง เพื่อหาต้นตอของสถานการณ์ ปัญหา หรือโอกาสที่ซ่อนเร้นอยู่ในเบื้องลึก
สอบถามทัศนะความคิดเห็น ภูมิหลังประวัติความเป็นมา ประสบการณ์ ฯลฯ จากผู้รู้ที่ไม่ได้เกี่ยวข้องโดยตรง', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(10, 4, 'ระดับที่ 4 คือ แสดงสมรรถนะระดับที่ 3 และสืบค้นข้อมูลอย่างเป็นระบบ
ดำเนินการเก็บข้อมูลที่จำเป็นในช่วงเวลาหนึ่งๆ อย่างเป็นระบบ
สืบค้นจากแหล่งข้อมูลที่แปลกใหม่แตกต่างจากปรกติธรรมดาทั่วไป
ลงมือสืบค้นวิจัยเอง หรือมอบหมายให้ผู้อื่นเก็บข้อมูลอย่างเป็นกิจจะลักษณะจากหนังสือพิมพ์ นิตยสาร ระบบสืบค้นโดยอาศัยเทคโนโลยีสารสนเทศ ตลอดจนแหล่งข่าวต่างๆ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(10, 5, 'ระดับที่ 5 คือ แสดงสมรรถนะระดับที่ 4 และวางระบบการสืบค้น เพื่อหาข้อมูลอย่างต่อเนื่อง
วางระบบการสืบค้น เพื่อให้มีข้อมูลที่ทันเหตุการณ์ป้อนเข้ามาอย่างต่อเนื่อง
กำหนดมอบหมายให้ผู้อื่นทำการสืบค้นหาข้อมูลให้อย่างสม่ำเสมอเป็นกิจวัตร', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(11, 0, 'ระดับที่ 0 คือ ไม่แสดงสมรรถนะด้านนี้อย่างชัดเจน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(11, 1, 'ระดับที่ 1 คือ เห็นคุณค่าและให้ความนสนใจวัฒนธรรมอื่น
ภาคภูมิใจในวัฒนธรรมไทยแต่ในขณะเดียวกันก็เห็นคุณค่าและแสดงความสนใจเรียนรู้ วัฒนธรรม ขนบธรรมเนียม ประเพณีปฏิบัติของชนชาติต่างๆ
ไม่แสดงอาการดูถูกวัฒนธรรมอื่นว่าด้อยกว่า
เห็นความจำเป็นในการปรับเปลี่ยนพฤติกรรมของตนให้สอดคล้องกับบริบททางวัฒนธรรมที่เปลี่ยนไปในที่ต่างๆ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(11, 2, 'ระดับที่ 2 คือ แสดงสมรรถนะระดับที่ 1 และเข้าใจและปรับตัวให้สอดคล้องกับวัฒนธรรมใหม่
เข้าใจมารยาท กาลเทศะ และธรรมเนียมปฏิบัติของวัฒนธรรมที่แตกต่างและพยายามปรับตัวให้สอดคล้องกลมกลืน
สื่อสารและสนทนาด้วยวิธีการเนื้อหาและถ้อยคำที่เหมาะสมกับวัฒนธรรมของชนชาติต่างๆ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(11, 3, 'ระดับที่ 3 คือ แสดงสมรรถนะระดับที่ 2 และเข้าใจในวัฒนธรรมต่างๆ อย่างลึกซึ้ง
เข้าใจบริบท และบรรทัดฐานที่แฝง (unspoken norms) อยู่ในวัฒนธรรมต่างๆ
เข้าใจรากฐานทางวัฒนธรรมที่แตกต่างกันของบุคคลอันทำให้เข้าใจวิธีการปฏิบัติงานหรือความคิดเห็นของบุคคลแตกต่างกัน
ไม่ด่วนสรุปบุคคลจากประสบการณ์หรือความแตกต่างทางวัฒนธรรม เชื้อชาติ เผ่าพันธุ์แต่ใช้ความรู้นั้นให้เป็นประโยชน์ในการสื่อสารทำความเข้าใจและสร้างสรรค์ผลสัมฤทธิ์ เพื่อประโยชน์ร่วมกัน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(11, 4, 'ระดับที่ 4 คือ แสดงสมรรถนะระดับที่ 3 และสร้างความยอมรับนับถือท่ามกลางความแตกต่างทางวัฒนธรรม
สร้างความยอมรับนับถือ ไว้วางใจในหมู่ผู้คนต่างวัฒนธรรม เพื่อประสานความร่วมมือและสัมพันธไมตรีอันดี
ริเริ่มเพื่อสนับสนุนให้เกิดการทำงานร่วมกัน เพื่อเชื่อมสัมพันธภาพระหว่างประเทศทั้งระดับทวิภาคีและพหุภาคี', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(11, 5, 'ระดับที่ 5 คือ แสดงสมรรถนะระดับที่ 4 และปรับแผนงานและวิธีการทำงานให้สอดคล้องกับบริบททางวัฒนธรรม
ไกล่เกลี่ยข้อพิพาทระหว่างวัฒนธรรมที่แตกต่างกันบนพื้นฐานของความเข้าใจอย่างลึกซึ้งในแต่ละวัฒนธรรม
ปรับเปลี่ยนกลยุทธ์ ท่าที ให้เหมาะสมสอดคล้องกับวัฒนธรรมที่แตกต่าง เพื่อเพิ่มศักยภาพในการเจรจาต่อรอง', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(12, 0, 'ระดับที่ 0 คือ ไม่แสดงสมรรถนะด้านนี้อย่างชัดเจน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(12, 1, 'ระดับที่ 1 คือ เข้าใจความหมายที่ผู้อื่นต้องการสื่อสาร
เข้าใจความหมายที่ผู้อื่นสื่อสารด้วยภาษาโดยสื่อต่างๆ สามารถจับใจความได้ สรุปเนื้อหาเรื่องราวได้ถูกต้องครบประเด็น', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(12, 2, 'ระดับที่ 2 คือ แสดงสมรรถนะระดับที่ 1 และเข้าใจอารมณ์ความรู้สึกและคำพูด
เข้าใจทั้งเนื้อหาของสารและนัยเชิงอารมณ์ (จากการสังเกตอวัจนภาษา เช่น ท่าทาง การแสดงออกทางสีหน้า หรือน้ำเสียง) ของผู้ที่ติดต่อด้วย', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(12, 3, 'ระดับที่ 3 คือ แสดงสมรรถนะระดับที่ 2 และเข้าใจความหมายแฝงในอากัปกิริยาและคำพูด
สามารถตีความหมายเบื้องลึกที่ไม่ได้แสดงออกอย่างชัดเจนในคำพูดหรือน้ำเสียง
เข้าใจความคิด ความกังวล หรือความรู้สึกของผู้อื่น ณ เวลาขณะนั้น ทั้งที่แสดงออกมาเพียงเล็กน้อย หรือไม่ได้แสดงออกเลยก็ตาม
สามารถระบุลักษณะนิสัยหรือจุดเด่นอย่างใดอย่างหนึ่งของผู้ที่ติดต่อด้วยได้', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(12, 4, 'ระดับที่ 4 คือ แสดงสมรรถนะระดับที่ 3 และใช้ความเข้าใจในการสื่อสารทั้งที่เป็นคำพูด และความหมานัยแฝงในการสื่อสารกับผู้นั้นได้อย่างเหมาะสม
แสดงความเข้าใจในนัยของพฤติกรรมและอารมณ์ความรู้สึกของผู้อื่น
ใช้ความเข้าใจในบุคคลนั้นให้เป็นประโยชน์ในการผูกมิตรทำความรู้จัก หรือติดต่อประสานงานในโอกาสต่างๆ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(12, 5, 'ระดับที่ 5 คือ แสดงสมรรถนะระดับที่ 4 และเข้าใจสาเหตุของพฤติกรรมผู้อื่น
แสดงความเข้าใจเบื้องลึกถึงสาเหตุของพฤติกรรม หรือปัญหาของผู้อื่นตลอดจนเข้าใจสาเหตุหรือแรงจูงใจในระยะยาวของพฤติกรรมและความรู้สึกของผู้อื่น
สามารถแสดงทัศนคติที่เป็นธรรมและเหมาะสมเกี่ยวกับจุดอ่อนและจุดเด่นทางพฤติกรรมและลักษณะนิสัยของผู้อื่น
มีจิตวิทยาในการใช้ความเข้าใจผู้อื่นเพื่อเป็นพื้นฐานในการทำงานร่วมกัน การเจรจาทำความเข้าใจ การให้ความรู้หรือให้บริการ การสอนงาน การพัฒนาบุคลากร ฯลฯ ตามภารกิจในงาน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(13, 0, 'ระดับที่ 0 คือ ไม่แสดงสมรรถนะด้านนี้อย่างชัดเจน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(13, 1, 'ระดับที่ 1 คือ เข้าใจโครงสร้างอย่างเป็นทางการขององค์กร
เข้าใจสายการบังคับบัญชาและโครงสร้างองค์กรของหน่วยงานที่ตนสังกัดอยู่ กฎระเบียบ ตลอดจนขั้นตอนกระบวนการปฏิบัติงานต่างๆ และนำความเข้าใจนี้มาใช้ในการติดต่อประสานงาน รายงานผล ฯลฯ ในหน้าที่ได้ถูกต้อง', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(13, 2, 'ระดับที่ 2 คือ แสดงสมรรถนะระดับที่ 1 และเข้าใจโครงสร้างอย่างไม่เป็นทางการขององค์กร
เข้าใจสัมพันธภาพอย่างไม่เป็นทางการระหว่างบุคคลในองค์กร ตลอดจนผู้มีอำนาจตัดสินใจและผู้มีอิทธิพลต่อการตัดสินใจในระดับต่างๆ และนำความเข้าใจนี้มาใช้ประโยชน์โดยมุ่งผลสัมฤทธิ์และประโยชน์ของภาคราชการเป็นสำคัญ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(13, 3, 'ระดับที่ 3 คือ แสดงสมรรถนะระดับที่ 2 และเข้าใจวัฒนธรรมองค์กร
เข้าใจวัฒนธรรม ประเพณีปฏิบัติ ค่านิยมขององค์กรภาครัฐโดยรวม วิธีการสื่อสารให้มีประสิทธิภาพ ฯลฯ และนำความเข้าใจนี้มาใช้ประโยชน์ในงาน
เข้าใจข้อจำกัดขององค์กร รู้ว่าสิ่งใดอาจกระทำได้หรือไม่อาจกระทำให้บรรลุผลได้ในสถานการณ์ต่างๆ เพื่อใช้พิจารณาดำเนินการต่างๆในงานตามกาละเทศะที่เหมาะสม', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(13, 4, 'ระดับที่ 4 คือ แสดงสมรรถนะระดับที่ 3 และเข้าใจกระแสการเมืองในองค์กรและในภาครัฐโดยรวม
เข้าใจนโยบายภาครัฐในภาพรวม อีกทั้งเข้าใจผลที่จะมีต่อหน่วยงานและบทบาทหน้าที่ของตนทั้งในภาคนโยบายและภาคปฏิบัติ
เข้าใจกลไกและสัมพันธภาพระหว่างภาคการเมืองและระบบราชการ เพื่อใช้ในการผลักดันภารกิจในหน้าที่รับผิดชอบได้อย่างมีประสิทธิภาพในจังหวะโอกาสที่เหมาะสมให้บรรลุผลเป็นประโยชน์ต่อส่วนรวมตามแผนงานที่กำหนดไว้', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(13, 5, 'ระดับที่ 5 คือ แสดงสมรรถนะระดับที่ 4 และเข้าใจมูลเหตุพื้นฐานของพฤติกรรมองค์กร
เข้าใจมูลเหตุพื้นฐานของพฤติกรรมองค์กรในหน่วยงานของตนและของภาครัฐโดยรวม ตลอดจนปัญหา และโอกาสที่มีอยู่ และนำความเข้าใจนี้มาขับเคลื่อนการปฏิบัติงานในส่วนที่ตนดูแลรับผิดชอบอยู่อย่างเป็นระบบ
เข้าใจประเด็นปัญหาทางการเมือง เศรษฐกิจ สังคม ฯลฯ ทั้งภายในและภายนอกประเทศที่มีผลกระทบต่อนโยบายภาครัฐและภารกิจของหน่วยงานของตน เพื่อใช้แปลงวิกฤติให้เป็นโอกาส กำหนดจุดยืนและท่าทีตามภารกิจในหน้าที่ได้อย่างสอดคล้องเหมาะสมโดยมุ่งประโยชน์ของชาติเป็นสำคัญ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(14, 0, 'ระดับที่ 0 คือ ไม่แสดงสมรรถนะด้านนี้อย่างชัดเจน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(14, 1, 'ระดับที่ 1 คือ เห็นโอกาสหรือปัญหาระยะสั้นและลงมือดำเนินการ
เล็งเห็นโอกาสในขณะนั้นและไม่รีรอที่จะนำโอกาสนั้นมาใช้ประโยชน์ในงาน
เล็งเห็นปัญหา อุปสรรค และลงมือหาวิธีแก้ไขโดยไม่รอช้า', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(14, 2, 'ระดับที่ 2 คือ แสดงสมรรถนะระดับที่ 1 และจัดการปัญหาเฉพาะหน้าหรือเหตุวิกฤต
ลงมืออย่างฉับไวเมื่อเกิดปัญหาเฉพาะหน้าหรือในเวลาวิกฤติ
กระทำการแก้ไขปัญหาอย่างเร่งด่วนในขณะที่คนส่วนใหญ่จะวิเคราะห์สถานการณ์ก่อนและรอให้ปัญหาคลี่คลายไปเอง
รู้จักพลิกแพลง ยืดหยุ่น ประนีประนอมเมื่อเผชิญอุปสรรค
มีใจเปิดกว้าง ยอมรับความคิดแปลกใหม่และแหวกแนวที่อาจเป็นประโยชน์ต่อการแก้ไขปัญหา', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(14, 3, 'ระดับที่ 3 คือ แสดงสมรรถนะระดับที่ 2 และลงมือกระทำการล่วงหน้า เพื่อสร้างโอกาส หรือหลีกเลี่ยงปัญหาระยะสั้น
คาดการณ์และลงมือกระทำการล่วงหน้าเพื่อสร้างโอกาส หรือหลีกเลี่ยงปัญหาที่อาจเกิดขึ้นได้ในระยะเวลา 3 เดือนข้างหน้า
ทดลองใช้วิธีการที่แปลกใหม่ในการแก้ไขปัญหาหรือสร้างสรรค์สิ่งใหม่ให้เกิดขึ้นในวงราชการ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(14, 4, 'ระดับที่ 4 คือ แสดงสมรรถนะระดับที่ 3 และเตรียมการล่วงหน้า เพื่อสร้างโอกาส หรือหลีกเลี่ยงปัญหาในระยะยาว
คาดการณ์และลงมือกระทำการล่วงหน้าเพื่อสร้างโอกาส หรือหลีกเลี่ยงปัญหาที่อาจเกิดขึ้นได้ในระยะเวลา 4-12 เดือนข้างหน้า
คิดนอกกรอบเพื่อหาวิธีการที่แปลกใหม่และสร้างสรรค์ในการแก้ไขปัญหาที่คาดว่าจะเกิดขึ้นในอนาคต', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(14, 5, 'ระดับที่ 5 คือ แสดงสมรรถนะระดับที่ 4 และเตรียมการณ์ล่วงหน้า เพื่อสร้างโอกาส หรือหลีกเลี่ยงปัญหาอย่างยั่งยืน
คาดการณ์และลงมือกระทำการล่วงหน้าเพื่อสร้างโอกาส หรือหลีกเลี่ยงปัญหาที่อาจเกิดขึ้นได้ในระยะเวลามากกว่า 12 เดือนข้างหน้า
สร้างบรรยากาศของการคิดริเริ่มให้เกิดขึ้นในหน่วยงานและกระตุ้นให้เพื่อนร่วมงานเสนอความคิดใหม่ๆในการทำงาน เพื่อแก้ปัญหาหรือสร้างโอกาสในระยะยาว', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(15, 0, 'ระดับที่ 0 คือ ไม่แสดงสมรรถนะด้านนี้อย่างชัดเจน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(15, 1, 'ระดับที่ 1 คือ ต้องการทำงานให้ถูกต้องและชัดเจน รักษาระเบียบ
ตั้งใจทำงานให้ถูกต้อง สะอาดเรียบร้อย
ดูแลให้เกิดความเป็นระเบียบเรียบร้อยในสภาพแวดล้อมการทำงาน ปฏิบัติตามหลัก 5 ส.
ปฏิบัติตามขั้นตอนการปฏิบัติงาน กฎ ระเบียบที่วางไว้', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(15, 2, 'ระดับที่ 2 คือ แสดงสมรรถนะระดับที่ 1 และตรวจทานความถูกต้องของงานที่ตนรับผิดชอบ
ตรวจทานความถูกต้องของงานอย่างละเอียดรอบคอบเพื่อให้งานมีคุณภาพดี
ต้องการทราบมาตรฐานของผลงานในรายละเอียดเพื่อจะได้ปฏิบัติได้ถูกต้อง
ตระหนักถึงผลเสียที่อาจจะเกิดขึ้นกับตนเองหรือหน่วยงาน จากความผิดพลาดในการปฏิบัติงานของตน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(15, 3, 'ระดับที่ 3 คือ แสดงสมรรถนะระดับที่ 2 และดูแลความถูกต้องของงานทั้งของตนและผู้อื่น (ที่อยู่ในความรับผิดชอบของตน)
ตรวจสอบความถูกต้องโดยรวมของงานของตนเอง
ตรวจสอบความถูกต้องโดยรวมของงานผู้อื่นตามอำนาจหน้าที่  โดยอิงมาตรฐานการปฏิบัติงาน หรือกฎหมาย ข้อบังคับ กฎระเบียบที่เกี่ยวข้อง', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(15, 4, 'ระดับที่ 4 คือ แสดงสมรรถนะระดับที่ 3 และตรวจสอบขั้นตอนการปฏิบัติงาน
ตรวจสอบว่าผู้อื่นปฏิบัติตามขั้นตอนการทำงานที่วางไว้หรือไม่
ให้ความเห็นและชี้แนะให้ผู้อื่นปฏิบัติตามขั้นตอนการทำงานที่วางไว้ เพื่อความถูกต้องของงาน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(15, 5, 'ระดับที่ 5 คือ แสดงสมรรถนะระดับที่ 4 และกำกับตรวจสอบความถูกต้องและคุณภาพของข้อมูลหรือโครงการ และการปฏิบัติงานโดยละเอียด
ตรวจสอบความก้าวหน้าของโครงการตามกำหนดเวลาที่วางไว้ 
ตรวจสอบความถูกต้องและคุณภาพของข้อมูล หรือการปฏิบัติงานโดยละเอียด
ระบุข้อบกพร่องหรือข้อมูลที่ขาดหายไป และกำกับดูแลให้หาข้อมูลเพิ่มเติมเพื่อให้ได้ผลลัพธ์หรือผลงานที่มีคุณภาพตามเกณฑ์ที่กำหนด', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(16, 0, 'ระดับที่ 0 คือ ไม่แสดงสมรรถนะด้านนี้อย่างชัดเจน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(16, 1, 'ระดับที่ 1 คือ ปฏิบัติงานได้ตามอำนาจหน้าที่โดยไม่ต้องมีการกำกับดูแล
ปฏิบัติงานได้โดยไม่ต้องมีการกำกับดูแลใกล้ชิด
ตัดสินใจได้เองในภารกิจภายใต้ขอบเขตอำนาจหน้าที่รับผิดชอบของตน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(16, 2, 'ระดับที่ 2 คือ แสดงสมรรถนะระดับที่ 1 และปฏิบัติงานในหน้าที่อย่างมั่นใจ
กล้าตัดสินใจเรื่องที่พิจารณาโดยถี่ถ้วนแล้วว่าถูกต้องในหน้าที่ แม้จะไม่มีมติเอกฉันท์ หรือมีผู้ไม่เห็นด้วยอยู่บ้าง
แสดงออกอย่างสงบและมั่นใจในการปฏิบัติหน้าที่แม้ในสถานการณ์ที่มีความไม่แน่นอนอยู่บ้าง', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(16, 3, 'ระดับที่ 3 คือ แสดงสมรรถนะระดับที่ 2 และมั่นใจในความสามารถของตน
เชื่อมั่นในความรู้ความสามารถ และศักยภาพของตนว่าจะสามารถปฏิบัติหน้าที่ให้ประสบผลสำเร็จได้
อาจแสดงความมั่นใจอย่างเปิดเผยในการตัดสินใจหรือความสามารถของตน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(16, 4, 'ระดับที่ 4 คือ แสดงสมรรถนะระดับที่ 3 และมั่นใจในการทำงานที่ท้าทาย/ความคิดของตน
ชอบงานที่ท้าทายความสามารถ และรู้สึกตื่นเต้นยินดีที่ได้ทำงานนั้น
แสดงความคิดเห็นของตนอย่างสุภาพเมื่อไม่เห็นด้วยกับผู้บังคับบัญชา หรือผู้มีอำนาจ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(16, 5, 'ระดับที่ 5 คือ แสดงสมรรถนะระดับที่ 4 และเต็มใจทำงานที่ท้าทายมากและกล้าแสดงจุดยืนของตน
เต็มใจและรับอาสาปฏิบัติงานที่ท้าทาย หรือเสี่ยงภัยมาก
กล้ายืนหยัดเผชิญหน้ากับผู้บังคับบัญชาหรือผู้มีอำนาจ กล้าแสดงจุดยืนของตนอย่างชัดเจนตรงไปตรงมาหากไม่เห็นด้วยในประเด็นที่เป็นสาระสำคัญในบทบาทหน้าที่ของตน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(17, 0, 'ระดับที่ 0 คือ ไม่แสดงสมรรถนะด้านนี้อย่างชัดเจน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(17, 1, 'ระดับที่ 1 คือ มีความคล่องตัวในการปฏิบัติงาน
ปรับตัวได้แม้ประสบความยากลำบากทางกายภาพในงาน ไม่ยึดติดกับความสะดวกสบาย วัตถุแสดงฐานะทางสังคม หรือระดับอาวุโสในงาน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(17, 2, 'ระดับที่ 2 คือ แสดงสมรรถนะระดับที่ 1 และยอมรับความจำเป็นที่จะต้องปรับเปลี่ยน
ยอมรับและเข้าใจความคิดเห็นของผู้อื่น
เต็มใจที่จะเปลี่ยนความคิด ทัศนคติ เมื่อได้รับข้อมูลใหม่หรือหลักฐานที่ขัดแย้งกับความคิดเดิม', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(17, 3, 'ระดับที่ 3 คือ แสดงสมรรถนะระดับที่ 2 และใช้กฎระเบียบอย่างยืดหยุ่น
มีความคล่องตัวในการปฏิบัติหน้าที่ ใช้กฎเกณฑ์ กระบวนการปฏิบัติงานอย่างยืดหยุ่น มีวิจารณญาณในการปรับให้เข้ากับสถานการณ์เฉพาะหน้า เพื่อให้เกิดผลสัมฤทธิ์ในการปฏิบัติงานหรือ เพื่อให้บรรลุวัตถุประสงค์ขององค์กร', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(17, 4, 'ระดับที่ 4 คือ แสดงสมรรถนะระดับที่ 3 และปรับเปลี่ยนวิธีการดำเนินงาน เพื่อให้งานมีประสิทธิภาพ
ปรับเปลี่ยนวิธีการดำเนินงาน ให้เข้ากับสถานการณ์ แต่ยังคงเป้าหมายเดิมไว้
ปรับแก้ไขกฎระเบียบขั้นตอนการทำงานที่ล้าสมัย เพื่อเพิ่มประสิทธิภาพของหน่วยงาน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(17, 5, 'ระดับที่ 5 คือ แสดงสมรรถนะระดับที่ 4 และปรับเปลี่ยนแผนกลยุทธ์ทั้งหมด เพื่อให้งานมีประสิทธิภาพ
ปรับแผนกลยุทธ์ทั้งหมด เพื่อให้เหมาะสมกับสถานการณ์เฉพาะหน้า
ทบทวน สังคายนา กฎระเบียบและวิธีการทำงานของหน่วยงานที่ดูแลรับผิดชอบอยู่ใหม่ทั้งหมด เพื่อเพิ่มประสิทธิภาพของหน่วยงาน
ปรับเปลี่ยนองค์กร สายการบังคับบัญชา เป็นการเฉพาะกิจ เพื่อให้สามารถตอบสนองต่อสถานการณ์เฉพาะหน้าได้อย่างเหมาะสม', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(18, 0, 'ระดับที่ 0 คือ ไม่แสดงสมรรถนะด้านนี้อย่างชัดเจน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(18, 1, 'ระดับที่ 1 คือ นำเสนออย่างตรงไปตรงมา
นำเสนอความเห็นอย่างตรงไปตรงมาในการอภิปรายหรือนำเสนอผลงาน อาจยกเหตุผลความเป็นมา  ข้อมูล หรือความสนใจของผู้ฟังมาประกอบการพูดหรือการนำเสนอ หรือยกตัวอย่างที่เป็นรูปธรรมมาประกอบการนำเสนอ เช่น ภาพประกอบหรือการสาธิต เป็นต้น แต่ยังมิได้ปรับรูปแบบการนำเสนอตามความสนใจและระดับของผู้ฟัง', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(18, 2, 'ระดับที่ 2 คือ แสดงสมรรถนะระดับที่ 1 และใช้ความพยายามขั้นต้นในการจูงใจ
มีการเตรียมข้อมูลที่ใช้ในการนำเสนออย่างรอบคอบละเอียดถี่ถ้วน อาจมีการนำเสนอประเด็น ข้อคิดเห็นที่แตกต่างกันในการบรรยายหรืออภิปราย เพื่อความกระจ่างหรือเพื่อจูงใจให้เห็นด้วย', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(18, 3, 'ระดับที่ 3 คือ แสดงสมรรถนะระดับที่ 2 และใช้ศิลปะการจูงใจ
ปรับรูปแบบการนำเสนอและอภิปรายให้เหมาะสมกับความสนใจและระดับของผู้ฟัง คาดการณ์ถึงผลกระทบของสิ่งที่นำเสนอและภาพพจน์ของผู้พูดที่จะมีต่อผู้ฟัง
ใช้รูปแบบการนำเสนอที่วางแผนไว้ล่วงหน้ามาอย่างดี ตื่นตาตื่นใจและแปลกใหม่ เพื่อให้เกิดผลกระทบต่อผู้ฟังในทิศทางที่ตนต้องการ อีกทั้งคาดการณ์และเตรียมการไว้ล่วงหน้าเพื่อรับมือกับปฏิกิริยาของผู้ฟังที่อาจเกิดขึ้น', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(18, 4, 'ระดับที่ 4 คือ แสดงสมรรถนะระดับที่ 3 และใช้อิทธิพลทางอ้อมในการจูงใจ
โน้มน้าวใจผู้ฟังทางอ้อมด้วยการชักจูงเป็นลูกโซ่ เช่น “ให้คุณ ก. แสดงให้คุณ ข. เห็น เพื่อให้คุณ ข.ไปบอกคุณ ค. ต่อไปอีกทอดหนึ่ง” เป็นต้น มีการปรับแต่ละขั้นตอนในการสื่อสาร นำเสนอ และจูงใจให้เหมาะสมกับผู้ฟังแต่ละกลุ่มหรือแต่ละราย
ใช้ผู้เชี่ยวชาญในด้านนั้นๆ มาช่วยให้การสื่อสารจูงใจได้ผลดียิ่งขึ้น', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(18, 5, 'ระดับที่ 5 คือ แสดงสมรรถนะระดับที่ 4 และใช้กลยุทธ์ซับซ้อนในการจูงใจ
สร้างกลุ่มผู้สนับสนุนอยู่เบื้องหลังหรือกลุ่มแนวร่วม เพื่อช่วยสนับสนุนผลักดันแนวคิด แผนงานโครงการ ฯลฯ ให้สัมฤทธิ์ผล
ใช้ความเข้าใจอย่างถ่องแท้เกี่ยวกับปฏิกิริยาของผู้รับสาร พฤติกรรมกลุ่ม จิตวิทยามวลชน ฯลฯ ให้เป็นประโยชน์ในการสื่อสารจูงใจ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(19, 0, 'ระดับที่ 0 คือ ไม่แสดงสมรรถนะด้านนี้อย่างชัดเจน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(19, 1, 'ระดับที่ 1 คือ บริหารการประชุมได้ดีและคอยแจ้งข่าวสารความเป็นไปอยู่เสมอ
กำหนดประเด็นหัวข้อในการประชุม วัตถุประสงค์ ควบคุมเวลา และแจกแจงหน้าที่รับผิดชอบให้แก่บุคคลในกลุ่มได้
แจ้งข่าวสารความเป็นไปให้ผู้ที่จะได้รับผลกระทบจากการตัดสินใจรับทราบอยู่เสมอแม้ไม่จำเป็นต้องกระทำ
อธิบายเหตุผลในการตัดสินใจให้ผู้อื่นทราบ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(19, 2, 'ระดับที่ 2 คือ แสดงสมรรถนะระดับที่ 1 และเป็นผู้นำในการทำงานของกลุ่ม
ส่งเสริมให้กลุ่มทำงานอย่างมีประสิทธิภาพ
ลงมือกระทำการเพื่อช่วยให้กลุ่มปฏิบัติหน้าที่ได้อย่างเต็มประสิทธิภาพ
กำหนดเป้าหมาย ทิศทางที่ชัดเจน ใช้โครงสร้างที่เหมาะสม เลือกคนให้เหมาะกับงาน หรือใช้วิธีการอื่นๆ เพื่อช่วยสร้างสภาวะที่จะทำให้กลุ่มทำงานได้ดีขึ้น
เปิดใจกว้างรับฟังความคิดเห็นของผู้อื่นเพื่อสนับสนุนให้กลุ่มหรือกระบวนการปฏิบัติงานมีประสิทธิภาพยิ่งขึ้น
สร้างขวัญกำลังใจในการปฏิบัติงาน หรือให้โอกาสผู้ใต้บังคับบัญชาในการแสดงศักยภาพการทำงานอย่างเต็มที่ เพื่อเสริมประสิทธิภาพ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(19, 3, 'ระดับที่ 3 คือ แสดงสมรรถนะระดับที่ 2 และให้การดูแลและช่วยเหลือผู้ใต้บังคับบัญชา
เป็นที่ปรึกษาและให้การดูแลผู้ใต้บังคับบัญชา
ปกป้องผู้ใต้บังคับบัญชาและชื่อเสียงขององค์กร
จัดหาบุคลากร ทรัพยากร หรือข้อมูลที่สำคัญมาให้ เมื่อองค์กรต้องการ เพื่อให้การสนับสนุนที่จำเป็นแก่ผู้ใต้บังคับบัญชา
ช่วยเหลือให้ผู้ใต้บังคับบัญชาเข้าใจถึงการปรับเปลี่ยนที่เกิดขึ้นภายในองค์กรและความจำเป็นของการปรับเปลี่ยนนั้นๆ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(19, 4, 'ระดับที่ 4 คือ แสดงสมรรถนะระดับที่ 3 และประพฤติตนสมกับเป็นผู้นำ
กำหนดธรรมเนียมปฏิบัติประจำกลุ่มและประพฤติตนเป็นแบบอย่างที่ดีแก่ผู้ใต้บังคับบัญชา
ยึดหลักธรรมาภิบาล  (Good Governance) (นิติธรรม คุณธรรม โปร่งใส ความมีส่วนร่วม ความรับผิดชอบ ความคุ้มค่า) ในการปกครองผู้ใต้บังคับบัญชา
สนับสนุนการมีส่วนร่วมของผู้ใต้บังคับบัญชาในการอุทิศตนให้กับการปฏิบัติงานเพื่อสนองนโยบายประเทศและบรรลุภารกิจภาครัฐ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(19, 5, 'ระดับที่ 5 คือ แสดงสมรรถนะระดับที่ 4 และแสดงวิสัยทัศน์ที่ชัดเจนต่อผู้ใต้บังคับบัญชา
สื่อสารวิสัยทัศน์ที่มีพลัง สามารถรวมใจคนและสร้างแรงบันดาลใจให้ผู้ใต้บังคับบัญชาสามารถปฏิบัติงานให้ภารกิจสำเร็จลุล่วงได้จริง
เป็นผู้นำในการปรับเปลี่ยนขององค์กร ผลักดันให้การปรับเปลี่ยนดำเนินไปได้อย่างราบรื่นและประสบความสำเร็จได้ด้วยกลยุทธ์และวิธีดำเนินการที่เหมาะสม
มีวิสัยทัศน์ในการเล็งเห็นการเปลี่ยนแปลงในอนาคต และเตรียมการสร้างกลยุทธ์ให้กับองค์กรในการรับมือกับการเปลี่ยนแปลงนั้นๆ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(20, 0, 'ระดับที่ 0 คือ ไม่แสดงสมรรถนะด้านนี้อย่างชัดเจน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(20, 1, 'ระดับที่ 1 คือ ซาบซึ้งในงานศิลป์
เห็นคุณค่าและแสดงความชื่นชมในงานศิลปะต่างๆ รวมถึงแสดงความรักและหวงแหนในงานศิลป์ของชาติ
สนใจและอยากเข้าไปมีส่วนร่วมในการเรียนรู้  ติดตามหรือปฏิบัติงานศิลป์แขนงต่างๆ 
หมั่นฝึกฝนสร้างความชำนาญในงานศิลป์ของตนอย่างสม่ำเสมอ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(20, 2, 'ระดับที่ 2 คือ แสดงสมรรถนะระดับที่ 1 และเข้าใจรูปแบบต่างๆ ของงานศิลป์
แยกแยะความแตกต่างของงานศิลป์รูปแบบต่างๆ และอธิบายให้ผู้อื่นรับรู้ถึงความสวยงามของงานศิลป์เหล่านั้นได้
เข้าใจรูปแบบอย่างถ่องแท้และเห็นข้อเด่นของงานศิลป์รูปแบบต่างๆ และนำไปปรับใช้การพัฒนางานศิลป์ของตน
สร้างจิตสำนึกในคุณค่าศิลปะไทยให้เกิดการอนุรักษ์ในวงกว้าง', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(20, 3, 'ระดับที่ 3 คือ แสดงสมรรถนะระดับที่ 2 และประยุกต์เพื่อสร้างสรรค์งานศิลป์
นำอิทธิพลของยุคสมัยต่างๆ หรือแนวโน้มทางงานศิลป์ มาเป็นแรงบันดาลใจหรือพื้นฐานในการคิดค้นประดิษฐ์ผลงานศิลป์ของตน
ประยุกต์ประสบการณ์และความรู้ในงานศิลป์มาใช้ในการสร้างสรรงานศิลป์ของตน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(20, 4, 'ระดับที่ 4 คือ แสดงสมรรถนะระดับที่ 3 และสร้างแรงบันดาลใจใดยอาศัยงานศิลป์
ประยุกต์สิ่งที่ดีจากยุคต่างๆ มาใช้ในการรังสรรค์ผลงานให้น่าสนใจ และเป็นแรงบันดาลใจให้ผู้อื่นเกิดจิตสำนึกในการอนุรักษ์ศิลปวัฒนธรรมไทย
นำศาสตร์ทางศิลป์ในหลายแขนงมาผสมผสาน เพื่อสร้างสรรค์ผลงาน พิจารณาศาสตร์ต่างๆ ในมุมมองที่แตกต่างไป อันนำไปสู่การสร้างสรรค์ผลงานศิลป์ที่แตกต่าง', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(20, 5, 'ระดับที่ 5 คือ แสดงสมรรถนะระดับที่ 4 และรังสรรค์งานศิลป์ที่เป็นเอกลักษณ์เฉพาะตน
รังสรรค์งานศิลป์ที่มีเอกสักษณ์เฉพาะที่เป็นที่ยอมรับในวงการ ไม่ว่าจะเป็นการคิดสร้างสรรค์งานแนวใหม่ หรืออนุรักษ์ไว้ซึ่งขนบดั้งเดิมของงานศิลปกรรมไทย
คิดนอกกรอบ รูปแบบ วิธี ตลอดจนนำองค์ความรู้ใหม่ด้านงานศิลป์มารังสรรค์งานที่เป็นเอกลักษณ์เฉพาะตน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(21, 0, 'ระดับที่ 0 คือ ไม่แสดงสมรรถนะด้านนี้อย่างชัดเจน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(21, 1, 'ระดับที่ 1 คือ เชื่อมโยงงานเข้ากับบริบทของภาครัฐโดยรวม
สื่อสารให้ผู้อื่นเข้าใจว่าสิ่งที่ทำอยู่นั้นมีผลอย่างไรต่อสาธารณชน พยายามทำให้ภาพรวมชัดเจนและเข้าใจง่าย ช่วยให้ผู้อื่นเข้าใจว่าบทบาทของตนเกี่ยวข้องกับบริบทโดยรวมอย่างไร
เชื่อมโยงวิสัยทัศน์ของหน่วยงานกับเป้าหมาย วัตถุประสงค์และกลยุทธ์ของภาครัฐโดยรวมได้', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(21, 2, 'ระดับที่ 2 คือ แสดงสมรรถนะระดับที่ 1 และให้ผู้อื่นได้มีส่วนร่วมในการกำหนดวิสัยทัศน์
แบ่งปันความรับผิดชอบในการกำหนดวิสัยทัศน์ระยะยาวโดยให้ผู้อื่นได้มีส่วนร่วมหรือแสดงความคิดเห็นด้วย', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(21, 3, 'ระดับที่ 3 คือ แสดงสมรรถนะระดับที่ 2 และทำให้วิสัยทัศน์ได้รับการยอมรับ
สร้างความน่าเชื่อถือให้แก่วิสัยทัศน์โดยการสื่อสารในวงกว้างในหน่วยงานที่ปฏิบัติหน้าที่อยู่
แบ่งปันข้อมูลแนวโน้มภายในและภายนอกหน่วยงาน ตลอดจนชี้ว่าข้อมูลเหล่านั้นจะนำมาเป็นพื้นฐานในการกำหนดกลยุทธ์ของหน่วยงานได้อย่างไร', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(21, 4, 'ระดับที่ 4 คือ แสดงสมรรถนะระดับที่ 3 และสื่อสารวิสัยทัศน์
ถ่ายทอดวิสัยทัศน์ของหน่วยงานที่ดูแลรับผิดชอบอยู่ด้วยวิธีที่สร้างแรงบันดาลใจ ความกระตือรือร้น และความร่วมแรงร่วมใจให้บรรลุวิสัยทัศน์นั้น
ใช้วิสัยทัศน์นั้นในการกำหนดจุดร่วมและทิศทางสำหรับผู้คนทั้งหลาย โดยเฉพาะอย่างยิ่งในสภาวการณ์ที่กำลังเผชิญการเปลี่ยนแปลง', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(21, 5, 'ระดับที่ 5 คือ แสดงสมรรถนะระดับที่ 4 และใช้วิสัยทัศน์มาช่วยกำหนดนโยบายในงาน
คิดนอกกรอบ นำเสนอความคิดใหม่เพื่อใช้กำหนดนโยบายในงานเพื่อประโยชน์หรือโอกาสของภาครัฐหรือสาธารณชนโดยรวมอย่างที่ไม่มีผู้ใดคิดมาก่อน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(22, 0, 'ระดับที่ 0 คือ ไม่แสดงสมรรถนะด้านนี้อย่างชัดเจน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(22, 1, 'ระดับที่ 1 คือ เข้าใจกลยุทธ์ภาครัฐ
เข้าใจวิสัยทัศน์ ภารกิจ นโยบาย กลยุทธ์ภาครัฐ อีกทั้งเข้าใจว่ามีความเกี่ยวโยงกับภารกิจของหน่วยงานที่ตนดูแลรับผิดชอบอยู่อย่างไร
สามารถวิเคราะห์ปัญหา อุปสรรคหรือโอกาสของหน่วยงานตนในการบรรลุผลสัมฤทธิ์ได้', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(22, 2, 'ระดับที่ 2 คือ แสดงสมรรถนะระดับที่ 1 และประยุกต์ประสบการณ์ในการกำหนดกลยุทธ์การปฏิบัติงานของหน่วยงาน
ประยุกต์ประสบการณ์และบทเรียนในอดีตมาใช้กำหนดกลยุทธ์ของหน่วยงานให้สอดคล้องกับกลยุทธ์ภาครัฐ และสามารถบรรลุภารกิจที่กำหนดไว้
ใช้ความรู้ความเข้าใจในระบบราชการมาปรับกลยุทธ์ หรือยุทธวิธีในการปฏิบัติงานของหน่วยงานให้เหมาะสมกับสถานการณ์ภายในที่เกิดขึ้นได้', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(22, 3, 'ระดับที่ 3 คือ แสดงสมรรถนะระดับที่ 2 และประยุกต์ทฤษฎีหรือแนวคิดซับซ้อนในการกำหนดกลยุทธ์การปฏิบัติงานในอนาคต
ประยุกต์ทฤษฎี หรือแนวคิดซับซ้อนในการคิดและพัฒนาเป้าหมายหรือกลยุทธ์ในการปฏิบัติงานของหน่วยงานที่ตนดูแลรับผิดชอบอยู่
คิดโครงการหรือแผนงานที่ผลสัมฤทธิ์มีประโยชน์ระยะยาวต่องานที่ตนดูแลรับผิดชอบอยู่', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(22, 4, 'ระดับที่ 4 คือ แสดงสมรรถนะระดับที่ 3 และเชื่อมโยงสถานการณ์ในประเทศเพื่อกำหนดกลยุทธ์ในการปฏิบัติงานทั้งในปัจจุบันและในอนาคต
ประเมินและเชื่อมโยงสถานการณ์ ประเด็น หรือปัญหาทางเศรษฐกิจ สังคม การเมืองภายในประเทศที่ซับซ้อนด้วยกรอบแนวคิดและวิธีพิจารณาแบบมองภาพองค์รวม เพื่อใช้ในการกำหนดกลยุทธ์ภาครัฐ
คิดแผนหรือกลยุทธ์เชิงรุกในการปฏิบัติงานของหน่วยงาน เพื่อตอบสนองโอกาสหรือประเด็นปัญหาที่เกิดขึ้นจากสถานการณ์ภายในประเทศที่เปลี่ยนแปลงไป', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(22, 5, 'ระดับที่ 5 คือ แสดงสมรรถนะระดับที่ 4 และเชื่อมโยงสถานการณ์โลกเพื่อกำหนดกลยุทธ์ในการปฏิบัติงานทั้งในปัจจุบันและในอนาคต
ประเมินและเชื่อมโยงสถานการณ์ ประเด็น หรือปัญหาทางเศรษฐกิจ สังคม การเมืองของโลก เพื่อใช้ในการกำหนดกลยุทธ์ภาครัฐให้สอดคล้องกับบริบทของประเทศ
คิดแผนหรือกลยุทธ์เชิงรุกในการปฏิบัติงานของหน่วยงาน เพื่อตอบสนองโอกาสหรือประเด็นปัญหาที่เกิดขึ้นจากสถานการณ์ต่างประเทศที่เปลี่ยนแปลงไป', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(23, 0, 'ระดับที่ 0 คือ ไม่แสดงสมรรถนะด้านนี้อย่างชัดเจน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(23, 1, 'ระดับที่ 1 คือ เห็นความจำเป็นของการปรับเปลี่ยน
เห็นความจำเป็นของการปรับเปลี่ยน สามารถกำหนดทิศทางและขอบเขตของการปรับเปลี่ยนที่ควรเกิดขึ้นภายในองค์กรได้
เข้าใจถึงความจำเป็นในการปรับเปลี่ยน และปรับพฤติกรรมให้สอดคล้องกับการปรับเปลี่ยนนั้น', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(23, 2, 'ระดับที่ 2 คือ แสดงสมรรถนะระดับที่ 1 และสนับสนุนให้ผู้อื่นเข้าใจการปรับเปลี่ยนที่จะเกิดขึ้น
ช่วยเหลือให้ผู้อื่นเข้าใจถึงการปรับเปลี่ยนที่เกิดขึ้นภายในองค์กร ความจำเป็นและประโยชน์ของการปรับเปลี่ยนนั้นๆ
สนับสนุนความพยายามในการปรับเปลี่ยนองค์กร พร้อมทั้งเสนอแนะวิธีการที่จะช่วยให้การปรับเปลี่ยนดำเนินไปอย่างมีประสิทธิภาพมากขึ้น', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(23, 3, 'ระดับที่ 3 คือ แสดงสมรรถนะระดับที่ 2 และกระตุ้น และสร้างแรงจูงใจให้ผู้อื่นเห็นความสำคัญของการปรับเปลี่ยนที่เกิดขึ้น
 กระตุ้น และสร้างแรงจูงใจให้ผู้อื่นเห็นความสำคัญของการปรับเปลี่ยนที่เกิดขึ้นเพื่อให้เกิดการร่วมแรงร่วมใจให้เกิดการเปลี่ยนแปลงนั้นขึ้นจริง
เน้นย้ำ และสร้างความชัดเจนโดยการอธิบายสาเหตุ ความจำเป็น ประโยชน์ ฯลฯ ของการปรับเปลี่ยนที่เกิดขึ้นอยู่เสมอ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(23, 4, 'ระดับที่ 4 คือ แสดงสมรรถนะระดับที่ 3 และทำให้เห็นชัดเจนว่าการเปลี่ยนแปลงนั้นเปลี่ยนไปอย่างไร และดีขึ้นอย่างไร
เปรียบเทียบให้เห็นว่าสิ่งที่ควรจะเป็นและสิ่งที่ประพฤติปฏิบัติกันอยู่นั้นแตกต่างกันอย่างไร
ท้าทายความคิดของผู้อื่น แสดงให้เห็นโทษของการนิ่งเฉย และประโยชน์ของการเปลี่ยนแปลงจากสภาวการณ์ปัจจุบัน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(23, 5, 'ระดับที่ 5 คือ แสดงสมรรถนะระดับที่ 4 และวางแผนงานที่ดีสำหรับการปรับเปลี่ยนในองค์กร
สร้างวิสัยทัศน์และชี้ให้เห็นผลสัมฤทธิ์จากความพยายามในการปรับเปลี่ยนองค์กรที่กำลังจะดำเนินการ อีกทั้งเตรียมแผนการให้องค์กรสามารถรับมือกับการเปลี่ยนแปลงนั้นๆ ได้อย่างมีประสิทธิภาพไม่ระส่ำระสาย', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(24, 0, 'ระดับที่ 0 คือ ไม่แสดงสมรรถนะด้านนี้อย่างชัดเจน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(24, 1, 'ระดับที่ 1 คือ ไม่แสดงพฤติกรรมอันไม่เหมาะสม
ไม่แสดงพฤติกรรมไม่สุภาพหรือไม่เหมาะสม แม้จะรู้สึกว่าถูกกระตุ้นทางอารมณ์ แต่สามารถระงับการกระทำนั้นไว้ได้
อดกลั้นไม่แสดงพฤติกรรมหุนหันพลันแล่น', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(24, 2, 'ระดับที่ 2 คือ แสดงสมรรถนะระดับที่ 1 และหลีกเลี่ยงหรือเบี่ยงเบนสถานการณ์ที่ทำให้เกิดความรุนแรงทางอารมณ์ 
อาจเลี่ยงออกไปจากสถานการณ์ (ที่ทำให้เกิดความรุนแรงทางอารมณ์) ชั่วคราวหากกระทำได้ หรืออาจเปลี่ยนหัวข้อสนทนา หรือหยุดพักชั่วคราวเพื่อสงบสติอารมณ์', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(24, 3, 'ระดับที่ 3 คือ แสดงสมรรถนะระดับที่ 2 และมีพฤติกรรมตอบโต้ได้อย่างสงบ แม้จะถูกยั่วยุจากฝ่ายตรงข้าม 
รู้สึกถึงความรุนแรงทางอารมณ์ในระหว่างการสนทนา หรือการปฏิบัติงาน เช่น ความโกรธ ความผิดหวัง หรือความกดดัน แต่ไม่ได้แสดงออกมา ไม่โต้ตอบรุนแรงแม้จะถูกยั่วยุจากฝ่ายตรงข้าม และยังคงครองสติปฏิบัติตนต่อไปได้อย่างสงบ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(24, 4, 'ระดับที่ 4 คือ แสดงสมรรถนะระดับที่ 3 และมีพฤติกรรมตอบโต้ได้อย่างสร้างสรรค์ เพื่อแก้ไขสถานการณ์ที่ทำให้เกิดความรุนแรงทางอารมณ์
รู้สึกถึงความรุนแรงทางอารมณ์ในระหว่างการสนทนา หรือการปฏิบัติงาน แต่สามารถเลือกวิธีการแสดงออกในทางสร้างสรรค์เพื่อแก้ไขสถานการณ์ให้ดีขึ้น', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(24, 5, 'ระดับที่ 5 คือ แสดงสมรรถนะระดับที่ 4 และจัดการความเครียดอย่างมีประสิทธิภาพ
สามารถปฏิบัติงานหรือตอบสนองอย่างสร้างสรรค์ในสภาวะความกดดันต่อเนื่อง
สามารถจัดการกับความเครียดหรือผลกระทบที่อาจจะเกิดขึ้นจากความรุนแรงทางอารมณ์ได้อย่างมีประสิทธิภาพ
อาจประยุกต์ใช้วิธีการเฉพาะตน หรือวางแผนล่วงหน้าเพื่อจัดการกับอารมณ์และความเครียดที่อาจจะเกิดขึ้น', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(25, 0, 'ระดับที่ 0 คือ ไม่แสดงสมรรถนะด้านนี้อย่างชัดเจน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(25, 1, 'ระดับที่ 1 คือ รู้สึกว่าตนมีสิทธิอำนาจในงาน/เป็นเจ้าของงาน
รับผิดชอบต่อผลงานการกระทำของตนเอง 
นำเสนอทางแก้ปัญหา มิใช่นำเสนอแต่ปัญหา', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(25, 2, 'ระดับที่ 2 คือ แสดงสมรรถนะระดับที่ 1 และทำให้ผู้ใต้บังคับบัญชารู้สึกว่าตนมีศักยภาพ
เข้าใจข้อดีและข้อด้อยของผู้ใต้บังคับบัญชาและชี้แนะหนทางเพื่อสนับสนุนส่งเสริมข้อดีให้โดดเด่น
ให้โอกาสผู้ใต้บังคับบัญชาได้แสดงออกถึงศักยภาพด้านดีของตนเพื่อเสริมความมั่นใจในการปฏิบัติหน้าที่', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(25, 3, 'ระดับที่ 3 คือ แสดงสมรรถนะระดับที่ 2 และให้โอกาสผู้ใต้บังคับบัญชาในการแสดงความสามารถในการทำงาน
มอบหมายงานประจำ ตลอดจนทรัพยากรที่จำเป็น คำชี้แนะและการสนับสนุนต่างๆ เพื่อให้งานสำเร็จ
พร้อมจะยอมเสี่ยงบ้าง โดยยอมให้ผู้อื่นตัดสินใจในบางเรื่อง', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(25, 4, 'ระดับที่ 4 คือ แสดงสมรรถนะระดับที่ 3 และช่วยขจัดข้อจำกัดของผู้ใต้บังคับบัญชาเพื่อพัฒนาศักยภาพ
ช่วยปรับเปลี่ยนทัศนคติเดิมที่เป็นปัจจัยขัดขวางการพัฒนาศักยภาพของผู้ใต้บังคับบัญชา 
มีจิตวิทยาในการเข้าถึงจิตใจและเหตุผลเบื้องหลังพฤติกรรมของแต่ละบุคคล เพื่อนำมาสนับสนุนในการล้มล้างความเชื่อ และค่านิยมเชิงลบและพัฒนาศักยภาพในการทำงานให้ดีขึ้น', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(25, 5, 'ระดับที่ 5 คือ แสดงสมรรถนะระดับที่ 4 และเปิดโอกาสให้ผู้ใต้บังคับบัญชาได้ริเริ่มและตัดสินใจเอง
เปิดโอกาสให้ผู้ใตับังคับบัญชาได้ริเริ่มสิ่งใหม่ด้วยตนเองโดยการมอบหมายอำนาจตัดสินใจให้ 
สร้างความรู้สึกรับผิดชอบในงานให้แก่ผู้อื่นโดยการอยู่หลังฉาก ปล่อยให้ผู้อื่นแสดงฝีมือในงานสำคัญๆ', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(26, 0, 'ระดับที่ 0 คือ ไม่แสดงสมรรถนะด้านนี้อย่างชัดเจน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(26, 1, 'ระดับที่ 1', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(26, 2, 'ระดับที่ 2', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(26, 3, 'ระดับที่ 3', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(26, 4, 'ระดับที่ 4', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO COMPETENCY_LEVEL (JOB_DES_ID, JOB_DES_LEVEL, JOB_DES_LEVEL_DESCRIPTION, UPDATE_USER, UPDATE_DATE) 
						VALUES(26, 5, 'ระดับที่ 5', $SESS_USERID, '$UPDATE_DATE') ";
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
						VALUES(1, 1, 'NOT SPECIFY', 'ยังไม่มีการกำหนดตำแหน่งใหม่', '1', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO CONFIG_WORKFLOW (WORKFLOW_ID, LV_ID, LV_NAME, LV_DESCRIPTION, WORKFLOW_TIMES, UPDATE_USER, UPDATE_DATE) 
						VALUES(2, 2, 'O1', 'ประเภททั่วไป ระดับปฏิบัติงาน', '1', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO CONFIG_WORKFLOW (WORKFLOW_ID, LV_ID, LV_NAME, LV_DESCRIPTION, WORKFLOW_TIMES, UPDATE_USER, UPDATE_DATE) 
						VALUES(3, 3, 'O2', 'ประเภททั่วไป ระดับชำนาญงาน', '1', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO CONFIG_WORKFLOW (WORKFLOW_ID, LV_ID, LV_NAME, LV_DESCRIPTION, WORKFLOW_TIMES, UPDATE_USER, UPDATE_DATE) 
						VALUES(4, 4, 'O3', 'ประเภททั่วไป ระดับอาวุโส', '1', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO CONFIG_WORKFLOW (WORKFLOW_ID, LV_ID, LV_NAME, LV_DESCRIPTION, WORKFLOW_TIMES, UPDATE_USER, UPDATE_DATE) 
						VALUES(5, 5, 'O4', 'ประเภททั่วไป ระดับทักษะพิเศษ', '1', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO CONFIG_WORKFLOW (WORKFLOW_ID, LV_ID, LV_NAME, LV_DESCRIPTION, WORKFLOW_TIMES, UPDATE_USER, UPDATE_DATE) 
						VALUES(6, 6, 'K1', 'ประเภทวิชาการ ระดับปฏิบัติการ', '1', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO CONFIG_WORKFLOW (WORKFLOW_ID, LV_ID, LV_NAME, LV_DESCRIPTION, WORKFLOW_TIMES, UPDATE_USER, UPDATE_DATE) 
						VALUES(7, 7, 'K2', 'ประเภทวิชาการ ระดับชำนาญการ', '1', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO CONFIG_WORKFLOW (WORKFLOW_ID, LV_ID, LV_NAME, LV_DESCRIPTION, WORKFLOW_TIMES, UPDATE_USER, UPDATE_DATE) 
						VALUES(8, 8, 'K3', 'ประเภทวิชาการ ระดับชำนาญการพิเศษ', '1', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO CONFIG_WORKFLOW (WORKFLOW_ID, LV_ID, LV_NAME, LV_DESCRIPTION, WORKFLOW_TIMES, UPDATE_USER, UPDATE_DATE) 
						VALUES(9, 9, 'K4', 'ประเภทวิชาการ ระดับเชี่ยวชาญ', '1', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO CONFIG_WORKFLOW (WORKFLOW_ID, LV_ID, LV_NAME, LV_DESCRIPTION, WORKFLOW_TIMES, UPDATE_USER, UPDATE_DATE) 
						VALUES(10, 10, 'K5', 'ประเภทวิชาการ ระดับทรงคุณวุฒิ', '1', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO CONFIG_WORKFLOW (WORKFLOW_ID, LV_ID, LV_NAME, LV_DESCRIPTION, WORKFLOW_TIMES, UPDATE_USER, UPDATE_DATE) 
						VALUES(11, 11, 'D1', 'ประเภทอำนวยการ ระดับต้น', '1', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO CONFIG_WORKFLOW (WORKFLOW_ID, LV_ID, LV_NAME, LV_DESCRIPTION, WORKFLOW_TIMES, UPDATE_USER, UPDATE_DATE) 
						VALUES(12, 12, 'D2', 'ประเภทอำนวยการ ระดับสูง', '1', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO CONFIG_WORKFLOW (WORKFLOW_ID, LV_ID, LV_NAME, LV_DESCRIPTION, WORKFLOW_TIMES, UPDATE_USER, UPDATE_DATE) 
						VALUES(13, 13, 'M1', 'ประเภทบริหาร ระดับต้น', '1', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO CONFIG_WORKFLOW (WORKFLOW_ID, LV_ID, LV_NAME, LV_DESCRIPTION, WORKFLOW_TIMES, UPDATE_USER, UPDATE_DATE) 
						VALUES(14, 14, 'M2', 'ประเภทบริหาร ระดับสูง', '1', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO CONFIG_WORKFLOW (WORKFLOW_ID, LV_ID, LV_NAME, LV_DESCRIPTION, WORKFLOW_TIMES, UPDATE_USER, UPDATE_DATE) 
						VALUES(15, 15, 'O1/O2', 'ประเภททั่วไป ระดับปฏิบัติงาน/ประเภททั่วไป ระดับชำนาญงาน', '1', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO CONFIG_WORKFLOW (WORKFLOW_ID, LV_ID, LV_NAME, LV_DESCRIPTION, WORKFLOW_TIMES, UPDATE_USER, UPDATE_DATE) 
						VALUES(16, 16, 'O2/O3', 'ประเภททั่วไป ระดับชำนาญงาน/ประเภททั่วไป ระดับอาวุโส', '1', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO CONFIG_WORKFLOW (WORKFLOW_ID, LV_ID, LV_NAME, LV_DESCRIPTION, WORKFLOW_TIMES, UPDATE_USER, UPDATE_DATE) 
						VALUES(19, 17, 'K1/K2', 'ประเภทวิชาการ ระดับปฏิบัติการ/ประเภทวิชาการ ระดับชำนาญการ', '1', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO CONFIG_WORKFLOW (WORKFLOW_ID, LV_ID, LV_NAME, LV_DESCRIPTION, WORKFLOW_TIMES, UPDATE_USER, UPDATE_DATE) 
						VALUES(20, 18, 'K2/K3', 'ประเภทวิชาการ ระดับชำนาญการ/ประเภทวิชาการ ระดับชำนาญการพิเศษ', '1', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO CONFIG_WORKFLOW (WORKFLOW_ID, LV_ID, LV_NAME, LV_DESCRIPTION, WORKFLOW_TIMES, UPDATE_USER, UPDATE_DATE) 
						VALUES(21, 19, 'K3/K4', 'ประเภทวิชาการ ระดับชำนาญการพิเศษ/ประเภทวิชาการ ระดับเชี่ยวชาญ', '1', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO CONFIG_WORKFLOW (WORKFLOW_ID, LV_ID, LV_NAME, LV_DESCRIPTION, WORKFLOW_TIMES, UPDATE_USER, UPDATE_DATE) 
						VALUES(22, 20, 'K4/K5', 'ประเภทวิชาการ ระดับเชี่ยวชาญ/ประเภทวิชาการ ระดับทรงคุณวุฒิ', '1', $SESS_USERID, '$UPDATE_DATE') ";
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
						VALUES(1, 'ด้านปฏิบัติการ', '1', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO ACCOUNTABILITY_TYPE (ACC_TYPE_ID, ACC_TYPE_NAME, ACC_TYPE_ACTIVE, UPDATE_USER, UPDATE_DATE) 
						VALUES(2, 'ด้านบริการ', '1', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO ACCOUNTABILITY_TYPE (ACC_TYPE_ID, ACC_TYPE_NAME, ACC_TYPE_ACTIVE, UPDATE_USER, UPDATE_DATE) 
						VALUES(3, 'งานกำกับดูแล', '1', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO ACCOUNTABILITY_TYPE (ACC_TYPE_ID, ACC_TYPE_NAME, ACC_TYPE_ACTIVE, UPDATE_USER, UPDATE_DATE) 
						VALUES(4, 'ด้านปฏิบัติการ/งานเชี่ยวชาญเฉพาะด้าน', '1', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO ACCOUNTABILITY_TYPE (ACC_TYPE_ID, ACC_TYPE_NAME, ACC_TYPE_ACTIVE, UPDATE_USER, UPDATE_DATE) 
						VALUES(5, 'ด้านวางแผน', '1', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO ACCOUNTABILITY_TYPE (ACC_TYPE_ID, ACC_TYPE_NAME, ACC_TYPE_ACTIVE, UPDATE_USER, UPDATE_DATE) 
						VALUES(6, 'ด้านประสานงาน', '1', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO ACCOUNTABILITY_TYPE (ACC_TYPE_ID, ACC_TYPE_NAME, ACC_TYPE_ACTIVE, UPDATE_USER, UPDATE_DATE) 
						VALUES(7, 'ด้านการบริการ', '1', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO ACCOUNTABILITY_TYPE (ACC_TYPE_ID, ACC_TYPE_NAME, ACC_TYPE_ACTIVE, UPDATE_USER, UPDATE_DATE) 
						VALUES(8, 'ด้านบริหารและกำกับดูแล', '1', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO ACCOUNTABILITY_TYPE (ACC_TYPE_ID, ACC_TYPE_NAME, ACC_TYPE_ACTIVE, UPDATE_USER, UPDATE_DATE) 
						VALUES(9, 'ด้านการวางแผน', '1', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO ACCOUNTABILITY_TYPE (ACC_TYPE_ID, ACC_TYPE_NAME, ACC_TYPE_ACTIVE, UPDATE_USER, UPDATE_DATE) 
						VALUES(10, 'ด้านการประสานงาน', '1', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO ACCOUNTABILITY_TYPE (ACC_TYPE_ID, ACC_TYPE_NAME, ACC_TYPE_ACTIVE, UPDATE_USER, UPDATE_DATE) 
						VALUES(11, 'ด้านงานบริหารบุคคล', '1', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO ACCOUNTABILITY_TYPE (ACC_TYPE_ID, ACC_TYPE_NAME, ACC_TYPE_ACTIVE, UPDATE_USER, UPDATE_DATE) 
						VALUES(12, 'ด้านการจัดสรรทรัพยากรหรืองบประมาณ', '1', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO ACCOUNTABILITY_TYPE (ACC_TYPE_ID, ACC_TYPE_NAME, ACC_TYPE_ACTIVE, UPDATE_USER, UPDATE_DATE) 
						VALUES(13, 'ด้านวางแผนการทำงาน', '1', $SESS_USERID, '$UPDATE_DATE') ";
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
						VALUES(1, 'ด้านปฏิบัติการ', '1', $SESS_USERID, '$UPDATE_DATE') ";
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
						  VALUES (1, 'TH', 48, 7, 'การประเมินค่างาน', 'S', 'N', 0, $CREATE_DATE, $CREATE_BY, $CREATE_DATE, 
						  $CREATE_BY) ";
		$db->send_cmd($cmd);
		//$db->show_error();

		$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV0 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
						  FLAG_SHOW, 	TYPE_LINKTO, LINKTO_TID, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
						  VALUES (1, 'EN', 48, 7, 'การประเมินค่างาน', 'S', 'N', 0, $CREATE_DATE, $CREATE_BY, $CREATE_DATE, 
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
							  VALUES (1, 'TH', $MAX_ID, 1, 'J01 มาตรฐานกำหนดตำแหน่ง', 'S', 'W', 
							  'master_table_pos_des_info.html?table=POS_DES_INFO', 0, 48, $CREATE_DATE, $CREATE_BY, 
							  $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 1, 'J01 มาตรฐานกำหนดตำแหน่ง', 'S', 'W', 
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
							  VALUES (1, 'TH', $MAX_ID, 2, 'J02 ความรู้', 'S', 'W', 'master_table_knowledge_info.html?table=KNOWLEDGE_INFO', 0, 48, $CREATE_DATE, 
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 2, 'J02 ความรู้', 'S', 'W', 'master_table_knowledge_info.html?table=KNOWLEDGE_INFO', 0, 48, $CREATE_DATE, 
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
							  VALUES (1, 'TH', $MAX_ID, 3, 'J03 ทักษะ', 'S', 'W', 'master_table_skill_info.html?table=SKILL_INFO', 0, 48, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 3, 'J03 ทักษะ', 'S', 'W', 'master_table_skill_info.html?table=SKILL_INFO', 0, 48, 
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
							  VALUES (1, 'TH', $MAX_ID, 4, 'J04 ประสบการณ์', 'S', 'W', 'master_table_exp_info.html?table=EXP_INFO', 0, 48, $CREATE_DATE,
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 4, 'J04 ประสบการณ์', 'S', 'W', 'master_table_exp_info.html?table=EXP_INFO', 0, 48, $CREATE_DATE,
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
							  VALUES (1, 'TH', $MAX_ID, 5, 'J05 สมรรถนะ', 'S', 'W', 'master_table_competency_info.html?table=COMPETENCY_INFO', 0, 48, $CREATE_DATE,
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 5, 'J05 สมรรถนะ', 'S', 'W', 'master_table_competency_info.html?table=COMPETENCY_INFO', 0, 48, $CREATE_DATE,
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
							  VALUES (1, 'TH', $MAX_ID, 6, 'J06 ตั้งค่าการอนุมัติการประเมินค่างาน', 'S', 'W', 'set_config_workflow.html', 0, 48, $CREATE_DATE,
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 6, 'J06 ตั้งค่าการอนุมัติการประเมินค่างาน', 'S', 'W', 'set_config_workflow.html', 0, 48, $CREATE_DATE,
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
							  VALUES (1, 'TH', $MAX_ID, 7, 'J07 ตั้งค่าระยะเวลาการประเมินค่างานรายส่วนราชการ', 'S', 'W', 'set_config_job_evaluation_all.html', 0, 48, $CREATE_DATE,
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 7, 'J07 ตั้งค่าระยะเวลาการประเมินค่างานรายส่วนราชการ', 'S', 'W', 'set_config_job_evaluation_all.html', 0, 48, $CREATE_DATE,
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
							  VALUES (1, 'TH', $MAX_ID, 8, 'J08 ประเภท/ระดับตำแหน่ง (ใหม่)', 'S', 'W', 'master_table_new_level.html?table=PER_NEW_LEVEL', 0, 48, $CREATE_DATE,
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 8, 'J08 ประเภท/ระดับตำแหน่ง (ใหม่)', 'S', 'W', 'master_table_new_level.html?table=PER_NEW_LEVEL', 0, 48, $CREATE_DATE,
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
							  VALUES (1, 'TH', $MAX_ID, 9, 'J09 กลุ่มประเภท/ระดับตำแหน่ง (ใหม่)', 'S', 'W', 'master_table_accountability_level_type.html?table=ACCOUNTABILITY_LEVEL_TYPE', 0, 48, $CREATE_DATE,
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 9, 'J09 กลุ่มประเภท/ระดับตำแหน่ง (ใหม่)', 'S', 'W', 'master_table_accountability_level_type.html?table=ACCOUNTABILITY_LEVEL_TYPE', 0, 48, $CREATE_DATE,
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
							  VALUES (1, 'TH', $MAX_ID, 10, 'J10 ประเภทหน้าที่ความรับผิดชอบของตำแหน่ง', 'S', 'W', 'master_table_nolog.html?table=ACCOUNTABILITY_TYPE', 0, 48, $CREATE_DATE,
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 10, 'J10 ประเภทหน้าที่ความรับผิดชอบของตำแหน่ง', 'S', 'W', 'master_table_nolog.html?table=ACCOUNTABILITY_TYPE', 0, 48, $CREATE_DATE,
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE MENU_LABEL = 'M11 ประเมินค่างาน' ";
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
							  VALUES (1, 'TH', $MAX_ID1, 11, 'M11 ประเมินค่างาน', 'S', 'N', 0, 9, $CREATE_DATE, $CREATE_BY, 
							  $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, UPDATE_DATE, 
							  UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID1, 11, 'M11 ประเมินค่างาน', 'S', 'N', 0, 9, $CREATE_DATE, $CREATE_BY, 
							  $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT MENU_ID FROM BACKOFFICE_MENU_BAR_LV1 WHERE MENU_LABEL = 'M11 ประเมินค่างาน' ";
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
							  VALUES (1, 'TH', $MAX_ID, 2, 'M1102 คำถาม - คำตอบแบบประเมินค่างาน', 'S', 'W', 'master_table_jem_answer_info.html', 0, 9, $MENU_ID, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
							  CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 2, 'M1102 คำถาม - คำตอบแบบประเมินค่างาน', 'S', 'W', 'master_table_jem_answer_info.html', 0, 9, $MENU_ID, 
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
							  VALUES (1, 'TH', $MAX_ID, 10, 'M1110 หัวข้อคำถามแบบประเมินค่างาน', 'S', 'W', 'master_table_jem_question_info.html', 0, 9, $MENU_ID, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
							  CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 10, 'M1110 หัวข้อคำถามแบบประเมินค่างาน', 'S', 'W', 'master_table_jem_question_info.html', 0, 9, $MENU_ID, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE MENU_LABEL = 'R11 รายงานการประเมินค่างาน' ";
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
							  VALUES (1, 'TH', $MENU_ID, 11, 'R11 รายงานการประเมินค่างาน', 'S', 'N', 0, 36, $CREATE_DATE, $CREATE_BY, 
							  $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);	
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, UPDATE_DATE, 
							  UPDATE_BY)
							  VALUES (1, 'EN', $MENU_ID, 11, 'R11 รายงานการประเมินค่างาน', 'S', 'N', 0, 36, $CREATE_DATE, $CREATE_BY, 
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
								  VALUES (1, 'TH', $MAX_ID, 1, 'R1101 รายชื่อตำแหน่งของส่วนราชการ', 'S', 'W', 'rpt_position_list.html', 0, 36, $MENU_ID, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 1, 'R1101 รายชื่อตำแหน่งของส่วนราชการ', 'S', 'W', 'rpt_position_list.html', 0, 36, $MENU_ID, 
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
								  VALUES (1, 'TH', $MAX_ID, 2, 'R1102 จัดทำข้อมูลบทสรุปผู้บริหาร', 'S', 'W', 'data_file_summary.html', 0, 36, $MENU_ID, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 2, 'R1102 จัดทำข้อมูลบทสรุปผู้บริหาร', 'S', 'W', 'data_file_summary.html', 0, 36, $MENU_ID, 
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
								  VALUES (1, 'TH', $MAX_ID, 3, 'R1103 บทสรุปผู้บริหาร', 'S', 'W', 'rpt_executive_summary.html', 0, 36, $MENU_ID, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 3, 'R1103 บทสรุปผู้บริหาร', 'S', 'W', 'rpt_executive_summary.html', 0, 36, $MENU_ID, 
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
								  VALUES (1, 'TH', $MAX_ID, 4, 'R1104 สรุปผลการประเมินค่างาน', 'S', 'W', 'rpt_evaluation_conclusion.html', 0, 36, $MENU_ID, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 4, 'R1104 สรุปผลการประเมินค่างาน', 'S', 'W', 'rpt_evaluation_conclusion.html', 0, 36, $MENU_ID, 
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
								  VALUES (1, 'TH', $MAX_ID, 5, 'R1105 ข้อมูลจำนวนตำแหน่ง', 'S', 'W', 'rpt_position_count.html', 0, 36, $MENU_ID, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 5, 'R1105 ข้อมูลจำนวนตำแหน่ง', 'S', 'W', 'rpt_position_count.html', 0, 36, $MENU_ID, 
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
								  VALUES (1, 'TH', $MAX_ID, 6, 'R1106 วัดการใช้งานระบบประเมินค่างาน', 'S', 'W', 'rpt_evaluation_measurement.html', 0, 36, $MENU_ID, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 6, 'R1106 วัดการใช้งานระบบประเมินค่างาน', 'S', 'W', 'rpt_evaluation_measurement.html', 0, 36, $MENU_ID, 
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
							  VALUES (1, 'TH', $MAX_ID, 5, 'J05 สมรรถนะ', 'S', 'W', 'master_table_competency_info.html?table=COMPETENCY_INFO', 0, 48, $CREATE_DATE,
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 5, 'J05 สมรรถนะ', 'S', 'W', 'master_table_competency_info.html?table=COMPETENCY_INFO', 0, 48, $CREATE_DATE,
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
							  VALUES (1, 'TH', $MAX_ID, 5, 'J05 สมรรถนะ', 'S', 'W', 'master_table_competency_info.html?table=COMPETENCY_INFO', 0, 48, $CREATE_DATE,
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 5, 'J05 สมรรถนะ', 'S', 'W', 'master_table_competency_info.html?table=COMPETENCY_INFO', 0, 48, $CREATE_DATE,
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
							  VALUES (1, 'TH', $MAX_ID, 5, 'J05 สมรรถนะ', 'S', 'W', 'master_table_competency_info.html?table=COMPETENCY_INFO', 0, 48, $CREATE_DATE,
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 5, 'J05 สมรรถนะ', 'S', 'W', 'master_table_competency_info.html?table=COMPETENCY_INFO', 0, 48, $CREATE_DATE,
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
							  VALUES (1, 'TH', $MAX_ID, 5, 'J05 สมรรถนะ', 'S', 'W', 'master_table_competency_info.html?table=COMPETENCY_INFO', 0, 48, $CREATE_DATE,
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 5, 'J05 สมรรถนะ', 'S', 'W', 'master_table_competency_info.html?table=COMPETENCY_INFO', 0, 48, $CREATE_DATE,
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
							  VALUES (1, 'TH', $MAX_ID, 5, 'J05 สมรรถนะ', 'S', 'W', 'master_table_competency_info.html?table=COMPETENCY_INFO', 0, 48, $CREATE_DATE,
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 5, 'J05 สมรรถนะ', 'S', 'W', 'master_table_competency_info.html?table=COMPETENCY_INFO', 0, 48, $CREATE_DATE,
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
							  VALUES (1, 'TH', $MAX_ID, 5, 'J05 สมรรถนะ', 'S', 'W', 'master_table_competency_info.html?table=COMPETENCY_INFO', 0, 48, $CREATE_DATE,
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 5, 'J05 สมรรถนะ', 'S', 'W', 'master_table_competency_info.html?table=COMPETENCY_INFO', 0, 48, $CREATE_DATE,
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
							  VALUES (1, 'TH', $MAX_ID, 5, 'J05 สมรรถนะ', 'S', 'W', 'master_table_competency_info.html?table=COMPETENCY_INFO', 0, 48, $CREATE_DATE,
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 5, 'J05 สมรรถนะ', 'S', 'W', 'master_table_competency_info.html?table=COMPETENCY_INFO', 0, 48, $CREATE_DATE,
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
							  VALUES (1, 'TH', $MAX_ID, 5, 'J05 สมรรถนะ', 'S', 'W', 'master_table_competency_info.html?table=COMPETENCY_INFO', 0, 48, $CREATE_DATE,
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 5, 'J05 สมรรถนะ', 'S', 'W', 'master_table_competency_info.html?table=COMPETENCY_INFO', 0, 48, $CREATE_DATE,
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
							  VALUES (1, 'TH', $MAX_ID, 5, 'J05 สมรรถนะ', 'S', 'W', 'master_table_competency_info.html?table=COMPETENCY_INFO', 0, 48, $CREATE_DATE,
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 5, 'J05 สมรรถนะ', 'S', 'W', 'master_table_competency_info.html?table=COMPETENCY_INFO', 0, 48, $CREATE_DATE,
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
							  VALUES (1, 'TH', $MAX_ID, 5, 'J05 สมรรถนะ', 'S', 'W', 'master_table_competency_info.html?table=COMPETENCY_INFO', 0, 48, $CREATE_DATE,
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 5, 'J05 สมรรถนะ', 'S', 'W', 'master_table_competency_info.html?table=COMPETENCY_INFO', 0, 48, $CREATE_DATE,
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
							  VALUES (1, 'TH', $MAX_ID, 5, 'J05 สมรรถนะ', 'S', 'W', 'master_table_competency_info.html?table=COMPETENCY_INFO', 0, 48, $CREATE_DATE,
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 5, 'J05 สมรรถนะ', 'S', 'W', 'master_table_competency_info.html?table=COMPETENCY_INFO', 0, 48, $CREATE_DATE,
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		} */
?>