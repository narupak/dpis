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

	if(in_array("ALL", $list_type) && !$DEPARTMENT_ID)  $f_all = true; else $f_all = false;

	if(!trim($RPTORD_LIST)){ 
	if($select_org_structure==1){	//สำหรับมอบหมายงาน
			if(trim($search_org_ass_id)){	$search_org_id=trim($search_org_ass_id);		}	
			if(trim($search_org_ass_id_1)){	$search_org_id_1=trim($search_org_ass_id_1);		}
			if(trim($search_org_ass_id_2)){	$search_org_id_2=trim($search_org_ass_id_2);		}
		}
		$RPTORD_LIST = "ORG|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)=="" && trim($search_org_id_2)=="") $RPTORD_LIST .= "ORG_1|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)!="" && trim($search_org_id_2)=="") $RPTORD_LIST .= "ORG_2|";
		if(in_array("PER_ORG", $list_type)  && trim($search_org_id_1)!="" && trim($search_org_id_2)!="") $RPTORD_LIST .= "LINE|";
		if(in_array("PER_ORG", $list_type)  && trim($search_pv_code)!="") $RPTORD_LIST .= "PROVINCE|";
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

				$heading_name .= $MINISTRY_TITLE;
				break;
			case "DEPARTMENT" :
				if($select_list) $select_list .= ", ";
				$select_list .= "c.ORG_SEQ_NO, c.ORG_CODE, a.DEPARTMENT_ID";

				if($order_by) $order_by .= ", ";
				$order_by .= "c.ORG_SEQ_NO, c.ORG_CODE, a.DEPARTMENT_ID";

				$heading_name .= " $DEPARTMENT_TITLE";
				break;
			case "ORG" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure == 0) $select_list .= "b.ORG_SEQ_NO, b.ORG_CODE, a.ORG_ID";
				else if($select_org_structure == 1) $select_list .= "b.ORG_SEQ_NO, b.ORG_CODE, d.ORG_ID";

				if($order_by) $order_by .= ", ";
				if($select_org_structure == 0) $order_by .= "b.ORG_SEQ_NO, b.ORG_CODE, a.ORG_ID";
				else if($select_org_structure == 1) $order_by .= "b.ORG_SEQ_NO, b.ORG_CODE, d.ORG_ID";

				$heading_name .= " $ORG_TITLE";
				break;
			case "LINE" :
				if($select_list) $select_list .= ", ";
				$select_list .= "a.PL_CODE";

				if($order_by) $order_by .= ", ";
				$order_by .= "a.PL_CODE";

				$heading_name .= " $PL_TITLE";
				break;
		} // end switch case
	} // end for
	if(!trim($order_by)){ 
		if(!$MINISTRY_ID){ $order_by = "c.ORG_ID_REF";
		}elseif(!$DEPARTMENT_ID){ 
			$order_by = "c.ORG_SEQ_NO, c.ORG_CODE, a.DEPARTMENT_ID";
		}else{ 
			if($select_org_structure == 0) $order_by = "b.ORG_SEQ_NO, b.ORG_CODE, a.ORG_ID";
			else if($select_org_structure == 1)  $order_by = "b.ORG_SEQ_NO, b.ORG_CODE, d.ORG_ID";
		}
	} // end if
	if(!trim($select_list)){ 
		if(!$MINISTRY_ID){ $select_list = "c.ORG_ID_REF as MINISTRY_ID";
		}elseif(!$DEPARTMENT_ID){ 
			$select_list = "c.ORG_SEQ_NO, c.ORG_CODE, a.DEPARTMENT_ID";
		}else{
			if($select_org_structure == 0) $select_list = "b.ORG_SEQ_NO, b.ORG_CODE, a.ORG_ID";
			else if($select_org_structure == 1) $select_list = "b.ORG_SEQ_NO, b.ORG_CODE, d.ORG_ID";
		}
	} // end if

	$search_condition = "";
	$arr_search_condition[] = "(d.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(d.PER_STATUS in (". implode(", ", $search_per_status) ."))";

	$list_type_text = $ALL_REPORT_TITLE;

	if(in_array("PER_ORG_TYPE_1", $list_type) ){
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
	if(in_array("PER_ORG_TYPE_2", $list_type) ){
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
	if(in_array("PER_LINE", $list_type)){
		// สายงาน
		$list_type_text = "";
		if($search_per_type==1 && trim($search_pl_code)){
			$search_pl_code = trim($search_pl_code);
			$arr_search_condition[] = "(trim(a.PL_CODE)='$search_pl_code')";
			$list_type_text .= "$search_pl_name";
		}elseif($search_per_type==2 && trim($search_pn_code)){
			$search_pn_code = trim($search_pn_code);
			$arr_search_condition[] = "(trim(a.PN_CODE)='$search_pn_code')";
			$list_type_text .= "$search_pn_name";
		} // end if
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
	$report_title = "$DEPARTMENT_NAME||จำนวน$PERSON_TYPE[$search_per_type]ที่จะเกษียณอายุ ประจำปีงบประมาณ พ.ศ. $search_budget_year";
	if($export_type=="report")	$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
	$report_code = "H12";
	include ("rpt_R010012_format.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน
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

		$fname= "rpt_R010012_rtf.rtf";

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

	function count_person($budget_year, $level_no, $search_condition, $addition_condition){
		global $DPISDB, $db_dpis2;
		global $search_per_type, $select_org_structure;
		
		if(trim($addition_condition)) $search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition;		
//		if($budget_year) $year_condition = generate_year_condition($budget_year);
		if($DPISDB=="odbc") $year_condition = "(LEFT(trim(d.PER_RETIREDATE), 10) > '".($budget_year - 1)."-10-01' and LEFT(trim(d.PER_RETIREDATE), 10) <= '".($budget_year)."-10-01')";
		elseif($DPISDB=="oci8") $year_condition = "(SUBSTR(trim(d.PER_RETIREDATE), 1, 10) > '".($budget_year - 1)."-10-01' and SUBSTR(trim(d.PER_RETIREDATE), 1, 10) <= '".($budget_year)."-10-01')";

		$search_condition = str_replace(" where ", " and ", $search_condition);

		if($search_per_type==1){
			if($DPISDB=="odbc"){
				$cmd = " select			count(d.PER_ID) as count_person
								from			(
														(
															PER_POSITION a
															left join PER_ORG b on (a.ORG_ID=b.ORG_ID)
														) left join PER_ORG c on (a.DEPARTMENT_ID=c.ORG_ID)
													) left join PER_PERSONAL d on (a.POS_ID=d.POS_ID)
								 where			d.LEVEL_NO='$level_no' and $year_condition
													$search_condition
								 group by		d.PER_ID ";
			}elseif($DPISDB=="oci8"){
				$search_condition = str_replace(" where ", " and ", $search_condition);
				$cmd = " select			count(d.PER_ID) as count_person
								 from			PER_POSITION a, PER_ORG b, PER_ORG c, PER_PERSONAL d
								 where			a.ORG_ID=b.ORG_ID(+) and a.DEPARTMENT_ID=c.ORG_ID(+) and a.POS_ID=d.POS_ID(+)
								 					and d.LEVEL_NO='$level_no' and $year_condition
													$search_condition
								 group by		d.PER_ID ";
			}
		}elseif($search_per_type==2){
			if($DPISDB=="odbc"){
				$cmd = " select			count(a.PER_ID) as count_person
								 from			(
														PER_PERSONAL a
														left join PER_POS_EMP b on (a.POEM_ID=b.POEM_ID)
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
								  where		$year_condition
													$search_condition
								  group by	a.PER_ID ";
			}elseif($DPISDB=="oci8"){
				$cmd = " select			count(a.PER_ID) as count_person
								 from			PER_PERSONAL a, PER_POS_EMP b, PER_ORG c
								 where		a.POEM_ID=b.POEM_ID(+) and b.ORG_ID=c.ORG_ID(+) and $year_condition
													$search_condition
								  group by	a.PER_ID ";
			}
		} // end if
		if($select_org_structure==1) { 
			$cmd = str_replace("b.ORG_ID", "d.ORG_ID", $cmd);
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

//			echo "age <= 24 :: $age_condition<br>";
		
		return $year_condition;
	} // function
	
	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order, $select_org_structure;
		global $MINISTRY_ID, $DEPARTMENT_ID, $ORG_ID, $PL_CODE;
				
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
					if($select_org_structure==0){
						if($ORG_ID && $ORG_ID!=-1) $arr_addition_condition[] = "(a.ORG_ID = $ORG_ID)";
						else $arr_addition_condition[] = "(a.ORG_ID = 0 or a.ORG_ID is null)";
					}elseif($select_org_structure==1){
						if($ORG_ID && $ORG_ID!=-1) $arr_addition_condition[] = "(d.ORG_ID = $ORG_ID)";
						else $arr_addition_condition[] = "(d.ORG_ID = 0 or d.ORG_ID is null)";
					}
				break;
				case "LINE" :
					if($PL_CODE) $arr_addition_condition[] = "(trim(a.PL_CODE) = '$PL_CODE')";
					else $arr_addition_condition[] = "(trim(a.PL_CODE) = '$PL_CODE' or a.PL_CODE is null)";
				break;
			} // end switch case
		} // end for
		
		$addition_condition = "";
		if(count($arr_addition_condition)) $addition_condition = implode(" and ", $arr_addition_condition);

		return $addition_condition;
	} // function
	
	function initialize_parameter($current_index){
		global $arr_rpt_order;
		global $MINISTRY_ID, $DEPARTMENT_ID, $ORG_ID, $PL_CODE;
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
					$PL_CODE = -1;
				break;
			} // end switch case
		} // end for
	} // function

	if($search_per_type==1){
		if($DPISDB=="odbc"){
			$cmd = " select			distinct $select_list
							 from			(
													(
														PER_POSITION a
														left join PER_ORG b on (a.ORG_ID=b.ORG_ID)
													) left join PER_ORG c on (a.DEPARTMENT_ID=c.ORG_ID)
												) left join PER_PERSONAL d on (a.POS_ID=d.POS_ID)
												$search_condition
							 order by		$order_by ";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select			distinct $select_list
							 from			PER_POSITION a, PER_ORG b, PER_ORG c, PER_PERSONAL d
							 where			a.ORG_ID=b.ORG_ID(+) and a.DEPARTMENT_ID=c.ORG_ID(+) and a.POS_ID=d.POS_ID(+)
												$search_condition
							 order by		$order_by ";
		}
	}elseif($search_per_type==2){
		if($DPISDB=="odbc"){
			$cmd = " select			distinct $select_list
							 from			(
													PER_POS_EMP a
													left join PER_PERSONAL b on (a.POEM_ID=b.POEM_ID)
												) left join PER_ORG c on (a.ORG_ID=c.ORG_ID)
												$search_condition
							 order by		$order_by ";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select			distinct $select_list
							 from			PER_POS_EMP a,PER_PERSONAL  b, PER_ORG c
							 where		a.POEM_ID=b.POEM_ID(+) and a.ORG_ID=c.ORG_ID(+) 
												$search_condition
							 order by		$order_by ";
		}
	} // end if
	if($select_org_structure==1) { 
		$cmd = str_replace("a.ORG_ID", "d.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	echo "$cmd<br>";
//	$db_dpis->show_error();
	$data_count = 0;
	for($i=0; $i<count($ARR_LEVEL_GROUP); $i++){
		$LEVEL_GROUP = $ARR_LEVEL_GROUP[$i];
		for($j=0; $j<count($ARR_LEVEL[$LEVEL_GROUP]); $j++){
			$LEVEL_NO = $ARR_LEVEL[$LEVEL_GROUP][$j];
			$LEVEL_GRAND_TOTAL[$LEVEL_NO] = 0;
		} // loop for
	} // loop for
	initialize_parameter(0);
//	echo "order=".implode(",",$arr_rpt_order)."<br>";
	$last_order_index = count($arr_rpt_order) - 1;
	$st_order = -1;
	while($data = $db_dpis->get_array()){
		for($rpt_order_index=0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "MINISTRY" :
					if($MINISTRY_ID != trim($data[MINISTRY_ID])){
						$MINISTRY_ID = trim($data[MINISTRY_ID]);
						if($MINISTRY_ID != ""){
							$cmd = " select ORG_NAME,ORG_SHORT from PER_ORG where ORG_ID=$MINISTRY_ID ";
							if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$MINISTRY_NAME = $data2[ORG_NAME];
							$MINISTRY_SHORT = $data2[ORG_SHORT];
						} // end if
if($f_all){
						$addition_condition = generate_condition($rpt_order_index);

						if ($st_order < 0) $st_order= $rpt_order_index;
						$arr_content[$data_count][type] = "MINISTRY";
						$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index-$st_order) * 5)) . $MINISTRY_NAME;
						$arr_content[$data_count][short_name] = $MINISTRY_SHORT;

						for($i=0; $i<count($ARR_LEVEL_GROUP); $i++){
							$LEVEL_GROUP = $ARR_LEVEL_GROUP[$i];
							for($j=0; $j<count($ARR_LEVEL[$LEVEL_GROUP]); $j++){
								$LEVEL_NO = $ARR_LEVEL[$LEVEL_GROUP][$j];
								$arr_content[$data_count][("count_".$LEVEL_NO)] = count_person(($search_budget_year - 543), $LEVEL_NO, $search_condition, $addition_condition);
								$arr_content[$data_count][total] += $arr_content[$data_count][("count_".$LEVEL_NO)];
								
								if($rpt_order_index == $last_order_index) $LEVEL_GRAND_TOTAL[$LEVEL_NO] += $arr_content[$data_count]["count_".$LEVEL_NO];
//								if($rpt_order_index == $last_order_index) { $LEVEL_GRAND_TOTAL[$LEVEL_NO] += $arr_content[$data_count]["count_".$LEVEL_NO];
//								echo "MINISTRY_NAME [$MINISTRY_NAME]..(LEVEL_NO=$LEVEL_NO)..".$LEVEL_GRAND_TOTAL[$LEVEL_NO]."<br>"; }
							} // loop for
						} // loop for

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);
						$data_count++;
}				
					} // end if
				break;

				case "DEPARTMENT" :
					if($DEPARTMENT_ID != trim($data[DEPARTMENT_ID])){
						$DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);
						if($DEPARTMENT_ID != ""){
							$cmd = " select ORG_NAME,ORG_SHORT from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
							if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$DEPARTMENT_NAME = $data2[ORG_NAME];
							$DEPARTMENT_ORG_SHORT = $data2[ORG_SHORT];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);

						if ($st_order < 0) $st_order= $rpt_order_index;
						$arr_content[$data_count][type] = "DEPARTMENT";
						$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index-$st_order) * 5)) . $DEPARTMENT_NAME;
						$arr_content[$data_count][short_name] = $DEPARTMENT_ORG_SHORT;

						for($i=0; $i<count($ARR_LEVEL_GROUP); $i++){
							$LEVEL_GROUP = $ARR_LEVEL_GROUP[$i];
							for($j=0; $j<count($ARR_LEVEL[$LEVEL_GROUP]); $j++){
								$LEVEL_NO = $ARR_LEVEL[$LEVEL_GROUP][$j];
								$arr_content[$data_count][("count_".$LEVEL_NO)] = count_person(($search_budget_year - 543), $LEVEL_NO, $search_condition, $addition_condition);
								$arr_content[$data_count][total] += $arr_content[$data_count][("count_".$LEVEL_NO)];
								
								if($rpt_order_index == $last_order_index) $LEVEL_GRAND_TOTAL[$LEVEL_NO] += $arr_content[$data_count]["count_".$LEVEL_NO];
//								if($rpt_order_index == $last_order_index) { $LEVEL_GRAND_TOTAL[$LEVEL_NO] += $arr_content[$data_count]["count_".$LEVEL_NO];
//								echo "DEPARTMENT_NAME [$DEPARTMENT_NAME]..(LEVEL_NO=$LEVEL_NO)..".$LEVEL_GRAND_TOTAL[$LEVEL_NO]."<br>"; }
							} // loop for
						} // loop for

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;

				case "ORG" :
					if($ORG_ID != trim($data[ORG_ID])){
						$ORG_ID = trim($data[ORG_ID]);
						if($ORG_ID != ""){
							$cmd = " select ORG_NAME,ORG_SHORT from PER_ORG where ORG_ID=$ORG_ID ";
							if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_NAME = $data2[ORG_NAME];
							$ORG_SHORT = $data2[ORG_SHORT];
							if($ORG_NAME=="-")	$ORG_NAME = $ORG_BKK_TITLE;
						} // end if

						$addition_condition = generate_condition($rpt_order_index);

						if ($st_order < 0) $st_order= $rpt_order_index;
						$arr_content[$data_count][type] = "ORG";
						$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index-$st_order) * 5)) . $ORG_NAME;
						$arr_content[$data_count][short_name] = $ORG_SHORT;

						for($i=0; $i<count($ARR_LEVEL_GROUP); $i++){
							$LEVEL_GROUP = $ARR_LEVEL_GROUP[$i];
							for($j=0; $j<count($ARR_LEVEL[$LEVEL_GROUP]); $j++){
								$LEVEL_NO = $ARR_LEVEL[$LEVEL_GROUP][$j];
								$arr_content[$data_count][("count_".$LEVEL_NO)] = count_person(($search_budget_year - 543), $LEVEL_NO, $search_condition, $addition_condition);
								$arr_content[$data_count][total] += $arr_content[$data_count][("count_".$LEVEL_NO)];
								
								if($rpt_order_index == $last_order_index) $LEVEL_GRAND_TOTAL[$LEVEL_NO] += $arr_content[$data_count]["count_".$LEVEL_NO];
//								if($rpt_order_index == $last_order_index) { $LEVEL_GRAND_TOTAL[$LEVEL_NO] += $arr_content[$data_count]["count_".$LEVEL_NO];
//								echo "ORG_NAME [$ORG_NAME]..(LEVEL_NO=$LEVEL_NO)..".$LEVEL_GRAND_TOTAL[$LEVEL_NO]."<br>"; }
							} // loop for
						} // loop for

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;
				
				case "LINE" :
					if($PL_CODE != trim($data[PL_CODE])){
						$PL_CODE = trim($data[PL_CODE]);
						if($PL_CODE != ""){
							$cmd = " select PL_NAME, PL_SHORTNAME from PER_LINE where trim(PL_CODE)='$PL_CODE' ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$PL_NAME = trim($data2[PL_SHORTNAME])?$data2[PL_SHORTNAME]:$data2[PL_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);

						if ($st_order < 0) $st_order= $rpt_order_index;
						$arr_content[$data_count][type] = "LINE";
						$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index-$st_order) * 5)) . $PL_NAME;
						$arr_content[$data_count][short_name] = "";

						for($i=0; $i<count($ARR_LEVEL_GROUP); $i++){
							$LEVEL_GROUP = $ARR_LEVEL_GROUP[$i];
							for($j=0; $j<count($ARR_LEVEL[$LEVEL_GROUP]); $j++){
								$LEVEL_NO = $ARR_LEVEL[$LEVEL_GROUP][$j];
								$arr_content[$data_count][("count_".$LEVEL_NO)] = count_person(($search_budget_year - 543), $LEVEL_NO, $search_condition, $addition_condition);
								$arr_content[$data_count][total] += $arr_content[$data_count][("count_".$LEVEL_NO)];
								
								if($rpt_order_index == $last_order_index) $LEVEL_GRAND_TOTAL[$LEVEL_NO] += $arr_content[$data_count]["count_".$LEVEL_NO];
//								if($rpt_order_index == $last_order_index) { $LEVEL_GRAND_TOTAL[$LEVEL_NO] += $arr_content[$data_count]["count_".$LEVEL_NO];
//								echo "PL_NAME [$PL_NAME]..(LEVEL_NO=$LEVEL_NO)..".$LEVEL_GRAND_TOTAL[$LEVEL_NO]."<br>"; }
							} // loop for
						} // loop for

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;
		
			} // end switch case
		} // end for
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	$GRAND_TOTAL = array_sum($LEVEL_GRAND_TOTAL);
	if($export_type=="report"){	
		if($count_data){
			$head_text1 = implode(",", $heading_text);
			$head_width1 = implode(",", $heading_width);
			$head_align1 = implode(",", $heading_align);
			$col_function = implode(",", $column_function);
			if ($FLAG_RTF) {
				$RTF->add_header("", 0, false);	// header default
				$RTF->add_footer("", 0, false);		// footer default
				
				$RTF->paragraph(); 
					
			//	echo "$head_text1<br>";
				$tab_align = "center";
				$result = $RTF->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", $fmtTableHeader_col_idx, $fmtTableHeader_bgcol_idx, $COLUMN_FORMAT, $col_function, false, $tab_align);
			} else {
				$pdf->AutoPageBreak = false; 
		//		echo "$head_text1<br>";
				$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", "0066CC", "EEEEFF", $COLUMN_FORMAT, $col_function);
			}
			if (!$result) echo "****** error ****** on open table for $table<br>";
			
			for($data_count=0; $data_count<count($arr_content); $data_count++){
				$REPORT_ORDER = $arr_content[$data_count][type];
				$NAME = $arr_content[$data_count][name];
				for($i=0; $i<count($ARR_LEVEL_GROUP); $i++){
					$LEVEL_GROUP = $ARR_LEVEL_GROUP[$i];
					for($j=0; $j<count($ARR_LEVEL[$LEVEL_GROUP]); $j++){
						$LEVEL_NO = $ARR_LEVEL[$LEVEL_GROUP][$j];
						${"COUNT_".$LEVEL_NO} = $arr_content[$data_count][("count_".$LEVEL_NO)];
					} // loop for
				} // loop for
				$TOTAL = $arr_content[$data_count][total];

				$arr_data = (array) null;
				$arr_data[] =  "$NAME";
				for($i=0; $i<count($ARR_LEVEL_GROUP); $i++){
					$LEVEL_GROUP = $ARR_LEVEL_GROUP[$i];
					for($j=0; $j<count($ARR_LEVEL[$LEVEL_GROUP]); $j++){
						$LEVEL_NO = $ARR_LEVEL[$LEVEL_GROUP][$j];
						$arr_data[] = ${"COUNT_".$LEVEL_NO};
					} // loop for
				} // loop for
				$arr_data[] = $TOTAL;
				
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

			$arr_data = (array) null;
			$arr_data[] =  "รวม";
			for($i=0; $i<count($ARR_LEVEL_GROUP); $i++){
				$LEVEL_GROUP = $ARR_LEVEL_GROUP[$i];
				for($j=0; $j<count($ARR_LEVEL[$LEVEL_GROUP]); $j++){
					$LEVEL_NO = $ARR_LEVEL[$LEVEL_GROUP][$j];
					$arr_data[] = $LEVEL_GRAND_TOTAL[$LEVEL_NO];
	//				echo "(LEVEL_NO=$LEVEL_NO)..".$LEVEL_GRAND_TOTAL[$LEVEL_NO]."<br>";
				} // loop for
			} // loop for
			$arr_data[] = $GRAND_TOTAL;
	//		echo "total..".implode(",",$arr_data)."<br>";
			
			if ($FLAG_RTF)
				$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
			else
				$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", "000000", "");		//TRHBL
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
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
	}else if($export_type=="graph"){
		$arr_content_map = (array) null;
		$arr_series_caption = (array) null;
		$arr_content_map[] = "name";
		$arr_series_caption[] = "";
		for($i=0; $i<count($ARR_LEVEL_GROUP); $i++){
			$LEVEL_GROUP = $ARR_LEVEL_GROUP[$i];
			for($j=0; $j<count($ARR_LEVEL[$LEVEL_GROUP]); $j++){
				$LEVEL_NO = $ARR_LEVEL[$LEVEL_GROUP][$j];
				$arr_content_map[] = "count_".$LEVEL_NO;
				$arr_series_caption[] = "".$ARR_LEVEL_GROUP_NAME[$LEVEL_GROUP]." ".$ARR_LEVEL_NAME[$LEVEL_NO][0]." ".$ARR_LEVEL_NAME[$LEVEL_NO][1];
			} // loop for
		} // loop for
		$arr_content_map[] = "total";
		$arr_series_caption[] = "";

		$arr_content_key = array_keys($arr_content[0]);//print_r($arr_content_key);
		$arr_categories = array();
		$arr_series_caption_list = array(); 
		$f_first = true;
		for($i=0;$i<count($arr_content);$i++){
			$arr_categories[$i] = trim($short_name=="y")?(trim($arr_content[$i][short_name])?$arr_content[$i][short_name]:$arr_content[$i][name]):$arr_content[$i][name];
			$cntseq=0;
			for($j=0; $j<count($arr_content_map); $j++){ 
	//			echo "level $j:";
				if ($arr_column_sel[$arr_column_map[$j]]==1 && strpos($arr_content_map[$arr_column_map[$j]],"count_")!==false) {
					$arr_series_caption_data[$cntseq][] = $arr_content[$i][$arr_content_map[$arr_column_map[$j]]];
	//				echo "-->map:".$arr_content_map[$arr_column_map[$j]]." data=".$arr_content[$i][$arr_content_map[$arr_column_map[$j]]]."<br>";
					if ($f_first) $arr_series_caption_list[] = $arr_series_caption[$arr_column_map[$j]];
					$cntseq++;
				}
	//			echo "<br>";
			} // end for $j
			$f_first=false;	// check สำหรับรอบแรกเท่านั้น
		} // end for $i
	//	echo "caption list count (".count($arr_series_caption_list).")=$series_caption_list<br>";
	//	echo "count (arr_series_caption_data)=".count($arr_series_caption_data)."<br>";
	/*
		$arr_content_key = array_keys($arr_content[0]);//print_r($arr_content_key);
		$arr_categories = array();
		for($i=0;$i<count($arr_content);$i++){
			if($arr_content[$i][type]==$arr_rpt_order[0]){
				$arr_categories[$i] = trim($short_name=="y")?(trim($arr_content[$i][short_name])?$arr_content[$i][short_name]:$arr_content[$i][name]):$arr_content[$i][name];
				
	/*
				for($j=0;$j<count($ARR_LEVEL_NO);$j++){
					$arr_series_caption[$j] = $ARR_POSITION_TYPE[$search_pt_name][$j];
					$arr_series_caption_data[$ARR_LEVEL_NO[$ARR_POSITION_TYPE[$search_pt_name][$j]]][] = $arr_content[$i]["count_".$ARR_LEVEL_NO[$ARR_POSITION_TYPE[$search_pt_name][$j]]];
				}//for($j=0;$j<count($ARR_LEVEL_NO);$j++){

	*/
	/*			for($i=0; $i<count($ARR_LEVEL_GROUP); $i++){
					$LEVEL_GROUP = $ARR_LEVEL_GROUP[$i];
					$arr_series_caption[$j] = $LEVEL_GROUP;
					for($j=0; $j<count($ARR_LEVEL[$LEVEL_GROUP]); $j++){
						$LEVEL_NO = $ARR_LEVEL[$LEVEL_GROUP][$j];
						$arr_series_caption_data[$ARR_LEVEL[$ARR_POSITION_TYPE[$search_pt_name][$j]]][] = $arr_content[$i]["count_".$ARR_LEVEL[$ARR_POSITION_TYPE[$search_pt_name][$j]]];
					} // loop for
				} // loop for
			
		}//if($arr_content[$i][type]==$arr_rpt_order[0]){
			
	}//for($i=0;$i<count($arr_content);$i++){
	/*
for($i=0;$i<count($ARR_LEVEL_NO);$i++){
		if(strtolower($graph_type)=="pie"){
			$arr_series_list[$i] = $LEVEL_GRAND_TOTAL[$ARR_LEVEL_NO[$ARR_POSITION_TYPE[$search_pt_name][$i]]];
			}else{//if(strtolower($graph_type)=="pie"){
			$arr_series_list[$i] = implode(";", $arr_series_caption_data[$ARR_LEVEL_NO[$ARR_POSITION_TYPE[$search_pt_name][$i]]]);
			}//if(strtolower($graph_type)=="pie"){
}//for($i=0;$i<count($ARR_LEVEL_NO);$i++){
*/
	if(strtolower($graph_type)=="pie"){
		for($i=0; $i<count($ARR_LEVEL_GROUP); $i++){
			$LEVEL_GROUP = $ARR_LEVEL_GROUP[$i];
			$arr_series_caption[$j] = $LEVEL_GROUP;
			for($j=0; $j<count($ARR_LEVEL[$LEVEL_GROUP]); $j++){
				$LEVEL_NO = $ARR_LEVEL[$LEVEL_GROUP][$j];
				$arr_series_list[$i] = $LEVEL_GRAND_TOTAL[$LEVEL_NO];
			} // loop for
		} // loop for
		$series_caption_list = implode(";",$arr_series_caption);
	} else {
		for($j=0;$j<count($arr_series_caption_data);$j++){
			$arr_series_list[$j] = implode(";", $arr_series_caption_data[$j]);
		}
		$series_caption_list = implode(";",$arr_series_caption_list);
	}//if(strtolower($graph_type)=="pie"){
//	echo "caption:".implode(",",$arr_series_caption)." || ".implode(",",$arr_series_list)."<br>";
//	print_r($arr_series_list);
//	$arr_series_list[2] = implode(";", $arr_series_caption_data[2]).";$GRAND_TOTAL_S";
	$chart_title = $report_title;   
	$chart_subtitle = $company_name;	
	if(!$setWidth){ $setWidth = "$GRAPH_WIDE";}else{ $setWidth = "800";}
	if(!$setHeight){ $setHeight = "$GRAPH_HIGH";}else{$setHeight = "600";}
	$selectedFormat = "SWF";
	$categories_list = implode(";", $arr_categories);
	if(strtolower($graph_type)=="pie"){
		$series_list = implode(";", $arr_series_list);
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
		} //end switch
 } //end if	
	
?>