<?php 
add_field("PER_EXTRAHIS", "EXH_ORG_NAME","VARCHAR", "255", "NULL");
			add_field("PER_EXTRAHIS", "EXH_DOCNO","VARCHAR", "255", "NULL");
			add_field("PER_EXTRAHIS", "EXH_DOCDATE","VARCHAR", "19", "NULL");
			add_field("PER_EXTRAHIS", "EXH_SALARY","NUMBER", "16,2", "NULL");
			add_field("PER_EXTRAHIS", "EXH_REMARK","MEMO", "2000", "NULL");

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_EDUCATE ALTER EDU_STARTYEAR NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_EDUCATE MODIFY EDU_STARTYEAR NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_EDUCATE MODIFY EDU_STARTYEAR NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_EDUCATE ALTER EDU_ENDYEAR NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_EDUCATE MODIFY EDU_ENDYEAR NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_EDUCATE MODIFY EDU_ENDYEAR NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_EDUCATE", "EL_CODE","VARCHAR", "10", "NULL");
			add_field("PER_EDUCATE", "EDU_ENDDATE","VARCHAR", "19", "NULL");
			add_field("PER_EDUCATE", "EDU_GRADE","NUMBER", "6,2", "NULL");
			add_field("PER_EDUCATE", "EDU_HONOR","VARCHAR", "100", "NULL");
			add_field("PER_EDUCATE", "EDU_BOOK_NO","VARCHAR", "255", "NULL");
			add_field("PER_EDUCATE", "EDU_BOOK_DATE","VARCHAR", "19", "NULL");
			add_field("PER_EDUCATE", "EDU_REMARK","MEMO", "2000", "NULL");
			add_field("PER_EDUCATE", "EDU_INSTITUTE","VARCHAR", "255", "NULL");
			add_field("PER_WORKFLOW_EDUCATE", "EDU_INSTITUTE","VARCHAR", "255", "NULL");

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_TRAINING ALTER TRN_STARTDATE NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_TRAINING MODIFY TRN_STARTDATE NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_TRAINING MODIFY TRN_STARTDATE NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_TRAINING ALTER TRN_ENDDATE NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_TRAINING MODIFY TRN_ENDDATE NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_TRAINING MODIFY TRN_ENDDATE NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_TRAINING", "TRN_BOOK_NO","VARCHAR", "255", "NULL");
			add_field("PER_TRAINING", "TRN_BOOK_DATE","VARCHAR", "19", "NULL");
			add_field("PER_TRAINING", "TRN_PROJECT_NAME","VARCHAR", "255", "NULL");
			add_field("PER_TRAINING", "TRN_COURSE_NAME","VARCHAR", "255", "NULL");
			add_field("PER_TRAINING", "TRN_DEGREE_RECEIVE","VARCHAR", "100", "NULL");
			add_field("PER_TRAINING", "TRN_POINT","VARCHAR", "10", "NULL");
			add_field("PER_REWARDHIS", "REH_YEAR","VARCHAR", "4", "NULL");
			add_field("PER_REWARDHIS", "REH_PERFORMANCE","VARCHAR", "255", "NULL");
			add_field("PER_REWARDHIS", "REH_OTHER_PERFORMANCE","VARCHAR", "255", "NULL");
			add_field("PER_REWARDHIS", "REH_REMARK","MEMO", "2000", "NULL");
			add_field("PER_MARRHIS", "PN_CODE","VARCHAR", "10", "NULL");
			add_field("PER_MARRHIS", "MAH_MARRY_NO","VARCHAR", "100", "NULL");
			add_field("PER_MARRHIS", "MAH_MARRY_ORG","VARCHAR", "255", "NULL");
			add_field("PER_MARRHIS", "PV_CODE","VARCHAR", "10", "NULL");
			add_field("PER_MARRHIS", "MR_CODE","VARCHAR", "2", "NULL");
			add_field("PER_MARRHIS", "MAH_BOOK_NO","VARCHAR", "255", "NULL");
			add_field("PER_MARRHIS", "MAH_BOOK_DATE","VARCHAR", "19", "NULL");
			add_field("PER_MARRHIS", "MAH_REMARK","MEMO", "2000", "NULL");
			add_field("PER_NAMEHIS", "NH_ORG","VARCHAR", "255", "NULL");
			add_field("PER_NAMEHIS", "PN_CODE_NEW","VARCHAR", "3", "NULL");
			add_field("PER_NAMEHIS", "NH_NAME_NEW","VARCHAR", "50", "NULL");
			add_field("PER_NAMEHIS", "NH_SURNAME_NEW","VARCHAR", "50", "NULL");
			add_field("PER_NAMEHIS", "NH_BOOK_NO","VARCHAR", "255", "NULL");
			add_field("PER_NAMEHIS", "NH_BOOK_DATE","VARCHAR", "19", "NULL");
			add_field("PER_NAMEHIS", "NH_REMARK","MEMO", "2000", "NULL");
			add_field("PER_DECORATEHIS", "DEH_RECEIVE_DATE","VARCHAR", "19", "NULL");
			add_field("PER_DECORATEHIS", "DEH_BOOK_NO","VARCHAR", "255", "NULL");
			add_field("PER_DECORATEHIS", "DEH_BOOK_DATE","VARCHAR", "19", "NULL");
			add_field("PER_DECORATEHIS", "DEH_REMARK","MEMO", "2000", "NULL");
			add_field("PER_TIMEHIS", "TIMEH_BOOK_NO","VARCHAR", "255", "NULL");
			add_field("PER_TIMEHIS", "TIMEH_BOOK_DATE","VARCHAR", "19", "NULL");
			add_field("PER_WORKFLOW_TIMEHIS", "TIMEH_BOOK_NO","VARCHAR", "255", "NULL");
			add_field("PER_WORKFLOW_TIMEHIS", "TIMEH_BOOK_DATE","VARCHAR", "19", "NULL");
			add_field("PER_POSITION", "PAY_NO","VARCHAR", "15", "NULL");
			add_field("PER_PERSONAL", "PAY_ID","INTEGER", "10", "NULL");
			
			$cmd = " UPDATE PER_PERSONAL SET PAY_ID = POS_ID WHERE POS_ID IS NOT NULL AND PER_TYPE = 1 AND PER_STATUS =  1 AND PAY_ID IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_POS_EMP", "POEM_REMARK","MEMO", "2000", "NULL");
			add_field("PER_POS_EMPSER", "POEMS_REMARK","MEMO", "2000", "NULL");

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_REWARDHIS ALTER REH_ORG VARCHAR(255) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_REWARDHIS MODIFY REH_ORG VARCHAR2(255) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_REWARDHIS MODIFY REH_ORG VARCHAR(255) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REWARDHIS DROP CONSTRAINT FK2_PER_REWARDHIS ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_REWARD ALTER REW_CODE CHAR(10) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_REWARD MODIFY REW_CODE CHAR(10) ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_REWARD MODIFY REW_CODE CHAR(10) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_REWARDHIS ALTER REW_CODE CHAR(10) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_REWARDHIS MODIFY REW_CODE CHAR(10) ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_REWARDHIS MODIFY REW_CODE CHAR(10) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REWARDHIS ADD CONSTRAINT FK2_PER_REWARDHIS FOREIGN KEY (REW_CODE) REFERENCES PER_REWARD (REW_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_REWARDHIS ALTER REH_ORG VARCHAR(255) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_REWARDHIS MODIFY REH_ORG VARCHAR2(255) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_REWARDHIS MODIFY REH_ORG VARCHAR(255) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_WORKFLOW_REWARDHIS DROP CONSTRAINT FK2_PER_WORKFLOW_REWARDHIS ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_WORKFLOW_REWARDHIS ALTER REW_CODE CHAR(10) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_WORKFLOW_REWARDHIS MODIFY REW_CODE CHAR(10) ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_WORKFLOW_REWARDHIS MODIFY REW_CODE CHAR(10) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_WORKFLOW_REWARDHIS ADD CONSTRAINT FK2_PER_WORKFLOW_REWARDHIS FOREIGN KEY (REW_CODE) REFERENCES PER_REWARD (REW_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_EXTRATYPE ALTER EX_CODE CHAR(10) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_EXTRATYPE MODIFY EX_CODE CHAR(10) ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_EXTRATYPE MODIFY EX_CODE CHAR(10) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_EXTRATYPE", "EX_SHORTNAME","VARCHAR", "30", "NULL");
			add_field("PER_EXTRATYPE", "EX_REMARK","MEMO", "2000", "NULL");

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_EXTRAHIS ALTER EX_CODE CHAR(10) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_EXTRAHIS MODIFY EX_CODE CHAR(10) ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_EXTRAHIS MODIFY EX_CODE CHAR(10) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DROP INDEX INXU1_PER_EXTRATYPE ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX INXU1_PER_EXTRATYPE ON PER_EXTRATYPE (EX_NAME) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_KPI ADD CONSTRAINT FK1_PER_KPI 
							  FOREIGN KEY (KPI_PER_ID) REFERENCES PER_PERSONAL (PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_KPI ADD CONSTRAINT FK2_PER_KPI 
							  FOREIGN KEY (PFR_ID) REFERENCES PER_PERFORMANCE_REVIEW (PFR_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_KPI ADD CONSTRAINT FK3_PER_KPI 
							  FOREIGN KEY (KPI_ID_REF) REFERENCES PER_KPI (KPI_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_KPI ADD CONSTRAINT FK4_PER_KPI 
							  FOREIGN KEY (DEPARTMENT_ID) REFERENCES PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 
							  WHERE LINKTO_WEB = 'data_salreceive_comdtl.html' ";
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
								  VALUES (1, 'TH', $MAX_ID, 7, 'P0407 บัญชีแนบท้ายคำสั่งให้ได้รับเงินเดือนตามวุฒิ', 'S', 'W', 'data_salreceive_comdtl.html', 0, 35, 244, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 7, 'P0407 บัญชีแนบท้ายคำสั่งให้ได้รับเงินเดือนตามวุฒิ', 'S', 'W', 'data_salreceive_comdtl.html', 0, 35, 244, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'data_move_salary.html' ";
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
								  VALUES (1, 'TH', $MAX_ID, 8, 'P0408 บัญชีแนบท้ายคำสั่งตัดโอนอัตราเงินเดือนข้าราชการ', 'S', 'W', 'data_move_salary.html', 0, 35, 244, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 8, 'P0408 บัญชีแนบท้ายคำสั่งตัดโอนอัตราเงินเดือนข้าราชการ', 'S', 'W', 'data_move_salary.html', 0, 35, 244, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE MENU_LABEL = 'P15 รักษาราชการแทน/รักษาการในตำแหน่ง/ช่วยราชการ' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();
			if (!$count_data) {
				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, UPDATE_DATE, 
								  UPDATE_BY)
								  VALUES (1, 'TH', 388, 15, 'P15 รักษาราชการแทน/รักษาการในตำแหน่ง/ช่วยราชการ', 'S', 'N', 0, 35, $CREATE_DATE, $UPDATE_BY, 
								  $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, UPDATE_DATE, 
								  UPDATE_BY)
								  VALUES (1, 'EN', 388, 15, 'P15 รักษาราชการแทน/รักษาการในตำแหน่ง/ช่วยราชการ', 'S', 'N', 0, 35, $CREATE_DATE, $UPDATE_BY, 
								  $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'data_acting_comdtl.html' ";
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
								  VALUES (1, 'TH', $MAX_ID, 1, 'P1501 บัญชีแนบท้ายคำสั่งรักษาราชการแทน/รักษาการในตำแหน่ง', 'S', 'W', 'data_acting_comdtl.html', 0, 35, 388, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 1, 'P1501 บัญชีแนบท้ายคำสั่งรักษาราชการแทน/รักษาการในตำแหน่ง', 'S', 'W', 'data_acting_comdtl.html', 0, 35, 388, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'data_assign_comdtl.html' ";
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
								  VALUES (1, 'TH', $MAX_ID, 2, 'P1502 บัญชีแนบท้ายคำสั่งมอบหมายให้ปฏิบัติราชการ/ปฏิบัติราชการแทน', 'S', 'W', 'data_assign_comdtl.html', 0, 35,
								  388, $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
	
				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 2, 'P1502 บัญชีแนบท้ายคำสั่งมอบหมายให้ปฏิบัติราชการ/ปฏิบัติราชการแทน', 'S', 'W', 'data_assign_comdtl.html', 0, 35,
								  388, $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'data_help_comdtl.html' ";
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
								  VALUES (1, 'TH', $MAX_ID, 3, 'P1503 บัญชีแนบท้ายคำสั่งช่วยราชการ', 'S', 'W', 'data_help_comdtl.html', 0, 35,
								  388, $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
	
				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 3, 'P1503 บัญชีแนบท้ายคำสั่งช่วยราชการ', 'S', 'W', 'data_help_comdtl.html', 0, 35,
								  388, $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE MENU_LABEL = 'P16 บันทึกข้อมูลจากส่วนภูมิภาค' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();
			if (!$count_data) {
				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, UPDATE_DATE, 
								  UPDATE_BY)
								  VALUES (1, 'TH', 403, 16, 'P16 บันทึกข้อมูลจากส่วนภูมิภาค', 'S', 'W', 'personal_workflow.html', 0, 35, $CREATE_DATE, $UPDATE_BY, 
								  $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);	
				//$db->show_error();	

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, UPDATE_DATE, 
								  UPDATE_BY)
								  VALUES (1, 'EN', 403, 16, 'P16 บันทึกข้อมูลจากส่วนภูมิภาค', 'S', 'W', 'personal_workflow.html', 0, 35, $CREATE_DATE, $UPDATE_BY, 
								  $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE MENU_LABEL = 'P17 คำสั่งยกเลิกคำสั่งเดิม/แก้ไขคำสั่งที่ผิดพลาด' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();
			if (!$count_data) {
				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, UPDATE_DATE, 
								  UPDATE_BY)
								  VALUES (1, 'TH', 404, 17, 'P17 คำสั่งยกเลิกคำสั่งเดิม/แก้ไขคำสั่งที่ผิดพลาด', 'S', 'W', 0, 35, $CREATE_DATE, $UPDATE_BY, 
								  $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
		
				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, UPDATE_DATE, 
								  UPDATE_BY)
								  VALUES (1, 'EN', 404, 17, 'P17 คำสั่งยกเลิกคำสั่งเดิม/แก้ไขคำสั่งที่ผิดพลาด', 'S', 'W', 0, 35, $CREATE_DATE, $UPDATE_BY, 
								  $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE MENU_LABEL = 'R10 รายงานของกรมการปกครอง' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();
			if (!$count_data) {
				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, UPDATE_DATE, 
								  UPDATE_BY)
								  VALUES (1, 'TH', 405, 10, 'R10 รายงานของกรมการปกครอง', 'S', 'N', 0, 36, $CREATE_DATE, $UPDATE_BY, 
								  $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, UPDATE_DATE, 
								  UPDATE_BY)
								  VALUES (1, 'EN', 405, 10, 'R10 รายงานของกรมการปกครอง', 'S', 'N', 0, 36, $CREATE_DATE, $UPDATE_BY, 
								  $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);	
				//$db->show_error();
			}

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_R010031.html' ";
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
								  VALUES (1, 'TH', $MAX_ID, 1, 'R1031 จำนวนข้าราชการพลเรือน จำแนกตามตำแหน่งในการบริหารงานและระดับตำแหน่ง', 'S', 'W', 'rpt_R010031.html', 0, 36, 405, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 1, 'R1031 จำนวนข้าราชการพลเรือน จำแนกตามตำแหน่งในการบริหารงานและระดับตำแหน่ง', 'S', 'W', 'rpt_R010031.html', 0, 36, 405, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_R010033.html' ";
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
								  VALUES (1, 'TH', $MAX_ID, 3, 'R1033 จำนวนข้าราชการพลเรือน จำแนกตามตำแหน่งในการบริหารงานและสังกัด', 'S', 'W', 'rpt_R010033.html', 0, 36, 405, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 3, 'R1033 จำนวนข้าราชการพลเรือน จำแนกตามตำแหน่งในการบริหารงานและสังกัด', 'S', 'W', 'rpt_R010033.html', 0, 36, 405, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			add_field("PER_COMDTL", "AM_SHOW","SINGLE", "1", "NULL");
	
			$cmd = " update PER_CONTROL set CTRL_ALTER = 8 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
?>