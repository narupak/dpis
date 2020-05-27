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
	
	$temp_start =  save_date($search_test_date_from);
	$temp_end =  save_date($search_test_date_to);
	$temp_start =  save_date($search_approve_date_from);
	$temp_end =  save_date($search_approve_date_to);
	$arr_condi = (array) null;
	if ($PER_ID) $arr_condi[] = "PER_ID = $PER_ID";
	if ($temp_start) $arr_condi[] = "CA_APPROVE_DATE>='$temp_start'";
	if ($temp_end) $arr_condi[] = "CA_APPROVE_DATE<='$temp_end'";
	if (count($arr_condi) > 0)  $where = "where ".implode(" and ", $arr_condi); else $where = "";
	
	$cmd = "select CA_COURSE, ORG_CODE, CA_SEQ, CA_CODE, CA_TYPE, PER_ID, CA_TEST_DATE,
							CA_APPROVE_DATE, PN_CODE, CA_NAME, CA_SURNAME, CA_CARDNO, CA_CONSISTENCY, CA_SCORE_1, CA_SCORE_2,
							CA_SCORE_3, CA_SCORE_4, CA_SCORE_5, CA_SCORE_6, CA_SCORE_7, CA_SCORE_8, CA_SCORE_9, CA_SCORE_10,
							CA_SCORE_11, CA_SCORE_12, CA_MEAN, CA_MINISTRY_NAME, CA_DEPARTMENT_NAME, CA_ORG_NAME, CA_ORG_NAME1,
							CA_ORG_NAME2, CA_LINE, LEVEL_NO, CA_MGT, CA_NEW_SCORE_1, CA_NEW_SCORE_2, CA_NEW_SCORE_3, CA_NEW_SCORE_4,
							CA_NEW_SCORE_5, CA_NEW_SCORE_6, CA_NEW_SCORE_7, CA_NEW_SCORE_8, CA_NEW_SCORE_9, CA_NEW_SCORE_10,
							CA_NEW_SCORE_11, CA_NEW_MEAN, CA_REMARK, UPDATE_USER, UPDATE_DATE 
							from PER_MGT_COMPETENCY_ASSESSMENT ".$where." order by CA_CODE ";
//							where CA_TEST_DATE>='$temp_start' and CA_TEST_DATE<='$temp_end'
//							where CA_ID=$CA_ID ";
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

		$logo_path = "../images/";
		$logo_file = $logo_path."logo.jpg";
//		echo "1.-".$logo_file." @@@@ (".file_exists($logo_file).")<br>";

		$start_x = $pdf->x;
		$start_y = $pdf->y;

		// print image logo
		$head_text1 = ",,";	// ������� head
		$head_width1 = "65,70,65";
		$head_align1 = "L,C,R";
		$column_function = array(($NUMBER_DISPLAY==2)?"TNUM":"ENUM",($NUMBER_DISPLAY==2)?"TNUM":"ENUM",($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$col_function = implode(",", $column_function);
		$COLUMN_FORMAT = do_COLUMN_FORMAT(explode(",",$head_text1), explode(",",$head_width1), explode(",",$head_align1));
//		echo "$HISTORY_NAME--text:$head_text1, width:$head_width1, align:$head_align1, col:$col_function, format:$COLUMN_FORMAT<br>";
		$result = $pdf->open_tab($head_text1, $head_width1, 7, "TLHBR", $head_align1, "", "14", "b", "0066CC", "EEEEFF", $COLUMN_FORMAT, $col_function, true);
		if (!$result) echo "****** error ****** on open table for $table<br>";

		$arr_data = (array) null;
		$arr_data[0] = "";
		$arr_data[1] = "<&&row&&><*img*".$logo_file."*img*>";
		$arr_data[2] = "";

		$data_align = array("L","C","R");

		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");	// line 1
		if (!$result) echo "****** error ****** add data to table at head line 1 <br>";

		$arr_data = (array) null;
		$arr_data[0] = "";
		$arr_data[1] = "<&&row&&><*img*".$logo_file."*img*>";
		$arr_data[2] = "";

		$data_align = array("L","C","R");
		
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");	// line 2
		if (!$result) echo "****** error ****** add data to table at head line 2 <br>";

		$arr_data = (array) null;
		$arr_data[0] = "";
		$arr_data[1] = "<&&row&&><*img*".$logo_file."*img*>";
		$arr_data[2] = "";

		$data_align = array("L","C","R");
		
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "U", "000000", "");	// line 3
		if (!$result) echo "****** error ****** add data to table at head line 3 <br>";

		$arr_data = (array) null;
		$arr_data[0] = "";
		$arr_data[1] = "<&&end&&><*img*".$logo_file."*img*>";
		$arr_data[2] = "";

		$data_align = array("L","C","R");
		
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "U", "000000", "");	// line 4
		if (!$result) echo "****** error ****** add data to table at head line 4<br>";

		$arr_data = (array) null;
		$arr_data[0] = "";
		$arr_data[1] = "";
		$arr_data[2] = "";

		$data_align = array("L","C","R");
		
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");	// line 4
		if (!$result) echo "****** error ****** add data to table at head line 4<br>";

		$pdf->close_tab("",""); 

		// ��͹���˹�价��鹡�д�� ���;������ǹ�����§ҹ ���ǹ text �ա��
		$pdf->x = $start_x;
		$pdf->y = $start_y;
		
		$HEAD_01_1 = "����Ѻ��û����Թ�ѹ��� ";
		$HEAD_01_2 = "$CA_TEST_DATE";
		$HEAD_01_4 = "���� ";
		$HEAD_01_5 = "$CA_CODE                                        ";
		$HEAD_02_4 = "���� ";
		$HEAD_02_5 = "                                                  ";		

		$pdf->SetFont($font,'',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(200 ,7," ","",1,"L");
		$pdf->Cell(200 ,7," ","",1,"L");
		$line_h = 7;
		$arr_text = array("$HEAD_01_1","$HEAD_01_2","","$HEAD_01_4","$HEAD_01_5");
		$arr_align = array("L","L","C","R","R");
		$arr_border = array("","","","","");
		$arr_font = array("$font||16","$font|U|16","$font||16","$font||16","$font|U|16");
		$arr_width = array(65,"-",70,65,"-");
		$arr_col_func = array(($NUMBER_DISPLAY==2)?"TNUM":"ENUM",($NUMBER_DISPLAY==2)?"TNUM":"ENUM",($NUMBER_DISPLAY==2)?"TNUM":"ENUM",($NUMBER_DISPLAY==2)?"TNUM":"ENUM",($NUMBER_DISPLAY==2)?"TNUM":"ENUM");

		$a = $pdf->add_lineFreeTab($line_h, $arr_text, $arr_align, $arr_border, $arr_font, $arr_width, $arr_col_func);
		$arr_text = array("","","","$HEAD_02_4","$HEAD_02_5");
		$a = $pdf->add_lineFreeTab($line_h, $arr_text, $arr_align, $arr_border, $arr_font);
		$pdf->SetFont($font,'',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(200 ,7," ","",1,"L");

		$line_h = 7;
		$arr_text = array(""," ��§ҹ�š�û����Թ���ö����ѡ�ҧ��ú����� ","");
		$arr_align = array("L","C","L");
		$arr_border = array("","TLBR","");
		$arr_font = array("","$font||16","");
		$a = $pdf->add_lineFreeTab($line_h, $arr_text, $arr_align, $arr_border, $arr_font);

//		$pdf->Cell(200 ,7,"                    ��§ҹ����繡��͸Ժ�¼š�û����Թ���ö����ѡ�ҧ��ú����� �����","",1,"L");
		$arr_text = array("","��§ҹ����繡��͸Ժ�¼š�û����Թ���ö����ѡ�ҧ��ú����� �����");
		$arr_align = array("L","L");
		$arr_border = array("","");
		$arr_font = array("$font||16","$font||16");
		$arr_width = array(22,178);
		$a = $pdf->add_lineFreeTab($line_h, $arr_text, $arr_align, $arr_border, $arr_font, $arr_width);
		
		$pdf->SetFont($font,'',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(200 ,7,"�س�ѡɳз���������Ѻ���˹觹ѡ�������дѺ�٧","",1,"L");

//		$pdf->Cell(200 ,7,"                    �š�û����Թ�� 3 �дѺ ���","",1,"L");
		$arr_text = array("","�š�û����Թ�� 3 �дѺ ���");
		$a = $pdf->add_lineFreeTab($line_h, $arr_text, $arr_align, $arr_border, $arr_font, $arr_width);

//		$pdf->Cell(200 ,7,"                    1. 75.00 % ����        ���¶֧  �����ö�й�������","",1,"L");
		$arr_text = array("","1. 75.00 % ����        ���¶֧  �����ö�й�������");
		$a = $pdf->add_lineFreeTab($line_h, $arr_text, $arr_align, $arr_border, $arr_font, $arr_width);
//		$pdf->Cell(200 ,7,"                    2. 60.00 � 74.99 %     ���¶֧  �����ö�й��������дѺ��ҧ ���þѲ���������","",1,"L");
		$arr_text = array("","2. 60.00 � 74.99 %     ���¶֧  �����ö�й��������дѺ��ҧ ���þѲ���������");
		$a = $pdf->add_lineFreeTab($line_h, $arr_text, $arr_align, $arr_border, $arr_font, $arr_width);
//		$pdf->Cell(200 ,7,"                    3. ��ӡ��� 60.00 %       ���¶֧  �դ������繵�ͧ�Ѳ�����ö�й��","",1,"L");
		$arr_text = array("","3. ��ӡ��� 60.00 %       ���¶֧  �դ������繵�ͧ�Ѳ�����ö�й��");
		$a = $pdf->add_lineFreeTab($line_h, $arr_text, $arr_align, $arr_border, $arr_font, $arr_width);

		$arr_text = array("","���ö����ѡ�ҧ��ú�����","�š�û����Թ");
		$arr_align = array("L","C","C");
		$arr_border = array("","TLBR","TLBR");
		$arr_font = array("","$font|b|16|AAAAAA|555555","$font|b|16|AAAAAA|555555");
		$arr_width = array(22,128,50);
		$arr_col_func = array(($NUMBER_DISPLAY==2)?"TNUM":"ENUM",($NUMBER_DISPLAY==2)?"TNUM":"ENUM",($NUMBER_DISPLAY==2)?"TNUM-2":"ENUM-2");
		$a = $pdf->add_lineFreeTab($line_h, $arr_text, $arr_align, $arr_border, $arr_font, $arr_width, $arr_col_func);

		$arr_font = array("$font||16","$font||16","$font||16");
		$arr_align = array("L","L","C");
		$arr_text = array("","1. �����ͺ���㹡�ú�����","");
		$a = $pdf->add_lineFreeTab($line_h, $arr_text, $arr_align, $arr_border, $arr_font, $arr_width, $arr_col_func);

		$arr_text = array("","      �  ��ú����á������¹�ŧ (Managing Change)","$CA_SCORE_1");
		$a = $pdf->add_lineFreeTab($line_h, $arr_text, $arr_align, $arr_border, $arr_font, $arr_width, $arr_col_func);

		$arr_text = array("","      �  ����ըԵ��觺�ԡ�� (Customer Service Orientation)","$CA_SCORE_2");
		$a = $pdf->add_lineFreeTab($line_h, $arr_text, $arr_align, $arr_border, $arr_font, $arr_width, $arr_col_func);

		$arr_text = array("","      �  ����ҧἹ�ԧ���ط�� (Strategic Planning)","$CA_SCORE_3");
		$a = $pdf->add_lineFreeTab($line_h, $arr_text, $arr_align, $arr_border, $arr_font, $arr_width, $arr_col_func);

		$arr_text = array("","2. ��ú��������ҧ����Ҫվ","");
		$a = $pdf->add_lineFreeTab($line_h, $arr_text, $arr_align, $arr_border, $arr_font, $arr_width, $arr_col_func);

		$arr_text = array("","      �  ��õѴ�Թ� (Decision Making)","$CA_SCORE_4");
		$a = $pdf->add_lineFreeTab($line_h, $arr_text, $arr_align, $arr_border, $arr_font, $arr_width, $arr_col_func);

		$arr_text = array("","      �  ��äԴ�ԧ���ط�� (Strategic Thinking)","$CA_SCORE_5");
		$a = $pdf->add_lineFreeTab($line_h, $arr_text, $arr_align, $arr_border, $arr_font, $arr_width, $arr_col_func);

		$arr_text = array("","      �  �����繼��� (Leadership)","$CA_SCORE_6");
		$a = $pdf->add_lineFreeTab($line_h, $arr_text, $arr_align, $arr_border, $arr_font, $arr_width, $arr_col_func);

		$arr_text = array("","3. ��ú����ä�","");
		$a = $pdf->add_lineFreeTab($line_h, $arr_text, $arr_align, $arr_border, $arr_font, $arr_width, $arr_col_func);

		$arr_text = array("","      �  ��û�Ѻ�����Ф����״���� (Adaptability and Flexibility)","$CA_SCORE_7");
		$a = $pdf->add_lineFreeTab($line_h, $arr_text, $arr_align, $arr_border, $arr_font, $arr_width, $arr_col_func);

		$arr_text = array("","      �  ��������ö��зѡ��㹡��������� (Communication)","$CA_SCORE_8");
		$a = $pdf->add_lineFreeTab($line_h, $arr_text, $arr_align, $arr_border, $arr_font, $arr_width, $arr_col_func);
		
		$arr_text = array("","      �  ��û���ҹ����ѹ�� (Collaborative)","$CA_SCORE_9");
		$a = $pdf->add_lineFreeTab($line_h, $arr_text, $arr_align, $arr_border, $arr_font, $arr_width, $arr_col_func);

		$arr_text = array("","4. ��ú�����Ẻ��觼����ķ���","");
		$a = $pdf->add_lineFreeTab($line_h, $arr_text, $arr_align, $arr_border, $arr_font, $arr_width);

		$arr_text = array("","      �  ����Ѻ�Դ�ͺ��Ǩ�ͺ�� (Accountability)","$CA_SCORE_10");
		$a = $pdf->add_lineFreeTab($line_h, $arr_text, $arr_align, $arr_border, $arr_font, $arr_width, $arr_col_func);

		$arr_text = array("","      �  ��÷ӧҹ������ؼ����ķ��� (Achieving Result)","$CA_SCORE_11");
		$a = $pdf->add_lineFreeTab($line_h, $arr_text, $arr_align, $arr_border, $arr_font, $arr_width, $arr_col_func);

		$arr_text = array("","      �  ��ú����÷�Ѿ�ҡ� (Managing Resources)","$CA_SCORE_12");
		$a = $pdf->add_lineFreeTab($line_h, $arr_text, $arr_align, $arr_border, $arr_font, $arr_width, $arr_col_func);

		$arr_font = array("","$font|b|16|AAAAAA|555555","$font|b|16|AAAAAA|555555");
		$arr_text = array("","������¡�û����Թ���ö����ѡ�ҧ��ú�����","$CA_MEAN");
		$a = $pdf->add_lineFreeTab($line_h, $arr_text, $arr_align, $arr_border, $arr_font, $arr_width, $arr_col_func);

		$pdf->SetFont($font,'',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

		$pdf->Cell(200 ,7,"","",1,"L");
		$arr_text = array("","ŧ���� ","                                        ");
		$arr_align = array("L","R","R");
		$arr_border = array("","","");
		$arr_font = array("","$font|b|16","$font|U|16");
		$arr_width = array(135,65,"-");
		$a = $pdf->add_lineFreeTab($line_h, $arr_text, $arr_align, $arr_border, $arr_font, $arr_width);
		$pdf->SetFont($font,'',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(200 ,7,"���˹�ҷ���Ǩ�ҹ�š�û����Թ�","",1,"R");
		$arr_text = array("","�ѹ��� ","                            ");
		$a = $pdf->add_lineFreeTab($line_h, $arr_text, $arr_align, $arr_border, $arr_font, $arr_width);
		
		$arr_text = array("�����˵�"," �š�û����Թ���ö����ѡ�ҧ��ú��������� 2 �� �Ѻ���ѹ�������Ѻ��û����Թ");
		$arr_align = array("L","L");
		$arr_border = array("","");
		$arr_font = array("$font|bU|16","$font||16");
		$a = $pdf->add_lineFreeTab($line_h, $arr_text, $arr_align, $arr_border, $arr_font);
/*
		// page 2
		$pdf->AddPage();

		$HEAD_01_1 = "�ѹ����鹺ѭ�� $CA_APPROVE_DATE";

		$head_text1 = ",,";	// ������� head
		$head_width1 = "65,70,65";
		$head_align1 = "L,C,R";
		$column_function = array(($NUMBER_DISPLAY==2)?"TNUM":"ENUM",($NUMBER_DISPLAY==2)?"TNUM":"ENUM",($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$col_function = implode(",", $column_function);
		$COLUMN_FORMAT = do_COLUMN_FORMAT(explode(",",$head_text1), explode(",",$head_width1), explode(",",$head_align1));
//		echo "$HISTORY_NAME--text:$head_text1, width:$head_width1, align:$head_align1, col:$col_function, format:$COLUMN_FORMAT<br>";
		$result = $pdf->open_tab($head_text1, $head_width1, 7, "", $head_align1, "", "14", "b", "0066CC", "EEEEFF", $COLUMN_FORMAT, $col_function, true);
		if (!$result) echo "****** error ****** on open table for $table<br>";

		$arr_data = (array) null;
		$arr_data[0] = "";
		$arr_data[1] = "<&&row&&><*img*".$logo_file."*img*>";
		$arr_data[2] = "";

		$data_align = array("L","C","R");

		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");	// line 1
		if (!$result) echo "****** error ****** add data to table at head line 1 <br>";

		$arr_data = (array) null;
		$arr_data[0] = "";
		$arr_data[1] = "<&&row&&><*img*".$logo_file."*img*>";
		$arr_data[2] = "";

		$data_align = array("L","C","R");
		
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");	// line 2
		if (!$result) echo "****** error ****** add data to table at head line 2 <br>";

		$arr_data = (array) null;
		$arr_data[0] = "$HEAD_01_1";
		$arr_data[1] = "<&&row&&><*img*".$logo_file."*img*>";
		$arr_data[2] = "$HEAD_01_3";

		$data_align = array("L","C","R");
		
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");	// line 3
		if (!$result) echo "****** error ****** add data to table at head line 3 <br>";

		$arr_data = (array) null;
		$arr_data[0] = "";
		$arr_data[1] = "<&&row&&><*img*".$logo_file."*img*>";
		$arr_data[2] = "$HEAD_02";

		$data_align = array("L","C","R");
		
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");	// line 4
		if (!$result) echo "****** error ****** add data to table at head line 4<br>";

		$pdf->close_tab(""); 

		$line_h = 7;
		$arr_text = array("","��§ҹ�š�û����Թ���ö����ѡ�ҧ��ú�����","");
		$arr_align = array("L","C","L");
		$arr_border = array("","TLBR","");
		$arr_font = array("","$font||16","");
		$a = $pdf->add_lineFreeTab($line_h, $arr_text, $arr_align, $arr_border, $arr_font);

		$pdf->SetFont($font,'',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

		$pdf->Cell(200 ,7,"                    ��§ҹ����繡��͸Ժ�¼š�û����Թ���ö����ѡ�ҧ��ú����� ����繤س�ѡɳз���������Ѻ���˹觹ѡ�������дѺ�٧","",1,"L");
		$pdf->Cell(200 ,7,"                    �š�û����Թ�� 3 �дѺ ���","",1,"L");
		$pdf->Cell(200 ,7,"                    1. 75.00 % ����	���¶֧	�����ö�й�������","",1,"L");
		$pdf->Cell(200 ,7,"                    2. 60.00 � 74.99 %	���¶֧	�����ö�й��������дѺ��ҧ ���þѲ���������","",1,"L");
		$pdf->Cell(200 ,7,"                    3. ��ӡ��� 60.00 %	���¶֧	�դ������繵�ͧ�Ѳ�����ö�й��","",1,"L");

		$head_text1 = "���ö����ѡ�ҧ��ú�����,�š�û����Թ";
		$head_width1 = "150,50";
		$head_align1 = "L,C";
		$column_function = array(($NUMBER_DISPLAY==2)?"TNUM":"ENUM",($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
		$col_function = implode(",", $column_function);
		$COLUMN_FORMAT = do_COLUMN_FORMAT(explode(",",$head_text1), explode(",",$head_width1), explode(",",$head_align1));
//		echo "$HISTORY_NAME--text:$head_text1, width:$head_width1, align:$head_align1, col:$col_function, format:$COLUMN_FORMAT<br>";
		$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", "0066CC", "EEEEFF", $COLUMN_FORMAT, $col_function, true);
		if (!$result) echo "****** error ****** on open table for $table<br>";

		$arr_data = (array) null;
		$arr_data[0] = "1. �����ͺ���㹡�ú�����";
		$arr_data[1] = "";

		$data_align = array("L","C");

		$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", "000000", "");	// line 1
		if (!$result) echo "****** error ****** add data to table at line 1 <br>";

		$arr_data = (array) null;
		$arr_data[0] = "      �	��ú����á������¹�ŧ (Managing Change)";
		$arr_data[1] = "$CA_NEW_SCORE_1";

		$data_align = array("L","C");
		
		$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", "000000", "");	// line 2
		if (!$result) echo "****** error ****** add data to table at line 2 <br>";

		$arr_data = (array) null;
		$arr_data[0] = "      �	����ըԵ��觺�ԡ�� (Customer Service Orientation)";
		$arr_data[1] = "$CA_NEW_SCORE_2";

		$data_align = array("L","C");
		
		$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", "000000", "");	// line 3
		if (!$result) echo "****** error ****** add data to table at line 3 <br>";

		$arr_data = (array) null;
		$arr_data[0] = "      �	����ҧἹ�ԧ���ط�� (Strategic Planning)";
		$arr_data[1] = "$CA_NEW_SCORE_3";

		$data_align = array("L","C");
		
		$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", "000000", "");	// line 4
		if (!$result) echo "****** error ****** add data to table at line 4 <br>";

		$arr_data = (array) null;
		$arr_data[0] = "2. ��ú��������ҧ����Ҫվ";
		$arr_data[1] = "";

		$data_align = array("L","C");

		$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", "000000", "");	// line 1
		if (!$result) echo "****** error ****** add data to table at line 5 <br>";

		$arr_data = (array) null;
		$arr_data[0] = "      �	��õѴ�Թ� (Decision Making)";
		$arr_data[1] = "$CA_NEW_SCORE_4";

		$data_align = array("L","C");
		
		$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", "000000", "");	// line 2
		if (!$result) echo "****** error ****** add data to table at line 6 <br>";

		$arr_data = (array) null;
		$arr_data[0] = "      �	��äԴ�ԧ���ط�� (Strategic Thinking)";
		$arr_data[1] = "$CA_NEW_SCORE_5";

		$data_align = array("L","C");
		
		$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", "000000", "");	// line 3
		if (!$result) echo "****** error ****** add data to table at line 7 <br>";

		$arr_data = (array) null;
		$arr_data[0] = "      �	�����繼��� (Leadership)";
		$arr_data[1] = "$CA_NEW_SCORE_6";

		$data_align = array("L","C");
		
		$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", "000000", "");	// line 4
		if (!$result) echo "****** error ****** add data to table at line 8 <br>";

		$arr_data = (array) null;
		$arr_data[0] = "3. ��ú����ä�";
		$arr_data[1] = "";

		$data_align = array("L","C");

		$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", "000000", "");	// line 1
		if (!$result) echo "****** error ****** add data to table at line 9 <br>";

		$arr_data = (array) null;
		$arr_data[0] = "      �	��û�Ѻ�����Ф����״���� (Adaptability and Flexibility)";
		$arr_data[1] = "$CA_NEW_SCORE_7";

		$data_align = array("L","C");
		
		$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", "000000", "");	// line 2
		if (!$result) echo "****** error ****** add data to table at line 10 <br>";

		$arr_data = (array) null;
		$arr_data[0] = "      �	��������ö��зѡ��㹡��������� (Communication)";
		$arr_data[1] = "$CA_NEW_SCORE_8";

		$data_align = array("L","C");
		
		$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", "000000", "");	// line 3
		if (!$result) echo "****** error ****** add data to table at line 11 <br>";

		$arr_data = (array) null;
		$arr_data[0] = "      �	��û���ҹ����ѹ�� (Collaborative)";
		$arr_data[1] = "$CA_NEW_SCORE_9";

		$data_align = array("L","C");
		
		$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", "000000", "");	// line 4
		if (!$result) echo "****** error ****** add data to table at line 12 <br>";

		$arr_data = (array) null;
		$arr_data[0] = "4. ��ú�����Ẻ��觼����ķ���";
		$arr_data[1] = "";

		$data_align = array("L","C");

		$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", "000000", "");	// line 1
		if (!$result) echo "****** error ****** add data to table at line 13 <br>";

		$arr_data = (array) null;
		$arr_data[0] = "      �	����Ѻ�Դ�ͺ��Ǩ�ͺ�� (Accountability)";
		$arr_data[1] = "$CA_NEW_SCORE_10";

		$data_align = array("L","C");
		
		$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", "000000", "");	// line 2
		if (!$result) echo "****** error ****** add data to table at line 14 <br>";

		$arr_data = (array) null;
		$arr_data[0] = "      �	��÷ӧҹ������ؼ����ķ��� (Achieving Result)";
		$arr_data[1] = "$CA_NEW_SCORE_11";

		$data_align = array("L","C");
		
		$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", "000000", "");	// line 3
		if (!$result) echo "****** error ****** add data to table at line 15 <br>";

		$arr_data = (array) null;
		$arr_data[0] = "      �	��ú����÷�Ѿ�ҡ� (Managing Resources)";
		$arr_data[1] = "$CA_NEW_SCORE_12";

		$data_align = array("L","C");
		
		$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", "000000", "");	// line 4
		if (!$result) echo "****** error ****** add data to table at line 16 <br>";

		$arr_data = (array) null;
		$arr_data[0] = "������¡�û����Թ���ö����ѡ�ҧ��ú�����";
		$arr_data[1] = "$CA_NEW_MEAN";

		$data_align = array("L","C");
		
		$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", "000000", "");	// line 4
		if (!$result) echo "****** error ****** add data to table at line 17 <br>";

		$pdf->close_tab(""); 

		$pdf->SetFont($font,'',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

		$pdf->Cell(200 ,7,"","",1,"L");
		$pdf->Cell(200 ,7,"ŧ����..........................................................","",1,"R");
		$pdf->Cell(200 ,7,"���˹�ҷ���Ǩ�ҹ�š�û����Թ�","",1,"R");
		$pdf->Cell(200 ,7,"�ѹ��腅�������������.","",1,"R");
		
		$arr_text = array("�����˵�","�š�û����Թ���ö����ѡ�ҧ��ú��������� 2 �� �Ѻ���ѹ�������Ѻ��û����Թ");
		$arr_align = array("L","L");
		$arr_border = array("bU","");
		$arr_font = array("$font||16","$font||16");
		$a = $pdf->add_lineFreeTab($line_h, $arr_text, $arr_align, $arr_border, $arr_font);
*/
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