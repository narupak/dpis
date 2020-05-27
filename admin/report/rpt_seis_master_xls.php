<?	
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include("../php_scripts/function_share.php");
	
	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$header_width[0] = 10; $header_text[0] = "�ӴѺ���"; 			
	$header_width[1] = 15; $header_text[1] = "���ʢ���Ҫ���";							//	PER_ID;
	$header_width[2] = 15; $header_text[2] = "�������ؤ��"; 			// for $PER_TYPE
	$header_width[3] = 15; $header_text[3] = "����������Ҫ���"; 	// for $OT_CODE
	$header_width[4] = 10; $header_text[4] = "�ӹ�˹�Ҫ���";			// for $PN_CODE
	$header_width[5] = 20; $header_text[5] = "����";							// for $PER_NAME
	$header_width[6] = 20; $header_text[6] = "���ʡ��";					// for $PER_SURNAME
	$header_width[7] = 20; $header_text[7] = "���� �ѧ���";				// for $PER_ENG_NAME
	$header_width[8] = 20; $header_text[8] = "���ʡ�� �ѧ���";		// for $PER_ENG_SURNAME
	$header_width[9] = 20; $header_text[9] = "�ӹѡ/�ͧ (�ͺ���§ҹ)"; 						// for $ORG_ID
	$header_width[10] = 15; $header_text[10] = "���ʵ��˹觢���Ҫ���";							//	POS_ID;
	$header_width[11] = 15; $header_text[11] = "���ʵ��˹��١��ҧ��Ш�";							//	POEM_ID;
	$header_width[12] = 10; $header_text[12] = "�дѺ���˹�";					// for $LEVEL_NO
	$header_width[13] = 10; $header_text[13] = "���˹�㹡�ú����çҹ����";	// for $PER_ORGMGT
	$header_width[14] = 10; $header_text[14] = "�Թ��͹";				// for $PER_SALARY
	$header_width[15] = 10; $header_text[15] = "�Թ��Шӵ��˹�";	// for $PER_MGTSALARY
	$header_width[16] = 10; $header_text[16] = "�Թ�ͺ᷹�����";				// for $PER_SPSALARY
	$header_width[17] = 5; $header_text[17] = "��"; 						// for $PER_GENDER 1 ��� 2 ˭ԧ
	$header_width[18] = 10; $header_text[18] = "ʶҹ�Ҿ����";			// for $MR_CODE
	$header_width[19] = 15; $header_text[19] = "�Ţ��Шӵ�ǻ�ЪҪ�";		// for $PER_CARDNO
	$header_width[20] = 15; $header_text[20] = "�Ţ��Шӵ�Ǣ���Ҫ���";		// for $PER_OFFNO
	$header_width[21] = 15; $header_text[21] = "�Ţ��Шӵ�Ǽ�����������ҡ�"; 				// for $PER_TAXNO
	$header_width[22] = 10; $header_text[22] = "��������ʹ"; 			// for $PER_BLOOD
	$header_width[23] = 10; $header_text[23] = "��ʹ�";				// for $RE_CODE
	$header_width[24] = 15; $header_text[24] = "�ѹ�Դ";			 		// for $PER_BIRTHDATE
	$header_width[25] = 15; $header_text[25] = "�ѹ������³�����Ҫ���";			 		// for $PER_RETIREDATE
	$header_width[26] = 15; $header_text[26] = "�ѹ���������Ѻ�Ҫ���";			// for $PER_STARTDATE
	$header_width[27] = 15; $header_text[27] = "�ѹ��������ǹ�Ҫ���";				// for $PER_OCCUPYDATE
	$header_width[28] = 15; $header_text[28] = "�ѹ���鹨ҡ�Ҫ���";		// for $PER_POSDATE
	$header_width[29] = 15; $header_text[29] = "�ѹ�������͹�Թ��͹";		// for $PER_SALDATE
	$header_width[30] = 10; $header_text[30] = "�ӹ�˹�Ҫ��ͺԴ�";	// for $PN_CODE_F
	$header_width[31] = 20; $header_text[31] = "���ͺԴ�";				// for $PER_FATHER_NAME
	$header_width[32] = 20; $header_text[32] = "���ʡ�źԴ�";		// for $PER_FATHER_SURNAME
	$header_width[33] = 10; $header_text[33] = "�ӹ�˹�Ҫ�����ô�";	// for $PN_CODE_M
	$header_width[34] = 20; $header_text[34] = "������ô�";				// for $PER_MOTHER_NAME
	$header_width[35] = 20; $header_text[35] = "���ʡ����ô�";	// for $PER_MOTHER_SURNAME
	$header_width[36] = 20; $header_text[36] = "������� 1";		// for $PER_ADD1
	$header_width[37] = 20; $header_text[37] = "������� 2";		// for $PER_ADD2
	$header_width[38] = 10; $header_text[38] = "�����������";		// for $PV_CODE
	$header_width[39] = 15; $header_text[39] = "�������������͹���";	// for $MOV_CODE
	$header_width[40] = 10; $header_text[40] = "�ػ����";				// for $PER_ORDAIN
	$header_width[41] = 10; $header_text[41] = "ࡳ�����";			// for $PER_SOLDIER
	$header_width[42] = 15; $header_text[42] = "��Ҫԡ ���./�ʨ.";	// for $PER_MEMBER
	$header_width[43] = 10; $header_text[43] = "ʶҹ�Ҿ";			// for $PER_STATUS
	$header_width[44] = 20; $header_text[44] = "���ʼ����������¹�ŧ������";	//	UPDATE_USER;
	$header_width[45] = 20; $header_text[45] = "�ѹ���� ����¹�ŧ������";				//	UPDATE_DATE;
	$header_width[46] = 10; $header_text[46] = "���";					// for $DEPARTMENT_ID
	$header_width[47] = 10; $header_text[47] = "���͹��ѵԡ����";					// for $APPROVE_PER_ID
	$header_width[48] = 10; $header_text[48] = "���͹��ѵԡ���� (᷹)";					// for $REPLACE_PER_ID
	$header_width[49] = 10; $header_text[49] = "ʶҹм��͹��ѵ�";				// for $ABSENT_FLAG
	$header_width[50] = 15; $header_text[50] = "���ʵ��˹觾�ѡ�ҹ�Ҫ���";							//	POEMS_ID;
	$header_width[51] = 10; $header_text[51] = "HiPPS";					// for $PER_HIP_FLAG
	$header_width[52] = 15; $header_text[52] = "�Ţ���㺻�Сͺ�ԪҪվ"; 	// for $PER_CERT_OCC
	$header_width[53] = 15; $header_text[53] = "��к͡�Թ��͹";	// for $LEVEL_NO_SALARY
	$header_width[54] = 10; $header_text[54] = "�������";					// for $PER_NICKNAME
	$header_width[55] = 15; $header_text[55] = "���Ѿ�����ҹ";			// for $PER_HOME_TEL
	$header_width[56] = 15; $header_text[56] = "���Ѿ����ӧҹ";	// for $PER_OFFICE_TEL
	$header_width[57] = 15; $header_text[57] = "�����";			// for $PER_FAX
	$header_width[58] = 15; $header_text[58] = "���Ѿ����Ͷ��";		// for $PER_MOBILE
	$header_width[59] = 15; $header_text[59] = "������";				// for $PER_EMAIL
	$header_width[60] = 10; $header_text[60] = "�Ţ������";		// for $PER_FILE_NO
	$header_width[61] = 15; $header_text[61] = "�Ţ���ѭ�ո�Ҥ��";	// for $PER_BANK_ACCOUNT
	$header_width[62] = 15; $header_text[62] = "���ѧ�Ѻ�ѭ�ҵ��������";					// for $PER_ID_REF
	$header_width[63] = 15; $header_text[63] = "���ѧ�Ѻ�ѭ�ҵ���ͺ���§ҹ";					// for $PER_ID_ASS_REF
	$header_width[64] = 10; $header_text[64] = "���������ö�Դ�����";		// for $PER_CONTACT_PERSON
	$header_width[65] = 20; $header_text[65] = "�����˵�";			// for $PER_REMARK
	$header_width[66] = 20; $header_text[66] = "�ѧ�Ѵ�������";		// for $PER_START_ORG
	$header_width[67] = 15; $header_text[67] = "��Ҫԡ�ˡó�";	// for $PER_COOPERATIVE
	$header_width[68] = 15; $header_text[68] = "�Ţ����¹��Ҫԡ�ˡó�";	// for $PER_COOPERATIVE_NO
	$header_width[69] = 15; $header_text[69] = "�ѹ�������Ҫԡ ���./�ʨ.";	// for $PER_MEMBERDATE
	$header_width[70] = 10; $header_text[70] = "�ӴѺ���";				// for $PER_SEQ_NO
	$header_width[71] = 15; $header_text[71] = "�Ţ��ͨ���";		// for $PAY_ID
	$header_width[72] = 15; $header_text[72] = "ʶҹС�ô�ç���˹�";	// for $ES_CODE
	$header_width[73] = 20; $header_text[73] = "���˹�";			// for $PL_NAME_WORK
	$header_width[74] = 20; $header_text[74] = "�ѧ�Ѵ";				// for $ORG_NAME_WORK
	$header_width[75] = 15; $header_text[75] = "�Ţ�����������ش";	// for $PER_DOCNO
	$header_width[76] = 15; $header_text[76] = "�ѹ����͡�����";	// for $PER_DOCDATE
	$header_width[77] = 15; $header_text[77] = "�ѹ��������ռ�";	// for $PER_EFFECTIVEDATE
	$header_width[78] = 20; $header_text[78] = "���˵�";		// for $PER_POS_REASON
	$header_width[79] = 10; $header_text[79] = "��";				// for $PER_POS_YEAR
	$header_width[80] = 10; $header_text[80] = "�͡�����ҧ�ԧ";	// for $PER_POS_DOCTYPE
	$header_width[81] = 10; $header_text[81] = "�Ţ����͡���";		// for $PER_POS_DOCNO
	$header_width[82] = 20; $header_text[82] = "��ǹ�Ҫ��÷���͹�";	// for $PER_POS_ORG
	$header_width[83] = 20; $header_text[83] = "��������´����ػ����";	// for $PER_ORDAIN_DETAIL
	$header_width[84] = 20; $header_text[84] = "�����˵ص��˹�";	// for $PER_POS_ORGMGT
	$header_width[85] = 15; $header_text[85] = "�ѹ����͡���";			// for $PER_POS_DOCDATE
	$header_width[86] = 15; $header_text[86] = "����ͧ";	// for $PER_POS_DESC
	$header_width[87] = 15; $header_text[87] = "�����˵�";			// for $PER_POS_REMARK
	$header_width[88] = 15; $header_text[88] = "�Ţ���˹ѧ��͹���";		// for $PER_BOOK_NO
	$header_width[89] = 15; $header_text[89] = "�ѹ���˹ѧ��͹���";				// for $PER_BOOK_DATE
	$header_width[90] = 10; $header_text[90] = "�ӹǹ���駷�����ѭ��";		// for $PER_CONTACT_COUNT
	$header_width[91] = 10; $header_text[91] = "ʶҹ�Ҿ�ҧ���";	// for $PER_DISABILITY
	$header_width[92] = 20; $header_text[92] = "���ʵ��˹��١��ҧ���Ǥ���";					// for $POT_ID
	$header_width[93] = 15; $header_text[93] = "��Ҫԡ���Ҿ����Ҫ��þ����͹���ѭ";					// for $PER_UNION
	$header_width[94] = 15; $header_text[94] = "�ѹ�������Ҫԡ���Ҿ";		// for $PER_UNIONDATE
	$header_width[95] = 20; $header_text[95] = "˹�ҷ���Ѻ�Դ�ͺ";			// for $PER_JOB
	$header_width[96] = 15; $header_text[96] = "��ӡ����ӹѡ/�ͧ 1 �дѺ����ͺ���§ҹ";		// for $ORG_ID_1
	$header_width[97] = 15; $header_text[97] = "��ӡ����ӹѡ/�ͧ 2 �дѺ����ͺ���§ҹ";	// for $ORG_ID_2
	$header_width[98] = 15; $header_text[98] = "��ӡ����ӹѡ/�ͧ 3 �дѺ����ͺ���§ҹ";	// for $ORG_ID_3
	$header_width[99] = 15; $header_text[99] = "��ӡ����ӹѡ/�ͧ 4 �дѺ����ͺ���§ҹ";	// for $ORG_ID_4
	$header_width[100] = 15; $header_text[100] = "��ӡ����ӹѡ/�ͧ 5 �дѺ����ͺ���§ҹ";				// for $ORG_ID_5
	$header_width[101] = 15; $header_text[101] = "��Ҫԡ���Ҿ����Ҫ����дѺ���";		// for $PER_UNION2
	$header_width[102] = 15; $header_text[102] = "�ѹ�������Ҫԡ���Ҿ";	// for $PER_UNIONDATE2
	$header_width[103] = 15; $header_text[103] = "��Ҫԡ���Ҿ����Ҫ����дѺ��з�ǧ";			// for $PER_UNION3
	$header_width[104] = 15; $header_text[104] = "�ѹ�������Ҫԡ���Ҿ";				// for $PER_UNIONDATE3
	$header_width[105] = 15; $header_text[105] = "��Ҫԡ���Ҿ����Ҫ����дѺ�ѧ��Ѵ";	// for $PER_UNION4
	$header_width[106] = 15; $header_text[106] = "�ѹ�������Ҫԡ���Ҿ";	// for $PER_UNIONDATE4
	$header_width[107] = 15; $header_text[107] = "��Ҫԡ���Ҿ����Ҫ��û��������˹觼�������";	// for $PER_UNION5
	$header_width[108] = 15; $header_text[108] = "�ѹ�������Ҫԡ���Ҿ";		// for $PER_UNIONDATE5
	$header_width[109] = 10; $header_text[109] = "��駤���ç���ҧ����ͺ���§ҹ";				// for $PER_SET_ASS

	require_once("excel_headpart_subrtn.php");
	

	$cmd = " select PER_ID, PER_TYPE, OT_CODE, PN_CODE, PER_NAME, PER_SURNAME, 
									PER_ENG_NAME, PER_ENG_SURNAME, ORG_ID, POS_ID, POEM_ID, 
									LEVEL_NO, PER_ORGMGT, PER_SALARY, PER_MGTSALARY, PER_SPSALARY, 
									PER_GENDER, MR_CODE, PER_CARDNO, PER_OFFNO, PER_TAXNO, 
									PER_BLOOD, RE_CODE, PER_BIRTHDATE, PER_RETIREDATE, PER_STARTDATE, 
									PER_OCCUPYDATE, PER_POSDATE, PER_SALDATE, PN_CODE_F, PER_FATHERNAME, 
									PER_FATHERSURNAME, PN_CODE_M, PER_MOTHERNAME, PER_MOTHERSURNAME, 
									PER_ADD1, PER_ADD2, PV_CODE, MOV_CODE, PER_ORDAIN, PER_SOLDIER, 
									PER_MEMBER, PER_STATUS, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID, 
									APPROVE_PER_ID, REPLACE_PER_ID, ABSENT_FLAG, POEMS_ID, PER_HIP_FLAG, 
									PER_CERT_OCC, LEVEL_NO_SALARY, PER_NICKNAME, PER_HOME_TEL, 
									PER_OFFICE_TEL, PER_FAX, PER_MOBILE, PER_EMAIL, PER_FILE_NO, PER_BANK_ACCOUNT, 
									PER_ID_REF, PER_ID_ASS_REF, PER_CONTACT_PERSON, PER_REMARK, PER_START_ORG, 
									PER_COOPERATIVE, PER_COOPERATIVE_NO, PER_MEMBERDATE, PER_SEQ_NO, PAY_ID, 
									ES_CODE, PL_NAME_WORK, ORG_NAME_WORK, PER_DOCNO, PER_DOCDATE, PER_EFFECTIVEDATE, 
									PER_POS_REASON, PER_POS_YEAR, PER_POS_DOCTYPE, PER_POS_DOCNO, 
									PER_POS_ORG, PER_ORDAIN_DETAIL, PER_POS_ORGMGT, PER_POS_DOCDATE, PER_POS_DOCNO, 
									PER_POS_DESC, PER_POS_REMARK, PER_BOOK_NO, PER_BOOK_DATE, PER_CONTACT_COUNT, 
									PER_DISABILITY, POT_ID, PER_UNION, PER_UNIONDATE, PER_JOB, ORG_ID_1, ORG_ID_2, ORG_ID_3, 
									ORG_ID_4, ORG_ID_5, PER_UNION2, PER_UNIONDATE2, PER_UNION3, PER_UNIONDATE3, PER_UNION4, 
									PER_UNIONDATE4, PER_UNION5, PER_UNIONDATE5, PER_SET_ASS
						from		PER_PERSONAL 
						where PER_ID >= 49500 and PER_ID < 60000
						order by PER_ID ";
//						where PER_ID < 5000
//						where PER_ID >= 5000 and PER_ID < 12000
//						where PER_ID >= 12000 and PER_ID < 20000
//						where PER_ID >= 20000 and PER_ID < 25000
//						where PER_ID >= 25000 and PER_ID < 35000
//						where PER_ID >= 35000 and PER_ID < 36500
//						where PER_ID >= 36500 and PER_ID < 38000
//						where PER_ID >= 38000 and PER_ID < 40000
//						where PER_ID >= 40000 and PER_ID < 41500
//						where PER_ID >= 41500 and PER_ID < 43500
//						where PER_ID >= 43500 and PER_ID < 45000
//						where PER_ID >= 45000 and PER_ID < 46500
//						where PER_ID >= 46500 and PER_ID < 48000
//						where PER_ID >= 48000 and PER_ID < 49500
//	echo "[$cmd]<br><br>";
	$count_data = $db_dpis->send_cmd($cmd);

	$cnt = 0;
	$file_limit = 2500;
	$data_limit = 250;
	$xlsRow = 0;
	$count_org_ref = 0;
	$sheet_no = 0; $sheet_no_text="";

	$report_code = "xls_PERSONAL";

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
	$head = "��ö����͹����Ҫ���";
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

			$worksheet = &$workbook->addworksheet("$report_code".$fnum_text.$sheet_no_text);
			$worksheet->set_margin_right(0.50);
			$worksheet->set_margin_bottom(1.10);

//			echo "$data_count>>sheet name=$report_code$fnum_text$sheet_no_text<br>";
			
			call_header($data_count, $head);
		}
		
		$seq_no++;
		$xlsRow++;
		
		$worksheet->write($xlsRow, 0, "$seq_no ��͹", set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 1, $data[PER_ID], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 2, $data[PER_TYPE], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 3, $data[OT_CODE], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 4, $data[PN_CODE], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 5, $data[PER_NAME], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 6, $data[PER_SURNAME], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 7, $data[PER_ENG_NAME], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 8, $data[PER_ENG_SURNAME], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 9, $data[ORG_ID], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 10, $data[POS_ID], set_format("xlsFmtTitle", "", "C", "LR", 0));	
		$worksheet->write($xlsRow, 11, $data[POEM_ID], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 12, $data[LEVEL_NO], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 13, $data[PER_ORGMGT], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 14, $data[PER_SALARY], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 15, $data[PER_MGTSALARY], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 16, $data[PER_SPSALARY], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 17, $data[PER_GENDER], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 18, $data[MR_CODE], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 19, $data[PER_CARDNO], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 20, $data[PER_OFFNO], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 21, $data[PER_TAXNO], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 22, $data[PER_BLOOD], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 23, $data[RE_CODE], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 24, $data[PER_BIRTHDATE], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 25, $data[PER_RETIREDATE], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 26, $data[PER_STARTDATE], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 27, $data[PER_OCCUPYDATE], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 28, $data[PER_POSDATE], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 29, $data[PER_SALDATE], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 30, $data[PN_CODE_F], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 31, $data[PER_FATHER_NAME], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 32, $data[PER_FATHER_SURNAME], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 33, $data[PN_CODE_M], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 34, $data[PER_MOTHER_NAME], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 35, $data[PER_MOTHER_SURNAME], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 36, $data[PER_ADD1], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 37, $data[PER_ADD2], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 38, $data[PV_CODE], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 39, $data[MOV_CODE], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 40, $data[PER_ORDAIN], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 41, $data[PER_SOLDIER], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 42, $data[PER_MEMBER], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 43, $data[PER_STATUS], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 44, $data[UPDATE_USER], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 45, $data[UPDATE_DATE], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 46, $data[DEPARTMENT_ID], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 47, $data[APPROVE_PER_ID], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 48, $data[REPLACE_PER_ID], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 49, $data[ABSENT_FLAG], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 50, $data[POEMS_ID], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 51, $data[PER_HIP_FLAG], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 52, $data[PER_CERT_OCC], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 53, $data[LEVEL_NAME_SALARY], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 54, $data[PER_NICKNAME], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 55, $data[PER_HOME_TEL], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 56, $data[PER_OFFICE_TEL], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 57, $data[PER_FAX], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 58, $data[PER_MOBILE], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 59, $data[PER_EMAIL], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 60, $data[PER_FILE_NO], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 61, $data[PER_BANK_ACCOUNT], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 62, $data[PER_ID_REF], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 63, $data[PER_ID_ASS_REF], set_format("xlsFmtTitle", "", "L", "LR", 0));
		$worksheet->write($xlsRow, 64, $data[PER_CONTACT_PERSON], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 65, $data[PER_REMARK], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 66, $data[PER_START_ORG], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 67, $data[PER_COOPERATIVE], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 68, $data[PER_COOPERATIVE_NO], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 69, $data[PER_MEMBERDATE], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 70, $data[PER_SEQ_NO], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 71, $data[PAY_ID], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 72, $data[ES_CODE], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 73, $data[PL_NAME_WORK], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 74, $data[ORG_NAME_WORK], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 75, $data[PER_DOCNO], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 76, $data[PER_DOCDATE], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 77, $data[PER_EFFECTIVEDATE], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 78, $data[PER_POS_REASON], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 79, $data[PER_POS_YEAR], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 80, $data[PER_POS_DOCTYPE], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 81, $data[PER_POS_DOCNO], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 82, $data[PER_POS_ORG], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 83, $data[PER_ORD_DETAIL], set_format("xlsFmtTitle", "", "C", "LR", 0));							
		$worksheet->write($xlsRow, 84, $data[PER_POS_ORGMGT], set_format("xlsFmtTitle", "", "C", "LR", 0));							
		$worksheet->write($xlsRow, 85, $data[PER_POS_DOCDATE], set_format("xlsFmtTitle", "", "C", "LR", 0));							
		$worksheet->write($xlsRow, 86, $data[PER_POS_DESC], set_format("xlsFmtTitle", "", "C", "LR", 0));							
		$worksheet->write($xlsRow, 87, $data[PER_POS_REMARK], set_format("xlsFmtTitle", "", "C", "LR", 0));							
		$worksheet->write($xlsRow, 88, $data[PER_BOOK_NO], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 89, $data[PER_BOOK_DATE], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 90, $data[PER_CONTACT_COUNT], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 91, $data[PER_DISABILITY], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 92, $data[POT_ID], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 93, $data[PER_UNION], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 94, $data[PER_UNIONDATE], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 95, $data[PER_JOB], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 96, $data[ORG_ID_1], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 97, $data[ORG_ID_2], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 98, $data[ORG_ID_3], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 99, $data[ORG_ID_4], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 100, $data[ORG_ID_5], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 101, $data[PER_UNION2], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 102, $data[PER_UNIONDATE2], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 103, $data[PER_UNION3], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 104, $data[PER_UNIONDATE3], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 105, $data[PER_UNION4], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 106, $data[PER_UNIONDATE4], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 107, $data[PER_UNION5], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 108, $data[PER_UNIONDATE5], set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 109, $data[PER_SET_ASS], set_format("xlsFmtTitle", "", "C", "LR", 0));
		
		$xlsRow++;
		
		$worksheet->write($xlsRow, 0, "$seq_no ��ѧ", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 1, $data[PER_ID], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 2, $data[PER_TYPE], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 3, $data[OT_CODE], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 4, $data[PN_CODE], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 5, $data[PER_NAME], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 6, $data[PER_SURNAME], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 7, $data[PER_ENG_NAME], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 8, $data[PER_ENG_SURNAME], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 9, $data[ORG_ID], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 10, $data[POS_ID], set_format("xlsFmtTitle", "B", "C", "LR", 0));	
		$worksheet->write($xlsRow, 11, $data[POEM_ID], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 12, $data[LEVEL_NO], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 13, $data[PER_ORGMGT], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 14, $data[PER_SALARY], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 15, $data[PER_MGTSALARY], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 16, $data[PER_SPSALARY], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 17, $data[PER_GENDER], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 18, $data[MR_CODE], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 19, $data[PER_CARDNO], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 20, $data[PER_OFFNO], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 21, $data[PER_TAXNO], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 22, $data[PER_BLOOD], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 23, $data[RE_CODE], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 24, $data[PER_BIRTHDATE], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 25, $data[PER_RETIREDATE], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 26, $data[PER_STARTDATE], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 27, $data[PER_OCCUPYDATE], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 28, $data[PER_POSDATE], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 29, $data[PER_SALDATE], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 30, $data[PN_CODE_F], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 31, $data[PER_FATHER_NAME], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 32, $data[PER_FATHER_SURNAME], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 33, $data[PN_CODE_M], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 34, $data[PER_MOTHER_NAME], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 35, $data[PER_MOTHER_SURNAME], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 36, $data[PER_ADD1], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 37, $data[PER_ADD2], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 38, $data[PV_CODE], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 39, $data[MOV_CODE], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 40, $data[PER_ORDAIN], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 41, $data[PER_SOLDIER], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 42, $data[PER_MEMBER], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 43, $data[PER_STATUS], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 44, $data[UPDATE_USER], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 45, $data[UPDATE_DATE], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 46, $data[DEPARTMENT_ID], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 47, $data[APPROVE_PER_ID], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 48, $data[REPLACE_PER_ID], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 49, $data[ABSENT_FLAG], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 50, $data[POEMS_ID], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 51, $data[PER_HIP_FLAG], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 52, $data[PER_CERT_OCC], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 53, $data[LEVEL_NAME_SALARY], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 54, $data[PER_NICKNAME], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 55, $data[PER_HOME_TEL], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 56, $data[PER_OFFICE_TEL], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 57, $data[PER_FAX], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 58, $data[PER_MOBILE], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 59, $data[PER_EMAIL], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 60, $data[PER_FILE_NO], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 61, $data[PER_BANK_ACCOUNT], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 62, $data[PER_ID_REF], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 63, $data[PER_ID_ASS_REF], set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 64, $data[PER_CONTACT_PERSON], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 65, $data[PER_REMARK], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 66, $data[PER_START_ORG], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 67, $data[PER_COOPERATIVE], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 68, $data[PER_COOPERATIVE_NO], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 69, $data[PER_MEMBERDATE], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 70, $data[PER_SEQ_NO], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 71, $data[PAY_ID], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 72, $data[ES_CODE], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 73, $data[PL_NAME_WORK], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 74, $data[ORG_NAME_WORK], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 75, $data[PER_DOCNO], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 76, $data[PER_DOCDATE], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 77, $data[PER_EFFECTIVEDATE], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 78, $data[PER_POS_REASON], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 79, $data[PER_POS_YEAR], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 80, $data[PER_POS_DOCTYPE], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 81, $data[PER_POS_DOCNO], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 82, $data[PER_POS_ORG], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 83, $data[PER_ORD_DETAIL], set_format("xlsFmtTitle", "B", "C", "LR", 0));							
		$worksheet->write($xlsRow, 84, $data[PER_POS_ORGMGT], set_format("xlsFmtTitle", "B", "C", "LR", 0));							
		$worksheet->write($xlsRow, 85, $data[PER_POS_DOCDATE], set_format("xlsFmtTitle", "B", "C", "LR", 0));							
		$worksheet->write($xlsRow, 86, $data[PER_POS_DESC], set_format("xlsFmtTitle", "B", "C", "LR", 0));							
		$worksheet->write($xlsRow, 87, $data[PER_POS_REMARK], set_format("xlsFmtTitle", "B", "C", "LR", 0));							
		$worksheet->write($xlsRow, 88, $data[PER_BOOK_NO], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 89, $data[PER_BOOK_DATE], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 90, $data[PER_CONTACT_COUNT], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 91, $data[PER_DISABILITY], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 92, $data[POT_ID], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 93, $data[PER_UNION], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 94, $data[PER_UNIONDATE], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 95, $data[PER_JOB], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 96, $data[ORG_ID_1], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 97, $data[ORG_ID_2], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 98, $data[ORG_ID_3], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 99, $data[ORG_ID_4], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 100, $data[ORG_ID_5], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 101, $data[PER_UNION2], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 102, $data[PER_UNIONDATE2], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 103, $data[PER_UNION3], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 104, $data[PER_UNIONDATE3], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 105, $data[PER_UNION4], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 106, $data[PER_UNIONDATE4], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 107, $data[PER_UNION5], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 108, $data[PER_UNIONDATE5], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 109, $data[PER_SET_ASS], set_format("xlsFmtTitle", "B", "C", "LR", 0));
		
		$xlsRow++;
		
		$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTitle", "", "C", "LR", 0));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));	
		$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 14, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 15, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 16, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 17, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 18, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 19, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 20, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 21, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 22, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 23, "", set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 24, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 25, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 26, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 27, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 28, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 29, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 30, "", set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 31, "", set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 32, "", set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 33, "", set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 34, "", set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 35, "", set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 36, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 37, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 38, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 39, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 40, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 41, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 42, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 43, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 44, "", set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 45, "", set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 46, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 47, "", set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 48, "", set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 49, "", set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 50, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 51, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 52, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 53, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 54, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 55, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 56, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 57, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 58, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 59, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 60, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 61, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 62, "", set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 63, "", set_format("xlsFmtTitle", "B", "L", "LR", 0));
		$worksheet->write($xlsRow, 64, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 65, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 66, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 67, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 68, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 69, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 70, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 71, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 72, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 73, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 74, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 75, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 76, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 77, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 78, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 79, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 80, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 81, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 82, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 83, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));							
		$worksheet->write($xlsRow, 84, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));							
		$worksheet->write($xlsRow, 85, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));							
		$worksheet->write($xlsRow, 86, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));							
		$worksheet->write($xlsRow, 87, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));							
		$worksheet->write($xlsRow, 88, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 89, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 90, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 91, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 92, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 93, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 94, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 95, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 96, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 97, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 98, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 99, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 100, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 101, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 102, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 103, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 104, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 105, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 106, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 107, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 108, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 109, "", set_format("xlsFmtTitle", "B", "C", "LR", 0));
		
		$data_count++;
	} // end while loop
	if ($xlsRow==2) {
		$xlsRow++;
		$worksheet->write($xlsRow, 0, "***** ��辺������ *****", set_format("xlsFmtTitle", "B", "C", "", 0));
	}

	$xlsRow++;
	$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTitle", "B", "C", "T", 0));
	$xlsRow++;
	$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTitle", "B", "C", "", 0));
	$xlsRow++;
	$worksheet->write($xlsRow, 0, "�ӹǹ������ $count_data ��¡��", set_format("xlsFmtTitle", "B", "L", "", 0));

	$workbook->close();

	$arr_file[] = $fname1;
	
	require_once("excel_tailpart_subrtn.php");
?>