<?	
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$header_width[0] = 10; $header_text[0] = "ลำดับที่"; 			
	$header_width[1] = 15; $header_text[1] = "รหัสประวัติการรับเครื่องราชฯ";				//	DEH_ID;
	$header_width[2] = 12; $header_text[2] = "รหัสข้าราชการ";							//	PER_ID;
	$header_width[3] = 20; $header_text[3] = "เครื่องราชฯ ที่ได้รับ";				//	DC_CODE; 
	$header_width[4] = 20; $header_text[4] = "วันที่ได้รับเครื่องราชฯ";					//	DEH_DATE;
	$header_width[5] = 20; $header_text[5] = "รหัสผู้ใช้ที่เปลี่ยนแปลงข้อมูล";	//	UPDATE_USER;
	$header_width[6] = 20; $header_text[6] = "วันเวลา เปลี่ยนแปลงข้อมูล";				//	UPDATE_DATE;
	$header_width[7] = 15; $header_text[7] = "เลขประจำตัวประชาชน";			//	PER_CARDNO;
	$header_width[8] = 20; $header_text[8] = "ราชกิจจานุเบกษา";		//	DEH_GAZETTE;
	$header_width[9] = 20; $header_text[9] = "การรับเครื่องราชฯ";		//	DEH_RECEIVE_FLAG;
	$header_width[10] = 20; $header_text[10] = "การคืนเครื่องราชฯ";	//	DEH_RETURN_FLAG;
	$header_width[11] = 20; $header_text[11] = "วันที่คืนเครื่องราชฯ";	//	DEH_RETURN_DATE;
	$header_width[12] = 20; $header_text[12] = "ประเภทการคืน";			//	DEH_RETURN_TYPE;
	$header_width[13] = 20; $header_text[13] = "วันที่ราชกิจจานุเบกษา";	//	DEH_RECEIVE_DATE;
	$header_width[14] = 10; $header_text[14] = "เลขที่นำส่ง";			//	DEH_BOOK_NO;
	$header_width[15] = 10; $header_text[15] = "วันที่หนังสือนำส่ง";			//	DEH_BOOK_DATE;
	$header_width[16] = 30; $header_text[16] = "หมายเหตุ";				//	DEH_REMARK;
	$header_width[17] = 20; $header_text[17] = "ตำแหน่ง";				//	DEH_POSITION;
	$header_width[18] = 10; $header_text[18] = "สังกัด";					//	DEH_ORG;
	$header_width[19] = 20; $header_text[19] = "ฉบับทะเบียนฐานันดร/ฉบับพิเศษ";		//	DEH_ISSUE;
	$header_width[20] = 20; $header_text[20] = "เล่ม";	//	DEH_BOOK;
	$header_width[21] = 20; $header_text[21] = "ตอนที่";	//	DEH_PART;
	$header_width[22] = 20; $header_text[22] = "หน้า";			//	DEH_PAGE;
	$header_width[23] = 20; $header_text[23] = "ลำดับ";	//	DEH_ORDER_DECOR;
	$header_width[24] = 10; $header_text[24] = "ตรวจสอบข้อมูล";			//	AUDIT_FLAG;

	require_once("excel_headpart_subrtn.php");

	$cmd = " select DEH_ID, PER_ID, DC_CODE, DEH_DATE, UPDATE_USER, UPDATE_DATE, PER_CARDNO, DEH_GAZETTE, 
									DEH_RECEIVE_FLAG, DEH_RETURN_FLAG, DEH_RETURN_DATE, DEH_RETURN_TYPE, DEH_RECEIVE_DATE, 
									DEH_BOOK_NO, DEH_BOOK_DATE, DEH_REMARK, DEH_POSITION, DEH_ORG, DEH_ISSUE, 
									DEH_BOOK, DEH_PART, DEH_PAGE, DEH_ORDER_DECOR, AUDIT_FLAG
					  from PER_DECORATEHIS 
					  where DEH_ID < 300000
					 order by DEH_ID ";
	$count_data = $db_dpis->send_cmd($cmd);
//	echo "[$cmd] len=".strlen($cmd)."<br>";

	$cnt = 0;
	$file_limit = 5000;
	$data_limit = 1000;
	$xlsRow = 0;
	$count_org_ref = 0;
	$sheet_no = 0; $sheet_no_text="";

	$report_code = "xls_DECORATEHIS";

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
	$head = "เครื่องราชฯ";
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
		$worksheet->write($xlsRow, 1, $data[DEH_ID], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 2, $data[PER_ID], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 3, $data[DC_CODE], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 4, $data[DEH_DATE], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 5, $data[UPDATE_USER], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 6, $data[UPDATE_DATE], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 7, $data[PER_CARDNO], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 8, $data[DEH_GAZETTE], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 9, $data[DEH_RECEIVE_FLAG], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 10, $data[DEH_RETURN_FLAG], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 11, $data[DEH_RETURN_DATE], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 12, $data[DEH_RETURN_TYPE], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 13, $data[DEH_RECEIVE_DATE], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 14, $data[DEH_BOOK_NO], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 15, $data[DEH_BOOK_DATE], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 16, $data[DEH_REMARK], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 17, $data[DEH_POSITION], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 18, $data[DEH_ORG], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 19, $data[DEH_ISSUE], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 20, $data[DEH_BOOK], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 21, $data[DEH_PART], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 22, $data[DEH_PAGE], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 23, $data[DEH_ORDER_DECOR], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 24, $data[AUDIT_FLAG], set_format("xlsFmtTitle", "", "L", "LR", 0));

		$xlsRow++;
		
		$worksheet->write($xlsRow, 0, "$seq_no หลัง", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 1, $data[DEH_ID], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 2, $data[PER_ID], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 3, $data[DC_CODE], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 4, $data[DEH_DATE], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 5, $data[UPDATE_USER], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 6, $data[UPDATE_DATE], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 7, $data[PER_CARDNO], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 8, $data[DEH_GAZETTE], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 9, $data[DEH_RECEIVE_FLAG], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 10, $data[DEH_RETURN_FLAG], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 11, $data[DEH_RETURN_DATE], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 12, $data[DEH_RETURN_TYPE], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 13, $data[DEH_RECEIVE_DATE], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 14, $data[DEH_BOOK_NO], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 15, $data[DEH_BOOK_DATE], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 16, $data[DEH_REMARK], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 17, $data[DEH_POSITION], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 18, $data[DEH_ORG], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 19, $data[DEH_ISSUE], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 20, $data[DEH_BOOK], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 21, $data[DEH_PART], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 22, $data[DEH_PAGE], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 23, $data[DEH_ORDER_DECOR], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 24, $data[AUDIT_FLAG], set_format("xlsFmtTitle", "B", "L", "LR", 0));

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