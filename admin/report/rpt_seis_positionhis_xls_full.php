<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$header_width[0] = 10; $header_text[0] = "ลำดับที่"; 			
	$header_width[1] = 20; $header_text[1] = "รหัสประวัติการดำรงตำแหน่ง";		// POH_ID;
	$header_width[2] = 15; $header_text[2] = "รหัสข้าราชการ";									// PER_ID
	$header_width[3] = 15; $header_text[3] = "วันที่เข้าดำรงตำแหน่ง";							// POH_EFFECTIVEDATE;
	$header_width[4] = 15; $header_text[4] = "ประเภทการเคลื่อนไหว";	// MOV_CODE;
	$header_width[5] = 15; $header_text[5] = "วันที่สิ้นสุดการดำรงตำแหน่ง";			// POH_ENDDATE;
	$header_width[6] = 15; $header_text[6] = "เลขที่คำสั่ง";						// POH_DOCNO;
	$header_width[7] = 15; $header_text[7] = "วันที่ออกคำสั่ง";						// POH_DOCDATE;
	$header_width[8] = 10; $header_text[8] = "เลขที่ตำแหน่ง";					// POH_POS_NO;
	$header_width[9] = 15; $header_text[9] = "ตำแหน่งในการบริหารงาน";		// PM_CODE;
	$header_width[10] = 15; $header_text[10] = "ระดับตำแหน่ง";					// LEVEL_NO;
	$header_width[11] = 15; $header_text[11] = "$PL_TITLE";			// PL_CODE;
	$header_width[12] = 15; $header_text[12] = "ตำแหน่งลูกจ้างประจำ";					// PN_CODE;
	$header_width[13] = 15; $header_text[13] = "ประเภทตำแหน่ง";			// PT_CODE;
	$header_width[14] = 10; $header_text[14] = "ประเทศ";						// CT_CODE;
	$header_width[15] = 10; $header_text[15] = "จังหวัด";						// PV_CODE;
	$header_width[16] = 10; $header_text[16] = "อำเภอ";							// AP_CODE;
	$header_width[17] = 15; $header_text[17] = "ตำแหน่งบริหารภายใน";	// POH_ORGMGT 1=ใช่  2=ไม่ใช่;
	$header_width[18] = 10; $header_text[18] = "$MINISTRY_TITLE";						// ORG_ID_1;
	$header_width[19] = 10; $header_text[19] = "$DEPARTMENT_TITLE";							// ORG_ID_2;
	$header_width[20] = 10; $header_text[20] = "$ORG_TITLE";					// ORG_ID_3;
	$header_width[21] = 30; $header_text[21] = "$ORG_TITLE1";	// POH_UNDER_ORG1;
	$header_width[22] = 30; $header_text[22] = "$ORG_TITLE2";	// POH_UNDER_ORG2;
	$header_width[23] = 20; $header_text[23] = "$ORG_TITLE ตามมอบหมายงาน";	// POH_ASS_ORG;
	$header_width[24] = 20; $header_text[24] = "$ORG_TITLE1 ตามมอบหมายงาน";	// POH_ASS_ORG1;
	$header_width[25] = 20; $header_text[25] = "$ORG_TITLE2 ตามมอบหมายงาน";	// POH_ASS_ORG2;
	$header_width[26] = 15; $header_text[26] = "อัตราเงินเดือน";				// POH_SALARY;
	$header_width[27] = 15; $header_text[27] = "เงินประจำตำแหน่ง";		//	POH_SALARY_POS;
	$header_width[28] = 20; $header_text[28] = "หมายเหตุ";						//	POH_REMARK;
	$header_width[29] = 20; $header_text[29] = "รหัสผู้ใช้ที่เปลี่ยนแปลงข้อมูล";						//	UPDATE_USER;
	$header_width[30] = 20; $header_text[30] = "วันเวลา เปลี่ยนแปลงข้อมูล";						//	UPDATE_DATE;
	$header_width[31] = 20; $header_text[31] = "เลขประจำตัวประชาชน";	// PER_CARDNO;
	$header_width[32] = 20; $header_text[32] = "ตำแหน่งพนักงานราชการ";				//	EP_CODE;
	$header_width[33] = 20; $header_text[33] = "$MINISTRY_TITLE";					//	POH_ORG1;
	$header_width[34] = 20; $header_text[34] = "$DEPARTMENT_TITLE";						//	POH_ORG2;
	$header_width[35] = 30; $header_text[35] = "$ORG_TITLE";				//	POH_ORG3;
	$header_width[36] = 20; $header_text[36] = "ส่วนราชการที่รับโอน/ให้โอน";	//	POH_ORG_TRANSFER;
	$header_width[37] = 30; $header_text[37] = "ข้อมูลเดิม (ก่อนถ่ายโอน)";	//	POH_ORG;
	$header_width[38] = 20; $header_text[38] = "ชื่อตำแหน่งในการบริหาร";	//	POH_PM_NAME;
	$header_width[39] = 30; $header_text[39] = "ชื่อตำแหน่งในสายงาน";		//	POH_PL_NAME;
	$header_width[40] = 10; $header_text[40] = "ลำดับที่คำสั่ง";							//	POH_SEQ_NO;
	$header_width[41] = 15; $header_text[41] = "ดำรงตำแหน่งล่าสุด";			//	POH_LAST_POSITION;
	$header_width[42] = 10; $header_text[42] = "ลำดับที่คำสั่ง";						//	POH_CMD_SEQ;
	$header_width[43] = 10; $header_text[43] = "ดำรงตำแหน่ง";					//	POH_ISREAL Y=จริง;
	$header_width[44] = 10; $header_text[44] = "รหัสอื่น";			//	POH_ORG_DOPA_CODE;
	$header_width[45] = 15; $header_text[45] = "สถานะการดำรงตำแหน่ง";			//	ES_CODE;
	$header_width[46] = 15; $header_text[46] = "ระดับตำแหน่ง";					// POH_LEVEL_NO;
	$header_width[47] = 15; $header_text[47] = "ตำแหน่งลูกจ้างชั่วคราว";	// TP_CODE;
	$header_width[48] = 20; $header_text[48] = "$ORG_TITLE3";	// POH_UNDER_ORG3;
	$header_width[49] = 20; $header_text[49] = "$ORG_TITLE4";	// POH_UNDER_ORG4;
	$header_width[50] = 20; $header_text[50] = "$ORG_TITLE5";	// POH_UNDER_ORG5;
	$header_width[51] = 20; $header_text[51] = "$ORG_TITLE3 ตามมอบหมายงาน";	// POH_ASS_ORG3;
	$header_width[52] = 20; $header_text[52] = "$ORG_TITLE4 ตามมอบหมายงาน";	// POH_ASS_ORG4;
	$header_width[53] = 20; $header_text[53] = "$ORG_TITLE5 ตามมอบหมายงาน";	// POH_ASS_ORG5;
	$header_width[54] = 20; $header_text[54] = "หมายเหตุ 1";						//	POH_REMARK1;
	$header_width[55] = 20; $header_text[55] = "หมายเหตุ 2";						//	POH_REMARK2;
	$header_width[56] = 20; $header_text[56] = "ชื่อเลขที่ตำแหน่ง";					// POH_POS_NO_NAME;
	$header_width[57] = 20; $header_text[57] = "แก้ไขคำสั่งเลขที่";						// POH_DOCNO_EDIT;
	$header_width[58] = 20; $header_text[58] = "แก้ไขวันที่ออกคำสั่ง";						// POH_DOCDATE_EDIT;
	$header_width[59] = 20; $header_text[59] = "ตรวจสอบข้อมูล";	// AUDIT_FLAG;
	$header_width[60] = 20; $header_text[60] = "ด้านความเชี่ยวชาญ";						//	POH_SPECIALIST;
	$header_width[61] = 20; $header_text[61] = "เอกสารอ้างอิง";						//	POH_REF_DOC;

	require_once("excel_headpart_subrtn.php");			// function call_header อยู่ในนี้

	$cmd = " SELECT POH_ID, PER_ID, POH_EFFECTIVEDATE, MOV_CODE, POH_ENDDATE, POH_DOCNO, POH_DOCDATE, 
									POH_POS_NO, PM_CODE, LEVEL_NO, PL_CODE, PN_CODE, PT_CODE, CT_CODE, PV_CODE, AP_CODE, 
									POH_ORGMGT, ORG_ID_1, ORG_ID_2, ORG_ID_3, POH_UNDER_ORG1, POH_UNDER_ORG2, POH_ASS_ORG, 
									POH_ASS_ORG1, POH_ASS_ORG2, POH_SALARY, POH_SALARY_POS, POH_REMARK, UPDATE_USER, 
									UPDATE_DATE, PER_CARDNO, EP_CODE, POH_ORG1, POH_ORG2, POH_ORG3, POH_ORG_TRANSFER, 
									POH_ORG, POH_PM_NAME, POH_PL_NAME, POH_SEQ_NO, POH_LAST_POSITION, 	POH_CMD_SEQ, POH_ISREAL, 
									POH_ORG_DOPA_CODE, ES_CODE, POH_LEVEL_NO, TP_CODE, POH_UNDER_ORG3, POH_UNDER_ORG4, 
									POH_UNDER_ORG5, POH_ASS_ORG3, POH_ASS_ORG4, POH_ASS_ORG5, POH_REMARK1, POH_REMARK2,
									POH_POS_NO_NAME, POH_DOCNO_EDIT, POH_DOCDATE_EDIT, AUDIT_FLAG, POH_SPECIALIST, POH_REF_DOC
						FROM	PER_POSITIONHIS 
						where POH_ID < 350000
						ORDER BY POH_ID ";
//						where POH_ID < 350000
//						where POH_ID >= 350000 and POH_ID < 365000
//						where POH_ID >= 365000 and POH_ID < 375000
	$count_data = $db_dpis->send_cmd($cmd);
//	echo "[$cmd]<br><br>";

	$cnt = 0;
	$file_limit = 2500;
	$data_limit = 250;
	$xlsRow = 0;
	$count_org_ref = 0;
	$sheet_no = 0; $sheet_no_text="";

	$report_code = "xls_POSITIONHIS";

//	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/$report_code";
	$fname1 = $fname.".xls";

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
	$head = "การดำรงตำแหน่ง";
	call_header($data_count, $head);

	while ($data = $db_dpis->get_array()) {

		$pname = $data[PER_NAME]." ".$data[PER_SURNAME];

		$seq_no++;
		$xlsRow++;

		$worksheet->write($xlsRow, 0, "$seq_no ก่อน", set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 1, $data[POH_ID], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 2, $data[PER_ID], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 3, $data[POH_EFFECTIVEDATE], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 4, $data[MOV_CODE], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 5, $data[POH_ENDDATE], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 6, $data[POH_DOCNO], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 7, $data[POH_DOCDATE], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 8, $data[POH_POS_NO], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 9, $data[PM_CODE], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 10, $data[LEVEL_NO], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 11, $data[PL_CODE], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 12, $data[PN_CODE], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 13, $data[PT_CODE], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 14, $data[CT_CODE], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 15, $data[PV_CODE], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 16, $data[AP_CODE], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 17, $data[POH_ORGMGT], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 18, $data[ORG_ID_1], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 19, $data[ORG_ID_2], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 20, $data[ORG_ID_3], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 21, $data[POH_UNDER_ORG1], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 22, $data[POH_UNDER_ORG2], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 23, $data[POH_ASS_ORG], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 24, $data[POH_ASS_ORG1], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 25, $data[POH_ASS_ORG2], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 26, $data[POH_SALARY], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 27, $data[POH_SALARY_POS], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 28, $data[POH_REMARK], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 29, $data[UPDATE_USER], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 30, $data[UPDATE_DATE], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 31, $data[PER_CARDNO], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 32, $data[EP_CODE], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 33, $data[POH_ORG1], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 34, $data[POH_ORG2], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 35, $data[POH_ORG3], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 36, $data[POH_ORG_TRANSFER], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 37, $data[POH_ORG], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 38, $data[POH_PM_NAME], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 39, $data[POH_PL_NAME], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 40, $data[POH_SEQ_NO], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 41, $data[POH_LAST_POSITION], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 42, $data[POH_CMD_SEQ], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 43, $data[POH_ISREAL], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 44, $data[POH_ORG_DOPA_CODE], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 45, $data[ES_CODE], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 46, $data[POH_LEVEL_NO], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 47, $data[TP_CODE], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 48, $data[POH_UNDER_ORG3], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 49, $data[POH_UNDER_ORG4], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 50, $data[POH_UNDER_ORG5], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 51, $data[POH_ASS_ORG3], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 52, $data[POH_ASS_ORG4], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 53, $data[POH_ASS_ORG5], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 54, $data[POH_REMARK1], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 55, $data[POH_REMARK2], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 56, $data[POH_POS_NO_NAME], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 57, $data[POH_DOCNO_EDIT], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 58, $data[POH_DOCDATE_EDIT], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 59, $data[AUDIT_FLAG], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 60, $data[POH_SPECIALIST], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 61, $data[POH_REF_DOC], set_format("xlsFmtTitle", "", "C", "LR", 0));
		
		$xlsRow++;

		$worksheet->write($xlsRow, 0, "$seq_no หลัง", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 1, $data[POH_ID], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 2, $data[PER_ID], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 3, $data[POH_EFFECTIVEDATE], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 4, $data[MOV_CODE], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 5, $data[POH_ENDDATE], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 6, $data[POH_DOCNO], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 7, $data[POH_DOCDATE], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 8, $data[POH_POS_NO], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 9, $data[PM_CODE], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 10, $data[LEVEL_NO], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 11, $data[PL_CODE], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 12, $data[PN_CODE], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 13, $data[PT_CODE], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 14, $data[CT_CODE], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 15, $data[PV_CODE], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 16, $data[AP_CODE], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 17, $data[POH_ORGMGT], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 18, $data[ORG_ID_1], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 19, $data[ORG_ID_2], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 20, $data[ORG_ID_3], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 21, $data[POH_UNDER_ORG1], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 22, $data[POH_UNDER_ORG2], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 23, $data[POH_ASS_ORG], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 24, $data[POH_ASS_ORG1], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 25, $data[POH_ASS_ORG2], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 26, $data[POH_SALARY], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 27, $data[POH_SALARY_POS], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 28, $data[POH_REMARK], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 29, $data[UPDATE_USER], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 30, $data[UPDATE_DATE], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 31, $data[PER_CARDNO], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 32, $data[EP_CODE], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 33, $data[POH_ORG1], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 34, $data[POH_ORG2], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 35, $data[POH_ORG3], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 36, $data[POH_ORG_TRANSFER], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 37, $data[POH_ORG], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 38, $data[POH_PM_NAME], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 39, $data[POH_PL_NAME], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 40, $data[POH_SEQ_NO], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 41, $data[POH_LAST_POSITION], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 42, $data[POH_CMD_SEQ], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 43, $data[POH_ISREAL], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 44, $data[POH_ORG_DOPA_CODE], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 45, $data[ES_CODE], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 46, $data[POH_LEVEL_NO], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 47, $data[TP_CODE], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 48, $data[POH_UNDER_ORG3], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 49, $data[POH_UNDER_ORG4], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 50, $data[POH_UNDER_ORG5], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 51, $data[POH_ASS_ORG3], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 52, $data[POH_ASS_ORG4], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 53, $data[POH_ASS_ORG5], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 54, $data[POH_REMARK1], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 55, $data[POH_REMARK2], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 56, $data[POH_POS_NO_NAME], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 57, $data[POH_DOCNO_EDIT], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 58, $data[POH_DOCDATE_EDIT], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 59, $data[AUDIT_FLAG], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 60, $data[POH_SPECIALIST], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 61, $data[POH_REF_DOC], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		
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
		$worksheet->write($xlsRow, 27, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 28, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 29, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 30, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 31, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 32, "", set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 33, "", set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 34, "", set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 35, "", set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 36, "", set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 37, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 38, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 39, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 40, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 41, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 42, "", set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 43, "", set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 44, "", set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 45, "", set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 46, "", set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 47, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 48, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 49, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 50, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 51, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 52, "", set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 53, "", set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 54, "", set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 55, "", set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 56, "", set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 57, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 58, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 59, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 60, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 61, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		
		$data_count++;
	} // end while loop ($data) 

	$xlsRow++;
	$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTitle", "B", "C", "T", 0));
	$xlsRow++;
	$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTitle", "B", "C", "", 0));
	$xlsRow++;
	$worksheet->write($xlsRow, 0, "จำนวนข้อมูล $count_data รายการ", set_format("xlsFmtTitle", "B", "L", "", 0));

	$workbook->close();

	ini_set("max_execution_time", 30);
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"$report_code.xls\"");
	header("Content-Disposition: inline; filename=\"$report_code.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>