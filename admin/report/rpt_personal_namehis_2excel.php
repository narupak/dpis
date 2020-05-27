<?	
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$header_width[0] = 15; $header_text[0] = "รหัสการเปลี่ยนชื่อ"; 	//	NH_ID;
	$header_width[1] = 30; $header_text[1] = "ชื่อ";							//	PER_NAME+PER_SURNAME;
	$header_width[2] = 20; $header_text[2] = "เลขที่หนังสือ";			//	NH_DOCNO;
	$header_width[3] = 20; $header_text[3] = "วันที่หนังสือ";				//	NH_DATE; 
	$header_width[4] = 20; $header_text[4] = "สำนักทะเบียน";			//	NH_ORG; 
	$header_width[5] = 15; $header_text[5] = "คำนำหน้าเก่า";			//	PN_CODE;
	$header_width[6] = 30; $header_text[6] = "ชื่อ";							//	NH_NAME;
	$header_width[7] = 30; $header_text[7] = "นามสกุล";					//	NH_SURNAME;
	$header_width[8] = 15; $header_text[8] = "คำนำหน้าใหม่";			//	PN_CODE_NEW;
	$header_width[9] = 30; $header_text[9] = "ชื่อใหม่";					//	NH_NAME_NEW;
	$header_width[10] = 30; $header_text[10] = "นามสกุลใหม่";		//	NH_SURNAME_NEW;
	$header_width[11] = 20; $header_text[11] = "เลขที่นำส่ง";			//	NH_BOOK_NO;
	$header_width[12] = 20; $header_text[12] = "วันที่หนังสือ";			//	NH_BOOK_DATE;
	$header_width[13] = 40; $header_text[13] = "หมายเหตุ";				//	NH_REMARK;

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

	if (trim($NH_ID))
		$search_arr_cond[] = "NH_ID = ".trim($NH_ID);
	if (trim($NH_DOCNO))
		if ($arr_cond_list["NH_DOCNO"])
			$search_arr_cond[] = $arr_cond_list["NH_DOCNO"];
		else
			$search_arr_cond[] = "NH_DOCNO = '".trim($NH_DOCNO)."'";

	if(trim($NH_DATE)){
		$NH_DATE =  save_date($NH_DATE);
		if ($arr_cond_list["NH_DATE"])
			$search_arr_cond[] = $arr_cond_list["NH_DATE"];
		else
			$search_arr_cond[] = "NH_DATE = '".trim($NH_DATE)."'";
	} // end if

	if (trim($NH_ORG))
		if ($arr_cond_list["NH_ORG"])
			$search_arr_cond[] = $arr_cond_list["NH_ORG"];
		else
			$search_arr_cond[] = "NH_ORG = '".trim($NH_ORG)."'";

	if (trim($PN_CODE))
		if (strpos(trim($PN_CODE),",")!==false)
			$search_arr_cond[] = "b.PN_CODE in (".fill_arr_string($PN_CODE).")";
		else
			$search_arr_cond[] = "b.PN_CODE = '".trim($PN_CODE)."'";

	if (trim($NH_NAME))
		if ($arr_cond_list["NH_NAME"])
			$search_arr_cond[] = $arr_cond_list["NH_NAME"];
		else
			$search_arr_cond[] = "NH_NAME = '%".trim($NH_NAME)."%'";

	if (trim($NH_SURNAME))
		if ($arr_cond_list["NH_SURNAME"])
			$search_arr_cond[] = $arr_cond_list["NH_SURNAME"];
		else
			$search_arr_cond[] = "NH_SURNAME = '%".trim($NH_SURNAME)."%'";

	if (trim($PN_CODE_NEW))
		if (strpos(trim($PN_CODE_NEW),",")!==false)
			$search_arr_cond[] = "PN_CODE_NEW in (".fill_arr_string($PN_CODE_NEW).")";
		else
			$search_arr_cond[] = "PN_CODE_NEW = '".trim($PN_CODE_NEW)."'";

	if (trim($NH_NAME_NEW))
		if ($arr_cond_list["NH_NAME_NEW"])
			$search_arr_cond[] = $arr_cond_list["NH_NAME_NEW"];
		else
			$search_arr_cond[] = "NH_NAME_NEW = '%".trim($NH_NAME_NEW)."%'";

	if (trim($NH_SURNAME))
		if ($arr_cond_list["NH_SURNAME_NEW"])
			$search_arr_cond[] = $arr_cond_list["NH_SURNAME_NEW"];
		else
			$search_arr_cond[] = "NH_SURNAME_NEW = '%".trim($NH_SURNAME_NEW)."%'";

	if (trim($NH_BOOK_NO))
		if ($arr_cond_list["NH_BOOK_NO"])
			$search_arr_cond[] = $arr_cond_list["NH_BOOK_NO"];
		else
			$search_arr_cond[] = "NH_BOOK_NO = '%".trim($NH_BOOK_NO)."%'";

	if(trim($NH_BOOK_DATE)){
		$NH_BOOK_DATE =  save_date($NH_BOOK_DATE);
		if ($arr_cond_list["NH_BOOK_DATE"])
			$search_arr_cond[] = $arr_cond_list["NH_BOOK_DATE"];
		else
			$search_arr_cond[] = "NH_BOOK_DATE = '".trim($NH_BOOK_DATE)."'";
	} // end if

	if (trim($NH_REMARK))
		if ($arr_cond_list["NH_REMARK"])
			$search_arr_cond[] = $arr_cond_list["NH_REMARK"];
		else
			$search_arr_cond[] = "NH_REMARK = '%".trim($NH_REMARK)."%'";

	$search_text = "and ".implode(" and ",$search_arr_cond);

	$cmd = " select PER_NAME, PER_SURNAME, b.* from PER_PERSONAL a, PER_NAMEHIS b where a.PER_ID = b.PER_ID $search_text order by a.PER_ID ";
	$count_data = $db_dpis->send_cmd($cmd);
	echo "[$cmd] len=".strlen($cmd)."<br>";
	$cnt = 0;

	$cnt = 0;
	$file_limit = 5000;
	$data_limit = 1000;
	$xlsRow = 0;
	$count_org_ref = 0;
	$sheet_no = 0; $sheet_no_text="";

	$report_code = "xls_NAMEHIS";

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
	$head = "การเปลี่ยนชื่อ-สกุล";
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
		
		$worksheet->write($xlsRow, 0, $data[NH_ID], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		$pname = $data[PER_NAME]." ".$data[PER_SURNAME];
		$worksheet->write($xlsRow, 1, $pname, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
		$worksheet->write($xlsRow, 2, trim($data[NH_DOCNO]), set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		
		$NH_DATE_text = show_date_format(trim($data[NH_DATE]),$DATE_DISPLAY);
		$worksheet->write($xlsRow, 3, $NH_DATE_text, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		
		$worksheet->write($xlsRow, 4, trim($data[NH_ORG]), set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		if (trim($data[PN_CODE])) {
			$cmd = " select PN_NAME from PER_PRENAME where PN_CODE='".trim($data[PN_CODE])."' ";
			$db_dpis1->send_cmd($cmd);
			if ($data_dpis1 = $db_dpis1->get_array())
				$PN_NAME_text = trim($data_dpis1[PN_NAME]);
			else
				$PN_NAME_text = "";
		} else  $PN_NAME_text = "";
		$worksheet->write($xlsRow, 5, $PN_NAME_text, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

		$worksheet->write($xlsRow, 6, trim($data[NH_NAME]), set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
		$worksheet->write($xlsRow, 7, trim($data[NH_SURNAME]), set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

		if (trim($data[PN_CODE_NEW])) {
			$cmd = " select PN_NAME from PER_PRENAME where PN_CODE='".trim($data[PN_CODE_NEW])."' ";
			$db_dpis1->send_cmd($cmd);
			if ($data_dpis1 = $db_dpis1->get_array())
				$PN_NAME_NEW_text = trim($data_dpis1[PN_NAME_NEW]);
			else
				$PN_NAME_NEW_text = "";
		} else  $PN_NAME_NEW_text = "";
		$worksheet->write($xlsRow, 8, $PN_NAME_NEW_text, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

		$worksheet->write($xlsRow, 9, trim($data[NH_NAME_NEW]), set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
		$worksheet->write($xlsRow, 10, trim($data[NH_SURNAME_NEW]), set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
		$worksheet->write($xlsRow, 11, trim($data[NH_BOOK_NO]), set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

		$NH_BOOK_DATE_text = show_date_format(trim($data[NH_BOOK_DATE]),$DATE_DISPLAY);
		$worksheet->write($xlsRow, 12, $NH_BOOK_DATE_text, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		$worksheet->write($xlsRow, 13, trim($data[NH_REMARK]), set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

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