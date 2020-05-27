<?
		$cmd = " ALTER TABLE USER_GROUP ALTER CODE VARCHAR(50) ";
		$db->send_cmd($cmd);
		//$db->show_error();

		$cmd = " ALTER TABLE USER_GROUP ALTER NAME_TH VARCHAR(255) ";
		$db->send_cmd($cmd);
		//$db->show_error();

		$cmd = " ALTER TABLE USER_GROUP ALTER NAME_EN VARCHAR(255) ";
		$db->send_cmd($cmd);
		//$db->show_error();

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_gpis.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 7, 'P1107 ระบบสารสนเทศเพื่อการวางแผนกำลังคนภาครัฐ', 'S', 'W', 'rpt_gpis.html', 0, 35, 251, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
							  CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 7, 'P1107 ระบบสารสนเทศเพื่อการวางแผนกำลังคนภาครัฐ', 'S', 'W', 'rpt_gpis.html', 0, 35, 251, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 
						  WHERE LINKTO_WEB = 'master_table_update_code.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 7, 'C0707 ปรับปรุงรหัสข้อมูลหลักให้เป็นมาตรฐาน', 'S', 'W', 'master_table_update_code.html', 0, 1, 305,
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
							  CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 7, 'C0707 ปรับปรุงรหัสข้อมูลหลักให้เป็นมาตรฐาน', 'S', 'W', 'master_table_update_code.html', 0, 1, 305,
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
						  FLAG_SHOW, TYPE_LINKTO, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, UPDATE_DATE, 
						  UPDATE_BY)
						  VALUES (1, 'TH', 308, 14, 'P14 จัดคนลง', 'S', 'N', 0, 35, $CREATE_DATE, $CREATE_BY, 
						  $CREATE_DATE, $CREATE_BY) ";
		$db->send_cmd($cmd);
		//$db->show_error();

		$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
						  FLAG_SHOW, TYPE_LINKTO, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, UPDATE_DATE, 
						  UPDATE_BY)
						  VALUES (1, 'EN', 308, 14, 'P14 จัดคนลง', 'S', 'N', 0, 35, $CREATE_DATE, $CREATE_BY, 
						  $CREATE_DATE, $CREATE_BY) ";
		$db->send_cmd($cmd);
		//$db->show_error();

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 
						  WHERE LINKTO_WEB = 'data_map_type_new_check.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 1, 'P1401 ตรวจสอบการจัดคนลงตาม พรบ.ใหม่', 'S', 'W', 'data_map_type_new_check.html', 0, 35, 308, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
							  CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 1, 'P1401 ตรวจสอบการจัดคนลงตาม พรบ.ใหม่', 'S', 'W', 'data_map_type_new_check.html', 0, 35, 308, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 
						  WHERE LINKTO_WEB = 'data_map_type_new_comdtl.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 2, 'P1402 บัญชีแนบท้ายคำสั่งจัดคนลงตาม พรบ.ใหม่', 'S', 'W', 'data_map_type_new_comdtl.html', 0, 35,
							  308, $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
							  CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 2, 'P1402 บัญชีแนบท้ายคำสั่งจัดคนลงตาม พรบ.ใหม่', 'S', 'W', 'data_map_type_new_comdtl.html', 0, 35,
							  308, $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		} else {
			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET MENU_ORDER = 2, 
							  MENU_LABEL = 'P1402 บัญชีแนบท้ายคำสั่งจัดคนลงตาม พรบ.ใหม่' 
							  WHERE LINKTO_WEB = 'data_map_type_new_comdtl.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 
						  WHERE LINKTO_WEB = 'master_table_layeremp.html?table=PER_LAYEREMP_NEW' ";
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
							  VALUES (1, 'TH', $MAX_ID, 7, 'M0407 บัญชีอัตราเงินเดือนลูกจ้างใหม่', 'S', 'W', 
							  'master_table_layeremp.html?table=PER_LAYEREMP_NEW', 0, 9, 297, $CREATE_DATE, $CREATE_BY, 
							  $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
							  CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 7, 'M0407 บัญชีอัตราเงินเดือนลูกจ้างใหม่', 'S', 'W', 
							  'master_table_layeremp.html?table=PER_LAYEREMP_NEW', 0, 9, 297, $CREATE_DATE, $CREATE_BY, 
							  $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 
						  WHERE LINKTO_WEB = 'master_table.html?table=PER_PERFORMANCE' ";
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
							  VALUES (1, 'TH', $MAX_ID, 9, 'K09 ผลงาน', 'S', 'W', 'master_table.html?table=PER_PERFORMANCE', 0, 40, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 9, 'K09 ผลงาน', 'S', 'W', 'master_table.html?table=PER_PERFORMANCE', 0, 40, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 
						  WHERE LINKTO_WEB = 'master_table.html?table=PER_GOODNESS' ";
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
							  VALUES (1, 'TH', $MAX_ID, 10, 'K10 คุณงามความดี', 'S', 'W', 'master_table.html?table=PER_GOODNESS', 0, 40, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 10, 'K10 คุณงามความดี', 'S', 'W', 'master_table.html?table=PER_GOODNESS', 0, 40, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'book_form.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 11, 'K11 สมุดบันทึกผลงานและคุณงามความดี', 'S', 'W', 'book_form.html', 0, 40, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 11, 'K11 สมุดบันทึกผลงานและคุณงามความดี', 'S', 'W', 'book_form.html', 0, 40, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 
						  WHERE LINKTO_WEB = 'master_table_sql.html?table=PER_SQL' ";
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
							  VALUES (1, 'TH', $MAX_ID, 6, 'C0706 SQL Command', 'S', 'W', 'master_table_sql.html?table=PER_SQL', 0, 1, 305, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
							  CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 6, 'C0706 SQL Command', 'S', 'W', 'master_table_sql.html?table=PER_SQL', 0, 1, 305, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'data_move_comdtl_N.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 11, 'P0311 บัญชีแนบท้ายคำสั่งจัดคนลงตามโครงสร้างส่วนราชการใหม่', 'S', 'W', 
							  'data_move_comdtl_N.html', 0, 35, 243, $CREATE_DATE, $CREATE_BY, 
							  $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 		
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
							  CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 11, 'P0311 บัญชีแนบท้ายคำสั่งจัดคนลงตามโครงสร้างส่วนราชการใหม่', 'S', 'W', 
							  'data_move_comdtl_N.html', 0, 35, 243, $CREATE_DATE, $CREATE_BY, 
							  $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 
						  WHERE LINKTO_WEB = 'master_table.html?table=PER_LINE_GROUP' ";
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
							  VALUES (1, 'TH', $MAX_ID, 13, 'M0313 สายงาน', 'S', 'W', 'master_table.html?table=PER_LINE_GROUP', 0, 9, 295, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
							  CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 13, 'M0313 สายงาน', 'S', 'W', 'master_table.html?table=PER_LINE_GROUP', 0, 9, 295, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV0 
						  WHERE LINKTO_WEB = '../help/dpis_menu/dpis_menu.htm' ";
		$count_data = $db->send_cmd($cmd);
		//$db->show_error();
		if (!$count_data) {
			$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV0 ";
			$db->send_cmd($cmd);
			//$db->show_error();
			$data = $db->get_array();
			$MAX_ID = $data[MAX_ID] + 1;
			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV0 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, CREATE_DATE, CREATE_BY, UPDATE_DATE, 
							  UPDATE_BY)
							  VALUES (1, 'TH', $MAX_ID, 12, 'ระบบให้ความช่วยเหลือ', 'S', 'W', '../help/dpis_menu/dpis_menu.htm', 0, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV0 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, CREATE_DATE, CREATE_BY, UPDATE_DATE, 
							  UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 12, 'ระบบให้ความช่วยเหลือ', 'S', 'W', '../help/dpis_menu/dpis_menu.htm', 0, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET MENU_LABEL = 'C0704 โปรแกรมปรับเปลี่ยนฐานข้อมูล' 
						  WHERE LINKTO_WEB = 'alter_table.html' ";
		$db->send_cmd($cmd);
		//$db->show_error();

		$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV0 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
						  FLAG_SHOW, TYPE_LINKTO, LINKTO_TID, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
						  VALUES (1, 'TH', 43, 5, 'รายงานผู้บริหารระดับสูง', 'S', 'N', 0, $CREATE_DATE, $CREATE_BY, 
						  $CREATE_DATE, $CREATE_BY) ";
		$db->send_cmd($cmd);
		//$db->show_error();

		$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV0 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
						  FLAG_SHOW, TYPE_LINKTO, LINKTO_TID, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
						  VALUES (1, 'EN', 43, 5, 'รายงานผู้บริหารระดับสูง', 'S', 'N', 0, $CREATE_DATE, $CREATE_BY, 
						  $CREATE_DATE, $CREATE_BY) ";
		$db->send_cmd($cmd);
		//$db->show_error();

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'rpt_R010001.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 1, 'H01 รายชื่อข้าราชการระดับ 9 - 11', 'S', 'W', 'rpt_R010001.html', 0, 43, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 1, 'H01 รายชื่อข้าราชการระดับ 9 - 11', 'S', 'W', 'rpt_R010001.html', 0, 43, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'rpt_R010002.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 2, 'H02 ข้อมูลข้าราชการพลเรือนระดับ 9 ขึ้นไปหรือเทียบเท่า', 'S', 'W', 'rpt_R010002.html', 0, 43, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 2, 'H02 ข้อมูลข้าราชการพลเรือนระดับ 9 ขึ้นไปหรือเทียบเท่า', 'S', 'W', 'rpt_R010002.html', 0, 43, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'rpt_R010003.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 3, 'H03 ผู้ดำรงตำแหน่งปลัดกระทรวงหรือเทียบเท่า (นักบริหาร 11)', 'S', 'W', 'rpt_R010003.html', 0, 43, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 3, 'H03 ผู้ดำรงตำแหน่งปลัดกระทรวงหรือเทียบเท่า (นักบริหาร 11)', 'S', 'W', 'rpt_R010003.html', 0, 43, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'rpt_R010004.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 4, 'H04 รายงานนักบริหารระดับ 10', 'S', 'W', 'rpt_R010004.html', 0, 43, $CREATE_DATE, 
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 4, 'H04 รายงานนักบริหารระดับ 10', 'S', 'W', 'rpt_R010004.html', 0, 43, $CREATE_DATE, 
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'rpt_R010005.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 5, 'H05 รายชื่อนักบริหารจำแนกตามส่วนราชการ', 'S', 'W', 'rpt_R010005.html', 0, 43, $CREATE_DATE, 
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 5, 'H05 รายชื่อนักบริหารจำแนกตามส่วนราชการ', 'S', 'W', 'rpt_R010005.html', 0, 43, $CREATE_DATE, 
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'rpt_R010006.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 6, 'H06 รายงานประวัติวุฒิการศึกษานักบริหาร', 'S', 'W', 'rpt_R010006.html', 0, 43, $CREATE_DATE, 
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 6, 'H06 รายงานประวัติวุฒิการศึกษานักบริหาร', 'S', 'W', 'rpt_R010006.html', 0, 43, $CREATE_DATE, 
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'rpt_R010007.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 7, 'H07 จำนวนข้าราชการพลเรือน แยกตามเพศ', 'S', 'W', 'rpt_R010007.html', 0, 43, $CREATE_DATE, 
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 7, 'H07 จำนวนข้าราชการพลเรือน แยกตามเพศ', 'S', 'W', 'rpt_R010007.html', 0, 43, $CREATE_DATE, 
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'rpt_R010008.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 8, 'H08 จำนวนข้าราชการพลเรือน แยกตามระดับการศึกษา', 'S', 'W', 'rpt_R010008.html', 0, 43, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 8, 'H08 จำนวนข้าราชการพลเรือน แยกตามระดับการศึกษา', 'S', 'W', 'rpt_R010008.html', 0, 43, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'rpt_R010009.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 9, 'H09 จำนวนข้าราชการพลเรือน แยกตามระดับตำแหน่ง', 'S', 'W', 'rpt_R010009.html', 0, 43, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 9, 'H09 จำนวนข้าราชการพลเรือน แยกตามระดับตำแหน่ง', 'S', 'W', 'rpt_R010009.html', 0, 43, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'rpt_R010010.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 10, 'H10 บัญชีรายชื่อข้าราชการผู้มีสิทธิ์ได้รับบำเหน็จบำนาญซึ่งจะมีอายุครบ 60 ปีบริบูรณ์', 'S', 'W', 
							  'rpt_R010010.html', 0, 43, $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 10, 'H10 บัญชีรายชื่อข้าราชการผู้มีสิทธิ์ได้รับบำเหน็จบำนาญซึ่งจะมีอายุครบ 60 ปีบริบูรณ์', 'S', 'W', 
							  'rpt_R010010.html', 0, 43, $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'rpt_R010011.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 11, 'H11 รายชื่อข้าราชการที่ผ่านการอบรมหลักสูตร นบส. หรือเทียบเท่า', 'S', 'W', 'rpt_R010011.html', 0, 43, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 11, 'H11 รายชื่อข้าราชการที่ผ่านการอบรมหลักสูตร นบส. หรือเทียบเท่า', 'S', 'W', 'rpt_R010011.html', 0, 43, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'rpt_R010012.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 12, 'H12 จำนวนข้าราชการที่จะเกษียณอายุ ประจำปีงบประมาณ', 'S', 'W', 'rpt_R010012.html', 0, 43, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 12, 'H12 จำนวนข้าราชการที่จะเกษียณอายุ ประจำปีงบประมาณ', 'S', 'W', 'rpt_R010012.html', 0, 43, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'rpt_R010013.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 13, 'H13 รายงานผู้ดำรงตำแหน่งระดับสูงทุกประเภทตำแหน่ง ในส่วนราชการระดับกรม', 'S', 'W', 
							  'rpt_R010013.html', 0, 43, $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 13, 'H13 รายงานผู้ดำรงตำแหน่งระดับสูงทุกประเภทตำแหน่ง ในส่วนราชการระดับกรม', 'S', 'W', 
							  'rpt_R010013.html', 0, 43, $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'rpt_R010014.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 14, 'H14 รายชื่อข้าราชการพลเรือนสามัญดำรงตำแหน่งประเภท ~ ระดับ ~', 'S', 'W', 'rpt_R010014.html', 0, 43, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 14, 'H14 รายชื่อข้าราชการพลเรือนสามัญดำรงตำแหน่งประเภท ~ ระดับ ~', 'S', 'W', 'rpt_R010014.html', 0, 43, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'rpt_R010015.html' ";
		$count_data = $db->send_cmd($cmd);
		//$db->show_error();
		if (!$count_data) {
			$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV1 ";
			$db->send_cmd($cmd);
			//$db->show_error();
			$data = $db->get_array();
			$MAX_ID = $data[MAX_ID] + 1;
			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, 	TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'TH', $MAX_ID, 15, 'H15 รายชื่อผู้ดำรงตำแหน่งประเภทบริหาร', 'S', 'W', 'rpt_R010015.html', 0, 43, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, 	TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 15, 'H15 รายชื่อผู้ดำรงตำแหน่งประเภทบริหาร', 'S', 'W', 'rpt_R010015.html', 0, 43, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'rpt_R010016.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 16, 'H16 รายชื่อข้าราชการที่ดำรงตำแหน่งประเภทบริหาร ที่ดำรงตำแหน่งครบ ~ ปี', 'S', 'W', 
							  'rpt_R010016.html', 0, 43, $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 16, 'H16 รายชื่อข้าราชการที่ดำรงตำแหน่งประเภทบริหาร ที่ดำรงตำแหน่งครบ ~ ปี', 'S', 'W', 
							  'rpt_R010016.html', 0, 43, $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'rpt_R010017.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 17, 'H17 ข้อมูลสายงาน ประเภทตำแหน่ง แสดงวุฒิการศึกษาและความเชี่ยวชาญพิเศษ', 'S', 'W', 
							  'rpt_R010017.html', 0, 43, $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 17, 'H17 ข้อมูลสายงาน ประเภทตำแหน่ง แสดงวุฒิการศึกษาและความเชี่ยวชาญพิเศษ', 'S', 'W', 
							  'rpt_R010017.html', 0, 43, $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'rpt_R010018.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 18, 'H18 ผู้ดำรงตำแหน่งประเภทบริหาร อำนวยการระดับสูง วิชาการระดับเชี่ยวชาญขึ้นไปและทั่วไประดับทักษะพิเศษ', 
							  'S', 'W', 'rpt_R010018.html', 0, 43, $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 18, 'H18 ผู้ดำรงตำแหน่งประเภทบริหาร อำนวยการระดับสูง วิชาการระดับเชี่ยวชาญขึ้นไปและทั่วไประดับทักษะพิเศษ', 
							  'S', 'W', 'rpt_R010018.html', 0, 43, $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'rpt_R010019.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 19, 'H19 บัญชีรายชื่อข้าราชการ', 'S', 'W', 'rpt_R010019.html', 0, 43, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 19, 'H19 บัญชีรายชื่อข้าราชการ', 'S', 'W', 'rpt_R010019.html', 0, 43, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'rpt_R010020.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 20, 'H20 จำนวนข้าราชการที่จะเกษียณอายุ ประจำปี', 'S', 'W', 'rpt_R010020.html', 0, 43, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 20, 'H20 จำนวนข้าราชการที่จะเกษียณอายุ ประจำปี', 'S', 'W', 'rpt_R010020.html', 0, 43, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'rpt_R010021.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 21, 'H21 จำนวนข้าราชการที่จะเกษียณอายุ จำแนกตามตำแหน่งประเภท และเพศ', 'S', 'W', 'rpt_R010021.html', 0, 
							  43, $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 21, 'H21 จำนวนข้าราชการที่จะเกษียณอายุ จำแนกตามตำแหน่งประเภท และเพศ', 'S', 'W', 'rpt_R010021.html', 0, 
							  43, $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV1 WHERE MENU_LABEL = 'R10 รายงานเกี่ยวกับผู้บริหารระดับสูง' ";
		$db->send_cmd($cmd);
		//$db->show_error();

		$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_R010001.html' ";
		$db->send_cmd($cmd);
		//$db->show_error();

		$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_R010002.html' ";
		$db->send_cmd($cmd);
		//$db->show_error();

		$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_R010003.html' ";
		$db->send_cmd($cmd);
		//$db->show_error();

		$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_R010004.html' ";
		$db->send_cmd($cmd);
		//$db->show_error();

		$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_R010005.html' ";
		$db->send_cmd($cmd);
		//$db->show_error();

		$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_R010006.html' ";
		$db->send_cmd($cmd);
		//$db->show_error();

		$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_R010007.html' ";
		$db->send_cmd($cmd);
		//$db->show_error();

		$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_R010008.html' ";
		$db->send_cmd($cmd);
		//$db->show_error();

		$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_R010009.html' ";
		$db->send_cmd($cmd);
		//$db->show_error();

		$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_R010010.html' ";
		$db->send_cmd($cmd);
		//$db->show_error();

		$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_R010011.html' ";
		$db->send_cmd($cmd);
		//$db->show_error();

		$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_R010012.html' ";
		$db->send_cmd($cmd);
		//$db->show_error();

		$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_R010013.html' ";
		$db->send_cmd($cmd);
		//$db->show_error();

		$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_R010014.html' ";
		$db->send_cmd($cmd);
		//$db->show_error();

		$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_R010015.html' ";
		$db->send_cmd($cmd);
		//$db->show_error();

		$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_R010016.html' ";
		$db->send_cmd($cmd);
		//$db->show_error();

		$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_R010017.html' ";
		$db->send_cmd($cmd);
		//$db->show_error();

		$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_R010018.html' ";
		$db->send_cmd($cmd);
		//$db->show_error();

		$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_R010019.html' ";
		$db->send_cmd($cmd);
		//$db->show_error();

		$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_R010020.html' ";
		$db->send_cmd($cmd);
		//$db->show_error();

		$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_R010021.html' ";
		$db->send_cmd($cmd);
		//$db->show_error();

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_soc.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 8, 'P1108 ระบบเครื่องราชย์ของสำนักเลขาธิการคณะรัฐมนตรี', 'S', 'W', 'rpt_soc.html', 0, 35, 251, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
							  CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 8, 'P1108 ระบบเครื่องราชย์ของสำนักเลขาธิการคณะรัฐมนตรี', 'S', 'W', 'rpt_soc.html', 0, 35, 251, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET MENU_LABEL = 'M0302 ชื่อตำแหน่งในการบริหารงาน' 
						  WHERE LINKTO_WEB = 'master_table_mgt.html?table=PER_MGT' ";
		$db->send_cmd($cmd);
		//$db->show_error();

		$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET MENU_LABEL = 'M0306 หมวดตำแหน่งลูกจ้างประจำ' 
						  WHERE LINKTO_WEB = 'master_table_pos_group.html?table=PER_POS_GROUP' ";
		$db->send_cmd($cmd);
		//$db->show_error();

		$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET MENU_LABEL = 'M0307 ชื่อตำแหน่งลูกจ้างประจำ' 
						  WHERE LINKTO_WEB = 'master_table_pos_name.html?table=PER_POS_NAME' ";
		$db->send_cmd($cmd);
		//$db->show_error();

		$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET MENU_LABEL = 'P1104 รูปแบบพรบ.ระเบียบข้าราชการพลเรือน พ.ศ.2551' 
						  WHERE LINKTO_WEB = 'rpt_gfmis.html' ";
		$db->send_cmd($cmd);
		//$db->show_error();

		$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET MENU_LABEL = 'P1107 รูปแบบรายงานบริหารทรัพยากรบุคคลประจำปี (GPIS)' 
						  WHERE LINKTO_WEB = 'rpt_gpis.html' ";
		$db->send_cmd($cmd);
		//$db->show_error();

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'upload_pic.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 8, 'C0708 โลโก้ของส่วนราชการ', 'S', 'W', 'upload_pic.html', 0, 1, 305, $CREATE_DATE, 
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
							  CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 8, 'C0708 โลโก้ของส่วนราชการ', 'S', 'W', 'upload_pic.html', 0, 1, 305, $CREATE_DATE, 
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_R001004.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 4, 'R0104 จำนวนตำแหน่งจำแนกตามประเภทตำแหน่ง', 'S', 'W', 'rpt_R001004.html', 0, 36, 233, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
							  CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 4, 'R0104 จำนวนตำแหน่งจำแนกตามประเภทตำแหน่ง', 'S', 'W', 'rpt_R001004.html', 0, 36, 233, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_R006017.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 17, 'R0617 บัญชีรายละเอียดเงินเพิ่มการครองชีพชั่วคราว', 'S', 'W', 'rpt_R006017.html', 0, 36, 238, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
							  CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 17, 'R0617 บัญชีรายละเอียดเงินเพิ่มการครองชีพชั่วคราว', 'S', 'W', 'rpt_R006017.html', 0, 36, 238, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_R006018.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 18, 'R0618 รายชื่อข้าราชการ/ลูกจ้างประจำเพื่อพิจารณาเลื่อนขั้นเงินเดือน', 'S', 'W', 'rpt_R006018.html', 0, 36, 
							  238, $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
							  CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 18, 'R0618 รายชื่อข้าราชการ/ลูกจ้างประจำเพื่อพิจารณาเลื่อนขั้นเงินเดือน', 'S', 'W', 'rpt_R006018.html', 0, 36, 
							  238, $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV0 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
						  FLAG_SHOW, 	TYPE_LINKTO, LINKTO_TID, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
						  VALUES (1, 'TH', 46, 9, 'การบริหารค่าตอบแทน', 'S', 'N', 0, $CREATE_DATE, $CREATE_BY, $CREATE_DATE, 
						  $CREATE_BY) ";
		$db->send_cmd($cmd);
		//$db->show_error();

		$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV0 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
						  FLAG_SHOW, TYPE_LINKTO, LINKTO_TID, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
						  VALUES (1, 'EN', 46, 9, 'การบริหารค่าตอบแทน', 'S', 'N', 0, $CREATE_DATE, $CREATE_BY, $CREATE_DATE, 
						  $CREATE_BY) ";
		$db->send_cmd($cmd);
		//$db->show_error();

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 
						'master_table_assess_main.html?table=PER_ASSESS_MAIN' ";
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
							  VALUES (1, 'TH', $MAX_ID, 1, 'A01 ระดับผลการประเมินหลัก', 'S', 'W', 
							  'master_table_assess_main.html?table=PER_ASSESS_MAIN', 0, 46, $CREATE_DATE, $CREATE_BY, 
							  $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 1, 'A01 ระดับผลการประเมินหลัก', 'S', 'W', 
							  'master_table_assess_main.html?table=PER_ASSESS_MAIN', 0, 46, $CREATE_DATE, $CREATE_BY, 
							  $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 
						'master_table_assess_level.html?table=PER_ASSESS_LEVEL' ";
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
							  VALUES (1, 'TH', $MAX_ID, 2, 'A02 ระดับผลการประเมินย่อย', 'S', 'W', 
							  'master_table_assess_level.html?table=PER_ASSESS_LEVEL', 0, 46, $CREATE_DATE, $CREATE_BY, 
							  $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 2, 'A02 ระดับผลการประเมินย่อย', 'S', 'W', 
							  'master_table_assess_level.html?table=PER_ASSESS_LEVEL', 0, 46, $CREATE_DATE, $CREATE_BY, 
							  $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'personal_kpi.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 3, 'A03 ผลการประเมินการปฏิบัติราชการของข้าราชการ', 'S', 'W', 'personal_kpi.html', 
							  0, 46, $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 3, 'A03 ผลการประเมินการปฏิบัติราชการของข้าราชการ', 'S', 'W', 'personal_kpi.html', 
							  0, 46, $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'data_salary_formula.html' ";
		$count_data = $db->send_cmd($cmd);
		//$db->show_error();

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'data_compensation_test.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 5, 'A05 การบริหารวงเงินงบประมาณเลื่อนเงินเดือน', 'S', 'W', 'data_compensation_test.html', 
							  0, 46, $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 5, 'A05 การบริหารวงเงินงบประมาณเลื่อนเงินเดือน', 'S', 'W', 'data_compensation_test.html', 
							  0, 46, $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 
						  WHERE LINKTO_WEB = 'data_compensation_salpromote_comdtl.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 6, 'A06 สร้างบัญชีแนบท้ายคำสั่งเลื่อนเงินเดือน', 'S', 'W', 'data_compensation_salpromote_comdtl.html', 
							  0, 46, $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 6, 'A06 สร้างบัญชีแนบท้ายคำสั่งเลื่อนเงินเดือน', 'S', 'W', 'data_compensation_salpromote_comdtl.html', 
							  0, 46, $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'data_compensation_test_empser.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 7, 'A07 การบริหารวงเงินงบประมาณเลื่อนเงินเดือน (พนักงานราชการ)', 'S', 'W', 'data_compensation_test_empser.html', 
							  0, 46, $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 7, 'A07 การบริหารวงเงินงบประมาณเลื่อนเงินเดือน (พนักงานราชการ)', 'S', 'W', 'data_compensation_test_empser.html', 
							  0, 46, $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 
						  WHERE LINKTO_WEB = 'data_compensation_salpromote_comdtl_empser.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 8, 'A08 สร้างบัญชีแนบท้ายคำสั่งเลื่อนเงินเดือน (พนักงานราชการ)', 'S', 'W', 'data_compensation_salpromote_comdtl_empser.html', 
							  0, 46, $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 8, 'A08 สร้างบัญชีแนบท้ายคำสั่งเลื่อนเงินเดือน (พนักงานราชการ)', 'S', 'W', 'data_compensation_salpromote_comdtl_empser.html', 
							  0, 46, $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_kpr9.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 10, 'P1110 แบบ คปร.9', 'S', 'W', 'rpt_kpr9.html', 0, 35, 251, $CREATE_DATE, 
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
							  CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 10, 'P1110 แบบ คปร.9', 'S', 'W', 'rpt_kpr9.html', 0, 35, 251, $CREATE_DATE, 
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV0 WHERE MENU_ID = 44 AND MENU_LABEL = 'การฝึกอบรม' ";
		$db->send_cmd($cmd);
		//$db->show_error();

		$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV1 
						  WHERE LINKTO_WEB = 'master_table.html?table=PER_PROJECT_GROUP' ";
		$count_data = $db->send_cmd($cmd);
		//$db->show_error();

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 
						  WHERE LINKTO_WEB = 'master_table.html?table=PER_PROJECT_GROUP' ";
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
							  VALUES (1, 'TH', $MAX_ID, 7, 'M0707 หมวดโครงการ', 'S', 'W', 'master_table.html?table=PER_PROJECT_GROUP', 0, 9, 
							  293, $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
							  CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 7, 'M0707 หมวดโครงการ', 'S', 'W', 'master_table.html?table=PER_PROJECT_GROUP', 0, 9, 
							  293, $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV1 
						  WHERE LINKTO_WEB = 'master_table.html?table=PER_PROJECT_PAYMENT' ";
		$count_data = $db->send_cmd($cmd);
		//$db->show_error();

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 
						  WHERE LINKTO_WEB = 'master_table.html?table=PER_PROJECT_PAYMENT' ";
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
							  VALUES (1, 'TH', $MAX_ID, 8, 'M0708 หมวดค่าใช้จ่ายโครงการ', 'S', 'W', 
							  'master_table.html?table=PER_PROJECT_PAYMENT', 0, 9, 293, $CREATE_DATE, $CREATE_BY, 
							  $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
							  CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 8, 'M0708 หมวดค่าใช้จ่ายโครงการ', 'S', 'W', 
							  'master_table.html?table=PER_PROJECT_PAYMENT', 0, 9, 293, $CREATE_DATE, $CREATE_BY, 
							  $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'data_trainner.html' ";
		$count_data = $db->send_cmd($cmd);
		//$db->show_error();

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'data_trainner.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 6, 'P0806 วิทยากร', 'S', 'W', 'data_trainner.html', 0, 35, 248, $CREATE_DATE, 
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
							  CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 6, 'P0806 วิทยากร', 'S', 'W', 'data_trainner.html', 0, 35, 248, $CREATE_DATE, 
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'train_plan.html' ";
		$count_data = $db->send_cmd($cmd);
		//$db->show_error();

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'train_plan.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 7, 'P0807 แผนฝึกอบรมประจำปี', 'S', 'W', 'train_plan.html', 0, 35, 248, $CREATE_DATE, 
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
							  CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 7, 'P0807 แผนฝึกอบรมประจำปี', 'S', 'W', 'train_plan.html', 0, 35, 248, $CREATE_DATE, 
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'personal_competency.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 4, 'A04 สังกัดของการเลื่อนเงินเดือน', 'S', 'W', 'personal_competency.html', 
							  0, 46, $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 4, 'A04 สังกัดของการเลื่อนเงินเดือน', 'S', 'W', 'personal_competency.html', 
							  0, 46, $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 
						  WHERE LINKTO_WEB = 'kpi_standard_competence.html?table=PER_STANDARD_COMPETENCE' ";
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
							  VALUES (1, 'TH', $MAX_ID, 12, 'K12 มาตรฐานสมรรถนะของระดับตำแหน่ง', 'S', 'W', 
							  'kpi_standard_competence.html?table=PER_STANDARD_COMPETENCE', 0, 40, $CREATE_DATE, 
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 12, 'K12 มาตรฐานสมรรถนะของระดับตำแหน่ง', 'S', 'W', 
							  'kpi_standard_competence.html?table=PER_STANDARD_COMPETENCE', 0, 40, $CREATE_DATE, 
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		} 

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'personal_competency_assessment.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 13, 'K13 เปอร์เซ็นต์การประเมินผล', 'S', 'W', 'personal_competency_assessment.html', 0, 40, $CREATE_DATE, 
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 13, 'K13 เปอร์เซ็นต์การประเมินผล', 'S', 'W', 'personal_competency_assessment.html', 0, 40, $CREATE_DATE, 
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		} 

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'select_soc_excel.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 11, 'P1111 นำเข้าข้อมูลเครื่องราชย์จากสำนักเลขาธิการคณะรัฐมนตรี', 'S', 'W', 'select_soc_excel.html', 0, 35, 251, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
							  CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 11, 'P1111 นำเข้าข้อมูลเครื่องราชย์จากสำนักเลขาธิการคณะรัฐมนตรี', 'S', 'W', 'select_soc_excel.html', 0, 35, 251, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'personal_kpi_flag.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 14, 'K14 สังกัดของการประเมินผล', 'S', 'W', 'personal_kpi_flag.html', 0, 40, $CREATE_DATE, 
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 14, 'K14 สังกัดของการประเมินผล', 'S', 'W', 'personal_kpi_flag.html', 0, 40, $CREATE_DATE, 
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		} 

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'kpi_line_competence.html?table=PER_LINE_COMPETENCE' ";
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
							  VALUES (1, 'TH', $MAX_ID, 15, 'K15 สมรรถนะของแต่ละสายงาน', 'S', 'W', 'kpi_line_competence.html?table=PER_LINE_COMPETENCE', 0, 40, $CREATE_DATE, 
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 15, 'K15 สมรรถนะของแต่ละสายงาน', 'S', 'W', 'kpi_line_competence.html?table=PER_LINE_COMPETENCE', 0, 40, $CREATE_DATE, 
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		} 

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 
						WHERE LINKTO_WEB = 'kpi_pos_empser_competence.html?table=PER_POSITION_COMPETENCE' ";
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
							  VALUES (1, 'TH', $MAX_ID, 16, 'K16 มาตรฐานสมรรถนะของตำแหน่งพนักงานราชการ', 'S', 'W', 
							  'kpi_pos_empser_competence.html?table=PER_POSITION_COMPETENCE', 0, 40, $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 16, 'K16 มาตรฐานสมรรถนะของตำแหน่งพนักงานราชการ', 'S', 'W', 
							  'kpi_pos_empser_competence.html?table=PER_POSITION_COMPETENCE', 0, 40, $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		} 

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_epis.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 12, 'P1112 ระบบสารสนเทศพนักงานราชการ', 'S', 'W', 'rpt_epis.html', 0, 35, 251, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
							  CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 12, 'P1112 ระบบสารสนเทศพนักงานราชการ', 'S', 'W', 'rpt_epis.html', 0, 35, 251, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_cgd_salary.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 13, 'P1113 บัญชีรายละเอียดให้ข้าราชการพลเรือนสามัญได้รับการเลื่อนเงินเดือน (กรมบัญชีกลาง)', 'S', 'W', 'rpt_cgd_salary.html', 0, 35, 251, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
							  CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 13, 'P1113 บัญชีรายละเอียดให้ข้าราชการพลเรือนสามัญได้รับการเลื่อนเงินเดือน (กรมบัญชีกลาง)', 'S', 'W', 'rpt_cgd_salary.html', 0, 35, 251, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'convert_texttoslip.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 14, 'P1114 นำเข้าข้อมูลสลิปเงินเดือน (กรมบัญชีกลาง)', 'S', 'W', 'convert_texttoslip.html', 0, 35, 251, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
							  CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 14, 'P1114 นำเข้าข้อมูลสลิปเงินเดือน (กรมบัญชีกลาง)', 'S', 'W', 'convert_texttoslip.html', 0, 35, 251, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		} else {
			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET MENU_LABEL = 'P1114 นำเข้าข้อมูลสลิปเงินเดือน (กรมบัญชีกลาง)' 
							  WHERE LINKTO_WEB = 'convert_texttoslip.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'select_cgd_salary_xls.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 15, 'P1115 นำเข้าข้อมูลการเลื่อนเงินเดือน', 'S', 'W', 'select_cgd_salary_excel.html', 0, 35, 251, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
							  CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 15, 'P1115 นำเข้าข้อมูลการเลื่อนเงินเดือน', 'S', 'W', 'select_cgd_salary_excel.html', 0, 35, 251, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_R004096.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 96, 'R0496 รายชื่อข้าราชการ ตำแหน่ง สังกัด ที่ไปช่วยราชการ', 'S', 'W', 'rpt_R004096.html', 0, 36,
							  236, $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
							  CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 96, 'R0496 รายชื่อข้าราชการ ตำแหน่ง สังกัด ที่ไปช่วยราชการ', 'S', 'W', 'rpt_R004096.html', 0, 36,
							  236, $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_R004097.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 97, 'R0497 รายชื่อบุคลากรที่ดำรงตำแหน่งในระดับ ~ เรียงตามลำดับอาวุโส', 'S', 'W', 'rpt_R004097.html', 0, 36,
							  236, $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
							  CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 97, 'R0497 รายชื่อบุคลากรที่ดำรงตำแหน่งในระดับ ~ เรียงตามลำดับอาวุโส', 'S', 'W', 'rpt_R004097.html', 0, 36,
							  236, $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_R004098.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 98, 'R0498 ข้อมูลข้าราชการ ระดับตำแหน่ง ~', 'S', 'W', 'rpt_R004098.html', 0, 36,
							  236, $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
							  CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 98, 'R0498 ข้อมูลข้าราชการ ระดับตำแหน่ง ~', 'S', 'W', 'rpt_R004098.html', 0, 36,
							  236, $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_R004099.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 99, 'R0499 รายงานการพัฒนาข้าราชการ', 'S', 'W', 'rpt_R004099.html', 0, 36,
							  236, $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
							  CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 99, 'R0499 รายงานการพัฒนาข้าราชการ', 'S', 'W', 'rpt_R004099.html', 0, 36,
							  236, $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'data_move_org_comdtl.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 12, 'P0312 บัญชีแนบท้ายคำสั่งเปลี่ยนชื่อกรม', 'S', 'W', 
							  'data_move_org_comdtl.html', 0, 35, 243, $CREATE_DATE, $CREATE_BY, 
							  $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 		
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
							  CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 12, 'P0312 บัญชีแนบท้ายคำสั่งเปลี่ยนชื่อกรม', 'S', 'W', 
							  'data_move_org_comdtl.html', 0, 35, 243, $CREATE_DATE, $CREATE_BY, 
							  $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

?>