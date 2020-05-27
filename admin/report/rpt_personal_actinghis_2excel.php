<?	
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$header_width[0] = 15; $header_text[0] = "รหัสรักษาการ"; 			//	ACTH_ID;
	$header_width[1] = 30; $header_text[1] = "ชื่อ";							//	PER_NAME+PER_SURNAME;
	$header_width[2] = 20; $header_text[2] = "วันที่มีผล";					//	ACTH_EFFECTIVEDATE; 
	$header_width[3] = 20; $header_text[3] = "วันที่สิ้นสุด";				//	ACTH_ENDDATE; 
	$header_width[4] = 20; $header_text[4] = "เลขที่ตำแหน่ง";			//	ACTH_POS_NO;
	$header_width[5] = 20; $header_text[5] = "ตำแหน่งบริหาร";		//	PM_CODE;
	$header_width[6] = 20; $header_text[6] = "ตำแหน่งในสาย";		//	PL_CODE;
	$header_width[7] = 20; $header_text[7] = "$MINISTRY_TITLE";					//	ACTH_ORG_1;
	$header_width[8] = 20; $header_text[8] = "$DEPARTMENT_TITLE";						//	ACTH_ORG_2;
	$header_width[9] = 20; $header_text[9] = "$ORG_TITLE";				//	ACTH_ORG_3;
	$header_width[10] = 20; $header_text[10] = "$ORG_TITLE1";		//	ACTH_ORG_4;
	$header_width[11] = 20; $header_text[11] = "$ORG_TITLE2";		//	ACTH_ORG_5;
	$header_width[12] = 20; $header_text[12] = "ระดับตำแหน่ง";		//	LEVEL_NO;
	$header_width[13] = 20; $header_text[13] = "การเคลื่อนไหว";		//	MOV_CODE;
	$header_width[14] = 20; $header_text[14] = "เลขที่ตำแหน่ง";		//	ACTH_POS_NO_ASSIGN;
	$header_width[15] = 20; $header_text[15] = "ตำแหน่งบริหาร";		//	PM_CODE_ASSIGN;
	$header_width[16] = 20; $header_text[16] = "ตำแหน่งในสาย";		//	PL_CODE_ASSIGN;
	$header_width[17] = 20; $header_text[17] = "$MINISTRY_TITLE";				//	ACTH_ORG_1_ASSIGN;
	$header_width[18] = 20; $header_text[18] = "$DEPARTMENT_TITLE";					//	ACTH_ORG_2_ASSIGN;
	$header_width[19] = 20; $header_text[19] = "$ORG_TITLE";			//	ACTH_ORG_3_ASSIGN;
	$header_width[20] = 20; $header_text[20] = "$ORG_TITLE1";		//	ACTH_ORG_4_ASSIGN;
	$header_width[21] = 20; $header_text[21] = "$ORG_TITLE2";		//	ACTH_ORG_5_ASSIGN;
	$header_width[22] = 20; $header_text[22] = "ระดับตำแหน่ง";		//	LEVEL_NO_ASSIGN;
	$header_width[23] = 20; $header_text[23] = "คำสั่งเลขที่";			//	ACTH_DOCNO;
	$header_width[24] = 20; $header_text[24] = "วันที่คำสั่ง";			//	ACTH_DOCDATE;
	$header_width[25] = 20; $header_text[25] = "มอบหมายให้ปฏิบัติ";	//	ACTH_ASSIGN;
	$header_width[26] = 20; $header_text[26] = "หมายเหตุ";				//	ACTH_REMARK;

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

	if (trim($ACTH_ID))
		$search_arr_cond[] = "ACTH_ID = ".trim($ACTH_ID);
		
	if(trim($ACTH_EFFECTIVEDATE)){
		$ACTH_EFFECTIVEDATE =  save_date($ACTH_EFFECTIVEDATE);
		if ($arr_cond_list["ACTH_EFFECTIVEDATE"])
			$search_arr_cond[] = $arr_cond_list["ACTH_EFFECTIVEDATE"];
		else
			$search_arr_cond[] = "ACTH_EFFECTIVEDATE = '".trim($ACTH_EFFECTIVEDATE)."'";
	} // end if

	if(trim($ACTH_ENDDATE)){
		$ACTH_ENDDATE =  save_date($ACTH_ENDDATE);
		if ($arr_cond_list["ACTH_ENDDATE"])
			$search_arr_cond[] = $arr_cond_list["ACTH_ENDDATE"];
		else
			$search_arr_cond[] = "ACTH_ENDDATE = '".trim($ACTH_ENDDATE)."'";
	} // end if

	if (trim($ACTH_POS_NO))
		if (strpos(trim($ACTH_POS_NO),",")!==false)
			$search_arr_cond[] = "b.ACTH_POS_NO in (".fill_arr_string($ACTH_POS_NO).")";
		else
			$search_arr_cond[] = "b.ACTH_POS_NO = '".trim($ACTH_POS_NO)."'";

	if (trim($PM_CODE))
		if (strpos(trim($PM_CODE),",")!==false)
			$search_arr_cond[] = "b.PM_CODE in (".fill_arr_string($PM_CODE).")";
		else
			$search_arr_cond[] = "b.PM_CODE = '".trim($PM_CODE)."'";

	if (trim($PL_CODE))
		if (strpos(trim($PL_CODE),",")!==false)
			$search_arr_cond[] = "b.PL_CODE in (".fill_arr_string($PL_CODE).")";
		else
			$search_arr_cond[] = "b.PL_CODE = '".trim($PL_CODE)."'";

	if (trim($ORG_NAME_1))
		$search_arr_cond[] = "b.ACTH_ORG1 = '".trim($ORG_NAME_1)."'";
	if (trim($ORG_NAME_2))
		$search_arr_cond[] = "b.ACTH_ORG2 = '".trim($ORG_NAME_2)."'";
	if (trim($ORG_NAME_3))
		$search_arr_cond[] = "b.ACTH_ORG3 = '".trim($ORG_NAME_3)."'";
	if (trim($ORG_NAME_4))
		$search_arr_cond[] = "b.ACTH_ORG4 = '%".trim($ORG_NAME_4)."%'";
	if (trim($ORG_NAME_5))
		$search_arr_cond[] = "b.ACTH_ORG5 = '%".trim($ORG_NAME_5)."%'";

	if (trim($LEVEL_NO))
		$search_arr_cond[] = "b.LEVEL_NO = '".trim($LEVEL_NO)."'";
	if (trim($MOV_CODE))
		if (strpos(trim($MOV_CODE),",")!==false)
			$search_arr_cond[] = "b.MOV_CODE in (".fill_arr_string($MOV_CODE).")";
		else
			$search_arr_cond[] = "b.MOV_CODE = '".trim($MOV_CODE)."'";
	if (trim($ACTH_POS_NO_ASSIGN))
		if (strpos(trim($ACTH_POS_NO_ASSIGN),",")!==false)
			$search_arr_cond[] = "b.ACTH_POS_NO_ASSIGN in (".fill_arr_string($ACTH_POS_NO_ASSIGN).")";
		else
			$search_arr_cond[] = "b.ACTH_POS_NO_ASSIGN = '".trim($ACTH_POS_NO_ASSIGN)."'";
	if (trim($PM_CODE_ASSIGN))
		if (strpos(trim($PM_CODE_ASSIGN),",")!==false)
			$search_arr_cond[] = "b.PM_CODE_ASSIGN in (".fill_arr_string($PM_CODE_ASSIGN).")";
		else
			$search_arr_cond[] = "b.PM_CODE_ASSIGN = '".trim($PM_CODE_ASSIGN)."'";
	if (trim($PL_CODE_ASSIGN))
		if (strpos(trim($PL_CODE_ASSIGN),",")!==false)
			$search_arr_cond[] = "b.PL_CODE_ASSIGN in (".fill_arr_string($PL_CODE_ASSIGN).")";
		else
			$search_arr_cond[] = "b.PL_CODE_ASSIGN = '".trim($PL_CODE_ASSIGN)."'";

	if (trim($ORG_NAME_1_ASSIGN))
		$search_arr_cond[] = "b.ACTH_ORG1_ASSIGN = '".trim($ORG_NAME_1_ASSIGN)."'";
	if (trim($ORG_NAME_2_ASSIGN))
		$search_arr_cond[] = "b.ACTH_ORG2_ASSIGN = '".trim($ORG_NAME_2_ASSIGN)."'";
	if (trim($ORG_NAME_3_ASSIGN))
		$search_arr_cond[] = "b.ACTH_ORG3_ASSIGN = '".trim($ORG_NAME_3_ASSIGN)."'";
	if (trim($ORG_NAME_4_ASSIGN))
		$search_arr_cond[] = "b.ACTH_ORG4_ASSIGN = '%".trim($ORG_NAME_4_ASSIGN)."%'";
	if (trim($ORG_NAME_5_ASSIGN))
		$search_arr_cond[] = "b.ACTH_ORG5_ASSIGN = '%".trim($ORG_NAME_5_ASSIGN)."%'";

	if (trim($LEVEL_NO_ASSIGN))
		$search_arr_cond[] = "b.LEVEL_NO_ASSIGN = '".trim($LEVEL_NO_ASSIGN)."'";

	if (trim($ACTH_DOCNO))
		if ($arr_cond_list["ACTH_DOCNO"])
			$search_arr_cond[] = $arr_cond_list["ACTH_DOCNO"];
		else
			$search_arr_cond[] = "ACTH_DOCNO = ".trim($ACTH_DOCNO);

	if(trim($ACTH_DOCDATE)){
		$ACTH_DOCDATE =  save_date($ACTH_DOCDATE);
		if ($arr_cond_list["ACTH_DOCDATE"])
			$search_arr_cond[] = $arr_cond_list["ACTH_DOCDATE"];
		else
			$search_arr_cond[] = "ACTH_DOCDATE = '".trim($ACTH_DOCDATE)."'";
	} // end if

	if (trim($ACTH_ASSIGN))
		if ($arr_cond_list["ACTH_ASSIGN"])
			$search_arr_cond[] = $arr_cond_list["ACTH_ASSIGN"];
		else
			$search_arr_cond[] = "ACTH_ASSIGN = '%".trim($ACTH_ASSIGN)."%'";
	if (trim($ACTH_REMARK))
		if ($arr_cond_list["ACTH_REMARK"])
			$search_arr_cond[] = $arr_cond_list["ACTH_REMARK"];
		else
			$search_arr_cond[] = "ACTH_REMARK = '%".trim($ACTH_REMARK)."%'";

	$search_text = "and ".implode(" and ",$search_arr_cond);

	$cmd = " select PER_NAME, PER_SURNAME, b.* from PER_PERSONAL a, PER_ACTINGHIS b where a.PER_ID = b.PER_ID $search_text order by a.PER_ID ";
	$count_data = $db_dpis->send_cmd($cmd);
//	echo "[$cmd] len=".strlen($cmd)."<br>";
	$cnt = 0;

	$cnt = 0;
	$file_limit = 5000;
	$data_limit = 1000;
	$xlsRow = 0;
	$count_org_ref = 0;
	$sheet_no = 0; $sheet_no_text="";

	$report_code = "xls_ACTINGHIS";

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
	$head = "รักษาราชการ/มอบหมายงาน";
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
		
		$worksheet->write($xlsRow, 0, $data[ACTH_ID], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		$pname = $data[PER_NAME]." ".$data[PER_SURNAME];
		$worksheet->write($xlsRow, 1, $pname, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

		$ACTH_EFFECTIVEDATE_text = show_date_format(trim($data[ACTH_EFFECTIVEDATE]),$DATE_DISPLAY);
		$worksheet->write($xlsRow, 2, $ACTH_EFFECTIVEDATE_text, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		$ACTH_ENDDATE_text = show_date_format(trim($data[ACTH_ENDDATE]),$DATE_DISPLAY);  
		$worksheet->write($xlsRow, 3, $ACTH_ENDDATE_text, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		$worksheet->write($xlsRow, 4, trim($data[ACTH_POS_NO]), set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		if (trim($data[PM_CODE])) {
			$cmd = " select PM_NAME from PER_MGT where PM_CODE='".trim($data[PM_CODE])."' ";
			$db_dpis1->send_cmd($cmd);
			if ($data_dpis1 = $db_dpis1->get_array())
				$PM_NAME_text = trim($data_dpis1[PM_NAME]);
			else
				$PM_NAME_text = "";
		} else  $PM_NAME_text = "";
		$worksheet->write($xlsRow, 5, $PM_NAME_text, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		if (trim($data[PL_CODE])) {
			$cmd = " select PL_NAME from PER_LINE where PL_CODE='".trim($data[PL_CODE])."' ";
			$db_dpis1->send_cmd($cmd);
			if ($data_dpis1 = $db_dpis1->get_array())
				$PL_NAME_text = trim($data_dpis1[PL_NAME]);
			else
				$PL_NAME_text = "";
		} else  $PL_NAME_text = "";
		$worksheet->write($xlsRow, 6, $PL_NAME_text, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		$worksheet->write($xlsRow, 7, $data[ACTH_ORG1], set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
		$worksheet->write($xlsRow, 8, $data[ACTH_ORG2], set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
		$worksheet->write($xlsRow, 9, $data[ACTH_ORG3], set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
		$worksheet->write($xlsRow, 10, $data[ACTH_ORG4], set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
		$worksheet->write($xlsRow, 11, $data[ACTH_ORG5], set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

		if (trim($data[LEVEL_NO])) {
		  	$cmd = " select LEVEL_NAME from PER_LEVEL where LEVEL_NO = '".trim($data[LEVEL_NO])."' ";
			$db_dpis1->send_cmd($cmd);
			if ($data_dpis1 = $db_dpis1->get_array())
				$LEVEL_NAME = $data_dpis1[LEVEL_NAME];
			else
				$LEVEL_NAME = "";
		} else  $LEVEL_NAME = "";
		$worksheet->write($xlsRow, 12, $LEVEL_NAME, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

		if (trim($data[MOV_CODE])) {
		  	$cmd = " select MOV_NAME from PER_MOVMENT where MOV_CODE = '".trim($data[MOV_CODE])."' ";
			$db_dpis1->send_cmd($cmd);
			if ($data_dpis1 = $db_dpis1->get_array())
				$MOV_NAME = $data_dpis1[MOV_NAME];
			else
				$MOV_NAME = "";
		} else  $MOV_NAME = "";
		$worksheet->write($xlsRow, 13, $MOV_NAME, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

		$worksheet->write($xlsRow, 14, trim($data[ACTH_POS_NO_ASSIGN]), set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		if (trim($data[PM_CODE_ASSIGN])) {
			$cmd = " select PM_NAME from PER_MGT where PM_CODE='".trim($data[PM_CODE_ASSIGN])."' ";
			$db_dpis1->send_cmd($cmd);
			if ($data_dpis1 = $db_dpis1->get_array())
				$PM_NAME_ASSIGN_text = trim($data_dpis1[PM_NAME]);
			else
				$PM_NAME_ASSIGN_text = "";
		} else  $PM_NAME_ASSIGN_text = "";
		$worksheet->write($xlsRow, 15, $PM_NAME_ASSIGN_text, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		if (trim($data[PL_CODE_ASSIGN])) {
			$cmd = " select PL_NAME from PER_LINE where PL_CODE='".trim($data[PL_CODE_ASSIGN])."' ";
			$db_dpis1->send_cmd($cmd);
			if ($data_dpis1 = $db_dpis1->get_array())
				$PL_NAME_ASSIGN_text = trim($data_dpis1[PL_NAME]);
			else
				$PL_NAME_ASSIGN_text = "";
		} else  $PL_NAME_ASSIGN_text = "";
		$worksheet->write($xlsRow, 16, $PL_NAME_ASSIGN_text, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		$worksheet->write($xlsRow, 17, $data[ACTH_ORG1_ASSIGN], set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
		$worksheet->write($xlsRow, 18, $data[ACTH_ORG2_ASSIGN], set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
		$worksheet->write($xlsRow, 19, $data[ACTH_ORG3_ASSIGN], set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
		$worksheet->write($xlsRow, 20, $data[ACTH_ORG4_ASSIGN], set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
		$worksheet->write($xlsRow, 21, $data[ACTH_ORG5_ASSIGN], set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

		if (trim($data[LEVEL_NO_ASSIGN])) {
		  	$cmd = " select LEVEL_NAME from PER_LEVEL where LEVEL_NO = '".trim($data[LEVEL_NO_ASSIGN])."' ";
			$db_dpis1->send_cmd($cmd);
			if ($data_dpis1 = $db_dpis1->get_array())
				$LEVEL_NAME_ASSIGN = $data_dpis1[LEVEL_NAME];
			else
				$LEVEL_NAME_ASSIGN = "";
		} else  $LEVEL_NAME_ASSIGN = "";
		$worksheet->write($xlsRow, 22, $LEVEL_NAME_ASSIGN, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

		$worksheet->write($xlsRow, 23, $data[ACTH_DOCNO], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		$ACTH_DOCDATE_text = show_date_format(trim($data[ACTH_DOCDATE]),$DATE_DISPLAY);  
		$worksheet->write($xlsRow, 24, $ACTH_DOCDATE_text, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		$worksheet->write($xlsRow, 25, $data[ACTH_ASSIGN], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		$worksheet->write($xlsRow, 26, $data[ACTH_REMARK], set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

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