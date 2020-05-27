<?	
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$header_width[0] = 10; $header_text[0] = "ลำดับที่"; 			
	$header_width[1] = 15; $header_text[1] = "รหัสประวัติการอบรม";			 			//	TRN_ID;
	$header_width[2] = 12; $header_text[2] = "รหัสข้าราชการ";							//	PER_ID;
	$header_width[3] = 10; $header_text[3] = "ประเภท";					//	TR_TYPE; 
	$header_width[4] = 15; $header_text[4] = "หลักสูตร";					//	TR_CODE; 
	$header_width[5] = 10; $header_text[5] = "รุ่น";							//	TRN_NO;
	$header_width[6] = 15; $header_text[6] = "วันที่เริ่มต้น";				//	TRN_STARTDATE; 
	$header_width[7] = 15; $header_text[7] = "วันที่สิ้นสุด";				//	TRN_ENDDATE;
	$header_width[8] = 30; $header_text[8] = "หน่วยงานที่จัด";			//	TRN_ORG;
	$header_width[9] = 30; $header_text[9] = "สถานที่จัด";					//	TRN_PLACE;
	$header_width[10] = 20; $header_text[10] = "ประเทศที่จัด";					//	CT_CODE;
	$header_width[11] = 30; $header_text[11] = "ชื่อทุน";						//	TRN_FUND;
	$header_width[12] = 20; $header_text[12] = "ประเทศเจ้าของทุน";	//	CT_CODE_FUND;
	$header_width[13] = 20; $header_text[13] = "รหัสผู้ใช้ที่เปลี่ยนแปลงข้อมูล";	//	UPDATE_USER;
	$header_width[14] = 20; $header_text[14] = "วันเวลา เปลี่ยนแปลงข้อมูล";				//	UPDATE_DATE;
	$header_width[15] = 15; $header_text[15] = "เลขประจำตัวประชาชน";			//	PER_CARDNO;
	$header_width[16] = 10; $header_text[16] = "จำนวนวัน";			//	TRN_DAY;
	$header_width[17] = 30; $header_text[17] = "หมายเหตุ";				//	TRN_REMARK;
	$header_width[18] = 08; $header_text[18] = "ผ่าน";					//	TRN_PASS;
	$header_width[19] = 15; $header_text[19] = "เลขที่หนังสือนำส่ง";			//	TRN_BOOK_NO;
	$header_width[20] = 15; $header_text[20] = "วันที่หนังสือนำส่ง";			//	TRN_BOOK_DATE;
	$header_width[21] = 10; $header_text[21] = "โครงการฝึกอบรม";				//	TRN_PROJECT_NAME;
	$header_width[22] = 30; $header_text[22] = "หลักสูตรอื่นๆ";		//	TRN_COURSE_NAME;
	$header_width[23] = 10; $header_text[23] = "วุฒิที่ได้รับ";			//	TRN_DEGREE_RECEIVE;
	$header_width[24] = 10; $header_text[24] = "คะแนน";				//	TRN_POINT;
	$header_width[25] = 30; $header_text[25] = "วัตถุประสงค์";					//	TRN_OBJECTIVE;
	$header_width[26] = 15; $header_text[26] = "เลขที่คำสั่ง";			//	TRN_DOCNO;
	$header_width[27] = 15; $header_text[27] = "วันที่ออกคำสั่ง";			//	TRN_DOCDATE;
	$header_width[28] = 10; $header_text[28] = "ตรวจสอบข้อมูล";				//	AUDIT_FLAG;

	require_once("excel_headpart_subrtn.php");

	$cmd = "select TRN_ID, PER_ID, TRN_TYPE, TR_CODE, TRN_NO, TRN_STARTDATE, TRN_ENDDATE, TRN_ORG, 
							TRN_PLACE, CT_CODE, TRN_FUND, CT_CODE_FUND, UPDATE_USER, UPDATE_DATE, PER_CARDNO, 
							TRN_DAY, TRN_REMARK, TRN_PASS, TRN_BOOK_NO, TRN_BOOK_DATE, TRN_PROJECT_NAME, 
							TRN_COURSE_NAME, TRN_DEGREE_RECEIVE, TRN_POINT, TRN_OBJECTIVE, TRN_DOCNO, TRN_DOCDATE, AUDIT_FLAG 
					  from PER_TRAINING  
					  where TRN_ID < 300000
					  order by TRN_ID";
	$count_data = $db_dpis->send_cmd($cmd);
//	echo "[$cmd] len=".strlen($cmd)."<br>";

	$cnt = 0;
	$file_limit = 5000;
	$data_limit = 1000;
	$xlsRow = 0;
	$count_org_ref = 0;
	$sheet_no = 0; $sheet_no_text="";

	$report_code = "xls_TRAINING";

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
	$head = "การถ่ายโอนประวัติการอบรม/ดูงาน/สัมมนา";
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

//			echo "$data_count>>sheet name=$report_code,$fnum_text,$sheet_no_text<br>";

			$worksheet = &$workbook->addworksheet("$report_code".$fnum_text.$sheet_no_text);
			$worksheet->set_margin_right(0.50);
			$worksheet->set_margin_bottom(1.10);
			
			call_header($data_count, $head);
		}

		$seq_no++;
		$xlsRow++;

		$worksheet->write($xlsRow, 0, "$seq_no ก่อน", set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 1, $data[TRN_ID], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 2, $data[PER_ID], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 3, $data[TRN_TYPE], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 4, $data[TR_CODE], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 5, $data[TRN_NO], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 6, $data[TRN_STARTDATE], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 7, $data[TRN_ENDDATE], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 8, $data[TRN_ORG], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 9, $data[TRN_PLACE], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 10, $data[CT_CODE], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 11, $data[TRN_FUND], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 12, $data[CT_CODE_FUND], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 13, $data[UPDATE_USER], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 14, $data[UPDATE_DATE], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 15, $data[PER_CARDNO], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 16, $data[TRN_DAY], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 17, $data[TRN_REMARK], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 18, $data[TRN_PASS], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 19, $data[TRN_BOOK_NO], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 20, $data[TRN_BOOK_DATE], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 21, $data[TRN_PROJECT_NAME], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 22, $data[TRN_COURSE_NAME], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 23, $data[TRN_DEGREE_RECEIVE], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 24, $data[TRN_POINT], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 25, $data[TRN_OBJECTIVE], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 26, $data[TRN_DOCNO], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 27, $data[TRN_DOCDATE], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 28, $data[AUDIT_FLAG], set_format("xlsFmtTitle", "", "L", "LR", 0));
		
		$xlsRow++;

		$worksheet->write($xlsRow, 0, "$seq_no หลัง", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 1, $data[TRN_ID], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 2, $data[PER_ID], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 3, $data[TRN_TYPE], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 4, $data[TR_CODE], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 5, $data[TRN_NO], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 6, $data[TRN_STARTDATE], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 7, $data[TRN_ENDDATE], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 8, $data[TRN_ORG], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 9, $data[TRN_PLACE], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 10, $data[CT_CODE], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 11, $data[TRN_FUND], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 12, $data[CT_CODE_FUND], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 13, $data[UPDATE_USER], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 14, $data[UPDATE_DATE], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 15, $data[PER_CARDNO], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 16, $data[TRN_DAY], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 17, $data[TRN_REMARK], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 18, $data[TRN_PASS], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 19, $data[TRN_BOOK_NO], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 20, $data[TRN_BOOK_DATE], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 21, $data[TRN_PROJECT_NAME], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 22, $data[TRN_COURSE_NAME], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 23, $data[TRN_DEGREE_RECEIVE], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 24, $data[TRN_POINT], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 25, $data[TRN_OBJECTIVE], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 26, $data[TRN_DOCNO], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 27, $data[TRN_DOCDATE], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 28, $data[AUDIT_FLAG], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		
		$xlsRow++;

		$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 14, "", set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 15, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 16, "", set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 17, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 18, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 19, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 20, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 21, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 22, "", set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 23, "", set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 24, "", set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 25, "", set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 26, "", set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 27, "", set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 28, "", set_format("xlsFmtTitle", "B", "L", "LR", 0));
		
		$data_count++;
	} // end while loop ($data)
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