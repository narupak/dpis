<?	
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$header_width[0] = 15; $header_text[0] = "รหัสทายาท";				//	HEIR_ID;
	$header_width[1] = 30; $header_text[1] = "ชื่อ";							//	PER_NAME+PER_SURNAME;
	$header_width[2] = 30; $header_text[2] = "ชื่อทายาท";				//	HEIR_NAME;
	$header_width[3] = 30; $header_text[3] = "ความสัมพันธ์";			//  HR_CODE;
	$header_width[4] = 40; $header_text[4] = "วันเกิด";						//	HEIR_BIRTHDAY; 
	$header_width[5] = 15; $header_text[5] = "สถานะการศึกษา";		//	HEIR_TAX;
	$header_width[6] = 15; $header_text[6] = "สถานะ";					//	HEIR_STATUS;
	$header_width[7] = 40; $header_text[7] = "หมายเหตุ";				//	HEIR_REMARK;

	require_once("excel_headpart_subrtn.php");

	$search_arr_cond = array();

//	if (trim($MAIN_MINISTRY_ID))
//		$srhname_arr_cond[] = "ORG_ID_REF = ".trim($MAIN_MINISTRY_ID);
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

	if (trim($HEIR_ID))
		$search_arr_cond[] = "HEIR_ID = ".trim($HEIR_ID);

	if (trim($HEIR_NAME))
		if ($arr_cond_list["HEIR_NAME"])
			$search_arr_cond[] = $arr_cond_list["HEIR_NAME"];
		else
			$search_arr_cond[] = "HEIR_NAME = '%".trim(HEIR_NAME)."%'";

	if (trim($HR_CODE))
		if (strpos(trim($HR_CODE),",")!==false)
			$search_arr_cond[] = "HR_CODE in (".fill_arr_string($HR_CODE).")";
		else
			$search_arr_cond[] = "HR_CODE = '".trim($HR_CODE)."'";

	if($HEIR_BIRTHDAY){
		$HEIR_BIRTHDAY =  save_date($HEIR_BIRTHDAY);
		if ($arr_cond_list["HEIR_BIRTHDAY"])
			$search_arr_cond[] = $arr_cond_list["HEIR_BIRTHDAY"];
		else
			$search_arr_cond[] = "HEIR_BIRTHDAY >= '".trim($HEIR_BIRTHDAY)."'";
	} // end if	

	if (trim($HEIR_TAX) >= 1 && trim($HEIR_TAX) <= 3)
		$search_arr_cond[] = "HEIR_TAX = ".trim($HEIR_TAX);
	if (trim($HEIR_STATUS) == 1 || trim($HEIR_STATUS) == 2)
		$search_arr_cond[] = "HEIR_STATUS = ".trim($HEIR_STATUS);
	if (trim($HEIR_REMARK))
		if ($arr_cond_list["HEIR_REMARK"])
			$search_arr_cond[] = $arr_cond_list["HEIR_REMARK"];
		else
			$search_arr_cond[] = "HEIR_REMARK = '%".trim($HEIR_REMARK)."%'";

	$search_text = implode(" and ",$search_arr_cond);
	if (trim($search_text))
		$search_text = "and ".trim($search_text);

	$cmd = "select PER_NAME, PER_SURNAME, b.* from PER_PERSONAL a, PER_HEIR b where a.PER_ID=b.PER_ID $search_text order by a.PER_ID";
	$count_data = $db_dpis->send_cmd($cmd);
//	echo "[$cmd] len=".strlen($cmd)."<br>";

	$cnt = 0;
	$file_limit = 5000;
	$data_limit = 1000;
	$xlsRow = 0;
	$count_org_ref = 0;
	$sheet_no = 0; $sheet_no_text="";

	$report_code = "xls_HEIR";

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
	$head = "ทายาทผู้รับผลประโยชน์";
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

		$pname = $data[PER_NAME]." ".$data[PER_SURNAME];
//		if (trim($data[PN_CODE])) {
//			$cmd1 = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='".trim($data[PN_CODE])."' ";
//			$db_dpis1->send_cmd($cmd1);
//			if ($data_dpis1 = $db_dpis1->get_array())
//				$pname = trim($data_dpis1[PN_NAME])." ".$pname;
//		}

		$xlsRow++;


		$worksheet->write($xlsRow, 0, $data[HEIR_ID], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		$worksheet->write($xlsRow, 1, $pname, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
		$worksheet->write($xlsRow, 2, $data[HEIR_NAME], set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

		if (trim($data[HR_CODE])) {
			$cmd1 ="select HR_NAME from PER_HEIRTYPE where HR_CODE = '".trim($data[HR_CODE])."' ";
			$db_dpis1->send_cmd($cmd1);
			if ($data_dpis1 = $db_dpis1->get_array())
				$HR_NAME_text = trim($data_dpis1[HR_NAME]);
			else
				$HR_NAME_text = "";
		} else  $HR_NAME_text = "";
		$worksheet->write($xlsRow, 3, $HR_NAME_text, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

		$HEIR_BIRTHDAY_text = show_date_format(trim($data[HEIR_BIRTHDAY]),$DATE_DISPLAY);
		$worksheet->write($xlsRow, 4, $HEIR_BIRTHDAY_text, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		if (trim($HEIR_TAX)==1) $HEIR_TAX_text = "กำลังศึกษา";
		elseif (trim($HEIR_TAX)==2) $HEIR_TAX_text = "ไม่ศึกษา";
		elseif (trim($HEIR_TAX)==3) $HEIR_TAX_text = "ไม่ระบุ";
		else  $HEIR_TAX_text = "";
		$worksheet->write($xlsRow, 5, $HEIR_TAX_text, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		if (trim($HEIR_STATUS)==1) $HEIR_STATUS_text = "ยังมีชีวิตอยู่";
		elseif (trim($HEIR_STATUS)==2) $HEIR_STATUS_text = "เสียชีวิต";
		else  $HEIR_STATUS_text = "";
		$worksheet->write($xlsRow, 6, $HEIR_STATUS_text, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		$worksheet->write($xlsRow, 7, $data[HEIR_REMARK], set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

		$data_count++;
	} // end while loop ($data)
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