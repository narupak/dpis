<?
/*
		$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV0 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
						  FLAG_SHOW, TYPE_LINKTO, LINKTO_TID, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
						  VALUES (1, 'TH', 41, 6, 'เครื่องบันทึกเวลา', 'S', 'N', 0, $CREATE_DATE, $CREATE_BY, $CREATE_DATE, 
						  $CREATE_BY) ";
		$db->send_cmd($cmd);
		//$db->show_error();

		$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV0 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
						  FLAG_SHOW, TYPE_LINKTO, LINKTO_TID, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
						  VALUES (1, 'EN', 41, 6, 'เครื่องบันทึกเวลา', 'S', 'N', 0, $CREATE_DATE, $CREATE_BY, $CREATE_DATE, 
						  $CREATE_BY) ";
		$db->send_cmd($cmd);
		//$db->show_error();

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 
						  WHERE LINKTO_WEB = 'select_database_attendance.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 1, 'T01 เลือกประเภทฐานข้อมูลเครื่องบันทึกเวลา', 'S', 'W', 'select_database_attendance.html', 0, 41, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 1, 'T01 เลือกประเภทฐานข้อมูลเครื่องบันทึกเวลา', 'S', 'W', 'select_database_attendance.html', 0, 41, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 
						  WHERE LINKTO_WEB = 'master_table.html?table=PER_WORK_LOCATION' ";
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
							  VALUES (1, 'TH', $MAX_ID, 2, 'T02 สถานที่ปฏิบัติราชการ', 'S', 'W', 'master_table.html?table=PER_WORK_LOCATION', 0, 41, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 2, 'T02 สถานที่ปฏิบัติราชการ', 'S', 'W', 'master_table.html?table=PER_WORK_LOCATION', 0, 41, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 
						  WHERE LINKTO_WEB = 'master_table_workcycle.html?table=PER_WORK_CYCLE' ";
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
							  VALUES (1, 'TH', $MAX_ID, 3, 'T03 รอบการมาปฏิบัติราชการ', 'S', 'W', 
							  'master_table_workcycle.html?table=PER_WORK_CYCLE', 0, 41, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 3, 'T03 รอบการมาปฏิบัติราชการ', 'S', 'W', 
							  'master_table_workcycle.html?table=PER_WORK_CYCLE', 0, 41, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 
						  WHERE LINKTO_WEB = 'master_table.html?table=PER_TIME_ATT' ";
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
							  VALUES (1, 'TH', $MAX_ID, 4, 'T04 เครื่องบันทึกเวลาการปฏิบัติงานอิเล็กทรอนิกส์', 'S', 'W', 
							  'master_table.html?table=PER_TIME_ATT', 0, 41, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 4, 'T04 เครื่องบันทึกเวลาการปฏิบัติงานอิเล็กทรอนิกส์', 'S', 'W', 
							  'master_table.html?table=PER_TIME_ATT', 0, 41, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 
						  WHERE LINKTO_WEB = 'master_table_worklate.html?table=PER_WORK_LATE' ";
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
							  VALUES (1, 'TH', $MAX_ID, 5, 'T05 เวลาสาย', 'S', 'W', 'master_table_worklate.html?table=PER_WORK_LATE', 0, 41, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 5, 'T05 เวลาสาย', 'S', 'W', 'master_table_worklate.html?table=PER_WORK_LATE', 0, 41, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'process_per_work_late.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 6, 'T06 สร้างเวลาสายล่วงหน้า', 'S', 'W', 'process_per_work_late.html', 0, 41, $CREATE_DATE, 
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 6, 'T06 สร้างเวลาสายล่วงหน้า', 'S', 'W', 'process_per_work_late.html', 0, 41, $CREATE_DATE, 
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 
						  WHERE LINKTO_WEB = 'process_per_time_attendance.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 7, 'T07 ถ่ายโอนข้อมูลจากเครื่องบันทึกเวลาการปฏิบัติงานอิเล็กทรอนิกส์', 'S', 'W', 
							  'process_per_time_attendance.html', 0, 41, $CREATE_DATE, $CREATE_BY, $CREATE_DATE, 
							  $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 7, 'T07 ถ่ายโอนข้อมูลจากเครื่องบันทึกเวลาการปฏิบัติงานอิเล็กทรอนิกส์', 'S', 'W', 
							  'process_per_time_attendance.html', 0, 41, $CREATE_DATE, $CREATE_BY, $CREATE_DATE, 
							  $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'data_time_attendance.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 8, 'T08 เวลาการมาปฏิบัติราชการจากเครื่องบันทึกเวลาการปฏิบัติงานอิเล็กทรอนิกส์', 'S', 'W', 
							  'data_time_attendance.html', 0, 41, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 8, 'T08 เวลาการมาปฏิบัติราชการจากเครื่องบันทึกเวลาการปฏิบัติงานอิเล็กทรอนิกส์', 'S', 'W', 
							  'data_time_attendance.html', 0, 41, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 
						  WHERE LINKTO_WEB = 'data_time_attendance_system.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 9, 'T09 ถ่ายโอนข้อมูลบุคลากรไปสู่โปรแกรม HIP Time Attendance System', 'S', 'W', 
							  'data_time_attendance_system.html', 0, 41, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 9, 'T09 ถ่ายโอนข้อมูลบุคลากรไปสู่โปรแกรม HIP Time Attendance System', 'S', 'W', 
							  'data_time_attendance_system.html', 0, 41, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 
						  WHERE LINKTO_WEB = 'process_time_attendance.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 10, 'T10 ยืนยันเวลาการมาปฏิบัติราชการ', 'S', 'W', 'process_time_attendance.html', 0, 41, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 10, 'T10 ยืนยันเวลาการมาปฏิบัติราชการ', 'S', 'W', 'process_time_attendance.html', 0, 41, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'personal_absent_work.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 11, 'T11 ตรวจสอบวันลาและวันที่มาปฏิบัติราชการ', 'S', 'W', 'personal_absent_work.html', 0, 41, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 11, 'T11 ตรวจสอบวันลาและวันที่มาปฏิบัติราชการ', 'S', 'W', 'personal_absent_work.html', 0, 41, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 
						  WHERE LINKTO_WEB = 'process_per_work_time.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 11, 'T12 สร้างวันที่มาปฏิบัติราชการล่วงหน้า', 'S', 'W', 'process_per_work_time.html', 0, 41, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
							  UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 11, 'T12 สร้างวันที่มาปฏิบัติราชการล่วงหน้า', 'S', 'W', 'process_per_work_time.html', 0, 41, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'rpt_map_per_type.html' ";
		$db->send_cmd($cmd);
		//$db->show_error();

		$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'rpt_map_per_formula.html' ";
		$db->send_cmd($cmd);
		//$db->show_error();

		$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'rpt_map_per_position.html' ";
		$db->send_cmd($cmd);
		//$db->show_error();

		$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV0 WHERE MENU_LABEL = 'รายงานสรุป' ";
		$db->send_cmd($cmd);
		//$db->show_error();

		$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'map_per_type.html' ";
		$db->send_cmd($cmd);
		//$db->show_error();

		$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'map_per_line.html' ";
		$db->send_cmd($cmd);
		//$db->show_error();

		$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'map_per_formula.html' ";
		$db->send_cmd($cmd);
		//$db->show_error();

		$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'map_per_co_level.html' ";
		$db->send_cmd($cmd);
		//$db->show_error();

		$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'map_per_position.html' ";
		$db->send_cmd($cmd);
		//$db->show_error();

		$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV0 WHERE MENU_LABEL = 'ตารางเทียบเคียง' ";
		$db->send_cmd($cmd);
		//$db->show_error();

		$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'master_table.html?table=PER_GROUP_N' ";
		$db->send_cmd($cmd);
		//$db->show_error();

		$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV1 
						  WHERE LINKTO_WEB = 'master_table_type_n.html?table=PER_TYPE_N' ";
		$db->send_cmd($cmd);
		//$db->show_error();

		$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'master_table_layer_n.html' ";
		$db->send_cmd($cmd);
		//$db->show_error();

		$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'master_table_line_n.html' ";
		$db->send_cmd($cmd);
		//$db->show_error();

		$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV0 WHERE MENU_LABEL = 'ประเภทตำแหน่ง/สายงาน (ใหม่)' ";
		$db->send_cmd($cmd);
		//$db->show_error();

		if($DPISDB=="odbc") 
			$cmd = " CREATE TABLE PER_WORK_LOCATION(
			WL_CODE VARCHAR(10) NOT NULL,	
			WL_NAME VARCHAR(100) NOT NULL,
			WL_ACTIVE SINGLE NOT NULL,
			UPDATE_USER INTEGER2 NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_WORK_LOCATION PRIMARY KEY (WL_CODE)) ";
		elseif($DPISDB=="oci8") 
			$cmd = " CREATE TABLE PER_WORK_LOCATION(
			WL_CODE VARCHAR2(10) NOT NULL,	
			WL_NAME VARCHAR2(100) NOT NULL,
			WL_ACTIVE NUMBER(1) NOT NULL,
			UPDATE_USER NUMBER(5) NOT NULL,
			UPDATE_DATE VARCHAR2(19) NOT NULL,		
			CONSTRAINT PK_PER_WORK_LOCATION PRIMARY KEY (WL_CODE)) ";
		elseif($DPISDB=="mysql")
			$cmd = " CREATE TABLE PER_WORK_LOCATION(
			WL_CODE VARCHAR(10) NOT NULL,	
			WL_NAME VARCHAR(100) NOT NULL,
			WL_ACTIVE SMALLINT(1) NOT NULL,
			UPDATE_USER SMALLINT(5) NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_WORK_LOCATION PRIMARY KEY (WL_CODE)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_WORK_LOCATION (WL_CODE, WL_NAME, WL_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES ('01', 'สำนักงานคณะกรรมการข้าราชการพลเรือน กรุงเทพฯ', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_WORK_LOCATION (WL_CODE, WL_NAME, WL_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES ('02', 'สำนักงานคณะกรรมการข้าราชการพลเรือน นนทบุรี', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " CREATE TABLE PER_WORK_CYCLE(
			WC_CODE VARCHAR(4) NOT NULL,	
			WC_NAME VARCHAR(100) NOT NULL,
			WC_START VARCHAR(4) NOT NULL,	
			WC_END VARCHAR(4) NOT NULL,	
			WC_ACTIVE SINGLE NOT NULL,
			UPDATE_USER INTEGER2 NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_WORK_CYCLE PRIMARY KEY (WC_CODE)) ";
		elseif($DPISDB=="oci8") 
			$cmd = " CREATE TABLE PER_WORK_CYCLE(
			WC_CODE VARCHAR2(4) NOT NULL,	
			WC_NAME VARCHAR2(100) NOT NULL,
			WC_START VARCHAR2(4) NOT NULL,	
			WC_END VARCHAR2(4) NOT NULL,	
			WC_ACTIVE NUMBER(1) NOT NULL,
			UPDATE_USER NUMBER(5) NOT NULL,
			UPDATE_DATE VARCHAR2(19) NOT NULL,		
			CONSTRAINT PK_PER_WORK_CYCLE PRIMARY KEY (WC_CODE)) ";
		elseif($DPISDB=="mysql")
			$cmd = " CREATE TABLE PER_WORK_CYCLE(
			WC_CODE VARCHAR(4) NOT NULL,	
			WC_NAME VARCHAR(100) NOT NULL,
			WC_START VARCHAR(4) NOT NULL,	
			WC_END VARCHAR(4) NOT NULL,	
			WC_ACTIVE SMALLINT(1) NOT NULL,
			UPDATE_USER SMALLINT(5) NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_WORK_CYCLE PRIMARY KEY (WC_CODE)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_WORK_CYCLE (WC_CODE, WC_NAME, WC_START, WC_END, WC_ACTIVE, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('01', 'รอบที่ 1 7:30 น.', '0730', '1530', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_WORK_CYCLE (WC_CODE, WC_NAME, WC_START, WC_END, WC_ACTIVE, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('02', 'รอบที่ 2 8:30 น.', '0830', '1630', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_WORK_CYCLE (WC_CODE, WC_NAME, WC_START, WC_END, WC_ACTIVE, UPDATE_USER, 
						  UPDATE_DATE)
						  VALUES ('03', 'รอบที่ 3 9:30 น.', '0930', '1730', 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
*/	
		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_R007004.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 4, 'R0704 รายงานการมาปฏิบัติราชการรายบุคคล', 'S', 'W', 'rpt_R007004.html', 0, 36, 239, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
							  CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 4, 'R0704 รายงานการมาปฏิบัติราชการรายบุคคล', 'S', 'W', 'rpt_R007004.html', 0, 36, 239, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_R007005.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 5, 'R0705 รายงานการมาปฏิบัติราชการ ประจำวัน', 'S', 'W', 'rpt_R007005.html', 0, 36, 239, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
							  CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 5, 'R0705 รายงานการมาปฏิบัติราชการ ประจำวัน', 'S', 'W', 'rpt_R007005.html', 0, 36, 239, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_R007006.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 6, 'R0706 รายงานการไม่บันทึกเวลาปฏิบัติราชการ', 'S', 'W', 'rpt_R007006.html', 0, 36, 239, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
							  CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 6, 'R0706 รายงานการไม่บันทึกเวลาปฏิบัติราชการ', 'S', 'W', 'rpt_R007006.html', 0, 36, 239, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_R007007.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 7, 'R0707 รายงานการลา สาย ขาดราชการ', 'S', 'W', 'rpt_R007007.html', 0, 36, 239, $CREATE_DATE, 
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
							  CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 7, 'R0707 รายงานการลา สาย ขาดราชการ', 'S', 'W', 'rpt_R007007.html', 0, 36, 239, $CREATE_DATE, 
							  $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_R007008.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 8, 'R0708 รายงานการปฏิบัติราชการนอกสถานที่', 'S', 'W', 'rpt_R007008.html', 0, 36, 239, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
							  CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 8, 'R0708 รายงานการปฏิบัติราชการนอกสถานที่', 'S', 'W', 'rpt_R007008.html', 0, 36, 239, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_R007009.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 9, 'R0709 รายงานรอบการมาปฏิบัติราชการรายบุคคล', 'S', 'W', 'rpt_R007009.html', 0, 36, 239, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
							  CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 9, 'R0709 รายงานรอบการมาปฏิบัติราชการรายบุคคล', 'S', 'W', 'rpt_R007009.html', 0, 36, 239, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_R007010.html' ";
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
							  VALUES (1, 'TH', $MAX_ID, 10, 'R0710 รายงานการมาสายจำแนกตามสำนัก/กอง', 'S', 'W', 'rpt_R007010.html', 0, 36, 239, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
							  CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 10, 'R0710 รายงานการมาสายจำแนกตามสำนัก/กอง', 'S', 'W', 'rpt_R007010.html', 0, 36, 239, 
							  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
		}
/*
		$cmd = " DROP TABLE PER_TIME_ATT ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
*/
		if($DPISDB=="odbc") 
			$cmd = " CREATE TABLE PER_TIME_ATT(
			TA_CODE VARCHAR(10) NOT NULL,	
			TA_NAME VARCHAR(100) NOT NULL,
			WL_CODE VARCHAR(10) NOT NULL,	
			TA_ACTIVE SINGLE NOT NULL,
			UPDATE_USER INTEGER2 NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_TIME_ATT PRIMARY KEY (TA_CODE)) ";
		elseif($DPISDB=="oci8") 
			$cmd = " CREATE TABLE PER_TIME_ATT(
			TA_CODE VARCHAR2(10) NOT NULL,	
			TA_NAME VARCHAR2(100) NOT NULL,
			WL_CODE VARCHAR2(10) NOT NULL,	
			TA_ACTIVE NUMBER(1) NOT NULL,
			UPDATE_USER NUMBER(5) NOT NULL,
			UPDATE_DATE VARCHAR2(19) NOT NULL,		
			CONSTRAINT PK_PER_TIME_ATT PRIMARY KEY (TA_CODE)) ";
		elseif($DPISDB=="mysql")
			$cmd = " CREATE TABLE PER_TIME_ATT(
			TA_CODE VARCHAR(10) NOT NULL,	
			TA_NAME VARCHAR(100) NOT NULL,
			WL_CODE VARCHAR(10) NOT NULL,	
			TA_ACTIVE SMALLINT(1) NOT NULL,
			UPDATE_USER SMALLINT(5) NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_TIME_ATT PRIMARY KEY (TA_CODE)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_TIME_ATT (TA_CODE, TA_NAME, WL_CODE, TA_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('1', 'เครื่องบันทึกเวลาการปฏิบัติงานอิเล็กทรอนิกส์ 1 (10.0.0.200)', '01', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_TIME_ATT (TA_CODE, TA_NAME, WL_CODE, TA_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('2', 'เครื่องบันทึกเวลาการปฏิบัติงานอิเล็กทรอนิกส์ 2 (10.0.0.201)', '01', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_TIME_ATT (TA_CODE, TA_NAME, WL_CODE, TA_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('3', 'เครื่องบันทึกเวลาการปฏิบัติงานอิเล็กทรอนิกส์ 3 (10.0.0.202)', '01', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_TIME_ATT (TA_CODE, TA_NAME, WL_CODE, TA_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('4', 'เครื่องบันทึกเวลาการปฏิบัติงานอิเล็กทรอนิกส์ 4 (10.0.0.203)', '01', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_TIME_ATT (TA_CODE, TA_NAME, WL_CODE, TA_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('5', 'เครื่องบันทึกเวลาการปฏิบัติงานอิเล็กทรอนิกส์ 5 (192.168.94.245)', '02', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_TIME_ATT (TA_CODE, TA_NAME, WL_CODE, TA_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('6', 'เครื่องบันทึกเวลาการปฏิบัติงานอิเล็กทรอนิกส์ 6 (192.168.94.246)', '02', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_TIME_ATT (TA_CODE, TA_NAME, WL_CODE, TA_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('7', 'เครื่องบันทึกเวลาการปฏิบัติงานอิเล็กทรอนิกส์ 7 (192.168.20.100)', '02', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
/*	
		if($DPISDB=="odbc") 
			$cmd = " CREATE TABLE PER_WORK_LATE(
			WL_CODE VARCHAR(10) NOT NULL,	
			WC_CODE VARCHAR(4) NOT NULL,	
			WORK_DATE VARCHAR(19) NOT NULL,
			LATE_TIME VARCHAR(4) NOT NULL,	
			LATE_REMARK VARCHAR(100) NULL,
			UPDATE_USER INTEGER2 NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_WORK_LATE PRIMARY KEY (WL_CODE, WC_CODE, WORK_DATE)) ";
		elseif($DPISDB=="oci8") 
			$cmd = " CREATE TABLE PER_WORK_LATE(
			WL_CODE VARCHAR2(10) NOT NULL,	
			WC_CODE VARCHAR2(4) NOT NULL,	
			WORK_DATE VARCHAR2(19) NOT NULL,
			LATE_TIME VARCHAR2(4) NOT NULL,	
			LATE_REMARK VARCHAR2(100) NULL,
			UPDATE_USER NUMBER(5) NOT NULL,
			UPDATE_DATE VARCHAR2(19) NOT NULL,		
			CONSTRAINT PK_PER_WORK_LATE PRIMARY KEY (WL_CODE, WC_CODE, WORK_DATE)) ";
		elseif($DPISDB=="mysql")
			$cmd = " CREATE TABLE PER_WORK_LATE(
			WL_CODE VARCHAR(10) NOT NULL,	
			WC_CODE VARCHAR(4) NOT NULL,	
			WORK_DATE VARCHAR(19) NOT NULL,
			LATE_TIME VARCHAR(4) NOT NULL,	
			LATE_REMARK VARCHAR(100) NULL,
			UPDATE_USER SMALLINT(5) NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_WORK_LATE PRIMARY KEY (WL_CODE, WC_CODE, WORK_DATE)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " CREATE TABLE PER_WORK_CYCLEHIS(
			WH_ID INTEGER NOT NULL,
			PER_ID INTEGER NOT NULL,
			WC_CODE VARCHAR(4) NOT NULL,	
			START_DATE VARCHAR(19) NOT NULL,
			END_DATE VARCHAR(19) NULL,
			REMARK VARCHAR(100) NULL,
			UPDATE_USER INTEGER2 NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_WORK_CYCLEHIS PRIMARY KEY (WH_ID)) ";
		elseif($DPISDB=="oci8") 
			$cmd = " CREATE TABLE PER_WORK_CYCLEHIS(
			WH_ID NUMBER(10) NOT NULL,
			PER_ID NUMBER(10) NOT NULL,
			WC_CODE VARCHAR2(4) NOT NULL,	
			START_DATE VARCHAR2(19) NOT NULL,
			END_DATE VARCHAR2(19) NULL,
			REMARK VARCHAR2(100) NULL,
			UPDATE_USER NUMBER(5) NOT NULL,
			UPDATE_DATE VARCHAR2(19) NOT NULL,		
			CONSTRAINT PK_PER_WORK_CYCLEHIS PRIMARY KEY (WH_ID)) ";
		elseif($DPISDB=="mysql")
			$cmd = " CREATE TABLE PER_WORK_CYCLEHIS(
			WH_ID INTEGER(10) NOT NULL,
			PER_ID INTEGER(10) NOT NULL,
			WC_CODE VARCHAR(4) NOT NULL,	
			START_DATE VARCHAR(19) NOT NULL,
			END_DATE VARCHAR(19) NULL,
			REMARK VARCHAR(100) NULL,
			UPDATE_USER SMALLINT(5) NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_WORK_CYCLEHIS PRIMARY KEY (WH_ID)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " CREATE TABLE PER_TIME_ATTENDANCE(
			PER_ID INTEGER NOT NULL,
			TIME_STAMP VARCHAR(19) NOT NULL,
			TA_CODE VARCHAR(10) NOT NULL,	
			UPDATE_USER INTEGER2 NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_TIME_ATTENDANCE PRIMARY KEY (PER_ID, TIME_STAMP)) ";
		elseif($DPISDB=="oci8") 
			$cmd = " CREATE TABLE PER_TIME_ATTENDANCE(
			PER_ID NUMBER(10) NOT NULL,
			TIME_STAMP VARCHAR2(19) NOT NULL,
			TA_CODE VARCHAR2(10) NOT NULL,	
			UPDATE_USER NUMBER(5) NOT NULL,
			UPDATE_DATE VARCHAR2(19) NOT NULL,		
			CONSTRAINT PK_PER_TIME_ATTENDANCE PRIMARY KEY (PER_ID, TIME_STAMP)) ";
		elseif($DPISDB=="mysql")
			$cmd = " CREATE TABLE PER_TIME_ATTENDANCE(
			PER_ID INTEGER(10) NOT NULL,
			TIME_STAMP VARCHAR(19) NOT NULL,
			TA_CODE VARCHAR(10) NOT NULL,	
			UPDATE_USER SMALLINT(5) NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_TIME_ATTENDANCE PRIMARY KEY (PER_ID, TIME_STAMP)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " CREATE TABLE PER_WORK_TIME(
			WT_ID INTEGER NOT NULL,
			PER_ID INTEGER NOT NULL,
			WL_CODE VARCHAR(10) NOT NULL,	
			WC_CODE VARCHAR(4) NOT NULL,	
			START_DATE VARCHAR(19) NOT NULL,
			END_DATE VARCHAR(19) NULL,
			HOLIDAY_FLAG SINGLE NULL,
			ABSENT_FLAG SINGLE NULL,
			REMARK VARCHAR(100) NULL,
			UPDATE_USER INTEGER2 NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_WORK_TIME PRIMARY KEY (WT_ID)) ";
		elseif($DPISDB=="oci8") 
			$cmd = " CREATE TABLE PER_WORK_TIME(
			WT_ID NUMBER(10) NOT NULL,
			PER_ID NUMBER(10) NOT NULL,
			WL_CODE VARCHAR2(10) NOT NULL,	
			WC_CODE VARCHAR2(4) NOT NULL,	
			START_DATE VARCHAR2(19) NOT NULL,
			END_DATE VARCHAR2(19) NULL,
			HOLIDAY_FLAG NUMBER(1) NULL,
			ABSENT_FLAG NUMBER(1) NULL,
			REMARK VARCHAR2(100) NULL,
			UPDATE_USER NUMBER(5) NOT NULL,
			UPDATE_DATE VARCHAR2(19) NOT NULL,		
			CONSTRAINT PK_PER_WORK_TIME PRIMARY KEY (WT_ID)) ";
		elseif($DPISDB=="mysql")
			$cmd = " CREATE TABLE PER_WORK_TIME(
			WT_ID INTEGER(10) NOT NULL,
			PER_ID INTEGER(10) NOT NULL,
			WL_CODE VARCHAR(10) NOT NULL,	
			WC_CODE VARCHAR(4) NOT NULL,	
			START_DATE VARCHAR(19) NOT NULL,
			END_DATE VARCHAR(19) NULL,
			HOLIDAY_FLAG SMALLINT(1) NULL,
			ABSENT_FLAG SMALLINT(1) NULL,
			REMARK VARCHAR(100) NULL,
			UPDATE_USER SMALLINT(5) NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_WORK_TIME PRIMARY KEY (WT_ID)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " CREATE TABLE PER_ABSENTSUM(
			AS_ID INTEGER NOT NULL,
			PER_ID INTEGER NOT NULL,
			AS_YEAR VARCHAR(4) NOT NULL,	
			AS_CYCLE SINGLE NULL,
			START_DATE VARCHAR(19) NOT NULL,
			END_DATE VARCHAR(19) NULL,
			AS_SICK_DAY INTEGER2 NULL,
			AS_SICK_NO INTEGER2 NULL,
			AS_BUSY_DAY INTEGER2 NULL,
			AS_BUSY_NO INTEGER2 NULL,
			AS_DEL_DAY INTEGER2 NULL,
			AS_DEL_NO INTEGER2 NULL,
			AS_LATE_DAY INTEGER2 NULL,
			AS_LATE_NO INTEGER2 NULL,
			AS_RELAX_DAY INTEGER2 NULL,
			UPDATE_USER INTEGER2 NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_ABSENTSUM PRIMARY KEY (AS_ID)) ";
		elseif($DPISDB=="oci8") 
			$cmd = " CREATE TABLE PER_ABSENTSUM(
			AS_ID NUMBER(10) NOT NULL,
			PER_ID NUMBER(10) NOT NULL,
			AS_YEAR VARCHAR2(4) NOT NULL,	
			AS_CYCLE NUMBER(1) NULL,
			START_DATE VARCHAR2(19) NOT NULL,
			END_DATE VARCHAR2(19) NULL,
			AS_SICK_DAY NUMBER(3) NULL,
			AS_SICK_NO NUMBER(3) NULL,
			AS_BUSY_DAY NUMBER(3) NULL,
			AS_BUSY_NO NUMBER(3) NULL,
			AS_DEL_DAY NUMBER(3) NULL,
			AS_DEL_NO NUMBER(3) NULL,
			AS_LATE_DAY NUMBER(3) NULL,
			AS_LATE_NO NUMBER(3) NULL,
			AS_RELAX_DAY NUMBER(3) NULL,
			UPDATE_USER NUMBER(5) NOT NULL,
			UPDATE_DATE VARCHAR2(19) NOT NULL,		
			CONSTRAINT PK_PER_ABSENTSUM PRIMARY KEY (AS_ID)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
*/
?>