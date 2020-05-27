<?
//	session_start();	
	include("../../php_scripts/connect_database.php");
	include("../php_scripts/pdf_wordarray_thaicut.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php");

	ini_set("max_execution_time", $max_execution_time);

	if (!$font) $font = "AngsanaUPC";

	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "";
	$report_title = "";
	$report_code = "";
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
	
	if($flag_type==1){	//วันที่ประเมิน 
		$HEAD_01_1 = "เข้ารับการประเมินวันที่ ";
		$FOOT_01_1 = "วันที่เข้ารับการประเมิน ";
	}else if($flag_type==2){	//วันที่ขึ้นทะเบียน
		$HEAD_01_1 = "วันที่ขึ้นบัญชี ";
		$FOOT_01_1 = "วันที่ขึ้นบัญชี ";
	}
	
	$arr_condi = (array) null;
	if ($SEARCH_CA_ID) $arr_condi[] = "CA_ID = $SEARCH_CA_ID";
	else {
		if($flag_type==1){	//วันที่ประเมิน 
			$temp_start =  save_date($search_test_date_from);
			$temp_end =  save_date($search_test_date_to);
			if ($temp_start) $arr_condi[] = "CA_TEST_DATE>='$temp_start'";
			if ($temp_end) $arr_condi[] = "CA_TEST_DATE<='$temp_end'";
		}else if($flag_type==2){	//วันที่ขึ้นทะเบียน
			$temp_start =  save_date($search_approve_date_from);
			$temp_end =  save_date($search_approve_date_to);
			if ($temp_start) $arr_condi[] = "CA_APPROVE_DATE>='$temp_start'";
			if ($temp_end) $arr_condi[] = "CA_APPROVE_DATE<='$temp_end'";
		}
//	if (count($arr_condi) > 0)  $where = "where ".implode(" and ", $arr_condi); else $where = "";
	}
	if (count($arr_condi) > 0) 	$search_condition 	= " and " . implode(" and ", $arr_condi);

	$cmd = "select CA_COURSE, ORG_CODE, CA_SEQ, CA_CODE, CA_TYPE, PER_ID, CA_TEST_DATE,
							CA_APPROVE_DATE, PN_CODE, CA_NAME, CA_SURNAME, CA_CARDNO, CA_CONSISTENCY, CA_SCORE_1, CA_SCORE_2,
							CA_SCORE_3, CA_SCORE_4, CA_SCORE_5, CA_SCORE_6, CA_SCORE_7, CA_SCORE_8, CA_SCORE_9, CA_SCORE_10,
							CA_SCORE_11, CA_SCORE_12, CA_MEAN, CA_MINISTRY_NAME, CA_DEPARTMENT_NAME, CA_ORG_NAME, CA_ORG_NAME1,
							CA_ORG_NAME2, CA_LINE, LEVEL_NO, CA_MGT, CA_NEW_SCORE_1, CA_NEW_SCORE_2, CA_NEW_SCORE_3, CA_NEW_SCORE_4,
							CA_NEW_SCORE_5, CA_NEW_SCORE_6, CA_NEW_SCORE_7, CA_NEW_SCORE_8, CA_NEW_SCORE_9, CA_NEW_SCORE_10,
							CA_NEW_SCORE_11, CA_NEW_MEAN, CA_REMARK, UPDATE_USER, UPDATE_DATE 
							from PER_MGT_COMPETENCY_ASSESSMENT
							where CA_CODE IS NOT NULL
							$search_condition
							$order_str ";
	$count_data = $db_dpis->send_cmd($cmd);
//echo "$cmd<br>";	
//$db_dpis->show_error();exit;

	if($count_data){
		$data_count = 0;
		while($data = $db_dpis->get_array()){
			$data_count++;
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

			$PN_CODE = trim($data[PN_CODE]);
			$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE) = '$PN_CODE' ";
			$db_dpis2->send_cmd($cmd);
	//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PN_NAME = trim($data2[PN_NAME]);

			if ($data_count > 1) $pdf->AddPage();

			$logo_path = "../images/";
			$logo_file = $logo_path."logo.jpg";
	//		echo "1.-".$logo_file." @@@@ (".file_exists($logo_file).")<br>";

			$start_x = $pdf->x;
			$start_y = $pdf->y;

			// print image logo
			$head_text1 = ",,";	// ไม่พิมพ์ head
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

			$pdf->x = $start_x;
			$pdf->y = $start_y;
			
			if($flag_type==1){	//วันที่ประเมิน 
				$HEAD_01_2 = "$CA_TEST_DATE";
			}else if($flag_type==2){	//วันที่ขึ้นทะเบียน
				$HEAD_01_2 = "$CA_APPROVE_DATE";
			}
			$HEAD_01_4 = "รหัส ";
			$HEAD_01_5 = "$CA_CODE";
			$HEAD_02_4 = "ชื่อ  ";
			$HEAD_02_5 = "$PN_NAME$CA_NAME $CA_SURNAME";		

			$pdf->SetFont($font,'',16);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
			$pdf->Cell(200 ,7," ","",1,"L");
			$pdf->Cell(200 ,7," ","",1,"L");
			$line_h = 7;
			$arr_text = array("$HEAD_01_1","$HEAD_01_2","","$HEAD_01_4","$HEAD_01_5");
			$arr_align = array("L","L","C","R","L");
			$arr_border = array("","","","","");
			$arr_font = array("$font||16","$font|U|16","$font||16","$font||16","$font|U|16");
			$arr_width = array(65,"-",70,15,50);
			$arr_col_func = array(($NUMBER_DISPLAY==2)?"TNUM":"ENUM",($NUMBER_DISPLAY==2)?"TNUM":"ENUM",($NUMBER_DISPLAY==2)?"TNUM":"ENUM",($NUMBER_DISPLAY==2)?"TNUM":"ENUM",($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
			$a = $pdf->add_lineFreeTab($line_h, $arr_text, $arr_align, $arr_border, $arr_font, $arr_width, $arr_col_func);
			$arr_text = array("","","","$HEAD_02_4","$HEAD_02_5");
			$a = $pdf->add_lineFreeTab($line_h, $arr_text, $arr_align, $arr_border, $arr_font, $arr_width, $arr_col_func);
			$pdf->SetFont($font,'',16);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
			$pdf->Cell(200 ,7," ","",1,"L");

			$line_h = 7;
			$arr_text = array(""," รายงานผลการประเมินสมรรถนะหลักทางการบริหาร ","");
			$arr_align = array("L","C","L");
			$arr_border = array("","TLBR","");
			$arr_font = array("","$font||16","");
			$a = $pdf->add_lineFreeTab($line_h, $arr_text, $arr_align, $arr_border, $arr_font);

	//		$pdf->Cell(200 ,7,"                    รายงานนี้เป็นการอธิบายผลการประเมินสมรรถนะหลักทางการบริหาร ซึ่งเป็น","",1,"L");
			$arr_text = array("","รายงานนี้เป็นการอธิบายผลการประเมินสมรรถนะหลักทางการบริหาร ซึ่งเป็น");
			$arr_align = array("L","L");
			$arr_border = array("","");
			$arr_font = array("$font||16","$font||16");
			$arr_width = array(22,178);
			$a = $pdf->add_lineFreeTab($line_h, $arr_text, $arr_align, $arr_border, $arr_font, $arr_width);
			
			$pdf->SetFont($font,'',16);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
			$pdf->Cell(200 ,7,"คุณลักษณะที่จำเป็นสำหรับตำแหน่งนักบริหารระดับสูง","",1,"L");

	//		$pdf->Cell(200 ,7,"                    ผลการประเมินมี 3 ระดับ คือ","",1,"L");
			$arr_text = array("","ผลการประเมินมี 3 ระดับ คือ");
			$a = $pdf->add_lineFreeTab($line_h, $arr_text, $arr_align, $arr_border, $arr_font, $arr_width);

	//		$pdf->Cell(200 ,7,"                    1. 75.00 % ขึ้นไป        หมายถึง  มีสมรรถนะนี้ดีแล้ว","",1,"L");
			$arr_text = array("","1. 75.00 % ขึ้นไป       หมายถึง  มีสมรรถนะนี้ดีแล้ว");
			$a = $pdf->add_lineFreeTab($line_h, $arr_text, $arr_align, $arr_border, $arr_font, $arr_width);
	//		$pdf->Cell(200 ,7,"                    2. 60.00 – 74.99 %     หมายถึง  มีสมรรถนะนี้แล้วในระดับกลาง แต่ควรพัฒนาเพิ่มขึ้น","",1,"L");
			$arr_text = array("","2. 60.00 – 74.99 %     หมายถึง  มีสมรรถนะนี้แล้วในระดับกลาง แต่ควรพัฒนาเพิ่มขึ้น");
			$a = $pdf->add_lineFreeTab($line_h, $arr_text, $arr_align, $arr_border, $arr_font, $arr_width);
	//		$pdf->Cell(200 ,7,"                    3. ต่ำกว่า 60.00 %       หมายถึง  มีความจำเป็นต้องพัฒนาสมรรถนะนี้","",1,"L");
			$arr_text = array("","3. ต่ำกว่า 60.00 %       หมายถึง  มีความจำเป็นต้องพัฒนาสมรรถนะนี้");
			$a = $pdf->add_lineFreeTab($line_h, $arr_text, $arr_align, $arr_border, $arr_font, $arr_width);

			$arr_text = array("","สมรรถนะหลักทางการบริหาร","ผลการประเมิน");
			$arr_align = array("L","C","C");
			$arr_border = array("","TLBR","TLBR");
			$arr_font = array("","$font|b|16|AAAAAA|555555","$font|b|16|AAAAAA|555555");
			$arr_width = array(22,128,50);
			$arr_col_func = array(($NUMBER_DISPLAY==2)?"TNUM":"ENUM",($NUMBER_DISPLAY==2)?"TNUM":"ENUM",($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
			$a = $pdf->add_lineFreeTab($line_h, $arr_text, $arr_align, $arr_border, $arr_font, $arr_width, $arr_col_func);

			$arr_font = array("$font||16","$font||16","$font||16");
			$arr_align = array("L","L","C");
			$arr_text = array("","1. ความรอบรู้ในการบริหาร","");
			$a = $pdf->add_lineFreeTab($line_h, $arr_text, $arr_align, $arr_border, $arr_font, $arr_width, $arr_col_func);

			$arr_text = array("","      •  การบริหารการเปลี่ยนแปลง (Managing Change)","$CA_SCORE_1");
			$a = $pdf->add_lineFreeTab($line_h, $arr_text, $arr_align, $arr_border, $arr_font, $arr_width, $arr_col_func);

			$arr_text = array("","      •  การมีจิตมุ่งบริการ (Customer Service Orientation)","$CA_SCORE_2");
			$a = $pdf->add_lineFreeTab($line_h, $arr_text, $arr_align, $arr_border, $arr_font, $arr_width, $arr_col_func);

			$arr_text = array("","      •  การวางแผนเชิงกลยุทธ์ (Strategic Planning)","$CA_SCORE_3");
			$a = $pdf->add_lineFreeTab($line_h, $arr_text, $arr_align, $arr_border, $arr_font, $arr_width, $arr_col_func);

			$arr_text = array("","2. การบริหารอย่างมืออาชีพ","");
			$a = $pdf->add_lineFreeTab($line_h, $arr_text, $arr_align, $arr_border, $arr_font, $arr_width, $arr_col_func);

			$arr_text = array("","      •  การตัดสินใจ (Decision Making)","$CA_SCORE_4");
			$a = $pdf->add_lineFreeTab($line_h, $arr_text, $arr_align, $arr_border, $arr_font, $arr_width, $arr_col_func);

			$arr_text = array("","      •  การคิดเชิงกลยุทธ์ (Strategic Thinking)","$CA_SCORE_5");
			$a = $pdf->add_lineFreeTab($line_h, $arr_text, $arr_align, $arr_border, $arr_font, $arr_width, $arr_col_func);

			$arr_text = array("","      •  ความเป็นผู้นำ (Leadership)","$CA_SCORE_6");
			$a = $pdf->add_lineFreeTab($line_h, $arr_text, $arr_align, $arr_border, $arr_font, $arr_width, $arr_col_func);

			$arr_text = array("","3. การบริหารคน","");
			$a = $pdf->add_lineFreeTab($line_h, $arr_text, $arr_align, $arr_border, $arr_font, $arr_width, $arr_col_func);

			$arr_text = array("","      •  การปรับตัวและความยืดหยุ่น (Adaptability and Flexibility)","$CA_SCORE_7");
			$a = $pdf->add_lineFreeTab($line_h, $arr_text, $arr_align, $arr_border, $arr_font, $arr_width, $arr_col_func);

			$arr_text = array("","      •  ความสามารถและทักษะในการสื่อสาร (Communication)","$CA_SCORE_8");
			$a = $pdf->add_lineFreeTab($line_h, $arr_text, $arr_align, $arr_border, $arr_font, $arr_width, $arr_col_func);
			
			$arr_text = array("","      •  การประสานสัมพันธ์ (Collaborative)","$CA_SCORE_9");
			$a = $pdf->add_lineFreeTab($line_h, $arr_text, $arr_align, $arr_border, $arr_font, $arr_width, $arr_col_func);

			$arr_text = array("","4. การบริหารแบบมุ่งผลสัมฤทธิ์","");
			$a = $pdf->add_lineFreeTab($line_h, $arr_text, $arr_align, $arr_border, $arr_font, $arr_width);

			$arr_text = array("","      •  การรับผิดชอบตรวจสอบได้ (Accountability)","$CA_SCORE_10");
			$a = $pdf->add_lineFreeTab($line_h, $arr_text, $arr_align, $arr_border, $arr_font, $arr_width, $arr_col_func);

			$arr_text = array("","      •  การทำงานให้บรรลุผลสัมฤทธิ์ (Achieving Result)","$CA_SCORE_11");
			$a = $pdf->add_lineFreeTab($line_h, $arr_text, $arr_align, $arr_border, $arr_font, $arr_width, $arr_col_func);

			$arr_text = array("","      •  การบริหารทรัพยากร (Managing Resources)","$CA_SCORE_12");
			$a = $pdf->add_lineFreeTab($line_h, $arr_text, $arr_align, $arr_border, $arr_font, $arr_width, $arr_col_func);

			$arr_font = array("","$font|b|16|AAAAAA|555555","$font|b|16|AAAAAA|555555");
			$arr_text = array("","ผลเฉลี่ยการประเมินสมรรถนะหลักทางการบริหาร","$CA_MEAN");
			$arr_col_func = array(($NUMBER_DISPLAY==2)?"TNUM":"ENUM",($NUMBER_DISPLAY==2)?"TNUM":"ENUM",($NUMBER_DISPLAY==2)?"TNUM-2":"ENUM-2");
			$a = $pdf->add_lineFreeTab($line_h, $arr_text, $arr_align, $arr_border, $arr_font, $arr_width, $arr_col_func);

			$pdf->SetFont($font,'',16);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

			$pdf->Cell(200 ,7,"","",1,"L");
			$arr_text = array("","ลงชื่อ ","                                        ");
			$arr_align = array("L","R","R");
			$arr_border = array("","","");
			$arr_font = array("","$font|b|16","$font|U|16");
			$arr_width = array(135,65,"-");
			$a = $pdf->add_lineFreeTab($line_h, $arr_text, $arr_align, $arr_border, $arr_font, $arr_width);
			$pdf->SetFont($font,'',16);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
			$pdf->Cell(200 ,7,"เจ้าหน้าที่ตรวจทานผลการประเมินฯ","",1,"R");
			$arr_text = array("","วันที่ ","                            ");
			$a = $pdf->add_lineFreeTab($line_h, $arr_text, $arr_align, $arr_border, $arr_font, $arr_width);
			
			$arr_text = array("หมายเหตุ"," ผลการประเมินสมรรถนะหลักทางการบริหารใช้ได้ 2 ปี นับแต่$FOOT_01_1");
			$arr_align = array("L","L");
			$arr_border = array("","");
			$arr_font = array("$font|bU|16","$font||16");
			$a = $pdf->add_lineFreeTab($line_h, $arr_text, $arr_align, $arr_border, $arr_font);
		} // end while
	}else{
		$pdf->SetFont($font,'b',20);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(200,7,"********** ไม่มีข้อมูล **********",0,1,"C");
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
			$arr_column_map[] = $i;		// link index ของ head 
			$arr_column_sel[] = 1;			// 1=แสดง	0=ไม่แสดง   ถ้าไม่มีการปรับแต่งรายงาน ก็เป็นแสดงหมด
		}
		$arr_column_width = $heading_width;	// ความกว้าง
		$arr_column_align = $data_align;		// align
		$COLUMN_FORMAT = implode(",",$arr_column_map)."|".implode(",",$arr_column_sel)."|".implode(",",$arr_column_width)."|".implode(",",$arr_column_align);
		
		return $COLUMN_FORMAT;
	}
?>