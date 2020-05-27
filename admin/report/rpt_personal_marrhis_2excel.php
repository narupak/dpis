<?	
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

// MAH_ID, PER_ID, MAH_SEQ, PN_CODE, MAH_NAME, MAH_MARRY_DATE, MAH_MARRY_NO, MAH_MARRY_ORG, PV_CODE, MR_CODE, DV_CODE, MAH_DIVORCE_DATE, MAH_BOOK_NO, MAH_BOOK_DATE, MAH_REMARK
	$header_width[0] = 10; $header_text[0] = "รหัสวินัย"; 					//	MAH_ID;
	$header_width[1] = 30; $header_text[1] = "ชื่อ";							//	PER_NAME+PER_SURNAME;
	$header_width[2] = 10; $header_text[2] = "ลำดับที่";					//	MAH_SEQ;
	$header_width[3] = 30; $header_text[3] = "ชื่อคู่สมรส";				//	PN_CODE + MAH_NAME; 
	$header_width[4] = 15; $header_text[4] = "วันที่จดทะเบียน";		//	MAH_MARRY_DATE;
	$header_width[5] = 15; $header_text[5] = "เลขทะเบียน";				//	MAH_MARRY_NO;
	$header_width[6] = 30; $header_text[6] = "สำนักทะเบียน";			//	MAH_MARRY_ORG;
	$header_width[7] = 20; $header_text[7] = "จังหวัด";					//	PV_CODE;
	$header_width[8] = 15; $header_text[8] = "สถานะภาพสมรส";		//	MR_CODE;
	$header_width[9] = 30; $header_text[9] = "เหตุที่ขาดจากสมรส";	//	DV_CODE;
	$header_width[10] = 15; $header_text[10] = "วันที่ขาดจากสมรส";	//	MAH_DIVORCE_DATE;
	$header_width[11] = 15; $header_text[11] = "เลขหนังสือนำส่ง";		//	MAH_BOOK_NO;
	$header_width[12] = 10; $header_text[12] = "วันที่หนักสือ";				//	MAH_BOOK_DATE;
	$header_width[13] = 40; $header_text[13] = "หมายเหตุ";				//	MAN_REMARK;

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

	if (trim($MAH_ID))
		$search_arr_cond[] = "MAH_ID = ".trim($MAH_ID);
	if (trim($MAH_SEQ))
		$search_arr_cond[] = "MAH_SEQ = ".trim($MAH_SEQ);

	if (trim($MAH_NAME))
		if ($arr_cond_list["MAH_NAME"])
			$search_arr_cond[] = $arr_cond_list["MAH_NAME"];
		else
			$search_arr_cond[] = "MAH_NAME = '%".trim($MAH_NAME)."%'";

	if(trim($MAH_MARRY_DATE)){
		$MAH_MARRY_DATE =  save_date($MAH_MARRY_DATE);
		if ($arr_cond_list["MAH_MARRY_DATE"])
			$search_arr_cond[] = $arr_cond_list["MAH_MARRY_DATE"];
		else
			$search_arr_cond[] = "MAH_MARRY_DATE = '".trim($MAH_MARRY_DATE)."'";
	} // end if

	if (trim($MAH_MARRY_NO))
		if ($arr_cond_list["MAH_MARRY_NO"])
			$search_arr_cond[] = $arr_cond_list["MAH_MARRY_NO"];
		else
			$search_arr_cond[] = "MAH_MARRY_NO = '".trim($MAH_MARRY_NO)."'";

	if (trim($MAH_MARRY_ORG))
		if ($arr_cond_list["MAH_MARRY_ORG"])
			$search_arr_cond[] = $arr_cond_list["MAH_MARRY_ORG"];
		else
			$search_arr_cond[] = "MAH_MARRY_ORG = '%".trim($MAH_MARRY_ORG)."%'";

	if (trim($PV_CODE))
		if (strpos(trim($PV_CODE),",")!==false)
			$search_arr_cond[] = "b.PV_CODE in (".fill_arr_string($PV_CODE).")";
		else
			$search_arr_cond[] = "b.PV_CODE = '".trim($PV_CODE)."'";

	if (trim($MR_CODE))
		if (strpos(trim($MR_CODE),",")!==false)
			$search_arr_cond[] = "b.MR_CODE in (".fill_arr_string($MR_CODE).")";
		else
			$search_arr_cond[] = "b.MR_CODE = '".trim($MR_CODE)."'";

	if (trim($DV_CODE))
		if (strpos(trim($DV_CODE),",")!==false)
			$search_arr_cond[] = "b.DV_CODE in (".fill_arr_string($DV_CODE).")";
		else
			$search_arr_cond[] = "b.DV_CODE = '".trim($DV_CODE)."'";

	if(trim($MAH_DIVORCE_DATE)){
		$MAH_DIVORCE_DATE =  save_date($MAH_DIVORCE_DATE);
		if ($arr_cond_list["MAH_DIVORCE_DATE"])
			$search_arr_cond[] = $arr_cond_list["MAH_DIVORCE_DATE"];
		else
			$search_arr_cond[] = "MAH_DIVORCE_DATE = '".trim($MAH_DIVORCE_DATE)."'";
	} // end if

	if (trim($MAH_BOOK_NO))
		if ($arr_cond_list["MAH_BOOK_NO"])
			$search_arr_cond[] = $arr_cond_list["MAH_BOOK_NO"];
		else
			$search_arr_cond[] = "MAH_BOOK_NO = '%".trim($MAH_BOOK_NO)."%'";

	if(trim($MAH_BOOK_DATE)){
		$MAH_BOOK_DATE =  save_date($MAH_BOOK_DATE);
		if ($arr_cond_list["MAH_BOOK_DATE"])
			$search_arr_cond[] = $arr_cond_list["MAH_BOOK_DATE"];
		else
			$search_arr_cond[] = "MAH_BOOK_DATE = '".trim($MAH_BOOK_DATE)."'";
	} // end if

	if (trim($MAH_REMARK))
		if ($arr_cond_list["MAH_REMARK"])
			$search_arr_cond[] = $arr_cond_list["MAH_REMARK"];
		else
			$search_arr_cond[] = "MAH_REMARK = '%".trim($MAH_REMARK)."%'";

	$search_text = "and ".implode(" and ",$search_arr_cond);

	$cmd = " select PER_NAME, PER_SURNAME, b.* from PER_PERSONAL a, PER_MARRHIS b where a.PER_ID = b.PER_ID $search_text order by a.PER_ID ";
	$count_data = $db_dpis->send_cmd($cmd);
	echo "[$cmd] len=".strlen($cmd)."<br>";
	$cnt = 0;

	$cnt = 0;
	$file_limit = 5000;
	$data_limit = 1000;
	$xlsRow = 0;
	$count_org_ref = 0;
	$sheet_no = 0; $sheet_no_text="";

	$report_code = "xls_MARRHIS";

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
	$head = "สมรส";
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
		
		$worksheet->write($xlsRow, 0, $data[MAH_ID], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		$pname = $data[PER_NAME]." ".$data[PER_SURNAME];
		$worksheet->write($xlsRow, 1, $pname, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
		$worksheet->write($xlsRow, 2, trim($data[MAH_SEQ]), set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		
		if(trim($data[PN_CODE])){
			$cmd = " select PN_NAME from PER_PRENAME where PN_CODE='".trim($data[PN_CODE])."' ";
			$db_dpis1->send_cmd($cmd);
			if ($data1 = $db_dpis1->get_array())
				$PN_NAME = $data1[PN_NAME]." ";
			else
				$PN_NAME = "";
		} else  $PN_NAME = "";	// end if
		$worksheet->write($xlsRow, 3, $PN_NAME.trim($data[MAH_NAME]), set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

		$MAH_MARRY_DATE_text = show_date_format($data[MAH_MARRY_DATE],$DATE_DISPLAY);
		$worksheet->write($xlsRow, 4, $MAH_MARRY_DATE_text, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		$worksheet->write($xlsRow, 5, trim($data[MAH_MARRY_NO]), set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		$worksheet->write($xlsRow, 6, trim($data[MAH_MARRY_ORG]), set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

		if (trim($data[PV_CODE])) {
			$cmd = " select PV_NAME from PER_PROVINCE where PV_CODE='".trim($data[PV_CODE])."' ";
			$db_dpis1->send_cmd($cmd);
			if ($data_dpis1 = $db_dpis1->get_array())
				$PV_NAME_text = trim($data_dpis1[PV_NAME]);
			else
				$PV_NAME_text = "";
		} else  $PV_NAME_text = "";
		$worksheet->write($xlsRow, 7, $PV_NAME_text, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

		if (trim($data[MR_CODE])) {
			$cmd = " select MR_NAME from PER_MARRIED where MR_CODE='".trim($data[MR_CODE])."' ";
			$db_dpis1->send_cmd($cmd);
			if ($data_dpis1 = $db_dpis1->get_array())
				$MR_NAME_text = trim($data_dpis1[MR_NAME]);
			else
				$MR_NAME_text = "";
		} else  $MR_NAME_text = "";
		$worksheet->write($xlsRow, 8, $MR_NAME_text, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		if (trim($data[DV_CODE])) {
			$cmd = " select DV_NAME from PER_DIVORCE where DV_CODE='".trim($data[DV_CODE])."' ";
			$db_dpis1->send_cmd($cmd);
			if ($data_dpis1 = $db_dpis1->get_array())
				$DV_NAME_text = trim($data_dpis1[DV_NAME]);
			else
				$DV_NAME_text = "";
		} else  $DV_NAME_text = "";
		$worksheet->write($xlsRow, 9, $DV_NAME_text, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

		$MAH_DIVORCE_DATE_text = show_date_format($data[MAH_DIVORCE_DATE],$DATE_DISPLAY);
		$worksheet->write($xlsRow, 10, $MAH_DIVORCE_DATE_text, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		$worksheet->write($xlsRow, 11, trim($data[MAH_BOOK_NO]), set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		$MAH_BOOK_DATE_text = show_date_format($data[MAH_BOOK_DATE],$DATE_DISPLAY);
		$worksheet->write($xlsRow, 12, $MAH_BOOK_DATE_text , set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		$worksheet->write($xlsRow, 13, trim($data[MAH_REMARK]), set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

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