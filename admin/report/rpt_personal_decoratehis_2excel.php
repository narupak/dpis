<?	
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

//	DEH_ID, PER_ID, DEH_DATE, DC_CODE, DEH_GAZETTE, DEH_RECEIVE_FLAG, DEH_RECEIVE_DATE, DEH_RETURN_FLAG, DEH_RETURN_DATE, DEH_RETURN_TYPE, DEH_POSITION, DEH_ORG, DEH_BOOK_NO, DEH_BOOK_DATE, DEH_REMARK
	$header_width[0] = 15; $header_text[0] = "รหัสรายการ";				//	DEH_ID;
	$header_width[1] = 30; $header_text[1] = "ชื่อ";							//	PER_NAME+PER_SURNAME;
	$header_width[2] = 20; $header_text[2] = "วันที่รับ";					//	DEH_DATE;
	$header_width[3] = 20; $header_text[3] = "เครื่องราชฯ";				//	DC_CODE; 
	$header_width[4] = 20; $header_text[4] = "ราชกิจจานุเบกษา";		//	DEH_GAZETTE;
	$header_width[5] = 30; $header_text[5] = "การรับเครื่องราชฯ";		//	DEH_RECEIVE_FLAG;
	$header_width[6] = 30; $header_text[6] = "วันที่ราชกิจจานุเบกษา";	//	DEH_RECEIVE_DATE;
	$header_width[7] = 30; $header_text[7] = "การคืนเครื่องราชฯ";	//	DEH_RETURN_FLAG;
	$header_width[8] = 20; $header_text[8] = "วันที่คืนเครื่องราชฯ";	//	DEH_RETURN_DATE;
	$header_width[9] = 20; $header_text[9] = "ประเภทการคืน";			//	DEH_RETURN_TYPE;
	$header_width[10] = 20; $header_text[10] = "ตำแหน่ง";				//	DEH_POSITION;
	$header_width[11] = 10; $header_text[11] = "สังกัด";					//	DEH_ORG;
	$header_width[12] = 10; $header_text[12] = "เลขที่นำส่ง";			//	DEH_BOOK_NO;
	$header_width[13] = 10; $header_text[13] = "วันที่หนังสือ";			//	DEH_BOOK_DATE;
	$header_width[14] = 40; $header_text[14] = "หมายเหตุ";				//	DEH_REMARK;

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

	if (trim($DEH_ID))
		$search_arr_cond[] = "DEH_ID = ".trim($DEH_ID);

	if(trim($DEH_DATE)){
		$DEH_DATE =  save_date($DEH_DATE);
		if ($arr_cond_list["DEH_DATE"])
			$search_arr_cond[] = $arr_cond_list["DEH_DATE"];
		else
			$search_arr_cond[] = "DEH_DATE = '".trim($DEH_DATE)."'";
	} // end if

	if (trim($DC_CODE))
		if (strpos(trim($DC_CODE),",")!==false)
			$search_arr_cond[] = "DC_CODE in (".fill_arr_string($DC_CODE).")";
		else
			$search_arr_cond[] = "DC_CODE = '".trim($DC_CODE)."'";

	if (trim($DEH_GAZETTE))
		if ($arr_cond_list["DEH_GAZETTE"])
			$search_arr_cond[] = $arr_cond_list["DEH_GAZETTE"];
		else
			$search_arr_cond[] = "DEH_GAZETTE = '".trim($DEH_GAZETTE)."'";

	if (trim($DEH_RECEIVE_FLAG)==0 || trim($DEH_RECEIVE_FLAG)==1)
		$search_arr_cond[] = "DEH_RECEIVE_FLAG = ".trim($DEH_RECEIVE_FLAG);

	if(trim($DEH_RECEIVE_DATE)){
		$DEH_RECEIVE_DATE =  save_date($DEH_RECEIVE_DATE);
		if ($arr_cond_list["DEH_RECEIVE_DATE"])
			$search_arr_cond[] = $arr_cond_list["DEH_RECEIVE_DATE"];
		else
			$search_arr_cond[] = "DEH_RECEIVE_DATE = '".trim($DEH_RECEIVE_DATE)."'";
	} // end if

	if (trim($DEH_RETURN_FLAG))
		$search_arr_cond[] = "DEH_RETURN_FLAG = ".trim($DEH_RETURN_FLAG);

	if(trim($DEH_RETURN_DATE)){
		$DEH_RETURN_DATE =  save_date($DEH_RETURN_DATE);
		if ($arr_cond_list["DEH_RETURN_DATE"])
			$search_arr_cond[] = $arr_cond_list["DEH_RETURN_DATE"];
		else
			$search_arr_cond[] = "DEH_RETURN_DATE = '".trim($DEH_RETURN_DATE)."'";
	} // end if

	if (trim($DEH_RETURN_TYPE))
		$search_arr_cond[] = "DEH_RETURN_TYPE = ".trim($DEH_RETURN_TYPE);

	if (trim($DEH_POSITION))
		if ($arr_cond_list["DEH_POSITION"])
			$search_arr_cond[] = $arr_cond_list["DEH_POSITION"];
		else
			$search_arr_cond[] = "DEH_POSITION = '%".trim($DEH_POSITION)."%'";

	if (trim($DEH_ORG))
		if ($arr_cond_list["DEH_ORG"])
			$search_arr_cond[] = $arr_cond_list["DEH_ORG"];
		else
			$search_arr_cond[] = "DEH_ORG = '%".trim($DEH_ORG)."%'";

	if (trim($DEH_BOOK_NO))
		if ($arr_cond_list["DEH_BOOK_NO"])
			$search_arr_cond[] = $arr_cond_list["DEH_BOOK_NO"];
		else
			$search_arr_cond[] = "DEH_BOOK_NO = '".trim($DEH_BOOK_NO)."'";

	if(trim($DEH_BOOK_DATE)){
		$DEH_BOOK_DATE =  save_date($DEH_BOOK_DATE);
		if ($arr_cond_list["DEH_BOOK_DATE"])
			$search_arr_cond[] = $arr_cond_list["DEH_BOOK_DATE"];
		else
			$search_arr_cond[] = "DEH_BOOK_DATE = '".trim($DEH_BOOK_DATE)."'";
	} // end if

	if (trim($DEH_REMARK))
		if ($arr_cond_list["DEH_REMARK"])
			$search_arr_cond[] = $arr_cond_list["DEH_REMARK"];
		else
			$search_arr_cond[] = "DEH_REMARK = '".trim($DEH_REMARK)."'";

	$search_text = "and ".implode(" and ",$search_arr_cond);

	$cmd = " select PER_NAME, PER_SURNAME, b.* from PER_PERSONAL a, PER_DECORATEHIS b where a.PER_ID = b.PER_ID $search_text order by a.PER_ID ";
	$count_data = $db_dpis->send_cmd($cmd);
	echo "[$cmd] len=".strlen($cmd)."<br>";
	$cnt = 0;

	$cnt = 0;
	$file_limit = 5000;
	$data_limit = 1000;
	$xlsRow = 0;
	$count_org_ref = 0;
	$sheet_no = 0; $sheet_no_text="";

	$report_code = "xls_DECORATEHIS";

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
	$head = "เครื่องราชฯ";
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
		
		$worksheet->write($xlsRow, 0, $data[DEH_ID], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		$pname = $data[PER_NAME]." ".$data[PER_SURNAME];
		$worksheet->write($xlsRow, 1, $pname, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

		$DEH_DATE_text = show_date_format(trim($data[DEH_DATE]),$DATE_DISPLAY);
		$worksheet->write($xlsRow, 2, $DEH_DATE_text, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		if (trim($data[DC_CODE])) {
			$cmd = " select DC_NAME from PER_DECORATION where DC_CODE='".trim($data[DC_CODE])."' ";
			$db_dpis1->send_cmd($cmd);
			if ($data_dpis1 = $db_dpis1->get_array())
				$DC_NAME_text = trim($data_dpis1[DC_NAME]);
			else
				$DC_NAME_text = "";
		} else  $DC_NAME_text = "";
		$worksheet->write($xlsRow, 3, $DC_NAME_text, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

		$worksheet->write($xlsRow, 4, trim($data[DEH_GAZETTE]), set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

		if (trim($data[DEH_RECEIVE_FLAG])==0)  $DEH_RECEIVE_FLAG_text = "รับแล้ว";
		elseif (trim($data[DEH_RECEIVE_FLAG])==1)  $DEH_RECEIVE_FLAG_text = "ยังไม่ได้รับ";
		else  $DEH_RECEIVE_FLAG_text = "";
		$worksheet->write($xlsRow, 5, $DEH_RECEIVE_FLAG_text, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

		$DEH_RECEIVE_DATE_text = show_date_format(trim($data[DEH_RECEIVE_DATE]),$DATE_DISPLAY);
		$worksheet->write($xlsRow, 6, $DEH_RECEIVE_DATE_text, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		if (trim($data[DEH_RETURN_FLAG])==1)  $DEH_RETURN_FLAG_text = "คืนแล้ว";
		elseif (trim($data[DEH_RETURN_FLAG])==2)  $DEH_RETURN_FLAG_text = "ยังไม่ได้คืน";
		else  $DEH_RETURN_FLAG_text = "";
		$worksheet->write($xlsRow, 7, $DEH_RETURN_FLAG_text, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

		$DEH_RETURN_DATE_text = show_date_format(trim($data[DEH_RETURN_DATE]),$DATE_DISPLAY);
		$worksheet->write($xlsRow, 8, $DEH_RETURN_DATE_text, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		if (trim($data[DEH_RETURN_TYPE])==1)  $DEH_RETURN_TYPE_text = "คืนเป็นเครื่องราชฯ";
		elseif (trim($data[DEH_RETURN_TYPE])==2)  $DEH_RETURN_TYPE_text = "คืนเป็นเงินสด";
		else  $DEH_RETURN_TYPE_text = "";
		$worksheet->write($xlsRow, 9, $DEH_RETURN_TYPE_text, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

		$worksheet->write($xlsRow, 10, trim($data[DEH_POSITION]), set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
		$worksheet->write($xlsRow, 11, trim($data[DEH_ORG]), set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
		$worksheet->write($xlsRow, 12, trim($data[DEH_BOOK_NO]), set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		$DEH_BOOK_DATE_text= show_date_format(trim($data[DEH_BOOK_DATE]),$DATE_DISPLAY);
		$worksheet->write($xlsRow, 13, $DEH_BOOK_DATE_text, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		$worksheet->write($xlsRow, 14, trim($data[DEH_REMARK]), set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

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