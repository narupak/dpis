<?	
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	$header_width[0] = 15; $header_text[0] = "รหัสการศึกษา"; 			//	EDU_ID;
	$header_width[1] = 30; $header_text[1] = "ชื่อ";							//	PER_NAME+PER_SURNAME;
	$header_width[2] = 10; $header_text[2] = "ลำดับที่";					//	EDU_SEQ; 
	$header_width[3] = 10; $header_text[3] = "ปีที่เริ่ม";					//	EDU_STARTYEAR;
	$header_width[4] = 10; $header_text[4] = "ปีที่จบ";						//	EDU_ENDYEAR; 
	$header_width[5] = 20; $header_text[5] = "ประเภททุน";				//	ST_CODE;
	$header_width[6] = 20; $header_text[6] = "ประเทศเจ้าของทุน";	//	CT_CODE;
	$header_width[7] = 30; $header_text[7] = "หน่วงงานที่ให้ทุน";		//	EDU_FUND;
	$header_width[8] = 30; $header_text[8] = "วุฒิการศึกษา";			//	EN_CODE;
	$header_width[9] = 30; $header_text[9] = "สาขาวิชา";				//	EM_CODE;
	$header_width[10] = 30; $header_text[10] = "สถานศึกษา";			//	INS_CODE;
	$header_width[11] = 30; $header_text[11] = "วุฒิที่ใช้บรรจุ";		//	EDU_TYPE;
	$header_width[12] = 30; $header_text[12] = "ระดับการศึกษา";	//	EL_CODE;
	$header_width[13] = 15; $header_text[13] = "วันที่จบ";				//	EDU_ENDDATE;
	$header_width[14] = 10; $header_text[14] = "เกรดเฉลี่ย";			//	EDU_GRADE;
	$header_width[15] = 10; $header_text[15] = "เกียรตินิยม";			//	EDU_HONOR
	$header_width[16] = 20; $header_text[16] = "เลขที่หนังสือนำส่ง";	//	EDU_BOOK_NO
	$header_width[17] = 10; $header_text[17] = "วันที่หนังสือ";			//	EDU_BOOK_DATE
	$header_width[18] = 30; $header_text[18] = "หมายเหตุ";				//	EDU_REMARK;
	$header_width[19] = 30; $header_text[19] = "สถานศึกษาอื่น ๆ";	//	EDU_INSTITUTE

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

	if (trim($EDU_ID))
		$search_arr_cond[] = "EDU_ID = ".trim($EDU_ID);
	if (trim($EDU_SEQ))
		$search_arr_cond[] = "EDU_SEQ = '".trim($EDU_SEQ)."'";
	if (trim($EDU_STARTYEAR))
		if ($arr_cond_list["EDU_STARTYEAR"])
			$search_arr_cond[] = $arr_cond_list["EDU_STARTYEAR"];
		else
			$search_arr_cond[] = "EDU_STARTYEAR = '".trim($EDU_STARTYEAR)."'";
	if (trim($EDU_ENDYEAR))
		if ($arr_cond_list["EDU_ENDYEAR"])
			$search_arr_cond[] = $arr_cond_list["EDU_ENDYEAR"];
		else
			$search_arr_cond[] = "EDU_ENDYEAR = '".trim($EDU_ENDYEAR)."'";
	if (trim($ST_CODE))
		if (strpos(trim($ST_CODE),",")!==false)
			$search_arr_cond[] = "ST_CODE in (".trim($ST_CODE).")";
		else
			$search_arr_cond[] = "ST_CODE = '".trim($ST_CODE)."'";
	if (trim($CT_CODE))
		if (strpos(trim($CT_CODE),",")!==false)
			$search_arr_cond[] = "CT_CODE in (".trim($CT_CODE).")";
		else
			$search_arr_cond[] = "CT_CODE = '".trim($CT_CODE)."'";
	if (trim($EDU_FUND))
		if ($arr_cond_list["EDU_FUND"])
			$search_arr_cond[] = $arr_cond_list["EDU_FUND"];
		else
			$search_arr_cond[] = "EDU_FUND = '".trim($EDU_FUND)."'";
	if (trim($EN_CODE))
		if (strpos(trim($EN_CODE),",")!==false)
			$search_arr_cond[] = "EN_CODE in (".trim($EN_CODE).")";
		else
			$search_arr_cond[] = "EN_CODE = '".trim($EN_CODE)."'";
	if (trim($EM_CODE))
		if (strpos(trim($EM_CODE),",")!==false)
			$search_arr_cond[] = "EM_CODE in (".trim($EM_CODE).")";
		else
			$search_arr_cond[] = "EM_CODE = '".trim($EM_CODE)."'";
	if (trim($INS_CODE))
		if (strpos(trim($INS_CODE),",")!==false)
			$search_arr_cond[] = "INS_CODE in (".trim($INS_CODE).")";
		else
			$search_arr_cond[] = "INS_CODE = '".trim($INS_CODE)."'";

	if (trim($EDU_TYPE)) {
		$arr_edu_search = (array) null;
		for ($i=0; $i<count($EDU_TYPE); $i++) {
			$arr_edu_search[] = "EDU_TYPE like '%".$EDU_TYPE[$i]."%'";
		} // end loop for $i
		$edu_type_srh = "(".implode(" or ", $arr_edu_search).")";
		$search_arr_cond[] = $edu_type_srh;
	}

	if (trim($EL_CODE))
		if (strpos(trim($EL_CODE),",")!==false)
			$search_arr_cond[] = "EL_CODE in (".trim($EL_CODE).")";
		else
			$search_arr_cond[] = "EL_CODE = '".trim($EL_CODE)."'";

	if($EDU_ENDDATE){
		$EDU_ENDDATE =  save_date($EDU_ENDDATE);
		if ($arr_cond_list["EDU_ENDDATE"])
			$search_arr_cond[] = $arr_cond_list["EDU_ENDDATE"];
		else
			$search_arr_cond[] = "EDU_ENDDATE = '".trim($EDU_ENDDATE)."'";
	} // end if

	if (trim($EDU_GRADE))
		$search_arr_cond[] = "EDU_GRADE = ".trim($GRADE);
	if (trim($EDU_HONOR))
		$search_arr_cond[] = "EDU_HONOR = '".trim($EDU_HONOR)."'";
	if (trim($EDU_BOOK_NO))
		if ($arr_cond_list["EDU_BOOK_NO"])
			$search_arr_cond[] = $arr_cond_list["EDU_BOOK_NO"];
		else
			$search_arr_cond[] = "EDU_BOOK_NO = '".trim($EDU_BOOK_NO)."'";
		
	if(trim($EDU_BOOK_DATE)){
		$EDU_BOOK_DATE =  save_date($EDU_BOOK_DATE);
		if ($arr_cond_list["EDU_BOOK_DATE"])
			$search_arr_cond[] = $arr_cond_list["EDU_BOOK_DATE"];
		else
			$search_arr_cond[] = "EDU_BOOK_DATE = '".trim($EDU_BOOK_DATE)."'";
	} // end if
		
	if (trim($EDU_REMARK))
		if ($arr_cond_list["EDU_REMARK"])
			$search_arr_cond[] = $arr_cond_list["EDU_REMARK"];
		else
			$search_arr_cond[] = "EDU_REMARK = '%".trim($EDU_REMARK)."%'";
	if (trim($EDU_INSTITUTE))
		if ($arr_cond_list["EDU_INSTITUTE"])
			$search_arr_cond[] = $arr_cond_list["EDU_INSTITUTE"];
		else
			$search_arr_cond[] = "EDU_INSTITUTE = '%".trim($EDU_INSTITUTE)."%'";

	$search_text = implode(" and ",$search_arr_cond);
	if (trim($search_text))
		$search_text = "and ".trim($search_text);

	$cmd = "select PER_NAME, PER_SURNAME, b.* from PER_PERSONAL a,  PER_EDUCATE b where a.PER_ID = b.PER_ID $search_text order by a.PER_ID";
	$count_data = $db_dpis->send_cmd($cmd);
//	echo "[$cmd] len=".strlen($cmd)."<br>";

	$cnt = 0;
	$file_limit = 5000;
	$data_limit = 1000;
	$xlsRow = 0;
	$count_org_ref = 0;
	$sheet_no = 0; $sheet_no_text="";

	$report_code = "xls_EDUCATEHIS";

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
	$head = "การดำรงตำแหน่ง";
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

		$worksheet->write($xlsRow, 0, $data[EDU_ID], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		$worksheet->write($xlsRow, 1, $pname, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
		$worksheet->write($xlsRow, 2, $data[EDU_SEQ], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		$worksheet->write($xlsRow, 3, $data[EDU_STARTYEAR], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		$worksheet->write($xlsRow, 4, $data[EDU_ENDYEAR], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		if (trim($data[ST_NAME])) {
			$cmd1 ="select ST_NAME from PER_SCHOLARTYPE where ST_CODE= '".trim($data[ST_CODE])."' ";
			$db_dpis1->send_cmd($cmd1);
			if ($data_dpis1 = $db_dpis1->get_array())
				$ST_NAME_text = trim($data_dpis1[ST_NAME]);
			else
				$ST_NAME_text = "";
		} else  $ST_NAME_text = "";
		$worksheet->write($xlsRow, 5, $ST_NAME_text, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

		if(trim($data[CT_CODE])){
			$cmd1 = " select CT_NAME from PER_COUNTRY where CT_CODE='".trim($data[CT_CODE])."' ";
			$db_dpis1->send_cmd($cmd1);
			if ($data_dpis1 = $db_dpis1->get_array())
				$CT_NAME_text = $data_dpis1[CT_NAME];
			else
				$CT_NAME_text = "";
		} else  $CT_NAME_text = "";
		$worksheet->write($xlsRow, 6, $CT_NAME_text, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

		$worksheet->write($xlsRow, 7, $data[EDU_FUND], set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

		if(trim($data[EN_CODE])){
			$cmd1 = " select EN_NAME from PER_EDUCNAME where EN_CODE='".trim($data[EN_CODE])."' ";
			$db_dpis1->send_cmd($cmd1);
			if ($data_dpis1 = $db_dpis1->get_array())
				$EN_NAME_text = $data_dpis1[EN_NAME];
			else
				$EN_NAME_text = "";
		}  else  $EN_NAME_text = "";
		$worksheet->write($xlsRow, 8, $EN_NAME_text, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

		if(trim($data[EM_CODE])){
			$cmd1 = " select EM_NAME from PER_EDUCMAJOR where EM_CODE='".trim($data[EM_CODE])."' ";
			$db_dpis1->send_cmd($cmd1);
			if ($data_dpis1 = $db_dpis1->get_array())
				$EM_NAME_text = $data_dpis1[EM_NAME];
			else
				$EM_NAME_text = "";
		}  else  $EM_NAME_text = "";
		$worksheet->write($xlsRow, 9, $EM_NAME_text, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

		if(trim($data[INS_CODE])){
			$cmd1 = " select INS_NAME from PER_INSTITUTE where INS_CODE='".trim($data[INS_CODE])."' ";
			$db_dpis1->send_cmd($cmd1);
			if ($data_dpis1 = $db_dpis1->get_array())
				$INS_NAME = $data_dpis1[INS_NAME];
			else
				$INS_NAME = "";
		}  $INS_NAME = "";
		$worksheet->write($xlsRow, 10, $INS_NAME_text, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

		if(trim($data[EDU_TYPE])){ 
			$EDU_TYPE_text = "";
			$arr_type = array("บรรจุ", "ปัจจุบัน", "อื่น ๆ", "สูงสุด", "เพิ่มเติม");
			$arr_edu = explode("||",trim($data[EDU_TYPE]));
			for ($i=0; $i<count($arr_edu); $i++) {
				if ($arr_edu[$i]) {
					$n = (int) $arr_edu[$i];
					$n--;
					$EDU_TYPE_text .= $arr_type[$n].",";
				}
			} // end loop for $i
			$EDU_TYPE_text = substr($EDU_TYPE_text,0,strlen($EDU_TYPE_text)-1);
		}
		$worksheet->write($xlsRow, 11, $EDU_TYPE_text, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
			
		if(trim($data[EL_CODE])){
			$cmd1 = " select EL_NAME from PER_EDUCLEVEL where EL_CODE='".trim($data[EL_CODE])."' ";
			$db_dpis1->send_cmd($cmd1);
			if ($data_dpis1 = $db_dpis1->get_array())
				$EL_NAME_text = $data_dpis1[EL_NAME];
			else
				$EL_NAME_text = "";
		}  else  $EL_NAME_text = "";
		$worksheet->write($xlsRow, 12, $EL_NAME_text, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

		$EDU_ENDDATE_text = show_date_format(trim($data[EDU_ENDDATE]),$DATE_DISPLAY);
		$worksheet->write($xlsRow, 13, $EDU_ENDDATE_text, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		
		$worksheet->write($xlsRow, 14, $data[EDU_GRADE], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		$worksheet->write($xlsRow, 15, $data[EDU_HONOR], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		$worksheet->write($xlsRow, 16, $data[EDU_BOOK_NO], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		$EDU_BOOK_DATE_text = show_date_format(trim($data[EDU_BOOK_DATE]),$DATE_DISPLAY);
		$worksheet->write($xlsRow, 17, $EDU_BOOK_DATE_text, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		$worksheet->write($xlsRow, 18, $data[EDU_REMARK], set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
		$worksheet->write($xlsRow, 19, $data[EDU_INSTITUTE], set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
		
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