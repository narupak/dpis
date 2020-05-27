<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$header_width[0] = 30; $header_text[0] = "ชื่อ";									// for $PER_NAME+$PER_SURNAME
	$header_width[1] = 20; $header_text[1] = "เลขที่การดำรงตำแหน่ง";		// POH_ID;
	$header_width[2] = 20; $header_text[2] = "วันที่มีผล";							// POH_EFFECTIVEDATE;
	$header_width[3] = 20; $header_text[3] = "วันที่สิ้นสุดการจ้าง";			// POH_ENDDATE;
	$header_width[4] = 20; $header_text[4] = "เลขที่คำสั่ง";						// POH_DOCNO;
	$header_width[5] = 20; $header_text[5] = "วันที่คำสั่ง";						// POH_DOCDATE;
	$header_width[6] = 20; $header_text[6] = "$POS_NO_TITLE";					// POH_POS_NO;
	$header_width[7] = 20; $header_text[7] = "$PM_TITLE";		// PM_CODE;
	$header_width[8] = 20; $header_text[8] = "$LEVEL_TITLE";					// LEVEL_NO;
	$header_width[9] = 20; $header_text[9] = "$PL_TITLE";			// PL_CODE;
//	$header_width[10] = 20; $header_text[10] = "คำนำหน้าชื่อ";					// PN_CODE;
	$header_width[10] = 20; $header_text[10] = "ตำแหน่งพนักงานราชการ";	// EP_CODE;
	$header_width[11] = 20; $header_text[11] = "$PT_TITLE";			// PT_CODE;
	$header_width[12] = 20; $header_text[12] = "$CT_TITLE";						// CT_CODE;
	$header_width[13] = 20; $header_text[13] = "$PV_TITLE";						// PV_CODE;
	$header_width[14] = 20; $header_text[14] = "$AP_TITLE";							// AP_CODE;
	$header_width[15] = 20; $header_text[15] = "ตำแหน่งบริหารภายใน";	// POH_ORGMGT 1=ใช่  2=ไม่ใช่;
	$header_width[16] = 20; $header_text[16] = "$MINISTRY_TITLE";						// ORG_ID_1;
	$header_width[17] = 20; $header_text[17] = "$DEPARTMENT_TITLE";							// ORG_ID_2;
	$header_width[18] = 20; $header_text[18] = "$ORG_TITLE";					// ORG_ID_3;
	$header_width[19] = 20; $header_text[19] = "$ORG_TITLE1";	// POH_UNDER_ORG1;
	$header_width[20] = 20; $header_text[20] = "$ORG_TITLE2";	// POH_UNDER_ORG2;
	$header_width[21] = 20; $header_text[21] = "$ORG_TITLE ตามมอบหมายงาน";	// POH_ASS_ORG;
	$header_width[22] = 20; $header_text[22] = "$ORG_TITLE1 ตามมอบหมายงาน";	// POH_ASS_ORG1;
	$header_width[23] = 20; $header_text[23] = "$ORG_TITLE2 ตามมอบหมายงาน";	// POH_ASS_ORG2;
	$header_width[24] = 20; $header_text[24] = "อัตราเงินเดือน";				// POH_SALARY;
	$header_width[25] = 20; $header_text[25] = "เงินประจำตำแหน่ง";		//	POH_SALARY_POS;
	$header_width[26] = 20; $header_text[26] = "หมายเหตุ";						//	POH_REMARK;
	$header_width[27] = 20; $header_text[27] = "บัตรประชาชน";				//	PER_CARDNO;
	$header_width[28] = 25; $header_text[28] = "$MOV_TITLE";	// MOV_CODE;
	$header_width[29] = 20; $header_text[29] = "$MINISTRY_TITLE";					//	POH_ORG1;
	$header_width[30] = 20; $header_text[30] = "$DEPARTMENT_TITLE";						//	POH_ORG2;
	$header_width[31] = 20; $header_text[31] = "$ORG_TITLE";				//	POH_ORG3;
	$header_width[32] = 20; $header_text[32] = "ส่วนราชการที่รับโอน/ให้โอน";	//	POH_ORG_TRANSFER;
	$header_width[33] = 20; $header_text[33] = "ข้อมูลเดิม (ก่อนถ่ายโอน)";	//	POH_ORG;
	$header_width[34] = 20; $header_text[34] = "ชื่อตำแหน่งในการบริหาร";	//	POH_PM_NAME;
	$header_width[35] = 20; $header_text[35] = "ชื่อตำแหน่งในสายงาน";		//	POH_PL_NAME;
	$header_width[36] = 20; $header_text[36] = "ลำดับที่";							//	POH_SEQ_NO;
	$header_width[37] = 20; $header_text[37] = "เป็นตำแหน่งล่าสุด";			//	POH_LAST_POSITION;
	$header_width[38] = 20; $header_text[38] = "คำสั่งล่าสุด";						//	POH_CMD_SEQ;
	$header_width[39] = 20; $header_text[39] = "ดำรงตำแหน่ง";					//	POH_ISREAL Y=จริง;
	$header_width[40] = 20; $header_text[40] = "$ES_TITLE";			//	ES_CODE;

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

	if (trim($POH_ID))
		$search_arr_cond[] = "POH_ID = ".trim($POH_ID);

	if($POH_EFFECTIVEDATE){
		$POH_EFFECTIVEDATE =  save_date($POH_EFFECTIVEDATE);
		if ($arr_cond_list["POH_EFFECTIVEDATE"])
			$search_arr_cond[] = $arr_cond_list["POH_EFFECTIVEDATE"];
		else
			$search_arr_cond[] = "POH_EFFECTIVEDATE >= '".trim($POH_EFFECTIVEDATE)."'";
	} // end if

	if (trim($MOV_CODE))
		if (strpos(trim($MOV_CODE),",")!==false)
			$search_arr_cond[] = "b.MOV_CODE in (".fill_arr_string($MOV_CODE).")";
		else
			$search_arr_cond[] = "b.MOV_CODE = '".trim($MOV_CODE)."'";

	if($POH_ENDDATE){
		$POH_ENDDATE =  save_date($POH_ENDDATE);
		if ($arr_cond_list["POH_ENDDATE"])
			$search_arr_cond[] = $arr_cond_list["POH_ENDDATE"];
		else
			$search_arr_cond[] = "POH_ENDDATE <= '".trim($POH_ENDDATE)."'";
	} // end if
		
	if (trim($POH_DOCNO))
		if ($arr_cond_list["POH_DOCNO"])
			$search_arr_cond[] = $arr_cond_list["POH_DOCNO"];
		else
			$search_arr_cond[] = "POH_DOCNO = '%".trim($POH_DOCNO)."'";

	if($POH_DOCDATE){
		$POH_DOCDATE =  save_date($POH_DOCDATE);
		if ($arr_cond_list["POH_DOCDATE"])
			$search_arr_cond[] = $arr_cond_list["POH_DOCDATE"];
		else
			$search_arr_cond[] = "POH_DOCDATE = ".trim($POH_DOCDATE);
	} // end if
		
	if (trim($POH_POS_NO))
		if (strpos(trim($POH_POS_NO),",")!==false)
			$search_arr_cond[] = "POH_POS_NO in (".fill_arr_string($POH_POS_NO).")";
		else
			$search_arr_cond[] = "POH_POS_NO = '".trim($POH_POS_NO)."'";
	if (trim($PM_CODE))
		if (strpos(trim($PM_CODE),",")!==false)
			$search_arr_cond[] = "PM_CODE in (".fill_arr_string($PM_CODE).")";
		else
			$search_arr_cond[] = "PM_CODE = '".trim($PM_CODE)."'";
	if (trim($LEVEL_NO))
		$search_arr_cond[] = "LEVEL_NO = '".trim($LEVEL_NO)."'";
	if (trim($PL_CODE))
		if (strpos(trim($PL_CODE),",")!==false)
			$search_arr_cond[] = "PL_CODE in (".fill_arr_string($PL_CODE).")";
		else
			$search_arr_cond[] = "PL_CODE = '".trim($PL_CODE)."'";
	if (trim($EP_CODE))
		$search_arr_cond[] = "EP_CODE = ".trim($EP_CODE);
	if (trim($PT_CODE))
		if (strpos(trim($PT_CODE),",")!==false)
			$search_arr_cond[] = "PT_CODE in (".fill_arr_string($PT_CODE).")";
		else
			$search_arr_cond[] = "PT_CODE = ".trim($PT_CODE);
	if (trim($CT_CODE))
		$search_arr_cond[] = "CT_CODE = ".trim($CT_CODE);
	if (trim($PV_CODE))
		if (strpos(trim($PV_CODE),",")!==false)
			$search_arr_cond[] = "b.PV_CODE in (".fill_arr_string($PV_CODE).")";
		else
			$search_arr_cond[] = "b.PV_CODE = ".trim($PV_CODE);
	if (trim($AP_CODE))
		if (strpos(trim($AP_CODE),",")!==false)
			$search_arr_cond[] = "AP_CODE in (".fill_arr_string($AP_CODE).")";
		else
			$search_arr_cond[] = "AP_CODE = '".trim($AP_CODE)."'";
	if (trim($POH_ORGMGT))
		$search_arr_cond[] = "POH_ORGMGT = ".trim($POH_ORGMGT);
	if (trim($ORG_ID_1))
		$search_arr_cond[] = "ORG_ID_1 = ".trim($ORG_ID_1);
	if (trim($ORG_ID_2))
		$search_arr_cond[] = "ORG_ID_2 = ".trim($ORG_ID_2);
	if (trim($ORG_ID_3))
		$search_arr_cond[] = "ORG_ID_3 = ".trim($ORG_ID_3);
	if (trim($POH_UNDER_ORG1))
		$search_arr_cond[] = "POH_UNDER_ORG1 = ".trim($POH_UNDER_ORG1);
	if (trim($POH_UNDER_ORG2))
		$search_arr_cond[] = "POH_UNDER_ORG2 = ".trim($POH_UNDER_ORG2);
//	if (trim($POH_ASS_ORG))
//		$search_arr_cond[] = "POH_ASS_ORG = ".trim($POH_ASS_ORG);
//	if (trim($POH_ASS_ORG1))
//		$search_arr_cond[] = "POH_ASS_ORG1 = ".trim($POH_ASS_ORG1);
//	if (trim($POH_ASS_ORG2))
//		$search_arr_cond[] = "POH_ASS_ORG2 = ".trim($POH_ASS_ORG2);
	if (trim($POH_SALARY))
		if ($arr_cond_list["POH_SALARY"])
			$search_arr_cond[] = $arr_cond_list["POH_SALARY"];
		else
			$search_arr_cond[] = "POH_SALARY = ".trim($POH_SALARY);
	if (trim($POH_SALARY_POS))
		if ($arr_cond_list["POH_SALARY_POS"])
			$search_arr_cond[] = $arr_cond_list["POH_SALARY_POS"];
		else
			$search_arr_cond[] = "POH_SALARY_POS = ".trim($POH_SALARY_POS);
	if (trim($POH_REMARK))
		$search_arr_cond[] = "POH_REMARK like '%".trim($POH_REMARK)."%'";
	if (trim($PER_ORG1))
		$search_arr_cond[] = "PER_ORG1 like '%".trim($PER_ORG1)."%'";
	if (trim($PER_ORG2))
		$search_arr_cond[] = "PER_ORG2 like '%".trim($PER_ORG2)."%'";
	if (trim($PER_ORG3))
		$search_arr_cond[] = "PER_ORG3 like '%".trim($PER_ORG3)."%'";
	if (trim($POH_ORG_TRANSFER))
		if ($arr_cond_list["POH_ORG_TRANSFER"])
			$search_arr_cond[] = $arr_cond_list["POH_ORG_TRANSFER"];
		else
			$search_arr_cond[] = "POH_ORG_TRANSFER like '%".trim($POH_ORG_TRANSFER)."%'";
	if (trim($POH_ORG))
		if ($arr_cond_list["POH_ORG"])
			$search_arr_cond[] = $arr_cond_list["POH_ORG"];
		else
			$search_arr_cond[] = "POH_ORG like '%".trim($POH_ORG)."%'";
	if (trim($POH_PM_NAME))
		$search_arr_cond[] = "POH_PM_NAME like '%".trim($POH_PM_NAME)."%'";
	if (trim($POH_PL_NAME))
		$search_arr_cond[] = "POH_PL_NAME like '%".trim($POH_PL_NAME)."%'";
	if (trim($POH_SEQ_NO))
		$search_arr_cond[] = "POH_SEQ_NO = ".trim($POH_SEQ_NO);
	if (trim($POH_LAST_POSITION))
		$search_arr_cond[] = "POH_LAST_POSITION = '".trim($POH_LAST_POSITION)."'";
	if (trim($POH_CMD_SEQ))
		$search_arr_cond[] = "POH_CMD_SEQ = ".trim($POH_CMD_SEQ);
	if (trim($POH_ISREAL))
		$search_arr_cond[] = "POH_ISREAL = '".trim($POH_ISREAL)."'";
	if (trim($ES_CODE))
		if (strpos(trim($ES_CODE),",")!==false)
			$search_arr_cond[] = "b.ES_CODE in (".fill_arr_string($ES_CODE).")";
		else
			$search_arr_cond[] = "b.ES_CODE = '".trim($ES_CODE)."'";

	$search_text = implode(" and ",$search_arr_cond);
	if (trim($search_text))
		$search_text = "and ".trim($search_text);

	$cmd = "	   SELECT a.PER_ID, PER_NAME, PER_SURNAME, a.PN_CODE as PER_TITLE, 
									POH_ID, POH_EFFECTIVEDATE, b.MOV_CODE, POH_ENDDATE, POH_DOCNO, POH_DOCDATE, 
									POH_POS_NO, PM_CODE, b.LEVEL_NO, PL_CODE, b.PN_CODE, EP_CODE, PT_CODE, CT_CODE, 
									b.PV_CODE, AP_CODE, POH_ORGMGT, ORG_ID_1, ORG_ID_2, ORG_ID_3, POH_UNDER_ORG1, 
									POH_UNDER_ORG2, POH_ASS_ORG, POH_ASS_ORG1, POH_ASS_ORG2, POH_SALARY, 
									POH_SALARY_POS, POH_REMARK, POH_ORG1, POH_ORG2, POH_ORG3, POH_ORG_TRANSFER, 
									POH_ORG, POH_PM_NAME, POH_PL_NAME, POH_SEQ_NO, POH_LAST_POSITION, 
									POH_CMD_SEQ, POH_ISREAL, b.ES_CODE, POH_LEVEL_NO 
						FROM	PER_PERSONAL a, PER_POSITIONHIS b
						WHERE a.PER_ID=b.PER_ID $search_text ORDER BY a.PER_ID ";
	$count_data = $db_dpis->send_cmd($cmd);
//	echo "[$cmd]<br><br>";

	$cnt = 0;
	$file_limit = 5000;
	$data_limit = 1000;
	$xlsRow = 0;
	$count_org_ref = 0;
	$sheet_no = 0; $sheet_no_text="";

	$report_code = "xls_POSITIONHIS";

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
//		$worksheet->write($xlsRow, 0, $cmd, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

		$worksheet->write($xlsRow, 0, $pname, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
		
		$worksheet->write($xlsRow, 1, $data[POH_ID], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		$POH_EFFECTIVEDATE_text = show_date_format(trim($data[POH_EFFECTIVEDATE]),$DATE_DISPLAY);
		$worksheet->write($xlsRow, 2, $POH_EFFECTIVEDATE_text, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		$POH_ENDDATE_text = show_date_format(trim($data[POH_ENDDATE]),$DATE_DISPLAY);
		$worksheet->write($xlsRow, 3, $POH_ENDDATE_text, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		$worksheet->write($xlsRow, 4, $data[POH_DOCNO], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		
		$POH_DOCDATE_text = show_date_format(trim($data[POH_DOCDATE]),$DATE_DISPLAY);
		$worksheet->write($xlsRow, 5, $POH_DOCDATE_text, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		$worksheet->write($xlsRow, 6, $data[POH_POS_NO], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		
		if (trim($data[PM_CODE])) {
			$cmd1 = " select PM_NAME from PER_MGT where trim(PM_CODE)=".trim($data[PM_CODE])." ";
			$db_dpis1->send_cmd($cmd1);
			if ($data_dpis1 = $db_dpis1->get_array())
				$PM_NAME = trim($data_dpis1[PM_NAME]);
			else
				$PM_NAME = "";
		} else  $PM_NAME = "";
		$worksheet->write($xlsRow, 7, $PM_NAME, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		if (trim($data[LEVEL_NO])) {
		  	$cmd1 = " select LEVEL_NAME from PER_LEVEL where LEVEL_NO = '".trim($data[LEVEL_NO])."' ";
			$db_dpis1->send_cmd($cmd1);
			if ($data_dpis1 = $db_dpis1->get_array())
				$LEVEL_NAME = $data_dpis1[LEVEL_NAME];
			else
				$LEVEL_NAME = "";
		} else  $LEVEL_NAME = "";
		$worksheet->write($xlsRow, 8, $LEVEL_NAME, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

		if (trim($data[PL_CODE])) {
			$cmd1 = " select PL_NAME from PER_LINE where trim(PL_CODE)='".trim($data[PL_CODE])."' ";
			$db_dpis1->send_cmd($cmd1);
			if ($data_dpis1 = $db_dpis1->get_array())
				$PL_NAME = trim($data_dpis1[PL_NAME]);
			else
				$PL_NAME = "";
		} else  $PL_NAME = "";
		$worksheet->write($xlsRow, 9, $PL_NAME, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

		if (trim($data[EP_CODE])) {
			$cmd1 = " select EP_NAME from PER_EMPSER_POS_NAME where trim(EP_CODE)='".trim($data[EP_CODE])."' ";
			$db_dpis1->send_cmd($cmd1);
			if ($data_dpis1 = $db_dpis1->get_array())
				$PL_NAME = trim($data_dpis1[EP_NAME]);
			else
				$PL_NAME = "";
		} else  $PL_NAME = "";
		$worksheet->write($xlsRow, 10, $PL_NAME, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

		if (trim($data[PT_CODE])) {
			$cmd1 = " select PT_NAME from PER_TYPE where PT_CODE='".trim($data[PT_CODE])."' ";
			$db_dpis1->send_cmd($cmd1);
			if ($data_dpis1 = $db_dpis1->get_array())
				$PT_NAME = $data_dpis1[PT_NAME];
			else
				$PT_NAME = "";
		} else  $PT_NAME = "";
		$worksheet->write($xlsRow, 11, $PT_NAME, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		if (trim($data[CT_CODE])) {
			$cmd1 = " select CT_NAME from PER_COUNTRY where CT_CODE='".trim($data[CT_CODE])."' ";
			$db_dpis1->send_cmd($cmd1);
			if ($data_dpis1 = $db_dpis1->get_array())
				$CT_NAME = $data_dpis1[CT_NAME];
			else
				$CT_NAME = "";
		} else  $CT_NAME = "";
		$worksheet->write($xlsRow, 12, $CT_NAME, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		if (trim($data[PV_CODE])) {
			$cmd1 = " select PV_NAME from PER_PROVINCE where PV_CODE='$PV_CODE' ";
			$db_dpis1->send_cmd($cmd1);
			if ($data_dpis1 = $db_dpis1->get_array())
				$PV_NAME = $data_dpis1[PV_NAME];
			else
				$PV_NAME = "";
		} else  $PV_NAME = "";
		$worksheet->write($xlsRow, 13, $PV_NAME, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		if (trim($data[AP_CODE])) {
			$cmd1 = " select AP_NAME from PER_AMPHUR where AP_CODE='$AP_CODE' ";
			$db_dpis1->send_cmd($cmd1);
			if ($data_dpis1 = $db_dpis1->get_array())
				$AP_NAME = $data_dpis1[AP_NAME];
			else
				$AP_NAME = "";
		} else  $AP_NAME = "";
		$worksheet->write($xlsRow, 14, $AP_NAME, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		if (trim($POH_ORGMGT)==1) $POH_ORGMGT_text = "ใช่";
		elseif (trim($POH_ORGMGT)==2) $POH_ORGMGT_text = "ไม่ใช่";
		else  $POH_ORGMGT_text = "";
		$worksheet->write($xlsRow, 15, $POH_ORGMGT_text, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		if (trim($data[ORG_ID_1])) {
			$cmd1 = " select ORG_NAME from PER_ORG where ORG_ID=".trim($data[ORG_ID_1])." ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis1->send_cmd($cmd1);
			if ($data_dpis1 = $db_dpis1->get_array())
				$ORG_NAME_1 = $data_dpis1[ORG_NAME];
			else
				$ORG_NAME_1 = "";
		} else  $ORG_NAME_1 = "";
		$worksheet->write($xlsRow, 16, $ORG_NAME_1, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

		if (trim($data[ORG_ID_2])) {
			$cmd1 = " select ORG_NAME from PER_ORG where ORG_ID=".trim($data[ORG_ID_2])." ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis1->send_cmd($cmd1);
			if ($data_dpis1 = $db_dpis1->get_array())
				$ORG_NAME_2 = $data_dpis1[ORG_NAME];
			else
				$ORG_NAME_2 = "";
		} else  $ORG_NAME_2 = "";
		$worksheet->write($xlsRow, 17, $ORG_NAME_2, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

		if (trim($data[ORG_ID_3])) {
			$cmd1 = " select ORG_NAME from PER_ORG where ORG_ID=".trim($data[ORG_ID_3])." ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis1->send_cmd($cmd1);
			if ($data_dpis1 = $db_dpis1->get_array())
				$ORG_NAME_3 = $data_dpis1[ORG_NAME];
			else
				$ORG_NAME_3 = "";
		} else  $ORG_NAME_3 = "";
		$worksheet->write($xlsRow, 18, $ORG_NAME_3, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

		$worksheet->write($xlsRow, 19, $data[POH_UNDER_ORG1], set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
		$worksheet->write($xlsRow, 20, $data[POH_UNDER_ORG2], set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
		$worksheet->write($xlsRow, 21, $data[POH_ASS_ORG], set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
		$worksheet->write($xlsRow, 22, $data[POH_ASS_ORG1], set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
		$worksheet->write($xlsRow, 23, $data[POH_ASS_ORG2], set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
		$worksheet->write($xlsRow, 24, $data[POH_SALARY], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		$worksheet->write($xlsRow, 25, $data[POH_SALARY_POS], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		$worksheet->write($xlsRow, 26, $data[POH_REMARK], set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
		$worksheet->write($xlsRow, 27,card_no_format($data[PER_CARDNO],$CARD_NO_DISPLAY), set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		if (trim($data[MOV_CODE])) {
			$cmd1 = " select MOV_NAME, MOV_TYPE from PER_MOVMENT where MOV_CODE='".trim($data[MOV_CODE])."'";
			$db_dpis1->send_cmd($cmd1);
			if ($data_dpis1 = $db_dpis1->get_array())
				$MOV_NAME_text = trim($data_dpis1[MOV_NAME]);
			else
				$MOV_NAME_text = "";
		} else  $MOV_NAME_text = "";
		$worksheet->write($xlsRow, 28, $MOV_NAME_text, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		$worksheet->write($xlsRow, 29, $data[POH_ORG1], set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
		$worksheet->write($xlsRow, 30, $data[POH_ORG2], set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
		$worksheet->write($xlsRow, 31, $data[POH_ORG3], set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
		$worksheet->write($xlsRow, 32, $data[POH_ORG_TRANSFER], set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
		$worksheet->write($xlsRow, 33, $data[POH_ORG], set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
		$worksheet->write($xlsRow, 34, $data[POH_PM_NAME], set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
		$worksheet->write($xlsRow, 35, $data[POH_PL_NAME], set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
		$worksheet->write($xlsRow, 36, $data[POH_SEQ_NO], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		
		if (trim($data[POH_LAST_POSITION])=="Y")  $LAST_POSITION_text = "ใช่";
		elseif (trim($data[POH_LAST_POSITION])=="N")  $LAST_POSITION_text = "ไม่ใช่";
		else  $LAST_POSITION_text = "";
		$worksheet->write($xlsRow, 37, $LAST_POSITION_text, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		$worksheet->write($xlsRow, 38, $data[POH_CMD_SEQ], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		if (trim($data[POH_ISREAL])=="Y")  $ISREAL_text = "จริง";
		elseif (trim($data[POH_ISREAL])=="N")  $ISREAL_text = "ไม่จริง";
		else  $ISREAL = "";
		$worksheet->write($xlsRow, 39, $ISREAL_text, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		if (trim($data[ES_CODE])) {
			$cmd1 = " select ES_NAME from PER_EMP_STATUS where trim(ES_CODE)='".trim($data[ES_CODE])."' ";
			$db_dpis1->send_cmd($cmd1);
			if ($data_dpis1 = $db_dpis1->get_array()) {
				$ES_NAME = trim($data_dpis1[ES_NAME]);
//				if ($ES_CODE != "02") {
//					$PL_NAME_WORK = trim($data[PL_NAME_WORK]);
//					$ORG_NAME_WORK = trim($data[ORG_NAME_WORK]);
//				}
			}  else 
				$ES_NAME = "";
		} else  $ES_NAME = "";
		$worksheet->write($xlsRow, 40, $ES_NAME, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		
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