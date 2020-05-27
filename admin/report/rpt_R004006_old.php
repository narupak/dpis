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
	
	$arr_history_name = explode("|", $HISTORY_LIST);
//	$arr_history_name = array("POSITIONHIS", "SALARYHIS", "EXTRA_INCOMEHIS", "EDUCATE", "TRAINING", "ABILITY", "HEIR", "ABSENTHIS", "PUNISHMENT", "TIMEHIS", "REWARDHIS", "MARRHIS", "NAMEHIS", "DECORATEHIS", "SERVICEHIS", "SPECIALSKILLHIS"); 
	$search_condition = "";
	$arr_search_condition[] = "(a.PER_TYPE in (". implode(", ", $search_per_type) ."))";
//	$arr_search_condition[] = "(a.PER_TYPE=1)";

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
		if(trim($search_org_id)) $arr_search_condition[] = "(c.ORG_ID = $search_org_id or d.ORG_ID = $search_org_id)";
		if(trim($search_org_id_1)) $arr_search_condition[] = "(c.ORG_ID_1 = $search_org_id_1 or d.ORG_ID_1 = $search_org_id_1)";
		if(trim($search_org_id_2)) $arr_search_condition[] = "(c.ORG_ID_2 = $search_org_id_2 or d.ORG_ID_2 = $search_org_id_2)";
//	}
		
	if($list_type == "SELECT"){
		if($SELECTED_PER_ID){		$arr_search_condition[] = "(a.PER_ID in ($SELECTED_PER_ID))";	}
	}elseif($list_type == "CONDITION"){
		//ทั้งข้าราชการ/ลูกจ้าง/พนง.ราชการ
		if(trim($search_pos_no)) $arr_search_condition[] = "(trim(c.POS_NO)='$search_pos_no' or trim(d.POEM_NO)='$search_pos_no'  or trim(e.POEMS_NO)='$search_pos_no') ";
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
	
	//กำหนดค่าประเภทบุคคล
	//$PERSON_TYPE จาก connect_database
	$show_persontype="";
	for($i=0; $i < count($search_per_type); $i++){
		$show_persontype .= $PERSON_TYPE[$search_per_type[$i]]."/";
	}
	$show_persontype = substr($show_persontype,0,-1);
	
	$report_title = "ประวัติ $show_persontype";
	$report_code = "R0406";
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

	$heading_width[POSITIONHIS][0] = "32";         
	$heading_width[POSITIONHIS][1] = "49";          
	$heading_width[POSITIONHIS][2] = "22";
	$heading_width[POSITIONHIS][3] = "35";      
	$heading_width[POSITIONHIS][4] = "17";
	$heading_width[POSITIONHIS][5] = "19";        
 	$heading_width[POSITIONHIS][6] = "26";

	$heading_width[SALARYHIS][0] = "25";
	$heading_width[SALARYHIS][1] = "25";
	$heading_width[SALARYHIS][2] = "150";

	$heading_width[EXTRA_INCOMEHIS][0] = "25";
	$heading_width[EXTRA_INCOMEHIS][1] = "25";
	$heading_width[EXTRA_INCOMEHIS][2] = "125";
	$heading_width[EXTRA_INCOMEHIS][3] = "25";

	$heading_width[EDUCATE][0] = "60";
	$heading_width[EDUCATE][1] = "50";
	$heading_width[EDUCATE][2] = "50";
	$heading_width[EDUCATE][3] = "40";

	$heading_width[TRAINING][0] = "35";
	$heading_width[TRAINING][1] = "88";
	$heading_width[TRAINING][2] = "40";
	$heading_width[TRAINING][3] = "37";

	$heading_width[ABILITY][0] = "100";
	$heading_width[ABILITY][1] = "100";

	$heading_width[HEIR][0] = "100";
	$heading_width[HEIR][1] = "100";

	$heading_width[ABSENTHIS][0] = "55";
	$heading_width[ABSENTHIS][1] = "20";
	$heading_width[ABSENTHIS][2] = "125";

	$heading_width[PUNISHMENT][0] = "70";
	$heading_width[PUNISHMENT][1] = "75";
	$heading_width[PUNISHMENT][2] = "55";

	$heading_width[TIMEHIS][0] = "150";
	$heading_width[TIMEHIS][1] = "25";
	$heading_width[TIMEHIS][2] = "25";

	$heading_width[REWARDHIS][0] = "60";
	$heading_width[REWARDHIS][1] = "140";

	$heading_width[MARRHIS][0] = "70";
	$heading_width[MARRHIS][1] = "50";
	$heading_width[MARRHIS][2] = "40";
	$heading_width[MARRHIS][3] = "40";

	$heading_width[NAMEHIS][0] = "60";
	$heading_width[NAMEHIS][1] = "140";

	$heading_width[DECORATEHIS][0] = "80";
	$heading_width[DECORATEHIS][1] = "60";
	$heading_width[DECORATEHIS][2] = "60";

	$heading_width[SERVICEHIS][0] = "30";
	$heading_width[SERVICEHIS][1] = "40";
	$heading_width[SERVICEHIS][2] = "50";
	$heading_width[SERVICEHIS][3] = "30";
	$heading_width[SERVICEHIS][4] = "30";
	$heading_width[SERVICEHIS][5] = "20";

	$heading_width[SPECIALSKILLHIS][0] = "80";
	$heading_width[SPECIALSKILLHIS][1] = "120";

	$heading_width[EXTRAHIS][0] = "25";
	$heading_width[EXTRAHIS][1] = "25";
	$heading_width[EXTRAHIS][2] = "125";
	$heading_width[EXTRAHIS][3] = "25";

	function print_header($HISTORY_NAME){
		global $pdf, $heading_width, $show_persontype, $report_title;
		
		$report_title = "ประวัติ $show_persontype";
		$pdf->SetFont($font,'',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
//		$pdf->Cell(100,2,"",0,1,'C');

		switch($HISTORY_NAME){
			case "POSITIONHIS" :
				$pdf->Cell($heading_width[$HISTORY_NAME][0] ,7,"กรม",'LTR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][1] ,7,"สำนัก/กอง",'LTR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][2] ,7,"วันที่",'LTR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][3] ,7,"ตำแหน่ง",'LTR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][4] ,7,"เลขที่",'LTR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][5] ,7,"อัตรา",'LTR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][6] ,7,"ประเภท",'LTR',1,'C',1);

				$pdf->Cell($heading_width[$HISTORY_NAME][0] ,7,"",'LBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][1] ,7,"",'LBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][2] ,7,"",'LBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][3] ,7,"",'LBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][4] ,7,"ตำแหน่ง",'LBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][5] ,7,"เงินเดือน",'LBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][6] ,7,"การเคลื่อนไหว",'LBR',1,'C',1);
				break;			
			case "SALARYHIS" :
				$pdf->Cell($heading_width[$HISTORY_NAME][0] ,7,"วันที่",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][1] ,7,"ขั้น",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][2] ,7,"ประเภทการเคลื่อนไหว",'LTBR',1,'C',1);
				break;			
			case "EXTRA_INCOMEHIS" :
				$pdf->Cell($heading_width[$HISTORY_NAME][0] ,7,"วันที่",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][1] ,7,"ถึงวันที่",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][2] ,7,"ประเภทเงินเพิ่มพิเศษ",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][3] ,7,"เงินเพิ่มพิเศษ",'LTBR',1,'C',1);
				break;			
			case "EDUCATE" :
				$pdf->Cell($heading_width[$HISTORY_NAME][0] ,7,"วุฒิการศึกษา",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][1] ,7,"สาขาวิชาเอก",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][2] ,7,"สถานศึกษา",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][3] ,7,"ประเทศ",'LTBR',1,'C',1);
				break;			
			case "TRAINING" :
				$pdf->Cell($heading_width[$HISTORY_NAME][0] ,7,"ช่วงระยะเวลา",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][1] ,7,"หลักสูตร",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][2] ,7,"หน่วยงานที่จัด",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][3] ,7,"สถานที่",'LTBR',1,'C',1);
				break;			
			case "ABILITY" :
				$pdf->Cell($heading_width[$HISTORY_NAME][0] ,7,"ด้านความสามารถพิเศษ",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][1] ,7,"ความสามารถพิเศษ",'LTBR',1,'C',1);
				break;			
			case "HEIR" :
				$pdf->Cell($heading_width[$HISTORY_NAME][0] ,7,"ความสัมพันธ์",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][1] ,7,"ชื่อ - สกุล",'LTBR',1,'C',1);
				break;			
			case "ABSENTHIS" :
				$pdf->Cell($heading_width[$HISTORY_NAME][0] ,7,"วันที่ลา",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][1] ,7,"จำนวนวัน",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][2] ,7,"ประเภทการลา",'LTBR',1,'C',1);
				break;			
			case "PUNISHMENT" :
				$pdf->Cell($heading_width[$HISTORY_NAME][0] ,7,"ฐานความผิด",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][1] ,7,"กรณีความผิด",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][2] ,7,"วันที่รับโทษ",'LTBR',1,'C',1);
				break;			
			case "TIMEHIS" :
				$pdf->Cell($heading_width[$HISTORY_NAME][0] ,7,"เวลาทวีคูณ",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][1] ,7,"จำนวนวัน",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][2] ,7,"จำนวนวันที่ไม่นับ",'LTBR',1,'C',1);
				break;			
			case "REWARDHIS" :
				$pdf->Cell($heading_width[$HISTORY_NAME][0] ,7,"วันที่ได้รับความดีความชอบ",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][1] ,7,"ความดีความชอบ",'LTBR',1,'C',1);
				break;			
			case "MARRHIS" :
				$pdf->Cell($heading_width[$HISTORY_NAME][0] ,7,"ชื่อคู่สมรส",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][1] ,7,"วันที่สมรส",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][2] ,7,"เหตุที่ขาดจากสมรส",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][3] ,7,"วันที่ขาดจากสมรส",'LTBR',1,'C',1);
				break;			
			case "NAMEHIS" :
				$pdf->Cell($heading_width[$HISTORY_NAME][0] ,7,"วันที่เปลี่ยนแปลง",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][1] ,7,"คำนำหน้า - ชื่อ - สกุล",'LTBR',1,'C',1);
				break;			
			case "DECORATEHIS" :
				$pdf->Cell($heading_width[$HISTORY_NAME][0] ,7,"ชื่อเครื่องราชฯ ที่ได้รับ",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][1] ,7,"วันที่ได้รับ",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][2] ,7,"ราชกิจจานุเบกษา",'LTBR',1,'C',1);
				break;			
			case "SERVICEHIS" :
				$pdf->Cell($heading_width[$HISTORY_NAME][0] ,7,"วันที่",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][1] ,7,"ราชการพิเศษ",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][2] ,7,"หัวข้อ / โครงการ",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][3] ,7,"สถานที่/หน่วยงาน",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][4] ,7,"เลขที่คำสั่ง",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][5] ,7,"หมายเหตุ",'LTBR',1,'C',1);
				break;			
			case "SPECIALSKILLHIS" :
				$pdf->Cell($heading_width[$HISTORY_NAME][0] ,7,"ด้านความเชี่ยวชาญพิเศษ",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][1] ,7,"เน้นทาง",'LTBR',1,'C',1);
				break;			
			case "EXTRAHIS" :
				$pdf->Cell($heading_width[$HISTORY_NAME][0] ,7,"วันที่",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][1] ,7,"ถึงวันที่",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][2] ,7,"ประเภทเงินพิเศษ",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][3] ,7,"เงินพิเศษ",'LTBR',1,'C',1);
				break;			
		} // end switch case
	} // function		

	if($DPISDB=="odbc"){
		$cmd = " select			a.PER_ID, a.LEVEL_NO, f.LEVEL_NAME, b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_BIRTHDATE, 
								a.PER_SALARY, a.PER_MGTSALARY, a.PER_STARTDATE, a.PER_RETIREDATE, a.PER_TYPE, a.PER_CARDNO,
								a.POS_ID, a.POEM_ID, a.POEMS_ID, a.PER_CERT_OCC,
								c.POS_NO, c.PL_CODE, c.PT_CODE, c.ORG_ID, c.ORG_ID_1, c.ORG_ID_2,
								d.POEM_NO as EMP_POS_NO, d.PN_CODE as EMP_PL_CODE, 
								d.ORG_ID as EMP_ORG_ID, d.ORG_ID_1 as EMP_ORG_ID_1, d.ORG_ID_2 as EMP_ORG_ID_2,
								e.POEMS_NO as EMPSER_POS_NO, e.EP_CODE as EMPSER_PL_CODE, 
								e.ORG_ID as EMPSER_ORG_ID, e.ORG_ID_1 as EMPSER_ORG_ID_1, e.ORG_ID_2 as EMPSER_ORG_ID_2
				 from			PER_PRENAME b inner join 
				 				(
									(
										( 	
											(
										PER_PERSONAL a 
										left join PER_POSITION c on (a.POS_ID=c.POS_ID) 
									) left join PER_POS_EMP d on (a.POEM_ID=d.POEM_ID)
								) left join PER_POS_EMPSER e on (a.POEMS_ID=e.POEMS_ID)
							) left join PER_LEVEL f on (a.LEVEL_NO=f.LEVEL_NO)
						) on (trim(a.PN_CODE)=trim(b.PN_CODE))
								$search_condition
				 order by			a.PER_NAME, a.PER_SURNAME ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select			a.PER_ID, a.LEVEL_NO, f.LEVEL_NAME, b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_BIRTHDATE, 
											a.PER_SALARY, a.PER_MGTSALARY, a.PER_STARTDATE, a.PER_RETIREDATE, a.PER_TYPE, a.PER_CARDNO,
											a.POS_ID, a.POEM_ID, a.POEMS_ID, a.PER_CERT_OCC,
											c.POS_NO, c.PL_CODE, c.PT_CODE, c.ORG_ID, c.ORG_ID_1, c.ORG_ID_2,
											d.POEM_NO as EMP_POS_NO, d.PN_CODE as EMP_PL_CODE, 
											d.ORG_ID as EMP_ORG_ID, d.ORG_ID_1 as EMP_ORG_ID_1, d.ORG_ID_2 as EMP_ORG_ID_2,
											e.POEMS_NO as EMPSER_POS_NO, e.EP_CODE as EMPSER_PL_CODE, 
											e.ORG_ID as EMPSER_ORG_ID, e.ORG_ID_1 as EMPSER_ORG_ID_1, e.ORG_ID_2 as EMPSER_ORG_ID_2
						 from			PER_PERSONAL a, PER_PRENAME b, PER_POSITION c, PER_POS_EMP d, PER_POS_EMPSER e, PER_LEVEL f
						 where		trim(a.PN_CODE)=trim(b.PN_CODE) and 
											a.POS_ID=c.POS_ID(+) and a.POEM_ID=d.POEM_ID(+) and a.POEMS_ID=e.POEMS_ID(+) and a.LEVEL_NO=f.LEVEL_NO(+)
											$search_condition
						 order by		a.PER_NAME, a.PER_SURNAME ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			a.PER_ID, a.LEVEL_NO, f.LEVEL_NAME, b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_BIRTHDATE, 
								a.PER_SALARY, a.PER_MGTSALARY, a.PER_STARTDATE, a.PER_RETIREDATE, a.PER_TYPE, a.PER_CARDNO,
								a.POS_ID, a.POEM_ID, a.POEMS_ID, a.PER_CERT_OCC,
								c.POS_NO, c.PL_CODE, c.PT_CODE, c.ORG_ID, c.ORG_ID_1, c.ORG_ID_2,
								d.POEM_NO as EMP_POS_NO, d.PN_CODE as EMP_PL_CODE, 
								d.ORG_ID as EMP_ORG_ID, d.ORG_ID_1 as EMP_ORG_ID_1, d.ORG_ID_2 as EMP_ORG_ID_2,
								e.POEMS_NO as EMPSER_POS_NO, e.EP_CODE as EMPSER_PL_CODE, 
								e.ORG_ID as EMPSER_ORG_ID, e.ORG_ID_1 as EMPSER_ORG_ID_1, e.ORG_ID_2 as EMPSER_ORG_ID_2
				 from			PER_PRENAME b inner join 
				 				(
									(
										( 	
											(
										PER_PERSONAL a 
										left join PER_POSITION c on (a.POS_ID=c.POS_ID) 
									) left join PER_POS_EMP d on (a.POEM_ID=d.POEM_ID)
								) left join PER_POS_EMPSER e on (a.POEMS_ID=e.POEMS_ID)
							) left join PER_LEVEL f on (a.LEVEL_NO=f.LEVEL_NO)
						) on (trim(a.PN_CODE)=trim(b.PN_CODE))
								$search_condition
				 order by			a.PER_NAME, a.PER_SURNAME ";
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	echo "$cmd<br>";
//	$db_dpis->show_error();

	if($count_data){
		$data_count = 0;
		while($data = $db_dpis->get_array()){
			$data_count++;
			$PER_ID = $data[PER_ID];
			$PER_TYPE = $data[PER_TYPE];
			
			if($PER_TYPE==1){
				$show_persontype = "ข้าราชการ";
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
				$show_persontype = "ลูกจ้างประจำ";
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
				$show_persontype = "พนักงานราชการ";
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
			} 

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

			$LEVEL_NO = trim($data[LEVEL_NO]);
			$LEVEL_NAME = trim(str_replace("ระดับ","",$data[LEVEL_NAME]));
			$PN_NAME = trim($data[PN_NAME]);
			$PER_NAME = trim($data[PER_NAME]);
			$PER_SURNAME = trim($data[PER_SURNAME]);
			$FULLNAME = ($PN_NAME)."$PER_NAME $PER_SURNAME";
			$PER_CARDNO = $data[PER_CARDNO];			
			$PER_CERT_OCC = trim($data[PER_CERT_OCC]);
			$img_file = "";
			if($PER_CARDNO && file_exists($IMG_PATH.$PER_CARDNO.".jpg")) $img_file = $IMG_PATH.$PER_CARDNO.".jpg";			
/*
			$PER_RETIREDATE = trim($data[PER_RETIREDATE]);
			if($PER_RETIREDATE){
				$arr_temp = explode("-", substr($PER_RETIREDATE, 0, 10));
				$PER_RETIREDATE = ($arr_temp[2] + 0) ." ". $month_full[($arr_temp[1] + 0)][TH] ." ". ($arr_temp[0] + 543);
			} // end if
*/
			$PER_BIRTHDATE = trim($data[PER_BIRTHDATE]);
			$PER_RETIREDATE = "";
			if($PER_BIRTHDATE){
				$arr_temp = explode("-", substr($PER_BIRTHDATE, 0, 10));
				$PER_RETIREDATE = "1 ตุลาคม ".($arr_temp[0] + 543 + 60 + ((substr($PER_BIRTHDATE, 5, 5) >= "10-01")?1:0));
				$PER_BIRTHDATE = ($arr_temp[2] + 0) ." ". $month_full[($arr_temp[1] + 0)][TH] ." ". ($arr_temp[0] + 543);
			} // end if
			
			$PER_SALARY = $data[PER_SALARY];
			$PER_MGTSALARY = $data[PER_MGTSALARY];

			$PER_STARTDATE = trim($data[PER_STARTDATE]);
			if($PER_STARTDATE){
				$arr_temp = explode("-", substr($PER_STARTDATE, 0, 10));
				$PER_STARTDATE = ($arr_temp[2] + 0) ." ". $month_full[($arr_temp[1] + 0)][TH] ." ". ($arr_temp[0] + 543);
			} // end if

			if($DPISDB=="odbc"){
				$cmd = " select		b.EN_NAME, c.EM_NAME, d.INS_NAME
						 from		((PER_EDUCATE a 
									left join PER_EDUCNAME b on (trim(a.EN_CODE)=trim(b.EN_CODE))
									) left join PER_EDUCMAJOR c on (trim(a.EM_CODE)=trim(c.EM_CODE))
									) left join PER_INSTITUTE d on (trim(a.INS_CODE)=trim(d.INS_CODE))
						 where		a.PER_ID=$PER_ID and a.EDU_TYPE like '%||2||%'
						 order by		a.EDU_SEQ desc, a.EDU_ENDYEAR desc ";							   
			}elseif($DPISDB=="oci8"){
				$cmd = " select		b.EN_NAME, c.EM_NAME, d.INS_NAME
						 from		PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCMAJOR c, PER_INSTITUTE d
						 where		a.PER_ID=$PER_ID and a.EDU_TYPE like '%||2||%' and
									a.EN_CODE=b.EN_CODE(+) and 
									a.EM_CODE=c.EM_CODE(+) and
									a.INS_CODE=d.INS_CODE(+)
						 order by		a.EDU_SEQ desc, a.EDU_ENDYEAR desc ";							   
			}elseif($DPISDB=="mysql"){
				$cmd = " select		b.EN_NAME, c.EM_NAME, d.INS_NAME
						 from		((PER_EDUCATE a 
									left join PER_EDUCNAME b on (trim(a.EN_CODE)=trim(b.EN_CODE))
									) left join PER_EDUCMAJOR c on (trim(a.EM_CODE)=trim(c.EM_CODE))
									) left join PER_INSTITUTE d on (trim(a.INS_CODE)=trim(d.INS_CODE))
						 where		a.PER_ID=$PER_ID and a.EDU_TYPE like '%||2||%'
						 order by		a.EDU_SEQ desc, a.EDU_ENDYEAR desc ";	
			} // end if
			$db_dpis2->send_cmd($cmd);
			//$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$EN_NAME = trim($data2[EN_NAME]);
			$EM_NAME = trim($data2[EM_NAME]);
			$INS_NAME = trim($data2[INS_NAME]);
			
			$pdf->SetFont($font,'',14);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
			$pdf->Cell(200,7,"เลขที่ตำแหน่ง  ".($POS_NO?$POS_NO:"-"),0,1,"L");

			if($img_file){
				$image_x = ($pdf->x + 170);		$image_y = ($pdf->y - 7);		$image_w = 30;			$image_h = 40;
				$pdf->Image($img_file, $image_x, $image_y, $image_w, $image_h);
			} // end if

			$pdf->Cell(95,7,$pdf->Text_print_optimize("ตำแหน่ง  ".(trim($PL_NAME)?($PL_NAME ." ". level_no_format($LEVEL_NAME) . (($PT_CODE != "11" && $LEVEL_NO >= 6)?"$PT_NAME":"")):" ".level_no_format($LEVEL_NAME)), 100),0,0,"L");
			$pdf->Cell(85,7,$pdf->Text_print_optimize("  สังกัด  ".($ORG_NAME?$ORG_NAME:"-"), 100),0,1,"L");
			
			if($ORG_ID_1){
				$pdf->Cell(85,7,"",0,0,"L");
				$pdf->Cell(85,7,$pdf->Text_print_optimize($ORG_NAME_1, 100),0,1,"L");
			} // end if

			if($ORG_ID_2){
				$pdf->Cell(85,7,"",0,0,"L");
				$pdf->Cell(85,7,$pdf->Text_print_optimize($ORG_NAME_2, 100),0,1,"L");
			} // end if

			$pdf->Cell(100,7,"ชื่อ  $FULLNAME ",0,0,"L");
			$pdf->Cell(100,7,"เลขประจำตัวประชาชน   $PER_CARDNO",0,1,"L");

			$pdf->Cell(100,7,"วันเกิด  ".($PER_BIRTHDATE?$PER_BIRTHDATE:"-"),0,0,"L");
			$pdf->Cell(100,7,"เลขที่ใบประกอบวิชาชีพ   $PER_CERT_OCC",0,1,"L");

			$pdf->Cell(100,7,"เงินเดือน  ".($PER_SALARY?number_format($PER_SALARY, ","):"-"),0,0,"L");
			$pdf->Cell(100,7,"เงินประจำตำแหน่ง  ".($PER_MGTSALARY?number_format($PER_MGTSALARY, ","):"-"),0,1,"L");

			$pdf->Cell(100,7,$pdf->Text_print_optimize("วุฒิ  ".($EN_NAME?$EN_NAME:"-"), 100),0,0,"L");			
			$pdf->Cell(100,7,$pdf->Text_print_optimize("สาขาวิชาเอก  ".($EM_NAME?$EM_NAME:"-"), 100),0,1,"L");

			$pdf->Cell(200,7,"สถานศึกษา  ".($INS_NAME?$INS_NAME:"-"),0,1,"L");

			$pdf->Cell(100,7,"วันเข้ารับราชการ  ".($PER_STARTDATE?$PER_STARTDATE:"-"),0,0,"L");
			$pdf->Cell(100,7,"วันเกษียณอายุราชการ  ".($PER_RETIREDATE?$PER_RETIREDATE:"-"),0,1,"L");
			
			$pdf->AutoPageBreak = false;

			for($history_index=0; $history_index<count($arr_history_name); $history_index++){
				$HISTORY_NAME = $arr_history_name[$history_index];
				switch($HISTORY_NAME){
					case "POSITIONHIS" :
							if(($pdf->h - $pdf->y - 10) < 30) $pdf->AddPage();
							$pdf->SetFont($fontb,'',14);
							$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
							if($pdf->y != $page_start_y) $pdf->Cell(200,7,"",0,1,"L");
							$pdf->Cell(200,7,($history_index + 1)."  ประวัติการรับราชการ ",0,1,"L");
							print_header($HISTORY_NAME);
							
							$ORG_NAME_1 = "";
							$ORG_NAME_2 = "";
							$POH_EFFECTIVEDATE = "";
							$PL_NAME = "";
							$LEVEL_NO = "";
							$POH_POS_NO = "";
							$POH_SALARY = "";
/*				
							if($DPISDB=="odbc"){
								$cmd = " select			b.ORG_NAME as ORG_NAME_1, c.ORG_NAME as ORG_NAME_2, a.POH_EFFECTIVEDATE, d.PL_NAME, 
														a.LEVEL_NO, a.POH_POS_NO, a.POH_SALARY, a.PT_CODE, e.PT_NAME, a.PM_CODE, f.PM_NAME
										   from			(
															(
																(
																	( 
																		PER_POSITIONHIS a
																		inner join PER_ORG b on (a.ORG_ID_2=b.ORG_ID)
																	) inner join PER_ORG c on (a.ORG_ID_3=c.ORG_ID)
																) left join PER_LINE d on (a.PL_CODE=d.PL_CODE)
															) left join PER_TYPE e on (a.PT_CODE=e.PT_CODE)
														) left join PER_MGT f on (a.PM_CODE=f.PM_CODE)
										   where		a.PER_ID=$PER_ID
										   order by		a.POH_EFFECTIVEDATE
									   	";							   
							}elseif($DPISDB=="oci8"){
								$cmd = " select			b.ORG_NAME ORG_NAME_1, c.ORG_NAME ORG_NAME_2, a.POH_EFFECTIVEDATE, d.PL_NAME, 
														a.LEVEL_NO, a.POH_POS_NO, a.POH_SALARY, a.PT_CODE, e.PT_NAME, a.PM_CODE, f.PM_NAME
										   from			PER_POSITIONHIS a, PER_ORG b, PER_ORG c, PER_LINE d, PER_TYPE e, PER_MGT f
										   where		a.PER_ID=$PER_ID and
														a.ORG_ID_2=b.ORG_ID and 
														a.ORG_ID_3=c.ORG_ID and 
														a.PL_CODE=d.PL_CODE(+) and
														a.PT_CODE=e.PT_CODE(+) and
														a.PM_CODE=f.PM_CODE(+)
										   order by		a.POH_EFFECTIVEDATE
									   	";							   
							}elseif($DPISDB=="mysql"){
							} // end if
*/
							if($DPISDB=="odbc"){
								$cmd = " select			a.POH_ORG2 as ORG_NAME_1, a.POH_ORG3 as ORG_NAME_2, a.POH_EFFECTIVEDATE, d.PL_NAME, 
																	a.LEVEL_NO, g.LEVEL_NAME, a.POH_POS_NO, a.POH_SALARY, a.PT_CODE, e.PT_NAME, a.PM_CODE, f.PM_NAME, h.MOV_NAME
												   from			(
																		(
																			(
																				(
																			PER_POSITIONHIS a
																			left join PER_LINE d on (a.PL_CODE=d.PL_CODE)
																		) left join PER_TYPE e on (a.PT_CODE=e.PT_CODE)
																	) left join PER_MGT f on (a.PM_CODE=f.PM_CODE)
																) left join PER_LEVEL g on (a.LEVEL_NO=g.LEVEL_NO)
												  			)  left join PER_MOVMENT h on (a.MOV_CODE=h.MOV_CODE)
												   where		a.PER_ID=$PER_ID
												   order by	LEFT(a.POH_EFFECTIVEDATE,10) desc, LEFT(a.POH_ENDDATE,10) desc ";							   
							}elseif($DPISDB=="oci8"){
								$cmd = " select			a.POH_ORG2 ORG_NAME_1, a.POH_ORG3 ORG_NAME_2, a.POH_EFFECTIVEDATE, d.PL_NAME, 
																	a.LEVEL_NO, g.LEVEL_NAME, a.POH_POS_NO, a.POH_SALARY, a.PT_CODE, e.PT_NAME, a.PM_CODE, f.PM_NAME, h.MOV_NAME
												   from			PER_POSITIONHIS a, PER_LINE d, PER_TYPE e, PER_MGT f, PER_LEVEL g, PER_MOVMENT h
												   where		a.PER_ID=$PER_ID and
																	a.PL_CODE=d.PL_CODE(+) and
																	a.PT_CODE=e.PT_CODE(+) and
																	a.PM_CODE=f.PM_CODE(+) and 
																	a.LEVEL_NO=g.LEVEL_NO(+) and 
																	a.MOV_CODE=h.MOV_CODE(+)
												   order by	SUBSTR(a.POH_EFFECTIVEDATE,1,10)  desc, SUBSTR(a.POH_ENDDATE,1,10) desc ";							   
							}elseif($DPISDB=="mysql"){
								$cmd = " select			a.POH_ORG2 as ORG_NAME_1, a.POH_ORG3 as ORG_NAME_2, a.POH_EFFECTIVEDATE, d.PL_NAME, 
																	a.LEVEL_NO, g.LEVEL_NAME, a.POH_POS_NO, a.POH_SALARY, a.PT_CODE, e.PT_NAME, a.PM_CODE, f.PM_NAME, h.MOV_NAME
												   from			(
																		(
																			(
																				(
																			PER_POSITIONHIS a
																			left join PER_LINE d on (a.PL_CODE=d.PL_CODE)
																		) left join PER_TYPE e on (a.PT_CODE=e.PT_CODE)
																	) left join PER_MGT f on (a.PM_CODE=f.PM_CODE)
																) left join PER_LEVEL g on (a.LEVEL_NO=g.LEVEL_NO)
												 			)  left join PER_MOVMENT h on (a.MOV_CODE=h.MOV_CODE)
												  where		a.PER_ID=$PER_ID
												 order by	LEFT(a.POH_EFFECTIVEDATE,10) desc, LEFT(a.POH_ENDDATE,10) desc ";		
							} // end if
							$count_positionhis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							if($count_positionhis){
								$positionhis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$positionhis_count++;
									$POH_EFFECTIVEDATE = display_date($data2[POH_EFFECTIVEDATE]);
				
									$border = "";
									$pdf->SetFont($font,'',14);
									$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
									
									$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
								
									if($ORG_NAME_1 != trim($data2[ORG_NAME_1])){
										$ORG_NAME_1 = trim($data2[ORG_NAME_1]);
										$pdf->MultiCell($heading_width[$HISTORY_NAME][0], 7, "$ORG_NAME_1", $border, "L");
									}else{
										$pdf->MultiCell($heading_width[$HISTORY_NAME][0], 7, "", $border, "L");
									} // end if
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0];
									$pdf->y = $start_y;
				
									if($ORG_NAME_2 != trim($data2[ORG_NAME_2])){
										$ORG_NAME_2 = trim($data2[ORG_NAME_2]);
										$pdf->MultiCell($heading_width[$HISTORY_NAME][1], 7, "$ORG_NAME_2", $border, "L");
									}else{
										$pdf->MultiCell($heading_width[$HISTORY_NAME][1], 7, "", $border, "L");
									} // end if
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1];
									$pdf->y = $start_y;
									
									$pdf->Cell($heading_width[$HISTORY_NAME][2] ,7,"$POH_EFFECTIVEDATE",$border,0,"C");
				
									if( ("$PM_CODE $PL_NAME $LEVEL_NO $POH_POS_NO") != (trim($data2[PM_CODE])." ".trim($data2[PL_NAME])." ".trim($data2[LEVEL_NO])." ".trim($data2[POH_POS_NO])) ){
										$PL_NAME = trim($data2[PL_NAME]);
										$LEVEL_NO = trim($data2[LEVEL_NO]);
										$LEVEL_NAME = trim(str_replace("ระดับ","",$data2[LEVEL_NAME]));
										$PT_CODE = trim($data2[PT_CODE]);
										$PT_NAME = trim($data2[PT_NAME]);
										$PM_CODE = trim($data2[PM_CODE]);
										$PM_NAME = trim($data2[PM_NAME]);
										$TMP_PL_NAME = (trim($PL_NAME)?($PL_NAME ." ". level_no_format($LEVEL_NAME) . (($PT_CODE != "11" && $LEVEL_NO >= 6)?"$PT_NAME":"")):" ".level_no_format($LEVEL_NAME));
										if($PM_CODE && $PM_CODE!="9999") $TMP_PL_NAME .= $PM_NAME."\n($TMP_PL_NAME)";
										$pdf->MultiCell($heading_width[$HISTORY_NAME][3], 7, $TMP_PL_NAME, $border, "L");
									}else{
										$pdf->MultiCell($heading_width[$HISTORY_NAME][3], 7, "", $border, "L");
									} // end if
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1] + $heading_width[$HISTORY_NAME][2] + $heading_width[$HISTORY_NAME][3];
									$pdf->y = $start_y;
				
									$POH_POS_NO = trim($data2[POH_POS_NO]);
									$POH_SALARY = $data2[POH_SALARY];
									$MOV_NAME = trim($data2[MOV_NAME]);
				
									$pdf->Cell($heading_width[$HISTORY_NAME][4] ,7,($POH_POS_NO?$POH_POS_NO:"-"),$border,0,"C");
									$pdf->Cell($heading_width[$HISTORY_NAME][5] ,7,number_format($POH_SALARY, ","),$border,0,'R');
									$pdf->MultiCell($heading_width[$HISTORY_NAME][6], 7, ($MOV_NAME?$MOV_NAME:"-"), $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1] + $heading_width[$HISTORY_NAME][2] + $heading_width[$HISTORY_NAME][3] + 
									$heading_width[$HISTORY_NAME][4] + $heading_width[$HISTORY_NAME][5] + $heading_width[$HISTORY_NAME][6];
									$pdf->y = $start_y;			
										
									//================= Draw Border Line ====================
									$line_start_y = $start_y;		$line_start_x = $start_x;
									$line_end_y = $max_y;		$line_end_x = $start_x;
									$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				
									for($i=0; $i<=6; $i++){
										$line_start_y = $start_y;		$line_start_x += $heading_width[$HISTORY_NAME][$i];
										$line_end_y = $max_y;		$line_end_x += $heading_width[$HISTORY_NAME][$i];
										$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
									} // end for
									//====================================================
				
									if(($pdf->h - $max_y - 10) < 15){ 
										$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
										if($positionhis_count < $count_positionhis){
											$pdf->AddPage();
											print_header($HISTORY_NAME);
											$max_y = $pdf->y;
										} // end if
									}else{
										if($positionhis_count == $count_positionhis) $pdf->Line($start_x, $max_y, $pdf->x, $max_y);
									} // end if
									$pdf->x = $start_x;			$pdf->y = $max_y;
								} // end while
							}else{
								$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
								$pdf->Cell(200,7,"********** ไม่มีข้อมูล **********","LBR",1,'C');
							} // end if
						break;
					case "SALARYHIS" :
							if(($pdf->h - $pdf->y - 10) < 30) $pdf->AddPage();
							$pdf->SetFont($fontb,'',14);
							$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
							if($pdf->y != $page_start_y) $pdf->Cell(200,7,"",0,1,"L");
							$pdf->Cell(200,7,($history_index + 1)."  ประวัติการเลื่อนขั้นเงินเดือน ",0,1,"L");
							print_header($HISTORY_NAME);
							
							$SAH_EFFECTIVEDATE = "";
							$SAH_SALARY = "";
							$MOV_NAME = "";

							if($DPISDB=="odbc"){
								$cmd = " select			a.SAH_EFFECTIVEDATE, a.SAH_SALARY, b.MOV_NAME
												 from			PER_SALARYHIS a, PER_MOVMENT b
												 where		a.PER_ID=$PER_ID and a.MOV_CODE=b.MOV_CODE 
												 order by		LEFT(a.SAH_EFFECTIVEDATE,10) desc, a.SAH_SALARY desc ";							   
							}elseif($DPISDB=="oci8"){
								$cmd = " select			a.SAH_EFFECTIVEDATE, a.SAH_SALARY, b.MOV_NAME
												 from			PER_SALARYHIS a, PER_MOVMENT b
												 where		a.PER_ID=$PER_ID and a.MOV_CODE=b.MOV_CODE 
												 order by		SUBSTR(a.SAH_EFFECTIVEDATE,1,10) desc, a.SAH_SALARY desc ";							   
							}elseif($DPISDB=="mysql"){
								$cmd = " select			a.SAH_EFFECTIVEDATE, a.SAH_SALARY, b.MOV_NAME
												 from			PER_SALARYHIS a, PER_MOVMENT b
												 where		a.PER_ID=$PER_ID and a.MOV_CODE=b.MOV_CODE 
												 order by		LEFT(a.SAH_EFFECTIVEDATE,10) desc, a.SAH_SALARY desc ";		
							} // end if
							$count_salaryhis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							if($count_salaryhis){
								$salaryhis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$salaryhis_count++;
									$SAH_EFFECTIVEDATE = display_date($data2[SAH_EFFECTIVEDATE]);
									$SAH_SALARY = $data2[SAH_SALARY];
									$MOV_NAME = trim($data2[MOV_NAME]);
				
									$border = "";
									$pdf->SetFont($font,'',14);
									$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
									
									$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
								
									$pdf->Cell($heading_width[$HISTORY_NAME][0] ,7,"$SAH_EFFECTIVEDATE",$border,0,"L");
									$pdf->Cell($heading_width[$HISTORY_NAME][1] ,7,number_format($SAH_SALARY, ","),$border,0,'R');
									$pdf->MultiCell($heading_width[$HISTORY_NAME][2], 7, "$MOV_NAME", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1] + $heading_width[$HISTORY_NAME][2];

									//================= Draw Border Line ====================
									$line_start_y = $start_y;		$line_start_x = $start_x;
									$line_end_y = $max_y;		$line_end_x = $start_x;
									$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				
									for($i=0; $i<=2; $i++){
										$line_start_y = $start_y;		$line_start_x += $heading_width[$HISTORY_NAME][$i];
										$line_end_y = $max_y;		$line_end_x += $heading_width[$HISTORY_NAME][$i];
										$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
									} // end for
									//====================================================
				
									if(($pdf->h - $max_y - 10) < 10){ 
										$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
										if($salaryhis_count < $count_salaryhis){
											$pdf->AddPage();
											print_header($HISTORY_NAME);
											$max_y = $pdf->y;
										} // end if
									}else{
										if($salaryhis_count == $count_salaryhis) $pdf->Line($start_x, $max_y, $pdf->x, $max_y);
									} // end if
									$pdf->x = $start_x;			$pdf->y = $max_y;
								} // end while
							}else{
								$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
								$pdf->Cell(200,7,"********** ไม่มีข้อมูล **********","LBR",1,'C');
							} // end if
						break;
					case "EXTRA_INCOMEHIS" :
							if(($pdf->h - $pdf->y - 10) < 30) $pdf->AddPage();
							$pdf->SetFont($fontb,'',14);
							$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
							if($pdf->y != $page_start_y) $pdf->Cell(200,7,"",0,1,"L");
							$pdf->Cell(200,7,($history_index + 1)."  ประวัติการรับเงินเพิ่มพิเศษ ",0,1,"L");
							print_header($HISTORY_NAME);

							$EXINH_EFFECTIVEDATE = "";
							$EXINH_ENDDATE = "";
							$EXIN_NAME = "";
							$EXINH_AMT = "";
							
							if($DPISDB=="odbc"){
								$cmd = " select			a.EXINH_EFFECTIVEDATE, a.EXINH_ENDDATE, b.EXIN_NAME, a.EXINH_AMT
												 from			PER_EXTRA_INCOMEHIS a, PER_EXTRA_INCOME_TYPE b
												 where		a.PER_ID=$PER_ID and a.EXIN_CODE=b.EXIN_CODE 
												 order by		a.EXINH_EFFECTIVEDATE ";							   
							}elseif($DPISDB=="oci8"){
								$cmd = " select			a.EXINH_EFFECTIVEDATE, a.EXINH_ENDDATE, b.EXIN_NAME, a.EXINH_AMT
												 from			PER_EXTRA_INCOMEHIS a, PER_EXTRA_INCOME_TYPE b
												 where		a.PER_ID=$PER_ID and a.EXIN_CODE=b.EXIN_CODE 
												 order by		a.EXINH_EFFECTIVEDATE ";							   
							}elseif($DPISDB=="mysql"){
								$cmd = " select			a.EXINH_EFFECTIVEDATE, a.EXINH_ENDDATE, b.EXIN_NAME, a.EXINH_AMT
												 from			PER_EXTRA_INCOMEHIS a, PER_EXTRA_INCOME_TYPE b
												 where		a.PER_ID=$PER_ID and a.EXIN_CODE=b.EXIN_CODE 
												 order by		a.EXINH_EFFECTIVEDATE ";		
							} // end if
							$count_exincomehis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							if($count_exincomehis){
								$exincomehis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$exincomehis_count++;
									$EXINH_EFFECTIVEDATE = display_date($data2[EXINH_EFFECTIVEDATE]);
									$EXINH_ENDDATE = display_date($data2[EXINH_ENDDATE]);
									$EXIN_NAME = trim($data2[EXIN_NAME]);
									$EXINH_AMT = $data2[EXINH_AMT];
				
									$border = "";
									$pdf->SetFont($font,'',14);
									$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
									
									$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
								
									$pdf->Cell($heading_width[$HISTORY_NAME][0] ,7,"$EXINH_EFFECTIVEDATE",$border,0,"L");
									$pdf->Cell($heading_width[$HISTORY_NAME][1] ,7,"$EXINH_ENDDATE",$border,0,"L");

									$pdf->MultiCell($heading_width[$HISTORY_NAME][2], 7, "$EXIN_NAME", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1] + $heading_width[$HISTORY_NAME][2];
									$pdf->y = $start_y;

									$pdf->Cell($heading_width[$HISTORY_NAME][3] ,7,number_format($EXINH_AMT, ","),$border,0,'R');

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
										if($exincomehis_count < $count_exincomehis){
											$pdf->AddPage();
											print_header($HISTORY_NAME);
											$max_y = $pdf->y;
										} // end if
									}else{
										if($exincomehis_count == $count_exincomehis) $pdf->Line($start_x, $max_y, $pdf->x, $max_y);
									} // end if
									$pdf->x = $start_x;			$pdf->y = $max_y;
								} // end while
							}else{
								$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
								$pdf->Cell(200,7,"********** ไม่มีข้อมูล **********","LBR",1,'C');
							} // end if
						break;
					case "EDUCATE" :
							if(($pdf->h - $pdf->y - 10) < 30) $pdf->AddPage();
							$pdf->SetFont($fontb,'',14);
							$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
							if($pdf->y != $page_start_y) $pdf->Cell(200,7,"",0,1,"L");
							$pdf->Cell(200,7,($history_index + 1)."  ประวัติการศึกษา ",0,1,"L");
							print_header($HISTORY_NAME);
							
							$EN_NAME = "";
							$EM_NAME = "";
							$INS_NAME = "";
							$CT_NAME = "";

							if($DPISDB=="odbc"){
								$cmd = " 	select		b.EN_NAME, c.EM_NAME, d.INS_NAME, e.CT_NAME
									 	from			(((PER_EDUCATE a
													left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
													) left join PER_EDUCMAJOR c on (a.EM_CODE=c.EM_CODE)
													) left join PER_INSTITUTE d on (a.INS_CODE=d.INS_CODE)
													) left join PER_COUNTRY e on (a.CT_CODE_EDU=e.CT_CODE)
										 where		a.PER_ID=$PER_ID
										 order by		a.EDU_SEQ ";							   
							}elseif($DPISDB=="oci8"){
								$cmd = " select		b.EN_NAME, c.EM_NAME, d.INS_NAME, e.CT_NAME
										from			PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCMAJOR c, PER_INSTITUTE d, PER_COUNTRY e
										where		a.PER_ID=$PER_ID and
													a.EN_CODE=b.EN_CODE(+) and 
													a.EM_CODE=c.EM_CODE(+) and 
													a.INS_CODE=d.INS_CODE(+) and 
													a.CT_CODE_EDU=e.CT_CODE(+)
										order by		a.EDU_SEQ ";							   
							}elseif($DPISDB=="mysql"){
								$cmd = " 	select		b.EN_NAME, c.EM_NAME, d.INS_NAME, e.CT_NAME
									 	from			(((PER_EDUCATE a
													left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
													) left join PER_EDUCMAJOR c on (a.EM_CODE=c.EM_CODE)
													) left join PER_INSTITUTE d on (a.INS_CODE=d.INS_CODE)
													) left join PER_COUNTRY e on (a.CT_CODE_EDU=e.CT_CODE)
										 where		a.PER_ID=$PER_ID
										 order by		a.EDU_SEQ ";			
							} // end if
							$count_educatehis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							if($count_educatehis){
								$educatehis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$educatehis_count++;
									$EN_NAME = trim($data2[EN_NAME]);
									$EM_NAME = trim($data2[EM_NAME]);
									$INS_NAME = trim($data2[INS_NAME]);
									$CT_NAME = trim($data2[CT_NAME]);
				
									$border = "";
									$pdf->SetFont($font,'',14);
									$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
									
									$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
								
									$pdf->MultiCell($heading_width[$HISTORY_NAME][0], 7, "$EN_NAME", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0];
									$pdf->y = $start_y;

									$pdf->MultiCell($heading_width[$HISTORY_NAME][1], 7, "$EM_NAME", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1];
									$pdf->y = $start_y;

									$pdf->MultiCell($heading_width[$HISTORY_NAME][2], 7, "$INS_NAME", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1] + $heading_width[$HISTORY_NAME][2];
									$pdf->y = $start_y;

									$pdf->MultiCell($heading_width[$HISTORY_NAME][3], 7, "$CT_NAME", $border, "L");
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
										if($educatehis_count < $count_educatehis){
											$pdf->AddPage();
											print_header($HISTORY_NAME);
											$max_y = $pdf->y;
										} // end if
									}else{
										if($educatehis_count == $count_educatehis) $pdf->Line($start_x, $max_y, $pdf->x, $max_y);
									} // end if
									$pdf->x = $start_x;			$pdf->y = $max_y;
								} // end while
							}else{
								$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
								$pdf->Cell(200,7,"********** ไม่มีข้อมูล **********","LBR",1,'C');
							} // end if
						break;
					case "TRAINING" :
							if(($pdf->h - $pdf->y - 10) < 30) $pdf->AddPage();
							$pdf->SetFont($fontb,'',14);
							$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
							if($pdf->y != $page_start_y) $pdf->Cell(200,7,"",0,1,"L");
							$pdf->Cell(200,7,($history_index + 1)."  ประวัติการฝึกอบรม สัมมนา และดูงาน ",0,1,"L");
							print_header($HISTORY_NAME);

							$TRN_STARTDATE = "";
							$TRN_ENDDATE = "";
							$TR_NAME = "";
							$TRN_PLACE = "";
							
							if($DPISDB=="odbc"){
								$cmd = " select			a.TRN_STARTDATE, a.TRN_ENDDATE, b.TR_NAME, a.TRN_PLACE, a.TRN_ORG
												 from			PER_TRAINING a, PER_TRAIN b
												 where		a.PER_ID=$PER_ID and a.TR_CODE=b.TR_CODE
												 order by		a.TRN_STARTDATE ";							   
							}elseif($DPISDB=="oci8"){
								$cmd = " select			a.TRN_STARTDATE, a.TRN_ENDDATE, b.TR_NAME, a.TRN_PLACE, a.TRN_ORG
												 from			PER_TRAINING a, PER_TRAIN b
												 where		a.PER_ID=$PER_ID and a.TR_CODE=b.TR_CODE
												 order by		a.TRN_STARTDATE ";							   
							}elseif($DPISDB=="mysql"){
								$cmd = " select			a.TRN_STARTDATE, a.TRN_ENDDATE, b.TR_NAME, a.TRN_PLACE, a.TRN_ORG
												 from			PER_TRAINING a, PER_TRAIN b
												 where		a.PER_ID=$PER_ID and a.TR_CODE=b.TR_CODE
												 order by		a.TRN_STARTDATE ";		
							} // end if
							$count_traininghis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							if($count_traininghis){
								$traininghis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$traininghis_count++;
									$TRN_STARTDATE = display_date($data2[TRN_STARTDATE]);
									$TRN_ENDDATE = display_date($data2[TRN_ENDDATE]);
									$TRN_DURATION = "$TRN_STARTDATE - $TRN_ENDDATE";
									if($TRN_STARTDATE == $TRN_ENDDATE) $TRN_DURATION = "$TRN_STARTDATE";
									$TR_NAME = trim($data2[TR_NAME]);
									$TRN_PLACE = trim($data2[TRN_PLACE]);
									$TRN_ORG = trim($data2[TRN_ORG]);
				
									$border = "";
									$pdf->SetFont($font,'',14);
									$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
									
									$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
								
									$pdf->MultiCell($heading_width[$HISTORY_NAME][0], 7, "$TRN_DURATION", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0];
									$pdf->y = $start_y;

									$pdf->MultiCell($heading_width[$HISTORY_NAME][1], 7, "$TR_NAME", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1];
									$pdf->y = $start_y;
									
									$pdf->MultiCell($heading_width[$HISTORY_NAME][2], 7, "$TRN_ORG", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1]  + $heading_width[$HISTORY_NAME][2];
									$pdf->y = $start_y;

									$pdf->MultiCell($heading_width[$HISTORY_NAME][3], 7, "$TRN_PLACE", $border, "L");
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
										if($traininghis_count < $count_traininghis){
											$pdf->AddPage();
											print_header($HISTORY_NAME);
											$max_y = $pdf->y;
										} // end if
									}else{
										if($traininghis_count == $count_traininghis) $pdf->Line($start_x, $max_y, $pdf->x, $max_y);
									} // end if
									$pdf->x = $start_x;			$pdf->y = $max_y;
								} // end while
							}else{
								$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
								$pdf->Cell(200,7,"********** ไม่มีข้อมูล **********","LBR",1,'C');
							} // end if
						break;
					case "ABILITY" :
							if(($pdf->h - $pdf->y - 10) < 30) $pdf->AddPage();
							$pdf->SetFont($fontb,'',14);
							$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
							if($pdf->y != $page_start_y) $pdf->Cell(200,7,"",0,1,"L");
							$pdf->Cell(200,7,($history_index + 1)."  ความสามารถพิเศษ ",0,1,"L");
							print_header($HISTORY_NAME);

							$AL_NAME = "";
							$ABI_DESC = "";

							if($DPISDB=="odbc"){
								$cmd = " select			b.AL_NAME, a.ABI_DESC
												 from			PER_ABILITY a, PER_ABILITYGRP b
												 where		a.PER_ID=$PER_ID and a.AL_CODE=b.AL_CODE
												 order by		a.ABI_ID ";							   
							}elseif($DPISDB=="oci8"){
								$cmd = " select			b.AL_NAME, a.ABI_DESC
												 from			PER_ABILITY a, PER_ABILITYGRP b
												 where		a.PER_ID=$PER_ID and a.AL_CODE=b.AL_CODE
												 order by		a.ABI_ID ";							   
							}elseif($DPISDB=="mysql"){
								$cmd = " select			b.AL_NAME, a.ABI_DESC
												 from			PER_ABILITY a, PER_ABILITYGRP b
												 where		a.PER_ID=$PER_ID and a.AL_CODE=b.AL_CODE
												 order by		a.ABI_ID ";		
							} // end if
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
								
									$pdf->MultiCell($heading_width[$HISTORY_NAME][0], 7, "$AL_NAME", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0];
									$pdf->y = $start_y;

									$pdf->MultiCell($heading_width[$HISTORY_NAME][1], 7, "$ABI_DESC", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1];
									$pdf->y = $start_y;

									//================= Draw Border Line ====================
									$line_start_y = $start_y;		$line_start_x = $start_x;
									$line_end_y = $max_y;		$line_end_x = $start_x;
									$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				
									for($i=0; $i<=1; $i++){
										$line_start_y = $start_y;		$line_start_x += $heading_width[$HISTORY_NAME][$i];
										$line_end_y = $max_y;		$line_end_x += $heading_width[$HISTORY_NAME][$i];
										$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
									} // end for
									//====================================================
				
									if(($pdf->h - $max_y - 10) < 10){ 
										$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
										if($abilityhis_count < $count_abilityhis){
											$pdf->AddPage();
											print_header($HISTORY_NAME);
											$max_y = $pdf->y;
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
					case "HEIR" :
							if(($pdf->h - $pdf->y - 10) < 30) $pdf->AddPage();
							$pdf->SetFont($fontb,'',14);
							$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
							if($pdf->y != $page_start_y) $pdf->Cell(200,7,"",0,1,"L");
							$pdf->Cell(200,7,($history_index + 1)."  ข้อมูลทายาท ",0,1,"L");
							print_header($HISTORY_NAME);

							$HR_NAME = "";
							$HEIR_NAME = "";

							if($DPISDB=="odbc"){
								$cmd = " select			b.HR_NAME, a.HEIR_NAME
												 from			PER_HEIR a, PER_HEIRTYPE b
												 where		a.PER_ID=$PER_ID and a.HR_CODE=b.HR_CODE
												 order by		a.HEIR_ID ";							   
							}elseif($DPISDB=="oci8"){
								$cmd = " select			b.HR_NAME, a.HEIR_NAME
												 from			PER_HEIR a, PER_HEIRTYPE b
												 where		a.PER_ID=$PER_ID and a.HR_CODE=b.HR_CODE
												 order by		a.HEIR_ID ";							   
							}elseif($DPISDB=="mysql"){
								$cmd = " select			b.HR_NAME, a.HEIR_NAME
												 from			PER_HEIR a, PER_HEIRTYPE b
												 where		a.PER_ID=$PER_ID and a.HR_CODE=b.HR_CODE
												 order by		a.HEIR_ID ";		
							} // end if
							$count_heirhis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							if($count_heirhis){
								$heirhis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$heirhis_count++;
									$HR_NAME = trim($data2[HR_NAME]);
									$HEIR_NAME = trim($data2[HEIR_NAME]);
				
									$border = "";
									$pdf->SetFont($font,'',14);
									$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
									
									$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
								
									$pdf->MultiCell($heading_width[$HISTORY_NAME][0], 7, "$HR_NAME", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0];
									$pdf->y = $start_y;

									$pdf->MultiCell($heading_width[$HISTORY_NAME][1], 7, "$HEIR_NAME", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1];
									$pdf->y = $start_y;

									//================= Draw Border Line ====================
									$line_start_y = $start_y;		$line_start_x = $start_x;
									$line_end_y = $max_y;		$line_end_x = $start_x;
									$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				
									for($i=0; $i<=1; $i++){
										$line_start_y = $start_y;		$line_start_x += $heading_width[$HISTORY_NAME][$i];
										$line_end_y = $max_y;		$line_end_x += $heading_width[$HISTORY_NAME][$i];
										$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
									} // end for
									//====================================================
				
									if(($pdf->h - $max_y - 10) < 10){ 
										$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
										if($heirhis_count < $count_heirhis){
											$pdf->AddPage();
											print_header($HISTORY_NAME);
											$max_y = $pdf->y;
										} // end if
									}else{
										if($heirhis_count == $count_heirhis) $pdf->Line($start_x, $max_y, $pdf->x, $max_y);
									} // end if
									$pdf->x = $start_x;			$pdf->y = $max_y;
								} // end while
							}else{
								$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
								$pdf->Cell(200,7,"********** ไม่มีข้อมูล **********","LBR",1,'C');
							} // end if
						break;
					case "ABSENTHIS" :
							if(($pdf->h - $pdf->y - 10) < 30) $pdf->AddPage();
							$pdf->SetFont($fontb,'',14);
							$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
							if($pdf->y != $page_start_y) $pdf->Cell(200,7,"",0,1,"L");
							$pdf->Cell(200,7,($history_index + 1)."  ประวัติการลา สาย ขาด ",0,1,"L");
							print_header($HISTORY_NAME);
							
							$ABS_STARTDATE = "";
							$ABS_ENDDATE = "";
							$ABS_DAY = "";
							$AB_NAME = "";

							if($DPISDB=="odbc"){
								$cmd = " select			a.ABS_STARTDATE, a.ABS_ENDDATE, a.ABS_DAY, b.AB_NAME
												 from			PER_ABSENTHIS a, PER_ABSENTTYPE b
												 where		a.PER_ID=$PER_ID and a.AB_CODE=b.AB_CODE
												 order by		a.ABS_STARTDATE ";							   
							}elseif($DPISDB=="oci8"){
								$cmd = " select			a.ABS_STARTDATE, a.ABS_ENDDATE, a.ABS_DAY, b.AB_NAME
												 from			PER_ABSENTHIS a, PER_ABSENTTYPE b
												 where		a.PER_ID=$PER_ID and a.AB_CODE=b.AB_CODE
												 order by		a.ABS_STARTDATE ";							   
							}elseif($DPISDB=="mysql"){
								$cmd = " select			a.ABS_STARTDATE, a.ABS_ENDDATE, a.ABS_DAY, b.AB_NAME
												 from			PER_ABSENTHIS a, PER_ABSENTTYPE b
												 where		a.PER_ID=$PER_ID and a.AB_CODE=b.AB_CODE
												 order by		a.ABS_STARTDATE ";
							} // end if
							$count_absenthis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							if($count_absenthis){
								$absenthis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$absenthis_count++;
									$ABS_STARTDATE = display_date($data2[ABS_STARTDATE]);
									$ABS_ENDDATE = display_date($data2[ABS_ENDDATE]);
									$ABS_DURATION = "$ABS_STARTDATE - $ABS_ENDDATE";
									if($ABS_STARTDATE == $ABS_ENDDATE) $ABS_DURATION = "$ABS_STARTDATE";
									$ABS_DAY = $data2[ABS_DAY];
									$AB_NAME = trim($data2[AB_NAME]);
				
									$border = "";
									$pdf->SetFont($font,'',14);
									$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
									
									$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
								
									$pdf->MultiCell($heading_width[$HISTORY_NAME][0], 7, "$ABS_DURATION", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0];
									$pdf->y = $start_y;

									$pdf->Cell($heading_width[$HISTORY_NAME][1] ,7,($ABS_DAY?$ABS_DAY:""),$border,0,"R");

									$pdf->MultiCell($heading_width[$HISTORY_NAME][2], 7, "$AB_NAME", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1] + $heading_width[$HISTORY_NAME][2];
									$pdf->y = $start_y;

									//================= Draw Border Line ====================
									$line_start_y = $start_y;		$line_start_x = $start_x;
									$line_end_y = $max_y;		$line_end_x = $start_x;
									$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				
									for($i=0; $i<=2; $i++){
										$line_start_y = $start_y;		$line_start_x += $heading_width[$HISTORY_NAME][$i];
										$line_end_y = $max_y;		$line_end_x += $heading_width[$HISTORY_NAME][$i];
										$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
									} // end for
									//====================================================
				
									if(($pdf->h - $max_y - 10) < 10){ 
										$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
										if($absenthis_count < $count_absenthis){
											$pdf->AddPage();
											print_header($HISTORY_NAME);
											$max_y = $pdf->y;
										} // end if
									}else{
										if($absenthis_count == $count_absenthis) $pdf->Line($start_x, $max_y, $pdf->x, $max_y);
									} // end if
									$pdf->x = $start_x;			$pdf->y = $max_y;
								} // end while
							}else{
								$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
								$pdf->Cell(200,7,"********** ไม่มีข้อมูล **********","LBR",1,'C');
							} // end if
						break;
					case "PUNISHMENT" :
							if(($pdf->h - $pdf->y - 10) < 30) $pdf->AddPage();
							$pdf->SetFont($fontb,'',14);
							$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
							if($pdf->y != $page_start_y) $pdf->Cell(200,7,"",0,1,"L");
							$pdf->Cell(200,7,($history_index + 1)."  ประวัติทางวินัย ",0,1,"L");
							print_header($HISTORY_NAME);

							$CR_NAME = "";
							$CRD_NAME = "";
							$PUN_STARTDATE = "";
							$PUN_ENDDATE = "";							
							
							if($DPISDB=="odbc"){
								$cmd = " select			c.CR_NAME, b.CRD_NAME, a.PUN_STARTDATE, a.PUN_ENDDATE
												 from			PER_PUNISHMENT a, PER_CRIME_DTL b, PER_CRIME c
												 where		a.PER_ID=$PER_ID and a.CRD_CODE=b.CRD_CODE and b.CR_CODE=c.CR_CODE
												 order by		a.PUN_STARTDATE ";							   
							}elseif($DPISDB=="oci8"){
								$cmd = " select			c.CR_NAME, b.CRD_NAME, a.PUN_STARTDATE, a.PUN_ENDDATE
												 from			PER_PUNISHMENT a, PER_CRIME_DTL b, PER_CRIME c
												 where		a.PER_ID=$PER_ID and a.CRD_CODE=b.CRD_CODE and b.CR_CODE=c.CR_CODE
												 order by		a.PUN_STARTDATE ";							   
							}elseif($DPISDB=="mysql"){
								$cmd = " select			c.CR_NAME, b.CRD_NAME, a.PUN_STARTDATE, a.PUN_ENDDATE
												 from			PER_PUNISHMENT a, PER_CRIME_DTL b, PER_CRIME c
												 where		a.PER_ID=$PER_ID and a.CRD_CODE=b.CRD_CODE and b.CR_CODE=c.CR_CODE
												 order by		a.PUN_STARTDATE ";		
							} // end if
							$count_punishmenthis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							if($count_punishmenthis){
								$punishmenthis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$punishmenthis_count++;
									$CR_NAME = trim($data2[CR_NAME]);
									$CRD_NAME = trim($data2[CRD_NAME]);
									$PUN_STARTDATE = display_date($data2[PUN_STARTDATE]);
									$PUN_ENDDATE = display_date($data2[PUN_ENDDATE]);
									$PUN_DURATION = "$PUN_STARTDATE - $PUN_ENDDATE";
									if($PUN_STARTDATE == $PUN_ENDDATE) $PUN_DURATION = "$PUN_STARTDATE";
				
									$border = "";
									$pdf->SetFont($font,'',14);
									$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
									
									$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
								
									$pdf->MultiCell($heading_width[$HISTORY_NAME][0], 7, "$CR_NAME", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0];
									$pdf->y = $start_y;

									$pdf->MultiCell($heading_width[$HISTORY_NAME][1], 7, "$CRD_NAME", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1];
									$pdf->y = $start_y;

									$pdf->MultiCell($heading_width[$HISTORY_NAME][2], 7, "$PUN_DURATION", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1] + $heading_width[$HISTORY_NAME][2];
									$pdf->y = $start_y;

									//================= Draw Border Line ====================
									$line_start_y = $start_y;		$line_start_x = $start_x;
									$line_end_y = $max_y;		$line_end_x = $start_x;
									$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				
									for($i=0; $i<=2; $i++){
										$line_start_y = $start_y;		$line_start_x += $heading_width[$HISTORY_NAME][$i];
										$line_end_y = $max_y;		$line_end_x += $heading_width[$HISTORY_NAME][$i];
										$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
									} // end for
									//====================================================
				
									if(($pdf->h - $max_y - 10) < 10){ 
										$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
										if($punishmenthis_count < $count_punishmenthis){
											$pdf->AddPage();
											print_header($HISTORY_NAME);
											$max_y = $pdf->y;
										} // end if
									}else{
										if($punishmenthis_count == $count_punishmenthis) $pdf->Line($start_x, $max_y, $pdf->x, $max_y);
									} // end if
									$pdf->x = $start_x;			$pdf->y = $max_y;
								} // end while
							}else{
								$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
								$pdf->Cell(200,7,"********** ไม่มีข้อมูล **********","LBR",1,'C');
							} // end if
						break;
					case "TIMEHIS" :
							if(($pdf->h - $pdf->y - 10) < 30) $pdf->AddPage();
							$pdf->SetFont($fontb,'',14);
							$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
							if($pdf->y != $page_start_y) $pdf->Cell(200,7,"",0,1,"L");
							$pdf->Cell(200,7,($history_index + 1)."  ประวัติเวลาทวีคูณ ",0,1,"L");
							print_header($HISTORY_NAME);

							$TIME_NAME = "";
							$TIME_DAY = "";
							$TIMEH_MINUS = "";
							
							if($DPISDB=="odbc"){
								$cmd = " select			b.TIME_NAME, b.TIME_DAY, a.TIMEH_MINUS
												 from			PER_TIMEHIS a, PER_TIME b
												 where		a.PER_ID=$PER_ID and a.TIME_CODE=b.TIME_CODE
												 order by		a.TIMEH_ID ";							   
							}elseif($DPISDB=="oci8"){
								$cmd = " select			b.TIME_NAME, b.TIME_DAY, a.TIMEH_MINUS
												 from			PER_TIMEHIS a, PER_TIME b
												 where		a.PER_ID=$PER_ID and a.TIME_CODE=b.TIME_CODE
												 order by		a.TIMEH_ID ";							   
							}elseif($DPISDB=="mysql"){
								$cmd = " select			b.TIME_NAME, b.TIME_DAY, a.TIMEH_MINUS
												 from			PER_TIMEHIS a, PER_TIME b
												 where		a.PER_ID=$PER_ID and a.TIME_CODE=b.TIME_CODE
												 order by		a.TIMEH_ID ";	
							} // end if
							$count_timehis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							if($count_timehis){
								$timehis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$timehis_count++;
									$TIME_NAME = trim($data2[TIME_NAME]);
									$TIME_DAY = $data2[TIME_DAY];
									$TIMEH_MINUS = $data2[TIMEH_MINUS];
				
									$border = "";
									$pdf->SetFont($font,'',14);
									$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
									
									$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
								
									$pdf->MultiCell($heading_width[$HISTORY_NAME][0], 7, "$TIME_NAME", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0];
									$pdf->y = $start_y;

									$pdf->Cell($heading_width[$HISTORY_NAME][1] ,7,($TIME_DAY?$TIME_DAY:""),$border,0,"R");
									$pdf->Cell($heading_width[$HISTORY_NAME][2] ,7,($TIMEH_MINUS?$TIMEH_MINUS:""),$border,0,"R");

									//================= Draw Border Line ====================
									$line_start_y = $start_y;		$line_start_x = $start_x;
									$line_end_y = $max_y;		$line_end_x = $start_x;
									$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				
									for($i=0; $i<=2; $i++){
										$line_start_y = $start_y;		$line_start_x += $heading_width[$HISTORY_NAME][$i];
										$line_end_y = $max_y;		$line_end_x += $heading_width[$HISTORY_NAME][$i];
										$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
									} // end for
									//====================================================
				
									if(($pdf->h - $max_y - 10) < 10){ 
										$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
										if($timehis_count < $count_timehis){
											$pdf->AddPage();
											print_header($HISTORY_NAME);
											$max_y = $pdf->y;
										} // end if
									}else{
										if($timehis_count == $count_timehis) $pdf->Line($start_x, $max_y, $pdf->x, $max_y);
									} // end if
									$pdf->x = $start_x;			$pdf->y = $max_y;
								} // end while
							}else{
								$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
								$pdf->Cell(200,7,"********** ไม่มีข้อมูล **********","LBR",1,'C');
							} // end if
						break;
					case "REWARDHIS" :
							if(($pdf->h - $pdf->y - 10) < 30) $pdf->AddPage();
							$pdf->SetFont($fontb,'',14);
							$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
							if($pdf->y != $page_start_y) $pdf->Cell(200,7,"",0,1,"L");
							$pdf->Cell(200,7,($history_index + 1)."  ประวัติการรับความดีความชอบ ",0,1,"L");
							print_header($HISTORY_NAME);

							$REH_DATE = "";
							$REW_NAME = "";
							
							if($DPISDB=="odbc"){
								$cmd = " select			a.REH_DATE, b.REW_NAME
												 from			PER_REWARDHIS a, PER_REWARD b
												 where		a.PER_ID=$PER_ID and a.REW_CODE=b.REW_CODE
												 order by		a.REH_DATE ";							   
							}elseif($DPISDB=="oci8"){
								$cmd = " select			a.REH_DATE, b.REW_NAME
												 from			PER_REWARDHIS a, PER_REWARD b
												 where		a.PER_ID=$PER_ID and a.REW_CODE=b.REW_CODE
												 order by		a.REH_DATE ";							   
							}elseif($DPISDB=="mysql"){
								$cmd = " select			a.REH_DATE, b.REW_NAME
												 from			PER_REWARDHIS a, PER_REWARD b
												 where		a.PER_ID=$PER_ID and a.REW_CODE=b.REW_CODE
												 order by		a.REH_DATE ";	
							} // end if
							$count_rewardhis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							if($count_rewardhis){
								$rewardhis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$rewardhis_count++;
									$REH_DATE = display_date($data2[REH_DATE]);
									$REW_NAME = trim($data2[REW_NAME]);
				
									$border = "";
									$pdf->SetFont($font,'',14);
									$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
									
									$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
								
									$pdf->MultiCell($heading_width[$HISTORY_NAME][0], 7, "$REH_DATE", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0];
									$pdf->y = $start_y;

									$pdf->MultiCell($heading_width[$HISTORY_NAME][1], 7, "$REW_NAME", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1];
									$pdf->y = $start_y;

									//================= Draw Border Line ====================
									$line_start_y = $start_y;		$line_start_x = $start_x;
									$line_end_y = $max_y;		$line_end_x = $start_x;
									$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				
									for($i=0; $i<=1; $i++){
										$line_start_y = $start_y;		$line_start_x += $heading_width[$HISTORY_NAME][$i];
										$line_end_y = $max_y;		$line_end_x += $heading_width[$HISTORY_NAME][$i];
										$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
									} // end for
									//====================================================
				
									if(($pdf->h - $max_y - 10) < 10){ 
										$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
										if($rewardhis_count < $count_rewardhis){
											$pdf->AddPage();
											print_header($HISTORY_NAME);
											$max_y = $pdf->y;
										} // end if
									}else{
										if($rewardhis_count == $count_rewardhis) $pdf->Line($start_x, $max_y, $pdf->x, $max_y);
									} // end if
									$pdf->x = $start_x;			$pdf->y = $max_y;
								} // end while
							}else{
								$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
								$pdf->Cell(200,7,"********** ไม่มีข้อมูล **********","LBR",1,'C');
							} // end if
						break;
					case "MARRHIS" :
							if(($pdf->h - $pdf->y - 10) < 30) $pdf->AddPage();
							$pdf->SetFont($fontb,'',14);
							$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
							if($pdf->y != $page_start_y) $pdf->Cell(200,7,"",0,1,"L");
							$pdf->Cell(200,7,($history_index + 1)."  ประวัติการสมรส ",0,1,"L");
							print_header($HISTORY_NAME);

							$MAH_NAME = "";
							$MAH_MARRY_DATE = "";
							
							if($DPISDB=="odbc"){
								$cmd = " select			MAH_NAME, MAH_MARRY_DATE, MAH_DIVORCE_DATE, DV_CODE
												 from			PER_MARRHIS
												 where		PER_ID=$PER_ID
												 order by		MAH_SEQ ";						   
							}elseif($DPISDB=="oci8"){
								$cmd = " select			MAH_NAME, MAH_MARRY_DATE, MAH_DIVORCE_DATE, DV_CODE
												 from			PER_MARRHIS
												 where		PER_ID=$PER_ID
												 order by		MAH_SEQ ";					   
																					    
							}elseif($DPISDB=="mysql"){
								$cmd = " select			MAH_NAME, MAH_MARRY_DATE, MAH_DIVORCE_DATE, DV_CODE
												 from			PER_MARRHIS
												 where		PER_ID=$PER_ID
												 order by		MAH_SEQ ";
							} // end if
							$count_marryhis = $db_dpis2->send_cmd($cmd);
		//				$db_dpis2->show_error();
				//		echo $cmd;
							if($count_marryhis){
								$marryhis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$marryhis_count++;
									$MAH_NAME = trim($data2[MAH_NAME]);
									$MAH_DIVORCE_DATE = display_date($data2[MAH_DIVORCE_DATE]);
									$MAH_MARRY_DATE = display_date($data2[MAH_MARRY_DATE]);
									$DV_CODE = trim($data2[DV_CODE]);
									
									$cmd = "select DV_NAME from PER_DIVORCE where DV_CODE = '$DV_CODE'";
									$db_dpis3->send_cmd($cmd);
									$data3 = $db_dpis3->get_array();
									$DV_NAME = $data3[DV_NAME];
				
									$border = "";
									$pdf->SetFont($font,'',14);
									$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
									
									$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
								
									$pdf->MultiCell($heading_width[$HISTORY_NAME][0], 7, "$MAH_NAME", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0];
									$pdf->y = $start_y;

									$pdf->MultiCell($heading_width[$HISTORY_NAME][1], 7, "$MAH_MARRY_DATE", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1];
									$pdf->y = $start_y;
									
									$pdf->MultiCell($heading_width[$HISTORY_NAME][2], 7, "$DV_NAME", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1] + $heading_width[$HISTORY_NAME][2];
									$pdf->y = $start_y;
									
									$pdf->MultiCell($heading_width[$HISTORY_NAME][3], 7, "$MAH_DIVORCE_DATE", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1] + $heading_width[$HISTORY_NAME][2]  + $heading_width[$HISTORY_NAME][3];
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
										if($marryhis_count < $count_marryhis){
											$pdf->AddPage();
											print_header($HISTORY_NAME);
											$max_y = $pdf->y;
										} // end if
									}else{
										if($marryhis_count == $count_marryhis) $pdf->Line($start_x, $max_y, $pdf->x, $max_y);
									} // end if
									$pdf->x = $start_x;			$pdf->y = $max_y;
								} // end while
							}else{
								$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
								$pdf->Cell(200,7,"********** ไม่มีข้อมูล **********","LBR",1,'C');
							} // end if
						break;
					case "NAMEHIS" :
							if(($pdf->h - $pdf->y - 10) < 30) $pdf->AddPage();
							$pdf->SetFont($fontb,'',14);
							$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
							if($pdf->y != $page_start_y) $pdf->Cell(200,7,"",0,1,"L");
							$pdf->Cell(200,7,($history_index + 1)."  ประวัติการเปลี่ยนแปลงชื่อ-สกุล ",0,1,"L");
							print_header($HISTORY_NAME);

							$NH_DATE = "";
							$PN_NAME = "";
							$NH_NAME = "";
							$NH_SURNAME = "";
							
							if($DPISDB=="odbc"){
								$cmd = " select			a.NH_DATE, b.PN_NAME, a.NH_NAME, a.NH_SURNAME
												 from			PER_NAMEHIS a, PER_PRENAME b
												 where		a.PER_ID=$PER_ID and a.PN_CODE=b.PN_CODE
												 order by		a.NH_DATE ";							   
							}elseif($DPISDB=="oci8"){
								$cmd = " select			a.NH_DATE, b.PN_NAME, a.NH_NAME, a.NH_SURNAME
												 from			PER_NAMEHIS a, PER_PRENAME b
												 where		a.PER_ID=$PER_ID and a.PN_CODE=b.PN_CODE
												 order by		a.NH_DATE ";							   
							}elseif($DPISDB=="mysql"){
								$cmd = " select			a.NH_DATE, b.PN_NAME, a.NH_NAME, a.NH_SURNAME
												 from			PER_NAMEHIS a, PER_PRENAME b
												 where		a.PER_ID=$PER_ID and a.PN_CODE=b.PN_CODE
												 order by		a.NH_DATE ";		
							} // end if
							$count_namehis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							if($count_namehis){
								$namehis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$namehis_count++;
									$NH_DATE = display_date($data2[NH_DATE]);
									$PN_NAME = trim($data2[PN_NAME]);
									$NH_NAME = trim($data2[NH_NAME]);
									$NH_SURNAME = trim($data2[NH_SURNAME]);
									$FULLNAME = ($PN_NAME?$PN_NAME:"")."$NH_NAME $NH_SURNAME";
				
									$border = "";
									$pdf->SetFont($font,'',14);
									$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
									
									$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
								
									$pdf->MultiCell($heading_width[$HISTORY_NAME][0], 7, "$NH_DATE", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0];
									$pdf->y = $start_y;

									$pdf->MultiCell($heading_width[$HISTORY_NAME][1], 7, "$FULLNAME", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1];
									$pdf->y = $start_y;

									//================= Draw Border Line ====================
									$line_start_y = $start_y;		$line_start_x = $start_x;
									$line_end_y = $max_y;		$line_end_x = $start_x;
									$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				
									for($i=0; $i<=1; $i++){
										$line_start_y = $start_y;		$line_start_x += $heading_width[$HISTORY_NAME][$i];
										$line_end_y = $max_y;		$line_end_x += $heading_width[$HISTORY_NAME][$i];
										$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
									} // end for
									//====================================================
				
									if(($pdf->h - $max_y - 10) < 10){ 
										$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
										if($namehis_count < $count_namehis){
											$pdf->AddPage();
											print_header($HISTORY_NAME);
											$max_y = $pdf->y;
										} // end if
									}else{
										if($namehis_count == $count_namehis) $pdf->Line($start_x, $max_y, $pdf->x, $max_y);
									} // end if
									$pdf->x = $start_x;			$pdf->y = $max_y;
								} // end while
							}else{
								$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
								$pdf->Cell(200,7,"********** ไม่มีข้อมูล **********","LBR",1,'C');
							} // end if
						break;
					case "DECORATEHIS" :
							if(($pdf->h - $pdf->y - 10) < 30) $pdf->AddPage();
							$pdf->SetFont($fontb,'',14);
							$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
							if($pdf->y != $page_start_y) $pdf->Cell(200,7,"",0,1,"L");
							$pdf->Cell(200,7,($history_index + 1)."  ประวัติการรับเครื่องราชฯ ",0,1,"L");
							print_header($HISTORY_NAME);

							$DC_NAME = "";
							$DEH_DATE = "";
							$DEH_GAZETTE = "";
							
							if($DPISDB=="odbc"){
								$cmd = " select			b.DC_NAME, a.DEH_DATE, a.DEH_GAZETTE
												 from			PER_DECORATEHIS a, PER_DECORATION b
												 where		a.PER_ID=$PER_ID and a.DC_CODE=b.DC_CODE
												 order by		a.DEH_DATE ";							   
							}elseif($DPISDB=="oci8"){
								$cmd = " select			b.DC_NAME, a.DEH_DATE, a.DEH_GAZETTE
												 from			PER_DECORATEHIS a, PER_DECORATION b
												 where		a.PER_ID=$PER_ID and a.DC_CODE=b.DC_CODE
												 order by		a.DEH_DATE ";							   
							}elseif($DPISDB=="mysql"){
								$cmd = " select			b.DC_NAME, a.DEH_DATE, a.DEH_GAZETTE
												 from			PER_DECORATEHIS a, PER_DECORATION b
												 where		a.PER_ID=$PER_ID and a.DC_CODE=b.DC_CODE
												 order by		a.DEH_DATE ";	
							} // end if
							$count_decoratehis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							if($count_decoratehis){
								$decoratehis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$decoratehis_count++;
									$DC_NAME = trim($data2[DC_NAME]);
									$DEH_DATE = display_date($data2[DEH_DATE]);
									$DEH_GAZETTE = trim($data2[DEH_GAZETTE]);
				
									$border = "";
									$pdf->SetFont($font,'',14);
									$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
									
									$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
								
									$pdf->MultiCell($heading_width[$HISTORY_NAME][0], 7, "$DC_NAME", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0];
									$pdf->y = $start_y;

									$pdf->MultiCell($heading_width[$HISTORY_NAME][1], 7, "$DEH_DATE", $border, "C");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1];
									$pdf->y = $start_y;

									$pdf->MultiCell($heading_width[$HISTORY_NAME][2], 7, "$DEH_GAZETTE", $border, "C");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1] + $heading_width[$HISTORY_NAME][2];
									$pdf->y = $start_y;

									//================= Draw Border Line ====================
									$line_start_y = $start_y;		$line_start_x = $start_x;
									$line_end_y = $max_y;		$line_end_x = $start_x;
									$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				
									for($i=0; $i<=2; $i++){
										$line_start_y = $start_y;		$line_start_x += $heading_width[$HISTORY_NAME][$i];
										$line_end_y = $max_y;		$line_end_x += $heading_width[$HISTORY_NAME][$i];
										$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
									} // end for
									//====================================================
				
									if(($pdf->h - $max_y - 10) < 10){ 
										$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
										if($decoratehis_count < $count_decoratehis){
											$pdf->AddPage();
											print_header($HISTORY_NAME);
											$max_y = $pdf->y;
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
						break;
					case "SERVICEHIS" :
							if(($pdf->h - $pdf->y - 10) < 30) $pdf->AddPage();
							$pdf->SetFont($fontb,'',14);
							$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
							if($pdf->y != $page_start_y) $pdf->Cell(200,7,"",0,1,"L");
							$pdf->Cell(200,7,($history_index + 1)."  ประวัติราชการพิเศษ ",0,1,"L");
							print_header($HISTORY_NAME);

							$SRH_STARTDATE = "";
							$SV_NAME = "";
							$SRH_DOCNO = "";
							$SRH_NOTE = "";
							
							if($DPISDB=="odbc"){
								$cmd = " select			a.SRH_STARTDATE, b.SV_NAME, a.SRH_DOCNO, a.SRH_NOTE, c.ORG_NAME
												 from			PER_SERVICEHIS a, PER_SERVICE b, PER_ORG c
												 where		a.PER_ID=$PER_ID and a.SV_CODE=b.SV_CODE and a.ORG_ID=c.ORG_ID
												 order by		a.SRH_STARTDATE ";							   
							}elseif($DPISDB=="oci8"){
								$cmd = " select			a.SRH_STARTDATE, b.SV_NAME, a.SRH_DOCNO, a.SRH_NOTE, c.ORG_NAME, d.SRT_NAME
												 from			PER_SERVICEHIS a, PER_SERVICE b, PER_ORG c, PER_SERVICETITLE d
												 where		a.PER_ID=$PER_ID and a.SV_CODE=b.SV_CODE and a.ORG_ID=c.ORG_ID  and a.SRT_CODE=d.SRT_CODE
												 order by		a.SRH_STARTDATE ";					
											   
							}elseif($DPISDB=="mysql"){
								$cmd = " select			a.SRH_STARTDATE, b.SV_NAME, a.SRH_DOCNO, a.SRH_NOTE, c.ORG_NAME
												 from			PER_SERVICEHIS a, PER_SERVICE b, PER_ORG c
												 where		a.PER_ID=$PER_ID and a.SV_CODE=b.SV_CODE and a.ORG_ID=c.ORG_ID
												 order by		a.SRH_STARTDATE ";	
							} // end if
							$count_servicehis = $db_dpis2->send_cmd($cmd);
							//echo $cmd."<hr>";
							//$db_dpis2->show_error();
							if($count_servicehis){
								$servicehis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$servicehis_count++;
									$SRH_STARTDATE = display_date($data2[SRH_STARTDATE]);
									$SV_NAME = trim($data2[SV_NAME]);
									$SRH_DOCNO = trim($data2[SRH_DOCNO]);
									$SRH_NOTE = trim($data2[SRH_NOTE]);
									$SRH_ORG = trim($data2[ORG_NAME]);
									$SRT_NAME = trim($data2[SRT_NAME]);
									//echo "SRT_NAME = ".$SRT_NAME;
									
						/*			$cmd = "select SRT_NAME from PER_SERVICETITLE where SRT_CODE='$SRT_CODE'";
									 $db_dpis3->send_cmd($cmd);
									 $db_dpis3->show_error();
									 echo $cmd."<br>";
									 $data3 = $db_dpis3->get_array();
									 $SRT_NAME = $data3[SRT_NAME];   */
				
									$border = "";
									$pdf->SetFont($font,'',14);
									$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
									
									$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
								
									$pdf->MultiCell($heading_width[$HISTORY_NAME][0], 7, "$SRH_STARTDATE", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0];
									$pdf->y = $start_y;

									$pdf->MultiCell($heading_width[$HISTORY_NAME][1], 7, "$SV_NAME", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1];
									$pdf->y = $start_y;
									
									$pdf->MultiCell($heading_width[$HISTORY_NAME][2], 7, "$SRT_NAME", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1] + $heading_width[$HISTORY_NAME][2];
									$pdf->y = $start_y;

									$pdf->MultiCell($heading_width[$HISTORY_NAME][3], 7, "$SRH_ORG", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1] + $heading_width[$HISTORY_NAME][2] + $heading_width[$HISTORY_NAME][3];
									$pdf->y = $start_y;

									$pdf->MultiCell($heading_width[$HISTORY_NAME][4], 7, "$SRH_DOCNO", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1] + $heading_width[$HISTORY_NAME][2] + $heading_width[$HISTORY_NAME][3] + $heading_width[$HISTORY_NAME][4];
									$pdf->y = $start_y;

									$pdf->MultiCell($heading_width[$HISTORY_NAME][5], 7, "$SRH_NOTE", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1] + $heading_width[$HISTORY_NAME][2] + $heading_width[$HISTORY_NAME][3] + $heading_width[$HISTORY_NAME][4] + $heading_width[$HISTORY_NAME][5];
									$pdf->y = $start_y;

									//================= Draw Border Line ====================
									$line_start_y = $start_y;		$line_start_x = $start_x;
									$line_end_y = $max_y;		$line_end_x = $start_x;
									$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				
									for($i=0; $i<=5; $i++){
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
							if($pdf->y != $page_start_y) $pdf->Cell(200,7,"",0,1,"L");
							$pdf->Cell(200,7,($history_index + 1)."  ความเชี่ยวชาญพิเศษ ",0,1,"L");
							print_header($HISTORY_NAME);

							$SS_NAME = "";
							$SPS_EMPHASIZE = "";
							
							if($DPISDB=="odbc"){
								$cmd = " select			b.SS_NAME, a.SPS_EMPHASIZE
												 from			PER_SPECIAL_SKILL a, PER_SPECIAL_SKILLGRP b
												 where		a.PER_ID=$PER_ID and a.SS_CODE=b.SS_CODE
												 order by		a.SPS_ID ";							   
							}elseif($DPISDB=="oci8"){
								$cmd = " select			b.SS_NAME, a.SPS_EMPHASIZE
												 from			PER_SPECIAL_SKILL a, PER_SPECIAL_SKILLGRP b
												 where		a.PER_ID=$PER_ID and a.SS_CODE=b.SS_CODE
												 order by		a.SPS_ID ";							   
							}elseif($DPISDB=="mysql"){
								$cmd = " select			b.SS_NAME, a.SPS_EMPHASIZE
												 from			PER_SPECIAL_SKILL a, PER_SPECIAL_SKILLGRP b
												 where		a.PER_ID=$PER_ID and a.SS_CODE=b.SS_CODE
												 order by		a.SPS_ID ";		
							} // end if
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
								
									$pdf->MultiCell($heading_width[$HISTORY_NAME][0], 7, "$SS_NAME", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0];
									$pdf->y = $start_y;

									$pdf->MultiCell($heading_width[$HISTORY_NAME][1], 7, "$SPS_EMPHASIZE", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1];
									$pdf->y = $start_y;

									//================= Draw Border Line ====================
									$line_start_y = $start_y;		$line_start_x = $start_x;
									$line_end_y = $max_y;		$line_end_x = $start_x;
									$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				
									for($i=0; $i<=1; $i++){
										$line_start_y = $start_y;		$line_start_x += $heading_width[$HISTORY_NAME][$i];
										$line_end_y = $max_y;		$line_end_x += $heading_width[$HISTORY_NAME][$i];
										$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
									} // end for
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
					case "EXTRAHIS" :
							if(($pdf->h - $pdf->y - 10) < 30) $pdf->AddPage();
							$pdf->SetFont($fontb,'',14);
							$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
							if($pdf->y != $page_start_y) $pdf->Cell(200,7,"",0,1,"L");
							$pdf->Cell(200,7,($history_index + 1)."  ประวัติการรับเงินพิเศษ ",0,1,"L");
							print_header($HISTORY_NAME);

							$EXH_EFFECTIVEDATE = "";
							$EXH_ENDDATE = "";
							$EX_NAME = "";
							$EXH_AMT = "";
							
							if($DPISDB=="odbc"){
								$cmd = " select			a.EXH_EFFECTIVEDATE, a.EXH_ENDDATE, b.EX_NAME, a.EXH_AMT
												 from			PER_EXTRAHIS a, PER_EXTRATYPE b
												 where		a.PER_ID=$PER_ID and a.EX_CODE=b.EX_CODE 
												 order by		a.EXH_EFFECTIVEDATE ";							   
							}elseif($DPISDB=="oci8"){
								$cmd = " select			a.EXH_EFFECTIVEDATE, a.EXH_ENDDATE, b.EX_NAME, a.EXH_AMT
												 from			PER_EXTRAHIS a, PER_EXTRATYPE b
												 where		a.PER_ID=$PER_ID and a.EX_CODE=b.EX_CODE 
												 order by		a.EXH_EFFECTIVEDATE ";							   
							}elseif($DPISDB=="mysql"){
								$cmd = " select			a.EXH_EFFECTIVEDATE, a.EXH_ENDDATE, b.EX_NAME, a.EXH_AMT
												 from			PER_EXTRAHIS a, PER_EXTRATYPE b
												 where		a.PER_ID=$PER_ID and a.EX_CODE=b.EX_CODE 
												 order by		a.EXH_EFFECTIVEDATE ";		
							} // end if
							$count_extrahis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							if($count_extrahis){
								$extrahis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$extrahis_count++;
									$EXH_EFFECTIVEDATE = display_date($data2[EXH_EFFECTIVEDATE]);
									$EXH_ENDDATE = display_date($data2[EXH_ENDDATE]);
									$EX_NAME = trim($data2[EX_NAME]);
									$EXH_AMT = $data2[EXH_AMT];
				
									$border = "";
									$pdf->SetFont($font,'',14);
									$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
									
									$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
								
									$pdf->Cell($heading_width[$HISTORY_NAME][0] ,7,"$EXH_EFFECTIVEDATE",$border,0,"L");
									$pdf->Cell($heading_width[$HISTORY_NAME][1] ,7,"$EXH_ENDDATE",$border,0,"L");

									$pdf->MultiCell($heading_width[$HISTORY_NAME][2], 7, "$EX_NAME", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1] + $heading_width[$HISTORY_NAME][2];
									$pdf->y = $start_y;

									$pdf->Cell($heading_width[$HISTORY_NAME][3] ,7,number_format($EXH_AMT, ","),$border,0,'R');

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
										if($extrahis_count < $count_exincomehis){
											$pdf->AddPage();
											print_header($HISTORY_NAME);
											$max_y = $pdf->y;
										} // end if
									}else{
										if($extrahis_count == $count_extrahis) $pdf->Line($start_x, $max_y, $pdf->x, $max_y);
									} // end if
									$pdf->x = $start_x;			$pdf->y = $max_y;
								} // end while
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