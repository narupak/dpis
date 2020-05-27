<?	
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$header_width[0] = 15; $header_text[0] = "รหัสที่อยู่"; 					//	ADR_ID;
	$header_width[1] = 30; $header_text[1] = "ชื่อ";							//	PER_NAME+PER_SURNAME;
	$header_width[2] = 20; $header_text[2] = "บ้านเลขที่";				//	ADR_NO;
	$header_width[3] = 30; $header_text[3] = "หมู่บ้าน";					//	ADR_VILLAGE; 
	$header_width[4] = 30; $header_text[4] = "อาคาร";					//	ADR_BUILDING; 
	$header_width[5] = 30; $header_text[5] = "ซอย";						//	ADR_SOI;
	$header_width[6] = 30; $header_text[6] = "ถนน";						//	ADR_ROAD;
	$header_width[7] = 30; $header_text[7] = "หมู่ที่";						//	ADR_MOO;
	$header_width[8] = 30; $header_text[8] = "ตำบล/แขวง";				//	ADR_DISTRICT;
	$header_width[9] = 30; $header_text[9] = "อำเภอ/เขต";				//	AP_CODE;
	$header_width[10] = 30; $header_text[10] = "จังหวัด";				//	PV_CODE;
	$header_width[11] = 10; $header_text[11] = "รหัสไปรษณีย์";		//	ADR_POSTCODE;
	$header_width[12] = 30; $header_text[12] = "อีเมล์";					//	ADR_EMAIL;
	$header_width[13] = 15; $header_text[13] = "โทรศัพท์บ้าน";		//	ADR_HOME_TEL;
	$header_width[14] = 15; $header_text[14] = "โทรศัพท์ที่ทำงาน";	//	ADR_OFFICE_TEL;
	$header_width[15] = 15; $header_text[15] = "โทรศัพท์มือถือ";		//	ADR_MOBILE;
	$header_width[16] = 15; $header_text[16] = "โทรสาร";				//	ADR_FAX;
	$header_width[17] = 15; $header_text[17] = "ประเภทที่อยู่";			//	ADR_TYPE;
	$header_width[18] = 40; $header_text[18] = "หมายเหตุ";				//	PUN_REMARK;

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

	if (trim($ADR_ID))
		$search_arr_cond[] = "ADR_ID = ".trim($ADR_ID);

	if (trim($ADR_NO))
		if ($arr_cond_list["ADR_NO"])
			$search_arr_cond[] = $arr_cond_list["ADR_NO"];
		else
			$search_arr_cond[] = "ADR_NO = '%".trim($ADR_NO)."%'";
	if (trim($ADR_VILLAGE))
		if ($arr_cond_list["ADR_VILLAGE"])
			$search_arr_cond[] = $arr_cond_list["ADR_VILLAGE"];
		else
			$search_arr_cond[] = "ADR_VILLAGE = '%".trim($ADR_VILLAGE)."%'";
	if (trim($ADR_BUILDING))
		if ($arr_cond_list["ADR_BUILDING"])
			$search_arr_cond[] = $arr_cond_list["ADR_BUILDING"];
		else
			$search_arr_cond[] = "ADR_BUILDING = '%".trim($ADR_BUILDING)."%'";
	if (trim($ADR_SOI))
		if ($arr_cond_list["ADR_SOI"])
			$search_arr_cond[] = $arr_cond_list["ADR_SOI"];
		else
			$search_arr_cond[] = "ADR_SOI = '%".trim($ADR_SOI)."%'";
	if (trim($ADR_ROAD))
		if ($arr_cond_list["ADR_ROAD"])
			$search_arr_cond[] = $arr_cond_list["ADR_ROAD"];
		else
			$search_arr_cond[] = "ADR_ROAD = '%".trim($ADR_ROAD)."%'";
	if (trim($ADR_MOO))
		if ($arr_cond_list["ADR_MOO"])
			$search_arr_cond[] = $arr_cond_list["ADR_MOO"];
		else
			$search_arr_cond[] = "ADR_MOO = '%".trim($ADR_MOO)."%'";
	if (trim($ADR_DISTRICT))
		if ($arr_cond_list["ADR_DISTRICT"])
			$search_arr_cond[] = $arr_cond_list["ADR_DISTRICT"];
		else
			$search_arr_cond[] = "ADR_DISTRICT = '%".trim($ADR_DISTRICT)."%'";

	if (trim($AP_CODE))
		if (strpos(trim($AP_CODE),",")!==false)
			$search_arr_cond[] = "b.AP_CODE in (".fill_arr_string($AP_CODE).")";
		else
			$search_arr_cond[] = "b.AP_CODE = '".trim($AP_CODE)."'";
	if (trim($PV_CODE))
		if (strpos(trim($PV_CODE),",")!==false)
			$search_arr_cond[] = "b.PV_CODE in (".fill_arr_string($PV_CODE).")";
		else
			$search_arr_cond[] = "b.PV_CODE = '".trim($PV_CODE)."'";

	if (trim($ADR_POSTCODE))
		if ($arr_cond_list["ADR_POSTCODE"])
			$search_arr_cond[] = $arr_cond_list["ADR_POSTCODE"];
		else
			$search_arr_cond[] = "ADR_POSTCODE = '%".trim($ADR_POSTCODE)."%'";
	if (trim($ADR_EMAIL))
		if ($arr_cond_list["ADR_EMAIL"])
			$search_arr_cond[] = $arr_cond_list["ADR_EMAIL"];
		else
			$search_arr_cond[] = "ADR_EMAIL = '%".trim($ADR_EMAIL)."%'";
	if (trim($ADR_HOME_TEL))
		if ($arr_cond_list["ADR_HOME_TEL"])
			$search_arr_cond[] = $arr_cond_list["ADR_HOME_TEL"];
		else
			$search_arr_cond[] = "ADR_HOME_TEL = '%".trim($ADR_HOME_TEL)."%'";
	if (trim($ADR_OFFICE_TEL))
		if ($arr_cond_list["ADR_OFFICE_TEL"])
			$search_arr_cond[] = $arr_cond_list["ADR_OFFICE_TEL"];
		else
			$search_arr_cond[] = "ADR_OFFICE_TEL = '%".trim($ADR_OFFICE_TEL)."%'";
	if (trim($ADR_MOBILE))
		if ($arr_cond_list["ADR_MOBILE"])
			$search_arr_cond[] = $arr_cond_list["ADR_MOBILE"];
		else
			$search_arr_cond[] = "ADR_MOBILE = '%".trim($ADR_MOBILE)."%'";
	if (trim($ADR_FAX))
		if ($arr_cond_list["ADR_FAX"])
			$search_arr_cond[] = $arr_cond_list["ADR_FAX"];
		else
			$search_arr_cond[] = "ADR_FAX = '%".trim($ADR_FAX)."%'";

	if (trim($ADR_TYPE))
		$search_arr_cond[] = "ADR_TYPE = '".trim($ADR_TYPE)."'";

	if (trim($ADR_REMARK))
		if ($arr_cond_list["ADR_REMARK"])
			$search_arr_cond[] = $arr_cond_list["ADR_REMARK"];
		else
			$search_arr_cond[] = "ADR_REMARK = '%".trim($ADR_REMARK)."%'";

	$search_text = "and ".implode(" and ",$search_arr_cond);

	$cmd = " select PER_NAME, PER_SURNAME, b.* from PER_PERSONAL a, PER_ADDRESS b where a.PER_ID = b.PER_ID $search_text order by a.PER_ID ";
	$count_data = $db_dpis->send_cmd($cmd);
	echo "[$cmd] len=".strlen($cmd)."<br>";
	$cnt = 0;

	$cnt = 0;
	$file_limit = 5000;
	$data_limit = 1000;
	$xlsRow = 0;
	$count_org_ref = 0;
	$sheet_no = 0; $sheet_no_text="";

	$report_code = "xls_ADDRESS";

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
	$head = "ที่อยู่";
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
		
		$worksheet->write($xlsRow, 0, $data[ADR_ID], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		$pname = $data[PER_NAME]." ".$data[PER_SURNAME];
		$worksheet->write($xlsRow, 1, $pname, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
		$worksheet->write($xlsRow, 2, trim($data[ADR_NO]), set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
		$worksheet->write($xlsRow, 3, trim($data[ADR_VILLAGE]), set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
		$worksheet->write($xlsRow, 4, trim($data[ADR_BUILDING]), set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
		$worksheet->write($xlsRow, 5, trim($data[ADR_SOI]), set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
		$worksheet->write($xlsRow, 6, trim($data[ADR_ROAD]), set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
		$worksheet->write($xlsRow, 7, trim($data[ADR_MOO]), set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
		$worksheet->write($xlsRow, 8, trim($data[ADR_DISTRICT]), set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
		
		if (trim($data[AP_CODE])) {
			$cmd = " select  AP_NAME from PER_AMPHUR  where AP_CODE='".trim($data[AP_CODE])."' ";
			$db_dpis1->send_cmd($cmd);
			if ($data_dpis1 = $db_dpis1->get_array())
				$AP_NAME_text = trim($data_dpis1[AP_NAME]);
			else
				$AP_NAME_text = "";
		} else  $AP_NAME_text = "";
		$worksheet->write($xlsRow, 9, $AP_NAME_text, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
		
		if (trim($data[PV_CODE])) {
			$cmd = " select  PV_NAME from PER_PROVINCE  where PV_CODE='".trim($data[PV_CODE])."' ";
			$db_dpis1->send_cmd($cmd);
			if ($data_dpis1 = $db_dpis1->get_array())
				$PV_NAME_text = trim($data_dpis1[PV_NAME]);
			else
				$PV_NAME_text = "";
		} else  $PV_NAME_text = "";
		$worksheet->write($xlsRow, 10, $PV_NAME_text, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

		$worksheet->write($xlsRow, 11, trim($data[ADR_POSTCODE]), set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		$worksheet->write($xlsRow, 12, trim($data[ADR_EMAIL]), set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
		$worksheet->write($xlsRow, 13, trim($data[ADR_HOME_TEL]), set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
		$worksheet->write($xlsRow, 14, trim($data[ADR_OFFICE_TEL]), set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
		$worksheet->write($xlsRow, 15, trim($data[ADR_MOBILE]), set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
		$worksheet->write($xlsRow, 16, trim($data[ADR_FAX]), set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

		if (trim($data[ADR_TYPE])==1)  $ADR_TYPE_text = "ที่อยู่ปัจจุบัน";
		elseif (trim($data[ADR_TYPE])==2)  $ADR_TYPE_text = "ที่อยู่ตามทะเบียนบ้าน";
		elseif (trim($data[ADR_TYPE])==3)  $ADR_TYPE_text = "ที่อยู่ตามบัตรประชาชน";
		elseif (trim($data[ADR_TYPE])==4)  $ADR_TYPE_text = "ที่อยู่ตามภูมิลำเนา";
		else  $ADR_TYPE_text = "";
		$worksheet->write($xlsRow, 17, $ADR_TYPE_text, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

		$worksheet->write($xlsRow, 18, trim($data[ADR_REMARK]), set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
		
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