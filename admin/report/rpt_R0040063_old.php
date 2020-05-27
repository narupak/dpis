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
	$arr_history_sort = array("EDUCATE", "PUNISHMENT", "NOTPAID", "POSITIONHIS", "", "ABILITY", "SERVICEHIS", "SPECIALSKILLHIS", "DECORATEHIS", "ABSENTHIS"); 
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
	
	$heading_width[POSITIONHIS][0] = "30";
	$heading_width[POSITIONHIS][1] = "90";      
	$heading_width[POSITIONHIS][2] = "16";
	$heading_width[POSITIONHIS][3] = "16";
	$heading_width[POSITIONHIS][4] = "16";
	$heading_width[POSITIONHIS][5] = "32";

	$heading_width[PUNISHMENT][0] = "45";
	$heading_width[PUNISHMENT][1] = "110";
	$heading_width[PUNISHMENT][2] = "45";

	$heading_width[NOTPAID][0] = "45";
	$heading_width[NOTPAID][1] = "110";
	$heading_width[NOTPAID][2] = "45";
	
	$heading_width[ABILITY][0] = "100";
	$heading_width[ABILITY][1] = "100";
	
	$heading_width[SERVICEHIS][0] = "20";
	$heading_width[SERVICEHIS][1] = "80";
	$heading_width[SERVICEHIS][2] = "20";
	$heading_width[SERVICEHIS][3] = "80";

	$heading_width[SPECIALSKILLHIS][0] = "80";
	$heading_width[SPECIALSKILLHIS][1] = "120";

	$heading_width[DECORATEHIS][0] = "50";
	$heading_width[DECORATEHIS][1] = "54";
	$heading_width[DECORATEHIS][2] = "32";
	$heading_width[DECORATEHIS][3] = "32";
	$heading_width[DECORATEHIS][4] = "32";

	$heading_width[ABSENTHIS][0] = "20";
	$heading_width[ABSENTHIS][1] = "16";
	$heading_width[ABSENTHIS][2] = "16";
	$heading_width[ABSENTHIS][3] = "16";
	$heading_width[ABSENTHIS][4] = "16";
	$heading_width[ABSENTHIS][5] = "16";

function print_header($HISTORY_NAME){
		global $pdf, $heading_width, $SESS_USERID;
		
		$pdf->SetFont($font,'',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));

		switch($HISTORY_NAME){
			case "EDUCATE" :
				$pdf->Cell($heading_width[$HISTORY_NAME][0] ,7,"สถานศึกษา",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][1] ,7,"ตั้งแต่ - ถึง",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][2] ,7,"วุฒิที่ได้รับ/สาขา",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][3] ,7,"สถานที่",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][4] ,7,"ตั้งแต่ - ถึง",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][5] ,7,"หลักสูตร/รุ่นที่",'LTBR',1,'C',1);
				break;
			case "POSITIONHIS" :
				$pdf->Cell($heading_width[POSITIONHIS][0],7,"วัน เดือน ปี",'LTR',0,'C',1);
				$pdf->Cell($heading_width[POSITIONHIS][1] ,7,"ตำแหน่ง",'LTR',0,'C',1);
				$pdf->Cell($heading_width[POSITIONHIS][2] ,7,"เลขที่",'LTR',0,'C',1);
				$pdf->Cell($heading_width[POSITIONHIS][3] ,7,"ระดับ",'LTR',0,'C',1);
				$pdf->Cell($heading_width[POSITIONHIS][4] ,7,"อัตรา",'LTR',0,'C',1);
				$pdf->Cell($heading_width[POSITIONHIS][5] ,7,"เอกสารอ้างอิง",'LTR',1,'C',1);
			
				$pdf->Cell($heading_width[POSITIONHIS][0] ,7,"",'LBR',0,'C',1);
				$pdf->Cell($heading_width[POSITIONHIS][1] ,7,"",'LBR',0,'C',1);
				$pdf->Cell($heading_width[POSITIONHIS][2] ,7,"ตำแหน่ง",'LBR',0,'C',1);
				$pdf->Cell($heading_width[POSITIONHIS][3] ,7,"",'LBR',0,'C',1);
				$pdf->Cell($heading_width[POSITIONHIS][4] ,7,"เงินเดือน",'LBR',0,'C',1);
//				$pdf->SetTextColor(hexdec("E4"),hexdec("E4"),hexdec("E4"));//สีเทา
				$pdf->Cell($heading_width[POSITIONHIS][5] ,7,$SESS_USERID,'LBR',1,'C',1);
//				$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
				// end set head
				break;
			case "PUNISHMENT" :
				$pdf->Cell($heading_width[PUNISHMENT][0] ,7,"พ.ศ.",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[PUNISHMENT][1] ,7,"รายการ",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[PUNISHMENT][2] ,7,"เอกสารอ้างอิง",'LTBR',1,'C',1);
				break;
			case "NOTPAID" :
				$pdf->Cell($heading_width[NOTPAID][0] ,7,"ตั้งแต่-ถึง",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[NOTPAID][1] ,7,"รายการ",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[NOTPAID][2] ,7,"เอกสารอ้างอิง",'LTBR',1,'C',1);
				break;
			case "ABILITY" :
				$pdf->Cell($heading_width[$HISTORY_NAME][0] ,7,"ด้านความสามารถพิเศษ",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][1] ,7,"ความสามารถพิเศษ",'LTBR',1,'C',1);
				break;			
			case "SERVICEHIS" :
				$pdf->Cell($heading_width[$HISTORY_NAME][0] ,7,"พ.ศ.",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][1] ,7,"รายการ",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][2] ,7,"พ.ศ.",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][3] ,7,"รายการ",'LTBR',1,'C',1);
//				$pdf->Cell($heading_width[$HISTORY_NAME][1] ,7,"ประเภทราชการพิเศษ",'LTBR',0,'C',1);
//				$pdf->Cell($heading_width[$HISTORY_NAME][2] ,7,"หัวข้อราชการพิเศษ",'LTBR',0,'C',1);
//				$pdf->Cell($heading_width[$HISTORY_NAME][3] ,7,"หนังสือ",'LTBR',1,'C',1);
				break;			
			case "SPECIALSKILLHIS" :
				$pdf->Cell($heading_width[$HISTORY_NAME][0] ,7,"ด้านความเชี่ยวชาญพิเศษ",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][1] ,7,"เน้นทาง",'LTBR',1,'C',1);
				break;			
			case "DECORATEHIS" :
				$pdf->Cell($heading_width[$HISTORY_NAME][0] ,7,"(1)",'LTR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][1] ,7,"(2)",'LTR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][2] ,7,"(3)",'LTR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][3] ,7,"(4)",'LTR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][4] ,7,"(5)",'LTR',1,'C',1);
				
				//แถวที่ 2
				$pdf->Cell($heading_width[$HISTORY_NAME][0] ,7,"ตำแหน่ง (อดีต-ปัจจุบัน",'LBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][1] ,7,"ลำดับเครื่องราชฯ ที่ได้รับ",'LBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][2] ,7,"ว.ด.ป.",'LBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][3] ,7,"เครื่องราชฯ",'LBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][4] ,7,"เครื่องราชฯ",'LBR',1,'C',1);
				
				//แถวที่ 3
				$pdf->Cell($heading_width[$HISTORY_NAME][0] ,7,"เฉพาะปีที่ได้รับ",'LBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][1] ,7,"พระราชทานแล้ว",'LBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][2] ,7,"ที่ได้รับ",'LBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][3] ,7,"ตาม (2)",'LBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][4] ,7,"ตาม (2)",'LBR',1,'C',1);
				
				//แถวที่ 4
				$pdf->Cell($heading_width[$HISTORY_NAME][0] ,7,"พระราชทานเครื่องราชฯ)",'LBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][1] ,7,"จากชั้นรองไปชั้นสูงตาม (1)",'LBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][2] ,7,"พระราชทานตาม (2)",'LBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][3] ,7,"รับมอบเมื่อ ",'LBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][4] ,7,"ส่งคืนเมื่อ ",'LBR',1,'C',1);
				break;			
			case "ABSENTHIS" :
				$pdf->Cell($heading_width[$HISTORY_NAME][0] ,7,"",'LTR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][1] ,7,"",'LTR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][2] ,7,"ลากิจ",'LTR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][3] ,7,"",'LTR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][4] ,7,"ขาด",'LTR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][5] ,7,"ลา",'LTR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][0] ,7,"",'LTR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][1] ,7,"",'LTR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][2] ,7,"ลากิจ",'LTR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][3] ,7,"",'LTR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][4] ,7,"ขาด",'LTR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][5] ,7,"ลา",'LTR',1,'C',1);

				$pdf->Cell($heading_width[$HISTORY_NAME][0] ,7,"พ.ศ.",'LR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][1] ,7,"ลาป่วย",'LR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][2] ,7,"และ",'LR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][3] ,7,"สาย",'LR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][4] ,7,"ราช",'LR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][5] ,7,"ศึกษา",'LR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][0] ,7,"พ.ศ.",'LR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][1] ,7,"ลาป่วย",'LR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][2] ,7,"และ",'LR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][3] ,7,"สาย",'LR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][4] ,7,"ราช",'LR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][5] ,7,"ศึกษา",'LR',1,'C',1);

				$pdf->Cell($heading_width[$HISTORY_NAME][0] ,7,"",'LBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][1] ,7,"",'LBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][2] ,7,"พักผ่อน",'LBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][3] ,7,"",'LBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][4] ,7,"การ",'LBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][5] ,7,"ต่อ",'LBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][0] ,7,"",'LBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][1] ,7,"",'LBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][2] ,7,"พักผ่อน",'LBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][3] ,7,"",'LBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][4] ,7,"การ",'LBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][5] ,7,"ต่อ",'LBR',1,'C',1);
break;			
		} // end switch
	} // function
	
	function print_footer($LEVEL_NAME) {
		global $pdf, $start_x, $start_y, $heading_width, $max_y, $FULLNAME, $FULL_LEVEL_NAME, $MINISTRY_NAME, $DEPARTMENT_NAME, $page_no;
		
		$pdf->x = $start_x + $heading_width[POSITIONHIS][0] + $heading_width[POSITIONHIS][1] + $heading_width[POSITIONHIS][2] + $heading_width[POSITIONHIS][3] + $heading_width[POSITIONHIS][4] + $heading_width[POSITIONHIS][5];

		$line_start_y = $start_y;		$line_start_x = $start_x;
		$line_end_y = $max_y;		$line_end_x = $pdf->x;
		$pdf->SetDrawColor(hexdec("00"),hexdec("00"),hexdec("00")); //สีดำ
		$pdf->Line($line_start_x, $line_end_y, $line_end_x, $line_end_y);

//		$pdf->Cell(5, 3, "", 0, 1, 'C', 0); // เว้น 1 บรรทัด สูง 3

		$pdf->x = $start_x-$heading_width[0];
		$pdf->SetFont($font,'',14);

		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00")); //สีดำ

		$prt_text_footer = "ชื่อ ".$FULLNAME."..".$LEVEL_NAME."..".$MINISTRY_NAME."..".$DEPARTMENT_NAME;
		$pdf->Cell(168 ,7,$prt_text_footer,1,0,"L");
		$pdf->Cell(32 ,7,"หน้า [$page_no]",1,1,"R");
	} // function footer

	if($DPISDB=="odbc"){
		$cmd = " select			a.PER_ID, a.LEVEL_NO, f.LEVEL_NAME, b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_BIRTHDATE, 
											a.PER_SALARY, a.PER_MGTSALARY, a.PER_STARTDATE, a.PER_RETIREDATE,a.PER_OCCUPYDATE,
											a.PER_TYPE, a.PER_CARDNO,a.POS_ID, a.POEM_ID, a.POEMS_ID,a.POT_ID,
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
				 		order by		a.PER_NAME, a.PER_SURNAME ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = "select			a.PER_ID, a.LEVEL_NO, f.LEVEL_NAME, b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_BIRTHDATE, 
											a.PER_SALARY, a.PER_MGTSALARY, a.PER_STARTDATE, a.PER_RETIREDATE,a.PER_OCCUPYDATE,
											a.PER_TYPE, a.PER_CARDNO,a.POS_ID, a.POEM_ID, a.POEMS_ID,a.POT_ID,
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
						order by		a.PER_NAME, a.PER_SURNAME ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			a.PER_ID, a.LEVEL_NO, f.LEVEL_NAME, b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_BIRTHDATE, 
											a.PER_SALARY, a.PER_MGTSALARY, a.PER_STARTDATE, a.PER_RETIREDATE,a.PER_OCCUPYDATE,
											a.PER_TYPE, a.PER_CARDNO,a.POS_ID, a.POEM_ID, a.POEMS_ID,a.POT_ID,
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
				 		order by  a.PER_NAME, a.PER_SURNAME ";
	}
//	echo "$cmd<\br>";
	$count_data = $db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();

	if($count_data){
		$data_count = 0;
		while($data = $db_dpis->get_array()){
			$data_count++;
			$PER_ID = $data[PER_ID];
			$PER_TYPE = $data[PER_TYPE];
			$PER_OFFNO = $data[PER_OFFNO];
			
			if($PER_TYPE==1){
				$POS_ID = $data[POS_ID];
				$POS_NO = $data[POS_NO_NAME].$data[POS_NO];
				$PL_CODE = trim($data[PL_CODE]);
				$PT_CODE = trim($data[PT_CODE]);
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

			$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $ORG_ID ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$ORG_NAME = trim($data2[ORG_NAME]);
			
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $ORG_ID_1 ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$ORG_NAME_1 = trim($data2[ORG_NAME]);

			$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $ORG_ID_2 ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$ORG_NAME_2 = trim($data2[ORG_NAME]);

			$PV_CODE = trim($data[PV_CODE]);
			$cmd = " select PV_NAME from PER_PROVINCE where PV_CODE = $PV_CODE ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PV_NAME = trim($data2[PV_NAME]);
			$PER_ADD1 = trim($data[PER_ADD1]);

			$cmd = " select a.*, b.AP_NAME, c.PV_NAME from PER_ADDRESS a left join PER_AMPHUR b on (a.AP_CODE=b.AP_CODE) left join PER_PROVINCE c on (a.PV_CODE=c.PV_CODE) where PER_ID = $PER_ID ";
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
						$arr_imgshow[] = 1;
						$PER_PICSAVEDATE = trim($data2[PER_PICSAVEDATE]);
					} else {
						$arr_img[] = "../".$PIC_PATH.($TMP_PER_CARDNO=="NULL"?$PER_GENNAME:$TMP_PER_CARDNO)."-".$T_PIC_SEQ.".jpg";
						$arr_imgsavedate[] = trim($data2[PER_PICSAVEDATE]);
						$arr_imgshow[] = 0;
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
			$PN_CODE_F = trim($data[PN_CODE_F]);
			$cmd = "select PN_NAME from PER_PRENAME WHERE PN_CODE = $PN_CODE_F ORDER BY PN_CODE";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$PN_NAME_F=trim($data_dpis2[PN_NAME]);

			$PER_FATHERNAME = trim($data[PER_FATHERNAME]);
			$PER_FATHERSURNAME = trim($data[PER_FATHERSURNAME]);
			$FATHERNAME = ($PN_NAME_F)."$PER_FATHERNAME $PER_FATHERSURNAME";
		
			$PN_CODE_M = trim($data[PN_CODE_M]);	
			$cmd = "select PN_NAME from PER_PRENAME WHERE PN_CODE = $PN_CODE_M ORDER BY PN_CODE";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$PN_NAME_M=trim($data_dpis2[PN_NAME]);
					
			$PER_MOTHERNAME = trim($data[PER_MOTHERNAME]);
			$PER_MOTHERSURNAME = trim($data[PER_MOTHERSURNAME]);
			$MOTHERNAME = ($PN_NAME_M)."$PER_MOTHERNAME $PER_MOTHERSURNAME";

			// =====  ข้อมูลคู่สมรส =====
			$cmd = "	select 	MAH_NAME 		from		PER_MARRHIS 
							where	PER_ID=$PER_ID 	order by	MAH_SEQ desc ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$SHOW_SPOUSE = trim($data_dpis2[MAH_NAME]);

			// เครื่องราชฯ
			if($DPISDB=="odbc"){
				$cmd = " select			b.DC_NAME, a.DEH_DATE, a.DEH_GAZETTE
								 from			PER_DECORATEHIS a, PER_DECORATION b
								 where		a.PER_ID=$PER_ID and a.DC_CODE=b.DC_CODE and DC_TYPE != 3
								 order by		a.DEH_DATE ";							   
			}elseif($DPISDB=="oci8"){
				$cmd = " select			b.DC_NAME, a.DEH_DATE, a.DEH_GAZETTE
								 from			PER_DECORATEHIS a, PER_DECORATION b
								 where		a.PER_ID=$PER_ID and a.DC_CODE=b.DC_CODE and DC_TYPE != 3
								 order by		a.DEH_DATE ";							   
			}elseif($DPISDB=="mysql"){
				$cmd = " select			b.DC_NAME, a.DEH_DATE, a.DEH_GAZETTE
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
					$DEH_GAZETTE = show_date_format(trim($data2[DEH_GAZETTE]),3);
					if ($DEH_SHOW) {
						$DEH_SHOW = "$DEH_SHOW,  ปี $DEH_YEAR1 $DC_NAME";
//						$DEH_SHOW1 = "$DEH_SHOW1, $DC_NAME รับพระราชทานเมื่อ $DEH_DATE1 ";
					} else {
						$DEH_SHOW = "ปี $DEH_YEAR1 $DC_NAME";
//						$DEH_SHOW1 = "$DC_NAME รับพระราชทานเมื่อ $DEH_DATE1 ";
					}
					$arr_DEH_SHOW[] = "$DC_NAME รับพระราชทานเมื่อ $DEH_DATE1 ";
				} // end while
			} // end if ($count_decoratehis)
			//------------------------------------------------------------------------------------------

			$pdf->SetFont($fontb,'',14);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

			$pdf->Cell(200,7,"11. การได้รับโทษทางวินัย ",1,1,"C");

			print_header("PUNISHMENT");

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
	
					$border = "";
					$pdf->SetFont($font,'',14);
					$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
									
					$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
							
//					$pdf->MultiCell($heading_width[PUNISHMENT][0], 7, "$CR_NAME", $border, "L");
					$pdf->MultiCell($heading_width[PUNISHMENT][0], 7, "$PUN_DURATION", $border, "L");
					if($pdf->y > $max_y) $max_y = $pdf->y;
					$pdf->x = $start_x + $heading_width[PUNISHMENT][0];
					$pdf->y = $start_y;

					$pdf->MultiCell($heading_width[PUNISHMENT][1], 7, "$CRD_NAME", $border, "L");
					if($pdf->y > $max_y) $max_y = $pdf->y;
					$pdf->x = $start_x + $heading_width[PUNISHMENT][0] + $heading_width[PUNISHMENT][1];
					$pdf->y = $start_y;

					$pdf->MultiCell($heading_width[PUNISHMENT][2], 7, "$PUN_REF", $border, "L");
					if($pdf->y > $max_y) $max_y = $pdf->y;
					$pdf->x = $start_x + $heading_width[PUNISHMENT][0] + $heading_width[PUNISHMENT][1] + $heading_width[PUNISHMENT][2];
					$pdf->y = $start_y;

					//================= Draw Border Line ====================
					$line_start_y = $start_y;		$line_start_x = $start_x;
					$line_end_y = $max_y;		$line_end_x = $start_x;
					$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				
					for($i=0; $i<=2; $i++){
						$line_start_y = $start_y;		$line_start_x += $heading_width[PUNISHMENT][$i];
						$line_end_y = $max_y;		$line_end_x += $heading_width[PUNISHMENT][$i];
//						$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
					} // end for
					//====================================================
				
					if(($pdf->h - $max_y - 10) < 10){ 
						$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
						if($punishmenthis_count < $count_punishmenthis){
							$pdf->AddPage();
							print_header("PUNISHMENT");
							$max_y = $pdf->y;
						} // end if
					}else{
						if($punishmenthis_count == $count_punishmenthis) $pdf->Line($start_x, $max_y, $pdf->x, $max_y);
					} // end if
					$pdf->x = $start_x;			$pdf->y = $max_y;
				} // end while
			} else {
				$border = "";
				$pdf->SetFont($font,'',14);
				$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
				$pdf->Cell($heading_width[PUNISHMENT][0] ,7,"",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[PUNISHMENT][1] ,7,"",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[PUNISHMENT][2] ,7,"",'LTBR',1,'C',1);
				$pdf->Cell($heading_width[PUNISHMENT][0] ,7,"",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[PUNISHMENT][1] ,7,"",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[PUNISHMENT][2] ,7,"",'LTBR',1,'C',1);
			}

			if(($pdf->h - $pdf->y - 10) < 30) $pdf->AddPage();
			$pdf->SetFont($fontb,'',14);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
			$pdf->Cell(200,7,"12. วันที่ไม่ได้รับเงินเดือน หรือวันที่ได้รับเงินเดือนไม่เต็ม หรือวันที่มิได้ประจำปฏิบัติหน้าที่อยู่ในเขตที่ได้มีประกาศใช้กฎอัยการศึก",1,1,"C");

			print_header("NOTPAID");
			
			$pdf->SetFont($fontb,'',14);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
			$pdf->Cell($heading_width[NOTPAID][0] ,7,"",'LTBR',0,'C',1);
			$pdf->Cell($heading_width[NOTPAID][1] ,7,"",'LTBR',0,'C',1);
//			$pdf->SetTextColor(hexdec("CD"),hexdec("CD"),hexdec("CD"));//สีเทาเข้มขึ้น
			$pdf->Cell($heading_width[NOTPAID][2] ,7,$SESS_USERID,'LTBR',1,'C',1);
//			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
			$pdf->Cell($heading_width[NOTPAID][0] ,7,"",'LTBR',0,'C',1);
			$pdf->Cell($heading_width[NOTPAID][1] ,7,"",'LTBR',0,'C',1);
			$pdf->Cell($heading_width[NOTPAID][2] ,7,"",'LTBR',1,'C',1);

			$pdf->Cell(65 ,7,($MINISTRY_NAME?$MINISTRY_NAME:""),"LTB",0,"L");
			$pdf->Cell(70 ,7,($DEPARTMENT_NAME?$DEPARTMENT_NAME:""),"TB",0,"L");
			$pdf->Cell(65 ,7,"ก.พ.7","TBR",1,"R");

			$this_y = $pdf->y;
			$this_x = $pdf->x;
			if($img_file){
				$image_x = ($pdf->x + 155);		$image_y = $pdf->y;		$image_w = 27;		$image_h = 35;
//				echo "img_file=$img_file<br>";
				$pdf->Image($img_file, $image_x, $image_y, $image_w, $image_h);
			} // end if
//			echo "af image<br>";
			$pdf->x = $this_x;
			$pdf->y = $this_y;
			$pdf->Cell(65 ,7,"1. ชื่อ  ".$FULLNAME,1,0,"L");
			$pdf->Cell(70 ,7,"5. ชื่อบิดา  ".($FATHERNAME?$FATHERNAME:"-"),1,0,"L");
			$pdf->Cell(65 ,7,"","LTR",1,"L");
			
			$pdf->Cell(65 ,7,"2. วัน เดือน ปี เกิด  ".$PER_BIRTHDATE,1,0,"L");
			$pdf->Cell(70 ,7,"6. ชื่อมารดา  ".($MOTHERNAME?$MOTHERNAME:"-"),1,0,"L");
			$pdf->Cell(65 ,7,"","LR",1,"L");

			$pdf->Cell(65 ,7,"3. วันครบเกษียณอายุ ".$PER_RETIREDATE,1,0,"L");
			$pdf->Cell(70 ,7,"7. วันสั่งบรรจุ  ".$PER_STARTDATE,1,0,"L");
			$pdf->Cell(65 ,7,"","LR",1,"L");

			$pdf->Cell(65 ,7,"4. ชื่อคู่สมรส ".($SHOW_SPOUSE?$SHOW_SPOUSE:"-"),1,0,"L");
			$pdf->Cell(70 ,7,"8. วันเริ่มปฏิบัติราชการ  ".$PER_STARTDATE,1,0,"L");
			$pdf->Cell(65 ,7,"","LR",1,"L");

			$pdf->Cell(65 ,7,"    ภูมิลำเนา ".($PV_NAME?$PV_NAME:"-"),1,0,"L");
			$pdf->Cell(70 ,7,"9. ประเภทข้าราชการ ".$OT_NAME,1,0,"L");
			$pdf->Cell(65 ,7,"","LBR",1,"L");

			$pdf->Cell(135 ,7,"เครื่องราชอิสริยาภรณ์",1,0,"C");
			$pdf->Cell(65 ,7,"เลขประจำตัวประชาชน : ".card_no_format($PER_CARDNO,1),1,1,"L");
			
			$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
			$border = "";
			$pdf->MultiCell(135, 7, $DEH_SHOW, $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + 135;
			$pdf->y = $start_y;

			$pdf->MultiCell(65, 7, "เลขประจำตัวข้าราชการ : $PER_OFFNO", $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$line_start_y = $start_y;		$line_start_x = $start_x;
			$line_end_y = $max_y;		$line_end_x = $pdf->x;
			$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);

			$line_start_y = $start_y;		$line_start_x = $start_x+135;
			$line_end_y = $max_y;		$line_end_x = $start_x+135;
			$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
			$line_start_y = $start_y;		$line_start_x = $start_x+200;
			$line_end_y = $max_y;		$line_end_x = $start_x+200;
			$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);

			$pdf->x = $start_x;
			$pdf->y = $max_y;
			
			$pdf->AutoPageBreak = false;

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
					// เฉพาะ EDUCATE และ TRAINING
						if(($pdf->h - $pdf->y - 10) < 30) $pdf->AddPage();
						$pdf->SetFont($fontb,'',14);
						$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
//						if($pdf->y != $page_start_y) $pdf->Cell(200,7,"",1,1,"L");
						$pdf->Cell(200,7,"10. ประวัติการศึกษา ฝึกอบรมและดูงาน",1,1,"C");
						$pdf->Cell(95,7,"ประวัติการศึกษา",1,0,"C");
						$pdf->Cell(105,7,"ประวัติการฝึกอบรมและดูงาน",1,1,"C");
						
						print_header($HISTORY_NAME);
							
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
								$row_count = 0;
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
							$row_count = 0;
							while($data2 = $db_dpis2->get_array()){
									$TRN_STARTDATE = show_date_format($data2[TRN_STARTDATE],2);
									$TRN_ENDDATE = show_date_format($data2[TRN_ENDDATE],2);
									if(trim($TRN_STARTDATE) && trim($TRN_ENDDATE)){
										$TRN_DURATION = $TRN_STARTDATE." - "."\n".$TRN_ENDDATE;
										if($TRN_STARTDATE == $TRN_ENDDATE) $TRN_DURATION = "$TRN_STARTDATE";
									}
									$arr_content[$row_count][trn_duration]=$TRN_DURATION;
									$TRN_ORG = trim($data2[TRN_ORG]);
									$TR_NAME = trim($data2[TR_NAME]);				
									if (!$TR_NAME || $TR_NAME=="อื่นๆ") $TR_NAME = trim($data2[TRN_COURSE_NAME]);
									//if($TR_NAME!=""){	$TR_NAME = str_replace("&quot;",""",$TR_NAME);		}
									$TRN_PLACE = trim($data2[TRN_PLACE]);
									$TRN_FUND = trim($data2[TRN_FUND]);
									$TRN_NO = trim($data2[TRN_NO]);
									if($TRN_NO && $TR_NAME) $TR_NAME .= " รุ่นที่ $TRN_NO";
									$arr_content[$row_count][tr_name] = $TR_NAME;
									$arr_content[$row_count][trn_org] = $TRN_ORG;
									$arr_content[$row_count][trn_place] = $TRN_PLACE;
									$arr_content[$row_count][trn_fund] = $TRN_FUND;
									$row_count++;
								} // end while data2 for TRAINING

								for($row_count=0; $row_count < count($arr_content); $row_count++) {
									$border = "";
									$pdf->SetFont($font,'',14);
									$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
								
									$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
									// พิมพ์ EDU
									$pdf->MultiCellThaiCut($heading_width[$HISTORY_NAME][0], 7, $arr_content[$row_count][ins_name], $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0];
									$pdf->y = $start_y;
									$pdf->MultiCellThaiCut($heading_width[$HISTORY_NAME][1], 7, $arr_content[$row_count][edu_period], $border, "R");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1];
									$pdf->y = $start_y;

									$pdf->MultiCellThaiCut($heading_width[$HISTORY_NAME][2], 7, $arr_content[$row_count][en_name]."  ".$arr_content[$row_count][em_name], $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1] + $heading_width[$HISTORY_NAME][2];
									$pdf->y = $start_y;
									
									// พิมพ์ TRN
									$pdf->MultiCellThaiCut($heading_width[$HISTORY_NAME][3], 7, $arr_content[$row_count][trn_place], $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1] + $heading_width[$HISTORY_NAME][2] + $heading_width[$HISTORY_NAME][3];
									$pdf->y = $start_y;

									$pdf->MultiCellThaiCut($heading_width[$HISTORY_NAME][4], 7, $arr_content[$row_count][trn_duration], $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1] + $heading_width[$HISTORY_NAME][2] + $heading_width[$HISTORY_NAME][3] + $heading_width[$HISTORY_NAME][4];
									$pdf->y = $start_y;

									$pdf->MultiCellThaiCut($heading_width[$HISTORY_NAME][5], 7, $arr_content[$row_count][tr_name], $border, "L");
									
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1] + $heading_width[$HISTORY_NAME][2] + $heading_width[$HISTORY_NAME][3] + $heading_width[$HISTORY_NAME][4] + $heading_width[$HISTORY_NAME][5];
									$pdf->y = $start_y;
									
									//================= Draw Border Line ====================
									$pdf->SetDrawColor(hexdec("00"),hexdec("00"),hexdec("00")); //สีดำ
									$line_start_y = $start_y;		$line_start_x = $start_x;
									$line_end_y = $pdf->y;		$line_end_x = $pdf->x;
									$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y); // เส้นนอน

									$line_start_y = $start_y;
									$line_end_y = $max_y;
									$line_start_x = $start_x;
									$line_end_x = $start_x;
									$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y); // เส้นขวางเส้นแรก
									
									for($i=0; $i<=5; $i++) {
										$line_start_y = $start_y;		$line_start_x += $heading_width[$HISTORY_NAME][$i];
										$line_end_y = $max_y;		$line_end_x += $heading_width[$HISTORY_NAME][$i];
										$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y); // เส้นขวางที่เหลือ
									} // end for
									//====================================================
				
									if(($pdf->h - $max_y - 10) < 10){ 
										$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
										if($row_count < count($arr_content)){
											$pdf->AddPage();
											print_header($HISTORY_NAME);
											$max_y = $pdf->y;
										} // end if
									}else{
										$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
//										if($row_count >= count($arr_content)) $pdf->Line($start_x, $max_y, $pdf->x, $max_y);
									} // end if
									$pdf->x = $start_x;			$pdf->y = $max_y;
							} // end for loop $row_count
							
							// พิมพ์ ส่วนที่เหลือให้เต็มหน้า
							while(($pdf->h - $max_y - 10) >= 10) {
									$border = "";
									$pdf->SetFont($font,'',14);
									$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
								
									$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
									// พิมพ์ EDU
									$pdf->MultiCellThaiCut($heading_width[$HISTORY_NAME][0], 7, "", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0];
									$pdf->y = $start_y;
									$pdf->MultiCellThaiCut($heading_width[$HISTORY_NAME][1], 7, "", $border, "R");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1];
									$pdf->y = $start_y;

									$pdf->MultiCellThaiCut($heading_width[$HISTORY_NAME][2], 7, "", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1] + $heading_width[$HISTORY_NAME][2];
									$pdf->y = $start_y;
									
									// พิมพ์ TRN
									$pdf->MultiCellThaiCut($heading_width[$HISTORY_NAME][3], 7, "", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1] + $heading_width[$HISTORY_NAME][2] + $heading_width[$HISTORY_NAME][3];
									$pdf->y = $start_y;

									$pdf->MultiCellThaiCut($heading_width[$HISTORY_NAME][4], 7, "", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1] + $heading_width[$HISTORY_NAME][2] + $heading_width[$HISTORY_NAME][3] + $heading_width[$HISTORY_NAME][4];
									$pdf->y = $start_y;

									$pdf->MultiCellThaiCut($heading_width[$HISTORY_NAME][5], 7, "", $border, "L");
									
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1] + $heading_width[$HISTORY_NAME][2] + $heading_width[$HISTORY_NAME][3] + $heading_width[$HISTORY_NAME][4] + $heading_width[$HISTORY_NAME][5];
									$pdf->y = $start_y;
									
									//================= Draw Border Line ====================
//									$pdf->SetDrawColor(hexdec("00"),hexdec("00"),hexdec("00")); //สีดำ
//									$line_start_y = $start_y;		$line_start_x = $start_x;
//									$line_end_y = $pdf->y;		$line_end_x = $pdf->x;
//									$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y); // เส้นนอน

									$line_start_y = $start_y;
									$line_end_y = $max_y;
									$line_start_x = $start_x;
									$line_end_x = $start_x;
									$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y); // เส้นขวางเส้นแรก
									
									for($i=0; $i<=5; $i++) {
										$line_start_y = $start_y;		$line_start_x += $heading_width[$HISTORY_NAME][$i];
										$line_end_y = $max_y;		$line_end_x += $heading_width[$HISTORY_NAME][$i];
										$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y); // เส้นขวางที่เหลือ
									} // end for
									//====================================================
									$pdf->x = $start_x;			$pdf->y = $max_y;
							} // end การ พิมพ์ ส่วนที่เหลือให้เต็มหน้า
							$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1] + $heading_width[$HISTORY_NAME][2] + $heading_width[$HISTORY_NAME][3] + $heading_width[$HISTORY_NAME][4] + $heading_width[$HISTORY_NAME][5];
							$pdf->SetDrawColor(hexdec("00"),hexdec("00"),hexdec("00")); //สีดำ
							$line_start_y = $pdf->y;		$line_start_x = $start_x;
							$line_end_y = $pdf->y;		$line_end_x = $pdf->x;
							$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
							break;
					case "POSITIONHIS" : //รวมประวัติรับราชการ + เลื่อนขั้นเงินเดือนเข้าด้วยกัน
//							if(($pdf->h - $pdf->y - 10) < 30) $pdf->AddPage();
							$page_no = 1;
							$pdf->AddPage();
							$pdf->SetFont($fontb,'',14);
							$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
							$pdf->Cell(200,7,"13. ตำแหน่งและอัตราเงินเดือน",1,1,"C");
							print_header($HISTORY_NAME);
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
										$POSITION_LEVEL = "ชำนาญการ"."\n"."พิเศษ";
//										$testpos = str_replace("\n","<BR>",$POSITION_LEVEL);
//										echo "POSITION_LEVEL=$testpos<BR>";
									}
									$POH_SEQ_NO = trim($data2[POH_SEQ_NO]);
//									if( ("$PM_CODE $PL_NAME $LEVEL_NO $POH_POS_NO") != (trim($data2[PM_CODE])." ".trim($data2[PL_NAME])." ".$LEVEL_NO." ".trim($data2[POH_POS_NO]))){
										$PL_NAME = trim($data2[PL_NAME]);
										$LEVEL_NAME = trim(str_replace("ระดับ","",$data2[LEVEL_NAME]));
										$footer_level = trim($data2[LEVEL_NAME]);
										$PT_CODE = trim($data2[PT_CODE]);
										$PT_NAME = trim($data2[PT_NAME]);
										$PM_CODE = trim($data2[PM_CODE]);
										$PM_NAME = trim($data2[PM_NAME]);
										$POH_PL_NAME = trim($data2[POH_PL_NAME]);
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
									$POH_DOCNO = (trim($data2[POH_DOCNO]))? $data2[POH_DOCNO] : "-" ;
									$MOV_CODE = trim($data2[MOV_CODE]);
									
									if(trim($data2[POH_DOCNO])){
										if($data2[POH_DOCDATE]){
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
										if (strpos($data2[POH_DOCNO],"คส.") !== false)
											$POH_DOCNO = $data2[POH_DOCNO]."\n".$POH_DOCDATE."\n".$USRNAME;
										else
											$POH_DOCNO = "คส. ".$data2[POH_DOCNO]."\n".$POH_DOCDATE."\n".$USRNAME;
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
									$ARR_POSITIONHIS[$PER_ID][] = array(
																					'DATE'=>$POH_EFFECTIVEDATE,
																					'SEQ'=>$POH_SEQ_NO,
																					'MOVE'=>$MOV_NAME,
																					'POS_NAME'=>$TMP_PL_NAME,
																					'POS_NO'=>$POH_POS_NO,
																					'LEVEL'=>$POSITION_LEVEL,
																					'FOOTERLEVEL'=>$footer_level,
																					'SALARY'=>$POH_SALARY,
																					'DOC_NO'=>$POH_DOCNO
																				);
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

									$cmd = " select SM_NAME, SM_FACTOR from PER_SALARY_MOVMENT where SM_CODE='$SM_CODE' ";
									$db_dpis3->send_cmd($cmd);
									$data3 = $db_dpis3->get_array();
									$SM_NAME = $data3[SM_NAME];
									$SM_FACTOR = $data3[SM_FACTOR] + 0;
//									if ($SM_NAME && $SM_FACTOR != 0) $MOV_NAME .= " ($SM_NAME)";
									if ($SM_NAME) $MOV_NAME .= " ($SM_NAME)";

									$ARR_POSCHECK[$PER_ID][DOC_NO][] = $data2[POH_DOCNO];
									$ARR_POSCHECK[$PER_ID][DOC_DATE][] = $POH_DOCDATE;
									$ARR_POSCHECK[$PER_ID][MOVE_CODE][] = $MOV_CODE;
									if(trim($data2[SAH_DOCNO])){
										if($data2[SAH_DOCDATE]){
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
										if (strpos($data2[SAH_DOCNO],"คส.") !== false)
											$SAH_DOCNO = $data2[SAH_DOCNO]."\n".$SAH_DOCDATE."\n".$USRNAME;
										else
											$SAH_DOCNO = "คส. ".$data2[SAH_DOCNO]."\n".$SAH_DOCDATE."\n".$USRNAME;
									}

									$cmd = " select POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$LEVEL_NO' ";
									$db_dpis3->send_cmd($cmd);
									//echo "<br>$cmd<br>";
									//$db_dpis3->show_error();
									$data3 = $db_dpis3->get_array();
									$POSITION_LEVEL = $data3[POSITION_LEVEL];
									if ($POSITION_LEVEL=="ชำนาญการพิเศษ") {
										$POSITION_LEVEL = "ชำนาญการ"."\n"."พิเศษ";
//										$testpos = str_replace("\n","<BR>",$POSITION_LEVEL);
//										echo "POSITION_LEVEL=$testpos<BR>";
									}
									$TMP_PL_NAME = trim($MOVE_NAME);

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
													$ARR_POSITIONHIS[$PER_ID][$key][POS_NAME] = $TMP_PL_NAME;
												if (!$ARR_POSITIONHIS[$PER_ID][$key][POS_NO]) 
													$ARR_POSITIONHIS[$PER_ID][$key][POS_NO] = $SAH_PAY_NO;
												if (!$ARR_POSITIONHIS[$PER_ID][$key][LEVEL]) 
													$ARR_POSITIONHIS[$PER_ID][$key][LEVEL] = $POSITION_LEVEL;
												if (!$ARR_POSITIONHIS[$PER_ID][$key][FOOTERLEVEL]) 
													$ARR_POSITIONHIS[$PER_ID][$key][FOOTERLEVEL] = $footer_level;
												if (!$ARR_POSITIONHIS[$PER_ID][$key][SALARY]) 
													$ARR_POSITIONHIS[$PER_ID][$key][SALARY] = $SAH_SALARY;
//											} // end if check movecode
										} // end if check doc_date
									} // end if check doc_no
									if (!$flag_dup) { // ถ้าไม่ซ้ำ
									//เก็บลง array ของ SALARYHIS
									$ARR_SALARYHIS[$PER_ID][] = array(
																					'DATE'=>$SAH_EFFECTIVEDATE,
																					'SEQ'=>$SAH_SEQ_NO,
																					'MOVE'=>$MOV_NAME,
																					'POS_NAME'=>$TMP_PL_NAME,
																					'POS_NO'=>$SAH_PAY_NO,
																					'LEVEL'=>$POSITION_LEVEL,
																					'FOOTERLEVEL'=>$footer_level,
																					'SALARY'=>$SAH_SALARY,
																					'DOC_NO'=>$SAH_DOCNO
																			);																			
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
//							sort($DATE_HIS);
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
										$DATE_POH_SAH = show_date_format(substr($POH_SAH_HIS[$PER_ID][$in]['DATE'], 0, 10),3);
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

									$sub_doc = explode("\n", $POH_SAH_HIS[$PER_ID][$in]['DOC_NO']);
									$prt_doc_line = count($sub_doc);
									$prt_doc_line = $prt_doc_line - (!trim($sub_doc[$prt_doc_line-1]) ? 1 -  (!trim($sub_doc[$prt_doc_line-2]) ? 1 : 0) : 0);
										$dlen = strlen($POH_SAH_HIS[$PER_ID][$in]['POS_NAME']);
//										echo "bf-".$POH_SAH_HIS[$PER_ID][$in]['POS_NAME']."[".$dlen."]<br>";
										$temptext = $pdf->thaiCutLinePDF($POH_SAH_HIS[$PER_ID][$in]['POS_NAME'], $heading_width[POSITIONHIS][1], "\n");
										$sub_pos = explode("\n", $temptext);
//									}
									$sub_level = explode("\n", $POH_SAH_HIS[$PER_ID][$in]['LEVEL']);
									$prt_level_line = count($sub_level);
									$prt_pos_line = 0;
									$prt_pos_line = count($sub_pos);
									$prt_pos_line = $prt_pos_line - (!trim($sub_pos[$prt_pos_line-1]) ? 1 -  (!trim($sub_doc[$prt_doc_line-2]) ? 1 : 0) : 0);
									$prt_max_line = ($prt_doc_line > $prt_pos_line ? $prt_doc_line : $prt_pos_line);
									$prt_max_line = ($prt_level_line > $prt_max_line ? $prt_level_line : $prt_max_line);
									if (((($linenum + $prt_max_line) * 7) + 14) > $pdf->h) {
									// $linenum = จำนวนบรรทัดที่พิมพ์ไปแล้ว + $prt_doc_line = จำนวนบรรทัดที่จะพิมพ์สำหรับรายการนี้ แล้วคูณด้วย 7 คือความสูงของบรรทัด
									//  แล้วบวกด้วย 7 คือเว้นเผื่อขอบล่าง 1 บรรทัด (ถ้าจะเว้น 2 บรรทัดก็ บวกด้วย 14)
										print_footer($last_footer_level);
										
										$page_no++;
										$pdf->AddPage();
										print_header($HISTORY_NAME);
										$max_y = $pdf->y;
										$linenum = ceil($pdf->y / 7);  // พิมพ์มาแล้วกี่บรรทัด
										$linecnt = 1;
									}
									
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
//										$numline = "(".$positionhis_count.") ";
//										$numline = "(".$linecnt.") ";
//										$pdf->Cell($heading_width[0] ,7,$numline,"",0,"C");		//แสดงบรรทัดที่

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
//										$pdf->Cell($heading_width[POSITIONHIS][3] ,7,($POH_SAH_HIS[$PER_ID][$in]['LEVEL']?$POH_SAH_HIS[$PER_ID][$in]['LEVEL']:""),$border,0,"C");
										if ($sub_line_i == 0) {
											$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
										} else {
											$pdf->SetTextColor(hexdec("FF"),hexdec("FF"),hexdec("FF"));//สีขาว คือไม่พิมพ์
										}
//										$pdf->Cell($heading_width[POSITIONHIS][4] ,7,($POH_SAH_HIS[$PER_ID][$in]['SALARY']?$POH_SAH_HIS[$PER_ID][$in]['SALARY']:""),$border,0,"C");
										$salary_text = ($POH_SAH_HIS[$PER_ID][$in]['SALARY']?number_format($POH_SAH_HIS[$PER_ID][$in]['SALARY']):"-");
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
/*									$border = "";
									$pdf->SetFont($font,'',14);
									$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
								
									$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;

//									echo ">>>>> rec=".($DATE_POH_SAH?$DATE_POH_SAH:"")."<br>";
									
									$pdf->MultiCell($heading_width[$HISTORY_NAME][0] ,7,($DATE_POH_SAH?$DATE_POH_SAH:""), $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0];
									$pdf->y = $start_y;
									if ($f_pos_color == "blue") {
										$pdf->SetTextColor(hexdec("65"),hexdec("00"),hexdec("CA"));	//สีน้ำเงิน
									} else {
										$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00")); // สีดำ
									}
									$pdf->MultiCellThaiCut($heading_width[$HISTORY_NAME][1], 7, $POH_SAH_HIS[$PER_ID][$in]['POS_NAME'], $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1];
									$pdf->y = $start_y;

									$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
									$pdf->MultiCellThaiCut($heading_width[$HISTORY_NAME][2], 7, ($POH_SAH_HIS[$PER_ID][$in]['POS_NO']?$POH_SAH_HIS[$PER_ID][$in]['POS_NO']:""), $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1] + $heading_width[$HISTORY_NAME][2];
									$pdf->y = $start_y;

									$pdf->MultiCell($heading_width[$HISTORY_NAME][3], 7, ($POH_SAH_HIS[$PER_ID][$in]['LEVEL']?$POH_SAH_HIS[$PER_ID][$in]['LEVEL']:""), $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1] + $heading_width[$HISTORY_NAME][2] + $heading_width[$HISTORY_NAME][3];
									$pdf->y = $start_y;

									$pdf->MultiCell($heading_width[$HISTORY_NAME][4], 7, ($POH_SAH_HIS[$PER_ID][$in]['SALARY']?$POH_SAH_HIS[$PER_ID][$in]['SALARY']:""), $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1] + $heading_width[$HISTORY_NAME][2] + $heading_width[$HISTORY_NAME][3] + $heading_width[$HISTORY_NAME][4];
									$pdf->y = $start_y;

									$pdf->MultiCellThaiCut($heading_width[$HISTORY_NAME][5], 7, $POH_SAH_HIS[$PER_ID][$in]['DOC_NO'], $border, "L");
									
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1] + $heading_width[$HISTORY_NAME][2] + $heading_width[$HISTORY_NAME][3] + $heading_width[$HISTORY_NAME][4] + $heading_width[$HISTORY_NAME][5];
									$pdf->y = $start_y;
									
									//================= Draw Border Line ====================
									$pdf->SetDrawColor(hexdec("00"),hexdec("00"),hexdec("00")); //สีดำ
									$line_start_y = $start_y;		$line_start_x = $start_x;
									$line_end_y = $pdf->y;		$line_end_x = $pdf->x;
									$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y); // เส้นนอน

									$line_start_y = $start_y;
									$line_end_y = $max_y;
									$line_start_x = $start_x;
									$line_end_x = $start_x;
									$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y); // เส้นขวางเส้นแรก
									
									for($i=0; $i<=5; $i++) {
										$line_start_y = $start_y;		$line_start_x += $heading_width[$HISTORY_NAME][$i];
										$line_end_y = $max_y;		$line_end_x += $heading_width[$HISTORY_NAME][$i];
										$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y); // เส้นขวางที่เหลือ
									} // end for
									//====================================================

//									echo ">>>>>h=".$pdf->h.",max_y=$max_y, rec=".($DATE_POH_SAH?$DATE_POH_SAH:"")."<br>";
									if(($pdf->h - $max_y - 10) < 10){ 
										$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
										if($in+1 < $count_positionhis){
											$pdf->AddPage();
											print_header($HISTORY_NAME);
											$max_y = $pdf->y;
										} // end if
									}else{
										$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
//										if($row_count >= count($arr_content)) $pdf->Line($start_x, $max_y, $pdf->x, $max_y);
									} // end if
									$pdf->x = $start_x;			$pdf->y = $max_y; */
							} // end for $in
/*							$pdf->SetDrawColor(hexdec("00"),hexdec("00"),hexdec("00")); //สีดำ
							$line_start_y = $pdf->y;		$line_start_x = $start_x;
							$line_end_y = $pdf->y;		$line_end_x = $pdf->x;
							$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y); */
							if($in<=0){	//}else{
								$pdf->SetFont($fontb,'',16);
								$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
								$pdf->Cell(200,7,"********** ไม่มีข้อมูล **********","LBR",1,'C');
							} else {
								$page_no++;
								print_footer($last_footer_level);
							} // end if	
						} // end if(is_array($POH_SAH_HIS) && !empty($POH_SAH_HIS))
						break;
					case "ABILITY" :
//							if(($pdf->h - $pdf->y - 10) < 30) $pdf->AddPage();
							$pdf->AddPage();
							$pdf->SetFont($fontb,'',14);
							$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
							if($pdf->y != $page_start_y) $pdf->Cell(200,7,"",0,1,"L");
							//$pdf->Cell(200,7,($history_index + 1)."  ความสามารถพิเศษ ",0,1,"L");
							$pdf->Cell(200,7,($history_index)."  ความสามารถพิเศษ ","TRBL",1,"L");
//							print_header($HISTORY_NAME);

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
								
/*									$pdf->MultiCell($heading_width[$HISTORY_NAME][0], 7, "$AL_NAME", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0];
									$pdf->y = $start_y;

									$pdf->MultiCell($heading_width[$HISTORY_NAME][1], 7, "$ABI_DESC", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1];
									$pdf->y = $start_y;
*/
									$pdf->MultiCell($heading_width[$HISTORY_NAME][0]+$heading_width[$HISTORY_NAME][1], 7, "ด้าน:$AL_NAME ความสามารถพิเศษ:$ABI_DESC", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1];
									$pdf->y = $start_y;
									//================= Draw Border Line ====================
									$line_start_y = $start_y;		$line_start_x = $start_x;
									$line_end_y = $max_y;		$line_end_x = $start_x;
									$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				
/*									for($i=0; $i<=1; $i++){
										$line_start_y = $start_y;		$line_start_x += $heading_width[$HISTORY_NAME][$i];
										$line_end_y = $max_y;		$line_end_x += $heading_width[$HISTORY_NAME][$i];
										$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
									} // end for
*/
									$line_start_y = $start_y;		$line_start_x = $start_x + $heading_width[$HISTORY_NAME][0]+$heading_width[$HISTORY_NAME][1];
									$line_end_y = $max_y;		$line_end_x = $line_start_x;
									$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
									//====================================================
				
									if(($pdf->h - $max_y - 10) < 10){ 
										$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
										if($abilityhis_count < $count_abilityhis){
											$pdf->AddPage();
//1											print_header($HISTORY_NAME);
											$max_y = $pdf->y;
											$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
											$line_start_y = $start_y;		$line_start_x = $start_x;
											$line_end_y = $max_y;		$line_end_x =  $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1];
											$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
										} // end if
									}else{
										if($abilityhis_count == $count_abilityhis) $pdf->Line($start_x, $max_y, $pdf->x, $max_y);
									} // end if
									$pdf->x = $start_x;			$pdf->y = $max_y;
								} // end while
							}else{
								$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
								$pdf->Cell(200,7,"********** ไม่มีข้อมูล **********","LBR",1,'C');
							} // end if
						break;
					case "SERVICEHIS" :
							if(($pdf->h - $pdf->y - 10) < 30) $pdf->AddPage();
							$pdf->SetFont($fontb,'',14);
							$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
//							if($pdf->y != $page_start_y) $pdf->Cell(200,7,"",0,1,"L");
							//$pdf->Cell(200,7,($history_index + 1)."  ประวัติการปฏิบัติราชการพิเศษ ",0,1,"L");
							$pdf->Cell(200,7,($history_index)."  ประวัติการปฏิบัติราชการพิเศษ ","TRBL",1,"L");
							print_header($HISTORY_NAME);

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
										$pdf->SetFont($font,'',14);
										$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
									
										$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
									
										$pdf->MultiCell($heading_width[$HISTORY_NAME][0], 7, "$SRH_STARTDATE1", $border, "L");
										if($pdf->y > $max_y) $max_y = $pdf->y;
										$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0];
										$pdf->y = $start_y;

										$pdf->MultiCell($heading_width[$HISTORY_NAME][1], 7, "ประเภท : $SV_NAME1   หัวข้อราชการพิเศษ : $SRT_NAME1  เอกสาร : $SRH_DOCNO1", $border, "L");	//$SRH_NOTE
										if($pdf->y > $max_y) $max_y = $pdf->y;
										$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1];
										$pdf->y = $start_y;
										
										$pdf->MultiCell($heading_width[$HISTORY_NAME][2], 7, "$SRH_STARTDATE2", $border, "L");
										if($pdf->y > $max_y) $max_y = $pdf->y;
										$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1] + $heading_width[$HISTORY_NAME][2];
										$pdf->y = $start_y;

										$pdf->MultiCell($heading_width[$HISTORY_NAME][3], 7, "ประเภท : $SV_NAME2   หัวข้อราชการพิเศษ : $SRT_NAME2  เอกสาร : $SRH_DOCNO2", $border, "L");	//$SRH_NOTE
										if($pdf->y > $max_y) $max_y = $pdf->y;
										$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1] + $heading_width[$HISTORY_NAME][2] + $heading_width[$HISTORY_NAME][3];
										$pdf->y = $start_y;

										//================= Draw Border Line ====================
										$line_start_y = $start_y;		$line_start_x = $start_x;
										$line_end_y = $max_y;		$line_end_x = $start_x;
										$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);

										for($i=0; $i<=3; $i++){
											$line_start_y = $start_y;		$line_start_x += $heading_width[$HISTORY_NAME][$i];
											$line_end_y = $max_y;		$line_end_x += $heading_width[$HISTORY_NAME][$i];
											$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
										} // end for
										//====================================================
				
										if(($pdf->h - $max_y - 10) < 10){ 
											$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
											if($servicehis_count < $count_servicehis){
												$pdf->AddPage();
												print_header($HISTORY_NAME);
												$max_y = $pdf->y;
											} // end if
										}else{
											if($servicehis_count == $count_servicehis) $pdf->Line($start_x, $max_y, $pdf->x, $max_y);
										} // end if
										$pdf->x = $start_x;			$pdf->y = $max_y;
									} // end if ($servicehis_count % 2 == 0)
								} // end while
							}else{
								$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
								$pdf->Cell(200,7,"********** ไม่มีข้อมูล **********","LBR",1,'C');
							} // end if
						break;
					case "SPECIALSKILLHIS" :
							if(($pdf->h - $pdf->y - 10) < 30) $pdf->AddPage();
							$pdf->SetFont($fontb,'',14);
							$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
//							if($pdf->y != $page_start_y) $pdf->Cell(200,7,"",0,1,"L");
							//$pdf->Cell(200,7,($history_index + 1)."  ความเชี่ยวชาญพิเศษ ",0,1,"L");
							$pdf->Cell(200,7,($history_index)."  รายการอื่น ๆ ที่จำเป็น ","TRBL",1,"L");
//							print_header($HISTORY_NAME);

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
									$pdf->SetFont($font,'',14);
									$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
									
									$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
								
									$pdf->MultiCell($heading_width[$HISTORY_NAME][0]+$heading_width[$HISTORY_NAME][1], 7, "ความสามารถพิเศษ : $SS_NAME, $SPS_EMPHASIZE", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1];
									$pdf->y = $start_y;

//									$pdf->MultiCell($heading_width[$HISTORY_NAME][1], 7, "$SPS_EMPHASIZE", $border, "L");
//									if($pdf->y > $max_y) $max_y = $pdf->y;
//									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1];
//									$pdf->y = $start_y;

									//================= Draw Border Line ====================
									$line_start_y = $start_y;		$line_start_x = $start_x;
									$line_end_y = $max_y;		$line_end_x = $start_x;
									$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				
//									for($i=0; $i<=1; $i++){
										$line_start_y = $start_y;		$line_start_x += $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1];
										$line_end_y = $max_y;		$line_end_x = $line_start_x;
										$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
//									} // end for
									//====================================================
				
									if(($pdf->h - $max_y - 10) < 10){ 
										$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
										if($specialskillhis_count < $count_specialskillhis){
											$pdf->AddPage();
											print_header($HISTORY_NAME);
											$max_y = $pdf->y;
										} // end if
									}else{
										if($specialskillhis_count == $count_specialskillhis) $pdf->Line($start_x, $max_y, $pdf->x, $max_y);
									} // end if
									$pdf->x = $start_x;			$pdf->y = $max_y;
								} // end while
							}else{
								$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
								$pdf->Cell(200,7,"********** ไม่มีข้อมูล **********","LBR",1,'C');
							} // end if
						break;
					case "DECORATEHIS" :
//							if(($pdf->h - $pdf->y - 10) < 30) $pdf->AddPage();
							$pdf->AddPage();
							$pdf->SetFont($fontb,'',14);
							$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
//							if($pdf->y != $page_start_y) $pdf->Cell(200,7,"",0,1,"L");
							//$pdf->Cell(200,7,($history_index + 1)."  ประวัติการรับเครื่องราชฯ ",0,1,"L");
//							$pdf->Cell(200,7,($history_index)."  ประวัติการรับเครื่องราชฯ ",0,1,"L");
//							print_header($HISTORY_NAME);

							$this_x = $pdf->x;
							$this_y = $pdf->y;									
							$line_start_y = $pdf->y;			$line_start_x = $pdf->x;
							$line_end_y = $pdf->y;			$line_end_x = $pdf->x + 200;
							$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
							// แถวประวัติรูป
							$imgcnt = count($arr_img);
							if ($imgcnt > 0) {
							$start_pic = 0;
							if  ($imgcnt > 7) $start_pic = $imgcnt - 7;	// แถวแสดงได้สูงสุด 7 ภาพ เริ่มนับจากภาพล่าสุดลงมา 7 ภาพ
							else $imgcnt = 7;
							$j = 0;		$image_y = $pdf->y+2;	$image_w = 25;		$image_h = 33;
							$line_start_y = $pdf->y;			$line_start_x = $pdf->x;
							$line_end_y = $pdf->y+45;	$line_end_x = $pdf->x;
							$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
							$imgx = $pdf->x+7; $line_start_x = $imgx; $line_end_x = $imgx;
							$pdf->SetFont($font,'',12);
							for($i=$start_pic; $i  < $imgcnt; $i++) {
								if($arr_img[$i]){
									$image_x = ($imgx + (($image_w+2) * $j) + 1);
//									echo "$i>".$arr_img[$i]."<br>";
									$pdf->Image($arr_img[$i], $image_x, $image_y, $image_w, $image_h);
									$j++;
								} // end if			
								$line_start_y = $pdf->y;			$line_start_x += $image_w+2;
								$line_end_y = $pdf->y+45;	$line_end_x = $line_start_x;
								if ($i  == $imgcnt-1) { 
									$line_start_x += 4; $line_end_x = $line_start_x; 
									$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y); // ถ้าขีดเส้นปิดรูปสุดท้ายเท่านั้น
								}
								$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);	// ถ้าขีดเส้นคั่นแต่ละรูป
							}
							$pdf->SetFont($font,'',12);
							$j = 0; $pdf->y += 35;
							for($i=$start_pic; $i  < $imgcnt; $i++) {
								if($arr_img[$i]){
									$pdf->x = ($imgx + (($image_w+2) * $j) + 1);
									if ($arr_imgshow[$i]==1){
										$pdf->SetFont($fontb,'',12);
										$pdf->SetTextColor(hexdec("FF"),hexdec("00"),hexdec("00")); // สีแดง
									} else {
										$pdf->SetFont($font,'',12);
										$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
									}
									$pdf->Cell(25 ,7,"รูปที่ ".($i+1),0,0,"C");
									$j++;
								} // end if			
							}
							$j = 0; $pdf->y += 4;
							for($i=$start_pic; $i  < $imgcnt; $i++) {
								if($arr_img[$i]){
									$pdf->x = ($imgx + (($image_w+2) * $j) + 1);
									if ($arr_imgshow[$i]==1){
										$pdf->SetFont($fontb,'',12);
										$pdf->SetTextColor(hexdec("FF"),hexdec("00"),hexdec("00")); // สีแดง
									} else {
										$pdf->SetFont($font,'',12);
										$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
									}
									$pdf->Cell(25 ,7,"พ.ศ. ".$arr_imgsavedate[$i],0,0,"C");
									$j++;
								} // end if			
							}
							$pdf->y += $image_h+6;
						}
						// จบประวัติรูป
	
						$pdf->SetFont($font,'',14);
						$pdf->x = $this_x;
						$pdf->y = $this_y+45;
						if(($pdf->h - $pdf->y - 10) < 30) $pdf->AddPage();
						$pdf->Cell(200 ,7,"1. ชื่อ  ".$FULLNAME."   วัน เดือน ปี เกิด  ".$PER_BIRTHDATE,1,1,"L");
						$pdf->Cell(200 ,7,"2. ที่อยู่ปัจจุบัน ".($PER_ADD1 ? $PER_ADD1 : $PER_ADD2),1,1,"L");
						$pdf->SetFont($fontb,'',14);
						$pdf->Cell(200 ,7,"3. เครื่องราชอิสริยาภรณ์ วันที่ได้รับ วันส่งคืน รวมทั้งเอกสารอ้างอิง",1,1,"L");
			
						$pdf->SetFont($font,'',14);
						$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
						
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
							$pdf->MultiCell(100, 7, $arr_DEH_SHOW[$i], $border, "L");
							if($pdf->y > $max_y) $max_y = $pdf->y;
							$pdf->x = $start_x + 100;
							$pdf->y = $start_y;
							$pdf->MultiCell(100, 7, $arr_DEH_SHOW[$i+1], $border, "L");
							if($pdf->y > $max_y) $max_y = $pdf->y;
							$pdf->x = $start_x + 100;
							$pdf->y = $start_y;
						}
						
						$line_start_y = $start_y;		$line_start_x = $start_x+200;
						$line_end_y = $max_y;		$line_end_x = $start_x+200;
						$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);

						$pdf->x = $start_x;
						$pdf->y = $max_y;
/*			
							$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
							$max_x = $start_x;
							for($i=0; $i<=4; $i++){
								$max_x += $heading_width[$HISTORY_NAME][$i];
							}
							$line_start_y = $start_y;		$line_start_x = $start_x;
							$line_end_y = $max_y;		$line_end_x =  $max_x;
							$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);

							$DC_NAME = "";
							$DEH_DATE = "";
							$DEH_GAZETTE = "";
							
							$cmd = " select		b.DC_NAME, a.DEH_DATE, a.DEH_GAZETTE
											 from			PER_DECORATEHIS a, PER_DECORATION b
											 where		a.PER_ID=$PER_ID and a.DC_CODE=b.DC_CODE
											 order by	a.DEH_DATE ";							   
							$count_decoratehis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							if($count_decoratehis){
								$decoratehis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$decoratehis_count++;
									$DC_NAME = trim($data2[DC_NAME]);
									$DEH_DATE = trim($data2[DEH_DATE]);
									if($DEH_DATE){
										$DEH_DATE = substr($DEH_DATE, 0, 10);
										$DEH_DATE1 = show_date_format($DEH_DATE,$DATE_DISPLAY);
									} // end if	
									$DEH_GAZETTE = trim($data2[DEH_GAZETTE]);
				
									//หาตำแหน่งที่ดำรง ณ ระหว่างวันที่ได้รับพระราชทานเครื่องราช
									if($DPISDB=="odbc"){
										$cmd3 = "select 		POH_ID, POH_EFFECTIVEDATE, POH_ENDDATE, POH_ORG2, POH_ORG3, 
																			PL_CODE, PM_CODE, PT_CODE, PN_CODE, EP_CODE, LEVEL_NO, POH_POS_NO    
														from 			PER_POSITIONHIS 
														where 		(PER_ID=$PER_ID) AND (POH_EFFECTIVEDATE <= '$DEH_DATE')
														ORDER BY	 LEFT(POH_EFFECTIVEDATE,10) desc, LEFT(POH_ENDDATE,10) desc ";
									}elseif($DPISDB=="oci8"){
										$cmd3 = "select 		POH_ID, POH_EFFECTIVEDATE, POH_ENDDATE, POH_ORG2, POH_ORG3, 
																			PL_CODE, PM_CODE, PT_CODE, PN_CODE, EP_CODE, LEVEL_NO, POH_POS_NO    
														from 			PER_POSITIONHIS 
														where 		(PER_ID=$PER_ID) AND (POH_EFFECTIVEDATE <= '$DEH_DATE')
														order by 		SUBSTR(POH_EFFECTIVEDATE,1,10)  desc, SUBSTR(POH_ENDDATE,1,10) desc ";
									}elseif($DPISDB=="mysql"){
										$cmd3 = "select 		POH_ID, POH_EFFECTIVEDATE, POH_ENDDATE, POH_ORG2, POH_ORG3, 
																			PL_CODE, PM_CODE, PT_CODE, PN_CODE, EP_CODE, LEVEL_NO, POH_POS_NO    
														from 			PER_POSITIONHIS 
														where 		(PER_ID=$PER_ID) AND (POH_EFFECTIVEDATE <= '$DEH_DATE')
														ORDER BY	 LEFT(POH_EFFECTIVEDATE,10) desc, LEFT(POH_ENDDATE,10) desc ";
									}
									//echo "<br>$cmd3<br>";
									$db_dpis3->send_cmd($cmd3);
									$data3 = $db_dpis3->get_array();
									$POH_LEVEL_NO = trim($data3[LEVEL_NO]);
									$POH_PL_CODE = trim($data3[PL_CODE]);
									$POH_PM_CODE = trim($data3[PM_CODE]);
									$POH_PT_CODE = trim($data3[PT_CODE]);
									$POH_PN_CODE = $data3[PN_CODE];
									$POH_EP_CODE = $data3[EP_CODE];
		
									$DEH_PL_NAME = "";
									$cmd3= " select PL_NAME from PER_LINE where PL_CODE='$POH_PL_CODE' ";			
									$db_dpis3->send_cmd($cmd3);
									$data3 = $db_dpis3->get_array();
									$DEH_PL_NAME = $data3[PL_NAME];
						
									$cmd3 = " 	select PM_NAME from PER_MGT	where PM_CODE='$POH_PM_CODE'  ";
									$db_dpis3->send_cmd($cmd3);
									$data3 = $db_dpis3->get_array();
									$DEH_PM_NAME = trim($data3[PM_NAME]);
						
									$cmd3 = " 	select PT_NAME from PER_TYPE	where PT_CODE='$POH_PT_CODE'  ";
									$db_dpis3->send_cmd($cmd3);
									$data3 = $db_dpis3->get_array();
									$DEH_PT_NAME = trim($data3[PT_NAME]);
																		
									$cmd3 = " select LEVEL_NAME from PER_LEVEL where LEVEL_NO='$POH_LEVEL_NO' ";
									$db_dpis3->send_cmd($cmd3);
									$data3 = $db_dpis3->get_array();
									$DEH_LEVEL_NAME = $data3[LEVEL_NAME];

								    $DEH_PL_NAME = (trim($DEH_PM_NAME) ?"$DEH_PM_NAME (":"") . (trim($DEH_PL_NAME)?($DEH_PL_NAME ." ". $DEH_LEVEL_NAME . (($DEH_PT_NAME != "ทั่วไป" && $DEH_LEVEL_NO >= 6)?"$DEH_PT_NAME":"")):"") . (trim($DEH_PM_NAME) ?")":"");
									//----------------------------
				
									$border = "";
									$pdf->SetFont($font,'',14);
									$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
									
									$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
								
									$pdf->MultiCell($heading_width[$HISTORY_NAME][0]+$heading_width[$HISTORY_NAME][1]+$heading_width[$HISTORY_NAME][2]+$heading_width[$HISTORY_NAME][3]+$heading_width[$HISTORY_NAME][4], 7, "ตำแหน่ง : $DEH_PL_NAME  ลำดับชั้น : $DC_NAME  รับพระราชทานเมื่อ : $DEH_DATE1".($DEH_GAZETTE?"  รับมอบเมื่อ : ".$DEH_GAZETTE:"")."  ", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $max_x;
									$pdf->y = $start_y;
/*								
									$pdf->MultiCell($heading_width[$HISTORY_NAME][0], 7, " $DEH_PL_NAME", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0];
									$pdf->y = $start_y;

									$pdf->MultiCell($heading_width[$HISTORY_NAME][1], 7, "$DC_NAME", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1];
									$pdf->y = $start_y;

									$pdf->MultiCell($heading_width[$HISTORY_NAME][2], 7, "$DEH_DATE1", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1] + $heading_width[$HISTORY_NAME][2];
									$pdf->y = $start_y;
									
									$pdf->MultiCell($heading_width[$HISTORY_NAME][3], 7, $DEH_GAZETTE?$DEH_GAZETTE:"-", $border, "C");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1] + $heading_width[$HISTORY_NAME][2] + $heading_width[$HISTORY_NAME][3];
									$pdf->y = $start_y;
									
									$pdf->Cell($heading_width[$HISTORY_NAME][4] ,7,"-",$border,0,"C");
*/									

									//================= Draw Border Line ====================
/*									$line_start_y = $start_y;		$line_start_x = $start_x;
									$line_end_y = $max_y;		$line_end_x = $start_x;
									$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				
/*									for($i=0; $i<=4; $i++){
										$line_start_y = $start_y;		$line_start_x += $heading_width[$HISTORY_NAME][$i];
										$line_end_y = $max_y;		$line_end_x += $heading_width[$HISTORY_NAME][$i];
										$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
									} // end for
*/
/*									$line_start_y = $start_y;		$line_start_x =$max_x;
									$line_end_y = $max_y;		$line_end_x = $max_x;
									$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
									//====================================================
				
									if(($pdf->h - $max_y - 10) < 10){ 
										$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
										if($decoratehis_count < $count_decoratehis){
											$pdf->AddPage();
//											print_header($HISTORY_NAME);
											$max_y = $pdf->y;
											$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
											$line_start_y = $start_y;		$line_start_x = $start_x;
											$line_end_y = $max_y;		$line_end_x =  $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1];
											$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
										} // end if
									}else{
										if($decoratehis_count == $count_decoratehis) $pdf->Line($start_x, $max_y, $pdf->x, $max_y);
									} // end if
									$pdf->x = $start_x;			$pdf->y = $max_y;
								} // end while
							}else{
								$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
								$pdf->Cell(200,7,"********** ไม่มีข้อมูล **********","LBR",1,'C');
							} // end if
*/
						break;
					case "ABSENTHIS" :
							if(($pdf->h - $pdf->y - 10) < 30) $pdf->AddPage();
							$pdf->SetFont($fontb,'',14);
							$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
//							if($pdf->y != $page_start_y) $pdf->Cell(200,7,"",0,1,"L");
							//$pdf->Cell(200,7,($history_index + 1)."  ประวัติการลา สาย ขาด ",0,1,"L");
							$pdf->Cell(200,7,"4. จำนวนวันลาหยุด ขาดราชการ มาสาย ",1,1,"L");
							print_header($HISTORY_NAME);
							
							$ABS_STARTDATE = "";
							$ABS_ENDDATE = "";
							$ABS_DAY = "";
							$AB_NAME = "";

							$cmd = " select			a.AB_CODE, a.ABS_STARTDATE, a.ABS_ENDDATE, a.ABS_DAY, b.AB_NAME
											 from			PER_ABSENTHIS a, PER_ABSENTTYPE b
											 where		a.PER_ID=$PER_ID and a.AB_CODE=b.AB_CODE
											 order by		a.ABS_STARTDATE ";							   
							$count_absenthis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$arr_AB_NAME = (array) null;
							if($count_absenthis){
								$absenthis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$absenthis_count++;
									$ABS_STARTDATE = show_date_format($data2[ABS_STARTDATE],$DATE_DISPLAY);
									$ABS_ENDDATE = show_date_format($data2[ABS_ENDDATE],$DATE_DISPLAY);
									$ABS_DURATION = "$ABS_STARTDATE - $ABS_ENDDATE";
									$ABS_YEAR = substr($data2[ABS_STARTDATE],0,4);
									if($ABS_STARTDATE == $ABS_ENDDATE) $ABS_DURATION = "$ABS_STARTDATE";
									$AB_CODE = trim($data2[AB_CODE]);
									$AB_NAME = trim($data2[AB_NAME]);
									$ABS_DAY = trim($data2[ABS_DAY]);
									if ($AB_CODE=='01' || $AB_CODE=='12')  
													$ABIDX=0;  // ลาป่วย, ป่วยจำเป็น
									elseif ($AB_CODE=='03' || $AB_CODE=='04' || $AB_CODE=='05' || $AB_CODE=='06' || $AB_CODE=='08' || $AB_CODE=='09' || $AB_CODE=='11')
													$ABIDX=1;  // ลากิจส่วนตัว, ลาพักผ่อน, ลาบวชทำพิธีฮัจย์, ลาตรวจเลือก, ลาปฏิบัติงานต่างประเทศ, ลาตามคู้สมรส, ลาเลี้ยงบุตร
									elseif ($AB_CODE=='10')
													$ABIDX=2;  // มาสาย
									elseif ($AB_CODE=='07')
													$ABIDX=4;  // ลาศึกษาต่อ
									else
													$ABIDX=3;  // ขาดราชการ
									if (!$arr_AB_NAME[$ABS_YEAR][$ABIDX]) $arr_AB_NAME[$ABS_YEAR][$ABIDX]=0;
									$arr_AB_NAME[$ABS_YEAR][$ABIDX] += $ABS_DAY;
								}

								$border = "";
								$pdf->SetFont($font,'',14);
								$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

								$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;

								$j = 1;
								foreach($arr_AB_NAME as $year => $sub_arr) {
									$pdf->Cell($heading_width[$HISTORY_NAME][0] ,7,$year,1,0,"C");
									for($abidx = 0; $abidx < 5; $abidx++) {
										if ($j % 2==0 && $abidx==4)
											$pdf->Cell($heading_width[$HISTORY_NAME][$abidx+1] ,7,$sub_arr[$abidx],1,1,"C");
										else
											$pdf->Cell($heading_width[$HISTORY_NAME][$abidx+1] ,7,$sub_arr[$abidx],1,0,"C");
									}
									if ($j % 2==0) { // จบ ตารางหลัง จึง เช็คขึ้นหน้าใหม่
										if(($pdf->h - $max_y - 10) < 10){ 
											$pdf->AddPage();
											print_header($HISTORY_NAME);
											$max_y = $pdf->y;
										}
										$pdf->x = $start_x;
									}
									$j++;
								}
								if ($j % 2==0) {
									$pdf->Cell($heading_width[$HISTORY_NAME][0] ,7,"",1,0,"C");
									for($abidx = 0; $abidx < 5; $abidx++) {
										if ($abidx==4)
											$pdf->Cell($heading_width[$HISTORY_NAME][$abidx+1] ,7,"",1,1,"C");
										else
											$pdf->Cell($heading_width[$HISTORY_NAME][$abidx+1] ,7,"",1,0,"C");
									}
								}
							}else{
								$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
								$pdf->Cell(200,7,"********** ไม่มีข้อมูล **********","LBR",1,'C');
							} // end if
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