<?	
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$header_width[0] = 20; $header_text[0] = "รหัสเงินเดือน"; 			//	SAH_ID;
	$header_width[1] = 20; $header_text[1] = "ชื่อ";							//	PER_NAME+PER_SURNAME;
	$header_width[2] = 20; $header_text[2] = "วันที่มีผล";					//	SAH_EFFECTIVEDATE; 
	$header_width[3] = 20; $header_text[3] = "เคลื่อนไหว";				//	MOV_CODE;; 
	$header_width[4] = 20; $header_text[4] = "อัตราเงินเดือน";			//	SAH_SALARY;
	$header_width[5] = 20; $header_text[5] = "คำสั่งเลขที่";				//	SAH_DOCNO;
	$header_width[6] = 20; $header_text[6] = "วันที่คำสั่ง";				//	SAH_DOCDATE;
	$header_width[7] = 20; $header_text[7] = "วันที่สิ้นสุด";				//	SAH_ENDDATE;
//	$header_width[8] = 20; $header_text[8] = "บัตรประชาชน";			//	PER_CARDNO;
	$header_width[8] = 20; $header_text[8] = "% ที่เลื่อน";				//	SAH_PERCENT_UP;
	$header_width[9] = 20; $header_text[9] = "เงินที่เลื่อน";				//	SAH_SALARY_UP;
	$header_width[10] = 20; $header_text[10] = "เงินตอบแทนพิเศษ";	//	SAH_SALARY_EXTRA;
	$header_width[11] = 20; $header_text[11] = "ลำดับที่";				//	SAH_SEQ_NO;
	$header_width[12] = 20; $header_text[12] = "หมายเหตุ";				//	SAH_REMARK;
	$header_width[13] = 20; $header_text[13] = "ระดับตำแหน่ง";		//	LEVEL_NO;
	$header_width[14] = 20; $header_text[14] = "เลขที่ตำแหน่ง";		//	SAH_POS_NO;
	$header_width[15] = 20; $header_text[15] = "ตำแหน่ง";				//	SAH_POSITION;
	$header_width[16] = 20; $header_text[16] = "สังกัด";					//	SAH_ORG;
	$header_width[17] = 20; $header_text[17] = "ประเภทเงิน";			//	EX_CODE;
	$header_width[18] = 20; $header_text[18] = "เลขถือจ่าย";			//	SAH_PAY_NO;
	$header_width[19] = 20; $header_text[19] = "ฐานการคำนวณ";	//	SAH_SALARY_MIDPOINT;
	$header_width[20] = 20; $header_text[20] = "ปีงบประมาณ";		//	SAH_KF_YEAR;
	$header_width[21] = 20; $header_text[21] = "รอบการประเมิน";	//	SAH_KF_CYCLE = 1 or 2;
	$header_width[22] = 20; $header_text[22] = "ผลการประเมิน";		//	SAH_TOTAL_SCORE;
	$header_width[23] = 20; $header_text[23] = "เงินเดือนล่าสุด";		//	SAH_LAST_SALARY;
	$header_width[24] = 20; $header_text[24] = "จำนวนขั้นเงินเดือน";		//	SM_CODE;
	$header_width[25] = 20; $header_text[25] = "ลำดับที่คำสั่ง";		//	SAH_CMD_SEQ;

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

	if (trim($SAH_ID))
		$search_arr_cond[] = "SAH_ID = ".trim($SAH_ID);
		
	if($SAH_EFFECTIVEDATE){
		$SAH_EFFECTIVEDATE =  save_date($SAH_EFFECTIVEDATE);
		if ($arr_cond_list["SAH_EFFECTIVEDATE"])
			$search_arr_cond[] = $arr_cond_list["SAH_EFFECTIVEDATE"];
		else
			$search_arr_cond[] = "SAH_EFFECTIVEDATE = '".trim($SAH_EFFECTIVEDATE)."'";
	} // end if

	if (trim($MOV_CODE))
		if (strpos(trim($MOV_CODE),",")!==false)
			$search_arr_cond[] = "b.MOV_CODE in (".fill_arr_string($MOV_CODE).")";
		else
			$search_arr_cond[] = "b.MOV_CODE = '".trim($MOV_CODE)."'";
	if (trim($SAH_SALARY))
		if ($arr_cond_list["SAH_SALARY"])
			$search_arr_cond[] = $arr_cond_list["SAH_SALARY"];
		else
			$search_arr_cond[] = "SAH_SALARY = ".trim($SAH_SALARY);
	if (trim($SAH_DOCNO))
		if ($arr_cond_list["SAH_DOCNO"])
			$search_arr_cond[] = $arr_cond_list["SAH_DOCNO"];
		else
			$search_arr_cond[] = "SAH_DOCNO = ".trim($SAH_DOCNO);

	if(trim($SAH_DOCDATE)){
		$SAH_DOCDATE =  save_date($SAH_DOCDATE);
		if ($arr_cond_list["SAH_DOCDATE"])
			$search_arr_cond[] = $arr_cond_list["SAH_DOCDATE"];
		else
			$search_arr_cond[] = "SAH_DOCDATE = '".trim($SAH_DOCDATE)."'";
	} // end if

	if(trim($SAH_ENDDATE)){
		$SAH_ENDDATE =  save_date($SAH_ENDDATE);
		if ($arr_cond_list["SAH_ENDDATE"])
			$search_arr_cond[] = $arr_cond_list["SAH_ENDDATE"];
		else
			$search_arr_cond[] = "SAH_ENDDATE = '".trim($SAH_ENDDATE)."'";
	} // end if

	if (trim($SAH_PERCENT_UP))
		$search_arr_cond[] = "SAH_PERCENT_UP = ".trim($SAH_PERCENT_UP);
	if (trim($SAH_SALARY_UP))
		$search_arr_cond[] = "SAH_SALARY_UP = ".trim($SAH_SALARY_UP);
	if (trim($SAH_SALARY_EXTRA))
		$search_arr_cond[] = "SAH_SALARY_EXTRA = ".trim($SAH_SALARY_EXTRA);
	if (trim($SAH_SEQ_NO))
		$search_arr_cond[] = "SAH_SEQ_NO = ".trim($SAH_SEQ_NO);
	if (trim($SAH_REMARK))
		if ($arr_cond_list["SAH_REMARK"])
			$search_arr_cond[] = $arr_cond_list["SAH_REMARK"];
		else
			$search_arr_cond[] = "SAH_REMARK = '%".trim($SAH_REMARK)."%'";
	if (trim($LEVEL_NO))
		$search_arr_cond[] = "b.LEVEL_NO = '".trim($LEVEL_NO)."'";
	if (trim($SAH_POS_NO))
		if ($arr_cond_list["SAH_POS_NO"])
			$search_arr_cond[] = $arr_cond_list["SAH_POS_NO"];
		else
			$search_arr_cond[] = "SAH_POS_NO = ".trim($SAH_POS_NO);
	if (trim($SAH_POSITION))
		if ($arr_cond_list["SAH_POSITION"])
			$search_arr_cond[] = $arr_cond_list["SAH_POSITION"];
		else
			$search_arr_cond[] = "SAH_POSITION = '%".trim($SAH_POSITION)."%'";
	if (trim($SAH_ORG))
		if ($arr_cond_list["SAH_ORG"])
			$search_arr_cond[] = $arr_cond_list["SAH_ORG"];
		else
			$search_arr_cond[] = "SAH_ORG = '%".trim($SAH_ORG)."%'";
	if (trim($EX_CODE))
		if (strpos(trim($EX_CODE),",")!==false)
			$search_arr_cond[] = "EX_CODE in (".fill_arr_string($EX_CODE).")";
		else
			$search_arr_cond[] = "EX_CODE = '".trim($EX_CODE)."'";
	if (trim($SAH_PAY_NO))
		if ($arr_cond_list["SAH_PAY_NO"])
			$search_arr_cond[] = $arr_cond_list["SAH_PAY_NO"];
		else
			$search_arr_cond[] = "SAH_PAY_NO = '".trim($SAH_PAY_NO)."'";
	if (trim($SAH_SALARY_MIDPOINT))
		$search_arr_cond[] = "SAH_SALARY_MIDPOINT = ".trim($SAH_SALARY_MIDPOINT);
	if (trim($SAH_KF_YEAR))
		$search_arr_cond[] = "SAH_KF_YEAR = ".trim($SAH_KF_YEAR);
	if (trim($SAH_KF_CYCLE))
		$search_arr_cond[] = "SAH_KF_CYCLE = ".trim($SAH_KF_CYCLE);
	if (trim($SAH_TOTAL_SCORE))
		$search_arr_cond[] = "SAH_TOTAL_SCORE = ".trim($SAH_TOTAL_SCORE);
	if (trim($SAH_LAST_SALARY))
		$search_arr_cond[] = "SAH_LAST_SALARY = '".trim($SAH_LAST_SALARY)."'";
	if (trim($SM_CODE))
		if ($arr_cond_list["SM_CODE"])
			$search_arr_cond[] = $arr_cond_list["SM_CODE"];
		else
			$search_arr_cond[] = "SM_CODE = '".trim($SM_CODE)."'";
	if (trim($SAH_CMD_SEQ))
		$search_arr_cond[] = "SAH_CMD_SEQ = ".trim($SAH_CMD_SEQ);

	$search_text = "and ".implode(" and ",$search_arr_cond);

	$cmd = " select PER_NAME, PER_SURNAME, b.* from PER_PERSONAL a, PER_SALARYHIS b where a.PER_ID = b.PER_ID $search_text order by a.PER_ID ";
	$count_data = $db_dpis->send_cmd($cmd);
//	echo "[$cmd] len=".strlen($cmd)."<br>";
	$cnt = 0;

	$cnt = 0;
	$file_limit = 5000;
	$data_limit = 1000;
	$xlsRow = 0;
	$count_org_ref = 0;
	$sheet_no = 0; $sheet_no_text="";

	$report_code = "xls_SALARYHIS";

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
	$head = "เงินเดือน";
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
		
		$worksheet->write($xlsRow, 0, $data[SAH_ID], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		$pname = $data[PER_NAME]." ".$data[PER_SURNAME];
		$worksheet->write($xlsRow, 1, $pname, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

		$SAH_EFFECTIVEDATE_text = show_date_format(trim($data[SAH_EFFECTIVEDATE]),$DATE_DISPLAY);
		$worksheet->write($xlsRow, 2, $SAH_EFFECTIVEDATE_text, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		
		if (trim($data[MOV_CODE])) {
			$cmd1 = " select MOV_NAME, MOV_TYPE from PER_MOVMENT where MOV_CODE=".trim($data[MOV_CODE]);
			$db_dpis1->send_cmd($cmd);
			if ($data_dpis1 = $db_dpis1->get_array())
				$MOVE_NAME_text = trim($data_dpis1[MOVE_NAME]);
			else
				$MOVE_NAME_text = "";
		} else  $MOVE_NAME_text = "";
		$worksheet->write($xlsRow, 3, $MOVE_NAME_text, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
				
		$worksheet->write($xlsRow, 4, $data[SAH_SALARY], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		$worksheet->write($xlsRow, 5, $data[SAH_DOCNO], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		$SAH_DOCDATE_text = show_date_format(trim($data[SAH_DOCDATE]),$DATE_DISPLAY);
		$worksheet->write($xlsRow, 6, $SAH_DOCDATE_text, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		$SAH_ENDDATE_text = show_date_format(trim($data[SAH_ENDDATE]),$DATE_DISPLAY);
		$worksheet->write($xlsRow, 7, $SAH_ENDDATE_text, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		$worksheet->write($xlsRow, 8, $data[SAH_PERCENT_UP], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		$worksheet->write($xlsRow, 9, $data[SAH_SALARY_UP], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		$worksheet->write($xlsRow, 10, $data[SAH_SALARY_EXTRA], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		$worksheet->write($xlsRow, 11, $data[SAH_SEQ_NO], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		$worksheet->write($xlsRow, 12, $data[SAH_REMARK], set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
		
		if (trim($data[LEVEL_NO])) {
		  	$cmd1 = " select LEVEL_NAME from PER_LEVEL where LEVEL_NO = '".trim($data[LEVEL_NO])."' ";
			$db_dpis1->send_cmd($cmd1);
			if ($data_dpis1 = $db_dpis1->get_array())
				$LEVEL_NAME = $data_dpis1[LEVEL_NAME];
			else
				$LEVEL_NAME = "";
		} else  $LEVEL_NAME = "";
		$worksheet->write($xlsRow, 13, $LEVEL_NAME, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

		$worksheet->write($xlsRow, 14, $data[SAH_POS_NO], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		$worksheet->write($xlsRow, 15, $data[SAH_POSITION], set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
		$worksheet->write($xlsRow, 16, $data[SAH_ORG], set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

		if (trim($data[EX_CODE])) {
			$cmd1 ="select EX_NAME from PER_EXTRATYPE where EX_CODE = '".trim($data[EX_CODE])."' ";
			$db_dpis1->send_cmd($cmd1);
			if ($data_dpis1 = $db_dpis1->get_array())
				$EX_NAME_text = trim($data_dpis1[EX_NAME]);
			else
				$EX_NAME_text = "";
		} else  $EX_NAME_text = "";
		$worksheet->write($xlsRow, 17, $EX_NAME_text, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
		
		$worksheet->write($xlsRow, 18, $data[SAH_PAY_NO], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		$worksheet->write($xlsRow, 19, $data[SAH_SALARY_MIDPOINT], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		$worksheet->write($xlsRow, 20, $data[SAH_KF_YEAR], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		$worksheet->write($xlsRow, 21, $data[SAH_KF_CYCLE], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		$worksheet->write($xlsRow, 22, $data[SAH_TOTAL_SCORE], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		if (trim($data[SAH_LAST_SALARY])=="Y")  $last_salary_text = "ใช่";
		elseif (trim($data[SAH_LAST_SALARY])=="Y")  $last_salary_text = "ไม่ใช่";
		else  $last_salary_text = "";
		$worksheet->write($xlsRow, 23, $last_salary_text, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		if (trim($data[SM_CODE])) {
			$cmd1 ="select SM_NAME from PER_SALARY_MOVMENT where SM_CODE = '".trim($data[SM_CODE])."' ";
			$db_dpis1->send_cmd($cmd1);
			if ($data_dpis1 = $db_dpis1->get_array())
				$SM_NAME_text = trim($data_dpis1[SM_NAME]);
			else
				$SM_NAME_text = "";
		} else  $SM_NAME_text = "";
		$worksheet->write($xlsRow, 24, $SM_NAME_text, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		$worksheet->write($xlsRow, 25, $data[SAH_CMD_SEQ], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		
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