<?php 
if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_KPI ALTER KPI_NAME MEMO ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_KPI MODIFY KPI_NAME VARCHAR2(1000) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_KPI MODIFY KPI_NAME TEXT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'rpt_geis.html' WHERE LINKTO_WEB = 'rpt_epis.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " SELECT AB_COUNT_01 FROM PER_ABSENTSUM ";
			$count_data = $db_dpis->send_cmd($cmd);

			if (!$count_data) {
				$cmd = " DROP TABLE PER_ABSENTSUM ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				if($DPISDB=="odbc") 
					$cmd = " CREATE TABLE PER_ABSENTSUM(
					AS_ID INTEGER NOT NULL,
					PER_ID INTEGER NOT NULL,
					PER_CARDNO VARCHAR(13) NULL,
					AS_YEAR VARCHAR(4) NOT NULL,	
					AS_CYCLE SINGLE NOT NULL,	
					START_DATE VARCHAR(19) NOT NULL,
					END_DATE VARCHAR(19) NULL,
					AB_CODE_01 NUMBER NULL,
					AB_COUNT_01 NUMBER NULL,
					AB_CODE_02 NUMBER NULL,
					AB_CODE_03 NUMBER NULL,
					AB_COUNT_03 NUMBER NULL,
					AB_CODE_04 NUMBER NULL,
					AB_CODE_05 NUMBER NULL,
					AB_CODE_06 NUMBER NULL,
					AB_CODE_07 NUMBER NULL,
					AB_CODE_08 NUMBER NULL,
					AB_CODE_09 NUMBER NULL,
					AB_CODE_10 NUMBER NULL,
					AB_CODE_11 NUMBER NULL,
					AB_CODE_12 NUMBER NULL,
					AB_CODE_13 NUMBER NULL,
					AB_CODE_14 NUMBER NULL,
					AB_CODE_15 NUMBER NULL,
					INCREASE_AB_CODE_04 NUMBER NULL,
					REMAIN_AB_CODE_04 NUMBER NULL,
					TOTAL_LEAVE NUMBER NULL,
					TOTAL_COUNT NUMBER NULL,
					UPDATE_USER INTEGER NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_ABSENTSUM PRIMARY KEY (AS_ID)) ";
				elseif($DPISDB=="oci8") 
					$cmd = " CREATE TABLE PER_ABSENTSUM(
					AS_ID NUMBER(10) NOT NULL,
					PER_ID NUMBER(10) NOT NULL,
					PER_CARDNO VARCHAR2(13) NULL,
					AS_YEAR VARCHAR2(4) NOT NULL,	
					AS_CYCLE NUMBER(1) NOT NULL,	
					START_DATE VARCHAR2(19) NOT NULL,
					END_DATE VARCHAR2(19) NULL,
					AB_CODE_01 NUMBER(6,2) NULL,
					AB_COUNT_01 NUMBER(3) NULL,
					AB_CODE_02 NUMBER(6,2) NULL,
					AB_CODE_03 NUMBER(6,2) NULL,
					AB_COUNT_03 NUMBER(3) NULL,
					AB_CODE_04 NUMBER(6,2) NULL,
					AB_CODE_05 NUMBER(6,2) NULL,
					AB_CODE_06 NUMBER(6,2) NULL,
					AB_CODE_07 NUMBER(6,2) NULL,
					AB_CODE_08 NUMBER(6,2) NULL,
					AB_CODE_09 NUMBER(6,2) NULL,
					AB_CODE_10 NUMBER(6,2) NULL,
					AB_CODE_11 NUMBER(6,2) NULL,
					AB_CODE_12 NUMBER(6,2) NULL,
					AB_CODE_13 NUMBER(6,2) NULL,
					AB_CODE_14 NUMBER(6,2) NULL,
					AB_CODE_15 NUMBER(6,2) NULL,
					INCREASE_AB_CODE_04 NUMBER(6,2) NULL,
					REMAIN_AB_CODE_04 NUMBER(6,2) NULL,
					TOTAL_LEAVE NUMBER(6,2) NULL,
					TOTAL_COUNT NUMBER(6,2) NULL,
					UPDATE_USER NUMBER(11) NOT NULL,
					UPDATE_DATE VARCHAR2(19) NOT NULL,		
					CONSTRAINT PK_PER_ABSENTSUM PRIMARY KEY (AS_ID)) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			} // end if

			add_field("PER_KPI_FORM", "ORG_ID_1_ASS","INTEGER", "10", "NULL");

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_TRAINING ALTER TR_CODE NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_TRAINING MODIFY TR_CODE NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_TRAINING MODIFY TR_CODE NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_COURSE ALTER TR_CODE NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COURSE MODIFY TR_CODE NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_COURSE MODIFY TR_CODE NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_COURSE ALTER CO_NO NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COURSE MODIFY CO_NO NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_COURSE MODIFY CO_NO NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET MENU_LABEL = 'P1108 ระบบเครื่องราชฯ ของสำนักเลขาธิการคณะรัฐมนตรี' 
							  WHERE LINKTO_WEB = 'rpt_soc.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET MENU_LABEL = 'P1111 นำเข้าข้อมูลเครื่องราชฯ จากสำนักเลขาธิการคณะรัฐมนตรี' 
							  WHERE LINKTO_WEB = 'select_soc_excel.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET MENU_LABEL = 'P1004 ออกแบบบัตรประจำตัวเจ้าหน้าที่ของรัฐ' 
							  WHERE LINKTO_WEB = 'per_percard_design.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET MENU_LABEL = 'P1005 พิมพ์บัตรประจำตัวเจ้าหน้าที่ของรัฐ' 
							  WHERE LINKTO_WEB = 'per_percard_print.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'data_trainner.html' WHERE LINKTO_WEB = 'data_trainner_tab.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV1 SET LINKTO_WEB = 'kpi_line_competence.html?table=PER_LINE_COMPETENCE' 
							  WHERE LINKTO_WEB = 'kpi_line_competence_tab.html?table=PER_LINE_COMPETENCE' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO PER_ABSENTTYPE (AB_CODE, AB_NAME, AB_QUOTA, AB_COUNT, AB_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('12', 'ลาป่วยจำเป็น', 0, 1, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_ABSENTTYPE (AB_CODE, AB_NAME, AB_QUOTA, AB_COUNT, AB_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('13', 'ขาดราชการ', 0, 1, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_ABSENTTYPE (AB_CODE, AB_NAME, AB_QUOTA, AB_COUNT, AB_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('14', 'ลาไปช่วยเหลือภริยาที่คลอดบุตร', 0, 1, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_ABSENTTYPE (AB_CODE, AB_NAME, AB_QUOTA, AB_COUNT, AB_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('15', 'ลาไปฟื้นฟูสมรรถภาพด้านอาชีพ', 0, 1, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, UPDATE_DATE, MOV_SUB_TYPE)
							  VALUES ('11899', 'ลาออก (ไม่นับอายุราชการ)', 1, 1, $SESS_USERID, '$UPDATE_DATE', 9) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " SELECT POS_ID, LEVEL_NO, PT_CODE FROM PER_POSITION WHERE LEVEL_NO IN ('O4', 'K2', 'K3', 'K4', 'K5', 'D1', 'D2', 'M1', 'M2') AND PT_CODE IN ('12', '21', '22', '31', '32') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			while($data = $db_dpis->get_array()){
				$POS_ID = $data[POS_ID];
				$LEVEL_NO = trim($data[LEVEL_NO]);
				$PT_CODE = trim($data[PT_CODE]);
				$EX_CODE = ""; 
				if ($LEVEL_NO=="O4") $EX_CODE = ""; 
				elseif ($LEVEL_NO=="K2" && $PT_CODE=="21") $EX_CODE = "020"; 
				elseif ($LEVEL_NO=="K3" && $PT_CODE=="21") $EX_CODE = "200"; 
				elseif ($LEVEL_NO=="K4") $EX_CODE = "011"; 
				elseif ($LEVEL_NO=="K5") $EX_CODE = ""; 
				elseif ($LEVEL_NO=="D1") $EX_CODE = "010"; 
				elseif ($LEVEL_NO=="D2") $EX_CODE = "017"; 
				elseif ($LEVEL_NO=="M1") $EX_CODE = ""; 
				elseif ($LEVEL_NO=="M2") $EX_CODE = "023"; 

				$cmd = " SELECT POS_ID FROM PER_POS_MGTSALARY 	WHERE POS_ID = $POS_ID AND trim(EX_CODE) = '$EX_CODE' ";
				$count_data = $db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
				if (!$count_data) {
					$cmd = " INSERT INTO  PER_POS_MGTSALARY (POS_ID, EX_CODE, POS_STARTDATE, POS_STATUS, UPDATE_USER, UPDATE_DATE)
									  VALUES ($POS_ID, '$EX_CODE', NULL, 1, $SESS_USERID, '$UPDATE_DATE') ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
				} // end if

				$EX_CODE = ""; 
				if ($LEVEL_NO=="K3" && $PT_CODE=="21") $EX_CODE = "201"; 
				elseif ($LEVEL_NO=="K4") $EX_CODE = "196"; 
				elseif ($LEVEL_NO=="D1") $EX_CODE = "195"; 
				elseif ($LEVEL_NO=="D2") $EX_CODE = "194"; 
				elseif ($LEVEL_NO=="M1") $EX_CODE = ""; 
				elseif ($LEVEL_NO=="M2") $EX_CODE = "193"; 

				$cmd = " SELECT POS_ID FROM PER_POS_MGTSALARY 	WHERE POS_ID = $POS_ID AND trim(EX_CODE) = '$EX_CODE' ";
				$count_data = $db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
				if (!$count_data) {
					$cmd = " INSERT INTO  PER_POS_MGTSALARY (POS_ID, EX_CODE, POS_STARTDATE, POS_STATUS, UPDATE_USER, UPDATE_DATE)
									  VALUES ($POS_ID, '$EX_CODE', NULL, 1, $SESS_USERID, '$UPDATE_DATE') ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
				} // end if

			} // end while

			$old_code = array(	"ทั่วไประดับปฏิบัติงาน", 
													"ประเภททั่วไป - ปฏิบัติงาน", 
													"ประเภททั่วไป ระดับปฏิบัติงาน", 
													"ระดับปฏิบัติงาน", 
													"ทั่วไประดับชำนาญงาน", 
													"ประเภททั่วไป - ชำนาญงาน", 
													"ประเภททั่วไป ระดับชำนาญงาน", 
													"ระดับชำนาญงาน", 
													"ทั่วไประดับอาวุโส", 
													"ประเภททั่วไป - อาวุโส", 
													"ประเภททั่วไป ระดับอาวุโส", 
													"ระดับอาวุโส", 
													"ทั่วไประดับทักษะพิเศษ", 
													"ประเภททั่วไป - ทักษะพิเศษ", 
													"ประเภททั่วไป ระดับทักษะพิเศษ", 
													"ระดับทักษะพิเศษ",
													"วิชาการระดับปฏิบัติการ", 
													"ประเภทวิชาการ - ปฏิบัติการ", 
													"ประเภทวิชาการ ระดับปฏิบัติการ", 
													"ระดับปฏิบัติการ", 
													"วิชาการระดับชำนาญการ", 
													"ประเภทวิชาการ - ชำนาญการ", 
													"ประเภทวิชาการ ระดับชำนาญการ", 
													"ระดับชำนาญการ", 
													"วิชาการระดับชำนาญการพิเศษ", 
													"ประเภทวิชาการ - ชำนาญการพิเศษ", 
													"ประเภทวิชาการ ระดับชำนาญการพิเศษ", 
													"ระดับชำนาญการพิเศษ", 
													"วิชาการระดับเชี่ยวชาญ", 
													"ประเภทวิชาการ - เชี่ยวชาญ", 
													"ประเภทวิชาการ ระดับเชี่ยวชาญ", 
													"ระดับเชี่ยวชาญ", 
													"วิชาการระดับทรงคุณวุฒิ", 
													"ประเภทวิชาการ - ทรงคุณวุฒิ", 
													"ประเภทวิชาการ ระดับทรงคุณวุฒิ", 
													"ระดับทรงคุณวุฒิ", 
													"ประเภทอำนวยการ ระดับต้น", 
													"อำนวยการ ระดับต้น", 
													"ประเภทอำนวยการ ระดับสูง", 
													"อำนวยการ ระดับสูง", 
													"บริหาร ระดับต้น", 
													"ประเภทบริหาร ระดับต้น", 
													"บริหาร ระดับสูง", 
													"ประเภทบริหาร - ระดับสูง", 
													"ประเภทบริหาร ระดับสูง", 
													"อำนวยการต้น - อำนวยการสูง", 
													"เชี่ยวชาญ - ทรงคุณวุฒิ"	);
			$new_code = array(	"ปฏิบัติงาน", "ปฏิบัติงาน", "ปฏิบัติงาน", "ปฏิบัติงาน", "ชำนาญงาน", "ชำนาญงาน", "ชำนาญงาน", "ชำนาญงาน", "อาวุโส", "อาวุโส", "อาวุโส", "อาวุโส", 
													"ทักษะพิเศษ", "ทักษะพิเศษ", "ทักษะพิเศษ", "ทักษะพิเศษ", "ปฏิบัติการ", "ปฏิบัติการ", "ปฏิบัติการ", "ปฏิบัติการ", "ชำนาญการ", "ชำนาญการ", "ชำนาญการ", "ชำนาญการ", 
													"ชำนาญการพิเศษ", "ชำนาญการพิเศษ", "ชำนาญการพิเศษ", "ชำนาญการพิเศษ", "เชี่ยวชาญ", "เชี่ยวชาญ", "เชี่ยวชาญ", "เชี่ยวชาญ", "ทรงคุณวุฒิ", "ทรงคุณวุฒิ", "ทรงคุณวุฒิ", "ทรงคุณวุฒิ", 
													"อำนวยการต้น", "อำนวยการต้น", "อำนวยการสูง", "อำนวยการสูง", "บริหารต้น", "บริหารต้น", "บริหารสูง", "บริหารสูง", "บริหารสูง", "อำนวยการต้น หรือ อำนวยการสูง", "เชี่ยวชาญ หรือ ทรงคุณวุฒิ" );
			for ( $i=0; $i<count($old_code); $i++ ) { 
				$cmd = " select count(cl_name) as COUNT_DATA from per_co_level where cl_name='$old_code[$i]' or cl_name='$new_code[$i]' ";
				$db_dpis->send_cmd($cmd);
	//			$db_dpis->show_error();
				$data = $db_dpis->get_array();
				$COUNT_DATA = $data[COUNT_DATA];
				if ($COUNT_DATA==2) {
					$cmd = " update per_position set cl_name='$new_code[$i]' where cl_name='$old_code[$i]' ";
					$db_dpis->send_cmd($cmd);
	//				$db_dpis->show_error();
				
					$cmd = " update per_pos_mov set cl_name='$new_code[$i]' where cl_name='$old_code[$i]' ";
					$db_dpis->send_cmd($cmd);
	//				$db_dpis->show_error();

					$cmd = " update per_order_dtl set cl_name='$new_code[$i]' where cl_name='$old_code[$i]' ";
					$db_dpis->send_cmd($cmd);
	//				$db_dpis->show_error();

					$cmd = " update per_req3_dtl set cl_name='$new_code[$i]' where cl_name='$old_code[$i]' ";
					$db_dpis->send_cmd($cmd);
	//				$db_dpis->show_error();

					$cmd = " update per_req2_dtl set cl_name='$new_code[$i]' where cl_name='$old_code[$i]' ";
					$db_dpis->send_cmd($cmd);
	//				$db_dpis->show_error();

					$cmd = " update per_req1_dtl1 set cl_name='$new_code[$i]' where cl_name='$old_code[$i]' ";
					$db_dpis->send_cmd($cmd);
	//				$db_dpis->show_error();

					$cmd = " update per_sum_dtl2 set cl_name='$new_code[$i]' where cl_name='$old_code[$i]' ";
					$db_dpis->send_cmd($cmd);
	//				$db_dpis->show_error();
					
					$cmd = " delete from per_co_level where cl_name='$old_code[$i]' ";
					$db_dpis->send_cmd($cmd);
	//				$db_dpis->show_error();
				}
			}

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_ORG ALTER ORG_CODE NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_ORG MODIFY ORG_CODE NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_ORG MODIFY ORG_CODE NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_ORG_ASS ALTER ORG_CODE NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_ORG_ASS MODIFY ORG_CODE NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_ORG_ASS MODIFY ORG_CODE NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMGROUP (COM_GROUP, CG_NAME, CG_ACTIVE, UPDATE_USER, UPDATE_DATE, CG_SEQ_NO)
							  VALUES ('524', 'คำสั่งปรับอัตราเงินเดือน', 1, $SESS_USERID, '$UPDATE_DATE', 24) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			if($BKK_FLAG==1) {
/*				$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'manage_data_restore.html' ";
				$count_data = $db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'manage_data_backup.html' ";
				$count_data = $db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'select_database_dpis.html' ";
				$count_data = $db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'select_database_nondpis.html' ";
				$count_data = $db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'dpis_to_text.html' ";
				$count_data = $db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'text_to_ppis.html' ";
				$count_data = $db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'mysql_to_oracle.html' ";
				$count_data = $db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('11001', 'นักบริหาร', 'นักบริหาร', 1, $SESS_USERID, '$UPDATE_DATE', 4, 'บริหารต้น หรือ บริหารสูง', 1) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('11002', 'ผู้ตรวจราชการกรุงเทพมหานคร', 'ผู้ตรวจราชการกรุงเทพมหานคร', 1, $SESS_USERID, '$UPDATE_DATE', 4, 'บริหารต้น หรือ บริหารสูง', 2) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('21001', 'ผู้อำนวยการ', 'ผอ.', 1, $SESS_USERID, '$UPDATE_DATE', 3, 'อำนวยการต้น หรือ อำนวยการสูง', 3) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('21012', 'ผู้อำนวยการเฉพาะด้าน(แพทย์)', 'ผอ.เฉพาะด้าน(แพทย์)', 1, $SESS_USERID, '$UPDATE_DATE', 3, 'อำนวยการต้น หรือ อำนวยการสูง', 4) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('21022', 'ผู้อำนวยการเฉพาะด้าน(ทันตแพทย์)', 'ผอ.เฉพาะด้าน(ทันตแพทย์)', 1, $SESS_USERID, '$UPDATE_DATE', 3, 'อำนวยการต้น หรือ อำนวยการสูง', 5) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('21032', 'ผู้อำนวยการเฉพาะด้าน(นายสัตวแพทย์)', 'ผอ.เฉพาะด้าน(นายสัตวแพทย์)', 1, $SESS_USERID, '$UPDATE_DATE', 3, 'อำนวยการต้น หรือ อำนวยการสูง', 6) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('21042', 'ผู้อำนวยการเฉพาะด้าน(วิชาการพยาบาล)', 'ผอ.เฉพาะด้าน(วิชาการพยาบาล)', 1, $SESS_USERID, '$UPDATE_DATE', 3, 'อำนวยการต้น หรือ อำนวยการสูง', 7) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('21052', 'ผู้อำนวยการเฉพาะด้าน(เภสัชกรรม)', 'ผอ.เฉพาะด้าน(เภสัชกรรม)', 1, $SESS_USERID, '$UPDATE_DATE', 3, 'อำนวยการต้น หรือ อำนวยการสูง', 8) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('21062', 'ผู้อำนวยการเฉพาะด้าน(วิชาการสุขาภิบาล)', 'ผอ.เฉพาะด้าน(วิชาการสุขาภิบาล)', 1, $SESS_USERID, '$UPDATE_DATE', 3, 'อำนวยการต้น หรือ อำนวยการสูง', 9) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('21072', 'ผู้อำนวยการเฉพาะด้าน(วิศวกรรม)', 'ผอ.เฉพาะด้าน(วิศวกรรม)', 1, $SESS_USERID, '$UPDATE_DATE', 3, 'อำนวยการต้น หรือ อำนวยการสูง', 10) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('21082', 'ผู้อำนวยการเฉพาะด้าน(วิศวกรรมโยธา)', 'ผอ.เฉพาะด้าน(วิศวกรรมโยธา)', 1, $SESS_USERID, '$UPDATE_DATE', 3, 'อำนวยการต้น หรือ อำนวยการสูง', 11) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('21092', 'ผู้อำนวยการเฉพาะด้าน(วิศวกรรมไฟฟ้า)', 'ผอ.เฉพาะด้าน(วิศวกรรมไฟฟ้า)', 1, $SESS_USERID, '$UPDATE_DATE', 3, 'อำนวยการต้น หรือ อำนวยการสูง', 12) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('21102', 'ผู้อำนวยการเฉพาะด้าน(วิศวกรรมเครื่องกล)', 'ผอ.เฉพาะด้าน(วิศวกรรมเครื่องกล)', 1, $SESS_USERID, '$UPDATE_DATE', 3, 'อำนวยการต้น หรือ อำนวยการสูง', 13) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('21112', 'ผู้อำนวยการเฉพาะด้าน(ปฏิบัติงานช่างโยธา)', 'ผอ.เฉพาะด้าน(ปฏิบัติงานช่างโยธา)', 1, $SESS_USERID, '$UPDATE_DATE', 3, 'อำนวยการต้น หรือ อำนวยการสูง', 14) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('21122', 'ผู้อำนวยการเฉพาะด้าน(วิชาการจัดหาที่ดิน)', 'ผอ.เฉพาะด้าน(วิชาการจัดหาที่ดิน)', 1, $SESS_USERID, '$UPDATE_DATE', 3, 'อำนวยการต้น หรือ อำนวยการสูง', 15) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('21132', 'ผู้อำนวยการเฉพาะด้าน(นิติการ)', 'ผอ.เฉพาะด้าน(นิติการ)', 1, $SESS_USERID, '$UPDATE_DATE', 3, 'อำนวยการต้น หรือ อำนวยการสูง', 16) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('21142', 'ผู้อำนวยการเฉพาะด้าน(ผังเมือง)', 'ผอ.เฉพาะด้าน(ผังเมือง)', 1, $SESS_USERID, '$UPDATE_DATE', 3, 'อำนวยการต้น หรือ อำนวยการสูง', 17) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('21152', 'ผู้อำนวยการเฉพาะด้าน(วิชาการแผนที่)', 'ผอ.เฉพาะด้าน(วิชาการแผนที่)', 1, $SESS_USERID, '$UPDATE_DATE', 3, 'อำนวยการต้น หรือ อำนวยการสูง', 18) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('21162', 'ผู้อำนวยการเฉพาะด้าน(วิชาการคอมพิวเตอร์)', 'ผอ.เฉพาะด้าน(วิชาการคอมพิวเตอร์)', 1, $SESS_USERID, '$UPDATE_DATE', 3, 'อำนวยการต้น หรือ อำนวยการสูง', 19) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('21172', 'ผู้อำนวยการเฉพาะด้าน(วิชาการบัญชี)', 'ผอ.เฉพาะด้าน(วิชาการบัญชี)', 1, $SESS_USERID, '$UPDATE_DATE', 3, 'อำนวยการต้น หรือ อำนวยการสูง', 20) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('21182', 'ผู้อำนวยการเฉพาะด้าน(วิชาการเงินและบัญชี)', 'ผอ.เฉพาะด้าน(วิชาการเงินและบัญชี)', 1, $SESS_USERID, '$UPDATE_DATE', 3, 'อำนวยการต้น หรือ อำนวยการสูง', 21) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('21192', 'ผู้อำนวยการเฉพาะด้าน(วิชาการคลัง)', 'ผอ.เฉพาะด้าน(วิชาการคลัง)', 1, $SESS_USERID, '$UPDATE_DATE', 3, 'อำนวยการต้น หรือ อำนวยการสูง', 22) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('21202', 'ผู้อำนวยการเฉพาะด้าน(วิชาการตรวจสอบภายใน)', 'ผอ.เฉพาะด้าน(วิชาการตรวจสอบภายใน)', 1, $SESS_USERID, '$UPDATE_DATE', 3, 'อำนวยการต้น หรือ อำนวยการสูง', 23) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('21212', 'ผู้อำนวยการเฉพาะด้าน(พัฒนาการกีฬา)', 'ผอ.เฉพาะด้าน(พัฒนาการกีฬา)', 1, $SESS_USERID, '$UPDATE_DATE', 3, 'อำนวยการต้น หรือ อำนวยการสูง', 24) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('21222', 'ผู้อำนวยการเฉพาะด้าน(เทคนิคการแพทย์)', 'ผอ.เฉพาะด้าน(เทคนิคการแพทย์)', 1, $SESS_USERID, '$UPDATE_DATE', 3, 'อำนวยการต้น หรือ อำนวยการสูง', 25) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('21003', 'ผู้ตรวจราชการ', 'ผู้ตรวจราชการ', 1, $SESS_USERID, '$UPDATE_DATE', 3, 'อำนวยการต้น หรือ อำนวยการสูง', 26) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('31001', 'นักจัดการงานทั่วไป', 'นักจัดการงานทั่วไป', 1, $SESS_USERID, '$UPDATE_DATE', 2, 'ปฏิบัติการ หรือ ชำนาญการ หรือ ชำนาญการพิเศษ', 27) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('31002', 'นักทรัพยากรบุคคล', 'นักทรัพยากรบุคคล', 1, $SESS_USERID, '$UPDATE_DATE', 2, 'ปฏิบัติการ หรือ ชำนาญการ หรือ ชำนาญการพิเศษ', 28) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('31003', 'นิติกร', 'นิติกร', 1, $SESS_USERID, '$UPDATE_DATE', 2, 'ปฏิบัติการ หรือ ชำนาญการ หรือ ชำนาญการพิเศษ หรือ เชี่ยวชาญ หรือ ทรงคุณวุฒิ', 29) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('31004', 'นักปกครองเขต', 'นักปกครองเขต', 1, $SESS_USERID, '$UPDATE_DATE', 2, 'ชำนาญการพิเศษ', 30) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('31005', 'เจ้าพนักงานเทศกิจ', 'จพง.เทศกิจ', 1, $SESS_USERID, '$UPDATE_DATE', 2, 'ปฏิบัติการ หรือ ชำนาญการ หรือ ชำนาญการพิเศษ', 31) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('31006', 'เจ้าพนักงานปกครอง', 'จพง.ปกครอง', 1, $SESS_USERID, '$UPDATE_DATE', 2, 'ปฏิบัติการ หรือ ชำนาญการ หรือ ชำนาญการพิเศษ', 32) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('31007', 'เจ้าหน้าที่ระบบงานคอมพิวเตอร์', 'จ.ระบบงานคอมพิวเตอร์', 1, $SESS_USERID, '$UPDATE_DATE', 2, 'ปฏิบัติการ หรือ ชำนาญการ หรือ ชำนาญการพิเศษ', 33) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('31008', 'นักวิเคราะห์นโยบายและแผน', 'นักวิเคราะห์นโยบายและแผน', 1, $SESS_USERID, '$UPDATE_DATE', 2, 'ปฏิบัติการ หรือ ชำนาญการ หรือ ชำนาญการพิเศษ หรือ เชี่ยวชาญ', 34) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('31009', 'นักวิจัยการจราจร', 'นักวิจัยการจราจร', 1, $SESS_USERID, '$UPDATE_DATE', 2, 'ปฏิบัติการ หรือ ชำนาญการ หรือ ชำนาญการพิเศษ', 35) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('31010', 'นักวิชาการคอมพิวเตอร์', 'นวก.คอมพิวเตอร์', 1, $SESS_USERID, '$UPDATE_DATE', 2, 'ปฏิบัติการ หรือ ชำนาญการ หรือ ชำนาญการพิเศษ', 36) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('31011', 'นักวิชาการพัสดุ', 'นวก.พัสดุ', 1, $SESS_USERID, '$UPDATE_DATE', 2, 'ปฏิบัติการ หรือ ชำนาญการ หรือ ชำนาญการพิเศษ', 37) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('31012', 'นักวิชาการสถิติ', 'นวก.สถิติ', 1, $SESS_USERID, '$UPDATE_DATE', 2, 'ปฏิบัติการ หรือ ชำนาญการ หรือ ชำนาญการพิเศษ', 38) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('31013', 'นักวิเทศสัมพันธ์', 'นักวิเทศสัมพันธ์', 1, $SESS_USERID, '$UPDATE_DATE', 2, 'ปฏิบัติการ หรือ ชำนาญการ หรือ ชำนาญการพิเศษ', 39) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('41001', 'เจ้าหน้าที่เทศกิจ', 'จ.เทศกิจ', 1, $SESS_USERID, '$UPDATE_DATE', 1, 'ปฏิบัติงาน หรือ ชำนาญงาน', 40) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('41002', 'เจ้าพนักงานธุรการ', 'จพง.ธุรการ', 1, $SESS_USERID, '$UPDATE_DATE', 1, 'ปฏิบัติงาน หรือ ชำนาญงาน หรือ อาวุโส', 41) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('41003', 'เจ้าพนักงานพัสดุ', 'จพง.พัสดุ', 1, $SESS_USERID, '$UPDATE_DATE', 1, 'ปฏิบัติงาน หรือ ชำนาญงาน',42) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('41004', 'เจ้าพนักงานเวชสถิติ', 'จพง.เวชสถิติ', 1, $SESS_USERID, '$UPDATE_DATE', 1, 'ปฏิบัติงาน หรือ ชำนาญงาน', 43) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('41005', 'เจ้าพนักงานสถิติ', 'จพง.สถิติ', 1, $SESS_USERID, '$UPDATE_DATE', 1, 'ปฏิบัติงาน หรือ ชำนาญงาน', 44) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('41006', 'เจ้าหน้าที่ปกครอง', 'จ.ปกครอง', 1, $SESS_USERID, '$UPDATE_DATE', 1, 'ปฏิบัติงาน หรือ ชำนาญงาน', 45) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('32001', 'นักวิเคราะห์งบประมาณ', 'นักวิเคราะห์งบประมาณ', 1, $SESS_USERID, '$UPDATE_DATE', 2, 'ปฏิบัติการ หรือ ชำนาญการ หรือ ชำนาญการพิเศษ', 46) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('32002', 'นักวิชาการคลัง', 'นวก.คลัง', 1, $SESS_USERID, '$UPDATE_DATE', 2, 'ปฏิบัติการ หรือ ชำนาญการ หรือ ชำนาญการพิเศษ หรือ เชี่ยวชาญ', 47) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('32003', 'นักวิชาการเงินและบัญชี', 'นวก.เงินและบัญชี', 1, $SESS_USERID, '$UPDATE_DATE', 2, 'ปฏิบัติการ หรือ ชำนาญการ หรือ ชำนาญการพิเศษ', 48) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('32004', 'นักวิชาการจัดเก็บรายได้', 'นวก.จัดเก็บรายได้', 1, $SESS_USERID, '$UPDATE_DATE', 2, 'ปฏิบัติการ หรือ ชำนาญการ หรือ ชำนาญการพิเศษ', 49) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('32005', 'นักวิชาการตรวจสอบภายใน', 'นวก.ตรวจสอบภายใน', 1, $SESS_USERID, '$UPDATE_DATE', 2, 'ปฏิบัติการ หรือ ชำนาญการ หรือ ชำนาญการพิเศษ', 50) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('32006', 'นักบัญชี', 'นักบัญชี', 1, $SESS_USERID, '$UPDATE_DATE', 2, 'ปฏิบัติการ หรือ ชำนาญการ หรือ ชำนาญการพิเศษ', 51) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('42001', 'เจ้าพนักงานการเงินและบัญชี', 'จพง.การเงินและบัญชี', 1, $SESS_USERID, '$UPDATE_DATE', 1, 'ปฏิบัติงาน หรือ ชำนาญงาน หรือ อาวุโส', 52) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('42002', 'เจ้าพนักงานการคลัง', 'จพง.การคลัง', 1, $SESS_USERID, '$UPDATE_DATE', 1, 'ปฏิบัติงาน หรือ ชำนาญงาน', 53) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('42003', 'เจ้าพนักงานจัดเก็บรายได้', 'จพง.จัดเก็บรายได้', 1, $SESS_USERID, '$UPDATE_DATE', 1, 'ปฏิบัติงาน หรือ ชำนาญงาน หรือ อาวุโส', 54) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('33001', 'นักประชาสัมพันธ์', 'นักประชาสัมพันธ์', 1, $SESS_USERID, '$UPDATE_DATE', 2, 'ปฏิบัติการ หรือ ชำนาญการ หรือ ชำนาญการพิเศษ', 55) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('33002', 'นักวิชาการโสตทัศนศึกษา', 'นวก.โสตทัศนศึกษา', 1, $SESS_USERID, '$UPDATE_DATE', 2, 'ปฏิบัติการ หรือ ชำนาญการ หรือ ชำนาญการพิเศษ', 56) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('43001', 'เจ้าพนักงานประชาสัมพันธ์', 'จพง.ประชาสัมพันธ์', 1, $SESS_USERID, '$UPDATE_DATE', 1, 'ปฏิบัติงาน หรือ ชำนาญงาน', 57) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('43002', 'เจ้าพนักงานสื่อสาร', 'จพง.สื่อสาร', 1, $SESS_USERID, '$UPDATE_DATE', 1, 'ปฏิบัติงาน หรือ ชำนาญงาน หรือ อาวุโส', 58) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('43003', 'เจ้าพนักงานโสตทัศนศึกษา', 'จพง.โสตทัศนศึกษา', 1, $SESS_USERID, '$UPDATE_DATE', 1, 'ปฏิบัติงาน หรือ ชำนาญงาน', 59) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('34001', 'นักวิชาการเกษตร', 'นวก.เกษตร', 1, $SESS_USERID, '$UPDATE_DATE', 2, 'ปฏิบัติการ หรือ ชำนาญการ หรือ ชำนาญการพิเศษ', 60) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('44001', 'เจ้าพนักงานการเกษตร', 'จพง.การเกษตร', 1, $SESS_USERID, '$UPDATE_DATE', 1, 'ปฏิบัติงาน หรือ ชำนาญงาน หรือ อาวุโส', 61) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('35001', 'นักวิทยาศาสตร์', 'นักวิทยาศาสตร์', 1, $SESS_USERID, '$UPDATE_DATE', 2, 'ปฏิบัติการ หรือ ชำนาญการ หรือ ชำนาญการพิเศษ', 62) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('45001', 'เจ้าพนักงานวิทยาศาสตร์', 'จพง.วิทยาศาสตร์', 1, $SESS_USERID, '$UPDATE_DATE', 1, 'ปฏิบัติงาน หรือ ชำนาญงาน', 63) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('36001', 'นักกายภาพบำบัด', 'นักกายภาพบำบัด', 1, $SESS_USERID, '$UPDATE_DATE', 2, 'ปฏิบัติการ หรือ ชำนาญการ หรือ ชำนาญการพิเศษ', 64) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('36002', 'นักจิตวิทยา', 'นักจิตวิทยา', 1, $SESS_USERID, '$UPDATE_DATE', 2, 'ปฏิบัติการ หรือ ชำนาญการ หรือ ชำนาญการพิเศษ', 65) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('36003', 'ทันตแพทย์', 'ทันตแพทย์', 1, $SESS_USERID, '$UPDATE_DATE', 2, 'ปฏิบัติการ หรือ ชำนาญการ หรือ ชำนาญการพิเศษ หรือ เชี่ยวชาญ หรือ ทรงคุณวุฒิ', 66) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('36004', 'นักเทคนิคการแพทย์', 'นักเทคนิคการแพทย์', 1, $SESS_USERID, '$UPDATE_DATE', 2, 'ปฏิบัติการ หรือ ชำนาญการ หรือ ชำนาญการพิเศษ หรือ เชี่ยวชาญ', 67) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('36005', 'นายสัตวแพทย์', 'นายสัตวแพทย์', 1, $SESS_USERID, '$UPDATE_DATE', 2, 'ปฏิบัติการ หรือ ชำนาญการ หรือ ชำนาญการพิเศษ หรือ เชี่ยวชาญ', 68) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('36006', 'พยาบาลวิชาชีพ', 'พยาบาลวิชาชีพ', 1, $SESS_USERID, '$UPDATE_DATE', 2, 'ปฏิบัติการ หรือ ชำนาญการ หรือ ชำนาญการพิเศษ หรือ เชี่ยวชาญ', 69) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('36007', 'นายแพทย์', 'นายแพทย์', 1, $SESS_USERID, '$UPDATE_DATE', 2, 'ปฏิบัติการ หรือ ชำนาญการ หรือ ชำนาญการพิเศษ หรือ เชี่ยวชาญ หรือ ทรงคุณวุฒิ', 70) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('36008', 'เภสัชกร', 'เภสัชกร', 1, $SESS_USERID, '$UPDATE_DATE', 2, 'ปฏิบัติการ หรือ ชำนาญการ หรือ ชำนาญการพิเศษ หรือ เชี่ยวชาญ', 71) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('36009', 'นักโภชนาการ', 'นักโภชนาการ', 1, $SESS_USERID, '$UPDATE_DATE', 2, 'ปฏิบัติการ หรือ ชำนาญการ หรือ ชำนาญการพิเศษ', 72) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('36010', 'นักรังสีการแพทย์', 'นักรังสีการแพทย์', 1, $SESS_USERID, '$UPDATE_DATE', 2, 'ปฏิบัติการ หรือ ชำนาญการ หรือ ชำนาญการพิเศษ', 73) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('36011', 'นักวิชาการพยาบาล', 'นวก.พยาบาล', 1, $SESS_USERID, '$UPDATE_DATE', 2, 'ปฏิบัติการ หรือ ชำนาญการ หรือ ชำนาญการพิเศษ', 74) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('36012', 'นักวิชาการสาธารณสุข', 'นวก.สาธารณสุข', 1, $SESS_USERID, '$UPDATE_DATE', 2, 'ปฏิบัติการ หรือ ชำนาญการ หรือ ชำนาญการพิเศษ หรือ เชี่ยวชาญ', 75) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('36013', 'นักวิชาการสุขาภิบาล', 'นวก.สุขาภิบาล', 1, $SESS_USERID, '$UPDATE_DATE', 2, 'ปฏิบัติการ หรือ ชำนาญการ หรือ ชำนาญการพิเศษ', 76) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('36014', 'นักอาชีวบำบัด', 'นักอาชีวบำบัด', 1, $SESS_USERID, '$UPDATE_DATE', 2, 'ปฏิบัติการ หรือ ชำนาญการ หรือ ชำนาญการพิเศษ', 77) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('36015', 'นักวิทยาศาสตร์การแพทย์', 'นักวิทยาศาสตร์การแพทย์', 1, $SESS_USERID, '$UPDATE_DATE', 2, 'ปฏิบัติการ หรือ ชำนาญการ หรือ ชำนาญการพิเศษ', 78) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('46001', 'เจ้าหน้าที่งานรักษาความสะอาด', 'จ.งานรักษาความสะอาด', 1, $SESS_USERID, '$UPDATE_DATE', 1, 'ปฏิบัติงาน หรือ ชำนาญงาน หรือ อาวุโส', 79) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('46002', 'เจ้าหน้าที่อนามัย', 'จ.อนามัย', 1, $SESS_USERID, '$UPDATE_DATE', 1, 'ปฏิบัติงาน หรือ ชำนาญงาน', 80) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('46003', 'ผู้ช่วยทันตแพทย์', 'ผช.ทันตแพทย์', 1, $SESS_USERID, '$UPDATE_DATE', 1, 'ปฏิบัติงาน หรือ ชำนาญงาน', 81) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('46004', 'เจ้าพนักงานทันตสาธารณสุข', 'จพง.ทันตสาธารณสุข', 1, $SESS_USERID, '$UPDATE_DATE', 1, 'ปฏิบัติงาน หรือ ชำนาญงาน', 82) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('46005', 'เจ้าพนักงานเภสัชกรรม', 'จพง.เภสัชกรรม', 1, $SESS_USERID, '$UPDATE_DATE', 1, 'ปฏิบัติงาน หรือ ชำนาญงาน', 83) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('46006', 'โภชนากร', 'โภชนากร', 1, $SESS_USERID, '$UPDATE_DATE', 1, 'ปฏิบัติงาน หรือ ชำนาญงาน', 84) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('46007', 'เจ้าพนักงานรังสีการแพทย์', 'จพง.รังสีการแพทย์', 1, $SESS_USERID, '$UPDATE_DATE', 1, 'ปฏิบัติงาน หรือ ชำนาญงาน', 85) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('46008', 'เจ้าพนักงานวิทยาศาสตร์การแพทย์', 'จพง.วิทยาศาสตร์การแพทย์', 1, $SESS_USERID, '$UPDATE_DATE', 1, 'ปฏิบัติงาน หรือ ชำนาญงาน', 86) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('46009', 'เจ้าพนักงานสาธารณสุข', 'จพง.สาธารณสุข', 1, $SESS_USERID, '$UPDATE_DATE', 1, 'ปฏิบัติงาน หรือ ชำนาญงาน หรือ อาวุโส', 87) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('46010', 'พยาบาลเทคนิค', 'พยาบาลเทคนิค', 1, $SESS_USERID, '$UPDATE_DATE', 1, 'ปฏิบัติงาน หรือ ชำนาญงาน', 88) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('46011', 'สัตวแพทย์', 'สัตวแพทย์', 1, $SESS_USERID, '$UPDATE_DATE', 1, 'ปฏิบัติงาน หรือ ชำนาญงาน', 89) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('37001', 'นักจัดการงานโยธา', 'นักจัดการงานโยธา', 1, $SESS_USERID, '$UPDATE_DATE', 2, 'ปฏิบัติการ หรือ ชำนาญการ หรือ ชำนาญการพิเศษ', 90) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('37002', 'เจ้าพนักงานป้องกันและบรรเทาสาธารณภัย', 'จพง.ป้องกันและบรรเทาสาธารณภัย', 1, $SESS_USERID, '$UPDATE_DATE', 2, 'ปฏิบัติการ หรือ ชำนาญการ หรือ ชำนาญการพิเศษ', 91) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('37003', 'มัณฑนากร', 'มัณฑนากร', 1, $SESS_USERID, '$UPDATE_DATE', 2, 'ปฏิบัติการ หรือ ชำนาญการ หรือ ชำนาญการพิเศษ', 92) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('37004', 'นักวิชาการช่างศิลป์', 'นวก.ช่างศิลป์', 1, $SESS_USERID, '$UPDATE_DATE', 2, 'ปฏิบัติการ หรือ ชำนาญการ หรือ ชำนาญการพิเศษ', 93) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('37005', 'นักวิชาการแผนที่', 'นวก.แผนที่', 1, $SESS_USERID, '$UPDATE_DATE', 2, 'ปฏิบัติการ หรือ ชำนาญการ หรือ ชำนาญการพิเศษ', 94) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('37006', 'วิศวกร', 'วิศวกร', 1, $SESS_USERID, '$UPDATE_DATE', 2, 'ปฏิบัติการ หรือ ชำนาญการ หรือ ชำนาญการพิเศษ หรือ เชี่ยวชาญ', 95) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('37007', 'วิศวกรเครื่องกล', 'วิศวกรเครื่องกล', 1, $SESS_USERID, '$UPDATE_DATE', 2, 'ปฏิบัติการ หรือ ชำนาญการ หรือ ชำนาญการพิเศษ', 96) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('37008', 'วิศวกรไฟฟ้า', 'วิศวกรไฟฟ้า', 1, $SESS_USERID, '$UPDATE_DATE', 2, 'ปฏิบัติการ หรือ ชำนาญการ หรือ ชำนาญการพิเศษ', 97) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('37009', 'วิศวกรโยธา', 'วิศวกรโยธา', 1, $SESS_USERID, '$UPDATE_DATE', 2, 'ปฏิบัติการ หรือ ชำนาญการ หรือ ชำนาญการพิเศษ หรือ เชี่ยวชาญ', 98) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('37010', 'วิศวกรสุขาภิบาล', 'วิศวกรสุขาภิบาล', 1, $SESS_USERID, '$UPDATE_DATE', 2, 'ปฏิบัติการ หรือ ชำนาญการ หรือ ชำนาญการพิเศษ', 99) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('37011', 'นักเวชนิทัศน์', 'นักเวชนิทัศน์', 1, $SESS_USERID, '$UPDATE_DATE', 2, 'ปฏิบัติการ หรือ ชำนาญการ หรือ ชำนาญการพิเศษ', 100) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('37012', 'สถาปนิก', 'สถาปนิก', 1, $SESS_USERID, '$UPDATE_DATE', 2, 'ปฏิบัติการ หรือ ชำนาญการ หรือ ชำนาญการพิเศษ', 101) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('47001', 'ช่างกายอุปกรณ์', 'ช่างกายอุปกรณ์', 1, $SESS_USERID, '$UPDATE_DATE', 1, 'ปฏิบัติงาน หรือ ชำนาญงาน', 102) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('47002', 'เจ้าพนักงานเครื่องคอมพิวเตอร์', 'จพง.เครื่องคอมพิวเตอร์', 1, $SESS_USERID, '$UPDATE_DATE', 1, 'ปฏิบัติงาน หรือ ชำนาญงาน', 103) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('47003', 'นายช่างเขียนแบบ', 'นายช่างเขียนแบบ', 1, $SESS_USERID, '$UPDATE_DATE', 1, 'ปฏิบัติงาน หรือ ชำนาญงาน', 104) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('47004', 'นายช่างเครื่องกล', 'นายช่างเครื่องกล', 1, $SESS_USERID, '$UPDATE_DATE', 1, 'ปฏิบัติงาน หรือ ชำนาญงาน หรือ อาวุโส', 105) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('47005', 'นายช่างเทคนิค', 'นายช่างเทคนิค', 1, $SESS_USERID, '$UPDATE_DATE', 1, 'ปฏิบัติงาน หรือ ชำนาญงาน', 106) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('47006', 'นายช่างไฟฟ้า', 'นายช่างไฟฟ้า', 1, $SESS_USERID, '$UPDATE_DATE', 1, 'ปฏิบัติงาน หรือ ชำนาญงาน หรือ อาวุโส', 107) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('47007', 'นายช่างภาพ', 'นายช่างภาพ', 1, $SESS_USERID, '$UPDATE_DATE', 1, 'ปฏิบัติงาน หรือ ชำนาญงาน', 108) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('47008', 'นายช่างโยธา', 'นายช่างโยธา', 1, $SESS_USERID, '$UPDATE_DATE', 1, 'ปฏิบัติงาน หรือ ชำนาญงาน หรือ อาวุโส', 109) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('47009', 'นายช่างศิลป์', 'นายช่างศิลป์', 1, $SESS_USERID, '$UPDATE_DATE', 1, 'ปฏิบัติงาน หรือ ชำนาญงาน หรือ อาวุโส', 110) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('47010', 'นายช่างสำรวจ', 'นายช่างสำรวจ', 1, $SESS_USERID, '$UPDATE_DATE', 1, 'ปฏิบัติงาน หรือ ชำนาญงาน หรือ อาวุโส', 111) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('47011', 'พนักงานป้องกันและบรรเทาสาธารณภัย', 'พนักงานป้องกันและบรรเทาสาธารณภัย', 1, $SESS_USERID, '$UPDATE_DATE', 1, 'ปฏิบัติงาน หรือ ชำนาญงาน หรือ อาวุโส', 112) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('38001', 'บรรณารักษ์', 'บรรณารักษ์', 1, $SESS_USERID, '$UPDATE_DATE', 2, 'ปฏิบัติการ หรือ ชำนาญการ หรือ ชำนาญการพิเศษ', 113) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('38002', 'นักผังเมือง', 'นักผังเมือง', 1, $SESS_USERID, '$UPDATE_DATE', 2, 'ปฏิบัติการ หรือ ชำนาญการ หรือ ชำนาญการพิเศษ', 114) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('38003', 'นักพัฒนาการกีฬา', 'นักพัฒนาการกีฬา', 1, $SESS_USERID, '$UPDATE_DATE', 2, 'ปฏิบัติการ หรือ ชำนาญการ หรือ ชำนาญการพิเศษ', 115) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('38004', 'นักพัฒนาการท่องเที่ยว', 'นักพัฒนาการท่องเที่ยว', 1, $SESS_USERID, '$UPDATE_DATE', 2, 'ปฏิบัติการ หรือ ชำนาญการ หรือ ชำนาญการพิเศษ', 116) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('38005', 'นักพัฒนาสังคม', 'นักพัฒนาสังคม', 1, $SESS_USERID, '$UPDATE_DATE', 2, 'ปฏิบัติการ หรือ ชำนาญการ หรือ ชำนาญการพิเศษ', 117) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('38006', 'นักวิเคราะห์ผังเมือง', 'นักวิเคราะห์ผังเมือง', 1, $SESS_USERID, '$UPDATE_DATE', 2, 'ปฏิบัติการ หรือ ชำนาญการ หรือ ชำนาญการพิเศษ', 118) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('38007', 'นักวิชาการจัดหาที่ดิน', 'นวก.จัดหาที่ดิน', 1, $SESS_USERID, '$UPDATE_DATE', 2, 'ปฏิบัติการ หรือ ชำนาญการ หรือ ชำนาญการพิเศษ', 119) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('38008', 'นักวิชาการละครและดนตรี', 'นวก.ละครและดนตรี', 1, $SESS_USERID, '$UPDATE_DATE', 2, 'ปฏิบัติการ หรือ ชำนาญการ หรือ ชำนาญการพิเศษ', 120) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('38009', 'นักวิชาการวัฒนธรรม', 'นวก.วัฒนธรรม', 1, $SESS_USERID, '$UPDATE_DATE', 2, 'ปฏิบัติการ หรือ ชำนาญการ หรือ ชำนาญการพิเศษ', 121) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('38010', 'นักวิชาการศึกษา', 'นวก.ศึกษา', 1, $SESS_USERID, '$UPDATE_DATE', 2, 'ปฏิบัติการ หรือ ชำนาญการ หรือ ชำนาญการพิเศษ', 122) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('38011', 'นักวิชาการศึกษาพิเศษ', 'นวก.ศึกษาพิเศษ', 1, $SESS_USERID, '$UPDATE_DATE', 2, 'ปฏิบัติการ หรือ ชำนาญการ หรือ ชำนาญการพิเศษ', 123) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('38012', 'นักวิชาการศูนย์เยาวชน', 'นวก.ศูนย์เยาวชน', 1, $SESS_USERID, '$UPDATE_DATE', 2, 'ปฏิบัติการ หรือ ชำนาญการ หรือ ชำนาญการพิเศษ', 124) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('38013', 'นักวิชาการสิ่งแวดล้อม', 'นวก.สิ่งแวดล้อม', 1, $SESS_USERID, '$UPDATE_DATE', 2, 'ปฏิบัติการ หรือ ชำนาญการ หรือ ชำนาญการพิเศษ', 125) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('38014', 'นักสังคมสงเคราะห์', 'นักสังคมสงเคราะห์.', 1, $SESS_USERID, '$UPDATE_DATE', 2, 'ปฏิบัติการ หรือ ชำนาญการ หรือ ชำนาญการพิเศษ', 126) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('48001', 'คีตศิลปิน', 'คีตศิลปิน', 1, $SESS_USERID, '$UPDATE_DATE', 1, 'ปฏิบัติงาน หรือ ชำนาญงาน หรือ อาวุโส', 127) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('48002', 'ดุริยางคศิลปิน', 'ดุริยางคศิลปิน', 1, $SESS_USERID, '$UPDATE_DATE', 1, 'ปฏิบัติงาน หรือ ชำนาญงาน หรือ อาวุโส', 128) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('48003', 'เจ้าพนักงานพัฒนาสังคม', 'จพง.พัฒนาสังคม', 1, $SESS_USERID, '$UPDATE_DATE', 1, 'ปฏิบัติงาน หรือ ชำนาญงาน', 129) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('48004', 'เจ้าพนักงานศูนย์เยาวชน', 'จพง.ศูนย์เยาวชน', 1, $SESS_USERID, '$UPDATE_DATE', 1, 'ปฏิบัติงาน หรือ ชำนาญงาน', 130) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, CL_NAME, PL_SEQ_NO)
								  VALUES ('48005', 'เจ้าพนักงานห้องสมุด', 'จพง.ห้องสมุด', 1, $SESS_USERID, '$UPDATE_DATE', 1, 'ปฏิบัติงาน หรือ ชำนาญงาน', 131) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 
*/
			} else {
				$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'data_map_type_new_check.html' ";
				$count_data = $db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'data_all_comdtl_tab.html?from_parent=map_type_new_comdtl' ";
				$count_data = $db->send_cmd($cmd);
				//$db->show_error();
			}
/*
			if ($SESS_DEPARTMENT_NAME!="กรมการปกครอง") {
				$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'data_move_salary.html' ";
				$count_data = $db->send_cmd($cmd);
				//$db->show_error();
			}
*/
			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'master_table.html?table=PER_PERFORMANCE' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'master_table.html?table=PER_GOODNESS' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			 if($SCOMPETENCY[$PER_ID_DEPARTMENT_ID]!="Y"){ 
				$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'personal_kpi_flag.html' ";
				$db->send_cmd($cmd);
				//$db->show_error();
			 }

			if ($BKK_FLAG != 1) {
				$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 
								  WHERE LINKTO_WEB = 'master_table.html?table=PER_PERFORMANCE' ";
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
									  VALUES (1, 'TH', $MAX_ID, 6, 'M0806 ผลงาน', 'S', 'W', 'master_table.html?table=PER_PERFORMANCE', 0, 9, 300, 
									  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();

					$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
									  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
									  CREATE_BY, UPDATE_DATE, UPDATE_BY)
									  VALUES (1, 'EN', $MAX_ID, 6, 'M0806 ผลงาน', 'S', 'W', 'master_table.html?table=PER_PERFORMANCE', 0, 9, 300, 
									  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();
				}

				$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 
								  WHERE LINKTO_WEB = 'master_table.html?table=PER_GOODNESS' ";
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
									  VALUES (1, 'TH', $MAX_ID, 7, 'M0807 คุณงามความดี', 'S', 'W', 'master_table.html?table=PER_GOODNESS', 0, 9, 300, 
									  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();

					$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
									  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
									  CREATE_BY, UPDATE_DATE, UPDATE_BY)
									  VALUES (1, 'EN', $MAX_ID, 7, 'M0807 คุณงามความดี', 'S', 'W', 'master_table.html?table=PER_GOODNESS', 0, 9, 300, 
									  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();
				}
			}

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'kpi_competence.html?table=PER_COMPETENCE' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'kpi_competence_level.html?table=PER_COMPETENCE_LEVEL' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'master_table_assess_main.html?table=PER_ASSESS_MAIN' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE MENU_LABEL = 'M11 การประเมินผล' ";
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
								  VALUES (1, 'TH', $MAX_ID1, 10, 'M11 การประเมินผล', 'S', 'N', 0, 9, $CREATE_DATE, $UPDATE_BY, 
								  $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, UPDATE_DATE, 
								  UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID1, 10, 'M11 การประเมินผล', 'S', 'N', 0, 9, $CREATE_DATE, $UPDATE_BY, 
								  $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " SELECT MENU_ID FROM BACKOFFICE_MENU_BAR_LV1 WHERE MENU_LABEL = 'M11 การประเมินผล' ";
			$db->send_cmd($cmd);
			//$db->show_error();
			$data = $db->get_array();
			$MENU_ID = $data[MENU_ID];

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 
							  WHERE LINKTO_WEB = 'kpi_competence.html?table=PER_COMPETENCE' ";
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
								  VALUES (1, 'TH', $MAX_ID, 1, 'M1101 สมรรถนะ', 'S', 'W', 'kpi_competence.html?table=PER_COMPETENCE', 0, 9, $MENU_ID, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 1, 'M1101 สมรรถนะ', 'S', 'W', 'kpi_competence.html?table=PER_COMPETENCE', 0, 9, $MENU_ID, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 
							  WHERE LINKTO_WEB = 'kpi_competence_level.html?table=PER_COMPETENCE_LEVEL' ";
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
								  VALUES (1, 'TH', $MAX_ID, 2, 'M1102 ระดับของแต่ละสมรรถนะ', 'S', 'W', 'kpi_competence_level.html?table=PER_COMPETENCE_LEVEL', 0, 9, $MENU_ID, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 2, 'M1102 ระดับของแต่ละสมรรถนะ', 'S', 'W', 'kpi_competence_level.html?table=PER_COMPETENCE_LEVEL', 0, 9, $MENU_ID, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 
							  WHERE LINKTO_WEB = 'master_table_assess_main.html?table=PER_ASSESS_MAIN' ";
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
								  VALUES (1, 'TH', $MAX_ID, 3, 'M1103 ระดับผลการประเมินหลัก', 'S', 'W', 'master_table_assess_main.html?table=PER_ASSESS_MAIN', 0, 9, $MENU_ID, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 3, 'M1103 ระดับผลการประเมินหลัก', 'S', 'W', 'master_table_assess_main.html?table=PER_ASSESS_MAIN', 0, 9, $MENU_ID, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_ASSESS_MAIN ALTER AM_POINT_MIN NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_ASSESS_MAIN MODIFY AM_POINT_MIN NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_ASSESS_MAIN MODIFY AM_POINT_MIN NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_ASSESS_MAIN ALTER AM_POINT_MAX NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_ASSESS_MAIN MODIFY AM_POINT_MAX NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_ASSESS_MAIN MODIFY AM_POINT_MAX NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " SELECT AB_CODE FROM PER_ABSENTTYPE WHERE AB_CODE = '16' AND AB_NAME = 'ลาเข้ารับการตรวจเลือกหรือลาเข้ารับการเตรียมพล' ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if ($count_data) {
				$cmd = " DELETE FROM PER_ABSENTTYPE WHERE AB_CODE = '16' ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			}

			$cmd = " SELECT AB_CODE FROM PER_ABSENTTYPE WHERE AB_CODE = '06' AND AB_NAME = 'ลาเข้ารับราชการทหาร' ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if ($count_data) 
				$cmd = " UPDATE PER_ABSENTTYPE SET AB_NAME = 'ลาเข้ารับการตรวจเลือกหรือเข้ารับการเตรียมพล' WHERE AB_CODE = '06' ";
			else
				$cmd = " INSERT INTO PER_ABSENTTYPE (AB_CODE, AB_NAME, AB_QUOTA, AB_COUNT, AB_ACTIVE, UPDATE_USER, UPDATE_DATE)
								  VALUES ('06', 'ลาเข้ารับการตรวจเลือกหรือลาเข้ารับการเตรียมพล', 0, 1, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " UPDATE PER_ABSENTTYPE SET AB_NAME = 'ลาไปศึกษา ฝึกอบรม ดูงาน หรือปฏิบัติการวิจัย' WHERE AB_CODE = '07' AND AB_NAME = 'ลาไปศึกษา ฝึกอบรม ดูงาน หรือปฏิบัติการ' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " UPDATE PER_ABSENTTYPE SET AB_NAME = 'ลากิจส่วนตัวเพื่อเลี้ยงดูบุตร' WHERE AB_CODE = '11' AND AB_NAME = 'ลากิจเลี้ยงดูบุตร' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			add_field("PER_COMDTL", "CMD_POS_NO_NAME","VARCHAR", "15", "NULL");
			add_field("PER_COMDTL", "CMD_POS_NO","VARCHAR", "15", "NULL");

			$cmd = " UPDATE PER_COMDTL SET CMD_POSITION = NULL WHERE CMD_POSITION = '\|' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_COMDTL SET CMD_ORG1 = NULL WHERE CMD_ORG1 = '\|' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_COMDTL SET CMD_ORG2 = NULL WHERE CMD_ORG2 = '\|' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " SELECT COM_ID, CMD_SEQ, CMD_POSITION FROM PER_COMDTL WHERE CMD_POSITION IS NOT NULL AND CMD_POS_NO IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			while($data = $db_dpis->get_array()){
				$COM_ID = $data[COM_ID];
				$CMD_SEQ = $data[CMD_SEQ];
				$CMD_POSITION = trim($data[CMD_POSITION]);
				$CMD_POS_NO = $CMD_PL_NAME = $CMD_PM_NAME = "";
				$tmp_data = explode("\|", trim($data[CMD_POSITION]));
				$CMD_POS_NO = $tmp_data[0];
				$CMD_PL_NAME = $tmp_data[1];
				$CMD_PM_NAME = $tmp_data[2];
				if ($CMD_PM_NAME) $CMD_POSITION = $CMD_PL_NAME . "\|" . $CMD_PM_NAME; 
				else $CMD_POSITION = $CMD_PL_NAME;

				$cmd = " UPDATE PER_COMDTL SET CMD_POSITION = '$CMD_POSITION', CMD_POS_NO = '$CMD_POS_NO' WHERE COM_ID = $COM_ID AND CMD_SEQ = $CMD_SEQ ";
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
			} // end while

			$count_data = add_field("PER_LINE_COMPETENCE", "DEPARTMENT_ID","INTEGER", "10", "NULL");
			if (!$count_data) {
				if ($CTRL_TYPE == 4)	{
					$cmd = " UPDATE PER_LINE_COMPETENCE SET DEPARTMENT_ID=$DEPARTMENT_ID WHERE DEPARTMENT_ID IS NULL ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				} else {
					if($DPISDB=="odbc" || $DPISDB=="mysql"){ 
						$cmd = " SELECT PL_CODE, ORG_ID, CP_CODE FROM PER_LINE_COMPETENCE WHERE ORG_ID IS NOT NULL ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
						while($data = $db_dpis->get_array()){
							$PL_CODE = trim($data[PL_CODE]);
							$ORG_ID = $data[ORG_ID];
							$CP_CODE = trim($data[CP_CODE]);
							$cmd = " SELECT ORG_ID_REF FROM PER_ORG WHERE ORG_ID = $ORG_ID ";
							$db_dpis1->send_cmd($cmd);
							//$db_dpis1->show_error();
							$data1 = $db_dpis1->get_array();
							$ORG_ID_REF = $data1[ORG_ID_REF];
							$cmd = " UPDATE PER_LINE_COMPETENCE SET DEPARTMENT_ID = $ORG_ID_REF 
											  WHERE PL_CODE = '$PL_CODE' AND ORG_ID = $ORG_ID AND CP_CODE = '$CP_CODE' ";
							$db_dpis1->send_cmd($cmd);
							//$db_dpis1->show_error();
						} // end while						
					} elseif($DPISDB=="oci8"){
						$cmd = " UPDATE PER_LINE_COMPETENCE A SET A.DEPARTMENT_ID = (SELECT D.ORG_ID_REF 
										  FROM PER_ORG C, PER_ORG D WHERE A.ORG_ID = C.ORG_ID AND C.ORG_ID = D.ORG_ID) ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
					} // end if
				} // end if
			}

			
			
			
			
			$cmd = " SELECT TG_CODE FROM PER_TEMP_POS_GROUP ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) {
				if($DPISDB=="odbc") 
					$cmd = " CREATE TABLE PER_TEMP_POS_GROUP(
					TG_CODE VARCHAR(10) NOT NULL,	
					TG_NAME VARCHAR(100) NOT NULL,	
					TG_SEQ_NO INTEGER2 NULL,
					TG_ACTIVE SINGLE NOT NULL,
					UPDATE_USER INTEGER NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_TEMP_POS_GROUP PRIMARY KEY (TG_CODE)) ";
				elseif($DPISDB=="oci8") 
					$cmd = " CREATE TABLE PER_TEMP_POS_GROUP(
					TG_CODE VARCHAR2(10) NOT NULL,	
					TG_NAME VARCHAR2(100) NOT NULL,	
					TG_SEQ_NO NUMBER(5) NULL,
					TG_ACTIVE NUMBER(1) NOT NULL,
					UPDATE_USER NUMBER(11) NOT NULL,
					UPDATE_DATE VARCHAR2(19) NOT NULL,		
					CONSTRAINT PK_PER_TEMP_POS_GROUP PRIMARY KEY (TG_CODE)) ";
				elseif($DPISDB=="mysql")
					$cmd = " CREATE TABLE PER_TEMP_POS_GROUP(
					TG_CODE VARCHAR(10) NOT NULL,	
					TG_NAME VARCHAR(100) NOT NULL,	
					TG_SEQ_NO SMALLINT(5) NULL,
					TG_ACTIVE SMALLINT(1) NOT NULL,
					UPDATE_USER INTEGER(11) NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_TEMP_POS_GROUP PRIMARY KEY (TG_CODE)) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				if($DPISDB=="odbc") 
					$cmd = " CREATE TABLE PER_TEMP_POS_NAME(
					TP_CODE VARCHAR(10) NOT NULL,	
					TP_NAME VARCHAR(100) NOT NULL,	
					TG_CODE VARCHAR(10) NULL,
					TP_SEQ_NO INTEGER2 NULL,
					TP_ACTIVE SINGLE NOT NULL,
					UPDATE_USER INTEGER NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_TEMP_POS_NAME PRIMARY KEY (TP_CODE)) ";
				elseif($DPISDB=="oci8") 
					$cmd = " CREATE TABLE PER_TEMP_POS_NAME(
					TP_CODE VARCHAR2(10) NOT NULL,	
					TP_NAME VARCHAR2(100) NOT NULL,	
					TG_CODE VARCHAR2(10) NULL,
					TP_SEQ_NO NUMBER(5) NULL,
					TP_ACTIVE NUMBER(1) NOT NULL,
					UPDATE_USER NUMBER(11) NOT NULL,
					UPDATE_DATE VARCHAR2(19) NOT NULL,		
					CONSTRAINT PK_PER_TEMP_POS_NAME PRIMARY KEY (TP_CODE)) ";
				elseif($DPISDB=="mysql")
					$cmd = " CREATE TABLE PER_TEMP_POS_NAME(
					TP_CODE VARCHAR(10) NOT NULL,	
					TP_NAME VARCHAR(100) NOT NULL,	
					TG_CODE VARCHAR(10) NULL,
					TP_SEQ_NO SMALLINT(5) NULL,
					TP_ACTIVE SMALLINT(1) NOT NULL,
					UPDATE_USER INTEGER(11) NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_TEMP_POS_NAME PRIMARY KEY (TP_CODE)) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_TEMP_POS_NAME (TP_CODE, TP_NAME, TP_ACTIVE, UPDATE_USER, UPDATE_DATE)
								  SELECT trim(PL_CODE), PL_NAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE FROM PER_LINE WHERE PL_ACTIVE = 1 ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_TEMP_POS_NAME (TP_CODE, TP_NAME, TP_ACTIVE, UPDATE_USER, UPDATE_DATE)
								  SELECT trim(PN_CODE), PN_NAME, PN_ACTIVE, UPDATE_USER, UPDATE_DATE FROM PER_POS_NAME WHERE PN_ACTIVE = 1 ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " DELETE FROM PER_TEMP_POS_NAME WHERE LENGTH(TP_CODE) = 10 ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				if($DPISDB=="odbc") 
					$cmd = " CREATE TABLE PER_POS_TEMP(
					POT_ID INTEGER NOT NULL,	
					ORG_ID INTEGER NOT NULL,
					POT_NO VARCHAR(15) NOT NULL,	
					ORG_ID_1 INTEGER NULL,
					ORG_ID_2 INTEGER NULL,
					ORG_ID_3 INTEGER NULL,
					ORG_ID_4 INTEGER NULL,
					ORG_ID_5 INTEGER NULL,
					TP_CODE VARCHAR(10) NOT NULL,
					POT_MIN_SALARY NUMBER NOT NULL,	
					POT_MAX_SALARY NUMBER NOT NULL,		
					DEPARTMENT_ID INTEGER NULL, 
					POT_SEQ_NO INTEGER2 NULL,
					POT_REMARK MEMO NULL,
					POT_STATUS SINGLE NOT NULL,		
					UPDATE_USER INTEGER NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_POS_TEMP PRIMARY KEY (POT_ID)) ";
				elseif($DPISDB=="oci8") 
					$cmd = " CREATE TABLE PER_POS_TEMP(
					POT_ID NUMBER(10) NOT NULL,	
					ORG_ID NUMBER(10) NOT NULL,
					POT_NO VARCHAR2(15) NOT NULL,	
					ORG_ID_1 NUMBER(10) NULL,
					ORG_ID_2 NUMBER(10) NULL,
					ORG_ID_3 NUMBER(10) NULL,
					ORG_ID_4 NUMBER(10) NULL,
					ORG_ID_5 NUMBER(10) NULL,
					TP_CODE VARCHAR2(10) NOT NULL,
					POT_MIN_SALARY NUMBER(16,2) NOT NULL,	
					POT_MAX_SALARY NUMBER(16,2) NOT NULL,	
					DEPARTMENT_ID NUMBER(10) NULL,
					POT_SEQ_NO NUMBER(5) NULL,
					POT_REMARK VARCHAR2(2000) NULL,
					POT_STATUS NUMBER(1) NOT NULL,		
					UPDATE_USER NUMBER(11) NOT NULL,
					UPDATE_DATE VARCHAR2(19) NOT NULL,		
					CONSTRAINT PK_PER_POS_TEMP PRIMARY KEY (POT_ID)) ";
				elseif($DPISDB=="mysql")
					$cmd = " CREATE TABLE PER_POS_TEMP(
					POT_ID INTEGER(10) NOT NULL,	
					ORG_ID INTEGER(10) NOT NULL,
					POT_NO VARCHAR(15) NOT NULL,	
					ORG_ID_1 INTEGER(10) NULL,
					ORG_ID_2 INTEGER(10) NULL,
					ORG_ID_3 INTEGER(10) NULL,
					ORG_ID_4 INTEGER(10) NULL,
					ORG_ID_5 INTEGER(10) NULL,
					TP_CODE VARCHAR(10) NOT NULL,
					POT_MIN_SALARY DECIMAL(16,2) NOT NULL,	
					POT_MAX_SALARY DECIMAL(16,2) NOT NULL,	
					DEPARTMENT_ID INTEGER(10) NULL,
					POT_SEQ_NO SMALLINT(5) NULL,
					POT_REMARK TEXT NULL,
					POT_STATUS SMALLINT(1) NOT NULL,		
					UPDATE_USER INTEGER(11) NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_POS_TEMP PRIMARY KEY (POT_ID)) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			}

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 
							  WHERE LINKTO_WEB = 'master_table_temp_pos_group.html?table=PER_TEMP_POS_GROUP' ";
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
								  VALUES (1, 'TH', $MAX_ID, 16, 'M0316 หมวดตำแหน่งลูกจ้างชั่วคราว', 'S', 'W', 'master_table_temp_pos_group.html?table=PER_TEMP_POS_GROUP', 0, 9, 295, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 16, 'M0316 หมวดตำแหน่งลูกจ้างชั่วคราว', 'S', 'W', 'master_table_temp_pos_group.html?table=PER_TEMP_POS_GROUP', 0, 9, 295, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 
							  WHERE LINKTO_WEB = 'master_table_temp_pos_name.html?table=PER_TEMP_POS_NAME' ";
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
								  VALUES (1, 'TH', $MAX_ID, 17, 'M0317 ชื่อตำแหน่งลูกจ้างชั่วคราว', 'S', 'W', 'master_table_temp_pos_name.html?table=PER_TEMP_POS_NAME', 0, 9, 295, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 17, 'M0317 ชื่อตำแหน่งลูกจ้างชั่วคราว', 'S', 'W', 'master_table_temp_pos_name.html?table=PER_TEMP_POS_NAME', 0, 9, 295, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 
							  WHERE LINKTO_WEB = 'master_table_pos_temp.html' ";
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
								  VALUES (1, 'TH', $MAX_ID, 4, 'S0204 ตำแหน่งลูกจ้างชั่วคราว', 'S', 'W', 'master_table_pos_temp.html', 0, 34, 254, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 4, 'S0204 ตำแหน่งลูกจ้างชั่วคราว', 'S', 'W', 'master_table_pos_temp.html', 0, 34, 254, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_POS_EMP ALTER POEM_MIN_SALARY NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POS_EMP MODIFY POEM_MIN_SALARY NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_POS_EMP MODIFY POEM_MIN_SALARY NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_POS_EMP ALTER POEM_MAX_SALARY NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POS_EMP MODIFY POEM_MAX_SALARY NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_POS_EMP MODIFY POEM_MAX_SALARY NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_POS_EMPSER ALTER POEM_MIN_SALARY NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POS_EMPSER MODIFY POEM_MIN_SALARY NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_POS_EMPSER MODIFY POEM_MIN_SALARY NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_POS_EMPSER ALTER POEM_MAX_SALARY NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POS_EMPSER MODIFY POEM_MAX_SALARY NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_POS_EMPSER MODIFY POEM_MAX_SALARY NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_POS_TEMP ALTER POT_MIN_SALARY NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POS_TEMP MODIFY POT_MIN_SALARY NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_POS_TEMP MODIFY POT_MIN_SALARY NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_POS_TEMP ALTER POT_MAX_SALARY NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POS_TEMP MODIFY POT_MAX_SALARY NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_POS_TEMP MODIFY POT_MAX_SALARY NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_cgd_educate.html' ";
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
								  VALUES (1, 'TH', $MAX_ID, 16, 'P1116 การจัดเตรียมข้อมูลเกี่ยวกับการศึกษา (กรมบัญชีกลาง)', 'S', 'W', 'rpt_cgd_educate.html', 0, 35, 251, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 16, 'P1116 การจัดเตรียมข้อมูลเกี่ยวกับการศึกษา (กรมบัญชีกลาง)', 'S', 'W', 'rpt_cgd_educate.html', 0, 35, 251, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " SELECT MSG_ID FROM PER_MESSAGE ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) {
				if($DPISDB=="odbc") 
					$cmd = " CREATE TABLE PER_MESSAGE (
					MSG_ID INTEGER NOT NULL,
					MSG_HEADER VARCHAR(255) NULL,
					MSG_DETAIL MEMO NULL,
					MSG_SOURCE VARCHAR(255) NULL,
					MSG_POST_DATE VARCHAR(19) NULL,
					MSG_START_DATE VARCHAR(19) NULL,
					MSG_FINISH_DATE VARCHAR(19) NULL,
					USER_ID INTEGER2 NOT NULL,
					MSG_TYPE SINGLE NOT NULL,	
					MSG_DOCUMENT VARCHAR(255) NULL,
					MSG_ORG_NAME VARCHAR(255) NULL,
					MSG_SHOW SINGLE NOT NULL,	
					UPDATE_USER INTEGER NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_MESSAGE  PRIMARY KEY (MSG_ID)) ";
				elseif($DPISDB=="oci8") 
					$cmd = " CREATE TABLE PER_MESSAGE (
					MSG_ID NUMBER(10) NOT NULL,
					MSG_HEADER VARCHAR2(255) NULL,
					MSG_DETAIL VARCHAR2(4000) NULL,
					MSG_SOURCE VARCHAR2(255) NULL,
					MSG_POST_DATE VARCHAR2(19) NULL,
					MSG_START_DATE VARCHAR2(19) NULL,
					MSG_FINISH_DATE VARCHAR2(19) NULL,
					USER_ID NUMBER(5) NOT NULL,
					MSG_TYPE NUMBER(1) NOT NULL,	
					MSG_DOCUMENT VARCHAR2(255) NULL,
					MSG_ORG_NAME VARCHAR2(255) NULL,
					MSG_SHOW NUMBER(1) NOT NULL,	
					UPDATE_USER NUMBER(11) NOT NULL,
					UPDATE_DATE VARCHAR2(19) NOT NULL,		
					CONSTRAINT PK_PER_MESSAGE  PRIMARY KEY (MSG_ID)) ";
				elseif($DPISDB=="mysql") 
					$cmd = " CREATE TABLE PER_MESSAGE (
					MSG_ID INTEGER(10) NOT NULL,
					MSG_HEADER VARCHAR(255) NULL,
					MSG_DETAIL TEXT NULL,
					MSG_SOURCE VARCHAR(255) NULL,
					MSG_POST_DATE VARCHAR(19) NULL,
					MSG_START_DATE VARCHAR(19) NULL,
					MSG_FINISH_DATE VARCHAR(19) NULL,
					USER_ID SMALLINT(5) NOT NULL,
					MSG_TYPE SMALLINT(1) NOT NULL,	
					MSG_DOCUMENT VARCHAR(255) NULL,
					MSG_ORG_NAME VARCHAR(255) NULL,
					MSG_SHOW SMALLINT(1) NOT NULL,	
					UPDATE_USER INTEGER(11) NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_MESSAGE  PRIMARY KEY (MSG_ID)) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			} // end if

			$cmd = " SELECT MSG_ID FROM PER_MESSAGE_USER ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) {
				if($DPISDB=="odbc") 
					$cmd = " CREATE TABLE PER_MESSAGE_USER (
					MSG_ID INTEGER NOT NULL,
					USER_ID INTEGER NOT NULL,
					MSG_STATUS SINGLE NOT NULL,	
					MSG_READ VARCHAR(19) NULL,
					UPDATE_USER INTEGER NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_MESSAGE_USER  PRIMARY KEY (MSG_ID, USER_ID)) ";
				elseif($DPISDB=="oci8") 
					$cmd = " CREATE TABLE PER_MESSAGE_USER (
					MSG_ID NUMBER(10) NOT NULL,
					USER_ID NUMBER(10) NOT NULL,
					MSG_STATUS NUMBER(1) NOT NULL,	
					MSG_READ VARCHAR2(19) NULL,
					UPDATE_USER NUMBER(11) NOT NULL,
					UPDATE_DATE VARCHAR2(19) NOT NULL,		
					CONSTRAINT PK_PER_MESSAGE_USER  PRIMARY KEY (MSG_ID, USER_ID)) ";
				elseif($DPISDB=="mysql") 
					$cmd = " CREATE TABLE PER_MESSAGE_USER (
					MSG_ID INTEGER(10) NOT NULL,
					USER_ID INTEGER(10) NOT NULL,
					MSG_STATUS SMALLINT(1) NOT NULL,	
					MSG_READ VARCHAR(19) NULL,
					UPDATE_USER SMALLINT(11) NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_MESSAGE_USER  PRIMARY KEY (MSG_ID, USER_ID)) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			} // end if

			$cmd = " SELECT ADR_ID FROM PER_ADDRESS ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) {
				if($DPISDB=="odbc") 
					$cmd = " CREATE TABLE PER_ADDRESS (
					ADR_ID INTEGER NOT NULL,
					PER_ID INTEGER NOT NULL,
					ADR_TYPE SINGLE NOT NULL,	
					ADR_NO VARCHAR(100) NULL,
					ADR_ROAD VARCHAR(255) NULL,
					ADR_SOI VARCHAR(255) NULL,
					ADR_MOO VARCHAR(255) NULL,
					ADR_VILLAGE VARCHAR(255) NULL,
					ADR_BUILDING VARCHAR(255) NULL,
					ADR_DISTRICT VARCHAR(255) NULL,
					AP_CODE VARCHAR(10) NULL,
					PV_CODE VARCHAR(10) NULL,
					ADR_HOME_TEL VARCHAR(100) NULL,
					ADR_OFFICE_TEL VARCHAR(100) NULL,
					ADR_FAX VARCHAR(100) NULL,
					ADR_MOBILE VARCHAR(100) NULL,
					ADR_EMAIL VARCHAR(100) NULL,
					ADR_POSTCODE VARCHAR(100) NULL,
					ADR_REMARK VARCHAR(100) NULL,
					UPDATE_USER INTEGER2 NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_ADDRESS  PRIMARY KEY (ADR_ID)) ";
				elseif($DPISDB=="oci8") 
					$cmd = " CREATE TABLE PER_ADDRESS (
					ADR_ID NUMBER(10) NOT NULL,
					PER_ID NUMBER(10) NOT NULL,
					ADR_TYPE NUMBER(1) NOT NULL,	
					ADR_NO VARCHAR2(100) NULL,
					ADR_ROAD VARCHAR2(255) NULL,
					ADR_SOI VARCHAR2(255) NULL,
					ADR_MOO VARCHAR2(255) NULL,
					ADR_VILLAGE VARCHAR2(255) NULL,
					ADR_BUILDING VARCHAR2(255) NULL,
					ADR_DISTRICT VARCHAR2(255) NULL,
					AP_CODE VARCHAR2(10) NULL,
					PV_CODE VARCHAR2(10) NULL,
					ADR_HOME_TEL VARCHAR2(100) NULL,
					ADR_OFFICE_TEL VARCHAR2(100) NULL,
					ADR_FAX VARCHAR2(100) NULL,
					ADR_MOBILE VARCHAR2(100) NULL,
					ADR_EMAIL VARCHAR2(100) NULL,
					ADR_POSTCODE VARCHAR2(100) NULL,
					ADR_REMARK VARCHAR2(100) NULL,
					UPDATE_USER NUMBER(5) NOT NULL,
					UPDATE_DATE VARCHAR2(19) NOT NULL,		
					CONSTRAINT PK_PER_ADDRESS  PRIMARY KEY (ADR_ID)) ";
				elseif($DPISDB=="mysql") 
					$cmd = " CREATE TABLE PER_ADDRESS (
					ADR_ID INTEGER(10) NOT NULL,
					PER_ID INTEGER(10) NOT NULL,
					ADR_TYPE SMALLINT(1) NOT NULL,	
					ADR_NO VARCHAR(100) NULL,
					ADR_ROAD VARCHAR(255) NULL,
					ADR_SOI VARCHAR(255) NULL,
					ADR_MOO VARCHAR(255) NULL,
					ADR_VILLAGE VARCHAR(255) NULL,
					ADR_BUILDING VARCHAR(255) NULL,
					ADR_DISTRICT VARCHAR(255) NULL,
					AP_CODE VARCHAR(10) NULL,
					PV_CODE VARCHAR(10) NULL,
					ADR_HOME_TEL VARCHAR(100) NULL,
					ADR_OFFICE_TEL VARCHAR(100) NULL,
					ADR_FAX VARCHAR(100) NULL,
					ADR_MOBILE VARCHAR(100) NULL,
					ADR_EMAIL VARCHAR(100) NULL,
					ADR_POSTCODE VARCHAR(100) NULL,
					ADR_REMARK VARCHAR(100) NULL,
					UPDATE_USER SMALLINT(5) NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_ADDRESS  PRIMARY KEY (ADR_ID)) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				if($DPISDB=="odbc") 
					$cmd = " SELECT * INTO PER_WORKFLOW_ADDRESS FROM PER_ADDRESS WHERE UPDATE_DATE = 'x' ";
				elseif($DPISDB=="oci8" || $DPISDB=="mysql") 
					$cmd = " CREATE TABLE PER_WORKFLOW_ADDRESS AS SELECT * FROM PER_ADDRESS WHERE UPDATE_DATE = 'x' ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_WORKFLOW_ADDRESS ADD CONSTRAINT PK_PER_WORKFLOW_ADDRESS  
								  PRIMARY KEY (ADR_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				add_field("PER_WORKFLOW_ADDRESS", "ADR_WF_STATUS","VARCHAR", "2", "NULL");

				$cmd = " SELECT max(ADR_ID) as MAX_ID FROM PER_ADDRESS ";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				$MAX_ID = $data[MAX_ID] + 1;

				$cmd = " SELECT PER_ID, PER_ADD1, PER_ADD2 FROM PER_PERSONAL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
				while($data = $db_dpis->get_array()){
					$PER_ID = $data[PER_ID];
					$PER_ADD1 = trim($data[PER_ADD1]);
					$PER_ADD2 = trim($data[PER_ADD2]);

					$cmd = " SELECT ADR_ID FROM PER_ADDRESS WHERE PER_ID = $PER_ID AND ADR_TYPE = 1 ";
					$count_data = $db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
					if (!$count_data && $PER_ADD2) {
						$cmd = " INSERT INTO PER_ADDRESS (ADR_ID, PER_ID, ADR_TYPE, ADR_REMARK, UPDATE_USER, UPDATE_DATE) 
										VALUES ($MAX_ID, $PER_ID, 1, '$PER_ADD2', $SESS_USERID, '$UPDATE_DATE') ";
						$db_dpis1->send_cmd($cmd);
						//$db_dpis1->show_error();
						$MAX_ID++;
					} // end if

					$cmd = " SELECT ADR_ID FROM PER_ADDRESS WHERE PER_ID = $PER_ID AND ADR_TYPE = 2 ";
					$count_data = $db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
					if (!$count_data && $PER_ADD1) {
						$cmd = " INSERT INTO PER_ADDRESS (ADR_ID, PER_ID, ADR_TYPE, ADR_REMARK, UPDATE_USER, UPDATE_DATE) 
										VALUES ($MAX_ID, $PER_ID, 2, '$PER_ADD1', $SESS_USERID, '$UPDATE_DATE') ";
						$db_dpis1->send_cmd($cmd);
						//$db_dpis1->show_error();
						$MAX_ID++;
					} // end if
				} // end while

			} // end if

			$cmd = " SELECT TRAINNER_ID FROM PER_TRAINNER ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) {
				if($DPISDB=="odbc") 
					$cmd = " CREATE TABLE PER_TRAINNER(
					TRAINNER_ID INTEGER NOT NULL,	
					TRAINNER_NAME VARCHAR(255) NOT NULL,
					TN_GENDER SINGLE NULL,
					TN_INOUT_ORG SINGLE NULL,
					TN_BIRTHDATE VARCHAR(19) NULL,		
					TN_EDU_HIS1 MEMO NULL,
					TN_EDU_HIS2 MEMO NULL,
					TN_EDU_HIS3 MEMO NULL,
					TN_POSITION MEMO NULL,
					TN_WORK_PLACE MEMO NULL,
					TN_WORK_TEL MEMO NULL,
					TN_WORK_EXPERIENCE MEMO NULL,
					TN_TRAIN_EXPERIENCE MEMO NULL,
					TN_ADDRESS MEMO NULL,
					TN_ADDRESS_TEL MEMO NULL,
					TN_TECHNOLOGY_HIS MEMO NULL,
					TN_TRAIN_SKILL1 MEMO NULL,
					TN_TRAIN_SKILL2 MEMO NULL,
					TN_TRAIN_SKILL3 MEMO NULL,
					TN_DEPT_TRAIN MEMO NULL,
					TN_SPEC_ABILITY MEMO NULL,
					TN_HOBBY MEMO NULL,
					TN_SEQ INTEGER2 NULL,
					TN_ACTIVE SINGLE NULL,
					UPDATE_USER INTEGER2 NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_TRAINNER PRIMARY KEY (TRAINNER_ID)) ";
				elseif($DPISDB=="oci8") 
					$cmd = " CREATE TABLE PER_TRAINNER(
					TRAINNER_ID NUMBER(10) NOT NULL,	
					TRAINNER_NAME VARCHAR2(255) NOT NULL,
					TN_GENDER NUMBER(1) NULL,
					TN_INOUT_ORG NUMBER(1) NULL,
					TN_BIRTHDATE VARCHAR2(19) NULL,		
					TN_EDU_HIS1 VARCHAR2(2000) NULL,
					TN_EDU_HIS2 VARCHAR2(2000) NULL,
					TN_EDU_HIS3 VARCHAR2(2000) NULL,
					TN_POSITION VARCHAR2(2000) NULL,
					TN_WORK_PLACE VARCHAR2(2000) NULL,
					TN_WORK_TEL VARCHAR2(2000) NULL,
					TN_WORK_EXPERIENCE VARCHAR2(2000) NULL,
					TN_TRAIN_EXPERIENCE VARCHAR2(2000) NULL,
					TN_ADDRESS VARCHAR2(2000) NULL,
					TN_ADDRESS_TEL VARCHAR2(2000) NULL,
					TN_TECHNOLOGY_HIS VARCHAR2(2000) NULL,
					TN_TRAIN_SKILL1 VARCHAR2(2000) NULL,
					TN_TRAIN_SKILL2 VARCHAR2(2000) NULL,
					TN_TRAIN_SKILL3 VARCHAR2(2000) NULL,
					TN_DEPT_TRAIN VARCHAR2(2000) NULL,
					TN_SPEC_ABILITY VARCHAR2(2000) NULL,
					TN_HOBBY VARCHAR2(2000) NULL,
					TN_SEQ NUMBER(3) NULL,
					TN_ACTIVE NUMBER(1) NULL,
					UPDATE_USER NUMBER(5) NOT NULL,
					UPDATE_DATE VARCHAR2(19) NOT NULL,		
					CONSTRAINT PK_PER_TRAINNER PRIMARY KEY (TRAINNER_ID)) ";
				elseif($DPISDB=="mysql")
					$cmd = " CREATE TABLE PER_TRAINNER(
					TRAINNER_ID INTEGER(10) NOT NULL,	
					TRAINNER_NAME VARCHAR(255) NOT NULL,
					TN_GENDER SMALLINT(1) NULL,
					TN_INOUT_ORG SMALLINT(1) NULL,
					TN_BIRTHDATE VARCHAR(19) NULL,		
					TN_EDU_HIS1 TEXT NULL,
					TN_EDU_HIS2 TEXT NULL,
					TN_EDU_HIS3 TEXT NULL,
					TN_POSITION TEXT NULL,
					TN_WORK_PLACE TEXT NULL,
					TN_WORK_TEL TEXT NULL,
					TN_WORK_EXPERIENCE TEXT NULL,
					TN_TRAIN_EXPERIENCE TEXT NULL,
					TN_ADDRESS TEXT NULL,
					TN_ADDRESS_TEL TEXT NULL,
					TN_TECHNOLOGY_HIS TEXT NULL,
					TN_TRAIN_SKILL1 TEXT NULL,
					TN_TRAIN_SKILL2 TEXT NULL,
					TN_TRAIN_SKILL3 TEXT NULL,
					TN_DEPT_TRAIN TEXT NULL,
					TN_SPEC_ABILITY TEXT NULL,
					TN_HOBBY TEXT NULL,
					TN_SEQ SMALLINT(3) NULL,
					TN_ACTIVE SMALLINT(1) NULL,
					UPDATE_USER SMALLINT(5) NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_TRAINNER PRIMARY KEY (TRAINNER_ID)) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			} // end if

			$cmd = " SELECT PLAN_ID FROM PER_TRAIN_PLAN ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) {
				if($DPISDB=="odbc") 
					$cmd = " CREATE TABLE PER_TRAIN_PLAN(
					PLAN_ID INTEGER NOT NULL,	
					PLAN_NAME VARCHAR(255) NOT NULL,
					TP_BUDGET_YEAR VARCHAR(4) NULL,
					TP_INOUT_PLAN SINGLE NULL,
					TP_ZONE INTEGER2 NULL,
					PLAN_ID_REF INTEGER NULL,	
					DEPARTMENT_ID INTEGER NULL,	
					TP_ACTIVE SINGLE NULL,
					UPDATE_USER INTEGER2 NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_TRAIN_PLAN PRIMARY KEY (PLAN_ID)) ";
				elseif($DPISDB=="oci8") 
					$cmd = " CREATE TABLE PER_TRAIN_PLAN(
					PLAN_ID NUMBER(10) NOT NULL,	
					PLAN_NAME VARCHAR2(255) NOT NULL,
					TP_BUDGET_YEAR VARCHAR2(4) NULL,		
					TP_INOUT_PLAN NUMBER(1) NULL,
					TP_ZONE NUMBER(3) NULL,
					PLAN_ID_REF NUMBER(10) NULL,	
					DEPARTMENT_ID NUMBER(10) NULL,	
					TP_ACTIVE NUMBER(1) NULL,
					UPDATE_USER NUMBER(5) NOT NULL,
					UPDATE_DATE VARCHAR2(19) NOT NULL,		
					CONSTRAINT PK_PER_TRAIN_PLAN PRIMARY KEY (PLAN_ID)) ";
				elseif($DPISDB=="mysql")
					$cmd = " CREATE TABLE PER_TRAIN_PLAN(
					PLAN_ID INTEGER(10) NOT NULL,	
					PLAN_NAME VARCHAR(255) NOT NULL,
					TP_BUDGET_YEAR VARCHAR(4) NULL,
					TP_INOUT_PLAN SMALLINT(1) NULL,
					TP_ZONE SMALLINT(3) NOT NULL,
					PLAN_ID_REF INTEGER(10) NULL,	
					DEPARTMENT_ID INTEGER(10) NULL,	
					TP_ACTIVE SMALLINT(1) NULL,
					UPDATE_USER SMALLINT(5) NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_TRAIN_PLAN PRIMARY KEY (PLAN_ID)) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			} // end if

			$cmd = " SELECT PG_ID FROM PER_PROJECT_GROUP ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) {
				if($DPISDB=="odbc") 
					$cmd = " CREATE TABLE PER_PROJECT_GROUP(
					PG_ID INTEGER NOT NULL,	
					PG_NAME VARCHAR(255) NOT NULL,
					PG_SEQ_NO INTEGER2 NULL,
					PG_ACTIVE SINGLE NULL,
					UPDATE_USER INTEGER2 NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_PROJECT_GROUP PRIMARY KEY (PG_ID)) ";
				elseif($DPISDB=="oci8") 
					$cmd = " CREATE TABLE PER_PROJECT_GROUP(
					PG_ID NUMBER(10) NOT NULL,	
					PG_NAME VARCHAR2(255) NOT NULL,
					PG_SEQ_NO NUMBER(5) NULL,
					PG_ACTIVE NUMBER(1) NULL,
					UPDATE_USER NUMBER(5) NOT NULL,
					UPDATE_DATE VARCHAR2(19) NOT NULL,		
					CONSTRAINT PK_PER_PROJECT_GROUP PRIMARY KEY (PG_ID)) ";
				elseif($DPISDB=="mysql")
					$cmd = " CREATE TABLE PER_PROJECT_GROUP(
					PG_ID INTEGER(10) NOT NULL,	
					PG_NAME VARCHAR(255) NOT NULL,
					PG_SEQ_NO SMALLINT(5) NULL,
					PG_ACTIVE SMALLINT(1) NULL,
					UPDATE_USER SMALLINT(5) NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_PROJECT_GROUP PRIMARY KEY (PG_ID)) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			} // end if

			$cmd = " SELECT PP_ID FROM PER_PROJECT_PAYMENT ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) {
				if($DPISDB=="odbc") 
					$cmd = " CREATE TABLE PER_PROJECT_PAYMENT(
					PP_ID INTEGER NOT NULL,	
					PP_NAME VARCHAR(255) NOT NULL,
					PP_SEQ_NO INTEGER2 NULL,
					PP_ACTIVE SINGLE NULL,
					UPDATE_USER INTEGER2 NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_PROJECT_PAYMENT PRIMARY KEY (PP_ID)) ";
				elseif($DPISDB=="oci8") 
					$cmd = " CREATE TABLE PER_PROJECT_PAYMENT(
					PP_ID NUMBER(10) NOT NULL,	
					PP_NAME VARCHAR2(255) NOT NULL,
					PP_SEQ_NO NUMBER(5) NULL,
					PP_ACTIVE NUMBER(1) NULL,
					UPDATE_USER NUMBER(5) NOT NULL,
					UPDATE_DATE VARCHAR2(19) NOT NULL,		
					CONSTRAINT PK_PER_PROJECT_PAYMENT PRIMARY KEY (PP_ID)) ";
				elseif($DPISDB=="mysql")
					$cmd = " CREATE TABLE PER_PROJECT_PAYMENT(
					PP_ID INTEGER(10) NOT NULL,	
					PP_NAME VARCHAR(255) NOT NULL,
					PP_SEQ_NO SMALLINT(5) NULL,
					PP_ACTIVE SMALLINT(1) NULL,
					UPDATE_USER SMALLINT(5) NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_PROJECT_PAYMENT PRIMARY KEY (PP_ID)) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			} // end if

			$cmd = " SELECT PROJ_ID FROM PER_TRAIN_PROJECT ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) {
				if($DPISDB=="odbc") 
					$cmd = " CREATE TABLE PER_TRAIN_PROJECT(
					PROJ_ID INTEGER NOT NULL,	
					PLAN_ID INTEGER NOT NULL,	
					PROJ_NAME VARCHAR(255) NOT NULL,
					TPJ_BUDGET_YEAR VARCHAR(4) NULL,		
					TPJ_MANAGE_ORG VARCHAR(255) NULL,		
					TPJ_RESPONSE_ORG VARCHAR(255) NULL,		
					TPJ_APP_PER_ID INTEGER NULL,	
					PG_ID INTEGER NOT NULL,	
					TPJ_APP_DATE VARCHAR(19) NULL,		
					TPJ_APP_DOC_NO VARCHAR(100) NULL,		
					TPJ_INOUT_TRAIN SINGLE NULL,
					TPJ_ZONE INTEGER2 NULL,
					PROJ_ID_REF INTEGER NOT NULL,	
					DEPARTMENT_ID INTEGER NOT NULL,	
					TPJ_ACTIVE SINGLE NULL,
					UPDATE_USER INTEGER2 NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_TRAIN_PROJECT PRIMARY KEY (PROJ_ID)) ";
				elseif($DPISDB=="oci8") 
					$cmd = " CREATE TABLE PER_TRAIN_PROJECT(
					PROJ_ID NUMBER(10) NOT NULL,	
					PLAN_ID NUMBER(10) NOT NULL,	
					PROJ_NAME VARCHAR2(255) NOT NULL,
					TPJ_BUDGET_YEAR VARCHAR2(4) NULL,		
					TPJ_MANAGE_ORG VARCHAR2(255) NULL,		
					TPJ_RESPONSE_ORG VARCHAR2(255) NULL,		
					TPJ_APP_PER_ID NUMBER(10) NULL,	
					PG_ID NUMBER(10) NOT NULL,	
					TPJ_APP_DATE VARCHAR2(19) NULL,		
					TPJ_APP_DOC_NO VARCHAR2(100) NULL,		
					TPJ_INOUT_TRAIN NUMBER(1) NULL,
					TPJ_ZONE NUMBER(3) NULL,
					PROJ_ID_REF NUMBER(10) NULL,	
					DEPARTMENT_ID NUMBER(10) NULL,	
					TPJ_ACTIVE NUMBER(1) NULL,
					UPDATE_USER NUMBER(5) NOT NULL,
					UPDATE_DATE VARCHAR2(19) NOT NULL,		
					CONSTRAINT PK_PER_TRAIN_PROJECT PRIMARY KEY (PROJ_ID)) ";
				elseif($DPISDB=="mysql")
					$cmd = " CREATE TABLE PER_TRAIN_PROJECT(
					PROJ_ID INTEGER(10) NOT NULL,	
					PLAN_ID INTEGER(10) NOT NULL,	
					PROJ_NAME VARCHAR(255) NOT NULL,
					TPJ_BUDGET_YEAR VARCHAR(4) NOT NULL,
					TPJ_MANAGE_ORG VARCHAR(255) NULL,		
					TPJ_RESPONSE_ORG VARCHAR(255) NULL,		
					TPJ_APP_PER_ID INTEGER(10) NULL,	
					PG_ID INTEGER(10) NOT NULL,	
					TPJ_APP_DATE VARCHAR(19) NULL,		
					TPJ_APP_DOC_NO VARCHAR(100) NULL,		
					TPJ_INOUT_TRAIN SMALLINT(1) NULL,
					TPJ_ZONE SMALLINT(3) NULL,
					PROJ_ID_REF INTEGER(10) NULL,	
					DEPARTMENT_ID INTEGER(10) NULL,	
					TPJ_ACTIVE SMALLINT(1) NULL,
					UPDATE_USER SMALLINT(5) NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_TRAIN_PROJECT PRIMARY KEY (PROJ_ID)) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			} // end if

			$cmd = " SELECT PROJ_ID FROM PER_TRAIN_PROJECT_DTL ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) {
				if($DPISDB=="odbc") 
					$cmd = " CREATE TABLE PER_TRAIN_PROJECT_DTL(
					PROJ_ID INTEGER NOT NULL,	
					TR_CODE VARCHAR(20) NOT NULL,	
					TR_CLASS INTERGER2 NULL,
					MAX_DAY INTERGER2 NULL,
					TRAIN_PLACE VARCHAR(255) NOT NULL,
					TARGET_POSITION MEMO NULL,		
					LEVEL_NO_START VARCHAR(10) NULL,		
					LEVEL_NO_END VARCHAR(10) NULL,		
					START_DATE VARCHAR(19) NULL,		
					END_DATE VARCHAR(19) NULL,		
					BUDGET NUMBER NULL,	
					BUDGET_USED NUMBER NULL,	
					LOCAL_TAX NUMBER NULL,	
					LOCAL_TAX_USED NUMBER NULL,	
					PER_DEVELOP_FUND NUMBER NULL,	
					PER_DEVELOP_FUND_USED NUMBER NULL,	
					OTHER_BUDGET NUMBER NULL,	
					OTHER_BUDGET_USED NUMBER NULL,	
					UPDATE_USER INTEGER2 NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_TRAIN_PROJECT_DTL PRIMARY KEY (PROJ_ID, TR_CODE, TR_CLASS)) ";
				elseif($DPISDB=="oci8") 
					$cmd = " CREATE TABLE PER_TRAIN_PROJECT_DTL(
					PROJ_ID NUMBER(10) NOT NULL,	
					TR_CODE VARCHAR2(20) NOT NULL,	
					TR_CLASS NUMBER(3) NULL,
					MAX_DAY NUMBER(3) NULL,
					TRAIN_PLACE VARCHAR2(255) NOT NULL,
					TARGET_POSITION VARCHAR2(1000) NULL,		
					LEVEL_NO_START VARCHAR2(10) NULL,		
					LEVEL_NO_END VARCHAR2(10) NULL,		
					START_DATE VARCHAR2(19) NULL,		
					END_DATE VARCHAR2(19) NULL,		
					BUDGET NUMBER(16,2) NULL,	
					BUDGET_USED NUMBER(16,2) NULL,	
					LOCAL_TAX NUMBER(16,2) NULL,	
					LOCAL_TAX_USED NUMBER(16,2) NULL,	
					PER_DEVELOP_FUND NUMBER(16,2) NULL,	
					PER_DEVELOP_FUND_USED NUMBER(16,2) NULL,	
					OTHER_BUDGET NUMBER(16,2) NULL,	
					OTHER_BUDGET_USED NUMBER(16,2) NULL,	
					UPDATE_USER NUMBER(5) NOT NULL,
					UPDATE_DATE VARCHAR2(19) NOT NULL,		
					CONSTRAINT PK_PER_TRAIN_PROJECT_DTL PRIMARY KEY (PROJ_ID, TR_CODE, TR_CLASS)) ";
				elseif($DPISDB=="mysql")
					$cmd = " CREATE TABLE PER_TRAIN_PROJECT_DTL(
					PROJ_ID INTEGER(10) NOT NULL,	
					TR_CODE VARCHAR(20) NOT NULL,	
					TR_CLASS SMALLINT(3) NULL,
					MAX_DAY SMALLINT(3) NULL,
					TRAIN_PLACE VARCHAR(255) NOT NULL,
					TARGET_POSITION TEXT NULL,		
					LEVEL_NO_START VARCHAR(10) NULL,		
					LEVEL_NO_END VARCHAR(10) NULL,		
					START_DATE VARCHAR(19) NULL,		
					END_DATE VARCHAR(19) NULL,		
					BUDGET DECIMAL(16,2) NULL,	
					BUDGET_USED DECIMAL(16,2) NULL,	
					LOCAL_TAX DECIMAL(16,2) NULL,	
					LOCAL_TAX_USED DECIMAL(16,2) NULL,	
					PER_DEVELOP_FUND DECIMAL(16,2) NULL,	
					PER_DEVELOP_FUND_USED DECIMAL(16,2) NULL,	
					OTHER_BUDGET DECIMAL(16,2) NULL,	
					OTHER_BUDGET_USED DECIMAL(16,2) NULL,	
					UPDATE_USER SMALLINT(5) NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_TRAIN_PROJECT_DTL PRIMARY KEY (PROJ_ID, TR_CODE, TR_CLASS)) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			} // end if

			$cmd = " SELECT PROJ_ID FROM PER_TRAIN_PROJECT_PAYMENT ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) {
				if($DPISDB=="odbc") 
					$cmd = " CREATE TABLE PER_TRAIN_PROJECT_PAYMENT(
					PROJ_ID INTEGER NOT NULL,	
					TR_CODE VARCHAR(20) NOT NULL,	
					TR_CLASS INTEGER2 NOT NULL,
					PP_ID INTEGER NOT NULL,
					BUDGET_SOURCE VARCHAR(50) NULL,
					OTHER_PAYMENT VARCHAR(255) NULL,		
					PAY_AMOUNT NUMBER NULL,	
					UPDATE_USER INTEGER2 NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_TRAIN_PROJECT_PAYMENT PRIMARY KEY (PROJ_ID, TR_CODE, TR_CLASS, PP_ID)) ";
				elseif($DPISDB=="oci8") 
					$cmd = " CREATE TABLE PER_TRAIN_PROJECT_PAYMENT(
					PROJ_ID NUMBER(10) NOT NULL,	
					TR_CODE VARCHAR2(20) NOT NULL,	
					TR_CLASS NUMBER(3) NOT NULL,
					PP_ID NUMBER(10) NOT NULL,
					BUDGET_SOURCE VARCHAR2(50) NULL,
					OTHER_PAYMENT VARCHAR2(255) NULL,		
					PAY_AMOUNT NUMBER(16,2) NULL,	
					UPDATE_USER NUMBER(5) NOT NULL,
					UPDATE_DATE VARCHAR2(19) NOT NULL,		
					CONSTRAINT PK_PER_TRAIN_PROJECT_PAYMENT PRIMARY KEY (PROJ_ID, TR_CODE, TR_CLASS, PP_ID)) ";
				elseif($DPISDB=="mysql")
					$cmd = " CREATE TABLE PER_TRAIN_PROJECT_PAYMENT(
					PROJ_ID INTEGER(10) NOT NULL,	
					TR_CODE VARCHAR(20) NOT NULL,	
					TR_CLASS SMALLINT(3) NOT NULL,
					PP_ID INTEGER(10) NOT NULL,
					BUDGET_SOURCE VARCHAR(50) NULL,
					OTHER_PAYMENT VARCHAR(255) NULL,		
					PAY_AMOUNT DECIMAL(16,2) NULL,	
					UPDATE_USER SMALLINT(5) NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_TRAIN_PROJECT_PAYMENT PRIMARY KEY (PROJ_ID, TR_CODE, TR_CLASS, PP_ID)) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			} // end if

			$cmd = " SELECT PROJ_ID FROM PER_TRAIN_PROJECT_PERSONAL ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) {
				if($DPISDB=="odbc") 
					$cmd = " CREATE TABLE PER_TRAIN_PROJECT_PERSONAL(
					PROJ_ID INTEGER NOT NULL,	
					TR_CODE VARCHAR(20) NOT NULL,	
					TR_CLASS INTEGER2 NOT NULL,
					PER_ID INTEGER NOT NULL,
					ORG_NAME VARCHAR(255) NULL,
					PL_NAME VARCHAR(255) NULL,		
					LEVEL_NO VARCHAR(10) NULL,		
					UPDATE_USER INTEGER2 NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_TRAIN_PROJECT_PERSONAL PRIMARY KEY (PROJ_ID, TR_CODE, TR_CLASS, PER_ID)) ";
				elseif($DPISDB=="oci8") 
					$cmd = " CREATE TABLE PER_TRAIN_PROJECT_PERSONAL(
					PROJ_ID NUMBER(10) NOT NULL,	
					TR_CODE VARCHAR2(20) NOT NULL,	
					TR_CLASS NUMBER(3) NOT NULL,
					PER_ID NUMBER(10) NOT NULL,
					ORG_NAME VARCHAR2(255) NULL,
					PL_NAME VARCHAR2(255) NULL,		
					LEVEL_NO VARCHAR2(10) NULL,		
					UPDATE_USER NUMBER(5) NOT NULL,
					UPDATE_DATE VARCHAR2(19) NOT NULL,		
					CONSTRAINT PK_PER_TRAIN_PROJECT_PERSONAL PRIMARY KEY (PROJ_ID, TR_CODE, TR_CLASS, PER_ID)) ";
				elseif($DPISDB=="mysql")
					$cmd = " CREATE TABLE PER_TRAIN_PROJECT_PERSONAL(
					PROJ_ID INTEGER(10) NOT NULL,	
					TR_CODE VARCHAR(20) NOT NULL,	
					TR_CLASS SMALLINT(3) NOT NULL,
					PER_ID INTEGER(10) NOT NULL,
					ORG_NAME VARCHAR(255) NULL,
					PL_NAME VARCHAR(255) NULL,		
					LEVEL_NO VARCHAR(10) NULL,		
					UPDATE_USER SMALLINT(5) NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_TRAIN_PROJECT_PERSONAL PRIMARY KEY (PROJ_ID, TR_CODE, TR_CLASS, PER_ID)) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			} // end if

			$count_data = add_field("PER_POS_MOVE", "DEPARTMENT_ID","INTEGER", "10", "NULL");
			if (!$count_data) {
				if ($CTRL_TYPE == 4)	{
					$cmd = " UPDATE PER_POS_MOVE SET DEPARTMENT_ID=$DEPARTMENT_ID WHERE DEPARTMENT_ID IS NULL ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				} else {
					if($DPISDB=="odbc" || $DPISDB=="mysql"){ 
						$cmd = " SELECT POS_ID, ORG_ID FROM PER_POS_MOVE WHERE ORG_ID IS NOT NULL ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
						while($data = $db_dpis->get_array()){
							$POS_ID = $data[POS_ID];
							$ORG_ID = $data[ORG_ID];
							$cmd = " SELECT ORG_ID_REF FROM PER_ORG WHERE ORG_ID = $ORG_ID ";
							$db_dpis1->send_cmd($cmd);
							//$db_dpis1->show_error();
							$data1 = $db_dpis1->get_array();
							$ORG_ID_REF = $data1[ORG_ID_REF];
							$cmd = " UPDATE PER_POS_MOVE SET DEPARTMENT_ID = $ORG_ID_REF WHERE POS_ID = $POS_ID ";
							$db_dpis1->send_cmd($cmd);
							//$db_dpis1->show_error();
						} // end while						
					} elseif($DPISDB=="oci8"){
						$cmd = " UPDATE PER_POS_MOVE A SET A.DEPARTMENT_ID = (SELECT D.ORG_ID_REF 
										  FROM PER_ORG C, PER_ORG D WHERE A.ORG_ID = C.ORG_ID AND C.ORG_ID = D.ORG_ID) ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
					} // end if
				} // end if
			}

			$cmd = " UPDATE PER_COMMAND SET COM_DATE = NULL WHERE COM_DATE = '-543--' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POSITIONHIS SET POH_EFFECTIVEDATE = '-' WHERE POH_EFFECTIVEDATE = '-543--' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_SALARYHIS SET SAH_EFFECTIVEDATE = '-' WHERE SAH_EFFECTIVEDATE = '-543--' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POSITION SET POS_DATE = NULL WHERE POS_DATE = '-543-00-00' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POSITION SET POS_GET_DATE = NULL WHERE POS_DATE = '-543-00-00' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POSITION SET POS_CHANGE_DATE = NULL WHERE POS_DATE = '-543-00-00' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " UPDATE PER_POSITIONHIS SET POH_EFFECTIVEDATE = LEFT(POH_EFFECTIVEDATE, 10) ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_POSITIONHIS SET POH_EFFECTIVEDATE = SUBSTR(POH_EFFECTIVEDATE, 1, 10) ";
			elseif($DPISDB=="mysql")
				$cmd = " UPDATE PER_POSITIONHIS SET POH_EFFECTIVEDATE = LEFT(POH_EFFECTIVEDATE, 10) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " UPDATE PER_POSITIONHIS SET POH_ENDDATE = LEFT(POH_ENDDATE, 10) ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_POSITIONHIS SET POH_ENDDATE = SUBSTR(POH_ENDDATE, 1, 10) ";
			elseif($DPISDB=="mysql")
				$cmd = " UPDATE PER_POSITIONHIS SET POH_ENDDATE = LEFT(POH_ENDDATE, 10) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " UPDATE PER_POSITIONHIS SET POH_DOCDATE = LEFT(POH_DOCDATE, 10) ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_POSITIONHIS SET POH_DOCDATE = SUBSTR(POH_DOCDATE, 1, 10) ";
			elseif($DPISDB=="mysql")
				$cmd = " UPDATE PER_POSITIONHIS SET POH_DOCDATE = LEFT(POH_DOCDATE, 10) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_EDUCLEVEL", "EL_CGD_CODE","VARCHAR", "10", "NULL");
			add_field("PER_EDUCNAME", "EN_CGD_CODE","VARCHAR", "10", "NULL");
			add_field("PER_EDUCMAJOR", "EM_CGD_CODE","VARCHAR", "10", "NULL");
			add_field("PER_INSTITUTE", "INS_CGD_CODE","VARCHAR", "10", "NULL");

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_educlevel.html?table=PER_EDUCLEVEL' 
							  WHERE LINKTO_WEB = 'master_table.html?table=PER_EDUCLEVEL' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_educmajor.html?table=PER_EDUCMAJOR' 
							  WHERE LINKTO_WEB = 'master_table.html?table=PER_EDUCMAJOR' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$count_data = add_field("PER_COMPETENCE", "DEPARTMENT_ID","INTEGER", "10", "NULL");
			if (!$count_data) {
				if ($CTRL_TYPE == 4)	{
					$cmd = " UPDATE PER_COMPETENCE SET DEPARTMENT_ID=$DEPARTMENT_ID WHERE DEPARTMENT_ID IS NULL ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				} // end if

				if($DPISDB=="oci8") {
					$cmd = " ALTER TABLE PER_COMPETENCE MODIFY DEPARTMENT_ID NOT NULL ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();

					$cmd = " ALTER TABLE PER_COMPETENCE DROP CONSTRAINT PK_PER_COMPETENCE ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();

					$cmd = " DROP INDEX PK_PER_COMPETENCE ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();

					$cmd = " ALTER TABLE PER_COMPETENCE ADD CONSTRAINT PK_PER_COMPETENCE PRIMARY KEY 
									  (CP_CODE, DEPARTMENT_ID) ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				}

				add_field("PER_COMPETENCE_LEVEL", "DEPARTMENT_ID","INTEGER", "10", "NULL");

				if ($CTRL_TYPE == 4)	{
					$cmd = " UPDATE PER_COMPETENCE_LEVEL SET DEPARTMENT_ID=$DEPARTMENT_ID WHERE DEPARTMENT_ID IS NULL ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				} // end if

				if($DPISDB=="oci8") {
					$cmd = " ALTER TABLE PER_COMPETENCE_LEVEL MODIFY DEPARTMENT_ID NOT NULL ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();

					$cmd = " ALTER TABLE PER_COMPETENCE_LEVEL DROP CONSTRAINT PK_PER_COMPETENCE_LEVEL ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();

					$cmd = " DROP INDEX PK_PER_COMPETENCE_LEVEL ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();

					$cmd = " ALTER TABLE PER_COMPETENCE_LEVEL ADD CONSTRAINT PK_PER_COMPETENCE_LEVEL PRIMARY KEY 
									  (CP_CODE, DEPARTMENT_ID, CL_NO) ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				}
			}

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_POSITION, PER_PERSONAL SET PER_POSITION.LEVEL_NO = PER_PERSONAL.LEVEL_NO 
								  WHERE PER_POSITION.POS_ID = PER_PERSONAL.POS_ID AND PER_PERSONAL.PER_TYPE=1 AND 
								  PER_PERSONAL.PER_STATUS=1 AND PER_POSITION.LEVEL_NO IS NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_POSITION A SET A.LEVEL_NO = 
								  (SELECT B.LEVEL_NO FROM PER_PERSONAL B WHERE A.POS_ID = B.POS_ID AND B.PER_TYPE=1 AND B.PER_STATUS=1) WHERE A.LEVEL_NO IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_POSITION, PER_CO_LEVEL SET PER_POSITION.LEVEL_NO = 
								  PER_CO_LEVEL.LEVEL_NO_MIN WHERE PER_POSITION.CL_NAME = PER_CO_LEVEL.CL_NAME AND PER_POSITION.LEVEL_NO IS NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_POSITION A SET A.LEVEL_NO = 
								  (SELECT B.LEVEL_NO_MIN FROM PER_CO_LEVEL B WHERE A.CL_NAME = B.CL_NAME) WHERE A.LEVEL_NO IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'rpt_R004000_select_person.html' WHERE LINKTO_WEB = 'rpt_R004000.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$el_code = array(	"00", "01", "02", "05", "10", "20", "30", "40", "50", "60", "70", "80", "90", "99" );
			$el_cgd_code = array(	"97", "31", "33", "40", "41", "42", "43", "51", "52", "53", "54", "55", "56", "99" );
			for ( $i=0; $i<count($el_code); $i++ ) { 
				$cmd = " UPDATE PER_EDUCLEVEL SET EL_CGD_CODE = '$el_cgd_code[$i]' WHERE EL_CODE = '$el_code[$i]' AND EL_CGD_CODE IS NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			}

			$cmd = " SELECT EB_CODE FROM PER_EXPENSE_BUDGET ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) {
				if($DPISDB=="odbc") 
					$cmd = " CREATE TABLE PER_EXPENSE_BUDGET(
					EB_CODE VARCHAR(10) NOT NULL,	
					EB_NAME VARCHAR(255) NOT NULL,
					EB_ACTIVE SINGLE NULL,
					UPDATE_USER INTEGER2 NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					EB_SEQ_NO INTEGER2 NULL,
					CONSTRAINT PK_PER_EXPENSE_BUDGET PRIMARY KEY (EB_CODE)) ";
				elseif($DPISDB=="oci8") 
					$cmd = " CREATE TABLE PER_EXPENSE_BUDGET(
					EB_CODE VARCHAR2(10) NOT NULL,	
					EB_NAME VARCHAR2(255) NOT NULL,
					EB_ACTIVE NUMBER(1) NULL,
					UPDATE_USER NUMBER(5) NOT NULL,
					UPDATE_DATE VARCHAR2(19) NOT NULL,		
					EB_SEQ_NO NUMBER(5) NULL,
					CONSTRAINT PK_PER_EXPENSE_BUDGET PRIMARY KEY (EB_CODE)) ";
				elseif($DPISDB=="mysql")
					$cmd = " CREATE TABLE PER_EXPENSE_BUDGET(
					EB_CODE VARCHAR(10) NOT NULL,	
					EB_NAME VARCHAR(255) NOT NULL,
					EB_ACTIVE SMALLINT(1) NULL,
					UPDATE_USER SMALLINT(5) NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					EB_SEQ_NO SMALLINT(5) NULL,
					CONSTRAINT PK_PER_EXPENSE_BUDGET PRIMARY KEY (EB_CODE)) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			} // end if

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 
							  WHERE LINKTO_WEB = 'master_table.html?table=PER_EXPENSE_BUDGET' ";
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
								  VALUES (1, 'TH', $MAX_ID, 13, 'M0113 งบรายจ่าย', 'S', 'W', 
								  'master_table.html?table=PER_EXPENSE_BUDGET', 0, 9, 296, $CREATE_DATE, $UPDATE_BY, 
								  $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 13, 'M0113 งบรายจ่าย', 'S', 'W', 
								  'master_table.html?table=PER_EXPENSE_BUDGET', 0, 9, 296, $CREATE_DATE, $UPDATE_BY, 
								  $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_POSITIONHIS, PER_LINE SET PER_POSITIONHIS.POH_PL_NAME = PER_LINE.PL_NAME 
								  WHERE PER_POSITIONHIS.PL_CODE = PER_LINE.PL_CODE AND PER_POSITIONHIS.PL_CODE IS NOT NULL AND PER_POSITIONHIS.POH_PL_NAME IS NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_POSITIONHIS A SET A.POH_PL_NAME = 
								  (SELECT B.PL_NAME FROM PER_LINE B WHERE A.PL_CODE = B.PL_CODE) WHERE A.PL_CODE IS NOT NULL AND A.POH_PL_NAME IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_POSITIONHIS, PER_MGT SET PER_POSITIONHIS.POH_PM_NAME = PER_MGT.PM_NAME 
								  WHERE PER_POSITIONHIS.PM_CODE = PER_MGT.PM_CODE AND PER_POSITIONHIS.PM_CODE IS NOT NULL AND PER_POSITIONHIS.POH_PM_NAME IS NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_POSITIONHIS A SET A.POH_PM_NAME = 
								  (SELECT B.PM_NAME FROM PER_MGT B WHERE A.PM_CODE = B.PM_CODE) WHERE A.PM_CODE IS NOT NULL AND A.POH_PM_NAME IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_POSITIONHIS, PER_ORG SET PER_POSITIONHIS.POH_ORG3 = PER_ORG.ORG_NAME 
								  WHERE PER_POSITIONHIS.ORG_ID_3 = PER_ORG.ORG_ID AND PER_POSITIONHIS.ORG_ID_3 IS NOT NULL AND 
								  PER_POSITIONHIS.ORG_ID_3 != 1 AND PER_POSITIONHIS.POH_ORG3 IS NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_POSITIONHIS A SET A.POH_ORG3 = 
								  (SELECT B.ORG_NAME FROM PER_ORG B WHERE A.ORG_ID_3 = B.ORG_ID) WHERE A.ORG_ID_3 IS NOT NULL AND A.ORG_ID_3 != 1 AND A.POH_ORG3 IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_POSITIONHIS SET POH_ORG = trim(trim(POH_UNDER_ORG2)+' '+trim(POH_UNDER_ORG1)+' '+trim(POH_ORG3)+' '+trim(POH_ORG2))
								  WHERE POH_ORG IS NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_POSITIONHIS SET POH_ORG = trim(trim(POH_UNDER_ORG2)||' '||trim(POH_UNDER_ORG1)||' '||trim(POH_ORG3)||' '||trim(POH_ORG2))
								  WHERE POH_ORG IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
/*
			if ($SESS_DEPARTMENT_NAME!="กรมการปกครอง") {
				$cmd = " UPDATE PER_PERSONAL SET  PAY_ID = POS_ID where PAY_ID != POS_ID ";
				$db_dpis->send_cmd($cmd);	
			}
*/
			$cmd = " SELECT SERVER_ID FROM OTH_SERVER ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) {
				if($DPISDB=="odbc") 
					$cmd = " CREATE TABLE OTH_SERVER(
					SERVER_ID INTEGER NOT NULL,	
					SERVER_NAME VARCHAR(255) NULL,
					FTP_SERVER VARCHAR(255) NULL,
					FTP_USERNAME VARCHAR(255) NULL,
					FTP_PASSWORD VARCHAR(255) NULL,
					MAIN_PATH VARCHAR(255) NULL,
					HTTP_SERVER VARCHAR(255) NULL,
					UPDATE_USER INTEGER2 NULL,
					UPDATE_DATE VARCHAR(19) NULL,		
					CONSTRAINT PK_OTH_SERVER PRIMARY KEY (SERVER_ID)) ";
				elseif($DPISDB=="oci8") 
					$cmd = " CREATE TABLE OTH_SERVER(
					SERVER_ID NUMBER(10) NOT NULL,	
					SERVER_NAME VARCHAR2(255) NULL,
					FTP_SERVER VARCHAR2(255) NULL,		
					FTP_USERNAME VARCHAR2(255) NULL,
					FTP_PASSWORD VARCHAR2(255) NULL,		
					MAIN_PATH VARCHAR2(255) NULL,
					HTTP_SERVER VARCHAR2(255) NULL,		
					UPDATE_USER NUMBER(5) NULL,
					UPDATE_DATE VARCHAR2(19) NULL,		
					CONSTRAINT PK_OTH_SERVER PRIMARY KEY (SERVER_ID)) ";
				elseif($DPISDB=="mysql")
					$cmd = " CREATE TABLE OTH_SERVER(
					SERVER_ID INTEGER(10) NOT NULL,	
					SERVER_NAME VARCHAR(255) NULL,
					FTP_SERVER VARCHAR(255) NULL,
					FTP_USERNAME VARCHAR(255) NULL,
					FTP_PASSWORD VARCHAR(255) NULL,
					MAIN_PATH VARCHAR(255) NULL,
					HTTP_SERVER VARCHAR(255) NULL,
					UPDATE_USER SMALLINT(5) NULL,
					UPDATE_DATE VARCHAR(19) NULL,		
					CONSTRAINT PK_OTH_SERVER PRIMARY KEY (SERVER_ID)) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO OTH_SERVER ( SERVER_ID, SERVER_NAME, FTP_SERVER, 
								  FTP_USERNAME, FTP_PASSWORD, MAIN_PATH, HTTP_SERVER ) 
								  VALUES (1, 'localhost', 'ftp.localhost.com', 'dpis@localhost.com', 'dpis', '\attachment\pic_personal\', 'http://www.localhost.com') "; 
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			} // end if

			$cmd = " SELECT OT_NAME FROM PER_ORG_TYPE WHERE OT_CODE = '11' ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if ($count_data) {
				$cmd = " INSERT INTO PER_ORG_TYPE (OT_CODE, OT_NAME, OT_ACTIVE, UPDATE_USER, UPDATE_DATE, OT_SEQ_NO)
								  VALUES ('01', 'ส่วนกลาง', 1, $SESS_USERID, '$UPDATE_DATE', 1) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_ORG_TYPE (OT_CODE, OT_NAME, OT_ACTIVE, UPDATE_USER, UPDATE_DATE, OT_SEQ_NO)
								  VALUES ('02', 'ส่วนกลางในภูมิภาค', 1, $SESS_USERID, '$UPDATE_DATE', 2) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_ORG_TYPE (OT_CODE, OT_NAME, OT_ACTIVE, UPDATE_USER, UPDATE_DATE, OT_SEQ_NO)
								  VALUES ('03', 'ส่วนภูมิภาค', 1, $SESS_USERID, '$UPDATE_DATE', 3) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_ORG_TYPE (OT_CODE, OT_NAME, OT_ACTIVE, UPDATE_USER, UPDATE_DATE, OT_SEQ_NO)
								  VALUES ('04', 'ต่างประเทศ', 1, $SESS_USERID, '$UPDATE_DATE', 4) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_ORG SET OT_CODE = '01' WHERE OT_CODE = '10' OR OT_CODE = '11' ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_ORG_ASS SET OT_CODE = '01' WHERE OT_CODE = '10' OR OT_CODE = '11' ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_SUM_DTL1 SET OT_CODE = '01' WHERE OT_CODE = '10' OR OT_CODE = '11' ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_SUM_DTL6 SET OT_CODE = '01' WHERE OT_CODE = '10' OR OT_CODE = '11' ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_ORG SET OT_CODE = '02' WHERE OT_CODE = '12' ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_ORG_ASS SET OT_CODE = '02' WHERE OT_CODE = '12' ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_SUM_DTL1 SET OT_CODE = '02' WHERE OT_CODE = '12' ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_SUM_DTL6 SET OT_CODE = '02' WHERE OT_CODE = '12' ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_ORG SET OT_CODE = '03' WHERE OT_CODE = '20' OR OT_CODE = '21' OR OT_CODE = '22' ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_ORG_ASS SET OT_CODE = '03' WHERE OT_CODE = '20' OR OT_CODE = '21' OR OT_CODE = '22' ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_SUM_DTL1 SET OT_CODE = '03' WHERE OT_CODE = '20' OR OT_CODE = '21' OR OT_CODE = '22' ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_SUM_DTL6 SET OT_CODE = '03' WHERE OT_CODE = '20' OR OT_CODE = '21' OR OT_CODE = '22' ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_ORG SET OT_CODE = '04' WHERE OT_CODE = '13' ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_ORG_ASS SET OT_CODE = '04' WHERE OT_CODE = '13' ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_SUM_DTL1 SET OT_CODE = '04' WHERE OT_CODE = '13' ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_SUM_DTL6 SET OT_CODE = '04' WHERE OT_CODE = '13' ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_ORG_TYPE SET OT_ACTIVE = 0 WHERE OT_CODE != '01' AND OT_CODE != '02' AND OT_CODE != '03' AND OT_CODE != '04' ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			} // end if

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO, COM_ACTIVE)
							VALUES ('5118', 'คส. 11.8', 'คำสั่งให้ออกจากราชการ', '511', 69, 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " SELECT PER_TYPE FROM PER_POSITION_COMPETENCE ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) {
				if($DPISDB=="odbc"){
					$cmd = " ALTER TABLE PER_POSITION_COMPETENCE ADD PER_TYPE SINGLE NULL ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();

					$cmd = " UPDATE PER_POSITION_COMPETENCE SET PER_TYPE = 1 WHERE PER_TYPE IS NULL ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();

					$cmd = " ALTER TABLE PER_POSITION_COMPETENCE ALTER PER_TYPE SINGLE NOT NULL ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();

					$cmd = " ALTER TABLE PER_POSITION_COMPETENCE DROP CONSTRAINT PK_PER_POSITION_COMPETENCE ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();

					$cmd = " ALTER TABLE PER_POSITION_COMPETENCE ADD CONSTRAINT PK_PER_POSITION_COMPETENCE 
									  PRIMARY KEY (PER_TYPE, POS_ID, CP_CODE) ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();

				}elseif($DPISDB=="oci8"){
					$cmd = " ALTER TABLE PER_POSITION_COMPETENCE ADD PER_TYPE NUMBER(1) NULL ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();

					$cmd = " UPDATE PER_POSITION_COMPETENCE SET PER_TYPE = 1 WHERE PER_TYPE IS NULL ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();

					$cmd = " ALTER TABLE PER_POSITION_COMPETENCE MODIFY PER_TYPE NOT NULL ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();

					$cmd = " ALTER TABLE PER_POSITION_COMPETENCE DROP CONSTRAINT PK_PER_POSITION_COMPETENCE ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();

					$cmd = " DROP INDEX PK_PER_POSITION_COMPETENCE ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();

					$cmd = " ALTER TABLE PER_POSITION_COMPETENCE ADD CONSTRAINT PK_PER_POSITION_COMPETENCE PRIMARY KEY 
									  (PER_TYPE, POS_ID, CP_CODE) ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				}
			}

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_POSITION, PER_PERSONAL SET PER_POSITION.LEVEL_NO = 
								  PER_PERSONAL.LEVEL_NO WHERE PER_PERSONAL.POS_ID = PER_POSITION.POS_ID AND 
								  (PER_POSITION.LEVEL_NO IS NULL OR PER_POSITION.LEVEL_NO = '0') AND PER_PERSONAL.PER_STATUS = 1 ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_POSITION A SET A.LEVEL_NO = 
								  (SELECT MAX(B.LEVEL_NO) FROM PER_PERSONAL B WHERE A.POS_ID = B.POS_ID AND B.PER_TYPE = 1 AND B.PER_STATUS = 1) 
								  WHERE A.LEVEL_NO IS NULL OR A.LEVEL_NO = '0' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_POSITION, PER_CO_LEVEL SET PER_POSITION.LEVEL_NO = 
								  PER_CO_LEVEL.LEVEL_NO_MAX WHERE TRIM(PER_CO_LEVEL.CL_NAME) = TRIM(PER_POSITION.CL_NAME) AND 
								  (PER_POSITION.LEVEL_NO IS NULL OR PER_POSITION.LEVEL_NO = '0') ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_POSITION A SET A.LEVEL_NO = 
								  (SELECT B.LEVEL_NO_MAX FROM PER_CO_LEVEL B WHERE TRIM(A.CL_NAME) = TRIM(B.CL_NAME)) 
								  WHERE A.LEVEL_NO IS NULL OR A.LEVEL_NO = '0' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE EDITOR_ATTACHMENT ALTER FILE_TYPE MEMO ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE EDITOR_ATTACHMENT MODIFY FILE_TYPE VARCHAR2(2000) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE EDITOR_ATTACHMENT MODIFY FILE_TYPE TEXT ";
			$db->send_cmd($cmd);
			//$db->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE EDITOR_IMAGE ALTER FILE_TYPE MEMO ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE EDITOR_IMAGE MODIFY FILE_TYPE VARCHAR2(2000) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE EDITOR_IMAGE MODIFY FILE_TYPE TEXT ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_extratype.html' 
							  WHERE LINKTO_WEB = 'master_table.html?table=PER_EXTRATYPE' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'personal_master.html' WHERE LINKTO_WEB = 'personal_master_tab.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " ALTER TABLE ACCOUNTABILITY_TYPE ADD CONSTRAINT PK_ACCOUNTABILITY_TYPE PRIMARY KEY (ACC_TYPE_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_PERSONAL", "PER_UNION2","SINGLE", "1", "NULL");
			add_field("PER_PERSONAL", "PER_UNIONDATE2","VARCHAR", "19", "NULL");
			add_field("PER_PERSONAL", "PER_UNION3","SINGLE", "1", "NULL");
			add_field("PER_PERSONAL", "PER_UNIONDATE3","VARCHAR", "19", "NULL");
			add_field("PER_PERSONAL", "PER_UNION4","SINGLE", "1", "NULL");
			add_field("PER_PERSONAL", "PER_UNIONDATE4","VARCHAR", "19", "NULL");
			add_field("PER_PERSONAL", "PER_UNION5","SINGLE", "1", "NULL");
			add_field("PER_PERSONAL", "PER_UNIONDATE5","VARCHAR", "19", "NULL");

			$cmd = " SELECT PL_CODE FROM PER_LINE_COMPETENCE ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) {
				if($DPISDB=="odbc") 
					$cmd = " CREATE TABLE PER_LINE_COMPETENCE(
					PL_CODE VARCHAR(10) NOT NULL,	
					ORG_ID INTEGER NOT NULL,	
					CP_CODE VARCHAR(3) NOT NULL,	
					LC_ACTIVE SINGLE NOT NULL,
					DEPARTMENT_ID INTEGER NULL,
					UPDATE_USER INTEGER NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_LINE_COMPETENCE PRIMARY KEY (PL_CODE, ORG_ID, CP_CODE)) ";
				elseif($DPISDB=="oci8") 
					$cmd = " CREATE TABLE PER_LINE_COMPETENCE(
					PL_CODE VARCHAR2(10) NOT NULL,	
					ORG_ID NUMBER(10) NOT NULL,	
					CP_CODE VARCHAR2(3) NOT NULL,	
					LC_ACTIVE NUMBER(1) NOT NULL,
					DEPARTMENT_ID NUMBER(10) NULL,
					UPDATE_USER NUMBER(11) NOT NULL,
					UPDATE_DATE VARCHAR2(19) NOT NULL,		
					CONSTRAINT PK_PER_LINE_COMPETENCE PRIMARY KEY (PL_CODE, ORG_ID, CP_CODE)) ";
				elseif($DPISDB=="mysql")
					$cmd = " CREATE TABLE PER_LINE_COMPETENCE(
					PL_CODE VARCHAR(10) NOT NULL,	
					ORG_ID INTEGER(10) NOT NULL,	
					CP_CODE VARCHAR(3) NOT NULL,	
					LC_ACTIVE SMALLINT(1) NOT NULL,
					DEPARTMENT_ID INTEGER(10) NULL,
					UPDATE_USER INTEGER(11) NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_LINE_COMPETENCE PRIMARY KEY (PL_CODE, ORG_ID, CP_CODE)) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " SELECT DISTINCT PL_CODE, ORG_ID, DEPARTMENT_ID FROM PER_POSITION WHERE DEPARTMENT_ID > 0 AND POS_STATUS = 1 ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
				while($data = $db_dpis->get_array()){
					$PL_CODE = trim($data[PL_CODE]);
					$TMP_ORG_ID = $data[ORG_ID];
					$TMP_DEPARTMENT_ID = $data[DEPARTMENT_ID];

					$cmd = " SELECT CP_CODE FROM PER_COMPETENCE WHERE CP_MODEL = 1 AND DEPARTMENT_ID = $TMP_DEPARTMENT_ID ";
					$db_dpis2->send_cmd($cmd);
					//$db_dpis2->show_error();
					while($data2 = $db_dpis2->get_array()){
						$CP_CODE = trim($data2[CP_CODE]);
						$cmd = " INSERT INTO PER_LINE_COMPETENCE (PL_CODE, ORG_ID, CP_CODE, LC_ACTIVE, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID) 
										VALUES('$PL_CODE', $TMP_ORG_ID, '$CP_CODE', 1, $SESS_USERID, '$UPDATE_DATE', $TMP_DEPARTMENT_ID) ";
						$db_dpis1->send_cmd($cmd);
						//$db_dpis1->show_error();
					} // end while						
				} // end while						

				$cmd = " SELECT DISTINCT PL_CODE, a.ORG_ID, a.DEPARTMENT_ID FROM PER_POSITION a, PER_PERSONAL b 
								WHERE a.POS_ID = b.POS_ID AND b.LEVEL_NO IN ('D1', 'D2', 'M1', 'M2') AND PER_STATUS = 1 ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
				while($data = $db_dpis->get_array()){
					$PL_CODE = trim($data[PL_CODE]);
					$TMP_ORG_ID = $data[ORG_ID];
					$TMP_DEPARTMENT_ID = $data[DEPARTMENT_ID];

					$cmd = " SELECT CP_CODE FROM PER_COMPETENCE WHERE CP_MODEL = 2 AND DEPARTMENT_ID = $TMP_DEPARTMENT_ID ";
					$db_dpis2->send_cmd($cmd);
					//$db_dpis2->show_error();
					while($data2 = $db_dpis2->get_array()){
						$CP_CODE = trim($data2[CP_CODE]);
						$cmd = " INSERT INTO PER_LINE_COMPETENCE (PL_CODE, ORG_ID, CP_CODE, LC_ACTIVE, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID) 
										VALUES('$PL_CODE', $TMP_ORG_ID, '$CP_CODE', 1, $SESS_USERID, '$UPDATE_DATE', $TMP_DEPARTMENT_ID) ";
						$db_dpis1->send_cmd($cmd);
						//$db_dpis1->show_error();
					} // end while						
				} // end while						
			} // end if

			$cmd = " SELECT ORG_ID, ORG_NAME, ORG_ID_REF, OL_CODE FROM PER_ORG WHERE DEPARTMENT_ID IS NULL AND OL_CODE > '02' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			while($data = $db_dpis->get_array()){
				$ORG_ID = $data[ORG_ID];
				$ORG_NAME = $data[ORG_NAME];
				$ORG_ID_REF = $data[ORG_ID_REF];
				$OL_CODE1 = trim($data[OL_CODE]);
				$cmd = " SELECT ORG_NAME, ORG_ID_REF, OL_CODE FROM PER_ORG WHERE ORG_ID = $ORG_ID_REF ";
				$db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$ORG_NAME_REF = $data1[ORG_NAME];
				$OL_CODE = trim($data1[OL_CODE]);
				if ($OL_CODE=='02') $DEPT_ID = $ORG_ID_REF;
				else {
					$ORG_ID_REF = $data1[ORG_ID_REF];
					$cmd = " SELECT ORG_ID_REF, OL_CODE FROM PER_ORG WHERE ORG_ID = $ORG_ID_REF ";
					$db_dpis1->send_cmd($cmd);
					$data1 = $db_dpis1->get_array();
					$OL_CODE = trim($data1[OL_CODE]);
					if ($OL_CODE=='02') $DEPT_ID = $ORG_ID_REF;
					else {
						$ORG_ID_REF = $data1[ORG_ID_REF];
						$cmd = " SELECT ORG_ID_REF, OL_CODE FROM PER_ORG WHERE ORG_ID = $ORG_ID_REF ";
						$db_dpis1->send_cmd($cmd);
						$data1 = $db_dpis1->get_array();
						$OL_CODE = trim($data1[OL_CODE]);
						if ($OL_CODE=='02') $DEPT_ID = $ORG_ID_REF;
						else {
							$ORG_ID_REF = $data1[ORG_ID_REF];
							$cmd = " SELECT ORG_ID_REF, OL_CODE FROM PER_ORG WHERE ORG_ID = $ORG_ID_REF ";
							$db_dpis1->send_cmd($cmd);
							$data1 = $db_dpis1->get_array();
							$OL_CODE = trim($data1[OL_CODE]);
							if ($OL_CODE=='02') $DEPT_ID = $ORG_ID_REF;
							else {
								$ORG_ID_REF = $data1[ORG_ID_REF];
								$cmd = " SELECT ORG_ID_REF, OL_CODE FROM PER_ORG WHERE ORG_ID = $ORG_ID_REF ";
								$db_dpis1->send_cmd($cmd);
								$data1 = $db_dpis1->get_array();
								$OL_CODE = trim($data1[OL_CODE]);
								if ($OL_CODE=='02') $DEPT_ID = $ORG_ID_REF;
								elseif ($OL_CODE1 != "01" && $OL_CODE1 != "02" && $ORG_ID != 1) 
									echo "PER_ORG $ORG_ID $ORG_NAME $OL_CODE1 $ORG_ID_REF $ORG_NAME_REF<br>";
							}
						}
					}
				}
				if ($OL_CODE1 != "01" && $OL_CODE1 != "02") {
					$cmd = " UPDATE PER_ORG SET DEPARTMENT_ID = $DEPT_ID WHERE ORG_ID = $ORG_ID ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
				}
			} // end while						

			$cmd = " ALTER SYSTEM SET DEFERRED_SEGMENT_CREATION=FALSE ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 
							  WHERE LINKTO_WEB = 'personal_master_structure.html' ";
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
								  VALUES (1, 'TH', $MAX_ID, 25, 'P0125 ปรับปรุงโครงสร้างตามมอบหมายงาน', 'S', 'W', 'personal_master_structure.html', 0, 35, 241, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 25, 'P0125 ปรับปรุงโครงสร้างตามมอบหมายงาน', 'S', 'W', 'personal_master_structure.html', 0, 35, 241, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'personal_master.html?SEARCHHIS=personal_extra_incomehis' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'personal_master.html?SEARCHHIS=personal_absentsum' ";
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
								  VALUES (1, 'TH', $MAX_ID, 18, 'P0118 สรุปวันลาสะสม', 'S', 'W', 'personal_master.html?SEARCHHIS=personal_absentsum', 0, 35, 241, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 18, 'P0118 สรุปวันลาสะสม', 'S', 'W', 'personal_master.html?SEARCHHIS=personal_absentsum', 0, 35, 241, 
								  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'data_absent_approve_person.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_POSITION, PER_ORG SET PER_POSITION.DEPARTMENT_ID = 
								  PER_ORG.ORG_ID_REF WHERE PER_ORG.ORG_ID = PER_POSITION.ORG_ID ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_POSITION A SET A.DEPARTMENT_ID = (SELECT B.ORG_ID_REF FROM PER_ORG B 
								  WHERE A.ORG_ID = B.ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " SELECT PER_ID, a.DEPARTMENT_ID FROM PER_POSITION a, PER_PERSONAL b 
							  WHERE a.POS_ID = b.POS_ID AND PER_TYPE = 1 AND a.DEPARTMENT_ID <> b.DEPARTMENT_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			while($data = $db_dpis->get_array()){
				$PER_ID = $data[PER_ID];
				$DEPT_ID = $data[DEPARTMENT_ID];

				$cmd = " UPDATE PER_PERSONAL SET DEPARTMENT_ID = $DEPT_ID WHERE PER_ID = $PER_ID ";
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
			} // end while						

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_POS_EMP, PER_ORG SET PER_POS_EMP.DEPARTMENT_ID = 
								  PER_ORG.ORG_ID_REF WHERE PER_ORG.ORG_ID = PER_POS_EMP.ORG_ID ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_POS_EMP A SET A.DEPARTMENT_ID = (SELECT B.ORG_ID_REF FROM PER_ORG B 
								  WHERE A.ORG_ID = B.ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " SELECT PER_ID, a.DEPARTMENT_ID FROM PER_POS_EMP a, PER_PERSONAL b 
							  WHERE a.POEM_ID = b.POEM_ID AND PER_TYPE = 2 AND a.DEPARTMENT_ID <> b.DEPARTMENT_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			while($data = $db_dpis->get_array()){
				$PER_ID = $data[PER_ID];
				$DEPT_ID = $data[DEPARTMENT_ID];

				$cmd = " UPDATE PER_PERSONAL SET DEPARTMENT_ID = $DEPT_ID WHERE PER_ID = $PER_ID ";
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
			} // end while						

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_POS_EMPSER, PER_ORG SET PER_POS_EMPSER.DEPARTMENT_ID = 
								  PER_ORG.ORG_ID_REF WHERE PER_ORG.ORG_ID = PER_POS_EMPSER.ORG_ID ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_POS_EMPSER A SET A.DEPARTMENT_ID = (SELECT B.ORG_ID_REF FROM PER_ORG B 
								  WHERE A.ORG_ID = B.ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " SELECT PER_ID, a.DEPARTMENT_ID FROM PER_POS_EMPSER a, PER_PERSONAL b 
							  WHERE a.POEMS_ID = b.POEMS_ID AND PER_TYPE = 3 AND a.DEPARTMENT_ID <> b.DEPARTMENT_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			while($data = $db_dpis->get_array()){
				$PER_ID = $data[PER_ID];
				$DEPT_ID = $data[DEPARTMENT_ID];

				$cmd = " UPDATE PER_PERSONAL SET DEPARTMENT_ID = $DEPT_ID WHERE PER_ID = $PER_ID ";
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
			} // end while						

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_POS_TEMP, PER_ORG SET PER_POS_TEMP.DEPARTMENT_ID = 
								  PER_ORG.ORG_ID_REF WHERE PER_ORG.ORG_ID = PER_POS_TEMP.ORG_ID ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_POS_TEMP A SET A.DEPARTMENT_ID = (SELECT B.ORG_ID_REF FROM PER_ORG B 
								  WHERE A.ORG_ID = B.ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " SELECT PER_ID, a.DEPARTMENT_ID FROM PER_POS_TEMP a, PER_PERSONAL b 
							  WHERE a.POT_ID = b.POT_ID AND PER_TYPE = 4 AND a.DEPARTMENT_ID <> b.DEPARTMENT_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			while($data = $db_dpis->get_array()){
				$PER_ID = $data[PER_ID];
				$DEPT_ID = $data[DEPARTMENT_ID];

				$cmd = " UPDATE PER_PERSONAL SET DEPARTMENT_ID = $DEPT_ID WHERE PER_ID = $PER_ID ";
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
			} // end while						

			$cmd = " ALTER TABLE PER_SERVICEHIS DROP CONSTRAINT FK5_PER_SERVICEHIS ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_KPI", "KPI_DEFINE","MEMO", "2000", "NULL");
			add_field("PER_KPI", "KPI_TYPE","CHAR", "1", "NULL");
			add_field("PER_KPI_FORM", "LEVEL_NO","VARCHAR", "10", "NULL");
			add_field("PER_PERFORMANCE_GOALS", "KF_TYPE","CHAR", "1", "NULL");

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_PERSONAL ALTER PER_CERT_OCC VARCHAR(100) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PERSONAL MODIFY PER_CERT_OCC VARCHAR2(100) ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_PERSONAL MODIFY PER_CERT_OCC VARCHAR(100) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if ($BKK_FLAG==1 || $SESS_DEPARTMENT_NAME=="สำนักงาน ก.พ.") {
				$cmd = " SELECT PJ_ID FROM PER_PROJECT ";
				$count_data = $db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
				if (!$count_data) {
					if($DPISDB=="odbc") 
						$cmd = " CREATE TABLE PER_PROJECT(
						PJ_ID INTEGER NOT NULL,	
						PJ_NAME MEMO NOT NULL,
						PJ_YEAR VARCHAR(4) NOT NULL,
						KPI_ID INTEGER NOT NULL,	
						PFR_ID INTEGER NOT NULL,	
						PJ_TYPE VARCHAR(10) NULL,
						PJ_CLASS VARCHAR(10) NULL,
						PJ_STATUS VARCHAR(10) NULL,
						PJ_EVALUATION NUMBER NULL,
						PJ_REPORT_STATUS VARCHAR(10) NULL,
						PJ_TARGET_STATUS VARCHAR(10) NULL,
						DEPARTMENT_ID INTEGER NULL,
						ORG_ID INTEGER NULL,
						START_DATE VARCHAR(19) NOT NULL,		
						END_DATE VARCHAR(19) NOT NULL,		
						PJ_OBJECTIVE  VARCHAR(255) NULL,
						PJ_TARGET VARCHAR(255) NULL,
						PJ_BUDGET_RECEIVE NUMBER NULL,
						PJ_BUDGET_USED NUMBER NULL,
						PJ_ID_REF INTEGER NULL,	
						UPDATE_USER INTEGER2 NOT NULL,
						UPDATE_DATE VARCHAR(19) NOT NULL,		
						CONSTRAINT PK_PER_PROJECT PRIMARY KEY (PJ_ID)) ";
					elseif($DPISDB=="oci8") 
						$cmd = " CREATE TABLE PER_PROJECT(
						PJ_ID NUMBER(10) NOT NULL,	
						PJ_NAME VARCHAR2(2000) NOT NULL,
						PJ_YEAR VARCHAR2(4) NOT NULL,
						KPI_ID NUMBER(10) NOT NULL,	
						PFR_ID NUMBER(10) NOT NULL,	
						PJ_TYPE VARCHAR2(10) NULL,
						PJ_CLASS VARCHAR2(10) NULL,
						PJ_STATUS VARCHAR2(10) NULL,
						PJ_EVALUATION NUMBER(6,2) NULL,
						PJ_REPORT_STATUS VARCHAR2(10) NULL,
						PJ_TARGET_STATUS VARCHAR2(10) NULL,
						DEPARTMENT_ID NUMBER(10) NULL,
						ORG_ID NUMBER(10) NULL,
						START_DATE VARCHAR2(19) NOT NULL,		
						END_DATE VARCHAR2(19) NOT NULL,		
						PJ_OBJECTIVE  VARCHAR2(255) NULL,
						PJ_TARGET VARCHAR2(255) NULL,
						PJ_BUDGET_RECEIVE NUMBER(16,2) NULL,
						PJ_BUDGET_USED NUMBER(16,2) NULL,
						PJ_ID_REF NUMBER(10) NULL,	
						UPDATE_USER NUMBER(5) NOT NULL,
						UPDATE_DATE VARCHAR2(19) NOT NULL,		
						CONSTRAINT PK_PER_PROJECT PRIMARY KEY (PJ_ID)) ";
					elseif($DPISDB=="mysql")
						$cmd = " CREATE TABLE PER_PROJECT(
						PJ_ID INTEGER(10) NOT NULL,	
						PJ_NAME TEXT NOT NULL,
						PJ_YEAR VARCHAR(4) NOT NULL,
						KPI_ID INTEGER(10) NOT NULL,	
						PFR_ID INTEGER(10) NOT NULL,	
						PJ_TYPE VARCHAR(10) NULL,
						PJ_CLASS VARCHAR(10) NULL,
						PJ_STATUS VARCHAR(10) NULL,
						PJ_EVALUATION DECEMAL(6,2) NULL,
						PJ_REPORT_STATUS VARCHAR(10) NULL,
						PJ_TARGET_STATUS VARCHAR(10) NULL,
						DEPARTMENT_ID INTEGER(10) NULL,
						ORG_ID INTEGER(10) NULL,
						START_DATE VARCHAR(19) NOT NULL,		
						END_DATE VARCHAR(19) NOT NULL,		
						PJ_OBJECTIVE  VARCHAR(255) NULL,
						PJ_TARGET VARCHAR(255) NULL,
						PJ_BUDGET_RECEIVE DECEMAL(6,2) NULL,
						PJ_BUDGET_USED DECEMAL(6,2) NULL,
						PJ_ID_REF INTEGER(10) NULL,	
						UPDATE_USER SMALLINT(5) NOT NULL,
						UPDATE_DATE VARCHAR(19) NOT NULL,		
						CONSTRAINT PK_PER_PROJECT PRIMARY KEY (PJ_ID)) ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();

					$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'kpi_project.html' ";
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
										  VALUES (1, 'TH', $MAX_ID, 3, 'K03 โครงการ', 'S', 'W', 'kpi_project.html', 0, 40, 
										  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
						$db->send_cmd($cmd);
						//$db->show_error();

						$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
										  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
										  UPDATE_DATE, UPDATE_BY)
										  VALUES (1, 'EN', $MAX_ID, 3, 'K03 โครงการ', 'S', 'W', 'kpi_project.html', 0, 40, 
										  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
						$db->send_cmd($cmd);
						//$db->show_error();
					} 
				}

				if ($BKK_FLAG==1) {
					$cmd = " SELECT KPI_SCORE FROM PER_KPI ";
					$count_data = $db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
					if (!$count_data) {
						$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'kpi_kpi_score.html' ";
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
											  VALUES (1, 'TH', $MAX_ID, 4, 'K04 คะแนนการประเมินผลระดับหน่วยงาน', 'S', 'W', 'kpi_kpi_score.html', 0, 40, 
											  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
							$db->send_cmd($cmd);
							//$db->show_error();

							$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
											  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
											  UPDATE_DATE, UPDATE_BY)
											  VALUES (1, 'EN', $MAX_ID, 4, 'K04 คะแนนการประเมินผลระดับหน่วยงาน', 'S', 'W', 'kpi_kpi_score.html', 0, 40, 
											  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
							$db->send_cmd($cmd);
							//$db->show_error();
						} 
					}
				}

				$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'kpi_project_inquire.html' ";
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
									  VALUES (1, 'TH', $MAX_ID, 5, 'K05 ค้นหาโครงการ', 'S', 'W', 'kpi_project_inquire.html', 0, 40, 
									  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();

					$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
									  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
									  UPDATE_DATE, UPDATE_BY)
									  VALUES (1, 'EN', $MAX_ID, 5, 'K05 ค้นหาโครงการ', 'S', 'W', 'kpi_project_inquire.html', 0, 40, 
									  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();
				} 

				$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'kpi_project_status.html' ";
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
									  VALUES (1, 'TH', $MAX_ID, 6, 'K06 สถานะโครงการ', 'S', 'W', 'kpi_project_status.html', 0, 40, 
									  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();

					$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
									  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
									  UPDATE_DATE, UPDATE_BY)
									  VALUES (1, 'EN', $MAX_ID, 6, 'K06 สถานะโครงการ', 'S', 'W', 'kpi_project_status.html', 0, 40, 
									  $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();
				}  
			}

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_PENALTY ALTER PEN_CODE CHAR(10) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PENALTY MODIFY PEN_CODE CHAR(10) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_PENALTY MODIFY PEN_CODE CHAR(10) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_PENALTY ALTER PEN_NAME VARCHAR(255) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PENALTY MODIFY PEN_NAME VARCHAR2(255) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_PENALTY MODIFY PEN_NAME VARCHAR(255) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_PUNISHMENT ALTER PEN_CODE CHAR(10) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PUNISHMENT MODIFY PEN_CODE CHAR(10) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_PUNISHMENT MODIFY PEN_CODE CHAR(10) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_INVEST2DTL ALTER PEN_CODE CHAR(10) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_INVEST2DTL MODIFY PEN_CODE CHAR(10) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_INVEST2DTL MODIFY PEN_CODE CHAR(10) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_PERFORMANCE_REVIEW ALTER PFR_NAME MEMO ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PERFORMANCE_REVIEW MODIFY PFR_NAME VARCHAR2(2000) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_PERFORMANCE_REVIEW MODIFY PFR_NAME TEXT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_COMPETENCE ALTER CP_ENG_NAME NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COMPETENCE MODIFY CP_ENG_NAME NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_COMPETENCE MODIFY CP_ENG_NAME NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " SELECT DT_CODE FROM PER_DISTRICT ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) {
				if($DPISDB=="odbc") 
					$cmd = " CREATE TABLE PER_DISTRICT(
					DT_CODE VARCHAR(10) NOT NULL,	
					DT_NAME VARCHAR(100) NOT NULL,	
					PV_CODE VARCHAR(10) NOT NULL,	
					AP_CODE VARCHAR(10) NOT NULL,	
					ZIP_CODE VARCHAR(10) NULL,	
					DT_SEQ_NO INTEGER2 NULL,
					DT_ACTIVE SINGLE NOT NULL,
					UPDATE_USER INTEGER NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_DISTRICT PRIMARY KEY (DT_CODE)) ";
				elseif($DPISDB=="oci8") 
					$cmd = " CREATE TABLE PER_DISTRICT(
					DT_CODE VARCHAR2(10) NOT NULL,	
					DT_NAME VARCHAR2(100) NOT NULL,	
					PV_CODE VARCHAR2(10) NOT NULL,	
					AP_CODE VARCHAR2(10) NOT NULL,	
					ZIP_CODE VARCHAR2(10) NULL,	
					DT_SEQ_NO NUMBER(5) NULL,
					DT_ACTIVE NUMBER(1) NOT NULL,
					UPDATE_USER NUMBER(11) NOT NULL,
					UPDATE_DATE VARCHAR2(19) NOT NULL,		
					CONSTRAINT PK_PER_DISTRICT PRIMARY KEY (DT_CODE)) ";
				elseif($DPISDB=="mysql")
					$cmd = " CREATE TABLE PER_DISTRICT(
					DT_CODE VARCHAR(10) NOT NULL,	
					DT_NAME VARCHAR(100) NOT NULL,	
					PV_CODE VARCHAR(10) NOT NULL,	
					AP_CODE VARCHAR(10) NOT NULL,	
					ZIP_CODE VARCHAR(10) NULL,	
					DT_SEQ_NO SMALLINT(5) NULL,
					DT_ACTIVE SMALLINT(1) NOT NULL,
					UPDATE_USER INTEGER(11) NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_DISTRICT PRIMARY KEY (DT_CODE)) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			}

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_DISTRICT ALTER ZIP_CODE NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_DISTRICT MODIFY ZIP_CODE NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_DISTRICT MODIFY ZIP_CODE NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if ($BKK_FLAG==1) {
/*				$cmd = " SELECT COUNT(BR_CODE) AS COUNT_DATA FROM PER_BONUS_RULE ";
				$count_data = $db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
				if (!$count_data) {
					if($DPISDB=="odbc") 
						$cmd = " CREATE TABLE PER_BONUS_RULE(
						BR_YEAR VARCHAR(4) NOT NULL,	
						BR_CODE VARCHAR(10) NOT NULL,	
						BR_TYPE CHAR(1) NOT NULL,	
						BR_NAME VARCHAR(100) NOT NULL,
						BR_ORG_POINT_MIN NUMBER NOT NULL,
						BR_ORG_POINT_MAX NUMBER NOT NULL,
						BR_PER_POINT_MIN NUMBER NOT NULL,
						BR_PER_POINT_MAX NUMBER NOT NULL,
						BR_TIMES NUMBER NOT NULL,
						BR_ACTIVE SINGLE NOT NULL,
						UPDATE_USER INTEGER2 NOT NULL,
						UPDATE_DATE VARCHAR(19) NOT NULL,		
						CONSTRAINT PK_PER_BONUS_RULE PRIMARY KEY (BR_YEAR, BR_CODE)) ";
					elseif($DPISDB=="oci8") 
						$cmd = " CREATE TABLE PER_BONUS_RULE(
						BR_YEAR VARCHAR2(4) NOT NULL,	
						BR_CODE VARCHAR2(10) NOT NULL,	
						BR_TYPE CHAR(1) NOT NULL,	
						BR_NAME VARCHAR2(100) NOT NULL,
						BR_ORG_POINT_MIN NUMBER(5,2) NOT NULL,
						BR_ORG_POINT_MAX NUMBER(5,2) NOT NULL,
						BR_PER_POINT_MIN NUMBER(5,2) NOT NULL,
						BR_PER_POINT_MAX NUMBER(5,2) NOT NULL,
						BR_TIMES NUMBER(6,3) NOT NULL,
						BR_ACTIVE NUMBER(1) NOT NULL,
						UPDATE_USER NUMBER(5) NOT NULL,
						UPDATE_DATE VARCHAR2(19) NOT NULL,		
						CONSTRAINT PK_PER_BONUS_RULE PRIMARY KEY (BR_YEAR, BR_CODE)) ";
					elseif($DPISDB=="mysql") 
						$cmd = " CREATE TABLE PER_BONUS_RULE(
						BR_YEAR VARCHAR(4) NOT NULL,	
						BR_CODE VARCHAR(10) NOT NULL,	
						BR_TYPE CHAR(1) NOT NULL,	
						BR_NAME VARCHAR(100) NOT NULL,
						BR_ORG_POINT_MIN DECIMAL(5,2) NOT NULL,
						BR_ORG_POINT_MAX DECIMAL(5,2) NOT NULL,
						BR_PER_POINT_MIN DECIMAL(5,2) NOT NULL,
						BR_PER_POINT_MAX DECIMAL(5,2) NOT NULL,
						BR_TIMES DECIMAL(6,3) NOT NULL,
						BR_ACTIVE SMALLINT(1) NOT NULL,
						UPDATE_USER SMALLINT(5) NOT NULL,
						UPDATE_DATE VARCHAR(19) NOT NULL,		
						CONSTRAINT PK_PER_BONUS_RULE PRIMARY KEY (BR_YEAR, BR_CODE)) ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				}
			
				$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'master_table_bonus_rule.html?table=PER_BONUS_RULE' ";
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
									  VALUES (1, 'TH', $MAX_ID, 9, 'A09 หลักเกณฑ์การจัดสรรเงินรางวัลประจำปี', 'S', 'W', 'master_table_bonus_rule.html?table=PER_BONUS_RULE', 
									  0, 46, $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();

					$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
									  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
									  UPDATE_DATE, UPDATE_BY)
									  VALUES (1, 'EN', $MAX_ID, 9, 'A09 หลักเกณฑ์การจัดสรรเงินรางวัลประจำปี', 'S', 'W', 'master_table_bonus_rule.html?table=PER_BONUS_RULE', 
									  0, 46, $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();
				}

				$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'data_bonus_comdtl.html' ";
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
									  VALUES (1, 'TH', $MAX_ID, 10, 'A10 บัญชีแนบท้ายคำสั่งเงินรางวัลประจำปี', 'S', 'W', 'data_bonus_comdtl.html', 
									  0, 46, $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();

					$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
									  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
									  UPDATE_DATE, UPDATE_BY)
									  VALUES (1, 'EN', $MAX_ID, 10, 'A10 บัญชีแนบท้ายคำสั่งเงินรางวัลประจำปี', 'S', 'W', 'data_bonus_comdtl.html', 
									  0, 46, $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
					$db->send_cmd($cmd);
					//$db->show_error();
				} */
			}

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_TRAINING ALTER TRN_COURSE_NAME MEMO ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_TRAINING MODIFY TRN_COURSE_NAME VARCHAR2(2000) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_TRAINING MODIFY TRN_COURSE_NAME TEXT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_TRAINING ALTER TRN_REMARK MEMO ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_TRAINING MODIFY TRN_REMARK VARCHAR2(2000) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_TRAINING MODIFY TRN_REMARK TEXT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_TRAINING ALTER TRN_PLACE MEMO ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_TRAINING MODIFY TRN_PLACE VARCHAR2(2000) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_TRAINING MODIFY TRN_PLACE TEXT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_TRAINING", "TRN_OBJECTIVE","MEMO", "2000", "NULL");
			add_field("PER_POSITIONHIS", "POH_DOCNO_EDIT","VARCHAR", "255", "NULL");
			add_field("PER_POSITIONHIS", "POH_DOCDATE_EDIT","VARCHAR", "19", "NULL");
			add_field("PER_ABSENTSUM", "AB_CODE_14","NUMBER", "6,2", "NULL");
			add_field("PER_ABSENTSUM", "AB_CODE_15","NUMBER", "6,2", "NULL");

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_SERVICEHIS ALTER ORG_ID NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SERVICEHIS MODIFY ORG_ID NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_SERVICEHIS MODIFY ORG_ID NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_SERVICEHIS ALTER SRH_STARTDATE NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SERVICEHIS MODIFY SRH_STARTDATE NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_SERVICEHIS MODIFY SRH_STARTDATE NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_SERVICEHIS", "SRH_ORG","MEMO", "2000", "NULL");

			$cmd = " SELECT SRH_ID, b.ORG_NAME FROM PER_SERVICEHIS a, PER_ORG b WHERE a.ORG_ID=b.ORG_ID AND a.SRH_ORG IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			while($data = $db_dpis->get_array()){
				$SRH_ID = $data[SRH_ID];
				$SRH_ORG = trim($data[ORG_NAME]);

				$cmd = " UPDATE PER_SERVICEHIS SET SRH_ORG = '$SRH_ORG' WHERE SRH_ID = $SRH_ID ";
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
			} // end while

			add_field("PER_PUNISHMENT", "PUN_RECEIVE_NO","VARCHAR", "20", "NULL");
			add_field("PER_PUNISHMENT", "PUN_SEND_NO","VARCHAR", "20", "NULL");
			add_field("PER_PUNISHMENT", "PUN_NOTICE","MEMO", "2000", "NULL");
			add_field("PER_PUNISHMENT", "PUN_REPORTDATE","VARCHAR", "19", "NULL");
			add_field("PER_PUNISHMENT", "PUN_VIOLATEDATE","VARCHAR", "19", "NULL");
			add_field("PER_PUNISHMENT", "PUN_DOCDATE","VARCHAR", "19", "NULL");
			add_field("PER_IPIP", "DEVELOP_EVALUATE","VARCHAR", "255", "NULL");
			add_field("PER_PROJECT", "PJ_BUDGET_RECEIVE","NUMBER", "16,2", "NULL");
			add_field("PER_PROJECT", "PJ_BUDGET_USED","NUMBER", "16,2", "NULL");

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'rpt_R001.html?report=rpt_R001001' WHERE LINKTO_WEB = 'rpt_R001001.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'rpt_R001.html?report=rpt_R001002' WHERE LINKTO_WEB = 'rpt_R001002.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'rpt_R001.html?report=rpt_R001004' WHERE LINKTO_WEB = 'rpt_R001004.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'rpt_R001.html?report=rpt_R001005' WHERE LINKTO_WEB = 'rpt_R001005.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'rpt_R007.html?report=rpt_R007006' WHERE LINKTO_WEB = 'rpt_R007006.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'rpt_R007.html?report=rpt_R007007' WHERE LINKTO_WEB = 'rpt_R007007.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'rpt_R007.html?report=rpt_R007008' WHERE LINKTO_WEB = 'rpt_R007008.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'rpt_R008_1.html?report=rpt_R008001' WHERE LINKTO_WEB = 'rpt_R008001.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'rpt_R008_1.html?report=rpt_R008002' WHERE LINKTO_WEB = 'rpt_R008002.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'rpt_R008_2.html?report=rpt_R008003' WHERE LINKTO_WEB = 'rpt_R008003.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'rpt_R008_2.html?report=rpt_R008004' WHERE LINKTO_WEB = 'rpt_R008004.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'rpt_R008_2.html?report=rpt_R008006' WHERE LINKTO_WEB = 'rpt_R008006.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'rpt_R008_2.html?report=rpt_R008007' WHERE LINKTO_WEB = 'rpt_R008007.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV1 SET LINKTO_WEB = 'rpt_R010.html?report=rpt_R010006' WHERE LINKTO_WEB = 'rpt_R010006.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV1 SET LINKTO_WEB = 'rpt_R010.html?report=rpt_R010011' WHERE LINKTO_WEB = 'rpt_R010011.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV1 SET LINKTO_WEB = 'rpt_R010.html?report=rpt_R010014' WHERE LINKTO_WEB = 'rpt_R010014.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV1 SET LINKTO_WEB = 'rpt_R010.html?report=rpt_R010018' WHERE LINKTO_WEB = 'rpt_R010018.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV1 SET LINKTO_WEB = 'rpt_R010.html?report=rpt_R010019' WHERE LINKTO_WEB = 'rpt_R010019.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV1 SET LINKTO_WEB = 'rpt_R010_1.html?report=rpt_R010007' WHERE LINKTO_WEB = 'rpt_R010007.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV1 SET LINKTO_WEB = 'rpt_R010_1.html?report=rpt_R010009' WHERE LINKTO_WEB = 'rpt_R010009.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();

			add_field("PER_PERSONAL", "PER_SET_ASS","SINGLE", "1", "NULL");
			add_field("PER_KPI", "KPI_SCORE","NUMBER", "7,4", "NULL");
			add_field("PER_KPI", "KPI_SCORE_FLAG","SINGLE", "1", "NULL");

			$cmd = " UPDATE PER_KPI SET KPI_SCORE_FLAG = 1 WHERE KPI_SCORE_FLAG IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$count_data = add_field("PER_KPI", "ORG_ID","INTEGER", "10", "NULL");
			if (!$count_data) {
				if ($CTRL_TYPE == 2)	{
					$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_KPI' ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error() ;

					$cmd = " select max(KPI_ID) as max_id from PER_KPI ";
					$db_dpis->send_cmd($cmd);
					$data = $db_dpis->get_array();
					$data = array_change_key_case($data, CASE_LOWER);
					$KPI_ID = $data[max_id] + 1;
			
					$cmd = " SELECT DISTINCT DEPARTMENT_ID, ORG_ID FROM PER_POSITION WHERE DEPARTMENT_ID IN 
									(SELECT DISTINCT DEPARTMENT_ID FROM PER_KPI WHERE ORG_ID IS NULL) 
									ORDER BY DEPARTMENT_ID, ORG_ID ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
					while($data = $db_dpis->get_array()){
						$TMP_DEPARTMENT_ID = $data[DEPARTMENT_ID];
						$TMP_ORG_ID = $data[ORG_ID];
						$cmd = " SELECT KPI_ID, KPI_NAME, KPI_YEAR, KPI_WEIGHT, KPI_MEASURE, KPI_PER_ID, PFR_ID, 
											KPI_TARGET_LEVEL1, KPI_TARGET_LEVEL2, KPI_TARGET_LEVEL3, KPI_TARGET_LEVEL4, KPI_TARGET_LEVEL5, 
											KPI_ID_REF, KPI_EVALUATE, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID, 
											KPI_TARGET_LEVEL1_DESC, KPI_TARGET_LEVEL2_DESC, KPI_TARGET_LEVEL3_DESC, 
											KPI_TARGET_LEVEL4_DESC, KPI_TARGET_LEVEL5_DESC, ORG_NAME, UNDER_ORG_NAME1, KPI_RESULT, 
											KPI_EVALUATE1, KPI_EVALUATE2, KPI_EVALUATE3, KPI_DEFINE, KPI_TYPE, KPI_SCORE, KPI_SCORE_FLAG, ORG_ID 
											FROM PER_KPI 
											WHERE DEPARTMENT_ID = $TMP_DEPARTMENT_ID AND ORG_ID IS NULL
											ORDER BY KPI_YEAR, KPI_ID ";
						$db_dpis1->send_cmd($cmd);
						while($data1 = $db_dpis1->get_array()){
							$OLD_KPI_ID = $data1[KPI_ID];
							$KPI_NAME = trim($data1[KPI_NAME]);
							$KPI_YEAR = trim($data1[KPI_YEAR]);
							$KPI_WEIGHT = $data1[KPI_WEIGHT];
							$KPI_MEASURE = trim($data1[KPI_MEASURE]);
							$KPI_PER_ID = $data1[KPI_PER_ID];
							$PFR_ID = $data1[PFR_ID];
							$KPI_TARGET_LEVEL1 = $data1[KPI_TARGET_LEVEL1];
							$KPI_TARGET_LEVEL2 = $data1[KPI_TARGET_LEVEL2];
							$KPI_TARGET_LEVEL3 = $data1[KPI_TARGET_LEVEL3];
							$KPI_TARGET_LEVEL4 = $data1[KPI_TARGET_LEVEL4];
							$KPI_TARGET_LEVEL5 = $data1[KPI_TARGET_LEVEL5];
							$KPI_ID_REF = $data1[KPI_ID_REF];
							$KPI_EVALUATE = $data1[KPI_EVALUATE];
							$UPDATE_USER = $data1[UPDATE_USER];
							$UPDATE_DATE = trim($data1[UPDATE_DATE]);
							$DEPARTMENT_ID = $data1[DEPARTMENT_ID];
							$KPI_TARGET_LEVEL1_DESC = trim($data1[KPI_TARGET_LEVEL1_DESC]);
							$KPI_TARGET_LEVEL2_DESC = trim($data1[KPI_TARGET_LEVEL2_DESC]);
							$KPI_TARGET_LEVEL3_DESC = trim($data1[KPI_TARGET_LEVEL3_DESC]);
							$KPI_TARGET_LEVEL4_DESC = trim($data1[KPI_TARGET_LEVEL4_DESC]);
							$KPI_TARGET_LEVEL5_DESC = trim($data1[KPI_TARGET_LEVEL5_DESC]);
							$ORG_NAME = trim($data1[ORG_NAME]);
							$UNDER_ORG_NAME1 = trim($data1[UNDER_ORG_NAME1]);
							$KPI_RESULT = trim($data1[KPI_RESULT]);
							$KPI_EVALUATE1 = $data1[KPI_EVALUATE1];
							$KPI_EVALUATE2 = $data1[KPI_EVALUATE2];
							$KPI_EVALUATE3 = $data1[KPI_EVALUATE3];
							$KPI_DEFINE = trim($data1[KPI_DEFINE]);
							$KPI_TYPE = trim($data1[KPI_TYPE]);
							$KPI_SCORE = $data1[KPI_SCORE];
							$KPI_SCORE_FLAG = $data1[KPI_SCORE_FLAG];

							if(!$KPI_WEIGHT) $KPI_WEIGHT = "NULL";
							if(!$KPI_TARGET_LEVEL1) $KPI_TARGET_LEVEL1 = "NULL";
							if(!$KPI_TARGET_LEVEL2) $KPI_TARGET_LEVEL2 = "NULL";
							if(!$KPI_TARGET_LEVEL3) $KPI_TARGET_LEVEL3 = "NULL";
							if(!$KPI_TARGET_LEVEL4) $KPI_TARGET_LEVEL4 = "NULL";
							if(!$KPI_TARGET_LEVEL5) $KPI_TARGET_LEVEL5 = "NULL";
							if(!$KPI_EVALUATE1) $KPI_EVALUATE1 = "NULL";
							if(!$KPI_EVALUATE2) $KPI_EVALUATE2 = "NULL";
							if(!$KPI_EVALUATE3) $KPI_EVALUATE3 = "NULL";
							if(!$KPI_EVALUATE) $KPI_EVALUATE = "NULL";
							
							if (!get_magic_quotes_gpc()) {
								$KPI_TARGET_LEVEL1_DESC = addslashes(str_replace('"', "&quot;", trim($KPI_TARGET_LEVEL1_DESC)));
								$KPI_TARGET_LEVEL2_DESC = addslashes(str_replace('"', "&quot;", trim($KPI_TARGET_LEVEL2_DESC)));
								$KPI_TARGET_LEVEL3_DESC = addslashes(str_replace('"', "&quot;", trim($KPI_TARGET_LEVEL3_DESC)));
								$KPI_TARGET_LEVEL4_DESC = addslashes(str_replace('"', "&quot;", trim($KPI_TARGET_LEVEL4_DESC)));
								$KPI_TARGET_LEVEL5_DESC = addslashes(str_replace('"', "&quot;", trim($KPI_TARGET_LEVEL5_DESC)));
							}else{
								$KPI_TARGET_LEVEL1_DESC = addslashes(str_replace('"', "&quot;", stripslashes(trim($KPI_TARGET_LEVEL1_DESC))));
								$KPI_TARGET_LEVEL2_DESC = addslashes(str_replace('"', "&quot;", stripslashes(trim($KPI_TARGET_LEVEL2_DESC))));
								$KPI_TARGET_LEVEL3_DESC = addslashes(str_replace('"', "&quot;", stripslashes(trim($KPI_TARGET_LEVEL3_DESC))));
								$KPI_TARGET_LEVEL4_DESC = addslashes(str_replace('"', "&quot;", stripslashes(trim($KPI_TARGET_LEVEL4_DESC))));
								$KPI_TARGET_LEVEL5_DESC = addslashes(str_replace('"', "&quot;", stripslashes(trim($KPI_TARGET_LEVEL5_DESC))));
							} // end if
							if($KPI_ID_REF) {
								$cmd = " select NEW_CODE from PER_MAP_CODE 
												  where MAP_CODE = 'PER_KPI' AND OLD_CODE = '$KPI_ID_REF' AND UPDATE_USER = $TMP_ORG_ID ";
								$db_dpis2->send_cmd($cmd);
								$data2 = $db_dpis2->get_array();
								$KPI_ID_REF = $data2[NEW_CODE];
							}
							if(!$KPI_ID_REF) $KPI_ID_REF = "NULL";
							if(!$KPI_SCORE) $KPI_SCORE = "NULL";
							
							if ($TMP_ORG_ID) {
								$cmd = " INSERT INTO PER_KPI (KPI_ID, KPI_NAME, KPI_YEAR, KPI_WEIGHT, KPI_MEASURE, KPI_PER_ID, PFR_ID, 
												KPI_TARGET_LEVEL1, KPI_TARGET_LEVEL2, KPI_TARGET_LEVEL3, KPI_TARGET_LEVEL4, KPI_TARGET_LEVEL5, 
												KPI_ID_REF, KPI_EVALUATE, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID, 
												KPI_TARGET_LEVEL1_DESC, KPI_TARGET_LEVEL2_DESC, KPI_TARGET_LEVEL3_DESC, 
												KPI_TARGET_LEVEL4_DESC, KPI_TARGET_LEVEL5_DESC, ORG_NAME, UNDER_ORG_NAME1, KPI_RESULT, 
												KPI_EVALUATE1, KPI_EVALUATE2, KPI_EVALUATE3, KPI_DEFINE, KPI_TYPE, KPI_SCORE, KPI_SCORE_FLAG, ORG_ID)
												VALUES ($KPI_ID, '$KPI_NAME', '$KPI_YEAR', $KPI_WEIGHT, '$KPI_MEASURE', $KPI_PER_ID, $PFR_ID, 
												$KPI_TARGET_LEVEL1, $KPI_TARGET_LEVEL2, $KPI_TARGET_LEVEL3, $KPI_TARGET_LEVEL4, $KPI_TARGET_LEVEL5, 
												$KPI_ID_REF, $KPI_EVALUATE, $UPDATE_USER, '$UPDATE_DATE', $TMP_DEPARTMENT_ID, 
												'$KPI_TARGET_LEVEL1_DESC', '$KPI_TARGET_LEVEL2_DESC', '$KPI_TARGET_LEVEL3_DESC', 
												'$KPI_TARGET_LEVEL4_DESC', '$KPI_TARGET_LEVEL5_DESC', '$ORG_NAME', '$UNDER_ORG_NAME1', '$KPI_RESULT', 
												$KPI_EVALUATE1, $KPI_EVALUATE2, $KPI_EVALUATE3, '$KPI_DEFINE', '$KPI_TYPE', $KPI_SCORE, $KPI_SCORE_FLAG, $TMP_ORG_ID) ";
								$db_dpis2->send_cmd($cmd);
								//$db_dpis2->show_error();

								$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
												VALUES ('PER_KPI', '$OLD_KPI_ID', '$KPI_ID', $TMP_ORG_ID, '$UPDATE_DATE') ";
								$db_dpis2->send_cmd($cmd);
								//$db_dpis2->show_error();

								$KPI_ID++;
							}
						} // end while						
					} // end while						
				} // end if
			} // end if

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_SERVICEHIS ALTER SRH_DOCNO VARCHAR(100) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SERVICEHIS MODIFY SRH_DOCNO VARCHAR2(100) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_SERVICEHIS MODIFY SRH_DOCNO VARCHAR(100) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_R099001.html' ";
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
								  VALUES (1, 'TH', $MAX_ID, 4, 'R0904 รายงานตัวชี้วัดประจำปีงบประมาณ', 'S', 'W', 'rpt_R099001.html', 0, 36, 
								  303, $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 4, 'R0904 รายงานตัวชี้วัดประจำปีงบประมาณ', 'S', 'W', 'rpt_R099001.html', 0, 36, 
								  303, $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_R099002.html' ";
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
								  VALUES (1, 'TH', $MAX_ID, 5, 'R0905 รายงานตัวชิ้วัดรายบุคคล', 'S', 'W', 'rpt_R099002.html', 0, 36, 
								  303, $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 5, 'R0905 รายงานตัวชิ้วัดรายบุคคล', 'S', 'W', 'rpt_R099002.html', 0, 36, 
								  303, $CREATE_DATE, $UPDATE_BY, $CREATE_DATE, $UPDATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_R009099.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();

			if ($BKK_FLAG==1) {
/*				$cmd = " UPDATE PER_ORG_LEVEL SET OL_NAME = 'กรุงเทพมหานคร' WHERE OL_CODE = '01' AND OL_NAME = 'กระทรวง' ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_ORG_LEVEL SET OL_NAME = 'หน่วยงาน' WHERE OL_CODE = '02' AND OL_NAME = 'กรม' ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_ORG_LEVEL SET OL_NAME = 'ส่วนราชการ' WHERE OL_CODE = '03' AND OL_NAME = 'สำนัก/กอง' ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_ORG_LEVEL SET OL_NAME = 'ฝ่าย/กลุ่มงาน/กลุ่ม' WHERE OL_CODE = '04' AND OL_NAME = 'ต่ำกว่าสำนัก/กอง 1 ระดับ' ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_ORG_LEVEL SET OL_NAME = 'งาน' WHERE OL_CODE = '05' AND OL_NAME = 'ต่ำกว่าสำนัก/กอง 2 ระดับ' ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " DELETE FROM PER_ORG_LEVEL WHERE OL_CODE in ('06', '07', '08') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); */
			}

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_COMDTL ALTER CMD_POSITION VARCHAR(255) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COMDTL MODIFY CMD_POSITION VARCHAR2(255) ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_COMDTL MODIFY CMD_POSITION VARCHAR(255) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " SELECT AM_YEAR FROM PER_ASSESS_MAIN ";
			$count_data = $db_dpis->send_cmd($cmd);

			if (!$count_data) {
				$cmd = " ALTER TABLE PER_ASSESS_MAIN DROP CONSTRAINT PK_PER_ASSESS_MAIN ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " DROP INDEX PK_PER_ASSESS_MAIN ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				add_field("PER_ASSESS_MAIN", "AM_YEAR","VARCHAR", "4", "NULL");
				add_field("PER_ASSESS_MAIN", "AM_CYCLE","SINGLE", "1", "NULL");

				$cmd = " SELECT DISTINCT SUBSTR(CP_END_DATE,1,4) as CP_YEAR, CP_CYCLE, PER_TYPE FROM PER_COMPENSATION_TEST 
								WHERE SUBSTR(CP_END_DATE,1,4) != '2012' and CP_CYCLE != 2 ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
				while($data = $db_dpis->get_array()){
					$CP_YEAR = trim($data[CP_YEAR]) + 543;
					$CP_CYCLE = $data[CP_CYCLE];
					$PER_TYPE = $data[PER_TYPE];

					$cmd = " SELECT AM_CODE, AM_NAME, AM_POINT_MIN, AM_POINT_MAX, AM_ACTIVE, UPDATE_USER, UPDATE_DATE, AM_SHOW
									FROM PER_ASSESS_MAIN 
									WHERE PER_TYPE = $PER_TYPE AND AM_YEAR IS NULL AND AM_CYCLE IS NULL ";
					$db_dpis1->send_cmd($cmd);
					while($data_dpis1 = $db_dpis1->get_array()) {
						$AM_CODE = trim($data_dpis1[AM_CODE]);
						$AM_NAME = trim($data_dpis1[AM_NAME]);
						$AM_POINT_MIN = $data_dpis1[AM_POINT_MIN];
						$AM_POINT_MAX = $data_dpis1[AM_POINT_MAX];
						$AM_ACTIVE = $data_dpis1[AM_ACTIVE];
						$UPDATE_USER = $data_dpis1[UPDATE_USER];
						$UPDATE_DATE = trim($data_dpis1[UPDATE_DATE]);
						$AM_SHOW = $data_dpis1[AM_SHOW];
						if (!$AM_POINT_MIN) $AM_POINT_MIN = "NULL";
						if (!$AM_POINT_MAX) $AM_POINT_MAX = "NULL";
						if (!$AM_SHOW) $AM_SHOW = "NULL";

						$cmd = " insert into PER_ASSESS_MAIN (AM_YEAR, AM_CYCLE, AM_CODE, AM_NAME, AM_POINT_MIN, AM_POINT_MAX, 
										AM_ACTIVE, UPDATE_USER, UPDATE_DATE, AM_SHOW, PER_TYPE) 
										values ('$CP_YEAR', $CP_CYCLE, '$AM_CODE', '$AM_NAME', $AM_POINT_MIN, $AM_POINT_MAX, $AM_ACTIVE, 
										$UPDATE_USER, '$UPDATE_DATE', $AM_SHOW, $PER_TYPE) ";
						$db_dpis2->send_cmd($cmd);
						//$db_dpis2->show_error();
					} // end while						
				} // end while						

				$cmd = " update PER_ASSESS_MAIN set AM_YEAR = '2555', AM_CYCLE = 2 where AM_YEAR is NULL and AM_CYCLE is NULL ";
				$db_dpis2->send_cmd($cmd);
				//$db_dpis2->show_error();

				$cmd = " delete from PER_ASSESS_MAIN where AM_YEAR is NULL and AM_CYCLE is NULL ";
				$db_dpis2->send_cmd($cmd);
				//$db_dpis2->show_error();

				if($DPISDB=="oci8") {
					$cmd = " ALTER TABLE PER_ASSESS_MAIN MODIFY AM_YEAR NOT NULL ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();

					$cmd = " ALTER TABLE PER_ASSESS_MAIN MODIFY AM_CYCLE NOT NULL ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();

					$cmd = " ALTER TABLE PER_ASSESS_MAIN ADD CONSTRAINT PK_PER_ASSESS_MAIN PRIMARY KEY 
									  (AM_YEAR, AM_CYCLE, AM_CODE) ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				}

				$cmd = " ALTER TABLE PER_ASSESS_LEVEL DROP CONSTRAINT PK_PER_ASSESS_LEVEL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " DROP INDEX PK_PER_ASSESS_LEVEL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				add_field("PER_ASSESS_LEVEL", "AL_YEAR","VARCHAR", "4", "NULL");
				add_field("PER_ASSESS_LEVEL", "AL_CYCLE","SINGLE", "1", "NULL");

				$cmd = " SELECT DISTINCT SUBSTR(CP_END_DATE,1,4) as CP_YEAR, CP_CYCLE, PER_TYPE FROM PER_COMPENSATION_TEST 
								WHERE SUBSTR(CP_END_DATE,1,4) != '2012' and CP_CYCLE != 2 ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
				while($data = $db_dpis->get_array()){
					$CP_YEAR = trim($data[CP_YEAR]) + 543;
					$CP_CYCLE = $data[CP_CYCLE];
					$PER_TYPE = $data[PER_TYPE];

					$cmd = " SELECT AL_CODE, AL_NAME, AL_POINT_MIN, AL_POINT_MAX, ORG_ID, DEPARTMENT_ID, AM_CODE, AL_ACTIVE, 
									UPDATE_USER, UPDATE_DATE, AL_PERCENT
									FROM PER_ASSESS_LEVEL 
									WHERE PER_TYPE = $PER_TYPE AND AL_YEAR IS NULL AND AL_CYCLE IS NULL ";
					$db_dpis1->send_cmd($cmd);
					while($data_dpis1 = $db_dpis1->get_array()) {
						$AL_CODE = trim($data_dpis1[AL_CODE]);
						$AL_NAME = trim($data_dpis1[AL_NAME]);
						$AL_POINT_MIN = $data_dpis1[AL_POINT_MIN];
						$AL_POINT_MAX = $data_dpis1[AL_POINT_MAX];
						$ORG_ID = $data_dpis1[ORG_ID];
						$DEPARTMENT_ID = $data_dpis1[DEPARTMENT_ID];
						$AM_CODE = trim($data_dpis1[AM_CODE]);
						$AL_ACTIVE = $data_dpis1[AL_ACTIVE];
						$UPDATE_USER = $data_dpis1[UPDATE_USER];
						$UPDATE_DATE = trim($data_dpis1[UPDATE_DATE]);
						$AL_PERCENT = $data_dpis1[AL_PERCENT];
						if (!$AL_POINT_MIN) $AL_POINT_MIN = "NULL";
						if (!$AL_POINT_MAX) $AL_POINT_MAX = "NULL";
						if (!$ORG_ID) $ORG_ID = "NULL";
						if (!$DEPARTMENT_ID) $DEPARTMENT_ID = "NULL";
						if (!$AL_PERCENT) $AL_PERCENT = "NULL";

						if ($BKK_FLAG==1) {
/*							for ( $i=2; $i<35; $i++ ) { 
								$cmd = " insert into PER_ASSESS_LEVEL (AL_YEAR, AL_CYCLE, AL_CODE, AL_NAME, AL_POINT_MIN, AL_POINT_MAX, 
												ORG_ID, DEPARTMENT_ID, AM_CODE, AL_ACTIVE, UPDATE_USER, UPDATE_DATE, AL_PERCENT, PER_TYPE) 
												values ('$CP_YEAR', $CP_CYCLE, '$AL_CODE', '$AL_NAME', $AL_POINT_MIN, $AL_POINT_MAX, $ORG_ID, 
												$i, '$AM_CODE', $AL_ACTIVE, $UPDATE_USER, '$UPDATE_DATE', $AL_PERCENT, $PER_TYPE) ";
								$db_dpis2->send_cmd($cmd);
								//$db_dpis2->show_error();
							} */
						} else {
							$cmd = " insert into PER_ASSESS_LEVEL (AL_YEAR, AL_CYCLE, AL_CODE, AL_NAME, AL_POINT_MIN, AL_POINT_MAX, 
											ORG_ID, DEPARTMENT_ID, AM_CODE, AL_ACTIVE, UPDATE_USER, UPDATE_DATE, AL_PERCENT, PER_TYPE) 
											values ('$CP_YEAR', $CP_CYCLE, '$AL_CODE', '$AL_NAME', $AL_POINT_MIN, $AL_POINT_MAX, $ORG_ID, 
											$DEPARTMENT_ID, '$AM_CODE', $AL_ACTIVE, $UPDATE_USER, '$UPDATE_DATE', $AL_PERCENT, $PER_TYPE) ";
							$db_dpis2->send_cmd($cmd);
							//$db_dpis2->show_error();
						}
					} // end while						
				} // end while						

				$cmd = " update PER_ASSESS_LEVEL set AL_YEAR = '2555', AL_CYCLE = 2 where AL_YEAR is NULL and AL_CYCLE is NULL ";
				$db_dpis2->send_cmd($cmd);
				//$db_dpis2->show_error();

				$cmd = " delete from PER_ASSESS_LEVEL where AL_YEAR is NULL and AL_CYCLE is NULL ";
				$db_dpis2->send_cmd($cmd);
				//$db_dpis2->show_error();

				if($DPISDB=="oci8") {
					$cmd = " ALTER TABLE PER_ASSESS_LEVEL MODIFY AL_YEAR NOT NULL ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();

					$cmd = " ALTER TABLE PER_ASSESS_LEVEL MODIFY AL_CYCLE NOT NULL ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();

					$cmd = " ALTER TABLE PER_ASSESS_LEVEL ADD CONSTRAINT PK_PER_ASSESS_LEVEL PRIMARY KEY 
									  (AL_YEAR, AL_CYCLE, AL_CODE) ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				}
			}

			add_field("PER_PERFORMANCE_REVIEW", "PFR_TYPE","CHAR", "1", "NULL");
/*
			if ($BKK_FLAG==1) {
				$cmd = " UPDATE PER_POSITIONHIS SET POH_ORG2='สำนักงานเขต', POH_ORG3=POH_UNDER_ORG1, POH_UNDER_ORG1=POH_UNDER_ORG2, POH_UNDER_ORG2=NULL 
								WHERE POH_UNDER_ORG1 LIKE 'สำนักงานเขต%' AND POH_ORG2 IS NULL AND POH_ORG3 IS NULL AND POH_UNDER_ORG2 IS NOT NULL "; 
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITIONHIS SET POH_ORG2='สำนักงานเขต', POH_ORG3=POH_UNDER_ORG2, POH_UNDER_ORG2=NULL 
								WHERE POH_UNDER_ORG2 LIKE 'สำนักงานเขต%' AND POH_ORG2 IS NULL AND POH_ORG3 IS NULL "; 
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITIONHIS SET POH_ORG2=POH_UNDER_ORG1, POH_ORG3=POH_UNDER_ORG2, POH_UNDER_ORG1=NULL, POH_UNDER_ORG2=NULL 
								WHERE (POH_UNDER_ORG1 LIKE 'กรม%' OR POH_UNDER_ORG1 LIKE 'สำนัก%') AND POH_ORG2 IS NULL AND POH_ORG3 IS NULL "; 
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITIONHIS SET POH_ORG2=POH_ORG3, POH_ORG3=POH_UNDER_ORG1, POH_UNDER_ORG1=POH_UNDER_ORG2, POH_UNDER_ORG2=NULL 
								WHERE (POH_ORG3 LIKE 'กรม%' OR POH_ORG3 LIKE 'สำนัก%' OR POH_ORG3 LIKE 'สํานัก%' OR POH_ORG3 in ('สนน.', 'สนป.', 'สนร.', 'สนศ.', 'สนส.')) AND POH_ORG2 IS NULL "; 
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITIONHIS SET POH_ORG1=POH_ORG3, POH_ORG2=POH_UNDER_ORG1, POH_ORG3=POH_UNDER_ORG2, 
								POH_UNDER_ORG1=NULL, POH_UNDER_ORG2=NULL 
								WHERE (POH_ORG3 LIKE 'จังหวัด%' OR POH_ORG3 LIKE 'จ.%' OR POH_ORG3 LIKE 'กระทรวง%' OR POH_ORG3 LIKE 'องค์การบริหารส่วน%') AND POH_ORG2 IS NULL "; 
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITIONHIS SET POH_UNDER_ORG1='สำนักผังเมือง' WHERE POH_UNDER_ORG1 = 'สำนักผัังเมือง' "; 
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITIONHIS SET POH_ORG3='สำนักการศึกษา' WHERE POH_ORG3 in ('ึสำนักการศึกษา','่สำนักการศึกษา') "; 
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITIONHIS SET POH_ORG3='สำนักผังเมือง' WHERE POH_ORG3 in ('สำนัักผังเมือง', 'สำนัักผัังเมือง') "; 
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITIONHIS SET POH_ORG3='สำนักสวัสดิการสังคม' WHERE POH_ORG3 in ('โำนักสวัสดิการสังคม', 'สำันักสวัสดิการสังคม', 'สำนัักสวัสดิการสังคม', 'สำนัสกวัสดิการสังคม', 'สำนกสวัสดิการสังคม', 'สำยนักสวัสดิการสังคม') "; 
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITIONHIS SET POH_ORG3='วชิรพยาบาล' WHERE POH_ORG3 in ('วชิราพยาบาล', 'วชิิรพยาบาล', 'วิชรพยาบาล', 'วชิรพพยาบาล', 'วชิรพยาบาบ') "; 
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITIONHIS SET POH_ORG3='สำนักการแพทย์' WHERE POH_ORG3 in ('ะำนักการแพทย์', 'สำักการแพทย์', '่สำนักการแพทย์', 'สำนกการแพทย์') "; 
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITIONHIS SET POH_ORG3='สำนักการคลัง' WHERE POH_ORG3 in ('ลำนักการคลัง', 'สำักการคลัง', 'สำันักการคลัง') "; 
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITIONHIS SET POH_ORG3='สำนักอนามัย' WHERE POH_ORG3 in ('สานักอนามัย', 'สาำนักอนามัย', 'สำนกอนามัย', 'สำนัอนามัย') "; 
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITIONHIS SET POH_ORG3='สำนักการโยธา' WHERE POH_ORG3 in ('สำักการโยธา', 'สำนัักการโยธา', 'สำนนักการโยธา') "; 
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITIONHIS SET POH_ORG1='กระทรวงสาธารณสุข', POH_ORG2='สำนักงานปลัดกระทรวงสาธารณสุข' 
								WHERE (POH_ORG3 LIKE 'สาธารณสุขจังหวัด%' OR POH_ORG3 LIKE 'สสจ%') AND POH_ORG2 IS NULL "; 
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITIONHIS SET POH_ORG2=POH_UNDER_ORG1, POH_ORG3=POH_UNDER_ORG2, POH_UNDER_ORG1=NULL, POH_UNDER_ORG2=NULL 
								WHERE POH_UNDER_ORG1 IN ('สำนักงานเลขานุการสภากรุงเทพมหานคร', 'สำนักงานเลขานุการผู้ว่าราชการกรุงเทพมหานคร', 'สำนักงานเลขานุการผู้ว่าราชการ กรุงเทพมหานคร', 'สำนักงานเลขานุการผู้ว่าราชการ กทม.', 
								'สำนักงานคณะกรรมการข้าราชการกรุงเทพมหานคร', 'สำนักปลัดกรุงเทพมหานคร', 'สำนักงานปลัด กทม.', 
								'สำนักการแพทย์', 'สำนักอนามัย', 'สำนักการศึกษา', 'สำนักการโยธา', 'สำนักการระบายน้ำ', 'สำนักการคลัง', 'สำนักเทศกิจ', 'สำนักการจราจรและขนส่ง', 'สำนักผังเมือง', 'สำนักป้องกันและบรรเทาสาธารณภัย', 
								'สำนักงบประมาณกรุงเทพมหานคร', 'สำนักยุทธศาสตร์และประเมินผล', 'สำนักสิ่งแวดล้อม', 'สำนักวัฒนธรรม กีฬา และการท่องเที่ยว', 'สำนักพัฒนาสังคม', 'ศูนย์รับเรื่องร้องเรียน', 'หน่วยปฎิบัติงานร่วมกับกรุงเทพมหานคร', 
								'มหาวิทยาลัยกรุงเทพมหานคร', 'สำนักงานสถานธนานุบาลกรุงเทพมหานคร', 'สำนักงานตลาดกรุงเทพมหานคร', 'สำนักงานปุ๋ยกรุงเทพมหานคร', 'สำนักงานพัฒนาที่อยู่อาศัย', 'กองอำนวยการตลาดนัดกรุงเทพมหานคร', 
								'สำนักนโยบายและแผนกรุงเทพมหานคร', 'สำนักนโยบายและแผน กทม.') AND 
								POH_ORG2 IS NULL AND POH_ORG3 IS NULL AND POH_UNDER_ORG2 IS NOT NULL "; 
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITIONHIS SET POH_ORG2=POH_ORG3, POH_ORG3=POH_UNDER_ORG1, POH_UNDER_ORG1=POH_UNDER_ORG2, POH_UNDER_ORG2=NULL 
								WHERE POH_ORG3 IN ('สนพ.') AND 
								POH_ORG2 IS NULL AND POH_ORG3 IS NOT NULL "; 
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITIONHIS SET POH_ORG2=POH_UNDER_ORG2, POH_UNDER_ORG2=NULL 
								WHERE POH_UNDER_ORG1 = 'กองส่งเสริมอาชีพ' AND POH_UNDER_ORG2 = 'สำนักพัฒนาสังคม' "; 
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITIONHIS SET POH_ORG3='กองตำแหน่งและอัตราเงินเดือน', POH_UNDER_ORG1='ฝ่ายตำแหน่งที่ 1' 
								WHERE POH_ORG3 = 'ฝ่ายตำแหน่งที่ 1 กองตำแหน่งและอัตราเงินเดือน' AND POH_UNDER_ORG1 IS NULL "; 
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITIONHIS SET POH_ORG3='กองบริหารทั่วไปและการสอบ', POH_UNDER_ORG1='ฝ่ายบริหารงานทั่วไป' 
								WHERE POH_ORG3 = 'ฝ่ายบริหารงานทั่วไป กองบริหารทั่วไปและการสอบ' AND POH_UNDER_ORG1 IS NULL "; 
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITIONHIS SET POH_ORG3='โรงพยาบาลตากสิน', POH_UNDER_ORG1='กองศัลยกรรม' 
								WHERE POH_ORG3 = 'กองศัลยกรรม โรงพยาบาลตากสิน' AND POH_UNDER_ORG1 IS NULL "; 
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITIONHIS SET POH_ORG3='โรงพยาบาลเจริญกรุงประชารักษ์', POH_UNDER_ORG1='กองศัลยกรรม' 
								WHERE POH_ORG3 = 'กองศัลยกรรม โรงพยาบาลเจริญกรุงประชารักษ์' AND POH_UNDER_ORG1 IS NULL "; 
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITIONHIS SET POH_ORG3='กองการเจ้าหน้าที่', POH_UNDER_ORG1='งานการสอบ' 
								WHERE POH_ORG3 = 'งานการสอบ  กองการเจ้าหน้าที่' AND POH_UNDER_ORG1 IS NULL "; 
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITIONHIS SET POH_ORG2='สำนักปลัดกรุงเทพมหานคร', POH_ORG3='กองการเจ้าหน้าที่' 
								WHERE POH_ORG3 in ('กกจ.สนป.', 'กองการเจ้าหน้าที่ สนป.', 'กองการเจ้าหน้าที่สำนักงานปลัดกรุงเทพมหานคร') AND POH_ORG2 IS NULL "; 
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITIONHIS SET POH_ORG2='สำนักการศึกษา', POH_ORG3='กองการเจ้าหน้าที่' 
								WHERE POH_ORG3 in ('กกจ.สนศ.', 'กองการเจ้าหน้าที่ สนศ.', 'กองการเจ้าหน้าที่ สำนักการศึกษา') AND POH_ORG2 IS NULL "; 
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITIONHIS SET POH_ORG2='สำนักการศึกษา', POH_ORG3='กองคลัง' 
								WHERE POH_ORG3 in ('กองคลัง สนศ.', 'กองคลัง สำนักการศึกษา') AND POH_ORG2 IS NULL "; 
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITIONHIS SET POH_ORG2='สำนักการศึกษา', POH_ORG3='กองโรงเรียน' 
								WHERE POH_ORG3 in ('กองโรงเรียน สนศ.', 'กองโรงเรียน สำนักการศึกษา', 'กองโรงเรียน สำนัการศึกษา') AND POH_ORG2 IS NULL "; 
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITIONHIS SET POH_ORG2='สำนักการศึกษา', POH_ORG3='กองวิชาการ' 
								WHERE POH_ORG3 in ('กองวิชาการ สนศ.', 'กองวิชาการ สำนักการศึกษา') AND POH_ORG2 IS NULL "; 
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITIONHIS SET POH_ORG2='สำนักการศึกษา', POH_ORG3='สำนักงานเลขานุการ' 
								WHERE POH_ORG3 = 'สก.สนศ.' AND POH_ORG2 IS NULL "; 
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITIONHIS SET POH_ORG2='สำนักสวัสดิการสังคม', POH_ORG3='กองนันทนาการ' 
								WHERE POH_ORG3 = 'กองนันทนาการ สำนักสวัสดิการสังคม' AND POH_ORG2 IS NULL "; 
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITIONHIS SET POH_ORG2='สำนักปลัดกรุงเพทมหานคร', POH_ORG3='กองกลาง' 
								WHERE POH_ORG3 = 'กองกลาง สำนักปลัดกรุงเพทมหานคร' AND POH_ORG2 IS NULL "; 
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITIONHIS SET POH_ORG2='สำนักสวัสดิการสังคม', POH_ORG3='กองสันทนาการ' 
								WHERE POH_ORG3 in ('กองสันทนาการ', 'กองสันทนาการ สำนักสวัสดิการสังคม', '') AND POH_ORG2 IS NULL "; 
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITIONHIS SET POH_ORG2='สำนักการระบายน้ำ' 
								WHERE POH_ORG3 = 'กองควบคุมระบบระบายน้ำ' AND POH_ORG2 IS NULL "; 
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITIONHIS SET POH_ORG2='สำนักผังเมือง', POH_UNDER_ORG2=NULL 
								WHERE POH_UNDER_ORG2 = 'สำนักผังเมือง' AND POH_ORG2 IS NULL AND POH_ORG3 IS NOT NULL "; 
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITIONHIS SET POH_ORG2='สำนักงานเขต' 
								WHERE (POH_ORG3 LIKE 'เขต%' OR POH_ORG3 LIKE 'สํานักงานเขต%') AND POH_ORG2 IS NULL "; 
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITIONHIS SET POH_ORG2='สำนักการโยธา', POH_ORG3='กองก่อสร้างและบูรณะ' 
								WHERE POH_ORG3 in ('กองก่อสร้างและบุรณะ', 'กองก่อสร้างและบูรณะ  สำนักการโยธา', 'กองก่อสร้างและบูรณะ สำนักการโยธา', '') AND POH_ORG2 IS NULL "; 
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITIONHIS SET POH_ORG2='สำนักปลัดกรุงเทพมหานคร', POH_ORG3='กองผังเมือง' 
								WHERE POH_ORG3 in ('กองผังเมือง สำนักงานปลัดกรุงเทพมหานคร', 'กองผังเมือง สำนักปลัดกรุงเทพมหานคร', '') AND POH_ORG2 IS NULL "; 
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITIONHIS SET POH_ORG2='สำนักการแพทย์', POH_ORG3='โรงพยาบาลกลาง' 
								WHERE POH_ORG3 in ('ร.พ.กลาง', 'รพ.กลาง', 'โรงพยาบาลกลาง', 'โรงพยาบาลกลาง สำนักการแพทย์') AND POH_ORG2 IS NULL "; 
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITIONHIS SET POH_ORG2='สำนักการแพทย์', POH_ORG3='โรงพยาบาลเจริญกรุงประชารักษ์' 
								WHERE POH_ORG3 in ('โรงพยาบาลเจริญกรุงประชารักษ์', 'รพ.เจริญกรุงประชารักษ์', 'โรงพยาบาลเจริญกรุงประรักษ์', 'โรงเรียนพยาบาลเจริญกรุงประชารักษ์ สนพ.') AND POH_ORG2 IS NULL "; 
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITIONHIS SET POH_ORG2='สำนักการแพทย์', POH_ORG3='โรงพยาบาลตากสิน' 
								WHERE POH_ORG3 in ('โรงพยบาบาลตากสิน สำนักการแพทย์', 'โรงพยาบาลตากสิน', 'โรงพยาบาลตากสิน สำนักการแพทย์', 'รพ.ตากสิน', 'รพ.ตากสิน สำนักการแพทย์') AND POH_ORG2 IS NULL "; 
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITIONHIS SET POH_ORG2='สำนักการแพทย์', POH_ORG3='วชิรพยาบาล' 
								WHERE POH_ORG3 in ('วชพ.', 'วชิรพยาบาล', 'วชิรพยาบาล สนพ.', 'วชิรพยาบาล สำนักการแพทย์') AND POH_ORG2 IS NULL "; 
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
//select distinct POH_PL_NAME, POH_ORG1, POH_ORG2, POH_ORG3, POH_UNDER_ORG1, POH_UNDER_ORG2, POH_ORG from PER_POSITIONHIS 
//								WHERE POH_ORG2 is null AND POH_ORG3 IS NULL and POH_UNDER_ORG1 is not null order by POH_ORG1, POH_ORG2, POH_ORG3, POH_UNDER_ORG1, POH_UNDER_ORG2
//select distinct POH_PL_NAME, POH_ORG1, POH_ORG2, POH_ORG3, POH_UNDER_ORG1, POH_UNDER_ORG2, POH_ORG from PER_POSITIONHIS 
//								WHERE POH_ORG2 is null AND POH_ORG3 IS not NULL order by POH_ORG1, POH_ORG2, POH_ORG3, POH_UNDER_ORG1, POH_UNDER_ORG2
//select * from per_positionhis where poh_pos_no_name like '%..%'

				$cmd = " UPDATE PER_SKILL SET SG_CODE='010' WHERE (SKILL_NAME LIKE '%เวชกรรม%' OR SKILL_NAME LIKE 'ศัลยกรรม%') AND SG_CODE = '990' "; 
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_SKILL SET SG_CODE='030' WHERE SKILL_NAME LIKE '%เภสัช%' AND SG_CODE = '990' "; 
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_SKILL SET SG_CODE='040' WHERE SKILL_NAME LIKE '%พยาบาล%' AND SG_CODE = '990' "; 
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET CL_NAME='ชำนาญการพิเศษ', PC_CODE='03' WHERE POS_NO_NAME||POS_NO IN 
								('สก.สนพ.20', 'สก.สนพ.35', 'สก.สนพ.59', 'กว.สนพ.8', 'กว.สนพ.16', 'รพก.4', 'รพก.36', 'รพก.101', 'รพก.102', 'รพก.103', 'รพก.104', 'รพก.105', 
								'รพก.106', 'รพก.634', 'รพก.778', 'รพก.786', 'รพก.787', 'รพก.788', 'รพก.789', 'รพก.791', 'รพก.807', 'รพก.808', 'รพก.832', 'รพต.4', 
								'รพต.33', 'รพต.97', 'รพต.98', 'รพต.99', 'รพต.100', 'รพต.101', 'รพต.102', 'รพต.103', 'รพต.104', 'รพต.105', 'รพต.106', 'รพต.107', 
								'รพต.108', 'รพต.544', 'รพต.661', 'รพต.670', 'รพต.671', 'รพต.672', 'รพต.673', 'รพต.675', 'รพต.690', 'รพต.714', 'รพต.723', 'รพจ.4', 
								'รพจ.38', 'รพจ.103', 'รพจ.104', 'รพจ.105', 'รพจ.106', 'รพจ.107', 'รพจ.108', 'รพจ.109', 'รพจ.110', 'รพจ.111', 'รพจ.112', 'รพจ.479', 
								'รพจ.589', 'รพจ.595', 'รพจ.596', 'รพจ.598', 'รพจ.599', 'รพจ.601', 'รพจ.611', 'รพจ.634', 'รพว.4', 'รพว.18', 'รพว.30', 'รพว.101', 'รพว.113', 
								'รพว.128', 'รพท.4', 'รพท.19', 'รพท.31', 'รพท.98', 'รพท.109', 'รพท.123', 'รพล.2', 'รพล.17', 'รพล.69', 'รพล.76', 'รพล.90', 'รพล.91', 'รพร.4', 
								'รพร.19', 'รพร.31', 'รพร.102', 'รพร.113', 'รพร.128', 'รพส.4', 'รพส.23', 'รพส.35', 'รพส.131', 'รพส.147', 'รพส.170', 'ศบฉ.6', 'ศบฉ.10') "; 
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PM_CODE='40019' WHERE POS_NO_NAME||POS_NO IN ('สก.สนพ.6', 'สก.สนพ.20', 'สก.สนพ.35', 'สก.สนพ.59', 'รพก.639', 'รพก.641', 'รพก.647', 
								'รพก.664', 'รพก.667', 'รพก.689', 'รพก.701', 'รพก.705', 'รพก.709', 'รพก.715', 'รพก.717', 'รพก.719', 'รพก.732', 'รพก.755', 'รพก.767', 'รพก.777', 'รพก.785', 'รพก.806', 'รพก.830', 'รพต.548', 
								'รพต.550', 'รพต.563', 'รพต.566', 'รพต.575', 'รพต.586', 'รพต.595', 'รพต.602', 'รพต.606', 'รพต.608', 'รพต.612', 'รพต.614', 'รพต.623', 'รพต.637', 'รพต.651', 'รพต.660', 'รพต.668', 'รพต.689', 
								'รพต.712', 'รพว.101', 'รพท.98', 'รพล.69', 'รพร.102', 'รพส.131') "; // หัวหน้ากลุ่มงาน
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PM_CODE='40011' WHERE POS_NO_NAME||POS_NO IN ('กว.สนพ.8', 'กว.สนพ.16', 'รพก.4', 'รพก.36', 'รพก.634', 'รพต.4', 'รพต.33', 'รพต.544', 'รพจ.4', 
								'รพจ.38', 'รพจ.479', 'รพว.4', 'รพว.18', 'รพท.4', 'รพท.19', 'รพล.2', 'รพล.17', 'รพร.4', 'รพร.19', 'รพส.4', 'รพส.23') "; // หัวหน้าฝ่าย
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PM_CODE='40014' WHERE POS_NO_NAME||POS_NO IN ('รพก.101', 'รพต.97', 'รพจ.103', 'รพว.128', 'รพล.90', 'รพร.128', 'รพส.170') "; // หัวหน้าพยาบาล
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_SKILL SET SG_CODE='010' WHERE SKILL_CODE IN ('1001', '1002', '1003', '1004', '1005', '1006', '1007', '1008', '1009', '1010', '1011', '1012', '1013', 
								'1014', '1015', '1016', '1017', '1018', '1019', '1020', '1021', '1022', '1023', '1024', '1025', '1026', '1027', '1028', '1029', '1030', '1031', '1032', '1033', '1034') "; 
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_SKILL SET SG_CODE='020' WHERE SKILL_CODE IN ('2001', '2002', '2003', '2004') "; 
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_SKILL SET SG_CODE='030' WHERE SKILL_CODE IN ('3001', '3002', '3003', '3004', '3005', '3006', '3007', '3008') "; 
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_SKILL SET SG_CODE='040' WHERE SKILL_CODE IN ('4004', '4005', '4006', '4007', '4008', '4009', '4010', '4013', '4014', '4017', '4018', '4019', '4020', 
								'4021', '4022', '4023', '4024', '4025', '4026', '4027', '4028', '4029', '4030', '4031', '4032', '4033') "; 
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='11001' WHERE PL_CODE='10109' "; // นักบริหาร
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='21004' WHERE PL_CODE='10208' "; // ผู้ตรวจราชการ
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='31006' WHERE PL_CODE='10403' "; // เจ้าพนักงานปกครอง
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='41006' WHERE PL_CODE='10501' "; // เจ้าหน้าที่ปกครอง
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='41006' WHERE PL_CODE='10512' "; // พนักงานปกครอง
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='31004' WHERE PL_CODE='10525' "; // เจ้าหน้าที่บริหารงานปกครอง
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='31002' WHERE PL_CODE='11403' "; // บุคลากร
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='41005' WHERE PL_CODE='12201' "; // เจ้าหน้าที่สถิติ
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='41005' WHERE PL_CODE='12212' "; // เจ้าพนักงานสถิติ
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='31012' WHERE PL_CODE='12223' "; // นักสถิติ
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='41004' WHERE PL_CODE='12301' "; // เจ้าหน้าที่เวชสถิติ
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='31003' WHERE PL_CODE='12403' "; // นิติกร
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='31009' WHERE PL_CODE='13503' "; // นักวิจัยการจราจร
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='32002' WHERE PL_CODE='20103' "; // นักวิชาการคลัง
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='42002' WHERE PL_CODE='20201' "; // เจ้าหน้าที่การคลัง
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='42002' WHERE PL_CODE='20212' "; // เจ้าพนักงานการคลัง
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='32002' WHERE PL_CODE='20224' "; // เจ้าหน้าที่บริหารงานการคลัง
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='32005' WHERE PL_CODE='20603' "; // เจ้าหน้าที่ตรวจสอบภายใน
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

//				$cmd = " UPDATE PER_POSITION SET PL_CODE='' WHERE PL_CODE='20801' "; // เจ้าหน้าที่ตรวจสอบบัญชี
//				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='42003' WHERE PL_CODE='22901' "; // เจ้าหน้าที่จัดเก็บรายได้
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='42003' WHERE PL_CODE='22912' "; // เจ้าพนักงานจัดเก็บรายได้
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='38008' WHERE PL_CODE='24003' "; // นักวิชาการละครและดนตรี
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='38009' WHERE PL_CODE='26203' "; // นักวิชาการวัฒนธรรม
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='43002' WHERE PL_CODE='31201' "; // เจ้าหน้าที่สื่อสาร
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='38004' WHERE PL_CODE='31303' "; // นักพัฒนาการท่องเที่ยว
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='43001' WHERE PL_CODE='31801' "; // เจ้าหน้าที่ประชาสัมพันธ์
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='43001' WHERE PL_CODE='31824' "; // เจ้าหน้าที่บริหารงานประชาสัมพันธ์
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='43001' WHERE PL_CODE='31902' "; // เจ้าพนักงานประชาสัมพันธ์
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='43003' WHERE PL_CODE='32501' "; // เจ้าหน้าที่โสตทัศนศึกษา
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='43003' WHERE PL_CODE='32512' "; // เจ้าพนักงานโสตทัศนศึกษา
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='33002' WHERE PL_CODE='32523' "; // นักวิชาการโสตทัศนศึกษา
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='45001' WHERE PL_CODE='50201' "; // เจ้าหน้าที่วิทยาศาสตร์
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='45001' WHERE PL_CODE='50212' "; // เจ้าพนักงานวิทยาศาสตร์
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='36007' WHERE PL_CODE='60104' "; // นายแพทย์
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='36003' WHERE PL_CODE='60204' "; // ทันตแพทย์
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='36005' WHERE PL_CODE='60304' "; // นายสัตวแพทย์
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='46006' WHERE PL_CODE='60801' "; // โภชนากร
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='36009' WHERE PL_CODE='60813' "; // นักโภชนาการ
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='36002' WHERE PL_CODE='61203' "; // นักจิตวิทยา
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='36012' WHERE PL_CODE='61303' "; // นักวิชาการสุขศึกษา
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='36011' WHERE PL_CODE='61514' "; // นักวิชาการพยาบาล
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='36006' WHERE PL_CODE='61523' "; // พยาบาลวิชาชีพ
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='46007' WHERE PL_CODE='61702' "; // เจ้าหน้าที่เอ็กซ์เรย์
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='46007' WHERE PL_CODE='61712' "; // เจ้าหน้าที่รังสีการแพทย์
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='36010' WHERE PL_CODE='61723' "; // นักรังสีการแพทย์
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='36001' WHERE PL_CODE='61803' "; // นักกายภาพบำบัด
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='36014' WHERE PL_CODE='61903' "; // นักอาชีวบำบัด
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='46011' WHERE PL_CODE='62102' "; // สัตวแพทย์
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='36012' WHERE PL_CODE='62503' "; // นักวิชาการสาธารณสุข
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='46009' WHERE PL_CODE='63001' "; // เจ้าหน้าที่ส่งเสริมสุขภาพ
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='46009' WHERE PL_CODE='63012' "; // เจ้าพนักงานส่งเสริมสุขภาพ
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='36012' WHERE PL_CODE='63023' "; // นักวิชาการส่งเสริมสุขภาพ
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='46002' WHERE PL_CODE='63031' "; // เจ้าหน้าที่อนามัย
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='46009' WHERE PL_CODE='63101' "; // เจ้าหน้าที่ควบคุมโรค
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='46009' WHERE PL_CODE='63112' "; // เจ้าพนักงานควบคุมโรค
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='36012' WHERE PL_CODE='63123' "; // นักวิชาการควบคุมโรค
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='46009' WHERE PL_CODE='63202' "; // เจ้าพนักงานสาธารณสุข
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='37006' WHERE PL_CODE='70103' "; // วิศวกร
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

//				$cmd = " UPDATE PER_POSITION SET PL_CODE='' WHERE PL_CODE='70491' "; // วิศวกรเครื่องกล/ไฟฟ้า/สุขาภิบาล
//				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='47008' WHERE PL_CODE='71401' "; // ช่างโยธา
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='47008' WHERE PL_CODE='71412' "; // นายช่างโยธา
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='47010' WHERE PL_CODE='71601' "; // ช่างสำรวจ
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='47010' WHERE PL_CODE='71612' "; // นายช่างสำรวจ
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='47002' WHERE PL_CODE='72901' "; // เจ้าหน้าที่เครื่องคอมพิวเตอร์
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='47005' WHERE PL_CODE='73501' "; // ช่างเทคนิค
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='47005' WHERE PL_CODE='73512' "; // นายช่างเทคนิค
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='37002' WHERE PL_CODE='74001' "; // เจ้าหน้าที่ป้องกันและบรรเทาสาธารณภัย
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='37003' WHERE PL_CODE='74003' "; // มัณฑนากร
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='47011' WHERE PL_CODE='74102' "; // พนักงานป้องกันและบรรเทาสาธารณภัย
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='47003' WHERE PL_CODE='74301' "; // ช่างเขียนแบบ
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='47003' WHERE PL_CODE='74312' "; // นายช่างเขียนแบบ
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='47009' WHERE PL_CODE='74401' "; // ช่างศิลป์
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='47009' WHERE PL_CODE='74412' "; // นายช่างศิลป์
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='37004' WHERE PL_CODE='74423' "; // นักวิชาการช่างศิลป์
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='47007' WHERE PL_CODE='74901' "; // ช่างภาพ
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='47007' WHERE PL_CODE='74912' "; // นายช่างภาพ
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='37011' WHERE PL_CODE='75003' "; // ช่างภาพการแพทย์
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='47001' WHERE PL_CODE='75105' "; // ช่างกายอุปกรณ์
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='38010' WHERE PL_CODE='80103' "; // นักวิชาการศึกษา
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='38011' WHERE PL_CODE='80513' "; // นักวิชาการศึกษาพิเศษ
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='31002' WHERE PL_CODE='80603' "; // เจ้าหน้าที่ฝึกอบรม
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='38003' WHERE PL_CODE='80902' "; // เจ้าหน้าที่พลศึกษา
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='38001' WHERE PL_CODE='81303' "; // บรรณารักษ์
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='48005' WHERE PL_CODE='81512' "; // เจ้าพนักงานห้องสมุด
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='48002' WHERE PL_CODE='82291' "; // ดุริยางคศิลปิน (นักดนตรี)
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='48002' WHERE PL_CODE='82292' "; // ดุริยางคศิลปิน (เจ้าหน้าที่โฆษกและตลก)
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='48002' WHERE PL_CODE='82293' "; // ดุริยางคศิลปิน (นักแต่งเพลง)
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='48002' WHERE PL_CODE='82294' "; // ดุริยางคศิลปิน (นักแยกเพลง)
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='48002' WHERE PL_CODE='82295' "; // ดุริยางคศิลปิน (เจ้าหน้าที่เขียนเพลง)
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='48001' WHERE PL_CODE='82390' "; // คีตศิลปิน (นักร้อง)
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='48003' WHERE PL_CODE='83701' "; // เจ้าหน้าที่พัฒนาชุมชน
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='48003' WHERE PL_CODE='83712' "; // เจ้าพนักงานพัฒนาชุมชน
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='38007' WHERE PL_CODE='84203' "; // เจ้าหน้าที่จัดหาที่ดิน
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='38010' WHERE PL_CODE='84512' "; // เจ้าพนักงานการศึกษา
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='41002' WHERE PL_CODE='11612' "; // เจ้าพนักงานธุรการ
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='31011' WHERE PL_CODE='11723' "; // นักวิชาการพัสดุ
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='32003' WHERE PL_CODE='20423' "; // นักวิชาการเงินและบัญชี
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='32004' WHERE PL_CODE='22923' "; // นักวิชาการจัดเก็บรายได้
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='44001' WHERE PL_CODE='40412' "; // เจ้าพนักงานการเกษตร
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='42001' WHERE PL_CODE='20412' "; // เจ้าพนักงานการเงินและบัญชี
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='47004' WHERE PL_CODE='71912' "; // นายช่างเครื่องกล
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='47006' WHERE PL_CODE='73012' "; // นายช่างไฟฟ้า
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='46010' WHERE PL_CODE='61502' "; // พยาบาลเทคนิค
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='46008' WHERE PL_CODE='62212' "; // เจ้าพนักงานวิทยาศาสตร์การแพทย์
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='46005' WHERE PL_CODE='62312' "; // เจ้าพนักงานเภสัชกรรม
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='37002' WHERE PL_CODE='74203' "; // เจ้าพนักงานป้องกันและบรรเทาสาธารณภัย
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='38005' WHERE PL_CODE='83723' "; // นักพัฒนาชุมชน
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='48004' WHERE PL_CODE='84612' "; // เจ้าพนักงานศูนย์เยาวชน 
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='38012' WHERE PL_CODE='84623' "; // นักวิชาการศูนย์เยาวชน
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='34001' WHERE PL_CODE='40103' "; // นักวิชาการเกษตร
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='41003' WHERE PL_CODE='11712' "; // เจ้าพนักงานพัสดุ
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

		// ปรับปรุง pos_id
				$cmd = " SELECT POS_ID, PER_ID FROM PER_PERSONAL WHERE PER_TYPE = 1 AND POS_ID IS NOT NULL ORDER BY PER_ID ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
				while($data = $db_dpis->get_array()){
					$POS_ID = $data[POS_ID];
					$PER_ID = $data[PER_ID];

					$cmd = " SELECT POS_NO_NAME, POS_NO FROM PER_POSITION WHERE POS_ID = $POS_ID ";
					$db_dpis1->send_cmd($cmd);
					$data_dpis1 = $db_dpis1->get_array();
					$POS_NO_NAME = trim($data_dpis1[POS_NO_NAME]);
					$POS_NO = trim($data_dpis1[POS_NO]);

					$cmd = " SELECT POS_ID FROM PER_POSITION
							WHERE POS_NO_NAME = '$POS_NO_NAME' AND POS_NO = '$POS_NO' AND POS_STATUS = 1 AND 
							PL_CODE IN ('31001', '31007', '31008', '31010', '31011', '32003', '32004', '33001', '34001', '35001', '36004', '36008', '36012', '36013', 
							'36015', '37001', '37002', '37007', '37008', '37009', '37010', '37012', '38005', '38012', '38013', '38014', '41002', '41003', '42001', '44001', 
							'46001', '46003', '46004', '46005', '46008', '46009', '46010', '47004', '47006', '48001', '48002', '48004', '48005') ";
					$db_dpis1->send_cmd($cmd);
					$data_dpis1 = $db_dpis1->get_array();
					$NEW_POS_ID = $data_dpis1[POS_ID];

					if ($NEW_POS_ID && $NEW_POS_ID != $POS_ID) {
						$cmd = " UPDATE PER_PERSONAL SET POS_ID = $NEW_POS_ID WHERE PER_ID = $PER_ID ";
						$db_dpis1->send_cmd($cmd);
						//$db_dpis1->show_error();

						$cmd = " DELETE FROM PER_POSITION WHERE POS_ID = $POS_ID ";
						$db_dpis1->send_cmd($cmd);
						//$db_dpis1->show_error();
					}
				} // end while						

				$cmd = " SELECT POS_ID, POS_NO_NAME, POS_NO FROM PER_POSITION WHERE POS_NO_NAME||POS_NO IN 
							(SELECT POS_NO_NAME||POS_NO FROM PER_POSITION WHERE POS_STATUS = 1 GROUP BY POS_NO_NAME||POS_NO HAVING COUNT(*) > 1) AND 
							PL_CODE IN ('11003', '10703', '11013', '11734', '20435', '22934', '31813', '40424', '50103', '60503', '60603', '62514', '61403', '60403', 
							'75505', '74306', '70403', '70503', '70203', '75603', '73803', '83734', '84624', '84625', '84626', '84627', '84628', '84635', '84003', '82903', 
							'11601', '11624', '11801', '11701', '20401', '40401', '63131', '62601', '62802', '62301', '62201', '62514', '61601', '71901', '73001', '82301', 
							'82201', '84601', '84602', '84603', '84604', '84605', '84613', '84614', '84615', '84616', '84617', '84635', '81501') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
				while($data = $db_dpis->get_array()){
					$POS_ID = $data[POS_ID];
					$POS_NO_NAME = trim($data[POS_NO_NAME]);
					$POS_NO = trim($data[POS_NO]);

					$cmd = " SELECT POS_ID FROM PER_POSITION WHERE POS_NO_NAME = '$POS_NO_NAME' AND POS_NO = '$POS_NO' AND POS_ID != $POS_ID ";
					$count_data = $db_dpis1->send_cmd($cmd);

					if ($count_data) {
						$cmd = " DELETE FROM PER_POSITION WHERE POS_ID = $POS_ID ";
						$db_dpis1->send_cmd($cmd);
						//$db_dpis1->show_error();
					}
				} // end while						

				$cmd = " UPDATE PER_POSITION SET PL_CODE='31007' WHERE PL_CODE='11003' "; // เจ้าหน้าที่ระบบงานคอมพิวเตอร์
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='31008' WHERE PL_CODE='10703' "; // เจ้าหน้าที่วิเคราะห์นโยบายและแผน
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='31010' WHERE PL_CODE='11013' "; // นักวิชาการคอมพิวเตอร์
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='31011' WHERE PL_CODE='11734' "; // เจ้าหน้าที่บริหารงานพัสดุ
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='32003' WHERE PL_CODE='20435' "; // เจ้าหน้าที่บริหารงานการเงินและบัญชี
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='32004' WHERE PL_CODE='22934' "; // เจ้าหน้าที่บริหารงานจัดเก็บรายได้
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='33001' WHERE PL_CODE='31813' "; // นักประชาสัมพันธ์
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='34001' WHERE PL_CODE='40424' "; // เจ้าหน้าที่บริหารงานการเกษตร
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='35001' WHERE PL_CODE='50103' "; // นักวิทยาศาสตร์
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='36004' WHERE PL_CODE='60503' "; // นักเทคนิคการแพทย์
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='36008' WHERE PL_CODE='60603' "; // เภสัชกร
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='36012' WHERE PL_CODE='62514' AND CL_NAME IN ('7', '8', 'ชำนาญการ') "; // เจ้าหน้าที่บริหารงานสาธารณสุข
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='36013' WHERE PL_CODE='61403' "; // นักวิชาการสุขาภิบาล
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='36015' WHERE PL_CODE='60403' "; // นักวิทยาศาสตร์การแพทย์
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='37001' WHERE PL_CODE='75505' "; // เจ้าหน้าที่บริหารงานโยธา
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='37002' WHERE PL_CODE='74306' "; // เจ้าหน้าที่บริหารงานป้องกันและบรรเทาสาธารณภัย
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='37007' WHERE PL_CODE='70403' "; // วิศวกรเครื่องกล
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='37008' WHERE PL_CODE='70503' "; // วิศวกรไฟฟ้า
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='37009' WHERE PL_CODE='70203' "; // วิศวกรโยธา
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='37010' WHERE PL_CODE='75603' "; // วิศวกรสุขาภิบาล
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='37012' WHERE PL_CODE='73803' "; // สถาปนิก
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='38005' WHERE PL_CODE='83734' "; // เจ้าหน้าที่บริหารงานพัฒนาชุมชน
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='38012' WHERE PL_CODE='84624' "; // นักวิชาการศูนย์เยาวชน (ผู้นำกิจกรรมพลศึกษา)
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='38012' WHERE PL_CODE='84625' "; // นักวิชาการศูนย์เยาวชน (ผู้นำกิจกรรมศิลปะ)
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='38012' WHERE PL_CODE='84626' "; // นักวิชาการศูนย์เยาวชน (ผู้นำกิจกรรมคหกรรมศาสตร์)
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='38012' WHERE PL_CODE='84627' "; // นักวิชาการศูนย์เยาวชน (ผู้นำกิจกรรมนาฏศิลป์)
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='38012' WHERE PL_CODE='84628' "; // นักวิชาการศูนย์เยาวชน (ผู้นำกิจกรรมห้องสมุด)
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='38012' WHERE PL_CODE='84635' AND CL_NAME IN ('7', 'ชำนาญการ') "; // เจ้าหน้าที่บริหารงานศูนย์เยาวชน
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='38013' WHERE PL_CODE='84003' "; // นักวิชาการสิ่งแวดล้อม
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='38014' WHERE PL_CODE='82903' "; // นักสังคมสงเคราะห์
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='41002' WHERE PL_CODE='11601' "; // เจ้าหน้าที่ธุรการ
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='41002' WHERE PL_CODE='11624' "; // เจ้าหน้าที่บริหารงานธุรการ
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='41002' WHERE PL_CODE='11801' "; // เจ้าหน้าที่บันทึกข้อมูล
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='41003' WHERE PL_CODE='11701' "; // เจ้าหน้าที่พัสดุ
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='42001' WHERE PL_CODE='20401' "; // เจ้าหน้าที่การเงินและบัญชี
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='44001' WHERE PL_CODE='40401' "; // เจ้าหน้าที่การเกษตร
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='46001' WHERE PL_CODE='63131' "; // เจ้าหน้าที่งานรักษาความสะอาด
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='46003' WHERE PL_CODE='62601' "; // ผู้ช่วยทันตแพทย์
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='46004' WHERE PL_CODE='62802' "; // เจ้าพนักงานทันตสาธารณสุข
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='46005' WHERE PL_CODE='62301' "; // ผู้ช่วยเภสัชกร
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='46008' WHERE PL_CODE='62201' "; // เจ้าหน้าที่วิทยาศาสตร์การแพทย์
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='46009' WHERE PL_CODE='62514' AND CL_NAME IN ('6', 'ชำนาญงาน', 'อาวุโส') "; // เจ้าหน้าที่บริหารงานสาธารณสุข
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='46010' WHERE PL_CODE='61601' "; // เจ้าหน้าที่พยาบาล
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='47004' WHERE PL_CODE='71901' "; // ช่างเครื่องกล
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='47006' WHERE PL_CODE='73001' "; // ช่างไฟฟ้า
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='48001' WHERE PL_CODE='82301' "; // คีตศิลปิน
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='48002' WHERE PL_CODE='82201' "; // ดุริยางคศิลปิน
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='48004' WHERE PL_CODE='84601' "; // เจ้าหน้าที่ศูนย์เยาวชน 
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='48004' WHERE PL_CODE='84602' "; // เจ้าหน้าที่ศูนย์เยาวชน (ผู้นำกิจกรรมศิลปะ)
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='48004' WHERE PL_CODE='84603' "; // เจ้าหน้าที่ศูนย์เยาวชน (ผู้นำกิจกรรมคหกรรมศาสตร์)
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='48004' WHERE PL_CODE='84604' "; // เจ้าหน้าที่ศูนย์เยาวชน (ผู้นำกิจกรรมนาฏศิลป์)
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='48004' WHERE PL_CODE='84605' "; // เจ้าหน้าที่ศูนย์เยาวชน (ผู้นำกิจกรรมห้องสมุด)
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='48004' WHERE PL_CODE='84613' "; // เจ้าพนักงานศูนย์เยาวชน (ผู้นำกิจกรรมพลศึกษา)
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='48004' WHERE PL_CODE='84614' "; // เจ้าพนักงานศูนย์เยาวชน (ผู้นำกิจกรรมศิลปะ)
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='48004' WHERE PL_CODE='84615' "; // เจ้าพนักงานศูนย์เยาวชน (ผู้นำกิจกรรมคหกรรมศาสตร์))
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='48004' WHERE PL_CODE='84616' "; // เจ้าพนักงานศูนย์เยาวชน (ผู้นำกิจกรรมนาฏศิลป์)
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='48004' WHERE PL_CODE='84617' "; // เจ้าพนักงานศูนย์เยาวชน (ผู้นำกิจกรรมห้องสมุด)
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='48004' WHERE PL_CODE='84635' AND CL_NAME IN ('5', '6', 'ชำนาญงาน') "; // เจ้าหน้าที่บริหารงานศูนย์เยาวชน
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='48005' WHERE PL_CODE='81501' "; // เจ้าหน้าที่ห้องสมุด
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET CL_NAME='ปฏิบัติงาน หรือ ชำนาญงาน' WHERE CL_NAME IN ('1-3', '1-3.', '1/3', '2-4', '2-+4', '2.4', '2/4', '3-4', 'ปฏิบัติงาน') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET CL_NAME='ปฏิบัติการ หรือ ชำนาญการ' WHERE CL_NAME IN ('3-5', '3/5', '5-6', 'ปฏิบัติการ') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET CL_NAME='ชำนาญการ' WHERE CL_NAME IN ('.') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET CL_NAME='บริหารสูง' WHERE CL_NAME IS NULL AND POS_ID IN 
								(SELECT A.POS_ID FROM PER_POSITION A, PER_PERSONAL B WHERE A.POS_ID=B.POS_ID AND B.LEVEL_NO IN ('10')) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET CL_NAME='ปฏิบัติงาน หรือ ชำนาญงาน' 
								WHERE CL_NAME IN ('4') AND PL_CODE IN ('41001', '41002', '41003', '41004', '41005', '41006', '42001', '42002', '42003', '43001', '43002', '43003', '44001', 
								'45001', '46001', '46002', '46003', '46004', '46005', '46006', '46007', '46008', '46009', '46010', '46011', '47001', '47002', '47003', '47004', '47005', '47006', '47007', 
								'47008', '47009', '47010', '47011', '48001', '48002', '48003', '48004', '48005') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET CL_NAME='ชำนาญงาน' 
								WHERE CL_NAME IN ('6') AND PL_CODE IN ('41001', '41002', '41003', '41004', '41005', '41006', '42001', '42002', '42003', '43001', '43002', '43003', '44001', 
								'45001', '46001', '46002', '46003', '46004', '46005', '46006', '46007', '46008', '46009', '46010', '46011', '47001', '47002', '47003', '47004', '47005', '47006', '47007', 
								'47008', '47009', '47010', '47011', '48001', '48002', '48003', '48004', '48005') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET CL_NAME='ชำนาญการ' 
								WHERE CL_NAME IN ('6') AND PL_CODE IN ('31001', '31002', '31003', '31004', '31005', '31006', '31007', '31008', '31009', '31010', '31011', '31012', '31013', 
								'32001', '32002', '32003', '32004', '32005', '32006', '33001', '33002', '34001', '35001', '36001', '36002', '36003', '36004', '36005', '36006', '36007', '36008', '36009', 
								'36010', '36011', '36012', '36013', '36014', '36015', '37001', '37002', '37003', '37004', '37005', '37006', '37007', '37008', '37009', '37010', '37011', '37012', '38001', 
								'38002', '38003', '38004', '38005', '38006', '38007', '38008', '38009', '38010', '38011', '38012', '38013', '38014') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET CL_NAME='ชำนาญการ หรือ ชำนาญการพิเศษ' 
								WHERE CL_NAME IN ('7') AND PL_CODE IN ('31001', '31002', '31003', '31004', '31005', '31006', '31007', '31008', '31009', '31010', '31011', '31012', '31013', 
								'32001', '32002', '32003', '32004', '32005', '32006', '33001', '33002', '34001', '35001', '36001', '36002', '36003', '36004', '36005', '36006', '36007', '36008', '36009', 
								'36010', '36011', '36012', '36013', '36014', '36015', '37001', '37002', '37003', '37004', '37005', '37006', '37007', '37008', '37009', '37010', '37011', '37012', '38001', 
								'38002', '38003', '38004', '38005', '38006', '38007', '38008', '38009', '38010', '38011', '38012', '38013', '38014', '43001', '43002', '46001', '47004', '47006', '47008', '48001') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET CL_NAME='ปฏิบัติการ หรือ ชำนาญการ หรือ ชำนาญการพิเศษ หรือ เชี่ยวชาญ' 
								WHERE CL_NAME IN ('4-6') AND PL_CODE IN ('36003', '36007') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET CL_NAME='ปฏิบัติงาน หรือ ชำนาญงาน' 
								WHERE CL_NAME IS NULL AND POS_ID IN (SELECT POS_ID FROM PER_PERSONAL WHERE LEVEL_NO IN ('O1', 'O2')) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET CL_NAME='อาวุโส' 
								WHERE CL_NAME IS NULL AND POS_ID IN (SELECT POS_ID FROM PER_PERSONAL WHERE LEVEL_NO IN ('O3')) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET CL_NAME='ปฏิบัติการ หรือ ชำนาญการ' 
								WHERE CL_NAME IS NULL AND POS_ID IN (SELECT POS_ID FROM PER_PERSONAL WHERE LEVEL_NO IN ('K1', 'K2')) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET CL_NAME='ชำนาญการ หรือ ชำนาญการพิเศษ' 
								WHERE CL_NAME IS NULL AND POS_ID IN (SELECT POS_ID FROM PER_PERSONAL WHERE LEVEL_NO IN ('K3')) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET CL_NAME='เชี่ยวชาญ' 
								WHERE CL_NAME IS NULL AND POS_ID IN (SELECT POS_ID FROM PER_PERSONAL WHERE LEVEL_NO IN ('K4')) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET CL_NAME='ทรงคุณวุฒิ' 
								WHERE CL_NAME IS NULL AND POS_ID IN (SELECT POS_ID FROM PER_PERSONAL WHERE LEVEL_NO IN ('K5')) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='32004' WHERE PL_CODE='42003' AND CL_NAME='ชำนาญการ' "; // นักวิชาการจัดเก็บรายได้
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='32003' WHERE PL_CODE='42001' AND CL_NAME='ชำนาญการ' "; // นักวิชาการเงินและบัญชี
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='21001' WHERE PL_CODE='31004' AND CL_NAME='อำนวยการสูง' "; // ผู้อำนวยการ
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION SET PL_CODE='41006' WHERE PL_CODE='31006' AND CL_NAME='ปฏิบัติงาน หรือ ชำนาญงาน' "; // เจ้าหน้าที่ปกครอง
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

			}
*/
			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_PERFORMANCE_GOALS ALTER KPI_NAME MEMO ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PERFORMANCE_GOALS MODIFY KPI_NAME VARCHAR2(2000) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_PERFORMANCE_GOALS MODIFY KPI_NAME TEXT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DELETE FROM PER_KPI_COMPETENCE WHERE CP_CODE NOT IN (SELECT DISTINCT CP_CODE FROM PER_COMPETENCE) ";
			$db_dpis1->send_cmd($cmd);
			//$db_dpis1->show_error();

			add_field("PER_DECORATEHIS", "DEH_ISSUE","VARCHAR", "100", "NULL");
			add_field("PER_DECORATEHIS", "DEH_BOOK","VARCHAR", "100", "NULL");
			add_field("PER_DECORATEHIS", "DEH_PART","VARCHAR", "100", "NULL");
			add_field("PER_DECORATEHIS", "DEH_PAGE","VARCHAR", "100", "NULL");
			add_field("PER_DECORATEHIS", "DEH_ORDER_DECOR","VARCHAR", "100", "NULL");

			$cmd = " SELECT PER_ID FROM PER_PERSONAL WHERE PER_STATUS = 2 AND (LENGTH(TRIM(PER_POSDATE)) < 10 OR PER_POSDATE IS NULL) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			while($data = $db_dpis->get_array()){
				$PER_ID = $data[PER_ID];

				$cmd = " SELECT POH_EFFECTIVEDATE FROM PER_POSITIONHIS a, PER_MOVMENT b 
								WHERE PER_ID = $PER_ID AND a.MOV_CODE = b.MOV_CODE AND MOV_SUB_TYPE = 9
								ORDER BY POH_EFFECTIVEDATE DESC ";
				$count_data = $db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
				if ($count_data) {
					$data1 = $db_dpis1->get_array();
					$POH_EFFECTIVEDATE = $data1[POH_EFFECTIVEDATE];
					$cmd = " UPDATE  PER_PERSONAL SET PER_POSDATE = '$POH_EFFECTIVEDATE' WHERE PER_ID = $PER_ID ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
				} // end if
			} // end while

			add_field("PER_ORG", "DT_CODE","VARCHAR", "10", "NULL");
			add_field("PER_ORG_ASS", "DT_CODE","VARCHAR", "10", "NULL");

			if ($BKK_FLAG==1) {
/*				$cmd = " UPDATE  PER_PERFORMANCE_REVIEW SET PFR_TYPE = '1' WHERE PFR_ID IN (1100, 1200, 1300, 1400, 1500) ";
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();

				$cmd = " UPDATE  PER_PERFORMANCE_REVIEW SET PFR_TYPE = '2' WHERE PFR_ID_REF IN 
								(SELECT PFR_ID FROM PER_PERFORMANCE_REVIEW WHERE PFR_TYPE = '1') ";
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();

				$cmd = " UPDATE  PER_PERFORMANCE_REVIEW SET PFR_TYPE = '3' WHERE PFR_ID_REF IN 
								(SELECT PFR_ID FROM PER_PERFORMANCE_REVIEW WHERE PFR_TYPE = '2') ";
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
*/
			}
	
			$cmd = " update PER_CONTROL set CTRL_ALTER = 13 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
?>