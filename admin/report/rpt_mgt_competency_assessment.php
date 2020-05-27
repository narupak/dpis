<?
//	session_start();	
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	if(trim($_GET[SLIP_ID]))		$SLIP_ID=trim($_GET[SLIP_ID]);
	
	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php");

	ini_set("max_execution_time", $max_execution_time);

	$fname= "rpt_personal_salaryslip.pdf";
//	$font = "AngsanaUPC";

	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "";
	$report_title = "";
	$report_code = "SLIP";
	$orientation='P';

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();

	$pdf = new PDF($orientation,$unit,$paper_size,$lang_code,$company_name,$report_title,$report_code,$heading,$heading_width,$heading_align);
	
 	$pdf->Open();
	$pdf->SetMargins(5,5,5);
	$pdf->AliasNbPages();
	$pdf->AddPage();
	$pdf->SetTextColor(0, 0, 0);
	$pdf->SetFont($font,'',14);
	
	$page_start_x = $pdf->x;			$page_start_y = $pdf->y;

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
//session_cache_limiter("nocache");
//	session_cache_limiter("private");

	$cmd = "CA_COURSE, ORG_CODE, CA_SEQ, CA_CODE, CA_TYPE, PER_ID, CA_TEST_DATE,
							CA_APPROVE_DATE, PN_CODE, CA_NAME, CA_SURNAME, CA_CARDNO, CA_CONSISTENCY, CA_SCORE_1, CA_SCORE_2,
							CA_SCORE_3, CA_SCORE_4, CA_SCORE_5, CA_SCORE_6, CA_SCORE_7, CA_SCORE_8, CA_SCORE_9, CA_SCORE_10,
							CA_SCORE_11, CA_SCORE_12, CA_MEAN, CA_MINISTRY_NAME, CA_DEPARTMENT_NAME, CA_ORG_NAME, CA_ORG_NAME1,
							CA_ORG_NAME2, CA_LINE, LEVEL_NO, CA_MGT, CA_NEW_SCORE_1, CA_NEW_SCORE_2, CA_NEW_SCORE_3, CA_NEW_SCORE_4,
							CA_NEW_SCORE_5, CA_NEW_SCORE_6, CA_NEW_SCORE_7, CA_NEW_SCORE_8, CA_NEW_SCORE_9, CA_NEW_SCORE_10,
							CA_NEW_SCORE_11, CA_NEW_MEAN, CA_REMARK, UPDATE_USER, UPDATE_DATE 
							from PER_MGT_COMPETENCY_ASSESSMENT 
							where CA_ID=$CA_ID ";
//	echo "$cmd<br>";
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();

	if($count_data){
		$data = $db_dpis->get_array();
		$CA_COURSE = $data[CA_COURSE];
		$ORG_CODE = trim($data[ORG_CODE]);
		$CA_SEQ = $data[CA_SEQ];
		$CA_CODE = trim($data[CA_CODE]);
		$CA_TYPE = $data[CA_TYPE];
		$PER_ID = $data[PER_ID];
		$CA_TEST_DATE = show_date_format($data[CA_TEST_DATE], 3);
		$CA_APPROVE_DATE = show_date_format($data[CA_APPROVE_DATE], 3);
		$PN_CODE = trim($data[PN_CODE]);
		$CA_NAME = trim($data[CA_NAME]);
		$CA_SURNAME = trim($data[CA_SURNAME]);
		$CA_CARDNO = trim($data[CA_CARDNO]);
		$CA_CONSISTENCY = $data[CA_CONSISTENCY];
		$CA_SCORE_1 = $data[CA_SCORE_1];
		$CA_SCORE_2 = $data[CA_SCORE_2];
		$CA_SCORE_3 = $data[CA_SCORE_3];
		$CA_SCORE_4 = $data[CA_SCORE_4];
		$CA_SCORE_5 = $data[CA_SCORE_5];
		$CA_SCORE_6 = $data[CA_SCORE_6];
		$CA_SCORE_7 = $data[CA_SCORE_7];
		$CA_SCORE_8 = $data[CA_SCORE_8];
		$CA_SCORE_9 = $data[CA_SCORE_9];
		$CA_SCORE_10 = $data[CA_SCORE_10];
		$CA_SCORE_11 = $data[CA_SCORE_11];
		$CA_SCORE_12 = $data[CA_SCORE_12];
		$CA_MEAN = $data[CA_MEAN];
		$CA_MINISTRY_NAME = trim($data[CA_MINISTRY_NAME]);
		$CA_DEPARTMENT_NAME = trim($data[CA_DEPARTMENT_NAME]);
		$CA_ORG_NAME = trim($data[CA_ORG_NAME]);
		$CA_ORG_NAME1 = trim($data[CA_ORG_NAME1]);
		$CA_ORG_NAME2 = trim($data[CA_ORG_NAME2]);
		$CA_LINE = trim($data[CA_LINE]);
		$LEVEL_NO = trim($data[LEVEL_NO]);
		$CA_MGT = trim($data[CA_MGT]);
		$CA_NEW_SCORE_1 = $data[CA_NEW_SCORE_1];
		$CA_NEW_SCORE_2 = $data[CA_NEW_SCORE_2];
		$CA_NEW_SCORE_3 = $data[CA_NEW_SCORE_3];
		$CA_NEW_SCORE_4 = $data[CA_NEW_SCORE_4];
		$CA_NEW_SCORE_5 = $data[CA_NEW_SCORE_5];
		$CA_NEW_SCORE_6 = $data[CA_NEW_SCORE_6];
		$CA_NEW_SCORE_7 = $data[CA_NEW_SCORE_7];
		$CA_NEW_SCORE_8 = $data[CA_NEW_SCORE_8];
		$CA_NEW_SCORE_9 = $data[CA_NEW_SCORE_9];
		$CA_NEW_SCORE_10 = $data[CA_NEW_SCORE_10];
		$CA_NEW_SCORE_11 = $data[CA_NEW_SCORE_11];
		$CA_NEW_MEAN = $data[CA_NEW_MEAN];
		$CA_REMARK = trim($data[CA_REMARK]);

		if ($xxxxxx)
			$HEAD_01_1 = "����Ѻ��û����Թ�ѹ��� $CA_TEST_DATE";
		else
			$HEAD_01_1 = "�ѹ����鹺ѭ�� $CA_APPROVE_DATE";
		$HEAD_01_3 = "���� $CA_CODE";
		$HEAD_02 = "���� ";
		$HEAD_03 = "˹��§ҹ ";
		$HEAD_04_1 = "�͹�Թ��� ";
		$HEAD_04_2 = " �Ţ���ѭ�� ";

		$pdf->SetFont($font,'b',20);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

		$pdf->Cell(200,7,(($NUMBER_DISPLAY==2)?convert2thaidigit("$HEAD_01"):"$HEAD_01"),0,1,"L");

		$line_h = 7;
		$arr_text = array($HEAD_01_1,$HEAD_01_3);
		$arr_align = array("L","L","C","L");
		$arr_border = array("","","","");
		$arr_font = array("$font|b|20|","$font||20","$font|b|20","$font||20");
		$a = $pdf->add_lineFreeTab($line_h, $arr_text, $arr_align, $arr_border, $arr_font);
		
		$arr_text = array($HEAD_02,$PN_NAME . $PER_NAME . "  " . $PER_SURNAME);
		$arr_align = array("L","L","C","L");
		$arr_border = array("","","","");
		$arr_font = array("$font|b|20|","$font||20","$font|b|20","$font||20");
		$a = $pdf->add_lineFreeTab($line_h, $arr_text, $arr_align, $arr_border, $arr_font);
		
		$arr_text = array($HEAD_03,$DEPARTMENT_NAME . "  " . $ORG_NAME);
		$arr_align = array("L","L");
		$arr_border = array("","");
		$arr_font = array("$font|b|20|","$font||20");
		$a = $pdf->add_lineFreeTab($line_h, $arr_text, $arr_align, $arr_border, $arr_font);
		
		$arr_text = array($HEAD_04_1,$BANK_NAME . "  " . $BRANCH_NAME,$HEAD_04_2,$PER_BANK_ACCOUNT);
		$arr_align = array("L","C","C","L");
		$arr_border = array("","","","");
		$arr_font = array("$font|b|20|","$font||20","$font|b|20|","$font||20");
//		echo "text:".implode(",", $arr_text).", font:".implode(",", $arr_font)."<br>";
		$a = $pdf->add_lineFreeTab($line_h, $arr_text, $arr_align, $arr_border, $arr_font);

		$pdf->SetFont($font,'bU',20);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(200,7,"��¡���Ѻ",0,1,"C");

		$heading_text_s= array("","","");
		$head_text1 = implode(",",$heading_text_s);	// ����������
		$heading_width_s= array(135,50,15);
		$head_width1 = implode(",",$heading_width_s);
		$heading_align_s = array("C","C","C");
		$head_align1 = implode(",",$heading_align_s);
		$column_function_s = array(($NUMBER_DISPLAY==2)?"TNUM":"ENUM",($NUMBER_DISPLAY==2)?"TNUM0-2":"ENUM0-2","");
		$col_function = implode(",", $column_function_s);
		$COLUMN_FORMAT = do_COLUMN_FORMAT($heading_text_s, $heading_width_s, $heading_align_s);
//		echo "Personal Information--text:$head_text1, width:$head_width1, align:$head_align1, col:$col_function, format:$COLUMN_FORMAT<br>";
		$result = $pdf->open_tab($head_text1, $head_width1, 7, "", $head_align1, "", "20", "b", "0066CC", "EEEEFF", $COLUMN_FORMAT, $col_function, true);
		if (!$result) echo "****** error ****** on open table for Personal Information<br>";

		$data_align = array("L","R","L");
		
		$arr_data = (array) null;
		$arr_data[0] = "�Թ��͹/��Ҩ�ҧ��Ш�";
		$arr_data[1] = (!$INCOME_01 ? 0 : $INCOME_01);
		$arr_data[2] = " �ҷ.";

		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "20", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";

		$arr_data[0] = "�Թ��͹/��Ҩ�ҧ��Ш� (���ԡ)";
		$arr_data[1] = (!$INCOME_02 ? 0 : $INCOME_02);
		$arr_data[2] = " �ҷ.";

		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "20", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";

		$arr_data[0] = "�Թ��Шӵ��˹�/�ԪҪվ";
		$arr_data[1] = (!$INCOME_03 ? 0 : $INCOME_03);
		$arr_data[2] = " �ҷ.";

		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "20", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";

		$arr_data[0] = "�Թ��Шӵ��˹�/�ԪҪվ (���ԡ)";
		$arr_data[1] = (!$INCOME_04 ? 0 : $INCOME_04);
		$arr_data[2] = " �ҷ.";

		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "20", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";

		$arr_data[0] = "�Թ��Шӵ��˹�/�ԪҪվ (���ԡ)";
		$arr_data[1] = (!$INCOME_04 ? 0 : $INCOME_04);
		$arr_data[2] = " �ҷ.";

		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "20", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";

		if ($INCOME_05 > 0) {
			$arr_data[0] = "";
			$arr_data[1] = (!$INCOME_05 ? 0 : $INCOME_05);
			$arr_data[2] = " �ҷ.";
	
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "20", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}

		if ($INCOME_06 > 0) {
			$arr_data[0] = "";
			$arr_data[1] = (!$INCOME_06 ? 0 : $INCOME_06);
			$arr_data[2] = " �ҷ.";
	
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "20", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}

		if ($INCOME_07 > 0) {
			$arr_data[0] = "";
			$arr_data[1] = (!$INCOME_07 ? 0 : $INCOME_07);
			$arr_data[2] = " �ҷ.";
	
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "20", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}

		if ($INCOME_08 > 0) {
			$arr_data[0] = "";
			$arr_data[1] = (!$INCOME_08 ? 0 : $INCOME_08);
			$arr_data[2] = " �ҷ.";
	
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "20", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}

		if ($INCOME_09 > 0) {
			$arr_data[0] = "";
			$arr_data[1] = (!$INCOME_09 ? 0 : $INCOME_09);
			$arr_data[2] = " �ҷ.";
	
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "20", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}

		if ($INCOME_10 > 0) {
			$arr_data[0] = "";
			$arr_data[1] = (!$INCOME_10 ? 0 : $INCOME_10);
			$arr_data[2] = " �ҷ.";
	
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "20", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}

		$arr_data[0] = "�Թ��ҵͺ᷹�����͹����Ҫ�����ҡѺ�Թ��Шӵ��˹�";
		$arr_data[1] = (!$INCOME_11 ? 0 : $INCOME_11);
		$arr_data[2] = " �ҷ.";

		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "20", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";

		$arr_data[0] = "�Թ��ҵͺ᷹�����͹����Ҫ�����ҡѺ�Թ��Шӵ��˹� (���ԡ)";
		$arr_data[1] = (!$INCOME_12 ? 0 : $INCOME_12);
		$arr_data[2] = " �ҷ.";

		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "20", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";

		$arr_data[0] = "�Թ��ҵͺ᷹�����͹����Ҫ����дѺ 8 ��� 8 �.";
		$arr_data[1] = (!$INCOME_13 ? 0 : $INCOME_13);
		$arr_data[2] = " �ҷ.";

		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "20", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";

		$arr_data[0] = "�Թ��ҵͺ᷹�����͹����Ҫ����дѺ 8 ��� 8 �. (���ԡ)";
		$arr_data[1] = (!$INCOME_14 ? 0 : $INCOME_14);
		$arr_data[2] = " �ҷ.";

		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "20", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";

		$arr_data[0] = "�Թ��ҵͺ᷹�����͹����Ѻ����Ҫ����дѺ 1-7/�١��ҧ/���ԡ";
		$arr_data[1] = (!$INCOME_15 ? 0 : $INCOME_15);
		$arr_data[2] = " �ҷ.";

		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "20", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";

		$arr_data[0] = "�Թ�ͺ᷹�����(2%,4%)����Ҫ���/�١��ҧ��Ш�/���ԡ";
		$arr_data[1] = (!$INCOME_16 ? 0 : $INCOME_16);
		$arr_data[2] = " �ҷ.";

		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "20", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";

		if ($INCOME_17 > 0) {
			$arr_data[0] = "";
			$arr_data[1] = (!$INCOME_17 ? 0 : $INCOME_17);
			$arr_data[2] = " �ҷ.";
	
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "20", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}

		if ($INCOME_18 > 0) {
			$arr_data[0] = "";
			$arr_data[1] = (!$INCOME_18 ? 0 : $INCOME_18);
			$arr_data[2] = " �ҷ.";
	
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "20", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}

		if ($INCOME_19 > 0) {
			$arr_data[0] = "";
			$arr_data[1] = (!$INCOME_19 ? 0 : $INCOME_19);
			$arr_data[2] = " �ҷ.";
	
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "20", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}

		if ($INCOME_20 > 0) {
			$arr_data[0] = "";
			$arr_data[1] = (!$INCOME_20 ? 0 : $INCOME_20);
			$arr_data[2] = " �ҷ.";
	
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "20", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}

		if ($EXTRA_INCOME_01 > 0) {
			$arr_data[0] = "$INCOME_NAME_01";
			$arr_data[1] = (!$EXTRA_INCOME_01 ? 0 : $EXTRA_INCOME_01);
			$arr_data[2] = " �ҷ.";
	
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "20", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}

		if ($EXTRA_INCOME_02 > 0) {
			$arr_data[0] = "$INCOME_NAME_02";
			$arr_data[1] = (!$EXTRA_INCOME_02 ? 0 : $EXTRA_INCOME_02);
			$arr_data[2] = " �ҷ.";
	
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "20", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}

		if ($EXTRA_INCOME_03 > 0) {
			$arr_data[0] = "$INCOME_NAME_03";
			$arr_data[1] = (!$EXTRA_INCOME_03 ? 0 : $EXTRA_INCOME_03);
			$arr_data[2] = " �ҷ.";
	
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "20", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}

		if ($EXTRA_INCOME_04 > 0) {
			$arr_data[0] = "$INCOME_NAME_04";
			$arr_data[1] = (!$EXTRA_INCOME_04 ? 0 : $EXTRA_INCOME_04);
			$arr_data[2] = " �ҷ.";
	
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "20", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}

		$arr_data[0] = "����";
		$arr_data[1] = (!$OTHER_INCOME ? 0 : $OTHER_INCOME);
		$arr_data[2] = " �ҷ.";
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "20", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";

		$arr_data[0] = "�����¡���Ѻ";
		$arr_data[1] = (!$TOTAL_INCOME ? 0 : $TOTAL_INCOME);
		$arr_data[2] = " �ҷ.";
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "20", "bU", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";

		$pdf->close_tab(""); 

		$pdf->SetFont($font,'bU',20);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(200,7,"��¡���ѡ",0,1,"C");

		$heading_text_s= array("","","");
		$head_text1 = implode(",",$heading_text_s);	// ����������
		$heading_width_s= array(135,50,15);
		$head_width1 = implode(",",$heading_width_s);
		$heading_align_s = array("C","C","C");
		$head_align1 = implode(",",$heading_align_s);
		$column_function_s = array(($NUMBER_DISPLAY==2)?"TNUM":"ENUM",($NUMBER_DISPLAY==2)?"TNUM0-2":"ENUM0-2","");
		$col_function = implode(",", $column_function_s);
		$COLUMN_FORMAT = do_COLUMN_FORMAT($heading_text_s, $heading_width_s, $heading_align_s);
//		echo "Personal Information--text:$head_text1, width:$head_width1, align:$head_align1, col:$col_function, format:$COLUMN_FORMAT<br>";
		$result = $pdf->open_tab($head_text1, $head_width1, 7, "", $head_align1, "", "20", "b", "0066CC", "EEEEFF", $COLUMN_FORMAT, $col_function, true);
		if (!$result) echo "****** error ****** on open table for Personal Information<br>";

		$data_align = array("L","R","L");
		
		$arr_data = (array) null;

		$arr_data[0] = "����/���ԡ";
		$arr_data[1] = (!$DEDUCT_01 ? 0 : $DEDUCT_01);
		$arr_data[2] = " �ҷ.";
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "20", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";

		if ($DEDUCT_02 > 0) {
			$arr_data[0] = "";
			$arr_data[1] = (!$DEDUCT_02 ? 0 : $DEDUCT_02);
			$arr_data[2] = " �ҷ.";
		
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "20", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}
		
		$arr_data[0] = "������/�Թ����ˡó�";
		$arr_data[1] = (!$DEDUCT_03 ? 0 : $DEDUCT_03);
		$arr_data[2] = " �ҷ.";
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "20", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		
		if ($DEDUCT_04 > 0) {
			$arr_data[0] = "";
			$arr_data[1] = (!$DEDUCT_04 ? 0 : $DEDUCT_04);
			$arr_data[2] = " �ҷ.";

			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "20", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}
		
		$arr_data[0] = "���./���ԡ,�ʨ./���ԡ";
		$arr_data[1] = (!$DEDUCT_05 ? 0 : $DEDUCT_05);
		$arr_data[2] = " �ҷ.";
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "20", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		
		$arr_data[0] = "�Թ��� ���.";
		$arr_data[1] = (!$DEDUCT_06 ? 0 : $DEDUCT_06);
		$arr_data[2] = " �ҷ.";

		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "20", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		
		$arr_data[0] = "�Թ��� (���.)";
		$arr_data[1] = (!$DEDUCT_07 ? 0 : $DEDUCT_07);
		$arr_data[2] = " �ҷ.";

		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "20", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		
		if ($DEDUCT_08 > 0) {
			$arr_data[0] = "";
			$arr_data[1] = (!$DEDUCT_08 ? 0 : $DEDUCT_08);
			$arr_data[2] = " �ҷ.";
		
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "20", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}
		
		if ($DEDUCT_09 > 0) {
			$arr_data[0] = "";
			$arr_data[1] = (!$DEDUCT_09 ? 0 : $DEDUCT_09);
			$arr_data[2] = " �ҷ.";
		
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "20", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}
		
		if ($DEDUCT_10 > 0) {
			$arr_data[0] = "";
			$arr_data[1] = (!$DEDUCT_10 ? 0 : $DEDUCT_10);
			$arr_data[2] = " �ҷ.";
		
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "20", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}
		
		$arr_data[0] = "����ҧ��";
		$arr_data[1] = (!$DEDUCT_11 ? 0 : $DEDUCT_11);
		$arr_data[2] = " �ҷ.";
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "20", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		
		$arr_data[0] = "�Թ���¡�׹";
		$arr_data[1] = (!$DEDUCT_12 ? 0 : $DEDUCT_12);
		$arr_data[2] = " �ҷ.";
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "20", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		
		if ($DEDUCT_13 > 0) {
			$arr_data[0] = "";
			$arr_data[1] = (!$DEDUCT_13 ? 0 : $DEDUCT_13);
			$arr_data[2] = " �ҷ.";
		
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "20", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}
		
		$arr_data[0] = "�Թ���ʴԡ��";
		$arr_data[1] = (!$DEDUCT_14 ? 0 : $DEDUCT_14);
		$arr_data[2] = " �ҷ.";
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "20", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		
		$arr_data[0] = "��Ҭһ��Ԩ";
		$arr_data[1] = (!$DEDUCT_15 ? 0 : $DEDUCT_15);
		$arr_data[2] = " �ҷ.";
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "20", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		
		if ($DEDUCT_16 > 0) {
			$arr_data[0] = "";
			$arr_data[1] = (!$DEDUCT_16 ? 0 : $DEDUCT_16);
			$arr_data[2] = " �ҷ.";
		
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "20", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}
		
		if ($EXTRA_DEDUCT_01 > 0) {
			$arr_data[0] = "$DEDUCT_NAME_01";
			$arr_data[1] = (!$EXTRA_DEDUCT_01 ? 0 : $EXTRA_DEDUCT_01);
			$arr_data[2] = " �ҷ.";
		
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "20", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}
		
		if ($EXTRA_DEDUCT_02 > 0) {
			$arr_data[0] = "$DEDUCT_NAME_02";
			$arr_data[1] = (!$EXTRA_DEDUCT_02 ? 0 : $EXTRA_DEDUCT_02);
			$arr_data[2] = " �ҷ.";
		
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "20", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}
		
		if ($EXTRA_DEDUCT_03 > 0) {
			$arr_data[0] = "$DEDUCT_NAME_03";
			$arr_data[1] = (!$EXTRA_DEDUCT_03 ? 0 : $EXTRA_DEDUCT_03);
			$arr_data[2] = " �ҷ.";
		
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "20", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}
		
		if ($EXTRA_DEDUCT_04 > 0) {
			$arr_data[0] = "$DEDUCT_NAME_04";
			$arr_data[1] = (!$EXTRA_DEDUCT_04 ? 0 : $EXTRA_DEDUCT_04);
			$arr_data[2] = " �ҷ.";
		
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "20", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}
		
		if ($EXTRA_DEDUCT_05 > 0) {
			$arr_data[0] = "$DEDUCT_NAME_05";
			$arr_data[1] = (!$EXTRA_DEDUCT_05 ? 0 : $EXTRA_DEDUCT_05);
			$arr_data[2] = " �ҷ.";
		
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "20", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}
		
		if ($EXTRA_DEDUCT_06 > 0) {
			$arr_data[0] = "$DEDUCT_NAME_06";
			$arr_data[1] = (!$EXTRA_DEDUCT_06 ? 0 : $EXTRA_DEDUCT_06);
			$arr_data[2] = " �ҷ.";
		
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "20", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}
		
		if ($EXTRA_DEDUCT_07 > 0) {
			$arr_data[0] = "$DEDUCT_NAME_07";
			$arr_data[1] = (!$EXTRA_DEDUCT_07 ? 0 : $EXTRA_DEDUCT_07);
			$arr_data[2] = " �ҷ.";
		
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "20", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}
		
		if ($EXTRA_DEDUCT_08 > 0) {
			$arr_data[0] = "$DEDUCT_NAME_08";
			$arr_data[1] = (!$EXTRA_DEDUCT_08 ? 0 : $EXTRA_DEDUCT_08);
			$arr_data[2] = " �ҷ.";
		
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "20", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}
		
		$arr_data[0] = "����";
		$arr_data[1] = (!$OTHER_DEDUCT ? 0 : $OTHER_DEDUCT);
		$arr_data[2] = " �ҷ.";
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "20", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		
		$arr_data[0] = "�����¡���ѡ";
		$arr_data[1] = (!$TOTAL_DEDUCT ? 0 : $TOTAL_DEDUCT);
		$arr_data[2] = " �ҷ.";
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "20", "bU", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		
		$arr_data[0] = "�ӹǹ�Թ����͹��Ҹ�Ҥ��";
		$arr_data[1] = (!$NET_INCOME ? 0 : $NET_INCOME);
		$arr_data[2] = " �ҷ.";
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "20", "bU", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";

		$pdf->close_tab("");

		$pdf->SetFont($font,'b',20);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
//		$pdf->Cell(200,7,"___________________________________________________________________________________________________________________",0,1,"C");
		$pdf->Cell(200,7,"ŧ����                                                                                        �����˹�ҷ������Թ",0,1,"L");
		$pdf->Cell(200,7,"$FULL_NAME",0,1,"L");

		$pdf->AddPage();
		$pdf->Cell(200,7,"�����˵�",0,1,"L");

		$heading_text_s= array("","","");
		$head_text1 = implode(",",$heading_text_s);	// ����������
		$heading_width_s= array(17,35,148);
		$head_width1 = implode(",",$heading_width_s);
		$heading_align_s = array("L","L","L");
		$head_align1 = implode(",",$heading_align_s);
		$column_function_s = array(($NUMBER_DISPLAY==2)?"TNUM":"ENUM",($NUMBER_DISPLAY==2)?"TNUM":"ENUM",($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$col_function = implode(",", $column_function_s);
		$COLUMN_FORMAT = do_COLUMN_FORMAT($heading_text_s, $heading_width_s, $heading_align_s);
//		echo "Personal Information--text:$head_text1, width:$head_width1, align:$head_align1, col:$col_function, format:$COLUMN_FORMAT<br>";
		$result = $pdf->open_tab($head_text1, $head_width1, 7, "", $head_align1, "", "20", "b", "0066CC", "EEEEFF", $COLUMN_FORMAT, $col_function, true);
		if (!$result) echo "****** error ****** on open table for Personal Information<br>";

		$data_align = array("L","L","L");
		
		$arr_data = (array) null;

		$arr_data[0] = "�Թ�Ѻ";
		$arr_data[1] = "�.�.���.";
		$arr_data[2] = "�Թ��ҵͺ᷹���Ҩ���᷹��èѴ��ö��Шӵ��˹�";
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "20", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";

		$arr_data[0] = "";
		$arr_data[1] = "�.�.�.";
		$arr_data[2] = "�Թ������ä�ͧ�վ���Ǥ��Ǣ���Ҫ���";
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "20", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		
		$arr_data[0] = "";
		$arr_data[1] = "�.�.�.";
		$arr_data[2] = "�Թ������ä�ͧ�վ���Ǥ����١��ҧ��Ш�";
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "20", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";

		$arr_data[0] = "�Թ�ѡ";
		$arr_data[1] = "���Ѵ�Թ";
		$arr_data[2] = "���Ѵ�Թ";
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "20", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";

		$arr_data[0] = "";
		$arr_data[1] = "���ʴ�.���.";
		$arr_data[2] = "�Թ������ʴԡ�ø�Ҥ������Թ";
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "20", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";

		$arr_data[0] = "";
		$arr_data[1] = "�.�.�.";
		$arr_data[2] = "�Թ���¡�׹����Թ���� (�Թ��͹/��Ҩ�ҧ��Ш� �Թ����ԡ�ҡ���ؤ�ҡ�)";
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "20", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";

		$arr_data[0] = "";
		$arr_data[1] = "�.�.�.�.�.";
		$arr_data[2] = "���¡�׹�Թ�ͺ᷹����ɢ���Ҫ��ü�����Ѻ�Թ��͹����٧ ( ������)";
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "20", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";

		$arr_data[0] = "";
		$arr_data[1] = "�.�.�.�.�.";
		$arr_data[2] = "���¡�׹�Թ�ͺ᷹������١��ҧ��ШӼ�����Ѻ��Ҩ�ҧ�֧����٧ (������)";
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "20", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";

		$arr_data[0] = "";
		$arr_data[1] = "ä.�.�.�.�0100";
		$arr_data[2] = "���¡�׹��й����Թ��Шӵ��˹觼��������дѺ�٧-��ҧ";
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "20", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";

		$arr_data[0] = "";
		$arr_data[1] = "ä.�.�.�.";
		$arr_data[2] = "���¡�׹�Թ������ä�ͧ�վ���Ǥ��Ǣ���Ҫ���";
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "20", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";

		$arr_data[0] = "";
		$arr_data[1] = "ä.�.�.�.";
		$arr_data[2] = "���¡�׹�Թ������ä�ͧ�վ���Ǥ����١��ҧ��Ш�";
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "20", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";

		$arr_data[0] = "";
		$arr_data[1] = "ä.�.�.�.���.";
		$arr_data[2] = "���¡�׹��й����Թ�ͺ᷹�����͹����Ҫ�������Թ��Шӵ��˹�";
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "20", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";

		$arr_data[0] = "";
		$arr_data[1] = "ä.�.�.�.1-7";
		$arr_data[2] = "���¡�׹�Թ��й����Թ��ҵͺ᷹�����͹����Ҫ����дѺ 1-7";
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "20", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";

		$arr_data[0] = "";
		$arr_data[1] = "ä.�.�.8-8 �.";
		$arr_data[2] = "���¡�׹�Թ��й����Թ�ͺ᷹�����͹����Ҫ��� �дѺ 8 ��� 8 �.";
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "20", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";

		$arr_data[0] = "";
		$arr_data[1] = "�.�.�ˡó�";
		$arr_data[2] = "�Թ�ҡ�ˡó�";
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "20", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";

		$arr_data[0] = "";
		$arr_data[1] = "�.�. Comp ICT";
		$arr_data[2] = "�Թ��� Computer ICT";
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "20", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";

		$arr_data[0] = "";
		$arr_data[1] = "����֡��";
		$arr_data[2] = "�Թ������͡���֡��";
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "20", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";

		$pdf->close_tab("");

	}else{
		$pdf->SetFont($font,'b',20);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(200,7,"********** ����բ����� **********",0,1,"C");
	} // end if

	$pdf->close();
	$pdf->Output();
	
	ini_set("max_execution_time", 60);

	function 	do_COLUMN_FORMAT($heading_text, $heading_width, $data_align) {
		$total_head_width = 0;
		for($iii=0; $iii < count($heading_width); $iii++) 	$total_head_width += (int)$heading_width[$iii];
	
		$arr_column_map = (array) null;
		$arr_column_sel = (array) null;
		for($i=0; $i < count($heading_text); $i++) {
			$arr_column_map[] = $i;		// link index �ͧ head 
			$arr_column_sel[] = 1;			// 1=�ʴ�	0=����ʴ�   �������ա�û�Ѻ����§ҹ �����ʴ����
		}
		$arr_column_width = $heading_width;	// �������ҧ
		$arr_column_align = $data_align;		// align
		$COLUMN_FORMAT = implode(",",$arr_column_map)."|".implode(",",$arr_column_sel)."|".implode(",",$arr_column_width)."|".implode(",",$arr_column_align);
		
		return $COLUMN_FORMAT;
	}
?>