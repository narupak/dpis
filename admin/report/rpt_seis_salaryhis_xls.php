<?	
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$header_width[0] = 10; $header_text[0] = "ลำดับที่"; 			
	$header_width[1] = 20; $header_text[1] = "รหัสประวัติการรับเงินเดือน"; 			//	SAH_ID;
	$header_width[2] = 12; $header_text[2] = "รหัสข้าราชการ";							//	PER_ID;
	$header_width[3] = 15; $header_text[3] = "วันที่มีผล";					//	SAH_EFFECTIVEDATE; 
	$header_width[4] = 20; $header_text[4] = "ประเภทการเคลื่อนไหว";				//	MOV_CODE;; 
	$header_width[5] = 15; $header_text[5] = "อัตราเงินเดือน";			//	SAH_SALARY;
	$header_width[6] = 15; $header_text[6] = "เลขที่คำสั่ง";				//	SAH_DOCNO;
	$header_width[7] = 20; $header_text[7] = "วันที่ออกคำสั่ง";				//	SAH_DOCDATE;
	$header_width[8] = 20; $header_text[8] = "วันที่สิ้นสุด";				//	SAH_ENDDATE;
	$header_width[9] = 20; $header_text[9] = "รหัสผู้ใช้ที่เปลี่ยนแปลงข้อมูล";	//	UPDATE_USER;
	$header_width[10] = 20; $header_text[10] = "วันเวลา เปลี่ยนแปลงข้อมูล";				//	UPDATE_DATE;
	$header_width[11] = 20; $header_text[11] = "เลขประจำตัวประชาชน";			//	PER_CARDNO;
	$header_width[12] = 15; $header_text[12] = "เปอร์เซ็นต์ที่เลื่อน";				//	SAH_PERCENT_UP;
	$header_width[13] = 15; $header_text[13] = "เงินเดือนที่เลื่อน";				//	SAH_SALARY_UP;
	$header_width[14] = 15; $header_text[14] = "เงินตอบแทนพิเศษ";	//	SAH_SALARY_EXTRA;
	$header_width[15] = 10; $header_text[15] = "ลำดับที่";				//	SAH_SEQ_NO;
	$header_width[16] = 20; $header_text[16] = "หมายเหตุ";				//	SAH_REMARK;
	$header_width[17] = 10; $header_text[17] = "ระดับตำแหน่ง";		//	LEVEL_NO;
	$header_width[18] = 10; $header_text[18] = "เลขที่ตำแหน่ง";		//	SAH_POS_NO;
	$header_width[19] = 30; $header_text[19] = "ตำแหน่ง";				//	SAH_POSITION;
	$header_width[20] = 50; $header_text[20] = "สังกัด";					//	SAH_ORG;
	$header_width[21] = 10; $header_text[21] = "ประเภทเงิน";			//	EX_CODE;
	$header_width[22] = 10; $header_text[22] = "เลขถือจ่าย";			//	SAH_PAY_NO;
	$header_width[23] = 15; $header_text[23] = "ฐานในการคำนวณ";	//	SAH_SALARY_MIDPOINT;
	$header_width[24] = 10; $header_text[24] = "ปีงบประมาณ";		//	SAH_KF_YEAR;
	$header_width[25] = 10; $header_text[25] = "รอบการประเมิน";	//	SAH_KF_CYCLE = 1 or 2;
	$header_width[26] = 10; $header_text[26] = "ผลการประเมิน";		//	SAH_TOTAL_SCORE;
	$header_width[27] = 15; $header_text[27] = "เงินเดือนล่าสุด";		//	SAH_LAST_SALARY;
	$header_width[28] = 15; $header_text[28] = "จำนวนขั้นเงินเดือน";		//	SM_CODE;
	$header_width[29] = 10; $header_text[29] = "ลำดับที่คำสั่ง";		//	SAH_CMD_SEQ;
	$header_width[30] = 10; $header_text[30] = "รหัสอื่น";					//	SAH_ORG_DOPA_CODE;
	$header_width[31] = 15; $header_text[31] = "อัตราเงินเดือนเดิม";			//	SAH_OLD_SALARY;
	$header_width[32] = 10; $header_text[32] = "ชื่อเลขที่ตำแหน่ง";			//	SAH_POS_NO_NAME;
	$header_width[33] = 15; $header_text[33] = "ตรวจสอบข้อมูล";	//	AUDIT_FLAG;
	$header_width[34] = 20; $header_text[34] = "ด้านความเชี่ยวชาญ";		//	SAH_SPECIALIST;
	$header_width[35] = 20; $header_text[35] = "เอกสารอ้างอิง";	//	SAH_REF_DOC;
	$header_width[36] = 15; $header_text[36] = "แก้ไขเลขที่คำสั่ง";		//	SAH_DOCNO_EDIT;
	$header_width[37] = 15; $header_text[37] = "แก้ไขวันที่ออกคำสั่ง";		//	SAH_DOCDATE_EDIT;

	require_once("excel_headpart_subrtn.php");

	$cnt = 0;
	$file_limit = 4000;
	$data_limit = 1000;
	$xlsRow = 0;
	$count_org_ref = 0;
	$sheet_no = 0; $sheet_no_text="";

	$report_code = "xls_SALARYHIS";

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

	$cmd = " select SAH_ID, PER_ID, SAH_EFFECTIVEDATE, MOV_CODE, SAH_SALARY, SAH_DOCNO, 
							SAH_DOCDATE, 	SAH_ENDDATE, UPDATE_USER, UPDATE_DATE, PER_CARDNO, 
							SAH_PERCENT_UP, SAH_SALARY_UP, SAH_SALARY_EXTRA, SAH_SEQ_NO, SAH_REMARK, 
							LEVEL_NO, SAH_POS_NO, SAH_POSITION, SAH_ORG, EX_CODE, SAH_PAY_NO, SAH_SALARY_MIDPOINT, 
							SAH_KF_YEAR, SAH_KF_CYCLE, SAH_TOTAL_SCORE, SAH_LAST_SALARY, SM_CODE, 
							SAH_CMD_SEQ, SAH_ORG_DOPA_CODE, SAH_OLD_SALARY, SAH_POS_NO_NAME, 
							AUDIT_FLAG, SAH_SPECIALIST, SAH_REF_DOC, SAH_DOCNO_EDIT, SAH_DOCDATE_EDIT
					from	PER_SALARYHIS 
					where SAH_ID >= 1150000 and SAH_ID < 1200000
					order by SAH_ID ";
//					where SAH_ID >= 1110000 and SAH_ID < 1115000
//					where SAH_ID < 400000
//					where SAH_ID >= 400000 and SAH_ID < 800000
//					where SAH_ID >= 800000 and SAH_ID < 900000
//					where SAH_ID >= 900000 and SAH_ID < 1040000
//					where SAH_ID >= 1040000 and SAH_ID < 1100000
//					where SAH_ID >= 1100000 and SAH_ID < 1110000
//					where SAH_ID >= 1120000 and SAH_ID < 1150000
	$count_data = $db_dpis->send_cmd($cmd);
//	echo "[$cmd] len=".strlen($cmd)."<br>";

	$data_count = 0;
	$head = "การถ่ายโอนประวัติการรับเงินเดือน";
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
		$worksheet->write($xlsRow, 1, $data[SAH_ID], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 2, $data[PER_ID], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 3, $data[SAH_EFFECTIVEDATE], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 4, $data[MOVE_CODE], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 5, $data[SAH_SALARY], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 6, $data[SAH_DOCNO], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 7, $data[SAH_DOCDATE], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 8, $data[SAH_ENDDATE], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 9, $data[UPDATE_USER], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 10, $data[UPDATE_DATE], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 11, $data[PER_CARDNO], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 12, $data[SAH_PERCENT_UP], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 13, $data[SAH_SALARY_UP], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 14, $data[SAH_SALARY_EXTRA], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 15, $data[SAH_SEQ_NO], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 16, $data[SAH_REMARK], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 17, $data[LEVEL_NO], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 18, $data[SAH_POS_NO], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 19, $data[SAH_POSITION], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 20, $data[SAH_ORG], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 21, $data[EX_CODE], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 22, $data[SAH_PAY_NO], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 23, $data[SAH_SALARY_MIDPOINT], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 24, $data[SAH_KF_YEAR], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 25, $data[SAH_KF_CYCLE], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 26, $data[SAH_TOTAL_SCORE], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 27, $data[SAH_LAST_SALARY], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 28, $data[SM_CODE], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 29, $data[SAH_CMD_SEQ], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 30, $data[SAH_ORG_DOPA_CODE], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 31, $data[SAH_OLD_SALARY], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 32, $data[SAH_POS_NO_NAME], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 33, $data[AUDIT_FLAG], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 34, $data[SAH_SPECIALIST], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 35, $data[SAH_REF_DOC], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 36, $data[SAH_DOCNO_EDIT], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 37, $data[SAH_DOCDATE_EDIT], set_format("xlsFmtTitle", "", "C", "LR", 0));
		
		$xlsRow++;
		
		$worksheet->write($xlsRow, 0, "$seq_no หลัง", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 1, $data[SAH_ID], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 2, $data[PER_ID], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 3, $data[SAH_EFFECTIVEDATE], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 4, $data[MOVE_CODE], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 5, $data[SAH_SALARY], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 6, $data[SAH_DOCNO], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 7, $data[SAH_DOCDATE], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 8, $data[SAH_ENDDATE], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 9, $data[UPDATE_USER], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 10, $data[UPDATE_DATE], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 11, $data[PER_CARDNO], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 12, $data[SAH_PERCENT_UP], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 13, $data[SAH_SALARY_UP], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 14, $data[SAH_SALARY_EXTRA], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 15, $data[SAH_SEQ_NO], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 16, $data[SAH_REMARK], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 17, $data[LEVEL_NO], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 18, $data[SAH_POS_NO], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 19, $data[SAH_POSITION], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 20, $data[SAH_ORG], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 21, $data[EX_CODE], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 22, $data[SAH_PAY_NO], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 23, $data[SAH_SALARY_MIDPOINT], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 24, $data[SAH_KF_YEAR], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 25, $data[SAH_KF_CYCLE], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 26, $data[SAH_TOTAL_SCORE], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 27, $data[SAH_LAST_SALARY], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 28, $data[SM_CODE], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 29, $data[SAH_CMD_SEQ], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 30, $data[SAH_ORG_DOPA_CODE], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 31, $data[SAH_OLD_SALARY], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 32, $data[SAH_POS_NO_NAME], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 33, $data[AUDIT_FLAG], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 34, $data[SAH_SPECIALIST], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 35, $data[SAH_REF_DOC], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 36, $data[SAH_DOCNO_EDIT], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 37, $data[SAH_DOCDATE_EDIT], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		
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
		$worksheet->write($xlsRow, 33, "", set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 34, "", set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 35, "", set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 36, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 37, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		
		$data_count++;
	} // end while loop ($data)
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