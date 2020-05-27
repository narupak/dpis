<?	
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include("../php_scripts/function_share.php");
	
	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$header_width[0] = 10; $header_text[0] = "ลำดับที่"; 			
	$header_width[1] = 15; $header_text[1] = "รหัสการประเมินสมรรถนะหลักทางการบริหาร";							//	CA_ID;
	$header_width[2] = 10; $header_text[2] = "รหัสหลักสูตร";		// for $CA_COURSE
	$header_width[3] = 10; $header_text[3] = "รหัสหน่วยงาน";		// for $ORG_CODE
	$header_width[4] = 10; $header_text[4] = "ลำดับ";		// for $CA_SEQ
	$header_width[5] = 10; $header_text[5] = "รหัสการประเมิน";		// for $CA_CODE
	$header_width[6] = 10; $header_text[6] = "ประเภทบุคคล"; 						// for $CA_TYPE
	$header_width[7] = 15; $header_text[7] = "รหัสข้าราชการ";							//	PER_ID;
	$header_width[8] = 15; $header_text[8] = "วันที่ประเมิน";		// for $CA_TEST_DATE
	$header_width[9] = 15; $header_text[9] = "วันที่ขึ้นทะเบียน";	// for $CA_APPROVE_DATE
	$header_width[10] = 10; $header_text[10] = "คำนำหน้าชื่อ";	// for $PN_CODE
	$header_width[11] = 20; $header_text[11] = "ชื่อ";		// CA_NAME;
	$header_width[12] = 20; $header_text[12] = "นามสกุล";			// CA_SURNAME;
	$header_width[13] = 20; $header_text[13] = "เลขประจำตัวประชาชน";							// for $CA_CARDNO
	$header_width[14] = 15; $header_text[14] = "ความสอดคล้องของคะแนน";				// for $CA_CONSISTENCY
	$header_width[15] = 15; $header_text[15] = "การบริหารการเปลี่ยนแปลง";	// for $CA_SCORE_1
	$header_width[16] = 15; $header_text[16] = "การมีจิตมุ่งบริการ";			// for $CA_SCORE_2
	$header_width[17] = 15; $header_text[17] = "การวางแผนเชิงกลยุทธ์";				// for $CA_SCORE_3
	$header_width[18] = 15; $header_text[18] = "การตัดสินใจ";	// for $CA_SCORE_4
	$header_width[19] = 15; $header_text[19] = "การคิดเชิงกลยุทธ์";					// for $CA_SCORE_5
	$header_width[20] = 15; $header_text[20] = "ความเป็นผู้นำ";	// for $CA_SCORE_6
	$header_width[21] = 15; $header_text[21] = "การปรับตัวและความยืดหยุ่น";			// for $CA_SCORE_7
	$header_width[22] = 15; $header_text[22] = "ความสามารถและทักษะในการสื่อสาร";	// for $CA_SCORE_8
	$header_width[23] = 15; $header_text[23] = "การประสานสัมพันธ์";			 		// for $CA_SCORE_9
	$header_width[24] = 15; $header_text[24] = "การรับผิดชอบตรวจสอบได้";			 		// for $CA_SCORE_10
	$header_width[25] = 15; $header_text[25] = "การทำงานให้บรรลุผลสัมฤทธิ์";			// for $CA_SCORE_11
	$header_width[26] = 15; $header_text[26] = "การบริหารทรัพยากร";					// for $CA_SCORE_12
	$header_width[27] = 15; $header_text[27] = "ค่าเฉลี่ย";				// for $CA_MEAN
	$header_width[28] = 20; $header_text[28] = "$MINISTRY_TITLE";				// for $CA_MINISTRY_NAME
	$header_width[29] = 30; $header_text[29] = "$DEPARTMENT_TITLE";	// for $CA_DEPARTMENT_NAME
	$header_width[30] = 20; $header_text[30] = "$ORG_TITLE";			// for $CA_ORG_NAME
	$header_width[31] = 20; $header_text[31] = "$ORG_TITLE1";				// for $CA_ORG_NAME1
	$header_width[32] = 20; $header_text[32] = "$ORG_TITLE2";	// for $CA_ORG_NAME2
	$header_width[33] = 30; $header_text[33] = "$PL_TITLE";					// for $CA_LINE
	$header_width[34] = 15; $header_text[34] = "$LEVEL_TITLE";	// for $LEVEL_NO
	$header_width[35] = 20; $header_text[35] = "$PM_TITLE";			// for $CA_MGT
	$header_width[36] = 20; $header_text[36] = "ตำแหน่ง";	// for $CA_POSITION
	$header_width[37] = 10; $header_text[37] = "$POS_NO_TITLE";			 		// for $CA_POS_NO
	$header_width[38] = 15; $header_text[38] = "วันที่ออกคำสั่ง";			 		// for $CA_DOC_DATE
	$header_width[39] = 15; $header_text[39] = "เลขที่คำสั่ง"; 	// for $CA_DOC_NO
	$header_width[40] = 15; $header_text[40] = "การมุ่งผลสัมฤทธิ์";	// for $CA_NEW_SCORE_1
	$header_width[41] = 15; $header_text[41] = "บริการที่ดี";			// for $CA_NEW_SCORE_2
	$header_width[42] = 15; $header_text[42] = "การสั่งสมความเชี่ยวชาญในงานอาชีพ";				// for $CA_NEW_SCORE_3
	$header_width[43] = 15; $header_text[43] = "การยึดมั่นในความถูกต้องชอบธรรม และจริยธรรม";	// for $CA_NEW_SCORE_4
	$header_width[44] = 15; $header_text[44] = "การทำงานเป็นทีม";					// for $CA_NEW_SCORE_5
	$header_width[45] = 15; $header_text[45] = "สภาวะผู้นำ";	// for $CA_NEW_SCORE_6
	$header_width[46] = 15; $header_text[46] = "วิสัยทัศน์";			// for $CA_NEW_SCORE_7
	$header_width[47] = 15; $header_text[47] = "การวางกลยุทธ์ภาครัฐ";	// for $CA_NEW_SCORE_8
	$header_width[48] = 15; $header_text[48] = "ศักยภาพเพื่อนำการปรับเปลี่ยน";			 		// for $CA_NEW_SCORE_9
	$header_width[49] = 15; $header_text[49] = "การควบคุมตนเอง";			 		// for $CA_NEW_SCORE_10
	$header_width[50] = 15; $header_text[50] = "การสอนงานและการมอบหมายงาน";			// for $CA_NEW_SCORE_11
	$header_width[51] = 15; $header_text[51] = "ค่าเฉลี่ย";				// for $CA_NEW_MEAN
	$header_width[52] = 20; $header_text[52] = "หมายเหตุ";		// for $CA_REMARK
	$header_width[53] = 20; $header_text[53] = "รหัสผู้ใช้ที่เปลี่ยนแปลงข้อมูล";	//	UPDATE_USER;
	$header_width[54] = 20; $header_text[54] = "วันเวลา เปลี่ยนแปลงข้อมูล";				//	UPDATE_DATE;

	require_once("excel_headpart_subrtn.php");
	

	$cmd = " select CA_ID, CA_COURSE, ORG_CODE, CA_SEQ, CA_CODE, CA_TYPE, PER_ID, CA_TEST_DATE, CA_APPROVE_DATE, PN_CODE, 
							CA_NAME, CA_SURNAME, CA_CARDNO, CA_CONSISTENCY, CA_SCORE_1, CA_SCORE_2, CA_SCORE_3, CA_SCORE_4, 
							CA_SCORE_5, CA_SCORE_6, CA_SCORE_7, CA_SCORE_8, CA_SCORE_9, CA_SCORE_10, CA_SCORE_11, CA_SCORE_12, 
							CA_MEAN, CA_MINISTRY_NAME, CA_DEPARTMENT_NAME, CA_ORG_NAME, CA_ORG_NAME1, CA_ORG_NAME2, CA_LINE, 
							LEVEL_NO, CA_MGT, CA_POSITION, CA_POS_NO, CA_DOC_DATE, CA_DOC_NO, CA_NEW_SCORE_1, CA_NEW_SCORE_2, 
							CA_NEW_SCORE_3, CA_NEW_SCORE_4, CA_NEW_SCORE_5, CA_NEW_SCORE_6, CA_NEW_SCORE_7, CA_NEW_SCORE_8, 
							CA_NEW_SCORE_9, CA_NEW_SCORE_10, CA_NEW_SCORE_11, CA_NEW_MEAN, CA_REMARK, UPDATE_USER, UPDATE_DATE
						from		PER_MGT_COMPETENCY_ASSESSMENT 
						where CA_ID >= 9000
						order by CA_ID ";
//						where CA_ID < 3000
//						where CA_ID >= 3000 and CA_ID < 6000
//						where CA_ID >= 6000 and CA_ID < 9000
//						where CA_ID >= 9000
//	echo "[$cmd]<br><br>";
	$count_data = $db_dpis->send_cmd($cmd);

	$cnt = 0;
	$file_limit = 4000;
	$data_limit = 400;
	$xlsRow = 0;
	$count_org_ref = 0;
	$sheet_no = 0; $sheet_no_text="";

	$report_code = "xls_MGT_COMPETENCY";

//	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/$report_code";
	$fname1 = $fname.".xls";
	$fnum = 0; $fnum_text="";

	$arr_file = (array) null;
	$f_new = false;

	$workbook = new writeexcel_workbook($fname1);

//	echo "$data_count>>fname=$fname1<br>";

	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.set_format.php";	// use set_format(set of parameter) funtion , help is in file
	require "../../Excel/my.defined_format.inc.format_param.php";	// define format parameter
	//====================== SET FORMAT ======================//

	$worksheet = &$workbook->addworksheet("$report_code".$fnum_text.$sheet_no_text);
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);

	$data_count = 0;
	$head = "การถ่ายโอนการประเมินสมรรถนะหลักทางการบริหาร";
	call_header($data_count, $head);

	while ($data = $db_dpis->get_array()) {
		// เช็คจบแต่ละ file ตาม $file_limit
		if ($data_count > 0 && ($data_count % $file_limit) == 0) {
//			echo "***** close>>fname=$fname1<br>";
			$workbook->close();
			$arr_file[] = $fname1;

			$fnum++; $fnum_text="_$fnum";
			$fname1=$fname.$fnum_text.".xls";
//			echo "$data_count>>fname=$fname1<br>";
			$workbook = new writeexcel_workbook($fname1);

			//====================== SET FORMAT ======================//
			require "../../Excel/my.defined_format.inc.format_param.php";	// define format parameter
			//====================== SET FORMAT ======================//

			$f_new = true;
		};
		// เช็คจบที่ข้อมูลแต่ละ sheet ตาม $data_limit
		if(($data_count > 0 && ($data_count % $data_limit) == 0) || $f_new){
			if ($f_new) {
				$sheet_no=0; $sheet_no_text="";
				$f_new = false;
			} else if ($data_count > 0 && ($data_count % $data_limit) == 0) {
				$sheet_no++;
				$sheet_no_text = "_$sheet_no";
			}

			$worksheet = &$workbook->addworksheet("$report_code".$fnum_text.$sheet_no_text);
			$worksheet->set_margin_right(0.50);
			$worksheet->set_margin_bottom(1.10);

//			echo "$data_count>>sheet name=$report_code$fnum_text$sheet_no_text<br>";
			
			call_header($data_count, $head);
		}
		
		$seq_no++;
		$xlsRow++;
		
		$worksheet->write($xlsRow, 0, "$seq_no ก่อน", set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 1, $data[CA_ID], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 2, $data[CA_COURSE], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 3, $data[ORG_CODE], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 4, $data[CA_SEQ], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 5, $data[CA_CODE], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 6, $data[CA_TYPE], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 7, $data[PER_ID], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 8, $data[CA_TEST_DATE], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 9, $data[CA_APPROVE_DATE], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 10, $data[PN_CODE], set_format("xlsFmtTitle", "", "C", "LR", 0));	
		$worksheet->write($xlsRow, 11, $data[CA_NAME], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 12, $data[CA_SURNAME], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 13, $data[CA_CARDNO], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 14, $data[CA_CONSISTENCY], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 15, $data[CA_SCORE_1], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 16, $data[CA_SCORE_2], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 17, $data[CA_SCORE_3], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 18, $data[CA_SCORE_4], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 19, $data[CA_SCORE_5], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 20, $data[CA_SCORE_6], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 21, $data[CA_SCORE_7], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 22, $data[CA_SCORE_8], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 23, $data[CA_SCORE_9], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 24, $data[CA_SCORE_10], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 25, $data[CA_SCORE_11], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 26, $data[CA_SCORE_12], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 27, $data[CA_MEAN], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 28, $data[CA_MINISTRY_NAME], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 29, $data[CA_DEPARTMENT_NAME], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 30, $data[CA_ORG_NAME], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 31, $data[CA_ORG_NAME1], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 32, $data[CA_ORG_NAME2], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 33, $data[CA_LINE], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 34, $data[LEVEL_NO], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 35, $data[CA_MGT], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 36, $data[CA_POSITION], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 37, $data[CA_POS_NO], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 38, $data[CA_DOC_DATE], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 39, $data[CA_DOC_NO], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 40, $data[CA_NEW_SCORE_1], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 41, $data[CA_NEW_SCORE_2], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 42, $data[CA_NEW_SCORE_3], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 43, $data[CA_NEW_SCORE_4], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 44, $data[CA_NEW_SCORE_5], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 45, $data[CA_NEW_SCORE_6], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 46, $data[CA_NEW_SCORE_7], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 47, $data[CA_NEW_SCORE_8], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 48, $data[CA_NEW_SCORE_9], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 49, $data[CA_NEW_SCORE_10], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 50, $data[CA_NEW_SCORE_11], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 51, $data[CA_NEW_MEAN], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 52, $data[CA_REMARK], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 53, $data[UPDATE_USER], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 54, $data[UPDATE_DATE], set_format("xlsFmtTitle", "", "C", "LR", 0));
		
		$xlsRow++;
		
		$worksheet->write($xlsRow, 0, "$seq_no หลัง", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 1, $data[CA_ID], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 2, $data[CA_COURSE], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 3, $data[ORG_CODE], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 4, $data[CA_SEQ], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 5, $data[CA_CODE], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 6, $data[CA_TYPE], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 7, $data[PER_ID], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 8, $data[CA_TEST_DATE], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 9, $data[CA_APPROVE_DATE], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 10, $data[PN_CODE], set_format("xlsFmtTitle", "B", "C", "LR", 0));	
		$worksheet->write($xlsRow, 11, $data[CA_NAME], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 12, $data[CA_SURNAME], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 13, $data[CA_CARDNO], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 14, $data[CA_CONSISTENCY], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 15, $data[CA_SCORE_1], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 16, $data[CA_SCORE_2], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 17, $data[CA_SCORE_3], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 18, $data[CA_SCORE_4], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 19, $data[CA_SCORE_5], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 20, $data[CA_SCORE_6], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 21, $data[CA_SCORE_7], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 22, $data[CA_SCORE_8], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 23, $data[CA_SCORE_9], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 24, $data[CA_SCORE_10], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 25, $data[CA_SCORE_11], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 26, $data[CA_SCORE_12], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 27, $data[CA_MEAN], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 28, $data[CA_MINISTRY_NAME], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 29, $data[CA_DEPARTMENT_NAME], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 30, $data[CA_ORG_NAME], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 31, $data[CA_ORG_NAME1], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 32, $data[CA_ORG_NAME2], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 33, $data[CA_LINE], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 34, $data[LEVEL_NO], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 35, $data[CA_MGT], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 36, $data[CA_POSITION], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 37, $data[CA_POS_NO], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 38, $data[CA_DOC_DATE], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 39, $data[CA_DOC_NO], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 40, $data[CA_NEW_SCORE_1], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 41, $data[CA_NEW_SCORE_2], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 42, $data[CA_NEW_SCORE_3], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 43, $data[CA_NEW_SCORE_4], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 44, $data[CA_NEW_SCORE_5], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 45, $data[CA_NEW_SCORE_6], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 46, $data[CA_NEW_SCORE_7], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 47, $data[CA_NEW_SCORE_8], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 48, $data[CA_NEW_SCORE_9], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 49, $data[CA_NEW_SCORE_10], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 50, $data[CA_NEW_SCORE_11], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 51, $data[CA_NEW_MEAN], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 52, $data[CA_REMARK], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 53, $data[UPDATE_USER], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 54, $data[UPDATE_DATE], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		
		$xlsRow++;
		
		$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));	
		$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 14, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 15, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 16, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 17, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 18, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 19, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 20, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 21, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 22, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 23, "", set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 24, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 25, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 26, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 27, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 28, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 29, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 30, "", set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 31, "", set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 32, "", set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 33, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 34, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 35, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 36, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 37, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 38, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 39, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 40, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 41, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 42, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 43, "", set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 44, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 45, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 46, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 47, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 48, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 49, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 50, "", set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 51, "", set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 52, "", set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 53, "", set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 54, "", set_format("xlsFmtTitle", "B", "L", "LR", 0));
		
		$data_count++;
	} // end while loop
	if ($xlsRow==2) {
		$xlsRow++;
		$worksheet->write($xlsRow, 0, "***** ไม่พบข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 0));
	}

	$xlsRow++;
	$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTitle", "B", "C", "T", 0));
	$xlsRow++;
	$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTitle", "B", "C", "", 0));
	$xlsRow++;
	$worksheet->write($xlsRow, 0, "จำนวนข้อมูล $count_data รายการ", set_format("xlsFmtTitle", "B", "L", "", 0));

	$workbook->close();

	$arr_file[] = $fname1;
	
	require_once("excel_tailpart_subrtn.php");
?>