<?	
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$header_width[0] = 15; $header_text[0] = "รหัสรายการ";				//	WT_ID;
	$header_width[1] = 30; $header_text[1] = "ชื่อ";							//	PER_NAME+PER_SURNAME;
	$header_width[2] = 20; $header_text[2] = "เลขคำสั่งสืบสวน";		//	WL_CODE;
	$header_width[3] = 20; $header_text[3] = "เลขคำสั่งทางวินัย";		//	WC_CODE; 
	$header_width[4] = 20; $header_text[4] = "อ้างอิงคำสั่งทางวินัยเลขที่";		//	START_DATE; 
	$header_width[5] = 30; $header_text[5] = "กรณีความผิด";			//	END_DATE;
	$header_width[6] = 30; $header_text[6] = "ระดับความผิด";			//	ABSENT_FLAG;
	$header_width[7] = 40; $header_text[7] = "หมายเหตุ";				//	REMARK;

	require_once("excel_headpart_subrtn.php");

	$search_arr_cond = array();

//	if (trim($MAIN_MINISTRY_ID))
//		$search_arr_cond[] = "ORG_ID_REF = ".trim($MAIN_MINISTRY_ID);
	if (trim($MAIN_DEPARTMENT_ID))
		$search_arr_cond[] = "a.DEPARTMENT_ID = ".trim($MAIN_DEPARTMENT_ID);
	if (trim($MAIN_ORG_ID))
		$search_arr_cond[] = "a.ORG_ID = ".trim($MAIN_ORG_ID);
	if (trim($PER_NAME))
		if ($arr_cond_list["PER_NAME"])
			$search_arr_cond[] = $arr_cond_list["PER_NAME"];
		else
			$search_arr_cond[] = "PER_NAME like '%".trim($PER_NAME)."%'";
	if (trim($PER_SURNAME))
		if ($arr_cond_list["PER_SURNAME"])
			$search_arr_cond[] = $arr_cond_list["PER_SURNAME"];
		else
			$search_arr_cond[] = "PER_SURNAME like '%".trim($PER_SURNAME)."%'";
	if (trim($PER_TYPE) || trim($PER_TYPE) > 0) // 0 คือทั้งหมด ไม่ต้องสร้างเงื่อนไข
		$search_arr_cond[] = "PER_TYPE = ".trim($PER_TYPE);

	if (trim($WT_ID))
		$search_arr_cond[] = "WT_ID = ".trim($WT_ID);

	if (trim($WL_CODE))
		if (strpos(trim($WL_CODE),",")!==false)
			$search_arr_cond[] = "WL_CODE in (".fill_arr_string($WL_CODE).")";
		else
			$search_arr_cond[] = "WL_CODE = '".trim($WL_CODE)."'";

	if (trim($WC_CODE))
		if (strpos(trim($WC_CODE),",")!==false)
			$search_arr_cond[] = "WC_CODE in (".fill_arr_string($WC_CODE).")";
		else
			$search_arr_cond[] = "WC_CODE = '".trim($WC_CODE)."'";

	if(trim($START_DATE)){
		$START_DATE =  save_date($START_DATE);
		if ($arr_cond_list["START_DATE"])
			$search_arr_cond[] = $arr_cond_list["START_DATE"];
		else
			$search_arr_cond[] = "START_DATE = '".trim($START_DATE)."'";
	} // end if

	if(trim($END_DATE)){
		$END_DATE =  save_date($END_DATE);
		if ($arr_cond_list["END_DATE"])
			$search_arr_cond[] = $arr_cond_list["END_DATE"];
		else
			$search_arr_cond[] = "END_DATE = '".trim($END_DATE)."'";
	} // end if

	if (trim($ABSENT_FLAG))
		$search_arr_cond[] = "ABSENT_FLAG = '".trim($ABSENT_FLAG)."'";

	if (trim($REMARK))
		if ($arr_cond_list["REMARK"])
			$search_arr_cond[] = $arr_cond_list["REMARK"];
		else
			$search_arr_cond[] = "REMARK = '%".trim($REMARK)."%'";

	$search_text = "and ".implode(" and ",$search_arr_cond);

	$cmd = " select PER_NAME, PER_SURNAME, b.* from PER_PERSONAL a, PER_WORK_TIME b where a.PER_ID = b.PER_ID $search_text order by a.PER_ID ";
	$count_data = $db_dpis->send_cmd($cmd);
	echo "[$cmd] len=".strlen($cmd)."<br>";
	$cnt = 0;

	$cnt = 0;
	$file_limit = 5000;
	$data_limit = 1000;
	$xlsRow = 0;
	$count_org_ref = 0;
	$sheet_no = 0; $sheet_no_text="";

	$report_code = "xls_WORKTIME";

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
	$head = "เวลาการมาปฏิบัติราชการ";
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

		$xlsRow++;
		
		$worksheet->write($xlsRow, 0, $data[WT_ID], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		$pname = $data[PER_NAME]." ".$data[PER_SURNAME];
		$worksheet->write($xlsRow, 1, $pname, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

		if (trim($data[WL_CODE])) {
			$cmd = " select WL_NAME from PER_WORK_LOCATION where WL_CODE='".trim($data[WL_CODE])."' ";
			$db_dpis1->send_cmd($cmd);
			if ($data_dpis1 = $db_dpis1->get_array())
				$WL_NAME_text = trim($data_dpis1[WL_NAME]);
			else
				$WL_NAME_text = "";
		} else  $WL_NAME_text = "";
		$worksheet->write($xlsRow, 2, $WL_NAME_text, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

		if (trim($data[WC_CODE])) {
			$cmd = " select WC_NAME from PER_WORK_CYCLE where WC_CODE='".trim($data[WC_CODE])."' ";
			$db_dpis1->send_cmd($cmd);
			if ($data_dpis1 = $db_dpis1->get_array())
				$WC_NAME_text = trim($data_dpis1[WC_NAME]);
			else
				$WC_NAME_text = "";
		} else  $WC_NAME_text = "";
		$worksheet->write($xlsRow, 3, $WC_NAME_text, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

		$START_DATE_text = show_date_format(trim($data[START_DATE]),$DATE_DISPLAY);
		$worksheet->write($xlsRow, 4, $START_DATE_text, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		$END_DATE_text =  show_date_format(trim($data[END_DATE]),$DATE_DISPLAY);
		$worksheet->write($xlsRow, 5, $END_DATE_text, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		if (trim($data[ABSENT_FLAG])==1)  $ABSENT_FLAG_text = "วันหยุด";
		elseif (trim($data[ABSENT_FLAG])==2)  $ABSENT_FLAG_text = "ลา";
		elseif (trim($data[ABSENT_FLAG])==3)  $ABSENT_FLAG_text = "สาย";
		elseif (trim($data[ABSENT_FLAG])==4)  $ABSENT_FLAG_text = "ปฏิบัติราชการนอกสถานที่";
		elseif (trim($data[ABSENT_FLAG])==5)  $ABSENT_FLAG_text = "ขาดราชการ";
		elseif (trim($data[ABSENT_FLAG])==9)  $ABSENT_FLAG_text = "ไม่บันทึกเวลา";
		else  $ABSENT_FLAG_text = "";
		$worksheet->write($xlsRow, 6, $ABSENT_FLAG_text, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

		$worksheet->write($xlsRow, 7, trim($data[REMARK]), set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

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
	$xlsRow++;
	$worksheet->write($xlsRow, 0, "Search Key", set_format("xlsFmtTitle", "", "L", "", 0));
	$search_key = implode(", ",$search_arr_cond);
	if ($search_key)
		$skey = $search_key;
	else
		$skey = "";
	$worksheet->write($xlsRow, 1, $skey, set_format("xlsFmtTitle", "", "L", "", 0));

	$workbook->close();

	$arr_file[] = $fname1;

	require_once("excel_tailpart_subrtn.php");
?>