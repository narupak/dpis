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

	$search_per_status[] = 1;
	
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
	if(count($arr_search_condition)) $search_condition = " and ". implode(" and ", $arr_search_condition);

	$company_name = "";
	$report_title = "บัญชีสรุปการใช้เงินเลื่อนขั้นเงินเดือนข้าราชการ||สำหรับการเลื่อนขั้นเงินเดือน วันที่ 1 เมษายน และวันที่ 1 ตุลาคม ". ($search_budget_year - 1) ."||$DEPARTMENT_NAME $MINISTRY_NAME||ประจำปีงบประมาณ พ.ศ.$search_budget_year";
	$report_code = "R0607";
	include ("rpt_R006007_format.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน
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

		$fname= "rpt_R006007_rtf.rtf";

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
		global $pdf, $RTF, $FLAG_RTF, $heading_width, $heading_name;

		if ($FLAG_RTF) {
			$result = $RTF->add_text_line("กลุ่มข้าราชการระดับ $search_level_min - $search_level_max", 7, "", "C", "", "14", "b", 0, 0);
			if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";
			
			$RTF->print_tab_header();
		} else {
			$result = $pdf->add_text_line("กลุ่มข้าราชการระดับ $search_level_min - $search_level_max", 7, "", "C", "", "14", "b", 0, 0);
			if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";
			
			$pdf->print_tab_header();
		}
	} // function		

	$cmd = " select distinct a.PER_ID from PER_PERSONAL a, PER_SALARYHIS b 
					where a.PER_ID=b.PER_ID and SAH_KF_YEAR='$search_budget_year' $search_condition ";
	$count_data = $db_dpis->send_cmd($cmd);
	
	$data_count = 0;
	for($i=0; $i<4; $i++){
		if($i == 0 || ($i % 2) == 0){
			$arr_content[$data_count][type] = "HEADER";
			if ($BKK_FLAG==1)
				$arr_content[$data_count][name] = "กรุงเทพมหานคร";
			else
				$arr_content[$data_count][name] = "ราชการบริหารส่วนกลาง";
			
			if($i == 0) $arr_content[$data_count][remark_2] = $remark_1?"$remark_1":"หมายเหตุ O4-K5";
			else $arr_content[$data_count][remark_2] = $remark_2?"$remark_2":"หมายเหตุ D1-M2";
			
			$data_count++;
		}else{
			if($i==1){
				$search_level_min = "O4";
				$search_level_max = "K5";
			}elseif($i==3){
				$search_level_min = "D1";
				$search_level_max = "M2";
			} // end if
					
			if($DPISDB=="odbc"){ 
				$cmd = " select sum(PER_SALARY) as TOTAL_SALARY from PER_PERSONAL where PER_TYPE = 1 and PER_STATUS = 1 and LEFT(trim(PER_STARTDATE), 10) < '". ($search_budget_year - 543 - 1) ."-09-01' and trim(LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
				$data = $db_dpis->get_array();
				$total_salary = $data[TOTAL_SALARY] + 0;
				$percent_salary = ($total_salary * 6) / 100;

				$cmd = " select sum(b.SALP_SALARY_NEW - b.SALP_SALARY_OLD) as PROMOTED_SALARY from PER_PERSONAL a, PER_SALPROMOTE b where trim(a.LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(a.LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' and b.SALQ_YEAR='$search_budget_year' and b.SALP_YN=1 and (b.SALP_LEVEL=0.5 or b.SALP_LEVEL=1 or b.SALP_LEVEL=1.5) and a.PER_ID=b.PER_ID ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
				$data = $db_dpis->get_array();
				$promoted_salary_1 = $data[PROMOTED_SALARY] + 0;
	
				$cmd = " select sum(b.SALP_SALARY_NEW - b.SALP_SALARY_OLD) as PROMOTED_SALARY from PER_PERSONAL a, PER_SALPROMOTE b where trim(a.LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(a.LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' and b.SALQ_YEAR='$search_budget_year' and b.SALQ_TYPE=2 and b.SALP_YN=1 and (b.SALP_PERCENT=2 or b.SALP_PERCENT=4) and a.PER_ID=b.PER_ID ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
				$data = $db_dpis->get_array();
				$promoted_salary_2 = $data[PROMOTED_SALARY] + 0;
				
				$total_promoted_salary = $promoted_salary_1 + $promoted_salary_2;
	
			}elseif($DPISDB=="oci8"){

				$cmd = " select sum(PER_SALARY) as TOTAL_SALARY from PER_PERSONAL where PER_TYPE = 1 and PER_STATUS = 1 and SUBSTR(trim(PER_STARTDATE), 1, 10) < '". ($search_budget_year - 543 - 1) ."-03-01' and trim(LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
//				echo "$cmd <br>";
				$data = $db_dpis->get_array();
				$total_salary = $data[TOTAL_SALARY] + 0;
				$percent_salary = ($total_salary * 6) / 100;

				$cmd = " select sum(b.SALP_SALARY_NEW - b.SALP_SALARY_OLD) as PROMOTED_SALARY from PER_PERSONAL a, PER_SALPROMOTE b where trim(a.LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(a.LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' and b.SALQ_YEAR='$search_budget_year' and b.SALP_YN=1 and (b.SALP_LEVEL=0.5 or b.SALP_LEVEL=1 or b.SALP_LEVEL=1.5) and a.PER_ID=b.PER_ID ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
//				echo "$cmd <br>";
				$data = $db_dpis->get_array();
				$promoted_salary_1 = $data[PROMOTED_SALARY] + 0;
	
				$cmd = " select sum(b.SALP_SALARY_NEW - b.SALP_SALARY_OLD) as PROMOTED_SALARY from PER_PERSONAL a, PER_SALPROMOTE b where trim(a.LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(a.LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' and b.SALQ_YEAR='$search_budget_year' and b.SALQ_TYPE=2 and b.SALP_YN=1 and (b.SALP_PERCENT=2 and b.SALP_PERCENT=4) and a.PER_ID=b.PER_ID ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
//				echo "$cmd <br>";
				$data = $db_dpis->get_array();
				$promoted_salary_2 = $data[PROMOTED_SALARY] + 0;
				
				$total_promoted_salary = $promoted_salary_1 + $promoted_salary_2;

			}elseif($DPISDB=="mysql"){

				$cmd = " select sum(PER_SALARY) as TOTAL_SALARY from PER_PERSONAL where PER_TYPE = 1 and PER_STATUS = 1 and LEFT(trim(PER_STARTDATE), 10) < '". ($search_budget_year - 543 - 1) ."-09-01' and trim(LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
				$data = $db_dpis->get_array();
				$total_salary = $data[TOTAL_SALARY] + 0;
				$percent_salary = ($total_salary * 6) / 100;

				$cmd = " select sum(b.SALP_SALARY_NEW - b.SALP_SALARY_OLD) as PROMOTED_SALARY from PER_PERSONAL a, PER_SALPROMOTE b where trim(a.LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(a.LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' and b.SALQ_YEAR='$search_budget_year' and b.SALP_YN=1 and (b.SALP_LEVEL=0.5 or b.SALP_LEVEL=1 or b.SALP_LEVEL=1.5) and a.PER_ID=b.PER_ID ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
				$data = $db_dpis->get_array();
				$promoted_salary_1 = $data[PROMOTED_SALARY] + 0;
	
				$cmd = " select sum(b.SALP_SALARY_NEW - b.SALP_SALARY_OLD) as PROMOTED_SALARY from PER_PERSONAL a, PER_SALPROMOTE b where trim(a.LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(a.LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' and b.SALQ_YEAR='$search_budget_year' and b.SALQ_TYPE=2 and b.SALP_YN=1 and (b.SALP_PERCENT=2 or b.SALP_PERCENT=4) and a.PER_ID=b.PER_ID ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
				$data = $db_dpis->get_array();
				$promoted_salary_2 = $data[PROMOTED_SALARY] + 0;
				
				$total_promoted_salary = $promoted_salary_1 + $promoted_salary_2;
	
			} // end if

			$arr_content[$data_count][type] = "DETAIL";
			$arr_content[$data_count][name] = str_repeat(" ", 5) . "$DEPARTMENT_NAME";
			$arr_content[$data_count][percent_salary] = number_format($percent_salary, 2);
			$arr_content[$data_count][promoted_salary_1] = number_format($promoted_salary_1, 2);
			$arr_content[$data_count][promoted_salary_2] = number_format($promoted_salary_2, 2);
			$arr_content[$data_count][total_promoted_salary] = number_format($total_promoted_salary, 2);
			$arr_content[$data_count][remark_1] = "ได้ตรวจสอบถูกต้องแล้ว";
			
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
			$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", "0066CC", "EEEEFF", $COLUMN_FORMAT, $col_function); 
		}
		// not print first head
		if (!$result) echo "****** error ****** on open table for $table<br>";
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){			
			if(($data_count % 2) == 0){
				if($data_count > 0){ 
					$border = "LR";
//					$pdf->SetFont($font,'',14);
//					$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

					for($i=0; $i<3; $i++){
						if ($FLAG_RTF)
							$result = $RTF->add_text_line("", 7, $border, "C", "", "14", "", 0, 0);
						else
							$result = $pdf->add_text_line("", 7, $border, "C", "", "14", "", 0, 0);
						if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";
					} // end if

					if ($FLAG_RTF)
						$result = $RTF->add_text_line("( $confirm_name )", 7, $border, "C", "", "14", "", 0, 0);
					else
						$result = $pdf->add_text_line("( $confirm_name )", 7, $border, "C", "", "14", "", 0, 0);
					if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";

					if ($FLAG_RTF)
						$result = $RTF->add_text_line("ตำแหน่ง  $confirm_position", 7, $border, "C", "", "14", "", 0, 0);
					else
						$result = $pdf->add_text_line("ตำแหน่ง  $confirm_position", 7, $border, "C", "", "14", "", 0, 0);
					if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";

					if ($FLAG_RTF)
						$result = $RTF->add_text_line("ตำแหน่ง  $confirm_position", 7, $border, "C", "", "14", "", 0, 0);
					else
						$result = $pdf->add_text_line("ตำแหน่ง  $confirm_position", 7, $border, "C", "", "14", "", 0, 0);
					if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";

					if ($FLAG_RTF)
						$result = $RTF->add_text_line("วันที่                         ตุลาคม ". ($search_budget_year - 1), 7, $border, "C", "", "14", "", 0, 0);
					else
						$result = $pdf->add_text_line("วันที่                         ตุลาคม ". ($search_budget_year - 1), 7, $border, "C", "", "14", "", 0, 0);
					if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";

					if ($BKK_FLAG==1)
						if ($FLAG_RTF)
							$result = $RTF->add_text_line("สำนักการคลังตรวจสอบแล้ว", 7, $border, "C", "", "14", "", 0, 0);
						else
							$result = $pdf->add_text_line("สำนักการคลังตรวจสอบแล้ว", 7, $border, "C", "", "14", "", 0, 0);
					else
						if ($FLAG_RTF)
							$result = $RTF->add_text_line("กรมบัญชีกลางตรวจสอบแล้ว", 7, $border, "C", "", "14", "", 0, 0);
						else
							$result = $pdf->add_text_line("กรมบัญชีกลางตรวจสอบแล้ว", 7, $border, "C", "", "14", "", 0, 0);
					if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";

					for($i=0; $i<3; $i++){
						if ($FLAG_RTF)
							$result = $RTF->add_text_line("", 7, $border, "C", "", "14", "", 0, 0);
						else
							$result = $pdf->add_text_line("", 7, $border, "C", "", "14", "", 0, 0);
						if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";
					} // end if

					$border = "";
//					$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;

					$arr_data = (array) null;
					$arr_data[] = $REMARK_2;
					$arr_data[] = "";
					$arr_data[] = "";
					$arr_data[] = "";
					$arr_data[] = "";
					$arr_data[] = "( ............................................................ )";
		
					if($REPORT_ORDER == "DETAIL")
						if ($FLAG_RTF)
							$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
						else
							$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "14", "", "000000", "");		//TRHBL
					else
						if ($FLAG_RTF)
							$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
						else
							$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "14", "b", "000000", "");		//TRHBL
					if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";

					if ($FLAG_RTF)
						$result = $RTF->add_text_line("ตำแหน่ง ............................................................ ", 7, $border, "C", "", "14", "", 0, 0);
					else
						$result = $pdf->add_text_line("ตำแหน่ง ............................................................ ", 7, $border, "C", "", "14", "", 0, 0);
					if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";

					if ($FLAG_RTF)
						$result = $RTF->add_text_line("วันที่ ............................................. ", 7, $border, "C", "", "14", "", 0, 0);
					else
						$result = $pdf->add_text_line("วันที่ ............................................. ", 7, $border, "C", "", "14", "", 0, 0);
					if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";

					if ($FLAG_RTF)
						$result = $RTF->add_text_line("", 7, $border, "C", "", "14", "", 0, 0);
					else
						$result = $pdf->add_text_line("", 7, $border, "C", "", "14", "", 0, 0);
					if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";
					$border = "LBR";

					if ($FLAG_RTF)
						$result = $RTF->add_text_line("", 7, $border, "C", "", "14", "", 0, 0);
					else
						$result = $pdf->add_text_line("", 7, $border, "C", "", "14", "", 0, 0);
					if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";

					if ($FLAG_RTF)
						$RTF->new_page();
					else
						$pdf->AddPage();
				} // end if

				if($data_count == 0) print_header("O4", "K5");
				elseif($data_count == 2) print_header("D1", "M2");
			} // end if
			
			$REPORT_ORDER = $arr_content[$data_count][type];
			$NAME = $arr_content[$data_count][name];
			$PERCENT_SALARY = $arr_content[$data_count][percent_salary];
			$PROMOTED_SALARY_1 = $arr_content[$data_count][promoted_salary_1];
			$PROMOTED_SALARY_2 = $arr_content[$data_count][promoted_salary_2];
			$TOTAL_PROMOTED_SALARY = $arr_content[$data_count][total_promoted_salary];
			$REMARK_1 = $arr_content[$data_count][remark_1];
			if($REPORT_ORDER == "HEADER") $REMARK_2 = $arr_content[$data_count][remark_2];

			$border = "";
			
			$arr_data = (array) null;
			$arr_data[] = $NAME;
			$arr_data[] = $PERCENT_SALARY;
			$arr_data[] = $PROMOTED_SALARY_1;
			$arr_data[] = $PROMOTED_SALARY_2;
			$arr_data[] = $TOTAL_PROMOTED_SALARY;
			$arr_data[] = $REMARK_1;

			if($REPORT_ORDER == "DETAIL")
				if ($FLAG_RTF)
					$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
				else
					$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "14", "", "000000", "");		//TRHBL
			else
				if ($FLAG_RTF)
					$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
				else
					$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "14", "b", "000000", "");		//TRHBL
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
		} // end for
		
		if($data_count > 0){
			$border = "LR";
//			$pdf->SetFont($font,'',14);
//			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

			for($i=0; $i<3; $i++){
				if ($FLAG_RTF)
					$result = $RTF->add_text_line("", 7, $border, "C", "", "14", "", 0, 0);
				else
					$result = $pdf->add_text_line("", 7, $border, "C", "", "14", "", 0, 0);
				if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";
			} // end if

			if ($FLAG_RTF)
				$result = $RTF->add_text_line("( $confirm_name )", 7, $border, "C", "", "14", "", 0, 0);
			else
				$result = $pdf->add_text_line("( $confirm_name )", 7, $border, "C", "", "14", "", 0, 0);
			if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";

			if ($FLAG_RTF)
				$result = $RTF->add_text_line("ตำแหน่ง  $confirm_position", 7, $border, "C", "", "14", "", 0, 0);
			else
				$result = $pdf->add_text_line("ตำแหน่ง  $confirm_position", 7, $border, "C", "", "14", "", 0, 0);
			if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";
		
			if ($FLAG_RTF)
				$result = $RTF->add_text_line("วันที่                         ตุลาคม ". ($search_budget_year - 1), 7, $border, "C", "", "14", "", 0, 0);
			else
				$result = $pdf->add_text_line("วันที่                         ตุลาคม ". ($search_budget_year - 1), 7, $border, "C", "", "14", "", 0, 0);
			if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";

			if ($FLAG_RTF)
				$result = $RTF->add_text_line("", 7, $border, "C", "", "14", "", 0, 0);
			else
				$result = $pdf->add_text_line("", 7, $border, "C", "", "14", "", 0, 0);
			if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";

			if ($BKK_FLAG==1)
				if ($FLAG_RTF)
					$result = $RTF->add_text_line("สำนักการคลังตรวจสอบแล้ว", 7, $border, "C", "", "14", "", 0, 0);
				else
					$result = $pdf->add_text_line("สำนักการคลังตรวจสอบแล้ว", 7, $border, "C", "", "14", "", 0, 0);
			else
				if ($FLAG_RTF)
					$result = $RTF>add_text_line("กรมบัญชีกลางตรวจสอบแล้ว", 7, $border, "C", "", "14", "", 0, 0);
				else
					$result = $pdf->add_text_line("กรมบัญชีกลางตรวจสอบแล้ว", 7, $border, "C", "", "14", "", 0, 0);
			if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";
		
			for($i=0; $i<3; $i++){
				if ($FLAG_RTF)
					$result = $RTF->add_text_line("", 7, $border, "C", "", "14", "", 0, 0);
				else
					$result = $pdf->add_text_line("", 7, $border, "C", "", "14", "", 0, 0);
				if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";
			} // end if
		
			$border = "";
			$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;

			$arr_data = (array) null;
			$arr_data[] = $REMARK_2;
			$arr_data[] = "";
			$arr_data[] = "";
			$arr_data[] = "";
			$arr_data[] = "";
			$arr_data[] = "( ............................................................ )";

			if($REPORT_ORDER == "DETAIL")
				if ($FLAG_RTF)
					$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
				else
					$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "14", "", "000000", "");		//TRHBL
			else
				if ($FLAG_RTF)
					$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
				else
					$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "14", "b", "000000", "");		//TRHBL
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
		
			if ($FLAG_RTF)
				$result = $RTF->add_text_line("ตำแหน่ง ............................................................ ", 7, $border, "C", "", "14", "", 0, 0);
			else
				$result = $pdf->add_text_line("ตำแหน่ง ............................................................ ", 7, $border, "C", "", "14", "", 0, 0);
			if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";

			if ($FLAG_RTF)
				$result = $RTF->add_text_line("วันที่ ............................................. ", 7, $border, "C", "", "14", "", 0, 0);
			else
				$result = $pdf->add_text_line("วันที่ ............................................. ", 7, $border, "C", "", "14", "", 0, 0);
			if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";

			if ($FLAG_RTF)
				$result = $RTF->add_text_line("", 7, $border, "C", "", "14", "", 0, 0);
			else
				$result = $pdf->add_text_line("", 7, $border, "C", "", "14", "", 0, 0);
			if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";

			$border = "LBR";
			
			if ($FLAG_RTF)
				$result = $RTF->add_text_line("", 7, $border, "C", "", "14", "", 0, 0);
			else
				$result = $pdf->add_text_line("", 7, $border, "C", "", "14", "", 0, 0);
			if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";
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