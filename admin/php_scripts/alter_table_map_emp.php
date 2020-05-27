<?
/*		$cmd = " SELECT EN_CODE, EN_NAME, EN_SHORTNAME FROM PER_EDUCNAME ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		while($data = $db_dpis->get_array()){
			$EN_CODE = trim($data[EN_CODE]);
			$EN_NAME = trim($data[EN_NAME]);
			$EN_SHORTNAME = trim($data[EN_SHORTNAME]);
			$EN_NAME = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($EN_NAME)));
			$EN_SHORTNAME = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($EN_SHORTNAME)));
			$cmd = " UPDATE PER_EDUCNAME SET EN_NAME = '$EN_NAME', EN_SHORTNAME = '$EN_SHORTNAME' WHERE EN_CODE = '$EN_CODE') ";
			$db_dpis1->send_cmd($cmd);
			//$db_dpis1->show_error();
		} // end while						*/

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 
						  WHERE LINKTO_WEB = 'data_map_emp_check.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 3, 'P1403 ตรวจสอบการจัดระบบตำแหน่งลูกจ้างประจำ', 'S', 'W', 'data_map_emp_check.html', 0, 35, 308, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
							  CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 3, 'P1403 ตรวจสอบการจัดระบบตำแหน่งลูกจ้างประจำ', 'S', 'W', 'data_map_emp_check.html', 0, 35, 308, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 
						  WHERE LINKTO_WEB = 'data_map_emp_comdtl.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 4, 'P1404 บัญชีแนบท้ายคำสั่งจัดระบบตำแหน่งลูกจ้างประจำ', 'S', 'W', 'data_map_emp_comdtl.html', 0, 35,
							  308, $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
							  CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 4, 'P1404 บัญชีแนบท้ายคำสั่งจัดระบบตำแหน่งลูกจ้างประจำ', 'S', 'W', 'data_map_emp_comdtl.html', 0, 35,
							  308, $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

			$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  LEVEL_NAME, PER_TYPE, LEVEL_SHORTNAME, LEVEL_SEQ_NO)
							  VALUES ('บ1', 1, $SESS_USERID, '$UPDATE_DATE', 'ระดับ บ1',2, 'บ1',11) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  LEVEL_NAME, PER_TYPE, LEVEL_SHORTNAME, LEVEL_SEQ_NO)
							  VALUES ('บ2', 1, $SESS_USERID, '$UPDATE_DATE', 'ระดับ บ2',2, 'บ2',12) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  LEVEL_NAME, PER_TYPE, LEVEL_SHORTNAME, LEVEL_SEQ_NO)
							  VALUES ('บ3', 1, $SESS_USERID, '$UPDATE_DATE', 'ระดับ บ2/หัวหน้า',2, 'บ2/หน.',13) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  LEVEL_NAME, PER_TYPE, LEVEL_SHORTNAME, LEVEL_SEQ_NO)
							  VALUES ('ส1', 1, $SESS_USERID, '$UPDATE_DATE', 'ระดับ ส1',2, 'ส1',21) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  LEVEL_NAME, PER_TYPE, LEVEL_SHORTNAME, LEVEL_SEQ_NO)
							  VALUES ('ส2', 1, $SESS_USERID, '$UPDATE_DATE', 'ระดับ ส2',2, 'ส2',22) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  LEVEL_NAME, PER_TYPE, LEVEL_SHORTNAME, LEVEL_SEQ_NO)
							  VALUES ('ส3', 1, $SESS_USERID, '$UPDATE_DATE', 'ระดับ ส2/หัวหน้า',2, 'ส2/หน.',23) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  LEVEL_NAME, PER_TYPE, LEVEL_SHORTNAME, LEVEL_SEQ_NO)
							  VALUES ('ส4', 1, $SESS_USERID, '$UPDATE_DATE', 'ระดับ ส3',2, 'ส3',24) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  LEVEL_NAME, PER_TYPE, LEVEL_SHORTNAME, LEVEL_SEQ_NO)
							  VALUES ('ส5', 1, $SESS_USERID, '$UPDATE_DATE', 'ระดับ ส3/หัวหน้า',2, 'ส3/หน.',25) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  LEVEL_NAME, PER_TYPE, LEVEL_SHORTNAME, LEVEL_SEQ_NO)
							  VALUES ('ส6', 1, $SESS_USERID, '$UPDATE_DATE', 'ระดับ ส4',2, 'ส4',26) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  LEVEL_NAME, PER_TYPE, LEVEL_SHORTNAME, LEVEL_SEQ_NO)
							  VALUES ('ส7', 1, $SESS_USERID, '$UPDATE_DATE', 'ระดับ ส4/หัวหน้า',2, 'ส4/หน.',27) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  LEVEL_NAME, PER_TYPE, LEVEL_SHORTNAME, LEVEL_SEQ_NO)
							  VALUES ('ช1', 1, $SESS_USERID, '$UPDATE_DATE', 'ระดับ ช1',2, 'ช1',31) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  LEVEL_NAME, PER_TYPE, LEVEL_SHORTNAME, LEVEL_SEQ_NO)
							  VALUES ('ช2', 1, $SESS_USERID, '$UPDATE_DATE', 'ระดับ ช2',2, 'ช2',32) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  LEVEL_NAME, PER_TYPE, LEVEL_SHORTNAME, LEVEL_SEQ_NO)
							  VALUES ('ช3', 1, $SESS_USERID, '$UPDATE_DATE', 'ระดับ ช2/หัวหน้า',2, 'ช2/หน.',33) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  LEVEL_NAME, PER_TYPE, LEVEL_SHORTNAME, LEVEL_SEQ_NO)
							  VALUES ('ช4', 1, $SESS_USERID, '$UPDATE_DATE', 'ระดับ ช3',2, 'ช3',34) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  LEVEL_NAME, PER_TYPE, LEVEL_SHORTNAME, LEVEL_SEQ_NO)
							  VALUES ('ช5', 1, $SESS_USERID, '$UPDATE_DATE', 'ระดับ ช3/หัวหน้า',2, 'ช3/หน.',35) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  LEVEL_NAME, PER_TYPE, LEVEL_SHORTNAME, LEVEL_SEQ_NO)
							  VALUES ('ช6', 1, $SESS_USERID, '$UPDATE_DATE', 'ระดับ ช4',2, 'ช4',36) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  LEVEL_NAME, PER_TYPE, LEVEL_SHORTNAME, LEVEL_SEQ_NO)
							  VALUES ('ช7', 1, $SESS_USERID, '$UPDATE_DATE', 'ระดับ ช4/หัวหน้า',2, 'ช4/หน.',37) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  LEVEL_NAME, PER_TYPE, LEVEL_SHORTNAME, LEVEL_SEQ_NO)
							  VALUES ('ท1', 1, $SESS_USERID, '$UPDATE_DATE', 'ระดับ ท1',2, 'ท1',41) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  LEVEL_NAME, PER_TYPE, LEVEL_SHORTNAME, LEVEL_SEQ_NO)
							  VALUES ('ท2', 1, $SESS_USERID, '$UPDATE_DATE', 'ระดับ ท2',2, 'ท2',42) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  LEVEL_NAME, PER_TYPE, LEVEL_SHORTNAME, LEVEL_SEQ_NO)
							  VALUES ('ท3', 1, $SESS_USERID, '$UPDATE_DATE', 'ระดับ ท2/หัวหน้า',2, 'ท2/หน.',43) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  LEVEL_NAME, PER_TYPE, LEVEL_SHORTNAME, LEVEL_SEQ_NO)
							  VALUES ('ท4', 1, $SESS_USERID, '$UPDATE_DATE', 'ระดับ ท3',2, 'ท3',44) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  LEVEL_NAME, PER_TYPE, LEVEL_SHORTNAME, LEVEL_SEQ_NO)
							  VALUES ('ท5', 1, $SESS_USERID, '$UPDATE_DATE', 'ระดับ ท3/หัวหน้า',2, 'ท3/หน.',45) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_GROUP (PG_CODE, PG_NAME, PG_ACTIVE, UPDATE_USER, UPDATE_DATE, PG_SEQ_NO)
							  VALUES ('1000', 'กลุ่มงานบริการพื้นฐาน', 1, $SESS_USERID, '$UPDATE_DATE', 10) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_POS_GROUP (PG_CODE, PG_NAME, PG_ACTIVE, UPDATE_USER, UPDATE_DATE, PG_SEQ_NO)
							  VALUES ('2000', 'กลุ่มงานสนับสนุน', 1, $SESS_USERID, '$UPDATE_DATE', 20) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_POS_GROUP (PG_CODE, PG_NAME, PG_ACTIVE, UPDATE_USER, UPDATE_DATE, PG_SEQ_NO)
							  VALUES ('3000', 'กลุ่มงานช่าง', 1, $SESS_USERID, '$UPDATE_DATE', 30) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_POS_GROUP (PG_CODE, PG_NAME, PG_ACTIVE, UPDATE_USER, UPDATE_DATE, PG_SEQ_NO)
							  VALUES ('4000', 'กลุ่มงานเทคนิคพิเศษ', 1, $SESS_USERID, '$UPDATE_DATE', 40) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_POS_NAME ADD PN_CODE_NEW VARCHAR(10) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POS_NAME ADD PN_CODE_NEW VARCHAR2(10) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_POS_NAME ADD LEVEL_NO VARCHAR(10) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POS_NAME ADD LEVEL_NO VARCHAR2(10) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '1101', LEVEL_NO = 'บ1'  WHERE PN_CODE = '100003' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '1101', LEVEL_NO = 'บ3'  WHERE PN_CODE = '200135' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '1102', LEVEL_NO = 'บ1' WHERE PN_CODE = '100014' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '1102', LEVEL_NO = 'บ3'  WHERE PN_CODE = '200137' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '1103', LEVEL_NO = 'บ1' WHERE PN_CODE = '100015' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '1104', LEVEL_NO = 'บ1' WHERE PN_CODE = '300296' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '1105', LEVEL_NO = 'บ1' WHERE PN_CODE = '100043' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '1105', LEVEL_NO = 'บ3' WHERE PN_CODE = '200141' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '1106', LEVEL_NO = 'บ3' WHERE PN_CODE in ('200142','200143') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '1107', LEVEL_NO = 'บ3' WHERE PN_CODE = '300316' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '1108', LEVEL_NO = 'บ1' WHERE PN_CODE = '200087' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '1109', LEVEL_NO = 'บ1' WHERE PN_CODE = '100019' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '1109', LEVEL_NO = 'บ3' WHERE PN_CODE = '410464' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '1110', LEVEL_NO = 'บ1' WHERE PN_CODE = '200114' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '1110', LEVEL_NO = 'บ3' WHERE PN_CODE = '300310' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '1111', LEVEL_NO = 'บ1' WHERE PN_CODE = '100026' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '1112', LEVEL_NO = 'บ1' WHERE PN_CODE = '200082' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '1112', LEVEL_NO = 'บ2' WHERE PN_CODE = '300257' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '1113', LEVEL_NO = 'บ1' WHERE PN_CODE in ('100009','100033') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '1114', LEVEL_NO = 'บ1' WHERE PN_CODE in ('100044','100048') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '1115', LEVEL_NO = 'บ1' WHERE PN_CODE in ('100006','100023','100024','100025') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '1115', LEVEL_NO = 'บ2' WHERE PN_CODE = '200104' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '1116', LEVEL_NO = 'บ1' WHERE PN_CODE = '100016' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '1116', LEVEL_NO = 'บ3' WHERE PN_CODE = '200138' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '1117', LEVEL_NO = 'บ1' WHERE PN_CODE = '200110' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '1117', LEVEL_NO = 'บ3' WHERE PN_CODE = '300308' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '1118', LEVEL_NO = 'บ1' WHERE PN_CODE = '200122' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '1118', LEVEL_NO = 'บ3' WHERE PN_CODE = '300312' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '1119', LEVEL_NO = 'บ1' WHERE PN_CODE in ('200109','200121') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '1201', LEVEL_NO = 'บ1'  WHERE PN_CODE in ('100050','100065') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '1202', LEVEL_NO = 'บ1' WHERE PN_CODE = '100055' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '1203', LEVEL_NO = 'บ1' WHERE PN_CODE = '100054' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '1203', LEVEL_NO = 'บ3' WHERE PN_CODE = '200178' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '1204', LEVEL_NO = 'บ1' WHERE PN_CODE = '100057' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '1205', LEVEL_NO = 'บ1' WHERE PN_CODE in ('100058','100061','100068') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '1206', LEVEL_NO = 'บ1' WHERE PN_CODE in ('100059','100060','100067') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '1207', LEVEL_NO = 'บ1' WHERE PN_CODE in ('100027','100066') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '1207', LEVEL_NO = 'บ2' WHERE PN_CODE = '200172' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '1301', LEVEL_NO = 'บ1' WHERE PN_CODE in ('100001','100010','100035','100038') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '1301', LEVEL_NO = 'บ3'  WHERE PN_CODE = '200136' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '1302', LEVEL_NO = 'บ1' WHERE PN_CODE = '300302' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '1303', LEVEL_NO = 'บ1' WHERE PN_CODE = '100039' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '1304', LEVEL_NO = 'บ1' WHERE PN_CODE = '200125' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '1305', LEVEL_NO = 'บ1' WHERE PN_CODE = '100040' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '1401', LEVEL_NO = 'บ1'  WHERE PN_CODE = '300266' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '1402', LEVEL_NO = 'บ1' WHERE PN_CODE = '200095' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '1403', LEVEL_NO = 'บ1' WHERE PN_CODE = '300273' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '1404', LEVEL_NO = 'บ2' WHERE PN_CODE = '300275' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '1405', LEVEL_NO = 'บ1' WHERE PN_CODE in ('200123','200124','200228') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '1405', LEVEL_NO = 'บ2' WHERE PN_CODE in ('300290','300402') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '1405', LEVEL_NO = 'บ3' WHERE PN_CODE = '300313' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '1406', LEVEL_NO = 'บ1' WHERE PN_CODE = '200232' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '1407', LEVEL_NO = 'บ1' WHERE PN_CODE in ('200073','200074','200107') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '1408', LEVEL_NO = 'บ1' WHERE PN_CODE in ('100020','100030','100037') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '1408', LEVEL_NO = 'บ2' WHERE PN_CODE in ('200093','200094','200076','200077','200106') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '1409', LEVEL_NO = 'บ1' WHERE PN_CODE = '200111' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '1409', LEVEL_NO = 'บ3' WHERE PN_CODE = '300309' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '1410', LEVEL_NO = 'บ1' WHERE PN_CODE in ('200088','200089') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '1411', LEVEL_NO = 'บ1' WHERE PN_CODE in ('200102','200103') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '1412', LEVEL_NO = 'บ1' WHERE PN_CODE in ('200097','200115','200130') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '1413', LEVEL_NO = 'บ1' WHERE PN_CODE = '300281' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '1414', LEVEL_NO = 'บ1' WHERE PN_CODE = '300273' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '1501', LEVEL_NO = 'บ1'  WHERE PN_CODE = '200098' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '1502', LEVEL_NO = 'บ1' WHERE PN_CODE = '200112' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '1503', LEVEL_NO = 'บ1' WHERE PN_CODE = '100021' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '1504', LEVEL_NO = 'บ1' WHERE PN_CODE = '' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '1505', LEVEL_NO = 'บ1' WHERE PN_CODE = '' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '1506', LEVEL_NO = 'บ1' WHERE PN_CODE = '200113' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '1507', LEVEL_NO = 'บ1' WHERE PN_CODE in ('200175','200176') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '1508', LEVEL_NO = 'บ1' WHERE PN_CODE = '200174' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '1509', LEVEL_NO = 'บ1' WHERE PN_CODE = '' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '1510', LEVEL_NO = 'บ1' WHERE PN_CODE = '100045' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '1511', LEVEL_NO = 'บ1' WHERE PN_CODE = '200071' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '1511', LEVEL_NO = 'บ3' WHERE PN_CODE = '300306' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2101', LEVEL_NO = 'ส1' WHERE PN_NAME in ('พนักงานบัญชี ชั้น 1','พนักงานการเงิน') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2101', LEVEL_NO = 'ส2' WHERE PN_NAME in ('พนักงานบัญชี ชั้น 2','พัฒนากรพลังงาน (บัญชี)') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2101', LEVEL_NO = 'ส4' WHERE PN_NAME in ('พนักงานบัญชี ชั้น 3','พนักงานการเงิน ชั้น 3') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2101', LEVEL_NO = 'ส6' WHERE PN_NAME in ('พนักงานบัญชี ชั้น 4','พนักงานการเงิน ชั้น 4') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2108', LEVEL_NO = 'ส1' WHERE PN_CODE in ('300451','300274') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2108', LEVEL_NO = 'ส2' WHERE PN_CODE = '410541' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2108', LEVEL_NO = 'ส4' WHERE PN_CODE = '420594' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2109', LEVEL_NO = 'ส1' WHERE PN_CODE = '300279' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2110', LEVEL_NO = 'ส1' WHERE PN_CODE = '300292' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2111', LEVEL_NO = 'ส1' WHERE PN_CODE in ('200118','200119') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2111', LEVEL_NO = 'ส2' WHERE PN_CODE = '300287' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2111', LEVEL_NO = 'ส4' WHERE PN_CODE = '410459' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2111', LEVEL_NO = 'ส5' WHERE PN_CODE = '300311' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2113', LEVEL_NO = 'ส1' WHERE PN_CODE = '200117' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2113', LEVEL_NO = 'ส2' WHERE PN_CODE = '300286' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2113', LEVEL_NO = 'ส4' WHERE PN_CODE = '410458' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2113', LEVEL_NO = 'ส6' WHERE PN_CODE = '420543' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2205', LEVEL_NO = 'ส1' WHERE PN_CODE = '200083' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2302', LEVEL_NO = 'ส1' WHERE PN_CODE = '300289' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2303', LEVEL_NO = 'ส1' WHERE PN_CODE = '300260' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2304', LEVEL_NO = 'ส1' WHERE PN_CODE = '410454' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2305', LEVEL_NO = 'ส1' WHERE PN_CODE = '300291' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2306', LEVEL_NO = 'ส1' WHERE PN_CODE in ('300271','300246') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2307', LEVEL_NO = 'ส1' WHERE PN_CODE = '300261' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2308', LEVEL_NO = 'ส1' WHERE PN_CODE = '410456' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2308', LEVEL_NO = 'ส3' WHERE PN_CODE = '420546' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2309', LEVEL_NO = 'ส1' WHERE PN_CODE = '300449' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2309', LEVEL_NO = 'ส2' WHERE PN_CODE = '420588' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2310', LEVEL_NO = 'ส1' WHERE PN_CODE = '300283' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2311', LEVEL_NO = 'ส1' WHERE PN_CODE in ('300280','300297') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2312', LEVEL_NO = 'ส1' WHERE PN_CODE = '300300' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2312', LEVEL_NO = 'ส2' WHERE PN_CODE = '410461' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2315', LEVEL_NO = 'ส1' WHERE PN_CODE in ('420580','420575') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2316', LEVEL_NO = 'ส1' WHERE PN_CODE = '300441' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2317', LEVEL_NO = 'ส1' WHERE PN_CODE = '300440' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2318', LEVEL_NO = 'ส1' WHERE PN_CODE in ('200131','200132') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2319', LEVEL_NO = 'ส1' WHERE PN_CODE = '410531' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2320', LEVEL_NO = 'ส1' WHERE PN_CODE = '420579' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2322', LEVEL_NO = 'ส1' WHERE PN_CODE = '300252' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2401', LEVEL_NO = 'ส1' WHERE PN_CODE in ('200153','200162') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2403', LEVEL_NO = 'ส1' WHERE PN_CODE = '300319' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2409', LEVEL_NO = 'ส1' WHERE PN_CODE in ('200170','200158') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2411', LEVEL_NO = 'ส1' WHERE PN_CODE = '300318' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2413', LEVEL_NO = 'ส1' WHERE PN_CODE in ('200148','200171') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2415', LEVEL_NO = 'ส1' WHERE PN_CODE = '200169' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2416', LEVEL_NO = 'ส1' WHERE PN_CODE = '200168' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2417', LEVEL_NO = 'ส1' WHERE PN_CODE = '300324' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2418', LEVEL_NO = 'ส1' WHERE PN_CODE in ('200161','200166') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2419', LEVEL_NO = 'ส1' WHERE PN_CODE = '200156' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2422', LEVEL_NO = 'ส1' WHERE PN_CODE = '300327' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2423', LEVEL_NO = 'ส1' WHERE PN_CODE in ('200164','200167') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2423', LEVEL_NO = 'ส3' WHERE PN_CODE = '300329' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2424', LEVEL_NO = 'ส1' WHERE PN_CODE = '300325' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2425', LEVEL_NO = 'ส1' WHERE PN_CODE = '300323' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2426', LEVEL_NO = 'ส1' WHERE PN_CODE = '200149' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2427', LEVEL_NO = 'ส1' WHERE PN_CODE = '200165' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2429', LEVEL_NO = 'ส1' WHERE PN_CODE = '200147' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2430', LEVEL_NO = 'ส1' WHERE PN_CODE = '300320' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2432', LEVEL_NO = 'ส1' WHERE PN_CODE = '420589' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2501', LEVEL_NO = 'ส1' WHERE PN_CODE = '410468' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2502', LEVEL_NO = 'ส1' WHERE PN_CODE = '200173' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2502', LEVEL_NO = 'ส2' WHERE PN_CODE = '300331' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2502', LEVEL_NO = 'ส4' WHERE PN_CODE = '410469' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2503', LEVEL_NO = 'ส1' WHERE PN_CODE in ('100053','100036') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2504', LEVEL_NO = 'ส1' WHERE PN_NAME in ('พนักงานเคหกิจเกษตร ชั้น 1','พนักงานเคหกิจเกษตร ชั้น 1') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2504', LEVEL_NO = 'ส2' WHERE PN_NAME in ('พนักงานเคหกิจเกษตร ชั้น 2','พนักงานเคหกิจเกษตร ชั้น 2') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2504', LEVEL_NO = 'ส4' WHERE PN_NAME in ('พนักงานเคหกิจเกษตร ชั้น 3','พนักงานเคหกิจเกษตร ชั้น 3') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2508', LEVEL_NO = 'ส1' WHERE PN_CODE = '300284' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2509', LEVEL_NO = 'ส1' WHERE PN_CODE = '300326' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2509', LEVEL_NO = 'ส2' WHERE PN_CODE = '410470' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2510', LEVEL_NO = 'ส1' WHERE PN_CODE in ('100011','100049','200157') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2511', LEVEL_NO = 'ส1' WHERE PN_CODE = '300328' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2514', LEVEL_NO = 'ส1' WHERE PN_CODE = '410540' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2516', LEVEL_NO = 'ส2' WHERE PN_CODE = '300278' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2517', LEVEL_NO = 'ส1' WHERE PN_CODE = '300330' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2518', LEVEL_NO = 'ส1' WHERE PN_CODE = '300334' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2518', LEVEL_NO = 'ส2' WHERE PN_CODE = '410471' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2519', LEVEL_NO = 'ส1' WHERE PN_CODE = '300333' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2601', LEVEL_NO = 'ส1' WHERE PN_CODE = '200084' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2602', LEVEL_NO = 'ส1' WHERE PN_CODE = '300304' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2603', LEVEL_NO = 'ส1' WHERE PN_CODE = '200134' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2603', LEVEL_NO = 'ส2' WHERE PN_CODE = '300303' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2603', LEVEL_NO = 'ส4' WHERE PN_CODE = '410463' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2603', LEVEL_NO = 'ส6' WHERE PN_CODE = '420545' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2604', LEVEL_NO = 'ส1' WHERE PN_CODE in ('200078','200079','300255','200081') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2604', LEVEL_NO = 'ส2' WHERE PN_CODE in ('300253','300256') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2604', LEVEL_NO = 'ส4' WHERE PN_CODE in ('410451','410453','200243') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2605', LEVEL_NO = 'ส1' WHERE PN_CODE = '200080' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2605', LEVEL_NO = 'ส2' WHERE PN_CODE = '300254' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2605', LEVEL_NO = 'ส4' WHERE PN_CODE in ('300254','410452') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2608', LEVEL_NO = 'ส1' WHERE PN_CODE = '200129' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2702', LEVEL_NO = 'ส1' WHERE PN_CODE = '200101' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2703', LEVEL_NO = 'ส1' WHERE PN_CODE = '300244' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2704', LEVEL_NO = 'ส1' WHERE PN_CODE = '300285' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2705', LEVEL_NO = 'ส1' WHERE PN_CODE = '200100' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2705', LEVEL_NO = 'ส3' WHERE PN_CODE = '300307' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2706', LEVEL_NO = 'ส1' WHERE PN_CODE = '300243' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2707', LEVEL_NO = 'ส1' WHERE PN_CODE = '300293' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2707', LEVEL_NO = 'ส3' WHERE PN_CODE = '410465' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2708', LEVEL_NO = 'ส1' WHERE PN_CODE = '300295' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2709', LEVEL_NO = 'ส1' WHERE PN_CODE = '300242' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2803', LEVEL_NO = 'ส1' WHERE PN_CODE = '410457' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2803', LEVEL_NO = 'ส3' WHERE PN_CODE = '420547' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2805', LEVEL_NO = 'ส1' WHERE PN_CODE = '300289' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2806', LEVEL_NO = 'ส1' WHERE PN_CODE = '200105' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2902', LEVEL_NO = 'ส1' WHERE PN_CODE = '200127' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2902', LEVEL_NO = 'ส3' WHERE PN_CODE = '300315' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2904', LEVEL_NO = 'ส1' WHERE PN_CODE in ('100032','100042','100031') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2904', LEVEL_NO = 'ส3' WHERE PN_CODE = '200140' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2907', LEVEL_NO = 'ส1' WHERE PN_CODE = '300265' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2908', LEVEL_NO = 'ส1' WHERE PN_CODE = '200085' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2909', LEVEL_NO = 'ส1' WHERE PN_CODE = '300294' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2910', LEVEL_NO = 'ส1' WHERE PN_CODE = '420541' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2911', LEVEL_NO = 'ส3' WHERE PN_CODE = '300258' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2912', LEVEL_NO = 'ส1' WHERE PN_CODE = '200086' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2913', LEVEL_NO = 'ส1' WHERE PN_CODE = '300263' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2913', LEVEL_NO = 'ส3' WHERE PN_CODE = '410466' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2914', LEVEL_NO = 'ส1' WHERE PN_CODE = '300264' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2915', LEVEL_NO = 'ส1' WHERE PN_CODE = '100002' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2915', LEVEL_NO = 'ส3' WHERE PN_CODE = '300305' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '2916', LEVEL_NO = 'ส1' WHERE PN_CODE = '200116' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3101', LEVEL_NO = 'ช1' WHERE PN_NAME = 'ช่างเขียน ชั้น 1' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3101', LEVEL_NO = 'ช2' WHERE PN_NAME = 'ช่างเขียน ชั้น 2' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3101', LEVEL_NO = 'ช4' WHERE PN_NAME = 'ช่างเขียน ชั้น 3' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3101', LEVEL_NO = 'ช6' WHERE PN_NAME = 'ช่างเขียน ชั้น 4' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3102', LEVEL_NO = 'ช1' WHERE PN_NAME = 'ช่างเขียนแบบ ชั้น 1' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3102', LEVEL_NO = 'ช2' WHERE PN_NAME in ('ช่างเขียนแบบ ชั้น 2','พนักงานเขียนแบบ') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3102', LEVEL_NO = 'ช4' WHERE PN_NAME = 'ช่างเขียนแบบ ชั้น 3' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3103', LEVEL_NO = 'ช1' WHERE PN_CODE = '200212' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3104', LEVEL_NO = 'ช1' WHERE PN_CODE = '300335' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3104', LEVEL_NO = 'ช3' WHERE PN_CODE = '410520' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3105', LEVEL_NO = 'ช1' WHERE PN_CODE = '410474' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3106', LEVEL_NO = 'ช1' WHERE PN_CODE = '200075' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3107', LEVEL_NO = 'ช1' WHERE PN_CODE = '300268' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3202', LEVEL_NO = 'ช1' WHERE PN_CODE = '500621' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3204', LEVEL_NO = 'ช1' WHERE PN_CODE = '300392' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3204', LEVEL_NO = 'ช3' WHERE PN_CODE = '420568' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3205', LEVEL_NO = 'ช1' WHERE PN_CODE = '200209' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3205', LEVEL_NO = 'ช2' WHERE PN_CODE = '300404' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3207', LEVEL_NO = 'ช2' WHERE PN_CODE = '300403' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3303', LEVEL_NO = 'ช1' WHERE PN_CODE = '500611' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3304', LEVEL_NO = 'ช1' WHERE PN_CODE = '200225' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3305', LEVEL_NO = 'ช1' WHERE PN_CODE = '200206' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3305', LEVEL_NO = 'ช2' WHERE PN_CODE in ('300393','300398','300395','300397','300394') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3305', LEVEL_NO = 'ช4' WHERE PN_CODE = '410505' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3305', LEVEL_NO = 'ช6' WHERE PN_CODE = '420556' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3306', LEVEL_NO = 'ช1' WHERE PN_CODE = '200199' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3306', LEVEL_NO = 'ช2' WHERE PN_CODE in ('300381','300382') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3306', LEVEL_NO = 'ช4' WHERE PN_CODE = '410499' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3306', LEVEL_NO = 'ช6' WHERE PN_CODE = '420552' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3307', LEVEL_NO = 'ช1' WHERE PN_CODE = '200204' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3307', LEVEL_NO = 'ช2' WHERE PN_CODE = '300390' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3307', LEVEL_NO = 'ช4' WHERE PN_CODE = '410504' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3307', LEVEL_NO = 'ช6' WHERE PN_CODE = '420555' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3308', LEVEL_NO = 'ช1' WHERE PN_CODE = '200210' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3308', LEVEL_NO = 'ช2' WHERE PN_CODE in ('300406','300407') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3308', LEVEL_NO = 'ช4' WHERE PN_CODE = '410509' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3308', LEVEL_NO = 'ช6' WHERE PN_CODE = '420557' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3309', LEVEL_NO = 'ช1' WHERE PN_CODE = '300344' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3311', LEVEL_NO = 'ช1' WHERE PN_CODE = '300356' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3312', LEVEL_NO = 'ช5' WHERE PN_CODE = '410518' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3313', LEVEL_NO = 'ช1' WHERE PN_CODE = '200234' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3313', LEVEL_NO = 'ช2' WHERE PN_CODE = '300436' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3314', LEVEL_NO = 'ช2' WHERE PN_CODE = '410476' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3314', LEVEL_NO = 'ช7' WHERE PN_CODE = '420560' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3316', LEVEL_NO = 'ช1' WHERE PN_CODE = '200233' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3316', LEVEL_NO = 'ช2' WHERE PN_CODE = '300435' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3318', LEVEL_NO = 'ช1' WHERE PN_CODE = '200180' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3318', LEVEL_NO = 'ช2' WHERE PN_CODE = '300340' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3318', LEVEL_NO = 'ช4' WHERE PN_CODE = '410477' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3319', LEVEL_NO = 'ช1' WHERE PN_CODE = '300437' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3321', LEVEL_NO = 'ช1' WHERE PN_CODE = '300351' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3322', LEVEL_NO = 'ช1' WHERE PN_CODE = '200203' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3322', LEVEL_NO = 'ช2' WHERE PN_CODE in ('300388','300389') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3322', LEVEL_NO = 'ช4' WHERE PN_CODE = '410503' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3322', LEVEL_NO = 'ช6' WHERE PN_CODE = '420553' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3322', LEVEL_NO = 'ช7' WHERE PN_CODE = '430596' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3324', LEVEL_NO = 'ช1' WHERE PN_CODE = '200211' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3324', LEVEL_NO = 'ช2' WHERE PN_CODE in ('300251','300421') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3324', LEVEL_NO = 'ช4' WHERE PN_CODE = '410511' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3325', LEVEL_NO = 'ช1' WHERE PN_CODE = '300410' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3325', LEVEL_NO = 'ช2' WHERE PN_CODE = '410510' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3325', LEVEL_NO = 'ช5' WHERE PN_CODE = '420569' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3326', LEVEL_NO = 'ช1' WHERE PN_CODE = '200202' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3326', LEVEL_NO = 'ช2' WHERE PN_CODE in ('300387','500617') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3326', LEVEL_NO = 'ช4' WHERE PN_CODE = '410502' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3327', LEVEL_NO = 'ช1' WHERE PN_CODE = '300349' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3328', LEVEL_NO = 'ช1' WHERE PN_CODE = '200195' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3328', LEVEL_NO = 'ช2' WHERE PN_CODE = '300369' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3328', LEVEL_NO = 'ช4' WHERE PN_CODE = '410493' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3328', LEVEL_NO = 'ช5' WHERE PN_CODE = '420567' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3329', LEVEL_NO = 'ช1' WHERE PN_CODE = '200194' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3329', LEVEL_NO = 'ช2' WHERE PN_CODE = '300367' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3329', LEVEL_NO = 'ช4' WHERE PN_CODE = '410492' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3329', LEVEL_NO = 'ช5' WHERE PN_CODE = '420566' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3330', LEVEL_NO = 'ช1' WHERE PN_CODE = '200191' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3330', LEVEL_NO = 'ช2' WHERE PN_CODE = '300360' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3330', LEVEL_NO = 'ช4' WHERE PN_CODE = '410487' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3330', LEVEL_NO = 'ช5' WHERE PN_CODE = '420562' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3331', LEVEL_NO = 'ช1' WHERE PN_CODE = '300366' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3331', LEVEL_NO = 'ช2' WHERE PN_CODE = '410491' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3331', LEVEL_NO = 'ช5' WHERE PN_CODE = '420565' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3332', LEVEL_NO = 'ช1' WHERE PN_CODE = '200190' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3332', LEVEL_NO = 'ช2' WHERE PN_CODE = '300354' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3332', LEVEL_NO = 'ช4' WHERE PN_CODE = '410486' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3332', LEVEL_NO = 'ช6' WHERE PN_CODE = '420550' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3333', LEVEL_NO = 'ช1' WHERE PN_CODE = '200189' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3333', LEVEL_NO = 'ช2' WHERE PN_CODE in ('300370','300352','300376') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3333', LEVEL_NO = 'ช4' WHERE PN_CODE in ('410485','410496') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3401', LEVEL_NO = 'ช1' WHERE PN_CODE = '200226' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3402', LEVEL_NO = 'ช1' WHERE PN_CODE = '300396' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3403', LEVEL_NO = 'ช1' WHERE PN_CODE = '200215' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3404', LEVEL_NO = 'ช1' WHERE PN_CODE = '300358' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3404', LEVEL_NO = 'ช3' WHERE PN_CODE = '410524' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3405', LEVEL_NO = 'ช1' WHERE PN_CODE = '410532' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3406', LEVEL_NO = 'ช1' WHERE PN_CODE in ('200184','200185','200186','200181') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3406', LEVEL_NO = 'ช2' WHERE PN_CODE = '300343' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3406', LEVEL_NO = 'ช4' WHERE PN_CODE = '410480' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3407', LEVEL_NO = 'ช1' WHERE PN_CODE = '200183' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3407', LEVEL_NO = 'ช2' WHERE PN_CODE = '300342' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3408', LEVEL_NO = 'ช1' WHERE PN_CODE = '200182' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3408', LEVEL_NO = 'ช2' WHERE PN_CODE = '300341' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3408', LEVEL_NO = 'ช4' WHERE PN_CODE = '410478' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3410', LEVEL_NO = 'ช1' WHERE PN_CODE = '200133' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3410', LEVEL_NO = 'ช2' WHERE PN_CODE = '300301' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3410', LEVEL_NO = 'ช4' WHERE PN_CODE = '410462' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3410', LEVEL_NO = 'ช6' WHERE PN_CODE = '420544' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3411', LEVEL_NO = 'ช2' WHERE PN_CODE = '300269' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3412', LEVEL_NO = 'ช1' WHERE PN_CODE = '200092' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3412', LEVEL_NO = 'ช3' WHERE PN_CODE = '410527' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3413', LEVEL_NO = 'ช1' WHERE PN_CODE = '300420' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3414', LEVEL_NO = 'ช1' WHERE PN_CODE = '410515' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3415', LEVEL_NO = 'ช1' WHERE PN_CODE = '300277' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3501', LEVEL_NO = 'ช1' 
							  WHERE PN_CODE in ('300427','300430','300423','300426','300428','300429','300424','300425','300431','300432') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3502', LEVEL_NO = 'ช1' WHERE PN_CODE = '300422' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3503', LEVEL_NO = 'ช1' WHERE PN_CODE in ('300433','410512') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3504', LEVEL_NO = 'ช1' WHERE PN_CODE = '420559' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3505', LEVEL_NO = 'ช1' WHERE PN_CODE = '300433' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3506', LEVEL_NO = 'ช1' WHERE PN_CODE = '410514' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3602', LEVEL_NO = 'ช1' WHERE PN_CODE = '300348' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3606', LEVEL_NO = 'ช1' WHERE PN_CODE = '200099' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3608', LEVEL_NO = 'ช1' WHERE PN_CODE = '200213' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3608', LEVEL_NO = 'ช2' WHERE PN_CODE in ('300417','300418') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3609', LEVEL_NO = 'ช2' WHERE PN_CODE = '300345' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3609', LEVEL_NO = 'ช5' WHERE PN_CODE = '410521' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3610', LEVEL_NO = 'ช1' WHERE PN_CODE = '200188' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3610', LEVEL_NO = 'ช2' WHERE PN_CODE = '300347' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3610', LEVEL_NO = 'ช4' WHERE PN_CODE = '410484' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3610', LEVEL_NO = 'ช6' WHERE PN_CODE = '420548' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3610', LEVEL_NO = 'ช7' WHERE PN_CODE = '420561' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3611', LEVEL_NO = 'ช1' WHERE PN_CODE = '300401' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3611', LEVEL_NO = 'ช2' WHERE PN_CODE = '410508' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3612', LEVEL_NO = 'ช1' WHERE PN_CODE in ('300363','300357','200242') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3612', LEVEL_NO = 'ช3' WHERE PN_CODE = '410523' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3614', LEVEL_NO = 'ช7' WHERE PN_CODE = '420558' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3703', LEVEL_NO = 'ช1' WHERE PN_CODE = '200196' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3703', LEVEL_NO = 'ช2' WHERE PN_CODE = '300372' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3703', LEVEL_NO = 'ช4' WHERE PN_CODE = '410495' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3703', LEVEL_NO = 'ช6' WHERE PN_CODE = '420551' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3704', LEVEL_NO = 'ช1' WHERE PN_CODE = '300272' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3705', LEVEL_NO = 'ช1' WHERE PN_CODE = '200222' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3706', LEVEL_NO = 'ช1' WHERE PN_CODE = '200198' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3706', LEVEL_NO = 'ช2' WHERE PN_CODE in ('300378','300380') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3706', LEVEL_NO = 'ช4' WHERE PN_CODE in ('410497','410498') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3707', LEVEL_NO = 'ช1' WHERE PN_CODE = '300368' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3708', LEVEL_NO = 'ช1' WHERE PN_CODE = '300350' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3709', LEVEL_NO = 'ช1' WHERE PN_CODE = '410488' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3710', LEVEL_NO = 'ช1' WHERE PN_CODE = '410494' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3711', LEVEL_NO = 'ช1' WHERE PN_CODE = '200193' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3711', LEVEL_NO = 'ช2' WHERE PN_CODE = '300364' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3711', LEVEL_NO = 'ช4' WHERE PN_CODE = '410490' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3711', LEVEL_NO = 'ช5' WHERE PN_CODE = '420563' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3712', LEVEL_NO = 'ช1' 
							  WHERE PN_CODE in ('200218','200223','200229','200216','200227','200224','200231') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3713', LEVEL_NO = 'ช1' WHERE PN_CODE in ('300405','300399','300413','300391') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3713', LEVEL_NO = 'ช3' WHERE PN_CODE = '410522' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3714', LEVEL_NO = 'ช1' WHERE PN_CODE = '200192' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3714', LEVEL_NO = 'ช2' WHERE PN_CODE in ('300362','300385','300386') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3714', LEVEL_NO = 'ช4' WHERE PN_CODE in ('410489','410501') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3715', LEVEL_NO = 'ช1' WHERE PN_CODE = '300365' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3715', LEVEL_NO = 'ช3' WHERE PN_CODE = '420563' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3716', LEVEL_NO = 'ช1' WHERE PN_CODE in ('410507','410506') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3716', LEVEL_NO = 'ช3' WHERE PN_CODE = '420590' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3717', LEVEL_NO = 'ช1' WHERE PN_CODE = '300374' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3718', LEVEL_NO = 'ช1' WHERE PN_CODE in ('200207','200154','200155','200163') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3719', LEVEL_NO = 'ช1' WHERE PN_CODE = '300248' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3720', LEVEL_NO = 'ช1' WHERE PN_CODE = '200205' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3721', LEVEL_NO = 'ช1' WHERE PN_CODE = '500616' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3722', LEVEL_NO = 'ช1' WHERE PN_CODE = '300355' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3723', LEVEL_NO = 'ช1' WHERE PN_CODE = '300339' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3724', LEVEL_NO = 'ช1' WHERE PN_CODE = '200179' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3724', LEVEL_NO = 'ช2' WHERE PN_CODE = '300337' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3724', LEVEL_NO = 'ช4' WHERE PN_CODE = '410475' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3725', LEVEL_NO = 'ช1' WHERE PN_CODE in ('300409','300400') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3726', LEVEL_NO = 'ช1' WHERE PN_CODE = '300250' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3727', LEVEL_NO = 'ช1' WHERE PN_CODE = '300359' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3728', LEVEL_NO = 'ช1' WHERE PN_CODE = '410517' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3801', LEVEL_NO = 'ช1' WHERE PN_CODE = '200241' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3801', LEVEL_NO = 'ช2' WHERE PN_CODE = '300439' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3801', LEVEL_NO = 'ช4' WHERE PN_CODE = '410530' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3802', LEVEL_NO = 'ช1' WHERE PN_CODE = '410529' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3802', LEVEL_NO = 'ช2' WHERE PN_CODE = '420570' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3802', LEVEL_NO = 'ช4' WHERE PN_CODE in ('430594','300446') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3803', LEVEL_NO = 'ช1' WHERE PN_CODE in ('300443','300444') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3806', LEVEL_NO = 'ช1' WHERE PN_CODE = '420582' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3807', LEVEL_NO = 'ช1' WHERE PN_CODE = '410537' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3808', LEVEL_NO = 'ช1' WHERE PN_CODE in ('410539','410534','410535','410538') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3808', LEVEL_NO = 'ช3' WHERE PN_CODE = '420591' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3809', LEVEL_NO = 'ช1' WHERE PN_CODE in ('420587','420586') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3810', LEVEL_NO = 'ช1' WHERE PN_CODE in ('420583','420574') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3811', LEVEL_NO = 'ช1' WHERE PN_CODE = '420584' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3812', LEVEL_NO = 'ช1' WHERE PN_CODE = '420585' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3814', LEVEL_NO = 'ช1' WHERE PN_CODE = '420578' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3902', LEVEL_NO = 'ช1' WHERE PN_CODE = '100051' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3904', LEVEL_NO = 'ช1' WHERE PN_CODE = '100052' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3905', LEVEL_NO = 'ช1' WHERE PN_CODE = '100064' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3906', LEVEL_NO = 'ช1' WHERE PN_CODE = '200146' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3907', LEVEL_NO = 'ช1' WHERE PN_CODE in ('300377','300375') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3908', LEVEL_NO = 'ช1' WHERE PN_CODE = '410460' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3909', LEVEL_NO = 'ช1' WHERE PN_CODE = '200200' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3909', LEVEL_NO = 'ช2' WHERE PN_CODE in ('300383','300384') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3909', LEVEL_NO = 'ช4' WHERE PN_CODE = '410500' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3910', LEVEL_NO = 'ช1' WHERE PN_CODE = '200144' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3910', LEVEL_NO = 'ช2' WHERE PN_CODE = '300317' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3910', LEVEL_NO = 'ช4' WHERE PN_CODE = '410467' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3911', LEVEL_NO = 'ช1' WHERE PN_CODE = '300270' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3912', LEVEL_NO = 'ช1' 
							  WHERE PN_CODE in ('200235','200239','200238','200214','200237','200236','200220','200219') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '3912', LEVEL_NO = 'ช2' WHERE PN_CODE = '300419' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '4101', LEVEL_NO = 'ท1' WHERE PN_CODE in ('430597','430598') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '4201', LEVEL_NO = 'ท1' WHERE PN_CODE = '430601' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '4301', LEVEL_NO = 'ท1' WHERE PN_CODE = '500619' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '4302', LEVEL_NO = 'ท2' WHERE PN_CODE = '500621' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '4303', LEVEL_NO = 'ท5' WHERE PN_CODE = '500623' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '4304', LEVEL_NO = 'ท1' WHERE PN_CODE = '430599' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '4305', LEVEL_NO = 'ท2' WHERE PN_CODE = '430595' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '4305', LEVEL_NO = 'ท4' WHERE PN_CODE = '440605' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '4305', LEVEL_NO = 'ท5' WHERE PN_CODE = '440607' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '4306', LEVEL_NO = 'ท4' WHERE PN_CODE = '440606' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '4307', LEVEL_NO = 'ท4' WHERE PN_CODE = '440603' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '4307', LEVEL_NO = 'ท5' WHERE PN_CODE = '440608' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PN_CODE_NEW = '4308', LEVEL_NO = 'ท5' WHERE PN_CODE = '440610' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_POS_NAME DROP CONSTRAINT INXU1_PER_POS_NAME ";
			elseif($DPISDB=="oci8")
				$cmd = " DROP INDEX INXU1_PER_POS_NAME ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('1101', 'พนักงานทั่วไป', '1000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 1101) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('1102', 'คนสวน', '1000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 1102) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('1103', 'พนักงานสถานที่', '1000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 1103) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('1104', 'แม่บ้าน', '1000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 1104) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('1105', 'พนักงานรักษาความปลอดภัย', '1000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 1105) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('1106', 'ผู้ดูแลหมวดสถานที่', '1000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 1106) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('1107', 'ผู้ดูแลหมวดนอกโรงงาน', '1000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 1107) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('1108', 'ผู้ดูแลสวนสมเด็จพระศรีนครินทร์', '1000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 1108) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('1109', 'ผู้รักษาราชอุทยาน', '1000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 1109) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('1110', 'พนักงานประจำพิพิธภัณฑ์', '1000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 1110) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('1111', 'พนักงานดูแลโบราณสถาน', '1000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 1111) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('1112', 'นายประตูกษาปณ์', '1000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 1112) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('1113', 'พนักงานประจำตึก', '1000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 1113) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('1114', 'พนักงานเปล', '1000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 1114) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('1115', 'พนักงานซักฟอก', '1000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 1115) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('1116', 'บริกร', '1000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 1116) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('1117', 'พนักงานบริการ', '1000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 1117) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('1118', 'พนักงานรับรอง', '1000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 1118) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('1119', 'พนักงานรับโทรศัพท์', '1000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 1119) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('1201', 'พนักงานเกษตรพื้นฐาน', '1000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 1201) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('1202', 'พนักงานปราบศัตรูพืช', '1000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 1202) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('1203', 'พนักงานประมงพื้นฐาน', '1000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 1203) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('1204', 'พนักงานรีดนมโค', '1000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 1204) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('1205', 'พนักงานเลี้ยงผึ้งและไหม', '1000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 1205) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('1206', 'พนักงานเลี้ยงสัตว์', '1000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 1206) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('1207', 'พนักงานชลประทาน', '1000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 1207) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('1301', 'กะลาสี', '1000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 1301) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('1302', 'สรั่งปากเรือ', '1000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 1302) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('1303', 'พนักงานเรือกล', '1000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 1303) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('1304', 'พนักงานเรือยนต์', '1000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 1304) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('1305', 'พนักงานเรือตรวจ', '1000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 1305) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('1401', 'พนักงานเขียนโฉนด', '1000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 1401) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('1402', 'พนักงานเขียนใบอนุญาต', '1000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 1402) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('1403', 'พนักงานเดินหมาย', '1000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 1403) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('1404', 'พนักงานตรวจปรู๊ฟ', '1000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 1404) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('1405', 'พนักงานโรงพิมพ์', '1000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 1405) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('1406', 'ผู้ช่วยพนักงานแยกสี', '1000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 1406) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('1407', 'พนักงานแท่นพิมพ์และตัดกระดาษ', '1000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 1407) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('1408', 'พนักงานเข้าเล่ม', '1000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 1408) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('1409', 'พนักงานบริการเอกสารทั่วไป', '1000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 1409) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('1410', 'พนักงานเก็บเอกสาร', '1000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 1410) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('1411', 'พนักงานซ่อมเอกสาร', '1000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 1411) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('1412', 'พนักงานจัดเก็บแผนที่', '1000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 1412) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('1413', 'พนักงานบริการสื่ออุปกรณ์การสอน', '1000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 1413) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('1414', 'พนักงานเดินหมาย', '1000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 1414) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('1501', 'พนักงานจัดรถเข้าชั่ง', '1000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 1501) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('1502', 'พนักงานบัตรยานพาหนะ', '1000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 1502) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('1503', 'พนักงานจำหน่ายบัตร', '1000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 1503) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('1504', 'พนักงานเก็บเงิน', '1000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 1504) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('1505', 'พนักงานโสตทัศนศึกษา', '1000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 1505) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('1506', 'พนักงานบันทึกเสียง', '1000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 1506) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('1507', 'พนักงานวัดระดับน้ำ', '1000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 1507) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('1508', 'พนักงานประตูน้ำ', '1000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 1508) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('1509', 'พนักงานผลิตน้ำประปา', '1000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 1509) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('1510', 'พนักงานกลั่นน้ำ', '1000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 1510) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('1511', 'ควาญช้าง', '1000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 1511) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2101', 'พนักงานการเงินและบัญชี', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2101) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2102', 'พนักงานวิเคราะห์ราคางาน', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2102) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2103', 'พนักงานการภาษี', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2103) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2104', 'พนักงานตรวจสอบและเร่งรัดภาษี', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2104) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2105', 'พนักงานขาย', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2105) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2106', 'พนักงานพัสดุ', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2106) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2107', 'พนักงานแสตมป์', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2107) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2108', 'พนักงานธุรการ', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2108) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2109', 'พนักงานธุรการการบิน', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2109) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2110', 'พนักงานออกของ', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2110) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2111', 'พนักงานพิมพ์แบบ', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2111) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2112', 'พนักงานพิมพ์ออฟเซท', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2112) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2113', 'พนักงานพิมพ์', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2113) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2114', 'พนักงานลิขิต', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2114) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2201', 'พนักงานรหัส', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2201) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2202', 'พนักงานจัดทำข้อมูลประมวลผล', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2202) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2203', 'พนักงานประเมินผล', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2203) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2204', 'พนักงานสถิติ', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' ,2204) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2205', 'พนักงานทะเบียนตำบล', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2205) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2206', 'พนักงานสำรวจ', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2206) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2207', 'พนักงานสมุทรศาสตร์', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2207) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2208', 'พนักงานวิจัย', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2208) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2209', 'พนักงานการศึกษา', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2209) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2210', 'พนักงานห้องสมุด', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2210) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2301', 'พนักงานสื่อสาร', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2301) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2302', 'พนักงานวิทยุ', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2302) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2303', 'พนักงานกระจายเสียงภาษาท้องถิ่น', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2303) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2304', 'พนักงานกระจายเสียงภาษาต่างประเทศ', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2304) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2305', 'พนักงานหนังสือพิมพ์จีน', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2305) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2306', 'พนักงานฉายภาพยนต์', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2306) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2307', 'พนักงานขยายเสียง', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2307) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2308', 'พนักงานนำชม', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2308) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2309', 'พนักงานแปล', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2309) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2310', 'พนักงานแปลอักษรโบราณ', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2310) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2311', 'ล่ามภาษาต่างประเทศ', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2311) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2312', 'ล่ามภาษาจีน', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2312) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2313', 'ครูสอนดนตรี', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2313) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2314', 'ครูสอนศิลปพื้นเมือง', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2314) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2315', 'ครูสอนภาษาจีน', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2315) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2316', 'ครูสอนชาวเขา', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2316) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2317', 'ครูพี่เลี้ยง', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2317) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2318', 'พี่เลี้ยง', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2318) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2319', 'ครูสอนศาสนาอิสลาม', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2319) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2320', 'ผู้ชำนาญการอิสลาม', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2320) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2321', 'นักร้อง', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2321) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2322', 'นักดนตรี', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2322) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2323', 'นาฏศิลปิน', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2323) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2324', 'ครูช่วยสอน', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2324) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2325', 'ผู้สอนวิชาชีพ', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2325) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2326', 'ผู้สอนการถนอมอาหาร', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2326) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2327', 'ผู้สอนคนพิการ', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2327) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2401', 'พนักงานช่วยการพยาบาล', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2401) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2402', 'พนักงานผู้ช่วยเหลือแพทย์และพยาบาล', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2402) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2403', 'พนักงานช่วยเหลือคนไข้', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2403) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2404', 'ผู้ช่วยพยาบาล', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2404) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2405', 'พยาบาล', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2405) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2406', 'ผู้ช่วยทันตแพทย์', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2406) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2407', 'ผู้ช่วยเภสัชกร', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2407) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2408', 'พนักงานเภสัชกรรม', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2408) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2409', 'พนักงานประจำห้องยา', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2409) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2410', 'ผู้ช่วยพนักงานสุขศึกษา', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2410) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2411', 'ผู้ช่วยเจ้าหน้าที่อนามัย', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2411) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2412', 'ผู้ช่วยเจ้าหน้าที่สาธารณสุข', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2412) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2413', 'พนักงานการแพทย์และรังสีเทคนิค', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2413) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2414', 'พนักงานจุลทัศนกร', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2414) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2415', 'พนักงานห้องเฝือก', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2415) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2416', 'พนักงานห้องผ่าตัด', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2416) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2417', 'พนักงานปฏิบัติการชันสูตรโรค', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2417) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2418', 'พนักงานผ่าและรักษาศพ', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2418) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2419', 'พนักงานประจำห้อง', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2419) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2420', 'พนักงานหอผู้ป่วย', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2420) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2421', 'พนักงานบัตรรายงานโรค', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2421) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2422', 'พนักงานสุขภาพชุมชน', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2422) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2423', 'พนักงานเยี่ยมบ้าน', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2423) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2424', 'พนักงานปฏิบัติการทดลองพาหะนำโรค', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2424) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2425', 'พนักงานปฏิบัติการควบคุมพาหะนำโรค', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2425) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2426', 'พนักงานปราบแมลง', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2426) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2427', 'พนักงานระบาดวิทยา', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2427) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2428', 'พนักงานบำบัดโรคเรื้อน', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2428) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2429', 'ผู้ช่วยนักกายภาพบำบัด', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2429) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2430', 'พนักงานกายภาพบำบัด', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2430) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2431', 'พนักงานอาชีวบำบัด', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2431) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2432', 'พนักงานเวชศาสตร์การบิน', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2432) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2433', 'โภชนากร', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2433) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2434', 'แพทย์ประจำบ้าน', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2434) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2501', 'พนักงานตรวจจำแนกพันธุ์ยาง', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2501) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2502', 'พนักงานตรวจสอบข้าว', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2502) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2503', 'พนักงานทดลองเกษตร', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2503) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2504', 'พนักงานเคหกิจเกษตร', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2504) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2505', 'พนักงานการเกษตร', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2505) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2506', 'พนักงานการประมง', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2506) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2507', 'พนักงานส่งน้ำ', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2507) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2508', 'ผู้ช่วยพนักงานผลิตทดลอง', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2508) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2509', 'พนักงานผลิตทดลอง', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2509) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2510', 'พนักงานประจำห้องทดลอง', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2510) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2511', 'พนักงานห้องปฏิบัติการ', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2511) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2512', 'พนักงานปฏิบัติการทดลอง', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2512) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2513', 'พนักงานวิทยาศาสตร์', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2513) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2514', 'พนักงานตรวจสอบร่องน้ำ', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2514) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2515', 'พนักงานอุทกวิทยา', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2515) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2516', 'พนักงานทดสอบดิน', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2516) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2517', 'พนักงานเรือนรุกขรังสี', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2517) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2518', 'พนักงานสวนป่า', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2518) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2519', 'พนักงานพิทักษ์ป่า', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2519) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2520', 'พนักงานส่งเสริมพลังงาน', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2520) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2521', 'พนักงานพัฒนาพลังงาน', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2521) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2522', 'นักพัฒนาพลังงาน', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2522) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2523', 'พนักงานเทคนิคอุตสาหกรรม', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2523) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2524', 'นักเทคนิคอุตสาหกรรม', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2524) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2601', 'ผู้ช่วยสหโภชน์', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2601) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2602', 'สหโภชน์', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2602) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2603', 'สรั่งเรือ', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2603) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2604', 'นายท้ายเรือ', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2604) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2605', 'นายท้ายเรือกลชายทะเล', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2605) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2606', 'นายท้ายเรือกลเดินทะเลเฉพาะเขต', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2606) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2607', 'นายเรือเรือกลเดินทะเลเฉพาะเขต', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2607) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2608', 'พนักงานอู่เรือ', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2608) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2701', 'พนักงานประกอบเครื่องเสวย', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2701) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2702', 'พนักงานเชิญเครื่องเสวย', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2702) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2703', 'เจ้าหน้าที่ราชูปโภค', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2703) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2704', 'พนักงานพระภูษา', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2704) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2705', 'พนักงานชาวที่', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2705) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2706', 'เจ้าหน้าที่พระราชพิธี', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2706) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2707', 'พราหมณ์', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2707) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2708', 'มหาดเล็ก', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2708) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2709', 'เจ้าหน้าที่ตำรวจวัง', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2709) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2801', 'พนักงานปราบปรามทางน้ำ', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2801) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2802', 'พนักงานควบคุม', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2802) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2803', 'พนักงานพินิจ', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2803) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2804', 'พนักงานอาณาบาล', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2804) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2805', 'พนักงานสืบราชการลับ', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2805) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2806', 'พนักงานดับเพลิง', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2806) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2807', 'พนักงานกู้ภัย', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2807) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2808', 'เจ้าหน้าที่ตรวจอาวุธและวัตถุอันตราย', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2808) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2901', 'พนักงานการเมือง', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2901) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2902', 'พนักงานเหรียญกษาปณ์', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2902) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2903', 'พนักงานพัฒนาชนบท', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2903) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2904', 'พนักงานบริการน้ำมันและหล่อลื่น', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2904) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2905', 'พนักงานจัดหาที่ดิน', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2905) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2906', 'ผู้ช่วยพนักงานที่ราชพัสดุ', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2906) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2907', 'พนักงานขุดแต่งโบราณสถาน', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2907) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2908', 'พนักงานดูแลผู้รับการสงเคราะห์', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2908) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2909', 'พ่อบ้าน-แม่บ้าน', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2909) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2910', 'พนักงานจัดการสโมสร', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2910) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2911', 'พนักงานจัดการหอพัก', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2911) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2912', 'ผู้ดูแลสนามบิน', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2912) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2913', 'พนักงานขับรถยนต์', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2913) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2914', 'พนักงานขับรถโดยสาร', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2914) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2915', 'พนักงานประกอบอาหาร', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2915) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('2916', 'พนักงานพิธีสงฆ์', '2000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 2916) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3101', 'ช่างเขียน', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3101) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3102', 'ช่างเขียนแบบ', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3102) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3103', 'ผู้ช่วยช่างเขียนแผนที่', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3103) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3104', 'ช่างเขียนแผนที่', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3104) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3105', 'ช่างเขียนแผนที่ด้วยเครื่องเขียนแผนที่ภาพถ่ายทางอากาศ', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3105) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3106', 'ช่างพิมพ์แผ่นที่', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3106) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3107', 'พนักงานคัดลอกแบบ', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3107) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3201', 'ช่างไฟฟ้า', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3201) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3202', 'ช่างอิเลคทรอนิคส์', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3202) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3203', 'ช่างไฟฟ้าและอิเลคทรอนิคส์', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3203) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3204', 'ช่างมาตรวัดไฟฟ้า', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3204) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3205', 'ช่างสายไฟฟ้า', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3205) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3206', 'ช่างสื่อสาร', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3206) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3207', 'ช่างวิทยุ', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3207) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3208', 'พนักงานวิทยุและทัศนสัญญาณ', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3208) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3301', 'ช่างสำรวจ', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3301) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3302', 'ช่างรังวัด', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3302) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3303', 'ช่างก่อสร้าง', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3303) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3304', 'ผู้ช่วยช่างไม้', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3304) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3305', 'ช่างไม้', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3305) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3306', 'ช่างปูน', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3306) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3307', 'ช่างฝีมือสนาม', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3307) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3308', 'ช่างสี', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3308) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3309', 'ช่างเคาะพ่นสี', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3309) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3310', 'ช่างผลิตภัณฑ์ซีเมนต์', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3310) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3311', 'ช่างตกแต่งสถานที่', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3311) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3312', 'ผู้ดูแลช่างก่อสร้างและตกแต่ง', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3312) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3313', 'พนักงานบำรุงทาง', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3313) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3314', 'ช่างเครื่องมือกล', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3314) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3315', 'ช่างเครื่องจักรกล', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3315) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3316', 'พนักงานเครื่องจักรกล', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3316) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3317', 'พนักงานคุมเครื่องยนต์และจักรกล', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3317) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3318', 'ช่างเครื่องยนต์', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3318) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3319', 'ลูกมือช่างเครื่องบิน', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3319) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3320', 'ช่างกลึง', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3320) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3321', 'ช่างเชื่อม', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3321) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3322', 'ช่างฝีมือโรงงาน', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3322) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3323', 'ช่างกลโรงงาน', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3323) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3324', 'ช่างเหล็ก', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3324) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3325', 'ช่างหล่อ', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3325) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3326', 'ช่างฝีมืองานโลหะ', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3326) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3327', 'ช่างชุบเคลือบผิวทางเคมี', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3327) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3328', 'ช่างทำรูปลอยตัว', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3328) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3329', 'ช่างทำตัวเหรียญกษาปณ์', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3329) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3330', 'ช่างตีตราเหรียญกษาปณ์', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3330) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3331', 'ช่างทำเครื่องมือ', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3331) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3332', 'ช่างปรับซ่อมเครื่องมือสำรวจ', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3332) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3333', 'ช่างซ่อมบำรุง', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3333) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3334', 'ช่างจัดสถานที่พิธีการ', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3334) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3335', 'ช่างฝีมือทั่วไป', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3335) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3401', 'ผู้ช่วยช่างไม้ขยายแบบ', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3401) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3402', 'ช่างไม้ขยายแบบ', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3402) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3403', 'ผู้ช่วยช่างต่อเรือเหล็ก', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3403) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3404', 'ช่างต่อเรือเหล็ก', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3404) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3405', 'ผู้ช่วยผู้เชี่ยวชาญการต่อเรือเหล็ก', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3405) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3406', 'ช่างเครื่องเรือ', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3406) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3407', 'ช่างเครื่องเรือกลลำน้ำ', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3407) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3408', 'ช่างเครื่องเรือกลชายทะเล', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3408) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3409', 'ช่างเครื่องเรือกลเดินทะเลเฉพาะเขต', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3409) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3410', 'สรั่งช่างกล', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3410) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3411', 'พนักงานคานเรือ', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3411) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3412', 'พนักงานขุด', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3412) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3413', 'ผู้ช่วยพนักงานควบคุมเรือขุด', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3413) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3414', 'พนักงานควบคุมเรือขุด', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3414) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3415', 'พนักงานทดลองอุปกรณ์เรือ', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3415) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3501', 'พนักงานขับรถงานเกษตรและก่อสร้าง', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3501) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3502', 'พนักงานขับเครื่องจักรกลขนาดเบา', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3502) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3503', 'พนักงานขับเครื่องจักรกลขนาดกลาง', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3503) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3504', 'พนักงานขับเครื่องจักรกลขนาดหนัก', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3504) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3505', 'พนักงานควบคุมเครื่องจักรกลขนาดเบา', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3505) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3506', 'พนักงานควบคุมเครื่องจักรกลขนาดหนัก', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3506) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3601', 'ช่างอุตสาหกรรม', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3601) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3602', 'ช่างเจาะระเบิด', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3602) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3603', 'ช่างเหมืองแร่', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3603) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3604', 'ช่างแต่งแร่', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3604) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3605', 'ช่างเจาะตรวจลองแร่', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3605) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3606', 'พนักงานช่างเจาะทางธรณีวิทยา', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3606) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3607', 'พนักงานช่วยเจาะและระบายน้ำ', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3607) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3608', 'ผู้ช่วยช่างเจาะ', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3608) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3609', 'ช่างเจาะสำรวจ', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3609) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3610', 'ช่างเจาะบ่อบาดาล', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3610) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3611', 'ช่างระบบน้ำ', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3611) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3612', 'ช่างต่อท่อ', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3612) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3613', 'ช่างประปา', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3613) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3614', 'ผู้ควบคุมหน่วยเจาะ', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3614) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3701', 'ช่างศิลป์', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3701) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3702', 'ช่างวิจิตรศิลป์', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3702) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3703', 'ช่างประณีตศิลป์', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3703) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3704', 'ช่างซ่อมเอกสารและศิลปวัตถุ', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3704) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3705', 'ผู้ช่วยช่างปั้น', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3705) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3706', 'ช่างปั้น', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3706) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3707', 'ช่างทำแบบเครื่องปั้นดินเผา', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3707) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3708', 'ช่างชุบเคลือบผิวทางเครื่องปั้นดินเผา', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3708) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3709', 'ช่างเตาเผา', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3709) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3710', 'ช่างทำหีบดิน', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3710) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3711', 'ช่างทอง', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3711) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3712', 'ผู้ช่วยช่างต้นแบบสิ่งทอ', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3712) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3713', 'ช่างต้นแบบสิ่งทอ', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3713) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3714', 'ช่างทดสอบสิ่งทอ', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3714) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3715', 'ช่างทอผ้า', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3715) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3716', 'ช่างเย็บสานทอและประดิษฐ์', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3716) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3717', 'ช่างประดิษฐ์ดอกไม้', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3717) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3718', 'ช่างตัดเย็บผ้า', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3718) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3719', 'ช่างเย็บและตกแต่ง', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3719) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3720', 'ช่างพรม', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3720) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3721', 'ช่างถ่ายภาพ', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3721) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3722', 'ช่างซ่อมสร้างเครื่องโขน', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3722) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3723', 'ช่างเครื่องดนตรี', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3723) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3724', 'ช่างครุภัณฑ์', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3724) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3725', 'ช่างเย็บหนัง', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3725) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3726', 'ช่างทำฟัน', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3726) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3727', 'ช่างตัดผม', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3727) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3728', 'พนักงานสตั๊ฟส์', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3728) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3801', 'ครูฝึกอาชีพสงเคราะห์', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3801) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3802', 'ครูฝึกฝีมือแรงงาน', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3802) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3803', 'ครูสอนเสริมสวยและอาภรณ์', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3803) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3804', 'ผู้สอนการตัดเย็บเสื้อผ้าอุตสาหกรรม', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3804) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3805', 'ผู้สอนงานช่างไม้', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3805) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3806', 'ผู้สอนงานแกะสลักไม้', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3806) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3807', 'ผู้สอนงานประดิษฐ์ดอกไม้และผลิตภัณฑ์ผ้า', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3807) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3808', 'ผู้สอนผลิตภัณฑ์หวายและไม้ไผ่', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3808) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3809', 'ผู้สอนงานเครื่องปั้นดินเผา', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3809) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3810', 'ผู้สอนงานเครื่องเขิน เครื่องเงินและเครื่องถม', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3810) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3811', 'ผู้สอนงานเครื่องประดับและอัญมณี', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3811) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3812', 'ผู้สอนงานเจียระไนพลอย', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3812) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3813', 'ผู้สอนการฟอกหนัง', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3813) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3814', 'ผู้สอนวิชาการทำรองเท้า', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3814) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3815', 'ผู้สอนวิชาการพิมพ์', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3815) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3816', 'ผู้สอนวิชาช่างเครื่องยนต์', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3816) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3901', 'ช่างเครื่องระบบกำเนิดไอน้ำและน้ำร้อน', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3901) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3902', 'พนักงานเครื่องกำเนิดไฟฟ้า', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3902) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3903', 'ช่างซ่อมเครื่องทำความเย็น', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3903) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3904', 'พนักงานเครื่องสูบน้ำ', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3904) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3905', 'พนักงานเครื่องมือทุ่นแรงการเกษตร', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3905) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3906', 'ช่างยูนิตทันตกรรม', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3906) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3907', 'ช่างปรับซ่อมครุภัณฑ์สำนักงาน', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3907) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3908', 'พนักงานพิมพ์แบบเรียนเบรลล์', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3908) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3909', 'ช่างเป่าแก้ว', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3909) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3910', 'ช่างเครื่องช่วยคนพิการ', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3910) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3911', 'พนักงานเครื่องยก', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3911) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('3912', 'ผู้ช่วยช่างทั่วไป', '3000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 3912) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('4101', 'นายช่างประณีตศิลป', '4000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 4101) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('4201', 'ผู้ฝึกการใช้เครื่องมือกล', '4000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 4201) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('4301', 'ช่างวิทยุการบิน', '4000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 4301) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('4302', 'ช่างอิเลคทรอนิคส์การบิน', '4000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 4302) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('4303', 'ผู้ควบคุมวิทยุสื่อสารการบิน', '4000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 4303) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('4304', 'ผู้ช่วยช่างเครื่องบิน', '4000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 4304) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('4305', 'ช่างเครื่องบิน', '4000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 4305) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('4306', 'สารวัตรช่างเครื่องบิน', '4000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 4306) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('4307', 'นักบิน', '4000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 4307) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_POS_NAME (PN_CODE, PN_NAME, PG_CODE, PN_DECOR, PN_ACTIVE, UPDATE_USER, UPDATE_DATE, PN_SEQ_NO)
							  VALUES ('4308', 'ผู้ควบคุมหน่วยการบิน', '4000', 0, 1, $SESS_USERID, '$UPDATE_DATE' , 4308) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_NAME SET PG_CODE = '1000' WHERE PG_CODE IN ('1100', '1200', '1300', '1400', '1500') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_POS_NAME SET PG_CODE = '2000' WHERE PG_CODE IN ('2100', '2200', '2300', '2400', '2500', '2600', '2700', '2800', '2900') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_POS_NAME SET PG_CODE = '3000' WHERE PG_CODE IN ('3100', '3200', '3300', '3400', '3500', '3600', '3700', '3800', '3900') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_POS_NAME SET PG_CODE = '4000' WHERE PG_CODE IN ('4100', '4200', '4300') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " DELETE PER_POS_GROUP WHERE PG_CODE IN ('1100', '1200', '1300', '1400', '1500', '2100', '2200', '2300', '2400', '2500', '2600', '2700', '2800', '2900', '3100', '3200', '3300', '3400', '3500', '3600', '3700', '3800', '3900', '4100', '4200', '4300') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('1000', 1, 4630, 201.30, 28.80, 1, $SESS_USERID, '$UPDATE_DATE', 1) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('1000', 1.5, 4740, 206.10, 29.45, 1, $SESS_USERID, '$UPDATE_DATE', 2) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('1000', 2, 4850, 210.90, 30.15, 1, $SESS_USERID, '$UPDATE_DATE', 3) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('1000', 2.5, 4970, 216.10, 30.90, 1, $SESS_USERID, '$UPDATE_DATE', 4) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('1000', 3, 5080, 220.90, 31.60, 1, $SESS_USERID, '$UPDATE_DATE', 5) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('1000', 3.5, 5180, 225.25, 32.20, 1, $SESS_USERID, '$UPDATE_DATE', 6) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('1000', 4, 5310, 230.90, 33.00, 1, $SESS_USERID, '$UPDATE_DATE', 7) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('1000', 4.5, 5410, 235.25, 33.65, 1, $SESS_USERID, '$UPDATE_DATE', 8) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('1000', 5, 5530, 240.45, 34.35, 1, $SESS_USERID, '$UPDATE_DATE', 9) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('1000', 5.5, 5680, 247.00, 35.30, 1, $SESS_USERID, '$UPDATE_DATE', 10) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('1000', 6, 5840, 253.95, 36.30, 1, $SESS_USERID, '$UPDATE_DATE', 11) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('1000', 6.5, 6000, 260.90, 37.30, 1, $SESS_USERID, '$UPDATE_DATE', 12) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('1000', 7, 6160, 267.85, 38.30, 1, $SESS_USERID, '$UPDATE_DATE', 13) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('1000', 7.5, 6310, 274.35, 39.20, 1, $SESS_USERID, '$UPDATE_DATE', 14) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('1000', 8, 6470, 281.30, 40.20, 1, $SESS_USERID, '$UPDATE_DATE', 15) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('1000', 8.5, 6630, 288.30, 41.20, 1, $SESS_USERID, '$UPDATE_DATE', 16) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('1000', 9, 6800, 295.65, 42.25, 1, $SESS_USERID, '$UPDATE_DATE', 17) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('1000', 9.5, 6940, 301.75, 43.15, 1, $SESS_USERID, '$UPDATE_DATE', 18) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('1000', 10, 7100, 308.70, 44.10, 1, $SESS_USERID, '$UPDATE_DATE', 19) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('1000', 10.5, 7260, 315.65, 45.10, 1, $SESS_USERID, '$UPDATE_DATE', 20) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('1000', 11, 7420, 322.65, 46.10, 1, $SESS_USERID, '$UPDATE_DATE', 21) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('1000', 11.5, 7580, 329.60, 47.10, 1, $SESS_USERID, '$UPDATE_DATE', 22) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('1000', 12, 7730, 336.10, 48.05, 1, $SESS_USERID, '$UPDATE_DATE', 23) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('1000', 12.5, 7890, 343.05, 49.05, 1, $SESS_USERID, '$UPDATE_DATE', 24) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('1000', 13, 8040, 349.60, 49.95, 1, $SESS_USERID, '$UPDATE_DATE', 25) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('1000', 13.5, 8200, 356.55, 50.95, 1, $SESS_USERID, '$UPDATE_DATE', 26) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('1000', 14, 8380, 364.35, 52.05, 1, $SESS_USERID, '$UPDATE_DATE', 27) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('1000', 14.5, 8540, 371.30, 53.05, 1, $SESS_USERID, '$UPDATE_DATE', 28) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('1000', 15, 8710, 378.70, 54.10, 1, $SESS_USERID, '$UPDATE_DATE', 29) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('1000', 15.5, 8880, 386.10, 55.20, 1, $SESS_USERID, '$UPDATE_DATE', 30) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('1000', 16, 9060, 393.95, 56.30, 1, $SESS_USERID, '$UPDATE_DATE', 31) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('1000', 16.5, 9240, 401.75, 57.40, 1, $SESS_USERID, '$UPDATE_DATE', 32) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('1000', 17, 9430, 410.00, 58.60, 1, $SESS_USERID, '$UPDATE_DATE', 33) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('1000', 17.5, 9590, 417.00, 59.60, 1, $SESS_USERID, '$UPDATE_DATE', 34) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('1000', 18, 9790, 425.65, 60.85, 1, $SESS_USERID, '$UPDATE_DATE', 35) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('1000', 18.5, 10030, 436.10, 62.30, 1, $SESS_USERID, '$UPDATE_DATE', 36) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('1000', 19, 10240, 445.25, 63.65, 1, $SESS_USERID, '$UPDATE_DATE', 37) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('1000', 19.5, 10440, 453.95, 64.85, 1, $SESS_USERID, '$UPDATE_DATE', 38) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('1000', 20, 10640, 462.65, 66.10, 1, $SESS_USERID, '$UPDATE_DATE', 39) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('1000', 20.5, 10850, 471.75, 67.40, 1, $SESS_USERID, '$UPDATE_DATE', 40) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('1000', 21, 11070, 481.30, 68.80, 1, $SESS_USERID, '$UPDATE_DATE', 41) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('1000', 21.5, 11290, 490.90, 70.15, 1, $SESS_USERID, '$UPDATE_DATE', 42) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('1000', 22, 11510, 500.45, 71.50, 1, $SESS_USERID, '$UPDATE_DATE', 43) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('1000', 22.5, 11740, 510.45, 72.95, 1, $SESS_USERID, '$UPDATE_DATE', 44) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('1000', 23, 11960, 520.00, 74.30, 1, $SESS_USERID, '$UPDATE_DATE', 45) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('1000', 23.5, 12200, 530.45, 75.80, 1, $SESS_USERID, '$UPDATE_DATE', 46) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('1000', 24, 12440, 540.90, 77.30, 1, $SESS_USERID, '$UPDATE_DATE', 47) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('1000', 24.5, 12670, 550.90, 78.70, 1, $SESS_USERID, '$UPDATE_DATE', 48) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('1000', 25, 13100, 569.60, 81.40, 1, $SESS_USERID, '$UPDATE_DATE', 49) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('1000', 25.5, 13360, 580.90, 83.00, 1, $SESS_USERID, '$UPDATE_DATE', 50) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('1000', 26, 13620, 592.20, 84.60, 1, $SESS_USERID, '$UPDATE_DATE', 51) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('1000', 26.5, 13870, 603.05, 86.15, 1, $SESS_USERID, '$UPDATE_DATE', 52) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('1000', 27, 14140, 614.80, 87.85, 1, $SESS_USERID, '$UPDATE_DATE', 53) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('1000', 27.5, 14410, 626.55, 89.55, 1, $SESS_USERID, '$UPDATE_DATE', 54) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('1000', 28, 14700, 639.15, 91.35, 1, $SESS_USERID, '$UPDATE_DATE', 55) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('1000', 28.5, 14970, 650.90, 93.00, 1, $SESS_USERID, '$UPDATE_DATE', 56) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('1000', 29, 15260, 663.50, 94.80, 1, $SESS_USERID, '$UPDATE_DATE', 57) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('1000', 29.5, 15560, 676.55, 96.65, 1, $SESS_USERID, '$UPDATE_DATE', 58) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('1000', 30, 15850, 689.15, 98.45, 1, $SESS_USERID, '$UPDATE_DATE', 59) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('1000', 30.5, 16150, 702.20, 100.35, 1, $SESS_USERID, '$UPDATE_DATE', 60) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('1000', 31, 16440, 714.80, 102.15, 1, $SESS_USERID, '$UPDATE_DATE', 61) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('1000', 31.5, 16730, 727.40, 103.95, 1, $SESS_USERID, '$UPDATE_DATE', 62) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('1000', 32, 17020, 740.00, 105.75, 1, $SESS_USERID, '$UPDATE_DATE', 63) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('1000', 32.5, 17320, 753.05, 107.60, 1, $SESS_USERID, '$UPDATE_DATE', 64) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('1000', 33, 17600, 765.25, 109.35, 1, $SESS_USERID, '$UPDATE_DATE', 65) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('1000', 33.5, 17890, 777.85, 111.15, 1, $SESS_USERID, '$UPDATE_DATE', 66) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('1000', 34, 18190, 790.90, 113.00, 1, $SESS_USERID, '$UPDATE_DATE', 67) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('2000', 1, 7170, 311.75, 44.55, 1, $SESS_USERID, '$UPDATE_DATE', 101) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('2000', 1.5, 7360, 320.00, 45.75, 1, $SESS_USERID, '$UPDATE_DATE', 102) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('2000', 2, 7560, 328.70, 47.00, 1, $SESS_USERID, '$UPDATE_DATE', 103) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('2000', 2.5, 7740, 336.55, 48.10, 1, $SESS_USERID, '$UPDATE_DATE', 104) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('2000', 3, 7940, 345.25, 49.35, 1, $SESS_USERID, '$UPDATE_DATE', 105) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('2000', 3.5, 8130, 353.50, 50.50, 1, $SESS_USERID, '$UPDATE_DATE', 106) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('2000', 4, 8320, 361.75, 51.70, 1, $SESS_USERID, '$UPDATE_DATE', 107) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('2000', 4.5, 8540, 371.30, 53.05, 1, $SESS_USERID, '$UPDATE_DATE', 108) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('2000', 5, 8770, 381.30, 54.50, 1, $SESS_USERID, '$UPDATE_DATE', 109) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('2000', 5.5, 8990, 390.90, 55.85, 1, $SESS_USERID, '$UPDATE_DATE', 110) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('2000', 6, 9230, 401.30, 57.35, 1, $SESS_USERID, '$UPDATE_DATE', 111) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('2000', 6.5, 9480, 412.20, 58.90, 1, $SESS_USERID, '$UPDATE_DATE', 112) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('2000', 7, 9700, 421.75, 60.25, 1, $SESS_USERID, '$UPDATE_DATE', 113) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('2000', 7.5, 9940, 432.20, 61.75, 1, $SESS_USERID, '$UPDATE_DATE', 114) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('2000', 8, 10190, 443.05, 63.30, 1, $SESS_USERID, '$UPDATE_DATE', 115) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('2000', 8.5, 10470, 455.25, 65.05, 1, $SESS_USERID, '$UPDATE_DATE', 116) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('2000', 9, 10770, 468.30, 66.90, 1, $SESS_USERID, '$UPDATE_DATE', 117) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('2000', 9.5, 11060, 480.90, 68.70, 1, $SESS_USERID, '$UPDATE_DATE', 118) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('2000', 10, 11350, 493.50, 70.50, 1, $SESS_USERID, '$UPDATE_DATE', 119) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('2000', 10.5, 11650, 506.55, 72.40, 1, $SESS_USERID, '$UPDATE_DATE', 120) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('2000', 11, 11930, 518.70, 74.10, 1, $SESS_USERID, '$UPDATE_DATE', 121) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('2000', 11.5, 12220, 531.30, 75.90, 1, $SESS_USERID, '$UPDATE_DATE', 122) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('2000', 12, 12530, 544.80, 77.85, 1, $SESS_USERID, '$UPDATE_DATE', 123) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('2000', 12.5, 12820, 557.40, 79.65, 1, $SESS_USERID, '$UPDATE_DATE', 124) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('2000', 13, 13110, 570.00, 81.45, 1, $SESS_USERID, '$UPDATE_DATE', 125) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('2000', 13.5, 13400, 582.65, 83.25, 1, $SESS_USERID, '$UPDATE_DATE', 126) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('2000', 14, 13690, 595.25, 85.05, 1, $SESS_USERID, '$UPDATE_DATE', 127) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('2000', 14.5, 13980, 607.85, 86.85, 1, $SESS_USERID, '$UPDATE_DATE', 128) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('2000', 15, 14280, 620.90, 88.70, 1, $SESS_USERID, '$UPDATE_DATE', 129) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('2000', 15.5, 14560, 633.05, 90.45, 1, $SESS_USERID, '$UPDATE_DATE', 130) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('2000', 16, 14860, 646.10, 92.30, 1, $SESS_USERID, '$UPDATE_DATE', 131) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('2000', 16.5, 15160, 659.15, 94.20, 1, $SESS_USERID, '$UPDATE_DATE', 132) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('2000', 17, 15460, 672.20, 96.05, 1, $SESS_USERID, '$UPDATE_DATE', 133) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('2000', 17.5, 15760, 685.25, 97.90, 1, $SESS_USERID, '$UPDATE_DATE', 134) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('2000', 18, 16070, 698.70, 99.85, 1, $SESS_USERID, '$UPDATE_DATE', 135) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('2000', 18.5, 16380, 712.20, 101.75, 1, $SESS_USERID, '$UPDATE_DATE', 136) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('2000', 19, 16710, 726.55, 103.80, 1, $SESS_USERID, '$UPDATE_DATE', 137) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('2000', 19.5, 17030, 740.45, 105.80, 1, $SESS_USERID, '$UPDATE_DATE', 138) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('2000', 20, 17360, 754.80, 107.85, 1, $SESS_USERID, '$UPDATE_DATE', 139) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('2000', 20.5, 17700, 769.60, 109.95, 1, $SESS_USERID, '$UPDATE_DATE', 140) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('2000', 21, 18040, 784.35, 112.05, 1, $SESS_USERID, '$UPDATE_DATE', 141) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('2000', 21.5, 18380, 799.15, 114.20, 1, $SESS_USERID, '$UPDATE_DATE', 142) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('2000', 22, 18720, 813.95, 116.30, 1, $SESS_USERID, '$UPDATE_DATE', 143) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('2000', 22.5, 19080, 829.60, 118.55, 1, $SESS_USERID, '$UPDATE_DATE', 144) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('2000', 23, 19420, 844.35, 120.65, 1, $SESS_USERID, '$UPDATE_DATE', 145) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('2000', 23.5, 19780, 860.00, 122.90, 1, $SESS_USERID, '$UPDATE_DATE', 146) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('2000', 24, 20130, 875.25, 125.05, 1, $SESS_USERID, '$UPDATE_DATE', 147) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('2000', 24.5, 20470, 890.00, 127.15, 1, $SESS_USERID, '$UPDATE_DATE', 148) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('2000', 25, 20830, 905.65, 129.40, 1, $SESS_USERID, '$UPDATE_DATE', 149) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('2000', 25.5, 21170, 920.45, 131.50, 1, $SESS_USERID, '$UPDATE_DATE', 150) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('2000', 26, 21520, 935.65, 133.70, 1, $SESS_USERID, '$UPDATE_DATE', 151) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('2000', 26.5, 21880, 951.30, 135.90, 1, $SESS_USERID, '$UPDATE_DATE', 152) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('2000', 27, 22220, 966.10, 138.05, 1, $SESS_USERID, '$UPDATE_DATE', 153) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('3000', 1, 8770, 381.30, 54.50, 1, $SESS_USERID, '$UPDATE_DATE', 201) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('3000', 1.5, 8990, 390.90, 55.85, 1, $SESS_USERID, '$UPDATE_DATE', 202) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('3000', 2, 9230, 401.30, 57.35, 1, $SESS_USERID, '$UPDATE_DATE', 203) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('3000', 2.5, 9480, 412.20, 58.90, 1, $SESS_USERID, '$UPDATE_DATE', 204) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('3000', 3, 9700, 421.75, 60.25, 1, $SESS_USERID, '$UPDATE_DATE', 205) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('3000', 3.5, 9940, 432.20, 61.75, 1, $SESS_USERID, '$UPDATE_DATE', 206) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('3000', 4, 10190, 443.05, 63.30, 1, $SESS_USERID, '$UPDATE_DATE', 207) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('3000', 4.5, 10470, 455.25, 65.05, 1, $SESS_USERID, '$UPDATE_DATE', 208) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('3000', 5, 10770, 468.30, 66.90, 1, $SESS_USERID, '$UPDATE_DATE', 209) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('3000', 5.5, 11060, 480.90, 68.70, 1, $SESS_USERID, '$UPDATE_DATE', 210) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('3000', 6, 11350, 493.50, 70.50, 1, $SESS_USERID, '$UPDATE_DATE', 211) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('3000', 6.5, 11650, 506.55, 72.40, 1, $SESS_USERID, '$UPDATE_DATE', 212) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('3000', 7, 11930, 518.70, 74.10, 1, $SESS_USERID, '$UPDATE_DATE', 213) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('3000', 7.5, 12220, 531.30, 75.90, 1, $SESS_USERID, '$UPDATE_DATE', 214) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('3000', 8, 12530, 544.80, 77.85, 1, $SESS_USERID, '$UPDATE_DATE', 215) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('3000', 8.5, 12880, 560.00, 80.00, 1, $SESS_USERID, '$UPDATE_DATE', 216) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('3000', 9, 13240, 575.65, 82.25, 1, $SESS_USERID, '$UPDATE_DATE', 217) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('3000', 9.5, 13610, 591.75, 84.55, 1, $SESS_USERID, '$UPDATE_DATE', 218) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('3000', 10, 13960, 607.00, 86.75, 1, $SESS_USERID, '$UPDATE_DATE', 219) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('3000', 10.5, 14330, 623.05, 89.05, 1, $SESS_USERID, '$UPDATE_DATE', 220) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('3000', 11, 14690, 638.70, 91.25, 1, $SESS_USERID, '$UPDATE_DATE', 221) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('3000', 11.5, 15040, 653.95, 93.45, 1, $SESS_USERID, '$UPDATE_DATE', 222) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('3000', 12, 15410, 670.00, 95.75, 1, $SESS_USERID, '$UPDATE_DATE', 223) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('3000', 12.5, 15780, 686.10, 98.05, 1, $SESS_USERID, '$UPDATE_DATE', 224) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('3000', 13, 16110, 700.45, 100.10, 1, $SESS_USERID, '$UPDATE_DATE', 225) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('3000', 13.5, 16480, 716.55, 102.40, 1, $SESS_USERID, '$UPDATE_DATE', 226) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('3000', 14, 16840, 732.20, 104.60, 1, $SESS_USERID, '$UPDATE_DATE', 227) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('3000', 14.5, 17200, 747.85, 106.85, 1, $SESS_USERID, '$UPDATE_DATE', 228) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('3000', 15, 17560, 763.50, 109.10, 1, $SESS_USERID, '$UPDATE_DATE', 229) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('3000', 15.5, 17910, 778.70, 111.25, 1, $SESS_USERID, '$UPDATE_DATE', 230) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('3000', 16, 18280, 794.80, 113.55, 1, $SESS_USERID, '$UPDATE_DATE', 231) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('3000', 16.5, 18640, 810.45, 115.80, 1, $SESS_USERID, '$UPDATE_DATE', 232) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('3000', 17, 19010, 826.55, 118.10, 1, $SESS_USERID, '$UPDATE_DATE', 233) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('3000', 17.5, 19390, 843.05, 120.45, 1, $SESS_USERID, '$UPDATE_DATE', 234) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('3000', 18, 19790, 860.45, 122.95, 1, $SESS_USERID, '$UPDATE_DATE', 235) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('3000', 18.5, 20180, 877.40, 125.35, 1, $SESS_USERID, '$UPDATE_DATE', 236) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('3000', 19, 20590, 895.25, 127.90, 1, $SESS_USERID, '$UPDATE_DATE', 237) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('3000', 19.5, 20990, 912.65, 130.40, 1, $SESS_USERID, '$UPDATE_DATE', 238) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('3000', 20, 21410, 930.90, 133.00, 1, $SESS_USERID, '$UPDATE_DATE', 239) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('3000', 20.5, 21820, 948.70, 135.55, 1, $SESS_USERID, '$UPDATE_DATE', 240) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('3000', 21, 22250, 967.40, 138.20, 1, $SESS_USERID, '$UPDATE_DATE', 241) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('3000', 21.5, 22680, 986.10, 140.90, 1, $SESS_USERID, '$UPDATE_DATE', 242) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('3000', 22, 23110, 1004.80, 143.55, 1, $SESS_USERID, '$UPDATE_DATE', 243) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('3000', 22.5, 23550, 1023.95, 146.30, 1, $SESS_USERID, '$UPDATE_DATE', 244) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('3000', 23, 23990, 1043.05, 149.05, 1, $SESS_USERID, '$UPDATE_DATE', 245) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('3000', 23.5, 24730, 1075.25, 153.65, 1, $SESS_USERID, '$UPDATE_DATE', 246) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('3000', 24, 25200, 1095.65, 156.55, 1, $SESS_USERID, '$UPDATE_DATE', 247) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('3000', 24.5, 25690, 1117.00, 159.60, 1, $SESS_USERID, '$UPDATE_DATE', 248) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('3000', 25, 26170, 1137.85, 162.55, 1, $SESS_USERID, '$UPDATE_DATE', 249) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('3000', 25.5, 26690, 1160.45, 165.80, 1, $SESS_USERID, '$UPDATE_DATE', 250) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('3000', 26, 27200, 1182.65, 168.95, 1, $SESS_USERID, '$UPDATE_DATE', 251) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('3000', 26.5, 27720, 1205.25, 172.20, 1, $SESS_USERID, '$UPDATE_DATE', 252) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('3000', 27, 28260, 1228.70, 175.55, 1, $SESS_USERID, '$UPDATE_DATE', 253) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('3000', 27.5, 28780, 1251.30, 178.80, 1, $SESS_USERID, '$UPDATE_DATE', 254) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('3000', 28, 29320, 1274.80, 182.15, 1, $SESS_USERID, '$UPDATE_DATE', 255) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('3000', 28.5, 29840, 1297.40, 185.35, 1, $SESS_USERID, '$UPDATE_DATE', 256) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('3000', 29, 30360, 1320.00, 188.60, 1, $SESS_USERID, '$UPDATE_DATE', 257) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('3000', 29.5, 30900, 1343.50, 191.95, 1, $SESS_USERID, '$UPDATE_DATE', 258) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('3000', 30, 31420, 1366.10, 195.20, 1, $SESS_USERID, '$UPDATE_DATE', 259) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('3000', 30.5, 31960, 1389.60, 198.55, 1, $SESS_USERID, '$UPDATE_DATE', 260) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('3000', 31, 32480, 1412.20, 201.75, 1, $SESS_USERID, '$UPDATE_DATE', 261) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('3000', 31.5, 33020, 1435.65, 205.10, 1, $SESS_USERID, '$UPDATE_DATE', 262) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('3000', 32, 33540, 1458.30, 208.35, 1, $SESS_USERID, '$UPDATE_DATE', 263) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('3000', 32.5, 34050, 1480.45, 211.50, 1, $SESS_USERID, '$UPDATE_DATE', 264) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('3000', 33, 34710, 1509.15, 215.60, 1, $SESS_USERID, '$UPDATE_DATE', 265) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('3000', 33.5, 35360, 1537.40, 219.65, 1, $SESS_USERID, '$UPDATE_DATE', 266) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('3000', 34, 36020, 1566.10, 223.75, 1, $SESS_USERID, '$UPDATE_DATE', 267) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('4000', 1, 24310, 1057.00, 151.00, 1, $SESS_USERID, '$UPDATE_DATE', 301) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('4000', 1.5, 24850, 1080.45, 154.35, 1, $SESS_USERID, '$UPDATE_DATE', 302) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('4000', 2, 25390, 1103.95, 157.75, 1, $SESS_USERID, '$UPDATE_DATE', 303) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('4000', 2.5, 25930, 1127.40, 161.10, 1, $SESS_USERID, '$UPDATE_DATE', 304) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('4000', 3, 26470, 1150.90, 164.45, 1, $SESS_USERID, '$UPDATE_DATE', 305) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('4000', 3.5, 27000, 1173.95, 167.75, 1, $SESS_USERID, '$UPDATE_DATE', 306) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('4000', 4, 27550, 1197.85, 171.15, 1, $SESS_USERID, '$UPDATE_DATE', 307) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('4000', 4.5, 28100, 1221.75, 174.55, 1, $SESS_USERID, '$UPDATE_DATE', 308) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('4000', 5, 28660, 1246.10, 178.05, 1, $SESS_USERID, '$UPDATE_DATE', 309) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('4000', 5.5, 29220, 1270.45, 181.50, 1, $SESS_USERID, '$UPDATE_DATE', 310) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('4000', 6, 29800, 1295.65, 185.10, 1, $SESS_USERID, '$UPDATE_DATE', 311) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('4000', 6.5, 30380, 1320.90, 188.70, 1, $SESS_USERID, '$UPDATE_DATE', 312) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('4000', 7, 30960, 1346.10, 192.30, 1, $SESS_USERID, '$UPDATE_DATE', 313) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('4000', 7.5, 31560, 1372.20, 196.05, 1, $SESS_USERID, '$UPDATE_DATE', 314) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('4000', 8, 32160, 1398.30, 199.80, 1, $SESS_USERID, '$UPDATE_DATE', 315) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('4000', 8.5, 32790, 1425.65, 203.70, 1, $SESS_USERID, '$UPDATE_DATE', 316) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('4000', 9, 33410, 1452.65, 207.55, 1, $SESS_USERID, '$UPDATE_DATE', 317) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('4000', 9.5, 34050, 1480.45, 211.50, 1, $SESS_USERID, '$UPDATE_DATE', 318) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('4000', 10, 34670, 1507.40, 215.35, 1, $SESS_USERID, '$UPDATE_DATE', 319) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('4000', 10.5, 35350, 1537.00, 219.60, 1, $SESS_USERID, '$UPDATE_DATE', 320) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('4000', 11, 36070, 1568.30, 224.05, 1, $SESS_USERID, '$UPDATE_DATE', 321) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('4000', 11.5, 36780, 1599.15, 228.45, 1, $SESS_USERID, '$UPDATE_DATE', 322) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('4000', 12, 37480, 1629.60, 232.80, 1, $SESS_USERID, '$UPDATE_DATE', 323) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('4000', 12.5, 38190, 1660.45, 237.35, 1, $SESS_USERID, '$UPDATE_DATE', 324) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('4000', 13, 38940, 1693.05, 241.90, 1, $SESS_USERID, '$UPDATE_DATE', 325) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('4000', 13.5, 39680, 1725.25, 246.50, 1, $SESS_USERID, '$UPDATE_DATE', 326) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('4000', 14, 40460, 1759.15, 251.35, 1, $SESS_USERID, '$UPDATE_DATE', 327) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('4000', 14.5, 41720, 1813.95, 259.15, 1, $SESS_USERID, '$UPDATE_DATE', 328) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('4000', 15, 42550, 1850.00, 264.30, 1, $SESS_USERID, '$UPDATE_DATE', 329) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('4000', 15.5, 43380, 1886.10, 269.45, 1, $SESS_USERID, '$UPDATE_DATE', 330) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('4000', 16, 44250, 1923.95, 274.85, 1, $SESS_USERID, '$UPDATE_DATE', 331) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('4000', 16.5, 45130, 1962.20, 280.35, 1, $SESS_USERID, '$UPDATE_DATE', 332) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('4000', 17, 45990, 1999.60, 285.70, 1, $SESS_USERID, '$UPDATE_DATE', 333) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('4000', 17.5, 47390, 2060.45, 294.35, 1, $SESS_USERID, '$UPDATE_DATE', 334) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('4000', 18, 48440, 2106.10, 300.90, 1, $SESS_USERID, '$UPDATE_DATE', 335) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('4000', 18.5, 49480, 2151.30, 307.35, 1, $SESS_USERID, '$UPDATE_DATE', 336) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('4000', 19, 50530, 2197.00, 313.90, 1, $SESS_USERID, '$UPDATE_DATE', 337) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('4000', 19.5, 51590, 2243.05, 320.45, 1, $SESS_USERID, '$UPDATE_DATE', 338) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('4000', 20, 52630, 2288.30, 326.90, 1, $SESS_USERID, '$UPDATE_DATE', 339) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('4000', 20.5, 53690, 2334.35, 333.50, 1, $SESS_USERID, '$UPDATE_DATE', 340) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('4000', 21, 54740, 2380.00, 340.00, 1, $SESS_USERID, '$UPDATE_DATE', 341) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('4000', 21.5, 55800, 2426.10, 346.60, 1, $SESS_USERID, '$UPDATE_DATE', 342) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('4000', 22, 56860, 2472.20, 353.20, 1, $SESS_USERID, '$UPDATE_DATE', 343) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('4000', 22.5, 57940, 2519.15, 359.90, 1, $SESS_USERID, '$UPDATE_DATE', 344) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('4000', 23, 59000, 2565.25, 366.50, 1, $SESS_USERID, '$UPDATE_DATE', 345) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('4000', 23.5, 60060, 2611.30, 373.05, 1, $SESS_USERID, '$UPDATE_DATE', 346) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('4000', 24, 61140, 2658.25, 379.75, 1, $SESS_USERID, '$UPDATE_DATE', 347) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('4000', 24.5, 62200, 2704.35, 386.30, 1, $SESS_USERID, '$UPDATE_DATE', 348) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('4000', 25, 63270, 2750.90, 393.00, 1, $SESS_USERID, '$UPDATE_DATE', 349) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('4000', 25.5, 64340, 2797.40, 399.65, 1, $SESS_USERID, '$UPDATE_DATE', 350) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc" || $DPISDB=="mysql") 
			$cmd = " ALTER TABLE PER_POS_EMP ADD PG_CODE_SALARY VARCHAR(10) ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_POS_EMP ADD PG_CODE_SALARY VARCHAR2(10) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc" || $DPISDB=="mysql") 
			$cmd = " UPDATE PER_POS_EMP, PER_POS_NAME SET PER_POS_EMP.PG_CODE_SALARY = 
							  PER_POS_NAME.PG_CODE WHERE PER_POS_NAME.PN_CODE = PER_POS_EMP.PN_CODE AND PER_POS_EMP.PG_CODE_SALARY IS NULL ";
		elseif($DPISDB=="oci8") 
			$cmd = " UPDATE PER_POS_EMP A SET A.PG_CODE_SALARY = 
							  (SELECT B.PG_CODE FROM PER_POS_NAME B WHERE A.PN_CODE = B.PN_CODE) WHERE A.PG_CODE_SALARY IS NULL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
?>