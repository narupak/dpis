<?	
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$header_width[0] = 15; $header_text[0] = "�������ҷ�դٳ";		//	TIMEH_ID;
	$header_width[1] = 30; $header_text[1] = "����";							//	PER_NAME+PER_SURNAME;
	$header_width[2] = 40; $header_text[2] = "���ҷ�դٳ";				//	TIME_CODE;
	$header_width[3] = 20; $header_text[3] = "�ӹǹ�ѹ������Ѻ��դٳ";	//	TIMEH_MINUS; 
	$header_width[4] = 20; $header_text[4] = "�Ţ˹ѧ���";				//	TIMEH_BOOK_NO; 
	$header_width[5] = 20; $header_text[5] = "�ѹ���˹ѧ���";				//	TIMEH_BOOK_DATE; 
	$header_width[6] = 40; $header_text[6] = "�����˵�";				//	TIMEH_REMARK;

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
	if (trim($PER_TYPE) || trim($PER_TYPE) > 0) // 0 ��ͷ����� ����ͧ���ҧ���͹�
		$search_arr_cond[] = "PER_TYPE = ".trim($PER_TYPE);

	if (trim($TIMEH_ID))
		$search_arr_cond[] = "TIMEH_ID = ".trim($TIMEH_ID);

	if (trim($TIME_CODE))
		if (strpos(trim($TIME_CODE),",")!==false)
			$search_arr_cond[] = "TIME_CODE in (".fill_arr_string($TIME_CODE).")";
		else
			$search_arr_cond[] = "TIME_CODE = '".trim($TIME_CODE)."'";

	if (trim($TIMEH_MINUS))
		if ($arr_cond_list["TIMEH_MINUS"])
			$search_arr_cond[] = $arr_cond_list["TIMEH_MINUS"];
		else
			$search_arr_cond[] = "TIMEH_MINUS = ".trim($TIMEH_MINUS);

	if (trim($TIMEH_BOOK_NO))
		if ($arr_cond_list["TIMEH_BOOK_NO"])
			$search_arr_cond[] = $arr_cond_list["TIMEH_BOOK_NO"];
		else
			$search_arr_cond[] = "TIMEH_BOOK_NO = '".trim($TIMEH_BOOK_NO)."'";

	if(trim($TIMEH_BOOK_DATE)){
		$TIMEH_BOOK_DATE =  save_date($TIMEH_BOOK_DATE);
		if ($arr_cond_list["TIMEH_BOOK_DATE"])
			$search_arr_cond[] = $arr_cond_list["TIMEH_BOOK_DATE"];
		else
			$search_arr_cond[] = "TIMEH_BOOK_DATE = '".trim($TIMEH_BOOK_DATE)."'";
	} // end if

	if (trim($TIMEH_REMARK))
		if ($arr_cond_list["TIMEH_REMARK"])
			$search_arr_cond[] = $arr_cond_list["TIMEH_REMARK"];
		else
			$search_arr_cond[] = "TIMEH_REMARK = '%".trim($TIMEH_REMARK)."%'";

	$search_text = "and ".implode(" and ",$search_arr_cond);

	$cmd = " select PER_NAME, PER_SURNAME, b.* from PER_PERSONAL a, PER_TIMEHIS b where a.PER_ID = b.PER_ID $search_text order by a.PER_ID ";
	$count_data = $db_dpis->send_cmd($cmd);
	echo "[$cmd] len=".strlen($cmd)."<br>";
	$cnt = 0;

	$cnt = 0;
	$file_limit = 5000;
	$data_limit = 1000;
	$xlsRow = 0;
	$count_org_ref = 0;
	$sheet_no = 0; $sheet_no_text="";

	$report_code = "xls_TIMEHIS";

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
	$head = "���ҷ�դٳ";
	call_header($data_count, $head);

	while ($data = $db_dpis->get_array()) {
		// �礨����� file ��� $file_limit
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
		// �礨������������� sheet ��� $data_limit
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
		
		$worksheet->write($xlsRow, 0, $data[TIMEH_ID], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		$pname = $data[PER_NAME]." ".$data[PER_SURNAME];
		$worksheet->write($xlsRow, 1, $pname, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

		if (trim($data[TIME_CODE])) {
			$cmd = " select TIME_NAME from PER_TIME where TIME_CODE='".trim($data[TIME_CODE])."' ";
			$db_dpis1->send_cmd($cmd);
			if ($data_dpis1 = $db_dpis1->get_array())
				$TIME_NAME_text = trim($data_dpis1[TIME_NAME]);
			else
				$TIME_NAME_text = "";
		} else  $TIME_NAME_text = "";
		$worksheet->write($xlsRow, 2, $TIME_NAME_text, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

		$worksheet->write($xlsRow, 3, trim($data[TIMEH_MINUS]), set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		$worksheet->write($xlsRow, 4, trim($data[TIMEH_BOOK_NO]), set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		$TIMEH_BOOK_DATE_text =  show_date_format(trim($data[TIMEH_BOOK_DATE]),$DATE_DISPLAY);
		$worksheet->write($xlsRow, 5, $TIMEH_BOOK_DATE_text, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		$worksheet->write($xlsRow, 6, trim($data[TIMEH_REMARK]), set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		$data_count++;
	} // end while loop
	if ($xlsRow==2) {
		$xlsRow++;
		$worksheet->write($xlsRow, 0, "***** ��辺������ *****", set_format("xlsFmtTitle", "B", "C", "", 0));
	}

	$xlsRow++;
	$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTitle", "B", "C", "", 0));
	$xlsRow++;
	$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTitle", "B", "C", "", 0));
	$xlsRow++;
	$worksheet->write($xlsRow, 0, "�ӹǹ������ $count_data ��¡��", set_format("xlsFmtTitle", "B", "L", "", 0));
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