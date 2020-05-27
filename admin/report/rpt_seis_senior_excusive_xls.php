<?	
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include("../php_scripts/function_share.php");
	
	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$header_width[0] = 10; $header_text[0] = "ลำดับที่"; 			
	$header_width[1] = 15; $header_text[1] = "รหัสผู้ฝึกอบรม นบส.";							//	SE_ID;
	$header_width[2] = 10; $header_text[2] = "ประเภทบุคคล"; 						// for $SE_TYPE
	$header_width[3] = 15; $header_text[3] = "รหัสข้าราชการ";							//	PER_ID;
	$header_width[4] = 15; $header_text[4] = "รหัสหลักสูตร นบส."; 	// for $SE_CODE
	$header_width[5] = 10; $header_text[5] = "รุ่น";		// for $SE_NO
	$header_width[6] = 10; $header_text[6] = "คำนำหน้าชื่อ";	// for $PN_CODE
	$header_width[7] = 20; $header_text[7] = "ชื่อ";		// SE_NAME;
	$header_width[8] = 20; $header_text[8] = "นามสกุล";			// SE_SURNAME;
	$header_width[9] = 20; $header_text[9] = "เลขประจำตัวประชาชน";							// for $SE_CARDNO
	$header_width[10] = 20; $header_text[10] = "กระทรวง";				// for $SE_MINISTRY_NAME
	$header_width[11] = 20; $header_text[11] = "กรม";	// for $SE_DEPARTMENT_NAME
	$header_width[12] = 20; $header_text[12] = "สำนัก/กอง";			// for $SE_ORG_NAME
	$header_width[13] = 20; $header_text[13] = "ต่ำกว่าสำนัก/กอง 1ระดับ";				// for $SE_ORG_NAME1
	$header_width[14] = 20; $header_text[14] = "ต่ำกว่าสำนัก/กอง 2 ระดับ";	// for $SE_ORG_NAME2
	$header_width[15] = 15; $header_text[15] = "$PL_TITLE";					// for $SE_LINE
	$header_width[16] = 15; $header_text[16] = "ระดับตำแหน่ง";	// for $LEVEL_NO
	$header_width[17] = 15; $header_text[17] = "ตำแหน่งในการบริหารงาน";			// for $SE_MGT
	$header_width[18] = 30; $header_text[18] = "ตำแหน่ง (ระหว่างอบรม)";	// for $SE_TRAIN_POSITION
	$header_width[19] = 30; $header_text[19] = "กระทรวง (ระหว่างอบรม)";			 		// for $SE_TRAIN_MINISTRY
	$header_width[20] = 30; $header_text[20] = "กรม (ระหว่างอบรม)";			 		// for $SE_TRAIN_DEPARTMENT
	$header_width[21] = 10; $header_text[21] = "ผ่าน/ไม่ผ่าน";			// for $SE_PASS
	$header_width[22] = 10; $header_text[22] = "ปีงบประมาณ";					// for $SE_YEAR
	$header_width[23] = 15; $header_text[23] = "วันเกิด";				// for $SE_BIRTHDATE
	$header_width[24] = 15; $header_text[24] = "วันที่เริ่มอบรม";		// for $SE_STARTDATE
	$header_width[25] = 15; $header_text[25] = "วันสิ้นสุดระยะเวลาอบรม";	// for $SE_ENDDATE
	$header_width[26] = 15; $header_text[26] = "โทรศัพท์";					// for $SE_TEL
	$header_width[27] = 15; $header_text[27] = "โทรสาร";	// for $SE_FAX
	$header_width[28] = 15; $header_text[28] = "มือถือ";	// for $SE_MOBILE
	$header_width[29] = 15; $header_text[29] = "อีเมล์";				// for $SE_EMAIL
	$header_width[30] = 20; $header_text[30] = "รหัสผู้ใช้ที่เปลี่ยนแปลงข้อมูล";	//	UPDATE_USER;
	$header_width[31] = 20; $header_text[31] = "วันเวลา เปลี่ยนแปลงข้อมูล";				//	UPDATE_DATE;
	$header_width[32] = 10; $header_text[32] = "ตรวจสอบข้อมูล";	// AUDIT_FLAG;

	require_once("excel_headpart_subrtn.php");
	

	$cmd = " select SE_ID, SE_TYPE, PER_ID, SE_CODE, SE_NO, PN_CODE, SE_NAME, 
						SE_SURNAME, SE_CARDNO, SE_MINISTRY_NAME, SE_DEPARTMENT_NAME, SE_ORG_NAME, 
						SE_ORG_NAME1, SE_ORG_NAME2, SE_LINE, LEVEL_NO, SE_MGT, SE_TRAIN_POSITION, 
						SE_TRAIN_MINISTRY, SE_TRAIN_DEPARTMENT, SE_PASS, SE_YEAR, SE_BIRTHDATE, 
						SE_STARTDATE, SE_ENDDATE, SE_TEL, SE_FAX, SE_MOBILE, SE_EMAIL, UPDATE_USER, UPDATE_DATE, AUDIT_FLAG
						from		PER_SENIOR_EXCUSIVE 
						where SE_ID < 5000
						order by SE_ID ";
//	echo "[$cmd]<br><br>";
	$count_data = $db_dpis->send_cmd($cmd);

	$cnt = 0;
	$file_limit = 4000;
	$data_limit = 400;
	$xlsRow = 0;
	$count_org_ref = 0;
	$sheet_no = 0; $sheet_no_text="";

	$report_code = "xls_SENIOR_EXCUSIVE";

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
	$head = "การถ่ายโอนผู้ฝึกอบรมหลักสูตรนักบริหารระดับสูง";
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
		$worksheet->write($xlsRow, 1, $data[SE_ID], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 2, $data[SE_TYPE], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 3, $data[PER_ID], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 4, $data[SE_CODE], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 5, $data[SE_NO], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 6, $data[PN_CODE], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 7, $data[SE_NAME], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 8, $data[SE_SURNAME], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 9, $data[SE_CARDNO], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 10, $data[SE_MINISTRY_NAME], set_format("xlsFmtTitle", "", "L", "LR", 0));	
		$worksheet->write($xlsRow, 11, $data[SE_DEPARTMENT_NAME], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 12, $data[SE_ORG_NAME], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 13, $data[SE_ORG_NAME1], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 14, $data[SE_ORG_NAME2], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 15, $data[SE_LINE], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 16, $data[LEVEL_NO], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 17, $data[SE_MGT], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 18, $data[SE_TRAIN_POSITION], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 19, $data[SE_TRAIN_MINISTRY], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 20, $data[SE_TRAIN_DEPARTMENT], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 21, $data[SE_PASS], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 22, $data[SE_YEAR], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 23, $data[SE_BIRTHDATE], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 24, $data[SE_STARTDATE], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 25, $data[SE_ENDDATE], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 26, $data[SE_TEL], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 27, $data[SE_FAX], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 28, $data[SE_MOBILE], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 29, $data[SE_EMAIL], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 30, $data[UPDATE_USER], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 31, $data[UPDATE_DATE], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 32, $data[AUDIT_FLAG], set_format("xlsFmtTitle", "", "C", "LR", 0));
		
		$xlsRow++;
		
		$worksheet->write($xlsRow, 0, "$seq_no หลัง", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 1, $data[SE_ID], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 2, $data[SE_TYPE], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 3, $data[PER_ID], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 4, $data[SE_CODE], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 5, $data[SE_NO], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 6, $data[PN_CODE], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 7, $data[SE_NAME], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 8, $data[SE_SURNAME], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 9, $data[SE_CARDNO], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 10, $data[SE_MINISTRY_NAME], set_format("xlsFmtTitle", "B", "L", "LR", 0));	
		$worksheet->write($xlsRow, 11, $data[SE_DEPARTMENT_NAME], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 12, $data[SE_ORG_NAME], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 13, $data[SE_ORG_NAME1], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 14, $data[SE_ORG_NAME2], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 15, $data[SE_LINE], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 16, $data[LEVEL_NO], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 17, $data[SE_MGT], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 18, $data[SE_TRAIN_POSITION], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 19, $data[SE_TRAIN_MINISTRY], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 20, $data[SE_TRAIN_DEPARTMENT], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 21, $data[SE_PASS], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 22, $data[SE_YEAR], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 23, $data[SE_BIRTHDATE], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 24, $data[SE_STARTDATE], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 25, $data[SE_ENDDATE], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 26, $data[SE_TEL], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 27, $data[SE_FAX], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 28, $data[SE_MOBILE], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 29, $data[SE_EMAIL], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 30, $data[UPDATE_USER], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 31, $data[UPDATE_DATE], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 32, $data[AUDIT_FLAG], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		
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