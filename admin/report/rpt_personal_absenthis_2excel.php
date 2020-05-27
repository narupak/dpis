<?	
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	$header_width[0] = 15; $header_text[0] = "รหัสการลา"; 				//	ABS_ID;
	$header_width[1] = 30; $header_text[1] = "ชื่อ";							//	PER_NAME+PER_SURNAME;
	$header_width[2] = 10; $header_text[2] = "วันที่เริ่ม";					//	ABS_STARTDATE; 
	$header_width[3] = 10; $header_text[3] = "ช่วงวันเริ่ม";				//	ABS_STARTPERIOD;
	$header_width[4] = 10; $header_text[4] = "ถึงวันที่";					//	ABS_ENDDATE;
	$header_width[5] = 10; $header_text[5] = "ถึงช่วง";						//	ABS_ENDPERIOD;
	$header_width[6] = 30; $header_text[6] = "ประเภทการลา";			//	AB_CODE;
	$header_width[7] = 10; $header_text[7] = "จำนวนวัน";				//	ABS_DAY;
	$header_width[8] = 40; $header_text[8] = "หมายเหตุ";				//	ABS_REMARK;

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

	if (trim($ABS_ID))
		$search_arr_cond[] = "ABS_ID = ".trim($ABS_ID);

	if(trim($ABS_STARTDATE)){
		$ABS_STARTDATE =  save_date($ABS_STARTDATE);
		if ($arr_cond_list["ABS_STARTDATE"])
			$search_arr_cond[] = $arr_cond_list["ABS_STARTDATE"];
		else
			$search_arr_cond[] = "ABS_STARTDATE = '".trim($ABS_STARTDATE)."'";
	} // end if

	if (trim($ABS_STARTPERIOD)) // 0 คือทั้งหมด ไม่ต้องสร้างเงื่อนไข
		$search_arr_cond[] = "ABS_STARTPERIOD = ".trim($ABS_STARTPERIOD);

	if(trim($ABS_ENDDATE)){
		$ABS_ENDDATE =  save_date($ABS_ENDDATE);
		if ($arr_cond_list["ABS_ENDDATE"])
			$search_arr_cond[] = $arr_cond_list["ABS_ENDDATE"];
		else
			$search_arr_cond[] = "ABS_ENDDATE = '".trim($ABS_ENDDATE)."'";
	} // end if

	if (trim($ABS_ENDPERIOD)) // 0 คือทั้งหมด ไม่ต้องสร้างเงื่อนไข
		$search_arr_cond[] = "ABS_ENDPERIOD = ".trim($ABS_ENDPERIOD);

	if (trim($AB_CODE))
		if (strpos(trim($AB_CODE),",")!==false)
			$search_arr_cond[] = "AB_CODE in (".fill_arr_string($AB_CODE).")";
		else
			$search_arr_cond[] = "AB_CODE = '".trim($AB_CODE)."'";
			
	if (trim($ABS_DAY))
		if ($arr_cond_list["ABS_DAY"])
			$search_arr_cond[] = $arr_cond_list["ABS_DAY"];
		else
			$search_arr_cond[] = "ABS_DAY = ".trim($ABS_DAY);

	if (trim($ABS_REMARK))
		if ($arr_cond_list["ABS_REMARK"])
			$search_arr_cond[] = $arr_cond_list["ABS_REMARK"];
		else
			$search_arr_cond[] = "ABS_REMARK = '%".trim($ABS_REMARK)."%'";

	$search_text = implode(" and ",$search_arr_cond);
	if (trim($search_text))
		$search_text = "and ".trim($search_text);

	$cmd = "select PER_NAME, PER_SURNAME, b.* from PER_PERSONAL a,  PER_ABSENTHIS b where a.PER_ID = b.PER_ID $search_text order by a.PER_ID";
	$count_data = $db_dpis->send_cmd($cmd);
//	echo "[$cmd] len=".strlen($cmd)."<br>";

	$cnt = 0;
	$file_limit = 5000;
	$data_limit = 1000;
	$xlsRow = 0;
	$count_org_ref = 0;
	$sheet_no = 0; $sheet_no_text="";

	$report_code = "xls_ABSENTHIS";

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
	$head = "การลา";
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

		$worksheet->write($xlsRow, 0, $data[ABS_ID], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		$worksheet->write($xlsRow, 1, $pname, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

		$ABS_STARTDATE_text =  show_date_format(trim($data[ABS_STARTDATE]),$DATE_DISPLAY);
		$worksheet->write($xlsRow, 2, $ABS_STARTDATE_text, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		if (trim($data[ABS_STARTPERIOD]) == 1)	$varbuff = "ครึ่งวันเช้า";
		elseif (trim($data[ABS_STARTPERIOD]) == 2)	$varbuff = "ครึ่งวันบ่าย";
		elseif (trim($data[ABS_STARTPERIOD]) == 3)	$varbuff = "ทั้งวัน";
		else  $varbuff = "";
		$worksheet->write($xlsRow, 3, $varbuff, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		$ABS_ENDDATE_text = show_date_format(trim($data[ABS_ENDDATE]),$DATE_DISPLAY);
		$worksheet->write($xlsRow, 4, $ABS_ENDDATE_text, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		if (trim($data[ABS_ENDPERIOD]) == 1)	$varbuff = "ครึ่งวันเช้า";
		elseif (trim($data[ABS_ENDPERIOD]) == 2)	$varbuff = "ครึ่งวันบ่าย";
		elseif (trim($data[ABS_ENDPERIOD]) == 3)	$varbuff = "ทั้งวัน";
		else  $varbuff = "";
		$worksheet->write($xlsRow, 5, $varbuff, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		if (trim($data[AB_CODE])) {
			$cmd1 ="select AB_NAME from PER_ABSENTTYPE where AB_CODE= '".trim($data[AB_CODE])."' ";
			$db_dpis1->send_cmd($cmd1);
			if ($data_dpis1 = $db_dpis1->get_array())
				$AB_NAME_text = trim($data_dpis1[AB_NAME]);
			else
				$AB_NAME_text = "";
		} else  $AB_NAME_text = "";
		$worksheet->write($xlsRow, 6, $AB_NAME_text, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

		$worksheet->write($xlsRow, 7, $data[ABS_DAY], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		$worksheet->write($xlsRow, 8, $data[ABS_REMARK], set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
		
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