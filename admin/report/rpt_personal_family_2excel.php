<?	
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$header_width[0] = 15; $header_text[0] = "รหัสรายการ";				//	FML_ID;
	$header_width[1] = 30; $header_text[1] = "ชื่อ";							//	PER_NAME+PER_SURNAME;
	$header_width[2] = 15; $header_text[2] = "ลำดับที่";					//	FML_SEQ;
	$header_width[3] = 20; $header_text[3] = "ประเภท";					//	FML_TYPE;
	$header_width[4] = 20; $header_text[4] = "คำนำหน้าชื่อ";			//	PN_CODE;
	$header_width[5] = 30; $header_text[5] = "ชื่อ";							//	FML_NAME;
	$header_width[6] = 30; $header_text[6] = "นามสกุล";					//	FML_SURNAME;
	$header_width[7] = 10; $header_text[7] = "เพศ";						//	FML_GENDER;
	$header_width[8] = 20; $header_text[8] = "เลขบัตรประชาชน";		//	FML_CARDNO;
	$header_width[9] = 20; $header_text[9] = "วันเกิด";						//	FML_BIRTHDATE;
	$header_width[10] = 20; $header_text[10] = "สถานภาพ";				//	FML_ALIVE;
	$header_width[11] = 20; $header_text[11] = "ศาสนา";				//	RE_CODE;
	$header_width[12] = 30; $header_text[12] = "อาชีพ";					//	OC_CODE;
	$header_width[13] = 40; $header_text[13] = "อาชีพอื่นๆ";			//	OC_OTHER;
	$header_width[14] = 30; $header_text[14] = "สัมพันธ์โดย";			//	FML_BY;
	$header_width[15] = 30; $header_text[15] = "สัมพันธ์โดยอื่นๆ";	//	FML_BY_OTHER;
	$header_width[16] = 20; $header_text[16] = "ประเภทเอกสาร";	//	FML_DOCTYPE;
	$header_width[17] = 20; $header_text[17] = "เลขที่เอกสาร";		//	FML_DOCNO;
	$header_width[18] = 20; $header_text[18] = "วันที่เอกสาร";			//	FML_DOCDATE;
	$header_width[19] = 15; $header_text[19] = "สถานภาพสมรส";	//	MR_CODE;
	$header_width[20] = 15; $header_text[20] = "ประเภทเอกสาร";	//	MR_DOCTYPE;
	$header_width[21] = 20; $header_text[21] = "เลขที่เอกสาร";		//	MR_DOCNO;
	$header_width[22] = 20; $header_text[22] = "วันที่เอกสาร";			//	MR_DOCDATE;
	$header_width[23] = 20; $header_text[23] = "ณ จังหวัด";			//	MR_DOC_PV_CODE;
	$header_width[24] = 20; $header_text[24] = "อาศัยในจังหวัด";		//	PV_CODE;
	$header_width[25] = 10; $header_text[25] = "รหัสไปรษณีย์";		//	POST_CODE;
	$header_width[26] = 30; $header_text[26] = "ไร้ความสามารถ";	//	FML_INCOMPETENT;
	$header_width[27] = 30; $header_text[27] = "เอกสารอ้างอิง";		//	IN_DOCTYPE;
	$header_width[28] = 20; $header_text[28] = "เลขที่เอกสาร";		//	IN_DOCNO;
	$header_width[29] = 40; $header_text[29] = "วันที่เอกสาร";			//	IN_DOCDATE;

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

	if (trim($FML_ID))
		$search_arr_cond[] = "FML_ID = ".trim($FML_ID);
	if (trim($FML_SEQ))
		if ($arr_cond_list["FML_SEQ"])
			$search_arr_cond[] = $arr_cond_list["FML_SEQ"];
		else
			$search_arr_cond[] = "FML_SEQ = ".trim($FML_SEQ);
	if (trim($FML_TYPE))
		$search_arr_cond[] = "FML_TYPE = ".trim($FML_TYPE);
	if (trim($PN_CODE))
		if (strpos(trim($PN_CODE),",")!==false)
			$search_arr_cond[] = "b.PN_CODE in (".fill_arr_string($PN_CODE).")";
		else
			$search_arr_cond[] = "b.PN_CODE = '".trim($PN_CODE)."'";
	if (trim($FML_NAME))
		if ($arr_cond_list["FML_NAME"])
			$search_arr_cond[] = $arr_cond_list["FML_NAME"];
		else
			$search_arr_cond[] = "FML_NAME = '%".trim($FML_NAME)."%'";
	if (trim($FML_SURNAME))
		if ($arr_cond_list["FML_SURNAME"])
			$search_arr_cond[] = $arr_cond_list["FML_SURNAME"];
		else
			$search_arr_cond[] = "FML_SURNAME = '%".trim($FML_SURNAME)."%'";
	if (trim($FML_GENDER))
		$search_arr_cond[] = "FML_GENDER = ".trim($FML_GENDER);
	if (trim($FML_CARDNO))
		if ($arr_cond_list["FML_CARDNO"])
			$search_arr_cond[] = $arr_cond_list["FML_CARDNO"];
		else
			$search_arr_cond[] = "FML_CARDNO = '%".trim($FML_CARDNO)."%'";

	if(trim($FML_BIRTHDATE)){
		$FML_BIRTHDATE =  save_date($FML_BIRTHDATE);
		if ($arr_cond_list["FML_BIRTHDATE"])
			$search_arr_cond[] = $arr_cond_list["FML_BIRTHDATE"];
		else
			$search_arr_cond[] = "FML_BIRTHDATE = '".trim($FML_BIRTHDATE)."'";
	} // end if

	if (trim($FML_ALIVE))
		$search_arr_cond[] = "FML_ALIVE = ".trim($FML_ALIVE);
	if (trim($RE_CODE))
		$search_arr_cond[] = "RE_CODE = '".trim($RE_CODE)."'";
	if (trim($OC_CODE))
		if (strpos(trim($OC_CODE),",")!==false)
			$search_arr_cond[] = "b.OC_CODE in (".fill_arr_string($OC_CODE).")";
		else
			$search_arr_cond[] = "b.OC_CODE = '".trim($OC_CODE)."'";
	if (trim($OC_OTHER))
		if ($arr_cond_list["OC_OTHER"])
			$search_arr_cond[] = $arr_cond_list["OC_OTHER"];
		else
			$search_arr_cond[] = "OC_OTHER = '%".trim($OC_OTHER)."%'";
	if (trim($FML_BY))
		$search_arr_cond[] = "FML_BY = ".trim($FML_BY);
	if (trim($FML_BY_OTHER))
		if ($arr_cond_list["FML_BY_OTHER"])
			$search_arr_cond[] = $arr_cond_list["FML_BY_OTHER"];
		else
			$search_arr_cond[] = "FML_BY_OTHER = '%".trim($FML_BY_OTHER)."%'";
	if (trim($FML_DOCTYPE))
		$search_arr_cond[] = "FML_DOCTYPE = ".trim($FML_DOCTYPE);
	if (trim($FML_DOCNO))
		if ($arr_cond_list["FML_DOCNO"])
			$search_arr_cond[] = $arr_cond_list["FML_DOCNO"];
		else
			$search_arr_cond[] = "FML_DOCNO = '".trim($FML_DOCNO)."'";

	if(trim($FML_DOCDATE)){
		$FML_DOCDATE =  save_date($FML_DOCDATE);
		if ($arr_cond_list["FML_DOCDATE"])
			$search_arr_cond[] = $arr_cond_list["FML_DOCDATE"];
		else
			$search_arr_cond[] = "FML_DOCDATE = '".trim($FML_DOCDATE)."'";
	} // end if
	if (trim($MR_CODE))
		if (strpos(trim($MR_CODE),",")!==false)
			$search_arr_cond[] = "b.MR_CODE in (".fill_arr_string($MR_CODE).")";
		else
			$search_arr_cond[] = "b.MR_CODE = '".trim($MR_CODE)."'";
	if (trim($MR_DOCTYPE))
		$search_arr_cond[] = "MR_DOCTYPE = ".trim($MR_DOCTYPE);
	if (trim($MR_DOCNO))
		if ($arr_cond_list["MR_DOCNO"])
			$search_arr_cond[] = $arr_cond_list["MR_DOCNO"];
		else
			$search_arr_cond[] = "MR_DOCNO = '".trim($MR_DOCNO)."'";

	if(trim($MR_DOCDATE)){
		$MR_DOCDATE =  save_date($MR_DOCDATE);
		if ($arr_cond_list["MR_DOCDATE"])
			$search_arr_cond[] = $arr_cond_list["MR_DOCDATE"];
		else
			$search_arr_cond[] = "MR_DOCDATE = '".trim($MR_DOCDATE)."'";
	} // end if
	if (trim($MR_DOC_PV_CODE))
		if (strpos(trim($MR_DOC_PV_CODE),",")!==false)
			$search_arr_cond[] = "MR_DOC_PV_CODE in (".fill_arr_string($MR_DOC_PV_CODE).")";
		else
			$search_arr_cond[] = "MR_DOC_PV_CODE = '".trim($MR_DOC_PV_CODE)."'";
	if (trim($PV_CODE))
		if (strpos(trim($PV_CODE),",")!==false)
			$search_arr_cond[] = "b.PV_CODE in (".fill_arr_string($PV_CODE).")";
		else
			$search_arr_cond[] = "b.PV_CODE = '".trim($PV_CODE)."'";
	if (trim($POST_CODE))
		if ($arr_cond_list["POST_CODE"])
			$search_arr_cond[] = $arr_cond_list["POST_CODE"];
		else
			$search_arr_cond[] = "POST_CODE = '".trim($POST_CODE)."'";
	if (trim($FML_INCOMPETENT))
		$search_arr_cond[] = "FML_INCOMPETENT = ".trim($FML_INCOMPETENT);
	if (trim($IN_DOCTYPE))
		$search_arr_cond[] = "IN_DOCTYPE = ".trim($IN_DOCTYPE);
	if (trim($IN_DOCNO))
		if ($arr_cond_list["IN_DOCNO"])
			$search_arr_cond[] = $arr_cond_list["IN_DOCNO"];
		else
			$search_arr_cond[] = "IN_DOCNO = '".trim($IN_DOCNO)."'";

	if(trim($IN_DOCDATE)){
		$IN_DOCDATE =  save_date($IN_DOCDATE);
		if ($arr_cond_list["IN_DOCDATE"])
			$search_arr_cond[] = $arr_cond_list["IN_DOCDATE"];
		else
			$search_arr_cond[] = "IN_DOCDATE = '".trim($IN_DOCDATE)."'";
	} // end if

	$search_text = "and ".implode(" and ",$search_arr_cond);

	$cmd = " select PER_NAME, PER_SURNAME, b.* from PER_PERSONAL a, PER_FAMILY b where a.PER_ID = b.PER_ID $search_text order by a.PER_ID ";
	$count_data = $db_dpis->send_cmd($cmd);
	echo "[$cmd] len=".strlen($cmd)."<br>";
	$cnt = 0;

	$cnt = 0;
	$file_limit = 5000;
	$data_limit = 1000;
	$xlsRow = 0;
	$count_org_ref = 0;
	$sheet_no = 0; $sheet_no_text="";

	$report_code = "xls_FAMILY";

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
	$head = "ครอบครัว";
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
		
		$worksheet->write($xlsRow, 0, $data[FML_ID], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		$pname = $data[PER_NAME]." ".$data[PER_SURNAME];
		$worksheet->write($xlsRow, 1, $pname, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
		$worksheet->write($xlsRow, 2, $data[FML_SEQ], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		if (trim($data[FML_TYPE])==1)  $FML_TYPE_text = "บิดา";
		elseif (trim($data[FML_TYPE])==2)  $FML_TYPE_text = "มารดา";
		elseif (trim($data[FML_TYPE])==3)  $FML_TYPE_text = "คู่สมรส";
		elseif (trim($data[FML_TYPE])==3)  $FML_TYPE_text = "บุตร";
		else  $FML_TYPE_text = "";
		$worksheet->write($xlsRow, 3, $FML_TYPE_text, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

		if (trim($data[PN_CODE])) {
			$cmd = " select PN_NAME from PER_PRENAME where PN_CODE='".trim($data[PN_CODE])."' ";
			$db_dpis1->send_cmd($cmd);
			if ($data_dpis1 = $db_dpis1->get_array())
				$PN_NAME_text = trim($data_dpis1[PN_NAME]);
			else
				$PN_NAME_text = "";
		} else  $PN_NAME_text = "";
		$worksheet->write($xlsRow, 4, $PN_NAME_text, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

		$worksheet->write($xlsRow, 5, trim($data[FML_NAME]), set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
		$worksheet->write($xlsRow, 6, trim($data[FML_SURNAME]), set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

		if (trim($data[FML_GENDER])==1)  $FML_GENDER_text = "ชาย";
		elseif (trim($data[FML_GENDER])==2)  $FML_GENDER_text = "หญิง";
		else  $FML_GENDER_text = "";
		$worksheet->write($xlsRow, 7, $FML_GENDER_text, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		
		$worksheet->write($xlsRow, 8, card_no_format(trim($data[FML_CARDNO]),$CARD_NO_DISPLAY), set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		$FML_BIRTHDATE_text = show_date_format(trim($data[FML_BIRTHDATE]),$DATE_DISPLAY);
		$worksheet->write($xlsRow, 9, $FML_BIRTHDATE_text, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		if (trim($data[FML_ALIVE])==1)  $FML_ALIVE_text = "มีชีวิต";
		elseif (trim($data[FML_ALIVE])==2)  $FML_ALIVE_text = "สาบสูญ";
		elseif (trim($data[FML_ALIVE])==3)  $FML_ALIVE_text = "เสียชีวิต";
		else  $FML_ALIVE_text = "";
		$worksheet->write($xlsRow, 10, $FML_ALIVE_text, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		if (trim($data[RE_CODE])) {
			$cmd = " select RE_NAME from PER_RELIGION where RE_CODE='".trim($data[RE_CODE])."' ";
			$db_dpis1->send_cmd($cmd);
			if ($data_dpis1 = $db_dpis1->get_array())
				$RE_NAME_text = trim($data_dpis1[RE_NAME]);
			else
				$RE_NAME_text = "";
		} else  $RE_NAME_text = "";
		$worksheet->write($xlsRow, 11, $RE_NAME_text, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

		if (trim($data[OC_CODE])) {
			$cmd = " select OC_NAME from PER_OCCUPATION where trim(OC_CODE)='".trim($data[OC_CODE])."' ";
			$db_dpis1->send_cmd($cmd);
			if ($data_dpis1 = $db_dpis1->get_array())
				$OC_NAME_text = trim($data_dpis1[OC_NAME]);
			else
				$OC_NAME_text = "";
		} else  $OC_NAME_text = "";
		$worksheet->write($xlsRow, 12, $OC_NAME_text, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

		$worksheet->write($xlsRow, 13, trim($data[OC_OTHER]), set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		if($FML_TYPE==1){ // บิดา
			if (trim($data[FML_BY])==1)  $FML_BY_text = "บิดารับเป็นบุตรบุญธรรม";
			elseif (trim($data[FML_BY])==2)  $FML_BY_text = "บิดาจดทะเบียนสมรสกับมารดา";
			elseif (trim($data[FML_BY])==3)  $FML_BY_text = "บิดารับรองบุตร";
			elseif (trim($data[FML_BY])==4)  $FML_BY_text = "บิดาอยู่กินกับมารดาก่อน 1 ตุลาคม 2478";
			elseif (trim($data[FML_BY])==5)  $FML_BY_text = "บิดาต้องคำสั่งศาล";
			else  $FML_BY_text = "";
		} elseif($FML_TYPE==2){ // มารดา
			if (trim($data[FML_BY])==6)  $FML_BY_text = "มารดาโดยสายเลือด";
			else  $FML_BY_text = "";
		} elseif($FML_TYPE==4){ // บุตร
			if (trim($data[FML_BY])==1)  $FML_BY_text = "บุตรที่บิดารับรอง";
			elseif (trim($data[FML_BY])==2)  $FML_BY_text = "บุตรที่บิดารับเป็นบุตรบุณธรรม";
			elseif (trim($data[FML_BY])==3)  $FML_BY_text = "บิดาจดทะเบียนสมรสกับมารดา";
			elseif (trim($data[FML_BY])==4)  $FML_BY_text = "อื่นๆ";
			else  $FML_BY_text = "";
		} else {
			$FML_BY_text = "";
		}
		$worksheet->write($xlsRow, 14, $FML_BY_text, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		$worksheet->write($xlsRow, 15, trim($data[FML_BY_OTHER]), set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		if($FML_TYPE==1){ // บิดา
			if (trim($data[FML_DOCTYPE])==1)  $FML_DOCTYPE_text = "ทะเบียนสมรส";
			elseif (trim($data[FML_DOCTYPE])==2)  $FML_DOCTYPE_text = "หย่า";
			elseif (trim($data[FML_DOCTYPE])==3)  $FML_DOCTYPE_text = "มรณบัตร-หม้าย";
			else  $FML_DOCTYPE_text = "";
		} elseif($FML_TYPE==2){ // มารดา
			if (trim($data[FML_DOCTYPE])==4)  $FML_DOCTYPE_text = "สูติบัตร หรือทะเบียนบ้านเจ้าของสิทธิ";
			else  $FML_DOCTYPE_text = "";
		} elseif($FML_TYPE==4){ // บุตร
			if (trim($data[FML_DOCTYPE])==1)  $FML_DOCTYPE_text = "ทะเบียนสมรส";
			elseif (trim($data[FML_DOCTYPE])==2)  $FML_DOCTYPE_text = "หย่า";
			elseif (trim($data[FML_DOCTYPE])==3)  $FML_DOCTYPE_text = "มรณบัตร-หม้าย";
			elseif (trim($data[FML_DOCTYPE])==4)  $FML_DOCTYPE_text = "ทะเบียนบ้านบุตร";
			elseif (trim($data[FML_DOCTYPE])==5)  $FML_DOCTYPE_text = "สูติบัตรบุตร";
			else  $FML_DOCTYPE_text = "";
		} else {
			$FML_DOCTYPE_text = "";
		}
		$worksheet->write($xlsRow, 16, $FML_DOCTYPE_text, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		$worksheet->write($xlsRow, 17, trim($data[FML_DOCNO]), set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		$FML_DOCDATE_text = show_date_format(trim($data[FML_DOCDATE]),$DATE_DISPLAY);
		$worksheet->write($xlsRow, 18, $FML_DOCDATE_text, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		if (trim($data[MR_CODE])) {
			$cmd = " select MR_NAME from PER_MARRIED where MR_CODE='".trim($data[MR_CODE])."' ";
			$db_dpis1->send_cmd($cmd);
			if ($data_dpis1 = $db_dpis1->get_array())
				$MR_NAME_text = trim($data_dpis1[MR_NAME]);
			else
				$MR_NAME_text = "";
		} else  $MR_NAME_text = "";
		$worksheet->write($xlsRow, 19, $MR_NAME_text, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

		if (trim($data[MR_DOCTYPE])==1)  $MR_DOCTYPE_text = "ทะเบียนสมรส";
		elseif (trim($data[MR_DOCTYPE])==2)  $MR_DOCTYPE_text = "หย่า";
		elseif (trim($data[MR_DOCTYPE])==3)  $MR_DOCTYPE_text = "มรณบัตร-หม้าย";
		else  $MR_DOCTYPE_text = "";
		$worksheet->write($xlsRow, 20, $MR_DOCTYPE_text, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		$worksheet->write($xlsRow, 21, trim($data[MR_DOCNO]), set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		$MR_DOCDATE_text = show_date_format(trim($data[MR_DOCDATE]),$DATE_DISPLAY);
		$worksheet->write($xlsRow, 22, $MR_DOCDATE_text, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		if (trim($data[MR_DOC_PV_CODE])) {
			$cmd = " select PV_NAME from PER_PROVINCE where trim(PV_CODE)='".trim($data[MR_DOC_PV_CODE])."' ";
			$db_dpis1->send_cmd($cmd);
			if ($data_dpis1 = $db_dpis1->get_array())
				$MR_DOC_PV_NAME_text = trim($data_dpis1[PV_NAME]);
			else
				$MR_DOC_PV_NAME_text = "";
		} else  $MR_DOC_PV_NAME_text = "";
		$worksheet->write($xlsRow, 23, $MR_DOC_PV_NAME_text, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

		if (trim($data[PV_CODE])) {
			$cmd = " select PV_NAME from PER_PROVINCE where trim(PV_CODE)='".trim($data[PV_CODE])."' ";
			$db_dpis1->send_cmd($cmd);
			if ($data_dpis1 = $db_dpis1->get_array())
				$PV_NAME_text = trim($data_dpis1[PV_NAME]);
			else
				$PV_NAME_text = "";
		} else  $PV_NAME_text = "";
		$worksheet->write($xlsRow, 24, $PV_NAME_text, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

		$worksheet->write($xlsRow, 25, trim($data[POST_CODE]), set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		if (trim($data[FML_INCOMPETENT])==1)  $FML_INCOMPETENT_text = "ไร้ความสามารถ/เสมือนไร้ความสามารถ";
		else  $FML_INCOMPETENT_text = "";
		$worksheet->write($xlsRow, 26, $FML_INCOMPETENT_text, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

		if (trim($data[IN_DOCTYPE])==1)  $IN_DOCTYPE_text = "คำสั่งศาล - ไร้ความสามารถ";
		else  $IN_DOCTYPE_text = "";
		$worksheet->write($xlsRow, 27, $IN_DOCTYPE_text, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

		$worksheet->write($xlsRow, 28, trim($data[IN_DOCNO]), set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		$IN_DOCDATE_text = show_date_format(trim($data[IN_DOCDATE]),$DATE_DISPLAY);
		$worksheet->write($xlsRow, 29, $IN_DOCDATE_text, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

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