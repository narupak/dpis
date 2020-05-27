<?
	include("../../php_scripts/connect_database.php");
	include("../php_scripts/pdf_wordarray_thaicut.php");
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
//	$arr_history_name = explode("|", $HISTORY_LIST);

	$search_condition = "";
	$arr_search_condition[] = "(a.PER_TYPE in (". implode(", ", $search_per_type) ."))";
	$MINISTRY_NAME = "";
	if($MINISTRY_ID){
		 $cmd = " select ORG_NAME from PER_ORG where ORG_ID = $MINISTRY_ID ";
		 $db_dpis->send_cmd($cmd);
		 $data = $db_dpis->get_array();
		 $MINISTRY_NAME = trim($data[ORG_NAME]);
	}
	$DEPARTMENT_NAME = "";
	if($DEPARTMENT_ID){
		 $cmd = " select ORG_NAME from PER_ORG where ORG_ID = $DEPARTMENT_ID ";
		 $db_dpis->send_cmd($cmd);
		 $data = $db_dpis->get_array();
		 $DEPARTMENT_NAME = trim($data[ORG_NAME]);
	}

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

	if($select_org_structure==0) {
		if(trim($search_org_id)){ 
			$arr_search_condition[] = "(b.ORG_ID = $search_org_id)";
			$list_type_text .= "$search_org_name";
		} // end if
		if(trim($search_org_id_1)){ 
			$arr_search_condition[] = "(b.ORG_ID_1 = $search_org_id_1)";
			$list_type_text .= " - $search_org_name_1";
		} // end if
		if(trim($search_org_id_2)){ 
			$arr_search_condition[] = "(b.ORG_ID_2 = $search_org_id_2)";
			$list_type_text .= " - $search_org_name_2";
		} // end if
	}else if($select_org_structure==1) {
		if(trim($search_org_ass_id)){ 
			$arr_search_condition[] = "(a.ORG_ID = $search_org_ass_id)";
			$list_type_text .= "$search_org_ass_name";
		} // end if
		if(trim($search_org_ass_id_1)){ 
			$arr_search_condition[] = "(a.ORG_ID =  $search_org_ass_id_1)";
			$list_type_text .= " - $search_org_ass_name_1";
		} // end if
		if(trim($search_org_ass_id_2)){ 
			$arr_search_condition[] = "(a.ORG_ID =  $search_org_ass_id_2)";
			$list_type_text .= " - $search_org_ass_name_2";
		} // end if
	}

	if($list_type == "SELECT"){
		if($SELECTED_PER_ID) {	$arr_search_condition[] = "(a.PER_ID in ($SELECTED_PER_ID))"; }
	}elseif($list_type == "CONDITION"){
		if(trim($search_pos_no)) $arr_search_condition[] = "(trim(c.POS_NO)='$search_pos_no' or trim(d.POEM_NO)='$search_pos_no' or trim(e.POEMS_NO)='$search_pos_no') or trim(g.POT_NO)='$search_pos_no')";
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
	}elseif($list_type == "ELECTRONICS"){  
		if(trim($SELECTED_PER_ID)){	$arr_search_condition[] = "(a.PER_ID in ($SELECTED_PER_ID))";	}
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);
	
	if(trim($START_DATE)){
		$arr_temp = explode("/", $START_DATE);
		$START_DATE = "ณ วันที่ ".$arr_temp[0] ." ". $month_full[($arr_temp[1] + 0)][TH] ." ". $arr_temp[2];
	}

	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = ""; // พิมพ์โดย function PDF ไม่สามารถสั่งพิมพ์ สีขาวได้ จึงไม่พิมพ์ที่นี่
	$report_title .= "";   //ใบ กพ. 7 แผ่นที่ ..........";	// พิมพ์โดย function PDF ไม่สามารถสั่งพิมพ์ สีขาวได้ จึงไม่พิมพ์ที่นี่
	$report_code = "";	// พิมพ์โดย function PDF ไม่สามารถสั่งพิมพ์ สีขาวได้ จึงไม่พิมพ์ที่นี่
	$orientation='P';

	$pdf = new PDF($orientation,$unit,$paper_size,$lang_code,$company_name,$report_title,$report_code,$heading,$heading_width,$heading_align,"","");
	
	// สั่งพิมพ์เอง เพื่อควบคุม การพิมพ์หรือไม่พิมพ์ หัวรายงาน
	$company_name = (($NUMBER_DISPLAY==2)?convert2thaidigit("ใบ กพ. 7 ".($START_DATE?$START_DATE:"")):"ใบ กพ. 7 ".($START_DATE?$START_DATE:""));
	$company_name1 = (($NUMBER_DISPLAY==2)?convert2thaidigit("ใบต่อ กพ. 7"):"ใบต่อ กพ. 7");
	$report_title .= (($NUMBER_DISPLAY==2)?convert2thaidigit("13. ตำแหน่ง และอัตราเงินเดือน "):"13. ตำแหน่ง และอัตราเงินเดือน ");                            //ใบ กพ. 7 แผ่นที่ ..........";	
	$report_code = (($NUMBER_DISPLAY==2)?convert2thaidigit("R04062"):"R04062");	

 	$pdf->Open();
	$pdf->SetMargins(5,5,5);
	$pdf->AliasNbPages();
	$pdf->AddPage();
	$pdf->SetTextColor(0, 0, 0);
	$pdf->SetFont($font,'',14);
	
	$page_start_x = $pdf->x;	 $page_start_y = $pdf->y;

	//เซตค่าที่รับมา
	$get_fall = $_GET['fall']; // "a" พิมพ์ทั้งหมด หรือ "1", "2", ......  เป็นเลขหน้า
	if (!$get_fall) $get_fall = "";
	
//	echo "rec_start=$rec_start, rec_end=$rec_end, line_start=$line_start<br>";

	if ($line_start) $get_line = $line_start;
	else $get_line = $_GET['line_start']; // บรรทัดต่อไปที่จะพิมพ์
	if ($rec_start) $get_first_rec = $rec_start;
	else $get_first_rec = $_GET['rec_start']; // เลขที่ record เริ่มต้นของหน้าที่ต้องการพิมพ์
	if ($rec_end) $get_end_rec = $rec_end;
	else $get_end_rec = $_GET['rec_end']; // เลขที่ record สุดท้ายของหน้าที่ต้องการพิมพ์

	$heading_width[0] = "7";
//	$heading_width[0] = "12";
	if ($BKK_FLAG==1) {
		$heading_width[1] = "20";
		$heading_width[2] = "70";      
		$heading_width[3] = "15";
		$heading_width[4] = "15";
		$heading_width[5] = "15";
		$heading_width[6] = "60";
		$date_format = 2;
	} else {
		$heading_width[1] = "28";
		$heading_width[2] = "90";      
		$heading_width[3] = "15";
		$heading_width[4] = "15";
		$heading_width[5] = "15";
		$heading_width[6] = "32";
		$date_format = 3;
	}

        /*Release 5.1.0.3 Begin*/
        function wordwapPOSITION_LEVEL($POSITION_LEVEL){
            switch ($POSITION_LEVEL) {
                case "ชำนาญการพิเศษ" : return "ชำนาญการ"."\n"."พิเศษ";break;
                case "กลุ่มงานบริการ" : return "กลุ่มงาน"."\n"."บริการ"; break;
                case "กลุ่มงานเทคนิค" : return "กลุ่มงาน"."\n"."เทคนิค";break;
                case "กลุ่มงานบริหารทั่วไป" : return "กลุ่มงาน"."\n"."บริหารทั่วไป";break;
                case "กลุ่มงานวิชาชีพเฉพาะ" : return "กลุ่มงาน"."\n"."วิชาชีพเฉพาะ";break;
                case "กลุ่มงานเชี่ยวชาญเฉพาะ" : return "กลุ่มงาน"."\n"."เชี่ยวชาญเฉพาะ";break;
                case "กลุ่มงานเชี่ยวชาญพิเศษ (ระดับทั่วไป)" : return "กลุ่มงาน"."\n"."เชี่ยวชาญพิเศษ"."\n"."(ระดับทั่วไป)";break;
                case "กลุ่มงานเชี่ยวชาญพิเศษ (ระดับประเทศ)" : return "กลุ่มงาน"."\n"."เชี่ยวชาญพิเศษ"."\n"."(ระดับประเทศ)";break;
                case "กลุ่มงานเชี่ยวชาญพิเศษ (ระดับสากล)" : return "กลุ่มงาน"."\n"."เชี่ยวชาญพิเศษ"."\n"."(ระดับสากล)";break;
                case "ระดับ บ2/หัวหน้า" : return "ระดับ"."\n"."บ2/หัวหน้า";break;
                case "ระดับ ส2/หัวหน้า" : return "ระดับ"."\n"."ส2/หัวหน้า";break;
                case "ระดับ ส3/หัวหน้า" : return "ระดับ"."\n"."ส3/หัวหน้า";break;
                case "ระดับ ส4/หัวหน้า" : return "ระดับ"."\n"."ส4/หัวหน้า";break;
                case "ระดับ ช2/หัวหน้า" : return "ระดับ"."\n"."ช2/หัวหน้า";break;
                case "ระดับ ช3/หัวหน้า" : return "ระดับ"."\n"."ช3/หัวหน้า";break;
                case "ระดับ ช4/หัวหน้า" : return "ระดับ"."\n"."ช4/หัวหน้า";break;
                case "ระดับ ท2/หัวหน้า" : return "ระดับ"."\n"."ท2/หัวหน้า";break;
                case "ระดับ ท3/หัวหน้า" : return "ระดับ"."\n"."ท3/หัวหน้า";break;
                case "กลุ่มงานเทคนิคพิเศษ" : return "กลุ่มงาน"."\n"."เทคนิคพิเศษ";break;
                default: return $POSITION_LEVEL;
            }
        }
        /*Release 5.1.0.3 End*/
        
        
	// set head
	function print_header($recno){
		global $pdf, $heading_width, $get_fall, $get_line, $company_name, $company_name1, $report_title, $report_code, $page_no, $PER_OFFNO, $PER_CARDNO, $start_x, $start_y, $max_y;
		global $NUMBER_DISPLAY;

		if ($get_fall == "a") {
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
			$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
			$pdf->SetDrawColor(hexdec("10"),hexdec("10"),hexdec("10"));
		} else {
			$pdf->SetTextColor(hexdec("FF"),hexdec("FF"),hexdec("FF"));
			$pdf->SetFillColor(hexdec("FF"),hexdec("FF"),hexdec("FF"));
			$pdf->SetDrawColor(hexdec("FF"),hexdec("FF"),hexdec("FF"));
		}
		$pdf->SetFont($font,'',14);

		$pdf->Cell(40 ,7,"",0,0,"L");
		$pdf->Cell(40 ,7,"",0,0,"C");
		$pdf->Cell(36 ,7,(($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title),0,0,"C");
		$pdf->Cell(40 ,7,"",0,0,"C");
		$pdf->Cell(40 ,7,"",0,1,"L");

//		$page_text = "แผ่นที่ $page_no";
		$page_text = "แผ่นที่     ";
		$pdf->Cell(40 ,7,"",0,0,"L");
		$pdf->Cell(40 ,7,"",0,0,"C");
		$pdf->Cell(36 ,7,"",0,0,"C");
		$pdf->Cell(40 ,7,"",0,0,"C");
		$pdf->Cell(40 ,7,$company_name1." ".$page_text,0,1,"R");
		
		//เริ่มแสดงเลข ปชช
		if($PER_CARDNO){	
			$pdf->Cell(20, 7, "", 0, 0, 'L', 0);
			$pdf->Cell(110, 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($PER_OFFNO):$PER_OFFNO), 0, 0, 'L', 0);
			for($i=0; $i < 13; $i++){
				showPER_CARDNO($i,(($NUMBER_DISPLAY==2)?convert2thaidigit($PER_CARDNO{$i}):$PER_CARDNO{$i}));
			}
			$pdf->Cell(5, 7, "", 0, 1, 'C', 0);
		}
		//สิ้นสุดการแสดง เลข ปชช.

		$pdf->Cell(5, 3, "", 0, 1, 'C', 0); // เว้น 1 บรรทัด สูง 3

		//สำหรับหัวตาราง ---------------
		if ($get_fall == "a") {
//			$pdf->SetTextColor(hexdec("E4"),hexdec("E4"),hexdec("E4"));//สีเทา
			$pdf->SetTextColor(hexdec("CD"),hexdec("CD"),hexdec("CD"));//สีเทาเข้มขึ้น
		} else {
			$pdf->SetTextColor(hexdec("FF"),hexdec("FF"),hexdec("FF"));
		}
//		if ($recno=="")
			$pdf->Cell($heading_width[0],7,"",'R',0,'C',0);
//		else
//			$pdf->Cell($heading_width[0],7,"Rec# ",'R',0,'C',0);

		$start_x = $pdf->x;

		if ($get_fall == "a") {
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		} else {
			$pdf->SetTextColor(hexdec("FF"),hexdec("FF"),hexdec("FF"));
		}
		$pdf->Cell($heading_width[1],7,"วัน เดือน ปี",'LTR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"ตำแหน่ง",'LTR',0,'C',1);
		$pdf->Cell($heading_width[3] ,7,"เลขที่",'LTR',0,'C',1);
		$pdf->Cell($heading_width[4] ,7,"ระดับ",'LTR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,"อัตรา",'LTR',0,'C',1);
		$pdf->Cell($heading_width[6] ,7,"เอกสารอ้างอิง",'LTR',1,'C',1);
		
		if ($get_fall == "a") {
//			$pdf->SetTextColor(hexdec("E4"),hexdec("E4"),hexdec("E4"));//สีเทา
			$pdf->SetTextColor(hexdec("CD"),hexdec("CD"),hexdec("CD"));//สีเทาเข้มขึ้น
		} else {
			$pdf->SetTextColor(hexdec("FF"),hexdec("FF"),hexdec("FF"));
		}
//		if ($recno=="")
			$pdf->Cell($heading_width[0],7,"",'R',0,'C',0);
//		else
//			$pdf->Cell($heading_width[0],7," $recno ",'R',0,'C',0);
		if ($get_fall == "a") {
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		} else {
			$pdf->SetTextColor(hexdec("FF"),hexdec("FF"),hexdec("FF"));
		}
		$pdf->Cell($heading_width[1] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[3] ,7,"ตำแหน่ง",'LBR',0,'C',1);
		$pdf->Cell($heading_width[4] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,"เงินเดือน",'LBR',0,'C',1);
		$pdf->Cell($heading_width[6] ,7,"",'LBR',1,'C',1);

		$pdf->x = $start_x + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6];

		$line_start_y = $pdf->y;	$line_start_x = $start_x;
		$line_end_y = $pdf->y;		$line_end_x = $pdf->x;

		if ($get_fall == "a")
			$pdf->SetDrawColor(hexdec("00"),hexdec("00"),hexdec("00")); // สีดำ
		else
			$pdf->SetDrawColor(hexdec("FF"),hexdec("FF"),hexdec("FF")); // สีขาว

		$pdf->Line($line_start_x, $line_end_y, $line_end_x, $line_end_y);
		// end set head
	} // function

	function showPER_CARDNO($i,$cardno){
		global $pdf,$CARD_NO_DISPLAY;
		$pdf->Cell(5, 7,card_no_format($cardno,$CARD_NO_DISPLAY), 1, 0, 'C', 0);
	}
			
	function print_footer($LEVEL_NAME) {
		global $pdf, $get_fall, $start_x, $start_y, $heading_width, $max_y, $FULLNAME, $FULL_LEVEL_NAME, $MINISTRY_NAME, $DEPARTMENT_NAME;
		global $NUMBER_DISPLAY;
		
		$pdf->x = $start_x + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6];

		$line_start_y = $start_x;		$line_start_x = $start_x;
		$line_end_y = $max_y;		$line_end_x = $pdf->x;
		if ($get_fall == "a") {
			$pdf->SetDrawColor(hexdec("00"),hexdec("00"),hexdec("00")); //สีดำ
		} else {
			$pdf->SetDrawColor(hexdec("FF"),hexdec("FF"),hexdec("FF"));
		}
		$pdf->Line($line_start_x, $line_end_y, $line_end_x, $line_end_y);

		$pdf->SetY(-14);
		$pdf->Cell(5, 3, "", 0, 1, 'C', 0); // เว้น 1 บรรทัด สูง 3

		$pdf->x = $start_x-$heading_width[0];
		$pdf->SetFont($font,'',14);
		if ($get_fall == "a") {
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00")); //สีดำ
		} else {
			$pdf->SetTextColor(hexdec("FF"),hexdec("FF"),hexdec("FF"));
		}
		$pdf->Cell(55 ,7,(($NUMBER_DISPLAY==2)?convert2thaidigit("14. ชื่อ ".$FULLNAME):"14. ชื่อ ".$FULLNAME),0,0,"L");
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00")); //สีดำ
		$pdf->Cell(50 ,7,($LEVEL_NAME?(($NUMBER_DISPLAY==2)?convert2thaidigit($LEVEL_NAME):$LEVEL_NAME):""),0,0,"L");
		if ($get_fall == "a") {
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00")); //สีดำ
		} else {
			$pdf->SetTextColor(hexdec("FF"),hexdec("FF"),hexdec("FF"));
		}
		$pdf->Cell(25 ,7,($MINISTRY_NAME?(($NUMBER_DISPLAY==2)?convert2thaidigit($MINISTRY_NAME):$MINISTRY_NAME):""),0,0,"L");
		$pdf->Cell(30 ,7,($DEPARTMENT_NAME?(($NUMBER_DISPLAY==2)?convert2thaidigit($DEPARTMENT_NAME):$DEPARTMENT_NAME):""),0,1,"L");
	} // end function print_footer

	if($DPISDB=="odbc"){
		$cmd = " select			a.PER_ID, a.LEVEL_NO, f.LEVEL_NAME, b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_BIRTHDATE, 
											a.PER_SALARY, a.PER_MGTSALARY, a.PER_STARTDATE, a.PER_RETIREDATE,a.PER_OCCUPYDATE,
											a.PER_TYPE, a.PER_OFFNO, a.PER_CARDNO,a.POS_ID, a.POEM_ID, a.POEMS_ID,a.POT_ID,
											a.OT_CODE,a.PN_CODE_F, a.PER_FATHERNAME, a.PER_FATHERSURNAME, a.PN_CODE_M, a.PER_MOTHERNAME, 			
											a.PER_MOTHERSURNAME, c.POS_NO, c.PL_CODE, c.PT_CODE, c.ORG_ID, c.ORG_ID_1, c.ORG_ID_2,
											d.POEM_NO as EMP_POS_NO, d.PN_CODE as EMP_PL_CODE, 
											d.ORG_ID as EMP_ORG_ID, d.ORG_ID_1 as EMP_ORG_ID_1, d.ORG_ID_2 as EMP_ORG_ID_2,
											e.POEMS_NO as EMPSER_POS_NO, e.EP_CODE as EMPSER_PL_CODE, 
											e.ORG_ID as EMPSER_ORG_ID, e.ORG_ID_1 as EMPSER_ORG_ID_1, e.ORG_ID_2 as EMPSER_ORG_ID_2,
											g.POT_NO as EMPTEMP_POS_NO, g.TP_CODE as EMPTEMP_PL_CODE, 
											g.ORG_ID as EMPTEMP_ORG_ID, g.ORG_ID_1 as EMPTEMP_ORG_ID_1, g.ORG_ID_2 as EMPTEMP_ORG_ID_2
				 from			PER_PRENAME b inner join 
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
											a.PER_TYPE, a.PER_OFFNO, a.PER_CARDNO,a.POS_ID, a.POEM_ID, a.POEMS_ID,a.POT_ID,
											a.OT_CODE,a.PN_CODE_F, a.PER_FATHERNAME, a.PER_FATHERSURNAME, a.PN_CODE_M, a.PER_MOTHERNAME, 
											a.PER_MOTHERSURNAME,	c.POS_NO, c.PL_CODE, c.PT_CODE, c.ORG_ID, c.ORG_ID_1, c.ORG_ID_2,
											d.POEM_NO as EMP_POS_NO, d.PN_CODE as EMP_PL_CODE, 
											d.ORG_ID as EMP_ORG_ID, d.ORG_ID_1 as EMP_ORG_ID_1, d.ORG_ID_2 as EMP_ORG_ID_2,
											e.POEMS_NO as EMPSER_POS_NO, e.EP_CODE as EMPSER_PL_CODE, 
											e.ORG_ID as EMPSER_ORG_ID, e.ORG_ID_1 as EMPSER_ORG_ID_1, e.ORG_ID_2 as EMPSER_ORG_ID_2,
											g.POT_NO as EMPTEMP_POS_NO, g.TP_CODE as EMPTEMP_PL_CODE, 
											g.ORG_ID as EMPTEMP_ORG_ID, g.ORG_ID_1 as EMPTEMP_ORG_ID_1, g.ORG_ID_2 as EMPTEMP_ORG_ID_2
						from			PER_PERSONAL a, PER_PRENAME b, PER_POSITION c, PER_POS_EMP d, PER_POS_EMPSER e, PER_LEVEL f,PER_POS_TEMP g
						where		trim(a.PN_CODE)=trim(b.PN_CODE) and 
											a.POS_ID=c.POS_ID(+) and a.POEM_ID=d.POEM_ID(+) and a.POEMS_ID=e.POEMS_ID(+) and a.POT_ID=g.POT_ID(+) and a.LEVEL_NO=f.LEVEL_NO(+) 
											$search_condition
						order by		a.PER_NAME, a.PER_SURNAME ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select	a.PER_ID, a.LEVEL_NO, f.LEVEL_NAME, b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_BIRTHDATE, 
									a.PER_SALARY, a.PER_MGTSALARY, a.PER_STARTDATE, a.PER_RETIREDATE,a.PER_OCCUPYDATE,
									a.PER_TYPE, a.PER_OFFNO, a.PER_CARDNO,a.POS_ID, a.POEM_ID, a.POEMS_ID,a.POT_ID,
									a.OT_CODE,a.PN_CODE_F, a.PER_FATHERNAME, a.PER_FATHERSURNAME, a.PN_CODE_M, a.PER_MOTHERNAME, 
									a.PER_MOTHERSURNAME, c.POS_NO, c.PL_CODE, c.PT_CODE, c.ORG_ID, c.ORG_ID_1, c.ORG_ID_2,
									d.POEM_NO as EMP_POS_NO, d.PN_CODE as EMP_PL_CODE, 
									d.ORG_ID as EMP_ORG_ID, d.ORG_ID_1 as EMP_ORG_ID_1, d.ORG_ID_2 as EMP_ORG_ID_2,
									e.POEMS_NO as EMPSER_POS_NO, e.EP_CODE as EMPSER_PL_CODE, 
									e.ORG_ID as EMPSER_ORG_ID, e.ORG_ID_1 as EMPSER_ORG_ID_1, e.ORG_ID_2 as EMPSER_ORG_ID_2,
									g.POT_NO as EMPTEMP_POS_NO, g.TP_CODE as EMPTEMP_PL_CODE, 
									g.ORG_ID as EMPTEMP_ORG_ID, g.ORG_ID_1 as EMPTEMP_ORG_ID_1, g.ORG_ID_2 as EMPTEMP_ORG_ID_2
				 		from	 	PER_PERSONAL a inner join PER_PRENAME b on (trim(a.PN_CODE)=trim(b.PN_CODE))
																  left join PER_POSITION c on (a.POS_ID=c.POS_ID) 
																  left join PER_POS_EMP d on (a.POEM_ID=d.POEM_ID)
																  left join PER_POS_EMPSER e on (a.POEMS_ID=e.POEMS_ID)
																  left join PER_LEVEL f on (a.LEVEL_NO=f.LEVEL_NO)
																  left join PER_POS_TEMP g on (a.POT_ID=g.POT_ID)
						$search_condition
				 		order by a.PER_NAME, a.PER_SURNAME ";
	}
	$count_data = $db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();
	if($count_data > 0){
		$data_count = 0;
		while($data = $db_dpis->get_array()){
			$data_count++;
			$PER_ID = $data[PER_ID];
			$PER_TYPE = $data[PER_TYPE];
			
			if($PER_TYPE==1){
				$POS_ID = $data[POS_ID];
				$POS_NO = $data[POS_NO];
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
				$POS_NO = $data[EMP_POS_NO];
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
				$POS_NO = $data[EMPSER_POS_NO];
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
				$POS_NO = $data[EMPTEMP_POS_NO];
				$PL_CODE = trim($data[EMPTEMP_PL_CODE]);
				$ORG_ID = $data[EMPTEMP_ORG_ID];
				$ORG_ID_1 = $data[EMPTEMP_ORG_ID_1];
				$ORG_ID_2 = $data[EMPTEMP_ORG_ID_2];

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
			$ORG_NAME = "";
			if($ORG_ID){
			 $cmd = " select ORG_NAME from PER_ORG where ORG_ID = $ORG_ID ";
			 $db_dpis2->send_cmd($cmd);
//			 $db_dpis2->show_error();
			 $data2 = $db_dpis2->get_array();
			 $ORG_NAME = trim($data2[ORG_NAME]);
			}
			$ORG_NAME_1 = "";
			if($ORG_ID_1){
			 $cmd = " select ORG_NAME from PER_ORG where ORG_ID = $ORG_ID_1 ";
			 $db_dpis2->send_cmd($cmd);
//			 $db_dpis2->show_error();
			 $data2 = $db_dpis2->get_array();
			 $ORG_NAME_1 = trim($data2[ORG_NAME]);
			}
			$ORG_NAME_2 = "";
			if($ORG_ID_2){
			 $cmd = " select ORG_NAME from PER_ORG where ORG_ID = $ORG_ID_2 ";
			 $db_dpis2->send_cmd($cmd);
//			 $db_dpis2->show_error();
			 $data2 = $db_dpis2->get_array();
			 $ORG_NAME_2 = trim($data2[ORG_NAME]);
			}
			$LEVEL_NO = trim($data[LEVEL_NO]);
			$FULL_LEVEL_NAME = trim($data[LEVEL_NAME]);
			$PN_NAME = trim($data[PN_NAME]);
			$PER_NAME = trim($data[PER_NAME]);
			$PER_SURNAME = trim($data[PER_SURNAME]);
			$FULLNAME = ($PN_NAME)."$PER_NAME $PER_SURNAME";			
			$PER_CARDNO = $data[PER_CARDNO];			
			$img_file = "";
			if($PER_CARDNO && file_exists($IMG_PATH.$PER_CARDNO.".jpg")) $img_file = $IMG_PATH.$PER_CARDNO.".jpg";			
			$PER_OFFNO = $data[PER_OFFNO];			

			$PER_BIRTHDATE = trim($data[PER_BIRTHDATE]);
			if($PER_BIRTHDATE){
				$arr_temp = explode("-", substr($PER_BIRTHDATE, 0, 10));
				$PER_RETIREDATE = ($arr_temp[0] + 60 + ((substr($PER_BIRTHDATE, 5, 5) >= "10-01")?1:0))."-10-01";
				$PER_RETIREDATE = show_date_format($PER_RETIREDATE,3);
				$PER_BIRTHDATE = show_date_format(substr($PER_BIRTHDATE, 0, 10),3);
			} // end if
			
			$PER_SALARY = $data[PER_SALARY];
			$PER_MGTSALARY = $data[PER_MGTSALARY];

			//วันบรรจุ
			$PER_STARTDATE = show_date_format($data[PER_STARTDATE],3);
			//วันที่เข้าส่วนราชการ
			$PER_OCCUPYDATE = show_date_format($data[PER_OCCUPYDATE],3);
		
			// =====  ข้อมูลบิดา และมารดา =====
			$PN_CODE_F = trim($data[PN_CODE_F]);
			$PN_NAME_F = "";
			if($PN_CODE_F){
			 $cmd = "select PN_NAME from PER_PRENAME WHERE PN_CODE = $PN_CODE_F ORDER BY PN_CODE";
			 $db_dpis2->send_cmd($cmd);
			 $data_dpis2 = $db_dpis2->get_array();
			 $PN_NAME_F = trim($data_dpis2[PN_NAME]);
			}
			$PER_FATHERNAME = trim($data[PER_FATHERNAME]);
			$PER_FATHERSURNAME = trim($data[PER_FATHERSURNAME]);
			$FATHERNAME = ($PN_NAME_F)."$PER_FATHERNAME $PER_FATHERSURNAME";
			
			$PN_CODE_M = trim($data[PN_CODE_M]);
			$PN_NAME_M = "";
			if($PN_CODE_M){			
			 $cmd = "select PN_NAME from PER_PRENAME WHERE PN_CODE = $PN_CODE_M ORDER BY PN_CODE";
			 $db_dpis2->send_cmd($cmd);
			 $data_dpis2 = $db_dpis2->get_array();
			 $PN_NAME_M = trim($data_dpis2[PN_NAME]);
			}			
			$PER_MOTHERNAME = trim($data[PER_MOTHERNAME]);
			$PER_MOTHERSURNAME = trim($data[PER_MOTHERSURNAME]);
			$MOTHERNAME = ($PN_NAME_M)."$PER_MOTHERNAME $PER_MOTHERSURNAME";

			// =====  ข้อมูลคู่สมรส =====
			$cmd = "	select 	MAH_NAME 		from		PER_MARRHIS 
					where	PER_ID=$PER_ID 	order by	MAH_SEQ desc ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$SHOW_SPOUSE = trim($data_dpis2[MAH_NAME]);
			//------------------------------------------------------------------------------------------

			$pdf->AutoPageBreak = false;

			//สำหรับ กพ. 7
			$ORG_NAME_1 = "";		$ORG_NAME_2 = "";
			$POH_EFFECTIVEDATE = "";	$PL_NAME = "";	$TMP_PL_NAME = "";
			$LEVEL_NO = "";	$POH_POS_NO = "";	$POH_SALARY = "";
			$SAH_EFFECTIVEDATE = "";	$SAH_SALARY = "";	$MOV_NAME = "";

			//########################
			//ประวัติการดำรงตำแหน่งข้าราชการ
			//########################
			// 2 || 3 || 4 ???
			if($DPISDB=="odbc"){
				$cmd = " select			a.POH_ORG2 as ORG_NAME_1, a.POH_ORG3 as ORG_NAME_2, a.POH_EFFECTIVEDATE,a.MOV_CODE, d.PL_NAME, 
													a.LEVEL_NO, g.LEVEL_NAME, g.POSITION_LEVEL, a.POH_POS_NO, a.POH_SALARY, a.POH_DOCNO, a.PT_CODE, e.PT_NAME, 
													a.PM_CODE, f.PM_NAME	,a.POH_DOCDATE,a.UPDATE_USER, a.POH_PL_NAME, a.POH_ORG, a.POH_SEQ_NO
							   from				(
														(
															(
																PER_POSITIONHIS a
																left join PER_LINE d on (a.PL_CODE=d.PL_CODE)
															) left join PER_TYPE e on (a.PT_CODE=e.PT_CODE)
														) left join PER_MGT f on (a.PM_CODE=f.PM_CODE)
													) left join PER_LEVEL g on (a.LEVEL_NO=g.LEVEL_NO)
							   where			a.PER_ID=$PER_ID
							   order by	a.POH_EFFECTIVEDATE  asc,a.POH_SALARY asc, a.POH_ENDDATE asc ";					   
			}elseif($DPISDB=="oci8"){
				$cmd = "select		a.POH_ORG2 as ORG_NAME_1, a.POH_ORG3 as ORG_NAME_2, a.POH_EFFECTIVEDATE,a.MOV_CODE, d.PL_NAME, 
													a.LEVEL_NO, g.LEVEL_NAME, g.POSITION_LEVEL, a.POH_POS_NO, a.POH_SALARY, a.POH_DOCNO, a.PT_CODE, e.PT_NAME, 
													a.PM_CODE, f.PM_NAME,a.POH_DOCDATE,a.UPDATE_USER, a.POH_PL_NAME, a.POH_ORG, a.POH_SEQ_NO
								from			PER_POSITIONHIS a, PER_LINE d, PER_TYPE e, PER_MGT f, PER_LEVEL g
								where		a.PER_ID=$PER_ID and
													a.PL_CODE=d.PL_CODE(+) and
													a.PT_CODE=e.PT_CODE(+) and
													a.PM_CODE=f.PM_CODE(+) and 
													a.LEVEL_NO=g.LEVEL_NO(+)
											order by	a.POH_EFFECTIVEDATE  asc,a.POH_SALARY asc, a.POH_ENDDATE asc ";
			}elseif($DPISDB=="mysql"){
				$cmd = "  select 	a.POH_ORG2 as ORG_NAME_1, a.POH_ORG3 as ORG_NAME_2, a.POH_EFFECTIVEDATE,a.MOV_CODE, d.PL_NAME, 
													a.LEVEL_NO, g.LEVEL_NAME, g.POSITION_LEVEL, a.POH_POS_NO, a.POH_SALARY, a.POH_DOCNO, a.PT_CODE, e.PT_NAME, 
													a.PM_CODE, f.PM_NAME	,a.POH_DOCDATE,a.UPDATE_USER, a.POH_PL_NAME, a.POH_ORG, a.POH_SEQ_NO
								  from			(
														(
															(
																PER_POSITIONHIS a left join PER_LINE d on (a.PL_CODE=d.PL_CODE)
															) 	left join PER_TYPE e on (a.PT_CODE=e.PT_CODE)
														) left join PER_MGT f on (a.PM_CODE=f.PM_CODE)
													) left join PER_LEVEL g on (a.LEVEL_NO=g.LEVEL_NO)
								   where		a.PER_ID=$PER_ID
								   			order by	a.POH_EFFECTIVEDATE  asc,a.POH_SALARY asc, a.POH_ENDDATE asc ";
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
									$POSITION_LEVEL = wordwapPOSITION_LEVEL(trim($data2[POSITION_LEVEL]));
//									if ($POSITION_LEVEL=="ชำนาญการพิเศษ") {
//										$POSITION_LEVEL = "ชำนาญการ"."\n"."พิเศษ";
////										$testpos = str_replace("\n","<BR>",$POSITION_LEVEL);
////										echo "POSITION_LEVEL=$testpos<BR>";
//									}
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
                                        $cmd = " select MOV_NAME, MOV_SUB_TYPE from PER_MOVMENT pcd where MOV_CODE='$MOV_CODE' ";
                                        $db_dpis3->send_cmd($cmd);
                                        //echo "<br>$cmd<br>";
                                        //$db_dpis3->show_error();
                                        $data3 = $db_dpis3->get_array();
                                        $MOV_NAME = $data3[MOV_NAME];
                                    
										$TMP_PL_NAME = $POH_PL_NAME."\n".$POH_ORG;
                                   // echo $chk_mov_name;
                                        if($chk_mov_name=="Y"){ //ถ้าติกเเสดงประเภทการเคลื่อนไหว
                                            $TMP_PL_NAME = $MOV_NAME."\n".$TMP_PL_NAME; 
                                        }else{
                                            $TMP_PL_NAME = (trim($TMP_PL_NAME)?trim($TMP_PL_NAME):"-");
                                        }
										//if($PM_CODE && $PM_CODE!="9999") $TMP_PL_NAME = $PM_NAME." ($TMP_PL_NAME)";
//									}
									$TMP_PL_NAME = (trim($TMP_PL_NAME)?trim($TMP_PL_NAME):"-");
									$POH_POS_NO = trim($data2[POH_POS_NO]);	$POH_POS_NO = ($POH_POS_NO?$POH_POS_NO:"-");
									$POH_SALARY = $data2[POH_SALARY];	
									$POH_DOCNO = (trim($data2[POH_DOCNO]))? $data2[POH_DOCNO] : "-" ;
									$MOV_CODE = trim($data2[MOV_CODE]);
									$POH_DOCDATE_ORDER=$data2[POH_DOCDATE];
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
										if ($BKK_FLAG==1) 
											$POH_DOCNO = $data2[POH_DOCNO]." ".$POH_DOCDATE;
										else
											$POH_DOCNO = $data2[POH_DOCNO]."\n".$POH_DOCDATE."\n".$USRNAME;
										if (trim($data2[POH_DOCNO]) && strpos($data2[POH_DOCNO],"คส.") == false)
											$POH_DOCNO = "คส. ".$POH_DOCNO;
									}

									//หาประเภทการเคลื่อนไหวของประวัติการดำรงตำแหน่งข้าราชการ
									
									$MOV_SUB_TYPE = $data3[MOV_SUB_TYPE];
									if ($MOV_SUB_TYPE==9) {
										$POH_POS_NO = $POSITION_LEVEL = $POH_SALARY = "";
									}
                                                                        //เก็บลง array ของ POSTION HIS
                                                                        if($chk_order_date_command=='Y'){ //ต้องการเรียงลำดับตามวันที่ลงนามในคำสั่ง http://dpis.ocsc.go.th/Service/node/1304
                                                                            $ARR_POSITIONHIS[$PER_ID][] = array(
                                                                                'DOCDATE'=>$POH_DOCDATE_ORDER,
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
                                                                        }else{
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
                                                                        }    
									
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
																	b.SAH_ORG, b.SM_CODE, b.SAH_PAY_NO, b.SAH_POS_NO, b.SAH_PERCENT_UP, b.SAH_SALARY_EXTRA
												 from			PER_SALARYHIS b
												 inner join 	PER_MOVMENT c	 on	b.MOV_CODE=c.MOV_CODE					
												 where		b.PER_ID=$PER_ID and b.MOV_CODE!='1901'
												 order by		b.SAH_SALARY asc, b.SAH_EFFECTIVEDATE asc ";							   
							}elseif($DPISDB=="oci8"){
								$cmd = " select	SUBSTR(b.SAH_EFFECTIVEDATE,1,10) as SAH_EFFECTIVEDATE, c.MOV_NAME, b.SAH_SALARY, 
                                                                            b.SAH_DOCNO, b.SAH_DOCDATE, b.UPDATE_USER, b.SAH_SEQ_NO, b.LEVEL_NO, b.SAH_POSITION, 
                                                                            b.SAH_ORG, b.SM_CODE, b.SAH_PAY_NO, b.SAH_POS_NO, b.SAH_PERCENT_UP, b.SAH_SALARY_EXTRA
                                                                            from PER_SALARYHIS b, PER_MOVMENT c 
                                                                        where b.PER_ID=$PER_ID and b.MOV_CODE=c.MOV_CODE and b.MOV_CODE!='1901' 
                                                                        order by b.SAH_SALARY asc, b.SAH_EFFECTIVEDATE asc ";		   					   
							}elseif($DPISDB=="mysql"){
								$cmd = " select			LEFT(b.SAH_EFFECTIVEDATE,10) as SAH_EFFECTIVEDATE, c.MOV_NAME, b.SAH_SALARY, 
																	b.SAH_DOCNO, b.SAH_DOCDATE, b.UPDATE_USER, b.SAH_SEQ_NO, b.LEVEL_NO, b.SAH_POSITION, 
																	b.SAH_ORG, b.SM_CODE, b.SAH_PAY_NO, b.SAH_POS_NO, b.SAH_PERCENT_UP, b.SAH_SALARY_EXTRA
												 from			PER_SALARYHIS b  inner join 	PER_MOVMENT c	 on	b.MOV_CODE=c.MOV_CODE
												 where			b.PER_ID=$PER_ID and b.MOV_CODE!='1901'
												 order by		b.SAH_SALARY asc, b.SAH_EFFECTIVEDATE asc ";
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
									if (!$SAH_PAY_NO) $SAH_PAY_NO = $data2[SAH_POS_NO];
									$SAH_PERCENT_UP = $data2[SAH_PERCENT_UP];		
									$SAH_SALARY_EXTRA = $data2[SAH_SALARY_EXTRA];		
                                                                        
									if ($SAH_PERCENT_UP) $MOV_NAME = $MOV_NAME . " " . number_format($SAH_PERCENT_UP, 3) . " %";		
									if (strpos($MOV_NAME,"เต็มขั้น") !== false) $MOV_NAME = $MOV_NAME . " " . number_format($SAH_SALARY_EXTRA, 3);	

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
                                                                        
                                                                        $SAH_DOCDATE_ORDER=$data2[SAH_DOCDATE];
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
										if ($BKK_FLAG==1) 
											$SAH_DOCNO = $data2[SAH_DOCNO]." ".$SAH_DOCDATE;
										else
											$SAH_DOCNO = $data2[SAH_DOCNO]."\n".$SAH_DOCDATE."\n".$USRNAME;
										if (trim($data2[SAH_DOCNO]) && strpos($data2[SAH_DOCNO],"คส.") == false)
											$SAH_DOCNO = "คส. ".$SAH_DOCNO;
									}

									$cmd = " select POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$LEVEL_NO' ";
									$db_dpis3->send_cmd($cmd);
									//echo "<br>$cmd<br>";
									//$db_dpis3->show_error();
									$data3 = $db_dpis3->get_array();
									$POSITION_LEVEL = wordwapPOSITION_LEVEL(trim($data3[POSITION_LEVEL]));
//									if ($POSITION_LEVEL=="ชำนาญการพิเศษ") {
//										$POSITION_LEVEL = "ชำนาญการ"."\n"."พิเศษ";
////										$testpos = str_replace("\n","<BR>",$POSITION_LEVEL);
////										echo "POSITION_LEVEL=$testpos<BR>";
//									}
//                                                                        if ($POSITION_LEVEL=="กลุ่มงานบริหารทั่วไป") {
//										$POSITION_LEVEL = "กลุ่มงาน"."\n"."บริหารทั่วไป";
//									}
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
                                                                            if($chk_order_date_command=='Y'){ //ต้องการเรียงลำดับตามวันที่ลงนามในคำสั่ง http://dpis.ocsc.go.th/Service/node/1304
                                                                                $ARR_SALARYHIS[$PER_ID][] = array(
                                                                                    'DOCDATE'=>$SAH_DOCDATE_ORDER,
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
                                                                            }else{
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
                                                                            }
                                                                            
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
						for($in=0; $in < count($ARRAY_POH_SAH[$PER_ID]);$in++){
							//เก็บค่าวันที่
                                                    if($chk_order_date_command=='Y'){ //ต้องการเรียงลำดับตามวันที่ลงนามในคำสั่ง http://dpis.ocsc.go.th/Service/node/1304
                                                        $DATE_HIS[] = array(
                                                            'DOCDATE'=>$ARRAY_POH_SAH[$PER_ID][$in]['DOCDATE'],
                                                            'DATE'=>$ARRAY_POH_SAH[$PER_ID][$in]['DATE'],
                                                            'SEQ'=>$ARRAY_POH_SAH[$PER_ID][$in]['SEQ'],
                                                            'MOVE'=>$ARRAY_POH_SAH[$PER_ID][$in]['MOVE'],
                                                            'POS_NAME'=>$ARRAY_POH_SAH[$PER_ID][$in]['POS_NAME'],
                                                            'POS_NO'=>$ARRAY_POH_SAH[$PER_ID][$in]['POS_NO'],
                                                            'LEVEL'=>$ARRAY_POH_SAH[$PER_ID][$in]['LEVEL'],
                                                            'FOOTERLEVEL'=>$ARRAY_POH_SAH[$PER_ID][$in]['FOOTERLEVEL'],
                                                            'SALARY'=>$ARRAY_POH_SAH[$PER_ID][$in]['SALARY'],
                                                            'DOC_NO'=>$ARRAY_POH_SAH[$PER_ID][$in]['DOC_NO']
                                                        );
                                                    }else{
                                                        $DATE_HIS[] = array(
                                                            'DATE'=>$ARRAY_POH_SAH[$PER_ID][$in]['DATE'],
                                                            'SEQ'=>$ARRAY_POH_SAH[$PER_ID][$in]['SEQ'],
                                                            'MOVE'=>$ARRAY_POH_SAH[$PER_ID][$in]['MOVE'],
                                                            'POS_NAME'=>$ARRAY_POH_SAH[$PER_ID][$in]['POS_NAME'],
                                                            'POS_NO'=>$ARRAY_POH_SAH[$PER_ID][$in]['POS_NO'],
                                                            'LEVEL'=>$ARRAY_POH_SAH[$PER_ID][$in]['LEVEL'],
                                                            'FOOTERLEVEL'=>$ARRAY_POH_SAH[$PER_ID][$in]['FOOTERLEVEL'],
                                                            'SALARY'=>$ARRAY_POH_SAH[$PER_ID][$in]['SALARY'],
                                                            'DOC_NO'=>$ARRAY_POH_SAH[$PER_ID][$in]['DOC_NO']
                                                        );
                                                    }
							
						} // end for
						unset($ARRAY_POH_SAH);
						foreach ($DATE_HIS as $key => $value) {		//กรณีที่วันที่เดียวกัน แต่ต้องเอาเงินเดือนน้อยกว่าแสดงก่อน
                                                    if($chk_order_date_command=='Y'){ //ต้องการเรียงลำดับตามวันที่ลงนามในคำสั่ง http://dpis.ocsc.go.th/Service/node/1304
                                                        $DOCDATE[$key]  = $value['DOCDATE'];
                                                    }
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
                                                if($chk_order_date_command=='Y'){ //ต้องการเรียงลำดับตามวันที่ลงนามในคำสั่ง http://dpis.ocsc.go.th/Service/node/1304
                                                    array_multisort($DOCDATE, SORT_ASC, $SALARY, SORT_NUMERIC, SORT_ASC, $DATE_HIS);
                                                }else{
                                                    array_multisort($DATE, SORT_ASC, $SALARY, SORT_NUMERIC, SORT_ASC, $DATE_HIS);
                                                }
						
						$POH_SAH_HIS[$PER_ID]=$DATE_HIS;
						unset($DATE_HIS);
						/***print("<pre>");
						print_r($POH_SAH_HIS);
						print("</pre>");***/
			###IN CASE POSITIONHIS #######
			/////////////////////////////////////////////////////////////////
//			for($history_index=0; $history_index<count($arr_history_name); $history_index++){
//				$HISTORY_NAME = $arr_history_name[$history_index];
				if ($get_fall == "a") {
					$page_no = 1;
					$first_rec = 1;
				} else {
					$page_no = $get_fall;
					$first_rec = $get_first_rec;
					$end_rec =$get_end_rec;
				}
				if ($get_line==1) { $get_fall = "a"; }// ถ้าเป็นการเริ่มพิมพ์ทั้งหน้า เปลี่ยน $get_fall เป็น 'a'
//				switch($HISTORY_NAME){
//					case "POSITIONHIS" : //รวมประวัติรับราชการ + เลื่อนขั้นเงินเดือนเข้าด้วยกัน
//					echo "get_line=$get_line<br>";
//					if ($get_line != "1") {
						print_header($first_rec);
					
						//ส่วนแสดงเนื้อหา หลังจากจัดเรียงข้อมูลแล้ว
						if(is_array($POH_SAH_HIS) && !empty($POH_SAH_HIS)){
							$count_positionhis=count($POH_SAH_HIS[$PER_ID]);
							$positionhis_count=$first_rec-1;
							//ถ้าไม่ได้ใส่ line มาคือแสดงทั้งหมด 
							if(!isset($get_line) || $get_line==""){		$get_line=$count_positionhis;		}
							$linenum = ceil($pdf->y / 7);  // พิมพ์มาแล้วกี่บรรทัด
							$linecnt = 0;
							$linenumshow = 0;
							if ($get_fall != "a" && $get_line > 1) {
								$pdf->SetFont($font,'',14);
								$pdf->x=$page_start_x;
								$pdf->SetTextColor(hexdec("E4"),hexdec("E4"),hexdec("E4"));//สีเทา
								$pdf->SetDrawColor(hexdec("00"),hexdec("00"),hexdec("00")); //สีดำ
								for($iline=1; $iline < $get_line; $iline++){
									$linenum++;
									$linenumshow++;
//									$pdf->Cell($heading_width[0] ,7,"(".$iline."|".$pdf->y.")","TBRL",0,"C"); //แสดงบรรทัดที่
//									$pdf->Cell($heading_width[0] ,7,"(".$iline.")","TBRL",0,"C"); //แสดงบรรทัดที่
									$pdf->Cell($heading_width[0] ,7,"","",0,"C"); //แสดงบรรทัดที่
									$pdf->Cell($heading_width[1] ,7,"","",0,"L"); // ส่วนพิมพ์วันที่
									$pdf->Cell($heading_width[2], 7, "","",0, "L");  // ส่วนพิมพ์ตำแหน่ง
									$pdf->Cell($heading_width[3] ,7,"","",0,"C");  // ส่วนพิมพ์เลขตำแหน่ง
									$pdf->Cell($heading_width[4] ,7,"","",0,"C");  // ส่วนพิมพ์ระดับ
									$pdf->Cell($heading_width[5] ,7,"","",0,"C");  // ส่วนพิมพ์เงินเดือน
									$pdf->Cell($heading_width[6], 7, "","",1, "L");  // ส่วนพิมพ์ เลขที่เอกสาร
								}
							}
//							echo "fall=$get_fall, line=$get_line, first line=$first_rec<br>";
							if($end_rec){ //ถ้ามีการระบุถึงรายการที่์ ให้เเสดงข้อมูลเท่าที่ระบุ จะมีผลเฉพาะกดปุ่ม ต่อใบ ก.พ.
								$count_positionhis = $end_rec;
							}else{
								$count_positionhis = $count_positionhis;
							}

							for($in=$first_rec-1; $in < $count_positionhis; $in++){
									$positionhis_count++;
									$linecnt++;
									if($POH_SAH_HIS[$PER_ID][$in]['DATE']){
										$DATE_POH_SAH = show_date_format(substr($POH_SAH_HIS[$PER_ID][$in]['DATE'], 0, 10),$date_format);
									}

									//หาระดับตำแหน่ง (1,2,3,4,5,6,7,8,9);
									$cmd = "select LEVEL_NAME, POSITION_TYPE from PER_LEVEL where LEVEL_NO='".$POH_SAH_HIS[$PER_ID][$in]['LEVEL']."' order by LEVEL_NO";
//									echo "$cmd<br>";
									$db_dpis2->send_cmd($cmd);
//									$db_dpis2->show_error();
									$data2 = $db_dpis2->get_array();
									$LEVEL_NAME1 = trim($data2[LEVEL_NAME])."<<<";
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
//									echo "bf-".$POH_SAH_HIS[$PER_ID][$in]['POS_NAME']."[".$dlen."]<br>";
									$temptext = $pdf->thaiCutLinePDF($POH_SAH_HIS[$PER_ID][$in]['POS_NAME'], $heading_width[2], "\n");
									$sub_pos = explode("\n", $temptext);
									$sub_level = explode("\n", $POH_SAH_HIS[$PER_ID][$in]['LEVEL']);
									$prt_level_line = count($sub_level);
									$prt_pos_line = 0;
									$prt_pos_line = count($sub_pos);
									$prt_pos_line = $prt_pos_line - (!trim($sub_pos[$prt_pos_line-1]) ? 1 -  (!trim($sub_doc[$prt_doc_line-2]) ? 1 : 0) : 0);
									$prt_max_line = ($prt_doc_line > $prt_pos_line ? $prt_doc_line : $prt_pos_line);
									$prt_max_line = ($prt_level_line > $prt_max_line ? $prt_level_line : $prt_max_line); 
//									$aa = (($linenum + $prt_max_line) * 7) + 14;
//									echo "$aa = ".$aa." > ".$pdf->h.",y=".$pdf->y.",date=".$DATE_POH_SAH."<br>";
									if (((($linenum + $prt_max_line) * 7) + 14) > $pdf->h) {
									// $linenum = จำนวนบรรทัดที่พิมพ์ไปแล้ว + 
									//	$prt_max_line = จำนวนบรรทัดที่จะพิมพ์สำหรับรายการนี้ แล้วคูณด้วย 7 คือความสูงของบรรทัด
									//  แล้วบวกด้วย 7 คือเว้นเผื่อขอบล่าง 1 บรรทัด (ถ้าจะเว้น 2 บรรทัดก็ บวกด้วย 14)
										print_footer($last_footer_level);

										$page_no++;
										$pdf->AddPage();
										$get_fall = "a"; // ถ้าสั่งพิมพ์แบบต่อบรรทัด เมื่อมีการเริ่มหน้าใหม่ ก็จะพิมพ์เหมือนกับพิมพ์ทั้งหมด จึงกำหนดให้ $get_fall = 'a'
										$start_y = $pdf->y;
										print_header($in+1);
										$max_y = $pdf->y;
										$linenum = ceil($pdf->y / 7);  // พิมพ์มาแล้วกี่บรรทัด
										$linecnt = 1;
										$linenumshow = 0;
									}
									$last_footer_level = $POH_SAH_HIS[$PER_ID][$in]['FOOTERLEVEL'];
									for($sub_line_i = 0; 	$sub_line_i < $prt_max_line; $sub_line_i++) {
										$linenum++;
										$linenumshow++;
										$border = "LR"; // โปรแกรมนี้จะพิมพ์เฉพาะ เส้นแนวตั้ง
										$pdf->SetFont($font,'',14);
										$pdf->x=$page_start_x;
										$start_y = $pdf->y; $max_y = $pdf->y;
										
										if ($get_fall <> "a") { // การพิมพ์ตารางถ้าเป็นการพิมพ์ต่อ จะไม่พิมพ์ตารางเลย
											$pdf->SetDrawColor(hexdec("FF"),hexdec("FF"),hexdec("FF")); // สีขาว
										} else {
											$pdf->SetDrawColor(hexdec("00"),hexdec("00"),hexdec("00")); //สีดำ
										}
										$pdf->SetFillColor(hexdec("FF"),hexdec("FF"),hexdec("FF"));

//										if ($sub_line_i == 0) {
//											if ($get_fall <> "a"  && $linecnt < $get_line) {
//											if ($get_fall <> "a"  && $linenumshow < $get_line) {
											if ($get_fall <> "a") {
												$pdf->SetTextColor(hexdec("FF"),hexdec("FF"),hexdec("FF")); // สีขาว ไม่พิมพ์
											} else {
//												$pdf->SetTextColor(hexdec("E4"),hexdec("E4"),hexdec("E4"));//สีเทา
												$pdf->SetTextColor(hexdec("CD"),hexdec("CD"),hexdec("CD"));//สีเทาเข้มขึ้นอีก
											}
//										} else {
//											$pdf->SetTextColor(hexdec("FF"),hexdec("FF"),hexdec("FF"));//สีขาว คือไม่พิมพ์ แบบเก่า record
//										}
//										$numline = "(".$positionhis_count.") ";
//										$numline = "(".$linecnt.") "; // แบบเก่าพิมพ์ราย record
//										$numline = "(".$linenumshow."|".$pdf->y.") ";
										$numline = "(".$linenumshow.") ";
										$pdf->Cell($heading_width[0] ,7,(($NUMBER_DISPLAY==2)?convert2thaidigit($numline):$numline),"",0,"C");		//แสดงบรรทัดที่

										$start_x = $pdf->x;
										//-------------------------------------
										if ($sub_line_i == 0) {
//											if ($get_fall <> "a"  && $linecnt < $get_line) {
//											if ($get_fall <> "a"  && $linenumshow < $get_line) {
//											if ($get_fall <> "a") {
//												$pdf->SetTextColor(hexdec("FF"),hexdec("FF"),hexdec("FF"));
//											} else {
												$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));	//สีดำ
//											}
										} else {
											$pdf->SetTextColor(hexdec("FF"),hexdec("FF"),hexdec("FF"));//สีขาว คือไม่พิมพ์
										}
										$pdf->SetFillColor(hexdec("FF"),hexdec("FF"),hexdec("FF"));
										$pdf->Cell($heading_width[1] ,7,($DATE_POH_SAH?(($NUMBER_DISPLAY==2)?convert2thaidigit($DATE_POH_SAH):$DATE_POH_SAH):""),$border,0,"L");

										//กำหนดชื่อตำแหน่ง -----------------------
										if ($sub_line_i < $prt_pos_line) {
//											if ($get_fall <> "a"  && $linecnt < $get_line) {
//											if ($get_fall <> "a"  && $linenumshow < $get_line) {
//											if ($get_fall <> "a") {
//												$pdf->SetTextColor(hexdec("FF"),hexdec("FF"),hexdec("FF"));
//											} else {
												if ($f_pos_color == "blue") {
													$pdf->SetTextColor(hexdec("65"),hexdec("00"),hexdec("CA"));	//สีน้ำเงิน
												} else {
													$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00")); // สีดำ
												}
//											}
											$prt_pos = $sub_pos[$sub_line_i];
										} else {
											$prt_pos = "";
										}
										$pdf->SetFillColor(hexdec("FF"),hexdec("FF"),hexdec("FF"));
										
										$pdf->Cell($heading_width[2], 7, ($prt_pos ? (($NUMBER_DISPLAY==2)?convert2thaidigit($prt_pos):$prt_pos) : ""),$border,0, "L");

										if ($sub_line_i == 0) {
//											if ($get_fall <> "a"  && $linecnt < $get_line) {
//											if ($get_fall <> "a"  && $linenumshow < $get_line) {
//											if ($get_fall <> "a") {
//												$pdf->SetTextColor(hexdec("FF"),hexdec("FF"),hexdec("FF"));
//											} else {
												$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
//											}
										} else {
											$pdf->SetTextColor(hexdec("FF"),hexdec("FF"),hexdec("FF"));//สีขาว คือไม่พิมพ์
										}
										$pdf->Cell($heading_width[3] ,7,($POH_SAH_HIS[$PER_ID][$in]['POS_NO']?(($NUMBER_DISPLAY==2)?convert2thaidigit($POH_SAH_HIS[$PER_ID][$in]['POS_NO']):$POH_SAH_HIS[$PER_ID][$in]['POS_NO']):""),$border,0,"C");

										if ($sub_line_i < $prt_level_line) {
//											if ($get_fall <> "a"  && $linecnt < $get_line) {
//											if ($get_fall <> "a"  && $linenumshow < $get_line) {
//											if ($get_fall <> "a") {
//												$pdf->SetTextColor(hexdec("FF"),hexdec("FF"),hexdec("FF"));
//											} else {
												$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
//											}
											$prt_level = $sub_level[$sub_line_i];
										} else {
											$prt_level = "";
										}
										$pdf->Cell($heading_width[4], 7, ($prt_level ? (($NUMBER_DISPLAY==2)?convert2thaidigit($prt_level):$prt_level) : ""),$border,0, "C");
//										$pdf->Cell($heading_width[4] ,7,($POH_SAH_HIS[$PER_ID][$in]['LEVEL']?$POH_SAH_HIS[$PER_ID][$in]['LEVEL']:""),$border,0,"C");
										if ($sub_line_i == 0) {
											$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
										} else {
											$pdf->SetTextColor(hexdec("FF"),hexdec("FF"),hexdec("FF"));//สีขาว คือไม่พิมพ์
										}
										$salary_text = ($POH_SAH_HIS[$PER_ID][$in]['SALARY']?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($POH_SAH_HIS[$PER_ID][$in]['SALARY'])):number_format($POH_SAH_HIS[$PER_ID][$in]['SALARY'])):"-");
										$pdf->Cell($heading_width[5] ,7,(($NUMBER_DISPLAY==2)?convert2thaidigit($salary_text):$salary_text),$border,0,"C");
										if ($sub_line_i < $prt_doc_line) {
//											if ($get_fall <> "a"  && $linecnt < $get_line) {
//											if ($get_fall <> "a"  && $linenumshow < $get_line) {
//											if ($get_fall <> "a") {
//												$pdf->SetTextColor(hexdec("FF"),hexdec("FF"),hexdec("FF"));
//											} else {
												$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
//											}
											$prt_doc = $sub_doc[$sub_line_i];
										} else {
											$prt_doc = "";
										}
										$pdf->Cell($heading_width[6], 7, ($prt_doc ? (($NUMBER_DISPLAY==2)?convert2thaidigit($prt_doc):$prt_doc) : ""),$border,1, "L");
										
										$pdf->x = $start_x + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6];

										$line_start_y = $start_y;		$line_start_x = $start_x;
										$line_end_y = $max_y;		$line_end_x = $pdf->x;
										if ($get_fall == "a") {
											$pdf->SetDrawColor(hexdec("E4"),hexdec("E4"),hexdec("E4"));//สีเทา
										} else {
											$pdf->SetDrawColor(hexdec("FF"),hexdec("FF"),hexdec("FF"));
										}
										$pdf->Line($line_start_x, $line_end_y, $line_end_x, $line_end_y);

										if($pdf->y > $max_y) $max_y = $pdf->y;
										$pdf->y = $max_y;
									} //end for $sub_line_i
									// พิมพ์ เว้นบรรทัด
									$linenum++;
									$border = "LR"; // โปรแกรมนี้จะพิมพ์เฉพาะ เส้นแนวตั้ง

									$pdf->x=$page_start_x;
									$start_y = $pdf->y; $max_y = $pdf->y;

// พิมพ์ส่วนที่เป็น line num
									$linenumshow++;
//									if ($get_fall <> "a"  && $linecnt < $get_line) {
//									if ($get_fall <> "a"  && $linenumshow < $get_line) {
									if ($get_fall <> "a") {
										$pdf->SetTextColor(hexdec("FF"),hexdec("FF"),hexdec("FF")); // สีขาว ไม่พิมพ์
									} else {
//										$pdf->SetTextColor(hexdec("E4"),hexdec("E4"),hexdec("E4"));//สีเทา
										$pdf->SetTextColor(hexdec("CD"),hexdec("CD"),hexdec("CD"));//สีเทาเข้มขึ้นอีก
									}

//									$numline = "(".$linenumshow."|".$pdf->y.") ";
									$numline = "(".$linenumshow.") ";
									$pdf->Cell($heading_width[0] ,7,(($NUMBER_DISPLAY==2)?convert2thaidigit($numline):$numline),"",0,"C");		//แสดงบรรทัดที่
// พิมพ์ส่วนที่เป็น line num

									if ($get_fall == "a")
										$pdf->SetDrawColor(hexdec("00"),hexdec("00"),hexdec("00"));//สีดำ
									else
										$pdf->SetDrawColor(hexdec("FF"),hexdec("FF"),hexdec("FF"));
									
//									$pdf->Cell($heading_width[0] ,7,"",$border,0,"C"); //แสดงบรรทัดที่
									$pdf->Cell($heading_width[1] ,7,"",$border,0,"L"); // ส่วนพิมพ์วันที่
									$pdf->Cell($heading_width[2], 7, "",$border,0, "L");  // ส่วนพิมพ์ตำแหน่ง
									$pdf->Cell($heading_width[3] ,7,"",$border,0,"C");  // ส่วนพิมพ์เลขตำแหน่ง
									$pdf->Cell($heading_width[4] ,7,"",$border,0,"C");  // ส่วนพิมพ์ระดับ
									$pdf->Cell($heading_width[5] ,7,"",$border,0,"C");  // ส่วนพิมพ์เงินเดือน
									$pdf->Cell($heading_width[6], 7, "",$border,1, "L");  // ส่วนพิมพ์ เลขที่เอกสาร

									$pdf->x = $start_x + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6];

									$line_start_y = $start_y;		$line_start_x = $start_x;
									$line_end_y = $max_y;		$line_end_x = $pdf->x;

									if ($get_fall == "a")
										$pdf->SetDrawColor(hexdec("E4"),hexdec("E4"),hexdec("E4"));//สีเทา
									else
										$pdf->SetDrawColor(hexdec("FF"),hexdec("FF"),hexdec("FF"));

									$pdf->Line($line_start_x, $line_end_y, $line_end_x, $line_end_y);

									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->y = $max_y;
									// พิมพ์ เว้นบรรทัด
							} // end for $in
							if ($get_fall == "a") {
								if ((($linenum * 7) + 14) < $pdf->h) {	
									// $linenum = จำนวนบรรทัดที่พิมพ์ไปแล้ว คูณด้วย 7 คือความสูงของบรรทัด
									//  แล้วบวกด้วย 7 คือเว้นเผื่อขอบล่าง 1 บรรทัด (ถ้าจะเว้น 2 บรรทัดก็ บวกด้วย 14)
																
									// loop เพื่อพิมพ์ ตารางส่วนที่เหลือของแผ่นสุดท้าย
									while ((($linenum * 7) + 14) < $pdf->h) {
											$linenum++;
											$border = "LR"; // โปรแกรมนี้จะพิมพ์เฉพาะ เส้นแนวตั้ง

											$pdf->SetDrawColor(hexdec("00"),hexdec("00"),hexdec("00"));//สีดำ

											$pdf->x=$page_start_x;
											$start_y = $pdf->y; $max_y = $pdf->y;

											$linenumshow++;
//											$pdf->SetTextColor(hexdec("E4"),hexdec("E4"),hexdec("E4"));//สีเทา
											$pdf->SetTextColor(hexdec("CD"),hexdec("CD"),hexdec("CD"));//สีเทาเข้มขึ้นอีก
											$numline = "(".$linenumshow.") ";
											$pdf->Cell($heading_width[0] ,7,(($NUMBER_DISPLAY==2)?convert2thaidigit($numline):$numline),"",0,"C");		//แสดงบรรทัดที่
											
											$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));//สีดำ
											$pdf->Cell($heading_width[1] ,7,"",$border,0,"L"); // ส่วนพิมพ์วันที่
											$pdf->Cell($heading_width[2], 7, "",$border,0, "L");  // ส่วนพิมพ์ตำแหน่ง
											$pdf->Cell($heading_width[3] ,7,"",$border,0,"C");  // ส่วนพิมพ์เลขตำแหน่ง
											$pdf->Cell($heading_width[4] ,7,"",$border,0,"C");  // ส่วนพิมพ์ระดับ
											$pdf->Cell($heading_width[5] ,7,"",$border,0,"C");  // ส่วนพิมพ์เงินเดือน
											$pdf->Cell($heading_width[6], 7, "",$border,1, "L");  // ส่วนพิมพ์ เลขที่เอกสาร

											$pdf->x = $start_x + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6];

											$line_start_y = $start_y;		$line_start_x = $start_x;
											$line_end_y = $max_y;		$line_end_x = $pdf->x;

											$pdf->SetDrawColor(hexdec("E4"),hexdec("E4"),hexdec("E4"));//สีเทา
											$pdf->Line($line_start_x, $line_end_y, $line_end_x, $line_end_y);

											if($pdf->y > $max_y) $max_y = $pdf->y;
											$pdf->y = $max_y;
											
//											if ((($linenum * 7) + 14) > $pdf->h) {
//												print_footer();
//												break;
//											}
									} // end while loop
								} // end if
   							    print_footer("");
							} // end if ($get_fall == "a")
							if($in<=0){	//}else{
								$pdf->SetFont($font,'b',16);
								$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
								$pdf->Cell(200,7,"********** ไม่มีข้อมูล **********","LBR",1,'C');
							} // end if	
						} // end if(is_array($POH_SAH_HIS) && !empty($POH_SAH_HIS))
//					} else { // else if ($get_line != "1")
//						$get_fall = "a";
//						print_header("");
//						$linenum = ceil($pdf->y / 7);  // พิมพ์มาแล้วกี่บรรทัด
//						while ((($linenum * 7) + 14) < $pdf->h) {
//								$linenum++;
//								$border = "LR"; // โปรแกรมนี้จะพิมพ์เฉพาะ เส้นแนวตั้ง

//								$pdf->SetDrawColor(hexdec("00"),hexdec("00"),hexdec("00"));//สีดำ

//								$pdf->x=$page_start_x;
//								$start_y = $pdf->y; $max_y = $pdf->y;

//								$pdf->Cell($heading_width[0] ,7,"","",0,"C");		// ส่วนพิมพ์บรรทัดที่
//								$pdf->Cell($heading_width[1] ,7,"",$border,0,"L"); // ส่วนพิมพ์วันที่
//								$pdf->Cell($heading_width[2], 7, "",$border,0, "L");  // ส่วนพิมพ์ตำแหน่ง
//								$pdf->Cell($heading_width[3] ,7,"",$border,0,"C");  // ส่วนพิมพ์เลขตำแหน่ง
//								$pdf->Cell($heading_width[4] ,7,"",$border,0,"C");  // ส่วนพิมพ์ระดับ
//								$pdf->Cell($heading_width[5] ,7,"",$border,0,"C");  // ส่วนพิมพ์เงินเดือน
//								$pdf->Cell($heading_width[6], 7, "",$border,1, "L");  // ส่วนพิมพ์ เลขที่เอกสาร

//								$pdf->x = $start_x + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6];

//								$line_start_y = $start_y;		$line_start_x = $start_x;
//								$line_end_y = $max_y;		$line_end_x = $pdf->x;

//								$pdf->SetDrawColor(hexdec("E4"),hexdec("E4"),hexdec("E4"));//สีเทา
//								$pdf->Line($line_start_x, $line_end_y, $line_end_x, $line_end_y);

//								if($pdf->y > $max_y) $max_y = $pdf->y;
//								$pdf->y = $max_y;
//						} // end while loop
//						print_footer("");
//					} // end if ($get_line != "1")						
//						break;
					/*case "SALARYHIS" :			//ในนี้ไม่มี ถูกรวมอยู่กับ POSITIONHIS แล้ว
						//ลบข้อ 2 ประวัติการเลื่อนขั้นเงินเดือน
						break;*/
//				} // end switch
//			} // end for $history_index
			
			//___if($data_count < $count_data) $pdf->AddPage();
			//echo "$in < $count_data<br>";
			if($data_count < $count_data) $pdf->AddPage();
//			$pdf->AddPage();
		} // end while
		
		// insert log 
		$RefFileName = get_ref_filename();		// จาก php_scripts/function_share.php
		$Login_by = get_user_login($SESS_USERID,$SESS_PER_ID);		// จาก php_scripts/function_share.php
		//echo $RefFileName."#### [$SESS_USERID / $SESS_PER_ID] พิมพ์ ก.พ. 7 อิเล็กทรอนิกส์ โดย ".$Login_by." [ที่ rpt_R0040062]";  //test function
		insert_log("พิมพ์ ก.พ. 7 อิเล็กทรอนิกส์ > โดย ".$Login_by." [ที่ rpt_R0040062 จาก ".$RefFileName."]");
	}else{
		$pdf->SetFont($font,'b',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(200,7,"********** ไม่มีข้อมูล **********",0,1,'C');
	} // end if

	$pdf->close();
	$pdf->Output();
?>