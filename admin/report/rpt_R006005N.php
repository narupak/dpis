<?
	include("../../php_scripts/connect_database.php");
	if (!$FLAG_RTF) include("../php_scripts/pdf_wordarray_thaicut.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	if ($FLAG_RTF) {
		include ("rtf_setvar.php");	// กำหนดตัวแปรค่าสี set_of_colors
		require("../../RTF/rtf_class.php");
	} else	 {
		define('FPDF_FONTPATH','../../PDF/font/');
		include ("../../PDF/fpdf.php");
		include ("../../PDF/pdf_extends_DPIS.php");
	}

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	if(!trim($RPTORD_LIST)){ 
		if($select_org_structure==1){	//สำหรับมอบหมายงาน
			if(trim($search_org_ass_id)){	$search_org_id=trim($search_org_ass_id);		}	
			if(trim($search_org_ass_id_1)){	$search_org_id_1=trim($search_org_ass_id_1);		}
			if(trim($search_org_ass_id_2)){	$search_org_id_2=trim($search_org_ass_id_2);		}
		}
		$RPTORD_LIST = "ORG|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)=="" && trim($search_org_id_2)=="") $RPTORD_LIST .= "ORG_1|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)!="" && trim($search_org_id_2)=="") $RPTORD_LIST .= "ORG_2|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)!="" && trim($search_org_id_2)!="") $RPTORD_LIST .= "LINE|";
	} // end if
	$arr_rpt_order = explode("|", $RPTORD_LIST);
//	$arr_rpt_order = array("ORG"); 

	$search_per_type = 1;
	$search_per_status = 1;
	
	$search_condition = "";
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

	$company_name = "";
	$report_title = "บัญชีสรุปจำนวนข้าราชการและอัตราเงินเดือนรวมของข้าราชการ||สำหรับการเลื่อนขั้นเงินเดือน ประจำปีงบประมาณ $search_budget_year||$DEPARTMENT_NAME $MINISTRY_NAME";
	$report_code = "R0605";
	include ("rpt_R006005_format.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน
//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();

	if ($FLAG_RTF) {
//		$sum_w = array_sum($heading_width);
		$sum_w = 0;
		for($h = 0; $h < count($heading_width); $h++) {
			if ($arr_column_sel[$h]==1)
				$sum_w += $heading_width[$h];
		}
		for($h = 0; $h < count($heading_width); $h++) {
			if ($arr_column_sel[$h]==1)
			$heading_width[$h] = $heading_width[$h] / $sum_w * 100;
		}

		$fname= "rpt_R006005_rtf.rtf";

	//	RTF $papersize = "a4", $margl = 720, $margr = 720, $margt = 720, $margb = 720, $orient = "p", $u_color_tab="";
		$paper_size="a4";
		$orientation='L';
		$RTF = new RTF($paper_size, ".7in", ".3in", ".5in", ".1in", $orientation, $set_of_colors);

		$RTF->set_default_font($font, 14);
	//	echo "default font_id::".$RTF->dfl_FontID."<br>";	

		$RTF->set_report_code($report_code);
		$RTF->set_report_title($report_title);
		$RTF->set_company_name($company_name);
	} else {
		$unit="mm";
		$paper_size="A4";
		$lang_code="TH";
		$orientation='L';

		$pdf = new PDF($orientation,$unit,$paper_size,$lang_code,$company_name,$report_title,$report_code,$heading,$heading_width,$heading_align);
		
		$pdf->Open();
		$pdf->SetMargins(5,5,5);
		$pdf->AliasNbPages();
		$pdf->AddPage();
		$pdf->SetTextColor(0, 0, 0);
		$pdf->SetFont($font,'',14);
		
		$page_start_x = $pdf->x;			$page_start_y = $pdf->y;
	}

	function print_header($search_level_min, $search_level_max){
		global $pdf, $heading_width, $heading_name;
		global $search_budget_year;
		
		$pdf->SetFont($font,'b',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

		$pdf->Cell(($heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6] + $heading_width[7] + $heading_width[8]) ,7,"",'',0,'C',0);
		$pdf->Cell(($heading_width[9] + $heading_width[10] + $heading_width[11] + $heading_width[12] + $heading_width[13]) ,7,"กลุ่มข้าราชการระดับ $search_level_min - $search_level_max",'',1,'C',0);

		$pdf->print_tab_header();
	} // function		
	
	$cmd = " select PER_ID from PER_SALARYHIS where SAH_KF_YEAR='$search_budget_year' and SAH_KF_CYCLE in (1,2) ";
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
				$search_level_min = "01";			//1
				$search_level_max = "08";			//8
			}elseif($i==3){
				$search_level_min = "09";			//9
				$search_level_max = "11";		//11
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
		$head_text1 = implode(",", $heading_text);
		$head_width1 = implode(",", $heading_width);
		$head_align1 = implode(",", $heading_align);
		$col_function = implode(",", $column_function);
		if ($FLAG_RTF) {
			$RTF->add_header("", 0, false);	// header default
			$RTF->add_footer("", 0, false);		// footer default
			
	//		echo "$head_text1<br>";
			$tab_align = "center";
			$result = $RTF->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", $fmtTableHeader_col_idx, $fmtTableHeader_bgcol_idx, $COLUMN_FORMAT, $col_function, false, $tab_align);
		} else {
			$pdf->AutoPageBreak = false; 
	//		echo "$head_text1<br>";
			$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", "0066CC", "EEEEFF", $COLUMN_FORMAT, $col_function, true); 
		}
		// not print first head
		if (!$result) echo "****** error ****** on open table for $table<br>";
		
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

					$arr_data = (array) null;
					$arr_data[] = "";
					$arr_data[] = "<**1**>ได้ตรวจสอบถูกต้องแล้ว";
					$arr_data[] = "<**1**>ได้ตรวจสอบถูกต้องแล้ว";
					$arr_data[] = "<**1**>ได้ตรวจสอบถูกต้องแล้ว";
					$arr_data[] = "<**1**>ได้ตรวจสอบถูกต้องแล้ว";
					$arr_data[] = "";
					$arr_data[] = "";
					$arr_data[] = "";
					$arr_data[] = "<**2**>".$CHECK_DEPT;
					$arr_data[] = "<**2**>".$CHECK_DEPT;
					$arr_data[] = "<**2**>".$CHECK_DEPT;
					$arr_data[] = "<**2**>".$CHECK_DEPT;
					$arr_data[] = "<**2**>".$CHECK_DEPT;
					$arr_data[] = "<**2**>".$CHECK_DEPT;
			
					if ($FLAG_RTF)
						$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
					else
						$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "14", "", "000000", "");		//TRHBL
					if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
					if ($FLAG_RTF)
						$result = $RTF->add_text_line("", 7, "", "C", "", "14", "", 0, 0);
					else
						$result = $pdf->add_text_line("", 7, "", "C", "", "14", "", 0, 0);
					if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";
					if ($FLAG_RTF)
						$result = $RTF->add_text_line("", 7, "", "C", "", "14", "", 0, 0);
					else
						$result = $pdf->add_text_line("", 7, "", "C", "", "14", "", 0, 0);
					if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";
					if ($FLAG_RTF)
						$result = $RTF->add_text_line("", 7, "", "C", "", "14", "", 0, 0);
					else
						$result = $pdf->add_text_line("", 7, "", "C", "", "14", "", 0, 0);
					if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";

					$arr_data = (array) null;
					$arr_data[] = "";
					$arr_data[] = "<**1**>( $confirm_name )";
					$arr_data[] = "<**1**>( $confirm_name )";
					$arr_data[] = "<**1**>( $confirm_name )";
					$arr_data[] = "<**1**>( $confirm_name )";
					$arr_data[] = "";
					$arr_data[] = "";
					$arr_data[] = "";
					$arr_data[] = "<**2**>( ............................................................ )";
					$arr_data[] = "<**2**>( ............................................................ )";
					$arr_data[] = "<**2**>( ............................................................ )";
					$arr_data[] = "<**2**>( ............................................................ )";
					$arr_data[] = "<**2**>( ............................................................ )";
					$arr_data[] = "<**2**>( ............................................................ )";
			
					if ($FLAG_RTF)
						$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
					else
						$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "14", "", "000000", "");		//TRHBL
					if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";

					$arr_data = (array) null;
					$arr_data[] = "";
					$arr_data[] = "<**1**>ตำแหน่ง  $confirm_position";
					$arr_data[] = "<**1**>ตำแหน่ง  $confirm_position";
					$arr_data[] = "<**1**>ตำแหน่ง  $confirm_position";
					$arr_data[] = "<**1**>ตำแหน่ง  $confirm_position";
					$arr_data[] = "";
					$arr_data[] = "";
					$arr_data[] = "";
					$arr_data[] = "<**2**>ตำแหน่ง ............................................................ ";
					$arr_data[] = "<**2**>ตำแหน่ง ............................................................ ";
					$arr_data[] = "<**2**>ตำแหน่ง ............................................................ ";
					$arr_data[] = "<**2**>ตำแหน่ง ............................................................ ";
					$arr_data[] = "<**2**>ตำแหน่ง ............................................................ ";
					$arr_data[] = "<**2**>ตำแหน่ง ............................................................ ";
			
					if ($FLAG_RTF)
						$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
					else
						$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "14", "", "000000", "");		//TRHBL
					if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";

					$arr_data = (array) null;
					$arr_data[] = "";
					$arr_data[] = "<**1**>วันที่                         ตุลาคม ". ($search_budget_year - 1);
					$arr_data[] = "<**1**>วันที่                         ตุลาคม ". ($search_budget_year - 1);
					$arr_data[] = "<**1**>วันที่                         ตุลาคม ". ($search_budget_year - 1);
					$arr_data[] = "<**1**>วันที่                         ตุลาคม ". ($search_budget_year - 1);
					$arr_data[] = "";
					$arr_data[] = "";
					$arr_data[] = "";
					$arr_data[] = "<**2**>วันที่ ............................................. ";
					$arr_data[] = "<**2**>วันที่ ............................................. ";
					$arr_data[] = "<**2**>วันที่ ............................................. ";
					$arr_data[] = "<**2**>วันที่ ............................................. ";
					$arr_data[] = "<**2**>วันที่ ............................................. ";
					$arr_data[] = "<**2**>วันที่ ............................................. ";
			
					if ($FLAG_RTF)
						$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
					else
						$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "14", "", "000000", "");		//TRHBL
					if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
					if (!$FLAG_RTF) $pdf->AddPage();
				} // end if
				if (!$FLAG_RTF) {
					if($data_count == 0) 			print_header("O1", "O3");		//print_header(1, 8);
					elseif($data_count == 2) 	print_header("K1", "K3");		//print_header(9, 11);
				}
			} // end if

			$arr_data = (array) null;
			$arr_data[] = $NAME;
			$arr_data[] = $TOTAL_PERSON;
			$arr_data[] = $PERCENT_PERSON;
			$arr_data[] = $PROMOTED_PERSON_NO1_1;
			$arr_data[] = $PROMOTED_PERSON_NO1_2;
			$arr_data[] = $TOTAL_SALARY;
			$arr_data[] = $PERCENT_SALARY;
			$arr_data[] = $PROMOTED_SALARY;
			$arr_data[] = $REMAIN_SALARY;
			$arr_data[] = $PERCENT_PERSON;
			$arr_data[] = $PROMOTED_PERSON_NO2_1;
			$arr_data[] = $PROMOTED_PERSON_NO2_2;
			$arr_data[] = $PROMOTED_PERSON_NO2_3;
			$arr_data[] = $PROMOTED_PERSON_ALL;
	
			if($REPORT_ORDER == "DETAIL") {
				if ($FLAG_RTF)
					$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
				else
					$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "14", "", "000000", "");		//TRHBL
			} else {
				if ($FLAG_RTF)
					$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
				else
					$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "14", "b", "000000", "");		//TRHBL
			}
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
		} // end for
		
		if($data_count > 0){

			$arr_data = (array) null;
			$arr_data[] = "";
			$arr_data[] = "<**1**>ได้ตรวจสอบถูกต้องแล้ว";
			$arr_data[] = "<**1**>ได้ตรวจสอบถูกต้องแล้ว";
			$arr_data[] = "<**1**>ได้ตรวจสอบถูกต้องแล้ว";
			$arr_data[] = "<**1**>ได้ตรวจสอบถูกต้องแล้ว";
			$arr_data[] = "";
			$arr_data[] = "";
			$arr_data[] = "";
			$arr_data[] = "<**2**>".$CHECK_DEPT;
			$arr_data[] = "<**2**>".$CHECK_DEPT;
			$arr_data[] = "<**2**>".$CHECK_DEPT;
			$arr_data[] = "<**2**>".$CHECK_DEPT;
			$arr_data[] = "<**2**>".$CHECK_DEPT;
			$arr_data[] = "<**2**>".$CHECK_DEPT;
	
			if ($FLAG_RTF)
				$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
			else
				$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "14", "", "000000", "");		//TRHBL
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
			if ($FLAG_RTF)
				$result = $RTF->add_text_line("", 7, "", "C", "", "14", "", 0, 0);
			else
				$result = $pdf->add_text_line("", 7, "", "C", "", "14", "", 0, 0);
			if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";
			if ($FLAG_RTF)
				$result = $RTF->add_text_line("", 7, "", "C", "", "14", "", 0, 0);
			else
				$result = $pdf->add_text_line("", 7, "", "C", "", "14", "", 0, 0);
			if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";
			if ($FLAG_RTF)
				$result = $RTF->add_text_line("", 7, "", "C", "", "14", "", 0, 0);
			else
				$result = $pdf->add_text_line("", 7, "", "C", "", "14", "", 0, 0);
			if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";

			$arr_data = (array) null;
			$arr_data[] = "";
			$arr_data[] = "<**1**>( $confirm_name )";
			$arr_data[] = "<**1**>( $confirm_name )";
			$arr_data[] = "<**1**>( $confirm_name )";
			$arr_data[] = "<**1**>( $confirm_name )";
			$arr_data[] = "";
			$arr_data[] = "";
			$arr_data[] = "";
			$arr_data[] = "<**2**>( ............................................................ )";
			$arr_data[] = "<**2**>( ............................................................ )";
			$arr_data[] = "<**2**>( ............................................................ )";
			$arr_data[] = "<**2**>( ............................................................ )";
			$arr_data[] = "<**2**>( ............................................................ )";
			$arr_data[] = "<**2**>( ............................................................ )";
	
			if ($FLAG_RTF)
				$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
			else
				$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "14", "", "000000", "");		//TRHBL
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";

			$arr_data = (array) null;
			$arr_data[] = "";
			$arr_data[] = "<**1**>ตำแหน่ง  $confirm_position";
			$arr_data[] = "<**1**>ตำแหน่ง  $confirm_position";
			$arr_data[] = "<**1**>ตำแหน่ง  $confirm_position";
			$arr_data[] = "<**1**>ตำแหน่ง  $confirm_position";
			$arr_data[] = "";
			$arr_data[] = "";
			$arr_data[] = "";
			$arr_data[] = "<**2**>ตำแหน่ง ............................................................ ";
			$arr_data[] = "<**2**>ตำแหน่ง ............................................................ ";
			$arr_data[] = "<**2**>ตำแหน่ง ............................................................ ";
			$arr_data[] = "<**2**>ตำแหน่ง ............................................................ ";
			$arr_data[] = "<**2**>ตำแหน่ง ............................................................ ";
			$arr_data[] = "<**2**>ตำแหน่ง ............................................................ ";
	
			if ($FLAG_RTF)
				$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
			else
				$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "14", "", "000000", "");		//TRHBL
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";

			$arr_data = (array) null;
			$arr_data[] = "";
			$arr_data[] = "<**1**>วันที่                         ตุลาคม ". ($search_budget_year - 1);
			$arr_data[] = "<**1**>วันที่                         ตุลาคม ". ($search_budget_year - 1);
			$arr_data[] = "<**1**>วันที่                         ตุลาคม ". ($search_budget_year - 1);
			$arr_data[] = "<**1**>วันที่                         ตุลาคม ". ($search_budget_year - 1);
			$arr_data[] = "";
			$arr_data[] = "";
			$arr_data[] = "";
			$arr_data[] = "<**2**>วันที่ ............................................. ";
			$arr_data[] = "<**2**>วันที่ ............................................. ";
			$arr_data[] = "<**2**>วันที่ ............................................. ";
			$arr_data[] = "<**2**>วันที่ ............................................. ";
			$arr_data[] = "<**2**>วันที่ ............................................. ";
			$arr_data[] = "<**2**>วันที่ ............................................. ";
	
			if ($FLAG_RTF)
				$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
			else
				$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "14", "", "000000", "");		//TRHBL
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
		} // end if
		
	}else{
		if ($FLAG_RTF)
			$result = $RTF->add_text_line("********** ไม่มีข้อมูล **********", 7, "", "C", "", "14", "b", 0, 0);
		else
			$result = $pdf->add_text_line("********** ไม่มีข้อมูล **********", 7, "", "L", "", "14", "b", 0, 0);
		if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";
	} // end if
	if ($FLAG_RTF) {
		$RTF->close_tab(); 
//			$RTF->close_section(); 

		$RTF->display($fname);
	} else {
		$pdf->close_tab(""); 
	
		$pdf->close();
		$pdf->Output();	
	}
?>