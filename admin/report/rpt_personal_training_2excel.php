<?	
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$header_width[0] = 15; $header_text[0] = "รหัส";			 			//	TRN_ID;
	$header_width[1] = 30; $header_text[1] = "ชื่อ";							//	PER_NAME+PER_SURNAME;
	$header_width[2] = 10; $header_text[2] = "ประเภท";					//	TR_TYPE; 
	$header_width[3] = 35; $header_text[3] = "หลักสูตร";					//	TR_CODE; 
	$header_width[4] = 20; $header_text[4] = "รุ่น";							//	TRN_NO;
	$header_width[5] = 10; $header_text[5] = "วันที่เริ่มต้น";				//	TRN_STARTDATE; 
	$header_width[6] = 10; $header_text[6] = "วันที่สิ้นสุด";				//	TRN_ENDDATE;
	$header_width[7] = 30; $header_text[7] = "หน่วยงานที่จัด";			//	TRN_ORG;
	$header_width[8] = 30; $header_text[8] = "สถานที่";					//	TRN_PLACE;
	$header_width[9] = 20; $header_text[9] = "ประเทศ";					//	CT_CODE;
	$header_width[10] = 30; $header_text[10] = "ชื่อทุน";						//	TRN_FUND;
	$header_width[11] = 20; $header_text[11] = "ประเทศเจ้าของทุน";	//	EXH_REMARK;
	$header_width[12] = 10; $header_text[12] = "จำนวนวัน";			//	TRN_DAY;
	$header_width[13] = 30; $header_text[13] = "หมายเหตุ";				//	TRN_REMARK;
	$header_width[14] = 08; $header_text[14] = "ผ่าน";					//	TRN_PASS;
	$header_width[15] = 15; $header_text[15] = "เลขที่นำส่ง";			//	TRN_BOOK_NO;
	$header_width[16] = 10; $header_text[16] = "วันที่นำส่ง";			//	TRN_BOOK_DATE;
	$header_width[17] = 10; $header_text[17] = "โครงการ";				//	TRN_PROJECT_NAME;
	$header_width[18] = 30; $header_text[18] = "หลักสูตรอื่นๆ";		//	TRN_COURSE_NAME;
	$header_width[19] = 30; $header_text[19] = "วุฒิที่ได้รับ";			//	TRN_DEGREE_RECEIVE;
	$header_width[20] = 10; $header_text[20] = "คะแนน";				//	TRN_POINT;

	require_once("excel_headpart_subrtn.php");

	$search_arr_cond = array();

//	if (trim($MAIN_MINISTRY_ID))
//		$srhname_arr_cond[] = "ORG_ID_REF = ".trim($MAIN_MINISTRY_ID);
	if (trim($MAIN_DEPARTMENT_ID))
		$srhname_arr_cond[] = "a.DEPARTMENT_ID = ".trim($MAIN_DEPARTMENT_ID);
	if (trim($MAIN_ORG_ID))
		$srhname_arr_cond[] = "a.ORG_ID = ".trim($MAIN_ORG_ID);
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

	if (trim($TRN_ID))
		$search_arr_cond[] = "TRN_ID = ".trim($TRN_ID);
	if (trim($TRN_TYPE))
		$search_arr_cond[] = "TRN_TYPE = '".trim($TRN_TYPE)."'";
	if (trim($TRN_CODE))
		$search_arr_cond[] = "TRN_CODE = '".trim($TRN_CODE)."'";
	if (trim($TRN_NO))
		$search_arr_cond[] = "TRN_NO = '".trim($TRN_NO)."'";
	if (trim($TR_CODE))
		if (strpos(trim($TR_CODE),",")!==false) {
			$search_arr_cond[] = "TR_CODE in (".fill_arr_string($TR_CODE).")";
		} else
			$search_arr_cond[] = "TR_CODE = '".trim($TR_CODE)."'";
		
	if($TRN_STARTDATE){
		$TRN_STARTDATE =  save_date($TRN_STARTDATE);
		if ($arr_cond_list["TRN_STARTDATE"])
			$search_arr_cond[] = $arr_cond_list["TRN_STARTDATE"];
		else
			$search_arr_cond[] = "TRN_STARTDATE = '".trim($TRN_STARTDATE)."'";
	} // end if

	if($TRN_ENDDATE){
		$TRN_ENDDATE =  save_date($TRN_ENDDATE);
		if ($arr_cond_list["TRN_ENDDATE"])
			$search_arr_cond[] = $arr_cond_list["TRN_ENDDATE"];
		else
			$search_arr_cond[] = "TRN_ENDDATE = '".trim($TRN_ENDDATE)."'";
	} // end if

	if (trim($TRN_ORG))
		if ($arr_cond_list["TRN_ORG"])
			$search_arr_cond[] = $arr_cond_list["TRN_ORG"];
		else
			$search_arr_cond[] = "TRN_ORG = '%".trim($TRN_ORG)."%'";
	if (trim($TRN_PLACE))
		if ($arr_cond_list["TRN_PLACE"])
			$search_arr_cond[] = $arr_cond_list["TRN_PLACE"];
		else
			$search_arr_cond[] = "TRN_PLACE = '%".trim($TRN_PLACE)."%'";
	if (trim($CT_CODE))
		if (strpos(trim($CT_CODE),",")!==false)
			$search_arr_cond[] = "CT_CODE in (".trim($CT_CODE).")";
		else
			$search_arr_cond[] = "CT_CODE = '".trim($CT_CODE)."'";
	if (trim($TRN_FUND))
		if ($arr_cond_list["TRN_FUND"])
			$search_arr_cond[] = $arr_cond_list["TRN_FUND"];
		else
			$search_arr_cond[] = "TRN_FUND = '%".trim($TRN_FUND)."%'";
	if (trim($CT_CODE_FUND))
		if (strpos(trim($CT_CODE_FUND),",")!==false)
			$search_arr_cond[] = "CT_CODE_FUND in (".trim($CT_CODE_FUND).")";
		else
			$search_arr_cond[] = "CT_CODE_FUND = '".trim($CT_CODE_FUND)."'";
	if (trim($TRN_DAY))
		$search_arr_cond[] = "TRN_DAY = ".trim($TRN_DAY);
	if (trim($TRN_REMARK))
		if ($arr_cond_list["TRN_REMARK"])
			$search_arr_cond[] = $arr_cond_list["TRN_REMARK"];
		else
			$search_arr_cond[] = "TRN_REMARK = '%".trim($TRN_REMARK)."%'";
	if (trim($TRN_PASS) == 0 || trim($TRN_PASS) == 1)
		$search_arr_cond[] = "TRN_PASS = ".trim($TRN_PASS);

	if (trim($TRN_BOOK_NO))
		if ($arr_cond_list["TRN_BOOK_NO"])
			$search_arr_cond[] = $arr_cond_list["TRN_BOOK_NO"];
		else
			$search_arr_cond[] = "TRN_BOOK_NO = '".trim($TRN_BOOK_NO)."'";

	if(trim($TRN_BOOK_DATE)){
		$TRN_BOOK_DATE =  save_date($TRN_BOOK_DATE);
		if ($arr_cond_list["TRN_BOOK_DATE"])
			$search_arr_cond[] = $arr_cond_list["TRN_BOOK_DATE"];
		else
			$search_arr_cond[] = "TRN_BOOK_DATE = '".trim($TRN_BOOK_DATE)."'";
	} // end if

	if (trim($TRN_PROJECT_NAME))
		if ($arr_cond_list["TRN_PROJECT_NAME"])
			$search_arr_cond[] = $arr_cond_list["TRN_PROJECT_NAME"];
		else
			$search_arr_cond[] = "TRN_PROJECT_NAME = '%".trim($TRN_PROJECT_NAME)."%'";
	if (trim($TRN_COURSE_NAME))
		if ($arr_cond_list["TRN_COURSE_NAME"])
			$search_arr_cond[] = $arr_cond_list["TRN_COURSE_NAME"];
		else
			$search_arr_cond[] = "TRN_COURSE_NAME = '%".trim($TRN_COURSE_NAME)."%'";
	if (trim($TRN_DEGREE_RECEIVE))
		if ($arr_cond_list["TRN_DEGREE_RECEIVE"])
			$search_arr_cond[] = $arr_cond_list["TRN_DEGREE_RECEIVE"];
		else
			$search_arr_cond[] = "TRN_DEGREE_RECEIVE = '%".trim($TRN_DEGREE_RECEIVE)."%'";
	if (trim($TRN_POINT))
		if ($arr_cond_list["TRN_POINT"])
			$search_arr_cond[] = $arr_cond_list["TRN_POINT"];
		else
			$search_arr_cond[] = "TRN_POINT = ".trim($TRN_POINT);

	$search_text = implode(" and ",$search_arr_cond);
	if (trim($search_text))
		$search_text = "and ".trim($search_text);

	$cmd = "select PER_NAME, PER_SURNAME, b.* from PER_PERSONAL a, PER_TRAINING b where a.PER_ID=b.PER_ID $search_text order by a.PER_ID";
	$count_data = $db_dpis->send_cmd($cmd);
//	echo "[$cmd] len=".strlen($cmd)."<br>";

	$cnt = 0;
	$file_limit = 5000;
	$data_limit = 1000;
	$xlsRow = 0;
	$count_org_ref = 0;
	$sheet_no = 0; $sheet_no_text="";

	$report_code = "xls_TRAINING";

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
	$head = "การอบรม/ดูงาน/สัมมนา";
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

		$worksheet->write($xlsRow, 0, $data[TRN_ID], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		$worksheet->write($xlsRow, 1, $pname, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

		if ($data[TRN_TYPE]="1")  $TRN_TYPE_text = "อบรม";
		elseif ($data[TRN_TYPE]="2")  $TRN_TYPE_text = "ดูงาน";
		elseif ($data[TRN_TYPE]="3")  $TRN_TYPE_text = "สัมมนา";
		else  $TRN_TYPE_text = "";
		$worksheet->write($xlsRow, 2, $TRN_TYPE_text, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
			
		if(trim($data[TR_CODE])){
			$cmd1 = " select TR_NAME from PER_TRAIN where TR_CODE='".trim($data[TR_CODE])."' ";
			$db_dpis1->send_cmd($cmd1);
			if ($data_dpis1 = $db_dpis1->get_array())
				$TR_NAME_text = $data_dpis1[TR_NAME];
			else
				$TR_NAME_text = "";
		}  else  $TR_NAME_text = "";
		$worksheet->write($xlsRow, 3, $TR_NAME_text, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
			
		$worksheet->write($xlsRow, 4, $data[TRN_NO], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		$TRN_STARTDATE_text = show_date_format(trim($data[TRN_STARTDATE]),$DATE_DISPLAY);
		$worksheet->write($xlsRow, 5, $TRN_STARTDATE_text, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		$TRN_ENDDATE_text =  show_date_format(trim($data[TRN_ENDDATE]),$DATE_DISPLAY);
		$worksheet->write($xlsRow, 6, $TRN_ENDDATE_text, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		$worksheet->write($xlsRow, 7, $data[TRN_ORG], set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
		$worksheet->write($xlsRow, 8, $data[TRN_PLACE], set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
			
		if(trim($data[CT_CODE])){
			$cmd1 = " select CT_NAME from PER_COUNTRY where CT_CODE='".trim($data[CT_CODE])."' ";
			$db_dpis1->send_cmd($cmd1);
			if($data_dpis1 = $db_dpis1->get_array())
				$CT_NAME_text = $data_dpis1[CT_NAME];
			else
				$CT_NAME_text = "";
		}  else  $CT_NAME_text = "";
		$worksheet->write($xlsRow, 9, $CT_NAME_text, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		$worksheet->write($xlsRow, 10, $data[TRN_FUND], set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
		if(trim($data[CT_CODE_FUND])){
			$cmd1 = " select CT_NAME from PER_COUNTRY where CT_CODE='".trim($data[CT_CODE_FUND])."' ";
			$db_dpis1->send_cmd($cmd1);
			if($data_dpis1 = $db_dpis1->get_array())
				$CT_NAME_FUND_text = $data_dpis1[CT_NAME];
			else
				$CT_NAME_FUND_text = "";
		}  else  $CT_NAME_FUND_text = "";
		$worksheet->write($xlsRow, 11, $CT_NAME_FUND_text, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
			
		$worksheet->write($xlsRow, 12, $data[TRN_DAY], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		$worksheet->write($xlsRow, 13, $data[TRN_REMARK], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		if(trim($data[TRN_PASS])=="1")  $TRN_PASS_text = "ผ่าน";
		elseif (trim($data[TRN_PASS])=="0")  $TRN_PASS_text = "ไม่ผ่าน";
		else  $TRN_PASS_text = "";
		$worksheet->write($xlsRow, 14, $TRN_PASS_text, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		$worksheet->write($xlsRow, 15, $data[TRN_BOOK_NO], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		$TRN_BOOK_DATE_text =  show_date_format(trim($data[TRN_BOOK_DATE]),$DATE_DISPLAY);
		$worksheet->write($xlsRow, 16, $TRN_BOOK_DATE_text, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		$worksheet->write($xlsRow, 17, $data[TRN_PROJECT_NAME], set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
		$worksheet->write($xlsRow, 18, $data[TRN_COURSE_NAME], set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
		$worksheet->write($xlsRow, 19, $data[TRN_DEGREE_RECEIVE], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		$worksheet->write($xlsRow, 20, $data[TRN_POINT], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		
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