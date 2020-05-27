<?	
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$header_width[0] = 15; $header_text[0] = "รหัสวินัย"; 					//	PUN_ID;
	$header_width[1] = 30; $header_text[1] = "ชื่อ";							//	PER_NAME+PER_SURNAME;
	$header_width[2] = 20; $header_text[2] = "เลขคำสั่งสืบสวน";		//	INV_NO;
	$header_width[3] = 20; $header_text[3] = "เลขคำสั่งทางวินัย";		//	PUN_NO; 
	$header_width[4] = 20; $header_text[4] = "อ้างอิงคำสั่งทางวินัยเลขที่";		//	PUN_REF_NO; 
	$header_width[5] = 30; $header_text[5] = "กรณีความผิด";			//	CRD_CODE;
	$header_width[6] = 30; $header_text[6] = "ระดับความผิด";			//	PUN_TYPE;
	$header_width[7] = 30; $header_text[7] = "สั่งให้";						//	PEN_CODE;
	$header_width[8] = 20; $header_text[8] = "วันที่รับโทษ";				//	PUN_STARTDATE;
	$header_width[9] = 20; $header_text[9] = "วันที่สิ้นสุด";				//	PUN_ENDDATE;
	$header_width[10] = 20; $header_text[10] = "เงินชดใช้";				//	PUN_PAY;
	$header_width[11] = 10; $header_text[11] = "ตัดเงินเดือน";			//	PUN_SALARY;
	$header_width[12] = 40; $header_text[12] = "หมายเหตุ";				//	PUN_REMARK;

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

	if (trim($PUN_ID))
		$search_arr_cond[] = "PUN_ID = ".trim($PUN_ID);
	if (trim($INV_NO))
		if ($arr_cond_list["INV_NO"])
			$search_arr_cond[] = $arr_cond_list["INV_NO"];
		else
			$search_arr_cond[] = "INV_NO = '".trim($INV_NO)."'";
	if (trim($PUN_NO))
		if ($arr_cond_list["PUN_NO"])
			$search_arr_cond[] = $arr_cond_list["PUN_NO"];
		else
			$search_arr_cond[] = "PUN_NO = '".trim($PUN_NO)."'";
	if (trim($PUN_REF_NO))
		if ($arr_cond_list["PUN_REF_NO"])
			$search_arr_cond[] = $arr_cond_list["PUN_REF_NO"];
		else
			$search_arr_cond[] = "PUN_REF_NO = '".trim($PUN_REF_NO)."'";
		
	if (trim($CRD_CODE))
		if (strpos(trim($CRD_CODE),",")!==false)
			$search_arr_cond[] = "b.CRD_CODE in (".fill_arr_string($CRD_CODE).")";
		else
			$search_arr_cond[] = "b.CRD_CODE = '".trim($CRD_CODE)."'";
	if (trim($PUN_TYPE))
		$search_arr_cond[] = "PUN_TYPE = '".trim($PUN_TYPE)."'";
	if (trim($PEN_CODE))
		if (strpos(trim($PEN_CODE),",")!==false)
			$search_arr_cond[] = "PEN_CODE in (".fill_arr_string($PEN_CODE).")";
		else
			$search_arr_cond[] = "PEN_CODE = '".trim($PEN_CODE)."'";

	if(trim($PUN_STARTDATE)){
		$PUN_STARTDATE =  save_date($PUN_STARTDATE);
		if ($arr_cond_list["PUN_STARTDATE"])
			$search_arr_cond[] = $arr_cond_list["PUN_STARTDATE"];
		else
			$search_arr_cond[] = "PUN_STARTDATE = '".trim($PUN_STARTDATE)."'";
	} // end if

	if(trim($PUN_ENDDATE)){
		$PUN_ENDDATE =  save_date($PUN_ENDDATE);
		if ($arr_cond_list["PUN_ENDDATE"])
			$search_arr_cond[] = $arr_cond_list["PUN_ENDDATE"];
		else
			$search_arr_cond[] = "PUN_ENDDATE = '".trim($PUN_ENDDATE)."'";
	} // end if

	if (trim($PUN_REMARK))
		if ($arr_cond_list["PUN_REMARK"])
			$search_arr_cond[] = $arr_cond_list["PUN_REMARK"];
		else
			$search_arr_cond[] = "PUN_REMARK = '%".trim($PUN_REMARK)."%'";

	$search_text = "and ".implode(" and ",$search_arr_cond);

	$cmd = " select PER_NAME, PER_SURNAME, b.* from PER_PERSONAL a, PER_PUNISHMENT b where a.PER_ID = b.PER_ID $search_text order by a.PER_ID ";
	$count_data = $db_dpis->send_cmd($cmd);
	echo "[$cmd] len=".strlen($cmd)."<br>";
	$cnt = 0;

	$cnt = 0;
	$file_limit = 5000;
	$data_limit = 1000;
	$xlsRow = 0;
	$count_org_ref = 0;
	$sheet_no = 0; $sheet_no_text="";

	$report_code = "xls_PUNISHMENT";

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
	$head = "วินัย";
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
		
		$worksheet->write($xlsRow, 0, $data[PUN_ID], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		$pname = $data[PER_NAME]." ".$data[PER_SURNAME];
		$worksheet->write($xlsRow, 1, $pname, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
		$worksheet->write($xlsRow, 2, trim($data[INV_NO]), set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		$worksheet->write($xlsRow, 3, trim($data[PUN_NO]), set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		$worksheet->write($xlsRow, 4, trim($data[PUN_REF_NO]), set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		if (trim($data[CRD_CODE])) {
			$cmd = " select CRD_NAME from PER_CRIME_DTL where CRD_CODE='".trim($data[CRD_CODE])."' ";
			$db_dpis1->send_cmd($cmd);
			if ($data_dpis1 = $db_dpis1->get_array())
				$CRD_NAME_text = trim($data_dpis1[CRD_NAME]);
			else
				$CRD_NAME_text = "";
		} else  $CRD_NAME_text = "";
		$worksheet->write($xlsRow, 5, $CRD_NAME_text, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

		if (trim($data[PUN_TYPE])==1)  $PUN_TYPE_text = "อย่างร้ายแรง";
		elseif (trim($data[PUN_TYPE])==2)  $PUN_TYPE_text = "ไม่ร้ายแรง";
		elseif (trim($data[PUN_TYPE])==3)  $PUN_TYPE_text = "ไม่เป็นความผิดทางวินัย";
		else  $PUN_TYPE_text = "";
		$worksheet->write($xlsRow, 6, $PUN_TYPE_text, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
		
		if (trim($data[PEN_CODE])) {
			$cmd = " select PEN_NAME from PER_PENALTY where PEN_CODE='".trim($data[PEN_CODE])."' ";
			$db_dpis1->send_cmd($cmd);
			if ($data_dpis1 = $db_dpis1->get_array())
				$PEN_NAME_text = trim($data_dpis1[PEN_NAME]);
			else
				$PEN_NAME_text = "";
		} else  $PEN_NAME_text = "";
		$worksheet->write($xlsRow, 7, $PEN_NAME_text, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

		$PUN_STARTDATE_text = show_date_format(trim($data[PUN_STARTDATE]),$DATE_DISPLAY);
		$worksheet->write($xlsRow, 8, $PUN_STARTDATE_text, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		$PUN_ENDDATE_text = show_date_format(trim($data[PUN_ENDDATE]),$DATE_DISPLAY);
		$worksheet->write($xlsRow, 9, $PUN_ENDDATE_text, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		$worksheet->write($xlsRow, 10, trim($data[PUN_PAY]), set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		$worksheet->write($xlsRow, 11, trim($data[PUN_SALARY]), set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		$worksheet->write($xlsRow, 12, trim($data[PUN_REMARK]), set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

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