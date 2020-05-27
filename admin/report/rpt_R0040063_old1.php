<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php");

	ini_set("max_execution_time", $max_execution_time);
	
	$IMG_PATH = "../../attachment/pic_personal/";
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	// sort หัวข้อที่เลือก เพื่อจัดลำดับการแสดงก่อนหลัง ของ $arr_history_name ที่เลือกเข้ามา
	$arr_history_sort = array("DECORATEHIS", "ABSENTSUM", "ABILITY", "SERVICEHIS", "SPECIALSKILLHIS", "EDUCATE", "PUNISHMENT", "NOTPAID", "POSITIONHIS", "KPI"); 
	// sort หัวข้อที่เลือก
	
	if (!$HISTORY_LIST) {
		$arr_history_name = $arr_history_sort;
	} else {
		$arr_history_name = explode("|", $HISTORY_LIST);
	}
	$search_condition = "";
	$arr_search_condition[] = "(a.PER_TYPE in (". implode(", ", $search_per_type) ."))";

  	if($DEPARTMENT_ID){
		$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
	}elseif($MINISTRY_ID){
		unset($arr_department);
		$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID and OL_CODE='02' ";
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_department[] = $data[ORG_ID];
		$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode(",", $arr_department) ."))";
	}elseif($PROVINCE_CODE){
		$cmd = " select ORG_ID from PER_ORG where PV_CODE='$PROVINCE_CODE' and OL_CODE='02' ";
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_department[] = $data[ORG_ID];
		$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode(",", $arr_department) ."))";
	} // end if

//	if(!trim($select_org_structure)){
		if($select_org_structure==1){	//สำหรับมอบหมายงาน
			if(trim($search_org_ass_id)){	$search_org_id=trim($search_org_ass_id);		}	
			if(trim($search_org_ass_id_1)){	$search_org_id_1=trim($search_org_ass_id_1);		}
			if(trim($search_org_ass_id_2)){	$search_org_id_2=trim($search_org_ass_id_2);		}
		}
		if(trim($search_org_id)) $arr_search_condition[] = "(c.ORG_ID = $search_org_id or d.ORG_ID = $search_org_id)";
		if(trim($search_org_id_1)) $arr_search_condition[] = "(c.ORG_ID_1 = $search_org_id_1 or d.ORG_ID_1 = $search_org_id_1)";
		if(trim($search_org_id_2)) $arr_search_condition[] = "(c.ORG_ID_2 = $search_org_id_2 or d.ORG_ID_2 = $search_org_id_2)";
//	}
		
	if($list_type == "SELECT"){
		$arr_search_condition[] = "(a.PER_ID in ($SELECTED_PER_ID))";
	}elseif($list_type == "CONDITION"){
		if(trim($search_pos_no))  {	
			if ($search_per_type == 1 || $search_per_type==5)
				$arr_search_condition[] = "(trim(POS_NO) = '$search_pos_no')";
			elseif ($search_per_type == 2) 
				$arr_search_condition[] = "(trim(POEM_NO) = '$search_pos_no')";		
			elseif ($search_per_type == 3) 	
				$arr_search_condition[] = "(trim(POEMS_NO) = '$search_pos_no')";
			elseif ($search_per_type == 4) 	
				$arr_search_condition[] = "(trim(POT_NO) = '$search_pos_no')";
			else if ($search_per_type==0)		//ทั้งหมด
				$arr_search_condition[] = "((trim(POS_NO) = '$search_pos_no') or (trim(POEM_NO) = '$search_pos_no') or (trim(POEMS_NO) = '$search_pos_no') or (trim(POT_NO) = '$search_pos_no')) ";
		}
		if(trim($search_pos_no_name)){
			if ($search_per_type == 1 || $search_per_type==5)
				$arr_search_condition[] = "(trim(POS_NO_NAME) = '$search_pos_no_name')";
			elseif ($search_per_type == 2) 
				$arr_search_condition[] = "(trim(POEM_NO_NAME) = '$search_pos_no_name')";		
			elseif ($search_per_type == 3) 	
				$arr_search_condition[] = "(trim(POEMS_NO_NAME) = '$search_pos_no_name')";
			elseif ($search_per_type == 4) 	
				$arr_search_condition[] = "(trim(POT_NO_NAME) = '$search_pos_no_name')";
			else if ($search_per_type==0)		//ทั้งหมด
				$arr_search_condition[] = "((trim(POS_NO_NAME) = '$search_pos_no_name') or (trim(POEM_NO_NAME) = '$search_pos_no_name') or (trim(POEMS_NO_NAME) = '$search_pos_no_name') or (trim(POT_NO_NAME) = '$search_pos_no_name')) ";
		}
		if(trim($search_name)) $arr_search_condition[] = "(a.PER_NAME like '$search_name%')";
		if(trim($search_surname)) $arr_search_condition[] = "(a.PER_SURNAME like '$search_surname%')";
		if(trim($search_min_level)){ 
			if($DPISDB=="odbc") $arr_search_condition[] = "(trim(a.LEVEL_NO) >= '". str_pad($search_min_level, 2, "0", STR_PAD_LEFT) ."')";
			if($DPISDB=="oci8") $arr_search_condition[] = "(trim(a.LEVEL_NO) >= '". str_pad($search_min_level, 2, "0", STR_PAD_LEFT) ."')";
			if($DPISDB=="mysql") $arr_search_condition[] = "(trim(a.LEVEL_NO) >= '". str_pad($search_min_level, 2, "0", STR_PAD_LEFT) ."')";
		}
		if(trim($search_max_level)){ 
			if($DPISDB=="odbc") $arr_search_condition[] = "(trim(a.LEVEL_NO) <= '". str_pad($search_max_level, 2, "0", STR_PAD_LEFT) ."')";
			if($DPISDB=="oci8") $arr_search_condition[] = "(trim(a.LEVEL_NO) <= '". str_pad($search_max_level, 2, "0", STR_PAD_LEFT) ."')";
			if($DPISDB=="mysql") $arr_search_condition[] = "(trim(a.LEVEL_NO) <= '". str_pad($search_max_level, 2, "0", STR_PAD_LEFT) ."')";
		}
		$arr_search_condition[] = "(a.PER_STATUS in (". implode(",", $search_per_status) ."))";
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);
	
	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "";
	$report_title = "";
	$report_code = "R04063";
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
	
	$page_start_x = $pdf->x;	  $page_start_y = $pdf->y;

	$heading_width[EDUCATE][0] = "40";
	$heading_width[EDUCATE][1] = "20";
	$heading_width[EDUCATE][2] = "35";
	$heading_width[EDUCATE][3] = "35";
	$heading_width[EDUCATE][4] = "25";
	$heading_width[EDUCATE][5] = "45";
	
	$heading_text[EDUCATE][0] = "สถานศึกษา";
	$heading_text[EDUCATE][1] = "ตั้งแต่ - ถึง";
	$heading_text[EDUCATE][2] = "วุฒิที่ได้รับ/สาขา";
	$heading_text[EDUCATE][3] = "สถานที่";
	$heading_text[EDUCATE][4] = "ตั้งแต่ - ถึง";
	$heading_text[EDUCATE][5] = "หลักสูตร/รุ่นที่";

	$heading_align[EDUCATE] = array('C', 'C', 'C', 'C', 'C', 'C');
	
	// หัวของกรณี $BKK_FLAG==1
	$heading_bkk1_width[EDUCATE][0] = "70";
	$heading_bkk1_width[EDUCATE][1] = "50";
	$heading_bkk1_width[EDUCATE][2] = "80";
	$heading_bkk2_width[EDUCATE][0] = "70";
	$heading_bkk2_width[EDUCATE][1] = "50";
	$heading_bkk2_width[EDUCATE][2] = "80";
	
	$heading_bkk1_text[EDUCATE][0] = "สถานศึกษา";
	$heading_bkk1_text[EDUCATE][1] = "ตั้งแต่ - ถึง";
	$heading_bkk1_text[EDUCATE][2] = "วุฒิที่ได้รับ/สาขา";
	$heading_bkk2_text[EDUCATE][0] = "สถานที่";
	$heading_bkk2_text[EDUCATE][1] = "ตั้งแต่ - ถึง";
	$heading_bkk2_text[EDUCATE][2] = "หลักสูตร/รุ่นที่";

	$heading_bkk1_align[EDUCATE] = array('C', 'C', 'C');
	$heading_bkk2_align[EDUCATE] = array('C', 'C', 'C');
	// end หัวของกรณี $BKK_FLAG==1

	if ($BKK_FLAG==1) {
		$heading_width[POSITIONHIS][0] = "20";
		$heading_width[POSITIONHIS][1] = "70";      
		$heading_width[POSITIONHIS][2] = "16";
		$heading_width[POSITIONHIS][3] = "16";
		$heading_width[POSITIONHIS][4] = "16";
		$heading_width[POSITIONHIS][5] = "62";
		$date_format = 2;
	} else {
		$heading_width[POSITIONHIS][0] = "30";
		$heading_width[POSITIONHIS][1] = "90";      
		$heading_width[POSITIONHIS][2] = "16";
		$heading_width[POSITIONHIS][3] = "16";
		$heading_width[POSITIONHIS][4] = "16";
		$heading_width[POSITIONHIS][5] = "32";
		$date_format = 3;
	}

	$heading_text[POSITIONHIS][0] = "วัน เดือน ปี|";
	$heading_text[POSITIONHIS][1] = "ตำแหน่ง|";
	$heading_text[POSITIONHIS][2] = "เลขที่|ตำแหน่ง";
	$heading_text[POSITIONHIS][3] = "ระดับ|";
	$heading_text[POSITIONHIS][4] = "อัตรา|เงินเดือน";
	$heading_text[POSITIONHIS][5] = "เอกสารอ้างอิง|$SESS_USERID";
			
	$heading_align[POSITIONHIS] = array('C', 'C', 'C', 'C', 'C', 'C');

	$heading_width[PUNISHMENT][0] = "45";
	$heading_width[PUNISHMENT][1] = "110";
	$heading_width[PUNISHMENT][2] = "45";

	$heading_text[PUNISHMENT][0] = "พ.ศ.";
	$heading_text[PUNISHMENT][1] = "รายการ";
	$heading_text[PUNISHMENT][2] = "เอกสารอ้างอิง";
	
	$heading_align[PUNISHMENT] = array('C', 'C', 'C');

	$heading_width[NOTPAID][0] = "45";
	$heading_width[NOTPAID][1] = "110";
	$heading_width[NOTPAID][2] = "45";
	
	$heading_text[NOTPAID][0] = "ตั้งแต่-ถึง";
	$heading_text[NOTPAID][1] = "รายการ";
	$heading_text[NOTPAID][2] = "เอกสารอ้างอิง";

	$heading_align[NOTPAID] = array('C', 'C', 'C');
	
	$heading_width[ABILITY][0] = "100";
	$heading_width[ABILITY][1] = "100";
	
	$heading_text[ABILITY][0] = "ด้านความสามารถพิเศษ";
	$heading_text[ABILITY][1] = "ความสามารถพิเศษ";
	
	$heading_align[ABILITY] = array('C', 'C');
	
	$heading_width[SERVICEHIS][0] = "20";
	$heading_width[SERVICEHIS][1] = "80";
	$heading_width[SERVICEHIS][2] = "20";
	$heading_width[SERVICEHIS][3] = "80";

	$heading_text[SERVICEHIS][0] = "พ.ศ.";
	$heading_text[SERVICEHIS][1] = "รายการ";
	$heading_text[SERVICEHIS][2] = "พ.ศ.";
	$heading_text[SERVICEHIS][3] = "รายการ";
//	$heading_text[SERVICEHIS][1] = "ประเภทราชการพิเศษ";
//	$heading_text[SERVICEHIS][2] = "หัวข้อราชการพิเศษ";
//	$heading_text[SERVICEHIS][3] = "หนังสือ";

	$heading_align[SERVICEHIS] = array('C', 'C', 'C', 'C');

	$heading_width[SPECIALSKILLHIS][0] = "80";
	$heading_width[SPECIALSKILLHIS][1] = "120";

	$heading_text[SPECIALSKILLHIS][0] = "ด้านความเชี่ยวชาญพิเศษ";
	$heading_text[SPECIALSKILLHIS][1] = "เน้นทาง";

	$heading_align[SPECIALSKILLHIS] = array('C', 'C');

	$heading_width[DECORATEHIS][0] = "50";
	$heading_width[DECORATEHIS][1] = "54";
	$heading_width[DECORATEHIS][2] = "32";
	$heading_width[DECORATEHIS][3] = "32";
	$heading_width[DECORATEHIS][4] = "32";

	$heading_text[DECORATEHIS][0] = "(1)|ตำแหน่ง (อดีต-ปัจจุบัน|เฉพาะปีที่ได้รับ|พระราชทานเครื่องราชฯ)";
	$heading_text[DECORATEHIS][1] = "(2)|ลำดับเครื่องราชฯ ที่ได้รับ|พระราชทานแล้ว|จากชั้นรองไปชั้นสูงตาม (1)";
	$heading_text[DECORATEHIS][2] = "(3)|ว.ด.ป.|ที่ได้รับ|พระราชทานตาม (2)";
	$heading_text[DECORATEHIS][3] = "(4)|เครื่องราชฯ|ตาม (2)|รับมอบเมื่อ ";
	$heading_text[DECORATEHIS][4] = "(5)|เครื่องราชฯ|ตาม (2)|ส่งคืนเมื่อ ";

	$heading_align[DECORATEHIS] = array('C', 'C', 'C', 'C', 'C');

	$heading_width[ABSENTSUM][0] = "20";
	$heading_width[ABSENTSUM][1] = "16";
	$heading_width[ABSENTSUM][2] = "16";
	$heading_width[ABSENTSUM][3] = "16";
	$heading_width[ABSENTSUM][4] = "16";
	$heading_width[ABSENTSUM][5] = "16";
	$heading_width[ABSENTSUM][6] = "20";
	$heading_width[ABSENTSUM][7] = "16";
	$heading_width[ABSENTSUM][8] = "16";
	$heading_width[ABSENTSUM][9] = "16";
	$heading_width[ABSENTSUM][10] = "16";
	$heading_width[ABSENTSUM][11] = "16";

	$heading_text[ABSENTSUM][0] = "|พ.ศ.|";
	$heading_text[ABSENTSUM][1] = "|ลาป่วย|";
	$heading_text[ABSENTSUM][2] = "ลากิจ|และ|พักผ่อน";
	$heading_text[ABSENTSUM][3] = "|สาย|";
	$heading_text[ABSENTSUM][4] = "ขาด|ราช|การ";
	$heading_text[ABSENTSUM][5] = "ลา|ศึกษา|ต่อ";
	$heading_text[ABSENTSUM][6] = "|พ.ศ.|";
	$heading_text[ABSENTSUM][7] = "|ลาป่วย|";
	$heading_text[ABSENTSUM][8] = "ลากิจ|และ|พักผ่อน";
	$heading_text[ABSENTSUM][9] = "|สาย|";
	$heading_text[ABSENTSUM][10] = "ขาด|ราช|การ";
	$heading_text[ABSENTSUM][11] = "ลา|ศึกษา|ต่อ";
	
	$heading_align[ABSENTSUM] = array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C');

	$heading_width[KPI][0] = "20";
	$heading_width[KPI][1] = "30";
	$heading_width[KPI][2] = "30";
	$heading_width[KPI][3] = "30";
	$heading_width[KPI][4] = "30";
	$heading_width[KPI][5] = "30";
	$heading_width[KPI][6] = "30";

	$heading_text[KPI][0] = "|ปีงบประมาณ|";
	$heading_text[KPI][1] = "<**1**>ผลการประเมินครั้งที่ 1 (1 ต.ค. - 31 มี.ค. ของปีถัดไป)|ดีเด่น|90 - 100%";
	$heading_text[KPI][2] = "<**1**>ผลการประเมินครั้งที่ 1 (1 ต.ค. - 31 มี.ค. ของปีถัดไป)|เป็นที่ยอมรับได้|60 - 89%";
	$heading_text[KPI][3] = "<**1**>ผลการประเมินครั้งที่ 1 (1 ต.ค. - 31 มี.ค. ของปีถัดไป)|ต้องปรับปรุง|ต่ำกว่า 60%";
	$heading_text[KPI][4] = "<**2**>ผลการประเมินครั้งที่ 2 (1 เม.ย. - 30 ก.ย.)|ดีเด่น|90 - 100%";
	$heading_text[KPI][5] = "<**2**>ผลการประเมินครั้งที่ 2 (1 เม.ย. - 30 ก.ย.)|เป็นที่ยอมรับได้|60 - 89%";
	$heading_text[KPI][6] = "<**2**>ผลการประเมินครั้งที่ 2 (1 เม.ย. - 30 ก.ย.)|ต้องปรับปรุง|ต่ำกว่า 60%";
	
	$heading_align[KPI] = array('C', 'C', 'C', 'C', 'C', 'C', 'C');

	function print_footer($LEVEL_NAME) {
		global $pdf, $start_x, $start_y, $heading_width, $max_y, $FULLNAME, $FULL_LEVEL_NAME, $MINISTRY_NAME, $DEPARTMENT_NAME, $ORG_NAME, $page_no;
		
		$pdf->x = $start_x + $heading_width[POSITIONHIS][0] + $heading_width[POSITIONHIS][1] + $heading_width[POSITIONHIS][2] + $heading_width[POSITIONHIS][3] + $heading_width[POSITIONHIS][4] + $heading_width[POSITIONHIS][5];

		$line_start_y = $start_y;		$line_start_x = $start_x;
		$line_end_y = $max_y;		$line_end_x = $pdf->x;
		$pdf->SetDrawColor(hexdec("00"),hexdec("00"),hexdec("00")); //สีดำ
		$pdf->Line($line_start_x, $line_end_y, $line_end_x, $line_end_y);

//		$pdf->Cell(5, 3, "", 0, 1, 'C', 0); // เว้น 1 บรรทัด สูง 3

		$pdf->x = $start_x-$heading_width[0];
		$pdf->SetFont($font,'',14);

		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00")); //สีดำ

		$prt_text_footer = "ชื่อ ".$FULLNAME."..".$LEVEL_NAME."..".$MINISTRY_NAME."..".$DEPARTMENT_NAME."..".$ORG_NAME;
		$buff_footer = $pdf->thaiCutLinePDF($prt_text_footer, 168, "\n");
		$sub_footer = explode("\n", $buff_footer);	//	echo "footer---".$buff_footer."  count_line=".count($sub_footer)."<br>";
		$prt_footer_line = count($sub_footer);

		$pdf->Cell(168 ,7,$sub_footer[0],"TLR",0,"L");
		$pdf->Cell(32 ,7,"","TLR",1,"R");
		$pdf->Cell(168 ,7,$sub_footer[1],"LBR",0,"L");
		$pdf->Cell(32 ,7,"หน้า [$page_no]","LBR",1,"R");
//		$pdf->Cell(168 ,7,$prt_text_footer,1,0,"L");
//		$pdf->Cell(32 ,7,"หน้า [$page_no]",1,1,"R");
	} // function footer

	if ($BKK_FLAG==1) {
		if($DPISDB=="odbc"){
			$order_str = "order by		c.POS_NO_NAME, CLng(c.POS_NO), d.POEM_NO_NAME, CLng(d.POEM_NO), 
															e.POEMS_NO_NAME, CLng(e.POEMS_NO), g.POT_NO_NAME, CLng(g.POT_NO) ";
		}elseif($DPISDB=="oci8"){
			$order_str = "order by		c.POS_NO_NAME, to_number(c.POS_NO), d.POEM_NO_NAME, to_number(d.POEM_NO), 
															e.POEMS_NO_NAME, to_number(e.POEMS_NO), g.POT_NO_NAME, to_number(g.POT_NO) ";
		}elseif($DPISDB=="mysql"){
			$order_str = "order by		c.POS_NO_NAME, c.POS_NO+0, d.POEM_NO_NAME, d.POEM_NO+0, e.POEMS_NO_NAME, e.POEMS_NO+0, g.POT_NO_NAME, g.POT_NO+0 ";
		}
	} else {
		$order_str = "order by		a.PER_NAME, a.PER_SURNAME ";
	}


	if($DPISDB=="odbc"){
		$cmd = " select			a.PER_ID, a.LEVEL_NO, f.LEVEL_NAME, b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_BIRTHDATE, 
											a.PER_SALARY, a.PER_MGTSALARY, a.PER_STARTDATE, a.PER_RETIREDATE,a.PER_OCCUPYDATE,
											a.PER_TYPE, a.PER_CARDNO,a.POS_ID, a.POEM_ID, a.POEMS_ID,a.POT_ID, a.DEPARTMENT_ID,
											a.OT_CODE,a.PN_CODE_F, a.PER_FATHERNAME, a.PER_FATHERSURNAME, a.PN_CODE_M, a.PER_MOTHERNAME, 
											a.PER_MOTHERSURNAME, c.POS_NO_NAME, c.POS_NO, c.PL_CODE, c.PT_CODE, c.ORG_ID, c.ORG_ID_1, c.ORG_ID_2,
											d.POEM_NO_NAME as EMP_POS_NO_NAME, d.POEM_NO as EMP_POS_NO, d.PN_CODE as EMP_PL_CODE, 
											d.ORG_ID as EMP_ORG_ID, d.ORG_ID_1 as EMP_ORG_ID_1, d.ORG_ID_2 as EMP_ORG_ID_2,
											e.POEMS_NO_NAME as EMPSER_POS_NO_NAME, e.POEMS_NO as EMPSER_POS_NO, e.EP_CODE as EMPSER_PL_CODE, 
											e.ORG_ID as EMPSER_ORG_ID, e.ORG_ID_1 as EMPSER_ORG_ID_1, e.ORG_ID_2 as EMPSER_ORG_ID_2, 
											a.PER_OFFNO, a.PV_CODE, a.PER_ADD1, a.PER_ADD2, 
											g.POT_NO_NAME as TEMP_POS_NO_NAME, g.POT_NO as TEMP_POS_NO, g.TP_CODE as TEMP_PL_CODE, 
											g.ORG_ID as TEMP_ORG_ID, g.ORG_ID_1 as TEMP_ORG_ID_1, g.ORG_ID_2 as TEMP_ORG_ID_2
				 		from		PER_PRENAME b inner join 
				 				(	
									(
										(
											( 	
												(
													PER_PERSONAL a 
													left join PER_POSITION c on (a.POS_ID=c.POS_ID) 
												) left join PER_POS_EMP d on (a.POEM_ID=d.POEM_ID)
											) left join PER_POS_EMPSER e on (a.POEMS_ID=e.POEMS_ID)
										) left join PER_LEVEL f on (a.LEVEL_NO=f.LEVEL_NO)
									) left join PER_POS_TEMP g on (a.POT_ID=g.POT_ID)	
									) on (trim(a.PN_CODE)=trim(b.PN_CODE))
						$search_condition
				 		$order_str ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = "select			a.PER_ID, a.LEVEL_NO, f.LEVEL_NAME, b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_BIRTHDATE, 
											a.PER_SALARY, a.PER_MGTSALARY, a.PER_STARTDATE, a.PER_RETIREDATE,a.PER_OCCUPYDATE,
											a.PER_TYPE, a.PER_CARDNO,a.POS_ID, a.POEM_ID, a.POEMS_ID,a.POT_ID, a.DEPARTMENT_ID,
											a.OT_CODE,a.PN_CODE_F, a.PER_FATHERNAME, a.PER_FATHERSURNAME, a.PN_CODE_M, a.PER_MOTHERNAME, 
											a.PER_MOTHERSURNAME, c.POS_NO_NAME, c.POS_NO, c.PL_CODE, c.PT_CODE, c.ORG_ID, c.ORG_ID_1, c.ORG_ID_2,
											d.POEM_NO_NAME as EMP_POS_NO_NAME, d.POEM_NO as EMP_POS_NO, d.PN_CODE as EMP_PL_CODE, 
											d.ORG_ID as EMP_ORG_ID, d.ORG_ID_1 as EMP_ORG_ID_1, d.ORG_ID_2 as EMP_ORG_ID_2,
											e.POEMS_NO_NAME as EMPSER_POS_NO_NAME, e.POEMS_NO as EMPSER_POS_NO, e.EP_CODE as EMPSER_PL_CODE, 
											e.ORG_ID as EMPSER_ORG_ID, e.ORG_ID_1 as EMPSER_ORG_ID_1, e.ORG_ID_2 as EMPSER_ORG_ID_2, 
											a.PER_OFFNO, a.PV_CODE, a.PER_ADD1, a.PER_ADD2, 
											g.POT_NO_NAME as TEMP_POS_NO_NAME, g.POT_NO as TEMP_POS_NO, g.TP_CODE as TEMP_PL_CODE, 
											g.ORG_ID as TEMP_ORG_ID, g.ORG_ID_1 as TEMP_ORG_ID_1, g.ORG_ID_2 as TEMP_ORG_ID_2
						from			PER_PERSONAL a, PER_PRENAME b, PER_POSITION c, PER_POS_EMP d, PER_POS_EMPSER e, PER_LEVEL f,PER_POS_TEMP g
						where		trim(a.PN_CODE)=trim(b.PN_CODE) and 
											a.POS_ID=c.POS_ID(+) and a.POEM_ID=d.POEM_ID(+) and a.POEMS_ID=e.POEMS_ID(+) and a.POT_ID=g.POT_ID(+) and a.LEVEL_NO=f.LEVEL_NO(+)
											$search_condition
						$order_str ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			a.PER_ID, a.LEVEL_NO, f.LEVEL_NAME, b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_BIRTHDATE, 
											a.PER_SALARY, a.PER_MGTSALARY, a.PER_STARTDATE, a.PER_RETIREDATE,a.PER_OCCUPYDATE,
											a.PER_TYPE, a.PER_CARDNO,a.POS_ID, a.POEM_ID, a.POEMS_ID,a.POT_ID, a.DEPARTMENT_ID,
											a.OT_CODE,a.PN_CODE_F, a.PER_FATHERNAME, a.PER_FATHERSURNAME, a.PN_CODE_M, a.PER_MOTHERNAME, 
											a.PER_MOTHERSURNAME, c.POS_NO_NAME, c.POS_NO, c.PL_CODE, c.PT_CODE, c.ORG_ID, c.ORG_ID_1, c.ORG_ID_2,
											d.POEM_NO_NAME as EMP_POS_NO_NAME, d.POEM_NO as EMP_POS_NO, d.PN_CODE as EMP_PL_CODE, 
											d.ORG_ID as EMP_ORG_ID, d.ORG_ID_1 as EMP_ORG_ID_1, d.ORG_ID_2 as EMP_ORG_ID_2,
											e.POEMS_NO_NAME as EMPSER_POS_NO_NAME, e.POEMS_NO as EMPSER_POS_NO, e.EP_CODE as EMPSER_PL_CODE, 
											e.ORG_ID as EMPSER_ORG_ID, e.ORG_ID_1 as EMPSER_ORG_ID_1, e.ORG_ID_2 as EMPSER_ORG_ID_2, 
											a.PER_OFFNO, a.PV_CODE, a.PER_ADD1, a.PER_ADD2, 
											g.POT_NO_NAME as TEMP_POS_NO_NAME, g.POT_NO as TEMP_POS_NO, g.TP_CODE as TEMP_PL_CODE, 
											g.ORG_ID as TEMP_ORG_ID, g.ORG_ID_1 as TEMP_ORG_ID_1, g.ORG_ID_2 as TEMP_ORG_ID_2
					 	from		PER_PERSONAL a inner join PER_PRENAME b on (trim(a.PN_CODE)=trim(b.PN_CODE))
																  left join PER_POSITION c on (a.POS_ID=c.POS_ID) 
																  left join PER_POS_EMP d on (a.POEM_ID=d.POEM_ID)
																  left join PER_POS_EMPSER e on (a.POEMS_ID=e.POEMS_ID)
																  left join PER_LEVEL f on (a.LEVEL_NO=f.LEVEL_NO)
																  left join PER_POS_TEMP g on (a.POT_ID=g.POT_ID)
						$search_condition
				 		$order_str ";
	}
//	echo "$cmd<\br>";
	$count_data = $db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();

	if($count_data){
		$data_count = 0;
		while($data = $db_dpis->get_array()){
			$ARRAY_POH_SAH = (array) null;
			$data_count++;
			$PER_ID = $data[PER_ID];
			$PER_TYPE = $data[PER_TYPE];
			$PER_OFFNO = $data[PER_OFFNO];
			
			if($PER_TYPE==1){
				$POS_ID = $data[POS_ID];
				$POS_NO = $data[POS_NO_NAME].$data[POS_NO];
				$PL_CODE = trim($data[PL_CODE]);
				$PT_CODE = trim($data[PT_CODE]);
				$TMP_DEPARTMENT_ID = $data[DEPARTMENT_ID];
				$ORG_ID = $data[ORG_ID];
				$ORG_ID_1 = $data[ORG_ID_1];
				$ORG_ID_2 = $data[ORG_ID_2];

				$cmd = " select PL_NAME from PER_LINE where trim(PL_CODE) = '$PL_CODE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PL_NAME = trim($data2[PL_NAME]);

				$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE) = '$PT_CODE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PT_NAME = trim($data2[PT_NAME]);
			}elseif($PER_TYPE==2){
				$POS_ID = $data[POEM_ID];
				$POS_NO = $data[EMP_POS_NO_NAME].$data[EMP_POS_NO];
				$PL_CODE = trim($data[EMP_PL_CODE]);
				$ORG_ID = $data[EMP_ORG_ID];
				$ORG_ID_1 = $data[EMP_ORG_ID_1];
				$ORG_ID_2 = $data[EMP_ORG_ID_2];

				$cmd = " select PN_NAME from PER_POS_NAME where trim(PN_CODE) = '$PL_CODE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PL_NAME = trim($data2[PN_NAME]);
			}elseif($PER_TYPE==3){
				$POS_ID = $data[POEMS_ID];
				$POS_NO = $data[EMPSER_POS_NO_NAME].$data[EMPSER_POS_NO];
				$PL_CODE = trim($data[EMPSER_PL_CODE]);
				$ORG_ID = $data[EMPSER_ORG_ID];
				$ORG_ID_1 = $data[EMPSER_ORG_ID_1];
				$ORG_ID_2 = $data[EMPSER_ORG_ID_2];

				$cmd = " select EP_NAME from PER_EMPSER_POS_NAME where trim(EP_CODE) = '$PL_CODE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PL_NAME = trim($data2[EP_NAME]);
			}elseif($PER_TYPE==4){
				$POS_ID = $data[POT_ID];
				$POS_NO = $data[TEMP_POS_NO_NAME].$data[TEMP_POS_NO];
				$PL_CODE = trim($data[TEMP_PL_CODE]);
				$ORG_ID = $data[TEMP_ORG_ID];
				$ORG_ID_1 = $data[TEMP_ORG_ID_1];
				$ORG_ID_2 = $data[TEMP_ORG_ID_2];

				$cmd = " select TP_NAME from PER_TEMP_POS_NAME where trim(TP_CODE) = '$PL_CODE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PL_NAME = trim($data2[TP_NAME]);
			} 

			// ข้อมูลประเภทข้าราชการ
			$OT_CODE = trim($data[OT_CODE]);
			$cmd = " select OT_NAME from PER_OFF_TYPE where trim(OT_CODE)='$OT_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$OT_NAME = trim($data_dpis2[OT_NAME]);

			$cmd = " select ORG_NAME, ORG_ID_REF from PER_ORG where ORG_ID = $TMP_DEPARTMENT_ID ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$DEPARTMENT_NAME = trim($data2[ORG_NAME]);
			$ORG_ID_REF = $data2[ORG_ID_REF];
			
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $ORG_ID_REF ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$MINISTRY_NAME = trim($data2[ORG_NAME]);
			
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $ORG_ID ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$ORG_NAME = trim($data2[ORG_NAME]);
			if ($ORG_NAME=="-") $ORG_NAME = "";
			
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $ORG_ID_1 ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$ORG_NAME_1 = trim($data2[ORG_NAME]);
			if ($ORG_NAME_1=="-") $ORG_NAME_1 = "";

			$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $ORG_ID_2 ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$ORG_NAME_2 = trim($data2[ORG_NAME]);
			if ($ORG_NAME_2=="-") $ORG_NAME_2 = "";

			$PV_CODE = trim($data[PV_CODE]);
			$cmd = " select PV_NAME from PER_PROVINCE where PV_CODE = $PV_CODE ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PV_NAME = trim($data2[PV_NAME]);
			$PER_ADD1 = trim($data[PER_ADD1]);

			$cmd = " select a.*, b.AP_NAME, c.PV_NAME 
							from PER_ADDRESS a left join PER_AMPHUR b on (a.AP_CODE=b.AP_CODE) left join PER_PROVINCE c on (a.PV_CODE=c.PV_CODE) 
							where PER_ID = $PER_ID and ADR_TYPE=1 ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();

				$AP_CODE_ADR = trim($data2[AP_CODE]);
				$cmd = " select AP_NAME from PER_AMPHUR where trim(AP_CODE)='$AP_CODE_ADR' ";
				$db_dpis3->send_cmd($cmd);
				$data3 = $db_dpis3->get_array();
				$AP_NAME_ADR = trim($data3[AP_NAME]);
				
				$PV_CODE_ADR = trim($data2[PV_CODE]);
				$cmd = " select PV_NAME from PER_PROVINCE where trim(PV_CODE)='$PV_CODE_ADR' ";
				$db_dpis3->send_cmd($cmd);
				$data3 = $db_dpis3->get_array();
				$PV_NAME_ADR = trim($data3[PV_NAME]);
				
			$PER_ADD1 = "";
			if($data2[ADR_VILLAGE]) $PER_ADD1 .= "หมู่บ้าน".$data2[ADR_VILLAGE]." ";
			if($data2[ADR_BUILDING]) $PER_ADD1 .= "อาคาร".$data2[ADR_BUILDING]." ";
			if($data2[ADR_NO]) $PER_ADD1 .= "เลขที่ ".$data2[ADR_NO]." ";
			if($data2[ADR_MOO]) $PER_ADD1 .= "หมู่ที่ ".$data2[ADR_MOO]." ";
			if($data2[ADR_SOI]) $PER_ADD1 .= "ซอย".$data2[ADR_SOI]." ";
			if($data2[ADR_ROAD]) $PER_ADD1 .= "ถนน".$data2[ADR_ROAD]." ";
			if($data2[ADR_DISTRICT]) $PER_ADD1 .= "ตำบล/แขวง ".$data2[ADR_DISTRICT]." ";
			if($AP_NAME_ADR) $PER_ADD1 .= "อำเภอ/เขต ".$AP_NAME_ADR." ";
			if($PV_NAME_ADR) $PER_ADD1 .= "จังหวัด ".$PV_NAME_ADR." ";
			if($data2[ADR_POSTCODE]) $PER_ADD1 .= "รหัสไปรษณีย์ ".$data2[ADR_POSTCODE]." ";
			if (!$PER_ADD1) $PER_ADD1 = trim($data[PER_ADD1]);

			$cmd = " select a.*, b.AP_NAME, c.PV_NAME 
							from PER_ADDRESS a left join PER_AMPHUR b on (a.AP_CODE=b.AP_CODE) left join PER_PROVINCE c on (a.PV_CODE=c.PV_CODE) 
							where PER_ID = $PER_ID and ADR_TYPE=2 ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();

				$AP_CODE_ADR = trim($data2[AP_CODE]);
				$cmd = " select AP_NAME from PER_AMPHUR where trim(AP_CODE)='$AP_CODE_ADR' ";
				$db_dpis3->send_cmd($cmd);
				$data3 = $db_dpis3->get_array();
				$AP_NAME_ADR = trim($data3[AP_NAME]);
				
				$PV_CODE_ADR = trim($data2[PV_CODE]);
				$cmd = " select PV_NAME from PER_PROVINCE where trim(PV_CODE)='$PV_CODE_ADR' ";
				$db_dpis3->send_cmd($cmd);
				$data3 = $db_dpis3->get_array();
				$PV_NAME_ADR = trim($data3[PV_NAME]);
				
			$PER_ADD2 = "";
			if($data2[ADR_VILLAGE]) $PER_ADD2 .= "หมู่บ้าน".$data2[ADR_VILLAGE]." ";
			if($data2[ADR_BUILDING]) $PER_ADD2 .= "อาคาร".$data2[ADR_BUILDING]." ";
			if($data2[ADR_NO]) $PER_ADD2 .= "เลขที่ ".$data2[ADR_NO]." ";
			if($data2[ADR_MOO]) $PER_ADD2 .= "หมู่ที่ ".$data2[ADR_MOO]." ";
			if($data2[ADR_SOI]) $PER_ADD2 .= "ซอย".$data2[ADR_SOI]." ";
			if($data2[ADR_ROAD]) $PER_ADD2 .= "ถนน".$data2[ADR_ROAD]." ";
			if($data2[ADR_DISTRICT]) $PER_ADD2 .= "ตำบล/แขวง ".$data2[ADR_DISTRICT]." ";
			if($AP_NAME_ADR) $PER_ADD2 .= "อำเภอ/เขต ".$AP_NAME_ADR." ";
			if($PV_NAME_ADR) $PER_ADD2 .= "จังหวัด ".$PV_NAME_ADR." ";
			if($data2[ADR_POSTCODE]) $PER_ADD2 .= "รหัสไปรษณีย์ ".$data2[ADR_POSTCODE]." ";
			if (!$PER_ADD2) $PER_ADD2 = trim($data[PER_ADD2]);

			$LEVEL_NO = trim($data[LEVEL_NO]);
			$FULL_LEVEL_NAME = trim($data[LEVEL_NAME]);
			$LEVEL_NAME = trim(str_replace("ระดับ","",$data[LEVEL_NAME]));
			$PN_NAME = trim($data[PN_NAME]);
			$PER_NAME = trim($data[PER_NAME]);
			$PER_SURNAME = trim($data[PER_SURNAME]);
			$FULLNAME = ($PN_NAME)."$PER_NAME $PER_SURNAME";			
			$PER_CARDNO = $data[PER_CARDNO];

			$arr_img = (array) null;
			$arr_imgsavedate = (array) null;
			$arr_imgshow = (array) null;
//			$img_file = "";
//			if($PER_CARDNO && file_exists($IMG_PATH.$PER_CARDNO.".jpg")) $img_file = $IMG_PATH.$PER_CARDNO.".jpg";
//			$cmd = " select * from PER_PERSONALPIC where PER_ID=$PER_ID AND PIC_SHOW = '1' ";
			$cmd = " select * from PER_PERSONALPIC where PER_ID=$PER_ID order by PER_PICSAVEDATE asc ";
//echo "IMG:$cmd<br>";
			$piccnt = $db_dpis2->send_cmd($cmd);
			if ($piccnt > 0) { 
				while ($data2 = $db_dpis2->get_array()) {
					$TMP_PER_CARDNO = trim($data2[PER_CARDNO]);
					$PER_GENNAME = trim($data2[PER_GENNAME]);
					$PIC_PATH = trim($data2[PER_PICPATH]);
					$PIC_SEQ = trim($data2[PER_PICSEQ]);
					$T_PIC_SEQ = substr("000",0,3-strlen("$PIC_SEQ"))."$PIC_SEQ";
					$PIC_SERVER_ID = trim($data2[PIC_SERVER_ID]);
					$PIC_SHOW = trim($data2[PIC_SHOW]);

					if ($PIC_SHOW == '1') {
						$img_file = $PIC_PATH.($TMP_PER_CARDNO=="NULL"?$PER_GENNAME:$TMP_PER_CARDNO)."-".$T_PIC_SEQ.".jpg";
						$arr_img[] = "../".$PIC_PATH.($TMP_PER_CARDNO=="NULL"?$PER_GENNAME:$TMP_PER_CARDNO)."-".$T_PIC_SEQ.".jpg";
//						echo "1..pic_show=$PIC_SHOW ==>".("../".$PIC_PATH.($TMP_PER_CARDNO=="NULL"?$PER_GENNAME:$TMP_PER_CARDNO)."-".$T_PIC_SEQ.".jpg")."<br>";
						$arr_imgshow[] = 1;
						$PER_PICSAVEDATE = trim($data2[PER_PICSAVEDATE]);
					} else {
						$arr_img[] = "../".$PIC_PATH.($TMP_PER_CARDNO=="NULL"?$PER_GENNAME:$TMP_PER_CARDNO)."-".$T_PIC_SEQ.".jpg";
						$arr_imgsavedate[] = trim($data2[PER_PICSAVEDATE]);
						$arr_imgshow[] = 0;
//						echo "2..pic_show=$PIC_SHOW ==>".("../".$PIC_PATH.($TMP_PER_CARDNO=="NULL"?$PER_GENNAME:$TMP_PER_CARDNO)."-".$T_PIC_SEQ.".jpg")."<br>";
					}
				} // end while loop
			} else {
				//$img_file="";
				$img_file=$IMG_PATH."shadow.jpg";
			}

			if ($PIC_SERVER_ID && $PIC_SERVER_ID > 0) {
				$cmd = " select * from OTH_SERVER where SERVER_ID=$PIC_SERVER_ID ";
				if ($db_dpis2->send_cmd($cmd)) { 
					$data2 = $db_dpis2->get_array();
					$SERVER_NAME = trim($data2[SERVER_NAME]);
					$ftp_server = trim($data2[FTP_SERVER]);
					$ftp_username = trim($data2[FTP_USERNAME]);
					$ftp_password = trim($data2[FTP_PASSWORD]);
					$main_path = trim($data2[MAIN_PATH]);
					$http_server = trim($data2[HTTP_SERVER]);
					if ($http_server) {
						//echo "1.".$http_server."/".$img_file."<br>";
						$fp = @fopen($http_server."/".$img_file, "r");
						if ($fp !== false) $img_file = $http_server."/".$img_file;
						else $img_file=$IMG_PATH."shadow.jpg";
						fclose($fp);
					} else {
//						echo "2.".$img_file."<br>";
						$img_file = file_exists("../".$img_file)?("../".$img_file):$IMG_PATH."shadow.jpg";
					}
				} else{
				 	$img_file = file_exists("../".$img_file)?("../".$img_file):$IMG_PATH."shadow.jpg";
				}
			} else{ 
				//$img_file = file_exists("../".$img_file)?("../".$img_file):$IMG_PATH."shadow.jpg";
				$img_file = file_exists("../".$img_file)?("../".$img_file):$IMG_PATH."shadow.jpg";
//				echo "../".$img_file." @@@@ ".file_exists("../".$img_file);
			}
//echo "img_file=$img_file // $PIC_SERVER_ID<br>";

			$PER_BIRTHDATE = trim($data[PER_BIRTHDATE]);
			$PER_RETIREDATE = date_adjust($PER_BIRTHDATE,'d',-1);
			$PER_RETIREDATE = date_adjust($PER_RETIREDATE,'y',60);
			if($PER_BIRTHDATE){
				$PER_BIRTHDATE = show_date_format(substr($PER_BIRTHDATE, 0, 10),3);
			} // end if
			
			if($PER_RETIREDATE){
				$PER_RETIREDATE = show_date_format(substr($PER_RETIREDATE, 0, 10),3);
			} // end if
			
			$PER_SALARY = $data[PER_SALARY];
			$PER_MGTSALARY = $data[PER_MGTSALARY];

			//วันบรรจุ
			$PER_STARTDATE = trim($data[PER_STARTDATE]);
			if($PER_STARTDATE){
				$PER_STARTDATE = show_date_format(substr($PER_STARTDATE, 0, 10),3);
			} // end if

			//วันที่เข้าส่วนราชการ
			$PER_OCCUPYDATE = trim($data[PER_OCCUPYDATE]);
			if($PER_OCCUPYDATE){
				$PER_OCCUPYDATE = show_date_format(substr($PER_OCCUPYDATE, 0, 10),3);
			} // end if
		
			// =====  ข้อมูลบิดา และมารดา =====
			$cmd = "	select	PN_CODE, FML_NAME, FML_SURNAME
								from		PER_FAMILY
								where	PER_ID=$PER_ID and FML_TYPE=1 ";	
			$db_dpis2->send_cmd($cmd);
	//		$db_dpis2->show_error();
			$data1 = $db_dpis2->get_array();		
			$PN_CODE_F = trim($data1[PN_CODE]);
			$PER_FATHERNAME = $data1[FML_NAME];
			$PER_FATHERSURNAME = $data1[FML_SURNAME];
			if (!$PER_FATHERNAME) {
				$PN_CODE_F = trim($data[PN_CODE_F]);
				$PER_FATHERNAME = trim($data[PER_FATHERNAME]);
				$PER_FATHERSURNAME = trim($data[PER_FATHERSURNAME]);
			}
			$cmd = "select PN_NAME from PER_PRENAME WHERE PN_CODE = $PN_CODE_F ORDER BY PN_CODE";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$PN_NAME_F=trim($data_dpis2[PN_NAME]);

			$FATHERNAME = ($PN_NAME_F)."$PER_FATHERNAME $PER_FATHERSURNAME";
		
			$cmd = "	select	PN_CODE, FML_NAME, FML_SURNAME
								from		PER_FAMILY
								where	PER_ID=$PER_ID and FML_TYPE=2 ";	
			$db_dpis2->send_cmd($cmd);
	//		$db_dpis2->show_error();
			$data1 = $db_dpis2->get_array();		
			$PN_CODE_M = trim($data1[PN_CODE]);
			$PER_MOTHERNAME = $data1[FML_NAME];
			$PER_MOTHERSURNAME = $data1[FML_SURNAME];
			if (!$PER_FATHERNAME) {
				$PN_CODE_M = trim($data[PN_CODE_M]);	
				$PER_MOTHERNAME = trim($data[PER_MOTHERNAME]);
				$PER_MOTHERSURNAME = trim($data[PER_MOTHERSURNAME]);
			}
			$cmd = "select PN_NAME from PER_PRENAME WHERE PN_CODE = $PN_CODE_M ORDER BY PN_CODE";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$PN_NAME_M=trim($data_dpis2[PN_NAME]);
					
			$MOTHERNAME = ($PN_NAME_M)."$PER_MOTHERNAME $PER_MOTHERSURNAME";

			// =====  ข้อมูลคู่สมรส =====
			$cmd = "	select 	MAH_NAME 		from		PER_MARRHIS 
							where	PER_ID=$PER_ID 	order by	MAH_SEQ desc ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$SHOW_SPOUSE = trim($data_dpis2[MAH_NAME]);

			if (!$SHOW_SPOUSE) {
				$cmd = "	select FML_NAME, FML_SURNAME from PER_FAMILY	where PER_ID=$PER_ID and FML_TYPE = 3 ";
				$db_dpis2->send_cmd($cmd);
				$data_dpis2 = $db_dpis2->get_array();
				$SHOW_SPOUSE = trim($data_dpis2[FML_NAME])." ".trim($data_dpis2[FML_SURNAME]);
			}
			
			// เครื่องราชฯ
			if($DPISDB=="odbc"){
				$cmd = " select			b.DC_NAME, a.DEH_DATE, a.DEH_GAZETTE, a.DEH_RECEIVE_DATE
								 from			PER_DECORATEHIS a, PER_DECORATION b
								 where		a.PER_ID=$PER_ID and a.DC_CODE=b.DC_CODE and DC_TYPE != 3
								 order by		a.DEH_DATE ";							   
			}elseif($DPISDB=="oci8"){
				$cmd = " select			b.DC_NAME, a.DEH_DATE, a.DEH_GAZETTE, a.DEH_RECEIVE_DATE
								 from			PER_DECORATEHIS a, PER_DECORATION b
								 where		a.PER_ID=$PER_ID and a.DC_CODE=b.DC_CODE and DC_TYPE != 3
								 order by		a.DEH_DATE ";							   
			}elseif($DPISDB=="mysql"){
				$cmd = " select			b.DC_NAME, a.DEH_DATE, a.DEH_GAZETTE, a.DEH_RECEIVE_DATE
								 from			PER_DECORATEHIS a, PER_DECORATION b
								 where		a.PER_ID=$PER_ID and a.DC_CODE=b.DC_CODE and DC_TYPE != 3
								 order by		a.DEH_DATE ";	
			} // end if
			$count_decoratehis = $db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			if($count_decoratehis){
				$decoratehis_count = 0;
				$DEH_SHOW = "";
				$arr_DEH_SHOW = (array) null;
				while($data2 = $db_dpis2->get_array()){
					$decoratehis_count++;
					$DC_NAME = trim($data2[DC_NAME]);
					$DEH_DATE = trim($data2[DEH_DATE]);
					if($DEH_DATE){
						$DEH_DATE = substr($DEH_DATE, 0, 10);
						$arr_temp = explode("-", $DEH_DATE);
						$DEH_YEAR1 = ($arr_temp[0] + 543);
						$DEH_DATE1 = show_date_format($DEH_DATE,3);
					} // end if	
					$DEH_GAZETTE = trim($data2[DEH_GAZETTE]);
					if($DEH_GAZETTE) $DEH_GAZETTE = "ราชกิจจานุเบกษา $DEH_GAZETTE";
					$DEH_RECEIVE_DATE = trim($data2[DEH_RECEIVE_DATE]);
					if($DEH_RECEIVE_DATE) $DEH_RECEIVE_DATE = "ลว. ".show_date_format($DEH_RECEIVE_DATE,2);
					if ($DEH_SHOW) {
						$DEH_SHOW = "$DEH_SHOW,  ปี $DEH_YEAR1 $DC_NAME";
//						$DEH_SHOW1 = "$DEH_SHOW1, $DC_NAME รับพระราชทานเมื่อ $DEH_DATE1 ";
					} else {
						$DEH_SHOW = "ปี $DEH_YEAR1 $DC_NAME";
//						$DEH_SHOW1 = "$DC_NAME รับพระราชทานเมื่อ $DEH_DATE1 ";
					}
					$arr_DEH_SHOW[] = "$DC_NAME รับพระราชทานเมื่อ $DEH_DATE1 *Enter*$DEH_GAZETTE $DEH_RECEIVE_DATE";
				} // end while
			} // end if ($count_decoratehis)
			//------------------------------------------------------------------------------------------

			//สำหรับ กพ. 7
			$ORG_NAME_1 = "";		$ORG_NAME_2 = "";
			$POH_EFFECTIVEDATE = "";	$PL_NAME = "";	$TMP_PL_NAME = "";
			$LEVEL_NO = "";	$POH_POS_NO = "";	$POH_SALARY = "";
			$SAH_EFFECTIVEDATE = "";	$SAH_SALARY = "";	$MOV_NAME = "";

			for($history_index=0; $history_index<count($arr_history_sort); $history_index++){
				if (in_array($arr_history_sort[$history_index],$arr_history_name)) {
					$HISTORY_NAME = $arr_history_sort[$history_index];
				} else {
					$HISTORY_NAME = "";
				}
				switch($HISTORY_NAME){
					case "EDUCATE" :
//						$pdf->AutoPageBreak = false;
							$pdf->AddPage();

						$pdf->SetFont($fontb,'',14);
						$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

						$pdf->Cell(200,7,"11. การได้รับโทษทางวินัย ",1,1,"C");

				//		print_header("PUNISHMENT");

						$head_text1 = implode(",", $heading_text[PUNISHMENT]);
						$head_width1 = implode(",", $heading_width[PUNISHMENT]);
						$head_align1 = implode(",", $heading_align[PUNISHMENT]);
			//			echo "$head_text1<br>";
						$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "angsa", "14", "b", "0066CC", "EEEEFF", 0);
						if (!$result) echo "****** error ****** on open table for PUNISHMENT<br>";

						$CR_NAME = "";
						$CRD_NAME = "";
						$PUN_STARTDATE = "";
						$PUN_ENDDATE = "";							
										
						if($DPISDB=="odbc"){
							$cmd = " select			c.CR_NAME, b.CRD_NAME, a.PUN_STARTDATE, a.PUN_ENDDATE
											 from			PER_PUNISHMENT a, PER_CRIME_DTL b, PER_CRIME c
											 where		a.PER_ID=$PER_ID and
																a.CRD_CODE=b.CRD_CODE and
																b.CR_CODE=c.CR_CODE
											 order by		a.PUN_STARTDATE ";							   
						}elseif($DPISDB=="oci8"){
							$cmd = " select			c.CR_NAME, b.CRD_NAME, a.PUN_STARTDATE, a.PUN_ENDDATE
											 from			PER_PUNISHMENT a, PER_CRIME_DTL b, PER_CRIME c
											 where		a.PER_ID=$PER_ID and
																a.CRD_CODE=b.CRD_CODE and
																b.CR_CODE=c.CR_CODE
											 order by		a.PUN_STARTDATE ";							   
						}elseif($DPISDB=="mysql"){
							$cmd = " select			c.CR_NAME, b.CRD_NAME, a.PUN_STARTDATE, a.PUN_ENDDATE, PUN_NO, PUN_REF_NO
											 from			PER_PUNISHMENT a, PER_CRIME_DTL b, PER_CRIME c
											 where		a.PER_ID=$PER_ID and
																a.CRD_CODE=b.CRD_CODE and
																b.CR_CODE=c.CR_CODE
											 order by		a.PUN_STARTDATE ";		
						} // end if
						$count_punishmenthis = $db_dpis2->send_cmd($cmd);
			//			$db_dpis2->show_error();
						if ($count_punishmenthis) {
							$punishmenthis_count = 0;
							while($data2 = $db_dpis2->get_array()){
								$punishmenthis_count++;
								$CR_NAME = trim($data2[CR_NAME]);
								$CRD_NAME = trim($data2[CRD_NAME]);
								$PUN_STARTDATE = show_date_format($data2[PUN_STARTDATE],3);
								$PUN_ENDDATE = show_date_format($data2[PUN_ENDDATE],3);
								$PUN_DURATION = "$PUN_STARTDATE - $PUN_ENDDATE";
								if($PUN_STARTDATE == $PUN_ENDDATE) $PUN_DURATION = "$PUN_STARTDATE";

								$PUN_NO = trim($data2[PUN_NO]);
								$PUN_REF_NO = trim($data2[PUN_REF_NO]);
								$PUN_REF = ($PUN_REF_NO ? $PUN_REF_NO : $PUN_NO);
				
								$arr_data = (array) null;
								$arr_data[] = $PUN_DURATION;
								$arr_data[] = $CRD_NAME;
								$arr_data[] = $PUN_REF;

								$data_align = array("L", "L", "L");
						
								$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "angsa", "14", "", "000000", "");
								if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
								
								$pdf->x = $start_x;			$pdf->y = $max_y;
							} // end while
						} else {
							$result = $pdf->add_text_line("********** ไม่มีข้อมูล **********", 7, "TRBL", "C", "", "14", "b", 0, 0);
							if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";
						}
						$pdf->close_tab(""); 

						if(($pdf->h - $pdf->y - 10) < 30) $pdf->AddPage();
						$pdf->SetFont($fontb,'',14);
						$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
						$pdf->Cell(200,7,"12. วันที่ไม่ได้รับเงินเดือน หรือวันที่ได้รับเงินเดือนไม่เต็ม หรือวันที่มิได้ประจำปฏิบัติหน้าที่อยู่ในเขตที่ได้มีประกาศใช้กฎอัยการศึก",1,1,"C");

						$head_text1 = implode(",", $heading_text[NOTPAID]);
						$head_width1 = implode(",", $heading_width[NOTPAID]);
						$head_align1 = implode(",", $heading_align[NOTPAID]);
			//			echo "$head_text1<br>";
						$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "angsa", "14", "b", "0066CC", "EEEEFF", "");
						if (!$result) echo "****** error ****** on open table for NOTPAID<br>";
						
						$arr_data = (array) null;
						$arr_data[] = "";
						$arr_data[] = "";
						$arr_data[] = "";

						$data_align = array("C", "C", "C");
						
						$result = $pdf->add_data_tab($arr_data, 7, "TRBL", $data_align, "angsa", "14", "", "000000", "");
						if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
						$result = $pdf->add_data_tab($arr_data, 7, "TRBL", $data_align, "angsa", "14", "", "000000", "");
						if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";

						$pdf->close_tab(""); 

						$pdf->SetFont($fontb,'',14);
						$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
						$pdf->Cell(180,7,trim($MINISTRY_NAME)." ".trim($DEPARTMENT_NAME)." ".trim($ORG_NAME)." ".trim($ORG_NAME_1)." ".trim($ORG_NAME_2),1,0,"L");
						$pdf->Cell(20,7,"ก.พ.7",1,1,"R");

						$head_text1 = ",,";	// ไม่พิมพ์ head
						$head_width1 = "65,75,60";
						$head_align1 = "C,C,C";
						$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRBL", $head_align1, "angsa", "14", "b", "0066CC", "EEEEFF", "");
						if (!$result) echo "****** error ****** on open table for Personal Information<br>";

						$arr_data[0] = "1. ชื่อ  ".$FULLNAME;
						$arr_data[1] = "5. ชื่อบิดา  ".($FATHERNAME?$FATHERNAME:"-");
						$arr_data[2] = "<&&row&&><*img*".$img_file."*img*>";

						$data_align = array("L","L","C");

						$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "angsa", "14", "", "000000", "");
						if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";

						$arr_data[0] = "2. วัน เดือน ปี เกิด  ".$PER_BIRTHDATE;
						$arr_data[1] = "6. ชื่อมารดา  ".($MOTHERNAME?$MOTHERNAME:"-");
						$arr_data[2] = "<&&row&&><*img*".$img_file."*img*>";

						$data_align = array("L","L","C");
						
						$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "angsa", "14", "", "000000", "");
						if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";

						$arr_data[0] = "3. วันครบเกษียณอายุ ".$PER_RETIREDATE;
						$arr_data[1] = "7. วันสั่งบรรจุ  ".$PER_STARTDATE;
						$arr_data[2] = "<&&row&&><*img*".$img_file."*img*>";

						$data_align = array("L","L","C");
						
						$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "angsa", "14", "", "000000", "");
						if (!$result) echo "****** error ****** add data to table at record count = 4 <br>";

						$arr_data[0] = "4. ชื่อคู่สมรส ".($SHOW_SPOUSE?$SHOW_SPOUSE:"-");
						$arr_data[1] = "8. วันเริ่มปฏิบัติราชการ  ".$PER_STARTDATE;
						$arr_data[2] = "<&&row&&><*img*".$img_file."*img*>";

						$data_align = array("L","L","C");
						
						$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "angsa", "14", "", "000000", "");
						if (!$result) echo "****** error ****** add data to table at record count = 5 <br>";

						if ($BKK_FLAG!=1)	$arr_data[0] = "    ภูมิลำเนา ".($PV_NAME?$PV_NAME:"-");
						else $arr_data[0] = "";
						$arr_data[1] = "9. ประเภทข้าราชการ ".$OT_NAME;
						$arr_data[2] = "<&&end&&><*img*".$img_file."*img*>";

						$data_align = array("L","L","C");
						
						$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "angsa", "14", "", "000000", "");
						if (!$result) echo "****** error ****** add data to table at record count = 6 <br>";

						$pdf->close_tab(""); 

						if ($BKK_FLAG==1) {
							$pdf->Cell(140 ,7,"",1,0,"C");
						} else {
							$pdf->Cell(140 ,7,"เครื่องราชอิสริยาภรณ์",1,0,"C");
						}
						$pdf->Cell(60 ,7,"เลขประจำตัวประชาชน : ".card_no_format($PER_CARDNO,1),1,1,"L");
							
						$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
						$border = "";
						if ($BKK_FLAG==1) {
							$pdf->Cell(140 ,7,"",1,0,"C");
						} else {
							$pdf->MultiCell(140, 7, $DEH_SHOW, $border, "L");
						}
						if($pdf->y > $max_y) $max_y = $pdf->y;
						$pdf->x = $start_x + 140;
						$pdf->y = $start_y;

						$pdf->MultiCell(60, 7, "เลขประจำตัวข้าราชการ : $PER_OFFNO", $border, "L");
						if($pdf->y > $max_y) $max_y = $pdf->y;
						$line_start_y = $start_y;		$line_start_x = $start_x;
						$line_end_y = $max_y;		$line_end_x = $pdf->x;
						$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);

						$line_start_y = $start_y;		$line_start_x = $start_x+140;
						$line_end_y = $max_y;		$line_end_x = $start_x+140;
						$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
						$line_start_y = $start_y;		$line_start_x = $start_x+200;
						$line_end_y = $max_y;		$line_end_x = $start_x+200;
						$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);

						$pdf->x = $start_x;
						$pdf->y = $max_y;
			
						$pdf->AutoPageBreak = false;

					// เฉพาะ EDUCATE และ TRAINING
						if(($pdf->h - $pdf->y - 10) < 30) $pdf->AddPage();
						$pdf->SetFont($fontb,'',14);
						$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
						$pdf->Cell(200,7,"10. ประวัติการศึกษา ฝึกอบรมและดูงาน",1,1,"C");

						$EDU_PERIOD="";
						$EN_NAME = "";
						$EM_NAME = "";
						$INS_NAME = "";
						$CT_NAME = "";

						if($DPISDB=="odbc"){
							$cmd = " 	select		a.EDU_STARTYEAR, a.EDU_ENDYEAR, a.EDU_INSTITUTE, b.EN_NAME, c.EM_NAME, d.INS_NAME
										from			((PER_EDUCATE a
													left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
													) left join PER_EDUCMAJOR c on (a.EM_CODE=c.EM_CODE)
													) left join PER_INSTITUTE d on (a.INS_CODE=d.INS_CODE)
										 where		a.PER_ID=$PER_ID
									 order by		a.EDU_SEQ ";							   
						}elseif($DPISDB=="oci8"){
							$cmd = " select		a.EDU_STARTYEAR, a.EDU_ENDYEAR, a.EDU_INSTITUTE, b.EN_NAME, c.EM_NAME, d.INS_NAME
									from			PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCMAJOR c, PER_INSTITUTE d
									where		a.PER_ID=$PER_ID and a.EN_CODE=b.EN_CODE(+) and 
												a.EM_CODE=c.EM_CODE(+) and a.INS_CODE=d.INS_CODE(+)
									order by		a.EDU_SEQ ";							   
						}elseif($DPISDB=="mysql"){
							$cmd = " 	select		a.EDU_STARTYEAR, a.EDU_ENDYEAR, a.EDU_INSTITUTE, b.EN_NAME, c.EM_NAME, d.INS_NAME
									from			((PER_EDUCATE a
												left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
												) left join PER_EDUCMAJOR c on (a.EM_CODE=c.EM_CODE)
												) left join PER_INSTITUTE d on (a.INS_CODE=d.INS_CODE)
									 where		a.PER_ID=$PER_ID
									 order by		a.EDU_SEQ ";			
						} // end if
						$count_educatehis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
						$arr_content = (array) null;
							$row_count = $edu_count = 0;
							while($data2 = $db_dpis2->get_array()){
								$EDU_STARTYEAR = trim($data2[EDU_STARTYEAR]);
								$EDU_ENDYEAR =  trim($data2[EDU_ENDYEAR]);
								if($EDU_STARTYEAR && $EDU_STARTYEAR != "-" && $EDU_ENDYEAR){
									$EDU_PERIOD = "$EDU_STARTYEAR - $EDU_ENDYEAR";
								}else{
									$EDU_PERIOD = "$EDU_ENDYEAR";
								}
								$arr_content[$row_count][edu_period] = $EDU_PERIOD;

								$EN_NAME = trim($data2[EN_NAME]);
								$EM_NAME = trim($data2[EM_NAME]);
								if($EM_NAME!=""){ $EM_NAME="($EM_NAME)"; }
								$INS_NAME = trim($data2[INS_NAME]);
								if (!$INS_NAME) $INS_NAME = trim($data2[EDU_INSTITUTE]);
								$arr_content[$row_count][en_name] = $EN_NAME;
								$arr_content[$row_count][em_name] = $EM_NAME;
								$arr_content[$row_count][ins_name] = $INS_NAME;
								$row_count++;
								$edu_count++;
							} // end while $data2 for EDUCATION
						
						$TRN_DURATION = "";
						$TRN_STARTDATE = "";
						$TRN_ENDDATE = "";
						$TR_NAME = "";
						$TRN_PLACE = "";
						
						if($DPISDB=="odbc"){
							$cmd = " select			a.TRN_STARTDATE, a.TRN_ENDDATE,a.TRN_ORG, b.TR_NAME, a.TRN_PLACE, a. TRN_FUND, a.TRN_NO, a.TRN_COURSE_NAME
											 from			PER_TRAINING a, PER_TRAIN b
											 where		a.PER_ID=$PER_ID and a.TR_CODE=b.TR_CODE
											 order by		a.TRN_STARTDATE ";							   
						}elseif($DPISDB=="oci8"){
							$cmd = " select			a.TRN_STARTDATE, a.TRN_ENDDATE, a.TRN_ORG, b.TR_NAME, a.TRN_PLACE , a. TRN_FUND, a.TRN_NO, a.TRN_COURSE_NAME
											 from			PER_TRAINING a, PER_TRAIN b
											 where		a.PER_ID=$PER_ID and a.TR_CODE=b.TR_CODE
											 order by		a.TRN_STARTDATE ";							   
						}elseif($DPISDB=="mysql"){
							$cmd = " select			a.TRN_STARTDATE, a.TRN_ENDDATE, a.TRN_ORG, b.TR_NAME, a.TRN_PLACE, a. TRN_FUND, a.TRN_NO, a.TRN_COURSE_NAME
											 from			PER_TRAINING a, PER_TRAIN b
											 where		a.PER_ID=$PER_ID and a.TR_CODE=b.TR_CODE
											 order by		a.TRN_STARTDATE ";		
						} // end if
						$count_traininghis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
						$row_count = $trn_count = 0;
						while($data2 = $db_dpis2->get_array()){
								$TRN_STARTDATE = show_date_format($data2[TRN_STARTDATE],2);
								$TRN_ENDDATE = show_date_format($data2[TRN_ENDDATE],2);
								if(trim($TRN_STARTDATE) && trim($TRN_ENDDATE)){
									if ($BKK_FLAG==1) { // กทม
										$TRN_DURATION = $TRN_STARTDATE." - ".$TRN_ENDDATE;
									} else {
										$TRN_DURATION = $TRN_STARTDATE." - *Enter*".$TRN_ENDDATE;
									}
									if($TRN_STARTDATE == $TRN_ENDDATE) $TRN_DURATION = "$TRN_STARTDATE";
								}
								$arr_content[$row_count][trn_duration]=$TRN_DURATION;
								$TRN_ORG = trim($data2[TRN_ORG]);
								$TR_NAME = trim($data2[TR_NAME]);				
								if (!$TR_NAME || $TR_NAME=="อื่นๆ") $TR_NAME = trim($data2[TRN_COURSE_NAME]);
								if($TR_NAME!=""){	$TR_NAME = str_replace("&quot;",'"',$TR_NAME);		}
								$TRN_PLACE = trim($data2[TRN_PLACE]);
								$TRN_FUND = trim($data2[TRN_FUND]);
								$TRN_NO = trim($data2[TRN_NO]);
								if($TRN_NO && $TR_NAME) $TR_NAME .= " รุ่นที่ $TRN_NO";
								$arr_content[$row_count][tr_name] = $TR_NAME;
								$arr_content[$row_count][trn_org] = $TRN_ORG;
								$arr_content[$row_count][trn_place] = $TRN_PLACE;
								$arr_content[$row_count][trn_fund] = $TRN_FUND;
								$row_count++;
								$trn_count++;
						} // end while data2 for TRAINING

//						$BKK_FLAG=1;
//						echo "BKK_FLAG=$BKK_FLAG<br>";
						if ($BKK_FLAG==1) { // กทม
							$pdf->Cell(200,7,"ประวัติการศึกษา",1,1,"C");
							
							$head_text1 = implode(",", $heading_bkk1_text[$HISTORY_NAME]);
							$head_width1 = implode(",", $heading_bkk1_width[$HISTORY_NAME]);
							$head_align1 = implode(",", $heading_bkk1_align[$HISTORY_NAME]);
							$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "angsa", "14", "b", "0066CC", "EEEEFF", 0);
							if (!$result) echo "****** error ****** on open table for $HISTORY_NAME<br>";
								
							for($row_count=0; $row_count < $edu_count; $row_count++) {
									$border = "";
									// พิมพ์ EDU
									$arr_data[0] = $arr_content[$row_count][ins_name];
									$arr_data[1] = $arr_content[$row_count][edu_period];
									$arr_data[2] = $arr_content[$row_count][en_name]."  ".$arr_content[$row_count][em_name];

									$data_align = array("L","C","L");
					
									$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "angsa", "14", "", "000000", "");
									if (!$result) echo "****** error ****** add data to table at record count = 6 <br>";
							} // end for loop $row_count
							
							$pdf->close_tab(""); 
							
							$pdf->Cell(200,7,"ประวัติการฝึกอบรมและดูงาน",1,1,"C");
							
							$head_text1 = implode(",", $heading_bkk2_text[$HISTORY_NAME]);
							$head_width1 = implode(",", $heading_bkk2_width[$HISTORY_NAME]);
							$head_align1 = implode(",", $heading_bkk2_align[$HISTORY_NAME]);
							$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "angsa", "14", "b", "0066CC", "EEEEFF", 0);
							if (!$result) echo "****** error ****** on open table for $HISTORY_NAME<br>";
								
							for($row_count=0; $row_count < $trn_count; $row_count++) {
									$border = "";
									// พิมพ์ TRN
									$arr_data[0] = $arr_content[$row_count][trn_place];
									$arr_data[1] = $arr_content[$row_count][trn_duration];
									$arr_data[2] = $arr_content[$row_count][tr_name];

									$data_align = array("L", "L", "L");
					
									$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "angsa", "14", "", "000000", "");
									if (!$result) echo "****** error ****** add data to table at record count = 6 <br>";
							} // end for loop $row_count
							
							// พิมพ์ ส่วนที่เหลือให้เต็มหน้า
							if (($pdf->h - $pdf->y - 10) >= 10) {
									$pdf->SetDrawColor(hexdec("00"),hexdec("00"),hexdec("00")); //สีดำ

									$line_start_y = $pdf->y;
									$line_end_y = $pdf->h-10;
									$line_start_x = $start_x;
									$line_end_x = $line_start_x;
									$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y); // เส้นขวางเส้นแรก
//									echo "0 x:".$pdf->x.",y:".$pdf->y."<br>";
									
									for($i=0; $i<=2; $i++) {
										$line_start_x += $heading_bkk2_width[$HISTORY_NAME][$i];
										$line_end_x += $heading_bkk2_width[$HISTORY_NAME][$i];
										$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y); // เส้นขวางที่เหลือ
//										echo "".($i+1)." x:".$pdf->x.",y:".$pdf->y."<br>";
									} // end for
									
									$line_start_y = $pdf->h-10;		$line_start_x = $start_x;
									$line_end_y = $pdf->h-10;	
									$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y); // เส้นนอน
							}
							$pdf->close_tab(""); 
						} else {	// $BKK_FLAG != 1
							$pdf->Cell(95,7,"ประวัติการศึกษา",1,0,"C");
							$pdf->Cell(105,7,"ประวัติการฝึกอบรมและดูงาน",1,1,"C");
							
							$head_text1 = implode(",", $heading_text[$HISTORY_NAME]);
							$head_width1 = implode(",", $heading_width[$HISTORY_NAME]);
							$head_align1 = implode(",", $heading_align[$HISTORY_NAME]);
							$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "angsa", "14", "b", "0066CC", "EEEEFF", 0);
							if (!$result) echo "****** error ****** on open table for $HISTORY_NAME<br>";
							
							for($row_count=0; $row_count < count($arr_content); $row_count++) {
									$border = "";
									// พิมพ์ EDU
									$arr_data[0] = $arr_content[$row_count][ins_name];
									$arr_data[1] = $arr_content[$row_count][edu_period];
									$arr_data[2] = $arr_content[$row_count][en_name]."  ".$arr_content[$row_count][em_name];
									// พิมพ์ TRN
									$arr_data[3] = $arr_content[$row_count][trn_place];
									$arr_data[4] = $arr_content[$row_count][trn_duration];
									$arr_data[5] = $arr_content[$row_count][tr_name];

									$data_align = array("L","R","L", "L", "L", "L");
					
									$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "angsa", "14", "", "000000", "");
									if (!$result) echo "****** error ****** add data to table at record count = 6 <br>";
							} // end for loop $row_count
							
							// พิมพ์ ส่วนที่เหลือให้เต็มหน้า
							if (($pdf->h - $pdf->y - 10) >= 10) {
									$pdf->SetDrawColor(hexdec("00"),hexdec("00"),hexdec("00")); //สีดำ

									$line_start_y = $pdf->y;
									$line_end_y = $pdf->h-10;
									$line_start_x = $start_x;
									$line_end_x = $line_start_x;
									$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y); // เส้นขวางเส้นแรก
//									echo "0 x:".$pdf->x.",y:".$pdf->y."<br>";
									
									for($i=0; $i<=5; $i++) {
										$line_start_x += $heading_width[$HISTORY_NAME][$i];
										$line_end_x += $heading_width[$HISTORY_NAME][$i];
										$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y); // เส้นขวางที่เหลือ
//										echo "".($i+1)." x:".$pdf->x.",y:".$pdf->y."<br>";
									} // end for
									
									$line_start_y = $pdf->h-10;		$line_start_x = $start_x;
									$line_end_y = $pdf->h-10;	
									$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y); // เส้นนอน
							}
							$pdf->close_tab(""); 
						}	// end if ($BKK_FLAG==1)
						break;
					case "POSITIONHIS" : //รวมประวัติรับราชการ + เลื่อนขั้นเงินเดือนเข้าด้วยกัน
//							if(($pdf->h - $pdf->y - 10) < 30) $pdf->AddPage();
							$page_no = 1;
							$pdf->AddPage();
							$pdf->SetFont($fontb,'',14);
							$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
							$pdf->Cell(200,7,"13. ตำแหน่งและอัตราเงินเดือน",1,1,"C");

							$head_text1 = implode(",", $heading_text[$HISTORY_NAME]);
							$head_width1 = implode(",", $heading_width[$HISTORY_NAME]);
							$head_align1 = implode(",", $heading_align[$HISTORY_NAME]);
							$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "angsa", "14", "b", "0066CC", "EEEEFF", 0);
							if (!$result) echo "****** error ****** on open table for $HISTORY_NAME<br>";
							//########################
							//ประวัติการดำรงตำแหน่งข้าราชการ
							//########################
							if($DPISDB=="odbc"){
								$cmd = " select	a.POH_ORG2 as ORG_NAME_1, a.POH_ORG3 as ORG_NAME_2, a.POH_EFFECTIVEDATE,
																a.MOV_CODE, d.PL_NAME, a.LEVEL_NO, g.LEVEL_NAME, g.POSITION_LEVEL, a.POH_POS_NO_NAME, a.POH_POS_NO, 
																a.POH_SALARY, a.POH_DOCNO, a.PT_CODE, e.PT_NAME, a.PM_CODE, f.PM_NAME, 
																a.POH_DOCDATE, a.UPDATE_USER, a.POH_PL_NAME, a.POH_ORG, a.POH_SEQ_NO
												   from			(
																		(
																			(
																			PER_POSITIONHIS a
																			left join PER_LINE d on (a.PL_CODE=d.PL_CODE)
																		) left join PER_TYPE e on (a.PT_CODE=e.PT_CODE)
																	) left join PER_MGT f on (a.PM_CODE=f.PM_CODE)
																) left join PER_LEVEL g on (a.LEVEL_NO=g.LEVEL_NO)
												   where		a.PER_ID=$PER_ID
												   order by		a.POH_EFFECTIVEDATE, a.POH_SALARY, a.POH_ENDDATE ";							   
							}elseif($DPISDB=="oci8"){
							 	$cmd = "select	a.POH_ORG2 as ORG_NAME_1, a.POH_ORG3 as ORG_NAME_2, a.POH_EFFECTIVEDATE,
																a.MOV_CODE, d.PL_NAME, a.LEVEL_NO, g.LEVEL_NAME, g.POSITION_LEVEL, a.POH_POS_NO_NAME, a.POH_POS_NO, 
																a.POH_SALARY, a.POH_DOCNO, a.PT_CODE, e.PT_NAME, a.PM_CODE, f.PM_NAME, 
																a.POH_DOCDATE,	a.UPDATE_USER, a.POH_PL_NAME, a.POH_ORG, a.POH_SEQ_NO
												from		PER_POSITIONHIS a, PER_LINE d, PER_TYPE e, PER_MGT f, PER_LEVEL g
												where	a.PER_ID=$PER_ID and a.PL_CODE=d.PL_CODE(+) and
																a.PT_CODE=e.PT_CODE(+) and a.PM_CODE=f.PM_CODE(+) and 
																a.LEVEL_NO=g.LEVEL_NO(+)
												order by	a.POH_EFFECTIVEDATE, a.POH_SALARY, a.POH_ENDDATE ";
							}elseif($DPISDB=="mysql"){
								$cmd = "  select a.POH_ORG2 as ORG_NAME_1, a.POH_ORG3 as ORG_NAME_2, a.POH_EFFECTIVEDATE,
																a.MOV_CODE, d.PL_NAME, a.LEVEL_NO, g.LEVEL_NAME, g.POSITION_LEVEL, a.POH_POS_NO_NAME, a.POH_POS_NO, 
																a.POH_SALARY, a.POH_DOCNO, a.PT_CODE, e.PT_NAME, a.PM_CODE, f.PM_NAME, 
																a.POH_DOCDATE, a.UPDATE_USER, a.POH_PL_NAME, a.POH_ORG, a.POH_SEQ_NO
												  from	(
																(
																	(
																		PER_POSITIONHIS a left join PER_LINE d on (a.PL_CODE=d.PL_CODE)
																	) 	left join PER_TYPE e on (a.PT_CODE=e.PT_CODE)
																) left join PER_MGT f on (a.PM_CODE=f.PM_CODE)
															) left join PER_LEVEL g on (a.LEVEL_NO=g.LEVEL_NO)
												   where	a.PER_ID=$PER_ID
												   order by		a.POH_EFFECTIVEDATE, a.POH_SALARY, a.POH_ENDDATE ";
							} // end if
							$count_positionhis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							if($count_positionhis){
								$positionhis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$positionhis_count++;
									$POH_EFFECTIVEDATE = trim(substr($data2[POH_EFFECTIVEDATE],0,10));
									$POH_EFFECTIVEDATE = ($POH_EFFECTIVEDATE?$POH_EFFECTIVEDATE:"-");
									$LEVEL_NO = trim($data2[LEVEL_NO]);
									$POSITION_LEVEL = trim($data2[POSITION_LEVEL]);
									if ($POSITION_LEVEL=="ชำนาญการพิเศษ") {
										$POSITION_LEVEL = "ชำนาญการ*Enter*พิเศษ";
//										$testpos = str_replace("\n","<BR>",$POSITION_LEVEL);
//										echo "POSITION_LEVEL=$testpos<BR>";
									}
									$POH_SEQ_NO = trim($data2[POH_SEQ_NO]);
//									if( ("$PM_CODE $PL_NAME $LEVEL_NO $POH_POS_NO") != (trim($data2[PM_CODE])." ".trim($data2[PL_NAME])." ".$LEVEL_NO." ".trim($data2[POH_POS_NO]))){
										$PL_NAME = trim($data2[PL_NAME]);
										$LEVEL_NAME = trim(str_replace("ระดับ","",$data2[LEVEL_NAME]));
										$footer_level = trim($data2[LEVEL_NAME]);
//										echo "PER_ID=$PER_ID,  POSITION_LEVEL=$POSITION_LEVEL,  LEVEL_NAME=$LEVEL_NAME,  footer_level=$footer_level<br>";
										$PT_CODE = trim($data2[PT_CODE]);
										$PT_NAME = trim($data2[PT_NAME]);
										$PM_CODE = trim($data2[PM_CODE]);
										$PM_NAME = trim($data2[PM_NAME]);
										$POH_PL_NAME = trim($data2[POH_PL_NAME]);
										if ($LEVEL_NO >= "1" && $LEVEL_NO <= "9") $POH_PL_NAME = $POH_PL_NAME." ".level_no_format($LEVEL_NO);
										$arr_temp = "";
										if (strlen($POH_PL_NAME) > 50) {
											$arr_temp = explode(" ", $POH_PL_NAME);
											if ((strlen($arr_temp[0])+strlen($arr_temp[1])+strlen($arr_temp[2])) < 50) 
												$POH_PL_NAME = $arr_temp[0]." ".$arr_temp[1]." ".$arr_temp[2]."\n".$arr_temp[3]." ".$arr_temp[4]." ".$arr_temp[5]." ".$arr_temp[6]." ".$arr_temp[7];
											elseif ((strlen($arr_temp[0])+strlen($arr_temp[1])) < 50) 
												$POH_PL_NAME = $arr_temp[0]." ".$arr_temp[1]."\n".$arr_temp[2]." ".$arr_temp[3]." ".$arr_temp[4]." ".$arr_temp[5]." ".$arr_temp[6]." ".$arr_temp[7];
											else
												$POH_PL_NAME = $arr_temp[0]."\n".$arr_temp[1]." ".$arr_temp[2]." ".$arr_temp[3]." ".$arr_temp[4]." ".$arr_temp[5]." ".$arr_temp[6]." ".$arr_temp[7];
										}
										$POH_ORG = trim($data2[POH_ORG]);
										$arr_temp = "";
										if (strlen($POH_ORG) > 50) {
											$arr_temp = explode(" ", $POH_ORG);
											$POH_ORG = $arr_temp[0]."\n".$arr_temp[1]." ".$arr_temp[2]." ".$arr_temp[3]." ".$arr_temp[4]." ".$arr_temp[5];
										}
										$TMP_PL_NAME = $POH_PL_NAME."\n".$POH_ORG;
										//if($PM_CODE && $PM_CODE!="9999") $TMP_PL_NAME = $PM_NAME." ($TMP_PL_NAME)";
//									}
									$TMP_PL_NAME = (trim($TMP_PL_NAME)?trim($TMP_PL_NAME):"-");
									$POH_POS_NO = trim($data2[POH_POS_NO_NAME]).trim($data2[POH_POS_NO]);	
									$POH_POS_NO = ($POH_POS_NO?$POH_POS_NO:"-");
									$POH_SALARY = $data2[POH_SALARY];
//									$POH_DOCNO = (trim($data2[POH_DOCNO]))? $data2[POH_DOCNO] : "-" ;
									$POH_DOCNO = trim($data2[POH_DOCNO]);
									$MOV_CODE = trim($data2[MOV_CODE]);
									
									if(trim($data2[POH_DOCNO]) && trim($data2[POH_DOCNO]) != "-"){
										if(trim($data2[POH_DOCDATE])){
											$POH_DOCDATE = "ลว. ".show_date_format(substr($data2[POH_DOCDATE], 0, 10),2);
										}
										$USRNAME = "";
										if($data2[UPDATE_USER]){
											//ดึงชื่อจาก ตาราง user_detail ของ mysql
											$cmd1 ="select fullname from user_detail where id=$data2[UPDATE_USER]";
											$db->send_cmd($cmd1);
											//$db->show_error();
											$datausr = $db->get_array();
											$datausr = array_change_key_case($datausr, CASE_LOWER);
											$USRNAME = $datausr[fullname]; 
										}
										if ($BKK_FLAG==1) 
											$POH_DOCNO = $data2[POH_DOCNO]." ".$POH_DOCDATE;
										else
											$POH_DOCNO = $data2[POH_DOCNO]."*Enter*".$POH_DOCDATE."*Enter*".$USRNAME;
										if (trim($data2[POH_DOCNO]) && trim($data2[POH_DOCNO]) != "-" && strpos($data2[POH_DOCNO],"คส.") == false)
											$POH_DOCNO = "คส. ".$POH_DOCNO;
									}

									//หาประเภทการเคลื่อนไหวของประวัติการดำรงตำแหน่งข้าราชการ
									$cmd = " select MOV_NAME, MOV_SUB_TYPE from PER_MOVMENT pcd where MOV_CODE='$MOV_CODE' ";
									$db_dpis3->send_cmd($cmd);
									//echo "<br>$cmd<br>";
									//$db_dpis3->show_error();
									$data3 = $db_dpis3->get_array();
									$MOV_NAME = $data3[MOV_NAME];
									$MOV_SUB_TYPE = $data3[MOV_SUB_TYPE];
									if ($MOV_SUB_TYPE==9) {
										$POH_POS_NO = $POSITION_LEVEL = $POH_SALARY = "";
									}

									//เก็บลง array ของ POSTION HIS
									if ($BKK_FLAG==1) 
										$ARR_POSITIONHIS[$PER_ID][] = array(
																						'DATE'=>$POH_EFFECTIVEDATE,
																						'SEQ'=>$POH_SEQ_NO,
																						'MOVE'=>$MOV_NAME,
																						'POS_NAME'=>$TMP_PL_NAME,
																						'POS_NO'=>$POH_POS_NO,
																						'LEVEL'=>$POSITION_LEVEL,
																						'FOOTERLEVEL'=>$footer_level,
																						'SALARY'=>$POH_SALARY,
																						'DOC_NO'=>$MOV_NAME."*Enter*".$POH_DOCNO);
									else
										$ARR_POSITIONHIS[$PER_ID][] = array(
																						'DATE'=>$POH_EFFECTIVEDATE,
																						'SEQ'=>$POH_SEQ_NO,
																						'MOVE'=>$MOV_NAME,
																						'POS_NAME'=>$TMP_PL_NAME,
																						'POS_NO'=>$POH_POS_NO,
																						'LEVEL'=>$POSITION_LEVEL,
																						'FOOTERLEVEL'=>$footer_level,
																						'SALARY'=>$POH_SALARY,
																						'DOC_NO'=>$POH_DOCNO);
									$ARR_POSCHECK[$PER_ID][DOC_NO][] = $data2[POH_DOCNO];
									$ARR_POSCHECK[$PER_ID][DOC_DATE][] = $data2[POH_DOCDATE];
									$ARR_POSCHECK[$PER_ID][MOVE_CODE][] = $MOV_CODE;
								} // end while
							} //end if 
	
							//########################
							//ประวัติการเลื่อนขั้นเงินเดือน
							//########################
							if($DPISDB=="odbc"){
								$cmd = " select			LEFT(b.SAH_EFFECTIVEDATE,10) as SAH_EFFECTIVEDATE, c.MOV_NAME, b.SAH_SALARY, 
																	b.SAH_DOCNO, b.SAH_DOCDATE, b.UPDATE_USER, b.SAH_SEQ_NO, b.LEVEL_NO, b.SAH_POSITION, 
																	b.SAH_ORG, b.SM_CODE, b.SAH_PAY_NO, b.SAH_POS_NO_NAME, b.SAH_POS_NO, b.SAH_PERCENT_UP, b.SAH_SALARY_EXTRA
												 from			PER_SALARYHIS b
												 inner join 	PER_MOVMENT c	 on	b.MOV_CODE=c.MOV_CODE					
												 where		b.PER_ID=$PER_ID and b.MOV_CODE!='1901'
												 order by		b.SAH_SALARY, b.SAH_EFFECTIVEDATE ";							   
							}elseif($DPISDB=="oci8"){
								$cmd = " select		 SUBSTR(b.SAH_EFFECTIVEDATE,1,10) as SAH_EFFECTIVEDATE, c.MOV_NAME, b.SAH_SALARY, 
																	b.SAH_DOCNO, b.SAH_DOCDATE, b.UPDATE_USER, b.SAH_SEQ_NO, b.LEVEL_NO, b.SAH_POSITION, 
																	b.SAH_ORG, b.SM_CODE, b.SAH_PAY_NO, b.SAH_POS_NO_NAME, b.SAH_POS_NO, b.SAH_PERCENT_UP, b.SAH_SALARY_EXTRA
												from			PER_SALARYHIS b, PER_MOVMENT c
												where		b.PER_ID=$PER_ID and b.MOV_CODE=c.MOV_CODE and b.MOV_CODE!='1901' 
												order by		b.SAH_SALARY, b.SAH_EFFECTIVEDATE ";		   					   
							}elseif($DPISDB=="mysql"){
								$cmd = " select			LEFT(b.SAH_EFFECTIVEDATE,10) as SAH_EFFECTIVEDATE, c.MOV_NAME, b.SAH_SALARY, 
																	b.SAH_DOCNO, b.SAH_DOCDATE, b.UPDATE_USER, b.SAH_SEQ_NO, b.LEVEL_NO, b.SAH_POSITION, 
																	b.SAH_ORG, b.SM_CODE, b.SAH_PAY_NO, b.SAH_POS_NO_NAME, b.SAH_POS_NO, b.SAH_PERCENT_UP, b.SAH_SALARY_EXTRA
												 from			PER_SALARYHIS b  inner join 	PER_MOVMENT c	 on	b.MOV_CODE=c.MOV_CODE
												 where			b.PER_ID=$PER_ID and b.MOV_CODE!='1901'
												 order by		b.SAH_SALARY, b.SAH_EFFECTIVEDATE ";
							} // end if
							$count_salaryhis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							//echo "<br>$cmd<br>";
							if($count_salaryhis){
								$salaryhis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$salaryhis_count++;
									$SAH_EFFECTIVEDATE = trim($data2[SAH_EFFECTIVEDATE]);
									$SAH_EFFECTIVEDATE = ($SAH_EFFECTIVEDATE?$SAH_EFFECTIVEDATE:"-");
									$MOV_NAME = trim($data2[MOV_NAME]);		
									$MOV_NAME = (trim($MOV_NAME)?trim($MOV_NAME):"");
									$SAH_SALARY = $data2[SAH_SALARY];
									$SAH_SEQ_NO = $data2[SAH_SEQ_NO];
									$LEVEL_NO = $data2[LEVEL_NO];
									$SAH_POSITION = $data2[SAH_POSITION];
									$SAH_ORG = $data2[SAH_ORG];
									$SM_CODE = $data2[SM_CODE];
									$SAH_PAY_NO = $data2[SAH_PAY_NO];
									if (!$SAH_PAY_NO) $SAH_PAY_NO = $data2[SAH_POS_NO_NAME].$data2[SAH_POS_NO];
									$SAH_PERCENT_UP = $data2[SAH_PERCENT_UP];		
									$SAH_SALARY_EXTRA = $data2[SAH_SALARY_EXTRA];		
									if ($SAH_PERCENT_UP) $MOV_NAME = $MOV_NAME . " " . number_format($SAH_PERCENT_UP, 2) . " %";		
									if (strpos($MOV_NAME,"เต็มขั้น") !== false) $MOV_NAME = $MOV_NAME . " " . number_format($SAH_SALARY_EXTRA, 2);	
									if ($LEVEL_NO >= "1" && $LEVEL_NO <= "9") $SAH_POSITION = $SAH_POSITION." ".level_no_format($LEVEL_NO);

									$cmd = " select SM_NAME, SM_FACTOR from PER_SALARY_MOVMENT where SM_CODE='$SM_CODE' ";
									$db_dpis3->send_cmd($cmd);
									$data3 = $db_dpis3->get_array();
									$SM_NAME = trim($data3[SM_NAME]);
									$SM_FACTOR = $data3[SM_FACTOR] + 0;
//									if ($SM_NAME && $SM_FACTOR != 0) $MOV_NAME .= " ($SM_NAME)";
									if ($BKK_FLAG==1) {
										if ($SM_NAME=="เลื่อนขั้น 1 ขั้น") $SAH_SALARY .= "*Enter*หนึ่งขั้น";
										elseif ($SM_NAME=="เลื่อนขั้น 1.5 ขั้น") $SAH_SALARY .= "*Enter*หนึ่งขั้นครึ่ง";
										elseif ($SM_NAME=="เลื่อนขั้น 2 ขั้น") $SAH_SALARY .= "*Enter*สองขั้น";
//										echo "SALARY=$SAH_SALARY<br>";
									} else {
										if ($SM_NAME) $MOV_NAME .= " ($SM_NAME)";
									}
									$ARR_POSCHECK[$PER_ID][DOC_NO][] = $data2[POH_DOCNO];
									$ARR_POSCHECK[$PER_ID][DOC_DATE][] = $POH_DOCDATE;
									$ARR_POSCHECK[$PER_ID][MOVE_CODE][] = $MOV_CODE;
									if(trim($data2[SAH_DOCNO]) && trim($data2[SAH_DOCNO]) != "-"){
										if(trim($data2[SAH_DOCDATE])){
											$SAH_DOCDATE = "ลว. ".show_date_format(substr($data2[SAH_DOCDATE], 0, 10),2);
										}
										$USRNAME = "";
										if($data2[UPDATE_USER]){
											//ดึงชื่อจาก ตาราง user_detail ของ mysql
											$cmd1 ="select fullname from user_detail where id=$data2[UPDATE_USER]";
											$db->send_cmd($cmd1);
											//	$db->show_error();
											$datausr = $db->get_array();
											$datausr = array_change_key_case($datausr, CASE_LOWER);
											$USRNAME = $datausr[fullname];
										}
										if ($BKK_FLAG==1) 
											$SAH_DOCNO = $data2[SAH_DOCNO]." ".$SAH_DOCDATE;
										else
											$SAH_DOCNO = $data2[SAH_DOCNO]."*Enter*".$SAH_DOCDATE."*Enter*".$USRNAME;
										if (trim($data2[SAH_DOCNO]) && trim($data2[SAH_DOCNO]) != "-" && strpos($data2[SAH_DOCNO],"คส.") == false)
											$SAH_DOCNO = "คส. ".$SAH_DOCNO;
									}

									$cmd = " select POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$LEVEL_NO' ";
									$db_dpis3->send_cmd($cmd);
									//echo "<br>$cmd<br>";
									//$db_dpis3->show_error();
									$data3 = $db_dpis3->get_array();
									$POSITION_LEVEL = $data3[POSITION_LEVEL];
									if ($POSITION_LEVEL=="ชำนาญการพิเศษ") {
										$POSITION_LEVEL = "ชำนาญการ*Enter*พิเศษ";
//										$testpos = str_replace("\n","<BR>",$POSITION_LEVEL);
//										echo "POSITION_LEVEL=$testpos<BR>";
									}
									$TMP_PL_NAME = trim($MOVE_NAME);
									
									$footer_level = $POSITION_LEVEL;

									$flag_dup = false;
									$key = array_search($data2[SAH_DOCNO], $ARR_POSCHECK[$PER_ID][DOC_NO]); 
									if ($key !== false) { // ถ้ามี DOC_NO เหมือนกัน
										if ($ARR_POSCHECK[$PER_ID][DOC_DATE][$key] == $data2[SAH_DOCDATE]) { // และถ้ามี doc_date เหมือนกัน
//											if ($ARR_POSCHECK[$PER_ID][MOVE_CODE][$key] == "") { // และถ้ามี move_code=
												$flag_dup = true;
												if (!$ARR_POSITIONHIS[$PER_ID][$key][SEQ]) 
													$ARR_POSITIONHIS[$PER_ID][$key][SEQ] = $SAH_SEQ_NO;
												if (!$ARR_POSITIONHIS[$PER_ID][$key][MOVE]) 
													$ARR_POSITIONHIS[$PER_ID][$key][MOVE] = $MOV_NAME;
												if (!$ARR_POSITIONHIS[$PER_ID][$key][POS_NAME]) 
													$ARR_POSITIONHIS[$PER_ID][$key][POS_NAME] = $SAH_POSITION;
												if (!$ARR_POSITIONHIS[$PER_ID][$key][POS_NO]) 
													$ARR_POSITIONHIS[$PER_ID][$key][POS_NO] = $SAH_PAY_NO;
												if (!$ARR_POSITIONHIS[$PER_ID][$key][LEVEL]) 
													$ARR_POSITIONHIS[$PER_ID][$key][LEVEL] = $POSITION_LEVEL;
//												echo "PER_ID=$PER_ID,  key=$key,  POSITION_LEVEL=$POSITION_LEVEL,  footer_level=$footer_level, FOOTER เดิม=".$ARR_POSITIONHIS[$PER_ID][$key][FOOTERLEVEL]."  LEVEL เดิม=".$ARR_POSITIONHIS[$PER_ID][$key][LEVEL]."<br>";
												if (!$ARR_POSITIONHIS[$PER_ID][$key][FOOTERLEVEL]) 
													$ARR_POSITIONHIS[$PER_ID][$key][FOOTERLEVEL] = $footer_level;
												if (!$ARR_POSITIONHIS[$PER_ID][$key][SALARY]) 
													$ARR_POSITIONHIS[$PER_ID][$key][SALARY] = $SAH_SALARY;
//											} // end if check movecode
										} // end if check doc_date
									} // end if check doc_no
									if (!$flag_dup) { // ถ้าไม่ซ้ำ
									//เก็บลง array ของ SALARYHIS
									if ($BKK_FLAG==1) 
										$ARR_SALARYHIS[$PER_ID][] = array(
																						'DATE'=>$SAH_EFFECTIVEDATE,
																						'SEQ'=>$SAH_SEQ_NO,
																						'MOVE'=>$SAH_POSITION."*Enter*".$SAH_ORG,
																						'POS_NAME'=>$TMP_PL_NAME,
																						'POS_NO'=>$SAH_PAY_NO,
																						'LEVEL'=>$POSITION_LEVEL,
																						'FOOTERLEVEL'=>$footer_level,
																						'SALARY'=>$SAH_SALARY,
																						'DOC_NO'=>$MOV_NAME."*Enter*".$SAH_DOCNO);																			
									else
										$ARR_SALARYHIS[$PER_ID][] = array(
																						'DATE'=>$SAH_EFFECTIVEDATE,
																						'SEQ'=>$SAH_SEQ_NO,
																						'MOVE'=>$MOV_NAME,
																						'POS_NAME'=>$TMP_PL_NAME,
																						'POS_NO'=>$SAH_PAY_NO,
																						'LEVEL'=>$POSITION_LEVEL,
																						'FOOTERLEVEL'=>$footer_level,
																						'SALARY'=>$SAH_SALARY,
																						'DOC_NO'=>$SAH_DOCNO);
									} // end if !$flag_dup 
								} // end while
							}// end if

							//######################################
							//รวมประวัติการดำรงตำแหน่ง + การเลื่อนขั้นเงินเดือน
							//######################################
							//array_multisort($ARR_POSITIONHIS[$PER_ID], SORT_ASC, $ARR_SALARYHIS[$PER_ID], SORT_ASC);
							$ARRAY_POH_SAH[$PER_ID] = array_merge_recursive($ARR_POSITIONHIS[$PER_ID] , $ARR_SALARYHIS[$PER_ID]);
							unset($ARR_POSITIONHIS);
							unset($ARR_SALARYHIS);

							// เรียงข้อมูล ตามวันที่ / เงินเดือน น้อยไปมาก
							for($in=0; $in < count($ARRAY_POH_SAH[$PER_ID]); $in++){
								//เก็บค่าวันที่
								$DATE_HIS[] = array('DATE'=>$ARRAY_POH_SAH[$PER_ID][$in]['DATE'],
															'SEQ'=>$ARRAY_POH_SAH[$PER_ID][$in]['SEQ'],
															'MOVE'=>$ARRAY_POH_SAH[$PER_ID][$in]['MOVE'],
															'POS_NAME'=>$ARRAY_POH_SAH[$PER_ID][$in]['POS_NAME'],
															'POS_NO'=>$ARRAY_POH_SAH[$PER_ID][$in]['POS_NO'],
															'LEVEL'=>$ARRAY_POH_SAH[$PER_ID][$in]['LEVEL'],
															'FOOTERLEVEL'=>$ARRAY_POH_SAH[$PER_ID][$in]['FOOTERLEVEL'],
															'SALARY'=>$ARRAY_POH_SAH[$PER_ID][$in]['SALARY'],
															'DOC_NO'=>$ARRAY_POH_SAH[$PER_ID][$in]['DOC_NO']);
							} // end for
							unset($ARRAY_POH_SAH);
							foreach ($DATE_HIS as $key => $value) {		//กรณีที่วันที่เดียวกัน แต่ต้องเอาเงินเดือนน้อยกว่าแสดงก่อน
								$DATE[$key]  = $value['DATE'];
								$SEQ[$key]  = $value['SEQ'];
								$MOVE[$key]  = $value['MOVE'];
								$POS_NAME[$key] = $value['POS_NAME'];
								$POS_NO[$key]  = $value['POS_NO'];
								$LEVEL[$key]  = $value['LEVEL'];
								$FOOTERLEVEL[$key]  = $value['FOOTERLEVEL'];
								$SALARY[$key] = $value['SALARY'];
								$DOC_NO[$key]  = $value['DOC_NO'];
//								echo "$PER_ID-->$key-->DATE=".$DATE[$key]." | SEQ=".$SEQ[$key]." | LEVEL=".$LEVEL[$key]." | FOOTERLEVEL=".$FOOTERLEVEL[$key]."<br>";
							} // end foreach
							array_multisort($DATE, SORT_ASC, $SALARY, SORT_NUMERIC, SORT_ASC, $DATE_HIS);
							$POH_SAH_HIS[$PER_ID]=$DATE_HIS;
							unset($DATE_HIS);

							//ส่วนแสดงเนื้อหา หลังจากจัดเรียงข้อมูลแล้ว
							if(is_array($POH_SAH_HIS) && !empty($POH_SAH_HIS)){
							$count_positionhis=count($POH_SAH_HIS[$PER_ID]);
							$positionhis_count=$first_line-1;
							//ถ้าไม่ได้ใส่ line มาคือแสดงทั้งหมด 
							if(!isset($get_line) || $get_line==""){		$get_line=$count_positionhis;		}
							$linenum = ceil($pdf->y / 7);  // พิมพ์มาแล้วกี่บรรทัด
							$linecnt = 0;
							for($in=0; $in < $count_positionhis; $in++){
									$positionhis_count++;
									$linecnt++;
									if($POH_SAH_HIS[$PER_ID][$in]['DATE']){
										$DATE_POH_SAH = show_date_format(substr($POH_SAH_HIS[$PER_ID][$in]['DATE'], 0, 10),$date_format);
									}

									//หาระดับตำแหน่ง (1,2,3,4,5,6,7,8,9);
									$cmd = "select LEVEL_NAME, POSITION_TYPE from PER_LEVEL where LEVEL_NO='".$POH_SAH_HIS[$PER_ID][$in]['LEVEL']."' order by LEVEL_NO";
									//echo "<br>$cmd<br>";
									$db_dpis2->send_cmd($cmd);
//									$db_dpis2->show_error();
									$data2 = $db_dpis2->get_array();
									$LEVEL_NAME1 = trim($data2[LEVEL_NAME]);
									$POSITION_TYPE = trim($data2[POSITION_TYPE]);
									$arr_temp = explode(" ", $LEVEL_NAME1);
									//หาชื่อระดับตำแหน่ง 
									$LEVEL_NAME1 ="";
									if(strstr($arr_temp[1], 'ระดับ') == TRUE) {
										$LEVEL_NAME1 =  str_replace("ระดับ", "", $arr_temp[1]);
									}else{
										$LEVEL_NAME1 =  $arr_temp[1];
									}
									
									//กำหนดชื่อตำแหน่ง -----------------------
									if(trim($POH_SAH_HIS[$PER_ID][$in]['POS_NAME'])){		//สำหรับการเคลื่อนไหวของ ตน.
										$f_pos_color = "blue";
										$POH_SAH_HIS[$PER_ID][$in]['POS_NAME'] = $POH_SAH_HIS[$PER_ID][$in]['POS_NAME']." ".$LEVEL_NAME1;	
									}else if(trim($POH_SAH_HIS[$PER_ID][$in]['MOVE'])){		//สำหรับการเลื่อนขั้นเงินเดือน
										$f_pos_color = "black";
										$POH_SAH_HIS[$PER_ID][$in]['POS_NAME'] =  $POH_SAH_HIS[$PER_ID][$in]['MOVE'];
									}

//									if ($BKK_FLAG==1) {
										$dlen = strlen($POH_SAH_HIS[$PER_ID][$in]['DOC_NO']);
										$temptext = $pdf->thaiCutLinePDF($POH_SAH_HIS[$PER_ID][$in]['DOC_NO'], $heading_width[POSITIONHIS][5], "\n");
										$sub_doc = explode("\n", $temptext);
//									} else {
//										$sub_doc = explode("\n", $POH_SAH_HIS[$PER_ID][$in]['DOC_NO']);
//									}
//									echo "PER_ID=$PER_ID, level=".$POH_SAH_HIS[$PER_ID][$in]['LEVEL'].",  last level=".$POH_SAH_HIS[$PER_ID][$in]['FOOTERLEVEL']."<br>";
									$prt_doc_line = count($sub_doc);
									$prt_doc_line = $prt_doc_line - (!trim($sub_doc[$prt_doc_line-1]) ? 1 -  (!trim($sub_doc[$prt_doc_line-2]) ? 1 : 0) : 0);
										$dlen = strlen($POH_SAH_HIS[$PER_ID][$in]['POS_NAME']);
										$temptext = $pdf->thaiCutLinePDF($POH_SAH_HIS[$PER_ID][$in]['POS_NAME'], $heading_width[POSITIONHIS][1], "\n");
										$sub_pos = explode("\n", $temptext);
									$buff_level = $pdf->thaiCutLinePDF($POH_SAH_HIS[$PER_ID][$in]['LEVEL'], $heading_width[POSITIONHIS][3], "\n");
									$sub_level = explode("\n", $buff_level);	//	echo "LEVEL---".$buff_level."  count_line=".count($sub_level)."<br>";
									$buff_salary = $pdf->thaiCutLinePDF($POH_SAH_HIS[$PER_ID][$in]['SALARY'], $heading_width[POSITIONHIS][4], "\n");
									$sub_salary = explode("\n", $buff_salary);	//	echo "SALARY---".$buff_salary."  count_line=".count($sub_salary)."<br>";
									$prt_level_line = count($sub_level);
									$prt_pos_line = 0;
									$prt_pos_line = count($sub_pos);
									$prt_salary_line = count($sub_salary);
//									echo "prt_salary_line=$prt_salary_line<br>";
									$prt_pos_line = $prt_pos_line - (!trim($sub_pos[$prt_pos_line-1]) ? 1 -  (!trim($sub_doc[$prt_doc_line-2]) ? 1 : 0) : 0);
									$prt_max_line = ($prt_doc_line > $prt_pos_line ? $prt_doc_line : $prt_pos_line);
									$prt_max_line = ($prt_level_line > $prt_max_line ? $prt_level_line : $prt_max_line);
									$prt_max_line = ($prt_salary_line > $prt_max_line ? $prt_salary_line : $prt_max_line);
									if (((($linenum + $prt_max_line) * 7) + 14) > $pdf->h) {
									// $linenum = จำนวนบรรทัดที่พิมพ์ไปแล้ว + $prt_doc_line = จำนวนบรรทัดที่จะพิมพ์สำหรับรายการนี้ แล้วคูณด้วย 7 คือความสูงของบรรทัด
									//  แล้วบวกด้วย 7 คือเว้นเผื่อขอบล่าง 1 บรรทัด (ถ้าจะเว้น 2 บรรทัดก็ บวกด้วย 14)
									
//										echo "PER_ID=$PER_ID, print last level=$last_footer_level<br>";
										print_footer($last_footer_level);
										
										$page_no++;
										$pdf->AddPage();
										$head_text1 = implode(",", $heading_text[$HISTORY_NAME]);
										$head_width1 = implode(",", $heading_width[$HISTORY_NAME]);
										$head_align1 = implode(",", $heading_align[$HISTORY_NAME]);
										$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "angsa", "14", "b", "0066CC", "EEEEFF", 0);
										if (!$result) echo "****** error ****** on open table for $HISTORY_NAME<br>";
										$max_y = $pdf->y;
										$linenum = ceil($pdf->y / 7);  // พิมพ์มาแล้วกี่บรรทัด
										$linecnt = 1;
									}
//									echo "PER_ID=$PER_ID, last level=".$POH_SAH_HIS[$PER_ID][$in]['FOOTERLEVEL']."<br>";
									$last_footer_level = $POH_SAH_HIS[$PER_ID][$in]['FOOTERLEVEL'];
									
									for($sub_line_i = 0; 	$sub_line_i < $prt_max_line; $sub_line_i++) {
										$linenum++;
										$border = (($sub_line_i == ($prt_max_line-1) && $sub_line_i == 0) ? "LTBR" : ($sub_line_i == 0 ? "LTR" : ($sub_line_i == ($prt_max_line-1) ? "LBR" : "LR")));
										$pdf->SetFont($font,'',14);
										$pdf->x=$page_start_x;
										$start_y = $pdf->y; $max_y = $pdf->y;

										$pdf->SetDrawColor(hexdec("00"),hexdec("00"),hexdec("00")); //สีดำ
										$pdf->SetFillColor(hexdec("FF"),hexdec("FF"),hexdec("FF"));
										
										if ($sub_line_i == 0) {
											$pdf->SetTextColor(hexdec("E4"),hexdec("E4"),hexdec("E4"));//สีเทา
										} else {
											$pdf->SetTextColor(hexdec("FF"),hexdec("FF"),hexdec("FF"));//สีขาว คือไม่พิมพ์
										}
										$start_x = $pdf->x;
										//-------------------------------------
										if ($sub_line_i == 0) {
											$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));	//สีดำ
										} else {
											$pdf->SetTextColor(hexdec("FF"),hexdec("FF"),hexdec("FF")); //สีขาว คือไม่พิมพ์
										}
										$pdf->SetFillColor(hexdec("FF"),hexdec("FF"),hexdec("FF"));
										$pdf->Cell($heading_width[POSITIONHIS][0] ,7,($DATE_POH_SAH?$DATE_POH_SAH:""),$border,0,"L");

										//กำหนดชื่อตำแหน่ง -----------------------
										if ($sub_line_i < $prt_pos_line) {
												if ($f_pos_color == "blue") {
													$pdf->SetTextColor(hexdec("65"),hexdec("00"),hexdec("CA"));	//สีน้ำเงิน
												} else {
													$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00")); // สีดำ
												}
											$prt_pos = $sub_pos[$sub_line_i];
										} else {
											$prt_pos = "";
										}
										$pdf->SetFillColor(hexdec("FF"),hexdec("FF"),hexdec("FF"));
										
										$pdf->Cell($heading_width[POSITIONHIS][1], 7, ($prt_pos ? $prt_pos : ""),$border,0, "L");
												
										if ($sub_line_i == 0) {
											$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
										} else {
											$pdf->SetTextColor(hexdec("FF"),hexdec("FF"),hexdec("FF"));//สีขาว คือไม่พิมพ์
										}
										$pdf->Cell($heading_width[POSITIONHIS][2] ,7,($POH_SAH_HIS[$PER_ID][$in]['POS_NO']?$POH_SAH_HIS[$PER_ID][$in]['POS_NO']:""),$border,0,"C");

										if ($sub_line_i < $prt_level_line) {
											$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
											$prt_level = $sub_level[$sub_line_i];
										} else {
											$prt_level = "";
										}
										$pdf->Cell($heading_width[POSITIONHIS][3], 7, ($prt_level ? $prt_level : ""),$border,0, "C");
										if ($sub_line_i == 0) {
											$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
										} else {
											$pdf->SetTextColor(hexdec("FF"),hexdec("FF"),hexdec("FF"));//สีขาว คือไม่พิมพ์
										}
										if ($sub_line_i < $prt_salary_line) {
											$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
											$prt_salary = $sub_salary[$sub_line_i];
//											echo "prt_salary [$sub_line_i]=$prt_salary<br>";
										} else {
											$prt_salary = "";
										}
//										echo "prt_salary=$prt_salary<br>";
//										$salary_text = ($POH_SAH_HIS[$PER_ID][$in]['SALARY']?number_format($POH_SAH_HIS[$PER_ID][$in]['SALARY']):"-");
										if ($prt_salary) {
											if ($sub_line_i==0)
												$salary_text = number_format($prt_salary);
											else
												$salary_text = $prt_salary;
//											echo "salary_text=$salary_text<br>";
										} else
											$salary_text = ($sub_line_i==0 ? "-" : "");
//										echo "salary_text=$salary_text<br>";
										$pdf->Cell($heading_width[POSITIONHIS][4] ,7,$salary_text,$border,0,"C");
										if ($sub_line_i < $prt_doc_line) {
											$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
											$prt_doc = $sub_doc[$sub_line_i];
										} else {
											$prt_doc = "";
										}
										$pdf->Cell($heading_width[POSITIONHIS][5], 7, ($prt_doc ? $prt_doc : ""),$border,1, "L");
										
										$pdf->x = $start_x + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6];

										$line_start_y = $start_y;		$line_start_x = $start_x;
										$line_end_y = $max_y;		$line_end_x = $pdf->x;
										$pdf->SetDrawColor(hexdec("FF"),hexdec("FF"),hexdec("FF"));
										$pdf->Line($line_start_x, $line_end_y, $line_end_x, $line_end_y);

										if($pdf->y > $max_y) $max_y = $pdf->y;
										$pdf->y = $max_y;
									} //end for $sub_line_i
							} // end for $in
							if($in<=0){	//}else{
								$pdf->SetFont($fontb,'',16);
								$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
								$pdf->Cell(200,7,"********** ไม่มีข้อมูล **********","LBR",1,'C');
							} else {
								print_footer($last_footer_level);
							} // end if	
						} // end if(is_array($POH_SAH_HIS) && !empty($POH_SAH_HIS))
						break;
					case "ABILITY" :
							$pdf->AddPage();
							$pdf->SetFont($fontb,'',14);
							$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
							if($pdf->y != $page_start_y) $pdf->Cell(200,7,"",0,1,"L");
							$pdf->Cell(200,7,"ข้อมูลสำคัญโดยย่อ",1,1,"C");
							$pdf->Cell(200,7,"5.  ความสามารถพิเศษ ","TRBL",1,"L");
							$head_text1 = "";
							$head_width1 = implode(",", $heading_width[$HISTORY_NAME]);
							$head_align1 = implode(",", $heading_align[$HISTORY_NAME]);
							$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "angsa", "14", "b", "0066CC", "EEEEFF", 0);
							if (!$result) echo "****** error ****** on open table for $HISTORY_NAME<br>";

							$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
							$line_start_y = $start_y;		$line_start_x = $start_x;
							$line_end_y = $max_y;		$line_end_x =  $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1];
							$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);

							$AL_NAME = "";
							$ABI_DESC = "";

							$cmd = " select		b.AL_NAME, a.ABI_DESC
											 from			PER_ABILITY a, PER_ABILITYGRP b
											 where		a.PER_ID=$PER_ID and a.AL_CODE=b.AL_CODE
											 order by	a.ABI_ID ";							   
							$count_abilityhis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							if($count_abilityhis){
								$abilityhis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$abilityhis_count++;
									$AL_NAME = trim($data2[AL_NAME]);
									$ABI_DESC = trim($data2[ABI_DESC]);

									$border = "";
									$pdf->SetFont($font,'',14);
									$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
									
									$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
								
									$arr_data[0] = "<**1**>ด้าน:$AL_NAME ความสามารถพิเศษ:$ABI_DESC";
									$arr_data[1] = "<**1**>";

									$data_align = array("L","L");
					
									$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "angsa", "14", "", "000000", "");
									if (!$result) echo "****** error ****** add data to table at record count = 6 <br>";

								} // end while
								$pdf->close_tab(""); 
							}else{
								$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
								$pdf->Cell(200,7,"********** ไม่มีข้อมูล **********","LBR",1,'C');
							} // end if
						break;
					case "SERVICEHIS" :
							if(($pdf->h - $pdf->y - 10) < 30) $pdf->AddPage();
							$pdf->SetFont($fontb,'',14);
							$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
							$pdf->Cell(200,7,"6.  การปฏิบัติราชการพิเศษ ","TRBL",1,"L");
							$head_text1 = implode(",", $heading_text[$HISTORY_NAME]);
							$head_width1 = implode(",", $heading_width[$HISTORY_NAME]);
							$head_align1 = implode(",", $heading_align[$HISTORY_NAME]);
							$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "angsa", "14", "b", "0066CC", "EEEEFF", 0);
							if (!$result) echo "****** error ****** on open table for $HISTORY_NAME<br>";

							$SRH_STARTDATE = "";
							$SV_NAME = "";
							$SRH_DOCNO = "";
							$SRH_NOTE = "";
							
							$cmd = " select		a.SRH_STARTDATE, b.SV_NAME, a.SRH_DOCNO, a.SRH_NOTE, c.ORG_NAME, d.SRT_NAME
											 from			PER_SERVICEHIS a, PER_SERVICE b, PER_ORG c, PER_SERVICETITLE d
											 where		a.PER_ID=$PER_ID and a.SV_CODE=b.SV_CODE and a.ORG_ID=c.ORG_ID and a.SRT_CODE=d.SRT_CODE
											 order by	a.SRH_STARTDATE ";							   
							if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$count_servicehis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							if($count_servicehis){
								$servicehis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$servicehis_count++;
									if ($servicehis_count % 2 == 0) {
										$SRH_STARTDATE2 = show_date_format($data2[SRH_STARTDATE],$DATE_DISPLAY);
										$SV_NAME2 = trim($data2[SV_NAME]);
										$SRH_DOCNO2 = trim($data2[SRH_DOCNO]);
										$SRH_NOTE2 = trim($data2[SRH_NOTE]);
										$SRH_ORG2 = trim($data2[ORG_NAME]);
										$SRT_NAME2 = trim($data2[SRT_NAME]);
									} else {
										$SRH_STARTDATE1 = show_date_format($data2[SRH_STARTDATE],$DATE_DISPLAY);
										$SV_NAME1 = trim($data2[SV_NAME]);
										$SRH_DOCNO1 = trim($data2[SRH_DOCNO]);
										$SRH_NOTE1 = trim($data2[SRH_NOTE]);
										$SRH_ORG1 = trim($data2[ORG_NAME]);
										$SRT_NAME1 = trim($data2[SRT_NAME]);
									}

									if ($servicehis_count % 2 == 0) {
										$border = "";
										
										$arr_data[0] = "$SRH_STARTDATE1";
										$arr_data[1] = "ประเภท : $SV_NAME1   หัวข้อราชการพิเศษ : $SRT_NAME1  เอกสาร : $SRH_DOCNO1";
										$arr_data[2] = "$SRH_STARTDATE2";
										$arr_data[3] = "ประเภท : $SV_NAME2   หัวข้อราชการพิเศษ : $SRT_NAME2  เอกสาร : $SRH_DOCNO2";
										$data_align = array("L","L","L","L");
					
										$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "angsa", "14", "", "000000", "");
										if (!$result) echo "****** error ****** add data to table at record count = 6 <br>";
									} // end if ($servicehis_count % 2 == 0)
								} // end while
							}

							$cmd = " select		a.REH_YEAR, b.REW_NAME, a.REH_OTHER_PERFORMANCE
											 from			PER_REWARDHIS a, PER_REWARD b
											 where		a.PER_ID=$PER_ID and a.REW_CODE=b.REW_CODE(+)
											 order by	a.REH_YEAR ";							   
							$count_rewardhis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							if($count_rewardhis){
								$rewardhis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$rewardhis_count++;
									if ($rewardhis_count % 2 == 0) {
										$REH_YEAR2 = trim($data2[REH_YEAR]);
										$REW_NAME2 = trim($data2[REW_NAME]);
										if (!$REW_NAME2) $REW_NAME2 = trim($data2[REH_OTHER_PERFORMANCE]);
									} else {
										$REH_YEAR1 = trim($data2[REH_YEAR]);
										$REW_NAME1 = trim($data2[REW_NAME]);
										if (!$REW_NAME1) $REW_NAME1 = trim($data2[REH_OTHER_PERFORMANCE]);
									}

									$border = "";
									
									$arr_data[0] = "$REH_YEAR1";
									$arr_data[1] = "$REW_NAME1";
									$arr_data[2] = "$REH_YEAR2";
									$arr_data[3] = "$REW_NAME2";
									$data_align = array("L","L","L","L");
				
									$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "angsa", "14", "", "000000", "");
									if (!$result) echo "****** error ****** add data to table at record count = 6 <br>";
								} // end while
							}

							if($count_servicehis || $count_rewardhis){
								$pdf->close_tab(""); 
							}else{
								$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
								$pdf->Cell(200,7,"********** ไม่มีข้อมูล **********","LBR",1,'C');
							} // end if
						break;
					case "SPECIALSKILLHIS" :
							if(($pdf->h - $pdf->y - 10) < 30) $pdf->AddPage();
							$pdf->SetFont($fontb,'',14);
							$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
							$pdf->Cell(200,7,"7.  รายการอื่น ๆ ที่จำเป็น ","TRBL",1,"L");
							$head_text1 = "";	// ไม่พิมพ์หัว
							$head_width1 = implode(",", $heading_width[$HISTORY_NAME]);
							$head_align1 = implode(",", $heading_align[$HISTORY_NAME]);
							$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "angsa", "14", "b", "0066CC", "EEEEFF", 0);
							if (!$result) echo "****** error ****** on open table for $HISTORY_NAME<br>";

							$SS_NAME = "";
							$SPS_EMPHASIZE = "";
							
							$cmd = " select		b.SS_NAME, a.SPS_EMPHASIZE
											 from			PER_SPECIAL_SKILL a, PER_SPECIAL_SKILLGRP b
											 where		a.PER_ID=$PER_ID and a.SS_CODE=b.SS_CODE
											 order by	a.SPS_ID ";							   
							$count_specialskillhis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							if($count_specialskillhis){
								$specialskillhis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$specialskillhis_count++;
									$SS_NAME = trim($data2[SS_NAME]);
									$SPS_EMPHASIZE = trim($data2[SPS_EMPHASIZE]);
				
									$border = "";
									
									$arr_data = (array) null;
									$arr_data[0] = "<**1**>ความเชี่ยวชาญพิเศษ : $SS_NAME, $SPS_EMPHASIZE";
									$arr_data[1] = "<**1**>";
									$data_align = array("L","L");
					
									$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "angsa", "14", "", "000000", "");
									if (!$result) echo "****** error ****** add data to table at record count = 6 <br>";
								} // end while
							}

							$cmd = " select		NH_DATE, b.PN_NAME, a.NH_NAME, a.NH_SURNAME, c.PN_NAME as PN_NAME_NEW, a.NH_NAME_NEW, a.NH_SURNAME_NEW 
											 from			PER_NAMEHIS a, PER_PRENAME b, PER_PRENAME c
											 where		a.PER_ID=$PER_ID and a.PN_CODE=b.PN_CODE(+) and a.PN_CODE_NEW=c.PN_CODE(+)
											 order by	NH_DATE ";							   
							$count_namehis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							if($count_namehis){
								$namehis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$namehis_count++;
									$NH_DATE = show_date_format($data2[NH_DATE],$DATE_DISPLAY);
									$OLD_NAME = trim($data2[PN_NAME]).trim($data2[NH_NAME])." ".trim($data2[NH_SURNAME]);
									$NEW_NAME = trim($data2[PN_NAME_NEW]).trim($data2[NH_NAME_NEW])." ".trim($data2[NH_SURNAME_NEW]);
				
									$border = "";
									
									$arr_data = (array) null;
									$arr_data[0] = "<**1**>วันที่เปลี่ยน : $NH_DATE จาก $OLD_NAME เป็น $NEW_NAME";
									$arr_data[1] = "<**1**>";
									$data_align = array("L","L");
					
									$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "angsa", "14", "", "000000", "");
									if (!$result) echo "****** error ****** add data to table at record count = 6 <br>";
								} // end while
							}

							if($count_specialskillhis || $count_namehis){
								$pdf->close_tab(""); 
							}else{
								$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
								$pdf->Cell(200,7,"********** ไม่มีข้อมูล **********","LBR",1,'C');
							} // end if
						break;
					case "DECORATEHIS" :

							$imgcnt = count($arr_img);
							if ($imgcnt > 0) {
								$head_text1 = ",,,,,,";	// ไม่พิมพ์หัว
								$head_width1 = "29,29,28,28,28,29,29";
								$head_align1 = "C,C,C,C,C,C,C";
								$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "angsa", "14", "b", "0066CC", "EEEEFF", 0);
								if (!$result) echo "****** error ****** on open table for $HISTORY_NAME<br>";
								
								$start_pic = 0;
								if  ($imgcnt > 7) $start_pic = $imgcnt - 7;	// แถวแสดงได้สูงสุด 7 ภาพ เริ่มนับจากภาพล่าสุดลงมา 7 ภาพ
								else $imgcnt = 7;
								$image_y = $pdf->y+2;	$image_w = 25;		$image_h = 33;
								for($dupline=0; $dupline < 5; $dupline++) {	// ทำ merge row 5 บรรทัด เพื่อให้มีพื้นที่ สำหรับรูป
									$j = 0;	
//									echo "imgcnt=$imgcnt<br>";
									$arr_data = (array) null;
									for($i=$start_pic; $i  < $imgcnt; $i++) {
//										echo "img [$i]=".$arr_img[$i]."::(".strlen(trim($arr_img[$i])).")<br>";
										if (strlen(trim($arr_img[$i])) > 0) {
											if ($dupline==4)
												$arr_data[] = "<&&end&&><*img*".$arr_img[$i]."*img*>";
											else
												$arr_data[] = "<&&row&&><*img*".$arr_img[$i]."*img*>";
										} else {
											if ($dupline==4)
												$arr_data[] = "<&&end&&>";
											else
												$arr_data[] = "<&&row&&>";
										} // end if	
										$j++;
									}
//									echo "$dupline..img..".implode(",",$arr_data)."<br>";
									$lh=7;
									$data_align = array("C","C","C","C","C","C","C");
					
									$result = $pdf->add_data_tab($arr_data, $lh, "TRHL", $data_align, "angsa", "14", "", "000000", "");
									if (!$result) echo "****** error ****** add data to table at record count = 6 <br>";
								}
								$arr_data = (array) null;
								for($i=$start_pic; $i  < $imgcnt; $i++) {
									if($arr_img[$i])	$arr_data[] = "รูปที่ ".($i+1);
									else $arr_data[] = "";
								}
								$lh=7;
								$data_align = array("C","C","C","C","C","C","C");

//								echo "$dupline..text..".implode(",",$arr_data)."<br>";
								$result = $pdf->add_data_tab($arr_data, $lh, "RHL", $data_align, "angsa", "14", "", "000000", "");
								if (!$result) echo "****** error ****** add data to table at record count = 6 <br>";

								$arr_data = (array) null;
								for($i=$start_pic; $i  < $imgcnt; $i++) {
									if($arr_img[$i])	$arr_data[] = "พ.ศ.".substr($arr_imgsavedate[$i],0,4);
									else $arr_data[] = "";
								}
//								echo "$dupline..พศ..".implode(",",$arr_data)."<br>";
								$lh=7;
								$data_align = array("C","C","C","C","C","C","C");
				
								$result = $pdf->add_data_tab($arr_data, $lh, "RHBL", $data_align, "angsa", "14", "", "000000", "");
								if (!$result) echo "****** error ****** add data to table at record count = 6 <br>";

								$pdf->close_tab(""); 
							}
							
							$head_text1 = "";	// ไม่พิมพ์หัว
							$head_width1 = "200";
							$head_align1 = "L";
							$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "angsa", "14", "b", "0066CC", "EEEEFF", 0);
							if (!$result) echo "****** error ****** on open table for $HISTORY_NAME<br>";
							
							$arr_data = array("1. ชื่อ  ".$FULLNAME."   วัน เดือน ปี เกิด  ".$PER_BIRTHDATE);
							
							$lh=7;
							$data_align = array("L");
					
							$result = $pdf->add_data_tab($arr_data, $lh, "TRHBL", $data_align, "angsa", "14", "", "000000", "");
							if (!$result) echo "****** error ****** add data to table at record count = 6 <br>";
							
							$arr_data = array("2. ที่อยู่ปัจจุบัน ".($PER_ADD1 ? $PER_ADD1 : ""));
							
							$lh=7;
							$data_align = array("L");
					
							$result = $pdf->add_data_tab($arr_data, $lh, "TRHBL", $data_align, "angsa", "14", "", "000000", "");
							if (!$result) echo "****** error ****** add data to table at record count = 6 <br>";

							$arr_data = array("   ที่อยู่ตามทะเบียนบ้าน ".($PER_ADD2 ? $PER_ADD2 : ""));
							
							$lh=7;
							$data_align = array("L");
					
							$result = $pdf->add_data_tab($arr_data, $lh, "TRHBL", $data_align, "angsa", "14", "", "000000", "");
							if (!$result) echo "****** error ****** add data to table at record count = 6 <br>";

							$arr_data = array("3. เครื่องราชอิสริยาภรณ์ วันที่ได้รับ วันส่งคืน รวมทั้งเอกสารอ้างอิง");
							
							$lh=7;
							$data_align = array("L");
					
							$result = $pdf->add_data_tab($arr_data, $lh, "TRHBL", $data_align, "angsa", "14", "", "000000", "");
							if (!$result) echo "****** error ****** add data to table at record count = 6 <br>";

							$pdf->close_tab(""); 
							
							$head_text1 = "";	// ไม่พิมพ์หัว
							$head_width1 = "100,100";
							$head_align1 = "L,L";
							$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "angsa", "14", "b", "0066CC", "EEEEFF", 0);
							if (!$result) echo "****** error ****** on open table for $HISTORY_NAME<br>";
							
							for($i  = 0; $i < count($arr_DEH_SHOW); $i+=2) {
								if ($i==0 && $i >= count($arr_DEH_SHOW)-2) {
									$border = "TRBL";
								} else if ($i==0) {
									$border = "TRL";
								} else if ($i >= count($arr_DEH_SHOW)-2) {
									$border = "RBL";
								} else {
									$border = "RL";
								}
								$arr_data = array($arr_DEH_SHOW[$i], $arr_DEH_SHOW[$i+1]);
							
								$lh=7;
								$data_align = array("L", "L");
					
								$result = $pdf->add_data_tab($arr_data, $lh, "TRHBL", $data_align, "angsa", "14", "", "000000", "");
								if (!$result) echo "****** error ****** add data to table at record count = 6 <br>";

							}
							$pdf->close_tab(""); 

							break;
					case "ABSENTSUM" :
							if(($pdf->h - $pdf->y - 10) < 30) $pdf->AddPage();
							$pdf->SetFont($fontb,'',14);
							$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
							$pdf->Cell(200,7,"4. จำนวนวันลาหยุด ขาดราชการ มาสาย ",1,1,"L");

							$head_text1 = implode(",", $heading_text[$HISTORY_NAME]);
							$head_width1 = implode(",", $heading_width[$HISTORY_NAME]);
							$head_align1 = implode(",", $heading_align[$HISTORY_NAME]);
							$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "angsa", "14", "b", "0066CC", "EEEEFF", 0);
							if (!$result) echo "****** error ****** on open table for $HISTORY_NAME<br>";

							$cmd = " select		AS_YEAR, AB_CODE_01, AB_CODE_02, AB_CODE_03, AB_CODE_04, AB_CODE_05, AB_CODE_06, AB_CODE_07, AB_CODE_08, 
																AB_CODE_09, AB_CODE_10, AB_CODE_11, AB_CODE_12, AB_CODE_13, AB_CODE_14, AB_CODE_15
											 from			PER_ABSENTSUM 
											 where		PER_ID=$PER_ID
											 order by	AS_YEAR ";							   
							$count_absentsum = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$arr_AB_NAME = (array) null;
							if($count_absentsum){
								$absentsum_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$absentsum_count++;
									$AS_YEAR = trim($data2[AS_YEAR]);
//									echo "AS_YEAR=$AS_YEAR<br>";
									$AB_CODE_01 = $data2[AB_CODE_01];
									$AB_CODE_02 = $data2[AB_CODE_02];
									$AB_CODE_03 = $data2[AB_CODE_03];
									$AB_CODE_04 = $data2[AB_CODE_04];
									$AB_CODE_05 = $data2[AB_CODE_05];
									$AB_CODE_06 = $data2[AB_CODE_06];
									$AB_CODE_07 = $data2[AB_CODE_07];
									$AB_CODE_08 = $data2[AB_CODE_08];
									$AB_CODE_09 = $data2[AB_CODE_09];
									$AB_CODE_10 = $data2[AB_CODE_10];
									$AB_CODE_11 = $data2[AB_CODE_11];
									$AB_CODE_12 = $data2[AB_CODE_12];
									$AB_CODE_13 = $data2[AB_CODE_13];
									$AB_CODE_14 = $data2[AB_CODE_14];
									$AB_CODE_15 = $data2[AB_CODE_15];
									if (!$arr_AB_NAME[$AS_YEAR][$ABIDX]) $arr_AB_NAME[$ABS_YEAR][$ABIDX]=0;
									$arr_AB_NAME[$AS_YEAR][0] += $AB_CODE_01 + $AB_CODE_12;
									$arr_AB_NAME[$AS_YEAR][1] += $AB_CODE_02 + $AB_CODE_03 + $AB_CODE_04 + $AB_CODE_05 + $AB_CODE_06 + 
																										$AB_CODE_08 + $AB_CODE_09 + $AB_CODE_11 + $AB_CODE_14 + $AB_CODE_15;
									$arr_AB_NAME[$AS_YEAR][2] += $AB_CODE_10;
									$arr_AB_NAME[$AS_YEAR][3] += $AB_CODE_13;
									$arr_AB_NAME[$AS_YEAR][4] += $AB_CODE_07;
								}

								$border = "";
								$pdf->SetFont($font,'',14);
								$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

								$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;

								$arr_year = (array) null;
								foreach($arr_AB_NAME as $year => $sub_arr) {
//									echo "year=$year<br>";
									if ($year)
										$arr_year[] = $year;
								}
								$j = 1; $arr_data = (array) null; $data_align = (array) null;
								$numline = ceil(count($arr_year)/2);	// จำนวนบรรทัด สำหรับแสดง
								for($yidx = 0; $yidx < $numline; $yidx++) {
									$arr_data[] = $arr_year[$yidx];
									$data_align[] = "C";
									for($abidx = 0; $abidx < 5; $abidx++) {
										if ($arr_AB_NAME[$arr_year[$yidx]][$abidx]==0) $arr_data[] = "-";
										else $arr_data[] = $arr_AB_NAME[$arr_year[$yidx]][$abidx];
										$data_align[] = "C";
									}
									if (($numline+$yidx) < count($arr_year)) {
										$arr_data[] = $arr_year[$numline+$yidx];
										$data_align[] = "C";
										for($abidx = 0; $abidx < 5; $abidx++) {
											if ($arr_AB_NAME[$arr_year[$numline+$yidx]][$abidx]==0) $arr_data[] = "-";
											else $arr_data[] = $arr_AB_NAME[$arr_year[$numline+$yidx]][$abidx];
											$data_align[] = "C";
										}
										$lh=7;
						
										$result = $pdf->add_data_tab($arr_data, $lh, "TRHBL", $data_align, "angsa", "14", "", "000000", "");
										if (!$result) echo "****** error ****** add data to table at record year = $year <br>";
												
										$arr_data = (array) null; $data_align = (array) null;
									} else {
										$arr_data[] = "";
										$data_align[] = "C";
										$arr_data[] = "";
										$data_align[] = "C";
										$arr_data[] = "";
										$data_align[] = "C";
										$arr_data[] = "";
										$data_align[] = "C";
										$arr_data[] = "";
										$data_align[] = "C";
	
										$lh=7;
						
										$result = $pdf->add_data_tab($arr_data, $lh, "TRHBL", $data_align, "angsa", "14", "", "000000", "");
										if (!$result) echo "****** error ****** add data to table at record year = $year <br>";
									}
								}
							}else{
								$result = $pdf->add_text_line("********** ไม่มีข้อมูล **********", 7, "TRBL", "C", "", "14", "b", 0, 0);
								if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";
							} // end if
							$pdf->close_tab(""); 

						break;
					case "KPI" :
//							if(($pdf->h - $pdf->y - 10) < 30) $pdf->AddPage();
							$pdf->AddPage();
							$pdf->SetFont($fontb,'',14);
							$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
							$pdf->Cell(200,7,"สรุปผลการประเมินประสิทธิภาพและประสิทธิผลการปฏิบัติงานของข้าราชการของ $FULLNAME",1,1,"C");

							$head_text1 = implode(",", $heading_text[$HISTORY_NAME]);
							$head_width1 = implode(",", $heading_width[$HISTORY_NAME]);
							$head_align1 = implode(",", $heading_align[$HISTORY_NAME]);
							$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "angsa", "14", "b", "0066CC", "EEEEFF", 0);
							if (!$result) echo "****** error ****** on open table for $HISTORY_NAME<br>";

							$cmd = " select		KF_END_DATE, KF_CYCLE, TOTAL_SCORE, SALARY_REMARK1, SALARY_REMARK2
											 from			PER_KPI_FORM 
											 where		PER_ID=$PER_ID
											 order by	KF_END_DATE ";							   
							$count_kpi_form = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$arr_year = (array) null;
							$arr_rem = (array) null;
							$arr_AB_NAME = (array) null;
							if($count_kpi_form){
								$kpi_form_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$kpi_form_count++;
									$TMP_KF_END_DATE = substr($data2[KF_END_DATE], 0, 10);
									$TMP_KF_YEAR = substr($TMP_KF_END_DATE, 0, 4) + 543;
//									echo "TMP_KF_YEAR=$TMP_KF_YEAR<br>";
									$TMP_KF_CYCLE = $data2[KF_CYCLE] + 0;
									$TMP_TOTAL_SCORE = $data2[TOTAL_SCORE];
									$TMP_REMARK = trim($data2[SALARY_REMARK1])." ".trim($data2[SALARY_REMARK2]);
									if (array_search($TMP_KF_YEAR, $arr_year) === false) 	{
										$arr_year[] = $TMP_KF_YEAR;
										$arr_rem[] = $TMP_REMARK;
									}
									if ($TMP_KF_CYCLE==1) {
										if ($TMP_TOTAL_SCORE >= 90) 
											$arr_AB_NAME[$TMP_KF_YEAR][0] = $TMP_TOTAL_SCORE;
										elseif ($TMP_TOTAL_SCORE >= 60) 
											$arr_AB_NAME[$TMP_KF_YEAR][1] = $TMP_TOTAL_SCORE;
										elseif ($TMP_TOTAL_SCORE < 60) 
											$arr_AB_NAME[$TMP_KF_YEAR][2] = $TMP_TOTAL_SCORE;
									} elseif ($TMP_KF_CYCLE==2) {
										if ($TMP_TOTAL_SCORE >= 90) 
											$arr_AB_NAME[$TMP_KF_YEAR][3] = $TMP_TOTAL_SCORE;
										elseif ($TMP_TOTAL_SCORE >= 60) 
											$arr_AB_NAME[$TMP_KF_YEAR][4] = $TMP_TOTAL_SCORE;
										elseif ($TMP_TOTAL_SCORE < 60) 
											$arr_AB_NAME[$TMP_KF_YEAR][5] = $TMP_TOTAL_SCORE;
									}
//									echo "TMP_KF_YEAR=$TMP_KF_YEAR  รอบ=$TMP_KF_CYCLE  คะแนน=$TMP_TOTAL_SCORE<br>";
								}

								$border = "";
								$pdf->SetFont($font,'',14);
								$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

								$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;

								$numline = count($arr_year);	// จำนวนบรรทัด สำหรับแสดง
								for($yidx = 0; $yidx < $numline; $yidx++) {
									$arr_data = (array) null; $data_align = (array) null;
									$arr_data[] = $arr_year[$yidx];
//									echo "arr_year [$yidx]--".$arr_year[$yidx]."<br>";
									$data_align[] = "C";
									for($abidx = 0; $abidx < 6; $abidx++) {
//										echo "yidx=$yidx  year=".$arr_year[$yidx]."  รอบ=$abidx  คะแนน=".$arr_AB_NAME[$arr_year[$yidx]][$abidx]."  remark=".$arr_rem[$yidx]."<br>";
										if ($arr_AB_NAME[$arr_year[$yidx]][$abidx]==0) $arr_data[] = "-";
										else $arr_data[] = $arr_AB_NAME[$arr_year[$yidx]][$abidx];
										$data_align[] = "C";
									}
									$arr_data[] = $arr_rem[$yidx];
									$lh=7;
						
									$result = $pdf->add_data_tab($arr_data, $lh, "TRHBL", $data_align, "angsa", "14", "", "000000", "");
									if (!$result) echo "****** error ****** add data to table at record year = $year <br>";
								}
							}else{
								$result = $pdf->add_text_line("********** ไม่มีข้อมูล **********", 7, "TRBL", "C", "", "14", "b", 0, 0);
								if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";
							} // end if
							$pdf->close_tab(""); 

						break;
				} // end switch
			} // end for
			
			if($data_count < $count_data) $pdf->AddPage();
		} // end while
	}else{
		$pdf->SetFont($fontb,'',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(200,10,"********** ไม่มีข้อมูล **********",0,1,'C');
	} // end if

	$pdf->close();
	$pdf->Output();
?>