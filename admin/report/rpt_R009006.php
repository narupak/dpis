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
	
	if(in_array("ALL", $list_type) && !$DEPARTMENT_ID)  {
		$f_all = true; 
		$RPTORD_LIST = "COUNTRY|$RPTORD_LIST";
	} else $f_all = false;	

	if(!trim($RPTORD_LIST)){ 
		$RPTORD_LIST = "ORG|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)=="" && trim($search_org_id_2)=="") $RPTORD_LIST .= "ORG_1|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)!="" && trim($search_org_id_2)=="") $RPTORD_LIST .= "ORG_2|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)!="" && trim($search_org_id_2)!="") $RPTORD_LIST .= "LINE|";
		if(in_array("PER_COUNTRY", $list_type) && trim($search_pv_code)!="") $RPTORD_LIST .= "PROVINCE|";
	} // end if
	$arr_rpt_order = explode("|", $RPTORD_LIST);

	$select_list = "";
	$order_by = "";
	$heading_name = "";

	if(trim($search_date_min)){
		$arr_temp = explode("/", $search_date_min);
		$search_date_min = ($arr_temp[2] - 543) ."-". $arr_temp[1] ."-". $arr_temp[0];
		$show_date_min = ($arr_temp[0] + 0) ." ". $month_full[($arr_temp[1] + 0)][TH] ." ". $arr_temp[2];
	} // end if

	if(trim($search_date_max)){
		$arr_temp = explode("/", $search_date_max);
		$search_date_max = ($arr_temp[2] - 543) ."-". $arr_temp[1] ."-". $arr_temp[0];
		$show_date_max = ($arr_temp[0] + 0) ." ". $month_full[($arr_temp[1] + 0)][TH] ." ". $arr_temp[2];
	} // end if

	$list_type_text = $ALL_REPORT_TITLE;

	if(in_array("ALL", $list_type) || !isset($list_type)){	//กรณีทั้งหมด หรือไม่ติ๊กเลือก check box list_type เลย
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(b.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(b.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
			$list_type_text .= " - $MINISTRY_NAME";
		}elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			if($select_org_structure==0) $arr_search_condition[] = "(trim(c.PV_CODE) = '$PROVINCE_CODE')";
			elseif($select_org_structure==1) $arr_search_condition[] = "(trim(e.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);
	
	if ($search_diff_type==1) $show_diff_type = "สูงขึ้น";
	elseif ($search_diff_type==-1) $show_diff_type = "ลดลง";

	$company_name = "";
	$report_title = "ภาพรวมการประเมินซ้ำคะแนน$show_diff_type $search_budget_year ถึง $search_budget_year5";
	if($export_type=="report")	$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
	$report_code = "R0906";
	include ("rpt_R009006_format.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน
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

		$fname= "rpt_R009006_rtf.rtf";

	//	RTF $papersize = "a4", $margl = 720, $margr = 720, $margt = 720, $margb = 720, $orient = "p", $u_color_tab="";
		$paper_size="a4";
		$orientation='P';
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
		$orientation='P';

		$pdf = new PDF($orientation,$unit,$paper_size,$lang_code,$company_name,$report_title,$report_code,$heading,$heading_width,$heading_align);
		
		$pdf->Open();
		$pdf->SetMargins(5,5,5);
		$pdf->AliasNbPages();
		$pdf->AddPage();
		$pdf->SetTextColor(0, 0, 0);
		$pdf->SetFont($font,'',14);
		
		$page_start_x = $pdf->x;			$page_start_y = $pdf->y;
	}
	
	function count_person($count_dup, $search_condition, $addition_condition){
		global $DPISDB, $db_dpis2, $ORG_CODE;
		
		if(trim($addition_condition)) $search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition;		
		$search_condition = str_replace(" where ", " and ", $search_condition);

		$cmd = " select		count(CA_NAME) as count_person
						 from			PER_MGT_COMPETENCY_ASSESSMENT 
						 where		ORG_CODE='$ORG_CODE'
											$search_condition 
						  group by	 CA_NAME, CA_SURNAME
						  having count(CA_NAME) = $count_dup ";
		$count_person = $db_dpis2->send_cmd($cmd);
//	echo "$cmd<hr>";
	//	$db_dpis2->show_error();
		if($count_person==1){
			$data = $db_dpis2->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			if($data[count_person] == 0) $count_person = 0;
		} // end if

		return $count_person;
	} // function
	
	function initialize_parameter($current_index){
		global $arr_rpt_order;
		global $ORG_CODE;
		for($rpt_order_index=$current_index; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			$ORG_CODE = -1;
		} // end for
	} // function

	$search_condition = "";
	unset($arr_search_condition);
	if(trim($search_date_min)){
		if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(CA_TEST_DATE, 10) >= '$search_date_min')";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(CA_TEST_DATE, 1, 10) >= '$search_date_min')";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(CA_TEST_DATE, 10) >= '$search_date_min')";
	} // end if
	if(trim($search_date_max)){
		if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(CA_TEST_DATE, 10) <= '$search_date_max')";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(CA_TEST_DATE, 1, 10) <= '$search_date_max')";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(CA_TEST_DATE, 10) <= '$search_date_max')";
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);
	$cmd = " select		distinct ORG_CODE
					 from			PER_MGT_COMPETENCY_ASSESSMENT
										$search_condition
					 order by	ORG_CODE ";
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
//	echo $cmd."<hr>";
	$data_count = 0;
	for($i=2; $i<=5; $i++) ${"GRAND_TOTAL_".$i} = 0;
	initialize_parameter(0); 
	while($data = $db_dpis->get_array()){
		$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
		if($ORG_CODE != $data[ORG_CODE]){
			$ORG_CODE = $data[ORG_CODE];

			$arr_content[$data_count][type] = "ORG";
			$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_CODE;
			for($i=2; $i<=5; $i++) 
				$arr_content[$data_count][("count_".$i)] = count_person($i, $search_condition, $addition_condition);

			if($rpt_order_index == 0){
				for($i=1; $i<=5; $i++) ${"GRAND_TOTAL_".$i} += $arr_content[$data_count]["count_".$i];
			} // end if

			if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
			$data_count++;
		} // end if
	} // end while
	
	$GRAND_TOTAL = $GRAND_TOTAL_1 + $GRAND_TOTAL_2 + $GRAND_TOTAL_3 + $GRAND_TOTAL_4 + $GRAND_TOTAL_5;
	if($GRAND_TOTAL){
		for($i=2; $i<=5; $i++) ${"PERCENT_TOTAL_".$i} = (${"GRAND_TOTAL_".$i} / $GRAND_TOTAL) * 100;		
	} // end if

//	echo "<pre>"; print_r($arr_content); echo "</pre>";
//	echo "GRAND_TOTAL = $GRAND_TOTAL";
		$head_text1 = implode(",", $heading_text);
		$head_width1 = implode(",", $heading_width);
		$head_align1 = implode(",", $heading_align);
		$col_function = implode(",", $column_function);
		if ($FLAG_RTF) {
			$RTF->add_header("", 0, false);	// header default
			$RTF->add_footer("", 0, false);		// footer default
				
		//	echo "$head_text1<br>";
			$tab_align = "center";
			$result = $RTF->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", $fmtTableHeader_col_idx, $fmtTableHeader_bgcol_idx, $COLUMN_FORMAT, $col_function, false, $tab_align);
		} else {
			$pdf->AutoPageBreak = false; 
	//		echo ">>cnt headtext=".count($heading_text).", cnt headwidth=".count($heading_width)."<br>";
			$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", "0066CC", "EEEEFF", $COLUMN_FORMAT, $col_function);
		}
		if (!$result) echo "****** error ****** on open table for $table<br>";

		if($count_data){
			for($data_count=0; $data_count<count($arr_content); $data_count++){
				$COUNT_TOTAL = 0;
				for($i=2; $i<=5; $i++) ${"COUNT_".$i} = ${"PERCENT_".$i} = 0;
	
				$REPORT_ORDER = $arr_content[$data_count][type];
				$NAME = $arr_content[$data_count][name];
				for($i=1; $i<=5; $i++){
					${"COUNT_".$i} = $arr_content[$data_count]["count_".$i];
					$COUNT_TOTAL += ${"COUNT_".$i};
				} // end for
				
				if($COUNT_TOTAL){
					for($i=2; $i<=5; $i++) ${"PERCENT_".$i} = (${"COUNT_".$i} / $COUNT_TOTAL) * 100;
				} // end if
				if($GRAND_TOTAL) $PERCENT_TOTAL = ($COUNT_TOTAL / $GRAND_TOTAL) * 100;
	
				$arr_data = (array) null;
				$arr_data[] = $NAME;
				for($i=2; $i<=5; $i++) {
					$arr_data[] = ${"COUNT_".$i};
					$arr_data[] = ${"PERCENT_".$i};
				}
				$arr_data[] = $COUNT_TOTAL;
				$arr_data[] = $PERCENT_TOTAL;
	
				if(array_search($REPORT_ORDER, $arr_rpt_order) !== false  && array_search($REPORT_ORDER, $arr_rpt_order) == 0 && count($arr_rpt_order) > 1)
					if ($FLAG_RTF)
						$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
					else
						$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "14", "b", "000000", "");		//TRHBL
				else
					if ($FLAG_RTF)
						$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
					else
						$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "14", "", "000000", "");		//TRHBL
				if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
			} // end for
		
			$PERCENT_TOTAL = ($GRAND_TOTAL / $GRAND_TOTAL) * 100;
	
			$arr_data = (array) null;
			$arr_data[] = "รวม";
			for($i=2; $i<=5; $i++) {
				$arr_data[] = ${"GRAND_TOTAL_".$i};
				$arr_data[] = ${"PERCENT_TOTAL_".$i};
			}
			$arr_data[] = $GRAND_TOTAL;
			$arr_data[] = $PERCENT_TOTAL;

			if ($FLAG_RTF)
				$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
			else
				$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", "000000", "");		//TRHBL
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
		}else{
			if ($FLAG_RTF)
				$result = $RTF->add_text_line("********** ไม่มีข้อมูล **********", 7, "", "C", "", "14", "b", 0, 0);
			else
				$result = $pdf->add_text_line("********** ไม่มีข้อมูล **********", 7, "", "C", "", "14", "b", 0, 0);
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