<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php");

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	if(!trim($RPTORD_LIST)){ 
		if($select_org_structure==1){	//สำหรับมอบหมายงาน
			if(trim($search_org_ass_id)){	$search_org_id=trim($search_org_ass_id);		}	
			if(trim($search_org_ass_id_1)){	$search_org_id_1=trim($search_org_ass_id_1);		}
			if(trim($search_org_ass_id_2)){	$search_org_id_2=trim($search_org_ass_id_2);		}
		}
		if($list_type=="PER_ORG" && trim($search_org_id_1)=="" && trim($search_org_id_2)=="") $RPTORD_LIST = "ORG_1";
		elseif($list_type=="PER_ORG" && trim($search_org_id_1)!="" && trim($search_org_id_2)=="") $RPTORD_LIST = "ORG_2";
		elseif($list_type=="PER_ORG" && trim($search_org_id_1)!="" && trim($search_org_id_2)!="") $RPTORD_LIST = "LINE";
		else $RPTORD_LIST = "ORG";
	} // end if
	$arr_rpt_order = explode("|", $RPTORD_LIST);
//	$arr_rpt_order = array("ORG"); 

	$search_per_type = 1;
	$search_per_status = 1;
	
	$search_condition = "";
//	$arr_search_condition[] = "(a.PER_TYPE in (". implode(", ", $search_per_type) .") and (b.POS_ID >= 0 or e.POEM_ID >= 0))";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(a.PER_STATUS in (". implode(", ", $search_per_status) ."))";

	$list_type_text = $ALL_REPORT_TITLE;

	if($DEPARTMENT_ID){
		$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
	}elseif($MINISTRY_ID){
		$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
		if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

		$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		$list_type_text .= " - $MINISTRY_NAME";
	}elseif($PROVINCE_CODE){
		$PROVINCE_CODE = trim($PROVINCE_CODE);
		$arr_search_condition[] = "(trim(c.PV_CODE) = '$PROVINCE_CODE')";
		$list_type_text .= " - $PROVINCE_NAME";
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);

	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "";
	$report_title = "บัญชีสรุปจำนวนข้าราชการและอัตราเงินเดือนรวมของข้าราชการ||สำหรับการเลื่อนขั้นเงินเดือน ประจำปีงบประมาณ $search_budget_year||$DEPARTMENT_NAME $MINISTRY_NAME";
	$report_code = "R0605";
	$orientation='L';

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

	$heading_width[0] = "52";
	$heading_width[1] = "20";
	$heading_width[2] = "15";
	$heading_width[3] = "15";
	$heading_width[4] = "20";
	$heading_width[5] = "25";
	$heading_width[6] = "20";
	$heading_width[7] = "20";
	$heading_width[8] = "20";
	$heading_width[9] = "15";
	$heading_width[10] = "15";
	$heading_width[11] = "15";
	$heading_width[12] = "20";
	$heading_width[13] = "15";

	function print_header($search_level_min, $search_level_max){
		global $pdf, $heading_width, $heading_name;
		global $search_budget_year;
		
		$pdf->SetFont($fontb,'',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

		$pdf->Cell(($heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6] + $heading_width[7] + $heading_width[8]) ,7,"",'',0,'C',0);
		$pdf->Cell(($heading_width[9] + $heading_width[10] + $heading_width[11] + $heading_width[12] + $heading_width[13]) ,7,"กลุ่มข้าราชการระดับ $search_level_min - $search_level_max",'',1,'C',0);

		$pdf->SetFont($font,'',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));

		$pdf->Cell($heading_width[0] ,7,"",'LTR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"จำนวน",'LTR',0,'C',1);
		$pdf->Cell(($heading_width[2] + $heading_width[3] + $heading_width[4]) ,7,"เลื่อน 1 เมษายน ". ($search_budget_year - 1),'LTR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,"อัตรา",'LTR',0,'C',1);
		$pdf->Cell($heading_width[6] ,7,"วงเงิน",'LTR',0,'C',1);
		$pdf->Cell($heading_width[7] ,7,"เงินเลื่อน",'LTR',0,'C',1);
		$pdf->Cell($heading_width[8] ,7,"วงเงิน",'LTR',0,'C',1);
		$pdf->Cell(($heading_width[9] + $heading_width[10] + $heading_width[11] + $heading_width[12]) ,7,"เลื่อน 1 ตุลาคม ". ($search_budget_year - 1),'LTR',0,'C',1);
		$pdf->Cell($heading_width[13] ,7,"รวม",'LTR',1,'C',1);

		$pdf->Cell($heading_width[0] ,7,"ส่วนราชการ",'LR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"อัตราที่มี",'LR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"ร้อยละ",'LTR',0,'C',1);
		$pdf->Cell($heading_width[3] ,7,"1 ขั้น",'LTR',0,'C',1);
		$pdf->Cell($heading_width[4] ,7,"ค่า",'LTR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,"เงินเดือน",'LR',0,'C',1);
		$pdf->Cell($heading_width[6] ,7,"เลื่อนขั้น",'LR',0,'C',1);
		$pdf->Cell($heading_width[7] ,7,"ขั้นที่ใช้",'LR',0,'C',1);
		$pdf->Cell($heading_width[8] ,7,"เลื่อนขั้น",'LR',0,'C',1);
		$pdf->Cell($heading_width[9] ,7,"ร้อยละ",'LTR',0,'C',1);
		$pdf->Cell($heading_width[10] ,7,"1 ขั้น",'LTR',0,'C',1);
		$pdf->Cell($heading_width[11] ,7,"1.5 ขั้น",'LTR',0,'C',1);
		$pdf->Cell($heading_width[12] ,7,"ค่า",'LTR',0,'C',1);
		$pdf->Cell($heading_width[13] ,7,"ทั้งปี",'LR',1,'C',1);

		$pdf->Cell($heading_width[0] ,7,"",'LR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"ผู้ครองอยู่",'LR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"15",'LR',0,'C',1);
		$pdf->Cell($heading_width[3] ,7,"",'LR',0,'C',1);
		$pdf->Cell($heading_width[4] ,7,"ตอบแทน",'LR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,"รวม ณ",'LR',0,'C',1);
		$pdf->Cell($heading_width[6] ,7,"ร้อยละ 6",'LR',0,'C',1);
		$pdf->Cell($heading_width[7] ,7,"ณ 1 เม.ย.",'LR',0,'C',1);
		$pdf->Cell($heading_width[8] ,7,"คงเหลือ",'LR',0,'C',1);
		$pdf->Cell($heading_width[9] ,7,"15",'LR',0,'C',1);
		$pdf->Cell($heading_width[10] ,7,"",'LR',0,'C',1);
		$pdf->Cell($heading_width[11] ,7,"",'LR',0,'C',1);
		$pdf->Cell($heading_width[12] ,7,"ตอบแทน",'LR',0,'C',1);
		$pdf->Cell($heading_width[13] ,7,"2 ขั้น",'LR',1,'C',1);

		$pdf->Cell($heading_width[0] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"ณ 1 มี.ค.",'LBR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[3] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[4] ,7,"4 %",'LBR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,"1 ก.ย.",'LBR',0,'C',1);
		$pdf->Cell($heading_width[6] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[7] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[8] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[9] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[10] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[11] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[12] ,7,"4 %",'LBR',0,'C',1);
		$pdf->Cell($heading_width[13] ,7,"",'LBR',1,'C',1);
	} // function		

	$cmd = " select PER_ID from PER_SALPROMOTE where SALQ_YEAR='$search_budget_year' and SALP_YN=1 ";
	$count_data = $db_dpis->send_cmd($cmd);
	
	$data_count = 0;
	for($i=0; $i<4; $i++){
		if($i == 0 || ($i % 2) == 0){
			$arr_content[$data_count][type] = "HEADER";
			if ($BKK_FLAG==1)
				$arr_content[$data_count][name] = "กรุงเทพมหานคร";
			else
				$arr_content[$data_count][name] = "ราชการบริหารส่วนกลาง";
			
			$data_count++;
		}else{
			if($i==1){ //?????
				$search_level_min = "O1";			//1
				$search_level_max = "O3";			//8
			}elseif($i==3){
				$search_level_min = "K1";			//9
				$search_level_max = "K3";		//11
			} // end if
					
			if($DPISDB=="odbc"){ 

				$cmd = " select count(PER_ID) as TOTAL_PERSON from PER_PERSONAL where PER_TYPE = 1 and PER_STATUS = 1 and LEFT(trim(PER_STARTDATE), 10) < '". ($search_budget_year - 543 - 1) ."-03-01' and trim(LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
				$data = $db_dpis->get_array();
				$total_person = $data[TOTAL_PERSON] + 0;
				$percent_person = ($total_person * 15) / 100;
					
				$cmd = " select sum(PER_SALARY) as TOTAL_SALARY from PER_PERSONAL where PER_TYPE = 1 and PER_STATUS = 1 and LEFT(trim(PER_STARTDATE), 10) < '". ($search_budget_year - 543 - 1) ."-09-01' and trim(LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
				$data = $db_dpis->get_array();
				$total_salary = $data[TOTAL_SALARY] + 0;
				$percent_salary = ($total_salary * 6) / 100;

				$cmd = " select count(a.PER_ID) as PROMOTED_PERSON from PER_PERSONAL a, PER_SALPROMOTE b where trim(a.LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(a.LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' and b.SALQ_YEAR='$search_budget_year' and b.SALQ_TYPE=1 and b.SALP_YN=1 and b.SALP_LEVEL=1 and a.PER_ID=b.PER_ID ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
				$data = $db_dpis->get_array();
				$promoted_person_no1_1 = $data[PROMOTED_PERSON] + 0;
	
				$cmd = " select count(a.PER_ID) as PROMOTED_PERSON from PER_PERSONAL a, PER_SALPROMOTE b where trim(a.LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(a.LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' and b.SALQ_YEAR='$search_budget_year' and b.SALQ_TYPE=1 and b.SALP_YN=1 and b.SALP_PERCENT=4 and a.PER_ID=b.PER_ID ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
				$data = $db_dpis->get_array();
				$promoted_person_no1_2 = $data[PROMOTED_PERSON] + 0;
	
				$cmd = " select sum(b.SALP_SALARY_NEW - b.SALP_SALARY_OLD) as PROMOTED_SALARY from PER_PERSONAL a, PER_SALPROMOTE b where trim(a.LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(a.LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' and b.SALQ_YEAR='$search_budget_year' and b.SALQ_TYPE=1 and b.SALP_YN=1 and a.PER_ID=b.PER_ID ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
				$data = $db_dpis->get_array();
				$promoted_salary = $data[PROMOTED_SALARY] + 0;					
				$remain_salary = $percent_salary - $promoted_salary;
						
				$cmd = " select count(a.PER_ID) as PROMOTED_PERSON from PER_PERSONAL a, PER_SALPROMOTE b where trim(a.LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(a.LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' and b.SALQ_YEAR='$search_budget_year' and b.SALQ_TYPE=2 and b.SALP_YN=1 and b.SALP_LEVEL=1 and a.PER_ID=b.PER_ID ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
				$data = $db_dpis->get_array();
				$promoted_person_no2_1 = $data[PROMOTED_PERSON] + 0;
	
				$cmd = " select count(a.PER_ID) as PROMOTED_PERSON from PER_PERSONAL a, PER_SALPROMOTE b where trim(a.LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(a.LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' and b.SALQ_YEAR='$search_budget_year' and b.SALQ_TYPE=2 and b.SALP_YN=1 and b.SALP_LEVEL=1.5 and a.PER_ID=b.PER_ID ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
				$data = $db_dpis->get_array();
				$promoted_person_no2_2 = $data[PROMOTED_PERSON] + 0;
	
				$cmd = " select count(a.PER_ID) as PROMOTED_PERSON from PER_PERSONAL a, PER_SALPROMOTE b where trim(a.LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(a.LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' and b.SALQ_YEAR='$search_budget_year' and b.SALQ_TYPE=2 and b.SALP_YN=1 and b.SALP_PERCENT=4 and a.PER_ID=b.PER_ID ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
				$data = $db_dpis->get_array();
				$promoted_person_no2_3 = $data[PROMOTED_PERSON] + 0;
	
				$cmd = " select count(a.PER_ID) as PROMOTED_PERSON from PER_PERSONAL a, PER_SALPROMOTE b where trim(a.LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(a.LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' and b.SALQ_YEAR='$search_budget_year' and b.SALP_YN=1 and a.PER_ID=b.PER_ID having sum(b.SALP_LEVEL) >= 2 ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
				$data = $db_dpis->get_array();
				$promoted_person_all = $data[PROMOTED_PERSON] + 0;

			}elseif($DPISDB=="oci8"){

				$cmd = " select count(PER_ID) as TOTAL_PERSON from PER_PERSONAL where PER_TYPE = 1 and PER_STATUS = 1 and SUBSTR(trim(PER_STARTDATE), 1, 10) < '". ($search_budget_year - 543 - 1) ."-03-01' and trim(LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
//				echo "$cmd <br>";
				$data = $db_dpis->get_array();
				$total_person = $data[TOTAL_PERSON] + 0;
				$percent_person = ($total_person * 15) / 100;
					
				$cmd = " select sum(PER_SALARY) as TOTAL_SALARY from PER_PERSONAL where PER_TYPE = 1 and PER_STATUS = 1 and SUBSTR(trim(PER_STARTDATE), 1, 10) < '". ($search_budget_year - 543 - 1) ."-03-01' and trim(LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' ";
				$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
//				echo "$cmd <br>";
				$data = $db_dpis->get_array();
				$total_salary = $data[TOTAL_SALARY] + 0;
				$percent_salary = ($total_salary * 6) / 100;

				$cmd = " select count(a.PER_ID) as PROMOTED_PERSON from PER_PERSONAL a, PER_SALPROMOTE b where trim(a.LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(a.LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' and b.SALQ_YEAR='$search_budget_year' and b.SALQ_TYPE=1 and b.SALP_YN=1 and b.SALP_LEVEL=1 and a.PER_ID=b.PER_ID ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
//				echo "$cmd <br>";
				$data = $db_dpis->get_array();
				$promoted_person_no1_1 = $data[PROMOTED_PERSON] + 0;
	
				$cmd = " select count(a.PER_ID) as PROMOTED_PERSON from PER_PERSONAL a, PER_SALPROMOTE b where trim(a.LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(a.LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' and b.SALQ_YEAR='$search_budget_year' and b.SALQ_TYPE=1 and b.SALP_YN=1 and b.SALP_PERCENT=4 and a.PER_ID=b.PER_ID ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
//				echo "$cmd <br>";
				$data = $db_dpis->get_array();
				$promoted_person_no1_2 = $data[PROMOTED_PERSON] + 0;
	
				$cmd = " select sum(b.SALP_SALARY_NEW - b.SALP_SALARY_OLD) as PROMOTED_SALARY from PER_PERSONAL a, PER_SALPROMOTE b where trim(a.LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(a.LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' and b.SALQ_YEAR='$search_budget_year' and b.SALQ_TYPE=1 and b.SALP_YN=1 and a.PER_ID=b.PER_ID ";
				$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
//				echo "$cmd <br>";
				$data = $db_dpis->get_array();
				$promoted_salary = $data[PROMOTED_SALARY] + 0;					
				$remain_salary = $percent_salary - $promoted_salary;
						
				$cmd = " select count(a.PER_ID) as PROMOTED_PERSON from PER_PERSONAL a, PER_SALPROMOTE b where trim(a.LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(a.LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' and b.SALQ_YEAR='$search_budget_year' and b.SALQ_TYPE=2 and b.SALP_YN=1 and b.SALP_LEVEL=1 and a.PER_ID=b.PER_ID ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
//				echo "$cmd <br>";
				$data = $db_dpis->get_array();
				$promoted_person_no2_1 = $data[PROMOTED_PERSON] + 0;
	
				$cmd = " select count(a.PER_ID) as PROMOTED_PERSON from PER_PERSONAL a, PER_SALPROMOTE b where trim(a.LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(a.LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' and b.SALQ_YEAR='$search_budget_year' and b.SALQ_TYPE=2 and b.SALP_YN=1 and b.SALP_LEVEL=1.5 and a.PER_ID=b.PER_ID ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
//				echo "$cmd <br>";
				$data = $db_dpis->get_array();
				$promoted_person_no2_2 = $data[PROMOTED_PERSON] + 0;
	
				$cmd = " select count(a.PER_ID) as PROMOTED_PERSON from PER_PERSONAL a, PER_SALPROMOTE b where trim(a.LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(a.LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' and b.SALQ_YEAR='$search_budget_year' and b.SALQ_TYPE=2 and b.SALP_YN=1 and b.SALP_PERCENT=4 and a.PER_ID=b.PER_ID ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
//				echo "$cmd <br>";
				$data = $db_dpis->get_array();
				$promoted_person_no2_3 = $data[PROMOTED_PERSON] + 0;
	
				$cmd = " select count(a.PER_ID) as PROMOTED_PERSON from PER_PERSONAL a, PER_SALPROMOTE b where trim(a.LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(a.LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' and b.SALQ_YEAR='$search_budget_year' and b.SALP_YN=1 and a.PER_ID=b.PER_ID having sum(b.SALP_LEVEL) >= 2 ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
//				echo "$cmd <br>";
				$data = $db_dpis->get_array();
				$promoted_person_all = $data[PROMOTED_PERSON] + 0;

			}elseif($DPISDB=="mysql"){

				$cmd = " select count(PER_ID) as TOTAL_PERSON from PER_PERSONAL where PER_TYPE = 1 and PER_STATUS = 1 and LEFT(trim(PER_STARTDATE), 10) < '". ($search_budget_year - 543 - 1) ."-03-01' and trim(LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
				$data = $db_dpis->get_array();
				$total_person = $data[TOTAL_PERSON] + 0;
				$percent_person = ($total_person * 15) / 100;
					
				$cmd = " select sum(PER_SALARY) as TOTAL_SALARY from PER_PERSONAL where PER_TYPE = 1 and PER_STATUS = 1 and LEFT(trim(PER_STARTDATE), 10) < '". ($search_budget_year - 543 - 1) ."-09-01' and trim(LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
				$data = $db_dpis->get_array();
				$total_salary = $data[TOTAL_SALARY] + 0;
				$percent_salary = ($total_salary * 6) / 100;

				$cmd = " select count(a.PER_ID) as PROMOTED_PERSON from PER_PERSONAL a, PER_SALPROMOTE b where trim(a.LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(a.LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' and b.SALQ_YEAR='$search_budget_year' and b.SALQ_TYPE=1 and b.SALP_YN=1 and b.SALP_LEVEL=1 and a.PER_ID=b.PER_ID ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
				$data = $db_dpis->get_array();
				$promoted_person_no1_1 = $data[PROMOTED_PERSON] + 0;
	
				$cmd = " select count(a.PER_ID) as PROMOTED_PERSON from PER_PERSONAL a, PER_SALPROMOTE b where trim(a.LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(a.LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' and b.SALQ_YEAR='$search_budget_year' and b.SALQ_TYPE=1 and b.SALP_YN=1 and b.SALP_PERCENT=4 and a.PER_ID=b.PER_ID ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
				$data = $db_dpis->get_array();
				$promoted_person_no1_2 = $data[PROMOTED_PERSON] + 0;
	
				$cmd = " select sum(b.SALP_SALARY_NEW - b.SALP_SALARY_OLD) as PROMOTED_SALARY from PER_PERSONAL a, PER_SALPROMOTE b where trim(a.LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(a.LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' and b.SALQ_YEAR='$search_budget_year' and b.SALQ_TYPE=1 and b.SALP_YN=1 and a.PER_ID=b.PER_ID ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
				$data = $db_dpis->get_array();
				$promoted_salary = $data[PROMOTED_SALARY] + 0;					
				$remain_salary = $percent_salary - $promoted_salary;
						
				$cmd = " select count(a.PER_ID) as PROMOTED_PERSON from PER_PERSONAL a, PER_SALPROMOTE b where trim(a.LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(a.LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' and b.SALQ_YEAR='$search_budget_year' and b.SALQ_TYPE=2 and b.SALP_YN=1 and b.SALP_LEVEL=1 and a.PER_ID=b.PER_ID ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
				$data = $db_dpis->get_array();
				$promoted_person_no2_1 = $data[PROMOTED_PERSON] + 0;
	
				$cmd = " select count(a.PER_ID) as PROMOTED_PERSON from PER_PERSONAL a, PER_SALPROMOTE b where trim(a.LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(a.LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' and b.SALQ_YEAR='$search_budget_year' and b.SALQ_TYPE=2 and b.SALP_YN=1 and b.SALP_LEVEL=1.5 and a.PER_ID=b.PER_ID ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
				$data = $db_dpis->get_array();
				$promoted_person_no2_2 = $data[PROMOTED_PERSON] + 0;
	
				$cmd = " select count(a.PER_ID) as PROMOTED_PERSON from PER_PERSONAL a, PER_SALPROMOTE b where trim(a.LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(a.LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' and b.SALQ_YEAR='$search_budget_year' and b.SALQ_TYPE=2 and b.SALP_YN=1 and b.SALP_PERCENT=4 and a.PER_ID=b.PER_ID ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
				$data = $db_dpis->get_array();
				$promoted_person_no2_3 = $data[PROMOTED_PERSON] + 0;
	
				$cmd = " select count(a.PER_ID) as PROMOTED_PERSON from PER_PERSONAL a, PER_SALPROMOTE b where trim(a.LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(a.LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' and b.SALQ_YEAR='$search_budget_year' and b.SALP_YN=1 and a.PER_ID=b.PER_ID having sum(b.SALP_LEVEL) >= 2 ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
				$data = $db_dpis->get_array();
				$promoted_person_all = $data[PROMOTED_PERSON] + 0;

			} // end if

			$arr_content[$data_count][type] = "DETAIL";
			$arr_content[$data_count][name] = str_repeat(" ", 5) . "$DEPARTMENT_NAME";
			$arr_content[$data_count][total_person] = number_format($total_person);
			$arr_content[$data_count][percent_person] = number_format($percent_person, 2);
			$arr_content[$data_count][promoted_person_no1_1] = number_format($promoted_person_no1_1);
			$arr_content[$data_count][promoted_person_no1_2] = number_format($promoted_person_no1_2);
			$arr_content[$data_count][total_salary] = number_format($total_salary, 2);
			$arr_content[$data_count][percent_salary] = number_format($percent_salary, 2);
			$arr_content[$data_count][promoted_salary] = number_format($promoted_salary, 2);
			$arr_content[$data_count][remain_salary] = number_format($remain_salary, 2);
			$arr_content[$data_count][promoted_person_no2_1] = number_format($promoted_person_no2_1);
			$arr_content[$data_count][promoted_person_no2_2] = number_format($promoted_person_no2_2);
			$arr_content[$data_count][promoted_person_no2_3] = number_format($promoted_person_no2_3);
			$arr_content[$data_count][promoted_person_all] = number_format($promoted_person_all);
			
			$data_count++;
		} // end if
	} // end for	

//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
	if($count_data){
		$pdf->AutoPageBreak = false;
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$NAME = $arr_content[$data_count][name];
			$TOTAL_PERSON = $arr_content[$data_count][total_person];
			$PERCENT_PERSON = $arr_content[$data_count][percent_person];
			$PROMOTED_PERSON_NO1_1 = $arr_content[$data_count][promoted_person_no1_1];
			$PROMOTED_PERSON_NO1_2 = $arr_content[$data_count][promoted_person_no1_2];
			$TOTAL_SALARY = $arr_content[$data_count][total_salary];
			$PERCENT_SALARY = $arr_content[$data_count][percent_salary];
			$PROMOTED_SALARY = $arr_content[$data_count][promoted_salary];
			$REMAIN_SALARY = $arr_content[$data_count][remain_salary];
			$PROMOTED_PERSON_NO2_1 = $arr_content[$data_count][promoted_person_no2_1];
			$PROMOTED_PERSON_NO2_2 = $arr_content[$data_count][promoted_person_no2_2];
			$PROMOTED_PERSON_NO2_3 = $arr_content[$data_count][promoted_person_no2_3];
			$PROMOTED_PERSON_ALL = $arr_content[$data_count][promoted_person_all];
			if ($BKK_FLAG==1)
				$CHECK_DEPT = "สำนักการคลังตรวจสอบแล้ว";
			else
				$CHECK_DEPT = "กรมบัญชีกลางตรวจสอบแล้ว";
			
			if(($data_count % 2) == 0){
				if($data_count > 0){ 
					$pdf->Line($pdf->x, $pdf->y, ($pdf->x + array_sum($heading_width)), $pdf->y);
					
					$pdf->SetFont($font,'',14);
					$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

					$pdf->Cell(array_sum($heading_width), 7, "", "", 1, 'C', 0);

					$pdf->Cell($heading_width[0], 7, "", "", 0, 'C', 0);
					$pdf->Cell(($heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4]), 7, "ได้ตรวจสอบถูกต้องแล้ว", "", 0, 'C', 0);
					$pdf->Cell(($heading_width[5] + $heading_width[6] + $heading_width[7]), 7, "", "", 0, 'C', 0);
					$pdf->Cell(($heading_width[8] + $heading_width[9] + $heading_width[10] + $heading_width[11] + $heading_width[12] + $heading_width[13]), 7, $CHECK_DEPT, "", 1, 'C', 0);

					$pdf->Cell(array_sum($heading_width), 7, "", "", 1, 'C', 0);
					$pdf->Cell(array_sum($heading_width), 7, "", "", 1, 'C', 0);
					$pdf->Cell(array_sum($heading_width), 7, "", "", 1, 'C', 0);

					$pdf->Cell($heading_width[0], 7, "", "", 0, 'C', 0);
					$pdf->Cell(($heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4]), 7, "( $confirm_name )", "", 0, 'C', 0);
					$pdf->Cell(($heading_width[5] + $heading_width[6] + $heading_width[7]), 7, "", "", 0, 'C', 0);
					$pdf->Cell(($heading_width[8] + $heading_width[9] + $heading_width[10] + $heading_width[11] + $heading_width[12] + $heading_width[13]), 7, "( ............................................................ )", "", 1, 'C', 0);

					$pdf->Cell($heading_width[0], 7, "", "", 0, 'C', 0);
					$pdf->Cell(($heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4]), 7, "ตำแหน่ง  $confirm_position", "", 0, 'C', 0);
					$pdf->Cell(($heading_width[5] + $heading_width[6] + $heading_width[7]), 7, "", "", 0, 'C', 0);
					$pdf->Cell(($heading_width[8] + $heading_width[9] + $heading_width[10] + $heading_width[11] + $heading_width[12] + $heading_width[13]), 7, "ตำแหน่ง ............................................................ ", "", 1, 'C', 0);

					$pdf->Cell($heading_width[0], 7, "", "", 0, 'C', 0);
					$pdf->Cell(($heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4]), 7, "วันที่                         ตุลาคม ". ($search_budget_year - 1), "", 0, 'C', 0);
					$pdf->Cell(($heading_width[5] + $heading_width[6] + $heading_width[7]), 7, "", "", 0, 'C', 0);
					$pdf->Cell(($heading_width[8] + $heading_width[9] + $heading_width[10] + $heading_width[11] + $heading_width[12] + $heading_width[13]), 7, "วันที่ ............................................. ", "", 1, 'C', 0);

					$pdf->AddPage();
				} // end if
				if($data_count == 0) 			print_header("O1", "O3");		//print_header(1, 8);
				elseif($data_count == 2) 	print_header("K1", "K3");		//print_header(9, 11);
			} // end if
			
			$border = "";
			if($REPORT_ORDER == "DETAIL") $pdf->SetFont($font,'',14);
			else $pdf->SetFont($fontb,'',14);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

			$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;

			$pdf->MultiCell($heading_width[0], 7, "$NAME", $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0];
			$pdf->y = $start_y;
			$pdf->Cell($heading_width[1], 7, "$TOTAL_PERSON", $border, 0, 'R', 0);
			$pdf->Cell($heading_width[2], 7, "$PERCENT_PERSON", $border, 0, 'R', 0);
			$pdf->Cell($heading_width[3], 7, "$PROMOTED_PERSON_NO1_1", $border, 0, 'R', 0);
			$pdf->Cell($heading_width[4], 7, "$PROMOTED_PERSON_NO1_2", $border, 0, 'R', 0);
			$pdf->Cell($heading_width[5], 7, "$TOTAL_SALARY", $border, 0, 'R', 0);
			$pdf->Cell($heading_width[6], 7, "$PERCENT_SALARY", $border, 0, 'R', 0);
			$pdf->Cell($heading_width[7], 7, "$PROMOTED_SALARY", $border, 0, 'R', 0);
			$pdf->Cell($heading_width[8], 7, "$REMAIN_SALARY", $border, 0, 'R', 0);
			$pdf->Cell($heading_width[9], 7, "$PERCENT_PERSON", $border, 0, 'R', 0);
			$pdf->Cell($heading_width[10], 7, "$PROMOTED_PERSON_NO2_1", $border, 0, 'R', 0);
			$pdf->Cell($heading_width[11], 7, "$PROMOTED_PERSON_NO2_2", $border, 0, 'R', 0);
			$pdf->Cell($heading_width[12], 7, "$PROMOTED_PERSON_NO2_3", $border, 0, 'R', 0);
			$pdf->Cell($heading_width[13], 7, "$PROMOTED_PERSON_ALL", $border, 0, 'R', 0);

			//================= Draw Border Line ====================
			$line_start_y = $start_y;		$line_start_x = $start_x;
			$line_end_y = $max_y;		$line_end_x = $start_x;
			$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
			
			for($i=0; $i<=13; $i++){
				$line_start_y = $start_y;		$line_start_x += $heading_width[$i];
				$line_end_y = $max_y;		$line_end_x += $heading_width[$i];
				$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
			} // end for
			//====================================================

			$pdf->x = $start_x;			$pdf->y = $max_y;
		} // end for
		
		if($data_count > 0){
			$pdf->Line($pdf->x, $pdf->y, ($pdf->x + array_sum($heading_width)), $pdf->y);
				
			$pdf->SetFont($font,'',14);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

			$pdf->Cell(array_sum($heading_width), 7, "", "", 1, 'C', 0);

			$pdf->Cell($heading_width[0], 7, "", "", 0, 'C', 0);
			$pdf->Cell(($heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4]), 7, "ได้ตรวจสอบถูกต้องแล้ว", "", 0, 'C', 0);
			$pdf->Cell(($heading_width[5] + $heading_width[6] + $heading_width[7]), 7, "", "", 0, 'C', 0);
			$pdf->Cell(($heading_width[8] + $heading_width[9] + $heading_width[10] + $heading_width[11] + $heading_width[12] + $heading_width[13]), 7, $CHECK_DEPT, "", 1, 'C', 0);

			$pdf->Cell(array_sum($heading_width), 7, "", "", 1, 'C', 0);
			$pdf->Cell(array_sum($heading_width), 7, "", "", 1, 'C', 0);
			$pdf->Cell(array_sum($heading_width), 7, "", "", 1, 'C', 0);

			$pdf->Cell($heading_width[0], 7, "", "", 0, 'C', 0);
			$pdf->Cell(($heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4]), 7, "( $confirm_name )", "", 0, 'C', 0);
			$pdf->Cell(($heading_width[5] + $heading_width[6] + $heading_width[7]), 7, "", "", 0, 'C', 0);
			$pdf->Cell(($heading_width[8] + $heading_width[9] + $heading_width[10] + $heading_width[11] + $heading_width[12] + $heading_width[13]), 7, "( ............................................................ )", "", 1, 'C', 0);

			$pdf->Cell($heading_width[0], 7, "", "", 0, 'C', 0);
			$pdf->Cell(($heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4]), 7, "ตำแหน่ง  $confirm_position", "", 0, 'C', 0);
			$pdf->Cell(($heading_width[5] + $heading_width[6] + $heading_width[7]), 7, "", "", 0, 'C', 0);
			$pdf->Cell(($heading_width[8] + $heading_width[9] + $heading_width[10] + $heading_width[11] + $heading_width[12] + $heading_width[13]), 7, "ตำแหน่ง ............................................................ ", "", 1, 'C', 0);

			$pdf->Cell($heading_width[0], 7, "", "", 0, 'C', 0);
			$pdf->Cell(($heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4]), 7, "วันที่                         ตุลาคม ". ($search_budget_year - 1), "", 0, 'C', 0);
			$pdf->Cell(($heading_width[5] + $heading_width[6] + $heading_width[7]), 7, "", "", 0, 'C', 0);
			$pdf->Cell(($heading_width[8] + $heading_width[9] + $heading_width[10] + $heading_width[11] + $heading_width[12] + $heading_width[13]), 7, "วันที่ ............................................. ", "", 1, 'C', 0);
		} // end if
		
	}else{
		$pdf->SetFont($fontb,'',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(287,10,"********** ไม่มีข้อมูล **********",0,1,'C');
	} // end if

	$pdf->close();
	$pdf->Output();	
?>