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
		if(in_array("PER_ORG", $list_type)  && trim($search_org_id_1)!="" && trim($search_org_id_2)=="") $RPTORD_LIST .= "ORG_2|";
		if(in_array("PER_ORG", $list_type)  && trim($search_org_id_1)!="" && trim($search_org_id_2)!="") $RPTORD_LIST .= "LINE|";
		if(in_array("PER_COUNTRY", $list_type) && trim($search_pv_code)!="") $RPTORD_LIST .= "PROVINCE|";
	} // end if
	$arr_rpt_order = explode("|", $RPTORD_LIST);
//	$arr_rpt_order = array("MINISTRY", "DEPARTMENT", "ORG", "LINE"); 
 
	$select_list = "";
	$order_by = "";
	$heading_name = "";
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
		$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
		switch($REPORT_ORDER){
			case "MINISTRY" :
				if($select_list) $select_list .= ", ";
				$select_list .= "c.ORG_ID_REF as MINISTRY_ID";

				if($order_by) $order_by .= ", ";
				$order_by .= "c.ORG_ID_REF";

				$heading_name .= " ส่วนราชการ";
				break;
			case "DEPARTMENT" :
				if($select_list) $select_list .= ", ";
				$select_list .= "c.ORG_SEQ_NO, c.ORG_CODE, a.DEPARTMENT_ID";

				if($order_by) $order_by .= ", ";
				$order_by .= "c.ORG_SEQ_NO, c.ORG_CODE, a.DEPARTMENT_ID";

				$heading_name .= " ส่วนราชการ";
				break;
			case "ORG" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure==0) $select_list .= "b.ORG_SEQ_NO, b.ORG_CODE, a.ORG_ID";
				else if($select_org_structure==1) $select_list .= "b.ORG_SEQ_NO, b.ORG_CODE, d.ORG_ID";

				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "b.ORG_SEQ_NO, b.ORG_CODE, a.ORG_ID";
				else if($select_org_structure==1) $order_by .= "b.ORG_SEQ_NO, b.ORG_CODE, d.ORG_ID";

				$heading_name .= " ส่วนราชการ";
				break;
			case "LINE" :
				if($select_list) $select_list .= ", ";
				$select_list .= "e.PM_SEQ_NO, a.PM_CODE, f.PL_SEQ_NO, a.PL_CODE";

				if($order_by) $order_by .= ", ";
				$order_by .= "e.PM_SEQ_NO, a.PM_CODE, f.PL_SEQ_NO, a.PL_CODE";

				$heading_name .= " ตำแหน่งในการบริหารงาน";
				break;
		} // end switch case
	} // end for
	if(!trim($order_by)){ 
		if(!$MINISTRY_ID){
			$order_by = "c.ORG_ID_REF";
		}elseif(!$DEPARTMENT_ID){
			$order_by = "c.ORG_SEQ_NO, c.ORG_CODE, a.DEPARTMENT_ID";
		}else{ 
			if($select_org_structure==0) $order_by = "b.ORG_SEQ_NO, b.ORG_CODE, a.ORG_ID";
			else if($select_org_structure==1) $order_by = "b.ORG_SEQ_NO, b.ORG_CODE, d.ORG_ID";
		}
	} // end if
	if(!trim($select_list)){ 
		if(!$MINISTRY_ID){
			$select_list = "c.ORG_ID_REF as MINISTRY_ID";
		}elseif(!$DEPARTMENT_ID){
			$select_list = "c.ORG_SEQ_NO, c.ORG_CODE, a.DEPARTMENT_ID";
		}else{ 
			if($select_org_structure==0) $select_list = "b.ORG_SEQ_NO, b.ORG_CODE, a.ORG_ID";
			else if($select_org_structure==1) $select_list = "b.ORG_SEQ_NO, b.ORG_CODE, d.ORG_ID";
		}
	} // end if

	$search_condition = "";
	$arr_search_condition[] = "(POS_STATUS=1)";

	$list_type_text = $ALL_REPORT_TITLE;

	if(in_array("PER_ORG_TYPE_1", $list_type)){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			$arr_search_condition[] = "(c.ORG_ID_REF = $MINISTRY_ID)";
			$list_type_text .= " - $MINISTRY_NAME";
		}elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			$arr_search_condition[] = "(trim(b.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if
		
		// ส่วนกลาง
		$list_type_text = "ส่วนกลาง";
		if($select_org_structure==0) $arr_search_condition[] = "(trim(b.OT_CODE)='01')";
		elseif($select_org_structure==1) $arr_search_condition[] = "(trim(d.OT_CODE)='01')";
	}
	if(in_array("PER_ORG_TYPE_3", $list_type)){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			$arr_search_condition[] = "(c.ORG_ID_REF = $MINISTRY_ID)";
			$list_type_text .= " - $MINISTRY_NAME";
		}elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			$arr_search_condition[] = "(trim(b.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if
		
		// ส่วนภูมิภาค
		$list_type_text = "ส่วนภูมิภาค";
		if($select_org_structure==0) $arr_search_condition[] = "(trim(b.OT_CODE)='03')";
		elseif($select_org_structure==1) $arr_search_condition[] = "(trim(d.OT_CODE)='03')";
	}
	if(in_array("PER_MGT", $list_type)){
		// ตำแหน่งในการบริหารงาน
		$list_type_text = "";
		$search_PM_CODE = trim($search_PM_CODE);
		$arr_search_condition[] = "(trim(a.PM_CODE)='$search_PM_CODE')";
		$list_type_text .= "$search_PM_NAME";
	}
	if(in_array("PER_COUNTRY", $list_type)){
		// ประเทศ , จังหวัด
		$list_type_text = "";
		if(trim($search_ct_code)){ 
			$search_ct_code = trim($search_ct_code);
			$arr_search_condition[] = "(trim(b.CT_CODE) = '$search_ct_code')";
			$list_type_text .= "$search_ct_name";
		} // end if
		if(trim($search_pv_code)){ 
			$search_pv_code = trim($search_pv_code);
			$arr_search_condition[] = "(trim(b.PV_CODE) = '$search_pv_code')";
			$list_type_text .= " - $search_pv_name";
		} // end if
	}
	if(in_array("ALL", $list_type) || !isset($list_type)){	//กรณีทั้งหมด หรือไม่ติ๊กเลือก check box list_type เลย
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			$arr_search_condition[] = "(c.ORG_ID_REF = $MINISTRY_ID)";
			$list_type_text .= " - $MINISTRY_NAME";
		}elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			$arr_search_condition[] = "(trim(b.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);
	
	$company_name = "$list_type_text";
	$report_title = "$DEPARTMENT_NAME||จำนวนข้าราชการ".$list_type_text." ประจำปีงบประมาณ พ.ศ. $search_budget_year||จำแนกตามตำแหน่งในการบริหารงาน และสังกัด";
	if($export_type=="report")$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
	$report_code = "R1033R";
//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();
		
	if ($FLAG_RTF) {
	//	include ("rpt_R010033r_format.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน
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

		$fname= "rpt_R010033r_rtf.rtf";

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

	$heading_width[0] = "20";
	$heading_width[1] = "50"; 	// A4 กว่าง 282 หักไป 70 เหลือ 212
	$heading_width[2] = "10";	// 
	
	function print_header($cont, $pm_seq, $pm){
//		echo "".implode(",",$org).",".count($org)."<br>";
		global $pdf, $FLAG_RTF, $RTF, $heading_width, $heading_name, $font, $fmtTableHeader_col_idx, $fmtTableHeader_bgcol_idx;
		global $ARR_LEVEL_GROUP, $ARR_LEVEL_GROUP_NAME, $ARR_LEVEL, $ARR_LEVEL_NAME, $TOTAL_LEVEL;
		
		if ($FLAG_RTF) {
			$RTF->set_table_font($font, 14);
	//		$RTF->set_font($font, 20);
			$RTF->color($fmtTableHeader_col_idx);	// $fmtTableHeader_col_idx
			
			$RTF->paragraph();
			// $fmtTableHeader_col_idx, $fmtTableHeader_bgcol_idx
			$border = "tlbr";
			$RTF->open_line("center", false, true);			
			$RTF->cell("ลำดับ", $heading_width[0], "center", $fmtTableHeader_bgcol_idx);
			$RTF->cell("หน่วยงาน", $heading_width[1], "center", $fmtTableHeader_bgcol_idx);
			if(!$idx)	$idx = 2;
			foreach($pm_seq as $key => $s_pm_seq){
				$CODE = $pm[$s_pm_seq][code];
				$NAME = $pm[$s_pm_seq][name];
				$RTF->cell($NAME, $heading_width[$idx], "center", $fmtTableHeader_bgcol_idx);
	//			 $idx++;
			}
			$RTF->cell("รวม", $heading_width[$idx], "center", $fmtTableHeader_bgcol_idx);
			$RTF->close_line();
			for($data_count=0; $data_count<count($cont); $data_count++){
				$REPORT_ORDER = $cont[$data_count][type];
				$DISPLAY_ORDER = $cont[$data_count][order];
			}
		} else {
			$pdf->SetFont($font,'',14);
			$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
			$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
	//		$pdf->Cell(100,2,"",0,1,'C');

			$pdf->Cell($heading_width[0] ,7,"ลำดับ",'LTBR',0,'C',1);
			$pdf->Cell($heading_width[1] ,7,"หน่วยงาน",'LTBR',0,'C',1);

			for($data_count=0; $data_count<count($cont); $data_count++){
				$REPORT_ORDER = $cont[$data_count][type];
				$DISPLAY_ORDER = $cont[$data_count][order];
			}
			
			foreach($pm_seq as $key => $s_pm_seq){
				$CODE = $pm[$s_pm_seq][code];
				$NAME = $pm[$s_pm_seq][name];
				$pdf->Cell($heading_width[2] ,7,"$NAME",'LTBR',0,'C',1);
	//			echo "$ORG_ID-$val<br>";
			}
			$pdf->Cell($heading_width[2] ,7,"รวม",'LTBR',1,'C',1);
		}
	} // function

	function count_person($budget_year, $pos_org_id, $search_condition, $addition_condition){
		global $DPISDB, $db_dpis2;
		global $select_org_structure;

		if(trim($addition_condition)) $search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition;	
		$year_condition="";
//		if($budget_year) $year_condition = generate_year_condition($budget_year);
/*		if($DPISDB=="odbc") $year_condition = " and (LEFT(trim(d.PER_RETIREDATE), 10) >= '".($budget_year - 1)."-10-01' and LEFT(trim(d.PER_RETIREDATE), 10) < '".($budget_year)."-10-01')";
		elseif($DPISDB=="oci8") $year_condition = " and (SUBSTR(trim(d.PER_RETIREDATE), 1, 10) >= '".($budget_year - 1)."-10-01' and SUBSTR(trim(d.PER_RETIREDATE), 1, 10) < '".($budget_year)."-10-01')";
		elseif($DPISDB=="mysql") $year_condition = " and (SUBSTRING(trim(d.PER_RETIREDATE), 1, 10) >= '".($budget_year - 1)."-10-01' and SUBSTRING(trim(d.PER_RETIREDATE), 1, 10) < '".($budget_year)."-10-01')";  */

		$pos_org_condition = "a.ORG_ID='$pos_org_id'";

		$search_condition = str_replace(" where ", " and ", $search_condition);

		if($DPISDB=="odbc"){
			$cmd = " select			count(d.PER_ID) as count_person
							from				(
														(
															PER_POSITION a
															left join PER_ORG b on (a.ORG_ID=b.ORG_ID)
														) left join PER_ORG c on (a.DEPARTMENT_ID=c.ORG_ID)
													) left join PER_PERSONAL d on (a.POS_ID=d.POS_ID and d.PER_TYPE=1 and d.PER_STATUS=1)
							 where			$pos_org_condition $year_condition
													$search_condition 
							 group by		d.PER_ID ";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select			count(d.PER_ID) as count_person
							 from				PER_POSITION a, PER_ORG b, PER_ORG c, PER_PERSONAL d
							 where			a.ORG_ID=b.ORG_ID(+) and a.DEPARTMENT_ID=c.ORG_ID(+) and a.POS_ID=d.POS_ID(+) and d.PER_TYPE=1 and d.PER_STATUS=1
							 						and $pos_org_condition $year_condition
													$search_condition 
							 group by		d.PER_ID ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			count(d.PER_ID) as count_person
							from				(
														(
															PER_POSITION a
															left join PER_ORG b on (a.ORG_ID=b.ORG_ID)
														) left join PER_ORG c on (a.DEPARTMENT_ID=c.ORG_ID)
													) left join PER_PERSONAL d on (a.POS_ID=d.POS_ID and d.PER_TYPE=1 and d.PER_STATUS=1)
							 where			$pos_org_condition $year_condition 
													$search_condition 
							 group by		d.PER_ID ";
		} // end if
		if($select_org_structure==1) { 
			$cmd = str_replace("a.ORG_ID", "d.ORG_ID", $cmd);
			$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		}
		$count_person = $db_dpis2->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis2->show_error();
		if($count_person==1){
			$data = $db_dpis2->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			if($data[count_person] == 0) $count_person = 0;
		} // end if

		return $count_person;
	} // function
	
	function generate_year_condition($budget_year){
		global $DPISDB;
		
//			คิดตามอายุจริง (ถ้ายังไม่ถึงวันเกิดจะไม่นับเป็นปี)
//			$birthdate_min = date_adjust(date("Y-m-d"), "y", -25);
//			if($DPISDB=="odbc") $age_condition = "(LEFT(trim(d.PER_BIRTHDATE), 10) > '$birthdate_min')";
//			elseif($DPISDB=="oci8") $age_condition = "(SUBSTR(trim(d.PER_BIRTHDATE), 1, 10) > '$birthdate_min')";

//			คิดเฉพาะปีเกิด กับปีปัจจุบัน
			$birthyear_min = date("Y") - 25;
			if($DPISDB=="odbc") $year_condition = "(LEFT(trim(d.PER_BIRTHDATE), 4) > '$birthyear_min')";
			elseif($DPISDB=="oci8") $year_condition = "(SUBSTR(trim(d.PER_BIRTHDATE), 1, 4) > '$birthyear_min')";
			elseif($DPISDB=="mysql") $year_condition = "(SUBSTRING(trim(d.PER_BIRTHDATE), 1, 4) > '$birthyear_min')";

//			echo "age <= 24 :: $age_condition<br>";
		
		return $year_condition;
	} // function
	
	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order,$select_org_structure;
		global $MINISTRY_ID, $DEPARTMENT_ID, $ORG_ID, $PM_CODE;
				
		for($rpt_order_index=0; $rpt_order_index <= $current_index; $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "MINISTRY" :	
					if($MINISTRY_ID && $MINISTRY_ID!=-1) $arr_addition_condition[] = "(c.ORG_ID_REF = $MINISTRY_ID)";
				break;
				case "DEPARTMENT" :	
					if($DEPARTMENT_ID && $DEPARTMENT_ID!=-1) $arr_addition_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
				break;
				case "ORG" :	
					if($ORG_ID && $ORG_ID!=-1){
						if($select_org_structure==0) $arr_addition_condition[] = "(a.ORG_ID = $ORG_ID)";
						else if($select_org_structure==1) $arr_addition_condition[] = "(d.ORG_ID = $ORG_ID)";
					}else{ 
						if($select_org_structure==0) $arr_addition_condition[] = "(a.ORG_ID = 0 or a.ORG_ID is null)";
						else if($select_org_structure==1) $arr_addition_condition[] = "(d.ORG_ID = 0 or d.ORG_ID is null)";
					}
				break;
				case "LINE" :
					if($PM_CODE) $arr_addition_condition[] = "(trim(a.PM_CODE) = '$PM_CODE')";
					else $arr_addition_condition[] = "(trim(a.PM_CODE) = '$PM_CODE' or a.PM_CODE is null)";
				break;
			} // end switch case
		} // end for
		
		$addition_condition = "";
		if(count($arr_addition_condition)) $addition_condition = implode(" and ", $arr_addition_condition);

		return $addition_condition;
	} // function
	
	function initialize_parameter($current_index){
		global $arr_rpt_order;
		global $MINISTRY_ID, $DEPARTMENT_ID, $ORG_ID, $PM_CODE;
		for($rpt_order_index=$current_index; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "MINISTRY" :	
					$MINISTRY_ID = -1;
				break;
				case "DEPARTMENT" :	
					$DEPARTMENT_ID = -1;
				break;
				case "ORG" :	
					$ORG_ID = -1;
				break;
				case "LINE" :
					$PM_CODE = -1;
				break;
			} // end switch case
		} // end for
	} // function
	
	function clear_display_order($req_index){
		global $arr_rpt_order, $display_order_number;
		
		$current_index = $req_index + 1;
		for($i=$current_index; $i<count($arr_rpt_order); $i++){
			$display_order_number[$current_index] = 0;
		} // loop for
	} // function

	function display_order($req_index){
		global $display_order_number;
		
		$return_display_order = "";
		$current_index = $req_index;
		while($current_index >= 0){
			if($current_index == $req_index){
				$return_display_order = $display_order_number[$current_index];
			}else{
				$return_display_order = $display_order_number[$current_index].".".$return_display_order;
			} // if else
			$current_index--;
		} // loop while
		
		return $return_display_order;
	} // function

	$year_condition = "";
/*	if($DPISDB=="odbc") $year_condition = "and (LEFT(trim(d.PER_RETIREDATE), 10) >= '".($budget_year - 1)."-10-01' and LEFT(trim(d.PER_RETIREDATE), 10) < '".($budget_year)."-10-01')";
	elseif($DPISDB=="oci8") $year_condition = "and (SUBSTR(trim(d.PER_RETIREDATE), 1, 10) >= '".($budget_year - 1)."-10-01' and SUBSTR(trim(d.PER_RETIREDATE), 1, 10) < '".($budget_year)."-10-01')";
	elseif($DPISDB=="mysql") $year_condition = "and (SUBSTRING(trim(d.PER_RETIREDATE), 1, 10) >= '".($budget_year - 1)."-10-01' and SUBSTRING(trim(d.PER_RETIREDATE), 1, 10) < '".($budget_year)."-10-01')"; */

	if($DPISDB=="odbc"){
		$cmd = " select			distinct b.ORG_SEQ_NO, b.ORG_CODE, a.ORG_ID, $select_list, count(d.PER_ID) as COUNT_PERSON
						 from			(
												(
													(
														PER_POSITION a
														left join PER_ORG b on (a.ORG_ID=b.ORG_ID)
													) left join PER_MGT e on (a.PM_CODE=e.PM_CODE)
												) left join PER_ORG c on (a.DEPARTMENT_ID=c.ORG_ID)
											) left join PER_PERSONAL d on (a.POS_ID=d.POS_ID and d.PER_TYPE=1 and d.PER_STATUS=1)
						$search_condition $year_condition 
						group by		b.ORG_SEQ_NO, b.ORG_CODE, a.ORG_ID, $order_by
						order by		b.ORG_SEQ_NO, b.ORG_CODE, a.ORG_ID, $order_by ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select			distinct b.ORG_SEQ_NO, b.ORG_CODE, a.ORG_ID, $select_list, count(d.PER_ID) as COUNT_PERSON
						 from			PER_POSITION a, PER_ORG b, PER_MGT e, PER_ORG c, PER_PERSONAL d, PER_LINE f
						 where			a.ORG_ID=b.ORG_ID(+) and a.PM_CODE=e.PM_CODE(+) and a.PL_CODE=f.PL_CODE(+) and a.DEPARTMENT_ID=c.ORG_ID(+) 
						 					and a.POS_ID=d.POS_ID(+) and d.PER_TYPE=1 and d.PER_STATUS=1
											$search_condition $year_condition 
						 group by		b.ORG_SEQ_NO, b.ORG_CODE, a.ORG_ID, $order_by
						 order by		b.ORG_SEQ_NO, b.ORG_CODE, a.ORG_ID, $order_by ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			distinct b.ORG_SEQ_NO, b.ORG_CODE, a.ORG_ID, $select_list, count(d.PER_ID) as COUNT_PERSON
						 from			(
												(
													(
														PER_POSITION a
														left join PER_ORG b on (a.ORG_ID=b.ORG_ID)
													) left join PER_MGT e on (a.PM_CODE=e.PM_CODE)
												) left join PER_ORG c on (a.DEPARTMENT_ID=c.ORG_ID)
											) left join PER_PERSONAL d on (a.POS_ID=d.POS_ID and d.PER_TYPE=1 and d.PER_STATUS=1)
						 $search_condition $year_condition 
						 group by		b.ORG_SEQ_NO, b.ORG_CODE, a.ORG_ID, $order_by
						 order by 		b.ORG_SEQ_NO, b.ORG_CODE, a.ORG_ID, $order_by ";
	} // end if
 	if($select_org_structure==1) { 
		$cmd = str_replace("a.ORG_ID", "d.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	echo "-> $cmd<br>";
//	$db_dpis->show_error();

	$arr_content = (array) null;
	$arr_grand_total = (array) null;
	$arr_org = (array) null;
	$arr_count = (array) null;
	$arr_pm = (array) null;
	$arr_pm_seq = (array) null;

	$data_count = -1;
	initialize_parameter(0);
	unset($display_order_number);
	
	$DISPLAY_ORDER_ORG = 0;
	$count_person = 0;
	while($data = $db_dpis->get_array()) {
		if ($s_org != $data[ORG_ID]) {
			$s_org = $data[ORG_ID];
			$data_count ++;
		}
		$s_org = $data[ORG_ID];
		$s_pm = trim($data[PM_CODE]);
		if (!$s_pm) $s_pm = trim($data[PL_CODE]);
		$s_pm_seq = $data[PM_SEQ_NO];
		if (!$s_pm_seq && $data[PL_SEQ_NO]) $s_pm_seq = "900000".$data[PL_SEQ_NO];
		if (!$s_pm_seq && $s_pm) $s_pm_seq = "9000000".$s_pm;

		if (!in_array($s_pm_seq, $arr_pm_seq)) {
			$arr_pm_seq[] = $s_pm_seq;
			//echo "-> $s_pm_seq <br>";
		}
//		echo "****************** [$s_pm_seq][$s_pm] PL_CODE=$s_pl<br>";
		if (!$arr_pm[$s_pm_seq][code]) {
			if($s_pm==""){
				$PM_NAME = "[ไม่ระบุตำแหน่งในการบริหารงาน]";
			}else{
				$cmd = " select PM_NAME, PM_SHORTNAME from PER_MGT where trim(PM_CODE)='$s_pm' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PM_NAME = trim($data2[PM_SHORTNAME])?$data2[PM_SHORTNAME]:$data2[PM_NAME];
				if (!$PM_NAME) {
					$cmd = " select PL_NAME, PL_SHORTNAME from PER_LINE where trim(PL_CODE)='$s_pm' ";
					$db_dpis2->send_cmd($cmd);
//					$db_dpis2->show_error();
					$data2 = $db_dpis2->get_array();
					$PM_NAME = trim($data2[PL_SHORTNAME])?$data2[PL_SHORTNAME]:$data2[PL_NAME];
				}	
			} // end if
			$arr_pm[$s_pm_seq][code] = $s_pm;
			$arr_pm[$s_pm_seq][name] = $PM_NAME;
//			echo "$s_pm-$PM_NAME<br>";
		} // endif (!$arr_pm[$s_pm_seq][code])
		
		//-------------------------------------------------------------------------
		$count_person= $data[COUNT_PERSON];
		if(!$count_person)	$count_person=0;
		$arr_count_person[$s_org][$s_pm] = $count_person;
		//$arr_count_total_person[$s_pm] += $arr_count_person[$s_org][$s_pm];
		//------------------------------------------------------------------------
		
		for($rpt_order_index=0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "MINISTRY" :
					if($MINISTRY_ID != $data[MINISTRY_ID]){
						$display_order_number[$rpt_order_index]++;
						clear_display_order($rpt_order_index);
						$MINISTRY_ID = $data[MINISTRY_ID];
						if($MINISTRY_ID == ""){
							$MINISTRY_NAME = "[ไม่ระบุ$MINISTRY_TITLE]";
						}else{
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
							if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$MINISTRY_NAME = $data2[ORG_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "MINISTRY";
						$arr_content[$data_count][order] = display_order($rpt_order_index);
						$arr_content[$data_count][code] = $s_org;
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $MINISTRY_NAME;

						$arr_content[$data_count][$s_org][$s_pm] = $arr_count_person[$s_org][$s_pm];	
						$arr_content[$data_count][total] += $arr_content[$data_count][$s_org][$s_pm];

						if($rpt_order_index == 0){ 
							//$arr_grand_total[$s_pm] += $arr_content[$data_count][$s_pm];
							$arr_grand_total[$s_pm] += $arr_count_person[$s_org][$s_pm];	
						}

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);
						$data_count++;
					} // end if
				break;

				case "DEPARTMENT" :
					if($DEPARTMENT_ID != $data[DEPARTMENT_ID]){
						$display_order_number[$rpt_order_index]++;
						clear_display_order($rpt_order_index);
						$DEPARTMENT_ID = $data[DEPARTMENT_ID];
						if($DEPARTMENT_ID == ""){
							$DEPARTMENT_NAME = "[ไม่ระบุ$DEPARTMENT_TITLE]";
						}else{
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
							if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$DEPARTMENT_NAME = $data2[ORG_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "DEPARTMENT";
						$arr_content[$data_count][order] = display_order($rpt_order_index);
						$arr_content[$data_count][code] = $s_org;
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $DEPARTMENT_NAME;
						$arr_content[$data_count][$s_org][$s_pm] = $arr_count_person[$s_org][$s_pm];	
						$arr_content[$data_count][total] += $arr_content[$data_count][$s_org][$s_pm];

						if($rpt_order_index == 0){ 
							//$arr_grand_total[$s_pm] += $arr_content[$data_count][$s_pm];
							$arr_grand_total[$s_pm] += $arr_count_person[$s_org][$s_pm];
						}
						
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;

				case "ORG" :
					if($ORG_ID != $data[ORG_ID]){
						$display_order_number[$rpt_order_index]++;
						clear_display_order($rpt_order_index);
						$ORG_ID = $data[ORG_ID];
						if($ORG_ID == ""){
							$ORG_NAME = "[ไม่ระบุ$ORG_TITLE]";
						}else{
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID ";
							if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_NAME = $data2[ORG_NAME];
							if($ORG_NAME=="-")	$ORG_NAME = $ORG_BKK_TITLE;
						} // end if

						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "ORG";
						$arr_content[$data_count][order] = display_order($rpt_order_index);
						$arr_content[$data_count][code] = $s_org;
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME;
						$arr_content[$data_count][$s_org][$s_pm] = $arr_count_person[$s_org][$s_pm];	
						$arr_content[$data_count][total] += $arr_content[$data_count][$s_org][$s_pm];
						
						if($rpt_order_index == 0){ 
							//$arr_grand_total[$s_pm] += $arr_content[$data_count][$s_pm];
							$arr_grand_total[$s_pm] += $arr_count_person[$s_org][$s_pm];
						}

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;
				
				case "LINE" :
//					if($PM_CODE != trim($data[PM_CODE])){
						$display_order_number[$rpt_order_index]++;
						clear_display_order($rpt_order_index);
						$PM_CODE = trim($data[PM_CODE]);
						// แสดงรายชื่อหน่วยงาน แนวตั้ง
						$ORG_ID = $data[ORG_ID];
						if($ORG_ID == ""){
							$ORG_NAME = "[ไม่ระบุ$ORG_TITLE]";
						}else{
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID ";
							if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_NAME = $data2[ORG_NAME];
							if($ORG_NAME=="-")	$ORG_NAME =  $ORG_BKK_TITLE;
						} // end if

						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "LINE";
						$arr_content[$data_count][order] = display_order($rpt_order_index);
						$arr_content[$data_count][code] = $s_org;
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . "".$ORG_NAME;

						$arr_content[$data_count][$s_org][$s_pm] = $arr_count_person[$s_org][$s_pm];	
						$arr_content[$data_count][total] += $arr_content[$data_count][$s_org][$s_pm];

						if($rpt_order_index == 0){ 
							//$arr_grand_total[$s_pm] += $arr_content[$data_count][$s_pm];
							$arr_grand_total[$s_pm] += $arr_count_person[$s_org][$s_pm];
						}

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);
						$data_count++;
//					} // end if
				break;		
			} // end switch case
		} // end for
	} // end while

//	echo "<hr><pre>"; print_r($arr_content); echo "</pre>";
//	print("<hr><pre>");		print_r($arr_pm_seq);			print("</pre>");
//	print("<pre><b>ARR_COUNT_PERSON</b> :: ");		print_r($arr_count_person);		print("</pre>");		

	if($export_type=="report"){	
		if ($count_data) {
		
			sort($arr_pm_seq);

			if ($FLAG_RTF) {
				$RTF->set_font($font, 14);
				$RTF->color("0");	// 0=BLACK
					
				$RTF->add_header("", 0, false);	// header default
				$RTF->add_footer("", 0, false);		// footer default
			} else {
				$pdf->AutoPageBreak = false;
			}

			$buff1 = floor(212 / (count($arr_pm_seq) + 1));				// A4 กว่าง 282 หักไป 70 เหลือ 212
			$heading_width[2] = trim((string) $buff1);			// 212 / จำนวน column ที่เหลือ + col รวม
			$buff2 = 50 + (212 - ((count($arr_pm_seq) + 1) * $buff1));
			$heading_width[1] = trim((string) $buff2); 			//  เศษที่เหลือ ไปบวกเพิ่มที่ หัว 1
	//		echo "$heading_width[0],$heading_width[1],$heading_width[2]<br>";
			if ($FLAG_RTF) {
				$sum_w = (int)$heading_width[0]+(int)$heading_width[1]+((count($arr_org_seq)+1) * (int)$heading_width[2]);
				for($h = 0; $h < count($heading_width); $h++) {
					$heading_width[$h] = $heading_width[$h] / $sum_w * 100;
				}
			}

			print_header($arr_content, $arr_pm_seq, $arr_pm);
			
			$DISPLAY_ORDER = 0;
			for($data_count=0; $data_count<count($arr_content); $data_count++){
				$REPORT_ORDER = $arr_content[$data_count][type];
				$NAME = $arr_content[$data_count][name];
				$CODE = $arr_content[$data_count][code];				//ORG_ID
				$DISPLAY_ORDER++;		
			
				if ($FLAG_RTF) {
					$border = "TLBR";
		//			$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
		//			$RTF->ln();			
					$RTF->set_table_font($font, 14);
				} else {
					$border = "LR";
					$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
				}

				if(!in_array($CODE,$arr_exist_orgid)){
					$DISPLAY_ORDER_ORGID++;
					$arr_exist_orgid[] = $CODE;
					if ($FLAG_RTF) {
						$RTF->open_line("center", false, false);			
						$RTF->cell($RTF->bold(1) . (($NUMBER_DISPLAY==2)?convert2thaidigit($DISPLAY_ORDER_ORGID):$DISPLAY_ORDER_ORGID) . $RTF->bold(0), $heading_width[0], "center", $border);
						$RTF->cell($RTF->bold(1) . (($NUMBER_DISPLAY==2)?convert2thaidigit($NAME):$NAME) . $RTF->bold(0), $heading_width[1], "left", $border);
					} else {
						$pdf->MultiCell($heading_width[0], 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($DISPLAY_ORDER_ORGID):$DISPLAY_ORDER_ORGID), $border, "C");
						if($pdf->y > $max_y) $max_y = $pdf->y;
						$pdf->x = $start_x + $heading_width[0];
						$pdf->y = $start_y;
						$pdf->MultiCell($heading_width[1], 7, "$NAME", $border, "L");
						if($pdf->y > $max_y) $max_y = $pdf->y;
						$pdf->x = $start_x + $heading_width[0] + $heading_width[1];
						$pdf->y = $start_y;
					}
					foreach($arr_pm_seq as $key => $pm_seq){
		//				echo "arr_count[$data_count][$pm_seq][$arr_pm[$pm_seq][code]]=".$arr_count[$data_count][$pm_seq][$arr_pm[$pm_seq][code]]."|";
						if ($FLAG_RTF)
							$RTF->cell($RTF->bold(1) . ($arr_count_person[$CODE][$arr_pm[$pm_seq][code]]?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($arr_count_person[$CODE][$arr_pm[$pm_seq][code]])):number_format($arr_count_person[$CODE][$arr_pm[$pm_seq][code]])):"-") . $RTF->bold(0), $heading_width[2], "right", $border);
						else
							$pdf->Cell($heading_width[2], 7, ($arr_count_person[$CODE][$arr_pm[$pm_seq][code]]?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($arr_count_person[$CODE][$arr_pm[$pm_seq][code]])):number_format($arr_count_person[$CODE][$arr_pm[$pm_seq][code]])):"-"), $border, 0, 'R', 0);

						$arr_grand_total[$arr_pm[$pm_seq][code]] += $arr_count_person[$CODE][$arr_pm[$pm_seq][code]];		//รวมแยกแต่ละ pl_code
					} // end foreach
					
		//			echo "total[$ORG_ID]=".$arr_grand_total[$ORG_ID]."<br>";
					if ($FLAG_RTF) {
						$RTF->cell($RTF->bold(1) . (array_sum($arr_count_person[$CODE])?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format(array_sum($arr_count_person[$CODE]))):number_format(array_sum($arr_count_person[$CODE]))):"-") . $RTF->bold(0), $heading_width[2], "right", $border);
						$RTF->close_line();
					} else {
						$pdf->Cell($heading_width[2], 7, (array_sum($arr_count_person[$CODE])?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format(array_sum($arr_count_person[$CODE]))):number_format(array_sum($arr_count_person[$CODE]))):"-"), $border, 0, 'R', 0);
					
						//================= Draw Border Line ====================
						$line_start_y = $start_y;		$line_start_x = $start_x;
						$line_end_y = $max_y;		$line_end_x = $start_x;
						$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);

						for($i=0; $i<=count($arr_pm_seq)+2; $i++){
							if($i==0){
								$line_start_y = $start_y;		$line_start_x += $heading_width[0];
								$line_end_y = $max_y;		$line_end_x += $heading_width[0];
							}elseif($i==1){
								$line_start_y = $start_y;		$line_start_x += $heading_width[1];
								$line_end_y = $max_y;		$line_end_x += $heading_width[1];
							}else{
								$line_start_y = $start_y;		$line_start_x += $heading_width[2];
								$line_end_y = $max_y;		$line_end_x += $heading_width[2];
							} // end if
							$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
						} // end for
						//====================================================

						if(($pdf->h - $max_y - 10) < 15){ 
							$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
							if($data_count < (count($arr_content) - 1)){
								$pdf->AddPage();
								print_header($arr_content, $arr_pm_seq, $arr_pm);
								$max_y = $pdf->y;
							} // end if
						}else{
							if($DISPLAY_ORDER == (count($arr_org) - 1)) $pdf->Line($start_x, $max_y, $pdf->x, $max_y);
						} // end if

						$pdf->x = $start_x;			$pdf->y = $max_y;
					}
					
				} //end if in_array
				
			} // end for
			if ($FLAG_RTF) {
				$border = "LTBR";
				$RTF->set_table_font($font, 14);

				$RTF->open_line("center", false, false);			
				$RTF->cell("", $heading_width[0], "center", $border);
				$RTF->cell("รวมทั้งหมด", $heading_width[1], "left", $border);
			} else {
				$border = "LTBR";
				$pdf->SetFont($font,'b',14);
				$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

				$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;

				$pdf->MultiCell($heading_width[0], 7, "", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0];
				$pdf->y = $start_y;
				$pdf->MultiCell($heading_width[1], 7, "รวมทั้งหมด", $border, "R");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1];
				$pdf->y = $start_y;
			}
	 
			$GRAND_TOTAL = 0;
			foreach($arr_pm_seq as $key => $pm_seq){
				if ($FLAG_RTF)
					$RTF->cell($RTF->bold(1) . ($arr_grand_total[$arr_pm[$pm_seq][code]]?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($arr_grand_total[$arr_pm[$pm_seq][code]])):number_format($arr_grand_total[$arr_pm[$pm_seq][code]])):"-") . $RTF->bold(0), $heading_width[2], "right", $border);
				else
					$pdf->Cell($heading_width[2], 7, ($arr_grand_total[$arr_pm[$pm_seq][code]]?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($arr_grand_total[$arr_pm[$pm_seq][code]])):number_format($arr_grand_total[$arr_pm[$pm_seq][code]])):"-"), $border, 0, 'R', 0);
				$GRAND_TOTAL += $arr_grand_total[$arr_pm[$pm_seq][code]];
			}
			if ($FLAG_RTF) {
				$RTF->cell($RTF->bold(1) . ($GRAND_TOTAL?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($GRAND_TOTAL)):number_format($GRAND_TOTAL)):"-") . $RTF->bold(0), $heading_width[2], "right", $border);
				$RTF->close_line();
			} else {
				$pdf->Cell($heading_width[2], 7, ($GRAND_TOTAL?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($GRAND_TOTAL)):number_format($GRAND_TOTAL)):"-"), $border, 1, 'R', 0);
			}
		}else{
			if ($FLAG_RTF) {
				$RTF->set_font($font, 16);
				$RTF->color("0");	// 0=BLACK
				$RTF->add_text($RTF->bold(1) . "********** ไม่มีข้อมูล **********" . $RTF->bold(0) , "center");
			} else {
				$pdf->SetFont($font,'b',16);
				$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
				$pdf->Cell(287,10,"********** ไม่มีข้อมูล **********",0,1,'C');
			}
		} // end if
			if ($FLAG_RTF) {
		//	$RTF->close_tab(); 
		//	$RTF->close_section(); 
			
			$RTF->display($fname);
		} else {
			$pdf->close();
			$pdf->Output();	
		}
	}else if($export_type=="graph"){
		$arr_content_key = array_keys($arr_content[0]);//print_r($arr_content_key);
		$arr_categories = array();
		for($i=1;$i<count($arr_content);$i++){
			if($arr_content[$i][type]==$arr_rpt_order[0]){
				$arr_categories[$i] = trim($short_name=="y")?(trim($arr_content[$i][short_name])?$arr_content[$i][short_name]:$arr_content[$i][name]):$arr_content[$i][name];
	//			$arr_categories[$i] = $arr_content[$i][short_name];
				$arr_series_caption_data[0][] = $arr_content[$i][count_1];
				$arr_series_caption_data[1][] = $arr_content[$i][count_2];
				$arr_series_caption_data[2][] = $arr_content[$i][count_3];
				}//if($arr_content[$i][type]==$arr_rpt_order[0]){
		}//print_r($arr_categories);
		$arr_series_list[0] = implode(";", $arr_series_caption_data[0])."";
		$arr_series_list[1] = implode(";", $arr_series_caption_data[1])."";
		$arr_series_list[2] = implode(";", $arr_series_caption_data[2])."";
		$chart_title = $report_title;
		$chart_subtitle = $company_name;
		if(!$setWidth){ $setWidth = "$GRAPH_WIDE";}else{ $setWidth = "800";}
		if(!$setHeight){ $setHeight = "$GRAPH_HIGH";}else{$setHeight = "600";}
		$selectedFormat = "SWF";
		$series_caption_list = "ถือครอง;ว่างมีเงิน;ว่างไม่มีเงิน";
		$categories_list = implode(";", $arr_categories)."";
		if(strtolower($graph_type)=="pie"){
			$series_list = $GRAND_TOTAL_1[$DEPARTMENT_ID].";".$GRAND_TOTAL_2[$DEPARTMENT_ID].";".$GRAND_TOTAL_3[$DEPARTMENT_ID];
			}else{
			$series_list = implode("|", $arr_series_list);
			}
		switch( strtolower($graph_type) ){
			case "column" :
				$style = $_SERVER['DOCUMENT_ROOT']."/graph/styles/Column/style/column.scs";
				break;
			case "bar" :
				$style = $_SERVER['DOCUMENT_ROOT']."/graph/styles/Bar/style/bar.scs";
				break;
			case "line" :
				$style = $_SERVER['DOCUMENT_ROOT']."/graph/styles/Line/style/line.scs";
				break;
			case "pie" :
				$style = $_SERVER['DOCUMENT_ROOT']."/graph/styles/Pie/style/pie.scs";
				break;
		} //switch( strtolower($graph_type) ){
	}//}else if($export_type=="graph"){
	
?>