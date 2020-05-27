<?	
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include("../php_scripts/function_share.php");
	
	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$header_width[0] = 10; $header_text[0] = "ลำดับที่"; 			
	$header_width[1] = 15; $header_text[1] = "รหัสตำแหน่งข้าราชการ";							//	POS_ID;
	$header_width[2] = 20; $header_text[2] = "$ORG_TITLE"; 						// for $ORG_ID
	$header_width[3] = 15; $header_text[3] = "$POS_NO_TITLE";							//	POS_NO;
	$header_width[4] = 15; $header_text[4] = "ประเภทข้าราชการ"; 	// for $OT_CODE
	$header_width[5] = 15; $header_text[5] = "$ORG_TITLE1";		// for $ORG_ID_1
	$header_width[6] = 15; $header_text[6] = "$ORG_TITLE2";	// for $ORG_ID_2
	$header_width[7] = 15; $header_text[7] = "$PM_TITLE";		// PM_CODE;
	$header_width[8] = 15; $header_text[8] = "$PL_TITLE";			// PL_CODE;
	$header_width[9] = 20; $header_text[9] = "$CL_TITLE";							// for $CL_NAME
	$header_width[10] = 10; $header_text[10] = "เงินเดือน";				// for $POS_SALARY
	$header_width[11] = 10; $header_text[11] = "เงินประจำตำแหน่ง";	// for $POS_MGTSALARY
	$header_width[12] = 10; $header_text[12] = "สาขาความเชี่ยวชาญ";			// for $SKILL_CODE
	$header_width[13] = 10; $header_text[13] = "$PT_TITLE";				// for $PT_CODE
	$header_width[14] = 15; $header_text[14] = "$PC_TITLE";	// for $PC_CODE
	$header_width[15] = 20; $header_text[15] = "รายละเอียดเงื่อนไขตำแหน่ง";					// for $POS_CONDITION
	$header_width[16] = 15; $header_text[16] = "เลขที่คำสั่ง";	// for $POS_DOC_NO
	$header_width[17] = 20; $header_text[17] = "$REMARK_TITLE";			// for $POS_REMARK
	$header_width[18] = 15; $header_text[18] = "วันที่กำหนดตำแหน่ง";	// for $POS_DATE
	$header_width[19] = 15; $header_text[19] = "วันที่ตำแหน่งว่าง";			 		// for $POS_VACANT_DATE
	$header_width[20] = 15; $header_text[20] = "วันที่ตำแหน่งเปลี่ยนสถานภาพ";			 		// for $POS_CHANGE_DATE
	$header_width[21] = 10; $header_text[21] = "สถานภาพ";			// for $POS_STATUS
	$header_width[22] = 20; $header_text[22] = "รหัสผู้ใช้ที่เปลี่ยนแปลงข้อมูล";	//	UPDATE_USER;
	$header_width[23] = 20; $header_text[23] = "วันเวลา เปลี่ยนแปลงข้อมูล";				//	UPDATE_DATE;
	$header_width[24] = 10; $header_text[24] = "$DEPARTMENT_TITLE";					// for $DEPARTMENT_ID
	$header_width[25] = 10; $header_text[25] = "ลำดับที่";				// for $POS_SEQ_NO
	$header_width[26] = 15; $header_text[26] = "เลขถือจ่าย";		// for $PAY_NO
	$header_width[27] = 10; $header_text[27] = "ตำแหน่งในการบริหารงานภายใน";	// for $POS_ORGMGT
	$header_width[28] = 10; $header_text[28] = "$LEVEL_TITLE";					// for $LEVEL_NO
	$header_width[29] = 15; $header_text[29] = "$ORG_TITLE3";	// for $ORG_ID_3
	$header_width[30] = 15; $header_text[30] = "$ORG_TITLE4";	// for $ORG_ID_4
	$header_width[31] = 15; $header_text[31] = "$ORG_TITLE5";				// for $ORG_ID_5
	$header_width[32] = 20; $header_text[32] = "ชื่อเลขที่ตำแหน่ง";					// POS_NO_NAME;
	$header_width[33] = 20; $header_text[33] = "ตรวจสอบข้อมูล";	// AUDIT_FLAG;

	require_once("excel_headpart_subrtn.php");
	

	$cmd = " select POS_ID, ORG_ID, POS_NO, OT_CODE, ORG_ID_1, ORG_ID_2, PM_CODE, PL_CODE, CL_NAME, POS_SALARY, 
						POS_MGTSALARY, SKILL_CODE, PT_CODE, PC_CODE, POS_CONDITION, POS_DOC_NO, POS_REMARK, POS_DATE, 
						POS_VACANT_DATE, POS_CHANGE_DATE, POS_STATUS, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID, 
						POS_SEQ_NO, PAY_NO, POS_ORGMGT, LEVEL_NO, ORG_ID_3, ORG_ID_4, ORG_ID_5, POS_NO_NAME, AUDIT_FLAG
						from		PER_POSITION 
						where POS_ID < 50000
						order by POS_ID ";
//	echo "[$cmd]<br><br>";
	$count_data = $db_dpis->send_cmd($cmd);

	$cnt = 0;
	$file_limit = 4000;
	$data_limit = 400;
	$xlsRow = 0;
	$count_org_ref = 0;
	$sheet_no = 0; $sheet_no_text="";

	$report_code = "xls_POSITION";

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
	$head = "การถ่ายโอนตำแหน่งข้าราชการ";
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
		$worksheet->write($xlsRow, 1, $data[POS_ID], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 2, $data[ORG_ID], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 3, $data[POS_NO], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 4, $data[OT_CODE], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 5, $data[ORG_ID_1], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 6, $data[ORG_ID_2], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 7, $data[PM_CODE], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 8, $data[PL_CODE], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 9, $data[CL_NAME], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 10, $data[POS_SALARY], set_format("xlsFmtTitle", "", "C", "LR", 0));	
		$worksheet->write($xlsRow, 11, $data[POS_MGTSALARY], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 12, $data[SKILL_CODE], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 13, $data[PT_CODE], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 14, $data[PC_CODE], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 15, $data[POS_CONDITION], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 16, $data[POS_DOC_NO], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 17, $data[POS_REMARK], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 18, $data[POS_DATE], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 19, $data[POS_VACANT_DATE], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 20, $data[POS_CHANGE_DATE], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 21, $data[POS_STATUS], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 22, $data[UPDATE_USER], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 23, $data[UPDATE_DATE], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 24, $data[DEPARTMENT_ID], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 25, $data[POS_SEQ_NO], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 26, $data[PAY_NO], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 27, $data[POS_ORGMGT], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 28, $data[LEVEL_NO], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 29, $data[ORG_ID_3], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 30, $data[ORG_ID_4], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 31, $data[ORG_ID_5], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 32, $data[POS_NO_NAME], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 33, $data[AUDIT_FLAG], set_format("xlsFmtTitle", "", "C", "LR", 0));
		
		$xlsRow++;
		
		$worksheet->write($xlsRow, 0, "$seq_no หลัง", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 1, $data[POS_ID], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 2, $data[ORG_ID], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 3, $data[POS_NO], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 4, $data[OT_CODE], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 5, $data[ORG_ID_1], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 6, $data[ORG_ID_2], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 7, $data[PM_CODE], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 8, $data[PL_CODE], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 9, $data[CL_NAME], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 10, $data[POS_SALARY], set_format("xlsFmtTitle", "B", "C", "LR", 0));	
		$worksheet->write($xlsRow, 11, $data[POS_MGTSALARY], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 12, $data[SKILL_CODE], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 13, $data[PT_CODE], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 14, $data[PC_CODE], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 15, $data[POS_CONDITION], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 16, $data[POS_DOC_NO], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 17, $data[POS_REMARK], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 18, $data[POS_DATE], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 19, $data[POS_VACANT_DATE], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 20, $data[POS_CHANGE_DATE], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 21, $data[POS_STATUS], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 22, $data[UPDATE_USER], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 23, $data[UPDATE_DATE], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 24, $data[DEPARTMENT_ID], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 25, $data[POS_SEQ_NO], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 26, $data[PAY_NO], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 27, $data[POS_ORGMGT], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 28, $data[LEVEL_NO], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 29, $data[ORG_ID_3], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 30, $data[ORG_ID_4], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 31, $data[ORG_ID_5], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 32, $data[POS_NO_NAME], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 33, $data[AUDIT_FLAG], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		
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
		
		$data_count++;
	} // end while loop
	if ($xlsRow==2) {
		$xlsRow++;
		$worksheet->write($xlsRow, 0, "***** ไม่พบข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 0));
	}

	$xlsRow++;
	$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTitle", "B", "C", "", 0));
	$xlsRow++;
	$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTitle", "B", "C", "", 0));
	$xlsRow++;
	$worksheet->write($xlsRow, 0, "จำนวนข้อมูล $count_data รายการ", set_format("xlsFmtTitle", "B", "L", "", 0));

	$workbook->close();

	$arr_file[] = $fname1;
	
	require_once("excel_tailpart_subrtn.php");
?>