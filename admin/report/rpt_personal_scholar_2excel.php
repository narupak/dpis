<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$header_width[0] = 10; $header_text[0] = "รหัสทุน";							// SC_ID
	$header_width[1] = 30; $header_text[1] = "ชื่อ";									// for $PER_NAME+$PER_SURNAME
//	$header_width[2] = 10; $header_text[2] = "ประเภททุน";						// SC_TYPE = 1;
	$header_width[2] = 20; $header_text[2] = "เลขที่คำสั่ง";						// SC_DOCNO;
	$header_width[3] = 20; $header_text[3] = "วันที่คำสั่ง";						// SC_DOCDATE;
	$header_width[4] = 20; $header_text[4] = "ประเทศที่สำเร็จ";				// INS_COUNTRY;
	$header_width[5] = 20; $header_text[5] = "ระดับการศึกษา";				// EL_CODE;
	$header_width[6] = 30; $header_text[6] = "วุฒิ";									// EN_CODE;
	$header_width[7] = 30; $header_text[7] = "สาขา";								// EM_CODE;
	$header_width[8] = 30; $header_text[8] = "สถานศึกษา";						// INS_CODE;
	$header_width[9] = 30; $header_text[9] = "สถานศึกษาอื่นๆ";				// SC_INSTITUTE;
	$header_width[10] = 20; $header_text[10] = "วันเริ่มต้น";						// SC_STARTDATE;
	$header_width[11] = 20; $header_text[11] = "ถึงวันที่";						// SC_ENDDATE;
	$header_width[12] = 20; $header_text[12] = "วันสำเร็จ";						// SC_FINISHDATE;
	$header_width[13] = 20; $header_text[13] = "วันที่กลับ";						// SC_BACKDATE;
	$header_width[14] = 15; $header_text[14] = "เกรดเฉลี่ย";					// SC_GRADE;
	$header_width[15] = 20; $header_text[15] = "เกียรตินิยม";					// SC_HONOR;
	$header_width[16] = 20; $header_text[16] = "วันทดสอบอังกฤษ";			// SC_TEST_DATE;
	$header_width[17] = 20; $header_text[17] = "ผลทดสอบอังกฤษ";			// SC_TEST_RESULT;
	$header_width[18] = 30; $header_text[18] = "ประเภททุน";					// SCH_CODE;
	$header_width[19] = 30; $header_text[19] = "หน่วยงานที่ให้ทุน";			// SC_FUND;
	$header_width[20] = 30; $header_text[20] = "ประเทศที่ให้ทุน";				// CT_CODE;
	$header_width[21] = 40; $header_text[21] = "หมายเหตุ";						// SC_REMARK;

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

	if (trim($SC_ID))
		$search_arr_cond[] = "SC_ID = ".trim($SC_ID);

	if (trim($SC_DOCNO))
		if ($arr_cond_list["SC_DOCNO"])
			$search_arr_cond[] = $arr_cond_list["SC_DOCNO"];
		else
			$search_arr_cond[] = "SC_DOCNO = '%".trim($POH_ENDDATE)."%'";

	if($SC_DOCDATE){
		$SC_DOCDATE =  save_date($SC_DOCDATE);
		if ($arr_cond_list["SC_DOCDATE"])
			$search_arr_cond[] = $arr_cond_list["SC_DOCDATE"];
		else
			$search_arr_cond[] = "SC_DOCDATE = '".trim($SC_DOCDATE)."'";
	} // end if

	if (trim($INS_COUNTRY))
		if ($arr_cond_list["INS_COUNTRY"])
			$search_arr_cond[] = $arr_cond_list["INS_COUNTRY"];
		else
			$search_arr_cond[] = "INS_COUNTRY = '%".trim($INS_COUNTRY)."%'";

	if (trim($EL_CODE))
		if (strpos(trim($EL_CODE),",")!==false)
			$search_arr_cond[] = "b.EL_CODE in (".fill_arr_string($EL_CODE).")";
		else
			$search_arr_cond[] = "b.EL_CODE = '".trim($EL_CODE)."'";
	if (trim($EN_CODE))
		if (strpos(trim($EN_CODE),",")!==false)
			$search_arr_cond[] = "b.EN_CODE in (".fill_arr_string($EN_CODE).")";
		else
			$search_arr_cond[] = "b.EN_CODE = '".trim($EN_CODE)."'";
	if (trim($EM_CODE))
		if (strpos(trim($EM_CODE),",")!==false)
			$search_arr_cond[] = "b.EM_CODE in (".fill_arr_string($EM_CODE).")";
		else
			$search_arr_cond[] = "b.EM_CODE = '".trim($EM_CODE)."'";
	if (trim($INS_CODE))
		if (strpos(trim($INS_CODE),",")!==false)
			$search_arr_cond[] = "b.INS_CODE in (".fill_arr_string($INS_CODE).")";
		else
			$search_arr_cond[] = "b.INS_CODE = '".trim($INS_CODE)."'";

	if (trim($SC_INSTITUTE))
		if ($arr_cond_list["SC_INSTITUTE"])
			$search_arr_cond[] = $arr_cond_list["SC_INSTITUTE"];
		else
			$search_arr_cond[] = "SC_INSTITUTE = '%".trim($SC_INSTITUTE)."%'";

	if($SC_STARTDATE){
		$SC_STARTDATE =  save_date($SC_STARTDATE);
		if ($arr_cond_list["SC_STARTDATE"])
			$search_arr_cond[] = $arr_cond_list["SC_STARTDATE"];
		else
			$search_arr_cond[] = "SC_STARTDATE = '".trim($SC_STARTDATE)."'";
	} // end if

	if($SC_ENDDATE){
		$SC_ENDDATE =  save_date($SC_ENDDATE);
		if ($arr_cond_list["SC_ENDDATE"])
			$search_arr_cond[] = $arr_cond_list["SC_ENDDATE"];
		else
			$search_arr_cond[] = "SC_ENDDATE = '".trim($SC_ENDDATE)."'";
	} // end if

	if($SC_FINISHDATE){
		$SC_FINISHDATE =  save_date($SC_FINISHDATE);
		if ($arr_cond_list["SC_FINISHDATE"])
			$search_arr_cond[] = $arr_cond_list["SC_FINISHDATE"];
		else
			$search_arr_cond[] = "SC_FINISHDATE = '".trim($SC_FINISHDATE)."'";
	} // end if

	if($SC_BACKDATE){
		$SC_BACKDATE =  save_date($SC_BACKDATE);
		if ($arr_cond_list["SC_BACKDATE"])
			$search_arr_cond[] = $arr_cond_list["SC_BACKDATE"];
		else
			$search_arr_cond[] = "SC_BACKDATE = '".trim($SC_BACKDATE)."'";
	} // end if

	if (trim($SC_GRADE))
		if ($arr_cond_list["SC_GRADE"])
			$search_arr_cond[] = $arr_cond_list["SC_GRADE"];
		else
			$search_arr_cond[] = "SC_GRADE = ".trim($SC_GRADE);
			
	if (trim($SC_HONOR))
		if ($arr_cond_list["SC_HONOR"])
			$search_arr_cond[] = $arr_cond_list["SC_HONOR"];
		else
			$search_arr_cond[] = "SC_HONOR = ".trim($SC_HONOR);

	if(trim($SC_TEST_DATE)){
		$SC_TEST_DATE =  save_date($SC_TEST_DATE);
		if ($arr_cond_list["SC_TEST_DATE"])
			$search_arr_cond[] = $arr_cond_list["SC_TEST_DATE"];
		else
			$search_arr_cond[] = "SC_TEST_DATE = ".trim($SC_TEST_DATE);
	} // end if

	if (trim($SC_TEST_RESULT))
		if ($arr_cond_list["SC_TEST_RESULT"])
			$search_arr_cond[] = $arr_cond_list["SC_TEST_RESULT"];
		else
			$search_arr_cond[] = "SC_TEST_RESULT = ".trim($SC_TEST_RESULT);
		
	if (trim($SCH_CODE))
		if (strpos(trim($SCH_CODE),",")!==false)
			$search_arr_cond[] = "SCH_CODE in (".fill_arr_string($SCH_CODE).")";
		else
			$search_arr_cond[] = "SCH_CODE = '".trim($SCH_CODE)."'";
	if (trim($SC_FUND))
		if ($arr_cond_list["SC_FUND"])
			$search_arr_cond[] = $arr_cond_list["SC_FUND"];
		else
			$search_arr_cond[] = "SC_FUND = '".trim($SC_FUND)."'";
	if (trim($CT_CODE))
		if (strpos(trim($CT_CODE),",")!==false)
			$search_arr_cond[] = "CT_CODE in (".fill_arr_string($CT_CODE).")";
		else
			$search_arr_cond[] = "CT_CODE = '".trim($CT_CODE)."'";
	if (trim($SC_REMARK))
		if ($arr_cond_list["SC_REMARK"])
			$search_arr_cond[] = $arr_cond_list["SC_REMARK"];
		else
			$search_arr_cond[] = "SC_REMARK like '%".trim($SC_REMARK)."%'";

	$search_text = implode(" and ",$search_arr_cond);
	if (trim($search_text))
		$search_text = "and ".trim($search_text);

	$cmd = " SELECT PER_NAME, PER_SURNAME, b.* FROM PER_PERSONAL a, PER_SCHOLAR b WHERE a.PER_ID=b.PER_ID $search_text ORDER BY a.PER_ID ";
	$count_data = $db_dpis->send_cmd($cmd);
//	echo "[$cmd]<br><br>";

	$cnt = 0;
	$file_limit = 5000;
	$data_limit = 1000;
	$xlsRow = 0;
	$count_org_ref = 0;
	$sheet_no = 0; $sheet_no_text="";

	$report_code = "xls_SCHOLAR";

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
	$head = "การลาศึกษาต่อ";
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

		$worksheet->write($xlsRow, 0, $data[SC_ID], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		$worksheet->write($xlsRow, 1, $pname, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));		
		$worksheet->write($xlsRow, 2, $data[SC_DOCNO], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		
		$SC_DOCDATE_text = show_date_format(trim($data[SC_DOCDATE]),$DATE_DISPLAY);
		$worksheet->write($xlsRow, 3, $SC_DOCDATE_text, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		if(trim($data[INS_CODE])){
			if($DPISDB=="odbc"){
				$cmd = " select 	INS.INS_NAME, CT.CT_NAME
								 from 		PER_INSTITUTE as INS
												left join PER_COUNTRY as CT on (INS.CT_CODE=CT.CT_CODE)
								 where 	INS.INS_CODE='".trim($data[INS_CODE])."' ";
			}elseif($DPISDB=="oci8"){
				$cmd = " select 	PER_INSTITUTE.INS_NAME, PER_COUNTRY.CT_NAME
								 from 		PER_INSTITUTE, PER_COUNTRY
								 where 	PER_INSTITUTE.CT_CODE=PER_COUNTRY.CT_CODE(+) and PER_INSTITUTE.INS_CODE='".trim($data[INS_CODE])."' ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select 	INS.INS_NAME, CT.CT_NAME
								 from 		PER_INSTITUTE as INS
												left join PER_COUNTRY as CT on (INS.CT_CODE=CT.CT_CODE)
								 where 	INS.INS_CODE='".trim($data[INS_CODE])."' ";
			} // end if
			
			$db_dpis1->send_cmd($cmd);
			if ($data1 = $db_dpis1->get_array()) {
				$INS_NAME = $data1[INS_NAME];
				$INS_COUNTRY = $data1[CT_NAME];
			} else {
				$INS_NAME = "";
				$INS_COUNTRY = "";
			}
		} else {
			$INS_NAME = "";
			$INS_COUNTRY = "";
		}// end if
		$worksheet->write($xlsRow, 4, $INS_COUNTRY, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

		if (trim($data[EL_CODE])) {
			$cmd1 = " select EL_NAME from PER_EDUCLEVEL where EL_CODE='".trim($data[EL_CODE])."'  ";
			$db_dpis1->send_cmd($cmd1);
			if ($data_dpis1 = $db_dpis1->get_array())
				$EL_NAME = trim($data_dpis1[EL_NAME]);
			else
				$EL_NAME = "";
		} else  $EL_NAME = "";
		$worksheet->write($xlsRow, 5, $EL_NAME, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		
		if(trim($data[EN_CODE])){
			$cmd = " select EN_NAME from PER_EDUCNAME where EN_CODE='".trim($data[EN_CODE])."' ";
			$db_dpis1->send_cmd($cmd);
			if ($data1 = $db_dpis1->get_array()) 
				$EN_NAME = trim($data1[EN_NAME]);
			else
				$EN_NAME = "";
		} else  $EN_NAME = "";	// end if
		$worksheet->write($xlsRow, 6, $EN_NAME, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		if(trim($data[EM_CODE])){
			$cmd = " select EM_NAME from PER_EDUCMAJOR where EM_CODE='".trim($data[EM_CODE])."' ";
			$db_dpis1->send_cmd($cmd);
			if ($data1 = $db_dpis1->get_array())
				$EM_NAME = trim($data1[EM_NAME]);
			else
				$EM_NAME = "";
		} else  $EM_NAME = "";	// end if
		$worksheet->write($xlsRow, 7, $EM_NAME, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		$worksheet->write($xlsRow, 8, $INS_NAME, set_format("xlsFmtTitle", "B", "L", "TLBR", 0)); // ข้อมูลจาก col 4
		$worksheet->write($xlsRow, 9, trim($data[SC_INSTITUTE]), set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

		$SC_STARTDATE_text = show_date_format(trim($data[SC_STARTDATE]),$DATE_DISPLAY);
		$worksheet->write($xlsRow, 10, $SC_STARTDATE_text, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		$SC_ENDDATE_text  = show_date_format(trim($data[SC_ENDDATE]),$DATE_DISPLAY);
		$worksheet->write($xlsRow, 11, $SC_ENDDATE_text, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		$SC_FINISHDATE_text = show_date_format(trim($data[SC_FINISHDATE]),$DATE_DISPLAY);
		$worksheet->write($xlsRow, 12, $SC_FINISHDATE_text, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		$SC_BACKDATE_text = show_date_format(trim($data[SC_BACKDATE]),$DATE_DISPLAY);
		$worksheet->write($xlsRow, 13, $SC_BACKDATE_text, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		$worksheet->write($xlsRow, 14, trim($data[SC_GRADE]), set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		$worksheet->write($xlsRow, 15, trim($data[SC_HONOR]), set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
		
		$SC_TEST_DATE_text = show_date_format(trim($data[SC_TEST_DATE]),$DATE_DISPLAY);
		$worksheet->write($xlsRow, 16, $SC_TEST_DATE_text, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		$worksheet->write($xlsRow, 17, trim($data[SC_TEST_RESULT]), set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		if(trim($data[SCH_CODE])){
			$cmd = " select SCH_NAME from PER_SCHOLARSHIP where SCH_CODE='".trim($data[SCH_CODE])."' ";
			$db_dpis1->send_cmd($cmd);
			if ($data1 = $db_dpis1->get_array()) 
				$SCH_NAME = $data1[SCH_NAME];
			else
				$SCH_NAME = "";
		} else  $SCH_NAME = "";	// end if
		$worksheet->write($xlsRow, 18, $SCH_NAME, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

		$worksheet->write($xlsRow, 19, trim($data[SC_FUND]), set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

		if(trim($data[CT_CODE])){
			$cmd = " select CT_NAME from PER_COUNTRY where CT_CODE='".trim($data[CT_CODE])."' ";
			$db_dpis1->send_cmd($cmd);
			if ($data1 = $db_dpis1->get_array()) 
				$CT_NAME = $data1[CT_NAME];
			else
				$CT_NAME = "";
		} else  $CT_NAME = "";	// end if
		$worksheet->write($xlsRow, 20, $CT_NAME, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

		$worksheet->write($xlsRow, 21, trim($data[SC_REMARK]), set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
		
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