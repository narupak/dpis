<?	
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include("../php_scripts/function_share.php");
	
	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$header_width[0] = 20; $header_text[0] = "ประเภทบุคคล"; 			// for $PER_TYPE
	$header_width[1] = 30; $header_text[1] = "ประเภทข้าราชการ"; 	// for $OT_CODE
	$header_width[2] = 15; $header_text[2] = "คำนำหน้าชื่อ";			// for $PN_CODE
	$header_width[3] = 30; $header_text[3] = "ชื่อ";							// for $PER_NAME
	$header_width[4] = 30; $header_text[4] = "นามสกุล";					// for $PER_SURNAME
	$header_width[5] = 30; $header_text[5] = "ชื่อ อังกฤษ";				// for $PER_ENG_NAME
	$header_width[6] = 30; $header_text[6] = "นามสกุล อังกฤษ";		// for $PER_ENG_SURNAME
	$header_width[7] = 5; $header_text[7] = "เพศ"; 						// for $PER_GENDER 1 ชาย 2 หญิง
	$header_width[8] = 20; $header_text[8] = "สังกัด"; 						// for $ORG_ID
	$header_width[9] = 10; $header_text[9] = "เลขตำแหน่ง";				// for $POS_NO
	$header_width[10] = 30; $header_text[10] = "ตำแหน่งสายงาน";		// for $PL_NAME
	$header_width[11] = 30; $header_text[11] = "ตำแหน่งบริหาร";		// for $PM_NAME
	$header_width[12] = 10; $header_text[12] = "สถานะภาพ";			// for $MR_CODE
	$header_width[13] = 30; $header_text[13] = "ระดับ";					// for $LEVEL_NO
	$header_width[14] = 20; $header_text[14] = "ประเภทการเคลื่อนไหว";	// for $MOV_CODE
	$header_width[15] = 30; $header_text[15] = "หน่วยงานมอบหมาย";	// for $PER_ORGMGT
	$header_width[16] = 10; $header_text[16] = "เงินเดือน";				// for $PER_SALARY
	$header_width[17] = 10; $header_text[17] = "เงินประจำตำแหน่ง";	// for $PER_MGTSALARY
	$header_width[18] = 10; $header_text[18] = "เงินพิเศษ";				// for $PER_SPSALARY
	$header_width[19] = 20; $header_text[19] = "เลขประชาชน";		// for $PER_CARDNO
	$header_width[20] = 20; $header_text[20] = "เลขข้าราชการ";		// for $PER_OFFNO
	$header_width[21] = 15; $header_text[21] = "เลขภาษี"; 				// for $PER_TAXNO
	$header_width[22] = 10; $header_text[22] = "กลุ่มเลือด"; 			// for $PER_BLOOD
	$header_width[23] = 10; $header_text[23] = "ศาสนา";				// for $RE_CODE
	$header_width[24] = 10; $header_text[24] = "วันเกิด";			 		// for $PER_BIRTHDATE
	$header_width[25] = 10; $header_text[25] = "วันเริ่มงาน";			// for $PER_STARTDATE
	$header_width[26] = 10; $header_text[26] = "วันบรรจุ";				// for $PER_OCCUPYDATE
	$header_width[27] = 15; $header_text[27] = "วันพ้นราชการ";		// for $PER_POSDATE
	$header_width[28] = 20; $header_text[28] = "คำนำหน้าชื่อบิดา";	// for $PN_CODE_F
	$header_width[29] = 30; $header_text[29] = "ชื่อบิดา";				// for $PER_FATHER_NAME
	$header_width[30] = 30; $header_text[30] = "นามสกุลบิดา";		// for $PER_FATHER_SURNAME
	$header_width[31] = 20; $header_text[31] = "คำนำหน้าชื่อมารดา";	// for $PN_CODE_M
	$header_width[32] = 30; $header_text[32] = "ชื่อมารดา";				// for $PER_MOTHER_NAME
	$header_width[33] = 30; $header_text[33] = "นามสกุลมารดา";	// for $PER_MOTHER_SURNAME
	$header_width[34] = 20; $header_text[34] = "ภูมิลำเนาเดิม";		// for $PV_CODE
	$header_width[35] = 10; $header_text[35] = "อุปสมบท";				// for $PER_ORDAIN
	$header_width[36] = 10; $header_text[36] = "เกณท์ทหาร";			// for $PER_SOLDIER
	$header_width[37] = 15; $header_text[37] = "สมาชิก กบข./กสจ.";	// for $PER_MEMBER
	$header_width[38] = 10; $header_text[38] = "สถานภาพ";			// for $PER_STATUS
	$header_width[39] = 20; $header_text[39] = "HiPPS";					// for $PER_HIP_FLAG
	$header_width[40] = 15; $header_text[40] = "เลขประกอบวิชาชีพ"; 	// for $PER_CERT_OCC
	$header_width[41] = 30; $header_text[41] = "กรม";					// for $DEPARTMENT_ID
	$header_width[42] = 20; $header_text[42] = "กระบอกเงินเดือน";	// for $LEVEL_NO_SALARY
	$header_width[43] = 10; $header_text[43] = "ชื่อเล่น";					// for $PER_NICKNAME
	$header_width[44] = 15; $header_text[44] = "โทร.บ้าน";			// for $PER_HOME_TEL
	$header_width[45] = 15; $header_text[45] = "โทร.ที่ทำงาน";	// for $PER_OFFICE_TEL
	$header_width[46] = 15; $header_text[46] = "Fax No.";			// for $PER_FAX
	$header_width[47] = 15; $header_text[47] = "โทร.พกพา";		// for $PER_MOBILE
	$header_width[48] = 30; $header_text[48] = "E-Mail";				// for $PER_EMAIL
	$header_width[49] = 10; $header_text[49] = "เลขที่แฟ้ม";		// for $PER_FILE_NO
	$header_width[50] = 15; $header_text[50] = "บ/ช.ธนาคาร";	// for $PER_BANK_ACC
	$header_width[51] = 10; $header_text[51] = "เลขที่แฟ้ม";		// for $PER_CONTACT_PERSON
	$header_width[52] = 30; $header_text[52] = "หมายเหตุ";			// for $PER_REMARK
	$header_width[53] = 30; $header_text[53] = "สังกัดเริ่มต้น";		// for $PER_START_ORG
	$header_width[54] = 15; $header_text[54] = "สมาชิกสหกรณ์";	// for $PER_COOPERATIVE
	$header_width[55] = 20; $header_text[55] = "เลขที่สมาชิกสหกรณ์";	// for $PER_COOPERATIVE_NO
	$header_width[56] = 20; $header_text[56] = "วันที่เป็นสมาชิก กบข./กสจ.";	// for $PER_MEMBERDATE
	$header_width[57] = 20; $header_text[57] = "สถานะการดำรงตำแหน่ง";	// for $ES_CODE
	$header_width[58] = 20; $header_text[58] = "การถือจ่าย";		// for $PAY_ID
	$header_width[59] = 30; $header_text[59] = "ตำแหน่ง";			// for $PL_NAME_WORK
	$header_width[60] = 30; $header_text[60] = "สังกัด";				// for $ORG_NAME_WORK
	$header_width[61] = 15; $header_text[61] = "เลขที่คำสั่งล่าสุด";	// for $PER_DOCNO
	$header_width[62] = 15; $header_text[62] = "วันที่มีคำสั่ง";	// for $PER_DOCDATE
	$header_width[63] = 15; $header_text[63] = "วันที่มีคำสั่งมีผล";	// for $PER_EFFECTIVEDATE
	$header_width[64] = 30; $header_text[64] = "เหตุผล";		// for $PER_POS_REASON
	$header_width[65] = 10; $header_text[65] = "ปี";				// for $PER_POS_YEAR
	$header_width[66] = 10; $header_text[66] = "ประเภทเอกสาร";	// for $PER_POS_DOCTYPE
	$header_width[67] = 10; $header_text[67] = "เลขที่เอกสาร";		// for $PER_POS_DOCNO
	$header_width[68] = 30; $header_text[68] = "ตำแหน่งในสังกัด";	// for $PER_POS_ORG
	$header_width[69] = 30; $header_text[69] = "รายละเอียดการอุปสมบท";	// for $PER_ORDAIN_DETAIL
	$header_width[70] = 30; $header_text[70] = "หมายเหตุตำแหน่ง";	// for $PER_POS_ORGMGT

	require_once("excel_headpart_subrtn.php");
	
	$search_arr_cond = array();
	
	if (trim($PER_TYPE) || trim($PER_TYPE) > 0) // 0 คือทั้งหมด ไม่ต้องสร้างเงื่อนไข
		$search_arr_cond[] = "PER_TYPE = ".trim($PER_TYPE);
	if (trim($OT_CODE))
		if (strpos(trim($OT_CODE),",") === false)
			$search_arr_cond[] = "OT_CODE = ".trim($OT_CODE);
		else
			$search_arr_cond[] = "OT_CODE in (".trim($OT_CODE).")";
	if (trim($PN_CODE))
		$search_arr_cond[] = "PN_CODE = ".trim($PN_CODE);
	if (trim($PER_NAME)) {
		if ($arr_cond_list["PER_NAME"])
			$search_arr_cond[] = $arr_cond_list["PER_NAME"];
		else
			$search_arr_cond[] = "PER_NAME = '%".trim($PER_NAME)."%'";
	}
	if (trim($PER_SURNAME))
		if ($arr_cond_list["PER_SURNAME"])
			$search_arr_cond[] = $arr_cond_list["PER_SURNAME"];
		else
			$search_arr_cond[] = "PER_SURNAME like '%".trim($PER_SURNAME)."%'";
	if (trim($PER_ENG_NAME))
		$search_arr_cond[] = "PER_ENG_NAME like '%".trim($PER_ENG_NAME)."%'";
	if (trim($PER_ENG_SURNAME))
		$search_arr_cond[] = "PER_ENG_SURNAME like '%".trim($PER_ENG_SURNAME)."%'";
	if (trim($ASS_ORG_ID))
		if (strpos(trim($ASS_ORG_ID),",") === false)
			$search_arr_cond[] = "ORG_ID = ".trim($ASS_ORG_ID);
		else
			$search_arr_cond[] = "ORG_ID in (".trim($ASS_ORG_ID).")";

	if ($PER_TYPE==1) {
		$POS_ID = (trim($POS_ID))? $POS_ID : "";
		$PAY_ID = (trim($PAY_ID))? $PAY_ID : "";
		$POEM_ID = $POEMS_ID = "";
	} elseif ($PER_TYPE==2) {
		$POEM_ID = (trim($POS_ID))? $POS_ID : "";
		$POS_ID = $PAY_ID = $POEMS_ID = "";			
	} elseif ($PER_TYPE==3) {
		$POEMS_ID = (trim($POS_ID))? $POS_ID : "";
		$POS_ID = $PAY_ID = $POEM_ID = "";			
	}
	if (trim($POS_ID))
		if (strpos(trim($POS_ID),",") === false)
			$search_arr_cond[] = "POS_ID = ".trim($POS_ID);
		else
			$search_arr_cond[] = "POS_ID in (".trim($POS_ID).")";
	if (trim($POEM_ID))
		if (strpos(trim($POEM_ID),",") === false)
			$search_arr_cond[] = "POEM_ID = ".trim($POEM_ID);
		else
			$search_arr_cond[] = "POEM_ID in (".trim($POEM_ID).")";
	if (trim($POEMS_ID))
		if (strpos(trim($POEMS_ID),",") === false)
			$search_arr_cond[] = "POEMS_ID = ".trim($POEMS_ID);
		else
			$search_arr_cond[] = "POEMS_ID in (".trim($POEMS_ID).")";
	
	if (trim($LEVEL_NO))
		$search_arr_cond[] = "LEVEL_NO = ".trim($LEVEL_NO);
	if (trim($PER_ORGMGT))
		$search_arr_cond[] = "PER_ORGMGT = ".trim($PER_ORGMGT);
	if (trim($PER_SALARY))
		if ($arr_cond_list["PER_SALARY"])
			$search_arr_cond[] = $arr_cond_list["PER_SALARY"];
		else
			$search_arr_cond[] = "PER_SALARY = ".trim($PER_SALARY);
//	if (trim($PER_MGTSALARY))
//		$search_arr_cond[] = "PER_MGTSALARY = ".trim($PER_MGTSALARY);
//	if (trim($PER_SPSALARY))
//		$search_arr_cond[] = "PER_SPSALARY = ".trim($PER_SPSALARY);
	if (trim($PER_GENDER) && $PER_GENDER != 3) // กรณี = 3 คือหา ทุกเพศ ไม่ต้องสร้าง condition
		$search_arr_cond[] = "PER_GENDER = ".trim($PER_GENDER);
	if (trim($MR_CODE))
		$search_arr_cond[] = "MR_CODE = ".trim($MR_CODE);
	if (trim($PER_CARDNO))
		if ($arr_cond_list["PER_CARDNO"])
			$search_arr_cond[] = $arr_cond_list["PER_CARDNO"];
		else
			$search_arr_cond[] = "PER_CARDNO = ".trim($PER_CARDNO);
	if (trim($PER_OFFNO))
		if ($arr_cond_list["PER_OFFNO"])
			$search_arr_cond[] = $arr_cond_list["PER_OFFNO"];
		else
			$search_arr_cond[] = "PER_OFFNO = ".trim($PER_OFFNO);
	if (trim($PER_TAXNO))
		if ($arr_cond_list["PER_TAXNO"])
			$search_arr_cond[] = $arr_cond_list["PER_TAXNO"];
		else
			$search_arr_cond[] = "PER_TAXNO = ".trim($PER_TAXNO);
	if (trim($PER_BLOOD))
		$search_arr_cond[] = "PER_BLOOD = '".trim($PER_BLOOD)."'";
	if (trim($RE_CODE))
		$search_arr_cond[] = "RE_CODE = ".trim($RE_CODE);

	if($PER_BIRTHDATE){
		$arr_temp = explode("/", $PER_BIRTHDATE);
		$PER_BIRTHDATE = ($arr_temp[2] - 543) ."-". str_pad($arr_temp[1], 2, "0", STR_PAD_LEFT) ."-". str_pad($arr_temp[0], 2, "0", STR_PAD_LEFT);
		$RETIRE_YEAR = ($arr_temp[2] - 543) + 60;
			
		if($arr_temp[1] > 10 || ($arr_temp[1]==10 && $arr_temp[0] > "01"))
			$RETIRE_YEAR += 1;
		$PER_RETIREDATE = $RETIRE_YEAR."-10-01";	
	} // end if
	if (trim($PER_BIRTHDATE))
		if ($arr_cond_list["PER_BIRTHDATE"])
			$search_arr_cond[] = $arr_cond_list["PER_BIRTHDATE"];
		else
			$search_arr_cond[] = "PER_BIRTHDATE = '".trim($PER_BIRTHDATE)."'";
	
	if($PER_STARTDATE){
		$PER_STARTDATE =  save_date($PER_STARTDATE);
		if ($arr_cond_list["PER_STARTDATE"])
			$search_arr_cond[] = $arr_cond_list["PER_STARTDATE"];
		else
			$search_arr_cond[] = "PER_STARTDATE = '".trim($PER_STARTDATE)."'";
	} // end if

	if($PER_OCCUPYDATE){
		$PER_OCCUPYDATE =  save_date($PER_OCCUPYDATE);
		if ($arr_cond_list["PER_OCCUPYDATE"])
			$search_arr_cond[] = $arr_cond_list["PER_OCCUPYDATE"];
		else
			$search_arr_cond[] = "PER_OCCUPYDATE = '".trim($PER_OCCUPYDATE)."'";
	} // end if

	if($PER_POSDATE){
		$PER_POSDATE =  save_date($PER_POSDATE);
		if ($arr_cond_list["PER_POSDATE"])
			$search_arr_cond[] = $arr_cond_list["PER_POSDATE"];
		else
			$search_arr_cond[] = "PER_POSDATE = '".trim($PER_POSDATE)."'";
	} // end if

	if (trim($PN_CODE_F))
		$search_arr_cond[] = "PN_CODE_F = ".trim($PN_CODE_F);
	if (trim($PER_FATHER_NAME))
		$search_arr_cond[] = "PER_FATHER_NAME like '%".trim($PER_FATHER_NAME)."%'";
	if (trim($PER_FATHER_SURNAME))
		$search_arr_cond[] = "PER_FATHER_SURNAME like '%".trim($PER_FATHER_SURNAME)."%'";
	if (trim($PN_CODE_M))
		$search_arr_cond[] = "PN_CODE_M = ".trim($PN_CODE_M);
	if (trim($PER_MOTHER_NAME))
		$search_arr_cond[] = "PER_MOTHER_NAME like '%".trim($PER_MOTHER_NAME)."%'";
	if (trim($PER_MOTHER_SURNAME))
		$search_arr_cond[] = "PER_MOTHER_SURNAME like '%".trim($PER_MOTHER_SURNAME)."%'";
	if (trim($PV_PER_CODE))
		$search_arr_cond[] = "PV_CODE = ".trim($PV_PER_CODE);
	if (trim($MOV_CODE))
		if (strpos(trim($MOV_CODE),",") === false)
			$search_arr_cond[] = "MOV_CODE = '".trim($MOV_CODE)."'";
		else
			$search_arr_cond[] = "MOV_CODE in (".fill_arr_string($MOV_CODE).")";
	if (trim($PER_ORDAIN))
		$search_arr_cond[] = "PER_ORDAIN = ".trim($PER_ORDAIN);
	if (trim($PER_SOLDIER))
		$search_arr_cond[] = "PER_SOLDIER = ".trim($PER_SOLDIER);
	if (trim($PER_MEMBER))
		$search_arr_cond[] = "PER_MEMBER = ".trim($PER_MEMBER);
	if (trim($PER_STATUS) && trim($PER_STATUS)!=3)	// 3 คือทั้งหมด ไม่ต้องสร้างเงื่อนไข
		$search_arr_cond[] = "PER_STATUS = ".trim($PER_STATUS);

	for ($i=0; $i<count($PER_HIP_FLAG); $i++) {
		$HIP_FLAG_TXT.= "||$PER_HIP_FLAG[$i]";
	}
	$HIP_FLAG_TXT = (trim($HIP_FLAG_TXT))? $HIP_FLAG_TXT."||" : "";
	if (trim($HIP_FLAG_TXT))
		$search_arr_cond[] = "PER_HIP_FLAG = '".trim($HIP_FLAG_TXT)."'";

	if (trim($PER_CERT_OCC))
		$search_arr_cond[] = "PER_CERT_OCC = ".trim($PER_CERT_OCC);
	if (trim($DEPARTMENT_ID))
		$search_arr_cond[] = "DEPARTMENT_ID = ".trim($DEPARTMENT_ID);
	if (trim($LEVEL_NO_SALARY))
		$search_arr_cond[] = "LEVEL_NO_SALARY = ".trim($LEVEL_NO_SALARY);
	if (trim($PER_NICKNAME))
		$search_arr_cond[] = "PER_NICKNAME like '%".trim($PER_NICKNAME)."%'";
	if (trim($PER_HOME_TEL))
		$search_arr_cond[] = "PER_HOME_TEL = ".trim($PER_HOME_TEL);
	if (trim($PER_OFFICE_TEL))
		$search_arr_cond[] = "PER_OFFICE_TEL = ".trim($PER_OFFICE_TEL);
	if (trim($PER_FAX))
		$search_arr_cond[] = "PER_FAX = ".trim($PER_FAX);
	if (trim($PER_MOBILE))
		$search_arr_cond[] = "PER_MOBILE = ".trim($PER_MOBILE);
	if (trim($PER_EMAIL))
		$search_arr_cond[] = "PER_EMAIL = ".trim($PER_EMAIL);
	if (trim($PER_FILE_NO))
		$search_arr_cond[] = "PER_FILE_NO = ".trim($PER_FILE_NO);
	if (trim($PER_BANK_ACCOUNT))
		$search_arr_cond[] = "PER_BANK_ACCOUNT = ".trim($PER_BANK_ACCOUNT);
	if (trim($PER_CONTACT_PERSON))
		$search_arr_cond[] = "PER_CONTACT_PERSON = ".trim($PER_CONTACT_PERSON);
	if (trim($PER_REMARK))
		$search_arr_cond[] = "PER_REMARK = ".trim($PER_REMARK);
	if (trim($PER_START_ORG))
		$search_arr_cond[] = "PER_START_ORG = ".trim($PER_START_ORG);
	if (trim($PER_COOPERATIVE))
		$search_arr_cond[] = "PER_COOPERATIVE = ".trim($PER_COOPERATIVE);
	if (trim($PER_COOPERATIVE_NO))
		$search_arr_cond[] = "PER_COOPERATIVE_NO = ".trim($PER_COOPERATIVE_NO);

	if($PER_MEMBERDATE){
		$PER_MEMBERDATE =  save_date($PER_MEMBERDATE);
		$search_arr_cond[] = "PER_MEMBERDATE = '".trim($PER_MEMBERDATE)."'";
	} // end if

	if (trim($ES_CODE))
		if (strpos(trim($ES_CODE),",") === false)
			$search_arr_cond[] = "ES_CODE = '".trim($ES_CODE)."'";
		else
			$search_arr_cond[] = "ES_CODE in (".fill_arr_string($ES_CODE).")";
	if (trim($PAY_ID))
		if (strpos(trim($PAY_ID),",") === false)
			$search_arr_cond[] = "PAY_ID = ".trim($PAY_ID);
		else
			$search_arr_cond[] = "PAY_ID in (".trim($PAY_ID).")";
	if (trim($PL_NAME_WORK))
		$search_arr_cond[] = "PL_NAME_WORK = ".trim($PL_NAME_WORK);
	if (trim($ORG_NAME_WORK))
		$search_arr_cond[] = "ORG_NAME_WORK = ".trim($ORG_NAME_WORK);
	if (trim($PER_DOCNO))
		$search_arr_cond[] = "PER_DOCNO = ".trim($PER_DOCNO);

	if($PER_DOCDATE){
		$PER_DOCDATE =  save_date($PER_DOCDATE);
		$search_arr_cond[] = "PER_DOCDATE = '".trim($PER_DOCDATE)."'";
	} // end if

	if($PER_EFFECTIVEDATE){
		$PER_EFFECTIVEDATE =  save_date($PER_EFFECTIVEDATE);
		$search_arr_cond[] = "PER_EFFECTIVEDATE = '".trim($PER_EFFECTIVEDATE)."'";
	} // end if

	if (trim($PER_POS_REASON))
		$search_arr_cond[] = "PER_POS_REASON = ".trim($PER_POS_REASON);
	if (trim($PER_POS_YEAR))
		$search_arr_cond[] = "PER_POS_YEAR = ".trim($PER_POS_YEAR);
	if (trim($PER_POS_DOCTYPE))
		$search_arr_cond[] = "PER_POS_DOCTYPE = ".trim($PER_POS_DOCTYPE);
	if (trim($PER_POS_DOCNO))
		$search_arr_cond[] = "PER_POS_DOCNO = ".trim($PER_POS_DOCNO);
	if (trim($PER_POS_ORG))
		$search_arr_cond[] = "PER_POS_ORG = ".trim($PER_POS_ORG);
	if (trim($PER_ORDAIN_DETAIL))
		$search_arr_cond[] = "PER_ORDAIN_DETAIL = ".trim($PER_ORDAIN_DETAIL);
//	if (trim($PER_POS_ORGMGT))
//		$search_arr_cond[] = "PER_POS_ORGMGT = ".trim($PER_POS_ORGMGT);
	
	$search_text = " where ".implode(" and ",$search_arr_cond);

	$cmd = "select * from PER_PERSONAL $search_text order by PER_ID";
//	echo "[$cmd]<br><br>";
	$count_data = $db_dpis->send_cmd($cmd);

	$cnt = 0;
	$file_limit = 5000;
	$data_limit = 1000;
	$xlsRow = 0;
	$count_org_ref = 0;
	$sheet_no = 0; $sheet_no_text="";

	$report_code = "xls_MASTER";

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
	$head = "ทั่วไป";
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

			$worksheet = &$workbook->addworksheet("$report_code".$fnum_text.$sheet_no_text);
			$worksheet->set_margin_right(0.50);
			$worksheet->set_margin_bottom(1.10);

//			echo "$data_count>>sheet name=$report_code$fnum_text$sheet_no_text<br>";
			
			call_header($data_count, $head);
		}
		
		$xlsRow++;
		
		$PER_TYPE_TEXT = $PERSON_TYPE[$PER_TYPE];
		
		$worksheet->write($xlsRow, 0, $PER_TYPE_TEXT, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		
		if (trim($data[OT_CODE])) {
			$cmd1 = " select OT_NAME from PER_OFF_TYPE where trim(OT_CODE)='".trim($data[OT_CODE])."' ";
			$db_dpis1->send_cmd($cmd1);
			if ($data_dpis1 = $db_dpis1->get_array())
				$OT_NAME = trim($data_dpis1[OT_NAME]);
			else 
				$OT_NAME = "";
		} else  $OT_NAME = "";
		$worksheet->write($xlsRow, 1, $OT_NAME, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		
		if (trim($data[PN_CODE])) {
			$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='".trim($data[PN_CODE])."' ";
			$db_dpis1->send_cmd($cmd);
			if ($data_dpis1 = $db_dpis1->get_array())
				$PN_NAME = trim($data_dpis1[PN_NAME]);
			else
				$PN_NAME = "";
		} else  $PN_NAME = "";
		$worksheet->write($xlsRow, 2, $PN_NAME, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
		
		$worksheet->write($xlsRow, 3, $data[PER_NAME], set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
		$worksheet->write($xlsRow, 4, $data[PER_SURNAME], set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
		$worksheet->write($xlsRow, 5, $data[PER_ENG_NAME], set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
		$worksheet->write($xlsRow, 6, $data[PER_ENG_SURNAME], set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

		if (trim($data[PER_GENDER]) == 1)  $PER_GENDER_text = "ชาย";
		else if (trim($data[PER_GENDER]) == 2)  $PER_GENDER_text = "หญิง";
		else	$PER_GENDER_text = "";
		$worksheet->write($xlsRow, 7, $PER_GENDER_text, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		
		if (trim($data[ORG_ID])) {
			$cmd1 = " select ORG_NAME from PER_ORG where ORG_ID=".trim($data[ORG_ID])." ";
			if($SESS_ORG_STRUCTURE==1) $cmd1 = str_replace("PER_ORG", "PER_ORG_ASS", $cmd1);
			$db_dpis1->send_cmd($cmd1);
			if ($data_dpis1 = $db_dpis1->get_array())
				$ORG_NAME = $data_dpis1[ORG_NAME];
			else
				$ORG_NAME = "";
		} else  $ORG_NAME = "";
		$worksheet->write($xlsRow, 8, $ORG_NAME, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		
//		if ($data[PER_TYPE]==1) {
//			$s_POS_ID = (trim($data[POS_ID]))? $data[POS_ID] : "NULL";
//		} elseif ($data[PER_TYPE]==2) {
//			$s_POS_ID = (trim($data[POEM_ID]))? $data[POEM_ID] : "NULL";
//		} elseif ($data[PER_TYPE]==3) {
//			$s_POS_ID = (trim($data[POEMS_ID]))? $data[POEMS_ID] : "NULL";
//		} else $s_POS_ID = "";
//		if (trim($s_POS_ID)) {
//		  	$cmd1 = " select POS_NAME from PER_POSITION where POS_ID = ".trim($s_POS_ID)." ";
//			$db_dpis1->send_cmd($cmd1);
//			if ($data_dpis1 = $db_dpis1->get_array())
//				$POS_NAME = $data_dpis1[POS_NAME];
//			else
//				$POS_NAME = "";
//		} else  $POS_NAME = "";

		if (trim($data[POS_ID])) {			
			$cmd1 = " select  POS_NO, PM_CODE, PL_CODE
							from 	PER_POSITION where POS_ID=".$data[POS_ID]." ";
			$db_dpis1->send_cmd($cmd1);
			$data_dpis1 = $db_dpis1->get_array();
			$POS_NO = trim($data_dpis1[POS_NO]);
			$PM_CODE = trim($data_dpis1[PM_CODE]);
			$PL_CODE = trim($data_dpis1[PL_CODE]);

			$cmd1 = " select PM_NAME from PER_MGT where trim(PM_CODE)='$PM_CODE' ";			
			$db_dpis1->send_cmd($cmd1);
			$data_dpis1 = $db_dpis1->get_array();
			$PM_NAME = trim($data_dpis1[PM_NAME]);
			
			$cmd1 = " select PL_NAME from PER_LINE where trim(PL_CODE)='$PL_CODE' ";
			$db_dpis1->send_cmd($cmd1);
			$data_dpis1 = $db_dpis1->get_array();
			$PL_NAME = trim($data_dpis1[PL_NAME]);
		} else if (trim($data[POEM_ID])) {
		//  ===== ถ้าเป็นลูกจ้างประจำ SELECT ข้อมูลตำแหน่งจาก table PER_POS_EMP =====  PER_TYPE=2
			$cmd1 = " select 	POEM_NO, PN_CODE
							from 	PER_POS_EMP where POEM_ID=".trim($data[POEM_ID])." ";
			$db_dpis1->send_cmd($cmd1);
			$data_dpis1 = $db_dpis1->get_array();
			$POEM_NO = trim($data_dpis1[POEM_NO]);
			$POS_ID = $POEM_ID;
			$POS_NO = trim($data_dpis1[POEM_NO]);
			$PER_POS_CODE = trim($data_dpis1[PN_CODE]);
			
			//  table  PER_POS_EMP = ตำแหน่งลูกจ้างประจำ
			$cmd1 = " select PN_NAME from PER_POS_NAME where trim(PN_CODE)='$PER_POS_CODE' ";
			$db_dpis1->send_cmd($cmd1);
			$data_dpis1 = $db_dpis1->get_array();
			$PL_NAME = trim($data_dpis1[PN_NAME]);
			$PM_NAME = "";
		} else if (trim($data[POEMS_ID])) {
		//  ===== ถ้าเป็นพนักงานราชการ SELECT ข้อมูลตำแหน่งจาก table PER_POS_EMPSER =====  PER_TYPE=3
			$cmd1 = " select  POEMS_NO, EP_CODE
							from 	PER_POS_EMPSER where POEMS_ID=".trim($data[POEMS_ID])." ";
			$db_dpis1->send_cmd($cmd1);
			$data_dpis1 = $db_dpis1->get_array();
			$POEMS_NO = trim($data_dpis1[POEMS_NO]);
			$POS_ID = $POEMS_ID;			
			$POS_NO = trim($data_dpis1[POEMS_NO]);
			$PER_POS_CODE = trim($data_dpis1[EP_CODE]);
			
			//  table  PER_POS_EMP = ตำแหน่งพนักงานราชการ
			$cmd1 = " select EP_NAME from PER_EMPSER_POS_NAME where trim(EP_CODE)='$PER_POS_CODE' ";
			$db_dpis1->send_cmd($cmd1);
			$data_dpis1 = $db_dpis1->get_array();
			$PL_NAME = trim($data_dpis1[EP_NAME]);
			$PM_NAME = "";
		}
		
		$worksheet->write($xlsRow, 9, $POS_NO, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));	
		
		$worksheet->write($xlsRow, 10, $PL_NAME, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		$worksheet->write($xlsRow, 11, $PM_NAME, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		
		if (trim($data[MR_CODE])) {
		  	$cmd1 = " select MR_NAME from PER_MARRIED where MR_CODE = '".trim($data[MR_CODE])."' ";
			$db_dpis1->send_cmd($cmd1);
			if ($data_dpis1 = $db_dpis1->get_array())
				$MR_NAME_text = $data_dpis1[MR_NAME];
			else
				$MR_NAME_text = "";
		} else  $MR_NAME_text = "";
		$worksheet->write($xlsRow, 12, $MR_NAME_text, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		if (trim($data[LEVEL_NO])) {
		  	$cmd1 = " select LEVEL_NAME from PER_LEVEL where LEVEL_NO = '".trim($data[LEVEL_NO])."' ";
			$db_dpis1->send_cmd($cmd1);
			if ($data_dpis1 = $db_dpis1->get_array())
				$LEVEL_NAME = $data_dpis1[LEVEL_NAME];
			else
				$LEVEL_NAME = "";
		} else  $LEVEL_NAME = "";
		$worksheet->write($xlsRow, 13, $LEVEL_NAME, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

		if (trim($data[MOV_CODE])) {
			$cmd1 = " select MOV_NAME, MOV_TYPE from PER_MOVMENT where MOV_CODE='".trim($data[MOV_CODE])."'";
			$db_dpis1->send_cmd($cmd);
			if ($data_dpis1 = $db_dpis1->get_array())
				$MOV_NAME_text = trim($data_dpis1[MOV_NAME]);
			else
				$MOV_NAME_text = "";
		} else  $MOV_NAME_text = "";
		$worksheet->write($xlsRow, 14, $MOV_NAME_text, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		
		$worksheet->write($xlsRow, 15, $data[PER_ORGMGT], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		$worksheet->write($xlsRow, 16, $data[PER_SALARY], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		$worksheet->write($xlsRow, 17, $data[PER_MGTSALARY], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		$worksheet->write($xlsRow, 18, $data[PER_SPSALARY], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		$worksheet->write($xlsRow, 19,card_no_format(trim($data[PER_CARDNO]),$CARD_NO_DISPLAY), set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		$worksheet->write($xlsRow, 20, "".$data[PER_OFFNO]."", set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		$worksheet->write($xlsRow, 21, "".$data[PER_TAXNO]."", set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		$worksheet->write($xlsRow, 22, $data[PER_BLOOD], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		
		if (trim($data[RE_CODE])) {
			$cmd1 = "select RE_NAME from PER_RELIGION where RE_ACTIVE=1 and RE_CODE = ".trim($data[RE_CODE])." ";
			$db_dpis1->send_cmd($cmd);
			if ($data_dpis1 = $db_dpis1->get_array())
				$RE_NAME = trim($data_dpis1[RE_NAME]);
			else
				$RE_NAME = "";
		} else  $RE_NAME = "";
		$worksheet->write($xlsRow, 23, $RE_NAME, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
		
		$PER_BIRTHDATE_text = show_date_format(trim($data[PER_BIRTHDATE]),$DATE_DISPLAY);
		$worksheet->write($xlsRow, 24, $PER_BIRTHDATE_text, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		
		$PER_STARTDATE_text = show_date_format(trim($data[PER_STARTDATE]),$DATE_DISPLAY);
		$worksheet->write($xlsRow, 25, $PER_STARTDATE_text, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		
		$PER_OCCUPYDATE_text = show_date_format(trim($data[PER_OCCUPYDATE]),$DATE_DISPLAY);
		$worksheet->write($xlsRow, 26, $PER_OCCUPYDATE_text, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		
		$PER_POSDATE_text = show_date_format(trim($data[PER_POSDATE]),$DATE_DISPLAY);
		$worksheet->write($xlsRow, 27, $PER_POSDATE_text, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		
		if (trim($data[PN_CODE_F])) {
			$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='".trim($data[PN_CODE_F])."' ";
			$db_dpis1->send_cmd($cmd);
			if ($data_dpis1 = $db_dpis1->get_array())
				$PN_F_NAME = trim($data_dpis1[PN_NAME]);
			else
				$PN_F_NAME = "";
		} else  $PN_F_NAME = "";
		$worksheet->write($xlsRow, 28, $PN_F_NAME, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
		
		$worksheet->write($xlsRow, 29, $data[PER_FATHER_NAME], set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
		$worksheet->write($xlsRow, 30, $data[PER_FATHER_SURNAME], set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
	
		if (trim($data[PN_CODE_M])) {
			$cmd1 = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='".trim($data[PN_CODE_M])."' ";
			$db_dpis1->send_cmd($cmd1);
			if ($data_dpis1 = $db_dpis1->get_array())
				$PN_M_NAME = trim($data_dpis1[PN_NAME]);
			else
				$PN_M_NAME = "";
		} else  $PN_M_NAME = "";
		$worksheet->write($xlsRow, 31, $PN_M_NAME, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
		
		$worksheet->write($xlsRow, 32, $data[PER_MOTHER_NAME], set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
		$worksheet->write($xlsRow, 33, $data[PER_MOTHER_SURNAME], set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
		
		if (trim($data[PV_CODE])) {
			$cmd1 = " select PV_NAME from PER_PROVINCE where trim(PV_CODE)='".trim($data[PV_CODE])."' ";
			$db_dpis1->send_cmd($cmd1);
			if ($data_dpis1 = $db_dpis1->get_array())
				$PV_NAME_text = trim($data_dpis1[PV_NAME]);
			else
				$PV_NAME_text = "";
		} else  $PV_NAME_text = "";
		$worksheet->write($xlsRow, 34, $PV_NAME_text, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		
		$chk_ordain = (trim($data[PER_ORDAIN]) == 1)?  "ผ่าน" : "";
		$worksheet->write($xlsRow, 35, $chk_ordain, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		$chk_soldier = (trim($data[PER_SOLDIER]) == 1)? "ผ่าน" : "";
		$worksheet->write($xlsRow, 36, $chk_soldier, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		$chk_member = (trim($data[PER_MEMBER]) == 1)? "สมาชิก" : "";
		$worksheet->write($xlsRow, 37, $chk_member, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		
		if ($data[PER_STATUS]=="0") $PER_STATUS_text = "รอบรรจุ";
		elseif ($data[PER_STATUS]=="1") $PER_STATUS_text = "ปกติ";
		elseif ($data[PER_STATUS]=="2") $PER_STATUS_text = "พ้นจากส่วนราชการ";
		else $PER_STATUS_text = "";

		$worksheet->write($xlsRow, 38, $PER_STATUS_text, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		$PER_HIP_FLAG1 = explode("||",$data[PER_HIP_FLAG]);
		$HIP_FLAG_TXT="";
		for ($i=0; $i<count($PER_HIP_FLAG1); $i++) {
			if ($PER_HIP_FLAG1[$i]=="1")  $HIP_FLAG_TXT.= "HiPPS, ";
			elseif ($PER_HIP_FLAG1[$i]=="2")  $HIP_FLAG_TXT.= "New Wave, ";
			elseif ($PER_HIP_FLAG1[$i]=="3")  $HIP_FLAG_TXT.= "นักเรียนทุน, ";
			elseif ($PER_HIP_FLAG1[$i]=="4")  $HIP_FLAG_TXT.= "นปร., ";
			elseif ($PER_HIP_FLAG1[$i]=="5") $HIP_FLAG_TXT.= "ผู้ปฏิบัติงานด้าน HR, ";
			else $HIP_FLAG_TXT.= "";
		}
		$HIP_FLAG_TXT = substr($HIP_FLAG_TXT,0,strlen($HIP_FLAG_TXT)-2);

		$worksheet->write($xlsRow, 39, $HIP_FLAG_TXT, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		$worksheet->write($xlsRow, 40, $data[PER_CERT_OCC], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		
		if ($data[DEPARTMENT_ID]) {
			$cmd1 = " select ORG_NAME from PER_ORG where ORG_ID=".$data[DEPARTMENT_ID]." ";
			if($SESS_ORG_STRUCTURE==1) $cmd1 = str_replace("PER_ORG", "PER_ORG_ASS", $cmd1);
			$db_dpis1->send_cmd($cmd1);
			if ($data_dpis1 = $db_dpis1->get_array())
				$DEPARTMENT_NAME = $data_dpis1[ORG_NAME];
			else
				$DEPARTMENT_NAME = "";
		} else  $DEPARTMENT_NAME = "";
		$worksheet->write($xlsRow, 41, $DEPARTMENT_NAME, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		if (trim($data[LEVEL_NO_SALARY])) {
		  	$cmd1 = " select LEVEL_NAME from PER_LEVEL where LEVEL_NO = '".trim($data[LEVEL_NO_SALARY])."' ";
			$db_dpis1->send_cmd($cmd1);
			if ($data_dpis1 = $db_dpis1->get_array())
				$LEVEL_NAME_SALARY = $data_dpis1[LEVEL_NAME];
			else
				$LEVEL_NAME_SALARY = "";
		} else  $LEVEL_NAME_SALARY = "";
		$worksheet->write($xlsRow, 42, $LEVEL_NAME_SALARY, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		
		$worksheet->write($xlsRow, 43, $data[PER_NICKNAME], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		$worksheet->write($xlsRow, 44, $data[PER_HOME_TEL], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		$worksheet->write($xlsRow, 45, $data[PER_OFFICE_TEL], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		$worksheet->write($xlsRow, 46, $data[PER_FAX], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		$worksheet->write($xlsRow, 47, $data[PER_MOBILE], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		$worksheet->write($xlsRow, 48, $data[PER_EMAIL], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		$worksheet->write($xlsRow, 49, $data[PER_FILE_NO], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		$worksheet->write($xlsRow, 50, $data[PER_BANK_ACCOUNT], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		$worksheet->write($xlsRow, 51, $data[PER_CONTACT_PERSON], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		$worksheet->write($xlsRow, 52, $data[PER_REMARK], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		if (trim($data[PER_START_ORG])) {
			$cmd1 = " select ORG_NAME from PER_ORG where ORG_ID=".trim($data[PER_START_ORG])." ";
			if($SESS_ORG_STRUCTURE==1) $cmd1 = str_replace("PER_ORG", "PER_ORG_ASS", $cmd1);
			$db_dpis1->send_cmd($cmd1);
			if ($data_dpis1 = $db_dpis1->get_array())
				$START_ORG_NAME = $data_dpis1[ORG_NAME];
			else
				$START_ORG_NAME = "";
		} else  $START_ORG_NAME = "";
		$worksheet->write($xlsRow, 53, $START_ORG_NAME, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		$chk_cooperative = (trim($data[PER_COOPERATIVE]) == 1)? "สมาชิก" : "";
		$worksheet->write($xlsRow, 54, $chk_cooperative, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		$worksheet->write($xlsRow, 55, $data[PER_COOPERATIVE_NO], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));

		$PER_MEMBERDATE_text = show_date_format(trim($data[PER_MEMBERDATE]),$DATE_DISPLAY);
		$worksheet->write($xlsRow, 56, $PER_MEMBERDATE_text, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		
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
		$worksheet->write($xlsRow, 57, $ES_NAME, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		
		if (trim($data[PAY_ID])) {
			$cmd1 = " select POS_NO from PER_POSITION where POS_ID=".trim($data[PAY_ID])." ";
			$db_dpis1->send_cmd($cmd1);
			if ($data_dpis1 = $db_dpis1->get_array())
				$PAY_NO = trim($data_dpis1[POS_NO]);
			else
				$PAY_NO = "";
		} else  $PAY_NO = "";
		$worksheet->write($xlsRow, 58, $PAY_NO, set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		
		$worksheet->write($xlsRow, 59, $data[PL_NAME_WORK], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		$worksheet->write($xlsRow, 60, $data[ORG_NAME_WORK], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		$worksheet->write($xlsRow, 61, $data[PER_DOCNO], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		$worksheet->write($xlsRow, 62, $data[PER_DOCDATE], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		$worksheet->write($xlsRow, 63, $data[PER_EFFECTIVEDATE], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		$worksheet->write($xlsRow, 64, $data[PER_POS_REASON], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		$worksheet->write($xlsRow, 65, $data[PER_POS_YEAR], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		$worksheet->write($xlsRow, 66, $data[PER_POS_DOCTYPE], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		$worksheet->write($xlsRow, 67, $data[PER_POS_DOCNO], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		$worksheet->write($xlsRow, 68, $data[PER_POS_ORG], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));
		$worksheet->write($xlsRow, 69, $data[PER_ORD_DETAIL], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));							
		$worksheet->write($xlsRow, 70, $data[PER_POS_ORGMGT], set_format("xlsFmtTitle", "B", "C", "TLBR", 0));							
		
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
	$worksheet->write($xlsRow, 1, $search_key, set_format("xlsFmtTitle", "", "L", "", 0));

	$workbook->close();

	$arr_file[] = $fname1;
	
	require_once("excel_tailpart_subrtn.php");
?>