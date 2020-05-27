<?	
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

//SRH_ID, PER_ID, SV_CODE, SRH_DOCNO, SRT_CODE, ORG_ID, PER_ID_ASSIGN, ORG_ID_ASSIGN, SRH_STARTDATE, SRH_ENDDATE, SRH_NOTE
	$header_width[0] = 15; $header_text[0] = "รหัสรายการพิเศษ";		//	SRH_ID;
	$header_width[1] = 30; $header_text[1] = "ชื่อ";							//	PER_NAME+PER_SURNAME;
	$header_width[2] = 20; $header_text[2] = "ประเภท";					//	SV_CODE;
	$header_width[3] = 30; $header_text[3] = "เลขคำสั่ง";					//	SRH_DOCNO; 
	$header_width[4] = 40; $header_text[4] = "หัวข้อ/โครงการ";			//	SRT_CODE;
	$header_width[5] = 30; $header_text[5] = "หน่วยงาน";				//	ORG_ID;
	$header_width[6] = 30; $header_text[6] = "ผู้มอบหมาย";				//	PER_ID_ASSIGN;
	$header_width[7] = 30; $header_text[7] = "หน่วยงานมอบหมาย";	//	ORG_ID_ASSIGN;
	$header_width[8] = 15; $header_text[8] = "ตั้งแต่วันที่";				//	SRH_STARTDATE;
	$header_width[9] = 15; $header_text[9] = "ถึงวันที่";					//	SRH_ENDDATE;
	$header_width[10] = 40; $header_text[10] = "หมายเหตุ";				//	SRH_NOTE;

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

	if (trim($SRH_ID))
		$search_arr_cond[] = "SRH_ID = ".trim($SRH_ID);
		
	if (trim($SV_CODE))
		if (strpos(trim($SV_CODE),",")!==false)
			$search_arr_cond[] = "SV_CODE in (".fill_arr_string($SV_CODE).")";
		else
			$search_arr_cond[] = "SV_CODE = '".trim($SV_CODE)."'";

	if (trim($SRH_DOCNO))
		if ($arr_cond_list["SRH_DOCNO"])
			$search_arr_cond[] = $arr_cond_list["SRH_DOCNO"];
		else
			$search_arr_cond[] = "SRH_DOCNO = '".trim($SRH_DOCNO)."'";

	if (trim($SRT_CODE))
		if (strpos(trim($SRT_CODE),",")!==false)
			$search_arr_cond[] = "SRT_CODE in (".fill_arr_string($SRT_CODE).")";
		else
			$search_arr_cond[] = "SRT_CODE = '".trim($SRT_CODE)."'";

	if (trim($ORG_ID))
		if (strpos(trim($ORG_ID),",")!==false)
			$search_arr_cond[] = "b.ORG_ID in (".fill_arr_string($ORG_ID).")";
		else
			$search_arr_cond[] = "b.ORG_ID = '".trim($ORG_ID)."'";

	if (trim($PER_ID_ASSIGN))
		if (strpos(trim($PER_ID_ASSIGN),",")!==false)
			$search_arr_cond[] = "PER_ID_ASSIGN in (".fill_arr_string($PER_ID_ASSIGN).")";
		else
			$search_arr_cond[] = "PER_ID_ASSIGN = '".trim($PER_ID_ASSIGN)."'";

	if (trim($ORG_ID_ASSIGN))
		if (strpos(trim($ORG_ID_ASSIGN),",")!==false)
			$search_arr_cond[] = "ORG_ID_ASSIGN in (".fill_arr_string($ORG_ID_ASSIGN).")";
		else
			$search_arr_cond[] = "ORG_ID_ASSIGN = '".trim($ORG_ID_ASSIGN)."'";

	if(trim($SRH_STARTDATE)){
		$SRH_STARTDATE =  save_date($SRH_STARTDATE);
		if ($arr_cond_list["SRH_STARTDATE"])
			$search_arr_cond[] = $arr_cond_list["SRH_STARTDATE"];
		else
			$search_arr_cond[] = "SRH_STARTDATE = '".trim($SRH_STARTDATE)."'";
	} // end if

	if(trim($SRH_ENDDATE)){
		$SRH_ENDDATE =  save_date($SRH_ENDDATE);
		if ($arr_cond_list["SRH_ENDDATE"])
			$search_arr_cond[] = $arr_cond_list["SRH_ENDDATE"];
		else
			$search_arr_cond[] = "SRH_ENDDATE = '".trim($SRH_ENDDATE)."'";
	} // end if

	if (trim($SRH_NOTE))
		if ($arr_cond_list["SRH_NOTE"])
			$search_arr_cond[] = $arr_cond_list["SRH_NOTE"];
		else
			$search_arr_cond[] = "SRH_NOTE = '%".trim($SRH_NOTE)."%'";

	$search_text = "and ".implode(" and ",$search_arr_cond);

	$cmd = " select PER_NAME, PER_SURNAME, b.* from PER_PERSONAL a, PER_SERVICEHIS b where a.PER_ID = b.PER_ID $search_text order by a.PER_ID ";
	$count_data = $db_dpis->send_cmd($cmd);
	echo "[$cmd] len=".strlen($cmd)."<br>";
	$cnt = 0;

	$cnt = 0;
	$file_limit = 5000;
	$data_limit = 1000;
	$xlsRow = 0;
	$count_org_ref = 0;
	$sheet_no = 0; $sheet_no_text="";

	$report_code = "xls_SERVICEHIS";

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
	$head = "รายการพิเศษ";
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
		
		$worksheet->write($xlsRow, 0, $data[SRH_ID], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		$pname = $data[PER_NAME]." ".$data[PER_SURNAME];
		$worksheet->write($xlsRow, 1, $pname, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

		if (trim($data[SV_CODE])) {
			$cmd = " select SV_NAME from PER_SERVICE where SV_CODE='".trim($data[SV_CODE])."' ";
			$db_dpis1->send_cmd($cmd);
			if ($data_dpis1 = $db_dpis1->get_array())
				$SV_NAME_text = trim($data_dpis1[SV_NAME]);
			else
				$SV_NAME_text = "";
		} else  $SV_NAME_text = "";
		$worksheet->write($xlsRow, 2, $SV_NAME_text, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

		$worksheet->write($xlsRow, 3, trim($data[SRH_DOCNO]), set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		if (trim($data[SRT_CODE])) {
			$cmd = " select SRT_NAME from PER_SERVICETITLE where SRT_CODE='".trim($data[SRT_CODE])."' ";
			$db_dpis1->send_cmd($cmd);
			if ($data_dpis1 = $db_dpis1->get_array())
				$SRT_NAME_text = trim($data_dpis1[SRT_NAME]);
			else
				$SRT_NAME_text = "";
		} else  $SRT_NAME_text = "";
		$worksheet->write($xlsRow, 4, $SRT_NAME_text, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

		if (trim($data[ORG_ID])) {
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=".trim($data[ORG_ID]);
			$db_dpis1->send_cmd($cmd);
			if ($data_dpis1 = $db_dpis1->get_array())
				$ORG_NAME_text = trim($data_dpis1[ORG_NAME]);
			else
				$ORG_NAME_text = "";
		} else  $ORG_NAME_text = "";
		$worksheet->write($xlsRow, 5, $ORG_NAME_text, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

		if (trim($data[PER_ID_ASSIGN])) {
			$cmd = " select PER_NAME, PER_SURNAME from PER_PERSONAL where PER_ID=".trim($data[PER_ID_ASSIGN]);
			$db_dpis1->send_cmd($cmd);
			if ($data_dpis1 = $db_dpis1->get_array())
				$PER_ID_ASSIGN_text = trim($data_dpis1[PER_NAME])+" "+trim($data_dpis1[PER_SURNAME]);
			else
				$PER_ID_ASSIGN_text = "";
		} else  $PER_ID_ASSIGN_text = "";
		$worksheet->write($xlsRow, 6, $PER_ID_ASSIGN_text, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

		if (trim($data[ORG_ID_ASSIGN])) {
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=".trim($data[ORG_ID_ASSIGN]);
			$db_dpis1->send_cmd($cmd);
			if ($data_dpis1 = $db_dpis1->get_array())
				$ORG_NAME_ASSIGN_text = trim($data_dpis1[ORG_NAME]);
			else
				$ORG_NAME_ASSIGN_text = "";
		} else  $ORG_NAME_ASSIGN_text = "";
		$worksheet->write($xlsRow, 7, $ORG_NAME_ASSIGN_text, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

		$SRH_STARTDATE_text = show_date_format(trim($data[SRH_STARTDATE]),$DATE_DISPLAY);
		$worksheet->write($xlsRow, 8, $SRH_STARTDATE_text, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		$SRH_ENDDATE_text = show_date_format(trim($data[SRH_ENDDATE]),$DATE_DISPLAY);
		$worksheet->write($xlsRow, 9, $SRH_ENDDATE_text, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		$worksheet->write($xlsRow, 10, trim($data[SRH_NOTE]), set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

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