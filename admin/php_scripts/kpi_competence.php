<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");	

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

        
        
	switch($CTRL_TYPE){
		case 2 :
			$search_pv_code = $PROVINCE_CODE;
			$search_pv_name = $PROVINCE_NAME;
			break;
		case 3 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			break;
		case 4 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			break;
	} // end switch case

	switch($SESS_USERGROUP_LEVEL){
		case 2 :
			$search_pv_code = $PROVINCE_CODE;
			$search_pv_name = $PROVINCE_NAME;
			break;
		case 3 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			break;
		case 4 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			break;
		case 5 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			$search_org_id = $ORG_ID;
			$search_org_name = $ORG_NAME;
			break;
		case 6 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			$search_org_id = $ORG_ID;
			$search_org_name = $ORG_NAME;
			$search_org_id_1 = $ORG_ID_1;
			$search_org_name_1 = $ORG_NAME_1;
			break;
	} // end switch case

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
//		$$arr_fields[$i]=str_replace("'","''",$$arr_fields[$i]);
//		if($UPD){
//			$$arr_fields[$i]=str_replace("\"","&quot;",$$arr_fields[$i]);
//			}

	$UPDATE_DATE = date("Y-m-d H:i:s");
//echo $command;
	
	if ($command == "SETASSESSMENT") {
		$setflagassessment =  implode(",",$list_assessment_id);
		$cmd = " update PER_COMPETENCE set CP_ASSESSMENT = 'N' where CP_CODE in (".stripslashes($current_list).") and DEPARTMENT_ID = $DEPARTMENT_ID ";
		$db_dpis->send_cmd($cmd);
//echo $cmd;
		$cmd = " update PER_COMPETENCE set CP_ASSESSMENT = 'Y' where CP_CODE in (".stripslashes($setflagassessment).") and DEPARTMENT_ID = $DEPARTMENT_ID ";
		$db_dpis->send_cmd($cmd);
//echo $cmd;
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการประเมินสมรรถนะ");
	}
        if($command == "SET_DEF_WEIGHT"){
            foreach($DEF_WEIGHT as $CP_CODE => $DEF_WEIGHT_VAL){
                if(empty($DEF_WEIGHT_VAL)){
                    $DEF_WEIGHT_VAL='NULL';
                }
                $cmd = " update PER_COMPETENCE set DEF_WEIGHT=$DEF_WEIGHT_VAL where CP_CODE=$CP_CODE ";
                $db_dpis->send_cmd($cmd);
            }
            insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ค่าน้ำหนักตั้งต้น (ร้อยละ)");
        }
	
	if ($command == "SETFLAG") {
		$setflagshow =  implode(",",$list_show_id);
		$cmd = " update PER_COMPETENCE set CP_ACTIVE = 0 where CP_CODE in (".stripslashes($current_list).") and DEPARTMENT_ID = $DEPARTMENT_ID ";
		$db_dpis->send_cmd($cmd);
		$cmd = " update PER_COMPETENCE set CP_ACTIVE = 1 where CP_CODE in (".stripslashes($setflagshow).") and DEPARTMENT_ID = $DEPARTMENT_ID ";
		$db_dpis->send_cmd($cmd);
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการใช้งานข้อมูล");
	}
	
	if (!$CP_ENG_NAME) $CP_ENG_NAME = $CP_NAME;
	if($command == "ADD" && trim($CP_CODE)){
		$cmd = " select CP_CODE, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ACTIVE  
				  from PER_COMPETENCE where CP_CODE='$CP_CODE' and DEPARTMENT_ID = $DEPARTMENT_ID ";
		$count_duplicate = $db_dpis->send_cmd($cmd);
                //echo $cmd.'<br>';
		if($count_duplicate <= 0){
                    $DEF_WEIGHT_VAL=$txt_DEF_WEIGHT;
                    if(empty($txt_DEF_WEIGHT)){$DEF_WEIGHT_VAL='NULL';}
			$cmd = " insert into PER_COMPETENCE (CP_CODE, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, CP_ASSESSMENT, DEPARTMENT_ID,DEF_WEIGHT) 
							  values ('$CP_CODE', '$CP_NAME', '$CP_ENG_NAME', '$CP_MEANING', $CP_MODEL, $CP_ACTIVE, $SESS_USERID, 
							  '$UPDATE_DATE', '$CP_ASSESSMENT', $DEPARTMENT_ID,$DEF_WEIGHT_VAL) ";
			$db_dpis->send_cmd($cmd);
                        //echo $cmd;
//			$db_dpis->show_error();

			if ($CP_MODEL==1 || $CP_MODEL==3) {
				$level = array(	"O1", "O2", "O3", "O4", "K1", "K2", "K3", "K4", "K5", "D1", "D2", "M1", "M2" );
				$target = array(	1, 1, 2, 3, 1, 2, 3, 4, 5, 3, 4, 4, 5 );
				for ( $i=0; $i<count($level); $i++ ) { 
					if ($level[$i]=="O1" && substr($CP_CODE,0,1)=="3") $TARGET_LEVEL = 0; else $TARGET_LEVEL = $target[$i]; 
					$cmd = " INSERT INTO PER_STANDARD_COMPETENCE (LEVEL_NO, CP_CODE, TARGET_LEVEL, UPDATE_USER, 
									UPDATE_DATE)
									VALUES ('$level[$i]', '$CP_CODE', $TARGET_LEVEL,  $SESS_USERID, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				} // end for
			} // end if
			
			if ($CP_MODEL==2) {
				$level = array(	"D1", "D2", "M1", "M2" );
				$target = array(	1, 2, 3, 4 );
				for ( $i=0; $i<count($level); $i++ ) { 
					$cmd = " INSERT INTO PER_STANDARD_COMPETENCE (LEVEL_NO, CP_CODE, TARGET_LEVEL, UPDATE_USER, 
									UPDATE_DATE)
									VALUES ('$level[$i]', '$CP_CODE', $target[$i],  $SESS_USERID, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				} // end for
			} // end if
				insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > $menu_detail เพิ่มข้อมูล [$CP_CODE : $DEPARTMENT_ID : $CP_NAME : $CP_MEANING : $CP_MODEL]");
				$success_sql = "เพิ่มข้อมูล [$CP_CODE : $DEPARTMENT_ID : $CP_NAME : $CP_MEANING : $CP_MODEL]เรียบร้อยแล้ว"; //กำหนดในส่วนของ php
		}else{
			$data = $db_dpis->get_array();			
			$err_text = "รหัสข้อมูลซ้ำ [$data[CP_CODE] $data[CP_NAME] $data[CP_MEANING]";
		} // endif
	}

	if($command == "UPDATE" && trim($CP_CODE)){
            $DEF_WEIGHT_VAL=$txt_DEF_WEIGHT;
            if(empty($txt_DEF_WEIGHT)){$DEF_WEIGHT_VAL='NULL';}
		$cmd = " update PER_COMPETENCE set 
								CP_CODE='$CP_CODE', 
								CP_NAME='$CP_NAME', 
								CP_ENG_NAME='$CP_ENG_NAME', 
								CP_MEANING='$CP_MEANING', 
								CP_MODEL=$CP_MODEL, 
								CP_ACTIVE=$CP_ACTIVE, 
								CP_ASSESSMENT='$CP_ASSESSMENT', 
                                                                DEF_WEIGHT=$DEF_WEIGHT_VAL,
								UPDATE_USER=$SESS_USERID, 
								UPDATE_DATE='$UPDATE_DATE' 
							where CP_CODE='$CP_CODE' and DEPARTMENT_ID = $DEPARTMENT_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [$CP_CODE : $DEPARTMENT_ID : $CP_NAME : $CP_MEANING : $CP_MODEL]");
	}
	
	if($command == "DELETE" && trim($CP_CODE)){
		$cmd = " select CP_NAME from PER_COMPETENCE where CP_CODE='$CP_CODE' and DEPARTMENT_ID = $DEPARTMENT_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$CP_NAME = $data[CP_NAME];
		
		$cmd = " delete from PER_COMPETENCE where CP_CODE='$CP_CODE' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [$CP_CODE : $DEPARTMENT_ID : $CP_NAME : $CP_MEANING : $CP_MODEL]");
	}
	
	if($UPD){
		$cmd = " select CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ACTIVE, CP_ASSESSMENT, DEPARTMENT_ID, UPDATE_USER, UPDATE_DATE,DEF_WEIGHT 
				  from PER_COMPETENCE where CP_CODE='$CP_CODE' and DEPARTMENT_ID = $DEPARTMENT_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$CP_NAME = $data[CP_NAME];
		$CP_ENG_NAME = $data[CP_ENG_NAME];
		$CP_MEANING = $data[CP_MEANING];
		$CP_MODEL = $data[CP_MODEL];
		$CP_ACTIVE = $data[CP_ACTIVE];
		$CP_ASSESSMENT = $data[CP_ASSESSMENT];
                $DEF_WEIGHT = $data[DEF_WEIGHT];
                
		$DEPARTMENT_ID = $data[DEPARTMENT_ID];
		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);
		
		$cmd = " select ORG_ID_REF, ORG_NAME from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$DEPARTMENT_NAME = $data[ORG_NAME];
		$MINISTRY_ID = $data[ORG_ID_REF];

		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MINISTRY_NAME = $data[ORG_NAME];
//		for($i=0;$i<sizeof($arr_fields);$i++){
//			$$arr_fields[$i]=str_replace("\"","&quot;",$$arr_fields[$i]);
//			}
		} // end if
	
	if( $command=='GENDATA' ) {
		$cmd = " SELECT DEPARTMENT_ID FROM PER_COMPETENCE ";
		$count_data = $db_dpis->send_cmd($cmd);

		if (!$count_data) {
			$cmd = " DROP TABLE PER_COMPETENCE ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " CREATE TABLE PER_COMPETENCE(
				CP_CODE VARCHAR(3) NOT NULL,	
				DEPARTMENT_ID INTEGER,
				CP_NAME VARCHAR(100) NOT NULL,	
				CP_ENG_NAME VARCHAR(100) NOT NULL,	
				CP_MEANING MEMO NULL,	
				CP_MODEL SINGLE NOT NULL,
				CP_ASSESSMENT CHAR(1) NULL,
				CP_ACTIVE SINGLE NOT NULL,
				UPDATE_USER INTEGER2 NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_COMPETENCE PRIMARY KEY (CP_CODE, DEPARTMENT_ID)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE PER_COMPETENCE(
				CP_CODE VARCHAR2(3) NOT NULL,	
				DEPARTMENT_ID NUMBER(10),
				CP_NAME VARCHAR2(100) NOT NULL,	
				CP_ENG_NAME VARCHAR2(100) NOT NULL,	
				CP_MEANING VARCHAR2(1000) NULL,	
				CP_MODEL NUMBER(1) NOT NULL,
				CP_ASSESSMENT CHAR(1) NULL,
				CP_ACTIVE NUMBER(1) NOT NULL,
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				CONSTRAINT PK_PER_COMPETENCE PRIMARY KEY (CP_CODE, DEPARTMENT_ID)) ";
			elseif($DPISDB=="mysql")
				$cmd = " CREATE TABLE PER_COMPETENCE(
				CP_CODE VARCHAR(3) NOT NULL,	
				DEPARTMENT_ID INTEGER(10),
				CP_NAME VARCHAR(100) NOT NULL,	
				CP_ENG_NAME VARCHAR(100) NOT NULL,	
				CP_MEANING TEXT NULL,	
				CP_MODEL SMALLINT(1) NOT NULL,
				CP_ACTIVE SMALLINT(1) NOT NULL,
				CP_ASSESSMENT CHAR(1) NULL,
				UPDATE_USER SMALLINT(5) NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_COMPETENCE PRIMARY KEY (CP_CODE, DEPARTMENT_ID)) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		}

		if ($DEPARTMENT_ID) $arr_dept[] = $DEPARTMENT_ID;
		elseif($CTRL_TYPE==1 || $CTRL_TYPE==2 || $CTRL_TYPE==3 || $BKK_FLAG==1){
//			$cmd = " SELECT DISTINCT DEPARTMENT_ID FROM PER_POS_EMPSER WHERE DEPARTMENT_ID > 1 AND POEM_STATUS = 1 ";
			$cmd = " SELECT DISTINCT a.DEPARTMENT_ID FROM PER_POSITION a, PER_ORG b 
							WHERE a.DEPARTMENT_ID = b.ORG_ID and a.DEPARTMENT_ID > 1 and b.OL_CODE = '02' and POS_STATUS = 1 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			while($data = $db_dpis->get_array()) $arr_dept[] = $data[DEPARTMENT_ID];
		}else $arr_dept[] = $DEPARTMENT_ID;

		if ($DEPARTMENT_ID)
			$cmd = " DELETE FROM PER_COMPETENCE_LEVEL WHERE DEPARTMENT_ID = $DEPARTMENT_ID ";
		else
			$cmd = " DELETE FROM PER_COMPETENCE_LEVEL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if ($DEPARTMENT_ID)
			$cmd = " DELETE FROM PER_COMPETENCE WHERE DEPARTMENT_ID = $DEPARTMENT_ID ";
		else
			$cmd = " DELETE FROM PER_COMPETENCE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if ($DEPARTMENT_ID)
			$cmd = " DELETE FROM PER_STANDARD_COMPETENCE WHERE DEPARTMENT_ID = $DEPARTMENT_ID ";
		else
			$cmd = " DELETE FROM PER_STANDARD_COMPETENCE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if ($DEPARTMENT_ID)
			$cmd = " DELETE FROM PER_TYPE_COMPETENCE WHERE DEPARTMENT_ID = $DEPARTMENT_ID ";
		else
			$cmd = " DELETE FROM PER_TYPE_COMPETENCE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		for ( $j=0; $j<count($arr_dept); $j++ ) { 
			if($BKK_FLAG==1){
				$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, DEPARTMENT_ID, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE)
								  VALUES ('101', $arr_dept[$j], 'คุณธรรมจริยธรรม', 'Integrity', 'การครองตนและประพฤติปฏิบัติตนถูกต้องตามหลักคุณธรรมจริยธรรม  มีความสำนึกและรับผิดชอบต่อตนเอง ตำแหน่งหน้าที่  ตลอดจนวิชาชีพของตนเพื่อธำรงรักษาศักดิ์ศรีแห่งอาชีพข้าราชการ', 1, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, DEPARTMENT_ID, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE)
								  VALUES ('102', $arr_dept[$j], 'การบริการที่ดี', 'Service Mind', 'พฤติกรรมที่แสดงถึงความตั้งใจ ความพยายาม ความพร้อมในการให้บริการประชาชน และอุทิศเวลาเพื่อตอบสนองความต้องการของผู้รับบริการ', 1, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, DEPARTMENT_ID, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE)
								  VALUES ('103', $arr_dept[$j], 'การมุ่งผลสัมฤทธิ์', 'Achievement Motivation', 'ความมุ่งมั่นใน การปฏิบัติราชการให้เกิดผลสัมฤทธิ์ตามมาตรฐานและคุณภาพงานที่กำหนด  และ/หรือให้เกินจากมาตรฐานที่กำหนด', 1, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, DEPARTMENT_ID, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE)
								  VALUES ('104', $arr_dept[$j], 'การทำงานเป็นทีม', 'Teamwork', 'การทำงานโดยมีพฤติกรรมที่สามารถทำงานร่วมกันกับผู้อื่นได้  สามารถรับฟังความคิดเห็นของสมาชิกในทีม  เต็มใจเรียนรู้จากผู้อื่น  และมีความสามารถในการสร้างและรักษาสัมพันธภาพกับสมาชิก', 1, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, DEPARTMENT_ID, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE)
								  VALUES ('105', $arr_dept[$j], 'การสั่งสมความเชี่ยวชาญในงานอาชีพ', 'Expertise', 'ความขวนขวาย  สนใจใฝ่รู้เพื่อพัฒนาศักยภาพ  ความรู้ความสามารถของตนในการปฏิบัติงานราชการ  ด้วยการศึกษา  ค้นคว้าหาความรู้  พัฒนาตนเองอย่างต่อเนื่อง  อีกทั้งรู้จักพัฒนา ปรับปรุงประยุกต์ใช้ความรู้เชิงวิชาการและเทคโนโลยีต่าง ๆ เข้ากับการปฏิบัติงานให้เกิดผลสัมฤทธิ์', 1, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			} else {
				$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, DEPARTMENT_ID, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE)
								  VALUES ('101', $arr_dept[$j], 'การมุ่งผลสัมฤทธิ์', 'Achievement Motivation', 'ความมุ่งมั่นจะปฏิบัติหน้าที่ราชการให้ดีหรือให้เกินมาตรฐานที่มีอยู่ โดยมาตรฐานนี้อาจเป็นผลการปฏิบัติงานที่ผ่านมาของตนเอง หรือเกณฑ์วัดผลสัมฤทธิ์ที่ส่วนราชการกำหนดขึ้น อีกทั้งยังหมายรวมถึงการสร้างสรรค์พัฒนาผลงานหรือกระบวนการปฏิบัติงานตามเป้าหมายที่ยากและท้าทายชนิดที่อาจไม่เคยมีผู้ใดสามารถกระทำได้มาก่อน', 1, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, DEPARTMENT_ID, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE)
								  VALUES ('102', $arr_dept[$j], 'บริการที่ดี', 'Service Mind', 'ความตั้งใจและความพยายามของข้าราชการในการให้บริการต่อประชาชน ข้าราชการ หรือหน่วยงานอื่นๆ ที่เกี่ยวข้อง', 1, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, DEPARTMENT_ID, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE)
								  VALUES ('103', $arr_dept[$j], 'การสั่งสมความเชี่ยวชาญในงานอาชีพ', 'Expertise', 'ความสนใจใฝ่รู้ สั่งสม ความรู้ความสามารถของตนในการปฏิบัติหน้าที่ราชการ ด้วยการศึกษา ค้นคว้า และพัฒนาตนเองอย่างต่อเนื่อง จนสามารถประยุกต์ใช้ความรู้เชิงวิชาการและเทคโนโลยีต่างๆ เข้ากับการปฏิบัติราชการให้เกิดผลสัมฤทธิ์', 1, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, DEPARTMENT_ID, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE)
								  VALUES ('104', $arr_dept[$j], 'การยึดมั่นในความถูกต้องชอบธรรม และจริยธรรม', 'Integrity', 'การดำรงตนและประพฤติปฏิบัติอย่างถูกต้องเหมาะสมทั้งตามกฎหมาย คุณธรรม จรรยาบรรณแห่งวิชาชีพ และจรรยาข้าราชการเพื่อรักษาศักดิ์ศรีแห่งความเป็นข้าราชการ', 1, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, DEPARTMENT_ID, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE)
								  VALUES ('105', $arr_dept[$j], 'การทำงานเป็นทีม', 'Teamwork', 'ความตั้งใจที่จะทำงานร่วมกับผู้อื่น เป็นส่วนหนึ่งของทีม หน่วยงาน หรือส่วนราชการ โดยผู้ปฏิบัติมีฐานะเป็นสมาชิก ไม่จำเป็นต้องมีฐานะหัวหน้าทีม รวมทั้งความสามารถในการสร้างและรักษาสัมพันธภาพกับสมาชิกในทีม', 1, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, DEPARTMENT_ID, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE)
								  VALUES ('201', $arr_dept[$j], 'สภาวะผู้นำ', 'Leadership', 'ความสามารถ หรือความตั้งใจที่จะรับบทในการเป็นผู้นำของกลุ่ม กำหนดทิศทาง เป้าหมาย วิธีการทำงาน ให้ทีมปฏิบัติงานได้อย่างราบรื่น เต็มประสิทธิภาพและบรรลุวัตถุประสงค์ของส่วนราชการ', 2, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, DEPARTMENT_ID, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE)
								  VALUES ('202', $arr_dept[$j], 'วิสัยทัศน์', 'Visioning', 'ความสามารถในการกำหนดทิศทาง ภารกิจ และเป้าหมายการทำงานที่ชัดเจน และความสามารถในการสร้างความร่วมแรงร่วมใจเพื่อให้ภารกิจบรรลุวัตถุประสงค์', 2, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, DEPARTMENT_ID, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE)
								  VALUES ('203', $arr_dept[$j], 'การวางกลยุทธ์ภาครัฐ', 'Strategic Orientation', 'ความเข้าใจวิสัยทัศน์และนโยบายภาครัฐและสามารถนำมาประยุกต์ใช้ในการกำหนดกลยุทธ์ของส่วนราชการได้', 2, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, DEPARTMENT_ID, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE)
								  VALUES ('204', $arr_dept[$j], 'ศักยภาพเพื่อนำการปรับเปลี่ยน', 'Change Leadership', 'ความสามารถในการกระตุ้น หรือผลักดันหน่วยงานไปสู่การปรับเปลี่ยนที่เป็นประโยชน์ รวมถึงการสื่อสารให้ผู้อื่นรับรู้ เข้าใจ และดำเนินการให้การปรับเปลี่ยนนั้นเกิดขึ้นจริง', 2, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, DEPARTMENT_ID, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE)
								  VALUES ('205', $arr_dept[$j], 'การควบคุมตนเอง', 'Self Control', 'ความสามารถในการควบคุมอารมณ์และพฤติกรรมในสถานการณ์ที่อาจจะถูกยั่วยุ หรือเผชิญหน้ากับความไม่เป็นมิตร หรือต้องทำงานภายใต้สภาวะกดดัน รวมถึงความอดทนอดกลั้นเมื่ออยู่ในสถานการณ์ที่ก่อความเครียดอย่างต่อเนื่อง', 2, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, DEPARTMENT_ID, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE)
								  VALUES ('206', $arr_dept[$j], 'การสอนงานและการมอบหมายงาน', 'Coaching and Empowering Others', 'ความตั้งใจที่จะส่งเสริมการเรียนรู้หรือการพัฒนาผู้อื่นในระยะยาวจนถึงระดับที่เชื่อมั่นว่าจะ สามารถมอบหมายหน้าที่ความรับผิดชอบให้ผู้นั้นมีอิสระที่จะตัดสินใจในการปฏิบัติหน้าที่ราชการของตนได้', 2, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, DEPARTMENT_ID, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE)
								  VALUES ('301', $arr_dept[$j], 'การคิดวิเคราะห์', 'Analytical Thinking', 'การทำความเข้าใจและวิเคราะห์สถานการณ์ ประเด็นปัญหา แนวคิดโดยการแยกแยะประเด็นออกเป็นส่วนย่อยๆ หรือทีละขั้นตอน รวมถึงการจัดหมวดหมู่อย่างเป็นระบบระเบียบ เปรียบเทียบแง่มุมต่างๆ สามารถลำดับความสำคัญ ช่วงเวลา เหตุและผล ที่มาที่ไปของกรณีต่างๆได้', 3, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, DEPARTMENT_ID, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE)
								  VALUES ('302', $arr_dept[$j], 'การมองภาพองค์รวม', 'Conceptual Thinking', 'การคิดในเชิงสังเคราะห์ มองภาพองค์รวม โดยการจับประเด็น สรุปรูปแบบ เชื่อมโยงหรือประยุกต์แนวทางจากสถานการณ์ ข้อมูล หรือทัศนะต่าง ๆ จนได้เป็นกรอบความคิดหรือแนวคิดใหม่', 3, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, DEPARTMENT_ID, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE)
								  VALUES ('303', $arr_dept[$j], 'การใส่ใจและพัฒนาผู้อื่น', 'Caring Others', 'ความใส่ใจและตั้งใจที่จะส่งเสริม ปรับปรุงและพัฒนาให้ผู้อื่นมีศักยภาพ หรือมีสุขภาวะทั้งทางปัญญา ร่างกาย จิตใจ และทัศนคติที่ดีอย่างยั่งยืนเกินกว่ากรอบของการปฏิบัติหน้าที่', 3, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, DEPARTMENT_ID, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE)
								  VALUES ('304', $arr_dept[$j], 'การสั่งการตามอำนาจหน้าที่', 'Holding People Accountable', 'การกำกับดูแลให้ผู้อื่นปฏิบัติตามมาตรฐาน กฎ ระเบียบ ข้อบังคับ โดยอาศัยอำนาจตามกฎหมาย หรือตามตำแหน่งหน้าที่ การกำกับดูแลนี้ หมายรวมถึงการออกคำสั่งโดยปกติทั่วไปจนถึงการใช้อำนาจตามกฎหมายกับผู้ฝ่าฝืน', 3, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, DEPARTMENT_ID, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE)
								  VALUES ('305', $arr_dept[$j], 'การสืบเสาะหาข้อมูล', 'Information Seeking', 'ความใฝ่รู้เชิงลึกที่จะแสวงหาข้อมูลเกี่ยวกับสถานการณ์ ภูมิหลัง ประวัติความเป็นมา ประเด็นปัญหา หรือเรื่องราวต่างๆ ที่เกี่ยวข้องหรือจะเป็นประโยชน์ในการปฏิบัติงาน', 3, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, DEPARTMENT_ID, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE)
								  VALUES ('306', $arr_dept[$j], 'ความเข้าใจข้อแตกต่างทางวัฒนธรรม', 'Cultural Sensitivity', 'การรับรู้ถึงข้อแตกต่างทางวัฒนธรรม และสามารถประยุกต์ความเข้าใจ เพื่อสร้างสัมพันธภาพระหว่างกันได้', 3, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, DEPARTMENT_ID, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE)
								  VALUES ('307', $arr_dept[$j], 'ความเข้าใจผู้อื่น', 'Interpersonal Understanding', 'ความสามารถในการรับฟังและเข้าใจความหมายตรง ความหมายแฝง ความคิด ตลอดจนสภาวะทางอารมณ์ของผู้ที่ติดต่อด้วย', 3, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, DEPARTMENT_ID, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE)
								  VALUES ('308', $arr_dept[$j], 'ความเข้าใจองค์กรและระบบราชการ', 'Organizational Awareness', 'ความสามารถในการเข้าใจความสัมพันธ์เชิงอำนาจตามกฎหมาย และอำนาจที่ไม่เป็นทางการ ในองค์กรของตนและองค์กรอื่นที่เกี่ยวข้องเพื่อประโยชน์ในการปฏิบัติหน้าที่ให้บรรลุเป้าหมาย รวมทั้งความสามารถที่จะคาดการณ์ได้ว่านโยบายภาครัฐ แนวโน้มทางการเมือง เศรษฐกิจ สังคม เทคโนโลยี ตลอดจนเหตุการณ์ ที่จะเกิดขึ้น จะมีผลต่อองค์กรอย่างไร', 3, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, DEPARTMENT_ID, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE)
								  VALUES ('309', $arr_dept[$j], 'การดำเนินการเชิงรุก', 'Proactiveness', 'การเล็งเห็นปัญหาหรือโอกาสพร้อมทั้งจัดการเชิงรุกกับปัญหานั้นโดยอาจ ไม่มีใครร้องขอ และอย่างไม่ย่อท้อ หรือใช้โอกาสนั้นให้เกิดประโยชน์ต่องาน ตลอดจนการคิดริเริ่มสร้างสรรค์ใหม่ๆ เกี่ยวกับงานด้วย เพื่อแก้ปัญหา ป้องกันปัญหา หรือสร้างโอกาสด้วย', 3, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, DEPARTMENT_ID, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE)
								  VALUES ('310', $arr_dept[$j], 'การตรวจสอบความถูกต้องตามกระบวนงาน', 'Concern for Order', 'ความใส่ใจที่จะปฏิบัติงานให้ถูกต้อง ครบถ้วน มุ่งเน้นความชัดเจนของบทบาท หน้าที่ และลดข้อบกพร่องที่อาจเกิดจากสภาพแวดล้อม โดยติดตาม ตรวจสอบการทำงานหรือข้อมูล ตลอดจนพัฒนาระบบการตรวจสอบเพื่อความถูกต้องของกระบวนงาน', 3, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, DEPARTMENT_ID, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE)
								  VALUES ('311', $arr_dept[$j], 'ความมั่นใจในตนเอง', 'Self Confidence', 'ความมั่นใจในความสามารถ ศักยภาพ และการตัดสินใจของตนที่จะปฏิบัติงานให้บรรลุผล หรือเลือกวิธีที่มีประสิทธิภาพในการปฏิบัติงาน หรือแก้ไขปัญหาให้สำเร็จลุล่วง', 3, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, DEPARTMENT_ID, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE)
								  VALUES ('312', $arr_dept[$j], 'ความยืดหยุ่นผ่อนปรน', 'Flexibility', 'ความสามารถในการปรับตัว และปฏิบัติงานได้อย่างมีประสิทธิภาพในสถานการณ์และกลุ่มคนที่หลากหลาย หมายความรวมถึงการยอมรับความเห็นที่แตกต่าง และปรับเปลี่ยนวิธีการเมื่อสถานการณ์เปลี่ยนไป', 3, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, DEPARTMENT_ID, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE)
								  VALUES ('313', $arr_dept[$j], 'ศิลปะการสื่อสารจูงใจ', 'Communication & Influencing', 'ความสามารถที่จะสื่อความด้วยการเขียน พูด โดยใช้สื่อต่างๆ เพื่อให้ผู้อื่นเข้าใจ ยอมรับ และสนับสนุนความคิดของตน', 3, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, DEPARTMENT_ID, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE)
								  VALUES ('314', $arr_dept[$j], 'สุนทรียภาพทางศิลปะ', 'Aesthetic Quality', 'ความซาบซึ้งในอรรถรสและเห็นคุณค่าของงานศิลปะที่เป็นเอกลักษณ์และมรดกของชาติ รวมถึงงานศิลปะอื่น ๆ และนำมาประยุกต์ในการสร้างสรรค์งานศิลปะของตนได้', 3, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, DEPARTMENT_ID, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE)
								  VALUES ('315', $arr_dept[$j], 'ความผูกพันที่มีต่อส่วนราชการ', 'Organizational Commitment', 'จิตสำนึกหรือความตั้งใจที่จะแสดงออกซึ่งพฤติกรรมที่สอดคล้องกับความต้องการ และเป้าหมายของส่วนราชการ ยึดถือประโยชน์ของส่วนราชการเป็นที่ตั้งก่อนประโยชน์ส่วนตัว', 3, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, DEPARTMENT_ID, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE)
								  VALUES ('316', $arr_dept[$j], 'การสร้างสัมพันธภาพ', 'Relationship Building', 'สร้างหรือรักษาสัมพันธภาพฉันมิตร เพื่อความสัมพันธ์ที่ดีระหว่างผู้เกี่ยวข้องกับงาน', 3, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			}

			if($BKK_FLAG==1)
				$code = array(	"101", "102", "103", "104", "105" );
			else
				$code = array(	"101", "102", "103", "104", "105", "201", "202", "203", "204", "205", "206", "301", "302", "303", "304", "305", "306", 
												"307", "308", "309", "310", "311", "312", "313", "314", "315", "316" );
			for ( $i=0; $i<count($code); $i++ ) { 
				if($BKK_FLAG==1)
					$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
									  UPDATE_DATE)
									  VALUES ('$code[$i]', $arr_dept[$j], 0, 'ไม่แสดงสมรรถนะด้านนี้อย่างชัดเจน', NULL, $SESS_USERID, '$UPDATE_DATE') ";
				else
					$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
									  UPDATE_DATE)
									  VALUES ('$code[$i]', $arr_dept[$j], 0, 'ไม่แสดงสมรรถนะด้านนี้ หรือแสดงอย่างไม่ชัดเจน', NULL, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			} // end for

			if($BKK_FLAG==1) {
				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('101', $arr_dept[$j], 1, 'ซื่อสัตย์สุจริต', 'ปฏิบัติหน้าที่ด้วยความโปร่งใส ซื่อสัตย์สุจริต ถูกต้องทั้งตามหลักกฎหมาย จริยธรรมและระเบียบวินัย
แสดงความคิดเห็นของตนตามหลักวิชาชีพอย่างเปิดเผยตรงไปตรงมา', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('101', $arr_dept[$j], 2, 'มีสัจจะเชื่อถือได้', 'รักษาวาจา มีสัจจะเชื่อถือได้ พูดอย่างไรทำอย่างนั้น ไม่บิดเบือนอ้างข้อยกเว้นให้ตนเอง
มีจิตสำนึกและความภาคภูมิใจในความเป็นข้าราชการ อุทิศแรงกายแรงใจผลักดันให้ภารกิจหลักของตนและหน่วยงานบรรลุผลเพื่อสนับสนุนส่งเสริมการพัฒนาประเทศชาติและสังคมไทย', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('101', $arr_dept[$j], 3, 'การครองตนโดยมีความรับผิดชอบต่อตนเองและต่อตำแหน่งหน้าที่', 'ยึดมั่นในหลักการและจรรยาบรรณของวิชาชีพ  ไม่เบี่ยงเบนด้วยอคติหรือผลประโยชน์ส่วนตน
เสียสละความสุขสบายตลอดจนความพึงพอใจส่วนตนหรือของครอบครัว  โดยมุ่งให้ภารกิจในหน้าที่สัมฤทธิ์ผลเป็นสำคัญ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('101', $arr_dept[$j], 4, 'ธำรงความถูกต้อง', 'ธำรงความถูกต้อง ยืนหยัดพิทักษ์ผลประโยชน์และชื่อเสียงของหน่วยงานแม้ในสถานการณ์ที่อาจสร้างความลำบากใจให้
ตัดสินใจในหน้าที่ ปฏิบัติราชการด้วยความถูกต้อง โปร่งใส เป็นธรรมแม้ผลของการปฏิบัติอาจสร้างศัตรูหรือก่อความไม่พึงพอใจให้แก่ผู้ที่เกี่ยวข้องหรือเสียประโยชน์', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('101', $arr_dept[$j], 5, 'อุทิศตนเพื่อผดุงความยุติธรรม', 'ธำรงความถูกต้อง ยืนหยัดพิทักษ์ผลประโยชน์และชื่อเสียงของหน่วยงาน ระบบราชการหรือประเทศชาติ แม้ในสถานการณ์ที่อาจเสี่ยงต่อความมั่นคงในตำแหน่งหน้าที่การงาน หรืออาจมีความเสี่ยงที่เป็นภัยต่อชีวิต', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('102', $arr_dept[$j], 1, 'ให้บริการที่เป็นมิตร', 'ให้คำแนะนำและคอยติดตามเรื่องให้เมื่อผู้รับบริการมีคำถาม  ข้อเรียกร้อง  หรือข้อร้องเรียนต่าง ๆที่เกี่ยวกับภารกิจของหน่วยงาน
ให้บริการด้วยอัธยาศัยไมตรีอันดี  และสร้างความประทับใจแก่ผู้รับบริการ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('102', $arr_dept[$j], 2, 'สื่อสารข้อมูลได้ชัดเจน', 'สื่อสารข้อมูล  ข่าวสาร  ความรู้  ของการบริการที่ชัดเจนกับผู้รับบริการได้ตลอดการให้บริการ
แจ้งให้ผู้รับบริการทราบความคืบหน้าในการดำเนินเรื่อง  หรือขั้นตอนงานต่าง ๆ ที่ให้บริการอยู่
ประสานงานภายในหน่วยงานและกับหน่วยงานที่เกี่ยวข้องเพื่อให้ผู้รับบริการได้รับบริการที่ต่อเนื่องและรวดเร็ว', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('102', $arr_dept[$j], 3, 'เต็มใจช่วยเหลือ', 'รับเป็นธุระ  แก้ปัญหาหรือหาแนวทางแก้ไขปัญหาที่เกิดขึ้นแก่ผู้รับบริการอย่างรวดเร็ว เต็มใจ ไม่บ่ายเบี่ยง  ไม่แก้ตัว  หรือปัดภาระ
คอยดูแลให้ผู้รับบริการได้รับความพึงพอใจ  และนำข้อขัดข้องใด ๆ ที่เกิดขึ้น (ถ้ามี) ไปพัฒนาการให้บริการให้ดียิ่งขึ้น', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('102', $arr_dept[$j], 4, 'เอื้อเฟื้อแสดงน้ำใจ', 'อุทิศเวลาให้แก่ผู้รับบริการ  โดยเฉพาะเมื่อผู้รับบริการประสบความยากลำบาก  เช่น  ให้เวลาและความพยายามเป็นกรณีพิเศษในการให้บริการเพื่อช่วยแก้ปัญหาให้แก่ผู้รับบริการ
คอยให้ข้อมูล  ข่าวสาร  ความรู้เกี่ยวข้องกับงานที่กำลังให้บริการอยู่  ซึ่งเป็นประโยชน์แก่ผู้รับบริการแม้ว่าผู้รับบริการจะไม่ได้ถามถึงหรือไม่ทราบมาก่อน
ให้บริการที่เกินความคาดหวังในระดับทั่วไป', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('102', $arr_dept[$j], 5, 'เข้าใจความต้องการที่แท้จริงของผู้รับบริการ', 'เข้าใจความจำเป็นหรือความต้องการที่แท้จริงของผู้รับบริการ  และ/หรือใช้เวลาแสวงหาข้อมูลและทำความเข้าใจเกี่ยวกับความจำเป็นหรือความต้องการที่แท้จริงของผู้รับบริการ
ให้คำแนะนำที่เป็นประโยชน์แก่ผู้รับบริการเพื่อตอบสนองความจำเป็นหรือความต้องการที่แท้จริงของผู้รับบริการ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('102', $arr_dept[$j], 6, 'ร่วมวางแผนเป็นที่ปรึกษาที่ผู้รับบริการวางใจ', 'เล็งเห็นผลประโยชน์ที่จะเกิดขึ้นกับผู้รับบริการในระยะยาว  และสามารถเปลี่ยนแปลงวิธีการหรือขั้นตอนการให้บริการ  เพื่อให้ผู้รับบริการได้ประโยชน์สูงสุด
ปฏิบัติตนเป็นที่ปรึกษาที่ผู้รับบริการไว้วางใจ  ตลอดจนมีส่วนช่วยในการตัดสินใจของผู้รับบริการ
สามารถให้ความเห็นส่วนตัวที่อาจแตกต่างไปจากวิธีการหรือขั้นตอนที่ผู้รับบริการต้องการ เพื่อให้สอดคล้องกับความจำเป็น  ปัญหา  โอกาส ฯลฯ เพื่อเป็นประโยชน์อย่างแท้จริงหรือในระยะยาวแก่ผู้รับบริการ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('103', $arr_dept[$j], 1, 'แสดงความมุ่งมั่นและกระตือรือร้นที่จะปฏิบัติราชการให้ดี', 'พยายามปฏิบัติราชการตามหน้าที่ให้ดีและถูกต้อง
มีความมานะอดทน ขยันหมั่นเพียรและตรงต่อเวลา
มีความรับผิดชอบในงาน และสามารถส่งงานได้ตามกำหนดเวลาอย่างถูกต้อง
แสดงออกว่าต้องการปฏิบัติงานให้ได้ดีขึ้น หรือแสดงความเห็นในเชิงปรับปรุงพัฒนาเมื่อประสบพบเห็นเหตุที่ก่อให้เกิดการสูญเปล่าหรือหย่อนประสิทธิภาพในงาน', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('103', $arr_dept[$j], 2, 'สามารถกำหนดมาตรฐานหรือเป้าหมายในการปฏิบัติงานของตนเองเพื่อให้ได้ผลสัมฤทธิ์ที่ดีเยี่ยม', 'หมั่นติดตามและวัดผลการปฏิบัติงานของตนโดยใช้เกณฑ์ที่ตนกำหนดขึ้นเอง โดยไม่ได้ถูกผู้อื่นบังคับ
กำหนดเป้าหมายหรือขั้นตอนในการทำงานของตนให้สามารถบรรลุเป้าหมายที่ผู้บังคับบัญชากำหนดหรือเป้าหมายของหน่วยงาน/กรม/กองที่รับผิดชอบ
มีความละเอียดรอบคอบเอาใจใส่ตรวจตราความถูกต้องของงานหรือข้อมูลที่รับผิดชอบเพื่อให้ได้ข้อมูลที่มีคุณภาพ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('103', $arr_dept[$j], 3, 'ปรับปรุงและพัฒนาการปฏิบัติงานให้มีประสิทธิภาพขึ้น', 'เปลี่ยนแปลงและพัฒนาตนเองซึ่งอาจรวมถึงการทำงานได้ดีขึ้น เร็วขึ้น มีประสิทธิภาพมากขึ้น หรือมีการเพิ่มคุณภาพของงานที่ทำ
เสนอหรือทดลองวิธีการหรือขั้นตอนทำงานแบบใหม่ที่แน่นอนเที่ยงตรงกว่าหรือมีประสิทธิภาพมากกว่าเพื่อให้บรรลุเป้าหมายที่ผู้บังคับบัญชากำหนดหรือเป้าหมายของหน่วยงาน/กรม/กองที่รับผิดชอบ
พัฒนาหรือปรับเปลี่ยนระบบหรือวิธีการทำงานของหน่วยงานเพี่อให้ได้ผลสัมฤทธิ์ที่ดีและมีประสิทธิภาพสูงขึ้น', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('103', $arr_dept[$j], 4, 'กำหนดแผนและปฏิบัติเพื่อให้บรรลุเป้าหมายที่ท้าทาย', 'กำหนดเป้าหมายที่ท้าทายและเป็นไปได้ยากเพื่อยกระดับผลสัมฤทธิ์ใหม่ให้ดีขึ้นกว่าผลงานเดิมอย่างเห็นได้ชัด
ลงมือกระทำการพัฒนาระบบ ขั้นตอน วิธีการปฏิบัติราชการเพื่อให้บรรลุมาตรฐานหรือผลสัมฤทธิ์ที่โดดเด่นและแตกต่าง', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('103', $arr_dept[$j], 5, 'ตัดสินใจโดยมีการคำนวณผลได้ผลเสียโดยชัดเจน เพื่อให้องค์กรบรรลุเป้าหมาย', 'ตัดสินใจ แยกแยะระดับความสำคัญของงานต่าง ๆ ในหน้าที่ โดยคิดคำนวณผลได้ผลเสียที่จะเกิดขึ้นอย่างชัดเจน (เช่น กล่าวถึงการพิจารณาเปรียบเทียบประโยชน์ที่ข้าราชการหรือประชาชนจะได้รับเพิ่มขึ้นเมื่อเปรียบเทียบกับต้นทุนหรือรายจ่ายที่รัฐต้องเสียไป)
บริหารจัดการและทุ่มเทเวลาและทรัพยากรเพื่อให้ได้ประโยชน์สูงสุดต่อภารกิจของหน่วยงานที่คาดการณ์ไว้', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('104', $arr_dept[$j], 1, 'ปฏิบัติหน้าที่ในส่วนของตนให้สำเร็จลุล่วง', 'ทำงานในส่วนที่ตนได้รับมอบหมายได้สำเร็จ สนับสนุนการตัดสินใจในกลุ่ม
รายงานให้สมาชิกทราบความคืบหน้าของการดำเนินงานในกลุ่ม หรือข้อมูลอื่นๆ ที่เป็นประโยชน์ต่อการทำงานอย่างต่อเนื่อง', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('104', $arr_dept[$j], 2, 'ผูกมิตรและร่วมมือ', 'สร้างสัมพันธ์เข้ากับผู้อื่นในกลุ่มได้ดี
สามารถปรับตัวเข้ากับสถานการณ์และกลุ่มงานที่หลากหลายในขณะปฏิบัติงานได้อย่างมีประสิทธิภาพ
เอื้อเฟื้อเผื่อแผ่ให้ความร่วมมือกับผู้อื่นในทีมและกลุ่มงานด้วยดี
กล่าวถึงเพื่อนร่วมงานในเชิงสร้างสรรค์', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('104', $arr_dept[$j], 3, 'รับฟังความเห็นและประสานสัมพันธ์', 'รับฟังความเห็นของสมาชิกในทีม เต็มใจเรียนรู้จากผู้อื่นรวมถึงผู้ใต้บังคับบัญชาและผู้ร่วมงาน
ประมวลความคิดเห็นต่างๆ มาใช้ประกอบการตัดสินใจหรือวางแผนงานร่วมกันในทีม
ประสานและส่งเสริมสัมพันธภาพอันดีในทีมเพื่อสนับสนุนการทำงานร่วมกันให้มีประสิทธิภาพยิ่งขึ้น', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('104', $arr_dept[$j], 4, 'ให้กำลังใจซึ่งกันและกัน', 'กล่าวชื่นชมให้กำลังใจเพื่อนร่วมงานได้อย่างจริงใจ
แสดงน้ำใจในเหตุวิกฤติ ให้ความช่วยเหลือแก่เพื่อนร่วมงานที่มีเหตุจำเป็นโดยไม่ต้องให้ร้องขอ
รักษามิตรภาพอันดีกับเพื่อนร่วมงานเพื่อช่วยเหลือกันในวาระต่างๆ ให้งานสำเร็จลุล่วงเป็นประโยชน์ต่อส่วนรวม', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('104', $arr_dept[$j], 5, 'รวมพลังสร้างความสามัคคีในทีม', 'ส่งเสริมความสามัคคีเป็นน้ำหนึ่งใจเดียวกันในทีม โดยไม่คำนึงถึงความชอบหรือไม่ชอบส่วนตน
ช่วยประสานรอยร้าว หรือคลี่คลายแก้ไขข้อขัดแย้งที่เกิดขึ้นในทีม
ประสานสัมพันธ์ส่งเสริมขวัญกำลังใจของทีมเพื่อรวมพลังกันในการปฏิบัติภารกิจใหญ่น้อยต่างๆ ให้บรรลุผล', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('105', $arr_dept[$j], 1, 'มีความสนใจใฝ่รู้ในสาขาอาชีพของตน', 'กระตือรือร้นในการศึกษาหาความรู้ สนใจเทคโนโลยีและองค์ความรู้ใหม่ๆ ในสาขาอาชีพของตน
หมั่นทดลองวิธีการทำงานแบบใหม่เพื่อพัฒนาประสิทธิภาพและความรู้ความสามารถของตนให้ดียิ่งขึ้น
ติดตามเทคโนโลยีองค์ความรู้ใหม่ๆ อยู่เสมอ ด้วยการสืบค้นข้อมูลจากแหล่งต่างๆ ที่จะเป็นประโยชน์ต่อการปฏิบัติราชการ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('105', $arr_dept[$j], 2, 'รอบรู้เท่าทันเหตุการณ์และเทคโนโลยีใหม่ๆ อยู่เสมอ', 'รอบรู้เท่าทันเทคโนโลยีหรือองค์ความรู้ใหม่ๆ ในสาขาอาชีพของตนและที่เกี่ยวข้อง หรืออาจมีผลกระทบต่อการปฏิบัติหน้าที่ของตน
ติดตามแนวโน้มวิทยาการที่ทันสมัย และเทคโนโลยีที่เกี่ยวข้องกับงานอย่างต่อเนื่อง', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('105', $arr_dept[$j], 3, 'นำความรู้ วิทยาการ หรือเทคโนโลยีใหม่ๆ ที่ได้ศึกษามาปรับใช้กับการทำงาน', 'เข้าใจประเด็นหลักๆ นัยสำคัญ และผลกระทบของวิทยาการต่างๆอย่างลึกซึ้ง
สามารถนำวิชาการ ความรู้ หรือเทคโนโลยีใหม่ๆ มาประยุกต์ใช้ในการปฏิบัติงานได้
สั่งสมความรู้ใหม่ๆ อยู่เสมอ และเล็งเห็นประโยชน์ความสำคัญขององค์ความรู้ เทคโนโลยีใหม่ๆ ที่จะส่งผลกระทบต่องานของตนในอนาคต', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('105', $arr_dept[$j], 4, 'รักษาและประยุกต์ความรู้ความเชี่ยวชาญทั้งเชิงลึกและเชิงกว้างอย่างต่อเนื่องและสม่ำเสมอ', 'มีความรู้ความเชี่ยวชาญแบบสหวิทยาการ และสามารถนำความรู้ไปปรับใช้ให้ปฏิบัติได้จริง
สามารถนำความรู้เชิงบูรณาการของตนไปใช้ในการสร้างวิสัยทัศน์เพื่อการปฏิบัติงานในอนาคต
ขวนขวายหาความรู้ที่เกี่ยวข้องกับงานทั้งเชิงลึกและเชิงกว้างอย่างต่อเนื่อง', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('105', $arr_dept[$j], 5, 'สร้างวัฒนธรรมแห่งการเรียนรู้เพื่อการพัฒนาในองค์กร', 'สนับสนุนให้เกิดบรรยากาศแห่งการพัฒนาความเชี่ยวชาญในองค์กร  ด้วยการจัดสรรทรัพยากร  เครื่องมืออุปกรณ์ที่เอื้อต่อการพัฒนา
ให้การสนับสนุน  ชมเชย  เมื่อมีผู้แสดงออกถึงความตั้งใจที่จะพัฒนาความเชี่ยวชาญในงาน
มีวิสัยทัศน์ในการเล็งเห็นประโยชน์ของเทคโนโลยี  องค์ความรู้  หรือวิทยาการใหม่ ๆ ต่อการปฏิบัติงานในอนาคต  และสนับสนุนส่งเสริมให้มีการนำ มาประยุกต์ใช้ในหน่วยงานอย่างต่อเนื่อง', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			} else {
				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('101', $arr_dept[$j], 1, 'แสดงความพยายามในการปฏิบัติหน้าที่ราชการให้ดี', 'พยายามทำงานในหน้าที่ให้ถูกต้อง
		พยายามปฏิบัติงานให้แล้วเสร็จตามกำหนดเวลา
		มานะอดทน ขยันหมั่นเพียรในการทำงาน
		แสดงออกว่าต้องการทำงานให้ได้ดีขึ้น
		แสดงความเห็นในเชิงปรับปรุงพัฒนาเมื่อเห็นความสูญเปล่า หรือหย่อนประสิทธิภาพในงาน', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('101', $arr_dept[$j], 2, 'แสดงสมรรถนะระดับที่ 1 และสามารถทำงานได้ผลงานตามเป้าหมายที่วางไว้', 'กำหนดมาตรฐาน หรือเป้าหมายในการทำงานเพื่อให้ได้ผลงานที่ดี
		ติดตาม และประเมินผลงานของตน โดยเทียบเคียงกับเกณฑ์มาตรฐาน 
		ทำงานได้ตามเป้าหมายที่ผู้บังคับบัญชากำหนด หรือเป้าหมายของหน่วยงานที่รับผิดชอบ 
		มีความละเอียดรอบคอบ เอาใจใส่ ตรวจตราความถูกต้อง เพื่อให้ได้งานที่มีคุณภาพ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('101', $arr_dept[$j], 3, 'แสดงสมรรถนะระดับที่ 2 และสามารถปรับปรุงวิธีการทำงานเพื่อให้ได้ผลงานที่มีประสิทธิภาพมากยิ่งขึ้น', 'ปรับปรุงวิธีการที่ทำให้ทำงานได้ดีขึ้น เร็วขึ้น มีคุณภาพดีขึ้น มีประสิทธิภาพมากขึ้น หรือทำให้ผู้รับบริการพึงพอใจมากขึ้น
		เสนอหรือทดลองวิธีการทำงานแบบใหม่ที่คาดว่าจะทำให้งานมีประสิทธิภาพมากขึ้น', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('101', $arr_dept[$j], 4, 'แสดงสมรรถนะระดับที่ 3 และสามารถกำหนดเป้าหมาย รวมทั้งพัฒนางาน เพื่อให้ได้ผลงานที่โดดเด่น หรือแตกต่างอย่างมีนัยสำคัญ', 'กำหนดเป้าหมายที่ท้าทาย และเป็นไปได้ยาก เพื่อให้ได้ผลงานที่ดีกว่าเดิมอย่างเห็นได้ชัด
		พัฒนาระบบ ขั้นตอน วิธีการทำงาน เพื่อให้ได้ผลงานที่โดดเด่น หรือแตกต่างไม่เคยมีผู้ใดทำได้มาก่อน', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('101', $arr_dept[$j], 5, 'แสดงสมรรถนะระดับที่ 4 และกล้าตัดสินใจ แม้ว่าการตัดสินใจนั้นจะมีความเสี่ยง เพื่อให้บรรลุเป้าหมายของหน่วยงาน หรือส่วนราชการ', 'ตัดสินใจได้ โดยมีการคำนวณผลได้ผลเสียอย่างชัดเจน และดำเนินการ เพื่อให้ภาครัฐและประชาชนได้ประโยชน์สูงสุด
		บริหารจัดการและทุ่มเทเวลา ตลอดจนทรัพยากร เพื่อให้ได้ประโยชน์สูงสุดต่อภารกิจของหน่วยงานตามที่วางแผนไว้', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('102', $arr_dept[$j], 1, 'สามารถให้บริการที่ผู้รับบริการต้องการได้ด้วยความเต็มใจ', 'ให้การบริการที่เป็นมิตร สุภาพ 
		ให้ข้อมูล ข่าวสาร ที่ถูกต้อง ชัดเจนแก่ผู้รับบริการ
		แจ้งให้ผู้รับบริการทราบความคืบหน้าในการดำเนินเรื่อง หรือขั้นตอนงานต่างๆ ที่ให้บริการอยู่ 
		ประสานงานภายในหน่วยงาน และหน่วยงานอื่นที่เกี่ยวข้อง เพื่อให้ผู้รับบริการได้รับบริการที่ต่อเนื่องและรวดเร็ว', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('102', $arr_dept[$j], 2, 'แสดงสมรรถนะระดับที่ 1 และช่วยแก้ปัญหาให้แก่ผู้รับบริการ', 'รับเป็นธุระ ช่วยแก้ปัญหาหรือหาแนวทางแก้ไขปัญหาที่เกิดขึ้นแก่ผู้รับบริการอย่างรวดเร็ว  ไม่บ่ายเบี่ยง ไม่แก้ตัว หรือปัดภาระ
		ดูแลให้ผู้รับบริการได้รับความพึงพอใจ และนำข้อขัดข้องใดๆ ในการให้บริการ ไปพัฒนาการให้บริการให้ดียิ่งขึ้น', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('102', $arr_dept[$j], 3, 'แสดงสมรรถนะระดับที่ 2 และให้บริการที่เกินความคาดหวัง แม้ต้องใช้เวลาหรือความพยายามอย่างมาก', 'ให้เวลาแก่ผู้รับบริการเป็นพิเศษ เพื่อช่วยแก้ปัญหาให้แก่ผู้รับบริการ
		ให้ข้อมูล ข่าวสาร ที่เกี่ยวข้องกับงานที่กำลังให้บริการอยู่ ซึ่งเป็นประโยชน์แก่ผู้รับบริการ แม้ว่าผู้รับบริการจะไม่ได้ถามถึง หรือไม่ทราบมาก่อน
		นำเสนอวิธีการในการให้บริการที่ผู้รับบริการจะได้รับประโยชน์สูงสุด', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('102', $arr_dept[$j], 4, 'แสดงสมรรถนะระดับที่ 3 และเข้าใจและให้บริการที่ตรงตามความต้องการที่แท้จริงของผู้รับบริการได้', 'เข้าใจ หรือพยายามทำความเข้าใจด้วยวิธีการต่างๆ เพื่อให้บริการได้ตรงตามความต้องการที่แท้จริงของผู้รับบริการ
		ให้คำแนะนำที่เป็นประโยชน์แก่ผู้รับบริการ เพื่อตอบสนองความจำเป็นหรือความต้องการที่แท้จริงของผู้รับบริการ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('102', $arr_dept[$j], 5, 'แสดงสมรรถนะระดับที่ 4 และให้บริการที่เป็นประโยชน์อย่างแท้จริงให้แก่ผู้รับบริการ', 'คิดถึงผลประโยชน์ของผู้รับบริการในระยะยาว และพร้อมที่จะเปลี่ยนวิธีหรือขั้นตอนการให้บริการ เพื่อประโยชน์สูงสุดของผู้รับบริการ
		เป็นที่ปรึกษาที่มีส่วนช่วยในการตัดสินใจที่ผู้รับบริการไว้วางใจ
		สามารถให้ความเห็นที่แตกต่างจากวิธีการ หรือขั้นตอนที่ผู้รับบริการต้องการให้สอดคล้องกับความจำเป็น ปัญหา โอกาส เพื่อเป็นประโยชน์อย่างแท้จริงของผู้รับบริการ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('103', $arr_dept[$j], 1, 'แสดงความสนใจและติดตามความรู้ใหม่ๆ ในสาขาอาชีพของตน หรือที่เกี่ยวข้อง', 'ศึกษาหาความรู้ สนใจเทคโนโลยีและองค์ความรู้ใหม่ๆ ในสาขาอาชีพของตน 
		พัฒนาความรู้ความสามารถของตนให้ดียิ่งขึ้น 
		ติดตามเทคโนโลยี และความรู้ใหม่ๆ อยู่เสมอด้วยการสืบค้นข้อมูลจากแหล่งต่างๆ ที่จะเป็นประโยชน์ต่อการปฏิบัติราชการ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('103', $arr_dept[$j], 2, 'แสดงสมรรถนะระดับที่ 1 และมีความรู้ในวิชาการ และเทคโนโลยีใหม่ๆ ในสาขาอาชีพของตน', 'รอบรู้ในเทคโนโลยีหรือองค์ความรู้ใหม่ๆ ในสาขาอาชีพของตน หรือที่เกี่ยวข้อง ซึ่งอาจมีผลกระทบต่อการปฏิบัติหน้าที่ราชการของตน 
		รับรู้ถึงแนวโน้มวิทยาการที่ทันสมัย และเกี่ยวข้องกับงานของตน อย่างต่อเนื่อง', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('103', $arr_dept[$j], 3, 'แสดงสมรรถนะระดับที่ 2 และสามารถนำความรู้ วิทยาการ หรือเทคโนโลยีใหม่ๆ มาปรับใช้กับการปฏิบัติหน้าที่ราชการ', 'สามารถนำวิชาการ ความรู้ หรือเทคโนโลยีใหม่ๆ มาประยุกต์ใช้ในการปฏิบัติหน้าที่ราชการได้ 
		สามารถแก้ไขปัญหาที่อาจเกิดจากการนำเทคโนโลยีใหม่มาใช้ในการปฏิบัติหน้าที่ราชการได้', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('103', $arr_dept[$j], 4, 'แสดงสมรรถนะระดับที่ 3 และศึกษา พัฒนาตนเองให้มีความรู้ และความเชี่ยวชาญในงานมากขึ้น ทั้งในเชิงลึก และเชิงกว้างอย่างต่อเนื่อง', 'มีความรู้ความเชี่ยวชาญในเรื่องที่มีลักษณะเป็นสหวิทยาการ และสามารถนำความรู้ไปปรับใช้ได้อย่างกว้างขวาง 
		สามารถนำความรู้เชิงบูรณาการของตนไปใช้ในการสร้างวิสัยทัศน์ เพื่อการปฏิบัติงานในอนาคต', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('103', $arr_dept[$j], 5, 'แสดงสมรรถนะระดับที่ 4 และสนับสนุนการทำงานของคนในส่วนราชการที่เน้นความเชี่ยวชาญในวิทยาการด้านต่างๆ', 'สนับสนุนให้เกิดบรรยากาศแห่งการพัฒนาความเชี่ยวชาญในองค์กร ด้วยการจัดสรรทรัพยากร เครื่องมือ อุปกรณ์ที่เอื้อต่อการพัฒนา
		บริหารจัดการให้ส่วนราชการนำเทคโนโลยี ความรู้ หรือวิทยาการใหม่ๆ มาใช้ในการปฏิบัติหน้าที่ราชการในงานอย่างต่อเนื่อง', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('104', $arr_dept[$j], 1, 'มีความสุจริต', 'ปฏิบัติหน้าที่ด้วยความสุจริต ไม่เลือกปฏิบัติ ถูกต้องตามกฎหมาย และวินัยข้าราชการ
		แสดงความคิดเห็นตามหลักวิชาชีพอย่างสุจริต', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('104', $arr_dept[$j], 2, 'แสดงสมรรถนะระดับที่ 1 และมีสัจจะเชื่อถือได้', 'รักษาคำพูด มีสัจจะ และเชื่อถือได้ 
		แสดงให้ปรากฎถึงความมีจิตสำนึกในความเป็นข้าราชการ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('104', $arr_dept[$j], 3, 'แสดงสมรรถนะระดับที่ 2 และยึดมั่นในหลักการ', 'ยึดมั่นในหลักการ จรรยาบรรณแห่งวิชาชีพ และจรรยาข้าราชการไม่เบี่ยงเบนด้วยอคติหรือผลประโยชน์ กล้ารับผิด และรับผิดชอบ
		เสียสละความสุขส่วนตน เพื่อให้เกิดประโยชน์แก่ทางราชการ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('104', $arr_dept[$j], 4, 'แสดงสมรรถนะระดับที่ 3 และยืนหยัดเพื่อความถูกต้อง', 'ยืนหยัดเพื่อความถูกต้องโดยมุ่งพิทักษ์ผลประโยชน์ของทางราชการ แม้ตกอยู่ในสถานการณ์ที่อาจยากลำบาก
		กล้าตัดสินใจ ปฏิบัติหน้าที่ราชการด้วยความถูกต้อง เป็นธรรม แม้อาจก่อความไม่พึงพอใจให้แก่ผู้เสียประโยชน์', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('104', $arr_dept[$j], 5, 'แสดงสมรรถนะระดับที่ 4 และอุทิศตนเพื่อความยุติธรรม', 'ยืนหยัดพิทักษ์ผลประโยชน์และชื่อเสียงของประเทศชาติแม้ในสถานการณ์ที่อาจเสี่ยงต่อความมั่นคงในตำแหน่งหน้าที่การงาน หรืออาจเสี่ยงภัยต่อชีวิต', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('105', $arr_dept[$j], 1, 'ทำหน้าที่ของตนในทีมให้สำเร็จ', 'สนับสนุนการตัดสินใจของทีม และทำงานในส่วนที่ตนได้รับมอบหมาย 
		รายงานให้สมาชิกทราบความคืบหน้าของการดำเนินงานของตนในทีม
		ให้ข้อมูล ที่เป็นประโยชน์ต่อการทำงานของทีม', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('105', $arr_dept[$j], 2, 'แสดงสมรรถนะระดับที่ 1 และให้ความร่วมมือในการทำงานกับเพื่อนร่วมงาน', 'สร้างสัมพันธ์  เข้ากับผู้อื่นในกลุ่มได้ดี
		ให้ความร่วมมือกับผู้อื่นในทีมด้วยดี
		กล่าวถึงเพื่อนร่วมงานในเชิงสร้างสรรค์และแสดงความเชื่อมั่นในศักยภาพของเพื่อนร่วมทีม ทั้งต่อหน้าและลับหลัง', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('105', $arr_dept[$j], 3, 'แสดงสมรรถนะระดับที่ 2 และประสานความร่วมมือของสมาชิกในทีม', 'รับฟังความเห็นของสมาชิกในทีม และเต็มใจเรียนรู้จากผู้อื่น 
		ตัดสินใจหรือวางแผนงานร่วมกันในทีมจากความคิดเห็นของเพื่อนร่วมทีม
		ประสานและส่งเสริมสัมพันธภาพอันดีในทีม เพื่อสนับสนุนการทำงานร่วมกันให้มีประสิทธิภาพยิ่งขึ้น', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('105', $arr_dept[$j], 4, 'แสดงสมรรถนะระดับที่ 3 และสนับสนุน ช่วยเหลือเพื่อนร่วมทีม เพื่อให้งานประสบความสำเร็จ', 'ยกย่อง และให้กำลังใจเพื่อนร่วมทีมอย่างจริงใจ 
		ให้ความช่วยเหลือเกื้อกูลแก่เพื่อนร่วมทีม แม้ไม่มีการร้องขอ
		รักษามิตรภาพอันดีกับเพื่อนร่วมทีม เพื่อช่วยเหลือกันในวาระต่างๆให้งานสำเร็จ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('105', $arr_dept[$j], 5, 'แสดงสมรรถนะระดับที่ 4 และสามารถนำทีมให้ปฏิบัติภารกิจให้ได้ผลสำเร็จ', 'เสริมสร้างความสามัคคีในทีม โดยไม่คำนึงความชอบหรือไม่ชอบส่วนตน 
		คลี่คลาย หรือแก้ไขข้อขัดแย้งที่เกิดขึ้นในทีม
		ประสานสัมพันธ์ สร้างขวัญกำลังใจของทีมเพื่อปฏิบัติภารกิจของส่วนราชการให้บรรลุผล', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('201', $arr_dept[$j], 1, 'ดำเนินการประชุมได้ดีและคอยแจ้งข่าวสารความเป็นไปโดยตลอด', 'ดำเนินการประชุมให้เป็นไปตามระเบียบ วาระ วัตถุประสงค์ และเวลาตลอดจนมอบหมายงานให้แก่บุคคลในกลุ่มได้
		แจ้งข่าวสารให้ผู้ที่จะได้รับผลกระทบจากการตัดสินใจรับทราบอยู่เสมอ แม้ไม่ได้ถูกกำหนดให้ต้องกระทำ
		อธิบายเหตุผลในการตัดสินใจให้ผู้ที่เกี่ยวข้องทราบ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('201', $arr_dept[$j], 2, 'แสดงสมรรถนะระดับที่ 1 และเป็นผู้นำในการทำงานของกลุ่มและใช้อำนาจอย่างยุติธรรม', 'ส่งเสริมและกระทำการเพื่อให้กลุ่มปฏิบัติหน้าที่ได้อย่างเต็มประสิทธิภาพ
		กำหนดเป้าหมาย ทิศทางที่ชัดเจน จัดกลุ่มงานและเลือกคนให้เหมาะกับงาน หรือกำหนดวิธีการที่จะทำให้กลุ่มทำงานได้ดีขึ้น 
		รับฟังความคิดเห็นของผู้อื่น
		สร้างขวัญกำลังใจในการปฏิบัติงาน 
		ปฏิบัติต่อสมาชิกในทีมด้วยความยุติธรรม', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('201', $arr_dept[$j], 3, 'แสดงสมรรถนะระดับที่ 2 และให้การดูแลและช่วยเหลือทีมงาน', 'เป็นที่ปรึกษาและช่วยเหลือทีมงาน 
		ปกป้องทีมงาน และชื่อเสียงของส่วนราชการ 
		จัดหาบุคลากร ทรัพยากร หรือข้อมูลที่สำคัญมาให้ทีมงาน', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('201', $arr_dept[$j], 4, 'แสดงสมรรถนะระดับที่ 3 และประพฤติตนสมกับเป็นผู้นำ', 'กำหนดธรรมเนียมปฏิบัติประจำกลุ่มและประพฤติตนอยู่ในกรอบของธรรมเนียมปฏิบัตินั้น
		ประพฤติปฏิบัติตนเป็นแบบอย่างที่ดี
		ยึดหลักธรรมาภิบาลในการปกครองผู้ใต้บังคับบัญชา', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('201', $arr_dept[$j], 5, 'แสดงสมรรถนะระดับที่ 4 และนำทีมงานให้ก้าวไปสู่พันธกิจระยะยาวขององค์กร', 'สามารถรวมใจคนและสร้างแรงบันดาลใจให้ทีมงานเกิดความมั่นใจในการปฏิบัติภารกิจให้สำเร็จลุล่วง
		เล็งเห็นการเปลี่ยนแปลงในอนาคต และมีวิสัยทัศน์ในการสร้างกลยุทธ์เพื่อรับมือกับการเปลี่ยนแปลงนั้น', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('202', $arr_dept[$j], 1, 'รู้และเข้าใจวิสัยทัศน์ขององค์กร', 'รู้ เข้าใจและสามารถอธิบายให้ผู้อื่นเข้าใจได้ว่างานที่ทำอยู่นั้นเกี่ยวข้องหรือตอบสนองต่อวิสัยทัศน์ของส่วนราชการอย่างไร', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('202', $arr_dept[$j], 2, 'แสดงสมรรถนะระดับที่ 1 และช่วยทำให้ผู้อื่นรู้และเข้าใจวิสัยทัศน์ขององค์กร', 'อธิบายให้ผู้อื่นรู้และเข้าใจวิสัยทัศน์และเป้าหมายการทำงานของหน่วยงานภายใต้ภาพรวมของส่วนราชการได้ 
		แลกเปลี่ยนข้อมูลรวมถึงรับฟังความคิดเห็นของผู้อื่นเพื่อประกอบการกำหนดวิสัยทัศน์', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('202', $arr_dept[$j], 3, 'แสดงสมรรถนะระดับที่ 2 และ สร้างแรงจูงใจให้ผู้อื่นเต็มใจที่จะปฏิบัติตามวิสัยทัศน์', 'โน้มน้าวให้สมาชิกในทีมเกิดความเต็มใจและกระตือรือร้นที่จะปฏิบัติหน้าที่ราชการเพื่อตอบสนองต่อวิสัยทัศน์ 
		ให้คำปรึกษาแนะนำแก่สมาชิกในทีมถึงแนวทางในการทำงานโดยยึดถือวิสัยทัศน์และเป้าหมายขององค์กรเป็นสำคัญ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('202', $arr_dept[$j], 4, 'แสดงสมรรถนะระดับที่ 3 และกำหนดนโยบายให้สอดคล้องกับวิสัยทัศน์ของส่วนราชการ', 'ริเริ่มและกำหนดนโยบายใหม่ๆ เพื่อตอบสนองต่อการนำวิสัยทัศน์ไปสู่ความสำเร็จ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('202', $arr_dept[$j], 5, 'แสดงสมรรถนะระดับที่ 4 และกำหนดวิสัยทัศน์ของส่วนราชการให้สอดคล้องกับวิสัยทัศน์ระดับประเทศ', 'กำหนดวิสัยทัศน์ เป้าหมาย และทิศทางในการปฏิบัติหน้าที่ของส่วนราชการเพื่อให้บรรลุวิสัยทัศน์ซึ่งสอดคล้องกับวิสัยทัศน์ระดับประเทศ 
		คาดการณ์ได้ว่าประเทศจะได้รับผลกระทบอย่างไรจากการเปลี่ยนแปลงทั้งภายในและภายนอก', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('203', $arr_dept[$j], 1, 'รู้และเข้าใจนโยบายรวมทั้งภารกิจภาครัฐ ว่ามีความเกี่ยวโยงกับหน้าที่ความรับผิดชอบของหน่วยงานอย่างไร', 'เข้าใจนโยบาย ภารกิจ รวมทั้งกลยุทธ์ของภาครัฐและส่วนราชการ ว่าสัมพันธ์ เชื่อมโยงกับภารกิจของหน่วยงานที่ตนดูแลรับผิดชอบอย่างไร
		สามารถวิเคราะห์ปัญหา อุปสรรคหรือโอกาสของหน่วยงานได้', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('203', $arr_dept[$j], 2, 'แสดงสมรรถนะระดับที่ 1 และนำประสบการณ์มาประยุกต์ใช้ในการกำหนดกลยุทธ์ได้', 'ประยุกต์ใช้ประสบการณ์ในการกำหนดกลยุทธ์ของหน่วยงานที่ตนดูแลรับผิดชอบให้สอดคล้องกับกลยุทธ์ภาครัฐได้ 
		ใช้ความรู้ความเข้าใจในระบบราชการมาปรับกลยุทธ์ให้เหมาะสมกับสถานการณ์ที่เปลี่ยนแปลงไปได้', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('203', $arr_dept[$j], 3, 'แสดงสมรรถนะระดับที่ 2 และนำทฤษฎีหรือแนวคิดซับซ้อนมาใช้ในการกำหนดกลยุทธ์', 'ประยุกต์ใช้ทฤษฎี หรือแนวคิดซับซ้อน ในการคิดและพัฒนาเป้าหมายหรือกลยุทธ์ของหน่วยงานที่ตนดูแลรับผิดชอบ
		ประยุกต์แนวทางปฏิบัติที่ประสบความสำเร็จ (Best Practice) หรือผลการวิจัยต่างๆ มากำหนดแผนงานเชิงกลยุทธ์ในหน่วยงานที่ตนดูแลรับผิดชอบ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('203', $arr_dept[$j], 4, 'แสดงสมรรถนะระดับที่ 3 และ กำหนดกลยุทธ์ที่สอดคล้องกับสถานการณ์ต่างๆ ที่เกิดขึ้น', 'ประเมินและสังเคราะห์สถานการณ์ ประเด็น หรือปัญหาทางเศรษฐกิจ สังคม การเมืองภายในประเทศ หรือของโลกโดยมองภาพในลักษณะองค์รวม เพื่อใช้ในการกำหนด กลยุทธ์ภาครัฐหรือส่วนราชการ
		คาดการณ์สถานการณ์ในอนาคต และกำหนดกลยุทธ์ให้สอดคล้องกับสถานการณ์ต่างๆ ที่จะเกิดขึ้นเพื่อให้บรรลุพันธกิจของส่วนราชการ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('203', $arr_dept[$j], 5, 'แสดงสมรรถนะระดับที่ 4 และบูรณาการองค์ความรู้ใหม่มาใช้ในการกำหนดกลยุทธ์ภาครัฐ', 'ริเริ่ม สร้างสรรค์ และบูรณาการองค์ความรู้ใหม่ในการกำหนดกลยุทธ์ภาครัฐ โดยพิจารณาจากบริบทในภาพรวม
		ปรับเปลี่ยนทิศทางของกลยุทธ์ในการพัฒนาประเทศอย่างต่อเนื่อง', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('204', $arr_dept[$j], 1, 'เห็นความจำเป็นของการปรับเปลี่ยน', 'เห็นความจำเป็นของการปรับเปลี่ยน และปรับพฤติกรรมหรือแผนการทำงานให้สอดคล้องกับการเปลี่ยนแปลงนั้น
		เข้าใจและยอมรับถึงความจำเป็นของการปรับเปลี่ยน และเรียนรู้เพื่อให้สามารถปรับตัวรับการ เปลี่ยนแปลงนั้นได้', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('204', $arr_dept[$j], 2, 'แสดงสมรรถนะระดับที่ 1 และสามารถทำให้ผู้อื่นเข้าใจการปรับเปลี่ยนที่จะเกิดขึ้น', 'ช่วยเหลือให้ผู้อื่นเข้าใจถึงความจำเป็นและประโยชน์ของการเปลี่ยนแปลงนั้น 
		สนับสนุนความพยายามในการปรับเปลี่ยนองค์กร พร้อมทั้งเสนอแนะวิธีการและมีส่วนร่วมในการปรับเปลี่ยนดังกล่าว', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('204', $arr_dept[$j], 3, 'แสดงสมรรถนะระดับที่ 2 และกระตุ้น และสร้างแรงจูงใจให้ผู้อื่นเห็นความสำคัญของการปรับเปลี่ยน', 'กระตุ้น และสร้างแรงจูงใจให้ผู้อื่นเห็นความสำคัญของการปรับเปลี่ยน เพื่อให้เกิดความร่วมแรงร่วมใจ
		เปรียบเทียบให้เห็นว่าสิ่งที่ปฏิบัติอยู่ในปัจจุบันกับสิ่งที่จะเปลี่ยนแปลงไปนั้นแตกต่างกันในสาระสำคัญอย่างไร
		สร้างความเข้าใจให้เกิดขึ้นแก่ผู้ที่ยังไม่ยอมรับการเปลี่ยนแปลงนั้น', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('204', $arr_dept[$j], 4, 'แสดงสมรรถนะระดับที่ 3 และวางแผนงานที่ดีเพื่อรับการปรับเปลี่ยนในองค์กร', 'วางแผนอย่างเป็นระบบและชี้ให้เห็นประโยชน์ของการปรับเปลี่ยน
		เตรียมแผน และติดตามการบริหารการเปลี่ยนแปลงอย่างสม่ำเสมอ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('204', $arr_dept[$j], 5, 'แสดงสมรรถนะระดับที่ 4 และผลักดันให้เกิดการปรับเปลี่ยนอย่างมีประสิทธิภาพ', 'ผลักดันให้การปรับเปลี่ยนสามารถดำเนินไปได้อย่างราบรื่นและประสบความสำเร็จ
		สร้างขวัญกำลังใจ และความเชื่อมั่นในการขับเคลื่อนให้เกิดการปรับเปลี่ยนอย่างมีประสิทธิภาพ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('205', $arr_dept[$j], 1, 'ไม่แสดงพฤติกรรมที่ไม่เหมาะสม', 'ไม่แสดงพฤติกรรมที่ไม่สุภาพหรือไม่เหมาะสมในทุกสถานการณ์', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('205', $arr_dept[$j], 2, 'แสดงสมรรถนะระดับที่ 1 และควบคุมอารมณ์ในแต่ละสถานการณ์ได้เป็นอย่างดี', 'รู้เท่าทันอารมณ์ของตนเองและควบคุมได้อย่างเหมาะสม โดยอาจหลีกเลี่ยงจากสถานการณ์ที่เสี่ยงต่อการเกิดความรุนแรงขึ้น หรืออาจเปลี่ยนหัวข้อสนทนา หรือหยุดพักชั่วคราวเพื่อสงบสติอารมณ์', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('205', $arr_dept[$j], 3, 'แสดงสมรรถนะระดับที่ 2 และสามารถใช้ถ้อยทีวาจา หรือปฏิบัติงานต่อไปได้อย่างสงบ แม้จะอยู่ในภาวะที่ถูกยั่วยุ', 'รู้สึกได้ถึงความรุนแรงทางอารมณ์ในระหว่างการสนทนา หรือการปฏิบัติงาน เช่น ความโกรธ ความผิดหวัง หรือความกดดัน แต่ไม่แสดงออกแม้จะถูกยั่วยุ โดยยังคงสามารถปฏิบัติงานต่อไปได้อย่างสงบ
		สามารถเลือกใช้วิธีการแสดงออกที่เหมาะสมเพื่อไม่ให้เกิดผลในเชิงลบทั้งต่อตนเองและผู้อื่น', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('205', $arr_dept[$j], 4, 'แสดงสมรรถนะระดับที่ 3 และจัดการความเครียดได้อย่างมีประสิทธิภาพ', 'สามารถจัดการกับความเครียดหรือผลที่อาจเกิดขึ้นจากภาวะกดดันทางอารมณ์ได้อย่างมีประสิทธิภาพ
		ประยุกต์ใช้วิธีการเฉพาะตน หรือวางแผนล่วงหน้าเพื่อจัดการกับความเครียดและความกดดันทางอารมณ์ที่คาดหมายได้ว่าจะเกิดขึ้น
		บริหารจัดการอารมณ์ของตนได้อย่างมีประสิทธิภาพเพื่อลดความเครียดของตนเองหรือผู้ร่วมงาน', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('205', $arr_dept[$j], 5, 'แสดงสมรรถนะระดับที่ 4 และเอาชนะอารมณ์ด้วยความเข้าใจ', 'ระงับอารมณ์รุนแรง ด้วยการพยายามทำความเข้าใจและแก้ไขที่ต้นเหตุของปัญหา รวมทั้งบริบทและปัจจัยแวดล้อมต่างๆ 
		ในสถานการณ์ที่ตึงเครียดมากก็ยังสามารถควบคุมอารมณ์ของตนเองได้ รวมถึงทำให้คนอื่นๆ มีอารมณ์ที่สงบลงได้', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('206', $arr_dept[$j], 1, 'สอนงานหรือให้คำแนะนำเกี่ยวกับวิธีปฏิบัติงาน', 'สอนงานด้วยการให้คำแนะนำอย่างละเอียด หรือด้วยการสาธิตวิธีปฏิบัติงาน
		ชี้แนะแหล่งข้อมูล หรือแหล่งทรัพยากรอื่นๆ เพื่อใช้ในการพัฒนาการปฏิบัติงาน', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE,, DEPARTMENT_ID CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('206', $arr_dept[$j], 2, 'แสดงสมรรถนะระดับที่ 1 และตั้งใจพัฒนาผู้ใต้บังคับบัญชาให้มีศักยภาพ', 'สามารถให้คำปรึกษาชี้แนะแนวทางในการพัฒนาหรือส่งเสริมข้อดีและปรับปรุงข้อด้อยให้ลดลง
		ให้โอกาสผู้ใต้บังคับบัญชาได้แสดงศักยภาพเพื่อสร้างความมั่นใจในการปฏิบัติงาน', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('206', $arr_dept[$j], 3, 'แสดงสมรรถนะระดับที่ 2 และวางแผนเพื่อให้โอกาสผู้ใต้บังคับบัญชาแสดงความสามารถในการทำงาน', 'วางแผนในการพัฒนาผู้ใต้บังคับบัญชาทั้งในระยะสั้นและระยะยาว 
		มอบหมายงานที่เหมาะสม รวมทั้งให้โอกาสผู้ใต้บังคับบัญชาที่จะได้รับการฝึกอบรม หรือพัฒนาอย่างสม่ำเสมอเพื่อสนับสนุนการเรียนรู้ 
		มอบหมายหน้าที่ความรับผิดชอบในระดับตัดสินใจให้ผู้ใต้บังคับบัญชาเป็นบางเรื่องเพื่อให้มีโอกาสริเริ่มสิ่งใหม่ๆ หรือบริหารจัดการด้วยตนเอง', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('206', $arr_dept[$j], 4, 'แสดงสมรรถนะระดับที่ 3 และสามารถช่วยแก้ไขปัญหาที่เป็นอุปสรรคต่อการพัฒนาศักยภาพของผู้ใต้บังคับบัญชา', 'สามารถปรับเปลี่ยนทัศนคติเดิมที่เป็นปัจจัยขัดขวางการพัฒนาศักยภาพของผู้ใต้บังคับบัญชา
		สามารถเข้าใจถึงสาเหตุแห่งพฤติกรรมของแต่ละบุคคล เพื่อนำมาเป็นปัจจัยในการพัฒนาศักยภาพของผู้ใต้บังคับบัญชาได้', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('206', $arr_dept[$j], 5, 'แสดงสมรรถนะระดับที่ 4 และทำให้ส่วนราชการมีระบบการสอนงานและการมอบหมายหน้าที่ความรับผิดชอบ', 'สร้าง และสนับสนุนให้มีการสอนงานและมีการมอบหมายหน้าที่ความรับผิดชอบอย่างเป็นระบบในส่วนราชการ 
		สร้าง และสนับสนุนให้มีวัฒนธรรมแห่งการเรียนรู้อย่างต่อเนื่องในส่วนราชการ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('301', $arr_dept[$j], 1, 'แยกแยะประเด็นปัญหา หรืองานออกเป็นส่วนย่อยๆ', 'แยกแยะปัญหาออกเป็นรายการอย่างง่ายๆได้โดยไม่เรียงลำดับความสำคัญ
		วางแผนงานโดยแตกประเด็นปัญหาออกเป็นส่วนๆ หรือเป็นกิจกรรมต่างๆ ได้', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('301', $arr_dept[$j], 2, 'แสดงสมรรถนะระดับที่ 1 และเข้าใจความสัมพันธ์ขั้นพื้นฐานของปัญหา หรืองาน', 'ระบุเหตุและผล ในแต่ละสถานการณ์ต่างๆ ได้
		ระบุข้อดีข้อเสียของประเด็นต่างๆได้
		วางแผนงานโดยจัดเรียงงาน หรือกิจกรรมต่างๆตามลำดับความสำคัญหรือความเร่งด่วนได้', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('301', $arr_dept[$j], 3, 'แสดงสมรรถนะระดับที่ 2 และเข้าใจความสัมพันธ์ที่ซับซ้อน ของปัญหา หรืองาน', 'เชื่อมโยงเหตุปัจจัยที่ซับซ้อนของแต่ละสถานการณ์ หรือเหตุการณ์
		วางแผนงานโดยกำหนดกิจกรรม ขั้นตอนการดำเนินงานต่างๆ ที่มีผู้เกี่ยวข้องหลายฝ่ายได้อย่างมีประสิทธิภาพ และสามารถคาดการณ์เกี่ยวกับปัญหา หรืออุปสรรคที่อาจเกิดขึ้นได้', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('301', $arr_dept[$j], 4, 'แสดงสมรรถนะระดับที่ 3 และสามารถวิเคราะห์ หรือวางแผนงานที่ซับซ้อนได้', 'เข้าใจประเด็นปัญหาในระดับที่สามารถแยกแยะเหตุปัจจัยเชื่อมโยงซับซ้อนในรายละเอียด และสามารถวิเคราะห์ความสัมพันธ์ของปัญหากับสถานการณ์หนึ่งๆ ได้ 
		วางแผนงานที่ซับซ้อนโดยกำหนดกิจกรรม ขั้นตอนการดำเนินงานต่างๆที่มีหน่วยงานหรือผู้เกี่ยวข้องหลายฝ่าย รวมถึงคาดการณ์ปัญหา อุปสรรค และวางแนวทางการป้องกันแก้ไขไว้ล่วงหน้า', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('301', $arr_dept[$j], 5, 'แสดงสมรรถนะระดับที่ 4 และใช้เทคนิค และรูปแบบต่างๆ ในการกำหนดแผนงาน หรือขั้นตอนการทำงาน  เพื่อเตรียมทางเลือกสำหรับการป้องกัน หรือแก้ไขปัญหา ที่เกิดขึ้น', 'ใช้เทคนิคการวิเคราะห์ที่เหมาะสมในการแยกแยะประเด็นปัญหาที่ซับซ้อนเป็นส่วนๆ
		ใช้เทคนิคการวิเคราะห์หลากหลายรูปแบบเพื่อหาทางเลือก ในการแก้ปัญหา รวมถึงพิจารณาข้อดีข้อเสียของทางเลือกแต่ละทาง
		วางแผนงานที่ซับซ้อนโดยกำหนดกิจกรรม ขั้นตอนการดำเนินงานต่างๆ ที่มีหน่วยงานหรือผู้เกี่ยวข้องหลายฝ่าย คาดการณ์ปัญหา อุปสรรค แนวทางการป้องกันแก้ไข รวมทั้งเสนอแนะทางเลือกและข้อดีข้อเสียไว้ให้', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('302', $arr_dept[$j], 1, 'ใช้กฎพื้นฐานทั่วไป', 'ใช้กฎพื้นฐาน หลักเกณฑ์ หรือสามัญสำนึกในการระบุประเด็นปัญหา หรือแก้ปัญหาในงาน', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('302', $arr_dept[$j], 2, 'แสดงสมรรถนะระดับที่ 1 และประยุกต์ใช้ประสบการณ์', 'ระบุถึงความเชื่อมโยงของข้อมูล แนวโน้ม และความไม่ครบถ้วนของข้อมูลได้
		ประยุกต์ใช้ประสบการณ์ในการระบุประเด็นปัญหาหรือแก้ปัญหาในงานได้', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('302', $arr_dept[$j], 3, 'แสดงสมรรถนะระดับที่ 2 และประยุกต์ทฤษฎีหรือแนวคิดซับซ้อน', 'ประยุกต์ใช้ทฤษฎี แนวคิดที่ซับซ้อน หรือแนวโน้มในอดีตในการระบุหรือแก้ปัญหาตามสถานการณ์ แม้ในบางกรณี แนวคิดที่นำมาใช้กับสถานการณ์อาจไม่มีสิ่งบ่งบอกถึงความเกี่ยวข้องเชื่อมโยงกันเลยก็ตาม', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('302', $arr_dept[$j], 4, 'แสดงสมรรถนะระดับที่ 3 และอธิบายข้อมูล หรือสถานการณ์ที่มีความยุ่งยากซับซ้อนให้เข้าใจได้ง่าย', 'สามารถอธิบายความคิด หรือสถานการณ์ที่ซับซ้อนให้ง่ายและสามารถเข้าใจได้
		สามารถสังเคราะห์ข้อมูล สรุปแนวคิด ทฤษฎี องค์ความรู้  ที่ซับซ้อนให้เข้าใจได้โดยง่ายและเป็นประโยชน์ต่องาน', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('302', $arr_dept[$j], 5, 'แสดงสมรรถนะระดับที่ 4 และคิดริเริ่ม สร้างสรรค์องค์ความรู้ใหม่', 'ริเริ่ม สร้างสรรค์ ประดิษฐ์คิดค้น รวมถึงสามารถนำเสนอรูปแบบ วิธีการหรือองค์ความรู้ใหม่ซึ่งอาจไม่เคยปรากฎมาก่อน', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('303', $arr_dept[$j], 1, 'ใส่ใจและให้ความสำคัญในการส่งเสริมและพัฒนาผู้อื่น', 'สนับสนุนให้ผู้อื่นพัฒนาศักยภาพหรือสุขภาวะทั้งทางปัญญา ร่างกาย จิตใจที่ดี 
		แสดงความเชื่อมั่นว่าผู้อื่นมีศักยภาพที่จะพัฒนาตนเองให้ดียิ่งขึ้นได้', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('303', $arr_dept[$j], 2, 'แสดงสมรรถนะระดับที่ 1 และสอนหรือให้คำแนะนำเพื่อพัฒนาให้ผู้อื่นมีศักยภาพหรือมีสุขภาวะทั้งทางปัญญา ร่างกาย จิตใจหรือทัศนคติที่ดี', 'สาธิต หรือให้คำแนะนำเกี่ยวกับการปฏิบัติตน เพื่อพัฒนาศักยภาพ สุขภาวะหรือทัศนคติที่ดีอย่างยั่งยืน
		มุ่งมั่นที่จะสนับสนุน โดยชี้แนะแหล่งข้อมูล หรือทรัพยากรที่จำเป็นต่อการพัฒนาของผู้อื่น', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('303', $arr_dept[$j], 3, 'แสดงสมรรถนะที่ 2 และใส่ใจในการให้เหตุผลประกอบการแนะนำ หรือมีส่วนสนับสนุนในการพัฒนาผู้อื่น', 'ให้แนวทางพร้อมทั้งอธิบายเหตุผลประกอบ เพื่อให้ผู้อื่นมั่นใจว่าสามารถพัฒนาศักยภาพสุขภาวะหรือทัศนคติที่ดีอย่างยั่งยืนได้
		ส่งเสริมให้มีการแลกเปลี่ยนการเรียนรู้หรือประสบการณ์  เพื่อให้ผู้อื่นมีโอกาสได้ถ่ายทอด และเรียนรู้วิธีการพัฒนาศักยภาพหรือเสริมสร้างสุขภาวะหรือทัศนคติที่ดีอย่างยั่งยืน
		สนับสนุนด้วยอุปกรณ์ เครื่องมือ หรือวิธีการในภาคปฏิบัติเพื่อให้ผู้อื่นมั่นใจว่าตนสามารถพัฒนาศักยภาพ สุขภาวะหรือทัศนคติที่ดีอย่างยั่งยืนและมีประสิทธิภาพสูงสุดได้', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('303', $arr_dept[$j], 4, 'แสดงสมรรถนะระดับที่ 3 และติดตามและให้คำติชมเพื่อส่งเสริมการพัฒนาอย่างต่อเนื่อง', 'ติดตามผลการพัฒนาของผู้อื่นรวมทั้งให้คำติชมที่จะส่งเสริมให้เกิดการพัฒนาอย่างต่อเนื่อง
		ให้คำแนะนำที่เหมาะสมกับลักษณะเฉพาะ เพื่อพัฒนาศักยภาพ สุขภาวะหรือทัศนคติที่ดีของแต่ละบุคคล', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('303', $arr_dept[$j], 5, 'แสดงสมรรถนะระดับที่ 4 และมุ่งเน้นการพัฒนาจากรากของปัญหา หรือความต้องการที่แท้จริง', 'พยายามทำความเข้าใจปัญหาหรือความต้องการที่แท้จริงของผู้อื่น เพื่อให้สามารถจัดทำแนวทางในการพัฒนาศักยภาพ สุขภาวะ หรือทัศนคติที่ดีอย่างยั่งยืนได้
		ค้นคว้า สร้างสรรค์วิธีการใหม่ๆ ในการพัฒนาศักยภาพ สุขภาวะหรือทัศนคติที่ดี ซึ่งตรงกับปัญหาหรือความต้องการที่แท้จริงของผู้อื่น', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('304', $arr_dept[$j], 1, 'สั่งให้กระทำการใด ๆ ตามมาตรฐาน กฎ ระเบียบ ข้อบังคับ', 'สั่งให้กระทำการใด ๆ  ตามมาตรฐาน  กฎ ระเบียบ ข้อบังคับ 
		มอบหมายงานในรายละเอียดบางส่วนให้ผู้อื่นดำเนินการแทนได้ เพื่อให้ตนเองปฏิบัติงานตามตำแหน่งหน้าที่ได้มากขึ้น', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('304', $arr_dept[$j], 2, 'แสดงสมรรถนะระดับที่ 1 และกำหนดขอบเขตข้อจำกัดในการกระทำการใดๆ', 'ปฏิเสธคำขอของผู้อื่น ที่ไม่สมเหตุสมผลหรือไม่เป็นไปตามมาตรฐาน กฎ ระเบียบ ข้อบังคับ  
		กำหนดลักษณะเชิงพฤติกรรมหรือแนวทางปฏิบัติหน้าที่ราชการไว้เป็นมาตรฐาน 
		สร้างเงื่อนไขเพื่อให้ผู้อื่นปฏิบัติตามกฎหมายหรือระเบียบ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('304', $arr_dept[$j], 3, 'แสดงสมรรถนะระดับที่ 2 และสั่งให้ปรับมาตรฐาน หรือปรับปรุงการปฏิบัติงานให้ดีขึ้น', 'กำหนดมาตรฐานในการปฏิบัติงาน ให้แตกต่าง หรือสูงขึ้น 
		สั่งให้ปรับปรุงการปฏิบัติงานให้เป็นไปตามมาตรฐาน กฎ ระเบียบ ข้อบังคับ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('304', $arr_dept[$j], 4, 'แสดงสมรรถนะระดับที่ 3 และติดตามควบคุมให้ปฏิบัติตามมาตรฐาน กฎ ระเบียบ ข้อบังคับ', 'ติดตาม ควบคุม ตรวจสอบหน่วยงานภายใต้การกำกับดูแลให้ปฏิบัติตามมาตรฐาน กฎ ระเบียบ ข้อบังคับ
		เตือนให้ทราบล่วงหน้าอย่างชัดเจนถึงผลที่จะเกิดขึ้นจากการไม่ปฏิบัติตามมาตรฐาน กฎ ระเบียบ ข้อบังคับ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('304', $arr_dept[$j], 5, 'แสดงสมรรถนะระดับที่ 4 และดำเนินการในกรณีที่มีการปฏิบัติไม่เป็นไปตามมาตรฐาน หรือขัดต่อกฎหมาย กฎ ระเบียบ ข้อบังคับ', 'ใช้วิธีเผชิญหน้าอย่างเปิดเผยตรงไปตรงมาในกรณีที่มีปัญหา หรือมีการปฏิบัติที่ไม่เป็นไปตามมาตรฐาน หรือขัดต่อกฎหมาย กฎ ระเบียบ ข้อบังคับ 
		ดำเนินการให้เป็นไปตามกฎหมายอย่างเคร่งครัด กรณีที่มีการปฏิบัติไม่เป็นไปตามมาตรฐาน หรือขัดต่อกฎหมาย กฎ ระเบียบ ข้อบังคับ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('305', $arr_dept[$j], 1, 'หาข้อมูลในเบื้องต้น', 'ใช้ข้อมูลที่มีอยู่ หรือหาจากแหล่งข้อมูลที่มีอยู่แล้ว
		ถามผู้ที่เกี่ยวข้องโดยตรงเพื่อให้ได้ข้อมูล', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('305', $arr_dept[$j], 2, 'แสดงสมรรถนะระดับที่ 1 และสืบเสาะค้นหาข้อมูล', 'สืบเสาะค้นหาข้อมูลด้วยวิธีการที่มากกว่าเพียงการตั้งคำถามพื้นฐาน
		สืบเสาะค้นหาข้อมูลจากผู้ที่ใกล้ชิดกับเหตุการณ์หรือเรื่องราวมากที่สุด', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('305', $arr_dept[$j], 3, 'แสดงสมรรถนะระดับที่ 2 และแสวงหาข้อมูลเชิงลึก', 'ตั้งคำถามเชิงลึกในประเด็นที่เกี่ยวข้องอย่างต่อเนื่องจนได้ที่มาของสถานการณ์ เหตุการณ์ ประเด็นปัญหา หรือค้นพบโอกาสที่จะเป็นประโยชน์ต่อการปฏิบัติงานต่อไป
		แสวงหาข้อมูลด้วยการสอบถามจากผู้รู้อื่นเพิ่มเติม ที่ไม่ได้มีหน้าที่เกี่ยวข้องโดยตรงในเรื่องนั้น', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('305', $arr_dept[$j], 4, 'แสดงสมรรถนะระดับที่ 3 และสืบค้นข้อมูลอย่างเป็นระบบ', 'วางแผนเก็บข้อมูลอย่างเป็นระบบ ในช่วงเวลาที่กำหนด
		สืบค้นข้อมูลจากแหล่งข้อมูลที่แตกต่างจากกรณีปกติธรรมดาโดยทั่วไป 
		ดำเนินการวิจัย หรือมอบหมายให้ผู้อื่นเก็บข้อมูลจากหนังสือพิมพ์ นิตยสาร ระบบสืบค้นโดยอาศัยเทคโนโลยีสารสนเทศ ตลอดจนแหล่งข้อมูลอื่นๆ เพื่อประกอบการทำวิจัย', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('305', $arr_dept[$j], 5, 'แสดงสมรรถนะระดับที่ 4 และวางระบบการสืบค้น เพื่อหาข้อมูลอย่างต่อเนื่อง', 'วางระบบการสืบค้น รวมทั้งการมอบหมายให้ผู้อื่นสืบค้นข้อมูล เพื่อให้ได้ข้อมูลที่ทันเหตุการณ์ อย่างต่อเนื่อง', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('306', $arr_dept[$j], 1, 'เห็นคุณค่าของวัฒนธรรมไทยและให้ความสนใจวัฒนธรรมของผู้อื่น', 'ภาคภูมิใจในวัฒนธรรมของไทย ขณะที่เห็นคุณค่าและสนใจที่จะเรียนรู้วัฒนธรรมของผู้อื่น
		ยอมรับความต่างทางวัฒนธรรม และไม่ดูถูกวัฒนธรรมอื่นว่าด้อยกว่า
		ปรับเปลี่ยนพฤติกรรมให้สอดคล้องกับบริบททางวัฒนธรรมที่เปลี่ยนไป', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('306', $arr_dept[$j], 2, 'แสดงสมรรถนะระดับที่ 1 และเข้าใจ รวมทั้งปรับตัวให้สอดคล้องกับวัฒนธรรมใหม่', 'เข้าใจมารยาท กาลเทศะ ตลอดจนธรรมเนียมปฏิบัติของวัฒนธรรมที่แตกต่าง และพยายามปรับตัวให้สอดคล้อง
		สื่อสารด้วยวิธีการ เนื้อหา และถ้อยคำที่เหมาะสมกับวัฒนธรรมของผู้อื่น', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('306', $arr_dept[$j], 3, 'แสดงสมรรถนะระดับที่ 2 และเข้าใจในวัฒนธรรมต่างๆ อย่างลึกซึ้ง รวมทั้งแสดงออกได้อย่างเหมาะสมกับสถานการณ์', 'เข้าใจบริบท และนัยสำคัญของวัฒนธรรมต่างๆ
		เข้าใจรากฐานทางวัฒนธรรมที่แตกต่างอันจะทำให้เข้าใจวิธีคิดของผู้อื่น
		ไม่ตัดสินผู้อื่นจากความแตกต่างทางวัฒนธรรม แต่ต้องพยายามทำความเข้าใจ เพื่อให้สามารถทำงานร่วมกันได้', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('306', $arr_dept[$j], 4, 'แสดงสมรรถนะระดับที่ 3 และสร้างการยอมรับในความแตกต่างทางวัฒนธรรม', 'สร้างการยอมรับในหมู่ผู้คนต่างวัฒนธรรม เพื่อสัมพันธไมตรีอันดี
		ริเริ่ม และสนับสนุนการทำงานร่วมกัน เพื่อสร้างสัมพันธภาพระหว่างประเทศ หรือระหว่างวัฒนธรรมที่ต่างกัน', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('306', $arr_dept[$j], 5, 'แสดงสมรรถนะระดับที่ 4 และปรับท่าที รวมทั้งวิธีการทำงานให้สอดคล้องกับบริบททางวัฒนธรรม', 'หาทางระงับข้อพิพาทระหว่างวัฒนธรรมที่แตกต่าง โดยพยายามประสานและประนีประนอมด้วยความเข้าใจในแต่ละวัฒนธรรมอย่างลึกซึ้ง
		ปรับเปลี่ยนกลยุทธ์ ท่าที ให้เหมาะสมสอดคล้องกับวัฒนธรรมที่แตกต่าง เพื่อประสานประโยชน์ระหว่างประเทศหรือระหว่างวัฒนธรรมที่ต่างกัน', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('307', $arr_dept[$j], 1, 'เข้าใจความหมายที่ผู้อื่นต้องการสื่อสาร', 'เข้าใจความหมายที่ผู้อื่นต้องการสื่อสาร สามารถจับใจความ สรุปเนื้อหาเรื่องราวได้ถูกต้อง', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('307', $arr_dept[$j], 2, 'แสดงสมรรถนะระดับที่ 1 และเข้าใจอารมณ์ความรู้สึกและคำพูด', 'เข้าใจทั้งความหมายและนัยเชิงอารมณ์ จากการสังเกต สีหน้า ท่าทาง หรือน้ำเสียง ของ ผู้ที่ติดต่อด้วย', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('307', $arr_dept[$j], 3, 'แสดงสมรรถนะระดับที่ 2 และเข้าใจความหมายแฝงในกิริยา ท่าทาง คำพูด หรือน้ำเสียง', 'เข้าใจความหมายที่ไม่ได้แสดงออกอย่างชัดเจนในกิริยา ท่าทาง คำพูด หรือน้ำเสียง
		เข้าใจความคิด ความกังวล หรือความรู้สึกของผู้อื่น แม้จะแสดงออกเพียงเล็กน้อย  
		สามารถระบุลักษณะนิสัยหรือจุดเด่นอย่างใดอย่างหนึ่งของผู้ที่ติดต่อด้วยได้', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('307', $arr_dept[$j], 4, 'แสดงสมรรถนะระดับที่ 3 และเข้าใจการสื่อสารทั้งที่เป็นคำพูด และความหมายแฝงในการสื่อสารกับผู้อื่นได้', 'เข้าใจนัยของพฤติกรรม อารมณ์ และความรู้สึกของผู้อื่น 
		ใช้ความเข้าใจนั้นให้เป็นประโยชน์ในการผูกมิตร ทำความรู้จัก หรือติดต่อประสานงาน', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('307', $arr_dept[$j], 5, 'แสดงสมรรถนะระดับที่ 4 และเข้าใจสาเหตุของพฤติกรรมผู้อื่น', 'เข้าใจถึงสาเหตุของพฤติกรรม หรือปัญหา ตลอดจนที่มาของแรงจูงใจระยะยาวที่ทำให้เกิดพฤติกรรมของผู้อื่น 
		เข้าใจพฤติกรรมของผู้อื่น จนสามารถบอกถึงจุดอ่อน จุดแข็ง และลักษณะนิสัย ของผู้นั้นได้อย่างถูกต้อง', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('308', $arr_dept[$j], 1, 'เข้าใจโครงสร้างองค์กร', 'เข้าใจโครงสร้างองค์กร สายการบังคับบัญชา กฎ ระเบียบ นโยบาย และขั้นตอนการปฏิบัติงาน เพื่อประโยชน์ในการปฏิบัติหน้าที่ราชการได้อย่างถูกต้อง', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('308', $arr_dept[$j], 2, 'แสดงสมรรถนะระดับที่ 1 และเข้าใจความสัมพันธ์เชิงอำนาจที่ไม่เป็นทางการ', 'เข้าใจสัมพันธภาพอย่างไม่เป็นทางการระหว่างบุคคลในองค์กร รับรู้ว่าผู้ใดมีอำนาจตัดสินใจหรือผู้ใดมีอิทธิพลต่อการตัดสินใจในระดับต่างๆ และนำความเข้าใจนี้มาใช้ประโยชน์โดยมุ่งผลสัมฤทธิ์ขององค์กรเป็นสำคัญ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('308', $arr_dept[$j], 3, 'แสดงสมรรถนะระดับที่ 2 และเข้าใจวัฒนธรรมองค์กร', 'เข้าใจประเพณีปฏิบัติ ค่านิยม และวัฒนธรรมของแต่ละองค์กรที่เกี่ยวข้อง รวมทั้งเข้าใจวิธีการสื่อสารให้มีประสิทธิภาพ เพื่อประโยชน์ในการปฏิบัติหน้าที่ราชการ
		เข้าใจข้อจำกัดขององค์กร รู้ว่าสิ่งใดอาจกระทำได้หรือไม่อาจกระทำให้บรรลุผลได้', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('308', $arr_dept[$j], 4, 'แสดงสมรรถนะระดับที่ 3 และเข้าใจความสัมพันธ์ของผู้มีบทบาทสำคัญในองค์กร', 'รับรู้ถึงความสัมพันธ์เชิงอำนาจของผู้มีบทบาทสำคัญในองค์กร เพื่อประโยชน์ในการผลักดันภารกิจตามหน้าที่รับผิดชอบให้เกิดประสิทธิผล', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('308', $arr_dept[$j], 5, 'แสดงสมรรถนะระดับที่ 4 และเข้าใจสาเหตุพื้นฐานของพฤติกรรมองค์กร', 'เข้าใจสาเหตุพื้นฐานของพฤติกรรมองค์กรในหน่วยงานของตนและของภาครัฐโดยรวม ตลอดจนปัญหา และโอกาสที่มีอยู่ และนำความเข้าใจนี้มาขับเคลื่อนการปฏิบัติงานในส่วนที่ตนดูแลรับผิดชอบอยู่อย่างเป็นระบบ
		เข้าใจประเด็นปัญหาทางการเมือง เศรษฐกิจ สังคม ทั้งภายในและภายนอกประเทศที่มีผลกระทบต่อนโยบายภาครัฐและภารกิจขององค์กร เพื่อแปลงวิกฤติเป็นโอกาส กำหนดจุดยืนและท่าทีตามภารกิจในหน้าที่ได้อย่างสอดคล้องเหมาะสมโดยมุ่งประโยชน์ของชาติเป็นสำคัญ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('309', $arr_dept[$j], 1, 'เห็นปัญหาหรือโอกาสระยะสั้นและลงมือดำเนินการ', 'เล็งเห็นปัญหา อุปสรรค และหาวิธีแก้ไขโดยไม่รอช้า 
		เล็งเห็นโอกาสและไม่รีรอที่จะนำโอกาสนั้นมาใช้ประโยชน์ในงาน', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('309', $arr_dept[$j], 2, 'แสดงสมรรถนะระดับที่ 1 และจัดการปัญหาเฉพาะหน้าหรือเหตุวิกฤต', 'ลงมือทันทีเมื่อเกิดปัญหาเฉพาะหน้าหรือในเวลาวิกฤติ โดยอาจไม่มีใครร้องขอ และไม่ย่อท้อ
		แก้ไขปัญหาอย่างเร่งด่วน ในขณะที่คนส่วนใหญ่จะวิเคราะห์สถานการณ์และรอให้ปัญหาคลี่คลายไปเอง', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('309', $arr_dept[$j], 3, 'แสดงสมรรถนะระดับที่ 2 และเตรียมการล่วงหน้า เพื่อสร้างโอกาส หรือหลีกเลี่ยงปัญหาระยะสั้น', 'คาดการณ์และเตรียมการล่วงหน้าเพื่อสร้างโอกาส หรือหลีกเลี่ยงปัญหาที่อาจเกิดขึ้นได้ในระยะสั้น 
		ทดลองใช้วิธีการที่แปลกใหม่ในการแก้ไขปัญหาหรือสร้างสรรค์สิ่งใหม่ให้เกิดขึ้นในวงราชการ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('309', $arr_dept[$j], 4, 'แสดงสมรรถนะระดับที่ 3 และเตรียมการล่วงหน้า เพื่อสร้างโอกาส หรือหลีกเลี่ยงปัญหาที่อาจเกิดขึ้นในระยะปานกลาง', 'คาดการณ์และเตรียมการล่วงหน้าเพื่อสร้างโอกาส หรือหลีกเลี่ยงปัญหาที่อาจเกิดขึ้นได้ในระยะปานกลาง 
		คิดนอกกรอบเพื่อหาวิธีการที่แปลกใหม่และสร้างสรรค์ในการแก้ไขปัญหาที่คาดว่าจะเกิดขึ้นในอนาคต', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('309', $arr_dept[$j], 5, 'แสดงสมรรถนะระดับที่ 4 และเตรียมการล่วงหน้า เพื่อสร้างโอกาส หรือหลีกเลี่ยงปัญหาที่อาจเกิดขึ้นในระยะยาว', 'คาดการณ์และเตรียมการล่วงหน้าเพื่อสร้างโอกาส หรือหลีกเลี่ยงปัญหาที่อาจเกิดขึ้นได้ในอนาคต 
		สร้างบรรยากาศของการคิดริเริ่มให้เกิดขึ้นในหน่วยงานและกระตุ้นให้เพื่อนร่วมงานเสนอความคิดใหม่ๆในการทำงาน เพื่อแก้ปัญหาหรือสร้างโอกาสในระยะยาว', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('310', $arr_dept[$j], 1, 'ต้องการความถูกต้อง ชัดเจนในงาน และรักษากฎ ระเบียบ', 'ต้องการให้ข้อมูล และบทบาทในการปฏิบัติงาน มีความถูกต้อง ชัดเจน  
		ดูแลให้เกิดความเป็นระเบียบในสภาพแวดล้อมของการทำงาน 
		ปฏิบัติตามกฎ ระเบียบ และขั้นตอน ที่กำหนด อย่างเคร่งครัด', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('310', $arr_dept[$j], 2, 'แสดงสมรรถนะระดับที่ 1 และตรวจทานความถูกต้องของงานที่ตนรับผิดชอบ', 'ตรวจทานงานในหน้าที่ ความรับผิดชอบอย่างละเอียด เพื่อความถูกต้อง', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('310', $arr_dept[$j], 3, 'แสดงสมรรถนะระดับที่ 2 และดูแลความถูกต้องของงานทั้งของตนและผู้อื่นที่อยู่ในความรับผิดชอบของตน', 'ตรวจสอบความถูกต้องของงานในหน้าที่ความรับผิดชอบของตนเอง
		ตรวจสอบความถูกต้องงานของผู้อื่น ตามอำนาจหน้าที่ที่กำหนดโดยกฎหมาย กฎ ระเบียบ ข้อบังคับ ที่เกี่ยวข้อง 
		ตรวจความถูกต้องตามขั้นตอนและกระบวนงานทั้งของตนเองและผู้อื่น ตามอำนาจหน้าที่
		บันทึกรายละเอียดของกิจกรรมในงานทั้งของตนเองและของผู้อื่น เพื่อความถูกต้องของงาน', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('310', $arr_dept[$j], 4, 'แสดงสมรรถนะระดับที่ 3 และตรวจสอบความถูกต้องรวมถึงคุณภาพของข้อมูลหรือโครงการ', 'ตรวจสอบรายละเอียดความคืบหน้าของโครงการตามกำหนดเวลา 
		ตรวจสอบความถูกต้อง ครบถ้วน และคุณภาพของข้อมูล 
		สามารถระบุข้อบกพร่องหรือข้อมูลที่หายไป และเพิ่มเติมให้ครบถ้วนเพื่อความถูกต้องของงาน', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('310', $arr_dept[$j], 5, 'แสดงสมรรถนะระดับที่ 4 และพัฒนาระบบการตรวจสอบความถูกต้องของกระบวนงาน', 'พัฒนาระบบการตรวจสอบ เพื่อความถูกต้องตามขั้นตอน และเพิ่มคุณภาพของข้อมูล', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('311', $arr_dept[$j], 1, 'ปฏิบัติงานได้ตามอำนาจหน้าที่โดยไม่ต้องมีการกำกับดูแล', 'ปฏิบัติงานได้โดยอาจไม่ต้องมีการกำกับดูแลใกล้ชิด
		ตัดสินใจเองได้ในภารกิจภายใต้ขอบเขตอำนาจหน้าที่รับผิดชอบของตน', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('311', $arr_dept[$j], 2, 'แสดงสมรรถนะระดับที่ 1 และปฏิบัติงานในหน้าที่อย่างมั่นใจ', 'กล้าตัดสินใจเรื่องที่เห็นว่าถูกต้องแล้วในหน้าที่ แม้จะมีผู้ไม่เห็นด้วยอยู่บ้างก็ตาม
		แสดงออกอย่างมั่นใจในการปฏิบัติหน้าที่แม้อยู่ในสถานการณ์ที่มีความไม่แน่นอน', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('311', $arr_dept[$j], 3, 'แสดงสมรรถนะระดับที่ 2 และมั่นใจในความสามารถของตน', 'เชื่อมั่นในความรู้ความสามารถ และศักยภาพของตนว่าจะสามารถปฏิบัติหน้าที่ให้ประสบผลสำเร็จได้
		แสดงความมั่นใจอย่างเปิดเผยในการตัดสินใจหรือความสามารถของตน', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('311', $arr_dept[$j], 4, 'แสดงสมรรถนะระดับที่ 3 และมั่นใจในการทำงานที่ท้าทาย', 'ชอบงานที่ท้าทายความสามารถ 
		แสดงความคิดเห็นของตนเมื่อไม่เห็นด้วยกับผู้บังคับบัญชา หรือผู้มีอำนาจ หรือในสถานการณ์ที่ขัดแย้ง', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('311', $arr_dept[$j], 5, 'แสดงสมรรถนะระดับที่ 4 และเต็มใจทำงานที่ท้าทายมากและกล้าแสดงจุดยืนของตน', 'เต็มใจและรับอาสาปฏิบัติงานที่ท้าทาย หรือมีความเสี่ยงสูง
		กล้ายืนหยัดเผชิญหน้ากับผู้บังคับบัญชาหรือผู้มีอำนาจ 
		กล้าแสดงจุดยืนของตนอย่างตรงไปตรงมาในประเด็นที่เป็นสาระสำคัญ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('312', $arr_dept[$j], 1, 'มีความคล่องตัวในการปฏิบัติงาน', 'ปรับตัวเข้ากับสภาพการทำงานที่ยากลำบาก หรือไม่เอื้ออำนวยต่อการปฏิบัติงาน', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('312', $arr_dept[$j], 2, 'แสดงสมรรถนะระดับที่ 1 และยอมรับความจำเป็นที่จะต้องปรับเปลี่ยน', 'ยอมรับและเข้าใจความเห็นของผู้อื่น 
		เต็มใจที่จะเปลี่ยนความคิด ทัศนคติ เมื่อได้รับข้อมูลใหม่', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('312', $arr_dept[$j], 3, 'แสดงสมรรถนะระดับที่ 2 และมีวิจารณญานในการปรับใช้กฎระเบียบ', 'มีวิจารณญานในการปรับใช้กฎระเบียบให้เหมาะสมกับสถานการณ์ เพื่อผลสำเร็จของงาน และวัตถุประสงค์ของหน่วยงาน', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('312', $arr_dept[$j], 4, 'แสดงสมรรถนะระดับที่ 3 และปรับเปลี่ยนวิธีการดำเนินงาน', 'ปรับเปลี่ยนวิธีการปฏิบัติงาน ให้เข้ากับสถานการณ์ หรือบุคคลแต่ยังคงเป้าหมายเดิมไว้ 
		ปรับขั้นตอนการทำงาน เพื่อเพิ่มประสิทธิภาพของหน่วยงาน', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('312', $arr_dept[$j], 5, 'แสดงสมรรถนะระดับที่ 4 และปรับเปลี่ยนแผนกลยุทธ์', 'ปรับแผนงาน เป้าหมาย หรือโครงการ เพื่อให้เหมาะสมกับสถานการณ์เฉพาะหน้า 
		ปรับเปลี่ยนโครงสร้าง หรือกระบวนงาน เป็นการเฉพาะกาล เพื่อให้รับกับสถานการณ์เฉพาะหน้า', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('313', $arr_dept[$j], 1, 'นำเสนอข้อมูล หรือความเห็นอย่างตรงไปตรงมา', 'นำเสนอ ข้อมูล หรือความเห็นอย่างตรงไปตรงมา โดยยังมิได้ปรับรูปแบบการนำเสนอตามความสนใจและระดับของผู้ฟัง', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('313', $arr_dept[$j], 2, 'แสดงสมรรถนะระดับที่ 1 และใช้ความพยายามขั้นต้นในการจูงใจ', 'นำเสนอข้อมูล ความเห็น ประเด็น หรือตัวอย่างประกอบที่มีการเตรียมอย่างรอบคอบ เพื่อให้ผู้อื่นเข้าใจ ยอมรับ และสนับสนุนความคิดของตน', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('313', $arr_dept[$j], 3, 'แสดงสมรรถนะระดับที่ 2 และปรับรูปแบบการนำเสนอเพื่อจูงใจ', 'ปรับรูปแบบการนำเสนอให้เหมาะสมกับความสนใจและระดับของผู้ฟัง 
		คาดการณ์ถึงผลของการนำเสนอ และคำนึงถึงภาพลักษณ์ของตนเอง', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('313', $arr_dept[$j], 4, 'แสดงสมรรถนะระดับที่ 3 และใช้ศิลปะการจูงใจ', 'วางแผนการนำเสนอโดยคาดหวังว่าจะสามารถจูงใจให้ผู้อื่นคล้อยตาม
		ปรับแต่ละขั้นตอนของการสื่อสาร นำเสนอ และจูงใจให้เหมาะสมกับผู้ฟังแต่ละกลุ่ม หรือแต่ละราย
		คาดการณ์และพร้อมที่จะรับมือกับปฏิกิริยาทุกรูปแบบของผู้ฟังที่อาจเกิดขึ้น', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('313', $arr_dept[$j], 5, 'แสดงสมรรถนะระดับที่ 4 และใช้กลยุทธ์ซับซ้อนในการจูงใจ', 'แสวงหาผู้สนับสนุน เพื่อเป็นแนวร่วมในการผลักดันแนวคิด แผนงานโครงการ ให้สัมฤทธิ์ผล
		ใช้ความรู้เกี่ยวกับจิตวิทยามวลชน ให้เป็นประโยชน์ในการสื่อสารจูงใจ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('314', $arr_dept[$j], 1, 'ซาบซึ้งในงานศิลปะ', 'เห็นคุณค่าในงานศิลปะของชาติและศิลปะอื่นๆ โดยแสดงความรักและหวงแหนในงานศิลปะ
		สนใจที่จะมีส่วนร่วมในการเรียนรู้  ติดตาม หรือสร้างงานศิลปะแขนงต่างๆ 
		ฝึกฝนเพื่อสร้างความชำนาญในงานศิลปะของตนอย่างสม่ำเสมอ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('314', $arr_dept[$j], 2, 'แสดงสมรรถนะระดับที่ 1 และเข้าใจรูปแบบต่างๆ ของงานศิลปะ', 'แยกแยะความแตกต่างของงานศิลปะรูปแบบต่างๆ และอธิบายให้ผู้อื่นรับรู้ถึงคุณค่าของงานศิลปะเหล่านั้นได้
		เข้าใจรูปแบบและจุดเด่นของงานศิลปะรูปแบบต่างๆ และนำไปใช้ในงานศิลปะของตนได้
		สามารถถ่ายทอดคุณค่าในเชิงศิลปะเพื่อให้เกิดการอนุรักษ์ในวงกว้าง', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('314', $arr_dept[$j], 3, 'แสดงสมรรถนะระดับที่ 2 และประยุกต์ในการสร้างสรรค์งานศิลปะ', 'นำอิทธิพลของงานศิลปะยุคสมัยต่างๆ มาเป็นแรงบันดาลใจในการสร้างสรรค์งานศิลปะของตน
		ประยุกต์ความรู้และประสบการณ์ในงานศิลปะมาใช้ในการสร้างสรรค์งานศิลปะของตน', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('314', $arr_dept[$j], 4, 'แสดงสมรรถนะระดับที่ 3 และสร้างแรงบันดาลใจให้แก่ตนเองและผู้อื่นได้', 'ประยุกต์คุณค่าและลักษณะเด่นของงานศิลปะยุคต่างๆ มาใช้ในการรังสรรค์ผลงาน และเป็นแรงบันดาลใจให้ผู้อื่นเกิดจิตสำนึกในการอนุรักษ์งานศิลปะ
		นำศาสตร์ทางศิลปะหลายแขนงมาผสมผสาน เพื่อสร้างสรรค์ผลงานที่แตกต่าง', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('314', $arr_dept[$j], 5, 'แสดงสมรรถนะระดับที่ 4 และรังสรรค์งานศิลปะที่เป็นเอกลักษณ์เฉพาะตน', 'รังสรรค์งานศิลปะที่มีเอกลักษณ์เฉพาะตนที่เป็นที่ยอมรับ ไม่ว่าจะเป็นการรังสรรค์งานแนวใหม่ หรืออนุรักษ์ไว้ซึ่งงานศิลปะดั้งเดิม', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('315', $arr_dept[$j], 1, 'ปฏิบัติตนเป็นส่วนหนึ่งของส่วนราชการ', 'เคารพและถือปฏิบัติตามแบบแผนและธรรมเนียมปฏิบัติของส่วนราชการ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('315', $arr_dept[$j], 2, 'แสดงสมรรถนะระดับที่ 1 และแสดงความภักดีต่อส่วนราชการ', 'แสดงความพึงพอใจและความภาคภูมิใจที่เป็นส่วนหนึ่งของส่วนราชการ
		มีส่วนสร้างภาพลักษณ์และชื่อเสียงให้แก่ส่วนราชการ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('315', $arr_dept[$j], 3, 'แสดงสมรรถนะระดับที่ 2 และมีส่วนร่วมในการผลักดันพันธกิจของส่วนราชการ', 'มีส่วนร่วมในการสนับสนุนพันธกิจของส่วนราชการจนบรรลุเป้าหมาย
		จัดลำดับความเร่งด่วนหรือความสำคัญของงานเพื่อให้พันธกิจของส่วนราชการบรรลุเป้าหมาย', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('315', $arr_dept[$j], 4, 'แสดงสมรรถนะระดับที่ 3 และยึดถือประโยชน์ของส่วนราชการเป็นที่ตั้ง', 'ยึดถือประโยชน์ของส่วนราชการหรือหน่วยงานเป็นที่ตั้ง ก่อนที่จะคิดถึงประโยชน์ของบุคคลหรือความต้องการของตนเอง
		ยืนหยัดในการตัดสินใจที่เป็นประโยชน์ต่อส่วนราชการ แม้ว่าการตัดสินใจนั้นอาจจะมีผู้ต่อต้านหรือแสดงความไม่เห็นด้วยก็ตาม', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('315', $arr_dept[$j], 5, 'แสดงสมรรถนะระดับที่ 4 และเสียสละเพื่อประโยชน์ของส่วนราชการ', 'เสียสละประโยชน์ระยะสั้นของหน่วยงานที่ตนรับผิดชอบ เพื่อประโยชน์ระยะยาวของส่วนราชการโดยรวม
		เสียสละหรือโน้มน้าวผู้อื่นให้เสียสละประโยชน์ส่วนตนเพื่อประโยชน์ของส่วนราชการ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('316', $arr_dept[$j], 1, 'สร้างหรือรักษาการติดต่อกับผู้ที่ต้องเกี่ยวข้องกับงาน', 'สร้างหรือรักษาการติดต่อกับผู้ที่ต้องเกี่ยวข้องกับงานเพื่อประโยชน์ในงาน', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('316', $arr_dept[$j], 2, 'แสดงสมรรถนะระดับที่ 1 และสร้างหรือรักษาความสัมพันธ์ที่ดีกับผู้ที่ต้องเกี่ยวข้องกับงานอย่างใกล้ชิด', 'สร้างหรือรักษาความสัมพันธ์ที่ดีกับผู้ที่ต้องเกี่ยวข้องกับงานอย่างใกล้ชิด
		เสริมสร้างมิตรภาพกับเพื่อนร่วมงาน ผู้รับบริการ หรือผู้อื่น', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('316', $arr_dept[$j], 3, 'แสดงสมรรถนะระดับที่ 2 และสร้างหรือรักษาการติดต่อสัมพันธ์ทางสังคม', 'ริเริ่มกิจกรรมเพื่อให้มีการติดต่อสัมพันธ์ทางสังคมกับผู้ที่ต้องเกี่ยวข้องกับงาน
		เข้าร่วมกิจกรรมทางสังคมในวงกว้างเพื่อประโยชน์ในงาน', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('316', $arr_dept[$j], 4, 'แสดงสมรรถนะระดับที่ 3 และสร้างหรือรักษาความสัมพันธ์ฉันมิตร', 'สร้างหรือรักษามิตรภาพโดยมีลักษณะเป็นความสัมพันธ์ในทางส่วนตัวมากขึ้น', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('316', $arr_dept[$j], 5, 'แสดงสมรรถนะระดับที่ 4 และรักษาความสัมพันธ์ฉันมิตรในระยะยาว', 'รักษาความสัมพันธ์ฉันมิตรไว้ได้อย่างต่อเนื่อง แม้อาจจะไม่ได้มีการติดต่อสัมพันธ์ในงานกันแล้วก็ตาม แต่ยังอาจมีโอกาสที่จะติดต่อสัมพันธ์ในงานได้อีกในอนาคต', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				if ($BKK_FLAG==1) {
					$level = array(	"O1", "O2", "O3", "O4", "K1", "K2", "K3", "K4", "K5", "D1", "D2", "M1", "M2" );
					$target = array(	1, 1, 2, 3, 1, 2, 3, 3, 3, 3, 3, 3, 3 );
				} else {
					$level = array(	"O1", "O2", "O3", "O4", "K1", "K2", "K3", "K4", "K5", "D1", "D2", "M1", "M2", "E1", "E2", "E3", "E4", "E5", "E7", "S1", "S2", "S3" );
					$target = array(	1, 1, 2, 3, 1, 2, 3, 4, 5, 3, 4, 4, 5, 1, 1, 2, 3, 4, 1, 3, 4, 5 );
				}
				for ( $i=0; $i<count($level); $i++ ) { 
					$cmd = " SELECT CP_CODE FROM PER_COMPETENCE WHERE DEPARTMENT_ID = $arr_dept[$j] AND CP_MODEL in (1,3) ";
					$db_dpis2->send_cmd($cmd);
					//$db_dpis2->show_error();
					while($data2 = $db_dpis2->get_array()){
						$CP_CODE = trim($data2[CP_CODE]);
						if ($level[$i]=="O1" && substr($CP_CODE,0,1)=="3") $TARGET_LEVEL = 0; else $TARGET_LEVEL = $target[$i]; 
						$cmd = " INSERT INTO PER_STANDARD_COMPETENCE (LEVEL_NO, CP_CODE, DEPARTMENT_ID, TARGET_LEVEL, UPDATE_USER, 
										UPDATE_DATE)
										VALUES ('$level[$i]', '$CP_CODE', $arr_dept[$j], $TARGET_LEVEL,  $SESS_USERID, '$UPDATE_DATE') ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
					} // end while
				} // end for
				
				if ($BKK_FLAG!=1) {
					$level = array(	"D1", "D2", "M1", "M2" );
					$target = array(	1, 2, 3, 4 );
					for ( $i=0; $i<count($level); $i++ ) { 
						$cmd = " SELECT CP_CODE FROM PER_COMPETENCE WHERE DEPARTMENT_ID = $arr_dept[$j] AND CP_MODEL = 2 ";
						$db_dpis2->send_cmd($cmd);
						//$db_dpis2->show_error();
						while($data2 = $db_dpis2->get_array()){
							$CP_CODE = trim($data2[CP_CODE]);
							$cmd = " INSERT INTO PER_STANDARD_COMPETENCE (LEVEL_NO, CP_CODE, DEPARTMENT_ID, TARGET_LEVEL, UPDATE_USER, 
											UPDATE_DATE)
											VALUES ('$level[$i]', '$CP_CODE', $arr_dept[$j], $target[$i],  $SESS_USERID, '$UPDATE_DATE') ";
							$db_dpis->send_cmd($cmd);
							//$db_dpis->show_error();
						} // end while
					} // end for
				}	// end if	
			} // end for
		} // end while
	}

	if( (!$UPD && !$DEL && !$err_text) ){
		$CP_CODE = "";
		$CP_NAME = "";
		$CP_ENG_NAME = "";
		$CP_MEANING = "";
		$CP_MODEL = 1;
		$CP_ASSESSMENT = 'Y';
                $DEF_WEIGHT="";
		$CP_ACTIVE = 1;
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
	} // end if
?>