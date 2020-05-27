<?	
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$header_width[0] = 15; $header_text[0] = "รหัสเงินพิเศษ"; 			//	EXH_ID;
	$header_width[1] = 30; $header_text[1] = "ชื่อ";							//	PER_NAME+PER_SURNAME;
	$header_width[2] = 10; $header_text[2] = "วันที่มีผล";					//	EXH_EFFECTIVEDATE; 
	$header_width[3] = 20; $header_text[3] = "ประเภทเงิน";				//	EX_CODE;
	$header_width[4] = 10; $header_text[4] = "จำนวนเงิน";				//	EXH_AMT; 
	$header_width[5] = 10; $header_text[5] = "วันที่สิ้นสุด";				//	EXH_ENDDATE;
//	$header_width[6] = 20; $header_text[6] = "บัตรประชาชน";			//	PER_CARDNO;
	$header_width[6] = 30; $header_text[6] = "หน่วยงานที่ออกคำสั่ง";	//	EXH_ORG_NAME;
	$header_width[7] = 20; $header_text[7] = "เลขที่เอกสาร";			//	EXH_DOCNO;
	$header_width[8] = 10; $header_text[8] = "วันที่เอกสาร";				//	EXH_DOCDATE;
	$header_width[9] = 10; $header_text[9] = "เงินเดือน";					//	EXH_SALARY;
	$header_width[10] = 40; $header_text[10] = "หมายเหตุ";				//	EXH_REMARK;
	$header_width[11] = 10; $header_text[11] = "สถานะรายการ";		//	EXH_ACTIVE 1=ใช้งาน 2=ยกเลิก;

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

	if (trim($EXH_ID))
		$search_arr_cond[] = "EXH_ID = ".trim($EXH_ID);
		
	if($EXH_EFFECTIVEDATE){
		$EXH_EFFECTIVEDATE =  save_date($EXH_EFFECTIVEDATE);
		if ($arr_cond_list["EXH_EFFECTIVEDATE"])
			$search_arr_cond[] = $arr_cond_list["EXH_EFFECTIVEDATE"];
		else
			$search_arr_cond[] = "EXH_EFFECTIVEDATE = '".trim($EXH_EFFECTIVEDATE)."'";
	} // end if

	if (trim($EX_CODE))
		if (strpos(trim($EX_CODE),",")!==false)
			$search_arr_cond[] = "EX_CODE in (".fill_arr_string($EX_CODE).")";
		else
			$search_arr_cond[] = "EX_CODE = ".trim($EX_CODE);
	if (trim($EXH_AMT))
		if ($arr_cond_list["EXH_AMT"])
			$search_arr_cond[] = $arr_cond_list["EXH_AMT"];
		else
			$search_arr_cond[] = "EXH_AMT = ".trim($EXH_AMT);
	if(trim($EXH_ENDDATE)){
		$EXH_ENDDATE =  save_date($EXH_ENDDATE);
		if ($arr_cond_list["EXH_ENDDATE"])
			$search_arr_cond[] = $arr_cond_list["EXH_ENDDATE"];
		else
			$search_arr_cond[] = "EXH_ENDDATE = '".trim($EXH_ENDDATE)."'";
	} // end if

	if (trim($PER_CARDNO))
		$search_arr_cond[] = "PER_CARDNO = ".trim($PER_CARDNO);
	if (trim($EXH_ORG_NAME))
		if ($arr_cond_list["EXH_ORG_NAME"])
			$search_arr_cond[] = $arr_cond_list["EXH_ORG_NAME"];
		else
			$search_arr_cond[] = "EXH_ORG_NAME = '%".trim($EXH_ORG_NAME)."%'";
	if (trim($EXH_DOCNO))
		if ($arr_cond_list["EXH_DOCNO"])
			$search_arr_cond[] = $arr_cond_list["EXH_DOCNO"];
		else
			$search_arr_cond[] = "EXH_DOCNO = '".trim($EXH_DOCNO)."'";

	if(trim($EXH_DOCDATE)){
		$EXH_DOCDATE =  save_date($EXH_DOCDATE);
		if ($arr_cond_list["EXH_DOCDATE"])
			$search_arr_cond[] = $arr_cond_list["EXH_DOCDATE"];
		else
			$search_arr_cond[] = "EXH_DOCDATE = '".trim($EXH_DOCDATE)."'";
	} // end if

	if (trim($EXH_SALARY))
		if ($arr_cond_list["EXH_SALARY"])
			$search_arr_cond[] = $arr_cond_list["EXH_SALARY"];
		else
			$search_arr_cond[] = "EXH_SALARY = ".trim($EXH_SALARY);
	if (trim($EXH_REMARK))
		if ($arr_cond_list["EXH_REMARK"])
			$search_arr_cond[] = $arr_cond_list["EXH_REMARK"];
		else
			$search_arr_cond[] = "EXH_REMARK = '%".trim($EXH_REMARK)."%'";
	if (trim($EXH_ACTIVE))
		$search_arr_cond[] = "EXH_ACTIVE = ".trim($EXH_ACTIVE);

	$search_text = implode(" and ",$search_arr_cond);
	if (trim($search_text))
		$search_text = "and ".trim($search_text);

	$cmd = "select PER_NAME, PER_SURNAME, b.* from PER_PERSONAL a, PER_EXTRAHIS b where a.PER_ID=b.PER_ID $search_text order by a.PER_ID";
	$count_data = $db_dpis->send_cmd($cmd);
//	echo "[$cmd] len=".strlen($cmd)."<br>";

	$cnt = 0;
	$file_limit = 5000;
	$data_limit = 1000;
	$xlsRow = 0;
	$count_org_ref = 0;
	$sheet_no = 0; $sheet_no_text="";

	$report_code = "xls_EXTRAHIS";

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
	$head = "เงินเพิ่มพิเศษ";
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

		$worksheet->write($xlsRow, 0, $data[EXH_ID], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		$worksheet->write($xlsRow, 1, $pname, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

		$EXH_EFFECTIVEDATE_text = show_date_format(trim($data[EXH_EFFECTIVEDATE]),$DATE_DISPLAY);
		$worksheet->write($xlsRow, 2, $EXH_EFFECTIVEDATE_text, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		
		if (trim($data[EX_CODE])) {
			$cmd1 ="select EX_NAME from PER_EXTRATYPE where EX_CODE = '".trim($data[EX_CODE])."' ";
			$db_dpis1->send_cmd($cmd1);
			if ($data_dpis1 = $db_dpis1->get_array())
				$EX_NAME_text = trim($data_dpis1[EX_NAME]);
			else
				$EX_NAME_text = "";
		} else  $EX_NAME_text = "";
		$worksheet->write($xlsRow, 3, $EX_NAME_text, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

		$worksheet->write($xlsRow, 4, $data[EXH_AMT], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
			
		$EXH_ENDDATE_text = show_date_format(trim($data[EXH_ENDDATE]),$DATE_DISPLAY);
		$worksheet->write($xlsRow, 5, $EXH_ENDDATE_text, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		$worksheet->write($xlsRow, 6, $data[EXH_ORG_NAME], set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
		$worksheet->write($xlsRow, 7, $data[EXH_DOCNO], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		$EXH_DOCDATE_text = show_date_format(trim($data[EXH_DOCDATE]),$DATE_DISPLAY);
		$worksheet->write($xlsRow, 8, $EXH_DOCDATE_text, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		$worksheet->write($xlsRow, 9, $data[EXH_SALARY], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		$worksheet->write($xlsRow, 10, $data[EXH_REMARK], set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

		if (trim($data[EXH_ACTIVE])=="1")			$EXH_ACTIVE_text = "ใช้งาน";
		elseif (trim($data[EXH_ACTIVE])=="2")		$EXH_ACTIVE_text = "ยกเลิก";
		else  $EXH_ACTIVE_text = "";
		$worksheet->write($xlsRow, 11, $EXH_ACTIVE_text, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		
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